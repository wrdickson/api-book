<?php

namespace wrdickson\apibook;

use \PDO;

Class Reservation{
  public $id;
  public $space_id;
  public $space_code;
  public $checkin;
  public $checkout;
  public $people;
  public $beds;
  public $folio;
  public $folio_obj;
  /**
   * Status:
   * 0 - Checked in/ in house
   * 1 - Checked out/ not in house
   */
  public $status;
  public $history;
  public $notes;
  public $customer;
  public $customer_obj;

  public function __construct($id){
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    while($obj = $stmt->fetch(PDO::FETCH_OBJ)){
      $this->id = $obj->id;
      $this->space_id = $obj->space_id;
      $this->space_code = $obj->space_code;
      $this->checkin = $obj->checkin;
      $this->checkout = $obj->checkout;
      $this->people = $obj->people;
      $this->beds = $obj->beds;
      $this->folio = $obj->folio;
      $iFolio = new Folio( $obj->folio );
      $this->folio_obj = $iFolio->to_array();
      $this->status = $obj->status;
      $this->history = json_decode($obj->history, true);
      $this->notes = json_decode($obj->notes, true);
      $this->customer = $obj->customer;
      $iCustomer = new Customer($obj->customer);
      $this->customer_obj = $iCustomer->to_array();
    }
  }

  // note is a assoc array 
   public function addNote( $note ){
    $arr = $this->notes;
    $x = array_push($arr, $note);
    $this->notes = $arr;
    return $this->notes;
  }

  public function add_history( $history_text, $user_id, $user_name ){
    $historyArr = array();
    $historyArr['date'] = date('Y-m-j H:i:s');
    $historyArr['account_id'] = $user_id;
    $historyArr['account_name'] = $user_name;
    $historyArr['text'] = $history_text;
    $arrpush = array_push($this->history, $historyArr);
    //$this->history = $arrpush;
    $updateSuccess = $this->update_to_db();
    return $updateSuccess;
  }

  public function checkin(){
    $this->status = 1;
    return $this->update_to_db();
  }

  public function checkout(){
    $this->status = 0;
    return $this->update_to_db();
  }
  
  public function to_array(){
    $arr = array();
    $arr['id'] = $this->id;
    $arr['space_id'] = $this->space_id;
    $arr['space_code'] = $this->space_code;
    $arr['checkin'] = $this->checkin;
    $arr['checkout'] = $this->checkout;
    $arr['people'] = $this->people;
    $arr['beds'] = $this->beds;
    $arr['folio'] = $this->folio;
    $arr['folio_obj'] = $this->folio_obj;
    $arr['status'] = $this->status;
    $arr['history'] = $this->history;
    $arr['notes'] = $this->notes;
    $arr['customer'] = $this->customer;
    $arr['customer_obj'] = $this->customer_obj;
    return $arr;
  }

  public function update_to_db(){
    $historyJson = json_encode($this->history);
    $notesJson = json_encode($this->notes);
    $pdo2 = DataConnector::get_connection();
    $stmt = $pdo2->prepare("UPDATE reservations SET space_id = :si, space_code = :sc, checkin = :ci, checkout = :co, people = :pe, beds = :be, folio = :fo, status = :st, history = :hi, notes = :nt, customer = :cu WHERE id = :id");
    $stmt->bindParam(":si", $this->space_id, PDO::PARAM_INT);
    $stmt->bindParam(":sc", $this->space_code, PDO::PARAM_STR);
    $stmt->bindParam(":ci", $this->checkin, PDO::PARAM_STR);
    $stmt->bindParam(":co", $this->checkout, PDO::PARAM_STR);
    $stmt->bindParam(":pe", $this->people, PDO::PARAM_INT);
    $stmt->bindParam(":be", $this->beds, PDO::PARAM_INT);
    $stmt->bindParam(":fo", $this->folio, PDO::PARAM_INT);
    $stmt->bindParam(":st", $this->status, PDO::PARAM_INT);
    $stmt->bindParam(":hi", $historyJson, PDO::PARAM_STR);
    $stmt->bindParam(":nt", $notesJson, PDO::PARAM_STR);
    $stmt->bindParam(":cu", $this->customer, PDO::PARAM_STR);
    $stmt->bindParam(":id", $this->id, PDO::PARAM_STR);
    $execute = $stmt->execute();
    $error = $stmt->errorInfo(); 
    return $execute;
  }

  public static function update_from_params( $resId, $space_id, $space_code, $checkin, $checkout, $people, $beds, $folio, $status, $history, $notes, $customer){
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("UPDATE reservations SET space_id = :si, space_code = :sc, checkin = :ci, checkout = :co, people = :pe, beds = :be, folio = :fo, status=:st, history = :hi, notes = :nt, customer = :cu WHERE id = :id");
    $stmt->bindParam(":si", $space_id, PDO::PARAM_INT);
    $stmt->bindParam(":sc", $space_code, PDO::PARAM_STR);
    $stmt->bindParam(":ci", $checkin, PDO::PARAM_STR);
    $stmt->bindParam(":co", $checkout, PDO::PARAM_STR);
    $stmt->bindParam(":pe", $people, PDO::PARAM_INT);
    $stmt->bindParam(":be", $beds, PDO::PARAM_INT);
    $stmt->bindParam(":fo", $folio, PDO::PARAM_INT);
    $stmt->bindParam(":st", $status, PDO::PARAM_INT);
    $stmt->bindParam(":hi", $history, PDO::PARAM_STR);
    $stmt->bindParam(":nt", $notes, PDO::PARAM_STR);
    $stmt->bindParam(":cu", $customer, PDO::PARAM_STR);
    $stmt->bindParam(":id", $resId, PDO::PARAM_STR);
    $execute = $stmt->execute();
    $error = $stmt->errorInfo(); 
    return $execute;
  }

  public static function getReservation($id){
        $pdo = DataConnector::get_connection();
        $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = :id");
        $stmt->bindParam(":id",$id,PDO::PARAM_INT);
        $stmt->execute();
        $r = array();
        while($obj = $stmt->fetch(PDO::FETCH_OBJ)){
          $r['id'] = $obj->id;
          $r['space_code'] = $obj->space_code;
          $r['space_id'] = $obj->space_id;
          $r['checkin'] = $obj->checkin;
          $r['checkout'] = $obj->checkout;
          $r['people'] = $obj->people;
          $r['beds'] = $obj->beds;
          $r['folio'] = $obj->folio;
          $r['status'] = $obj->status;
          $r['history'] = json_decode($obj->history, true);
          $r['notes'] = json_decode($obj->notes, true);
          $r['customer'] = $obj->customer;
          $iCustomer = new Customer($obj->customer);
          $r['customer_obj'] = $iCustomer->to_array();
        }
        $folio = new Folio( $r['folio'] );
        $r['folio_obj'] = $folio->to_array();

        return $r;
  }

  public static function updateReservation1( $resId, $beds, $checkin, $checkout, $customer, $folio, $people, $space_code, $space_id, $status ){
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("UPDATE reservations SET space_id = :si, space_code = :sc, checkin = :ci, checkout = :co, people = :pe, beds = :be, folio = :fo, status=:st, customer = :cu WHERE id = :id");
    $stmt->bindParam(":si", $space_id);
    $stmt->bindParam(":sc", $space_code);
    $stmt->bindParam(":ci", $checkin);
    $stmt->bindParam(":co", $checkout);
    $stmt->bindParam(":pe", $people);
    $stmt->bindParam(":be", $beds);
    $stmt->bindParam(":fo", $folio);
    $stmt->bindParam(":st", $status);
    $stmt->bindParam(":cu", $customer);
    $stmt->bindParam(":id", $resId);
    $execute = $stmt->execute();
    $error = $stmt->errorInfo(); 
    return $execute;

  }
}