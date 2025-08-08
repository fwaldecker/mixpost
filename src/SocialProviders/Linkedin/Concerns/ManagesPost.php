<?php

namespace Inovector\Mixpost\SocialProviders\Linkedin\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Inovector\Mixpost\Enums\SocialProviderContentType;
use Inovector\Mixpost\Enums\SocialProviderResponseStatus;
use Inovector\Mixpost\Models\Media;
use Inovector\Mixpost\Support\FetchUrlCard;
use Inovector\Mixpost\Support\SocialProviderResponse;
use Inovector\Mixpost\Support\TemporaryFile;
use RuntimeException;

trait ManagesPost
{
    use UsesUploads;
    use ManageComments;

    public function publishPost(string $text, Collection $media, array $params = []): SocialProviderResponse
    {
        if ($this->hasRefreshToken() && $this->tokenIsAboutToExpire()) {
            $newAccessToken = $this->refreshToken();

            if ($newAccessToken->hasError()) {
                return $newAccessToken;
            }

            $this->updateToken($newAccessToken->context());
        }

        try {
            $postParams = [
                'author' => "urn:li:{$this->author()}:{$this->values['provider_id']}",
                'commentary' => $text,
                'visibility' => Str::upper(Arr::get($params, 'visibility', 'PUBLIC')),
                'distribution' => [
                    'feedDistribution' => 'MAIN_FEED',
                ],
                'lifecycleState' => 'PUBLISHED',
                'isReshareDisabledByAuthor' => false,
            ];

            /**
             * @var SocialProviderResponse $previousPostResponse
             */
            if ($previousPostResponse = $params['previous_post_response'] ?? null && self::contentType() === SocialProviderContentType::COMMENTS) {
                return $this->publishComment($text, $previousPostResponse->id());
            }

            $this->handleArticle($media, $params, $postParams);

            // One image post
            if ($media->count() === 1 && $media->first()->isImage()) {
                $this->handleImage($media->first(), $postParams);
            }

            // One video post
            if ($media->count() === 1 && $media->first()->isVideo()) {
                $this->handleVideo($media->first(), $params, $postParams);
            }

            // Multiple images post
            if ($media->count() > 1) {
                $this->handleImages($media->filter(fn($media) => $media->isImage()), $postParams);
            }
        } catch (RuntimeException $e) {
            return $this->response(SocialProviderResponseStatus::ERROR, json_decode($e->getMessage(), true));
        }

        $response = $this->getHttpClient()::withToken($this->getAccessToken()['access_token'])
            ->withHeaders($this->httpHeadersNext())
            ->post("$this->apiUrl/rest/posts", $postParams);

        return $this->buildResponse($response, function () use ($response) {
            return [
                'id' => $response->header('x-linkedin-id')
            ];
        });
    }

    public function deletePost($id): SocialProviderResponse
    {
        if ($this->hasRefreshToken() && $this->tokenIsAboutToExpire()) {
            $newAccessToken = $this->refreshToken();

            if ($newAccessToken->hasError()) {
                return $newAccessToken;
            }

            $this->updateToken($newAccessToken->context());
        }

        $response = $this->getHttpClient()::withToken($this->getAccessToken()['access_token'])
            ->withHeaders($this->httpHeaders())
            ->delete("$this->apiUrl/rest/posts/$id");

        return $this->buildResponse($response);
    }

    private function handleArticle(Collection $media, array $params, array &$postParams): void
    {
        if ($media->count()) {
            return;
        }

        if (!$url = $params['url'] ?? '') {
            return;
        }

        $card = (new FetchUrlCard())($url);

        $postParams['content'] = [
            'article' => [
                'source' => $url,
                'title' => $card['default']['title'],
                'description' => $card['default']['description'],
            ]
        ];

        if ($card['default']['image']) {
            $file = null;

            try {
                $file = TemporaryFile::make()->fromUrl($card['default']['image']);
                $response = $this->uploadImage($file);

                if (!$response->hasError()) {
                    $postParams['content']['article']['thumbnail'] = $response->id();
                }
            } finally {
                $file?->directory()->delete();
            }
        }
    }

    private function handleImage(Media $video, array &$postParams): void
    {
        $response = $this->uploadImage($video);

        if ($response->hasError()) {
            throw new RuntimeException(json_encode($response->context()));
        }

        $postParams['content']['media'] = [
            'id' => $response->id(),
            'altText' => $video->alt_text ?? '',
        ];
    }

    private function handleVideo(Media $video, array $params, array &$postParams): void
    {
        $response = $this->uploadVideo($video, $params);

        if ($response->hasError()) {
            throw new RuntimeException(json_encode($response->context()));
        }

        $postParams['content']['media'] = [
            'id' => $response->id(),
        ];
    }

    private function handleImages(Collection $images, array &$postParams): void
    {
        $uploadedImages = $images->map(function ($media) {
            $response = $this->uploadImage($media);

            if ($response->hasError()) {
                throw new RuntimeException(json_encode($response->context()));
            }

            return [
                'id' => $response->id(),
                'altText' => $media->alt_text ?? '',
            ];
        });

        if ($uploadedImages->isEmpty()) {
            return;
        }

        $postParams['content']['multiImage']['images'] = $uploadedImages->toArray();
    }
}
