<?php namespace Cooka\Order\Models;

use Model;

/**
 * Order Model
 */
class Order extends Model
{
    public $table = 'cooka_orders';

    protected $jsonable = ['cook_data'];

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
    public $hasMany = [
        'memos' => ['Kenny\Memo\Models\Memo', 'key' => 'attach_id'],
        'invoices' => ['Cooka\Invoice\Models', 'key' => 'order_id']
    ];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [
        'upload_file' => ['System\Models\File']
    ];
    public $attachMany = [
        'upload_files' => ['System\Models\File', 'order' => 'sort_order', 'delete' => 'true' ]
    ];
    public function latestMemo()
    {
        return $this->hasOne('Memo')->latest();
    }
}