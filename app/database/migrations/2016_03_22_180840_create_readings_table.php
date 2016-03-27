<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReadingsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create(Reading::TABLE_NAME, function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('created_at')->default(DB::raw('current_timestamp')); // Works with MySQL, not tested with PostgreSQL
            $table->timestamp('updated_at')->default(DB::raw('current_timestamp'));
            $table->string('value', 255);
            $dataSeriesForeignKeyColumnName = Dataseries::TABLE_NAME . '_id';
            $table->integer($dataSeriesForeignKeyColumnName)->unsigned();
            $table->foreign($dataSeriesForeignKeyColumnName)->references('id')->on(Dataseries::TABLE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop(Reading::TABLE_NAME);
    }
}
