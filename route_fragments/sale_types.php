<?php

namespace wrdickson\apibook;

$f3 = \Base::instance();

$f3->route('POST /sale-types/get-all/', function ( $f3 ) {
  $response['all_sale_types'] = SaleTypes::get_all_sale_types();
  print json_encode($response);
});
