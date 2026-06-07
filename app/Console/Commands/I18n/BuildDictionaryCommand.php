<?php

namespace OGame\Console\Commands\I18n;

use Illuminate\Console\Command;
use JsonException;
use RuntimeException;

class BuildDictionaryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'i18n:build-dictionary
                            {--source= : Path to ogame_CANONICAL_en_*.json. Defaults to the most recent file in resources/i18n/source/ or storage/i18n/source/}
                            {--out= : Output PHP file. Defaults to storage/i18n/master_dictionary.php}
                            {--overrides= : Path to a PHP file returning manual overrides. Defaults to resources/i18n/overrides.php}
                            {--no-overrides : Skip the overrides file even if it exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build the master OGame translation dictionary (storage/i18n/master_dictionary.php) from the official CANONICAL JSON scrape.';

    /**
     * Languages we expect every CANONICAL entry to expose.
     *
     * @var array<int, string>
     */
    private const EXPECTED_LANGUAGES = [
        'en', 'ar', 'br', 'cz', 'de', 'dk', 'es', 'fi', 'fr', 'gr',
        'hr', 'hu', 'it', 'jp', 'mx', 'nl', 'pl', 'pt', 'ro', 'ru',
        'se', 'si', 'sk', 'tr', 'tw', 'us', 'yu',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $sourcePath = $this->resolveSourcePath();
        if ($sourcePath === null) {
            $this->error('No CANONICAL source file found. Provide one with --source=PATH.');
            return self::FAILURE;
        }

        $this->info('Reading canonical source: ' . $sourcePath);

        $raw = @file_get_contents($sourcePath);
        if ($raw === false) {
            $this->error('Unable to read source file.');
            return self::FAILURE;
        }

        try {
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->error('Invalid JSON: ' . $e->getMessage());
            return self::FAILURE;
        }

        if (!isset($data['entries']) || !is_array($data['entries'])) {
            $this->error('Source JSON does not contain an "entries" object.');
            return self::FAILURE;
        }

        $declaredLangs = is_array($data['languages'] ?? null) ? $data['languages'] : self::EXPECTED_LANGUAGES;
        $missing = array_diff(self::EXPECTED_LANGUAGES, $declaredLangs);
        if ($missing !== []) {
            $this->warn('Source JSON is missing expected languages: ' . implode(', ', $missing));
        }

        [$dictionary, $stats] = $this->buildDictionary($data['entries'], $declaredLangs);

        $overridesPath = $this->resolveOverridesPath();
        $overrideStats = ['files' => 0, 'applied' => 0, 'skipped' => 0, 'added' => 0];
        if ($overridesPath !== null) {
            $overrideStats = $this->applyOverrides($dictionary, $overridesPath);
        }

        $outPath = $this->resolveOutputPath();
        $this->ensureDirectory(dirname($outPath));

        file_put_contents(
            $outPath,
            $this->renderPhp($dictionary, $sourcePath, $declaredLangs, $stats, $overridesPath, $overrideStats)
        );

        $this->info('Wrote dictionary: ' . $outPath);
        $this->table(
            ['metric', 'value'],
            [
                ['total entries', $stats['total']],
                ['variant entries', $stats['variant']],
                ['invariant entries', $stats['invariant']],
                ['languages', count($declaredLangs)],
                ['overrides applied', $overrideStats['applied']],
                ['overrides skipped', $overrideStats['skipped']],
                ['file size (bytes)', filesize($outPath) ?: 0],
            ]
        );

        return self::SUCCESS;
    }

    private function resolveOverridesPath(): string|null
    {
        if ($this->option('no-overrides')) {
            return null;
        }

        $explicit = (string) ($this->option('overrides') ?? '');
        if ($explicit !== '') {
            return is_file($explicit) ? $explicit : null;
        }

        $default = base_path('resources/i18n/overrides.php');
        return is_file($default) ? $default : null;
    }

    /**
     * @param  array<string, array<string, string>>  $dictionary
     * @return array{files:int, applied:int, skipped:int, added:int}
     */
    private function applyOverrides(array &$dictionary, string $path): array
    {
        $stats = ['files' => 1, 'applied' => 0, 'skipped' => 0, 'added' => 0];

        /** @var mixed $loaded */
        $loaded = require $path;
        if (!is_array($loaded)) {
            $this->warn("Overrides file did not return an array: $path");
            return $stats;
        }

        foreach ($loaded as $englishText => $langMap) {
            if (!is_string($englishText) || !is_array($langMap)) {
                continue;
            }
            if (!isset($dictionary[$englishText])) {
                // New entry — seed with the English source string itself.
                $dictionary[$englishText] = ['en' => $this->normalizeText($englishText)];
                $stats['added']++;
            }
            foreach ($langMap as $lang => $value) {
                if (!is_string($lang) || !is_string($value)) {
                    $stats['skipped']++;
                    continue;
                }
                $dictionary[$englishText][$lang] = $this->normalizeText($value);
                $stats['applied']++;
            }
        }

        ksort($dictionary, SORT_NATURAL | SORT_FLAG_CASE);

        $this->info(sprintf(
            'Applied %d override(s) from %s (%d new entries added)',
            $stats['applied'],
            basename($path),
            $stats['added']
        ));

        return $stats;
    }

    /**
     * @param  array<string, mixed>  $entries
     * @param  array<int, string>    $languages
     * @return array{0: array<string, array<string, string>>, 1: array{total:int,variant:int,invariant:int}}
     */
    private function buildDictionary(array $entries, array $languages): array
    {
        $dictionary = [];
        $stats = ['total' => 0, 'variant' => 0, 'invariant' => 0];

        foreach ($entries as $englishText => $entry) {
            if (!is_array($entry)) {
                continue;
            }

            $row = [];
            foreach ($languages as $lang) {
                if (isset($entry[$lang]) && is_string($entry[$lang])) {
                    $row[$lang] = $this->normalizeText($entry[$lang]);
                }
            }

            // Always guarantee an 'en' entry — fall back to the key itself.
            if (!isset($row['en'])) {
                $row['en'] = $this->normalizeText((string) $englishText);
            }

            $dictionary[(string) $englishText] = $row;

            $stats['total']++;
            if (!empty($entry['_invariant'])) {
                $stats['invariant']++;
            } else {
                $stats['variant']++;
            }
        }

        ksort($dictionary, SORT_NATURAL | SORT_FLAG_CASE);

        return [$dictionary, $stats];
    }

    /**
     * Normalize text scraped from the OGame UI before storing it in the dictionary.
     *
     * The upstream scrape consistently captures typographic apostrophes as
     * literal backticks (U+0060) — French alone has 50+ occurrences. Every
     * inspected case is unambiguously meant to be an apostrophe (l`unica,
     * d`ensemble, video`s, απ` ό,τι), so we normalize them globally.
     */
    private function normalizeText(string $value): string
    {
        return str_replace("\u{0060}", "'", $value);
    }

    private function resolveSourcePath(): string|null
    {
        $explicit = (string) ($this->option('source') ?? '');
        if ($explicit !== '') {
            return is_file($explicit) ? $explicit : null;
        }

        /** @var array<int, string> $candidates */
        $candidates = [];
        foreach ([base_path('resources/i18n/source'), storage_path('i18n/source')] as $dir) {
            if (!is_dir($dir)) {
                continue;
            }
            foreach ((array) glob($dir . DIRECTORY_SEPARATOR . 'ogame_CANONICAL_en_*.json') as $file) {
                if (is_string($file)) {
                    $candidates[] = $file;
                }
            }
        }

        if ($candidates === []) {
            return null;
        }

        usort($candidates, static fn (string $a, string $b): int => (int) filemtime($b) <=> (int) filemtime($a));

        return $candidates[0];
    }

    private function resolveOutputPath(): string
    {
        $explicit = (string) ($this->option('out') ?? '');
        if ($explicit !== '') {
            return $explicit;
        }

        return storage_path('i18n/master_dictionary.php');
    }

    private function ensureDirectory(string $dir): void
    {
        if (is_dir($dir)) {
            return;
        }
        if (!mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException(sprintf('Unable to create directory "%s"', $dir));
        }
    }

    /**
     * @param  array<string, array<string, string>>  $dictionary
     * @param  array<int, string>                    $languages
     * @param  array{total:int,variant:int,invariant:int} $stats
     * @param  array{files:int,applied:int,skipped:int,added?:int} $overrideStats
     */
    private function renderPhp(
        array $dictionary,
        string $sourcePath,
        array $languages,
        array $stats,
        string|null $overridesPath,
        array $overrideStats
    ): string {
        $overrideLine = $overridesPath !== null
            ? basename($overridesPath) . ' (' . $overrideStats['applied'] . ' applied)'
            : 'none';

        $header = "<?php\n\n"
            . "/**\n"
            . " * OGame master translation dictionary.\n"
            . " *\n"
            . " * AUTO-GENERATED by `php artisan i18n:build-dictionary`. DO NOT EDIT BY HAND.\n"
            . " *\n"
            . " * Source    : " . basename($sourcePath) . "\n"
            . " * Built     : " . date('c') . "\n"
            . " * Total     : " . $stats['total'] . " entries (" . $stats['variant'] . " variant, " . $stats['invariant'] . " invariant)\n"
            . " * Langs     : " . implode(', ', $languages) . "\n"
            . " * Overrides : " . $overrideLine . "\n"
            . " *\n"
            . " * Structure: array<englishText, array<langCode, translation>>\n"
            . " */\n\n"
            . "return " . $this->varExportShort($dictionary, 0) . ";\n";

        return $header;
    }

    /**
     * Compact var_export — short array syntax, indented two spaces, single-line leaves.
     *
     * @param  mixed  $value
     */
    private function varExportShort($value, int $depth): string
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
                $rendered = $this->varExportShort($v, $depth + 1);
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
}
