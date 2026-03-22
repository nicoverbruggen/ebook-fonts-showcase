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

Fonts are cloned from [ebook-fonts](https://github.com/nicoverbruggen/ebook-fonts) during the build phase (see `nixpacks.toml`).

## OG image

The Open Graph image (`public/assets/favicon/og-image.png`) is a 1200x630 PNG screenshot of the app. To regenerate it:

```bash
./generate-og-image.sh
```

This starts the PHP server, takes a screenshot at 2x (2400x1260) via Playwright, then resizes and optimizes with `magick` and `pngquant`. If the fonts repo isn't cloned yet, it will be cloned automatically.

Requires `imagemagick`, `pngquant` (`brew install pngquant`), and Playwright (`npx playwright install chromium`).
