<?php

namespace Parallax\FilamentComments\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;

/**
 * @property int $user_id
 */
class FilamentComment extends Model
{
    use MassPrunable;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'subject_type',
        'subject_id',
        'comment',
    ];

    public function __construct(array $attributes = [])
    {
        $config = Config::get('filament-comments');

        if (isset($config['table_name'])) {
            $this->setTable($config['table_name']);
        }

        parent::__construct($attributes);
    }

    /**
     * @return BelongsTo<Model, $this>
     */
    public function user(): BelongsTo
    {
        $authenticatable = config('filament-comments.authenticatable');

        if (! is_string($authenticatable) || ! is_a($authenticatable, Model::class, true)) {
            $authenticatable = Model::class;
        }

        return $this->belongsTo($authenticatable, 'user_id');
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return Builder<self>
     */
    public function prunable(): Builder
    {
        $days = config('filament-comments.prune_after_days');

        if (! is_numeric($days)) {
            return self::query()->whereRaw('1 = 0');
        }

        return self::onlyTrashed()->where('created_at', '<=', now()->subDays((int) $days));
    }
}
