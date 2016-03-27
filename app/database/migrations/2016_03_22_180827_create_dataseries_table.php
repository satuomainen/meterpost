<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDataseriesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create(Dataseries::TABLE_NAME, function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('created_at')->default(DB::raw('current_timestamp')); // Works with MySQL, not tested with PostgreSQL
            $table->timestamp('updated_at')->default(DB::raw('current_timestamp'));
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('label', 255)->nullable();
            $table->string('api_key', 255);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop(Dataseries::TABLE_NAME);
    }
}
