#!/usr/bin/env php
<?php

$dir = $argv[1] ?? '/var/www/html/wp-content/uploads';
$quality = (int) ($argv[2] ?? 82);

if ( ! is_dir( $dir ) ) {
    fwrite( STDERR, "Directory not found: {$dir}\n" );
    exit( 1 );
}

$it = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator( $dir, RecursiveDirectoryIterator::SKIP_DOTS )
);

$count    = 0;
$skipped  = 0;
$errors   = 0;

foreach ( $it as $file ) {
    $ext = strtolower( $file->getExtension() );
    if ( ! in_array( $ext, [ 'jpg', 'jpeg' ], true ) ) {
        continue;
    }

    try {
        $img = new Imagick( $file->getRealPath() );
        $current = $img->getImageCompressionQuality();
        if ( $current <= $quality ) {
            $img->clear();
            $skipped++;
            continue;
        }
        $img->setImageCompressionQuality( $quality );
        $img->stripImage();
        $img->writeImage();
        $img->clear();
        $count++;
        echo "  OK  {$file->getFilename()} ({$current}% -> {$quality}%)\n";
    } catch ( Exception $e ) {
        $errors++;
        echo "FAIL  {$file->getFilename()}: {$e->getMessage()}\n";
    }
}

echo "\n---\nCompressed: {$count}, Skipped: {$skipped}, Errors: {$errors}\n";
