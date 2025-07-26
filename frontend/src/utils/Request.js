import axios from 'axios';
import crypto from 'crypto';

const baseURL = import.meta.env.PUBLIC_API_BASE;
const publicKey = import.meta.env.PUBLIC_API_PUBLIC_KEY.replace(/\\n/g, '\n');

function signPayload(payload) {
  const ts = Math.floor(Date.now() / 1000);
  const data = JSON.stringify({ts, payload});
  const encrypted = crypto.publicEncrypt(publicKey, Buffer.from(data));
  return {token: encrypted.toString('base64')};
}

export async function apiRequest(method, url, body = {}) {
  const {token} = signPayload(body);

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
