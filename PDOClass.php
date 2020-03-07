<?php

/*
**
**
  Connection class to bbdd with PDO drivers.
  Driver List: https://www.php.net/manual/es/pdo.drivers.php

  The way to use it in a PHP program is to add the PDOClass.php file to the beginning of the file and call the methods directly. As it is a static class it is not necessary to declare the variables a direct call can be made.

  Example:

  # Array de configuración
  $config = array(
    'dsn' => 'mysql:host=localhost;dbname=sakila;charset=utf8',
    'username' => 'devuser',
    'password' => 'mysql',
  );

  # To make the connection to the bbdd
  $connection = PDOClass::Connection ( $config);

  # Prepare the parameters for a SELECT statement
  # We have to send in the connection array the values for connection, 
  # query and parameters
  $data['connection'] = $connection['data'];
  $data['query'] = "Select staff_id, first_name, last_name, email, username,  
                      password From staff where staff_id = ?";
  $data['params'] = array( '11');

  # Execute the query and return results in rows variable
  $rows = PDOClass::ExecuteQuery( $data);

  # Delete connection
  $connection = null;

  The results are expressed in an array.
  $rows will have the following outputs:

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

  A array with three positions:
  - success:  true (if OK) or false (if error)
  - data: a array with the query results
  - count: it's the number total of records of the query

**
**   
*/
class PDOClass
{

  # Ruta para dejar los logs cuando se generan errores, por defecto el directorio de trabajo
  static protected $pathLogs = './';

  function __construct() {}

  # Función para obtener la conexión a la bse de datos
  public static function Connection( $config = array())
  {

    # Si el array de configuración viene vacio devolvemos un error y lo guardamos en el LOG
    if ( empty( $config))
    {
      # Si el array de configuracion viene vacio devolvemos un error y lo grabamos en el fichero de errores.
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

      # Devolvemos success=true y la conexión a la base de datos
      return ( array( 'success' => true, 'data' => $conn));

    } 
    catch (PDOException $e)
    {
      # Si obtenemos un error lo escribimos en un log de errores y devolvemos success=false y el error
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
  # 
  #    $data['connection'] = Conexion;
  #    $data['query'] = Consulta;
  #    $data['params'] = Array de parametros;
  #
  public static function ExecuteQuery( $params = array())
  {
    # Recibimos la conexión por parametro y la ponemos en una variable
    $db_conn = $params['connection'];

    try
    {
      # Preparamos la sentencia que queremos ejecutar que se pasa en el array params
      $stmt = $db_conn->prepare( $params['query']);

      # Asignamos los parametros a la sentecia. Si no tiene parametros tendriamos que pasar un array vacio ( array() )
      $stmt->execute( $params['params'] );

      # Ejecutamos la sentencia y devuelve el resultado en la variable $data
      $data = $stmt->fetchAll( PDO::FETCH_ASSOC);

      # Devuelve en la variable $count el numero de filas afectadas
      $count = $stmt->rowCount();

      # Cerramos el cursor
      $stmt->closeCursor();

      # Asignamos a una variable los datos que vamos a devolver a la llamada
      $return = array( 'success' => true, 'data' => $data, 'count' => $count);
    }
    catch (PDOException $e)
    {
      # Si hay error lo guardamos en un fichero y devolvemos success = false y los datos del error
      # 
      $_error = print_r( $e->getTrace(), true) . "\n" . $e->getMessage();

      error_log( date("Y-m-d H:i:s") . " - " . $_error . "\n", 3, static::$pathLogs."db_error.log");
      $return = array(
        'success' => false,
        'data' => $_error,
      );
    }

    # Limpiamos variables utilizadas
    unset ( $stmt);
    unset( $db_conn);

    # Devolvemos el resultado.
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
    # Recibimos la conexión por parametro y la ponemos en una variable
    $db_conn = $params['connection'];

    try
    {
      # Preparamos la sentencia que queremos ejecutar que se pasa en el array params
      $stmt = $db_conn->prepare( $params['query']);

      # Asignamos los parametros a la sentecia. Si no tiene parametros tendriamos que pasar un array vacio ( array() )
      $stmt->execute( $params['params'] );

      # Devuelve en la variable $count el numero de filas afectadas
      $count = $stmt->rowCount();


      # Asignamos a una variable los datos que vamos a devolver a la llamada
      $return = array( 'success' => true, 'count' => $count);
    }
    catch (PDOException $e)
    {
      # Si hay error lo guardamos en un fichero y devolvemos success = false y los datos del error
      # 
      $_error = print_r( $e->getTrace(), true) . "\n" . $e->getMessage();

      error_log( date("Y-m-d H:i:s") . " - " . $_error . "\n", 3, static::$pathLogs."db_error.log");
      $return = array(
        'success' => false,
        'data' => $_error,
      );
    }

    # Limpiamos variables utilizadas
    unset ( $stmt);
    unset( $db_conn);

    # Devolvemos el resultado.
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
    # Inicializamos las variables que vamos a utilizar
    $fields = $fields_values = $a_values = "";

    # Recorremos la variable que tienen los campos de la tabla y asignamos el par field = valor
    foreach ( $params['fields'] as $key => $value) 
    {
      $fields .= $key . ",";
      $fields_values .= " ?,";
      $a_values .= $value . ".:.";
    }

    # Eliminamos los valores finales que no necesitamos en la consulta insert
    $fields  = substr( $fields, 0, strlen( $fields) - 1);
    $fields_values  = substr( $fields_values, 0, strlen( $fields_values) - 1);
    $a_values  = substr( $a_values, 0, strlen( $a_values) - 3);


    # Recibimos la conexión por parametro y la ponemos en una variable
    $db_conn = $params['connection'];

    try
    {
      # Creamos la sentencia Insert con los datos variables de la tabla, campos y valores
      $sql = "insert into " . $params['table'] . "( {$fields} ) values( ".$fields_values." )";

      # Preparamos la consulta que ejecutaremos
      $stmt = $db_conn->prepare( $sql);

      # Ejecutamos la consulta con los valores en un array
      $r = $stmt->execute( explode( ".:.", $a_values));


      $id = $db_conn->lastInsertID();

      # Guardamos los valores de las filas afectadas
      $count = $stmt->rowCount();

      # Creamos la variable que vamos a devolver.
      $return = array( 'success' => true, 'count' => $count, 'id' => $id);

    } 
    catch (PDOException $e)
    {

      # Si hay error lo guardamos en un fichero y devolvemos success = false y los datos del error
      #
      $_error = print_r( $e->getTrace(), true) . "\n" . $e->getMessage();

      error_log( date("Y-m-d H:i:s") . " - " . $_error . "\n", 3, static::$pathLogs."db_error.log");
      $return = array(
        'success' => false,
        'data' => $_error,
      );

    }

    # Limpiamos variables utilizadas
    unset( $stmt);
    unset( $db_conn);

    # Devolvemos el resultado.
    return ( $return );

  }

}