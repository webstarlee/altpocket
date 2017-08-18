<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Update;
use App\Question;
use App\Answer;
use App\Vote;
use App\Reply;
use App\User;
use App\Change;
use Auth;
use Alert;
use Redirect;
use App\Notifications\NewAnswer;
use App\Notifications\NewQuestion;
use DB;


class SupportController extends Controller
{
  public function index()
  {
    return view('support.index');
  }
  public function about()
  {
    return view('support.about');
  }
  public function ask()
  {
    return view('support.ask');
  }
  public function reply($id)
  {
    return view('support.reply', ['answer' => Answer::where('id', $id)->first()]);
  }
  public function updates()
  {
    return view('support.updates', ['updates' => Update::orderBy('id', 'desc')->get(), 'changes' => Change::get()]);
  }
  public function questions(Request $request)
  {
    if($request->get('q') == ""){
      if($request->get('filter') == "")
      {
      return view('support.questions', ['questions' => Question::where('sticky', 'no')->orderby('created_at', 'desc')->get(), 'stickys' => Question::where('sticky', 'yes')->orderby('created_at', 'desc')->get()]);
      }
      else
      {
        return view('support.questions', ['questions' => Question::where([['sticky', 'no'], ['tag', '=', $request->get('filter')]])->orderby('created_at', 'desc')->get(), 'stickys' => Question::where('sticky', 'yes')->orderby('created_at', 'desc')->get()]);
      }
    } else
    {
        return view('support.questions', ['questions' => Question::where([['sticky', 'no'], ['title', 'LIKE', "%".$request->get('q')."%"]])->orderby('created_at', 'desc')->get(), 'stickys' => Question::where('sticky', 'yes')->orderby('created_at', 'desc')->get()]);
    }
  }

  public function my(Request $request)
  {
    if($request->get('filter') == "")
    {
      return view('support.my', ['questions' => Question::orderby('created_at', 'desc')->get()]);
    } else
    {
      return view('support.my', ['questions' => Question::where('tag', $request->get('filter'))->orderby('created_at', 'desc')->get()]);
    }
  }

  public function question($id)
  {
    $question = Question::where('id', $id)->first();
    if($question){
    $question->views += 1;
    $question->save();
    return view('support.question', ['question' => Question::where('id', $id)->first(), 'answers' => Answer::where([['questionid', '=', $id], ['tag', '=', 0]])->get(), 'answer' => Answer::where([['questionid', '=', $id], ['tag', '=', 1]])->first()]);
  } else {
    return view('support.questions', ['questions' => Question::where('sticky', 'no')->orderby('created_at', 'desc')->get(), 'stickys' => Question::where('sticky', 'yes')->orderby('created_at', 'desc')->get()]);
  }
  }

  //Post Stuff
  public function postQuestion(Request $request)
  {
    if(Auth::user())
    {
      $question = new Question;
      $question->userid = Auth::user()->id;
      $question->category = $request->get('category');
      $question->title = $request->get('title');
      $question->question = app('profanityFilter')->filter($request->get('message'));
      $question->views = 0;
      $question->answers = 0;
      $question->votes = 0;
      $question->tag = "Unanswered";
      $question->sticky = "no";
      $question->save();


      $staffs = DB::table('user_group')->where('group_id', '8')->orWhere('group_id', '6')->get();

      foreach($staffs as $staff)
      {
        $notifiable = User::where('id', $staff->user_id)->first();

        //Notification array
        $notification = [
            'icon' => 'fa fa-question-circle',
            'title' => 'New Question',
            'data' => 'There is a new question on the help desk.',
            'type' => 'question',
            'question' => $question->id
        ];

        $notifiable->notify(new NewQuestion($notification));

      }


      Alert::success('Your question has been posted!', 'Post successful');
      return redirect('/question/'.$question->id);
    } else
    {
      Alert::error('You need to be logged in to post a question.', 'Post failed');
      return redirect('/login/');
    }
  }

  public function postAnswer($id, Request $request)
  {
    if(Auth::user())
    {
      $answer = new Answer;
      $answer->userid = Auth::user()->id;
      $answer->questionid = $id;
      $answer->votes = 0;
      $answer->description = app('profanityFilter')->filter($request->get('message'));
      $answer->tag = 0;
      $answer->save();

      $question = Question::where('id', $id)->first();
      $question->answers += 1;

      if($question->tag == "Unanswered")
      {
        $question->tag = "Open";
      }

      $question->save();


      // Get notify guy
      $notifiable = User::where('id', $question->userid)->first();

      //Notification array
      $notification = [
          'icon' => 'fa fa-reply',
          'title' => 'New Answer',
          'data' => 'You have a new answer on your question.',
          'type' => 'question',
          'question' => $question->id
      ];

      $notifiable->notify(new NewAnswer($notification));


      Alert::success('Your answer has been posted!', 'Post successful');
      return redirect('/question/'.$id);

    } else
    {
      Alert::error('You need to be logged in to answer a question.', 'Post failed');
      return redirect('/login/');
    }
  }

  public function postReply($id, Request $request)
  {
    if(Auth::user())
    {
      $reply = new Reply;
      $reply->userid = Auth::user()->id;
      $reply->answerid = $id;
      $reply->reply = app('profanityFilter')->filter($request->get('message'));
      $reply->save();

      $answer = Answer::where('id', $id)->first();
      $question = Question::where('id', $answer->questionid)->first();

      Alert::success('Your reply has been posted!', 'Reply successful');
      return redirect('/question/'.$question->id);
    }
    else
    {
      Alert::error('You need to be logged in to answer a question.', 'Post failed');
      return redirect('/login/');
    }
  }

  // Actions

  public function delete($id)
  {
    if(Auth::user())
    {
      $question = Question::where('id', $id)->first();
      if($question)
      {
        if(Auth::user()->id == $question->userid || Auth::user()->isStaff() || Auth::user()->isFounder())
        {
          $question->delete();

          Alert::success('Your question was deleted!', 'Delete successful');
          return redirect('/questions/');

        } else
        {
          Alert::error('You can´t delete someone elses question.', 'Delete failed');
          return Redirect::back();
        }

      } else
      {
        Alert::error('No question was found!', 'Delete failed');
        return Redirect::back();
      }

    }
    else
    {
      Alert::error('You must be logged in to delete a question.', 'Delete failed');
      return redirect('/login');
    }
  }

  public function delete_a($id)
  {
    if(Auth::user())
    {
      $answer = Answer::where('id', $id)->first();
      $question = Question::where('id', $answer->questionid)->first();
      if($answer)
      {
        if(Auth::user()->id == $answer->userid)
        {
          $answer->delete();

          $question->answers -= 1;
          $question->save();

          Alert::success('Your answer was deleted!', 'Delete successful');
          return Redirect::back();

        } else
        {
          Alert::error('You can´t delete someone elses answer.', 'Delete failed');
          return Redirect::back();
        }

      } else
      {
        Alert::error('No answer was found!', 'Delete failed');
        return Redirect::back();
      }

    }
    else
    {
      Alert::error('You must be logged in to delete a answer.', 'Delete failed');
      return redirect('/login');
    }
  }

  public function delete_r($id)
  {
    if(Auth::user())
    {
      $reply = Reply::where('id', $id)->first();
      if($reply)
      {
        if(Auth::user()->id == $reply->userid)
        {
          $reply->delete();

          Alert::success('Your reply was deleted!', 'Delete successful');
          return Redirect::back();

        } else
        {
          Alert::error('You can´t delete someone elses reply.', 'Delete failed');
          return Redirect::back();
        }

      } else
      {
        Alert::error('No reply was found!', 'Delete failed');
        return Redirect::back();
      }

    }
    else
    {
      Alert::error('You must be logged in to delete a reply.', 'Delete failed');
      return redirect('/login');
    }
  }

  public function edit($id)
  {
    if(Auth::user())
    {
      $question = Question::where('id', $id)->first();

      if(Auth::user()->id == $question->userid)
      {
        return view('support.edit', ['question' => $question]);
      } else
      {
        Alert::error('You can´t edit someone elses question.', 'Edit failed');
        return Redirect::back();
      }
    } else
    {
      Alert::error('You must be logged in to delete a question.', 'Delete failed');
      return redirect('/login');
    }
  }

  public function edit_a($id)
  {
    if(Auth::user())
    {
      $answer = Answer::where('id', $id)->first();

      if(Auth::user()->id == $answer->userid)
      {
        return view('support.edit_a', ['answer' => $answer]);
      } else
      {
        Alert::error('You can´t edit someone elses answer.', 'Edit failed');
        return Redirect::back();
      }
    } else
    {
      Alert::error('You must be logged in to delete a answer.', 'Delete failed');
      return redirect('/login');
    }
  }

  public function edit_r($id)
  {
    if(Auth::user())
    {
      $reply = Reply::where('id', $id)->first();
      $answer = Answer::where('id', $reply->answerid)->first();

      if(Auth::user()->id == $reply->userid)
      {
        return view('support.edit_r', ['reply' => $reply, 'answer' => $answer]);
      } else
      {
        Alert::error('You can´t edit someone elses reply.', 'Edit failed');
        return Redirect::back();
      }
    } else
    {
      Alert::error('You must be logged in to delete a reply.', 'Delete failed');
      return redirect('/login');
    }
  }

  public function update($id, Request $request)
  {
    if(Auth::user())
    {
      $question = Question::where('id', $id)->first();
      if($question)
      {
        if(Auth::user()->id == $question->userid)
        {
          $question->title = $request->get('title');
          $question->category = $request->get('category');
          $question->question = app('profanityFilter')->filter($request->get('message'));
          $question->save();

          Alert::success('Your question was edited.', 'Edit Successful');
          return redirect('/question/'.$id);
        }
      } else
      {
        Alert::error('We could not edit that question.', 'Edit failed');
        return Redirect::back();
      }
    } else
    {
      Alert::error('You must be logged in to edit a question.', 'Edit failed');
      return redirect('/login');
    }
  }

  public function update_a($id, Request $request)
  {
    if(Auth::user())
    {
      $answer = Answer::where('id', $id)->first();
      if($answer)
      {
        if(Auth::user()->id == $answer->userid)
        {
          $answer->description = app('profanityFilter')->filter($request->get('message'));
          $answer->save();

          Alert::success('Your answer was edited.', 'Edit Successful');
          return redirect('/question/'.$answer->questionid);
        }
      } else
      {
        Alert::error('We could not edit that answer.', 'Edit failed');
        return Redirect::back();
      }
    } else
    {
      Alert::error('You must be logged in to edit a answer.', 'Edit failed');
      return redirect('/login');
    }
  }

  public function update_r($id, Request $request)
  {
    if(Auth::user())
    {
      $reply = Reply::where('id', $id)->first();
      $answer = Answer::where('id', $reply->answerid)->first();
      $question = Question::where('id', $answer->questionid)->first();
      if($reply)
      {
        if(Auth::user()->id == $reply->userid)
        {
          $reply->reply = app('profanityFilter')->filter($request->get('message'));
          $reply->save();

          Alert::success('Your reply was edited.', 'Edit Successful');
          return redirect('/question/'.$question->id);
        }
      } else
      {
        Alert::error('We could not edit that reply.', 'Edit failed');
        return Redirect::back();
      }
    } else
    {
      Alert::error('You must be logged in to edit a reply.', 'Edit failed');
      return redirect('/login');
    }
  }

  public function best($id)
  {
    $answer = Answer::where('id', $id)->first();
    $question = Question::where('id', $answer->questionid)->first();
    if(Auth::user())
    {
      if(Auth::user()->isFounder() || Auth::user()->isStaff() || Auth::user()->id == $answer->userid)
      {

        if(!$answer->tag == 1)
        {
        if(Answer::where([['questionid', '=', $answer->questionid], ['tag', '=', 1]])->exists())
        {
          $oldbest = Answer::where([['questionid', '=', $answer->questionid], ['tag', '=', 1]])->first();
          $oldbest->tag = 0;
          $oldbest->save();
        }


        $answer->tag = 1;
        $answer->save();

        $question->tag = "Resolved";
        $question->save();

        Alert::success('The answer was marked as the best answer!', 'Mark Successful');
        return Redirect::back();
      } else
      {
        $answer->tag = 0;
        $answer->save();

        $question->tag = "Open";
        $question->save();

        Alert::success('The answer was marked as the best answer!', 'Mark Successful');
        return Redirect::back();
      }
      } else
      {
        Alert::error('You are not allowed to mark this answer as best answer.', 'Mark failed');
        return Redirect::back();
      }
    } else
    {
      Alert::error('You must be logged in to edit a reply.', 'Edit failed');
      return redirect('/login');
    }
  }

  public function sticky($id)
  {
    $question = Question::where('id', $id)->first();

    if(Auth::user()->isFounder() || Auth::user()->isStaff())
    {
      if($question->sticky == "no")
      {
        $question->sticky = "yes";
        $question->save();

        Alert::success('The question was marked as a sticky.', 'Sticky Successful');
        return Redirect::back();
      } else
      {
        $question->sticky = "no";
        $question->save();

        Alert::success('The question was unmarked as a sticky.', 'Unsticky Successful');
        return Redirect::back();
      }
    }
  }



  // Upvote and Downvote answers
  public function upvote($id)
  {
    if(Auth::user())
    {
    $question = Question::where('id', $id)->first();

      if(!Vote::where([['userid', '=', Auth::user()->id], ['vote_type', '=', 'up'], ['type', '=', 'question'], ['questionid', '=', $id]])->exists())
      {
        $question->votes += 1;
        $question->save();

        if(Vote::where([['userid', '=', Auth::user()->id], ['vote_type', '=', 'down'], ['type', '=', 'question'], ['questionid', '=', $id]])->exists())
        {
          $vote2 = Vote::where([['userid', '=', Auth::user()->id], ['vote_type', '=', 'down'], ['type', '=', 'question'], ['questionid', '=', $id]])->first();
          $question->votes += 1;
          $question->save();
          $vote2->delete();
        }


        $vote = new Vote;
        $vote->userid = Auth::user()->id;
        $vote->questionid = $id;
        $vote->vote_type = "Up";
        $vote->type = "question";
        $vote->save();

        Alert::success('You have upvoted the question!', 'Upvote successful');
        return Redirect::back();

      } else
      {
        Alert::error('You have already upvoted this question.', 'Upvote failed');
        return Redirect::back();
      }

    } else
    {
      Alert::error('You have to be logged in to upvote a question.', 'Upvote failed');
      return Redirect::back();
    }

  }

  public function downvote($id)
  {
    if(Auth::user())
    {
    $question = Question::where('id', $id)->first();

      if(!Vote::where([['userid', '=', Auth::user()->id], ['vote_type', '=', 'down'], ['type', '=', 'question'], ['questionid', '=', $id]])->exists())
      {
        $question->votes -= 1;
        $question->save();

        if(Vote::where([['userid', '=', Auth::user()->id], ['vote_type', '=', 'up'], ['type', '=', 'question'], ['questionid', '=', $id]])->exists())
        {
          $vote2 = Vote::where([['userid', '=', Auth::user()->id], ['vote_type', '=', 'up'], ['type', '=', 'question'], ['questionid', '=', $id]])->first();
          $question->votes -= 1;
          $question->save();
          $vote2->delete();
        }


        $vote = new Vote;
        $vote->userid = Auth::user()->id;
        $vote->questionid = $id;
        $vote->vote_type = "Down";
        $vote->type = "question";
        $vote->save();

        Alert::success('You have downvoted the question!', 'Downvote successful');
        return Redirect::back();

      } else
      {
        Alert::error('You have already downvoted this question.', 'Downvote failed');
        return Redirect::back();
      }

    } else
    {
      Alert::error('You have to be logged in to downvote a question.', 'Downvote failed');
      return Redirect::back();
    }

  }

  // Upvote and Downvote answers
  public function upvote_a($id)
  {
    if(Auth::user())
    {
    $answer = Answer::where('id', $id)->first();

      if(!Vote::where([['userid', '=', Auth::user()->id], ['vote_type', '=', 'up'], ['type', '=', 'answer'], ['questionid', '=', $id]])->exists())
      {
        $answer->votes += 1;
        $answer->save();

        if(Vote::where([['userid', '=', Auth::user()->id], ['vote_type', '=', 'down'], ['type', '=', 'answer'], ['questionid', '=', $id]])->exists())
        {
          $vote2 = Vote::where([['userid', '=', Auth::user()->id], ['vote_type', '=', 'down'], ['type', '=', 'answer'], ['questionid', '=', $id]])->first();
          $answer->votes += 1;
          $answer->save();
          $vote2->delete();
        }


        $vote = new Vote;
        $vote->userid = Auth::user()->id;
        $vote->questionid = $id;
        $vote->vote_type = "Up";
        $vote->type = "answer";
        $vote->save();

        Alert::success('You have upvoted the answer!', 'Upvote successful');
        return Redirect::back();

      } else
      {
        Alert::error('You have already upvoted this answer.', 'Upvote failed');
        return Redirect::back();
      }

    } else
    {
      Alert::error('You have to be logged in to upvote an answer.', 'Upvote failed');
      return Redirect::back();
    }

  }

  public function downvote_a($id)
  {
    if(Auth::user())
    {
    $answer = Answer::where('id', $id)->first();

      if(!Vote::where([['userid', '=', Auth::user()->id], ['vote_type', '=', 'down'], ['type', '=', 'answer'], ['questionid', '=', $id]])->exists())
      {
        $answer->votes -= 1;
        $answer->save();

        if(Vote::where([['userid', '=', Auth::user()->id], ['vote_type', '=', 'up'], ['type', '=', 'answer'], ['questionid', '=', $id]])->exists())
        {
          $vote2 = Vote::where([['userid', '=', Auth::user()->id], ['vote_type', '=', 'up'], ['type', '=', 'answer'], ['questionid', '=', $id]])->first();
          $vote2->delete();
          $answer->votes -= 1;
          $answer->save();
        }


        $vote = new Vote;
        $vote->userid = Auth::user()->id;
        $vote->questionid = $id;
        $vote->vote_type = "Down";
        $vote->type = "answer";
        $vote->save();

        Alert::success('You have downvoted the answer!', 'Downvote successful');
        return Redirect::back();

      } else
      {
        Alert::error('You have already downvoted this answer.', 'Downvote failed');
        return Redirect::back();
      }

    } else
    {
      Alert::error('You have to be logged in to downvote an answer.', 'Downvote failed');
      return Redirect::back();
    }

  }

}
