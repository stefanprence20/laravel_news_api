<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $user_id
 * @property int $preferable_id
 * @property string $preferable_type
 * @property string $created_at
 * @property string $updated_at
 * @property int $id
 */
class Preferable extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'preferable_id',
        'preferable_type'
    ];

    public function preferables(): MorphTo
    {
        return $this->morphTo();
    }
}
