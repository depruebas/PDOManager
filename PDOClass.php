<?php

/*
  Clase de conexión a bbddd con drivers PDO. 
  Lista de drivers PDO https://www.php.net/manual/es/pdo.drivers.php
  
  La forma de utilizarla en un programa PHP es añadir el fichero PDOClass.php al principio del fichero y 
  llamar a los métodos directamente. Como es una clase estatica no es necesario declar las variables se puede hacer una llamada directa.

  Por ejemplo:

    # Array de configuración

    $config = array(
      'dsn' => 'mysql:host=localhost;dbname=sakila;charset=utf8',
      'username' => 'devuser',
      'password' => 'mysql',
    );

    # Realizamos la conexion
    $connection = PDOClass::Connection ( $config);
    

    # Preparamos los parametros para una sentencia SELECT:
    #   conexion, query y parametros 
    $data['connection'] = $connection['data'];
    $data['query'] = "Select staff_id, first_name, last_name, email, username, password From staff where staff_id = ?";
    $data['params'] = array( '11');

    # Ejecutamos la consulta y recojemos los resultados en una variable
    $rows = PDOClass::ExecuteQuery( $data);

    # Eliminamos la conexión
    $connection = null;

    Los resultados se expresan en un array, asi que en $rows tendra la siguiente salidas:

    (
      [success] => 1
      [data] => Array
          (
              [0] => Array
                  (
                      [staff_id] => 11
                      [first_name] => Juan
                      [last_name] => Sin Miedo33
                      [email] => 
                      [username] => juan
                      [password] => 
                  )

          )

      [count] => 1
    )

    Un array con tres posiciones:
      - success:  true o false dependiendo si hay error o no
      - data: un array con el resultado de la consulta
      - count: total de registros obtenido


    Todos los metodos tiene una gestion de errores

    
*/
class PDOClass
{

  # ruta para dejar los logs cuando se generan errores  
  static protected $pathLogs = './';

  function __construct() {}

  # Función para obtener la conexión a la bse de datos
  public static function Connection( $config = array())
  {

    # Si el array de configuración viene vacio devolvemos un error y lo guardamos en el LOG
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
      # Realizamos la conexión a la base de datos con los datos del array
      $conn = new PDO( $config['dsn'], $config['username'], $config['password']);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      # Devolvemos success=true y la conexión
      return ( array( 'success' => true, 'data' => $conn));

    } 
    catch (PDOException $e)
    {
      # SI obtenemos un error lo escribimos en un log y devolvemos success=false y el error
      $_error = print_r( $e->getTrace(), true) . "\n" . $e->getMessage();

      error_log( date("Y-m-d H:i:s") . " - " . $_error . "\n", 3, static::$pathLogs."db_error.log");
      $return = array(
        'success' => false,
        'data' => $_error,
      );
      return ( array( 'success' => false, 'data' => $return));
    }

  }

  # Función para ejecutar consultas (select)
  # Recibe un array de parametros:
  #    $data['connection'] = Conexion;
  #    $data['query'] = Consulta;
  #    $data['params'] = Array de parametros;
  #
  public static function ExecuteQuery( $params = array())
  {

    $db_conn = $params['connection'];

    try
    {
      $stmt = $db_conn->prepare( $params['query']);
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
    unset( $db_conn);

    return ( $return);
  }

  # Función para ejecutar sentencias (insert/update/delete) o cualquiera que no sea una select
  # Recibe un array de parametros
  #   $data['connection'] = Conexion;
  #   $data['query'] = Sentencia a ejecutar;
  #   $data['params'] = Array de parametros;
  #
  public static function Execute( $params = array())
  {

    $db_conn = $params['connection'];

    try
    {
      $stmt = $db_conn->prepare( $params['query']);
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
    unset( $db_conn);

    return ( $return);

  }

  # Funcion para grabar datos (insert) 
  # Recibe un array de parametros
  #     $data['connection'] = $conn['data'];
  #     $data['table'] = "staff";
  #     $data['fields'] = array (
  #           'first_name' => 'Juan',
  #           'last_name' => 'Sin Miedo288',
  #           'address_id' => 1,
  #           'store_id' => 2,
  #           'username' => 'juan',
  #         ); 
  #
  #  En el campo fields hay un array de parametros a insertar
  #
  #      'first_name' => 'Juan',
  #
  #  El valor de la izquierda first_name es un nombre del campo de la base de datos y el valor
  #  de la derecha en el valor de ese campo

  public static function Insert( $params = array())
  {

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


    $db_conn = $params['connection'];

    try
    {

      $sql = "insert into " . $params['table'] . "( {$fields} ) values( ".$fields_values." )";

      $stmt = $db_conn->prepare( $sql);
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
    unset( $db_conn);

    return ( $return );

  }

}