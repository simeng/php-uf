The microframework(tm).

Yet another tiny framework for creating websites in PHP.


SETUP
=====

Needs the following stuff

Smarty v3+ Templating
    http://www.smarty.net/download
RedBean ORM
    http://redbeanphp.com/downloadredbean.php


FILESTRUCTURE
-------------

  Project files
    www/index.php - apache rewrite everything to this
    config/route.json - json url router config
    lib/modules/* - Base dir for all modules

  Framework files
    lib/uf/* - The framework
    lib/uf/mod_base.php - The basemodule that all modules loaded with "load" should extend.

  Templating files
    lib/smarty/ - Put contents of libs/-dir from downloaded smarty files here
    templates/* - Template source files
    templates_c/* - Output dir for smarty (must be writable by webserver)

  Redbean ORM
    lib/redbean/ - Put redbean files here


config/route.json
-----------------

Routes all regex matches in "urls" to corresponding configs
A route match expects "load" and "template".  It will load modules named 
in "load" and render their output to the template named in "template" with
the module output named as template variable as specified in 
"id" (if specified) or "name"

Example:

    {
        "setup": {
            "orm": {
                "username": "username",
                "password": "password",
                "dsn": "mysql:host=localhost;dbname=db",
                "mode": "devel" 
            }
        },
        "globals": {
            "load": [
                "mod_header", 
                "mod_footer"
            ]
        },
        "urls": {
            "^/?$": {
                "load": [
                    { 
                        "name": "mod_rss", 
                        "options": { 
                            "src": "http://uf-php.net/rss.xml"
                        },
                        "id": "uf_rss"
                    }
                ],
                "template": {
                    "name": "index.tpl"
                }
            },
            "^/category/(?P<category>[^/]+)/?$": {
                "template": {
                    "name": "categories/index.tpl"
                }
            },
            "^/category/(?P<category>[^/]+)/edit/?$": {
                "template": {
                    "name": "categories/edit.tpl"
                }
            },
            "^/about$": { 
                "load": [ "mod_about" ],
                "template": { 
                    "name": "about.tpl",
                    "set": {
                        "authors": [
                            {"name": "Simen Graaten", "email": "simeng@gmail.com"}
                        ]
                    }
                }
            }
        }
    }


magical template variables
--------------------------

You have access to `$request` in all smarty templates.
It's an array that looks something like this:

    Array
    (
        [template] => Array
            (
                [name] => category/index.tpl
            )

        [load] => Array
            (
                [0] => Array
                    (
                        [name] => mod_category
                        [options] => Array
                            (
                            )

                    )

            )

        [params] => Array
            (
                [category] => stuff
                [0] => stuff
            )

        [uri] => /category/stuff/
        [uri_absolute] => http://mysite.no/category/stuff/
        [remote_ip] => 10.0.0.1
        [referer] => http://mysite.no/category/
    )

Contents returned for the run()-method in modules will also be available as 
the name or the id of the module from the config, so `$mod_category` in the 
above example.


