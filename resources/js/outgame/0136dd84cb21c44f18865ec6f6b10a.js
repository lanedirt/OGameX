window.ogame.characteristics = {
    characteristicsMapping: null,
    activeFilters: [],

    init: function(characteristicsMapping){
        window.ogame.characteristics.characteristicsMapping = characteristicsMapping;

        $('body').on('mouseenter', '#characteristicsTooltip', function(){
            window.ogame.characteristics.cancelFadeOutCharacteristics();
        });

        $('body').on('mouseleave', '#characteristicsTooltip', function(){
            window.ogame.characteristics.fadeOutCharacteristics();
        });

        $("#uni_selection > div").mouseenter(function(){
            var tooltip = $(this).data('tooltip');

            if ((tooltip !== null) && (tooltip instanceof Object)) {
                window.ogame.characteristics.showTooltip(tooltip);
            } else {
                $('#characteristicsTooltip').remove();
            }
        });

        $("#uni_selection").mouseleave(function(){
            window.ogame.characteristics.fadeOutCharacteristics();
        });

        $('body').on('click', '.tabContent .filter .characteristic', function(){
            $(this).toggleClass('filter_off');
            var state = !$(this).hasClass('filter_off');

            if (state) {
                window.ogame.characteristics.activeFilters.push('filter_' + $(this).data('filter'));
                $(this).children().show();
            } else {
                var index = window.ogame.characteristics.activeFilters.indexOf('filter_' + $(this).data('filter'));
                window.ogame.characteristics.activeFilters.splice(index, 1);
                $(this).children().hide();
            }

            window.ogame.characteristics.updateFilteredServers();
        });
    },

    intersection: function() {
        return window.ogame.characteristics.toArray(arguments).reduce(function(previous, current) {
            return previous.filter(function(element) {
                return current.indexOf(element) > -1;
            });
        });
    },

    updateFilteredServers: function() {
        $('li.server').each(function() {
            var activeFilters = (window.ogame.characteristics.activeFilters).slice(0);
            var classList     = (window.ogame.characteristics.toArray(this.classList)).slice(0);
            var filterCheck   = window.ogame.characteristics.intersection(activeFilters, classList);

            if (filterCheck.length == activeFilters.length) {
                $(this).removeClass('hidden');
            } else {
                $(this).addClass('hidden');
            }
        });
    },

    toArray: function(obj) {
      var array = [];
      // iterate backwards ensuring that length is an UInt32
      for (var i = obj.length >>> 0; i--;) {
        array[i] = obj[i];
      }
      return array;
    },

    getTooltipLi: function(tooltip, map) {
        if (map.hasOwnProperty('condition')) {
            if (!(eval(map.condition))) {
                return;
            }
        }

        var valueAppendix = '';

        if (map.hasOwnProperty('valueAppendix')) {
            valueAppendix = map.valueAppendix;
        }

        var li = $('<li/>', {
            class: "characteristic " + map.css
        });

        var value = $('<span/>', {
            class: 'value'
        }).html(tooltip[map.valueCategory][map.valueKey] + valueAppendix);

        value.appendTo(li);

        var span = $('<span/>').html(
            map.text.replace(
                '#value#',
                '<strong>' + tooltip[map.valueCategory][map.valueKey] + valueAppendix + '</strong>'
            )
        );

        span.appendTo(li);

        return li;
    },

    showTooltip: function(tooltip) {
        window.ogame.characteristics.cancelFadeOutCharacteristics();
        var container = $('#characteristicsTooltip');
        container.remove();

        container = $('<div/>', {
            id: 'characteristicsTooltip',
            class: 'container'
        });

        var ttList = $('<ul/>', {
            class: 'tooltip element'
        });

        var header = $('<li/>', {
            id: 'tooltip_header',
            class: 'content_header'
        }).html('Server: ' + tooltip.general.name + '<br/>' + tooltip.exodusInfo);

        var body = $('<ul/>', {
            id: 'tooltip_body',
            class: 'indented'
        });

        for (var e in window.ogame.characteristics.characteristicsMapping) {
            var newElem = window.ogame.characteristics.getTooltipLi(
                tooltip,
                window.ogame.characteristics.characteristicsMapping[e]
            );

            if (newElem) {
                body.append(newElem);
            }
        }

        container.append(ttList);

        ttList.append(header)
            .append(body);

        container.appendTo('#subscribeForm');
    },

    fadeOutCharacteristics: function () {
        $('#characteristicsTooltip').fadeOut(2000, function() {
            $('#characteristicsTooltip').remove();
        });
    },

    cancelFadeOutCharacteristics: function() {
        $('#characteristicsTooltip').stop(true).fadeIn(0);
    },

    updateFilterFlags: function(filter, min, max) {
        $('.' + filter).each(function(){
            var value = $(this).data('value');
            var parent = $(this).parent().parent();

            if (min <= value && value <= max) {
                parent.addClass('filter_' + filter);
            } else {
                parent.removeClass('filter_' + filter);
            }
        });

        window.ogame.characteristics.updateFilteredServers();
    }
};