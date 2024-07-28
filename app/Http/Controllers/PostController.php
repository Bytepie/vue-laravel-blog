<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function getPosts(Request $request)
    {
        $query = $request->query('query');
        $posts = DB::table('posts');

        if (empty($query)) {
            return response(['posts' => $posts->paginate(10), 'request' => $request->all()], 200);
        } else {
            return response(['posts' => $posts->where('title', 'like', '%' . $query . '%')->paginate(10), 'query' => $query], 200);
            // return response('Query is empty', 204);
        }
    }

    public function store(Request $request)
    {
        $fields = $request->all();
        $errors = $this->postValidation($fields);
        if ($errors->fails()) {
            return response($errors->errors()->all(), 422);
        }

        $post = Post::create([
            'title' => $fields['title'],
            'post_content' => $fields['post_content'],
        ]);

        return response($post, 201);
    }

    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        if (empty($post)) {
            return response(['message' => 'Resource not found'], 404);
        }
        $fields = $request->all();
        dd($request->all());
        $errors = $this->postValidation($fields);
        if ($errors->fails()) {
            return response($errors->errors()->all(), 422);
        }
        $post = $post->update([
            'title' => $fields['title'],
            'post_content' => $fields['post_content'],
        ]);
        return response($post, 200);
    }

    public function postValidation($fields)
    {
        $errors = Validator::make($fields, [
            'title' => 'required|min:3|string',
            'post_content' => 'required|string',
        ]);

        return $errors;
    }
}
