{{-- BBCode parser for buddy request previews --}}
<script type="text/javascript">
    // BBCode parser for buddy request previews
    window.buddyBBCodeParser = function(text) {
        if (!text) return '';
        var html = text
            .replace(/\[b\](.*?)\[\/b\]/gi, '<strong style="font-weight:bold">$1</strong>')
            .replace(/\[i\](.*?)\[\/i\]/gi, '<em style="font-style:italic">$1</em>')
            .replace(/\[u\](.*?)\[\/u\]/gi, '<span style="text-decoration:underline">$1</span>')
            .replace(/\[s\](.*?)\[\/s\]/gi, '<span style="text-decoration:line-through">$1</span>')
            .replace(/\[sup\](.*?)\[\/sup\]/gi, '<sup>$1</sup>')
            .replace(/\[sub\](.*?)\[\/sub\]/gi, '<sub>$1</sub>')
            .replace(/\[color=(.*?)\](.*?)\[\/color\]/gi, '<span style="color:$1">$2</span>')
            .replace(/\[size=(\d+)\](.*?)\[\/size\]/gi, '<span style="font-size:$1px">$2</span>')
            .replace(/\[url=(.*?)\](.*?)\[\/url\]/gi, '<a href="$1" target="_blank" style="color:#6f9fc8;text-decoration:underline">$2</a>')
            .replace(/\[url\](.*?)\[\/url\]/gi, '<a href="$1" target="_blank" style="color:#6f9fc8;text-decoration:underline">$1</a>')
            .replace(/\n/g, '<br>');
        return '<div style="color:#fff;padding:5px">' + html + '</div>';
    };
</script>
