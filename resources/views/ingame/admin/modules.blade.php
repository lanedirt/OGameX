@extends('ingame.layouts.main')

@section('content')

    @if (session('success'))
        <script>fadeBox('{{ session('success') }}', false);</script>
    @endif

    @if (session('error'))
        <script>fadeBox('{{ session('error') }}', true);</script>
    @endif

    <div id="resourcesettingscomponent" class="maincontent">
        <div id="planet" class="shortHeader">
            <h2>@lang('Modules')</h2>
        </div>

        <div id="buttonz">
            <div class="header">
                <h2>@lang('Installed Modules')</h2>
            </div>
            <div class="content">
                <div class="buddylistContent" style="margin-bottom: 60px;">

                    <div style="width: 606px; margin: 0 auto;">
                    <span class="fleft" style="padding: 8px 0 0 5px; color: #aaa;">
                        <span style="color: #8f8;">{{ $enabledCount }}</span> enabled
                        &nbsp;/&nbsp;
                        <span style="color: #888;">{{ $disabledCount }}</span> disabled
                    </span>
                    <input class="fright textInput w200" id="moduleSearch" type="text" placeholder="Search modules...">
                    <br class="clearfloat">

                    @if (empty($modules))
                        <p class="box_highlight textCenter no_buddies">
                            No modules installed. Create one with <code>php artisan module:make Blog</code>.
                        </p>
                    @else
                            <table cellpadding="0" cellspacing="0" class="content_table" id="modulelist"
                                   style="table-layout: fixed; border-left: 0; border-right: 0;">
                                <colgroup>
                                    <col span="1" style="width: 6%;">
                                    <col span="1" style="width: 32%;">
                                    <col span="1" style="width: 12%;">
                                    <col span="1" style="width: 12%;">
                                    <col span="1" style="width: 20%;">
                                    <col span="1" style="width: 18%;">
                                </colgroup>
                            <thead>
                            <tr class="ct_head_row">
                                <th class="no ct_th first">#</th>
                                <th class="ct_th">Module</th>
                                <th class="ct_th">Version</th>
                                <th class="ct_th">Priority</th>
                                <th class="ct_th">Status</th>
                                <th class="ct_th textCenter">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="zebra">
                                @foreach ($modules as $module)
                                    @php
                                        $rowClass = $loop->index % 2 === 0 ? 'odd' : 'even';
                                    @endphp
                                    <tr class="{{ $rowClass }}">
                                        <td class="no ct_td">{{ $loop->iteration }}.</td>
                                        <td class="ct_td">
                                            <span class="fleft" title="{{ $module['description'] }}">
                                                {{ $module['name'] }}
                                                <span style="color: #888; font-size: 10px;">{{ $module['alias'] }}</span>
                                            </span>
                                        </td>
                                        <td class="ct_td">{{ $module['version'] }}</td>
                                        <td class="ct_td">{{ $module['priority'] }}</td>
                                        <td class="ct_td">
                                            @if ($module['enabled'])
                                                <span class="tooltip fleft playerstatus online" data-tooltip-title="Enabled"></span>
                                                <span class="fleft" style="color: #8f8;">Enabled</span>
                                            @else
                                                <span class="tooltip fleft playerstatus offline" data-tooltip-title="Disabled"></span>
                                                <span class="fleft" style="color: #999;">Disabled</span>
                                            @endif
                                        </td>
                                        <td class="ct_td textCenter">
                                            <form action="{{ route('admin.modules.toggle') }}" method="POST" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="module" value="{{ $module['name'] }}">
                                                <input type="submit" class="btn_blue"
                                                       value="{{ $module['enabled'] ? 'Disable' : 'Enable' }}"
                                                       style="font-size: 11px; padding: 2px 8px;"
                                                       @if ($module['enabled']) onclick="return confirm('Disable {{ $module['name'] }}? Disabling immediately stops its routes, views and services.')" @endif>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            </table>
                    @endif

                    <div class="smallFont" style="margin-top: 10px;">
                        Module state is stored in <code>modules_statuses.json</code>. You can also manage modules via <code>php artisan module:enable|disable|list</code>.
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var input = document.getElementById('moduleSearch');
            if (!input) return;

            input.addEventListener('keyup', function () {
                var query = this.value.toLowerCase();
                document.querySelectorAll('#modulelist tbody tr').forEach(function (row) {
                    row.style.display = row.textContent.toLowerCase().indexOf(query) !== -1 ? '' : 'none';
                });
            });
        })();
    </script>
@endsection
