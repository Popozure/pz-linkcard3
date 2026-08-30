/* Pz-LinkCard3 block editor integration. */

(() => {
    const { wp, pz_lkc_block_icon: blockIcon } = window;
    if (!wp?.blocks || !wp?.element || !wp?.data || !wp?.blockEditor || !wp?.hooks || !wp?.compose) return;

    const { createBlock, registerBlockType, registerBlockVariation } = wp.blocks;
    const { createElement: el, useEffect, useState } = wp.element;
    const { useDispatch, useSelect } = wp.data;
    const { store: blockEditorStore } = wp.blockEditor;
    const useEditorBlockProps = wp.blockEditor.useBlockProps || ((props) => props);
    const { addFilter } = wp.hooks;
    const { createHigherOrderComponent } = wp.compose;
    const ServerSideRender = wp.serverSideRender;
    const defaultShortcode = (blockIcon?.shortcode || "blogcard").replace(/[^a-zA-Z0-9]/g, "") || "blogcard";
    const blockTitle = blockIcon?.title || "Pz-LinkCard3";
    const urlPlaceholder = blockIcon?.placeholder || "Enter the URL and press Enter";
    const blockDescription = blockIcon?.description || "Create a Pz-LinkCard3 shortcode.";
    const shortcodes = Array.from(
        new Set(
            (blockIcon?.shortcodes || [defaultShortcode])
                .map((name) => String(name || "").replace(/[^a-zA-Z0-9]/g, ""))
                .filter(Boolean)
        )
    );
    if (!shortcodes.includes(defaultShortcode)) shortcodes.unshift(defaultShortcode);

    const icon = {
        src: () =>
            el("img", {
                src: blockIcon?.iconUrl || "",
                alt: "",
                style: {
                    width: "24px",
                    height: "24px",
                },
            }),
    };

    const escapeRegExp = (value) => String(value).replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    const decodeShortcodeAttribute = (value) =>
        String(value || "")
            .replace(/&quot;/g, '"')
            .replace(/&#039;/g, "'")
            .replace(/&#91;/g, "[")
            .replace(/&#93;/g, "]")
            .replace(/&amp;/g, "&");
    const escapeShortcodeAttribute = (value) =>
        String(value || "")
            .replace(/&/g, "&amp;")
            .replace(/"/g, "&quot;")
            .replace(/\[/g, "&#91;")
            .replace(/\]/g, "&#93;");
    const buildShortcodeText = (url, shortcodeName = defaultShortcode, sourceText = "") => {
        const text = String(sourceText || "");
        const nextUrl = `url="${escapeShortcodeAttribute(url)}"`;
        if (text) {
            const replaced = text.replace(/\burl\s*=\s*(?:"[^"]*(?:"|$)|'[^']*(?:'|$)|[^\s\]]+)/i, nextUrl);
            if (replaced !== text) return replaced;
        }
        return `[${shortcodeName} ${nextUrl}]`;
    };
    const parseShortcode = (text) => {
        const shortcodeNames = shortcodes.map(escapeRegExp).join("|");
        const pattern = new RegExp("^\\s*\\[(" + shortcodeNames + ")\\b([^\\]]*)(?:\\]\\s*)?$", "i");
        const match = String(text || "").match(pattern);
        if (!match) return null;

        const urlMatch = String(match[2] || "").match(/\burl\s*=\s*(?:"([^"]*)"?|'([^']*)'?|([^\s\]]+))/i);
        if (!urlMatch) return null;

        return {
            shortcode: match[1],
            url: decodeShortcodeAttribute(urlMatch[1] ?? urlMatch[2] ?? urlMatch[3] ?? ""),
            text: String(text || ""),
        };
    };
    const isPzShortcodeBlock = (attributes) => parseShortcode(attributes?.text) !== null;

    const PzLinkCardEditor = ({ url, shortcodeName, commitUrl, clientId }) => {
        const { removeBlock } = useDispatch(blockEditorStore);
        const [tempUrl, setTempUrl] = useState(url || "");
        const isSelected = useSelect(
            (select) => select(blockEditorStore).getSelectedBlockClientId() === clientId,
            [clientId]
        );

        useEffect(() => {
            setTempUrl(url || "");
        }, [url]);

        const blockProps = useEditorBlockProps({
            className: "pz-lkc3-block-editor",
            tabIndex: 0,
            onKeyDown: (event) => {
                if (
                    isSelected &&
                    event.target === event.currentTarget &&
                    (event.key === "Delete" || event.key === "Backspace")
                ) {
                    event.preventDefault();
                    removeBlock(clientId);
                }
            },
            style: {
                backgroundColor: "rgba(240, 250, 255, 0.2)",
                border: "1px solid #2277bb",
                borderRadius: "4px",
                boxSizing: "border-box",
                padding: "12px",
            },
        });

        return el(
            "div",
            blockProps,
            el(
                "div",
                {
                    style: {
                        color: "#111827",
                        fontSize: "13px",
                        fontWeight: "700",
                        lineHeight: "1.4",
                        marginBottom: "6px",
                    },
                },
                blockTitle
            ),
            el("input", {
                type: "url",
                value: tempUrl,
                placeholder: urlPlaceholder,
                onChange: (event) => setTempUrl(event.target.value),
                onKeyDown: (event) => {
                    if (event.key === "Enter") {
                        event.preventDefault();
                        commitUrl(tempUrl);
                    }
                },
                onBlur: () => commitUrl(tempUrl),
                style: {
                    width: "100%",
                    padding: "6px",
                    fontSize: "14px",
                    boxSizing: "border-box",
                    marginBottom: "10px",
                },
            }),
            url
                ? el(ServerSideRender, {
                      block: "pz-series/pz-linkcard-block",
                      attributes: { url, shortcode: shortcodeName || defaultShortcode },
                  })
                : null
        );
    };

    registerBlockVariation("core/shortcode", {
        name: "pz-linkcard3",
        title: "Pz-LinkCard3",
        description: blockDescription,
        icon,
        attributes: {
            text: buildShortcodeText("", defaultShortcode),
        },
        isActive: isPzShortcodeBlock,
    });

    addFilter(
        "editor.BlockEdit",
        "pz-linkcard3/shortcode-edit",
        createHigherOrderComponent(
            (BlockEdit) =>
                (props) => {
                    if (props.name !== "core/shortcode" || !isPzShortcodeBlock(props.attributes)) {
                        return el(BlockEdit, props);
                    }

                    const parsed = parseShortcode(props.attributes.text);
                    return el(PzLinkCardEditor, {
                        url: parsed.url,
                        shortcodeName: parsed.shortcode,
                        clientId: props.clientId,
                        commitUrl: (nextUrl) =>
                            props.setAttributes({
                                text: buildShortcodeText(nextUrl, parsed.shortcode, parsed.text),
                            }),
                    });
                },
            "withPzLinkCardShortcodeEdit"
        )
    );

    registerBlockType("pz-series/pz-linkcard-block", {
        title: "Pz-LinkCard3",
        icon,
        category: "widgets",
        supports: {
            inserter: false,
        },
        attributes: {
            url: {
                type: "string",
            },
        },
        edit: ({ attributes, clientId }) => {
            const { replaceBlock } = useDispatch(blockEditorStore);
            const url = attributes.url || "";

            useEffect(() => {
                if (url) {
                    replaceBlock(clientId, createBlock("core/shortcode", { text: buildShortcodeText(url) }));
                }
            }, [clientId, replaceBlock, url]);

            return el(PzLinkCardEditor, {
                url,
                shortcodeName: defaultShortcode,
                clientId,
                commitUrl: (nextUrl) =>
                    replaceBlock(clientId, createBlock("core/shortcode", { text: buildShortcodeText(nextUrl) })),
            });
        },
        save: () => null,
    });
})();
