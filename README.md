## 项目结构：Smart CV Resume Generator

- 项目结构清晰
- 支持前后端分离
- 集成 Docker 一键启动

---

## 🚀 快速启动

### 1️⃣ 前端开发（Astro）
```bash
cd frontend
yarn install
yarn dev
# 默认访问：http://localhost:4321
```

### 2️⃣ 后端开发（Lumen）
```bash
cd backend
composer install
cp .env.example .env
php artisan serve
# 默认访问：http://localhost:8000
# 默认访问：http://localhost:4321
```

### 3️⃣ Docker 一键启动（开发环境）
```bash
docker-compose up --build
```

| 服务       | 描述              | 端口        |
| -------- | --------------- | --------- |
| frontend | Astro 项目        | 4321      |
| backend  | PHP-FPM + Lumen | 9000 (内部) |
| nginx    | Web网关（可选）       | 80 / 8080 |
| db       | MariaDB         | 3306      |

### 数据库
```bash
volumes:
  mariadb:
    driver: local
```

```bash
DB_HOST=db
DB_PORT=3306
DB_DATABASE=smartcv
DB_USERNAME=root
DB_PASSWORD=rootpassword
```
### 目录结构
```bash
docker/
├── nginx/          ← 自定义 nginx 配置（如你要做接口反向代理）
├── php/            ← Dockerfile + php.ini（可安装扩展）
├── mariadb/        ← DB 初始 SQL 脚本（建库建表）
```
### TODO
- 接入 OpenAI GPT 接口（文本转简历语句）
- Lumen 后端处理 AI 调用 + 用户输入
- Astro 表单界面交互优化
- 域名 / ICP / 微信打赏二维码集成
