<?php

namespace wrdickson\apibook;

$f3 = \Base::instance();

//  CREATE ROOT SPACE
//  note the weird url . . . we used POST to require auth for a psuedo get
$f3->route('POST /root-spaces-create', function ( $f3 ) {
  $perms = [ 'permission' => 7, 'role' => 'create_root_spaces' ];
  //  the request should have 'Jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );

  $response = array();
  $response['f3auth'] = $f3auth;

  $params = json_decode($f3->get('BODY'));
  $response['params'] = $params;

  $beds = $params->beds;
  $child_of = $params->childOf;
  $display_order = $params->displayOrder;
  $people = $params->people;
  $show_children = $params->showChildren;
  $space_type = $params->spaceType;
  $title = $params->title;

  // TODO validate inputs!!!!!!

  $response['create'] = RootSpaces::create_root_space( $beds, $child_of, $display_order, $people, $show_children, $space_type, $title );
  if( $response['create'] > 0 ) {
    $root_spaces_pre_children = RootSpaces::get_root_spaces();
    $root_spaces_children_parents = array();
    foreach( $root_spaces_pre_children as $rspc ) {
      $rspc['children'] = RootSpaces::get_root_space_children($rspc['id']);
      $rspc['parents'] = RootSpaces::get_root_space_parents($rspc['id']);
      array_push($root_spaces_children_parents, $rspc);
    }
    $response['root_spaces_children_parents'] = $root_spaces_children_parents;
  }
  print json_encode($response);
});

//  UPDATE ROOT SPACE
$f3->route('PUT /root-spaces', function ( $f3, $args ) {
  $perms = [ 'permission' => 7, 'role' => 'edit_root_spaces' ];
  //  the request should have 'Jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );

  $response = array();
  $response['f3auth'] = $f3auth;
  $response['args'] = $args;
  $response['body'] = $f3->get('BODY');
  //  see https://stackoverflow.com/questions/73463955/fat-free-get-data-from-put-request
  parse_str($f3->get('BODY'), $p);
  $aArr = json_decode($f3->get('BODY'));
  $response['aArr'] = $aArr;
  $response['p'] = $p;
  $response['params'] = $f3['REQUEST'];


  print json_encode( $response );
});

//  GET ROOT SPACES
$f3->route('POST /root-spaces', function ( $f3 ) {
  $perms = [ 'permission' => 1, 'role' => 'get_root_spaces' ];
  //  the request should have 'Jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  
  $account = $f3auth['decoded']->account;
  $params = json_decode($f3->get('BODY'));
  
  $root_spaces_pre_children = RootSpaces::get_root_spaces();
  $root_spaces_children_parents = array();
  foreach( $root_spaces_pre_children as $rspc ) {
    $rspc['children'] = RootSpaces::get_root_space_children($rspc['id']);
    $rspc['parents'] = RootSpaces::get_root_space_parents($rspc['id']);
    array_push($root_spaces_children_parents, $rspc);
  }
  
  $response['account'] = $account;
  $response['params'] = $params;
  $response['root_spaces_pre_children'] = $root_spaces_pre_children;
  $response['root_spaces_children_parents'] = $root_spaces_children_parents;
  print json_encode($response);
});