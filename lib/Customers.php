<?php

namespace wrdickson\apibook;

use \PDO;
use \Exception;

Class Customers {

  public static function create_customer( $lastName, $firstName, $address1, $address2, $city, $region, $country, $postalCode, $phone, $email ){
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("INSERT INTO customers (lastName, firstName, address1, address2, city, region, country, postalCode, phone, email) VALUES (:ln, :fn, :a1, :a2, :ci, :re, :co, :pc, :ph, :em)");
    $stmt->bindParam(":ln", $lastName, PDO::PARAM_STR);
    $stmt->bindParam(":fn", $firstName, PDO::PARAM_STR);
    $stmt->bindParam(":a1", $address1, PDO::PARAM_STR);
    $stmt->bindParam(":a2", $address2, PDO::PARAM_STR);
    $stmt->bindParam(":ci", $city, PDO::PARAM_STR);
    $stmt->bindParam(":re", $region, PDO::PARAM_STR);
    $stmt->bindParam(":co", $country, PDO::PARAM_STR);
    $stmt->bindParam(":pc", $postalCode, PDO::PARAM_STR);
    $stmt->bindParam(":ph", $phone, PDO::PARAM_STR);
    $stmt->bindParam(":em", $email, PDO::PARAM_STR);
    $i = $stmt->execute();
    $insertId = $pdo->lastInsertId();
    return $insertId;
  }

  public static function search_customers( $lastName, $firstName, $offset, $limit ){
    $last = $lastName . "%";
    $first = $firstName ."%";
    $pdo = DataConnector::get_connection();
    //are lastName and firstName both > 1?
    if( strlen($last) > 1 && strlen($first) > 1 ){
      $stmt = $pdo->prepare("SELECT * FROM customers WHERE lastName LIKE :last AND firstName LIKE :first ORDER BY lastName, firstName ASC LIMIT :offset, :limit" );
      $stmt->bindParam(":last",$last,PDO::PARAM_STR);
      $stmt->bindParam(":first",$first,PDO::PARAM_STR);
      $stmt->bindParam(":offset", $offset);
      $stmt->bindParam(":limit", $limit);
      //  count
      $stmt_count = $pdo->prepare("SELECT * FROM customers WHERE lastName LIKE :last AND firstName LIKE :first");
      $stmt_count->bindParam(":last",$last,PDO::PARAM_STR);
      $stmt_count->bindParam(":first",$first,PDO::PARAM_STR);

    //is lastName >1 while firstName = 0?
    } elseif ( strlen($last) > 1 && strlen($first) == 0 ){
      $stmt = $pdo->prepare("SELECT * FROM customers WHERE lastName LIKE :last ORDER BY lastName, firstName ASC LIMIT :offset, :limit");
      $stmt->bindParam(":last",$last,PDO::PARAM_STR);
      $stmt->bindParam(":offset", $offset);
      $stmt->bindParam(":limit", $limit);
      //  count
      $stmt_count = $pdo->prepare("SELECT * FROM customers WHERE lastName LIKE :last");
      $stmt_count->bindParam(":last", $last);
    //is firstName > 1 and lastName = 0?
    } elseif ( strlen($first) > 1 && strlen($last) == 0 ){
      $stmt = $pdo->prepare("SELECT * FROM customers WHERE firstName LIKE :first ORDER BY lastName, firstName ASC LIMIT :offset, :limit");
      $stmt->bindParam(":first",$first,PDO::PARAM_STR);
      $stmt->bindParam(":offset", $offset);
      $stmt->bindParam(":limit", $limit);
      //  count
      $stmt_count = $pdo->prepare("SELECT * FROM customers WHERE firstName LIKE :first");
      $stmt_count->bindParam(":first", $first);
    //first and last are both 0 (ie empty)
    } else {
      $stmt = $pdo->prepare("SELECT * FROM customers WHERE lastName LIKE :last AND firstName LIKE :first ORDER BY lastName, firstName ASC LIMIT :offset,:limit");
      $stmt->bindParam(":last",$last,PDO::PARAM_STR);
      $stmt->bindParam(":first",$first,PDO::PARAM_STR);
      $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
      $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
      //  count
      $stmt_count = $pdo->prepare("SELECT * FROM customers WHERE lastName LIKE :last AND firstName LIKE :first");
      $stmt_count->bindParam(":last",$last,PDO::PARAM_STR);
      $stmt_count->bindParam(":first",$first,PDO::PARAM_STR);
    }
    $stmt->execute();
    $stmt_count->execute();
    $cArr = array();
    while($obj = $stmt->fetch(PDO::FETCH_OBJ)){
      $iCust = new Customer($obj->id);
      array_push($cArr, $iCust->to_array());
    }
    $result['customers'] = $cArr;
    $result['count'] = $stmt_count->rowCount();
    return $result;
  }

}
