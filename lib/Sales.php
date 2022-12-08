<?php 

namespace wrdickson\apibook;
use \PDO;

Class Sale {
  private $id;
  private $sale_datetime;
  private $sale_type;
  private $sale_description;
  private $net;
  private $tax;
  private $sold_by;
  private $folio;

  public static function get_sales_by_folio( $folio_id ){

  }

  public static function post_sale( $sale_type, $sale_description, $net, $tax, $sold_by, $folio ){
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("INSERT INTO sales ( sale_datetime, sale_type, sale_description, net, tax, sold_by, folio ) VALUES (  NOW(), :st, :sq, :sd, :nt, :tx, :sb, :fo)");
    $stmt->bindParam(':st', $sale_type, PDO::PARAM_INT);
    $stmt->bindParam(':sd', $sale_description, PDO::PARAM_STR);
    $stmt->bindParam(':nt', $net, PDO::PARAM_STR);
    $stmt->bindParam(':tx', $tax, PDO::PARAM_STR);
    $stmt->bindParam(':sb', $sold_by, PDO::PARAM_INT);
    $stmt->bindParam(':fo', $folio, PDO::PARAM_INT);
    $success = $stmt->execute();
    return $success;
  }

}