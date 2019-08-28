<?php

namespace App\Http\Controllers\Api;

use App\Comment;
use App\Http\Resources\CommentsResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostsResource;
use App\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostsController extends Controller
{

    public function index()
    {
        $posts = Post::paginate(env('POSTS_PER_PAGE'));
        return new PostsResource($posts);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'category_id' => 'required',
        ]);

        $user = $request->user();
        $post = new post();
        $post->title = $request->get('title');
        $post->content = $request->get('content');
        if(intval( $request->get('category_id')) !=0 ){
            $post->category_id = $request->get('category_id');
        }
        $post -> user_id = $user->id;
        //TODO:handle the features_image file uplaod

        $post->votes_up = 0;
        $post->votes_down = 0;

        $post ->date_written = Carbon::now()->format('Y-m-d m:i:s');

        $post->save();

        return new PostResource($post);



    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post=Post::find($id);
        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function comments($id){
        $posts = Post::find($id);
        $comments = $posts->Comments()-> paginate(env('COMMENTS_PER_PAGE'));
        return new CommentsResource($comments);



    }


}
