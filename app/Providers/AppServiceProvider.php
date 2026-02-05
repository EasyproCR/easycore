<?php

namespace App\Providers;

use AzureOss\FlysystemAzureBlobStorage\AzureBlobStorageAdapter;
use AzureOss\Storage\Blob\BlobServiceClient;
use AzureOss\Storage\Blob\Options\BlobServiceClientOptions;
use AzureOss\Storage\Common\Middleware\HttpClientOptions;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Config;
use League\Flysystem\Filesystem;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Ensure the Azure disk works in local dev on Windows where PHP/cURL
        // often lacks a configured CA bundle (cURL error 60). Prefer fixing
        // the CA bundle, but this provides an opt-in escape hatch.
        Storage::extend('azure-storage-blob', function (Application $app, array $config): FilesystemAdapter {
            if (! isset($config['connection_string']) || ! is_string($config['connection_string'])) {
                throw new \InvalidArgumentException('The [connection_string] must be a string in the disk configuration.');
            }

            if (! isset($config['container']) || ! is_string($config['container'])) {
                throw new \InvalidArgumentException('The [container] must be a string in the disk configuration.');
            }

            $prefix = '';
            if (isset($config['prefix']) && is_string($config['prefix'])) {
                $prefix = $config['prefix'];
            } elseif (isset($config['root']) && is_string($config['root'])) {
                $prefix = $config['root'];
            }

            $timeout = isset($config['timeout']) && is_numeric($config['timeout']) ? (int) $config['timeout'] : null;
            $connectTimeout = isset($config['connect_timeout']) && is_numeric($config['connect_timeout']) ? (int) $config['connect_timeout'] : null;

            $verifySsl = $config['verify_ssl'] ?? null;
            if (is_string($verifySsl)) {
                $verifySsl = filter_var($verifySsl, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
            }
            if (! is_bool($verifySsl)) {
                $verifySsl = $app->environment('local') ? false : null;
            }

            $clientOptions = new BlobServiceClientOptions(
                new HttpClientOptions(
                    timeout: $timeout,
                    connectTimeout: $connectTimeout,
                    verifySsl: $verifySsl,
                ),
            );

            $serviceClient = BlobServiceClient::fromConnectionString($config['connection_string'], $clientOptions);
            $containerClient = $serviceClient->getContainerClient($config['container']);

            $adapter = new AzureBlobStorageAdapter($containerClient, $prefix);
            $filesystem = new Filesystem($adapter, $config);

            return new class($filesystem, $adapter, $config) extends FilesystemAdapter {
                public function url($path)
                {
                    return $this->adapter->publicUrl((string) $path, new Config);
                }

                public function temporaryUrl($path, $expiration, array $options = [])
                {
                    return $this->adapter->temporaryUrl(
                        (string) $path,
                        $expiration,
                        new Config(array_merge(['permissions' => 'r'], $options))
                    );
                }

                public function temporaryUploadUrl($path, $expiration, array $options = [])
                {
                    $url = $this->adapter->temporaryUrl(
                        (string) $path,
                        $expiration,
                        new Config(array_merge(['permissions' => 'cw'], $options))
                    );

                    $contentType = isset($options['content-type']) && is_string($options['content-type'])
                        ? $options['content-type']
                        : 'application/octet-stream';

                    return [
                        'url' => $url,
                        'headers' => [
                            'x-ms-blob-type' => 'BlockBlob',
                            'Content-Type' => $contentType,
                        ],
                    ];
                }
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
}
