<?php
// session_start();
// echo 1;
 


$timeout = 100000000;
//ini_set( "session.gc_maxlifetime", $timeout );
//ini_set( "session.cookie_lifetime", $timeout );

//Start a new session
session_start();

//Set the default session name
$s_name = session_name();

//Check the session exists or not
if(isset( $_COOKIE[ $s_name ] )) {
    //setcookie( $s_name, $_COOKIE[ $s_name ], time() + $timeout, '/' );
    echo "Session is created for $s_name";
} else {
    echo "Session is expired";
}
?>
