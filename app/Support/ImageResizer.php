<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageResizer
{
    /**
     * Resize an uploaded file and store it on the public disk, returning the
     * stored path. Used for every admin/user image upload (news covers,
     * title covers, avatars) so a raw phone photo never ends up served as-is.
     */
    public static function storeUploaded(UploadedFile $file, string $directory, int $maxWidth = 800, int $quality = 78): string
    {
        $path = $directory.'/'.Str::random(24).'.jpg';
        $resized = self::resize(file_get_contents($file->getRealPath()), $maxWidth, $quality);
        Storage::disk('public')->put($path, $resized);

        return $path;
    }

    /**
     * Resize raw downloaded bytes (from an external API/RSS feed) and store
     * them on the public disk, returning the stored path.
     */
    public static function storeBytes(string $bytes, string $directory, int $maxWidth = 800, int $quality = 78): string
    {
        $path = $directory.'/'.Str::random(24).'.jpg';
        Storage::disk('public')->put($path, self::resize($bytes, $maxWidth, $quality));

        return $path;
    }

    /**
     * Resize raw image bytes down to a max width and re-encode as JPEG at a
     * moderate quality, so downloaded covers/thumbnails never bloat page
     * weight. Uses GD (bundled with PHP, no extra dependency). Falls back to
     * the original bytes if GD can't decode the image for any reason.
     */
    public static function resize(string $bytes, int $maxWidth = 800, int $quality = 78): string
    {
        $source = @imagecreatefromstring($bytes);

        if ($source === false) {
            return $bytes;
        }

        $width = imagesx($source);
        $height = imagesy($source);

        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) round($height * ($maxWidth / $width));

            $resized = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($source);
            $source = $resized;
        }

        ob_start();
        imagejpeg($source, null, $quality);
        $output = ob_get_clean();
        imagedestroy($source);

        return $output ?: $bytes;
    }
}
