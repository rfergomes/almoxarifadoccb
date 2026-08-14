<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->date('expiration_date')->nullable()->after('ca_validity');
            $table->string('patrimony_code', 50)->nullable()->unique()->after('expiration_date');
            $table->index('expiration_date');
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropIndex(['expiration_date']);
            $table->dropUnique(['patrimony_code']);
            $table->dropColumn(['expiration_date', 'patrimony_code']);
        });
    }
};
