 
<?php  
 

//  $array = array('name' => 1,'id' => 2,'url' => 3);
//  $fp = fopen('results.json', 'w');
//  fwrite($fp, json_encode($array, JSON_PRETTY_PRINT));   // here it will print the array pretty
//  fclose($fp);

$file = 'results.json';

$jsonString = file_get_contents($file);
$array = json_decode($jsonString, true);

 $array['name'] = 11111;
 $fp = fopen($file, 'w');
 fwrite($fp, json_encode($array, JSON_PRETTY_PRINT));   // here it will print the array pretty
 fclose($fp);

print_r($array['name']);
?>  
 
