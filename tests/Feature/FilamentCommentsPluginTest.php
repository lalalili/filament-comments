<?php

use Parallax\FilamentComments\FilamentCommentsPlugin;
use Parallax\FilamentComments\FilamentCommentsServiceProvider;
use Parallax\FilamentComments\Models\FilamentComment;

it('can instantiate the plugin', function () {
    $plugin = FilamentCommentsPlugin::make();

    expect($plugin)->toBeInstanceOf(FilamentCommentsPlugin::class)
        ->and($plugin->getId())->toBe('filament-comments');
});

it('loads package configuration', function () {
    expect(config('filament-comments.comment_model'))->toBe(FilamentComment::class)
        ->and(FilamentCommentsServiceProvider::$name)->toBe('filament-comments');
});
