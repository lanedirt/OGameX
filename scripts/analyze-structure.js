/**
 * Structural analysis script for the OGame in-game JavaScript.
 *
 * Parses the beautified file and produces:
 * 1. inventory.json — all classes, functions, globals with line ranges
 * 2. A summary report on stdout
 *
 * Usage: node scripts/analyze-structure.js
 */

import * as fs from "node:fs";
import * as path from "node:path";
import * as acorn from "acorn";

const INPUT = "resources/js/ingame/e7c74974620fa35b197315ebdbb8c2.js";
const OUTPUT = "storage/app/ingame-inventory.json";

const code = fs.readFileSync(INPUT, "utf8");

// Parse with acorn (more permissive than Babel for legacy pre-ES6 code)
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
  console.error("acorn parse failed:", err.message);
  process.exit(1);
}

const body = ast.body;

// ---- Data structures for the inventory ----

/** @type {Map<string, {name: string, methods: Array<{name: string, startLine: number, endLine: number}>, startLine: number, endLine: number}>} */
const classes = new Map();

/** @type {Array<{name: string, startLine: number, endLine: number}>} */
const standaloneFunctions = [];

/** @type {Array<{name: string, startLine: number, endLine: number}>} */
const jqueryPlugins = [];

/** @type {Array<{name: string, startLine: number, endLine: number, initValue?: string}>} */
const globalVars = [];

/** @type {Array<{startLine: number, endLine: number}>} */
const iifeBlocks = [];

/** @type {Array<{name: string, startLine: number}>} */
const windowAssignments = [];

// ---- Helper: get line number ----
function lineOf(node) {
  return node.loc ? node.loc.start.line : -1;
}
function endLineOf(node) {
  return node.loc ? node.loc.end.line : -1;
}

// ---- Walk top-level statements ----
for (const stmt of body) {
  // --- IIFE: (function() { ... })() ---
  if (
    stmt.type === "ExpressionStatement" &&
    stmt.expression.type === "CallExpression" &&
    (stmt.expression.callee.type === "FunctionExpression" ||
      (stmt.expression.callee.type === "ParenthesizedExpression" &&
        stmt.expression.callee.expression?.type === "FunctionExpression"))
  ) {
    iifeBlocks.push({ startLine: lineOf(stmt), endLine: endLineOf(stmt) });
    continue;
  }

  // --- IIFE variant: !function(){}() or ~function(){}() etc ---
  if (
    stmt.type === "ExpressionStatement" &&
    stmt.expression.type === "UnaryExpression" &&
    stmt.expression.argument?.type === "CallExpression" &&
    stmt.expression.argument.callee?.type === "FunctionExpression"
  ) {
    iifeBlocks.push({ startLine: lineOf(stmt), endLine: endLineOf(stmt) });
    continue;
  }

  // --- var declarations at top level ---
  if (stmt.type === "VariableDeclaration") {
    for (const decl of stmt.declarations) {
      if (decl.id.type === "Identifier") {
        const name = decl.id.name;
        // Check if init is a function expression → named class-like or standalone function
        if (
          decl.init &&
          (decl.init.type === "FunctionExpression" || decl.init.type === "ArrowFunctionExpression")
        ) {
          // Check if there are prototype assignments nearby → class-like
          // We'll handle prototype grouping in a second pass
          standaloneFunctions.push({
            name,
            startLine: lineOf(stmt),
            endLine: endLineOf(stmt),
          });
        } else {
          // Global variable
          globalVars.push({
            name,
            startLine: lineOf(stmt),
            endLine: endLineOf(stmt),
            initValue: decl.init ? code.slice(decl.init.range[0], decl.init.range[1]).slice(0, 80) : undefined,
          });
        }
      }
    }
    continue;
  }

  // --- Assignment expressions: X.prototype.Y = function, $.fn.X = ..., window.X = ... ---
  if (stmt.type === "ExpressionStatement" && stmt.expression.type === "AssignmentExpression") {
    const left = stmt.expression.left;
    const right = stmt.expression.right;

    // $.fn.pluginName = function/object
    if (
      left.type === "MemberExpression" &&
      left.object.type === "MemberExpression" &&
      left.object.object.type === "Identifier" &&
      left.object.object.name === "$" &&
      left.object.property.type === "Identifier" &&
      left.object.property.name === "fn"
    ) {
      const pluginName = left.property.type === "Identifier" ? left.property.name : "[computed]";
      jqueryPlugins.push({
        name: pluginName,
        startLine: lineOf(stmt),
        endLine: endLineOf(stmt),
      });
      continue;
    }

    // window.Name = ...
    if (
      left.type === "MemberExpression" &&
      left.object.type === "Identifier" &&
      left.object.name === "window" &&
      left.property.type === "Identifier"
    ) {
      windowAssignments.push({ name: left.property.name, startLine: lineOf(stmt) });
      continue;
    }

    // ClassName.prototype.method = function(...) { ... }
    if (
      left.type === "MemberExpression" &&
      left.object.type === "MemberExpression" &&
      left.object.property.type === "Identifier" &&
      left.object.property.name === "prototype" &&
      left.object.object.type === "Identifier"
    ) {
      const className = left.object.object.name;
      const methodName = left.property.type === "Identifier" ? left.property.name : "[computed]";

      if (!classes.has(className)) {
        classes.set(className, {
          name: className,
          methods: [],
          startLine: lineOf(stmt),
          endLine: endLineOf(stmt),
        });
      }

      const cls = classes.get(className);
      cls.methods.push({
        name: methodName,
        startLine: lineOf(stmt),
        endLine: endLineOf(stmt),
      });
      // Extend the class range
      if (lineOf(stmt) < cls.startLine) cls.startLine = lineOf(stmt);
      if (endLineOf(stmt) > cls.endLine) cls.endLine = endLineOf(stmt);
      continue;
    }

    // SomeObject.staticMethod = function(...)
    if (left.type === "MemberExpression" && left.object.type === "Identifier" && left.property.type === "Identifier") {
      const objName = left.object.name;
      const methodName = left.property.name;
      // If this matches a known class name, treat as static method
      if (classes.has(objName)) {
        const cls = classes.get(objName);
        cls.methods.push({
          name: `static.${methodName}`,
          startLine: lineOf(stmt),
          endLine: endLineOf(stmt),
        });
        if (lineOf(stmt) < cls.startLine) cls.startLine = lineOf(stmt);
        if (endLineOf(stmt) > cls.endLine) cls.endLine = endLineOf(stmt);
      }
      continue;
    }
  }

  // --- Named function declarations ---
  if (stmt.type === "FunctionDeclaration" && stmt.id) {
    standaloneFunctions.push({
      name: stmt.id.name,
      startLine: lineOf(stmt),
      endLine: endLineOf(stmt),
    });
    continue;
  }
}

// ---- Second pass: identify which standaloneFunctions are actually class constructors ----
// If a var X = function(...) {...} has prototype assignments, it's a class, not standalone.
const classConstructorNames = new Set(classes.keys());
const trulyStandalone = standaloneFunctions.filter((f) => !classConstructorNames.has(f.name));

// ---- Build inventory ----
const inventory = {
  file: INPUT,
  totalLines: code.split("\n").length,
  summary: {
    classes: classes.size,
    classMethods: [...classes.values()].reduce((sum, c) => sum + c.methods.length, 0),
    standaloneFunctions: trulyStandalone.length,
    jqueryPlugins: jqueryPlugins.length,
    globalVars: globalVars.length,
    windowAssignments: windowAssignments.length,
    iifeBlocks: iifeBlocks.length,
  },
  classes: [...classes.values()]
    .sort((a, b) => b.methods.length - a.methods.length)
    .map((c) => ({
      name: c.name,
      methodCount: c.methods.length,
      startLine: c.startLine,
      endLine: c.endLine,
      methods: c.methods.slice(0, 5).map((m) => m.name), // first 5 method names
    })),
  jqueryPlugins: jqueryPlugins.map((p) => ({
    name: p.name,
    startLine: p.startLine,
    endLine: p.endLine,
  })),
  standaloneFunctions: trulyStandalone.map((f) => ({
    name: f.name,
    startLine: f.startLine,
    endLine: f.endLine,
  })),
  windowAssignments: windowAssignments.map((w) => ({
    name: w.name,
    startLine: w.startLine,
  })),
  globalVars: globalVars.map((v) => ({
    name: v.name,
    startLine: v.startLine,
    endLine: v.endLine,
  })),
  iifeBlocks: iifeBlocks.map((i) => ({
    startLine: i.startLine,
    endLine: i.endLine,
  })),
};

// ---- Write output ----
fs.mkdirSync(path.dirname(OUTPUT), { recursive: true });
fs.writeFileSync(OUTPUT, JSON.stringify(inventory, null, 2));

// ---- Print summary ----
console.log("=== OGame In-Game JS Structural Analysis ===");
console.log(`File: ${INPUT}`);
console.log(`Lines: ${inventory.totalLines.toLocaleString()}`);
console.log();
console.log(`Classes:              ${inventory.summary.classes}`);
console.log(`  Total methods:      ${inventory.summary.classMethods}`);
console.log(`Standalone functions: ${inventory.summary.standaloneFunctions}`);
console.log(`jQuery plugins:       ${inventory.summary.jqueryPlugins}`);
console.log(`Global vars:          ${inventory.summary.globalVars}`);
console.log(`Window assignments:   ${inventory.summary.windowAssignments}`);
console.log(`IIFE blocks:          ${inventory.summary.iifeBlocks}`);
console.log();
console.log("=== Top 20 Classes by Method Count ===");
for (const cls of inventory.classes.slice(0, 20)) {
  console.log(
    `  ${cls.name.padEnd(30)} ${String(cls.methodCount).padStart(4)} methods  (lines ${cls.startLine}-${cls.endLine})`,
  );
}
console.log();
console.log("=== jQuery Plugins ===");
for (const p of inventory.jqueryPlugins) {
  console.log(`  $.fn.${p.name}  (line ${p.startLine})`);
}
console.log();
console.log("=== Global Variables ===");
for (const v of inventory.globalVars.slice(0, 20)) {
  console.log(`  var ${v.name}  (line ${v.startLine})`);
}
console.log();
console.log(`Full inventory written to: ${OUTPUT}`);
