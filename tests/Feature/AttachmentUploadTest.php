<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Attachment;
use App\Models\Category;
use App\Models\EntryDocument;
use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttachmentUploadTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Material $material;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        Storage::fake('public');

        $this->adminUser = User::where('email', 'admin@ccb.org.br')->first();

        $category = Category::create(['name' => 'Materiais Gerais']);
        $this->material = Material::create([
            'code_sku' => 'MAT-TEST-01',
            'name' => 'Material Teste Upload',
            'category_id' => $category->id,
            'unit_measure' => 'UN',
            'current_stock' => 10,
            'minimum_stock' => 2,
            'status' => true,
        ]);
    }

    public function test_entry_can_be_created_with_pdf_document_attachment(): void
    {
        $file = UploadedFile::fake()->create('Nota_Fiscal_1020.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->adminUser)
            ->post(route('entries.store'), [
                'document_type' => 'NOTA_FISCAL',
                'document_number' => 'NF-1020',
                'supplier_or_donor' => 'Fornecedor Teste Ltda',
                'issued_at' => now()->format('Y-m-d'),
                'total_amount' => 1500.00,
                'document_file' => $file,
                'items' => [
                    [
                        'material_id' => $this->material->id,
                        'quantity' => 5,
                    ]
                ]
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('entry_documents', [
            'document_number' => 'NF-1020',
        ]);

        $entryDoc = EntryDocument::where('document_number', 'NF-1020')->first();
        $this->assertNotNull($entryDoc->attachment);
        $this->assertEquals('Nota_Fiscal_1020.pdf', $entryDoc->attachment->original_name);

        Storage::disk('public')->assertExists($entryDoc->attachment->file_path);
    }

    public function test_entry_creation_fails_with_invalid_file_extension(): void
    {
        $file = UploadedFile::fake()->create('script_malicioso.exe', 100, 'application/octet-stream');

        $response = $this->actingAs($this->adminUser)
            ->post(route('entries.store'), [
                'document_type' => 'NOTA_FISCAL',
                'document_number' => 'NF-ERRADO',
                'supplier_or_donor' => 'Fornecedor Teste',
                'document_file' => $file,
                'items' => [
                    [
                        'material_id' => $this->material->id,
                        'quantity' => 1,
                    ]
                ]
            ]);

        $response->assertSessionHasErrors('document_file');
    }

    public function test_material_adjust_stock_can_have_image_attachment(): void
    {
        $image = UploadedFile::fake()->create('avaria_foto.jpg', 800, 'image/jpeg');

        $response = $this->actingAs($this->adminUser)
            ->post(route('materials.adjust-stock', $this->material), [
                'new_stock' => 5,
                'justification' => 'Baixa por avaria verificada na vistoria.',
                'attachment_file' => $image,
            ]);

        $response->assertRedirect(route('materials.index'));
        $this->assertEquals(5, $this->material->fresh()->current_stock);

        $attachment = Attachment::where('attachable_type', Material::class)
            ->where('attachable_id', $this->material->id)
            ->first();

        $this->assertNotNull($attachment);
        $this->assertEquals('avaria_foto.jpg', $attachment->original_name);
        Storage::disk('public')->assertExists($attachment->file_path);
    }

    public function test_attachment_download_returns_file_stream(): void
    {
        $file = UploadedFile::fake()->create('recibo_doacao.pdf', 200, 'application/pdf');

        $attachment = Attachment::create([
            'attachable_type' => Material::class,
            'attachable_id' => $this->material->id,
            'file_path' => $file->storeAs('attachments/test', 'test.pdf', 'public'),
            'original_name' => 'recibo_doacao.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 200,
            'uploaded_by' => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('attachments.download', $attachment));

        $response->assertOk();
    }

    public function test_attachment_deletion_by_admin(): void
    {
        $file = UploadedFile::fake()->create('documento_antigo.pdf', 300, 'application/pdf');
        $storedPath = $file->storeAs('attachments/test', 'old.pdf', 'public');

        $attachment = Attachment::create([
            'attachable_type' => Material::class,
            'attachable_id' => $this->material->id,
            'file_path' => $storedPath,
            'original_name' => 'documento_antigo.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 300,
            'uploaded_by' => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('attachments.destroy', $attachment));

        $response->assertOk();
        $this->assertDatabaseMissing('attachments', ['id' => $attachment->id]);
        Storage::disk('public')->assertMissing($storedPath);
    }
}
