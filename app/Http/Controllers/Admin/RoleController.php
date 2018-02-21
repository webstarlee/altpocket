<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Slim;
use Redirect;
use Alert;
class RoleController extends Controller
{
    public function index()
    {
      return view('admin.roles.index');
    }


    // Basic GET for all different tables
    public function get(Request $request) {

      $query = $request->query();
      $page = $query['datatable']['pagination']['page'];
      $perpage = $query['datatable']['pagination']['perpage'];
      $sort = $query['datatable']['sort']['sort'] ? : "asc";
      $field = $query['datatable']['sort']['field'] ? : "id";
      $search = $query['datatable']['query']['generalSearch'] ? : "";

      $roles = Role::where('name', 'like', "%$search%")->orWhere('title', 'like', "%$search%")->orWhere('type', 'like', "%$search%")->orWhere('description', 'like', "%$search%")->orderBy($field, $sort)->paginate($perpage, ['*'], 'page', $page);

      $meta = array(
      	'page'    => $roles->currentPage(),
      	'pages'   => $roles->lastPage(),
      	'perpage' => $roles->perPage(),
      	'total'   => $roles->total(),
      );


      $result = array(
      	'meta' => $meta + array(
      			'sort'  => 'asc',
      			'field' => 'id',
      		),
      	'data' => $roles->getCollection()
      );

      return $result;
    }


    // Show create page
    public function create()
    {
      $permissions = Permission::all();
      $emblems = Role::where('emblem', '!=', '')->selectRaw('title, emblem')->get();
      return view('admin.roles.create', ['permissions' => $permissions, 'emblems' => $emblems]);
    }

    public function edit($name)
    {
      $role = Role::findByName($name);
      $permissions = Permission::all();
      $emblems = Role::where('emblem', '!=', '')->selectRaw('title, emblem')->get();
      return view('admin.roles.edit', ['permissions' => $permissions, 'emblems' => $emblems, 'role' => $role]);
    }

    // Create new role
    public function new(Request $request)
    {
      // Check if role exists
      if(!Role::where('name', $request->get('name'))->first())
      {
        $role = Role::create(['name' => $request->get('name')]);
      } else {
        Alert::error('A role with that name identifier already exists.', 'Oops..')->persistent('Okay');
        return Redirect::back();
      }

      $role->title = $request->get('title');
      $role->description = $request->get('description');
      if($request->color != "#ffffff")
      {
        $role->color = $request->get('color');
      }
      $role->type = $request->get('type');
      $role->style = $request->get('style');

      $role->emblem = $request->get('emblem');

      // Handle Emblem

      $images = Slim::getImages();

      // No image found under the supplied input name
      if ($images == false) {
      }
      else {
          foreach ($images as $image) {

              $files = array();

              // save output data if set
              if (isset($image['output']['data'])) {

                  // Save the file
                  $name = rand();

                  // We'll use the output crop data
                  $data = $image['output']['data'];

                  // If you want to store the file in another directory pass the directory name as the third parameter.
                  // $file = Slim::saveFile($data, $name, 'my-directory/');

                  // If you want to prevent Slim from adding a unique id to the file name add false as the fourth parameter.
                  // $file = Slim::saveFile($data, $name, 'tmp/', false);
                  $output = Slim::saveFile($data, $name, 'awards/', false);
                  $role->emblem = $name;
                  array_push($files, $output);
              }

              // save input data if set
              if (isset ($image['input']['data'])) {

                  // Save the file
                  $name = rand();

                  // We'll use the output crop data
                  $data = $image['input']['data'];

                  // If you want to store the file in another directory pass the directory name as the third parameter.
                  // $file = Slim::saveFile($data, $name, 'my-directory/');

                  // If you want to prevent Slim from adding a unique id to the file name add false as the fourth parameter.
                  // $file = Slim::saveFile($data, $name, 'tmp/', false);
                  $input = Slim::saveFile($data, $name, 'awards/', false);
                  $role->emblem = $name;
                  array_push($files, $input);
              }


          }
      }
      $role->save();
      if($request->input('permissions'))
      {
        foreach($request->input('permissions') as $perm)
        {
          if(!$role->hasPermissionTo($perm))
          {
            $role->givePermissionTo($perm);
          }
        }
      }

      Alert::success('The role has been created!', 'Success');
      return redirect('/admin/roles');
    }


    public function update(Request $request, $name)
    {
      if(Role::findByName($name)) {
        $role = Role::where('name', $name)->first();
      } else {
        Alert::error('We could not find a role by that name.', 'Oops..')->persistent('Okay');
        return Redirect::back();
      }

      $role->title = $request->get('title');
      $role->description = $request->get('description');
      if($request->color != "#ffffff")
      {
        $role->color = $request->get('color');
      }
      $role->type = $request->get('type');
      $role->style = $request->get('style');

      $role->emblem = $request->get('emblem');

      // Handle Emblem

      $images = Slim::getImages();

      // No image found under the supplied input name
      if ($images == false) {
      }
      else {
          foreach ($images as $image) {

              $files = array();

              // save output data if set
              if (isset($image['output']['data'])) {

                  // Save the file
                  $name = rand() . $image['input']['name'];

                  // We'll use the output crop data
                  $data = $image['output']['data'];

                  // If you want to store the file in another directory pass the directory name as the third parameter.
                  // $file = Slim::saveFile($data, $name, 'my-directory/');

                  // If you want to prevent Slim from adding a unique id to the file name add false as the fourth parameter.
                  // $file = Slim::saveFile($data, $name, 'tmp/', false);
                  $output = Slim::saveFile($data, $name, 'awards/', false);
                  $role->emblem = $name;
                  array_push($files, $output);
              }

              // save input data if set
              if (isset ($image['input']['data'])) {

                  // Save the file
                  $name = rand() . $image['input']['name'];

                  // We'll use the output crop data
                  $data = $image['input']['data'];

                  // If you want to store the file in another directory pass the directory name as the third parameter.
                  // $file = Slim::saveFile($data, $name, 'my-directory/');

                  // If you want to prevent Slim from adding a unique id to the file name add false as the fourth parameter.
                  // $file = Slim::saveFile($data, $name, 'tmp/', false);
                  $input = Slim::saveFile($data, $name, 'awards/', false);
                  $role->emblem = $name;
                  array_push($files, $input);
              }


          }
      }
      $role->save();
      if($request->input('permissions'))
      {
        foreach($request->input('permissions') as $perm)
        {
          if(!$role->hasPermissionTo($perm))
          {
            $role->givePermissionTo($perm);
          }
        }
      }

      Alert::success('The role has been updated!', 'Success');
      return redirect('/admin/roles');
    }

    // Delete Role
    public function delete($name)
    {
      if(Role::findByName($name)) {
        $role = Role::findByName($name);
      } else {
        Alert::error('We could not find a role by that name.', 'Oops..')->persistent('Okay');
        return Redirect::back();
      }

      // Get users with the role and remove it from their assigned roles.
      $users = $role->users();

      foreach($users as $user)
      {
        $user->removeRole($role);
      }

      $role->delete();
      return Redirect::back();
    }

    public function uploadEmblem(Request $request)
    {
      $images = Slim::getImages();

      // No image found under the supplied input name
      if ($images == false) {

          // inject your own auto crop or fallback script here
          Alert::error('Could not find emblem.', 'Failed.');
          return Redirect::back();

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
                  $output = Slim::saveFile($data, $name, 'awards/', false);
                  return $name;
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
                  $input = Slim::saveFile($data, $name, 'awards/', false);
                  return $name;
                  array_push($files, $input);
              }


          }
      }
    }
}
