/* ===== Settings tabs ===== */

/* Pz-LinkCard3 settings tabs. */

document.addEventListener("DOMContentLoaded", () => {
    const MIN_SWIPE_DIST = 100;
    const SCROLL_TOP_THRESHOLD = 90;
    const SCROLL_MARGIN = 30;
    const DRAG_CLICK_THRESHOLD = 6;
    const TAB_SCROLL_STEP = 150;
    const LINKED_TABS = ["pz-external", "pz-internal"];

    const tabbar = document.getElementById("pz-tabbar");
    if (!tabbar) return;

    const leftBtn = document.querySelector(".pz-tab-left");
    const rightBtn = document.querySelector(".pz-tab-right");
    let lastFocusKey = "";
    let lastTabName = "";
    let suppressClick = false;
    let lastKeyboardTabMoveAt = 0;

    const isVisibleTab = (tab) => {
        const style = window.getComputedStyle(tab);
        return style.display !== "none" &&
            style.visibility !== "hidden" &&
            tab.getClientRects().length > 0;
    };
    const getTabs = () =>
        Array.from(document.querySelectorAll(".pz-tab"))
            .filter(isVisibleTab);

    const getTabName = (tab) => tab?.getAttribute("name") || "";
    const getActiveTabName = () => getTabName(document.querySelector(".pz-tab-active"));

    const setTabNow = (tabName) => {
        const tabNow = document.querySelector('input[name="tab-now"]');
        if (tabNow) tabNow.value = tabName;
    };

    const getWrappedIndex = (index, length) => {
        if (!length) return -1;
        return (index + length) % length;
    };

    const getCurrentTabIndex = (tabs) => {
        const activeTabName = getActiveTabName();
        if (activeTabName) lastTabName = activeTabName;
        if (!lastTabName) lastTabName = getTabName(tabs[0]);
        return tabs.findIndex((tab) => getTabName(tab) === lastTabName);
    };

    const adjustTabVisibility = (tab) => {
        const tabRect = tab.getBoundingClientRect();
        const barRect = tabbar.getBoundingClientRect();

        if (tabRect.left < barRect.left) {
            tabbar.scrollBy({
                left: tabRect.left - barRect.left - SCROLL_MARGIN,
                behavior: "smooth",
            });
        } else if (tabRect.right > barRect.right) {
            tabbar.scrollBy({
                left: tabRect.right - barRect.right + SCROLL_MARGIN,
                behavior: "smooth",
            });
        }
    };

    const updateButtons = () => {
        if (!leftBtn || !rightBtn) return;

        leftBtn.style.display = tabbar.scrollLeft <= 0 ? "none" : "block";
        rightBtn.style.display =
            tabbar.scrollLeft + tabbar.clientWidth >= tabbar.scrollWidth - 1
                ? "none"
                : "inline";
    };

    const focusTabField = (tab, tabName) => {
        if (LINKED_TABS.includes(tabName) && lastFocusKey) {
            const prefixMap = {
                "pz-external": ["ex-", "in-"],
                "pz-internal": ["in-", "ex-"],
            };

            for (const prefix of prefixMap[tabName]) {
                const target = document.querySelector(`[name="properties[${prefix}${lastFocusKey}]"]`);
                if (target) {
                    target.focus();
                    return;
                }
            }
        }

        if (tab.dataset.lastfocus) {
            document.querySelector(`[name="${tab.dataset.lastfocus}"]`)?.focus();
        }
    };

    const openTab = (tabName) => {
        if (!tabName) return;

        const tabs = getTabs();
        const tab = tabs.find((item) => getTabName(item) === tabName);
        if (!tab) {
            const fallbackTab = tabs.find((item) => getTabName(item) === "pz-basic") || tabs[0];
            if (fallbackTab && tabName !== getTabName(fallbackTab)) openTab(getTabName(fallbackTab));
            return;
        }

        tabs.forEach((item) => item.classList.remove("pz-tab-active"));
        document.querySelectorAll(".pz-page").forEach((page) => page.classList.remove("pz-page-active"));

        tab.classList.add("pz-tab-active");
        document.getElementById(tabName)?.classList.add("pz-page-active");

        const tabNameEl = document.querySelector(".pz-tab-name");
        if (tabNameEl) tabNameEl.textContent = tab.textContent;

        lastTabName = tabName;
        setTabNow(tabName);
        focusTabField(tab, tabName);
        adjustTabVisibility(tab);
        updateButtons();
    };

    const moveTab = (direction, focusTab = false) => {
        const tabs = getTabs();
        if (!tabs.length) return;

        const currentIndex = getCurrentTabIndex(tabs);
        if (currentIndex === -1) return;

        const nextTab = tabs[getWrappedIndex(currentIndex + direction, tabs.length)];
        if (!nextTab) return;

        openTab(getTabName(nextTab));
        if (focusTab) nextTab.focus();
    };

    window.PzLkc3SettingsTabs = {
        openTab,
        moveTab,
        getActiveTabName,
    };

    getTabs().forEach((tab) => {
        tab.addEventListener("click", (event) => {
            if (suppressClick) {
                event.preventDefault();
                return;
            }

            event.preventDefault();
            openTab(getTabName(tab));
        });
    });

    tabbar.addEventListener(
        "wheel",
        (event) => {
            event.preventDefault();
            moveTab(event.deltaY > 0 ? 1 : -1);
        },
        { passive: false }
    );

    const savedTab = document.querySelector('input[name="tab-now"]')?.value;
    const tabs = getTabs();
    openTab(savedTab || getTabName(tabs[0]));

    document.querySelectorAll("[name^='properties[']").forEach((el) => {
        el.addEventListener("focus", function () {
            const match = this.name.match(/^properties\[(.+?)\]$/);
            if (!match) return;

            const parts = match[1].split("-");
            if (parts.length <= 1) return;

            const activeTab = document.querySelector(".pz-tab-active");
            if (!activeTab) return;

            const activeName = getTabName(activeTab);
            if (LINKED_TABS.includes(activeName)) {
                lastFocusKey = parts.slice(1).join("-");
            } else {
                activeTab.dataset.lastfocus = this.name;
            }
        });
    });

    document.addEventListener("keydown", (event) => {
        if (!["ArrowLeft", "ArrowRight"].includes(event.key)) return;

        const shouldMove = event.ctrlKey || document.activeElement?.classList?.contains("pz-tab");
        if (!shouldMove) return;

        event.preventDefault();
        event.stopImmediatePropagation();
        const now = Date.now();
        if (event.repeat && now - lastKeyboardTabMoveAt < 30) return;
        lastKeyboardTabMoveAt = now;
        moveTab(event.key === "ArrowRight" ? 1 : -1, !event.ctrlKey);
    });

    let touchStartX = 0;
    let touchStartY = 0;
    let touchSwipeEnabled = false;

    const isPageSwipeTarget = (target, clientY) => {
        const tabbarWrapper = document.getElementById("pz-tabbar-wrapper");
        const tabbarBottom = tabbarWrapper
            ? tabbarWrapper.getBoundingClientRect().bottom
            : 0;

        if (clientY <= tabbarBottom) return false;
        if (target.closest("#pz-infobar, #pz-tabbar-wrapper, .pz-submit-float, #pz-preview-container")) return false;
        return !!target.closest(".pz-page-active");
    };

    document.addEventListener("touchstart", (event) => {
        touchSwipeEnabled = false;
        if (event.touches.length !== 1) return;

        const touch = event.touches[0];
        touchSwipeEnabled = isPageSwipeTarget(event.target, touch.clientY);
        touchStartX = touch.screenX;
        touchStartY = touch.screenY;
    });

    document.addEventListener("touchend", (event) => {
        if (!touchSwipeEnabled) return;
        touchSwipeEnabled = false;
        if (!event.changedTouches?.length) return;

        const endX = event.changedTouches[0].screenX;
        const endY = event.changedTouches[0].screenY;
        if (Math.abs(endY - touchStartY) > SCROLL_TOP_THRESHOLD) return;

        const distX = endX - touchStartX;
        if (Math.abs(distX) > MIN_SWIPE_DIST) {
            moveTab(distX < 0 ? 1 : -1);
        }
    });
    document.addEventListener("touchcancel", () => {
        touchSwipeEnabled = false;
    });

    if (leftBtn && rightBtn) {
        leftBtn.addEventListener("click", () => tabbar.scrollBy({ left: -TAB_SCROLL_STEP, behavior: "smooth" }));
        rightBtn.addEventListener("click", () => tabbar.scrollBy({ left: TAB_SCROLL_STEP, behavior: "smooth" }));
        window.addEventListener("load", updateButtons);
        window.addEventListener("resize", updateButtons);
        tabbar.addEventListener("scroll", updateButtons);
        updateButtons();
    }

    let isDragging = false;
    let dragStartX = 0;
    let dragScrollLeft = 0;
    let moved = 0;

    const startDrag = (pageX) => {
        isDragging = true;
        moved = 0;
        dragStartX = pageX - tabbar.offsetLeft;
        dragScrollLeft = tabbar.scrollLeft;
        tabbar.classList.add("dragging");
    };

    const moveDrag = (pageX) => {
        if (!isDragging) return;

        const walk = pageX - tabbar.offsetLeft - dragStartX;
        moved = Math.max(moved, Math.abs(walk));
        tabbar.scrollLeft = dragScrollLeft - walk;
    };

    const endDrag = () => {
        if (!isDragging) return;

        isDragging = false;
        tabbar.classList.remove("dragging");
        suppressClick = moved >= DRAG_CLICK_THRESHOLD;
        if (suppressClick) window.setTimeout(() => { suppressClick = false; }, 0);
    };

    tabbar.addEventListener("mousedown", (event) => startDrag(event.pageX));
    tabbar.addEventListener("mousemove", (event) => {
        if (!isDragging) return;

        event.preventDefault();
        moveDrag(event.pageX);
    });
    tabbar.addEventListener("mouseup", endDrag);
    tabbar.addEventListener("mouseleave", endDrag);
    tabbar.addEventListener("touchstart", (event) => startDrag(event.touches[0].pageX), { passive: true });
    tabbar.addEventListener(
        "touchmove",
        (event) => {
            if (isDragging) event.preventDefault();
            moveDrag(event.touches[0].pageX);
        },
        { passive: false }
    );
    tabbar.addEventListener("touchend", endDrag, { passive: true });

    document.querySelectorAll(".pz-tab").forEach((tab) => {
        tab.addEventListener("dragstart", (event) => event.preventDefault());
    });
});
