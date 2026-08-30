/* Pz-LinkCard3 click counter. */

document.addEventListener("click", (event) => {
    if (!(event.target instanceof Element)) return;

    const link = event.target.closest("a.lkc3-link");
    if (!link) return;

    const cardId = link.closest(".linkcard3")?.dataset.lkc3Id;
    if (!cardId || !window.lkc3_ajax_count) return;

    fetch(lkc3_ajax_count.ajax_url, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
            action: "pz_lkc3_click_count",
            nonce: lkc3_ajax_count.nonce,
            card_id: cardId,
        }),
    }).catch((err) => {
        console.error("Pz-LinkCard3 click count error:", err);
    });
});
