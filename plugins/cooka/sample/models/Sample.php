<?php namespace Cooka\Sample\Models;

use Model;

/**
 * Order Model
 */
class Sample extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cooka_samples';

    //protected $jsonable = ['cook_data', 'bill'];

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [
        'sample_images' => ['System\Models\File', 'order' => 'sort_order', 'delete' => 'true' ]
    ];

}