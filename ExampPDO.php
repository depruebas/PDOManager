<?php

  # Sample application to test the class PDOClass.php
  # This example uses MySql database, but other databases how can it be postgresql, 
  # can be used by changing the connection dsn

  # Application usage:
  #     php ExampPDO.php insert|select|update|delete 

  define( "EOF", "\n");

  include "PDOClass.php";

  # array data access configuration
  $config = array(
    'dsn' => 'mysql:host=localhost;dbname=sakila;charset=utf8',
    'username' => 'devuser',
    'password' => 'mysql',
  );

  # We get the connection to the bbdd in the variable
  $conn = PDOClass::Connection( $config);  

  # Depending on the argument executes one action or another
  switch ( $argv[1]) 
  {
    case 'insert':
      
      # Parameters to call Insert method 
      $data['connection'] = $conn['data'];
      $data['table'] = "staff";
      $data['fields'] = array (
        'first_name' => 'Juan',
        'last_name' => 'Sin Miedo288',
        'address_id' => 1,
        'store_id' => 2,
        'username' => 'juan',
      );
     
      # Call to Insert method
      $ret = PDOClass::Insert( $data);

      print_r( $ret);

      break;
    
    case 'select':
      
      # Parameters to call select method 
      $data['connection'] = $conn['data'];
      $data['query'] = "Select staff_id, first_name, last_name, email, username, password From staff where staff_id = ?";
      $data['params'] = array( '11');

      $rows = PDOClass::ExecuteQuery( $data);

      print_r( $rows);

      $data['connection'] = $conn['data'];
      $data['query'] = "Select staff_id, first_name, last_name, email, username, password From staff";
      $data['params'] = array();

      $rows = PDOClass::ExecuteQuery( $data);$data['query'] = "Select staff_id, first_name, last_name, email, username, password From staff";
      $data['params'] = array();

      print_r( $rows);

      break;

    case 'update':

      # Parameters to call update method 
      $data['connection'] = $conn['data'];
      $data['query'] = "update staff set first_name = ?, email = ? where staff_id = ?";
      $data['params'] = array( 'alex', 'aaaa@airp.com', '7');

      $ret = PDOClass::Execute( $data);

      print_r( $ret);

      break;

    case 'delete':
      
      # Parameters to call delete method 
      $data['connection'] = $conn['data'];
      $data['query'] = "delete From staff where staff_id = ?";
      $data['params'] = array( '10');

      $rows = PDOClass::Execute( $data);

      break;

    default:

      echo EOF;
      echo 'Application usage: ' . EOF;
      echo '    php ExampPDO.php insert|select|update|delete ' . EOF . EOF;
      
      break;
  }

  # Close and set null connection
  $conn = null;