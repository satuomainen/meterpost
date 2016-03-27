<?php

class Reading extends Eloquent {
    
    const TABLE_NAME = 'readings';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = Reading::TABLE_NAME;

    /**
     * The attributes visible in the model's JSON form.
     *
     * @var array
     */
    protected $visible = array('created_at', 'value');
}
