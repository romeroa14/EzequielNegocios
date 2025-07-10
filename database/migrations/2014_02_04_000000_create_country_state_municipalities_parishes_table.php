<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountryStateMunicipalitiesParishesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',255);
            $table->string('iso_3366_1',255);
            $table->timestamps();
        });

        Schema::create('states', function (Blueprint $table) {
            $table->increments('id')->comment("Clave primaria de la tablaa");
            $table->unsignedSmallInteger('country_id')
                ->comment('ID del pais al que pertenece el estado');
            $table->foreign('country_id')->references('id')->on('countries')->cascadeOnUpdate()->restrictOnDelete()
                ->comment('Relacion con la tabla de paises');
            $table->string('name')->comment('Nombre del estado');
            $table->string('iso_3366_2', 10)->comment('Codigo ISO para principales subdivisiones de los paises');
            $table->string('category')->nullable()->comment('Nombre de la categoria');
            $table->string('zoom')->nullable()->comment('zoom del estado');
            $table->string('region')->nullable()->comment('Nombre de la region');
            $table->string('latitude_center')->nullable()->comment('Latitud para centrar el mapa con respecto al estado');
            $table->string('longitude_center')->nullable()->comment('Longitud para centrar el mapa con respecto al estado');
            $table->timestamps();
            $table->comment('Estados, Provincias o Departamentos');
        });

        Schema::create('municipalities', function (Blueprint $table) {
            $table->increments('id')->comment("Clave primaria de la tablaa");
            $table->unsignedSmallInteger('state_id')
                ->comment('ID del estado al que pertenece el municipio');
            $table->foreign('state_id')->references('id')->on('states')->cascadeOnUpdate()->restrictOnDelete()
                ->comment('Relacion con la tabla de estados');
            $table->string('name')->comment('Nombre del municipio');
            $table->string('id_municipio')->comment('ID del municipio');
            $table->timestamps();
            $table->comment('Municipios');
        });

        Schema::create('parishes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('municipality_id');
            $table->string('name',255);
            $table->string('id_municipio',255);

            
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('municipality_id')->references('id')->on('municipalities')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('municipalities');
        Schema::dropIfExists('states');
        Schema::dropIfExists('countries');
    }
}
