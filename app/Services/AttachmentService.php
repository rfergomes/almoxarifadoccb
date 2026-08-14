<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentService
{
    /**
     * Armazena um arquivo enviado no disco public e cria o registro de Attachment vinculado.
     */
    public function uploadAttachment(UploadedFile $file, Model $attachable, int $userId, string $subfolder = 'general'): Attachment
    {
        $yearMonth = date('Y/m');
        $extension = $file->getClientOriginalExtension() ?: 'bin';
        $fileName = Str::uuid()->toString() . '.' . $extension;
        $directory = "attachments/{$subfolder}/{$yearMonth}";

        $filePath = $file->storeAs($directory, $fileName, 'public');

        return Attachment::create([
            'attachable_type' => get_class($attachable),
            'attachable_id' => $attachable->getKey(),
            'file_path' => $filePath,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType() ?: $file->getMimeType() ?: 'application/octet-stream',
            'file_size' => $file->getSize(),
            'uploaded_by' => $userId,
        ]);
    }

    /**
     * Exclui o arquivo do armazenamento físico e remove o registro do banco de dados.
     */
    public function deleteAttachment(Attachment $attachment): bool
    {
        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        return (bool) $attachment->delete();
    }

    /**
     * Substitui o anexo atual de um modelo por um novo arquivo enviado.
     */
    public function replaceAttachment(UploadedFile $file, Model $attachable, int $userId, string $subfolder = 'general'): Attachment
    {
        if (method_exists($attachable, 'attachment') && $attachable->attachment) {
            $this->deleteAttachment($attachable->attachment);
        }

        return $this->uploadAttachment($file, $attachable, $userId, $subfolder);
    }
}
