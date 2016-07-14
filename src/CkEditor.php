<?php


namespace JeroenNoten\LaravelCkEditor;


use View;

class CkEditor
{
    public static function editor()
    {
        return View::make('ckeditor::js')->render();
    }
}