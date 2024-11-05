<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$order_number_arr=return_library_array( "select id, po_number from   wo_po_break_down",'id','po_number');
$color_arr=return_library_array( "select id,color_name  from  lib_color", "id", "color_name"  );

if ($action=="load_drop_down_location")
{
	//echo "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name";die;
	echo create_drop_down( "cbo_location_name", 140, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/cutting_entry_controller', this.value, 'load_drop_down_floor', 'floor_td' )","" );     	 
	exit();
}
if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_name", 140, "select id,floor_name from lib_prod_floor where production_process=1 and status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "","" );     	 
}
if($db_type==0){ $insert_year="SUBSTRING_INDEX(b.insert_date, '-', 1)";}
if($db_type==2){ $insert_year="extract(year from b.insert_date)";}

if ($action=="load_drop_down_buyer")
{    
     $data=explode("**",$data);
	 $sql="select distinct c.id,c.buyer_name from  wo_po_break_down a,wo_po_details_master b,lib_buyer c where  a.job_no_mst=b.job_no and b.company_name=".$data[2]."  and job_no_prefix_num='".$data[0]."' and $insert_year='".$data[1]."' and b.buyer_name=c.id and a.status_active=1";
	$result=sql_select($sql);
	foreach($result as $val)  
	{
	 $buyer_value=$val[csf('buyer_name')];
	
	}
   echo create_drop_down( "txt_buyer_name", 140,$sql, "id,buyer_name", 0, "select Buyer" ,$buyer_value);
   exit();
}


if($action=="load_drop_down_cutt_company")
{
	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];
	
	if($data==3)
	{
		if($db_type==0)
		{
			echo create_drop_down( "cbo_cutting_company", 150, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,party_type) order by supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "fnc_workorder_search(this.value);load_drop_down( 'requires/cutting_entry_controller', this.value, 'load_drop_down_location', 'location_td' )" );
		}
		else
		{
			echo create_drop_down( "cbo_cutting_company", 150, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=22 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select--", $selected, "fnc_workorder_search(this.value)" );
		}
	}
	else if($data==1)
		echo create_drop_down( "cbo_cutting_company", 150, "select id,company_name from lib_company where is_deleted=0 and status_active=1 order by company_name","id,company_name", 1, "--- Select ---", $selected_company, "load_drop_down( 'requires/cutting_entry_controller', this.value, 'load_drop_down_location', 'location_td' )",0,0 );
	else
		echo create_drop_down( "cbo_cutting_company", 150, $blank_array,"", 1, "--- Select ---", $selected, "",0 );
	exit();
}

if ($action=="load_drop_down_workorder")
{
	$explode_data = explode("_",$data);
	
	if($explode_data[0]!="" && $explode_data[1]!="" && $explode_data[2]!="")
	{
		$sql = "select a.id,a.sys_number from piece_rate_wo_mst a, piece_rate_wo_dtls b where a.id=b.mst_id and b.order_id in(".$explode_data[2].") and a.company_id=$explode_data[0]  and a.rate_for=20 and a.service_provider_id=$explode_data[1]   and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 group by a.id,a.sys_number order by a.id"; 
		//echo $sql;
		echo create_drop_down( "cbo_work_order", 150, $sql,"id,sys_number", 1, "-- Select Work Order --", $selected, "fnc_workorder_rate(this.value,'$data')",0 );
	}
	else
	{
		echo create_drop_down( "cbo_work_order", 150, $blank_array,"", 1, "-- Select Work Order --", $selected, "fnc_workorder_rate(this.value,'$data')",0 );
	}
}
if($action=="populate_workorder_rate")
{
	$data=explode("_",$data);
	$po_break_down_id=$data[3];
	$company_id=$data[1];
	$suppplier=$data[2];
	$sql = sql_select("select a.id,a.sys_number,a.currence,a.exchange_rate,sum(b.avg_rate) as rate,b.uom,b.order_id from piece_rate_wo_mst a, piece_rate_wo_dtls b where a.id=".$data[0]." and a.id=b.mst_id and b.order_id in(".$po_break_down_id.") and a.company_id=$company_id and a.service_provider_id=$suppplier and a.rate_for=20   and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 group by a.id,a.sys_number,a.currence ,a.exchange_rate,b.uom,b.order_id order by a.id"); 
	//print_r($sql);
	$data_string="";
	foreach($sql as $val)
	{
		if($val[csf('uom')]==2) 
		{
			$rate=$val[csf('rate')]/12;
		}
		else
		{
			$rate=$val[csf('rate')];
		}
		if($data_string!="") $data_string.=",";
		$data_string.=$val[csf('order_id')]."_".$val[csf('currence')]."_".$val[csf('exchange_rate')]."_".$rate;
	/*	echo "$('#piece_rate_data_string').val('".$sql[0][csf('currence')]."');\n";
		echo "$('#hidden_exchange_rate').val('".$sql[0][csf('exchange_rate')]."');\n";
		echo "$('#hidden_piece_rate').val('".$rate."');\n"*/;
	}
	echo "$('#piece_rate_data_string').val('".$data_string."');\n";
}

if($action=="create_job_search_list_view")
{
    $ex_data = explode("_",$data);
	$company = $ex_data[0];	
	$buyer = $ex_data[1];
	$from_date = $ex_data[2];
	$to_date = $ex_data[3];
	$job_prifix= $ex_data[4];
	$job_year = $ex_data[5];
	$po_no = $ex_data[6];
	$job_cond="";
	
	if(str_replace("'","",$company)=="") $conpany_cond=""; else $conpany_cond="and b.company_name=".str_replace("'","",$company)."";
	if(str_replace("'","",$buyer)==0) $buyer_cond=""; else $buyer_cond="and b.buyer_name=".str_replace("'","",$buyer)."";
    if($db_type==2) $year_cond=" and extract(year from b.insert_date)=$job_year";
    if($db_type==0) $year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=$job_year";
	if(str_replace("'","",$job_prifix)!="")  $job_cond="and b.job_no_prefix_num=".str_replace("'","",$job_prifix)."  $year_cond";
	if(str_replace("'","",$po_no)!="")  $order_cond="and a.po_number like '%".str_replace("'","",$po_no)."%' "; else $order_cond="";
	if($db_type==0)
	{
		if( $from_date!="" && $to_date!="" ) $sql_cond = " and a.pub_shipment_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
	$sql_order="select b.job_no,b.buyer_name,a.po_number,a.pub_shipment_date,b.style_ref_no,b.job_no_prefix_num as job_prefix,SUBSTRING_INDEX(b.insert_date, '-', 1) as year from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no $buyer_cond  $sql_cond $conpany_cond $job_cond $order_cond group by b.buyer_name,b.job_no,a.po_number ";  
	}
	
	if($db_type==2)
	{
	 if( str_replace("'","",$from_date)!="" && str_replace("'","",$to_date)!="" ) 
	  {
		  $sql_cond = " and a.pub_shipment_date  between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
	  }
	
	 
	$sql_order="select b.job_no,b.buyer_name,a.po_number,a.pub_shipment_date,b.style_ref_no,b.job_no_prefix_num as job_prefix,extract(year from b.insert_date) as year from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no $buyer_cond  $sql_cond $conpany_cond $job_cond $order_cond group by  b.job_no,b.buyer_name,a.po_number,a.pub_shipment_date,b.style_ref_no,b.job_no_prefix_num, b.insert_date order by  job_no_prefix_num";  
	}
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$arr=array (3=>$buyer_arr);
	echo create_list_view("list_view", "Job NO,Year,Style Ref,Buyer Name, Orer No,Shipment Date","100,100,150,150,150,150","850","270",0, $sql_order , "js_set_order", "job_no,buyer_name,year", "", 1, "0,0,0,buyer_name,0,0,0", $arr, "job_prefix,year,style_ref_no,buyer_name,po_number,pub_shipment_date", "","setFilterGrid('list_view',-1)") ;	
	
}
//master data save update delete here------------------------------//
if($action=="save_update_delete")
{	
     
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$color_size_arr=sql_select("select id,size_number_id,color_number_id,po_break_down_id,country_id,item_number_id from wo_po_color_size_breakdown where status_active=1 and is_deleted=0");
	$color_beckdown_arr=array();
	foreach($color_size_arr as $val)
	{
		$color_beckdown_arr[$val[csf("po_break_down_id")]][$val[csf("country_id")]][$val[csf("item_number_id")]][$val[csf("color_number_id")]][$val[csf("size_number_id")]]=$val[csf("id")];
	}
	
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
	    $con = connect();
	    if($db_type==0)	{ mysql_query("BEGIN"); }
		
	    $field_array="id, garments_nature, company_id, production_source, serving_company,country_id, po_break_down_id,item_number_id, location, production_date,production_quantity,production_type, entry_break_down_type, production_hour, floor_id, reject_qnty,total_produced, yet_to_produced, cut_no, delivery_mst_id,batch_no,wo_order_id,currency_id,exchange_rate,rate,amount, inserted_by, insert_date";
	    $field_array_dtls="id, mst_id,production_type,color_size_break_down_id,production_qnty,cut_no,bundle_no,reject_qty,replace_qty,delivery_mst_id";
 	   	$id_bundle = return_next_id_by_sequence(  "pro_bundle_mst_seq",  "pro_bundle_mst", $con );

	    $field_array_bundle="id, pro_gmts_pro_id, po_break_down_id, color_size_id, bundle_no_creation, cut_no, pcs_per_bundle, no_of_bundle, batch_no, status_active, is_deleted, inserted_by, insert_date";
 		$bundle_dtls_id= return_next_id_by_sequence(  "pro_bundle_dtls_seq",  "pro_bundle_dtls", $con );
		$field_array_bundle_dtls="id,bundle_mst_id,pcs_per_bundle,pcs_range_start,pcs_range_end,color_size_id,bundle_bar_code,bundle_bar_code_prefix, cut_no";
		$field_array_qc_mst="id,cut_qc_prefix,cut_qc_prefix_no,cutting_qc_no,cutting_no,location_id,floor_id,table_no,job_no,batch_id, company_id, entry_date,start_time, end_date,end_time ,marker_length,marker_width,fabric_width, gsm,width_dia,cutting_qc_date, cutting_qc_time, production_source, serving_company, work_order_id, workorder_rate_breakdown,remarks, inserted_by,insert_date, status_active,is_deleted";
	    $field_array_qc_dtls="id,mst_id,order_id,country_id,color_id,size_id,color_size_id,bundle_no,number_start,number_end,bundle_qty, reject_qty, replace_qty, qc_pass_qty, inserted_by, insert_date, status_active,is_deleted";
		$field_array_defect="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty,bundle_no, inserted_by, insert_date";
		
		
		$txt_order_id=explode("*",$txt_order_id);
		$country_id=explode("___",$country_id);
		$hidden_color=explode("*",$hidden_color);
		$txt_gmt_id=explode("*",$txt_gmt_id);
		$total_qc_qty=explode("*",$total_qc_qty);
		$total_reject_qty=explode("*",$total_reject_qty);
		$txt_qty=explode("___",$txt_qty);
		$txt_bundle_no=explode("___",$txt_bundle_no);
		$txt_start=explode("___",$txt_start);				
		$txt_end=explode("___",$txt_end);
		$txt_reject=explode("___",$txt_reject);
		$txt_qcpass=explode("___",$txt_qcpass);
		$txt_replace=explode("___",$txt_replace);
		$size_id=explode("___",$size_id);	
		$size_details_id=explode("___",$size_details_id);
		$size_details_qty=explode("___",$size_details_qty);
		$size_details_bdl=explode("___",$size_details_bdl);
		$pcs_per_bdl=explode("___",$pcs_per_bdl);
		$actual_reject=explode("##",$actual_reject);
		
 		$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );

 		$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

 		$qc_id = return_next_id_by_sequence(  "pro_gmts_cutting_qc_mst_seq",   "pro_gmts_cutting_qc_mst", $con );

 	    $qc_dtls_id = return_next_id_by_sequence(  "pro_gmts_cutting_qc_dtls_seq",  "pro_gmts_cutting_qc_dtls", $con );
 		$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con ); 

		if($db_type==0)
		{ 
			$txt_cutting_date=change_date_format($txt_cutting_date,"yyyy-mm-dd");
			$txt_entry_date=change_date_format($txt_entry_date,"yyyy-mm-dd");
			$txt_end_date=change_date_format($txt_end_date,"yyyy-mm-dd");
			$year_id="YEAR(insert_date)=";
		}
		if($db_type==2) 
		{
			$txt_cutting_date=change_date_format($txt_cutting_date,"yyyy-mm-dd","-",1);
			$txt_entry_date=change_date_format($txt_entry_date,"yyyy-mm-dd","-",1);
			$txt_end_date=change_date_format($txt_end_date,"yyyy-mm-dd","-",1);
			$year_id=" extract(year from insert_date)=";
			$txt_cutting_hour=str_replace("'","",$txt_cutting_date)." ".str_replace("'","",$txt_cutting_hour);
			$txt_cutting_hour="to_date('".$txt_cutting_hour."','DD MONTH YYYY HH24:MI:SS')";
		}
		
 		$new_system_id = explode("*", return_next_id_by_sequence("", "pro_gmts_cutting_qc_mst",$con,1,$cbo_company,'CQ',0,date("Y",time()),0,0,0,0,0 ));

		if(str_replace("'","",$txt_in_time_hours)!="" && str_replace("'","",$txt_in_time_minuties)!="")
		{
	     	$start_time=str_replace("'","",$txt_in_time_hours).":".str_replace("'","",$txt_in_time_minuties);
		}
		else  $start_time="";
		if(str_replace("'","",$txt_out_time_hours)!="" && str_replace("'","",$txt_out_time_minuties)!="")
		{
		 	$end_time=str_replace("'","",$txt_out_time_hours).":".str_replace("'","",$txt_out_time_minuties);
		}
		else  $end_time="";
		if($db_type==0)
		{
			 $data_arra_cutt_mst="(".$qc_id.",'".$new_system_id[1]."',".(int)$new_system_id[2].",'".$new_system_id[0]."','".$txt_cutting_no."','".$cbo_location_name."','".$cbo_floor_name."','".$txt_table_no."','".$txt_job_no."', '".$txt_batch_no."',".$cbo_company.",'".$txt_entry_date."','".$start_time."','".$txt_end_date."','".$end_time."','".$txt_marker_length."','".$txt_marker_width."','".$txt_fabric_width."','".$txt_gsm."','".$cbo_width_dia."','".$txt_cutting_date."','".$txt_cutting_hour."','".$cbo_source."','".$cbo_cutting_company."','".$cbo_work_order."','".$piece_rate_data_string."','".$txt_remarks."',".$user_id.",'".$pc_date_time."',1,0)";
		}
		else
		{
		 	$data_arra_cutt_mst="insert into pro_gmts_cutting_qc_mst (".$field_array_qc_mst.") VALUES(".$qc_id.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."','".$txt_cutting_no."','".$cbo_location_name."','".$cbo_floor_name."','".$txt_table_no."','".$txt_job_no."', '".$txt_batch_no."',".$cbo_company.",'".$txt_entry_date."','".$start_time."','".$txt_end_date."','".$end_time."','".$txt_marker_length."','".$txt_marker_width."','".$txt_fabric_width."','".$txt_gsm."','".$cbo_width_dia."','".$txt_cutting_date."',".$txt_cutting_hour.",'".$cbo_source."','".$cbo_cutting_company."','".$cbo_work_order."','".$piece_rate_data_string."','".$txt_remarks."',".$user_id.",'".$pc_date_time."',1,0)"; 
			
		}
		$order_wise_rate_data_arr=array();
		$piece_rate_data_string=explode(",",$piece_rate_data_string);
		foreach($piece_rate_data_string as $order_rate_data)
		{
			$order_rate_data_arr=explode("_",$order_rate_data);
			$order_wise_rate_data_arr[$order_rate_data_arr[0]][currency]=$order_rate_data_arr[1];
			$order_wise_rate_data_arr[$order_rate_data_arr[0]][exchange_rate]=$order_rate_data_arr[2];
			$order_wise_rate_data_arr[$order_rate_data_arr[0]][rate]=$order_rate_data_arr[3];
		}
		
		 $sl=0;
	     for($i=0;$i<count($txt_order_id); $i++)
		 { 
			$txt_country_id=explode("*",$country_id[$i]);
			$txt_size_qty=explode("*",$txt_qty[$i]);
			$txt_bundle_number=explode("*",$txt_bundle_no[$i]);
			$txt_bdl_start=explode("*",$txt_start[$i]);				
			$txt_bdl_end=explode("*",$txt_end[$i]);
			$txt_reject_qty=explode("*",$txt_reject[$i]);
			$txt_replace_qty=explode("*",$txt_replace[$i]);
			$txt_qcpass_qty=explode("*",$txt_qcpass[$i]);
			$txt_size_id=explode("*",$size_id[$i]);
			$txt_size_details_id=explode("*",$size_details_id[$i]);   
	    	$txt_size_details_qty=explode("*",$size_details_qty[$i]);
		    $txt_size_details_bdl=explode("*",$size_details_bdl[$i]);
		    $txt_pcs_per_bdl=explode("*",$pcs_per_bdl[$i]);
			$txt_rmg_start=explode("*",$txt_start[$i]);
		    $txt_rmg_end=explode("*",$txt_end[$i]);
			$txt_actual_reject=explode("_",$actual_reject[$i]);
			$k=0;
			if($i!=0) $data_array_qc_detls.=",";
			if($i!=0) $data_array_bundle_dtls.=",";
			if($i!=0) $data_array_bundle.=",";
			if($i!=0) $data_array_dtls.=",";
		    for($m=0;   $m<count($txt_bundle_number); $m++)
		    {
				 if(str_replace("'","",$txt_qcpass_qty[$m])!="")
				 {
					 $color_size_bkdown_id=$color_beckdown_arr[$txt_order_id[$i]][$txt_country_id[$m]][$txt_gmt_id[$i]][$hidden_color[$i]][$txt_size_id[$m]];
					 $bundle_no_creation=1;
					 $bundle_barcode_prifix=explode("-",$txt_bundle_number[$m]);
					 $bundle_barcode_prifix=$bundle_barcode_prifix[0]."-".$bundle_barcode_prifix[1];
					 if($m!=0) $data_array_bundle_dtls.=",";
					 if($m!=0) $data_array_dtls.=",";
					 if(str_replace("'","",$txt_reject_qty[$m])=="") $txt_reject_qty[$m]=0;
					 if(str_replace("'","",$txt_replace_qty[$m])=="") $txt_replace_qty[$m]=0;
					 
					 $rls=0;
					 if($txt_actual_reject[$m]!="")
					 {
						$actual_reject_info=explode("**",$txt_actual_reject[$m]); 
						for($rls=0;$rls<count($actual_reject_info); $rls++)
						{
							$bundle_reject_info=explode("*",$actual_reject_info[$rls]);	
							if( trim($data_array_defect)!="") $data_array_defect.=",";
							$defectPointId=$bundle_reject_info[0];
							$defect_qty=$bundle_reject_info[1];
							
							$data_array_defect.="(".$dft_id.",".$id.",1,'".$txt_order_id[$i]."',3,".$defectPointId.",'".$defect_qty."','".$txt_bundle_number[$m]."',".$user_id.",'".$pc_date_time."')";
							//$dft_id++;
						$dft_id = return_next_id_by_sequence( "pro_gmts_prod_dft_seq", "pro_gmts_prod_dft", $con ); 

						}
					 }
					 
					 $data_array_bundle_dtls.="(".$bundle_dtls_id.",".$id_bundle.",".$bundle_no_creation.",'".$txt_bdl_start[$m]."','".$txt_bdl_end[$m]."','".$color_size_bkdown_id."','".$txt_bundle_number[$m]."','".$bundle_barcode_prifix."','".$txt_cut_prifix."')";
				
					//$expBundleData=explode('-',$txt_bundle_number[$m]);
					//$cut_no=trim($expBundleData[0]).'-'.trim($expBundleData[1]);
					

					 $data_array_dtls .= "(".$dtls_id.",".$id.",1,'".$color_size_bkdown_id."','".$txt_qcpass_qty[$m]."','".$txt_cutting_no."','".$txt_bundle_number[$m]."','".$txt_reject_qty[$m]."','".$txt_replace_qty[$m]."','".$qc_id."')";
					// $dtls_id=$dtls_id+1;
					 $dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

				
					 
					 if($m!=0) $data_array_qc_detls.=",";
					 if($m!=0) $data_array_bundle.=",";
					 
					 $data_array_bundle.="(".$id_bundle.",".$id.",'".$txt_order_id[$i]."','".$color_size_bkdown_id."',".$bundle_no_creation.",'".$txt_cut_prifix."','".trim($txt_size_qty[$m])."','1','".$txt_batch_no."',1,0,".$user_id.",'".$pc_date_time."')";
					 //".$txt_size_details_bdl[$m]."
					 $data_array_qc_detls.="(".$qc_dtls_id.",".$qc_id.",'".$txt_order_id[$i]."','".$txt_country_id[$m]."','".$hidden_color[$i]."','".$txt_size_id[$m]."','".$color_size_bkdown_id."','".$txt_bundle_number[$m]."','".$txt_rmg_start[$m]."','".$txt_rmg_end[$m]."','".trim($txt_size_qty[$m])."','".$txt_reject_qty[$m]."','".$txt_replace_qty[$m]."','".$txt_qcpass_qty[$m]."',".$user_id.",'".$pc_date_time."',1,0)";
					  //$bundle_dtls_id++;
					  $bundle_dtls_id= return_next_id_by_sequence(  "pro_bundle_dtls_seq",  "pro_bundle_dtls", $con );
						$id_bundle = return_next_id_by_sequence(  "pro_bundle_mst_seq",  "pro_bundle_mst", $con );
					//  $qc_dtls_id++;
					  $qc_dtls_id = return_next_id_by_sequence(  "pro_gmts_cutting_qc_dtls_seq",   "pro_gmts_cutting_qc_dtls", $con );

					  
					  $country_qcpass_qty[$txt_country_id[$m]]+=$txt_qcpass_qty[$m]*1;
					  $country_reject_qty[$txt_country_id[$m]]+=$txt_reject_qty[$m]*1;
					  $country_replace_qty[$txt_country_id[$m]]+=$txt_replace_qty[$m]*1;
				  	}
				 }
				 foreach($country_qcpass_qty as $c_id=>$c_qty)
				 {
					$plan_cut_qnty=return_field_value("sum(plan_cut_qnty) as plan_cut_qty","wo_po_color_size_breakdown","po_break_down_id=".$txt_order_id[$i]."
					and item_number_id=".$txt_gmt_id[$i]." and  country_id=".$c_id." and status_active=1 and is_deleted=0","plan_cut_qty");
			
					$total_produced = return_field_value("sum(production_quantity) as total_cut","pro_garments_production_mst","po_break_down_id=".$txt_order_id[$i]." and item_number_id=".$txt_gmt_id[$i]." and  country_id=".$c_id." and production_type=1 and is_deleted=0","total_cut");
					$yet_to_produced=$plan_cut_qnty-$total_produced;
					
					$currency_id=$order_wise_rate_data_arr[$txt_order_id[$i]][currency];
					$exchange_rate=$order_wise_rate_data_arr[$txt_order_id[$i]][exchange_rate];
					$rate=str_replace("'","",$order_wise_rate_data_arr[$txt_order_id[$i]][rate]);
					if($rate!="") {  $amount=$rate*str_replace("'","",trim($c_qty))*1;}
					else {$amount="";}
					
					if($country_reject_qty[$c_id]=="") $country_reject_qty[$c_id]=0;
					if($db_type==0)
					{
						if($sl!=0) $data_array .=",";
						$data_array.="(".$id.",".$garments_nature.",".$cbo_company.",'".$cbo_source."','".$cbo_cutting_company."',".$c_id.",'".$txt_order_id[$i]."', ".$txt_gmt_id[$i].",'".$cbo_location_name."','".$txt_cutting_date."',".trim($c_qty).",1,3,'".$txt_cutting_hour."','".$cbo_floor_name."',".$country_reject_qty[$c_id].",'".$total_produced."','".$yet_to_produced."','".$txt_cutting_no."','".$qc_id."','".$txt_batch_no."','".$cbo_work_order."','".$currency_id."','".$exchange_rate."','".$rate."','".$amount."',".$user_id.",'".$pc_date_time."')";
						//$id=$id+1;
								$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq", "pro_garments_production_mst", $con );

						$sl++;
					}
					else
					{
						
						
						$data_array.=" INTO pro_garments_production_mst (".$field_array.") VALUES(".$id.",".$garments_nature.",".$cbo_company.",'".$cbo_source."','".$cbo_cutting_company."','".$c_id."','".$txt_order_id[$i]."', ".$txt_gmt_id[$i].",'".$cbo_location_name."', '".$txt_cutting_date."',".trim($c_qty).",1,3,".$txt_cutting_hour.",'".$cbo_floor_name."','".$country_reject_qty[$c_id]."','".$total_produced."', '".$yet_to_produced."','".$txt_cutting_no."','".$qc_id."','".$txt_batch_no."','".$cbo_work_order."','".$currency_id."','".$exchange_rate."','".$rate."', '".$amount."', ".$user_id.",'".$pc_date_time."')";
						//$id=$id+1;
						$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );

					}
				 }
			 unset($country_qcpass_qty);
			 unset($country_reject_qty);
			 unset($country_replace_qty);
		     unset($check_size_id);
		 }
	    if($db_type==2)
		{
			$query="INSERT ALL ".$data_array." SELECT * FROM dual";
			$rID=execute_query($query);
			$rID_mst=execute_query($data_arra_cutt_mst);
			//echo $data_arra_cutt_mst;
		}
		else
		{
			$rID=sql_insert("pro_garments_production_mst",$field_array,$data_array,1);
			$rID_mst=sql_insert("pro_gmts_cutting_qc_mst",$field_array_qc_mst,$data_arra_cutt_mst,1);
		}
		$rID_dtls=sql_insert("pro_gmts_cutting_qc_dtls",$field_array_qc_dtls,$data_array_qc_detls,1);
		$defectQ=1;
		if($data_array_defect!="")
		{
			$defectQ=sql_insert("pro_gmts_prod_dft",$field_array_defect,$data_array_defect,1);
		}
    	$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);
		$rID_bundle=sql_insert("pro_bundle_mst",$field_array_bundle,$data_array_bundle,1);
		$rID_bundle_dtls=sql_insert("pro_bundle_dtls",$field_array_bundle_dtls,$data_array_bundle_dtls,1);

		
		// echo "10**insert into pro_gmts_cutting_qc_dtls (".$field_array_qc_dtls.") Values ".$data_array_qc_detls."";die;
	
		//echo "10**".$rID;die;	
  
 		//10**0 && 1 && 0 && 0 && 0 && 1 && 1
 		// echo "10**".$rID."**".$dtlsrID."**".$rID_mst."**".$rID_dtls."**".$rID_bundle."**".$rID_bundle_dtls."**".$defectQ;die();
		
		if($db_type==0)
		{
			if($rID && $dtlsrID && $rID_mst && $rID_dtls && $rID_bundle && $rID_bundle_dtls && $defectQ)
			{
				mysql_query("COMMIT");  
				echo "0**".$new_system_id[0]."**".$txt_cutting_no;
			}
	  		else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_system_id[0];
			}
		 }
		 
		 if($db_type==2 || $db_type==1 )
		 {

			if($rID && $dtlsrID && $rID_mst && $rID_dtls && $rID_bundle && $rID_bundle_dtls && $defectQ)
			{
				oci_commit($con);   
				echo "0**".$new_system_id[0]."**".$txt_cutting_no;
			}
	   		else
			{
				oci_rollback($con);
				echo "10**".$new_system_id[0];
			}
					
		}
		disconnect($con);
		die;
	}		
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
 		$con = connect();
	    if($db_type==0)	{ mysql_query("BEGIN"); }
		$cut_no_prifix=return_field_value("cut_num_prefix_no"," ppl_cut_lay_mst","cutting_no='".$txt_cutting_no."'");//die;
	
		$field_array1="production_source*serving_company*production_date*production_quantity*production_hour*reject_qnty*wo_order_id* currency_id* exchange_rate* rate*amount*updated_by*update_date";	
	    $field_array_dtls="id, mst_id,production_type,color_size_break_down_id,production_qnty,cut_no,bundle_no,reject_qty,replace_qty";
 	   	$id_bundle = return_next_id_by_sequence(  "pro_bundle_mst_seq",  "pro_bundle_mst", $con );

 		$bundle_dtls_id= return_next_id_by_sequence(  "pro_bundle_dtls_seq",  "pro_bundle_dtls", $con );
 		$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",   "pro_gmts_prod_dft", $con ); 

 		$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );

 		$qc_dtls_id = return_next_id_by_sequence(  "pro_gmts_cutting_qc_dtls_seq",   "pro_gmts_cutting_qc_dtls", $con );
		
	    $field_array_bundle="id, pro_gmts_pro_id, po_break_down_id, color_size_id, bundle_no_creation, cut_no, pcs_per_bundle, no_of_bundle, status_active, is_deleted, inserted_by, insert_date";
		$field_array_bundle_dtls="id,bundle_mst_id,pcs_per_bundle,pcs_range_start,pcs_range_end,color_size_id,bundle_bar_code,bundle_bar_code_prefix,cut_no";
		$field_array_qc_mst="production_source*serving_company*work_order_id*workorder_rate_breakdown*cutting_qc_date*cutting_qc_time*remarks*updated_by*update_date";
	    $field_array_qc_dtls="reject_qty*replace_qty*qc_pass_qty* updated_by*update_date";
		$field_array_defect="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty,bundle_no, inserted_by, insert_date";
		$field_array_qc_save="id,mst_id,order_id,country_id,color_id,size_id,color_size_id,bundle_no,number_start,number_end,bundle_qty,reject_qty, replace_qty, qc_pass_qty,inserted_by,insert_date,status_active,is_deleted";
		
		$product_mst_sql=sql_select("select * from pro_garments_production_mst where production_type=1 and cut_no='".$txt_cutting_no."'");
		$product_mst_arr=array();
		foreach($product_mst_sql as $row)
		{
			$update_mst_arr[]=$row[csf("id")];
			$product_mst_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]]=$row[csf("id")];
			$all_po_arr[]=$row[csf("id")];
		}
		
		$country_id=explode("___",$country_id);
	    $update_details_id=explode("___",$update_details_id);
		$txt_order_id=explode("*",$txt_order_id);
		$hidden_color=explode("*",$hidden_color);
		$txt_gmt_id=explode("*",$txt_gmt_id);
		$total_qc_qty=explode("*",$total_qc_qty);
		$total_reject_qty=explode("*",$total_reject_qty);
		$txt_qty=explode("___",$txt_qty);
		$txt_bundle_no=explode("___",$txt_bundle_no);
		$txt_start=explode("___",$txt_start);				
		$txt_end=explode("___",$txt_end);
		$txt_reject=explode("___",$txt_reject);
		$txt_replace=explode("___",$txt_replace);
		$txt_qcpass=explode("___",$txt_qcpass);
		$size_id=explode("___",$size_id);	
		$size_details_id=explode("___",$size_details_id);
		$size_details_qty=explode("___",$size_details_qty);
		$size_details_bdl=explode("___",$size_details_bdl);
		$pcs_per_bdl=explode("___",$pcs_per_bdl);
		$actual_reject=explode("##",$actual_reject);
		
		if($db_type==0)
		{ 
			  $txt_cutting_date=change_date_format($txt_cutting_date,"yyyy-mm-dd");
			  $txt_entry_date=change_date_format($txt_entry_date,"yyyy-mm-dd");
			  $txt_end_date=change_date_format($txt_end_date,"yyyy-mm-dd");
			  $year_id="YEAR(insert_date)=";
			  $txt_cutting_hour="'".$txt_cutting_hour."'";
		}
		if($db_type==2) 
		{
			  $txt_cutting_date=change_date_format($txt_cutting_date,"yyyy-mm-dd","-",1);
			  $txt_entry_date=change_date_format($txt_entry_date,"yyyy-mm-dd","-",1);
			  $txt_end_date=change_date_format($txt_end_date,"yyyy-mm-dd","-",1);
			  $year_id=" extract(year from insert_date)=";
			  $txt_cutting_hour=str_replace("'","",$txt_cutting_date)." ".str_replace("'","",$txt_cutting_hour);
			  $txt_cutting_hour="to_date('".$txt_cutting_hour."','DD MONTH YYYY HH24:MI:SS')";
		}
		
		if($db_type==2)
		{
			$txt_reporting_hour=str_replace("'","",$txt_cutting_date)." ".str_replace("'","",$txt_reporting_hour);
			$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
		}
		
	  	$txt_mst_id=implode(",",$all_po_arr);
	  	$data_arra_cutt_mst="'".$cbo_source."'*".$cbo_cutting_company."*'".$cbo_work_order."'*'".$piece_rate_data_string."'*'".$txt_cutting_date."'*".$txt_cutting_hour."*'".$txt_remarks."'*".$user_id."*'".$pc_date_time."'";
	  
		$order_wise_rate_data_arr=array();
		$piece_rate_data_string=explode(",",$piece_rate_data_string);
		foreach($piece_rate_data_string as $order_rate_data)
		{
			$order_rate_data_arr=explode("_",$order_rate_data);
			$order_wise_rate_data_arr[$order_rate_data_arr[0]][currency]=$order_rate_data_arr[1];
			$order_wise_rate_data_arr[$order_rate_data_arr[0]][exchange_rate]=$order_rate_data_arr[2];
			$order_wise_rate_data_arr[$order_rate_data_arr[0]][rate]=$order_rate_data_arr[3];
		}
		
	  	for($i=0;$i<count($txt_order_id); $i++)
	  	{ 
			if($db_type==2)
			{
				$txt_reporting_hour=str_replace("'","",$txt_cutting_date)." ".str_replace("'","",$txt_reporting_hour);
				$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
			}
			
		    $txt_country_id=explode("*",$country_id[$i]);
			$update_detail_id=explode("*",$update_details_id[$i]);
			$txt_size_qty=explode("*",$txt_qty[$i]);
			$txt_bundle_number=explode("*",$txt_bundle_no[$i]);
			$txt_bdl_start=explode("*",$txt_start[$i]);				
			$txt_bdl_end=explode("*",$txt_end[$i]);
			$txt_reject_qty=explode("*",$txt_reject[$i]);
			$txt_replace_qty=explode("*",$txt_replace[$i]);
			$txt_qcpass_qty=explode("*",$txt_qcpass[$i]);
			$txt_size_id=explode("*",$size_id[$i]);
			$txt_size_details_id=explode("*",$size_details_id[$i]);
	    	$txt_size_details_qty=explode("*",$size_details_qty[$i]);
		    $txt_size_details_bdl=explode("*",$size_details_bdl[$i]);
		    $txt_pcs_per_bdl=explode("*",$pcs_per_bdl[$i]);
			$txt_rmg_start=explode("*",$txt_start[$i]);
		    $txt_rmg_end=explode("*",$txt_end[$i]);
			$txt_actual_reject=explode("_",$actual_reject[$i]);
			
			$k=0;
		    for($m=0;$m<count($txt_bundle_number); $m++)
		    {
				if(str_replace("'","",$txt_qcpass_qty[$m])!="")
				{
				   $color_size_bkdown_id=$color_beckdown_arr[$txt_order_id[$i]][$txt_country_id[$m]][$txt_gmt_id[$i]][$hidden_color[$i]][$txt_size_id[$m]];
				   $bundle_no_creation=1;
				   if(str_replace("'","",$txt_reject_qty[$m])=="") $txt_reject_qty[$m]=0;
				   if(str_replace("'","",$txt_replace_qty[$m])=="") $txt_replace_qty[$m]=0;
				   
				   if(str_replace("'",'',$update_detail_id[$m])!="")
				   {
					   $update_detail_arr[]=str_replace("'",'',$update_detail_id[$m]);
					   $update_detail=str_replace("'",'',$update_detail_id[$m]);
					   $data_array_qc_detls[$update_detail] =explode(",",("'".$txt_reject_qty[$m]."','".$txt_replace_qty[$m]."','".$txt_qcpass_qty[$m]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
				   }
				   else
				   {
				    	if($data_array_qc_save!="") $data_array_qc_save.=",";
					    $data_array_qc_save.="(".$qc_dtls_id.",".$update_id.",'".$txt_order_id[$i]."','".$txt_country_id[$m]."','".$hidden_color[$i]."','".$txt_size_id[$m]."','".$color_size_bkdown_id."','".$txt_bundle_number[$m]."','".$txt_rmg_start[$m]."','".$txt_rmg_end[$m]."','".trim($txt_size_qty[$m])."','".$txt_reject_qty[$m]."','".$txt_replace_qty[$m]."','".$txt_qcpass_qty[$m]."',".$user_id.",'".$pc_date_time."',1,0)";  
						//$qc_dtls_id++;
					   $qc_dtls_id = return_next_id_by_sequence(  "pro_gmts_cutting_qc_dtls_seq",  "pro_gmts_cutting_qc_dtls", $con );
				   }
				   $bundle_barcode_prifix=explode("-",$txt_bundle_number[$m]);
				   $bundle_barcode_prifix=$bundle_barcode_prifix[0]."-".$bundle_barcode_prifix[1];
				   if($m!=0) $data_array_bundle_dtls.=",";
				   if($m!=0) $data_array_dtls.=",";
				   if($m!=0) $data_array_bundle.=",";
				   if($i!=0 && $m==0) $data_array_bundle_dtls.=",";
				   if($i!=0 && $m==0) $data_array_dtls.=",";
				   if($i!=0 && $m==0) $data_array_bundle.=",";
				   $rls=0;
				   if($txt_actual_reject[$m]!="")
				   {
						$actual_reject_info=explode("**",$txt_actual_reject[$m]); 
						for($rls=0;$rls<count($actual_reject_info); $rls++)
						{
							$bundle_reject_info=explode("*",$actual_reject_info[$rls]);	
							if( trim($data_array_defect)!="") $data_array_defect.=",";
							$defectPointId=$bundle_reject_info[0];
							$defect_qty=$bundle_reject_info[1];
							
							$data_array_defect.="(".$dft_id.",".$product_mst_arr[$txt_order_id[$i]][$txt_country_id[$m]].",1,".$txt_order_id[$i].",3,".$defectPointId.",'".$defect_qty."','".$txt_bundle_number[$m]."',".$user_id.",'".$pc_date_time."')";
							//$dft_id++;
						$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con ); 

						}
					
				   }
				   $data_array_bundle_dtls.="(".$bundle_dtls_id.",".$id_bundle.",".$bundle_no_creation.",'".$txt_bdl_start[$m]."','".$txt_bdl_end[$m]."','".$color_size_bkdown_id."','".$txt_bundle_number[$m]."','".$bundle_barcode_prifix."','".$cut_no_prifix."')";
				   
				   if(empty($txt_order_id[$i])) {$txt_order_id=0; }else{$txt_order_id = $txt_order_id[$i];}
				   $data_array_bundle.="(".$id_bundle.",'".$product_mst_arr[$txt_order_id[$i]][$txt_country_id[$m]]."',".$txt_order_id.",'".$color_size_bkdown_id."',".$bundle_no_creation.",'".$cut_no_prifix."','".trim($txt_size_qty[$m])."','".$txt_size_details_bdl[$m]."',1,0,".$user_id.",'".$pc_date_time."')";
				   
				   //$expBundleData=explode('-',$txt_bundle_number[$m]);
				   //$cut_no=trim($expBundleData[0]).'-'.trim($expBundleData[1]);
				   
				   $data_array_dtls .= "(".$dtls_id.",'".$product_mst_arr[$txt_order_id[$i]][$txt_country_id[$m]]."',1,'".$color_size_bkdown_id."','".$txt_qcpass_qty[$m]."','".$txt_cutting_no."','".$txt_bundle_number[$m]."','".$txt_reject_qty[$m]."','".$txt_replace_qty[$m]."')";
				  // $dtls_id=$dtls_id+1;
				   	$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

				  // $bundle_dtls_id++;
				   	$bundle_dtls_id= return_next_id_by_sequence(  "pro_bundle_dtls_seq",  "pro_bundle_dtls", $con );
				  $id_bundle = return_next_id_by_sequence(  "pro_bundle_mst_seq",  "pro_bundle_mst", $con );
				 //  $qc_dtls_id++;
				   $qc_dtls_id = return_next_id_by_sequence(  "pro_gmts_cutting_qc_dtls_seq",   "pro_gmts_cutting_qc_dtls", $con );
				   
				   $product_mst_qc[$txt_order_id[$i]][$c_id]+=$txt_qcpass_qty[$m]*1;
				   $product_mst_re[$txt_order_id[$i]][$c_id]+=$txt_replace_qty[$m]*1;
				   $country_qcpass_qty[$txt_country_id[$m]]+=$txt_qcpass_qty[$m]*1;
				   $country_reject_qty[$txt_country_id[$m]]+=$txt_reject_qty[$m]*1;
				   $country_replace_qty[$txt_country_id[$m]]+=$txt_replace_qty[$m]*1;
				}
		 	 }

			 foreach($country_qcpass_qty as $c_id=>$c_qty)
			 {
				$currency_id=$order_wise_rate_data_arr[$txt_order_id[$i]][currency];
				$exchange_rate=$order_wise_rate_data_arr[$txt_order_id[$i]][exchange_rate];
				$rate=str_replace("'","",$order_wise_rate_data_arr[$txt_order_id[$i]][rate]);
				if($rate!="") {  $amount=$rate*str_replace("'","",trim($country_qcpass_qty[$c_id]))*1;}
				else {$amount="";}
				if($country_reject_qty[$c_id]=="") $country_reject_qty[$c_id]=0;
				$data_array_prod[str_replace("'",'',$product_mst_arr[$txt_order_id[$i]][$c_id])] =explode("*",("'".$cbo_source."'*'".$cbo_cutting_company."'*'".$txt_cutting_date."'*".trim($country_qcpass_qty[$c_id])."*".$txt_cutting_hour."*'".$country_reject_qty[$c_id]."'*'".$cbo_work_order."'*'".$currency_id."'*'".$exchange_rate."'*'".$rate."'*'".$amount."'*'".$user_id."'*'".$pc_date_time."'"));
			 }
			 
		     unset($check_size_id);
			 $id=$id+1; 
		 }
		$rejectDelete=execute_query("DELETE FROM pro_gmts_prod_dft WHERE mst_id in (".$txt_mst_id.") and defect_type_id=3 and production_type=1");
		
	    $dtlsrDelete = execute_query("delete from pro_garments_production_dtls where mst_id in (".$txt_mst_id.")",0);
	    $dtlsrDelete_bundle = execute_query("delete from pro_bundle_mst where  pro_gmts_pro_id in($txt_mst_id)",0);      
	    $dtlsrDelete_bundle_dtls = execute_query("delete from pro_bundle_dtls where cut_no in ($cut_no_prifix)",0);
		
		$query=execute_query( bulk_update_sql_statement("pro_garments_production_mst","id",$field_array1,$data_array_prod,$update_mst_arr),1);
		$rID_dtls_qc = 1;
		if($data_array_qc_detls !="")
		{
			$rID_dtls_qc=execute_query( bulk_update_sql_statement("pro_gmts_cutting_qc_dtls","id",$field_array_qc_dtls,$data_array_qc_detls,$update_detail_arr),1);
		}
		
		$defectQ=1;
		if($data_array_defect!="")
		{
			$defectQ=sql_insert("pro_gmts_prod_dft",$field_array_defect,$data_array_defect,1);
		}
		
		$rID_dtlsqc=1;
		if($data_array_qc_save!="")
		{
			$rID_dtlsqc=sql_insert("pro_gmts_cutting_qc_dtls",$field_array_qc_save,$data_array_qc_save,1);
		}
		$rID_mst_qc = 1;
		if($data_arra_cutt_mst !="")
		{
			$rID_mst_qc=sql_update("pro_gmts_cutting_qc_mst",$field_array_qc_mst,$data_arra_cutt_mst,"id",$update_id,1);
		}
		
    	$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);
		$rID_bundle=sql_insert("pro_bundle_mst",$field_array_bundle,$data_array_bundle,1);
 		$rID_bundle_dtls=sql_insert("pro_bundle_dtls",$field_array_bundle_dtls,$data_array_bundle_dtls,1);
		
		// echo "10**insert into pro_garments_production_dtls $field_array_dtls values ($data_array_dtls)";die();
		// echo "10**";echo bulk_update_sql_statement("pro_gmts_cutting_qc_dtls","id",$field_array_qc_dtls,$data_array_qc_detls,$update_detail_arr);die;
		
		// echo "10**".$dtlsrDelete."**".$dtlsrDelete_bundle."**".$dtlsrDelete_bundle_dtls."**".$query."**".$rID_dtls_qc."**".$rID_mst_qc."**".$dtlsrID."**".$rID_bundle."**".$rID_bundle_dtls."**".$rejectDelete."**".$rID_dtlsqc;die;
		// 10**1**1**1**1**0**1**0**0**0**1**1
		
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($dtlsrDelete && $dtlsrDelete_bundle && $dtlsrDelete_bundle_dtls && $query && $rID_dtls_qc && $rID_mst_qc && $dtlsrID && $rID_bundle && $rID_bundle_dtls && $rejectDelete && $rID_dtlsqc)
			{
				mysql_query("COMMIT");  
			    echo "1**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_cutting_no)."";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_cutting_no)."";
			}
		}
		 
		if($db_type==2 || $db_type==1 )
		{
		    if($dtlsrDelete && $dtlsrDelete_bundle && $dtlsrDelete_bundle_dtls && $query && $rID_dtls_qc && $rID_mst_qc && $dtlsrID && 
		    $rID_bundle && $rID_bundle_dtls && $rID_dtlsqc)
		    {
				oci_commit($con); 
				echo "1**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_cutting_no)."";
			}
			else
		    {
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_cutting_no)."";
		    }
		 }
			disconnect($con);
			die;
	   }
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
	}		
}


if($action=="cutting_number_popup")
{
  	echo load_html_head_contents("Batch Info","../../", 1, 1, '','1','');
	extract($_REQUEST);
?>
	<script>
		function js_set_cutting_value(strCon ) 
		{
			
		document.getElementById('update_mst_id').value=strCon;
		parent.emailwindow.hide();
		}
    </script>
</head>
<body>
<div align="center" style="width:100%; overflow-y:hidden;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="950" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>
                <tr>                	 
                    <th width="140">Company name</th>
                    <th width="130">Cutting No</th>
                    <th width="130">Job No</th>
                    <th width="130">Order No</th>
                    <th width="250">Date Range</th>
                    <th width="120"><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                  <tr>                    
                        <td>
                              <? 
                        
                                   echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --",$company_id, "");
                             ?>
                        </td>
                      
                        <td align="center" >
                                <input type="text" id="txt_cut_no" name="txt_cut_no" style="width:120px"  class="text_boxes"/>
                                <input type="hidden" id="update_mst_id" name="update_mst_id" />
                        </td>
                        <td align="center">
                               <input name="txt_job_search" id="txt_job_search" class="text_boxes" style="width:120px"  />
                        </td>
                        <td align="center">
                               <input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:120px"  />
                        </td>
                        <td align="center" width="250">
                               <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date" />
                               <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date" />
                        </td>
                        <td align="center">
                               <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value, 'create_cutting_search_list_view', 'search_div', 'cutting_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                        </td>
                 </tr>
        		 <tr>                  
                        <td align="center" height="40" valign="middle" colspan="6">
                            <? echo load_month_buttons(1);  ?>
                        </td>
                </tr>   
            </tbody>
         </tr>         
      </table> 
     <div align="center" valign="top" id="search_div"> </div>  
  </form>
</div>    
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_cutting_search_list_view")
{
	
    $ex_data = explode("_",$data);
	$company = $ex_data[0];	
	$cutting_no = $ex_data[1];
	$job_no = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$cut_year= $ex_data[5];
	$order_no= $ex_data[6];
    if($db_type==2) { $year_cond=" and extract(year from a.insert_date)=$cut_year"; $year=" extract(year from a.insert_date) as year ";}
    if($db_type==0) {$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cut_year"; $year=" SUBSTRING_INDEX(a.insert_date, '-', 1) as year ";}
	if(str_replace("'","",$company)==0) $conpany_cond=""; else $conpany_cond="and a.company_id=".str_replace("'","",$company)."";
	if(str_replace("'","",$cutting_no)=="") $cut_cond=""; else $cut_cond="and a.cut_num_prefix_no='".str_replace("'","",$cutting_no)."'";//  $year_cond
	if(str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and b.job_no_prefix_num='".str_replace("'","",$job_no)."'";
	if(str_replace("'","",$order_no)=="") $order_cond=""; else $order_cond="and c.po_number like '%".trim($order_no)."%' ";
	if( $from_date!="" && $to_date!="" )
	{
		if($db_type==0)
	       {
			   $sql_cond= " and entry_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		   }
	  if($db_type==2)
	       {
			    $sql_cond= " and entry_date  between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
		   }
	}
	
	$production_sql=return_library_array( "SELECT b.cut_no from pro_garments_production_mst a,pro_garments_production_dtls b  where a.id=b.mst_id and  a.production_type=1 and a.status_active=1 and a.is_deleted=0  and b.production_type=1 and b.status_active=1 and b.is_deleted=0 $conpany_cond group by b.cut_no ", "cut_no", "cut_no"  );
	//print_r($production_sql);

	$sql_order="SELECT a.id,a.cut_num_prefix_no,a.cutting_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.marker_length, a.marker_width, a.fabric_width,c.id as order_id,d.color_id,$year FROM ppl_cut_lay_mst a,wo_po_details_master b,wo_po_break_down c,ppl_cut_lay_dtls d where  a.id=d.mst_id and a.job_no=b.job_no and b.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.status_active=1 and d.status_active=1 and d.is_deleted=0  $conpany_cond $year_cond $cut_cond $job_cond $sql_cond $order_cond order by id desc"; // and c.id=d.order_id
	// echo $sql_order;

	$ppl_mst_id_arr=array();
	foreach(sql_select($sql_order) as $vals)
	{
		$cutting=$vals[csf("cutting_no")];
		if($production_sql[$cutting])
		{
			$ppl_mst_id_arr[$vals[csf("id")]]=$vals[csf("id")];
		}

	}
	$new_sql_cond="";
	$ppl_mst_ids=implode(",", $ppl_mst_id_arr);
	if($ppl_mst_ids && count($ppl_mst_id_arr)>999 )
	{
		$chnk_arr=array_chunk($ppl_mst_id_arr, 999);
		foreach($chnk_arr as $v)
		{
			$ids=implode(",",$v);
			$new_sql_cond.=" and a.id not in($ids)";


		}

		 //$new_sql_cond= " and a.id not in($ppl_mst_ids) ";
	}
	else if($ppl_mst_ids && count($ppl_mst_id_arr)<999)
	{
		 $new_sql_cond= " and a.id not in($ppl_mst_ids) ";
	}

	$sql_order="SELECT a.id,a.cut_num_prefix_no,a.cutting_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.marker_length, a.marker_width, a.fabric_width,c.id as order_id,d.color_id,$year FROM ppl_cut_lay_mst a,wo_po_details_master b,wo_po_break_down c,ppl_cut_lay_dtls d where  a.id=d.mst_id and a.job_no=b.job_no and b.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.status_active=1 and d.status_active=1 and d.is_deleted=0  $conpany_cond $year_cond $cut_cond $job_cond $sql_cond $order_cond $new_sql_cond order by id desc";
	// echo $sql_order;

	$table_no_arr=return_library_array( "select id,table_no  from  lib_cutting_table",'id','table_no');
	$arr=array(2=>$table_no_arr,4=>$order_number_arr,5=>$color_arr);
	echo create_list_view("list_view", "Cut No,Year,Table No,Job No,Order NO,Color,Marker Length,Markar Width,Fabric Width,Entry Date","90,50,60,120,120,100,80,80,80,120","950","270",0, $sql_order , "js_set_cutting_value", "cutting_no", "", 1, "0,0,table_no,0,order_id,color_id,0,0,0,0,0", $arr, "cut_num_prefix_no,year,table_no,job_no,order_id,color_id,marker_length,marker_width,fabric_width,entry_date", "","setFilterGrid('list_view',-1)") ;	

}


if($action=="system_number_popup")
{
  	echo load_html_head_contents("Batch Info","../../", 1, 1, '','1','');
	extract($_REQUEST);
?>
	<script>
		function js_set_system_value(strCon ) 
		{
		document.getElementById('update_mst_id').value=strCon;
		parent.emailwindow.hide();
		}
    </script>
</head>
<body>
<div align="center" style="width:100%; overflow-y:hidden;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="950" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>                	 
                    <th width="130">Company name</th>
                    <th width="120">Cutting QC No</th>
                    <th width="120">Cutting No</th>
                    <th width="120">Job No</th>
                    <th width="120">Order No</th>
                    <th width="180">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                  <tr class="general">                    
                        <td>
                              <? 
                        
                                 echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --","", "");
                             ?>
                        </td>
                        <td align="center">
                               <input name="txt_cut_qc" id="txt_cut_qc" class="text_boxes" style="width:100px"  placeholder="Write"/>
                        </td>
                        <td align="center" >
                                <input type="text" id="txt_cut_no" name="txt_cut_no" style="width:100px"  class="text_boxes" placeholder="Write"/>
                                <input type="hidden" id="update_mst_id" name="update_mst_id" />
                        </td>
                        <td align="center">
                               <input name="txt_job_search" id="txt_job_search" class="text_boxes" style="width:100px"  placeholder="Write"/>
                        </td>
                        
                        <td align="center">
                               <input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:100px" placeholder="Write" />
                        </td>
                       
                        <td align="center">
                               <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                               <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                        </td>
                        <td align="center">
                               <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_cut_qc').value+'_'+document.getElementById('txt_order_search').value, 'create_system_search_list_view', 'search_div', 'cutting_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                        </td>
                 </tr>
        		 <tr>                  
                        <td align="center" height="40" valign="middle" colspan="7">
                            <? echo load_month_buttons(1);  ?>
                        </td>
                </tr>   
            </tbody>
         </tr>         
      </table> 
     <div align="center" valign="top" id="search_div"> </div>  
  </form>
</div>    
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_system_search_list_view")
{
    $ex_data = explode("_",$data);
	$company = $ex_data[0];	
	$cutting_no = $ex_data[1];
	$job_no = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$cut_year= $ex_data[5];
	$system_no= $ex_data[6];
	$order_no= $ex_data[7];
	if(str_replace("'","",$company)==0) { echo "Please select company First"; die;}
    if($db_type==2){ $year_cond=" and extract(year from a.insert_date)=$cut_year"; $year=" extract(year from a.insert_date) as year";}
    if($db_type==0) { $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cut_year"; $year=" SUBSTRING_INDEX(a.insert_date, '-', 1) as year";}
	if(str_replace("'","",$company)==0) $conpany_cond=""; else $conpany_cond="and a.company_id=".str_replace("'","",$company)."";
	if(str_replace("'","",$cutting_no)=="") $cut_cond=""; else $cut_cond="and b.cut_num_prefix_no='".str_replace("'","",$cutting_no)."'  ";
	if(str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and c.job_no_prefix_num='".str_replace("'","",$job_no)."'";
	if(str_replace("'","",$system_no)=="") $system_cond=""; else $system_cond="and a.cut_qc_prefix_no=".trim($system_no)." $year_cond";
	if(str_replace("'","",$order_no)=="") $order_cond=""; else $order_cond=" and d.po_number='".str_replace("'","",$order_no)."'";
	
	if( $from_date!="" && $to_date!="" )
	{
		if($db_type==0)
		{
			$sql_cond= " and a.cutting_qc_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		}
		if($db_type==2)
		{
			$sql_cond= " and a.cutting_qc_date  between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
		}
	}
	
	$sql_order="SELECT a.id,a.cutting_no,a.cut_qc_prefix_no,a.cutting_qc_no, a.table_no, a.job_no, a.batch_id, a.cutting_qc_date, a.marker_length, a.marker_width, a.fabric_width,c.job_no_prefix_num,b.cut_num_prefix_no,$year, d.po_number
    FROM pro_gmts_cutting_qc_mst a, ppl_cut_lay_mst b,wo_po_details_master c,wo_po_break_down d
    where a.cutting_no=b.cutting_no and a.job_no=b.job_no and a.job_no=c.job_no and c.job_no=d.job_no_mst a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $conpany_cond  $cut_cond $job_cond $sql_cond $order_cond $system_cond order by cutting_qc_date desc";
	//echo $sql_order;
	$table_no_arr=return_library_array( "select id,table_no  from  lib_cutting_table",'id','table_no');
	//$order_library=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number"); and a.job_no=c.job_no
	$arr=array(3=>$table_no_arr);
	echo create_list_view("list_view", "Cutting QC No,Year,Cut No,Table No,Job No,Order No,Batch No,Marker Length,Markar Width,Fabric Width,Cutting QC Date","60,60,60,80,100,100,80,80,80,80","950","270",0, $sql_order , "js_set_system_value", "cutting_qc_no,cutting_no", "", 1, "0,0,0,table_no,0,0,0,0,0,0,0", $arr, "cut_qc_prefix_no,year,cut_num_prefix_no,table_no,job_no,po_number,batch_id,marker_length,marker_width,fabric_width,cutting_qc_date", "","setFilterGrid('list_view',-1)","0,0,0,0,0,0,0,0,0,0,3") ;	
}

if($action=="load_system_mst_form")
{  
    if($db_type==0) $cutting_hour=" TIME_FORMAT(a.cutting_qc_time, '%H:%i' ) as cutting_qc_time";
	if($db_type==2) $cutting_hour=" TO_CHAR(a.cutting_qc_time,'HH24:MI') as cutting_qc_time";
	$company_arr=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0 ",'id','company_name');
	$sql_qc_mst=sql_select("select a.id,a.cut_qc_prefix,a.cut_qc_prefix_no,a.cutting_qc_no,a.cutting_no,a.location_id,a.floor_id,a.table_no, a.job_no, a.batch_id, a.company_id,a.entry_date,a.start_time,a.end_date,a.end_time,a.marker_length,a.marker_width,a.fabric_width,a.gsm, a.width_dia, a.cutting_qc_date, $cutting_hour, a.table_no,a.location_id,a.floor_id,a.production_source,a.serving_company,a.work_order_id,a.workorder_rate_breakdown,a.remarks from pro_gmts_cutting_qc_mst a where a.cutting_qc_no='".$data."' and a.status_active=1 and a.is_deleted=0");
	
	foreach($sql_qc_mst as $val)
	{	
		$working_company=return_field_value("working_company_id","ppl_cut_lay_mst "," cutting_no='".$val[csf("cutting_no")]."' and status_active=1 and is_deleted=0 ","working_company_id");
		$start_time=explode(":",$val[csf("start_time")]);
		$end_time=explode(":",$val[csf("end_time")]);
		echo "document.getElementById('txt_lay_company').value = '".$company_arr[$working_company]."';\n"; 
		echo "load_drop_down( 'requires/cutting_entry_controller', '".($val[csf("serving_company")])."', 'load_drop_down_location', 'location_td' );\n";
		echo "document.getElementById('txt_system_no').value = '".($val[csf("cutting_qc_no")])."';\n"; 
		echo "document.getElementById('cbo_company_name').value = '".($val[csf("company_id")])."';\n"; 
		echo "document.getElementById('txt_table_no').value = '".($val[csf("table_no")])."';\n"; 
		echo "$('#txt_entry_date').val('".change_date_format($val[csf("entry_date")])."');\n";  
		echo "$('#txt_end_date').val('".change_date_format($val[csf("end_date")])."');\n"; 
		echo "document.getElementById('txt_marker_length').value  = '".($val[csf("marker_length")])."';\n"; 
		echo "document.getElementById('txt_marker_width').value  = '".($val[csf("marker_width")])."';\n";  
		echo "document.getElementById('txt_fabric_width').value = '".($val[csf("fabric_width")])."';\n";    
		echo "document.getElementById('txt_gsm').value  = '".($val[csf("gsm")])."';\n";
		echo "document.getElementById('cbo_width_dia').value  = '".($val[csf("width_dia")])."';\n";
		echo "document.getElementById('cbo_location_name').value  = '".($val[csf("location_id")])."';\n";
		echo "load_drop_down( 'requires/cutting_entry_controller', '".($val[csf("location_id")])."', 'load_drop_down_floor', 'floor_td' );\n";   
		echo "document.getElementById('txt_batch_no').value = '".($val[csf("batch_id")])."';\n"; 
		echo "document.getElementById('txt_job_no').value = '".($val[csf("job_no")])."';\n"; 
		echo "document.getElementById('txt_cutting_date').value = '".change_date_format(($val[csf("cutting_qc_date")]))."';\n"; 
		echo "document.getElementById('txt_cutting_hour').value = '".($val[csf("cutting_qc_time")])."';\n"; 
		echo "document.getElementById('txt_remarks').value = '".($val[csf("remarks")])."';\n"; 
		echo "document.getElementById('txt_cut_prifix').value  = '".($val[csf("cut_num_prefix_no")])."';\n";  
		echo "document.getElementById('update_id').value = '".($val[csf("id")])."';\n";
		echo "document.getElementById('txt_cutting_no').value = '".($val[csf("cutting_no")])."';\n"; 
		echo "document.getElementById('cbo_floor_name').value  = '".($val[csf("floor_id")])."';\n";  
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_cut_lay_info',1);\n"; 
		echo "document.getElementById('txt_in_time_hours').value  = '".($start_time[0])."';\n";  
		echo "document.getElementById('txt_in_time_minuties').value = '".($start_time[1])."';\n";
		echo "document.getElementById('txt_out_time_hours').value = '".($end_time[0])."';\n"; 
		echo "document.getElementById('txt_out_time_minuties').value  = '".($end_time[1])."';\n";  
		echo "document.getElementById('cbo_floor_name').value  = '".($val[csf("floor_id")])."';\n"; 
		
		if($db_type==0)
		{
			$allorder_id=return_field_value("group_concat(distinct(a.order_id)) as order_id","ppl_cut_lay_dtls a,ppl_cut_lay_mst b","  b.id=a.mst_id and b.cutting_no='".$val[csf("cutting_no")]."' and status_active=1 and is_deleted=0 ","order_id");
		}
		else
		{
			$allorder_id=return_field_value("listagg(a.order_id,',') within group (order by a.order_id) as order_id","ppl_cut_lay_dtls a,ppl_cut_lay_mst b"," b.id=a.mst_id and b.cutting_no='".$val[csf("cutting_no")]."' and b.status_active=1 and b.is_deleted=0 ","order_id");
		}
		echo "document.getElementById('all_order_id').value  = '".$allorder_id."';\n"; 
		echo "document.getElementById('cbo_source').value  = '".($val[csf("production_source")])."';\n"; 
		echo "load_drop_down( 'requires/cutting_entry_controller', '".$val[csf('production_source')]."', 'load_drop_down_cutt_company', 'cutt_company_td' );\n";
		echo "document.getElementById('cbo_cutting_company').value  = '".($val[csf("serving_company")])."';\n"; 
		echo "load_drop_down( 'requires/cutting_entry_controller', '".$val[csf('company_id')]."_".$val[csf("serving_company")]."_".$allorder_id."', 'load_drop_down_workorder', 'workorder_td' );\n";
		
		
		echo "document.getElementById('cbo_work_order').value  = '".($val[csf("work_order_id")])."';\n";
		echo "document.getElementById('piece_rate_data_string').value  = '".($val[csf("workorder_rate_breakdown")])."';\n";  
		//echo "select distinct c.id,c.buyer_name from  wo_po_break_down a,wo_po_details_master b,lib_buyer c where  a.job_no_mst=b.job_no and b.job_no='".$val[csf("job_no")]."' and b.buyer_name=c.id and a.status_active=1"; 
		if($db_type==0){ $insert_year="SUBSTRING_INDEX(b.insert_date, '-', 1) as year";}
		if($db_type==2){ $insert_year="extract(year from b.insert_date) as year";}
		
		$sql=sql_select("select distinct c.id,c.buyer_name,$insert_year from  wo_po_break_down a,wo_po_details_master b,lib_buyer c where  a.job_no_mst=b.job_no and b.job_no='".$val[csf("job_no")]."' and b.buyer_name=c.id and a.status_active=1");
	
		foreach($sql as $row)
		{
			echo "document.getElementById('txt_buyer_name').value = '".$row[csf("id")]."';\n"; 
			echo "document.getElementById('txt_job_year').value = '".$row[csf("year")]."';\n";   
		}
	}
}

if($action=="load_php_mst_form")
{  
    if($db_type==0) $cutting_hour=" TIME_FORMAT(a.cutting_qc_time, '%H:%i' ) as cutting_qc_time";
	if($db_type==2) $cutting_hour=" TO_CHAR(a.cutting_qc_time,'HH24:MI') as cutting_qc_time";
	$company_arr=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0 ",'id','company_name'); 
//echo "select a.id,a.cut_qc_prefix,a.cut_qc_prefix_no,a.cutting_qc_no,a.cutting_no,a.location_id, a.floor_id,a.table_no,a.job_no, a.batch_id,a.company_id, a.entry_date, a.start_time,a.end_date,a.end_time ,a.marker_length,a.marker_width,a.fabric_width,a.gsm, a.width_dia,a.table_no, a.production_source,a.serving_company, a.cutting_qc_date, $cutting_hour, a.location_id, a.floor_id,a.work_order_id,a.workorder_rate_breakdown,a.remarks from pro_gmts_cutting_qc_mst a where a.cutting_no='".$data."' and a.status_active=1 and a.is_deleted=0";


   	$sql_qc_mst=sql_select("select a.id,a.cut_qc_prefix,a.cut_qc_prefix_no,a.cutting_qc_no,a.cutting_no,a.location_id, a.floor_id,a.table_no,a.job_no, a.batch_id,a.company_id, a.entry_date, a.start_time,a.end_date,a.end_time ,a.marker_length,a.marker_width,a.fabric_width,a.gsm, a.width_dia,a.table_no, a.production_source,a.serving_company, a.cutting_qc_date, $cutting_hour, a.location_id, a.floor_id,a.work_order_id,a.workorder_rate_breakdown,a.remarks from pro_gmts_cutting_qc_mst a where a.cutting_no='".$data."' and a.status_active=1 and a.is_deleted=0");
   
	if(count($sql_qc_mst)>0)
	{
		$working_company=return_field_value("working_company_id","ppl_cut_lay_mst "," cutting_no='".$data."' and status_active=1 and is_deleted=0 ","working_company_id");
		foreach($sql_qc_mst as $val)
		{
			$start_time=explode(":",$val[csf("start_time")]);
			$end_time=explode(":",$val[csf("end_time")]);
			
			echo "load_drop_down( 'requires/cutting_entry_controller', '".($val[csf("serving_company")])."', 'load_drop_down_location', 'location_td' );\n";
			echo "document.getElementById('txt_lay_company').value = '".$company_arr[$val[csf("working_company_id")]]."';\n";
			
			echo "document.getElementById('txt_system_no').value = '".($val[csf("cutting_qc_no")])."';\n"; 
			echo "document.getElementById('cbo_company_name').value = '".($val[csf("company_id")])."';\n"; 
			echo "document.getElementById('txt_table_no').value = '".($val[csf("table_no")])."';\n"; 
			echo "$('#txt_entry_date').val('".change_date_format($val[csf("entry_date")])."');\n";  
			echo "$('#txt_end_date').val('".change_date_format($val[csf("end_date")])."');\n"; 
			echo "document.getElementById('txt_marker_length').value  = '".($val[csf("marker_length")])."';\n"; 
			echo "document.getElementById('txt_marker_width').value  = '".($val[csf("marker_width")])."';\n";  
			echo "document.getElementById('txt_fabric_width').value = '".($val[csf("fabric_width")])."';\n";    
			echo "document.getElementById('txt_gsm').value  = '".($val[csf("gsm")])."';\n";
			echo "document.getElementById('cbo_width_dia').value  = '".($val[csf("width_dia")])."';\n";
			
			echo "document.getElementById('cbo_location_name').value  = '".($val[csf("location_id")])."';\n"; 
			echo "load_drop_down( 'requires/cutting_entry_controller', '".($val[csf("location_id")])."', 'load_drop_down_floor', 'floor_td' );\n"; 
			echo "document.getElementById('txt_batch_no').value = '".($val[csf("batch_id")])."';\n"; 
			echo "document.getElementById('txt_job_no').value = '".($val[csf("job_no")])."';\n"; 
			echo "document.getElementById('txt_cutting_date').value = '".change_date_format(($val[csf("cutting_qc_date")]))."';\n"; 
			echo "document.getElementById('txt_cutting_hour').value = '".($val[csf("cutting_qc_time")])."';\n"; 
			echo "document.getElementById('txt_remarks').value = '".($val[csf("remarks")])."';\n"; 
			echo "document.getElementById('txt_cut_prifix').value  = '".($val[csf("cut_num_prefix_no")])."';\n";  
			echo "document.getElementById('update_id').value = '".($val[csf("id")])."';\n";
			echo "document.getElementById('txt_cutting_no').value = '".($val[csf("cutting_no")])."';\n"; 
			echo "document.getElementById('cbo_floor_name').value  = '".($val[csf("floor_id")])."';\n";  
			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_cut_lay_info',1);\n"; 
			echo "document.getElementById('txt_in_time_hours').value  = '".($start_time[0])."';\n";  
			echo "document.getElementById('txt_in_time_minuties').value = '".($start_time[1])."';\n";
			echo "document.getElementById('txt_out_time_hours').value = '".($end_time[0])."';\n"; 
			echo "document.getElementById('txt_out_time_minuties').value  = '".($end_time[1])."';\n";  
			echo "document.getElementById('cbo_floor_name').value  = '".($val[csf("floor_id")])."';\n";
			
			if($db_type==0)
			{
				$allorder_id=return_field_value("group_concat(distinct(a.order_id)) as order_id","ppl_cut_lay_dtls a,ppl_cut_lay_mst b","  b.id=a.mst_id and b.cutting_no='".$val[csf("cutting_no")]."' and status_active=1 and is_deleted=0 ","order_id");
			}
			else
			{
				$allorder_id=return_field_value("listagg(a.order_id,',') within group (order by a.order_id) as order_id","ppl_cut_lay_dtls a,ppl_cut_lay_mst b"," b.id=a.mst_id and b.cutting_no='".$val[csf("cutting_no")]."' and b.status_active=1 and b.is_deleted=0 ","order_id");
			}
			echo "document.getElementById('all_order_id').value  = '".$allorder_id."';\n"; 
			echo "document.getElementById('cbo_source').value  = '".($val[csf("production_source")])."';\n"; 
			echo "load_drop_down( 'requires/cutting_entry_controller', '".$val[csf('production_source')]."', 'load_drop_down_cutt_company', 'cutt_company_td' );\n";
			echo "document.getElementById('cbo_cutting_company').value  = '".($val[csf("serving_company")])."';\n"; 
			echo "load_drop_down( 'requires/cutting_entry_controller', '".$val[csf('company_id')]."_".$val[csf("serving_company")]."_".$allorder_id."', 'load_drop_down_workorder', 'workorder_td' );\n";
			//"_".($val[csf("serving_company")]."_".$allorder_id.
			//load_drop_down( 'requires/cutting_entry_controller', company+"_"+supplier_id+"_"+po_break_down_id, 'load_drop_down_workorder', 'workorder_td' );
			echo "document.getElementById('cbo_work_order').value  = '".($val[csf("work_order_id")])."';\n";
			echo "document.getElementById('piece_rate_data_string').value  = '".($val[csf("workorder_rate_breakdown")])."';\n"; 
			
		
			
			//echo "document.getElementById('all_order_id').value  = '".$allorder_id."';\n";  
		
			if($db_type==0){ $insert_year="SUBSTRING_INDEX(b.insert_date, '-', 1) as year";}
			if($db_type==2){ $insert_year="extract(year from b.insert_date) as year";}

			$sql=sql_select("select distinct c.id,c.buyer_name,$insert_year from  wo_po_break_down a,wo_po_details_master b,lib_buyer c where  a.job_no_mst=b.job_no and b.job_no='".$val[csf("job_no")]."' and b.buyer_name=c.id and a.status_active=1");
			
			foreach($sql as $row)
		   	{
				echo "document.getElementById('txt_buyer_name').value = '".$row[csf("id")]."';\n"; 
				echo "document.getElementById('txt_job_year').value = '".$row[csf("year")]."';\n";   
		   	}
		}
	}
	else
	{
		$sql_data=sql_select("select b.id as tbl_id,b.table_no,b.location_id,b.floor_id,a.id,a.job_no,a.company_id,a.entry_date, end_date, a.marker_length, a.marker_width,a.fabric_width,a.gsm,a.width_dia,a.cutting_no, a.batch_id,a.start_time,a.end_time, a.cut_num_prefix_no,a.working_company_id from  ppl_cut_lay_mst a left join lib_cutting_table b on  a.table_no=b.id where  a.cutting_no='".$data."' ");
		
		foreach($sql_data as $val)
		{
			$start_time=explode(":",$val[csf("start_time")]);
			$end_time=explode(":",$val[csf("end_time")]);
			echo "load_drop_down( 'requires/cutting_entry_controller', '".($val[csf("working_company_id")])."', 'load_drop_down_location', 'location_td' );\n";
			echo "document.getElementById('cbo_source').value = '1';\n"; 
			echo "load_drop_down( 'requires/cutting_entry_controller', '1**'+$('#working_company_id').val(), 'load_drop_down_cutt_company', 'cutt_company_td' );";
			echo "document.getElementById('cbo_cutting_company').value = '".($val[csf("working_company_id")])."';\n"; 
			
			echo "document.getElementById('txt_lay_company').value = '".$company_arr[$val[csf("working_company_id")]]."';\n";
			echo "document.getElementById('cbo_company_name').value = '".($val[csf("company_id")])."';\n"; 
			echo "document.getElementById('txt_table_no').value = '".($val[csf("table_no")])."';\n"; 
			echo "$('#txt_entry_date').val('".change_date_format($val[csf("entry_date")])."');\n";  
			echo "$('#txt_end_date').val('".change_date_format($val[csf("end_date")])."');\n"; 
			echo "document.getElementById('txt_marker_length').value  = '".($val[csf("marker_length")])."';\n"; 
			echo "document.getElementById('txt_marker_width').value  = '".($val[csf("marker_width")])."';\n";  
			echo "document.getElementById('txt_fabric_width').value = '".($val[csf("fabric_width")])."';\n";    
			echo "document.getElementById('txt_gsm').value  = '".($val[csf("gsm")])."';\n";
			echo "document.getElementById('cbo_width_dia').value  = '".($val[csf("width_dia")])."';\n";
			echo "document.getElementById('cbo_location_name').value  = '".($val[csf("location_id")])."';\n";
			echo "load_drop_down( 'requires/cutting_entry_controller', '".($val[csf("location_id")])."', 'load_drop_down_floor', 'floor_td' );\n";  
			echo "document.getElementById('txt_batch_no').value = '".($val[csf("batch_id")])."';\n"; 
			echo "document.getElementById('txt_job_no').value = '".($val[csf("job_no")])."';\n"; 
			echo "document.getElementById('txt_cut_prifix').value  = '".($val[csf("cut_num_prefix_no")])."';\n";  
			echo "document.getElementById('update_id').value = '".($val[csf("id")])."';\n";
			echo "document.getElementById('txt_cutting_no').value = '".($val[csf("cutting_no")])."';\n"; 
			echo "document.getElementById('cbo_floor_name').value  = '".($val[csf("floor_id")])."';\n";  
			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_cut_lay_info',1);\n"; 
			echo "document.getElementById('txt_in_time_hours').value  = '".($start_time[0])."';\n";  
			echo "document.getElementById('txt_in_time_minuties').value = '".($start_time[1])."';\n";
			echo "document.getElementById('txt_out_time_hours').value = '".($end_time[0])."';\n"; 
			echo "document.getElementById('txt_out_time_minuties').value  = '".($end_time[1])."';\n";  
			echo "document.getElementById('cbo_floor_name').value  = '".($val[csf("floor_id")])."';\n"; 
			if($db_type==0){ $insert_year="SUBSTRING_INDEX(b.insert_date, '-', 1) as year";}
			if($db_type==2){ $insert_year="extract(year from b.insert_date) as year";}

			if($db_type==0)
			{
				$allorder_id=return_field_value("group_concat(distinct(order_id)) as order_id","ppl_cut_lay_dtls","mst_id=".$val[csf("id")]." and status_active=1 and is_deleted=0 ","order_id");
			}
			else
			{
				$allorder_id=return_field_value("listagg(order_id,',') within group (order by order_id) as order_id","ppl_cut_lay_dtls","mst_id=".$val[csf("id")]." and status_active=1 and is_deleted=0 ","order_id");
			}
			
			echo "document.getElementById('all_order_id').value  = '".$allorder_id."';\n"; 
			//echo $allorder_id."**";die;
			$sql=sql_select("select distinct c.id,c.buyer_name,$insert_year from  wo_po_break_down a,wo_po_details_master b,lib_buyer c where  a.job_no_mst=b.job_no and b.job_no='".$val[csf("job_no")]."' and b.buyer_name=c.id and a.status_active=1");
			
			foreach($sql as $row)
			{
				echo "document.getElementById('txt_buyer_name').value = '".$row[csf("id")]."';\n"; 
				echo "document.getElementById('txt_job_year').value = '".$row[csf("year")]."';\n";   
			}
			echo "document.getElementById('all_order_id').value  = '".$allorder_id."';\n"; 
			echo "load_drop_down( 'requires/cutting_entry_controller', '".$val[csf('company_id')]."_".$val[csf("working_company_id")]."_".$allorder_id."', 'load_drop_down_workorder', 'workorder_td' );\n";
			
			echo "document.getElementById('cbo_work_order').value  = '".($val[csf("work_order_id")])."';\n";
			echo "document.getElementById('piece_rate_data_string').value  = '".($val[csf("workorder_rate_breakdown")])."';\n";
		}
	}
}

if($action=="order_details_list")
{
	
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	
	$cutt_qc_dtls=sql_select(" select a.id,a.mst_id,a.order_id,a.color_id,a.size_id,a.color_size_id,a.bundle_no,a.number_start,a.number_end,a.bundle_qty,a.reject_qty,a.replace_qty,a.qc_pass_qty from pro_gmts_cutting_qc_dtls a,pro_gmts_cutting_qc_mst b where a.mst_id=b.id and b.cutting_no='".$data."' and  b.status_active=1 and b.is_deleted=0");
	$qc_details_arr=array();
	$total_qty=array();
	$order_color_qty=array();
	foreach($cutt_qc_dtls as $inf)
	{
		$qc_details_arr[$inf[csf("order_id")]][$inf[csf("color_id")]][$inf[csf("bundle_no")]]['qc_pass_qty']=$inf[csf("qc_pass_qty")];
		$qc_details_arr[$inf[csf("order_id")]][$inf[csf("color_id")]][$inf[csf("bundle_no")]]['update_id']=$inf[csf("id")];
		$qc_details_arr[$inf[csf("order_id")]][$inf[csf("color_id")]][$inf[csf("bundle_no")]]['reject_qty']=$inf[csf("reject_qty")];
		$qc_details_arr[$inf[csf("order_id")]][$inf[csf("color_id")]][$inf[csf("bundle_no")]]['replace_qty']=$inf[csf("replace_qty")];
		$order_color_qty[$inf[csf("order_id")]][$inf[csf("color_id")]]['qc_pass_qty']+=$inf[csf("qc_pass_qty")];
		$order_color_qty[$inf[csf("order_id")]][$inf[csf("color_id")]]['reject_qty']+=$inf[csf("reject_qty")];
		$order_color_qty[$inf[csf("order_id")]][$inf[csf("color_id")]]['replace_qty']+=$inf[csf("replace_qty")];
		$total_qty['qc_pass_qty']+=$inf[csf("qc_pass_qty")];
		$total_qty['reject_qty']+=$inf[csf("reject_qty")];
		$total_qty['replace_qty']+=$inf[csf("replace_qty")];
	}
	
	if(count($cutt_qc_dtls)!=0)
	{
		$j=1;
		$sql_bundle_reject=sql_select("select a.defect_type_id,a.production_type,a.defect_point_id,a.defect_qty,a.bundle_no from  pro_gmts_prod_dft a where a.production_type=1 and defect_type_id=3 ");
		$bundle_reject_data=array();
		foreach($sql_bundle_reject as $inf)
		{
			if($check_arr[$inf[csf('bundle_no')]]!='') //,$check_arr))
			{
			$bundle_reject_data[$inf[csf('bundle_no')]].="**".$inf[csf('defect_point_id')]."*".$inf[csf('defect_qty')]; 
			}
			else
			{
			$bundle_reject_data[$inf[csf('bundle_no')]]=$inf[csf('defect_point_id')]."*".$inf[csf('defect_qty')];  
			}
			$check_arr[$inf[csf('bundle_no')]]=$inf[csf('bundle_no')]; 
		}
		 
		$sql_dtls=sql_select("select b.id,a.order_id,a.ship_date,a.color_id,a.gmt_item_id,a.plies,a.marker_qty,a.order_qty,a.total_lay_qty,
		a.lay_balance_qty,b.job_no,b.job_year,b.company_id ,a.id as details_id from ppl_cut_lay_dtls a, ppl_cut_lay_mst b,ppl_cut_lay_bundle c
		where b.id=a.mst_id and b.cutting_no='".$data."' and b.id=c.mst_id and a.id=c.dtls_id 
		group by b.id,a.order_id,a.ship_date,a.color_id,a.gmt_item_id,a.plies,a.marker_qty,a.order_qty,a.total_lay_qty,
		a.lay_balance_qty,b.job_no,b.job_year,b.company_id ,a.id order by a.id");
		?>
         	<table cellpadding="0" cellspacing="0" width="550" class="" border="1" rules="all" id="" align="center">
            	<tr style=" font-size:larger;color:#093">
                	<td width="150" style="font-size:larger;">Erase Bundle Qty</td>
                    <td width="120" style="font-size:larger;">From:
                    <input type="text" id="txt_erage_from" name="txt_erage_from" style="width:70px;" class="text_boxes_numeric" />
                    </td>
                    <td width="120" style="font-size:larger;">To:
                    <input type="text" id="txt_erage_to" name="txt_erage_to" class="text_boxes_numeric"  style="width:70px;" />
                    </td>
                    <td width="100"><input type="button" value="OK" style="width:80px"  class="formbutton" onClick="fnc_erage_qty()"/></td>
                </tr>
                <tr style=" font-size:larger;color:#093">
                	<td width="150" style="font-size:larger;">Replace Bundle Qty</td>
                    <td width="120" style="font-size:larger;">From:
                    <input type="text" id="txt_replace_from" name="txt_replace_from" style="width:70px;" class="text_boxes_numeric" />
                    </td>
                    <td width="120" style="font-size:larger;">To:
                    <input type="text" id="txt_replace_to" name="txt_replace_to" class="text_boxes_numeric"  style="width:70px;" />
                    </td>
                    <td width="100"><input type="button" value="OK" style="width:80px"  class="formbutton" onClick="fnc_replace_qty()"/></td>
                </tr>
            </table> 
        <? 
		$job_qty=0;
		foreach($sql_dtls as $val)
		{
		?>
			<div style="width:800px; margin-top:10px" id="" align="left"> 
				<table cellpadding="0" cellspacing="0" width="800" class="" border="1" rules="all" id="order_table_<? echo $j; ?>">  
					<tr >
						<td colspan="6">         
							 <b>  Order No:<? echo $order_number_arr[$val[csf('order_id')]]; ?> ;&nbsp; Gmt Item:<? echo $garments_item[$val[csf('gmt_item_id')]]; ?> ;&nbsp; Color:<? echo $color_arr[$val[csf('color_id')]]; ?>  ; &nbsp; </b><br/>  
							 <b> 
							   Ship Date:<? echo change_date_format($val[csf('ship_date')]); ?>;&nbsp;Order Qty:<? echo $val[csf('order_qty')]; ?></b>
							   <br/>
			  				<p style="color:red; font-size:12px; text-align:center">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  Double Click on Reject Qty Field for Defect Record</p>
						</td>
						<td>
					 	<?   
					 
						$size_total_sql=sql_select("select  a.country_id,sum(a.size_qty) as marker_qty,count(a.size_id) as bdl_no,max(a.size_qty) as pcs_per_bundle from ppl_cut_lay_bundle a, ppl_cut_lay_size b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.mst_id=".$val[csf('id')]." and a.dtls_id=".$val[csf('details_id')]."  group by a.country_id,b.size_id"); 
					    $s=1;
					    foreach($size_total_sql as $sval)
					    {
							if($s==1)
							{
								$dtls_size_qty=$sval[csf("marker_qty")];
								$bundle_no=$sval[csf("bdl_no")];
								$pcs_per_bundle=$sval[csf("pcs_per_bundle")];
							}
							else
							{
								$dtls_size_qty.="*".$sval[csf("marker_qty")];
								$bundle_no.="*".$sval[csf("bdl_no")];
								$pcs_per_bundle.="*".$sval[csf("pcs_per_bundle")];
							}
							$s++;
					    }
					?>             
					 <input type="hidden" name="txt_dtls_size_id_<? echo $j; ?>"  id="txt_dtls_size_id_<? echo $j; ?>"  value="<? echo $dtls_size_id; ?>"  />
					 <input type="hidden" name="txt_dtls_size_qty_<? echo $j; ?>"  id="txt_dtls_size_qty_<? echo $j; ?>"  value="<? echo $dtls_size_qty; ?>"  /> 
					 <input type="hidden" name="txt_dtls_size_bdl_<? echo $j; ?>"  id="txt_dtls_size_bdl_<? echo $j; ?>"  value="<? echo $bundle_no; ?>"  />                 <input type="hidden" name="txt__pcs_per_bdl_<? echo $j; ?>"  id="txt__pcs_per_bdl_<? echo $j; ?>"  value="<? echo $pcs_per_bundle; ?>"  />
					 <input type="hidden" name="txt_oder_qty_<? echo $j; ?>"  id="txt_oder_qty_<? echo $j; ?>"  value="<? echo $val[csf('order_qty')]; ?>"  />
					 <input type="hidden" name="hidden_po_<? echo $j; ?>"  id="hidden_po_<? echo $j; ?>"  value="<? echo $val[csf('order_id')]; ?>"  /> 
					 <input type="hidden" name="hidden_gmt_<? echo $j; ?>"  id="hidden_gmt_<? echo $j; ?>"  value="<? echo $val[csf('gmt_item_id')]; ?>"  />                
                      <input type="hidden" name="hidden_color_<? echo $j; ?>"  id="hidden_color_<? echo $j; ?>"  value="<? echo $val[csf('color_id')]; ?>"  />
							</td>
					 </tr>       
			  </table>   
			  <table cellpadding="0" cellspacing="0" width="650" class="rpt_table" border="1" rules="all" id="order_table_<? echo $j; ?>">
			  	<thead>
				  	<tr>
                        <th  width="30" rowspan="2" rclass="">SL</th>
                        <th width="100" rowspan="2">Country</th>
                        <th width="80" rowspan="2">Bundle No</th>
                        <th width="100" rowspan="2" >Size</th>
                        <th width="100" colspan="2">RMG No</th>
                        <th width="60" rowspan="2" class="">Bundle Qty </th>
                        <th width="50" rowspan="2">Reject Qty</th>
                        <th width="50" rowspan="2">Replace Qty</th>
                        <th width="60" rowspan="2">QC Pass Qty</th>
					  </tr> 
					  <tr>
                        <th  width="50"  >From</th>
                        <th width="50"  >To</th>
					 </tr>  
			   </thead>
			   <tbody id="tbl_body_<? echo $j; ?>">
		<?	 
				   $bundle_data=sql_select("select DISTINCT a.id,a.country_id,a.bundle_no,a.size_id,a.number_start,a.number_end,a.size_qty from ppl_cut_lay_bundle a, ppl_cut_lay_size b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.mst_id=".$val[csf('id')]." and a.dtls_id=".$val[csf('details_id')]."  order by a.id ASC");
					$i=1;
					$color_qty=0;
					foreach($bundle_data as $row)
					{
						$color_qty+=$row[csf('size_qty')];
						$job_qty+=$row[csf('size_qty')];
		?>
					 <tr id="table_tr_<? echo $j."_".$i; ?>">
						  <td id=""><? echo $i; ?>
						  <input type="hidden" id="hidden_order_<? echo $i; ?>"  name="hidden_order_<? echo $i; ?>" value="<? echo $val[csf('order_id')]; ?>"/>
						 <input type="hidden" name="size_id_<? echo $j."_".$i; ?>"  id="size_id_<? echo $j."_".$i; ?>"  value="<? echo $row[csf('size_id')]; ?>"  /> 
						 <input type="hidden" name="update_details_id_<? echo $j."_".$i; ?>"  id="update_details_id_<? echo $j."_".$i; ?>"  value="<? echo $qc_details_arr[$val[csf("order_id")]][$val[csf("color_id")]][$row[csf("bundle_no")]]['update_id'];  ?>"  />
						  <input type="hidden" name="hidden_country_<? echo $j."_".$i; ?>"  id="hidden_country_<? echo $j."_".$i; ?>"  value="<? echo $row[csf('country_id')]; ?>"  />
						  <input type="hidden" name="actual_reject_<? echo $j."_".$i; ?>"  id="actual_reject_<? echo $j."_".$i; ?>"  value="<? echo $bundle_reject_data[$row[csf('bundle_no')]]; ?>"  />
						  
						  </td>
						  <td align="center" id="txt_country_name_<? echo $j."_".$i; ?>"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
						  <td align="center" id="txt_bundle_<? echo $j."_".$i; ?>"><? echo $row[csf('bundle_no')]; ?></td> 
						  <td align="center" id="txt_size_<? echo $j."_".$i; ?>" ><? echo $size_arr[$row[csf('size_id')]]; ?>
						  </td>
						  <td align="right" id="txt_start_<? echo $j."_".$i; ?>"><? echo $row[csf('number_start')]; ?></td>
						  <td align="right" id="txt_end_<? echo $j."_".$i; ?>"><? echo $row[csf('number_end')]; ?></td>
						  <td align="right" id="txt_qty_<? echo $j."_".$i; ?>"><? echo $row[csf('size_qty')]; ?>
						  </td>
						  <td  align="center">
							   <input type="text" name="txt_reject_<? echo $j."_".$i; ?>"  id="txt_reject_<? echo $j."_".$i; ?>" class="text_boxes_numeric" style="width:68px"   onKeyUp="total_qc_pass(this.id,this.value,<? echo $j; ?>)"  onDblClick="pop_entry_reject(<? echo $j; ?>,<? echo $i; ?>)" value="<?  if($qc_details_arr[$val[csf("order_id")]][$val[csf("color_id")]][$row[csf("bundle_no")]]['reject_qty']!=0) echo $qc_details_arr[$val[csf("order_id")]][$val[csf("color_id")]][$row[csf("bundle_no")]]['reject_qty'];  ?>"/>                      </td>
							<td  align="center">
							 <input type="text" name="txt_replace_<? echo $j."_".$i; ?>"  id="txt_replace_<? echo $j."_".$i; ?>" class="text_boxes_numeric" style="width:50px"   onKeyUp="total_qc_pass(this.id,this.value,<? echo $j; ?>)" value="<?  if($qc_details_arr[$val[csf("order_id")]][$val[csf("color_id")]][$row[csf("bundle_no")]]['replace_qty']!=0) echo $qc_details_arr[$val[csf("order_id")]][$val[csf("color_id")]][$row[csf("bundle_no")]]['replace_qty'];  ?>" />                      </td>     
						 
						  <td align="center"> 
							   <input type="text" name="txt_qcpass_<? echo $j."_".$i; ?>"  id="txt_qcpass_<? echo $j."_".$i; ?>" class="text_boxes_numeric"  value="<? echo $qc_details_arr[$val[csf("order_id")]][$val[csf("color_id")]][$row[csf("bundle_no")]]['qc_pass_qty'];  ?>" style="width:68px"  readonly/> 
							   <input type="hidden" name="hidden_qcpass_<? echo $j."_".$i; ?>"  id="hidden_qcpass_<? echo $j."_".$i; ?>" class="text_boxes_numeric"  value="<? echo $row[csf('size_qty')]; ?>"  />  
						   </td>
					 </tr>
		<?       
					$i++;
					}
					?>
					</tbody>
					  <tr  style=" background-color:#B0B0B0;"  b height="10">
							  <td id="" ></td>
							  <td align="center"  ><?   //echo $j; ?></td>
							   <td align="center"  ><?   //echo $j; ?></td>
							  <td align="center" ><? //echo $row[csf('bundle_no')]; ?></td>
							  <td align="right" ><input type="hidden" id="hidden_total_qc_qty_<? echo $j; ?>" 
							   name="hidden_total_qc_qty_<? echo $j; ?>"  value="<? echo $order_color_qty[$val[csf("order_id")]][$val[csf("color_id")]]['qc_pass_qty'];  ?> "/></td>
							  <td align="right" ><input type="hidden" id="hidden_reject_qty_<? echo $j; ?>" 
							  name="hidden_reject_qty_<? echo $j; ?>"  value="<? echo $order_color_qty[$val[csf("order_id")]][$val[csf("color_id")]]['reject_qty'];  ?>" />
							  <input type="hidden" id="hidden_replace_qty_<? echo $j; ?>" 
							  name="hidden_replace_qty_<? echo $j; ?>"  value="<? echo $order_color_qty[$val[csf("order_id")]][$val[csf("color_id")]]['replace_qty'];  ?>" />
							  
							  </td>
							  <td align="right" >Total</td>
							  <td  align="right" id="total_reject_qty_<? echo $j; ?>"><? echo $order_color_qty[$val[csf("order_id")]][$val[csf("color_id")]]['reject_qty'];  ?></td>
							   <td  align="right" id="total_replace_qty_<? echo $j; ?>"> <? echo $order_color_qty[$val[csf("order_id")]][$val[csf("color_id")]]['replace_qty'];  ?></td>
							  <td align="right" id="total_qc_qty_<? echo $j; ?>"><? echo $order_color_qty[$val[csf("order_id")]][$val[csf("color_id")]]['qc_pass_qty'];  ?>
							  </td>
					 </tr>
				 <?
				$j++;	 
		   }
		 
		?>
			  <tfoot>
				 <tr class="general"  height="15" >
					  <th width="480"  align="right"  colspan="7"> Grand Total</th>
					  <th width="60"  align=" right" id="grand_reject_qty"><? echo $total_qty['reject_qty'];  ?> </th>
					  <th width="60"  align=" right" id="grand_replace_qty"> <? echo $total_qty['replace_qty'];  ?> </th>
					  <th  width="60" align="right" id="grand_qc_qty"><? echo $total_qty['qc_pass_qty'];  ?> </th> 
					  
					 
				 </tr>
			 </tfoot>
		  </table>
		  </div>
		
			 <table width="800" cellpadding="0" cellspacing="2" align="center">
				   <tr>
					   <td colspan="7" align="center" class="">
							<? 
							   echo load_submit_buttons( $permission, "fnc_cut_qc_info", 1,0,"reset_form('','','','','clear_tr()')",1);
							?>

							</td>
				  </tr>
				</table>  
			<input type="hidden" id="total_order_id" name="total_order_id" value="<? echo $j-1; ?>"  />
		<?
	}
	else
	{
	 	$j=1;
	
	 	$sql_dtls=sql_select("SELECT b.id,c.order_id,a.ship_date,a.color_id,a.gmt_item_id,a.plies,a.marker_qty,a.order_qty,a.total_lay_qty,
	 	a.lay_balance_qty,b.job_no,b.job_year,b.company_id ,a.id as details_id from ppl_cut_lay_dtls a, ppl_cut_lay_mst b,ppl_cut_lay_bundle c
	 	where b.id=a.mst_id and b.cutting_no='".$data."' and b.id=c.mst_id and a.id=c.dtls_id 
	 	group by b.id,c.order_id,a.ship_date,a.color_id,a.gmt_item_id,a.plies,a.marker_qty,a.order_qty,a.total_lay_qty,
	 	a.lay_balance_qty,b.job_no,b.job_year,b.company_id ,a.id
	 	order by b.id");
	 	?>
     		<table cellpadding="0" cellspacing="0" width="550" class="" border="1" rules="all" id="" align="center">
            	<tr style=" font-size:larger;color:#093">
                	<td width="150" style="font-size:larger;">Erase Bundle Qty</td>
                    <td width="120" style="font-size:larger;">From:
                    <input type="text" id="txt_erage_from" name="txt_erage_from" style="width:70px;" class="text_boxes_numeric" />
                    </td>
                    <td width="120" style="font-size:larger;">To:
                    <input type="text" id="txt_erage_to" name="txt_erage_to" class="text_boxes_numeric"  style="width:70px;" />
                    </td>
                    <td width="100"><input type="button" value="OK" style="width:80px"  class="formbutton" onClick="fnc_erage_qty()"/></td>
                </tr>
                <tr style=" font-size:larger;color:#093">
                	<td width="150" style="font-size:larger;">Replace Bundle Qty</td>
                    <td width="120" style="font-size:larger;">From:
                    <input type="text" id="txt_replace_from" name="txt_replace_from" style="width:70px;" class="text_boxes_numeric" />
                    </td>
                    <td width="120" style="font-size:larger;">To:
                    <input type="text" id="txt_replace_to" name="txt_replace_to" class="text_boxes_numeric"  style="width:70px;" />
                    </td>
                    <td width="100"><input type="button" value="OK" style="width:80px"  class="formbutton" onClick="fnc_replace_qty()"/></td>
                </tr>
            </table> 
     <?
	 $job_qty=0;
	 foreach($sql_dtls as $val)
	   {
		?>
     	<div style="width:800px; margin-top:10px" id="" align="left"> 
     	<table cellpadding="0" cellspacing="0" width="800" class="" border="1" rules="all" id="order_table_<? echo $j; ?>" >  
    	 <tr >
        		 <td colspan="6" >         
                         <b>  Order No:<? echo $order_number_arr[$val[csf('order_id')]]; ?> ;&nbsp; Gmt Item:<? echo $garments_item[$val[csf('gmt_item_id')]]; ?> ;&nbsp; Color:<? echo $color_arr[$val[csf('color_id')]]; ?>  ; 
                         
                           </b><br/>
                          <b> 
                           Ship Date:<? echo change_date_format($val[csf('ship_date')]); ?>;&nbsp;Order Qty:<? echo $val[csf('order_qty')]; ?>
                           </b>
                           
            <br/>
           <p style="color:red; font-size:12px; text-align:center">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  Double Click on Reject Qty Field for Defect Record</p>               
                           
                </td>
                <td>
                <? 
				 
                $size_total_sql=sql_select("SELECT  a.country_id,sum(a.size_qty) as marker_qty,count(a.size_id) as bdl_no,max(a.size_qty) as pcs_per_bundle from ppl_cut_lay_bundle a, ppl_cut_lay_size b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.mst_id=".$val[csf('id')]." and a.dtls_id=".$val[csf('details_id')]."  group by a.country_id,b.size_id"); 
				$s=1;
				foreach($size_total_sql as $sval)
				{		 			
					if($s==1)
					{
						
						$dtls_size_qty=$sval[csf("marker_qty")];
						$bundle_no=$sval[csf("bdl_no")];
						$pcs_per_bundle=$sval[csf("pcs_per_bundle")];
					}
					else
					{
						
						$dtls_size_qty.="*".$sval[csf("marker_qty")];
						$bundle_no.="*".$sval[csf("bdl_no")];
						$pcs_per_bundle.="*".$sval[csf("pcs_per_bundle")];
					}
					$s++;
				}
        ?>             
                 <input type="hidden" name="txt_dtls_size_id_<? echo $j; ?>"  id="txt_dtls_size_id_<? echo $j; ?>"  value="<? echo $dtls_size_id; ?>"  />
                 <input type="hidden" name="txt_dtls_size_qty_<? echo $j; ?>"  id="txt_dtls_size_qty_<? echo $j; ?>"  value="<? echo $dtls_size_qty; ?>"  /> 
                 <input type="hidden" name="txt_dtls_size_bdl_<? echo $j; ?>"  id="txt_dtls_size_bdl_<? echo $j; ?>"  value="<? echo $bundle_no; ?>"  />                 <input type="hidden" name="txt__pcs_per_bdl_<? echo $j; ?>"  id="txt__pcs_per_bdl_<? echo $j; ?>"  value="<? echo $pcs_per_bundle; ?>"  />
                 <input type="hidden" name="txt_oder_qty_<? echo $j; ?>"  id="txt_oder_qty_<? echo $j; ?>"  value="<? echo $val[csf('order_qty')]; ?>"  />
                 <input type="hidden" name="hidden_po_<? echo $j; ?>"  id="hidden_po_<? echo $j; ?>"  value="<? echo $val[csf('order_id')]; ?>"  /> 
                 <input type="hidden" name="hidden_gmt_<? echo $j; ?>"  id="hidden_gmt_<? echo $j; ?>"  value="<? echo $val[csf('gmt_item_id')]; ?>"  />                 <input type="hidden" name="hidden_color_<? echo $j; ?>"  id="hidden_color_<? echo $j; ?>"  value="<? echo $val[csf('color_id')]; ?>"  />
  
                    </td>
             </tr>       
      </table>   
      <table cellpadding="0" cellspacing="0" width="650" class="rpt_table" border="1" rules="all" id="order_table_<? echo $j; ?>">
          <thead >
                 <tr>
                        <th  width="30" rowspan="2" rclass="">SL</th>
                        <th width="100" rowspan="2">Country</th>
                        <th width="80" rowspan="2">Bundle No</th>
                        <th width="100" rowspan="2" >Size</th>
                        <th width="100" colspan="2">RMG No</th>
                        <th width="60" rowspan="2" class="">Bundle Qty </th>
                        <th width="50" rowspan="2">Reject Qty</th>
                        <th width="50" rowspan="2">Replace Qty</th>
                        <th width="60" rowspan="2">QC Pass Qty</th>
                        
                   </tr> 
                  <tr>
                        <th  width="50"  >From</th>
                        <th width="50"  >To</th>
                   </tr> 
         </thead>
        <tbody id="tbl_body_<? echo $j; ?>">
		<?	 
			//echo "select  a.country_id,a.id,a.bundle_no,a.size_id,a.number_start,a.number_end,a.size_qty from ppl_cut_lay_bundle a, ppl_cut_lay_size b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.mst_id=".$val[csf('id')]." and a.dtls_id=".$val[csf('details_id')]."  group by a.id,a.bundle_no,a.size_id,a.number_start,a.number_end,a.size_qty  order by a.id ASC";die;
				 $bundle_data=sql_select("SELECT  a.country_id,a.id,a.bundle_no,a.size_id,a.number_start,a.number_end,a.size_qty from ppl_cut_lay_bundle a, ppl_cut_lay_size b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.mst_id=".$val[csf('id')]." and a.dtls_id=".$val[csf('details_id')]."   group by a.country_id,a.id,a.bundle_no,a.size_id,a.number_start,a.number_end,a.size_qty  order by a.id ASC");
			    $i=1;
				$color_qty=0;
				foreach($bundle_data as $row)
				{
					$color_qty+=$row[csf('size_qty')];
					$job_qty+=$row[csf('size_qty')];
		?>
                 <tr id="table_tr_<? echo $j."_".$i; ?>">
                     <td id=""><? echo $i; ?>
                     <input type="hidden" id="hidden_order_<? echo $i; ?>"  name="hidden_order_<? echo $i; ?>" value="<? echo $val[csf('order_id')]; ?>"/>
                     <input type="hidden" name="size_id_<? echo $j."_".$i; ?>"  id="size_id_<? echo $j."_".$i; ?>"  value="<? echo $row[csf('size_id')]; ?>"  /> 
                      <input type="hidden" name="update_details_id_<? echo $j."_".$i; ?>"  id="update_details_id_<? echo $j."_".$i; ?>"  value="<? //echo $qc_details_arr[$val[csf("order_id")]][$val[csf("color_id")]][$row[csf("bundle_no")]]['update_id'];  ?>"  />
                     <input type="hidden" name="hidden_country_<? echo $j."_".$i; ?>"  id="hidden_country_<? echo $j."_".$i; ?>"  value="<? echo $row[csf('country_id')]; ?>"  />
                      <input type="hidden" name="actual_reject_<? echo $j."_".$i; ?>"  id="actual_reject_<? echo $j."_".$i; ?>"  value=""  />
                      </td>
                      <td align="center" id="txt_country_name_<? echo $j."_".$i; ?>"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
                      <td align="center" id="txt_bundle_<? echo $j."_".$i; ?>"><? echo $row[csf('bundle_no')]; ?></td>
                      <td align="center" id="txt_size_<? echo $j."_".$i; ?>" ><? echo $size_arr[$row[csf('size_id')]]; ?>
                      </td>
                      <td align="right" id="txt_start_<? echo $j."_".$i; ?>"><? echo $row[csf('number_start')]; ?></td>
                      <td align="right" id="txt_end_<? echo $j."_".$i; ?>"><? echo $row[csf('number_end')]; ?></td>
                      <td align="right" id="txt_qty_<? echo $j."_".$i; ?>"><? echo $row[csf('size_qty')]; ?>
                      </td>
                      <td  align="center">
                           <input type="text" name="txt_reject_<? echo $j."_".$i; ?>"  id="txt_reject_<? echo $j."_".$i; ?>" class="text_boxes_numeric" style="width:40px"   onKeyUp="total_qc_pass(this.id,this.value,<? echo $j; ?>)" onDblClick="pop_entry_reject(<? echo $j; ?>,<? echo $i; ?>)"/>                      </td>
                      <td  align="center">
                         <input type="text" name="txt_replace_<? echo $j."_".$i; ?>"  id="txt_replace_<? echo $j."_".$i; ?>" class="text_boxes_numeric" style="width:40px"   onKeyUp="total_qc_pass(this.id,this.value,<? echo $j; ?>)"/>                      </td>
                      <td align="center"> 
                           <input type="text" name="txt_qcpass_<? echo $j."_".$i; ?>"  id="txt_qcpass_<? echo $j."_".$i; ?>" class="text_boxes_numeric"  value="<? echo $row[csf('size_qty')]; ?>" style="width:50px"  readonly/> 
                           <input type="hidden" name="hidden_qcpass_<? echo $j."_".$i; ?>"  id="hidden_qcpass_<? echo $j."_".$i; ?>" class="text_boxes_numeric"  value="<? echo $row[csf('size_qty')]; ?>"   readonly/>  
                       </td>
                 </tr>
		<?       
            $i++;
		    }
			?>
            </tbody>
              <tr  style=" background-color:#B0B0B0;"  b height="10">
                      <td id="" ></td>
                      <td id="" ></td>
                      <td align="center"  ><? // echo $j; ?></td>
                      <td align="center" ><? //echo $row[csf('bundle_no')]; ?></td>
                      <td align="right" ><input type="hidden" id="hidden_total_qc_qty_<? echo $j; ?>" 
                       name="hidden_total_qc_qty_<? echo $j; ?>"  value="<? echo $color_qty;  ?> "/></td>
                      <td align="right" ><input type="hidden" id="hidden_reject_qty_<? echo $j; ?>" 
                      name="hidden_reject_qty_<? echo $j; ?>"  value="0" />
                      <input type="hidden" id="hidden_replace_qty_<? echo $j; ?>" 
						  name="hidden_replace_qty_<? echo $j; ?>"  value="0" />
                      </td>
                      <td align="right" >Total</td>
                      <td  align="right" id="total_reject_qty_<? echo $j; ?>"></td>
                      <td  align="right" id="total_replace_qty_<? echo $j; ?>"></td>
                      <td align="right" id="total_qc_qty_<? echo $j; ?>"><? echo $color_qty;  ?>
                      </td>
                 </tr>
			 <?
		 $j++;	 
	 }
	           
		?>
            <tfoot>
                 <tr class="general"  height="15" >
                      <th width="480"  align="right"  colspan="7"> Grand Total</th>
                      <th width="60"  align=" right" id="grand_reject_qty"></th>
                      <th width="60"  align=" right" id="grand_replace_qty"></th>
                      <th  width="60" align="right" id="grand_qc_qty"><? echo $job_qty;  ?> </th>
                     
                 </tr>
             </tfoot>
    	</table>
    	</div>
      	<table cellpadding="0" cellspacing="0" width="650" class="rpt_table" border="1" rules="all" >
          
           </table>
             <table width="800" cellpadding="0" cellspacing="2" align="center">
               <tr>
                   <td colspan="7" align="center" class="">
                        <? 
                           echo load_submit_buttons( $permission, "fnc_cut_qc_info", 0,0,"reset_form('','','','','clear_tr()')",1);
                        ?>
                        </td>
              </tr>
            </table> 
    	<input type="hidden" id="total_order_id" name="total_order_id" value="<? echo $j-1; ?>"  />
   		<?
	}

}

if($action=="update_order_details_list")
{
	$size_arr=return_library_array( "select id, size_name from lib_size order by id desc",'id','size_name');
	// print_r($size_arr[18]);die;
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$data=explode("**",$data);

	$cutt_qc_dtls=sql_select("SELECT a.id,a.mst_id,a.order_id,a.country_id,a.color_id,a.size_id,a.color_size_id,a.bundle_no,a.number_start,a.number_end,a.bundle_qty,a.reject_qty,a.replace_qty,a.qc_pass_qty from pro_gmts_cutting_qc_dtls a,pro_gmts_cutting_qc_mst b where a.mst_id=b.id and b.cutting_qc_no='".$data[0]."' and  b.status_active=1 and b.is_deleted=0 and  a.status_active=1 and a.is_deleted=0");
	$qc_details_arr=array();
	$total_qty=array();
	$order_color_qty=array();
	foreach($cutt_qc_dtls as $inf)
	{
		$qc_details_arr[$inf[csf("order_id")]][$inf[csf("country_id")]][$inf[csf("color_id")]][$inf[csf("bundle_no")]]['qc_pass_qty']=$inf[csf("qc_pass_qty")];
		$qc_details_arr[$inf[csf("order_id")]][$inf[csf("country_id")]][$inf[csf("color_id")]][$inf[csf("bundle_no")]]['update_id']=$inf[csf("id")];
		$qc_details_arr[$inf[csf("order_id")]][$inf[csf("country_id")]][$inf[csf("color_id")]][$inf[csf("bundle_no")]]['reject_qty']=$inf[csf("reject_qty")];
		$qc_details_arr[$inf[csf("order_id")]][$inf[csf("country_id")]][$inf[csf("color_id")]][$inf[csf("bundle_no")]]['replace_qty']=$inf[csf("replace_qty")];
		$order_color_qty[$inf[csf("order_id")]][$inf[csf("country_id")]][$inf[csf("color_id")]]['qc_pass_qty']+=$inf[csf("qc_pass_qty")];
		$order_color_qty[$inf[csf("order_id")]][$inf[csf("country_id")]][$inf[csf("color_id")]]['reject_qty']+=$inf[csf("reject_qty")];
		$order_color_qty[$inf[csf("order_id")]][$inf[csf("country_id")]][$inf[csf("color_id")]]['replace_qty']+=$inf[csf("replace_qty")];
		$total_qty['qc_pass_qty']+=$inf[csf("qc_pass_qty")];
		$total_qty['reject_qty']+=$inf[csf("reject_qty")];
		$total_qty['replace_qty']+=$inf[csf("replace_qty")];
	}
	
	 $sql_bundle_reject=sql_select("SELECT a.defect_type_id,a.production_type,a.defect_point_id,a.defect_qty,a.bundle_no from  pro_gmts_prod_dft a where a.production_type=1 and defect_type_id=3 ");
	 $bundle_reject_data=array();
	 foreach($sql_bundle_reject as $inf)
	 {
		 
		 //if(in_array($inf[csf('bundle_no')],$check_arr))
		 if($check_arr[$inf[csf('bundle_no')]]!='')
		 {
		 $bundle_reject_data[$inf[csf('bundle_no')]].="**".$inf[csf('defect_point_id')]."*".$inf[csf('defect_qty')]; 
		 }
		 else
		 {
		 $bundle_reject_data[$inf[csf('bundle_no')]]=$inf[csf('defect_point_id')]."*".$inf[csf('defect_qty')];  
		 }
		$check_arr[$inf[csf('bundle_no')]]=$inf[csf('bundle_no')]; 
	 }
	
	
	 $j=1;
	$sql_dtls=sql_select("SELECT b.id,c.order_id,a.ship_date,a.color_id,a.gmt_item_id,a.plies,a.marker_qty,a.order_qty,a.total_lay_qty,a.lay_balance_qty,
	 b.job_no,b.job_year,b.company_id ,c.country_id,a.id as details_id from ppl_cut_lay_dtls a, ppl_cut_lay_mst b,ppl_cut_lay_bundle c 
	 where b.id=a.mst_id and a.id=c.dtls_id and b.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and  b.cutting_no='".$data[1]."'  
	 group by b.id,c.order_id,a.ship_date,a.color_id,a.gmt_item_id,a.plies,a.marker_qty,a.order_qty,a.total_lay_qty,a.lay_balance_qty,
	 b.job_no,b.job_year,b.company_id ,c.country_id,a.id  order by a.id");
	 ?>
	 	<table cellpadding="0" cellspacing="0" width="800" class="" border="1" rules="all" id="" align="center">
            	<tr style=" font-size:larger;color:#093">
                	<td width="150" style="font-size:larger;">Erase Bundle Qty</td>
                    <td width="120" style="font-size:larger;">From:
                    <input type="text" id="txt_erage_from" name="txt_erage_from" style="width:70px;" class="text_boxes_numeric" />
                    </td>
                    <td width="120" style="font-size:larger;">To:
                    <input type="text" id="txt_erage_to" name="txt_erage_to" class="text_boxes_numeric"  style="width:70px;" />
                    </td>
                    <td width="100"><input type="button" value="OK" style="width:80px"  class="formbutton" onClick="fnc_erage_qty()"/></td>
                </tr>
                <tr style=" font-size:larger;color:#093">
                	<td width="150" style="font-size:larger;">Replace Bundle Qty</td>
                    <td width="120" style="font-size:larger;">From:
                    <input type="text" id="txt_replace_from" name="txt_replace_from" style="width:70px;" class="text_boxes_numeric" />
                    </td>
                    <td width="120" style="font-size:larger;">To:
                    <input type="text" id="txt_replace_to" name="txt_replace_to" class="text_boxes_numeric"  style="width:70px;" />
                    </td>
                    <td width="100"><input type="button" value="OK" style="width:80px"  class="formbutton" onClick="fnc_replace_qty()"/></td>
                </tr>
            </table>
	 <?	 
	 $po_id_array = array();
	 foreach($sql_dtls as $val)
	 {
	 	$po_id_array[$val[csf('order_id')]] = $val[csf('order_id')];
	 }
	$poIds = implode(",", $po_id_array);
	$sql = sql_select( "SELECT PO_BREAK_DOWN_ID,COUNTRY_ID, COUNTRY_SHIP_DATE from wo_po_color_size_breakdown where po_break_down_id in($poIds)");
	$country_ship_date_arr = array();
	foreach ($sql as $val) 
	{
		$country_ship_date_arr[$val['PO_BREAK_DOWN_ID']][$val['COUNTRY_ID']] = $val['COUNTRY_SHIP_DATE'];
	}

	 $order_number_arr = return_library_array( "SELECT id, po_number from wo_po_break_down where id in($poIds)",'id','po_number');
	 $job_qty=0;
	 foreach($sql_dtls as $val)
	   {
?>
        <div style="width:800px; margin-top:10px" id="" align="left"> 
        <table cellpadding="0" cellspacing="0" width="800" class="" border="1" rules="all" id="order_table_<? echo $j; ?>">  
             <tr >
                    <td colspan="6">         
                         <b>  Order No:<? echo $order_number_arr[$val[csf('order_id')]]; ?> ;&nbsp; Gmt Item:<? echo $garments_item[$val[csf('gmt_item_id')]]; ?> ;&nbsp; Color:<? echo $color_arr[$val[csf('color_id')]]; ?>  ; &nbsp; </b><br/>
                         <b> 
                           &nbsp;  Country:&nbsp;<? echo $country_arr[$val[csf('country_id')]]; ?>  
                           Ship Date:<? echo change_date_format($country_ship_date_arr[$val[csf('order_id')]][$val[csf('country_id')]]); ?>;&nbsp;Order Qty:<? echo $val[csf('order_qty')]; ?></b>
    <br/>
           <p style="color:red; font-size:12px; text-align:center">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  Double Click on Reject Qty Field for Defect Record</p>
                    </td>
                    <td>
                          <? 
                     $size_total_sql=sql_select("SELECT a.country_id, b.size_id,b.marker_qty,count(a.size_id) as bdl_no,max(a.size_qty) as pcs_per_bundle from ppl_cut_lay_bundle a, ppl_cut_lay_size b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.size_id=b.size_id and a.mst_id=".$val[csf('id')]." and a.dtls_id=".$val[csf('details_id')]." and a.country_id=".$val[csf('country_id')]."  group by b.size_id,b.marker_qty,a.country_id "); 
					 $s=1;
					 foreach($size_total_sql as $sval)
					 {
						
						if($s==1)
							{
							$dtls_size_id=$sval[csf("size_id")];
							$dtls_size_qty=$sval[csf("marker_qty")];
							$bundle_no=$sval[csf("bdl_no")];
							$pcs_per_bundle=$sval[csf("pcs_per_bundle")];
							}
							else
							{
							$dtls_size_id.="*".$sval[csf("size_id")];
							$dtls_size_qty.="*".$sval[csf("marker_qty")];
							$bundle_no.="*".$sval[csf("bdl_no")];
							$pcs_per_bundle.="*".$sval[csf("pcs_per_bundle")];
							}
							$s++;
					 }
        ?>             
                 <input type="hidden" name="txt_dtls_size_id_<? echo $j; ?>"  id="txt_dtls_size_id_<? echo $j; ?>"  value="<? echo $dtls_size_id; ?>"  />
                 <input type="hidden" name="txt_dtls_size_qty_<? echo $j; ?>"  id="txt_dtls_size_qty_<? echo $j; ?>"  value="<? echo $dtls_size_qty; ?>"  />
                 <input type="hidden" name="txt_dtls_size_bdl_<? echo $j; ?>"  id="txt_dtls_size_bdl_<? echo $j; ?>"  value="<? echo $bundle_no; ?>"  />                 <input type="hidden" name="txt__pcs_per_bdl_<? echo $j; ?>"  id="txt__pcs_per_bdl_<? echo $j; ?>"  value="<? echo $pcs_per_bundle; ?>"  />
                 <input type="hidden" name="txt_oder_qty_<? echo $j; ?>"  id="txt_oder_qty_<? echo $j; ?>"  value="<? echo $val[csf('order_qty')]; ?>"  />
                 <input type="hidden" name="hidden_po_<? echo $j; ?>"  id="hidden_po_<? echo $j; ?>"  value="<? echo $val[csf('order_id')]; ?>"  /> 
                 <input type="hidden" name="hidden_gmt_<? echo $j; ?>"  id="hidden_gmt_<? echo $j; ?>"  value="<? echo $val[csf('gmt_item_id')]; ?>"  />                 <input type="hidden" name="hidden_color_<? echo $j; ?>"  id="hidden_color_<? echo $j; ?>"  value="<? echo $val[csf('color_id')]; ?>"  />

                    	</td>
            	 </tr>       
     	  </table>   
    	  <table cellpadding="0" cellspacing="0" width="800" class="rpt_table" border="1" rules="all" id="order_table_<? echo $j; ?>">
        	  <thead >
                	 <tr>
                        <th  width="40" rowspan="2" rclass="">SL</th>
                        <th width="100" rowspan="2">Country</th>
                        <th width="100" rowspan="2">Bundle No</th>
                        <th width="120" rowspan="2" >Size</th>
                        <th width="120" colspan="2">RMG No</th>
                        <th width="80" rowspan="2">Bundle Qty </th>
                        <th width="60" rowspan="2">Reject Qty</th>
                        <th width="60" rowspan="2">Replace Qty</th>
                        <th width="60" rowspan="2">QC Pass Qty</th>
                  	 </tr> 
                	  <tr>
                        <th  width="60"  >From</th>
                        <th width="60"  >To</th>
                   </tr> 
       	   </thead>
       	   <tbody id="tbl_body_<? echo $j; ?>">
<?	 
			   $bundle_data=sql_select("SELECT a.country_id, a.id,a.bundle_no,a.size_id,a.number_start,a.number_end,a.size_qty from ppl_cut_lay_bundle a, ppl_cut_lay_size b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.mst_id=".$val[csf('id')]." and a.dtls_id=".$val[csf('details_id')]." and a.country_id=".$val[csf('country_id')]."  group by a.country_id, a.id,a.bundle_no,a.size_id,a.number_start,a.number_end,a.size_qty order by a.id ASC");
			    $i=1;
				$color_qty=0;
				foreach($bundle_data as $row)
				{
					$color_qty+=$row[csf('size_qty')];
					$job_qty+=$row[csf('size_qty')];
					// echo $row[csf('size_id')];
					?>
                 	<tr id="table_tr_<? echo $j."_".$i; ?>">
                      <td id=""><? echo $i; ?>
                      <input type="hidden" id="hidden_order_<? echo $i; ?>"  name="hidden_order_<? echo $i; ?>" value="<? echo $val[csf('order_id')]; ?>"/>
                     <input type="hidden" name="size_id_<? echo $j."_".$i; ?>"  id="size_id_<? echo $j."_".$i; ?>"  value="<? echo $row[csf('size_id')]; ?>"  /> 
                     <input type="hidden" name="update_details_id_<? echo $j."_".$i; ?>"  id="update_details_id_<? echo $j."_".$i; ?>"  value="<? echo $qc_details_arr[$val[csf("order_id")]][$val[csf('country_id')]][$val[csf("color_id")]][$row[csf("bundle_no")]]['update_id'];  ?>"  />
                     <input type="hidden" name="hidden_country_<? echo $j."_".$i; ?>"  id="hidden_country_<? echo $j."_".$i; ?>"  value="<? echo $row[csf('country_id')]; ?>"  />
                     <input type="hidden" name="actual_reject_<? echo $j."_".$i; ?>"  id="actual_reject_<? echo $j."_".$i; ?>"  value="<? echo $bundle_reject_data[$row[csf('bundle_no')]]; ?>"  />
                      </td>
                      <td align="center" id="txt_country_<? echo $j."_".$i; ?>"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
                      <td align="center" id="txt_bundle_<? echo $j."_".$i; ?>"><? echo $row[csf('bundle_no')]; ?></td>
                      <td align="center" id="txt_size_<? echo $j."_".$i; ?>" ><? echo $size_arr[$row[csf('size_id')]]; ?>
                      </td>
                      <td align="right" id="txt_start_<? echo $j."_".$i; ?>"><? echo $row[csf('number_start')]; ?></td>
                      <td align="right" id="txt_end_<? echo $j."_".$i; ?>"><? echo $row[csf('number_end')]; ?></td>
                      <td align="right" id="txt_qty_<? echo $j."_".$i; ?>"><? echo $row[csf('size_qty')]; ?>
                      </td>
                      <td  align="center">
                           <input type="text" name="txt_reject_<? echo $j."_".$i; ?>"  id="txt_reject_<? echo $j."_".$i; ?>" class="text_boxes_numeric" style="width:68px"   onKeyUp="total_qc_pass(this.id,this.value,<? echo $j; ?>)"  onDblClick="pop_entry_reject(<? echo $j; ?>,<? echo $i; ?>)" value="<?  if($qc_details_arr[$val[csf("order_id")]][$val[csf('country_id')]][$val[csf("color_id")]][$row[csf("bundle_no")]]['reject_qty']!=0) echo $qc_details_arr[$val[csf("order_id")]][$val[csf('country_id')]][$val[csf("color_id")]][$row[csf("bundle_no")]]['reject_qty'];  ?>"/>                      </td>
                      <td  align="center">
                         <input type="text" name="txt_replace_<? echo $j."_".$i; ?>"  id="txt_replace_<? echo $j."_".$i; ?>" class="text_boxes_numeric" style="width:50px"   onKeyUp="total_qc_pass(this.id,this.value,<? echo $j; ?>)" value="<?  if($qc_details_arr[$val[csf("order_id")]][$val[csf('country_id')]][$val[csf("color_id")]][$row[csf("bundle_no")]]['replace_qty']!=0) echo $qc_details_arr[$val[csf("order_id")]][$val[csf('country_id')]][$val[csf("color_id")]][$row[csf("bundle_no")]]['replace_qty'];  ?>" />                      </td>
                      <td align="center"> 
                           <input type="text" name="txt_qcpass_<? echo $j."_".$i; ?>"  id="txt_qcpass_<? echo $j."_".$i; ?>" class="text_boxes_numeric"  value="<? echo $qc_details_arr[$val[csf("order_id")]][$val[csf('country_id')]][$val[csf("color_id")]][$row[csf("bundle_no")]]['qc_pass_qty'];  ?>" style="width:68px"  readonly/> 
                           <input type="hidden" name="hidden_qcpass_<? echo $j."_".$i; ?>"  id="hidden_qcpass_<? echo $j."_".$i; ?>" class="text_boxes_numeric"  value="<? echo $row[csf('size_qty')]; ?>"  />  
                       </td>
                 	</tr>
<?       
					$i++;
				}
				?>
				</tbody>
				  <tr  style=" background-color:#B0B0B0;"  b height="10">
						  <td id="" ></td>
						  <td align="center"  ><?   //echo $j; ?></td>
						  <td align="center" ><? //echo $row[csf('bundle_no')]; ?></td>
						  <td align="center" ><? //echo $row[csf('bundle_no')]; ?></td>
						  <td align="right" ><input type="hidden" id="hidden_total_qc_qty_<? echo $j; ?>" 
						   name="hidden_total_qc_qty_<? echo $j; ?>"  value="<? echo $color_qty;  ?> "/></td>
						  <td align="right" ><input type="hidden" id="hidden_reject_qty_<? echo $j; ?>" 
						  name="hidden_reject_qty_<? echo $j; ?>"  value="<? echo $order_color_qty[$val[csf("order_id")]][$val[csf('country_id')]][$val[csf("color_id")]]['replace_qty'];  ?>" />
                          <input type="hidden" id="hidden_replace_qty_<? echo $j; ?>" 
						  name="hidden_replace_qty_<? echo $j; ?>"  value="<? echo $order_color_qty[$val[csf("order_id")]][$val[csf('country_id')]][$val[csf("color_id")]]['reject_qty'];  ?>" />
                          
                          </td>
						  <td align="right" >Total</td>
						  <td  align="right" id="total_reject_qty_<? echo $j; ?>"><? echo $order_color_qty[$val[csf("order_id")]][$val[csf('country_id')]][$val[csf("color_id")]]['reject_qty'];  ?></td>
                          <td  align="right" id="total_replace_qty_<? echo $j; ?>"> <? echo $order_color_qty[$val[csf("order_id")]][$val[csf('country_id')]][$val[csf("color_id")]]['replace_qty'];  ?></td>
						  <td align="right" id="total_qc_qty_<? echo $j; ?>"><? echo $order_color_qty[$val[csf("order_id")]][$val[csf('country_id')]][$val[csf("color_id")]]['qc_pass_qty'];  ?>
						  </td>
				 </tr>
			 <?
		    $j++;	 
	   }
	?>
          <tfoot>
             <tr class="general"  height="15" >
                  <th width="680"  align="right"  colspan="7"> Grand Total</th>
                  <th width="60"  align=" right" id="grand_reject_qty"><? echo $total_qty['reject_qty'];  ?> </th>
                  <th width="60"  align=" right" id="grand_replace_qty"> <? echo $total_qty['replace_qty'];  ?> </th>
                  <th  width="60" align="right" id="grand_qc_qty"><? echo $total_qty['qc_pass_qty'];  ?> </th>
                 
             </tr>
         </tfoot>
      </table>
      </div>
         <table width="800" cellpadding="0" cellspacing="2" align="center">
               <tr>
                   <td colspan="7" align="center" class="">
                        <? 
                           echo load_submit_buttons( $permission, "fnc_cut_qc_info", 1,0,"reset_form('','','','','clear_tr()')",1);
                        ?>
                        </td>
              </tr>
            </table>  
        <input type="hidden" id="total_order_id" name="total_order_id" value="<? echo $j-1; ?>"  />
 <?
}

?>

<?
if($action=="reject_qty_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	$caption_name="";
	//print_r($sew_fin_alter_defect_type);die;
	
	?>
   <script>
   		function fnc_close()
		{
			var save_string='';	
			var total_qty=0;
			$("#tbl_list_search").find('tr').each(function()
			{
				
				var txtDefectQnty=$(this).find('input[name="txtDefectQnty[]"]').val();
				var txtDefectId=$(this).find('input[name="txtDefectId[]"]').val();
				//var txtDefectUpdateId=$(this).find('input[name="txtDefectUpdateId[]"]').val();
				
				if(txtDefectQnty*1>0)
				{
					if(save_string=="")
					{
						save_string=txtDefectId+"*"+txtDefectQnty;
						total_qty+=txtDefectQnty*1;
					}
					else
					{
						save_string+="**"+txtDefectId+"*"+txtDefectQnty;
						total_qty+=txtDefectQnty*1;
					}
				}
			});
			
			$('#actual_reject_infos').val( save_string );
			$('#actual_reject_qty').val(total_qty);
			
			parent.emailwindow.hide();
		}
   function calculate_reject()
   {
	 var reject_qty=0;
	 $("#tbl_list_search").find('tbody tr').each(function()
		{
			//alert(4);
		reject_qty+=$(this).find('input[name="txtDefectQnty[]"]').val()*1;	
		});
	   $("#reject_qty_td").text(reject_qty);
   }
   
   </script> 
    </head>
    <body>
        <div align="center" style="width:100%;" >
        <form name="defect_1"  id="defect_1" autocomplete="off">
			<? //echo load_freeze_divs ("../../",$permission,1); ?>
            <fieldset style="width:360px;">
              
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="350">  
            	<thead>
                	<tr><th colspan="3">Reject Record</th></tr>
                	<tr><th width="40">SL</th><th width="150">Reject Name</th><th>No. of Defect</th></tr>
                </thead>
            </table>
            <div style="width:350px;   id="list_container" align="left"> 
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="320" id="tbl_list_search">  
                <tbody>
                    <?
					
						$explSaveData = explode("**",$actual_infos);
						
						$defect_dataArray=array();
						foreach($explSaveData as $val)
						{
							$difectVal = explode("*",$val);
							//$defect_dataArray['up_id']=$difectVal[0];
							$defect_dataArray[$difectVal[0]]['defectid']=$difectVal[0];
							$defect_dataArray[$difectVal[0]]['defectQnty']=$difectVal[1];
						}
					
                        $i=1;
						$total_reject=0;
                        foreach($cutting_qc_reject_type as $id=>$val)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$total_reject+=$defect_dataArray[$id]['defectQnty'];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><? echo $val; ?></td>
                                <td align="center">
                                    <input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>"  onKeyUp="calculate_reject()">
                                    <input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
                                    <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
                                </td>
                            </tr>
                            <?
                            $i++;
                        }
					?>
                    </tbody>
                    <tfoot>
                        <tr class="tbl_bottom">
                            <td align="right" colspan="2">Total</td>
                            
                            <td align="right"  id="reject_qty_td" style="padding-right:20px"> <? echo $total_reject; ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
			<table width="320" id="table_id">
				 <tr>
					<td align="center" colspan="3">
						<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                        <input type="hidden" id="actual_reject_infos" />
                         <input type="hidden" id="actual_reject_qty" />
					</td>
				</tr>
			</table>
            </fieldset>
        </form>
        </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
  <?	
}


