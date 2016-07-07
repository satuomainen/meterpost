<?php

/**
 * Class DataseriesSummary
 *
 * This is a denormalization of the readings for the dashboard. When the number of data series
 * grows, loading the dashboard starts to take a lot of time. With five data series and a year
 * of data caused the dashboard to take up to 2000-2500 ms to load. By calculating the needed
 * summary data and keeping it up to date speeds up the loading remarkably.
 */
class DataseriesSummary extends Eloquent {

    const TABLE_NAME = 'dataseries_summaries';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = DataseriesSummary::TABLE_NAME;

    /**
     * The attributes visible in the model's JSON form.
     *
     * @var array
     */
    protected $visible = array('dataseries_id', 'current_value', 'min_value', 'max_value');
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = array('dataseries_id', 'current_value', 'min_value', 'max_value');
}
