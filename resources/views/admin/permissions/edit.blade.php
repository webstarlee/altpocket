@extends('layouts.admin');

@section('title', 'Admin | List Permissions')


@section('header')
  <div class="m-subheader " style="height:100px;padding:30px 30px 0 30px;">
    <div class="d-flex align-items-center">
      <div class="mr-auto">
        <h3 class="m-subheader__title m-subheader__title--separator">
          Edit Permission
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
            <a href="/admin/permissions" class="m-nav__link">
              <span class="m-nav__link-text">
                Permissions
              </span>
            </a>
          </li>
          <li class="m-nav__separator">
            -
          </li>
          <li class="m-nav__item">
            <a href="" class="m-nav__link">
              <span class="m-nav__link-text">
                Create Permission
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
                        Permission Actions
                      </span>
                    </li>
                    <li class="m-nav__item">
                      <a href="/admin/permissions/create" class="m-nav__link">
                        <i class="m-nav__link-icon la la-plus-square"></i>
                        <span class="m-nav__link-text">
                          Create Permission
                        </span>
                      </a>
                    </li>
                    <li class="m-nav__item">
                      <a href="/admin/permissions/assign" class="m-nav__link">
                        <i class="m-nav__link-icon la la-user-plus"></i>
                        <span class="m-nav__link-text">
                          Assign Permission
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
								Edit Permission
							</h3>
						</div>
					</div>
				</div>
				<!--begin::Form-->
				<form class="m-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed" role="form" method="post" action="">
          {{csrf_field()}}
					<div class="m-portlet__body">
						<div class="form-group m-form__group row">
							<div class="col-lg-6">
								<label>
									Name
								</label>
								<input type="text" class="form-control m-input" placeholder="Enter a name" name="name" value="{{$permission->name}}" required>
								<span class="m-form__help">
									Permission name identifier, usually lowercase, please refrain from using spaces and use _ as separator.
								</span>
							</div>
							<div class="col-lg-6">
								<label class="">
									Title
								</label>
								<input type="text" class="form-control m-input" placeholder="Enter a title" value="{{$permission->title}}" name="title" required>
								<span class="m-form__help">
									Permission title, this is the display name of the permission.
								</span>
							</div>
						</div>
						<div class="form-group m-form__group row">
							<div class="col-lg-6">
								<label>
									Description:
								</label>
								<div class="m-input-icon m-input-icon--right">
									<input type="text" class="form-control m-input" placeholder="Enter a description" value="{{$permission->description}}" name="description" required>
								</div>
								<span class="m-form__help">
									Permission description, used to describe the permission.
								</span>
							</div>
              <div class="col-lg-6">
                <label>
                  Permission Type:
                </label>
                <div class="m-radio-inline">
                  <label class="m-radio m-radio--solid">
                    <input type="radio" name="type" @if($permission->type == "Default") checked @endif @if($permission->type == "") checked @endif value="Default">
                    Default
                    <span></span>
                  </label>
                  <label class="m-radio m-radio--solid">
                    <input type="radio" name="type" @if($permission->type == "Rank") checked @endif value="Rank">
                    Rank
                    <span></span>
                  </label>
                  <label class="m-radio m-radio--solid">
                    <input type="radio" name="type" @if($permission->type == "Staff") checked @endif value="Staff">
                    Staff
                    <span></span>
                  </label>
                  <label class="m-radio m-radio--solid">
                    <input type="radio" name="type" @if($permission->type == "Donator") checked @endif value="Donator">
                    Donator
                    <span></span>
                  </label>
                </div>
                <span class="m-form__help">
                  Please select permission group
                </span>
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
