<?php

namespace JeroenNoten\LaravelCkEditor;

use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use JeroenNoten\LaravelAdminLte\ServiceProvider as AdminLteServiceProvider;

class ServiceProvider extends BaseServiceProvider
{

    public function boot()
    {
        $this->bladeDirective();

        $this->loadViews();

        $this->publishAssets();
    }

    public function register()
    {
        //
    }

    private function bladeDirective()
    {
        \Blade::directive('ckeditor', function ($expression) {
            $contentClass = CkEditor::class;
            return "<?={$contentClass}::editor();?>";
        });
    }

    private function loadViews()
    {
        $viewsPath = $this->packagePath('resources/views');

        $this->loadViewsFrom($viewsPath, 'ckeditor');

        $this->publishes([
            $viewsPath => base_path('resources/views/vendor/adminlte'),
        ], 'views');
    }

    private function publishAssets()
    {
        $this->publishes([
            $this->packagePath('resources/assets') => public_path('vendor/ckeditor'),
        ], 'assets');
    }

    private function packagePath($path)
    {
        return __DIR__ . "/../$path";
    }
}