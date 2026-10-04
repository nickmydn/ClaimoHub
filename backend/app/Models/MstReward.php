<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MstReward extends Model
{
    use HasFactory;
    protected $table = 'mst_reward';

    protected $fillable = [
        'mr_voucher_product_id',
        'mr_code',
        'mr_name',
        'mr_min_transaction',
        'mr_quota',
        'mr_start_date',
        'mr_end_date',
        'is_active',
    ];

    protected $casts = [
        'mr_min_transaction' => 'decimal:2',
        'mr_start_date' => 'date',
        'mr_end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function mst_voucherPrd(): BelongsTo
    {
        return $this->belongsTo(MstVoucherProduct::class, "mr_voucher_product_id","id");
    }
}
