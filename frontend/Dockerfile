# frontend/Dockerfile
FROM node:20-alpine

WORKDIR /app

COPY . .

RUN yarn install && yarn build

EXPOSE 4321

CMD ["yarn", "dev", "--host"]

