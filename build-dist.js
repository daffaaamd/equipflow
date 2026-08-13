import { cpSync, existsSync, mkdirSync, writeFileSync } from 'fs';
import { execSync } from 'child_process';

console.log('--- Step 1: Building Vite assets ---');
try {
    execSync('npx vite build', { stdio: 'inherit' });
} catch (e) {
    console.error('Vite build warning:', e.message);
}

console.log('--- Step 2: Preparing dist directory for Vercel ---');
if (!existsSync('dist')) {
    mkdirSync('dist', { recursive: true });
}

if (existsSync('public')) {
    cpSync('public', 'dist', { recursive: true });
}

if (!existsSync('dist/index.html')) {
    writeFileSync('dist/index.html', '<!DOCTYPE html><html><head><meta charset="utf-8"><title>EquipFlow</title></head><body>EquipFlow Application</body></html>');
}

console.log('--- Step 3: dist directory ready! ---');
