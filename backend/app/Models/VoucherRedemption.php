<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoucherRedemption extends Model
{
    use HasFactory;

    protected $table = 'trx_voucher_redemption';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'tvr_reward_order_trans_id',
        'tvr_user_id',
        'tvr_voucher_product_id',
        'tvr_redemption_no',
        'tvr_external_reference',
        'tvr_voucher_code',
        'tvr_amount',
        'tvr_status',
        'tvr_failure_reason',
        'tvr_redeemed_at',
    ];

    protected $casts = [
        'tvr_amount' => 'decimal:2',
        'tvr_redeemed_at' => 'datetime',
    ];

    public function rewardOrderTrans(): BelongsTo
    {
        return $this->belongsTo(
            RewardOrderTrans::class,
            'tvr_reward_order_trans_id',"id"
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'tvr_user_id',"id"
        );
    }

    public function voucherProduct(): BelongsTo
    {
        return $this->belongsTo(
            MstVoucherProduct::class,
            'tvr_voucher_product_id',"id"
        );
    }
}
