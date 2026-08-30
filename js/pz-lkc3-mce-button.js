/* Pz-LinkCard3 TinyMCE insert button. */

(() => {
    const { tinymce } = window;
    if (!tinymce?.create || !tinymce?.PluginManager) return;

    const SELECTORS = {
        overlay: "pz-lkc3-overlay",
        modal: "pz-lkc3-modal",
        url: "pz-lkc3-url",
        code: "pz-lkc3-code",
        close: "pz-lkc3-close",
        insert: "pz-lkc3-insert",
    };

    const get = (id) => document.getElementById(id);
    const getModalElements = () => ({
        overlay: get(SELECTORS.overlay),
        modal: get(SELECTORS.modal),
        urlInput: get(SELECTORS.url),
        codeInput: get(SELECTORS.code),
    });

    const extractUrl = (value) => {
        if (!value) return "";
        const match = String(value).match(/((https?|file|ftp|data|ogg):\/\/[^ "<,]+)/);
        return match ? match[1] : "";
    };

    const setModalVisible = (visible) => {
        const { overlay, modal } = getModalElements();
        if (overlay) overlay.style.display = visible ? "block" : "none";
        if (modal) modal.style.display = visible ? "block" : "none";
    };

    const centerModal = () => {
        const modal = get(SELECTORS.modal);
        if (!modal) return;

        modal.style.left = `${(window.innerWidth - modal.offsetWidth) / 2}px`;
        modal.style.top = `${(window.innerHeight - modal.offsetHeight) / 2}px`;
    };

    const focusUrlInput = () => {
        const urlInput = get(SELECTORS.url);
        if (!urlInput) return;

        window.setTimeout(() => {
            urlInput.focus();
            urlInput.select();
        }, 100);
    };

    const openModal = () => {
        const { urlInput } = getModalElements();
        setModalVisible(true);
        if (urlInput) {
            urlInput.value = extractUrl(tinymce.activeEditor.selection.getContent());
        }
        centerModal();
        focusUrlInput();
    };

    const closeModal = () => {
        setModalVisible(false);
        tinymce.activeEditor?.focus();
    };

    tinymce.create("tinymce.plugins.pz_linkcard_tinymce", {
        getInfo() {
            return {
                longname: "Pz-LinkCard Insert Button",
                author: "poporon",
                authorurl: "https://popozure.info",
                infourl: "https://popozure.info/pz-linkcard",
                version: "0.8",
            };
        },

        init(editor, url) {
            const commandId = "pz_linkcard_insert_shortcode";
            editor.addButton(commandId, {
                title: "Insert Linkcard",
                cmd: commandId,
                image: `${url}/pz-lkc3-mce-button.png`,
            });
            editor.addCommand(commandId, openModal);
        },
    });

    tinymce.PluginManager.add("pz_linkcard_tinymce", tinymce.plugins.pz_linkcard_tinymce);
    tinymce.PluginManager.requireLangPack("pz_linkcard_tinymce");

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") closeModal();
    });

    document.querySelectorAll(`#${SELECTORS.overlay}, #${SELECTORS.close}`).forEach((el) => {
        el.addEventListener("click", closeModal);
    });

    get(SELECTORS.url)?.addEventListener("paste", function (event) {
        if (this.value !== "") return;

        const pastedText = event.clipboardData?.getData("text/plain") || "";
        const url = extractUrl(pastedText);
        if (!url) return;

        this.value = url;
        this.select();
        event.preventDefault();
    });

    get(SELECTORS.insert)?.addEventListener("click", () => {
        const { urlInput, codeInput } = getModalElements();
        const url = urlInput?.value || "";
        const code = codeInput?.value || "";

        closeModal();
        if (url && code) {
            tinymce.activeEditor.selection.setContent(`<p>[${code} url="${url}"]</p>`);
        }
        tinymce.activeEditor?.focus();
    });

    window.addEventListener("resize", centerModal);
})();
