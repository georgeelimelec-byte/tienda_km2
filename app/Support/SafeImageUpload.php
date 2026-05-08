<?php

namespace App\Support;

use Illuminate\Validation\ValidationException;

class SafeImageUpload
{
    private const MAX_BYTES = 5242880;
    private const MAX_DIMENSION = 8000;
    private const OUTPUT_MAX_WIDTH = 2400;
    private const OUTPUT_MAX_HEIGHT = 2400;

    private const ALLOWED_MIMES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    public static function assertValid($file): void
    {
        $inspection = self::inspect($file);
        $source = self::imageFromPath($inspection['path'], $inspection['mime']);

        if (!$source) {
            self::fail('La imagen no se pudo procesar con seguridad.');
        }

        imagedestroy($source);
    }

    public static function store($file, string $publicDirectory, string $prefix): string
    {
        $inspection = self::inspect($file);
        $sourcePath = $inspection['path'];
        $mime = $inspection['mime'];

        $source = self::imageFromPath($sourcePath, $mime);
        if (!$source) {
            self::fail('La imagen no se pudo procesar con seguridad.');
        }

        $targetSize = self::targetSize(imagesx($source), imagesy($source));
        $clean = imagecreatetruecolor($targetSize['width'], $targetSize['height']);
        imagealphablending($clean, false);
        imagesavealpha($clean, true);
        imagefill($clean, 0, 0, imagecolorallocatealpha($clean, 255, 255, 255, 127));

        imagecopyresampled(
            $clean,
            $source,
            0,
            0,
            0,
            0,
            $targetSize['width'],
            $targetSize['height'],
            imagesx($source),
            imagesy($source)
        );

        $publicDirectory = trim(str_replace('\\', '/', $publicDirectory), '/');
        $directory = public_path($publicDirectory);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = preg_replace('/[^a-z0-9_\\-]/i', '_', $prefix)
            . '_' . date('YmdHis')
            . '_' . bin2hex(random_bytes(8))
            . '.webp';

        $destination = $directory . DIRECTORY_SEPARATOR . $filename;
        $saved = imagewebp($clean, $destination, 86);

        imagedestroy($source);
        imagedestroy($clean);

        if (!$saved) {
            self::fail('No se pudo guardar la imagen procesada.');
        }

        return asset($publicDirectory . '/' . $filename);
    }

    private static function inspect($file): array
    {
        if (!$file || !method_exists($file, 'getRealPath')) {
            self::fail('No se pudo leer la imagen subida.');
        }

        if (method_exists($file, 'isValid') && !$file->isValid()) {
            self::fail('La imagen subida no es valida.');
        }

        $sourcePath = $file->getRealPath();
        if (!$sourcePath || !is_file($sourcePath)) {
            self::fail('La imagen subida no se encuentra disponible.');
        }

        $size = method_exists($file, 'getSize') ? (int) $file->getSize() : (int) filesize($sourcePath);
        if ($size <= 0 || $size > self::MAX_BYTES) {
            self::fail('La imagen supera el limite permitido de 5 MB.');
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($sourcePath) ?: '';
        if (!in_array($mime, self::ALLOWED_MIMES, true)) {
            self::fail('Solo se permiten imagenes JPG, PNG o WebP.');
        }

        $imageInfo = @getimagesize($sourcePath);
        if (!$imageInfo || empty($imageInfo[0]) || empty($imageInfo[1])) {
            self::fail('El archivo no es una imagen valida.');
        }

        [$width, $height] = [(int) $imageInfo[0], (int) $imageInfo[1]];
        if ($width > self::MAX_DIMENSION || $height > self::MAX_DIMENSION) {
            self::fail('La imagen es demasiado grande para procesarla con seguridad.');
        }

        return [
            'path' => $sourcePath,
            'mime' => $mime,
            'width' => $width,
            'height' => $height,
        ];
    }

    private static function imageFromPath(string $path, string $mime)
    {
        return match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/webp' => @imagecreatefromwebp($path),
            default => false,
        };
    }

    private static function targetSize(int $width, int $height): array
    {
        $ratio = min(self::OUTPUT_MAX_WIDTH / $width, self::OUTPUT_MAX_HEIGHT / $height, 1);

        return [
            'width' => max(1, (int) floor($width * $ratio)),
            'height' => max(1, (int) floor($height * $ratio)),
        ];
    }

    private static function fail(string $message): void
    {
        throw ValidationException::withMessages([
            'imagen' => $message,
        ]);
    }
}
