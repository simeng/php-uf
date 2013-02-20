<?php
require_once("uf/mod_base.php");

class mod_product extends mod_base {
    public function run($req) {
        if (isset($req['params']['product_id'])) {
            $html = "<p>Product Id: {$req['params']['product_id']}</p>";
        }

        $html .= "<p>Connect from ip {$req['remote_ip']}</p>";

        if (isset($req['referer']))
            $html .= "<p>Referer {$req['referer']}</p>";
        else
            $html .= "<p>No referer</p>";

        return $html;
    }
}
