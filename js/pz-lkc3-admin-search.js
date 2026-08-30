/* ===== Settings search ===== */

/* Pz-LinkCard3 settings search. */

document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.querySelector("#pz-search-box");
    const searchBtn = document.querySelector("#pz-search-btn");
    const searchStatus = document.querySelector("#pz-search-status");
    if (!searchInput || !searchBtn || !searchStatus) return;

    const TARGET_SELECTOR = "h1, h2, h3, h4, h5, h6, label, th";
    const state = {
        matches: [],
        currentIndex: -1,
        lastKeyword: "",
    };

    const escapeRegExp = (value) => String(value).replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    const getKeyword = () => searchInput.value.trim().toLowerCase();
    const getTargets = (root = document) => Array.from(root.querySelectorAll(TARGET_SELECTOR));

    const resetHighlight = (el) => {
        el.querySelectorAll("span.highlight").forEach((span) => {
            span.replaceWith(document.createTextNode(span.textContent));
        });
        el.normalize();
    };

    const resetAllHighlights = () => {
        getTargets().forEach(resetHighlight);
    };

    const highlightKeywordTextNode = (el, keyword) => {
        const regex = new RegExp(`(${escapeRegExp(keyword)})`, "gi");

        Array.from(el.childNodes).forEach((node) => {
            if (node.nodeType !== Node.TEXT_NODE || !node.textContent) return;

            const fragment = document.createDocumentFragment();
            let lastIndex = 0;
            node.textContent.replace(regex, (match, text, offset) => {
                if (offset > lastIndex) {
                    fragment.appendChild(document.createTextNode(node.textContent.slice(lastIndex, offset)));
                }

                const span = document.createElement("span");
                span.className = "highlight";
                span.textContent = text;
                fragment.appendChild(span);
                lastIndex = offset + text.length;
                return match;
            });

            if (lastIndex === 0) return;
            if (lastIndex < node.textContent.length) {
                fragment.appendChild(document.createTextNode(node.textContent.slice(lastIndex)));
            }
            node.replaceWith(fragment);
        });
    };

    const getVisibleTabPages = () =>
        Array.from(document.querySelectorAll(".pz-tab"))
            .filter((tab) => tab.offsetParent !== null)
            .map((tab) => tab.getAttribute("href"))
            .filter((href) => href?.startsWith("#"))
            .map((href) => document.getElementById(href.slice(1)))
            .filter(Boolean);

    const searchSettings = (keyword) => {
        const normalizedKeyword = keyword.trim().toLowerCase();
        if (!normalizedKeyword) return [];

        resetAllHighlights();

        const found = [];
        getVisibleTabPages().forEach((container) => {
            getTargets(container).forEach((el) => {
                if (!el.innerText.toLowerCase().includes(normalizedKeyword)) return;

                highlightKeywordTextNode(el, normalizedKeyword);
                found.push(el);
            });
        });

        return found;
    };

    const updateStatus = () => {
        searchStatus.textContent = state.matches.length
            ? `${state.currentIndex + 1}/${state.matches.length}`
            : "";
    };

    const openTabForElement = (el) => {
        const container = el.closest("div[id]");
        if (!container) return;

        if (window.PzLkc3SettingsTabs?.openTab) {
            window.PzLkc3SettingsTabs.openTab(container.id);
            return;
        }

        const tab = Array.from(document.querySelectorAll(".pz-tab"))
            .find((item) => item.getAttribute("href") === `#${container.id}`);
        tab?.click();
    };

    const focusResult = (el) => {
        if (!el) return;

        openTabForElement(el);
        el.scrollIntoView({ behavior: "smooth", block: "center" });
        el.style.transition = "background-color 0.5s";
        el.style.backgroundColor = "orange";
        window.setTimeout(() => {
            el.style.backgroundColor = "";
        }, 1000);
    };

    const runSearch = (keyword = getKeyword()) => {
        if (!keyword) return false;

        if (keyword !== state.lastKeyword) {
            state.matches = searchSettings(keyword);
            state.lastKeyword = keyword;
            state.currentIndex = -1;
        }

        return state.matches.length > 0;
    };

    const moveResult = (direction) => {
        if (!runSearch()) {
            updateStatus();
            return;
        }

        state.currentIndex = (state.currentIndex + direction + state.matches.length) % state.matches.length;
        focusResult(state.matches[state.currentIndex]);
        updateStatus();
    };

    const clearSearch = () => {
        resetAllHighlights();
        state.matches = [];
        state.currentIndex = -1;
        state.lastKeyword = "";
        updateStatus();
    };

    searchBtn.addEventListener("click", () => {
        const keyword = getKeyword();
        if (!keyword) return;

        state.matches = searchSettings(keyword);
        state.lastKeyword = keyword;
        state.currentIndex = state.matches.length ? 0 : -1;
        focusResult(state.matches[state.currentIndex]);
        updateStatus();
    });

    searchInput.addEventListener("keydown", (event) => {
        if (event.key === "Escape") {
            event.preventDefault();
            clearSearch();
            return;
        }

        if (event.key !== "Enter") return;

        event.preventDefault();
        moveResult(event.shiftKey ? -1 : 1);
    });

    searchInput.addEventListener("input", () => {
        searchStatus.textContent = "";
        if (state.lastKeyword && searchInput.value.toLowerCase() !== state.lastKeyword) {
            resetAllHighlights();
        }
    });

    document.addEventListener("keydown", (event) => {
        const selectedText = window.getSelection().toString().trim();

        if (event.ctrlKey && event.key.toLowerCase() === "f") {
            event.preventDefault();
            if (selectedText) searchInput.value = selectedText;
            searchInput.focus();
            searchInput.select();

            state.matches = searchSettings(getKeyword());
            state.lastKeyword = getKeyword();
            state.currentIndex = state.matches.length ? 0 : -1;
            focusResult(state.matches[state.currentIndex]);
            updateStatus();
            return;
        }

        if (event.key !== "F3") return;

        event.preventDefault();
        if (!state.matches.length && selectedText) searchInput.value = selectedText;
        if (!state.matches.length) {
            searchInput.focus();
            searchInput.select();
        }
        moveResult(event.shiftKey ? -1 : 1);
    });
});
