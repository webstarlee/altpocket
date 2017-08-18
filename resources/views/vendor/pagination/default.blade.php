@if ($paginator->hasPages())
    <ul class="pagination" style="display:block;text-align:center;">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="disabled"><button class="mdl-button previous disabled" disabled>Previous</button></li>
        @else
            <li><button class="mdl-button previous" id="{{$paginator->currentPage()-1}}" rel="previous">Previous</button></li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="disabled"><span>{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="active"><button class="mdl-button  mdl-button--raised mdl-button--colored" id="{{$page}}">{{ $page }}</button></li>
                    @else
                        <li><button class="mdl-button " id="{{$page}}">{{ $page }}</button></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li><button class="mdl-button next" id="{{$paginator->currentPage()+1}}" rel="next">Next</button></li>
        @else
            <li class="disabled"><button class="mdl-button previous disabled" disabled>Next</button></li>
        @endif
    </ul>
@endif
