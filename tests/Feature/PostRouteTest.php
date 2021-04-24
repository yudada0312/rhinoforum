<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\UserAndPostTableSeeder;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;

class PostRouteTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(UserAndPostTableSeeder::class);
    }

    // 測試透過作者查詢貼文
    public function testFilterByUser()
    {
        $parameters = [
            'per_page' => 10,
            'page' => 1,
            'user_id' => 1
        ];
        $response = $this->call('GET', 'getPosts', $parameters);
        $response
            ->assertStatus(200)
            ->assertJsonPath('posts.0.user_id', '1');
        $this->assertTrue(true);
    }

    // 測試透過分類查詢貼文
    public function testFilterByCategory()
    {
        $parameters = [
            'per_page' => 10,
            'page' => 1,
            'category' => 'B'
        ];
        $response = $this->call('GET', 'getPosts', $parameters);
        $response
            ->assertStatus(200)
            ->assertJsonPath('posts.0.category', 'B');
        $this->assertTrue(true);
    }

    // 測試透過貼文內容查詢貼文
    public function testFilterByContent()
    {
        $parameters = [
            'per_page' => 10,
            'page' => 1,
            'content' => 'testPartString'
        ];
        $response = $this->call('GET', 'getPosts', $parameters);
        $post = $response->decodeResponseJson();
        $pos = strpos($post['posts'][0]['content'], 'testPartString');

        $this->assertTrue(($pos !== false) ? true : false);
    }

    // 測試透過發布時間（時間 x 和 y 的區間）查詢貼文
    public function testFilterByPublished()
    {
        $parameters = [
            'per_page' => 10,
            'page' => 1,
            'published_start' => '2021-04-02 00:00:00',
            'published_end' => '2021-04-04 00:00:00'
        ];
        $response = $this->call('GET', 'getPosts', $parameters);

        $post = $response->decodeResponseJson();
        foreach ($post['posts'] as $post) {
            $pass = false;
            if ($post['published_at'] >= '2021-04-02 00:00:00' && $post['published_at'] <= '2021-04-04 00:00:00') {
                $pass = true;
            }
            $this->assertTrue($pass);
        }
    }

    // 測試分頁數    
    public function testPagination()
    {
        $parameters = [
            'per_page' => 10,
            'page' => 1,
        ];
        $response = $this->call('GET', 'getPosts', $parameters);
        $post = $response->decodeResponseJson();

        $this->assertEquals(10, count($post['posts']));

        $parameters = [
            'per_page' => 5,
            'page' => 1,
        ];

        $response = $this->call('GET', 'getPosts', $parameters);
        $post = $response->decodeResponseJson();
        $this->assertEquals(5, count($post['posts']));
    }
}
