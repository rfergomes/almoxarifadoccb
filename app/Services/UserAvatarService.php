<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserAvatarService
{
    /**
     * Armazena uma foto de avatar no disco público e retorna o caminho relativo gerado.
     */
    public function uploadAvatar(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg';
        $fileName = Str::uuid()->toString() . '.' . strtolower($extension);
        $directory = 'avatars';
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        return $file->storeAs($directory, $fileName, 'public');
    }

    /**
     * Exclui o arquivo físico de avatar do disco público, se existir.
     */
    public function deleteAvatar(?string $path): bool
    {
        if (!empty($path) && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }

    /**
     * Substitui o avatar existente por um novo, excluindo o arquivo anterior.
     */
    public function replaceAvatar(UploadedFile $file, ?string $oldPath = null): string
    {
        if (!empty($oldPath)) {
            $this->deleteAvatar($oldPath);
        }

        return $this->uploadAvatar($file);
    }
}
