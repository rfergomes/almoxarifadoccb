<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entry_documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_number', 50);
            $table->string('document_type', 30); // Enum DocumentType
            $table->string('supplier_or_donor', 150);
            $table->decimal('total_amount', 12, 2)->nullable();
            $table->date('issued_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('movements', function (Blueprint $table) {
            $table->foreignId('entry_document_id')->nullable()->after('destination_id')->constrained('entry_documents')->onDelete('cascade');
            $table->foreignId('beneficiary_id')->nullable()->change();
            $table->foreignId('destination_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('movements', function (Blueprint $table) {
            $table->dropForeign(['entry_document_id']);
            $table->dropColumn('entry_document_id');
        });

        Schema::dropIfExists('entry_documents');
    }
};
