<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    function getPosts(Request $request)
    {
        $query = $request->get('query');
        $posts = DB::table('post');

        if (empty($query)) {
            return response($posts->paginate(10), 200);
        } else {
            return response($posts->where('title', 'like', '%' . $query . '%')->paginate(10), 200);
            // return response('Query is empty', 204);
        }
    }
}
