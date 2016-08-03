<?php


namespace JeroenNoten\LaravelCkEditor;


use Illuminate\Contracts\View\Factory;

class CkEditor
{
    private $view;

    private $instanceCount = 0;

    public function __construct(Factory $view)
    {
        $this->view = $view;
    }

    public function editor($name = null, $config = null)
    {
        $instanceCount = ++$this->instanceCount;
        return $this->view->make('ckeditor::js')->with(compact('name', 'config', 'instanceCount'))->render();
    }
}