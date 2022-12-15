<?php

namespace wrdickson\apibook;

$f3->route('POST /payments/quick-folio-sale', function ( $f3 ) {
  $perms = [ 'permission' => 3, 'role' => 'post_payment' ];
  //  the request should have 'Jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  $response['auth'] = $f3auth;
  
  $params = json_decode($f3->get('BODY'), true );
  $response['params'] = $params;

  //  get useful variables
  $customer = $params['resCustomer'];
  $payment_type = $params['paymentType'];
  $res_folio = $params['resFolio'];
  $res_id = $params['resId'];
  $sale_items = $params['saleItems'];
  $sale_subtotal = $params['saleSubtotal'];
  $sale_tax = $params['saleTax'];
  $sale_total = $params['saleTotal'];
  $sold_by = $params['soldBy'];

  //  this validation should be much more robust,
  //  but this should toss horribly malformed requests
  $options = array(
    'paymentType' => array (
      'is_integer'
    ),
    'resCustomer' => array (
      'is_integer'
    ),
    'resFolio' => array (
      'is_integer'
    ),
    'resId' => array (
      'is_integer',
    ),
    'saleSubtotal' => array (
      'is_float'
    ),
    'saleTax' => array (
      'is_float'
    ),
    'saleTotal' => array (
      'is_float'
    ),
    'soldBy' => array (
      'is_float'
    ),
    'saleItems' => array (
      'is_array'
    )
  );

  $v = new Validate( $params, $options);
  $v_result = $v->validate();

  $response['validate'] = $v_result;
  if($v_result['valid']) {
    //  carry on  . . .
    //  TODO this needs to be done as a transaction!
    //  TODO all bits of the transaction need the same datetime . . . don't use NOW()

    //  1. create an invoice
    $invoice_id = Invoices::create_invoice( $res_folio, $customer);
    $response['invoiceId'] = $invoice_id;
    //  2. iterate through the sale items and create
    $sale_items_created = array();
    $i = 0;
    foreach($sale_items as $sale_item) {
      $sale_items_created[$i] = SaleItems::create_sale_item( $invoice_id, $sale_item['description'], $sale_item['saleType'], $sale_item['saleQuantity'], $sale_item['salePrice'], $sale_item['saleSubtotal'], $sale_item['saleTax'], $sale_item['saleTotal'], $sale_item['taxTypes'], $sale_item['taxSpread'] );
      $i+=1;
    }
    $response['sale_items_created'] = $sale_items_created;
    //  3. post a payment
    $payment_posted = Payments::create_payment($invoice_id, $customer, $sale_subtotal, $sale_tax, $sale_total, $payment_type, $sold_by);
    $response['payment_id'] = $payment_posted;

    print json_encode($response);

  } else {
    $f3->error(400, 'marams malformed or missing');
  }
});

