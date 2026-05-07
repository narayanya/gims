<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('request_transactions', function (Blueprint $table) {
            $table->id();
            // Relations
            $table->foreignId('request_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignId('dispatch_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignId('accession_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignId('lot_id')->nullable()->constrained()->nullOnDelete();

            // User tracking
            $table->foreignId('created_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Transaction type
            $table->enum('transaction_type', [
                'request',
                'approve',
                'reject',
                'dispatch',
                'return',
                'cancel',
                'stock_update'
            ]);

            // Quantity movement
            $table->decimal('quantity', 12, 2)->default(0);

            // Before stock
            $table->decimal('old_quantity', 12, 2)->default(0);

            $table->decimal('old_quantity_show', 12, 2)->default(0);

            // After stock
            $table->decimal('new_quantity', 12, 2)->default(0);

            $table->decimal('new_quantity_show', 12, 2)->default(0);

            // Reference details
            $table->string('reference_no')->nullable();

            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_transactions');
    }
};
