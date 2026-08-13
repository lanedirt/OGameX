# De-minification & Chunking Plan: `e7c74974620fa35b197315ebdbb8c2.js`

## File Profile

| Property | Value |
|---|---|
| **Path** | `resources/js/ingame/e7c74974620fa35b197315ebdbb8c2.js` |
| **Size** | ~1.4 MB (1,420,387 bytes) |
| **Lines** | ~41,463 |
| **Status** | Partially beautified (has JSDoc comments, some indentation), but still largely a single concatenated file |
| **Paradigm** | Pre-ES6: `var`, `prototype`-based OOP, jQuery plugins, no modules |
| **Classes found** | 54+ (via `ClassName.prototype.method = function`) |
| **Top classes by method count** | FleetDispatcher (130), Marketplace (129), Alliance (50), HappyEdit (39), OGameLineChart (38) |
| **Vendor libs** | Select2, jQuery UI components, validation engine, markItUp, colorpicker, AnythingSlider, BBQ (all in `vendor/` dir) |

---

## Phase 0 — Prerequisite: Tool Installation

Install all needed tools **once** before any work begins:

```bash
# Core beautification & AST tools
npm install --save-dev prettier js-beautify

# AST parsing & code generation
npm install --save-dev @babel/parser @babel/traverse @babel/generator @babel/types

# Deobfuscation (try all three; each handles different obfuscation patterns)
npm install -g js-deobfuscator          # ben-sb/javascript-deobfuscator
npm install -g deobfuscator             # relative/synchrony
npm install -g webcrack                 # webcrack (modern, handles webpack bundles too)

# Structural analysis & dependency graphing
npm install --save-dev madge dependency-cruiser

# Codemod / refactoring engines
npm install --save-dev jscodeshift putout

# AST structural search (Rust-based, very fast)
npm install -g @ast-grep/cli

# Minifier (for validation: re-minify extracted chunks and compare behavior)
npm install --save-dev terser

# Diffing / comparison
# (use system `diff`, `git diff --no-index`, or `delta` for side-by-side)
```

---

## Phase 1 — Full Beautification (Uniform Formatting)

### Goal
Produce a single, consistently formatted file where every statement, block, and expression has predictable whitespace. This makes AST parsing reliable and regex-based chunking possible.

### Tools
- **Prettier** — industry standard, handles edge cases well on huge files
- **js-beautify** — fallback if Prettier chokes on 1.4 MB; better at preserving some original formatting
- **uglify-js** (beautify mode) — third fallback; `uglifyjs input.js -b -o output.js`

### Steps
1. Run Prettier with conservative options:
   ```
   npx prettier --print-width 120 --tab-width 2 --no-semi false --single-quote false \
     --write e7c74974620fa35b197315ebdbb8c2.js
   ```
2. If Prettier fails (OOM / parse errors on legacy JS), fall back to js-beautify:
   ```
   npx js-beautify -s 2 -w 120 --brace-style collapse -r e7c74974620fa35b197315ebdbb8c2.js
   ```
3. If both fail, use uglify-js beautify mode:
   ```
   npx uglify-js e7c74974620fa35b197315ebdbb8c2.js -b beautify=true,indent_level=2 -o e7c74974620fa35b197315ebdbb8c2.beautified.js
   ```
4. Save the result as `ingame.beautified.js` (keep original as backup).
5. **Validate**: Run `node --check ingame.beautified.js` to confirm syntax is valid.
6. **Git commit** the beautified version as a baseline.

### Deliverable
- `resources/js/ingame/ingame.beautified.js` — consistently formatted, syntax-valid file

---

## Phase 2 — Deobfuscation (Name & String Recovery)

### Goal
Recover meaningful identifiers and strings that were mangled/minified. This is OPTIONAL — only run if the file contains obfuscated sections (hex identifiers like `_0x...`, encoded strings, proxy function chains).

### Assessment
First check if deobfuscation is needed:
```bash
# Check for hex identifiers
grep -c '_0x[0-9a-f]' ingame.beautified.js
# Check for encoded strings
grep -c '\\x[0-9a-f][0-9a-f]' ingame.beautified.js
```

If counts are low (< 50), skip this phase — the file is mostly minified, not obfuscated.

### Tools (try in order)
1. **webcrack** — best for modern webpack/bundler output; handles string arrays, mangle deobfuscation, control flow flattening
   ```
   npx webcrack ingame.beautified.js -o ingame.deobfuscated.js
   ```
2. **js-deobfuscator** — good for general-purpose deobfuscation (array unpacking, proxy functions, hex renaming)
   ```
   js-deobfuscator -i ingame.beautified.js -o ingame.deobfuscated.js
   ```
3. **deobfuscator (synchrony)** — specifically strong against javascript-obfuscator output
   ```
   synchrony deobfuscate ingame.beautified.js
   ```

### Validation
- Each tool's output must pass `node --check`
- Diff against beautified version to understand what changed
- If a tool breaks syntax, discard its output and try the next one

### Deliverable
- `ingame.deobfuscated.js` (only if deobfuscation was needed and successful)

---

## Phase 3 — AST-Based Structural Analysis

### Goal
Build a complete map of the code: every function, class, global variable, and their dependencies. This map drives the chunking decisions.

### Tools & Techniques

#### 3a. Extract AST with Babel
```bash
# Dump the full AST as JSON for programmatic analysis
node -e "
const parser = require('@babel/parser');
const fs = require('fs');
const code = fs.readFileSync('ingame.beautified.js', 'utf8');
const ast = parser.parse(code, {
  sourceType: 'script',
  plugins: ['jsx', 'flow'],
  errorRecovery: true,
  allowImportExportEverywhere: false,
  allowReturnOutsideFunction: true,
  allowSuperOutsideMethod: true,
  allowUndeclaredExports: true,
});
fs.writeFileSync('ast-dump.json', JSON.stringify(ast, null, 2));
"
```
- Babel 8 may fail on legacy syntax. If so, fall back to **acorn** (more permissive):
  ```
  npx acorn --ecma2020 --allow-hash-bang ingame.beautified.js > ast-dump.json
  ```
- Or use **espree** (ESLint's parser, very tolerant).

#### 3b. Extract Structural Inventory with ast-grep
`ast-grep` is ideal for finding patterns without writing parsing code:
```yaml
# Find all prototype method assignments: X.prototype.Y = function(...) {...}
rule:
  pattern: $CLASS.prototype.$METHOD = $FUNC
# Find all jQuery plugins: $.fn.$NAME = ...
rule:
  pattern: $.fn.$NAME = $VALUE
# Find all window/global assignments
rule:
  pattern: window.$NAME = $VALUE
```

#### 3c. Build a Custom Analysis Script (Node.js + Babel traverse)
Write a one-off script that:
1. Parses the AST
2. Walks all top-level statements
3. Groups them by "logical unit":
   - **Class**: All `ClassName.prototype.method = function` blocks for the same `ClassName`
   - **jQuery plugin**: Everything attached to a single `$.fn.X`
   - **Standalone function**: Named `function` declarations
   - **Namespace object**: `var X = { ... }` with methods
   - **IIFE block**: `(function() { ... })()` blocks
4. Emits a JSON inventory:
   ```json
   {
     "classes": {
       "FleetDispatcher": { "methods": 130, "startLine": 15000, "endLine": 22000 },
       "Marketplace": { "methods": 129, "startLine": 22001, "endLine": 29000 }
     },
     "jqueryPlugins": {
       "ogameLineChart": { "startLine": ... }
     },
     "standaloneFunctions": { ... },
     "globalVariables": { ... }
   }
   ```

#### 3d. Dependency Graph with madge
```
npx madge --image graph.svg ingame.beautified.js
```
- Visualizes which functions call which
- Helps decide chunking order (extract leaf nodes first)
- But: madge may struggle with prototype-based OOP. Use `dependency-cruiser` as fallback:
  ```
  npx depcruise --output-type dot ingame.beautified.js | dot -T svg > deps.svg
  ```

### Deliverables
- `ast-dump.json` — full AST
- `inventory.json` — structural inventory of all logical units
- `deps.svg` — dependency graph visualization

---

## Phase 4 — Chunking Strategy

### Principles
1. **Extract leaf dependencies first** — standalone utility functions with no internal deps
2. **One class per file** — each `ClassName.prototype` cluster becomes its own file
3. **Preserve order** — chunks must concatenate back in original order (or use a loader)
4. **Keep vendor libraries separate** — Select2, jQuery plugins already in `vendor/`
5. **Extract shared utilities** — helper functions used by multiple classes become a `shared.js`
6. **Don't break IIFEs** — keep self-executing blocks intact
7. **Validate at every step** — after each extraction, concatenate all chunks + original vendor files and diff against original behavior

### Chunk Categories (discovered so far)

| Category | Classes/Functions | Est. Size |
|---|---|---|
| **Core/Timer** | TimerHandler | Small |
| **Fleet** | FleetDispatcher (130 methods), FleetHelper (12 methods) | Very Large |
| **Marketplace** | Marketplace (129 methods) | Very Large |
| **Alliance** | Alliance (50 methods), AllianceClassBoxes (12 methods) | Large |
| **Chat/Editor** | HappyEdit (39 methods) | Medium |
| **Charts** | OGameLineChart (38 methods) | Medium |
| **Technology** | TechnologyDetails (22 methods) | Medium |
| **Graveyard** | Graveyard (21 methods) | Medium |
| **Exodus** | Exodus (21 methods) | Medium |
| **Rewarding** | Rewarding (19 methods) | Medium |
| **PercentageBar** | PercentageBar (18 methods) | Medium |
| **Sorting/Pagination** | OGameSortable (13), OGamePaginatable (7) | Small |
| **Select2 (embedded)** | Select2 (21), SelectAdapter (11), BaseSelection (7), etc. | Medium |
| **Lifeform** | LifeformResearch (10), LifeformSettings (8) | Small |
| **Missile** | Missile (9 methods) | Small |
| **Resources** | ResourceTicker (8 methods) | Small |
| **Characters** | CharacterClassBoxes (9 methods) | Small |
| **Search** | Search (11 methods) | Small |
| **Notify/Translation** | Notify (3), Translation (3) | Small |
| **Select2 internals** | Dropdown (3), Results (15), Tags (4), etc. | Medium |
| **jQuery Plugins** | $.fn.ogameLineChart, $.fn.ogameLoadingIndicator, $.fn.ogamePaginatable, $.fn.ogameSortable | Mixed |

### Chunking Order (by dependency depth)
1. **Utilities/Shared**: TimerHandler, AjaxAdapter, Observable, Translation, Notify
2. **jQuery Plugins**: ogameLineChart, ogameLoadingIndicator, ogameSortable, ogamePaginatable
3. **Select2 (embedded)**: All Select2-related classes
4. **Feature Pages**: FleetDispatcher, Marketplace, Alliance, TechnologyDetails, Graveyard, Exodus, Rewarding, Missile, Lifeform, CharacterClassBoxes
5. **Entry Point**: Top-level init/bootstrap code

---

## Phase 5 — Automated Extraction (Script-Based Chunking)

### Tools
- **jscodeshift** — ideal for writing codemods that extract AST nodes
- **putout** — plugin-based, good for repetitive transformations
- **Custom Node.js script** using `@babel/parser` + `@babel/generator` — most flexible

### Approach
Write a Node.js script (`scripts/chunk-ingame.js`) that:

1. **Parses** the beautified file with Babel into an AST
2. **Walks** the top-level program body
3. **Identifies logical boundaries**:
   - A `var ClassName = function(...) { ... };` followed by `ClassName.prototype.method = ...` blocks → extract as one chunk
   - A `$.fn.pluginName = ...` block → extract as one chunk
   - A standalone `function name(...) { ... }` → extract if not part of a class
   - An IIFE `(function() { ... })()` → extract whole
   - Global `var X = ...` assignments → extract if referenced by multiple chunks → goes to shared
4. **Writes** each chunk to `resources/js/ingame/chunks/{category}/{name}.js`
5. **Generates** a `chunks-manifest.json` listing all chunks in dependency order
6. **Generates** a concatenation script that reassembles chunks in correct order

### Output Structure
```
resources/js/ingame/chunks/
├── manifest.json              # { "order": ["shared/timer.js", "shared/ajax.js", ...], "chunks": {...} }
├── shared/
│   ├── timer-handler.js
│   ├── ajax-adapter.js
│   ├── observable.js
│   ├── translation.js
│   └── notify.js
├── jquery-plugins/
│   ├── ogame-line-chart.js
│   ├── ogame-loading-indicator.js
│   ├── ogame-sortable.js
│   └── ogame-paginatable.js
├── select2/
│   ├── select2-core.js
│   ├── select-adapter.js
│   └── ...
├── fleet/
│   ├── fleet-dispatcher.js
│   └── fleet-helper.js
├── marketplace/
│   └── marketplace.js
├── alliance/
│   ├── alliance.js
│   └── alliance-class-boxes.js
├── technology/
│   └── technology-details.js
├── ...
└── entry/
    └── init.js                  # Top-level bootstrap code
```

---

## Phase 6 — Validation Strategy

### Critical: Every extraction must be validated

1. **Syntax check** each chunk:
   ```bash
   for f in resources/js/ingame/chunks/**/*.js; do node --check "$f" || echo "FAIL: $f"; done
   ```

2. **Reassembly test** — concatenate all chunks in manifest order + vendor libs:
   ```bash
   node scripts/assemble-chunks.js > reassembled.js
   diff <(uglifyjs reassembled.js) <(uglifyjs original.js)
   ```
   - Use `terser` to minify both the reassembled and original versions
   - If they produce identical minified output → extraction was lossless

3. **Behavioral test** — if there are existing integration tests or the game loads in a browser:
   - Point the game at the chunked version (via a loader script)
   - Verify key pages still function

4. **Incremental extraction** — extract ONE chunk at a time, reassemble, validate. This makes it trivial to identify which extraction broke something.

---

## Phase 7 — Manual Refactoring (Post-Chunking)

After automated chunking, each chunk file can be incrementally improved:

### Per-chunk improvements (human + AI assisted)
- Convert `var` → `const` / `let`
- Convert `ClassName.prototype.method = function` → ES6 `class` syntax
- Extract magic numbers into named constants
- Add missing JSDoc types
- Rename single-letter variables (`e`, `t`, `i`) to meaningful names based on usage context
- Split very large classes further (e.g., FleetDispatcher at 130 methods)

### Tool-assisted renaming
- Use **ast-grep** to find all usages of a variable before renaming:
  ```
  sg -p 'var $NAME' ingame.beautified.js
  ```
- Use **jscodeshift** codemods for repetitive refactors (e.g., var→const)

---

## Phase 8 — Integration

### Option A: Concatenation (current approach)
- Keep `vite.config.js` pointing to a single entry that imports chunked files
- Or use Vite's `rollupOptions.input` to preserve the concatenation approach

### Option B: ES Modules (long-term)
- Convert each chunk to an ES module with explicit `export`/`import`
- This enables tree-shaking and lazy loading
- Requires converting all `window.X = ...` globals to proper imports

---

## Phase 9 — Documentation & Insights

As chunks are understood, document:
- `chunks/{name}/README.md` — what this chunk does, key entry points
- Call graphs for complex subsystems (Fleet, Marketplace)
- AJAX endpoint inventory — all URLs the JS calls
- WebSocket event inventory — all Reverb/Pusher events listened to

---

## Tool Quick Reference

| Task | Tool | Command |
|---|---|---|
| Beautify | Prettier | `npx prettier --write file.js` |
| Beautify (fallback) | js-beautify | `npx js-beautify -s 2 -r file.js` |
| Beautify (fallback 2) | uglify-js | `npx uglify-js file.js -b -o out.js` |
| Parse AST | Babel | `node -e "require('@babel/parser').parse(...)"` |
| Parse AST (fallback) | acorn | `npx acorn file.js` |
| Structural search | ast-grep | `sg -p 'pattern' file.js` |
| Codemod | jscodeshift | `npx jscodeshift -t transform.js file.js` |
| Codemod (declarative) | putout | `npx putout file.js` |
| Dependency graph | madge | `npx madge --image graph.svg file.js` |
| Dependency graph (alt) | dependency-cruiser | `npx depcruise --output-type dot file.js` |
| Deobfuscate | webcrack | `npx webcrack file.js -o out.js` |
| Deobfuscate (alt) | js-deobfuscator | `js-deobfuscator -i file.js -o out.js` |
| Deobfuscate (alt 2) | synchrony | `synchrony deobfuscate file.js` |
| Minify (for diff) | terser | `npx terser file.js -c -m` |
| Syntax check | node | `node --check file.js` |

---

## Estimated Effort

| Phase | Effort | Risk |
|---|---|---|
| 0. Install tools | 30 min | Low |
| 1. Beautification | 1-2 hrs (tool trial/error) | Low |
| 2. Deobfuscation | 1-3 hrs (if needed) | Medium (tools may break syntax) |
| 3. AST Analysis | 2-4 hrs (write analysis script) | Medium (parser compatibility) |
| 4. Chunking strategy | 1 hr (review + adjust inventory) | Low |
| 5. Automated extraction | 3-6 hrs (write + debug chunking script) | High (subtle ordering bugs) |
| 6. Validation | 2-4 hrs (per-chunk validation loops) | Medium |
| 7. Manual refactoring | Ongoing, per-chunk | Low (isolated changes) |
| 8. Integration | 1-2 hrs | Low |
| **Total (first milestone)** | **~15-25 hrs** | |

---

## Risks & Mitigations

| Risk | Mitigation |
|---|---|
| Parser crashes on legacy JS | Try acorn, espree, or esprima (more permissive than Babel) |
| Chunks have hidden ordering dependencies | Incremental extraction + reassembly test after each chunk |
| Prototype assignments span non-contiguous lines | AST-based extraction groups by class name, not by line proximity |
| Some code is intentionally obfuscated | Skip deobfuscation if it breaks syntax; use human analysis instead |
| Vendor libs mixed into main file | Identify via signature patterns (e.g., Select2 internal classes) and extract to vendor/ |
