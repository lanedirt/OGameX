<div id="payment">
    <div class="ajaxContent" style="height:620px; width:800px;">
        <p class="textCenter">{{ __('t_ingame.shop.feature_coming_soon') }}</p>
    </div>
    <!--
    <IFRAME id="iframe" allowtransparency="true" src="{{ route('payment.iframesrc') }}" frameborder="0"
            style="height:620px; width:800px; overflow-x:hidden; overflow-y:auto;">
    </IFRAME>
    -->
</div>
<script type="text/javascript">
    var $overlay = $('#payment').parent();
    $overlay.addClass('payment');
    $overlay.dialog('option', {
        title: {!! json_encode(__('t_ingame.shop.btn_purchase_dark_matter')) !!},
        modal: true,
        resizable: false,
        draggable: false,
        close: function() {
            $overlay.remove();
            getAjaxResourcebox(function(resources) {
                $("#planet #content .level span")
                    .attr('class', 'undermark')
                    .text(gfNumberGetHumanReadable(resources.darkmatter.amount, isMobile));
            });
        }
    });
</script>