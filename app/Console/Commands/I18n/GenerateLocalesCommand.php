<?php

namespace OGame\Console\Commands\I18n;

use Illuminate\Console\Command;
use OGame\Services\I18n\OGameLocale;
use RuntimeException;

class GenerateLocalesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'i18n:generate-locales
                            {--lang= : Comma-separated language codes to generate. Defaults to every OGameLocale::SUPPORTED_LANGUAGES entry that has no resources/lang/<code>/ directory yet}
                            {--reference=en : Reference language whose file structure we mirror}
                            {--force : Overwrite existing locale directories (use with care — destroys hand-tuned translations)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate per-language Laravel lang files for every OGame locale, mirroring the reference English structure and resolving leaves through the master dictionary.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (OGameLocale::count() === 0) {
            $this->error('Master dictionary is empty. Run `php artisan i18n:build-dictionary` first.');
            return self::FAILURE;
        }

        $reference = (string) ($this->option('reference') ?? 'en');
        $referenceDir = base_path('resources/lang/' . $reference);
        if (!is_dir($referenceDir)) {
            $this->error("Reference language directory not found: $referenceDir");
            return self::FAILURE;
        }

        $targets = $this->resolveTargets($reference);
        if ($targets === []) {
            $this->info('Nothing to generate — every supported language already has a directory. Use --force or --lang=... to override.');
            return self::SUCCESS;
        }

        $force = (bool) $this->option('force');
        $referenceFiles = glob($referenceDir . DIRECTORY_SEPARATOR . '*.php') ?: [];
        sort($referenceFiles);

        $this->info(sprintf(
            'Generating %d locale(s) from %d reference file(s): %s',
            count($targets),
            count($referenceFiles),
            implode(', ', $targets)
        ));

        $globalStats = [];

        foreach ($targets as $lang) {
            $langDir = base_path('resources/lang/' . $lang);

            if (is_dir($langDir) && !$force) {
                $this->warn("Skipping $lang — directory already exists. Use --force to overwrite.");
                continue;
            }

            if (!is_dir($langDir) && !mkdir($langDir, 0775, true) && !is_dir($langDir)) {
                throw new RuntimeException("Unable to create $langDir");
            }

            $langStats = ['files' => 0, 'leaves' => 0, 'translated' => 0, 'fallback' => 0];
            $unmappedReport = [];

            foreach ($referenceFiles as $refFile) {
                $basename = basename($refFile);
                $tree = require $refFile;
                if (!is_array($tree)) {
                    continue;
                }

                $fileLeaves = ['translated' => 0, 'fallback' => 0, 'unmapped' => []];
                $translatedTree = $this->translateTree($tree, $lang, '', $fileLeaves);

                file_put_contents(
                    $langDir . DIRECTORY_SEPARATOR . $basename,
                    $this->renderPhpFile($translatedTree, $basename, $lang, $reference)
                );

                $langStats['files']++;
                $langStats['leaves'] += $fileLeaves['translated'] + $fileLeaves['fallback'];
                $langStats['translated'] += $fileLeaves['translated'];
                $langStats['fallback'] += $fileLeaves['fallback'];

                if ($fileLeaves['unmapped'] !== []) {
                    $unmappedReport[$basename] = $fileLeaves['unmapped'];
                }
            }

            file_put_contents(
                $langDir . DIRECTORY_SEPARATOR . self::STATUS_FILE,
                $this->renderStatusReport($lang, $reference, $langStats, $unmappedReport)
            );

            $globalStats[$lang] = $langStats;

            $this->info(sprintf(
                '  %s: %d files, %d leaves (%d translated, %d english fallback)',
                $lang,
                $langStats['files'],
                $langStats['leaves'],
                $langStats['translated'],
                $langStats['fallback']
            ));
        }

        $this->info('Done.');
        $this->table(
            ['lang', 'files', 'leaves', 'translated', 'fallback', 'rate'],
            array_map(
                static fn (string $lang, array $s): array => [
                    $lang,
                    $s['files'],
                    $s['leaves'],
                    $s['translated'],
                    $s['fallback'],
                    $s['leaves'] > 0 ? round(100 * $s['translated'] / $s['leaves'], 1) . '%' : 'n/a',
                ],
                array_keys($globalStats),
                array_values($globalStats)
            )
        );

        return self::SUCCESS;
    }

    /**
     * Sentinel filename emitted in every auto-generated locale directory.
     * Its presence marks a directory as safe to overwrite on --force.
     */
    private const STATUS_FILE = '_TRANSLATION_STATUS.md';

    /**
     * @return array<int, string>
     */
    private function resolveTargets(string $reference): array
    {
        $explicit = (string) ($this->option('lang') ?? '');
        if ($explicit !== '') {
            $list = array_filter(array_map('trim', explode(',', $explicit)));
            return array_values(array_unique($list));
        }

        $force = (bool) $this->option('force');

        $targets = [];
        foreach (OGameLocale::SUPPORTED_LANGUAGES as $code) {
            if ($code === $reference) {
                continue;
            }

            $dir = base_path('resources/lang/' . $code);
            if (!is_dir($dir)) {
                $targets[] = $code;
                continue;
            }

            // Directory exists. Only regenerate if --force AND it carries our
            // sentinel file (i.e. it was previously emitted by this command).
            // Hand-maintained directories like en/, de/, it/, nl/ are skipped.
            if ($force && is_file($dir . DIRECTORY_SEPARATOR . self::STATUS_FILE)) {
                $targets[] = $code;
            }
        }

        return $targets;
    }

    /**
     * @param  array<int|string, mixed>  $tree
     * @param  array{translated:int,fallback:int,unmapped:array<int, array{key:string,en:string}>}  $stats
     * @return array<int|string, mixed>
     */
    private function translateTree(array $tree, string $lang, string $prefix, array &$stats): array
    {
        $out = [];
        foreach ($tree as $key => $value) {
            $compoundKey = $prefix === '' ? (string) $key : $prefix . '.' . $key;
            if (is_array($value)) {
                $out[$key] = $this->translateTree($value, $lang, $compoundKey, $stats);
                continue;
            }
            if (!is_string($value)) {
                $out[$key] = $value;
                continue;
            }

            $translated = OGameLocale::lookup($value, $lang);
            if ($translated !== null) {
                $out[$key] = $translated;
                $stats['translated']++;
            } else {
                $out[$key] = $value;
                $stats['fallback']++;
                $stats['unmapped'][] = ['key' => $compoundKey, 'en' => $value];
            }
        }
        return $out;
    }

    /**
     * @param  array<int|string, mixed>  $tree
     */
    private function renderPhpFile(array $tree, string $basename, string $lang, string $reference): string
    {
        return "<?php\n\n"
            . "/**\n"
            . " * AUTO-GENERATED by `php artisan i18n:generate-locales`. DO NOT EDIT BY HAND.\n"
            . " *\n"
            . " * Language : $lang\n"
            . " * Source   : resources/lang/$reference/$basename\n"
            . " * Built    : " . date('c') . "\n"
            . " *\n"
            . " * Untranslated leaves fall back to the english source string.\n"
            . " * See _TRANSLATION_STATUS.md in this directory for the list of\n"
            . " * keys still needing manual translation.\n"
            . " */\n\n"
            . "return " . $this->varExport($tree, 0) . ";\n";
    }

    /**
     * @param  mixed  $value
     */
    private function varExport($value, int $depth): string
    {
        $indent = str_repeat('    ', $depth);
        $childIndent = str_repeat('    ', $depth + 1);

        if (is_array($value)) {
            if ($value === []) {
                return '[]';
            }

            $isList = array_keys($value) === range(0, count($value) - 1);
            $lines = [];
            foreach ($value as $k => $v) {
                $rendered = $this->varExport($v, $depth + 1);
                if ($isList) {
                    $lines[] = $childIndent . $rendered . ',';
                } else {
                    $lines[] = $childIndent . $this->exportScalar((string) $k) . ' => ' . $rendered . ',';
                }
            }
            return "[\n" . implode("\n", $lines) . "\n" . $indent . ']';
        }

        return $this->exportScalar($value);
    }

    /**
     * @param  mixed  $value
     */
    private function exportScalar($value): string
    {
        if (is_string($value)) {
            return "'" . str_replace(['\\', "'"], ['\\\\', "\\'"], $value) . "'";
        }
        if ($value === null) {
            return 'null';
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        return var_export($value, true);
    }

    /**
     * @param  array{files:int,leaves:int,translated:int,fallback:int}  $stats
     * @param  array<string, array<int, array{key:string,en:string}>>  $unmapped
     */
    private function renderStatusReport(string $lang, string $reference, array $stats, array $unmapped): string
    {
        $rate = $stats['leaves'] > 0
            ? round(100 * $stats['translated'] / $stats['leaves'], 1) . '%'
            : 'n/a';

        $lines = [];
        $lines[] = "# Translation status — `$lang`";
        $lines[] = '';
        $lines[] = "Auto-generated by `php artisan i18n:generate-locales` from `resources/lang/$reference/`.";
        $lines[] = '';
        $lines[] = '## Summary';
        $lines[] = '';
        $lines[] = '| metric | value |';
        $lines[] = '|---|---|';
        $lines[] = '| files | ' . $stats['files'] . ' |';
        $lines[] = '| total leaves | ' . $stats['leaves'] . ' |';
        $lines[] = '| translated | ' . $stats['translated'] . ' |';
        $lines[] = '| english fallback | ' . $stats['fallback'] . ' |';
        $lines[] = '| translation rate | ' . $rate . ' |';
        $lines[] = '';

        if ($unmapped === []) {
            $lines[] = 'All leaves translated.';
            return implode("\n", $lines) . "\n";
        }

        $lines[] = '## Keys needing manual translation';
        $lines[] = '';
        $lines[] = 'These keys could not be resolved through the OGame master dictionary and were emitted with the english source string as fallback.';
        $lines[] = '';

        ksort($unmapped);
        foreach ($unmapped as $file => $rows) {
            $lines[] = '### ' . $file . ' (' . count($rows) . ')';
            $lines[] = '';
            $lines[] = '| key | english fallback |';
            $lines[] = '|---|---|';
            foreach ($rows as $row) {
                $lines[] = '| `' . $row['key'] . '` | ' . $this->mdEscape($row['en']) . ' |';
            }
            $lines[] = '';
        }

        return implode("\n", $lines);
    }

    private function mdEscape(string $value): string
    {
        $value = str_replace(["\r\n", "\r", "\n"], ' ', $value);
        return str_replace('|', '\\|', $value);
    }
}
