<?php if (!defined( 'ABSPATH' ) ) { header( 'HTTP/1.1 403 Forbidden' ); exit; } ?>

<div class="pz-color-picker-panel" id="colorPicker">
  <div class="pz-sv-area" id="svArea">
    <div class="pz-sv-white"></div>
    <div class="pz-sv-black"></div>
    <div class="pz-sv-cursor" id="svCursor"></div>
  </div>

  <div class="pz-slider pz-hue-slider" id="hueSlider">
    <div class="pz-slider-thumb" id="hueThumb"></div>
  </div>

  <div class="pz-slider pz-alpha-slider" id="alphaSlider">
    <div class="pz-alpha-inner" id="alphaInner"></div>
    <div class="pz-slider-thumb" id="alphaThumb"></div>
  </div>

  <div class="pz-preview-row">
    <div class="pz-preview-circle" id="previewCircle"></div>

    <!-- モード切替（button化） -->
    <button type="button" id="modeLabel" class="pz-mode-label is-hex"></button>

    <div class="pz-input-wrap" id="inputWrap"></div>
  </div>

  <div class="pz-palette" id="palette"></div>
</div>
