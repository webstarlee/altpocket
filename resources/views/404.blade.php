@extends('layouts.app')

@section('title')
Private Profile
@endsection


@section('content')
    <div id="content_wrapper" class="">
        <div id="content" class="container-fluid">
            <div class="content-body">
                <div id="card_content" class="tab-content" style="text-align:center;">
                    <div class="row">
                  <div class="col-xs-12">
                    <div class="card">
                      <header class="card-heading">
                        <h2 class="card-title">No user found!</h2>
                          <hr>
                      </header>
                      <div class="card-body">
                          <p>There was no user found with that username.</p>
                        </div>
                      </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>


@endsection