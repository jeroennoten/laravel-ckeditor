<?php

namespace JeroenNoten\LaravelCkEditor\Uploads;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;

class ImageUploader
{
    private $disk;

    private $url;

    public function __construct(FilesystemAdapter $disk, UrlGenerator $url)
    {
        $this->disk = $disk;
        $this->url = $url;
    }

    public function upload(UploadedFile $file)
    {
        $path = 'uploads' . DIRECTORY_SEPARATOR . $this->generateFileName($file);

        $this->disk->put($path, file_get_contents($file));

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