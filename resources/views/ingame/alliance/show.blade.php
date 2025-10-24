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
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                @if(session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif

                                @if($alliance->logo)
                                    <div style="margin: 10px 0;">
                                        <img src="{{ $alliance->logo }}" alt="{{ $alliance->name }} Logo" style="max-width: 200px;">
                                    </div>
                                @endif

                                <div style="margin: 15px 0;">
                                    <p><strong>Tag:</strong> [{{ $alliance->tag }}]</p>
                                    <p><strong>Name:</strong> {{ $alliance->name }}</p>
                                    <p><strong>Founder:</strong> {{ $alliance->founder->username }}</p>
                                    <p><strong>Members:</strong> {{ $memberCount }}</p>

                                    @if($alliance->external_url)
                                        <p><strong>Homepage:</strong> <a href="{{ $alliance->external_url }}" target="_blank" rel="noopener">{{ $alliance->external_url }}</a></p>
                                    @endif
                                </div>

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

                                <div style="margin-top: 30px; text-align: center;">
                                    @if($isMember)
                                        <a href="{{ route('alliance.index') }}" class="btn_blue">View Your Alliance</a>
                                    @elseif($hasPendingApplication)
                                        <p class="alert alert-info">You have a pending application to this alliance.</p>
                                    @elseif($canApply)
                                        <table class="members" width="100%" cellpadding="0" cellspacing="1">
                                            <tr>
                                                <th>Apply to this Alliance</th>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <form action="{{ route('alliance.apply', $alliance->id) }}" method="POST" style="margin: 15px 0;">
                                                        @csrf
                                                        <div style="margin-bottom: 10px;">
                                                            <label>Application Message (optional):</label><br>
                                                            <textarea name="application_text" class="alliancetexts" rows="5"></textarea>
                                                        </div>
                                                        <button type="submit" class="btn_blue">Submit Application</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        </table>
                                    @else
                                        <p class="alert alert-warning">This alliance is not currently accepting applications.</p>
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
