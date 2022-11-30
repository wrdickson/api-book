<?php

namespace wrdickson\apibook;

use \PDO;
use \Exception;

Class Reservations {

public static function check_availability_by_dates( $start, $end ) {
  $response = array();
  $pdo = DataConnector::get_connection();
  //first, get all reservations that conflict with those dates
  $stmt = $pdo->prepare("SELECT * FROM reservations WHERE checkin < :end AND checkout > :start");
  $stmt->bindParam(":start", $start, PDO::PARAM_STR);
  $stmt->bindParam(":end", $end, PDO::PARAM_STR);
  $stmt->execute();
  //second, get all space_id's that are booked for those dates ($rArr)
  $rArr = array();
  while( $obj = $stmt->fetch(PDO::FETCH_OBJ)){
    $tArr = explode(",", $obj->space_code);
    foreach( $tArr as $iterate){
      if(! in_array($iterate, $rArr)) {
        array_push( $rArr, $iterate );
      }
    }
  }
  $response['rArr'] = $rArr;
  //third, get an array of all space_id's
  $allSpaceIds = RootSpaces::get_all_space_ids();
  $response['allspaceids'] = $allSpaceIds;
  //fourth, get only those from all space_ids that are
  //NOT in the array of booked id's
  $availableSpaceIds = array_diff($allSpaceIds, $rArr);

  //  So far, we are failing to catch the situation where
  //  a child item reserved should block the parent
  //  get all root spaces with children
  
  //  iterate through available space_id's 
  //  1. generate children for each one
  //  2. if one of the children is in a reservation space code, remove it 
  foreach( $availableSpaceIds as $index => $spaceId ) {
    //  run the recursive function to get the space's children
    $children = RootSpaces::get_root_space_children($spaceId);
    //  iterate through the children
    foreach($children as $childSpaceId){
      //  compare to the array we made above to include all space codes in res
      if ( in_array( $childSpaceId, $rArr) ){
        //  unset
        unset($availableSpaceIds[$index]);
      }
    }
  }
  $response['availableSpaceIds'] = $availableSpaceIds;
  return $response;
}

public static function check_availability_by_dates_ignore_res( $start, $end, $res_id ) {
  $response = array();
  $pdo = DataConnector::get_connection();
  //first, get all reservations that conflict with those dates
  $stmt = $pdo->prepare("SELECT * FROM reservations WHERE checkin < :end AND checkout > :start AND id != :id");
  $stmt->bindParam(":start", $start, PDO::PARAM_STR);
  $stmt->bindParam(":end", $end, PDO::PARAM_STR);
  $stmt->bindParam(":id", $res_id, PDO::PARAM_INT);
  $stmt->execute();
  //second, get all space_id's that are booked for those dates ($rArr)
  $rArr = array();
  while( $obj = $stmt->fetch(PDO::FETCH_OBJ)){
    $tArr = explode(",", $obj->space_code);
    foreach( $tArr as $iterate){
      if(! in_array($iterate, $rArr)) {
        array_push( $rArr, $iterate );
      }
    }
  }
  $response['rArr'] = $rArr;
  //third, get an array of all space_id's
  $allSpaceIds = RootSpaces::get_all_space_ids();
  $response['allspaceids'] = $allSpaceIds;
  //fourth, get only those from all space_ids that are
  //NOT in the array of booked id's
  $availableSpaceIds = array_diff($allSpaceIds, $rArr);

  //  So far, we are failing to catch the situation where
  //  a child item reserved should block the parent
  //  get all root spaces with children
  
  //  iterate through available space_id's 
  //  1. generate children for each one
  //  2. if one of the children is in a reservation space code, remove it 
  foreach( $availableSpaceIds as $index => $spaceId ) {
    //  run the recursive function to get the space's children
    $children = RootSpaces::get_root_space_children($spaceId);
    //  iterate through the children
    foreach($children as $childSpaceId){
      //  compare to the array we made above to include all space codes in res
      if ( in_array( $childSpaceId, $rArr) ){
        //  unset
        unset($availableSpaceIds[$index]);
      }
    }
  }
  return $availableSpaceIds;
}


public static function check_conflicts( $start, $end, $space_id ) {
    $pdo = DataConnector::get_connection();
    //works, note the comparators are "<" and ">", not "<=" and ">=" because
    //we do allow overlap in sense that one person can checkout on the same
    //day someone checks in
    //  https://stackoverflow.com/questions/325933/determine-whether-two-date-ranges-overlap
    $stmt = $pdo->prepare("SELECT * FROM `reservations` WHERE FIND_IN_SET( :spaceId, space_code ) > 0 AND ( :start < `checkout` AND :end > `checkin` )");
    $stmt->bindParam(":start", $start, PDO::PARAM_STR);
    $stmt->bindParam(":end", $end, PDO::PARAM_STR);
    $stmt->bindParam(":spaceId", $space_id, PDO::PARAM_INT);
    $success = $stmt->execute();
    $pdoError = $pdo->errorInfo();
    $response['success'] = $success;
    $rArr = array();
    // TODO ? handle the case where the space_id doesn't exist
    while( $obj = $stmt->fetch(PDO::FETCH_OBJ)){
        $iArr = array();
        $iArr['id'] = $obj->id;
        $iArr['space_id'] = $obj->space_id;
        array_push($rArr, $iArr);
    };
    $response['hits'] = $rArr;
    //return $rArr;
    if(sizeOf($response['hits']) > 0){
        return false;
    } else {
        return true;
    };
  }

  public static function check_conflicts_ignore_res( $start, $end, $space_id, $res_id ) {
    $pdo = DataConnector::get_connection();
    //works, note the comparators are "<" and ">", not "<=" and ">=" because
    //we do allow overlap in sense that one person can checkout on the same
    //day someone checks in
    //  https://stackoverflow.com/questions/325933/determine-whether-two-date-ranges-overlap
    $stmt = $pdo->prepare("SELECT * FROM `reservations` WHERE FIND_IN_SET( :spaceId, space_code ) > 0 AND ( :start < `checkout` AND :end > `checkin`  ) AND id != :id");
    $stmt->bindParam(":start", $start, PDO::PARAM_STR);
    $stmt->bindParam(":end", $end, PDO::PARAM_STR);
    $stmt->bindParam(":spaceId", $space_id, PDO::PARAM_INT);
    $stmt->bindParam(":id", $res_id, PDO::PARAM_INT);
    $success = $stmt->execute();
    $pdoError = $pdo->errorInfo();
    $response['success'] = $success;
    $rArr = array();
    // TODO ? handle the case where the space_id doesn't exist
    while( $obj = $stmt->fetch(PDO::FETCH_OBJ)){
        $iArr = array();
        $iArr['id'] = $obj->id;
        $iArr['space_id'] = $obj->space_id;
        array_push($rArr, $iArr);
    };
    $response['hits'] = $rArr;
    //return $rArr;
    if(sizeOf($response['hits']) > 0){
        return false;
    } else {
        return true;
    };
  }

  /**
   *  Create Reservation
   */
  public static function create_reservation( $checkin, $checkout, $customer, $spaceId, $people, $beds ){ 
    $response = array();
    //  TODO make damn sure there is not a comflict

    //  generate the space code
    $childrenArr = RootSpaces::get_root_space_children( $spaceId );
    if(count($childrenArr) > 0){
      $spaceCode = $spaceId . ',' . implode(',',$childrenArr);
    } else {
      $spaceCode = $spaceId;
    }
    $response['space_code'] = $spaceCode;
    try {
      //  add to db
      $pdo = DataConnector::get_connection();
      $pdo->beginTransaction();
      $stmt = $pdo->prepare("INSERT INTO reservations (space_code, space_id, checkin, checkout, customer, people, beds, folio, history, status, notes) VALUES (:sc, :si, :ci, :co, :cus, :ppl, :bds, '0', '[]', '0', '[]')");
      $stmt->bindParam(":sc", $spaceCode);
      $stmt->bindParam(":si", $spaceId);
      $stmt->bindParam(":ci", $checkin);
      $stmt->bindParam(":co", $checkout);
      $stmt->bindParam(":cus", $customer);
      $stmt->bindParam(":ppl", $people);
      $stmt->bindParam(":bds", $beds);
      $execute = $stmt->execute();
      $resId = $pdo->lastInsertId();
      $response['execute'] = $execute;
      $response['new_id'] = $resId;

      //  now create the folio
      $folioId = Folios::create_folio( $resId, $customer );

      $pdo->commit();
    } catch ( Exception $e ) {
      $pdo->rollBack();
    }

    $newRes = new Reservation($resId);
    $newRes->folio = $folioId;
    $newRes->update_to_db();
    $finalRes = new Reservation($resId);
    $response['new_res'] = $finalRes->to_array();
    //  return
    return $response;
  }

  public static function get_reservations_date_range( $startDate, $endDate ){
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE checkout >= :start AND checkin <= :end");
    $stmt->bindParam(':start', $startDate);
    $stmt->bindParam(':end', $endDate);
    $stmt->execute();
    $arr= array();
    while($obj = $stmt->fetch(PDO::FETCH_OBJ)){
        $cust = new Customer($obj->customer);
        $iArr = array();
        $iArr['id'] = $obj->id;
        $iArr['space_id'] = $obj->space_id;
        $iArr['space_code'] = $obj->space_code;
        $iArr['checkin'] = $obj->checkin;
        $iArr['checkout'] = $obj->checkout;
        $iArr['customer'] = $obj->customer;
        $iArr['customer_obj'] = $cust->to_array();
        $iArr['people'] = $obj->people;
        $iArr['beds'] = $obj->beds;
        $iArr['folio'] = $obj->folio;
        $iArr['status'] = $obj->status;
        $iArr['history'] = json_decode($obj->history, true);
        $iArr['notes'] = json_decode($obj->notes, true);
        array_push($arr, $iArr);
    };
    return $arr;
  }

}
