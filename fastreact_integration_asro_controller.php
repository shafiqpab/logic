<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
if($user_id=="") $user_id=99999;
 echo "0**";
$fr_product_type=array(1=>"Tee",
    2=>"Tee",
    3=>"Polo",
    4=>"Polo",
    5=>"Tank Top",
    6=>"Designed Tee",
    7=>"Jacket",
    8=>"Henley",
    9=>"Designed Tee",
    10=>"Designed Tee",
    11=>"Designed Tee",
    12=>"Others",
    14=>"Fashion jacket",
    15=>"Jacket",
    16=>"Nighty",
    17=>"Others",
    18=>"Fashion dress top",
    19=>"Fashion dress top",
    20=>"Pant",
    21=>"Pant",
    22=>"Pant",
    23=>"Others",
    24=>"Romper",
    25=>"Romper",
    26=>"Romper",
    27=>"Romper",
    28=>"Legging",
    29=>"Pant",
    30=>"Skirt",
    31=>"Jumpsuit",
    32=>"Others",
    33=>"Jumpsuit",
    34=>"Jumpsuit",
    35=>"Legging",
    36=>"Bag",
    37=>"Bra",
    38=>"Boxer",
    39=>"Sweat Shirt",
    40=>"Singlet",
    41=>"Singlet",
    42=>"Boxer",
    43=>"Boxer",
    44=>"Boxer",
    45=>"Boxer",
    46=>"Boxer",
    47=>"Boxer",
    48=>"Boxer",
    49=>"Boxer",
    50=>"Boxer",
    51=>"Others",
    52=>"Designed Tank Top",
    53=>"Others",
    54=>"Boxer",
    60=>"Others",
    61=>"Others",
    62=>"Others",
    63=>"Others",
    64=>"Others",
    65=>"Others",
    66=>"Others",
    67=>"Pant",
    68=>"Others",
    69=>"Jogger",
    70=>"Pant",
    71=>"Pant",
    72=>"Others",
    73=>"Designed Tank Top",
    74=>"Others",
    75=>"Others",
    76=>"Tank top",
    77=>"Tank top",
    78=>"Others",
    79=>"Others",
    80=>"Romper",
    81=>"Romper",
    82=>"Romper",
    83=>"Others",
    84=>"Designed Tee",
    85=>"Designed Tee",
    86=>"Others",
    87=>"Nighty",
    88=>"Others",
    89=>"Designed Tee",
    90=>"Designed Tee",
    91=>"Jogger",
    92=>"Jogger",
    93=>"Jogger",
    94=>"Jogger",
    95=>"Others",
    96=>"Nighty",
    97=>"Designed Tee",
    98=>"Romper",
    99=>"Others",
    100=>"Others",
    101=>"Others",
    102=>"Tee",
    103=>"Designed Tee",
    104=>"Others",
    105=>"Jumpsuit",
    106=>"Others",
    107=>"Others",
    108=>"Cardigan",
    109=>"Nighty",
    110=>"Nighty",
    111=>"Designed Tee",
    112=>"Tee",
    113=>"Designed Tank Top",
    114=>"Boxer",
    115=>"Boxer",
    116=>"Boxer",
    117=>"Boxer",
    118=>"Boxer",
    119=>"Others",
    120=>"Boxer",
    121=>"Designed Tank Top",
    122=>"Boxer",
    123=>"Boxer",
    124=>"Tee",
    125=>"Tee");
 
  //$garments_item=return_library_array("select id,item_name from lib_garment_item where status_active=1 and is_deleted=0 order by item_name","id","item_name");
//$production_process=array(1=>"Cutting",2=>"Knitting",3=>"Dyeing",4=>"Finishing",5=>"Sewing",6=>"Fabric Printing",7=>"Washing",8=>"Gmts Printing",9=>"Embroidery",10=>"Iron",11=>"Gmts Finishing");

$production_process_freact=array(1=>"Yarn",2=>"Knitting",3=>"AOP",4=>"Cutting",5=>"Printing",6=>"Printing Received",7=>"Embroidery",8=>"Sweing In",9=>"Sewing Out",10=>"Wash",11=>"Iron Output",12=>"Gmts Finishing",13=>"Ex-Factory",14=>"Dyeing");

$gmt_prod_id_map[1]=4;
//$gmt_prod_id_map[2]=6;
//$gmt_prod_id_map[3]=5;
$gmt_prod_id_map[4]=8;
$gmt_prod_id_map[5]=9;
$gmt_prod_id_map[7]=11;
$gmt_prod_id_map[8]=12;
$gmt_prod_id_map[9]=5;
$gmt_prod_id_map[10]=6;
$gmt_prod_id_map[11]=7;
$gmt_prod_id_map[12]=10;

$fariha_cluster=explode(",","172,173,174,175,176,193,194,195,196,197,198,199,200,209,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,162,163,164,165,166,167,168,169,170,171,202,203,204,205,206,207,208");
$fariha_cluster = array_combine($fariha_cluster, $fariha_cluster);
 
$unit_b=explode(",","61,62,63,160,161,201,211,215,216,217,218,219,79,80,81,82,83,84,85,86,87,88");
$unit_b = array_combine($unit_b, $unit_b);
$gala=explode(",","225");
$gala = array_combine($gala, $gala);

$tna_task_id_fr="'10','12','31','32','50','51','52','60','61','62','63','70','71','72','73','80','101','110'";
$mapped_tna_task =array(						 				
						//1	=> "Order Placement Date",			
						//2	=> "Order Evaluation",			
						//3	=> "Acceptance To Be Given",			
						//4	=> "Internal Communication To Be Done",			
						//5	=> "SC/LC Received",
					//	7	=> "Fit Sample Submit",
						//8	=> "PP Sample Submit",
					//	9	=> "Labdip Submit", 			
						10	=> "Labdip Approval",			
						//11	=> "Trims Approval",			
						12	=> "PP Sample Approval",
						//13	=> "Fit Sample Approval",
						//14	=> "Size Set Submission",
						//15	=> "Size Set Approval",
						//16	=> "Production Sample Submission",
					//	17	=> "Production Sample Approval",
					//	19	=> "Embellishment Submission",			
					//	20	=> "Embellishment Approval",
					//	21	=> "Tag Sample Submission",
					//	22	=> "Tag Sample Approval",
					//	23	=> "Photo Sample Submission",
					//	24	=> "Photo Sample Approval",
					//	25	=> "Trims Submission",
					//	26	=> "Packing  Sample Submission",
					//	27	=> "Packing  Sample Approval",
					//	28	=> "Final  Sample Submission",
					//	29	=> "Final  Sample Approval",
					//	30	=> "Sample Fabric Booking To Be Issued",			
						31	=> "Fabric Booking To Be Issued",			
					 	32	=> "Trims Booking To Be Issued",			
					//	33	=> "Fabric Service Work Order To Be Issued",
					//	34	=> "Woven Fabric Work Order To Be Issued",			
					//	40	=> "Fabric Test To Be Done",			
					//	41	=> "Garments Test To Be Done",
					//	45	=> "Yarn purchase requisition",
					//	46	=> "Yarn purchase order",
					//	47	=> "Yarn Receive",
					//	48	=> "Yarn Allocating",		
						50	=> "Yarn Issue To Be Done",			
						51	=> "Yarn Send for Dyeing",			
						52	=> "Dyed Yarn Receive",
									
						60	=> "Gray Fabric Production To Be Done",			
						61	=> "Dyeing Production To Be Done",			
						62	=> "Fabric Send for AOP",			
						63	=> "AOP Receive",			
						//64	=> "Finish Fabric Production To Be Done",			
					 	70	=> "Sewing Trims To Be In-house",			
						71	=> "Finishing Trims To Be In-house",			
						72	=> "Grey fabric to be in-house",			
						73	=> "Finished fabric to be in-house",	
						//74	=> "Finish Fabric Issue to Cut",		
						80	=> "PP Meeting To Be Conducted",			
						//81	=> "Trail cut to be done",			
						//82	=> "Trail production to be submitted",			
						//83	=> "Trail production approval to be received",			
						//84	=> "Cutting To Be Done",			
						//85	=> "Print/Emb To Be Done",			
					//	86	=> "Sewing To Be Done",			
						//87	=> "Iron To Be Done",			
					//	88	=> "Garments Finishing To Be Done",	
					//	89	=> "Garments sent for Wash",	
						//90	=> "Garments Receive from Wash",
						//91	=> "Poly Entry done",
						//100	=> "Inspection Schedule To Be Offered",			
						101	=> "Inspection To Be Done",			
						110	=> "Ex-Factory To Be Done",			
						//120	=> "Document to be submited",			
						//121	=> "Proceeds to be realized",
						//122	=> "Sewing Input To Be Done",
						//123	=> "Test Sample Approval",
						//124	=> "Photo In Lay/Litho Link"
						);	

 
$line_name=return_library_array("select id,line_name from lib_sewing_line","id","line_name");

//if ( $action=="save_update_delete" )
//{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	foreach (glob("frfiles/"."*.*") as $filename)
	{			
		@unlink($filename);
	}
	//echo $received_date; 
	$cbo_fr_integrtion=0; $received_date='';
	header('Content-Type: text/csv; charset=utf-8');
	if ( $cbo_fr_integrtion==0 )
	{
		$color_lib=return_library_array("select id,color_name from lib_color","id","color_name");
		$size_lib=return_library_array("select id,size_name from lib_size","id","size_name");
		$floor_name=return_library_array("select id,floor_name from  lib_prod_floor","id","floor_name");
		$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
		$line_name_res=return_library_array("select id,line_number from prod_resource_mst","id","line_number");
		$floor_name_res=return_library_array("select id,floor_id from prod_resource_mst","id","floor_id");
		$team_leader_arr=return_library_array("select id,team_leader_name from lib_marketing_team","id","team_leader_name");
		 
		// Customer File
		$sql=sql_select ( "select id, buyer_name, short_name from lib_buyer" );
		$file_name="frfiles/CUSTOMER.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
		$txt .=str_pad("C.CODE",0," ")."\tEND\r\n";
		foreach($sql as $name)
		{
			//if(strlen($name)>6) $txt .=$name."\tEND\r\n"; else 
			$buyer_name_array[$name[csf("id")]]=$name[csf("short_name")];
			$txt .=str_pad($name[csf("short_name")],0," ")."\tEND\r\n";
		}
		fwrite($myfile, $txt);
		fclose($myfile); 
		
		$txt="";
		if( $db_type==0 )
		{
			if(trim( $received_date)=="") $received_date="2015-08-01"; else $received_date=change_date_format($received_date, "yyyy-mm-dd", "-",1);
		}
		else if($db_type==2)
		{
			if(trim( $received_date)=="") $received_date=date('d-M-Y', strtotime('-7 months')); else $received_date=date("d-M-Y",strtotime($received_date));//$received_date=change_date_format($received_date, "yyyy-mm-dd", "-",1);
		}
		// Products file Data
		//echo $received_date; die;
		if( $db_type==0 )
		{
			//$shipment_date="and c.country_ship_date >= '2014-11-01'";
			//$shipment_date="and b.po_received_date >= '$received_date'";
			$shipment_date=" and b.pub_shipment_date >= '$received_date'";
			
		}
		else if($db_type==2)
		{
			$shipment_date=" and b.pub_shipment_date >= '$received_date'";
		}
		
		//echo $shipment_date; die;
		
		$shiping_status="and c.shiping_status !=3";
			
		$po_arr=array();
		$ft_data_arr=array();
		 
		//$sql_po="select a.id, a.job_no, a.style_ref_no, a.style_description, a.gmts_item_id, a.buyer_name, a.team_leader, a.bh_merchant, a.season, b.id as bid, b.po_number, b.is_confirmed, b.is_deleted, b.po_quantity, b.pub_shipment_date, c.id as color_size_break_id, c.item_number_id, c.country_ship_date, c.country_id, c.cutup, c.color_number_id, c.size_number_id, c.plan_cut_qnty, c.order_quantity, c.order_total, c.shiping_status, d.costing_per, d.sew_smv, e.fab_knit_req_kg from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_mst d, wo_pre_cost_sum_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id $shipment_date and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  order by a.id";
		
		//$sql_po="select a.id, a.job_no, a.style_ref_no, a.style_description, a.gmts_item_id, a.buyer_name, a.team_leader, a.bh_merchant, a.agent_name, a.season, b.id as bid, b.po_number, b.is_confirmed , b.po_quantity, b.pub_shipment_date, c.id as color_size_break_id,a.order_uom, c.item_number_id, c.country_ship_date, c.country_id,c.cutup, c.color_number_id, c.size_number_id, c.plan_cut_qnty, c.order_quantity, c.order_total, c.shiping_status, d.costing_per, d.sew_smv, e.fab_knit_req_kg, b.status_active,a.style_ref_no_prev, a.gmts_item_id_prev,b.po_number_prev,b.pub_shipment_date_prev,c.country_ship_date_prev,c.color_number_id_prev from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_mst d, wo_pre_cost_sum_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id $shipment_date and a.is_deleted=0 and a.status_active=1 and c.is_deleted=0 and c.status_active=1  order by a.id";
		//and b.is_deleted=0 and b.status_active=1 
		
			$sql_po="select a.id, a.job_no, a.style_ref_no, a.style_description, a.gmts_item_id, a.buyer_name, a.team_leader, a.bh_merchant, a.agent_name, a.season, b.id as bid, b.po_number, b.is_confirmed , b.po_quantity, b.pub_shipment_date, b.extended_ship_date, c.id as color_size_break_id,a.order_uom, c.item_number_id, c.country_ship_date, c.country_id,c.cutup, c.color_number_id, c.size_number_id, c.plan_cut_qnty, c.order_quantity, c.order_total, c.shiping_status, b.status_active,a.style_ref_no_prev, a.gmts_item_id_prev,b.po_number_prev,b.pub_shipment_date_prev,c.country_ship_date_prev,c.color_number_id_prev 
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c 
		where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id $shipment_date and a.is_deleted=0 and a.status_active=1 and c.is_deleted=0 and c.status_active=1  order by a.id";
		
		//echo $sql_po; die;
		//and a.job_no in ('ASTL-16-00266','FKTL-16-00617','AST-17-00071','FKTL-17-00038') 
		//echo $sql_po; //die;and a.job_no='FKTL-15-00002'  , b.id, c.country_ship_date, c.cutup
		$sql_po_data=sql_select($sql_po); $product_size_arr=array(); $orders_arr=array(); $orders_data_arr=array(); $order_size_arr=array(); $prod_up_arr=array();
		foreach($sql_po_data as $porow)
		{
			$po_arr[po_id][$porow[csf('id')]]=$porow[csf('id')];
			$po_arr[col_po_id][$porow[csf('color_size_break_id')]]=$porow[csf('color_size_break_id')];
			
			$po_arr[job_no][$porow[csf('job_no')]]="'".$porow[csf('job_no')]."'";
			$po_arr[costing_per][$porow[csf('job_no')]]=$porow[csf('costing_per')];
			
			$ft_data_arr[$porow[csf('job_no')]][agent_name]=$buyer_name_array[$porow[csf('agent_name')]];//P.CODE
			$ft_data_arr[$porow[csf('job_no')]][job_no]=$porow[csf('job_no')];//P.CODE
			$ft_data_arr[$porow[csf('job_no')]][gmts_item_id]=$porow[csf('gmts_item_id')];//P.TYPE
			$ft_data_arr[$porow[csf('job_no')]][style_ref_no]=$porow[csf('style_ref_no')];//P.DESCRIP
			$ft_data_arr[$porow[csf('job_no')]][style_description]=$porow[csf('style_description')];//P.DESCRIP
			$fab_knit_req_kg=0;
			
			//$ft_data_arr[$porow[csf('job_no')]][old_job_no]=$porow[csf('job_no')];//P.CODE
			$ft_data_arr[$porow[csf('job_no')]][old_gmts_item_id]=$porow[csf('gmts_item_id_prev')];//P.TYPE
			$ft_data_arr[$porow[csf('job_no')]][old_style_ref_no]=$porow[csf('style_ref_no_prev')];//P.DESCRIP
			//$ft_data_arr[$porow[csf('job_no')]][old_style_description]=$porow[csf('style_description')];//P.DESCRIP
			
			
			$ft_data_arr[$porow[csf('job_no')]][buyer_name]=$porow[csf('buyer_name')];
			$ft_data_arr[$porow[csf('job_no')]][team_leader]=$porow[csf('team_leader')];
			$ft_data_arr[$porow[csf('job_no')]][bh_merchant]=$porow[csf('bh_merchant')];
			$ft_data_arr[$porow[csf('job_no')]][po_quantity]=$porow[csf('po_quantity')];
			$po_break[$porow[csf('bid')]]=$porow[csf('bid')];
			
			$ft_data_arr[$porow[csf('job_no')]][cutting]=1;//P^WC:10
			
			$ft_data_arr[$porow[csf('job_no')]][sew_input]=1;//P^WC:35
			$ft_data_arr[$porow[csf('job_no')]][sew_output]=$porow[csf('sew_smv')];//P^WC:140
			
			$ft_data_arr[$porow[csf('job_no')]][poly]=1;//P^WC:160
			
			$product_size_arr[$porow[csf('job_no')]][$size_lib[$porow[csf('size_number_id')]]]=$size_lib[$porow[csf('size_number_id')]];//P.CODE and size for product size file
			
			$po_str=$porow[csf('bid')].'_'.$porow[csf('pub_shipment_date')].'_'.$porow[csf('is_confirmed')].'_'.$porow[csf('status_active')].'_'.$porow[csf('extended_ship_date')];
			
			
			$orders_arr[$porow[csf('job_no')]][$porow[csf('po_number')]][$porow[csf('pub_shipment_date')]][$porow[csf('item_number_id')]][$porow[csf('color_number_id')]]['plan_cut']+=$porow[csf('plan_cut_qnty')];
			$orders_arr[$porow[csf('job_no')]][$porow[csf('po_number')]][$porow[csf('pub_shipment_date')]][$porow[csf('item_number_id')]][$porow[csf('color_number_id')]]['order_qty']+=$porow[csf('order_quantity')];
			$orders_arr[$porow[csf('job_no')]][$porow[csf('po_number')]][$porow[csf('pub_shipment_date')]][$porow[csf('item_number_id')]][$porow[csf('color_number_id')]]['order_value']+=$porow[csf('order_total')];
			$orders_arr[$porow[csf('job_no')]][$porow[csf('po_number')]][$porow[csf('pub_shipment_date')]][$porow[csf('item_number_id')]][$porow[csf('color_number_id')]]['color_size_break_id'].=trim($porow[csf('color_size_break_id')]).'**'.$porow[csf('shiping_status')].',';
			
			$orders_arr[$porow[csf('job_no')]][$porow[csf('po_number')]][$porow[csf('pub_shipment_date')]][$porow[csf('item_number_id')]][$porow[csf('color_number_id')]]['bid'].=$porow[csf('bid')].",";
			$orders_arr[$porow[csf('job_no')]][$porow[csf('po_number')]][$porow[csf('pub_shipment_date')]][$porow[csf('item_number_id')]][$porow[csf('color_number_id')]]['order_uom']=$porow[csf('order_uom')];
			$orders_arr[$porow[csf('job_no')]][$porow[csf('po_number')]][$porow[csf('pub_shipment_date')]][$porow[csf('item_number_id')]][$porow[csf('color_number_id')]]['po_str']=$po_str;
			
			$orders_arr[$porow[csf('job_no')]][$porow[csf('po_number')]][$porow[csf('pub_shipment_date')]][$porow[csf('item_number_id')]][$porow[csf('color_number_id')]]['po_sys_id']=$porow[csf('bid')];
			
			$orders_arr[$porow[csf('job_no')]][$porow[csf('po_number')]][$porow[csf('pub_shipment_date')]][$porow[csf('item_number_id')]][$porow[csf('color_number_id')]]['old_po_number']=$porow[csf('po_number_prev')];
			$orders_arr[$porow[csf('job_no')]][$porow[csf('po_number')]][$porow[csf('pub_shipment_date')]][$porow[csf('item_number_id')]][$porow[csf('color_number_id')]]['old_color_number']=$porow[csf('color_number_id_prev')];
			$orders_arr[$porow[csf('job_no')]][$porow[csf('po_number')]][$porow[csf('pub_shipment_date')]][$porow[csf('item_number_id')]][$porow[csf('color_number_id')]]['old_pub_shipdate']=$porow[csf('pub_shipment_date_prev')];
			$orders_arr[$porow[csf('job_no')]][$porow[csf('po_number')]][$porow[csf('pub_shipment_date')]][$porow[csf('item_number_id')]][$porow[csf('color_number_id')]]['old_country_shipdate']=$porow[csf('country_ship_date_prev')];
			
			
			//$str_po=$po_no."::".$jobno."::".$color_lib[$color_id]."".$str_item."::".date("d-m-Y",strtotime($shipdate));  
			$order_size_array[$porow[csf('job_no')]][$porow[csf('po_number')]][$porow[csf('item_number_id')]][$porow[csf('color_number_id')]][$porow[csf('pub_shipment_date')]][$porow[csf('size_number_id')]]+=$porow[csf('order_quantity')];
			
			$new_color_size[$porow[csf('job_no')]][$porow[csf('po_number')]][$porow[csf('pub_shipment_date')]][$porow[csf('item_number_id')]][$porow[csf('color_number_id')]][$porow[csf('color_size_break_id')]]=$porow[csf('color_size_break_id')];
			
			$orders_data_arr[$porow[csf('bid')]]['po_no']=$porow[csf('po_number')];
			$orders_data_arr[$porow[csf('bid')]]['season']=$porow[csf('season')];
			
			$order_size_arr[$porow[csf('po_number')]][$porow[csf('job_no')]][$porow[csf('item_number_id')]][$porow[csf('color_number_id')]][$porow[csf('size_number_id')]]+=$porow[csf('order_quantity')];
			$prod_up_arr[$porow[csf('color_size_break_id')]]=$porow[csf('size_number_id')];
			$prod_up_arr_powise[$porow[csf('color_size_break_id')]]=$porow[csf('bid')];
		}
		//print_r($new_color_size); die;
		unset($sql_po_data);
		$po_string= implode(",",$po_arr[po_id]);
		$job_string= implode(",",$po_arr[job_no]);
		//echo "10**";
		$con=connect();
		execute_query("delete from tmp_poid",0);
		oci_commit($con);
		foreach($po_arr[col_po_id] as $ids)
		{
			//echo "insert into tmp_poid (userid, poid) values (".$user_id.",$ids)"; 
			execute_query("insert into tmp_poid (userid, poid) values (".$user_id.",$ids)");
		}
		oci_commit($con);/*		echo "test";*/
		
		 
	    $job=array_chunk($po_arr[job_no],999, true);
	    $job_cond_in=""; $job_cond_img='';  $ji=0; $job_cond_ind=''; $job_cond_in_mst="";
		foreach($job as $key=> $value)
		{
			if($ji==0)
			{
				$job_cond_in.="and job_no in(".implode(",",$value).")"; 
				$job_cond_ind.="job_no in(".implode(",",$value).")"; 
				$job_cond_in_mst.=" a.job_no_mst in(".implode(",",$value).")"; 
				$job_cond_img.=" and master_tble_id in(".implode(",",$value).")"; 
			}
			else
			{
				$job_cond_in.=" or job_no in(".implode(",",$value).")"; 
				$job_cond_in_mst.=" or a.job_no_mst in(".implode(",",$value).")"; 
				$job_cond_img.=" or master_tble_id in(".implode(",",$value).")"; 
				$job_cond_ind.=" or job_no in(".implode(",",$value).")"; 
			}
			$ji++;
		}
		//print_r($job_cond_in); die;
		  
		  $sql_po="select  d.job_no, d.costing_per, d.sew_smv, e.fab_knit_req_kg 
		from  wo_pre_cost_mst d, wo_pre_cost_sum_dtls e 
		where d.job_no=e.job_no and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1  $job_cond_in";
		 
		//and a.job_no in ('ASTL-16-00266','FKTL-16-00617','AST-17-00071','FKTL-17-00038') 
		//echo $sql_po; //die;and a.job_no='FKTL-15-00002'  , b.id, c.country_ship_date, c.cutup
		//$sql_po_data=sql_select($sql_po); $product_size_arr=array(); $orders_arr=array(); $orders_data_arr=array(); $order_size_arr=array(); $prod_up_arr=array();
		foreach($sql_po_data as $porow)
		{
			if($porow[csf('costing_per')]==1) $fab_knit_req_kg=$porow[csf('fab_knit_req_kg')]/12;
			else if($porow[csf('costing_per')]==2) $fab_knit_req_kg=$porow[csf('fab_knit_req_kg')];
			else if($porow[csf('costing_per')]==3) $fab_knit_req_kg=$porow[csf('fab_knit_req_kg')]/24;
			else if($porow[csf('costing_per')]==4) $fab_knit_req_kg=$porow[csf('fab_knit_req_kg')]/36;
			else if($porow[csf('costing_per')]==5) $fab_knit_req_kg=$porow[csf('fab_knit_req_kg')]/48;
			
			$ft_data_arr[$porow[csf('job_no')]][fab_knit_req_kg]=number_format($fab_knit_req_kg,3);//P^CF:5
			
			$ft_data_arr[$porow[csf('job_no')]][shiped]=$porow[csf('sew_smv')];//P^WC:165
			
		}
		unset($sql_po_data);
		
		/* $sql_booking="select  a.po_break_down_id, d.booking_no 
		from wo_booking_mst d, wo_booking_dtls a
		where $job_cond_ind and a.booking_no =d.booking_no  and d.booking_type=1 and d.is_short=2 and d.is_deleted=0 and d.status_active=1  ";*/
		$sql_booking="select po_break_down_id, booking_no 
		from wo_booking_dtls 
		where ( $job_cond_ind ) and booking_type=1 and is_short=2 and is_deleted=0 and status_active=1 group by po_break_down_id, booking_no";
		//echo  $sql_booking; die;
		//and a.job_no in ('ASTL-16-00266','FKTL-16-00617','AST-17-00071','FKTL-17-00038') 
		//echo $sql_po; //die;and a.job_no='FKTL-15-00002'  , b.id, c.country_ship_date, c.cutup
		$sql_booking_data=sql_select($sql_booking);// $product_size_arr=array(); $orders_arr=array(); $orders_data_arr=array(); $order_size_arr=array(); $prod_up_arr=array();
		//die;
		foreach($sql_booking_data as $porow)
		{
			$porow[csf('booking_no')]=str_replace("AST-Fb-","",$porow[csf('booking_no')]);
			$porow[csf('booking_no')]=str_replace("FKTL-Fb-","",$porow[csf('booking_no')]);
			$porow[csf('booking_no')]=str_replace("ASTL-Fb-","",$porow[csf('booking_no')]);
			$porow[csf('booking_no')]=str_replace("FKTL-FB-","",$porow[csf('booking_no')]);
			
			if( $ft_data_arr_book[$porow[csf('po_break_down_id')]]['booking_no']=='')
				$ft_data_arr_book[$porow[csf('po_break_down_id')]]['booking_no']=$porow[csf('booking_no')];//P^WC:165
			else
				$ft_data_arr_book[$porow[csf('po_break_down_id')]]['booking_no'] .=','.$porow[csf('booking_no')];
		}
		unset($sql_booking_data);
		//print_r($ft_data_arr_book);
		//die;
		 
		 
		$sql_fabric_prod=sql_select("select min(id) as id, job_no, color_type_id from wo_pre_cost_fabric_cost_dtls where $job_cond_in and fabric_source=1 and avg_cons>0 and status_active=1 and is_deleted=0  group by job_no, color_type_id");
		foreach($sql_fabric_prod as $row_fabric_prod)
		{
			$ft_data_arr[fab_delivery][$row_fabric_prod[csf('job_no')]]=1;//P^WC:5 
			if($row_fabric_prod[csf('color_type_id')]==5) $ft_data_arr[aop][$row_fabric_prod[csf('job_no')]]=1;
			else $ft_data_arr[aop][$row_fabric_prod[csf('job_no')]]=0;
			  
		}
		unset($sql_fabric_prod);
		$sql_print_embroid=sql_select("select min(id) as id, job_no, emb_name, avg(cons_dzn_gmts) as cons_dzn_gmts from wo_pre_cost_embe_cost_dtls where $job_cond_in and emb_name in(1,2,3) and cons_dzn_gmts>0  and status_active=1 and is_deleted=0 group by job_no, emb_name");
		foreach($sql_print_embroid as $row_print_embroid)
		{
			if($row_print_embroid[csf('emb_name')]==1)
			{
				$ft_data_arr[$row_print_embroid[csf('job_no')]][dv_printing]=1; //P^WC:15
				$ft_data_arr[$row_print_embroid[csf('job_no')]][rv_printing]=1; //P^WC:20
			}
			else $ft_data_arr[$row_print_embroid[csf('job_no')]][dv_printing]=0; //P^WC:15
			if($row_print_embroid[csf('emb_name')]==2)
			{
				$ft_data_arr[$row_print_embroid[csf('job_no')]][dv_embrodi]=1; //P^WC:25
				$ft_data_arr[$row_print_embroid[csf('job_no')]][rv_embrodi]=1; //P^WC:30
			}
			else $ft_data_arr[$row_print_embroid[csf('job_no')]][dv_embrodi]=0; //P^WC:25
			if($row_print_embroid[csf('emb_name')]==3)
			{
				$ft_data_arr[$row_print_embroid[csf('job_no')]][dv_wash]=1; //P^WC:145
				$ft_data_arr[$row_print_embroid[csf('job_no')]][rv_wash]=1; //P^WC:150
			}
			else $ft_data_arr[$row_print_embroid[csf('job_no')]][dv_wash]=0; //P^WC:145
		}
		
		unset($sql_print_embroid);
		//=================================Item wise Array Srart=====================================
		$arr_itemsmv=array();
		//echo "select job_no, gmts_item_id, set_item_ratio, smv_pcs_precost, smv_set_precost, smv_pcs, embelishment from wo_po_details_mas_set_details where $job_cond_in";
		$sql_itemsmv=sql_select("select job_no, gmts_item_id, set_item_ratio, smv_pcs_precost, smv_set_precost, smv_pcs, embelishment from wo_po_details_mas_set_details where 1=1 $job_cond_in");
		foreach($sql_itemsmv as $row_itemsmv)
		{
			$arr_itemsmv[$row_itemsmv[csf('job_no')]][$row_itemsmv[csf('gmts_item_id')]]['smv']=$row_itemsmv[csf('smv_pcs')]; 
			$arr_itemsmv[$row_itemsmv[csf('job_no')]][$row_itemsmv[csf('gmts_item_id')]]['emb']=$row_itemsmv[csf('embelishment')];  
		}
		unset($sql_itemsmv);
		 
		// =================================Item wise Array End=====================================
		
		
		
		//print_r($array_fabric_prod_item); die;
		// Products file
		$txt=""; $myfile =''; $file_name='';
		$file_name="frfiles/PRODUCTS.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
	 	
		
		//$txt .=str_pad("P.CODE",0," ")."\t".str_pad("P.OLDCODE",0," ")."\t".str_pad("P.UDStyle",0," ")."\t".str_pad("P.DESCRIP",0," ")."\t".str_pad("P.TYPE",0," ")."\t".str_pad("P.CUST",0," ")."\t".str_pad("P^WC:1",0," ")."\t".str_pad("P^CF:1",0," ")."\t".str_pad("P^WC:2",0," ")."\t".str_pad("P^CF:2",0," ")."\t".str_pad("P^WC:14",0," ")."\t".str_pad("P^CF:14",0," ")."\t".str_pad("P^WC:3",0," ")."\t".str_pad("P^CF:3",0," ")."\t".str_pad("P^WC:4",0," ")."\t".str_pad("P^WC:5",0," ")."\t".str_pad("P^WC:7",0," ")."\t".str_pad("P^WC:8",0," ")."\t".str_pad("P^WC:9",0," ")."\t".str_pad("P^WC:10",0," ")."\t".str_pad("P^WC:11",0," ")."\t".str_pad("P^WC:12",0," ")."\t".str_pad("P^WC:13",0," ")."\t".str_pad("P.UDAgent",0," ")."\t".str_pad("P.UDMarketing Head",0," ")."\r\n";
		$txt .=str_pad("P.CODE",0," ")."\t".str_pad("P.OLDCODE",0," ")."\t".str_pad("P.UDStyle",0," ")."\t".str_pad("P.DESCRIP",0," ")."\t".str_pad("P.TYPE",0," ")."\t".str_pad("P.CUST",0," ")."\t".str_pad("P^WC:4",0," ")."\t".str_pad("P^WC:6",0," ")."\t".str_pad("P^WC:7",0," ")."\t".str_pad("P^WC:8",0," ")."\t".str_pad("P^WC:9",0," ")."\t".str_pad("P^WC:10",0," ")."\t".str_pad("P^WC:11",0," ")."\t".str_pad("P^WC:12",0," ")."\t".str_pad("P^WC:13",0," ")."\t".str_pad("P.UDAgent",0," ")."\t".str_pad("P.UDMarketing Head",0," ")."\r\n";
		//echo $txt; die;
	
		foreach($ft_data_arr as $rows)
		{
			$item_chaned=0;
			$gitem=explode(",",$rows[gmts_item_id]);
			$old_gitem=explode(",", trim($rows[old_gmts_item_id]));
			$changgid=array_diff($gitem,$old_gitem);
			if(count($changgid)>0) $item_chaned=1;
			if(trim($rows[old_gmts_item_id])=='')$item_chaned=0;
			
			$dt="";
			$style_ref_arr[$rows[job_no]]=$rows[style_ref_no];
			$job_ref_arr[$rows[job_no]]=$rows[job_no];
			//$job_ref_arr[$rows[job_no]]=$rows[job_no];
			$aop=0; $aop=$ft_data_arr[aop][$rows[job_no]];
			
			$inc=0;
			if(count($gitem)>1)
			{
				$item_array[$rows[job_no]]=1;
				
				foreach( $gitem as $id )
				{
					$emb_req=''; $smv='';
					$smv=$arr_itemsmv[$rows[job_no]][$id]['smv'];
					$emb_req=$arr_itemsmv[$rows[job_no]][$id]['emb'];
					$img_prod[$rows[job_no]][]=$rows[style_ref_no]."::".$rows[job_no]."::".$fr_product_type[$id];
					
					//$ft_data_arr[$porow[csf('job_no')]][old_gmts_item_id]=$porow[csf('gmts_item_id_prev')];//P.TYPE
					//$ft_data_arr[$porow[csf('job_no')]][old_style_ref_no]
					$style_changed=0;
					$old_code='';
					if(trim($rows[old_style_ref_no])!='')
					{
						if(trim($rows[old_style_ref_no])!=trim($rows[style_ref_no]))
						{
							$old_code=$rows[old_style_ref_no]."::".$rows[job_no]."::".$fr_product_type[$id];
							$style_changed=1;
						}
					}
					if($item_chaned==1)
					{
						
						if($style_changed==1) 
							$old_code=$rows[old_style_ref_no]."::".$rows[job_no]."::".$fr_product_type[$old_gitem[$inc]];
						else
							$old_code=$rows[style_ref_no]."::".$rows[job_no]."::".$fr_product_type[$old_gitem[$inc]];
							
							$new_item_index[$rows[job_no]][$id]=$old_gitem[$inc];
							$ft_data_arr[$rows[job_no]][old_gmts_item_id_chaged]=1;
					}
				 
					$inc++;
			
					if(trim($rows[job_no])!="") $txt .=str_pad($rows[style_ref_no]."::".$rows[job_no]."::".$fr_product_type[$id],0," ")."\t".str_pad($old_code,0," ")."\t".str_pad($rows[style_ref_no],0," ")."\t".str_pad($rows[style_description],0," ")."\t".str_pad($fr_product_type[$id],0," ")."\t".str_pad($buyer_name_array[$rows[buyer_name]],0," ")."\t".str_pad("1",0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][dv_printing],0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][dv_embrodi],0," ")."\t".str_pad("1",0," ")."\t".str_pad($smv,0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][dv_wash],0," ")."\t".str_pad("1",0," ")."\t".str_pad("1",0," ")."\t".str_pad("1",0," ")."\t".str_pad($rows[agent_name],0," ")."\t".str_pad($team_leader_arr[$rows[team_leader]],0," ")."\r\n";
				}
			}
			else
			{
				$old_code='';
				if(trim($rows[old_style_ref_no])!='')
				{
					if(trim($rows[old_style_ref_no])!=trim($rows[style_ref_no]))
					{
						$old_code=$rows[old_style_ref_no]."::".$rows[job_no];
					}
				}
				
				$smv=$arr_itemsmv[$rows[job_no]][$rows[gmts_item_id]]['smv'];
				$img_prod[$rows[job_no]][]=$rows[style_ref_no]."::".$rows[job_no];
				if(trim($rows[job_no])!="") $txt .=str_pad($rows[style_ref_no]."::".$rows[job_no],0," ")."\t".str_pad($old_code,0," ")."\t".str_pad($rows[style_ref_no],0," ")."\t".str_pad($rows[style_description],0," ")."\t".str_pad($fr_product_type[$rows[gmts_item_id]],0," ")."\t".str_pad($buyer_name_array[$rows[buyer_name]],0," ")."\t".str_pad("1",0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][dv_printing],0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][dv_embrodi],0," ")."\t".str_pad("1",0," ")."\t".str_pad($smv,0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][dv_wash],0," ")."\t".str_pad("1",0," ")."\t".str_pad("1",0," ")."\t".str_pad("1",0," ")."\t".str_pad($rows[agent_name],0," ")."\t".str_pad($team_leader_arr[$rows[team_leader]],0," ")."\r\n";
			}
		}
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
	 
		
		$poIds=array_chunk($po_arr[po_id],999, true);
	    $poIds_cond_prod=""; $poIds_cond_tna=""; $poIds_cond_order=""; $jk=0;
		foreach($poIds as $key=> $value)
		{
			if($jk==0)
			{
				$poIds_cond_prod="and po_break_down_id in(".implode(",",$value).")"; 
				$poIds_cond_tna.=" po_number_id in(".implode(",",$value).")";
				$poIds_cond_order.=" and a.id in(".implode(",",$value).")"; 
			}
			else
			{
				$poIds_cond_prod.=" or po_break_down_id in(".implode(",",$value).")"; 
				$poIds_cond_tna.=" or po_number_id in(".implode(",",$value).")"; 
				$poIds_cond_order.=" or a.id in(".implode(",",$value).")"; 
			}
			$jk++;
		}
		 
		$file_name="frfiles/ORDERS.TXT";
		$myfile = fopen( $file_name, "w" ) or die("Unable to open file!");
		
		$txt .=str_pad("O.CODE",0," ")."\t".str_pad("O.OLDCODE",0," ")."\t".str_pad("O.PROD",0," ")."\t".str_pad("O.CUST",0," ")."\t".str_pad("O^DD:1",0," ")."\t".str_pad("O^DQ:1",0," ")."\t".str_pad("O.CONTRACT_QTY",0," ")."\t".str_pad("O.STATUS",0," ")."\t".str_pad("O.SPRICE",0," ")."\t".str_pad("O.DESCRIP",0," ")."\t".str_pad("O.UDWork Order",0," ")."\t".str_pad("O.UDSeason",0," ")."\t".str_pad("O.COMPLETE",0," ")."\t".str_pad("O^EXDD:1",0," ")."\t".str_pad("O^EXDR:1",0," ")."\r\n";
		$order_size=array(); $newid_ar=array();
		foreach($orders_arr as $jobno=>$po_data)
		{
			foreach($po_data as $po_st=>$ship_item_data)
			{
				foreach($ship_item_data as $ship_date=>$item_data)
				 {
					foreach($item_data as $item_id=>$color_data)
					{
						//foreach($color_data as $color_id=>$cutup_data)
						//{  
							foreach($color_data as $color_id=>$other_val)
							{
								if($other_val['order_qty']>1)
								{
									$ex_po=explode("_",$other_val['po_str']);
									$po_id=$other_val['bid']; 
									$shipdate=$ship_date;
									$is_confirm=$ex_po[2];
									$is_deleted=$ex_po[3];
									if($ex_po[4]=="") $extShipDate="";
									else $extShipDate=date("d/m/Y",strtotime($ex_po[4]));
									
									
									
									$col_sizebreak_id_str=""; $shiping_status_str="";
									$ex_other_data=explode(",",$other_val['color_size_break_id']);
									 
									if( $is_confirm==1 ) $str="F"; else $str="P";
									$po_no=$po_st;
									$old_str_po='';
									$str_po='';
									$changed=0;
									$ssts="0";
									
									$buyer_style=''; 
									if( $other_val['order_uom']!=1 ) 
									{
										if( $ft_data_arr[$jobno][old_gmts_item_id_chaged]==1)
										{
											$changed=1;
											//$old_item_code= $new_item_index[$jobno][$item_id] ;//[$rows[job_no]][$id]=$old_gitem[$inc];
											$old_item_code="::".$fr_product_type[$new_item_index[$jobno][$item_id]];
										}
										else
											$old_item_code="::".$fr_product_type[$item_id];
											
										$str_item="::".$fr_product_type[$item_id];
										$buyer_style=$ft_data_arr[$jobno][style_ref_no]."::".$jobno."::".$fr_product_type[$item_id];
									}
									else
									{
										$old_item_code='';
										$str_item=""; 
										$buyer_style=$ft_data_arr[$jobno][style_ref_no]."::".$jobno;
									}
									
									$job_wise_uom[$jobno]=$other_val['order_uom'];
									$str_po=$po_no."::".$jobno."::".$color_lib[$color_id]."".$str_item."::".date("d-m-Y",strtotime($shipdate));  
									
									if( $other_val['old_po_number']!='' && $other_val['old_po_number']!=$po_no)
									{
										$old_po= $other_val['old_po_number'];
										$changed=1;
									}
									else
										$old_po= $po_no;
									
									if( $other_val['old_color_number']!='' && $other_val['old_color_number']!=$color_id)
									{
										$old_color= $other_val['old_color_number'];
										$changed=1;
									}
									else
										$old_color= $color_id;
									
									if( $other_val['old_pub_shipdate']!='' && date("d-m-Y",strtotime($other_val['old_pub_shipdate']))!=date("d-m-Y",strtotime($shipdate)))
									{
										$old_pub_ship= $other_val['old_pub_shipdate'];
										$changed=1;
									}
									else
										$old_pub_ship= date("d-m-Y",strtotime($shipdate));
									
									if($changed==1)
										$old_str_po=$old_po."::".$jobno."::".$color_lib[$old_color]."".$old_item_code."::".date("d-m-Y",strtotime($old_pub_ship)); 
										
									foreach($new_color_size[$jobno][$po_st][$shipdate][$item_id][$color_id] as $cids)
									{
										$newid_ar[trim($cids)]=$str_po;
									}
									//color_size_break_id
									$ship_sts=array ();
									foreach($ex_other_data as $val_color_size_id)
									{
										$ex_color_size_break_id_val=explode("**",$val_color_size_id);
										$tmp_col_size[$ex_color_size_break_id_val[0]]=$ex_color_size_break_id_val[1];
										//if( $ex_color_size_break_id_val[1]==3 ) $ssts="1"; 
										$ship_sts[$ex_color_size_break_id_val[1]]=$ex_color_size_break_id_val[1];
									}
									//if( $ship_sts[1]=='' && $ship_sts[2]=='' )   $ssts="1"; 
									
									if( $ship_sts[3]==3 ) $ssts="1";  else $ssts="0";
									$fob=0; $fob=$other_val['order_value']/$other_val['order_qty'];
								
									//$txt .=str_pad($str_po,0," ")."\t".str_pad('',0," ")."\t".str_pad($buyer_style,0," ")."\t".str_pad($buyer_name_array[$ft_data_arr[$jobno][buyer_name]],0," ")."\t".str_pad(date("d/m/Y",strtotime($shipdate)),0," ")."\t".str_pad($other_val['plan_cut'],0," ")."\t".str_pad($other_val['order_qty'],0," ")."\t".str_pad($str,0," ")."\t".str_pad($fob,0," ")."\t".str_pad('',0," ")."\t".str_pad($style_ref_arr[$jobno],0," ")."\t".str_pad('',0," ")."\t".str_pad($orders_data_arr[$po_id]['season'],0," ")."\t".str_pad($ssts,0," ")."\r\n";
									$po_id_arr=array_filter(array_unique(explode(",",$po_id)));
									
									//print_r($po_id_arr);
									
									$booking_str=""; //$bookingNo='';
									foreach($po_id_arr as $poid)
									{
										//echo $ft_data_arr_book[$poid]['booking_no'].'=';
										//$bookingNo=implode(",",array_filter(array_unique(explode(",",$ft_data_arr_book[$pid]['booking_no']))));
										if ($booking_str=="") $booking_str=$ft_data_arr_book[$poid]['booking_no']; else $booking_str.=','.$ft_data_arr_book[$poid]['booking_no'];
									}
									
									$po_id_arrs=implode("",$po_id_arr);
									$exbooking_no=array_unique(explode(",",$booking_str));
									$bookingNo=implode(",",$exbooking_no);
									//echo $bookingNo.'=';
									if( $is_deleted==2 ||  $is_deleted==3  ) 
									{ 
										$str="X"; 
										$deleted_polist[$po_id_arrs]=$po_id_arrs;
									}  //$ssts=0;
									
									$str_po_list[$po_id_arrs][$str_po]=$str_po;	
									
									$txt .=str_pad($str_po,0," ")."\t".str_pad($old_str_po,0," ")."\t".str_pad($buyer_style,0," ")."\t".str_pad($buyer_name_array[$ft_data_arr[$jobno][buyer_name]],0," ")."\t".str_pad(date("d/m/Y",strtotime($shipdate)),0," ")."\t".str_pad($other_val['plan_cut'],0," ")."\t".str_pad($other_val['order_qty'],0," ")."\t".str_pad($str,0," ")."\t".str_pad($fob,0," ")."\t".str_pad($style_ref_arr[$jobno],0," ")."\t".str_pad($bookingNo,0," ")."\t".str_pad($orders_data_arr[$po_id]['season'],0," ")."\t".str_pad($ssts,0," ")."\t".str_pad($extShipDate,0," ")."\t\r\n";
								}
							}
						//}
					}
				 }
			}
		}
		
		//print_r ($newid_ar); die;
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
		//die;
		/*$file_name="frfiles/ORDSIZE.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
		$txt .=str_pad("O.CODE",0," ")."\t".str_pad("O.SIZENAME",0," ")."\t".str_pad("O.SIZERATIO",0," ")."\r\n";
		
		foreach($order_size_array as $job=>$job_val)
		{
			foreach($job_val as $po=>$item_val)
			{
				foreach($item_val as $item_id=>$color_val)
				{
					foreach($color_val as $color_id=>$shipdte)
					{
						foreach($shipdte as $shipdate=>$size_val)
						{
							foreach($size_val as $size_id=>$size_qty)
							{
								if( $size_qty>1 )
								{
									$str_po='';
									$ponum=$order_size[$po][$job][$item_id][$color_lib[$color_id]][date("d-m-Y",strtotime($shipdate))];
									 
									if( $job_wise_uom[$job]!=1 ) 
									{
										$str_item="::".$fr_product_type[$item_id];
									}
									else
									{
										$str_item=""; 
									}
										
									$str_po=$po."::".$job."::".$color_lib[$color_id]."".$str_item."::".date("d-m-Y",strtotime($shipdate));  
										
									$txt .=str_pad($str_po,0," ")."\t".str_pad($size_lib[$size_id],0," ")."\t".str_pad($size_qty,0," ")."\r\n";
								}
							}
						}
					}
				}
			}
		}
		unset($order_size_arr);
		fwrite($myfile, $txt);
		fclose($myfile); */
		
		$txt="";
		
		// Production file
		$file_name="frfiles/UPDNORM.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
		
		//$txt .=str_pad("U.ORDER",0," ")."\t".str_pad("U.DATE",0," ")."\t".str_pad("U.OPERATION",0," ")."\t".str_pad("U.LINE_EXTERNAL_ID",0," ")."\t".str_pad("U.SIZENAME",0," ")."\t".str_pad("U.QTY",0," ")."\r\n";
		$txt .=str_pad("U.ORDER",0," ")."\t".str_pad("U.DATE",0," ")."\t".str_pad("U.OPERATION",0," ")."\t".str_pad("U.LINE_EXTERNAL_ID",0," ")."\t".str_pad("U.QTY",0," ")."\r\n";
		
		$prod_sql="SELECT a.po_break_down_id, a.country_id, a.item_number_id, a.company_id, a.floor_id, a.sewing_line, a.production_type, a.production_date as production_date, b.color_size_break_down_id, sum(b.production_qnty) AS production_quantity, a.embel_name from pro_garments_production_mst a, pro_garments_production_dtls b, tmp_poid c where a.production_type in (1,3,4,5,8,7) and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.color_size_break_down_id=c.poid and c.userid='$user_id'
		group by a.po_break_down_id, a.country_id, a.item_number_id, a.company_id, a.floor_id, a.sewing_line, a.production_type, 
a.production_date, b.color_size_break_down_id, a.embel_name
		 order by  production_date ASC,b.color_size_break_down_id asc"; //$poIds_cond_prod tmp_poid (userid, poid)  and a.embel_name in (1,2,3,4,5) 
		//echo $prod_sql; die;
		$production_qty=array(); 
		$prod_sql_res=sql_select($prod_sql);
		foreach($prod_sql_res as $row_sew)
		{
			//echo $newid_ar[$row_sew[csf("color_size_break_down_id")]].'<br>';
			if($newid_ar[$row_sew[csf("color_size_break_down_id")]]!='' && $prod_up_arr_powise[$row_sew[csf("color_size_break_down_id")]]==$row_sew[csf("po_break_down_id")])
			{
				if($row_sew[csf("production_type")]==3)
				{
					if($row_sew[csf("embel_name")]==1) $row_sew[csf("production_type")]=10;
					else if($row_sew[csf("embel_name")]==2) $row_sew[csf("production_type")]=11;
					else if( $row_sew[csf("embel_name")]==3 ) $row_sew[csf("production_type")]=12;
				}
				if( $row_sew[csf("production_type")]!=5 ) $row_sew[csf("sewing_line")]=0;
				
				if( $row_sew[csf("production_type")]==5 )
				{
					if( $row_sew[csf("sewing_line")]==0 )
					{
						if($row_sew[csf("company_id")]==1) $row_sew[csf("sewing_line")]=995; //Asrotex
						else if($row_sew[csf("company_id")]==2) $row_sew[csf("sewing_line")]=996; //Ltd
						else //if($row_sew[csf("company_id")]==3)  
							$row_sew[csf("sewing_line")]=997; //Fariha
					}
					if( $row_sew[csf("company_id")]!=1)
					{
						if( $row_sew[csf("sewing_line")]<990 )
							$row_sew[csf("sewing_line")]=(int)$line_name_res[$row_sew[csf("sewing_line")]]; 
					}
					 if( $fariha_cluster[$row_sew[csf("sewing_line")]]!='')
					//if(in_array($row_sew[csf("sewing_line")], $fariha_cluster ))
						$row_sew[csf("sewing_line")]="991";
					if($unit_b[$row_sew[csf("sewing_line")]]!='')
						$row_sew[csf("sewing_line")]="992";
					if($gala[$row_sew[csf("sewing_line")]]!='')
						$row_sew[csf("sewing_line")]="993";
				}

				$production_qty[$newid_ar[$row_sew[csf("color_size_break_down_id")]]][$row_sew[csf("production_type")]][$row_sew[csf("sewing_line")]][qnty]+=$row_sew[csf("production_quantity")];  //[$row_sew[csf("floor_id")]]
				
				$production_qty[$newid_ar[$row_sew[csf("color_size_break_down_id")]]][$row_sew[csf("production_type")]][$row_sew[csf("sewing_line")]][pdate]=$row_sew[csf("production_date")]; 
				$production_qty[$newid_ar[$row_sew[csf("color_size_break_down_id")]]][$row_sew[csf("production_type")]][$row_sew[csf("sewing_line")]][col_size_id]=$row_sew[csf("color_size_break_down_id")];
			}
		}
		unset($prod_sql_res);
	 //echo "0**<pre>";
	//echo $i;
	// print_r($production_qty); die;

		foreach($production_qty as $oid=>$prodctn)
		{
			foreach($prodctn as $prod_typed=>$prodflr)
			{
				foreach($prodflr as $line=>$sdata)
				{
					$prod_typ=$gmt_prod_id_map[$prod_typed]*1;
					$sdate=date("d/m/Y",strtotime($sdata[pdate]));
					if($line==0) $nnline=""; else $nnline=$line;
					if( $prod_typ!=9 )  $nnline="";
				
					if( $sdata[qnty]>0 && $prod_typ>0)
						//$txt .=str_pad($oid,0," ")."\t".str_pad($sdate,0," ")."\t".str_pad($prod_typ,0," ")."\t".str_pad($line,0," ")."\t".str_pad($size_data,0," ")."\t".str_pad($sdata[qnty],0," ")."\r\n";
						$txt .=str_pad($oid,0," ")."\t".str_pad($sdate,0," ")."\t".str_pad($prod_typ,0," ")."\t".str_pad($nnline,0," ")."\t".str_pad($sdata[qnty],0," ")."\r\n";
				}
			}	
		}
		
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
		
		$po_breaks=array_chunk($po_break,999, true);
	     $poIds_cond_tna="";  $jk=0;
		foreach($po_breaks as $key=> $value)
		{
			if($jk==0)
			{
				//$poIds_cond_prod="and po_break_down_id in(".implode(",",$value).")"; 
				$poIds_cond_tna.=" po_number_id in(".implode(",",$value).")";
				//$poIds_cond_order.=" and a.id in(".implode(",",$value).")"; 
			}
			else
			{
				//$poIds_cond_prod.=" or po_break_down_id in(".implode(",",$value).")"; 
				$poIds_cond_tna.=" or po_number_id in(".implode(",",$value).")"; 
				//$poIds_cond_order.=" or a.id in(".implode(",",$value).")"; 
			}
			$jk++;
		}
		 
		 // TNA file
		$sql=sql_select ( "select a.po_number_id,a.job_no, a.actual_start_date, a.actual_finish_date,a.task_start_date,a.task_finish_date, task_number from tna_process_mst a where $poIds_cond_tna  and task_number in ( $tna_task_id_fr ) order by po_number_id,task_number" ); 
		
		$file_name="frfiles/EVENTS.TXT";
		$myfile = fopen( $file_name, "w" ) or die("Unable to open file!");
	 	
		//$txt .=str_pad("E.CODE",0," ")."\t".str_pad("E.TYPE",0," ")."\t".str_pad("E.EVENT",0," ")."\t".str_pad("E.OVERRIDEEXEC",0," ")."\t".str_pad("E.AD",0," ")."\t".str_pad("E.NOTE",0," ")."\t".str_pad("E.DONE",0," ")."\r\n";
		
		$txt .=str_pad("E.CODE",0," ")."\t".str_pad("E.TYPE",0," ")."\t".str_pad("E.EVENT",0," ")."\t".str_pad("E.OVERRIDEEXEC",0," ")."\t".str_pad("E.NOTE",0," ")."\t".str_pad("E.DONE",0," ")."\r\n";
		
		foreach($sql as $name)
		{
			if( $deleted_polist[$name[csf('po_number_id')]]=='' )
			{
				if( $name[csf('actual_finish_date')]=="0000-00-00"  || $name[csf('actual_finish_date')]=="")  $name[csf('actual_finish_date')]=""; else   $name[csf('actual_finish_date')]=date("d/m/Y",strtotime($name[csf('actual_finish_date')]));
				if( $name[csf('actual_start_date')]=="0000-00-00" || $name[csf('actual_start_date')]=="")  $name[csf('actual_start_date')]=""; else   $name[csf('actual_start_date')]=date("d/m/Y",strtotime($name[csf('actual_start_date')]));
				
				if( $name[csf('task_start_date')]=="0000-00-00" || $name[csf('task_start_date')]=="")  $name[csf('task_start_date')]=""; else   $name[csf('task_start_date')]=date("d/m/Y",strtotime($name[csf('task_start_date')]));
				if( $name[csf('task_finish_date')]=="0000-00-00" || $name[csf('task_finish_date')]=="")  $name[csf('task_finish_date')]=""; else   $name[csf('task_finish_date')]=date("d/m/Y",strtotime($name[csf('task_finish_date')]));
			
				$tna_po_name[$name[csf('po_number_id')]]=$name[csf('po_number_id')];
				$tna_po_task[$name[csf('po_number_id')]][$name[csf('task_number')]]['task_start_date']=$name[csf('task_start_date')];
				$tna_po_task[$name[csf('po_number_id')]][$name[csf('task_number')]]['actual_start_date']=$name[csf('actual_start_date')];
				
				$tna_po_task[$name[csf('po_number_id')]][$name[csf('task_number')]]['task_finish_date']=$name[csf('task_finish_date')];
				$tna_po_task[$name[csf('po_number_id')]][$name[csf('task_number')]]['actual_finish_date']=$name[csf('actual_finish_date')];
			}
		}
		unset( $sql );
		//print_r($str_po_list);
		//die;
		//intelo cut
	//	$tna_task_id_fr="10,12,31,32,50,51,52,60,61,62,63,70,71,72,73,80,101,110";
	//	$double_row=array(72=>72,61=>61,73=>73);
		foreach($tna_po_name as $poid )
		{
			foreach( $str_po_list[$poid] as $order=>$no ) //$str_po_list[$name[csf('po_break_down_id')]][$str_po]=$str_po;	
			{
				
				foreach($tna_po_task[$poid] as $taskids=>$names)
				{
					// print_r($names['job_no_mst']);
					//if( $mapped_tna_task[$task]=='' ) $mapped_tna_task[$task]=0;
					//if( $mapped_tna_task[$task]!='' )
					
					//$tna_task_id_fr="10,12,31,32,50,51,52,60,61,62,63,70,71,72,73,80,101,110";
					//if( $double_row[$task]=='' )
					//	$txt .=str_pad($order,0," ")."\tO\t".str_pad($task."01",0," ")."\t\t".str_pad($names['task_start_date'],0," ")."\t\t".str_pad($names['actual_start_date'],0," ")."\r\n";
					//else
					//{
						//$txt .=str_pad($order,0," ")."\tO\t".str_pad($taskids."01",0," ")."\t\t".str_pad($names['task_start_date'],0," ")."\t\t".str_pad($names['actual_start_date'],0," ")."\r\n";
						//$txt .=str_pad($order,0," ")."\tO\t".str_pad($taskids."02",0," ")."\t\t".str_pad($names['task_finish_date'],0," ")."\t\t".str_pad($names['actual_finish_date'],0," ")."\r\n";
						$txt .=str_pad($order,0," ")."\tO\t".str_pad($taskids."01",0," ")."\t\t\t".str_pad($names['actual_start_date'],0," ")."\r\n";
						$txt .=str_pad($order,0," ")."\tO\t".str_pad($taskids."02",0," ")."\t\t\t".str_pad($names['actual_finish_date'],0," ")."\r\n";
					//}
						
				}
			} //$tna_po_id[$name[csf('po_number_id')]]
		}
		fwrite( $myfile, $txt );
		fclose($myfile); 
		$txt=""; 
		
		
		
		$sql=sql_select("select id, master_tble_id, image_location from common_photo_library where is_deleted=0 and file_type=1 and form_name='knit_order_entry' $job_cond_img ");
		$file_name="frfiles/IMGATTACH.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
		$txt .="IMG.CODE\tIMG.FILENAME\tIMG.NAME\tIMG.FILEPATH\tIMG.DEFAULT\r\n";
		
		$zipimg = new ZipArchive();			// Load zip library	
		$filenames = str_replace(".sql",".zip",'frfiles/ImgFolders.sql');			// Zip name
		if($zipimg->open($filenames, ZIPARCHIVE::CREATE)!==TRUE)
		{		// Opening zip file to load files
			$error .=  "* Sorry ZIP creation failed at this time<br/>"; echo $error;
		}
		$imlocc="Z:\\" ;
		foreach($sql as $rows)
		{
			$name=explode("/",$rows[csf("image_location")]);
			foreach( $img_prod[$rows[csf("master_tble_id")]] as $job  )
			{
				
				$txt .=$job."\t".$name[1]."\t".str_replace(".jpg","",$name[1])."\t".$imlocc."\t1\r\n";
			}
			 //echo "0**../../../".$rows[csf("image_location")]; 
			$zipimg->addFile("".$rows[csf("image_location")]);
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
	//echo "window.open('frfiles/fr_files.zip','##');";
	//	$zip->addFile("frfiles/ImgFolders.zip");
	$zip->close();
	echo "0**d";
	exit();
//}

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
/*$fr_product_type=array(1 => "T-Shirt-Long Sleeve",
    2=>"T-Shirt-Short Sleeve",
    3=>"Polo Shirt-Long Sleeve",
    4=>"Polo Shirt-Short Sleeve",
    5=>"Tank Top",
    6=>"T-Shirt 3/4 ARM",
    7=>"Hoodies",
    8=>"Henley",
    9=>"T-Shirt-Sleeveless",
    10=>"Raglans",
    11=>"High Neck/Turtle Neck",
    12=>"Scarf",
    14=>"Blazer",
    15=>"Jacket",
    16=>"Night Wear",
    17=>"Marry Dress",
    18=>"Ladies Long Dress",
    19=>"Girls Dress",
    20=>"Full Pant",
    21=>"Short Pant",
    22=>"Trouser",
    23=>"Payjama",
    24=>"Romper Short Sleeve",
    25=>"Romper Long Sleeve",
    26=>"Romper Sleeveless",
    27=>"Romper",
    28=>"Legging",
    29=>"Three Quater",
    30=>"Skirts",
    31=>"Jump Suit",
    32=>"Cap",
    33=>"Tanktop Pyjama",
    34=>"Short Sleeve Pyjama",
    35=>"Jogging Pant",
    36=>"Bag",
    37=>"Bra",
    38=>"Underwear Bottom",
    39=>"Sweat Shirt",
    40=>"Singlet",
    41=>"Teens Singlet",
    42=>"Boxer",
    43=>"Stripe Boxer",
    44=>"Teens Boxer",
    45=>"Jersy Boxer",
    46=>"Panty",
    47=>"Slip Brief",
    48=>"Classic Brief",
    49=>"Short Brief",
    50=>"Mini Brief",
    51=>"Bikini",
    52=>"Lingerie",
    53=>"Bikers",
    54=>"Underwear",
    60=>"Plain Socks",
    61=>"Rib Socks",
    62=>"Jacuard/Patern Socks",
    63=>"Heavy Gauge Socks",
    64=>"Sports Socks",
    65=>"Terry Socks",
    66=>"Tight Socks",
    67=>"Sweat Pant",
    68=>"Sports Ware",
    69=>"Jogging Top",
    70=>"Long Pant",
    71=>"Pirates Pant",
    72=>"Bolero",
    73=>"Strap Top",
    74=>"Ladies Gypsy",
    75=>"Long Sleeve Body",
    76=>"Tank Top Body",
    77=>"Underwear Top",
    78=>"Whiper",
    79=>"Sleeping Bag",
    80=>"Romper Long Sleeve Boys",
    81=>"Romper Long Sleeve Girls",
    82=>"Romper Long Sleeve Unisex",
    83=>"Baby Bodies",
    84=>"TQ Pintuck Tee",
    85=>"LS Pintuck Tee",
    86=>"Twist Neck Pintuck",
    87=>"Maxi dress",
    88=>"Lace Gupsy",
    89=>"Gypsy Tie Neck Tee",
    90=>"V-neck Tunic",
    91=>"Roll Top Jogger",
    92=>"Soft Touch Jogger",
    93=>"Plaited Jogger",
    94=>"Loopback Jogger",
    95=>"Jegging",
    96=>"Mens Night Wear",
    97=>"Spots Shirt",
    98=>"Romper B",
    99=>"Inner Top",
    100=>"Outer Top",
    101=>"Mock Long Sleeve T- Shirt",
    102=>"Round Neck Long Sleeve T- Shirt",
    103=>"V Neck Long Sleeve T-Shirt",
    104=>"BLANKET",
    105=>"Playsuit",
    106=>"Sweater",
    107=>"Jumper",
    108=>"Cardigan",
    109=>"Nightware-Top",
    110=>"Nightware-Bottom",
    111=>"V Neck Short Sleeve T-Shirt",
    112=>"R Neck Short Sleeve T-Shirt",
    113=>"Mens Trunk",
    114=>"Mens Brief",
    115=>"Mens Boxer",
    116=>"Boys Boxer",
    117=>"Boys Brief",
    118=>"Girls Brief",
    119=>"Ladies Hipster",
    120=>"Ladies Thong",
    121=>"Mens Pattern Trunk",
    122=>"Mens Pattern Brief",
    123=>"Mens Pattern Boxer",
    124=>"T-Shirt-Long Sleeve-1",
    125=>"T-Shirt-Long Sleeve-2");*/
?>

