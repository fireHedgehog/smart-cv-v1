# 构建阶段
FROM node:20-alpine AS builder

WORKDIR /app
COPY . .
RUN yarn install && yarn build

# 运行阶段
FROM nginx:alpine

# 拷贝 Astro build 后的静态文件到 nginx 默认路径
COPY --from=builder /app/dist /usr/share/nginx/html

# 可选：覆盖 nginx 配置（如果你需要 rewrite 或 SPA support）
# COPY nginx.conf /etc/nginx/nginx.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
