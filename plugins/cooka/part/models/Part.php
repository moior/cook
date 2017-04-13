<?php namespace Cooka\Part\Models;

use Model;

/**
 * Order Model
 */
class Part extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'cooka_parts';

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
    public $belongsTo = [
        /*'team' => ['']*/
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [
        'part_images' => ['System\Models\File', 'order' => 'sort_order', 'delete' => 'true' ]
    ];

}