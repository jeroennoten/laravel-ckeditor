<?php


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Laravel\BrowserKitTesting\Concerns\InteractsWithSession;
use Laravel\BrowserKitTesting\Constraints\HasSource;
use Laravel\BrowserKitTesting\Constraints\PageConstraint;
use Laravel\BrowserKitTesting\Constraints\ReversePageConstraint;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use PHPUnit_Framework_Assert as PHPUnit;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use InteractsWithSession;

    protected $crawler;
    protected $response;

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

    public function assertRedirectedTo($uri, $with = [])
    {
        PHPUnit::assertInstanceOf('Illuminate\Http\RedirectResponse', $this->response);

        PHPUnit::assertEquals($this->app['url']->to($uri), $this->response->headers->get('Location'));

        $this->assertSessionHasAll($with);

        return $this;
    }

    public function see($text, $negate = false)
    {
        return $this->_assertInPage(new HasSource($text), $negate);
    }

    public function seeJson(array $data = null, $negate = false)
    {
        if (is_null($data)) {
            $this->assertJson(
                $this->response->getContent(), "JSON was not returned from [{$this->currentUri}]."
            );

            return $this;
        }

        try {
            return $this->seeJsonEquals($data);
        } catch (PHPUnit_Framework_ExpectationFailedException $e) {
            return $this->seeJsonContains($data, $negate);
        }
    }

    public function seeJsonEquals(array $data)
    {
        $actual = json_encode(Arr::sortRecursive(
            (array) $this->decodeResponseJson()
        ));

        $this->assertEquals(json_encode(Arr::sortRecursive($data)), $actual);

        return $this;
    }

    protected function _assertInPage(PageConstraint $constraint, $reverse = false, $message = '')
    {
        if ($reverse) {
            $constraint = new ReversePageConstraint($constraint);
        }

        self::assertThat(
            $this->crawler() ?: $this->response->getContent(),
            $constraint, $message
        );

        return $this;
    }

    protected function crawler()
    {
        if (! empty($this->subCrawlers)) {
            return end($this->subCrawlers);
        }

        return $this->crawler;
    }

    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        $kernel = $this->app->make('Illuminate\Contracts\Http\Kernel');

        $this->currentUri = $this->prepareUrlForRequest($uri);

        $this->resetPageContext();

        $symfonyRequest = SymfonyRequest::create(
            $this->currentUri, $method, $parameters,
            $cookies, $this->filterFiles($files), array_replace($this->serverVariables, $server), $content
        );

        $request = Request::createFromBase($symfonyRequest);

        $response = $kernel->handle($request);

        $kernel->terminate($request, $response);

        return $this->response = $response;
    }

    protected function resetPageContext()
    {
        $this->crawler = null;

        $this->subCrawlers = [];
    }
    protected function filterFiles($files)
    {
        foreach ($files as $key => $file) {
            if ($file instanceof UploadedFile) {
                continue;
            }

            if (is_array($file)) {
                if (! isset($file['name'])) {
                    $files[$key] = $this->filterFiles($files[$key]);
                } elseif (isset($files[$key]['error']) && $files[$key]['error'] !== 0) {
                    unset($files[$key]);
                }

                continue;
            }

            unset($files[$key]);
        }

        return $files;
    }
    protected function decodeResponseJson()
    {
        $decodedResponse = json_decode($this->response->getContent(), true);

        if (is_null($decodedResponse) || $decodedResponse === false) {
            $this->fail('Invalid JSON was returned from the route. Perhaps an exception was thrown?');
        }

        return $decodedResponse;
    }
}