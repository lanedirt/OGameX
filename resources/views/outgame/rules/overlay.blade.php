<div class="tabContent">
    @if(!empty($contentHtml))
        <div class="inner-box clearfix" style="color:#848484;">
            {!! $contentHtml !!}
        </div>
    @else
        <div class="inner-box clearfix" style="color:#848484;">
            <p>{{ $emptyMessage }}</p>
        </div>
    @endif
</div>
