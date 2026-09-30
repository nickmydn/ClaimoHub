<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MstDistributor extends Model
{
    use HasFactory;
    protected $table = 'mst_distributor';

    protected $fillable = [
        'md_code',
        'md_name',
        'md_email',
        'md_phone',
        'md_address',
        'is_active',
    ];

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function mst_merchants(): HasMany{
        return $this->hasMany(MstMerchant::class,'mm_distributor_id','id');
    }
}
