/* ===== Settings preview ===== */

/* =========================================
  RULES
========================================= */

// Helper functions to reduce duplication
function createSimpleRule(targets, prop, after = '') {
  return { targets, prop, after };
}

function createShadowRule(targets, group, part) {
  return { targets, prop: 'box-shadow', group, part };
}

function createTransformRule(targets, before, after) {
  return { targets, prop: 'transform', before, after };
}

function createTransformGroupRule(targets, group, part) {
  return { targets, prop: 'transform', group, part };
}

function createFontRule(targets, prop) {
  return { targets, prop };
}

function createHtmlRule(html) {
  return { targets: '', prop: '', after: '', html };
}

function createToggleRule(targets, props, keys) {
  return { targets, prop: '!toggle', props, keys };
}

const RULES = {
  // Margins
  'margin-top':          createSimpleRule('.lkc3-wrap', 'margin-top'),
  'margin-left':         createSimpleRule('.lkc3-wrap', 'margin-left'),
  'margin-right':        createSimpleRule('.lkc3-wrap', 'margin-right'),
  'margin-bottom':       createSimpleRule('.lkc3-wrap', 'margin-bottom'),

  // Paddings
  'padding-top':         createSimpleRule('.lkc3-wrap', 'padding-top'),
  'padding-left':        createSimpleRule('.lkc3-wrap', 'padding-left'),
  'padding-right':       createSimpleRule('.lkc3-wrap', 'padding-right'),
  'padding-bottom':      createSimpleRule('.lkc3-wrap', 'padding-bottom'),

  // Content and width
  'content-height':      createSimpleRule('.lkc3-contents', 'height', 'px'),
  'width':               createSimpleRule(['.lkc3-external-wrap','.lkc3-internal-wrap'], 'width'),

  // Heading margins
  'heading-margin-left': createSimpleRule('.lkc3-heading', 'left'),
  'heading-margin-top':  createSimpleRule('.lkc3-heading', 'top'),
  'heading-padding-h':   createSimpleRule('.lkc3-heading', ['padding-left','padding-right']),
  'heading-padding-v':   createSimpleRule('.lkc3-heading', ['padding-top','padding-bottom']),

  // Siteiconloadoption
  'siteicon-size':       { targets: '.lkc3-siteicon', prop: ['height','width'], after: '', htmlAttrs: [{ sel: '.lkc3-siteicon img', attr: 'width' }, { sel: '.lkc3-siteicon img', attr: 'height' }] },

  // Thumbnail
  'thumbnail-width':      { targets: ['.lkc3-external-wrap .lkc3-thumbnail','.lkc3-internal-wrap .lkc3-thumbnail'], prop: 'width', after: 'px', htmlAttrs: [{ sel: '.lkc3-thumbnail-img', attr: 'width' }] },
  'thumbnail-height':     { targets: ['.lkc3-external-wrap .lkc3-thumbnail','.lkc3-internal-wrap .lkc3-thumbnail'], prop: 'height', after: 'px', htmlAttrs: [{ sel: '.lkc3-thumbnail-img', attr: 'height' }] },

  // External wrap styles
  'ex-bg-color':         createSimpleRule('.lkc3-external-wrap', 'background-color'),
  'ex-border-color':     createSimpleRule('.lkc3-external-wrap', 'border-color'),
  'ex-border-style':     createSimpleRule('.lkc3-external-wrap', 'border-style'),
  'ex-border-width':     createSimpleRule('.lkc3-external-wrap', 'border-width', 'px'),
  'ex-border-radius':    createSimpleRule('.lkc3-external-wrap', 'border-radius', 'px'),

  // External wrap shadows
  'ex-shadow-x':         createShadowRule('.lkc3-external-wrap', 'ex-card-shadow', 'x'),
  'ex-shadow-y':         createShadowRule('.lkc3-external-wrap', 'ex-card-shadow', 'y'),
  'ex-shadow-blur':      createShadowRule('.lkc3-external-wrap', 'ex-card-shadow', 'blur'),
  'ex-shadow-spread':    createShadowRule('.lkc3-external-wrap', 'ex-card-shadow', 'spread'),
  'ex-shadow-color':     createShadowRule('.lkc3-external-wrap', 'ex-card-shadow', 'color'),
  'ex-shadow-inset':     createShadowRule('.lkc3-external-wrap', 'ex-card-shadow', 'inset'),

  // External wrap transforms
  'ex-transform-x':      createTransformGroupRule('.lkc3-external-wrap', 'ex-card-transform', 'x'),
  'ex-transform-y':      createTransformGroupRule('.lkc3-external-wrap', 'ex-card-transform', 'y'),
  'ex-transform-rotate': createTransformGroupRule('.lkc3-external-wrap', 'ex-card-transform', 'rotate'),
  'ex-transform-scale':  createTransformGroupRule('.lkc3-external-wrap', 'ex-card-transform', 'scale'),

  // External heading styles
  'ex-heading-bg-color':          createSimpleRule('.lkc3-external-wrap .lkc3-heading', 'background-color'),
  'ex-heading-border-color':      createSimpleRule('.lkc3-external-wrap .lkc3-heading', 'border-color'),
  'ex-heading-border-style':      createSimpleRule('.lkc3-external-wrap .lkc3-heading', 'border-style'),
  'ex-heading-border-width':      createSimpleRule('.lkc3-external-wrap .lkc3-heading', 'border-width', 'px'),
  'ex-heading-border-radius':     createSimpleRule('.lkc3-external-wrap .lkc3-heading', 'border-radius', 'px'),
  'ex-heading-shadow-x':          createShadowRule('.lkc3-external-wrap .lkc3-heading', 'ex-heading', 'x'),
  'ex-heading-shadow-y':          createShadowRule('.lkc3-external-wrap .lkc3-heading', 'ex-heading', 'y'),
  'ex-heading-shadow-blur':       createShadowRule('.lkc3-external-wrap .lkc3-heading', 'ex-heading', 'blur'),
  'ex-heading-shadow-spread':     createShadowRule('.lkc3-external-wrap .lkc3-heading', 'ex-heading', 'spread'),
  'ex-heading-shadow-color':      createShadowRule('.lkc3-external-wrap .lkc3-heading', 'ex-heading', 'color'),
  'ex-heading-shadow-inset':      createShadowRule('.lkc3-external-wrap .lkc3-heading', 'ex-heading', 'inset'),
  'ex-heading-transform-x':       createTransformGroupRule('.lkc3-external-wrap .lkc3-heading', 'ex-heading-transform', 'x'),
  'ex-heading-transform-y':       createTransformGroupRule('.lkc3-external-wrap .lkc3-heading', 'ex-heading-transform', 'y'),
  'ex-heading-transform-rotate':  createTransformGroupRule('.lkc3-external-wrap .lkc3-heading', 'ex-heading-transform', 'rotate'),
  'ex-heading-transform-scale':   createTransformGroupRule('.lkc3-external-wrap .lkc3-heading', 'ex-heading-transform', 'scale'),

  // External thumbnail styles
  'ex-thumbnail-bg-color':          createSimpleRule('.lkc3-external-wrap .lkc3-thumbnail-img', 'background-color'),
  'ex-thumbnail-border-color':      createSimpleRule('.lkc3-external-wrap .lkc3-thumbnail-img', 'border-color'),
  'ex-thumbnail-border-style':      createSimpleRule('.lkc3-external-wrap .lkc3-thumbnail-img', 'border-style'),
  'ex-thumbnail-border-width':      createSimpleRule('.lkc3-external-wrap .lkc3-thumbnail-img', 'border-width', 'px'),
  'ex-thumbnail-border-radius':     createSimpleRule('.lkc3-external-wrap .lkc3-thumbnail-img', 'border-radius', 'px'),
  'ex-thumbnail-shadow-x':          createShadowRule('.lkc3-external-wrap .lkc3-thumbnail-img', 'ex-thumbnail', 'x'),
  'ex-thumbnail-shadow-y':          createShadowRule('.lkc3-external-wrap .lkc3-thumbnail-img', 'ex-thumbnail', 'y'),
  'ex-thumbnail-shadow-blur':       createShadowRule('.lkc3-external-wrap .lkc3-thumbnail-img', 'ex-thumbnail', 'blur'),
  'ex-thumbnail-shadow-spread':     createShadowRule('.lkc3-external-wrap .lkc3-thumbnail-img', 'ex-thumbnail', 'spread'),
  'ex-thumbnail-shadow-color':      createShadowRule('.lkc3-external-wrap .lkc3-thumbnail-img', 'ex-thumbnail', 'color'),
  'ex-thumbnail-shadow-inset':      createShadowRule('.lkc3-external-wrap .lkc3-thumbnail-img', 'ex-thumbnail', 'inset'),
  'ex-thumbnail-transform-x':       createTransformRule('.lkc3-external-wrap .lkc3-thumbnail-img', 'translateX(', 'px)'),
  'ex-thumbnail-transform-y':       createTransformRule('.lkc3-external-wrap .lkc3-thumbnail-img', 'translateY(', 'px)'),
  'ex-thumbnail-transform-rotate':  createTransformRule('.lkc3-external-wrap .lkc3-thumbnail-img', 'rotate(', 'deg)'),
  'ex-thumbnail-transform-scale':   { targets: '.lkc3-external-wrap .lkc3-thumbnail-img', prop: 'transform', before: 'scale(', unit: '%', after: ')',  },

  // External more styles
  'ex-more-bg-color':           createSimpleRule('.lkc3-external-wrap .lkc3-more', 'background-color'),
  'ex-more-border-color':       createSimpleRule('.lkc3-external-wrap .lkc3-more', 'border-color'),
  'ex-more-border-style':       createSimpleRule('.lkc3-external-wrap .lkc3-more', 'border-style'),
  'ex-more-border-width':       createSimpleRule('.lkc3-external-wrap .lkc3-more', 'border-width', 'px'),
  'ex-more-border-radius':      createSimpleRule('.lkc3-external-wrap .lkc3-more', 'border-radius', 'px'),
  'ex-more-shadow-x':          createShadowRule('.lkc3-external-wrap .lkc3-more', 'ex-more', 'x'),
  'ex-more-shadow-y':          createShadowRule('.lkc3-external-wrap .lkc3-more', 'ex-more', 'y'),
  'ex-more-shadow-blur':       createShadowRule('.lkc3-external-wrap .lkc3-more', 'ex-more', 'blur'),
  'ex-more-shadow-spread':     createShadowRule('.lkc3-external-wrap .lkc3-more', 'ex-more', 'spread'),
  'ex-more-shadow-color':      createShadowRule('.lkc3-external-wrap .lkc3-more', 'ex-more', 'color'),
  'ex-more-shadow-inset':      createShadowRule('.lkc3-external-wrap .lkc3-more', 'ex-more', 'inset'),
  'ex-more-transform-x':        createTransformRule('.lkc3-external-wrap .lkc3-more', 'translateX(', 'px)'),
  'ex-more-transform-y':        createTransformRule('.lkc3-external-wrap .lkc3-more', 'translateY(', 'px)'),
  'ex-more-transform-rotate':   createTransformRule('.lkc3-external-wrap .lkc3-more', 'rotate(', 'deg)'),
  'ex-more-transform-scale':    { targets: '.lkc3-external-wrap .lkc3-more', prop: 'transform', before: 'scale(', unit: '%', after: ')',  },
  
  'in-bg-color':         { targets: '.lkc3-internal-wrap', prop: 'background-color', after: '',  },
  'in-border-color':     { targets: '.lkc3-internal-wrap', prop: 'border-color', after: '',  },
  'in-border-style':     { targets: '.lkc3-internal-wrap', prop: 'border-style', after: '',  },
  'in-border-width':     { targets: '.lkc3-internal-wrap', prop: 'border-width', after: 'px',  },
  'in-border-radius':    { targets: '.lkc3-internal-wrap', prop: 'border-radius', after: 'px',  },

  'in-shadow-x':         createShadowRule('.lkc3-internal-wrap', 'in-card-shadow', 'x'),
  'in-shadow-y':         createShadowRule('.lkc3-internal-wrap', 'in-card-shadow', 'y'),
  'in-shadow-blur':      createShadowRule('.lkc3-internal-wrap', 'in-card-shadow', 'blur'),
  'in-shadow-spread':    createShadowRule('.lkc3-internal-wrap', 'in-card-shadow', 'spread'),
  'in-shadow-color':     createShadowRule('.lkc3-internal-wrap', 'in-card-shadow', 'color'),
  'in-shadow-inset':     createShadowRule('.lkc3-internal-wrap', 'in-card-shadow', 'inset'),

  'in-transform-x':      createTransformGroupRule('.lkc3-internal-wrap', 'in-card-transform', 'x'),
  'in-transform-y':      createTransformGroupRule('.lkc3-internal-wrap', 'in-card-transform', 'y'),
  'in-transform-rotate': createTransformGroupRule('.lkc3-internal-wrap', 'in-card-transform', 'rotate'),
  'in-transform-scale':  createTransformGroupRule('.lkc3-internal-wrap', 'in-card-transform', 'scale'),

  'in-heading-bg-color':          { targets: '.lkc3-internal-wrap .lkc3-heading', prop: 'background-color', after: '',  },
  'in-heading-border-color':      { targets: '.lkc3-internal-wrap .lkc3-heading', prop: 'border-color', after: '',  },
  'in-heading-border-style':      { targets: '.lkc3-internal-wrap .lkc3-heading', prop: 'border-style', after: '',  },
  'in-heading-border-width':      { targets: '.lkc3-internal-wrap .lkc3-heading', prop: 'border-width', after: 'px',  },
  'in-heading-border-radius':     { targets: '.lkc3-internal-wrap .lkc3-heading', prop: 'border-radius', after: 'px',  },
  'in-heading-shadow-x':          createShadowRule('.lkc3-internal-wrap .lkc3-heading', 'in-heading', 'x'),
  'in-heading-shadow-y':          createShadowRule('.lkc3-internal-wrap .lkc3-heading', 'in-heading', 'y'),
  'in-heading-shadow-blur':       createShadowRule('.lkc3-internal-wrap .lkc3-heading', 'in-heading', 'blur'),
  'in-heading-shadow-spread':     createShadowRule('.lkc3-internal-wrap .lkc3-heading', 'in-heading', 'spread'),
  'in-heading-shadow-color':      createShadowRule('.lkc3-internal-wrap .lkc3-heading', 'in-heading', 'color'),
  'in-heading-shadow-inset':      createShadowRule('.lkc3-internal-wrap .lkc3-heading', 'in-heading', 'inset'),
  'in-heading-transform-x':       createTransformGroupRule('.lkc3-internal-wrap .lkc3-heading', 'in-heading-transform', 'x'),
  'in-heading-transform-y':       createTransformGroupRule('.lkc3-internal-wrap .lkc3-heading', 'in-heading-transform', 'y'),
  'in-heading-transform-rotate':  createTransformGroupRule('.lkc3-internal-wrap .lkc3-heading', 'in-heading-transform', 'rotate'),
  'in-heading-transform-scale':   createTransformGroupRule('.lkc3-internal-wrap .lkc3-heading', 'in-heading-transform', 'scale'),

  'in-thumbnail-bg-color':          { targets: '.lkc3-internal-wrap .lkc3-thumbnail-img', prop: 'background-color', after: '',  },
  'in-thumbnail-border-color':      { targets: '.lkc3-internal-wrap .lkc3-thumbnail-img', prop: 'border-color', after: '',  },
  'in-thumbnail-border-style':      { targets: '.lkc3-internal-wrap .lkc3-thumbnail-img', prop: 'border-style', after: '',  },
  'in-thumbnail-border-width':      { targets: '.lkc3-internal-wrap .lkc3-thumbnail-img', prop: 'border-width', after: 'px',  },
  'in-thumbnail-border-radius':     { targets: '.lkc3-internal-wrap .lkc3-thumbnail-img', prop: 'border-radius', after: 'px',  },
  'in-thumbnail-shadow-x':          createShadowRule('.lkc3-internal-wrap .lkc3-thumbnail-img', 'in-thumbnail', 'x'),
  'in-thumbnail-shadow-y':          createShadowRule('.lkc3-internal-wrap .lkc3-thumbnail-img', 'in-thumbnail', 'y'),
  'in-thumbnail-shadow-blur':       createShadowRule('.lkc3-internal-wrap .lkc3-thumbnail-img', 'in-thumbnail', 'blur'),
  'in-thumbnail-shadow-spread':     createShadowRule('.lkc3-internal-wrap .lkc3-thumbnail-img', 'in-thumbnail', 'spread'),
  'in-thumbnail-shadow-color':      createShadowRule('.lkc3-internal-wrap .lkc3-thumbnail-img', 'in-thumbnail', 'color'),
  'in-thumbnail-shadow-inset':      createShadowRule('.lkc3-internal-wrap .lkc3-thumbnail-img', 'in-thumbnail', 'inset'),
  'in-thumbnail-transform-x':       { targets: '.lkc3-internal-wrap .lkc3-thumbnail-img', prop: 'transform', before: 'translateX(', after: 'px)',  },
  'in-thumbnail-transform-y':       { targets: '.lkc3-internal-wrap .lkc3-thumbnail-img', prop: 'transform', before: 'translateY(', after: 'px)',  },
  'in-thumbnail-transform-rotate':  { targets: '.lkc3-internal-wrap .lkc3-thumbnail-img', prop: 'transform', before: 'rotate(', after: 'deg)',  },
  'in-thumbnail-transform-scale':   { targets: '.lkc3-internal-wrap .lkc3-thumbnail-img', prop: 'transform', before: 'scale(', unit: '%', after: ')',  },

  'in-more-bg-color':          { targets: '.lkc3-internal-wrap .lkc3-more', prop: 'background-color', after: '',  },
  'in-more-border-color':      { targets: '.lkc3-internal-wrap .lkc3-more', prop: 'border-color', after: '',  },
  'in-more-border-style':      { targets: '.lkc3-internal-wrap .lkc3-more', prop: 'border-style', after: '',  },
  'in-more-border-width':      { targets: '.lkc3-internal-wrap .lkc3-more', prop: 'border-width', after: 'px',  },
  'in-more-border-radius':     { targets: '.lkc3-internal-wrap .lkc3-more', prop: 'border-radius', after: 'px',  },
  'in-more-shadow-x':          createShadowRule('.lkc3-internal-wrap .lkc3-more', 'in-more', 'x'),
  'in-more-shadow-y':          createShadowRule('.lkc3-internal-wrap .lkc3-more', 'in-more', 'y'),
  'in-more-shadow-blur':       createShadowRule('.lkc3-internal-wrap .lkc3-more', 'in-more', 'blur'),
  'in-more-shadow-spread':     createShadowRule('.lkc3-internal-wrap .lkc3-more', 'in-more', 'spread'),
  'in-more-shadow-color':      createShadowRule('.lkc3-internal-wrap .lkc3-more', 'in-more', 'color'),
  'in-more-shadow-inset':      createShadowRule('.lkc3-internal-wrap .lkc3-more', 'in-more', 'inset'),
  'in-more-transform-x':       { targets: '.lkc3-internal-wrap .lkc3-more', prop: 'transform', before: 'translateX(', after: 'px)',  },
  'in-more-transform-y':       { targets: '.lkc3-internal-wrap .lkc3-more', prop: 'transform', before: 'translateY(', after: 'px)',  },
  'in-more-transform-rotate':  { targets: '.lkc3-internal-wrap .lkc3-more', prop: 'transform', before: 'rotate(', after: 'deg)',  },
  'in-more-transform-scale':   { targets: '.lkc3-internal-wrap .lkc3-more', prop: 'transform', before: 'scale(', unit: '%', after: ')',  },

  'title-color':         { targets: '.lkc3-title', prop: 'color', after: '',  },
  'title-bg-color':      { targets: '.lkc3-title', prop: 'background-color', after: '',  },
  'title-size':          { targets: '.lkc3-title', prop: 'font-size', after: 'px',  },
  'title-height':        { targets: '.lkc3-title', prop: 'line-height', after: 'px',  },
  'title-bold':          { targets: '.lkc3-title', prop: '!bold' },
  'title-italic':        { targets: '.lkc3-title', prop: '!italic' },
  'title-underline':     { targets: '.lkc3-title', prop: '!underline' },

  'excerpt-color':       { targets: '.lkc3-excerpt', prop: 'color', after: '',  },
  'excerpt-bg-color':    { targets: '.lkc3-excerpt', prop: 'background-color', after: '',  },
  'excerpt-size':        { targets: '.lkc3-excerpt', prop: 'font-size', after: 'px',  },
  'excerpt-height':      { targets: '.lkc3-excerpt', prop: 'line-height', after: 'px',  },
  'excerpt-bold':        { targets: '.lkc3-excerpt', prop: '!bold' },
  'excerpt-italic':      { targets: '.lkc3-excerpt', prop: '!italic' },
  'excerpt-underline':   { targets: '.lkc3-excerpt', prop: '!underline' },

  'url-color':           { targets: '.lkc3-url', prop: 'color', after: '',  },
  'url-bg-color':        { targets: '.lkc3-url', prop: 'background-color', after: '',  },
  'url-size':            { targets: '.lkc3-url', prop: 'font-size', after: 'px',  },
  'url-height':          { targets: '.lkc3-url', prop: 'line-height', after: 'px',  },
  'url-bold':            { targets: '.lkc3-url', prop: '!bold' },
  'url-italic':          { targets: '.lkc3-url', prop: '!italic' },
  'url-underline':       { targets: '.lkc3-url', prop: '!underline' },

  'date-color':          { targets: '.lkc3-date', prop: 'color', after: '',  },
  'date-bg-color':       { targets: '.lkc3-date', prop: 'background-color', after: '',  },
  'date-size':           { targets: '.lkc3-date', prop: 'font-size', after: 'px',  },
  'date-height':         { targets: '.lkc3-date', prop: 'line-height', after: 'px',  },
  'date-bold':           { targets: '.lkc3-date', prop: '!bold' },
  'date-italic':         { targets: '.lkc3-date', prop: '!italic' },
  'date-underline':      { targets: '.lkc3-date', prop: '!underline' },

  'heading-color':       { targets: '.lkc3-heading', prop: 'color', after: '',  },
  'heading-bg-color':    { targets: '.lkc3-heading', prop: 'background-color', after: '',  },
  'heading-size':        { targets: '.lkc3-heading', prop: 'font-size', after: 'px',  },
  'heading-height':      { targets: '.lkc3-heading', prop: 'line-height', after: 'px',  },
  'heading-bold':        { targets: '.lkc3-heading', prop: '!bold' },
  'heading-italic':      { targets: '.lkc3-heading', prop: '!italic' },
  'heading-underline':   { targets: '.lkc3-heading', prop: '!underline' },

  'more-color':          { targets: '.lkc3-more', prop: 'color', after: '',  },
  'more-bg-color':       { targets: '.lkc3-more', prop: 'background-color', after: '',  },
  'more-size':           { targets: '.lkc3-more', prop: 'font-size', after: 'px',  },
  'more-height':         { targets: '.lkc3-more', prop: 'line-height', after: 'px',  },
  'more-bold':           { targets: '.lkc3-more', prop: '!bold' },
  'more-italic':         { targets: '.lkc3-more', prop: '!italic' },
  'more-underline':      { targets: '.lkc3-more', prop: '!underline' },

  'info-color':          { targets: '.lkc3-info', prop: 'color', after: '',  },
  'info-bg-color':       { targets: '.lkc3-info', prop: 'background-color', after: '',  },
  'info-size':           { targets: '.lkc3-info', prop: 'font-size', after: 'px',  },
  'info-height':         { targets: '.lkc3-info', prop: 'line-height', after: 'px',  },
  'info-bold':           { targets: '.lkc3-info', prop: '!bold' },
  'info-italic':         { targets: '.lkc3-info', prop: '!italic' },
  'info-underline':      { targets: '.lkc3-info', prop: '!underline' },

  'added-color':         { targets: '.lkc3-added', prop: 'color', after: '',  },
  'added-bg-color':      { targets: '.lkc3-added', prop: 'background-color', after: '',  },
  'added-size':          { targets: '.lkc3-added', prop: 'font-size', after: 'px',  },
  'added-height':        { targets: '.lkc3-added', prop: 'line-height', after: 'px',  },
  'added-bold':          { targets: '.lkc3-added', prop: '!bold' },
  'added-italic':        { targets: '.lkc3-added', prop: '!italic' },
  'added-underline':     { targets: '.lkc3-added', prop: '!underline' },

  'cat-color':           { targets: '.lkc3-cat', prop: 'color', after: '',  },
  'cat-bg-color':        { targets: '.lkc3-cat', prop: 'background-color', after: '',  },
  'cat-size':            { targets: '.lkc3-cat', prop: 'font-size', after: 'px',  },
  'cat-height':          { targets: '.lkc3-cat', prop: 'line-height', after: 'px',  },
  'cat-bold':            { targets: '.lkc3-cat', prop: '!bold' },
  'cat-italic':          { targets: '.lkc3-cat', prop: '!italic' },
  'cat-underline':       { targets: '.lkc3-cat', prop: '!underline' },

  'ex-transform-enabled':         createTransformGroupRule('.lkc3-external-wrap', 'ex-card-transform', 'enabled'),
  'ex-bg-enabled':                createToggleRule('.lkc3-external-wrap', ['background-color', 'background-image'], ['ex-bg-color']),
  'ex-border-enabled':            createToggleRule('.lkc3-external-wrap', ['border', 'border-color', 'border-style', 'border-width', 'border-radius'], ['ex-border-color', 'ex-border-style', 'ex-border-width', 'ex-border-radius']),
  'ex-shadow-enabled':            createShadowRule('.lkc3-external-wrap', 'ex-card-shadow', 'enabled'),
  'ex-heading-transform-enabled': createTransformGroupRule('.lkc3-external-wrap .lkc3-heading', 'ex-heading-transform', 'enabled'),
  'ex-heading-bg-enabled':        createToggleRule('.lkc3-external-wrap .lkc3-heading', ['background-color', 'background-image'], ['ex-heading-bg-color']),
  'ex-heading-border-enabled':    createToggleRule('.lkc3-external-wrap .lkc3-heading', ['border', 'border-color', 'border-style', 'border-width', 'border-radius'], ['ex-heading-border-color', 'ex-heading-border-style', 'ex-heading-border-width', 'ex-heading-border-radius']),
  'ex-heading-shadow-enabled':    createShadowRule('.lkc3-external-wrap .lkc3-heading', 'ex-heading', 'enabled'),
  'ex-thumbnail-border-enabled':  createToggleRule('.lkc3-external-wrap .lkc3-thumbnail-img', ['border', 'border-color', 'border-style', 'border-width', 'border-radius'], ['ex-thumbnail-border-color', 'ex-thumbnail-border-style', 'ex-thumbnail-border-width', 'ex-thumbnail-border-radius']),
  'ex-thumbnail-shadow-enabled':  createShadowRule('.lkc3-external-wrap .lkc3-thumbnail-img', 'ex-thumbnail', 'enabled'),
  'ex-more-bg-enabled':           createToggleRule('.lkc3-external-wrap .lkc3-more', ['background-color', 'background-image'], ['ex-more-bg-color']),
  'ex-more-border-enabled':       createToggleRule('.lkc3-external-wrap .lkc3-more', ['border', 'border-color', 'border-style', 'border-width', 'border-radius'], ['ex-more-border-color', 'ex-more-border-style', 'ex-more-border-width', 'ex-more-border-radius']),
  'in-bg-enabled':                createToggleRule('.lkc3-internal-wrap', ['background-color', 'background-image'], ['in-bg-color']),
  'in-border-enabled':            createToggleRule('.lkc3-internal-wrap', ['border', 'border-color', 'border-style', 'border-width', 'border-radius'], ['in-border-color', 'in-border-style', 'in-border-width', 'in-border-radius']),
  'in-transform-enabled':         createTransformGroupRule('.lkc3-internal-wrap', 'in-card-transform', 'enabled'),
  'in-shadow-enabled':            createShadowRule('.lkc3-internal-wrap', 'in-card-shadow', 'enabled'),
  'in-heading-bg-enabled':        createToggleRule('.lkc3-internal-wrap .lkc3-heading', ['background-color', 'background-image'], ['in-heading-bg-color']),
  'in-heading-border-enabled':    createToggleRule('.lkc3-internal-wrap .lkc3-heading', ['border', 'border-color', 'border-style', 'border-width', 'border-radius'], ['in-heading-border-color', 'in-heading-border-style', 'in-heading-border-width', 'in-heading-border-radius']),
  'in-heading-transform-enabled': createTransformGroupRule('.lkc3-internal-wrap .lkc3-heading', 'in-heading-transform', 'enabled'),
  'in-heading-shadow-enabled':    createShadowRule('.lkc3-internal-wrap .lkc3-heading', 'in-heading', 'enabled'),
  'in-thumbnail-border-enabled':  createToggleRule('.lkc3-internal-wrap .lkc3-thumbnail-img', ['border', 'border-color', 'border-style', 'border-width', 'border-radius'], ['in-thumbnail-border-color', 'in-thumbnail-border-style', 'in-thumbnail-border-width', 'in-thumbnail-border-radius']),
  'in-thumbnail-shadow-enabled':  createShadowRule('.lkc3-internal-wrap .lkc3-thumbnail-img', 'in-thumbnail', 'enabled'),
  'in-more-bg-enabled':           createToggleRule('.lkc3-internal-wrap .lkc3-more', ['background-color', 'background-image'], ['in-more-bg-color']),
  'in-more-border-enabled':       createToggleRule('.lkc3-internal-wrap .lkc3-more', ['border', 'border-color', 'border-style', 'border-width', 'border-radius'], ['in-more-border-color', 'in-more-border-style', 'in-more-border-width', 'in-more-border-radius']),
  'ex-more-shadow-enabled':       createShadowRule('.lkc3-external-wrap .lkc3-more', 'ex-more', 'enabled'),
  'in-more-shadow-enabled':       createShadowRule('.lkc3-internal-wrap .lkc3-more', 'in-more', 'enabled'),

  // HTML更新だけしたい（targets/propは使わない想定）
  'ex-content-type-1':   { targets: '', prop: '', after: '', html: '1' },
  'ex-content-type-2':   { targets: '', prop: '', after: '', html: '1' },
  'ex-content-type-3':   { targets: '', prop: '', after: '', html: '1' },
  'ex-content-type-4':   { targets: '', prop: '', after: '', html: '1' },
  'ex-content-type-5':   { targets: '', prop: '', after: '', html: '1' },
  'ex-info-type-1':   { targets: '', prop: '', after: '', html: '1' },
  'ex-info-type-2':   { targets: '', prop: '', after: '', html: '1' },
  'ex-info-type-3':   { targets: '', prop: '', after: '', html: '1' },
  'ex-info-type-4':   { targets: '', prop: '', after: '', html: '1' },
  'ex-info-type-5':   { targets: '', prop: '', after: '', html: '1' },

  'ex-info-text':        { targets: '', prop: '', after: '', html: '1' },
  'ex-heading-text':     { targets: '', prop: '', after: '', html: '1' },
  'ex-more-text':        { targets: '', prop: '', after: '', html: '1' },
  'ex-siteicon-alt':     { targets: '', prop: '', after: '', html: '1' },
  'ex-thumbnail-alt':    { targets: '', prop: '', after: '', html: '1' },

  'in-info-text':        { targets: '', prop: '', after: '', html: '1' },
  'in-heading-text':     { targets: '', prop: '', after: '', html: '1' },
  'in-more-text':        { targets: '', prop: '', after: '', html: '1' },
  'in-thumbnail-alt':    { targets: '', prop: '', after: '', html: '1' },
};
