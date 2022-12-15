<?php

namespace wrdickson\apibook;

class SaleItems {

  public static function create_sale_item( $invoice, 
                                          $description, 
                                          $sale_type, 
                                          $sale_quantity, 
                                          $sale_price, 
                                          $sale_subtotal, 
                                          $sale_tax, 
                                          $sale_total, 
                                          $tax_types, 
                                          $tax_spread) {
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("INSERT INTO sale_items ( invoice, description, sale_datetime, sale_type, sale_quantity, sale_price, sale_subtotal, sale_tax, sale_total, tax_types, tax_spread ) VALUES ( :i, :d, NOW(), :st, :sq, :sp, :ss, :stx, :stot, :tt, :ts )");
    $stmt->bindParam(':i', $invoice);
    $stmt->bindParam(':d', $description);
    $stmt->bindParam(':st', $sale_type);
    $stmt->bindParam(':sq', $sale_quantity);
    $stmt->bindParam(':sp', $sale_price);
    $stmt->bindParam(':ss', $sale_subtotal);
    $stmt->bindParam(':stx', $sale_tax);
    $stmt->bindParam(':stot', $sale_total);
    $stmt->bindParam(':tt', json_encode($tax_types));
    $stmt->bindParam(':ts', json_encode($tax_spread));
    $i = $stmt->execute();
    $insertId = $pdo->lastInsertId();
    return $insertId;
  }
}
