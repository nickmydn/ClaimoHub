<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MstMerchant extends Model
{
    use HasFactory;

    protected $table = 'mst_merchant';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'mm_distributor_id',
        'mm_code',
        'mm_name',
        'mm_email',
        'mm_phone',
        'mm_address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function mst_distributor(): BelongsTo{
        return $this->belongsTo(MstDistributor::class,'mm_distributor_id','id');
    }

    public function mst_products(): HasMany{
        return $this->hasMany(MstProduct::class,'mp_merchant_id','id');
    }
}
