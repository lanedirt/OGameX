@extends('ingame.layouts.main')

@section('content')
    <div id="alliancecomponent" class="maincontent">
        <div id="netz">
            <div id="alliance">
                <div id="inhalt">
                    <div id="planet" class="planet-header">
                        <h2>Search Alliance</h2>
                        <a class="toggleHeader" href="javascript:void(0);" data-name="alliance">
                            <img alt="" src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="22" width="22">
                        </a>
                    </div>
                    <div class="c-left"></div>
                    <div class="c-right"></div>

                    <div class="alliance_wrapper">
                        <div class="allianceContent">
                            <div class="contentz">
                                <form action="{{ route('alliance.search') }}" method="GET">
                                    <div style="margin-bottom: 20px;">
                                        <input type="text" name="query" class="text w290" placeholder="Search by tag or name..." value="{{ $query }}">
                                        <button type="submit" class="btn_blue">Search</button>
                                    </div>
                                </form>

                                @if($query)
                                    <h3>Search Results for "{{ $query }}"</h3>

                                    @if($results->isEmpty())
                                        <p>No alliances found matching your search.</p>
                                    @else
                                        <div id="section12" style="margin: 20px 0;">
                                            <table class="members" width="100%" cellpadding="0" cellspacing="1">
                                                <tr>
                                                    <th>Tag</th>
                                                    <th>Name</th>
                                                    <th>Founder</th>
                                                    <th>Members</th>
                                                    <th>Actions</th>
                                                </tr>
                                                @foreach($results as $index => $alliance)
                                                    <tr class="{{ $index % 2 == 1 ? 'alt' : '' }}">
                                                        <td>[{{ $alliance->tag }}]</td>
                                                        <td>{{ $alliance->name }}</td>
                                                        <td>{{ $alliance->founder->username }}</td>
                                                        <td>{{ $alliance->members_count }}</td>
                                                        <td>
                                                            <a href="{{ route('alliance.show', $alliance->id) }}" class="btn_blue" style="padding: 2px 8px;">View</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="new_footer"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
