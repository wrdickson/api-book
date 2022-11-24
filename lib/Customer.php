<?php

namespace wrdickson\apibook;

use \PDO;

Class Customer {
  private $id;
  private $lastName;
  private $firstName;
  private $address1;
  private $address2;
  private $city;
  private $region;
  private $country;
  private $postalCode;
  private $email;
  private $phone;
  
  public function __construct($id){
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = :id");
    $stmt->bindParam(":id",$id,PDO::PARAM_INT);
    $stmt->execute();
    while($obj = $stmt->fetch(PDO::FETCH_OBJ)){
      $this->id = $obj->id;
      $this->lastName = $obj->lastName;
      $this->firstName = $obj->firstName;
      $this->address1 = $obj->address1;
      $this->address2 = $obj->address2;
      $this->city = $obj->city;
      $this->region = $obj->region;
      $this->country = $obj->country;
      $this->postalCode = $obj->postalCode;
      $this->phone = $obj->phone;
      $this->email = $obj->email;
    }
  }
  
  public function to_array(){
    $arr = array();
    $arr['id'] = $this->id;
    $arr['lastName'] = $this->lastName;
    $arr['firstName'] = $this->firstName;
    $arr['address1'] = $this->address1;
    $arr['address2'] = $this->address2;
    $arr['city'] = $this->city;
    $arr['region'] = $this->region;
    $arr['country'] = $this->country;
    $arr['postalCode'] = $this->postalCode;
    $arr['phone'] = $this->phone;
    $arr['email'] = $this->email;
    return $arr;
  }
  
  public static function getCustomers(){
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("SELECT * FROM customers ORDER BY lastName ASC");
    $stmt->execute();
    $cArr = array();
    while($obj = $stmt->fetch(PDO::FETCH_OBJ)){
      $iArr =array();
      $iArr['id'] = $obj->id;
      $iArr['lastName'] = $obj->lastName;
      $iArr['firstName'] = $obj->firstName;
      $iArr['address1'] = $obj->address1;
      $iArr['address2'] = $obj->address2;
      $iArr['city'] = $obj->city;
      $iArr['region'] = $obj->region;
      $iarr['country'] = $obj->country;
      $iArr['postalCode'] = $obj->postalCode;
      $iArr['phone'] = $obj->phone;
      $iArr['email'] = $obj->email;
      array_push($cArr, $iArr);
    }
    return $cArr;
  }
  
  public function update(){
    //TODO validate
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("UPDATE customers SET lastName = :lastName, firstName = :firstName, address1 = :address1, address2 = :address2, city = :city, region = :region, country = :country, postalCode = :postalCode, phone = :phone, email = :email WHERE id=:id");
    $stmt->bindParam(":lastName", $this->lastName, PDO::PARAM_STR);
    $stmt->bindParam(":firstName", $this->firstName, PDO::PARAM_STR);
    $stmt->bindParam(":address1", $this->address1, PDO::PARAM_STR);
    $stmt->bindParam(":address2", $this->address2, PDO::PARAM_STR);
    $stmt->bindParam(":city", $this->city, PDO::PARAM_STR);
    $stmt->bindParam(":region", $this->region, PDO::PARAM_STR);
    $stmt->bindParam(":country", $this->country, PDO::PARAM_STR);
    $stmt->bindParam(":postalCode", $this->postalCode, PDO::PARAM_STR);
    $stmt->bindParam(":phone", $this->phone, PDO::PARAM_STR);
    $stmt->bindParam(":email", $this->email, PDO::PARAM_STR);
    $stmt->bindParam(":id", $this->id, PDO::PARAM_STR);
    $success = $stmt->execute();
    return $success;
  }
  
  //getters
  public function get_id(){
    return $this->id;
  }
  public function get_last_name(){
    return $this->lastName;
  }
  public function get_first_name(){
    return $this->firstName;
  }
  public function get_address1(){
    return $this->address1;
  }
  public function get_address2(){
    return $this->address2;
  }
  public function get_city(){
    return $this->city;
  }
  public function get_region(){
    return $this->region;
  }
  public function get_country(){
    return $this->country;
  }
  public function get_postal_code(){
    return $this->postalCode;
  }
  public function get_email(){
    return $this->email;
  }
  public function get_phone(){
    return $this->phone;
  }
}
