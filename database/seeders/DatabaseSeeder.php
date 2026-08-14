<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ItemStatus;
use App\Enums\MovementStatus;
use App\Enums\MovementType;
use App\Models\Beneficiary;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Material;
use App\Models\Movement;
use App\Models\MovementItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        // Categorias Padrão de Almoxarifado CCB
        $catConstrucao = Category::firstOrCreate(['name' => 'Construção Civil'], ['description' => 'Cimentos, areia, tijolos, argamassas e agregados']);
        $catEletrica = Category::firstOrCreate(['name' => 'Elétrica'], ['description' => 'Fios, cabos, disjuntores, conduítes e iluminação']);
        $catPintura = Category::firstOrCreate(['name' => 'Pintura & Insumos'], ['description' => 'Tintas, massa corrida, grafiato, seladores e rolos']);
        $catHidraulica = Category::firstOrCreate(['name' => 'Hidráulica & Encanamento'], ['description' => 'Tubos PVC, conexões, registros e caixas d\'água']);
        $catEpi = Category::firstOrCreate(['name' => 'EPI (Proteção Individual)'], ['description' => 'Equipamentos de Proteção Individual']);
        $catFerramentas = Category::firstOrCreate(['name' => 'Ferramentas & Equipamentos'], ['description' => 'Ferramentas e máquinas retornáveis']);
        $catFerragens = Category::firstOrCreate(['name' => 'Ferragens & Serralheria'], ['description' => 'Parafusos, pregos, dobradiças, fechaduras e eletrodos']);
        $catMarcenaria = Category::firstOrCreate(['name' => 'Marcenaria & Madeira'], ['description' => 'Tábuas, sarrafos, vigas e compensados']);
        $catConsumo = Category::firstOrCreate(['name' => 'Consumo Geral & Limpeza'], ['description' => 'Estopas, fitas adesivas, lixas e suprimentos diversos']);

        // Destinos Padrão (Casas de Oração e Obras)
        $dest1 = Destination::firstOrCreate(
            ['code' => 'CO-001'],
            ['name' => 'Brás Central - Brás', 'type' => 'casa_de_oracao', 'city' => 'São Paulo', 'address' => 'Rua Visconde de Parnaíba, 1616']
        );
        $dest2 = Destination::firstOrCreate(
            ['code' => 'CO-002'],
            ['name' => 'Jardim das Flores', 'type' => 'casa_de_oracao', 'city' => 'Osasco', 'address' => 'Av. das Flores, 500']
        );
        $dest3 = Destination::firstOrCreate(
            ['code' => 'OB-101'],
            ['name' => 'Obra Nova C.O. Vila Nova', 'type' => 'obra', 'city' => 'Guarulhos', 'address' => 'Rua Principal, 100']
        );

        // Beneficiários Padrão (Trabalhadores e Voluntários)
        $ben1 = Beneficiary::firstOrCreate(
            ['document_cpf' => '111.222.333-44'],
            ['name' => 'João Silva', 'phone' => '(11) 98888-1111', 'role_in_ccb' => 'Voluntário']
        );
        $ben2 = Beneficiary::firstOrCreate(
            ['document_cpf' => '555.666.777-88'],
            ['name' => 'Pedro Oliveira', 'phone' => '(11) 97777-2222', 'role_in_ccb' => 'Pedreiro / Construtor']
        );
        $ben3 = Beneficiary::firstOrCreate(
            ['document_cpf' => '999.888.777-66'],
            ['name' => 'Mateus Santos', 'phone' => '(11) 96666-3333', 'role_in_ccb' => 'Oficial de Manutenção']
        );

        // Materiais Padrão
        $matCimento = Material::firstOrCreate(
            ['code_sku' => 'MAT-CON-001'],
            ['name' => 'Cimento CP II 50kg', 'category_id' => $catConstrucao->id, 'unit_measure' => 'CX', 'current_stock' => 50, 'minimum_stock' => 10, 'is_returnable' => false]
        );
        $matTinta = Material::firstOrCreate(
            ['code_sku' => 'MAT-CON-002'],
            ['name' => 'Tinta Acrílica Branca 18L', 'category_id' => $catPintura->id, 'unit_measure' => 'CX', 'current_stock' => 8, 'minimum_stock' => 10, 'is_returnable' => false]
        );
        $matCapacete = Material::firstOrCreate(
            ['code_sku' => 'EPI-001'],
            ['name' => 'Capacete de Segurança Branco com Jugular', 'category_id' => $catEpi->id, 'unit_measure' => 'UN', 'current_stock' => 20, 'minimum_stock' => 5, 'ca_number' => 'CA-31415', 'ca_validity' => now()->addYear(), 'is_returnable' => false]
        );
        $matFuradeira = Material::firstOrCreate(
            ['code_sku' => 'EQP-001'],
            ['name' => 'Furadeira de Impacto Industrial Bosch', 'category_id' => $catFerramentas->id, 'unit_measure' => 'UN', 'current_stock' => 4, 'minimum_stock' => 1, 'is_returnable' => true]
        );
        $matEscada = Material::firstOrCreate(
            ['code_sku' => 'EQP-002'],
            ['name' => 'Escada Extensível de Alumínio 7m', 'category_id' => $catFerramentas->id, 'unit_measure' => 'UN', 'current_stock' => 2, 'minimum_stock' => 1, 'is_returnable' => true]
        );

        $almoxarife = User::where('email', 'almoxarife@ccb.org.br')->first();

        // 1. Movimentação de Consumo
        $mov1 = Movement::firstOrCreate(
            ['code' => 'MOV-20260811-0001'],
            [
                'user_id' => $almoxarife->id,
                'beneficiary_id' => $ben1->id,
                'destination_id' => $dest2->id,
                'type' => MovementType::CONSUMPTION,
                'status' => MovementStatus::COMPLETED,
                'notes' => 'Saída de cimento para reforma do sanitário.',
            ]
        );
        MovementItem::firstOrCreate(
            ['movement_id' => $mov1->id, 'material_id' => $matCimento->id],
            ['quantity' => 5, 'returned_quantity' => 0, 'status' => ItemStatus::DELIVERED]
        );

        // 2. Movimentação de Empréstimo em Atraso para testes
        $mov2 = Movement::firstOrCreate(
            ['code' => 'MOV-20260801-0002'],
            [
                'user_id' => $almoxarife->id,
                'beneficiary_id' => $ben2->id,
                'destination_id' => $dest3->id,
                'type' => MovementType::LOAN,
                'status' => MovementStatus::OVERDUE,
                'notes' => 'Empréstimo de furadeira para a estrutura da obra.',
            ]
        );
        MovementItem::firstOrCreate(
            ['movement_id' => $mov2->id, 'material_id' => $matFuradeira->id],
            [
                'quantity' => 1,
                'returned_quantity' => 0,
                'expected_return_date' => now()->subDays(3),
                'status' => ItemStatus::PENDING_RETURN,
            ]
        );
    }
}
