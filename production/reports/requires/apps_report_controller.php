<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];



$company_arr=return_library_array( "select id, company_name from lib_company where status_active =1 and is_deleted=0", "id", "company_name"  );
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0", "id", "buyer_name"  );


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   	 
} 



//order wise browse------------------------------//
if($action=="order_wise_search")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
	    
	<script type="text/javascript">
		var selected_id = new Array;
		var selected_name = new Array;
	    function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
			}
		}
			
			
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style ) 
			{ 
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
			
			function js_set_value( strCon ) 
			{
				var splitSTR = strCon.split("_");
				var str = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
						
				if( jQuery.inArray( selectID, selected_id ) == -1 )
				{
				selected_id.push( selectID );
				selected_name.push( selectDESC );					
				}
				else
				{
					for( var i = 0; i < selected_id.length; i++ )
					 {
					 if( selected_id[i] == selectID ) break;
					 }
					 selected_id.splice( i, 1 );
					 selected_name.splice( i, 1 ); 
				}
				var id = ''; var name = ''; var job = '';
				for( var i = 0; i < selected_id.length; i++ )
				 {
				 id += selected_id[i] + ',';
				 name += selected_name[i] + ','; 
				 }
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 ); 
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name ); 
			}
	 
	 
	</script>
    

	<?
	extract($_REQUEST);
	//echo $job_no;die;
	if($company==0) $company_name=""; else $company_name=" and b.company_name=$company";
	if($buyer==0) $buyer_name=""; else $buyer_name="and b.buyer_name=$buyer";
	if(str_replace("'","",$job_id)!="")  $job_cond="and a.id in(".str_replace("'","",$job_id).")";
    else  if (str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and a.job_no_mst='".$job_no."'";
	
	if(str_replace("'","",$style_id)!="")  $style_cond="and b.id in(".str_replace("'","",$style_id).")";
    else  if (str_replace("'","",$style_no)=="") $style_cond=""; else $style_cond="and b.style_ref_no='".$style_no."'";
	$sql = "SELECT distinct a.id,a.po_number,b.style_ref_no,b.job_no from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $company_name $buyer_name order by job_no";
	//echo $sql;die;
	echo create_list_view("list_view", "Order Number,Job No, Style Ref","150,100,250","550","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_no,style_ref_no", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}


// //item style-------------------------------------------------------------------------
// if($action=="style_wise_search")
// {		  
// 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
// 	extract($_REQUEST);
// 	?>
// 	<script language="javascript">
// 		var selected_id = new Array;
// 		var selected_name = new Array;
//     	function check_all_data() {
// 			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
// 			tbl_row_count = tbl_row_count - 0;
// 			for( var i = 1; i <= tbl_row_count; i++ ) {
// 				var onclickString = $('#tr_' + i).attr('onclick');
// 				var paramArr = onclickString.split("'");
// 				var functionParam = paramArr[1];
// 				js_set_value( functionParam );
// 			}
// 		}
		
// 		function toggle( x, origColor ) {
// 			var newColor = 'yellow';
// 			if ( x.style ) { 
// 				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
// 			}
// 		}
		
// 		function js_set_value( strCon ) 
// 		{
// 			var splitSTR = strCon.split("_");
// 			var str = splitSTR[0];
// 			var selectID = splitSTR[1];
// 			var selectDESC = splitSTR[2];
// 			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
// 			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
// 				selected_id.push( selectID );
// 				selected_name.push( selectDESC );					
// 			}
// 			else {
// 				for( var i = 0; i < selected_id.length; i++ ) {
// 					if( selected_id[i] == selectID ) break;
// 				}
// 				selected_id.splice( i, 1 );
// 				selected_name.splice( i, 1 ); 
// 			}
// 			var id = ''; var name = ''; var job = '';
// 			for( var i = 0; i < selected_id.length; i++ ) {
// 				id += selected_id[i] + ',';
// 				name += selected_name[i] + ','; 
// 			}
// 			id 		= id.substr( 0, id.length - 1 );
// 			name 	= name.substr( 0, name.length - 1 ); 
// 			$('#txt_selected_id').val( id );
// 			$('#txt_selected').val( name ); 
// 		}
    
//     </script>
    
// 	<?php
// 	if($company==0) $company_name=""; else $company_name="and a.company_name=$company";
// 	if($buyer==0) $buyer_name=""; else $buyer_name="and a.buyer_name=$buyer";
// 	/*if(str_replace("'","",$job_id)!="")  $job_cond="and b.id in(".str_replace("'","",$job_id).")";
//     else  */if (str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and a.job_no_mst='".$job_no."'";
//     if($db_type==2) $year_cond="  extract(year from a.insert_date) as year";
// 	if($db_type==0) $year_cond="  SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
	
// 	if($db_type==0)
// 	{
// 		$year_field_con=" and SUBSTRING_INDEX(a.insert_date, '-', 1)";
// 		if($cbo_year!=0) $year_cond_id=" $year_field_con=$cbo_year"; else $year_cond_id="";	
// 	}
// 	else
// 	{
// 		$year_field_con=" and to_char(a.insert_date,'YYYY')";
// 		if($cbo_year!=0) $year_cond_id=" $year_field_con=$cbo_year"; else $year_cond_id="";	
// 	}
	
// 	$sql = "SELECT a.id, a.style_ref_no, a.job_no_prefix_num, $year_cond from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3) and  b.shiping_status!=3 $company_name $buyer_name $year_cond_id  group by a.id, a.style_ref_no, a.job_no_prefix_num, a.insert_date order by a.id DESC"; 
	
// 	echo create_list_view("list_view", "Style Refference,Job no,Year","190,100,100","440","310",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	
// 	echo "<input type='hidden' id='txt_selected_id' />";
// 	echo "<input type='hidden' id='txt_selected' />";
// 	exit();
// }

if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
		
		$cbo_company_name=str_replace("'","",$cbo_company_name);
		$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
		$txt_order_no=str_replace("'","",$txt_order_no);

		$txt_style_no=str_replace("'","",$txt_style_no);//new
		$hidden_style_id=str_replace("'","",$hidden_style_id);

		$cbo_date_type=str_replace("'","",$cbo_date_type);
		$cbo_status=str_replace("'","",$cbo_status);
		$cbo_result=str_replace("'","",$cbo_result);
		$txt_date_from=str_replace("'","",$txt_date_from);
		$txt_date_to=str_replace("'","",$txt_date_to);

		
		if($cbo_company_name==0) $company_con=""; else $company_con=" and a.company_name=$cbo_company_name";
		if($cbo_buyer_name==0) $buyer_con=""; else $buyer_con=" and a.buyer_name=$cbo_buyer_name";
		if(trim($txt_order_no)=="") $order_con=""; else $order_con="and b.po_number in('$txt_order_no')";
		if(trim($txt_style_no)=="") $style_con=""; else $style_con="and a.style_ref_no in('$txt_style_no')";//new a.style_ref_no
		
		
		if(trim($hidden_style_id)!=""){$style_con.="and a.id in('$hidden_style_id')";}
		
		
	
		if($txt_date_from!='' && $txt_date_to!='')
		{
			if($db_type==0){
				$from_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$to_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else
			{
				$from_date=change_date_format($txt_date_from,'','',-1);
				$to_date=change_date_format($txt_date_to,'','',-1);
			}
				
				if($cbo_date_type==2)
				{
					$date_con=" and b.pub_shipment_date BETWEEN '$from_date' and '$to_date'";
				}
				else if($cbo_date_type==3)
				{
					$date_con=" and d.inspection_date BETWEEN '$from_date' and '$to_date'";
				}
				else
				{
					$date_con=" and c.country_ship_date BETWEEN '$from_date' and '$to_date'";
				}
		}
		else
		{
			$date_con="";	
		}
		

	$buyer_arr=return_library_array( "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name", "id", "buyer_name" );
	
	$third_party_arr=return_library_array( "select distinct a.id, a.supplier_name from lib_supplier a,lib_supplier_party_type b  where a.id=b.supplier_id and  b.party_type=41 and a.status_active=1 and a.is_deleted=0 order by a.supplier_name", "id", "supplier_name" );
	$self_arr=$company_arr;     	 

	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where file_type=1",'master_tble_id','image_location');
	$company_arr=return_library_array( "select ID,COMPANY_NAME from LIB_COMPANY where status_active=1 and is_deleted=0",'ID','COMPANY_NAME');
	$location_arr=return_library_array( "select ID,LOCATION_NAME from LIB_LOCATION where status_active=1 and is_deleted=0",'ID','LOCATION_NAME');
	
	

 if($cbo_result)
 {
	$sql= "select b.po_break_down_id,b.inspection_qnty,b.inspection_status,b.inspection_cause from  pro_buyer_inspection b  where  b.status_active=1 and b.is_deleted=0 and b.inspection_status=$cbo_result"; 
	$ins_result=sql_select($sql);
	 foreach($ins_result as $rows){
	$po_id[]=$rows[csf('po_break_down_id')];
	 }
	 $po_id_string = implode(",",$po_id);
	 if($cbo_result){$condition_result=" and b.id in($po_id_string)";}
 }
	
	

 if($cbo_status)
 {
	$sql= "select b.po_break_down_id,b.inspection_qnty,b.inspection_status,b.inspection_cause from  pro_buyer_inspection b  where  b.status_active=1 and b.is_deleted=0"; 
	$ins_result=sql_select($sql);
	 foreach($ins_result as $rows){
	$po_id[]=$rows[csf('po_break_down_id')];
	 }
	 $po_id_string = implode(",",$po_id);
	 if($cbo_status==1){$condition_result=" and b.id not in($po_id_string)";}
 }
	

	// add style_con in both sql
	if($cbo_date_type==2)
	{
		$sql = "SELECT a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, b.pub_shipment_date, b.id, b.po_number, (b.po_quantity*a.total_set_qnty) as po_quantity, (b.unit_price/a.total_set_qnty) as rate, b.po_total_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $company_con $buyer_con $order_con $style_con $date_con $condition_result and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 order by  b.pub_shipment_date,a.job_no_prefix_num,b.id "; 
	}
	else if($cbo_date_type==3)
	{
		$sql = "SELECT d.inspection_date, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, b.pub_shipment_date, b.id, b.po_number, (b.po_quantity*a.total_set_qnty) as po_quantity, (b.unit_price/a.total_set_qnty) as rate, b.po_total_price from wo_po_details_master a, wo_po_break_down b, pro_buyer_inspection d where a.job_no=b.job_no_mst and b.id=d.po_break_down_id and a.job_no=d.job_no and d.job_no=b.job_no_mst $company_con $buyer_con $order_con $style_con $date_con $condition_result and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 order by  b.pub_shipment_date,a.job_no_prefix_num,b.id";
	}
	else
	{
		$sql = "SELECT a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, b.pub_shipment_date, b.id, b.po_number, sum(c.order_quantity) as po_quantity, c.order_rate as rate from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id $company_con $buyer_con $order_con $style_con $date_con and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and c.status_active in(1,2,3) group by a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, b.pub_shipment_date, b.id, b.po_number, c.order_rate order by b.pub_shipment_date ";
	}
	 //echo $sql; 
	$po_id=array();
	$buyer_po_qty_arr=array();	 
	$result = sql_select($sql);
	foreach($result as $rows)
	{
		$order_data_arr[$rows[csf('id')]]=array(
			'job_no'=>$rows[csf('job_no')],
			'company_name'=>$rows[csf('company_name')],
			'buyer_name'=>$rows[csf('buyer_name')],
			'style_ref_no'=>$rows[csf('style_ref_no')],
			'gmts_item_id'=>$rows[csf('gmts_item_id')],
			'po_id'=>$rows[csf('id')],
			'po_number'=>$rows[csf('po_number')],
			'pub_shipment_date'=>change_date_format($rows[csf('pub_shipment_date')]),
			'po_quantity'=>$rows[csf('po_quantity')]
		);
		
		
		$buyer_data_arr[$rows[csf('buyer_name')]]=array(
			'job_no'=>$rows[csf('job_no')],
			'company_name'=>$rows[csf('company_name')],
			'buyer_name'=>$rows[csf('buyer_name')],
			'style_ref_no'=>$rows[csf('style_ref_no')],
			'gmts_item_id'=>$rows[csf('gmts_item_id')],
			'po_id'=>$rows[csf('id')],
			'po_number'=>$rows[csf('po_number')],
			'pub_shipment_date'=>change_date_format($rows[csf('pub_shipment_date')]),
			'pub_shipment_date'=>change_date_format($rows[csf('pub_shipment_date')])
		);
		$buyer_po_qty_arr[$rows[csf('buyer_name')]]+=$rows[csf('po_quantity')];
		$buyer_po_val_arr[$rows[csf('buyer_name')]]+=($rows[csf('po_quantity')]*$rows[csf('rate')]);
		$po_rate_arr[$rows[csf('id')]]=$rows[csf('rate')];
		
		$po_id[$rows[csf('id')]]=$rows[csf('id')];
	}
	$po_id_string = implode(",",$po_id);	
	if($po_id_string == ""){$po_id_string = 0;}
		
	$sql= "SELECT a.job_no,a.buyer_name,b.po_break_down_id,b.inspected_by,b.inspection_company,b.inspected_by,b.inspection_date,b.inspection_qnty,b.inspection_status,b.inspection_cause,b.comments,b.WORKING_COMPANY,b.working_location,b.INSPECTION_STATUS,b.INS_REASON from  wo_po_details_master a,pro_buyer_inspection b  where a.job_no=b.job_no and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($po_id,0,'b.PO_BREAK_DOWN_ID')." "; 
	// echo $sql; 
	
	$ins_result=sql_select($sql);
	$ins_data_arr=array();
	foreach($ins_result as $rows)
	{
		//Summary part--------------------------------------------------------
		if($rows[csf('inspection_status')]==1){
		$ins_buyer_data_arr[$rows[csf('buyer_name')]]['pass_qty']+=$rows[csf('inspection_qnty')];
		
		$ins_buyer_data_arr[$rows[csf('buyer_name')]]['pass_val']+=($rows[csf('inspection_qnty')]*$po_rate_arr[$rows[csf('po_break_down_id')]]);
		}
		
		
		//Details part--------------------------------------------------------
		if($rows[csf('inspected_by')]==1){
			$ins_data_arr[$rows[csf('po_break_down_id')]]['ins_by']=$buyer_arr[$rows[csf('inspection_company')]];
		}
		elseif($rows[csf('inspected_by')]==2)
		{
			$ins_data_arr[$rows[csf('po_break_down_id')]]['ins_by']=$third_party_arr[$rows[csf('inspection_company')]];
		}
		else
		{
			$ins_data_arr[$rows[csf('po_break_down_id')]]['ins_by']=$self_arr[$rows[csf('inspection_company')]];
		}
		
		$ins_data_arr[$rows[csf('po_break_down_id')]]['ins_qty']+=$rows[csf('inspection_qnty')]; 
		$ins_data_arr[$rows[csf('po_break_down_id')]]['ins_date']=change_date_format($rows[csf('inspection_date')]); 
		if($rows[csf('inspection_status')]==1){
		$ins_data_arr[$rows[csf('po_break_down_id')]]['pass_qty']+=$rows[csf('inspection_qnty')];
		}
		
		if($rows[csf('inspection_status')]==2){
		$ins_data_arr[$rows[csf('po_break_down_id')]]['recheck_count']+=1;
		}
		if($rows[csf('inspection_status')]==3){
		$ins_data_arr[$rows[csf('po_break_down_id')]]['fail_count']+=1;
		}
		
		$ins_data_arr[$rows[csf('po_break_down_id')]]['COMMENTS'][$rows[COMMENTS]]=$rows[COMMENTS];
		$ins_data_arr[$rows[csf('po_break_down_id')]]['INSPECTION_STATUS'][$rows[INSPECTION_STATUS]]=$inspection_status[$rows[INSPECTION_STATUS]];
		$ins_data_arr[$rows[csf('po_break_down_id')]]['INS_REASON'][$rows[INS_REASON]]=$rows[INS_REASON];
		$ins_data_arr[$rows[csf('po_break_down_id')]]['WORKING_COMPANY']=$rows[WORKING_COMPANY];
		$ins_data_arr[$rows[csf('po_break_down_id')]]['WORKING_LOCATION']=$rows[WORKING_LOCATION];
	}
	
	
	

	$sql_result = sql_select("SELECT SEWING_PRODUCTION,PRODUCTION_ENTRY FROM VARIABLE_SETTINGS_PRODUCTION WHERE COMPANY_NAME=$cbo_company_name AND VARIABLE_LIST=1 AND STATUS_ACTIVE=1");

	$sewing_production_variable = $sql_result[0]['SEWING_PRODUCTION'];
	//================================ exfact data ===================================
	if($sewing_production_variable !=1) // gross level
	{
		$sqlEx = "SELECT A.PO_BREAK_DOWN_ID as PO_ID,MAX(A.EX_FACTORY_DATE) AS EXDATE,SUM(A.EX_FACTORY_QNTY) AS EXQNTY,C.BUYER_ID FROM PRO_EX_FACTORY_MST A,PRO_EX_FACTORY_DELIVERY_MST C WHERE C.ID=A.DELIVERY_MST_ID AND A.STATUS_ACTIVE=1 AND C.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND C.IS_DELETED=0 ".where_con_using_array($po_id,0,'A.PO_BREAK_DOWN_ID')." GROUP BY A.PO_BREAK_DOWN_ID,C.BUYER_ID ";
	}
	else // color or color size level
	{
		$sqlEx = "SELECT A.PO_BREAK_DOWN_ID as PO_ID,MAX(A.EX_FACTORY_DATE) AS EXDATE,SUM(B.PRODUCTION_QNTY) AS EXQNTY,C.BUYER_ID FROM PRO_EX_FACTORY_MST A,PRO_EX_FACTORY_DTLS B,PRO_EX_FACTORY_DELIVERY_MST C WHERE A.ID=B.MST_ID AND C.ID=A.DELIVERY_MST_ID AND A.STATUS_ACTIVE=1 AND B.STATUS_ACTIVE=1 AND C.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.IS_DELETED=0 AND C.IS_DELETED=0 ".where_con_using_array($po_id,0,'A.PO_BREAK_DOWN_ID')." GROUP BY A.PO_BREAK_DOWN_ID,C.BUYER_ID ";
	}
	// echo $sqlEx;
	$sqlExRes = sql_select($sqlEx);
	$exfactDataArr = array();
	$exfactBuyerDataArr = array();
	foreach ($sqlExRes as $val) 
	{
		$exfactBuyerDataArr[$val['BUYER_ID']] += $val['EXQNTY'];
		$exfactDataArr[$val['PO_ID']]['QTY'] += $val['EXQNTY'];
		$exfactDataArr[$val['PO_ID']]['DATE'] = $val['EXDATE'];
	}
	// print_r($exfactDataArr);
	ob_start();
	?>
        
    <!-- <table><tr><td>   
    <fieldset style="width:1220px;"> 
        <legend>Summary Report Panel</legend>  
        <div style="width:1200px;">
            <table class="rpt_table" border="1" rules="all" width="1200" align="left">
                <thead>
                    <tr>
                        <th width="40" >SL</th>
                        <th width="110">Buyer</th>
                        <th width="100">Order Qty.</th>
                        <th width="80">Pass Qty.</th>
                        <th width="80">Pass % to Ord</th>
                        <th width="100">Yet to Inspect</th>
                        <th width="80">% to Ord</th>
                        <th width="100">Order Value</th>
                        <th width="100">Pass Qty. Value</th>
                        <th width="100">% to Ord</th>
                        <th width="100">Bal. Inspect Value</th>
                        <th width="100">Exfact Qty</th>
                        <th>% to Ord</th>
                    </tr>    
                </thead>
            </table>
            <div style="width:1200px; max-height:300px; float:left; overflow-y:scroll;" id="scroll_body">
            	<table width="1180"  cellspacing="0" border="1" class="rpt_table" rules="all" align="left" id="table_body_sumarry">
                	<?
                    $i=1;
					$ins_status='';
				   foreach($buyer_data_arr as $buyer=>$row){
					   $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ; 
					
						$total_po_qty+=$buyer_po_qty_arr[$buyer];
						$total_pass_qty+=$ins_buyer_data_arr[$buyer]['pass_qty'];
						$total_po_val+=$buyer_po_val_arr[$buyer];
						$total_pass_val+=$ins_buyer_data_arr[$buyer]['pass_val'];
						$total_ex_qnty+=$exfactBuyerDataArr[$buyer];
					
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">
                    	<td width="40" align="center"><?  echo $i ; ?></td>
                        <td width="110" ><? echo $buyer_short_library[$row['buyer_name']];?></td>
                        <td width="100" align="right"><p><? echo $buyer_po_qty_arr[$buyer];?></p></td>
                        <td width="80" align="right"><p><? echo $ins_buyer_data_arr[$buyer]['pass_qty'];?></p></td>
                        <td width="80" align="right">
							<? 
								$pass_qty_persent = ($ins_buyer_data_arr[$buyer]['pass_qty']/$buyer_po_qty_arr[$buyer])*100;
								echo number_format($pass_qty_persent,2);
								
                            ?>
                        </td>
                        <td width="100" align="right">
						<? 
							$yet_to_ins=($buyer_po_qty_arr[$buyer]-$ins_buyer_data_arr[$buyer]['pass_qty']);
							echo $yet_to_ins;
							$total_yet_ins+=$yet_to_ins;
						?>
                        </td>
                        <td width="80" align="right">
							<? 
								$yet_to_persent = ($yet_to_ins/$buyer_po_qty_arr[$buyer])*100;
								echo number_format($yet_to_persent,2);
                            ?>
                        </td>
                        <td width="100" align="right"><? echo number_format($buyer_po_val_arr[$buyer],2);?></td>
                        <td align="right" width="100"><? echo number_format($ins_buyer_data_arr[$buyer]['pass_val'],2);?></td>
                        <td align="right" width="100">
							<? 
								$pass_val_persent = ($ins_buyer_data_arr[$buyer]['pass_val']/$buyer_po_val_arr[$buyer])*100;
								echo number_format($pass_val_persent,2);
                            ?>
                        </td>
                        <td align="right" width="100">
							<?
							$bal_ins_val=($buyer_po_val_arr[$buyer]-$ins_buyer_data_arr[$buyer]['pass_val']);
							echo number_format($bal_ins_val,2);
							$total_balance_ins_val+=$bal_ins_val;
							?>
                        </td>
                        <td width="100" align="right"><? echo number_format($exfactBuyerDataArr[$buyer]); ?></td>
                        <td align="right">
							<? 
								$bal_val_persent = ($bal_ins_val/$buyer_po_val_arr[$buyer])*100;
								echo number_format($bal_val_persent,2);
                            ?>
                        </td>
                        
                    </tr>
					<?
                    $i++;
                    } 
                    ?>
                </table>
            </div>
                <table width="1180" border="1" class="rpt_table" rules="all" align="left">
                    <tfoot>
                        <th colspan="2" align="right"><b>Total:</b></th>
                        <th width="100" align="right"><? echo number_format($total_po_qty,2);?></th>
                        <th width="80" align="right"><? echo number_format($total_pass_qty,2);?></th>
                        <th width="80" align="right"></th>
                        <th width="100" align="right"><? echo number_format($total_yet_ins,2);?></th>
                        <th width="80" align="right"></th>
                        <th width="100" align="right"><? echo number_format($total_po_val,2);?></th>
                        <th width="100" align="right"><? echo number_format($total_pass_val,2);?></th>
                        <th width="100"></th>
                        <th width="100"><? echo number_format($total_balance_ins_val,2);?></th>
                        <th width="100"><? echo number_format($total_ex_qnty,2);?></th>
                        <th width="76"></th>
               		</tfoot>
                </table>
            
        
        
        </div>
    </fieldset>
   </td>
   <td>
   	 <fieldset style="margin:5px;">
    <div id="container1" style="height:250px; width:300px;margin:10px;">
  
   </div>
    </fieldset>
   </td>
   
   </tr></table>    
       
  <br />      -->
 <!--Details part ..............................................................-->      
       <?php $width=2220; ?>
       
       	<fieldset style="width:<?= $width+20;?>px;"> 
       	<legend>Report Panel</legend>  
       		<div style="width:<?= $width+20;?>px;">
            	<table width="<?= $width;?>"  cellspacing="0">
                	<tr>
                        <td colspan="21" align="center" >
                            <font size="3">
                                <strong>Company Name:<?php echo $company_arr[$cbo_company_name]; ?></strong>
                            </font>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="21" align="center">
                        	<font size="3"><strong>Address</strong></font>
                        </td>
                    </tr>
                </table>
            </div>

                <table class="rpt_table" border="1" rules="all" width="<?= $width;?>" align="left">
                    <thead>
                        <tr>
                            <th width="40" >SL</th>
                            <th width="110">Bundle No</th>
                            <th width="100">Worker Name</th>
                            <th width="100">Working ID</th>
                            <th width="100">Job No</th>
                            <th width="130">Order No</th>
                            <th width="100">Style Ref</th>
                            
                            <th width="130">Garments Item</th>
                            <th width="100">Operation Name</th>
                            <th width="100">Target/Hour</th>
                            <th width="100">Start Time</th>
                            <th width="100">End Time</th>
                            <th width="100">Archive/Hour</th>
                            <th width="100">Actual Efficiency%</th>
                            
                            
                            
                        </tr>    
                    </thead>
                </table>
            <div style="width:<?= $width+20;?>px; max-height:300px; float:left; overflow-y:scroll;" id="scroll_body">
            	<table width="<?= $width;?>"  cellspacing="0" border="1" class="rpt_table" rules="all" id="table_body" align="left">
                	<?
                    $i=1;
					
				   	foreach($order_data_arr as $row)
				   	{
					   $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ; 
					
					   	//$ins_data_arr[$row['po_id']]['recheck_count']+
					   
					   	$ins_status='';
					    if($row['po_quantity']<=$ins_data_arr[$row['po_id']]['pass_qty'])
						{
							$ins_status='Complete';	
						}
						else if($ins_data_arr[$row['po_id']]['pass_qty'] > 0 && $ins_data_arr[$row['po_id']]['pass_qty']<$row['po_quantity'])
						{
							$ins_status='Partial';	
						}
						elseif($ins_data_arr[$row['po_id']]['ins_qty'] > 0)
						{
							$ins_status='Started';
						}
						$a=$ins_data_arr[$row['po_id']]['ins_qty'];
						$exQty=$exfactDataArr[$row['po_id']]['QTY'];
						$exDate=$exfactDataArr[$row['po_id']]['DATE'];

						if($a=="0"){$ins_status='Not Started';}
						
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                    	<td width="40" align="center"><?  echo $i ; ?></td>
	                        <td width="110" ><? echo $row['job_no'];?></td>
	                        <td width="100"><p><? echo $company_arr[$ins_data_arr[$row['po_id']]['WORKING_COMPANY']];?></p></td>
	                        <td width="100"><p><? echo $location_arr[$ins_data_arr[$row['po_id']]['WORKING_LOCATION']];?></p></td>
	                        <td width="100"><p><? echo $buyer_short_library[$row['buyer_name']];?></p></td>
	                        <td width="130"><p><? echo $row['style_ref_no'];?></p></td>
	                        <td width="100"><p><? echo $row['po_number'];?></p></td>
	                        
	                        <td width="130"><? echo $garments_item[$row['gmts_item_id']];?></td>
	                        <td width="100" align="center"><? echo $row['pub_shipment_date'];?></td>
	                        <td align="right" width="100"><? echo $row['po_quantity'];?></td>
	                       
	                        <td align="right" width="100"><? echo $row['po_quantity']-$ins_data_arr[$row['po_id']]['pass_qty'];?></td>
	                         <td align="right" width="100"><?= implode(',',$ins_data_arr[$row['po_id']]['INSPECTION_STATUS']);?></td>
                            <td  align="center" width="80"><? echo $ins_data_arr[$row['po_id']]['ins_date']; ?></td>
	                        
	                        
	                        
	                        <td align="right" width="60"><? echo $ins_data_arr[$row['po_id']]['recheck_count'];?></td>
	                       
                            
	                    </tr>
						<?
	                    $i++;
                    } // end  foreach($order_id_arr as $key_id=>$val)
                    ?>
                </table>
            </div>
                
                <table width="<?= $width;?>" border="1" class="rpt_table" rules="all" align="left">
                    <tfoot>
                        
                        <th width="100"align="right"><b>Total:</b></th>
                        <th width="100" align="right" id="total_po_qnty"></th>
                        <th width="100" align="right" id="total_ins_qty"></th>
                        <th width="100" align="right" id="total_pass_qnty"></th>
                        <th width="100" align="right" id="total_yet_ins_qty"></th>
                        <th width="100" align="right"></th>
                        <th width="80" align="right"></th>
                        <th width="60" align="right" id="total_re_check"></th>
                        <th width="60" align="right" id="total_fail"></th>
                        <th width="60"></th>
                        <th width="60" align="right" id="total_ex_qnty"></th>
                        <th width="60"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th></th>
               		</tfoot>
                </table>
        </fieldset>
		
        

<?
	foreach (glob("$user_id*.xls") as $filename)
	{		
		@unlink($filename);
	}
	$name=$user_id.'_'.time().".xls";
	$create_new_excel = fopen($name, 'w');	
	$report_data=ob_get_contents();
	ob_clean();
	$is_created = fwrite($create_new_excel,$report_data);
	echo $report_data."####".$name;
	
exit();
}



if($action=="show_image")
{
	echo load_html_head_contents("Set Entry","../../", 1, 1, $unicode);
    extract($_REQUEST);
	//echo "select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1";
	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
	
	?>
    <table>
    <tr>
    <?
    foreach ($data_array as $row)
	{ 
	?>
    <td><img src='../../../<? echo $row[csf('image_location')]; ?>' height='250' width='300' /></td>
    <?
	}
	?>
    </tr>
    </table>
    
    <?
	exit();
}


if($action=="order_inspection_details")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);

    extract($_REQUEST);
 	
	$buyer_arr=return_library_array( "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name", "id", "buyer_name" );
	
	$third_party_arr=return_library_array( "select distinct a.id, a.supplier_name from lib_supplier a,lib_supplier_party_type b  where a.id=b.supplier_id and  b.party_type=41 and a.status_active=1 and a.is_deleted=0 order by a.supplier_name", "id", "supplier_name" );
	$self_arr=$company_arr;     	 
	$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
	
	
	$sql= "select a.week_id,a.po_break_down_id,a.inspected_by,a.inspection_company,a.inspection_date,a.inspection_qnty,a.inspection_status,a.inspection_cause,a.week_id,a.country_id,a.comments,b.pub_shipment_date from  pro_buyer_inspection a,wo_po_break_down b  where a.status_active=1 and b.status_active in(1,2,3) and a.is_deleted=0 and a.po_break_down_id=$order_id and a.po_break_down_id=b.id"; 
//echo $sql;
$data_array=sql_select($sql);
?>
<table width="1000" class="rpt_table" border="1" rules="all">
    <thead>
        <th width="35">Inspec. No</th>
        <th width="100">Inspection By</th>
        <th width="60">Week No</th>
        <th width="100">Country</th>
        <th width="100">Ship Date</th>
        <th width="80">Inspec. Date</th>
        <th width="80">Inspec. Qty</th>
        <th width="80">Result</th>
        <th>Comments</th>
    </thead>
<?
$i=1;
foreach($data_array as $row){
		$inspection_company='';
		if(str_replace("'","",$row[csf('inspected_by')])==1){
			$inspection_company=$buyer_arr[$row[csf('inspection_company')]];
		}
		elseif(str_replace("'","",$row[csf('inspected_by')])==2)
		{
			$inspection_company=$third_party_arr[$row[csf('inspection_company')]];
		}
		else
		{
			$inspection_company=$self_arr[$row[csf('inspection_company')]];
		}
	
//var_dump($row[csf('inspected_by')]);	
	
	$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ; 

?>
    <tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" style="cursor:pointer;">
        <td align="center"><? echo $i;?></td>
        <td><? echo $inspection_company;?></td>
        <td align="center"><? echo $row[csf('week_id')];?></td>
        <td><? echo $country_arr[$row[csf('country_id')]];?></td>
        <td align="center"><? echo change_date_format($row[csf('pub_shipment_date')]);?></td>
        <td align="center"><? echo change_date_format($row[csf('inspection_date')]);?></td>
        <td align="right"><? echo $ins_qty=$row[csf('inspection_qnty')];$tot_qty+=$ins_qty;?></td>
        <td align="center"><?  echo $inspection_status[$row[csf('inspection_status')]];?></td>
        <td><? echo $row[csf('comments')];?></td>
    </tr>
<?
$i++;
}

?>
    <tfoot>
    	<th>&nbsp;</th>
    	<th>&nbsp;</th>
    	<th>&nbsp;</th>
    	<th>&nbsp;</th>
    	<th>&nbsp;</th>
    	<th>&nbsp;</th>
    	<th align="right"><? echo $tot_qty;?></th>
    	<th>&nbsp;</th>
    	<th>&nbsp;</th>
    </tfoot>
</table>

<?

}