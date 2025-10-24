@extends('ingame.layouts.main')

@section('content')
    <div id="alliancecomponent" class="maincontent">
        <div id="netz">
            <div id="alliance">
                <div id="inhalt">
                    <div id="planet" class="planet-header">
                        <h2>Manage Alliance</h2>
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

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form action="{{ route('alliance.update') }}" method="POST">
                                    @csrf
                                    <table class="members" width="100%" cellpadding="0" cellspacing="1">
                                        <tr>
                                            <th colspan="2">Manage Alliance Settings</th>
                                        </tr>
                                        <tr>
                                            <td style="width: 200px;">Alliance Name</td>
                                            <td><input type="text" name="name" class="text w200" value="{{ old('name', $alliance->name) }}" required></td>
                                        </tr>
                                        <tr class="alt">
                                            <td>Description</td>
                                            <td><textarea name="description" rows="5" cols="50">{{ old('description', $alliance->description) }}</textarea></td>
                                        </tr>
                                        <tr>
                                            <td>Logo URL</td>
                                            <td><input type="url" name="logo" class="text w200" value="{{ old('logo', $alliance->logo) }}"></td>
                                        </tr>
                                        <tr class="alt">
                                            <td>Website URL</td>
                                            <td><input type="url" name="external_url" class="text w200" value="{{ old('external_url', $alliance->external_url) }}"></td>
                                        </tr>
                                        <tr>
                                            <td>Internal Text</td>
                                            <td><textarea name="internal_text" rows="5" cols="50">{{ old('internal_text', $alliance->internal_text) }}</textarea></td>
                                        </tr>
                                        <tr class="alt">
                                            <td>Application Text</td>
                                            <td><textarea name="application_text" rows="3" cols="50">{{ old('application_text', $alliance->application_text) }}</textarea></td>
                                        </tr>
                                        <tr>
                                            <td>Open for Applications</td>
                                            <td>
                                                <input type="checkbox" name="open_for_applications" value="1" {{ old('open_for_applications', $alliance->open_for_applications) ? 'checked' : '' }}>
                                            </td>
                                        </tr>
                                        <tr class="alt">
                                            <td colspan="2" style="text-align: center; padding: 15px;">
                                                <button type="submit" class="btn_blue">Update Alliance</button>
                                                <a href="{{ route('alliance.index') }}" class="btn_blue">Back to Alliance</a>
                                            </td>
                                        </tr>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="new_footer"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
