<?php

namespace Rabiloo\Laravel\LocalTemporaryUrl;

use DateTimeInterface;
use Illuminate\Contracts\Foundation\CachesRoutes;
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
        if (! ($this->app instanceof CachesRoutes && $this->app->routesAreCached())) {
            Route::get('local/temp/{path}', fn (string $path) => Storage::disk('local')->download($path))
                ->where('path', '.*')
                ->name('local.temp')
                ->middleware(['web', 'signed']);
        }

        Storage::disk('local')
            ->buildTemporaryUrlsUsing(function (string $path, DateTimeInterface $expiration, array $options = []) {
                return URL::temporarySignedRoute(
                    'local.temp',
                    $expiration,
                    array_merge($options, ['path' => $path])
                );
            });
    }
}
