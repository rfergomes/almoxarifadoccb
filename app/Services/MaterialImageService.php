<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MaterialImageService
{
    /**
     * Armazena um arquivo de imagem no disco público e retorna o caminho relativo gerado.
     */
    public function uploadImage(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg';
        $fileName = Str::uuid()->toString() . '.' . strtolower($extension);
        $directory = 'materials/images';

        return $file->storeAs($directory, $fileName, 'public');
    }

    /**
     * Exclui o arquivo físico de imagem do disco público, se existir.
     */
    public function deleteImage(?string $path): bool
    {
        if (!empty($path) && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }

    /**
     * Substitui a imagem existente por uma nova, excluindo a anterior do disco público.
     */
    public function replaceImage(UploadedFile $file, ?string $oldPath = null): string
    {
        if (!empty($oldPath)) {
            $this->deleteImage($oldPath);
        }

        return $this->uploadImage($file);
    }
}
