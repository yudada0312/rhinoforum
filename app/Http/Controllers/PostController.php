<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PostService;
use Illuminate\Support\Facades\Cache;

class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function getPosts(Request $request)
    {
        $prem = $request->all();

        $cacheKey = 'getPosts:' . json_encode($prem);
        $cacheValue = Cache::get($cacheKey);
        if (!empty($cacheValue)) {
            return response()->json($cacheValue);
        }

        $posts = $this->postService->getPosts($request->all());

        // == 寫入快取資料,當文章有更新會新增再清除緩存==
        $respData = ['posts' => $posts];
        Cache::put($cacheKey, $respData);

        return response()->json($respData);
    }
}
