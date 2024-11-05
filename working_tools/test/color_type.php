<?
$array=array(2,3,4,6,32,33,5,7,1,20,25,26,27,28,29,30,31,34,35,36,37);







$color_type = array(1 => "Solid", 2 => "Stripe [Y/D]", 3 => "Cross Over [Y/D]", 4 => "Check [Y/D]", 5 => "AOP", 6 => "Solid [Y/D]", 7 => "AOP Stripe", 20 => "Florecent", 25 => "Reactive", 26 => "Melange", 27 => "Marl", 28 => "Burn Out", 29 => "Gmts Dyeing", 30 => "Cross Dyeing", 31 => "Over Dyed", 32 => "Space Y/D", 33 => "Faulty Y/D", 34 => "Solid Stripe", 35 => "One Part Dye", 36 => "Space Dyeing", 37 => "Dope Dye", 38 => "INDIGO", 39 => "Neon",40=>"RND Shade",41=>"Tie Dyed",42=>"RFD",43=>"Inject",44=>"Stripe [Y/D Melange]",45=>"AOP [Melange]",46=>"RFD Shade",47=>"Stripe [Y/D AOP]",48=>"Stripe [Y/D Burn-Out AOP]",49=>"AOP on RFD",50=>"Dip Dye",51=>"Solid[Discharge Able Dyeing]",52=>"Discharge Dyeing",53=>"Acid Wash",54=>"AOP [Pigment]",55=>"AOP [Reactive]",56=>"AOP [Discharge]",57=>"AOP [Disperse]",58=>"AOP [Acid Print]",59=>"AOP [Burn Out]",60=>"AOP [Digital Print]",61=>"Siro",62=>"Normal Wash",63=>"Solid [Y/D AOP]",64=>"Double Dyeing");


foreach($array as $id){
	unset($color_type[$id]);	
}

echo "<pre>";
print_r($color_type);


?>