<?php

error_reporting(0);

function _sleep($file_name=''){
	$file_path = $file_name."_sleep.txt";
	$users = file($file_path);
	$user = $users[0]+1;

	$openFile = fopen($file_path , "w");
	fputs($openFile , $user);
	fclose($openFile);
	sleep($user);
}

function _wake($file_name=''){
	$file_path = $file_name."_sleep.txt";
	$users = file($file_path);
	$user = $users[0]-1;

	$openFile = fopen($file_path , "w");
	fputs($openFile , $user);
	fclose($openFile);
}

	echo 1;
	_sleep('reza1');
	echo 2;
	_wake('reza1');
	echo 3;





 



?>