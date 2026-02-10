<div class="tabContent">
    @if(!empty($rulesHtml))
        <div class="inner-box clearfix" style="color:#848484;">
            {!! $rulesHtml !!}
        </div>
    @else
        <div class="inner-box clearfix" style="color:#848484;">
            <p>No rules have been set.</p>
        </div>
    @endif
</div>
