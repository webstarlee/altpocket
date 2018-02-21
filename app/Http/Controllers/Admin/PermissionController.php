<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Alert;
use Redirect;

class PermissionController extends Controller
{
    public function index()
    {
      return view('admin.permissions.index');
    }
    // Get permissions
    public function get(Request $request) {

      $query = $request->query();
      $page = $query['datatable']['pagination']['page'];
      $perpage = $query['datatable']['pagination']['perpage'];
      $sort = $query['datatable']['sort']['sort'] ? : "asc";
      $field = $query['datatable']['sort']['field'] ? : "id";
      $search = $query['datatable']['query']['generalSearch'] ? : "";

      $permissions = Permission::where('name', 'like', "%$search%")->orWhere('type', 'like', "%$search%")->orWhere('description', 'like', "%$search%")->orderBy($field, $sort)->paginate($perpage, ['*'], 'page', $page);

      $meta = array(
        'page'    => $permissions->currentPage(),
        'pages'   => $permissions->lastPage(),
        'perpage' => $permissions->perPage(),
        'total'   => $permissions->total(),
      );


      $result = array(
        'meta' => $meta + array(
            'sort'  => 'asc',
            'field' => 'id',
          ),
        'data' => $permissions->getCollection()
      );

      return $result;
    }

    public function edit($name)
    {
      $permission = Permission::findByName($name);
      return view('admin.permissions.edit', ['permission' => $permission]);
    }

    public function update(Request $request, $name)
    {
      // Check if role exists
      if(Permission::where('name', $request->get('name'))->first())
      {
        $permission = Permission::where('name', $request->get('name'))->first();
      } else {
        Alert::error('We could not find a permission by that name.', 'Oops..')->persistent('Okay');
        return Redirect::back();
      }

      $permission->title = $request->get('title');
      $permission->description = $request->get('description');
      $permission->type = $request->get('type');
      $permission->save();

      Alert::success('The permission has been created!', 'Success');
      return redirect('/admin/permissions');

    }


    // Show create page
    public function create()
    {
      return view('admin.permissions.create');
    }

    // Create new permission
    public function new(Request $request)
    {
      // Check if role exists
      if(!Permission::where('name', $request->get('name'))->first())
      {
        $permission = Permission::create(['name' => $request->get('name')]);
      } else {
        Alert::error('A permission with that name identifier already exists.', 'Oops..')->persistent('Okay');
        return Redirect::back();
      }

      $permission->title = $request->get('title');
      $permission->description = $request->get('description');
      $permission->type = $request->get('type');
      $permission->save();

      Alert::success('The permission has been created!', 'Success');
      return redirect('/admin/permissions');

    }
    // Remove Permission
    public function delete($name)
    {
      if(Permission::findByName($name)) {
        $permission = Permission::findByName($name);
      } else {
        Alert::error('We could not find a permission by that name.', 'Oops..')->persistent('Okay');
        return Redirect::back();
      }

      // Get users with the permission and remove it from their assigned roles.
      $users = $permission->users();

      foreach($users as $user)
      {
        $user->revokePermissionTo($permission);
      }

      $roles = $permission->roles();

      foreach($roles as $role)
      {
        $role->revokePermissionTo($permission);
      }

      $permission->delete();
      return Redirect::back();
    }
}
