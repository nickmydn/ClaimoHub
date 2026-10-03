<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TransRewardModel extends Model
{
    use HasFactory;

    protected $table = 'trx_reward_claim';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';
    protected $fillable = [
        'trc_user_id',
        'trc_merchant_id',
        'trc_trx_no',
        'trc_amount',
        'trc_status',
        'trc_trx_date',
    ];

    protected $casts = [
        'trc_amount' => 'decimal:2',
        'trc_trx_date' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(MstMerchant::class);
    }

    public function reward(): HasOne
    {
        return $this->hasOne(RewardOrderTrans::class);
    }
}
