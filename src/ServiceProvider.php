<?php

namespace Rabiloo\Laravel\LocalTemporaryUrl;

use DateTimeInterface;
use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app instanceof CachesRoutes && $this->app->routesAreCached()) {
            return;
        }

        foreach ($this->app['config']['filesystems.disks'] ?? [] as $disk => $config) {
            if (! $this->shouldServeFiles($config)) {
                continue;
            }

            $this->app->booted(function ($app) use ($disk, $config) {
                $uri = isset($config['url'])
                    ? rtrim(parse_url($config['url'])['path'], '/')
                    : $disk . '/temp';

                $isProduction = $app->isProduction();

                Route::get(
                    $uri . '/{path}',
                    fn (Request $request, string $path) => (new ServeFile(
                        $disk,
                        $config,
                        $isProduction
                    ))($request, $path)
                )->where('path', '.*')->name($disk . '.temp');

                Storage::disk($disk)->buildTemporaryUrlsUsing(
                    fn (string $path, DateTimeInterface $expiration, array $options = []) => URL::temporarySignedRoute(
                        $disk . '.temp',
                        $expiration,
                        array_merge($options, ['path' => $path]),
                        false,
                    )
                );
            });
        }
    }

    /**
     * Determine if the disk is serveable.
     *
     * @param  array  $config
     * @return bool
     */
    protected function shouldServeFiles(array $config)
    {
        return $config['driver'] === 'local' && ($config['serve'] ?? false);
    }
}
