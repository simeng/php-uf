<?php 

require_once("smarty/Smarty.class.php");

class template {
    private $template;
    private static $self;

    public function __construct() {
        $this->template = new Smarty();
        $this->template->template_dir = '../templates';
        $this->template->compile_dir = '../templates_c';
        self::$self = $this;
    }

    public static function get_instance() {
        return self::$self;
    }

    public function set($key, $value) {
        $this->template->assign($key, $value);
    }
    public function fetch($file) {
        return $this->template->fetch($file);
    }
}
