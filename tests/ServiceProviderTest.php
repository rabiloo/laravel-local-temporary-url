<?php

namespace Test;

use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase;
use Rabiloo\Laravel\LocalTemporaryUrl\ServiceProvider;

class ServiceProviderTest extends TestCase
{
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

    /** @test */
    public function it_should_has_temporary_url()
    {
        $path = 'test/file.txt';
        Storage::disk('local')->put($path, 'Hello world');

        $url = Storage::disk('local')->temporaryUrl($path, now()->addSeconds(1));
        $this->assertIsString($url);

        $response = $this->get($url);
        $response->assertOk()
            ->assertDownload('file.txt');

        sleep(2);

        $response = $this->get($url);
        $response->assertForbidden();
    }
}
