<?php

namespace wrdickson\apibook;

require 'lib/Reservations.php';
require 'lib/Customer.php';

use wrdickson\apitest\Auth;

$f3 = \Base::instance();

$f3->route('POST /reservations/range', function( $f3 ) {
  $perms = [ 'permission' => 1, 'role' => 'get_reservations' ];
  //  the request should have 'jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  
  $account = $f3auth['decoded']->account;
  $params = $f3['REQUEST'];

  $response['account'] = $account;
  $response['params'] = $params;
  $response['reservations'] = Reservations::getReservationsDateRange($params['startDate'], $params['endDate']);
  print json_encode($response);
});

