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
use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{

    public function index()
    {
        $posts = Post::paginate(env('POSTS_PER_PAGE'));
        return new PostsResource($posts);

    }

    /**
     * @param Request $request
     * @return PostResource
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


        $post->votes_up = 0;
        $post->votes_down = 0;

        $post ->date_written = Carbon::now()->format('Y-m-d m:i:s');

        //image handling
        if($request->hasFile('featured_image')){
            $featuredImage = $request->file('featured_image');
            $filename = time().$featuredImage->getClientOriginalName();
            Storage::disk('images')->putFileAs(
                $filename,
                $featuredImage,
                $filename
            );
            $post->featured_image = url('/').'/images' .$filename;


        }

        $post->save();

        return new PostResource($post);



    }

    /**
     * @param $id
     * @return PostResource
     */
    public function show($id)
    {
        $post=Post::find($id);
        return new PostResource($post);
    }

    /**
     * @param Request $request
     * @param $id
     * @return PostResource
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $post = Post::find($id);

        if($request->has('title')) {
            $post->title = $request->get('title');
        }

        if($request->has('content')) {
            $post->content = $request->get('content');
        }

        if($request->has('category_id')) {

            if(intval( $request->get('category_id')) !=0 ){
                $post->category_id = $request->get('category_id');
            }

        }

        //image handling
        if($request->hasFile('featured_image')){
            $featuredImage = $request->file('featured_image');
            $filename = time().$featuredImage->getClientOriginalName();
            Storage::disk('images')->putFileAs(
                $filename,
                $featuredImage,
                $filename
            );
            $post->featured_image = url('/').'/images/' .$filename;
        }

        $post->save();

        return new PostResource($post);



    }

    /**
     * @param $id
     * @return PostResource
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        $post->delete();
        return new PostResource($post);

    }

    public function comments($id){
        $posts = Post::find($id);
        $comments = $posts->Comments()-> paginate(env('COMMENTS_PER_PAGE'));
        return new CommentsResource($comments);

    }


}
