<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;

class PostService
{

    public function getPosts($prem)
    {
        $posts = Post::select('*');

        if (!empty($prem['user_id'])) {
            $posts->where('user_id', $prem['user_id']);
        }
        if (!empty($prem['category'])) {
            $posts->where('category', $prem['category']);
        }
        if (!empty($prem['content'])) {
            $posts->where('content', 'like', '%' . $prem['content'] . '%');
        }
        if (!empty($prem['published_start']) && !empty($prem['published_end'])) {
            $posts->whereBetween('published_at', [$prem['published_start'], $prem['published_end']]);
        }

        $offset = $prem['per_page'] * ($prem['page'] - 1);
        $posts->orderBy('id', 'DESC')->skip($offset)->take($prem['per_page']);
     
        $user_id = $posts->pluck('user_id');
        $userNames = User::select(['id', 'name'])->whereIn('id', $user_id)->get()->mapWithKeys(function ($item) {
            return [$item['id'] => $item['name']];
        });

        $items = [];
        foreach ($posts->get() as $post) {
            $items[] = [
                'user_id' => (string) $post->user_id,
                'author' => $userNames[$post->user_id],
                'category' => $post->category,
                'content' => $post->content,
                'published_at' => $post->published_at,
            ];
        }

        return $items;
    }
}
