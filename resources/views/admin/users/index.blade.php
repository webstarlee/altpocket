@extends('layouts.admin');

@section('title', 'Admin | List Users')


@section('header')
  <div class="m-subheader " style="height:100px;padding:30px 30px 0 30px;">
    <div class="d-flex align-items-center">
      <div class="mr-auto">
        <h3 class="m-subheader__title m-subheader__title--separator">
          User Management
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
            <a href="/admin/users" class="m-nav__link">
              <span class="m-nav__link-text">
                Users
              </span>
            </a>
          </li>
          <li class="m-nav__separator">
            -
          </li>
          <li class="m-nav__item">
            <a href="" class="m-nav__link">
              <span class="m-nav__link-text">
                List Users
              </span>
            </a>
          </li>
        </ul>
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
						List Users
						<small>
							Altpockets userbase
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
		<script src="/panel/assets/demo/default/custom/components/datatables/base/data-ajax-users.js?version=abe2" type="text/javascript"></script>

@endsection
