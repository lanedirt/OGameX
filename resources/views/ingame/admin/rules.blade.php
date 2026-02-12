@extends('ingame.layouts.main')

@section('content')

    @include('ingame.shared.buddy.bbcode-parser')

    <div id="alliancecomponent" class="maincontent">
        <div id="netz">
            <div id="alliance">
                <div id="inhalt">
                    <div id="planet" class="planet-header">
                        <h2>@lang('Rules & Legal')</h2>
                    </div>
                    <div class="c-left"></div>
                    <div class="c-right"></div>
                    <div class="clearfloat"></div>
                    <div class="alliance_wrapper" style="height:auto; min-height:auto; padding-bottom:50px;">
                        <div class="allianceContent">
                            <div class="sectioncontent" style="display:block;">
                                <div class="contentz ui-tabs ui-corner-all ui-widget ui-widget-content" id="rulesLegalTabs">
                                    <ul class="tabsbelow subsection_tabs ui-state-active ui-tabs-nav ui-corner-all ui-helper-reset ui-helper-clearfix ui-widget-header" role="tablist">
                                        <li role="tab" tabindex="0" class="ui-tabs-tab ui-corner-top ui-state-default ui-tab ui-tabs-active ui-state-active">
                                            <a href="#tab-rules" role="presentation" tabindex="-1" class="ui-tabs-anchor"><span>@lang('Rules')</span></a>
                                        </li>
                                        <li role="tab" tabindex="-1" class="ui-tabs-tab ui-corner-top ui-state-default ui-tab">
                                            <a href="#tab-legal" role="presentation" tabindex="-1" class="ui-tabs-anchor"><span>@lang('Legal')</span></a>
                                        </li>
                                        <li role="tab" tabindex="-1" class="ui-tabs-tab ui-corner-top ui-state-default ui-tab">
                                            <a href="#tab-privacy" role="presentation" tabindex="-1" class="ui-tabs-anchor"><span>@lang('Privacy Policy')</span></a>
                                        </li>
                                        <li role="tab" tabindex="-1" class="ui-tabs-tab ui-corner-top ui-state-default ui-tab">
                                            <a href="#tab-terms" role="presentation" tabindex="-1" class="ui-tabs-anchor"><span>@lang('T&Cs')</span></a>
                                        </li>
                                        <li role="tab" tabindex="-1" class="ui-tabs-tab ui-corner-top ui-state-default ui-tab">
                                            <a href="#tab-contact" role="presentation" tabindex="-1" class="ui-tabs-anchor"><span>@lang('Contact')</span></a>
                                        </li>
                                    </ul>
                                    <div id="tab-rules" class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="false">
                                        <form action="{{ route('admin.rules.update') }}" method="post" autocomplete="off">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="legal_content" value="{{ $legal_content }}">
                                            <input type="hidden" name="privacy_policy_content" value="{{ $privacy_policy_content }}">
                                            <input type="hidden" name="terms_content" value="{{ $terms_content }}">
                                            <input type="hidden" name="contact_content" value="{{ $contact_content }}">
                                            <textarea name="rules_content" class="alliancetexts">{{ $rules_content }}</textarea>
                                            <input type="submit" class="btn_blue float_right" value="@lang('Save')">
                                        </form>
                                    </div>
                                    <div id="tab-legal" class="ui-tabs-panel ui-corner-bottom ui-widget-content" style="display:none;" aria-hidden="true">
                                        <form action="{{ route('admin.rules.update') }}" method="post" autocomplete="off">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="rules_content" value="{{ $rules_content }}">
                                            <input type="hidden" name="privacy_policy_content" value="{{ $privacy_policy_content }}">
                                            <input type="hidden" name="terms_content" value="{{ $terms_content }}">
                                            <input type="hidden" name="contact_content" value="{{ $contact_content }}">
                                            <textarea name="legal_content" class="alliancetexts">{{ $legal_content }}</textarea>
                                            <input type="submit" class="btn_blue float_right" value="@lang('Save')">
                                        </form>
                                    </div>
                                    <div id="tab-privacy" class="ui-tabs-panel ui-corner-bottom ui-widget-content" style="display:none;" aria-hidden="true">
                                        <form action="{{ route('admin.rules.update') }}" method="post" autocomplete="off">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="rules_content" value="{{ $rules_content }}">
                                            <input type="hidden" name="legal_content" value="{{ $legal_content }}">
                                            <input type="hidden" name="terms_content" value="{{ $terms_content }}">
                                            <input type="hidden" name="contact_content" value="{{ $contact_content }}">
                                            <textarea name="privacy_policy_content" class="alliancetexts">{{ $privacy_policy_content }}</textarea>
                                            <input type="submit" class="btn_blue float_right" value="@lang('Save')">
                                        </form>
                                    </div>
                                    <div id="tab-terms" class="ui-tabs-panel ui-corner-bottom ui-widget-content" style="display:none;" aria-hidden="true">
                                        <form action="{{ route('admin.rules.update') }}" method="post" autocomplete="off">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="rules_content" value="{{ $rules_content }}">
                                            <input type="hidden" name="legal_content" value="{{ $legal_content }}">
                                            <input type="hidden" name="privacy_policy_content" value="{{ $privacy_policy_content }}">
                                            <input type="hidden" name="contact_content" value="{{ $contact_content }}">
                                            <textarea name="terms_content" class="alliancetexts">{{ $terms_content }}</textarea>
                                            <input type="submit" class="btn_blue float_right" value="@lang('Save')">
                                        </form>
                                    </div>
                                    <div id="tab-contact" class="ui-tabs-panel ui-corner-bottom ui-widget-content" style="display:none;" aria-hidden="true">
                                        <form action="{{ route('admin.rules.update') }}" method="post" autocomplete="off">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="rules_content" value="{{ $rules_content }}">
                                            <input type="hidden" name="legal_content" value="{{ $legal_content }}">
                                            <input type="hidden" name="privacy_policy_content" value="{{ $privacy_policy_content }}">
                                            <input type="hidden" name="terms_content" value="{{ $terms_content }}">
                                            <textarea name="contact_content" class="alliancetexts">{{ $contact_content }}</textarea>
                                            <input type="submit" class="btn_blue float_right" value="@lang('Save')">
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

            $('#rulesLegalTabs').tabs();

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
