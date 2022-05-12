<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class Post
{
    public function __construct(
        public ?string $title,
        public ?string $excerpt,
        public ?string $date,
        public ?string $body,
        public ?string $slug
    ){
    }

    public static function all(): Collection
    {
        return cache()->rememberForever("posts.all", function () {
            return collect(File::files(resource_path("posts")))
                ->map(fn($file) => YamlFrontMatter::parseFile($file))
                ->map(fn($document) => new Post(
                    $document->matter("title"),
                    $document->matter("excerpt"),
                    $document->matter("date"),
                    $document->body(),
                    $document->matter("slug"),
                ))
                ->sortByDesc('date');
        });
    }

    public static function find($slug): mixed
    {
        return static::all()->firstWhere('slug', $slug);
    }

}
