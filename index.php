<?php
$fontRoot = __DIR__ . '/ebook-fonts/fonts';
$fontEntries = [];
$fontFamilies = [];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($fontRoot, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $fileInfo) {
    if (!$fileInfo->isFile()) {
        continue;
    }
    if (strtolower($fileInfo->getExtension()) !== 'ttf') {
        continue;
    }
    $relative = str_replace(__DIR__ . '/', '', $fileInfo->getPathname());
    $relative = str_replace(DIRECTORY_SEPARATOR, '/', $relative);

    $fileName = pathinfo($relative, PATHINFO_FILENAME);
    $parts = explode('-', $fileName);
    $rawFamily = count($parts) > 1 ? implode('-', array_slice($parts, 0, -1)) : $fileName;
    $family = str_replace('_', ' ', $rawFamily);
    if ($family === 'NV OpenDyslexic') {
        continue;
    }
    $fontFamilies[$family][] = $relative;
    $fontEntries[] = $relative;
}

sort($fontEntries, SORT_NATURAL | SORT_FLAG_CASE);
$fontFilesJson = json_encode($fontEntries, JSON_UNESCAPED_SLASHES);
if ($fontFilesJson === false) {
    $fontFilesJson = '[]';
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Preview and compare curated eBook fonts in a device-style reading view." />
    <meta name="theme-color" content="#f6e9de" />
    <meta property="og:title" content="eBook Fonts Showcase" />
    <meta property="og:description" content="Preview and compare curated eBook fonts in a device-style reading view." />
    <meta property="og:type" content="website" />
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='14' fill='%23a04c24'/%3E%3Ctext x='50%25' y='56%25' font-size='34' text-anchor='middle' fill='white' font-family='Georgia,serif'%3EAa%3C/text%3E%3C/svg%3E" />
    <title>eBook Fonts Showcase</title>
    <style>
      :root {
        color-scheme: light;
        --ink: #171717;
        --muted: #5b5b5b;
        --accent: #a04c24;
        --accent-soft: #f2d2c2;
        --cta: #a04c24;
        --cta-hover: #7f3b1b;
        --surface: #fffdf8;
        --panel: #ffffff;
        --border: #e6ded6;
        --shadow: 0 18px 40px rgba(29, 18, 10, 0.12);
        --screen-width: 632px;
        --screen-height: 840px;
        --screen-bg: #f4efe6;
        --screen-bg-dark: #1d1a17;
      }

      * {
        box-sizing: border-box;
      }

      body {
        margin: 0;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", system-ui, sans-serif;
        color: var(--ink);
        background: radial-gradient(circle at 15% 20%, #f6e9de 0%, #fff8f0 45%, #f6efe9 100%);
        min-height: 100vh;
      }

      .page {
        max-width: 1380px;
        margin: 0 auto;
        padding: 28px 16px 40px;
      }

      header {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 18px;
      }

      h1 {
        font-size: clamp(2rem, 3.4vw, 3rem);
        margin: 0;
        letter-spacing: -0.02em;
      }

      header p {
        margin: 0;
        color: var(--muted);
        font-size: 1.05rem;
        max-width: 640px;
      }

      .layout {
        display: grid;
        grid-template-columns: minmax(320px, 420px) minmax(0, 1fr);
        gap: 28px;
        align-items: center;
      }

      .panel {
        background: var(--panel);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 18px;
        box-shadow: var(--shadow);
      }

      label {
        font-size: 1rem;
        color: #3e3530;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        display: block;
        margin-bottom: 6px;
        padding-bottom: 6px;
        font-weight: 600;
      }

      input[type="range"] {
        width: 100%;
        font: inherit;
        border-radius: 12px;
        border: 1px solid var(--border);
        padding: 6px 10px;
        background: var(--surface);
        color: var(--ink);
        accent-color: var(--accent);
      }

      select {
        width: 100%;
        font: inherit;
        border-radius: 12px;
        border: 1px solid var(--border);
        padding: 10px 12px;
        background: var(--surface);
        color: var(--ink);
      }

      .sidebar {
        display: flex;
        flex-direction: column;
        gap: 12px;
        align-self: center;
      }

      .sidebar-controls {
        display: flex;
        flex-direction: column;
        gap: 10px;
        border-top: 1px solid var(--border);
        padding-top: 16px;
      }

      .sidebar-controls > div {
        padding-top: 0;
      }

      .sidebar-controls .control-group {
        border-top: 1px solid var(--border);
        padding-top: 14px;
        margin-top: 2px;
      }

      .sidebar-actions-group {
        border-top: 1px solid var(--border);
        padding-top: 20px;
        margin-top: 8px;
      }

      .toggle-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
      }

      .toggle-label {
        font-size: 0.9rem;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 600;
      }

      .toggle-input {
        position: absolute;
        opacity: 0;
        width: 1px;
        height: 1px;
        appearance: none;
      }

      .toggle-track {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        width: 170px;
        padding: 9px 6px;
        border-radius: 999px;
        border: 1px solid var(--border);
        background: var(--surface);
        cursor: pointer;
      }

      .toggle-option {
        flex: 1;
        text-align: center;
        font-size: 0.7rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--muted);
        position: relative;
        z-index: 2;
        user-select: none;
      }

      .toggle-thumb {
        position: absolute;
        top: 6px;
        bottom: 6px;
        left: 6px;
        width: calc(50% - 8px);
        border-radius: 999px;
        background: var(--accent);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        transition: transform 0.2s ease;
      }

      .toggle-input:not(:checked) + .toggle-track .toggle-option--left {
        color: #fff;
      }

      .toggle-input:checked + .toggle-track .toggle-option--right {
        color: #fff;
      }

      .toggle-input:checked + .toggle-track .toggle-thumb {
        transform: translateX(100%);
      }


      .badge {
        padding: 6px 10px;
        border-radius: 999px;
        background: var(--accent-soft);
        color: var(--accent);
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
      }

      .device-footer {
        margin-top: 18px;
        text-align: center;
        color: #8a7f74;
        font-size: 0.95rem;
      }

      .device-footer a {
        color: var(--accent);
        text-decoration: none;
        font-weight: 600;
      }

      .device-footer a:hover {
        color: var(--cta-hover);
      }

      .sidebar-actions {
        display: flex;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
        padding-top: 16px;
      }

      .button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 18px;
        border-radius: 999px;
        border: 1px solid var(--border);
        background: #fff;
        color: var(--ink);
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        text-decoration: none;
        gap: 8px;
      }

      .button svg {
        width: 16px;
        height: 16px;
      }

      .button--primary {
        border-color: var(--cta);
        background: var(--cta);
        color: #fff;
      }

      .button--primary:hover {
        background: var(--cta-hover);
        border-color: var(--cta-hover);
      }

      .preview {
        display: flex;
        flex-direction: column;
        gap: 18px;
        padding: 0;
      }

      @media (min-width: 1051px) {
        .preview {
          min-height: calc(100vh - 68px);
          justify-content: center;
        }
      }

      .reader {
        align-self: center;
        width: min(100%, calc(var(--screen-width) + 56px));
        background: #ffffff;
        border-radius: 24px;
        padding: 36px 30px 54px;
        box-shadow: 0 22px 50px rgba(15, 10, 8, 0.28);
        border: 2px solid #e7e1da;
        position: relative;
        max-height: 100vh;
      }

      .reader.is-bezel-dark {
        background: #0f0f0f;
        border-color: #111;
        box-shadow: 0 22px 50px rgba(15, 10, 8, 0.32);
      }

      .reader-screen {
        background: var(--screen-bg);
        border-radius: 0;
        padding: 22px;
        width: 100%;
        min-height: 520px;
        height: min(var(--screen-height), calc(100vh - 140px));
        box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.08);
        display: flex;
        flex-direction: column;
        gap: 18px;
        overflow: hidden;
      }

      .reader.is-dark .reader-screen {
        background: var(--screen-bg-dark);
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.08);
      }

      .reader.is-dark .reader-toolbar,
      .reader.is-dark .reader-footer {
        color: #b8ada3;
      }

      .reader.is-dark .sample {
        color: #f3ede7;
      }

      .reader.is-dark .chapter-title {
        color: #d2c4b6;
      }

      .reader-toolbar {
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 0.85rem;
        color: #6b6158;
        text-transform: uppercase;
        letter-spacing: 0.12em;
      }

      .reader-meta {
        font-weight: 600;
        letter-spacing: 0.03em;
        display: flex;
        align-items: center;
        gap: 10px;
      }

      .reader-dot {
        width: 4px;
        height: 4px;
        border-radius: 999px;
        background: currentColor;
        opacity: 0.7;
      }

      .sample {
        padding: 8px 6px 0;
        min-height: 220px;
        line-height: 1.45;
        color: #1f1a16;
        flex: 1;
        overflow: hidden;
        word-break: break-word;
        overflow-wrap: anywhere;
        position: relative;
      }

      .sample::after {
        content: "";
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 56px;
        background: linear-gradient(to bottom, rgba(244, 239, 230, 0), rgba(244, 239, 230, 1));
        pointer-events: none;
      }

      .reader.is-dark .sample::after {
        background: linear-gradient(to bottom, rgba(29, 26, 23, 0), rgba(29, 26, 23, 1));
      }

      .chapter-title {
        margin: 0 0 2em;
        padding-top: 0.5em;
        text-align: center;
        font-size: 1.4em;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: #3a332c;
      }

      .sample p {
        margin: 0;
        text-indent: 1.5em;
      }

      .sample p:first-child {
        text-indent: 0;
      }

      .reader-footer {
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 0.8rem;
        color: #8a7f74;
        padding-top: 4px;
        margin-top: auto;
        text-transform: uppercase;
        letter-spacing: 0.12em;
      }

      .font-list {
        display: grid;
        grid-template-columns: repeat(2, minmax(120px, 1fr));
        gap: 8px;
        max-height: calc(var(--screen-height) - 200px);
        overflow: auto;
        padding-right: 6px;
      }

      .desktop-only {
        display: block;
      }

      .mobile-only {
        display: none;
      }

      .font-sections {
        display: flex;
        flex-direction: column;
        gap: 12px;
      }

      .quick-title {
        font-size: 1rem;
        color: #3e3530;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 8px;
        padding-bottom: 6px;
        font-weight: 600;
      }

      summary.quick-title {
        cursor: pointer;
        list-style: none;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 12px;
        margin-bottom: 0;
        padding-bottom: 0;
      }

      summary.quick-title::-webkit-details-marker {
        display: none;
      }

      details .quick-title::after {
        content: "Show";
        font-size: 0.7rem;
        letter-spacing: 0.12em;
        color: var(--muted);
        text-transform: uppercase;
        margin-left: auto;
      }

      details .quick-title::before {
        content: "";
        width: 0;
        height: 0;
        border-top: 6px solid transparent;
        border-bottom: 6px solid transparent;
        border-left: 8px solid var(--muted);
        transition: transform 0.2s ease;
      }

      details[open] .quick-title::after {
        content: "Hide";
      }

      details[open] .quick-title::before {
        transform: rotate(90deg);
      }

      .quick-group + .quick-group {
        border-top: 1px solid var(--border);
        padding-top: 14px;
        margin-top: 10px;
      }

      #extraFonts {
        padding: 20px 0 10px;
      }

      .quick-group > .font-list {
        margin-top: 10px;
      }

      .font-card {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        border-radius: 14px;
        border: 1px solid var(--border);
        padding: 4px 8px;
        background: #fff;
        cursor: pointer;
      }

      .font-card:hover {
        border-color: var(--accent);
      }

      .font-card.is-active {
        border-color: var(--accent);
        background: var(--accent);
        color: #fff;
        box-shadow: inset 0 0 0 1px var(--accent);
      }

      .font-card p {
        margin: 0;
        font-size: 1.02rem;
        line-height: 1;
      }

      .font-card.is-ninepoint p {
        transform: translateY(1px);
      }

      .font-card.is-charis p {
        transform: translateY(-1px);
      }

      @media (max-width: 1050px) {
        :root {
          --screen-width: 480px;
          --screen-height: 640px;
        }

        .layout {
          grid-template-columns: 1fr;
          align-items: start;
        }

        .reader {
          width: min(100%, 520px);
          max-height: 100vh;
        }

        .reader-screen {
          height: min(var(--screen-height), calc(100vh - 120px));
          min-height: 420px;
        }

        .font-list {
          grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
          max-height: none;
          overflow: visible;
          padding-right: 0;
        }

        .desktop-only {
          display: none;
        }

        .mobile-only {
          display: block;
        }
      }

      @media (max-width: 640px) {
        :root {
          --screen-width: 320px;
          --screen-height: 500px;
        }

        .page {
          padding-top: 20px;
        }

        .reader {
          width: min(100%, 360px);
          padding: 20px 16px 28px;
          border-radius: 14px;
          max-height: 100vh;
        }

        .reader-screen {
          padding: 16px;
          min-height: 500px;
          height: min(var(--screen-height), calc(100vh - 120px));
        }
      }
    </style>
  </head>
  <body>
    <div class="page">
      <div class="layout">
        <section class="panel sidebar">
          <div class="mobile-only">
            <label for="fontSelect">Font</label>
            <select id="fontSelect"></select>
          </div>
          <div class="desktop-only">
            <div class="font-sections">
              <div class="quick-group">
                <div class="quick-title">Core Collection</div>
                <div class="font-list" id="fontListCore"></div>
              </div>
              <details class="quick-group" id="extraFonts">
                <summary class="quick-title">Extra Collection</summary>
                <div class="font-list" id="fontListExtra"></div>
              </details>
            </div>
          </div>
          <div class="sidebar-controls">
            <div>
              <label for="sizeRange">Preview size</label>
              <input id="sizeRange" type="range" min="14" max="42" value="20" />
            </div>
            <div class="control-group">
              <div class="toggle-row">
                <span class="toggle-label">Screen</span>
                <div class="toggle-group">
                  <input id="darkModeToggle" class="toggle-input" type="checkbox" />
                  <label for="darkModeToggle" class="toggle-track" aria-label="Screen mode">
                    <span class="toggle-option toggle-option--left">Light</span>
                    <span class="toggle-option toggle-option--right">Dark</span>
                    <span class="toggle-thumb"></span>
                  </label>
                </div>
              </div>
            </div>
            <div class="control-group">
              <div class="toggle-row">
                <span class="toggle-label">Bezel</span>
                <div class="toggle-group">
                  <input id="bezelToggle" class="toggle-input" type="checkbox" />
                  <label for="bezelToggle" class="toggle-track" aria-label="Bezel color">
                    <span class="toggle-option toggle-option--left">White</span>
                    <span class="toggle-option toggle-option--right">Black</span>
                    <span class="toggle-thumb"></span>
                  </label>
                </div>
              </div>
            </div>
            <div class="sidebar-actions-group">
              <div class="sidebar-actions">
                <a class="button button--primary" href="https://github.com/nicoverbruggen/ebook-fonts/releases" target="_blank" rel="noreferrer">
                  <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path fill="currentColor" d="M12 3a1 1 0 0 1 1 1v9.17l2.59-2.58a1 1 0 1 1 1.41 1.42l-4.3 4.29a1 1 0 0 1-1.4 0l-4.3-4.29a1 1 0 1 1 1.41-1.42L11 13.17V4a1 1 0 0 1 1-1zm-6 15a1 1 0 0 1 1 1v1h10v-1a1 1 0 1 1 2 0v2a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-2a1 1 0 0 1 1-1z" />
                  </svg>
                  Download
                </a>
                <a class="button" href="https://github.com/nicoverbruggen/ebook-fonts" target="_blank" rel="noreferrer">
                  <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path fill="currentColor" d="M12 2C6.48 2 2 6.58 2 12.24c0 4.52 2.87 8.35 6.84 9.71.5.1.68-.22.68-.48 0-.24-.01-.86-.01-1.69-2.78.62-3.37-1.37-3.37-1.37-.45-1.18-1.11-1.49-1.11-1.49-.91-.63.07-.62.07-.62 1 .07 1.53 1.06 1.53 1.06.9 1.57 2.36 1.12 2.94.86.09-.67.35-1.12.63-1.38-2.22-.26-4.56-1.14-4.56-5.06 0-1.12.39-2.03 1.03-2.75-.1-.26-.45-1.32.1-2.75 0 0 .84-.27 2.75 1.05A9.3 9.3 0 0 1 12 7.07c.83 0 1.67.11 2.45.33 1.91-1.32 2.75-1.05 2.75-1.05.55 1.43.2 2.49.1 2.75.64.72 1.03 1.63 1.03 2.75 0 3.93-2.34 4.8-4.58 5.05.36.32.68.95.68 1.92 0 1.38-.01 2.5-.01 2.84 0 .26.18.59.69.48A10.24 10.24 0 0 0 22 12.24C22 6.58 17.52 2 12 2z" />
                  </svg>
                  GitHub
                </a>
              </div>
            </div>
          </div>
        </section>

        <section class="preview">
          <div class="reader">
            <div class="reader-screen">
              <div class="reader-toolbar">
                <span class="reader-meta">
                  <span id="readerChapter">Chapter 1</span>
                  <span class="reader-dot" aria-hidden="true"></span>
                  <span id="readerChapterProgress">1 OF 17</span>
                </span>
              </div>
              <div class="sample" id="sampleArea"></div>
              <div class="reader-footer">
                <span class="reader-meta">
                  <span id="readerBook">Pride and Prejudice</span>
                  <span class="reader-dot" aria-hidden="true"></span>
                  <span id="readerBookProgress">1 OF 256</span>
                </span>
              </div>
            </div>
          </div>
          <div class="device-footer">Made with &hearts; for digital reading by <a href="https://nicoverbruggen.be" target="_blank" rel="noreferrer">Nico Verbruggen</a>.</div>
        </section>
      </div>
    </div>

    <script>
      const fontFiles = <?php echo $fontFilesJson; ?>;
      const sizeRange = document.getElementById("sizeRange");
      const sampleArea = document.getElementById("sampleArea");
      const fontSelect = document.getElementById("fontSelect");
      const fontListCore = document.getElementById("fontListCore");
      const fontListExtra = document.getElementById("fontListExtra");
      const extraFonts = document.getElementById("extraFonts");
      const darkModeToggle = document.getElementById("darkModeToggle");
      const bezelToggle = document.getElementById("bezelToggle");
      const reader = document.querySelector(".reader");
      const readerChapter = document.getElementById("readerChapter");
      const readerChapterProgress = document.getElementById("readerChapterProgress");
      const readerBook = document.getElementById("readerBook");
      const readerBookProgress = document.getElementById("readerBookProgress");

      const fonts = new Map();
      const sampleText = `Chapter 1

It is a truth universally acknowledged, that a single man in possession of a good fortune, must be in want of a wife.

However little known the feelings or views of such a man may be on his first entering a neighbourhood, this truth is so well fixed in the minds of the surrounding families, that he is considered the rightful property of some one or other of their daughters.

"My dear Mr. Bennet," said his lady to him one day, "have you heard that Netherfield Park is let at last?"

Mr. Bennet replied that he had not.

"But it is," returned she; "for Mrs. Long has just been here, and she told me all about it."

Mr. Bennet made no answer.

"Do you not want to know who has taken it?" cried his wife impatiently.

"You want to tell me, and I have no objection to hearing it."

This was invitation enough.

"Why, my dear, you must know, Mrs. Long says that Netherfield is taken by a young man of large fortune from the north of England; that he came down on Monday in a chaise and four to see the place, and was so much delighted with it that he agreed with Mr. Morris immediately; that he is to take possession before Michaelmas, and some of his servants are to be in the house by the end of next week."

"What is his name?"

"Bingley."

"Is he married or single?"

"Oh! Single, my dear, to be sure! A single man of large fortune; four or five thousand a year. What a fine thing for our girls!"

"How so? how can it affect them?"

"My dear Mr. Bennet," replied his wife, "how can you be so tiresome! You must know that I am thinking of his marrying one of them."

"Is that his design in settling here?"

"Design! nonsense, how can you talk so! But it is very likely that he may fall in love with one of them, and therefore you must visit him as soon as he comes."

"I see no occasion for that. You and the girls may go, or you may send them by themselves, which perhaps will be still better, for as you are as handsome as any of them, Mr. Bingley may like you the best of the party."

"My dear, you flatter me. I certainly have had my share of beauty, but I do not pretend to be anything extraordinary now."`;

      function parseFont(file) {
        const fileName = file.split("/").pop().replace(".ttf", "");
        const parts = fileName.split("-");
        const rawFamily = parts.slice(0, -1).join("-") || fileName;
        const rawStyle = parts.length > 1 ? parts[parts.length - 1] : "Regular";
        const family = rawFamily.replace(/_/g, " ");
        const styleToken = rawStyle || "Regular";
        const isBold = styleToken.includes("Bold");
        const isItalic = styleToken.includes("Italic");
        const weight = isBold ? 700 : 400;
        const style = isItalic ? "italic" : "normal";
        const styleKey = `${isBold ? "bold" : "regular"}${isItalic ? "italic" : ""}`;

        return {
          file,
          family,
          weight,
          style,
          styleKey,
          collection: file.includes("/core/") ? "Core" : "Extra"
        };
      }

      function buildFontFaces() {
        const styleEl = document.createElement("style");
        const rules = [];

        fontFiles.forEach((file) => {
          const info = parseFont(file);
          if (!fonts.has(info.family)) {
            fonts.set(info.family, {
              family: info.family,
              collection: info.collection,
              files: []
            });
          }
          fonts.get(info.family).files.push(info);

          rules.push(`@font-face {\n  font-family: "${info.family}";\n  src: url("./${info.file}") format("truetype");\n  font-weight: ${info.weight};\n  font-style: ${info.style};\n  font-display: swap;\n}`);
        });

        styleEl.textContent = rules.join("\n");
        document.head.appendChild(styleEl);
      }

      function updateFontList(filter) {
        fontListCore.innerHTML = "";
        fontListExtra.innerHTML = "";
        fontSelect.innerHTML = "";
        const preferredOrder = [
          "NV NinePoint",
          "NV Charis",
          "NV Garamond",
          "NV Jost"
        ];
        const items = [...fonts.values()]
          .filter((font) => font.family.toLowerCase().includes(filter.toLowerCase()))
          .sort((a, b) => {
            const aIndex = preferredOrder.indexOf(a.family);
            const bIndex = preferredOrder.indexOf(b.family);
            if (aIndex !== -1 || bIndex !== -1) {
              if (aIndex === -1) return 1;
              if (bIndex === -1) return -1;
              return aIndex - bIndex;
            }
            return a.family.localeCompare(b.family);
          });

        const coreGroup = document.createElement("optgroup");
        coreGroup.label = "Core Collection";
        const extraGroup = document.createElement("optgroup");
        extraGroup.label = "Extra Collection";

        items.forEach((font) => {
          const card = document.createElement("button");
          card.type = "button";
          card.className = "font-card";
          if (font.family === "NV NinePoint") {
            card.classList.add("is-ninepoint");
          }
          if (font.family === "NV Charis") {
            card.classList.add("is-charis");
          }
          card.style.fontFamily = `"${font.family}", serif`;
          card.innerHTML = `<p>${font.family}</p>`;
          card.addEventListener("click", () => {
            setActiveFont(font.family);
          });
          if (font.collection === "Core") {
            fontListCore.appendChild(card);
          } else {
            fontListExtra.appendChild(card);
          }

          const option = document.createElement("option");
          option.value = font.family;
          option.textContent = font.family;
          if (font.collection === "Core") {
            coreGroup.appendChild(option);
          } else {
            extraGroup.appendChild(option);
          }
        });

        fontSelect.appendChild(coreGroup);
        fontSelect.appendChild(extraGroup);
      }

      function setActiveFont(family) {
        activeFamily = family;
        renderPreview();
        const cards = document.querySelectorAll(".font-card");
        cards.forEach((card) => {
          card.classList.toggle("is-active", card.textContent.trim() === family);
        });
        if (fontSelect.value !== family) {
          fontSelect.value = family;
        }
      }

      function renderPreview() {
        const family = activeFamily;
        const info = fonts.get(family);

        sampleArea.style.fontFamily = `"${family}", serif`;
        sampleArea.style.fontSize = `${sizeRange.value}px`;
        sampleArea.style.fontWeight = "400";
        sampleArea.style.fontStyle = "normal";
        const paragraphs = sampleText
          .split(/\n\s*\n/)
          .map((paragraph) => paragraph.trim())
          .filter(Boolean);

        const rendered = paragraphs
          .map((paragraph, index) => {
            const safeText = paragraph.replace(/\n/g, " ");
            if (index === 0 && /^chapter\s+\d+/i.test(safeText)) {
              return `<h3 class="chapter-title">${safeText}</h3>`;
            }
            return `<p>${safeText}</p>`;
          })
          .join("");

        sampleArea.innerHTML = rendered;


        readerChapter.textContent = "Chapter 1";
        readerChapterProgress.textContent = "1 OF 17";
        readerBook.textContent = "Pride and Prejudice";
        readerBookProgress.textContent = "1 OF 256";
      }

      function setupInteractions() {
        sizeRange.addEventListener("input", renderPreview);
        fontSelect.addEventListener("change", (event) => {
          setActiveFont(event.target.value);
        });
        darkModeToggle.addEventListener("change", (event) => {
          reader.classList.toggle("is-dark", event.target.checked);
        });
        bezelToggle.addEventListener("change", (event) => {
          reader.classList.toggle("is-bezel-dark", event.target.checked);
        });
        window.addEventListener("resize", renderPreview);
      }

      function syncToggles() {
        darkModeToggle.checked = reader.classList.contains("is-dark");
        bezelToggle.checked = reader.classList.contains("is-bezel-dark");
      }

      let activeFamily = "NV NinePoint";
      buildFontFaces();
      updateFontList("");
      setupInteractions();
      if (window.matchMedia("(min-width: 1051px)").matches) {
        sizeRange.value = "22";
      }
      syncToggles();
      if (extraFonts) {
        extraFonts.open = window.matchMedia("(max-width: 1050px)").matches;
      }
      setActiveFont(activeFamily);
      if (fontSelect.value !== activeFamily) {
        fontSelect.value = activeFamily;
      }
    </script>
  </body>
</html>
