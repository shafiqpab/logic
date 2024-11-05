<?php
function filter_string($inputString){
	return preg_replace('/[^A-Za-z0-9\-@#_= ]/', '', $inputString);
}


$inputString = "Hello, -#@World! Hello, <World!> TIGER ��� S EYE 17-1038 TCX=";
echo filter_string($inputString);
 
 


 

?>