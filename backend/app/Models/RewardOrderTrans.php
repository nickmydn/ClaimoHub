<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
            TransRewardModel::class
        );
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(
            MstReward::class
        );
    }

    public function voucherProduct(): BelongsTo
    {
        return $this->belongsTo(
            MstVoucherProduct::class
        );
    }
}
