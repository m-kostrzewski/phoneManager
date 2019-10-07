<?php
defined("_VALID_ACCESS") || die('Direct access forbidden');

class phoneManagerInstall extends ModuleInstall {

    public function install() {
        $ret = true;
        Base_ThemeCommon::install_default_theme($this->get_type());
        Base_LangCommon::install_translations($this->get_type());
    
        return $ret; 
    }

    public function uninstall() {
        $ret = true;
        return $ret; 
    }

    public function requires($v) {
        return array(); 
    }
    public function info() {
        return array (
                'Author' => 'Mateusz Kostrzewski',
                'License' => 'MIT 1.0',
                'Description' => '' 
        );
    }
    public function version() {

        return array('1.0'); 
    }
    public function simple_setup() { 
        return array (
                'package' => __ ( 'Phone Manager' ),
                'version' => '0.1' 
        ); 
    }
}