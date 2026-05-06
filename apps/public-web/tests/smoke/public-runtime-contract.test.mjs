import assert from 'node:assert/strict';
import { existsSync, readFileSync } from 'node:fs';
import { join } from 'node:path';

const root = process.cwd();
const requiredFiles = [
  'package.json',
  'next.config.ts',
  'tsconfig.json',
  'app/layout.tsx',
  'app/[locale]/page.tsx',
  'app/globals.css',
  'styles/tokens.css',
  'components/ui/ActionLink.tsx',
  'components/ui/SurfaceCard.tsx',
  'components/sections/PublicRuntimePage.tsx',
  'lib/api/publicRuntime.ts',
  'lib/navigation/routes.ts',
  'lib/theme-manifest/kabeeriPublicFoundation.ts',
];

for (const file of requiredFiles) {
  assert.equal(existsSync(join(root, file)), true, `${file} should exist`);
}

const packageJson = JSON.parse(readFileSync(join(root, 'package.json'), 'utf8'));
assert.equal(packageJson.scripts.build, 'next build');
assert.equal(packageJson.scripts.typecheck, 'tsc --noEmit');
assert.equal(packageJson.dependencies.next.startsWith('^16.'), true);

const routes = readFileSync(join(root, 'lib/navigation/routes.ts'), 'utf8');
for (const key of ['home', 'audiences', 'pricing', 'marketplace', 'developers', 'mall']) {
  assert.match(routes, new RegExp(`key: '${key}'`));
}

const manifest = readFileSync(join(root, 'lib/theme-manifest/kabeeriPublicFoundation.ts'), 'utf8');
for (const quality of ['rtl', 'responsive', 'accessible', 'api-only', 'marketplace-mall-separated']) {
  assert.match(manifest, new RegExp(quality));
}

const apiClient = readFileSync(join(root, 'lib/api/publicRuntime.ts'), 'utf8');
assert.match(apiClient, /api\/public-web\/manifest/);
assert.doesNotMatch(apiClient, /DB::|Model::|\.blade/);

const page = readFileSync(join(root, 'components/sections/PublicRuntimePage.tsx'), 'utf8');
assert.match(page, /KABEERI V15 Public Runtime/);
assert.match(page, /Kabeeri Marketplace/);
assert.match(page, /Kabeeri Mall/);
assert.match(page, /GET \/api\/public-web\/manifest/);

const css = readFileSync(join(root, 'styles/tokens.css'), 'utf8');
assert.match(css, /--kbr-ink/);
assert.match(css, /--kbr-bronze/);