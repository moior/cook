<?php namespace Cooka\Invoice\Models;

use Model;

/**
 * Order Model
 */
class Invoice extends Model
{
    public $table = 'cooka_invoices';

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
     * @var array Relations , 'public' => false
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'staff' => ['Backend\Models\User', 'key' => 'staff_id']
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [
        'upload_file' => ['System\Models\File']
    ];
    public $attachMany = [
    ];

}