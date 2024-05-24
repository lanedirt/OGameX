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
                        <h2>Alliance</h2>
                        <a class="toggleHeader" href="javascript:void(0);" data-name="alliance">
                            <img alt="" src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="22" width="22">
                        </a>
                    </div>
                    <div class="c-left "></div>
                    <div class="c-right "></div>
                    <div id="tabs">
                        <ul class="tabsbelow" id="tab-ally">
                            <li class="aktiv">
                                <a class="createNewAlliance ipiHintable" rel="{{ route('alliance.ajax.create')  }}" data-ipi-hint="ipiAllianceCreate"><span>Create alliance</span></a>
                                <input type="hidden" id="applied" value="">
                            </li>
                            <li>
                                <a class="overlay ipiHintable" href="{{ route('search.overlay', ['category' => 4, 'ajax' => 1])  }}" data-ipi-hint="ipiAllianceSearch">
                                    <span>Search alliance</span>
                                </a>
                            </li>
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
            var tab = "createNewAlliance";
            var loca = {"LOCA_ALL_AJAXLOAD":"load...","LOCA_NETWORK_ALLY":"Alliance","LOCA_LEFTMENU_OVERVIEW":"Overview","LOCA_NETWORK_NAVI_OVERVIEW":"Management","LOCA_NETWORK_NAVI_COMUNICATE":"Communication","LOCA_NETWORK_APPLICATIONS":"Applications","LOCA_NETWORK_APPLY":"apply","LOCA_NETWORK_NAVI_NEWALLY":"Create alliance","LOCA_NETWORK_NAVI_SEARCHALLY":"Search alliance","LOCA_NETWORK_YOUR_ALLY":"Your alliance","LOCA_NETWORK_ALLY_LOGO":"Alliance logo","LOCA_ALL_NAME":"Name","LOCA_NETWORK_MEMBER_LIST":"Member List","LOCA_NETWORK_TAG":"Tag","LOCA_NETWORK_USERS":"Member","LOCA_NETWORK_YOUR_RANK":"Your Rank","LOCA_NETWORK_HOMEPAGE":"Homepage","LOCA_PREVIEW_ALLIANCE":"Open alliance page","LOCA_NETWORK_MEMBERS_RANK":"Rank","LOCA_GALAXY_RANK":"Rank","LOCA_NETWORK_USER_COORD":"Coords","LOCA_NETWORK_USER_JOIN":"Joined","LOCA_NETWORK_USER_INACTIVE":"Online","LOCA_NETWORK_FUNCTION":"Function","LOCA_NETWORK_ASSIGN_RANK":"Assign rank","LOCA_NETWORK_NO_MEMBERS":"No members found","LOCA_NETWORK_INTERN_AREA":"Internal Area","LOCA_NETWORK_EXTERN_AREA":"External Area","LOCA_NETWORK_ADDRESSEE":"To","LOCA_NETWORK_ALLPLAYERS":"all players","LOCA_NETWORK_RANK":"only rank:","LOCA_ALL_SEND":"Send","LOCA_NETWORK_NO_APPLICATIONS":"No applications found","LOCA_NETWORK_ALLY_APPLICATION_DATE":"Application date","LOCA_NETWORK_ACTION":"Action","LOCA_NETWORK_ALLY_ACCEPT_NEWMEMBER":"accept","LOCA_NETWORK_ALLY_DENIED_NEWMEMBER":"Deny applicant","LOCA_NETWORK_ALLY_REPORTED":"Report application","LOCA_WRITE_MSG_ANSWER":"answer","LOCA_NETWORK_ALLY_REASON":"Reason","LOCA_NETWORK_PRIVILEGES":"Configure privileges","LOCA_ALLIANCE_RIGHT_EXPLANATION":"Rights legend","LOCA_NETWORK_ALLY_RIGHTS_SEEAPPLICATIONS":"Show applications","LOCA_NETWORK_ALLY_RIGHTS_EDITAPPLICATIONS":"Process applications","LOCA_NETWORK_ALLY_RIGHTS_SEEMEMBERS":"Show member list","LOCA_NETWORK_ALLY_RIGHTS_KICKUSER":"Kick user","LOCA_NETWORK_ALLY_RIGHTS_SEEMEMBERONLINESTATUS_SHORT":"See online status","LOCA_NETWORK_ALLY_RIGHTS_SENDCIRCULARMSG":"Write circular message","LOCA_NETWORK_ALLY_RIGHTS_DELETEALLY":"Disband alliance","LOCA_NETWORK_ALLY_RIGHTS_MANAGEALLY":"Manage alliance","LOCA_NETWORK_ALLY_RIGHTS_RIGHTHAND_SHORT":"Right hand","LOCA_NETWORK_ALLY_RIGHTS_RIGHTHAND":"`Right Hand` (necessary to transfer founder rank)","LOCA_NETWORK_ALLY_RIGHTS_MANAGECLASSES":"Manage alliance class","LOCA_NETWORK_CREATE_RANK":"Create new rank","LOCA_NETWORK_RANKING_NAME":"Rank name","LOCA_NETWORK_NO_RANKS":"No ranks found","LOCA_NETWORK_RANK_DELETE":"Delete rank","LOCA_ALL_NETWORK_SAVE":"Save","LOCA_ALLIANCE_CAN_ONLY_GIVE_OWN_RANKS":"[b]Warning![\/b] You can only give permissions that you have yourself.","LOCA_NETWORK_TEXT_MANAGE":"Manage texts","LOCA_NETWORK_TEXT_INTERN":"Internal text","LOCA_NETWORK_TEXT_EXTERN":"External text","LOCA_NETWORK_TEXT_APPLICATION":"Application text","LOCA_NETWORK_SETTINGS":"Options","LOCA_NETWORK_ALLY_SLOTS_OPEN":"Possible (alliance open)","LOCA_NETWORK_ALLY_SLOTS_CLOSE":"Impossible (alliance closed)","LOCA_NETWORK_ALLY_RENAME_FOUNDERNAME":"Rename founder title as","LOCA_ALLY_RENAME_NEWCOMER":"Rename Newcomer rank","LOCA_NETWORK_ALLY_TAGNAME_EDIT":"Change alliance tag\/name","LOCA_NETWORK_ALLY_TAG_EDIT":"Change alliance tag","LOCA_NETWORK_ALLY_NAME_EDIT":"Change alliance name","LOCA_NETWORK_ALLY_TAG_NOW":"Former alliance tag","LOCA_NETWORK_ALLY_TAG_NEW":"New alliance tag","LOCA_NETWORK_ALLY_NAME_NOW":"Former alliance name","LOCA_NETWORK_ALLY_NAME_NEW":"New alliance name","LOCA_NETWORK_ALLY_DELETE_PASSON":"Delete alliance\/Pass alliance on","LOCA_NETWORK_ALLY_DELETE":"Delete this alliance","LOCA_ALLY_HANDOVER":"Handover alliance","LOCA_ALLY_TAKEOVER":"Take over alliance","LOCA_ALL_BUTTON_FORWARD":"Continue","LOCA_ALLY_TRANSFER_LONG":"Abandon this alliance?","LOCA_NETWORK_ALLY_CHANGE_FOUNDER":"Transfer the founder title to:","LOCA_ALLY_ERROR_NO_TRANSFER_MEMBERS":"None of the members have the required `right hand` right. You cannot hand over the alliance.","LOCA_ALLY_TAKEOVER_LONG":"Take over this alliance?","LOCA_NETWORK_ALLY_ERROR_FOUNDER_ACTIVE":"The founder is not inactive long enough in order to take over the alliance.","LOCA_ALL_OK":"Ok","LOCA_ALL_CANCEL":"Cancel","LOCA_ALLIANCE_CLASS_SELECTION_HEADER":"Class Selection","LOCA_ALLIANCE_CLASS_SELECTION_HEAD":"Select alliance class","LOCA_ALLIANCE_CLASS_SELECTION_NOTE":"Select an alliance class to receive special bonuses. You can change the alliance class in the alliance menu, provided you have the requisite permissions.","LOCA_ALL_NETWORK_ATTENTION":"Caution","LOCA_ALL_YES":"yes","LOCA_ALL_NO":"No","LOCA_NETWORK_ALLY_TAKEOVER_ARE_YOU_SURE":"Are you sure you want to pass on your alliance?","LOCA_NETWORK_ALLY_GIVEUP":"Really delete alliance?","LOCA_ALLY_TAKEOVER_QUESTION":"Are you sure that you want to take over this alliance?","LOCA_NETWORK_LEAVE_ALLY":"Leave alliance","locaAllyNameCharacter":"Alliance-Tag (3-30 characters)","locaAllyTagCharacter":"Alliance-Name (3-8 characters)","LOCA_NETWORK_ALLY_NEWALLYNAME":"Alliance name (3-30 characters)","LOCA_NETWORK_ALLY_NEWTAG":"Alliance Tag (3-8 characters)","LOCA_ALLIANCE_CLASS_SELECTION_BUTTON_DEACTIVATE":"Deactivate","LOCA_ALLIANCE_CLASS_NOTE_ACTIVATE_WITH_DM":"Do you want to activate the alliance class #allianceClassName# for #darkmatter# Dark Matter? In doing so, you will lose your current alliance class.","LOCA_ALLIANCE_CLASS_NOTE_ACTIVATE_WITH_ITEM":"Do you want to activate the alliance class #allianceClassName#? In doing so, you will lose your current alliance class.","LOCA_ALLIANCE_CLASS_NOTE_DEACTIVATE":"Do you really want to deactivate the alliance class #allianceClassName#? Reactivation requires an alliance class change item for 500,000 Dark Matter.","LOCA_ALLIANCE_CLASS_NOTE_ACTIVATE_APPEND_CURRENT_CLASS":"<br><br>Current alliance class: #currentAllianceClassName#<br><br>Last changed on: #lastAllianceClassChange#","LOCA_ALLIANCE_CLASS":"Alliance Class","LOCA_ALL_NOTICE":"Reference","LOCA_ALL_ERROR_LACKING_DM":"Not enough Dark Matter available! Do you want to buy some now?","LOCA_CHARACTERCLASS_SELECTION_BUTTON_DEACTIVATE":"Deactivate","LOCA_ALLIANCE_CREATION_DATE":"Created","LOCA_SETTINGS_SELECT_LANGUAGE":"Language:"};
            var alliance = new Alliance(window)
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
