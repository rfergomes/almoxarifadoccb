<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Material;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Remaneja e remove duplicatas antigas se existirem
        foreach ([8 => 1, 9 => 3, 12 => 2] as $dupId => $targetId) {
            $dup = Category::find($dupId);
            if ($dup) {
                Material::where('category_id', $dupId)->update(['category_id' => $targetId]);
                $dup->delete();
            }
        }

        // 2. Atualiza as categorias originais para os nomes completos
        $c1 = Category::find(1);
        if ($c1) {
            $c1->update(['name' => 'EPI (Proteção Individual)', 'description' => 'Equipamentos de Proteção Individual']);
        }

        $c2 = Category::find(2);
        if ($c2) {
            $c2->update(['name' => 'Consumo Geral & Limpeza', 'description' => 'Estopas, fitas adesivas, lixas e suprimentos diversos']);
        }

        $c3 = Category::find(3);
        if ($c3) {
            $c3->update(['name' => 'Ferramentas & Equipamentos', 'description' => 'Ferramentas e máquinas retornáveis']);
        }

        // 3. Garante a inserção de todas as categorias oficiais
        $categories = [
            'Construção Civil' => 'Cimentos, areia, tijolos, argamassas e agregados',
            'Elétrica' => 'Fios, cabos, disjuntores, conduítes e iluminação',
            'Pintura & Insumos' => 'Tintas, massa corrida, grafiato, seladores e rolos',
            'Hidráulica & Encanamento' => 'Tubos PVC, conexões, registros e caixas d\'água',
            'EPI (Proteção Individual)' => 'Equipamentos de Proteção Individual',
            'Ferramentas & Equipamentos' => 'Ferramentas e máquinas retornáveis',
            'Ferragens & Serralheria' => 'Parafusos, pregos, dobradiças, fechaduras e eletrodos',
            'Marcenaria & Madeira' => 'Tábuas, sarrafos, vigas e compensados',
            'Consumo Geral & Limpeza' => 'Estopas, fitas adesivas, lixas e suprimentos diversos',
        ];

        foreach ($categories as $name => $description) {
            Category::firstOrCreate(['name' => $name], ['description' => $description]);
        }
    }
}
