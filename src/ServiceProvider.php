<?php

namespace Rabiloo\Laravel\LocalTemporaryUrl;

use DateTimeInterface;
use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $local = env('LOCAL_TEMPORARY_URL', 'local');

        if (! ($this->app instanceof CachesRoutes && $this->app->routesAreCached())) {
            Route::get($local.'/temp/{path}', function (string $path): StreamedResponse {
                /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
                $disk = Storage::disk($local);

                return $disk->download($path);
            })
                ->where('path', '.*')
                ->name($local.'.temp')
                ->middleware(['web', 'signed']);
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk($local);
        $disk->buildTemporaryUrlsUsing(
            function (string $path, DateTimeInterface $expiration, array $options = []) {
                return URL::temporarySignedRoute(
                    $local.'.temp',
                    $expiration,
                    array_merge($options, ['path' => $path])
                );
            }
        );
    }
}
