FROM alpine:latest AS fonts

RUN apk add --no-cache git python3 \
    && git clone --branch trunk --depth 1 \
        https://github.com/nicoverbruggen/ebook-fonts.git \
        /ebook-fonts \
    && python3 /ebook-fonts/tools/download_fonts.py \
    && rm -rf /ebook-fonts/.git

FROM php:8.5-fpm-alpine

RUN apk add --no-cache nginx

WORKDIR /app

COPY . .
COPY --from=fonts /ebook-fonts ./public/repos/ebook-fonts

RUN chmod +x /app/bin/start.sh

EXPOSE 8080

CMD ["sh", "bin/start.sh"]
