<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // View users
    public function index()
    {
      return view('admin.users.index');
    }
    // Get users
    public function get(Request $request) {

      $query = $request->query();
      $page = $query['datatable']['pagination']['page'];
      $perpage = $query['datatable']['pagination']['perpage'];
      $sort = $query['datatable']['sort']['sort'] ? : "asc";
      $field = $query['datatable']['sort']['field'] ? : "id";
      $search = $query['datatable']['query']['generalSearch'] ? : "";

      $selects = 'id, username, avatar, primary_role';

      $users = User::where('username', 'like', "%$search%")->orWhere('email', 'like', "%$search%")->selectRaw($selects)->orderBy($field, $sort)->paginate($perpage, ['*'], 'page', $page);

      foreach($users->getCollection() as $user)
      {
        if($user->primary_role != "")
        {
          $user->display_role = Role::findByName($user->primary_role);
        } else {
          $user->display_role = "";
        }
      }

      $meta = array(
        'page'    => $users->currentPage(),
        'pages'   => $users->lastPage(),
        'perpage' => $users->perPage(),
        'total'   => $users->total(),
      );


      $result = array(
        'meta' => $meta + array(
            'sort'  => 'asc',
            'field' => 'id',
          ),
        'data' => $users->getCollection()
      );

      return $result;
    }
}
