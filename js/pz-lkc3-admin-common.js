/* Pz-LinkCard3 common admin helpers. */
(function () {
    "use strict";

    const admin = window.PzLkc3Admin || {};

    admin.$ = (selector, root = document) => root.querySelector(selector);
    admin.$all = (selector, root = document) => Array.from(root.querySelectorAll(selector));
    admin.on = (el, event, handler, options) => el && el.addEventListener(event, handler, options);

    admin.showLoadedControls = () => {
        const show = () => document.body.classList.add("pz-lkc3-page-loaded");
        if (document.readyState === "complete") show();
        else window.addEventListener("load", show, { once: true });
    };

    admin.serializeFormValues = (form) =>
        Array.from(form.elements)
            .filter((el) => el.name && !el.matches("button, input[type='button'], input[type='submit'], input[type='reset']"))
            .map((el) => {
                const type = (el.type || "").toLowerCase();
                if (type === "checkbox" || type === "radio") {
                    return `${el.name}:${type}:${el.value}:${el.checked ? "1" : "0"}`;
                }
                if (el.tagName === "SELECT" && el.multiple) {
                    return `${el.name}:select-multiple:${Array.from(el.selectedOptions).map((option) => option.value).join(",")}`;
                }
                return `${el.name}:${type}:${el.value}`;
            })
            .join("\n");

    admin.initScrollPosition = (options = {}) => {
        const scrollInput = document.querySelector(options.inputSelector || 'input[name="scroll-now"]');
        if (!scrollInput) return;

        if ("scrollRestoration" in history) {
            history.scrollRestoration = "manual";
        }

        const restoreScroll = (top) => {
            window.scrollTo({ top, behavior: "auto" });
        };

        if (options.forceTopWhen?.()) {
            [0, 50, 150, 350].forEach((delay) => window.setTimeout(() => restoreScroll(0), delay));
            window.addEventListener("load", () => restoreScroll(0), { once: true });
            window.addEventListener("pageshow", () => restoreScroll(0), { once: true });
            return;
        }

        const getScrollTop = () => window.scrollY || document.documentElement.scrollTop || 0;
        const syncScrollInput = () => {
            scrollInput.value = Math.max(0, Math.round(getScrollTop()));
        };
        const savedScroll = parseInt(scrollInput.value, 10);
        const form = options.form || scrollInput.form || (options.formSelector ? document.querySelector(options.formSelector) : null);

        window.addEventListener("scroll", syncScrollInput, { passive: true });
        form?.addEventListener("submit", syncScrollInput);

        if (!Number.isNaN(savedScroll) && savedScroll > 0) {
            [0, 50, 150, 350].forEach((delay) => {
                window.setTimeout(() => restoreScroll(savedScroll), delay);
            });
            window.addEventListener("load", () => restoreScroll(savedScroll), { once: true });
            window.addEventListener("pageshow", () => restoreScroll(savedScroll), { once: true });
        }
    };

    admin.initOverlay = (options = {}) => {
        const overlay = document.querySelector(options.overlaySelector || "#pz-overlay-proc");
        if (!overlay) return null;

        const triggerSelector = options.triggerSelector || "button, input[type='submit'], input[type='button'], a[href]";
        const targetAreaSelector = options.targetAreaSelector || ".pz-settings, .pz-man";
        const delay = Number.isFinite(options.delay) ? options.delay : 500;
        let overlayTimer = null;

        const isOverlayShown = () => window.getComputedStyle(overlay).display !== "none";
        const hideOverlay = () => {
            if (overlayTimer) {
                window.clearTimeout(overlayTimer);
                overlayTimer = null;
            }
            overlay.style.setProperty("display", "none", "important");
        };
        const getContentLeft = () => {
            const wpContent = document.querySelector("#wpcontent");
            if (wpContent) return wpContent.getBoundingClientRect().left;
            const menuWrap = document.querySelector("#adminmenuwrap");
            return menuWrap ? menuWrap.getBoundingClientRect().right : 0;
        };
        const getAdminBarBottom = () => {
            const adminBar = document.querySelector("#wpadminbar");
            if (!adminBar) return 0;

            const rect = adminBar.getBoundingClientRect();
            const visible = rect.height > 0 && window.getComputedStyle(adminBar).display !== "none";
            return visible ? Math.max(0, rect.bottom) : 0;
        };
        const adjustOverlay = () => {
            const infobar = document.querySelector("#pz-infobar");
            const top = infobar ? infobar.getBoundingClientRect().bottom : getAdminBarBottom();
            const left = getContentLeft();
            overlay.style.top = `${Math.max(0, top)}px`;
            overlay.style.left = `${Math.max(0, left)}px`;
            overlay.style.right = "0";
            overlay.style.bottom = "0";
            overlay.style.width = "auto";
            overlay.style.height = "auto";
        };
        const showOverlayNow = () => {
            adjustOverlay();
            overlay.style.removeProperty("display");
            overlay.style.setProperty("background-color", "rgba(0,0,0,0.2)");
            overlay.style.setProperty("display", "flex", "important");
        };
        const showOverlay = () => {
            if (overlayTimer) window.clearTimeout(overlayTimer);
            overlayTimer = window.setTimeout(() => {
                overlayTimer = null;
                showOverlayNow();
            }, delay);
        };
        const isOverlayTargetArea = (el) => !!el?.closest?.(targetAreaSelector);
        const shouldSkipFormValidation = (submitter) =>
            options.honorFormNoValidate !== false &&
            !!submitter &&
            (submitter.formNoValidate || submitter.hasAttribute?.("formnovalidate"));
        const isFormInvalidForSubmitter = (form, submitter = null) =>
            !!form?.checkValidity && !shouldSkipFormValidation(submitter) && !form.checkValidity();

        let isPointerOverOverlay = false;
        overlay.addEventListener("pointerenter", () => { isPointerOverOverlay = true; });
        overlay.addEventListener("pointerleave", () => { isPointerOverOverlay = false; });
        overlay.addEventListener("mouseover", () => { isPointerOverOverlay = true; });
        overlay.addEventListener("mouseout", () => { isPointerOverOverlay = false; });

        const hideOverlayByEsc = (e) => {
            if (e.key !== "Escape" && e.key !== "Esc") return;
            if (!isOverlayShown()) return;
            if (!isPointerOverOverlay && e.target !== overlay) return;
            hideOverlay();
        };
        const showOverlayClick = (e) => {
            if (e.defaultPrevented) return;

            const target = e.target?.nodeType === 1 ? e.target : e.target?.parentElement;
            const trigger = target?.closest?.(triggerSelector);
            if (!trigger) return;
            if (!isOverlayTargetArea(trigger)) return;
            if (trigger.matches("a[href]") && trigger.target === "_blank") return;
            if (options.skipClick?.(trigger, e, { hideOverlay })) return;

            const onclick = trigger.getAttribute("onclick") || "";
            if (onclick.includes("confirm(")) return;

            if (trigger.dataset?.downloadOverlay === "hide") {
                window.setTimeout(hideOverlay, 0);
                return;
            }
            if (trigger.dataset?.noOverlay === "1") return;

            if (trigger.type === "submit" && isFormInvalidForSubmitter(trigger.form, trigger)) {
                hideOverlay();
                return;
            }

            showOverlay();
        };
        const showOverlaySubmit = (e) => {
            if (e.defaultPrevented) return;

            const submitter = e.submitter;
            if (submitter) {
                if (!isOverlayTargetArea(submitter)) return;
                if (submitter.dataset?.noOverlay === "1") return;
                if (options.skipSubmit?.(submitter, e, { hideOverlay })) return;
            } else if (options.requireSubmitter || !isOverlayTargetArea(e.target)) {
                return;
            }

            if (isFormInvalidForSubmitter(e.target, submitter)) {
                hideOverlay();
                return;
            }

            showOverlay();
        };

        hideOverlay();
        window.addEventListener("load", hideOverlay);
        window.addEventListener("pageshow", hideOverlay);
        window.addEventListener("keydown", hideOverlayByEsc, true);
        window.addEventListener("keyup", hideOverlayByEsc, true);
        document.addEventListener("click", showOverlayClick);
        document.addEventListener("submit", showOverlaySubmit);
        document.addEventListener("invalid", hideOverlay, true);

        return { adjustOverlay, hideOverlay, showOverlay, showOverlayNow };
    };

    window.PzLkc3Admin = admin;
})();
