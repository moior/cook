<?php namespace Boonzero\Sms\Models;

use Model;

/**
 * Order Model
 */
class History extends Model
{
    public $table = 'boon_sms_history';

    //protected $jsonable = ['cook_data'];

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations , 'public' => false
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
        'upload_files' => ['System\Models\File', 'order' => 'sort_order', 'delete' => 'true' ]
    ];
}