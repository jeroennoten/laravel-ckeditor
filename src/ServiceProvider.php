<?php

namespace JeroenNoten\LaravelCkEditor;

use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use JeroenNoten\LaravelAdminLte\ServiceProvider as AdminLteServiceProvider;
use JeroenNoten\LaravelPackageHelper\ServiceProviderTraits\Assets;
use JeroenNoten\LaravelPackageHelper\ServiceProviderTraits\BladeDirective;
use JeroenNoten\LaravelPackageHelper\ServiceProviderTraits\Views;

class ServiceProvider extends BaseServiceProvider
{
    use BladeDirective, Views, Assets;

    public function boot()
    {
        $this->bladeDirective('ckeditor', CkEditor::class, 'editor');
        $this->loadViews();
        $this->publishAssets();
    }

    public function register()
    {
        //
    }

    protected function path(): string
    {
        return __DIR__.'/..';
    }

    protected function name(): string
    {
        return 'ckeditor';
    }
}