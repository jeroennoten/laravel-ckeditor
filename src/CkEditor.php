<?php


namespace JeroenNoten\LaravelCkEditor;


use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Session\Store;

class CkEditor
{
    private $view;
    private $url;
    private $session;
    private $instanceCount = 0;

    public function __construct(Factory $view, UrlGenerator $url, Store $session)
    {
        $this->view = $view;
        $this->url = $url;
        $this->session = $session;
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
            'filebrowserImageUploadUrl' => $this->url->route('ckeditor.images.store', ['_token' => $this->session->token()]),
            'uploadUrl' => $this->url->route('ckeditor.images.store', ['json', '_token' => $this->session->token()]),
            'extraPlugins' => 'uploadimage'
        ];
    }
}