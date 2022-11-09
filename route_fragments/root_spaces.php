<?php

namespace wrdickson\apibook;

$f3->route('POST /root-spaces', function ( $f3 ) {
  $perms = [ 'permission' => 1, 'role' => 'get_root_spaces' ];
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  $account = $f3auth['auth']['decoded']->account;
  $params = $f3auth['request'];
  
  print json_encode($params);
  print json_encode($account);
});