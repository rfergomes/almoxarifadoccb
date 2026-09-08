<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MaterialImageUploadTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        Storage::fake('public');

        $this->adminUser = User::where('email', 'admin@ccb.org.br')->first();
        $this->category = Category::create(['name' => 'Tintas e Solventes']);
    }

    /**
     * Cria um UploadedFile simulando imagem válida (PNG) sem depender da extensão GD.
     */
    protected function createFakeImage(string $filename = 'test.png', int $sizeKb = 10): UploadedFile
    {
        $pngBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
        $content = base64_decode($pngBase64);
        if ($sizeKb > 1) {
            $content .= str_repeat("\0", ($sizeKb - 1) * 1024);
        }

        return UploadedFile::fake()->createWithContent($filename, $content);
    }

    public function test_material_can_be_created_with_valid_image(): void
    {
        $image = $this->createFakeImage('tinta_branca.png', 50);

        $response = $this->actingAs($this->adminUser)
            ->post(route('materials.store'), [
                'name' => 'Tinta Acrílica Branca 18L',
                'category_id' => $this->category->id,
                'unit_measure' => 'LT',
                'current_stock' => 10,
                'minimum_stock' => 2,
                'is_returnable' => false,
                'status' => true,
                'image' => $image,
            ]);

        $response->assertRedirect(route('materials.index'));
        $response->assertSessionHas('success');

        $material = Material::where('name', 'Tinta Acrílica Branca 18L')->first();
        $this->assertNotNull($material);
        $this->assertNotNull($material->image_path);
        $this->assertTrue($material->hasImage());
        $this->assertNotNull($material->image_url);

        Storage::disk('public')->assertExists($material->image_path);
    }

    public function test_material_creation_rejects_non_image_files(): void
    {
        $pdfFile = UploadedFile::fake()->create('manual.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->adminUser)
            ->post(route('materials.store'), [
                'name' => 'Martelo Teste PDF',
                'category_id' => $this->category->id,
                'unit_measure' => 'UN',
                'current_stock' => 5,
                'minimum_stock' => 1,
                'image' => $pdfFile,
            ]);

        $response->assertSessionHasErrors(['image']);
        $this->assertDatabaseMissing('materials', ['name' => 'Martelo Teste PDF']);
    }

    public function test_material_creation_rejects_image_exceeding_max_size(): void
    {
        // 6000 KB > 5120 KB (5MB)
        $largeImage = $this->createFakeImage('foto_gigante.png', 6000);

        $response = $this->actingAs($this->adminUser)
            ->post(route('materials.store'), [
                'name' => 'Produto com Foto Grande',
                'category_id' => $this->category->id,
                'unit_measure' => 'UN',
                'current_stock' => 5,
                'minimum_stock' => 1,
                'image' => $largeImage,
            ]);

        $response->assertSessionHasErrors(['image']);
        $this->assertDatabaseMissing('materials', ['name' => 'Produto com Foto Grande']);
    }

    public function test_material_can_replace_existing_image_and_removes_old_file(): void
    {
        $oldImage = $this->createFakeImage('foto_antiga.png', 20);
        $newImage = $this->createFakeImage('foto_nova.png', 30);

        $material = Material::create([
            'code_sku' => 'CCB-901',
            'name' => 'Rolo de Pintura 23cm',
            'category_id' => $this->category->id,
            'unit_measure' => 'UN',
            'current_stock' => 15,
            'minimum_stock' => 3,
            'status' => true,
            'is_returnable' => false,
            'image_path' => $oldImage->store('materials/images', 'public'),
        ]);

        $oldPath = $material->image_path;
        Storage::disk('public')->assertExists($oldPath);

        $response = $this->actingAs($this->adminUser)
            ->put(route('materials.update', $material), [
                'code_sku' => 'CCB-901',
                'name' => 'Rolo de Pintura 23cm (Atualizado)',
                'category_id' => $this->category->id,
                'unit_measure' => 'UN',
                'minimum_stock' => 3,
                'status' => true,
                'is_returnable' => false,
                'image' => $newImage,
            ]);

        $response->assertRedirect(route('materials.index'));
        $material->refresh();

        $this->assertNotEquals($oldPath, $material->image_path);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($material->image_path);
    }

    public function test_material_can_remove_existing_image(): void
    {
        $image = $this->createFakeImage('foto_para_remover.png', 20);
        $storedPath = $image->store('materials/images', 'public');

        $material = Material::create([
            'code_sku' => 'CCB-902',
            'name' => 'Pincel 2 Polegadas',
            'category_id' => $this->category->id,
            'unit_measure' => 'UN',
            'current_stock' => 8,
            'minimum_stock' => 2,
            'status' => true,
            'is_returnable' => false,
            'image_path' => $storedPath,
        ]);

        Storage::disk('public')->assertExists($storedPath);

        $response = $this->actingAs($this->adminUser)
            ->put(route('materials.update', $material), [
                'code_sku' => 'CCB-902',
                'name' => 'Pincel 2 Polegadas',
                'category_id' => $this->category->id,
                'unit_measure' => 'UN',
                'minimum_stock' => 2,
                'status' => true,
                'is_returnable' => false,
                'remove_image' => '1',
            ]);

        $response->assertRedirect(route('materials.index'));
        $material->refresh();

        $this->assertNull($material->image_path);
        Storage::disk('public')->assertMissing($storedPath);
    }

    public function test_material_deletion_cleans_up_physical_image_file(): void
    {
        $image = $this->createFakeImage('material_deletar.png', 20);
        $storedPath = $image->store('materials/images', 'public');

        $material = Material::create([
            'code_sku' => 'CCB-903',
            'name' => 'Item Para Deletar Sem Movimento',
            'category_id' => $this->category->id,
            'unit_measure' => 'UN',
            'current_stock' => 0,
            'minimum_stock' => 0,
            'status' => true,
            'is_returnable' => false,
            'image_path' => $storedPath,
        ]);

        Storage::disk('public')->assertExists($storedPath);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('materials.destroy', $material));

        $response->assertRedirect(route('materials.index'));
        $this->assertDatabaseMissing('materials', ['id' => $material->id]);
        Storage::disk('public')->assertMissing($storedPath);
    }

    public function test_materials_index_displays_thumbnails_and_preview_modal(): void
    {
        $image = $this->createFakeImage('item_com_foto.png', 20);
        $storedPath = $image->store('materials/images', 'public');

        Material::create([
            'code_sku' => 'CCB-904',
            'name' => 'Item Visual Com Foto',
            'category_id' => $this->category->id,
            'unit_measure' => 'UN',
            'current_stock' => 10,
            'minimum_stock' => 2,
            'status' => true,
            'is_returnable' => false,
            'image_path' => $storedPath,
        ]);

        Material::create([
            'code_sku' => 'CCB-905',
            'name' => 'Item Visual Sem Foto',
            'category_id' => $this->category->id,
            'unit_measure' => 'UN',
            'current_stock' => 5,
            'minimum_stock' => 1,
            'status' => true,
            'is_returnable' => false,
            'image_path' => null,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('materials.index'));

        $response->assertOk();
        $response->assertSee('Item Visual Com Foto');
        $response->assertSee('Item Visual Sem Foto');
        $response->assertSee('modalMaterialImagePreview');
        $response->assertSee('btn-preview-material-image');
    }
}
