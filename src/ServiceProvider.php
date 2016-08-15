<?php

namespace JeroenNoten\LaravelCkEditor;

use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use JeroenNoten\LaravelCkEditor\Uploads\ImageUploader;
use JeroenNoten\LaravelPackageHelper\ServiceProviderTraits\Assets;
use JeroenNoten\LaravelPackageHelper\ServiceProviderTraits\BladeDirective;
use JeroenNoten\LaravelPackageHelper\ServiceProviderTraits\Views;

class ServiceProvider extends BaseServiceProvider
{
    use BladeDirective, Views, Assets;

    public function boot(Router $router)
    {
        $this->bladeDirective('ckeditor', CkEditor::class, 'editor');
        $this->loadViews();
        $this->publishAssets();
        $this->registerRoutes($router);
    }

    public function register()
    {
        $this->app->singleton(CkEditor::class);

        $this->app->singleton(ImageUploader::class, function (Application $app) {
            $storage = $this->app->make(Factory::class);
            $url = $this->app->make(UrlGenerator::class);
            return new ImageUploader($storage->disk('public'), $url);
        });
    }

    protected function path()
    {
        return __DIR__.'/..';
    }

    protected function name()
    {
        return 'ckeditor';
    }

    private function registerRoutes(Router $router)
    {
        $router->group([
            'prefix' => 'ckeditor',
            'as' => 'ckeditor.',
            'middleware' => ['api', StartSession::class],
            'namespace' => __NAMESPACE__ . '\\Http\\Controllers'
        ], function (Router $router) {

            $router->post('images', 'Images@store')->name('images.store');

        });
    }
}