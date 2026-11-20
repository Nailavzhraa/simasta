import { spawn } from 'node:child_process';
import { fileURLToPath } from 'node:url';
import { verifyPassword as verifyScrypt } from './security.js';

export function phpPassword(input) {
  return new Promise((resolve, reject) => {
    const child = spawn(process.env.SIMASTA_PHP_BINARY || '/usr/bin/php',
      ['-n', fileURLToPath(new URL('./verify-simasta-password.php', import.meta.url))],
      { stdio: ['pipe', 'pipe', 'ignore'], windowsHide: true });
    let output = '';
    const timer = setTimeout(() => child.kill(), 5000);
    child.stdout.on('data', chunk => { output += chunk; if (output.length > 1024) child.kill(); });
    child.on('error', () => { clearTimeout(timer); reject(new Error('Password verifier unavailable')); });
    child.on('close', code => {
      clearTimeout(timer);
      if (code === 0 && output.length <= 1024) resolve(output);
      else reject(new Error('Password verifier unavailable'));
    });
    child.stdin.on('error', () => {});
    child.stdin.end(JSON.stringify(input));
  });
}

export async function verifyCentralPassword(password, hash) {
  if (typeof password !== 'string' || Buffer.byteLength(password) > 256 || typeof hash !== 'string') return false;
  if (hash.startsWith('scrypt$')) return verifyScrypt(password, hash);
  if (/^\$2[aby]\$\d{2}\$[./A-Za-z0-9]{53}$/.test(hash)) {
    return await phpPassword({ mode: 'verify', password, hash }) === 'true';
  }
  return false;
}
