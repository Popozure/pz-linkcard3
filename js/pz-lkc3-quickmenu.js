/* Pz-LinkCard3 frontend context menu for administrators. */

(() => {
    const config = window.pz_lkc3_quickmenu;
    if (!config?.ajax_url || !config?.nonce) return;

    const labels = {
        copyTitle: "Copy title",
        copyExcerpt: "Copy excerpt",
        copyLink: "Copy link address",
        edit: "Edit",
        refreshContent: "Refresh post content",
        refreshThumbnail: "Refresh thumbnail image",
        cacheManager: "Pzカード管理",
        cardSettings: "Pzカード設定",
        refreshing: "Retrieving...",
        failed: "Failed to retrieve the post content.",
        ...(config.labels || {}),
    };

    let menu = null;
    let activeCard = null;

    function ensureStyle() {
        if (document.getElementById("pz-lkc3-quickmenu-style")) return;

        const style = document.createElement("style");
        style.id = "pz-lkc3-quickmenu-style";
        style.textContent = `
            .pz-lkc3-quickmenu {
                position: fixed;
                z-index: 100000;
                min-width: 184px;
                padding: 6px 0;
                margin: 0;
                border: 1px solid #ccd0d4;
                border-radius: 8px;
                background: #fff;
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.18);
                color: #1d2327;
                font: 13px/1.4 -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                list-style: none;
                opacity: 0;
                transform: translateY(-2px);
                transition: opacity 0.3s ease, transform 0.3s ease;
            }
            .linkcard3[data-lkc3-id] {
                position: relative;
            }
            .pz-lkc3-quickmenu-indicator {
                position: absolute;
                top: 8px;
                right: 8px;
                z-index: 2;
                width: 20px;
                height: 20px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.78);
                color: #2271b1;
                font-size: 20px;
                line-height: 20px;
                text-align: center;
                opacity: 0;
                cursor: pointer;
                pointer-events: auto;
                transition: opacity 0.15s ease;
            }
            .linkcard3[data-lkc3-id]:hover .pz-lkc3-quickmenu-indicator,
            .linkcard3[data-lkc3-id]:focus-within .pz-lkc3-quickmenu-indicator {
                opacity: 1;
            }
            .pz-lkc3-quickmenu.pz-lkc3-quickmenu-open {
                opacity: 1;
                transform: translateY(0);
            }
            .pz-lkc3-quickmenu button,
            .pz-lkc3-quickmenu a {
                display: flex;
                align-items: center;
                gap: 8px;
                width: 100%;
                box-sizing: border-box;
                min-height: 32px;
                padding: 6px 14px;
                border: 0;
                background: transparent;
                color: inherit;
                cursor: pointer;
                font: inherit;
                text-align: left;
                text-decoration: none;
                white-space: nowrap;
            }
            .pz-lkc3-quickmenu button .dashicons,
            .pz-lkc3-quickmenu a .dashicons {
                flex: 0 0 20px;
                width: 20px;
                height: 20px;
                font-size: 18px;
                line-height: 20px;
                color: #2271b1;
            }
            .pz-lkc3-quickmenu-icon-space {
                flex: 0 0 20px;
                width: 20px;
                height: 20px;
            }
            .pz-lkc3-quickmenu button:hover,
            .pz-lkc3-quickmenu button:focus,
            .pz-lkc3-quickmenu a:hover,
            .pz-lkc3-quickmenu a:focus {
                background: #f0f0f1;
                outline: none;
                color: inherit;
            }
            .pz-lkc3-quickmenu button:disabled {
                color: #8c8f94;
                cursor: default;
            }
            .pz-lkc3-quickmenu-logo {
                display: flex;
                justify-content: flex-end;
                align-items: center;
                min-height: 22px;
                margin-bottom: -6px;
                padding: 2px 4px 6px;
                border-top: 1px solid #b8dce7;
                border-radius: 0 0 4px 4px;
                background: linear-gradient(45deg, #def, #fff, #46f);
                user-select: none;
                cursor: default;
            }
            .pz-lkc3-quickmenu-logo img {
                display: block;
                width: auto;
                max-width: 112px;
                height: 18px;
            }
            .pz-lkc3-quickmenu hr {
                height: 1px;
                margin: 5px 0;
                border: 0;
                background: #dcdcde;
            }
        `;
        document.head.appendChild(style);
    }

    function findCardLink(card) {
        const wrapLink = card.querySelector(":scope > a.lkc3-wrap");
        if (wrapLink?.href) return wrapLink;

        return card.querySelector("a.lkc3-link[href]");
    }

    function closeMenu() {
        menu?.remove();
        menu = null;
        activeCard = null;
    }

    function placeMenu(x, y) {
        if (!menu) return;

        menu.style.left = "0px";
        menu.style.top = "0px";
        const rect = menu.getBoundingClientRect();
        const left = Math.min(x, window.innerWidth - rect.width - 8);
        const top = Math.min(y, window.innerHeight - rect.height - 8);
        menu.style.left = `${Math.max(8, left)}px`;
        menu.style.top = `${Math.max(8, top)}px`;
    }

    function makeButton(text, callback, iconClass = "", iconStyle = "") {
        const item = document.createElement("li");
        const button = document.createElement("button");
        button.type = "button";
        const icon = document.createElement("span");
        icon.className = iconClass ? `dashicons ${iconClass}` : "pz-lkc3-quickmenu-icon-space";
        if (iconStyle) icon.setAttribute("style", iconStyle);
        icon.setAttribute("aria-hidden", "true");
        button.appendChild(icon);
        button.appendChild(document.createTextNode(text));
        button.addEventListener("click", callback);
        item.appendChild(button);
        return item;
    }

    function makeLink(text, href, iconClass = "") {
        const item = document.createElement("li");
        const link = document.createElement("a");
        link.href = href;
        link.target = "_self";
        const icon = document.createElement("span");
        icon.className = iconClass ? `dashicons ${iconClass}` : "pz-lkc3-quickmenu-icon-space";
        icon.setAttribute("aria-hidden", "true");
        link.appendChild(icon);
        link.appendChild(document.createTextNode(text));
        link.addEventListener("click", closeMenu);
        item.appendChild(link);
        return item;
    }

    function makeLogo() {
        const item = document.createElement("li");
        item.className = "pz-lkc3-quickmenu-logo";
        item.setAttribute("role", "presentation");
        if (config.logo_url) {
            const image = document.createElement("img");
            image.src = config.logo_url;
            image.alt = "Pz-LinkCard3";
            item.appendChild(image);
        }
        return item;
    }

    function makeSeparator() {
        const item = document.createElement("li");
        item.setAttribute("role", "separator");
        item.appendChild(document.createElement("hr"));
        return item;
    }

    function fallbackCopyText(text) {
        const input = document.createElement("input");
        input.type = "text";
        input.value = text;
        input.style.position = "fixed";
        input.style.left = "-9999px";
        document.body.appendChild(input);
        input.select();

        try {
            document.execCommand("copy");
        } finally {
            input.remove();
        }
    }

    function copyText(text) {
        if (!text) return;

        if (navigator.clipboard?.writeText) {
            navigator.clipboard.writeText(text).catch(() => fallbackCopyText(text));
            return;
        }

        fallbackCopyText(text);
    }

    function copyTitle() {
        const title = activeCard?.querySelector(".lkc3-title")?.textContent?.trim();
        closeMenu();
        copyText(title);
    }

    function copyExcerpt() {
        const excerpt = activeCard?.querySelector(".lkc3-excerpt")?.textContent?.trim();
        closeMenu();
        copyText(excerpt);
    }

    function copyLinkAddress() {
        const link = activeCard ? findCardLink(activeCard) : null;
        const href = link?.href;
        closeMenu();
        copyText(href);
    }

    function replaceCards(cardId, html) {
        const template = document.createElement("template");
        template.innerHTML = html.trim();
        const newCard = template.content.firstElementChild;
        if (!newCard) throw new Error(labels.failed);

        document.querySelectorAll(`.linkcard3[data-lkc3-id="${cardId}"]`).forEach((card) => {
            card.replaceWith(newCard.cloneNode(true));
        });
    }

    function refreshCard(button, action) {
        if (!activeCard) return;

        const card = activeCard;
        const cardId = card.dataset.lkc3Id;
        if (!cardId) return;

        button.disabled = true;
        button.textContent = labels.refreshing;

        fetch(config.ajax_url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
                action,
                nonce: config.nonce,
                card_id: cardId,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (!data?.success || !data.data?.html) throw new Error(data?.data || labels.failed);
                replaceCards(data.data.card_id || cardId, data.data.html);
                closeMenu();
            })
            .catch((err) => {
                console.error("Pz-LinkCard3 refresh error:", err);
                button.disabled = false;
                button.textContent = labels.failed;
                setTimeout(() => {
                    if (button.isConnected) button.textContent = button.dataset.label || "";
                }, 1600);
            });
    }

    function editCard() {
        if (!activeCard || !config.edit_url || !config.edit_nonce) return;

        const cardId = activeCard.dataset.lkc3Id;
        closeMenu();
        if (!cardId) return;

        const form = document.createElement("form");
        form.method = "POST";
        form.action = config.edit_url;
        form.style.display = "none";

        const returnScroll = Math.max(0, Math.round(window.scrollY || document.documentElement.scrollTop || 0));
        const returnUrl = new URL(window.location.href);
        returnUrl.searchParams.set("pz_lkc3_restore_scroll", String(returnScroll));

        const fields = {
            _wpnonce: config.edit_nonce,
            "single-edit": cardId,
            return_url: returnUrl.toString(),
            return_scroll: returnScroll,
        };
        Object.entries(fields).forEach(([name, value]) => {
            const input = document.createElement("input");
            input.type = "hidden";
            input.name = name;
            input.value = value;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
        form.remove();
    }

    function openCacheManager() {
        if (!activeCard || !config.edit_url || !config.edit_nonce) return;

        const cardId = activeCard.dataset.lkc3Id;
        closeMenu();
        if (!cardId) return;

        const form = document.createElement("form");
        form.method = "POST";
        form.action = config.edit_url;
        form.style.display = "none";

        const fields = {
            _wpnonce: config.edit_nonce,
            action: "search",
            keyword: `ID:${cardId}`,
            page_now: "1",
            filter: "all",
        };
        Object.entries(fields).forEach(([name, value]) => {
            const input = document.createElement("input");
            input.type = "hidden";
            input.name = name;
            input.value = value;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
        form.remove();
    }

    function showMenu(card, x, y) {
        ensureStyle();
        closeMenu();
        activeCard = card;
        const refreshContentItem = makeButton(labels.refreshContent, (event) => refreshCard(event.currentTarget, "pz_lkc3_refresh_card"), "dashicons-update");
        const refreshThumbnailItem = makeButton(labels.refreshThumbnail, (event) => refreshCard(event.currentTarget, "pz_lkc3_refresh_thumbnail"), "dashicons-format-image");
        refreshContentItem.querySelector("button").dataset.label = labels.refreshContent;
        refreshThumbnailItem.querySelector("button").dataset.label = labels.refreshThumbnail;

        menu = document.createElement("ul");
        menu.className = "pz-lkc3-quickmenu";
        menu.setAttribute("role", "menu");
        menu.appendChild(makeButton(labels.edit, editCard, "dashicons-edit-page"));
        menu.appendChild(makeSeparator());
        menu.appendChild(makeButton(labels.copyTitle, copyTitle, "dashicons-admin-page", "vertical-align: text-bottom;"));
        menu.appendChild(makeButton(labels.copyExcerpt, copyExcerpt, "dashicons-admin-page", "vertical-align: text-bottom;"));
        menu.appendChild(makeButton(labels.copyLink, copyLinkAddress, "dashicons-admin-page", "vertical-align: text-bottom;"));
        menu.appendChild(makeSeparator());
        menu.appendChild(refreshContentItem);
        menu.appendChild(refreshThumbnailItem);
        if (config.edit_url || config.settings_url) {
            menu.appendChild(makeSeparator());
            if (config.edit_url) {
                menu.appendChild(makeButton(labels.cacheManager, openCacheManager, "dashicons-excerpt-view"));
            }
            if (config.settings_url) {
                menu.appendChild(makeLink(labels.cardSettings, config.settings_url, "dashicons-admin-generic"));
            }
        }
        menu.appendChild(makeLogo());

        document.body.appendChild(menu);
        placeMenu(x, y);
        requestAnimationFrame(() => menu?.classList.add("pz-lkc3-quickmenu-open"));
    }

    ensureStyle();

    function openIndicatorMenu(event) {
        if (!(event.target instanceof Element)) return;

        const indicator = event.target.closest(".pz-lkc3-quickmenu-indicator");
        if (!indicator) return;

        const card = indicator.closest(".linkcard3[data-lkc3-id]");
        if (!card) return;

        event.preventDefault();
        event.stopPropagation();

        const rect = indicator.getBoundingClientRect();
        showMenu(card, rect.right + 4, rect.top);
    }

    document.addEventListener("click", openIndicatorMenu);
    document.addEventListener("contextmenu", openIndicatorMenu);
    document.addEventListener("quickmenu", openIndicatorMenu);

    document.addEventListener("click", (event) => {
        if (event.target instanceof Element && event.target.closest(".pz-lkc3-quickmenu-indicator")) return;
        if (menu && event.target instanceof Node && !menu.contains(event.target)) closeMenu();
    });
    document.addEventListener("contextmenu", (event) => {
        if (event.target instanceof Element && event.target.closest(".pz-lkc3-quickmenu-indicator")) return;
        if (!menu || !(event.target instanceof Node)) return;

        if (!menu.contains(event.target)) {
            closeMenu();
            return;
        }

        const item = event.target instanceof Element
            ? event.target.closest(".pz-lkc3-quickmenu button, .pz-lkc3-quickmenu a")
            : null;
        if (!item) return;

        event.preventDefault();
        item.click();
    });
    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") closeMenu();
    });
    window.addEventListener("blur", closeMenu);
    window.addEventListener("resize", closeMenu);
    window.addEventListener("scroll", closeMenu, true);
})();
