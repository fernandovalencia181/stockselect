<?php

namespace App\Utils;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;

class ImageHelper
{
    /**
     * Optimiza una imagen: escala a 1200px máx y convierte a WebP.
     * Acepta tanto UploadedFile como TemporaryUploadedFile de Livewire.
     */
    public static function optimizeToWebp(UploadedFile $file, string $directory = 'products'): string
    {
        if (class_exists(ImageManager::class)) {
            try {
                $manager = new ImageManager(new Driver());
                $img = $manager->decode(file_get_contents($file->getRealPath()));
                
                // Redimensionar proporcionalmente a máx 1200px
                $img->scale(width: 1200, height: 1200);
                
                // Codificar a WebP con calidad 80 usando sintaxis v4
                $encoded = $img->encode(new WebpEncoder(quality: 80));
                
                $name = Str::uuid() . '.webp';
                $path = $directory . '/' . $name;
                
                Storage::disk('public')->put($path, (string)$encoded);
                
                return $path;
            } catch (\Exception $e) {
                // Fallback si falla el procesamiento
                return $file->store($directory, 'public');
            }
        }

        return $file->store($directory, 'public');
    }
}
