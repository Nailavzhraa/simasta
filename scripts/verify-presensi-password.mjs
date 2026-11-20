import { scrypt, timingSafeEqual } from 'node:crypto';
import { promisify } from 'node:util';

// Passwords enter through stdin, never command arguments, files, or logs.
try {
  const chunks = [];
  let size = 0;
  for await (const chunk of process.stdin) {
    if ((size += chunk.length) > 4096) throw new Error();
    chunks.push(chunk);
  }
  const { password, hash } = JSON.parse(Buffer.concat(chunks).toString());
  const parts = typeof hash === 'string' ? hash.split('$') : [];
  let matches = false;
  if (typeof password === 'string' && Buffer.byteLength(password) <= 256
      && parts.length === 6 && parts.slice(0, 4).join('$') === 'scrypt$32768$8$1'
      && /^[a-f0-9]{32}$/.test(parts[4]) && /^[a-f0-9]{128}$/.test(parts[5])) {
    const actual = await promisify(scrypt)(password, parts[4], 64,
      { N: 32768, r: 8, p: 1, maxmem: 64 * 1024 * 1024 });
    matches = timingSafeEqual(actual, Buffer.from(parts[5], 'hex'));
  }
  process.stdout.write(matches ? 'true' : 'false');
} catch {
  process.stdout.write('false');
  process.exitCode = 1;
}
