@extends('ingame.layouts.main')

@section('content')
    <div id="alliancecomponent" class="maincontent">
        <div id="netz">
            <div id="alliance">
                <div id="inhalt">
                    <div id="planet" class="planet-header">
                        <h2>Create Alliance</h2>
                    </div>
                    <div class="c-left"></div>
                    <div class="c-right"></div>

                    <div class="alliance_wrapper">
                        <div class="allianceContent">
                            <div class="contentz">
                                @if ($errors->any())
                                    <table class="members" width="100%" cellpadding="0" cellspacing="1" style="margin-bottom: 10px;">
                                        <tr>
                                            <th>Validation Errors</th>
                                        </tr>
                                        @foreach ($errors->all() as $error)
                                            <tr>
                                                <td style="color: #f66;">{{ $error }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                @endif

                                <form action="{{ route('alliance.store') }}" method="POST">
                                    @csrf
                                    <table class="members" width="100%" cellpadding="0" cellspacing="1">
                                        <tr>
                                            <th colspan="2">Create New Alliance</th>
                                        </tr>
                                        <tr>
                                            <td style="width: 200px;">Alliance Tag (3-8 characters)</td>
                                            <td><input class="text w200" type="text" name="tag" id="allyTagField" maxlength="8" value="{{ old('tag') }}" required></td>
                                        </tr>
                                        <tr class="alt">
                                            <td>Alliance Name (3-64 characters)</td>
                                            <td><input class="text w200" type="text" name="name" id="allyNameField" maxlength="64" value="{{ old('name') }}" required></td>
                                        </tr>
                                        <tr>
                                            <td>Description (optional)</td>
                                            <td><textarea name="description" rows="5" cols="50">{{ old('description') }}</textarea></td>
                                        </tr>
                                        <tr class="alt">
                                            <td>Logo URL (optional)</td>
                                            <td><input class="text w200" type="url" name="logo" value="{{ old('logo') }}"></td>
                                        </tr>
                                        <tr>
                                            <td>Website URL (optional)</td>
                                            <td><input class="text w200" type="url" name="external_url" value="{{ old('external_url') }}"></td>
                                        </tr>
                                        <tr class="alt">
                                            <td>Application Text (optional)</td>
                                            <td><textarea name="application_text" rows="3" cols="50">{{ old('application_text') }}</textarea></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="text-align: center; padding: 15px;">
                                                <button type="submit" class="btn_blue">Create Alliance</button>
                                                <a href="{{ route('alliance.index') }}" class="btn_blue">Cancel</a>
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
