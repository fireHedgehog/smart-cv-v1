import axios from 'axios';

const baseURL = import.meta.env.PUBLIC_API_BASE || 'http://localhost:8000';
const publicKeyPem = `-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA1bG8OWN9kActzqaXoi29
yt+eJxc6uTZ0gWfiHulXKPjGlioLZdIVBtRQ7GVZ0qchWO/Fzf7tRKomHlj35H8B
CNSNas4zjnwEI/Yy9xcAfe9HEoD1sylt0iAd4JGpjd2KKm/6LBbOOodp1M8HG1jt
6oTVMXlWSE43d81J9uNofQBBqq2DhgyUTUdfzpxSFRl+KCr0mbBlRD8ZyRFKAW4w
Xv+nm5XlKc1Tiy7mxGCVZc76Fq3xCCP489D/BH+KvkVvq/pYPAEMzphCnwfYOqtH
zxR3ofHgBkQb9ILQCVmTwOGCclSh+N3j/gPa3b8xlb2s/tuENK5rhUI0v6Zy2B+n
+QIDAQAB
-----END PUBLIC KEY-----`;

async function importPublicKey() {
  console.log('API_BASE', baseURL);
  console.log('PUBLIC_API_PUBLIC_KEY', publicKeyPem);

  const keyData = publicKeyPem
    .replace('-----BEGIN PUBLIC KEY-----', '')
    .replace('-----END PUBLIC KEY-----', '')
    .replace(/\n/g, '');

  console.log('🔍 Public key SHA256', crypto.subtle.digest('SHA-256', Uint8Array.from(atob(keyData), c => c.charCodeAt(0))));

  const binary = Uint8Array.from(atob(keyData), c => c.charCodeAt(0));

  return crypto.subtle.importKey(
    'spki',
    binary.buffer,
    {
      name: 'RSA-OAEP',
      hash: { name: 'SHA-1' } // ✅ 必须加
    },
    false,
    ['encrypt']
  );

}


async function signPayload(payload) {
  const ts = Math.floor(Date.now() / 1000);
  const data = new TextEncoder().encode(JSON.stringify({ ts, payload }));

  const key = await importPublicKey();

  console.log('🔐 Using HASH', key.algorithm);

  const encrypted = await crypto.subtle.encrypt({ name: 'RSA-OAEP' }, key, data);

  return btoa(String.fromCharCode(...new Uint8Array(encrypted)));
}

export async function apiRequest(method, url, body = {}) {
  const token = await signPayload(body);
  return axios({
    method,
    url: `${baseURL}/${url}`,
    data: body,
    headers: {
      'X-Signature': token,
      'Content-Type': 'application/json'
    }
  });
}
