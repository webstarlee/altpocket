<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Award;
class AwardController extends Controller
{
  public function index()
  {
    return view('admin.awards.index');
  }

  public function get(Request $request) {

    $query = $request->query();
    $page = $query['datatable']['pagination']['page'];
    $perpage = $query['datatable']['pagination']['perpage'];
    $sort = $query['datatable']['sort']['sort'] ? : "asc";
    $field = $query['datatable']['sort']['field'] ? : "id";
    $search = $query['datatable']['query']['generalSearch'] ? : "";

    $roles = Award::where('name', 'like', "%$search%")->orWhere('description', 'like', "%$search%")->orderBy($field, $sort)->paginate($perpage, ['*'], 'page', $page);

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
}
