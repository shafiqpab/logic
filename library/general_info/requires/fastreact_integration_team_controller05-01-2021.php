<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
echo "0**";

$fr_product_type=return_library_array("select id,item_name from lib_garment_item where status_active=1 and is_deleted=0 order by item_name","id","item_name");
//$production_process=array(1=>"Cutting",2=>"Knitting",3=>"Dyeing",4=>"Finishing",5=>"Sewing",6=>"Fabric Printing",7=>"Washing",8=>"Gmts Printing",9=>"Embroidery",10=>"Iron",11=>"Gmts Finishing");

$production_process_freact=array(1=>"Yarn",2=>"Knitting",3=>"AOP",4=>"Cutting",5=>"Printing Send",6=>"Printing Received",7=>"Embroidery Send",8=>"Sweing In",9=>"Sewing Out",10=>"Wash Send",11=>"Iron Output",12=>"Gmts Finishing",13=>"Ex-Factory",14=>"Dyeing",17=>"Embroidery Received",18=>"Wash Received");


$gmt_prod_id_map[1]=10; //cut
//$gmt_prod_id_map[2]=6;
//$gmt_prod_id_map[3]=5;
$gmt_prod_id_map[4]=60; //Sewing Input
$gmt_prod_id_map[5]=70; //Sewing Output
$gmt_prod_id_map[7]=100; //Poly
//$gmt_prod_id_map[8]=12;
$gmt_prod_id_map[9]=20;  //Print Send
$gmt_prod_id_map[10]=30; //Print Rec
$gmt_prod_id_map[11]=40; //Emb Send
$gmt_prod_id_map[12]=80; //Wash Send
$gmt_prod_id_map[17]=50; //Emb Rec
$gmt_prod_id_map[18]=90; //Wash Rec
$gmt_prod_id_map[13]=110; //Ship

$line_name=return_library_array("select id,line_name from lib_sewing_line","id","line_name");
$companyArr=return_library_array("select id,company_name from lib_company","id","company_name");

if ( $action=="save_update_delete" )
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	foreach (glob("frfiles/"."*.*") as $filename)
	{			
		@unlink($filename);
	}
	//echo $received_date;
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
		$factMarchent_arr=return_library_array("select id, user_tag_id from lib_mkt_team_member_info","id","user_tag_id");
		$user_arr=return_library_array("select id,user_name from user_passwd","id","user_name");
		 
		// Customer File
		$sql=sql_select ( "select id, buyer_name, short_name from lib_buyer" );
		$file_name="frfiles/CUSTOMER_L.TXT";
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
			if(trim( $received_date)=="") $received_date="2019-07-01"; else $received_date=change_date_format($received_date, "yyyy-mm-dd", "-",1);
		}
		else if($db_type==2)
		{
			if(trim( $received_date)=="") $received_date=date('d-M-Y', strtotime("01-07-2019")); else $received_date=date("d-M-Y",strtotime($received_date));
		}
		// Products file Data
		//echo $received_date; die;
		
		
		if( $db_type==0) $shipment_date=" and b.shipment_date >= '$received_date'"; else if($db_type==2) $shipment_date=" and b.shipment_date >= '$received_date'";
		$shiping_status=" and c.shiping_status !=3";
		
		// $shipment_date=" and c.cutup_date >= '".date('d-M-Y', strtotime("01-10-2017"))."'";
		//	echo $shipment_date; die;
		$po_arr=array(); $ft_data_arr=array();
		 
		$sql_po="select a.id, a.company_name, a.job_no, a.style_ref_no, a.style_ref_no_prev, a.style_description, a.gmts_item_id, a.gmts_item_id_prev, a.buyer_name, a.order_uom, a.team_leader, a.bh_merchant, a.agent_name, a.season_buyer_wise as season, a.total_set_qnty, a.factory_marchant, a.garments_nature, b.id as bid, b.po_number, b.projected_po_id, b.is_confirmed, b.po_quantity, b.pub_shipment_date, b.shipment_date, b.po_received_date, (b.pub_shipment_date - b.po_received_date) as leadtime, b.status_active, b.po_number_prev, b.pub_shipment_date_prev, c.id as color_size_break_id, c.item_number_id, c.country_ship_date, c.country_id, c.cutup, c.color_mst_id, c.item_mst_id, c.color_number_id, c.size_number_id, c.plan_cut_qnty, c.order_quantity, c.order_total, c.shiping_status, c.country_ship_date_prev, c.color_number_id_prev, c.cutup_date
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id $shipment_date and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and c.is_deleted=0 order by a.id";
		//echo $sql_po;
		
		$sql_po_data=sql_select($sql_po); $product_size_arr=array(); $orders_arr=array(); $orders_data_arr=array(); $order_size_arr=array(); $prod_up_arr=array();
		foreach($sql_po_data as $porow)
		{
			$porow[csf('pub_shipment_date')]=$porow[csf('shipment_date')];
			$porow[csf('po_number')]=trim($porow[csf('po_number')]);
			$porow[csf('po_number_prev')]=trim($porow[csf('po_number_prev')]);
			
			$job_no=""; $po_number=""; $pub_ship_date=""; $item_no_id=''; $color_id=''; $size_id=''; $color_size_break_id="";
			$job_no=$porow[csf('job_no')];
			$po_number=$porow[csf('po_number')];
			$pub_ship_date=$porow[csf('pub_shipment_date')];
			$item_no_id=$porow[csf('item_number_id')];
			$color_id=$porow[csf('color_number_id')]; 
			$size_id=$porow[csf('size_number_id')];
			$color_size_break_id=$porow[csf('color_size_break_id')];
			if($porow[csf('shiping_status')]!=3)
			{
				$po_break[$porow[csf('bid')]]=$porow[csf('bid')];
				$po_arr[col_po_id][$color_size_break_id]=$color_size_break_id;
			}
			
			$po_arr[po_id][$porow[csf('id')]]=$porow[csf('id')];
			$po_arr[job_no][$job_no]="'".$job_no."'";
			
			$ft_data_arr[$job_no][agent_name]=$buyer_name_array[$porow[csf('agent_name')]];//P.CODE
			$ft_data_arr[$job_no][job_no]=$job_no;//P.CODE
			$ft_data_arr[$job_no][company]=$companyArr[$porow[csf('company_name')]];//P.CODE
			$ft_data_arr[$job_no][gmts_item_id]=$porow[csf('gmts_item_id')];//P.TYPE
			$ft_data_arr[$job_no][style_ref_no]=$porow[csf('style_ref_no')];//P.DESCRIP
			$ft_data_arr[$job_no][style_description]=$porow[csf('style_description')];//P.DESCRIP
			$ft_data_arr[$job_no][buyer_name]=$porow[csf('buyer_name')];
			$ft_data_arr[$job_no][team_leader]=$porow[csf('team_leader')];
			$ft_data_arr[$job_no][bh_merchant]=$porow[csf('bh_merchant')];
			$ft_data_arr[$job_no][po_quantity]=$porow[csf('po_quantity')];
			$ft_data_arr[$job_no][order_uom]=$porow[csf('order_uom')];
			$ft_data_arr[$job_no][season]=$season_arr[$porow[csf('season')]];
			$garmentsNature='';
			if($porow[csf('garments_nature')]==2) $garmentsNature='Knit'; else if($porow[csf('garments_nature')]==3) $garmentsNature='Woven'; else if($porow[csf('garments_nature')]==100) $garmentsNature='Sweater';
			$ft_data_arr[$job_no][garments_nature]=$garmentsNature;
			$ft_data_arr[$job_no][user]=$user_arr[$factMarchent_arr[$porow[csf('factory_marchant')]]];
			$ft_data_arr[$job_no][team]=$user_arr[$factMarchent_arr[$porow[csf('team_leader')]]];
			$ft_data_arr[$job_no][leadtime]=$porow[csf('leadtime')];
			
			
			$ft_data_arr[$job_no][old_gmts_item_id]=$porow[csf('gmts_item_id_prev')];//P.TYPE
			$ft_data_arr[$job_no][old_style_ref_no]=$porow[csf('style_ref_no_prev')];//P.DESCRIP
			
			
			
			$ft_data_arr[$job_no][cutting]=1;//P^WC:10
			$ft_data_arr[$job_no][sew_input]=1;//P^WC:35
			$ft_data_arr[$job_no][poly]=1;//P^WC:160
			
			$product_size_arr[$job_no][$size_lib[$size_id]]=$size_lib[$size_id];//P.CODE and size for product size file
			
			$po_str=$porow[csf('bid')].'_'.$pub_ship_date.'_'.$porow[csf('is_confirmed')].'_'.$porow[csf('status_active')].'_'.$porow[csf('projected_po_id')];
			
			$orders_arr[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id]['plan_cut']+=$porow[csf('plan_cut_qnty')];
			$orders_arr[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id]['order_qty']+=$porow[csf('order_quantity')];
			$orders_arr[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id]['order_value']+=$porow[csf('order_total')];
			$orders_arr[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id]['color_size_break_id'].=trim($color_size_break_id).'**'.$porow[csf('shiping_status')].',';
			$orders_arr[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id]['bid'].=$porow[csf('bid')].",";
			$orders_arr[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id]['order_uom']=$porow[csf('order_uom')];
			$orders_arr[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id]['porec_date']=$porow[csf('po_received_date')];
			$orders_arr[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id]['po_str']=$po_str;
			$orders_arr[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id]['icmstid']=$porow[csf('item_mst_id')].'_'.$porow[csf('color_mst_id')];
			
			$orders_arr[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id]['old_po_number']=$porow[csf('po_number_prev')];
			$orders_arr[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id]['old_color_number']=$porow[csf('color_number_id_prev')];
			$orders_arr[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id]['old_pub_shipdate']=$porow[csf('pub_shipment_date_prev')];
			$orders_arr[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id]['old_country_shipdate']=$porow[csf('country_ship_date_prev')];
			$order_size_array[$job_no][$po_number][$item_no_id][$color_id][$pub_ship_date][$size_id]+=$porow[csf('order_quantity')];
			
			$new_color_size[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id][$color_size_break_id]=$color_size_break_id;
			
			$orders_data_arr[$porow[csf('bid')]]['po_no']=$po_number;
			$orders_data_arr[$porow[csf('bid')]]['season']=$porow[csf('season')];
			
			$order_size_arr[$po_number][$job_no][$item_no_id][$color_id][$size_id]+=$porow[csf('order_quantity')];
			$prod_up_arr[$color_size_break_id]=$size_id;
			$prod_up_arr_powise[$color_size_break_id]=$porow[csf('bid')];
		}
		unset($sql_po_data);
		
		$po_string= implode(",",$po_arr[po_id]);
		$job_string= implode(",",$po_arr[job_no]);
		
		$con=connect();
		execute_query("delete from tmp_poid",0);
		oci_commit($con);
		foreach($po_break as $ids)
		{
			//echo "insert into tmp_poid (userid, poid) values (".$_SESSION['logic_erp']['user_id'].",$ids)"; 
			execute_query("insert into tmp_poid (userid, poid) values (".$_SESSION['logic_erp']['user_id'].",$ids)");
		}
		oci_commit($con);
		
		execute_query("delete from tmp_col_po_id",0);
		oci_commit($con);
		foreach($po_arr[col_po_id] as $ids)
		{
			//echo "insert into tmp_poid (userid, poid) values (".$_SESSION['logic_erp']['user_id'].",$ids)"; 
			execute_query("insert into tmp_col_po_id (userid, col_po_id) values (".$_SESSION['logic_erp']['user_id'].",$ids)");
		}
		oci_commit($con);/*		echo "test";*/
		disconnect($con);
				
	    $job=array_chunk($po_arr[job_no],999, true);
	    $job_cond_in=""; $job_cond_img=''; $job_cond_ind=""; $cmJobCond=""; $ji=0;
		foreach($job as $key=> $value)
		{
			if($ji==0)
			{
				$cmJobCond="and job_no in(".implode(",",$value).")"; 
				$job_cond_in="job_no in(".implode(",",$value).")"; 
				$job_cond_in_mst.=" a.job_no_mst in(".implode(",",$value).")"; 
				$job_cond_img.=" and master_tble_id in(".implode(",",$value).")";
				$job_cond_ind.=" and d.job_no in(".implode(",",$value).")"; 
			}
			else
			{
				$cmJobCond.=" or job_no in(".implode(",",$value).")"; 
				$job_cond_in.=" or job_no in(".implode(",",$value).")"; 
				$job_cond_in_mst.=" or a.job_no_mst in(".implode(",",$value).")"; 
				$job_cond_img.=" or master_tble_id in(".implode(",",$value).")";
				$job_cond_ind.=" or d.job_no in(".implode(",",$value).")";  
			}
			$ji++;
		}
		//print_r($job_cond_in); die;
		
		$cmArr=return_library_array("select job_no, cm_cost from wo_pre_cost_dtls where is_deleted=0 and status_active=1 $cmJobCond","job_no","cm_cost");
		
		$sqlBomDtls="select d.job_no, d.sew_effi_percent, d.costing_per, d.sew_smv, e.fab_knit_req_kg from wo_pre_cost_mst d, wo_pre_cost_sum_dtls e where 1=1 $job_cond_ind and d.job_no=e.job_no and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 ";
		 
		// $product_size_arr=array(); $orders_arr=array(); $orders_data_arr=array(); $order_size_arr=array(); $prod_up_arr=array();
		$sqlBomDtlsData=sql_select($sqlBomDtls);
		//print_r($sqlBomDtlsData);
		foreach($sqlBomDtlsData as $bomrow)
		{
			$fab_knit_req_kg=0;
			if($bomrow[csf('costing_per')]==1) $fab_knit_req_kg=12;
			else if($bomrow[csf('costing_per')]==2) $fab_knit_req_kg=1;
			else if($bomrow[csf('costing_per')]==3) $fab_knit_req_kg=24;
			else if($bomrow[csf('costing_per')]==4) $fab_knit_req_kg=36;
			else if($bomrow[csf('costing_per')]==5) $fab_knit_req_kg=48;
			
			$ft_data_arr[$bomrow[csf('job_no')]][fab_knit_req_kg]=number_format($bomrow[csf('fab_knit_req_kg')]/$fab_knit_req_kg,3);//P^CF:5
			$ft_data_arr[$bomrow[csf('job_no')]][shiped]=$bomrow[csf('sew_smv')];//P^WC:165
			$ft_data_arr[$bomrow[csf('job_no')]][sew_output]=$bomrow[csf('sew_smv')];//P^WC:140
			$ft_data_arr[$bomrow[csf('job_no')]][sew_eff]=$bomrow[csf('sew_effi_percent')];
			
			$ft_data_arr[$bomrow[csf('job_no')]][cmpcs]=$cmArr[$bomrow[csf('job_no')]]/$fab_knit_req_kg;
		}
		unset($sqlBomDtlsData);
		//echo $ft_data_arr['GKD-20-00630'][cmpcs]; die;
		   
		$sql_fabric_prod=sql_select("select job_no, color_type_id, gsm_weight from wo_pre_cost_fabric_cost_dtls where $job_cond_in and fabric_source=1 and avg_cons>0 and status_active=1 and is_deleted=0");
		foreach($sql_fabric_prod as $row_fabric_prod)
		{
			$ft_data_arr[fab_delivery][$row_fabric_prod[csf('job_no')]]=1;//P^WC:5 
			if($row_fabric_prod[csf('color_type_id')]==5) $ft_data_arr[aop][$row_fabric_prod[csf('job_no')]]=1;
			else $ft_data_arr[aop][$row_fabric_prod[csf('job_no')]]=0;
			
			$ft_data_arr[$row_fabric_prod[csf('job_no')]][gsm]=$row_fabric_prod[csf('gsm_weight')].',';
		}
		unset($sql_fabric_prod);
		$sql_print_embroid=sql_select("select min(id) as id, job_no, emb_name, avg(cons_dzn_gmts) as cons_dzn_gmts from wo_pre_cost_embe_cost_dtls where $job_cond_in and emb_name in(1,2,3) and cons_dzn_gmts>0  and status_active=1 and is_deleted=0 group by job_no, emb_name");
		foreach($sql_print_embroid as $row_print_embroid)
		{
			if($row_print_embroid[csf('emb_name')]==1)
			{
				$ft_data_arr[$row_print_embroid[csf('job_no')]][dv_printing]=1; //P^WC:20
				$ft_data_arr[$row_print_embroid[csf('job_no')]][rv_printing]=1; //P^WC:30
			}
			else
			{ 
				$ft_data_arr[$row_print_embroid[csf('job_no')]][dv_printing]=0; //P^WC:20
				$ft_data_arr[$row_print_embroid[csf('job_no')]][rv_printing]=0; //P^WC:30
			}
			if($row_print_embroid[csf('emb_name')]==2)
			{
				$ft_data_arr[$row_print_embroid[csf('job_no')]][dv_embrodi]=1; //P^WC:40
				$ft_data_arr[$row_print_embroid[csf('job_no')]][rv_embrodi]=1; //P^WC:50
			}
			else 
			{
				$ft_data_arr[$row_print_embroid[csf('job_no')]][dv_embrodi]=0; //P^WC:40
				$ft_data_arr[$row_print_embroid[csf('job_no')]][rv_embrodi]=0; //P^WC:50
			}
			if($row_print_embroid[csf('emb_name')]==3)
			{
				$ft_data_arr[$row_print_embroid[csf('job_no')]][dv_wash]=1; //P^WC:80
				$ft_data_arr[$row_print_embroid[csf('job_no')]][rv_wash]=1; //P^WC:90
			}
			else
			{
				$ft_data_arr[$row_print_embroid[csf('job_no')]][dv_wash]=0; //P^WC:80
				$ft_data_arr[$row_print_embroid[csf('job_no')]][rv_wash]=0; //P^WC:90
			}
		}
		
		unset($sql_print_embroid);
		//=================================Item wise Array Srart=====================================
		$arr_itemsmv=array();
		$sql_itemsmv=sql_select("select job_no, gmts_item_id, set_item_ratio, smv_pcs_precost, smv_set_precost, smv_pcs, embelishment from wo_po_details_mas_set_details where $job_cond_in");
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
		$file_name="frfiles/PRODUCTS_L.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
	 	
		//$txt .=str_pad("P.CODE",0," ")."\t".str_pad("P.OLDCODE",0," ")."\t".str_pad("P.DESCRIP",0," ")."\t".str_pad("P.TYPE",0," ")."\t".str_pad("P.CUST",0," ")."\t".str_pad("P^WC:10",0," ")."\t".str_pad("P^WC:20",0," ")."\t".str_pad("P^WC:30",0," ")."\t".str_pad("P^WC:40",0," ")."\t".str_pad("P^WC:50",0," ")."\t".str_pad("P^WC:60",0," ")."\t".str_pad("P^WC:70",0," ")."\t".str_pad("P^WC:80",0," ")."\t".str_pad("P^WC:90",0," ")."\t".str_pad("P^WC:100",0," ")."\t".str_pad("P^WC:110",0," ")."\r\n";
		$txt .=str_pad("P.CODE",0," ")."\t".str_pad("P.DESCRIP",0," ")."\t".str_pad("P.TYPE",0," ")."\t".str_pad("P.CUST",0," ")."\t".str_pad("P^WC:20",0," ")."\t".str_pad("P^WC:30",0," ")."\t".str_pad("P^WC:40",0," ")."\t".str_pad("P^WC:50",0," ")."\t".str_pad("P^WC:70",0," ")."\t".str_pad("P^WC:80",0," ")."\t".str_pad("P^WC:90",0," ")."\r\n";
		//echo $txt; die;
		
		foreach($ft_data_arr as $rows)
		{
			$item_chaned=0;
			$gitem=explode(",",$rows[gmts_item_id]);
			$old_gitem=explode(",", trim($rows[old_gmts_item_id]));
			$changgid=array_diff($gitem,$old_gitem);
			if(count($changgid)>0) $item_chaned=1;
			if(trim($rows[old_gmts_item_id])=='')$item_chaned=0;
			
			$dt=""; $aop=0; $inc=0;
			$style_ref_arr[$rows[job_no]]=$rows[style_ref_no];
			$job_ref_arr[$rows[job_no]]=$rows[job_no];
			//$job_ref_arr[$rows[job_no]]=$rows[job_no];
			$aop=$ft_data_arr[aop][$rows[job_no]];
			if(count($gitem)>1)
			{
				$item_array[$rows[job_no]]=1;
				foreach( $gitem as $id )
				{
					$emb_req=''; $smv='';
					$smv=$arr_itemsmv[$rows[job_no]][$id]['smv'];
					$emb_req=$arr_itemsmv[$rows[job_no]][$id]['emb'];
					$img_prod[$rows[job_no]][]=$rows[style_ref_no]."::".$rows[season]."::".$fr_product_type[$id];
					
					//$ft_data_arr[$porow[csf('job_no')]][old_gmts_item_id]=$porow[csf('gmts_item_id_prev')];//P.TYPE
					//$ft_data_arr[$porow[csf('job_no')]][old_style_ref_no]
					$style_changed=0;
					$old_code='';
					if(trim($rows[old_style_ref_no])!='')
					{
						if(trim($rows[old_style_ref_no])!=trim($rows[style_ref_no]))
						{
							$old_code=$rows[old_style_ref_no]."::".$rows[season]."::".$fr_product_type[$id];
							$style_changed=1;
						}
					}
					if($item_chaned==1)
					{
						if($style_changed==1) 
							$old_code=$rows[old_style_ref_no]."::".$rows[season]."::".$fr_product_type[$old_gitem[$inc]];
						else
							$old_code=$rows[style_ref_no]."::".$rows[season]."::".$fr_product_type[$old_gitem[$inc]];
							
						$new_item_index[$rows[job_no]][$id]=$old_gitem[$inc];
						$ft_data_arr[$rows[job_no]][old_gmts_item_id_chaged]=1;
					}
				 
					$inc++;
					
					if(trim($rows[job_no])!="" && ($smv*1)>0) $txt .=str_pad($rows[style_ref_no]."::".$rows[season]."::".$fr_product_type[$id],0," ")."\t".str_pad($rows[style_description],0," ")."\t".str_pad($fr_product_type[$id],0," ")."\t".str_pad($buyer_name_array[$rows[buyer_name]],0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][dv_printing],0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][rv_printing],0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][dv_embrodi],0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][rv_embrodi],0," ")."\t".str_pad($smv,0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][dv_wash],0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][rv_wash],0," ")."\r\n";
				}
			}
			else
			{
				$old_code='';
				if(trim($rows[old_style_ref_no])!='')
				{
					if(trim($rows[old_style_ref_no])!=trim($rows[style_ref_no]))
					{
						$old_code=$rows[old_style_ref_no]."::".$rows[season];
					}
				}
				$order_uom=$ft_data_arr[$rows[job_no]][order_uom]; $add_item="";
				if($order_uom==58) $add_item="::".$fr_product_type[$rows[gmts_item_id]];
				$smv=$arr_itemsmv[$rows[job_no]][$rows[gmts_item_id]]['smv'];
				$img_prod[$rows[job_no]][]=$rows[style_ref_no]."::".$rows[season];
				if(trim($rows[job_no])!="" && ($smv*1)>0) $txt .=str_pad($rows[style_ref_no]."::".$rows[season].$add_item,0," ")."\t".str_pad($rows[style_description],0," ")."\t".str_pad($fr_product_type[$rows[gmts_item_id]],0," ")."\t".str_pad($buyer_name_array[$rows[buyer_name]],0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][dv_printing],0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][rv_printing],0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][dv_embrodi],0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][rv_embrodi],0," ")."\t".str_pad($smv,0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][dv_wash],0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][rv_wash],0," ")."\r\n";
			}
		}
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
	  	//die;
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
		
		$file_name="frfiles/ORDERS_L.TXT";
		$myfile = fopen( $file_name, "w" ) or die("Unable to open file!");
		
		$sqlOldcode="select job_no, po_id, color_mst_id, item_mst_id, shipdate, new_code from fr_temp_code where user_id=".$_SESSION['logic_erp']['user_id']."";
		$nameArray=sql_select($sqlOldcode); $oldCodeArr=array();
		foreach($nameArray as $orow)
		{
			$oldCodeArr[$orow[csf("job_no")]][$orow[csf("po_id")]][$orow[csf("color_mst_id")]][$orow[csf("item_mst_id")]]=$orow[csf("new_code")];
		}
		unset($nameArray);
		
		$txt .=str_pad("O.CODE",0," ")."\t".str_pad("O.OLDCODE",0," ")."\t".str_pad("O.PROD",0," ")."\t".str_pad("O.CUST",0," ")."\t".str_pad("O^DD:1",0," ")."\t".str_pad("O^DQ:1",0," ")."\t".str_pad("O.CONTRACT_QTY",0," ")."\t".str_pad("O.STATUS",0," ")."\t".str_pad("O.SPRICE",0," ")."\t".str_pad("O.SCOST",0," ")."\t".str_pad("O.MCOST",0," ")."\t".str_pad("O.UDBUYER_STYLE_QTY",0," ")."\t".str_pad("O.UDFACTORY",0," ")."\t".str_pad("O.UDGSM",0," ")."\t".str_pad("O.MERCHANDISER",0," ")."\t".str_pad("O.UDPRE_EFFICIENCY",0," ")."\t".str_pad("O.UDLEAD TIME",0," ")."\t".str_pad("O.UDSEASON",0," ")."\t".str_pad("O.EVBASE",0," ")."\r\n";
		$order_size=array(); $tmpNewCodeArr=array(); $projectedCodeArr=array(); $confirmCodeArr=array();
		foreach($orders_arr as $jobno=>$po_data)
		{
			foreach($po_data as $po_st=>$ship_item_data)
			{
				foreach($ship_item_data as $ship_date=>$item_data)
				 {
					foreach($item_data as $item_id=>$color_data)
					{
						foreach($color_data as $color_id=>$other_val)
						{
							if($other_val['order_qty']>1)
							{
								$ex_po=explode("_",$other_val['po_str']);
								$po_id=implode(",",array_filter(array_unique(explode(",",$other_val['bid'])))); 
								$shipdate=date("d/m/Y",strtotime($ship_date));
								$is_confirm=$ex_po[2];
								$is_deleted=$ex_po[3];
								$projected_po_id=$ex_po[4];
								
								$col_sizebreak_id_str=""; $shiping_status_str="";
								$ex_other_data=explode(",",$other_val['color_size_break_id']);
								 
								if($is_confirm==1) $str="F"; else $str="P";
								$po_no=$po_st;
								$old_str_po=''; $str_po=''; $changed=0; $ssts="0"; $old_item_code=''; $buyer_style=''; 
								if( $other_val['order_uom']!=1 ) 
								{
									if( $ft_data_arr[$jobno][old_gmts_item_id_chaged]==1)
									{
										$changed=1;
										$old_item_code="::".$fr_product_type[$new_item_index[$jobno][$item_id]];
									}
									else
										$old_item_code="::".$fr_product_type[$item_id];
										
									if($fr_product_type[$item_id]!="") $str_item="::".$fr_product_type[$item_id]; else $str_item="";
									$buyer_style=$ft_data_arr[$jobno][style_ref_no]."::".$ft_data_arr[$jobno][season]."::".$fr_product_type[$item_id];
								}
								else
								{
									$str_item=""; 
									$buyer_style=$ft_data_arr[$jobno][style_ref_no]."::".$ft_data_arr[$jobno][season];
								}
								
								if($is_confirm==2) $str_job="::".$jobno; else $str_job="";
								
								$icmstid=""; $oldCode="";
								$job_wise_uom[$jobno]=$other_val['order_uom'];
								$icmstid=$other_val['icmstid'];
								$exicid=explode("_",$icmstid);
								
								$itemMstId=$exicid[0];
								$colorMstId=$exicid[1];
								
								$oldCode=$oldCodeArr[$jobno][$po_id][$colorMstId][$itemMstId];
								
								//$str_po=$po_no."::".$jobno."::".$color_lib[$color_id]."".$str_item."::".date("d-m-Y",strtotime($shipdate));  
								//$str_po=$jobno."::".$po_no."::".$color_lib[$color_id]."::".date("ymd",strtotime($shipdate))."".$str_item; 
								$str_po=$ft_data_arr[$jobno][style_ref_no]."::".$po_no."::".$color_lib[$color_id]."::".$jobno."::".$shipdate."".$str_item."".$str_job;
								
								$tmpNewCodeArr[$str_po]=$jobno."__".$po_id."__".$itemMstId."__".$colorMstId."__".$shipdate."__".$oldCode; 
								if($is_confirm==2)
								{
									$projectedCodeArr[$po_id][$itemMstId][$colorMstId]=$str_po;
								}
								
								if($is_confirm==1)
								{
									$confirmCodeArr[$str_po]=$po_id."__".$projected_po_id."__".$itemMstId."__".$colorMstId;
								}
								 
								
								/*if( $ft_data_arr[$jobno][old_style_ref_no]!='' && $ft_data_arr[$jobno][old_style_ref_no]!=$ft_data_arr[$jobno][style_ref_no])
								{
									$old_style=$ft_data_arr[$jobno][old_style_ref_no];
									$changed=1;
								}
								else $old_style=$ft_data_arr[$jobno][style_ref_no];
								
								if( $other_val['old_po_number']!='' && $other_val['old_po_number']!=$po_no)
								{
									$old_po= $other_val['old_po_number'];
									$changed=1;
								}
								else $old_po= $po_no;
								
								if( $other_val['old_color_number']!='' && $other_val['old_color_number']!=$color_id)
								{
									$old_color= $other_val['old_color_number'];
									$changed=1;
								}
								else $old_color= $color_id;
								
								if( $other_val['old_pub_shipdate']!='' && date("d/m/Y",strtotime($other_val['old_pub_shipdate']))!=$shipdate)
								{
									$old_pub_ship= date("d/m/Y",strtotime($other_val['old_pub_shipdate']));
									$changed=1;
								}
								else $old_pub_ship=$shipdate;
								
								if($changed==1)
									$old_str_po=$old_style."::".$old_po."::".$color_lib[$old_color]."::".$jobno."::".$old_pub_ship."".$old_item_code; */
									//$old_str_po=$jobno."::".$old_po."::".$color_lib[$old_color]."::".$old_pub_ship."".$old_item_code; 
								
								foreach($new_color_size[$jobno][$po_st][$ship_date][$item_id][$color_id] as $cids)
								{
									$newid_ar[trim($cids)]=$str_po;
								}
								//color_size_break_id
								foreach($ex_other_data as $val_color_size_id)
								{
									$ex_color_size_break_id_val=explode("**",$val_color_size_id);
									$tmp_col_size[$ex_color_size_break_id_val[0]]=$ex_color_size_break_id_val[1];
									if( $ex_color_size_break_id_val[1]==3) $ssts="1"; 
								}
								$tmppoo=implode("",array_unique(explode(",",$po_id)));
								if( $is_deleted==2 ||  $is_deleted==3  ) 
								{ 
									$str="X"; 
									$deleted_po_list[$tmppoo]=$tmppoo; 
								}  //$ssts=0;
								
								$fob=0; $fob=$other_val['order_value']/$other_val['order_qty'];
								$udbuyer_style_qty=''; $udbuyer_style_qty=$buyer_name_array[$ft_data_arr[$jobno][buyer_name]].'_'.$ft_data_arr[$jobno][style_ref_no].'_'.$other_val['order_qty'];
								$fabgsm=implode(",",array_filter(array_unique(explode(",",$ft_data_arr[$jobno][gsm]))));
								
								$cmpcs=0;
								$cmpcs=$ft_data_arr[$jobno][cmpcs];
								
								if($oldCode=="") $oldCode=$str_po;
								
								
								$txt .=str_pad($str_po,0," ")."\t".str_pad($oldCode,0," ")."\t".str_pad($buyer_style,0," ")."\t".str_pad($buyer_name_array[$ft_data_arr[$jobno][buyer_name]],0," ")."\t".str_pad(' '.$shipdate,0," ")."\t".str_pad($other_val['plan_cut'],0," ")."\t".str_pad($other_val['order_qty'],0," ")."\t".str_pad($str,0," ")."\t".str_pad(number_format($fob, 4, '.', ''),0," ")."\t".str_pad($cmpcs,0," ")."\t".str_pad($cmpcs,0," ")."\t".str_pad($udbuyer_style_qty,0," ")."\t".str_pad($ft_data_arr[$jobno][company],0," ")."\t".str_pad($fabgsm,0," ")."\t".str_pad($ft_data_arr[$jobno][team],0," ")."\t".str_pad($ft_data_arr[$jobno][sew_eff],0," ")."\t".str_pad($ft_data_arr[$jobno][leadtime],0," ")."\t".str_pad($ft_data_arr[$jobno][season],0," ")."\t".str_pad(' '.date("d/m/Y",strtotime($other_val['porec_date'])),0," ")."\r\n";
							}
						}
					}
				}
			}
		}
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
		
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
			$shipdate=$exstrVal[4];
			$oldCode=$exstrVal[5];
			
			execute_query("insert into fr_temp_code (job_no, po_id, color_mst_id, item_mst_id, shipdate, new_code, old_code, user_id) values ('".$jobno."','".$po_id."','".$itemMstId."','".$colorMstId."','".$shipdate."','".$ncode."','".$oldCode."',".$_SESSION['logic_erp']['user_id'].")");
		}
		disconnect($con);
		oci_commit($con);
		//die;
		
		
		$file_name="frfiles/RELATE_L.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");

		$txt .=str_pad("O.CODE",0," ")."\t".str_pad("O.HOST_ORDER",0," ")."\t".str_pad("END",0," ")."\r\n";
		foreach($confirmCodeArr as $cstr=>$condate)
		{
			$excdata=explode("__",$condate);
			$po_id=$proj_po_id=$itemMstId=$colorMstId="";
			$po_id=$excdata[0];
			$proj_po_id=$excdata[1];
			$itemMstId=$excdata[2];
			$colorMstId=$excdata[3];
			
			$projCode="";
			
			$projCode=$projectedCodeArr[$proj_po_id][$itemMstId][$colorMstId];
			
			if($cstr!="")
				$txt .=str_pad($cstr,0," ")."\t".str_pad($projCode,0," ")."\t END\r\n";
		}
		
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
		
		// Production file
		$prod_sql="SELECT a.po_break_down_id, a.country_id, a.item_number_id, a.floor_id, a.sewing_line, a.production_type, a.production_date as production_date, b.color_size_break_down_id, b.production_qnty AS production_quantity, a.embel_name, a.status_active, a.is_deleted from pro_garments_production_mst a, pro_garments_production_dtls b, tmp_col_po_id c where a.production_type in (1,2,3,4,5,11) and a.id=b.mst_id and b.color_size_break_down_id=c.col_po_id and c.userid='$user_id' order by production_date ASC,b.color_size_break_down_id asc"; //$poIds_cond_prod tmp_poid (userid, poid)  and a.embel_name in (1,2,3,4,5)  and a.is_deleted=0 and a.status_active=1 
		//echo $prod_sql; die;
	//	print_r($deleted_po_list);
		$prod_sql_res=sql_select($prod_sql);
		foreach($prod_sql_res as $row_sew)
		{
			if($deleted_po_list[$row_sew[csf("po_break_down_id")]]!='')
				$deleted_colors[$row_sew[csf("color_size_break_down_id")]]=$row_sew[csf("color_size_break_down_id")];
				
			//echo $newid_ar[$row_sew[csf("color_size_break_down_id")]].'='.$prod_up_arr_powise[$row_sew[csf('color_size_break_down_id')]].'='.$row_sew[csf("po_break_down_id")].'='.$deleted_po_list[$row_sew[csf("po_break_down_id")]].'<br>';
			
			
			if($newid_ar[$row_sew[csf("color_size_break_down_id")]]!='' && $prod_up_arr_powise[$row_sew[csf('color_size_break_down_id')]]==$row_sew[csf("po_break_down_id")] && $deleted_po_list[$row_sew[csf("po_break_down_id")]]=='')
			{
				if($row_sew[csf("production_type")]==3)//issue
				{
					if($row_sew[csf("embel_name")]==1) $row_sew[csf("production_type")]=10;
					else if($row_sew[csf("embel_name")]==2) $row_sew[csf("production_type")]=17;
					else if( $row_sew[csf("embel_name")]==3 ) $row_sew[csf("production_type")]=18;
				}
				else if($row_sew[csf("production_type")]==2) // send emb
				{
					if($row_sew[csf("embel_name")]==1) $row_sew[csf("production_type")]=9;
					else if($row_sew[csf("embel_name")]==2) $row_sew[csf("production_type")]=11;
					else if( $row_sew[csf("embel_name")]==3 ) $row_sew[csf("production_type")]=12;
				}
				if($row_sew[csf("production_type")]==11) $row_sew[csf("production_type")]=7;
				
				if( $row_sew[csf("status_active")]==0 && $row_sew[csf("is_deleted")]==1 ) $row_sew[csf("production_quantity")]=0;
				
				$row_sew[csf("sewing_line")]=(int)$line_name_res[$row_sew[csf("sewing_line")]]; 
				
				$prod_typ=$row_sew[csf("production_type")];
				if( $prod_typ!=5 ) $row_sew[csf("sewing_line")]=0;
				//if( $prod_typ==8 ||$prod_typ==1 ||$prod_typ==9 ||$prod_typ==10 ||$prod_typ==4 ) $row_sew[csf("sewing_line")]=0;
				$exfdate=$row_sew[csf("production_date")];
				
				$production_qty[$newid_ar[$row_sew[csf("color_size_break_down_id")]]][$row_sew[csf("production_type")]][$row_sew[csf("sewing_line")]]['qnty']+=$row_sew[csf("production_quantity")];
				
				$production_qty[$newid_ar[$row_sew[csf("color_size_break_down_id")]]][$row_sew[csf("production_type")]][$row_sew[csf("sewing_line")]]['pdate']=$exfdate; 
				$production_qty[$newid_ar[$row_sew[csf("color_size_break_down_id")]]][$row_sew[csf("production_type")]][$row_sew[csf("sewing_line")]]['col_size_id']=$row_sew[csf("color_size_break_down_id")]; 
			}
		}
		unset($prod_sql_res);
		/*echo"<pre>";
		print_r($production_qty);*/ //die;
	
		$prod_sql="SELECT a.mst_id, a.color_size_break_down_id, a.production_qnty, b.ex_factory_date, b.shiping_status from pro_ex_factory_dtls a, pro_ex_factory_mst b, tmp_col_po_id c where a.color_size_break_down_id=c.col_po_id and b.id=a.mst_id and a.is_deleted=0 and a.status_active=1 and a.mst_id=b.id and b.is_deleted=0 and b.status_active=1 and c.userid='$user_id' ";
		//echo $prod_sql; die;
		$prod_sql_res=sql_select($prod_sql); $exFactoryArr=array();
		foreach($prod_sql_res as $row_sew)
		{
			if($newid_ar[$row_sew[csf("color_size_break_down_id")]]!='' && $deleted_colors[$row_sew[csf("color_size_break_down_id")]]=='') 
			{
				$row_sew[csf("production_type")]=13;
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
		$file_name="frfiles/UPDNORM_L.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");

		$txt .=str_pad("U.ORDER",0," ")."\t".str_pad("U.DATE",0," ")."\t".str_pad("U.OPERATION",0," ")."\t".str_pad("U.LINE_EXTERNAL_ID",0," ")."\t".str_pad("U.QTY",0," ")."\t".str_pad("U.OPN_COMPLETE",0," ")."\r\n";
		foreach($production_qty as $oid=>$prodctn)
		{
			foreach($prodctn as $prod_typ=>$prodflr)
			{
				foreach($prodflr as $line=>$sdata)
				{
					//if($prod_typ==5) { print_r($sdata); echo $line."="; }
					$prod_type_n=$gmt_prod_id_map[$prod_typ]*1;
					
					$sdate=date("d/m/Y",strtotime($sdata['pdate']));
					if($line==0) $line=""; else $line=$line;//(int)$line_name_res[$line]; 
					$flor=$floor_name_res[$line];
					if($flor==0) $flor=""; 
					if($line==0) $line="";
					//$size_data=$size_lib[$prod_up_arr[$sdata[col_size_id]]];
					if( $prod_type_n==12 || $prod_type_n==4 || $prod_type_n==5 || $prod_type_n==6 || $prod_type_n==8 ) $line="";
					if( $prod_type_n==6 ||$prod_type_n==5 ) $size_data="";
					$usect='';
					if($prod_type_n==9) $usect="SEW";
					
					if(  $prod_type_n>0)
						$txt .=str_pad($oid,0," ")."\t ".str_pad($sdate,0," ")."\t".str_pad($prod_type_n,0," ")."\t".str_pad($line,0," ")."\t".str_pad($sdata['qnty'],0," ")."\t".str_pad('',0," ")."\r\n";
				}
			}	
		}
		
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
		
		
		$file_name="frfiles/ORDUPDAT_L.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");

		$txt .=str_pad("O.CODE",0," ")."\t".str_pad("O.COMPLETE",0," ")."\t".str_pad("O.UDACTUAL SHIPMENT",0," ")."\r\n";
		foreach($exFactoryArr as $ocode=>$exdate)
		{
			if($ocode!="")
				$txt .=str_pad($ocode,0," ")."\t 1 \t ".str_pad($exdate,0," ")."\r\n";
		}
		
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
		//	print_r($production_qty); die;
		// print_r($production_qty); die;
		$sql=sql_select("select id, master_tble_id, image_location from common_photo_library where is_deleted=0 and file_type=1 and form_name='knit_order_entry'  $job_cond_img");//and ((image_location like '%.jpg') or (image_location like '%.JPG') or (image_location like '%.PNG') or (image_location like '%.png') or (image_location like '%.BMP') or (image_location like '%.bmp'))
		$file_name="frfiles/IMGATTACH_L.TXT";
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
				$exfile=explode(".",$name[1]);
				if($exfile[1]=='jpg' || $exfile[1]=='JPG' || $exfile[1]=='png' || $exfile[1]=='PNG' || $exfile[1]=='BMP' || $exfile[1]=='bmp')
				{
					$txt .=$job."\t".$name[1]."\t".str_replace(".jpg","",$name[1])."\t".$imlocc."\t1\r\n";
				}
			}
			$extfile=explode(".",$rows[csf("image_location")]);
			if($extfile[1]=='jpg' || $extfile[1]=='JPG' || $extfile[1]=='png' || $extfile[1]=='PNG' || $extfile[1]=='BMP' || $extfile[1]=='bmp')
			{
				//echo "0**../../../".$rows[csf("image_location")]; 
				$zipimg->addFile("../../../".$rows[csf("image_location")]);
			}
		}
		$zipimg->close();
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
	}
	else if ( $cbo_fr_integrtion==1 )
	{
		$sql=sql_select ( "select id,short_name from lib_buyer" );
		$file_name="frfiles/CUSTOMER_L.TXT";
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
		if($db_type==0) $shipment_date="and c.country_ship_date >= '2019-07-01'"; else if($db_type==2) $shipment_date="and c.country_ship_date >= '01-Jul-2019'";
		
		$shiping_status="and c.shiping_status !=3";
		
		$po_arr=array();
		$ft_data_arr=array();
		
		$sql_po="select a.id,a.job_no,a.style_ref_no,a.gmts_item_id from wo_po_details_master a, wo_po_break_down b  where a.job_no=b.job_no_mst  and b.shipment_date >= '2014-11-01' and b.is_confirmed=2   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  order by a.id,b.id";
		$sql_po_data=sql_select($sql_po);
		foreach($sql_po_data as $sql_po_row)
		{
			$ft_data_arr[$sql_po_row[csf('job_no')]][job_no]=$sql_po_row[csf('job_no')];//P.CODE
			$ft_data_arr[$sql_po_row[csf('job_no')]][gmts_item_id]=$sql_po_row[csf('gmts_item_id')];//P.TYPE
			$ft_data_arr[$sql_po_row[csf('job_no')]][style_ref_no]=$sql_po_row[csf('style_ref_no')];//P.DESCRIP
		}
		
		unset($sql_po_data);
		
		$sql_po="select a.id,a.job_no,a.style_ref_no,a.gmts_item_id,d.costing_per,d.sew_smv,e.fab_knit_req_kg   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_mst d, wo_pre_cost_sum_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no  and b.id=c.po_break_down_id $shipment_date   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by a.id,b.id"; //and b.is_confirmed=1  
		$sql_po_data=sql_select($sql_po);
		foreach($sql_po_data as $sql_po_row)
		{
		$po_arr[po_id][$sql_po_row[csf('id')]]=$sql_po_row[csf('id')];
		$po_arr[job_no][$sql_po_row[csf('job_no')]]="'".$sql_po_row[csf('job_no')]."'";
		
		$ft_data_arr[$sql_po_row[csf('job_no')]][job_no]=$sql_po_row[csf('job_no')];//P.CODE
		$ft_data_arr[$sql_po_row[csf('job_no')]][gmts_item_id]=$sql_po_row[csf('gmts_item_id')];//P.TYPE
		$ft_data_arr[$sql_po_row[csf('job_no')]][style_ref_no]=$sql_po_row[csf('style_ref_no')];//P.DESCRIP
		$fab_knit_req_kg=0;
		if($sql_po_row[csf('costing_per')]==1)
		{
			$fab_knit_req_kg=$sql_po_row[csf('fab_knit_req_kg')]/12;
		}
		if($sql_po_row[csf('costing_per')]==2)
		{
			$fab_knit_req_kg=$sql_po_row[csf('fab_knit_req_kg')];
		}
		if($sql_po_row[csf('costing_per')]==3)
		{
			$fab_knit_req_kg=$sql_po_row[csf('fab_knit_req_kg')]/24;
		}
		if($sql_po_row[csf('costing_per')]==4)
		{
			$fab_knit_req_kg=$sql_po_row[csf('fab_knit_req_kg')]/36;
		}
		if($sql_po_row[csf('costing_per')]==5)
		{
			$fab_knit_req_kg=$sql_po_row[csf('fab_knit_req_kg')]/48;
		}
		$ft_data_arr[$sql_po_row[csf('job_no')]][fab_knit_req_kg]=number_format($fab_knit_req_kg,3);//P^CF:5
		$ft_data_arr[$sql_po_row[csf('job_no')]][cutting]=1;//P^WC:10
		
		
		
		$ft_data_arr[$sql_po_row[csf('job_no')]][sew_input]=1;//P^WC:35
		$ft_data_arr[$sql_po_row[csf('job_no')]][sew_output]=$sql_po_row[csf('sew_smv')];//P^WC:140
		
		$ft_data_arr[$sql_po_row[csf('job_no')]][poly]=1;//P^WC:160
		$ft_data_arr[$sql_po_row[csf('job_no')]][shiped]=$sql_po_row[csf('sew_smv')];//P^WC:165
	
		}
		
		
		$po_string= implode(",",$po_arr[po_id]);
		$job_string= implode(",",$po_arr[job_no]);
		
	   $job=array_chunk($po_arr[job_no],1000, true);
	   $job_cond_in="";
	   $ji=0;
	   foreach($job as $key=> $value)
	   {
		   if($ji==0)
		   {
			$job_cond_in="job_no in(".implode(",",$value).")"; 
		   }
		   else
		   {
				$job_cond_in.=" or job_no in(".implode(",",$value).")"; 
		   }
		   $ji++;
	   }
	   
	   
	   $sql_fabric_prod=sql_select("select min(id) as id,job_no  from wo_pre_cost_fabric_cost_dtls where $job_cond_in and fabric_source=1 and avg_cons>0  and status_active=1 and is_deleted=0 group by job_no");
	   foreach($sql_fabric_prod as $row_fabric_prod)
	   {
		$ft_data_arr[fab_delivery][$row_fabric_prod[csf('job_no')]]=1;//P^WC:5   
	   }
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
		//fputcsv($output, array( $rows[job_no],$rows[gmts_item_id],$rows[style_ref_no],$rows[fab_knit_req_kg],$rows[cutting] == '' ? 0 : $rows[cutting],$rows[dv_printing] == '' ? 0 : $rows[dv_printing],$rows[rv_printing] == '' ? 0 : $rows[rv_printing],$rows[dv_embrodi] == '' ? 0 :$rows[dv_embrodi],$rows[rv_embrodi] == '' ? 0 :$rows[rv_embrodi],$rows[sew_input] == '' ? 0 :$rows[sew_input],$rows[sew_output] == '' ? 0 :$rows[sew_output],$rows[dv_wash] == '' ? 0 :$rows[dv_wash],$rows[rv_wash] == '' ? 0 :$rows[rv_wash],$rows[poly] == '' ? 0 :$rows[poly],$rows[shiped] == '' ? 0 :$rows[shiped],'','','',''));
		 
		$txt="";
		$file_name="frfiles/PRODUCTS_L.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
		$txt .=str_pad("P.CODE",15," ")."\t".str_pad("P.TYPE",15," ")."\t".str_pad("P.DESCRIP",25," ")."\t".str_pad("P^CF:5",12," ")."\t".str_pad("P^WC:5",12," ")."\t".str_pad("P^WC:10",12," ")."\t".str_pad("P^WC:15",12," ")."\t".str_pad("P^WC:20",12," ")."\t".str_pad("P^WC:25",12," ")."\t".str_pad("P^WC:30",12," ")."\t".str_pad("P^WC:35",12," ")."\t".str_pad("P^WC:140",12," ")."\t".str_pad("P^WC:145",12," ")."\t".str_pad("P^WC:150",12," ")."\t".str_pad("P^WC:160",12," ")."\t".str_pad("P^WC:165",12," ")."\t".str_pad("P.UDFabrication",12," ")."\t".str_pad("P.UDGSM",12," ")."\t".str_pad("P.UDYarn Count",12," ")."\t".str_pad("P.UDStyle",12," ")."\r\n";

		foreach($ft_data_arr as $rows)
		{
			$txt .=str_pad($rows[job_no],15," ")."\t".str_pad($rows[gmts_item_id],15," ")."\t".str_pad($rows[style_ref_no],25," ")."\t".str_pad($rows[fab_knit_req_kg],12," ")."\t".str_pad($rows[cutting] == '' ? 0 : $rows[cutting],12," ")."\t".str_pad($rows[dv_printing] == '' ? 0 : $rows[dv_printing],12," ")."\t".str_pad($rows[rv_printing] == '' ? 0 : $rows[rv_printing],12," ")."\t".str_pad($rows[dv_embrodi] == '' ? 0 :$rows[dv_embrodi],12," ")."\t".str_pad($rows[rv_embrodi] == '' ? 0 :$rows[rv_embrodi],12," ")."\t".str_pad($rows[sew_input] == '' ? 0 :$rows[sew_input],12," ")."\t".str_pad($rows[sew_output] == '' ? 0 :$rows[sew_output],12," ")."\t".str_pad($rows[dv_wash] == '' ? 0 :$rows[dv_wash],12," ")."\t".str_pad($rows[rv_wash] == '' ? 0 :$rows[rv_wash],12," ")."\t".str_pad($rows[poly] == '' ? 0 :$rows[poly],12," ")."\t".str_pad($rows[shiped] == '' ? 0 :$rows[shiped],12," ")."\t\t\t\t\t\r\n";
		}
		
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
	}
	else if ( $cbo_fr_integrtion==4 )
	{
		
	}
	else if( $cbo_fr_integrtion==7 )
	{
		$sql=sql_select ( "select id,master_tble_id,image_location from common_photo_library where is_deleted=0 and file_type=1 and form_name='knit_order_entry' and ((image_location like '%.jpg') or (image_location like '%.JPG') or (image_location like '%.PNG') or (image_location like '%.png') or (image_location like '%.BMP') or (image_location like '%.bmp'))" ); // and master_tble_id in ('".implode("','",$job_ref_arr)."')
		$file_name="frfiles/IMGATTACH_L.txt";
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
			/*$exfile=explode(".",$rows[csf("image_location")]);
			if($exfile[1]=='.jpg' || $exfile[1]=='.JPG' || $exfile[1]=='.png' || $exfile[1]=='.PNG' || $exfile[1]=='.BMP' || $exfile[1]=='.bmp')
			{*/
				$name=explode("/",$rows[csf("image_location")]);
				foreach( $img_prod[$rows[csf("master_tble_id")]] as $job  )
				{
					$txt .=$job."\t".$name[1]."\t".str_replace(".jpg","",$name[1])."\t".$name[1]."\t1\r\n";
				}
				$zipimg->addFile("../../../".$rows[csf("image_location")]);
			//}
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
	//	$zip->addFile("frfiles/ImgFolders.zip");
	$zip->close();
	echo "0**d";
	disconnect($con);
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