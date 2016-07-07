<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataseriesSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(DataseriesSummary::TABLE_NAME, function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('created_at')->default(DB::raw('current_timestamp')); // Works with MySQL, not tested with PostgreSQL
            $table->timestamp('updated_at')->default(DB::raw('current_timestamp'));
            $table->string('current_value', 255);
            $table->string('min_value', 255);
            $table->string('max_value', 255);
            $dataSeriesForeignKeyColumnName = Dataseries::TABLE_NAME . '_id';
            $table->integer($dataSeriesForeignKeyColumnName)->unsigned();
            $table->foreign($dataSeriesForeignKeyColumnName)->references('id')->on(Dataseries::TABLE_NAME);
            $table->unique($dataSeriesForeignKeyColumnName);
        });

        $summaryRows = DataseriesService::getSummaryRows();
        foreach ($summaryRows as $summaryRow) {
            DataseriesSummary::create($summaryRow);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop(DataseriesSummary::TABLE_NAME);
    }

}
