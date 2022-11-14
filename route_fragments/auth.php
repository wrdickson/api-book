<?php

namespace wrdickson\apibook;

use wrdickson\apitest\Auth;

$f3 = \Base::instance();

/**
 * ftn requires SERVER_NAME, JWT_KEY, DB_HOST, DB_NAME, DB_USER, DB_PASS as DEFINED vars
 */
$f3->route('POST /login', function( $f3 ) {
  $iAuth = new Auth( SERVER_NAME, JWT_KEY, DB_HOST, DB_NAME, DB_USER, DB_PASS);
  print json_encode( $iAuth->check_login( $f3['REQUEST']['username'], $f3['REQUEST']['password'] ) );
});

$f3->route('POST /authorize-token', function( $f3 ) {
  $perms = ['permission'=> 1, 'role'=>'void'];
  $r = F3Auth::authorize_token( $f3, $perms );
  print json_encode($r);
});


