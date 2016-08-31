<?php

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use JeroenNoten\LaravelCkEditor\Uploads\ImageUploader;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;

class ImageUploaderTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testUpload()
    {
        $filesMock = Mockery::mock(FilesystemAdapter::class);
        $filesMock->shouldReceive('put')->with(typeOf('string'), 'xyz')->once()->andReturn(true);
        $filesMock->shouldReceive('url')->with(typeOf('string'))->once()->andReturn('/hi.txt');

        $urlMock = Mockery::mock(UrlGenerator::class);
        $urlMock->shouldReceive('to')->with('/hi.txt')->once()->andReturn('http://localhost/hi.txt');

        $imageMock = Mockery::mock(\Intervention\Image\Image::class);
        $imageMock->shouldReceive('resize')->once()->andReturnSelf();
        $imageMock->shouldReceive('encode')->once()->andReturnSelf();
        $imageMock->shouldReceive('getEncoded')->once()->andReturn('xyz');

        $imageManagerMock = Mockery::mock(ImageManager::class);
        $imageManagerMock->shouldReceive('make')->once()->andReturn($imageMock);

        $uploader = new ImageUploader($filesMock, $urlMock, $imageManagerMock);

        $url = $uploader->upload(new UploadedFile(__DIR__.'/../stubs/xyz.txt', 'hi.doc'));

        $this->assertEquals('http://localhost/hi.txt', $url);
    }

    public function testAutoScale()
    {
        $urlMock = Mockery::mock(UrlGenerator::class);
        $urlMock->shouldReceive('to')->once()->andReturnUsing(function ($path) {
            return realpath(__DIR__.'/../stubs'.$path);
        });

        $uploader = new ImageUploader($this->getFileSystemAdapter(), $urlMock, new ImageManager());
        $image = $uploader->upload(new UploadedFile(__DIR__.'/../stubs/laravel-l-slant.png', 'laravel-l-slant.png'));
        $this->assertEquals(450, getimagesize($image)[0]);

    }

    public function testString()
    {
        $filesMock = Mockery::mock(FilesystemAdapter::class);
        $filesMock->shouldReceive('put')->with(typeOf('string'), typeOf('string'))->once()->andReturn(true);
        $filesMock->shouldReceive('url')->with(typeOf('string'))->once()->andReturn('/hi.txt');

        $urlMock = Mockery::mock(UrlGenerator::class);
        $urlMock->shouldReceive('to')->with('/hi.txt')->once()->andReturn('http://localhost/hi.txt');

        $uploader = new ImageUploader($filesMock, $urlMock, new ImageManager());

        $uploader->upload(new UploadedFile(__DIR__.'/../stubs/laravel-l-slant.png', 'laravel-l-slant.png'));
    }

    private function getFileSystemAdapter()
    {
        return new FilesystemAdapter($this->getFileSystem());
    }

    private function getFileSystem()
    {
        return new Filesystem(new Local(__DIR__.'/../stubs/storage'));
    }
}

