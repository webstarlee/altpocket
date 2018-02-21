<div class="m-list-search__results">

  @if(count($users) <= 0)
  	<span class="m-list-search__result-message">
    		No users found
  	</span>
  @else


	<span class="m-list-search__result-category  m-list-search__result-category--first">
		Users
	</span>
  @foreach($users as $user)
    @php
      if($user->avatar == "default.jpg"){
         $url = 'https://altpocket.io/assets/img/default.png';
      } else {
         $url = 'https://altpocket.io/uploads/avatars/'.$user->id.'/'.$user->avatar;
      }
    @endphp
  	<a href="/user/{{$user->username}}" class="m-list-search__result-item">
  		<span class="m-list-search__result-item-pic"><img class="m--img-rounded" src="{{$url}}" title=""/></span>
  		<span class="m-list-search__result-item-text" style="color:{{$user->groupColor()}};{{$user->groupStyle()}}">@if($user->getEmblem())<img src="/awards/{{$user->getEmblem()}}" style="width:18px;margin-right:3px;padding-bottom:2px;">@endif{{$user->username}}</span>
  	</a>
  @endforeach
</div>
@endif
