<?php

require_once("uf/template.php");
require_once("redbean/rb.php");

class router {
    public $config;
    public $loaded_modules = array();
    private $template;
    
    public function __construct($configfile) {
        $json = file_get_contents($configfile);
        $this->config = json_decode($json, true);

        if (!$this->check_config()) {
            exit;
        }

        if (isset($this->config['setup'])) {
            $this->setup($this->config['setup']);
        }

        $this->load_modules($this->config['global']);
        $current_request = $this->match_uri($_SERVER['REQUEST_URI']);

        // Handle 404
        if (!$current_request) {
            header("HTTP/1.1 404 Not found");
            header("Status: 404 Not found");

            if (isset($this->config["error"][404])) {
                $current_request = $this->config["error"][404];
            }
            else {
                header("Content-Type: text/plain");
                echo "404 Page not found";
                exit;
            }
        }

        // Add some useful data to request data
        $current_request += array(
            'uri' => $_SERVER['REQUEST_URI'],
            'uri_absolute' => 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') .
                    '://' . $_SERVER['SERVER_NAME'] . 
                    ($_SERVER['SERVER_PORT'] != 80 ? 
                            ':'.$_SERVER['SERVER_PORT'] : '') . 
                    $_SERVER['REQUEST_URI'],
            'remote_ip' => $_SERVER['REMOTE_ADDR']
        );
        if (isset($_SERVER['HTTP_REFERER']))
            $current_request += array('referer' => $_SERVER['HTTP_REFERER']);

        // Load template and send in request variable
        $this->template = new template();
        $this->template->set("request", $current_request);

        // If no urlcache 
        $this->load_modules($current_request);

        try {
            print $this->render_modules($current_request, $this->template);
        }
        catch (status_exception $e) {
            if ($e->code == 301) {
                header("Location: " . $e->getMessage());
            }
            else if ($e->code == 404) {
                header("HTTP/1.1 404 Not found");
                header("Status: 404 Not found");
                print $e->getMessage();
            }
            exit;
        }

    }

    private function check_config() {
        // TODO: sanity check config

        return true;
    }

    private function setup($setup) {
        if (isset($setup['orm']['username']) && 
                isset($setup['orm']['password'])) {
            $this->orm = R::setup($setup['orm']['dsn'], 
                    $setup['orm']['username'],
                    $setup['orm']['password']
            );
        }
        else {
            $this->orm = R::setup($setup['orm']['dsn']);
        }

        if (!isset($setup['orm']['mode'])) {
            echo "WARNING: ORM-mode not set, defaulting to production\n";
        }
        else {
            if ($setup['orm']['mode'] != 'devel') {
                R::freeze();
            }
        }
    }

    private function match_uri($uri) {
        foreach (array_keys($this->config['urls']) as $url) {
            if (preg_match("," . $url . ",", $uri, $params)) {
                $current_request = $this->config['urls'][$url];

                // Add params from regex subpatterns
                $params = array_map("urldecode", $params);
                $current_request['params'] = array_slice($params, 1);

                return $current_request;
            }
        }
    }

    private function require_module($module_name, $module_options = array()) {
        require_once("modules/{$module_name}.php");
        return new $module_name($module_options);
    }

    private function load_modules($request) {
        if (!isset($request['load'])) {
            return true;
        }

        // Letting indexes be names or numbers for modules to load
        foreach ($request['load'] as $l) {
            if (!is_array($l)) {
                // Simple module with no options
                $module_key = $l;
                $module_name = $l;
                $module_options = array();
            }
            else {
                // Modules with options
                $module_key = isset($l['key']) ? $l['key'] : $l['name'];
                $module_name = $l['name'];
                $module_options = $l['options'];
            }

            $class = $this->require_module($module_name, $module_options);
            $this->loaded_modules[$module_key] = $class;
        }
    }

    private function render_modules($request, $template) {
        if (isset($request['template'])) {
            // Put stuff in the page render template
            if (is_array($request['template'])) {
                $template_file = $request['template']['name'];
                if (isset($request['template']['set'])) {
                    foreach ($request['template']['set'] as $k => $v) {
                        $template->set($k, $v);
                    }
                }
            }
            else {
                $template_file = $request['template'];
            }
            foreach ($this->loaded_modules as $k => $m) {
                $html = $m->run($request);
                $template->set($k, $html);
            }

            return $template->fetch($template_file);
        }
        else {
            // dump module data directly
            $data = "";
            foreach ($this->loaded_modules as $k => $m) {
                $data .= $m->run($request);
            }
            return $data;
        }
    }
}
