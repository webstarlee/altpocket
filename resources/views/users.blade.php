@extends('layouts.app')

@section('title')
Users
@endsection

@section('content')
<link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
    <div id="content_wrapper" class="">
    <div id="header_wrapper" class="header-sm">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12">
                    <header id="header">
                        <h1>Altpocket Users</h1>
                    </header>
                </div>
            </div>
        </div>
    </div>
</div>
          <div id="content" class="container-fluid">
            <div class="content-body">
            @include('module.users')  
              
              
              </div>
            </div>
        
<div>
    
    
    
    @stack('scripts')
<script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
@endsection