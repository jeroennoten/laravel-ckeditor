<?php

use JeroenNoten\LaravelCkEditor\CkEditor;

class CkEditorTest extends \Orchestra\Testbench\TestCase
{
    public function testJavaLibraryOnly()
    {
        $this->assertEquals(
            "<script src=\"http://localhost/vendor/ckeditor/ckeditor.js\"></script>\n",
            app(CkEditor::class)->editor()
        );
    }

    public function testNamedTextField()
    {
        $this->assertEquals(
            "<script src=\"http://localhost/vendor/ckeditor/ckeditor.js\"></script>\n" .
            "    <script>CKEDITOR.replace(\"textfield\", null);</script>\n",
            app(CkEditor::class)->editor('textfield')
        );
    }

    public function testTwoInstances()
    {
        $this->assertEquals(
            "<script src=\"http://localhost/vendor/ckeditor/ckeditor.js\"></script>\n" .
            "    <script>CKEDITOR.replace(\"textfield1\", null);</script>\n",
            app(CkEditor::class)->editor('textfield1')
        );
        $this->assertEquals(
            "<script>CKEDITOR.replace(\"textfield2\", null);</script>\n",
            app(CkEditor::class)->editor('textfield2')
        );
    }

    protected function getPackageProviders($app)
    {
        return [\JeroenNoten\LaravelCkEditor\ServiceProvider::class];
    }
}