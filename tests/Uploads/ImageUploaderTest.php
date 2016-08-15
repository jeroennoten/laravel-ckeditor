<?php

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use JeroenNoten\LaravelCkEditor\Uploads\ImageUploader;

class ImageUploaderTest extends PHPUnit_Framework_TestCase
{
    public function testUpload()
    {
        $filesMock = Mockery::mock(FilesystemAdapter::class);
        $filesMock->shouldReceive('put')->with(typeOf('string'), 'xyz')->once()->andReturn(true);
        $filesMock->shouldReceive('url')->with(typeOf('string'))->once()->andReturn('/hi.txt');

        $urlMock = Mockery::mock(UrlGenerator::class);
        $urlMock->shouldReceive('to')->with('/hi.txt')->once()->andReturn('http://localhost/hi.txt');

        $uploader = new ImageUploader($filesMock, $urlMock);

        $url = $uploader->upload(new UploadedFile(__DIR__ . '/../stubs/xyz.txt', 'hi.doc'));

        $this->assertEquals('http://localhost/hi.txt', $url);
    }
}