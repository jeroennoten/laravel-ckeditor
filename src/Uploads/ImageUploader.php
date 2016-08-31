<?php

namespace JeroenNoten\LaravelCkEditor\Uploads;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Constraint;
use Intervention\Image\ImageManager;

class ImageUploader
{
    private $disk;

    private $url;

    private $imageManager;

    public function __construct(FilesystemAdapter $disk, UrlGenerator $url, ImageManager $imageManager)
    {
        $this->disk = $disk;
        $this->url = $url;
        $this->imageManager = $imageManager;
    }

    public function getDisk()
    {
        return $this->disk;
    }

    public function upload(UploadedFile $file)
    {
        $path = 'uploads'.DIRECTORY_SEPARATOR.$this->generateFileName($file);

        $image = $this->imageManager->make($file)->resize(450, null, function (Constraint $constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->encode()->getEncoded();

        $this->disk->put($path, $image);

        return $this->url->to($this->getPublicUrl($path));
    }

    private function generateFileName(UploadedFile $file)
    {
        $id = uniqid();

        return "$id.{$file->extension()}";
    }

    private function getPublicUrl($path)
    {
        return $this->disk->url($path);
    }
}