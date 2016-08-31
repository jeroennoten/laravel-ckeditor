<?php

namespace JeroenNoten\LaravelCkEditor;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Routing\Router;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Intervention\Image\ImageManager;
use JeroenNoten\LaravelCkEditor\Http\Middleware\VerifyCsrfToken;
use JeroenNoten\LaravelCkEditor\Uploads\ImageUploader;
use JeroenNoten\LaravelPackageHelper\ServiceProviderTraits;

class ServiceProvider extends BaseServiceProvider
{
    use ServiceProviderTraits;

    public function boot(Router $router)
    {
        $this->bladeDirective('ckeditor', CkEditor::class, 'editor');
        $this->loadViews();
        $this->publishAssets();
        $this->registerRoutes($router);
        $this->publishConfig();
    }

    public function register()
    {
        $this->app->singleton(CkEditor::class);

        $this->app->singleton(ImageUploader::class, function (Container $app) {
            $storage = $app->make(Factory::class);
            $url = $app->make(UrlGenerator::class);
            $config = $this->getConfig();
            $disk = $storage->disk($config['disk']);
            return new ImageUploader($disk, $url, new ImageManager(['drive' => 'imagick']));
        });
    }

    protected function path()
    {
        return __DIR__ . '/..';
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
            'middleware' => ['web', 'auth'],
            'namespace' => __NAMESPACE__ . '\\Http\\Controllers'
        ], function (Router $router) {

            $router->post('images', 'Images@store')->name('images.store');

        });
    }

    private function getConfig()
    {
        $config = $this->app->make(Repository::class);
        return $config['ckeditor'];
    }

    protected function getContainer()
    {
        return $this->app;
    }
}