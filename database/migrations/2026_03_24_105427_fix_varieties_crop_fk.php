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
        // Drop FK only if it exists
        $fks = collect(\DB::select("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'varieties' 
            AND CONSTRAINT_TYPE = 'FOREIGN KEY' AND CONSTRAINT_NAME = 'varieties_crop_id_foreign'"));
        
        Schema::table('varieties', function (Blueprint $table) use ($fks) {
            if ($fks->isNotEmpty()) {
                $table->dropForeign('varieties_crop_id_foreign');
            }
            $table->unsignedBigInteger('crop_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('varieties', function (Blueprint $table) {

            $table->dropForeign(['crop_id']);

            $table->foreign('crop_id')
                ->references('id')
                ->on('crops');
        });
    }
};
