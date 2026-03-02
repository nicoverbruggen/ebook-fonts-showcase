const sizeRange = document.getElementById("sizeRange");
const lineHeightRange = document.getElementById("lineHeightRange");
const sampleArea = document.getElementById("sampleArea");
const fontSelect = document.getElementById("fontSelect");
const fontListCore = document.getElementById("fontListCore");
const fontListExtra = document.getElementById("fontListExtra");
const extraFonts = document.getElementById("extraFonts");
const darkModeToggle = document.getElementById("darkModeToggle");
const bezelToggle = document.getElementById("bezelToggle");
const reader = document.querySelector(".reader");
const sizeValue = document.getElementById("sizeValue");
const lineHeightValue = document.getElementById("lineHeightValue");

const fonts = new Map();
const preferredOrder = [
  "Readerly",
  "Cartisse",
  "NV NinePoint",
  "NV Charis",
  "NV Garamond",
  "NV Jost"
];
let sampleHtml = "";
let activeFamily = "";

function parseFont(file) {
  const fileName = file.split("/").pop().replace(".ttf", "");
  const parts = fileName.split("-");
  const rawFamily = parts.slice(0, -1).join("-") || fileName;
  const rawStyle = parts.length > 1 ? parts[parts.length - 1] : "Regular";
  const family = rawFamily.replace(/_/g, " ");
  const styleToken = rawStyle || "Regular";
  const isBold = styleToken.includes("Bold");
  const isItalic = styleToken.includes("Italic");
  return {
    file,
    family,
    weight: isBold ? 700 : 400,
    style: isItalic ? "italic" : "normal",
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

    rules.push(
      `@font-face {\n` +
      `  font-family: "${info.family}";\n` +
      `  src: url("./${info.file}") format("truetype");\n` +
      `  font-weight: ${info.weight};\n` +
      `  font-style: ${info.style};\n` +
      `  font-display: swap;\n` +
      `}`
    );
  });

  styleEl.textContent = rules.join("\n");
  document.head.appendChild(styleEl);
}

function sortFonts() {
  return [...fonts.values()].sort((a, b) => {
    const aIndex = preferredOrder.indexOf(a.family);
    const bIndex = preferredOrder.indexOf(b.family);
    if (aIndex !== -1 || bIndex !== -1) {
      if (aIndex === -1) return 1;
      if (bIndex === -1) return -1;
      return aIndex - bIndex;
    }
    return a.family.localeCompare(b.family);
  });
}

function createFontCard(font) {
  const card = document.createElement("button");
  card.type = "button";
  card.className = "font-card";
  if (font.family === "NV NinePoint") card.classList.add("is-ninepoint");
  if (font.family === "NV Charis") card.classList.add("is-charis");
  card.style.fontFamily = `"${font.family}", serif`;
  card.innerHTML = `<p>${font.family}</p>`;
  card.addEventListener("click", () => setActiveFont(font.family));
  return card;
}

function createFontOption(font) {
  const option = document.createElement("option");
  option.value = font.family;
  option.textContent = font.family;
  return option;
}

function updateFontList() {
  fontListCore.innerHTML = "";
  fontListExtra.innerHTML = "";
  fontSelect.innerHTML = "";

  const coreGroup = document.createElement("optgroup");
  coreGroup.label = "Core Collection";
  const extraGroup = document.createElement("optgroup");
  extraGroup.label = "Extra Collection";

  sortFonts().forEach((font) => {
    const target = font.collection === "Core" ? fontListCore : fontListExtra;
    target.appendChild(createFontCard(font));

    const group = font.collection === "Core" ? coreGroup : extraGroup;
    group.appendChild(createFontOption(font));
  });

  fontSelect.appendChild(coreGroup);
  fontSelect.appendChild(extraGroup);

  const scrollHint = document.createElement("div");
  scrollHint.className = "scroll-hint";
  scrollHint.textContent = "Scroll to see more fonts";
  fontListExtra.appendChild(scrollHint);
}

function setActiveFont(family) {
  activeFamily = family;
  renderPreview();
  document.querySelectorAll(".font-card").forEach((card) => {
    card.classList.toggle("is-active", card.textContent.trim() === family);
  });
  if (fontSelect.value !== family) {
    fontSelect.value = family;
  }
}

function updateValueDisplays() {
  sizeValue.textContent = `(${sizeRange.value}pt)`;
  lineHeightValue.textContent = `(${lineHeightRange.value})`;
}

function renderPreview() {
  sampleArea.style.fontFamily = `"${activeFamily}", serif`;
  sampleArea.style.fontSize = `${sizeRange.value}px`;
  sampleArea.style.lineHeight = lineHeightRange.value;
  sampleArea.style.fontWeight = "400";
  sampleArea.style.fontStyle = "normal";
  updateValueDisplays();

  if (sampleHtml) {
    sampleArea.innerHTML = sampleHtml;
  }
}

function loadSampleText() {
  fetch("assets/sample.html")
    .then((r) => r.text())
    .then((html) => {
      sampleHtml = html;
      renderPreview();
    });
}

function setupScrollFade(list) {
  function update() {
    const atBottom = list.scrollHeight - list.scrollTop - list.clientHeight < 2;
    list.classList.toggle("is-overflowing", !atBottom);
  }
  list.addEventListener("scroll", update);
  extraFonts.addEventListener("toggle", update);
  update();
}

function setupInteractions() {
  sizeRange.addEventListener("input", renderPreview);
  lineHeightRange.addEventListener("input", renderPreview);
  fontSelect.addEventListener("change", (e) => setActiveFont(e.target.value));
  darkModeToggle.addEventListener("change", (e) => {
    reader.classList.toggle("is-dark", e.target.checked);
  });
  bezelToggle.addEventListener("change", (e) => {
    reader.classList.toggle("is-bezel-dark", !e.target.checked);
  });
  if (fontListExtra) {
    setupScrollFade(fontListExtra);
  }
}

function syncToggles() {
  darkModeToggle.checked = reader.classList.contains("is-dark");
  bezelToggle.checked = !reader.classList.contains("is-bezel-dark");
}

function resetControls() {
  const isDesktop = window.matchMedia("(min-width: 1051px)").matches;
  sizeRange.value = isDesktop ? "22" : "20";
  lineHeightRange.value = "1.45";
}

function init() {
  buildFontFaces();
  updateFontList();
  activeFamily = preferredOrder.find((f) => fonts.has(f)) || fonts.keys().next().value;
  setupInteractions();
  resetControls();
  syncToggles();
  loadSampleText();

  if (extraFonts) {
    extraFonts.open = window.matchMedia("(max-width: 1050px)").matches;
  }

  setActiveFont(activeFamily);
}

init();
