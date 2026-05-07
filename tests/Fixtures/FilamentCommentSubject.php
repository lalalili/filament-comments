<?php

namespace Parallax\FilamentComments\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Parallax\FilamentComments\Models\Traits\HasFilamentComments;

class FilamentCommentSubject extends Model
{
    use HasFilamentComments;

    protected $table = 'filament_comment_subjects';
}
