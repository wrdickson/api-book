<?php

namespace wrdickson\apibook;

use wrdickson\apitest\Auth;

$f3 = \Base::instance();

$f3->route('POST /login',
  function( $f3 ) {
    $iAuth = new Auth( SERVER_NAME, JWT_KEY, DB_HOST, DB_NAME, DB_USER, DB_PASS);
    $response = array();
    $username = $f3['REQUEST']['username'];
    $password = $f3['REQUEST']['password'];
    $response = $iAuth->check_login( $username, $password );
    print json_encode($response);
  }
);