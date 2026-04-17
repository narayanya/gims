<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToVarietiesTable extends Migration
{
    public function up()
    {
        Schema::table('varieties', function (Blueprint $table) {

            // Basic
            $table->string('variety_type')->nullable();
            $table->string('breeder_name')->nullable();
            $table->year('release_year')->nullable();
            $table->string('release_authority')->nullable();

            // Identification
            $table->string('source')->nullable();
            $table->string('accession_number')->nullable();
            $table->string('country_origin')->nullable();
            $table->string('state_origin')->nullable();

            // Agronomy
            $table->integer('maturity_days')->nullable();
            $table->string('plant_height')->nullable();
            $table->string('grain_type')->nullable();
            $table->string('seed_color')->nullable();
            $table->decimal('yield_potential',8,2)->nullable();

            // Quality
            $table->decimal('germination',5,2)->nullable();
            $table->decimal('purity',5,2)->nullable();
            $table->decimal('moisture',5,2)->nullable();
            $table->decimal('test_weight',6,2)->nullable();

            // Resistance
            $table->string('disease_resistance')->nullable();
            $table->string('pest_resistance')->nullable();
            $table->string('drought_tolerance')->nullable();
            $table->string('flood_tolerance')->nullable();
            $table->string('salinity_tolerance')->nullable();

            // Seed production
            $table->string('isolation_distance')->nullable();
            $table->string('seed_class')->nullable();
            $table->string('production_region')->nullable();
            $table->integer('storage_life')->nullable();

            // Admin
            $table->string('status')->default('Active');
            $table->text('remarks')->nullable();

        });
    }

    public function down()
    {
        Schema::table('varieties', function (Blueprint $table) {
            $table->dropColumn([
                'variety_type','breeder_name','release_year','release_authority',
                'source','accession_number','country_origin','state_origin',
                'maturity_days','plant_height','grain_type','seed_color','yield_potential',
                'germination','purity','moisture','test_weight',
                'disease_resistance','pest_resistance','drought_tolerance',
                'flood_tolerance','salinity_tolerance',
                'isolation_distance','seed_class','production_region','storage_life',
                'status','remarks'
            ]);
        });
    }
}
