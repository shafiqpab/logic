<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

include ("../../../../ext_resource/excel/excel/vendor/autoload.php");
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;





$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_store")
{
	$data=explode('_',$data);
	echo create_drop_down( "cbo_store_name", 120, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id  and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] and  b.category_type=$data[1] order by a.store_name","id,store_name", 1, "--Select Store--", 1, "",0 );
	exit();
	//select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] and b.category_type=$data[1] order by a.store_name
}

if ($action=="item_account_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	$cbo_company_name=str_replace("'","",$data[0]);
	$cbo_item_category_id=str_replace("'","",$data[1]);
	$txt_item_acc=str_replace("'","",$data[2]);
	$txt_product_id_des=str_replace("'","",$data[3]);
	$txt_product_id_no=str_replace("'","",$data[4]);
	//print_r ($data); 
	?>	
    <script>
	/* var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	 
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
	function js_set_value(id)
	{
		var str=id.split("_");
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		var strdt=str[2];
		str=str[1];
	
		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
			selected_name.push( strdt );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i,1 );
		}
		var id = '';
		var ddd='';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			ddd += selected_name[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		ddd = ddd.substr( 0, ddd.length - 1 );
		$('#item_account_id').val( id );
		$('#item_account_val').val( ddd );
	}*/ 
	
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
	<!--    <input type="hidden" id="item_account_id" />
		<input type="hidden" id="item_account_val" />
	-->   
	<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="600" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr>                	 
                    <th width="100">Product Id</th>
                    <th width="200">Item Description</th>
                    <th width="100">Gsm</th>
                    <th width="100">Dia</th>
                    <th><input type="reset" id="" value="Reset" style="width:80px;" class="formbutton" /></th>
                </tr>
            </thead>
            <tbody>
                <tr align="center">
                    <td align="center"><input type="text" style="width:70px" class="text_boxes"  name="txt_prod_id" id="txt_prod_id" /></td>
                    <td align="center"><input type="text" style="width:160px" class="text_boxes"  name="txt_item_description" id="txt_item_description" /></td>
                    <td align="center"><input type="text" style="width:70px" class="text_boxes"  name="txt_gsm" id="txt_gsm" /></td>
                    <td align="center"><input type="text" style="width:70px" class="text_boxes"  name="txt_dia" id="txt_dia" /></td> 
                    <td align="center">
                    	<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('txt_prod_id').value+'_'+document.getElementById('txt_item_description').value+'_'+document.getElementById('txt_gsm').value+'_'+document.getElementById('txt_dia').value+'_'+<? echo $cbo_company_name; ?>+'_'+<? echo $cbo_item_category_id; ?>+'_'+'<? echo $txt_item_acc; ?>'+'_'+'<? echo $txt_product_id_des; ?>'+'_'+'<? echo $txt_product_id_no; ?>', 'create_item_search_list_view', 'search_div', 'closing_stock_report_controller', '');" style="width:80px;" />				
                    </td>
                </tr>
            </tbody>
        </table>    
    <div align="center" valign="top" style="margin-top:5px" id="search_div"> </div> 
    </form>	

    </div> 
    
     
 <?
 
}

if ($action=="create_item_search_list_view")
{
	$ex_data=explode("_",$data);
	$txt_prod_id=str_replace("'","",$ex_data[0]);
	$txt_item_description=str_replace("'","",$ex_data[1]);
	$txt_gsm=str_replace("'","",$ex_data[2]);
	$txt_dia=str_replace("'","",$ex_data[3]);
	$cbo_company_name=str_replace("'","",$ex_data[4]);
	$cbo_item_category_id=str_replace("'","",$ex_data[5]);
	$txt_item_acc=str_replace("'","",$ex_data[6]);
	$txt_product_id_des=str_replace("'","",$ex_data[7]);
	$txt_product_id_no=str_replace("'","",$ex_data[8]);
	
	$sql_cond_all="";

	if($txt_prod_id!="") $sql_cond_all=" and id=$txt_prod_id";
	if($txt_item_description!="") $sql_cond_all.=" and product_name_details like '%$txt_item_description'";
	if($txt_gsm!="") $sql_cond_all.=" and gsm='$txt_gsm'";
	if($txt_dia!="") $sql_cond_all.=" and dia_width='$txt_dia'";
	if($cbo_company_name!=0) $sql_cond_all.=" and company_id=$cbo_company_name";
	if($cbo_item_category_id!=0) $sql_cond_all.=" and item_category_id=$cbo_item_category_id";
	
	$color_arr = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	
	$sql="SELECT id,product_name_details,color,gsm,dia_width,supplier_id from  product_details_master where status_active=1 and is_deleted=0 $sql_cond_all";
	//echo $sql;
	$arr=array(1=>$color_arr,4=>$supplierArr);
	echo  create_list_view("list_view", "Item Description,Color,Gsm,Dia,Supplier,Product ID", "150,120,70,70,130","680","300",0, $sql , "js_set_value", "id,product_name_details", "", 1, "0,color,0,0,supplier_id,0", $arr , "product_name_details,color,gsm,dia_width,supplier_id,id", "",'setFilterGrid("list_view",-1);','0,0,0,0,0,0','',1) ;
	
	//echo create_list_view("list_view", "Style Ref No,Job No,Year","160,90,100","400","310",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","",1) ;
	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	?>
    <script language="javascript" type="text/javascript">
	/*var account_des='<?// echo $txt_item_acc;?>';
	var account_id='<?// echo $txt_product_id_des;?>';
	var account_no='<?// echo $txt_product_id_no;?>';
	alert(account_id);
	if(account_no!="")
	{
		account_no_arr=account_no.split(",");
		account_id_arr=account_id.split(",");
		account_des_arr=account_des.split(",");
		var str_ref="";
		for(var k=0;k<account_no_arr.length; k++)
		{
			str_ref=account_no_arr[k]+'_'+account_id_arr[k]+'_'+account_des_arr[k];
			js_set_value(str_ref);
		}
	}*/
	</script>  
    <?
	
	exit();
}


if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$txt_product_id_des=str_replace("'","",$txt_product_id_des);
	$txt_product_id=str_replace("'","",$txt_product_id);
    $report_type=str_replace("'","",$report_type);
	$cbo_source_type=str_replace("'","",$cbo_source_type);

	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
	$determinaArr = return_library_array("select id,construction from  lib_yarn_count_determina_mst where status_active=1 and is_deleted=0","id","construction");
	$color_arr=return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	
	if($db_type==0)
	{
		$select_from_date=change_date_format($from_date,'yyyy-mm-dd');
		$select_from_to=change_date_format($to_date,'yyyy-mm-dd');
	}
	else if($db_type==2) 
	{
		$select_from_date=change_date_format($from_date,'','',1);
		$select_from_to=change_date_format($to_date,'','',1);
	}
	else 
	{
		$select_from_date="";
		$select_from_to="";
	}
	$days_doh=array();
	if($db_type==2)
	{
		$returnRes="select prod_id, min(transaction_date) || ',' || max(transaction_date )  as trans_date from inv_transaction where transaction_type in (1,3) and  item_category in (2,3) and status_active=1 and is_deleted=0 group by prod_id ";
		//$returnRes_result= sql_select($returnRes);
	}
	else
	{
		$returnRes="select prod_id, concat(min(transaction_date),',',max(transaction_date))  as trans_date from inv_transaction where transaction_type in (1,3) and  item_category in (2,3) and status_active=1 and is_deleted=0 group by prod_id ";
	}
	//echo $returnRes;die;
	$returnRes_result= sql_select($returnRes);
	foreach($returnRes_result as $row_d)
	{
		$date_total=explode(",",$row_d[csf('trans_date')]);
		if($db_type==2)
		{
			$today= change_date_format(date("Y-m-d"),'','',1);	
			$daysOnHand = datediff("d",change_date_format($date_total[1],'','',1),$today);
		}
		else
		{
			$today= change_date_format(date("Y-m-d"));	
			$daysOnHand = datediff("d",change_date_format($date_total[1]),$today);
		}
		$days_doh[$row_d[csf('prod_id')]]['daysonhand']=$daysOnHand ;
	} 
	
	$sql_cond="";
	
	if ($cbo_company_name!=0) $sql_cond =" and a.company_id=$cbo_company_name";
	if ($cbo_item_category_id!=0) $sql_cond.=" and a.item_category=$cbo_item_category_id";
	if ($txt_product_id_des!="") $sql_cond.=" and a.prod_id in ($txt_product_id_des)";
	if ($txt_product_id!="") $sql_cond.=" and a.prod_id in ($txt_product_id)";
	if ($txt_product_id!="") $sql_cond2=" and a.prod_id in ($txt_product_id)";
	if ($cbo_uom!="") $sql_cond.=" and a.cons_uom in ($cbo_uom)";

	
	if($report_type ==3 || $report_type ==6)
	{
		if($cbo_store_name) $sql_cond .= " and a.store_id=$cbo_store_name" ;
		$data_trns_array=array();
		/*$trnasactionData=sql_select("Select a.prod_id,
			sum(case when a.transaction_type=1 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
			sum(case when a.transaction_type=2 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
			sum(case when a.transaction_type=3 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as rcv_return_opening,
			sum(case when a.transaction_type=4 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as iss_return_opening,
			sum(case when a.transaction_type=5 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as trans_in_opening,
			sum(case when a.transaction_type=6 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as trans_out_opening,
			sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$select_from_date."' then a.cons_amount else 0 end) as rcv_total_opening_amt,
			sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$select_from_date."' then a.cons_amount else 0 end) as iss_total_opening_amt,    
			sum(case when a.transaction_type=1 and a.transaction_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as receive,
			sum(case when a.transaction_type=2 and a.transaction_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as issue,
			sum(case when a.transaction_type=3 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as rec_return,
			sum(case when a.transaction_type=4 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as issue_return,
			sum(case when a.transaction_type=5 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as transfer_in,
			sum(case when a.transaction_type=6 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as transfer_out,
			sum(case when a.transaction_type in(1,4,5) and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_amount else 0 end) as rcv_total_value,
			sum(case when a.transaction_type in(2,3,6) and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_amount else 0 end) as iss_total_value,
			sum(case when a.transaction_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_rate else 0 end) as rate
			from inv_transaction a, product_details_master b
			where a.prod_id = b.id and a.status_active=1 and a.is_deleted=0 $sql_cond group by a.prod_id order by a.prod_id ASC");*/

			//Note consider INSER_DATE replace of TRANSACTION_DATE because some transaction date mismatch for previous data.

			$trnasactionData=sql_select("Select a.prod_id,
			sum(case when a.transaction_type=1 and a.insert_date<'".$select_from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
			sum(case when a.transaction_type=2 and a.insert_date<'".$select_from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
			sum(case when a.transaction_type=3 and a.insert_date<'".$select_from_date."' then a.cons_quantity else 0 end) as rcv_return_opening,
			sum(case when a.transaction_type=4 and a.insert_date<'".$select_from_date."' then a.cons_quantity else 0 end) as iss_return_opening,
			sum(case when a.transaction_type=5 and a.insert_date<'".$select_from_date."' then a.cons_quantity else 0 end) as trans_in_opening,
			sum(case when a.transaction_type=6 and a.insert_date<'".$select_from_date."' then a.cons_quantity else 0 end) as trans_out_opening,
			sum(case when a.transaction_type in(1,4,5) and a.insert_date<'".$select_from_date."' then a.cons_amount else 0 end) as rcv_total_opening_amt,
			sum(case when a.transaction_type in(2,3,6) and a.insert_date<'".$select_from_date."' then a.cons_amount else 0 end) as iss_total_opening_amt,    
			sum(case when a.transaction_type=1 and a.insert_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as receive,
			sum(case when a.transaction_type=2 and a.insert_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as issue,
			sum(case when a.transaction_type=3 and a.insert_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as rec_return,
			sum(case when a.transaction_type=4 and a.insert_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as issue_return,
			sum(case when a.transaction_type=5 and a.insert_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as transfer_in,
			sum(case when a.transaction_type=6 and a.insert_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as transfer_out,
			sum(case when a.transaction_type in(1,4,5) and a.insert_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_amount else 0 end) as rcv_total_value,
			sum(case when a.transaction_type in(2,3,6) and a.insert_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_amount else 0 end) as iss_total_value,
			sum(case when a.insert_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_rate else 0 end) as rate
			from inv_transaction a, product_details_master b
			where a.prod_id = b.id and a.status_active=1 and a.is_deleted=0 $sql_cond group by a.prod_id order by a.prod_id ASC");

		$last_transaction_date=sql_select("Select a.prod_id, a.transaction_date from inv_transaction a, product_details_master b where a.prod_id = b.id and a.status_active=1 and a.is_deleted=0 $sql_cond group by a.prod_id, a.transaction_date order by a.prod_id, a.transaction_date ASC");

		$last_transaction_date_arr =array();

		foreach($last_transaction_date as $row)
		{
			$last_transaction_date_arr[$row[csf("prod_id")]]['last_transaction_date']=$row[csf("transaction_date")];
		}
		unset($last_transaction_date);
		
		foreach($trnasactionData as $row)
		{
			$data_trns_array[$row[csf("prod_id")]]['rcv_total_opening']=$row[csf("rcv_total_opening")];
			$data_trns_array[$row[csf("prod_id")]]['iss_total_opening']=$row[csf("iss_total_opening")];
			$data_trns_array[$row[csf("prod_id")]]['rcv_return_opening']=$row[csf("rcv_return_opening")];
			$data_trns_array[$row[csf("prod_id")]]['iss_return_opening']=$row[csf("iss_return_opening")];
			$data_trns_array[$row[csf("prod_id")]]['trans_in_opening']=$row[csf("trans_in_opening")];
			$data_trns_array[$row[csf("prod_id")]]['trans_out_opening']=$row[csf("trans_out_opening")];
			$data_trns_array[$row[csf("prod_id")]]['receive']=$row[csf("receive")];
			$data_trns_array[$row[csf("prod_id")]]['issue_return']=$row[csf("issue_return")];
			$data_trns_array[$row[csf("prod_id")]]['transfer_in']=$row[csf("transfer_in")];
			$data_trns_array[$row[csf("prod_id")]]['issue']=$row[csf("issue")];
			$data_trns_array[$row[csf("prod_id")]]['rec_return']=$row[csf("rec_return")];
			$data_trns_array[$row[csf("prod_id")]]['transfer_out']=$row[csf("transfer_out")];
			$data_trns_array[$row[csf("prod_id")]]['avg_rate']=$row[csf("rate")];
			$data_trns_array[$row[csf("prod_id")]]['rcv_total_opening_amt']=$row[csf("rcv_total_opening_amt")];
			$data_trns_array[$row[csf("prod_id")]]['iss_total_opening_amt']=$row[csf("iss_total_opening_amt")];
			$data_trns_array[$row[csf("prod_id")]]['rcv_total_value']=$row[csf("rcv_total_value")];
			$data_trns_array[$row[csf("prod_id")]]['iss_total_value']=$row[csf("iss_total_value")];
			$all_prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
					
		}

		$all_prod_id_arr = array_filter($all_prod_id_arr);
		if(!empty($all_prod_id_arr))
		{
			$all_prod_ids = implode(",", $all_prod_id_arr);
			$all_prod_id_cond=""; $prodCond=""; 
			if($db_type==2 && count($all_prod_id_arr)>999)
			{
				$all_prod_id_arr_chunk=array_chunk($all_prod_id_arr,999) ;
				foreach($all_prod_id_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);	
					$prodCond.="  id in($chunk_arr_value) or ";	
				}
				$all_prod_id_cond.=" and (".chop($prodCond,'or ').")";	
			}
			else
			{
				$all_prod_id_cond=" and id in($all_prod_ids)";	 
			}
		}

		$i=1;
		ob_start();	
		if($report_type ==3)
		{ 
			?>
			<style type="text/css">
				.wrap_break_word {
					word-break: break-all;
					word-wrap: break-word;
				}
			</style>
			<div> 
				<table style="width:1500px" border="1" cellpadding="2" cellspacing="0" class="" id="table_header_1" > 
					<thead>
						<tr class="form_caption" style="border:none;">
							<td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold" > <? echo $report_title; ?></td> 
						</tr>
						<tr style="border:none;">
							<td colspan="16" align="center" style="border:none; font-size:14px;">
							   <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>                               
							</td>
						</tr>
						<tr style="border:none;">
							<td colspan="16" align="center" style="border:none;font-size:12px; font-weight:bold">
								<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
							</td>
						</tr>
					</thead>
				</table>
				<table style="width:1760px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" > 
					<thead>
						<tr>
							<th rowspan="2" width="40">SL</th>
							<th colspan="6">Description</th>
							<th rowspan="2" width="110">Opening Stock</th>
							<th colspan="4">Receive</th>
							<th colspan="4">Issue</th>
							<th rowspan="2" width="100">Closing Stock</th>
							<th rowspan="2" width="100">Ageing</th>
							<!--<th rowspan="2" width="100" style="display:none">Avg. Rate</th>
							<th rowspan="2" width="100" style="display:none">Stock Value</th>-->
							<th rowspan="2">DOH</th>
						</tr> 
						<tr> 
							<th width="60">Prod. ID</th>                    
							<th width="120">Construction</th>
							<th width="180">Composition</th>
							<th width="70">GSM</th>
							<th width="80">Dia/ Width</th>
							<th width="100">Color</th>
							<th width="80">Purchase</th>
							<th width="80">Issue Return</th> 
							<th width="80">Trans In</th> 
							<th width="100">Total Received</th>
							<th width="80">Issue</th>
							<th width="80">Receive Return</th>
							<th width="80">Trans Out</th>
							<th width="100">Total Issue</th> 
						</tr> 
					</thead>
				</table>
				<div style="width:1778px; max-height:280px; overflow-y:scroll" id="scroll_body" > 
				<table style="width:1760px; margin-left: -20px;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
				<?
					$composition_arr=array();
					$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
					$data_array=sql_select($sql_deter);
					if(count($data_array)>0)
					{
						foreach( $data_array as $row )
						{
							if(array_key_exists($row[csf('id')],$composition_arr))
							{
								$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
							}
							else
							{
								$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
							}
						}
					}
					
					$sql_all_cond="";
					if ($cbo_company_name!=0) $sql_all_cond =" and company_id='$cbo_company_name'";
					if ($cbo_item_category_id!=0) $sql_all_cond.=" and item_category_id='$cbo_item_category_id'";
					if ($txt_product_id_des!="") $sql_all_cond.=" and id in ($txt_product_id_des)";
					if ($txt_product_id!="") $sql_all_cond.=" and id in ($txt_product_id)";
					if ($cbo_store_name!=0) $sql_all_cond.=" and store_id='$cbo_store_name'";
				   
				   if(!empty($all_prod_id_arr))
				   {
				   		$sql="select id, detarmination_id, gsm, dia_width, color, current_stock from product_details_master where status_active=1 and is_deleted=0  $all_prod_id_cond order by id"; //$sql_all_cond
						//echo $sql."<br>";	die;
						$result = sql_select($sql);
				   }
				   

					$tot_opening_bal=0;$tot_purchuse=0;$tot_issue_return=0;$tot_receive=0; $tot_issue=0;$tot_receive_return=0;$tot_total_issue=0;$total_closing_stock=0;$tot_closing_stock=0;
					foreach($result as $row)
					{
						if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
							
						$openingBalance =($data_trns_array[$row[csf("id")]]['rcv_total_opening']+$data_trns_array[$row[csf("id")]]['iss_return_opening'] + $data_trns_array[$row[csf("id")]]['trans_in_opening'])-($data_trns_array[$row[csf("id")]]['iss_total_opening']+$data_trns_array[$row[csf("id")]]['rcv_return_opening'] + $data_trns_array[$row[csf("id")]]['trans_out_opening']);
						
						$purchase = $data_trns_array[$row[csf("id")]]['receive'];
						$issue_return=$data_trns_array[$row[csf("id")]]['issue_return'];
						$transfer_in=$data_trns_array[$row[csf("id")]]['transfer_in'];
						$totalReceive=$purchase+$issue_return + $transfer_in;//
						$issue=$data_trns_array[$row[csf("id")]]['issue'];
						$rec_return=$data_trns_array[$row[csf("id")]]['rec_return'];
						$transfer_out=$data_trns_array[$row[csf("id")]]['transfer_out'];
						$totalIssue=$issue+$rec_return+$transfer_out;//

						$date1=date("d-m-Y");
						$date2 = $last_transaction_date_arr[$row[csf("id")]]['last_transaction_date'];
						$diff = abs(strtotime($date1) - strtotime($date2));
						$years = floor($diff / (365*60*60*24));
						$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
						//$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
						//printf("%d Years, %d Months, %d Days\n", $years, $months, $days);
						if($date2==''){
							$ageing = '';
						}
						else{
							$ageing = floor(($diff)/ (60*60*24))+1;
							$ageing = $ageing;
						}
						
						//$age = floor(($diff)/ (60*60*24));

						
						$closingStock=$openingBalance+$totalReceive-$totalIssue;
						//$avgRate=$data_trns_array[$row[csf("id")]]['avg_rate'];
						//$stockValue=$closingStock*$avgRate;
						 if($closingStock >= $cbo_value_with){
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="40"><? echo $i; ?></td>
								<td width="60"><? echo $row[csf("id")]; ?></td>								
								<td width="120" class="wrap_break_word"><? echo $determinaArr[$row[csf("detarmination_id")]]; ?></td>                                 
								<td width="180"><p class="wrap_break_word"><? echo $composition_arr[$row[csf('detarmination_id')]]; ?></p></td>
								<td width="70"><p><? echo $row[csf("gsm")]; ?></p></td> 
								<td width="80"><p><? echo $row[csf("dia_width")]; ?></p></td> 
								<td width="100"><p class="wrap_break_word"><? echo $color_arr[$row[csf("color")]]; ?></p></td> 
								
								<td width="110" align="right"><p><? echo number_format($openingBalance,2,'.','');$tot_opening_bal+=$openingBalance; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($purchase,2,'.',''); $tot_purchuse+=$purchase; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($issue_return,2,'.',''); $tot_issue_return+=$issue_return; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($transfer_in,2,'.',''); $tot_trans_in+=$transfer_in; ?></p></td>
								<td width="100" align="right"><p><? echo number_format($totalReceive,2,'.',''); $tot_receive+=$totalReceive; ?></p></td>
								
								<td width="80" align="right"><p><? echo number_format($issue,2,'.',''); $tot_issue+=$issue; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($rec_return,2,'.',''); $tot_receive_return+=$rec_return; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($transfer_out,2,'.',''); $tot_trans_out+=$transfer_out; ?></p></td>
								<td width="100" align="right"><p><? echo number_format($totalIssue,2,'.',''); $tot_total_issue+=$totalIssue; ?></p></td>
								<td width="100" align="right">
									<a href="##" onclick="stock_qnty_popup('<? echo $row[csf("id")];?>','<? echo $cbo_item_category_id;?>')">
										<p><? echo number_format($closingStock,2,'.',''); $total_closing_stock+=$closingStock; ?></p>
									</a>
								</td>
								<td width="100" align="center"><?php echo $ageing;?></td>
								<td align="right"><? echo $days_doh[$row[csf('id')]]['daysonhand'];?></td>
							</tr>
						<? 												
						$i++;
						 }
					}
				?>
				</table>
			   </div>
				<table style="width:1760px" border="1" cellpadding="2" cellspacing="0" class="tbl_bottom" rules="all" id="">
					<tr>
						<td align="right" width="40">&nbsp;</td>
						<td align="right" width="60">&nbsp;</td>
						<td align="right" width="120">&nbsp;</td>
						<td align="right" width="180">&nbsp;</td>
						<td align="right" width="70">&nbsp;</td>
						<td align="right" width="80">&nbsp;</td>
						<td align="right" width="100">Total</td>
						<td align="right" width="110" id="tot_opening_bal" ><? echo number_format($tot_opening_bal,2); ?></td>
						<td align="right" width="80" id="tot_purchuse" ><? echo number_format($tot_purchuse,2); ?></td>
						<td align="right" width="80" id="tot_issue_return" ><? echo number_format($tot_issue_return,2); ?></td>
						<td align="right" width="80" id="tot_trans_in" ><? echo number_format($tot_trans_in,2); ?></td>
						<td align="right" width="100" id="tot_receive" ><? echo number_format($tot_receive,2); ?></td>
						<td align="right" width="80" id="tot_issue" ><? echo number_format($tot_issue,2); ?></td>
						<td align="right" width="80" id="tot_receive_return" ><? echo number_format($tot_receive_return,2); ?></td>
						<td align="right" width="80" id="tot_trans_out" ><? echo number_format($tot_trans_out ,2); ?></td>
						<td align="right" width="100" id="tot_total_issue" ><? echo number_format($tot_total_issue,2); ?></td>
						<td align="right" width="100" id="total_closing_stock" ><? echo number_format($total_closing_stock,2); ?></td>
						<td align="right">&nbsp;</td> 
						<td align="right">&nbsp;</td>  
					</tr>
				</table>
			</div>
			<?
		} 
		
		if($report_type ==6)
		{
			?>
			<div> 
				<table style="width:1660px" border="1" cellpadding="2" cellspacing="0" class="" id="table_header_1" > 
					<thead>
						<tr class="form_caption" style="border:none;">
							<td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold" > <? echo $report_title; ?></td> 
						</tr>
						<tr style="border:none;">
							<td colspan="16" align="center" style="border:none; font-size:14px;">
							   <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>                               
							</td>
						</tr>
						<tr style="border:none;">
							<td colspan="16" align="center" style="border:none;font-size:12px; font-weight:bold">
								<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
							</td>
						</tr>
					</thead>
				</table>
				<table style="width:1660px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" > 
					<thead>
						<tr>
							<th rowspan="2" width="40">SL</th>
							<th colspan="6">Description</th>
							<th rowspan="2" width="110">Opening Stock</th>
							<th colspan="4">Receive</th>
							<th colspan="4">Issue</th>
							<th rowspan="2" width="100">Closing Stock</th>
							<!--<th rowspan="2" width="100" style="display:none">Avg. Rate</th>
							<th rowspan="2" width="100" style="display:none">Stock Value</th>-->
							<th rowspan="2">DOH</th>
						</tr> 
						<tr> 
							<th width="60">Prod. ID</th>                    
							<th width="120">Construction</th>
							<th width="180">Composition</th>
							<th width="70">GSM</th>
							<th width="80">Dia/ Width</th>
							<th width="100">Color</th>
							<th width="80">Purchase</th>
							<th width="80">Issue Return</th> 
							<th width="80">Trans In</th> 
							<th width="100">Total Received</th>
							<th width="80">Issue</th>
							<th width="80">Receive Return</th>
							<th width="80">Trans Out</th>
							<th width="100">Total Issue</th> 
						</tr> 
					</thead>
				</table>
				<div style="width:1688px; max-height:280px; overflow-y:scroll" id="scroll_body" > 
				<table style="width:1660px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left">
				<?
					$composition_arr=array();
					$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
					$data_array=sql_select($sql_deter);
					if(count($data_array)>0)
					{
						foreach( $data_array as $row )
						{
							if(array_key_exists($row[csf('id')],$composition_arr))
							{
								$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
							}
							else
							{
								$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
							}
						}
					}
					
					$sql_all_cond="";
					if ($cbo_company_name!=0) $sql_all_cond =" and company_id='$cbo_company_name'";
					if ($cbo_item_category_id!=0) $sql_all_cond.=" and item_category_id='$cbo_item_category_id'";
					if ($txt_product_id_des!="") $sql_all_cond.=" and id in ($txt_product_id_des)";
					if ($txt_product_id!="") $sql_all_cond.=" and id in ($txt_product_id)";
					if ($cbo_store_name!=0) $sql_all_cond.=" and store_id='$cbo_store_name'";
				   
				   /*$sql="select id, detarmination_id, gsm, dia_width, color, current_stock from product_details_master where status_active=1 and is_deleted=0 $sql_all_cond order by id";
					$result = sql_select($sql);*/

					 if(!empty($all_prod_id_arr))
				   {
				   		$sql="select id, detarmination_id, gsm, dia_width, color, current_stock from product_details_master where status_active=1 and is_deleted=0  $all_prod_id_cond order by id"; //$sql_all_cond
						//echo $sql."<br>";	die;
						$result = sql_select($sql);
				   }

					$tot_opening_bal=0;$tot_purchuse=0;$tot_issue_return=0;$tot_receive=0; $tot_issue=0;$tot_receive_return=0;$tot_total_issue=0;$total_closing_stock=0;$tot_closing_stock=0;
					foreach($result as $row)
					{
						if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
							
						$openingBalance =($data_trns_array[$row[csf("id")]]['rcv_total_opening']+$data_trns_array[$row[csf("id")]]['iss_return_opening'] + $data_trns_array[$row[csf("id")]]['trans_in_opening'])-($data_trns_array[$row[csf("id")]]['iss_total_opening']+$data_trns_array[$row[csf("id")]]['rcv_return_opening'] + $data_trns_array[$row[csf("id")]]['trans_out_opening']);
						
						$purchase = $data_trns_array[$row[csf("id")]]['receive'];
						$issue_return=$data_trns_array[$row[csf("id")]]['issue_return'];
						$transfer_in=$data_trns_array[$row[csf("id")]]['transfer_in'];
						$totalReceive=$purchase+$issue_return+$transfer_in;//
						$issue=$data_trns_array[$row[csf("id")]]['issue'];
						$rec_return=$data_trns_array[$row[csf("id")]]['rec_return'];
						$transfer_out=$data_trns_array[$row[csf("id")]]['transfer_out'];
						$totalIssue=$issue+$rec_return+$transfer_out;//
						
						$closingStock=$openingBalance+$totalReceive-$totalIssue;
						//$avgRate=$data_trns_array[$row[csf("id")]]['avg_rate'];
						//$stockValue=$closingStock*$avgRate;
						if($closingStock >= $cbo_value_with)
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="40"><? echo $i; ?></td>
								<td width="60"><? echo $row[csf("id")]; ?></td>								
								<td width="120"><p class="wrap_break_word"><? echo $determinaArr[$row[csf("detarmination_id")]]; ?></p></td>                                 
								<td width="180"><p class="wrap_break_word"><? echo $composition_arr[$row[csf('detarmination_id')]]; ?></p></td>
								<td width="70"><p><? echo $row[csf("gsm")]; ?></p></td> 
								<td width="80"><p><? echo $row[csf("dia_width")]; ?></p></td> 
								<td width="100"><p class="wrap_break_word"><? echo $color_arr[$row[csf("color")]]; ?></p></td> 
								
								<td width="110" align="right"><p><? echo number_format($openingBalance,2,'.','');$tot_opening_bal+=$openingBalance; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($purchase,2,'.',''); $tot_purchuse+=$purchase; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($issue_return,2,'.',''); $tot_issue_return+=$issue_return; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($transfer_in,2,'.',''); $tot_trans_in+=$transfer_in; ?></p></td>
								<td width="100" align="right"><p><? echo number_format($totalReceive,2,'.',''); $tot_receive+=$totalReceive; ?></p></td>
								
								<td width="80" align="right"><p><? echo number_format($issue,2,'.',''); $tot_issue+=$issue; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($rec_return,2,'.',''); $tot_receive_return+=$rec_return; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($transfer_out,2,'.',''); $tot_trans_out+=$transfer_out; ?></p></td>
								<td width="100" align="right"><p><? echo number_format($totalIssue,2,'.',''); $tot_total_issue+=$totalIssue; ?></p></td>
								<td width="100" align="right"><p><? echo number_format($closingStock,2,'.',''); $total_closing_stock+=$closingStock; ?></p></td>
								<td align="right"><? echo $days_doh[$row[csf('id')]]['daysonhand'];?></td>
							</tr>
							<? 												
							$i++;
						}
					}
				?>
				</table>
			   </div>
				<table style="width:1660px" border="1" cellpadding="2" cellspacing="0" class="tbl_bottom" rules="all" id="">
					<tr>
						<td align="right" width="40">&nbsp;</td>
						<td align="right" width="60">&nbsp;</td>
						<td align="right" width="120">&nbsp;</td>
						<td align="right" width="180">&nbsp;</td>
						<td align="right" width="70">&nbsp;</td>
						<td align="right" width="80">&nbsp;</td>
						<td align="right" width="100">Total</td>
						<td align="right" width="110" id="tot_opening_bal" ><? echo number_format($tot_opening_bal,2); ?></td>
						<td align="right" width="80" id="tot_purchuse" ><? echo number_format($tot_purchuse,2); ?></td>
						<td align="right" width="80" id="tot_issue_return" ><? echo number_format($tot_issue_return,2); ?></td>
						<td align="right" width="80" id="tot_trans_in" ><? echo number_format($tot_trans_in,2); ?></td>
						<td align="right" width="100" id="tot_receive" ><? echo number_format($tot_receive,2); ?></td>
						<td align="right" width="80" id="tot_issue" ><? echo number_format($tot_issue,2); ?></td>
						<td align="right" width="80" id="tot_receive_return" ><? echo number_format($tot_receive_return,2); ?></td>
						<td align="right" width="80" id="tot_trans_out" ><? echo number_format($tot_trans_out,2); ?></td>
						<td align="right" width="100" id="tot_total_issue" ><? echo number_format($tot_total_issue,2); ?></td>
						<td align="right" width="100" id="total_closing_stock" ><? echo number_format($total_closing_stock,2); ?></td>
						<td align="right">&nbsp;</td>  
					</tr>
				</table>
			</div>
			<?
		}
	}
	else if( $report_type ==4)
	{
		/*$basis_cond="";
		if($cbo_source_type==1) $basis_cond=" and a.receive_basis not in(1,2) and b.receive_basis not in(1,2)";
		else if($cbo_source_type==2) $basis_cond=" and a.receive_basis in(1,2) and b.receive_basis in(1,2)";
		else $basis_cond="";

		$sql_purchase=sql_select("select a.prod_id from inv_receive_master b, inv_transaction a where b.id=a.mst_id and b.entry_form in(7,17,37,66,68) $basis_cond and a.transaction_type=1 and a.status_active=1 and a.company_id=$cbo_company_name");*/
		$sql_cond_1 = $sql_cond_2 = "";
		if ($cbo_store_name!=0) $sql_cond_1.=" and a.store_id='$cbo_store_name'";
		if ($cbo_store_name!=0) $sql_cond_2.=" and store_id='$cbo_store_name'";
		if($cbo_source_type==2)
		{
			$sql_purchase=sql_select("select a.prod_id from inv_receive_master b, inv_transaction a where b.id=a.mst_id and b.entry_form in(7,17,37,66,68) and a.receive_basis in(1,2) and b.receive_basis in(1,2) and a.transaction_type=1 and a.status_active=1 and a.company_id=$cbo_company_name $sql_cond_1");
		}
		else
		{
			$sql_purchase=sql_select("select a.prod_id from inv_receive_master b, inv_transaction a where b.id=a.mst_id and b.entry_form in(7,17,37,66,68) and a.receive_basis not in(1,2) and b.receive_basis not in(1,2) and a.transaction_type=1 and a.status_active=1 and a.company_id=$cbo_company_name $sql_cond_1
				union all select prod_id from  inv_transaction where transaction_type=5 and status_active=1 and company_id=$cbo_company_name $sql_cond_2");
		}


		/*echo "select a.prod_id from inv_receive_master b, inv_transaction a where b.id=a.mst_id and b.entry_form in(7,17,37,66,68) $basis_cond and a.transaction_type=1 and a.status_active=1 and a.company_id=$cbo_company_name";die;*/
		$search_product=array();
		foreach($sql_purchase as $row)
		{
			$search_product[$row[csf("prod_id")]]=$row[csf("prod_id")];
		}
		if ($cbo_store_name!=0) $sql_cond.=" and a.store_id='$cbo_store_name'";
		$data_trns_array=array();
		/*$trnasactionSql="Select a.prod_id, a.cons_uom,a.transaction_date,
			sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
			sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
			sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$select_from_date."' then a.cons_amount else 0 end) as rcv_total_opening_amt,
			sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$select_from_date."' then a.cons_amount else 0 end) as iss_total_opening_amt,    
			sum(case when a.transaction_type=1 and a.transaction_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as receive,
			sum(case when a.transaction_type=2 and a.transaction_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as issue,
			sum(case when a.transaction_type=3 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as rec_return,
			sum(case when a.transaction_type=4 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as issue_return,
			sum(case when a.transaction_type=5 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as trans_rcv,
			sum(case when a.transaction_type=6 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as trans_issue,
			sum(case when a.transaction_type in(1,4,5) and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_amount else 0 end) as rcv_total_value,
			sum(case when a.transaction_type in(2,3,6) and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_amount else 0 end) as iss_total_value,
			sum(case when a.transaction_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_rate else 0 end) as rate,
			sum((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end)-(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end) ) as closing_stock
			from inv_transaction a
			where a.status_active=1 and a.is_deleted=0 $sql_cond 
			group by a.cons_uom, a.prod_id,a.transaction_date 
			order by a.cons_uom, a.prod_id ASC";*/

			$trnasactionSql="Select a.prod_id, b.unit_of_measure as cons_uom,a.transaction_date,
			sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
			sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
			sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$select_from_date."' then a.cons_amount else 0 end) as rcv_total_opening_amt,
			sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$select_from_date."' then a.cons_amount else 0 end) as iss_total_opening_amt,    
			sum(case when a.transaction_type=1 and a.transaction_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as receive,
			sum(case when a.transaction_type=2 and a.transaction_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as issue,
			sum(case when a.transaction_type=3 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as rec_return,
			sum(case when a.transaction_type=4 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as issue_return,
			sum(case when a.transaction_type=5 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as trans_rcv,
			sum(case when a.transaction_type=6 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as trans_issue,
			sum(case when a.transaction_type in(1,4,5) and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_amount else 0 end) as rcv_total_value,
			sum(case when a.transaction_type in(2,3,6) and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_amount else 0 end) as iss_total_value,
			sum(case when a.transaction_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_rate else 0 end) as rate,
			sum((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end)-(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end) ) as closing_stock
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 $sql_cond 
			group by b.unit_of_measure, a.prod_id, a.transaction_date 
			order by b.unit_of_measure, a.prod_id ASC";

		//echo $trnasactionSql;	
		$trnasactionData=sql_select($trnasactionSql);
		foreach($trnasactionData as $row)
		{
			$trns_date=date('Y-m-d',strtotime($row[csf("transaction_date")]));
			$date_frm=date('Y-m-d',strtotime($select_from_date));
			$date_to=date('Y-m-d',strtotime($select_from_to));

			if($trns_date<$date_frm)
			{
				if($search_product[$row[csf("prod_id")]]>0)
				{
					$data_trns_array[$row[csf("cons_uom")]][$row[csf("prod_id")]]['rcv_total_opening']+=$row[csf("rcv_total_opening")];
					$data_trns_array[$row[csf("cons_uom")]][$row[csf("prod_id")]]['iss_total_opening']+=$row[csf("iss_total_opening")];
					$data_trns_array[$row[csf("cons_uom")]][$row[csf("prod_id")]]['rcv_total_opening_amt2']+=$row[csf("rcv_total_opening_amt")];
					$data_trns_array[$row[csf("cons_uom")]][$row[csf("prod_id")]]['iss_total_opening_amt2']+=$row[csf("iss_total_opening_amt")];
				}
				
			}

			if($trns_date>=$date_frm && $trns_date<=$date_to)
			{
				if($search_product[$row[csf("prod_id")]]>0)
				{
					$data_trns_array[$row[csf("cons_uom")]][$row[csf("prod_id")]]['rcv_total_opening']+=$row[csf("rcv_total_opening")];
					$data_trns_array[$row[csf("cons_uom")]][$row[csf("prod_id")]]['iss_total_opening']+=$row[csf("iss_total_opening")];
					$data_trns_array[$row[csf("cons_uom")]][$row[csf("prod_id")]]['receive']+=$row[csf("receive")];
					$data_trns_array[$row[csf("cons_uom")]][$row[csf("prod_id")]]['issue_return']+=$row[csf("issue_return")];
					$data_trns_array[$row[csf("cons_uom")]][$row[csf("prod_id")]]['trans_rcv']+=$row[csf("trans_rcv")];
					$data_trns_array[$row[csf("cons_uom")]][$row[csf("prod_id")]]['issue']+=$row[csf("issue")];
					$data_trns_array[$row[csf("cons_uom")]][$row[csf("prod_id")]]['rec_return']=$row[csf("rec_return")];
					$data_trns_array[$row[csf("cons_uom")]][$row[csf("prod_id")]]['trans_issue']+=$row[csf("trans_issue")];
					$data_trns_array[$row[csf("cons_uom")]][$row[csf("prod_id")]]['avg_rate']=$row[csf("rate")];
					$data_trns_array[$row[csf("cons_uom")]][$row[csf("prod_id")]]['rcv_total_opening_amt']+=$row[csf("rcv_total_opening_amt")];
					$data_trns_array[$row[csf("cons_uom")]][$row[csf("prod_id")]]['iss_total_opening_amt']+=$row[csf("iss_total_opening_amt")];
					$data_trns_array[$row[csf("cons_uom")]][$row[csf("prod_id")]]['rcv_total_value']+=$row[csf("rcv_total_value")];
					$data_trns_array[$row[csf("cons_uom")]][$row[csf("prod_id")]]['iss_total_value']+=$row[csf("iss_total_value")];
					//$tot_qnty+=($row[csf("rcv_total_opening")]+$row[csf("receive")]+$row[csf("issue_return")]+$row[csf("trans_rcv")])-($row[csf("iss_total_opening")]+$row[csf("issue")]+$row[csf("rec_return")]+$row[csf("trans_issue")]);
				}
			}
		}
		//print_r($data_trns_test[12]);die;
		//echo $tot_qnty.jahid;die;
		$i=1;
		ob_start();	
		
		?>
		<div> 
		<table width="2200" border="1" cellpadding="2" cellspacing="0" class="" id="table_header_1" > 
			<thead>
				<tr class="form_caption" style="border:none;">
					<td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td> 
				</tr>
				<tr style="border:none;">
					<td colspan="16" align="center" style="border:none; font-size:14px;">
					   <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>                               
					</td>
				</tr>
				<tr style="border:none;">
					<td colspan="16" align="center" style="border:none;font-size:12px; font-weight:bold">
						<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
					</td>
				</tr>
			</thead>
		</table>
		<table width="2200" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" > 
			<thead>
				<tr>
					<th rowspan="2" width="40">SL</th>
					<th colspan="7">Description</th>
					<th rowspan="2" width="100">Opening Rate</th>
					<th rowspan="2" width="110">Opening Stock</th>
					<th rowspan="2" width="100">Opening Value</th>
					<th colspan="5">Receive</th>
					<th colspan="5">Issue</th>
					<th rowspan="2" width="100">Closing Stock</th>
					<th rowspan="2" width="100">Closing Stock Value</th>
					<th rowspan="2">DOH</th>
				</tr> 
				<tr> 
					<th width="60">Prod. ID</th>                    
					<th width="120">Construction</th>
					<th width="180">Composition</th>
					<th width="70">GSM</th>
					<th width="80">Dia/ Width</th>
					<th width="100">Color</th>
					<th width="50">UOM</th>
					<th width="80">Purchase</th>
					<th width="80">Issue Return</th>
                    <th width="80">Transfer In</th> 
					<th width="100">Total Received</th>
					<th width="100">Total Received Value</th>
					<th width="80">Issue</th>
					<th width="80">Receive Return</th>
                    <th width="80">Transfer Out</th>
					<th width="100">Total Issue</th> 
					<th width="100">Total Issue Value</th>
				</tr>
			</thead>
		</table>
		<div style="width:2220px; max-height:280px; overflow-y:scroll" id="scroll_body" > 
		<table width="2200" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
		<?
			$composition_arr=array();
			$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
			$data_array=sql_select($sql_deter);
			if(count($data_array)>0)
			{
				foreach( $data_array as $row )
				{
					if(array_key_exists($row[csf('id')],$composition_arr))
					{
						$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					}
					else
					{
						$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					}
				}
			}
			
			$sql_all_cond="";
			if ($cbo_company_name!=0) $sql_all_cond =" and company_id='$cbo_company_name'";
			if ($cbo_item_category_id!=0) $sql_all_cond.=" and item_category_id='$cbo_item_category_id'";
			if ($txt_product_id_des!="") $sql_all_cond.=" and id in ($txt_product_id_des)";
			if ($txt_product_id!="") $sql_all_cond.=" and id in ($txt_product_id)";
			//if ($cbo_store_name!=0) $sql_all_cond.=" and store_id='$cbo_store_name'";
			//if($cbo_uom!=0) $sql_all_cond.=" and unit_of_measure in ($cbo_uom)";
		   
		    $sql="select id, detarmination_id, gsm, dia_width, color, unit_of_measure, current_stock from product_details_master where status_active=1 and is_deleted=0 $sql_all_cond order by unit_of_measure desc, id asc";
			$result = sql_select($sql);
			$product_data=array();
			foreach($result as $row)
			{
				$product_data[$row[csf("id")]]["id"]=$row[csf("id")];
				$product_data[$row[csf("id")]]["detarmination_id"]=$row[csf("detarmination_id")];
				$product_data[$row[csf("id")]]["gsm"]=$row[csf("gsm")];
				$product_data[$row[csf("id")]]["dia_width"]=$row[csf("dia_width")];
				$product_data[$row[csf("id")]]["color"]=$row[csf("color")];
				$product_data[$row[csf("id")]]["unit_of_measure"]=$row[csf("unit_of_measure")];
				$product_data[$row[csf("id")]]["current_stock"]=$row[csf("current_stock")];
			}
			$tot_opening_bal=0;$tot_purchuse=0;$tot_issue_return=0;$tot_receive=0; $tot_issue=0;$tot_receive_return=0;$tot_total_issue=0;$total_closing_stock=0;$tot_closing_stock=0;$sub_uom="";
			$i=1;
			foreach($data_trns_array as $uom=>$uom_data)
			{
				foreach($uom_data as $prod_id=>$prod_val)
				{
					if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
					$openingBalance =$prod_val['rcv_total_opening']-$prod_val['iss_total_opening'];
					$openingAmount =$prod_val['rcv_total_opening_amt2']-$prod_val['iss_total_opening_amt2'];
					$totOpeningRate=0;
					if ($openingBalance>0) 
					{
						$totOpeningRate=$openingAmount/$openingBalance;
					}
					
					$totalReceive=$prod_val['receive']+$prod_val['issue_return']+$prod_val['trans_rcv'];//
					$totalIssue=$prod_val['issue']+$prod_val['rec_return']+$prod_val['trans_issue'];//
					
					$tot_rcv_value = $prod_val['rcv_total_value'];
					$tot_iss_value = $prod_val['iss_total_value'];
					/*$totOpeningRate=$totOpeningValue=0;
					$totOpeningRate =0;
					if($prod_val['rcv_total_opening'] > 0 && $prod_val['rcv_total_opening_amt'])
					{
						$totOpeningRate = $prod_val['rcv_total_opening_amt'] / $prod_val['rcv_total_opening'];
					}*/
					$totOpeningValue = $openingBalance * $totOpeningRate;
					$closingStock=$openingBalance+$totalReceive-$totalIssue;
					$closingStockValue = ($totOpeningValue+$tot_rcv_value) - $tot_iss_value;
					
					$closingStockQnty=number_format($closingStock,2,".","");
					$closingStockQnty= str_replace("-0.00", "0.00", $closingStockQnty) ;

					$openingBalance=number_format($openingBalance,2,".","");
					$openingBalance= str_replace("-0.00", "0.00", $openingBalance) ;

					$closingStockValue=number_format($closingStockValue,2,".","");
					$closingStockValue= str_replace("-0.00", "0.00", $closingStockValue) ;

					$totOpeningRate=number_format($totOpeningRate,2,".","");
					$totOpeningRate= str_replace("-0.00", "0.00", $totOpeningRate) ;

					$totOpeningValue=number_format($totOpeningValue,2,".","");
					$totOpeningValue= str_replace("-0.00", "0.00", $totOpeningValue) ;
					
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="60"><? echo $prod_id; ?></td>								
                        <td width="120" title="<? echo $prod_id;?>"><p><? echo $determinaArr[$product_data[$prod_id]["detarmination_id"]]; ?></p></td>                                 
                        <td width="180"><p><? echo $composition_arr[$product_data[$prod_id]["detarmination_id"]]; ?></p></td>
                        <td width="70"><p><? echo $product_data[$prod_id]["gsm"]; ?></p></td> 
                        <td width="80"><p><? echo $product_data[$prod_id]["dia_width"]; ?></p></td> 
                        <td width="100"><p class="wrap_break_word"><? echo $color_arr[$product_data[$prod_id]["color"]]; ?></p></td>
                        <td width="50" align="center"><p class="wrap_break_word"><? echo $unit_of_measurement[$uom]; ?></p></td>
                        
                        <td width="100" align="right"><p><? echo number_format($totOpeningRate,3);?></p></td>
                        <td width="110" align="right"><p><? echo number_format($openingBalance,2,'.','');$tot_opening_bal+=$openingBalance; ?></p></td>
                        <td width="100" align="right"><p><? echo number_format($totOpeningValue,3,'.','');$tot_opening_value+=$totOpeningValue;?></p></td>
                        <td width="80" align="right"><p><? echo number_format($prod_val['receive'],2,'.',''); $tot_purchuse+=$prod_val['receive']; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($prod_val['issue_return'],2,'.',''); $tot_issue_return+=$prod_val['issue_return']; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($prod_val['trans_rcv'],2,'.',''); $tot_trans_rcv+=$prod_val['trans_rcv']; ?></p></td>
                        <td width="100" align="right"><p><? echo number_format($totalReceive,2,'.',''); $tot_receive+=$totalReceive; ?></p></td>
                        <td width="100" align="right"><? echo number_format($tot_rcv_value,3,'.',''); $grand_tot_rcv_value+=$tot_rcv_value?></td>
                                            
                        <td width="80" align="right"><p><? echo number_format($prod_val['issue'],2,'.',''); $tot_issue+=$prod_val['issue']; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($prod_val['rec_return'],2,'.',''); $tot_receive_return+=$prod_val['rec_return']; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($prod_val['trans_issue'],2,'.',''); $tot_trans_issue+=$prod_val['trans_issue']; ?></p></td>
                        <td width="100" align="right"><p><? echo number_format($totalIssue,2,'.',''); $tot_total_issue+=$totalIssue; ?></p></td>
                        <td width="100" align="right"><? echo number_format($tot_iss_value,3,'.',''); $grand_tot_iss_value+=$tot_iss_value;?></td>
                        <td width="100" align="right"><p><? echo number_format($closingStockQnty,2,'.',''); $total_closing_stock+=$closingStock; ?></p></td>
                        <td width="100" align="right"><p><? echo number_format($closingStockValue,2,'.',''); $tot_closingStockValue+=$closingStockValue;?></p></td>
                        <td align="right" style="min-width: 36px;"><? echo $days_doh[$prod_id]['daysonhand'];?></td>
                    </tr>
                    <?
					$i++;
					$sub_opening_bal+=$openingBalance;									
					$sub_opening_value+=$totOpeningValue;									
					$sub_purchuse+=$prod_val['receive'];									
					$sub_issue_return+=$prod_val['issue_return'];
					$sub_trans_rcv +=$prod_val['trans_rcv'];
					$sub_receive+=$totalReceive;
					$sub_issue+=$prod_val['issue'];			
					$sub_receive_return+=$prod_val['rec_return'];
					$sub_trans_issue+=$prod_val['trans_issue'];	
					
					$sub_total_issue+=$totalIssue;	
					$sub_tot_iss_value+=$tot_iss_value;	
					$sub_closing_stock+=$closingStock;	
					$sub_closingStockValue+=$closingStockValue;
				}
				?>
                <tr style="font-weight: bold;">			                
                    <td align="right" width="650" colspan="7">Sub Total</td>
                    <td align="center" width="50"><? echo $unit_of_measurement[$uom]; ?></td>
                    <td align="right" width="100">&nbsp;</td>
                    <td align="right" width="110" ><? echo number_format($sub_opening_bal,2); ?></td>
                    <td align="right" width="100" ><? echo number_format($sub_opening_value,2); ?></td>
                    <td align="right" width="80" ><? echo number_format($sub_purchuse,2); ?></td>
                    <td align="right" width="80" ><? echo number_format($sub_issue_return,2); ?></td>
                    <td align="right" width="80" ><? echo number_format($sub_trans_rcv,2); ?></td>
                    <td align="right" width="100" ><? echo number_format($sub_receive,2); ?></td>
                    <td align="right" width="100"><? echo number_format($sub_tot_rcv_value,3);?></td>
                    <td align="right" width="80"  ><? echo number_format($sub_issue,2); ?></td>
                    <td align="right" width="80" ><? echo number_format($sub_receive_return,2); ?></td>
                    <td align="right" width="80" ><? echo number_format($sub_trans_issue,2); ?></td>
                    <td align="right" width="100" ><? echo number_format($sub_total_issue,2); ?></td>
                    <td align="right" width="100" ><? echo number_format($sub_tot_iss_value,3)?></td>
                    <td align="right" width="100" ><? echo number_format($sub_closing_stock,3); ?></td>
                    <td align="right" width="100" ><p><? echo number_format($sub_closingStockValue,3); ?></p></td>
                    <td align="right" style="min-width: 36px;">&nbsp;&nbsp;&nbsp;</td>  
                </tr>
                <?
				$sub_opening_bal=$sub_opening_value=$sub_purchuse=$sub_issue_return=$sub_trans_rcv=$sub_receive_return=$sub_trans_issue=$sub_total_issue=$sub_tot_iss_value=$sub_closing_stock=$sub_closingStockValue=$sub_trans_rcv=$sub_trans_issue=$sub_receive=0;
			}
		?>
		</table>
	   </div>
		<table width="2200" border="1" cellpadding="2" cellspacing="0" class="tbl_bottom" rules="all" id="">
			<tr>
				<td align="right" width="40">&nbsp;</td>
				<td align="right" width="60">&nbsp;</td>
				<td align="right" width="120">&nbsp;</td>
				<td align="right" width="180">&nbsp;</td>
				<td align="right" width="70">&nbsp;</td>
				<td align="right" width="80">&nbsp;</td>
				<td align="right" width="100">&nbsp;</td>
				<td align="right" width="50">Total</td>
				<td align="right" width="100">&nbsp;</td>
				<td align="right" width="110" ><? echo number_format($tot_opening_bal,2); ?></td>
				<td align="right" width="100" ><? echo number_format($tot_opening_value,2); ?></td>
				<td align="right" width="80" ><? echo number_format($tot_purchuse,2); ?></td>
				<td align="right" width="80" ><? echo number_format($tot_issue_return,2); ?></td>
                <td align="right" width="80" ><? echo number_format($tot_trans_rcv,2); ?></td>
				<td align="right" width="100" ><? echo number_format($tot_receive,2); ?></td>
				<td align="right" width="100"><? echo number_format($grand_tot_rcv_value,3);?></td>
				<td align="right" width="80"  ><? echo number_format($tot_issue,2); ?></td>
				<td align="right" width="80" ><? echo number_format($tot_receive_return,2); ?></td>
                <td align="right" width="80" ><? echo number_format($tot_trans_issue,2); ?></td>
				<td align="right" width="100" ><? echo number_format($tot_total_issue,2); ?></td>
				<td align="right" width="100" ><? echo number_format($grand_tot_iss_value,3)?></td>
				<td align="right" width="100" ><? echo number_format($total_closing_stock,3); ?></td>
				<td align="right" width="100" ><p><? echo number_format($tot_closingStockValue,3); ?></p></td>
				<td align="right" style="min-width: 36px;">&nbsp;&nbsp;&nbsp;</td>  
			</tr>
		</table>
        </div>
        <?
	}
	else if($report_type == 5)
	{
	
		if ($cbo_store_name!=0) $sql_cond.=" and store_id='$cbo_store_name'";
		$buyerFromTransIdRes = sql_select("select a.trans_id,c.buyer_name
		from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c
		where a.po_breakdown_id = b.id and b.job_no_mst = c.job_no and a.trans_id > 0
		order by c.buyer_name");
		foreach ($buyerFromTransIdRes as $value) {
			$buyerFromTransIdArr[$value[csf("trans_id")]] = $value[csf("buyer_name")];
		}

		$batch_ref_arr =array();$booking_ref_arr=array(); $buyerFromBatchArr = array();
		//echo "select a.id , a.batch_no, a.booking_without_order,a.booking_no_id from pro_batch_create_mst a	where a.status_active = 1 and a.is_deleted = 0 and a.company_id = $cbo_company_name";die;
		$batch_ref_arr =  sql_select("select a.id , a.batch_no, a.booking_without_order,a.booking_no_id
		from pro_batch_create_mst a	where a.status_active = 1 and a.is_deleted = 0 and a.company_id = $cbo_company_name");
		foreach ($batch_ref_arr as  $batch_row) 
		{
			//if($batch_row[csf("booking_without_order")] == 1)
			//{
				$bookingBatchId[$batch_row[csf("booking_no_id")]] = $batch_row[csf("id")];
			//}
		}
		// echo "<pre>";
		// var_dump($bookingBatchId);
		// echo "</pre>";
		// die("with batch");
		//echo "select b.buyer_id, b.id, 1 as type from wo_non_ord_samp_booking_mst b	where b.status_active = 1 and b.is_deleted = 0 and b.item_category in (2,3) and b.company_id = $cbo_company_name	union	select c.buyer_id, c.id, 2 as type from  wo_booking_mst c where c.status_active = 1 and c.is_deleted = 0 and c.item_category in (2,3) and c.company_id = $cbo_company_name";die;

		$booking_ref_arr = sql_select("select b.buyer_id, b.id, 1 as type
		from wo_non_ord_samp_booking_mst b
		where b.status_active = 1 and b.is_deleted = 0 and b.item_category in (2,3) and b.company_id = $cbo_company_name
		union
		select c.buyer_id, c.id, 2 as type 
		from  wo_booking_mst c
		where c.status_active = 1 and c.is_deleted = 0 and c.item_category in (2,3) and c.company_id = $cbo_company_name");

		foreach ($booking_ref_arr as $book_row) 
		{
			if($book_row[csf("type")] == 1)
			{
				$buyerFromBatchArr[$bookingBatchId[$book_row[csf("id")]]]["non_order"]= $book_row[csf("buyer_id")];
			}
			else{
				$buyerFromBatchArr[$bookingBatchId[$book_row[csf("id")]]]["order"]= $book_row[csf("buyer_id")];
			}
					
		}
		// echo "<pre>";
		// var_dump($buyerFromBatchArr);
		// echo "</pre>";
		// die("with batch");




		/*foreach ($batch_ref_arr as  $batch_row) 
		{
			foreach ($booking_ref_arr as $book_row) 
			{
				if($batch_row[csf("booking_without_order")] == 1 && $book_row[csf("type")] == 1 && ($book_row[csf("id")] == $batch_row[csf("booking_no_id")]))
				{
					$buyerFromBatchArr[$batch_row[csf("id")]]["non_order"]= $book_row[csf("buyer_id")];
					break;
				}
				else if(($batch_row[csf("booking_without_order")] == 0 || $batch_row[csf("booking_without_order")] == "") && $book_row[csf("type")] == 2 && $book_row[csf("id")] == $batch_row[csf("booking_no_id")])
				{
					$buyerFromBatchArr[$batch_row[csf("id")]]["order"]= $book_row[csf("buyer_id")];
					break;
				}
						
			}
			
		}*/

		unset($buyerFromTransIdRes);
		unset($booking_ref_arr);
		unset($batch_ref_arr);
		/*echo "<pre>";
		print_r($buyerFromBatchArr);
		echo "</pre>";
		die;*/
		$sql = "Select a.id as trans_id, b.unit_of_measure,a.pi_wo_batch_no,a.batch_id,a.transaction_type,a.item_category,
		(case when a.transaction_type in (1,4,5) and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
		(case when a.transaction_type in (2,3,6) and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as iss_total_opening,

		(case when a.transaction_type=1 and a.transaction_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as receive,
		(case when a.transaction_type=2 and a.transaction_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as issue,
		(case when a.transaction_type=3 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as rec_return,
		(case when a.transaction_type=4 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as issue_return,
		(case when a.transaction_type=5 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as trans_in,
		(case when a.transaction_type=6 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as trans_out
		from inv_transaction a, product_details_master b
		where a.prod_id = b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond and a.item_category in (2,3) order by b.unit_of_measure desc";
		//echo $sql;die;
		$result = sql_select($sql);
		$data_array = array();
		foreach ($result as $trans_row) 
		{
			if($trans_row[csf("item_category")] == 2 && $buyerFromTransIdArr[$trans_row[csf("trans_id")]] == "" )
			{
				$buyerFromBatch = $buyerFromBatchArr[$trans_row[csf("pi_wo_batch_no")]]["non_order"];
			}
			elseif ($trans_row[csf("item_category")] == 2 && $buyerFromTransIdArr[$trans_row[csf("trans_id")]] != "") {
				$buyerFromBatch = $buyerFromTransIdArr[$trans_row[csf("trans_id")]];
			}
			elseif ($trans_row[csf("item_category")] == 3 && $buyerFromTransIdArr[$trans_row[csf("trans_id")]] == "" ) {
				$buyerFromBatch = $buyerFromBatchArr[$trans_row[csf("batch_id")]]["non_order"];
			}else{
				$buyerFromBatch = $buyerFromTransIdArr[$trans_row[csf("trans_id")]];
			}
			$data_array[$trans_row[csf("unit_of_measure")]][$buyerFromBatch]["rcv_total_opening"] += $trans_row[csf("rcv_total_opening")];
			$data_array[$trans_row[csf("unit_of_measure")]][$buyerFromBatch]["iss_total_opening"] += $trans_row[csf("iss_total_opening")];
			$data_array[$trans_row[csf("unit_of_measure")]][$buyerFromBatch]["receive"] += $trans_row[csf("receive")];
			$data_array[$trans_row[csf("unit_of_measure")]][$buyerFromBatch]["issue"] += $trans_row[csf("issue")];
			$data_array[$trans_row[csf("unit_of_measure")]][$buyerFromBatch]["rec_return"] += $trans_row[csf("rec_return")];
			$data_array[$trans_row[csf("unit_of_measure")]][$buyerFromBatch]["issue_return"] += $trans_row[csf("issue_return")];
			$data_array[$trans_row[csf("unit_of_measure")]][$buyerFromBatch]["trans_in"] += $trans_row[csf("trans_in")];
			$data_array[$trans_row[csf("unit_of_measure")]][$buyerFromBatch]["trans_out"] += $trans_row[csf("trans_out")];
		}
		//echo $sql;
		/*echo "<pre>";
		print_r($data_array);
		echo "</pre>";
		die;*/
		?>
		<div style="width:1350px;">
			<table style="width:1300px" border="1" cellpadding="2" cellspacing="0" class="" id="table_header_1" align="left"> 
				<thead>
					<tr class="form_caption" style="border:none;">
						<td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" >Buyer Wise Finish Fabric Closing Summary Report</td> 
					</tr>
					<tr style="border:none;">
						<td colspan="14" align="center" style="border:none; font-size:14px;">
						   <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>                               
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="14" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
						</td>
					</tr>
				</thead>
			</table>
			<table style="width:1300px;" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left"> 
				<thead>
					<tr>
						<th rowspan="2" width="40">SL</th>
						<th rowspan="2" width="120">Buyer Name</th>
						<th rowspan="2" width="80">UOM</th>
						<th rowspan="2" width="120">Opening Stock</th>
						<th colspan="4">Receive</th>
						<th colspan="4">Issue</th>
						<th rowspan="2" width="100">Closing Stock</th>
						<th rowspan="2">Remarks</th>
					</tr> 
					<tr> 
						<th width="80">Receive</th>
						<th width="80">Receive Return</th> 
						<th width="100">Transfer In</th>
						<th width="100">Total Received</th>
						<th width="80">Issue</th>
						<th width="80">Issue Return</th>
						<th width="100">Transfer Out</th> 
						<th width="100">Total Issue</th>
					</tr> 
				</thead>
			</table>

			<div style="width:1320px; max-height:300px; overflow-y:scroll; float:left;" id="scroll_body"> 
			<table style="width:1300px;" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left">
				<?	$checkUOMArr= array();
					foreach ($data_array as $uom => $uomData) 
					{
						$i=1;$sub_total_opening_stock=0;$row_total_rcv=0;$sub_total_receive=0;$sub_total_rec_return=0;$sub_total_trans_in=0;$sub_total_row_total_rcv=0;$row_total_iss=0;$sub_total_issue=0;$sub_total_issue_return=0;$sub_total_trans_out=0;$sub_total_row_total_iss=0;$closingStock=0;$sub_total_closingStock = 0;
						foreach ($uomData as $buyer => $row) 
						{
							if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
							$opening_stock =  $row["rcv_total_opening"]- $row["iss_total_opening"];
							$row_total_rcv = ($row["receive"]+$row["trans_in"])-$row["rec_return"];
							$row_total_iss = ($row["issue"]+$row["trans_out"])-$row["issue_return"];
							$closingStock = ($opening_stock+$row_total_rcv) - $row_total_iss;
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $unit_of_measurement[$uom].'_'.$i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $unit_of_measurement[$uom].'_'.$i; ?>">
									<td width="40"><? echo $i;?></td>
									<td width="120" align="center"><? echo $buyer_arr[$buyer];?></td>
									<td width="80" align="center"><? echo $unit_of_measurement[$uom];?></td>
									<td width="120" align="right">
									<? 
										echo $opening_stock;
									?>        								
									</td>
									<td width="80" align="right"><?echo number_format($row["receive"],2);?></td>
									<td width="80" align="right"><?echo number_format($row["rec_return"],2);?></td>
									<td width="100" align="right"><?echo number_format($row["trans_in"],2);?></td>
									<td width="100" align="right"><?echo number_format($row_total_rcv,2);?></td>
									<td width="80" align="right"><?echo number_format($row["issue"],2);?></td>
									<td width="80" align="right"><?echo number_format($row["issue_return"],2);?></td>
									<td width="100" align="right"><?echo number_format($row["trans_out"],2);?></td>
									<td width="100" align="right"><?echo number_format($row_total_iss,2);?></td>
									<td width="100" align="right"><?echo number_format($closingStock,2);?></td>
									<td>&nbsp;</td>
								</tr>
							<?
							$sub_total_opening_stock += $opening_stock;
							$sub_total_receive += $row["receive"];
							$sub_total_rec_return += $row["rec_return"];
							$sub_total_trans_in += $row["trans_in"];
							$sub_total_row_total_rcv += $row_total_rcv;
							$sub_total_issue += $row["issue"];
							$sub_total_issue_return += $row["issue_return"];
							$sub_total_trans_out += $row["trans_out"];
							$sub_total_row_total_iss +=$row_total_iss;
							$sub_total_closingStock += $closingStock;
							$i++;
						}
						?>
							<tr style="background-color: #dcdcdc; font-weight: bold;">
								<td colspan="2" align="right">Sub Total</td>
								<td align="center"><? echo $unit_of_measurement[$uom];?></td>
								<td align="right"><? echo number_format($sub_total_opening_stock,2)?></td>
								<td align="right"><? echo number_format($sub_total_receive,2)?></td>
								<td align="right"><? echo number_format($sub_total_rec_return,2)?></td>
								<td align="right"><? echo number_format($sub_total_trans_in,2)?></td>
								<td align="right"><? echo number_format($sub_total_row_total_rcv,2)?></td>
								<td align="right"><? echo number_format($sub_total_issue,2)?></td>
								<td align="right"><? echo number_format($sub_total_issue_return,2)?></td>
								<td align="right"><? echo number_format($sub_total_trans_out,2)?></td>
								<td align="right"><? echo number_format($sub_total_row_total_iss,2)?></td>
								<td align="right"><? echo number_format($sub_total_closingStock,2)?></td>
								<td>&nbsp;</td>
							</tr>
						<?
					}

				?>
			</table>
			</div>
		</div>
		<?
	
		
	}
	else if($report_type == 7)
	{
		if ($cbo_store_name!=0) $sql_cond.=" and a.store_id='$cbo_store_name'";
		
		$booking_ref_arr = sql_select("select b.buyer_id, b.id, b.booking_no, 1 as type
		from wo_non_ord_samp_booking_mst b
		where b.status_active = 1 and b.is_deleted = 0 and b.item_category in (2,3) and b.company_id = $cbo_company_name
		union
		select c.buyer_id, c.id, c.booking_no, 0 as type 
		from  wo_booking_mst c
		where c.status_active = 1 and c.is_deleted = 0 and c.item_category in (2,3) and c.company_id = $cbo_company_name");
		$booking_buyer_data=array();
		foreach($booking_ref_arr as $row)
		{
			$booking_buyer_data[$row[csf("booking_no")]]=$row[csf("buyer_id")];
		}

		$batch_ref_arr =  sql_select("select a.id, a.batch_no, a.booking_without_order, a.booking_no_id, a.booking_no
		from pro_batch_create_mst a
		where a.status_active = 1 and a.is_deleted = 0 and a.company_id = $cbo_company_name");
		foreach ($batch_ref_arr as  $batch_row) 
		{
			if($booking_buyer_data[$batch_row[csf("booking_no")]])
			{
				$buyerFromBatchArr[$batch_row[csf("id")]]=$booking_buyer_data[$batch_row[csf("booking_no")]];
			}
		}
		//print_r($buyerFromBatchArr[22822]);die;
		unset($booking_ref_arr);
		unset($batch_ref_arr);
		/*echo "<pre>";
		print_r($buyerFromBatchArr);
		echo "</pre>";
		die;*/
		//7,14,15,17,18,19,37,46,52,66,68,71,126,134,195,196,202,209,216,219,225,233
		$buyerFromTransIdRes = sql_select("SELECT a.trans_id, a.is_sales, c.buyer_name 
		from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c 
		where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.entry_form in(7,14,15,17,18,19,37,46,52,66,68,71,126,134,195,196,202,209,216,219,225,233) and a.trans_id > 0");
		foreach ($buyerFromTransIdRes as $value) 
		{
			$buyerFromTransIdArr[$value[csf("trans_id")]] = $value[csf("buyer_name")];
		}

		$sql_purchase=sql_select("select a.prod_id, a.receive_basis from inv_receive_master b, inv_transaction a 
		where b.id=a.mst_id and b.entry_form in(7,17,37,66,68) and a.transaction_type=1 and a.status_active=1 and a.company_id=$cbo_company_name");
		$purchase_product=array();
		foreach($sql_purchase as $row)
		{
			if($row[csf("receive_basis")]==1 || $row[csf("receive_basis")]==2)
			{
				$purchase_product[$row[csf("prod_id")]]=$row[csf("prod_id")];
			}
			else
			{
				$production_product[$row[csf("prod_id")]]=$row[csf("prod_id")];
			}
			
		}
		//print_r($purchase_product);die;
		
		$sql = "Select a.id as trans_id, (case when a.transaction_type=2 and a.item_category = 3  then b.unit_of_measure else a.cons_uom end) as unit_of_measure, a.pi_wo_batch_no, a.batch_id, a.transaction_type, a.item_category, a.prod_id,
		(case when a.transaction_type in (1,4,5) and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
		(case when a.transaction_type in (2,3,6) and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
		(case when a.transaction_type=1 and a.transaction_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as receive,
		(case when a.transaction_type=2 and a.transaction_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as issue,
		(case when a.transaction_type=3 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as rec_return,
		(case when a.transaction_type=4 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as issue_return,
		(case when a.transaction_type=5 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as trans_in,
		(case when a.transaction_type=6 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as trans_out,
		((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end)-(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end) ) as closing_stock
		from inv_transaction a left join product_details_master b on a.prod_id = b.id 
		where a.status_active=1 and a.is_deleted=0 $sql_cond
		order by a.cons_uom";
		//echo $sql."<br>";//die;
		$result = sql_select($sql);
		//echo "<pre>";print_r($result);die;
		
		$data_array = array();$purchase_data=array();$production_data=array();
		foreach ($result as $trans_row) 
		{
			$addi = "";
			/*if($trans_row[csf("item_category")] == 2 && $buyerFromTransIdArr[$trans_row[csf("trans_id")]] == "" )
			{
				$buyerFromBatch = $buyerFromBatchArr[$trans_row[csf("pi_wo_batch_no")]];
				$addi = "a";
			}*/
			if($trans_row[csf("item_category")] == 2 && $buyerFromBatchArr[$trans_row[csf("pi_wo_batch_no")]] )
			{
				$buyerFromBatch = $buyerFromBatchArr[$trans_row[csf("pi_wo_batch_no")]];
				$addi = "a2";
			}
			elseif ($trans_row[csf("item_category")] == 2 && $buyerFromTransIdArr[$trans_row[csf("trans_id")]] != "") 
			{
				$buyerFromBatch = $buyerFromTransIdArr[$trans_row[csf("trans_id")]];
				$addi = "b";
			}
			elseif($trans_row[csf("item_category")]==3 && $buyerFromBatchArr[$trans_row[csf("batch_id")]]) 
			{
				$buyerFromBatch = $buyerFromBatchArr[$trans_row[csf("batch_id")]];
				$addi = "c";
			}
			else
			{
				$buyerFromBatch = $buyerFromTransIdArr[$trans_row[csf("trans_id")]];
				$addi = "d";
			}
			

			/*if($buyerFromBatch == "")
			{
				echo $trans_row[csf("trans_id")]."==".$addi;die;
			}*/

			if($production_product[$trans_row[csf("prod_id")]] > 0)
			{
				$production_data[$trans_row[csf("unit_of_measure")]][$buyerFromBatch]["rcv_total_opening"] += $trans_row[csf("rcv_total_opening")];
				$production_data[$trans_row[csf("unit_of_measure")]][$buyerFromBatch]["iss_total_opening"] += $trans_row[csf("iss_total_opening")];
				$production_data[$trans_row[csf("unit_of_measure")]][$buyerFromBatch]["receive"] += $trans_row[csf("receive")];
				$production_data[$trans_row[csf("unit_of_measure")]][$buyerFromBatch]["issue"] += $trans_row[csf("issue")];
				$production_data[$trans_row[csf("unit_of_measure")]][$buyerFromBatch]["rec_return"] += $trans_row[csf("rec_return")];
				$production_data[$trans_row[csf("unit_of_measure")]][$buyerFromBatch]["issue_return"] += $trans_row[csf("issue_return")];
				$production_data[$trans_row[csf("unit_of_measure")]][$buyerFromBatch]["trans_in"] += $trans_row[csf("trans_in")];
				$production_data[$trans_row[csf("unit_of_measure")]][$buyerFromBatch]["trans_out"] += $trans_row[csf("trans_out")];
				$production_data[$trans_row[csf("unit_of_measure")]][$buyerFromBatch]["trans_id"] += $trans_row[csf("trans_id")];
			}
			
			if($purchase_product[$trans_row[csf("prod_id")]] > 0)
			{
				$purchase_data[$trans_row[csf("unit_of_measure")]][$buyerFromBatch]["rcv_total_opening"] += $trans_row[csf("rcv_total_opening")];
				$purchase_data[$trans_row[csf("unit_of_measure")]][$buyerFromBatch]["iss_total_opening"] += $trans_row[csf("iss_total_opening")];
				$purchase_data[$trans_row[csf("unit_of_measure")]][$buyerFromBatch]["receive"] += $trans_row[csf("receive")];
				$purchase_data[$trans_row[csf("unit_of_measure")]][$buyerFromBatch]["issue"] += $trans_row[csf("issue")];
				$purchase_data[$trans_row[csf("unit_of_measure")]][$buyerFromBatch]["rec_return"] += $trans_row[csf("rec_return")];
				$purchase_data[$trans_row[csf("unit_of_measure")]][$buyerFromBatch]["issue_return"] += $trans_row[csf("issue_return")];
				$purchase_data[$trans_row[csf("unit_of_measure")]][$buyerFromBatch]["trans_in"] += $trans_row[csf("trans_in")];
				$purchase_data[$trans_row[csf("unit_of_measure")]][$buyerFromBatch]["trans_out"] += $trans_row[csf("trans_out")];
				$test_qnty+=($trans_row[csf("rcv_total_opening")]+$trans_row[csf("receive")]+$trans_row[csf("issue_return")]+$trans_row[csf("trans_in")])-($trans_row[csf("iss_total_opening")]+$trans_row[csf("issue")]+$trans_row[csf("rec_return")]+$trans_row[csf("trans_out")]);
				$test_opening+=$trans_row[csf("rcv_total_opening")]-$trans_row[csf("iss_total_opening")];
				$test_rcv+=$trans_row[csf("receive")]+$trans_row[csf("issue_return")]+$trans_row[csf("trans_in")];
				$test_issue+=$trans_row[csf("issue")]+$trans_row[csf("rec_return")]+$trans_row[csf("trans_out")];
			}
		}
		//echo implode(",",$production_product);die;
		//echo $test_qnty."=".$test_opening."=".$test_rcv."=".$test_issue."<br>";
		//echo $test_data;die;
		//echo $sql;
		//echo "<pre>";print_r($test_data);die;
		?>
		<div>
			<table width="1320" border="1" cellpadding="2" cellspacing="0" class="" id="table_header_1" > 
				<thead>
					<tr class="form_caption" style="border:none;">
						<td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" >Buyer Wise Finish Fabric Closing Summary Report</td> 
					</tr>
					<tr style="border:none;">
						<td colspan="14" align="center" style="border:none; font-size:14px;">
						   <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>                               
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="14" align="center" style="border:none; font-size:14px;"><b>Prodution Report</b>                               
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="14" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
						</td>
					</tr>
				</thead>
			</table>
			<table width="1320" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
				<thead>
					<tr>
						<th rowspan="2" width="40">SL</th>
						<th rowspan="2" width="120" title="This report only for Garments not for sales order or textile. So sales order wise buyer is not showing.">Buyer Name</th>
						<th rowspan="2" width="80">UOM</th>
						<th rowspan="2" width="120">Opening Stock</th>
						<th colspan="4">Receive</th>
						<th colspan="4">Issue</th>
						<th rowspan="2" width="100">Closing Stock</th>
						<th rowspan="2">Remarks</th>
					</tr> 
					<tr> 
						<th width="80">Receive</th>
						<th width="80">Issue Return</th> 
						<th width="100">Transfer In</th>
						<th width="100">Total Received</th>
						<th width="80">Issue</th>
						<th width="80">Receive Return</th>
						<th width="100">Transfer Out</th> 
						<th width="100">Total Issue</th>
					</tr> 
				</thead>
			</table>
			<div style="width:1320px; max-height:300px; overflow-y:scroll;" id="scroll_body"> 
			<table width="1300" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
				<?	
				$checkUOMArr= array();
				$i=1;
				foreach ($production_data as $uom => $uomData) 
				{
					$sub_total_opening_stock=0;$row_total_rcv=0;$sub_total_receive=0;$sub_total_rec_return=0;$sub_total_trans_in=0;$sub_total_row_total_rcv=0;$row_total_iss=0;$sub_total_issue=0;$sub_total_issue_return=0;$sub_total_trans_out=0;$sub_total_row_total_iss=0;$closingStock=0;$sub_total_closingStock = 0;
					foreach ($uomData as $buyer => $row) 
					{
						$closingStock = 0;
						if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
						$opening_stock =  $row["rcv_total_opening"]- $row["iss_total_opening"];
						$row_total_rcv = $row["receive"]+$row["issue_return"]+$row["trans_in"];
						$row_total_iss = $row["issue"]+$row["rec_return"]+$row["trans_out"];
						$closingStock = ($row["rcv_total_opening"]+$row["receive"]+$row["issue_return"]+$row["trans_in"]) - ($row["iss_total_opening"]+$row["issue"]+$row["rec_return"]+$row["trans_out"]);
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="40" align="center"><? echo $i;?></td>
                            <td width="120" align="center" title="<? echo 'Buyer='.$buyer.', trans_id='.$row["trans_id"]; ?>"><? echo $buyer_arr[$buyer];?></td>
                            <td width="80" align="center"><? echo $unit_of_measurement[$uom];?></td>
                            <td width="120" align="right"><? echo number_format($opening_stock,2); ?></td>
                            <td width="80" align="right"><? echo number_format($row["receive"],2);?></td>
                            <td width="80" align="right"><? echo number_format($row["issue_return"],2);?></td>
                            <td width="100" align="right"><? echo number_format($row["trans_in"],2);?></td>
                            <td width="100" align="right"><? echo number_format($row_total_rcv,2);?></td>
                            <td width="80" align="right"><? echo number_format($row["issue"],2);?></td>
                            <td width="80" align="right"><? echo number_format($row["rec_return"],2);?></td>
                            <td width="100" align="right"><? echo number_format($row["trans_out"],2);?></td>
                            <td width="100" align="right"><? echo number_format($row_total_iss,2);?></td>
                            <td width="100" align="right"><? echo number_format($closingStock,2);?></td>
                            <td>&nbsp;</td>
                        </tr>
                        <?
                        $sub_total_opening_stock += $opening_stock;
                        $sub_total_receive += $row["receive"];
                        $sub_total_issue_return += $row["issue_return"];
                        $sub_total_trans_in += $row["trans_in"];
                        $sub_total_row_total_rcv += $row_total_rcv;
						
                        $sub_total_issue += $row["issue"];
						$sub_total_rec_return += $row["rec_return"];
                        $sub_total_trans_out += $row["trans_out"];
                        $sub_total_row_total_iss +=$row_total_iss;
                        $sub_total_closingStock += $closingStock;
                        $i++;
						
					}
					?>
						<tr style="background-color: #dcdcdc; font-weight: bold;">
							<td colspan="2" align="right">Sub Total</td>
							<td align="center"><? echo $unit_of_measurement[$uom];?></td>
							<td align="right"><? echo number_format($sub_total_opening_stock,2)?></td>
							<td align="right"><? echo number_format($sub_total_receive,2)?></td>
							<td align="right"><? echo number_format($sub_total_issue_return,2)?></td>
							<td align="right"><? echo number_format($sub_total_trans_in,2)?></td>
							<td align="right"><? echo number_format($sub_total_row_total_rcv,2)?></td>
							<td align="right"><? echo number_format($sub_total_issue,2)?></td>
							<td align="right"><? echo number_format($sub_total_rec_return,2)?></td>
							<td align="right"><? echo number_format($sub_total_trans_out,2)?></td>
							<td align="right"><? echo number_format($sub_total_row_total_iss,2)?></td>
							<td align="right"><? echo number_format($sub_total_closingStock,2)?></td>
							<td>&nbsp;</td>
						</tr>
					<?
				}

				?>
			</table>
			</div>
			
			
			<table width="1320" border="1" cellpadding="2" cellspacing="0" class="" id="table_header_1" > 
				<thead>
					<tr class="form_caption" style="border:none;">
						<td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" >Buyer Wise Finish Fabric Closing Summary Report</td> 
					</tr>
					<tr style="border:none;">
						<td colspan="14" align="center" style="border:none; font-size:14px;">
						   <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>                               
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="14" align="center" style="border:none; font-size:14px;"><b>Purchase Report</b>                               
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="14" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
						</td>
					</tr>
				</thead>
			</table>
			<table width="1320" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
				<thead>
					<tr>
						<th rowspan="2" width="40">SL</th>
						<th rowspan="2" width="120">Buyer Name</th>
						<th rowspan="2" width="80">UOM</th>
						<th rowspan="2" width="120">Opening Stock</th>
						<th colspan="4">Receive</th>
						<th colspan="4">Issue</th>
						<th rowspan="2" width="100">Closing Stock</th>
						<th rowspan="2">Remarks</th>
					</tr> 
					<tr> 
						<th width="80">Receive</th>
						<th width="80">Issue Return</th> 
						<th width="100">Transfer In</th>
						<th width="100">Total Received</th>
						<th width="80">Issue</th>
						<th width="80">Receive Return</th>
						<th width="100">Transfer Out</th> 
						<th width="100">Total Issue</th>
					</tr> 
				</thead>
			</table>
			<div style="width:1320px; max-height:300px; overflow-y:scroll;" id="scroll_body"> 
			<table width="1300" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
				<?	
				$checkUOMArr= array();
				$k=1;
				foreach ($purchase_data as $uom => $uomData) 
				{
					$sub_total_opening_stock=0;$row_total_rcv=0;$sub_total_receive=0;$sub_total_rec_return=0;$sub_total_trans_in=0;$sub_total_row_total_rcv=0;$row_total_iss=0;$sub_total_issue=0;$sub_total_issue_return=0;$sub_total_trans_out=0;$sub_total_row_total_iss=0;$closingStock=0;$sub_total_closingStock = 0;
					foreach ($uomData as $buyer => $row) 
					{
						$opening_stock =0;
						if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
						$opening_stock =  $row["rcv_total_opening"]- $row["iss_total_opening"];
						$row_total_rcv = $row["receive"]+$row["issue_return"]+$row["trans_in"];
						$row_total_iss = $row["issue"]+$row["rec_return"]+$row["trans_out"];
						$closingStock = ($row["rcv_total_opening"]+$row["receive"]+$row["issue_return"]+$row["trans_in"]) - ($row["iss_total_opening"]+$row["issue"]+$row["rec_return"]+$row["trans_out"]);
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="40" align="center"><? echo $k;?></td>
                            <td width="120" align="center" title="<? echo $buyer; ?>"><? echo $buyer_arr[$buyer];?></td>
                            <td width="80" align="center"><? echo $unit_of_measurement[$uom];?></td>
                            <td width="120" align="right"><? echo number_format($opening_stock,2); ?></td>
                            <td width="80" align="right"><? echo number_format($row["receive"],2);?></td>
                            <td width="80" align="right"><? echo number_format($row["issue_return"],2);?></td>
                            <td width="100" align="right"><? echo number_format($row["trans_in"],2);?></td>
                            <td width="100" align="right"><? echo number_format($row_total_rcv,2);?></td>
                            <td width="80" align="right"><? echo number_format($row["issue"],2);?></td>
                            <td width="80" align="right"><? echo number_format($row["rec_return"],2);?></td>
                            <td width="100" align="right"><? echo number_format($row["trans_out"],2);?></td>
                            <td width="100" align="right"><? echo number_format($row_total_iss,2);?></td>
                            <td width="100" align="right"><? echo number_format($closingStock,2);?></td>
                            <td>&nbsp;</td>
                        </tr>
                        <?
                        $sub_total_opening_stock += $opening_stock;
                        $sub_total_receive += $row["receive"];
                        $sub_total_rec_return += $row["rec_return"];
                        $sub_total_trans_in += $row["trans_in"];
                        $sub_total_row_total_rcv += $row_total_rcv;
                        $sub_total_issue += $row["issue"];
                        $sub_total_issue_return += $row["issue_return"];
                        $sub_total_trans_out += $row["trans_out"];
                        $sub_total_row_total_iss +=$row_total_iss;
                        $sub_total_closingStock += $closingStock;
                        $i++;$k++;
					}
					
					?>
                    <tr style="background-color: #dcdcdc; font-weight: bold;">
                        <td colspan="2" align="right">Sub Total</td>
                        <td align="center"><? echo $unit_of_measurement[$uom];?></td>
                        <td align="right"><? echo number_format($sub_total_opening_stock,2)?></td>
                        <td align="right"><? echo number_format($sub_total_receive,2)?></td>
                        <td align="right"><? echo number_format($sub_total_issue_return,2)?></td>
                        <td align="right"><? echo number_format($sub_total_trans_in,2)?></td>
                        <td align="right"><? echo number_format($sub_total_row_total_rcv,2)?></td>
                        <td align="right"><? echo number_format($sub_total_issue,2)?></td>
                        <td align="right"><? echo number_format($sub_total_rec_return,2)?></td>
                        <td align="right"><? echo number_format($sub_total_trans_out,2)?></td>
                        <td align="right"><? echo number_format($sub_total_row_total_iss,2)?></td>
                        <td align="right"><? echo number_format($sub_total_closingStock,2)?></td>
                        <td>&nbsp;</td>
                    </tr>
                    <?
				}
				?>
			</table>
			</div>
		</div>
		<?
	}
	if($report_type ==8)
	{
		if($cbo_store_name) $sql_cond .= " and a.store_id=$cbo_store_name" ;
		$data_trns_array=array();
		if($cbo_source_type==1)
		{
			echo "<p style='text-align:center; color:red;'>Data Not Found Production Fabric Source Wise</p>";die;
		}

		/*
		$trnasactionData=sql_select("Select a.prod_id,
			sum(case when a.transaction_type=1 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
			sum(case when a.transaction_type=2 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
			sum(case when a.transaction_type=3 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as rcv_return_opening,
			sum(case when a.transaction_type=4 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as iss_return_opening,
			sum(case when a.transaction_type=5 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as trans_in_opening,
			sum(case when a.transaction_type=6 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as trans_out_opening,
			sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$select_from_date."' then a.cons_amount else 0 end) as rcv_total_opening_amt,
			sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$select_from_date."' then a.cons_amount else 0 end) as iss_total_opening_amt,    
			sum(case when a.transaction_type=1 and a.transaction_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as receive,
			sum(case when a.transaction_type=2 and a.transaction_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as issue,
			sum(case when a.transaction_type=3 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as rec_return,
			sum(case when a.transaction_type=4 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as issue_return,
			sum(case when a.transaction_type=5 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as transfer_in,
			sum(case when a.transaction_type=6 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as transfer_out,
			sum(case when a.transaction_type in(1,4,5) and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_amount else 0 end) as rcv_total_value,
			sum(case when a.transaction_type in(2,3,6) and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_amount else 0 end) as iss_total_value,
			sum(case when a.transaction_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_rate else 0 end) as rate
			from inv_transaction a, product_details_master b
			where a.prod_id = b.id and a.status_active=1 and a.is_deleted=0 $sql_cond group by a.prod_id order by a.prod_id ASC");*/
			$sql_all_cond="";
			if ($cbo_company_name!=0) $sql_all_cond =" and a.company_id='$cbo_company_name'";
			if ($cbo_item_category_id!=0) $sql_all_cond.=" and a.item_category='$cbo_item_category_id'";
			if ($txt_product_id_des!="") $sql_all_cond.=" and b.id in ($txt_product_id_des)";
			if ($txt_product_id!="") $sql_all_cond.=" and b.id in ($txt_product_id)";
			if ($cbo_store_name!=0) $sql_all_cond.=" and a.store_id='$cbo_store_name'";

		/*$trnasactionData=sql_select("Select a.item_category, a.prod_id,a.batch_id,a.pi_wo_batch_no, c.batch_no, d.batch_no as batch_no_knit, a.transaction_type,a.weight_type,b.detarmination_id, b.gsm, b.dia_width,b.weight, b.color, 
			(case when a.transaction_type=1 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
			(case when a.transaction_type=2 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
			(case when a.transaction_type=3 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as rcv_return_opening,
			(case when a.transaction_type=4 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as iss_return_opening,
			(case when a.transaction_type=5 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as trans_in_opening,
			(case when a.transaction_type=6 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as trans_out_opening,
			(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$select_from_date."' then a.cons_amount else 0 end) as rcv_total_opening_amt,
			(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$select_from_date."' then a.cons_amount else 0 end) as iss_total_opening_amt,    
			(case when a.transaction_type=1 and a.transaction_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as receive,
			(case when a.transaction_type=2 and a.transaction_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as issue,
			(case when a.transaction_type=3 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as rec_return,
			(case when a.transaction_type=4 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as issue_return,
			(case when a.transaction_type=5 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as transfer_in,
			(case when a.transaction_type=6 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as transfer_out,
			(case when a.transaction_type in(1,4,5) and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_amount else 0 end) as rcv_total_value,
			(case when a.transaction_type in(2,3,6) and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_amount else 0 end) as iss_total_value,
			(case when a.transaction_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_rate else 0 end) as rate
			from inv_transaction a
			left join pro_batch_create_mst c on a.batch_id=c.id and a.item_category=3 and (a.transaction_type=1 or a.transaction_type=2)
			left join pro_batch_create_mst d on a.pi_wo_batch_no=d.id and ( (a.item_category =3 and a.transaction_type in (3,4,5,6)) or (a.item_category =2)), 
			product_details_master b
			where a.prod_id = b.id and a.status_active=1 and a.is_deleted=0 $sql_cond $sql_all_cond  order by a.prod_id ASC");*/
			//and b.id in(16068,16069) and a.batch_id in(5063,5064)


			//Note consider INSER_DATE replace of TRANSACTION_DATE because some transaction date mismatch for previous data.

			$trnasactionData=sql_select("Select a.item_category, a.prod_id,a.batch_id,a.pi_wo_batch_no, c.batch_no, d.batch_no as batch_no_knit, a.transaction_type,a.weight_type,b.detarmination_id, b.gsm, b.dia_width,b.weight, b.color, 
			(case when a.transaction_type=1 and a.insert_date<'".$select_from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
			(case when a.transaction_type=2 and a.insert_date<'".$select_from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
			(case when a.transaction_type=3 and a.insert_date<'".$select_from_date."' then a.cons_quantity else 0 end) as rcv_return_opening,
			(case when a.transaction_type=4 and a.insert_date<'".$select_from_date."' then a.cons_quantity else 0 end) as iss_return_opening,
			(case when a.transaction_type=5 and a.insert_date<'".$select_from_date."' then a.cons_quantity else 0 end) as trans_in_opening,
			(case when a.transaction_type=6 and a.insert_date<'".$select_from_date."' then a.cons_quantity else 0 end) as trans_out_opening,
			(case when a.transaction_type in(1,4,5) and a.insert_date<'".$select_from_date."' then a.cons_amount else 0 end) as rcv_total_opening_amt,
			(case when a.transaction_type in(2,3,6) and a.insert_date<'".$select_from_date."' then a.cons_amount else 0 end) as iss_total_opening_amt,    
			(case when a.transaction_type=1 and a.insert_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as receive,
			(case when a.transaction_type=2 and a.insert_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as issue,
			(case when a.transaction_type=3 and a.insert_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as rec_return,
			(case when a.transaction_type=4 and a.insert_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as issue_return,
			(case when a.transaction_type=5 and a.insert_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as transfer_in,
			(case when a.transaction_type=6 and a.insert_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as transfer_out,
			(case when a.transaction_type in(1,4,5) and a.insert_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_amount else 0 end) as rcv_total_value,
			(case when a.transaction_type in(2,3,6) and a.insert_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_amount else 0 end) as iss_total_value,
			(case when a.insert_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_rate else 0 end) as rate
			from inv_transaction a
			left join pro_batch_create_mst c on a.batch_id=c.id and a.item_category=3 and (a.transaction_type=1 or a.transaction_type=2)
			left join pro_batch_create_mst d on a.pi_wo_batch_no=d.id and ( (a.item_category =3 and a.transaction_type in (3,4,5,6)) or (a.item_category =2)), 
			product_details_master b
			where a.prod_id = b.id and a.status_active=1 and a.is_deleted=0 $sql_cond $sql_all_cond  order by a.prod_id ASC");
			
			
		foreach($trnasactionData as $row)
		{
			if(($row[csf("item_category")]==3) && ($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==2))
			{
				$recv_issue_batchId=$row[csf("batch_id")];
				$batch_no=$row[csf("batch_no")];
			}
			else
			{
				$recv_issue_batchId=$row[csf("pi_wo_batch_no")];
				$batch_no=$row[csf("batch_no_knit")];
			}
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['rcv_total_opening']+=$row[csf("rcv_total_opening")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['iss_total_opening']+=$row[csf("iss_total_opening")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['rcv_return_opening']+=$row[csf("rcv_return_opening")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['iss_return_opening']+=$row[csf("iss_return_opening")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['trans_in_opening']+=$row[csf("trans_in_opening")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['trans_out_opening']+=$row[csf("trans_out_opening")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['receive']+=$row[csf("receive")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['issue_return']+=$row[csf("issue_return")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['transfer_in']+=$row[csf("transfer_in")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['issue']+=$row[csf("issue")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['rec_return']+=$row[csf("rec_return")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['transfer_out']+=$row[csf("transfer_out")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['avg_rate']+=$row[csf("rate")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['rcv_total_opening_amt']+=$row[csf("rcv_total_opening_amt")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['iss_total_opening_amt']+=$row[csf("iss_total_opening_amt")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['rcv_total_value']+=$row[csf("rcv_total_value")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['iss_total_value']+=$row[csf("iss_total_value")];
			

			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['detarmination_id']=$row[csf("detarmination_id")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['gsm']=$row[csf("gsm")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['dia_width']=$row[csf("dia_width")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['weight']=$row[csf("weight")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['color']=$row[csf("color")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['weight_type']=$row[csf("weight_type")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['batch_no']=$batch_no;


			$all_prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
			$all_batch_id_arr[$recv_issue_batchId] = $recv_issue_batchId;
					
		}

		/*echo "<pre>";
		print_r($data_trns_array);
		die;*/

		/* $all_batch_id_arr = array_filter($all_batch_id_arr);
		if(!empty($all_batch_id_arr))
		{
			$all_batch_ids = implode(",", $all_batch_id_arr);
			$all_batch_id_cond=""; $batchCond=""; 
			if($db_type==2 && count($all_batch_id_arr)>999)
			{
				$all_batch_id_arr_chunk=array_chunk($all_batch_id_arr,999) ;
				foreach($all_batch_id_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);	
					$batchCond.="  a.id in($chunk_arr_value) or ";
				}
				$all_batch_id_cond.=" and (".chop($batchCond,'or ').")";
			}
			else
			{
				$all_batch_id_cond=" and a.id in($all_batch_ids)";
			}
			$sql_batch=sql_select("select a.id,a.batch_no from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $all_batch_id_cond"); 

			foreach($sql_batch as $row)
			{
				$batch_info_arr[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
			}
		} */

		$i=1;
		ob_start();	
		if($report_type ==8)
		{ 
			?>
			<style type="text/css">
				.wrap_break_word {
					word-break: break-all;
					word-wrap: break-word;
				}
			</style>
			<div> 
				<table style="width:2240px" border="1" cellpadding="2" cellspacing="0" class="" id="table_header_1" > 
					<thead>
						<tr class="form_caption" style="border:none;">
							<td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold" > <? echo $report_title; ?></td> 
						</tr>
						<tr style="border:none;">
							<td colspan="16" align="center" style="border:none; font-size:14px;">
							   <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>                               
							</td>
						</tr>
						<tr style="border:none;">
							<td colspan="16" align="center" style="border:none;font-size:12px; font-weight:bold">
								<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
							</td>
						</tr>
					</thead>
				</table>
				<table style="width:2200px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" > 
					<thead>
						<tr>
							<th rowspan="2" width="40">SL</th>
							<th colspan="9">Description</th>
							<th rowspan="2" width="110">Opening Stock</th>
							<th colspan="4">Receive</th>
							<th colspan="4">Issue</th>
							<th rowspan="2" width="100">Closing Stock</th>
							<th rowspan="2" width="100">DOH</th>
						</tr> 
						<tr> 
							<th width="60">Prod. ID</th>                    
							<th width="150">Construction</th>
							<th width="250">Composition</th>

							<th width="100">Type</th>
							<th width="150">Design</th>
							<th width="70">Weight</th>
							<th width="70">Full Width</th>
							<th width="100">Batch/Lot</th>

							<th width="100">Color</th>
							<th width="100">Purchase</th>
							<th width="100">Issue Return</th> 
							<th width="100">Trans In</th> 
							<th width="100">Total Received</th>
							<th width="100">Issue</th>
							<th width="100">Receive Return</th>
							<th width="100">Trans Out</th>
							<th width="100">Total Issue</th> 
						</tr> 
					</thead>
				</table>
				<div style="width:2220px; max-height:280px; overflow-y:scroll; float:left;" id="scroll_body" > 
				<table style="width:2200px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left">
				<?
					$composition_arr=array();$composition_info_arr=array();
					$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent,a.type,a.design from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
					$data_array=sql_select($sql_deter);
					if(count($data_array)>0)
					{
						foreach( $data_array as $row )
						{
							if(array_key_exists($row[csf('id')],$composition_arr))
							{
								$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
								$composition_info_arr[$row[csf('id')]]["type"]=$row[csf('type')];
								$composition_info_arr[$row[csf('id')]]["design"]=$row[csf('design')];
							}
							else
							{
								$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";

								$composition_info_arr[$row[csf('id')]]["type"]=$row[csf('type')];
								$composition_info_arr[$row[csf('id')]]["design"]=$row[csf('design')];
							}
						}
					}
				
					$tot_opening_bal=0;$tot_purchuse=0;$tot_issue_return=0;$tot_receive=0; $tot_issue=0;$tot_receive_return=0;$tot_total_issue=0;$total_closing_stock=0;$tot_closing_stock=0;

 					if(!empty($all_prod_id_arr))
				   	{
						foreach ($data_trns_array as $prod_ids => $prod_data) 
						{
							foreach ($prod_data as $batch_ids => $row) 
							{
								if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
								
								$openingBalance =($row['rcv_total_opening']+$row['iss_return_opening'] + $row['trans_in_opening'])-($row['iss_total_opening']+$row['rcv_return_opening'] + $row['trans_out_opening']);
								
								$purchase = $row['receive'];
								$issue_return=$row['issue_return'];
								$transfer_in=$row['transfer_in'];
								$totalReceive=$purchase+$issue_return + $transfer_in;//
								$issue=$row['issue'];
								$rec_return=$row['rec_return'];
								$transfer_out=$row['transfer_out'];
								$totalIssue=$issue+$rec_return+$transfer_out;//
								
								$closingStock=$openingBalance+$totalReceive-$totalIssue;

								//if($closingStock >= $cbo_value_with){
								if(($closingStock > 0 && $cbo_value_with==1) || ($closingStock >=0 && $cbo_value_with==0)){ 
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="40"><? echo $i; ?></td>
										<td width="60"><? echo $prod_ids; ?></td>								
										<td width="150" class="wrap_break_word"><? echo $determinaArr[$row['detarmination_id']]; ?></td>                                 
										<td width="250"><p class="wrap_break_word"><? echo $composition_arr[$row['detarmination_id']]; ?></p></td>
										
										<td width="100" align="center"><p><? echo $composition_info_arr[$row['detarmination_id']]["type"]; ?></p></td> 
										<td width="150" align="center"><p><? echo $composition_info_arr[$row['detarmination_id']]["design"]; ?></p></td> 
										<td width="70" align="center"><p><? echo $row["weight"]; ?></p></td> 
										<td width="70" align="center"><p><? echo $row["dia_width"]; ?></p></td> 
										<td width="100" align="center"><p><? echo  $row['batch_no'];//$batch_info_arr[$batch_ids]["batch_no"]; ?></p></td> 
										
										<td width="100"><p class="wrap_break_word"><? echo $color_arr[$row["color"]]; ?></p></td> 
										
										<td width="110" align="right"><p><? echo number_format($openingBalance,2,'.','');$tot_opening_bal+=$openingBalance; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($purchase,2,'.',''); $tot_purchuse+=$purchase; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($issue_return,2,'.',''); $tot_issue_return+=$issue_return; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($transfer_in,2,'.',''); $tot_trans_in+=$transfer_in; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($totalReceive,2,'.',''); $tot_receive+=$totalReceive; ?></p></td>
										
										<td width="100" align="right"><p><? echo number_format($issue,2,'.',''); $tot_issue+=$issue; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($rec_return,2,'.',''); $tot_receive_return+=$rec_return; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($transfer_out,2,'.',''); $tot_trans_out+=$transfer_out; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($totalIssue,2,'.',''); $tot_total_issue+=$totalIssue; ?></p></td>
										<td width="100" align="right">
											<a href="##" onclick="stock_qnty_popup('<? echo $prod_ids;?>','<? echo $cbo_item_category_id;?>','<? echo $batch_ids;?>')">
												<p><? echo number_format($closingStock,2,'.',''); $total_closing_stock+=$closingStock; ?></p>
											</a>
										</td>
										<td width="100" align="right"><? echo $days_doh[$prod_ids]['daysonhand'];?></td>
									</tr>
								<? 												
								$i++;
								 }
							}
						}
					}
				?>
				</table>
			   </div>
				<table style="width:2200px" border="1" cellpadding="2" cellspacing="0" class="tbl_bottom" rules="all" id="">
					<tr>
						<td align="right" width="40">&nbsp;</td>
						<td align="right" width="60">&nbsp;</td>
						<td align="right" width="150">&nbsp;</td>
						<td align="right" width="250">&nbsp;</td>

						<td align="right" width="100">&nbsp;</td>
						<td align="right" width="150">&nbsp;</td>
						<td align="right" width="70">&nbsp;</td>
						<td align="right" width="70">&nbsp;</td>
						<td align="right" width="100">&nbsp;</td>

						<td align="right" width="100">Total</td>
						<td align="right" width="110" id="tot_opening_bal" ><? echo number_format($tot_opening_bal,2); ?></td>
						<td align="right" width="100" id="tot_purchuse" ><? echo number_format($tot_purchuse,2); ?></td>
						<td align="right" width="100" id="tot_issue_return" ><? echo number_format($tot_issue_return,2); ?></td>
						<td align="right" width="100" id="tot_trans_in" ><? echo number_format($tot_trans_in,2); ?></td>
						<td align="right" width="100" id="tot_receive" ><? echo number_format($tot_receive,2); ?></td>
						<td align="right" width="100" id="tot_issue" ><? echo number_format($tot_issue,2); ?></td>
						<td align="right" width="100" id="tot_receive_return" ><? echo number_format($tot_receive_return,2); ?></td>
						<td align="right" width="100" id="tot_trans_out" ><? echo number_format($tot_trans_out ,2); ?></td>
						<td align="right" width="100" id="tot_total_issue" ><? echo number_format($tot_total_issue,2); ?></td>
						<td align="right" width="100" id="tot_closing_stock" ><? echo number_format($total_closing_stock,2); ?></td>
						<td align="right" width="100">&nbsp;</td>  
					</tr>
				</table>
			</div>
			<?
		} 	
	}

	else if ($report_type == 9) // Total Value
	{
		if($cbo_item_category_id!=3)
		{
			echo "<span style='color:red;font-size:20px;'>This report only for Woven Fabric</span>";die;
		}
	
 		$pre_from_date = date('d-M-Y',strtotime('first day of last month',strtotime($from_date)));
        $pre_to_date = date('d-M-Y',strtotime('last day of previous month',strtotime($from_date)));

		if($db_type==0)
		{
			$pre_from_date=change_date_format($pre_from_date,'yyyy-mm-dd');
			$pre_to_date=change_date_format($pre_to_date,'yyyy-mm-dd');
		}
		else if($db_type==2) 
		{
			$pre_from_date=change_date_format($pre_from_date,'','',1);
			$pre_to_date=change_date_format($pre_to_date,'','',1);
		}
		else 
		{
			$pre_from_date="";
			$pre_to_date="";
		}

		ob_start();
		?>
		<div>
		<table style="width:1320px" border="1" cellpadding="2" cellspacing="0"  id="caption" rules="all"> 
			<thead>
				<tr class="form_caption" style="border:none;">
					<td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td> 
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="14" align="center" style="border:none;font-size:14px;">
						<b><? echo $companyArr[$cbo_company_name];?></b>
					</td>
				</tr>
				<!-- <tr class="form_caption" style="border:none;">
						<td colspan="14" align="center" style="border:none;font-size:12px; font-weight:bold">
							<p>Monthly Closing Value status</p>
						</td>
					</tr> -->
				<tr class="form_caption" style="border:none;">
					<td colspan="14" align="center" style="border:none;font-size:12px; font-weight:bold">
						<? if ($from_date != "" || $to_date != "") echo "From : " . change_date_format($from_date) . " To : " . change_date_format($to_date) . ""; ?>
					</td>
				</tr>
			</thead>
		</table>
		<table width="1340" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<tr>
					 <th rowspan="2" width="40">SL</th>
					 <th rowspan="2" width="100">Company Name</th>
					 <th rowspan="2" width="100">Opening Value TK</th>
					 <th colspan="4">Receive</th>
					 <th colspan="5">Issue</th>
					 <th rowspan="2" width="100">Closing Value TK</th>
					 <th rowspan="2" width="80">Previous Month Consumption value TK</th>
				</tr> 
				<tr>                         
					 <th width="100" >Purchase/Receive Value TK</th>
					 <th width="100">Transfer In Value TK</th>
					 <th width="80">Issue Return Value TK</th>
					 <th width="100">Total Rcv Value TK</th>
					 <th width="80">Consumption Value TK</th>
					 <th width="100">Transfer Out Value TK</th>
					 <th width="80">Rcv Return Value TK</th>
					 <th width="100">Other Issue Value TK</th>
					 <th width="100">Total Issue Value TK</th>
				</tr> 
			 </thead>
		</table>
		<div style="width:1380px; max-height:350px;overflow-y:scroll" id="scroll_body" > 
        <table align="center" style="margin-left: 20px;" width="1340" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
              <? 
            if ($txt_product_id_des==0 || $txt_product_id_des=="") 
            {
                $item_description=""; 
                $item_description_issue=""; 
            }
            else 
            {
                $item_description=" and a.prod_id in ($txt_product_id_des)";
                $item_description_issue=" and d.prod_id in ($txt_product_id_des)";
            }
            $from_date=$select_from_date;
			$to_date=$select_from_to;
           
            $store_cond_issue="";
            if($cbo_store_name>0)  $store_cond_issue=" and d.store_id=$cbo_store_name";
            if ($cbo_company_name>=0) $company_id_issue =" and d.company_id='$cbo_company_name'";                    
            $companyArr = return_library_array("select id,company_name from lib_company","id","company_name"); 
            $issue_sql= "select d.cons_amount as iss_amount,e.company_id, d.id as trans_id, c.entry_form, c.issue_purpose
            from inv_issue_master c, inv_transaction d,  product_details_master e
            where c.id = d.mst_id and d.prod_id = e.id and d.transaction_date between '".$from_date."' and '".$to_date."' $item_description_issue $store_cond_issue and c.entry_form=19 and d.transaction_type = 2 and d.item_category = 3 and e.item_category_id = 3 and c.status_active = 1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0 $company_id_issue order by e.company_id";
            //and c.issue_purpose in (4,8,36)
            //echo $issue_sql."<br>";
            $iss_result=  sql_select($issue_sql);
            $iss_array= array();
            $trans_check = array();
            foreach($iss_result as $iss_row)
            {
                if(empty($trans_check[$iss_row[csf("trans_id")]]))
                {
                    $trans_check[$iss_row[csf("trans_id")]] = $iss_row[csf("trans_id")];

                    if($iss_row[csf("entry_form")] == 19)
                    {
                        //if($iss_row[csf("issue_purpose")] == 4 || $iss_row[csf("issue_purpose")] == 8 || $iss_row[csf("issue_purpose")] == 36 || $iss_row[csf("issue_purpose")] == 42)
                        //{
                            $iss_array[$iss_row[csf("company_id")]] += $iss_row[csf("iss_amount")];
                            //$issue_test_data[$iss_row[csf("company_id")]][1]+= $iss_row[csf("iss_amount")];
                        //}
                        //$issue_test_data[$iss_row[csf("company_id")]][4]+= $iss_row[csf("iss_amount")];
                    }
                    
                    //$issue_test_data[$iss_row[csf("company_id")]][3]+= $iss_row[csf("iss_amount")];
                }
            }
            unset($iss_result);
            //echo "<pre>";print_r($issue_test_data);
            //echo $iss_array[1]."<br>";
                       //Select b.id, b.item_description, b.current_stock, b.avg_rate_per_unit, a.store_id,a.company_id,                         
            $sql="Select a.prod_id as id,a.company_id, b.unit_of_measure as cons_uom,a.transaction_date, 
            sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
            sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
            sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as rcv_total_opening_amt,
            sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as iss_total_opening_amt,
            sum(case when a.transaction_type in(1,4,5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as purchase_quantity,
            sum(case when a.transaction_type in(1,4,5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as purchase_amount, 
            sum(case when a.transaction_type in(2,3,6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue_quantity, 
            sum(case when a.transaction_type in(2,3,6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as issue_amount, 
            sum(case when a.transaction_type in(1) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as receive,
            sum(case when a.transaction_type in(4) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as issue_return,
            sum(case when a.transaction_type in(2) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as issue,
            sum(case when a.transaction_type in(3) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as receive_return,
            sum(case when a.transaction_type in(5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as receive_transfer,
            sum(case when a.transaction_type in(6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as issue_transfer,
            sum(case when a.transaction_type in(2,3,6) and a.transaction_date between '".$pre_from_date."' and '".$pre_to_date."' then a.cons_amount else 0 end) as pre_month_issue
            from inv_transaction a, product_details_master b
            where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id=3 and a.item_category=3 and a.company_id=$cbo_company_name $item_description  $store_cond  $sql_cond2
            group by a.prod_id,a.company_id, b.unit_of_measure,a.transaction_date order by a.prod_id ASC";
            // and b.entry_form in (17) 

            //echo $sql;
            $result=  sql_select($sql);
            $i=0;$count=1;//$totOpeningValue =0;
            foreach($result as $row)
            { 
                if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; $count++;
                $ageOfDays = datediff("d",$date_array[$row[csf("id")]]['min_date'],date("Y-m-d"));
                $daysOnHand = datediff("d",$date_array[$row[csf("id")]]['max_date'],date("Y-m-d")); 
                
                $opening_bal=$row[csf("rcv_total_opening")]-$row[csf("iss_total_opening")]; 
                $openingAmount =$row[csf("rcv_total_opening_amt")]-$row[csf("iss_total_opening_amt")];  
                $totOpeningRate=0;
				if ($opening_bal>0) 
				{
					$totOpeningRate=$openingAmount/$opening_bal;
				}                 
                /*$openingRate=$openingBalanceValue=0;
                if($row[csf("rcv_total_opening")] > 0)
                {
                    $openingRate = $row[csf("rcv_total_opening_amt")] / $row[csf("rcv_total_opening")];
                }
                //$openingBalanceValue = $opening_bal*$openingRate;
                //$openingBalanceValue = $opening_bal*$row[csf("avg_rate_per_unit")];
                */
                $openingBalanceValue=0;
                $openingBalanceValue = $row[csf("rcv_total_opening_amt")]-$row[csf("iss_total_opening_amt")];
                /*
                $openingRate=$openingBalanceValue=0;
                $openingBalanceValue = $row[csf("rcv_total_opening_amt")]-$row[csf("iss_total_opening_amt")];
                if($opening_bal>0) 
                {
                $openingRate=$openingBalanceValue/$opening_bal;
                }
                */
                $totOpeningValue = $opening_bal * $totOpeningRate;
                
                $receive = $row[csf("receive")];
                $issue = $row[csf("issue")];
                $issue_return=$row[csf("issue_return")];
                $receive_return=$row[csf("receive_return")];
                $issue_transfer=$row[csf("issue_transfer")];
                $receive_transfer=$row[csf("receive_transfer")];
                
                $purchase_amount_value=$row[csf("purchase_amount")];
                $issue_amount_value=$row[csf("issue_amount")];
                
                
                $tot_receive=$receive+$issue_return+$receive_transfer;
                $tot_issue=$issue+$receive_return+$issue_transfer;
                
                $closingStock=$opening_bal+$tot_receive-$tot_issue;
                $closingRate=$amount=0;
                $amount= ($openingBalanceValue + $purchase_amount_value) - $issue_amount_value;
                if($closingStock>0)
                {
                    $closingRate = $amount/$closingStock;
                }
                
                $rept_data[$row[csf("company_id")]]['opening']+=$openingBalanceValue;
                //$rept_data[$row[csf("company_id")]]['opening']+=$totOpeningValue;
                $rept_data[$row[csf("company_id")]]['opening_qnty']+=$opening_bal;
                $rept_data[$row[csf("company_id")]]['company']=$row[csf("company_id")];
                $rept_data[$row[csf("company_id")]]['receive']+=$receive;
                $rept_data[$row[csf("company_id")]]['tot_receive_qnty']+=$row[csf("purchase_quantity")];
                $rept_data[$row[csf("company_id")]]['tot_issue_qnty']+=$row[csf("issue_quantity")];
                $rept_data[$row[csf("company_id")]]['iss_return']+=$issue_return;
                $rept_data[$row[csf("company_id")]]['trans_in']+=$receive_transfer;
                $rept_data[$row[csf("company_id")]]['total_rcv']+=$purchase_amount_value;
                $rept_data[$row[csf("company_id")]]['issue'] = $iss_array[$row[csf("company_id")]];//$issue;
                $rept_data[$row[csf("company_id")]]['issue_w_other']+= $row[csf("issue")];
                $rept_data[$row[csf("company_id")]]['rcv_return']+=$receive_return;
                $rept_data[$row[csf("company_id")]]['trans_out']+=$issue_transfer;
                $rept_data[$row[csf("company_id")]]['total_issue']+=$issue_amount_value;
                $rept_data[$row[csf("company_id")]]['closing_value']+= $amount;
                $rept_data[$row[csf("company_id")]]['closingStock']+= $closingStock;
                $rept_data[$row[csf("company_id")]]['pre_month_issue']+= $row[csf("pre_month_issue")];
                
                //|| (number_format($closingStock,2) > 0.00)
                //if(((($value_with ==1) && (number_format($stockInHand,2) > 0.00)) || ($value_with ==0)) && ((number_format($openingBalance,2) > 0.00) || (number_format($totalRcv,2) > 0.00) || (number_format($totalIssue,2) > 0.00)) )
                //if(($value_with ==1 && (number_format($openingBalance,2) > 0.00 || number_format($stockInHand,2) > 0.00 ) )    ||    ($value_with ==0 && (number_format($openingBalance,2) > 0.00 || number_format($stockInHand,2) > 0.00 || number_format($totalRcv,2) > 0.00 || number_format($totalIssue,2) > 0.00 )))
                //if(((($value_with ==1) && (number_format($closingStock,2) > 0.00))||($value_with ==0)) && ( (number_format($opening_bal,2) > 0.00) || (number_format($tot_receive,2) > 0.00) || (number_format($tot_issue,2) > 0.00) ) )
                /*if(($value_with ==1 && (number_format($opening_bal,2) > 0.00 || number_format($closingStock,2) > 0.00 ) )    ||    ($value_with ==0 && (number_format($opening_bal,2) > 0.00 || number_format($closingStock,2) > 0.00 || number_format($tot_receive,2) > 0.00 || number_format($tot_issue,2) > 0.00 ))) 
                {
                    if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $closingStock>$txt_qnty) || ($get_upto_qnty==2 && $closingStock<$txt_qnty) || ($get_upto_qnty==3 && $closingStock>=$txt_qnty) || ($get_upto_qnty==4 && $closingStock<=$txt_qnty) || ($get_upto_qnty==5 && $closingStock==$txt_qnty) || $get_upto_qnty==0))
                    {
                        //$amount=$closingStock*$row[csf("avg_rate_per_unit")];
                        
                        $rept_data[$row[csf("company_id")]]['opening']+=$openingBalanceValue;
                        
                        $rept_data[$row[csf("company_id")]]['opening_qnty']+=$opening_bal;
                        
                        $rept_data[$row[csf("company_id")]]['company']=$row[csf("company_id")];
                        $rept_data[$row[csf("company_id")]]['receive']+=$receive;
                        $rept_data[$row[csf("company_id")]]['iss_return']+=$issue_return;
                        $rept_data[$row[csf("company_id")]]['trans_in']+=$receive_transfer;
                        $rept_data[$row[csf("company_id")]]['total_rcv']+=$purchase_amount_value;
                        
                        $rept_data[$row[csf("company_id")]]['issue'] = $iss_array[$row[csf("company_id")]];//$issue;
                        $rept_data[$row[csf("company_id")]]['issue_w_other']+= $row[csf("issue")];
                        $rept_data[$row[csf("company_id")]]['rcv_return']+=$receive_return;
                        $rept_data[$row[csf("company_id")]]['trans_out']+=$issue_transfer;
                        $rept_data[$row[csf("company_id")]]['total_issue']+=$issue_amount_value;
                        
                        
                        $rept_data[$row[csf("company_id")]]['closing_value']+= $amount;
                        //$rept_data[$row[csf("company_id")]]['pre_month_issue']+= $row[csf("pre_month_issue")];
                    
                    }
                }*/

            }
            //echo $totOpeningValue;
            //echo $rept_data[1]['issue_w_other'];die;
            unset($result);
        
             $sl = 1;
            foreach ($rept_data as $value)
            {
             	if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
             
                //if(($value_with ==1 && (number_format($value["opening_qnty"],2) > 0.00 || number_format($value["closingStock"],2) > 0.00 ) )    ||    ($value_with ==0 && (number_format($value["opening_qnty"],2) > 0.00 || number_format($value["closingStock"],2) > 0.00 || number_format($value["tot_receive_qnty"],2) > 0.00 || number_format($value["tot_issue_qnty"],2) > 0.00 )))
                //if(($value_with ==1 && ($value["opening_qnty"] != 0 || $value["closingStock"] != 0) ) || ($value_with ==0 && ($value["opening_qnty"] != 0 || $value["closingStock"] != 0 || $value["tot_receive_qnty"] != 0 || $value["tot_issue_qnty"] != 0)))
                if(($value_with ==1 && ($value["opening_qnty"] != 0 || $value["opening"] !=0  || $value["closingStock"] != 0 || $value['closing_value'] !=0)) || ($value_with ==0 || ($value["opening_qnty"] != 0  || $value["opening"] !=0 || $value["closingStock"] != 0 || $value['closing_value'] !=0 || $value["tot_receive_qnty"] != 0 || $value['total_rcv'] !=0 || $value["tot_issue_qnty"] != 0  || $value['total_issue'] !=0)))
                {
                    
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
                        <td width="40"><? echo $sl;?></td>
                        <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: center; "><? echo $companyArr[$value['company']];?></td>
                        <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value["opening"],2);?></p></td>
                        <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value["receive"],2);?></p></td>
                        <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value["trans_in"],2);?></p></td>
                        <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value["iss_return"],2);?></p></td>
                        <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value['total_rcv'],2);?></p></td>
                        <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value['issue'],2);?></p></td>
                        <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value["trans_out"],2);?></p></td>
                        <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value["rcv_return"],2);?></p></td>
                        <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format(($value['issue_w_other']-$value['issue']),2);?></p></td>
                        <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value["total_issue"],2);?> </p></td>
                        <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value['closing_value'],2);?></p></td>
                        <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value['pre_month_issue'],2);?></p></td>
                    </tr>
                    
                    <?
                    $grand_opening += $value["opening"];
                    $grand_opening_qnty += $value["opening_qnty"];
                    
                    $grand_rcv += $value["receive"];
                    $grand_trans_in += $value["trans_in"];
                    $grand_iss_return += $value["iss_return"];
                    $grand_rcv_total += $value['total_rcv'];
                    $grand_issue += $value['issue'];
                    $grand_trans_out += $value["trans_out"];
                    $grand_rcv_return += $value["rcv_return"];
                    $grand_other_issue += $value['issue_w_other']-$value['issue'];
                    $grand_issue_total += $value["total_issue"];
                    $grand_closing_value += $value['closing_value'];
                    $grand_pre_issue += $value['pre_month_issue'];
                    $sl++;
                 
                }	
            }
                                                                         
              ?>
          </table>
          </div>       
        <table width="1340" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" > 
            <tr>
                <td style="word-break: break-all; word-wrap:break-word;width: 40px;text-align: right;"><p>&nbsp;</p></td>
                <td style="word-break: break-all; word-wrap:break-word;width: 100px;text-align: right;" title="<? echo $count;?>"><p>Grand Total=</p></td>
                <td id="tot_grand_opening" width="100" title="<? echo $grand_opening_qnty; ?>" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_opening,2);?></p></td>
                <td id="tot_grand_recv" width="100" title="rcv" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_rcv,2);?></p></td>
                <td id="tot_grand_trans_in" width="100" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_trans_in,2);?></p></td>
                <td id="tot_grand_iss_return" width="80" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_iss_return,2);?></p></td>
                <td id="tot_grand_recv_total" width="100" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_rcv_total,2);?></p></td>
                 <td id="tot_grand_issue" width="80" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_issue,2);?></p></td>
                 <td id="tot_grand_trans_out" width="100" title="tr_out" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_trans_out,2);?></p></td>
                 <td id="tot_grand_recv_return" width="80" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_rcv_return,2);?></p></td>
                 <td id="tot_grand_other_issue" width="100" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_other_issue,2)?></p></td>
                 <td id="tot_grand_issue_total" width="100" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_issue_total,2);?></p></td>
                 <td id="tot_grand_closing_value" width="100" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_closing_value,2);?></p></td>
                 <td id="tot_grand_pre_issue" width="80" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_pre_issue,2);?></p></td>
            </tr> 
        </table>
        </div>
		<?  
    }
    if($report_type ==10)
	{
		if($cbo_store_name) $sql_cond .= " and a.store_id=$cbo_store_name" ;
		$data_trns_array=array();
		if($cbo_source_type==1)
		{
			echo "<p style='text-align:center; color:red;'>Data Not Found Production Fabric Source Wise</p>";die;
		}

			$sql_all_cond="";
			if ($cbo_company_name!=0) $sql_all_cond =" and a.company_id='$cbo_company_name'";
			if ($cbo_item_category_id!=0) $sql_all_cond.=" and a.item_category='$cbo_item_category_id'";
			if ($txt_product_id_des!="") $sql_all_cond.=" and b.id in ($txt_product_id_des)";
			if ($txt_product_id!="") $sql_all_cond.=" and b.id in ($txt_product_id)";
			if ($cbo_store_name!=0) $sql_all_cond.=" and a.store_id='$cbo_store_name'";

		$trnasactionData=sql_select("Select a.item_category, a.prod_id,a.batch_id,a.pi_wo_batch_no, c.batch_no, d.batch_no as batch_no_knit, a.transaction_type,a.weight_type,b.detarmination_id, b.gsm, b.dia_width,b.weight, b.color, 
			(case when a.transaction_type=1 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
			(case when a.transaction_type=2 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
			(case when a.transaction_type=3 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as rcv_return_opening,
			(case when a.transaction_type=4 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as iss_return_opening,
			(case when a.transaction_type=5 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as trans_in_opening,
			(case when a.transaction_type=6 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as trans_out_opening,
			(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$select_from_date."' then a.cons_amount else 0 end) as rcv_total_opening_amt,
			(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$select_from_date."' then a.cons_amount else 0 end) as iss_total_opening_amt,    
			(case when a.transaction_type=1 and a.transaction_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as receive,
			(case when a.transaction_type=2 and a.transaction_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as issue,
			(case when a.transaction_type=3 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as rec_return,
			(case when a.transaction_type=4 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as issue_return,
			(case when a.transaction_type=5 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as transfer_in,
			(case when a.transaction_type=6 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as transfer_out,
			(case when a.transaction_type in(1,4,5) and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_amount else 0 end) as rcv_total_value,
			(case when a.transaction_type in(2,3,6) and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_amount else 0 end) as iss_total_value,
			(case when a.transaction_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_rate else 0 end) as rate
			from inv_transaction a
			left join pro_batch_create_mst c on a.batch_id=c.id and a.item_category=3 and (a.transaction_type=1 or a.transaction_type=2)
			left join pro_batch_create_mst d on a.pi_wo_batch_no=d.id and ( (a.item_category =3 and a.transaction_type in (3,4,5,6)) or (a.item_category =2)), 
			product_details_master b
			where a.prod_id = b.id and a.status_active=1 and a.is_deleted=0 $sql_cond $sql_all_cond  order by a.prod_id ASC");
			//and b.id in(16068,16069) and a.batch_id in(5063,5064)
			
		foreach($trnasactionData as $row)
		{
			if(($row[csf("item_category")]==3) && ($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==2))
			{
				$recv_issue_batchId=$row[csf("batch_id")];
				$batch_no=$row[csf("batch_no")];
			}
			else
			{
				$recv_issue_batchId=$row[csf("pi_wo_batch_no")];
				$batch_no=$row[csf("batch_no_knit")];
			}
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['rcv_total_opening']+=$row[csf("rcv_total_opening")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['iss_total_opening']+=$row[csf("iss_total_opening")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['rcv_return_opening']+=$row[csf("rcv_return_opening")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['iss_return_opening']+=$row[csf("iss_return_opening")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['trans_in_opening']+=$row[csf("trans_in_opening")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['trans_out_opening']+=$row[csf("trans_out_opening")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['receive']+=$row[csf("receive")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['issue_return']+=$row[csf("issue_return")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['transfer_in']+=$row[csf("transfer_in")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['issue']+=$row[csf("issue")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['rec_return']+=$row[csf("rec_return")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['transfer_out']+=$row[csf("transfer_out")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['avg_rate']+=$row[csf("rate")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['rcv_total_opening_amt']+=$row[csf("rcv_total_opening_amt")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['iss_total_opening_amt']+=$row[csf("iss_total_opening_amt")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['rcv_total_value']+=$row[csf("rcv_total_value")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['iss_total_value']+=$row[csf("iss_total_value")];
			

			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['detarmination_id']=$row[csf("detarmination_id")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['gsm']=$row[csf("gsm")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['dia_width']=$row[csf("dia_width")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['weight']=$row[csf("weight")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['color']=$row[csf("color")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['weight_type']=$row[csf("weight_type")];
			$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['batch_no']=$batch_no;


			$all_prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
			$all_batch_id_arr[$recv_issue_batchId] = $recv_issue_batchId;
					
		}

		

		$i=1;
		ob_start();	
		if($report_type ==10)
		{ 
			?>
			<style type="text/css">
				.wrap_break_word {
					word-break: break-all;
					word-wrap: break-word;
				}
			</style>
			<div> 
				<table style="width:2440px" border="1" cellpadding="2" cellspacing="0" class="" id="table_header_1" > 
					<thead>
						<tr class="form_caption" style="border:none;">
							<td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold" > <? echo $report_title; ?></td> 
						</tr>
						<tr style="border:none;">
							<td colspan="16" align="center" style="border:none; font-size:14px;">
							   <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>                               
							</td>
						</tr>
						<tr style="border:none;">
							<td colspan="16" align="center" style="border:none;font-size:12px; font-weight:bold">
								<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
							</td>
						</tr>
					</thead>
				</table>
				<table style="width:2400px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" > 
					<thead>
						<tr>
							<th rowspan="2" width="40">SL</th>
							<th colspan="9">Description</th>
							<th rowspan="2" width="110">Opening Stock</th>
							<th colspan="4">Receive</th>
							<th colspan="4">Issue</th>
							<th rowspan="2" width="100">Closing Stock</th>
							<th rowspan="2" width="100">Avg Rate TK</th>
							<th rowspan="2" width="100">Amount</th>
							<th rowspan="2" width="100">DOH</th>
						</tr> 
						<tr> 
							<th width="60">Prod. ID</th>                    
							<th width="150">Construction</th>
							<th width="250">Composition</th>

							<th width="100">Type</th>
							<th width="150">Design</th>
							<th width="70">Weight</th>
							<th width="70">Full Width</th>
							<th width="100">Batch/Lot</th>

							<th width="100">Color</th>
							<th width="100">Purchase</th>
							<th width="100">Issue Return</th> 
							<th width="100">Trans In</th> 
							<th width="100">Total Received</th>
							<th width="100">Issue</th>
							<th width="100">Receive Return</th>
							<th width="100">Trans Out</th>
							<th width="100">Total Issue</th> 
						</tr> 
					</thead>
				</table>
				<div style="width:2420px; max-height:280px; overflow-y:scroll; float:left;" id="scroll_body" > 
				<table style="width:2400px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left">
				<?
					$composition_arr=array();$composition_info_arr=array();
					$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent,a.type,a.design from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
					$data_array=sql_select($sql_deter);
					if(count($data_array)>0)
					{
						foreach( $data_array as $row )
						{
							if(array_key_exists($row[csf('id')],$composition_arr))
							{
								$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
								$composition_info_arr[$row[csf('id')]]["type"]=$row[csf('type')];
								$composition_info_arr[$row[csf('id')]]["design"]=$row[csf('design')];
							}
							else
							{
								$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";

								$composition_info_arr[$row[csf('id')]]["type"]=$row[csf('type')];
								$composition_info_arr[$row[csf('id')]]["design"]=$row[csf('design')];
							}
						}
					}
				
					$tot_opening_bal=0;$tot_purchuse=0;$tot_issue_return=0;$tot_receive=0; $tot_issue=0;$tot_receive_return=0;$tot_total_issue=0;$total_closing_stock=0;$tot_closing_stock=0;

 					if(!empty($all_prod_id_arr))
				   	{
						foreach ($data_trns_array as $prod_ids => $prod_data) 
						{
							foreach ($prod_data as $batch_ids => $row) 
							{
								if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
								
								$openingBalance =($row['rcv_total_opening']+$row['iss_return_opening'] + $row['trans_in_opening'])-($row['iss_total_opening']+$row['rcv_return_opening'] + $row['trans_out_opening']);
								
								$purchase = $row['receive'];
								$issue_return=$row['issue_return'];
								$transfer_in=$row['transfer_in'];
								$totalReceive=$purchase+$issue_return + $transfer_in;//
								$issue=$row['issue'];
								$rec_return=$row['rec_return'];
								$transfer_out=$row['transfer_out'];
								$totalIssue=$issue+$rec_return+$transfer_out;//
								
								$closingStock=$openingBalance+$totalReceive-$totalIssue;

								$closingStockAmount=$row['rcv_total_opening_amt']-$row['iss_total_opening_amt'];

								$closingAvgRate=($closingStockAmount/$closingStock);

								//if($closingStock >= $cbo_value_with){
								if(($closingStock > 0 && $cbo_value_with==1) || ($closingStock >=0 && $cbo_value_with==0)){ 
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="40"><? echo $i; ?></td>
										<td width="60"><? echo $prod_ids; ?></td>								
										<td width="150" class="wrap_break_word"><? echo $determinaArr[$row['detarmination_id']]; ?></td>                                 
										<td width="250"><p class="wrap_break_word"><? echo $composition_arr[$row['detarmination_id']]; ?></p></td>
										
										<td width="100" align="center"><p><? echo $composition_info_arr[$row['detarmination_id']]["type"]; ?></p></td> 
										<td width="150" align="center"><p><? echo $composition_info_arr[$row['detarmination_id']]["design"]; ?></p></td> 
										<td width="70" align="center"><p><? echo $row["weight"]; ?></p></td> 
										<td width="70" align="center"><p><? echo $row["dia_width"]; ?></p></td> 
										<td width="100" align="center"><p><? echo  $row['batch_no'];//$batch_info_arr[$batch_ids]["batch_no"]; ?></p></td> 
										
										<td width="100"><p class="wrap_break_word"><? echo $color_arr[$row["color"]]; ?></p></td> 
										
										<td width="110" align="right"><p><? echo number_format($openingBalance,2,'.','');$tot_opening_bal+=$openingBalance; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($purchase,2,'.',''); $tot_purchuse+=$purchase; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($issue_return,2,'.',''); $tot_issue_return+=$issue_return; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($transfer_in,2,'.',''); $tot_trans_in+=$transfer_in; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($totalReceive,2,'.',''); $tot_receive+=$totalReceive; ?></p></td>
										
										<td width="100" align="right"><p><? echo number_format($issue,2,'.',''); $tot_issue+=$issue; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($rec_return,2,'.',''); $tot_receive_return+=$rec_return; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($transfer_out,2,'.',''); $tot_trans_out+=$transfer_out; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($totalIssue,2,'.',''); $tot_total_issue+=$totalIssue; ?></p></td>

										<td width="100" align="right">
											<a href="##" onclick="stock_qnty_popup('<? echo $prod_ids;?>','<? echo $cbo_item_category_id;?>','<? echo $batch_ids;?>')">
												<p><? echo number_format($closingStock,2,'.',''); $total_closing_stock+=$closingStock; ?></p>
											</a>
										</td>
										
										<td width="100" align="right"><p><? echo number_format($closingAvgRate,2,'.',''); ?></p></td>
										<td width="100" align="right"><p><? echo number_format($closingStockAmount,2,'.','');  ?></p></td>
										
										<td width="100" align="right"><? echo $days_doh[$prod_ids]['daysonhand'];?></td>
									</tr>
								<? 												
								$i++;
								 }
							}
						}
					}
				?>
				</table>
			   </div>
				<table style="width:2400px" border="1" cellpadding="2" cellspacing="0" class="tbl_bottom" rules="all" id="">
					<tr>
						<td align="right" width="40">&nbsp;</td>
						<td align="right" width="60">&nbsp;</td>
						<td align="right" width="150">&nbsp;</td>
						<td align="right" width="250">&nbsp;</td>

						<td align="right" width="100">&nbsp;</td>
						<td align="right" width="150">&nbsp;</td>
						<td align="right" width="70">&nbsp;</td>
						<td align="right" width="70">&nbsp;</td>
						<td align="right" width="100">&nbsp;</td>

						<td align="right" width="100">Total</td>
						<td align="right" width="110" id="tot_opening_bal" ><? echo number_format($tot_opening_bal,2); ?></td>
						<td align="right" width="100" id="tot_purchuse" ><? echo number_format($tot_purchuse,2); ?></td>
						<td align="right" width="100" id="tot_issue_return" ><? echo number_format($tot_issue_return,2); ?></td>
						<td align="right" width="100" id="tot_trans_in" ><? echo number_format($tot_trans_in,2); ?></td>
						<td align="right" width="100" id="tot_receive" ><? echo number_format($tot_receive,2); ?></td>
						<td align="right" width="100" id="tot_issue" ><? echo number_format($tot_issue,2); ?></td>
						<td align="right" width="100" id="tot_receive_return" ><? echo number_format($tot_receive_return,2); ?></td>
						<td align="right" width="100" id="tot_trans_out" ><? echo number_format($tot_trans_out ,2); ?></td>
						<td align="right" width="100" id="tot_total_issue" ><? echo number_format($tot_total_issue,2); ?></td>
						<td align="right" width="100" id="tot_closing_stock" ><? echo number_format($total_closing_stock,2); ?></td>
						<td align="right" width="100"><? ?></td>
						<td align="right" width="100"><? ?></td>
						<td align="right" width="100">&nbsp;</td>  
					</tr>
				</table>
			</div>
			<?
		} 	
	}
	
    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$report_type"; 
    exit();
}

if($action=="report_generate_exel_only")
{ 
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$txt_product_id_des=str_replace("'","",$txt_product_id_des);
	$txt_product_id=str_replace("'","",$txt_product_id);
    $report_type=str_replace("'","",$report_type);
	$cbo_source_type=str_replace("'","",$cbo_source_type);

	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
	$determinaArr = return_library_array("select id,construction from  lib_yarn_count_determina_mst where status_active=1 and is_deleted=0","id","construction");
	$color_arr=return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	
	if($db_type==0)
	{
		$select_from_date=change_date_format($from_date,'yyyy-mm-dd');
		$select_from_to=change_date_format($to_date,'yyyy-mm-dd');
	}
	else if($db_type==2) 
	{
		$select_from_date=change_date_format($from_date,'','',1);
		$select_from_to=change_date_format($to_date,'','',1);
	}
	else 
	{
		$select_from_date="";
		$select_from_to="";
	}
	$days_doh=array();
	if($db_type==2)
	{
		$returnRes="select prod_id, min(transaction_date) || ',' || max(transaction_date )  as trans_date from inv_transaction where transaction_type in (1,3) and  item_category in (2,3) and status_active=1 and is_deleted=0 group by prod_id ";
		//$returnRes_result= sql_select($returnRes);
	}
	else
	{
		$returnRes="select prod_id, concat(min(transaction_date),',',max(transaction_date))  as trans_date from inv_transaction where transaction_type in (1,3) and  item_category in (2,3) and status_active=1 and is_deleted=0 group by prod_id ";
	}
	//echo $returnRes;die;
	$returnRes_result= sql_select($returnRes);
	foreach($returnRes_result as $row_d)
	{
		$date_total=explode(",",$row_d[csf('trans_date')]);
		if($db_type==2)
		{
			$today= change_date_format(date("Y-m-d"),'','',1);	
			$daysOnHand = datediff("d",change_date_format($date_total[1],'','',1),$today);
		}
		else
		{
			$today= change_date_format(date("Y-m-d"));	
			$daysOnHand = datediff("d",change_date_format($date_total[1]),$today);
		}
		$days_doh[$row_d[csf('prod_id')]]['daysonhand']=$daysOnHand ;
	} 
	
	$sql_cond="";
	
	if ($cbo_company_name!=0) $sql_cond =" and a.company_id=$cbo_company_name";
	if ($cbo_item_category_id!=0) $sql_cond.=" and a.item_category=$cbo_item_category_id";
	if ($txt_product_id_des!="") $sql_cond.=" and a.prod_id in ($txt_product_id_des)";
	if ($txt_product_id!="") $sql_cond.=" and a.prod_id in ($txt_product_id)";
	if ($txt_product_id!="") $sql_cond2=" and a.prod_id in ($txt_product_id)";
	if ($cbo_uom!="") $sql_cond.=" and a.cons_uom in ($cbo_uom)";

	
	if($cbo_store_name) $sql_cond .= " and a.store_id=$cbo_store_name" ;
	$data_trns_array=array();
	if($cbo_source_type==1)
	{
		echo "<p style='text-align:center; color:red;'>Data Not Found Production Fabric Source Wise</p>";die;
	}

	
	$sql_all_cond="";
	if ($cbo_company_name!=0) $sql_all_cond =" and a.company_id='$cbo_company_name'";
	if ($cbo_item_category_id!=0) $sql_all_cond.=" and a.item_category='$cbo_item_category_id'";
	if ($txt_product_id_des!="") $sql_all_cond.=" and b.id in ($txt_product_id_des)";
	if ($txt_product_id!="") $sql_all_cond.=" and b.id in ($txt_product_id)";
	if ($cbo_store_name!=0) $sql_all_cond.=" and a.store_id='$cbo_store_name'";

	$trnasactionData=sql_select("Select a.item_category, a.prod_id,a.batch_id,a.pi_wo_batch_no, c.batch_no, d.batch_no as batch_no_knit, a.transaction_type,a.weight_type,b.detarmination_id, b.gsm, b.dia_width,b.weight, b.color, 
		(case when a.transaction_type=1 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
		(case when a.transaction_type=2 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
		(case when a.transaction_type=3 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as rcv_return_opening,
		(case when a.transaction_type=4 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as iss_return_opening,
		(case when a.transaction_type=5 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as trans_in_opening,
		(case when a.transaction_type=6 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as trans_out_opening,
		(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$select_from_date."' then a.cons_amount else 0 end) as rcv_total_opening_amt,
		(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$select_from_date."' then a.cons_amount else 0 end) as iss_total_opening_amt,    
		(case when a.transaction_type=1 and a.transaction_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as receive,
		(case when a.transaction_type=2 and a.transaction_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as issue,
		(case when a.transaction_type=3 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as rec_return,
		(case when a.transaction_type=4 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as issue_return,
		(case when a.transaction_type=5 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as transfer_in,
		(case when a.transaction_type=6 and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_quantity else 0 end) as transfer_out,
		(case when a.transaction_type in(1,4,5) and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_amount else 0 end) as rcv_total_value,
		(case when a.transaction_type in(2,3,6) and a.transaction_date  between '".$select_from_date."' and '".$select_from_to."' then a.cons_amount else 0 end) as iss_total_value,
		(case when a.transaction_date between '".$select_from_date."' and '".$select_from_to."' then a.cons_rate else 0 end) as rate
		from inv_transaction a
		left join pro_batch_create_mst c on a.batch_id=c.id and a.item_category=3 and (a.transaction_type=1 or a.transaction_type=2)
		left join pro_batch_create_mst d on a.pi_wo_batch_no=d.id and ( (a.item_category =3 and a.transaction_type in (3,4,5,6)) or (a.item_category =2)), 
		product_details_master b
		where a.prod_id = b.id and a.status_active=1 and a.is_deleted=0 $sql_cond $sql_all_cond  order by a.prod_id ASC");
		//and b.id in(16068,16069) and a.batch_id in(5063,5064)

		
	foreach($trnasactionData as $row)
	{
		if(($row[csf("item_category")]==3) && ($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==2))
		{
			$recv_issue_batchId=$row[csf("batch_id")];
			$batch_no=$row[csf("batch_no")];
		}
		else
		{
			$recv_issue_batchId=$row[csf("pi_wo_batch_no")];
			$batch_no=$row[csf("batch_no_knit")];
		}
		$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['rcv_total_opening']+=$row[csf("rcv_total_opening")];
		$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['iss_total_opening']+=$row[csf("iss_total_opening")];
		$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['rcv_return_opening']+=$row[csf("rcv_return_opening")];
		$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['iss_return_opening']+=$row[csf("iss_return_opening")];
		$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['trans_in_opening']+=$row[csf("trans_in_opening")];
		$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['trans_out_opening']+=$row[csf("trans_out_opening")];
		$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['receive']+=$row[csf("receive")];
		$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['issue_return']+=$row[csf("issue_return")];
		$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['transfer_in']+=$row[csf("transfer_in")];
		$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['issue']+=$row[csf("issue")];
		$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['rec_return']+=$row[csf("rec_return")];
		$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['transfer_out']+=$row[csf("transfer_out")];
		$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['avg_rate']+=$row[csf("rate")];
		$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['rcv_total_opening_amt']+=$row[csf("rcv_total_opening_amt")];
		$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['iss_total_opening_amt']+=$row[csf("iss_total_opening_amt")];
		$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['rcv_total_value']+=$row[csf("rcv_total_value")];
		$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['iss_total_value']+=$row[csf("iss_total_value")];
		

		$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['detarmination_id']=$row[csf("detarmination_id")];
		$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['gsm']=$row[csf("gsm")];
		$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['dia_width']=$row[csf("dia_width")];
		$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['weight']=$row[csf("weight")];
		$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['color']=$row[csf("color")];
		$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['weight_type']=$row[csf("weight_type")];
		$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['batch_no']=$batch_no;


		$all_prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
		$all_batch_id_arr[$recv_issue_batchId] = $recv_issue_batchId;
				
	}

	$i=1;
	//ob_start();	
	$html = "";

	/* <style type="text/css">
		.wrap_break_word {
			word-break: break-all;
			word-wrap: break-word;
		}
	</style> */

	$html .='<table style="width:2240px" border="1" cellpadding="2" cellspacing="0" class="" id="table_header_1" > 
		<thead>
			<tr class="form_caption" style="border:none;">
				<td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold" >'.$report_title .'</td> 
			</tr>
			<tr style="border:none;">
				<td colspan="16" align="center" style="border:none; font-size:14px;">
					<b>Company Name : '.$companyArr[$cbo_company_name] .'</b>                               
				</td>
			</tr>
			<tr style="border:none;">
				<td colspan="16" align="center" style="border:none;font-size:12px; font-weight:bold">';
					if($from_date!="" || $to_date!="") $html .= "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;
				$html .='</td>
			</tr>
		</thead>
	</table>
	<table style="width:2200px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" > 
		<thead>
			<tr>
				<th rowspan="2" width="40">SL</th>
				<th colspan="9">Description</th>
				<th rowspan="2" width="110">Opening Stock</th>
				<th colspan="4">Receive</th>
				<th colspan="4">Issue</th>
				<th rowspan="2" width="100">Closing Stock</th>
				<th rowspan="2" width="100">DOH</th>
			</tr> 
			<tr> 
				<th width="60">Prod. ID</th>                    
				<th width="150">Construction</th>
				<th width="250">Composition</th>

				<th width="100">Type</th>
				<th width="150">Design</th>
				<th width="70">Weight</th>
				<th width="70">Full Width</th>
				<th width="100">Batch/Lot</th>

				<th width="100">Color</th>
				<th width="100">Purchase</th>
				<th width="100">Issue Return</th> 
				<th width="100">Trans In</th> 
				<th width="100">Total Received</th>
				<th width="100">Issue</th>
				<th width="100">Receive Return</th>
				<th width="100">Trans Out</th>
				<th width="100">Total Issue</th> 
			</tr> 
		</thead>
	</table>

	<table style="width:2200px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left">';
	
		$composition_arr=array();$composition_info_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent,a.type,a.design from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
		$data_array=sql_select($sql_deter);
		if(count($data_array)>0)
		{
			foreach( $data_array as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					$composition_info_arr[$row[csf('id')]]["type"]=$row[csf('type')];
					$composition_info_arr[$row[csf('id')]]["design"]=$row[csf('design')];
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";

					$composition_info_arr[$row[csf('id')]]["type"]=$row[csf('type')];
					$composition_info_arr[$row[csf('id')]]["design"]=$row[csf('design')];
				}
			}
		}
	
		$tot_opening_bal=0;$tot_purchuse=0;$tot_issue_return=0;$tot_receive=0; $tot_issue=0;$tot_receive_return=0;$tot_total_issue=0;$total_closing_stock=0;$tot_closing_stock=0;

		if(!empty($all_prod_id_arr))
		{
			foreach ($data_trns_array as $prod_ids => $prod_data) 
			{
				foreach ($prod_data as $batch_ids => $row) 
				{
					if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
					
					$openingBalance =($row['rcv_total_opening']+$row['iss_return_opening'] + $row['trans_in_opening'])-($row['iss_total_opening']+$row['rcv_return_opening'] + $row['trans_out_opening']);
					
					$purchase = $row['receive'];
					$issue_return=$row['issue_return'];
					$transfer_in=$row['transfer_in'];
					$totalReceive=$purchase+$issue_return + $transfer_in;//
					$issue=$row['issue'];
					$rec_return=$row['rec_return'];
					$transfer_out=$row['transfer_out'];
					$totalIssue=$issue+$rec_return+$transfer_out;//
					
					$closingStock=$openingBalance+$totalReceive-$totalIssue;

					//if($closingStock >= $cbo_value_with){
					if(($closingStock > 0 && $cbo_value_with==1) || ($closingStock >=0 && $cbo_value_with==0))
					{ 
					
						$html .='<tr>
							<td>'.$i.'</td>
							<td>'.$prod_ids.'</td>								
							<td>'.$determinaArr[$row['detarmination_id']].'</td>                                 
							<td>'.$composition_arr[$row['detarmination_id']].'</td>
							
							<td>'.$composition_info_arr[$row['detarmination_id']]["type"].'</td> 
							<td>'.$composition_info_arr[$row['detarmination_id']]["design"].'</td> 
							<td>'.$row["weight"].'</td> 
							<td>'.$row["dia_width"].'</td> 
							<td>'.$row['batch_no'].'</td> 
							
							<td>'.$color_arr[$row["color"]].'</td> 
							
							<td>'.number_format($openingBalance,2,'.','').'</td>
							<td>'.number_format($purchase,2,'.','').'</td>
							<td>'.number_format($issue_return,2,'.','').'</td>
							<td>'.number_format($transfer_in,2,'.','').'</td>
							<td>'.number_format($totalReceive,2,'.','').'</td>
							
							<td>'.number_format($issue,2,'.','').'</td>
							<td>'.number_format($rec_return,2,'.','').'</td>
							<td>'.number_format($transfer_out,2,'.','').'</td>
							<td>'.number_format($totalIssue,2,'.','').'</td>
							<td>'.number_format($closingStock,2,'.','').'</td>
							<td>'.$days_doh[$prod_ids]['daysonhand'].'</td>
						</tr>';
					
						$tot_opening_bal+=$openingBalance;
						$tot_purchuse+=$purchase;
						$tot_issue_return+=$issue_return;
						$tot_trans_in+=$transfer_in;
						$tot_receive+=$totalReceive;
						$tot_issue+=$issue;
						$tot_receive_return+=$rec_return;
						$tot_trans_out+=$transfer_out;
						$tot_total_issue+=$totalIssue;
						$total_closing_stock+=$closingStock;
						$i++;
					}
				}
			}
		}
	
	$html .='</table>
	
	<table style="width:2200px" border="1" cellpadding="2" cellspacing="0" class="tbl_bottom" rules="all" id="">
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>

			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>

			<td>Total</td>
			<td>'. number_format($tot_opening_bal,2).'</td>
			<td>'. number_format($tot_purchuse,2).'</td>
			<td>'. number_format($tot_issue_return,2).'</td>
			<td>'. number_format($tot_trans_in,2).'</td>
			<td>'. number_format($tot_receive,2).'</td>
			<td>'. number_format($tot_issue,2).'</td>
			<td>'. number_format($tot_receive_return,2).'</td>
			<td>'. number_format($tot_trans_out ,2).'</td>
			<td>'. number_format($tot_total_issue,2).'</td>
			<td>'. number_format($total_closing_stock,2).'</td>
			<td>&nbsp;</td>  
		</tr>
	</table>';

	
	//$html = ob_get_contents();
	//ob_clean();
	foreach (glob("ffcsr_*.xls") as $filename) {
		@unlink($filename);
	}
	$name=time();
	$filename='ffcsr_'.$user_id."_".$name.".xls";
	

	/* 
		N.B. For PHP version 7.4 or higher

		$reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
		$spreadsheet = $reader->loadFromString($html);

		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
		$writer->save($filename);  
	*/
	

	//N.B. For Lower PHP version

	$tmp_html = './tmp_html/';

	if (file_exists($tmp_html)) {
		
	} else {
		mkdir("tmp_html/" . $dirname, 0777);
		//echo "The directory $dirname was successfully created.";
		//exit;
	}




	
	$temporary_html_file = './tmp_html/' . time() . '.html';
	file_put_contents($temporary_html_file, $html);
	$reader = IOFactory::createReader('Html');

	$spreadsheet = $reader->load($temporary_html_file);
	$writer = IOFactory::createWriter($spreadsheet, 'Xls');
	$writer->save($filename); 



	header('Content-Type: application/x-www-form-urlencoded');
	header('Content-Transfer-Encoding: Binary');
	header("Content-disposition: attachment; filename=\"".$filename."\"");

	//readfile($filename);
	unlink($temporary_html_file);
	//unlink($filename);

	echo "$filename####$filename"; 
	exit();
}

if($action=="stock_qnty_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $po_id."**".$color;die;

	$color_arr = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");

	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="550" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th width="50">Sl</th>
						<th width="80">Color</th>
						<th width="100">Batch No</th>
						<th width="200">Fabric Des.</th>
						<th>Qty</th>
					</tr>
				</thead>
				<tbody>
					<?
					if ($store_id>0)
					{
						$store_cond = " and b.store_id = $store_id";
						$store_cond2 = " and a.store_id = $store_id";
					}

					$mrr_sql =sql_select("select a.item_category, a.prod_id,b.color,b.product_name_details,a.transaction_type,a.batch_id,a. pi_wo_batch_no ,
					(case when a.transaction_type=1  then a.cons_quantity else 0 end) as receive,
					            (case when a.transaction_type=2 then a.cons_quantity else 0 end) as issue,
					            (case when a.transaction_type=3  then a.cons_quantity else 0 end) as rec_return,
					            (case when a.transaction_type=4  then a.cons_quantity else 0 end) as issue_return,
					            (case when a.transaction_type=5  then a.cons_quantity else 0 end) as transfer_in,
					            (case when a.transaction_type=6 then a.cons_quantity else 0 end) as transfer_out

					from inv_transaction a, product_details_master b 
					where a.prod_id = b.id and a.status_active=1 and a.is_deleted=0 and b.id=$prod_id 

					and  a.item_category in ($item_category) and b.status_active=1 and b.is_deleted=0  $store_cond2 and b.company_id = $companyID 

					 order by a.prod_id ASC");

					$allProdId="";
					foreach($mrr_sql as $row)
					{
						if(($row[csf("item_category")]==3) && ($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==2))
						{
							$recv_issue_batchId=$row[csf("batch_id")];
						}
						else
						{
							$recv_issue_batchId=$row[csf("pi_wo_batch_no")];
						}
						if ($recv_issue_batchId==$batch_id) 
						{
							$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['receive']+=$row[csf("receive")];
							$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['issue_return']+=$row[csf("issue_return")];
							$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['transfer_in']+=$row[csf("transfer_in")];
							$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['issue']+=$row[csf("issue")];
							$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['rec_return']+=$row[csf("rec_return")];
							$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['transfer_out']+=$row[csf("transfer_out")];
							
							$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['color']=$row[csf("color")];
							$data_trns_array[$row[csf("prod_id")]][$recv_issue_batchId]['product_name_details']=$row[csf("product_name_details")];

							$allProdId.=$row[csf("prod_id")].",";
							$all_prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];

							$all_batch_id_arr[$recv_issue_batchId] =$recv_issue_batchId;
						}
								
					}
					$allProdId=chop($allProdId,",");

					$all_batch_ids = implode(",",array_filter($all_batch_id_arr));

					$sql_batch=sql_select("select a.id,a.batch_no from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in($all_batch_ids)"); 

					foreach($sql_batch as $row)
					{
						$batch_info_arr[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
					}

					$i=1;

					foreach ($data_trns_array as $prod_ids => $prod_data) 
					{
						foreach ($prod_data as $batch_ids => $row) 
						{

							if($batch_ids==$batch_id)
							{
								$recv=$data_trns_array[$prod_ids][$batch_id]['receive']+$data_trns_array[$prod_ids][$batch_id]['issue_return']+$data_trns_array[$prod_ids][$batch_id]['transfer_in'];
								$issue+=$data_trns_array[$prod_ids][$batch_id]['rec_return']+$data_trns_array[$prod_ids][$batch_id]['issue']+$data_trns_array[$prod_ids][$batch_id]['transfer_out'];
									
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";	
								$balance = $recv - $issue;
								if($balance !=0)
								{
									?>
									<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
										<td align="center"><p><? echo $i; ?></p></td>
										<td align="center"><p><? echo $color_arr[$row['color']]; ?></p></td>
										<td><p><? echo $batch_info_arr[$batch_ids]["batch_no"]; ?></p></td>
										<td align="center"><p><? echo $row['product_name_details']; ?></p></td>
										<td align="right"><p><? echo number_format($balance,2); ?></p></td>
									</tr>
									<?
									$tot_qty+=$balance;
									$i++;
								}
							}
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="4" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}

if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=6 and report_id=165 and is_deleted=0 and status_active=1");
   	$printButton=explode(',',$print_report_format);

	foreach($printButton as $id){

		if($id==178)$buttonHtml.='<input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:60px" class="formbutton" />';
		if($id==195)$buttonHtml.='<input type="button" name="search" id="search" value="Show 2" onClick="generate_report(8)" style="width:60px" class="formbutton" />';
		if($id==242)$buttonHtml.='<input type="button" name="search" id="search" value="Show 3" onClick="generate_report(10)" style="width:60px" class="formbutton" />';
		if($id==256)$buttonHtml.='<input type="button" name="search1" id="search1" value="Report2" onClick="generate_report(4)" style="width:70px" class="formbutton">';
		if($id==263)$buttonHtml.='<input type="button" name="search3" id="search3" value="Report3" onClick="generate_report(6)" style="width:60px" class="formbutton" />';
		if($id==264)$buttonHtml.='<input type="button" name="search2" id="search2" value="Report4" onClick="generate_report(7)" style="width:70px" class="formbutton" title="This report only for garments not for sales order or textile.">';
		if($id==352)$buttonHtml.='<input type="button" name="search2" id="search2" value="UOM wise" onClick="generate_report(5)" style="width:70px" class="formbutton">';
		if($id==734)$buttonHtml.='<input type="button" name="search2" id="search2" value="Total Value" onClick="generate_report(9)" style="width:70px" class="formbutton">';
		if($id==422)$buttonHtml.='<input type="button" name="search2" id="search2" value="Excel Only" onClick="generate_report_exel_only(1)" style="width:70px" class="formbutton"><a href="" id="aa1"></a>';

		
	}

   echo "document.getElementById('button_data_panel').innerHTML = '".$buttonHtml."';\n";
    exit();
}

?>