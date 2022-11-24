<?php

namespace wrdickson\apibook;

use wrdickson\apitest\Auth;
use \Exception;
use FFI\Exception as FFIException;

$f3 = \Base::instance();

/**
 *  CREATE RESERVATION
 * 
 */
$f3->route('POST /reservations', function ( $f3 ) {
  $perms = ['permission' => 2, 'role' => 'create_reservation' ];
  $f3auth = F3Auth::authorize_token( $f3, $perms);

  $account = $f3auth['decoded']->account;
  $params = $f3['REQUEST'];

  //  validate params

  $params_valid = true;
  try {
  $response['create'] = Reservations::create_reservation( $params['checkin'],
                                                          $params['checkout'],
                                                          $params['customer']['id'],
                                                          $params['space_id'],
                                                          $params['people'],
                                                          $params['beds'] );
  } catch (Exception $e) {
    $response['e'] = $e;
  }

  $response['account'] = $account;
  $response['params'] = $params;
  print json_encode($response);
});

$f3->route('POST /reservations/availability', function ( $f3 ) {
  $perms = [ 'permission' => 0, 'role' => 'get_availability' ];
  //  the request should have 'Jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );

  $account = $f3auth['decoded']->account;
  $params = $f3['REQUEST'];

  $response['account'] = $account;
  $response['params'] = $params;
  $response['availability'] = Reservations::check_availability_by_dates( $params['startDate'], $params['endDate'] );
  print json_encode($response);
});

$f3->route('POST /reservations/conflicts', function ( $f3 ) {
  $perms = [ 'permission' => 1, 'role' => 'check_conflicts' ];
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  $start = $f3['REQUEST']['startDate'];
  $end = $f3['REQUEST']['endDate'];
  $space_id = $f3['REQUEST']['spaceId'];

  $response['f3auth'] = $f3auth;
  $response['checkConflicts'] = Reservations::check_conflicts( $start, $end, $space_id );
  print json_encode( $response );
});

$f3->route('POST /reservations/range', function( $f3 ) {
  $perms = [ 'permission' => 1, 'role' => 'get_reservations' ];
  //  the request should have 'Jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  
  $account = $f3auth['decoded']->account;
  $params = $f3['REQUEST'];

  $response['account'] = $account;
  $response['params'] = $params;
  $response['reservations'] = Reservations::get_reservations_date_range($params['startDate'], $params['endDate']);
  print json_encode($response);
});

