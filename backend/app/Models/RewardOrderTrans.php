<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RewardOrderTrans extends Model
{
    protected $fillable = [
        'transaction_id',
        'campaign_id',
        'voucher_product_id',
        'reward_amount',
        'status',
        'awarded_at',
    ];

    protected $casts = [
        'reward_amount' => 'decimal:2',
        'awarded_at' => 'datetime',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(
            TransRewardModel::class, "transaction_id", "id"
        );
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(
            MstReward::class, "campaign_id", "id"
        );
    }

    public function voucherProduct(): BelongsTo
    {
        return $this->belongsTo(
            MstVoucherProduct::class, "voucher_product_id","id"
        );
    }

    public function voucherRedemption(): HasOne
    {
        return $this->hasOne(
            VoucherRedemption::class,
            'tvr_reward_order_trans_id',"id"
        );
    }
}
