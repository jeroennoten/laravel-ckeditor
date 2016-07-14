<?php


namespace JeroenNoten\LaravelCkEditor;


use Illuminate\Contracts\View\Factory;

class CkEditor
{
    private $view;

    public function __construct(Factory $view)
    {
        $this->view = $view;
    }

    public function editor($config = null)
    {
        return $this->view->make('ckeditor::js')->with(compact('config'));
    }
}