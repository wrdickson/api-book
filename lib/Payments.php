<?php

namespace wrdickson\apibook;

Class Payments {

  public static function create_payment( $invoice, $customer, $subtotal, $tax, $total, $payment_type, $posted_by) {
    $pdo = Dataconnector::get_connection();
    $stmt = $pdo->prepare("INSERT INTO payments ( invoice, customer, datetime_posted, subtotal, tax, total, payment_type, posted_by) Values ( :inv, :cus, NOW(), :sbt, :tx, :ttl, :pyt, :pby)");
    $stmt->bindParam(':inv', $invoice);
    $stmt->bindParam(':cus', $customer);
    $stmt->bindParam(':sbt', $subtotal);
    $stmt->bindParam(':sbt', $subtotal);
    $stmt->bindParam(':tx', $tax);
    $stmt->bindParam(':ttl', $total);
    $stmt->bindParam(':pyt', $payment_type);
    $stmt->bindParam(':pby', $posted_by);
    $i = $stmt->execute();
    $insertId = $pdo->lastInsertId();
    return $insertId;
  }

}
