<?php

namespace App\Modules\CMS\Services;

use SimpleXMLElement;
use Throwable;

class WordPressXmlParser
{
    /**
     * @return array<string, mixed>
     */
    public function parseFile(string $path): array
    {
        if (! is_file($path)) {
            return $this->emptyResult(["File [{$path}] was not found."]);
        }

        return $this->parse((string) file_get_contents($path));
    }

    /**
     * @return array<string, mixed>
     */
    public function parse(string $xml): array
    {
        $previous = libxml_use_internal_errors(true);
        libxml_clear_errors();

        try {
            $document = simplexml_load_string($xml, SimpleXMLElement::class, LIBXML_NOCDATA);

            if (! $document instanceof SimpleXMLElement) {
                $warnings = array_map(
                    static fn ($error): string => trim($error->message),
                    libxml_get_errors(),
                );

                return $this->emptyResult($warnings ?: ['Malformed XML.']);
            }

            return $this->parseDocument($document);
        } catch (Throwable $exception) {
            return $this->emptyResult([$exception->getMessage()]);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function parseDocument(SimpleXMLElement $document): array
    {
        $channel = $document->channel;
        $namespaces = $document->getNamespaces(true);
        $wpNamespace = $namespaces['wp'] ?? null;
        $contentNamespace = $namespaces['content'] ?? null;
        $excerptNamespace = $namespaces['excerpt'] ?? null;

        $authors = [];
        $categories = [];
        $tags = [];

        if ($wpNamespace !== null) {
            foreach ($channel->children($wpNamespace)->author as $author) {
                $authors[] = [
                    'id' => (string) $author->author_id,
                    'login' => (string) $author->author_login,
                    'email' => (string) $author->author_email,
                    'display_name' => (string) $author->author_display_name,
                ];
            }

            foreach ($channel->children($wpNamespace)->category as $category) {
                $categories[] = [
                    'id' => (string) $category->term_id,
                    'slug' => (string) $category->category_nicename,
                    'name' => (string) $category->cat_name,
                ];
            }

            foreach ($channel->children($wpNamespace)->tag as $tag) {
                $tags[] = [
                    'id' => (string) $tag->term_id,
                    'slug' => (string) $tag->tag_slug,
                    'name' => (string) $tag->tag_name,
                ];
            }
        }

        $posts = [];
        $pages = [];
        $attachments = [];
        $unknownPostTypes = [];

        foreach ($channel->item as $item) {
            $wp = $wpNamespace !== null ? $item->children($wpNamespace) : null;
            $content = $contentNamespace !== null ? $item->children($contentNamespace) : null;
            $excerpt = $excerptNamespace !== null ? $item->children($excerptNamespace) : null;
            $postType = $wp !== null ? (string) $wp->post_type : 'post';

            $parsedItem = [
                'id' => $wp !== null ? (string) $wp->post_id : null,
                'title' => (string) $item->title,
                'slug' => $wp !== null ? (string) $wp->post_name : null,
                'date' => $wp !== null ? (string) $wp->post_date : null,
                'status' => $wp !== null ? (string) $wp->status : null,
                'excerpt' => $excerpt !== null ? (string) $excerpt->encoded : null,
                'content_html' => $content !== null ? (string) $content->encoded : null,
                'author' => (string) $item->children('dc', true)->creator,
                'terms' => $this->parseItemTerms($item),
                'featured_image_id' => $this->metaValue($wp, '_thumbnail_id'),
                'attachment_url' => $wp !== null ? (string) $wp->attachment_url : null,
                'meta' => $this->parseMeta($wp),
            ];

            match ($postType) {
                'post' => $posts[] = $parsedItem,
                'page' => $pages[] = $parsedItem,
                'attachment' => $attachments[] = $parsedItem,
                default => $unknownPostTypes[$postType] = ($unknownPostTypes[$postType] ?? 0) + 1,
            };
        }

        return [
            'site' => [
                'title' => (string) $channel->title,
                'link' => (string) $channel->link,
            ],
            'authors' => $authors,
            'categories' => $categories,
            'tags' => $tags,
            'posts' => $posts,
            'pages' => $pages,
            'attachments' => $attachments,
            'unknown_post_types' => $unknownPostTypes,
            'warnings' => [],
            'summary' => [
                'authors' => count($authors),
                'categories' => count($categories),
                'tags' => count($tags),
                'posts' => count($posts),
                'pages' => count($pages),
                'attachments' => count($attachments),
                'unknown_post_types' => array_sum($unknownPostTypes),
            ],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    protected function parseItemTerms(SimpleXMLElement $item): array
    {
        $terms = [];

        foreach ($item->category as $category) {
            $attributes = $category->attributes();
            $terms[] = [
                'domain' => (string) ($attributes['domain'] ?? ''),
                'slug' => (string) ($attributes['nicename'] ?? ''),
                'name' => (string) $category,
            ];
        }

        return $terms;
    }

    /**
     * @return array<string, string>
     */
    protected function parseMeta(?SimpleXMLElement $wp): array
    {
        if ($wp === null) {
            return [];
        }

        $meta = [];

        foreach ($wp->postmeta as $postmeta) {
            $meta[(string) $postmeta->meta_key] = (string) $postmeta->meta_value;
        }

        return $meta;
    }

    protected function metaValue(?SimpleXMLElement $wp, string $key): ?string
    {
        $meta = $this->parseMeta($wp);

        return $meta[$key] ?? null;
    }

    /**
     * @param  array<int, string>  $warnings
     * @return array<string, mixed>
     */
    protected function emptyResult(array $warnings): array
    {
        return [
            'site' => ['title' => null, 'link' => null],
            'authors' => [],
            'categories' => [],
            'tags' => [],
            'posts' => [],
            'pages' => [],
            'attachments' => [],
            'unknown_post_types' => [],
            'warnings' => array_values(array_filter($warnings)),
            'summary' => [
                'authors' => 0,
                'categories' => 0,
                'tags' => 0,
                'posts' => 0,
                'pages' => 0,
                'attachments' => 0,
                'unknown_post_types' => 0,
            ],
        ];
    }
}
