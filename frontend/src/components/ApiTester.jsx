import {useState} from 'react';
import {apiRequest} from '../utils/Request.js';

export default function ApiTester() {
  const [text, setText] = useState('');
  const [result, setResult] = useState('');

  async function send(e) {
    e.preventDefault(); // ✅ 阻止默认 form 提交，防止 4321 POST
    try {
      const res = await apiRequest('post', 'v1/CV/new', {text});
      setResult(JSON.stringify(res.data, null, 2));
    } catch (err) {
      setResult('请求失败: ' + err.message);
    }
  }

  return (
    <form onSubmit={send}>
      <input value={text} onChange={(e) => setText(e.target.value)} placeholder="输入简历文本"/>
      <button type="submit">发送</button>
      <pre>{result}</pre>
    </form>
  );
}
