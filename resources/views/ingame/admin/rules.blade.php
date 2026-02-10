@extends('ingame.layouts.main')

@section('content')

    @include('ingame.shared.buddy.bbcode-parser')

    <div id="alliancecomponent" class="maincontent">
        <div id="netz">
            <div id="alliance">
                <div id="inhalt">
                    <div id="planet" class="planet-header">
                        <h2>@lang('Server Rules')</h2>
                    </div>
                    <div class="c-left"></div>
                    <div class="c-right"></div>
                    <div class="clearfloat"></div>
                    <div class="alliance_wrapper">
                        <div class="allianceContent">
                            <div class="sectioncontent" style="display:block;">
                                <div class="contentz ui-tabs ui-corner-all ui-widget ui-widget-content">
                                    <div class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="false">
                                        <form action="{{ route('admin.rules.update') }}" method="post" autocomplete="off">
                                            {{ csrf_field() }}
                                            <textarea name="rules_content" class="alliancetexts">{{ $rules_content }}</textarea>
                                            <input type="submit" class="btn_blue float_right" value="@lang('Save rules')">
                                        </form>
                                    </div>
                                </div>
                                <div class="footer"></div>
                            </div>
                        </div>
                    </div>
                    <div class="new_footer"></div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            @if (session('success'))
                fadeBox('{{ session('success') }}', false);
            @endif

            initBBCodeEditor(
                locaKeys,
                {},
                false,
                '.alliancetexts',
                50000,
                true
            );
        });
    </script>

@endsection
