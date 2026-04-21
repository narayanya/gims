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
        Schema::create('lot_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_id')->constrained()->onDelete('cascade');
    
            // Source Location
            $table->foreignId('from_storage_id')->constrained('storages');
            $table->foreignId('from_section_id')->nullable()->constrained('sections');
            $table->foreignId('from_rack_id')->nullable()->constrained('racks');
            $table->foreignId('from_bin_id')->nullable()->constrained('bins');
            
            // Destination Location
            $table->foreignId('to_storage_id')->constrained('storages');
            $table->foreignId('to_section_id')->nullable()->constrained('sections');
            $table->foreignId('to_rack_id')->nullable()->constrained('racks');
            $table->foreignId('to_bin_id')->nullable()->constrained('bins');
            
            $table->decimal('quantity', 15, 2);
            $table->text('remarks')->nullable();
            $table->string('reference_no')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->foreignId('transferred_by')->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lot_transfers');
    }
};
