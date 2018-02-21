<?php

namespace App\Http\Controllers;

use Auth;
use Alert;
use Cache;
use Validator;
use App\User;
use App\Slim;
use App\IoGroup;
use App\IoGroupUser;
use App\IoGroupPost;
use App\IoGroupPoll;
use App\IoGroupPollVote;
use App\IoGroupPollAnswer;
use App\IoGroupPhoto;
use App\IoGroupRequest;
use App\IogGroupComment;
use Illuminate\Http\Request;
use App\Events\NewGroupPost;
use App\Events\EditGroupPost;
use App\Events\DeleteGroupPost;

class GroupController extends Controller
{

    public function index() {
        $groups = IoGroup::all();
        $group_users = IoGroupUser::where('user_id', Auth::user()->id)->get();
        $invite_group_count = IoGroupRequest::where('user_id', Auth::user()->id)->where('method', 1)->count();
        $invite_groups = "null";
        if ($invite_group_count > 0) {
            $invite_groups = IoGroupRequest::where('io_group_requests.user_id', Auth::user()->id)->where('method', 1)->join('io_groups', 'io_groups.id', '=', 'io_group_requests.group_id')->select('io_groups.*')->get();
        }
        return view('exploreGroup', ['groups' =>$groups, 'group_users' => $group_users, 'invite_groups' => $invite_groups]);
    }

    public function generate_invite_key($id) {
        $check_group = IoGroup::find($id);
        $invite_url = "";
        if ($check_group->expire == 1) {
            $invite_key_never = $check_group->invite;
            $invite_url = url('group/invite/'.$id.'/'.$invite_key_never);
        } else {
            $invite_key = Cache::remember('invite_group:'.$id, 1,  function() use($id) {
                $random_invite = $this->AlphaNumeric(6);
                $group = IoGroup::find($id);
                $group->invite = $random_invite.$group->id;
                $group->save();
                return $group->invite;
            });
            $invite_url = url('group/invite/'.$id.'/'.$invite_key);
        }

        return $invite_url;
    }

    public function set_expire_key($id, $status) {
        $group = IoGroup::find($id);
        if ($group) {
            $group->expire = $status;
            $group->save();
            return "success";
        }
        return "fail";
    }

    public function invite_check($id, $invite) {
        $group = IoGroup::find($id);
        if ($group) {
            $invite_key = null;
            if ($group->expire == 1) {
                $invite_key = $group->invite;
            } else {
                $invite_key = Cache::get('invite_group:'.$group->id);
            }

            if ($invite_key && $invite_key == $invite) {
                $group_user_check = IoGroupUser::where('group_id', $group->id)->where('user_id', Auth::user()->id)->count();
                if ($group_user_check > 0 || $group->user_id == Auth::user()->id) {
                    return redirect('group/view/'.$group->url);
                } else {
                    $new_member = new IoGroupUser;
                    $new_member->group_id = $group->id;
                    $new_member->user_id = Auth::user()->id;
                    $new_member->save();

                    return redirect('group/view/'.$group->url);
                }
            } else {
                Alert::error('Invite key Expired.', 'Failed')->autoclose(3000);
                return redirect()->route('group.explore');
            }
        }

        Alert::error('Invalid invite url.', 'Failed')->autoclose(3000);
        return redirect()->route('group.explore');
    }

    public function autocomplete_user(Request $request) {
        $char_string = $request->username;
        $users = User::where('id', '!=', Auth::user()->id)->where('name', 'like', '%' . $char_string . '%')->get();
        return $users;
    }

    public function autocomplete_user_add_member(Request $request, $id) {
        $char_string = $request->username;
        $users = User::where('id', '!=', Auth::user()->id)->where('name', 'like', '%' . $char_string . '%')->get();
        $filtered_users = array();
        foreach ($users as $user) {
            $group = IoGroup::find($id);
            if ($group) {
                $ismember_check = IoGroupUser::where('group_id', $id)->where('user_id', $user->id)->count();
                $isrequest_check = IoGroupRequest::where('group_id', $id)->where('user_id', $user->id)->count();
                if ($group->user_id != $user->id && $ismember_check == 0 && $isrequest_check == 0) {
                    $filtered_users[] = $user;
                }
            }
        }
        return $filtered_users;
    }

    public static function AlphaNumeric($length) {
         $chars = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
         $clen   = strlen( $chars )-1;
         $id  = '';

         for ($i = 0; $i < $length; $i++) {
                 $id .= $chars[mt_rand(0,$clen)];
         }
         return ($id);
     }

    public function create(Request $request) {

        $random_url = Auth::user()->id."_".time();
        $random_invite = $this->AlphaNumeric(6);
        $group = new IoGroup();
        $group->name = $request->group_name;
        if ($request->group_description != "") {
            $group->description = $request->group_description;
        }
        $group->url = $random_url;
        $group->user_id = Auth::user()->id;
        $group->private = $request->private_group;
        $group->save();

        $group->invite = $random_invite.$group->id;
        $group->save();

        if ($request->request_user != "") {
            foreach ($request->request_user as $user) {
                $group_request = new IoGroupRequest;
                $group_request->group_id = $group->id;
                $group_request->user_id = $user;
                $group_request->method = 1;
                $group_request->save();
            }
        }

        $new_post = new IoGroupPost;
        $new_post->group_id = $group->id;
        $new_post->user_id = Auth::user()->id;
        $new_post->editable = 0;
        $new_post->description = "Created the group <span style='font-size:16px;color: #3778b1;font-weight: bold;'>".$group->name."</span>";
        $new_post->save();

        return redirect('group/view/'.$group->url);
    }

    public function update(Request $request) {
        $group = IoGroup::find($request->group_id_for_edit);
        if ($group) {
            $group->name = $request->_group_name;
            $group->private = $request->_private_group;
            $group->description = $request->_group_description;
            $group->save();

            Alert::success('Group Updated.', 'Success')->autoclose(3000);
            return back();
        }

        Alert::error('Went something Wrong.', 'Failed')->autoclose(3000);
        return back();
    }

    public function check_ban_user($group_id){
        $blocked_user_count = IoGroupUser::where('group_id', $group_id)->where('user_id', Auth::user()->id)->where('user_level', 2)->count();
        if ($blocked_user_count > 0) {
            return false;
        }else {
            return true;
        }
    }

    public function view_single($url) {
        $group = IoGroup::where('url', $url)->first();
        if ($group) {
            if ($this->check_ban_user($group->id)) {
                $group_photos = IoGroupPhoto::where('group_id', $group->id)->get();
                $group_creator = User::find($group->user_id);
                $group_posts = IoGroupPost::where('io_group_posts.group_id', $group->id)
                ->join('users', 'users.id', '=', 'io_group_posts.user_id')
                ->select('io_group_posts.*', 'users.username as poster_name', 'users.avatar as poster_avatar')->orderBy('created_at', 'DESC')->get();

                if($group->private == 1) {
                    $group_member_check = IoGroupUser::where('group_id', $group->id)->where('user_id', Auth::user()->id)->count();
                    if ($group_member_check > 0 || $group->user_id == Auth::user()->id) {
                        return view('viewGroup', ['group' => $group, 'group_photos' => $group_photos, 'group_creator' => $group_creator, 'group_posts' => $group_posts]);
                    } else {
                        return redirect('group/view/'.$group->url.'/about');
                    }
                }
                return view('viewGroup', ['group' => $group, 'group_photos' => $group_photos, 'group_creator' => $group_creator, 'group_posts' => $group_posts]);
            }else {
                Alert::error('You blocked by this group admin.', 'Failed')->autoclose(3000);
                return back();
            }
        }

        return redirect()->route('group.explore');
    }

    public function view_single_about($url) {
        $group = IoGroup::where('url', $url)->first();
        if ($group) {
            if ($this->check_ban_user($group->id)) {
                $group_photos = IoGroupPhoto::where('group_id', $group->id)->get();
                $group_creator = User::find($group->user_id);
                $group_posts = IoGroupPost::where('io_group_posts.group_id', $group->id)
                ->join('users', 'users.id', '=', 'io_group_posts.user_id')
                ->select('io_group_posts.*', 'users.username as poster_name', 'users.avatar as poster_avatar')->orderBy('created_at', 'DESC')->get();

                return view('viewGroup', ['group' => $group, 'group_photos' => $group_photos, 'group_creator' => $group_creator, 'group_posts' => $group_posts]);
            }else {
                Alert::error('You blocked by this group admin.', 'Failed')->autoclose(3000);
                return back();
            }
        }

        return redirect()->route('group.explore');
    }

    public function view_single_members($url) {
        $group = IoGroup::where('url', $url)->first();
        if ($group) {
            if ($this->check_ban_user($group->id)) {
                $group_photos = IoGroupPhoto::where('group_id', $group->id)->get();
                $group_creator = User::find($group->user_id);
                $group_posts = IoGroupPost::where('io_group_posts.group_id', $group->id)
                ->join('users', 'users.id', '=', 'io_group_posts.user_id')
                ->select('io_group_posts.*', 'users.username as poster_name', 'users.avatar as poster_avatar')->orderBy('created_at', 'DESC')->get();

                return view('viewGroup', ['group' => $group, 'group_photos' => $group_photos, 'group_creator' => $group_creator, 'group_posts' => $group_posts]);
            }else {
                Alert::error('You blocked by this group admin.', 'Failed')->autoclose(3000);
                return back();
            }
        }

        return redirect()->route('group.explore');
    }

    public function view_single_blocked_users($url) {
        $group = IoGroup::where('url', $url)->first();
        if ($group) {
            if ($this->check_ban_user($group->id)) {
                $group_photos = IoGroupPhoto::where('group_id', $group->id)->get();
                $group_creator = User::find($group->user_id);
                $group_posts = IoGroupPost::where('io_group_posts.group_id', $group->id)
                ->join('users', 'users.id', '=', 'io_group_posts.user_id')
                ->select('io_group_posts.*', 'users.username as poster_name', 'users.avatar as poster_avatar')->orderBy('created_at', 'DESC')->get();

                return view('viewGroup', ['group' => $group, 'group_photos' => $group_photos, 'group_creator' => $group_creator, 'group_posts' => $group_posts]);
            }else {
                Alert::error('You blocked by this group admin.', 'Failed')->autoclose(3000);
                return back();
            }
        }

        return redirect()->route('group.explore');
    }

    public function view_single_photos($url) {
        $group = IoGroup::where('url', $url)->first();
        if ($group) {
            if ($this->check_ban_user($group->id)) {
                $group_photos = IoGroupPhoto::where('group_id', $group->id)->get();
                $group_creator = User::find($group->user_id);
                $group_posts = IoGroupPost::where('io_group_posts.group_id', $group->id)
                ->join('users', 'users.id', '=', 'io_group_posts.user_id')
                ->select('io_group_posts.*', 'users.username as poster_name', 'users.avatar as poster_avatar')->orderBy('created_at', 'DESC')->get();

                if($group->private == 1) {
                    $group_member_check = IoGroupUser::where('group_id', $group->id)->where('user_id', Auth::user()->id)->count();
                    if ($group_member_check > 0 || $group->user_id == Auth::user()->id) {
                        return view('viewGroup', ['group' => $group, 'group_photos' => $group_photos, 'group_creator' => $group_creator, 'group_posts' => $group_posts]);
                    } else {
                        return redirect('group/view/'.$group->url.'/about');
                    }
                }
                return view('viewGroup', ['group' => $group, 'group_photos' => $group_photos, 'group_creator' => $group_creator, 'group_posts' => $group_posts]);
            }else {
                Alert::error('You blocked by this group admin.', 'Failed')->autoclose(3000);
                return back();
            }
        }

        return redirect()->route('group.explore');
    }

    public function coverphoto_upload_ready(Request $request) {

        $group = IoGroup::find($request->group_id_for_photo);

        $imageRand = rand(1000, 9999);
        $random_name = time()."_".$group->id;

        if(!is_dir(public_path('assets/images/group/'.$group->url))){
            mkdir(public_path('assets/images/group/'.$group->url));
        }

        $dst = public_path('assets/images/group/'.$group->url."/");

        $rules = [
            'file' => 'image',
            'slim[]' => 'image'
            ];

        $validator = Validator::make($request->all(), $rules);
        $errors = $validator->errors();

        if($validator->fails()){
            Alert::error('You are not allowed to upload anything but an image.', 'Upload failed');
            return back();
        }

        // Get posted data
        $images = Slim::getImages();

        // No image found under the supplied input name
        if ($images == false) {
            echo '<p>Slim was not used to upload these images.</p>';
        }
        else {
            foreach ($images as $image) {

                $files = array();

                // save output data if set
                if (isset($image['output']['data'])) {

                    // Save the file
                    $origine_name = $image['input']['name'];
                    $file_type = pathinfo($origine_name, PATHINFO_EXTENSION);
                    $name = $random_name.".".$file_type;

                    // We'll use the output crop data
                    $data = $image['output']['data'];

                    $output = Slim::saveFile($data, $name, $dst, false);

                    $group_photo = new IoGroupPhoto;
                    $group_photo->group_id = $group->id;
                    $group_photo->user_id = Auth::user()->id;
                    $group_photo->photo = $name;
                    $group_photo->save();

                    if ($this->cover_photo_save($group->id, $group_photo->id)) {
                        Alert::success('Cover photo updated.', 'Success')->autoclose(3000);
                        return back();
                        array_push($files, $output);
                    }
                }

                // save input data if set
                if (isset ($image['input']['data'])) {

                    // Save the file
                    $origine_name = $image['input']['name'];
                    $file_type = pathinfo($origine_name, PATHINFO_EXTENSION);

                    $name = $random_name.".".$file_type;

                    $data = $image['input']['data'];
                    $input = Slim::saveFile($data, $name, $dst, false);

                    $group_photo = new IoGroupPhoto;
                    $group_photo->group_id = $group->id;
                    $group_photo->user_id = Auth::user()->id;
                    $group_photo->photo = $name;
                    $group_photo->save();
                    if ($this->cover_photo_save($group->id, $group_photo->id)) {
                        Alert::success('Cover photo updated.', 'Success')->autoclose(3000);
                        return back();
                        array_push($files, $input);
                    }
                }
            }
        }
    }

    public function post_photo_upload(Request $request) {

        $group = IoGroup::find($request->group_id_for_photo);

        $imageRand = rand(1000, 9999);
        $random_name = time()."_".$group->id;

        if(!is_dir(public_path('assets/images/group/'.$group->url))){
            mkdir(public_path('assets/images/group/'.$group->url));
        }

        $dst = public_path('assets/images/group/'.$group->url."/");

        $rules = [
            'file' => 'image',
            'slim[]' => 'image'
            ];

        $validator = Validator::make($request->all(), $rules);
        $errors = $validator->errors();

        if($validator->fails()){
            return "image_vali_fail";
        }

        // Get posted data
        $images = Slim::getImages();

        // No image found under the supplied input name
        if ($images == false) {
            return "image_fail";
        }
        else {
            foreach ($images as $image) {

                $files = array();

                // save output data if set
                if (isset($image['output']['data'])) {

                    // Save the file
                    $origine_name = $image['input']['name'];
                    $file_type = pathinfo($origine_name, PATHINFO_EXTENSION);
                    $name = $random_name.".".$file_type;

                    // We'll use the output crop data
                    $data = $image['output']['data'];

                    $output = Slim::saveFile($data, $name, $dst, false);

                    $group_photo = new IoGroupPhoto;
                    $group_photo->group_id = $group->id;
                    $group_photo->user_id = Auth::user()->id;
                    $group_photo->photo = $name;
                    $group_photo->save();

                    $photo_url = asset('assets/images/group/'.$group->url.'/'.$group_photo->photo);
                    $data_array = array('photo_id' => $group_photo->id, 'photo_url' => $photo_url);
                    return $data_array;
                    array_push($files, $output);
                }

                // save input data if set
                if (isset ($image['input']['data'])) {

                    // Save the file
                    $origine_name = $image['input']['name'];
                    $file_type = pathinfo($origine_name, PATHINFO_EXTENSION);

                    $name = $random_name.".".$file_type;

                    $data = $image['input']['data'];
                    $input = Slim::saveFile($data, $name, $dst, false);

                    $group_photo = new IoGroupPhoto;
                    $group_photo->group_id = $group->id;
                    $group_photo->user_id = Auth::user()->id;
                    $group_photo->photo = $name;
                    $group_photo->save();

                    $photo_url = asset('assets/images/group/'.$group->url.'/'.$group_photo->photo);
                    $data_array = array('photo_id' => $group_photo->id, 'photo_url' => $photo_url);
                    return $data_array;
                    array_push($files, $input);
                }
            }
        }
    }

    public function accept_group_user($id) {
        $request = IoGroupRequest::find($id);
        if ($request) {
            $group = IoGroup::find($request->group_id);
            $user = User::find($request->user_id);
            $check_group_member = IoGroupUser::where('group_id', $group->id)->where('user_id', $user->id)->count();
            if ($check_group_member == 0) {
                $new_member = new IoGroupUser;
                $new_member->group_id = $group->id;
                $new_member->user_id = $user->id;
                $new_member->save();

                $request->delete();
                Alert::success($user->username.' is group member from now.', 'Success')->autoclose(3000);
                return back();
            }else {
                $request->delete();
                Alert::success($user->username.' is already group member', 'Success')->autoclose(3000);
                return back();
            }
        }
        Alert::error('Something went wrong', 'Failed')->autoclose(3000);
        return back();
    }

    public function reject_group_user($id) {
        $request = IoGroupRequest::find($id);
        if ($request) {
            $request->delete();
            Alert::success('Rejected request', 'Success')->autoclose(3000);
            return back();
        }
        Alert::error('Something went wrong', 'Failed')->autoclose(3000);
        return back();
    }

    public function delete_group_user_request($id) {
        $request = IoGroupRequest::find($id);
        if ($request) {
            $request->delete();
            Alert::success('Deleted request', 'Success')->autoclose(3000);
            return back();
        }
        Alert::error('Something went wrong', 'Failed')->autoclose(3000);
        return back();
    }

    public function cover_photo_save($group_id, $photo_id) {
        $group_photo = IoGroupPhoto::find($photo_id);
        if ($group_photo) {
            $group = IoGroup::find($group_id);

            $imagePath = public_path('assets/images/group/'.$group->url."/".$group_photo->photo);

            if(!is_dir(public_path('assets/images/group/'.$group->url."/thumbnail"))){
                mkdir(public_path('assets/images/group/'.$group->url."/thumbnail"));
            }

            $dst = public_path('assets/images/group/'.$group->url."/thumbnail"."/".$group_photo->photo);

            $img_info = getimagesize($imagePath);
            $width = $img_info[0];
            $height = $img_info[1];
            $nimg = imagecreatetruecolor(300,300);
            switch ($img_info[2]) {
                case IMAGETYPE_GIF  : $im_src = imagecreatefromgif($imagePath);  break;
                case IMAGETYPE_JPEG : $im_src = imagecreatefromjpeg($imagePath); break;
                case IMAGETYPE_PNG  : $im_src = imagecreatefrompng($imagePath);  break;
                default : return back()->with('error', 'Unknown file type');
            }
    		imagecopyresized($nimg,$im_src,0, 0, 0, 0, 300, 300, $width, $height);
    		imagejpeg($nimg,$dst);

            $group->image = $group_photo->photo;
            $group->thumbnail = $group_photo->photo;
            $group->save();

            $photo_ids_array = array('0' => $group_photo->id);

            $new_post = new IoGroupPost;
            $new_post->group_id = $group->id;
            $new_post->user_id = Auth::user()->id;
            $new_post->photo_ids = serialize($photo_ids_array);
            $new_post->editable = 0;
            $new_post->description = "Changed group cover photo.";
            $new_post->save();

            return true;
        }
        return false;
    }

    public function coverphoto_choose(Request $request) {
        // return $request->all();
        $group = IoGroup::find($request->choose_photo_group_id);
        $group_photo = IoGroupPhoto::find($request->group_photo_radio);

        if ($group && $group_photo) {
            $uploadedimg_url = asset('assets/images/group/'.$group->url.'/'.$group_photo->photo);
            $image_data = array('group_photo_id' => $group_photo->id, 'img_url' => $uploadedimg_url);
            return $image_data;
        }
        return "fail";
    }

    public function cover_photo_delete($id) {
        $group = IoGroup::find($id);
        if ($group) {
            $group->image = "";
            $group->thumbnail = "";
            $group->save();
            return "sucesss";
        }
        return "fail";
    }

    public function join_group($id) {
        $check_reuest_count = IoGroupRequest::where('group_id', $id)->where('user_id', Auth::user()->id)->where('method', 1)->count();
        $group = IoGroup::find($id);
        if ($check_reuest_count > 0 && $group) {
            $check_already_user = IoGroupUser::where('group_id', $id)->where('user_id', Auth::user()->id)->count();
            if ($check_already_user == 0) {
                $group_user = new IoGroupUser;
                $group_user->group_id = $id;
                $group_user->user_id = Auth::user()->id;
                $group_user->save();
            }

            $group_reuest = IoGroupRequest::where('group_id', $id)->where('user_id', Auth::user()->id)->where('method', 1)->delete();

            return redirect('group/view/'.$group->url);
        }
    }

    public function decline_request_group($id) {
        $check_reuest_count = IoGroupRequest::where('group_id', $id)->where('user_id', Auth::user()->id)->where('method', 1)->count();
        $group = IoGroup::find($id);
        if ($check_reuest_count > 0) {

            $group_reuest = IoGroupRequest::where('group_id', $id)->where('user_id', Auth::user()->id)->where('method', 1)->delete();

            return back();
        }
    }

    public function group_member_add(Request $request) {

        $group = IoGroup::find($request->add_member_group_id);
        if ($group) {
            $new_users = array();
            foreach ($request->request_user as $user_id) {
                $is_exist_check = IoGroupRequest::where('group_id', $group->id)->where('user_id', $user_id)->count();
                if ($is_exist_check == 0) {
                    $new_request = new IoGroupRequest;
                    $new_request->group_id = $group->id;
                    $new_request->user_id = $user_id;
                    $new_request->method = 1;
                    $new_request->save();

                    $user = User::find($user_id);
                    $avatar_url = "";
                    if($user->avatar == "default.jpg"){
                        $avatar_url = asset('assets/img/default.png');
                    }else{
                        if (file_exists('uploads/avatars/'.$user->id.'/'.$user->avatar)) {
                            $avatar_url = asset('uploads/avatars/'.$user->id.'/'.$user->avatar);
                        }else {
                            $avatar_url = asset('assets/img/default.png');
                        }
                    }
                    $user_url = url('user/'.$user->username);

                    $new_users[] = array('avatar_url' => $avatar_url, 'user_url' => $user_url);
                }
            }

            return $new_users;
        }
        return "fail";
    }

    public function add_group_member_request($group_id, $user_id) {

        $group = IoGroup::find($group_id);
        if ($group) {
            $new_request = new IoGroupRequest;
            $new_request->group_id = $group->id;
            $new_request->user_id = $user_id;
            $new_request->method = 1;
            $new_request->save();

            Alert::success('Request sent', 'Success')->autoclose(3000);
            return back();
        }
        Alert::error('Went something wrong', 'failed')->autoclose(3000);
        return back();
    }

    public function join_group_user($id) {

        $group = IoGroup::find($id);
        if ($group) {
            $check_exist_reuqest = IoGroupRequest::where('group_id', $group->id)->where('user_id', Auth::user()->id)->where('method', 0)->count();
            if ($check_exist_reuqest == 0) {
                $new_request = new IoGroupRequest;
                $new_request->group_id = $group->id;
                $new_request->user_id = Auth::user()->id;
                $new_request->method = 0;
                $new_request->save();
            }

            return "success";
        }
        return "fail";
    }

    public function join_group_user_cancel($id) {

        $group = IoGroup::find($id);
        if ($group) {
            $check_exist_reuqest = IoGroupRequest::where('group_id', $group->id)->where('user_id', Auth::user()->id)->where('method', 0)->count();
            if ($check_exist_reuqest > 0) {
                IoGroupRequest::where('group_id', $group->id)->where('user_id', Auth::user()->id)->where('method', 0)->delete();
            }

            return "success";
        }
        return "fail";
    }

    public function join_group_user_on($id) {
        $group = IoGroup::find($id);
        if ($group) {
            $check_exist_reuqest = IoGroupRequest::where('group_id', $group->id)->where('user_id', Auth::user()->id)->where('method', 0)->count();
            if ($check_exist_reuqest == 0) {
                $new_request = new IoGroupRequest;
                $new_request->group_id = $group->id;
                $new_request->user_id = Auth::user()->id;
                $new_request->method = 0;
                $new_request->save();
            }
        }
        return back();
    }

    public function join_group_user_cancel_on($id) {
        $group = IoGroup::find($id);
        if ($group) {
            $check_exist_reuqest = IoGroupRequest::where('group_id', $group->id)->where('user_id', Auth::user()->id)->where('method', 0)->count();
            if ($check_exist_reuqest > 0) {
                IoGroupRequest::where('group_id', $group->id)->where('user_id', Auth::user()->id)->where('method', 0)->delete();
            }
        }
        return back();
    }

    public function own_group_delete($id) {
        $group = IoGroup::find($id);
        if ($group && $group->user_id == Auth::user()->id) {
            $delete_requests = IoGroupRequest::where('group_id', $group->id)->delete();
            $delete_members = IoGroupUser::where('group_id', $group->id)->delete();
            $delete_group_photos = IoGroupPhoto::where('group_id', $group->id)->delete();

            if (is_dir('assets/images/group/'.$group->url)) {
                $this->deleteDir('assets/images/group/'.$group->url);
            }

            $group->delete();

            $redirect_url = route('group.explore');

            return $redirect_url;
        }
        return "fail";
    }

    public function deleteDir($dirPath) {
        if (! is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    public function leave_group($id) {

        $group = IoGroup::find($id);
        if ($group) {
            $is_user_count = IoGroupUser::where('group_id', $id)->where('user_id', Auth::user()->id)->count();
            if ($is_user_count) {
                IoGroupUser::where('group_id', $id)->where('user_id', Auth::user()->id)->delete();
            }
        }
        return back();
    }

    public function group_post_create(Request $request) {
        // dd($request->all());
        $group = IoGroup::find($request->for_post_group_id);
        if ($group) {
            $group_post = new IoGroupPost;
            $group_post->group_id = $group->id;
            $group_post->user_id = Auth::user()->id;

            if ($request->group_post_text) {
                $group_post->status = $request->group_post_text;
            }

            if ($request->gourp_post_photo_id) {
                $group_post->photo_ids = serialize($request->gourp_post_photo_id);
            }

            if ($request->post_giphy) {
                $group_post->giphys = serialize($request->post_giphy);
            }

            if ($request->post_youtube) {
                $group_post->youtubes = serialize($request->post_youtube);
            }
            $group_post->save();

            event(new NewGroupPost($group->id, $group_post->id, $group_post->user_id));
            return $group_post;
        }

        return "fail";
    }

    public function group_post_update(Request $request) {
        // dd($request->all());
        $group_post = IoGroupPost::find($request->group_post_id_for_edit);
        $group = IoGroup::find($group_post->group_id);
        if ($group_post) {

            if ($request->post_text_textarea) {
                $group_post->status = $request->post_text_textarea;
                $group_post->save();
            } else {
                $group_post->status = "";
                $group_post->save();
            }

            if ($request->gourp_post_photo_id) {
                $group_post->photo_ids = serialize($request->gourp_post_photo_id);
                $group_post->save();
            }else {
                $group_post->photo_ids = "";
                $group_post->save();
            }

            if ($request->post_giphy) {
                $group_post->giphys = serialize($request->post_giphy);
                $group_post->save();
            } else {
                $group_post->giphys = "";
                $group_post->save();
            }

            if ($request->post_youtube) {
                $group_post->youtubes = serialize($request->post_youtube);
                $group_post->save();
            } else {
                $group_post->youtubes = "";
                $group_post->save();
            }
            event(new EditGroupPost($group->id, $group_post->id, $group_post->user_id));
            return "success";
        }
        return "fail";
    }

    public function group_post_destroy($id) {
        $group_post = IoGroupPost::find($id);
        if ($group_post) {
            if ($group_post->poll == 1) {
                $group_poll = IoGroupPoll::find($group_post->poll_id);
                $pollanswers = IoGroupPollAnswer::where('pollid', $id)->delete();
                $pollvotes = IoGroupPollVote::where('pollid', $id)->delete();
                $group_poll->delete();
            }
            $group_post_comments = IogGroupComment::where('post_id', $id)->delete();
            event(new DeleteGroupPost($group_post->group_id, $group_post->id, $group_post->user_id));
            $group_post->delete();

            return "success";
        }

        return "fail";
    }

    public function get_just_post($id) {
        $group_post = IoGroupPost::join('users', 'users.id', '=', 'io_group_posts.user_id')
        ->select('io_group_posts.*', 'users.username as poster_name', 'users.avatar as poster_avatar')->find($id);
        if ($group_post) {
            $view = view('module.groupSinglePost',compact('group_post'))->render();
            return response()->json(['html'=>$view]);
        }
    }

    public function get_just_post_edited($id) {
        $group_post = IoGroupPost::join('users', 'users.id', '=', 'io_group_posts.user_id')
        ->select('io_group_posts.*', 'users.username as poster_name', 'users.avatar as poster_avatar')->find($id);
        if ($group_post) {
            $view = view('module.groupSinglePostEdit',compact('group_post'))->render();
            return response()->json(['html'=>$view]);
        }
    }

    public function group_post_get($id) {
        $group_post = IoGroupPost::find($id);
        if ($group_post && $group_post->user_id == Auth::user()->id) {
            $group = IoGroup::find($group_post->group_id);

            $photo_datas = array();
            if ($group_post->photo_ids) {
                $post_photos = unserialize($group_post->photo_ids);
                foreach ($post_photos as $post_photo) {
                    $posted_photo = IoGroupPhoto::find($post_photo);
                    $photo_url = asset('assets/images/group/'.$group->url.'/'.$posted_photo->photo);
                    $photo_datas[] = array('photo_id' => $posted_photo->id, 'photo_url' => $photo_url);
                }
            }
            $giphy_datas = array();
            if ($group_post->giphys) {
                $giphy_datas = unserialize($group_post->giphys);
            }
            $youtube_datas = array();
            if ($group_post->youtubes) {
                $youtube_datas = unserialize($group_post->youtubes);
            }
            $final_data = array('post_id' => $group_post->id, 'post_text' => $group_post->status, 'photo_datas' => $photo_datas, 'giphy_datas' => $giphy_datas, 'youtube_datas' => $youtube_datas);
            return $final_data;
        }

        return "fail";
    }
}
