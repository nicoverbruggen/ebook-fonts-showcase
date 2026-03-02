<?php
define('APP_ROOT', __DIR__);
require __DIR__ . '/web/load_fonts.php';
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
    <link rel="stylesheet" href="assets/styles.css">
  </head>
  <body>
    <div class="page">
      <div class="layout">
        <div class="sidebar-column">
        <section class="panel sidebar">
          <div class="mobile-only">
            <label for="fontSelect">Font</label>
            <select id="fontSelect"></select>
          </div>
          <div class="desktop-only">
            <div class="font-sections">
              <div class="quick-group">
                <div class="quick-title"><span class="quick-star">&#9733;</span> Core Collection</div>
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
              <label for="sizeRange">Font size <span class="value" id="sizeValue"></span></label>
              <input id="sizeRange" type="range" min="14" max="42" value="20" />
            </div>
            <div>
              <label for="lineHeightRange">Line height <span class="value" id="lineHeightValue"></span></label>
              <input id="lineHeightRange" type="range" min="1.0" max="2.2" step="0.05" value="1.45" />
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
                    <span class="toggle-option toggle-option--left">Black</span>
                    <span class="toggle-option toggle-option--right">White</span>
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
        <div class="sidebar-notice">
          <a href="https://openfontlicense.org" target="_blank" rel="noreferrer">
            <img src="assets/ofl.svg" alt="Open Font License" />
          </a>
          <p>The majority of the fonts included in the collection are licensed under the OFL. <a href="https://github.com/nicoverbruggen/ebook-fonts?tab=readme-ov-file#how-are-these-fonts-licensed" target="_blank" rel="noreferrer">Learn more</a>.</p>
        </div>
        </div>

        <section class="preview">
          <div class="reader is-bezel-dark">
            <div class="reader-screen">
              <div class="reader-toolbar">
                <span class="reader-meta">
                  <span>Prologue</span>
                  <span class="reader-dot" aria-hidden="true"></span>
                  <span>1 OF 58</span>
                </span>
              </div>
              <div class="sample" id="sampleArea"></div>
              <div class="reader-footer">
                <span class="reader-meta">
                  <span>Warbreaker</span>
                  <span class="reader-dot" aria-hidden="true"></span>
                  <span>1 OF 592</span>
                </span>
              </div>
            </div>
          </div>
          <div class="device-footer">
              <p>Made with &hearts; for digital reading by <a href="https://nicoverbruggen.be" target="_blank" rel="noreferrer">Nico Verbruggen</a>.</p>
              <p class="attribution">Preview text from <a href="https://www.brandonsanderson.com/blogs/blog/warbreaker-rights-explanation"><em>Warbreaker</em></a> by Brandon Sanderson, used under <a href="https://creativecommons.org/licenses/by-nc-nd/3.0/us/" target="_blank" rel="noreferrer">Creative Commons</a>.</p>
          </div>
        </section>
      </div>
    </div>

    <script>const fontFiles = <?php echo $fontFilesJson; ?>;</script>
    <script src="assets/app.js"></script>
  </body>
</html>
