# JS Chunking Tooling

Local-only scripts and deps for chunking the in-game JavaScript. Not committed.

## Setup

Install the extra dev dependencies from `package.dev.json`:

```bash
npm install --save-dev $(node -e "const p=require('./package.dev.json'); console.log(Object.entries(p.devDependencies).map(([k,v])=>k+'@'+v).join(' '))")
```

This adds acorn, prettier, and terser to `node_modules`. The main `package.json` is not affected.

## Usage

```bash
# Regenerate chunks from the original minified file
node dev-scripts/chunk-ingame.js

# Validate chunks against the original
node dev-scripts/validate-chunks.js

# Build with Vite
npm run build
```
