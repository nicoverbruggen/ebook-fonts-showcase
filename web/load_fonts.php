<?php
if (!defined('APP_ROOT')) {
    http_response_code(403);
    exit;
}

$fontRoot = APP_ROOT . '/repos/ebook-fonts/fonts';
$fontEntries = [];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($fontRoot, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $fileInfo) {
    if (!$fileInfo->isFile()) {
        continue;
    }
    if (strtolower($fileInfo->getExtension()) !== 'ttf') {
        continue;
    }
    if (str_contains($fileInfo->getFilename(), 'NV_OpenDyslexic')) {
        continue;
    }
    $relative = str_replace(APP_ROOT . '/', '', $fileInfo->getPathname());
    $relative = str_replace(DIRECTORY_SEPARATOR, '/', $relative);
    $fontEntries[] = $relative;
}

sort($fontEntries, SORT_NATURAL | SORT_FLAG_CASE);
$fontFilesJson = json_encode($fontEntries, JSON_UNESCAPED_SLASHES);
if ($fontFilesJson === false) {
    $fontFilesJson = '[]';
}
