<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 */
class Source extends Model
{
    use HasFactory;

    const THE_GUARDIAN_SOURCE_NAME = 'The Guardian';
    const NYTIMES_SOURCE_NAME = 'New York Times';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
