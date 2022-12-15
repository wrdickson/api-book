<?php

namespace wrdickson\apibook;

use \PDO;
use \Exception;

Class Folio{
  //$id, $customer, and $reservaton are from the folio table
  private $id;
  private $customer;
  private $reservation;
  //$invoices is generated from the sales table
  private $invoices;

  public function __construct( $id ) {

    $pdo = DataConnector::get_connection();

    //first get the basics: id, customer, reservation
    $stmt = $pdo->prepare("SELECT * FROM folios WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    while($obj = $stmt->fetch(PDO::FETCH_OBJ)){
      $this->id = $obj->id;
      $this->customer = $obj->customer;
      $this->reservation = $obj->reservation;
      
    }
    //second, get the invoices
    $stmt = $pdo->prepare("SELECT * FROM invoices WHERE folio = :f");
    $stmt->bindParam(':f', $id);
    $ex = $stmt->execute();
    $inv = array();
    while($obj = $stmt->fetch(PDO::FETCH_OBJ)){
      $arr = array();
      $arr['id'] = $obj->id;
      $arr['customer'] = $obj->customer;
      $arr['folio'] = $obj->folio;
      array_push( $inv, $arr );
    }
    // temporary invoices, just customer and folio
    $this->invoices = $inv;

    //  populate the temporary invoices with 1- saleItems, 2-  payments
   
    foreach($this->invoices as $index => $invoice ) {
      $invoice_id = $invoice['id'];
       //  1. sale items:
      $stmt = $pdo->prepare("SELECT * FROM sale_items WHERE invoice = :i");
      $stmt->bindParam(':i', $invoice_id);
      $i = $stmt->execute();
      $items_arr = array();
      while($obj = $stmt->fetch(PDO::FETCH_OBJ)) {
        $arr = array();
        $arr['id'] = $obj->id;
        $arr['invoice'] = $obj->invoice;
        $arr['description'] = $obj->description;
        $arr['sale_datetime'] = $obj->sale_datetime;
        $arr['sale_type'] = $obj->sale_type;
        $arr['sale_quantity'] = $obj->sale_quantity;
        $arr['sale_price'] = $obj->sale_price;
        $arr['sale_subtotal'] = $obj->sale_subtotal;
        $arr['sale_tax'] = $obj->sale_tax;
        $arr['sale_total'] = $obj->sale_total;
        $arr['tax_types'] = json_decode( $obj->tax_types, true );
        $arr['tax_spread'] = json_decode( $obj->tax_spread, true );
        array_push( $items_arr, $arr );
      }
      //  assign the value
      $this->invoices[$index]['sale_items'] = $items_arr;
      // 2. payments
      $stmt = $pdo->prepare("SELECT * FROM payments WHERE invoice = :i");
      $stmt->bindParam(':i', $invoice_id);
      $i = $stmt->execute();
      $payments_arr = array();
      while( $obj = $stmt->fetch(PDO::FETCH_OBJ)) {
        $arr = array();
        $arr['id'] = $obj->id;
        $arr['invoice'] = $obj->invoice;
        $arr['customer'] = $obj->customer;
        $arr['datetime_posted'] = $obj->datetime_posted;
        $arr['subtotal'] = $obj->subtotal;
        $arr['tax'] = $obj->tax;
        $arr['total'] = $obj->total;
        $arr['payment_type'] = $obj->payment_type;
        $arr['posted_by'] = $obj->posted_by;
        array_push($payments_arr, $arr);
      }
      // assign the value
      $this->invoices[$index]['payments'] = $payments_arr;
    };
  }

  public function get_id(){
    return $this->id;
  }
  
  public function to_array(){
    $arr = array();
    $arr['id'] = $this->id;
    $arr['customer'] = $this->customer;
    $arr['reservation'] = $this->reservation;
    $arr['invoices'] = $this->invoices;
    return $arr;
  }
}
