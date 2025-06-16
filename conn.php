<?php

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST');
    header("Access-Control-Allow-Headers: X-Requested-With");

   $dsn = 'mysql:host=localhost;dbname=u492790436_Wazfnee';
   $user = 'u492790436_wazfneeDB';
   $pass = 'GUi@Pa7&';
   $options = array(
      PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
   );

   try {
      require "crud.php";
      require "methods.php";
      
      $conn = new PDO($dsn, $user, $pass, $options);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   }
   catch(PDOException $e) {
      echo 'Failed ' . $e->getMessage();
   }