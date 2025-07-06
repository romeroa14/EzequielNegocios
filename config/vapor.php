<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Vapor Asset URL
    |--------------------------------------------------------------------------
    |
    | If you want to use the Vapor CDN to serve your assets, you should specify
    | the URL for the CDN here. This URL will be prepended to all asset paths
    | when serving your application in production.
    |
    */

    'asset_url' => env('ASSET_URL'),

    /*
    |--------------------------------------------------------------------------
    | Vapor Storage Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default storage driver that will be used by your
    | application when storing files using Vapor. This driver will handle the
    | storage of files on S3 and ensure they are properly secured.
    |
    */

    'storage_driver' => env('VAPOR_STORAGE_DRIVER', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Vapor Storage Bucketublic
    |--------------------------------------------------------------------------
    |
    | This option controls the default storage bucket that will be used by your
    | application when storing files using Vapor. You may configure multiple
    | buckets and specify which one should be used for each storage disk.
    |
    */

    'bucket' => env('VAPOR_ARTIFACT_BUCKET'),

    /*
    |--------------------------------------------------------------------------
    | Vapor Storage Region
    |--------------------------------------------------------------------------
    |
    | This option controls the default storage region that will be used by your
    | application when storing files using Vapor. You may configure multiple
    | regions and specify which one should be used for each storage disk.
    |
    */

    'region' => env('VAPOR_ARTIFACT_REGION', 'us-east-1'),
]; 