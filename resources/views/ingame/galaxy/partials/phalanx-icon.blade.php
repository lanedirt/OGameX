@if ($can_phalanx)
    <a class="phalanxlink" href="javascript:void(0);" data-galaxy="{{ $galaxy }}" data-system="{{ $system }}" data-position="{{ $position }}">
        <div class="tooltip js_hideTipOnMobile phalanxActive" title="Use phalanx"></div>
    </a>
@elseif ($phalanx_inactive)
    <div class="tooltip js_hideTipOnMobile phalanxInctive" title="{{ $phalanx_inactive_reason }}"></div>
@endif
