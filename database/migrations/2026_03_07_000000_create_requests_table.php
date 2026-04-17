<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('crop_id')->constrained()->onDelete('cascade');
            $table->foreignId('variety_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->string('requester_name');
            $table->string('requester_email')->nullable();
            $table->string('requester_phone')->nullable();
            $table->text('purpose')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->date('request_date');
            $table->date('required_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
