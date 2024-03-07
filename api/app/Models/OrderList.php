<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderList extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'order_list';
    protected $fillable = [
        'id',
        'code',
        'customer_id',
        'quantity',
        'total_amount',
        'status',
        'date_created',
        'date_updated',
        'product_name',
        'order_token',
        'order_numbers',
        'product_id',
        'payment_method',
        'order_expiration',
        'pix_code',
        'pix_qrcode',
        'id_mp',
        'txid',
        'discount_amount',
        'whatsapp_status',
        'dwapi_status',
        'referral_id',
        'split_account'
    ];
    public $timestamps = false;
}
