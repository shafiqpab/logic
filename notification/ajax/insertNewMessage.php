<?php

include_once( dirname( __FILE__ ) . '/../class/Database.class.php' );
$pdo = Database::getInstance()->getPdoObject();

$name = $_POST[ 'name' ];
$message = $_POST[ 'message' ];
$insertStatement = $pdo->prepare('insert into message (author, message) values (:author, :message)'); 
$insertStatement->execute( array( 'author'=>$name, 'message'=>$message ) );

?>