<?php

namespace Inovector\Mixpost\Http\Base\Controllers\Workspace;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Inovector\Mixpost\Enums\ServiceGroup;
use Inovector\Mixpost\Facades\ServiceManager;
use Inovector\Mixpost\Http\Base\Requests\Workspace\DeleteMedia;
use Inovector\Mixpost\Http\Base\Requests\Workspace\UpdateMedia;

class MediaController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Workspace/Media', [
            'is_configured_service' => ServiceManager::isActive(
                ServiceManager::services()->group(ServiceGroup::MEDIA)->getNames()
            )
        ]);
    }

    public function update(UpdateMedia $updateMedia): RedirectResponse
    {
        $updateMedia->handle();

        return redirect()->back();
    }

    public function destroy(DeleteMedia $deleteMediaFiles): HttpResponse
    {
        $deleteMediaFiles->handle();

        return response()->noContent();
    }
}
