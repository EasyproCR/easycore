<?php

$azureConnectionString = env('AZURE_STORAGE_CONNECTION_STRING');
$azureContainer = env('AZURE_STORAGE_CONTAINER', 'public');

$azureStorageUrl = env('AZURE_STORAGE_URL');
if (! $azureStorageUrl && is_string($azureConnectionString)) {
    preg_match('/AccountName=([^;]+)/', $azureConnectionString, $accountMatch);
    preg_match('/EndpointSuffix=([^;]+)/', $azureConnectionString, $suffixMatch);

    $accountName = $accountMatch[1] ?? null;
    $endpointSuffix = $suffixMatch[1] ?? 'core.windows.net';

    if (is_string($accountName) && $accountName !== '') {
        $azureStorageUrl = "https://{$accountName}.blob.{$endpointSuffix}/{$azureContainer}";
    }
}

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'azure_public'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

        'azure_public' => [
            'driver' => 'azure-storage-blob',
            'connection_string' => env('AZURE_STORAGE_CONNECTION_STRING'),
            'container' => env('AZURE_STORAGE_CONTAINER', 'public'),

            // Used by some UI components (e.g. FileUpload previews).
            // If AZURE_STORAGE_URL is not set, we derive it from the connection string.
            'url' => $azureStorageUrl,

            'prefix' => env('AZURE_STORAGE_PREFIX'),
            'visibility' => 'public',
            'throw' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
