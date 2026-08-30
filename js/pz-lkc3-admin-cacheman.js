/* Pz-LinkCard3 cache manager screen
 * Made by Poporon / Refactoring by ChatGPT
 */

document.addEventListener("DOMContentLoaded", () => {

    window.PzLkc3Admin?.showLoadedControls();

    // ====== Utilities ======
    const admin = window.PzLkc3Admin || {};
    const $ = admin.$ || ((s) => document.querySelector(s));
    const $all = admin.$all || ((s) => Array.from(document.querySelectorAll(s)));
    const on = admin.on || ((el, evt, fn) => el && el.addEventListener(evt, fn));
    const labels = window.pz_lkc3_cacheman_options?.labels || {};

    const initScrollPosition = () => {
        admin.initScrollPosition?.({
            forceTopWhen: () => !!document.querySelector(".pz-man-cache-dirty-check"),
        });
    };
    initScrollPosition();

    const initCharacterCounts = () => {
        $all("[data-pz-character-count-for]").forEach((counter) => {
            const target = document.getElementById(counter.dataset.pzCharacterCountFor || "");
            if (!target) return;

            const template = counter.dataset.pzCharacterCountTemplate || "%s characters";
            const formatter = new Intl.NumberFormat(document.documentElement.lang || undefined);
            const update = () => {
                const count = Array.from(target.value || "").length;
                counter.textContent = template.replace("%s", formatter.format(count));
            };

            update();
            target.addEventListener("input", update);
        });
    };
    initCharacterCounts();

    const initMediaButtons = () => {
        const buttons = $all(".pz-man-cache-media-button");
        if (!buttons.length || !window.wp?.media) return;

        let frame = null;
        let activeInput = null;

        const updatePreview = (input, url) => {
            const imageBox = input
                ?.closest(".pz-man-cache-image-box")
                ?.querySelector(".pz-man-cache-image-preview");
            if (!imageBox) return;

            imageBox.classList.remove("pz-man-cache-image-empty");
            imageBox.innerHTML = "";

            const link = document.createElement("a");
            link.href = url;
            link.target = "_blank";
            link.rel = "noopener noreferrer";
            link.className = "pz-man-image-box-trigger";

            const frameInner = document.createElement("div");
            const img = document.createElement("img");
            img.src = url;
            img.alt = "";
            img.loading = "lazy";

            frameInner.appendChild(img);
            link.appendChild(frameInner);
            imageBox.appendChild(link);
        };

        buttons.forEach((button) => {
            button.addEventListener("click", (event) => {
                event.preventDefault();

                const targetName = button.dataset.pzMediaTarget;
                activeInput = targetName
                    ? $all("input[name]").find((input) => input.name === targetName)
                    : null;
                if (!activeInput) return;

                if (!frame) {
                    frame = window.wp.media({
                        title: labels.selectMedia || "Select Media",
                        button: {
                            text: labels.useMedia || "Use this media",
                        },
                        multiple: false,
                    });

                    frame.on("select", () => {
                        const attachment = frame.state().get("selection").first()?.toJSON();
                        const url = attachment?.url;
                        if (!activeInput || !url) return;

                        activeInput.value = url;
                        activeInput.dispatchEvent(new Event("input", { bubbles: true }));
                        activeInput.dispatchEvent(new Event("change", { bubbles: true }));
                        updatePreview(activeInput, url);
                    });
                }

                frame.open();
            });
        });
    };
    initMediaButtons();

    const initImportFileButton = () => {
        const input = $("#import_file");
        const button = $("#import_button");
        if (!input || !button) return;

        const update = () => {
            button.disabled = !(input.files && input.files.length > 0);
        };

        update();
        input.addEventListener("change", update);
    };
    initImportFileButton();

    const initImportHostButton = () => {
        const input = $("#import_host");
        const button = $("#import_host_button");
        if (!input || !button) return;

        const update = () => {
            button.disabled = !input.checked;
        };

        update();
        input.addEventListener("change", update);
    };
    initImportHostButton();

    const serializeFormValues = (form) => admin.serializeFormValues?.(form) || "";

    const initUnsavedEditorWarning = () => {
        const editor = $(".pz-man-cache-dirty-check");
        const form = editor?.closest("form");
        if (!form) return;

        const confirmMessage = labels.discardChanges || "Discard changes?";
        let isSubmitting = false;
        let initialState = serializeFormValues(form);
        let hasUnsavedChanges = false;

        const updateUnsavedChanges = () => {
            hasUnsavedChanges = serializeFormValues(form) !== initialState;
        };

        form.addEventListener("input", updateUnsavedChanges);
        form.addEventListener("change", updateUnsavedChanges);
        form.addEventListener("reset", () => {
            window.setTimeout(updateUnsavedChanges, 0);
        });
        form.addEventListener("submit", (event) => {
            const submitter = event.submitter;
            const isUpdate = submitter?.name === "action" && submitter?.value === "update";
            if (!isUpdate && hasUnsavedChanges && !window.confirm(confirmMessage)) {
                event.preventDefault();
                event.stopPropagation();
                return;
            }

            if (submitter?.classList.contains("pz-man-cache-reload-button")) {
                submitter.classList.add("is-spinning");
            }

            isSubmitting = true;
            initialState = serializeFormValues(form);
            hasUnsavedChanges = false;
        });

        window.addEventListener("pageshow", () => {
            isSubmitting = false;
        });

        window.addEventListener("beforeunload", (event) => {
            if (isSubmitting || !hasUnsavedChanges) return;

            event.preventDefault();
            event.returnValue = confirmMessage;
            return confirmMessage;
        });

    };
    initUnsavedEditorWarning();

    // ====== Screen Options ======
    const initScreenOptions = () => {
        const root = $(".pz-man-screen-options");
        const toggle = $("#pz-man-screen-options-toggle");
        const panel = $("#pz-man-screen-options-panel");
        if (!root || !toggle || !panel) return;

        const columns = {
            id: [".pz-man-head-card_id", ".pz-man-body-id"],
            excerpt: [".pz-man-head-excerpt", ".pz-man-body-excerpt-cell"],
            charset: [".pz-man-head-charset", ".pz-man-body-charset"],
            domain: [".pz-man-head-domain", ".pz-man-body-domain-cell"],
            sns: [".pz-man-head-sns_twitter", ".pz-man-body-sns"],
            regist_time: [".pz-man-head-regist_time", ".pz-man-body-regist-time"],
            update_time: [".pz-man-head-update_time", ".pz-man-body-update-time"],
            sns_time: [".pz-man-head-sns_time", ".pz-man-body-sns-time"],
            alive_time: [".pz-man-head-alive_time", ".pz-man-body-alive-time"],
            post_id: [".pz-man-head-use_post_id1", ".pz-man-body-post-id"],
            click_count: [".pz-man-head-click_count", ".pz-man-body-click-count"],
            result: [".pz-man-head-update_result", ".pz-man-body-result"],
        };

        const saveState = (state, perPage) => {
            const options = window.pz_lkc3_cacheman_options || {};
            if (!options.ajaxurl || !options.nonce) return Promise.resolve();

            const body = new URLSearchParams();
            body.set("action", "pz_lkc3_save_cacheman_columns");
            body.set("nonce", options.nonce);
            Object.entries(state).forEach(([column, visible]) => {
                body.set(`columns[${column}]`, visible ? "1" : "0");
            });
            if (perPage) body.set("per_page", perPage);

            return fetch(options.ajaxurl, {
                method: "POST",
                credentials: "same-origin",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
                },
                body: body.toString(),
            }).catch(() => {
                // Keep the current display when saving fails.
            });
        };

        const setPanelOpen = (open) => {
            panel.hidden = !open;
            toggle.setAttribute("aria-expanded", open ? "true" : "false");
            const icon = toggle.querySelector(".dashicons");
            if (icon) {
                icon.classList.toggle("dashicons-arrow-down-alt2", !open);
                icon.classList.toggle("dashicons-arrow-up-alt2", open);
            }
        };

        const applyColumn = (column, visible) => {
            (columns[column] || []).forEach((selector) => {
                document.querySelectorAll(selector).forEach((el) => {
                    el.classList.toggle("pz-man-column-hidden", !visible);
                });
            });
        };

        const state = {};
        panel.querySelectorAll(".pz-man-screen-column-toggle").forEach((checkbox) => {
            const column = checkbox.dataset.pzManColumn;
            const visible = checkbox.checked;
            state[column] = visible;
            applyColumn(column, visible);
            checkbox.addEventListener("change", () => {
                state[column] = checkbox.checked;
                applyColumn(column, checkbox.checked);
                saveState(state);
            });
        });

        const perPageSelect = $("#pz-man-screen-option-per-page");
        if (perPageSelect) {
            perPageSelect.addEventListener("change", () => {
                saveState(state, perPageSelect.value).finally(() => {
                    const form = perPageSelect.closest("form");
                    const pageNow = form?.querySelector('input[name="page_now"]');
                    if (pageNow) pageNow.value = "1";
                    if (form?.requestSubmit) {
                        form.requestSubmit();
                    } else {
                        form?.submit();
                    }
                });
            });
        }

        toggle.addEventListener("click", (e) => {
            e.preventDefault();
            setPanelOpen(panel.hidden);
        });

        document.addEventListener("click", (e) => {
            if (panel.hidden) return;
            if (root.contains(e.target)) return;
            setPanelOpen(false);
        });

        document.addEventListener("keydown", (e) => {
            if (e.key !== "Escape") return;
            if (panel.hidden) return;
            setPanelOpen(false);
            toggle.focus();
        });
    };
    initScreenOptions();

    // --- Processing overlay ---
    const overlayController = admin.initOverlay?.({
        skipClick: (trigger) => trigger.classList.contains("pz-man-cache-reload-button"),
    });

    // ====== Search box ======
    const input = $("#post-search-input");
    const searchSubmit = $("#search-submit");
    const pageSelector = $("#current-page-selector");
    const runIdSearch = (id) => {
        if (!input || !searchSubmit || !id) return;

        input.value = `ID:${id}`;
        input.dispatchEvent(new Event("input", { bubbles: true }));
        input.dispatchEvent(new Event("change", { bubbles: true }));

        const pageNow = input.form?.querySelector('input[name="page_now"]');
        if (pageNow) pageNow.value = "1";

        searchSubmit.click();
    };
    const openPage = (delta) => {
        if (!pageSelector) return;

        const page = parseInt(pageSelector.value, 10);
        if (!Number.isFinite(page)) return;

        const pageButton = $(`button[name="page_button"][value="${page + delta}"]:not(:disabled)`);
        pageButton?.click();
    };

    if (input && input.value.trim()) {
        input.focus();
        input.select();
    }

    pageSelector?.addEventListener("keydown", (e) => {
        if (e.key !== "Enter") return;
        overlayController?.showOverlayNow?.();
    });

    document.addEventListener("click", (e) => {
        const button = e.target?.closest?.(".pz-man-id-search");
        if (!button) return;

        e.preventDefault();
        runIdSearch(button.dataset.pzManSearchId);
    });

    // ====== Keyboard shortcuts ======
    const isTextEditingTarget = (target) =>
        !!target?.closest?.("input, textarea, select, [contenteditable='true']");

    document.addEventListener("keydown", (e) => {
        const key = e.key.toLowerCase();
        if (isTextEditingTarget(e.target)) return;

        if (e.ctrlKey && key === "a") {
            e.preventDefault();
            const selectAllCheckbox = $("#cb-select-all-1");
            if (selectAllCheckbox && !selectAllCheckbox.checked) selectAllCheckbox.click();
        }

        if (e.ctrlKey && key === "f") {
            e.preventDefault();
            input?.focus();
        }

        if (e.key === "F3") {
            e.preventDefault();
            if (input) input.value.trim() ? searchSubmit?.click() : input.focus();
        }

        if (e.ctrlKey && key === "g") {
            e.preventDefault();
            if (pageSelector) {
                pageSelector.focus();
                pageSelector.select?.();
            }
        }

        if (e.ctrlKey && !e.altKey && !e.metaKey && !e.shiftKey && e.key === "ArrowLeft") {
            e.preventDefault();
            openPage(-1);
        }

        if (e.ctrlKey && !e.altKey && !e.metaKey && !e.shiftKey && e.key === "ArrowRight") {
            e.preventDefault();
            openPage(1);
        }
    });

    // ====== Shared Shift + wheel handling for SELECT / RADIO / NUMBER / RANGE ======
    const dispatchInput = (el) => el.dispatchEvent(new Event("input", { bubbles: true }));
    const dispatchChange = (el) => el.dispatchEvent(new Event("change", { bubbles: true }));
    const isOutOfRange = (el, value) =>
        (el.min !== "" && value < parseFloat(el.min)) ||
        (el.max !== "" && value > parseFloat(el.max));

    const stepChoiceInput = (el, delta) => {
        const isSelect = el.tagName === "SELECT";
        const items = isSelect
            ? Array.from(el.options).filter((option) => !option.disabled)
            : Array.from(document.querySelectorAll(`input[type="radio"][name="${el.name}"]`))
                .filter((radio) => !radio.disabled);
        const currentIndex = isSelect
            ? items.findIndex((option) => option.value === el.value)
            : items.findIndex((radio) => radio === el || radio.checked);
        const newIndex = currentIndex + delta;

        if (newIndex < 0 || newIndex >= items.length) return;

        if (isSelect) {
            el.value = items[newIndex].value;
            dispatchChange(el);
        } else {
            items[newIndex].checked = true;
            items[newIndex].focus();
            dispatchChange(items[newIndex]);
        }
    };

    const stepNumericInput = (el, delta) => {
        const step = el.type === "range" ? 1 : parseFloat(el.step) || 1;
        const value = parseFloat(el.value) || 0;
        const newValue = value - delta * step;

        if (isOutOfRange(el, newValue)) return;

        el.value = newValue;
        dispatchInput(el);
    };

    const handleShiftWheel = (el, delta) => {
        if (!el) return;
        if (el.tagName === "SELECT" || el.type === "radio") return stepChoiceInput(el, delta);
        if (el.type === "number" || el.type === "range") return stepNumericInput(el, delta);
    };

    document.addEventListener("wheel", (e) => {
        if (!e.shiftKey) return;
        const el = e.target.closest("select, input[type='radio'], input[type='number'], input[type='range']");
        if (!el) return;
        e.preventDefault();
        handleShiftWheel(el, e.deltaY > 0 ? 1 : -1);
    }, { passive: false });

    // ====== Infobar / overlay positioning ======
    const infobar = $("#pz-infobar");
    if (infobar) {
        let adjustInfobarFrame = null;
        const adjustInfobar = () => {
            const adminBarBottom = getAdminBarBottom();
            document.body.classList.toggle("pz-has-wpadminbar", adminBarBottom > 0);
            document.body.classList.toggle("pz-no-wpadminbar", adminBarBottom <= 0);
            infobar.style.left = `${Math.max(0, getContentLeft())}px`;
            infobar.style.top = `${adminBarBottom}px`;
            adjustOverlay();
        };
        const requestAdjustInfobar = () => {
            if (adjustInfobarFrame) return;
            adjustInfobarFrame = window.requestAnimationFrame(() => {
                adjustInfobarFrame = null;
                adjustInfobar();
            });
        };
        adjustInfobar();
        on(window, "resize", requestAdjustInfobar);
        on(window, "scroll", requestAdjustInfobar);
        document.addEventListener("scroll", requestAdjustInfobar, true);
        window.visualViewport?.addEventListener("scroll", requestAdjustInfobar);
        window.visualViewport?.addEventListener("resize", requestAdjustInfobar);

        const observer = new MutationObserver(requestAdjustInfobar);
        observer.observe(document.body, { attributes: true, attributeFilter: ["class"] });
        const menuWrap = $("#adminmenuwrap");
        if (menuWrap) observer.observe(menuWrap, { attributes: true, attributeFilter: ["class"] });
    }

});
