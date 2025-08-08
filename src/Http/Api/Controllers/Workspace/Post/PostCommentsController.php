<?php

namespace Inovector\Mixpost\Http\Api\Controllers\Workspace\Post;

use Illuminate\Routing\Controller;
use Inovector\Mixpost\Http\Base\Requests\Workspace\Post\StorePostComment;
use Inovector\Mixpost\Http\Base\Resources\PostActivityResource;

class PostCommentsController extends Controller
{
    public function store(StorePostComment $storePostComment): PostActivityResource
    {
        return new PostActivityResource($storePostComment->handle());
    }
}


