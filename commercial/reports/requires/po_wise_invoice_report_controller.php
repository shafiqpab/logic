<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.fabrics.php');
include('../../../includes/class4/class.yarns.php');
include('../../../includes/class4/class.conversions.php');
include('../../../includes/class4/class.trims.php');
include('../../../includes/class4/class.emblishments.php');
include('../../../includes/class4/class.washes.php');
include('../../../includes/class4/class.commercials.php');
include('../../../includes/class4/class.commisions.php');
include('../../../includes/class4/class.others.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if($db_type==2 || $db_type==1 )
{
	$select_date=" to_char(a.insert_date,'YYYY')";
	$group_concat="wm_concat";
}
else if ($db_type==0)
{
	$select_date=" year(a.insert_date)";
	$group_concat="group_concat";
}



//load drop down Buyer
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0); 
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
    	function check_all_data() 
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			//alert(1);
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				if($('#tr_' + i).is(':visible'))
				{
					var onclickString = $('#tr_' + i).attr('onclick');
					var paramArr = onclickString.split("'");
					var functionParam = paramArr[1];
					js_set_value( functionParam );
				}
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+'<? echo $buyer; ?>'+'**'+document.getElementById('txt_style_ref_no').value+'**'+document.getElementById('txt_job_no').value, 'style_refarence_surch_list_view', 'search_div', 'po_wise_invoice_report_controller', 'setFilterGrid(\'list_view\',-1)');fn_selected();" style="width:100px;" />
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

//style search------------//
if($action=="style_refarence_surch_list_view")
{
	extract($_REQUEST);
	list($company,$buyer,$txt_style_ref_no,$txt_job_no)=explode('**',$data);
	
	if($txt_style_ref_no!=""){$search_con=" and a.style_ref_no like('%$txt_style_ref_no%')";}
	if($txt_job_no!=""){$search_con .= " and a.job_no like('%$txt_job_no')";}
	
	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$buyer_cond = '';
	if($buyer != 0) $buyer_cond="and a.buyer_name=$buyer";

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
    	function check_all_data() 
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				if($('#tr_' + i).is(':visible'))
				{
					var onclickString = $('#tr_' + i).attr('onclick');
					var paramArr = onclickString.split("'");
					var functionParam = paramArr[1];
					js_set_value( functionParam );
				}
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
                    <th>Actual PO</th>
                    <th>Order No</th>
                    <th>Job No</th>
                    <th>Internal Ref No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                </thead>
                <tbody>
                	<tr>
                        <td align="center">	
                    		<input type="text" style="width:130px" class="text_boxes" name="txt_actural_no" id="txt_actural_no" />
                        </td>
                        <td align="center">	
                    		<input type="text" style="width:130px" class="text_boxes" name="txt_order_no" id="txt_order_no" />
                        </td>
                        <td align="center">
                        	<input type="text" style="width:130px" class="text_boxes" name="txt_job_no" id="txt_job_no" value="" />
                        </td>
                        <td align="center">
                        	<input type="text" style="width:130px" class="text_boxes" name="txt_internal_ref" id="txt_internal_ref" value="" />
                        </td>                 
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+'<? echo $buyer; ?>'+'**'+document.getElementById('txt_order_no').value+'**'+document.getElementById('txt_job_no').value+'**'+document.getElementById('txt_internal_ref').value+'**'+document.getElementById('txt_actural_no').value, 'order_surch_list_view', 'search_div', 'po_wise_invoice_report_controller', 'setFilterGrid(\'list_view\',-1)');fn_selected();" style="width:100px;" />
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
	list($company,$buyer,$txt_order_no,$txt_job_no,$txt_internal_ref,$txt_actural_no)=explode('**',$data);
	
	if($txt_order_no != '') $search_con=" and a.po_number like('%$txt_order_no%')";
	if($txt_actural_no != '') $search_con.=" and c.acc_po_no like('%$txt_actural_no%')";
	if($txt_job_no != '') $search_con .= " and a.job_no_mst like('%$txt_job_no')";
	if($txt_internal_ref != '') $search_con .= " and a.grouping like('%$txt_internal_ref%')";

	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$style_all=str_replace("'","",$style_all);
	
	$buyer_cond=$style_cond='';
	if($buyer != 0) $buyer_cond="and b.buyer_name=$buyer";
	if($style_all != '') $style_cond="and b.id in($style_all)";

	if($db_type==0) $group_con=" group_concat(c.acc_po_no)  as acc_po_no";
	else $group_con=" listagg(c.acc_po_no,',') within group (order by c.acc_po_no) as acc_po_no";
	$sql = "SELECT a.id, a.po_number, a.job_no_mst, b.style_ref_no, b.job_no_prefix_num, $select_date as year, a.grouping, $group_con  from wo_po_break_down a, wo_po_details_master b, wo_po_acc_po_info c where a.job_id=b.id and a.id=c.po_break_down_id and b.job_no=c.job_no and b.company_name=$company $search_con $buyer_cond  $style_cond and a.status_active=1 and b.is_deleted=0 and c.is_deleted=0 group by a.id, a.po_number, a.job_no_mst, b.style_ref_no, b.job_no_prefix_num, a.grouping, $select_date"; 
	// echo $sql; die;
	echo create_list_view("list_view", "Order NO,Job No,Year,Style Ref No,Internal Ref No,Actual PO","150,80,70,150,150,150","780","230",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_no_prefix_num,year,style_ref_no,grouping,acc_po_no", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";	
	exit();
}



//report generated here----------//
if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_style_ref_id=str_replace("'","",$txt_style_ref_id);
	$txt_order=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$sub_sql="select a.INVOICE_ID, a.DOC_SUBMISSION_MST_ID, b.ENTRY_FORM from com_export_doc_submission_invo a, com_export_doc_submission_mst b where a.doc_submission_mst_id=b.id and a.status_active=1 and b.status_active=1 and b.company_id=$cbo_company_name";
	$submission_status_id_arr=$submission_bank_arr=array();
	$sub_sql_result=sql_select($sub_sql);
	foreach($sub_sql_result as $row)
	{
		$submission_status_id_arr[$row["INVOICE_ID"]]=$row["DOC_SUBMISSION_MST_ID"];
		if($row["ENTRY_FORM"]==40)
		{
			$submission_bank_arr[$row["INVOICE_ID"]]=$row["DOC_SUBMISSION_MST_ID"];
		}
		
	}
	//$submission_status_id_arr=return_library_array( "select invoice_id, doc_submission_mst_id from com_export_doc_submission_invo",'invoice_id','doc_submission_mst_id');
	$realize_status_id_arr=return_library_array( "select invoice_bill_id, id from com_export_proceed_realization",'invoice_bill_id','id');
	$lc_arr=return_library_array( "select id, export_lc_no from com_export_lc where beneficiary_name=$cbo_company_name",'id','export_lc_no');
	$sc_arr=return_library_array( "select id, contract_no from com_sales_contract where beneficiary_name=$cbo_company_name",'id','contract_no');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_name_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$deling_marchent_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	if($db_type==2 || $db_type==1 )
	{
		$article_arr=return_library_array("select LISTAGG(cast(article_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY article_number) as article_number, po_break_down_id from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and article_number is not null group by po_break_down_id","po_break_down_id","article_number");
	}
	else if ($db_type==0)
	{
		$article_arr=return_library_array("select group_concat(distinct(article_number)) as article_number, po_break_down_id from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and article_number!='' group by po_break_down_id","po_break_down_id","article_number");
	}

	
	$buyer_cond = '';
	if($cbo_buyer_name != 0) $buyer_cond =" and b.buyer_name=$cbo_buyer_name";

	if($txt_style_ref != '')
	{
		if($txt_style_ref_id != '') $txt_style_ref_id="and b.id in($txt_style_ref_id)";
		else $txt_style_ref_id="and b.style_ref_no='$txt_style_ref'";
	}
	else $txt_style_ref_id = '';

	if($txt_order != '') 
	{
		if($txt_order_id != '') $txt_order_id="and a.id in($txt_order_id)";
		else $txt_order_id="and a.po_number='$txt_order'";
	} else $txt_order_id = '';
	
	$sql_order="SELECT a.id as ORDER_ID, a.JOB_NO_MST, a.PO_NUMBER, b.BUYER_NAME,b.STYLE_REF_NO, b.DEALING_MARCHANT, a.STATUS_ACTIVE
	from wo_po_break_down a, wo_po_details_master b
	where a.job_no_mst=b.job_no and b.company_name=$cbo_company_name $buyer_cond $txt_style_ref_id $txt_order_id  and a.status_active in(1,3) and a.is_deleted=0 and b.is_deleted=0 
	order by a.id";
	//echo $sql_order;die;
	$sql_order_res=sql_select($sql_order);
	$order_data_arr=array();$po_id=0;$job_no_all='';$job_arr=array();
	foreach($sql_order_res as $row)
	{
		//if($po_id==0) $po_id=$row[csf("order_id")]; else $po_id=$po_id.",".$row[csf("order_id")];
		$po_id_arr[$row['ORDER_ID']]=$row['ORDER_ID'];
		$order_data_arr[$row['ORDER_ID']]['ORDER_ID']=$row['ORDER_ID'];
		$order_data_arr[$row['ORDER_ID']]['JOB_NO_MST']=$row['JOB_NO_MST'];
		$order_data_arr[$row['ORDER_ID']]['PO_NUMBER']=$row['PO_NUMBER'];
		$order_data_arr[$row['ORDER_ID']]['BUYER_NAME']=$row['BUYER_NAME'];
		$order_data_arr[$row['ORDER_ID']]['STYLE_REF_NO']=$row['STYLE_REF_NO'];
		$order_data_arr[$row['ORDER_ID']]['DEALING_MARCHANT']=$row['DEALING_MARCHANT'];
		$order_data_arr[$row['ORDER_ID']]['STATUS_ACTIVE']=$row['STATUS_ACTIVE'];
		$job_no_all.="'".$row['JOB_NO_MST']."',";
		$job_arr[$row['ORDER_ID']]=$row['JOB_NO_MST'];
	}
	unset($sql_order_res);
	//var_dump($order_data_arr);die;
	//echo $sql;die;
	$buyer_cond_inv = '';
	if($cbo_buyer_name != 0) $buyer_cond_inv =" and d.BUYER_ID=$cbo_buyer_name";
	$sql_invoice="SELECT c.PO_BREAKDOWN_ID, c.mst_id as INVOICE_ID, c.id as INV_DTLS_ID, c.CURRENT_INVOICE_QNTY, c.CURRENT_INVOICE_VALUE, d.IS_LC, d.LC_SC_ID, d.INVOICE_NO, d.EXP_FORM_NO, c.ACTUAL_PO_INFOS
	from com_export_invoice_ship_dtls c, com_export_invoice_ship_mst d
	where c.mst_id=d.id and c.current_invoice_value >0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.BENIFICIARY_ID=$cbo_company_name $buyer_cond_inv";
	//echo $sql_invoice;//die;
	$invoice_result=sql_select($sql_invoice);
	$invoice_data_arr=array();
	foreach($invoice_result as $row)
	{
		$invoice_data_arr[$row['PO_BREAKDOWN_ID']][$row['INVOICE_ID']]['INVOICE_ID']=$row['INVOICE_ID'];
		$invoice_data_arr[$row['PO_BREAKDOWN_ID']][$row['INVOICE_ID']]['INV_DTLS_ID']=$row['INV_DTLS_ID'];
		$invoice_data_arr[$row['PO_BREAKDOWN_ID']][$row['INVOICE_ID']]['CURRENT_INVOICE_QNTY'] +=$row['CURRENT_INVOICE_QNTY'];
		$invoice_data_arr[$row['PO_BREAKDOWN_ID']][$row['INVOICE_ID']]['CURRENT_INVOICE_VALUE'] +=$row['CURRENT_INVOICE_VALUE'];
		$invoice_data_arr[$row['PO_BREAKDOWN_ID']][$row['INVOICE_ID']]['IS_LC'] =$row['IS_LC'];
		$invoice_data_arr[$row['PO_BREAKDOWN_ID']][$row['INVOICE_ID']]['LC_SC_ID'] =$row['LC_SC_ID'];
		$invoice_data_arr[$row['PO_BREAKDOWN_ID']][$row['INVOICE_ID']]['INVOICE_NO'] =$row['INVOICE_NO'];
		$invoice_data_arr[$row['PO_BREAKDOWN_ID']][$row['INVOICE_ID']]['EXP_FORM_NO'] =$row['EXP_FORM_NO'];
		$ac_po_data=explode("**",$row['ACTUAL_PO_INFOS']);
		foreach($ac_po_data as $ac_pos)
		{
			$ac_pos_ref=explode("=",$ac_pos);
			if($ac_pos_ref[2]!="")
			{
				$acc_po_arr[$row['PO_BREAKDOWN_ID']].=$ac_pos_ref[2].', ';
			}
		}
	}
	unset($invoice_result);
	//echo print_r($acc_po_arr);die;
	/* $po_id_in=where_con_using_array($po_id_arr,0,'po_break_down_id');
	$acc_po_sql = sql_select("SELECT PO_BREAK_DOWN_ID,ACC_PO_NO from wo_po_acc_po_info where status_active=1 $po_id_in"); 
	$acc_po_arr=array();
	foreach($acc_po_sql as $row)
	{
		$acc_po_arr[$row['PO_BREAK_DOWN_ID']].=$row['ACC_PO_NO'].', ';
	} */
	//$sql_result=sql_select($sql);
	//var_dump($invoice_data_arr);die;
	if($rpt_type==4)
	{
		$div_width=1180;
		$tbl_width=1162;
		
		$job_no_all=implode(",",array_unique(explode(",",chop($job_no_all,','))));

		$condition= new condition();
		$condition->job_no("in ($job_no_all)");
		// $condition->jobid_in("$all_job_id");
		$condition->init();
		$yarn= new yarn($condition);
		$yarn_costing_arr=$yarn->getJobWiseYarnAmountArray();
		$fabric= new fabric($condition);
		$fabric_costing_arr2=$fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
		$conversion= new conversion($condition);
		$conversion_costing_arr_process=$conversion->getAmountArray_by_job();
		$trims= new trims($condition);
		$trims_costing_arr=$trims->getAmountArray_by_job();
		$emblishment= new emblishment($condition);
		$emblishment_costing_arr=$emblishment->getAmountArray_by_job();
		$wash= new wash($condition);
		$emblishment_costing_arr_wash=$wash->getAmountArray_by_job();
		$commercial= new commercial($condition);
		$commercial_costing_arr=$commercial->getAmountArray_by_job();
		$commission= new commision($condition);
		$commission_costing_arr=$commission->getAmountArray_by_job();
		$other= new other($condition);
		$other_costing_arr=$other->getAmountArray_by_job();

		$sql_trim_summ = "SELECT id, job_no, trim_group,description,brand_sup_ref,remark, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active,seq
		from wo_pre_cost_trim_cost_dtls
		where job_no in ($job_no_all) and status_active=1 and is_deleted=0 order by seq";
		$data_array_trim_summ=sql_select($sql_trim_summ);
		$trim_amount_arr=$trims->getAmountArray_precostdtlsid();					
		foreach( $data_array_trim_summ as $row )
		{
			$trim_amount=$trim_amount_arr[$row[csf("id")]];
			$trim_job_amountArr[$row[csf("job_no")]]+=$trim_amount;
		}

		$pre_cost_dtls_sql = "SELECT job_no,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent,certificate_pre_cost,certificate_percent,common_oh,common_oh_percent,total_cost,deffdlc_cost,deffdlc_percent,interest_cost,interest_percent,incometax_cost,incometax_percent,total_cost_percent,price_dzn,price_dzn_percent,margin_dzn,margin_dzn_percent,price_pcs_or_set,depr_amor_pre_cost,depr_amor_po_price,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche
		from wo_pre_cost_dtls
		where job_no in ($job_no_all) and status_active=1 and is_deleted=0";
		// echo $pre_cost_dtls_sql;
		$pre_cost_dtls_data=sql_select($pre_cost_dtls_sql);
		$all_total_cost=array();
		foreach($pre_cost_dtls_data as $row){
			$job_no=$row[csf("job_no")];
			$fab_purchase_knit2=array_sum($fabric_costing_arr2['knit']['grey'][$job_no]);
			$fab_purchase_woven2=array_sum($fabric_costing_arr2['woven']['grey'][$job_no]);
			$yarn_costing=$yarn_costing_arr[$job_no];
			$tot_fabric_cost=$fab_purchase_knit2+$fab_purchase_woven2;
			$conversion_cost=array_sum($conversion_costing_arr_process[$job_no]);
			$freight_cost=$other_costing_arr[$job_no]['freight'];
			$inspection_cost=$other_costing_arr[$job_no]['inspection'];
			$certificate_cost=$other_costing_arr[$job_no]['certificate_pre_cost'];
			$common_oh=$other_costing_arr[$job_no]['common_oh'];
			$currier_cost=$other_costing_arr[$job_no]['currier_pre_cost'];
			$cm_cost=$other_costing_arr[$job_no]['cm_cost'];
			$lab_test_cost=$other_costing_arr[$job_no]['lab_test'];
			$depr_amor_pre_cost=$other_costing_arr[$job_no]['depr_amor_pre_cost'];
			$deffdlc_cost=$other_costing_arr[$job_no]['deffdlc_cost'];
			$fabric_cost=$tot_fabric_cost;
			$trims_cost=$trim_job_amountArr[$job_no];
			$embel_cost=$emblishment_costing_arr[$job_no];
			$wash=$emblishment_costing_arr_wash[$job_no];
			$interest_cost=$row[csf("interest_cost")];
			$incometax_cost=$row[csf("incometax_cost")];

			$lab_test=$lab_test_cost;
			$inspection=$inspection_cost;

			$currier_pre_cost=$currier_cost;
			$certificate_pre_cost=$certificate_cost;
			$common_oh=$common_oh;

			$all_total_cost[$job_no]=$tot_fabric_cost+$yarn_costing+$conversion_cost+$trims_cost+$embel_cost+$wash+$lab_test_cost+$currier_pre_cost+$inspection_cost+$common_oh+$certificate_pre_cost+$depr_amor_pre_cost+$interest_cost+$incometax_cost+$deffdlc_cost;
		}

		$order_sql = "SELECT a.id as ID, a.job_no as JOB_NO, a.avg_unit_price as AVG_UNIT_PRICE, sum(b.po_quantity) as ORD_QTY, LISTAGG(b.id, ',') WITHIN GROUP (ORDER BY b.id) as PO_ID, c.costing_per as COSTING_PER 
		from wo_po_details_master a,wo_po_break_down b, wo_pre_cost_mst c
		where a.id=b.job_id and a.job_no in ($job_no_all) and b.job_no_mst=c.job_no  and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id,a.avg_unit_price,a.job_no, c.costing_per";
		$order_data=sql_select($order_sql);
		$cmPerDzn=array();
		$costPerDzn=array();
		foreach($order_data as $row){
			$po_id_arr=explode(",",$row['PO_ID']);
			if($row[csf("costing_per")]==1){$order_price_per_dzn=12;}
			else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;}
			else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;}
			else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;}
			else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;}
			foreach($po_id_arr as $val){
				$less_commission=$commission_costing_arr[$job_arr[$val]];
				$less_commercial=$commercial_costing_arr[$job_arr[$val]];
				$less_freight=$other_costing_arr[$job_arr[$val]]['freight'];
				$order_net_value=($row['ORD_QTY']*$row['AVG_UNIT_PRICE'])-($less_commission+$less_commercial+$less_freight);
				$cmValue = $order_net_value-$all_total_cost[$job_arr[$val]];
				$cmPerDzn[$val]=$cmValue/$row['ORD_QTY']*$order_price_per_dzn;
				$costPerDzn[$val]=$order_price_per_dzn;
			}
		}
	}
	else
	{
		$div_width=1000;
		$tbl_width=982;
	}

	ob_start();	
	?>
	<div style="width:<?=$div_width;?>"> 
		<table width="<?=$div_width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="center"> 
				<tr class="form_caption" style="border:none;">
					<td colspan="8" align="center" style="border:none;font-size:16px; font-weight:bold" >Order Wise Export Invoice Report </td> 
				</tr>
				<tr style="border:none;">
					<td colspan="8" align="center" style="border:none; font-size:14px;">
						Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>                      
					</td>
				</tr>
		   </table>
		   <br />
		<table width="<?=$div_width;?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="center"> 
			<thead>
				<tr>
					<th width="80" >SL</th>
					<th width="120" >Invoice No</th>
					<th width="120" >Lc No</th>
					<th width="120" >Invoice Qty</th>
					<th width="120">Invoice Amount</th>
					<th width="120">Submit Status</th>
					<th width="120">Realize Status</th>
					<?
						if($rpt_type==4)
						{
							?>
								<th width="60">CM/Dzn</th>
								<th width="90">CM Value Per Pcs</th>
								<th width="80">Total CM as per Invoice Qty</th>
							<?
						}
					?>
					<th >Dealing Merchandiser</th>
				</tr>
			</thead>
	    </table> 
	  	<div style="width:<?=$div_width;?>px; overflow-y: scroll; max-height:250px;" id="scroll_body">
			<table width="<?=$tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
			<?
			$i=1;
			$order_arr=array();
			$sub_total_inv_qty=0;
			$sub_total_inv_value=0;
			$grand_total_qty=0;
			$grand_total_val=0;
			foreach($order_data_arr as $order_id=>$val)
			{
				?>
				<tr bgcolor="#ECDFE9">
					<td width="80"  style="border-color:#ECDFE9" align="right"><b>Order No:</b>&nbsp;</td>
					<td width="120"  style="border-color:#ECDFE9" title="<?= $order_id; ?>"><? echo $val['PO_NUMBER']; ?> </td>
					<td width="120"  style="border-color:#ECDFE9" align="right"><b>Buyer:</b>&nbsp;</td>
					<td width="120"  style="border-color:#ECDFE9"><? echo $buyer_name_arr[$val['BUYER_NAME']]; ?></td>
					<td width="120"  style="border-color:#ECDFE9" align="right"><b>Style Ref No</b>&nbsp;</td>
					<td width="120"  style="border-color:#ECDFE9" >:<? echo trim($val['STYLE_REF_NO']); ?></td>
					<td width="120" style="border-color:#ECDFE9" align="right"><b>Article No:</b>&nbsp;</td>
					<?
						if($rpt_type==4)
						{
							?>
								<td width="230"  colspan="3" ></td>
							<?
						}
					?>
					<td>
					<?  
						//$article=explode(",",$article_arr[$order_id]);
						$article=implode(",",array_unique(explode(",",$article_arr[$order_id])));
						 echo $article."&nbsp;"; if($val['STATUS_ACTIVE']==3) echo '<span style="color:#F00; font-size:14px;">Cancelled</span>';
					?>
					</td>
				</tr>
				<tr bgcolor="#ECDFE9">
					<td style="border-color:#ECDFE9" align="right"><b>Actual PO: </b>&nbsp;</td>
					<td colspan="<?echo ($rpt_type==4)? 10:7;?>" style="border-color:#ECDFE9" ><? echo rtrim($acc_po_arr[$order_id],', ');?></td>
				</tr>
				<?
				$j=1;				
				foreach($invoice_data_arr[$order_id] as $invoice_id=>$row)
				{
					if ($j%2==0) $bgcolor="#E9F3FF";
					else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor;?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td  align="center"><? echo $j; ?></td>
						<td ><a href='##' onClick="print_report('<?=$invoice_id;?>','invoice_report_print','../export_details/requires/export_information_entry_controller')"><? echo $row['INVOICE_NO']; ?></a> </td>
						<td >
							<?
							if($row['IS_LC']==1){ $lc_sc_no=$lc_arr[$row['LC_SC_ID']];}
							else{ $lc_sc_no=$sc_arr[$row['LC_SC_ID']]; }
							?>
							<a href='##' onClick="openmypage('<?=$row['IS_LC'];?>','<?=$row['LC_SC_ID'];?>','<?=$lc_sc_no;?>')"><?=$lc_sc_no; ?></a>
						</td>
						<td  align="right"><a href='#report_detals'  onclick= "openmypage_invoice('<?=$order_id; ?>','<?=$invoice_id; ?>')"><? echo $row['CURRENT_INVOICE_QNTY'];?></a></td>
						<td  align="right"><? 
						echo number_format($row['CURRENT_INVOICE_VALUE'],4);
						?></td>
						<td align="center" >
						<?
							//$submission_status_id=return_field_value("a.doc_submission_mst_id","com_export_doc_submission_invo a","a.invoice_id='".$row[("invoice_id")]."'","doc_submission_mst_id");
							$submission_status_id=$submission_status_id_arr[$row['INVOICE_ID']];							
							if($submission_status_id != '') echo 'Submitted'; else echo ''; 	
						?>
						</td>
						<td align="center">
						<?
							$submission_bank_id=$submission_bank_arr[$row['INVOICE_ID']];
							if($realize_status_id_arr[$submission_bank_id] != '') {echo 'Realized';} else {echo '';} 	
						?>
						</td>
						<?
							if($rpt_type==4)
							{
								$cm_dzn=$cmPerDzn[$order_id];
								$cm_dzn_per=$cmPerDzn[$order_id]/$costPerDzn[$order_id];
								$cm_dzn_inv_qty=($cmPerDzn[$order_id]/$costPerDzn[$order_id])*$row['CURRENT_INVOICE_QNTY'];
								?>
									<td width="60" align="right"><?echo number_format($cm_dzn,4);?></td>
									<td width="90" align="right"><?echo number_format($cm_dzn_per,4);?></td>
									<td width="80" align="right"><?echo number_format($cm_dzn_inv_qty,4);?></td>
								<?
							}
						?>
						<td align="center"><? echo $deling_marchent_arr[$val['DEALING_MARCHANT']]; ?></td>
					</tr>
					<?
					$sub_total_inv_qty +=$row['CURRENT_INVOICE_QNTY'];
					
					$sub_total_inv_value +=$row['CURRENT_INVOICE_VALUE'];
					$sub_total_cm_dzn_inv_qty +=$cm_dzn_inv_qty;
					
					$grand_total_qty +=$row['CURRENT_INVOICE_QNTY'];
					$grand_total_val +=$row['CURRENT_INVOICE_VALUE'];
					$grand_total_cm_dzn_inv_qty +=$cm_dzn_inv_qty;
					$j++;$i++;
				}
				?>
				
				<tr bgcolor="#ccc">
					<td  colspan="3" align="right" ><b>Sub-total</b></td>
					<td align="right"><b><? echo $sub_total_inv_qty; ?> </b></td>
					<td  align="right"><b><? echo number_format($sub_total_inv_value,4); ?></b></td>
					<td ></td>
					<td ></td>
					<?
						if($rpt_type==4)
						{
							?>
								<td ></td>
								<td ></td>
								<td align="right"><b><?=number_format($sub_total_cm_dzn_inv_qty,4);?></b></td>
							<?
						}
					?>
					<td ></td>
				</tr>
				<?
				$sub_total_inv_qty="";
				$sub_total_inv_value="";
				$sub_total_cm_dzn_inv_qty="";
			}
			?>    
			<tr bgcolor="#ccc">
				<td  colspan="3" align="right" ><b>Grand Total</b></td>
				<td  align="right"><b><? echo $grand_total_qty; ?></b> </td>
				<td  align="right"><b><? echo number_format($grand_total_val,4); ?></b></td>
				<td >&nbsp;</td>
				<td >&nbsp;</td>
				<?
					if($rpt_type==4)
					{
						?>
							<td ></td>
							<td ></td>
							<td align="right"><b><?=number_format($grand_total_cm_dzn_inv_qty,4);?></b></td>
						<?
					}
				?>
				<td >&nbsp;</td>
			</tr>
			<!--<script language="javascript"> setFilterGrid('table_body',-1)</script>  -->
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

if($action=="lcsc_popup")
{
 	echo load_html_head_contents("LC/SC Details", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $id;//$job_no;
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:400px">
        <div class="form_caption" align="center"><strong>LC/SC Details</strong></div><br />
            <div style="width:100%">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="120">LC/SC No.</th>
                        <th width="120">Invoice No.</th>
                        <th >Invoice Date</th>
                     </tr>
                </thead>
            </table>
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i=1;
				$sql=("SELECT INVOICE_NO, INVOICE_DATE
				from com_export_invoice_ship_mst where is_lc=$is_lc and lc_sc_id=$lc_sc_id and status_active=1 ");
				//echo $sql;

                $sql_data=sql_select($sql);

                foreach($sql_data as $row)
                {
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="35"><? echo $i; ?></td>
                        <td width="120"><? echo $lc_sc_no; ?></td>
                        <td width="120"><? echo $row["INVOICE_NO"]; ?></td>
                        <td align="center"><? echo change_date_format($row["INVOICE_DATE"]); ?></td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
            </table>
        </div>
		</fieldset>
	</div>
	<?
    exit();
}


if($action=="po_id_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$invoice_id=str_replace("'","",$invoice_id);
	//print_r($po_id);die;
	?>

	<div style="width:970px">
	<fieldset style="width:100%"  >
	    <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="970">
	        <thead>
	            <th width="120">Order NO</th>
	            <th width="120">Style No</th>
	            <th width="80">Ship Date</th>
	            <th width="80">Order Qty (Pcs)</th>
	            <th width="70">Unit Price (P.O)</th>
	            <th width="80">Order Value ($)</th>
	            <th width="80">Attach Qnty (Pcs)</th>
	            <th width="70">Unit Price (LC/SC)</th>
	            <th width="80">Attach Value ($)</th>
	            <th width="80">Invoice Qnty (Pcs)</th>
	            <th width="70">Unit Price (Invoice)</th>
	            <th width="80">Invoice Value ($)</th>
	            <th width="80">Unit Price Diff. (LC-Inv)</th>
	        </thead>
	        <tbody>
			<?
			$lc_attach_sql=sql_select("SELECT b.com_export_lc_id as lc_sc_id, b.wo_po_break_down_id, b.attached_qnty, b.attached_rate, b.attached_value, 1 as type from com_export_lc_order_info b where b.wo_po_break_down_id=$order_id and b.status_active=1 and b.is_deleted=0
			union all
			select b.com_sales_contract_id as lc_sc_id, b.wo_po_break_down_id, b.attached_qnty, b.attached_rate, b.attached_value, 2 as type from  com_sales_contract_order_info b where b.wo_po_break_down_id=$order_id and b.status_active=1 and b.is_deleted=0");
			$lc_sc_qnty_arr=array();
			foreach($lc_attach_sql as $row)
			{
				$lc_sc_qnty_arr[$row[csf("lc_sc_id")]][$row[csf("type")]][$row[csf("wo_po_break_down_id")]]["attached_qnty"]=$row[csf("attached_qnty")];
				$lc_sc_qnty_arr[$row[csf("lc_sc_id")]][$row[csf("type")]][$row[csf("wo_po_break_down_id")]]["attached_rate"]=$row[csf("attached_rate")];
				$lc_sc_qnty_arr[$row[csf("lc_sc_id")]][$row[csf("type")]][$row[csf("wo_po_break_down_id")]]["attached_value"]=$row[csf("attached_value")];
			}
			$sql="SELECT a.lc_sc_id, a.is_lc, (b.current_invoice_qnty*d.total_set_qnty) as current_invoice_qnty, (b.current_invoice_rate/d.total_set_qnty) as current_invoice_rate, b.current_invoice_value, c.id as po_id, c.po_number, c.pub_shipment_date, (c.po_quantity*d.total_set_qnty) as po_quantity, (c.unit_price/d.total_set_qnty) as unit_price, c.po_total_price, d.style_ref_no, d.total_set_qnty 
			from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b, wo_po_break_down c ,wo_po_details_master d 
			where a.id=b.mst_id and b.po_breakdown_id=c.id and c.job_no_mst = d.job_no  and a.id=$invoice_id and c.id=$order_id and b.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			//echo $sql;

			$i=1;
	        $sql_re=sql_select($sql);
	        $total_invoice_qty=0;  $total_order_qty=0;  $total_attach_qty=0;
	        $result=0;
	        foreach($sql_re as $row)
	        {
	        ?>

	            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                <td><? echo $row[csf('po_number')]; ?>&nbsp;</td>
	                <td><? echo $row[csf('style_ref_no')]; ?>&nbsp;</td>
	                <td align="center"><? if($row[csf('pub_shipment_date')]!="" && $row[csf('pub_shipment_date')]!="0000-00-00") echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
	                <td align="right"><?  echo number_format($row[csf('po_quantity')],0);  $total_order_qty +=$row[csf('po_quantity')]; ?> </td>
	                <td align="right"><?  echo number_format($row[csf('unit_price')],4);  ?> </td>
	                <td align="right"><?  echo number_format($row[csf('po_total_price')],2); $total_order_value+=$row[csf('po_total_price')]; ?></td>

	                <td align="right"><?  echo number_format($lc_sc_qnty_arr[$row[csf("lc_sc_id")]][$row[csf("is_lc")]][$row[csf("po_id")]]["attached_qnty"]*$row['TOTAL_SET_QNTY'],0);  $total_lc_qty +=$lc_sc_qnty_arr[$row[csf("lc_sc_id")]][$row[csf("is_lc")]][$row[csf("po_id")]]["attached_qnty"]*$row['TOTAL_SET_QNTY']; ?> </td>
	                <td align="right"><?  echo number_format($lc_sc_qnty_arr[$row[csf("lc_sc_id")]][$row[csf("is_lc")]][$row[csf("po_id")]]["attached_rate"]/$row['TOTAL_SET_QNTY'],4);  ?> </td>
	                <td align="right"><?  echo number_format($lc_sc_qnty_arr[$row[csf("lc_sc_id")]][$row[csf("is_lc")]][$row[csf("po_id")]]["attached_value"],2); $total_lc_value+=$lc_sc_qnty_arr[$row[csf("lc_sc_id")]][$row[csf("is_lc")]][$row[csf("po_id")]]["attached_value"]; ?></td>
	                <td align="right"><?  echo number_format($row[csf('current_invoice_qnty')],0);  $total_invoice_qty +=$row[csf('current_invoice_qnty')]; ?> </td>
	                <td align="right"><?  echo number_format($row[csf('current_invoice_rate')],4);  ?> </td>
	                <td align="right"><?  echo number_format($row[csf('current_invoice_value')],2); $total_invoice_value+=$row[csf('current_invoice_value')]; ?></td>
	            	<td align="right" title="LC Rate-Invoice Rate"><? $rate_dev=($lc_sc_qnty_arr[$row[csf("lc_sc_id")]][$row[csf("is_lc")]][$row[csf("po_id")]]["attached_rate"]/$row['TOTAL_SET_QNTY'])-$row[csf('current_invoice_rate')];  echo number_format($rate_dev,4);  ?> </td>
	            </tr>
			<?
			$i++;
	        }
	        ?>
	        </tbody>
	        <tfoot>
	            <tr >
	                <th align="right">&nbsp;</th>
	                <th align="right">&nbsp;</th>
	                <th align="right" >Total</th>
	                <th align="right"><? echo number_format($total_order_qty,0); ?></th>
	                <th align="right">&nbsp;</th>
	                <th align="right"><? echo number_format($total_order_value,2); ?></th>
	                <th align="right"><? echo number_format($total_lc_qty,0); ?></th>
	                <th align="right">&nbsp;</th>
	                <th align="right"><? echo number_format($total_lc_value,2); ?></th>
	                <th align="right"><? echo number_format($total_invoice_qty,0); ?></th>
	                <th align="right">&nbsp;</th>
	                <th align="right"><? echo number_format($total_invoice_value,2); ?></th>
	                <th align="right">&nbsp;</th>
	            </tr>
	        </tfoot>
	    </table>
	</fieldset>
	</div>
	<?
	exit();
}
?>

