<?php

namespace Parallax\FilamentComments\Livewire;

use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View as ViewFactory;
use Livewire\Component;
use LogicException;
use Parallax\FilamentComments\Models\FilamentComment;

class CommentsComponent extends Component implements HasForms
{
    use InteractsWithForms;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public Model $record;

    public function mount(): void
    {
        $this->commentForm()->fill();
    }

    public function form(Schema $schema): Schema
    {
        if (! Gate::allows('create', $this->getCommentModelClass())) {
            return $schema;
        }

        if (config('filament-comments.editor') === 'markdown') {
            $editor = MarkdownEditor::make('comment')
                ->hiddenLabel()
                ->required()
                ->placeholder(__('filament-comments::filament-comments.comments.placeholder'))
                ->toolbarButtons(config('filament-comments.toolbar_buttons'));
        } else {
            $editor = RichEditor::make('comment')
                ->hiddenLabel()
                ->required()
                ->placeholder(__('filament-comments::filament-comments.comments.placeholder'))
                ->extraInputAttributes(['style' => 'min-height: 6rem'])
                ->toolbarButtons(config('filament-comments.toolbar_buttons'));
        }

        return $schema
            ->components([
                $editor,
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        if (! Gate::allows('create', $this->getCommentModelClass())) {
            return;
        }

        $this->commentForm()->validate();

        $data = $this->commentForm()->getState();

        $this->filamentComments()->create([
            'subject_type' => $this->record->getMorphClass(),
            'comment' => $data['comment'],
            'user_id' => auth()->id(),
        ]);

        Notification::make()
            ->title(__('filament-comments::filament-comments.notifications.created'))
            ->success()
            ->send();

        $this->commentForm()->fill();
    }

    public function delete(int $id): void
    {
        $comment = FilamentComment::find($id);

        if (! $comment) {
            return;
        }

        if (! Gate::allows('delete', $comment)) {
            return;
        }

        $comment->delete();

        Notification::make()
            ->title(__('filament-comments::filament-comments.notifications.deleted'))
            ->success()
            ->send();
    }

    public function render(): View
    {
        $comments = $this->filamentComments()->with(['user'])->latest()->get();

        return ViewFactory::make('filament-comments::comments', ['comments' => $comments]);
    }

    protected function commentForm(): Schema
    {
        return $this->getSchema('form') ?? throw new LogicException('The comments form schema is not available.');
    }

    /**
     * @return HasMany<FilamentComment, Model>
     */
    protected function filamentComments(): HasMany
    {
        return $this->record
            ->hasMany($this->getCommentModelClass(), 'subject_id')
            ->where('subject_type', $this->record->getMorphClass())
            ->latest();
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
