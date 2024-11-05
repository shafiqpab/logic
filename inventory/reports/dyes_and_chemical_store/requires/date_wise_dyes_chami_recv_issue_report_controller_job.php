<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

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
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
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
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
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
	$txt_style_ref_id=str_replace("'","",$txt_style_ref_id);
	$txt_style_ref_no=str_replace("'","",$txt_style_ref_no);
	$cbo_year=str_replace("'","",$cbo_year);
	
	if($buyer!=0) $buyer_cond=" and b.buyer_name=$buyer"; else $buyer_cond="";
	if($txt_style_ref_id!="")
	{
		$style_cond=" and b.id in($txt_style_ref_id)";
	}
	else
	{
		if($txt_style_ref_no!="")
		{
			$style_cond=" and b.job_no like '%$txt_style_ref_no%'";
		}
		else
		{
			$style_cond="";
		}
	}
	//if($style_all!="") $style_cond="and b.id in($style_all)"; else $style_cond="";
	if($cbo_year!=0){ if($db_type==0) $year_cond=" and year(b.insert_date)='$cbo_year'"; else  $year_cond=" and to_char(b.insert_date,'YYYY')='$cbo_year'";}else {$year_cond="";}
	//echo $year_cond;die;
	
	$sql = "select a.id,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$select_year(b.insert_date $year_con) as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and b.company_name=$company and a.status_active=1 and b.status_active=1 $buyer_cond  $style_cond $year_cond "; 
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
	$rptType=str_replace("'","",$rptType);
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$txt_batch=str_replace("'","",$txt_batch);
	$cbo_year=str_replace("'","",$cbo_year);
	
	/*if($txt_order!="" || $txt_style_ref!="" || $txt_batch!="")
	{
		echo "jahid";die;
	}
	else
	{
		echo "Nahid";die;
	}
	*/
	if($rptType==3)
	{
		echo "Report Show later";die;
	}


	
	/*$receive_sql=sql_select("select id, recv_number, challan_no, supplier_id,knitting_source,knitting_company,currency_id,exchange_rate from  inv_receive_master where status_active=1 and is_deleted=0");
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
	
	$issue_sql=sql_select("select id, issue_number, challan_no,req_no,knit_dye_source,knit_dye_company from inv_issue_master where status_active=1 and is_deleted=0");
	foreach($issue_sql as $row)
	{
		$issue_num_arr[$row[csf("id")]]["id"]=$row[csf("id")];
		$issue_num_arr[$row[csf("id")]]["issue_number"]=$row[csf("issue_number")];
		$issue_num_arr[$row[csf("id")]]["challan_no"]=$row[csf("challan_no")];
		$issue_num_arr[$row[csf("id")]]["req_no"]=$row[csf("req_no")];
		$issue_num_arr[$row[csf("id")]]["knit_dye_source"]=$row[csf("knit_dye_source")];
		$issue_num_arr[$row[csf("id")]]["knit_dye_company"]=$row[csf("knit_dye_company")];
	}*/
	
	$style_order_id="";
	if($txt_style_ref!="")
	{
		
		if($db_type==0)
		 {
			 if($txt_style_ref_id!="") $job_cond=" and a.id in($txt_style_ref_id)"; else $job_cond=" and a.job_no_prefix_num in($txt_style_ref) and year(a.insert_date)='$cbo_year'";
			 $style_order_id=return_field_value("group_concat(b.id)  as po_id ","wo_po_details_master a,  wo_po_break_down b","a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $job_cond","po_id");
		 }
		 if($db_type==2)
		 {
			if($txt_style_ref_id!="") $job_cond=" and a.id in($txt_style_ref_id)"; else $job_cond=" and a.job_no_prefix_num in($txt_style_ref) and to_char(a.insert_date,'YYYY')='$cbo_year'";
			$style_order_id=return_field_value("listagg(cast(b.id as varchar(4000)),',') within group (order by b.id) as po_id ","wo_po_details_master a,  wo_po_break_down b","a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $job_cond","po_id");
		 }
	}
	$style_order_cond="";
	if($style_order_id!="") $style_order_cond=" and e.po_id in($style_order_id)";
	
	
	if($txt_order!="")
	{
		if($txt_order_id!="")
		{
			$style_order_cond .=" and e.po_id in($txt_order_id)";
		}
		else
		{
			$txt_order_id=return_field_value("id as po_id ","wo_po_break_down","po_number='$txt_order'","po_id");
			$style_order_cond .=" and e.po_id in($txt_order_id)";
		}
	}
	
	
	
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
		$insert_date_group="DATE_FORMAT(a.insert_date,'%d-%m-%Y')";
		$insert_time_group="DATE_FORMAT(a.insert_date,'%H:%i:%S')";
		//
		$select_recepi_id="group_concat(d.recipe_id)  as recipe_id";
		$select_po_id="group_concat(e.po_id)  as po_id";
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
		$insert_date_group="to_char(a.insert_date,'DD-MON-YYYY')";
		$insert_time_group="to_char(a.insert_date,'HH24:MI:SS')";
		$select_recepi_id="listagg(cast(d.recipe_id as varchar(4000)),',') within group (order by d.recipe_id) as recipe_id";
		$select_po_id="listagg(cast(e.po_id as varchar(4000)),',') within group (order by e.po_id) as po_id";
	}
	
	
	if($cbo_item_cat!=0) $item_cond=" and a.item_category=$cbo_item_cat"; else $item_cond=" and a.item_category in(5,6,7)";
	
	
	
	//echo $date_cond;die;
	
	
	if($rptType==1) 
	{
		$sql="select 
				a.id as trans_id,
				a.transaction_type,
				a.mst_id as rec_issue_id,
				a.transaction_date,
				a.item_category,
				a.cons_quantity as receive_qty,
				0 as issue_qty,
				a.cons_uom,
				b.id as prod_id,
				b.item_group_id,
				b.sub_group_name,
				b.item_description,
				b.item_code,
				a.cons_rate,
				a.cons_amount,
				a.inserted_by,
				$select_insert_date,
				$select_insert_time,
				a.order_qnty,
				a.order_rate,
				c.recv_number as recv_issue_num,
				c.booking_id,
				c.booking_no,
				c.challan_no as challan_no, 
				c.knitting_source as knitting_source, 
				c.knitting_company as knitting_company,
				c.receive_basis as receive_issue_basis,
				c.receive_purpose as receive_issue_purpose,
				null as req_no,
				null as batch_no,
				c.currency_id,
				c.exchange_rate,
				1 as type
			from
				inv_transaction a, product_details_master b, inv_receive_master c 
			where
				a.prod_id=b.id and a.mst_id=c.id and a.company_id=$cbo_company_name and a.transaction_type in(1,4) and c.entry_form in(4,29)  $item_cond $date_cond
			
			union all
			
			select 
				a.id as trans_id,
				a.transaction_type,
				a.mst_id as rec_issue_id,
				a.transaction_date,
				a.item_category,
				0 as receive_qty,
				a.cons_quantity as issue_qty,
				a.cons_uom,
				b.id as prod_id,
				b.item_group_id,
				b.sub_group_name,
				b.item_description,
				b.item_code,
				a.cons_rate,
				a.cons_amount,
				a.inserted_by,
				$select_insert_date,
				$select_insert_time,
				a.order_qnty,
				a.order_rate,
				c.issue_number as recv_issue_num,
				c.booking_id,
				c.booking_no,
				c.challan_no as challan_no,
				c.knit_dye_source as knitting_source,  
				c.knit_dye_company as knitting_company,
				c.issue_basis as receive_issue_basis,
				c.issue_purpose as receive_issue_purpose,
				c.req_no,
				c.batch_no,
				null as currency_id,
				null as exchange_rate,
				2 as type
			from
				inv_transaction a, product_details_master b, inv_issue_master c
			where
				a.prod_id=b.id and a.mst_id=c.id and a.company_id=$cbo_company_name and a.transaction_type in(2,3) and c.entry_form in(5,28)  $item_cond $date_cond
			
			union all
			
			select 
				a.id as trans_id,
				a.transaction_type,
				a.mst_id as rec_issue_id,
				a.transaction_date,
				a.item_category,
				a.cons_quantity as receive_qty,
				0 as issue_qty,
				a.cons_uom,
				b.id as prod_id,
				b.item_group_id,
				b.sub_group_name,
				b.item_description,
				b.item_code,
				a.cons_rate,
				a.cons_amount,
				a.inserted_by,
				$select_insert_date,
				$select_insert_time,
				a.order_qnty,
				a.order_rate,
				c.transfer_system_id as recv_issue_num,
				0 as booking_id,
				null as booking_no,
				c.challan_no as challan_no,
				0 as knitting_source,  
				0 as knitting_company, 
				0 as currency_id,
				0 as receive_issue_purpose,
				null as req_no,
				null as batch_no,
				null as currency_id,
				null as exchange_rate,
				3 as type
			from
				inv_transaction a, product_details_master b, inv_item_transfer_mst c 
			where
				a.prod_id=b.id and a.mst_id=c.id and a.company_id=$cbo_company_name and a.transaction_type in(5,6) and c.entry_form in(55)  $item_cond $date_cond
				
			order by transaction_date, trans_id";
	}
	else if($rptType==2)
	{
		$sql="select 
				a.id as trans_id,
				a.transaction_type,
				a.mst_id as rec_issue_id,
				a.transaction_date,
				a.item_category,
				a.cons_quantity as receive_qty,
				0 as issue_qty,
				a.cons_uom,
				b.id as prod_id,
				b.item_group_id,
				b.sub_group_name,
				b.item_description,
				b.item_code,
				a.cons_rate,
				a.cons_amount,
				a.inserted_by,
				$select_insert_date,
				$select_insert_time,
				a.order_qnty,
				a.order_rate,
				c.recv_number as recv_issue_num,
				c.booking_id,
				c.booking_no,
				c.challan_no, 
				c.knitting_source as knitting_source, 
				c.knitting_company as knitting_company,
				c.receive_basis as receive_issue_basis,
				c.receive_purpose as receive_issue_purpose,
				null as req_no,
				null as batch_no,
				c.currency_id,
				c.exchange_rate,
				1 as type
			from
				inv_transaction a, product_details_master b, inv_receive_master c 
			where
				a.prod_id=b.id and a.mst_id=c.id and a.company_id=$cbo_company_name and a.transaction_type in(1,4) and c.entry_form in(4,29)  $item_cond $date_cond
			
			union all
			
			select 
				a.id as trans_id,
				a.transaction_type,
				a.mst_id as rec_issue_id,
				a.transaction_date,
				a.item_category,
				a.cons_quantity as receive_qty,
				0 as issue_qty,
				a.cons_uom,
				b.id as prod_id,
				b.item_group_id,
				b.sub_group_name,
				b.item_description,
				b.item_code,
				a.cons_rate,
				a.cons_amount,
				a.inserted_by,
				$select_insert_date,
				$select_insert_time,
				a.order_qnty,
				a.order_rate,
				c.transfer_system_id as recv_issue_num,
				0 as booking_id,
				null as booking_no,
				c.challan_no,
				0 as knitting_source,  
				0 as knitting_company, 
				0 as currency_id,
				0 as receive_issue_purpose,
				null as req_no,
				null as batch_no,
				null as currency_id,
				null as exchange_rate,
				3 as type
			from
				inv_transaction a, product_details_master b, inv_item_transfer_mst c 
			where
				a.prod_id=b.id and a.mst_id=c.id and a.company_id=$cbo_company_name and a.transaction_type in(5) and c.entry_form in(55)  $item_cond $date_cond
				
			order by transaction_date, trans_id";
	}
	else if($rptType==3)
	{
		
		if($txt_order!="" || $txt_style_ref!="" || $txt_batch!="")
		{
			$sql="select 
					a.id as trans_id,
					a.transaction_type,
					a.mst_id as rec_issue_id,
					a.transaction_date,
					a.item_category,
					0 as receive_qty,
					a.cons_quantity as issue_qty,
					a.cons_uom,
					b.id as prod_id,
					b.item_group_id,
					b.sub_group_name,
					b.item_description,
					b.item_code,
					a.cons_rate,
					a.cons_amount,
					a.inserted_by,
					$select_insert_date,
					$select_insert_time,
					a.order_qnty,
					a.order_rate,
					c.issue_number as recv_issue_num,
					c.booking_id,
					c.booking_no,
					c.challan_no as challan_no,
					c.knit_dye_source as knitting_source,  
					c.knit_dye_company as knitting_company,
					c.issue_basis as receive_issue_basis,
					c.issue_purpose as receive_issue_purpose,
					c.req_no,
					c.batch_no,
					$select_recepi_id,
					2 as type
				from
					inv_transaction a, product_details_master b, inv_issue_master c,dyes_chem_issue_dtls d
				where
					a.prod_id=b.id and a.mst_id=c.id and c.id=d.mst_id and a.company_id=$cbo_company_name and a.transaction_type in(2,3) and c.entry_form in(5,28)  $item_cond $date_cond
				group by a.mst_id,c.issue_number,c.booking_id,c.booking_no,c.challan_no,c.knit_dye_source,c.knit_dye_company,c.issue_basis,c.issue_purpose,c.req_no,c.batch_no,a.id,a.transaction_type,a.transaction_date,a.item_category,a.cons_uom,a.cons_rate,a.cons_quantity,a.cons_amount,a.inserted_by,$insert_date_group,$insert_time_group,a.order_qnty,a.order_rate,b.id,b.item_group_id,b.sub_group_name,b.item_description,b.item_code
				
				union all
				
				select 
					a.id as trans_id,
					a.transaction_type,
					a.mst_id as rec_issue_id,
					a.transaction_date,
					a.item_category,
					a.cons_quantity as receive_qty,
					0 as issue_qty,
					a.cons_uom,
					b.id as prod_id,
					b.item_group_id,
					b.sub_group_name,
					b.item_description,
					b.item_code,
					a.cons_rate,
					a.cons_amount,
					a.inserted_by,
					$select_insert_date,
					$select_insert_time,
					a.order_qnty,
					a.order_rate,
					c.transfer_system_id as recv_issue_num,
					0 as booking_id,
					null as booking_no,
					c.challan_no as challan_no,
					0 as knitting_source,  
					0 as knitting_company, 
					0 as currency_id,
					0 as receive_issue_purpose,
					null as req_no,
					null as batch_no,
					null as recipe_id,
					3 as type
				from
					inv_transaction a, product_details_master b, inv_item_transfer_mst c 
				where
					a.prod_id=b.id and a.mst_id=c.id and a.company_id=$cbo_company_name and a.transaction_type in(6) and c.entry_form in(55)  $item_cond $date_cond
					
				order by transaction_date, trans_id";
		}
		else
		{
			$sql="select 
					a.id as trans_id,
					a.transaction_type,
					a.mst_id as rec_issue_id,
					a.transaction_date,
					a.item_category,
					0 as receive_qty,
					a.cons_quantity as issue_qty,
					a.cons_uom,
					b.id as prod_id,
					b.item_group_id,
					b.sub_group_name,
					b.item_description,
					b.item_code,
					a.cons_rate,
					a.cons_amount,
					a.inserted_by,
					$select_insert_date,
					$select_insert_time,
					a.order_qnty,
					a.order_rate,
					c.issue_number as recv_issue_num,
					c.booking_id,
					c.booking_no,
					c.challan_no as challan_no,
					c.knit_dye_source as knitting_source,  
					c.knit_dye_company as knitting_company,
					c.issue_basis as receive_issue_basis,
					c.issue_purpose as receive_issue_purpose,
					c.req_no,
					c.batch_no,
					2 as type
				from
					inv_transaction a, product_details_master b, inv_issue_master c
				where
					a.prod_id=b.id and a.mst_id=c.id and a.company_id=$cbo_company_name and a.transaction_type in(2,3) and c.entry_form in(5,28)  $item_cond $date_cond
				
				union all
				
				select 
					a.id as trans_id,
					a.transaction_type,
					a.mst_id as rec_issue_id,
					a.transaction_date,
					a.item_category,
					a.cons_quantity as receive_qty,
					0 as issue_qty,
					a.cons_uom,
					b.id as prod_id,
					b.item_group_id,
					b.sub_group_name,
					b.item_description,
					b.item_code,
					a.cons_rate,
					a.cons_amount,
					a.inserted_by,
					$select_insert_date,
					$select_insert_time,
					a.order_qnty,
					a.order_rate,
					c.transfer_system_id as recv_issue_num,
					0 as booking_id,
					null as booking_no,
					c.challan_no as challan_no,
					0 as knitting_source,  
					0 as knitting_company, 
					0 as currency_id,
					0 as receive_issue_purpose,
					null as req_no,
					null as batch_no,
					3 as type
				from
					inv_transaction a, product_details_master b, inv_item_transfer_mst c 
				where
					a.prod_id=b.id and a.mst_id=c.id and a.company_id=$cbo_company_name and a.transaction_type in(6) and c.entry_form in(55)  $item_cond $date_cond
					
				order by transaction_date, trans_id";	
		}
		
	}
	//echo $sql;die;
	
	if($rptType==1)
	{
		$table_width=2030;
		$div_width="2050px";
	}
	else if($rptType==2)
	{
		$table_width=1950;
		$div_width="1970px";
	}
	else if($rptType==3)
	{
		$table_width=2070;
		$div_width="2090px";
	}
	//echo $sql;	die;	
	$sql_result=sql_select($sql);
	//var_dump($sql_result);die;
	
	//$wo_num=return_library_array("select id, wo_number_prefix_num,requisition_no from wo_non_order_info_mst where item_category in(5,6,7)","id","wo_number_prefix_num");
	//$pi_num_arr=return_library_array( "select id, pi_number from com_pi_master_details where item_category_id in(5,6,7)",'id','pi_number');
	$requisiton_arr=return_library_array( "select id, requ_no from inv_purchase_requisition_mst where item_category_id in(5,6,7)",'id','requ_no');
	$batch_num_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
	
	$department_arr=return_library_array( "select id, department_name from lib_department",'id','department_name');
	$section_arr=return_library_array( "select id, section_name from lib_section",'id','section_name');
	$floor_arr=return_library_array( "select id, floor_name from  lib_prod_floor",'id','floor_name');
	$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');
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
						<th width="80">Basis</th>
						<th width="100">Req. No/ Batch NO/ PI/ WO</th>
						<?
						if($rptType==3)
						{
							?>
							<th width="80">Repecy No</th>
							<th width="80">Machine No</th>
                            <th width="70">Buyer</th>
                            <th width="50">Job</th>
                            <th width="80">Style</th>
                            <th width="80">Order</th>
							<?
						}
						?>
                        <th width="80">Category</th>
                        <th width="80">Purpose</th>
						<th width="80">Item Group</th>
						<th width="80">Item Sub-Group</th>
                        <th width="50">Item Code</th>
						<th width="120">Item Description</th>
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
						<td width="100" align="center"><p><? if($row[csf("transaction_date")]!="0000-00-00" && $row[csf("transaction_date")]!="") echo change_date_format($row[csf("transaction_date")]); else echo ""; ?>&nbsp;</p></td>
						<td width="130"  align="center"><p><? echo $row[csf("recv_issue_num")]; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $row[csf('challan_no')];?>&nbsp;</p></td>
						<td width="80"><p><? echo $receive_basis_arr[$row[csf("receive_issue_basis")]];?>&nbsp;</p></td>
						<td width="100"><p>
						<?
						if($row[csf("transaction_type")]==1)
						{
							if($row[csf("receive_issue_basis")]==1 || $row[csf("receive_issue_basis")]==2)
							{
								echo $row[csf("booking_no")];
							}
							else
							{
								echo "Independent";
							}
						}
						else if($row[csf("transaction_type")]==2)
						{
							if($row[csf("receive_issue_basis")]==5 )
							{
								echo $batch_num_arr[$row[csf("batch_no")]];
							}
							else if($row[csf("receive_issue_basis")]==7)
							{
								echo $requisiton_arr[$row[csf("req_no")]];
							}
							else
							{
								echo "Independent";
							}
						}
						else
						{
							echo "Issue Return";
						}
						?>&nbsp;</p></td>
						<?
						if($rptType==3)
						{
							?>
							<td width="80" align="center"><p>&nbsp;</p></td>
                            <td width="80" align="center"><p>&nbsp;</p></td>
                            <td width="70" align="center"><p>&nbsp;</p></td>
                            <td width="50" align="center"><p>&nbsp;</p></td>
                            <td width="80" align="center"><p>&nbsp;</p></td>
                            <td width="80" align="center"><p>&nbsp;</p></td>
							<?
						}
						?>
                        <td width="80" ><p><? echo $item_category[$row[csf('item_category')]]; ?>&nbsp;</p></td>
                        <td width="80" ><p><? echo $yarn_issue_purpose[$row[csf('receive_issue_purpose')]]; ?>&nbsp;</p></td>
						<td width="80" ><p><? echo $group_arr[$row[csf('item_group_id')]]; ?>&nbsp;</p></td>
						<td width="80" ><p><? echo $row[csf('sub_group_name')]; ?>&nbsp;</p></td>
                        <td width="50" ><p><? echo $row[csf('item_code')]; ?>&nbsp;</p></td>
						<td width="120"><p><? echo $row[csf('item_description')]; ?>&nbsp;</p></td>
						 <?
						if($rptType==2 || $rptType==1 )
						{
							?>
							<td width="80" align="center"><p><? if($row[csf("transaction_type")]==1) echo $currency[$row[csf('currency_id')]]; ?>&nbsp;</p></td>
							<td width="80" align="right"><p><? if($row[csf("transaction_type")]==1) echo number_format($row[csf('exchange_rate')],2); ?>&nbsp;</p></td>
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
                    	<th width="30" >&nbsp;</th>
						<th width="70" >&nbsp;</th>
						<th width="100" >&nbsp;</th>
						<th width="130" >&nbsp;</th>
						<th width="100" >&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<?
						if($rptType==3)
						{
							?>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="50">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="80">&nbsp;</th>
							<?
						}
						?>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
                        <th width="50">&nbsp;</th>
						<th width="120"><? if($rptType==3) echo "Total:"; else echo "&nbsp;"; ?></th>
						<?
						if($rptType==2 || $rptType==1 )
						{
							?>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">Total</th>
							<th width="80" id="value_total_receive"><? echo number_format($total_receive,2); ?></th>
							<th width="80"  id="value_total_order_amt"><? echo number_format($total_order_amt,2); ?></th>
							<?
						}
						if($rptType==3 || $rptType==1 )
						{
							?>
							<th  width="80" id="value_total_issue"><? echo number_format($total_issue,2); ?></th>
							<?
						}
						?>
						<th  width="80">&nbsp;</th>
						<th id="value_total_amount"  width="100"><? echo number_format($total_amount,2); ?></th>
						<th  width="110">&nbsp;</th>
						<th  width="160">&nbsp;</th>
					</tr>
				</tfoot> 
			</table>
		 </div>
	</fieldset>
	</div>    
	<?	 
			
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

