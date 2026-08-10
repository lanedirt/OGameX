/**
 * Automated chunking script for the OGame in-game JavaScript.
 *
 * Parses the beautified file, identifies logical boundaries (classes,
 * jQuery plugins, IIFEs, standalone functions), and extracts each
 * unit into its own file under resources/js/ingame/chunks/.
 *
 * Usage: node scripts/chunk-ingame.js
 */

import * as fs from "node:fs";
import * as path from "node:path";
import * as acorn from "acorn";

const INPUT = "resources/js/ingame/e7c74974620fa35b197315ebdbb8c2.js";
const OUTPUT_DIR = "resources/js/ingame/chunks";
const MANIFEST = path.join(OUTPUT_DIR, "manifest.json");

const code = fs.readFileSync(INPUT, "utf8");
const lines = code.split("\n");

// ---- Parse ----
let ast;
try {
  ast = acorn.parse(code, {
    ecmaVersion: 2020,
    sourceType: "script",
    locations: true,
    ranges: true,
    allowReserved: true,
    allowReturnOutsideFunction: true,
    allowImportExportEverywhere: true,
    allowHashBang: true,
  });
} catch (err) {
  console.error("Parse error:", err.message);
  process.exit(1);
}

// ---- Helper functions ----
function getName(node) {
  // Get the name from various LHS patterns
  if (!node) return null;

  // Identifier
  if (node.type === "Identifier") return node.name;

  // X.prototype.Y → className = X, methodName = Y
  if (
    node.type === "MemberExpression" &&
    node.object?.type === "MemberExpression" &&
    node.object.property?.type === "Identifier" &&
    node.object.property.name === "prototype" &&
    node.object.object?.type === "Identifier"
  ) {
    return {
      type: "prototype",
      className: node.object.object.name,
      methodName: node.property?.type === "Identifier" ? node.property.name : null,
    };
  }

  // $.fn.X → plugin name
  if (
    node.type === "MemberExpression" &&
    node.object?.type === "MemberExpression" &&
    node.object.object?.type === "Identifier" &&
    node.object.object.name === "$" &&
    node.object.property?.type === "Identifier" &&
    node.object.property.name === "fn"
  ) {
    return {
      type: "jqueryPlugin",
      name: node.property?.type === "Identifier" ? node.property.name : null,
    };
  }

  // window.X → global
  if (
    node.type === "MemberExpression" &&
    node.object?.type === "Identifier" &&
    node.object.name === "window" &&
    node.property?.type === "Identifier"
  ) {
    return { type: "window", name: node.property.name };
  }

  // X.Y = ... → static method
  if (node.type === "MemberExpression" && node.object?.type === "Identifier" && node.property?.type === "Identifier") {
    return { type: "staticMethod", className: node.object.name, methodName: node.property.name };
  }

  return null;
}

function isFunctionExpr(node) {
  return node?.type === "FunctionExpression" || node?.type === "ArrowFunctionExpression";
}

function isAssignmentToFunction(stmt) {
  return (
    stmt.type === "ExpressionStatement" &&
    stmt.expression.type === "AssignmentExpression" &&
    isFunctionExpr(stmt.expression.right)
  );
}

function isVarWithFunction(stmt) {
  return (
    stmt.type === "VariableDeclaration" &&
    stmt.declarations.length === 1 &&
    stmt.declarations[0].id?.type === "Identifier" &&
    isFunctionExpr(stmt.declarations[0].init)
  );
}

function isIIFE(stmt) {
  if (stmt.type !== "ExpressionStatement") return false;
  const expr = stmt.expression;
  // (function(){})()
  if (
    expr.type === "CallExpression" &&
    (expr.callee.type === "FunctionExpression" ||
      (expr.callee.type === "ParenthesizedExpression" && expr.callee.expression?.type === "FunctionExpression"))
  ) {
    return true;
  }
  // !function(){}()
  if (
    expr.type === "UnaryExpression" &&
    expr.argument?.type === "CallExpression" &&
    expr.argument.callee?.type === "FunctionExpression"
  ) {
    return true;
  }
  return false;
}

// ---- Classification ----
/**
 * @typedef {Object} Chunk
 * @property {string} id
 * @property {string} type - "class" | "jquery-plugin" | "iife" | "function" | "standalone" | "unknown"
 * @property {string} name
 * @property {number} startLine
 * @property {number} endLine
 * @property {string} category - high-level grouping
 */

/** @type {Chunk[]} */
const chunks = [];

// Track which class is currently "open" (we're collecting its prototype methods)
let currentClass = null;
let currentClassStartLine = null;
let currentClassEndLine = null;
let classChunkStatements = [];

// Track prototype assignments to group by class name
const protoAssignments = new Map(); // className -> [{endLine, ...}]

// ---- Pre-pass: collect all prototype class names ----
// We need this before the main pass because constructors appear before
// their prototype assignments in the source order.
for (const stmt of ast.body) {
  if (
    stmt.type === "ExpressionStatement" &&
    stmt.expression.type === "AssignmentExpression"
  ) {
    const lhs = getName(stmt.expression.left);
    if (lhs && lhs.type === "prototype") {
      if (!protoAssignments.has(lhs.className)) {
        protoAssignments.set(lhs.className, []);
      }
      protoAssignments.get(lhs.className).push(stmt.loc.end.line);
    }
  }
}

// ---- First Pass: classify every top-level statement ----
for (let i = 0; i < ast.body.length; i++) {
  const stmt = ast.body[i];
  const startLine = stmt.loc.start.line;
  const endLine = stmt.loc.end.line;

  // --- IIFE ---
  if (isIIFE(stmt)) {
    // Finalize any open class
    if (currentClass) {
      chunks.push({
        id: `class-${currentClass.toLowerCase()}`,
        type: "class",
        name: currentClass,
        startLine: currentClassStartLine,
        endLine: currentClassEndLine,
        category: categorize(currentClass),
      });
      currentClass = null;
    }

    chunks.push({
      id: `iife-${startLine}`,
      type: "iife",
      name: `IIFE_${startLine}`,
      startLine,
      endLine,
      category: "iife",
    });
    continue;
  }

  // --- var ClassName = function(...){...} ---
  if (isVarWithFunction(stmt)) {
    const varName = stmt.declarations[0].id.name;

    // Finalize any open class
    if (currentClass && currentClass !== varName) {
      chunks.push({
        id: `class-${currentClass.toLowerCase()}`,
        type: "class",
        name: currentClass,
        startLine: currentClassStartLine,
        endLine: currentClassEndLine,
        category: categorize(currentClass),
      });
    }

    // Check if this var has prototype assignments elsewhere
    if (protoAssignments.has(varName) || isLikelyClass(varName)) {
      // Start a new class chunk
      currentClass = varName;
      currentClassStartLine = startLine;
      currentClassEndLine = endLine;
    } else {
      // Standalone function
      currentClass = null;
      chunks.push({
        id: `func-${varName.toLowerCase()}`,
        type: "function",
        name: varName,
        startLine,
        endLine,
        category: "functions",
      });
    }
    continue;
  }

  // --- ClassName.prototype.method = function(...){...} ---
  if (isAssignmentToFunction(stmt)) {
    const lhs = getName(stmt.expression.left);
    if (lhs && lhs.type === "prototype") {
      // Track this prototype assignment
      if (!protoAssignments.has(lhs.className)) {
        protoAssignments.set(lhs.className, []);
      }
      protoAssignments.get(lhs.className).push(endLine);

      if (currentClass === lhs.className) {
        // Continue the current class chunk
        currentClassEndLine = endLine;
      } else {
        // New class (prototype without preceding var declaration)
        if (currentClass) {
          chunks.push({
            id: `class-${currentClass.toLowerCase()}`,
            type: "class",
            name: currentClass,
            startLine: currentClassStartLine,
            endLine: currentClassEndLine,
            category: categorize(currentClass),
          });
        }
        currentClass = lhs.className;
        currentClassStartLine = startLine;
        currentClassEndLine = endLine;
      }
      continue;
    }

    // --- $.fn.pluginName = function(...){...} ---
    if (lhs && lhs.type === "jqueryPlugin") {
      // Finalize open class
      if (currentClass) {
        chunks.push({
          id: `class-${currentClass.toLowerCase()}`,
          type: "class",
          name: currentClass,
          startLine: currentClassStartLine,
          endLine: currentClassEndLine,
          category: categorize(currentClass),
        });
        currentClass = null;
      }

      // Don't chunk the core jQuery plugins that are vendor libs
      // (dump, hoverIntent, markItUp, validationEngine, wipetouch, select2)
      const vendorPlugins = ["dump", "hoverIntent", "markItUp", "markItUpRemove", "validationEngine", "wipetouch", "select2"];
      if (vendorPlugins.includes(lhs.name)) {
        chunks.push({
          id: `standalone-jquery-${lhs.name}`,
          type: "standalone",
          name: `$.fn.${lhs.name}`,
          startLine,
          endLine,
          category: "jquery-vendor",
        });
      } else {
        chunks.push({
          id: `jqplugin-${lhs.name}`,
          type: "jquery-plugin",
          name: lhs.name,
          startLine,
          endLine,
          category: "jquery-plugins",
        });
      }
      continue;
    }

    // --- ClassName.staticMethod = function ---
    if (lhs && lhs.type === "staticMethod") {
      if (currentClass === lhs.className) {
        currentClassEndLine = endLine;
        continue;
      }
    }

    // --- window.X = ... ---
    if (lhs && lhs.type === "window") {
      if (currentClass) {
        chunks.push({
          id: `class-${currentClass.toLowerCase()}`,
          type: "class",
          name: currentClass,
          startLine: currentClassStartLine,
          endLine: currentClassEndLine,
          category: categorize(currentClass),
        });
        currentClass = null;
      }
      chunks.push({
        id: `window-${lhs.name.toLowerCase()}`,
        type: "standalone",
        name: `window.${lhs.name}`,
        startLine,
        endLine,
        category: "globals",
      });
      continue;
    }
  }

  // --- Named function declaration ---
  if (stmt.type === "FunctionDeclaration" && stmt.id) {
    const funcName = stmt.id.name;

    // If this function name matches a class with prototype assignments,
    // treat it as a class constructor (e.g., function Missile(cfg) { ... })
    if (protoAssignments.has(funcName)) {
      // Finalize any open class
      if (currentClass && currentClass !== funcName) {
        chunks.push({
          id: `class-${currentClass.toLowerCase()}`,
          type: "class",
          name: currentClass,
          startLine: currentClassStartLine,
          endLine: currentClassEndLine,
          category: categorize(currentClass),
        });
      }
      currentClass = funcName;
      currentClassStartLine = startLine;
      currentClassEndLine = endLine;
    } else {
      // Regular standalone function
      if (currentClass) {
        chunks.push({
          id: `class-${currentClass.toLowerCase()}`,
          type: "class",
          name: currentClass,
          startLine: currentClassStartLine,
          endLine: currentClassEndLine,
          category: categorize(currentClass),
        });
        currentClass = null;
      }
      chunks.push({
        id: `func-${funcName.toLowerCase()}`,
        type: "function",
        name: funcName,
        startLine,
        endLine,
        category: "functions",
      });
    }
    continue;
  }

  // --- var X = ... (non-function global) ---
  if (stmt.type === "VariableDeclaration") {
    if (currentClass) {
      chunks.push({
        id: `class-${currentClass.toLowerCase()}`,
        type: "class",
        name: currentClass,
        startLine: currentClassStartLine,
        endLine: currentClassEndLine,
        category: categorize(currentClass),
      });
      currentClass = null;
    }

    for (const decl of stmt.declarations) {
      if (decl.id?.type === "Identifier") {
        chunks.push({
          id: `var-${decl.id.name.toLowerCase()}`,
          type: "standalone",
          name: `var ${decl.id.name}`,
          startLine,
          endLine,
          category: "globals",
        });
      }
    }
    continue;
  }

  // --- Everything else: close open class, collect as standalone ---
  if (currentClass) {
    chunks.push({
      id: `class-${currentClass.toLowerCase()}`,
      type: "class",
      name: currentClass,
      startLine: currentClassStartLine,
      endLine: currentClassEndLine,
      category: categorize(currentClass),
    });
    currentClass = null;
  }

  // Any remaining expression statements, etc.
  chunks.push({
    id: `stmt-${startLine}`,
    type: "standalone",
    name: `statement_${startLine}`,
    startLine,
    endLine,
    category: "misc",
  });
}

// Finalize last open class
if (currentClass) {
  chunks.push({
    id: `class-${currentClass.toLowerCase()}`,
    type: "class",
    name: currentClass,
    startLine: currentClassStartLine,
    endLine: currentClassEndLine,
    category: categorize(currentClass),
  });
}

// ---- Helper: categorize a class name into a high-level group ----
function categorize(name) {
  const map = {
    TimerHandler: "core",
    FleetDispatcher: "fleet",
    FleetHelper: "fleet",
    Marketplace: "marketplace",
    Alliance: "alliance",
    AllianceClassBoxes: "alliance",
    HappyEdit: "chat",
    OGameLineChart: "charts",
    TechnologyDetails: "technology",
    Graveyard: "graveyard",
    Exodus: "exodus",
    Rewarding: "rewarding",
    PercentageBar: "ui",
    OGameSortable: "ui",
    OGamePaginatable: "ui",
    OGameLoadingIndicator: "ui",
    ResourceTicker: "resources",
    LifeformResearch: "lifeform",
    LifeformSettings: "lifeform",
    CharacterClassBoxes: "characters",
    Missile: "missile",
    Notify: "core",
    AjaxAdapter: "core",
    Observable: "core",
    Translation: "core",
    InfiniteScroll: "ui",
    Search: "search",
    // Select2 internals
    Select2: "select2",
    SelectAdapter: "select2",
    BaseAdapter: "select2",
    ArrayAdapter: "select2",
    AjaxAdapter: "select2",
    BaseSelection: "select2",
    SingleSelection: "select2",
    MultipleSelection: "select2",
    Placeholder: "select2",
    AllowClear: "select2",
    HidePlaceholder: "select2",
    Dropdown: "select2",
    Results: "select2",
    Options: "select2",
    Defaults: "select2",
    Tags: "select2",
    Tokenizer: "select2",
    CloseOnSelect: "select2",
    SelectOnClose: "select2",
    MinimumInputLength: "select2",
    MaximumInputLength: "select2",
    MinimumResultsForSearch: "select2",
    MaximumSelectionLength: "select2",
    EventRelay: "select2",
    AttachBody: "select2",
  };
  return map[name] || "other";
}

function isLikelyClass(name) {
  // Heuristic: if the name starts with uppercase, it's likely a class constructor
  return name[0] === name[0].toUpperCase() && name[0] !== name[0].toLowerCase();
}

// ---- Consolidate adjacent standalone chunks of the same category ----
// (Reduce noise from single-line global var statements)
const consolidated = [];
for (let i = 0; i < chunks.length; i++) {
  const prev = consolidated[consolidated.length - 1];
  const curr = chunks[i];

  if (
    prev &&
    prev.type === "standalone" &&
    curr.type === "standalone" &&
    prev.category === curr.category &&
    prev.category === "globals"
  ) {
    // Merge into previous
    prev.endLine = curr.endLine;
    prev.name = `${prev.name}, ${curr.name}`;
  } else {
    consolidated.push({ ...curr });
  }
}

// ---- Assign filenames ----
const rawChunks = consolidated.map((chunk, idx) => {
  let subdir, filename;

  switch (chunk.category) {
    case "core":
      subdir = "core";
      filename = `${chunk.name.toLowerCase().replace(/[^a-z0-9]+/g, "-")}.js`;
      break;
    case "fleet":
      subdir = "fleet";
      filename = `${chunk.name.toLowerCase().replace(/[^a-z0-9]+/g, "-")}.js`;
      break;
    case "marketplace":
      subdir = "marketplace";
      filename = `${chunk.name.toLowerCase().replace(/[^a-z0-9]+/g, "-")}.js`;
      break;
    case "alliance":
      subdir = "alliance";
      filename = `${chunk.name.toLowerCase().replace(/[^a-z0-9]+/g, "-")}.js`;
      break;
    case "chat":
      subdir = "chat";
      filename = `${chunk.name.toLowerCase().replace(/[^a-z0-9]+/g, "-")}.js`;
      break;
    case "charts":
      subdir = "charts";
      filename = `${chunk.name.toLowerCase().replace(/[^a-z0-9]+/g, "-")}.js`;
      break;
    case "technology":
      subdir = "technology";
      filename = `${chunk.name.toLowerCase().replace(/[^a-z0-9]+/g, "-")}.js`;
      break;
    case "graveyard":
      subdir = "graveyard";
      filename = `${chunk.name.toLowerCase().replace(/[^a-z0-9]+/g, "-")}.js`;
      break;
    case "exodus":
      subdir = "exodus";
      filename = `${chunk.name.toLowerCase().replace(/[^a-z0-9]+/g, "-")}.js`;
      break;
    case "rewarding":
      subdir = "rewarding";
      filename = `${chunk.name.toLowerCase().replace(/[^a-z0-9]+/g, "-")}.js`;
      break;
    case "ui":
      subdir = "ui";
      filename = `${chunk.name.toLowerCase().replace(/[^a-z0-9]+/g, "-")}.js`;
      break;
    case "resources":
      subdir = "resources";
      filename = `${chunk.name.toLowerCase().replace(/[^a-z0-9]+/g, "-")}.js`;
      break;
    case "lifeform":
      subdir = "lifeform";
      filename = `${chunk.name.toLowerCase().replace(/[^a-z0-9]+/g, "-")}.js`;
      break;
    case "characters":
      subdir = "characters";
      filename = `${chunk.name.toLowerCase().replace(/[^a-z0-9]+/g, "-")}.js`;
      break;
    case "missile":
      subdir = "missile";
      filename = `${chunk.name.toLowerCase().replace(/[^a-z0-9]+/g, "-")}.js`;
      break;
    case "search":
      subdir = "search";
      filename = `${chunk.name.toLowerCase().replace(/[^a-z0-9]+/g, "-")}.js`;
      break;
    case "select2":
      subdir = "select2";
      filename = `${chunk.name.toLowerCase().replace(/[^a-z0-9]+/g, "-")}.js`;
      break;
    case "jquery-plugins":
      subdir = "jquery-plugins";
      filename = `${chunk.name.toLowerCase().replace(/[^a-z0-9]+/g, "-")}.js`;
      break;
    case "jquery-vendor":
      subdir = "jquery-vendor";
      filename = `${chunk.name.toLowerCase().replace(/[^a-z0-9]+/g, "-")}.js`;
      break;
    case "functions":
      subdir = "functions";
      filename = `${chunk.name.toLowerCase().replace(/[^a-z0-9]+/g, "-")}.js`;
      break;
    case "iife":
      subdir = "iife";
      filename = `iife-${chunk.startLine}.js`;
      break;
    case "globals":
      subdir = "globals";
      filename = `globals-${chunk.startLine}.js`;
      break;
    default:
      subdir = "misc";
      filename = `stmt-${chunk.startLine}.js`;
  }

  return {
    ...chunk,
    subdir,
    filename,
    path: `${subdir}/${filename}`,
  };
});

// ---- Extraction: two strategies ----
// 1. "fullSource" — used for individual chunk files (full AST range, trimmed to not include next chunk)
// 2. "assemblySource" — used for reassembly (cursor-based, no overlaps, covers gaps)
// Sort chunks by startLine
rawChunks.sort((a, b) => a.startLine - b.startLine);

// --- Strategy 1: Individual chunk files with trimmed full ranges ---
// For each chunk, use the full AST range but trim trailing content that
// belongs to the next chunk (acorn includes trailing comments in ranges).
const finalChunks = [];
for (let i = 0; i < rawChunks.length; i++) {
  const chunk = rawChunks[i];
  let srcEndLine = chunk.endLine;

  // If there's a next chunk, trim trailing content that belongs to it
  if (i + 1 < rawChunks.length) {
    const nextStart = rawChunks[i + 1].startLine;
    // Walk backwards from endLine to find where the next chunk's content begins
    for (let line = chunk.endLine; line >= nextStart && line >= chunk.startLine; line--) {
      const lineContent = lines[line - 1] || "";
      // Check if this line starts the next chunk (IIFE, class, function, etc.)
      const isNextChunkStart =
        line === nextStart ||
        /^;\s*\(function\b/.test(lineContent.trim()) ||
        /^\/\*[*!]/.test(lineContent.trim()); // JSDoc comment that might precede next chunk
      if (isNextChunkStart) {
        srcEndLine = line - 1;
      } else {
        break;
      }
    }
  }

  chunk.source = lines.slice(chunk.startLine - 1, srcEndLine).join("\n") + "\n";
  chunk.trimmedEndLine = srcEndLine;
  finalChunks.push(chunk);
}

// --- Strategy 2: Assembly source (cursor-based, no overlaps, including gaps) ---
let cursor = 0;
let assemblySource = "";

for (const chunk of rawChunks) {
  const chunkStart = chunk.startLine - 1;

  // Capture gap content
  if (cursor < chunkStart) {
    const gap = lines.slice(cursor, chunkStart).join("\n");
    if (gap.trim()) {
      assemblySource += gap + "\n";
    }
  }

  // Extract from cursor (skip overlaps)
  const extractStart = Math.max(cursor, chunkStart);
  const slice = lines.slice(extractStart, chunk.endLine).join("\n");
  assemblySource += slice + "\n";

  cursor = chunk.endLine;
}

// Capture tail
if (cursor < lines.length) {
  const tail = lines.slice(cursor).join("\n");
  if (tail.trim()) {
    assemblySource += tail + "\n";
  }
}

// ---- Write chunks to disk ----
// Clean output directory first
if (fs.existsSync(OUTPUT_DIR)) {
  fs.rmSync(OUTPUT_DIR, { recursive: true });
}

for (const chunk of finalChunks) {
  const dir = path.join(OUTPUT_DIR, chunk.subdir);
  fs.mkdirSync(dir, { recursive: true });
  const filepath = path.join(OUTPUT_DIR, chunk.path);
  // Append if file already exists (multiple chunks may share the same filename
  // e.g., class constructor + its prototype methods)
  if (fs.existsSync(filepath)) {
    fs.appendFileSync(filepath, "\n" + chunk.source);
  } else {
    fs.writeFileSync(filepath, chunk.source);
  }
  console.log(`  Wrote: ${chunk.path} (lines ${chunk.startLine}-${chunk.endLine}, ${chunk.source.split("\n").length - 1} lines)`);
}

// Write reassembled file for validation (after directory is created)
fs.writeFileSync(path.join(OUTPUT_DIR, "_reassembled.js"), assemblySource);

// ---- Write manifest (deduplicated by path) ----
const seenPaths = new Set();
const dedupedChunks = [];
for (const c of finalChunks) {
  if (!seenPaths.has(c.path)) {
    seenPaths.add(c.path);
    dedupedChunks.push({
      id: c.id,
      type: c.type,
      name: c.name,
      category: c.category,
      path: c.path,
      startLine: c.startLine,
      endLine: c.endLine,
      lineCount: c.endLine - c.startLine + 1,
    });
  }
}

const manifest = {
  description: "Chunk manifest for OGame in-game JavaScript",
  source: INPUT,
  totalChunks: dedupedChunks.length,
  chunks: dedupedChunks,
};

fs.writeFileSync(MANIFEST, JSON.stringify(manifest, null, 2));

// ---- Print summary ----
console.log(`\n=== Chunking Complete ===`);
console.log(`Total chunks: ${finalChunks.length}`);

const byCategory = {};
for (const c of finalChunks) {
  byCategory[c.category] = (byCategory[c.category] || 0) + 1;
}
console.log(`\nChunks by category:`);
for (const [cat, count] of Object.entries(byCategory).sort()) {
  console.log(`  ${cat.padEnd(20)} ${count}`);
}

const byType = {};
for (const c of finalChunks) {
  byType[c.type] = (byType[c.type] || 0) + 1;
}
console.log(`\nChunks by type:`);
for (const [type, count] of Object.entries(byType).sort()) {
  console.log(`  ${type.padEnd(20)} ${count}`);
}
