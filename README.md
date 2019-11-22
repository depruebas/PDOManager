# PDOManager
Class and example of data access through PDO in PHP

Connection class to bbdd with PDO drivers.<br>
Driver List: https://www.php.net/manual/es/pdo.drivers.php

The way to use it in a PHP program is to add the PDOClass.php file to the beginning of the file and call the methods directly. As it is a static class it is not necessary to declare the variables a direct call can be made.

Example:

// Array de configuración
```
$config = array(
  'dsn' => 'mysql:host=localhost;dbname=sakila;charset=utf8',<br><br>
  'username' => 'devuser',<br><br>
  'password' => 'mysql',<br><br>
);
```
<br><br>
// To make the connection to the bbdd
```
$connection = PDOClass::Connection ( $config);
```
// Prepare the parameters for a SELECT statement
// We have to send in the connection array the values for connection, 
// query and parameters
```
$data['connection'] = $connection['data'];
$data['query'] = "Select staff_id, first_name, last_name, email, username, password From staff where staff_id = ?";
$data['params'] = array( '11');
```
// Execute the query and return results in rows variable
```
$rows = PDOClass::ExecuteQuery( $data);
```

// Delete connection
```
$connection = null;
```

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
