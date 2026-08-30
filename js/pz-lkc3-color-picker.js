/* Pz-LinkCard3 カラーピッカー
 * Made by Poporon / Refactoring by ChatGPT
 */

// ======================================================
// Utility
// ======================================================
const rect = el => el.getBoundingClientRect();
const clamp = (v, min, max) => Math.min(Math.max(v, min), max);
const emit = (el, type) => el?.dispatchEvent(new Event(type, { bubbles: true }));
const emitInput = (el) => emit(el, "input");
const labels = window.pz_lkc3_color_picker?.labels || {};
const emitInputAndChange = (el) => {
  emitInput(el);
  emit(el, "change");
};

function getClientXY(e) {
  if (e.touches && e.touches[0]) return { x: e.touches[0].clientX, y: e.touches[0].clientY };
  if (e.changedTouches && e.changedTouches[0]) return { x: e.changedTouches[0].clientX, y: e.changedTouches[0].clientY };
  return { x: e.clientX, y: e.clientY };
}

function toNum(v, fallback = 0) {
  const n = Number(v);
  return Number.isFinite(n) ? n : fallback;
}

function getContentLeft() {
  const menuWrap = document.querySelector("#adminmenuwrap");
  if (menuWrap) return menuWrap.getBoundingClientRect().right;
  const wpContent = document.querySelector("#wpcontent");
  return wpContent ? wpContent.getBoundingClientRect().left : 0;
}

function getContentTop() {
  const infobar = document.querySelector("#pz-infobar");
  if (infobar) return infobar.getBoundingClientRect().bottom;
  const adminBar = document.querySelector("#wpadminbar");
  return adminBar ? adminBar.getBoundingClientRect().bottom : 0;
}

function getPickerBounds() {
  const pr = rect(picker);
  const minLeft = window.scrollX + Math.max(0, getContentLeft());
  const minTop = window.scrollY + Math.max(0, getContentTop());
  const maxLeft = window.scrollX + document.documentElement.clientWidth - pr.width;
  const maxTop = window.scrollY + document.documentElement.clientHeight - pr.height;

  return {
    minLeft,
    minTop,
    maxLeft: Math.max(minLeft, maxLeft),
    maxTop: Math.max(minTop, maxTop)
  };
}

function setPickerPosition(left, top) {
  const bounds = getPickerBounds();
  picker.style.left = `${clamp(left, bounds.minLeft, bounds.maxLeft)}px`;
  picker.style.top = `${clamp(top, bounds.minTop, bounds.maxTop)}px`;
}

// ドラッグ処理を共通化（Pointer Events）
function dragHandler(target, onMove) {
  const onPointerDown = (e) => {
    if (e.button != null && e.button !== 0) return;
    e.preventDefault();
    target.setPointerCapture?.(e.pointerId);

    onMove(e);

    const move = (ev) => { ev.preventDefault(); onMove(ev); };
    const up = (ev) => {
      target.releasePointerCapture?.(ev.pointerId);
      window.removeEventListener("pointermove", move, { passive: false });
      window.removeEventListener("pointerup", up, { passive: false });
      window.removeEventListener("pointercancel", up, { passive: false });
    };

    window.addEventListener("pointermove", move, { passive: false });
    window.addEventListener("pointerup", up, { passive: false });
    window.addEventListener("pointercancel", up, { passive: false });
  };

  target.addEventListener("pointerdown", onPointerDown, { passive: false });
}

// スライダー共通処理
function sliderMove(e, slider, callback) {
  const r = rect(slider);
  const { x } = getClientXY(e);
  const px = clamp(x - r.left, 0, r.width);
  callback(r.width ? (px / r.width) : 0);
}

// ======================================================
// Color Conversion
// ======================================================
function hsvToRgb(h, s, v) {
  h = ((h % 360) + 360) % 360;
  let c = v * s;
  let x = c * (1 - Math.abs((h / 60) % 2 - 1));
  let m = v - c;
  let r1, g1, b1;

  if (h < 60)      { r1 = c; g1 = x; b1 = 0; }
  else if (h < 120){ r1 = x; g1 = c; b1 = 0; }
  else if (h < 180){ r1 = 0; g1 = c; b1 = x; }
  else if (h < 240){ r1 = 0; g1 = x; b1 = c; }
  else if (h < 300){ r1 = x; g1 = 0; b1 = c; }
  else             { r1 = c; g1 = 0; b1 = x; }

  return {
    r: Math.round((r1 + m) * 255),
    g: Math.round((g1 + m) * 255),
    b: Math.round((b1 + m) * 255)
  };
}

function rgbToHex(r, g, b, a = 1) {
  const toHex = v => clamp(Math.round(v), 0, 255).toString(16).padStart(2, "0");
  const rgbHex = "#" + toHex(r) + toHex(g) + toHex(b);
  const alphaHex = toHex(Math.round(clamp(a, 0, 1) * 255));
  return alphaHex === "ff" ? rgbHex : rgbHex + alphaHex;
}

// 不正HEXは null を返す（UIを壊さないため）
function hexToRgbaSafe(hex) {
  if (!hex) return null;
  hex = String(hex).trim().replace("#", "");
  if (hex.length === 3 || hex.length === 4) hex = hex.split("").map(c => c + c).join("");
  if (hex.length !== 6 && hex.length !== 8) return null;

  const r = parseInt(hex.slice(0, 2), 16);
  const g = parseInt(hex.slice(2, 4), 16);
  const b = parseInt(hex.slice(4, 6), 16);
  const a = hex.length === 8 ? parseInt(hex.slice(6, 8), 16) / 255 : 1;

  if (![r, g, b, a].every(Number.isFinite)) return null;
  return { r, g, b, a: clamp(a, 0, 1) };
}

function rgbToHsv(r, g, b) {
  r /= 255; g /= 255; b /= 255;
  const max = Math.max(r, g, b), min = Math.min(r, g, b);
  const d = max - min;
  let h = 0, s = max === 0 ? 0 : d / max, v = max;

  if (d !== 0) {
    switch (max) {
      case r: h = (g - b) / d + (g < b ? 6 : 0); break;
      case g: h = (b - r) / d + 2; break;
      case b: h = (r - g) / d + 4; break;
    }
    h *= 60;
  }
  return { h, s, v };
}

function rgbToHsl(r, g, b) {
  r /= 255; g /= 255; b /= 255;
  const max = Math.max(r, g, b), min = Math.min(r, g, b);
  let h = 0, s = 0, l = (max + min) / 2;

  if (max !== min) {
    const d = max - min;
    s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
    switch (max) {
      case r: h = (g - b) / d + (g < b ? 6 : 0); break;
      case g: h = (b - r) / d + 2; break;
      case b: h = (r - g) / d + 4; break;
    }
    h *= 60;
  }
  return { h, s, l };
}

function hslToRgb(h, s, l) {
  h = ((h % 360) + 360) % 360;
  const c = (1 - Math.abs(2 * l - 1)) * s;
  const x = c * (1 - Math.abs((h / 60) % 2 - 1));
  const m = l - c / 2;
  let r1, g1, b1;

  if (h < 60)      { r1 = c; g1 = x; b1 = 0; }
  else if (h < 120){ r1 = x; g1 = c; b1 = 0; }
  else if (h < 180){ r1 = 0; g1 = c; b1 = x; }
  else if (h < 240){ r1 = 0; g1 = x; b1 = c; }
  else if (h < 300){ r1 = x; g1 = 0; b1 = c; }
  else             { r1 = c; g1 = 0; b1 = x; }

  return {
    r: Math.round((r1 + m) * 255),
    g: Math.round((g1 + m) * 255),
    b: Math.round((b1 + m) * 255)
  };
}

// ======================================================
// State
// ======================================================
const state = { h: 120, s: 0.3, v: 0.96, a: 1 };
let inputMode = 0;
let activeColorInput = null;
let originalColorValue = "";
let pickerDragging = false;
let isColorEmpty = false;

let activeTriggerWrap = null;       // ★最後に開いたトリガー(wrapper)
let suppressOutsideClose = false;   // ★ドラッグ直後の外クリック閉じ抑止

// ======================================================
// Elements
// ======================================================
const picker = document.getElementById("colorPicker");
const svArea = document.getElementById("svArea");
const svCursor = document.getElementById("svCursor");
const hueSlider = document.getElementById("hueSlider");
const hueThumb = document.getElementById("hueThumb");
const alphaSlider = document.getElementById("alphaSlider");
const alphaInner = document.getElementById("alphaInner");
const alphaThumb = document.getElementById("alphaThumb");
const previewCircle = document.getElementById("previewCircle");
const inputWrap = document.getElementById("inputWrap");
const modeLabel = document.getElementById("modeLabel"); // button想定
const palette = document.getElementById("palette");

// ======================================================
// UI Update
// ======================================================
function updatePreview(rgb) {
  const previewColor = isColorEmpty
    ? "transparent"
    : `rgba(${rgb.r},${rgb.g},${rgb.b},${state.a})`;
  previewCircle.style.setProperty("--preview-color", previewColor);
  previewCircle.classList.toggle("pz-checker", isColorEmpty || state.a < 1);
}

function syncInputs() {
  const inputs = inputWrap.querySelectorAll("input");
  const rgb = hsvToRgb(state.h, state.s, state.v);

  if (inputMode === 0 && inputs.length >= 1) {
    inputs[0].value = isColorEmpty ? "" : rgbToHex(rgb.r, rgb.g, rgb.b, state.a);
  } else if (inputMode === 1 && inputs.length === 4) {
    inputs[0].value = rgb.r;
    inputs[1].value = rgb.g;
    inputs[2].value = rgb.b;
    inputs[3].value = Math.round(state.a * 100);
  } else if (inputMode === 2 && inputs.length === 4) {
    const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
    inputs[0].value = Math.round(hsl.h);
    inputs[1].value = Math.round(hsl.s * 100);
    inputs[2].value = Math.round(hsl.l * 100);
    inputs[3].value = Math.round(state.a * 100);
  }
}

function updateUI() {
  // hue を 0〜359 に矯正
  state.h = clamp(state.h, 0, 359);

  const base = hsvToRgb(state.h, 1, 1);
  svArea.style.backgroundColor = `rgb(${base.r},${base.g},${base.b})`;

  const svR = rect(svArea);
  svCursor.style.left = (state.s * svR.width) + "px";
  svCursor.style.top  = ((1 - state.v) * svR.height) + "px";

  const hueR = rect(hueSlider);
  hueThumb.style.left = (state.h / 359) * hueR.width + "px";

  const rgb = hsvToRgb(state.h, state.s, state.v);

  const aR = rect(alphaSlider);
  alphaThumb.style.left = (state.a * aR.width) + "px";
  alphaInner.style.background =
    `linear-gradient(to right, rgba(${rgb.r},${rgb.g},${rgb.b},0), rgba(${rgb.r},${rgb.g},${rgb.b},1))`;

  updatePreview(rgb);

  if (activeColorInput) {
    activeColorInput.value = isColorEmpty ? "" : rgbToHex(rgb.r, rgb.g, rgb.b, state.a);
    emitInput(activeColorInput);
  }

  syncInputs();
}

// ======================================================
// Drag Events（Pointer Events）
// ======================================================
dragHandler(svArea, e => {
  const r = rect(svArea);
  const { x, y } = getClientXY(e);

  const px = clamp(x - r.left, 0, r.width);
  const py = clamp(y - r.top, 0, r.height);

  state.s = r.width ? (px / r.width) : 0;
  state.v = r.height ? (1 - (py / r.height)) : 0;
  isColorEmpty = false;

  updateUI();
});

dragHandler(hueSlider, e => {
  sliderMove(e, hueSlider, ratio => {
    state.h = ratio * 359;
    isColorEmpty = false;
    updateUI();
  });
});

dragHandler(alphaSlider, e => {
  sliderMove(e, alphaSlider, ratio => {
    state.a = clamp(ratio, 0, 1);
    isColorEmpty = false;
    updateUI();
  });
});

// ======================================================
// Picker Window Drag Move（縦ズレ修正 + 画面内クランプ + ドラッグ直後閉じ抑止）
// ======================================================
(function enablePickerDrag() {
  let offsetX = 0, offsetY = 0;
  let moved = false;

  picker.addEventListener("pointerdown", e => {
    const isBlocked =
      e.target.closest(".pz-sv-area") ||
      e.target.closest(".pz-hue-slider") ||
      e.target.closest(".pz-alpha-slider") ||
      e.target.closest(".pz-input-wrap") ||
      e.target.closest(".pz-mode-label") ||
      e.target.closest(".pz-palette");

    if (isBlocked) return;

    pickerDragging = true;
    moved = false;
    suppressOutsideClose = false;

    picker.setPointerCapture?.(e.pointerId);

    // client座標同士でオフセットを取る（スクロールしてもズレない）
    const r = rect(picker);
    offsetX = e.clientX - r.left;
    offsetY = e.clientY - r.top;

    picker.style.opacity = "0.6";
    e.preventDefault();
  }, { passive: false });

  window.addEventListener("pointermove", e => {
    if (!pickerDragging) return;

    moved = true;
    suppressOutsideClose = true;

    // client -> ページ座標へは最後に scroll を足す
    let left = (e.clientX - offsetX) + window.scrollX;
    let top  = (e.clientY - offsetY) + window.scrollY;

    // 画面(ビューポート)内にクランプ
    setPickerPosition(left, top);
  });

  window.addEventListener("pointerup", () => {
    if (!pickerDragging) return;

    pickerDragging = false;
    picker.style.opacity = "1";

    if (moved) setTimeout(() => { suppressOutsideClose = false; }, 0);
    else suppressOutsideClose = false;
  });
})();

// ======================================================
// Input Mode Switching（モード色切替）
// ======================================================
function createLabelRow(labels) {
  const row = document.createElement("div");
  row.className = "pz-input-label-row";
  labels.forEach(t => {
    const s = document.createElement("span");
    s.textContent = t;
    row.appendChild(s);
  });
  return row;
}

function renderInputFields() {
  inputWrap.innerHTML = "";
  const rgb = hsvToRgb(state.h, state.s, state.v);

  if (inputMode === 0) {
    modeLabel.classList.remove("is-hex", "is-rgb", "is-hsl");
    modeLabel.classList.add("is-hex");
    modeLabel.textContent = "HEX";

    const labelRow = createLabelRow(["HEX"]);
    labelRow.classList.add("is-hex");
    inputWrap.appendChild(labelRow);

    // HEX input + Clear
    const row = document.createElement("div");
    row.className = "pz-hex-row";

    const input = document.createElement("input");
    input.type = "text";
    input.className = "pz-hex-input is-hex";
    input.value = rgbToHex(rgb.r, rgb.g, rgb.b, state.a);

    // 完成形になった瞬間だけ即時反映（#RRGGBB / #RRGGBBAA）
    input.addEventListener("input", e => {
      const v = String(e.target.value).trim();
      if (!v) {
        isColorEmpty = true;
        if (activeColorInput) {
          activeColorInput.value = "";
          emitInput(activeColorInput);
        }
        updateUI();
        return;
      }

      // #付きの完成形のみを即時反映
      if (!/^#([0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/.test(v)) return;

      const parsed = hexToRgbaSafe(v);
      if (!parsed) return;

      const hsv = rgbToHsv(parsed.r, parsed.g, parsed.b);
      state.h = hsv.h;
      state.s = hsv.s;
      state.v = hsv.v;
      state.a = parsed.a;
      isColorEmpty = false;

      updateUI();
    });

    // フォーカスアウト時は空欄不可・不正なら戻す
    input.addEventListener("change", e => {
      const value = String(e.target.value).trim();

      // 空欄を許容
      if (!value) {
        isColorEmpty = true;
        if (activeColorInput) {
          activeColorInput.value = "";
          emitInputAndChange(activeColorInput);
        }
        updateUI();
        return;
      }

      const parsed = hexToRgbaSafe(value);
      if (!parsed) { syncInputs(); return; }

      const hsv = rgbToHsv(parsed.r, parsed.g, parsed.b);
      state.h = hsv.h; state.s = hsv.s; state.v = hsv.v; state.a = parsed.a;
      isColorEmpty = false;
      updateUI();
    });

    const clearBtn = document.createElement("button");
    clearBtn.type = "button";
    clearBtn.className = "pz-hex-clear";
    clearBtn.textContent = labels.clear || "Clear";
    clearBtn.addEventListener("click", () => {
      input.value = "";
      isColorEmpty = true;
      if (activeColorInput) {
        activeColorInput.value = "";
        emitInputAndChange(activeColorInput);
      }
      updateUI();
    });

    row.appendChild(input);
    row.appendChild(clearBtn);
    inputWrap.appendChild(row);
  }

  if (inputMode === 1) {
    modeLabel.classList.remove("is-hex", "is-rgb", "is-hsl");
    modeLabel.classList.add("is-rgb");
    modeLabel.textContent = "RGB";

    inputWrap.appendChild(createLabelRow(["R", "G", "B", "A"]));

    [rgb.r, rgb.g, rgb.b, Math.round(state.a * 100)].forEach(val => {
      const input = document.createElement("input");
      input.type = "number";
      input.className = "pz-hex-input pz-small";
      input.value = val;

      input.addEventListener("input", () => {
        const ins = inputWrap.querySelectorAll("input");
        const r = clamp(toNum(ins[0].value, rgb.r), 0, 255);
        const g = clamp(toNum(ins[1].value, rgb.g), 0, 255);
        const b = clamp(toNum(ins[2].value, rgb.b), 0, 255);
        const a = clamp(toNum(ins[3].value, Math.round(state.a * 100)) / 100, 0, 1);

        const hsv = rgbToHsv(r, g, b);
        state.h = hsv.h; state.s = hsv.s; state.v = hsv.v; state.a = a;
        updateUI();
      });

      inputWrap.appendChild(input);
    });
  }

  if (inputMode === 2) {
    modeLabel.classList.remove("is-hex", "is-rgb", "is-hsl");
    modeLabel.classList.add("is-hsl");
    modeLabel.textContent = "HSL";

    inputWrap.appendChild(createLabelRow(["H", "S", "L", "A"]));

    const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
    [Math.round(hsl.h), Math.round(hsl.s * 100), Math.round(hsl.l * 100), Math.round(state.a * 100)]
      .forEach(val => {
        const input = document.createElement("input");
        input.type = "number";
        input.className = "pz-hex-input pz-small";
        input.value = val;

        input.addEventListener("input", () => {
          const ins = inputWrap.querySelectorAll("input");
          const h = toNum(ins[0].value, hsl.h);
          const s = clamp(toNum(ins[1].value, hsl.s * 100) / 100, 0, 1);
          const l = clamp(toNum(ins[2].value, hsl.l * 100) / 100, 0, 1);
          const a = clamp(toNum(ins[3].value, Math.round(state.a * 100)) / 100, 0, 1);

          const rgb2 = hslToRgb(h, s, l);
          const hsv = rgbToHsv(rgb2.r, rgb2.g, rgb2.b);
          state.h = hsv.h; state.s = hsv.s; state.v = hsv.v; state.a = a;
          updateUI();
        });

        inputWrap.appendChild(input);
      });
  }

  updateUI();
}

modeLabel.addEventListener("click", (e) => {
  e.preventDefault();
  inputMode = (inputMode + 1) % 3;
  renderInputFields();
});

// ======================================================
// Palette
// ======================================================
palette.innerHTML = "";
[
  "#000000", "#333333", "#666666", "#999999",
  "#cccccc", "#ffffff", "#ff0000", "#ff7f00",
  "#ffff00", "#00ff00", "#00ffff", "#0000ff",
  "#8b00ff", "#ff00ff", "#a6f5a2", "#ffc0cb"
].forEach(color => {
  const div = document.createElement("div");
  div.className = "pz-palette-color";
  div.style.backgroundColor = color;

  div.addEventListener("click", () => {
    const parsed = hexToRgbaSafe(color);
    if (!parsed) return;
    const hsv = rgbToHsv(parsed.r, parsed.g, parsed.b);
    state.h = hsv.h; state.s = hsv.s; state.v = hsv.v; state.a = 1;
    isColorEmpty = false;
    updateUI();
  });

  palette.appendChild(div);
});

// ======================================================
// Open Picker（swatchは input/change で同期）
// disabledは置き換えるがクリック無効
// ======================================================
document.querySelectorAll(".pz-color-picker").forEach(input => {
  const wrapper = document.createElement("div");
  wrapper.className = "pz-color-button";

  // disabled の場合は見た目用クラス（CSSは任意）
  if (input.disabled) {
    wrapper.classList.add("is-disabled");
  }

  const swatch = document.createElement("div");
  swatch.className = "pz-color-swatch";
  swatch.style.backgroundColor = input.value;

  const label = document.createElement("span");
  label.textContent = labels.selectColor || "Select color";

  wrapper.appendChild(swatch);
  wrapper.appendChild(label);

  input.style.display = "none";
  input.parentNode.insertBefore(wrapper, input.nextSibling);

  const isDisabled = () =>
    input.disabled ||
    input.readOnly ||
    input.getAttribute("aria-disabled") === "true" ||
    input.classList.contains("pz-disabled") ||
    input.closest(".pz-disabled");

  const syncDisabled = () => {
    const disabled = Boolean(isDisabled());
    wrapper.classList.toggle("is-disabled", disabled);
    wrapper.setAttribute("aria-disabled", disabled ? "true" : "false");
  };

  const syncSwatch = () => {
    const value = String(input.value || "").trim();
    swatch.style.backgroundColor = value || "transparent";
    swatch.classList.toggle("pz-checker", !value);
    syncDisabled();
  };
  input.addEventListener("input", syncSwatch);
  input.addEventListener("change", syncSwatch);
  const observer = new MutationObserver(syncDisabled);
  observer.observe(input, { attributes: true, attributeFilter: ["class", "disabled", "readonly", "aria-disabled"] });
  syncSwatch();

  wrapper.addEventListener("click", () => {
    // disabledならクリック無効
    if (isDisabled()) return;
    // 同じトリガーを再クリックしたら閉じる
    if (picker.style.display !== "none" && activeTriggerWrap === wrapper) {
      closeColorPicker();
      return;
    }

    activeTriggerWrap = wrapper;

    activeColorInput = input;
    originalColorValue = input.value;

    const r = rect(wrapper);
    picker.style.display = "block"; // 先に表示（高さを測るため）

    // 横位置（そのまま）
    let left = r.left + window.scrollX;

    // 下に開いた場合の top
    let topBelow = r.bottom + window.scrollY + 6;

    // ピッカーの高さ
    const pickerRect = rect(picker);

    // ビューポート下端（ページ座標）
    const viewportBottom = window.scrollY + window.innerHeight;

    // 下がはみ出すか？
    let top;
    if (topBelow + pickerRect.height > viewportBottom) {
      // 上に開く
      top = r.top + window.scrollY - pickerRect.height - 6;
    } else {
      // 下に開く
      top = topBelow;
    }

    setPickerPosition(left, top);

    const parsed = hexToRgbaSafe(input.value);
    isColorEmpty = !String(input.value || "").trim();
    if (parsed) {
      const hsv = rgbToHsv(parsed.r, parsed.g, parsed.b);
      state.h = hsv.h; state.s = hsv.s; state.v = hsv.v; state.a = parsed.a;
      isColorEmpty = false;
    }

    renderInputFields();
  });
});

// ======================================================
// Close Picker
// ======================================================
function closeColorPicker() {
  picker.style.display = "none";
  activeColorInput = null;
  activeTriggerWrap = null;
}

document.addEventListener("pointerdown", e => {
  if (picker.style.display === "none") return;

  if (pickerDragging) return;
  if (suppressOutsideClose) return;

  if (picker.contains(e.target)) return;

  // トリガーボタン(wrapper)上のクリックは閉じない
  if (activeTriggerWrap && activeTriggerWrap.contains(e.target)) return;

  closeColorPicker();
});

window.addEventListener("keydown", e => {
  if (picker.style.display === "none") return;

  if (e.key === "Escape") {
    if (activeColorInput) {
      activeColorInput.value = originalColorValue;
      emitInput(activeColorInput);
    }
    closeColorPicker();
  }

  if (e.key === "Enter") closeColorPicker();
});

// ダブルクリックで閉じる
[svArea, hueSlider, alphaSlider, palette].forEach(el => {
  el.addEventListener("dblclick", () => closeColorPicker());
});

// ======================================================
// Init
// ======================================================
renderInputFields();
// updateUI(); // renderInputFields() 内で updateUI() 済みなので多重呼び出し防止
