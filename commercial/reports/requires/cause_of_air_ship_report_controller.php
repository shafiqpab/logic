<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];



/*

//--------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------

$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$buyer_name_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
$lc_arr=return_library_array( "select id, export_lc_no from com_export_lc",'id','export_lc_no');
$sc_arr=return_library_array( "select id, contract_no from com_sales_contract",'id','contract_no');
$deling_marchent_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
if($db_type==2 || $db_type==1 )
{
$article_arr=return_library_array("select LISTAGG(cast(article_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY article_number) as article_number, po_break_down_id from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and article_number is not null group by po_break_down_id","po_break_down_id","article_number");
}
else if ($db_type==0)
{
$article_arr=return_library_array("select group_concat(distinct(article_number)) as article_number, po_break_down_id from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and article_number!='' group by po_break_down_id","po_break_down_id","article_number");
}

$submission_status_id_arr=return_library_array( "select invoice_id, doc_submission_mst_id from com_export_doc_submission_invo",'invoice_id','doc_submission_mst_id');
$realize_status_id_arr=return_library_array( "select invoice_bill_id, id from com_export_proceed_realization",'invoice_bill_id','id');*/
//load drop down Buyer
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0); 
	exit();
}



if($action=="style_refarence_surch")
{
	echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
			//alert(strCon);
				var splitSTR = strCon.split("_");
				var str = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
				
				toggle( document.getElementById( 'tr_' + str), '#FFFFCC' );
				
				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );
					selected_no.push( str );				
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
					selected_no.splice( i, 1 ); 
				}
				var id = ''; var name = ''; var job = ''; var num='';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
					num += selected_no[i] + ','; 
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 ); 
				num 	= num.substr( 0, num.length - 1 );
				//alert(num);
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name ); 
				$('#txt_selected_no').val( num );
		}
		
		function fn_selected()
		{
			var style_no='<? echo $txt_style_ref_no;?>';
			var style_id='<? echo $txt_style_ref_id;?>';
			var style_des='<? echo $txt_style_ref;?>';
			if(style_no!="")
			{
				style_no_arr=style_no.split(",");
				style_id_arr=style_id.split(",");
				style_des_arr=style_des.split(",");
				var str_ref="";
				for(var k=0;k<style_no_arr.length; k++)
				{
					str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k];
					js_set_value(str_ref);
				}
			}
		}
		
		
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Style Ref No</th>
                    <th>Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                </thead>
                <tbody>
                	<tr>
                        <td align="center">	
                    		<input type="text" style="width:130px" class="text_boxes" name="txt_style_ref_no" id="txt_style_ref_no" />
                        </td>
                        <td align="center">
                        	 <input type="text" style="width:130px" class="text_boxes" name="txt_job_no" id="txt_job_no" value="" />
                        </td>                 
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+'<? echo $buyer; ?>'+'**'+document.getElementById('txt_style_ref_no').value+'**'+document.getElementById('txt_job_no').value, 'style_refarence_surch_list_view', 'search_div', 'cause_of_air_ship_report_controller', 'setFilterGrid(\'list_view\',-1)');fn_selected();" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
		</fieldset>
            <div style="margin-top:15px" id="search_div"></div>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}
//style search------------------------------//
if($action=="style_refarence_surch_list_view")
{
	extract($_REQUEST);
	//echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	list($company,$buyer,$txt_style_ref_no,$txt_job_no)=explode('**',$data);
	
	if($txt_style_ref_no!=""){$search_con=" and a.style_ref_no like('%$txt_style_ref_no%')";}
	if($txt_job_no!=""){$search_con .= " and a.job_no like('%$txt_job_no')";}
	if($db_type==2 || $db_type==1 )
	{
		$select_date=" to_char(a.insert_date,'YYYY')";
	}
	else if ($db_type==0)
	{
		$select_date=" year(a.insert_date)";
	}
	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	if($buyer!=0) $buyer_cond="and a.buyer_name=$buyer"; else $buyer_cond="";
	$sql = "select a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$select_date as year from wo_po_details_master a where a.company_name=$company $buyer_cond  and is_deleted=0 $search_con order by job_no_prefix_num"; 
	//echo $sql; die;
	echo create_list_view("list_view", "Style Ref No,Job No,Year","160,90,100","400","230",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	exit();
}

if($action=="order_surch")
{
	echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
			//alert(strCon);
				var splitSTR = strCon.split("_");
				var str_or = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
				
				toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );
				
				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );
					selected_no.push( str_or );				
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
					selected_no.splice( i, 1 ); 
				}
				var id = ''; var name = ''; var job = ''; var num='';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
					num += selected_no[i] + ','; 
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 ); 
				num 	= num.substr( 0, num.length - 1 );
				//alert(num);
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name ); 
				$('#txt_selected_no').val( num );
		}
		
		function fn_selected()
		{
			var style_no='<? echo $txt_order_id_no;?>';
			var style_id='<? echo $txt_order_id;?>';
			var style_des='<? echo $txt_order;?>';
			//alert(style_id);
			if(style_no!="")
			{
				style_no_arr=style_no.split(",");
				style_id_arr=style_id.split(",");
				style_des_arr=style_des.split(",");
				var str_ref="";
				for(var k=0;k<style_no_arr.length; k++)
				{
					str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k];
					js_set_value(str_ref);
				}
			}
		}
		
		
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Order No</th>
                    <th>Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                </thead>
                <tbody>
                	<tr>
                        <td align="center">	
                    		<input type="text" style="width:130px" class="text_boxes" name="txt_order_no" id="txt_order_no" />
                        </td>
                        <td align="center">
                        	 <input type="text" style="width:130px" class="text_boxes" name="txt_job_no" id="txt_job_no" value="" />
                        </td>                 
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+'<? echo $buyer; ?>'+'**'+document.getElementById('txt_order_no').value+'**'+document.getElementById('txt_job_no').value, 'order_surch_list_view', 'search_div', 'cause_of_air_ship_report_controller', 'setFilterGrid(\'list_view\',-1)');fn_selected();" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
		</fieldset>
            <div style="margin-top:15px" id="search_div"></div>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}
//order search------------------------------//
if($action=="order_surch_list_view")
{
	extract($_REQUEST);
	//echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	list($company,$buyer,$txt_order_no,$txt_job_no)=explode('**',$data);
	
	if($txt_order_no!=""){$search_con=" and a.po_number like('%$txt_order_no%')";}
	if($txt_job_no!=""){$search_con .= " and a.job_no_mst like('%$txt_job_no')";}
	if($db_type==2 || $db_type==1 )
	{
		$select_date=" to_char(a.insert_date,'YYYY')";
	}
	else if ($db_type==0)
	{
		$select_date=" year(a.insert_date)";
	}
	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$style_all=str_replace("'","",$style_all);
	
	if($buyer!=0) $buyer_cond="and b.buyer_name=$buyer"; else $buyer_cond="";
	if($style_all!="") $style_cond="and b.id in($style_all)"; else $style_cond="";
	$sql = "select a.id,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$select_date as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and b.company_name=$company $search_con $buyer_cond  $style_cond and a.status_active=1"; 
	//echo $sql; die;
	echo create_list_view("list_view", "Order NO,Job No,Year,Style Ref No","150,80,70,150","500","230",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_no_prefix_num,year,style_ref_no", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	
	
	exit();
}



//report generated here--------------------//
if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//echo "test";die;
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_style_ref_id=str_replace("'","",$txt_style_ref_id);
	$txt_order=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	
	$txt_date_from_ship=str_replace("'","",$txt_date_from_ship);
	$txt_date_to_ship=str_replace("'","",$txt_date_to_ship);
	$txt_invoice_no=str_replace("'","",$txt_invoice_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_date_type=str_replace("'","",$cbo_date_type);
	
	
	$sql_cond="";
	if($cbo_buyer_name>0) $sql_cond.=" and d.BUYER_NAME=$cbo_buyer_name";
	if($txt_style_ref_id!="") $sql_cond.=" and d.id in($txt_style_ref_id)";
	if($txt_order_id!="") $sql_cond.=" and c.id in($txt_order_id)";
	if($txt_date_from_ship!="" && $txt_date_to_ship!="") $sql_cond.=" and c.PUB_SHIPMENT_DATE between '$txt_date_from_ship' and '$txt_date_to_ship'";
	if($txt_invoice_no!="") $sql_cond.=" and a.INVOICE_NO='$txt_invoice_no'";
	if($txt_date_from!="" && $txt_date_to!="")
	{
		if($cbo_date_type==1) $sql_cond.=" and a.INVOICE_DATE between '$txt_date_from' and '$txt_date_to'"; 
		else $sql_cond.=" and a.EX_FACTORY_DATE between '$txt_date_from' and '$txt_date_to'"; 
	}
	
	$sql="select d.ID AS JOB_ID, d.JOB_NO, d.BUYER_NAME, d.STYLE_REF_NO, d.TOTAL_SET_QNTY, c.PO_NUMBER, c.PO_QUANTITY, b.PO_BREAKDOWN_ID, b.AMOUNT, b.QNTY_DTLS, a.INVOICE_NO, a.INVOICE_QUANTITY, a.INVOICE_VALUE, a.EX_FACTORY_DATE, a.ID AS INVOICE_ID
	from COM_EXPORT_INVOICE_SHIP_MST a, EXPORT_INVOICE_FREIGHT_DTLS b, WO_PO_BREAK_DOWN c, WO_PO_DETAILS_MASTER d
	where a.ID=b.INVOICE_ID and b.PO_BREAKDOWN_ID=c.ID and c.JOB_NO_MST=d.JOB_NO and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.BENIFICIARY_ID=$cbo_company_name $sql_cond";
	//echo $sql;die;
	$sql_result=sql_select($sql);
	$summery_data=array();$dtls_data=array();
	foreach($sql_result as $row)
	{
		$qnty_dtls_arr=explode("**",$row["QNTY_DTLS"]);
		foreach($qnty_dtls_arr as $val)
		{
			$cause_dtls_arr=explode("__",$val);
			$com_location=explode("*",$cause_dtls_arr[0]);			
			$com_id=$com_location[1];
			$loc_id=$com_location[0];
			$department_id=$cause_dtls_arr[1];
			$cause_id=$cause_dtls_arr[2];
			$cause_wise_amt=$cause_dtls_arr[3];
			$cause_remarks=$cause_dtls_arr[5];
			$all_buyer_arr[$row["BUYER_NAME"]][$cause_dtls_arr[0]]=$row["BUYER_NAME"];
			$all_com_arr[$com_id]=$com_id;
			$all_loc_arr[$loc_id]=$loc_id;
			if($cause_wise_amt>0)
			{
				$all_department_arr[$department_id]=$department_id;
			}
			$summery_data[$row["BUYER_NAME"]][$com_id][$loc_id][$department_id]+=$cause_wise_amt;
			
			$dtls_data[$row["BUYER_NAME"]][$row["PO_BREAKDOWN_ID"]][$row["INVOICE_ID"]][$com_id][$loc_id][$department_id][$cause_id]["INVOICE_NO"]=$row["INVOICE_NO"];
			$dtls_data[$row["BUYER_NAME"]][$row["PO_BREAKDOWN_ID"]][$row["INVOICE_ID"]][$com_id][$loc_id][$department_id][$cause_id]["STYLE_REF_NO"]=$row["STYLE_REF_NO"];
			$dtls_data[$row["BUYER_NAME"]][$row["PO_BREAKDOWN_ID"]][$row["INVOICE_ID"]][$com_id][$loc_id][$department_id][$cause_id]["JOB_NO"]=$row["JOB_NO"];
			$dtls_data[$row["BUYER_NAME"]][$row["PO_BREAKDOWN_ID"]][$row["INVOICE_ID"]][$com_id][$loc_id][$department_id][$cause_id]["PO_NUMBER"]=$row["PO_NUMBER"];
			$dtls_data[$row["BUYER_NAME"]][$row["PO_BREAKDOWN_ID"]][$row["INVOICE_ID"]][$com_id][$loc_id][$department_id][$cause_id]["TOTAL_SET_QNTY"]=$row["TOTAL_SET_QNTY"];
			$dtls_data[$row["BUYER_NAME"]][$row["PO_BREAKDOWN_ID"]][$row["INVOICE_ID"]][$com_id][$loc_id][$department_id][$cause_id]["PO_QUANTITY"]=$row["PO_QUANTITY"];
			$dtls_data[$row["BUYER_NAME"]][$row["PO_BREAKDOWN_ID"]][$row["INVOICE_ID"]][$com_id][$loc_id][$department_id][$cause_id]["INVOICE_QUANTITY"]=$row["INVOICE_QUANTITY"];
			$dtls_data[$row["BUYER_NAME"]][$row["PO_BREAKDOWN_ID"]][$row["INVOICE_ID"]][$com_id][$loc_id][$department_id][$cause_id]["INVOICE_VALUE"]=$row["INVOICE_VALUE"];
			$dtls_data[$row["BUYER_NAME"]][$row["PO_BREAKDOWN_ID"]][$row["INVOICE_ID"]][$com_id][$loc_id][$department_id][$cause_id]["EX_FACTORY_DATE"]=$row["EX_FACTORY_DATE"];
			$dtls_data[$row["BUYER_NAME"]][$row["PO_BREAKDOWN_ID"]][$row["INVOICE_ID"]][$com_id][$loc_id][$department_id][$cause_id]["ORDER_CAUSE_AMOUNT"]=$row["AMOUNT"];
			$dtls_data[$row["BUYER_NAME"]][$row["PO_BREAKDOWN_ID"]][$row["INVOICE_ID"]][$com_id][$loc_id][$department_id][$cause_id]["CAUSE_AMOUNT"]+=$cause_wise_amt;
			if($dtls_data_check[$row["BUYER_NAME"]][$row["PO_BREAKDOWN_ID"]][$row["INVOICE_ID"]][$com_id][$loc_id][$department_id][$cause_id]=="")
			{
				$dtls_data_check[$row["BUYER_NAME"]][$row["PO_BREAKDOWN_ID"]][$row["INVOICE_ID"]][$com_id][$loc_id][$department_id][$cause_id]=$cause_id;
				$dtls_data[$row["BUYER_NAME"]][$row["PO_BREAKDOWN_ID"]][$row["INVOICE_ID"]][$com_id][$loc_id][$department_id][$cause_id]["CAUSE_REMARKS"].=$cause_remarks.",";
			}
		}
		//$summery_data[$row["BUYER_NAME"]][
	}
	//echo "<pre>";print_r($all_buyer_arr);die;
	foreach($dtls_data as $b_id => $b_data)
	{
		foreach($b_data as $ord_id => $ord_data)
		{
			foreach($ord_data as $inv_id => $inv_data)
			{
				foreach($inv_data as $com_id => $com_data)
				{
					foreach($com_data as $loc_id => $loc_data)
					{
						foreach($loc_data as $dept_id => $dept_data)
						{
							foreach($dept_data as $cause_id => $cu_data)
							{
								$bu_ord_inv[$b_id][$ord_id][$inv_id]++;
								$ord_tot_amt_arr[$ord_id]+=$cu_data["CAUSE_AMOUNT"];
							}
						}
					}
				}
			}
		}
	}
	
	//echo "<pre>";print_r($all_com_arr);
	//echo "<pre>";print_r($summery_data);
	//echo "<pre>";print_r($dtls_data);die;
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	
	ob_start();
	$summery_tbl_width=480+(90*count($all_department_arr));
	?>
	<div style="width:1400px" align="left"> 
		<table width="1400" border="0" cellpadding="2" cellspacing="0" align="left"> 
            <tr class="form_caption" style="border:none;">
                <td colspan="8" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td> 
            </tr>
            <tr style="border:none;">
                    <td colspan="8" align="center" style="border:none; font-size:14px;">
                        Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>                                
                    </td>
            </tr>
		</table>
        <br />
        <table width="1400" border="0" cellpadding="2" cellspacing="0" align="left">
        	<tr><td style="font-size:16px; font-weight:bold">Summery Data</td></tr>
        </table>
        <br/>
        <table width="<?= $summery_tbl_width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_summery" align="left">
        	<thead>
            	<tr>
                	<th width="100">Buyer</th>
                    <th width="130">Resposible Company</th>
                    <th width="130">Resposible Location</th>
                    <?
					foreach($all_department_arr as $dept_id)
					{
						?>
                        <th width="90"><? echo $short_booking_cause_arr[$dept_id];?></th>
                        <?
					}
					?>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
            	<?
				$i=1;
				foreach($all_buyer_arr as $buy_id=>$buy_data)
				{
					foreach($buy_data as $cause_id=>$b_data)
					{
						$com_loc_ref=explode("*",$cause_id);
						$com_id=$com_loc_ref[1];
						$loc_id=$com_loc_ref[0];
						?>
                        <tr>
                        	<td><p><? echo $buyer_arr[$buy_id];?>&nbsp;</p></td>
                            <td title="<? echo $cause_id; ?>"><p><? echo $company_arr[$com_id];?>&nbsp;</p></td>
                            <td><p><? echo $location_arr[$loc_id];?>&nbsp;</p></td>
                            <?
							$row_tot=0;
							foreach($all_department_arr as $dept_id)
							{
								?>
								<td align="right"><? echo number_format($summery_data[$buy_id][$com_id][$loc_id][$dept_id],2);?></td>
								<?
								$row_tot+=$summery_data[$buy_id][$com_id][$loc_id][$dept_id];
								$dept_wise[$dept_id]+=$summery_data[$buy_id][$com_id][$loc_id][$dept_id];
							}
							?>
							<td align="right"><? echo number_format($row_tot,2);?></td>
                        </tr>
                        <?
					}
				}
				?>
            </tbody>
            <tfoot>
            	<tr>
                	<th colspan="3" align="right">Total:</th>
                    <?
					$g_tot=0;
					foreach($all_department_arr as $dept_id)
					{
						?>
						<th align="right"><? echo number_format($dept_wise[$dept_id],2);?></th>
						<?
						$g_tot+=$dept_wise[$dept_id];
					}
					?>
					<th align="right"><? echo number_format($g_tot,2);?></th>
                </tr>
            </tfoot>
        </table>
       	<table width="1400" border="0" cellpadding="2" cellspacing="0" align="left">
        	<tr><td>&nbsp;</td></tr>
        </table>
        <table width="1400" border="0" cellpadding="2" cellspacing="0" align="left">
        	<tr><td style="font-size:16px; font-weight:bold">Details Data</td></tr>
        </table>
        <br/>
		<table width="1520" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left"> 
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="110">Invoice No</th>
					<th width="110">Style No</th>
					<th width="110">Order NO</th>
					<th width="80">Order Qnty(pcs) </th>
					<th width="80">Invoice Qnty(pcs)</th>
					<th width="75">TOD</th>
                    <th width="75">Ex-Factory Date</th>
                    <th width="75">Invoice Price($)</th>
                    <th width="130">Resposible Company</th>
                    <th width="130">Responsible Location</th>
					<th width="100">Responsible Dept</th>
                    <th width="120">Cause OF Air</th>
                    <th width="120">Remarks</th>
                    <th width="80">Air Fright($)</th>
					<th>Air Fright %</th>
				</tr>
			</thead>
	   </table> 
	  <div style="width:1540px; overflow-y: scroll; max-height:250px;" id="scroll_body">
		<table width="1520" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
			<?
			$i=1;
			//$cause_arr=array(1=>"couse 1",2=>"cause 2", 3=>"caouse 3");
			$cause_arr=return_library_array( "select id, cause from booking_cause where entry_form=270 and status_active=1 and is_deleted=0",'id','cause');
			foreach($dtls_data as $b_id => $b_data)
			{
				$buy_cause_amt=$buy_cause_per=0;
				?>
                <tr bgcolor="#FFFF99"><td colspan="16" style="font-size:14px; font-weight:bold"><? echo $buyer_arr[$b_id];?></td></tr>
                <?
				foreach($b_data as $ord_id => $ord_data)
				{
					$ord_cause_amt=$ord_cause_per=0;
					foreach($ord_data as $inv_id => $inv_data)
					{
						foreach($inv_data as $com_id => $com_data)
						{
							foreach($com_data as $loc_id => $loc_data)
							{
								foreach($loc_data as $dept_id => $dept_data)
								{
									foreach($dept_data as $cause_id => $cu_data)
									{
										if ($i%2==0) $bgcolor='#E9F3FF'; else $bgcolor='#FFFFFF';
										?>
                                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                            <td width="30"><? echo $i; ?></td>
                                            <td width="110"><p><? echo $cu_data["INVOICE_NO"]; ?>&nbsp;</p></td>
                                            <td width="110"><p><? echo $cu_data["STYLE_REF_NO"]; ?>&nbsp;</p></td>
                                            <td width="110"><p><? echo $cu_data["PO_NUMBER"]; ?>&nbsp;</p></td>
                                            <td width="80" align="right"><? echo number_format(($cu_data["PO_QUANTITY"]*$cu_data["TOTAL_SET_QNTY"]),2); ?></td>
                                            <td width="80" align="right"><? echo number_format(($cu_data["INVOICE_QUANTITY"]*$cu_data["TOTAL_SET_QNTY"]),2); ?></td>
                                            <td width="75">&nbsp;</td>
                                            <td width="75" align="center"><p><? if($cu_data["EX_FACTORY_DATE"]!="" && $cu_data["EX_FACTORY_DATE"]!="0000-00-00") echo change_date_format($cu_data["EX_FACTORY_DATE"]); ?>&nbsp;</p></td>
                                            <td width="75" align="right"><? echo number_format(($cu_data["INVOICE_VALUE"]/$cu_data["INVOICE_QUANTITY"]),2); ?></td>
                                            <td width="130"><p><? echo $company_arr[$com_id];?>&nbsp;</p></td>
                                            <td width="130"><p><? echo $location_arr[$loc_id];?>&nbsp;</p></td>
                                            <td width="100"><p><? echo $short_booking_cause_arr[$dept_id];?>&nbsp;</p></td>
                                            <td width="120" title="<?= $cause_id;?>"><p><? echo $cause_arr[$cause_id];?>&nbsp;</p></td>
                                            <td width="120"><p><? echo chop($cu_data["CAUSE_REMARKS"],",");?>&nbsp;</p></td>
                                            <td width="80" align="right"><? echo number_format($cu_data["CAUSE_AMOUNT"],2); ?></td>
                                            <td align="right"><? $cause_persent=(($cu_data["CAUSE_AMOUNT"]/$ord_tot_amt_arr[$ord_id])*100); echo number_format($cause_persent,2); ?></th>
                                        </tr>
                                        <?
										$i++;
										$ord_cause_amt+=$cu_data["CAUSE_AMOUNT"];
										$buy_cause_amt+=$cu_data["CAUSE_AMOUNT"];
										$gt_cause_amt+=$cu_data["CAUSE_AMOUNT"];
									}
								}
							}
						}
					}
					?>
                    <tr bgcolor="#CCCCCC">
                    	<td colspan="14" align="right">Order wise Total :</td>
                        <td align="right"><? echo number_format($ord_cause_amt,2);?></td>
                        <td></td>
                    </tr>
                    <?

				}
				?>
                <tr bgcolor="#CCCCCC">
                    <td colspan="14" align="right">Buyer  Total :</td>
                    <td align="right"><? echo number_format($buy_cause_amt,2);?></td>
                    <td></td>
                </tr>
                <?
			}
			?>    
			<tr bgcolor="#999999">
				<td  colspan="14" align="right" ><b>Grand Total</b></td>
				<td  align="right"><b><? echo number_format($gt_cause_amt,2); ?></b> </td>
				<td >&nbsp;</td>
			</tr>
		</table>
	    </div>
    </div>
	<?	 
	$html = ob_get_contents();
	ob_clean();
	$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("$user_id*.xls") as $filename) {
	if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename"; 
	exit();
}
?>

