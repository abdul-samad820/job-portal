<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait VerifiesUploadedFileMime
{
    protected function verifyStoredMime(string $disk, string $path, array $allowedMimes): bool
    {
        $absolutePath = Storage::disk($disk)->path($path);
        $mime = function_exists('mime_content_type')
            ? mime_content_type($absolutePath)
            : (new \finfo(FILEINFO_MIME_TYPE))->file($absolutePath);

        if (! $mime || ! in_array($mime, $allowedMimes, true)) {
            Storage::disk($disk)->delete($path);

            return false;
        }

        return true;
    }
}
