<?php


use JeroenNoten\LaravelCkEditor\ServiceProvider;
use JeroenNoten\LaravelCkEditor\Uploads\ImageUploader;
use League\Flysystem\Adapter\Local;
use League\Flysystem\AwsS3v3\AwsS3Adapter;

class ServiceProviderTest extends TestCase
{
    public function testConfig()
    {
        $this->assertEquals('public', config('ckeditor.disk'));
    }

    public function testDefaultDisk()
    {
        /** @var ImageUploader $imageUploader */
        $imageUploader = $this->app->make(ImageUploader::class);

        /** @var \League\Flysystem\Filesystem $driver */
        $driver = $imageUploader->getDisk()->getDriver();
        $this->assertInstanceOf(Local::class, $driver->getAdapter());
    }

    public function testCloudDisk()
    {
        $this->app['config']['ckeditor.disk'] = 's3';
        $this->app['config']['filesystems.disks.s3.region'] = 'your-region';

        /** @var ImageUploader $imageUploader */
        $imageUploader = $this->app->make(ImageUploader::class);

        /** @var \League\Flysystem\Filesystem $driver */
        $driver = $imageUploader->getDisk()->getDriver();
        $this->assertInstanceOf(AwsS3Adapter::class, $driver->getAdapter());
    }

    public function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }
}