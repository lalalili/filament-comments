<?php

namespace Parallax\FilamentComments\Actions;

use Filament\Actions\Action;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View as ViewFactory;
use Parallax\FilamentComments\Models\FilamentComment;

class CommentsAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'comments';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->hiddenLabel()
            ->icon(config('filament-comments.icons.action'))
            ->color('gray')
            ->badge(fn (): ?int => $this->getCommentsBadgeCount())
            ->slideOver()
            ->modalContentFooter(fn (): View => ViewFactory::make('filament-comments::component'))
            ->modalHeading(__('filament-comments::filament-comments.modal.heading'))
            ->modalWidth(Width::Medium)
            ->modalSubmitAction(false)
            ->modalCancelAction(false)
            ->visible(fn (): bool => Gate::allows('viewAny', $this->getCommentModelClass()));
    }

    protected function getCommentsBadgeCount(): ?int
    {
        $record = $this->getRecord();

        if (! $record instanceof Model) {
            return null;
        }

        return $record
            ->hasMany($this->getCommentModelClass(), 'subject_id')
            ->where('subject_type', $record->getMorphClass())
            ->count();
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
