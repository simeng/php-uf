<?php
class status_exception extends exception {
    public $code = null;

    public function __construct($msg, $code = 404) {
        $this->code = $code;
        parent::__construct($msg);
    }
}
