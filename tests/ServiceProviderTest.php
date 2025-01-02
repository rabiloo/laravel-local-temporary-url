<?php

namespace Test;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase;
use Rabiloo\Laravel\LocalTemporaryUrl\ServiceProvider;

class ServiceProviderTest extends TestCase
{
    /**
     * Define environment setup.
     *
     * @api
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app)
    {
        // Setup local filesystem config
        tap($app['config'], function (Repository $config) {
            // enable serve file
            $config->set('filesystems.disks.local.serve', true);
        });
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_should_has_temporary_url()
    {
        $path = 'test/file.txt';
        $content = 'Hello world';
        Storage::disk('local')->put($path, $content);

        $url = Storage::disk('local')->temporaryUrl($path, now()->addSeconds(1));
        $this->assertIsString($url);

        $response = $this->get($url);
        $response->assertOk()
            ->assertHeader('Content-Disposition', 'inline; filename=file.txt')
            ->assertStreamedContent($content);

        // Sleep 2 seconds
        sleep(2);

        $response = $this->get($url);
        $response->assertForbidden();
    }
}
