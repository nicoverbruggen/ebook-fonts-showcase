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
    <title>eBook Fonts Showcase</title>
    <style>
      :root {
        color-scheme: light;
        --ink: #171717;
        --muted: #5b5b5b;
        --accent: #a04c24;
        --accent-soft: #f2d2c2;
        --cta: #1e6bd6;
        --cta-hover: #1757af;
        --surface: #fffdf8;
        --panel: #ffffff;
        --border: #e6ded6;
        --shadow: 0 18px 40px rgba(29, 18, 10, 0.12);
        --screen-width: 632px;
        --screen-height: 840px;
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
        align-items: start;
      }

      .panel {
        background: var(--panel);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 18px;
        box-shadow: var(--shadow);
      }

      label {
        font-size: 0.9rem;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.08em;
        display: block;
        margin-bottom: 8px;
      }

      input[type="range"] {
        width: 100%;
        font: inherit;
        border-radius: 12px;
        border: 1px solid var(--border);
        padding: 10px 12px;
        background: var(--surface);
        color: var(--ink);
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
      }

      .sidebar-controls {
        display: flex;
        flex-direction: column;
        gap: 14px;
        border-top: 1px solid var(--border);
        padding-top: 16px;
      }

      .sidebar-controls > div {
        padding-top: 0;
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

      .sidebar-actions {
        display: flex;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
      }

      .button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 14px;
        border-radius: 999px;
        border: 1px solid var(--border);
        background: #fff;
        color: var(--ink);
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        text-decoration: none;
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

      .reader {
        align-self: center;
        width: min(100%, calc(var(--screen-width) + 56px));
        background: #0f0f0f;
        border-radius: 24px;
        padding: 36px 30px 54px;
        box-shadow: 0 22px 50px rgba(15, 10, 8, 0.28);
        border: 2px solid #111;
        position: relative;
      }

      .reader-screen {
        background: #f4efe6;
        border-radius: 0;
        padding: 22px;
        width: 100%;
        min-height: 520px;
        height: var(--screen-height);
        box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.08);
        display: flex;
        flex-direction: column;
        gap: 18px;
      }

      .reader-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
        color: #6b6158;
      }

      .reader-title {
        font-weight: 600;
        letter-spacing: 0.03em;
      }

      .sample {
        padding: 8px 6px 0;
        min-height: 220px;
        line-height: 1.45;
        color: #1f1a16;
        flex: 1;
        overflow: hidden;
      }

      .chapter-title {
        margin: 0 0 0.6em;
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
        justify-content: space-between;
        align-items: center;
        font-size: 0.8rem;
        color: #8a7f74;
        padding-top: 4px;
        margin-top: auto;
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

      .quick-group + .quick-group {
        border-top: 1px solid var(--border);
        padding-top: 18px;
        margin-top: 12px;
      }

      .font-card {
        border-radius: 14px;
        border: 1px solid var(--border);
        padding: 10px;
        background: #fff;
        cursor: pointer;
      }

      .font-card:hover {
        border-color: var(--accent);
      }

      .font-card.is-active {
        border-color: var(--accent);
        box-shadow: inset 0 0 0 1px var(--accent);
      }

      .font-card p {
        margin: 0;
        font-size: 0.95rem;
      }

      @media (max-width: 1250px) {
        :root {
          --screen-width: 520px;
          --screen-height: 692px;
        }
      }

      @media (max-width: 1050px) {
        .layout {
          grid-template-columns: 1fr;
        }

        .reader {
          width: min(100%, 520px);
        }

        .reader-screen {
          height: auto;
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
          --screen-height: 520px;
        }

        .page {
          padding-top: 20px;
        }

        .reader {
          width: min(100%, 360px);
          padding: 20px 16px 28px;
          border-radius: 14px;
        }

        .reader-screen {
          padding: 16px;
          min-height: 520px;
          height: 520px;
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
              <div class="quick-group">
                <div class="quick-title">Extra Collection</div>
                <div class="font-list" id="fontListExtra"></div>
              </div>
            </div>
          </div>
          <div class="sidebar-controls">
            <div>
              <label for="sizeRange">Preview size</label>
              <input id="sizeRange" type="range" min="14" max="42" value="20" />
            </div>
            <div>
              <div class="sidebar-actions">
                <a class="button button--primary" href="https://github.com/nicoverbruggen/ebook-fonts/releases" target="_blank" rel="noreferrer">Download</a>
                <a class="button" href="https://github.com/nicoverbruggen/ebook-fonts" target="_blank" rel="noreferrer">GitHub</a>
              </div>
            </div>
          </div>
        </section>

        <section class="preview">
          <div class="reader">
            <div class="reader-screen">
              <div class="reader-toolbar">
                <span class="reader-title">Pride and Prejudice</span>
                <span id="readerClock">9:00</span>
              </div>
              <div class="sample" id="sampleArea"></div>
              <div class="reader-footer">
                <span id="readerPage">Page 1</span>
                <span id="readerProgress">1%</span>
              </div>
            </div>
          </div>
          <div class="device-footer">This curated collection of fonts is available on GitHub.</div>
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
      const readerClock = document.getElementById("readerClock");
      const readerPage = document.getElementById("readerPage");
      const readerProgress = document.getElementById("readerProgress");

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
        const items = [...fonts.values()]
          .filter((font) => font.family.toLowerCase().includes(filter.toLowerCase()))
          .sort((a, b) => a.family.localeCompare(b.family));

        const coreGroup = document.createElement("optgroup");
        coreGroup.label = "Core Collection";
        const extraGroup = document.createElement("optgroup");
        extraGroup.label = "Extra Collection";

        items.forEach((font) => {
          const card = document.createElement("button");
          card.type = "button";
          card.className = "font-card";
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

        const now = new Date();
        readerClock.textContent = now.toLocaleTimeString([], {
          hour: "2-digit",
          minute: "2-digit"
        });
        readerPage.textContent = "Page 1";
        readerProgress.textContent = "1%";
      }

      function setupInteractions() {
        sizeRange.addEventListener("input", renderPreview);
        fontSelect.addEventListener("change", (event) => {
          setActiveFont(event.target.value);
        });
      }

      let activeFamily = "NV Charis";
      buildFontFaces();
      updateFontList("");
      setupInteractions();
      setActiveFont(activeFamily);
      if (fontSelect.value !== activeFamily) {
        fontSelect.value = activeFamily;
      }
      setInterval(() => {
        const now = new Date();
        readerClock.textContent = now.toLocaleTimeString([], {
          hour: "2-digit",
          minute: "2-digit"
        });
      }, 60000);
    </script>
  </body>
</html>
