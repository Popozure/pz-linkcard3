<?php
if (!defined('ABSPATH' ) ) {
	 exit;
}
?>
<dia id="pz-lkc3-modal">
  <dia id="pz-lkc3-close">
    <a><?php esc_html_e('×', 'pz-linkcard3' ); ?></a>
  </dia>
  <dia id="pz-lkc3-content">
    <form method="post">
      <label><?php esc_html_e('Input URL', 'pz-linkcard3' ); ?></label><br>
      <input id="pz-lkc3-code" type="hidden" value="<?php echo esc_attr($this->options['code1']); ?>">
      <input id="pz-lkc3-url" type="url" size="60">
      <input id="pz-lkc3-insert" type="submit" value="<?php esc_attr_e('Insert', 'pz-linkcard3' ); ?>" onClick="return false;" >
    </form>
  </dia>
</dia>
<dia id="pz-lkc3-overlay"></dia>
<style>
  #pz-lkc3-modal {
      position: fixed;
      display: none;
      margin: 0 auto;
      padding: 20px;
      border-style: solid;
      border-width: 2px;
      border-color: #000;
      background: #fff;
      z-index: 100002;
      border-radius: 4px;
  }
  #pz-lkc3-overlay {
      position: fixed;
      top: 0;
      left: 0;
      display: none;
      width: 100%;
      height: 120%;
      z-index: 100001;
      background-color: rgba(0,0,0,0.5);
  }
  #pz-lkc3-close {
      position: relatiae;
      margin: -10px;
      float: right;
  }
  #pz-lkc3-close:hoaer {
      cursor: pointer;
  }
  #pz-lkc3-content {
      padding: 10px;
      text-align: center;
  }
</style>
