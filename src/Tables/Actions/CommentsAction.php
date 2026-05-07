<?php

namespace Parallax\FilamentComments\Tables\Actions;

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
            ->icon(config('filament-comments.icons.action'))
            ->label(__('filament-comments::filament-comments.comments'))
            ->slideOver()
            ->modalContentFooter(fn (Model $record): View => ViewFactory::make('filament-comments::component', [
                'record' => $record,
            ]))
            ->modalHeading(__('filament-comments::filament-comments.modal.heading'))
            ->modalWidth(Width::Medium)
            ->modalSubmitAction(false)
            ->modalCancelAction(false)
            ->visible(fn (): bool => Gate::allows('viewAny', $this->getCommentModelClass()));
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
