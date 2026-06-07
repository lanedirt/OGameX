<?php

namespace OGame\Console\Commands\I18n;

use Illuminate\Console\Command;
use OGame\Services\I18n\OGameLocale;

class MapKeysCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'i18n:map-keys
                            {--lang=en : Reference language whose files we walk to extract english source strings}
                            {--out-dir= : Output directory for mapped.json / unmapped_keys.csv / unmapped_keys.md (defaults to storage/i18n/)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Map every Laravel translation key in resources/lang/<lang>/*.php to the OGame master dictionary, producing mapped.json plus reports for unmapped (custom-fork) keys.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $lang = (string) ($this->option('lang') ?? 'en');
        $langDir = base_path('resources/lang/' . $lang);
        if (!is_dir($langDir)) {
            $this->error("Language directory not found: $langDir");
            return self::FAILURE;
        }

        if (OGameLocale::count() === 0) {
            $this->error('Master dictionary is empty. Run `php artisan i18n:build-dictionary` first.');
            return self::FAILURE;
        }

        $outDir = (string) ($this->option('out-dir') ?? '') ?: storage_path('i18n');
        if (!is_dir($outDir) && !mkdir($outDir, 0775, true) && !is_dir($outDir)) {
            $this->error("Unable to create output directory: $outDir");
            return self::FAILURE;
        }

        $files = glob($langDir . DIRECTORY_SEPARATOR . '*.php') ?: [];
        sort($files);

        $this->info(sprintf('Scanning %d file(s) under %s', count($files), $langDir));

        $mappedReport = [
            'generated_at' => date('c'),
            'reference_lang' => $lang,
            'dictionary_entries' => OGameLocale::count(),
            'stats' => [
                'files' => count($files),
                'total_keys' => 0,
                'mapped' => 0,
                'unmapped' => 0,
            ],
            'files' => [],
        ];

        $unmappedRows = [];

        foreach ($files as $file) {
            $basename = basename($file);
            $data = require $file;
            if (!is_array($data)) {
                $this->warn("Skipping $basename — does not return an array.");
                continue;
            }

            $flat = [];
            $this->flatten($data, '', $flat);

            $fileBlock = [
                'total' => count($flat),
                'mapped' => 0,
                'unmapped' => 0,
                'keys' => [],
            ];

            foreach ($flat as $dotted => $english) {
                $matched = OGameLocale::has($english);
                $fileBlock['keys'][$dotted] = [
                    'en' => $english,
                    'matched' => $matched,
                ];

                if ($matched) {
                    $fileBlock['mapped']++;
                    $mappedReport['stats']['mapped']++;
                } else {
                    $fileBlock['unmapped']++;
                    $mappedReport['stats']['unmapped']++;
                    $unmappedRows[] = [
                        'file' => $basename,
                        'key' => $dotted,
                        'en' => $english,
                    ];
                }

                $mappedReport['stats']['total_keys']++;
            }

            $mappedReport['files'][$basename] = $fileBlock;
        }

        $jsonPath = $outDir . DIRECTORY_SEPARATOR . 'mapped.json';
        $csvPath = $outDir . DIRECTORY_SEPARATOR . 'unmapped_keys.csv';
        $mdPath = $outDir . DIRECTORY_SEPARATOR . 'unmapped_keys.md';

        file_put_contents(
            $jsonPath,
            json_encode($mappedReport, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n"
        );

        $this->writeCsv($csvPath, $unmappedRows);
        $this->writeMarkdown($mdPath, $unmappedRows, $mappedReport['stats']);

        $this->info("Wrote $jsonPath");
        $this->info("Wrote $csvPath");
        $this->info("Wrote $mdPath");

        $this->table(
            ['metric', 'value'],
            [
                ['files scanned', $mappedReport['stats']['files']],
                ['total keys', $mappedReport['stats']['total_keys']],
                ['mapped to dictionary', $mappedReport['stats']['mapped']],
                ['unmapped (custom)', $mappedReport['stats']['unmapped']],
                [
                    'mapping rate',
                    $mappedReport['stats']['total_keys'] > 0
                        ? round(100 * $mappedReport['stats']['mapped'] / $mappedReport['stats']['total_keys'], 1) . '%'
                        : 'n/a',
                ],
            ]
        );

        return self::SUCCESS;
    }

    /**
     * Flatten a nested PHP lang array into a dotted-key map of leaf strings.
     *
     * Non-string leaves and empty strings are ignored — they cannot be
     * looked up in the dictionary.
     *
     * @param  array<int|string, mixed>  $data
     * @param  array<string, string>     $out
     */
    private function flatten(array $data, string $prefix, array &$out): void
    {
        foreach ($data as $key => $value) {
            $compoundKey = $prefix === '' ? (string) $key : $prefix . '.' . $key;
            if (is_array($value)) {
                $this->flatten($value, $compoundKey, $out);
                continue;
            }
            if (!is_string($value)) {
                continue;
            }
            $trimmed = trim($value);
            if ($trimmed === '') {
                continue;
            }
            $out[$compoundKey] = $value;
        }
    }

    /**
     * @param  array<int, array{file:string,key:string,en:string}>  $rows
     */
    private function writeCsv(string $path, array $rows): void
    {
        $fh = fopen($path, 'wb');
        if ($fh === false) {
            return;
        }
        fputcsv($fh, ['file', 'key', 'english_text']);
        foreach ($rows as $row) {
            fputcsv($fh, [$row['file'], $row['key'], $row['en']]);
        }
        fclose($fh);
    }

    /**
     * @param  array<int, array{file:string,key:string,en:string}>  $rows
     * @param  array{files:int,total_keys:int,mapped:int,unmapped:int}  $stats
     */
    private function writeMarkdown(string $path, array $rows, array $stats): void
    {
        $byFile = [];
        foreach ($rows as $row) {
            $byFile[$row['file']][] = $row;
        }
        ksort($byFile);

        $lines = [];
        $lines[] = '# Unmapped Translation Keys';
        $lines[] = '';
        $lines[] = 'Keys below exist in `resources/lang/en/*.php` but have no exact match in the OGame master dictionary. They are custom to this fork and need manual translation in PR 3+.';
        $lines[] = '';
        $lines[] = '## Summary';
        $lines[] = '';
        $lines[] = '| metric | value |';
        $lines[] = '|---|---|';
        $lines[] = '| files scanned | ' . $stats['files'] . ' |';
        $lines[] = '| total keys | ' . $stats['total_keys'] . ' |';
        $lines[] = '| mapped | ' . $stats['mapped'] . ' |';
        $lines[] = '| unmapped | ' . $stats['unmapped'] . ' |';
        $lines[] = '';

        foreach ($byFile as $file => $fileRows) {
            $lines[] = '## ' . $file . ' (' . count($fileRows) . ')';
            $lines[] = '';
            $lines[] = '| key | english text |';
            $lines[] = '|---|---|';
            foreach ($fileRows as $row) {
                $lines[] = '| `' . $row['key'] . '` | ' . $this->mdEscape($row['en']) . ' |';
            }
            $lines[] = '';
        }

        file_put_contents($path, implode("\n", $lines));
    }

    private function mdEscape(string $value): string
    {
        $value = str_replace(["\r\n", "\r", "\n"], ' ', $value);
        return str_replace('|', '\\|', $value);
    }
}
