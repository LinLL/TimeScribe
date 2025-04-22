<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AppCategoryEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ActivityHistory extends Model
{
    protected $appends = [
        'color',
        'category_color',
    ];

    protected $fillable = [
        'app_name',
        'app_identifier',
        'app_icon',
        'app_category',
        'started_at',
        'ended_at',
        'duration',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'duration' => 'integer',
        'app_category' => AppCategoryEnum::class,
    ];

    public function scopeActive($query)
    {
        return $query->where('ended_at', '>', Carbon::now()->subSeconds(7));
    }

    public function getColorAttribute(): string
    {
        mt_srand(crc32($this->app_identifier));

        return sprintf('#00b9cd%02x', mt_rand(50, 255));
    }

    public function getCategoryColorAttribute(): string
    {
        mt_srand(crc32($this->app_category?->value ?? 'Unknow'));

        return sprintf('#fb64b6%02x', mt_rand(100, 255));
    }
}
