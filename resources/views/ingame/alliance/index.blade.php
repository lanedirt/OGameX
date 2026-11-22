@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div id="eventboxContent" style="display: none">
        <img height="16" width="16" src="/img/icons/3f9884806436537bdec305aa26fc60.gif">
    </div>

    <div id="alliancecomponent" class="maincontent">
        <div id="netz">
            <div id="alliance">
                <div id="inhalt">
                    <div id="planet" class="planet-header ">
                        <h2>{{ __('t_ingame.alliance.page_title') }}</h2>
                        <a class="toggleHeader" href="javascript:void(0);" data-name="alliance">
                            <img alt="" src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="22" width="22">
                        </a>
                    </div>
                    <div class="c-left "></div>
                    <div class="c-right "></div>
                    <div id="tabs">
                        <ul class="tabsbelow" id="tab-ally">
                            @if($alliance)
                                {{-- User is in an alliance - show alliance management tabs --}}
                                <li class="aktiv">
                                    <a class="overview navi" rel="{{ route('alliance.ajax.overview') }}" data-tab="overview">
                                        <span>{{ __('t_ingame.alliance.tab_overview') }}</span>
                                    </a>
                                </li>
                                @if($member && $member->hasPermission(\OGame\Models\AllianceRank::PERMISSION_MANAGE_ALLY))
                                    <li>
                                        <a class="management navi" rel="{{ route('alliance.ajax.management') }}" data-tab="management">
                                            <span>{{ __('t_ingame.alliance.tab_management') }}</span>
                                        </a>
                                    </li>
                                @endif
                                @if($member && $member->hasPermission(\OGame\Models\AllianceRank::PERMISSION_SEND_CIRCULAR_MSG))
                                    <li>
                                        <a class="broadcast navi" rel="{{ route('alliance.ajax.broadcast') }}" data-tab="broadcast">
                                            <span>{{ __('t_ingame.alliance.tab_communication') }}</span>
                                        </a>
                                    </li>
                                @endif
                                @if($member && $member->hasPermission(\OGame\Models\AllianceRank::PERMISSION_SEE_APPLICATIONS))
                                    <li>
                                        <a class="applications navi" rel="{{ route('alliance.ajax.applications') }}" data-tab="applications">
                                            <span id="applicationTab">{{ __('t_ingame.alliance.tab_applications') }} ({{ $applications->count() }})<span class="newApplications undermark" style="display: {{ $applications->count() > 0 ? 'inline' : 'none' }}"></span></span>
                                        </a>
                                    </li>
                                @endif
                                @if($member && $member->hasPermission(\OGame\Models\AllianceRank::PERMISSION_MANAGE_CLASSES))
                                    <li>
                                        <a class="classselection navi" rel="{{ route('alliance.ajax.classes') }}" data-tab="classselection">
                                            <span>{{ __('t_ingame.alliance.tab_classes') }}</span>
                                        </a>
                                    </li>
                                @endif
                            @else
                                {{-- User is not in an alliance - show create/search or apply options --}}
                                @if($targetAllianceId)
                                    <li class="aktiv">
                                        <a id="isNewApplication" rel="{{ route('alliance.ajax.handleapplication', ['alliance_id' => $targetAllianceId]) }}"><span>{{ __('t_ingame.alliance.tab_apply') }}</span></a>
                                        <input type="hidden" id="applied" value="">
                                    </li>
                                    <li>
                                        <a class="overlay ipiHintable" href="{{ route('search.overlay', ['category' => 4, 'ajax' => 1])  }}" data-ipi-hint="ipiAllianceSearch">
                                            <span>{{ __('t_ingame.alliance.tab_search') }}</span>
                                        </a>
                                    </li>
                                @else
                                    <li class="aktiv">
                                        <a class="createNewAlliance ipiHintable" rel="{{ route('alliance.ajax.create')  }}" data-ipi-hint="ipiAllianceCreate"><span>{{ __('t_ingame.alliance.tab_create') }}</span></a>
                                        <input type="hidden" id="applied" value="">
                                    </li>
                                    <li>
                                        <a class="overlay ipiHintable" href="{{ route('search.overlay', ['category' => 4, 'ajax' => 1])  }}" data-ipi-hint="ipiAllianceSearch">
                                            <span>{{ __('t_ingame.alliance.tab_search') }}</span>
                                        </a>
                                    </li>
                                @endif
                            @endif
                        </ul>
                    </div>
                    <div class="clearfloat"></div>
                    <div class="alliance_wrapper">
                        <div class="allianceContent">

                        </div>
                        <div class="og-loading" style="display: none;"><div class="og-loading-overlay"><div class="og-loading-indicator"></div></div></div></div>
                    <div class="new_footer"></div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            var tab = "{{ $targetAllianceId ? 'handleApplication' : ($alliance ? 'overview' : 'createNewAlliance') }}";
            var appliedAllyId = {{ $targetAllianceId ?? 0 }};
            var loca = {!! json_encode([
                'LOCA_ALL_AJAXLOAD'                              => __('t_ingame.alliance.loca_loading'),
                'LOCA_NETWORK_ALLY'                              => __('t_ingame.alliance.page_title'),
                'LOCA_LEFTMENU_OVERVIEW'                         => __('t_ingame.alliance.tab_overview'),
                'LOCA_NETWORK_NAVI_OVERVIEW'                     => __('t_ingame.alliance.tab_management'),
                'LOCA_NETWORK_NAVI_COMUNICATE'                   => __('t_ingame.alliance.tab_communication'),
                'LOCA_NETWORK_APPLICATIONS'                      => __('t_ingame.alliance.tab_applications'),
                'LOCA_NETWORK_APPLY'                             => __('t_ingame.alliance.tab_apply'),
                'LOCA_NETWORK_NAVI_NEWALLY'                      => __('t_ingame.alliance.tab_create'),
                'LOCA_NETWORK_NAVI_SEARCHALLY'                   => __('t_ingame.alliance.tab_search'),
                'LOCA_NETWORK_YOUR_ALLY'                         => __('t_ingame.alliance.your_alliance'),
                'LOCA_NETWORK_ALLY_LOGO'                         => __('t_ingame.alliance.logo'),
                'LOCA_ALL_NAME'                                  => __('t_ingame.alliance.name'),
                'LOCA_NETWORK_MEMBER_LIST'                       => __('t_ingame.alliance.member_list'),
                'LOCA_NETWORK_TAG'                               => __('t_ingame.alliance.tag'),
                'LOCA_NETWORK_USERS'                             => __('t_ingame.alliance.member'),
                'LOCA_NETWORK_YOUR_RANK'                         => __('t_ingame.alliance.your_rank'),
                'LOCA_NETWORK_HOMEPAGE'                          => __('t_ingame.alliance.homepage'),
                'LOCA_PREVIEW_ALLIANCE'                          => __('t_ingame.alliance.open_page'),
                'LOCA_NETWORK_MEMBERS_RANK'                      => __('t_ingame.alliance.col_rank'),
                'LOCA_GALAXY_RANK'                               => __('t_ingame.alliance.col_rank'),
                'LOCA_NETWORK_USER_COORD'                        => __('t_ingame.alliance.col_coords'),
                'LOCA_NETWORK_USER_JOIN'                         => __('t_ingame.alliance.col_joined'),
                'LOCA_NETWORK_USER_INACTIVE'                     => __('t_ingame.alliance.col_online'),
                'LOCA_NETWORK_FUNCTION'                          => __('t_ingame.alliance.col_function'),
                'LOCA_NETWORK_ASSIGN_RANK'                       => __('t_ingame.alliance.assign_rank_btn'),
                'LOCA_NETWORK_NO_MEMBERS'                        => __('t_ingame.alliance.no_members'),
                'LOCA_NETWORK_INTERN_AREA'                       => __('t_ingame.alliance.internal_area'),
                'LOCA_NETWORK_EXTERN_AREA'                       => __('t_ingame.alliance.external_area'),
                'LOCA_NETWORK_ADDRESSEE'                         => __('t_ingame.alliance.addressee'),
                'LOCA_NETWORK_ALLPLAYERS'                        => __('t_ingame.alliance.all_players'),
                'LOCA_NETWORK_RANK'                              => __('t_ingame.alliance.only_rank'),
                'LOCA_ALL_SEND'                                  => __('t_ingame.alliance.send_btn'),
                'LOCA_NETWORK_NO_APPLICATIONS'                   => __('t_ingame.alliance.no_applications'),
                'LOCA_NETWORK_ALLY_APPLICATION_DATE'             => __('t_ingame.alliance.app_date'),
                'LOCA_NETWORK_ACTION'                            => __('t_ingame.alliance.action_col'),
                'LOCA_NETWORK_ALLY_ACCEPT_NEWMEMBER'             => __('t_ingame.alliance.accept_btn'),
                'LOCA_NETWORK_ALLY_DENIED_NEWMEMBER'             => __('t_ingame.alliance.deny_btn'),
                'LOCA_NETWORK_ALLY_REPORTED'                     => __('t_ingame.alliance.report_btn'),
                'LOCA_WRITE_MSG_ANSWER'                          => __('t_ingame.alliance.answer_btn'),
                'LOCA_NETWORK_ALLY_REASON'                       => __('t_ingame.alliance.reason_label'),
                'LOCA_NETWORK_PRIVILEGES'                        => __('t_ingame.alliance.configure_privileges'),
                'LOCA_ALLIANCE_RIGHT_EXPLANATION'                => __('t_ingame.alliance.rights_legend'),
                'LOCA_NETWORK_ALLY_RIGHTS_SEEAPPLICATIONS'       => __('t_ingame.alliance.perm_see_applications'),
                'LOCA_NETWORK_ALLY_RIGHTS_EDITAPPLICATIONS'      => __('t_ingame.alliance.perm_edit_applications'),
                'LOCA_NETWORK_ALLY_RIGHTS_SEEMEMBERS'            => __('t_ingame.alliance.perm_see_members'),
                'LOCA_NETWORK_ALLY_RIGHTS_KICKUSER'              => __('t_ingame.alliance.perm_kick_user'),
                'LOCA_NETWORK_ALLY_RIGHTS_SEEMEMBERONLINESTATUS_SHORT' => __('t_ingame.alliance.perm_see_online'),
                'LOCA_NETWORK_ALLY_RIGHTS_SENDCIRCULARMSG'       => __('t_ingame.alliance.perm_send_circular'),
                'LOCA_NETWORK_ALLY_RIGHTS_DELETEALLY'            => __('t_ingame.alliance.perm_disband'),
                'LOCA_NETWORK_ALLY_RIGHTS_MANAGEALLY'            => __('t_ingame.alliance.perm_manage'),
                'LOCA_NETWORK_ALLY_RIGHTS_RIGHTHAND_SHORT'       => __('t_ingame.alliance.perm_right_hand'),
                'LOCA_NETWORK_ALLY_RIGHTS_RIGHTHAND'             => __('t_ingame.alliance.perm_right_hand_long'),
                'LOCA_NETWORK_ALLY_RIGHTS_MANAGECLASSES'         => __('t_ingame.alliance.perm_manage_classes'),
                'LOCA_NETWORK_CREATE_RANK'                       => __('t_ingame.alliance.create_rank_btn'),
                'LOCA_NETWORK_RANKING_NAME'                      => __('t_ingame.alliance.col_rank_name'),
                'LOCA_NETWORK_NO_RANKS'                          => __('t_ingame.alliance.no_ranks'),
                'LOCA_NETWORK_RANK_DELETE'                       => __('t_ingame.alliance.delete_rank'),
                'LOCA_ALL_NETWORK_SAVE'                          => __('t_ingame.alliance.save_btn'),
                'LOCA_ALLIANCE_CAN_ONLY_GIVE_OWN_RANKS'          => __('t_ingame.alliance.rights_warning_loca'),
                'LOCA_NETWORK_TEXT_MANAGE'                       => __('t_ingame.alliance.manage_texts'),
                'LOCA_NETWORK_TEXT_INTERN'                       => __('t_ingame.alliance.internal_text'),
                'LOCA_NETWORK_TEXT_EXTERN'                       => __('t_ingame.alliance.external_text'),
                'LOCA_NETWORK_TEXT_APPLICATION'                  => __('t_ingame.alliance.application_text'),
                'LOCA_NETWORK_SETTINGS'                          => __('t_ingame.alliance.options'),
                'LOCA_NETWORK_ALLY_SLOTS_OPEN'                   => __('t_ingame.alliance.status_open'),
                'LOCA_NETWORK_ALLY_SLOTS_CLOSE'                  => __('t_ingame.alliance.status_closed'),
                'LOCA_NETWORK_ALLY_RENAME_FOUNDERNAME'           => __('t_ingame.alliance.rename_founder'),
                'LOCA_ALLY_RENAME_NEWCOMER'                      => __('t_ingame.alliance.rename_newcomer'),
                'LOCA_NETWORK_ALLY_TAGNAME_EDIT'                 => __('t_ingame.alliance.change_tag_name'),
                'LOCA_NETWORK_ALLY_TAG_EDIT'                     => __('t_ingame.alliance.change_tag'),
                'LOCA_NETWORK_ALLY_NAME_EDIT'                    => __('t_ingame.alliance.change_name'),
                'LOCA_NETWORK_ALLY_TAG_NOW'                      => __('t_ingame.alliance.former_tag_short'),
                'LOCA_NETWORK_ALLY_TAG_NEW'                      => __('t_ingame.alliance.new_tag_short'),
                'LOCA_NETWORK_ALLY_NAME_NOW'                     => __('t_ingame.alliance.former_name_short'),
                'LOCA_NETWORK_ALLY_NAME_NEW'                     => __('t_ingame.alliance.new_name_short'),
                'LOCA_NETWORK_ALLY_DELETE_PASSON'                => __('t_ingame.alliance.delete_pass_on'),
                'LOCA_NETWORK_ALLY_DELETE'                       => __('t_ingame.alliance.delete_btn'),
                'LOCA_ALLY_HANDOVER'                             => __('t_ingame.alliance.handover'),
                'LOCA_ALLY_TAKEOVER'                             => __('t_ingame.alliance.takeover_btn'),
                'LOCA_ALL_BUTTON_FORWARD'                        => __('t_ingame.alliance.loca_continue'),
                'LOCA_ALLY_TRANSFER_LONG'                        => __('t_ingame.alliance.confirm_abandon'),
                'LOCA_NETWORK_ALLY_CHANGE_FOUNDER'               => __('t_ingame.alliance.loca_change_founder'),
                'LOCA_ALLY_ERROR_NO_TRANSFER_MEMBERS'            => __('t_ingame.alliance.loca_no_transfer_error'),
                'LOCA_ALLY_TAKEOVER_LONG'                        => __('t_ingame.alliance.confirm_takeover_long'),
                'LOCA_NETWORK_ALLY_ERROR_FOUNDER_ACTIVE'         => __('t_ingame.alliance.loca_founder_inactive_error'),
                'LOCA_ALL_OK'                                    => __('t_ingame.shared.caution') !== 'Caution' ? 'Ok' : 'Ok',
                'LOCA_ALL_CANCEL'                                => __('t_ingame.shared.no') !== 'No' ? __('t_ingame.shared.no') : 'Cancel',
                'LOCA_ALLIANCE_CLASS_SELECTION_HEADER'           => __('t_ingame.alliance.class_selection_header'),
                'LOCA_ALLIANCE_CLASS_SELECTION_HEAD'             => __('t_ingame.alliance.select_class_title'),
                'LOCA_ALLIANCE_CLASS_SELECTION_NOTE'             => __('t_ingame.alliance.select_class_note'),
                'LOCA_ALL_NETWORK_ATTENTION'                     => __('t_ingame.shared.caution'),
                'LOCA_ALL_YES'                                   => __('t_ingame.shared.yes'),
                'LOCA_ALL_NO'                                    => __('t_ingame.shared.no'),
                'LOCA_NETWORK_ALLY_TAKEOVER_ARE_YOU_SURE'        => __('t_ingame.alliance.confirm_pass_on'),
                'LOCA_NETWORK_ALLY_GIVEUP'                       => __('t_ingame.alliance.confirm_disband'),
                'LOCA_ALLY_TAKEOVER_QUESTION'                    => __('t_ingame.alliance.confirm_takeover'),
                'LOCA_NETWORK_LEAVE_ALLY'                        => __('t_ingame.alliance.leave_btn'),
                'locaAllyNameCharacter'                          => __('t_ingame.alliance.loca_ally_tag_chars'),
                'locaAllyTagCharacter'                           => __('t_ingame.alliance.loca_ally_name_chars'),
                'LOCA_NETWORK_ALLY_NEWALLYNAME'                  => __('t_ingame.alliance.loca_ally_name_label'),
                'LOCA_NETWORK_ALLY_NEWTAG'                       => __('t_ingame.alliance.loca_ally_tag_label'),
                'LOCA_ALLIANCE_CLASS_SELECTION_BUTTON_DEACTIVATE'=> __('t_ingame.alliance.loca_deactivate'),
                'LOCA_ALLIANCE_CLASS_NOTE_ACTIVATE_WITH_DM'      => __('t_ingame.alliance.loca_activate_dm'),
                'LOCA_ALLIANCE_CLASS_NOTE_ACTIVATE_WITH_ITEM'    => __('t_ingame.alliance.loca_activate_item'),
                'LOCA_ALLIANCE_CLASS_NOTE_DEACTIVATE'            => __('t_ingame.alliance.loca_deactivate_note'),
                'LOCA_ALLIANCE_CLASS_NOTE_ACTIVATE_APPEND_CURRENT_CLASS' => __('t_ingame.alliance.loca_class_change_append'),
                'LOCA_ALLIANCE_CLASS'                            => __('t_ingame.alliance.class_label'),
                'LOCA_ALL_NOTICE'                                => __('t_ingame.alliance.loca_reference'),
                'LOCA_ALL_ERROR_LACKING_DM'                      => __('t_ingame.alliance.loca_no_dm'),
                'LOCA_CHARACTERCLASS_SELECTION_BUTTON_DEACTIVATE'=> __('t_ingame.alliance.loca_deactivate'),
                'LOCA_ALLIANCE_CREATION_DATE'                    => __('t_ingame.alliance.created'),
                'LOCA_SETTINGS_SELECT_LANGUAGE'                  => __('t_ingame.alliance.loca_language'),
            ]) !!};
            var alliance = new Alliance({tab: tab, token: '{{ csrf_token() }}', loca: loca});
            function manageTabs(id)
            {
                var selector = '#' + id;
                var rel = $(selector).attr('rel');

                if ($(selector).hasClass('opened')) {
                    $(selector).addClass('closed');
                    $(selector).removeClass('opened');
                    $("#" + rel).hide();
                } else {
                    $(selector).removeClass('closed');
                    $(selector).addClass('opened');
                    $("#" + rel).show();
                    $('.alliancetexts').keyup(); //This will trigger the keyup-Event for the editor. This will set the remaining Chars Counter to the right value.
                }
            }

        </script>                    </div>
@endsection
