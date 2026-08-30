/* Pz-LinkCard3 cache manager image box */
document.addEventListener("DOMContentLoaded", () => {
  const CLICK_ZOOM = 5;
  const MAX_ZOOM = 10;
  const MIN_ZOOM = 1;
  const WHEEL_ZOOM_STEP = 0.5;

  const cacheman = document.querySelector(".pz-cacheman");
  if (!cacheman) return;

  const style = document.createElement("style");
  style.textContent = `
    .pz-image-box {
      position: fixed;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(0, 0, 0, 0.72);
      z-index: 1000;
      cursor: default;
      box-sizing: border-box;
      padding: 56px 28px 28px;
      overflow: auto;
    }
    .pz-image-box img {
      display: block;
      max-width: min(92vw, 100%);
      max-height: calc(100vh - 120px);
      object-fit: contain;
      background: #fff;
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
      cursor: zoom-in;
      transform-origin: center center;
      transition: transform 120ms ease;
      user-select: none;
    }
    .pz-image-box img.pz-image-box-zoomed {
      cursor: zoom-out;
    }
    .pz-image-box-close {
      position: absolute;
      top: 12px;
      right: 16px;
      width: 36px;
      height: 36px;
      border: 0;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.92);
      color: #111;
      font-size: 28px;
      line-height: 1;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0 0 2px;
    }
    .pz-image-box-close:hover,
    .pz-image-box-close:focus {
      background: #fff;
      outline: 2px solid #72aee6;
      outline-offset: 2px;
    }
  `;
  document.head.appendChild(style);

  let box = null;
  let observer = null;
  let zoom = 1;
  let activeImage = null;

  const clampZoom = (value) => Math.min(MAX_ZOOM, Math.max(MIN_ZOOM, value));

  const applyZoom = () => {
    if (!activeImage) return;
    activeImage.style.transform = `scale(${zoom})`;
    activeImage.classList.toggle("pz-image-box-zoomed", zoom > 1);
  };

  const setZoom = (value) => {
    zoom = clampZoom(value);
    applyZoom();
  };

  const handleKeydown = (e) => {
    if (e.key !== "Escape" && e.key !== "Esc") return;
    closeBox();
  };

  const getContentLeft = () => {
    const wpContent = document.querySelector("#wpcontent");
    if (wpContent) return wpContent.getBoundingClientRect().left;
    const menuWrap = document.querySelector("#adminmenuwrap");
    return menuWrap ? menuWrap.getBoundingClientRect().right : 0;
  };

  const positionBox = () => {
    if (!box) return;
    const adminBar = document.querySelector("#wpadminbar");
    const infobar = document.querySelector("#pz-infobar");
    const top = infobar
      ? infobar.getBoundingClientRect().bottom
      : adminBar
        ? adminBar.getBoundingClientRect().bottom
        : 0;
    const left = getContentLeft();
    box.style.top = `${Math.max(0, top)}px`;
    box.style.left = `${Math.max(0, left)}px`;
    box.style.right = "0";
    box.style.bottom = "0";
    box.style.width = "auto";
    box.style.height = "auto";
  };

  const closeBox = () => {
    if (!box) return;
    box.remove();
    box = null;
    activeImage = null;
    zoom = 1;
    window.removeEventListener("resize", positionBox);
    window.removeEventListener("scroll", positionBox, true);
    window.removeEventListener("keydown", handleKeydown);
    if (observer) {
      observer.disconnect();
      observer = null;
    }
  };

  const openBox = (src, alt) => {
    closeBox();

    box = document.createElement("div");
    box.className = "pz-image-box";

    const close = document.createElement("button");
    close.type = "button";
    close.className = "pz-image-box-close";
    close.setAttribute("aria-label", "Close");
    close.textContent = "\u00d7";

    const img = document.createElement("img");
    img.src = src;
    img.alt = alt || "";
    activeImage = img;
    zoom = 1;

    box.append(close, img);
    document.body.appendChild(box);
    positionBox();

    close.addEventListener("click", closeBox);
    box.addEventListener("click", closeBox);
    img.addEventListener("click", (e) => {
      e.stopPropagation();
      setZoom(zoom > MIN_ZOOM ? MIN_ZOOM : CLICK_ZOOM);
    });
    box.addEventListener("wheel", (e) => {
      if (!e.ctrlKey) return;
      e.preventDefault();
      e.stopPropagation();
      const delta = e.deltaY > 0 ? -WHEEL_ZOOM_STEP : WHEEL_ZOOM_STEP;
      setZoom(zoom + delta);
    }, { passive: false });

    window.addEventListener("resize", positionBox);
    window.addEventListener("scroll", positionBox, true);
    window.addEventListener("keydown", handleKeydown);

    observer = new MutationObserver(positionBox);
    observer.observe(document.body, { attributes: true, attributeFilter: ["class"] });
    const menuWrap = document.querySelector("#adminmenuwrap");
    if (menuWrap) {
      observer.observe(menuWrap, { attributes: true, attributeFilter: ["class", "style"] });
    }
  };

  cacheman.addEventListener("click", (e) => {
    const link = e.target.closest(".pz-man-thumbnail, .pz-man-image-box-trigger");
    if (!link || !cacheman.contains(link)) return;

    const img = link.querySelector("img");
    const src = link.getAttribute("href") || img?.src;
    if (!src) return;

    e.preventDefault();
    e.stopPropagation();
    openBox(src, img?.alt || "");
  });
});
