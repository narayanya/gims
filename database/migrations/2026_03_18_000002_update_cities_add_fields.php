<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->renameColumn('name', 'sName');
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->string('sCode')->nullable()->after('sName');
            $table->string('bGroup')->nullable()->after('sCode');
            $table->unsignedBigInteger('iParentId')->nullable()->after('bGroup');
            $table->unsignedBigInteger('iState')->nullable()->after('iParentId');
            $table->tinyInteger('blsMetroCity')->default(0)->after('iState');
        });
    }

    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn(['sCode', 'bGroup', 'iParentId', 'iState', 'blsMetroCity']);
        });
        Schema::table('cities', function (Blueprint $table) {
            $table->renameColumn('sName', 'name');
        });
    }
};
