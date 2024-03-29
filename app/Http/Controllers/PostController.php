<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Post;
use Validator;
use DB;
use url;
class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $query = $request->get('query');

       $data=DB::table('posts');

       if (!is_null($query)) 
       {
          $posts=$data->where('title','like','%'.$query.'%')
          ->paginate(5);
          return response($posts,201);
       }
        
        $posts=$data->paginate(5);
        return response($posts,201);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $fields=$request->all();

        $errors = Validator::make($fields, [
            'title' => 'required|string',
            'post_content' => 'required|string'
        ]);

        if($errors->fails()) {
             return response($errors->errors()->all(),422);
        }

        $post= Post::create($fields);
        return response([
            'post'=>$post,
            'message'=>'post created !'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Post::find($id);
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
        $Post = Post::find($id);
        $Post->update($request->all());
        return $Post;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         Post::destroy($id);
         return response(['message'=>'post deleted !'],201);
    }



    function addPhoto(Request $request)
    {


        if ($request->hasFile('image')) {
           

            $image = $request->file('image');

            $input['file'] = time() . '.' . $image->getClientOriginalExtension();
            // Corrected code
            Storage::disk('public')
            ->put('images/' . $input['file'], file_get_contents($image), 'public');


            $baseUrl= url('/');

            $imageURL= $baseUrl.'/storage/images/'.$input['file'];

            Post::where('id',$request->postId)
                ->update(['image' =>$imageURL]);


            return response(['message'=>'image uploaded !'],201);
        }

  }


    

}