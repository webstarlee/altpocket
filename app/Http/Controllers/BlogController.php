<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\User;
use App\BlogComment;
use Redirect;
use Alert;
use Auth;
use App\Slim;

class BlogController extends Controller
{
    public function index()
    {
      return view('blog.index', ['posts' => Post::get()]);
    }

    public function viewPost($id)
    {

      $post = Post::where('id', $id)->first();

      return view('blog.single', ['post' => Post::where('id', $id)->first(), 'user' => User::where('id', $post->userid)->first(), 'comments' => BlogComment::where('postid', $id)->get()]);
    }

    public function leaveComment($id, Request $request)
    {
      $post = Post::where('id', $id)->first();

      if(Auth::user())
      {
        $comment = new BlogComment;
        $comment->userid = Auth::user()->id;
        $comment->comment = $request->get('comment');
        $comment->postid = $id;
        $comment->save();

        Alert::success('Your comment has been posted!', 'Post successful');
        return Redirect::back();
      } else
      {
        Alert::error('You need to be logged in to leave a comment on the blog.', 'Post failed');
        return Redirect::back();
      }
    }

    public function deleteComment($id)
    {
      $comment = BlogComment::where('id', $id)->first();
      if(Auth::user())
      {
        if(Auth::user()->id == $comment->userid || Auth::user()->isStaff() || Auth::user()->isFounder())
        {
          $comment->delete();
          Alert::success('Your comment was deleted.', 'Delete successful');
          return Redirect::back();
        }
      } else
      {
        Alert::error('You can´t delete someone elses comment!', 'Delete failed');
        return Redirect::back();
      }
    }

    public function viewMake()
    {
      if(Auth::user())
      {
        if(Auth::user()->isFounder())
        {
          return view('blog.makepost');
        } else
        {
          return redirect('/');
        }
      }
    }

    public function makePost(Request $request)
    {
      if(Auth::user())
      {
        if(Auth::user()->isFounder())
        {




          // Get posted data
          $images = Slim::getImages();

          // No image found under the supplied input name
          if ($images == false) {

              // inject your own auto crop or fallback script here
              echo '<p>Slim was not used to upload these images.</p>';

          }
          else {
              foreach ($images as $image) {

                  $files = array();

                  // save output data if set
                  if (isset($image['output']['data'])) {

                      // Save the file
                      $name = $image['input']['name'];

                      // We'll use the output crop data
                      $data = $image['output']['data'];

                      // If you want to store the file in another directory pass the directory name as the third parameter.
                      // $file = Slim::saveFile($data, $name, 'my-directory/');

                      // If you want to prevent Slim from adding a unique id to the file name add false as the fourth parameter.
                      // $file = Slim::saveFile($data, $name, 'tmp/', false);

                      $post = new Post;
                      $post->userid = Auth::user()->id;
                      $post->title = $request->get('title');
                      $post->description = $request->get('message');
                      $post->image = $name;
                      $post->save();


                      $output = Slim::saveFile($data, $name, 'uploads/blog/'.$post->id.'/', false);

                      Alert::success('The blog post was posted!', 'Post sucessful');
                      return redirect('post/'.$post->id);
                      array_push($files, $output);
                  }

                  // save input data if set
                  if (isset ($image['input']['data'])) {

                      // Save the file
                      $name = $image['input']['name'];

                      // We'll use the output crop data
                      $data = $image['input']['data'];

                      // If you want to store the file in another directory pass the directory name as the third parameter.
                      // $file = Slim::saveFile($data, $name, 'my-directory/');

                      // If you want to prevent Slim from adding a unique id to the file name add false as the fourth parameter.
                      // $file = Slim::saveFile($data, $name, 'tmp/', false);

                      $post = new Post;
                      $post->userid = Auth::user()->id;
                      $post->title = $request->get('title');
                      $post->description = $request->get('message');
                      $post->image = $name;
                      $post->save();

                      $input = Slim::saveFile($data, $name, 'uploads/blog/'.$post->id.'/', false);


                      Alert::success('The blog post was posted!', 'Post sucessful');
                      return redirect('post/'.$post->id);
                      array_push($files, $input);
                  }


              }
          }
        } else {

        }
      }
    }

    public function viewEdit($id)
    {
      if(Auth::user())
      {
        if(Auth::user()->isFounder())
        {
          return view('blog.editpost', ['post' => Post::where('id', $id)->first()]);
        } else
        {
          return redirect('/');
        }
      }
    }

    public function editPost($id, Request $request)
    {
      $post = Post::where('id', $id)->first();

      if(Auth::user())
      {
        if(Auth::user()->isFounder())
        {




          // Get posted data
          $images = Slim::getImages();

          // No image found under the supplied input name
          if ($images == false) {

              // inject your own auto crop or fallback script here
              echo '<p>Slim was not used to upload these images.</p>';

          }
          else {
              foreach ($images as $image) {

                  $files = array();

                  // save output data if set
                  if (isset($image['output']['data'])) {

                      // Save the file
                      $name = $image['input']['name'];

                      // We'll use the output crop data
                      $data = $image['output']['data'];

                      // If you want to store the file in another directory pass the directory name as the third parameter.
                      // $file = Slim::saveFile($data, $name, 'my-directory/');

                      // If you want to prevent Slim from adding a unique id to the file name add false as the fourth parameter.
                      // $file = Slim::saveFile($data, $name, 'tmp/', false);

                      $post->title = $request->get('title');
                      $post->description = $request->get('message');
                      $post->image = $name;
                      $post->save();


                      $output = Slim::saveFile($data, $name, 'uploads/blog/'.$post->id.'/', false);

                      Alert::success('The blog post was edited!', 'Edit sucessful');
                      return redirect('post/'.$post->id);
                      array_push($files, $output);
                  }

                  // save input data if set
                  if (isset ($image['input']['data'])) {

                      // Save the file
                      $name = $image['input']['name'];

                      // We'll use the output crop data
                      $data = $image['input']['data'];

                      // If you want to store the file in another directory pass the directory name as the third parameter.
                      // $file = Slim::saveFile($data, $name, 'my-directory/');

                      // If you want to prevent Slim from adding a unique id to the file name add false as the fourth parameter.
                      // $file = Slim::saveFile($data, $name, 'tmp/', false);


                      $post->title = $request->get('title');
                      $post->description = $request->get('message');
                      $post->image = $name;
                      $post->save();

                      $input = Slim::saveFile($data, $name, 'uploads/blog/'.$post->id.'/', false);


                      Alert::success('The blog post was edited!', 'Edit sucessful');
                      return redirect('post/'.$post->id);
                      array_push($files, $input);
                  }


              }
          }
        } else {

        }
      }
    }
}
