<div class="col-xl-12 order-xl-1 col-lg-12 order-lg-1 col-md-12 col-sm-12 col-xs-12">
	<div class="ui-block">
		<div class="ui-block-title">
			<h5 class="title">Photos</h5>
			{{-- <ul class="card-actions icons group-photo-view-cardaction-more">
				<li class="dropdown">
					<a href="javascript:void(0)" data-toggle="dropdown">
						<i class="zmdi zmdi-more-vert"></i>
					</a>
					<ul class="dropdown-menu dropdown-menu-right btn-primary">
						<li><a href="">Add new photo</a></li>
					</ul>
				</li>
			</ul> --}}
		</div>
		<div class="ui-block-content">
			<!-- W-Latest-Photo -->
			<ul class="widget w-last-photo js-zoom-gallery">
				<?php foreach ($group_photos as $group_photo): ?>
					<li>
						<a href="{{asset('assets/images/group/'.$group->url.'/'.$group_photo->photo)}}">
							<img src="{{asset('assets/images/group/'.$group->url.'/'.$group_photo->photo)}}" alt="photo">
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<!-- .. end W-Latest-Photo -->
		</div>
	</div>
</div>
