<?php

namespace wrdickson\apibook;

Class Invoices {

  public static function create_invoice ( $folio, $customer ) {
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("INSERT INTO invoices ( customer, folio ) VALUES ( :cu, :fo )");
    $stmt->bindParam(':cu', $customer);
    $stmt->bindParam(':fo', $folio);
    $i = $stmt->execute();
    $insertId = $pdo->lastInsertId();
    return $insertId;
  }

}
