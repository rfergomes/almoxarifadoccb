<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Services\AttachmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentController extends Controller
{
    public function __construct(
        protected AttachmentService $attachmentService
    ) {}

    public function download(Attachment $attachment): StreamedResponse
    {
        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'Arquivo de anexo não encontrado no servidor.');
        }

        return Storage::disk('public')->download(
            $attachment->file_path,
            $attachment->original_name
        );
    }

    public function destroy(Attachment $attachment): JsonResponse
    {
        $this->attachmentService->deleteAttachment($attachment);

        return response()->json([
            'success' => true,
            'message' => 'Anexo removido com sucesso!',
        ]);
    }
}
