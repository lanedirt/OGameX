<?php

namespace OGame\Services;

/**
 * Class BbCodeParserService.
 *
 * Converts BBCode to safe HTML. Input is escaped with htmlspecialchars() first
 * to prevent XSS, then BBCode tags are converted to HTML.
 *
 * @package OGame\Services
 */
class BbCodeParserService
{
    /**
     * Parse BBCode string to HTML.
     *
     * @param string $text
     * @return string
     */
    public function parse(string $text): string
    {
        if (empty($text)) {
            return '';
        }

        // Escape HTML first to prevent XSS.
        $html = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

        // BBCode replacements (same tags as client-side parser).
        $patterns = [
            '/\[b\](.*?)\[\/b\]/is' => '<strong style="font-weight:bold">$1</strong>',
            '/\[i\](.*?)\[\/i\]/is' => '<em style="font-style:italic">$1</em>',
            '/\[u\](.*?)\[\/u\]/is' => '<span style="text-decoration:underline">$1</span>',
            '/\[s\](.*?)\[\/s\]/is' => '<span style="text-decoration:line-through">$1</span>',
            '/\[sup\](.*?)\[\/sup\]/is' => '<sup>$1</sup>',
            '/\[sub\](.*?)\[\/sub\]/is' => '<sub>$1</sub>',
            '/\[color=([a-zA-Z0-9#]+)\](.*?)\[\/color\]/is' => '<span style="color:$1">$2</span>',
            '/\[size=(\d+)\](.*?)\[\/size\]/is' => '<span style="font-size:$1px">$2</span>',
            '/\[url=(.*?)\](.*?)\[\/url\]/is' => '<a href="$1" target="_blank" rel="noopener noreferrer" style="color:#6f9fc8;text-decoration:underline">$2</a>',
            '/\[url\](.*?)\[\/url\]/is' => '<a href="$1" target="_blank" rel="noopener noreferrer" style="color:#6f9fc8;text-decoration:underline">$1</a>',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $html = preg_replace($pattern, $replacement, $html);
        }

        // Convert newlines to <br>.
        $html = nl2br($html);

        return $html;
    }
}
