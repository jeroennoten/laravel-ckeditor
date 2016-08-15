<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Routing\UrlGenerator;
use JeroenNoten\LaravelCkEditor\Uploads\ImageUploader;

class ImagesTest extends TestCase
{
    public function testUpload(UrlGenerator $urlGenerator)
    {
        $this->upload($urlGenerator->route('ckeditor.images.store', [
            'CKEditorFuncNum' => 9
        ]));

        $this->see('<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction(9, "http:\\/\\/localhost\\/xyz.png");</script>');
    }

    public function testJsonUpload(UrlGenerator $urlGenerator) {
        $this->upload($urlGenerator->route('ckeditor.images.store', [
            'CKEditorFuncNum' => 9,
            'json' => ''
        ]));

        $this->seeJson([
            'uploaded' => 1,
            'url' => "http://localhost/xyz.png"
        ]);
    }

    private function upload($uploadUrl)
    {
        $path = __DIR__ . '/stubs/laravel-logo.png';
        $name = 'laravel.png';

        $uploader = Mockery::mock(ImageUploader::class);
        $this->app->instance(ImageUploader::class, $uploader);
        $uploader->shouldReceive('upload')->with(Mockery::on(function (UploadedFile $file) use ($path, $name) {
            return $file->path() == $path && $file->getClientOriginalName() == $name;
        }))->andReturn('http://localhost/xyz.png')->once();

        $data = ['ckCsrfToken' => '123'];
        $cookies = $data;
        $files = ['upload' => new UploadedFile($path, $name)];

        $this->call('post', $uploadUrl, $data, $cookies, $files);
    }
}