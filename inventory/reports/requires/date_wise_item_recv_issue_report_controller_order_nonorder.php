<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($db_type==0)
{
	$select_year="year";
	$year_con="";
}
else
{
	$select_year="to_char";
	$year_con=",'YYYY'";
}


//--------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------

$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$buyer_name_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
$buyer_short_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
$supplier_arr=return_library_array( "select id, short_name from  lib_supplier",'id','short_name');
$user_name_arr=return_library_array( "select id, user_name from  user_passwd",'id','user_name');
//$yarn_requisition_arr=return_library_array( "select id, requisition_no from  ppl_yarn_requisition_entry",'id','requisition_no');
//$yarn_booking_arr=return_library_array( "select id, booking_no from  wo_booking_mst",'id','booking_no');
$composition_arr=array();
$construction_arr=array();
$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
$data_array=sql_select($sql_deter);
if(count($data_array)>0)
{
	
/*foreach( $data_array as $row )
{
	if(array_key_exists($row[csf('id')],$composition_arr))
	{
		$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]].$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	else
	{
		$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
}*/

	foreach( $data_array as $row )
	{
		if(array_key_exists($row[csf('id')],$construction_arr))
		{
			$construction_arr[$row[csf('id')]]=$construction_arr[$row[csf('id')]];
		}
		else
		{
			$construction_arr[$row[csf('id')]]=$row[csf('construction')];
		}
		if(array_key_exists($row[csf('id')],$composition_arr))
		{
			$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]];
		}
		else
		{
			$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
	}
}

//load drop down Buyer
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0); 
	exit();
}




//style search------------------------------//
if($action=="style_refarence_surch")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	//echo $style_id;die;

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
				
				toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
				
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
		
		
		
    </script>
    <?
	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$cbo_year=str_replace("'","",$cbo_year);
	if($buyer!=0) $buyer_cond=" and buyer_name=$buyer"; else $buyer_cond="";
	if($cbo_year!=0){ if($db_type==0) $year_cond=" and year(insert_date)='$cbo_year'"; else  $year_cond=" and to_char(insert_date,'YYYY')='$cbo_year'";}else {$year_cond="";}
	//echo $year_cond.jahid;die;
	$sql = "select id,style_ref_no,job_no,job_no_prefix_num,$select_year(insert_date $year_con) as year from wo_po_details_master where company_name=$company $buyer_cond $year_cond  and is_deleted=0 order by job_no_prefix_num"; 
	//echo $sql; die;
	echo create_list_view("list_view", "Style Ref No,Job No,Year","160,90,100","400","310",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_style_ref_no;?>';
	var style_id='<? echo $txt_style_ref_id;?>';
	var style_des='<? echo $txt_style_ref;?>';
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
	</script>
    
    <?
	
	exit();
}


//order search------------------------------//
if($action=="order_surch")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
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
    </script>
    <?
	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$style_all=str_replace("'","",$style_all);
	
	if($buyer!=0) $buyer_cond="and b.buyer_name=$buyer"; else $buyer_cond="";
	if($style_all!="") $style_cond="and b.id in($style_all)"; else $style_cond="";
	$sql = "select a.id,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$select_year(b.insert_date $year_con) as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and b.company_name=$company $buyer_cond  $style_cond and a.status_active=1"; 
	//echo $sql; die;
	echo create_list_view("list_view", "Order NO,Job No,Year,Style Ref No","150,80,70,150","500","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_no_prefix_num,year,style_ref_no", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	
	?>
    <script language="javascript" type="text/javascript">
	var order_no='<? echo $txt_order_id_no; ?>';
	var order_id='<? echo $txt_order_id;?>';
	var order_des='<? echo $txt_order;?>';
	//alert(style_id);
	if(order_no!="")
	{
		order_no_arr=order_no.split(",");
		order_id_arr=order_id.split(",");
		order_des_arr=order_des.split(",");
		var order_ref="";
		for(var k=0;k<order_no_arr.length; k++)
		{
			order_ref=order_no_arr[k]+'_'+order_id_arr[k]+'_'+order_des_arr[k];
			js_set_value(order_ref);
		}
	}
	</script>
    
    <?
	
	exit();
}



//report generated here--------------------//
if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_item_cat=str_replace("'","",$cbo_item_cat);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_style_ref_id=str_replace("'","",$txt_style_ref_id);
	$txt_order=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_dyed_type=str_replace("'","",$cbo_dyed_type);
	$cbo_yarn_count=str_replace("'","",$cbo_yarn_count);
	$rptType=str_replace("'","",$rptType);
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	//echo $cbo_based_on;die;
	


/*$receive_num_arr=return_library_array( "select id, recv_number from  inv_receive_master",'id','recv_number');
$issue_num_arr=return_library_array( "select id, issue_number from  inv_issue_master",'id','issue_number');
*/
	$receive_sql=sql_select("select id, recv_number, challan_no, supplier_id,knitting_source,knitting_company,currency_id,exchange_rate from  inv_receive_master where status_active=1 and is_deleted=0");
	foreach($receive_sql as $row)
	{
		$receive_num_arr[$row[csf("id")]]["id"]=$row[csf("id")];
		$receive_num_arr[$row[csf("id")]]["recv_number"]=$row[csf("recv_number")];
		$receive_num_arr[$row[csf("id")]]["challan_no"]=$row[csf("challan_no")];
		$receive_num_arr[$row[csf("id")]]["supplier_id"]=$row[csf("supplier_id")];
		$receive_num_arr[$row[csf("id")]]["knitting_source"]=$row[csf("knitting_source")];
		$receive_num_arr[$row[csf("id")]]["knitting_company"]=$row[csf("knitting_company")];
		$receive_num_arr[$row[csf("id")]]["currency_id"]=$row[csf("currency_id")];
		$receive_num_arr[$row[csf("id")]]["exchange_rate"]=$row[csf("exchange_rate")];
	}
	
	$receive_sql=sql_select("select id, issue_number, challan_no,req_no,knit_dye_source,knit_dye_company from inv_issue_master where status_active=1 and is_deleted=0");
	foreach($receive_sql as $row)
	{
		$issue_num_arr[$row[csf("id")]]["id"]=$row[csf("id")];
		$issue_num_arr[$row[csf("id")]]["issue_number"]=$row[csf("issue_number")];
		$issue_num_arr[$row[csf("id")]]["challan_no"]=$row[csf("challan_no")];
		$issue_num_arr[$row[csf("id")]]["req_no"]=$row[csf("req_no")];
		$issue_num_arr[$row[csf("id")]]["knit_dye_source"]=$row[csf("knit_dye_source")];
		$issue_num_arr[$row[csf("id")]]["knit_dye_company"]=$row[csf("knit_dye_company")];
	}
	//var_dump($receive_num_arr);die;
	//echo $cbo_item_cat;die;
	if($db_type==0)
	{
		if($cbo_based_on==1)
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.transaction_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' "; else $date_cond=""; 
		}
		else
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.insert_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."  00:00:01' and '".change_date_format($txt_date_to,"yyyy-mm-dd")." 23:59:59' "; else $date_cond="";
		}
		$select_insert_date="DATE_FORMAT(a.insert_date,'%d-%m-%Y') as insert_date";
		$select_insert_time="DATE_FORMAT(a.insert_date,'%H:%i:%S') as insert_time";
	}
	else
	{
		if($cbo_based_on==1)
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.transaction_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' "; else $date_cond="";
		}
		else
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.insert_date between '".date("j-M-Y",strtotime($txt_date_from))."   01:00:01 AM' and '".date("j-M-Y",strtotime($txt_date_to))."  11:59:59 PM' "; else $date_cond="";
		}
		$select_insert_date=" to_char(a.insert_date,'DD-MON-YYYY')  as insert_date";//HH24:MI:SS
		$select_insert_time=" to_char(a.insert_date,'HH24:MI:SS')  as insert_time";
	}
	
	
	if($db_type==2) $null_field='null'; else if($db_type==0) $null_field="''";
	
	if($cbo_item_cat==4)
	{
		if($txt_style_ref!="")
		{
			if($txt_style_ref_id!="")
			{
				$txt_style_ref_id="and d.id in($txt_style_ref_id)";
			}
			else
			{
				$txt_style_ref_id="and d.job_no_prefix_num like'%$txt_style_ref'"; 
	
			}
		}
		else
		{
			 $txt_style_ref_id="";
		}
		if($txt_order!="") 
		{
			if($txt_order_id!="")
			{
				$txt_order_id="and c.id in($txt_order_id)";
			}
			else
			{
				$txt_order_id="and c.po_number='$txt_order'";
			}
		}
		else
		{
			$txt_order_id="";
		}
		//print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));
		if($cbo_buyer_name!=0) $cbo_buyer_name="and d.buyer_name=$cbo_buyer_name"; else $cbo_buyer_name="";
		
		
		//echo $date_cond;die;
		
		if($txt_style_ref!="" || $txt_order!="" || $cbo_buyer_name!=0)
		{
			if($rptType==1)
			{
				$sql="select 
						a.id as trans_id,
						b.po_breakdown_id as order_id,
						a.transaction_date,
						case when b.trans_type in(1,4) then b.quantity else 0 end as receive_qty,
						case when b.trans_type in(2,3) then b.quantity else 0 end as issue_qty,
						a.cons_uom,
						d.job_no,
						d.job_no_prefix_num,
						d.style_ref_no,
						$select_year(d.insert_date $year_con) as job_year,
						d.buyer_name,
						c.po_number,
						c.shipment_date,
						b.prod_id,
						a.transaction_type,
						a.mst_id,
						a.cons_rate,
						a.order_rate,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						a.order_qnty,
						p.item_description,
						p.product_name_details,
						p.color,
						p.item_color,
						p.gmts_size,
						p.item_size,
						p.item_group_id
					from
						inv_transaction a, order_wise_pro_details b, wo_po_break_down c,wo_po_details_master d, product_details_master p
					where
						a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and p.id=a.prod_id and a.item_category=4 and d.company_name=$cbo_company_name $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond  and a.status_active=1 and a.is_deleted=0 and b.entry_form in(24,25,49)  order by a.transaction_date, a.id";
			}
			else if($rptType==2)
			{
				$sql="select 
						a.id as trans_id,
						b.po_breakdown_id as order_id,
						a.transaction_date,
						b.quantity as receive_qty,
						'' as issue_qty,
						a.cons_uom,
						d.job_no,
						d.job_no_prefix_num,
						d.style_ref_no,
						$select_year(d.insert_date $year_con) as job_year,
						d.buyer_name,
						c.po_number,
						c.shipment_date,
						b.prod_id,
						a.transaction_type,
						a.mst_id,
						a.cons_rate,
						a.order_rate,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						a.order_qnty,
						p.item_description,
						p.product_name_details,
						p.color,
						p.item_color,
						p.gmts_size,
						p.item_size,
						p.item_group_id
					from
						inv_transaction a, order_wise_pro_details b, wo_po_break_down c,wo_po_details_master d, product_details_master p
					where
						a.id=b.trans_id  and p.id=a.prod_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.item_category=4 and d.company_name=$cbo_company_name $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond and b.trans_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.entry_form in(24)  order by a.transaction_date, a.id";
			}
			else if($rptType==3)
			{
				$sql="select 
						a.id as trans_id,
						b.po_breakdown_id as order_id,
						a.transaction_date,
						'' as receive_qty,
						b.quantity as issue_qty,
						a.cons_uom,
						d.job_no,
						d.job_no_prefix_num,
						d.style_ref_no,
						$select_year(d.insert_date $year_con) as job_year,
						d.buyer_name,
						c.po_number,
						c.shipment_date,
						b.prod_id,
						a.transaction_type,
						a.mst_id,
						a.cons_rate,
						a.order_rate,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						a.order_qnty,
						p.item_description,
						p.product_name_details,
						p.color,
						p.item_color,
						p.gmts_size,
						p.item_size,
						p.item_group_id
					from
						inv_transaction a, order_wise_pro_details b, wo_po_break_down c,wo_po_details_master d, product_details_master p
					where
						a.id=b.trans_id  and p.id=a.prod_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.item_category=4 and b.trans_type in(2,3) and d.company_name=$cbo_company_name $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond  and a.status_active=1 and a.is_deleted=0 and b.entry_form in(25,49)  order by a.transaction_date, a.id";
			}
		}
		else
		{
			if($rptType==1)
			{
				$sql="select 
						a.id as trans_id,
						b.po_breakdown_id as order_id,
						a.transaction_date,
						case when b.trans_type in(1,4,5) then b.quantity else 0 end as receive_qty,
						case when b.trans_type in(2,3,6) then b.quantity else 0 end as issue_qty,
						a.cons_uom,
						d.job_no,
						d.job_no_prefix_num,
						d.style_ref_no,
						$select_year(d.insert_date $year_con) as job_year,
						d.buyer_name,
						c.po_number,
						c.shipment_date,
						b.prod_id,
						a.transaction_type,
						a.mst_id,
						a.cons_rate,
						a.order_rate,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						a.order_qnty,
						p.item_description,
						p.product_name_details,
						p.color,
						p.item_color,
						p.gmts_size,
						p.item_size,
						p.item_group_id,
						1 as type
						
					from
						inv_transaction a, order_wise_pro_details b, wo_po_break_down c,wo_po_details_master d, product_details_master p
					where
						a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and p.id=b.prod_id and a.item_category=4 and d.company_name=$cbo_company_name $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond  and a.status_active=1 and a.is_deleted=0 and b.entry_form in(24,25,49)
						
					union all
					
					select 
						a.id as trans_id,
						0 as order_id,
						a.transaction_date,
						0 as receive_qty,
						a.cons_quantity as issue_qty,
						a.cons_uom,
						$null_field as job_no,
						$null_field as job_no_prefix_num,
						$null_field as style_ref_no,
						$null_field as job_year,
						$null_field as buyer_name,
						$null_field as po_number,
						$null_field as shipment_date,
						p.id as prod_id,
						a.transaction_type,
						a.mst_id,
						a.cons_rate,
						a.order_rate,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						a.order_qnty,
						p.item_description,
						p.product_name_details,
						p.color,
						p.item_color,
						p.gmts_size,
						p.item_size,
						p.item_group_id,
						2 as type
						
					from
						inv_transaction a, product_details_master p,  inv_issue_master c
					where
						a.prod_id=p.id and c.id=a.mst_id and a.item_category=4 and c.issue_basis=2 and a.company_id=$cbo_company_name  $date_cond  and a.status_active=1 and a.is_deleted=0 and c.entry_form=25
						
					union all
					
					select 
						a.id as trans_id,
						0 as order_id,
						a.transaction_date,
						case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end as receive_qty,
						case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end as issue_qty,
						a.cons_uom,
						$null_field as job_no,
						$null_field as job_no_prefix_num,
						$null_field as style_ref_no,
						$null_field as job_year,
						$null_field as buyer_name,
						$null_field as po_number,
						$null_field as shipment_date,
						p.id as prod_id,
						a.transaction_type,
						a.mst_id,
						a.cons_rate,
						a.order_rate,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						a.order_qnty,
						p.item_description,
						p.product_name_details,
						p.color,
						p.item_color,
						p.gmts_size,
						p.item_size,
						p.item_group_id,
						3 as type
						
					from
						inv_transaction a, product_details_master p
					where
						a.prod_id=p.id and a.item_category=4 and a.company_id=$cbo_company_name  $date_cond  and a.status_active=1 and a.is_deleted=0 and p.entry_form=20	
					order by transaction_date, trans_id";
			}
			else if($rptType==2)
			{
				$sql="select 
						a.id as trans_id,
						b.po_breakdown_id as order_id,
						a.transaction_date,
						b.quantity as receive_qty,
						0 as issue_qty,
						a.cons_uom,
						d.job_no,
						d.job_no_prefix_num,
						d.style_ref_no,
						$select_year(d.insert_date $year_con) as job_year,
						d.buyer_name,
						c.po_number,
						c.shipment_date,
						b.prod_id,
						a.transaction_type,
						a.mst_id,
						a.cons_rate,
						a.order_rate,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						a.order_qnty,
						p.item_description,
						p.product_name_details,
						p.color,
						p.item_color,
						p.gmts_size,
						p.item_size,
						p.item_group_id,
						1 as type
					from
						inv_transaction a, order_wise_pro_details b, wo_po_break_down c,wo_po_details_master d , product_details_master p
					where
						a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and p.id=b.prod_id and a.item_category=4 and d.company_name=$cbo_company_name $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond and b.trans_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.entry_form in(24,25)
						
					union all
					
					select 
						a.id as trans_id,
						0 as order_id,
						a.transaction_date,
						a.cons_quantity as receive_qty,
						0 as issue_qty,
						a.cons_uom,
						$null_field as  job_no,
						$null_field as job_no_prefix_num,
						$null_field as style_ref_no,
						$null_field as job_year,
						$null_field as buyer_name,
						$null_field as po_number,
						$null_field as shipment_date,
						p.id as prod_id,
						a.transaction_type,
						a.mst_id,
						a.cons_rate,
						a.order_rate,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						a.order_qnty,
						p.item_description,
						p.product_name_details,
						p.color,
						p.item_color,
						p.gmts_size,
						p.item_size,
						p.item_group_id,
						2 as type
					from
						inv_transaction a, product_details_master p
					where
						a.prod_id=p.id and a.item_category=4 and a.company_id=$cbo_company_name  $date_cond  and a.status_active=1 and a.is_deleted=0 and p.entry_form=20	
					order by transaction_date, trans_id";
			}
			else if($rptType==3)
			{
				$sql="select 
						a.id as trans_id,
						b.po_breakdown_id as order_id,
						a.transaction_date,
						0 as receive_qty,
						b.quantity as issue_qty,
						a.cons_uom,
						d.job_no,
						d.job_no_prefix_num,
						d.style_ref_no,
						$select_year(d.insert_date $year_con) as job_year,
						d.buyer_name,
						c.po_number,
						c.shipment_date,
						b.prod_id,
						a.transaction_type,
						a.mst_id,
						a.cons_rate,
						a.order_rate,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						a.order_qnty,
						p.item_description,
						p.product_name_details,
						p.color,
						p.item_color,
						p.gmts_size,
						p.item_size,
						p.item_group_id,
						1 as type
					from
						inv_transaction a, order_wise_pro_details b, wo_po_break_down c,wo_po_details_master d, product_details_master p
					where
						a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no  and p.id=b.prod_id and a.item_category=4 and b.trans_type in(2,3) and d.company_name=$cbo_company_name $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond  and a.status_active=1 and a.is_deleted=0 and b.entry_form in(24,25)
						
					union all
					
					select 
						a.id as trans_id,
						0 as order_id,
						a.transaction_date,
						0 as receive_qty,
						a.cons_quantity as issue_qty,
						a.cons_uom,
						$null_field as job_no,
						$null_field as job_no_prefix_num,
						$null_field as style_ref_no,
						$null_field as job_year,
						$null_field as buyer_name,
						$null_field as po_number,
						$null_field as shipment_date,
						p.id as prod_id,
						a.transaction_type,
						a.mst_id,
						a.cons_rate,
						a.order_rate,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						a.order_qnty,
						p.item_description,
						p.product_name_details,
						p.color,
						p.item_color,
						p.gmts_size,
						p.item_size,
						p.item_group_id,
						2 as type
						
					from
						inv_transaction a, product_details_master p,  inv_issue_master c
					where
						a.prod_id=p.id and c.id=a.mst_id and a.item_category=4 and c.issue_basis=2 and a.company_id=$cbo_company_name  $date_cond  and a.status_active=1 and a.is_deleted=0 and c.entry_form=25
						
					union all
					
					select 
						a.id as trans_id,
						0 as order_id,
						a.transaction_date,
						0 as receive_qty,
						a.cons_quantity as issue_qty,
						a.cons_uom,
						$null_field as job_no,
						$null_field as job_no_prefix_num,
						$null_field as style_ref_no,
						$null_field as job_year,
						$null_field as buyer_name,
						$null_field as po_number,
						$null_field as shipment_date,
						p.id as prod_id,
						a.transaction_type,
						a.mst_id,
						a.cons_rate,
						a.order_rate,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						a.order_qnty,
						p.item_description,
						p.product_name_details,
						p.color,
						p.item_color,
						p.gmts_size,
						p.item_size,
						p.item_group_id,
						3 as type
					from
						inv_transaction a, product_details_master p
					where
						a.prod_id=p.id and a.item_category=4 and a.company_id=$cbo_company_name  $date_cond  and a.status_active=1 and a.is_deleted=0 and p.entry_form=20	
					order by transaction_date, trans_id";
			}
		}
					
		//echo $sql;	
		$sql_result=sql_select($sql);
		$trms_rasult_arr=array();
		$order_id="";
		foreach($sql_result as $row)
		{
			if($order_id=="") $order_id=$row[csf("order_id")]; else $order_id=$order_id.",".$row[csf("order_id")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["trans_id"]=$row[csf("trans_id")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["order_id"]=$row[csf("order_id")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["prod_id"]=$row[csf("prod_id")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["transaction_type"]=$row[csf("transaction_type")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["rec_issue_id"]=$row[csf("mst_id")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["job_no"]=$row[csf("job_no")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["job_year"]=$row[csf("job_year")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["buyer_name"]=$row[csf("buyer_name")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["po_number"]=$row[csf("po_number")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["shipment_date"]=$row[csf("shipment_date")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["receive_qty"]+=$row[csf("receive_qty")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["issue_qty"]+=$row[csf("issue_qty")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["cons_uom"]=$row[csf("cons_uom")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["cons_rate"]=$row[csf("cons_rate")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["inserted_by"]=$row[csf("inserted_by")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["insert_date"]=$row[csf("insert_date")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["insert_time"]=$row[csf("insert_time")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["order_qnty"]=$row[csf("order_qnty")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["order_rate"]=$row[csf("order_rate")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["item_description"]=$row[csf("item_description")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["product_name_details"]=$row[csf("product_name_details")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["color"]=$row[csf("color")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["item_color"]=$row[csf("item_color")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["gmts_size"]=$row[csf("gmts_size")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["item_size"]=$row[csf("item_size")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["item_group_id"]=$row[csf("item_group_id")];
		}

		if($rptType==1)
		{
			$table_width=2440;
			$div_width="2460px";
		}
		else if($rptType==2)
		{
			$table_width=2360;
			$div_width="2380px";
		}
		else 
		{
			$table_width=2040;
			$div_width="2060px";
		}
			ob_start();	
			?>
			
			<div style="width:<? echo $div_width; ?>"> 
        <fieldset style="width:<? echo $div_width; ?>">
				<table width="<? echo $table_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" align="left"> 
						<tr class="form_caption" style="border:none;">
							<td colspan="17" align="center" style="border:none;font-size:16px; font-weight:bold" >Date Wise Item Receive Issue Report </td> 
						</tr>
						<tr style="border:none;">
								<td colspan="17" align="center" style="border:none; font-size:14px;">
									Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>                                
								</td>
						</tr>
				   </table>
				   <br />
				<table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left"> 
					<thead>
						<tr>
							<th width="30" >SL</th>
							<th width="50" >Prod. Id</th>
							<th width="80" >Trans. Date</th>
							<th width="120" >Trans. Ref.</th>
							<th width="60" >Year</th>
							<th width="50" >Job No</th>
                            <th width="100">Style Ref. No</th>
							<th width="90">Buyer</th>
							<th width="120">Order No</th>
                            <th width="100">Challan No</th>
                            <th width="100">Supplier</th>
							<th width="80">Ship Date</th>
							<th width="100">Group Name</th>
							<th width="220">Description</th>
							<th width="80">RMG Color</th>
							<th width="60">RMG Size</th>
                            <?
							if($rptType==2 || $rptType==1 )
							{
								?>
                                <th width="80">Currency</th>
                                <th width="80">Exchange Rate</th>
                                <th width="80">Actual Rate</th>
								<th width="80">Receive Qty</th>
                                <th width="80">Actual Amt</th>
                                <?
							}
							if($rptType==3 || $rptType==1 )
							{
								?>
								<th width="80">Issue Qty</th>
                                <?
							}
							?>
                            <th width="80">Rate(TK)</th>
                            <th width="100">Amount(TK)</th>
							<th width="48">UOM</th>
                            <th width="110">User</th>
                            <th width="160">Insert Time</th>
						</tr>
					</thead>
			   </table> 
			  <div style="width:<? echo $div_width; ?>; overflow-y: scroll; max-height:240px;" id="scroll_body">
				<table width="<? echo $table_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
					<tbody>
					<?
					$sql_item_group=sql_select("select id, item_name,conversion_factor from  lib_item_group where status_active=1 and is_deleted =0");
					foreach($sql_item_group as $row)
					{
						$group_arr[$row[csf("id")]]["item_name"]=$row[csf("item_name")];
						$group_arr[$row[csf("id")]]["conversion_factor"]=$row[csf("conversion_factor")];
					}
					$size_arr=return_library_array( "select id, size_name from  lib_size",'id','size_name');
					
					$i=1;
					foreach($trms_rasult_arr as $trans_key=>$value)
					{
						foreach($value as $order_key=>$val)
						{
							if ($i%2==0)
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";

							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><p><? echo $i; ?>&nbsp;</p></td>
								<td width="50"><p><? echo $val["prod_id"]; ?>&nbsp;</p></td>
								<td width="80" align="center"><p><? if($val["transaction_date"]!="0000-00-00") echo change_date_format($val["transaction_date"]); else echo ""; ?>&nbsp;</p></td>
								<td width="120"  align="center"><p>
                                <?
									if($val["transaction_type"]==1 || $val["transaction_type"]==4) 
									{
										echo $receive_num_arr[$val['rec_issue_id']]["recv_number"];
									}
									else if($val["transaction_type"]==2 || $val["transaction_type"]==3)
									{
										echo $issue_num_arr[$val['rec_issue_id']]["issue_number"];
									}
	
								?>&nbsp;
								</p></td>
								<td width="60"  align="center"><p><? echo $val["job_year"]; ?>&nbsp;</p></td>
								<td width="50"  align="center"><p><? echo $val["job_no_prefix_num"]; ?>&nbsp;</p></td>
                                <td width="100"><p><? echo $val["style_ref_no"]; ?>&nbsp;</p></td>
								<td width="90"><p><? echo $buyer_short_arr[$val["buyer_name"]]; ?>&nbsp;</p></td>
								<td width="120" align="center"><p><? echo $val["po_number"]; ?>&nbsp;</p></td>
                                <td width="100"><p>
								<?
									if($val["transaction_type"]==1 || $val["transaction_type"]==4) 
									{
										echo $receive_num_arr[$val['rec_issue_id']]["challan_no"];
									}
									else if($val["transaction_type"]==2 || $val["transaction_type"]==3)
									{
										echo $issue_num_arr[$val['rec_issue_id']]["challan_no"];
									}
	
								?>
                                &nbsp;</p></td>
                                <td width="100"><p>
								<?
									if($val["transaction_type"]==1 || $val["transaction_type"]==4) 
									{
										echo $supplier_arr[$receive_num_arr[$val['rec_issue_id']]["supplier_id"]];
									}
									else if($val["transaction_type"]==2 || $val["transaction_type"]==3)
									{
										echo "&nbsp;";
									}
	
								?>
                                &nbsp;</p></td>
								<td width="80"  align="center"><p><? if($val["shipment_date"]!="0000-00-00") echo change_date_format($val["shipment_date"]); else echo ""; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $group_arr[$val["item_group_id"]]["item_name"]; ?>&nbsp;</p></td>
								<td width="220"><p><? echo $val["product_name_details"]; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $color_arr[$val["color"]]; ?>&nbsp;</p></td>
								<td width="60"><p><? echo $size_arr[$val["gmts_size"]]; ?>&nbsp;</p></td>
                                <?
								if($rptType==2 || $rptType==1 )
								{
									?>
                                    <td width="80" align="center"><p><? if($val["transaction_type"]==1) echo $currency[$receive_num_arr[$val['rec_issue_id']]["currency_id"]]; ?></p></td>
                                    <td width="80" align="right"><p><? if($val["transaction_type"]==1) echo number_format($receive_num_arr[$val['rec_issue_id']]["exchange_rate"],2); ?></p></td>
                                    <td width="80" align="right"><p><? if($val["transaction_type"]==1) echo number_format($val["order_rate"],2); ?></p></td>
                                    
									<td width="80" align="right"><p>
									<?
									if($val["transaction_type"]==1 || $val["transaction_type"]==4) 
									{
										$rcv_issue_qnty=$group_arr[$val["item_group_id"]]["conversion_factor"] *$val["receive_qty"];
										echo number_format($rcv_issue_qnty,2,".",""); $total_receive_qty+=$rcv_issue_qnty; 
									}
									?>
                                    </p></td>
                                    <td width="80" align="right"><p><? if($val["transaction_type"]==1) $order_amt=$val["order_qnty"]*$val["order_rate"]; echo number_format($order_amt,4); $total_order_amt+=$order_amt; $order_amt=0; ?></p></td>
                                    <?
								}
                                if($rptType==3 || $rptType==1 )
                                {
                                    ?>
									<td width="80" align="right"><p>
									<?
									 if($val["transaction_type"]==2 || $val["transaction_type"]==3)
									 {
										 $rcv_issue_qnty=$group_arr[$val["item_group_id"]]["conversion_factor"] *$val["issue_qty"];
										 echo number_format($rcv_issue_qnty,2,".",""); $total_issue_qty+=$rcv_issue_qnty; 
									 }
									 ?>
                                     </p></td>
                                    <?
								}
								?>
                                <td width="80" align="right"><p><? echo number_format($val["cons_rate"],4,".",""); ?></p></td>
                                <td width="100" align="right"><p>
								<?
								$amount=$rcv_issue_qnty*$val["cons_rate"];
								 echo number_format($amount,2,".",""); $total_amount+=$amount; 
								?>
                                 </p></td>
								<td width="48"><p><? echo $unit_of_measurement[$val["cons_uom"]]; ?>&nbsp;</p></td>
                                <td width="110"><p><? echo $user_name_arr[$val["inserted_by"]]; ?>&nbsp;</p></td>
                                <td width="160"><p><? echo change_date_format($val["insert_date"])." ".$val["insert_time"]; ?>&nbsp;</p></td>
							</tr>
							<?
							$i++;
						}
					}
					?>   
					</tbody>
				</table>
				<!--<script language="javascript"> setFilterGrid('table_body',-1)</script> --> 
             <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left"> 
					<tfoot>
						<tr>
                        	<th width="30">&nbsp;</th>
                            <th width="50">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="60">&nbsp;</th>
                            <th width="50">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="90">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="220">&nbsp;</th>
                            <th width="80">&nbsp;</th>
							<th width="60"><? if($rptType==3) echo "Total:"; ?> </th>
                            <?
							if($rptType==2 || $rptType==1 )
							{
								?>
                                <th width="80" >&nbsp;</th>
                                <th width="80" >&nbsp;</th>
                                <th width="80" >Total:</th>
                                <th width="80" id="value_total_receive_qty"><? echo number_format($total_receive_qty,2); ?></th>
                                <th width="80" id="value_total_order_amt"><? echo number_format($total_order_amt,2); ?></th>
								
                                <?
							}
							if($rptType==3 || $rptType==1 )
							{
								?>
								<th width="80" id="value_total_issue_qty"><? echo number_format($total_issue_qty,2); ?></th>
                                <?
							}
							?>
                            <th width="80"></th>
                            <th width="100" id="value_total_amount"><? echo number_format($total_amount,2); ?></th>
                            <th width="48">&nbsp;</th>
                            <th width="110">&nbsp;</th>
                            <th width="160">&nbsp;</th>
						</tr>
					</tfoot>
			   </table> 
			 </div>
        </fieldset>
		</div>    
			<?
	}
	
	
	else if($cbo_item_cat==2)
	{
		
		if($txt_style_ref!="")
		{
			if($txt_style_ref_id!="")
			{
				$txt_style_ref_id="and d.id in($txt_style_ref_id)";
			}
			else
			{
				$txt_style_ref_id="and d.job_no_prefix_num ='$txt_style_ref'"; 
	
			}
		}
		else
		{
			 $txt_style_ref_id="";
		}
		if($txt_order!="") 
		{
			if($txt_order_id!="")
			{
				$txt_order_id="and c.id in($txt_order_id)";
			}
			else
			{
				$txt_order_id="and c.po_number='$txt_order'";
			}
		}
		else
		{
			$txt_order_id="";
		}
	
		if($cbo_buyer_name!=0) $cbo_buyer_name="and d.buyer_name=$cbo_buyer_name"; else $cbo_buyer_name="";
		
		//echo $date_cond;die;
		if($txt_style_ref!="" || $txt_order!="" || $cbo_buyer_name!=0)
		{
			if($rptType==1)
			{
				$sql="select 
						a.id as trans_id,
						a.transaction_type,
						b.po_breakdown_id as order_id,
						a.transaction_date,
						case when b.trans_type in(1,4,5) then b.quantity else 0 end as receive_qty,
						case when b.trans_type in(2,3,6) then b.quantity else 0 end as issue_qty,
						a.cons_uom,
						d.job_no,
						d.job_no_prefix_num,
						$select_year(d.insert_date $year_con) as job_year,
						d.buyer_name,
						c.po_number,
						c.shipment_date,
						b.prod_id,
						a.mst_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width
					from
						inv_transaction a, order_wise_pro_details b, wo_po_break_down c,wo_po_details_master d, product_details_master e
					where
						a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and e.id=b.prod_id and a.item_category=2 and d.company_name=$cbo_company_name $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond and a.transaction_type in(1,2,3,4,5,6)  and a.status_active=1 and a.is_deleted=0 and b.entry_form in (7,15,37,18,46)  order by a.transaction_date, a.id";
			}
			else if($rptType==2)
			{
				$sql="select 
						a.id as trans_id,
						a.transaction_type,
						b.po_breakdown_id as order_id,
						a.transaction_date,
						b.quantity as receive_qty,
						'' as issue_qty,
						a.cons_uom,
						d.job_no,
						d.job_no_prefix_num,
						$select_year(d.insert_date $year_con) as job_year,
						d.buyer_name,
						c.po_number,
						c.shipment_date,
						b.prod_id,
						a.mst_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width
					from
						inv_transaction a, order_wise_pro_details b, wo_po_break_down c,wo_po_details_master d, product_details_master e
					where
						a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and e.id=b.prod_id and a.item_category=2 and b.trans_type in(1,4,5) and d.company_name=$cbo_company_name $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond  and a.status_active=1 and a.is_deleted=0 and b.entry_form in (7,37,15)  order by a.transaction_date, a.id";
			}
			else if($rptType==3)
			{
				$sql="select 
						a.id as trans_id,
						a.transaction_type,
						b.po_breakdown_id as order_id,
						a.transaction_date,
						'' as receive_qty,
						b.quantity as issue_qty,
						a.cons_uom,
						d.job_no,
						d.job_no_prefix_num,
						$select_year(d.insert_date $year_con) as job_year,
						d.buyer_name,
						c.po_number,
						c.shipment_date,
						b.prod_id,
						a.mst_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width
					from
						inv_transaction a, order_wise_pro_details b, wo_po_break_down c,wo_po_details_master d, product_details_master e
					where
						a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and e.id=b.prod_id and a.item_category=2 and b.trans_type in(2,3,6) and d.company_name=$cbo_company_name $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond and a.transaction_type in(2,3,6)  and a.status_active=1 and a.is_deleted=0 and b.entry_form in (15,18,46)  order by a.transaction_date, a.id";
			}
		}
		else
		{
			if($rptType==1)
			{
				$sql="select 
						a.id as trans_id,
						a.transaction_type,
						b.po_breakdown_id as order_id,
						a.transaction_date,
						b.quantity as receive_qty,
						0 as issue_qty,
						a.cons_uom,
						d.job_no,
						d.job_no_prefix_num,
						$select_year(d.insert_date $year_con) as job_year,
						d.buyer_name,
						c.po_number,
						c.shipment_date,
						b.prod_id,
						a.mst_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width,
						1 as type
					from
						inv_receive_master p, inv_transaction a, order_wise_pro_details b, wo_po_break_down c,wo_po_details_master d, product_details_master e
					where
						p.id=a.mst_id and a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and e.id=b.prod_id and p.booking_without_order!=1 and a.item_category=2 and d.company_name=$cbo_company_name $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond and a.transaction_type in(1,4)  and a.status_active=1 and a.is_deleted=0 and b.entry_form in (7,37)
						
					union all
					
					select 
						a.id as trans_id,
						a.transaction_type,
						0 as order_id,
						a.transaction_date,
						a.cons_quantity as receive_qty,
						0 as issue_qty,
						a.cons_uom,
						$null_field as job_no,
						$null_field as job_no_prefix_num,
						$null_field as  job_year,
						$null_field as buyer_name,
						$null_field as po_number,
						$null_field as shipment_date,
						a.prod_id,
						a.mst_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width,
						2 as type
					from
						inv_receive_master p, inv_transaction a, product_details_master e
					where
						p.id=a.mst_id  and e.id=a.prod_id  and a.item_category=2 and p.booking_without_order=1 and p.company_id=$cbo_company_name $date_cond and a.transaction_type in(1,4)  and a.status_active=1 and a.is_deleted=0 and p.entry_form in (7,37)
					
					union all
					
					select 
						a.id as trans_id,
						a.transaction_type,
						b.po_breakdown_id as order_id,
						a.transaction_date,
						0 as receive_qty,
						b.quantity as issue_qty,
						a.cons_uom,
						d.job_no,
						d.job_no_prefix_num,
						$select_year(d.insert_date $year_con) as job_year,
						d.buyer_name,
						c.po_number,
						c.shipment_date,
						b.prod_id,
						a.mst_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width,
						3 as type
					from
						 inv_issue_master p, inv_transaction a, order_wise_pro_details b, wo_po_break_down c,wo_po_details_master d, product_details_master e
					where
						p.id=a.mst_id and a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and e.id=b.prod_id and a.item_category=2 and d.company_name=$cbo_company_name $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond and a.transaction_type in(2,3)  and a.status_active=1 and a.is_deleted=0 and b.entry_form in (18,46)
						
					union all
					
					select 
						a.id as trans_id,
						a.transaction_type,
						0 as order_id,
						a.transaction_date,
						0 as receive_qty,
						a.cons_quantity as issue_qty,
						a.cons_uom,
						$null_field as job_no,
						$null_field as job_no_prefix_num,
						$null_field as job_year,
						$null_field as buyer_name,
						$null_field as po_number,
						$null_field as shipment_date,
						$null_field as prod_id,
						a.mst_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width,
						4 as type
					from
						 inv_issue_master p, inv_transaction a, product_details_master e
					where
						p.id=a.mst_id and e.id=a.prod_id and a.item_category=2 and p.issue_purpose=8 and p.company_id=$cbo_company_name $date_cond and a.transaction_type in(2,3)  and a.status_active=1 and a.is_deleted=0 and p.entry_form in (18,46)
					
					union all
					
					select 
						a.id as trans_id,
						a.transaction_type,
						b.po_breakdown_id as order_id,
						a.transaction_date,
						(case when b.trans_type=5 then b.quantity else 0 end)  as receive_qty,
						(case when b.trans_type=6 then b.quantity else 0 end) as issue_qty,
						a.cons_uom,
						d.job_no,
						d.job_no_prefix_num,
						$select_year(d.insert_date $year_con) as job_year,
						d.buyer_name,
						c.po_number,
						c.shipment_date,
						b.prod_id,
						a.mst_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width,
						5 as type
					from
						  inv_item_transfer_mst p, inv_transaction a, order_wise_pro_details b, wo_po_break_down c,wo_po_details_master d, product_details_master e
					where
						p.id=a.mst_id and a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and e.id=b.prod_id and a.item_category=2 and d.company_name=$cbo_company_name $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond and a.transaction_type in(5,6)  and a.status_active=1 and a.is_deleted=0 and b.entry_form in (15)
					order by transaction_date, trans_id";
			}
			//18,46
			else if($rptType==2)
			{
				$sql="select 
						a.id as trans_id,
						a.transaction_type,
						b.po_breakdown_id as order_id,
						a.transaction_date,
						b.quantity as receive_qty,
						0 as issue_qty,
						a.cons_uom,
						d.job_no,
						d.job_no_prefix_num,
						$select_year(d.insert_date $year_con) as job_year,
						d.buyer_name,
						c.po_number,
						c.shipment_date,
						b.prod_id,
						a.mst_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width,
						1 as type
					from
						inv_receive_master p, inv_transaction a, order_wise_pro_details b, wo_po_break_down c,wo_po_details_master d, product_details_master e
					where
						p.id=a.mst_id and a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and e.id=b.prod_id and p.booking_without_order!=1 and a.item_category=2 and d.company_name=$cbo_company_name $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond and a.transaction_type in(1,4)  and a.status_active=1 and a.is_deleted=0 and b.entry_form in (7,37)
						
					union all
					
					select 
						a.id as trans_id,
						a.transaction_type,
						0 as order_id,
						a.transaction_date,
						a.cons_quantity as receive_qty,
						0 as issue_qty,
						a.cons_uom,
						$null_field as job_no,
						$null_field as job_no_prefix_num,
						$null_field as  job_year,
						$null_field as buyer_name,
						$null_field as po_number,
						$null_field as shipment_date,
						a.prod_id,
						a.mst_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width,
						2 as type
					from
						inv_receive_master p, inv_transaction a, product_details_master e
					where
						p.id=a.mst_id  and e.id=a.prod_id  and a.item_category=2 and p.booking_without_order=1 and p.company_id=$cbo_company_name $date_cond and a.transaction_type in(1,4)  and a.status_active=1 and a.is_deleted=0 and p.entry_form in (7,37)
						
					union all
					
					select 
						a.id as trans_id,
						a.transaction_type,
						b.po_breakdown_id as order_id,
						a.transaction_date,
						(case when b.trans_type=5 then b.quantity else 0 end)  as receive_qty,
						0 as issue_qty,
						a.cons_uom,
						d.job_no,
						d.job_no_prefix_num,
						$select_year(d.insert_date $year_con) as job_year,
						d.buyer_name,
						c.po_number,
						c.shipment_date,
						b.prod_id,
						a.mst_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width,
						5 as type
					from
						  inv_item_transfer_mst p, inv_transaction a, order_wise_pro_details b, wo_po_break_down c,wo_po_details_master d, product_details_master e
					where
						p.id=a.mst_id and a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and e.id=b.prod_id and a.item_category=2 and d.company_name=$cbo_company_name $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond and a.transaction_type in(5)  and a.status_active=1 and a.is_deleted=0 and b.entry_form in (15)
					order by transaction_date, trans_id";
			}
			else if($rptType==3)
			{
				$sql="select 
						a.id as trans_id,
						a.transaction_type,
						b.po_breakdown_id as order_id,
						a.transaction_date,
						0 as receive_qty,
						b.quantity as issue_qty,
						a.cons_uom,
						d.job_no,
						d.job_no_prefix_num,
						$select_year(d.insert_date $year_con) as job_year,
						d.buyer_name,
						c.po_number,
						c.shipment_date,
						b.prod_id,
						a.mst_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width,
						3 as type
					from
						 inv_issue_master p, inv_transaction a, order_wise_pro_details b, wo_po_break_down c,wo_po_details_master d, product_details_master e
					where
						p.id=a.mst_id and a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and e.id=b.prod_id and a.item_category=2 and d.company_name=$cbo_company_name $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond and a.transaction_type in(2,3)  and a.status_active=1 and a.is_deleted=0 and b.entry_form in (18,46)
						
					union all
					
					select 
						a.id as trans_id,
						a.transaction_type,
						0 as order_id,
						a.transaction_date,
						0 as receive_qty,
						a.cons_quantity as issue_qty,
						a.cons_uom,
						$null_field as job_no,
						$null_field as job_no_prefix_num,
						$null_field as job_year,
						$null_field as buyer_name,
						$null_field as po_number,
						$null_field as shipment_date,
						$null_field as prod_id,
						a.mst_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width,
						4 as type
					from
						 inv_issue_master p, inv_transaction a, product_details_master e
					where
						p.id=a.mst_id and e.id=a.prod_id and a.item_category=2 and p.issue_purpose=8 and p.company_id=$cbo_company_name $date_cond and a.transaction_type in(2,3)  and a.status_active=1 and a.is_deleted=0 and p.entry_form in (18,46)
					
					union all
					
					select 
						a.id as trans_id,
						a.transaction_type,
						b.po_breakdown_id as order_id,
						a.transaction_date,
						0  as receive_qty,
						(case when b.trans_type=6 then b.quantity else 0 end) as issue_qty,
						a.cons_uom,
						d.job_no,
						d.job_no_prefix_num,
						$select_year(d.insert_date $year_con) as job_year,
						d.buyer_name,
						c.po_number,
						c.shipment_date,
						b.prod_id,
						a.mst_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width,
						5 as type
					from
						  inv_item_transfer_mst p, inv_transaction a, order_wise_pro_details b, wo_po_break_down c,wo_po_details_master d, product_details_master e
					where
						p.id=a.mst_id and a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and e.id=b.prod_id and a.item_category=2 and d.company_name=$cbo_company_name $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond and a.transaction_type in(6)  and a.status_active=1 and a.is_deleted=0 and b.entry_form in (15)
					order by transaction_date, trans_id";
			}
		}
					
		//echo $sql;die;
		$sql_result=sql_select($sql);
		$trms_rasult_arr=array();
		$order_id="";$trans_rec_id="";$trans_issue_id="";
		foreach($sql_result as $row)
		{
			if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5)
			{ 
				if($trans_rec_id=="") $trans_rec_id=$row[csf("trans_id")]; else   $trans_rec_id=$trans_rec_id.",".$row[csf("trans_id")];
			}
			if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6)
			{ 
				if($trans_issue_id=="") $trans_issue_id=$row[csf("trans_id")]; else   $trans_issue_id=$trans_issue_id.",".$row[csf("trans_id")];
			}
			if($order_id=="") $order_id=$row[csf("order_id")]; else $order_id=$order_id.",".$row[csf("order_id")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["trans_id"]=$row[csf("trans_id")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["order_id"]=$row[csf("order_id")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["prod_id"]=$row[csf("prod_id")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["transaction_type"]=$row[csf("transaction_type")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["rec_issue_id"]=$row[csf("mst_id")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["job_no"]=$row[csf("job_no")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["job_year"]=$row[csf("job_year")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["buyer_name"]=$row[csf("buyer_name")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["po_number"]=$row[csf("po_number")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["shipment_date"]=$row[csf("shipment_date")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["receive_qty"]+=$row[csf("receive_qty")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["issue_qty"]+=$row[csf("issue_qty")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["cons_uom"]=$row[csf("cons_uom")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["inserted_by"]=$row[csf("inserted_by")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["insert_date"]=$row[csf("insert_date")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["insert_time"]=$row[csf("insert_time")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["product_name_details"]=$row[csf("product_name_details")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["color"]=$row[csf("color")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["item_color"]=$row[csf("item_color")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["detarmination_id"]=$row[csf("detarmination_id")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["gsm"]=$row[csf("gsm")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["dia_width"]=$row[csf("dia_width")];
		}
		
		
		//var_dump($order_id);die;
		/*if(empty($order_id))  $order_id=0;
		$sql_prod="select
							a.id as product_id,
							b.trans_id,
							b.po_breakdown_id,
							a.product_name_details,
							a.color,
							a.item_color,
							a.detarmination_id,
							a.gsm,
							a.dia_width
							
					from
							 product_details_master a, order_wise_pro_details b
					where
							a.id=b.prod_id and b.entry_form in (7,37,18) order by b.trans_id";
		
		//echo $sql_prod;die;
		$product_result_arr=array();
		$result=sql_select($sql_prod);
		foreach($result as $row)
		{
			$product_result_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]["product_id"]=$row[csf("product_id")];
			$product_result_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]["product_name_details"]=$row[csf("product_name_details")];
			$product_result_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]["color"]=$row[csf("color")];
			$product_result_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]["detarmination_id"]=$row[csf("detarmination_id")];
			$product_result_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]["gsm"]=$row[csf("gsm")];
			$product_result_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]["width"]=$row[csf("dia_width")];
		}*/
		
		//echo 'jahid'.'________'.$trans_rec_id."_________".$trans_issue_id;die;
		
		if($trans_rec_id!="")
		{
			$sql_batch_rec=sql_select("select a.trans_id,a.po_breakdown_id,b.batch_id from order_wise_pro_details a,pro_finish_fabric_rcv_dtls b  where a.trans_id=b.trans_id and  a.trans_id in($trans_rec_id)");
			foreach($sql_batch_rec as $row)
			{
				$batch_rec_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]=$row[csf("batch_id")];
			}
		}
		if($trans_issue_id!="")
		{
			$sql_batch_issue=sql_select("select a.trans_id,a.po_breakdown_id,b.batch_id from order_wise_pro_details a,inv_finish_fabric_issue_dtls b  where a.trans_id=b.trans_id and a.trans_id in($trans_issue_id)");
			foreach($sql_batch_issue as $row)
			{
				$batch_issue_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]=$row[csf("batch_id")];
			}
		}
		
		
		
		//var_dump($product_result_arr);die;
		
		
			ob_start();	
			?>
			
			<div style="width:1720px"> 
        <fieldset style="width:1720px;">
				<table width="1700" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" align="left"> 
						<tr class="form_caption" style="border:none;">
							<td colspan="18" align="center" style="border:none;font-size:16px; font-weight:bold" >Date Wise Item Receive Issue Report </td> 
						</tr>
						<tr style="border:none;">
								<td colspan="18" align="center" style="border:none; font-size:14px;">
									Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>                                
								</td>
						</tr>
				   </table>
				   <br />
				<table width="1700" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left"> 
					<thead>
						<tr>
							<th width="30" >SL</th>
							<th width="50" >Prod. Id</th>
							<th width="60" >Year</th>
							<th width="50" >Job No</th>
							<th width="70">Buyer</th>
							<th width="120">Order No</th>
							<th width="80">Ship Date</th>
							<th width="80" >Trans. Date</th>
							<th width="120" >Trans. Ref.</th>
                            <th width="100" >Challan No</th>
                            <th width="110">Construction</th>
							<th width="110">Composition</th>
							<th width="80">Color</th>
							<th width="50">GSM</th>
                            <th width="50">Dia</th>
							<th width="80">Receive Qty</th>
							<th width="80">Issue Qty</th>
							<th width="100">Batch No</th>
                            <th width="110">User</th>
                            <th width="160">Insert Date</th>
						</tr>
					</thead>
			   </table> 
			  <div style="width:1718px; overflow-y: scroll; max-height:250px;" id="scroll_body">
				<table width="1700" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
					<tbody>
					<?
					$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
					$i=1;$total_receive="";$total_issue="";
					foreach($trms_rasult_arr as $trans_key=>$value)
					{
						foreach($value as $order_key=>$val)
						{
							if ($i%2==0)
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";

							?>
							<tr  bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><p><? echo $i; ?>&nbsp;</p></td>
								<td width="50"><p><? echo $val["prod_id"]; ?>&nbsp;</p></td>
								<td width="60"  align="center"><p><? echo $val["job_year"]; ?>&nbsp;</p></td>
								<td width="50"  align="center"><p><? echo $val["job_no_prefix_num"]; ?>&nbsp;</p></td>
								<td width="70"><p><? echo $buyer_short_arr[$val["buyer_name"]]; ?>&nbsp;</p></td>
								<td width="120" align="center"><p><? echo $val["po_number"]; ?>&nbsp;</p></td>
								<td width="80"  align="center"><p><? if($val["shipment_date"]!="0000-00-00") echo change_date_format($val["shipment_date"]); else echo ""; ?>&nbsp;</p></td>
								<td width="80" align="center"><p><? if($val["transaction_date"]!="0000-00-00") echo change_date_format($val["transaction_date"]); else echo ""; ?>&nbsp;</p></td>
								<td width="120"  align="center"><p>
								<?
									if($val["transaction_type"]==1  || $val["transaction_type"]==4) 
									{
										echo $receive_num_arr[$val['rec_issue_id']]["recv_number"];
									}
									else if($val["transaction_type"]==2  || $val["transaction_type"]==3)
									{
										echo $issue_num_arr[$val['rec_issue_id']]["issue_number"];
									}
	
								?>&nbsp;
								</p></td>
                                <td width="100"><p>
								<? 
									if($val["transaction_type"]==1  || $val["transaction_type"]==4) 
									{
										echo $receive_num_arr[$val['rec_issue_id']]["challan_no"];
									}
									else if($val["transaction_type"]==2  || $val["transaction_type"]==3)
									{
										echo $issue_num_arr[$val['rec_issue_id']]["challan_no"];
									}
								?>
                                &nbsp;</p></td>
								<td width="110"><p><? echo $construction_arr[$val["detarmination_id"]]; ?>&nbsp;</p></td>
								<td width="110"><p><? echo $composition_arr[$val["detarmination_id"]]; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $color_arr[$val["color"]]; ?>&nbsp;</p></td>
								<td width="50"><p><? echo $val["gsm"]; ?>&nbsp;</p></td>
                                <td width="50"><p><? echo $val["width"]; ?>&nbsp;</p></td>
								<td width="80" align="right"><p><? echo number_format($val["receive_qty"],2); $total_receive +=$val["receive_qty"]; ?></p></td>
								<td width="80" align="right"><p><?  echo number_format($val["issue_qty"],2); $total_issue +=$val["issue_qty"]; ?></p></td>
								<td width="100"><p>
								<? 
									if($val["transaction_type"]==1 || $val["transaction_type"]==4) echo $batch_arr[$batch_rec_arr[$trans_key][$order_key]];
									if($val["transaction_type"]==2 || $val["transaction_type"]==3) echo $batch_arr[$batch_issue_arr[$trans_key][$order_key]];
								?>
                                &nbsp;</p></td>
                                <td width="110"><p><? echo $user_name_arr[$val["inserted_by"]]; ?>&nbsp;</p></td>
                                <td width="160"><p><? echo change_date_format($val["insert_date"])." ".$val["insert_time"]; ?>&nbsp;</p></td>
							</tr>
							<?
							$i++;
						}
					}
					?>   
					</tbody>
				</table>
				<table width="1700" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
                	<tfoot>
                    	<th width="30">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                    	<th width="50">Total:</th>
                        <th width="80" id="value_total_receive"><? echo number_format($total_receive,2); ?></th>
                        <th width="80" id="value_total_issue"><? echo number_format($total_issue,2); ?></th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="160">&nbsp;</th>
                    </tfoot> 
                </table>
			 </div>
        </fieldset>
		</div>    
			<?	 
	
	
	}
	
	else if($cbo_item_cat==13)
	{
		if($txt_style_ref!="")
		{
			if($txt_style_ref_id!="")
			{
				$txt_style_ref_id="and d.id in($txt_style_ref_id)";
			}
			else
			{
				$txt_style_ref_id="and d.job_no_prefix_num ='$txt_style_ref'"; 
	
			}
		}
		else
		{
			 $txt_style_ref_id="";
		}
		if($txt_order!="") 
		{
			if($txt_order_id!="")
			{
				$txt_order_id="and c.id in($txt_order_id)";
			}
			else
			{
				$txt_order_id="and c.po_number='$txt_order'";
			}
		}
		else
		{
			$txt_order_id="";
		}
	
		if($cbo_buyer_name!=0) $cbo_buyer_name="and d.buyer_name=$cbo_buyer_name"; else $cbo_buyer_name="";
		
		
		//echo $date_cond;die;
		if($txt_style_ref!="" || $txt_order!="" || $cbo_buyer_name!=0)
		{
			if($rptType==1)
			{
				$sql="select 
						a.id as trans_id,
						a.transaction_type,
						a.mst_id,
						b.po_breakdown_id as order_id,
						a.transaction_date,
						b.quantity as receive_qty,
						0  as issue_qty,
						a.cons_uom,
						d.job_no,
						d.job_no_prefix_num,
						$select_year(d.insert_date $year_con) as job_year,
						d.buyer_name,
						c.po_number,
						c.shipment_date,
						b.prod_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width,
						f.id as dtls_id,
						f.color_id as dtls_color,
						f.stitch_length as stitch_length,
						f.yarn_lot,
						f.yarn_count
					from
						inv_transaction a, order_wise_pro_details b, wo_po_break_down c,wo_po_details_master d, product_details_master e, pro_grey_prod_entry_dtls f
					where
						a.id=b.trans_id and e.id=a.prod_id and f.trans_id=a.id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.item_category=13 and d.company_name=$cbo_company_name $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond and a.transaction_type in(1,4,5) and b.entry_form in (2,13,22) and a.status_active=1 and a.is_deleted=0
					
					union all
					
					select 
						a.id as trans_id,
						a.transaction_type,
						a.mst_id,
						b.po_breakdown_id as order_id,
						a.transaction_date,
						0 as receive_qty,
						b.quantity as issue_qty,
						a.cons_uom,
						d.job_no,
						d.job_no_prefix_num,
						$select_year(d.insert_date $year_con) as job_year,
						d.buyer_name,
						c.po_number,
						c.shipment_date,
						b.prod_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width,
						f.id as dtls_id,
						f.color_id as dtls_color,
						f.stitch_length as stitch_length,
						f.yarn_lot,
						f.yarn_count
					from
						inv_transaction a, order_wise_pro_details b, wo_po_break_down c,wo_po_details_master d, product_details_master e, inv_grey_fabric_issue_dtls f
					where
						a.id=b.trans_id and e.id=a.prod_id and f.trans_id=a.id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.item_category=13 and d.company_name=$cbo_company_name $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond and a.transaction_type in(2,3,6) and b.entry_form in (13,16,45) and a.status_active=1 and a.is_deleted=0
					  
					order by transaction_date, trans_id";
			}
			else if($rptType==2)
			{
				$sql="select 
						a.id as trans_id,
						a.transaction_type,
						a.mst_id,
						b.po_breakdown_id as order_id,
						a.transaction_date,
						b.quantity as receive_qty,
						'' as issue_qty,
						a.cons_uom,
						d.job_no,
						d.job_no_prefix_num,
						$select_year(d.insert_date $year_con) as job_year,
						d.buyer_name,
						c.po_number,
						c.shipment_date,
						b.prod_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width,
						f.id as dtls_id,
						f.color_id as dtls_color,
						f.stitch_length as stitch_length,
						f.yarn_lot,
						f.yarn_count
					from
						inv_transaction a, order_wise_pro_details b, wo_po_break_down c,wo_po_details_master d, product_details_master e, pro_grey_prod_entry_dtls f
					where
						a.id=b.trans_id and e.id=a.prod_id and f.trans_id=a.id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.item_category=13 and  b.trans_type in(1,4,5) and d.company_name=$cbo_company_name $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond and b.entry_form in (2,13,22) and a.status_active=1 and a.is_deleted=0  order by a.transaction_date, a.id";
			}
			else if($rptType==3)
			{
				$sql="select 
						a.id as trans_id,
						a.transaction_type,
						a.mst_id,
						b.po_breakdown_id as order_id,
						a.transaction_date,
						'' as receive_qty,
						b.quantity as issue_qty,
						a.cons_uom,
						d.job_no,
						d.job_no_prefix_num,
						$select_year(d.insert_date $year_con) as job_year,
						d.buyer_name,
						c.po_number,
						c.shipment_date,
						b.prod_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width,
						f.id as dtls_id,
						f.color_id as dtls_color,
						f.stitch_length as stitch_length,
						f.yarn_lot,
						f.yarn_count
					from
						inv_transaction a, order_wise_pro_details b, wo_po_break_down c,wo_po_details_master d, product_details_master e, inv_grey_fabric_issue_dtls f
					where
						a.id=b.trans_id and e.id=a.prod_id and f.trans_id=a.id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.item_category=13 and b.trans_type in(2,3,6) and d.company_name=$cbo_company_name $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond and b.entry_form in (13,16,45) and a.status_active=1 and a.is_deleted=0  order by a.transaction_date, a.id";
			}
		}
		else
		{
			if($rptType==1)
			{
				$sql="select 
						a.id as trans_id,
						a.transaction_type,
						a.mst_id,
						b.po_breakdown_id as order_id,
						a.transaction_date,
						b.quantity as receive_qty,
						0 as issue_qty,
						a.cons_uom,
						d.job_no,
						d.job_no_prefix_num,
						$select_year(d.insert_date $year_con) as job_year,
						d.buyer_name,
						c.po_number,
						c.shipment_date,
						b.prod_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width,
						f.id as dtls_id,
						f.color_id as dtls_color,
						f.stitch_length as stitch_length,
						f.yarn_lot,
						f.yarn_count,
						1 as type
					from
						inv_receive_master p, inv_transaction a, order_wise_pro_details b, wo_po_break_down c,wo_po_details_master d, product_details_master e, pro_grey_prod_entry_dtls f
					where
						p.id=a.mst_id and p.id=f.mst_id and a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and e.id=a.prod_id and a.item_category=13 and d.company_name=$cbo_company_name $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond and a.transaction_type in(1,4) and b.entry_form in (2,22) and a.status_active=1 and a.is_deleted=0
					union all
					
					select 
						a.id as trans_id,
						a.transaction_type,
						a.mst_id,
						0 as order_id,
						a.transaction_date,
						a.cons_quantity as receive_qty,
						0 as issue_qty,
						a.cons_uom,
						$null_field as job_no,
						$null_field as job_no_prefix_num,
						$null_field as job_year,
						$null_field as buyer_name,
						$null_field as po_number,
						$null_field as shipment_date,
						a.prod_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width,
						f.id as dtls_id,
						f.color_id as dtls_color,
						f.stitch_length as stitch_length,
						f.yarn_lot,
						f.yarn_count,
						2 as type
					from
						inv_receive_master p, inv_transaction a, product_details_master e, pro_grey_prod_entry_dtls f
					where
						p.id=a.mst_id and p.id=f.mst_id and e.id=a.prod_id and a.item_category=13 and p.company_id=$cbo_company_name $date_cond and a.transaction_type in(1,4) and p.entry_form in (2,22) and p.booking_without_order=1 and a.status_active=1 and a.is_deleted=0 and p.status_active=1 and p.is_deleted=0
					
					union all
					
					select 
						a.id as trans_id,
						a.transaction_type,
						a.mst_id,
						b.po_breakdown_id as order_id,
						a.transaction_date,
						0 as receive_qty,
						b.quantity as issue_qty,
						a.cons_uom,
						d.job_no,
						d.job_no_prefix_num,
						$select_year(d.insert_date $year_con) as job_year,
						d.buyer_name,
						c.po_number,
						c.shipment_date,
						b.prod_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width,
						f.id as dtls_id,
						f.color_id as dtls_color,
						f.stitch_length as stitch_length,
						f.yarn_lot,
						f.yarn_count,
						3 as type
					from
						 inv_issue_master p, inv_transaction a, order_wise_pro_details b, wo_po_break_down c,wo_po_details_master d, product_details_master e, inv_grey_fabric_issue_dtls f
					where
						p.id=a.mst_id and p.id=f.mst_id and a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and e.id=a.prod_id and a.item_category=13 and b.trans_type in(2,3) and d.company_name=$cbo_company_name $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond and b.entry_form in (16,45) and a.status_active=1 and a.is_deleted=0
					
					union all
					
					select 
						a.id as trans_id,
						a.transaction_type,
						a.mst_id,
						0 as order_id,
						a.transaction_date,
						0 as receive_qty,
						a.cons_quantity as issue_qty,
						a.cons_uom,
						$null_field as job_no,
						$null_field as job_no_prefix_num,
						$null_field as  job_year,
						$null_field as buyer_name,
						$null_field as po_number,
						$null_field as shipment_date,
						a.prod_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width,
						f.id as dtls_id,
						f.color_id as dtls_color,
						f.stitch_length as stitch_length,
						f.yarn_lot,
						f.yarn_count,
						4 as type
					from
						 inv_issue_master p, inv_transaction a, product_details_master e, inv_grey_fabric_issue_dtls f
					where
						p.id=a.mst_id and p.id=f.mst_id and e.id=a.prod_id and p.issue_purpose=8 and a.item_category=13 and a.transaction_type in(2,3) and p.company_id=$cbo_company_name $date_cond and p.entry_form in (16,45) and a.status_active=1 and a.is_deleted=0 and p.status_active=1 and p.is_deleted=0
						
					union all
					
					select 
						a.id as trans_id,
						a.transaction_type,
						a.mst_id,
						b.po_breakdown_id as order_id,
						a.transaction_date,
						0 as receive_qty,
						b.quantity as issue_qty,
						a.cons_uom,
						d.job_no,
						d.job_no_prefix_num,
						$select_year(d.insert_date $year_con) as job_year,
						d.buyer_name,
						c.po_number,
						c.shipment_date,
						b.prod_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width,
						$null_field as  dtls_id,
						$null_field as  dtls_color,
						$null_field as stitch_length,
						$null_field as yarn_lot,
						$null_field as yarn_count,
						5 as type
					from
						 inv_item_transfer_mst p, inv_transaction a, order_wise_pro_details b, wo_po_break_down c,wo_po_details_master d, product_details_master e
					where
						p.id=a.mst_id and a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and e.id=a.prod_id and a.item_category=13 and b.trans_type in(5,6) and d.company_name=$cbo_company_name $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond and b.entry_form in (13) and a.status_active=1 and a.is_deleted=0
					
					order by transaction_date, trans_id";
			}
			else if($rptType==2)
			{
				$sql="select 
						a.id as trans_id,
						a.transaction_type,
						a.mst_id,
						b.po_breakdown_id as order_id,
						a.transaction_date,
						b.quantity as receive_qty,
						0 as issue_qty,
						a.cons_uom,
						d.job_no,
						d.job_no_prefix_num,
						$select_year(d.insert_date $year_con) as job_year,
						d.buyer_name,
						c.po_number,
						c.shipment_date,
						b.prod_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width,
						f.id as dtls_id,
						f.color_id as dtls_color,
						f.stitch_length,
						f.yarn_lot,
						f.yarn_count,
						1 as type
					from
						inv_receive_master p, inv_transaction a, order_wise_pro_details b, wo_po_break_down c,wo_po_details_master d, product_details_master e, pro_grey_prod_entry_dtls f
					where
						p.id=a.mst_id and p.id=f.mst_id  and a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and e.id=a.prod_id and a.item_category=13 and d.company_name=$cbo_company_name $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond and a.transaction_type in(1,4) and b.entry_form in (2,22) and a.status_active=1 and a.is_deleted=0
					union all
					
					select 
						a.id as trans_id,
						a.transaction_type,
						a.mst_id,
						0 as order_id,
						a.transaction_date,
						a.cons_quantity as receive_qty,
						0 as issue_qty,
						a.cons_uom,
						$null_field as job_no,
						$null_field as job_no_prefix_num,
						$null_field as job_year,
						$null_field as buyer_name,
						$null_field as po_number,
						$null_field as shipment_date,
						a.prod_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width,
						f.id as dtls_id,
						f.color_id as dtls_color,
						f.stitch_length,
						f.yarn_lot,
						f.yarn_count,
						2 as type
					from
						inv_receive_master p, inv_transaction a, product_details_master e, pro_grey_prod_entry_dtls f
					where
						p.id=a.mst_id and p.id=f.mst_id and e.id=a.prod_id and a.item_category=13 and p.company_id=$cbo_company_name $date_cond and a.transaction_type in(1,4) and p.entry_form in (2,22) and p.booking_without_order=1 and a.status_active=1 and a.is_deleted=0 and p.status_active=1 and p.is_deleted=0
						
					union all
					
					select 
						a.id as trans_id,
						a.transaction_type,
						a.mst_id,
						b.po_breakdown_id as order_id,
						a.transaction_date,
						0 as receive_qty,
						b.quantity as issue_qty,
						a.cons_uom,
						d.job_no,
						d.job_no_prefix_num,
						$select_year(d.insert_date $year_con) as job_year,
						d.buyer_name,
						c.po_number,
						c.shipment_date,
						b.prod_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width,
						$null_field as dtls_id,
						$null_field as dtls_color,
						$null_field as stitch_length,
						$null_field as yarn_lot,
						$null_field as yarn_count,
						5 as type
					from
						 inv_item_transfer_mst p, inv_transaction a, order_wise_pro_details b, wo_po_break_down c,wo_po_details_master d ,product_details_master e
					where
						p.id=a.mst_id and a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and e.id=a.prod_id and a.item_category=13 and b.trans_type in(5) and d.company_name=$cbo_company_name $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond and b.entry_form in (13) and a.status_active=1 and a.is_deleted=0
					
					order by transaction_date, trans_id";
			}
			else if($rptType==3)
			{
				$sql="select 
						a.id as trans_id,
						a.transaction_type,
						a.mst_id,
						b.po_breakdown_id as order_id,
						a.transaction_date,
						0 as receive_qty,
						b.quantity as issue_qty,
						a.cons_uom,
						d.job_no,
						d.job_no_prefix_num,
						$select_year(d.insert_date $year_con) as job_year,
						d.buyer_name,
						c.po_number,
						c.shipment_date,
						b.prod_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width,
						f.id as dtls_id,
						f.color_id as dtls_color,
						f.stitch_length,
						f.yarn_lot,
						f.yarn_count,
						3 as type
					from
						 inv_issue_master p, inv_transaction a, order_wise_pro_details b, wo_po_break_down c,wo_po_details_master d, product_details_master e, inv_grey_fabric_issue_dtls f
					where
						p.id=a.mst_id and p.id=f.mst_id and a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and e.id=a.prod_id and a.item_category=13 and b.trans_type in(2,3) and d.company_name=$cbo_company_name $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond and b.entry_form in (16,45) and a.status_active=1 and a.is_deleted=0
					
					union all
					
					select 
						a.id as trans_id,
						a.transaction_type,
						a.mst_id,
						0 as order_id,
						a.transaction_date,
						0 as receive_qty,
						a.cons_quantity as issue_qty,
						a.cons_uom,
						$null_field as job_no,
						$null_field as job_no_prefix_num,
						$null_field as  job_year,
						$null_field as buyer_name,
						$null_field as po_number,
						$null_field as shipment_date,
						a.prod_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width,
						f.id as dtls_id,
						f.color_id as dtls_color,
						f.stitch_length,
						f.yarn_lot,
						f.yarn_count,
						4 as type
					from
						 inv_issue_master p, inv_transaction a, product_details_master e, inv_grey_fabric_issue_dtls f
					where
						p.id=a.mst_id and p.id=f.mst_id and e.id=a.prod_id and p.issue_purpose=8 and a.item_category=13 and a.transaction_type in(2,3) and p.company_id=$cbo_company_name $date_cond and p.entry_form in (16,45) and a.status_active=1 and a.is_deleted=0 and p.status_active=1 and p.is_deleted=0
						
					union all
					
					select 
						a.id as trans_id,
						a.transaction_type,
						a.mst_id,
						b.po_breakdown_id as order_id,
						a.transaction_date,
						0 as receive_qty,
						b.quantity as issue_qty,
						a.cons_uom,
						d.job_no,
						d.job_no_prefix_num,
						$select_year(d.insert_date $year_con) as job_year,
						d.buyer_name,
						c.po_number,
						c.shipment_date,
						b.prod_id,
						a.inserted_by,
						$select_insert_date,
						$select_insert_time,
						e.product_name_details,
						e.color,
						e.item_color,
						e.detarmination_id,
						e.gsm,
						e.dia_width,
						$null_field as dtls_id,
						$null_field as dtls_color,
						$null_field as stitch_length,
						$null_field as yarn_lot,
						$null_field as yarn_count,
						5 as type
					from
						 inv_item_transfer_mst p, inv_transaction a, order_wise_pro_details b, wo_po_break_down c,wo_po_details_master d, product_details_master e
					where
						p.id=a.mst_id  and a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and e.id=a.prod_id and a.item_category=13 and b.trans_type in(6) and d.company_name=$cbo_company_name $cbo_buyer_name $txt_style_ref_id $txt_order_id $date_cond and b.entry_form in (13) and a.status_active=1 and a.is_deleted=0
					
					order by transaction_date, trans_id";
			}
		}
		
		//echo $sql;	
		$sql_result=sql_select($sql);
		$trms_rasult_arr=array();
		$order_id="";
		$mst_id="";
		foreach($sql_result as $row)
		{
			//if($order_id=="") $order_id=$row[csf("order_id")]; else $order_id=$order_id.",".$row[csf("order_id")];
			$mst_id .=$row[csf("mst_id")].",";
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["trans_id"]=$row[csf("trans_id")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["order_id"]=$row[csf("order_id")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["prod_id"]=$row[csf("prod_id")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["transaction_type"]=$row[csf("transaction_type")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["rec_issue_id"]=$row[csf("mst_id")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["job_no"]=$row[csf("job_no")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["job_year"]=$row[csf("job_year")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["buyer_name"]=$row[csf("buyer_name")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["po_number"]=$row[csf("po_number")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["shipment_date"]=$row[csf("shipment_date")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["receive_qty"]+=$row[csf("receive_qty")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["issue_qty"]+=$row[csf("issue_qty")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["cons_uom"]=$row[csf("cons_uom")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["inserted_by"]=$row[csf("inserted_by")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["insert_date"]=$row[csf("insert_date")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["insert_time"]=$row[csf("insert_time")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["product_name_details"]=$row[csf("product_name_details")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["color"]=$row[csf("color")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["item_color"]=$row[csf("item_color")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["detarmination_id"]=$row[csf("detarmination_id")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["gsm"]=$row[csf("gsm")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["dia_width"]=$row[csf("dia_width")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["dtls_color"]=$row[csf("dtls_color")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["stitch_length"]=$row[csf("stitch_length")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["yarn_lot"]=$row[csf("yarn_lot")];
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["yarn_count"]=$row[csf("yarn_count")];
			
			$trms_rasult_arr[$row[csf("trans_id")]][$row[csf("order_id")]]["type"]=$row[csf("type")];
			
		}
		
		/*$mst_id=substr($mst_id,0,-1);
		//echo $mst_id.jjj;die;
		
		$sql_prod_receive="select
							c.id as dtls_id,
							c.mst_id,
							c.color_id as dtls_color,
							c.stitch_length,
							c.yarn_lot,
							c.yarn_count
							
					from
							 pro_grey_prod_entry_dtls c
					where
							c.status_active=1 and c.is_deleted=0 and mst_id in($mst_id)";
		
		//echo $sql_prod_receive;die;
		$product_result_arr=array();
		$result=sql_select($sql_prod_receive);
		foreach($result as $row)
		{
			$product_receive_arr[$row[csf("mst_id")]]["dtls_id"]=$row[csf("dtls_id")];
			$product_receive_arr[$row[csf("mst_id")]]["dtls_color"]=$row[csf("dtls_color")];
			$product_receive_arr[$row[csf("mst_id")]]["stitch_length"]=$row[csf("stitch_length")];
			$product_receive_arr[$row[csf("mst_id")]]["yarn_lot"]=$row[csf("yarn_lot")];
			$product_receive_arr[$row[csf("mst_id")]]["yarn_count"]=$row[csf("yarn_count")];
		}*/
		
		
		//var_dump($trms_rasult_arr);die;
		/*if(empty($order_id))  $order_id=0;
		$sql_prod="select
							a.id as product_id,
							b.trans_id,
							b.po_breakdown_id,
							a.product_name_details,
							a.color,
							a.item_color,
							a.detarmination_id,
							a.gsm,
							a.dia_width,
							c.color_id as dtls_color,
							c.stitch_length,
							c.yarn_lot,
							c.yarn_count
							
					from
							 product_details_master a, order_wise_pro_details b, pro_grey_prod_entry_dtls c
					where
							a.id=b.prod_id and b.trans_id=c.trans_id and b.trans_type in(1,4) and b.po_breakdown_id in($order_id) order by b.trans_id";*/
							
		/*$sql_prod_receive="select
							a.id as product_id,
							b.trans_id,
							b.po_breakdown_id,
							a.product_name_details,
							a.color,
							a.item_color,
							a.detarmination_id,
							a.gsm,
							a.dia_width,
							c.color_id as dtls_color,
							c.stitch_length,
							c.yarn_lot,
							c.yarn_count
							
					from
							 product_details_master a, order_wise_pro_details b, pro_grey_prod_entry_dtls c
					where
							a.id=b.prod_id and b.trans_id=c.trans_id and b.entry_form in (2,13,22) and b.trans_type in(1,4) and b.trans_id<>0 order by b.trans_id";
		
		//echo $sql_prod_receive;die;
		$product_result_arr=array();
		$result=sql_select($sql_prod_receive);
		foreach($result as $row)
		{
			$product_receive_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]["product_id"]=$row[csf("product_id")];
			$product_receive_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]["product_name_details"]=$row[csf("product_name_details")];
			$product_receive_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]["color"]=$row[csf("color")];
			$product_receive_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]["detarmination_id"]=$row[csf("detarmination_id")];
			$product_receive_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]["gsm"]=$row[csf("gsm")];
			$product_receive_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]["width"]=$row[csf("dia_width")];
			$product_receive_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]["dtls_color"]=$row[csf("dtls_color")];
			$product_receive_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]["stitch_length"]=$row[csf("stitch_length")];
			$product_receive_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]["yarn_lot"]=$row[csf("yarn_lot")];
			$product_receive_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]["yarn_count"]=$row[csf("yarn_count")];
		}
		
		$stitch_sql=sql_select("select id,prod_id,rack,self,stitch_length from pro_grey_prod_entry_dtls where status_active=1 and is_deleted=0");
		$stitch_length=array();
		foreach($stitch_sql as $row)
		{
			$stitch_length[$row[csf("prod_id")]][$row[csf("rack")]][$row[csf("self")]]=$row[csf("stitch_length")];
		}
		$sql_prod_issue="select
							a.id as product_id,
							b.trans_id,
							b.po_breakdown_id,
							a.product_name_details,
							a.color,
							a.item_color,
							a.detarmination_id,
							a.gsm,
							a.dia_width,
							c.color_id as dtls_color,
							c.yarn_lot,
							c.yarn_count,
							c.rack,
							c.self
							
							
					from
							 product_details_master a, order_wise_pro_details b, inv_grey_fabric_issue_dtls c
					where
							a.id=b.prod_id and b.trans_id=c.trans_id and b.trans_type=2 and b.entry_form =16  order by b.trans_id";
		
		//echo $sql_prod_issue;die;
		$product_result_arr=array();
		$result=sql_select($sql_prod_issue);
		foreach($result as $row)
		{
			$product_issue_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]["product_id"]=$row[csf("product_id")];
			$product_issue_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]["product_name_details"]=$row[csf("product_name_details")];
			$product_issue_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]["color"]=$row[csf("color")];
			$product_issue_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]["body_part_id"]=$row[csf("body_part_id")];
			$product_issue_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]["detarmination_id"]=$row[csf("detarmination_id")];
			$product_issue_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]["determin_id"]=$row[csf("determin_id")];
			$product_issue_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]["gsm"]=$row[csf("gsm")];
			$product_issue_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]["width"]=$row[csf("width")];
			$product_issue_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]["dtls_color"]=$row[csf("dtls_color")];
			$product_issue_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]["stitch_length"]=$stitch_length[$row[csf("product_id")]][$row[csf("rack")]][$row[csf("self")]];
			$product_issue_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]["yarn_lot"]=$row[csf("yarn_lot")];
			$product_issue_arr[$row[csf("trans_id")]][$row[csf("po_breakdown_id")]]["yarn_count"]=$row[csf("yarn_count")];
		}
		
		$knitting_com_sql="";*/
		
		

		//var_dump($product_issue_arr);die;
		
			ob_start();	
			?>
			
			<div style="width:1930px"> 
        <fieldset style="width:1930px;">
				<table width="1910" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" align="left"> 
						<tr class="form_caption" style="border:none;">
							<td colspan="19" align="center" style="border:none;font-size:16px; font-weight:bold" >Date Wise Item Receive Issue Report </td> 
						</tr>
						<tr style="border:none;">
								<td colspan="19" align="center" style="border:none; font-size:14px;">
									Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>                                
								</td>
						</tr>
				   </table>
				   <br />
				<table width="1910" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left"> 
					<thead>
						<tr>
							<th width="30" >SL</th>
							<th width="50" >Prod. Id</th>
							<th width="60" >Year</th>
							<th width="50" >Job No</th>
							<th width="70">Buyer</th>
							<th width="120">Order No</th>
							<th width="80">Ship Date</th>
							<th width="80" >Trans. Date</th>
							<th width="130" >Trans. Ref.</th>
                            <th width="100">Challan No</th>
                            <th width="100">Party Name</th>
                            <th width="80" >Yarn Lot</th>
                            <th width="80" >Yarn Count</th>
                            <th width="100">Construction</th>
							<th width="110">Composition</th>
							<th width="80">Color</th>
							<th width="50">GSM</th>
                            <th width="50">Dia</th>
                            <th width="60">Stitch Lenth</th>
							<th width="80">Receive Qty</th>
							<th width="80">Issue Qty</th>
							<th width="110">User</th>
							<th width="160">Insert Date</th>
						</tr>
					</thead>
			   </table> 
			  <div style="width:1928px; overflow-y: scroll; max-height:250px;" id="scroll_body">
				<table width="1910" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
					<tbody>
					<?
					$yarn_count_arr=return_library_array( "select id, yarn_count from  lib_yarn_count",'id','yarn_count');
					$i=1;$total_receive="";$total_issue="";
					foreach($trms_rasult_arr as $trans_key=>$value)
					{
						foreach($value as $order_key=>$val)
						{
							if ($i%2==0)
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><p><? echo $i; ?>&nbsp;</p></td>
								<td width="50"><p><? echo $val["prod_id"]; ?>&nbsp;</p></td>
								<td width="60"  align="center"><p><? echo $val["job_year"]; ?>&nbsp;</p></td>
								<td width="50"  align="center"><p><? echo $val["job_no_prefix_num"]; ?>&nbsp;</p></td>
								<td width="70"><p><? echo $buyer_short_arr[$val["buyer_name"]]; ?>&nbsp;</p></td>
								<td width="120" align="center"><p><? echo $val["po_number"]; ?>&nbsp;</p></td>
								<td width="80"  align="center"><p><? if($val["shipment_date"]!="0000-00-00") echo change_date_format($val["shipment_date"]); else echo ""; ?>&nbsp;</p></td>
								<td width="80" align="center"><p><? if($val["transaction_date"]!="0000-00-00") echo change_date_format($val["transaction_date"]); else echo ""; ?>&nbsp;</p></td>
								<td width="130"  align="center"><p>
								<?
									
									if($val["transaction_type"]==1  || $val["transaction_type"]==4) 
									{
										echo $receive_num_arr[$val['rec_issue_id']]["recv_number"];
									}
									else if($val["transaction_type"]==2  || $val["transaction_type"]==3)
									{
										echo $issue_num_arr[$val['rec_issue_id']]["issue_number"];
									}
	
	
								?>&nbsp;
								</p></td>
                                <td width="100"  align="center"><p>
								<?
									
									if($val["transaction_type"]==1  || $val["transaction_type"]==4) 
									{
										echo $receive_num_arr[$val['rec_issue_id']]["challan_no"];
									}
									else if($val["transaction_type"]==2  || $val["transaction_type"]==3)
									{
										echo $issue_num_arr[$val['rec_issue_id']]["challan_no"];
									}
	
	
								?>&nbsp;
								</p></td>
                                <td width="100"  align="center"><p>
								<?
									
									if($val["transaction_type"]==1  || $val["transaction_type"]==4) 
									{
										if($receive_num_arr[$val['rec_issue_id']]["knitting_source"]==1)
										{
											echo $company_arr[$receive_num_arr[$val['rec_issue_id']]["knitting_company"]];
										}
										else if($receive_num_arr[$val['rec_issue_id']]["knitting_source"]==3)
										{
											echo $supplier_arr[$receive_num_arr[$val['rec_issue_id']]["knitting_company"]];
										}
									}
									else if($val["transaction_type"]==2  || $val["transaction_type"]==3)
									{
										if($issue_num_arr[$val['rec_issue_id']]["knit_dye_source"]==1)
										{
											echo $company_arr[$issue_num_arr[$val['rec_issue_id']]["knit_dye_company"]];
										}
										else if($issue_num_arr[$val['rec_issue_id']]["knit_dye_source"]==3)
										{
											echo $supplier_arr[$issue_num_arr[$val['rec_issue_id']]["knit_dye_company"]];
										}
									}
	
	
								?>&nbsp;
								</p></td>
                                <?
                                if($val["transaction_type"]==1  || $val["transaction_type"]==4 || $val["transaction_type"]==5) 
								{
									?>
									<td width="80" align="center"><p><? echo $val["yarn_lot"]; ?>&nbsp;</p></td>
									<td width="80" align="center"><p>
									<?
									$yarn_count_id_arr=explode(",",$val["yarn_count"]);
									$yarn_count_all="";
									foreach($yarn_count_id_arr as $yarn_count_id)
									{
										if($yarn_count_all!="") $yarn_count_all.=", ";
										$yarn_count_all.=$yarn_count_arr[$yarn_count_id];
									}
									 echo $yarn_count_all; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $construction_arr[$val["detarmination_id"]]; ?>&nbsp;</p></td>
									<td width="110"><p><? echo $composition_arr[$val["detarmination_id"]]; ?>&nbsp;</p></td>
									<td width="80"><p><? echo $color_arr[$val["dtls_color"]]; ?>&nbsp;</p></td>
									<td width="50"><p><? echo $val["gsm"]; ?>&nbsp;</p></td>
									<td width="50"><p><? echo $val["dia_width"]; ?>&nbsp;</p></td>
									<td width="60" ><p><? echo $val["stitch_length"]; ?>&nbsp;</p></td>
									<?
                                }
                                else if($val["transaction_type"]==2  || $val["transaction_type"]==3 || $val["transaction_type"]==6) 
								{
									?>
									<td width="80" align="center"><p><? echo $val["yarn_lot"]; ?>&nbsp;</p></td>
									<td width="80" align="center"><p><? echo $yarn_count_arr[$val["yarn_count"]]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $construction_arr[$val["detarmination_id"]]; ?>&nbsp;</p></td>
									<td width="110"><p><? echo $composition_arr[$val["detarmination_id"]]; ?>&nbsp;</p></td>
									<td width="80"><p><? echo $color_arr[$val["dtls_color"]]; ?>&nbsp;</p></td>
									<td width="50"><p><? echo $val["gsm"]; ?>&nbsp;</p></td>
									<td width="50"><p><? echo $val["dia_width"]; ?>&nbsp;</p></td>
									<td width="60" ><p><? echo $val["stitch_length"]; ?>&nbsp;</p></td>
									<?
                                }
                                ?>
                                
								<td width="80" align="right"><p><? echo number_format($val["receive_qty"],2); $total_receive +=$val["receive_qty"]; ?></p></td>
								<td align="right" width="80"><p><? echo number_format($val["issue_qty"],2); $total_issue +=$val["issue_qty"]; ?></p></td>
                                <td width="110"><p><? echo $user_name_arr[$val["inserted_by"]]; ?>&nbsp;</p></td>
                                <td width="160"><p><? echo change_date_format($val["insert_date"])." ".$val["insert_time"]; ?>&nbsp;</p></td>
							</tr>
							<?
							$i++;
						}
					}
					?>   
					</tbody>
				</table>
                <table width="1910" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
                    <tfoot>
                    	<th width="30">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="130">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="60">Total:</th>
                        <th width="80" id="value_total_receive"><? echo number_format($total_receive,2); ?></th>
                        <th width="80" id="value_total_issue"><? echo number_format($total_issue,2); ?></th>
                        <th width="110">&nbsp;</th>
                        <th width="160">&nbsp;</th>
                    </tfoot> 
                </table>
			 </div>
        </fieldset>
		</div>    
			<?	 
	
	}
	
	else if($cbo_item_cat==8 || $cbo_item_cat==9 || $cbo_item_cat==10 || $cbo_item_cat==11 || $cbo_item_cat==15 || $cbo_item_cat==16 || $cbo_item_cat==17 || $cbo_item_cat==18 || $cbo_item_cat==19 || $cbo_item_cat==20 || $cbo_item_cat==21 || $cbo_item_cat==22)
	{
		
		if($cbo_item_cat==8) $item_cond="and a.item_category=8";
		else if($cbo_item_cat==9) $item_cond="and a.item_category=9";
		else if($cbo_item_cat==10) $item_cond="and a.item_category=10";
		else if($cbo_item_cat==11) $item_cond="and a.item_category=11";
		else if($cbo_item_cat==15) $item_cond="and a.item_category=15";
		else if($cbo_item_cat==16) $item_cond="and a.item_category=16";
		else if($cbo_item_cat==17) $item_cond="and a.item_category=17";
		else if($cbo_item_cat==18) $item_cond="and a.item_category=18";
		else if($cbo_item_cat==19) $item_cond="and a.item_category=19";
		else if($cbo_item_cat==20) $item_cond="and a.item_category=20";
		else if($cbo_item_cat==21) $item_cond="and a.item_category=21";
		else if($cbo_item_cat==22) $item_cond="and a.item_category=22";
		else echo  $item_cond="";
		//echo $item_cond;die;
		
		
		//echo $date_cond;die;
		
		if($rptType==1)
		{
			$sql="select 
					a.id as trans_id,
					a.transaction_type,
					a.mst_id as rec_issue_id,
					a.transaction_date,
					case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end as receive_qty,
					case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end as issue_qty,
					a.cons_uom,
					b.id as prod_id,
					b.item_group_id,
					b.sub_group_name,
					b.item_description,
					b.item_size,
					a.cons_rate,
					a.cons_amount,
					a.inserted_by,
					$select_insert_date,
					$select_insert_time,
					a.order_qnty,
					a.order_rate
				from
					inv_transaction a, product_details_master b
				where
					a.prod_id=b.id and a.company_id=$cbo_company_name and a.transaction_type in(1,2,3,4,5,6)  $item_cond $date_cond
				order by a.transaction_date, a.id";
		}
		else if($rptType==2)
		{
			$sql="select 
					a.id as trans_id,
					a.transaction_type,
					a.mst_id as rec_issue_id,
					a.transaction_date,
					a.cons_quantity as receive_qty,
					'' as issue_qty,
					a.cons_uom,
					b.id as prod_id,
					b.item_group_id,
					b.sub_group_name,
					b.item_description,
					b.item_size,
					a.cons_rate,
					a.cons_amount,
					c.receive_basis,
					c.booking_id,
					c.booking_no,
					a.inserted_by,
					$select_insert_date,
					$select_insert_time,
					a.order_qnty,
					a.order_rate
				from
					inv_transaction a, product_details_master b, inv_receive_master c
				where
					a.mst_id=c.id and a.prod_id=b.id and a.company_id=$cbo_company_name and a.transaction_type in(1,4,5)  $item_cond $date_cond
				order by a.transaction_date, a.id";
		}
		else if($rptType==3)
		{
			$sql="select 
					a.id as trans_id,
					a.transaction_type,
					a.mst_id as rec_issue_id,
					a.transaction_date,
					'' as receive_qty,
					a.cons_quantity as issue_qty,
					a.cons_uom,
					b.id as prod_id,
					b.item_group_id,
					b.sub_group_name,
					b.item_description,
					b.item_size,
					a.cons_rate,
					a.cons_amount,
					a.department_id,
					a.section_id,
					a.inserted_by,
					$select_insert_date,
					$select_insert_time,
					a.order_qnty,
					a.order_rate
				from
					inv_transaction a, product_details_master b
				where
					a.prod_id=b.id and a.company_id=$cbo_company_name and a.transaction_type in(2,3,6)  $item_cond $date_cond
				order by a.transaction_date, a.id";
		}
		//echo $sql;
		if($rptType==1)
		{
			$table_width=1860;
			$div_width="1880px";
		}
		else if($rptType==2)
		{
			$table_width=1940;
			$div_width="1960px";
		}
		else if($rptType==3)
		{
			$table_width=1640;
			$div_width="1660px";
		}
		//echo $sql;		
		$sql_result=sql_select($sql);
		$requisiton_arr=return_library_array( "select id, requ_prefix_num from inv_purchase_requisition_mst",'id','requ_prefix_num');
		$wo_sql=sql_select("select id, wo_number_prefix_num,requisition_no from wo_non_order_info_mst where item_category not in(1,2,3,12,13,14)");
		foreach($wo_sql as $row)
		{
			$wo_arr[$row[csf("id")]]['wo_number']=$row[csf("wo_number_prefix_num")];
			$wo_arr[$row[csf("id")]]['requ_no']=$row[csf("requisition_no")];
		}
		$pi_num_arr=return_library_array( "select id, pi_number from com_pi_master_details",'id','pi_number');
		$department_arr=return_library_array( "select id, department_name from lib_department",'id','department_name');
		$section_arr=return_library_array( "select id, section_name from lib_section",'id','section_name');
		ob_start();	
		
		?>
			
		<div style="width:<? echo $div_width; ?>"> 
        <fieldset style="width:<? echo $div_width; ?>">
				<table width="<? echo $table_width; ?>" border="0" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" align="left"> 
						<tr class="form_caption" style="border:none;">
							<td colspan="12" align="center" style="border:none;font-size:16px; font-weight:bold" >Date Wise Item Receive Issue Report </td> 
						</tr>
						<tr style="border:none;">
								<td colspan="12" align="center" style="border:none; font-size:14px;">
									Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>                                
								</td>
						</tr>
				   </table>
				   <br />
				<table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left"> 
					<thead>
						<tr>
							<th width="30" >SL</th>
							<th width="70" >Prod. Id</th>
							<th width="100" >Trans. Date</th>
							<th width="130" >Trans. Ref.</th>
                            <th width="100" >Challan No</th>
                            <?
							if($rptType==2)
							{
								?>
								<th width="80">Receive Basis</th>
                                <?
							}
							?>
                            <th width="100">Requisition No</th>
                            <?
							if($rptType==2)
							{
								?>
								<th width="100">WO/PI No.</th>
                                <?
							}
							else if($rptType==3)
							{
								?>
								<th width="100">Department</th>
                                <th width="100">Section</th>
                                <?
							}
							?>
							<th width="100">Item Group</th>
							<th width="100">Item Sub-Group</th>
                            <th width="120">Item Description</th>
                            <th width="80">Size</th>
                            <?
							if($rptType==2 || $rptType==1 )
							{
								?>
                                <th width="80">Currency</th>
                                <th width="80">Exchange Rate</th>
                                <th width="80">Actual Rate</th>
								<th width="80">Receive Qty</th>
                                <th width="80">Actual Amt</th>
                                <?
							}
							if($rptType==3 || $rptType==1 )
							{
								?>
								<th  width="80">Issue Qty</th>
                                <?
							}
							?>
                            <th  width="80">Rate(TK)</th>
                            <th  width="100">Amount(TK)</th>
                            <th  width="110">User</th>
                            <th  width="160">Insert Date</th>
						</tr>
					</thead>
			   </table> 
               <br />
			  <div style="width:<? echo $div_width; ?>; overflow-y: scroll; max-height:250px;" id="scroll_body">
				<table width="<? echo $table_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
					<tbody>
					<?
					$group_arr=return_library_array( "select id, item_name from  lib_item_group",'id','item_name');
					
					$i=1;$total_receive="";$total_issue="";
					foreach($sql_result as $row)
					{
						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><p><? echo $i; ?>&nbsp;</p></td>
							<td width="70" align="center"><p><? echo $row[csf("prod_id")]; ?>&nbsp;</p></td>
							<td width="100" align="center"><p><? if($row[csf("transaction_date")]!="0000-00-00") echo change_date_format($row[csf("transaction_date")]); else echo ""; ?>&nbsp;</p></td>
							<td width="130"  align="center"><p>
							<?
								
									if($row[csf("transaction_type")]==1  || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) 
									{
										echo $receive_num_arr[$row[csf('rec_issue_id')]]["recv_number"];
									}
									else if($row[csf("transaction_type")]==2  || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6)
									{
										echo $issue_num_arr[$row[csf('rec_issue_id')]]["issue_number"];
									}
	

							?>&nbsp;
							</p>
                            </td>
                            <td width="100"><p>
							<?
								
									if($row[csf("transaction_type")]==1  || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) 
									{
										echo $receive_num_arr[$row[csf('rec_issue_id')]]["challan_no"];
									}
									else if($row[csf("transaction_type")]==2  || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6)
									{
										echo $issue_num_arr[$row[csf('rec_issue_id')]]["challan_no"];
									}
	

							?>&nbsp;
							</p>
                            </td>
                            <?
							if($rptType==2)
							{
								?>
								<td width="80"><p><? echo $receive_basis_arr[$row[csf("receive_basis")]]; ?>&nbsp;</p></td>
                                <?
							}
							?>
                            <td width="100"><p>
                            <?
							if($rptType==2)
							{
								if($row[csf("receive_basis")]==7)
								{
									echo $requisiton_arr[$row[csf("booking_id")]];
								}
								else if ($row[csf("receive_basis")]==2)
								{
									 $req_all_arr=array_unique(explode(",",$wo_arr[$row[csf("booking_id")]]['requ_no']));
									 $req_all="";
									 foreach($req_all_arr as $req_id)
									 {
										 if($req_all=="") $req_all=$requisiton_arr[$req_id]; else  $req_all.=", ".$requisiton_arr[$req_id];
									 }
									 echo $req_all;
								}
							}
							else
							{
								echo $issue_num_arr[$row[csf('rec_issue_id')]]["req_no"];
							}
                            ?>&nbsp;</p></td>
                            <?
							if($rptType==2)
							{
								?>
								<td width="100" align="center"><p>
								<?
                                if($row[csf("receive_basis")]==1)
                                {
                                    echo $pi_num_arr[$row[csf("booking_id")]];
                                }
                                else if($row[csf("receive_basis")]==2)
                                {
                                    echo $wo_arr[$row[csf("booking_id")]]['wo_number'];
									//requ_no
                                }
                                ?>&nbsp;</p>
                                </td>
                                <?
							}
							else if($rptType==3)
							{
								?>
								<td width="100" align="center"><p><? echo $department_arr[$row[csf("department_id")]]; ?>&nbsp;</p></td>
                                <td width="100" align="center"><p><? echo $section_arr[$row[csf("section_id")]]; ?>&nbsp;</p></td>
                                <?
							}
							
							?>
                            <td width="100" ><p><? echo $group_arr[$row[csf('item_group_id')]]; ?>&nbsp;</p></td>
                            <td width="100" ><p><? echo $row[csf('sub_group_name')]; ?>&nbsp;</p></td>
                            <td width="120"><p><? echo $row[csf('item_description')]; ?>&nbsp;</p></td>
                            <td width="80"><p><? echo $row[csf('item_size')]; ?>&nbsp;</p></td>
                             <?
							if($rptType==2 || $rptType==1 )
							{
								?>
                                <td width="80" align="center"><p><? if($row[csf("transaction_type")]==1) echo $currency[$receive_num_arr[$row[csf('rec_issue_id')]]["currency_id"]]; ?>&nbsp;</p></td>
                                <td width="80" align="right"><p><? if($row[csf("transaction_type")]==1) echo number_format($receive_num_arr[$row[csf('rec_issue_id')]]["exchange_rate"],2); ?>&nbsp;</p></td>
                                <td width="80" align="right"><p><? if($row[csf("transaction_type")]==1) echo number_format($row[csf('order_rate')],2); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($row[csf("receive_qty")],2); $total_receive +=$row[csf("receive_qty")]; ?></p></td>
                                <td width="80" align="right"><p><? if($row[csf("transaction_type")]==1) $order_amt=$row[csf('order_qnty')]*$row[csf('order_rate')]; echo number_format($order_amt,4); $total_order_amt+=$order_amt; $order_amt=0; ?></p></td>
                                <?
							}
							if($rptType==3 || $rptType==1 )
							{
								?>
								<td width="80" align="right"><p><? echo number_format($row[csf("issue_qty")],2); $total_issue +=$row[csf("issue_qty")]; ?></p></td>
                                <?
							}
							?>
                            <td width="80" align="right"><p><? echo number_format($row[csf("cons_rate")],2); ?></p></td>
                            <td align="right" width="100" style="padding-right:3px"><p><? echo number_format($row[csf("cons_amount")],2); $total_amount +=$row[csf("cons_amount")]; ?></p></td>
                            <td width="107"><p><? echo $user_name_arr[$row[csf("inserted_by")]]; ?>&nbsp;</p></td>
                            <td width="160"><p><? echo change_date_format($row[csf("insert_date")])." ".$row[csf("insert_time")]; ?>&nbsp;</p></td>
						</tr>
						<?
						$i++;
					}
					?>   
					</tbody>
				</table>
                <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
                    <tfoot>
                    	<tr>
                            <th width="30">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="130">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <?
							if($rptType==2)
							{
								?>
                            	<th width="80">&nbsp;</th>
                                <?
							}
							?>
                            <th width="100">&nbsp;</th>
                            <?
							if($rptType==2)
							{
								?>
                            	<th width="80">&nbsp;</th>
                                <?
							}
							else if($rptType==3)
							{
								?>
                            	<th width="100">&nbsp;</th>
                                <th width="100">&nbsp;</th>
                                <?
							}
							
							?>
                            <th width="100">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="80"><? if($rptType==3) echo "Total:"; ?></th>
                            <?
							if($rptType==2 || $rptType==1 )
							{
								?>
                                <th width="80" >&nbsp;</th>
                                <th width="80" >&nbsp;</th>
                                <th width="80" >Total:</th>
                            	<th width="80" id="value_total_receive"><? echo number_format($total_receive,2); ?></th>
                                <th width="80"  id="value_total_order_amt"><? echo number_format($total_order_amt,2); ?></th>
                                <?
							}
							if($rptType==3 || $rptType==1 )
							{
								?>
                            	<th width="80" id="value_total_issue"><? echo number_format($total_issue,2); ?></th>
                                <?
							}
							?>
                            <th width="80" >&nbsp;</th>
                            <th id="value_total_amount"  width="100"><? echo number_format($total_amount,2); ?></th>
                            <th width="105" >&nbsp;</th>
                            <th width="160" >&nbsp;</th>
                        </tr>
                    </tfoot> 
                </table>
			 </div>
        </fieldset>
		</div>    
			<?	 
	}
	else if($cbo_item_cat==1)
	{
		
	$yarn_pi_num_arr=return_library_array( "select id, pi_number from com_pi_master_details where item_category_id=1",'id','pi_number');
	$yarn_work_order_arr=return_library_array( "select id, wo_number from  wo_non_order_info_mst where item_category=1",'id','wo_number');
	$yarn_dyeing_wo_arr=return_library_array( "select id, ydw_no from wo_yarn_dyeing_mst where item_category_id=24",'id','ydw_no');
	
	
		$yarn_count_cond="";
		if($cbo_yarn_count!=0) $yarn_count_cond=" and b.yarn_count_id=$cbo_yarn_count";
		
		//echo $date_cond;die;
		if($rptType==1)
		{
			if($cbo_dyed_type==0)
			{
				$sql="select
							b.id as prod_id, 
							a.id as trans_id,
							a.receive_basis,
							a.transaction_type,
							a.mst_id as rec_issue_id,
							a.transaction_date,
							case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end as receive_qty ,
							case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end as issue_qty ,
							case when a.transaction_type in(2) then a.return_qnty else 0 end as return_qnty ,
							a.cons_uom,
							b.yarn_comp_type1st as yarn_comp_type1st,
							b.yarn_comp_percent1st as yarn_comp_percent1st,
							b.yarn_comp_type2nd as yarn_comp_type2nd,
							b.yarn_comp_percent2nd  as yarn_comp_percent2nd,
							b.lot,
							b.yarn_count_id,
							b.yarn_type,
							b.color,
							a.cons_rate,
							a.cons_amount,
							a.inserted_by,
							$select_insert_date,
							$select_insert_time,
							a.order_qnty,
							a.order_rate
						from
							inv_transaction a, product_details_master b
						where
							a.prod_id=b.id and a.item_category=1 and a.company_id=$cbo_company_name and a.transaction_type in(1,2,3,4,5,6) and a.cons_quantity>0 and a.status_active=1 and a.is_deleted=0   $date_cond $yarn_count_cond order by a.transaction_date, a.id";
			}
			else
			{
				if($cbo_dyed_type==1) $purpose='2'; else $purpose='1,3,4,5,6,7,8,12,15,16,26,29,30';
				$sql="select
							b.id as prod_id, 
							a.id as trans_id,
							a.receive_basis,
							a.transaction_type,
							a.mst_id as rec_issue_id,
							a.transaction_date,
							case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end as receive_qty ,
							case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end as issue_qty ,
							case when a.transaction_type in(2) then a.return_qnty else 0 end as return_qnty ,
							a.cons_uom,
							b.yarn_comp_type1st as yarn_comp_type1st,
							b.yarn_comp_percent1st as yarn_comp_percent1st,
							b.yarn_comp_type2nd as yarn_comp_type2nd,
							b.yarn_comp_percent2nd as yarn_comp_percent2nd,
							b.lot,
							b.yarn_count_id,
							b.yarn_type,
							b.color,
							1 as type,
							a.cons_rate,
							a.cons_amount,
							a.inserted_by,
							$select_insert_date,
							$select_insert_time,
							a.order_qnty,
							a.order_rate
						from
							inv_transaction a, product_details_master b,  inv_receive_master c
						where
							a.prod_id=b.id and a.mst_id=c.id and a.item_category=1 and a.company_id=$cbo_company_name and a.transaction_type in(1,4,5) and c.receive_purpose in($purpose)  and a.cons_quantity>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $date_cond $yarn_count_cond  
							
				union all
						
						select
							b.id as prod_id, 
							a.id as trans_id,
							a.receive_basis,
							a.transaction_type,
							a.mst_id as rec_issue_id,
							a.transaction_date,
							case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end as receive_qty ,
							case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end as issue_qty ,
							case when a.transaction_type in(2) then a.return_qnty else 0 end as return_qnty ,
							a.cons_uom,
							b.yarn_comp_type1st as yarn_comp_type1st,
							b.yarn_comp_percent1st as yarn_comp_percent1st,
							b.yarn_comp_type2nd as yarn_comp_type2nd,
							b.yarn_comp_percent2nd as yarn_comp_percent2nd,
							b.lot,
							b.yarn_count_id,
							b.yarn_type,
							b.color,
							2 as type,
							a.cons_rate,
							a.cons_amount,
							a.inserted_by,
							$select_insert_date,
							$select_insert_time,
							a.order_qnty,
							a.order_rate
						from
							inv_transaction a, product_details_master b, inv_issue_master c
						where
							a.prod_id=b.id and a.mst_id=c.id and a.item_category=1 and a.company_id=$cbo_company_name and a.transaction_type in(2,3,6) and c.issue_purpose in($purpose)  and a.cons_quantity>0 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $date_cond $yarn_count_cond
							order by transaction_date, trans_id";
							
			}
		}
		else if($rptType==2)
		{
			if($cbo_dyed_type==0)
			{
				$sql="select
							b.id as prod_id, 
							a.id as trans_id,
							a.receive_basis,
							a.transaction_type,
							a.mst_id as rec_issue_id,
							a.transaction_date,
							a.cons_quantity  as receive_qty ,
							a.cons_uom,
							b.yarn_comp_type1st as yarn_comp_type1st,
							b.yarn_comp_percent1st as yarn_comp_percent1st,
							b.yarn_comp_type2nd as yarn_comp_type2nd,
							b.yarn_comp_percent2nd as yarn_comp_percent2nd,
							b.lot,
							b.yarn_count_id,
							b.yarn_type,
							b.color,
							a.cons_rate,
							a.cons_amount,
							a.inserted_by,
							$select_insert_date,
							$select_insert_time,
							a.order_qnty,
							a.order_rate
						from
							inv_transaction a, product_details_master b
						where
							a.prod_id=b.id and a.item_category=1 and a.company_id=$cbo_company_name and a.transaction_type in(1,4,5)  and a.cons_quantity>0 and a.status_active=1 and a.is_deleted=0   $date_cond $yarn_count_cond order by a.transaction_date, a.id";
			}
			else
			{
				if($cbo_dyed_type==1) $purpose='2'; else $purpose='1,3,4,5,6,7,8,12,15,16,26,29,30';
				$sql="select
							b.id as prod_id, 
							a.id as trans_id,
							a.receive_basis,
							a.transaction_type,
							a.mst_id as rec_issue_id,
							a.transaction_date,
							a.cons_quantity as receive_qty ,
							a.cons_uom,
							b.yarn_comp_type1st as yarn_comp_type1st,
							b.yarn_comp_percent1st as yarn_comp_percent1st,
							b.yarn_comp_type2nd as yarn_comp_type2nd,
							b.yarn_comp_percent2nd as yarn_comp_percent2nd,
							b.lot,
							b.yarn_count_id,
							b.yarn_type,
							b.color,
							1 as type,
							a.cons_rate,
							a.cons_amount,
							a.inserted_by,
							$select_insert_date,
							$select_insert_time,
							a.order_qnty,
							a.order_rate
						from
							inv_transaction a, product_details_master b,  inv_receive_master c
						where
							a.prod_id=b.id and a.mst_id=c.id and a.item_category=1 and a.company_id=$cbo_company_name and a.transaction_type in(1,4,5) and c.receive_purpose in($purpose)  and a.cons_quantity>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $date_cond $yarn_count_cond  
							order by a.transaction_date, a.id";
							
			}
		}
		else if($rptType==3)
		{
			if($cbo_dyed_type==0)
			{
				$sql="select
							b.id as prod_id, 
							a.id as trans_id,
							a.receive_basis,
							a.transaction_type,
							a.mst_id as rec_issue_id,
							a.transaction_date,
							a.cons_quantity as issue_qty ,
							a.return_qnty as return_qnty ,
							a.cons_uom,
							b.yarn_comp_type1st as yarn_comp_type1st,
							b.yarn_comp_percent1st as yarn_comp_percent1st,
							b.yarn_comp_type2nd as yarn_comp_type2nd,
							b.yarn_comp_percent2nd as yarn_comp_percent2nd,
							b.lot,
							b.yarn_count_id,
							b.yarn_type,
							b.color,
							a.cons_rate,
							a.cons_amount,
							a.inserted_by,
							$select_insert_date,
							$select_insert_time,
							a.order_qnty,
							a.order_rate
						from
							inv_transaction a, product_details_master b
						where
							a.prod_id=b.id and a.item_category=1 and a.company_id=$cbo_company_name and a.transaction_type in(2,3,6)  and a.cons_quantity>0 and a.status_active=1 and a.is_deleted=0   $date_cond $yarn_count_cond order by a.transaction_date, a.id";
			}
			else
			{
				if($cbo_dyed_type==1) $purpose='2'; else $purpose='1,3,4,5,6,7,8,12,15,16,26,29,30';
				$sql="select
							b.id as prod_id, 
							a.id as trans_id,
							a.receive_basis,
							a.transaction_type,
							a.mst_id as rec_issue_id,
							a.transaction_date,
							a.cons_quantity as issue_qty ,
							a.return_qnty as return_qnty ,
							a.cons_uom,
							b.yarn_comp_type1st as yarn_comp_type1st,
							b.yarn_comp_percent1st as yarn_comp_percent1st,
							b.yarn_comp_type2nd as yarn_comp_type2nd,
							b.yarn_comp_percent2nd as yarn_comp_percent2nd,
							b.lot,
							b.yarn_count_id,
							b.yarn_type,
							b.color,
							a.cons_rate,
							a.cons_amount,
							a.inserted_by,
							$select_insert_date,
							$select_insert_time,
							a.order_qnty,
							a.order_rate
						from
							inv_transaction a, product_details_master b, inv_issue_master c
						where
							a.prod_id=b.id and a.mst_id=c.id and a.item_category=1 and a.company_id=$cbo_company_name and a.transaction_type in(2,3,6) and c.issue_purpose in($purpose)  and a.cons_quantity>0 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $date_cond $yarn_count_cond
							order by a.transaction_date, a.id";
							
			}
		}
		
		//echo $sql;
		$sql_result=sql_select($sql);
		
		
		$sql_receive=sql_select("select id,entry_form,recv_number,receive_basis,booking_no,receive_purpose,supplier_id,booking_id,currency_id,exchange_rate,challan_no from inv_receive_master where company_id=$cbo_company_name and item_category=1");
		$receive_data_arr=array();
		foreach($sql_receive as $row)
		{
			$receive_data_arr[$row[csf("id")]]['rec_id']=$row[csf("id")];
			$receive_data_arr[$row[csf("id")]]['recv_number']=$row[csf("recv_number")];
			$receive_data_arr[$row[csf("id")]]['receive_basis']=$row[csf("receive_basis")];
			$receive_data_arr[$row[csf("id")]]['booking_id']=$row[csf("booking_id")];
			$receive_data_arr[$row[csf("id")]]['receive_purpose']=$row[csf("receive_purpose")];
			$receive_data_arr[$row[csf("id")]]['supplier_id']=$row[csf("supplier_id")];
			$receive_data_arr[$row[csf("id")]]['entry_form']=$row[csf("entry_form")];
			$receive_data_arr[$row[csf("id")]]['currency_id']=$row[csf("currency_id")];
			$receive_data_arr[$row[csf("id")]]['exchange_rate']=$row[csf("exchange_rate")];
			$receive_data_arr[$row[csf("id")]]['challan_no']=$row[csf("challan_no")];
		}
		
		$sql_issue=sql_select("select id,entry_form,issue_number,issue_basis,booking_no,issue_purpose,supplier_id,booking_id from inv_issue_master where company_id=$cbo_company_name and item_category=1");
		$issue_data_arr=array();
		foreach($sql_issue as $row)
		{
			$issue_data_arr[$row[csf("id")]]['issue_id']=$row[csf("id")];
			$issue_data_arr[$row[csf("id")]]['issue_number']=$row[csf("issue_number")];
			$issue_data_arr[$row[csf("id")]]['issue_basis']=$row[csf("issue_basis")];
			$issue_data_arr[$row[csf("id")]]['booking_id']=$row[csf("booking_id")];
			$issue_data_arr[$row[csf("id")]]['issue_purpose']=$row[csf("issue_purpose")];
			$issue_data_arr[$row[csf("id")]]['supplier_id']=$row[csf("supplier_id")];
			$issue_data_arr[$row[csf("id")]]['entry_form']=$row[csf("entry_form")];
		}
		//var_dump($issue_data_arr);die;
		if($rptType==1)
		{
			$table_width=2340;
			$div_width="2360px";
		}
		else if($rptType==2)
		{
			$table_width=2260;
			$div_width="2280px";
		} 
		else 
		{
			$table_width=1940;
			$div_width="1960px";
		}
		//echo $sql;		
		$sql_result=sql_select($sql);
		ob_start();	
		?>
		
		<div style="width:<? echo $div_width; ?>"> 
        <fieldset style="width:<? echo $div_width; ?>;">
				<table width="<? echo $table_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" align="left"> 
						<tr class="form_caption" style="border:none;">
							<td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" >Date Wise Item Receive Issue Report </td> 
						</tr>
						<tr style="border:none;">
								<td colspan="14" align="center" style="border:none; font-size:14px;">
									Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>                                
								</td>
						</tr>
				   </table>
				   <br />
				<table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left"> 
					<thead>
						<tr>
							<th width="30" >SL</th>
							<th width="70" >Prod. Id</th>
							<th width="80" >Trans. Date</th>
							<th width="130" >Trans. Ref.</th>
                            <th width="100" >Challan No</th>
                            <th width="100" >Party Name</th>
                            <th width="100" >Yarn Lot</th>
                            <th width="100" >Yarn Count</th>
							<th width="120">Composition</th>
							<th width="100">Yarn Type</th>
							<th width="100">Color</th>
                            <th width="100">Basis</th>
                            <th width="100">WO/PI NO.</th>
                            <th width="100">Purpose</th>
                            <?
							if($rptType==2 || $rptType==1 )
							{
								?>
                                <th width="80">Currency</th>
                                <th width="80">Exchange Rate</th>
                                <th width="80">Actual Rate</th>
								<th width="80">Receive Qty</th>
                                <th width="80">Actual Amt</th>
                                <?
							}
							if($rptType==3 || $rptType==1 )
							{
								?>
								<th width="80">Issue Qty</th>
                                <th width="80">Returnable Qty</th>
                                <?
							}
							?>
                            <th width="80">Rate(TK)</th>
                            <th width="100">Amount(TK)</th>
                            <th width="110">User</th>
                            <th width="160">Insert Date</th>
						</tr>
					</thead>
			   </table> 
			  <div style="width:<? echo $div_width; ?>; overflow-y: scroll; max-height:250px;" id="scroll_body">
				<table width="<? echo $table_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
					<tbody>
					<?
					$yarn_count_arr=return_library_array( "select id, yarn_count from  lib_yarn_count",'id','yarn_count');
					
					$i=1;$total_receive="";$total_issue="";
					foreach($sql_result as $val)
					{
						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><p><? echo $i; ?>&nbsp;</p></td>
							<td width="70" align="center"><p><? echo $val[csf("prod_id")]; ?>&nbsp;</p></td>
							<td width="80" align="center"><p><? if($val[csf("transaction_date")]!="0000-00-00") echo change_date_format($val[csf("transaction_date")]); else echo ""; ?>&nbsp;</p></td>
							<td width="130"  align="center"><p>
							<?
								
								if($val[csf("transaction_type")]==1  || $val[csf("transaction_type")]==4 || $val[csf("transaction_type")]==5) 
								{
									echo $receive_num_arr[$val[csf('rec_issue_id')]]["recv_number"];
								}
								else if($val[csf("transaction_type")]==2  || $val[csf("transaction_type")]==3 || $val[csf("transaction_type")]==6)
								{
									echo $issue_num_arr[$val[csf('rec_issue_id')]]["issue_number"];
									//echo $val[csf('rec_issue_id')];
								}
									
							?>&nbsp;
							</p></td>
                            <td width="100"><p>
							<?
								
								if($val[csf("transaction_type")]==1  || $val[csf("transaction_type")]==4 || $val[csf("transaction_type")]==5) 
								{
									echo $receive_num_arr[$val[csf('rec_issue_id')]]["challan_no"];
								}
								else if($val[csf("transaction_type")]==2  || $val[csf("transaction_type")]==3 || $val[csf("transaction_type")]==6)
								{
									echo $issue_num_arr[$val[csf('rec_issue_id')]]["challan_no"];
									//echo $val[csf('rec_issue_id')];
								}
									
							?>&nbsp;
                            </p></td>
                            <td width="100" align="center"><p>
							<?
								if($val[csf("transaction_type")]==1  || $val[csf("transaction_type")]==4 || $val[csf("transaction_type")]==5) 
								{
									echo $supplier_arr[$receive_data_arr[$val[csf('rec_issue_id')]]["supplier_id"]];
								}
								else if($val[csf("transaction_type")]==2  || $val[csf("transaction_type")]==3 || $val[csf("transaction_type")]==6)
								{
									echo $supplier_arr[$issue_data_arr[$val[csf('rec_issue_id')]]["supplier_id"]];
								}
							?>&nbsp;
                            </p></td>
                            <td width="100" align="center"><p><? echo $val[csf("lot")]; ?>&nbsp;</p></td>
                            <td width="100" align="center"><p><? echo $yarn_count_arr[$val[csf("yarn_count_id")]]; ?>&nbsp;</p></td>
                            <td width="120"><p><?
							if($val[csf("yarn_comp_percent1st")]!=0) {$parcent1st="%";} else {$parcent1st="";}
							if($val[csf("yarn_comp_percent2nd")]!=0 ){ $parcent2nd="%";} else {$parcent2nd="";}
							 echo $composition[$val[csf("yarn_comp_type1st")]].$val[csf("yarn_comp_percent1st")].$parcent1st.$composition[$val[csf("yarn_comp_type2nd")]].$val[csf("yarn_comp_percent2nd")].$parcent2nd; 
							 ?>&nbsp;</p></td>
                            <td width="100"><p><? echo $yarn_type[$val[csf("yarn_type")]]; ?>&nbsp;</p></td>
                            <td width="100"><p><? echo $color_arr[$val[csf("color")]]; ?>&nbsp;</p></td>
							<?
							if($val[csf("transaction_type")]==1 || $val[csf("transaction_type")]==4 || $val[csf("transaction_type")]==5) 
							{
								?>
								<td width="100"><p><? 
								if($receive_data_arr[$val[csf("rec_issue_id")]]['entry_form']!=9){ if($receive_data_arr[$val[csf("rec_issue_id")]]['receive_basis']!=3) echo $receive_basis_arr[$receive_data_arr[$val[csf("rec_issue_id")]]['receive_basis']]; }?>&nbsp;</p></td>
								<td width="100"><p>
								<?
								 if($receive_data_arr[$val[csf("rec_issue_id")]]['entry_form']==9)
								 {
									 echo "Issue Return";
								 }
								 else
								 {
									 if($val[csf("receive_basis")]==1) echo $yarn_pi_num_arr[$receive_data_arr[$val[csf("rec_issue_id")]]['booking_id']];
									 if($val[csf("receive_basis")]==2){ if($receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose']==2) echo $yarn_dyeing_wo_arr[$receive_data_arr[$val[csf("rec_issue_id")]]['booking_id']];  else  echo $yarn_work_order_arr[$receive_data_arr[$val[csf("rec_issue_id")]]['booking_id']];}
								 }
								?>&nbsp;</p></td>
								<td width="100"><p><? echo $yarn_issue_purpose[$receive_data_arr[$val[csf("rec_issue_id")]]['receive_purpose']]; ?>&nbsp;</p></td>
								<?
							}
							else if($val[csf("transaction_type")]==2 || $val[csf("transaction_type")]==3 || $val[csf("transaction_type")]==6) 
							{
								?>
								<td width="100"><p><? echo $issue_basis[$issue_data_arr[$val[csf("rec_issue_id")]]['issue_basis']];?>&nbsp;</p></td>
								<td width="100"><p>
								<?
								//if($val[csf("receive_basis")]==1) echo $yarn_booking_arr[$issue_data_arr[$val[csf("rec_issue_id")]]['booking_id']];
								//if($val[csf("receive_basis")]==2) echo $yarn_work_order_arr[$issue_data_arr[$val[csf("rec_issue_id")]]['booking_id']];
								//if($val[csf("receive_basis")]==3) echo $yarn_requisition_arr[$issue_data_arr[$val[csf("rec_issue_id")]]['booking_id']];
								?>&nbsp;</p></td>
								<td width="100"><p><? echo $yarn_issue_purpose[$issue_data_arr[$val[csf("rec_issue_id")]]['issue_purpose']]; ?>&nbsp;</p></td>
								<?
							}
							if($rptType==2 || $rptType==1 )
							{
								?>
                                <td width="80" align="center"><p><? if($val[csf("transaction_type")]==1) echo $currency[$receive_data_arr[$val[csf('rec_issue_id')]]["currency_id"]]; ?></p></td>
                                <td width="80" align="right"><p><? if($val[csf("transaction_type")]==1) echo number_format($receive_data_arr[$val[csf('rec_issue_id')]]["exchange_rate"],2); ?></p></td>
                                <td width="80" align="right"><p><? if($val[csf("transaction_type")]==1) echo number_format($val[csf("order_rate")],4); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($val[csf("receive_qty")],2,".",""); $total_receive +=$val[csf("receive_qty")]; ?></p></td>
                                <td width="80" align="right"><p><? if($val[csf("transaction_type")]==1) $order_amt=$val[csf("order_qnty")]*$val[csf("order_rate")]; echo number_format($order_amt,4); $total_order_amt+=$order_amt; $order_amt=0; ?></p></td>
                                <?
							}
							if($rptType==3 || $rptType==1 )
							{
								?>
								<td align="right"  width="80"><p><? echo number_format($val[csf("issue_qty")],2,".",""); $total_issue +=$val[csf("issue_qty")]; ?></p></td>
                                <td align="right"  width="80"><p><? echo number_format($val[csf("return_qnty")],2,".",""); $total_return +=$val[csf("return_qnty")]; ?></p></td>
                                <?
							}
							?>
                            <td align="right"  width="80"><p><? echo number_format($val[csf("cons_rate")],2,".",""); ?></p></td>
                            <td align="right" width="100" style="padding-right:3px;"><p><? echo number_format($val[csf("cons_amount")],2,".",""); $totla_amount +=$val[csf("cons_amount")]; ?></p></td>
                            <td width="107"><p><? echo $user_name_arr[$val[csf("inserted_by")]]; ?>&nbsp;</p></td>
                            <td width="160"><p><? echo change_date_format($val[csf("insert_date")])." ".$val[csf("insert_time")]; ?>&nbsp;</p></td>
						</tr>
						<?
						$i++;
					}
					?>   
					</tbody>
				</table>
                <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
                    <tfoot>
                    	<th width="30">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="130">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100"><? if($rptType==3) echo "Total:"; ?></th>
                        <?
						if($rptType==2 || $rptType==1 )
						{
							?>
                            <th width="80" >&nbsp;</th>
                            <th width="80" >&nbsp;</th>
                            <th width="80" >Total:</th>
                        	<th width="80" id="value_total_receive"><? echo number_format($total_receive,2); ?></th>
                            <th width="80" id="value_total_order_amt"><? echo number_format($total_order_amt,2); ?></th>
                            <?
						}
						if($rptType==3 || $rptType==1 )
						{
							?>
                            <th width="80" id="value_total_issue"><? echo number_format($total_issue,2); ?></th>
                            <th width="80" id="value_total_return"><? echo number_format($total_return,2); ?></th>
                            <?
						}
						?>
                        <th width="80">&nbsp;</th>
                        <th id="value_totla_amount" width="100" style="padding-right:3px;"><? echo number_format($totla_amount,2); ?></th>
                        <th width="107">&nbsp;</th>
                        <th width="160">&nbsp;</th>
                    </tfoot> 
                </table>
			 </div>
        </fieldset>
		</div>    
			<?	 
	
	}
	
	
			
	foreach (glob("*.xls") as $filename) {
	if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$html**$filename**$cbo_item_cat**$rptType"; 
	exit();
	
}
disconnect($con);
?>

