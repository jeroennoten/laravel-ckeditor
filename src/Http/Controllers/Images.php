<?php


namespace JeroenNoten\LaravelCkEditor\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use JeroenNoten\LaravelCkEditor\Uploads\ImageUploader;

class Images extends Controller
{
    public function store(Request $request, ImageUploader $uploader)
    {
        $file = $request->file('upload');
        $url = $uploader->upload($file);

        if ($request->exists('json')) {
            return [
                'uploaded' => 1,
                'url' => $url
            ];
        }

        return view('ckeditor::upload', [
            'url' => $url,
            'funcNum' => (int)$request->input('CKEditorFuncNum')
        ]);
    }
}