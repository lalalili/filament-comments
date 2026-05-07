<?php

namespace Parallax\FilamentComments\Infolists\Components;

use Filament\Infolists\Components\Entry;
use Illuminate\Support\Facades\Gate;
use Parallax\FilamentComments\Models\FilamentComment;

class CommentsEntry extends Entry
{
    protected string $view = 'filament-comments::component';

    protected function setUp(): void
    {
        parent::setUp();

        $this->visible(fn (): bool => Gate::allows('viewAny', $this->getCommentModelClass()));
    }

    /**
     * @return class-string<FilamentComment>
     */
    protected function getCommentModelClass(): string
    {
        $model = config('filament-comments.comment_model', FilamentComment::class);

        if (is_string($model) && is_a($model, FilamentComment::class, true)) {
            return $model;
        }

        return FilamentComment::class;
    }
}
