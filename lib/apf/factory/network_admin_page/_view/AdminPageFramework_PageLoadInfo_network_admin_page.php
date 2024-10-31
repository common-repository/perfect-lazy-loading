<?php 
/**
	Admin Page Framework v3.8.18 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/admin-page-framework>
	Copyright (c) 2013-2018, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AdminPageFramework_PageLoadInfo_network_admin_page extends AdminPageFramework_PageLoadInfo_Base {
    private static $_oInstance;
    private static $aClassNames = array();
    public function __construct($oProp, $oMsg) {
        if (is_network_admin() && $this->isDebugMode()) {
            add_action('in_admin_footer', array($this, '_replyToSetPageLoadInfoInFooter'), 999);
        }
        parent::__construct($oProp, $oMsg);
    }
    public static function instantiate($oProp, $oMsg) {
        if (!is_network_admin()) {
            return;
        }
        if (in_array($oProp->sClassName, self::$aClassNames)) {
            return self::$_oInstance;
        }
        self::$aClassNames[] = $oProp->sClassName;
        self::$_oInstance = new AdminPageFramework_PageLoadInfo_network_admin_page($oProp, $oMsg);
        return self::$_oInstance;
    }
    public function _replyToSetPageLoadInfoInFooter() {
        if ($this->oProp->isPageAdded()) {
            add_filter('update_footer', array($this, '_replyToGetPageLoadInfo'), 999);
        }
    }
}
