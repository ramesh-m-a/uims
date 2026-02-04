<?php

$dir = __DIR__ . '/resources/views';
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($dir)
);

$pattern = '/<div class="flex justify-between items-center">([\s\S]*?)<h1[^>]*>([\s\S]*?)<span[^>]*>››<\/span>\s*<span[^>]*>(.*?)<\/span>[\s\S]*?<\/h1>([\s\S]*?)<\/div>/m';

foreach ($iterator as $file) {
    if (!$file->isFile()) continue;
    if (!str_ends_with($file->getFilename(), '.blade.php')) continue;

    $path = $file->getPathname();
    $content = file_get_contents($path);

    $new = preg_replace_callback($pattern, function ($m) {
        $title = trim(strip_tags($m[2]));
        $subtitle = trim(strip_tags($m[3]));
        $body = trim($m[4]);

        return <<<BLADE
<x-sub-header title="$title" subtitle="$subtitle">
$body
</x-sub-header>
BLADE;
    }, $content, -1, $count);

    if ($count > 0) {
        file_put_contents($path, $new);
        echo "✔ Updated: $path\n";
    }
}

echo "\nDone.\n";
