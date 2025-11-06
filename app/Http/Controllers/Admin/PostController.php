<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    const OBJECT = 'admin.post';
    const DOT = '.';

    public function index()
    {
        $posts = Post::with('author')->latest()->get();
        return view(self::OBJECT . self::DOT . __FUNCTION__, compact('posts'));
    }

    public function create()
    {
        return view(self::OBJECT . self::DOT . __FUNCTION__);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'slug'    => 'nullable|string|max:255|unique:posts,slug',
            'content' => 'required|string',
        ]);
        $slug = $request->slug ?: Str::slug($request->title);

        $post = Post::create([
            'title'     => $request->title,
            'slug'      => $slug,
            'content'   => $request->content,
            'author_id' => Auth::id(),
        ]);

        return redirect()->route('admin.post.index')->with('success', 'Bài viết đã được tạo!');
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        return view(self::OBJECT . self::DOT . __FUNCTION__, compact('post'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'slug'    => 'nullable|string|max:255|unique:posts,slug,' . $id,
            'content' => 'required|string',
        ]);

        $post = Post::findOrFail($id);
        $slug = $request->slug ?: Str::slug($request->title);

        $post->update([
            'title'   => $request->title,
            'slug'    => $slug,
            'content' => $request->content,
        ]);

        return redirect()->route('admin.post.index')->with('success', 'Bài viết đã được cập nhật!');
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return redirect()->route('admin.post.index')->with('success', 'Bài viết đã được xóa!');
    }
}
