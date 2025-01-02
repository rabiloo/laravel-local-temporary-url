<?php

namespace Rabiloo\Laravel\LocalTemporaryUrl;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use League\Flysystem\PathTraversalDetected;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ServeFile
{
    /**
     * Create a new invokable controller to serve files.
     */
    public function __construct(
        protected string $disk,
        protected array $config,
        protected bool $isProduction,
    ) {
        //
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $path): StreamedResponse
    {
        abort_unless(
            $this->hasValidSignature($request),
            $this->isProduction ? 404 : 403
        );
        try {
            abort_unless(Storage::disk($this->disk)->exists($path), 404);

            /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
            $disk = Storage::disk($this->disk);
            $headers = [
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Content-Security-Policy' => "default-src 'none'; style-src 'unsafe-inline'; sandbox",
            ];

            return tap(
                $disk->response($path, null, $headers),
                function ($response) use ($headers) {
                    if (! $response->headers->has('Content-Security-Policy')) {
                        $response->headers->replace();
                    }
                }
            );
        } catch (PathTraversalDetected $e) {
            abort(404);
        }
    }

    /**
     * Determine if the request has a valid signature if applicable.
     */
    protected function hasValidSignature(Request $request): bool
    {
        return ($this->config['visibility'] ?? 'private') === 'public' || URL::hasValidRelativeSignature($request);
    }
}
