/* Pz-LinkCard3 lazy card replacement. */

document.addEventListener("DOMContentLoaded", () => {
    const restBaseUrl = window.pz_lkc3_lazy?.rest_url || "/wp-json/pz-linkcard/v1/card/";

    function scrollToErrorFromTop() {
        const params = new URLSearchParams(window.location.search);
        const targetId = params.get("pz_lkc3_scroll") || (window.location.hash === "#lkc3-error" ? "lkc3-error" : "");
        if (targetId !== "lkc3-error") return;

        const target = document.getElementById(targetId);
        if (!target) return;

        if ("scrollRestoration" in history) {
            history.scrollRestoration = "manual";
        }

        window.scrollTo(0, 0);
        window.requestAnimationFrame(() => {
            window.requestAnimationFrame(() => {
                target.scrollIntoView({ behavior: "smooth", block: "center" });
            });
        });

        if (params.has("pz_lkc3_scroll") && window.history.replaceState) {
            params.delete("pz_lkc3_scroll");
            const query = params.toString();
            const cleanUrl = `${window.location.pathname}${query ? `?${query}` : ""}${window.location.hash}`;
            window.history.replaceState(null, "", cleanUrl);
        }
    }

    function restoreQuickmenuScroll() {
        const params = new URLSearchParams(window.location.search);
        if (!params.has("pz_lkc3_restore_scroll")) return;

        const top = Math.max(0, parseInt(params.get("pz_lkc3_restore_scroll"), 10) || 0);
        if ("scrollRestoration" in history) {
            history.scrollRestoration = "manual";
        }

        const restore = () => window.scrollTo({ top, behavior: "auto" });
        restore();
        window.requestAnimationFrame(() => {
            restore();
            window.setTimeout(restore, 250);
            window.setTimeout(restore, 800);
        });

        params.delete("pz_lkc3_restore_scroll");
        if (window.history.replaceState) {
            const query = params.toString();
            const cleanUrl = `${window.location.pathname}${query ? `?${query}` : ""}${window.location.hash}`;
            window.history.replaceState(null, "", cleanUrl);
        }
    }

    function checkAndUpdateCards() {
        const cards = document.querySelectorAll(".linkcard3[data-lazy='true']:not([data-lazy-loading='true'])");
        if (!cards.length) return;

        cards.forEach((card) => {
            const dataId = card.dataset.lkc3Id;
            const token = card.dataset.lkc3Token;
            const page = card.dataset.lkc3Page;
            if (!dataId || !token || !page) return;

            const url = new URL(`${restBaseUrl}${encodeURIComponent(dataId)}`, window.location.href);
            url.searchParams.set("token", token);
            url.searchParams.set("page", page);

            card.dataset.lazyLoading = "true";
            fetch(url.toString())
                .then((response) => {
                    if (response.status === 202) return null;
                    if (!response.ok) throw new Error(`REST API error: ${response.status}`);
                    return response.json();
                })
                .then((data) => {
                    if (!data || Number(data.status) !== 200 || !data.html) return;

                    card.outerHTML = data.html;
                })
                .catch((err) => {
                    console.error(`Pz-LinkCard3 lazy load error (${dataId}):`, err);
                })
                .finally(() => {
                    delete card.dataset.lazyLoading;
                });
        });
    }

    scrollToErrorFromTop();
    restoreQuickmenuScroll();
    checkAndUpdateCards();
    setInterval(checkAndUpdateCards, 2000);
});
