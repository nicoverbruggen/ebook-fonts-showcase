# Showcase for ebook-fonts

**This repository is just to make the source code available.** You can visit the interactive showcase here: [ebook-fonts.nicoverbruggen.be](https://ebook-fonts.nicoverbruggen.be).

A website to showcase my [font collection](https://github.com/nicoverbruggen/ebook-fonts). Browse and preview fonts with adjustable size, line height, dark mode, and bezel color.

## Project structure

```
public/          Web root (served to browsers)
  assets/        CSS, JS, SVGs, favicons
src/             PHP includes (not web-accessible)
  load_fonts.php Scans the font repo and builds the font list
```

## Local development

```bash
./serve-locally.sh
```

Starts a PHP dev server on `http://localhost:8888`. Set `PORT` to use a different port.

## Deployment

Create an application in Coolify and choose **Dockerfile** as the build pack. The included image uses PHP 8.5, PHP-FPM, and Nginx, and exposes port `8080`; no custom build or start command is necessary.

The latest `trunk` version of [ebook-fonts](https://github.com/nicoverbruggen/ebook-fonts) is cloned into the image during each build. The fonts are included in that repository, so no separate font download is needed. Production requests are served by Nginx and PHP-FPM rather than PHP's development server.

## OG image

The Open Graph image (`public/assets/favicon/og-image.png`) is a 1200x630 PNG screenshot of the app. To regenerate it:

```bash
./generate-og-image.sh
```

This starts the PHP server, takes a screenshot at 2x (2400x1260) via Playwright, then resizes and optimizes with `magick` and `pngquant`. If the fonts repo isn't cloned yet, it will be cloned automatically.

Requires `imagemagick`, `pngquant` (`brew install pngquant`), and Playwright (`npx playwright install chromium`).
