<?php

namespace wrdickson\apibook;

require 'lib/RootSpaces.php';
require 'lib/RootSpace.php';

$f3->route('POST /root-spaces', function ( $f3 ) {
  $perms = [ 'permission' => 1, 'role' => 'get_root_spaces' ];
  //  the request should have 'jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  
  $account = $f3auth['decoded']->account;
  $params = $f3['REQUEST'];
  
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