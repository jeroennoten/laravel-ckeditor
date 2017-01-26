<?php


class TestCase extends \Orchestra\Testbench\TestCase
{
    use \Laravel\BrowserKitTesting\Concerns\MakesHttpRequests;
    use \Laravel\BrowserKitTesting\Concerns\InteractsWithSession;

    /** @before */
    public function injectDependencies()
    {
        $this->setDependencyInput(
            $this->getMethodDependencies([$this, $this->getName()])
        );
    }

    protected function getPackageProviders($app)
    {
        return [\JeroenNoten\LaravelCkEditor\ServiceProvider::class];
    }

    protected function getMethodDependencies($callback, array $parameters = [])
    {
        $dependencies = [];

        foreach ($this->getCallReflector($callback)->getParameters() as $parameter) {
            $this->addDependencyForCallParameter($parameter, $parameters, $dependencies);
        }

        return array_merge($dependencies, $parameters);
    }

    protected function getCallReflector($callback)
    {
        if (is_string($callback) && strpos($callback, '::') !== false) {
            $callback = explode('::', $callback);
        }

        if (is_array($callback)) {
            return new ReflectionMethod($callback[0], $callback[1]);
        }

        return new ReflectionFunction($callback);
    }

    protected function addDependencyForCallParameter(ReflectionParameter $parameter, array &$parameters, &$dependencies)
    {
        if (array_key_exists($parameter->name, $parameters)) {
            $dependencies[] = $parameters[$parameter->name];

            unset($parameters[$parameter->name]);
        } elseif ($parameter->getClass()) {
            $dependencies[] = $this->app->make($parameter->getClass()->name);
        } elseif ($parameter->isDefaultValueAvailable()) {
            $dependencies[] = $parameter->getDefaultValue();
        }
    }
}