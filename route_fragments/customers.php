<?php

namespace wrdickson\apibook;

$f3 = \Base::instance();

//  Create customer
$f3->route('POST /customers/', function ( $f3 ) {
  $perms = [ 'permission' => 2, 'role' => 'create_customer' ];
  //  the request should have 'jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );

  $params = $f3['REQUEST'];
  $fistName = $params['firstName'];
  $lastName = $params['lastName'];
  $email = $params['email'];
  $phone = $params['phone'];
  $address1 = $params['address1'];
  $address2 = $params['address2'];
  $city = $params['city'];
  $region = $params['region'];
  $postal_code = $params['postalCode'];

  //  TODO validate inputs

  $response = array();
  $response['params'] = $params;
  $response['createCustomer'] = Customers::create_customer( $params['lastName'], $params['firstName'], '', '', '', '', '', '', 
  $params['phone'], $params['email']);
  if( $response['createCustomer'] && $response['createCustomer'] > 0 ) {
    $new_customer = new Customer($response['createCustomer']);
    $response['newCustomer'] = $new_customer->to_array();
  }
  print json_encode($response);


});


//  Search Customers
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

  print json_encode(Customers::search_customers( $last_name, $first_name, $offset, $limit ));
});