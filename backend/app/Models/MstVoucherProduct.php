<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MstVoucherProduct extends Model
{
    use HasFactory;
    protected $table = 'mst_voucher_product';
    protected $fillable = [
        'mvp_provider',
        'mvp_code',
        'mvp_name',
        'mvp_denomination',
        'mvp_stock',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'mvp_denomination' => 'decimal:2'
    ];

    public function mst_rewards(): HasMany{
        return $this->hasMany(MstReward::class,'mr_voucher_product_id','id');
    }
}
