<?php

namespace Inovector\Mixpost\Events\Post;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Inovector\Mixpost\Contracts\QueueWorkspaceAware;
use Inovector\Mixpost\Contracts\WebhookEvent;
use Inovector\Mixpost\Http\Base\Resources\PostActivityResource;
use Inovector\Mixpost\Enums\PostActivityType;
use Inovector\Mixpost\Facades\WorkspaceManager;
use Inovector\Mixpost\Models\PostActivity;

class PostCommentCreated implements WebhookEvent, QueueWorkspaceAware
{
    use Dispatchable, SerializesModels;

    public PostActivity $activity;

    public function __construct(PostActivity $activity)
    {
        $this->activity = $activity;
    }

    public static function name(): string
    {
        return 'comment.created';
    }

    public static function nameLocalized(): string
    {
        return __('mixpost::webhook.event.comment.created');
    }

    public function payload(): array
    {
        $activity = $this->activity->load(['user', 'reactions.user', 'post'])->loadCount('children');

        // Resolve parent UUID if exists
        $parentUuid = null;
        if ($activity->parent_id) {
            $parent = PostActivity::find($activity->parent_id);
            $parentUuid = $parent?->uuid;
        }

        // History: last 50 comment activities for this post (ascending by id)
        $history = [];
        if ($activity->post) {
            $historyRecords = $activity->post
                ->activities()
                ->where('type', PostActivityType::COMMENT)
                ->latest('id')
                ->take(50)
                ->get()
                ->sortBy('id')
                ->values();

            $history = PostActivityResource::collection($historyRecords)->resolve();
        }

        return [
            'workspace_uuid' => WorkspaceManager::current()?->uuid,
            'post_uuid' => $activity->post?->uuid,
            'parent_uuid' => $parentUuid,
            'latest_message' => $activity->text,
            'activity' => (new PostActivityResource($activity))->resolve(),
            'history' => $history,
        ];
    }
}


