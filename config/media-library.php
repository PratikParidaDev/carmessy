<?php

return [
    'disk_name' => env('MEDIA_DISK', 'public'),
    
    'max_file_size' => 1024 * 1024 * 10, // 10MB
    
    'queue_name' => env('QUEUE_NAME', 'default'),
    
    'queue_conversions_by_default' => env('QUEUE_CONVERSIONS_BY_DEFAULT', true),
    
    'media_model' => Spatie\MediaLibrary\MediaCollections\Models\Media::class,
    
    'temporary_upload_model' => Spatie\MediaLibrary\MediaCollections\Models\Media::class,
    
    'enable_temporary_uploads_session_affinity' => true,
    
    'generate_thumbnails_for_temporary_uploads' => true,
    
    'file_namer' => Spatie\MediaLibrary\Support\FileNamer\DefaultFileNamer::class,
    
    'path_generator' => Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator::class,
    
    'url_generator' => Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator::class,
    
    'moves_media_on_update' => false,
    
    'version_urls' => false,
    
    'image_optimizers' => [
        Spatie\ImageOptimizer\Optimizers\Jpegoptim::class => [
            '-m85',
            '--force',
            '--strip-all',
            '--all-progressive',
        ],
        Spatie\ImageOptimizer\Optimizers\Pngquant::class => [
            '--force',
        ],
    ],
    
    'image_generators' => [
        Spatie\MediaLibrary\Conversions\ImageGenerators\Image::class,
    ],
    
    'image_driver' => env('IMAGE_DRIVER', 'gd'),
    
    'ffmpeg_path' => env('FFMPEG_PATH', '/usr/bin/ffmpeg'),
    
    'ffprobe_path' => env('FFPROBE_PATH', '/usr/bin/ffprobe'),
    
    'jobs' => [
        'perform_conversions' => Spatie\MediaLibrary\Conversions\Jobs\PerformConversionsJob::class,
        'generate_responsive_images' => Spatie\MediaLibrary\ResponsiveImages\Jobs\GenerateResponsiveImagesJob::class,
    ],
    
    'media_downloader' => Spatie\MediaLibrary\Downloaders\DefaultDownloader::class,
    
    'remote' => [
        'extra_headers' => [
            'CacheControl' => 'max-age=604800',
        ],
    ],
    
    'responsive_images' => [
        'width_calculator' => Spatie\MediaLibrary\ResponsiveImages\WidthCalculator\FileSizeOptimizedWidthCalculator::class,
        'use_tiny_placeholders' => true,
        'tiny_placeholder_generator' => Spatie\MediaLibrary\ResponsiveImages\TinyPlaceholderGenerator\Blurred::class,
    ],
    
    'enable_vapor_uploads' => env('ENABLE_VAPOR_UPLOADS', false),
    
    'default_loading_attribute_value' => null,
];
