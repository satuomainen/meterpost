<?php

class Dataseries extends Eloquent {
    
    const TABLE_NAME = 'dataseries';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = Dataseries::TABLE_NAME;

    /**
     * The attributes visible in the model's JSON form.
     *
     * @var array
     */
    protected $visible = array('id', 'name', 'description', 'label');
}
