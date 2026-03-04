<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');

            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('customer_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('type');
            $table->date('invoice_date')->default(now());
            $table->date('due_date');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount_total', 12, 2);
            $table->string('discount_type', 2)->default('%');
            ;
            $table->decimal('tax_total', 5, 2);
            $table->string('tax_type', 2)->default('%');
            ;
            $table->decimal('total', 12, 2);
            $table->string('comments')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
