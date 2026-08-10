/**
 * Reassembly & validation script.
 * Concatenates all chunks in manifest order and compares with the original file.
 *
 * Usage: node scripts/validate-chunks.js
 */

import * as fs from "node:fs";
import * as path from "node:path";
import { execSync } from "node:child_process";

const CHUNKS_DIR = "resources/js/ingame/chunks";
const MANIFEST = path.join(CHUNKS_DIR, "manifest.json");
const ORIGINAL = "resources/js/ingame/e7c74974620fa35b197315ebdbb8c2.js";
const REASSEMBLED = path.join(CHUNKS_DIR, "_reassembled.js");

const manifest = JSON.parse(fs.readFileSync(MANIFEST, "utf8"));

// ---- Reassemble ----
let reassembled = "";
for (const chunk of manifest.chunks) {
  const chunkPath = path.join(CHUNKS_DIR, chunk.path);
  if (!fs.existsSync(chunkPath)) {
    console.error(`MISSING: ${chunk.path}`);
    process.exit(1);
  }
  reassembled += fs.readFileSync(chunkPath, "utf8");
}

fs.writeFileSync(REASSEMBLED, reassembled);

// ---- Compare sizes ----
const originalSize = fs.statSync(ORIGINAL).size;
const reassembledSize = fs.statSync(REASSEMBLED).size;
console.log(`Original:    ${originalSize.toLocaleString()} bytes`);
console.log(`Reassembled: ${reassembledSize.toLocaleString()} bytes`);
console.log(`Delta:       ${(reassembledSize - originalSize).toLocaleString()} bytes`);

// ---- Syntax check reassembled ----
try {
  execSync(`node --check "${REASSEMBLED}"`, { stdio: "pipe" });
  console.log("Syntax:      OK");
} catch (err) {
  console.log("Syntax:      FAILED");
  console.log(err.stderr?.toString().slice(0, 500));
}

// ---- Diff (first 200 lines of diff) ----
console.log("\n=== Diff (first 30 lines of differences) ===");
try {
  const diff = execSync(`diff "${ORIGINAL}" "${REASSEMBLED}"`, {
    stdio: "pipe",
    maxBuffer: 1024 * 1024,
  }).toString();
  const lines = diff.split("\n");
  if (lines.length <= 1) {
    console.log("NO DIFFERENCES — reassembly is byte-perfect!");
  } else {
    console.log(`DIFF LINES: ${lines.length - 1}`);
    console.log(lines.slice(0, 30).join("\n"));
  }
} catch (err) {
  // diff exits with code 1 when there are differences
  const diff = err.stdout?.toString() || "";
  const lines = diff.split("\n");
  console.log(`DIFF LINES: ${lines.length}`);
  console.log(lines.slice(0, 30).join("\n"));
}

// ---- Normalized comparison (re-minify both and compare) ----
console.log("\n=== Normalized comparison (re-minified) ===");
try {
  execSync(`npx terser "${ORIGINAL}" -c -m -o /tmp/orig.min.js 2>/dev/null`, { stdio: "pipe" });
  execSync(`npx terser "${REASSEMBLED}" -c -m -o /tmp/reass.min.js 2>/dev/null`, { stdio: "pipe" });

  const origMin = fs.readFileSync("/tmp/orig.min.js", "utf8");
  const reassMin = fs.readFileSync("/tmp/reass.min.js", "utf8");

  if (origMin === reassMin) {
    console.log("MINIFIED MATCH — extraction is semantically identical!");
  } else {
    console.log(`Original minified:    ${origMin.length.toLocaleString()} bytes`);
    console.log(`Reassembled minified: ${reassMin.length.toLocaleString()} bytes`);
    console.log(`Delta:                ${(reassMin.length - origMin.length).toLocaleString()} bytes`);

    // Show first differing characters
    for (let i = 0; i < Math.min(origMin.length, reassMin.length); i++) {
      if (origMin[i] !== reassMin[i]) {
        console.log(`First difference at char ${i}:`);
        console.log(`  Original:    ...${origMin.slice(Math.max(0, i - 20), i + 20)}...`);
        console.log(`  Reassembled: ...${reassMin.slice(Math.max(0, i - 20), i + 20)}...`);
        break;
      }
    }
  }
} catch (err) {
  console.log("Minification comparison failed:", err.message?.slice(0, 200));
}

// ---- Chunk size stats ----
console.log("\n=== Chunk Size Stats ===");
const sizes = manifest.chunks.map((c) => ({
  name: c.path,
  lines: c.lineCount,
  category: c.category,
}));

// Top 10 largest
console.log("\nTop 10 largest chunks:");
sizes
  .sort((a, b) => b.lines - a.lines)
  .slice(0, 10)
  .forEach((s) => console.log(`  ${s.name.padEnd(50)} ${String(s.lines).padStart(5)} lines  [${s.category}]`));

// Smallest chunks
const tiny = sizes.filter((s) => s.lines <= 3);
console.log(`\nTiny chunks (≤3 lines): ${tiny.length}`);
console.log(`Medium chunks (4-20 lines): ${sizes.filter((s) => s.lines > 3 && s.lines <= 20).length}`);
console.log(`Large chunks (>20 lines): ${sizes.filter((s) => s.lines > 20).length}`);
