@extends('layouts.app')


@section('content')
<div class="row">
                <div class="col-xs-12">
                  <div class="card card-data-tables">
                      <br>
                        <div class="alert alert-warning" role="alert">
                                <strong>Note:</strong> People who are not in the verified group may have untrue values.
                              </div>
                    <header class="card-heading">
                      <small class="dataTables_info">
                        </small>
                      
                      <div class="card-search">
                        <div id="productsTable_wrapper" class="form-group label-floating is-empty">
                          <i class="zmdi zmdi-search search-icon-left"></i>
                          <input type="text" class="form-control filter-input" placeholder="Filter Products..." autocomplete="off">
                          <a href="javascript:void(0)" class="close-search" data-card-search="close" data-toggle="tooltip" data-placement="top" title="Close"><i class="zmdi zmdi-close"></i></a>
                        </div>
                      </div>
                      <ul class="card-actions icons right-top">
                        <li id="deleteItems" style="display: none;">
                          <span class="label label-info pull-left m-t-5 m-r-10 text-white"></span>
                          <a href="javascript:void(0)" id="confirmDelete" data-toggle="tooltip" data-placement="top" data-original-title="Delete Product(s)">
                            <i class="zmdi zmdi-delete"></i>
                          </a>
                        </li>
                        <li>
                          <a href="javascript:void(0)" data-card-search="open" data-toggle="tooltip" data-placement="top" data-original-title="Filter Products">
                            <i class="zmdi zmdi-filter-list"></i>
                          </a>
                        </li>
                        <li class="dropdown" data-toggle="tooltip" data-placement="top" data-original-title="Show Entries">
                          <a href="javascript:void(0)" data-toggle="dropdown">
                            <i class="zmdi zmdi-more-vert"></i>
                          </a>
                          <div id="dataTablesLength">
                          </div>
                        </li>
                      </ul>
                    </header>
                    <div class="card-body p-0">
                      <div class="alert alert-info m-20 hidden-md hidden-lg" role="alert">
                        <p>
                          Heads up! You can Swipe table Left to Right on Mobile devices.
                        </p>
                      </div>
                      <div class="table-responsive">
                        <table id="asdf" class="mdl-data-table product-table m-t-30" cellspacing="0" width="100%">
                          <thead>
                            <tr>
                              <th class="col-xs-2">Username</th>
                              <th class="col-xs-2">Invested</th>
                              <th class="col-xs-2">Profit</th>
                            </tr>
                          </thead>
                          <tbody>
                              
                              
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
@stop



@push('scripts')
<script>
$(function() {
$('#asdf').DataTable({
processing: true,
serverSide: true,
ajax: '/data',
columns: [
{ data: 'username', name: 'username'},
{ data: 'invested', name: 'invested'},
{ data: 'profit', name: 'profit'},
]
});
});
</script>
@endpush
           