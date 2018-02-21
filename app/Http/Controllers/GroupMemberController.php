<?php

namespace App\Http\Controllers;

use Auth;
use Alert;
use Validator;
use App\User;
use App\Slim;
use App\IoGroup;
use App\IoGroupUser;
use App\IoGroupPost;
use App\IoGroupPhoto;
use App\IoGroupRequest;
use App\IogGroupComment;
use Illuminate\Http\Request;

class GroupMemberController extends Controller
{
    public function make_admin_user($group_id, $user_id) {
        $group = IoGroup::find($group_id);
        $check_my_level = IoGroupUser::where('group_id', $group_id)->where('user_id', Auth::user()->id)->first();
        if ($group && ($group->user_id == Auth::user()->id || $check_my_level->user_level == 1)) {
            $group_user = IoGroupUser::where('group_id', $group_id)->where('user_id', $user_id)->first();
            if ($group_user) {
                $group_user->user_level = 1;
                $group_user->save();
                return back();
            }
        }
        Alert::error('Permission denied.', 'Faild')->autoclose(3000);
        return back();
    }

    public function remove_as_admin_user($group_id, $user_id) {
        $group = IoGroup::find($group_id);
        if ($group && $group->user_id == Auth::user()->id) {
            $group_user = IoGroupUser::where('group_id', $group_id)->where('user_id', $user_id)->first();
            if ($group_user) {
                $group_user->user_level = 0;
                $group_user->save();
                return back();
            }
        }
        Alert::error('Permission denied.', 'Faild')->autoclose(3000);
        return back();
    }

    public function make_admin_request($group_id, $user_id) {
        $group = IoGroup::find($group_id);
        $check_my_level = IoGroupUser::where('group_id', $group_id)->where('user_id', Auth::user()->id)->first();
        if ($group && ($group->user_id == Auth::user()->id || $check_my_level->user_level == 1)) {
            $group_user_request = IoGroupRequest::where('group_id', $group_id)->where('user_id', $user_id)->first();
            if ($group_user_request) {
                $check_alreay_member = IoGroupUser::where('group_id', $group_id)->where('user_id', $user_id)->count();
                if ($check_alreay_member == 0) {
                    $group_user = new IoGroupUser;
                    $group_user->group_id = $group_id;
                    $group_user->user_id = $user_id;
                    $group_user->user_level = 1;
                    $group_user->save();
                }else {
                    $alreay_member = IoGroupUser::where('group_id', $group_id)->where('user_id', $user_id)->first();
                    $alreay_member->user_level = 1;
                    $alreay_member->save();
                }
                $group_user_request->delete();
                return back();
            }
        }
        Alert::error('Permission denied.', 'Faild')->autoclose(3000);
        return back();
    }

    public function remove_group_member(Request $request) {
        // dd($request->all());
        $group = IoGroup::find($request->group_id);
        if ($group) {
            $group_admin_check = IoGroupUser::where('group_id', $group->id)->where('user_id', Auth::user()->id)->where('user_level', 1)->count();
            if ($group->user_id == Auth::user()->id || $group_admin_check > 0) {
                $remove_user_level = $request->remove_member_level;
                if ($remove_user_level == 0) {
                    $remove_user = IoGroupUser::where('group_id', $group->id)->where('user_id', $request->remove_user_id)->first();
                    if ($remove_user) {
                        if ($request->post_delete) {
                            IoGroupPost::where('group_id', $group->id)->where('user_id', $request->remove_user_id)->delete();
                        }
                        if ($request->comment_delete) {
                            IogGroupComment::where('group_id', $group->id)->where('user_id', $request->remove_user_id)->delete();
                        }

                        if ($request->block_member) {
                            $remove_user->user_level = 2;
                            $remove_user->save();
                        } else {
                            $remove_user->delete();
                        }
                    }
                    return back();
                } elseif ($remove_user_level == 3) {
                    $remove_request = IoGroupRequest::where('group_id', $group->id)->where('user_id', $request->remove_user_id)->where('method', 1)->first();
                    if ($remove_request) {
                        if ($request->block_member) {
                            $remove_request_block = new IoGroupUser;
                            $remove_request_block->group_id = $group->id;
                            $remove_request_block->user_id = $request->remove_user_id;
                            $remove_request_block->user_level = 2;
                            $remove_request_block->save();
                        } else {
                            $remove_request->delete();
                        }
                    }
                    return back();
                }elseif ($remove_user_level == 1) {
                    if ($group->user_id == Auth::user()->id) {
                        $remove_user = IoGroupUser::where('group_id', $group->id)->where('user_id', $request->remove_user_id)->where('user_level', 1)->first();
                        if ($remove_user) {
                            if ($request->post_delete) {
                                IoGroupPost::where('group_id', $group->id)->where('user_id', $request->remove_user_id)->delete();
                            }
                            if ($request->comment_delete) {
                                IogGroupComment::where('group_id', $group->id)->where('user_id', $request->remove_user_id)->delete();
                            }
                            if ($request->block_member) {
                                $remove_user->user_level = 2;
                                $remove_user->save();
                            } else {
                                $remove_user->delete();
                            }
                        }
                        return back();
                    }else {
                        Alert::error('Permission denied.', 'Faild')->autoclose(3000);
                        return back();
                    }
                }
            }else {
                Alert::error('Permission denied.', 'Faild')->autoclose(3000);
                return back();
            }
        }
    }

    public function remove_group_blocked_member($group_id, $user_id) {
        $group = IoGroup::find($group_id);
        $check_my_level = IoGroupUser::where('group_id', $group_id)->where('user_id', Auth::user()->id)->first();
        if ($group && ($group->user_id == Auth::user()->id || $check_my_level->user_level == 1)) {
            $group_user = IoGroupUser::where('group_id', $group_id)->where('user_id', $user_id)->where('user_level', 2)->first();
            if ($group_user) {
                $group_user->delete();
                $check_blocked_user = IoGroupUser::where('group_id', $group_id)->where('user_level', 2)->count();
                if ($check_blocked_user > 0) {
                    return back();
                }else {
                    return redirect('group/view/'.$group->url.'/members');
                }
            }
        }
        Alert::error('Permission denied.', 'Faild')->autoclose(3000);
        return back();
    }
}
