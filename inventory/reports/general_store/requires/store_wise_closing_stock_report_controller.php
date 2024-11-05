<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action = explode("__",$_REQUEST['action']);

//========== user credential start ========
$userCredential = sql_select("SELECT unit_id as company_id,store_location_id,item_cate_id FROM user_passwd where id=$user_id");
$cre_company_id = $userCredential[0][csf('company_id')];
$cre_store_location_id = $userCredential[0][csf('store_location_id')];
$cre_item_cate_id = $userCredential[0][csf('item_cate_id')];

if ($cre_company_id !='') {
    $company_credential_cond = " and comp.id in($cre_company_id)";
}
if ($cre_store_location_id !='') {
    $store_location_credential_cond = " and a.id in($cre_store_location_id)"; 
}
if($cre_item_cate_id !='') {
    $item_cate_credential_cond = $cre_item_cate_id ;  
}
//========== user credential end ==========


if ($action[0]=="load_drop_down_store" || $action[1]=="load_drop_down_buyer")
{
    extract($_REQUEST);
    $choosenCompany = $choosenCompany;  
	echo create_drop_down( "cbo_store_name", 130, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id in ($choosenCompany) and  b.category_type in(4,8,9,10,11,15,16,17,18,19,20,21,22) $store_location_credential_cond group by a.id,a.store_name","id,store_name", "", "--Select Store--", 0, "")."**".create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($choosenCompany) and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", "", "-- Select Buyer --", $selected, "",1);
	exit();
}

//item group search------------------------------//
if($action[0]=="item_group_such_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	//echo $style_id;die;

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
		
    </script>
    <?
	$company=str_replace("'","",$company);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$txt_item_group=str_replace("'","",$txt_item_group);
	$txt_item_group_id=str_replace("'","",$txt_item_group_id);
	$txt_item_group_no=str_replace("'","",$txt_item_group_no);
	$sql="SELECT id,item_name from  lib_item_group where item_category in(4) and status_active=1 and is_deleted=0";
	//echo $sql; die;
	$arr=array();
	echo create_list_view("list_view", "Item Group Name","250","300","300",0, $sql , "js_set_value", "id,item_name", "", 1, "0", $arr, "item_name", "","setFilterGrid('list_view',-1)","0","",1);
		
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	
	?>
    <script language="javascript" type="text/javascript">
	var txt_item_group_no='<? echo $txt_item_group_no;?>';
	var txt_item_group_id='<? echo $txt_item_group_id;?>';
	var txt_item_group='<? echo $txt_item_group;?>';
	//alert(style_id);
	if(txt_item_group_no!="")
	{
		item_group_no_arr=txt_item_group_no.split(",");
		item_group_id_arr=txt_item_group_id.split(",");
		item_group_arr=txt_item_group.split(",");
		var item_group="";
		for(var k=0;k<item_group_no_arr.length; k++)
		{
			item_group=item_group_no_arr[k]+'_'+item_group_id_arr[k]+'_'+item_group_arr[k];
			js_set_value(item_group);
		}
	}
	</script>
    
    <?
	exit();
}

//item group search------------------------------//
if($action[0]=="item_account_such_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	//echo $style_id;die;
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
		
    </script>
    <?
	$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$company=str_replace("'","",$company);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$txt_item_acc=str_replace("'","",$txt_item_acc);
	$txt_item_account_id=str_replace("'","",$txt_item_account_id);
	$txt_item_acc_no=str_replace("'","",$txt_item_acc_no);
	$sql="SELECT id,item_account,item_category_id,item_group_id,item_description,supplier_id from  product_details_master where item_category_id in(4) and status_active=1 and is_deleted=0"; 
	//echo $sql; die;
	$arr=array(1=>$general_item_category,2=>$itemgroupArr,4=>$supplierArr);
	echo  create_list_view("list_view", "Item Account,Item Category,Item Group,Item Description,Supplier,Product ID", "70,110,130,150,100","720","320",0, $sql , "js_set_value", "id,item_description", "", 1, "0,item_category_id,item_group_id,0,supplier_id,0", $arr , "item_account,item_category_id,item_group_id,item_description,supplier_id,id", "",'setFilterGrid("list_view",-1);','0,0,0,0,0,0','',1) ;
		
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	
	?>
    <script language="javascript" type="text/javascript">
	var item_acc_no_arr=item_acc_id_arr=item_acc_arr=new Array();
	var txt_item_acc_no='<? echo $txt_item_acc_no;?>';
	var txt_item_account_id='<? echo $txt_item_account_id;?>';
	var txt_item_acc='<? echo $txt_item_acc;?>';
	//alert(txt_item_acc_no);
	if(txt_item_acc_no !="")
	{
		item_acc_no_arr=txt_item_acc_no.split(",");
		item_acc_id_arr=txt_item_account_id.split(",");
		item_acc_arr=txt_item_acc.split(",");
		var item_account="";
		for(var k=0;k<item_acc_no_arr.length; k++)
		{
			item_account=item_acc_no_arr[k]+'_'+item_acc_id_arr[k]+'_'+item_acc_arr[k];
			js_set_value(item_account);
		}
	}
	</script>
    
    <?
	
	exit();
}


if($action[0]=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);    
	$item_group_id=str_replace("'","",$item_group_id);
	$item_account_id=str_replace("'","",$item_account_id);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id); 
	$value_with=str_replace("'","",$cbo_value_with);
	$cbo_get_upto_qnty=str_replace("'","",$cbo_get_upto_qnty); 
	$txt_qnty=str_replace("'","",$txt_qnty);
	
	$str_cond="";$zero_cond="";
	if ($cbo_company_name!=0) $str_cond =" and a.company_id in($cbo_company_name)";
	if ($item_group_id!="") $str_cond .=" and a.item_group_id in($item_group_id)";
	if ($item_account_id!="") $str_cond .=" and a.id in ($item_account_id)";
	if ($cbo_store_name!=0) $str_cond .=" and b.store_id in($cbo_store_name)";
	if ($cbo_buyer_id!=0) $str_cond .=" and e.buyer_name in($cbo_buyer_id)";

	if($db_type==0) $select_from_date=change_date_format($from_date,'yyyy-mm-dd');
	if($db_type==2) $select_from_date=change_date_format($from_date,'','',1);
	
	if($db_type==0) $select_to_date=change_date_format($to_date,'yyyy-mm-dd');
	if($db_type==2) $select_to_date=change_date_format($to_date,'','',1);
	//if($value_with==0) $zero_cond .=""; else $zero_cond .= "  and a.current_stock>0";
	
	//echo $str_cond; die;
	
	
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name"); 
	$store_name_arr=return_library_array("select id, store_name from  lib_store_location","id","store_name");
	//$itemgroupArr = return_library_array("select id,item_name from lib_item_group where status_active=1 and is_deleted=0 and ITEM_CATEGORY=4","id","item_name");
	//$conversion_factor_arr = return_library_array("select id,conversion_factor from lib_item_group where status_active=1 and is_deleted=0","id","conversion_factor");
	$item_group_sql=sql_select("select ID, ITEM_NAME, CONVERSION_FACTOR from lib_item_group where status_active=1 and is_deleted=0 and ITEM_CATEGORY=4");
	foreach($item_group_sql as $val)
	{
		$itemgroupArr[$val["ID"]]=$val["ITEM_NAME"];
		$conversion_factor_arr[$val["ID"]]=$val["CONVERSION_FACTOR"];
	}
	unset($item_group_sql);
	$color_name_arr=return_library_array("select id, color_name from  lib_color","id","color_name");
				
	if($cbo_search_by==1)  
	{
		$div_width="1550";
		$table_width="1530";
		
		$sql="Select e.buyer_name, b.prod_id, b.store_id, a.item_category_id, a.item_group_id, a.avg_rate_per_unit, a.item_description, a.item_color, a.item_size, b.cons_uom as unit_of_measure, 
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (1,4,5) and c.trans_type in (1,4,5)  then c.quantity else 0 end) as opening_total_receive,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (1,4,5) and c.trans_type in (1,4,5)  then (c.quantity*c.order_rate) else 0 end) as opening_rcv_amt,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (2,3,6) and c.trans_type in (2,3,6)  then c.quantity else 0 end) as opening_total_issue,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (2,3,6) and c.trans_type in (2,3,6)  then (c.quantity*c.order_rate) else 0 end) as opening_issue_amt,
		sum(case when b.transaction_type=1 and c.trans_type=1 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as receive,
		sum(case when b.transaction_type=1 and c.trans_type=1 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as receive_amt,
		sum(case when b.transaction_type=4 and c.trans_type=4 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as issue_return,
		sum(case when b.transaction_type=4 and c.trans_type=4 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as issue_return_amt,
		sum(case when b.transaction_type=5 and c.trans_type=5 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as trans_in,
		sum(case when b.transaction_type=5 and c.trans_type=5 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as trans_in_amt,		
		sum(case when b.transaction_type=2 and c.trans_type=2 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as issue,
		sum(case when b.transaction_type=2 and c.trans_type=2 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as issue_amt,
		sum(case when b.transaction_type=3 and c.trans_type=3 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as receive_return,
		sum(case when b.transaction_type=3 and c.trans_type=3 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as receive_return_amt,		
		sum(case when b.transaction_type=6  and c.trans_type=6 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as trans_out,
		sum(case when b.transaction_type=6  and c.trans_type=6 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as trans_out_amt 
		
		from product_details_master a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e
		where a.id=b.prod_id and b.id=c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and b.transaction_type in (1,2,3,4,5,6) and c.trans_type in (1,2,3,4,5,6) and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form in(24,25,49,73,78,112) and b.transaction_date<='".$select_to_date."' $str_cond $zero_cond
		group by e.buyer_name, b.store_id, b.prod_id, a.item_category_id, a.item_group_id, a.avg_rate_per_unit, a.item_description, a.item_color, a.item_size, b.cons_uom
		order by e.buyer_name, b.prod_id";
		
    } 
	elseif($cbo_search_by==2)  
	{
		 $div_width="1430";
		 $table_width="1410";
		
		$sql="Select b.prod_id, b.store_id, a.item_category_id, a.item_group_id, a.avg_rate_per_unit, a.item_description, a.item_color, a.item_size, b.cons_uom as unit_of_measure, 
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (1,4,5) and c.trans_type in (1,4,5)  then c.quantity else 0 end) as opening_total_receive,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (1,4,5) and c.trans_type in (1,4,5)  then (c.quantity*c.order_rate) else 0 end) as opening_rcv_amt,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (2,3,6) and c.trans_type in (2,3,6)  then c.quantity else 0 end) as opening_total_issue,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (2,3,6) and c.trans_type in (2,3,6)  then (c.quantity*c.order_rate) else 0 end) as opening_issue_amt,
		sum(case when b.transaction_type=1 and c.trans_type=1 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as receive,
		sum(case when b.transaction_type=1 and c.trans_type=1 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as receive_amt,
		sum(case when b.transaction_type=4 and c.trans_type=4 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as issue_return,
		sum(case when b.transaction_type=4 and c.trans_type=4 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as issue_return_amt,
		sum(case when b.transaction_type=5 and c.trans_type=5 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as trans_in,
		sum(case when b.transaction_type=5 and c.trans_type=5 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as trans_in_amt,		
		sum(case when b.transaction_type=2 and c.trans_type=2 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as issue,
		sum(case when b.transaction_type=2 and c.trans_type=2 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as issue_amt,
		sum(case when b.transaction_type=3 and c.trans_type=3 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as receive_return,
		sum(case when b.transaction_type=3 and c.trans_type=3 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as receive_return_amt,		
		sum(case when b.transaction_type=6  and c.trans_type=6 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as trans_out,
		sum(case when b.transaction_type=6  and c.trans_type=6 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as trans_out_amt 
		
		from product_details_master a, inv_transaction b, order_wise_pro_details c
		where a.id=b.prod_id and b.id=c.trans_id and b.transaction_type in (1,2,3,4,5,6) and c.trans_type in (1,2,3,4,5,6) and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form in(24,25,49,73,78,112) and b.transaction_date<='".$select_to_date."'  $str_cond $zero_cond
		group by  b.store_id, b.prod_id, a.item_category_id, a.item_group_id, a.avg_rate_per_unit, a.item_description, a.item_color, a.item_size, b.cons_uom";
	} 
	elseif($cbo_search_by==3)  
	{
		$sql="Select e.buyer_name, b.prod_id, b.store_id, a.item_category_id, a.item_group_id, a.avg_rate_per_unit, a.item_description, a.item_color, a.item_size, b.cons_uom as unit_of_measure, 
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (1,4,5) and c.trans_type in (1,4,5)  then c.quantity else 0 end) as opening_total_receive,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (1,4,5) and c.trans_type in (1,4,5)  then (c.quantity*c.order_rate) else 0 end) as opening_rcv_amt,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (2,3,6) and c.trans_type in (2,3,6)  then c.quantity else 0 end) as opening_total_issue,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (2,3,6) and c.trans_type in (2,3,6)  then (c.quantity*c.order_rate) else 0 end) as opening_issue_amt,
		sum(case when b.transaction_type=1 and c.trans_type=1 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as receive,
		sum(case when b.transaction_type=1 and c.trans_type=1 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as receive_amt,
		sum(case when b.transaction_type=4 and c.trans_type=4 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as issue_return,
		sum(case when b.transaction_type=4 and c.trans_type=4 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as issue_return_amt,
		sum(case when b.transaction_type=5 and c.trans_type=5 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as trans_in,
		sum(case when b.transaction_type=5 and c.trans_type=5 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as trans_in_amt,		
		sum(case when b.transaction_type=2 and c.trans_type=2 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as issue,
		sum(case when b.transaction_type=2 and c.trans_type=2 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as issue_amt,
		sum(case when b.transaction_type=3 and c.trans_type=3 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as receive_return,
		sum(case when b.transaction_type=3 and c.trans_type=3 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as receive_return_amt,		
		sum(case when b.transaction_type=6  and c.trans_type=6 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as trans_out,
		sum(case when b.transaction_type=6  and c.trans_type=6 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as trans_out_amt 
		
		from product_details_master a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e
		where a.id=b.prod_id and b.id=c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and b.transaction_type in (1,2,3,4,5,6) and c.trans_type in (1,2,3,4,5,6) and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form in(24,25,49,73,78,112) and b.transaction_date<='".$select_to_date."' $str_cond $zero_cond
		group by e.buyer_name, b.store_id, b.prod_id, a.item_category_id, a.item_group_id, a.avg_rate_per_unit, a.item_description, a.item_color, a.item_size, b.cons_uom
		order by e.buyer_name, b.prod_id";
		 
	} 
	elseif($cbo_search_by==4)  
	{
		$div_width="1400";
		$table_width="1380";
		 
		$sql="Select b.store_id, b.prod_id, a.item_category_id, a.item_group_id, a.avg_rate_per_unit, a.item_description, b.cons_uom as unit_of_measure, a.item_color, a.item_size,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in(1,4)  then b.cons_quantity else 0 end) as opening_total_receive,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in(1,4)  then b.cons_amount else 0 end) as opening_rcv_amt,
		0 as opening_total_issue,
		0 as opening_issue_amt,
		sum(case when b.transaction_type=1 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then b.cons_quantity else 0 end) as receive,
		sum(case when b.transaction_type=1 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then b.cons_amount else 0 end) as receive_amt,
		sum(case when b.transaction_type=4 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then b.cons_quantity else 0 end) as issue_return,
		sum(case when b.transaction_type=4 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then b.cons_amount else 0 end) as issue_return_amt,
		0 as trans_in,
		0 as trans_in_amt,
		0 as issue,
		0 as issue_amt,
		0 as receive_return,
		0 as receive_return_amt,
		0 as trans_out,
		0 as trans_out_amt, 
		1 as type
		
		from  product_details_master a, inv_transaction b, inv_receive_master c
		where a.id=b.prod_id and b.mst_id=c.id and b.transaction_type in (1,4) and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form in(20,28)  $str_cond $zero_cond
		group by b.store_id, b.prod_id, a.item_category_id, a.item_group_id, a.avg_rate_per_unit, a.item_description, b.cons_uom, a.item_color, a.item_size
		
		union all
		
		Select b.store_id, b.prod_id, a.item_category_id, a.item_group_id, a.avg_rate_per_unit, a.item_description, b.cons_uom as unit_of_measure, a.item_color, a.item_size,
		0 as opening_total_receive,
		0 as opening_rcv_amt,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in(2,3) then b.cons_quantity else 0 end) as opening_total_issue,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in(2,3) then b.cons_amount else 0 end) as opening_issue_amt,
		0 as receive,
		0 as receive_amt,
		0 as issue_return,
		0 as issue_return_amt,
		0 as trans_in,
		0 as trans_in_amt,
		sum(case when b.transaction_type=2 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then b.cons_quantity else 0 end) as issue,
		sum(case when b.transaction_type=2 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then b.cons_amount else 0 end) as issue_amt,
		sum(case when b.transaction_type=3 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then b.cons_quantity else 0 end) as receive_return,
		sum(case when b.transaction_type=3 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then b.cons_amount else 0 end) as receive_return_amt,
		0 as trans_out,
		0 as trans_out_amt, 
		2 as type
		
		from  product_details_master a, inv_transaction b, inv_issue_master c
		where a.id=b.prod_id and b.mst_id=c.id and b.transaction_type in (2,3) and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form in(21,26)  $str_cond $zero_cond
		group by b.store_id, b.prod_id, a.item_category_id, a.item_group_id, a.avg_rate_per_unit, a.item_description, b.cons_uom, a.item_color, a.item_size
		
		union all
		
		Select b.store_id, b.prod_id, a.item_category_id, a.item_group_id, a.avg_rate_per_unit, a.item_description, b.cons_uom as unit_of_measure, a.item_color, a.item_size,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (5)  then b.cons_quantity else 0 end) as opening_total_receive,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (5)  then b.cons_amount else 0 end) as opening_rcv_amt,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (6) then b.cons_quantity else 0 end) as opening_total_issue,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (6) then b.cons_amount else 0 end) as opening_issue_amt,
		0 as receive,
		0 as receive_amt,
		0 as issue_return,
		0 as issue_return_amt, 
		sum(case when b.transaction_type=5 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then b.cons_quantity else 0 end) as trans_in,
		sum(case when b.transaction_type=5 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then b.cons_amount else 0 end) as trans_in_amt,
		0 as issue,
		0 as issue_amt,
		0 as receive_return,
		0 as receive_return_amt,
		sum(case when b.transaction_type=6  and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then b.cons_quantity else 0 end) as trans_out,
		sum(case when b.transaction_type=6  and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then b.cons_amount else 0 end) as trans_out_amt,
		3 as type
		
		from  product_details_master a, inv_transaction b, inv_item_transfer_mst c
		where a.id=b.prod_id and b.mst_id=c.id and b.transaction_type in (5,6) and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form in(57) and b.transaction_date<='".$select_to_date."' $str_cond $zero_cond
		group by b.store_id, b.prod_id, a.item_category_id, a.item_group_id, a.avg_rate_per_unit, a.item_description, b.cons_uom, a.item_color, a.item_size";
	}
	
	//echo $sql;//die;
	
	$result = sql_select($sql);
	$store_wise_data=$store_data=array();
	if($cbo_search_by==3) 
	{	
		$storeIdArr = array();
		foreach($result as $storeRow)
		{
			$item_color_name=$item_size_name="";
			if($storeRow[csf('item_color')]>0) $item_color_name=", ".$color_name_arr[$storeRow[csf('item_color')]];
			if($storeRow[csf('item_size')]!="") $item_size_name=", ".$storeRow[csf('item_size')];		
			$storeIdArr[$storeRow[csf('store_id')]] = $storeRow[csf('store_id')];
			$store_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]]["item_group_id"] = $storeRow[csf('item_group_id')];
			$store_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]]["item_description"] = $storeRow[csf('item_description')]." ".$item_color_name." ".$item_size_name;
			$store_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]]["avg_rate_per_unit"] = $storeRow[csf('avg_rate_per_unit')];
			$store_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]]["unit_of_measure"] = $storeRow[csf('unit_of_measure')];
			$store_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]]["opening_total_receive"] += $storeRow[csf('opening_total_receive')]*$conversion_factor_arr[$storeRow[csf('item_group_id')]];
			$store_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]]["opening_rcv_amt"] += $storeRow[csf('opening_rcv_amt')];
			$store_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]]["opening_total_issue"] += $storeRow[csf('opening_total_issue')]*$conversion_factor_arr[$storeRow[csf('item_group_id')]];
			$store_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]]["opening_issue_amt"] += $storeRow[csf('opening_issue_amt')];
			
			$store_wise_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["receive"] += $storeRow[csf('receive')]*$conversion_factor_arr[$storeRow[csf('item_group_id')]];
			$store_wise_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["receive_amt"] += $storeRow[csf('receive_amt')];
			$store_wise_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["issue_return"] += $storeRow[csf('issue_return')]*$conversion_factor_arr[$storeRow[csf('item_group_id')]];
			$store_wise_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["issue_return_amt"] += $storeRow[csf('issue_return_amt')];
			$store_wise_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["trans_in"] += $storeRow[csf('trans_in')]*$conversion_factor_arr[$storeRow[csf('item_group_id')]];
			$store_wise_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["trans_in_amt"] += $storeRow[csf('trans_in_amt')];
			$store_wise_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["issue"] += $storeRow[csf('issue')]*$conversion_factor_arr[$storeRow[csf('item_group_id')]];
			$store_wise_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["issue_amt"] += $storeRow[csf('issue_amt')];
			$store_wise_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["receive_return"] += $storeRow[csf('receive_return')]*$conversion_factor_arr[$storeRow[csf('item_group_id')]];
			$store_wise_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["receive_return_amt"] += $storeRow[csf('receive_return_amt')];
			$store_wise_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["trans_out"] += $storeRow[csf('trans_out')]*$conversion_factor_arr[$storeRow[csf('item_group_id')]];
			$store_wise_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["trans_out_amt"] += $storeRow[csf('trans_out_amt')];
			
		}
		unset($result);
	}
	
	$general_ac_data=array();
	
	if($cbo_search_by==4) 
	{	
		$storeIdArr = array();
		foreach($result as $storeRow)
		{		
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["prod_id"] = $storeRow[csf('prod_id')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["store_id"] = $storeRow[csf('store_id')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["item_category_id"] = $storeRow[csf('item_category_id')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["item_group_id"] = $storeRow[csf('item_group_id')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["avg_rate_per_unit"] = $storeRow[csf('avg_rate_per_unit')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["item_description"] = $storeRow[csf('item_description')]." ".$color_name_arr[$storeRow[csf('item_color')]]." ".$storeRow[csf('item_size')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["unit_of_measure"] = $storeRow[csf('unit_of_measure')];
			
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["opening_total_receive"] += $storeRow[csf('opening_total_receive')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["opening_rcv_amt"] += $storeRow[csf('opening_rcv_amt')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["opening_total_issue"] += $storeRow[csf('opening_total_issue')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["opening_issue_amt"] += $storeRow[csf('opening_issue_amt')];
			
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["receive"] += $storeRow[csf('receive')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["receive_amt"] += $storeRow[csf('receive_amt')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["issue_return"] += $storeRow[csf('issue_return')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["issue_return_amt"] += $storeRow[csf('issue_return_amt')];
			
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["trans_in"] += $storeRow[csf('trans_in')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["trans_in_amt"] += $storeRow[csf('trans_in_amt')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["issue"] += $storeRow[csf('issue')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["issue_amt"] += $storeRow[csf('issue_amt')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["receive_return"] += $storeRow[csf('receive_return')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["receive_return_amt"] += $storeRow[csf('receive_return_amt')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["trans_out"] += $storeRow[csf('trans_out')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["trans_out_amt"] += $storeRow[csf('trans_out_amt')];
			
		}
		unset($result);
	}
	
	if($cbo_search_by==3)
	{
		$div_width=750+(count($storeIdArr)*240);
		$table_width=750+(count($storeIdArr)*240);
	}

	$i=1;
	ob_start();	
	?>
    <div align="left"> 
        <table style="width:<? echo $table_width; ?>px" border="0">
            <tr class="form_caption" style="border:none;">
                <td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td> 
            </tr>
            
            <tr style="border:none;">
                <td colspan="20" align="center" style="border:none; font-size:14px;">
                   <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>                               
                </td>
            </tr>
            
            <tr style="border:none;">
                <td colspan="20" align="center" style="border:none;font-size:12px; font-weight:bold">
                    <? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
                </td>
            </tr>
        </table> 
                
               
               
                <? 
                if($cbo_search_by==1 || $cbo_search_by==2) 
                {
					?>
                    <table style="width:<? echo $table_width; ?>px" border="1" cellpadding="0" cellspacing="0" class="rpt_table" id="table_header_1" rules="all" align="left"> 
                        <thead> 

                        <tr>
                            <th rowspan="2" width="40">SL</th>
                            <th rowspan="2" width="60">Prod.ID</th>
                            
                            <? if($cbo_search_by==1)  {?>
                            <th rowspan="2" width="120">Buyer</th>
                            <? } ?>
                            
                            <th colspan="2" width="280">Description</th>
                            <th rowspan="2" width="120">Store Name</th>
                            <th rowspan="2" width="60">UOM</th>
                            <th rowspan="2" width="80">Opening Stock</th>
                            <th colspan="4" width="320">Receive</th>  
                            <th colspan="4" width="320">Issue</th> 
                            <th rowspan="2" >Closing Stock</th>
                        </tr> 
					
                        <tr>
                            <th width="100">Item Group</th>
                            <th width="180">Item Description</th>
                            
                            <th width="80">Receive</th>
                            <th width="80">Issue Return</th>
                            <th width="80">Transfer In</th>
                            <th width="80">Total Receive</th>
                            
                            <th width="80">Issue</th>
                            <th width="80">Received Return</th>
                            <th width="80">Transfer Out</th>
                            <th width="80">Total Issue</th>
                        </tr>  
					</thead>
					</table>
					
					<div style="width:<? echo $div_width; ?>px; max-height:350px; overflow-y:scroll;" id="scroll_body"  > 
					<table style="width:<? echo $table_width; ?>px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
					<?
					$grandTotalOpeningStockQty = 0;
					$grandTotalAmount = 0;
					$grandTotalReceive = 0;
					$grandTotalIssueReturn = 0;
					$grandTotalTransferIn = 0;
					$grandTotalTotalReceive = 0;
					$grandTotalTotalTotalReceiveAmount = 0;
					$grandTotalIssue = 0;
					$grandTotalReceiveReturn = 0;
					$grandTotalTransOut=0;
					$grandTotalTotalIssue = 0;
					$grandTotalTotalIssueAmount=0;
					$grandTotalClosingStock = 0;
					$grandTotalClosingStockAmount = 0;
					foreach($result as $row)
					{
						
						if( $row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==4 ) 
						$stylecolor='style="color:#A61000"';
						else
						$stylecolor='style="color:#000000"';
						$openingStockQty = (($row[csf("opening_total_receive")]-$row[csf("opening_total_issue")])*$conversion_factor_arr[$row[csf('item_group_id')]]);
						$openingStockAmt = (($row[csf("opening_rcv_amt")]-$row[csf("opening_issue_amt")]));
						$totalReceive = (($row[csf("receive")]+$row[csf("issue_return")]+$row[csf("trans_in")])*$conversion_factor_arr[$row[csf('item_group_id')]]);
						$totalReceiveAmount = (($row[csf("receive_amt")]+$row[csf("issue_return_amt")]+$row[csf("trans_in_amt")]));
						$totalIssue = (($row[csf("issue")]+$row[csf("receive_return")]+$row[csf("trans_out")])*$conversion_factor_arr[$row[csf('item_group_id')]]);
						$totalIssueAmount = (($row[csf("issue_amt")]+$row[csf("receive_return_amt")]+$row[csf("trans_out_amt")]));
						$closingStock = (($openingStockQty+$totalReceive)-$totalIssue);
						$closingStockAmount = (($openingStockAmt+$totalReceiveAmount)-$totalIssueAmount);;
						
						$get_up_qnty_check=0;
						if($cbo_get_upto_qnty==0)
						{
							$get_up_qnty_check=1;
						}
						else if($cbo_get_upto_qnty==1 && $closingStock>$txt_qnty)
						{
							$get_up_qnty_check=1;
						}
						else if($cbo_get_upto_qnty==2 && $closingStock<$txt_qnty)
						{
							$get_up_qnty_check=1;
						}
						else if($cbo_get_upto_qnty==3 && $closingStock>=$txt_qnty)
						{
							$get_up_qnty_check=1;
						}
						else if($cbo_get_upto_qnty==4 && $closingStock<=$txt_qnty)
						{
							$get_up_qnty_check=1;
						}
						else if($cbo_get_upto_qnty==5 && $closingStock=$txt_qnty)
						{
							$get_up_qnty_check=1;
						}
						
						$item_color_name=$item_size_name="";
						if($row[csf('item_color')]>0) $item_color_name=", ".$color_name_arr[$row[csf('item_color')]];
						if($row[csf('item_size')]!="") $item_size_name=", ".$row[csf('item_size')];
						if($value_with ==1 && (number_format($openingStockQty,2,".","") >0 || number_format($totalReceive,2,".","") >0 || number_format($totalIssue,2,".","") >0 || number_format($closingStock,2,".","") >0) && $get_up_qnty_check==1 )
						{
							$grandTotalOpeningStockQty +=$openingStockQty;
							$grandTotalAmount +=$openingStockAmt; 
							$grandTotalReceive +=$row[csf("receive")]*$conversion_factor_arr[$row[csf('item_group_id')]];  
							$grandTotalIssueReturn +=$row[csf("issue_return")]*$conversion_factor_arr[$row[csf('item_group_id')]];
							$grandTotalTransferIn +=$row[csf("trans_in")]*$conversion_factor_arr[$row[csf('item_group_id')]];
							$grandTotalTotalReceive +=$totalReceive; 
							$grandTotalTotalTotalReceiveAmount += $totalReceiveAmount;
							$grandTotalIssue +=$row[csf("issue")]*$conversion_factor_arr[$row[csf('item_group_id')]];
							$grandTotalReceiveReturn +=$row[csf("receive_return")]*$conversion_factor_arr[$row[csf('item_group_id')]];
							$grandTotalTransOut +=$row[csf("trans_out")]*$conversion_factor_arr[$row[csf('item_group_id')]];
							$grandTotalTotalIssue +=$totalIssue;
							$grandTotalTotalIssueAmount +=$totalIssueAmount;
							$grandTotalClosingStock +=$closingStock;
							$grandTotalClosingStockAmount +=$closingStockAmount;
							
							if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
							?>
                            <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">    
                                <td width="40"><? echo $i;?></td> 
                                <td width="60"><p><? echo $row[csf('prod_id')];?></p></td>
                                <? if($cbo_search_by==1)  {?>                     
                                <td width="120" style="word-break: break-all;"><p><? echo $buyerArr[$row[csf("buyer_name")]]; ?></p></td> 
                                <? } ?>
                                <td width="100" style="word-break: break-all;"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?></p></td>
                                <td width="180" style="word-break: break-all;"><p><? echo $row[csf("item_description")]." ".$item_color_name." ".$item_size_name; ?></p></td> 
                                <td width="120" style="word-break: break-all;"><p><? echo $store_name_arr[$row[csf("store_id")]]; ?></p></td>                     
                                <td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td> 			   
                                <td width="80" align="right" title="<? echo $openingStockQty."ja"; ?>"><p><? echo number_format($openingStockQty,2); ?></p></td>                   
                                <td width="80" align="right"><p><? echo number_format($row[csf("receive")]*$conversion_factor_arr[$row[csf('item_group_id')]],2); ?></p></td> 
                                <td width="80" align="right"><p><? echo number_format($row[csf("issue_return")]*$conversion_factor_arr[$row[csf('item_group_id')]],2); ?></p></td>
                                <td width="80" align="right"><p><? echo number_format($row[csf("trans_in")]*$conversion_factor_arr[$row[csf('item_group_id')]],2); ?></p></td>  
                                <td width="80" align="right"><p><? echo number_format($totalReceive,2); ?></p></td>
                                <td width="80" align="right"><p><? echo number_format($row[csf("issue")]*$conversion_factor_arr[$row[csf('item_group_id')]],2); ?></p></td>  
                                <td width="80" align="right"><p><? echo number_format($row[csf("receive_return")]*$conversion_factor_arr[$row[csf('item_group_id')]],2); ?></p></td>
                                <td width="80" align="right"><p><? echo number_format($row[csf("trans_out")]*$conversion_factor_arr[$row[csf('item_group_id')]],2); ?></p></td> 
                                <td width="80" align="right"><p><? echo number_format($totalIssue,2); ?></p></td>
                                <td align="right"><p><a href='##' onclick="openmypage_stock('<? echo $row[csf('prod_id')];?>','<? echo $row[csf('store_id')]; ?>','<? echo $select_from_date; ?>','<? echo $select_to_date; ?>','<? echo $row[csf("unit_of_measure")]; ?>','<? echo $row[csf("buyer_name")]; ?>','stock_popup_details')"><? echo number_format($closingStock,2); ?></a></p></td>                                   
                            </tr>
                            <? 												
                            $i++;
						}
						else if($value_with ==0)
						{
							$grandTotalOpeningStockQty +=$openingStockQty;

							$grandTotalAmount +=$openingStockAmt; 
							$grandTotalReceive +=$row[csf("receive")]*$conversion_factor_arr[$row[csf('item_group_id')]];  
							$grandTotalIssueReturn +=$row[csf("issue_return")]*$conversion_factor_arr[$row[csf('item_group_id')]];
							$grandTotalTransferIn +=$row[csf("trans_in")]*$conversion_factor_arr[$row[csf('item_group_id')]];
							$grandTotalTotalReceive +=$totalReceive; 
							$grandTotalTotalTotalReceiveAmount += $totalReceiveAmount;
							$grandTotalIssue +=$row[csf("issue")]*$conversion_factor_arr[$row[csf('item_group_id')]];
							$grandTotalReceiveReturn +=$row[csf("receive_return")]*$conversion_factor_arr[$row[csf('item_group_id')]];
							$grandTotalTransOut +=$row[csf("trans_out")]*$conversion_factor_arr[$row[csf('item_group_id')]];
							$grandTotalTotalIssue +=$totalIssue;
							$grandTotalTotalIssueAmount +=$totalIssueAmount;
							$grandTotalClosingStock +=$closingStock;
							$grandTotalClosingStockAmount +=$closingStockAmount;
							if($get_up_qnty_check==1)
							{
								if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">    
									<td width="40"><?echo $i;?></td> 
									<td width="60"><p><? echo $row[csf('prod_id')];?></p></td>
									<? if($cbo_search_by==1)  {?>                     
									<td width="120" style="word-break: break-all;"><p><? echo $buyerArr[$row[csf("buyer_name")]]; ?></p></td> 
									<? } ?>
									<td width="100" style="word-break: break-all;"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?></p></td> 
                                    <td width="180" style="word-break: break-all;"><p><? echo $row[csf("item_description")]." ".$item_color_name." ".$item_size_name; ?></p></td>
                                    <td width="120" style="word-break: break-all;"><p><? echo $store_name_arr[$row[csf("store_id")]]; ?></p></td>                     
									<td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td> 			   
									<td width="80" align="right"><p><? echo number_format($openingStockQty,2); ?></p></td>                     
									<td width="80" align="right"><p><? echo number_format($row[csf("receive")]*$conversion_factor_arr[$row[csf('item_group_id')]],2); ?></p></td> 
									<td width="80" align="right"><p><? echo number_format($row[csf("issue_return")]*$conversion_factor_arr[$row[csf('item_group_id')]],2); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($row[csf("trans_in")]*$conversion_factor_arr[$row[csf('item_group_id')]],2); ?></p></td>  
									<td width="80" align="right"><p><? echo number_format($totalReceive,2); ?></p></td>
                                    <td width="80" align="right"><p><? echo number_format($row[csf("issue")]*$conversion_factor_arr[$row[csf('item_group_id')]],2); ?></p></td>  
									<td width="80" align="right"><p><? echo number_format($row[csf("receive_return")]*$conversion_factor_arr[$row[csf('item_group_id')]],2); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($row[csf("trans_out")]*$conversion_factor_arr[$row[csf('item_group_id')]],2); ?></p></td> 
									<td width="80" align="right"><p><? echo number_format($totalIssue,2); ?></p></td>
                                    <td align="right"><p><? echo number_format($closingStock,2); ?></p></td>                                   
								</tr>
								<? 												
								$i++;
							}
						}
					}
					?>
					<tr bgcolor="#CCCCCC" style="font-weight:bold">           	
                        <td colspan="<? if($cbo_search_by==1) {echo $colspan = 7;}else {echo $colspan=6;} ?>" align="right"><strong>Grand Total</strong></td>
                        <td align="right"><? echo number_format($grandTotalOpeningStockQty,2); ?></td>
                        <td align="right"><? echo number_format($grandTotalReceive,2); ?></td>
                        <td align="right"><? echo number_format($grandTotalIssueReturn,2); ?></td>
                        <td align="right"><? echo number_format($grandTotalTransferIn,2); ?></td>
                        <td align="right"><? echo number_format($grandTotalTotalReceive,2); ?></td>
                        <td align="right"><? echo number_format($grandTotalIssue,2); ?></td>
                        <td align="right"><? echo number_format($grandTotalReceiveReturn,2); ?></td>
                        <td align="right"><? echo number_format($grandTotalTransOut,2); ?></td>
                        <td align="right"><? echo number_format($grandTotalTotalIssue,2); ?></td>
                        <td align="right"><? echo number_format($grandTotalClosingStock,2); ?></td>  
					</tr>  
					</table>
					</div> 
					<? 
        		}
				else if($cbo_search_by==4) 
                {
					?>
                    <table style="width:<? echo $table_width; ?>px" border="1" cellpadding="0" cellspacing="0" class="rpt_table" id="table_header_1" rules="all" align="left"> 
                        <thead> 

                        <tr>
                            <th rowspan="2" width="40">SL</th>
                            <th rowspan="2" width="50">Prod.ID</th><th colspan="2">Description</th>
                            <th rowspan="2" width="130">Store Name</th>
                            <th rowspan="2" width="60">UOM</th>
                            <th rowspan="2" width="80">Opening Stock</th>
                            <th colspan="4">Receive</th>
                            <th colspan="4">Issue</th>
                            <th rowspan="2">Closing Stock</th>
                        </tr> 
                        <tr>
                            <th width="100">Item Group</th>
                            <th width="180">Item Description</th>
                            <th width="80">Receive</th>
                            <th width="80">Issue Return</th>
                            <th width="80">Transfer In</th>
                            <th width="80">Total Receive</th>
                            <th width="80">Issue</th>
                            <th width="80">Received Return</th>
                            <th width="80">Transfer Out</th>
                            <th width="80">Total Issue</th>
                        </tr>  
					</thead>
					</table>
					
					<div style="width:<? echo $div_width; ?>px; max-height:350px; overflow-y:scroll;" id="scroll_body"  > 
					<table style="width:<? echo $table_width; ?>px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
					<?
					$grandTotalOpeningStockQty = 0;
					$grandTotalAmount = 0;
					$grandTotalReceive = 0;
					$grandTotalIssueReturn = 0;
					$grandTotalTransferIn = 0;
					$grandTotalTotalReceive = 0;
					$grandTotalTotalTotalReceiveAmount = 0;
					$grandTotalIssue = 0;
					$grandTotalReceiveReturn = 0;
					$grandTotalTransOut=0;
					$grandTotalTotalIssue = 0;
					$grandTotalTotalIssueAmount=0;
					$grandTotalClosingStock = 0;
					$grandTotalClosingStockAmount = 0;
					foreach($general_ac_data as $item_val)
					{
						foreach($item_val as $row)
						{
							
							if( $row[("transaction_type")]==1 || $row[("transaction_type")]==2 || $row[("transaction_type")]==3 || $row[("transaction_type")]==4 ) 
							$stylecolor='style="color:#A61000"';
							else
							$stylecolor='style="color:#000000"';
							$openingStockQty = $row[("opening_total_receive")]-$row[("opening_total_issue")];
							$openingStockAmt = $row[("opening_rcv_amt")]-$row[("opening_issue_amt")];
							$amount = ($openingStockQty*$row[("avg_rate_per_unit")]);
							$totalReceive = $row[("receive")]+$row[("issue_return")]+$row[("trans_in")];
							$totalReceiveAmount = $row[("receive_amt")]+$row[("issue_return_amt")]+$row[("trans_in_amt")];
							$totalIssue = $row[("issue")]+$row[("receive_return")]+$row[("trans_out")];
							$totalIssueAmount = $row[("issue_amt")]+$row[("receive_return_amt")]+$row[("trans_out_amt")];;
							$closingStock = (($openingStockQty+$totalReceive)-$totalIssue);
							$closingStockAmount = (($openingStockAmt+$totalReceiveAmount)-$totalIssueAmount);
							
							$get_up_qnty_check=0;
							if($cbo_get_upto_qnty==0)
							{
								$get_up_qnty_check=1;
							}
							else if($cbo_get_upto_qnty==1 && $closingStock>$txt_qnty)
							{
								$get_up_qnty_check=1;
							}
							else if($cbo_get_upto_qnty==2 && $closingStock<$txt_qnty)
							{
								$get_up_qnty_check=1;
							}
							else if($cbo_get_upto_qnty==3 && $closingStock>=$txt_qnty)
							{
								$get_up_qnty_check=1;
							}
							else if($cbo_get_upto_qnty==4 && $closingStock<=$txt_qnty)
							{
								$get_up_qnty_check=1;
							}
							else if($cbo_get_upto_qnty==5 && $closingStock=$txt_qnty)
							{
								$get_up_qnty_check=1;
							}
							
							if($value_with ==1 && (number_format($openingStockQty,2,".","") >0 || number_format($totalReceive,2,".","") >0 || number_format($totalIssue,2,".","") >0 || number_format($closingStock,2,".","") >0) && $get_up_qnty_check==1 )
							{
								$grandTotalOpeningStockQty +=$openingStockQty;
								$grandTotalAmount +=$openingStockAmt; 
								$grandTotalReceive +=$row[("receive")];  
								$grandTotalIssueReturn +=$row[("issue_return")];
								$grandTotalTransferIn +=$row[("trans_in")];
								$grandTotalTotalReceive +=$totalReceive; 
								$grandTotalTotalTotalReceiveAmount += $totalReceiveAmount;
								$grandTotalIssue +=$row[("issue")];
								$grandTotalReceiveReturn +=$row[("receive_return")];
								$grandTotalTransOut +=$row[("trans_out")];
								$grandTotalTotalIssue +=$totalIssue;
								$grandTotalTotalIssueAmount +=$totalIssueAmount;
								$grandTotalClosingStock +=$closingStock;
								$grandTotalClosingStockAmount +=$closingStockAmount;
								
								if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
								?>
                                <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">    
                                    <td width="40" align="center"><? echo $i;?></td> 
                                    <td width="50" align="center"><p><? echo $row[('prod_id')];?></p></td>
                                    <td width="100" style="word-break: break-all;"><p><? echo $itemgroupArr[$row[("item_group_id")]]; ?></p></td>  
                                    <td width="180" style="word-break: break-all;"><p><? echo $row[("item_description")]; ?></p></td>  
                                    
                                    <td width="130" style="word-break: break-all;"><p><? echo $store_name_arr[$row[("store_id")]]; ?></p></td>                     
                                    <td width="60" align="center"><p><? echo $unit_of_measurement[$row[("unit_of_measure")]]; ?></p></td> 			   
                                    <td width="80" align="right"><p><? echo number_format($openingStockQty,2); ?></p></td>                     
                                    <td width="80" align="right"><p><? echo number_format($row[("receive")],2); ?></p></td> 
                                    <td width="80" align="right"><p><? echo number_format($row[("issue_return")],2); ?></p></td>
                                    <td width="80" align="right"><p><? echo number_format($row[("trans_in")],2); ?></p></td>  
                                    <td width="80" align="right"><p><? echo number_format($totalReceive,2); ?></p></td>
                                    <td width="80" align="right"><p><? echo number_format($row[("issue")],2); ?></p></td>  
                                    <td width="80" align="right"><p><? echo number_format($row[("receive_return")],2); ?></p></td>
                                    <td width="80" align="right"><p><? echo number_format($row[("trans_out")],2); ?></p></td> 
                                    <td width="80" align="right"><p><? echo number_format($totalIssue,2); ?></p></td>
                                    <td align="right"><p><? echo number_format($closingStock,2); ?></p></td>                                  
                                </tr>
                                <? 												
                                $i++;
							}
							else if($value_with ==0)
							{
								$grandTotalOpeningStockQty +=$openingStockQty;
								$grandTotalAmount +=$openingStockAmt; 
								$grandTotalReceive +=$row[("receive")];  
								$grandTotalIssueReturn +=$row[("issue_return")];
								$grandTotalTransferIn +=$row[("trans_in")];
								$grandTotalTotalReceive +=$totalReceive; 
								$grandTotalTotalTotalReceiveAmount += $totalReceiveAmount;
								$grandTotalIssue +=$row[("issue")];
								$grandTotalReceiveReturn +=$row[("receive_return")];
								$grandTotalTransOut +=$row[("trans_out")];
								$grandTotalTotalIssue +=$totalIssue;
								$grandTotalTotalIssueAmount +=$totalIssueAmount;
								$grandTotalClosingStock +=$closingStock;
								$grandTotalClosingStockAmount +=$closingStockAmount;
								if($get_up_qnty_check==1)
								{
									if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">    
										<td width="40" align="center"><? echo $i;?></td> 
										<td width="50" align="center"><p><? echo $row[('prod_id')];?></p></td>
										<td width="100" style="word-break: break-all;"><p><? echo $itemgroupArr[$row[("item_group_id")]]; ?></p></td>  
										<td width="180" style="word-break: break-all;"><p><? echo $row[("item_description")]; ?></p></td> 
                                        <td width="130" style="word-break: break-all;"><p><? echo $store_name_arr[$row[("store_id")]]; ?></p></td>                     
										<td width="60" align="center"><p><? echo $unit_of_measurement[$row[("unit_of_measure")]]; ?></p></td> 			   
										<td width="80" align="right"><p><? echo number_format($openingStockQty,2); ?></p></td>
                                        <td width="80" align="right"><p><? echo number_format($row[("receive")],2); ?></p></td> 
										<td width="80" align="right"><p><? echo number_format($row[("issue_return")],2); ?></p></td>
										<td width="80" align="right"><p><? echo number_format($row[("trans_in")],2); ?></p></td>  
										<td width="80" align="right"><p><? echo number_format($totalReceive,2); ?></p></td>
                                        <td width="80" align="right"><p><? echo number_format($row[("issue")],2); ?></p></td>  
										<td width="80" align="right"><p><? echo number_format($row[("receive_return")],2); ?></p></td>
										<td width="80" align="right"><p><? echo number_format($row[("trans_out")],2); ?></p></td> 
										<td width="80" align="right"><p><? echo number_format($totalIssue,2); ?></p></td>
                                        <td align="right"><p><? echo number_format($closingStock,2); ?></p></td>                                
									</tr>
									<? 												
									$i++;
								}
							}
						}
					}
					?>
					<tr bgcolor="#CCCCCC" style="font-weight:bold">           	
                        <td colspan="<? if($cbo_search_by==1) {echo $colspan = 7;}else {echo $colspan=6;} ?>" align="right"><strong>Grand Total</strong></td>
                        <td align="right"><? echo number_format($grandTotalOpeningStockQty,2); ?></td>
                        <td align="right"><? echo number_format($grandTotalReceive,2); ?></td>
                        <td align="right"><? echo number_format($grandTotalIssueReturn,2); ?></td>
                        <td align="right"><? echo number_format($grandTotalTransferIn,2); ?></td>
                        <td align="right"><? echo number_format($grandTotalTotalReceive,2); ?></td>
                        <td align="right"><? echo number_format($grandTotalIssue,2); ?></td>
                        <td align="right"><? echo number_format($grandTotalReceiveReturn,2); ?></td>
                        <td align="right"><? echo number_format($grandTotalTransOut,2); ?></td>
                        <td align="right"><? echo number_format($grandTotalTotalIssue,2); ?></td>
                        <td align="right"><? echo number_format($grandTotalClosingStock,2); ?></td> 
					</tr>  
					</table>
					</div> 
					<? 
        		}  
				else 
				{ 
					?>
					<table style="width:<? echo $table_width; ?>px" border="0" cellpadding="2" cellspacing="0" class="rpt_table" id="table_header_2" rules="all" align="left"> 
						<thead> 
							<tr>
								<th rowspan="2" width="40">SL</th>
								<th rowspan="2" width="60">Prod.ID</th>
								<th rowspan="2" width="120">Buyer</th>
								<th colspan="2" width="280">Description</th>
								<th rowspan="2" width="60">UOM</th>
								<th rowspan="2" width="80">Opening Stock</th>
								<? foreach($storeIdArr as $storeId)
								{
									?>
                                    <th colspan="3"><? echo $store_name_arr[$storeId];?></th>  
                                    <? 
								}
								?>
								<th rowspan="2" width="100">Closing Stock</th> 
							</tr>
							
							<tr>
								<th width="100">Item Group</th>
								<th width="180">Item Description</th>
								<? foreach($storeIdArr as $storeId)
								{
									?>
                                    <th width="80">Quantity</th>
                                    <th width="80">Avg Rate</th>
                                    <th width="80">Amount</th>
                                    <? 
								}?> 
							</tr>
						</thead>
					</table>
					
					<div style="width:<? echo $div_width; ?>px; max-height:350px; overflow-y:scroll;" id="scroll_body" align="left"> 
						<table style="width:<? echo $table_width; ?>px" border="0" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left">
							<? 
							foreach($store_data as $buyer_id=>$buyer_data) 
							{ 
								foreach($buyer_data as $prod_id=>$prod_data) 
								{
									$openingStockQty = $prod_data["opening_total_receive"]-$prod_data["opening_total_issue"];
									$opening_amount = $prod_data["opening_rcv_amt"]-$prod_data["opening_issue_amt"];
									$grand_opening_stock+=$openingStockQty;
									$grand_opening_amount+=$opening_amount;
									?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">    
                                       <td width="40" align="center"> <? echo $i; ?></td>
                                       <td width="60"><p><? echo $prod_id;?></p></td>
                                       <td width="120" style="word-break: break-all;"><p><? echo $buyerArr[$buyer_id]; ?></p></td> 
                                       <td width="100" style="word-break: break-all;"><p><? echo $itemgroupArr[$prod_data["item_group_id"]]; ?></p></td>  
                                       <td width="180" style="word-break: break-all;"><p><? echo $prod_data["item_description"]; ?></p></td>
                                       <td width="60" align="center"><p><? echo $unit_of_measurement[$prod_data["unit_of_measure"]]; ?></p></td>
                                       <td width="80" align="right"><? echo number_format($openingStockQty,2); ?></td> 
                                       
                                       <? 
                                       $total_store_qnty=$colsing_stock_qnty=$colsing_stock_amt= $total_store_qnty_amt=0;
                                       foreach($storeIdArr as $storeId)
                                       {
                                           $running_qnty=0;
                                           $running_qnty=(($store_wise_data[$buyer_id][$prod_id][$storeId]["receive"]+$store_wise_data[$buyer_id][$prod_id][$storeId]["issue_return"]+$store_wise_data[$buyer_id][$prod_id][$storeId]["trans_in"])-($store_wise_data[$buyer_id][$prod_id][$storeId]["issue"]+$store_wise_data[$buyer_id][$prod_id][$storeId]["receive_return"]+$store_wise_data[$buyer_id][$prod_id][$storeId]["trans_out"]));
                                           $total_store_qnty+=$running_qnty;
                                           $running_amt=(($store_wise_data[$buyer_id][$prod_id][$storeId]["receive_amt"]+$store_wise_data[$buyer_id][$prod_id][$storeId]["issue_return_amt"]+$store_wise_data[$buyer_id][$prod_id][$storeId]["trans_in_amt"])-($store_wise_data[$buyer_id][$prod_id][$storeId]["issue_amt"]+$store_wise_data[$buyer_id][$prod_id][$storeId]["receive_return_amt"]+$store_wise_data[$buyer_id][$prod_id][$storeId]["trans_out_amt"]));
                                            $total_store_qnty_amt+=$running_amt;
                                           ?>
                                           <td width="80" align="right"><? echo number_format($running_qnty,2); ?></td>	
                                           <td width="80" align="right"><? if($running_qnty>0) echo number_format($running_amt/$running_qnty,4); else echo "0.00"; ?></td>
                                           <td width="80" align="right"><? echo number_format($running_amt,2); ?></td> 
                                        <?
                                        $grand_store_data[$storeId]["qnty"]+=$running_qnty;
                                        $grand_store_data[$storeId]["amt"]+=$running_amt; 
                                       }
                                       $colsing_stock_qnty=$openingStockQty+$total_store_qnty;
                                       $colsing_stock_amt=$opening_amount+$total_store_qnty_amt;
                                       $grand_closing_qnty+=$colsing_stock_qnty;
                                       $grand_closing_amt+=$colsing_stock_amt;
                                       ?>
                                       <td align="right" width="100"><? echo number_format($colsing_stock_qnty,2); ?></td>
                                     </tr>   
                                    <?
                                    $i++;      
								}	
							}
			
							?>
							<tr bgcolor="#CCCCCC" style="font-weight:bold">           	
								<th colspan="6" align="right">Grand Total: </th>
								<th align="right"><? echo number_format($grand_opening_stock,2);?></th>
								<? foreach($storeIdArr as $storeId){?>
								<th align="right" ><? echo number_format($grand_store_data[$storeId]["qnty"],2); ?></th>
								<th></th>
								<th align="right"><? echo number_format($grand_store_data[$storeId]["amt"],2); ?></th> 
								<? }?>
								<th align="right"><? echo number_format($grand_closing_qnty,2);?></th> 
							</tr>
							<?
						}
                ?>
            </table>
        </div>  
    </div>    
    <?
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$cbo_search_by"; 
    exit();
}

if($action[0]=="report_generate_exel_only")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);    
	$item_group_id=str_replace("'","",$item_group_id);
	$item_account_id=str_replace("'","",$item_account_id);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id); 
	$value_with=str_replace("'","",$cbo_value_with);
	$cbo_get_upto_qnty=str_replace("'","",$cbo_get_upto_qnty); 
	$txt_qnty=str_replace("'","",$txt_qnty);
	
	$str_cond="";$zero_cond="";
	if ($cbo_company_name!=0) $str_cond =" and a.company_id in($cbo_company_name)";
	if ($item_group_id!="") $str_cond .=" and a.item_group_id in($item_group_id)";
	if ($item_account_id!="") $str_cond .=" and a.id in ($item_account_id)";
	if ($cbo_store_name!=0) $str_cond .=" and b.store_id in($cbo_store_name)";
	if ($cbo_buyer_id!=0) $str_cond .=" and e.buyer_name in($cbo_buyer_id)";

	if($db_type==0) $select_from_date=change_date_format($from_date,'yyyy-mm-dd');
	if($db_type==2) $select_from_date=change_date_format($from_date,'','',1);
	
	if($db_type==0) $select_to_date=change_date_format($to_date,'yyyy-mm-dd');
	if($db_type==2) $select_to_date=change_date_format($to_date,'','',1);
	//if($value_with==0) $zero_cond .=""; else $zero_cond .= "  and a.current_stock>0";
	
	//echo $str_cond; die;
	
	
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name"); 
	$store_name_arr=return_library_array("select id, store_name from  lib_store_location","id","store_name");
	$itemgroupArr = return_library_array("select id,item_name from lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	$conversion_factor_arr = return_library_array("select id,conversion_factor from lib_item_group where status_active=1 and is_deleted=0","id","conversion_factor");
	$color_name_arr=return_library_array("select id, color_name from  lib_color","id","color_name");
				
	if($cbo_search_by==1) // Accessories
	{
		$div_width="2150";
		$table_width="2130";
		
		$sql="Select e.buyer_name, b.prod_id, b.store_id, a.item_category_id, a.item_group_id, a.avg_rate_per_unit, a.item_description, a.item_color, a.item_size, b.cons_uom as unit_of_measure, 
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (1,4,5) and c.trans_type in (1,4,5)  then c.quantity else 0 end) as opening_total_receive,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (1,4,5) and c.trans_type in (1,4,5)  then (c.quantity*c.order_rate) else 0 end) as opening_rcv_amt,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (2,3,6) and c.trans_type in (2,3,6)  then c.quantity else 0 end) as opening_total_issue,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (2,3,6) and c.trans_type in (2,3,6)  then (c.quantity*c.order_rate) else 0 end) as opening_issue_amt,
		sum(case when b.transaction_type=1 and c.trans_type=1 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as receive,
		sum(case when b.transaction_type=1 and c.trans_type=1 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as receive_amt,
		sum(case when b.transaction_type=4 and c.trans_type=4 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as issue_return,
		sum(case when b.transaction_type=4 and c.trans_type=4 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as issue_return_amt,
		sum(case when b.transaction_type=5 and c.trans_type=5 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as trans_in,
		sum(case when b.transaction_type=5 and c.trans_type=5 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as trans_in_amt,		
		sum(case when b.transaction_type=2 and c.trans_type=2 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as issue,
		sum(case when b.transaction_type=2 and c.trans_type=2 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as issue_amt,
		sum(case when b.transaction_type=3 and c.trans_type=3 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as receive_return,
		sum(case when b.transaction_type=3 and c.trans_type=3 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as receive_return_amt,		
		sum(case when b.transaction_type=6  and c.trans_type=6 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as trans_out,
		sum(case when b.transaction_type=6  and c.trans_type=6 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as trans_out_amt 
		
		from product_details_master a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e
		where a.id=b.prod_id and b.id=c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and b.transaction_type in (1,2,3,4,5,6) and c.trans_type in (1,2,3,4,5,6) and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form in(24,25,49,73,78,112)  $str_cond $zero_cond
		group by e.buyer_name, b.store_id, b.prod_id, a.item_category_id, a.item_group_id, a.avg_rate_per_unit, a.item_description, a.item_color, a.item_size, b.cons_uom
		order by e.buyer_name, b.prod_id";
    } 
	elseif($cbo_search_by==2) // Item Group Wise 
	{
		$div_width="2038";
		$table_width="2020";
		
		$sql="Select b.prod_id, b.store_id, a.item_category_id, a.item_group_id, a.avg_rate_per_unit, a.item_description, a.item_color, a.item_size, b.cons_uom as unit_of_measure, 
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (1,4,5) and c.trans_type in (1,4,5)  then c.quantity else 0 end) as opening_total_receive,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (1,4,5) and c.trans_type in (1,4,5)  then (c.quantity*c.order_rate) else 0 end) as opening_rcv_amt,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (2,3,6) and c.trans_type in (2,3,6)  then c.quantity else 0 end) as opening_total_issue,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (2,3,6) and c.trans_type in (2,3,6)  then (c.quantity*c.order_rate) else 0 end) as opening_issue_amt,
		sum(case when b.transaction_type=1 and c.trans_type=1 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as receive,
		sum(case when b.transaction_type=1 and c.trans_type=1 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as receive_amt,
		sum(case when b.transaction_type=4 and c.trans_type=4 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as issue_return,
		sum(case when b.transaction_type=4 and c.trans_type=4 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as issue_return_amt,
		sum(case when b.transaction_type=5 and c.trans_type=5 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as trans_in,
		sum(case when b.transaction_type=5 and c.trans_type=5 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as trans_in_amt,		
		sum(case when b.transaction_type=2 and c.trans_type=2 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as issue,
		sum(case when b.transaction_type=2 and c.trans_type=2 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as issue_amt,
		sum(case when b.transaction_type=3 and c.trans_type=3 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as receive_return,
		sum(case when b.transaction_type=3 and c.trans_type=3 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as receive_return_amt,		
		sum(case when b.transaction_type=6  and c.trans_type=6 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as trans_out,
		sum(case when b.transaction_type=6  and c.trans_type=6 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as trans_out_amt 
		
		from product_details_master a, inv_transaction b, order_wise_pro_details c
		where a.id=b.prod_id and b.id=c.trans_id and b.transaction_type in (1,2,3,4,5,6) and c.trans_type in (1,2,3,4,5,6) and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form in(24,25,49,73,78,112)  $str_cond $zero_cond
		group by  b.store_id, b.prod_id, a.item_category_id, a.item_group_id, a.avg_rate_per_unit, a.item_description, a.item_color, a.item_size, b.cons_uom";
	} 
	elseif($cbo_search_by==3) // Store Wise
	{
		$sql="Select e.buyer_name, b.prod_id, b.store_id, a.item_category_id, a.item_group_id, a.avg_rate_per_unit, a.item_description, a.item_color, a.item_size, b.cons_uom as unit_of_measure, 
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (1,4,5) and c.trans_type in (1,4,5)  then c.quantity else 0 end) as opening_total_receive,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (1,4,5) and c.trans_type in (1,4,5)  then (c.quantity*c.order_rate) else 0 end) as opening_rcv_amt,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (2,3,6) and c.trans_type in (2,3,6)  then c.quantity else 0 end) as opening_total_issue,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (2,3,6) and c.trans_type in (2,3,6)  then (c.quantity*c.order_rate) else 0 end) as opening_issue_amt,
		sum(case when b.transaction_type=1 and c.trans_type=1 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as receive,
		sum(case when b.transaction_type=1 and c.trans_type=1 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as receive_amt,
		sum(case when b.transaction_type=4 and c.trans_type=4 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as issue_return,
		sum(case when b.transaction_type=4 and c.trans_type=4 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as issue_return_amt,
		sum(case when b.transaction_type=5 and c.trans_type=5 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as trans_in,
		sum(case when b.transaction_type=5 and c.trans_type=5 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as trans_in_amt,		
		sum(case when b.transaction_type=2 and c.trans_type=2 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as issue,
		sum(case when b.transaction_type=2 and c.trans_type=2 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as issue_amt,
		sum(case when b.transaction_type=3 and c.trans_type=3 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as receive_return,
		sum(case when b.transaction_type=3 and c.trans_type=3 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as receive_return_amt,		
		sum(case when b.transaction_type=6  and c.trans_type=6 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then c.quantity else 0 end) as trans_out,
		sum(case when b.transaction_type=6  and c.trans_type=6 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then (c.quantity*c.order_rate) else 0 end) as trans_out_amt 
		
		from product_details_master a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e
		where a.id=b.prod_id and b.id=c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and b.transaction_type in (1,2,3,4,5,6) and c.trans_type in (1,2,3,4,5,6) and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form in(24,25,49,73,78,112)  $str_cond $zero_cond
		group by e.buyer_name, b.store_id, b.prod_id, a.item_category_id, a.item_group_id, a.avg_rate_per_unit, a.item_description, a.item_color, a.item_size, b.cons_uom
		order by e.buyer_name, b.prod_id";
	} 
	elseif($cbo_search_by==4) // General Accessories  
	{
		$div_width="2028";
		$table_width="2010";
		 
		$sql="Select b.store_id, b.prod_id, a.item_category_id, a.item_group_id, a.avg_rate_per_unit, a.item_description, b.cons_uom as unit_of_measure, a.item_color, a.item_size,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in(1,4)  then b.cons_quantity else 0 end) as opening_total_receive,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in(1,4)  then b.cons_amount else 0 end) as opening_rcv_amt,
		0 as opening_total_issue,
		0 as opening_issue_amt,
		sum(case when b.transaction_type=1 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then b.cons_quantity else 0 end) as receive,
		sum(case when b.transaction_type=1 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then b.cons_amount else 0 end) as receive_amt,
		sum(case when b.transaction_type=4 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then b.cons_quantity else 0 end) as issue_return,
		sum(case when b.transaction_type=4 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then b.cons_amount else 0 end) as issue_return_amt,
		0 as trans_in,
		0 as trans_in_amt,
		0 as issue,
		0 as issue_amt,
		0 as receive_return,
		0 as receive_return_amt,
		0 as trans_out,
		0 as trans_out_amt, 
		1 as type
		
		from  product_details_master a, inv_transaction b, inv_receive_master c
		where a.id=b.prod_id and b.mst_id=c.id and b.transaction_type in (1,4) and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form in(20,28)  $str_cond $zero_cond
		group by b.store_id, b.prod_id, a.item_category_id, a.item_group_id, a.avg_rate_per_unit, a.item_description, b.cons_uom, a.item_color, a.item_size
		
		union all
		
		Select b.store_id, b.prod_id, a.item_category_id, a.item_group_id, a.avg_rate_per_unit, a.item_description, b.cons_uom as unit_of_measure, a.item_color, a.item_size,
		0 as opening_total_receive,
		0 as opening_rcv_amt,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in(2,3) then b.cons_quantity else 0 end) as opening_total_issue,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in(2,3) then b.cons_amount else 0 end) as opening_issue_amt,
		0 as receive,
		0 as receive_amt,
		0 as issue_return,
		0 as issue_return_amt,
		0 as trans_in,
		0 as trans_in_amt,
		sum(case when b.transaction_type=2 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then b.cons_quantity else 0 end) as issue,
		sum(case when b.transaction_type=2 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then b.cons_amount else 0 end) as issue_amt,
		sum(case when b.transaction_type=3 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then b.cons_quantity else 0 end) as receive_return,
		sum(case when b.transaction_type=3 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then b.cons_amount else 0 end) as receive_return_amt,
		0 as trans_out,
		0 as trans_out_amt, 
		2 as type
		
		from  product_details_master a, inv_transaction b, inv_issue_master c
		where a.id=b.prod_id and b.mst_id=c.id and b.transaction_type in (2,3) and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form in(21,26)  $str_cond $zero_cond
		group by b.store_id, b.prod_id, a.item_category_id, a.item_group_id, a.avg_rate_per_unit, a.item_description, b.cons_uom, a.item_color, a.item_size
		
		union all
		
		Select b.store_id, b.prod_id, a.item_category_id, a.item_group_id, a.avg_rate_per_unit, a.item_description, b.cons_uom as unit_of_measure, a.item_color, a.item_size,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (5)  then b.cons_quantity else 0 end) as opening_total_receive,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (5)  then b.cons_amount else 0 end) as opening_rcv_amt,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (6) then b.cons_quantity else 0 end) as opening_total_issue,
		sum(case when b.transaction_date<'".$select_from_date."' and b.transaction_type in (6) then b.cons_amount else 0 end) as opening_issue_amt,
		0 as receive,
		0 as receive_amt,
		0 as issue_return,
		0 as issue_return_amt, 
		sum(case when b.transaction_type=5 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then b.cons_quantity else 0 end) as trans_in,
		sum(case when b.transaction_type=5 and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then b.cons_amount else 0 end) as trans_in_amt,
		0 as issue,
		0 as issue_amt,
		0 as receive_return,
		0 as receive_return_amt,
		sum(case when b.transaction_type=6  and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then b.cons_quantity else 0 end) as trans_out,
		sum(case when b.transaction_type=6  and b.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then b.cons_amount else 0 end) as trans_out_amt,
		3 as type
		
		from  product_details_master a, inv_transaction b, inv_item_transfer_mst c
		where a.id=b.prod_id and b.mst_id=c.id and b.transaction_type in (5,6) and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form in(57)  $str_cond $zero_cond
		group by b.store_id, b.prod_id, a.item_category_id, a.item_group_id, a.avg_rate_per_unit, a.item_description, b.cons_uom, a.item_color, a.item_size";
	}
	
	//echo $sql;//die;
	
	$result = sql_select($sql);
	$store_wise_data=$store_data=array();
	if($cbo_search_by==3) // Store Wise
	{
		$storeIdArr = array();
		foreach($result as $storeRow)
		{
			$item_color_name=$item_size_name="";
			if($storeRow[csf('item_color')]>0) $item_color_name=", ".$color_name_arr[$storeRow[csf('item_color')]];
			if($storeRow[csf('item_size')]!="") $item_size_name=", ".$storeRow[csf('item_size')];		
			$storeIdArr[$storeRow[csf('store_id')]] = $storeRow[csf('store_id')];
			$store_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]]["item_group_id"] = $storeRow[csf('item_group_id')];
			$store_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]]["item_description"] = $storeRow[csf('item_description')]." ".$item_color_name." ".$item_size_name;
			$store_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]]["avg_rate_per_unit"] = $storeRow[csf('avg_rate_per_unit')];
			$store_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]]["unit_of_measure"] = $storeRow[csf('unit_of_measure')];
			$store_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]]["opening_total_receive"] += $storeRow[csf('opening_total_receive')]*$conversion_factor_arr[$storeRow[csf('item_group_id')]];
			$store_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]]["opening_rcv_amt"] += $storeRow[csf('opening_rcv_amt')];
			$store_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]]["opening_total_issue"] += $storeRow[csf('opening_total_issue')]*$conversion_factor_arr[$storeRow[csf('item_group_id')]];
			$store_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]]["opening_issue_amt"] += $storeRow[csf('opening_issue_amt')];
			
			$store_wise_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["receive"] += $storeRow[csf('receive')]*$conversion_factor_arr[$storeRow[csf('item_group_id')]];
			$store_wise_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["receive_amt"] += $storeRow[csf('receive_amt')];
			$store_wise_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["issue_return"] += $storeRow[csf('issue_return')]*$conversion_factor_arr[$storeRow[csf('item_group_id')]];
			$store_wise_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["issue_return_amt"] += $storeRow[csf('issue_return_amt')];
			$store_wise_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["trans_in"] += $storeRow[csf('trans_in')]*$conversion_factor_arr[$storeRow[csf('item_group_id')]];
			$store_wise_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["trans_in_amt"] += $storeRow[csf('trans_in_amt')];
			$store_wise_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["issue"] += $storeRow[csf('issue')]*$conversion_factor_arr[$storeRow[csf('item_group_id')]];
			$store_wise_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["issue_amt"] += $storeRow[csf('issue_amt')];
			$store_wise_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["receive_return"] += $storeRow[csf('receive_return')]*$conversion_factor_arr[$storeRow[csf('item_group_id')]];
			$store_wise_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["receive_return_amt"] += $storeRow[csf('receive_return_amt')];
			$store_wise_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["trans_out"] += $storeRow[csf('trans_out')]*$conversion_factor_arr[$storeRow[csf('item_group_id')]];
			$store_wise_data[$storeRow[csf('buyer_name')]][$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["trans_out_amt"] += $storeRow[csf('trans_out_amt')];
			
		}
	}
	
	$general_ac_data=array();
	
	if($cbo_search_by==4) // General Accessories
	{
		$storeIdArr = array();
		foreach($result as $storeRow)
		{		
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["prod_id"] = $storeRow[csf('prod_id')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["store_id"] = $storeRow[csf('store_id')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["item_category_id"] = $storeRow[csf('item_category_id')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["item_group_id"] = $storeRow[csf('item_group_id')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["avg_rate_per_unit"] = $storeRow[csf('avg_rate_per_unit')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["item_description"] = $storeRow[csf('item_description')]." ".$color_name_arr[$storeRow[csf('item_color')]]." ".$storeRow[csf('item_size')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["unit_of_measure"] = $storeRow[csf('unit_of_measure')];
			
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["opening_total_receive"] += $storeRow[csf('opening_total_receive')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["opening_rcv_amt"] += $storeRow[csf('opening_rcv_amt')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["opening_total_issue"] += $storeRow[csf('opening_total_issue')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["opening_issue_amt"] += $storeRow[csf('opening_issue_amt')];
			
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["receive"] += $storeRow[csf('receive')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["receive_amt"] += $storeRow[csf('receive_amt')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["issue_return"] += $storeRow[csf('issue_return')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["issue_return_amt"] += $storeRow[csf('issue_return_amt')];
			
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["trans_in"] += $storeRow[csf('trans_in')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["trans_in_amt"] += $storeRow[csf('trans_in_amt')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["issue"] += $storeRow[csf('issue')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["issue_amt"] += $storeRow[csf('issue_amt')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["receive_return"] += $storeRow[csf('receive_return')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["receive_return_amt"] += $storeRow[csf('receive_return_amt')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["trans_out"] += $storeRow[csf('trans_out')];
			$general_ac_data[$storeRow[csf('prod_id')]][$storeRow[csf('store_id')]]["trans_out_amt"] += $storeRow[csf('trans_out_amt')];
			
		}
	}
	
	if($cbo_search_by==3)
	{
		$div_width=1060+(count($storeIdArr)*240);
		$table_width=1040+(count($storeIdArr)*240);
	}

	$i=1;
	//ob_start();	
	
    // <div align="left"> 
        $html .= '<table style="width:'.$table_width.'px" border="0">
            <tr class="form_caption" style="border:none;"> 
                <td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold">'. $report_title.'</td>
            </tr>
            
            <tr style="border:none;">
                <td colspan="20" align="center" style="border:none; font-size:14px;">
                   <b>Company Name : '.$companyArr[$cbo_company_name].'</b>                               
                </td>
            </tr>
            
            <tr style="border:none;">
                <td colspan="20" align="center" style="border:none;font-size:12px; font-weight:bold">';
                    if($from_date!="" || $to_date!="") $html .='From : '.change_date_format($from_date,"dd-mm-yyyy").' To : '.change_date_format($to_date,"dd-mm-yyyy").'
                </td>
            </tr>
        </table>';
        
        if($cbo_search_by==1 || $cbo_search_by==2) // Accessories and Item Group Wise 
        {
            $html .='<table style="width:<? echo $table_width; ?>px" border="1" cellpadding="0" cellspacing="0" class="rpt_table" id="table_header_1" rules="all" align="left"> 
                <thead>
	                <tr>
	                    <th rowspan="2">SL</th>
	                    <th rowspan="2">Prod.ID</th>';	                    
	                     
	                    if($cbo_search_by==1)  
	                    {
	                    	$html .='<th rowspan="2">Buyer</th>';
	                	}
	                    
	                    $html .='<th colspan="2">Description</th>
	                    <th rowspan="2">Store Name</th>
	                    <th rowspan="2">UOM</th>
	                    <th rowspan="2">Opening Stock</th>
	                    <th rowspan="2">Avg Rate</th>
	                    <th rowspan="2">Amount</th>
	                    
	                    <th colspan="5">Receive</th>  
	                    <th rowspan="2">Receive Amount</th>
	                    
	                    <th colspan="5">Issue</th> 
	                    <th rowspan="2">Issue Amount</th>
	                    
	                    <th rowspan="2">Closing Stock</th>
	                    <th rowspan="2">Avg Rate</th>
	                    <th rowspan="2">Closing Amount</th>
	                </tr>
	                <tr>
	                    <th>Item Group</th>
	                    <th>Item Description</th>
	                    
	                    <th>Receive</th>
	                    <th>Issue Return</th>
	                    <th>Transfer In</th>
	                    <th>Total Receive</th>
	                    <th>Avg Rate</th>
	                    
	                    <th>Issue</th>
	                    <th>Received Return</th>
	                    <th>Transfer Out</th>
	                    <th>Total Issue</th>
	                    <th>Avg Rate</th>
	                </tr>  
				</thead>
			</table>';
			
			/*<div style="width:<? echo $div_width; ?>px; max-height:350px; overflow-y:scroll;" id="scroll_body">*/
				$html .='<table style="width:'.$table_width.'px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">';
					
					$grandTotalOpeningStockQty = 0;
					$grandTotalAmount = 0;
					$grandTotalReceive = 0;
					$grandTotalIssueReturn = 0;
					$grandTotalTransferIn = 0;
					$grandTotalTotalReceive = 0;
					$grandTotalTotalTotalReceiveAmount = 0;
					$grandTotalIssue = 0;
					$grandTotalReceiveReturn = 0;
					$grandTotalTransOut=0;
					$grandTotalTotalIssue = 0;
					$grandTotalTotalIssueAmount=0;
					$grandTotalClosingStock = 0;
					$grandTotalClosingStockAmount = 0;
					foreach($result as $row)
					{
						if( $row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==4 ) 
						$stylecolor='style="color:#A61000"';
						else
						$stylecolor='style="color:#000000"';
						$openingStockQty = (($row[csf("opening_total_receive")]-$row[csf("opening_total_issue")])*$conversion_factor_arr[$row[csf('item_group_id')]]);
						$openingStockAmt = (($row[csf("opening_rcv_amt")]-$row[csf("opening_issue_amt")]));
						$totalReceive = (($row[csf("receive")]+$row[csf("issue_return")]+$row[csf("trans_in")])*$conversion_factor_arr[$row[csf('item_group_id')]]);
						$totalReceiveAmount = (($row[csf("receive_amt")]+$row[csf("issue_return_amt")]+$row[csf("trans_in_amt")]));
						$totalIssue = (($row[csf("issue")]+$row[csf("receive_return")]+$row[csf("trans_out")])*$conversion_factor_arr[$row[csf('item_group_id')]]);
						$totalIssueAmount = (($row[csf("issue_amt")]+$row[csf("receive_return_amt")]+$row[csf("trans_out_amt")]));
						$closingStock = (($openingStockQty+$totalReceive)-$totalIssue);
						$closingStockAmount = (($openingStockAmt+$totalReceiveAmount)-$totalIssueAmount);;
						
						$get_up_qnty_check=0;
						if($cbo_get_upto_qnty==0)
						{
							$get_up_qnty_check=1;
						}
						else if($cbo_get_upto_qnty==1 && $closingStock>$txt_qnty)
						{
							$get_up_qnty_check=1;
						}
						else if($cbo_get_upto_qnty==2 && $closingStock<$txt_qnty)
						{
							$get_up_qnty_check=1;
						}
						else if($cbo_get_upto_qnty==3 && $closingStock>=$txt_qnty)
						{
							$get_up_qnty_check=1;
						}
						else if($cbo_get_upto_qnty==4 && $closingStock<=$txt_qnty)
						{
							$get_up_qnty_check=1;
						}
						else if($cbo_get_upto_qnty==5 && $closingStock=$txt_qnty)
						{
							$get_up_qnty_check=1;
						}
						
						$item_color_name=$item_size_name="";
						if($row[csf('item_color')]>0) $item_color_name=", ".$color_name_arr[$row[csf('item_color')]];
						if($row[csf('item_size')]!="") $item_size_name=", ".$row[csf('item_size')];
						if($value_with ==1 && (number_format($openingStockQty,2,".","") >0 || number_format($totalReceive,2,".","") >0 || number_format($totalIssue,2,".","") >0 || number_format($closingStock,2,".","") >0) && $get_up_qnty_check==1 )
						{
							$grandTotalOpeningStockQty +=$openingStockQty;
							$grandTotalAmount +=$openingStockAmt; 
							$grandTotalReceive +=$row[csf("receive")]*$conversion_factor_arr[$row[csf('item_group_id')]];  
							$grandTotalIssueReturn +=$row[csf("issue_return")]*$conversion_factor_arr[$row[csf('item_group_id')]];
							$grandTotalTransferIn +=$row[csf("trans_in")]*$conversion_factor_arr[$row[csf('item_group_id')]];
							$grandTotalTotalReceive +=$totalReceive; 
							$grandTotalTotalTotalReceiveAmount += $totalReceiveAmount;
							$grandTotalIssue +=$row[csf("issue")]*$conversion_factor_arr[$row[csf('item_group_id')]];
							$grandTotalReceiveReturn +=$row[csf("receive_return")]*$conversion_factor_arr[$row[csf('item_group_id')]];
							$grandTotalTransOut +=$row[csf("trans_out")]*$conversion_factor_arr[$row[csf('item_group_id')]];
							$grandTotalTotalIssue +=$totalIssue;
							$grandTotalTotalIssueAmount +=$totalIssueAmount;
							$grandTotalClosingStock +=$closingStock;
							$grandTotalClosingStockAmount +=$closingStockAmount;

		                    $html .='<tr id="tr_'. $i.'"> 
		                        <td>'. $i.'</td>
		                        <td>'. $row[csf('prod_id')].'</td>';

		                        if($cbo_search_by==1)  
		                        {
			                        $html .= '<td>'.$buyerArr[$row[csf("buyer_name")]].'</td>';
		                    	}
		                    	$html .=
								'<td>'. $itemgroupArr[$row[csf("item_group_id")]].'</td>
								<td>'. $row[csf("item_description")]." ".$item_color_name." ". $item_size_name.'</td> 
								<td>'. $store_name_arr[$row[csf("store_id")]].'</td>
								<td>'. $unit_of_measurement[$row[csf("unit_of_measure")]].'</td>
								<td>'. number_format($openingStockQty,2).'</td>';

								if($openingStockQty>0)  
		                        {
			                        $html .= '<td>'.number_format($openingStockAmt/$openingStockQty,4).'</td>
			                         		  <td>'.number_format($openingStockAmt,2).'</td>';
		                    	}
		                    	else 
		                    	{
		                    		$html .= '<td>0.00</td>
		                    				  <td>0.00</td>';
		                    	}
		                    	$html .=
								'<td>'. number_format($row[csf("receive")]*$conversion_factor_arr[$row[csf('item_group_id')]],2).'</td>
								<td>'. number_format($row[csf("issue_return")]*$conversion_factor_arr[$row[csf('item_group_id')]],2).'</td>
								<td>'. number_format($row[csf("trans_in")]*$conversion_factor_arr[$row[csf('item_group_id')]],2).'</td>
								<td>'. number_format($totalReceive,2).'</td>';

								if($totalReceive>0)  
		                        {
			                        $html .= '<td>'.number_format($totalReceiveAmount/$totalReceive,2).'</td>';
		                    	}
		                    	else 
		                    	{
		                    		$html .= '<td>0.00</td>';
		                    	}
		                        $html .='<td>'. number_format($totalReceiveAmount,2).'</td>
		                        <td>'. number_format($row[csf("issue")]*$conversion_factor_arr[$row[csf('item_group_id')]],2).'</td>                  
		                        <td>'. number_format($row[csf("receive_return")]*$conversion_factor_arr[$row[csf('item_group_id')]],2).'</td>                  
		                        <td>'. number_format($row[csf("trans_out")]*$conversion_factor_arr[$row[csf('item_group_id')]],2).'</td>                  
		                        <td>'. number_format($totalIssue,2).'</td>';                 
		                        
		                        if($totalIssue>0)  
		                        {
			                        $html .= '<td>'.number_format($totalIssueAmount/$totalIssue,2).'</td>';
		                    	}
		                    	else 
		                    	{
		                    		$html .= '<td>0.00</td>';
		                    	}
		                        $html .='<td>'.number_format($totalIssueAmount,2).'</td>
		                        <td>'.number_format($closingStock,2).'</td>';

		                        if(number_format($closingStock,2)>0)  
		                        {
			                        $html .= '<td>'.number_format($closingStockAmount/$closingStock,4).'</td>
			                        		  <td>'.number_format($closingStockAmount,2).'</td>';
		                    	}
		                    	else 
		                    	{
		                    		$html .= '<td>0.00</td>
		                    				  <td>0.00</td>';
		                    	}                                 
		                    $html .= '</tr>';										
		                    $i++;
						}
						else if($value_with ==0)
						{
							$grandTotalOpeningStockQty +=$openingStockQty;
							$grandTotalAmount +=$openingStockAmt; 
							$grandTotalReceive +=$row[csf("receive")]*$conversion_factor_arr[$row[csf('item_group_id')]];  
							$grandTotalIssueReturn +=$row[csf("issue_return")]*$conversion_factor_arr[$row[csf('item_group_id')]];
							$grandTotalTransferIn +=$row[csf("trans_in")]*$conversion_factor_arr[$row[csf('item_group_id')]];
							$grandTotalTotalReceive +=$totalReceive; 
							$grandTotalTotalTotalReceiveAmount += $totalReceiveAmount;
							$grandTotalIssue +=$row[csf("issue")]*$conversion_factor_arr[$row[csf('item_group_id')]];
							$grandTotalReceiveReturn +=$row[csf("receive_return")]*$conversion_factor_arr[$row[csf('item_group_id')]];
							$grandTotalTransOut +=$row[csf("trans_out")]*$conversion_factor_arr[$row[csf('item_group_id')]];
							$grandTotalTotalIssue +=$totalIssue;
							$grandTotalTotalIssueAmount +=$totalIssueAmount;
							$grandTotalClosingStock +=$closingStock;
							$grandTotalClosingStockAmount +=$closingStockAmount;
							if($get_up_qnty_check==1)
							{
								$html .='<tr id="tr_'. $i.'">
									<td>'. $i.'</td>
		                        	<td>'. $row[csf('prod_id')].'</td>';
		                        	if($cbo_search_by==1)  
			                        {
				                        $html .= '<td>'.$buyerArr[$row[csf("buyer_name")]].'</td>';
			                    	}
			                    	$html .=
									'<td>'. $itemgroupArr[$row[csf("item_group_id")]].'</td>
									<td>'. $row[csf("item_description")]." ".$item_color_name." ". $item_size_name.'</td>
									<td>'. $store_name_arr[$row[csf("store_id")]].'</td>
									<td>'. $unit_of_measurement[$row[csf("unit_of_measure")]].'</td>
									<td>'. number_format($openingStockQty,2).'</td>';

									if($openingStockQty>0)  
			                        {
				                        $html .= '<td>'.number_format($openingStockAmt/$openingStockQty,4).'</td>
				                         		  <td>'.number_format($openingStockAmt,2).'</td>';
			                    	}
			                    	else 
			                    	{
			                    		$html .= '<td>0.00</td>
			                    				  <td>0.00</td>';
			                    	}
			                    	$html .=
									'<td>'. number_format($row[csf("receive")]*$conversion_factor_arr[$row[csf('item_group_id')]],2).'</td>
									<td>'. number_format($row[csf("issue_return")]*$conversion_factor_arr[$row[csf('item_group_id')]],2).'</td>
									<td>'. number_format($row[csf("trans_in")]*$conversion_factor_arr[$row[csf('item_group_id')]],2).'</td>
									<td>'. number_format($totalReceive,2).'</td>';

									if($totalReceive>0)  
			                        {
				                        $html .= '<td>'.number_format($totalReceiveAmount/$totalReceive,2).'</td>';
			                    	}
			                    	else 
			                    	{
			                    		$html .= '<td>0.00</td>';
			                    	}
			                        $html .='<td>'. number_format($totalReceiveAmount,2).'</td>
			                        <td>'. number_format($row[csf("issue")]*$conversion_factor_arr[$row[csf('item_group_id')]],2).'</td>                  
			                        <td>'. number_format($row[csf("receive_return")]*$conversion_factor_arr[$row[csf('item_group_id')]],2).'</td>                  
			                        <td>'. number_format($row[csf("trans_out")]*$conversion_factor_arr[$row[csf('item_group_id')]],2).'</td>                  
			                        <td>'. number_format($totalIssue,2).'</td>';

			                        if($totalIssue>0)  
			                        {
				                        $html .= '<td>'.number_format($totalIssueAmount/$totalIssue,2).'</td>';
			                    	}
			                    	else 
			                    	{
			                    		$html .= '<td>0.00</td>';
			                    	}
			                        $html .='<td>'.number_format($totalIssueAmount,2).'</td>
			                        <td>'.number_format($closingStock,2).'</td>';

			                        if(number_format($closingStock,2)>0)  
			                        {
				                        $html .= '<td>'.number_format($closingStockAmount/$closingStock,4).'</td>
				                        		  <td>'.number_format($closingStockAmount,2).'</td>';
			                    	}
			                    	else 
			                    	{
			                    		$html .= '<td>0.00</td>
			                    				  <td>0.00</td>';
			                    	}                                   
								$html .= '</tr>';
								$i++;
							}
						}
					}
					
					if($cbo_search_by==1)
	                {
	                	$colspan= 7;
	                }
	                else 
                	{
                		$colspan= 6;
                	}
					$html .='<tr bgcolor="#CCCCCC" style="font-weight:bold">
		                <td colspan="'.$colspan.'" align="right"><strong>Grand Total</strong>
	                	</td>
	                	<td>'. number_format($grandTotalOpeningStockQty,2).'</td>
	                	<td>&nbsp;</td>
						<td>'. number_format($grandTotalAmount,2).'</td>
		                <td>'.number_format($grandTotalReceive,2).'</td>
		                <td>'.number_format($grandTotalIssueReturn,2).'</td>
		                <td>'.number_format($grandTotalTransferIn,2).'</td>
		                <td>'.number_format($grandTotalTotalReceive,2).'</td>
		                <td>&nbsp;</td>
		                <td>'.number_format($grandTotalTotalTotalReceiveAmount,2).'</td>
		                <td>'.number_format($grandTotalIssue,2).'</td>
		                <td>'.number_format($grandTotalReceiveReturn,2).'</td>
		                <td>'.number_format($grandTotalTransOut,2).'</td>
		                <td>'.number_format($grandTotalTotalIssue,2).'</td>
		                <td>&nbsp;</td>
		                <td>'.number_format($grandTotalTotalIssueAmount,2).'</td>
		                <td>'.number_format($grandTotalClosingStock,2).'</td> 
		                <td>&nbsp;</td> 
		                <td>'.number_format($grandTotalClosingStockAmount,2).'</td>  
					</tr>  
				</table>';
			/* </div> */
		}
		else if($cbo_search_by==4) // General Accessories
        {
            $html .='<table style="width:'.$table_width.'px" border="1" cellpadding="0" cellspacing="0" class="rpt_table" id="table_header_1" rules="all" align="left"> 
                <thead>
	                <tr>
	                    <th rowspan="2">SL</th>
	                    <th rowspan="2">Prod.ID</th>
	                    
	                    <th colspan="2">Description</th>
	                    <th rowspan="2">Store Name</th>
	                    <th rowspan="2">UOM</th>
	                    <th rowspan="2">Opening Stock</th>
	                    <th rowspan="2">Avg Rate</th>
	                    <th rowspan="2">Amount</th>
	                    
	                    <th colspan="5">Receive</th>  
	                    <th rowspan="2">Receive Amount</th>
	                    
	                    <th colspan="5">Issue</th> 
	                    <th rowspan="2">Issue Amount</th>
	                    
	                    <th rowspan="2">Closing Stock</th>
	                    <th rowspan="2">Avg Rate</th>
	                    <th rowspan="2">Closing Amount</th>
	                </tr> 
				
	                <tr>
	                    <th>Item Group</th>
	                    <th>Item Description</th>
	                    
	                    <th>Receive</th>
	                    <th>Issue Return</th>
	                    <th>Transfer In</th>
	                    <th>Total Receive</th>
	                    <th>Avg Rate</th>
	                    
	                    <th>Issue</th>
	                    <th>Received Return</th>
	                    <th>Transfer Out</th>
	                    <th>Total Issue</th>
	                    <th>Avg Rate</th>
	                </tr>  
				</thead>
			</table>';
			
			/*<div style="width:<? echo $div_width; ?>px; max-height:350px; overflow-y:scroll;" id="scroll_body"  >*/
				$html .='<table style="width:'.$table_width.'px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">';
					
					$grandTotalOpeningStockQty = 0;
					$grandTotalAmount = 0;
					$grandTotalReceive = 0;
					$grandTotalIssueReturn = 0;
					$grandTotalTransferIn = 0;
					$grandTotalTotalReceive = 0;
					$grandTotalTotalTotalReceiveAmount = 0;
					$grandTotalIssue = 0;
					$grandTotalReceiveReturn = 0;
					$grandTotalTransOut=0;
					$grandTotalTotalIssue = 0;
					$grandTotalTotalIssueAmount=0;
					$grandTotalClosingStock = 0;
					$grandTotalClosingStockAmount = 0;
					foreach($general_ac_data as $item_val)
					{
						foreach($item_val as $row)
						{
							
							if( $row[("transaction_type")]==1 || $row[("transaction_type")]==2 || $row[("transaction_type")]==3 || $row[("transaction_type")]==4 ) 
							$stylecolor='style="color:#A61000"';
							else
							$stylecolor='style="color:#000000"';
							$openingStockQty = $row[("opening_total_receive")]-$row[("opening_total_issue")];
							$openingStockAmt = $row[("opening_rcv_amt")]-$row[("opening_issue_amt")];
							$amount = ($openingStockQty*$row[("avg_rate_per_unit")]);
							$totalReceive = $row[("receive")]+$row[("issue_return")]+$row[("trans_in")];
							$totalReceiveAmount = $row[("receive_amt")]+$row[("issue_return_amt")]+$row[("trans_in_amt")];
							$totalIssue = $row[("issue")]+$row[("receive_return")]+$row[("trans_out")];
							$totalIssueAmount = $row[("issue_amt")]+$row[("receive_return_amt")]+$row[("trans_out_amt")];;
							$closingStock = (($openingStockQty+$totalReceive)-$totalIssue);
							$closingStockAmount = (($openingStockAmt+$totalReceiveAmount)-$totalIssueAmount);
							
							$get_up_qnty_check=0;
							if($cbo_get_upto_qnty==0)
							{
								$get_up_qnty_check=1;
							}
							else if($cbo_get_upto_qnty==1 && $closingStock>$txt_qnty)
							{
								$get_up_qnty_check=1;
							}
							else if($cbo_get_upto_qnty==2 && $closingStock<$txt_qnty)
							{
								$get_up_qnty_check=1;
							}
							else if($cbo_get_upto_qnty==3 && $closingStock>=$txt_qnty)
							{
								$get_up_qnty_check=1;
							}
							else if($cbo_get_upto_qnty==4 && $closingStock<=$txt_qnty)
							{
								$get_up_qnty_check=1;
							}
							else if($cbo_get_upto_qnty==5 && $closingStock=$txt_qnty)
							{
								$get_up_qnty_check=1;
							}
							
							if($value_with ==1 && (number_format($openingStockQty,2,".","") >0 || number_format($totalReceive,2,".","") >0 || number_format($totalIssue,2,".","") >0 || number_format($closingStock,2,".","") >0) && $get_up_qnty_check==1 )
							{
								$grandTotalOpeningStockQty +=$openingStockQty;
								$grandTotalAmount +=$openingStockAmt; 
								$grandTotalReceive +=$row[("receive")];  
								$grandTotalIssueReturn +=$row[("issue_return")];
								$grandTotalTransferIn +=$row[("trans_in")];
								$grandTotalTotalReceive +=$totalReceive; 
								$grandTotalTotalTotalReceiveAmount += $totalReceiveAmount;
								$grandTotalIssue +=$row[("issue")];
								$grandTotalReceiveReturn +=$row[("receive_return")];
								$grandTotalTransOut +=$row[("trans_out")];
								$grandTotalTotalIssue +=$totalIssue;
								$grandTotalTotalIssueAmount +=$totalIssueAmount;
								$grandTotalClosingStock +=$closingStock;
								$grandTotalClosingStockAmount +=$closingStockAmount;
								
								$html .='<tr id="tr_'. $i.'">    
		                            <td>'. $i.'</td>
		                            <td>'. $row[('prod_id')].'</td>
		                            <td>'. $itemgroupArr[$row[("item_group_id")]].'</td>
		                            <td>'. $row[("item_description")].'</td>
		                            <td>'. $store_name_arr[$row[("store_id")]].'</td>
		                            <td>'. $unit_of_measurement[$row[("unit_of_measure")]].'</td>
		                            <td>'. number_format($openingStockQty,2).'</td>';
		                            if($openingStockQty>0)  
			                        {
				                        $html .= '<td>'.number_format($openingStockAmt/$openingStockQty,2).'</td>
				                         		  <td>'.number_format($openingStockAmt,2).'</td>';
			                    	}
			                    	else 
			                    	{
			                    		$html .= '<td>0.00</td>
			                    				  <td>0.00</td>';
			                    	}
			                    	$html .=
									'<td>'. number_format($row[("receive")],2).'</td>
									<td>'. number_format($row[("issue_return")],2).'</td>
									<td>'. number_format($row[("trans_in")],2).'</td>
									<td>'. number_format($totalReceive,2).'</td>';

									if($totalReceive>0)  
			                        {
				                        $html .= '<td>'.number_format($totalReceiveAmount/$totalReceive,2).'</td>';
			                    	}
			                    	else 
			                    	{
			                    		$html .= '<td>0.00</td>';
			                    	}
			                        $html .='<td>'. number_format($totalReceiveAmount,2).'</td>
			                        <td>'. number_format($row[("issue")],2).'</td>
			                        <td>'. number_format($row[("receive_return")],2).'</td>
			                        <td>'. number_format($row[("trans_out")],2).'</td>
			                        <td>'. number_format($totalIssue,2).'</td>';
		                            
		                            if($totalReceive>0)  
			                        {
				                        $html .= '<td>'.number_format($totalIssueAmount/$totalIssue,2).'</td>';
			                    	}
			                    	else 
			                    	{
			                    		$html .= '<td>0.00</td>';
			                    	}
			                        $html .='<td>'.number_format($totalIssueAmount,2).'</td>
			                        <td>'.number_format($closingStock,2).'</td>';

		                            if(number_format($closingStock,2)>0)  
			                        {
				                        $html .= '<td>'.number_format($closingStockAmount/$closingStock,4).'</td>';
			                    	}
			                    	else 
			                    	{
			                    		$html .= '<td>0.00</td>';
			                    	}
			                    	$html .='<td>'.number_format($closingStockAmount,2).'</td>';
		                        $html .= '</tr>';											
		                        $i++;
							}
							else if($value_with ==0)
							{
								$grandTotalOpeningStockQty +=$openingStockQty;
								$grandTotalAmount +=$openingStockAmt; 
								$grandTotalReceive +=$row[("receive")];  
								$grandTotalIssueReturn +=$row[("issue_return")];
								$grandTotalTransferIn +=$row[("trans_in")];
								$grandTotalTotalReceive +=$totalReceive; 
								$grandTotalTotalTotalReceiveAmount += $totalReceiveAmount;
								$grandTotalIssue +=$row[("issue")];
								$grandTotalReceiveReturn +=$row[("receive_return")];
								$grandTotalTransOut +=$row[("trans_out")];
								$grandTotalTotalIssue +=$totalIssue;
								$grandTotalTotalIssueAmount +=$totalIssueAmount;
								$grandTotalClosingStock +=$closingStock;
								$grandTotalClosingStockAmount +=$closingStockAmount;
								if($get_up_qnty_check==1)
								{
									$html .='<tr id="tr_'. $i.'">    
		                            	<td>'. $i.'</td>
		                            	<td>'. $row[('prod_id')].'</td>
		                            	<td>'. $itemgroupArr[$row[("item_group_id")]].'</td>
			                            <td>'. $row[("item_description")].'</td>
			                            <td>'. $store_name_arr[$row[("store_id")]].'</td>
			                            <td>'. $unit_of_measurement[$row[("unit_of_measure")]].'</td>
			                            <td>'. number_format($openingStockQty,2).'</td>';
			                            if($openingStockQty>0)  
				                        {
					                        $html .= '<td>'.number_format($openingStockAmt/$openingStockQty,2).'</td>
					                         		  <td>'.number_format($openingStockAmt,2).'</td>';
				                    	}
				                    	else 
				                    	{
				                    		$html .= '<td>0.00</td>
				                    				  <td>0.00</td>';
				                    	}
				                    	$html .=
										'<td>'. number_format($row[("receive")],2).'</td>
										<td>'. number_format($row[("issue_return")],2).'</td>
										<td>'. number_format($row[("trans_in")],2).'</td>
										<td>'. number_format($totalReceive,2).'</td>';

										if($totalReceive>0)  
				                        {
					                        $html .= '<td>'.number_format($totalReceiveAmount/$totalReceive,2).'</td>';
				                    	}
				                    	else 
				                    	{
				                    		$html .= '<td>0.00</td>';
				                    	}
				                        $html .='<td>'. number_format($totalReceiveAmount,2).'</td>
				                        <td>'. number_format($row[("issue")],2).'</td>
				                        <td>'. number_format($row[("receive_return")],2).'</td>
				                        <td>'. number_format($row[("trans_out")],2).'</td>
				                        <td>'. number_format($totalIssue,2).'</td>';
			                            
			                            if($totalReceive>0)  
				                        {
					                        $html .= '<td>'.number_format($totalIssueAmount/$totalIssue,2).'</td>';
				                    	}
				                    	else 
				                    	{
				                    		$html .= '<td>0.00</td>';
				                    	}
				                        $html .='<td>'.number_format($totalIssueAmount,2).'</td>
				                        <td>'.number_format($closingStock,2).'</td>';

			                            if(number_format($closingStock,2)>0)  
				                        {
					                        $html .= '<td>'.number_format($closingStockAmount/$closingStock,4).'</td>';
				                    	}
				                    	else 
				                    	{
				                    		$html .= '<td>0.00</td>';
				                    	}
				                    	$html .='<td>'.number_format($closingStockAmount,2).'</td>';
			                        $html .= '</tr>';											
									$i++;
								}
							}
						}
					}
					
					if($cbo_search_by==1)
	                {
	                	$colspan= 7;
	                }
	                else 
                	{
                		$colspan= 6;
                	}
					$html .='<tr bgcolor="#CCCCCC" style="font-weight:bold">
		                <td colspan="'.$colspan.'" align="right"><strong>Grand Total</strong>
	                	</td>
	                	<td>'. number_format($grandTotalOpeningStockQty,2).'</td>
		                <td>&nbsp;</td>
		                <td>'. number_format($grandTotalAmount,2).'</td>
		                <td>'. number_format($grandTotalReceive,2).'</td>
		                <td>'. number_format($grandTotalIssueReturn,2).'</td>
		                <td>'. number_format($grandTotalTransferIn,2).'</td>
		                <td>'. number_format($grandTotalTotalReceive,2).'</td>
		                <td>&nbsp;</td>
		                <td>'. number_format($grandTotalTotalTotalReceiveAmount,2).'</td>
		                <td>'. number_format($grandTotalIssue,2).'</td>
		                <td>'. number_format($grandTotalReceiveReturn,2).'</td>
		                <td>'. number_format($grandTotalTransOut,2).'</td>
		                <td>'. number_format($grandTotalTotalIssue,2).'</td>
		                <td>&nbsp;</td>
		                <td>'. number_format($grandTotalTotalIssueAmount,2).'</td>
		                <td>'. number_format($grandTotalClosingStock,2).'</td>
		                <td>&nbsp;</td>
		                <td>'. number_format($grandTotalClosingStockAmount,2).'</td> 
					</tr>  
				</table>';
			/* </div> */
		}
		else // Store Wise
		{
			$html .='<table style="width:'.$table_width.'px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_header_2" rules="all" align="left"> 
				<thead> 
					<tr>
						<th rowspan="2">SL</th>
						<th rowspan="2">Prod.ID</th>
						<th rowspan="2">Buyer</th>
						<th colspan="2">Description</th>
						<th rowspan="2">UOM</th>
						<th rowspan="2">Opening Stock</th>
						<th rowspan="2">Avg Rate</th>
						<th rowspan="2">Amount</th>';

						foreach($storeIdArr as $storeId)
						{
							$html .='<th colspan="3">'.$store_name_arr[$storeId].'</th>'; 
						}
						$html .='<th rowspan="2">Closing Stock</th>
	                    <th rowspan="2">Avg Rate</th>
						<th rowspan="2">Closing Amount</th> 
					</tr>
					
					<tr>
						<th>Item Group</th>
						<th>Item Description</th>';

						foreach($storeIdArr as $storeId)
						{
							$html .='<th>Quantity</th>
									 <th>Avg Rate</th>
									 <th>Amount</th>';
						}

					$html .='</tr>
				</thead>
			</table>';
			
			/*<div style="width:<? echo $div_width; ?>px; max-height:350px; overflow-y:scroll;" id="scroll_body" align="left">*/
				$html .='<table style="width:'.$table_width.'px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left">';
					 
					foreach($store_data as $buyer_id=>$buyer_data) 
					{ 
						foreach($buyer_data as $prod_id=>$prod_data) 
						{
							$openingStockQty = $prod_data["opening_total_receive"]-$prod_data["opening_total_issue"];
							$opening_amount = $prod_data["opening_rcv_amt"]-$prod_data["opening_issue_amt"];
							$grand_opening_stock+=$openingStockQty;
							$grand_opening_amount+=$opening_amount;
							
							$html .='<tr id="tr_'. $i.'"> 
		                        <td>'. $i.'</td>
		                        <td>'. $prod_id.'</td>
		                        <td>'. $buyerArr[$buyer_id].'</td>
		                        <td>'. $itemgroupArr[$prod_data["item_group_id"]].'</td>
		                        <td>'. $prod_data["item_description"].'</td>
		                        <td>'. $unit_of_measurement[$prod_data["unit_of_measure"]].'</td>
		                        <td>'. number_format($openingStockQty,2).'</td>';

		                       	if($openingStockQty>0)  
		                        {
			                        $html .= '<td>'.number_format($opening_amount/$openingStockQty,4).'</td>';
		                    	}
		                    	else 
		                    	{
		                    		$html .= '<td>0.00</td>';
		                    	}
		                    	$html .='<td>'. number_format($opening_amount,2).'</td>';
                               	
                               
                               $total_store_qnty=$colsing_stock_qnty=$colsing_stock_amt= $total_store_qnty_amt=0;
                               foreach($storeIdArr as $storeId)
                               {
                                   	$running_qnty=0;
                                   	$running_qnty=(($store_wise_data[$buyer_id][$prod_id][$storeId]["receive"]+$store_wise_data[$buyer_id][$prod_id][$storeId]["issue_return"]+$store_wise_data[$buyer_id][$prod_id][$storeId]["trans_in"])-($store_wise_data[$buyer_id][$prod_id][$storeId]["issue"]+$store_wise_data[$buyer_id][$prod_id][$storeId]["receive_return"]+$store_wise_data[$buyer_id][$prod_id][$storeId]["trans_out"]));
                                   	$total_store_qnty+=$running_qnty;
                                   	$running_amt=(($store_wise_data[$buyer_id][$prod_id][$storeId]["receive_amt"]+$store_wise_data[$buyer_id][$prod_id][$storeId]["issue_return_amt"]+$store_wise_data[$buyer_id][$prod_id][$storeId]["trans_in_amt"])-($store_wise_data[$buyer_id][$prod_id][$storeId]["issue_amt"]+$store_wise_data[$buyer_id][$prod_id][$storeId]["receive_return_amt"]+$store_wise_data[$buyer_id][$prod_id][$storeId]["trans_out_amt"]));
                                    $total_store_qnty_amt+=$running_amt;
                                   
                                   	$html .='<td align="right">'.number_format($running_qnty,2).'</td>';

                                   	if($running_qnty>0)  
			                        {
				                        $html .= '<td>'.number_format($running_amt/$running_qnty,4).'</td>';
			                    	}
			                    	else 
			                    	{
			                    		$html .= '<td>0.00</td>';
			                    	}
			                    	$html .='<td>'. number_format($running_amt,2).'</td>';

                                	$grand_store_data[$storeId]["qnty"]+=$running_qnty;
                                	$grand_store_data[$storeId]["amt"]+=$running_amt; 
                               }
                               $colsing_stock_qnty=$openingStockQty+$total_store_qnty;
                               $colsing_stock_amt=$opening_amount+$total_store_qnty_amt;
                               $grand_closing_qnty+=$colsing_stock_qnty;
                               $grand_closing_amt+=$colsing_stock_amt;
                               
                               $html .='<td align="right">'. number_format($colsing_stock_qnty,2).'</td>';

                               if($colsing_stock_qnty>0)  
		                        {
			                        $html .= '<td>'.number_format($colsing_stock_amt/$colsing_stock_qnty,4).'</td>';
		                    	}
		                    	else 
		                    	{
		                    		$html .= '<td>0.00</td>';
		                    	}
		                    	$html .='<td>'. number_format($colsing_stock_amt,2).'</td>
                            </tr>';
                            $i++;      
						}
					}
					
					$html .='<tr bgcolor="#CCCCCC" style="font-weight:bold">
						<th colspan="6" align="right">Grand Total: </th>
						<th align="right">'.number_format($grand_opening_stock,2).'</th>
						<th></th>
						<th align="right">'.number_format($grand_opening_stock,2).'</th>';


						foreach($storeIdArr as $storeId)
						{
							$html .= '<td>'.number_format($grand_store_data[$storeId]["qnty"],2).'</td>
				                      <td></td>
				                      <td>'.number_format($grand_store_data[$storeId]["amt"],2).'</td>';
						}

						$html .='<th align="right">'.number_format($grand_closing_qnty,2).'</th>
						<th></th>
						<th align="right">'.number_format($grand_closing_amt,2).'</th> 
					</tr>
				</table>';
			/*</div>*/
		}
    /*</div>*/
    
    foreach (glob("swcsr_*.xls") as $filename) {
		@unlink($filename);
	}
	$name=time();
	$filename="swcsr_".$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$filename####$filename";
	exit();
}

if ($action[0]=="stock_popup_details") 
{
	echo load_html_head_contents("Stock Details", "../../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
	//echo $prod_id.test;die;
	$prod_id = str_replace("'","",$prod_id);
	$store_id = str_replace("'","",$store_id);
	$date_form = str_replace("'","",$date_form);
	$date_to = str_replace("'","",$date_to);
	$uom = str_replace("'","",$uom);
	$buyer = str_replace("'","",$buyer);
	?>
	<fieldset style="width:833px">
		<legend>Item Details</legend>
		<table width="815" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<th width="50">SL</th>
				<th width="120">Buyer</th>
				<th width="110">Style</th>
				<th width="110">Job</th>
				<th width="110">Season</th>
                <th width="110">Order</th>
				<th width="100">Opening Stock Qty.</th>
                <th>Closing Stock Qty.</th>
			</thead>
		</table>
		<div style="width:833px; overflow-y:scroll; max-height:300px" id="scroll_body">
			<table width="815" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
				<?
				$i = 1;
				$tot_recv_qnty = '';
				$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
				$season_arr = return_library_array("select id, season_name from lib_buyer_season", 'id', 'season_name');
				$store_cond="";
				if($store_id>0) $store_cond=" and a.store_id=$store_id";
				if($uom>0) $uom_cond=" and  a.cons_uom=$uom";
				if($buyer>0) $buyer_cond=" and  d.buyer_name=$buyer";
				$sql = "select c.id as po_id, d.buyer_name, d.style_ref_no, d.style_ref_no, d.job_no, d.season_matrix, d.season_buyer_wise, c.po_number,
				sum(case when b.trans_type in(1,4,5) and a.transaction_date < '$date_form' then b.quantity else 0 end) as opening_rcv,
				sum(case when b.trans_type in(2,3,6) and a.transaction_date < '$date_form' then b.quantity else 0 end) as opening_issue,
				sum(case when b.trans_type in(1,4,5) and a.transaction_date <= '$date_to' then b.quantity else 0 end) as closing_rcv,
				sum(case when b.trans_type in(2,3,6) and a.transaction_date <= '$date_to' then b.quantity else 0 end) as closing_issue 
				from inv_transaction a, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d
				where a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.prod_id=$prod_id and b.prod_id=$prod_id $store_cond $uom_cond $buyer_cond
				group by c.id , d.buyer_name, d.style_ref_no, d.job_no, d.season_matrix, d.season_buyer_wise, c.po_number";
				//echo $sql;die;
				$result = sql_select($sql);
				foreach ($result as $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";

					$opening_qty = $row[csf("opening_rcv")]-$row[csf("opening_issue")];
					$closing_qty = $row[csf("closing_rcv")]-$row[csf("closing_issue")];
					$tot_opening_qty += $opening_qty;
					$tot_closing_qty += $closing_qty;
					
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="50" align="center"><? echo $i; ?></td>
						<td width="120"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?>&nbsp;</p></td>
						<td width="110"><p><? echo $row[csf("style_ref_no")]; ?>&nbsp;</p></td>
                        <td width="110" align="center"><p><? echo $row[csf("job_no")]; ?>&nbsp;</p></td>
                        <td width="110" align="center" title="<?  echo "seson matrix :".$row[csf("season_matrix")]."seson buyer :".$row[csf("season_buyer_wise")];?>" ><p><? echo $season_arr[$row[csf("season_buyer_wise")]]; ?>&nbsp;</p></td>
                        <td width="110" align="center"><p><? echo $row[csf("po_number")]; ?>&nbsp;</p></td>
						<td width="100" align="right"><? echo number_format($opening_qty, 2); ?>&nbsp;</td>
						<td align="right"><? echo number_format($closing_qty, 2); ?>&nbsp;</td>
					</tr>
					<?
					$i++;
				}
				?>
				<tfoot>
					<th colspan="6">Total</th>
					<th><? echo number_format($tot_opening_qty, 2); ?>&nbsp;</th>
					<th><? echo number_format($tot_closing_qty, 2); ?>&nbsp;</th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}
?>