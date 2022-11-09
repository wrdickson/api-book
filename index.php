<?php

namespace wrdickson\apibook;

require 'config/config.php';
require 'vendor/autoload.php';
require 'lib/F3Auth.php';

$a - 4;


$f3 = \Base::instance();

$f3->route('GET /hello',
    function( $f3 ) {
      print json_encode( $f3['REQUEST'] );
    }
);

//  handles login
require 'route_fragments/login.php';

//  handles RootSpaces & Root Space
require 'route_fragments/root_spaces.php';




$f3->run();
