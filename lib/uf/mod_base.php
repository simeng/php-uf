<?php

require_once("uf/template.php");

abstract class mod_base {
    public $options;

    public function __construct($options) {
        $this->options = $options;
    }

    protected function get_template() {
        return template::get_instance();
    }
    protected abstract function run($request);
}
