<div class="searchLayer">
    <div class="messagebox">
        <div id="netz">
            <div id="message">
                <div id="inhalt">
                    <div class="sectioncontent" id="section41" style="display:block;">
                        <div class="contentz">
                            <table cellpadding="0" cellspacing="0" class="searchall">
                                <tbody><tr>
                                    <td class="textCenter" style="padding-bottom:10px;">
                                        Put in player, alliance or planet name
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ptb10 textCenter">
                                        <form id="searchForm" name="search" action="javascript:void(0);" onsubmit="return false;" method="POST">
                                            <input type="search" id="searchText" name="searchtext" class="textInput" value="">
                                            <input type="submit" value="Search" name="search" class="btn_blue buttonSave"><p>
                                            </p></form>
                                    </td>
                                </tr>
                                </tbody></table>
                            <div class="searchTabs">
                                <ul class="tabsbelow subsection_tabs" id="tabs_example_one">
                                    <li class="{{ request('category') == 2 || !request('category') ? 'ui-tabs-active' : '' }}">
                                        <a href="#one" class="tab" data-category="2">
                                            <span>
                                               Player names
                                            </span>
                                        </a>
                                    </li>
                                    <li class="{{ request('category') == 4 ? 'ui-tabs-active' : '' }}">
                                        <a href="#two" class="tab" data-category="4">
                                            <span>
                                               Alliances/Tags
                                            </span>
                                        </a>
                                    </li>
                                    <li class="{{ request('category') == 3 ? 'ui-tabs-active' : '' }}">
                                        <a href="#three" class="tab" data-category="3">
                                            <span>
                                               Planet names
                                            </span>
                                        </a>
                                    </li>
                                </ul>

                                <div class="ajaxContent">
                                    <br>
                                    <p class="textCenter">No search term entered</p>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
(function($) {
    let currentCategory = {{ request('category', 2) }};

    // Tab switching
    $('.searchTabs .tab').on('click', function(e) {
        e.preventDefault();
        currentCategory = $(this).data('category');

        // Update active tab
        $('.searchTabs li').removeClass('ui-tabs-active');
        $(this).parent().addClass('ui-tabs-active');

        // Clear results if search text is empty
        if ($('#searchText').val().trim() === '') {
            $('.ajaxContent').html('<br><p class="textCenter">No search term entered</p>');
        } else {
            // Re-run search with new category
            performSearch();
        }
    });

    // Search form submission
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        performSearch();
    });

    function performSearch() {
        const searchText = $('#searchText').val().trim();

        if (searchText === '') {
            $('.ajaxContent').html('<br><p class="textCenter">No search term entered</p>');
            return;
        }

        // Show loading indicator
        $('.ajaxContent').html('<br><p class="textCenter">Searching...</p>');

        $.ajax({
            url: '{{ route('search.ajax') }}',
            type: 'POST',
            data: {
                searchtext: searchText,
                category: currentCategory,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.status === 'error') {
                    $('.ajaxContent').html('<br><p class="textCenter">' + response.message + '</p>');
                    return;
                }

                displayResults(response.results, response.category);
            },
            error: function() {
                $('.ajaxContent').html('<br><p class="textCenter">Search failed. Please try again.</p>');
            }
        });
    }

    function displayResults(results, category) {
        if (results.length === 0) {
            $('.ajaxContent').html('<br><p class="textCenter">No results found</p>');
            return;
        }

        let html = '';

        if (category === 2) {
            // Player results
            html += '<table class="searchresults" cellpadding="0" cellspacing="0">';
            html += '<tbody><tr><th>Player Name</th></tr>';
            results.forEach(function(result) {
                html += '<tr><td>' + escapeHtml(result.name) + '</td></tr>';
            });
            html += '</tbody></table>';
        } else if (category === 3) {
            // Planet results
            html += '<table class="searchresults" cellpadding="0" cellspacing="0">';
            html += '<tbody><tr><th>Planet Name</th><th>Coordinates</th></tr>';
            results.forEach(function(result) {
                html += '<tr><td>' + escapeHtml(result.name) + '</td><td>' + escapeHtml(result.coordinates) + '</td></tr>';
            });
            html += '</tbody></table>';
        } else if (category === 4) {
            // Alliance results
            html += '<table cellpadding="0" cellspacing="0" class="searchresults">';
            html += '<tbody><tr>';
            html += '<th class="allyTag">{{ __('Tag') }}</th>';
            html += '<th class="allyName">{{ __('Alliance name') }}</th>';
            html += '<th class="allyMembers">{{ __('Member') }}</th>';
            html += '<th class="allyPoints">{{ __('Points') }}</th>';
            html += '<th class="action">{{ __('Action') }}</th>';
            html += '</tr>';

            let rowClass = 'alt';
            results.forEach(function(result) {
                const infoUrl = '{{ route('alliance.info', ['alliance_id' => '__ID__']) }}'.replace('__ID__', result.id);
                const highscoreUrl = '{{ route('highscore.index') }}?searchRelId=' + result.id + '&category=2&type=0';
                const applyUrl = '{{ route('alliance.index') }}?alliance_id=' + result.id;

                // Format points with thousands separators
                const formattedPoints = new Intl.NumberFormat('en-US').format(result.points);

                html += '<tr class="' + rowClass + '">';
                html += '<td class="allyTag">';
                html += '<a class="dark_highlight_tablet" target="_ally" href="' + infoUrl + '">' + escapeHtml(result.tag) + '</a>';
                html += '</td>';
                html += '<td class="allyName">';
                html += '<a class="dark_highlight_tablet alliance_class small none" target="_ally" href="' + infoUrl + '">' + escapeHtml(result.name) + '</a>';
                html += '</td>';
                html += '<td class="allyMembers">' + result.member_count + '</td>';
                html += '<td class="allyPoints">';
                html += '<a class="dark_highlight_tablet" target="_parent" href="' + highscoreUrl + '">' + formattedPoints + '</a>';
                html += '</td>';
                html += '<td class="action">';

                // Only show apply link if user is not in an alliance AND alliance is open
                @if(!auth()->user()->alliance_id)
                if (result.is_open) {
                    html += '<a title="{{ __('Apply for this alliance') }}" class="tooltip js_hideTipOnMobile icon" href="' + applyUrl + '">';
                    html += '<span class="icon icon_mail"></span>';
                    html += '</a>';
                }
                @endif

                html += '</td>';
                html += '</tr>';

                // Alternate row class
                rowClass = (rowClass === 'alt') ? '' : 'alt';
            });

            html += '<tr><th colspan="5"></th></tr>';
            html += '<tr><td colspan="5" class="pagebar" align="right"><p></p></td></tr>';
            html += '</tbody></table>';
        }

        $('.ajaxContent').html(html);
    }

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
})(jQuery);
</script>
