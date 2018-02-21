@extends('layouts.app2')

@section('title')
New Gamble test
@endsection
@section('css')
<link href="/css/blocks.css" rel="stylesheet">
@endsection

@section('content')
    <div id="content_wrapper" class="">
        <div id="content" class="container-fluid">
            <div class="content-body">
                <div id="card_content" class="tab-content" style="text-align:center;">
                    <div class="row">
                  <div class="col-md-6 col-md-offset-3">
                    <div class="ui-block">
										<div class="ui-block-content">
                      <form class="form-horizontal" role="form" method="POST" action="/gamble/new" id="makenewgamble">
                        {{csrf_field()}}
                        <div class="col-xl-12 col-lg-12 col-md-12">
        									<div class="form-group label-floating is-empty">
        										<label class="control-label">ETH Amount</label>
        										<input class="form-control" placeholder="" type="text" name="amount">
        									</div>
        								</div>
                        <button type="submit" class="btn btn-lg full-width" style="color:white!important;background-color:#3a94ff">Create New Session</button>
                      </form>
										</div>
									</div>
                  </div>
                    </div>
                </div>
            </div>
        </div>
</div>


@endsection
