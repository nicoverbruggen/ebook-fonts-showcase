#!/usr/bin/env bash
set -euo pipefail

PORT="${PORT:-8888}"
URL="http://localhost:${PORT}"
OUT="public/assets/favicon/og-image.png"
FONTS_DIR="public/repos/ebook-fonts"

cleanup() {
    kill "$SERVER_PID" 2>/dev/null || true
}
trap cleanup EXIT

if [ ! -d "$FONTS_DIR" ]; then
    echo "Cloning fonts..."
    git clone --branch trunk --depth 1 https://github.com/nicoverbruggen/ebook-fonts.git "$FONTS_DIR"
fi

echo "Starting server on ${URL}..."
php -S "0.0.0.0:${PORT}" -t public &
SERVER_PID=$!
sleep 1

echo "Taking screenshot at 2400x1340..."
npx --yes playwright screenshot \
    --viewport-size="2400,1340" \
    --wait-for-timeout=2000 \
    "$URL" "$OUT"

echo "Removing device shadows..."
node -e "
const { chromium } = require('playwright');
(async () => {
    const browser = await chromium.launch();
    const page = await browser.newPage({ viewport: { width: 2400, height: 1340 } });
    await page.goto('$URL');
    await page.waitForTimeout(2000);
    await page.addStyleTag({ content: '.reader, .reader-screen { box-shadow: none !important; }' });
    await page.screenshot({ path: '$OUT' });
    await browser.close();
})();
"

echo "Resizing and shifting content up..."
magick "$OUT" -strip -resize 1200x670 -gravity south -crop 1200x630+0+0 +repage "$OUT"

echo "Adding CTA text..."
magick "$OUT" \
    -gravity south \
    -fill "#6e351b" -draw "rectangle 0,520 1200,630" \
    -fill white -font "$FONTS_DIR/fonts/core/Readerly-BoldItalic.ttf" -pointsize 56 \
    -gravity south -annotate +0+30 "Explore fonts for digital reading" \
    "$OUT"

echo "Optimizing with pngquant..."
pngquant --quality=65-80 --strip --force --output "$OUT" "$OUT"

echo "Done: $OUT ($(ls -lh "$OUT" | awk '{print $5}'))"
