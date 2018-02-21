@extends('layouts.admin');

@section('title', 'Admin | Create Role')

@section('css')
<link type="text/css" href="/css/slim.min.css" rel="stylesheet" type="text/css">
<style>
.slim {
  width: 128px;
  height:128px;
  margin:0 auto;
}
</style>
@endsection

@section('header')
  <div class="m-subheader " style="height:100px;padding:30px 30px 0 30px;">
    <div class="d-flex align-items-center">
      <div class="mr-auto">
        <h3 class="m-subheader__title m-subheader__title--separator">
          Create Role
        </h3>
        <ul class="m-subheader__breadcrumbs m-nav m-nav--inline">
          <li class="m-nav__item m-nav__item--home">
            <a href="/admin" class="m-nav__link m-nav__link--icon">
              <i class="m-nav__link-icon la la-home"></i>
            </a>
          </li>
          <li class="m-nav__separator">
            -
          </li>
          <li class="m-nav__item">
            <a href="/admin/roles" class="m-nav__link">
              <span class="m-nav__link-text">
                Roles
              </span>
            </a>
          </li>
          <li class="m-nav__separator">
            -
          </li>
          <li class="m-nav__item">
            <a href="" class="m-nav__link">
              <span class="m-nav__link-text">
                Create Role
              </span>
            </a>
          </li>
        </ul>
      </div>
      <div>
        <div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover" aria-expanded="true">
          <a href="#" class="m-portlet__nav-link btn btn-lg btn-secondary  m-btn m-btn--outline-2x m-btn--air m-btn--icon m-btn--icon-only m-btn--pill  m-dropdown__toggle">
            <i class="la la-plus m--hide"></i>
            <i class="la la-ellipsis-h"></i>
          </a>
          <div class="m-dropdown__wrapper">
            <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
            <div class="m-dropdown__inner">
              <div class="m-dropdown__body">
                <div class="m-dropdown__content">
                  <ul class="m-nav">
                    <li class="m-nav__section m-nav__section--first m--hide">
                      <span class="m-nav__section-text">
                        Role Actions
                      </span>
                    </li>
                    <li class="m-nav__item">
                      <a href="create" class="m-nav__link">
                        <i class="m-nav__link-icon la la-plus-square"></i>
                        <span class="m-nav__link-text">
                          Create Role
                        </span>
                      </a>
                    </li>
                    <li class="m-nav__item">
                      <a href="assign" class="m-nav__link">
                        <i class="m-nav__link-icon la la-user-plus"></i>
                        <span class="m-nav__link-text">
                          Assign Role
                        </span>
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection


@section('content')
  <div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<span class="m-portlet__head-icon m--hide">
								<i class="la la-gear"></i>
							</span>
							<h3 class="m-portlet__head-text">
								Create a new role
							</h3>
						</div>
					</div>
				</div>
				<!--begin::Form-->
				<form class="m-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed" role="form" method="post" action="" enctype="multipart/form-data">
          {{csrf_field()}}
					<div class="m-portlet__body">
						<div class="form-group m-form__group row">
							<div class="col-lg-6">
								<label>
									Name
								</label>
								<input type="text" class="form-control m-input" placeholder="Enter a name" name="name" required>
								<span class="m-form__help">
									Role name identifier, usually lowercase.
								</span>
							</div>
							<div class="col-lg-6">
								<label class="">
									Title
								</label>
								<input type="text" class="form-control m-input" placeholder="Enter a title" name="title" required>
								<span class="m-form__help">
									Role title, this is the display name of the role.
								</span>
							</div>
						</div>
						<div class="form-group m-form__group row">
							<div class="col-lg-6">
								<label>
									Description:
								</label>
								<div class="m-input-icon m-input-icon--right">
									<input type="text" class="form-control m-input" placeholder="Enter a description" name="description">
								</div>
								<span class="m-form__help">
									Role description, used to describe the role.
								</span>
							</div>
							<div class="col-lg-6">
								<label class="">
									Color:
								</label>
								<div class="m-input-icon m-input-icon--right">
                  <input class="form-control m-input" type="color" value="#ffffff" id="example-color-input" style="height: 37px;" name="color">
								</div>
								<span class="m-form__help">
									Role color, used to style the title and users name, leave white to use default.
								</span>
							</div>
						</div>
            <div class="form-group m-form__group row">
							<div class="col-lg-6">
								<label>
									Role Type:
								</label>
								<div class="m-radio-inline">
                  <label class="m-radio m-radio--solid">
                    <input type="radio" name="type" checked value="Default">
                    Default
                    <span></span>
                  </label>
                  <label class="m-radio m-radio--solid">
                    <input type="radio" name="type" value="Rank">
                    Rank
                    <span></span>
                  </label>
                  <label class="m-radio m-radio--solid">
										<input type="radio" name="type" value="Staff">
										Staff
										<span></span>
									</label>
									<label class="m-radio m-radio--solid">
										<input type="radio" name="type" value="Donator">
										Donator
										<span></span>
									</label>
								</div>
								<span class="m-form__help">
									Please select role group
								</span>
                <br><br>
                <label>
                  Styling:
                </label>
                <div class="m-input-icon m-input-icon--right">
                  <input type="text" name="style" class="form-control m-input" placeholder="font-weight:600;...">
                </div>
                <span class="m-form__help">
                  Role styling, used to style the title and users name.
                </span>
                <br><br>
                <label>
                  Inherit Emblem:
                </label>
                <div>
                  <select class="form-control m-bootstrap-select m_selectpicker" name="emblem" style="padding-right:0px!important;">
                    <option value="">Optional</option>
                    @foreach($emblems as $emblem)
                      <option data-content="<img src='/awards/{{$emblem->emblem}}'> {{$emblem->title}}" value="{{$emblem->emblem}}">
                      {{$emblem->title}}
                      </option>
                    @endforeach
                  </select>
                </div>
                <span class="m-form__help">
                  Use this if you do not wish to upload an new emblem.
                </span>
							</div>
              <div class="col-lg-6">
                <label>
                  Upload Emblem
                </label>
                <div class="slim" data-min-size="24,24" data-size="24,24" data-forze-size="24,24" data-label=" " data-edit="false">
                    <input type="file"/>
                </div>
                <span class="m-form__help">
                  Upload an emblem for this specific role, this is optional. When uploaded resolution will be fixed.
                </span>
              </div>
              </div>
						</div>
            <div class="form-group m-form__group row">
              <span class="m-section__sub">
									Role Permissions
								</span>
              <div class="col-lg-12">
								<div class="m-form__group form-group row">
                @foreach($permissions as $perm)
                  <label class="col-3 col-form-label" style="text-align:left;max-width:10%">
                    {{ucwords($perm->title)}}
                  </label>
                  <div class="col-3">
                    <span class="m-switch">
                      <label>
                        <input type="checkbox" name="permissions[]" value="{{$perm->name}}">
                        <span></span>
                      </label>
                    </span>
                  </div>
                @endforeach
              </div>
              </div>
            </div>
					</div>
					<div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
						<div class="m-form__actions m-form__actions--solid">
							<div class="row">
								<div class="col-lg-6">
									<button type="submit" class="btn btn-primary">
										Create
									</button>
								</div>
							</div>
						</div>
					</div>
				</form>
				<!--end::Form-->
			</div>
    </div>
  </div>
@endsection


@section('js')
   <script src="/js/slim.kickstart.min.js" type="text/javascript"></script>
   <script src="/panel/assets/demo/default/custom/components/forms/widgets/bootstrap-select.js" type="text/javascript"></script>
@endsection
