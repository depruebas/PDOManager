<?php

# Sample application to test the class PDOClass2.php
# This example uses MySql database, but other databases how can it be postgresql,
# can be used by changing the connection dsn

# Application usage:
#     php ExampPDO.php
#


define( "EOF", "\n");

include "PDOClass2.php";

# array data access configuration
$config = array(
  //'dsn' => 'mysql:host=localhost;dbname=sakila;charset=utf8',
  'dsn' => 'mysql:host=localhost;dbname=sakila;charset=utf8',
  'username' => '',
  'password' => '',
);

# We get the connection to the bbdd in the variable
PDOClass2::Connection( $config);

$data['query'] = "Select staff_id, first_name, last_name, email, username, password From staff";
$data['params'] = array();

$rows = PDOClass2::ExecuteQuery( $data);

foreach ($rows['data'] as $value)
{
	echo $value['staff_id'] . " - " . $value['first_name'] . " - " . $value['last_name'] . "\n";
}

$data['table'] = "staff";
$data['fields'] = array (
  'first_name' => 'Juan',
  'last_name' => 'Sin Miedo300',
  'address_id' => 1,
  'store_id' => 2,
  'username' => 'juan',
);

# Call to Insert method
$ret = PDOClass2::Insert( $data);

print_r( $ret);

$data['query'] = "Select staff_id, first_name, last_name, email, username, password From staff";
$data['params'] = array();

$rows = PDOClass2::ExecuteQuery( $data);

foreach ($rows['data'] as $value)
{
	echo $value['staff_id'] . " - " . $value['first_name'] . " - " . $value['last_name'] . "\n";
}


PDOClass2::Close();