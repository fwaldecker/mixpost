<?php

namespace Inovector\Mixpost\Concerns\Model\Media;

use Intervention\Image\Facades\Image;

trait HasImageData
{
    public function imageHeight()
    {
        if (isset($this->data['height'])) {
            return $this->data['height'];
        }

        $height = Image::make($this->getFullPath())->height();
        $this->saveImageHeight($height);

        return $height;
    }

    public function imageWidth()
    {
        if (isset($this->data['width'])) {
            return $this->data['width'];
        }

        return Image::make($this->getFullPath())->width();
    }

    public function saveImageHeight(int $height): void
    {
        $this->data = array_merge($this->data, ['height' => $height]);
        $this->save();
    }

    public function saveImageWidth(int $width): void
    {
        $this->data = array_merge($this->data, ['width' => $width]);
        $this->save();
    }
}
