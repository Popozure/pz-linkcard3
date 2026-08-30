/* ===== Settings controls ===== */

/* Pz-LinkCard3 settings screen
 * Made by Poporon / Refactoring by ChatGPT
 */
document.addEventListener("DOMContentLoaded", () => {

  window.PzLkc3Admin?.showLoadedControls();

  const SCROLL_TOP_THRESHOLD = 90;

  // --- Utilities ---
  const admin = window.PzLkc3Admin || {};
  const $ = admin.$ || ((sel) => document.querySelector(sel));
  const $all = admin.$all || ((sel) => Array.from(document.querySelectorAll(sel)));
  const on = admin.on || ((el, ev, fn) => el && el.addEventListener(ev, fn));
  const labels = window.lkc3_ajax_preview?.labels || {};
  const emit = (el, type) => el.dispatchEvent(new Event(type, { bubbles: true }));
  const emitInputAndChange = (el) => {
    emit(el, "input");
    emit(el, "change");
  };

  // --- Processing overlay ---
  const overlayControls = admin.initOverlay?.({
    triggerSelector: "button, input[type='submit'], input[type='button']",
    requireSubmitter: true,
    honorFormNoValidate: false,
    skipClick: (trigger) => {
      if (trigger.closest(".pz-settings") && trigger.type !== "submit") return true;
      return isSettingsSaveSubmitter(trigger);
    },
    skipSubmit: (submitter) => {
      if (submitter.closest(".pz-settings") && submitter.type !== "submit") return true;
      return isSettingsSaveSubmitter(submitter);
    },
  }) || {};
  const hideOverlay = overlayControls.hideOverlay || (() => {});
  const isSettingsSaveSubmitter = (el) =>
    !!el?.closest?.(".pz-settings") &&
    el?.type === "submit" &&
    (el.name === "submit" || el.id === "submit");

  // Warn before leaving the settings page when form values have unsaved changes.
  const initUnsavedChangesWarning = () => {
    const form = $("#pz-settings-form");
    if (!form) return;

    const confirmMessage = labels.discardChanges || "Discard changes?";
    let isSubmittingSettings = false;

    const serializeSettingsForm = () => admin.serializeFormValues?.(form) || "";

    let initialState = serializeSettingsForm();
    let hasUnsavedChanges = false;

    const updateUnsavedChanges = () => {
      hasUnsavedChanges = serializeSettingsForm() !== initialState;
    };

    form.addEventListener("input", updateUnsavedChanges);
    form.addEventListener("change", updateUnsavedChanges);
    form.addEventListener("reset", () => {
      window.setTimeout(updateUnsavedChanges, 0);
    });
    form.addEventListener("submit", () => {
      isSubmittingSettings = true;
      initialState = serializeSettingsForm();
      hasUnsavedChanges = false;
    });

    window.addEventListener("pageshow", () => {
      isSubmittingSettings = false;
    });

    window.addEventListener("beforeunload", (event) => {
      if (isSubmittingSettings || !hasUnsavedChanges) return;

      event.preventDefault();
      event.returnValue = confirmMessage;
      return confirmMessage;
    });
  };
  initUnsavedChangesWarning();

  const initDbClearDelay = () => {
    const checkbox = $(".pz-db-clear-enabled");
    const button = $(".pz-db-clear");
    if (!checkbox || !button) return;

    const delayMs = 5000;
    let timer = null;
    let startedAt = 0;

    const setProgress = (progress) => {
      button.style.setProperty("--pz-db-clear-progress", `${Math.max(0, Math.min(100, progress))}%`);
    };

    const reset = () => {
      if (timer) window.cancelAnimationFrame(timer);
      timer = null;
      startedAt = 0;
      button.disabled = true;
      button.dataset.ready = "0";
      button.classList.remove("pz-db-clear-ready");
      setProgress(0);
    };

    const tick = (now) => {
      const progress = ((now - startedAt) / delayMs) * 100;
      setProgress(progress);
      if (progress >= 100) {
        timer = null;
        button.disabled = false;
        button.dataset.ready = "1";
        button.classList.add("pz-db-clear-ready");
        setProgress(100);
        return;
      }
      timer = window.requestAnimationFrame(tick);
    };

    const start = () => {
      reset();
      startedAt = performance.now();
      timer = window.requestAnimationFrame(tick);
    };

    checkbox.addEventListener("change", () => {
      if (checkbox.checked) {
        start();
      } else {
        reset();
      }
    });

    button.addEventListener("click", (e) => {
      if (button.dataset.ready === "1") return;
      e.preventDefault();
      e.stopImmediatePropagation();
    }, true);

    button.form?.addEventListener("submit", (e) => {
      if (e.submitter !== button || button.dataset.ready === "1") return;
      e.preventDefault();
      e.stopImmediatePropagation();
    });

    reset();
  };
  initDbClearDelay();

  $all("button, input[type='submit'], input[type='button']").forEach((el) => {
    const original = el.onclick;
    if (!original || !original.toString().includes("confirm(")) return;

    el.onclick = (e) => {
      const result = original.call(el, e);
      if (result === false) hideOverlay();
      return result;
    };
  });

  const variableListStorageKey = "pz-lkc3-variable-list-visible";
  const getVariableLists = () => $all(".pz-variable-list");
  const setVariableListVisible = (visible) => {
    getVariableLists().forEach((el) => {
      el.classList.toggle("pz-hidden", !visible);
    });
    try {
      localStorage.setItem(variableListStorageKey, visible ? "1" : "0");
    } catch (e) {
      // Ignore storage errors in restricted browser modes.
    }
  };
  const restoreVariableListVisible = () => {
    try {
      if (localStorage.getItem(variableListStorageKey) === "1") {
        setVariableListVisible(true);
      }
    } catch (e) {
      // Ignore storage errors in restricted browser modes.
    }
  };
  restoreVariableListVisible();

  $all(".pz-variable-list-close").forEach((button) => {
    on(button, "click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      setVariableListVisible(false);
    });
    on(button, "mousedown", (e) => e.stopPropagation());
    on(button, "touchstart", (e) => e.stopPropagation());
  });

  document.addEventListener("keydown", (e) => {
    if (e.shiftKey && e.key === "F12") {
      const variableLists = getVariableLists();
      if (!variableLists.length) return;

      e.preventDefault();
      const visible = Array.from(variableLists).every((el) => el.classList.contains("pz-hidden"));
      setVariableListVisible(visible);
      window.dispatchEvent(new Event("resize"));
      return;
    }

    if ((e.ctrlKey || e.altKey) && e.key?.toLowerCase() === "s") {
      e.preventDefault();
      const btn = $("#submit");
      if (btn) btn.click();
      else console.warn("#submit was not found.");
    }
  });

  const initVariableListDrag = () => {
    const getVariableListBounds = (list) => {
      const infobar = $("#pz-infobar");
      const minTop = Math.max(0, infobar ? infobar.getBoundingClientRect().bottom : 0);
      const minLeft = Math.max(0, getContentLeft());
      const maxLeft = Math.max(minLeft, window.innerWidth - list.offsetWidth);
      const maxTop = Math.max(minTop, window.innerHeight - list.offsetHeight);

      return { minLeft, minTop, maxLeft, maxTop };
    };

    const clampVariableListPosition = (list, left, top) => {
      const bounds = getVariableListBounds(list);
      return {
        left: Math.min(bounds.maxLeft, Math.max(bounds.minLeft, left)),
        top: Math.min(bounds.maxTop, Math.max(bounds.minTop, top)),
      };
    };

    const setVariableListPosition = (list, left, top) => {
      const pos = clampVariableListPosition(list, left, top);
      list.style.left = `${pos.left}px`;
      list.style.top = `${pos.top}px`;
    };

    $all(".pz-variable-list").forEach((list) => {
      let startX = 0;
      let startY = 0;
      let startLeft = 0;
      let startTop = 0;

      const move = (event) => {
        setVariableListPosition(
          list,
          startLeft + event.clientX - startX,
          startTop + event.clientY - startY
        );
      };

      const stop = (event) => {
        list.releasePointerCapture?.(event.pointerId);
        window.removeEventListener("pointermove", move);
        window.removeEventListener("pointerup", stop);
        window.removeEventListener("pointercancel", stop);
      };

      list.addEventListener("pointerdown", (event) => {
        if (event.target !== list) return;

        event.preventDefault();
        startX = event.clientX;
        startY = event.clientY;
        startLeft = list.getBoundingClientRect().left;
        startTop = list.getBoundingClientRect().top;
        list.setPointerCapture?.(event.pointerId);
        window.addEventListener("pointermove", move);
        window.addEventListener("pointerup", stop);
        window.addEventListener("pointercancel", stop);
      });

      const resetPosition = () => {
        const rect = list.getBoundingClientRect();
        const hasPosition = list.style.left !== "" || list.style.top !== "";
        const bounds = getVariableListBounds(list);
        setVariableListPosition(
          list,
          hasPosition ? rect.left : bounds.minLeft,
          hasPosition ? rect.top : bounds.minTop
        );
      };

      resetPosition();
      on(window, "resize", resetPosition);

      const observer = new MutationObserver(resetPosition);
      observer.observe(document.body, { attributes: true, attributeFilter: ["class"] });
      const menuWrap = $("#adminmenuwrap");
      if (menuWrap) observer.observe(menuWrap, { attributes: true, attributeFilter: ["class", "style"] });
      const infobar = $("#pz-infobar");
      if (infobar) observer.observe(infobar, { attributes: true, attributeFilter: ["class", "style"] });
    });
  };
  initVariableListDrag();

  const initTopButton = () => {
    const topBtn = $(".pz-button-top");
    const indicator = $(".pz-indicator");
    if (!topBtn || !indicator) return;

    const updateIndicator = () => {
      indicator.style.opacity =
        window.scrollY > SCROLL_TOP_THRESHOLD ? "1" : "0";
    };

    updateIndicator();
    on(window, "scroll", updateIndicator);
    on(topBtn, "click", () => window.scrollTo({ top: 0, behavior: "smooth" }));
  };
  initTopButton();

  $all(".pz-shortcode-1").forEach((el) => {
    on(el, "keyup", (e) => {
      const val = e.target.value;
      $all(".pz-shortcode-copy").forEach((x) => (x.textContent = val));
      $all(".pz-shortcode-enabled").forEach(
        (x) => (x.disabled = val.length === 0)
      );
    });
  });

  ["code1", "code2", "code3", "code4"].forEach((code) => {
    const el = $(`input[name="properties[${code}]"]`);
    on(el, "keydown", (e) => {
      if (e.key === " ") e.preventDefault();
    });
  });

  const widthInput = $(`input[name="properties[width]"]`);
  const widthUnit = $(`select[name="properties[width-unit]"]`);
  on(widthInput, "keydown", (e) => {
    if (e.ctrlKey || e.altKey || e.metaKey) return;

    const key = e.key?.toLowerCase();
    const unit = e.key === "%" ? "%" : key === "p" ? "px" : "";
    if (!unit || !widthUnit) return;

    e.preventDefault();
    if (widthUnit.value === unit) return;

    widthUnit.value = unit;
    emitInputAndChange(widthUnit);
  });

  $all(".pz-cron-all").forEach((el) => {
    on(el, "change", (e) => {
      $all(".pz-cron-list-other").forEach((row) => {
        const visible = e.target.checked;
        row.style.display = visible ? "table-row" : "none";
        row.classList.toggle("pz-show", visible);
        row.classList.toggle("pz-hide", !visible);
      });
    });
  });

  $all(".pz-click-all-select").forEach((el) => {
    on(el, "click", (e) => {
      const target = e.target;
      if (target.tagName === "INPUT") return target.select();
      if (target.tagName === "DIV") {
        const range = document.createRange();
        range.selectNodeContents(target);
        const sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);
      }
    });
  });

  $all("input[type=checkbox]").forEach((el) => {
    on(el, "click", (e) => {
      if (e.target.readOnly) e.preventDefault();
    });
  });

  const syncSameNameFields = (e) => {
    if (!e.target.matches("input.pz-sync, select.pz-sync, textarea.pz-sync"))
      return;
    if (e.target.dataset.pzSyncing === "1") return;
    const name = e.target.name;
    const value = e.target.value;
    const checked = e.target.checked;
    const isCheckbox = e.target.matches("input[type='checkbox']");

    document.querySelectorAll(`[name="${name}"]`).forEach((el) => {
      if (el === e.target) return;
      if (isCheckbox) {
        if (!el.matches("input[type='checkbox']")) return;
        if (el.checked === checked) return;
        el.checked = checked;
        el.dataset.pzSyncing = "1";
        emitInputAndChange(el);
        delete el.dataset.pzSyncing;
        return;
      }
      if (el.matches("input[type='radio']")) {
        if (el.value === value) {
          if (el.checked) return;
          el.checked = true;
          el.dataset.pzSyncing = "1";
          emit(el, "change");
          delete el.dataset.pzSyncing;
        }
      } else {
        if (el.value === value) return;
        el.value = value;
        el.dataset.pzSyncing = "1";
        emit(el, "input");
        delete el.dataset.pzSyncing;
      }
    });
  };
  document.addEventListener("input", syncSameNameFields);
  document.addEventListener("change", syncSameNameFields);

  const uncheckSyncCheckboxes = (names) => {
    names.forEach((name) => {
      document
        .querySelectorAll(`input[type="checkbox"][name="${name}"]`)
        .forEach((el) => {
          if (!el.checked) return;
          el.checked = false;
          emitInputAndChange(el);
        });
    });
  };

  const checkSyncCheckboxes = (names) => {
    names.forEach((name) => {
      document
        .querySelectorAll(`input[type="checkbox"][name="${name}"]`)
        .forEach((el) => {
          if (el.checked) return;
          el.checked = true;
          emitInputAndChange(el);
        });
    });
  };

  document
    .querySelectorAll('input[type="checkbox"][name="properties[debug-mode]"]')
    .forEach((el) => {
      el.addEventListener("change", () => {
        if (el.checked) return;
        uncheckSyncCheckboxes([
          "properties[additional-mode]",
          "properties[log-mode]",
          "properties[admin-mode]",
        ]);
      });
    });

  [
    "properties[additional-mode]",
    "properties[log-mode]",
    "properties[admin-mode]",
  ].forEach((name) => {
    document
      .querySelectorAll(`input[type="checkbox"][name="${name}"]`)
      .forEach((el) => {
        el.addEventListener("change", () => {
          if (!el.checked) return;
          checkSyncCheckboxes(["properties[debug-mode]"]);
        });
      });
  });

  document
    .querySelectorAll('input[type="checkbox"][name="properties[admin-mode]"]')
    .forEach((el) => {
      el.addEventListener("change", () => {
        if (!el.checked) return;
        checkSyncCheckboxes(["properties[additional-mode]"]);
      });
    });

  document
    .querySelectorAll(
      'input[type="checkbox"][name="properties[additional-mode]"]'
    )
    .forEach((el) => {
      el.addEventListener("change", () => {
        if (el.checked) return;
        uncheckSyncCheckboxes(["properties[admin-mode]"]);
      });
    });

  document.addEventListener("focusin", (e) => {
    const el = e.target;
    if (el.matches("input[type='radio']")) {
      const checked = document.querySelector(
        `input[name="${el.name}"]:checked`
      );
      el.dataset.before = checked?.value || "";
    } else if (el.matches("input, select, textarea")) {
      el.dataset.before = el.value;
    }
  });

  document.addEventListener("keydown", (e) => {
    if (e.key !== "Escape") return;
    hideOverlay();
    const el = document.activeElement;
    if (!el) return;

    if (el.matches("input[type='radio']")) {
      const before = el.dataset.before;
      if (before === undefined) return;
      document.querySelectorAll(`input[name="${el.name}"]`).forEach((r) => {
        r.checked = r.value === before;
      });
      emit(el, "change");
      return;
    }

    if (el.matches("select")) {
      if (el.dataset.before !== undefined) {
        el.value = el.dataset.before;
        emit(el, "change");
      }
    } else if (el.matches("input, textarea")) {
      if (el.dataset.before !== undefined) {
        el.value = el.dataset.before;
        emit(el, "input");
      }
    }
  });

  const setDisabled = (selector, disabled, readonly = false, color = null) => {
    $all(selector).forEach((el) => {
      el.disabled = disabled;
      el.readOnly = readonly;
      if (color !== null && el.parentElement) {
        el.parentElement.style.color = color;
      }
    });
  };

  const switchEnabled = () => {
    const inGet = $(`select[name='properties[in-content-get]']`)?.value;
    setDisabled("input[name='properties[in-content-title]']", inGet != "3");
    setDisabled("input[name='properties[in-content-excerpt]']", inGet != "3");

    const exThumb = $(`select[name='properties[ex-thumbnail-get]']`)?.value;
    setDisabled(
      "select[name='properties[ex-thumbnail-size]']",
      !(exThumb === "1" || exThumb === "13")
    );

    const inThumb = $(`select[name='properties[in-thumbnail-get]']`)?.value;
    setDisabled(
      "select[name='properties[in-thumbnail-size]']",
      !(inThumb === "1" || inThumb === "13")
    );

  };
  switchEnabled();

  $all("select[name^='properties[']").forEach((el) =>
    on(el, "change", switchEnabled)
  );
  $all("input[type='checkbox'][name^='properties[']").forEach((el) =>
    on(el, "change", switchEnabled)
  );

  const infobar = $("#pz-infobar");
  if (infobar) {
      let adjustInfobarFrame = null;
      let followInfobarFrame = null;
      let followInfobarTimer = null;
      const adjustInfobar = (skipOverlay = false) => {
          const adminBarBottom = getAdminBarBottom();
          const rootStyle = document.documentElement.style;
          document.body.classList.toggle("pz-has-wpadminbar", adminBarBottom > 0);
          document.body.classList.toggle("pz-no-wpadminbar", adminBarBottom <= 0);
          infobar.style.left = `${Math.max(0, getContentLeft())}px`;
          infobar.style.top = `${adminBarBottom}px`;
          rootStyle.setProperty("--pz-infobar-bottom", `${Math.max(0, infobar.getBoundingClientRect().bottom)}px`);
          const tabbarWrapper = $("#pz-tabbar-wrapper");
          if (tabbarWrapper) {
              rootStyle.setProperty("--pz-tabbar-bottom", `${Math.max(0, tabbarWrapper.getBoundingClientRect().bottom)}px`);
          }
          if (!skipOverlay) adjustOverlay();
      };
      const requestAdjustInfobar = () => {
          if (adjustInfobarFrame) return;
          adjustInfobarFrame = window.requestAnimationFrame(() => {
              adjustInfobarFrame = null;
              adjustInfobar();
          });
      };
      const stopFollowInfobar = () => {
          if (followInfobarFrame) window.cancelAnimationFrame(followInfobarFrame);
          followInfobarFrame = null;
          followInfobarTimer = null;
          requestAdjustInfobar();
      };
      const followInfobar = () => {
          adjustInfobar(true);
          followInfobarFrame = window.requestAnimationFrame(followInfobar);
      };
      const startFollowInfobar = () => {
          if (!followInfobarFrame) followInfobarFrame = window.requestAnimationFrame(followInfobar);
          if (followInfobarTimer) window.clearTimeout(followInfobarTimer);
          followInfobarTimer = window.setTimeout(stopFollowInfobar, 200);
      };
      adjustInfobar();
      on(window, "resize", requestAdjustInfobar);
      window.addEventListener("scroll", startFollowInfobar, { passive: true });
      document.addEventListener("scroll", startFollowInfobar, { capture: true, passive: true });
      window.addEventListener("wheel", startFollowInfobar, { passive: true });
      window.addEventListener("touchmove", startFollowInfobar, { passive: true });
      window.visualViewport?.addEventListener("scroll", startFollowInfobar, { passive: true });
      window.visualViewport?.addEventListener("resize", requestAdjustInfobar);

      const observer = new MutationObserver(requestAdjustInfobar);
      observer.observe(document.body, { attributes: true, attributeFilter: ["class"] });
      const menuWrap = $("#adminmenuwrap");
      if (menuWrap) observer.observe(menuWrap, { attributes: true, attributeFilter: ["class"] });
      const tabbarWrapper = $("#pz-tabbar-wrapper");
      if (window.ResizeObserver) {
          const resizeObserver = new ResizeObserver(requestAdjustInfobar);
          resizeObserver.observe(infobar);
          if (tabbarWrapper) resizeObserver.observe(tabbarWrapper);
      }
  }

  const settingsRoot = document.getElementById("pz-settings");
  const rules = [
    { checkbox: "properties[debug-mode]", targetClass: ".pz-debug-only", rootClass: "pz-debug-mode-enabled" },
    { checkbox: "properties[additional-mode]", targetClass: ".pz-additional-only", rootClass: "pz-additional-mode-enabled" },
    { checkbox: "properties[log-mode]", targetClass: ".pz-log-only", rootClass: "pz-log-mode-enabled" },
    { checkbox: "properties[admin-mode]", targetClass: ".pz-admin-only", rootClass: "pz-admin-mode-enabled" },
    { checkbox: "properties[multi-mode]", targetClass: ".pz-multi-only", rootClass: "pz-multi-mode-enabled" },
    { checkbox: "properties[develop-mode]", targetClass: ".pz-develop-only", rootClass: "pz-develop-mode-enabled" },
  ];
  rules.forEach((rule) => {
    const checkboxes = document.querySelectorAll(
      `input[type="checkbox"][name="${rule.checkbox}"]`
    );
    if (!checkboxes.length) return;

    const toggleElements = () => {
      const checked = Array.from(checkboxes).some((el) => el.checked);
      settingsRoot?.classList.toggle(rule.rootClass, checked);
      const elements = document.querySelectorAll(rule.targetClass);
      elements.forEach((el) => {
        el.classList.toggle("pz-hidden", !checked);
      });
    };

    checkboxes.forEach((cb) => cb.addEventListener("change", toggleElements));
  });

  const enabledGroups = new Map();
  document
    .querySelectorAll('input[type="checkbox"][class$="-enabled"]')
    .forEach((cb) => {
      const enabledClass = Array.from(cb.classList).find((cls) =>
        cls.endsWith("-enabled")
      );
      if (!enabledClass) return;

      const targetClass = enabledClass.replace(/-enabled$/, "");
      if (!enabledGroups.has(targetClass)) {
        enabledGroups.set(targetClass, []);
      }
      enabledGroups.get(targetClass).push(cb);
    });

  enabledGroups.forEach((cbs, targetClass) => {
    const targetSelector = `.${targetClass}`;
    const syncTarget = () => {
      const enabled = cbs.some((cb) => cb.checked);
      document.querySelectorAll(targetSelector).forEach((el) => {
        if (el.matches("input, select, textarea, button")) {
          el.disabled = !enabled;
        }
        el.classList.toggle("pz-disabled", !enabled);
      });
    };

    syncTarget();
    cbs.forEach((cb) => cb.addEventListener("change", syncTarget));
  });

  document
    .querySelectorAll(".pz-toggle-button.pz-enabled input[type='checkbox'], .pz-enabled input[type='checkbox']")
    .forEach((cb) => {
      const update = () => {
        const td = cb.closest("td");
        if (!td) return;

        const lock = !cb.checked;

        td.querySelectorAll(".pz-items:not(:has(.pz-toggle-button.pz-enabled)) input, .pz-items:not(:has(.pz-toggle-button.pz-enabled)) select, .pz-items:not(:has(.pz-toggle-button.pz-enabled)) textarea, .pz-items:not(:has(.pz-toggle-button.pz-enabled)) button, .pz-items:not(:has(.pz-toggle-button.pz-enabled)) label").forEach((el) => {
          if (el === cb) return;

          el.classList.toggle("pz-disabled", lock);

          if (el instanceof HTMLInputElement || el instanceof HTMLTextAreaElement) {
            if (
              el instanceof HTMLInputElement &&
              (el.type === "checkbox" || el.type === "radio" || el.type === "file")
            ) {
              el.setAttribute("aria-disabled", lock ? "true" : "false");
            } else {
              el.readOnly = lock;
              el.setAttribute("aria-disabled", lock ? "true" : "false");
            }
          }

          if (el instanceof HTMLSelectElement || el instanceof HTMLButtonElement) {
            el.setAttribute("aria-disabled", lock ? "true" : "false");
          }

          if (lock) {
            if (!el.hasAttribute("data-tabindex")) {
              const prev = el.getAttribute("tabindex");
              el.setAttribute("data-tabindex", prev == null ? "" : prev);
            }
            el.setAttribute("tabindex", "-1");
          } else {
            if (el.hasAttribute("data-tabindex")) {
              const prev = el.getAttribute("data-tabindex");
              if (prev === "") el.removeAttribute("tabindex");
              else el.setAttribute("tabindex", prev);
              el.removeAttribute("data-tabindex");
            }
          }
        });

        window.pzLkc3SyncLivePreview?.();
      };

      cb.addEventListener("change", update);
      update();
    });

  document.addEventListener("beforeinput", (e) => {
    if (e.target.closest?.(".pz-disabled")) e.preventDefault();
  }, true);

  document.addEventListener("keydown", (e) => {
    const target = e.target.closest?.("input.pz-disabled, select.pz-disabled, textarea.pz-disabled, button.pz-disabled");
    if (!target) return;

    const allowedKeys = new Set(["Tab", "Shift", "Control", "Alt", "Meta", "Escape"]);
    if (!allowedKeys.has(e.key)) e.preventDefault();
  }, true);

  document.addEventListener("mousedown", (e) => {
    if (e.target.closest?.("input.pz-disabled, select.pz-disabled, textarea.pz-disabled, button.pz-disabled")) {
      e.preventDefault();
    }
  }, true);

  document.querySelectorAll('.pz-range-sign').forEach(range => {
    const update = () => {
      const min = Number(range.min);
      const max = Number(range.max);
      const val = Number(range.value);

      const percent = (val - min) / (max - min) * 100;
      const center = 50;

      if (percent > center) {
        range.style.background = `
          linear-gradient(to right,
            #e5e7eb 0%,
            #e5e7eb ${center}%,
            #0063b1 ${center}%,
            #0063b1 ${percent}%,
            #e5e7eb ${percent}%,
            #e5e7eb 100%
          )`;
      } else if (percent < center) {
        range.style.background = `
          linear-gradient(to right,
            #e5e7eb 0%,
            #e5e7eb ${percent}%,
            #ef4444 ${percent}%,
            #ef4444 ${center}%,
            #e5e7eb ${center}%,
            #e5e7eb 100%
          )`;
      } else {
        range.style.background = `
          linear-gradient(to right,
            #e5e7eb 0%,
            #e5e7eb 100%
          )`;
      }
    };

    range.addEventListener('input', update);
    update();
  });

  const checkbox = document.querySelector(
    'input[type="checkbox"][name="properties[debug-style-admin]"]'
  );
  if (checkbox) {
    const targets = document.querySelectorAll(".pz-settings *");

    const toggleDebugCSS = () => {
      targets.forEach((el) => {
        el.classList.toggle("pz-debug-css", checkbox.checked);
      });
    };

    checkbox.addEventListener("change", toggleDebugCSS);
  }

});


/* ===== Settings scroll position ===== */

document.addEventListener("DOMContentLoaded", () => {
    window.PzLkc3Admin?.initScrollPosition({
        formSelector: "#pz-settings-form",
    });
});
