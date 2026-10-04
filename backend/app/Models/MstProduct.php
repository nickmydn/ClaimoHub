<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MstProduct extends Model
{
    use HasFactory;

    protected $table = 'mst_product';
    protected $fillable = [
        'mp_merchant_id',
        'mp_code',
        'mp_name',
        'mp_price',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'mp_price' => 'decimal:2'
    ];

    public function mst_merchant(): BelongsTo{
        return $this->belongsTo(MstMerchant::class, "mp_merchant_id", "id");
    }
}
