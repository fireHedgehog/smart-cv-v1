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
├── docker/         # 存放 Dockerfile 和 nginx 配置等
│   ├── nginx/
│   ├── php/
│   └── mariadb/
├── docker-compose.yml
├── .env
└── README.md
```
### TODO
- 接入 OpenAI GPT 接口（文本转简历语句）
- Lumen 后端处理 AI 调用 + 用户输入
- Astro 表单界面交互优化
- 域名 / ICP / 微信打赏二维码集成

# 核心配置
### 目录 
```angular2html
smart-cv-v1/
├── frontend/       # Astro 项目
├── backend/        # Lumen 项目
├── docker/         # 存放 Dockerfile 和 nginx 配置等
│   ├── nginx/
│   ├── php/
│   └── mariadb/
├── docker-compose.yml
├── .env
└── README.md
```

### frontend/Dockerfile
```angular2html
# frontend/Dockerfile
FROM node:20-alpine

WORKDIR /app

COPY . .

RUN yarn install && yarn build

EXPOSE 4321

CMD ["yarn", "dev"]
```
### backend/Dockerfile
```angular2html
# backend/Dockerfile
FROM php:8.3-fpm

# 安装扩展和 Composer
RUN apt-get update && apt-get install -y \
libzip-dev zip unzip mariadb-client \
&& docker-php-ext-install pdo_mysql

# 安装 Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install

EXPOSE 9000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
```
### docker-compose.yml
```angular2html
version: '3.8'

services:
frontend:
build:
context: ./frontend
volumes:
- ./frontend:/app
ports:
- "4321:4321"
environment:
- NODE_ENV=development
depends_on:
- backend

backend:
build:
context: ./backend
volumes:
- ./backend:/var/www
ports:
- "8000:8000"
environment:
- DB_HOST=db
- DB_PORT=3306
- DB_DATABASE=smartcv
- DB_USERNAME=root
- DB_PASSWORD=rootpassword
depends_on:
- db

db:
image: mariadb
restart: always
environment:
MYSQL_ROOT_PASSWORD: rootpassword
MYSQL_DATABASE: smartcv
ports:
- "3306:3306"
volumes:
- mariadb_data:/var/lib/mysql

phpmyadmin:
image: phpmyadmin
restart: always
ports:
- "8081:80"
environment:
PMA_HOST: db
MYSQL_ROOT_PASSWORD: rootpassword

volumes:
mariadb_data:
```

### 🔧 第三步：配置 .env 文件（或 .env.docker）

```angular2html
APP_NAME=SmartCV
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=smartcv
DB_USERNAME=root
DB_PASSWORD=rootpassword
```

### 🚀 第四步：运行项目
```angular2html
docker-compose up --build
```

```angular2html
访问地址：

前端：http://localhost:4321

后端：http://localhost:8000

phpMyAdmin：http://localhost:8081
```

