@extends('ingame.layouts.main')

@section('content')
    <div id="alliancecomponent" class="maincontent">
        <div id="netz">
            <div id="alliance">
                <div id="inhalt">
                    <div id="planet" class="planet-header">
                        <h2>[{{ $alliance->tag }}] {{ $alliance->name }}</h2>
                        <a class="toggleHeader" href="javascript:void(0);" data-name="alliance">
                            <img alt="" src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="22" width="22">
                        </a>
                    </div>
                    <div class="c-left"></div>
                    <div class="c-right"></div>

                    <div class="alliance_wrapper">
                        <div class="allianceContent">
                            <div class="contentz">
                                @if(session('success'))
                                    <table class="members" width="100%" cellpadding="0" cellspacing="1" style="margin-bottom: 10px;">
                                        <tr>
                                            <td style="color: #6f9;">{{ session('success') }}</td>
                                        </tr>
                                    </table>
                                @endif

                                @if(session('error'))
                                    <table class="members" width="100%" cellpadding="0" cellspacing="1" style="margin-bottom: 10px;">
                                        <tr>
                                            <td style="color: #f66;">{{ session('error') }}</td>
                                        </tr>
                                    </table>
                                @endif

                                <table class="members" width="100%" cellpadding="0" cellspacing="1">
                                    <tr>
                                        <th colspan="2">[{{ $alliance->tag }}] {{ $alliance->name }}</th>
                                    </tr>
                                    @if($alliance->logo)
                                    <tr>
                                        <td colspan="2" style="text-align: center; padding: 10px;">
                                            <img src="{{ $alliance->logo }}" alt="{{ $alliance->name }} Logo" style="max-width: 200px;">
                                        </td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td style="width: 200px;">Tag:</td>
                                        <td>[{{ $alliance->tag }}]</td>
                                    </tr>
                                    <tr class="alt">
                                        <td>Name:</td>
                                        <td>{{ $alliance->name }}</td>
                                    </tr>
                                    <tr>
                                        <td>Founder:</td>
                                        <td>{{ $alliance->founder->username }}</td>
                                    </tr>
                                    <tr class="alt">
                                        <td>Members:</td>
                                        <td>{{ $memberCount }}</td>
                                    </tr>
                                    @if($alliance->external_url)
                                    <tr>
                                        <td>Homepage:</td>
                                        <td><a href="{{ $alliance->external_url }}" target="_blank" rel="noopener">{{ $alliance->external_url }}</a></td>
                                    </tr>
                                    @endif
                                </table>

                                @if($alliance->description)
                                    <div style="margin: 20px 0;">
                                        <table class="members" width="100%" cellpadding="0" cellspacing="1">
                                            <tr>
                                                <th>Description</th>
                                            </tr>
                                            <tr>
                                                <td><div id="allyText">{{ $alliance->description }}</div></td>
                                            </tr>
                                        </table>
                                    </div>
                                @endif

                                @if($alliance->application_text && !$isMember)
                                    <div style="margin: 20px 0;">
                                        <table class="members" width="100%" cellpadding="0" cellspacing="1">
                                            <tr>
                                                <th>Application Information</th>
                                            </tr>
                                            <tr>
                                                <td><div id="allyText">{{ $alliance->application_text }}</div></td>
                                            </tr>
                                        </table>
                                    </div>
                                @endif

                                <div style="margin-top: 10px;">
                                    @if($isMember)
                                        <table class="members" width="100%" cellpadding="0" cellspacing="1">
                                            <tr>
                                                <td style="text-align: center; padding: 15px;">
                                                    <a href="{{ route('alliance.index') }}" class="btn_blue">View Your Alliance</a>
                                                </td>
                                            </tr>
                                        </table>
                                    @elseif($hasPendingApplication)
                                        <table class="members" width="100%" cellpadding="0" cellspacing="1">
                                            <tr>
                                                <td style="color: #6cf; text-align: center; padding: 10px;">You have a pending application to this alliance.</td>
                                            </tr>
                                        </table>
                                    @elseif($canApply)
                                        <table class="members" width="100%" cellpadding="0" cellspacing="1">
                                            <tr>
                                                <th>Apply to this Alliance</th>
                                            </tr>
                                            <tr>
                                                <td style="padding: 15px;">
                                                    <form action="{{ route('alliance.apply', $alliance->id) }}" method="POST">
                                                        @csrf
                                                        <div style="margin-bottom: 10px;">
                                                            <label>Application Message (optional):</label><br>
                                                            <textarea name="application_text" rows="5" cols="50"></textarea>
                                                        </div>
                                                        <div style="text-align: center;">
                                                            <button type="submit" class="btn_blue">Submit Application</button>
                                                        </div>
                                                    </form>
                                                </td>
                                            </tr>
                                        </table>
                                    @else
                                        <table class="members" width="100%" cellpadding="0" cellspacing="1">
                                            <tr>
                                                <td style="color: #fc6; text-align: center; padding: 10px;">This alliance is not currently accepting applications.</td>
                                            </tr>
                                        </table>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="new_footer"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
