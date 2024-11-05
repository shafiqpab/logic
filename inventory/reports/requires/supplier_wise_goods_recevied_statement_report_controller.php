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

//load drop down Buyer
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/supplier_wise_goods_recevied_statement_report_controller', this.value, 'load_drop_down_season', 'season_td');" ,0); 
	exit();
}
if ($action=="load_drop_down_supplier")
{	
	echo create_drop_down( "cbo_supplier", 80, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type=2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "-- Select --", 0, "","" );
	exit();	
}


if ($action=="load_drop_down_season")
{
	//echo "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC";die;
	echo create_drop_down( "cbo_season_id", 80, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
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
				if($('#tr_'+str_or).css("display")!="none")
				{
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

				if($('#tr_'+str).css("display")!="none")
				{
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


//supplier search------------------------------//
if($action=="supplier_popup")
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
				if($('#tr_' + str_or).css("display") !='none')
				{
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
	if($buyer!=0) $buyer_cond="and b.buyer_name=$buyer"; else $buyer_cond="";
	if($style_all!="") $style_cond="and b.id in($style_all)"; else $style_cond="";
	
	 $sql = "select c.supplier_name,c.id,c.short_name from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name,c.short_name order by c.supplier_name";
	//echo $sql; die;
	echo create_list_view("list_view", "Supplier Name,Short Name","150,80","350","310",0, $sql , "js_set_value", "id,supplier_name", "", 1, "0", $arr, "supplier_name,short_name", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	
	
	?>
    <script language="javascript" type="text/javascript">
	var txt_supplier_id_no='<? echo $txt_supplier_id_no;?>';
	var txt_supplier_id='<? echo $txt_supplier_id;?>';
	var txt_supplier='<? echo $txt_supplier;?>';
	//alert(style_id);
	if(txt_supplier_id_no!="")
	{
		var txt_supplier_id_no_arr=txt_supplier_id_no.split(",");
		var txt_supplier_id_arr=txt_supplier_id.split(",");
		var txt_supplier_arr=txt_supplier.split(",");
		var txt_supplier_ref="";
		for(var k=0;k<txt_supplier_id_no_arr.length; k++)
		{
			txt_supplier_ref=txt_supplier_id_no_arr[k]+'_'+txt_supplier_id_arr[k]+'_'+txt_supplier_arr[k];
			js_set_value(txt_supplier_ref);
		}
	}
	</script>
	<?
	exit();
}

//Item search------------------------------//
if($action=="item_item_popup")
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
				if($('#tr_' + str_or).css("display") !='none')
				{
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
	//$buyer=str_replace("'","",$buyer);
	//$company=str_replace("'","",$company);
	if($cbo_item_category!=0) $cat_cond=" and c.item_category in($cbo_item_category)"; else $cat_cond="";
	$sql = "select c.item_name,c.id from  lib_item_group c where   c.status_active=1 and c.is_deleted=0 $cat_cond group by c.id, c.item_name order by c.item_name";
	//echo $sql; die;
	echo create_list_view("list_view", "Item Name","150","350","310",0, $sql , "js_set_value", "id,item_name", "", 1, "0", $arr, "item_name", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	exit();
}
//Wo Order search------------------------------//
if($action=="item_wo_order_popup")
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

				if($('#tr_'+str_or).css("display")!="none")
				{
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
	//$txt_supplier_id=explode(",",$txt_supplier_id);
	if($txt_supplier_id!="") $supplier_cond=" and a.supplier_id in($txt_supplier_id)";else $supplier_cond="";
	//print_r( $txt_supplier_id);
	$category=explode(",",$cbo_item_category);
	foreach($category as $cat_id)
	{
		if($cat_id==4) 
		{ 
			$cat_type=4;
		}
	}
	
	if($company!=0) $company_cond=" and a.company_id in($company)"; else $company_cond="";
	if($cbo_item_category!=0) $category_cond=" and a.item_category in($cat_type)"; else $category_cond="";
	//echo $cbo_item_category;
	if($cat_type==4)
	{
		$sql=("select a.id, a.booking_no,a.booking_no_prefix_num from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and  a.item_category=4 and a.booking_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $company_cond $supplier_cond group by a.id, a.booking_no,a.booking_no_prefix_num order by  a.booking_no");
		
		echo create_list_view("list_view", "Wo Order No,Wo Prefix No","150,100","350","250",0, $sql , "js_set_value", "id,booking_no_prefix_num", "", 1, "0", $arr, "booking_no,booking_no_prefix_num", "","setFilterGrid('list_view',-1)","0","",1) ;
	}
	else
	{
		if($company!=0) $company_cond=" and a.company_name in($company)"; else $company_cond="";
	 $sql = "select a.wo_number,a.id,a.wo_number_prefix_num,a.requisition_no from  wo_non_order_info_mst a,wo_non_order_info_dtls b where   a.status_active=1 and a.is_deleted=0 $company_cond $category_cond group by a.wo_number,a.id,a.wo_number_prefix_num,a.requisition_no  order by a.id";
	 echo create_list_view("list_view", "Wo Order No, Req. No","150,100","350","310",0, $sql , "js_set_value", "id,wo_number_prefix_num", "", 1, "0", $arr, "wo_number,requisition_no", "","setFilterGrid('list_view',-1)","0","",1) ;
	}
	//echo $sql; die;
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	
	exit();
}

$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$buyer_name_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
$po_number_arr=return_library_array( "select id, po_number from   wo_po_break_down",'id','po_number');
$lib_item_group=return_library_array( "select id, item_name from  lib_item_group",'id','item_name');
$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
$season_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');


//report generated here--------------------//
if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_item_cat=str_replace("'","",$cbo_item_cat);
	$txt_item_id=str_replace("'","",$txt_item_id);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_style_ref_id=str_replace("'","",$txt_style_ref_id);
	$txt_order=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$rptType=str_replace("'","",$rptType);
	$cbo_currency=str_replace("'","",$cbo_currency);
	$txt_wo_order_id=str_replace("'","",$txt_wo_order_id);
	$cbo_season_id=str_replace("'","",$cbo_season_id);
	
	//echo $txt_supplier_id;
	//echo $cbo_currency;die;
	if($db_type==0)
	{
		if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.transaction_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' "; else $date_cond=""; 
		
	}
	else
	{
		if( $txt_date_from!="" && $txt_date_to!="" ) $date_cond="and a.transaction_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' "; else $date_cond="";	
	}
	$user_name_arr=return_library_array( "select id, user_name from  user_passwd",'id','user_name');
	//echo $category_yarn.'=='.$category_finish;

	/*$pi_num_arr=array();
	$pi_sql=sql_select("select a.id, a.pi_number,b.quantity from com_pi_master_details a,com_pi_item_details b where a.id=b.pi_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($pi_sql as $row)
	{
		$pi_num_arr[$row[csf("id")]]["pi_number"]=$row[csf("pi_number")];
		$pi_num_arr[$row[csf("id")]]["quantity"]=$row[csf("quantity")];
		
	}*/
	
	$wo_num_arr=array();
	$wo_sql=sql_select("select a.id, a.wo_number, a.supplier_id, b.amount,b.rate, b.supplier_order_quantity as quantity, a.currency_id, a.wo_date 
	from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.entry_form=146 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($wo_sql as $row)
	{
		$wo_num_arr[$row[csf("id")]]["wo_number"]=$row[csf("wo_number")];
		$wo_num_arr[$row[csf("id")]]["quantity"]+=$row[csf("quantity")];
		$wo_num_arr[$row[csf("id")]]["amount"]+=$row[csf("amount")];
		if($row[csf("currency_id")]==1)
		{
			$wo_num_arr[$row[csf("id")]]["wo_date_taka"]+=$row[csf("wo_date")];
		}
		else if($row[csf("currency_id")]==2)
		{
			$wo_num_arr[$row[csf("id")]]["wo_date_usd"]+=$row[csf("wo_date")];
		}
		
		//$wo_num_arr[$row[csf("id")]]["rate"]+=$row[csf("rate")];
	}
	//var_dump($transfer_num_arr);die;
	//echo $cbo_item_cat;die;
	
	
	if($rptType==1) //Summary
	{
		if($txt_style_ref!="")
		{
			if($txt_style_ref_id!="")
			{
				$style_ref_id="and a.id in($txt_style_ref_id)";
			}
			else
			{
				$style_ref_id="and a.job_no_prefix_num like'%$txt_style_ref'"; 
	
			}
		}
		else
		{
			 $style_ref_id="";
		}
		//echo $txt_style_ref_id;
		if($txt_order!="")
		{
			if($txt_order_id!="")
			{
				$order_id_cond="and b.id in($txt_order_id)";
			}
			else
			{
				$order_id_cond="and b.po_number='$txt_order'";
			}
		}
		else
		{
			$order_id_cond="";
		}
		//print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));//,4,5
		if($txt_item_id!="") $item_id_cond="and b.item_group_id in($txt_item_id)"; else $item_id_cond="";
		if($cbo_buyer_name!=0) $buyer_cond="and a.buyer_name=$cbo_buyer_name"; else $buyer_cond="";
		if($txt_supplier_id!="") $supplier_cond="and c.supplier_id in($txt_supplier_id)"; else $supplier_cond="";
		if($cbo_item_cat!="") $category_cond="and c.item_category in($cbo_item_cat)"; else $category_cond="";
		if($txt_wo_order_id!="") $wo_id_cond="and d.booking_id in($txt_wo_order_id)"; else $wo_id_cond="";
		if($cbo_buyer_name!=0) $buyer_cond="and a.buyer_name=$cbo_buyer_name"; else $buyer_cond="";
		$season_cond="";
		if($cbo_season_id>0) $season_cond=" and a.season_matrix=$cbo_season_id";
		
		$wo_booking_num_arr=array();
		$wo_sql=sql_select("select a.id, a.booking_no,a.supplier_id,a.booking_date,
		case when a.currency_id=1 then (b.amount) else 0 end as wo_amount_tk,
		case when a.currency_id=2 then (b.amount) else 0 end as wo_amount_usd,
		case when a.currency_id=1 then (a.booking_date) else null end as booking_date_tk,
		case when a.currency_id=2 then (a.booking_date) else null end as booking_date_usd,
		b.trim_group,b.wo_qnty as quantity 
		from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and  a.item_category=4 and a.booking_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach($wo_sql as $row)
		{
			$wo_booking_num_arr[$row[csf("id")]][$row[csf("supplier_id")]][$row[csf("trim_group")]]["date_tk"]=$row[csf("booking_date_tk")];
			$wo_booking_num_arr[$row[csf("id")]][$row[csf("supplier_id")]][$row[csf("trim_group")]]["date_usd"]=$row[csf("booking_date_usd")];
			
			$wo_booking_num_arr[$row[csf("id")]][$row[csf("supplier_id")]][$row[csf("trim_group")]]["booking_no"]=$row[csf("booking_no")];
			$wo_booking_num_arr[$row[csf("id")]][$row[csf("supplier_id")]][$row[csf("trim_group")]]["quantity"]+=$row[csf("quantity")];
			$wo_booking_num_arr[$row[csf("id")]][$row[csf("supplier_id")]][$row[csf("trim_group")]]["amount_tk"]+=$row[csf("wo_amount_tk")];
			$wo_booking_num_arr[$row[csf("id")]][$row[csf("supplier_id")]][$row[csf("trim_group")]]["amount_usd"]+=$row[csf("wo_amount_usd")];
		}
		
		if($db_type==0) $grop_po="group_concat( d.po_breakdown_id) AS po_id,group_concat( c.currency_id) AS currency_id ";
		else if($db_type==2)
		//$grop_po="listagg(cast(d.po_breakdown_id as varchar2(4000)),',') within group (order by d.po_breakdown_id) AS po_id,listagg(cast(c.currency_id as varchar2(4000)),',') within group (order by c.currency_id) AS currency_id";
		$grop_po="rtrim(xmlagg(xmlelement(e,d.po_breakdown_id,',').extract('//text()') order by d.po_breakdown_id).GetClobVal(),',') as po_id,
		rtrim(xmlagg(xmlelement(e,c.currency_id,',').extract('//text()') order by c.currency_id).GetClobVal(),',') as currency_id";
		if($db_type==0) $job_po="group_concat( a.job_no_prefix_num) AS job,group_concat( a.buyer_name) AS buyer_name";
		else if($db_type==2)$job_po="listagg(cast(a.job_no_prefix_num as varchar2(4000)),',') within group (order by a.job_no_prefix_num) AS job,listagg(cast(a.buyer_name as varchar2(4000)),',') within group (order by a.buyer_name) AS buyer_name";
		
		$poDataArray=sql_select("select b.id,b.po_number,$job_po, b.grouping from  wo_po_break_down b,wo_po_details_master a where  a.job_no=b.job_no_mst and a.company_name=$cbo_company_name and b.status_active=1 and b.is_deleted=0 $order_id_cond $buyer_cond $style_ref_id $season_cond group by  b.id,b.po_number,b.grouping ");// and a.season like '$txt_season'
		$job_array=array(); $all_job_id='';
		foreach($poDataArray as $row)
		{
			$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$job_array[$row[csf('id')]]['job']=$row[csf('job')];
			$job_array[$row[csf('id')]]['grouping']=$row[csf('grouping')];
			if($all_po_id=="") $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')]; //echo $all_po_id;
		} //echo $all_po_id;
		
		
		if($season_cond!="" && count($poDataArray)<1)
		{
			echo '<p style="font-size:18px; font-weight:bold; color:red;">No Data Found.<p>';die;
		}
		
		if($txt_style_ref_id!='' || $txt_style_ref!='' || $cbo_buyer_name!=0 || $txt_order_id!='' ||  $txt_order!='')
		{
			if($all_po_id!='') $po_cond_id=" and d.po_breakdown_id in($all_po_id)";else $po_cond_id=""; 
		}
		else
		{
			$po_cond_id="";	
		}
		//echo $po_cond_id;
		$all_mrr_id='';
		$order_data_arr=array();
		$sql_order="select $grop_po,c.recv_number 
		from inv_transaction a, product_details_master b, inv_receive_master c, order_wise_pro_details d 
		where a.mst_id=c.id and a.prod_id=b.id and d.trans_id=a.id and d.entry_form in(24) and a.item_category=4 and a.company_id=$cbo_company_name and a.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.receive_basis in(2) $po_cond_id group by c.recv_number";
		$sql_res=sql_select($sql_order);
		foreach($sql_res as $row)
		{
			$row[csf('po_id')]=$row[csf('po_id')]->load();
			$row[csf('currency_id')]=$row[csf('currency_id')]->load();
			$order_data_arr[$row[csf('recv_number')]]['order']=$row[csf('po_id')];	
			$order_data_arr[$row[csf('recv_number')]]['curr']=$row[csf('currency_id')];	
			if($all_mrr_id=="") $all_mrr_id="'".$row[csf('recv_number')]."'"; else $all_mrr_id.=","."'".$row[csf('recv_number')]."'";
		}
		
		
		
		//echo $all_mrr_id;
		if($txt_style_ref_id!='' || $txt_style_ref!='' || $cbo_buyer_name!=0 || $txt_order_id!='' ||  $txt_order!='' || $season_cond!="")
		{
			if($all_mrr_id!='') 
			{
				$all_mrr_cond_id=" and c.recv_number in($all_mrr_id)";
			}
			else 
			{
				echo '<p style="font-size:18px; font-weight:bold; color:red;">No Data Found.<p>';die;
			}
		}
		else
		{
			$all_mrr_cond_id="";	
		}

		if($db_type==0) $group_concat="group_concat( distinct d.booking_id) AS booking_id,group_concat( distinct c.recv_number) AS recv_number,group_concat( distinct a.order_uom) AS order_uom,
		GROUP_CONCAT( CASE WHEN c.currency_id =1 THEN c.receive_date ELSE NULL END  ORDER BY c.receive_date ASC SEPARATOR ',') AS receive_date_tk, GROUP_CONCAT( CASE WHEN c.currency_id =2 THEN c.receive_date ELSE NULL END  ORDER BY c.receive_date ASC SEPARATOR ',') AS receive_date_usd";
		else if($db_type==2)  
		// $group_concat="listagg(cast(d.booking_id as varchar2(4000)),',') within group (order by d.booking_id) AS booking_id,listagg(cast(c.recv_number as varchar2(4000)),',') within group (order by c.recv_number) AS recv_number,listagg(cast(a.order_uom as varchar2(4000)),',') within group (order by a.order_uom) AS order_uom,listagg(case when c.currency_id =1 then c.receive_date end, ',') within group (order by c.receive_date) receive_date_tk,
	 	// listagg(case when c.currency_id =2 then c.receive_date end, ',') within group (order by c.receive_date) receive_date_usd";

		$group_concat="
		rtrim(xmlagg(xmlelement(e,d.booking_id,',').extract('//text()') order by d.booking_id).GetClobVal(),',') as booking_id,
		rtrim(xmlagg(xmlelement(e,c.recv_number,',').extract('//text()') order by c.recv_number).GetClobVal(),',') as recv_number,
		rtrim(xmlagg(xmlelement(e,a.order_uom,',').extract('//text()') order by a.order_uom).GetClobVal(),',') as order_uom,
		rtrim(xmlagg(xmlelement(e,c.receive_date,',').extract('//text()') order by c.receive_date).GetClobVal(),',') as all_rcv_date";
	
		// $sql_access="select  c.item_category, c.supplier_id ,b.item_group_id, c.entry_form, $group_concat,
		// sum(case when c.currency_id=1 then (a.order_qnty) else 0 end) as receive_qty_tk,
		// sum(case when c.currency_id=2 then (a.order_qnty) else 0 end) as receive_qty_usd,
		
		// sum(case when c.currency_id=1 then (a.order_amount) else 0 end) as receive_amt_tk,
		// sum(case when c.currency_id=2 then (a.order_amount) else 0 end) as receive_amt_usd
		
 		// from inv_transaction a, product_details_master b, inv_receive_master c , inv_trims_entry_dtls d
		// where  a.mst_id = c.id and a.prod_id = b.id and c.id = d.mst_id and a.id=d.trans_id and a.item_category in($cbo_item_cat) and a.company_id=$cbo_company_name and a.transaction_type in(1) and a.order_qnty>0 and a.status_active=1 and a.is_deleted=0 and c.receive_basis in(2)  $date_cond  $supplier_cond  $item_id_cond $wo_id_cond $all_mrr_cond_id 
		// group by c.item_category, c.supplier_id, b.item_group_id, c.entry_form order by c.supplier_id";

		$sql_access="select  c.item_category, c.supplier_id ,b.item_group_id, c.entry_form,c.currency_id, $group_concat,
		sum(case when c.currency_id=1 then (a.order_qnty) else 0 end) as receive_qty_tk,
		sum(case when c.currency_id=2 then (a.order_qnty) else 0 end) as receive_qty_usd,
		
		sum(case when c.currency_id=1 then (a.order_amount) else 0 end) as receive_amt_tk,
		sum(case when c.currency_id=2 then (a.order_amount) else 0 end) as receive_amt_usd
		
 		from inv_transaction a, product_details_master b, inv_receive_master c , inv_trims_entry_dtls d
		where  a.mst_id = c.id and a.prod_id = b.id and c.id = d.mst_id and a.id=d.trans_id and a.item_category in($cbo_item_cat) and a.company_id=$cbo_company_name and a.transaction_type in(1) and a.order_qnty>0 and a.status_active=1 and a.is_deleted=0 and c.receive_basis in(2)  $date_cond  $supplier_cond  $item_id_cond $wo_id_cond $all_mrr_cond_id 
		group by c.item_category, c.supplier_id, b.item_group_id, c.entry_form,c.currency_id order by c.supplier_id";
		 //echo $sql_access; die;

		// echo $sql_access; die;

		$sql_result=sql_select($sql_access);
		ob_start();	
		?>
		<div id="" style="width:1500px;"> 
			<table width="1500px" id="" align="left"> 
				<tr class="form_caption" style="border:none;">
					<td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold" >Supplier Wise Goods Receive Statement Summary &nbsp; <? echo $currency[$cbo_currency]; ?></td> 
					</tr>
					<tr style="border:none;">
					<td colspan="15" align="center" style="border:none; font-size:14px;">
					Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>
					</td>
				</tr>
			</table>
			<br />
			<table width="1500" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left"> 
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="100">Item Group</th>
						<th width="100">Work Order No</th>
						<th width="120">Buyer</th>
						<th width="130">Jobs</th>
						<th width="100">Internal ref</th>
						<th width="130">Orders</th>
						<th width="150">MRR No</th>
						<th width="100">UOM</th>
						<th width="80">WO Qty.</th>
						<th width="80">Recv. Qty.</th>
						<th width="80">Bal. Qty.</th>
						<th width="60">WO Rate</th>
						<th width="80">WO Amt.</th>
						<th width="80">Recv. Amt.</th>
						<th width="">Bal. Amt.</th>
					</tr>
				</thead>
			</table>
             
			<div style="width:1520px; overflow-y: scroll; max-height:240px;" id="scroll_body">
			<table width="1500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all"  align="left">
				<tbody>
					<?
					$i=1;$category_check_arr=array();$supplier_check_arr=array();$supplier_check_arr2=array();
					$j=1;$total_wo_qty=0;$total_recv_qty=0;$total_bal_qty=0;$total_wo_amount=0;$total_recv_amount=0;$total_bal_amount=0;
					foreach($sql_result as $val)
					{
						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";

						$val[csf("booking_id")]=$val[csf("booking_id")]->load();
						$val[csf("recv_number")]=$val[csf("recv_number")]->load();
						$val[csf("order_uom")]=$val[csf("order_uom")]->load();
						$val[csf("all_rcv_date")]=$val[csf("all_rcv_date")]->load();

						if($val[csf('currency_id')]==1){  
							$receive_date_tk = $val[csf('all_rcv_date')];
						}
						if($val[csf('currency_id')]==2){  
							$receive_date_usd = $val[csf('all_rcv_date')];
						}


						//$receive_date_cond_usd=explode(",",$val[csf('receive_date_usd')]);//change_date_format
						//$receive_date_cond_tk=explode(",",$val[csf('receive_date_tk')]);
						$receive_date_cond_tk=explode(",",$receive_date_tk);
						$receive_date_cond_usd=explode(",",$receive_date_usd);//change_date_format
						//print_r($receive_date_cond_tk);
						$mrr_currency_id=$val[csf('currency_id')];
						$booking_ids=array_unique(explode(",",$val[csf("booking_id")]));
						//print_r($booking_ids);
						$wo_no=''; $wo_uom='';
						$order_uom_cond=explode(",",$val[csf("order_uom")]);
						foreach($order_uom_cond as $uom)
						{
							 if($wo_uom=='') $wo_uom=$unit_of_measurement[$uom]; else $wo_uom.=",".$unit_of_measurement[$uom];	
						}
						$wo_qty=0;$wo_amount_tk=0;$wo_amount_usd=0;$wo_date_tk='';$wo_date_usd='';
						if($val[csf("entry_form")]==24)
						{
							foreach($booking_ids as $ids)
							{
								//echo $ids;
								if($wo_no=='') $wo_no=$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["booking_no"]; else $wo_no.=",".$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["booking_no"];
								
								if($wo_date_usd=='') $wo_date_usd=$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["date_usd"]; else $wo_date_usd.=",".$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["date_usd"];
								if($wo_date_tk=='') $wo_date_tk=$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["date_tk"]; else $wo_date_tk.=",".$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["date_tk"];
								$wo_booking_num_arr[$row[csf("id")]][$row[csf("supplier_id")]][$row[csf("trim_group")]]["date_usd"];
								
								$wo_qty+=$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["quantity"];
								$wo_amount_tk+=$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["amount_tk"];	
								$wo_amount_usd+=$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["amount_usd"];			
							}
							//print_r($wo_date_usd);
							$recve_mrr=explode(",",$val[csf("recv_number")]);
							foreach($recve_mrr as $recv)
							{
								$order_id=$order_data_arr[$recv]['order'];	
								$currency_id=$order_data_arr[$recv]['curr'];//$order_data_arr[$row[csf('recv_number')]]['curr']
							}
							$order_data=array_unique(explode(",",$order_id));
							
							//print_r($currency_id_data);
							$po_id='';$all_job='';$all_buyer='';$all_int_ref='';
							foreach( $order_data as $po)
							{
								if($all_job=='') $all_job=$job_array[$po]['job'];else  $all_job.=",".$job_array[$po]['job'];
								if($all_int_ref=='') $all_int_ref=$job_array[$po]['grouping'];else  $all_int_ref.=",".$job_array[$po]['grouping'];
								if($po_id=='') $po_id=$po_number_arr[$po];else $po_id.=','.$po_number_arr[$po];
								if($all_buyer=='') $all_buyer=$buyer_name_arr[$job_array[$po]['buyer']];else $all_buyer.=",".$buyer_name_arr[$job_array[$po]['buyer']];	
							}
							
						}
						else
						{
							foreach($booking_ids as $ids)
							{
								//echo $ids;
								if($wo_no=='') $wo_no=$wo_num_arr[$ids]["wo_number"]; else $wo_no.=",".$wo_num_arr[$ids]["wo_number"];
								
								if($wo_date_usd=='') $wo_date_usd=$wo_num_arr[$ids]["wo_date_usd"]; else $wo_date_usd.=",".$wo_num_arr[$ids]["wo_date_usd"];
								if($wo_date_tk=='') $wo_date_tk=$wo_num_arr[$ids]["wo_date_taka"]; else $wo_date_tk.=",".$wo_num_arr[$ids]["wo_date_taka"];
								
								$wo_qty+=$wo_num_arr[$ids]["quantity"];
								//$wo_amount_tk+=$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["amount_tk"];	
								$wo_amount_usd+=$wo_num_arr[$ids]["amount"];			
							}
						}
						
						$currency_id_data=array_unique(explode(",",$mrr_currency_id));
							
						$wo_date_tk=explode(",",$wo_date_tk);
						$wo_date_usd=explode(",",$wo_date_usd);
						
						if($cbo_currency==1) //Taka
						{
							$exchange_rate=0;
							foreach( $receive_date_cond_tk as $dates)
							{
							$exchange_rate=set_conversion_rate( 2, $dates );
							}
							$rev_amount=($val[csf("receive_amt_tk")])+$val[csf("receive_amt_usd")]*$exchange_rate;
							
							$wo_exchange_rate=0;//wo AMT
							foreach( $wo_date_usd as $wo_dates)
							{
							 $wo_exchange_rate=set_conversion_rate( 2, $wo_dates );
							}
							
							if($wo_amount_usd>0 && $wo_exchange_rate>0)
							{
								$wo_amount=($wo_amount_tk)+($wo_amount_usd*$wo_exchange_rate);
							}
							else
							{
								$wo_amount=$wo_amount_tk;
							}
							
								
						}
						else if($cbo_currency==2) //USD
						{
							$exchange_rate=0;
							foreach($receive_date_cond_usd as $dates)
							{
							 $exchange_rate=set_conversion_rate( 2, $dates );
							}
							$rev_amount=($val[csf("receive_amt_usd")])+$val[csf("receive_amt_tk")]/$exchange_rate;
						
							$wo_exchange_rate=0;//wo AMT
							foreach( $wo_date_tk as $wo_dates)
							{
							 $wo_exchange_rate=set_conversion_rate( 2, $wo_dates );
							}
							
							if($wo_amount_tk>0 && $wo_exchange_rate>0)
							{
								$wo_amount=($wo_amount_usd)+($wo_amount_tk/$wo_exchange_rate);
							}
							else
							{
								$wo_amount=$wo_amount_usd;
							}
						}
						
						if (!in_array($val[csf('item_category')],$category_check_arr) )
						{ 
							?>
							<tr bgcolor="#EFEFEF"><td colspan="16"><b>Item Category Name: <? echo $item_category[$val[csf('item_category')]]; if($cbo_season_id)  echo ",  Season Name:". $season_arr[$cbo_season_id]; ?></b></td></tr>
							<?
							$category_check_arr[]=$val[csf('item_category')]; 
						}
						 
						if (!in_array($val[csf('supplier_id')],$supplier_check_arr2) )
						{ 
							if($j!=1)
							{
								?>
								<tr bgcolor="#CCCCCC" >
									<td align="right" colspan="12"><b> Supplier Total: </b></td>
									<td align="right"><? echo number_format($tot_wo_amount,2);$tot_wo_amount=0; ?></td>
									<td align="right"><? echo number_format($tot_recv_amount,2);$tot_recv_amount=0; ?></td>
									<td align="right"><? echo number_format($tot_bal_amount,2);$tot_bal_amount=0; ?></td>
								</tr>
								<?
							} 
							$j++;
							$supplier_check_arr2[]=$val[csf('supplier_id')];
							?>
							<tr bgcolor="#EFEFEF"><td colspan="16"><b>Supplier Name: <? echo $supplier_arr[$val[csf('supplier_id')]]; ?></b></td></tr>
							<? 
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="100"><p><? echo $lib_item_group[$val[csf("item_group_id")]]; ?></p></td>
							<td width="100" align="center"><p><? $wo_no_all=implode(",",array_unique(explode(",",$wo_no)));echo $wo_no_all; ?></p></td>
							<td width="120"  align="center"><p>
							<?  echo implode(",",array_unique(explode(",",$all_buyer))); ?>
							</p></td>
							<td width="130"  align="center"><p><? echo implode(",",array_unique(explode(",",$all_job))); ?></p></td>               
							<td width="100"  align="center"><p><? echo implode(",",array_unique(explode(",",$all_int_ref))); ?></p></td>               
							<td width="130"  align="center"><p><? echo $po_id; ?></p></td>
							<td width="150"><p><? $recv_number=implode(",",array_unique(explode(",",$val[csf("recv_number")])));echo $recv_number; ?></p></td>
							<td width="100"><p><? $wo_uom_cond=implode(",",array_unique(explode(",",$wo_uom)));echo $wo_uom_cond; ?></p></td>
							<td width="80" align="right"><p><? $tot_wo_qty+=$wo_qty;echo number_format($wo_qty,2); ?></p></td>
							<td width="80" align="right" title="<? echo $val[csf("receive_amt")];?>"><p>
							<? $tot_receive_qty+=$val[csf("receive_qty_tk")]+$val[csf("receive_qty_usd")]; echo $val[csf("receive_qty_tk")]+$val[csf("receive_qty_usd")];
							//if($val[csf("currency_id")]==1) echo 'Taka';else echo 'USD';?>
							</p></td>
							<td width="80" align="right"><p>
							<?  $bal_qty=$wo_qty-($val[csf("receive_qty_tk")]+$val[csf("receive_qty_usd")]);echo number_format($bal_qty,2);
							$tot_bal_qty+=$bal_qty;
							?>
							</p></td>
							<td width="60"  align="center"><p><? 
							
							$wo_rate_conver=$rev_amount/($val[csf("receive_qty_tk")]+$val[csf("receive_qty_usd")]); 
							echo number_format($wo_rate_conver,3);	
							 ?></p></td>
							<td width="80" align="right"><p><? $tot_wo_amount+=$wo_amount;echo number_format($wo_amount,2); ?></p></td>
							<td width="80" align="right"><p><? 
							$recv_amount=$rev_amount;//($val[csf("receive_qty_tk")]+$val[csf("receive_qty_usd")])*$wo_rate_conver;
							echo number_format($recv_amount,2);
							$tot_recv_amount+=$recv_amount;
							; ?></p></td>
							<td width="" align="right"><p><? $bal_amount=$wo_amount-$recv_amount;echo number_format($bal_amount,2);  $tot_bal_amount+=$bal_amount;?></p></td>
						</tr>
						<?
						$total_wo_qty+=$wo_qty;
						$total_recv_qty+=$val[csf("receive_qty_tk")]+$val[csf("receive_qty_usd")];
						$total_bal_qty+=$bal_qty;
						$total_wo_amount+=$wo_amount;
						$total_recv_amount+=$recv_amount;
						$total_bal_amount+=$bal_amount;
						$i++;
					}
					?> 
					<tr bgcolor="#CCCCCC" >
						<td align="right" colspan="13"><b> Supplier Total: </b></td>
						<td align="right"><? echo number_format($tot_wo_amount,2);$tot_wo_amount=0; ?></td>
						<td align="right"><? echo number_format($tot_recv_amount,2);$tot_recv_amount=0; ?></td>
						<td align="right"><? echo number_format($tot_bal_amount,2);$tot_bal_amount=0; ?></td>
					</tr>  
				</tbody>
			</table>
			<table width="1500" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left"> 
				<tfoot>
					<tr>
						<th width="30"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="120"></th>
						<th width="130"></th>
						<th width="100"></th>
						<th width="130"></th>
						<th width="150"></th>
						<th width="100"></th>
						<th width="80"><? //echo number_format($total_wo_qty,2); ?></th>
						<th width="80"><? //echo number_format($total_recv_qty,2); ?></th>
						<th width="80"><? //echo number_format($total_bal_qty,2); ?></th>
						<th width="60">Grand Total</th>
						<th width="80"><? echo number_format($total_wo_amount,2); ?></th>
						<th width="80"><? echo number_format($total_recv_amount,2); ?></th>
						<th width=""><? echo number_format($total_bal_amount,2); ?></th>
					</tr>
				</tfoot>
			</table> 
		 </div>
		 <br />
		 <?
				
	}// Summary End
	else // Details Start
	{
		
			if($txt_style_ref!="")
			{
				if($txt_style_ref_id!="")
				{
					$style_ref_id="and a.id in($txt_style_ref_id)";
				}
				else
				{
					$style_ref_id="and a.job_no_prefix_num like'%$txt_style_ref'"; 
		
				}
			}
			else
			{
				 $style_ref_id="";
			}
			//echo $txt_style_ref_id;
			if($txt_order!="")
			{
				if($txt_order_id!="")
				{
					$order_id_cond="and b.id in($txt_order_id)";
				}
				else
				{
					$order_id_cond="and b.po_number='$txt_order'";
				}
			}
			else
			{
				$order_id_cond="";
			}
			//print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));//,4,5
			if($txt_item_id!="") $item_id_cond="and b.item_group_id in($txt_item_id)"; else $item_id_cond="";
			if($cbo_buyer_name!=0) $buyer_cond="and a.buyer_name=$cbo_buyer_name"; else $buyer_cond="";
			if($txt_supplier_id!="") $supplier_cond="and c.supplier_id in($txt_supplier_id)"; else $supplier_cond="";
			if($cbo_item_cat!="") $category_cond="and c.item_category in($cbo_item_cat)"; else $category_cond="";
			if($txt_wo_order_id!="") $wo_id_cond="and d.booking_id in($txt_wo_order_id)"; else $wo_id_cond="";
			if($cbo_buyer_name!=0) $buyer_cond="and a.buyer_name=$cbo_buyer_name"; else $buyer_cond="";
			if($cbo_season_id>0) $season_cond=" and a.season_matrix=$cbo_season_id";
			
			$wo_booking_num_arr=array();
			$wo_sql=sql_select("select a.id, a.booking_no,a.supplier_id,b.amount,b.trim_group,b.wo_qnty as quantity from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and  a.item_category=4 and a.booking_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			foreach($wo_sql as $row)
			{
				$wo_booking_num_arr[$row[csf("id")]][$row[csf("supplier_id")]][$row[csf("trim_group")]]["booking_no"]=$row[csf("booking_no")];
				$wo_booking_num_arr[$row[csf("id")]][$row[csf("supplier_id")]][$row[csf("trim_group")]]["quantity"]+=$row[csf("quantity")];
				$wo_booking_num_arr[$row[csf("id")]][$row[csf("supplier_id")]][$row[csf("trim_group")]]["amount"]+=$row[csf("amount")];
			}
			if($db_type==0) $grop_po="group_concat( d.po_breakdown_id) AS po_id,group_concat( c.currency_id) AS currency_id ";
			else if($db_type==2)//$grop_po="listagg(cast(d.po_breakdown_id as varchar2(4000)),',') within group (order by d.po_breakdown_id) AS po_id,listagg(cast(c.currency_id as varchar2(4000)),',') within group (order by c.currency_id) AS currency_id";
			$grop_po="rtrim(xmlagg(xmlelement(e,d.po_breakdown_id,',').extract('//text()') order by d.po_breakdown_id).GetClobVal(),',') as po_id,
			rtrim(xmlagg(xmlelement(e,c.currency_id,',').extract('//text()') order by c.currency_id).GetClobVal(),',') as currency_id";

			if($db_type==0) $job_po="group_concat( a.job_no_prefix_num) AS job,group_concat( a.buyer_name) AS buyer_name";
			else if($db_type==2)$job_po="listagg(cast(a.job_no_prefix_num as varchar2(4000)),',') within group (order by a.job_no_prefix_num) AS job,listagg(cast(a.buyer_name as varchar2(4000)),',') within group (order by a.buyer_name) AS buyer_name";
			$poDataArray=sql_select("select b.id, b.grouping, b.po_number,$job_po from  wo_po_break_down b, wo_po_details_master a where a.job_no=b.job_no_mst and a.company_name=$cbo_company_name and b.status_active=1 and b.is_deleted=0 $order_id_cond $buyer_cond $style_ref_id $season_cond group by b.id, b.grouping, b.po_number ");// and a.season like '$txt_season'
			$job_array=array(); $all_job_id='';
			foreach($poDataArray as $row)
			{
				$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
				$job_array[$row[csf('id')]]['job']=$row[csf('job')];
				$job_array[$row[csf('id')]]['grouping']=$row[csf('grouping')];
				if($all_po_id=="") $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')]; //echo $all_po_id;
			} //echo $all_po_id;
			
			if($season_cond!="" && count($poDataArray)<1)
			{
				echo '<p style="font-size:18px; font-weight:bold; color:red;">No Data Found.<p>';die;
			}
			
			if($txt_style_ref_id!='' || $txt_style_ref!='' || $cbo_buyer_name!=0 || $txt_order_id!='' ||  $txt_order!='')
			{
				if($all_po_id!='') $po_cond_id=" and d.po_breakdown_id in($all_po_id)";else $po_cond_id=""; 
			}
			else
			{
				$po_cond_id="";	
			}
			//echo $po_cond_id;
			$all_mrr_id='';
			$order_data_arr=array();
			$sql_order="select $grop_po,c.recv_number,b.item_group_id 
			from inv_transaction a, product_details_master b,inv_receive_master c,order_wise_pro_details d 
			where a.mst_id=c.id and a.prod_id=b.id and d.trans_id=a.id and d.entry_form in(24) and a.item_category=4 and a.company_id=$cbo_company_name and a.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.receive_basis in(2) $po_cond_id 
			group by c.recv_number,b.item_group_id";
			//echo $sql_order;die;
			$sql_res=sql_select($sql_order);
			foreach($sql_res as $row)
			{
				$row[csf('po_id')]=$row[csf('po_id')]->load();
				$row[csf('currency_id')]=$row[csf('currency_id')]->load();
				// $order_data_arr[$row[csf('recv_number')]]['order']=$row[csf('po_id')];	
				// $order_data_arr[$row[csf('recv_number')]]['curr']=$row[csf('currency_id')];	
				$order_data_arr[$row[csf('recv_number')]][$row[csf('item_group_id')]]['order']=$row[csf('po_id')];	
				$order_data_arr[$row[csf('recv_number')]][$row[csf('item_group_id')]]['curr']=$row[csf('currency_id')];	
				if($all_mrr_id=="") $all_mrr_id="'".$row[csf('recv_number')]."'"; else $all_mrr_id.=","."'".$row[csf('recv_number')]."'";
			}
			//echo $all_mrr_id;
			if($txt_style_ref_id!='' || $txt_style_ref!='' || $cbo_buyer_name!=0 || $txt_order_id!='' ||  $txt_order!='' ||  $season_cond!='')
			{
				if($all_mrr_id!='') 
				{
					$all_mrr_cond_id=" and c.recv_number in($all_mrr_id)";
				}
				else 
				{
					echo '<p style="font-size:18px; font-weight:bold; color:red;">No Data Found.<p>';die;
				}
			}
			else
			{
				$all_mrr_cond_id="";	
			}
	
			/*if($db_type==0) $group_concat="group_concat( distinct d.booking_id) AS booking_id";
			else if($db_type==2)  $group_concat="listagg(cast(d.booking_id as varchar2(4000)),',') within group (order by d.booking_id) AS booking_id";*/
			if($db_type==0)
			{
				$group_concat="group_concat( distinct d.booking_id) AS booking_id";
				$select_insert_date="DATE_FORMAT(c.insert_date,'%d-%m-%Y %H:%i:%S') as insert_date";
				$select_insert_time="DATE_FORMAT(c.insert_date,'%H:%i:%S') as insert_time";
			}
			else
			{
				$group_concat="listagg(cast(d.booking_id as varchar2(4000)),',') within group (order by d.booking_id) AS booking_id";
				$select_insert_date=" to_char(c.insert_date,'DD-MM-YYYY HH24:MI:SS') as insert_date";
				//HH24:MI:SS
				$select_insert_time=" to_char(c.insert_date,'HH24:MI:SS') as insert_time";
			} 
			$sql_access="SELECT a.item_category, c.supplier_id,b.item_group_id,c.recv_number,c.challan_no,a.order_uom,c.receive_date,c.currency_id,$group_concat,
			sum(case when a.transaction_type in(1) then a.order_qnty else 0 end) as receive_qty ,
			sum(case when a.transaction_type in(1) then (a.order_amount) else 0 end) as receive_amt,
			c.entry_form, c.inserted_by, $select_insert_date, $select_insert_time
			from inv_transaction a, product_details_master b, inv_receive_master c  , inv_trims_entry_dtls d
			where  a.mst_id = c.id and a.prod_id = b.id and c.id = d.mst_id and a.id=d.trans_id and a.item_category in($cbo_item_cat) and a.company_id=$cbo_company_name and a.transaction_type in(1) and a.order_qnty>0 and a.status_active=1 and a.is_deleted=0 and c.receive_basis in(2) $date_cond $supplier_cond $item_id_cond $wo_id_cond $all_mrr_cond_id 
			group by  a.item_category, c.supplier_id, b.item_group_id, c.currency_id, c.challan_no, a.order_uom, c.recv_number, c.receive_date, c.entry_form, c.inserted_by, c.insert_date
			order by c.supplier_id";
			// echo $sql_access;
			$sql_result=sql_select($sql_access);
			ob_start();	
			?>
			<div id="" style="width:1460px;">
				<table width="1460px" id="" align="left"> 
					<tr class="form_caption" style="border:none;">
						<td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold" >Supplier Wise Goods Receive Statement Details &nbsp; <? echo $currency[$cbo_currency]; ?></td> 
					</tr>
					<tr style="border:none;">
						<td colspan="15" align="center" style="border:none; font-size:14px;">
						Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>
						</td>
					</tr>
				</table>
				<br />
				<table width="1460" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left"> 
					<thead>
						<tr>
							<th width="30">SL</th>
							<th width="80">Rev. Date</th>
							<th width="120">MRR No</th>
							<th width="120">Item Group</th>
							<th width="130">Work Order No</th>
							<th width="120">Buyer</th>
							<th width="100">Jobs</th>
							<th width="100">Orders</th>
							<th width="100">Internal ref</th>
							<th width="80">Challan No</th>
							<th width="60">UOM</th>
							<th width="80">Rev. Qty.</th>
							<th width="60">Rev. Rate</th>
							<th width="100">Rev. Amt.</th>
							<th width="100">Insert Date</th>
							<th width="">Insert User</th>
							
						</tr>
					</thead>
				</table> 
	   
				<div style="width:1480px; overflow-y: scroll; max-height:240px;" id="scroll_body">
				<table width="1460" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all"  align="left">
					<tbody>
						<?
						$i=1;$category_check_arr=array();$supplier_check_arr=array();$supplier_check_arr2=array();
						$j=1;$total_wo_qty=0;$total_recv_qty=0;$total_bal_qty=0;$total_wo_amount=0;$total_recv_amount=0;$total_bal_amount=0;
						foreach($sql_result as $val)
						{
							if ($i%2==0)
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";
							$receive_date=$val[csf('receive_date')];//change_date_format
							//print_r($receive_date);
							$mrr_currency_id=$val[csf('currency_id')];
							$booking_ids=array_unique(explode(",",$val[csf("booking_id")]));
							$wo_no=''; $wo_uom='';
							$order_uom_cond=explode(",",$val[csf("order_uom")]);
							foreach($order_uom_cond as $uom)
							{
								 if($wo_uom=='') $wo_uom=$unit_of_measurement[$uom]; else $wo_uom.=",".$unit_of_measurement[$uom];	
							}
							
							$wo_qty=0;
							if($val[csf('entry_form')]==24)
							{
								foreach($booking_ids as $ids)
								{
									//echo $ids;
									if($wo_no=='') $wo_no=$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["booking_no"]; else $wo_no.=",".$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["booking_no"];
									//$wo_qty+=$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["quantity"];
									//$wo_amount=$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["amount"];
								}
								
								$recve_mrr=explode(",",$val[csf("recv_number")]);
								foreach($recve_mrr as $recv)
								{
									$order_id=$order_data_arr[$recv][$val[csf("item_group_id")]]['order'];	
									$currency_id=$order_data_arr[$recv][$val[csf("item_group_id")]]['curr'];//$order_data_arr[$row[csf('recv_number')]]['curr']
								}
								// $inter_ref=$job_array[$po]['grouping'];

								$order_data=array_unique(explode(",",$order_id));								
								$po_id='';$all_job='';$all_buyer='';$inter_ref='';
								foreach( $order_data as $po)
								{
									//echo $po;
									if($all_job=='') $all_job=$job_array[$po]['job'];else  $all_job.=",".$job_array[$po]['job'];
									if($inter_ref=='') $inter_ref=$job_array[$po]['grouping'];else  $inter_ref.=",".$job_array[$po]['grouping'];
									
									//$all_job=$job_array[$po]['buyer'];
									if($po_id=='') $po_id=$po_number_arr[$po];else $po_id.=','.$po_number_arr[$po];
									if($all_buyer=='') $all_buyer=$buyer_name_arr[$job_array[$po]['buyer']];else $all_buyer.=",".$buyer_name_arr[$job_array[$po]['buyer']];	
								}
								$order_data=array_unique(explode(",",$order_id));
							}
							else
							{
								foreach($booking_ids as $ids)
								{
									//echo $ids;
									if($wo_no=='') $wo_no=$wo_num_arr[$ids]["wo_number"]; else $wo_no.=",".$wo_num_arr[$ids]["wo_number"];
									//$wo_qty+=$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["quantity"];
									//$wo_amount=$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["amount"];
								}
							}
							
							
							$currency_id_data=array_unique(explode(",",$currency_id));
							
							foreach($currency_id_data as $curr_id)
							{
								if($curr==1) $curr_id=1;
								else if($curr==2) $curr_id=2;
							}
							//echo $curr_id;
							if($cbo_currency==1) //Taka
							{
								if($mrr_currency_id==2)
								{
									//$exchange_rate=0;
									//foreach( $receive_date_cond as $dates)
									//{
									$exchange_rate=set_conversion_rate( 2, $receive_date );
									//}
									
									$wo_rate=$val[csf("receive_amt")]/$val[csf("receive_qty")];
									$wo_rate_conver=$exchange_rate*$wo_rate;
								}
								else
								{
									$wo_rate_conver=$val[csf("receive_amt")]/$val[csf("receive_qty")];	
								}
							}
							else if($cbo_currency==2) //USD
							{
								//echo $val[csf("currency_id")];
								if($mrr_currency_id==1)
								{
									//$exchange_rate=0;
									//foreach( $receive_date_cond as $dates)
									//{
									$exchange_rate=set_conversion_rate( 2, $receive_date );
									//}
									$wo_rate=$val[csf("receive_amt")]/$val[csf("receive_qty")];
									$wo_rate_conver=$wo_rate/$exchange_rate;
								}
								else
								{
									$wo_rate_conver=$val[csf("receive_amt")]/$val[csf("receive_qty")];	
								}
							}
							//print_r($currency_id_data);
														
							if (!in_array($val[csf('item_category')],$category_check_arr) )
							{ 
								?>
								<tr bgcolor="#EFEFEF"><td colspan="16"><b>Item Category Name: <? echo $item_category[$val[csf('item_category')]]; if($cbo_season_id)  echo ",  Season Name:". $season_arr[$cbo_season_id]; ?></b></td></tr>
								 <?
								$category_check_arr[]=$val[csf('item_category')]; 
							} 
							if (!in_array($val[csf('supplier_id')],$supplier_check_arr2) )
							{ 
								if($j!=1)
								{
									?>
									<tr bgcolor="#CCCCCC" >
                                        <td colspan="13" align="right"><b>Supplier Total:</b></td>
                                        <td width="100" align="right"><b><? echo number_format($tot_recv_amount,2);$tot_recv_amount=0; ?></b></td>
                                        <td width="100"></td>
										<td width=""></td>
									</tr>
									<?
								} 
								$j++;
								$supplier_check_arr2[]=$val[csf('supplier_id')];
								?>
								<tr bgcolor="#EFEFEF">
									<td colspan="16"><b>Supplier Name: <? echo $supplier_arr[$val[csf('supplier_id')]]; ?></b></td>
								</tr>
								<? 
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><p><? echo $i; ?></p></td>
								<td width="80"><p><? echo change_date_format($receive_date,2);//$lib_item_group[$val[csf("item_group_id")]]; ?></p></td>
								<td width="120" align="center"><p><? echo $val[csf('recv_number')]; ?></p></td>
								<td width="120"  align="center"><p>
								<? echo $lib_item_group[$val[csf("item_group_id")]]; ?>
								</p></td>
								<td width="130"  align="center"><p><? $wo_no_all=implode(",",array_unique(explode(",",$wo_no)));echo $wo_no_all;//echo $all_job;//$buyer_name_arr[$val[csf("buyer_id")]]; ?></p></td>
								<td width="120"  align="center"><p><? 
								
								echo implode(",",array_unique(explode(",",$all_buyer)));//$po_id; ?></p></td>
								<td width="100"><p><? $job=implode(",",array_unique(explode(",",$all_job)));echo $job; ?></p></td>
								<td width="100"><p><? echo implode(",",array_unique(explode(",",$po_id)));//$wo_uom_cond=implode(",",array_unique(explode(",",$wo_uom)));echo $wo_uom_cond; ?></p></td>

								<td width="100"><p><? echo $inter_ref=implode(",",array_unique(explode(",",$inter_ref))); ?></p></td>

								<td width="80" align="right"><p><? echo $val[csf("challan_no")]; //$tot_wo_qty+=$wo_qty;echo number_format($wo_qty,2); ?></p></td>
								<td width="60" align="right" title="<? echo $unit_of_measurement[$val[csf("order_uom")]];//$val[csf("receive_amt")];?>"><p>
								<?  echo $unit_of_measurement[$val[csf("order_uom")]];
								//$tot_receive_qty+=$val[csf("receive_qty")];?>
							   </p></td>
								<td width="80" align="right" title="<? echo  $val[csf("receive_amt")];?>"><p>
								<?  $tot_receive_qty+=$val[csf("receive_qty")];echo number_format($val[csf("receive_qty")],4);//$bal_qty=$wo_qty-$val[csf("receive_qty")];echo number_format($bal_qty,2);
								//$tot_bal_qty+=$bal_qty;
								?>
								</p></td>
								<td width="60"  align="center" title="<? if($mrr_currency_id==1) echo 'Taka';else echo 'USD';?>"><p><?  
								echo number_format($wo_rate_conver,3);	
								//$wo_rate_conver//echo $wo_rate=number_format($val[csf("receive_amt")]/$val[csf("receive_qty")],2); ?></p></td>
								<td width="100" align="right"><p><? 
								$recv_amount=$val[csf("receive_qty")]*$wo_rate_conver;
								$tot_recv_amount+=$recv_amount;
								echo number_format($recv_amount,2); ?></p></td>
								<td width="100"><p><? echo change_date_format($val[csf("insert_date")])." ".$val[csf("insert_time")]; ?></p></td>
                                <td width=""><p><? echo $user_name_arr[$val[csf("inserted_by")]]; ?></p></td>
							</tr>
							<?
							$total_wo_qty+=$wo_qty;
							$total_recv_qty+=$val[csf("receive_qty")];
							$total_bal_qty+=$bal_qty;
							$total_wo_amount+=$wo_amount;
							$total_recv_amount+=$recv_amount;
							$total_bal_amount+=$bal_amount;
							$i++;
						}
						?>  
						<tr bgcolor="#CCCCCC" >
							<td colspan="13" align="right"><b>Supplier Total:</b></td>
							<td width="100" align="right"><b><? echo number_format($tot_recv_amount,2);$tot_recv_amount=0; ?></b></td>
							<td width="100"></td>
							<td width=""></td>
						</tr>
					</tbody>
				</table>
				<table width="1460" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left"> 
					<tfoot>
						<tr>
							<th width="30"></th>
							<th width="80"></th>
							<th width="120"></th>
							<th width="120"></th>
							<th width="130"></th>
							<th width="120"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="80"><? //echo number_format($total_wo_qty,2); ?></th>
							<th width="60"></th>
							<th width="80"><? //echo number_format($total_recv_qty,2); ?></th>
							<th width="60">Grand Total</th>
							<th width="100"><? echo number_format($total_recv_amount,2); ?></th>
							<th width="100"></th>
							<th width=""></th>
						</tr>
					</tfoot>
				</table> 
				 </div>
			</div>
			<?					
	}// Summary End
			
	foreach (glob("*.xls") as $filename) {
	if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	//echo "$html**$filename**$cbo_item_cat**$rptType";
	echo "$total_data**$filename"; 
	exit();
	
}


// report generate here for big amount of data (created for youth group-live)
if($action=="generate_report2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_item_cat=str_replace("'","",$cbo_item_cat);
	$txt_item_id=str_replace("'","",$txt_item_id);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_style_ref_id=str_replace("'","",$txt_style_ref_id);
	$txt_order=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$rptType=str_replace("'","",$rptType);
	$cbo_currency=str_replace("'","",$cbo_currency);
	$txt_wo_order_id=str_replace("'","",$txt_wo_order_id);
	$cbo_season_id=str_replace("'","",$cbo_season_id);
	
	//echo $txt_supplier_id;
	//echo $cbo_currency;die;
	if($db_type==0)
	{
		if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.transaction_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' "; else $date_cond=""; 
		
	}
	else
	{
		if( $txt_date_from!="" && $txt_date_to!="" ) $date_cond="and a.transaction_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' "; else $date_cond="";	
	}
	$user_name_arr=return_library_array( "select id, user_name from  user_passwd",'id','user_name');
	
	$wo_num_arr=array();
	$wo_sql=sql_select("select a.id, a.wo_number, a.supplier_id, b.amount,b.rate, b.supplier_order_quantity as quantity, a.currency_id, a.wo_date 
	from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.entry_form=146 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($wo_sql as $row)
	{
		$wo_num_arr[$row[csf("id")]]["wo_number"]=$row[csf("wo_number")];
		$wo_num_arr[$row[csf("id")]]["quantity"]+=$row[csf("quantity")];
		$wo_num_arr[$row[csf("id")]]["amount"]+=$row[csf("amount")];
		if($row[csf("currency_id")]==1)
		{
			$wo_num_arr[$row[csf("id")]]["wo_date_taka"]+=$row[csf("wo_date")];
		}
		else if($row[csf("currency_id")]==2)
		{
			$wo_num_arr[$row[csf("id")]]["wo_date_usd"]+=$row[csf("wo_date")];
		}
		
	}
	
	
	
	if($rptType==1) //Summary
	{
		if($txt_style_ref!="")
		{
			if($txt_style_ref_id!="")
			{
				$style_ref_id="and a.id in($txt_style_ref_id)";
			}
			else
			{
				$style_ref_id="and a.job_no_prefix_num like'%$txt_style_ref'"; 
	
			}
		}
		else
		{
			 $style_ref_id="";
		}
		//echo $txt_style_ref_id;
		if($txt_order!="")
		{
			if($txt_order_id!="")
			{
				$order_id_cond="and b.id in($txt_order_id)";
			}
			else
			{
				$order_id_cond="and b.po_number='$txt_order'";
			}
		}
		else
		{
			$order_id_cond="";
		}
		//print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));//,4,5
		if($txt_item_id!="") $item_id_cond="and b.item_group_id in($txt_item_id)"; else $item_id_cond="";
		if($cbo_buyer_name!=0) $buyer_cond="and a.buyer_name=$cbo_buyer_name"; else $buyer_cond="";
		if($txt_supplier_id!="") $supplier_cond="and c.supplier_id in($txt_supplier_id)"; else $supplier_cond="";
		if($cbo_item_cat!="") $category_cond="and c.item_category in($cbo_item_cat)"; else $category_cond="";
		if($txt_wo_order_id!="") $wo_id_cond="and d.booking_id in($txt_wo_order_id)"; else $wo_id_cond="";
		if($cbo_buyer_name!=0) $buyer_cond="and a.buyer_name=$cbo_buyer_name"; else $buyer_cond="";
		$season_cond="";
		if($cbo_season_id>0) $season_cond=" and a.season_matrix=$cbo_season_id";
		
		$wo_booking_num_arr=array();
		$wo_sql=sql_select("select a.id, a.booking_no,a.supplier_id,a.booking_date,
		case when a.currency_id=1 then (b.amount) else 0 end as wo_amount_tk,
		case when a.currency_id=2 then (b.amount) else 0 end as wo_amount_usd,
		case when a.currency_id=1 then (a.booking_date) else null end as booking_date_tk,
		case when a.currency_id=2 then (a.booking_date) else null end as booking_date_usd,
		b.trim_group,b.wo_qnty as quantity 
		from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and  a.item_category=4 and a.booking_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach($wo_sql as $row)
		{
			$wo_booking_num_arr[$row[csf("id")]][$row[csf("supplier_id")]][$row[csf("trim_group")]]["date_tk"]=$row[csf("booking_date_tk")];
			$wo_booking_num_arr[$row[csf("id")]][$row[csf("supplier_id")]][$row[csf("trim_group")]]["date_usd"]=$row[csf("booking_date_usd")];
			
			$wo_booking_num_arr[$row[csf("id")]][$row[csf("supplier_id")]][$row[csf("trim_group")]]["booking_no"]=$row[csf("booking_no")];
			$wo_booking_num_arr[$row[csf("id")]][$row[csf("supplier_id")]][$row[csf("trim_group")]]["quantity"]+=$row[csf("quantity")];
			$wo_booking_num_arr[$row[csf("id")]][$row[csf("supplier_id")]][$row[csf("trim_group")]]["amount_tk"]+=$row[csf("wo_amount_tk")];
			$wo_booking_num_arr[$row[csf("id")]][$row[csf("supplier_id")]][$row[csf("trim_group")]]["amount_usd"]+=$row[csf("wo_amount_usd")];
		}
		
		if($db_type==0) $grop_po="group_concat( d.po_breakdown_id) AS po_id,group_concat( c.currency_id) AS currency_id ";
		else if($db_type==2)
			$grop_po="RTRIM ( XMLAGG (XMLELEMENT ( e, CAST (d.po_breakdown_id AS VARCHAR2 (4000)) || ',') ORDER BY d.po_breakdown_id).EXTRACT ('//text()').getClobVal (), ',') AS po_id, c.currency_id AS currency_id";
		if($db_type==0) $job_po="group_concat( a.job_no_prefix_num) AS job,group_concat( a.buyer_name) AS buyer_name";
		
		else if($db_type==2)$job_po="listagg(cast(a.job_no_prefix_num as varchar2(4000)),',') within group (order by a.job_no_prefix_num) AS job,listagg(cast(a.buyer_name as varchar2(4000)),',') within group (order by a.buyer_name) AS buyer_name";
		
		$poDataArray=sql_select("select b.id,b.po_number,$job_po from  wo_po_break_down b,wo_po_details_master a where  a.job_no=b.job_no_mst and a.company_name=$cbo_company_name and b.status_active=1 and b.is_deleted=0 $order_id_cond $buyer_cond $style_ref_id $season_cond group by  b.id,b.po_number ");// and a.season like '$txt_season'
		$job_array=array(); $all_job_id='';
		foreach($poDataArray as $row)
		{
			$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$job_array[$row[csf('id')]]['job']=$row[csf('job')];
			if($all_po_id=="") $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')]; //echo $all_po_id;
		} 
		
		
		if($season_cond!="" && count($poDataArray)<1)
		{
			echo '<p style="font-size:18px; font-weight:bold; color:red;">No Data Found.<p>';die;
		}
		
		if($txt_style_ref_id!='' || $txt_style_ref!='' || $cbo_buyer_name!=0 || $txt_order_id!='' ||  $txt_order!='')
		{
			if($all_po_id!='') $po_cond_id=" and d.po_breakdown_id in($all_po_id)";else $po_cond_id=""; 
		}
		else
		{
			$po_cond_id="";	
		}
		//echo $po_cond_id;
		$all_mrr_id='';
		$order_data_arr=array();
		// $sql_order="select $grop_po,c.recv_number  from inv_transaction a, product_details_master b, inv_receive_master c, order_wise_pro_details d  where a.mst_id=c.id and a.prod_id=b.id and d.trans_id=a.id and d.entry_form in(24) and a.item_category=4 and a.company_id=$cbo_company_name and a.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.receive_basis in(2) $po_cond_id group by c.recv_number";

		$sql_order = "select distinct $grop_po, c.recv_number from inv_transaction a, product_details_master b, inv_receive_master c, order_wise_pro_details d where a.mst_id = c.id and a.prod_id = b.id and d.trans_id = a.id and d.entry_form in (24) and a.item_category = 4 and a.company_id = $cbo_company_name and a.transaction_type in (1) and a.status_active = 1 and a.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and c.receive_basis in (2) $po_cond_id group by c.recv_number, c.currency_id";
 

		// echo $sql_order;die;
		 
		$sql_res=sql_select($sql_order);

		
		foreach($sql_res as $row)
		{
			$po_id_str = $row[csf("po_id")]->load();
			$po_ids=implode("," , array_unique(explode(",",$po_id_str))); 
			// foreach($po_ids as $val){
			// 	$po_str = $val.",";
			// }
			$order_data_arr[$row[csf('recv_number')]]['order']=$po_id_str;	
			$order_data_arr[$row[csf('recv_number')]]['curr']=$row[csf('currency_id')];	
			if($all_mrr_id=="") $all_mrr_id="'".$row[csf('recv_number')]."'"; else $all_mrr_id.=","."'".$row[csf('recv_number')]."'";
		}
		
		if($txt_style_ref_id!='' || $txt_style_ref!='' || $cbo_buyer_name!=0 || $txt_order_id!='' ||  $txt_order!='' || $season_cond!="")
		{
			if($all_mrr_id!='') 
			{
				$all_mrr_cond_id=" and c.recv_number in($all_mrr_id)";
			}
			else 
			{
				echo '<p style="font-size:18px; font-weight:bold; color:red;">No Data Found.<p>';die;
			}
		}
		else
		{
			$all_mrr_cond_id="";	
		}

		if($db_type==0) $group_concat="group_concat( distinct d.booking_id) AS booking_id,group_concat( distinct c.recv_number) AS recv_number,group_concat( distinct a.order_uom) AS order_uom,
		GROUP_CONCAT( CASE WHEN c.currency_id =1 THEN c.receive_date ELSE NULL END  ORDER BY c.receive_date ASC SEPARATOR ',') AS receive_date_tk, GROUP_CONCAT( CASE WHEN c.currency_id =2 THEN c.receive_date ELSE NULL END  ORDER BY c.receive_date ASC SEPARATOR ',') AS receive_date_usd";
		else if($db_type==2)  

		$group_concat="RTRIM( XMLAGG(XMLELEMENT( E, d.booking_id, ',' ).EXTRACT('//text()') ORDER BY d.booking_id ).GetClobVal(), ',' ) AS booking_id, RTRIM ( XMLAGG (XMLELEMENT (E, a.order_uom, ',').EXTRACT ('//text()') ORDER BY a.order_uom).GetClobVal (), ',') AS order_uom, RTRIM (
			XMLAGG (XMLELEMENT (E, c.recv_number, ',').EXTRACT ('//text()')
					ORDER BY c.recv_number).GetClobVal (),
			',')
			AS recv_number";

		$sql_access="select  c.item_category, c.supplier_id ,b.item_group_id, c.entry_form, $group_concat,
		sum(case when c.currency_id=1 then (a.order_qnty) else 0 end) as receive_qty_tk,
		sum(case when c.currency_id=2 then (a.order_qnty) else 0 end) as receive_qty_usd,
		
		sum(case when c.currency_id=1 then (a.order_amount) else 0 end) as receive_amt_tk,
		sum(case when c.currency_id=2 then (a.order_amount) else 0 end) as receive_amt_usd
		
 		from inv_transaction a, product_details_master b, inv_receive_master c , inv_trims_entry_dtls d
		where  a.mst_id = c.id and a.prod_id = b.id and c.id = d.mst_id and a.id=d.trans_id and a.item_category in($cbo_item_cat) and a.company_id=$cbo_company_name and a.transaction_type in(1) and a.order_qnty>0 and a.status_active=1 and a.is_deleted=0 and c.receive_basis in(2)  $date_cond  $supplier_cond  $item_id_cond $wo_id_cond $all_mrr_cond_id 
		group by c.item_category, c.supplier_id, b.item_group_id, c.entry_form order by c.supplier_id";

		$sql_result=sql_select($sql_access);

		ob_start();	
		?>
		<div id="" style="width:1400px;"> 
			<table width="1400px" id="" align="left"> 
				<tr class="form_caption" style="border:none;">
					<td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold" >Supplier Wise Goods Receive Statement Summary &nbsp; <? echo $currency[$cbo_currency]; ?></td> 
					</tr>
					<tr style="border:none;">
					<td colspan="15" align="center" style="border:none; font-size:14px;">
					Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>
					</td>
				</tr>
			</table>
			<br />
			<table width="1400" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left"> 
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="100">Item Group</th>
						<th width="100">Work Order No</th>
						<th width="120">Buyer</th>
						<th width="130">Jobs</th>
						<th width="130">Orders</th>
						<th width="150">MRR No</th>
						<th width="100">UOM</th>
						<th width="80">WO Qty.</th>
						<th width="80">Recv. Qty.</th>
						<th width="80">Bal. Qty.</th>
						<th width="60">WO Rate</th>
						<th width="80">WO Amt.</th>
						<th width="80">Recv. Amt.</th>
						<th width="">Bal. Amt.</th>
					</tr>
				</thead>
			</table>
             
			<div style="width:1420px; overflow-y: scroll; max-height:240px;" id="scroll_body">
			<table width="1400" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all"  align="left">
				<tbody>
					<?
					$i=1;$category_check_arr=array();$supplier_check_arr=array();$supplier_check_arr2=array();
					$j=1;$total_wo_qty=0;$total_recv_qty=0;$total_bal_qty=0;$total_wo_amount=0;$total_recv_amount=0;$total_bal_amount=0;
					foreach($sql_result as $val)
					{
						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						$receive_date_cond_usd=explode(",",$val[csf('receive_date_usd')]);//change_date_format
						$receive_date_cond_tk=explode(",",$val[csf('receive_date_tk')]);
						//print_r($receive_date_cond_tk);
						$mrr_currency_id=$val[csf('currency_id')];
						$booking_id_str = $val[csf("booking_id")]->load();
						$order_uom_str = $val[csf("order_uom")]->load();
						$booking_ids=array_unique(explode(",",$booking_id_str));
						//print_r($booking_ids);
						$wo_no=''; $wo_uom='';
						$order_uom_cond=explode(",",$order_uom_str);
						foreach($order_uom_cond as $uom)
						{
							 if($wo_uom=='') $wo_uom=$unit_of_measurement[$uom]; else $wo_uom.=",".$unit_of_measurement[$uom];	
						}
						$wo_qty=0;$wo_amount_tk=0;$wo_amount_usd=0;$wo_date_tk='';$wo_date_usd='';
						if($val[csf("entry_form")]==24)
						{
							foreach($booking_ids as $ids)
							{
								//echo $ids;
								if($wo_no=='') $wo_no=$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["booking_no"]; else $wo_no.=",".$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["booking_no"];
								
								if($wo_date_usd=='') $wo_date_usd=$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["date_usd"]; else $wo_date_usd.=",".$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["date_usd"];
								if($wo_date_tk=='') $wo_date_tk=$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["date_tk"]; else $wo_date_tk.=",".$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["date_tk"];
								$wo_booking_num_arr[$row[csf("id")]][$row[csf("supplier_id")]][$row[csf("trim_group")]]["date_usd"];
								
								$wo_qty+=$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["quantity"];
								$wo_amount_tk+=$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["amount_tk"];	
								$wo_amount_usd+=$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["amount_usd"];			
							}
							
							// $recve_mrr=explode(",",$val[csf("recv_number")]);

							$recve_mrr= explode(",",$val[csf("recv_number")]->load());
							// print_r($recve_mrr);die;

							foreach($recve_mrr as $recv)
							{
								$order_id = $order_data_arr[$recv]['order'];	
								$currency_id=$order_data_arr[$recv]['curr'];
							}
							$recve_mrr = implode(",", array_unique(explode(",",$val[csf("recv_number")]->load())) ) ;

							$order_data=array_unique(explode(",",$order_id));

							$po_id='';$all_job='';$all_buyer='';
							foreach( $order_data as $po)
							{
								if($all_job=='') $all_job=$job_array[$po]['job'];else  $all_job.=",".$job_array[$po]['job'];
								if($po_id=='') $po_id=$po_number_arr[$po];else $po_id.=','.$po_number_arr[$po];

								if($all_buyer=='') 
								$all_buyer=$buyer_name_arr[$job_array[$po]['buyer']];
								else $all_buyer.=",".$buyer_name_arr[$job_array[$po]['buyer']];	
								
							}
							
						}
						else
						{
							foreach($booking_ids as $ids)
							{
								if($wo_no=='') $wo_no=$wo_num_arr[$ids]["wo_number"]; else $wo_no.=",".$wo_num_arr[$ids]["wo_number"];
								
								if($wo_date_usd=='') $wo_date_usd=$wo_num_arr[$ids]["wo_date_usd"]; else $wo_date_usd.=",".$wo_num_arr[$ids]["wo_date_usd"];
								if($wo_date_tk=='') $wo_date_tk=$wo_num_arr[$ids]["wo_date_taka"]; else $wo_date_tk.=",".$wo_num_arr[$ids]["wo_date_taka"];
								
								$wo_qty+=$wo_num_arr[$ids]["quantity"];
								$wo_amount_usd+=$wo_num_arr[$ids]["amount"];			
							}
						}

						
						
						$currency_id_data=array_unique(explode(",",$mrr_currency_id));
							
						$wo_date_tk=explode(",",$wo_date_tk);
						$wo_date_usd=explode(",",$wo_date_usd);
						
						if($cbo_currency==1) //Taka
						{
							$exchange_rate=0;
							foreach( $receive_date_cond_tk as $dates)
							{
							$exchange_rate=set_conversion_rate( 2, $dates );
							}
							$rev_amount=($val[csf("receive_amt_tk")])+$val[csf("receive_amt_usd")]*$exchange_rate;
							
							$wo_exchange_rate=0;//wo AMT
							foreach( $wo_date_usd as $wo_dates)
							{
							 $wo_exchange_rate=set_conversion_rate( 2, $wo_dates );
							}
							
							if($wo_amount_usd>0 && $wo_exchange_rate>0)
							{
								$wo_amount=($wo_amount_tk)+($wo_amount_usd*$wo_exchange_rate);
							}
							else
							{
								$wo_amount=$wo_amount_tk;
							}
							
								
						}
						else if($cbo_currency==2) //USD
						{
							$exchange_rate=0;
							foreach( $receive_date_cond_tk as $dates)
							{
							 $exchange_rate=set_conversion_rate( 2, $dates );
							}
							$rev_amount=($val[csf("receive_amt_usd")])+$val[csf("receive_amt_tk")]/$exchange_rate;
						
							$wo_exchange_rate=0;//wo AMT
							foreach( $wo_date_tk as $wo_dates)
							{
							 $wo_exchange_rate=set_conversion_rate( 2, $wo_dates );
							}
							
							if($wo_amount_tk>0 && $wo_exchange_rate>0)
							{
								$wo_amount=($wo_amount_usd)+($wo_amount_tk/$wo_exchange_rate);
							}
							else
							{
								$wo_amount=$wo_amount_usd;
							}
						}
						
						if (!in_array($val[csf('item_category')],$category_check_arr) )
						{ 
							?>
							<tr bgcolor="#EFEFEF"><td colspan="15"><b>Item Category Name: <? echo $item_category[$val[csf('item_category')]]; if($cbo_season_id)  echo ",  Season Name:". $season_arr[$cbo_season_id]; ?></b></td></tr>
							<?
							$category_check_arr[]=$val[csf('item_category')]; 
						}
						 
						if (!in_array($val[csf('supplier_id')],$supplier_check_arr2) )
						{ 
							if($j!=1)
							{
								?>
								<tr bgcolor="#CCCCCC" >
									<td align="right" colspan="12"><b> Supplier Total: </b></td>
									<td align="right"><? echo number_format($tot_wo_amount,2);$tot_wo_amount=0; ?></td>
									<td align="right"><? echo number_format($tot_recv_amount,2);$tot_recv_amount=0; ?></td>
									<td align="right"><? echo number_format($tot_bal_amount,2);$tot_bal_amount=0; ?></td>
								</tr>
								<?
							} 
							$j++;
							$supplier_check_arr2[]=$val[csf('supplier_id')];
							?>
							<tr bgcolor="#EFEFEF"><td colspan="15"><b>Supplier Name: <? echo $supplier_arr[$val[csf('supplier_id')]]; ?></b></td></tr>
							<? 
						}
						?>
						
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="100"><p><? echo $lib_item_group[$val[csf("item_group_id")]]; ?></p></td>
							<td width="100" align="center"><p><? $wo_no_all=implode(",",array_unique(explode(",",$wo_no)));echo $wo_no_all; ?></p></td>
							<td width="120"  align="center"><p>
							<?  echo implode(",",array_unique(explode(",",$all_buyer))); ?>
							</p></td> 
							<td width="130"  align="center"><p><? echo implode(",",array_unique(explode(",",$all_job))); ?></p></td>               <td width="130"  align="center"><p><? 
							echo $po_id; ?></p></td>
							<td width="150"><p><?= $recve_mrr;?></p></td>
							<td width="100"><p><? $wo_uom_cond=implode(",",array_unique(explode(",",$wo_uom)));echo $wo_uom_cond; ?></p></td>
							<td width="80" align="right"><p><? $tot_wo_qty+=$wo_qty;echo number_format($wo_qty,2); ?></p></td>
							<td width="80" align="right" title="<? echo $val[csf("receive_amt")];?>"><p>
							<? $tot_receive_qty+=$val[csf("receive_qty_tk")]+$val[csf("receive_qty_usd")]; echo $val[csf("receive_qty_tk")]+$val[csf("receive_qty_usd")];
							//if($val[csf("currency_id")]==1) echo 'Taka';else echo 'USD';?>
							</p></td>
							<td width="80" align="right"><p>
							<?  $bal_qty=$wo_qty-($val[csf("receive_qty_tk")]+$val[csf("receive_qty_usd")]);echo number_format($bal_qty,2);
							$tot_bal_qty+=$bal_qty;
							?>
							</p></td>
							<td width="60"  align="center"><p><? 
							
							$wo_rate_conver=$rev_amount/($val[csf("receive_qty_tk")]+$val[csf("receive_qty_usd")]); 
							echo number_format($wo_rate_conver,3);	
							 ?></p></td>
							<td width="80" align="right"><p><? $tot_wo_amount+=$wo_amount;echo number_format($wo_amount,2); ?></p></td>
							<td width="80" align="right"><p><? 
							$recv_amount=$rev_amount;//($val[csf("receive_qty_tk")]+$val[csf("receive_qty_usd")])*$wo_rate_conver;
							echo number_format($recv_amount,2);
							$tot_recv_amount+=$recv_amount;
							; ?></p></td>
							<td width="" align="right"><p><? $bal_amount=$wo_amount-$recv_amount;echo number_format($bal_amount,2);  $tot_bal_amount+=$bal_amount;?></p></td>
						</tr>
						<?
						$total_wo_qty+=$wo_qty;
						$total_recv_qty+=$val[csf("receive_qty_tk")]+$val[csf("receive_qty_usd")];
						$total_bal_qty+=$bal_qty;
						$total_wo_amount+=$wo_amount;
						$total_recv_amount+=$recv_amount;
						$total_bal_amount+=$bal_amount;
						$i++;
					}
					?> 
					<tr bgcolor="#CCCCCC" >
						<td align="right" colspan="12"><b> Supplier Total: </b></td>
						<td align="right"><? echo number_format($tot_wo_amount,2);$tot_wo_amount=0; ?></td>
						<td align="right"><? echo number_format($tot_recv_amount,2);$tot_recv_amount=0; ?></td>
						<td align="right"><? echo number_format($tot_bal_amount,2);$tot_bal_amount=0; ?></td>
					</tr>  
				</tbody>
			</table>
			<table width="1400" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left"> 
				<tfoot>
					<tr>
						<th width="30"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="120"></th>
						<th width="130"></th>
						<th width="130"></th>
						<th width="150"></th>
						<th width="100"></th>
						<th width="80"><? //echo number_format($total_wo_qty,2); ?></th>
						<th width="80"><? //echo number_format($total_recv_qty,2); ?></th>
						<th width="80"><? //echo number_format($total_bal_qty,2); ?></th>
						<th width="60">Grand Total</th>
						<th width="80"><? echo number_format($total_wo_amount,2); ?></th>
						<th width="80"><? echo number_format($total_recv_amount,2); ?></th>
						<th width=""><? echo number_format($total_bal_amount,2); ?></th>
					</tr>
				</tfoot>
			</table> 
		 </div>
		 <br />
		 <?
				
	}// Summary End
	else // Details Start
	{
		
			if($txt_style_ref!="")
			{
				if($txt_style_ref_id!="")
				{
					$style_ref_id="and a.id in($txt_style_ref_id)";
				}
				else
				{
					$style_ref_id="and a.job_no_prefix_num like'%$txt_style_ref'"; 
		
				}
			}
			else
			{
				 $style_ref_id="";
			}
			//echo $txt_style_ref_id;
			if($txt_order!="")
			{
				if($txt_order_id!="")
				{
					$order_id_cond="and b.id in($txt_order_id)";
				}
				else
				{
					$order_id_cond="and b.po_number='$txt_order'";
				}
			}
			else
			{
				$order_id_cond="";
			}
			//print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));//,4,5
			if($txt_item_id!="") $item_id_cond="and b.item_group_id in($txt_item_id)"; else $item_id_cond="";
			if($cbo_buyer_name!=0) $buyer_cond="and a.buyer_name=$cbo_buyer_name"; else $buyer_cond="";
			if($txt_supplier_id!="") $supplier_cond="and c.supplier_id in($txt_supplier_id)"; else $supplier_cond="";
			if($cbo_item_cat!="") $category_cond="and c.item_category in($cbo_item_cat)"; else $category_cond="";
			if($txt_wo_order_id!="") $wo_id_cond="and d.booking_id in($txt_wo_order_id)"; else $wo_id_cond="";
			if($cbo_buyer_name!=0) $buyer_cond="and a.buyer_name=$cbo_buyer_name"; else $buyer_cond="";
			if($cbo_season_id>0) $season_cond=" and a.season_matrix=$cbo_season_id";
			
			$wo_booking_num_arr=array();
			$wo_sql=sql_select("select a.id, a.booking_no,a.supplier_id,b.amount,b.trim_group,b.wo_qnty as quantity from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and  a.item_category=4 and a.booking_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			
			// echo "<pre>";
			// print_r($wo_sql);die;
			
			foreach($wo_sql as $row)
			{
				$wo_booking_num_arr[$row[csf("id")]][$row[csf("supplier_id")]][$row[csf("trim_group")]]["booking_no"]=$row[csf("booking_no")];
				$wo_booking_num_arr[$row[csf("id")]][$row[csf("supplier_id")]][$row[csf("trim_group")]]["quantity"]+=$row[csf("quantity")];
				$wo_booking_num_arr[$row[csf("id")]][$row[csf("supplier_id")]][$row[csf("trim_group")]]["amount"]+=$row[csf("amount")];
			}
			if($db_type==0) $grop_po="group_concat( d.po_breakdown_id) AS po_id,group_concat( c.currency_id) AS currency_id ";
			else if($db_type==2)$grop_po="listagg(cast(d.po_breakdown_id as varchar2(4000)),',') within group (order by d.po_breakdown_id) AS po_id,listagg(cast(c.currency_id as varchar2(4000)),',') within group (order by c.currency_id) AS currency_id";
			if($db_type==0) $job_po="group_concat( a.job_no_prefix_num) AS job,group_concat( a.buyer_name) AS buyer_name";
			else if($db_type==2)$job_po="listagg(cast(a.job_no_prefix_num as varchar2(4000)),',') within group (order by a.job_no_prefix_num) AS job,listagg(cast(a.buyer_name as varchar2(4000)),',') within group (order by a.buyer_name) AS buyer_name";

			$poDataArray=sql_select("select b.id,b.po_number,$job_po from  wo_po_break_down b, wo_po_details_master a where a.job_no=b.job_no_mst and a.company_name=$cbo_company_name and b.status_active=1 and b.is_deleted=0 $order_id_cond $buyer_cond $style_ref_id $season_cond group by b.id,b.po_number ");// and a.season like '$txt_season'
			$job_array=array(); $all_job_id='';
			foreach($poDataArray as $row)
			{
				$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
				$job_array[$row[csf('id')]]['job']=$row[csf('job')];
				if($all_po_id=="") $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')]; //echo $all_po_id;
			} //echo $all_po_id;
			/* echo '<pre>';
			print_r($job_array); die; */
			
			if($season_cond!="" && count($poDataArray)<1)
			{
				echo '<p style="font-size:18px; font-weight:bold; color:red;">No Data Found.<p>';die;
			}
			
			if($txt_style_ref_id!='' || $txt_style_ref!='' || $cbo_buyer_name!=0 || $txt_order_id!='' ||  $txt_order!='')
			{
				if($all_po_id!='') $po_cond_id=" and d.po_breakdown_id in($all_po_id)";else $po_cond_id=""; 
			}
			else
			{
				$po_cond_id="";	
			}
			//echo $po_cond_id;
			$all_mrr_id='';
			$order_data_arr=array();
			$sql_order="select d.po_breakdown_id as po_id,c.currency_id as currency_id,c.recv_number,b.item_group_id 
			from inv_transaction a, product_details_master b,inv_receive_master c,order_wise_pro_details d 
			where a.mst_id=c.id and a.prod_id=b.id and d.trans_id=a.id and d.entry_form in(24) and a.item_category=4 and a.company_id=$cbo_company_name and a.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.receive_basis in(2) $po_cond_id 
			group by c.recv_number,b.item_group_id, d.po_breakdown_id,c.currency_id";
			//echo $sql_order;die;
			$sql_res=sql_select($sql_order);
			foreach($sql_res as $row)
			{
				// $order_data_arr[$row[csf('recv_number')]]['order']=$row[csf('po_id')];	
				// $order_data_arr[$row[csf('recv_number')]]['curr']=$row[csf('currency_id')];	
				$order_data_arr[$row[csf('recv_number')]][$row[csf('item_group_id')]]['order'][$row[csf('po_id')]]=$row[csf('po_id')];	
				$order_data_arr[$row[csf('recv_number')]][$row[csf('item_group_id')]]['curr'][$row[csf('currency_id')]]=$row[csf('currency_id')];	
				if($all_mrr_id=="") $all_mrr_id="'".$row[csf('recv_number')]."'"; else $all_mrr_id.=","."'".$row[csf('recv_number')]."'";
			}
			/* echo '<pre>';
			print_r($order_data_arr); die; */
			//echo $all_mrr_id;
			if($txt_style_ref_id!='' || $txt_style_ref!='' || $cbo_buyer_name!=0 || $txt_order_id!='' ||  $txt_order!='' ||  $season_cond!='')
			{
				if($all_mrr_id!='') 
				{
					$all_mrr_cond_id=" and c.recv_number in($all_mrr_id)";
				}
				else 
				{
					echo '<p style="font-size:18px; font-weight:bold; color:red;">No Data Found.<p>';die;
				}
			}
			else
			{
				$all_mrr_cond_id="";	
			}
	
			/*if($db_type==0) $group_concat="group_concat( distinct d.booking_id) AS booking_id";
			else if($db_type==2)  $group_concat="listagg(cast(d.booking_id as varchar2(4000)),',') within group (order by d.booking_id) AS booking_id";*/
			if($db_type==0)
			{
				$group_concat="group_concat( distinct d.booking_id) AS booking_id";
				$select_insert_date="DATE_FORMAT(c.insert_date,'%d-%m-%Y %H:%i:%S') as insert_date";
				$select_insert_time="DATE_FORMAT(c.insert_date,'%H:%i:%S') as insert_time";
			}
			else
			{
				$group_concat="listagg(cast(d.booking_id as varchar2(4000)),',') within group (order by d.booking_id) AS booking_id";
				$select_insert_date=" to_char(c.insert_date,'DD-MM-YYYY HH24:MI:SS') as insert_date";
				//HH24:MI:SS
				$select_insert_time=" to_char(c.insert_date,'HH24:MI:SS') as insert_time";
			} 
			$sql_access="SELECT a.item_category, c.supplier_id,b.item_group_id,c.recv_number,c.challan_no,a.order_uom,c.receive_date,c.currency_id,$group_concat,
			sum(case when a.transaction_type in(1) then a.order_qnty else 0 end) as receive_qty ,
			sum(case when a.transaction_type in(1) then (a.order_amount) else 0 end) as receive_amt,
			c.entry_form, c.inserted_by, $select_insert_date, $select_insert_time
			from inv_transaction a, product_details_master b, inv_receive_master c  , inv_trims_entry_dtls d
			where  a.mst_id = c.id and a.prod_id = b.id and c.id = d.mst_id and a.id=d.trans_id and a.item_category in($cbo_item_cat) and a.company_id=$cbo_company_name and a.transaction_type in(1) and a.order_qnty>0 and a.status_active=1 and a.is_deleted=0 and c.receive_basis in(2) $date_cond $supplier_cond $item_id_cond $wo_id_cond $all_mrr_cond_id 
			group by  a.item_category, c.supplier_id, b.item_group_id, c.currency_id, c.challan_no, a.order_uom, c.recv_number, c.receive_date, c.entry_form, c.inserted_by, c.insert_date
			order by c.supplier_id";
			// echo $sql_access;
			$sql_result=sql_select($sql_access);

			// echo "<pre>";
			// print_r($sql_result); die;
			ob_start();	
			?>
			<div id="" style="width:1360px;">
				<table width="1260px" id="" align="left"> 
					<tr class="form_caption" style="border:none;">
						<td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold" >Supplier Wise Goods Receive Statement Details &nbsp; <? echo $currency[$cbo_currency]; ?></td> 
					</tr>
					<tr style="border:none;">
						<td colspan="15" align="center" style="border:none; font-size:14px;">
						Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>
						</td>
					</tr>
				</table>
				<br />
				<table width="1360" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left"> 
					<thead>
						<tr>
							<th width="30">SL</th>
							<th width="80">Rev. Date</th>
							<th width="120">MRR No</th>
							<th width="120">Item Group</th>
							<th width="130">Work Order No</th>
							<th width="120">Buyer</th>
							<th width="100">Jobs</th>
							<th width="100">Orders</th>
							<th width="80">Challan No</th>
							<th width="60">UOM</th>
							<th width="80">Rev. Qty.</th>
							<th width="60">Rev. Rate</th>
							<th width="100">Rev. Amt.</th>
							<th width="100">Insert Date</th>
							<th width="">Insert User</th>
							
						</tr>
					</thead>
				</table> 
	   
				<div style="width:1380px; overflow-y: scroll; max-height:240px;" id="scroll_body">
				<table width="1360" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all"  align="left">
					<tbody>
						<?
						$i=1;$category_check_arr=array();$supplier_check_arr=array();$supplier_check_arr2=array();
						$j=1;$total_wo_qty=0;$total_recv_qty=0;$total_bal_qty=0;$total_wo_amount=0;$total_recv_amount=0;$total_bal_amount=0;
						
						foreach($sql_result as $val)
						{
							if ($i%2==0)
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";
							$receive_date=$val[csf('receive_date')];//change_date_format
							//print_r($receive_date);
							$mrr_currency_id=$val[csf('currency_id')];
							$booking_ids=array_unique(explode(",",$val[csf("booking_id")]));
							$wo_no=''; $wo_uom='';
							$order_uom_cond=explode(",",$val[csf("order_uom")]);
							foreach($order_uom_cond as $uom)
							{
								 if($wo_uom=='') $wo_uom=$unit_of_measurement[$uom]; else $wo_uom.=",".$unit_of_measurement[$uom];	
							}
							$wo_qty=0;
							if($val[csf('entry_form')]==24)
							{
								
								foreach($booking_ids as $ids)
								{
									//echo $ids;
									if($wo_no=='') $wo_no=$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["booking_no"]; else $wo_no.=",".$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["booking_no"];
									//$wo_qty+=$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["quantity"];
									//$wo_amount=$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["amount"];
								}
								
								$recve_mrr=explode(",",$val[csf("recv_number")]);
								
								 

								foreach($recve_mrr as $recv)
								{
									$order_id=$order_data_arr[$recv][$val[csf("item_group_id")]]['order'];
									 
									$currency_id=$order_data_arr[$recv][$val[csf("item_group_id")]]['curr'];//$order_data_arr[$row[csf('recv_number')]]['curr']4
									
								}
								
								$order_data=array_unique(explode(",",$order_id));
								
								// print_r($order_id);die;

								$po_id='';$all_job='';$all_buyer='';
								
								
								foreach( $order_id as $po)
								{
									//echo $po;
									if($all_job=='') $all_job=$job_array[$po]['job'];else  $all_job.=",".$job_array[$po]['job'];
									//$all_job=$job_array[$po]['buyer'];
									if($po_id=='') $po_id=$po_number_arr[$po];else $po_id.=','.$po_number_arr[$po];
									if($all_buyer=='') $all_buyer=$buyer_name_arr[$job_array[$po]['buyer']];else $all_buyer.=",".$buyer_name_arr[$job_array[$po]['buyer']];	
								}
								$order_data=array_unique(explode(",",$order_id));
							}
							else
							{
								foreach($booking_ids as $ids)
								{
									//echo $ids;
									if($wo_no=='') $wo_no=$wo_num_arr[$ids]["wo_number"]; else $wo_no.=",".$wo_num_arr[$ids]["wo_number"];
									//$wo_qty+=$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["quantity"];
									//$wo_amount=$wo_booking_num_arr[$ids][$val[csf('supplier_id')]][$val[csf("item_group_id")]]["amount"];
								}
							}
						
						
							

							$currency_id_data=array_unique(explode(",",$currency_id));
							
							foreach($currency_id_data as $curr_id)
							{
								if($curr==1) $curr_id=1;
								else if($curr==2) $curr_id=2;
							}
							//echo $curr_id;
							if($cbo_currency==1) //Taka
							{
								if($mrr_currency_id==2)
								{
									//$exchange_rate=0;
									//foreach( $receive_date_cond as $dates)
									//{
									$exchange_rate=set_conversion_rate( 2, $receive_date );
									//}
									
									$wo_rate=$val[csf("receive_amt")]/$val[csf("receive_qty")];
									$wo_rate_conver=$exchange_rate*$wo_rate;
								}
								else
								{
									$wo_rate_conver=$val[csf("receive_amt")]/$val[csf("receive_qty")];	
								}
							}
							else if($cbo_currency==2) //USD
							{
								//echo $val[csf("currency_id")];
								if($mrr_currency_id==1)
								{
									//$exchange_rate=0;
									//foreach( $receive_date_cond as $dates)
									//{
									$exchange_rate=set_conversion_rate( 2, $receive_date );
									//}
									$wo_rate=$val[csf("receive_amt")]/$val[csf("receive_qty")];
									$wo_rate_conver=$wo_rate/$exchange_rate;
								}
								else
								{
									$wo_rate_conver=$val[csf("receive_amt")]/$val[csf("receive_qty")];	
								}
							}
							//print_r($currency_id_data);
							
							
							if (!in_array($val[csf('item_category')],$category_check_arr) )
							{ 
								?>
								<tr bgcolor="#EFEFEF"><td colspan="15"><b>Item Category Name: <? echo $item_category[$val[csf('item_category')]]; if($cbo_season_id)  echo ",  Season Name:". $season_arr[$cbo_season_id]; ?></b></td></tr>
								 <?
								$category_check_arr[]=$val[csf('item_category')]; 
							} 
							if (!in_array($val[csf('supplier_id')],$supplier_check_arr2) )
							{ 
								if($j!=1)
								{
									?>
									<tr bgcolor="#CCCCCC" >
                                        <td colspan="12" align="right"><b>Supplier Total:</b></td>
                                        <td width="100" align="right"><b><? echo number_format($tot_recv_amount,2);$tot_recv_amount=0; ?></b></td>
                                        <td width="100"></td>
										<td width=""></td>
									</tr>
									<?
								} 
								$j++;
								$supplier_check_arr2[]=$val[csf('supplier_id')];
								?>
								<tr bgcolor="#EFEFEF">
									<td colspan="15"><b>Supplier Name: <? echo $supplier_arr[$val[csf('supplier_id')]]; ?></b></td>
								</tr>
								<? 
							}
							?>
							<? //echo "aa".$val[csf("challan_no")];;die; ?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><p><? echo $i; ?></p></td>
								<td width="80"><p><? echo change_date_format($receive_date,2);//$lib_item_group[$val[csf("item_group_id")]]; ?></p></td>
								<td width="120" align="center"><p><? echo $val[csf('recv_number')]; ?></p></td>
								<td width="120"  align="center"><p>
								<? echo $lib_item_group[$val[csf("item_group_id")]]; ?>
								</p></td>
								<td width="130"  align="center"><p><? $wo_no_all=implode(",",array_unique(explode(",",$wo_no)));echo $wo_no_all;//echo $all_job;//$buyer_name_arr[$val[csf("buyer_id")]]; ?></p></td>
								



								
								<td width="120"  align="center"><p><? 
								echo implode(",",array_unique(explode(",",$all_buyer)));//$po_id; ?></p></td>




								<td width="100"><p><? $job=implode(",",array_unique(explode(",",$all_job)));echo $job; ?></p></td>
								<td width="100"><p><? echo implode(",",array_unique(explode(",",$po_id)));//$wo_uom_cond=implode(",",array_unique(explode(",",$wo_uom)));echo $wo_uom_cond; ?></p></td>
								<td width="80" align="right"><p><? echo $val[csf("challan_no")]; //$tot_wo_qty+=$wo_qty;echo number_format($wo_qty,2); ?></p></td>
								<td width="60" align="right" title="<? echo $unit_of_measurement[$val[csf("order_uom")]];//$val[csf("receive_amt")];?>"><p>
								<?  echo $unit_of_measurement[$val[csf("order_uom")]];
								//$tot_receive_qty+=$val[csf("receive_qty")];?>
							   </p></td>
								<td width="80" align="right" title="<? echo  $val[csf("receive_amt")];?>"><p>
								<?  $tot_receive_qty+=$val[csf("receive_qty")];echo $val[csf("receive_qty")];//$bal_qty=$wo_qty-$val[csf("receive_qty")];echo number_format($bal_qty,2);
								//$tot_bal_qty+=$bal_qty;
								?>
								</p></td>
								<td width="60"  align="center" title="<? if($mrr_currency_id==1) echo 'Taka';else echo 'USD';?>"><p><?  
								echo number_format($wo_rate_conver,3);	
								//$wo_rate_conver//echo $wo_rate=number_format($val[csf("receive_amt")]/$val[csf("receive_qty")],2); ?></p></td>
								<td width="100" align="right"><p><? 
								$recv_amount=$val[csf("receive_qty")]*$wo_rate_conver;
								$tot_recv_amount+=$recv_amount;
								echo number_format($recv_amount,2); ?></p></td>
								<td width="100"><p><? echo change_date_format($val[csf("insert_date")])." ".$val[csf("insert_time")]; ?></p></td>
                                <td width=""><p><? echo $user_name_arr[$val[csf("inserted_by")]]; ?></p></td>
							</tr>
							<?
							$total_wo_qty+=$wo_qty;
							$total_recv_qty+=$val[csf("receive_qty")];
							$total_bal_qty+=$bal_qty;
							$total_wo_amount+=$wo_amount;
							$total_recv_amount+=$recv_amount;
							$total_bal_amount+=$bal_amount;
							$i++;
						}
						?>  
						<tr bgcolor="#CCCCCC" >
							<td colspan="12" align="right"><b>Supplier Total:</b></td>
							<td width="100" align="right"><b><? echo number_format($tot_recv_amount,2);$tot_recv_amount=0; ?></b></td>
							<td width="100"></td>
							<td width=""></td>
						</tr>
					</tbody>
				</table>
				<table width="1360" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left"> 
					<tfoot>
						<tr>
							<th width="30"></th>
							<th width="80"></th>
							<th width="120"></th>
							<th width="120"></th>
							<th width="130"></th>
							<th width="120"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="80"><? //echo number_format($total_wo_qty,2); ?></th>
							<th width="60"></th>
							<th width="80"><? //echo number_format($total_recv_qty,2); ?></th>
							<th width="60">Grand Total</th>
							<th width="100"><? echo number_format($total_recv_amount,2); ?></th>
							<th width="100"></th>
							<th width=""></th>
						</tr>
					</tfoot>
				</table> 
				 </div>
			</div>
			<?
		
				
	}// Summary End
			
	foreach (glob("*.xls") as $filename) {
	if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	//echo "$html**$filename**$cbo_item_cat**$rptType";
	echo "$total_data**$filename"; 
	exit();
	
}
?>

