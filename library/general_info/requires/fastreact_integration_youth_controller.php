<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
echo "0**";
 
$fr_product_type=array(1=>"Basic T Shirt",2=>"Basic Polo",3=>"Basic Tank Top",4=>"Sweat Shirt",5=>"Hooded Jacket",6=>"Nightwear",7=>"Cap",8=>"Basic Trouser",9=>"Basic Shorts",10=>"Boys Singlet",11=>"Boys Boxer",12=>"Underwear",13=>"Girls Bra",14=>"Panty",15=>"Leggings",16=>"NW Bottom",17=>"NW Top Basic",18=>"Hooded Jacket",19=>"Basic T Shirt",20=>"Romper",21=>"Girls Brief",22=>"Mens Minislip",23=>"Girls Hipster",24=>"NW Big Shirt",25=>"Basic Shorts",26=>"Serafino",27=>"Girls Brief",28=>"Basic Trouser",29=>"Jumper",30=>"Others Dress [TOP]",31=>"Basic T Shirt",32=>"JUMPSUIT",33=>"",34=>"Sweat Shirt",35=>"Bottom",36=>"Hair Band",37=>"Shopping Bag",38=>"Basic T Shirt",39=>"Basic T Shirt",40=>"Basic T Shirt",41=>"Basic T Shirt",42=>"Basic Tank Top",43=>"Basic Tank Top",44=>"Basic Trouser",45=>"Baby Suits",46=>"Basic Tank Top",47=>"Basic Tank Top",48=>"Basic T Shirt",49=>"Basic T Shirt",50=>"Basic T Shirt",51=>"Basic T Shirt",52=>"Basic Trouser",53=>"Pet Outfit",54=>"Mens Trunk",55=>"Boys Singlet",56=>"Girls Boxer",57=>"Mens Minislip",58=>"Mens Boxer",59=>"Boys Boxer",60=>"Boys Brief",61=>"Basic T Shirt",62=>"Basic T Shirt",63=>"Basic T Shirt",64=>"Basic T Shirt",65=>"Basic T Shirt",66=>"Basic T Shirt",67=>"Basic T Shirt",68=>"Basic T Shirt",69=>"Basic T Shirt",70=>"Basic T Shirt",71=>"Basic T Shirt",72=>"Basic T Shirt",73=>"Basic T Shirt",74=>"Basic T Shirt",75=>"Basic T Shirt",76=>"Basic T Shirt",77=>"Romper",78=>"Romper",79=>"Basic Polo",80=>"Basic Polo",81=>"Hooded Jacket",82=>"Basic Trouser",83=>"Bibs",84=>"Basic T Shirt",85=>"Basic T Shirt",86=>"Basic T Shirt",87=>"Basic Tank Top",88=>"Basic T Shirt",89=>"Basic T Shirt",90=>"Basic T Shirt",91=>"Basic Trouser",92=>"Basic T Shirt",93=>"Romper",94=>"Romper",95=>"Basic T Shirt",96=>"Basic T Shirt",97=>"Basic T Shirt",98=>"Basic T Shirt",99=>"Basic T Shirt",100=>"Basic T Shirt",101=>"Basic T Shirt",102=>"",103=>"Basic T Shirt",104=>"Basic T Shirt",105=>"Basic T Shirt",106=>"Basic Tank Top",107=>"Basic T Shirt",108=>"Basic Shorts",109=>"Basic T Shirt",110=>"NW Bottom",111=>"Basic Tank Top",112=>"Basic Trouser",113=>"Basic T Shirt",114=>"Basic T Shirt",115=>"Basic T Shirt",116=>"Basic T Shirt",117=>"Basic Trouser",118=>"Basic Trouser",119=>"Basic T Shirt",120=>"Basic T Shirt",121=>"Basic T Shirt",122=>"Basic T Shirt",123=>"Basic Trouser",124=>"Basic T Shirt",125=>"Basic T Shirt",126=>"Basic Polo",127=>"Basic T Shirt",128=>"Basic Trouser",129=>"Girls Slip",130=>"Girls Slip",131=>"Girls Slip",132=>"Basic T Shirt",133=>"Joggers Pant",134=>"Basic T Shirt",135=>"Basic Trouser",136=>"Basic T Shirt",137=>"Basic T Shirt",138=>"Basic T Shirt",139=>"Critical Polo",140=>"Girls Singlet",141=>"Basic Polo",142=>"Sweat Shirt"); // 33 => deleted in table

$production_process_freact=array(70=>"Cutting",80=>"Print Send",90=>"Print Receive",100=>"Emb Send",110=>"Emb Receive",120=>"Sewing Input",230=>"Sewing Output",240=>"Wash Send",250=>"Wash Receive",260=>"QC Pass",270=>"Finishing",280=>"Packing",290=>"Shipped");

//$fr_product_type=$garments_item;
$group_unit_array=array( 10 => 10,11 => 10,12 => 10,13 => 10,14 => 80,15 => 80,16 => 80,17 => 80,18 => 10,19 => 10,20 => 10,21 => 10,39 => 30,40 =>30,42 => 20,43 =>20);

$groupNameArr=array(10 => "Unit 1",20 => "Unit 2",30 => "Unit 3",40 => "Unit 4",50 => "Subcon",80 => "Lingerie");

$line_array=array(1=>"10",2=>"15",3=>"20",4=>"25",5=>"30",6=>"35",7=>"40",8=>"45",9=>"50",10=>"55",11=>"60",12=>"65",13=>"70",14=>"75",15=>"80",16=>"85",17=>"90",18=>"95",19=>"100",20=>"105",21=>"110",22=>"115",23=>"120",24=>"125",25=>"130",26=>"135",27=>"140",28=>"145",29=>"150",30=>"155",31=>"160",32=>"165",34=>"855",35=>"860",36=>"865",37=>"870",38=>"875",39=>"880",40=>"890",41=>"850",42=>"810",43=>"815",44=>"820",45=>"825",46=>"830",47=>"835",48=>"840",49=>"845",50=>"905",51=>"910",52=>"915",53=>"920",54=>"925",55=>"930",56=>"935",57=>"940",58=>"945",59=>"950",60=>"955",61=>"960",62=>"965",63=>"970",64=>"975",65=>"980",66=>"255",67=>"260",68=>"265",69=>"270",70=>"275",71=>"280",72=>"285",73=>"290",74=>"295",75=>"300",76=>"305",77=>"310",119=>"315",78=>"320",79=>"325",80=>"330",81=>"335",82=>"340",83=>"345",84=>"350",85=>"355",86=>"360",87=>"365",88=>"370",89=>"375",90=>"380",91=>"385",92=>"390",93=>"395",94=>"400",95=>"405",96=>"410",97=>"415",98=>"420",99=>"425",100=>"430",101=>"435",102=>"440",103=>"710",104=>"720",105=>"730",106=>"740",107=>"750",108=>"760",109=>"770",110=>"780",111=>"610",112=>"620",113=>"630",114=>"640",115=>"650",116=>"660",117=>"670",118=>"680"); //34=>"810",35=>"815",36=>"820",37=>"825",38=>"830",39=>"835",40=>"840",41=>"845",42=>"850",43=>"855",44=>"860",45=>"865",46=>"870",47=>"875",48=>"880",49=>"890", 

$lineNameArr=array("10"=>"Line 3 - 1","15"=>"Line 3 - 2","20"=>"Line 3 - 3","25"=>"Line 3 - 4","30"=>"Line 3 - 5","35"=>"Line 3 - 6","40"=>"Line 3 - 7","45"=>"Line 3 - 8","50"=>"Line 4 - 1","55"=>"Line 4 - 2","60"=>"Line 4 - 3","65"=>"Line 4 - 4","70"=>"Line 4 - 5","75"=>"Line 4 - 6","80"=>"Line 4 - 7","85"=>"Line 4 - 8","90"=>"Line 5 - 1","95"=>"Line 5 - 2","100"=>"Line 5 - 3","105"=>"Line 5 - 4","110"=>"Line 5 - 5","115"=>"Line 5 - 6","120"=>"Line 5 - 7","125"=>"Line 5 - 8","130"=>"Line 6 - 1","135"=>"Line 6 - 2","140"=>"Line 6 - 3","145"=>"Line 6 - 4","150"=>"Line 6 - 5","155"=>"Line 6 - 6","160"=>"Line 6 - 7","165"=>"Line 6 - 8","810"=>"Green L 3 - 1","815"=>"Green L 3 - 2","820"=>"Green L 3 - 3","825"=>"Green L 3 - 4","830"=>"Green L 3 - 5","835"=>"Green L 3 - 6","840"=>"Green L 3 - 7","845"=>"Green L 3 - 8","850"=>"Green L 4 - 1","855"=>"Green L 4 - 2","860"=>"Green L 4 - 3","865"=>"Green L 4 - 4","870"=>"Green L 4 - 5","875"=>"Green L 4 - 6","880"=>"Green L 4 - 7","890"=>"Green L 4 - 8","905"=>"Green L 5 - 1","910"=>"Green L 5 - 2","915"=>"Green L 5 - 3","920"=>"Green L 5 - 4","925"=>"Green L 5 - 5","930"=>"Green L 5 - 6","935"=>"Green L 5 - 7","940"=>"Green L 5 - 8","945"=>"Green L 6 - 1","950"=>"Green L 6 - 2","955"=>"Green L 6 - 3","960"=>"Green L 6 - 4","965"=>"Green L 6 - 5","970"=>"Green L 6 - 6","975"=>"Green L 6 - 7","980"=>"Green L 6 - 8","255"=>"Maple Leaf L 4 - 1","260"=>"Maple Leaf L 4 - 2","265"=>"Maple Leaf L 4 - 3","270"=>"Maple Leaf L 4 - 4","275"=>"Maple Leaf L 4 - 5","280"=>"Maple Leaf L 4 - 6","285"=>"Maple Leaf L 4 - 7","290"=>"Maple Leaf L 4 - 8","295"=>"Maple Leaf L 5 - 1","300"=>"Maple Leaf L 5 - 2","305"=>"Maple Leaf L 5 - 3","310"=>"Maple Leaf L 5 - 4","315"=>"Maple Leaf L 5 - 5","320"=>"Maple Leaf L 5 - 6","325"=>"Maple Leaf L 5 - 7","330"=>"Maple Leaf L 5 - 8","335"=>"Maple Leaf L 6 - 1","340"=>"Maple Leaf L 6 - 2","345"=>"Maple Leaf L 6 - 3","350"=>"Maple Leaf L 6 - 4","355"=>"Maple Leaf L 6 - 5","360"=>"Maple Leaf L 6 - 6","365"=>"Maple Leaf L 6 - 7","370"=>"Maple Leaf L 6 - 8","375"=>"Maple Leaf L 6 - 9","380"=>"Maple Leaf L 6 - 10","385"=>"Maple Leaf L 6 - 11","390"=>"Maple Leaf L 7 - 1","395"=>"Maple Leaf L 7 - 2","400"=>"Maple Leaf L 7 - 3","405"=>"Maple Leaf L 7 - 4","410"=>"Maple Leaf L 7 - 5","415"=>"Maple Leaf L 7 - 6","420"=>"Maple Leaf L 7 - 7","425"=>"Maple Leaf L 7 - 8","430"=>"Maple Leaf L 7 - 9","435"=>"Maple Leaf L 7 - 10","440"=>"Maple Leaf L 7 - 11","710"=>"Line 1","720"=>"Line 2","730"=>"Line 3","740"=>"Line 4","750"=>"Line 5","760"=>"Line 6","770"=>"Line 7","780"=>"Line 8","610"=>"Line 1","620"=>"Line 2","630"=>"Line 3","640"=>"Line 4","650"=>"Line 5","660"=>"Line 6","670"=>"Line 7","680"=>"Line 8");

$buyer_name_array=array(1=>"H n M",2=>"Primark",3=>"Engelbert Strauss",4=>"Auchan",5=>"KIK",6=>"C n A",7=>"Sportmaster",8=>"SRG",9=>"Next",10=>"Wood Bridge",11=>"DKC",12=>"Interexpo",13=>"Indesore",15=>"Zara",16=>"Fashion UK",34=>"TCHIBO",40=>"The North Face",48=>"Jako",52=>"GBG",71=>"Petromax",93=>"Schijvens",97=>"VDE",106=>"United Legwear [HURLEY/KOHLS]",109=>"Metro",112=>"S. Oliver",117=>"ACCOLADE",138=>"TRICORP",143=>"Saltedbasics");
 
//in (1,2,3,4,5,6,7,8,9,10,11,12,13,15,34,40,48,52,71,93,97,106,109,112)

if ( $action=="save_update_delete" )
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	foreach (glob("frfiles/"."*.*") as $filename)
	{			
		@unlink($filename);
	}
	//echo $cbo_fr_integrtion=str_replace("'",$cbo_fr_integrtion); //die;
	header('Content-Type: text/csv; charset=utf-8');
	if ( $cbo_fr_integrtion==0 )
	{
		$color_lib=return_library_array("select id,color_name from lib_color","id","color_name");
		$countryArr=return_library_array("select id,short_name from lib_country","id","short_name");
		$floor_name=return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
		$line_name=return_library_array("select id,line_name from lib_sewing_line","id","line_name");
		$line_name_res=return_library_array("select id,line_number from prod_resource_mst","id","line_number");
		$floor_name_res=return_library_array("select id,floor_id from prod_resource_mst","id","floor_id");
		$yarnCountArr=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
		 
		// Customer File
		$sql=sql_select ( "select id, buyer_name, short_name from lib_buyer where id in (1,2,3,4,5,6,7,8,9,10,11,12,13,15,16,34,40,48,52,71,93,97,106,109,112,117,138,143)" );
		$file_name="frfiles/CUSTOMER.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
		$txt .=str_pad("C.CODE",0," ")."\t".str_pad("C.DESCRIP",0," ")."\r\n";
		foreach($sql as $name)
		{
			//if(strlen($name)>6) $txt .=$name."\tEND\r\n"; else 
			//$buyer_name_array[$name[csf("id")]]=$name[csf("short_name")];
			$txt .=str_pad($buyer_name_array[$name[csf("id")]],0," ")."\t".str_pad($name[csf("buyer_name")],0," ")."\r\n";
		}
		fwrite($myfile, $txt);
		fclose($myfile); 
		
		$txt="";
		if(trim( $received_date)=="") $received_date="01-Sep-2021"; else $received_date=change_date_format($received_date, "yyyy-mm-dd", "-",1);
		// Products file Data
		//echo $received_date; die;
		if( $db_type==0 )
		{
			//$shipment_date="and c.country_ship_date >= '2014-11-01'";
			$shipment_date="and c.country_ship_date >= '$received_date'";
		}
		if($db_type==2)
		{
			$shipment_date="and c.country_ship_date >= '$received_date'";
		}
		$shiping_status="and c.shiping_status !=3";
			
		//letsgrowourfood.com
		
		$jobNo="and a.job_no='CCKL-23-01043'"; 
			
		$ft_data_arr=array(); $jobCostingper=array(); $po_arr=array(); $orders_arr=array();
		
		$sql_po='select a.id as "id", a.job_no as "job_no", a.style_ref_no as "style_ref_no", a.style_ref_no_prev as "style_ref_no_prev", a.style_description as "style_description", a.gmts_item_id as "gmts_item_id", a.gmts_item_id_prev as "gmts_item_id_prev", a.buyer_name as "buyer_name", a.order_uom as "order_uom", b.id as "bid", b.po_number as "po_number", b.is_confirmed as "is_confirmed", b.po_quantity as "po_quantity", b.grouping as "internal_ref", b.pub_shipment_date as "pub_shipment_date", b.status_active as "status_active", b.po_number_prev as "po_number_prev", b.pub_shipment_date_prev as "pub_shipment_date_prev", c.id as "color_size_break_id", c.item_number_id as "item_number_id", c.country_ship_date as "country_ship_date", c.country_id as "country_id", c.cutup as "cutup", c.color_mst_id as "color_mst_id", c.item_mst_id as "item_mst_id", c.color_number_id as "color_number_id", c.plan_cut_qnty as "plan_cut_qnty", c.order_quantity as "order_quantity", c.order_total as "order_total", c.shiping_status as "shiping_status", c.country_ship_date_prev as "country_ship_date_prev", c.color_number_id_prev as "color_number_id_prev", c.cutup_date as "cutup_date", c.is_deleted as "is_deleted"
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id '. $shipment_date .' and a.is_deleted=0 and a.status_active=1 and b.status_active in (0,1,3) and c.plan_cut_qnty!=0 and a.buyer_name in (1,2,3,4,5,6,7,8,9,10,11,12,13,15,16,34,40,48,52,71,93,97,106,109,112,117,138,143) order by a.id, c.country_ship_date asc';//'.$jobNo.''.$jobNo.'
		//echo $sql_po; die;
		$sql_po_data=sql_select($sql_po);  //and b.is_confirmed=2 
		foreach($sql_po_data as $name)
		{
			//Product File
			$ft_data_arr[$name['job_no']]['style_ref_no']=$name['style_ref_no'];//P.DESCRIP
			$ft_data_arr[$name['job_no']]['gmts_item_id']=$name['gmts_item_id'];//P.TYPE
			$ft_data_arr[$name['job_no']]['buyer_name']=$name['buyer_name'];
			//$ft_data_arr[$name['job_no']]['po_quantity']=$name['po_quantity'];
			
			$po_arr[po_id][$name['bid']]=$name['bid'];
			$po_arr[job_id][$name['id']]=$name['id'];
			
			// Orders file
			$old_str_po=''; $old_item_code=''; $changed=0; $old_po=""; $job=$cdate="";
			$poId=0; $item_id=0; $color_id=0; $cut_up=0;
			
			$job=$name['job_no'];
			$cdate=$name['country_ship_date'];
			$poId=$name['bid']; 
			$item_id=$name['item_number_id']; 
			$color_id=$name['color_number_id']; 
			$cut_up=$name['cutup'];
			
			$buyer_name=$buyer_name_array[$name['buyer_name']];
			
			if( $name['shiping_status']==3) $ssts="1"; else $ssts=""; //Sewing Out
			
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id]['cid'].=$name['color_size_break_id'].',';
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id]['countryid'].=$name['country_id'].',';
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id]['buyer']=$buyer_name;
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id]['style_ref']=$name['style_ref_no'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id]['uom']=$name['order_uom'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id]['gmts_item_id']=$name['gmts_item_id'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id]['po_number']=$name['po_number'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id]['internal_ref']=$name['internal_ref'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id]['po_received_date']=$name['po_received_date'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id]['is_confirmed']=$name['is_confirmed'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id]['shiping_status']=$ssts;
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id]['icmstid']=$name['item_mst_id'].'_'.$name['color_mst_id'];
			
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id]['is_deleted'].="'".$name['is_deleted']."',";
			if($name['is_deleted']==0)
			{
				$orders_arr[$job][$poId][$item_id][$cdate][$color_id]['plan_cut_new']+=$name['plan_cut_qnty'];
				$orders_arr[$job][$poId][$item_id][$cdate][$color_id]['order_quantity']+=$name['order_quantity'];
				$orders_arr[$job][$poId][$item_id][$cdate][$color_id]['order_total']+=$name['order_total'];
			}
			else
			{
				$orders_arr[$job][$poId][$item_id][$cdate][$color_id]['plan_cut_del']+=$name['plan_cut_qnty'];
				$orders_arr[$job][$poId][$item_id][$cdate][$color_id]['order_qty_del']+=$name['order_quantity'];
				$orders_arr[$job][$poId][$item_id][$cdate][$color_id]['order_total_del']+=$name['order_total'];
			}
			
			if( trim($name['style_ref_no_prev'])!='' && trim($name['style_ref_no_prev'])!=$name['style_ref_no'])
			{
				$ft_data_arr[$name['job_no']]['style_ref_no_prev']=$name['style_ref_no_prev'];
			}
			if( trim($name['gmts_item_id_prev'])!='' && trim($name['gmts_item_id_prev'])!=$name['gmts_item_id'])
			{
				$ft_data_arr[$name['job_no']]['gmts_item_id_prev']=$name['gmts_item_id_prev'];
			}
			
			if( trim($name['po_number_prev'])!='' && trim($name['po_number_prev'])!=$name['po_number'])
			{
				$orders_arr[$job][$poId][$item_id][$cdate][$color_id]['po_number_prev']=$name['po_number_prev'];
			}
			if( trim($name['gmts_item_id_prev'])!='' && trim($name['gmts_item_id_prev'])!=$name['gmts_item_id'])
			{
				$orders_arr[$job][$poId][$item_id][$cdate][$color_id]['gmts_item_id_prev']=$name['gmts_item_id_prev'];
			}
			
			if( trim($name['color_number_id_prev'])!='' && trim($name['color_number_id_prev'])!=$name['color_number_id'])
			{
				$orders_arr[$job][$poId][$item_id][$cdate][$color_id]['color_number_id_prev']=$name['color_number_id_prev'];
			}
			
			//$new_color_size[$job][$poId][$item_id][$cdate][$color_id][$name['color_size_break_id']]=$name['color_size_break_id'];
			$prod_up_arr_powise[$name['color_size_break_id']]=$poId;
		}
		/*echo "<pre>";
		print_r($orders_arr); die;*/
		unset($sql_po_data);
		
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2) and ENTRY_FORM=1");
		oci_commit($con);
		//echo "11";
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 1, 1, $po_arr[po_id], $empty_arr);//PO ID
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 1, 2, $po_arr[job_id], $empty_arr);//Job ID table name, entry form, id type, data array,
			
		//print_r($job_cond_in); die;
		$cmArr=return_library_array("select a.job_no, a.cm_cost from wo_pre_cost_dtls a, gbl_temp_engine b where a.job_id=b.ref_val and b.user_id = ".$user_id." and b.entry_form=1 and b.ref_from=2 and a.is_deleted=0 and a.status_active=1","job_no","cm_cost");
		
		$sql_fabric_cons_item=sql_select("select A.ID, A.JOB_NO, A.ITEM_NUMBER_ID, A.COLOR_TYPE_ID, A.FABRIC_SOURCE, A.AVG_FINISH_CONS, A.AVG_CONS, A.CONSTRUCTION, A.COMPOSITION, A.GSM_WEIGHT from wo_pre_cost_fabric_cost_dtls a, gbl_temp_engine b where a.status_active=1 and a.is_deleted=0 and a.job_id=b.ref_val and b.user_id = ".$user_id." and b.entry_form=1 and b.ref_from=2 ");
		//echo "select a.job_no, a.item_number_id, a.color_type_id, a.fabric_source, a.avg_finish_cons, a.avg_cons, a.construction, a.composition, a.gsm_weight from wo_pre_cost_fabric_cost_dtls a, gbl_temp_engine b where a.status_active=1 and a.is_deleted=0 and a.job_id=b.ref_val and b.user_id = ".$user_id." and b.entry_form=1 and b.ref_from=2 " ; die;
		$fabricGConsArr=array(); $fabricConvConsArr=array(); $fabfinishconsarr=array();
		foreach($sql_fabric_cons_item as $row_fabric_prod)
		{
			if($row_fabric_prod['FABRIC_SOURCE']==1)
			{
				$fabricGConsArr[$row_fabric_prod['JOB_NO']][$row_fabric_prod['ITEM_NUMBER_ID']]['cons_costingper']+=$row_fabric_prod['AVG_CONS'];//P^WC:5   
			}
			if($row_fabric_prod['COLOR_TYPE_ID']==5 || $row_fabric_prod['COLOR_TYPE_ID']==7 ||$row_fabric_prod['COLOR_TYPE_ID']==67 ||$row_fabric_prod['COLOR_TYPE_ID']==69 || $row_fabric_prod['COLOR_TYPE_ID']==56 ||$row_fabric_prod['COLOR_TYPE_ID']==57 ||$row_fabric_prod['COLOR_TYPE_ID']==58 || $row_fabric_prod['COLOR_TYPE_ID']==59 ||$row_fabric_prod['COLOR_TYPE_ID']==60 ||$row_fabric_prod['COLOR_TYPE_ID']==54 || $row_fabric_prod['COLOR_TYPE_ID']==55 ||$row_fabric_prod['COLOR_TYPE_ID']==49 ||$row_fabric_prod['COLOR_TYPE_ID']==45)
			{
				//$fabricGConsArr[$row_fabric_prod['JOB_NO']][$row_fabric_prod['ITEM_NUMBER_ID']]['aop_costingper']+=$row_fabric_prod['AVG_CONS'];//P^WC:5 
				//$fabricConvConsArr[$row_fabric_prod['JOB_NO']]['isaop']=1;  
			}
			
			if($row_fabric_prod['COLOR_TYPE_ID']==2 || $row_fabric_prod['COLOR_TYPE_ID']==3 || $row_fabric_prod['COLOR_TYPE_ID']==4 || $row_fabric_prod['COLOR_TYPE_ID']==6 || $row_fabric_prod['COLOR_TYPE_ID']==31 || $row_fabric_prod['COLOR_TYPE_ID']==32 || $row_fabric_prod['COLOR_TYPE_ID']==33 || $row_fabric_prod['COLOR_TYPE_ID']==34 || $row_fabric_prod['COLOR_TYPE_ID']==47 || $row_fabric_prod['COLOR_TYPE_ID']==63 || $row_fabric_prod['COLOR_TYPE_ID']==71)
			{
				//$fabricConvConsArr[$row_fabric_prod['JOB_NO']]['isyd']='Yes';  
			}
			
			$fabricGConsArr[$row_fabric_prod['JOB_NO']][$row_fabric_prod['ITEM_NUMBER_ID']]['compo'].=$row_fabric_prod['COMPOSITION'].'___';
			$fabricGConsArr[$row_fabric_prod['JOB_NO']][$row_fabric_prod['ITEM_NUMBER_ID']]['const'].=$row_fabric_prod['CONSTRUCTION'].'___';
			$fabricGConsArr[$row_fabric_prod['JOB_NO']][$row_fabric_prod['ITEM_NUMBER_ID']]['gsm'].=$row_fabric_prod['GSM_WEIGHT'].'___';
			$fabfinishconsarr[$row_fabric_prod['ID']]['fincons']+=$row_fabric_prod['AVG_FINISH_CONS'];
			$fabfinishconsarr[$row_fabric_prod['ID']]['greycons']+=$row_fabric_prod['AVG_CONS'];
			$fabfinishconsarr[$row_fabric_prod['ID']]['gmtitem']=$row_fabric_prod['ITEM_NUMBER_ID'];
			
			$fabricConvConsArr[$row_fabric_prod['JOB_NO']][$row_fabric_prod['ITEM_NUMBER_ID']]['fabfinish']+=$row_fabric_prod['AVG_FINISH_CONS'];
		}
		unset($sql_fabric_cons_item);
		//print_r($fabricGConsArr['CCKL-22-00554']); die;
		
		$sqlDia="Select A.JOB_NO, A.DIA_WIDTH from wo_pre_cos_fab_co_avg_con_dtls a, gbl_temp_engine b where a.is_deleted=0 and a.status_active=1 and a.job_id=b.ref_val and b.user_id = ".$user_id." and b.entry_form=1 and b.ref_from=2 ";
		$sqlDiaSql=sql_select($sqlDia); $job_diaarr=array();
		
		foreach($sqlDiaSql as $brow)
		{
			$job_diaarr[$brow['JOB_NO']].=$brow['DIA_WIDTH'].',';
		}
		unset($sqlDiaSql);
		
		$bomMst="select a.job_no, a.costing_per, a.sew_smv from wo_pre_cost_mst a, gbl_temp_engine b where a.status_active=1 and a.is_deleted=0 and a.job_id=b.ref_val and b.user_id = ".$user_id." and b.entry_form=1 and b.ref_from=2 ";
		$bomMstSql=sql_select($bomMst); $cmCostArr=array();
		
		foreach($bomMstSql as $brow)
		{
			$costingper=$brow[csf('costing_per')];
			
			if($costingper==1) $costingQty=12;
			else if($costingper==2) $costingQty=1;
			else if($costingper==3) $costingQty=24;
			else if($costingper==4) $costingQty=36;
			else if($costingper==5) $costingQty=48;
			else $costingQty=0;
			
			$jobCostingper[$brow[csf('job_no')]]=$costingQty;
			
			$cmCost=$cmArr[$brow[csf('job_no')]]/$costingQty;
			
			$cmCostArr[$brow[csf('job_no')]]=$cmCost;
		}
		unset($bomMstSql);
		
		//print_r($jobCostingper); die;
		$process_id_knitting_array = array(1,2,3,4);
		$process_id_yd_array = array(30,36,384);
		$process_id_aop_array = array(35,37,40,209,393,452);
		$process_id_washing_array = array(64,140,193,201,231,233,239,242,397,438,439,451);
		$process_id_fabricdyeing_array = array(25,26,31,32,33,34,38,39,61,62,63,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,100,125,127,128,129,135,136,137,138,139,141,142,143,144,145,146,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,190,191,192,194,195,196,197,198,199,200,202,204,205,206,207,208,210,211,212,218,219,220,221,222,223,224,225,227,229,230,232,234,238,240,243,244,245,246,247,248,249,250,251,252,254,255,256,257,258,259,260,261,262,263,264,265,266,267,268,269,277,278,279,281,282,283,284,285,286,287,288,290,291,292,293,294,295,296,297,298,299,300,303,304,305,306,307,308,309,310,311,312,313,314,315,316,317,318,319,320,321,322,323,324,325,326,327,328,329,330,331,332,333,334,335,336,337,338,339,340,341,342,343,344,345,346,347,348,349,350,351,352,353,354,355,356,357,358,359,360,361,362,363,364,365,366,368,369,370,371,372,373,374,375,376,377,378,379,380,381,382,383,385,386,387,388,390,391,398,399,400,401,402,403,404,405,406,407,408,409,410,411,412,413,414,415,416,417,418,419,420,421,422,423,424,425,426,427,428,429,430,432,433,434,435,436,437,440,441,442,443,453,454,455,456,457,458,459,460);
		
		$bomConv="select A.FABRIC_DESCRIPTION, A.JOB_NO, A.CONS_PROCESS, A.REQ_QNTY from wo_pre_cost_fab_conv_cost_dtls a, gbl_temp_engine b where a.status_active=1 and a.is_deleted=0  and a.job_id=b.ref_val and b.user_id = ".$user_id." and b.entry_form=1 and b.ref_from=2";
		$bomConvSql=sql_select($bomConv);
		
		foreach($bomConvSql as $brow)
		{
			if (in_array($brow['CONS_PROCESS'], $process_id_yd_array))// Yarn Dyeing
			{
				$fabricConvConsArr[$brow['JOB_NO']][$fabfinishconsarr[$brow['FABRIC_DESCRIPTION']]['gmtitem']]['ydyeing']+=$fabfinishconsarr[$brow['FABRIC_DESCRIPTION']]['greycons'];//$brow[csf('req_qnty')];
				$fabricConvConsArr[$brow['JOB_NO']]['isydyeing']=1;
			}
			else if (in_array($brow['CONS_PROCESS'], $process_id_washing_array)) // Washing
			{
				$fabricConvConsArr[$brow['JOB_NO']][$fabfinishconsarr[$brow['FABRIC_DESCRIPTION']]['gmtitem']]['washing']+=$fabfinishconsarr[$brow['FABRIC_DESCRIPTION']]['fincons'];//$brow[csf('req_qnty')];
				$fabricConvConsArr[$brow['JOB_NO']]['iswashing']=1;
			}
			else if (in_array($brow['CONS_PROCESS'], $process_id_knitting_array))// Knitting
			{
				$fabricConvConsArr[$brow['JOB_NO']][$fabfinishconsarr[$brow['FABRIC_DESCRIPTION']]['gmtitem']]['kniting']+=$fabfinishconsarr[$brow['FABRIC_DESCRIPTION']]['fincons'];//$brow[csf('req_qnty')];
				$fabricConvConsArr[$brow['JOB_NO']]['iskniting']=1;
			}
			else if (in_array($brow['CONS_PROCESS'], $process_id_aop_array))// Aop
			{
				$fabricConvConsArr[$brow['JOB_NO']][$fabfinishconsarr[$brow['FABRIC_DESCRIPTION']]['gmtitem']]['aop']+=$fabfinishconsarr[$brow['FABRIC_DESCRIPTION']]['fincons'];//$brow[csf('req_qnty')];
				$fabricConvConsArr[$brow['JOB_NO']]['isaop']=1;
			}
			else if (in_array($brow['CONS_PROCESS'], $process_id_fabricdyeing_array))// Fabric Dyeing
			{
				$fabricConvConsArr[$brow['JOB_NO']][$fabfinishconsarr[$brow['FABRIC_DESCRIPTION']]['gmtitem']]['fabdyeing']+=$fabfinishconsarr[$brow['FABRIC_DESCRIPTION']]['fincons'];//$brow[csf('req_qnty')];
				$fabricConvConsArr[$brow['JOB_NO']]['isfabdyeing']=1;
			}
		}
		unset($bomConvSql);
		  
		$sql_print_embroid=sql_select("select min(A.ID) AS ID, A.JOB_NO, A.EMB_NAME, avg(A.CONS_DZN_GMTS) AS CONS_DZN_GMTS, A.BODY_PART_ID, A.EMB_TYPE, c.PO_BREAK_DOWN_ID, c.ITEM_NUMBER_ID, c.COLOR_NUMBER_ID from wo_pre_cost_embe_cost_dtls a, gbl_temp_engine b, wo_pre_cos_emb_co_avg_con_dtls c where a.id=c.pre_cost_emb_cost_dtls_id and a.emb_name in(1,2,3) and c.requirment>0 and a.status_active=1 and a.is_deleted=0 and a.job_id=b.ref_val and b.user_id = ".$user_id." and b.entry_form=1 and b.ref_from=2 group by a.job_no, a.emb_name, a.body_part_id, a.emb_type, c.PO_BREAK_DOWN_ID, c.ITEM_NUMBER_ID, c.COLOR_NUMBER_ID");
	
		$orderDataArr=array();
		foreach($sql_print_embroid as $row_print_embroid)
		{
			if($row_print_embroid['EMB_NAME']==1)
			{
				$ft_data_arr[$row_print_embroid['JOB_NO']][dv_printing]=1; //P^WC:15
				$ft_data_arr[$row_print_embroid['JOB_NO']][rv_printing]=1; //P^WC:20
				
				$orderDataArr[$row_print_embroid['JOB_NO']][$row_print_embroid['PO_BREAK_DOWN_ID']][$row_print_embroid['ITEM_NUMBER_ID']][$row_print_embroid['COLOR_NUMBER_ID']]['printbodypart'].=$body_part[$row_print_embroid['BODY_PART_ID']].',';
				$orderDataArr[$row_print_embroid['JOB_NO']][$row_print_embroid['PO_BREAK_DOWN_ID']][$row_print_embroid['ITEM_NUMBER_ID']][$row_print_embroid['COLOR_NUMBER_ID']]['printtype'].=$emblishment_print_type[$row_print_embroid['EMB_TYPE']].',';
			}
			if($row_print_embroid['EMB_NAME']==2)
			{
				$ft_data_arr[$row_print_embroid['JOB_NO']][dv_embrodi]=1; //P^WC:25
				$ft_data_arr[$row_print_embroid['JOB_NO']][rv_embrodi]=1; //P^WC:30
			}
			if($row_print_embroid['EMB_NAME']==3)
			{
				$ft_data_arr[$row_print_embroid['JOB_NO']][dv_wash]=1; //P^WC:145
				$ft_data_arr[$row_print_embroid['JOB_NO']][rv_wash]=1; //P^WC:150
				$orderDataArr[$row_print_embroid['JOB_NO']][$row_print_embroid['PO_BREAK_DOWN_ID']][$row_print_embroid['ITEM_NUMBER_ID']][$row_print_embroid['COLOR_NUMBER_ID']]['washtype'].=$emblishment_wash_type[$row_print_embroid['EMB_TYPE']].',';
			}
		}
		unset($sql_print_embroid);
		
		$sqlYarnCount="select A.JOB_NO, A.COUNT_ID from wo_pre_cost_fab_yarn_cost_dtls a, gbl_temp_engine b where a.status_active=1 and a.is_deleted=0 and a.job_id=b.ref_val and b.user_id = ".$user_id." and b.entry_form=1 and b.ref_from=2";
		$sqlYarnCountRes=sql_select($sqlYarnCount);
		$yCountDataArr=array();
		foreach($sqlYarnCountRes as $brow)
		{
			$yCountDataArr[$brow['JOB_NO']]['ydyeing'].=$yarnCountArr[$brow['COUNT_ID']].',';
		}
		unset($sqlYarnCountRes);
		  
		//=================================Item wise Array Srart=====================================
		$arr_itemsmv=array();
		$sql_itemsmv=sql_select("select a.job_no, a.gmts_item_id, a.set_item_ratio, a.smv_pcs_precost, a.smv_set_precost, a.smv_pcs, a.embelishment from wo_po_details_mas_set_details a, gbl_temp_engine b where a.job_id=b.ref_val and b.user_id = ".$user_id." and b.entry_form=1 and b.ref_from=2");
		foreach($sql_itemsmv as $row_itemsmv)
		{
			$arr_itemsmv[$row_itemsmv[csf('job_no')]][$row_itemsmv[csf('gmts_item_id')]]['smv']=$row_itemsmv[csf('smv_pcs')]; 
			//$arr_itemsmv[$row_itemsmv[csf('job_no')]][$row_itemsmv[csf('gmts_item_id')]]['emb']=$row_itemsmv[csf('embelishment')];  
		}
		
		// Products file
		$txt="";
		$file_name="frfiles/PRODUCTS.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
	 	
		$txt .=str_pad("P.CODE",0," ")."\t".str_pad("P.OLDCODE",0," ")."\t".str_pad("P.TYPE",0," ")."\t".str_pad("P.CUST",0," ")."\t".str_pad("P^WC:8",0," ")."\t".str_pad("P^CF:8",0," ")."\t".str_pad("P^WC:10",0," ")."\t".str_pad("P^CF:10",0," ")."\t".str_pad("P^WC:22",0," ")."\t".str_pad("P^CF:22",0," ")."\t".str_pad("P^WC:26",0," ")."\t".str_pad("P^CF:26",0," ")."\t".str_pad("P^WC:30",0," ")."\t".str_pad("P^CF:30",0," ")."\t".str_pad("P^WC:40",0," ")."\t".str_pad("P^CF:40",0," ")."\t".str_pad("P^WC:50",0," ")."\t".str_pad("P^CF:50",0," ")."\t".str_pad("P^WC:70",0," ")."\t".str_pad("P^WC:80",0," ")."\t".str_pad("P^WC:90",0," ")."\t".str_pad("P^WC:100",0," ")."\t".str_pad("P^WC:110",0," ")."\t".str_pad("P^WC:120",0," ")."\t".str_pad("P^WC:230",0," ")."\t".str_pad("P^WC:240",0," ")."\t".str_pad("P^WC:250",0," ")."\t".str_pad("P^WC:260",0," ")."\t".str_pad("P^WC:280",0," ")."\t".str_pad("P^WC:290",0," ")."\r\n";
		
		foreach($ft_data_arr as $jobno=>$rows)
		{
			$gitem=explode(",",$rows['gmts_item_id']);
			
			$dt=""; $inc=0; $changed=0; $old_style=""; $oldcode=""; $old_item_code=""; $fabdyeingReq=$fabfabfinishReq=0;
			$style_ref_arr[$jobno]=$rows['style_ref_no'];
			$job_ref_arr[$jobno]=$jobno;
			//$job_ref_arr[$rows[job_no]]=$rows[job_no];
			
			if( trim($rows['style_ref_no_prev'])!='' && trim($rows['style_ref_no_prev'])!=$rows['style_ref_no'])
			{
				$old_style=trim($rows['style_ref_no_prev']);
				$changed=1;
			}
			else
				$old_style= $rows['style_ref_no'];
			
			if(count($gitem)>1)
			{
				$item_array[$jobno]=1;
				foreach($gitem as $id)
				{
					
					$yarnReq=($fabricGConsArr[$jobno][$id]['cons_costingper']*1)/($jobCostingper[$jobno]*1);
					$ydReq=($fabricConvConsArr[$jobno][$id]['ydyeing']*1)/($jobCostingper[$jobno]*1);
					$grayCons=$yarnReq;
					$fabdyeingReq=($fabricConvConsArr[$jobno][$id]['fabdyeing']*1)/($jobCostingper[$jobno]*1);
					$fabfabfinishReq=($fabricConvConsArr[$jobno][$id]['fabfinish']*1)/($jobCostingper[$jobno]*1);
					$aopcons=($fabricGConsArr[$jobno][$id]['aop_costingper']*1)/($jobCostingper[$jobno]*1);
					//$new_job_no[$rows[job_no]]=$rows[job_no]
					//if($dt=="") $dt=$fr_product_type[$id]; else $dt .=",".$fr_product_type[$id];
					//$array_fabric_cons_item[$row_fabric_cons_item[csf('job_no')]][$row_fabric_cons_item[csf('item_number_id')]]
					//$img_prod[$jobno][]=$jobno."::".$fr_product_type[$id];
					
					$pgitem=explode(",",trim($rows['gmts_item_id_prev']));
					if(count($pgitem)>1)
					{
						foreach( $pgitem as $iid )
						{
							if( $iid!="")
							{
								$changed=1;
								$old_item_code="::".$fr_product_type[$iid];
							}
							$inc++;
						}
					}
					else
						$old_item_code=$fr_product_type[$id];//$old_item_code="::".$fr_product_type[$gitem_id];
						
					if($changed==1)
					{
						$oldcode=$old_style."::".$jobno."::".$old_item_code;
					}
					
					if(trim($jobno)!="") $txt .=str_pad($rows['style_ref_no']."::".$jobno."::".$fr_product_type[$id],0," ")."\t".str_pad($oldcode,0," ")."\t".str_pad($fr_product_type[$id],0," ")."\t".str_pad($buyer_name_array[$rows['buyer_name']],0," ")."\t".str_pad("1",0," ")."\t".str_pad(fn_number_format($yarnReq, 4, '.', ''),0," ")."\t".str_pad($fabricConvConsArr[$jobno]['isydyeing'] == '' ? 0 : $fabricConvConsArr[$jobno]['isydyeing'],0," ")."\t".str_pad(fn_number_format($ydReq, 4, '.', ''),0," ")."\t".str_pad("1",0," ")."\t".str_pad(fn_number_format($grayCons, 4, '.', ''),0," ")."\t".str_pad("1",0," ")."\t".str_pad(fn_number_format($fabdyeingReq, 4, '.', ''),0," ")."\t".str_pad("1",0," ")."\t".str_pad(fn_number_format($fabfabfinishReq, 4, '.', ''),0," ")."\t".str_pad($fabricConvConsArr[$jobno]['isaop']== '' ? 0 :$fabricConvConsArr[$jobno]['isaop'],0," ")."\t".str_pad($fabricConvConsArr[$jobno]['isaop']== '' ? 0 :$fabricConvConsArr[$jobno]['isaop'],0," ")."\t".str_pad($fabricConvConsArr[$jobno]['isaop'] == '' ? 0 :$fabricConvConsArr[$jobno]['isaop'],0," ")."\t".str_pad($fabricConvConsArr[$jobno]['isaop'] == '' ? 0 :$fabricConvConsArr[$jobno]['isaop'],0," ")."\t".str_pad("1",0," ")."\t".str_pad($rows[dv_printing] == '' ? 0 : $rows[dv_printing],0," ")."\t".str_pad($rows[rv_printing] == '' ? 0 : $rows[rv_printing],0," ")."\t".str_pad($rows[dv_embrodi] == '' ? 0 : $rows[dv_embrodi],0," ")."\t".str_pad($rows[rv_embrodi] == '' ? 0 : $rows[rv_embrodi],0," ")."\t".str_pad("1",0," ")."\t".str_pad($arr_itemsmv[$jobno][$id]['smv'] == '' ? 0 :$arr_itemsmv[$jobno][$id]['smv'],0," ")."\t".str_pad($rows[dv_wash] == '' ? 0 :$rows[dv_wash],0," ")."\t".str_pad($rows[rv_wash] == '' ? 0 :$rows[rv_wash],0," ")."\t".str_pad("1",0," ")."\t".str_pad("1",0," ")."\t".str_pad("1",0," ")."\r\n";
				}
			}
			else
			{
				//$img_prod[$rows[job_no]][]=$rows[job_no];
				//echo $fabricGConsArr[$jobno][$rows['gmts_item_id']]['cons_costingper'].'--'.$jobCostingper[$jobno].'<br>';
				
				$yarnReq=($fabricGConsArr[$jobno][$rows['gmts_item_id']]['cons_costingper']*1)/($jobCostingper[$jobno]*1);
				$ydReq=($fabricConvConsArr[$jobno][$rows['gmts_item_id']]['ydyeing']*1)/($jobCostingper[$jobno]*1);
				$grayCons=$yarnReq;
				$fabdyeingReq=($fabricConvConsArr[$jobno][$rows['gmts_item_id']]['fabdyeing']*1)/($jobCostingper[$jobno]*1);
				$fabfabfinishReq=($fabricConvConsArr[$jobno][$rows['gmts_item_id']]['fabfinish']*1)/($jobCostingper[$jobno]*1);
				$aopcons=($fabricGConsArr[$jobno][$rows['gmts_item_id']]['aop_costingper']*1)/($jobCostingper[$jobno]*1);
					
				if($changed==1)
				{
					$oldcode=$old_style."::".$jobno;
				}
				
				if(trim($jobno)!="") $txt .=str_pad($rows['style_ref_no']."::".$jobno,0," ")."\t".str_pad($oldcode,0," ")."\t".str_pad($fr_product_type[$rows['gmts_item_id']],0," ")."\t".str_pad($buyer_name_array[$rows['buyer_name']],0," ")."\t".str_pad("1",0," ")."\t".str_pad(fn_number_format($yarnReq, 4, '.', ''),0," ")."\t".str_pad($fabricConvConsArr[$jobno]['isydyeing'] == '' ? 0 : $fabricConvConsArr[$jobno]['isydyeing'],0," ")."\t".str_pad(fn_number_format($ydReq, 4, '.', ''),0," ")."\t".str_pad("1",0," ")."\t".str_pad(fn_number_format($grayCons, 4, '.', ''),0," ")."\t".str_pad("1",0," ")."\t".str_pad(fn_number_format($fabdyeingReq, 4, '.', ''),0," ")."\t".str_pad("1",0," ")."\t".str_pad(fn_number_format($fabfabfinishReq, 4, '.', ''),0," ")."\t".str_pad($fabricConvConsArr[$jobno]['isaop']== '' ? 0 :$fabricConvConsArr[$jobno]['isaop'],0," ")."\t".str_pad($fabricConvConsArr[$jobno]['isaop']== '' ? 0 :$fabricConvConsArr[$jobno]['isaop'],0," ")."\t".str_pad($fabricConvConsArr[$jobno]['isaop'] == '' ? 0 :$fabricConvConsArr[$jobno]['isaop'],0," ")."\t".str_pad($fabricConvConsArr[$jobno]['isaop'] == '' ? 0 :$fabricConvConsArr[$jobno]['isaop'],0," ")."\t".str_pad("1",0," ")."\t".str_pad($rows[dv_printing] == '' ? 0 : $rows[dv_printing],0," ")."\t".str_pad($rows[rv_printing] == '' ? 0 : $rows[rv_printing],0," ")."\t".str_pad($rows[dv_embrodi] == '' ? 0 : $rows[dv_embrodi],0," ")."\t".str_pad($rows[rv_embrodi] == '' ? 0 : $rows[rv_embrodi],0," ")."\t".str_pad("1",0," ")."\t".str_pad($arr_itemsmv[$jobno][$rows['gmts_item_id']]['smv'] == '' ? 0 :$arr_itemsmv[$jobno][$rows['gmts_item_id']]['smv'],0," ")."\t".str_pad($rows[dv_wash] == '' ? 0 :$rows[dv_wash],0," ")."\t".str_pad($rows[rv_wash] == '' ? 0 :$rows[rv_wash],0," ")."\t".str_pad("1",0," ")."\t".str_pad("1",0," ")."\t".str_pad("1",0," ")."\r\n";
			}
		}
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
		//die;
		
		// Orders file
		$file_name="frfiles/ORDERS.TXT";
		$myfile = fopen( $file_name, "w" ) or die("Unable to open file!");
		
		$sqlOldcode="select job_no, po_id, color_mst_id, item_mst_id, shipdate, new_code from fr_temp_code where user_id=".$_SESSION['logic_erp']['user_id']."";
		$nameArray=sql_select($sqlOldcode); $oldCodeArr=array();
		foreach($nameArray as $orow)
		{
			$oldCodeArr[$orow[csf("job_no")]][$orow[csf("po_id")]][$orow[csf("color_mst_id")]][$orow[csf("item_mst_id")]]=$orow[csf("new_code")];
		}
		unset($nameArray);
	  	
		$txt .=str_pad("O.CODE",0," ")."\t".str_pad("O.OLDCODE",0," ")."\t".str_pad("O.PROD",0," ")."\t".str_pad("O.CUST",0," ")."\t".str_pad("O^DD:1",0," ")."\t".str_pad("O^DQ:1",0," ")."\t".str_pad("O.CONTRACT_QTY",0," ")."\t".str_pad("O.STATUS",0," ")."\t".str_pad("O.SPRICE",0," ")."\t".str_pad("O.SCOST",0," ")."\t".str_pad("O.COMPLETE",0," ")."\t".str_pad("O.UDCountries",0," ")."\t".str_pad("O.UDDia",0," ")."\t".str_pad("O.UDFabric Type",0," ")."\t".str_pad("O.UDFiber Compstn",0," ")."\t".str_pad("O.UDGSM",0," ")."\t".str_pad("O.UDPrint Placement",0," ")."\t".str_pad("O.UDPrint Type",0," ")."\t".str_pad("O.UDSMV",0," ")."\t".str_pad("O.UDWash",0," ")."\t".str_pad("O.UDY/D",0," ")."\t".str_pad("O.UDYarn Count",0," ")."\r\n";
		
		$newid_ar=array(); $tmpNewCodeArr=array();
		foreach($orders_arr as $jobno=>$job_data)
		{
			foreach($job_data as $poid=>$po_data)
			{
				foreach($po_data as $gitem_id=>$item_data)
				{
					$a=0;
					foreach($item_data as $cdate=>$cdate_data)
					{
						$a++;
						//$devseq[$jobno][$gitem_id]++;
						//$devseq[$poid][$gitem_id]+=1;
						foreach($cdate_data as $colorid=>$exdata)
						{
							$old_str_po=''; $old_item_code=''; $changed=0; $old_po=$str_po=$countryStr=$fabCompo=$fabGsm=$printbodypartdataStr=$printprintdataStr=$washdataStr=$yCountStr="";
							$gitem=explode(",",$exdata['gmts_item_id']);
							//echo count($gitem).'=';
							if( count($gitem)>1) $str_item="::".$fr_product_type[$gitem_id]; else $str_item=""; 
							//echo $str_item.'-'.$gitem_id.'=';
							
							$excountryid=array_filter(array_unique(explode(",",$exdata['countryid'])));
							$countryStr="";
							foreach($excountryid as $counid)
							{
								if($countryStr=="") $countryStr=$countryArr[$counid]; else $countryStr.=', '.$countryArr[$counid];
							}

							if( count($gitem)==1)
							{
								 $str_po=$exdata['internal_ref']."::".$exdata['po_number']."::".$color_lib[$colorid]."::".$a."::".$jobno;
								 $str_po_oprod=$exdata['style_ref']."::".$jobno;
							}
							else 
							{
								$str_po=$exdata['internal_ref']."::".$exdata['po_number']."::".$color_lib[$colorid]."::".$a."::".$jobno."".$str_item;  
								//$str_po=$exdata['internal_ref']."::".$exdata['po_number']."::".$color_lib[$colorid]."::".$cutoff[$poid."::".$color_lib[$colorid]."".$str_item]."::".$jobno."".$str_item;  
								$str_po_oprod=$exdata['style_ref']."::".$jobno."".$str_item;
							}
							
							$icmstid=$exdata['icmstid'];
							$exicid=explode("_",$icmstid);
							
							$itemMstId=$exicid[0];
							$colorMstId=$exicid[1];
							
							$oldCode=$oldCodeArr[$jobno][$poid][$colorMstId][$itemMstId];
							
							$tmpNewCodeArr[$str_po]=$jobno."__".$poid."__".$itemMstId."__".$colorMstId."__".$cdate."__".$oldCode; 
							
							/*$pgitem=explode(",",trim($exdata['gmts_item_id_prev']));
							$inc=0;
							if(count($pgitem)>1)
							{
								foreach( $pgitem as $iid )
								{
									if( $iid!="")
									{
										$changed=1;
										$old_item_code="::".$fr_product_type[$iid];
									}
									$inc++;
								}
							}
							else
								$old_item_code=$str_item;//$old_item_code="::".$fr_product_type[$gitem_id];
							
							if( trim($exdata['po_number_prev'])!='' && trim($exdata['po_number_prev'])!=$exdata['po_number'])
							{
								$old_po=trim($exdata['po_number_prev']);
								$changed=1;
							}
							else
								$old_po= $exdata['po_number'];
								
							if( $exdata['color_number_id_prev']!='' && $exdata['color_number_id_prev']!=$colorid)
							{
								if( $exdata['color_number_id_prev']!=0)
								{
									$old_color= trim($exdata['color_number_id_prev']);
									$changed=1;
								}
							}
							else
								$old_color= $colorid;
								
							$old_str_po="";	
							if($changed==1)
							{
								if(count($gitem_id)==1)
								{
									$old_str_po=$exdata['internal_ref']."::".$old_po."::".$color_lib[$old_color]."::".$a."::".$jobno; 
									//$name[csf("job_no_mst")]."::".$old_po."::".$color_lib[$old_color]."::".$old_pub_ship."".$old_item_code; 
								}
								else
								{
									$old_str_po=$exdata['internal_ref']."::".$old_po."::".$color_lib[$old_color]."::".$a."::".$jobno."".$old_item_code;   
								}
							}*/
							$nid=array_filter(array_unique(explode(",",$exdata['cid'])));
							foreach($nid as $vid)
							{
								$newid_ar[$vid]=$str_po;
							}
							
							if($dtls_id=="") $dtls_id=$exdata['id']; else $dtls_id .=",".$exdata['id'];
							$str="";
							if( $exdata['is_confirmed']==1) $str="F"; else $str="P";
							
							$deletestatusArr=array_filter(array_unique(explode(",",$exdata['is_deleted'])));
							//print_r($deletestatusArr);
							$isdel=0;
							foreach($deletestatusArr as $delsts)
							{
								if(str_replace("'", "", $delsts)==0) $isdel=1;
							}
							
							if($isdel!=1 ) {
								$str="X";   
								$orderAmr=$exdata['order_total_del']/$exdata['order_qty_del'];
								
								$exdata['order_quantity']=$exdata['order_qty_del'];
								$exdata['plan_cut_new']=$exdata['plan_cut_del'];
								$exdata['order_total']=$exdata['order_total_del'];
							}
							else
							{
								$orderAmr=$exdata['order_total']/$exdata['order_quantity'];
								
								$exdata['order_quantity']=$exdata['order_quantity'];
								$exdata['plan_cut_new']=$exdata['plan_cut_new'];
								$exdata['order_total']=$exdata['order_total'];
							}
							//$countryStr=implode(",",);
							$diaStr=implode(",",array_filter(array_unique(explode(",",$job_diaarr[$jobno]))));
							$fabConst=implode(",",array_filter(array_unique(explode("___",$fabricGConsArr[$jobno][$gitem_id]['const']))));
							$fabCompo=implode(",",array_filter(array_unique(explode("___",$fabricGConsArr[$jobno][$gitem_id]['compo']))));
							$fabGsm=implode(",",array_filter(array_unique(explode("___",$fabricGConsArr[$jobno][$gitem_id]['gsm']))));
							$printbodypartdataStr=implode(",",array_filter(array_unique(explode(",",$orderDataArr[$jobno][$poid][$gitem_id][$colorid]['printbodypart']))));
							$printprintdataStr=implode(",",array_filter(array_unique(explode(",",$orderDataArr[$jobno][$poid][$gitem_id][$colorid]['printtype']))));
							$styleSmv=$arr_itemsmv[$jobno][$gitem_id]['smv']*$exdata['order_quantity'];
							$washdataStr=implode(",",array_filter(array_unique(explode(",",$orderDataArr[$jobno][$poid][$gitem_id][$colorid]['washtype']))));
							$yCountStr=implode(",",array_filter(array_unique(explode(",",$yCountDataArr[$jobno]['ydyeing']))));
							
							$noldCode="";
							if($oldCode!=$str_po) $noldCode=$oldCode;
							
							$txt .=str_pad($str_po,0," ")."\t".str_pad($noldCode,0," ")."\t".str_pad($str_po_oprod,0," ")."\t".str_pad( $exdata['buyer'],0," ")."\t".str_pad(date("d/m/Y",strtotime($cdate)),0," ")."\t".str_pad($exdata['plan_cut_new'],0," ")."\t".str_pad($exdata['order_quantity'],0," ")."\t".str_pad($str,0," ")."\t".str_pad($orderAmr,0," ")."\t".str_pad($cmCostArr[$jobno],0," ")."\t".str_pad($exdata['shiping_status'],0," ")."\t".str_pad($countryStr,0," ")."\t".str_pad($diaStr,0," ")."\t".str_pad($fabConst,0," ")."\t".str_pad($fabCompo,0," ")."\t".str_pad($fabGsm,0," ")."\t".str_pad($printbodypartdataStr,0," ")."\t".str_pad($printprintdataStr,0," ")."\t".str_pad($styleSmv,0," ")."\t".str_pad($washdataStr,0," ")."\t".str_pad($fabricConvConsArr[$jobno]['isyd'] == '' ? 'No' :$fabricConvConsArr[$jobno]['isyd'],0," ")."\t".str_pad($yCountStr,0," ")."\r\n";
						}
						//$devseq[$poid][$gitem_id][$cdate]++;
					}
				}
			}
		}
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
		//die;
		//print_r($newid_ar); die;
		
		$con=connect();
		execute_query("delete from fr_temp_code where user_id=".$_SESSION['logic_erp']['user_id']."",0);
		oci_commit($con);
		foreach($tmpNewCodeArr as $ncode=>$str_val)
		{
			$exstrVal=explode("__",$str_val);
			$jobno=$po_id=$itemMstId=$colorMstId=$shipdate=$oldCode="";
			//echo $str_val.'<br>';
			$jobno=$exstrVal[0];
			$po_id=$exstrVal[1];
			$itemMstId=$exstrVal[2];
			$colorMstId=$exstrVal[3];
			$shipdate=date("d-M-Y",strtotime($exstrVal[4]));
			$oldCode=$exstrVal[5];
			//echo "insert into fr_temp_code (job_no, po_id, color_mst_id, item_mst_id, shipdate, new_code, old_code, user_id) values ('".$jobno."','".$po_id."','".$colorMstId."','".$itemMstId."','".$shipdate."','".$ncode."','".$oldCode."',".$_SESSION['logic_erp']['user_id'].")<br>";
			execute_query("insert into fr_temp_code (job_no, po_id, color_mst_id, item_mst_id, shipdate, new_code, old_code, user_id) values ('".$jobno."','".$po_id."','".$colorMstId."','".$itemMstId."','".$shipdate."','".$ncode."','".$oldCode."',".$_SESSION['logic_erp']['user_id'].")");
		}
		oci_commit($con);
		disconnect($con);
		
		// Production file
		$prod_sql='SELECT a.po_break_down_id as "po_break_down_id", a.country_id as "country_id", a.item_number_id as "item_number_id", a.floor_id as "floor_id", a.sewing_line as "sewing_line", a.production_type as "production_type", a.production_source as "production_source", a.production_date as "production_date", b.color_size_break_down_id as "color_size_break_down_id", b.production_qnty AS "production_quantity", a.embel_name as "embel_name", a.status_active as "status_active", a.is_deleted as "is_deleted" from pro_garments_production_mst a, pro_garments_production_dtls b, gbl_temp_engine c where a.production_type in (1,2,3,4,5,7,8,11) and a.id=b.mst_id and a.po_break_down_id=c.ref_val and c.user_id = '.$user_id.' and c.entry_form=1 and c.ref_from=1 order by production_date, b.color_size_break_down_id asc'; //$poIds_cond_prod tmp_poid (userid, poid)  and a.embel_name in (1,2,3,4,5)  and a.is_deleted=0 and a.status_active=1  and c.userid='$user_id'
		//echo $prod_sql; die;
		$prod_sql_res=sql_select($prod_sql);$q=0;
		foreach($prod_sql_res as $row_sew)
		{
			if($deleted_po_list[$row_sew["po_break_down_id"]]!='')
				$deleted_colors[$row_sew["color_size_break_down_id"]]=$row_sew["color_size_break_down_id"];
			//if( $row_sew["embel_name"]==3 ) echo $newid_ar[$row_sew["color_size_break_down_id"]].'-'.$prod_up_arr_powise[$row_sew['color_size_break_down_id']].'-'.$row_sew["po_break_down_id"]."<br>";
			
			if($newid_ar[$row_sew["color_size_break_down_id"]]!='' && $prod_up_arr_powise[$row_sew['color_size_break_down_id']]==$row_sew["po_break_down_id"] )
			{
				//echo $q++."<br>";
				//echo $row_sew["production_type"];
				if($row_sew["production_type"]==3)//issue
				{
					if($row_sew["embel_name"]==1) $row_sew["production_type"]=80;
					else if($row_sew["embel_name"]==2) $row_sew["production_type"]=100;
					else if( $row_sew["embel_name"]==3 ) $row_sew["production_type"]=240;
				}
				else if($row_sew["production_type"]==2) // send emb
				{
					if($row_sew["embel_name"]==1) $row_sew["production_type"]=90;
					else if($row_sew["embel_name"]==2) $row_sew["production_type"]=110;
					else if( $row_sew["embel_name"]==3 ) $row_sew["production_type"]=250;
				}
				if($row_sew["production_type"]==1) $row_sew["production_type"]=70;
				if($row_sew["production_type"]==4) $row_sew["production_type"]=120;
				if($row_sew["production_type"]==5) $row_sew["production_type"]=230;
				//if($row_sew["production_type"]==7) $row_sew["production_type"]=270;
				if($row_sew["production_type"]==8) $row_sew["production_type"]=280;
				if($row_sew["production_type"]==11) $row_sew["production_type"]=7;
				
				if( $row_sew["status_active"]==0 && $row_sew["is_deleted"]==1 ) $row_sew["production_quantity"]=0; 
				
				$row_sew["sewing_line"]=(int)$line_name_res[$row_sew["sewing_line"]]; 
				
				if( $row_sew["production_type"]!=230 ) $row_sew["sewing_line"]=0;
				//if( $prod_typ==8 ||$prod_typ==1 ||$prod_typ==9 ||$prod_typ==10 ||$prod_typ==4 ) $row_sew["sewing_line"]=0;
				$exfdate=$row_sew["production_date"];
				/*$lineName="";
				if($row_sew["sewing_line")]!="") 
				{
					$exLine=explode(",",$row_sew["sewing_line"]);
					foreach($exLine as $lid)
					{
						if($lineName=="") $lineName=$line_name[$lid]; else $lineName.=','.$line_name[$lid];
					}
				}*/
				
				$production_qty[$newid_ar[$row_sew["color_size_break_down_id"]]][$row_sew["production_type"]][$row_sew["sewing_line"]]['qnty']+=$row_sew["production_quantity"];
				
				$production_qty[$newid_ar[$row_sew["color_size_break_down_id"]]][$row_sew["production_type"]][$row_sew["sewing_line"]]['pdate']=$exfdate; 
				$production_qty[$newid_ar[$row_sew["color_size_break_down_id"]]][$row_sew["production_type"]][$row_sew["sewing_line"]]['col_size_id']=$row_sew["color_size_break_down_id"]; 
			}
		}
		unset($prod_sql_res);
		/*echo"10** <pre>";
		print_r($production_qty); die;*/
	
		$prod_sql="SELECT a.mst_id, a.color_size_break_down_id, a.production_qnty, b.ex_factory_date, b.shiping_status, b.po_break_down_id from pro_ex_factory_dtls a, pro_ex_factory_mst b, gbl_temp_engine c where b.po_break_down_id=c.ref_val and c.user_id = ".$user_id." and c.entry_form=1 and c.ref_from=1 and b.id=a.mst_id and a.is_deleted=0 and a.status_active=1 and a.mst_id=b.id and b.is_deleted=0 and b.status_active=1 and c.user_id='$user_id'";// and c.userid='$user_id'
		//echo $prod_sql; die;
		
		$prod_sql_res=sql_select($prod_sql); $exFactoryArr=array();
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2) and ENTRY_FORM=1");
		oci_commit($con);
		disconnect($con);
		foreach($prod_sql_res as $row_sew)
		{
			if($newid_ar[$row_sew[csf("color_size_break_down_id")]]!='' && $deleted_colors[$row_sew[csf("color_size_break_down_id")]]=='') 
			{
				$row_sew[csf("production_type")]=290;
				$row_sew[csf("sewing_line")]=0;
				
				if($row_sew[csf("shiping_status")]==3)
				{
					$exFactoryArr[$newid_ar[$row_sew[csf("color_size_break_down_id")]]]=$row_sew[csf("ex_factory_date")];
				}
				
				$production_qty[$newid_ar[$row_sew[csf("color_size_break_down_id")]]][$row_sew[csf("production_type")]][$row_sew[csf("sewing_line")]]['qnty']+=$row_sew[csf("production_qnty")];
				$production_qty[$newid_ar[$row_sew[csf("color_size_break_down_id")]]][$row_sew[csf("production_type")]][$row_sew[csf("sewing_line")]]['pdate']=$row_sew[csf("ex_factory_date")]; 
				$production_qty[$newid_ar[$row_sew[csf("color_size_break_down_id")]]][$row_sew[csf("production_type")]][$row_sew[csf("sewing_line")]]['col_size_id']=$row_sew[csf("color_size_break_down_id")]; 
			}
		}
		unset($prod_sql_res);
		$file_name="frfiles/UPDNORM.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");

		$txt .=str_pad("U.ORDER",0," ")."\t".str_pad("U.DATE",0," ")."\t".str_pad("U.OPERATION",0," ")."\t".str_pad("U.LINE_EXTERNAL_ID",0," ")."\t".str_pad("U.QTY",0," ")."\r\n";
		foreach($production_qty as $oid=>$prodctn)
		{
			ksort($prodctn);
			foreach($prodctn as $prod_typ=>$prodflr)
			{
				ksort($prodflr);
				foreach($prodflr as $resoLine=>$sdata)
				{
					//if($prod_typ==5) { print_r($sdata); echo $line."="; }
					$libline="";
					$sdate=date("d/m/Y",strtotime($sdata['pdate']));
					if($resoLine==0 || $resoLine=="") $libline=""; else $libline=$line_array[$resoLine];//(int)$line_name_res[$line];  $line_array[$line_name[$line]]
					//$flor=$floor_name_res[$libline];
					//if($flor==0) $flor=""; 
					if($libline==0) $libline="";
					if( $prod_typ!=230 ) $libline="";
					
					if($prod_typ>0)
						$txt .=str_pad($oid,0," ")."\t ".str_pad($sdate,0," ")."\t".str_pad($prod_typ,0," ")."\t".str_pad($libline,0," ")."\t".str_pad($sdata['qnty'],0," ")."\r\n";
				}
			}	
		}
		
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
		
		//Attendance SMV
		
		$tpd_data_arr=sql_select( "select company_id, location_id, floor_id, line_marge, line_number, b.id, a.pr_date, sum(a.target_per_hour*a.working_hour) tpd, sum(a.man_power*a.working_hour) tsmv from prod_resource_dtls a, prod_resource_mst b where b.id=a.mst_id and b.id!=1 and a.is_deleted=0 and b.is_deleted=0 group by company_id, location_id, floor_id,line_marge,line_number,b.id,a.pr_date");
		$file_name="frfiles/ATTMINS.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
		$txt .="A.DATE\tA.GROUP\tA.GROUP_EXTERNAL_ID\tA.ROW\tA.ROW_EXTERNAL_ID\tA.MINUTES\r\n";
		
        foreach($tpd_data_arr as $row)
        {
			$txt .=date("d/m/Y",strtotime($row[csf('pr_date')]))."\t".$groupNameArr[$group_unit_array[$row[csf('floor_id')]]]."\t".$group_unit_array[$row[csf('floor_id')]]."\t".$lineNameArr[$line_array[$row[csf('line_number')]]]."\t".$line_array[$row[csf('line_number')]]."\t".($row[csf('tsmv')]*60)."\r\n";
        }
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt=""; 
	}
	else if ( $cbo_fr_integrtion==1 )
	{
		$sql=sql_select ( "select id,short_name from lib_buyer" );
		$file_name="frfiles/CUSTOMER.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
		$txt .=str_pad("C.CODE",15," ")."\tEND\r\n";
		foreach($sql as $name)
		{
			//if(strlen($name)>6) $txt .=$name."\tEND\r\n"; else 
			$txt .=str_pad($name[csf("short_name")],15," ")."\tEND\r\n";
		}
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
	}
	else if ( $cbo_fr_integrtion==2 )
	{
		$txt="";
		if(trim( $received_date)=="") $received_date="01-Aug-2018"; else $received_date=change_date_format($received_date, "yyyy-mm-dd", "-",1);
		// Products file Data
		//echo $received_date; die;
		if( $db_type==0 )
		{
			//$shipment_date="and c.country_ship_date >= '2014-11-01'";
			$shipment_date="and b.po_received_date >= '$received_date'";
		}
		if($db_type==2)
		{
			$shipment_date="and b.po_received_date >= '$received_date'";
		}
		$shiping_status="and c.shiping_status !=3";
		
		//letsgrowourfood.com
		
		$po_arr=array(); $ft_data_arr=array(); $jobCostingper=array();
		 
		$sql_po="select a.id, a.job_no, a.style_ref_no, a.gmts_item_id, a.buyer_name, b.po_quantity from wo_po_details_master a, wo_po_break_down b  where a.job_no=b.job_no_mst  and b.shipment_date >= '$received_date'  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.job_no='FFL-20-02215' order by a.id,b.id";
		$sql_po_data=sql_select($sql_po);// and b.is_confirmed=2 
		foreach($sql_po_data as $sql_po_row)
		{
			$ft_data_arr[$sql_po_row[csf('job_no')]][job_no]=$sql_po_row[csf('job_no')];//P.CODE
			$ft_data_arr[$sql_po_row[csf('job_no')]][gmts_item_id]=$sql_po_row[csf('gmts_item_id')];//P.TYPE
			$ft_data_arr[$sql_po_row[csf('job_no')]][style_ref_no]=$sql_po_row[csf('style_ref_no')];//P.DESCRIP
			$po_arr[job_no][$sql_po_row[csf('id')]]=$sql_po_row[csf('id')];
			$ft_data_arr[$sql_po_row[csf('job_no')]][buyer_name]=$sql_po_row[csf('buyer_name')];
			$ft_data_arr[$sql_po_row[csf('job_no')]][po_quantity]=$sql_po_row[csf('po_quantity')];
			//$po_arr[po_id][0]=0;
		}
		unset($sql_po_data);
		//echo "0**d";
		//print_r($ft_data_arr);
		//die;
		$sql_po="select a.id, a.job_no, a.buyer_name, a.style_ref_no, b.id as bid, b.po_quantity, b.unit_price, a.gmts_item_id, d.costing_per, d.sew_smv from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_mst d where  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and b.id=c.po_break_down_id and b.job_id=d.job_id and c.job_id=d.job_id $shipment_date and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by a.id, b.id";
		//echo $sql_po; die;
		$sql_po_data=sql_select($sql_po);
		foreach($sql_po_data as $sql_po_row)
		{
			//$po_arr[po_id][$sql_po_row[csf('id')]]=$sql_po_row[csf('id')];
			$po_arr[job_no][$sql_po_row[csf('id')]]=$sql_po_row[csf('id')];
			$jobCostingper[$sql_po_row[csf('job_no')]]=$sql_po_row[csf('costing_per')];
			
			$ft_data_arr[$sql_po_row[csf('job_no')]][job_no]=$sql_po_row[csf('job_no')];//P.CODE
			$ft_data_arr[$sql_po_row[csf('job_no')]][gmts_item_id]=$sql_po_row[csf('gmts_item_id')];//P.TYPE
			$ft_data_arr[$sql_po_row[csf('job_no')]][style_ref_no]=$sql_po_row[csf('style_ref_no')];//P.DESCRIP
			$fab_knit_req_kg=0;
			 
			$ft_data_arr[$sql_po_row[csf('job_no')]][buyer_name]=$sql_po_row[csf('buyer_name')];
			$ft_data_arr[$sql_po_row[csf('job_no')]][po_quantity]=$sql_po_row[csf('po_quantity')];
			$ft_data_arr[$sql_po_row[csf('job_no')]][ufob]=$sql_po_row[csf('unit_price')];
			$po_break[$sql_po_row[csf('bid')]]=$sql_po_row[csf('bid')];
			$ft_data_arr[$sql_po_row[csf('job_no')]][cutting]=1;//P^WC:10
			$ft_data_arr[$sql_po_row[csf('job_no')]][sew_input]=1;//P^WC:35
			$ft_data_arr[$sql_po_row[csf('job_no')]][sew_output]=$sql_po_row[csf('sew_smv')];//P^WC:140
			
			$ft_data_arr[$sql_po_row[csf('job_no')]][poly]=1;//P^WC:160
			$ft_data_arr[$sql_po_row[csf('job_no')]][shiped]=$sql_po_row[csf('sew_smv')];//P^WC:165
		}
		
		//print_r($jobCostingper); die;
		//$po_string= implode(",",$po_arr[po_id]);
		$job_string= implode(",",$po_arr[job_no]);
		
	   $job=array_chunk($po_arr[job_no],1000, true);
	   $job_cond_in=""; $job_condtrim_in="";
	   $ji=0;
	   foreach($job as $key=> $value)
	   {
		   if($ji==0)
		   {
				$job_cond_in="job_id in(".implode(",",$value).")"; 
				$job_cond_in_mst.=" a.job_id in(".implode(",",$value).")"; 
				$job_condtrim_in=" a.job_id in(".implode(",",$value).")"; 
		   }
		   else
		   {
				$job_cond_in.=" or job_id in(".implode(",",$value).")"; 
				$job_cond_in_mst.=" or a.job_id in(".implode(",",$value).")"; 
				$job_condtrim_in.=" or a.job_id in(".implode(",",$value).")";
		   }
		   $ji++;
	   }
	   //print_r($job_cond_in); die;
	   
	   /*$sql_fabric_prod=sql_select("select min(id) as id,job_no  from wo_pre_cost_fabric_cost_dtls where $job_cond_in and fabric_source=1 and avg_cons>0  and status_active=1 and is_deleted=0  group by job_no");
	   foreach($sql_fabric_prod as $row_fabric_prod)
	   {
		$ft_data_arr[fab_delivery][$row_fabric_prod[csf('job_no')]]=1;//P^WC:5   
	   }*/
	   //print_r($ft_data_arr); die;
	  $sql_print_embroid=sql_select("select min(id) as id,job_no,emb_name,avg(cons_dzn_gmts) as cons_dzn_gmts  from wo_pre_cost_embe_cost_dtls where $job_cond_in and emb_name in(1,2,3) and cons_dzn_gmts>0  and status_active=1 and is_deleted=0 group by job_no,emb_name");
	  foreach($sql_print_embroid as $row_print_embroid)
	  {
		  if($row_print_embroid[csf('emb_name')]==1)
		  {
			$ft_data_arr[$row_print_embroid[csf('job_no')]][dv_printing]=1; //P^WC:15
			$ft_data_arr[$row_print_embroid[csf('job_no')]][rv_printing]=1; //P^WC:20
		  }
		  if($row_print_embroid[csf('emb_name')]==2)
		  {
			$ft_data_arr[$row_print_embroid[csf('job_no')]][dv_embrodi]=1; //P^WC:25
			$ft_data_arr[$row_print_embroid[csf('job_no')]][rv_embrodi]=1; //P^WC:30
		  }
		  if($row_print_embroid[csf('emb_name')]==3)
		  {
			$ft_data_arr[$row_print_embroid[csf('job_no')]][dv_wash]=1; //P^WC:145
			$ft_data_arr[$row_print_embroid[csf('job_no')]][rv_wash]=1; //P^WC:150
		  }
	  }
	  
	  //=================================Item wise Array Srart=====================================
	   $arr_itemsmv=array();
	   $sql_itemsmv=sql_select("select job_no, gmts_item_id, set_item_ratio, smv_pcs_precost, smv_set_precost,smv_pcs,embelishment  from wo_po_details_mas_set_details where $job_cond_in");
	   foreach($sql_itemsmv as $row_itemsmv)
	   {
			$arr_itemsmv[$row_itemsmv[csf('job_no')]][$row_itemsmv[csf('gmts_item_id')]]['smv']=$row_itemsmv[csf('smv_pcs')]; 
			$arr_itemsmv[$row_itemsmv[csf('job_no')]][$row_itemsmv[csf('gmts_item_id')]]['emb']=$row_itemsmv[csf('embelishment')];  
	   }
		$cmArr=return_library_array("select job_no, cm_cost from wo_pre_cost_dtls where $job_cond_in and is_deleted=0 and status_active=1","job_no","cm_cost");
		//print_r($cmArr);
		$array_fabric_cons_item=array(); $array_fabric_prod_item=array(); $cmCostArr=array();
		$sql_fabric_cons_item=sql_select("select job_no, item_number_id, fabric_source, avg_finish_cons, avg_cons from wo_pre_cost_fabric_cost_dtls where $job_cond_in  and status_active=1 and is_deleted=0");
		// echo "select job_no, item_number_id, fabric_source, avg_finish_cons, avg_cons from wo_pre_cost_fabric_cost_dtls where $job_cond_in  and status_active=1 and is_deleted=0"; die;
		foreach($sql_fabric_cons_item as $row_fabric)
		{
			if($row_fabric[csf('fabric_source')]==1)
			{
				$array_fabric_prod_item[$row_fabric[csf('job_no')]][$row_fabric[csf('item_number_id')]]=1;    
			}
			$costingper=$jobCostingper[$row_fabric[csf('job_no')]];
			
			if($costingper==1) $costingQty=12;
			else if($costingper==2) $costingQty=1;
			else if($costingper==3) $costingQty=24;
			else if($costingper==4) $costingQty=36;
			else if($costingper==5) $costingQty=48;
			else $costingQty=0;
			
			$fab_knit_req_kg=$fab_knitgrey_req_kg=$cmCost=0;
			
			$fab_knit_req_kg=$row_fabric[csf('avg_finish_cons')]/$costingQty;
			$fab_knitgrey_req_kg=$row_fabric[csf('avg_cons')]/$costingQty;
			
			$array_fabric_cons_item[$row_fabric[csf('job_no')]][$row_fabric[csf('item_number_id')]]['fin']+=number_format($fab_knit_req_kg,4);
			$array_fabric_cons_item[$row_fabric[csf('job_no')]][$row_fabric[csf('item_number_id')]]['grey']+=number_format($fab_knitgrey_req_kg,4); 
			
			$cmCost=$cmArr[$row_fabric[csf('job_no')]]/$costingQty;
			//echo $cmArr[$row_fabric[csf('job_no')]].'/'.$costingper.'<br>';
			$cmCostArr[$row_fabric[csf('job_no')]]=$cmCost;
		}
		//print_r($cmCostArr);
		//echo "10**select job_no,item_number_id, sum(avg_finish_cons) as avg_cons, sum(avg_cons) as avg_grey_cons from wo_pre_cost_fabric_cost_dtls where $job_cond_in  and status_active=1 and is_deleted=0 group by job_no, item_number_id"; die;
		
		/*$sql_fabric_prod_item=sql_select("select min(id) as id,job_no,item_number_id  from wo_pre_cost_fabric_cost_dtls where $job_cond_in and fabric_source=1 and avg_cons>0  and status_active=1 and is_deleted=0  group by job_no,item_number_id");
		foreach($sql_fabric_prod_item as $row_fabric_prod_item)
		{
			$array_fabric_prod_item[$row_fabric_prod_item[csf('job_no')]][$row_fabric_prod_item[csf('item_number_id')]]=1;   
		}*/
		//echo "10**select a.job_no, 1 as type from wo_pre_cost_trim_cost_dtls a, lib_item_group b where $job_condtrim_in and a.trim_group=b.id and a.status_active=1 and a.is_deleted=0 and b.trim_type=3"; die;
		$ishtparr=array();
		$sqltrim_bom=sql_select("select a.job_no from wo_pre_cost_trim_cost_dtls a, lib_item_group b where $job_condtrim_in and a.trim_group=b.id and a.status_active=1 and a.is_deleted=0 and b.trim_type=3 group by a.job_no");
		
		foreach($sqltrim_bom as $rowtrim)
		{
			$ishtparr[$rowtrim[csf('job_no')]]=1;   
		}
		unset($sqltrim_bom);
		/*echo count($ishtparr);
		print_r($ishtparr);die;*/
		
		//echo "10**select job_no, cm_cost from wo_pre_cost_dtls where $job_cond_in and is_deleted=0 and status_active=1"; die;
	   //=================================Item wise Array End=====================================
		
		// Products file
		$txt="";
		$file_name="frfiles/PRODUCTS.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
	 	
		$txt .=str_pad("P.CODE",0," ")."\t".str_pad("P.TYPE",0," ")."\t".str_pad("P.DESCRIP",0," ")."\t".str_pad("P^CF:1",0," ")."\t".str_pad("P^WC:1",0," ")."\t".str_pad("P^CF:2",0," ")."\t".str_pad("P^WC:2",0," ")."\t".str_pad("P^CF:5",0," ")."\t".str_pad("P^WC:5",0," ")."\t".str_pad("P^WC:10",0," ")."\t".str_pad("P^WC:12",0," ")."\t".str_pad("P^WC:15",0," ")."\t".str_pad("P^WC:20",0," ")."\t".str_pad("P^WC:25",0," ")."\t".str_pad("P^WC:30",0," ")."\t".str_pad("P^WC:35",0," ")."\t".str_pad("P^WC:140",0," ")."\t".str_pad("P^WC:145",0," ")."\t".str_pad("P^WC:150",0," ")."\t".str_pad("P^WC:160",0," ")."\t".str_pad("P^WC:165",0," ")."\t".str_pad("P.SPRICE",0," ")."\t".str_pad("P.OTHCOST",0," ")."\t".str_pad("P.UDFabrication",0," ")."\t".str_pad("P.UDGSM",0," ")."\t".str_pad("P.UDYarn Count",0," ")."\t".str_pad("P.UDStyle",0," ")."\r\n";
		
		foreach($ft_data_arr as $rows)
		{
			$gitem=explode(",",$rows[gmts_item_id]);
			
			$dt="";
			$style_ref_arr[$rows[job_no]]=$rows[style_ref_no];
			$job_ref_arr[$rows[job_no]]=$rows[job_no];
			//$job_ref_arr[$rows[job_no]]=$rows[job_no];
			
			if(count($gitem)>1)
			{
				$item_array[$rows[job_no]]=1;
				
				foreach($gitem as $id)

				{
					//$new_job_no[$rows[job_no]]=$rows[job_no]
					//if($dt=="") $dt=$fr_product_type[$id]; else $dt .=",".$fr_product_type[$id];
					//$array_fabric_cons_item[$row_fabric_cons_item[csf('job_no')]][$row_fabric_cons_item[csf('item_number_id')]]['fin']
					$img_prod[$rows[job_no]][]=$rows[job_no]."::".$fr_product_type[$id];
					
					if(trim($rows[job_no])!="") $txt .=str_pad($rows[job_no]."::".$fr_product_type[$id],0," ")."\t".str_pad($fr_product_type[$id],0," ")."\t".str_pad($rows[style_ref_no],0," ")."\t".str_pad(number_format($array_fabric_cons_item[$rows[job_no]][$id]['grey'], 4, '.', ''),0," ")."\t".str_pad("1",0," ")."\t".str_pad(number_format($array_fabric_cons_item[$rows[job_no]][$id]['grey'], 4, '.', ''),0," ")."\t".str_pad("1",0," ")."\t".str_pad(number_format($array_fabric_cons_item[$rows[job_no]][$id]['fin'], 4, '.', ''),0," ")."\t".str_pad($array_fabric_prod_item[$rows[job_no]][$id] == '' ? 0 :$array_fabric_prod_item[$rows[job_no]][$id],0," ")."\t".str_pad("1",0," ")."\t".str_pad($ishtparr[$rows[job_no]] == '' ? 0 : $ishtparr[$rows[job_no]],0," ")."\t".str_pad($rows[dv_printing] == '' ? 0 : $rows[dv_printing],0," ")."\t".str_pad($rows[rv_printing] == '' ? 0 : $rows[rv_printing],0," ")."\t".str_pad($rows[dv_embrodi] == '' ? 0 :$rows[dv_embrodi],0," ")."\t".str_pad($rows[rv_embrodi] == '' ? 0 :$rows[rv_embrodi],0," ")."\t".str_pad("1",0," ")."\t".str_pad($arr_itemsmv[$rows[job_no]][$id]['smv'] == '' ? 0 :$arr_itemsmv[$rows[job_no]][$id]['smv'],0," ")."\t".str_pad($rows[dv_wash] == '' ? 0 :$rows[dv_wash],0," ")."\t".str_pad($rows[rv_wash] == '' ? 0 :$rows[rv_wash],0," ")."\t".str_pad($rows[poly] == '' ? 0 :$rows[poly],0," ")."\t".str_pad("1",0," ")."\t".str_pad($rows[ufob],0," ")."\t".str_pad($cmCostArr[$rows[job_no]],0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\r\n";
				}
			}
			else
			{
				$img_prod[$rows[job_no]][]=$rows[job_no];
				if(trim($rows[job_no])!="") $txt .=str_pad($rows[job_no],0," ")."\t".str_pad($fr_product_type[$rows[gmts_item_id]],0," ")."\t".str_pad($rows[style_ref_no],0," ")."\t".str_pad( number_format( $array_fabric_cons_item[$rows[job_no]][$rows[gmts_item_id]]['grey'], 4, '.', '' ),0," ")."\t".str_pad("1",0," ")."\t".str_pad( number_format( $array_fabric_cons_item[$rows[job_no]][$rows[gmts_item_id]]['grey'], 4, '.', '' ),0," ")."\t".str_pad("1",0," ")."\t".str_pad( number_format( $array_fabric_cons_item[$rows[job_no]][$rows[gmts_item_id]]['fin'], 4, '.', '' ),0," ")."\t".str_pad($array_fabric_prod_item[$rows[job_no]][$rows[gmts_item_id]] == '' ? 0 :$array_fabric_prod_item[$rows[job_no]][$rows[gmts_item_id]],0," ")."\t".str_pad("1",0," ")."\t".str_pad($ishtparr[$rows[job_no]] == '' ? 0 : $ishtparr[$rows[job_no]],0," ")."\t".str_pad($rows[dv_printing] == '' ? 0 : $rows[dv_printing],0," ")."\t".str_pad($rows[rv_printing] == '' ? 0 : $rows[rv_printing],0," ")."\t".str_pad($rows[dv_embrodi] == '' ? 0 :$rows[dv_embrodi],0," ")."\t".str_pad($rows[rv_embrodi] == '' ? 0 :$rows[rv_embrodi],0," ")."\t".str_pad("1",0," ")."\t".str_pad($arr_itemsmv[$rows[job_no]][$rows[gmts_item_id]]['smv'] == '' ? 0 :$arr_itemsmv[$rows[job_no]][$rows[gmts_item_id]]['smv'],0," ")."\t".str_pad($rows[dv_wash] == '' ? 0 :$rows[dv_wash],0," ")."\t".str_pad($rows[rv_wash] == '' ? 0 :$rows[rv_wash],0," ")."\t".str_pad($rows[poly] == '' ? 0 :$rows[poly],0," ")."\t".str_pad("1",0," ")."\t".str_pad($rows[ufob],0," ")."\t".str_pad($cmCostArr[$rows[job_no]],0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\r\n";
			}
		}
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";

		/*foreach($ft_data_arr as $rows)
		{
			$txt .=str_pad($rows[job_no],15," ")."\t".str_pad($rows[gmts_item_id],15," ")."\t".str_pad($rows[style_ref_no],25," ")."\t".str_pad($rows[fab_knit_req_kg],12," ")."\t".str_pad($rows[cutting] == '' ? 0 : $rows[cutting],12," ")."\t".str_pad($rows[dv_printing] == '' ? 0 : $rows[dv_printing],12," ")."\t".str_pad($rows[rv_printing] == '' ? 0 : $rows[rv_printing],12," ")."\t".str_pad($rows[dv_embrodi] == '' ? 0 :$rows[dv_embrodi],12," ")."\t".str_pad($rows[rv_embrodi] == '' ? 0 :$rows[rv_embrodi],12," ")."\t".str_pad($rows[sew_input] == '' ? 0 :$rows[sew_input],12," ")."\t".str_pad($rows[sew_output] == '' ? 0 :$rows[sew_output],12," ")."\t".str_pad($rows[dv_wash] == '' ? 0 :$rows[dv_wash],12," ")."\t".str_pad($rows[rv_wash] == '' ? 0 :$rows[rv_wash],12," ")."\t".str_pad($rows[poly] == '' ? 0 :$rows[poly],12," ")."\t".str_pad($rows[shiped] == '' ? 0 :$rows[shiped],12," ")."\t\t\t\t\t\r\n";
		}*/
		
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
	}
	else if ( $cbo_fr_integrtion==3)
	{
		$color_lib=return_library_array("select id,color_name from lib_color","id","color_name");
		$floor_name=return_library_array("select id,floor_name from  lib_prod_floor","id","floor_name");
		$line_name=return_library_array("select id,line_name from lib_sewing_line","id","line_name");
		$line_name_res=return_library_array("select id,line_number from prod_resource_mst","id","line_number");
		$floor_name_res=return_library_array("select id,floor_id from prod_resource_mst","id","floor_id");
		
		$sql=sql_select ( "select id,short_name from lib_buyer" );
 
		foreach($sql as $name)
		{
			//if(strlen($name)>6) $txt .=$name."\tEND\r\n"; else 
			$buyer_name_array[$name[csf("id")]]=$name[csf("short_name")];
			 
		}
		
		if(trim( $received_date)=="") $received_date="01-Aug-2018"; else $received_date=change_date_format($received_date, "yyyy-mm-dd", "-",1);
			 
		if( $db_type==0 )
		{
			//$shipment_date="and c.country_ship_date >= '2014-11-01'";
			$shipment_date="and b.po_received_date >= '$received_date'";
		}
		if($db_type==2)
		{
			$shipment_date="and b.po_received_date >= '$received_date'";
		}
 
 		$sql=sql_select ( "SELECT rtrim(xmlagg(xmlelement(e,c.id,',').extract('//text()') order by c.id).GetClobVal(),',') as id, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.gmts_item_id_prev, b.job_no_mst, b.po_number, b.pub_shipment_date, b.po_received_date, b.po_number_prev, b.is_confirmed, b.is_deleted, c.po_break_down_id, c.item_number_id, c.country_ship_date, c.color_number_id, c.cutup, c.color_number_id_prev, min(c.shiping_status) as shiping_status, sum(c.plan_cut_qnty) as plan_cut_new, sum(c.order_quantity) as order_quantity
		FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c WHERE 1=1 and a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and c.plan_cut_qnty!=0 $shipment_date
		group by a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.gmts_item_id_prev, b.job_no_mst, b.po_number, b.pub_shipment_date, b.po_received_date, b.po_number_prev, b.is_confirmed, b.is_deleted, c.po_break_down_id, c.item_number_id, c.country_ship_date, c.color_number_id, c.cutup, c.color_number_id_prev order by c.country_ship_date, c.po_break_down_id, c.cutup"); //b.pub_shipment_date_prev, c.country_ship_date_prev,
		
		$file_name="frfiles/ORDERS.TXT";
		$myfile = fopen( $file_name, "w" ) or die("Unable to open file!");
	  	$txt="";
		
		$txt .=str_pad("O.CODE",0," ")."\t".str_pad("O.OLDCODE",0," ")."\t".str_pad("O.PROD",0," ")."\t".str_pad("O.CUST",0," ")."\t".str_pad("O^DD:1",0," ")."\t".str_pad("O^DQ:1",0," ")."\t".str_pad("O.DESCRIP",0," ")."\t".str_pad("O.STATUS",0," ")."\t".str_pad("O.COMPLETE",0," ")."\t".str_pad("O.CONTRACT_QTY",0," ")."\t".str_pad("O.EVBASE",0," ")."\t".str_pad("O.UDJob No",0," ")."\t".str_pad("O.UDOPD",0," ")."\t".str_pad("O.UDOrder Code",0," ")."\t".str_pad("O.UDColour",0," ")."\t".str_pad("O.UDOrder",0," ")."\r\n";
		$orders_arr=array();
		foreach($sql as $name)
		{
			$old_str_po=''; $old_item_code=''; $changed=0; $old_po="";
			$job=$cdate="";
			$poId=0; $item_id=0; $color_id=0; $cut_up=0;
			
			$job=$name[csf('job_no_mst')];
			$cdate=$name[csf('country_ship_date')];
			$poId=$name[csf('po_break_down_id')]; 
			$item_id=$name[csf('item_number_id')]; 
			$color_id=$name[csf('color_number_id')]; 
			$cut_up=$name[csf('cutup')];
			
			$name[csf('id')]= $name[csf('id')]->load();
			$buyer_name=$buyer_name_array[$name[csf('buyer_name')]];
			
			if( $name[csf('shiping_status')]==3) $ssts="1";
			else $ssts="0"; //Sewing Out
			/*$gitem=explode(",",$name[csf('gmts_item_id')]);
			if( count($gitem)>1) $str_item="::".$fr_product_type[$name[csf('item_number_id')]]; else $str_item=""; */
			
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['cid']=$name[csf('id')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['buyer']=$buyer_name;
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['style_ref']=$name[csf('style_ref_no')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['uom']=$name[csf('order_uom')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['gmts_item_id']=$name[csf('gmts_item_id')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['po_number']=$name[csf('po_number')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['po_received_date']=$name[csf('po_received_date')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['is_confirmed']=$name[csf('is_confirmed')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['is_deleted']=$name[csf('is_deleted')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['shiping_status']=$ssts;
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['plan_cut_new']+=$name[csf('plan_cut_new')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['order_quantity']+=$name[csf('order_quantity')];
			
			if( trim($name[csf('po_number_prev')])!='' && trim($name[csf('po_number_prev')])!=$name[csf('po_number')])
			{
				$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['po_number_prev']=$name[csf('po_number_prev')];
			}
			if( trim($name[csf('gmts_item_id_prev')])!='' && trim($name[csf('gmts_item_id_prev')])!=$name[csf('gmts_item_id')])
			{
				$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['gmts_item_id_prev']=$name[csf('gmts_item_id_prev')];
			}
			
			if( trim($name[csf('color_number_id_prev')])!='' && trim($name[csf('color_number_id_prev')])!=$name[csf('color_number_id')])
			{
				$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['color_number_id_prev']=$name[csf('color_number_id_prev')];
			}
			
			
			/*if( $name[csf('cutup')]==0 )
			{
				 $str_po=$name[csf('po_number')]."-".str_replace("FFL-","",$name[csf('job_no_mst')])."::".$color_lib[$name[csf('color_number_id')]]."".$str_item;
				 $str_po_cut="";
			}
			else 
			{
				$cutoff[$name[csf('po_break_down_id')]."::".$color_lib[$name[csf('color_number_id')]]."".$str_item]+=1;
				
				$str_po=$name[csf('po_number')]."-".str_replace("FFL-","",$name[csf('job_no_mst')])."::".$color_lib[$name[csf('color_number_id')]]."::".$cutoff[$name[csf('po_break_down_id')]."::".$color_lib[$name[csf('color_number_id')]]."".$str_item]."".$str_item;  
				$str_po_cut=$cutoff[$name[csf('po_break_down_id')]."::".$color_lib[$name[csf('color_number_id')]]."".$str_item]."::"; 
			}
			
			$pgitem=explode(",",trim($name[csf('gmts_item_id_prev')]));
			$inc=0;
			if(count($pgitem)>1)
			{
				foreach( $pgitem as $iid )
				{
					if( $iid!="")
					{
						$changed=1;
						$old_item_code="::".$fr_product_type[$iid];
					}
					$inc++;
				}
			}
			else
				$old_item_code="::".$fr_product_type[$name[csf('item_number_id')]];
			
			if( $name[csf('po_number_prev')]!='' && trim($name[csf('po_number_prev')])!=$name[csf('po_number')])
			{
				$old_po=trim($name[csf('po_number_prev')]);
				$changed=1;
			}
			else
				$old_po= $name[csf('po_number')];
				
			if( $name[csf('color_number_id_prev')]!='' && $name[csf('color_number_id_prev')]!=$name[csf('color_number_id')])
			{
				$old_color= trim($name[csf('color_number_id_prev')]);
				$changed=1;
			}
			else
				$old_color= $name[csf('color_number_id')];
			
			if( $name[csf('pub_shipment_date_prev')]!='' && date("d-m-Y",strtotime($name[csf('pub_shipment_date_prev')]))!=date("d-m-Y",strtotime($name[csf('pub_shipment_date')])))
			{
				$old_pub_ship= date("ymd",strtotime($name[csf('pub_shipment_date_prev')]));
				$changed=1;
			}
			else
				$old_pub_ship= date("ymd",strtotime($name[csf('pub_shipment_date')]));
			
			if($changed==1)
			{
				if( $name[csf('cutup')]==0 )
				{
					$old_str_po=$old_po."-".str_replace("FFL-","",$name[csf('job_no_mst')])."::".$color_lib[$old_color]."".$old_item_code;
					//$name[csf("job_no_mst")]."::".$old_po."::".$color_lib[$old_color]."::".$old_pub_ship."".$old_item_code; 
				}
				else
				{
					$old_str_po=$old_po."-".str_replace("FFL-","",$name[csf('job_no_mst')])."::".$color_lib[$old_color]."::".$cutoff[$name[csf('po_break_down_id')]."::".$color_lib[$old_color]."".$str_item]."".$old_item_code;  
				}
			}
			
			if($dtls_id=="") $dtls_id=$name[csf('id')]; else $dtls_id .=",".$name[csf('id')];
			
			//$ssts=0;
			
			
			
			
			$txt .=str_pad($str_po,0," ")."\t".str_pad($old_str_po,0," ")."\t".str_pad($name[csf('job_no_mst')]."".$str_item,0," ")."\t".str_pad( $buyer_naem,0," ")."\t".str_pad(date("d/m/Y",strtotime($name[csf('country_ship_date')])),0," ")."\t".str_pad($name[csf('plan_cut_new')],0," ")."\t".str_pad($name[csf('style_ref_no')],0," ")."\t".str_pad($str,0," ")."\t".str_pad($ssts,0," ")."\t".str_pad($name[csf('order_quantity')],0," ")."\t".str_pad($dtd,0," ")."\t".str_pad($dtd,0," ")."\t".str_pad($dtd,0," ")."\t".str_pad($name[csf('po_number')],0," ")."\t".str_pad($color_lib[$name[csf('color_number_id')]],0," ")."\t".str_pad($name[csf('po_number')]."::".$color_lib[$name[csf('color_number_id')]]."::". $str_po_cut .$fr_product_type[$name[csf('item_number_id')]],0," ")."\r\n";
       		$buyer_naem='';
            $id_arr[] = $id;*/
        }
		
		foreach($orders_arr as $jobno=>$job_data)
		{
			foreach($job_data as $poid=>$po_data)
			{
				foreach($po_data as $item_id=>$item_data)
				{
					foreach($item_data as $cdate=>$cdate_data)
					{
						foreach($cdate_data as $colorid=>$colorid_data)
						{
							foreach($colorid_data as $cut_up=>$exdata)
							{
								$old_str_po=''; $old_item_code=''; $changed=0; $old_po="";
								$gitem=explode(",",$exdata['gmts_item_id']);
								if( count($gitem)>1) $str_item="::".$fr_product_type[$item_id]; else $str_item=""; 
								
								if( $cut_up==0 && count($item_data)==1)
								{
									 $str_po=$exdata['po_number']."-".str_replace("FFL-","",$jobno)."::".$color_lib[$colorid]."".$str_item;
									 $str_po_cut="";
								}
								else 
								{
									$cutoff[$poid."::".$color_lib[$colorid]."".$str_item]+=1;
									$str_po=$exdata['po_number']."-".str_replace("FFL-","",$jobno)."::".$color_lib[$colorid]."::".$cutoff[$poid."::".$color_lib[$colorid]."".$str_item]."".$str_item;  
									$str_po_cut=$cutoff[$poid."::".$color_lib[$colorid]."".$str_item]."::"; 
								}
								
								$pgitem=explode(",",trim($exdata['gmts_item_id_prev']));
								$inc=0;
								if(count($pgitem)>1)
								{
									foreach( $pgitem as $iid )
									{
										if( $iid!="")
										{
											$changed=1;
											$old_item_code="::".$fr_product_type[$iid];
										}
										$inc++;
									}
								}
								else
									$old_item_code=$str_item;//$old_item_code="::".$fr_product_type[$item_id];
								
								if( trim($exdata['po_number_prev'])!='' && trim($exdata['po_number_prev'])!=$exdata['po_number'])
								{
									$old_po=trim($exdata['po_number_prev']);
									$changed=1;
								}
								else
									$old_po= $exdata['po_number'];
									
								if( $exdata['color_number_id_prev']!='' && $exdata['color_number_id_prev']!=$colorid)
								{
									if($exdata['color_number_id_prev']!=0)
									{
										$old_color= trim($exdata['color_number_id_prev']);
										$changed=1;
									}
								}
								else
									$old_color= $colorid;
									
									
								if($changed==1)
								{
									if( $cut_up==0 && count($item_data)==1)
									{
										$old_str_po=$old_po."-".str_replace("FFL-","",$jobno)."::".$color_lib[$old_color]."".$old_item_code;
										//$name[csf("job_no_mst")]."::".$old_po."::".$color_lib[$old_color]."::".$old_pub_ship."".$old_item_code; 
									}
									else
									{
										$old_str_po=$old_po."-".str_replace("FFL-","",$jobno)."::".$color_lib[$old_color]."::".$cutoff[$poid."::".$color_lib[$colorid]."".$str_item]."".$old_item_code;  
									}
								}
								
								if($dtls_id=="") $dtls_id=$exdata['id']; else $dtls_id .=",".$exdata['id'];
								
								if( $exdata['is_confirmed']==1) $str="F"; else $str="P";
								if( $exdata['is_deleted']==1 ) { $str="X";  } 
								
								
								$txt .=str_pad($str_po,0," ")."\t".str_pad($old_str_po,0," ")."\t".str_pad($jobno."".$str_item,0," ")."\t".str_pad( $exdata['buyer'],0," ")."\t".str_pad(date("d/m/Y",strtotime($cdate)),0," ")."\t".str_pad($exdata['plan_cut_new'],0," ")."\t".str_pad($exdata['style_ref'],0," ")."\t".str_pad($str,0," ")."\t".str_pad($exdata['shiping_status'],0," ")."\t".str_pad($exdata['order_quantity'],0," ")."\t".str_pad(date("d/m/Y",strtotime($exdata['po_received_date'])),0," ")."\t".str_pad($dtd,0," ")."\t".str_pad($dtd,0," ")."\t".str_pad($exdata['po_number'],0," ")."\t".str_pad($color_lib[$colorid],0," ")."\t".str_pad($exdata['po_number']."::".$color_lib[$colorid]."::". $str_po_cut .$fr_product_type[$item_id],0," ")."\r\n";
								//$buyer_naem='';
								//$id_arr[] = $id;
							}
						}
					}
				}
			}
		}
		//echo $txt;
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
	}
	else if ( $cbo_fr_integrtion==4 )
	{
		if(trim( $received_date)=="") $received_date="01-Aug-2018"; else $received_date=change_date_format($received_date, "yyyy-mm-dd", "-",1);
		$color_lib=return_library_array("select id, color_name from lib_color","id","color_name");
		if( $db_type==0 )
		{
			//$shipment_date="and c.country_ship_date >= '2014-11-01'";
			$shipment_date="and b.po_received_date >= '$received_date'";
		}
		if($db_type==2)
		{
			$shipment_date="and b.po_received_date >= '$received_date'";
		}
		//echo "select a.po_number_id, a.job_no, a.actual_start_date, a.actual_finish_date, a.task_number, b.po_number, c.style_ref_no, c.gmts_item_id, d.item_number_id, d.cutup, d.color_number_id from tna_process_mst a, wo_po_break_down b, wo_po_details_master c, wo_po_color_size_breakdown d where a.job_no=b.job_no_mst and b.job_no_mst=c.job_no and a.po_number_id=b.id and b.id=d.po_break_down_id $shipment_date order by a.po_number_id, a.task_number"; die;
		
		$sql=sql_select ( "select a.po_number_id, a.job_no, a.actual_start_date, a.actual_finish_date, a.task_number, b.po_number, c.style_ref_no, c.gmts_item_id, d.item_number_id, d.cutup, d.color_number_id from tna_process_mst a, wo_po_break_down b, wo_po_details_master c, wo_po_color_size_breakdown d where a.job_no=b.job_no_mst and b.job_no_mst=c.job_no and a.po_number_id=b.id and b.id=d.po_break_down_id $shipment_date order by a.po_number_id, a.task_number" ); 
		
		
		$file_name="frfiles/EVENTS.TXT";
		$myfile = fopen( $file_name, "w" ) or die("Unable to open file!");
	 	
		$txt .=str_pad("E.CODE",0," ")."\t".str_pad("E.TYPE",0," ")."\t".str_pad("E.EVENT",0," ")."\t".str_pad("E.AD",0," ")."\t".str_pad("E.DONE",0," ")."\t".str_pad("E.SKIP",0," ")."\t".str_pad("E.NOTE",0," ")."\r\n";
		foreach($sql as $name)
		{
			$gitem=explode(",",$name[csf('gmts_item_id')]);
			
			if( count($gitem)>1) $str_item="::".$fr_product_type[$name[csf('item_number_id')]]; else $str_item=""; 
			
			if( $name[csf('cutup')]==0 )
			{
				 $str_po=$name[csf('po_number')]."-".str_replace("FFL-","",$name[csf('job_no')])."::".$color_lib[$name[csf('color_number_id')]]."".$str_item;
				 $str_po_cut="";
			}
			else 
			{
				$cutoff[$name[csf('po_number_id')]."::".$color_lib[$name[csf('color_number_id')]]."".$str_item]+=1;
				
				$str_po=$name[csf('po_number')]."-".str_replace("FFL-","",$name[csf('job_no')])."::".$color_lib[$name[csf('color_number_id')]]."::".$cutoff[$name[csf('po_break_down_id')]."::".$color_lib[$name[csf('color_number_id')]]."".$str_item]."".$str_item;  
				$str_po_cut=$cutoff[$name[csf('po_number_id')]."::".$color_lib[$name[csf('color_number_id')]]."".$str_item]."::"; 
			}
			
			$str_po_list[$name[csf('po_number_id')]][$str_po]=$str_po;	
			
			if( $name[csf('actual_finish_date')]=="0000-00-00")  $name[csf('actual_finish_date')]=""; else   $name[csf('actual_finish_date')]=date("d/m/Y",strtotime($name[csf('actual_finish_date')]));
			if( $name[csf('actual_start_date')]=="0000-00-00")  $name[csf('actual_start_date')]=""; else   $name[csf('actual_start_date')]=date("d/m/Y",strtotime($name[csf('actual_start_date')]));
			
			$tna_po_name[$name[csf('po_number_id')]]=$name[csf('po_number_id')];
			$tna_po_task[$name[csf('po_number_id')]][$name[csf('task_number')]]['actual_finish_date']=$name[csf('actual_finish_date')];
			$tna_po_task[$name[csf('po_number_id')]][$name[csf('task_number')]]['job_no_mst']=$name[csf('job_no')];
			$tna_po_task[$name[csf('po_number_id')]][$name[csf('task_number')]]['style_ref_no']=$name[csf('style_ref_no')];
			//$tna_po_task[$name[csf('po_number_id')]][$name[csf('task_number')]]['actual_finish_date']=$name[csf('actual_finish_date')];
			//$tna_po_task[$name[csf('po_number_id')]][$name[csf('task_number')]]['actual_finish_date']=$name[csf('actual_finish_date')];
		}
		unset( $sql );
		
		foreach($tna_po_name as $poid )
		{
			foreach( $str_po_list[$poid] as $order=>$no ) //$str_po_list[$name[csf('po_break_down_id')]][$str_po]=$str_po;	
			{
				foreach($tna_po_task[$poid] as $task=>$names)
				{
					// print_r($names['job_no_mst']);
					if( $mapped_tna_task[$task]=='' ) $mapped_tna_task[$task]=0;
					if( $mapped_tna_task[$task]!=0 )
						$txt .=str_pad($order,0," ")."\tO\t".str_pad($mapped_tna_task[$task],0," ")."\t\t".str_pad($names['actual_finish_date'],0," ")."\t0\t".str_pad($names['style_ref_no'],0," ")."\r\n";
				}
			} //$tna_po_id[$name[csf('po_number_id')]]
		}
		fwrite( $myfile, $txt );
		fclose($myfile); 
		$txt="";
	}
	else if ( $cbo_fr_integrtion==5 )
	{
		if(trim( $received_date)=="") $received_date="01-Aug-2018"; else $received_date=change_date_format($received_date, "yyyy-mm-dd", "-",1);
		$shipment_date="and b.po_received_date >= '$received_date'";
		
		$color_lib=return_library_array("select id,color_name from lib_color","id","color_name");
		$floor_name=return_library_array("select id,floor_name from  lib_prod_floor","id","floor_name");
		$line_name=return_library_array("select id,line_name from lib_sewing_line","id","line_name");
		$line_name_res=return_library_array("select id,line_number from prod_resource_mst","id","line_number");
		$floor_name_res=return_library_array("select id,floor_id from prod_resource_mst","id","floor_id");
		//echo '0**';
		$sql_po="SELECT rtrim(xmlagg(xmlelement(e,c.id,',').extract('//text()') order by c.id).GetClobVal(),',') as id, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.gmts_item_id_prev, b.job_no_mst, b.po_number, b.pub_shipment_date, b.po_received_date, b.po_number_prev, b.is_confirmed, b.is_deleted, c.po_break_down_id, c.item_number_id, c.country_ship_date, c.color_number_id, c.cutup, c.color_number_id_prev, min(c.shiping_status) as shiping_status, sum(c.plan_cut_qnty) as plan_cut_new, sum(c.order_quantity) as order_quantity
		FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c WHERE 1=1 and a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and c.plan_cut_qnty!=0 $shipment_date
		group by a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.gmts_item_id_prev, b.job_no_mst, b.po_number, b.pub_shipment_date, b.po_received_date, b.po_number_prev, b.is_confirmed, b.is_deleted, c.po_break_down_id, c.item_number_id, c.country_ship_date, c.color_number_id, c.cutup, c.color_number_id_prev order by c.country_ship_date, c.po_break_down_id, c.cutup";
		

		/*$sql_po="select a.id, a.job_no, a.style_ref_no, a.buyer_name, a.order_uom, a.gmts_item_id, b.id as bid, b.po_number, b.po_quantity, b.po_received_date, d.costing_per, d.sew_smv, e.fab_knit_req_kg, 
		rtrim(xmlagg(xmlelement(e,c.id,',').extract('//text()') order by c.id).GetClobVal(),',') as cid, c.item_number_id, min(c.shiping_status) as shiping_status, c.country_ship_date, c.color_number_id, c.cutup, sum(c.order_quantity) as cqty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_mst d, wo_pre_cost_sum_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.shiping_status in (1,2) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $shipment_date and a.job_no='FFL-19-02133'
		group by  a.id, b.id, a.job_no, a.style_ref_no, a.order_uom, a.gmts_item_id, b.po_number, d.costing_per, d.sew_smv, e.fab_knit_req_kg, a.buyer_name, b.po_quantity, c.item_number_id, c.country_ship_date, c.color_number_id, c.cutup order by a.id, b.id, c.country_ship_date";*/
		//echo "10**".$sql_po;
		$new_indx_powise=array();
		$sql_po_data=sql_select($sql_po); $tot_rows=0; $poIds=''; $i=0; $k=1; $po_req_qty_array=array(); $req_qty_array=array();
		foreach($sql_po_data as $sql_po_row)
		{
			$tot_rows++;
			$poIds.=$sql_po_row[csf("po_break_down_id")].",";
			$po_break[$sql_po_row[csf('po_break_down_id')]]=$sql_po_row[csf('po_break_down_id')];
			
			$old_str_po=''; $old_item_code=''; $changed=0; $old_po="";
			$job=$cdate="";
			$poId=0; $item_id=0; $color_id=0; $cut_up=0;
			
			$job=$sql_po_row[csf('job_no_mst')];
			$cdate=$sql_po_row[csf('country_ship_date')];
			$poId=$sql_po_row[csf('po_break_down_id')]; 
			$item_id=$sql_po_row[csf('item_number_id')]; 
			$color_id=$sql_po_row[csf('color_number_id')]; 
			$cut_up=$sql_po_row[csf('cutup')];
			
			$sql_po_row[csf('id')]= $sql_po_row[csf('id')]->load();
			$buyer_name=$buyer_name_array[$sql_po_row[csf('buyer_name')]];
			//echo $sql_po_row[csf('id')].'=';
			
			if( $sql_po_row[csf('shiping_status')]==3) $ssts="1";
			else $ssts="0"; //Sewing Out
			/*$gitem=explode(",",$name[csf('gmts_item_id')]);
			if( count($gitem)>1) $str_item="::".$fr_product_type[$name[csf('item_number_id')]]; else $str_item=""; */
			
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['cid'].=$sql_po_row[csf('id')].',';
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['buyer']=$buyer_name;
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['style_ref']=$sql_po_row[csf('style_ref_no')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['uom']=$sql_po_row[csf('order_uom')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['gmts_item_id']=$sql_po_row[csf('gmts_item_id')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['po_number']=$sql_po_row[csf('po_number')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['po_received_date']=$sql_po_row[csf('po_received_date')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['is_confirmed']=$sql_po_row[csf('is_confirmed')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['is_deleted']=$sql_po_row[csf('is_deleted')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['shiping_status']=$ssts;
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['plan_cut_new']+=$sql_po_row[csf('plan_cut_new')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['order_quantity']+=$sql_po_row[csf('order_quantity')];
		}
		//print_r($orders_arr); die;
		
		$newid_ar=array();
		foreach($orders_arr as $jobno=>$job_data)
		{
			foreach($job_data as $poid=>$po_data)
			{
				foreach($po_data as $item_id=>$item_data)
				{
					foreach($item_data as $cdate=>$cdate_data)
					{
						foreach($cdate_data as $colorid=>$colorid_data)
						{
							foreach($colorid_data as $cut_up=>$exdata)
							{
								$old_str_po=''; $old_item_code=''; $changed=0; $old_po="";
								$gitem=explode(",",$exdata['gmts_item_id']);
								if( count($gitem)>1) $str_item="::".$fr_product_type[$item_id]; else $str_item=""; 
								
								if( $cut_up==0 && count($item_data)==1)
								{
									 $str_po=$exdata['po_number']."-".str_replace("FFL-","",$jobno)."::".$color_lib[$colorid]."".$str_item;
									 $str_po_cut="";
								}
								else 
								{
									$cutoff[$poid."::".$color_lib[$colorid]."".$str_item]+=1;
									$str_po=$exdata['po_number']."-".str_replace("FFL-","",$jobno)."::".$color_lib[$colorid]."::".$cutoff[$poid."::".$color_lib[$colorid]."".$str_item]."".$str_item;  
									$str_po_cut=$cutoff[$poid."::".$color_lib[$colorid]."".$str_item]."::"; 
								}
								$nid="";
								$nid=array_filter(array_unique(explode(",",$exdata['cid'])));
								foreach($nid as $vid)
								{
									$newid_ar[$vid]=$str_po;
								}
							}
						}
					}
				}
			}
		}
		//print_r($newid_ar); die;
		
		$poIds=chop($poIds,','); $poIds_cond=""; $bookingpoIds_cond=""; $prodpoIds_cond="";
		if($db_type==2 && $tot_rows>1000)
		{
			$poIds_cond=" and (";
			$bookingpoIds_cond=" and (";
			$prodpoIds_cond=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poIds_cond.=" po_break_down_id in($ids) or ";
				$bookingpoIds_cond.=" b.po_break_down_id in($ids) or ";
				$prodpoIds_cond.=" c.po_breakdown_id in($ids) or ";
			}
			$poIds_cond=chop($poIds_cond,'or ');
			$poIds_cond.=")";
			
			$bookingpoIds_cond=chop($bookingpoIds_cond,'or ');
			$bookingpoIds_cond.=")";
			
			$prodpoIds_cond=chop($prodpoIds_cond,'or ');
			$prodpoIds_cond.=")";
		}
		else
		{
			$poIds_cond=" and po_break_down_id in ($poIds)";
			$bookingpoIds_cond=" and b.po_break_down_id in ($poIds)";
			$prodpoIds_cond=" and c.po_breakdown_id in ($poIds)";
		}
		$txt="";
		
		// Production file
		$file_name="frfiles/UPDNORM.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
	  	
		$operation_arra=array(1=>"10",2=>"15",3=>"20",4=>"35",5=>"140",6=>"Finish Input",7=>"Iron Output",8=>"160",9=>"Cutting Delivery",25=>"25",30=>"30",145=>"145",150=>"150",165=>"165",170=>"170",180=>"180"); //145=issue to wash,150=receive from wash,165=Shipped,170= grey production,180=finish production
	  	
		$sql_booking="select b.po_break_down_id, sum(b.fin_fab_qnty) as req_qnty, sum(b.grey_fab_qnty) as grey_req_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $bookingpoIds_cond group by b.po_break_down_id";
		$booking_req_arr=array();
		$sql_booking_res=sql_select($sql_booking);
		foreach($sql_booking_res as $row_req)
		{
			$booking_req_arr[$row_req[csf("po_break_down_id")]]['grey']=$row_req[csf("grey_req_qnty")];
			$booking_req_arr[$row_req[csf("po_break_down_id")]]['fin']=$row_req[csf("req_qnty")];
		}
		unset($sql_booking_res);
		
		$fab_sql="select c.po_breakdown_id, c.entry_form, sum(c.quantity) as quantity, max(a.receive_date) as receive_date from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in (2,7) and c.entry_form in (2,7) $prodpoIds_cond group by c.po_breakdown_id, c.entry_form";
		
		$fabric_production_arr=array();
		$fab_sql_res=sql_select($fab_sql);
		foreach($fab_sql_res as $row_p)
		{
			if($row_p[csf("entry_form")]==2)
			{
				$fabric_production_arr[$row_p[csf("po_breakdown_id")]]['grey']+=$row_p[csf("quantity")];
				$fabric_production_arr[$row_p[csf("po_breakdown_id")]]['greyDate']=$row_p[csf("receive_date")];
			}
			else if($row_p[csf("entry_form")]==7)
			{
				$fabric_production_arr[$row_p[csf("po_breakdown_id")]]['fin']+=$row_p[csf("quantity")];
				$fabric_production_arr[$row_p[csf("po_breakdown_id")]]['finDate']=$row_p[csf("receive_date")];
			}
		}
		unset($fab_sql_res);
		//print_r( $fabric_production_arr); 
		//die;
		foreach( $fabric_production_arr as $po=>$datas)
		{
			$reqfin_qty=$booking_req_arr[$po]['fin'];
			$grey_qty=$datas['grey'];
			$greyDate=$datas['greyDate'];
			$fin_qty=$datas['fin'];
			$finDate=$datas['finDate'];
			
			$poQty=array_sum($new_indx_powise_qnty[$po]);
			$j=1;
			foreach( $new_indx_powise_qnty[$po] as $inc=>$cQty)
			{
				$colSize_qty=0;
				$colSize_qty=$cQty/$poQty;
				
				//$greyQty=$grey_qty*$colSize_qty;
				$finQtyReq=$reqfin_qty*$colSize_qty;
				$finprodQty=$fin_qty*$colSize_qty;
				if ( $finQtyReq >= $finprodQty ) 
					$finQty=$finprodQty; 
				else
				{
					$finQty=$finprodQty; 
					$fin_qty =$fin_qty -$finprodQty;
				}
				
				$postr=$new_indx_powise_id[$po][$inc];
				
				/*if($greyQty!=0)
				{
					$production_qty[$postr]['170'][0][0][qnty]+=$greyQty;
					$production_qty[$postr]['170'][0][0][pdate]+=$greyDate;
				}*/
				if($finQty!=0)
				{
					$production_qty[$postr]['180'][0][0][qnty]+=$finQty;
					$production_qty[$postr]['180'][0][0][pdate]=$finDate;
				}
			}
		}
		
		$exfac_sql=sql_select( "SELECT a.po_break_down_id, sum(b.production_qnty) AS production_quantity, b.color_size_break_down_id, 165 as production_type, max(a.ex_factory_date) as production_date from pro_ex_factory_mst a, pro_ex_factory_dtls b where a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 $poIds_cond group by a.po_break_down_id,  b.color_size_break_down_id order by a.po_break_down_id, production_date asc "); 
		foreach($exfac_sql as $row_ex)
		{
			$production_qty[$newid_ar[$row_ex[csf("color_size_break_down_id")]]][$row_ex[csf("production_type")]][0][0][qnty]+=$row_ex[csf("production_quantity")];
			$production_qty[$newid_ar[$row_ex[csf("color_size_break_down_id")]]][$row_ex[csf("production_type")]][0][0][pdate]=$row_ex[csf("production_date")]; 
		}
		unset($exfac_sql);
		//print_r($new_indx_powise_qnty); die;
		
		//$txt .=str_pad("U.ORDER",0," ")."\t".str_pad("U.DATE",0," ")."\t".str_pad("U.OPERATION",0," ")."\t".str_pad("U.GROUP_EXTERNAL_ID",0," ")."\t".str_pad("U.LINE_EXTERNAL_ID",0," ")."\t".str_pad("U.QTY",0," ")."\t".str_pad("U.OPN_COMPLETE",0," ")."\r\n";
		$txt .=str_pad("U.ORDER",0," ")."\t".str_pad("U.DATE",0," ")."\t".str_pad("U.OPERATION",0," ")."\t".str_pad("U.LINE_EXTERNAL_ID",0," ")."\t".str_pad("U.QTY",0," ")."\r\n";
		$sewing_qnty=sql_select( "SELECT a.po_break_down_id, a.embel_name, sum(b.production_qnty) AS production_quantity, a.floor_id, a.sewing_line, b.color_size_break_down_id, a.production_type, max(a.production_date) as production_date from pro_garments_production_mst a, pro_garments_production_dtls b where a.production_type in (1,2,3,4,5,8) and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.production_qnty is not null $poIds_cond group by a.po_break_down_id, a.embel_name, a.floor_id, a.sewing_line, b.color_size_break_down_id, a.production_type order by a.po_break_down_id, production_date asc "); // and a.po_break_down_id=22333
		
		//production data,
		
		foreach($sewing_qnty as $row_sew)
		{
			$ssts=1;
			
			if($operation_arra[$row_sew[csf("production_type")]]==35 )
			{
				$row_sew[csf("floor_id")]=0;
				$row_sew[csf("sewing_line")]=0;
			}
			if($row_sew[csf("production_type")]==1 || $row_sew[csf("production_type")]==2 || $row_sew[csf("production_type")]==3 || $row_sew[csf("production_type")]==4) $row_sew[csf("floor_id")]=0;//$row_sew[csf("production_type")]==1 || 
			
			if($row_sew[csf("production_type")]==2 && $row_sew[csf("embel_name")]==3)
			{
				$row_sew[csf("production_type")]=145;
				$row_sew[csf("floor_id")]=0;
				$row_sew[csf("sewing_line")]=0;
			}
			if($row_sew[csf("production_type")]==2 && $row_sew[csf("embel_name")]==2)//Embro
			{
				$row_sew[csf("production_type")]=25;
				$row_sew[csf("floor_id")]=0;
				$row_sew[csf("sewing_line")]=0;
			}
			if($row_sew[csf("production_type")]==3 && $row_sew[csf("embel_name")]==3)
			{
				$row_sew[csf("production_type")]=150;
				$row_sew[csf("floor_id")]=0;
				$row_sew[csf("sewing_line")]=0;
			}
			if($row_sew[csf("production_type")]==3 && $row_sew[csf("embel_name")]==2)//Embro
			{
				$row_sew[csf("production_type")]=30;
				$row_sew[csf("floor_id")]=0;
				$row_sew[csf("sewing_line")]=0;
			}
			//if($newid_ar[$row_sew[csf("color_size_break_down_id")]]=="") echo $row_sew[csf("color_size_break_down_id")].'<br>';
			$production_qty[$newid_ar[$row_sew[csf("color_size_break_down_id")]]][$row_sew[csf("production_type")]][$row_sew[csf("floor_id")]][$row_sew[csf("sewing_line")]][qnty]+=$row_sew[csf("production_quantity")];
			
			$production_qty[$newid_ar[$row_sew[csf("color_size_break_down_id")]]][$row_sew[csf("production_type")]][$row_sew[csf("floor_id")]][$row_sew[csf("sewing_line")]][pdate]=$row_sew[csf("production_date")]; 
		}
		
		//print_r($production_qty); die;
		
		foreach($production_qty as $oid=>$prodctn)
		{
			ksort($prodctn);
			foreach($prodctn as $prod_typ=>$prodflr)
			{
				ksort($prodflr);
				foreach($prodflr as $flor=>$lndata)
				{
					ksort($lndata);
					foreach($lndata as $line=>$sdata)
					{
						//ksort($sdata);
						//foreach($sdata as $sdate=>$data)
						//{
							$sdate=date("d/m/Y",strtotime($sdata[pdate]));
							if($line==0) $line=""; else $line=(int)$line_name_res[$line]; 
							$flor=$floor_name_res[$line];
							if($flor==0) $flor="";
							$ssts=0;
							if(trim($oid)!='')
								$txt .=str_pad($oid,0," ")."\t".str_pad($sdate,0," ")."\t".str_pad($operation_arra[$prod_typ],0," ")."\t".str_pad($line_array[$line],0," ")."\t".str_pad($sdata[qnty],0," ")."\r\n";
						//}
					}
				}
			}	
		}
		
		fwrite($myfile, $txt);
		fclose($myfile); 
	}
	else if( $cbo_fr_integrtion==7 )
	{
		$sql=sql_select ( "select id,master_tble_id,image_location from common_photo_library where is_deleted=0 and file_type=1 and form_name='knit_order_entry' " ); // and master_tble_id in ('".implode("','",$job_ref_arr)."')
		$file_name="frfiles/IMGATTACH.txt";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
		$txt .="IMG.CODE\tIMG.FILENAME\tIMG.NAME\tIMG.FILEPATH\tIMG.DEFAULT\r\n";
		
		$zipimg = new ZipArchive();			// Load zip library	
		$filenamess = str_replace(".sql",".zip",'frfiles/ImgFolders.sql');			// Zip name
		if($zipimg->open($filenamess, ZIPARCHIVE::CREATE)!==TRUE)
		{		// Opening zip file to load files
			$error .=  "* Sorry ZIP creation failed at this time<br/>"; echo $error; 
		}
		 
		foreach($sql as $rows)
		{
			$name=explode("/",$rows[csf("image_location")]);
			foreach( $img_prod[$rows[csf("master_tble_id")]] as $job  )
			{
				$txt .=$job."\t".$name[1]."\t".str_replace(".jpg","",$name[1])."\t".$name[1]."\t1\r\n";
		 		
			}

			$zipimg->addFile("../../../".$rows[csf("image_location")]);
		}
		$zipimg->close();
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
	}
	
	$zip = new ZipArchive();			// Load zip library	
	$filename = str_replace(".sql",".zip",'frfiles/fr_files.sql');			// Zip name
	if($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE)
	{		// Opening zip file to load files
		$error .=  "* Sorry ZIP creation failed at this time<br/>"; echo $error;
	} 
	
	foreach (glob("frfiles/"."*.*") as $filenames)
	{			
	   $zip->addFile($filenames);		
	}
	
	//$zip->addFile("frfiles/ImgFolders.zip");
	$zip->close();
	echo "0**d";
	exit();
}

/*
select sum(b.document_currency) as tot_document_currency 
from com_export_proceed_realization a, 2815
com_export_proceed_rlzn_dtls b, 
com_export_doc_submission_invo c 
where a.id=b.mst_id and a.invoice_bill_id=c.doc_submission_mst_id -- and a.benificiary_id like '1' 
--and a.is_invoice_bill=1 --and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 
and b.insert_date between '02-Mar-2015' and '03-Mar-2015' 
--Group By b.document_currency
Order by 1

select a.buyer_id,c.is_lc,c.lc_sc_id,b.type,sum(b.document_currency) as tot_document_currency 
from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_doc_submission_invo c 
where a.id=b.mst_id and a.invoice_bill_id=c.doc_submission_mst_id and a.benificiary_id like '1' and a.is_invoice_bill=1 
and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 
and b.insert_date between '02-Mar-2015' and '03-Mar-2015' group by a.buyer_id,c.is_lc,c.lc_sc_id,b.type


select sum(nvl(b.document_currency,0)) as tot_document_currency 
from com_export_proceed_rlzn_dtls b
where b.insert_date between '02-Mar-2015' and '03-Mar-2015' 
--group by a.buyer_id --,c.is_lc,c.lc_sc_id,b.type,a.insert_date,a.update_date,b.insert_date,b.update_date
Order by 1
*/

?>