<?php

use JeroenNoten\LaravelCkEditor\CkEditor;

class CkEditorTest extends TestCase
{
    public function testJavascriptLibraryOnly()
    {
        $this->assertScript(true);
    }

    public function testNamedTextField()
    {
        $this->assertScript(true, 'textfield');
    }

    public function testTwoInstances()
    {
        $this->assertScript(true, 'textfield1');
        $this->assertScript(false, 'textfield2');
    }

    public function testOverrideConfig()
    {
        $config = $this->getConfig();
        $config['filebrowserImageUploadUrl'] = '/upload.php';

        $this->assertScript(true, 'textfield2', $config);
    }

    private function assertScript($lib, $editor = null, $config = null)
    {
        $instance = app(CkEditor::class);

        $config = $config ? $config : $this->getConfig();

        $this->assertEquals(
            ($lib ? "<script src=\"http://localhost/vendor/ckeditor/ckeditor.js\"></script>\n" : '') .
            ($lib && $editor ? '    ' : '') .
            ($editor ? "<script>CKEDITOR.replace(\"$editor\", " . json_encode($config) . ");</script>\n" : ''),
            $instance->editor($editor, $config)
        );
    }

    private function getConfig()
    {
        return [
            'filebrowserImageUploadUrl' => route('ckeditor.images.store'),
            'uploadUrl' => route('ckeditor.images.store', 'json'),
            'extraPlugins' => 'uploadimage'
        ];
    }
}