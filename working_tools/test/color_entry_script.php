<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');
$con=connect();

$color_id_library = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0","id","color_name");

$insert_color_arr= array('ALASKA BLUE','PESTO','P.NECTER','BLANC','CANAL BLUE','P.PINK','NEUTRAL','BLACK','BROWN','BRIGHT WHITE','WHITE','AVG','CLNV','DZBL','ERED','MRNB','ORRD','WHIT','NAVY','BLUE','NETURAL','DDBH','GREY','SILVER MELL','CPGY','HUDG','RYTR','BLACK OUT','WHITE MELL','STONE+BLUE+WHITE+CHARCOAL','MUSTARD','TEAL','KHAKI','LIME','CHARCOAL','ORANGE+TEAL','GREY MELL','PLUM','GREEN','COBALT','BLCK','ECRU','AVG(LIGHT)','BKHT','BZOR','CBSG','DNDL','SFBH','WNBL','SMOT','TLBL','MULTI','NAVY+STONE','DEEP','CBYW','GREY MARL','BURGUNDY','BLACK GERMANY FLAG','WHITE PORTUGAL FOOTBALL','RED','LTGY','PKCR','HBHB','LT HEATHER GREY+PEARL PINK+WHITE','BLRF','DTWB','LIGHT','WHIT+CLNV','WHITE+CLNV','BLACK+WHITE','B.PINK+WHITE','E.BLUE+WHITE','LT.HEATHER GREY+WHITE','FRHR','ECRU MELLANGE','PINK','G.MARL','HZBL','VYBL','WAOR','HGRY','SFBH/PGHG','FUSIA RED','LIGHT PINK','BACHELOR BUTTON','PURPLE ROSE','SNOW WHITE','AQUA','BRICH','NUDE','ANTIQUE WHITE','SYRINGA','SMASHING PUNK','HEAVY BLUE + GREEN','HEAVY BLUE','BLUE NIGHT','ORE','INK','AMBER','OFF WHITE','ANTERCITE','HEAVY BLUE+DUFFEL','WHITE+BLACK','GREY LIGHT MELEE','AVG(DEEP)','KIT','AVG DEEP','DK BLUE','OFF WHITE+NAVY','NEW IVORY','FIREY RED','BLUE DARK','GREEN MIDDLE','GREEN OLIVE LT','H.BLUE+N.IVORY','GREEN OCEAN','ANTRA MELEE','FUCHSIA','SKY BLUE','TAUPE','GREEN LIGHT','DARK COLOR','LIGHT COLOR','HIGH RISE','PRISTINE','CLAY','GREEN OLIVE','GREY MIDDLE','MINT LIGHT','SAND','BERING SEA','BROWNISH PINK','FRENCH BLUE+WHITE','HIGH RISE+WHITE','OATMEAL','BLACK BEAUTY','BLACK UNI','PINK BERRY','BLUE NIGHITS','ORANGE FLAMINGO','CORAL ROSE','ALASKAN BLUE+WHITE','PINK+OFF-WHITE','GREY ICE','CRUDO','AZUL','AMR.LLO','ROSA','V.OSCURE','NARANJA','NEGRO');

foreach($insert_color_arr as $color_text)
{
	$color_text = str_replace("(","[",$color_text);
	$color_text = str_replace(")","]",$color_text);

	if (!in_array(str_replace("'","",$color_text),$new_array_color))
	{
		$color_id = return_id( $color_text, $color_id_library , "lib_color", "id,color_name","2");
		$new_array_color[$color_id]=str_replace("'","",$color_text);
	}
	else 
	{
		$color_id =  array_search(str_replace("'","",$color_text), $new_array_color);
	}

	echo $color_text.'='.$color_id."<br>";
}


echo "Success";
oci_commit($con);  
disconnect($con); 

die;



?>