# JS Chunking Guide

## What was done

The file `resources/js/ingame/e7c74974620fa35b197315ebdbb8c2.js` (1.4 MB, ~46K lines) was split into ~510 smaller files under `resources/js/ingame/chunks/`. The original is pre-ES6 OGame JavaScript: `var`, `prototype`-based OOP, jQuery plugins, no modules.

### Pipeline

**1. Beautify** (`prettier`)
```
npx prettier --write input.js
```
Consistent formatting is required for line-based extraction. Prettier handled the 1.4 MB file in ~1.3 seconds.

**2. Parse AST** (`acorn`)
```
node scripts/analyze-structure.js
```
Walks the AST and emits `storage/app/ingame-inventory.json` listing every class, function, global variable, jQuery plugin, and IIFE with line ranges.

**3. Extract chunks** (`scripts/chunk-ingame.js`)
```
node scripts/chunk-ingame.js
```
Classifies each top-level statement into one of these categories:

| Category | Detection pattern | Example filenames |
|---|---|---|
| `class` | `var X = function` + `X.prototype.Y = function` | `fleet/fleetdispatcher.js` |
| `class` | `function X()` + `X.prototype.Y = function` | `missile/missile.js` |
| `jquery-plugin` | `$.fn.X = function` | `charts/ogamelinechart.js` |
| `iife` | `(function(){})()` or `!function(){}()` | `iife/iife-84.js` |
| `function` | Named `function foo()` declarations | `functions/reloadresources.js` |
| `globals` | Top-level `var X = ...` | `globals/globals-7310.js` |
| `misc` | Everything else (event bindings, `class` syntax, object literals) | `misc/stmt-17385.js` |

Lines between chunks (comments, whitespace) are preserved in a cursor-based assembly pass that writes `_reassembled.js` for validation.

**4. Validate** (`scripts/validate-chunks.js`)
```
node scripts/validate-chunks.js
```
Reassembles all chunks, runs `node --check`, diffs against original, re-minifies both with `terser` and compares. A match confirms the extraction is semantically lossless.

**5. Wire up Vite** (`vite.config.js`)
The `getChunkPaths()` function reads the chunk manifest and passes all file paths to the existing `concatLegacyBundles` plugin. The chunks replace the single `e7c7497...js` entry in the concatenation array. Vite serves them concatenated in order in both dev and build modes.

### Scripts

| Script | Purpose |
|---|---|
| `scripts/analyze-structure.js` | Parse AST, dump inventory JSON |
| `scripts/chunk-ingame.js` | Classify and extract chunks |
| `scripts/validate-chunks.js` | Reassemble, syntax check, diff, minify-compare |

---

## Reusing for other large JS files

### Step 1: Beautify

```bash
npx prettier --print-width 120 --write path/to/large-file.js
```

If Prettier fails on very old JS syntax, try `js-beautify` or `uglify-js -b`.

### Step 2: Update the scripts

Edit the `INPUT` constant in `scripts/analyze-structure.js` and `scripts/chunk-ingame.js` to point to the new file. Update `OUTPUT_DIR` in `scripts/chunk-ingame.js` to a new chunks directory.

### Step 3: Run the pipeline

```bash
node scripts/analyze-structure.js
node scripts/chunk-ingame.js
node scripts/validate-chunks.js
```

### Step 4: Add classification rules for new patterns

The `categorize()` function in `scripts/chunk-ingame.js` maps class names to directories. Add entries for classes found in the new file. If the file uses patterns not covered (e.g. ES6 `class` syntax, ES modules), add detection in the main classification loop.

### Step 5: Wire up your bundler

Update `vite.config.js` (or webpack config) to include the chunk files in the same order as the manifest. The `getChunkPaths()` function shows the pattern for reading the manifest.

---

## Testing and verification

### Static analysis (no browser needed)

**After chunking, before bundling:**

```bash
# Syntax check every chunk
for f in resources/js/ingame/chunks/**/*.js; do
  node --check "$f" || echo "BROKEN: $f"
done

# Reassembly diff
node scripts/validate-chunks.js

# Minified comparison (confirms semantic equivalence)
npx terser original.js -c -m -o /tmp/orig.min.js
npx terser chunks/_reassembled.js -c -m -o /tmp/reass.min.js
diff /tmp/orig.min.js /tmp/reass.min.js
```

If the minified diff is empty, the extraction lost no code.

**After bundling:**

```bash
npm run build

# Verify key symbols exist in the bundle
BUNDLE=$(node -e "const m=require('./public/build/manifest.json');
  console.log('public/build/'+m['resources/js/ingame.js'].file)")
node -e "
  const b=require('fs').readFileSync('$BUNDLE','utf8');
  ['FleetDispatcher','Marketplace','Alliance','Missile'].forEach(s =>
    console.log(s, b.includes('function '+s) || b.includes(s+'=function'))
  );
"
```

### Manual browser testing

1. Start the dev server (`npm run dev` or `docker compose up`).
2. Open the browser DevTools Console.
3. Navigate through each game page (Overview, Fleet, Alliance, Marketplace, Technology, Galaxy, Messages, etc.).
4. Watch for `ReferenceError` (missing definitions) and `TypeError` (calling methods on undefined).
5. For each error, find the referenced symbol in the source:
   ```bash
   grep -n 'SymbolName' resources/js/ingame/e7c74974620fa35b197315ebdbb8c2.js
   ```
6. Check if the definition and the usage are in the correct order in the manifest:
   ```bash
   node -e "const m=require('./resources/js/ingame/chunks/manifest.json');
     m.chunks.forEach((c,i) => { if (c.name.includes('SymbolName')) console.log(i, c.path); })"
   ```

### Incremental approach

When working with a new large file:

1. Extract only 10-20 chunks first. Run the reassembly validation.
2. Add them to the bundler. Build. Check the console for errors.
3. Add the next batch. Repeat.
4. This catches ordering issues early, before you have 500 chunks to debug.

### Common issues

- **Duplicate filenames overwriting content**: When a class splits across multiple AST nodes (constructor + prototypes), the chunking script assigns the same filename to both. The script handles this with `appendFileSync`. Verify with `grep -c 'function ClassName' chunks/category/classname.js`.
- **IIFE boundaries**: `acorn` includes trailing JSDoc comments in node ranges, making IIFE chunks too long. The script trims trailing `;(function` and `/**` lines.
- **Startup order**: The manifest preserves the original file order. If a class is used before it is defined in the original file, the original was already broken. The chunked version will have the same issue.

---

## Other chunkable files in the project

A scan of all JS files under `resources/js/` found these files worth chunking:

| File | Size | Status |
|---|---|---|
| `ingame/e7c74974620fa35b197315ebdbb8c2.js` | 1.4 MB | Chunked (510 files) |
| `ingame/jquery.js` | 625 KB, 15K lines | **Candidate** |
| `outgame/22ef0d59ed3309209b51ac1d7d8674.js` | 255 KB | Vendor (jQuery UI), skip |
| `outgame/22838c9f0f7e8e3535367164b832ce.js` | 88 KB | Vendor (jQuery 3.2.1), skip |
| `outgame/0b5c68ed173515e7cb0965c287aa0c.js` | 45 KB | Vendor (Fancybox), skip |
| `outgame/4c590fd581de4bc24b47347d879e94.js` | 30 KB | Vendor (jQuery Validation Engine), skip |

Vendor libraries are left as-is since they are already separate files and not OGame-specific code.

### `resources/js/ingame/jquery.js` (625 KB)

This is OGame's custom jQuery extension layer (field selection helpers, form utilities, DOM wrappers). It is already partially beautified at 15K lines but is a single monolithic file.

To chunk it, follow the same steps as the main ingame file. Update the `INPUT` and `OUTPUT_DIR` constants in the scripts, run the pipeline, then update `vite.config.js` to spread the chunk paths in place of the single `jquery.js` entry.

This file will produce fewer chunks than the main ingame file (estimated 50-150 vs 510) since it contains utility functions rather than full page-level classes.

---

## Outgame scripts detail

The outgame scripts (`resources/js/outgame/`) are 12 files totaling 460 KB. After inspection, 4 of the 5 largest files are vendor libraries, not OGame-specific code:

| File | Size | Content |
|---|---|---|
| `22ef0d59...js` | 255 KB | jQuery UI 1.12.1 |
| `22838c9f...js` | 88 KB | jQuery 3.2.1 |
| `0b5c68ed...js` | 45 KB | Fancybox |
| `4c590fd5...js` | 30 KB | jQuery Validation Engine |
| Remaining 8 files | < 6 KB each | OGame-specific (already small) |

Since the outgame files are either vendor libraries or already under 6 KB, there is nothing to chunk. The outgame `concatLegacyBundles` setup in `vite.config.js` works well as-is.

If new large outgame files are added later, the same pipeline applies: beautify, parse, extract, validate, wire up.
