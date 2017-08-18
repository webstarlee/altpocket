
																	<div class="row">
																		<div class="col-xs-12 col-sm-11 col-sm-offset-1">
                                                                            @foreach($comments as $comment)
                                                                            @php $commenter = DB::table('users')->where('id', $comment->commenter)->first(); 
                                                                            @endphp
                                                                            
																			<article>
																				<div class="card card-comment" data-timeline="comment">
																					<header class="card-heading mw-lightGray comment-header">
                                                                                        <a href="/user/{{$commenter->username}}">
                                                                                        @if($commenter->avatar != "default.jpg")
																						<img src="/uploads/avatars/{{$commenter->id}}/{{$commenter->avatar}}" alt="" class="img-circle img-sm pull-left m-r-10"></a>
                                                                                        @else
																						<img src="/assets/img/logo.png" alt="" class="img-circle img-sm pull-left m-r-10"></a>
                                                                                        @endif
                                                                                        <h2 class="card-title m-t-5"><a style="" href="/user/{{$commenter->username}}">{{$commenter->username}}</a></h2>
																						<ul class="card-actions icons right-top">
																							<li class="hidden-sm hidden-md hidden-lg">
																								<a href="javascript:void(0)">
																									<i class="zmdi zmdi-comment-text"></i>
																								</a>
																							</li>
																							<li style="font-size:11px;">
																								{{$comment->created_at->format('Y-m-d')}}
																							</li>
																							<li class="dropdown">
																								<a href="javascript:void(0)" data-toggle="dropdown">
																									<i class="zmdi zmdi-more-vert"></i>
																								</a>
																								<ul class="dropdown-menu btn-primary dropdown-menu-right">
                                                                                                    @if(Auth::check())
                                                                                                    @if(Auth::user()->id == $comment->commenter)
																									<li>
																										<a href="javascript:void(0)" data-toggle="modal" data-target="#edit_comment" id="{{$comment->id}}" class="edit-comment">Edit Comment</a>
																									</li>
																									<li>
																										<a href="/comment/delete/{{$comment->id}}">Delete Comment</a>
																									</li>
                                                                                                    @elseif(Auth::user()->id == $comment->userid)
																									<li>
																										<a href="/comment/delete/{{$comment->id}}">Delete Comment</a>
																									</li>
                                                                                                    @endif
                                                                                                    @endif
																								</ul>
																							</li>
																						</ul>
																					</header>
																					<div class="card-body">
																						<p>{!! nl2br(e($comment->comment)) !!}</p>
																						</div>
																					</div>
																				</article>
                                                                            @endforeach
                                                                            {{ $comments->links() }}
                                                                        </div>
																			</div>	