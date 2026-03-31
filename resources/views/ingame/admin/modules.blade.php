@extends('ingame.layouts.main')

@section('content')
    <div id="resourcesettingscomponent" class="maincontent">
        <div id="planet" class="shortHeader">
            <h2>@lang('Modules')</h2>
        </div>

        <div id="buttonz">
            <div class="header">
                <h2>@lang('Installed Modules')</h2>
            </div>

            @if (session('success'))
                <div class="alert alert-success" style="padding:8px 12px; margin:10px 0; background:#2a4a2a; color:#8fc88f; border:1px solid #4a7a4a; border-radius:4px;">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger" style="padding:8px 12px; margin:10px 0; background:#4a2a2a; color:#c88f8f; border:1px solid #7a4a4a; border-radius:4px;">
                    {{ session('error') }}
                </div>
            @endif

            @if (empty($modules))
                <p style="padding: 15px; color: #aaa;">No modules are declared in <code>config/modules.php</code>.</p>
            @else
                <table style="width:100%; border-collapse:collapse; margin-top:10px;">
                    <thead>
                        <tr style="background:#1a1a2a; color:#aaa; font-size:11px; text-transform:uppercase;">
                            <th style="padding:8px 12px; text-align:left;">Module</th>
                            <th style="padding:8px 12px; text-align:left;">Version</th>
                            <th style="padding:8px 12px; text-align:left;">Description</th>
                            <th style="padding:8px 12px; text-align:left;">Status</th>
                            <th style="padding:8px 12px; text-align:left;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($modules as $module)
                            <tr style="border-bottom:1px solid #2a2a3a; {{ $loop->even ? 'background:#111118;' : '' }}">
                                <td style="padding:10px 12px;">
                                    <strong style="color:#e0a040;">{{ $module['name'] }}</strong>
                                    @if ($module['missing'])
                                        <br><small style="color:#c87070;">Class not found: {{ $module['provider'] }}</small>
                                    @else
                                        <br><small style="color:#888;">{{ $module['id'] }}</small>
                                    @endif
                                </td>
                                <td style="padding:10px 12px; color:#aaa;">{{ $module['version'] }}</td>
                                <td style="padding:10px 12px; color:#ccc; font-size:12px;">{{ $module['description'] ?: '—' }}</td>
                                <td style="padding:10px 12px;">
                                    @if ($module['missing'])
                                        <span style="color:#c87070;">&#x2717; Missing</span>
                                    @elseif ($module['enabled'])
                                        <span style="color:#70c870;">&#x2713; Enabled</span>
                                    @else
                                        <span style="color:#888;">&#x2717; Disabled</span>
                                    @endif
                                </td>
                                <td style="padding:10px 12px;">
                                    @unless ($module['missing'])
                                        <form action="{{ route('admin.modules.toggle') }}" method="POST" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="provider" value="{{ $module['provider'] }}">
                                            <button type="submit"
                                                    style="padding:4px 12px; border:none; border-radius:3px; cursor:pointer; font-size:11px;
                                                           {{ $module['enabled'] ? 'background:#7a3030; color:#f0a0a0;' : 'background:#2a5a2a; color:#a0f0a0;' }}">
                                                {{ $module['enabled'] ? 'Disable' : 'Enable' }}
                                            </button>
                                        </form>
                                    @endunless
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <p style="margin-top:16px; padding:0 12px; font-size:11px; color:#666;">
                    Changes take effect on the next page load. To permanently add or remove modules, edit <code>config/modules.php</code>.
                </p>
            @endif
        </div>
    </div>
@endsection
