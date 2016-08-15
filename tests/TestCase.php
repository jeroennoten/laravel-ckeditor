<?php


class TestCase extends \Orchestra\Testbench\TestCase
{
    /** @before */
    public function injectDependencies()
    {
        $this->setDependencyInput(
            $this->app->getMethodDependencies([$this, $this->getName()])
        );
    }

    protected function getPackageProviders($app)
    {
        return [\JeroenNoten\LaravelCkEditor\ServiceProvider::class];
    }
}