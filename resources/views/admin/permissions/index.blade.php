@extends('layouts.admin');

@section('title', 'Admin | List Permissions')


@section('header')
  <div class="m-subheader " style="height:100px;padding:30px 30px 0 30px;">
    <div class="d-flex align-items-center">
      <div class="mr-auto">
        <h3 class="m-subheader__title m-subheader__title--separator">
          Permission Management
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
            <a href="javascript:void(0)" class="m-nav__link">
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
                List Permissions
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
  <div class="m-portlet m-portlet--mobile">
		<div class="m-portlet__head">
			<div class="m-portlet__head-caption">
				<div class="m-portlet__head-title">
					<h3 class="m-portlet__head-text">
						List Permissions
						<small>
							All available permissions on Altpocket.
						</small>
					</h3>
				</div>
			</div>
		</div>
		<div class="m-portlet__body">
			<!--begin: Search Form -->
			<div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
				<div class="row align-items-center">
					<div class="col-xl-8 order-2 order-xl-1">
						<div class="form-group m-form__group row align-items-center">

							<div class="col-md-4">
								<div class="m-input-icon m-input-icon--left">
									<input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch">
									<span class="m-input-icon__icon m-input-icon__icon--left">
										<span>
											<i class="la la-search"></i>
										</span>
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-4 order-1 order-xl-2 m--align-right">
						<a href="/admin/permissions/create" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill">
							<span>
								<i class="la la-plus-square"></i>
								<span>
									New Permission
								</span>
							</span>
						</a>
						<div class="m-separator m-separator--dashed d-xl-none"></div>
					</div>
				</div>
			</div>
			<!--end: Search Form -->
<!--begin: Datatable -->
			<div class="m_datatable" id="ajax_data"></div>
			<!--end: Datatable -->
		</div>
  </div>
@endsection

@section('js')
		<script src="/panel/assets/demo/default/custom/components/datatables/base/data-ajax-permissions.js?version=11111" type="text/javascript"></script>

    <script>

    $('.m_datatable')
  .on('m-datatable--on-init', function () {
    $(".delete-permission").click(function(){
      var id = $(this).attr('id');
      swal({
        title: "Are you sure?",
        text: 'Once deleted, you will not be able to recover this permission, everyone with the permission will have it removed aswell. Please enter "I Understand" in the input below to delete it.',
        type: 'input',
        showCancelButton: true,
        closeOnConfirm: false,
        animation: "slide-from-top"
      }, function(inputValue){
        if(inputValue == "I Understand") {
          window.location.replace('/admin/permissions/delete/'+id);
        }
      });
    });
  })
  .on('m-datatable--on-layout-updated', function () {
    $(".delete-permission").click(function(){
      var id = $(this).attr('id');
      swal({
        title: "Are you sure?",
        text: 'Once deleted, you will not be able to recover this permission, everyone with the permission will have it removed aswell. Please enter "I Understand" in the input below to delete it.',
        type: 'input',
        showCancelButton: true,
        closeOnConfirm: false,
        animation: "slide-from-top"
      }, function(inputValue){
        if(inputValue == "I Understand") {
          window.location.replace('/admin/permissions/delete/'+id);
        }
      });
    });
  })
  .on('m-datatable--on-ajax-done', function () {
    $(".delete-permission").click(function(){
      var id = $(this).attr('id');
      swal({
        title: "Are you sure?",
        text: 'Once deleted, you will not be able to recover this permission, everyone with the permission will have it removed aswell. Please enter "I Understand" in the input below to delete it.',
        type: 'input',
        showCancelButton: true,
        closeOnConfirm: false,
        animation: "slide-from-top"
      }, function(inputValue){
        if(inputValue == "I Understand") {
          window.location.replace('/admin/permissions/delete/'+id);
        }
      });
    });
  });

    </script>

@endsection
