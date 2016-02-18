<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://microffice';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    /**
    * Migrate and seed the database
    */
    protected function setDB($database)
    {
        Config::set('database.default', $database);
        if($database == 'sqlite' || $database == 'sqlite_memory')
        {
            Artisan::call('migrate:refresh');
        }
    }

    /**
    * Mock raw() on Gate
    */
    protected function allow($n)
    {
        $mock = $this->getRawMock();
        $mock = $this->setTimes($mock, $n, true);
        // Swap facade's underlying instances
        Gate::swap($mock);
    }

    /**
    * Mock raw() on Gate
    */
    protected function deny($n)
    {
        $mock = $this->getRawMock();
        $mock = $this->setTimes($mock, $n, false);
        // Swap facade's underlying instances
        Gate::swap($mock);
    }

    /**
    * Set Gate::raw() mock
    */
    protected function getRawMock()
    {
        // Gate partial mock for raw()
        return Mockery::mock(Illuminate\Auth\Access\Gate::class . '[raw]', [$app = app(), function () use ($app) {
                return $app['auth']->user();
            }]);
    }

    /**
    * Set times on mock
    */
    protected function setTimes($mock, $n, $bool)
    {
        if(is_int($n))
        {
            $mock->shouldAllowMockingProtectedMethods()->shouldReceive('raw')->andReturn($bool)->times($n);
        }
        else
        {
            $mock->shouldAllowMockingProtectedMethods()->shouldReceive('raw')->andReturn($bool)->$n();
        }

        return $mock;
    }

    /**
    * tearDown is called after each test
    * @return [type] [description]
    */
    public function tearDown()
    {
        Mockery::close();
    }
}
