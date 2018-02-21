<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Auth;

class RoleController extends Controller
{
  // setup
  public function initiateRoles()
  {
    $user = Auth::user();
    if(!$user->hasRole('staff'))
    {
    $user->assignRole('staff');
  } else {
    echo "welcome to staff";
  }
  }
}
