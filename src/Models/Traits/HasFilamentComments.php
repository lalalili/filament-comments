<?php

namespace Parallax\FilamentComments\Models\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Parallax\FilamentComments\Models\FilamentComment;

trait HasFilamentComments
{
    /**
     * @return HasMany<FilamentComment, $this>
     */
    public function filamentComments(): HasMany
    {
        $commentModel = config('filament-comments.comment_model', FilamentComment::class);

        if (! is_string($commentModel) || ! is_a($commentModel, FilamentComment::class, true)) {
            $commentModel = FilamentComment::class;
        }

        return $this
            ->hasMany($commentModel, 'subject_id')
            ->where('subject_type', $this->getMorphClass())
            ->latest();
    }
}
