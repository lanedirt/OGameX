@php /** @var OGame\Services\PlayerService $currentPlayer */ @endphp
<div id="adminbar">
    <style>
        #adminbar {
            background: transparent url('/img/admin/admin-menu-bg.jpg') repeat-x;
            font: normal 11px Tahoma, Arial, Helvetica, sans-serif;
            height: 32px;
            left: 0;
            padding: 0;
            text-align: center;
            top: 0;
            width: 100%;
            z-index: 3000;
        }

        #adminbar #mmoContent {
            height: 32px;
            margin: 0 auto;
            width: 990px;
            position: relative;
        }

        #adminbar #adminLogo {
            float: left;
            display: block;
            height: 32px;
            width: auto;
            padding: 5px 10px;
            padding-left: 0;
            font-size: 14px;
            color: #f48406 !important;
            font-weight: bold;
        }

        #adminbar #adminLogo span {
            font-size: 18px;
            vertical-align: middle;
        }

        #adminbar ul {
            list-style: none;
            margin-top: 8px;
            padding: 0;
            float: right;
        }

        #adminbar ul li {
            display: inline;
            margin-right: 10px;
        }

        #adminbar ul li a {
            color: #fff;
            background-color: #333;
            padding: 3px 10px;
            text-decoration: none;
            border-radius: 3px;
            font-size: 11px;
        }

        #adminbar ul li a:hover, #adminbar ul li a.active  {
            background-color: #555;
        }
    </style>
    <div id="mmoContent">
        <div id="adminLogo">
            Server admin
        </div>
        <ul>
            <li><a class="{{(Request::is('admin/developer-shortcuts') ? 'active' : '') }}" href="{{ route('admin.developershortcuts.index') }}">Developer shortcuts</a></li>
            <li><a class="{{(Request::is('admin/server-settings') ? 'active' : '') }}" href="{{ route('admin.serversettings.index') }}">Server settings</a></li>
        </ul>
    </div>
</div>
