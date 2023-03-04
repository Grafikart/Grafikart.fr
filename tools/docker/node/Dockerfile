FROM node:16-alpine

RUN corepack enable
RUN corepack prepare pnpm@latest --activate

RUN mkdir -m 777 /.npm
RUN mkdir -m 777 /.yarn
RUN touch /.yarnrc && chmod 777 /.yarnrc
