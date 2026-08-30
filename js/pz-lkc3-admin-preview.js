/* ===== Settings preview ===== */

/* =========================================
  CACHE
========================================= */
const SHADOW_CACHE = {};
const TRANSFORM_CACHE = {};


/* =========================================
  UTIL
========================================= */

function getValue(el){
  if (!el) return '';
  if (el.type === 'checkbox') return el.checked ? el.value : '';
  return el.value ?? '';
}

function getPropertyValue(key){
  const elements = Array.from(document.getElementsByName(`properties[${key}]`))
    .filter(el => !el.classList.contains('pz-disabled'));

  if (elements.length === 0) return '';

  const checkboxes = elements.filter(el => el.type === 'checkbox');
  if (checkboxes.length > 0) {
    const checked = checkboxes.find(el => el.checked);
    return checked ? checked.value : '';
  }

  const radios = elements.filter(el => el.type === 'radio');
  if (radios.length > 0) {
    const checked = radios.find(el => el.checked);
    return checked ? checked.value : '';
  }

  const visible = elements.find(el => el.type !== 'hidden');
  return getValue(visible || elements[elements.length - 1]);
}

function setStyle(targets, prop, value, priority = ''){
  if (!targets || !prop) return;
  const sels = Array.isArray(targets) ? targets : [targets];

  sels.forEach(sel=>{
    document.querySelectorAll(sel).forEach(el=>{
      if (Array.isArray(prop)) {
        prop.forEach(p=>el.style.setProperty(p,value,priority));
      } else {
        el.style.setProperty(prop,value,priority);
      }
    });
  });
}

function applyWidthRule(forceInitial = false){
  const targets = ['.lkc3-external-wrap', '.lkc3-internal-wrap'];
  if (forceInitial) {
    setStyle(targets, 'width', 'initial');
    setStyle(targets, 'max-width', 'initial');
    return;
  }

  const widthEl = document.querySelector('[name="properties[width]"]');
  const unitEl = document.querySelector('[name="properties[width-unit]"]');
  const width = getValue(widthEl);
  const unit = getValue(unitEl) === '%' ? '%' : 'px';
  if (width === '') {
    setStyle(targets, 'width', 'initial');
    setStyle(targets, 'max-width', 'initial');
    return;
  }

  if (unit === '%') {
    setStyle(targets, 'width', `${width}%`);
    setStyle(targets, 'max-width', 'initial');
  } else {
    setStyle(targets, 'width', 'initial');
    setStyle(targets, 'max-width', `${width}px`);
  }
}

function setHtmlAttrs(htmlAttrs, value){
  if (!Array.isArray(htmlAttrs) || htmlAttrs.length === 0) return;
  const normalizedValue = value == null ? '' : String(value);

  htmlAttrs.forEach((item)=>{
    if (!item) return;
    const sel = item.sel || item.selector || '';
    const attr = item.attr || '';
    if (!sel || !attr) return;

    document.querySelectorAll(sel).forEach((el)=>{
      if (normalizedValue === '') {
        el.removeAttribute(attr);
      } else {
        el.setAttribute(attr, normalizedValue);
      }
    });
  });
}

function getGroupKey(rule){
  if (rule.group) return rule.group;
  const targets = Array.isArray(rule.targets) ? rule.targets.join(',') : rule.targets;
  return `${rule.prop}::${targets}`;
}

function inferPart(rule, key){
  if (rule.part) return rule.part;
  if (rule.prop === 'box-shadow'){
    if (key.endsWith('-shadow-enabled')) return 'enabled';
    if (key.endsWith('-shadow-x')) return 'x';
    if (key.endsWith('-shadow-y')) return 'y';
    if (key.endsWith('-shadow-blur')) return 'blur';
    if (key.endsWith('-shadow-spread')) return 'spread';
    if (key.endsWith('-shadow-color')) return 'color';
    if (key.endsWith('-shadow-inset')) return 'inset';
  }
  if (rule.prop === 'transform'){
    if (key.endsWith('-transform-enabled')) return 'enabled';
    if (key.endsWith('-transform-x')) return 'x';
    if (key.endsWith('-transform-y')) return 'y';
    if (key.endsWith('-transform-rotate')) return 'rotate';
    if (key.endsWith('-transform-scale')) return 'scale';
  }
  return null;
}

function initGroupCache(cache, prop, groupKey){
  cache[groupKey] ||= {};
  const group = cache[groupKey];
  if (Object.keys(group).length > 0) return;

  Object.entries(RULES).forEach(([k, r])=>{
    if (!r || r.prop !== prop) return;
    if (getGroupKey(r) !== groupKey) return;

    const part = inferPart(r, k);
    if (!part) return;

    const v = getPropertyValue(k);
    if (v === '' && part !== 'enabled') return;
    group[part] = v;
  });
}


/* =========================================
  COLLECT FORM
========================================= */
function collectAllProperties(){
  const fd = new FormData();
  const values = new Map();

  document.querySelectorAll('[name]').forEach(el=>{
    if (!el.name?.startsWith('properties[')) return;
    if (el.classList.contains('pz-disabled')) return;

    if (el.type === 'checkbox') {
      const checkboxValue = el.checked ? el.value : '';
      values.set(el.name, checkboxValue);
    } else if (el.type === 'radio') {
      if (el.checked) values.set(el.name, el.value);
    } else {
      values.set(el.name, el.value);
    }
  });

  values.forEach((value, name)=>{
    fd.append(name, value);
  });

  return fd;
}


/* =========================================
  CALLBACKS
========================================= */
let cssKickTimer = null;
let htmlKickTimer = null;
let cssRequestSeq = 0;
let htmlRequestSeq = 0;
const APPLIED_INPUTS = new Map();

function getAjaxUrl(){
  if (typeof lkc3_ajax_preview !== 'undefined' && lkc3_ajax_preview?.ajaxurl) return lkc3_ajax_preview.ajaxurl;
  if (typeof ajaxurl !== 'undefined' && ajaxurl) return ajaxurl;
  return '';
}

async function postPreviewAction(action){
  const ajaxUrl = getAjaxUrl();
  if (!ajaxUrl) return '';

  const fd = collectAllProperties();
  fd.append('action', action);
  fd.append('_ajax_nonce', lkc3_ajax_preview?.nonce || '');

  const response = await fetch(ajaxUrl, { method: 'POST', body: fd });
  return response.text();
}

function scheduleKickCssCallback(delay = 120){
  if (cssKickTimer) clearTimeout(cssKickTimer);
  cssKickTimer = setTimeout(()=>{ kickCssCallback(); }, delay);
}

function scheduleKickHtmlCallback(delay = 160){
  if (htmlKickTimer) clearTimeout(htmlKickTimer);
  htmlKickTimer = setTimeout(()=>{ kickHtmlCallback(); }, delay);
}

async function kickCssCallback(){
  const seq = ++cssRequestSeq;
  try {
    const css = await postPreviewAction('pz_lkc3_generate_css');
    if (seq !== cssRequestSeq) return;
    const styleEl = document.getElementById('pz-lkc3-preview-css');
    if (styleEl) styleEl.textContent = css;
  } catch (err) {
    console.warn('kickCssCallback failed:', err);
  }
}

async function kickHtmlCallback(){
  const seq = ++htmlRequestSeq;
  try {
    const html = await postPreviewAction('pz_lkc3_generate_html');
    if (seq !== htmlRequestSeq) return;
    const preview = document.getElementById('pz-lkc3-preview');
    if (preview) preview.innerHTML = html;
  } catch (err) {
    console.warn('kickHtmlCallback failed:', err);
  }
}

/* =========================================
  BUILDERS
========================================= */
function buildBoxShadow(g){
  if (Object.prototype.hasOwnProperty.call(g, 'enabled') && !g.enabled) return 'initial';

  const x = g.x||0, y=g.y||0, b=g.blur||0, s=g.spread||0;
  const c = g.color||'#000';
  const i = g.inset ? ' inset':'';
  return `${x}px ${y}px ${b}px ${s}px ${c}${i}`;
}

function buildTransform(g){
  if (Object.prototype.hasOwnProperty.call(g, 'enabled') && !g.enabled) return 'initial';

  const x=g.x||0, y=g.y||0, r=g.rotate||0;
  const sc=(g.scale||100)/100;
  return `translateX(${x}px) translateY(${y}px) rotate(${r}deg) scale(${sc})`;
}


/* =========================================
  APPLY
========================================= */
function applyRule(key,value,forceInitial=false){
  if (key === 'width' || key === 'width-unit') {
    applyWidthRule(forceInitial);
    return;
  }

  const rule = RULES[key];
  if (!rule) { 
    // RULES 外の項目は CSS 再生成（連打対策でデバウンス）
    scheduleKickCssCallback();
    return;
  }

  const hasHtmlAttrs = Array.isArray(rule.htmlAttrs) && rule.htmlAttrs.length > 0;
  const shouldKickCss = rule.css === '1';
  const shouldKickHtml = rule.html === '1';
  if (forceInitial && rule.prop) {
    if (rule.prop === 'box-shadow') delete SHADOW_CACHE[getGroupKey(rule)];
    if (rule.prop === 'transform') delete TRANSFORM_CACHE[getGroupKey(rule)];
    setStyle(rule.targets, rule.prop, 'initial', rule.prop === 'box-shadow' ? 'important' : '');
    if (hasHtmlAttrs) setHtmlAttrs(rule.htmlAttrs, '');
    if (shouldKickCss) scheduleKickCssCallback();
    if (shouldKickHtml) scheduleKickHtmlCallback();
    return;
  }

  // box-shadow
  if (rule.prop==='box-shadow'){
    const g = getGroupKey(rule);
    initGroupCache(SHADOW_CACHE, 'box-shadow', g);
    const part = inferPart(rule, key);
    if (!part) {
      const finalValue = (rule.before||'') + value + (rule.after||'');
      setStyle(rule.targets, rule.prop, finalValue, 'important');
      if (shouldKickCss) scheduleKickCssCallback();
      if (shouldKickHtml) scheduleKickHtmlCallback();
      return;
    }
    SHADOW_CACHE[g] ||= {};
    SHADOW_CACHE[g][part] = value;

    const v = buildBoxShadow(SHADOW_CACHE[g]);
    setStyle(rule.targets,'box-shadow',v,'important');
    if (shouldKickCss) scheduleKickCssCallback();
    if (shouldKickHtml) scheduleKickHtmlCallback();
    return;
  }

  // transform
  if (rule.prop==='transform'){
    const g = getGroupKey(rule);
    initGroupCache(TRANSFORM_CACHE, 'transform', g);
    const part = inferPart(rule, key);
    if (!part) {
      const finalValue = (rule.before||'') + value + (rule.after||'');
      setStyle(rule.targets, rule.prop, finalValue);
      if (shouldKickCss) scheduleKickCssCallback();
      if (shouldKickHtml) scheduleKickHtmlCallback();
      return;
    }
    TRANSFORM_CACHE[g] ||= {};
    TRANSFORM_CACHE[g][part] = value;

    const v = buildTransform(TRANSFORM_CACHE[g]);
    setStyle(rule.targets,'transform',v);
    if (shouldKickCss) scheduleKickCssCallback();
    if (shouldKickHtml) scheduleKickHtmlCallback();
    return;
  }

  if (rule.prop === '!toggle') {
    const props = Array.isArray(rule.props) ? rule.props : [];
    const keys = Array.isArray(rule.keys) ? rule.keys : [];

    if (value === '') {
      setStyle(rule.targets, props, 'initial', 'important');
      scheduleKickCssCallback();
      return;
    }

    setStyle(rule.targets, props, '');
    keys.forEach((targetKey) => {
      applyRule(targetKey, getPropertyValue(targetKey), false);
    });
    scheduleKickCssCallback();
    return;
  }

  if (rule.prop === '!bold') {
    const styleValue = value === '' ? 'normal' : 'bold';
    setStyle(rule.targets, 'font-weight', styleValue);
    if (hasHtmlAttrs) setHtmlAttrs(rule.htmlAttrs, value);
    if (shouldKickCss) scheduleKickCssCallback();
    if (shouldKickHtml) scheduleKickHtmlCallback();
    return;
  }

  if (rule.prop === '!italic') {
    const styleValue = value === '' ? 'normal' : 'italic';
    setStyle(rule.targets, 'font-style', styleValue);
    if (hasHtmlAttrs) setHtmlAttrs(rule.htmlAttrs, value);
    if (shouldKickCss) scheduleKickCssCallback();
    if (shouldKickHtml) scheduleKickHtmlCallback();
    return;
  }

  if (rule.prop === '!underline') {
    const styleValue = value === '' ? 'none' : 'underline';
    setStyle(rule.targets, 'text-decoration', styleValue);
    if (hasHtmlAttrs) setHtmlAttrs(rule.htmlAttrs, value);
    if (shouldKickCss) scheduleKickCssCallback();
    if (shouldKickHtml) scheduleKickHtmlCallback();
    return;
  }

  const finalValue = (rule.before||'') + value + (rule.after||'');
  setStyle(rule.targets, rule.prop, finalValue);
  if (hasHtmlAttrs) setHtmlAttrs(rule.htmlAttrs, value);
  if (shouldKickCss) scheduleKickCssCallback();
  if (shouldKickHtml) scheduleKickHtmlCallback();
}


/* =========================================
  EVENT
========================================= */
function bindInputs(){
  ['#pz-position','#pz-display','#pz-letter','#pz-external','#pz-internal']
  .forEach(sel=>{
    const root=document.querySelector(sel);
    if(!root) return;

    const handleInput = (el) => {
      const name=el.name;
      if(!name) return;
      if(!name.startsWith('properties[')) {
        scheduleKickHtmlCallback();
        return;
      }

      const key=name.match(/properties\[(.+)\]/)?.[1];
      if(!key) return;

      const forceInitial = el.classList.contains('pz-disabled');
      const value = getValue(el);
      const signature = `${value}|${forceInitial ? '1' : '0'}`;
      if (APPLIED_INPUTS.get(key) === signature) return;
      APPLIED_INPUTS.set(key, signature);
      applyRule(key,value,forceInitial);
    };

    root.querySelectorAll('input,select,textarea').forEach(el=>{
      el.addEventListener('input',()=>{ handleInput(el); });
      el.addEventListener('change',()=>{ handleInput(el); });
    });

    // .pz-disabled の付け外しで無効化された値を反映
    const observer = new MutationObserver((mutations)=>{
      mutations.forEach((m)=>{
        if (m.type !== 'attributes' || m.attributeName !== 'class') return;
        const el = m.target;
        if (!(el instanceof HTMLElement)) return;
        if (!el.name?.startsWith('properties[')) return;
        handleInput(el);
      });
    });
    observer.observe(root, { subtree: true, attributes: true, attributeFilter: ['class'] });
  });
}

/* =========================================
  INIT
========================================= */
document.addEventListener('DOMContentLoaded',()=>{
  bindInputs();
  kickHtmlCallback();
  kickCssCallback();
});

document.addEventListener("DOMContentLoaded", () => {
  const previewContainer = document.getElementById("pz-preview-container");
  const previewCheckbox = document.querySelector("input.pz-preview-checkbox");
  const preview = document.getElementById("pz-lkc3-preview");
  const dragHandle = document.getElementById("pz-resize-handle");
  const modeButton = document.getElementById("pz-preview-mode");
  const closeButton = document.getElementById("pz-preview-close");
  if (!previewContainer || !previewCheckbox) return;

  const storageKeyChecked = "pz-preview-checked";
  const storageKeyWidth = "pz-preview-width";
  const storageKeyHeight = "pz-preview-height";
  const storageKeyLeft = "pz-preview-left";
  const storageKeyTop = "pz-preview-top";
  const storageKeyMode = "pz-preview-mode";
  const storageKeyDockedHeight = "pz-preview-docked-height";

  const DEFAULT_WIDTH = 980;
  const DEFAULT_HEIGHT = 300;
  const MIN_WIDTH = 320;
  const MIN_HEIGHT = 160;
  const DOCKED_MIN_HEIGHT = 24;
  const EDGE_MARGIN = 12;
  let suppressHandleDblClick = false;
  let lastHandleClick = { time: 0, x: 0, y: 0 };

  const preventPreviewLink = (event) => {
    if (!event.target?.closest?.("#pz-lkc3-preview a")) return;
    event.preventDefault();
    event.stopPropagation();
  };
  preview?.addEventListener("click", preventPreviewLink, true);
  preview?.addEventListener("auxclick", preventPreviewLink, true);

  const clamp = (value, min, max) => Math.min(max, Math.max(min, value));
  const resizeDirections = ["n", "ne", "e", "se", "s", "sw", "w", "nw"];
  const readInt = (key) => {
    const value = parseInt(localStorage.getItem(key) || "", 10);
    return Number.isFinite(value) ? value : null;
  };
  const writeInt = (key, value) => {
    if (Number.isFinite(value)) localStorage.setItem(key, String(Math.round(value)));
  };
  const isPreviewVisible = () => previewCheckbox.checked;
  const isPreviewDocked = () => localStorage.getItem(storageKeyMode) === "docked";
  const getViewportWidth = () => document.documentElement.clientWidth || window.innerWidth;
  const getViewportHeight = () => document.documentElement.clientHeight || window.innerHeight;
  const updateModeButton = () => {
    if (!modeButton) return;

    const docked = isPreviewDocked();
    modeButton.textContent = docked ? "□" : "-";
    modeButton.classList.toggle("pz-preview-mode-docked", docked);
  };

  const getBounds = () => {
    const infobar = document.getElementById("pz-infobar");
    const menu = document.getElementById("adminmenuwrap");
    const minTop = Math.max(EDGE_MARGIN, infobar ? infobar.getBoundingClientRect().bottom + EDGE_MARGIN : EDGE_MARGIN);
    const minLeft = Math.max(EDGE_MARGIN, menu ? menu.getBoundingClientRect().right + EDGE_MARGIN : EDGE_MARGIN);
    return { minLeft, minTop };
  };

  const getDockLeft = () => {
    const menu = document.getElementById("adminmenuwrap");
    if (menu) return Math.max(0, menu.getBoundingClientRect().right);

    const wpContent = document.getElementById("wpcontent");
    return wpContent ? Math.max(0, wpContent.getBoundingClientRect().left) : 0;
  };

  const getWindowRect = () => {
    const rect = previewContainer.getBoundingClientRect();
    return {
      left: rect.left,
      top: rect.top,
      width: rect.width,
      height: rect.height,
    };
  };

  const setDockedScrollSpace = (height = 0) => {
    const active = height > 0 && isPreviewVisible() && isPreviewDocked();
    document.body.classList.toggle("pz-preview-docked-active", active);
    if (active) {
      document.documentElement.style.setProperty("--pz-preview-docked-height", `${height}px`);
    } else {
      document.documentElement.style.removeProperty("--pz-preview-docked-height");
    }
  };

  const fitToViewport = (left, top, width, height) => {
    const { minLeft, minTop } = getBounds();
    const viewportWidth = getViewportWidth();
    const viewportHeight = getViewportHeight();
    const maxWidth = Math.max(MIN_WIDTH, viewportWidth - minLeft - EDGE_MARGIN);
    const maxHeight = Math.max(MIN_HEIGHT, viewportHeight - minTop - EDGE_MARGIN);
    const nextWidth = clamp(width, MIN_WIDTH, maxWidth);
    const nextHeight = clamp(height, MIN_HEIGHT, maxHeight);
    const maxLeft = Math.max(minLeft, viewportWidth - nextWidth - EDGE_MARGIN);
    const maxTop = Math.max(minTop, viewportHeight - nextHeight - EDGE_MARGIN);

    return {
      left: clamp(left, minLeft, maxLeft),
      top: clamp(top, minTop, maxTop),
      width: nextWidth,
      height: nextHeight,
    };
  };

  const getResizeRect = (direction, startRect, startX, startY, event) => {
    const { minLeft, minTop } = getBounds();
    const maxRight = getViewportWidth() - EDGE_MARGIN;
    const maxBottom = getViewportHeight() - EDGE_MARGIN;
    const dx = event.clientX - startX;
    const dy = event.clientY - startY;
    let left = startRect.left;
    let top = startRect.top;
    let right = startRect.left + startRect.width;
    let bottom = startRect.top + startRect.height;

    if (direction.includes("w")) left = clamp(startRect.left + dx, minLeft, right - MIN_WIDTH);
    if (direction.includes("e")) right = clamp(startRect.left + startRect.width + dx, left + MIN_WIDTH, maxRight);
    if (direction.includes("n")) top = clamp(startRect.top + dy, minTop, bottom - MIN_HEIGHT);
    if (direction.includes("s")) bottom = clamp(startRect.top + startRect.height + dy, top + MIN_HEIGHT, maxBottom);

    return {
      left,
      top,
      width: right - left,
      height: bottom - top,
    };
  };

  const applyRect = (rect, save = false) => {
    const next = fitToViewport(rect.left, rect.top, rect.width, rect.height);
    previewContainer.classList.remove("pz-preview-docked");
    updateModeButton();
    setDockedScrollSpace(0);
    previewContainer.style.left = `${next.left}px`;
    previewContainer.style.top = `${next.top}px`;
    previewContainer.style.width = `${next.width}px`;
    previewContainer.style.height = `${next.height}px`;
    previewContainer.style.right = "auto";
    previewContainer.style.bottom = "auto";

    if (save) {
      writeInt(storageKeyLeft, next.left);
      writeInt(storageKeyTop, next.top);
      writeInt(storageKeyWidth, next.width);
      writeInt(storageKeyHeight, next.height);
    }
  };

  const applyDockedRect = (height = null, save = false) => {
    const { minTop } = getBounds();
    const dockLeft = getDockLeft();
    const viewportWidth = getViewportWidth();
    const viewportHeight = getViewportHeight();
    const maxHeight = Math.max(DOCKED_MIN_HEIGHT, viewportHeight - minTop);
    const nextHeight = clamp(
      height ?? readInt(storageKeyDockedHeight) ?? readInt(storageKeyHeight) ?? DEFAULT_HEIGHT,
      DOCKED_MIN_HEIGHT,
      maxHeight
    );

    previewContainer.classList.add("pz-preview-docked");
    updateModeButton();
    previewContainer.style.left = `${dockLeft}px`;
    previewContainer.style.top = `${viewportHeight - nextHeight}px`;
    previewContainer.style.right = "auto";
    previewContainer.style.bottom = "auto";
    previewContainer.style.width = `${Math.max(MIN_WIDTH, viewportWidth - dockLeft)}px`;
    previewContainer.style.height = `${nextHeight}px`;
    setDockedScrollSpace(nextHeight);

    if (save) writeInt(storageKeyDockedHeight, nextHeight);
  };

  const getInitialRect = () => {
    const { minLeft, minTop } = getBounds();
    const viewportWidth = getViewportWidth();
    const viewportHeight = getViewportHeight();
    const width = readInt(storageKeyWidth) ?? Math.min(DEFAULT_WIDTH, viewportWidth - minLeft - EDGE_MARGIN);
    const height = readInt(storageKeyHeight) ?? Math.min(DEFAULT_HEIGHT, viewportHeight - minTop - EDGE_MARGIN);
    const left = readInt(storageKeyLeft) ?? Math.max(minLeft, viewportWidth - width - 24);
    const top = readInt(storageKeyTop) ?? Math.max(minTop, viewportHeight - height - 24);
    return { left, top, width, height };
  };

  const applyPreviewState = () => {
    const visible = isPreviewVisible();
    previewContainer.style.display = visible ? "block" : "none";
    if (!visible) {
      updateModeButton();
      setDockedScrollSpace(0);
      return;
    }
    if (isPreviewDocked()) applyDockedRect(null, true);
    else applyRect(getInitialRect(), true);
  };

  const beginPointerDrag = (event, cursor, onMove, onEnd) => {
    event.preventDefault();
    const captureTarget = event.currentTarget || previewContainer;
    captureTarget.setPointerCapture?.(event.pointerId);
    document.body.style.cursor = cursor;
    document.body.style.userSelect = "none";

    const move = (moveEvent) => onMove(moveEvent);
    const stop = (stopEvent) => {
      captureTarget.releasePointerCapture?.(stopEvent.pointerId);
      document.body.style.cursor = "";
      document.body.style.userSelect = "";
      window.removeEventListener("pointermove", move);
      window.removeEventListener("pointerup", stop);
      window.removeEventListener("pointercancel", stop);
      onEnd?.();
    };

    window.addEventListener("pointermove", move);
    window.addEventListener("pointerup", stop);
    window.addEventListener("pointercancel", stop);
  };

  const startResize = (event, direction) => {
    if (!isPreviewVisible()) return;

    const startX = event.clientX;
    const startY = event.clientY;
    const startRect = getWindowRect();

    if (isPreviewDocked()) {
      if (direction !== "n") return;
      beginPointerDrag(
        event,
        "ns-resize",
        (moveEvent) => {
          applyDockedRect(startRect.height + startY - moveEvent.clientY);
        },
        () => applyDockedRect(getWindowRect().height, true)
      );
      return;
    }

    const cursor = {
      n: "ns-resize",
      ne: "nesw-resize",
      e: "ew-resize",
      se: "nwse-resize",
      s: "ns-resize",
      sw: "nesw-resize",
      w: "ew-resize",
      nw: "nwse-resize",
    }[direction] || "default";

    beginPointerDrag(
      event,
      cursor,
      (moveEvent) => applyRect(getResizeRect(direction, startRect, startX, startY, moveEvent)),
      () => applyRect(getWindowRect(), true)
    );
  };

  resizeDirections.forEach((direction) => {
    const handle = document.createElement("div");
    handle.className = `pz-preview-resize-handle pz-preview-resize-${direction}`;
    handle.dataset.pzPreviewResize = direction;
    handle.addEventListener("pointerdown", (event) => startResize(event, direction));
    previewContainer.appendChild(handle);
  });

  const animatePreviewWindow = () => {
    previewContainer.classList.add("pz-preview-animating");
    window.setTimeout(() => {
      previewContainer.classList.remove("pz-preview-animating");
    }, 150);
  };

  const toggleDockedPreview = () => {
    animatePreviewWindow();
    const currentRect = getWindowRect();
    if (isPreviewDocked()) {
      localStorage.setItem(storageKeyMode, "floating");
      applyRect(getInitialRect(), true);
      return;
    }

    applyRect(currentRect, true);
    localStorage.setItem(storageKeyMode, "docked");
    applyDockedRect(currentRect.height, true);
  };

  dragHandle?.addEventListener("pointerdown", (event) => {
    if (!isPreviewVisible()) return;

    const now = Date.now();
    const distance = Math.hypot(event.clientX - lastHandleClick.x, event.clientY - lastHandleClick.y);
    const isDoubleClick = now - lastHandleClick.time < 400 && distance < 8;
    lastHandleClick = { time: now, x: event.clientX, y: event.clientY };

    if (event.detail >= 2 || isDoubleClick) {
      event.preventDefault();
      suppressHandleDblClick = true;
      toggleDockedPreview();
      return;
    }

    const startX = event.clientX;
    const startY = event.clientY;
    const startRect = getWindowRect();

    if (isPreviewDocked()) {
      beginPointerDrag(
        event,
        "ns-resize",
        (moveEvent) => {
          applyDockedRect(startRect.height + startY - moveEvent.clientY);
        },
        () => applyDockedRect(getWindowRect().height, true)
      );
      return;
    }

    beginPointerDrag(
      event,
      "move",
      (moveEvent) => {
        applyRect({
          ...startRect,
          left: startRect.left + moveEvent.clientX - startX,
          top: startRect.top + moveEvent.clientY - startY,
        });
      },
      () => applyRect(getWindowRect(), true)
    );
  });

  dragHandle?.addEventListener("dblclick", (event) => {
    if (!isPreviewVisible()) return;

    event.preventDefault();
    if (suppressHandleDblClick) {
      suppressHandleDblClick = false;
      return;
    }
    toggleDockedPreview();
  });

  previewCheckbox.checked = localStorage.getItem(storageKeyChecked) === "true";
  applyPreviewState();

  previewCheckbox.addEventListener("change", () => {
    localStorage.setItem(storageKeyChecked, String(previewCheckbox.checked));
    applyPreviewState();
  });

  closeButton?.addEventListener("click", (event) => {
    event.preventDefault();
    event.stopPropagation();
    closeButton.blur();
    previewCheckbox.checked = false;
    localStorage.setItem(storageKeyChecked, "false");
    applyPreviewState();
  });

  modeButton?.addEventListener("click", (event) => {
    event.preventDefault();
    event.stopPropagation();
    modeButton.blur();
    if (!isPreviewVisible()) return;
    toggleDockedPreview();
  });

  window.addEventListener("resize", () => {
    if (!isPreviewVisible()) return;
    if (isPreviewDocked()) applyDockedRect(getWindowRect().height, true);
    else applyRect(getWindowRect(), true);
  });

  const menu = document.getElementById("adminmenuwrap");
  if (menu) new ResizeObserver(() => {
    if (!isPreviewVisible()) return;
    if (isPreviewDocked()) applyDockedRect(getWindowRect().height, true);
    else applyRect(getWindowRect(), true);
  }).observe(menu);
});
