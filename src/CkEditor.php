<?php


namespace JeroenNoten\LaravelCkEditor;


use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\View\Factory;

class CkEditor
{
    private $view;

    private $url;

    private $instanceCount = 0;

    public function __construct(Factory $view, UrlGenerator $url)
    {
        $this->view = $view;
        $this->url = $url;
    }

    public function editor($name = null, $config = null)
    {
        $instanceCount = ++$this->instanceCount;
        $config = ($config ?: []) + $this->config();
        return $this->view->make('ckeditor::js')->with(compact('name', 'config', 'instanceCount'))->render();
    }

    private function config()
    {
        return [
            'filebrowserImageUploadUrl' => $this->url->route('ckeditor.images.store'),
            'uploadUrl' => $this->url->route('ckeditor.images.store', 'json'),
            'extraPlugins' => 'uploadimage'
        ];
    }
}