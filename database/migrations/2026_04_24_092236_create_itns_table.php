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
        Schema::create('itns', function (Blueprint $table) {
            $table->id();
            $table->string('itn_number')->unique();
            $table->foreignId('transfer_id')->constrained('warehouse_transfers')->cascadeOnDelete();
            $table->unsignedBigInteger('lot_id');
            $table->unsignedBigInteger('crop_id');
            $table->unsignedBigInteger('accession_id');

            $table->unsignedBigInteger('from_warehouse_id');
            $table->unsignedBigInteger('to_warehouse_id');

            $table->unsignedBigInteger('from_storage_id');
            $table->unsignedBigInteger('to_storage_id');
            $table->decimal('quantity', 10, 2)->nullable();
        
        $table->date('itn_date');

        $table->string('receiver');
        $table->string('mobile_number');
        $table->string('email')->nullable();

        $table->text('instructions')->nullable();
        $table->string('photo')->nullable();

        $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itns');
    }
};
