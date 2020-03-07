<?php

class PDOClass2
{

  protected static $conn = null;
  protected static $pathLogs = './logs/';

  function __construct() {}

  public static function Connection( $config = array())
  {

    if ( empty( $config))
    {
      error_log( date("Y-m-d H:i:s") . " - Config file empty \n", 3, static::$pathLogs."db_error.log");
      $return = array(
        'success' => false,
        'data' => 'Config file empty',
      );
      return ( array( 'success' => false, 'data' => $return));
    }

    try
    {
      self::$conn = new PDO( $config['dsn'], $config['username'], $config['password']);
      self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      return ( array( 'success' => true, 'data' => self::$conn));

    }
    catch (PDOException $e)
    {
      print_r( $e);
      die;
      $_error = print_r( $e->getTrace(), true) . "\n" . $e->getMessage();

      error_log( date("Y-m-d H:i:s") . " - " . $_error . "\n", 3, static::$pathLogs."db_error.log");
      $return = array(
        'success' => false,
        'data' => $_error,
      );
      return ( array( 'success' => false, 'data' => $return));
    }

  }

  public static function Close()
  {

    self::$conn = null;

  }

  public static function ExecuteQuery( $params = array())
  {

   try
    {
      $stmt = self::$conn->prepare( $params['query']);
      $stmt->execute( $params['params'] );
      $data = $stmt->fetchAll( PDO::FETCH_ASSOC);
      $count = $stmt->rowCount();
      $stmt->closeCursor();

      $return = array( 'success' => true, 'data' => $data, 'count' => $count);
    }
    catch (PDOException $e)
    {
      $_error = print_r( $e->getTrace(), true) . "\n" . $e->getMessage();

      error_log( date("Y-m-d H:i:s") . " - " . $_error . "\n", 3, static::$pathLogs."db_error.log");
      $return = array(
        'success' => false,
        'data' => $_error,
      );
    }

    unset ( $stmt);

    return ( $return);
  }

  public static function Execute( $params = array())
  {

    try
    {
      $stmt = self::$conn->prepare( $params['query']);
      $stmt->execute( $params['params'] );
      $count = $stmt->rowCount();

      $return = array( 'success' => true, 'count' => $count);
    }
    catch (PDOException $e)
    {
      $_error = print_r( $e->getTrace(), true) . "\n" . $e->getMessage();

      error_log( date("Y-m-d H:i:s") . " - " . $_error . "\n", 3, static::$pathLogs."db_error.log");
      $return = array(
        'success' => false,
        'data' => $_error,
      );
    }

    unset ( $stmt);

    return ( $return);

  }

  public static function Insert( $params = array())
  {

    if ( empty( self::$conn))
    {
      $config_db = ConfigClass::get("database.twitter");
      self::Connection( $config_db);
    }

    $data = array();
    $fields = $fields_values = $a_values = "";

    foreach ( $params['fields'] as $key => $value)
    {
      $fields .= $key . ",";
      $fields_values .= " ?,";
      $a_values .= $value . ".:.";
    }

    $fields  = substr( $fields, 0, strlen( $fields) - 1);
    $fields_values  = substr( $fields_values, 0, strlen( $fields_values) - 1);
    $a_values  = substr( $a_values, 0, strlen( $a_values) - 3);


    try
    {

      $sql = "insert into " . $params['table'] . "( {$fields} ) values( ".$fields_values." )";

      $stmt = self::$conn->prepare( $sql);
      $r = $stmt->execute( explode( ".:.", $a_values));
      $count = $stmt->rowCount();

      $return = array( 'success' => true, 'data' => $data, 'count' => $count);

    }
    catch (PDOException $e)
    {

      $_error = print_r( $e->getTrace(), true) . "\n" . $e->getMessage();

      error_log( date("Y-m-d H:i:s") . " - " . $_error . "\n", 3, static::$pathLogs."db_error.log");
      $return = array(
        'success' => false,
        'data' => $_error,
      );

    }

    unset( $stmt);

    return ( $return );

  }




}