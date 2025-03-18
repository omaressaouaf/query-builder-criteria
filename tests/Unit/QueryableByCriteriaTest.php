<?php

namespace Omaressaouaf\QueryBuilderCriteria\Tests\Unit;

use Illuminate\Database\Eloquent\Collection;
use Omaressaouaf\QueryBuilderCriteria\Tests\Factories\UserFactory;
use Omaressaouaf\QueryBuilderCriteria\Tests\Models\Post;
use Omaressaouaf\QueryBuilderCriteria\Tests\TestCase;

class QueryableByCriteriaTest extends TestCase
{
    public function test_it_can_filter(): void
    {
        $dummyPosts = Post::factory()->count(5)->create();

        $posts = $this->getPosts([
            'filter' => [
                'title' => $dummyPosts[0]->title,
            ],
        ]);
        $this->assertCount(1, $posts);
        $this->assertEquals($dummyPosts[0]->title, $posts->first()->title);

        $posts = $this->getPosts([
            'filter' => [
                'id' => $dummyPosts[3]->id,
            ],
        ]);
        $this->assertCount(1, $posts);
        $this->assertEquals($dummyPosts[3]->id, $posts->first()->id);

        $posts = $this->getPosts([
            'filter' => [
                'user' => $dummyPosts[2]->user->id,
            ],
        ]);
        $this->assertCount(1, $posts);
        $this->assertEquals($dummyPosts[2]->user_id, $posts->first()->user_id);

        $dummyPost = Post::factory()->create(['published_at' => now()->subCentury()]);
        $posts = $this->getPosts([
            'filter' => [
                'published_before' => $dummyPost->published_at->toDateTimeString(),
            ],
        ]);
        $this->assertCount(1, $posts);
        $this->assertEquals($dummyPost->published_at, $posts->first()->published_at);

        $dummyPost->delete();
        $posts = $this->getPosts([
            'filter' => [
                'trashed' => 'with',
            ],
        ]);
        $this->assertCount(6, $posts);
        $posts = $this->getPosts([
            'filter' => [
                'trashed' => 'only',
            ],
        ]);
        $this->assertCount(1, $posts);
    }

    public function test_it_can_sort(): void
    {
        Post::factory()->count(5)->create();

        $posts = $this->getPosts()->toArray();
        foreach ($posts as $i => $post) {
            if (! isset($posts[$i - 1])) {
                continue;
            }
            $this->assertTrue($posts[$i]['published_at'] < $posts[$i - 1]['published_at']);
        }

        $posts = $this->getPosts([
            'sort' => 'published_at',
        ])->toArray();
        foreach ($posts as $i => $post) {
            if (! isset($posts[$i - 1])) {
                continue;
            }
            $this->assertTrue($posts[$i]['published_at'] > $posts[$i - 1]['published_at']);
        }
    }

    public function test_it_can_include(): void
    {
        Post::factory()->count(5)->create();

        $posts = $this->getPosts()->toArray();
        foreach ($posts as $post) {
            $this->assertNull($post['user'] ?? null);
        }

        $posts = $this->getPosts([
            'include' => 'user',
            'fields' => [
                'posts' => 'id,user_id',
            ],
        ])->toArray();
        foreach ($posts as $post) {
            $this->assertIsArray($post['user']);
        }
    }

    public function test_it_can_select(): void
    {
        Post::factory()->count(5)->create();

        $posts = $this->getPosts()->toArray();
        foreach ($posts as $post) {
            $this->assertTrue(isset($post['id']) && isset($post['slug']));
        }

        $posts = $this->getPosts([
            'fields' => [
                'posts' => 'id,user_id',
            ],
        ])->toArray();
        foreach ($posts as $post) {
            $this->assertTrue(isset($post['id']) && isset($post['user_id']) && ! isset($post['slug']));
        }
    }

    public function test_it_can_search(): void
    {
        Post::factory()->count(5)->create();

        Post::factory()->create(['title' => 'find me']);
        $posts = $this->getPosts([
            'filter' => [
                'search_query' => 'find me',
            ],
        ]);
        $this->assertCount(1, $posts);
        $this->assertEquals('find me', $posts->first()->title);

        Post::factory()->create(['body' => 'find me']);
        $posts = $this->getPosts([
            'filter' => [
                'search_query' => 'find me',
            ],
        ]);
        $this->assertCount(2, $posts);
        $this->assertEquals('find me', $posts->first()->body);

        Post::factory()->for(UserFactory::new(['bio' => 'by user bio this time']))->create();
        $posts = $this->getPosts([
            'filter' => [
                'search_query' => 'by user bio',
            ],
        ]);
        $this->assertCount(1, $posts);
        $this->assertEquals('by user bio this time', $posts->first()->user->bio);
    }

    private function getPosts(array $params = []): Collection
    {
        $this->get('/?'.http_build_query($params));

        return Post::query()->queryByCriteria()->get();
    }
}
