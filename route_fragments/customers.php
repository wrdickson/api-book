<?php

namespace wrdickson\apibook;

use wrdickson\apitest\Auth;

$f3 = \Base::instance();

$f3->route('POST /customers/search', function ( $f3 ) {
  $perms = [ 'permission' => 1, 'role' => 'search_customers' ];
  //  the request should have 'jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  
  //$account = $f3auth['decoded']->account;

  $params = $f3['REQUEST'];
  $last_name = $params['lastName'];
  $first_name = $params['firstName'];
  $offset = $params['offset'];
  $limit = $params['limit'];
  $response = array();
  

  print json_encode(Customer::searchCustomers( $last_name, $first_name, $offset, $limit ));
});