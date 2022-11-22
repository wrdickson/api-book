<?php

namespace wrdickson\apibook;

use \PDO;

Class Folios {

  public static function create_folio( $resId, $customerId ) {
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("INSERT INTO folios ( customer, reservation ) VALUES ( :custId, :resId )");
    $stmt->bindParam(":custId", $customerId, PDO::PARAM_INT);
    $stmt->bindParam(":resId", $resId, PDO::PARAM_INT);
    
    $execute = $stmt->execute();
    $id = $pdo->lastInsertId();
    $response = array();
    $response['execute'] = $execute;
    $response['newId'] = $id;
    return $response['newId'];
  }

}
