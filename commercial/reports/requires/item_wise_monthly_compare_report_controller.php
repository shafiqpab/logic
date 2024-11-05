<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_store")
{
	$data=explode("**",$data);
	if($data[1]==2) $disable=1; else $disable=0;
	$userCredential = sql_select("SELECT store_location_id, item_cate_id FROM user_passwd where id=$user_id");
	$store_cond = ($userCredential[0][csf("store_location_id")]) ? " and a.id in (".$userCredential[0][csf("store_location_id")].")" : "" ;
	echo create_drop_down( "cbo_store_name", 120, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id='$data[0]' and  b.category_type in(8,9,10,11,15,16,17,18,19,20,21,22,32,34,35,36,37,38,39,40,41,68) $store_cond group by a.id,a.store_name","id,store_name", 1, "--Select Store--", 0, "",$disable );
	exit();
}


//item group search------------------------------//
if($action=="item_group_such_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
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
	$sql="SELECT id,item_name from  lib_item_group where item_category in($cbo_item_category_id) and status_active=1 and is_deleted=0";
	//echo $sql; die;
	$arr=array();
	echo create_list_view("list_view", "Item Group","250","300","300",0, $sql , "js_set_value", "id,item_name", "", 1, "0", $arr, "item_name", "","setFilterGrid('list_view',-1)","0","",1);
		
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
if($action=="item_account_such_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
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
	$txt_item_group_id=str_replace("'","",$txt_item_group_id);
	$txt_item_acc=str_replace("'","",$txt_item_acc);
	$txt_item_account_id=str_replace("'","",$txt_item_account_id);
	$txt_item_acc_no=str_replace("'","",$txt_item_acc_no);
	 $sql_cond="";
	if($txt_item_group_id!="") $sql_cond=" and item_group_id in($txt_item_group_id)";
	
	$sql="SELECT id,item_account,item_category_id,item_group_id,item_description,supplier_id from  product_details_master where item_category_id in($cbo_item_category_id) and status_active=1 and is_deleted=0 and company_id=$company $sql_cond"; 
	//echo $sql; die;
	$arr=array(1=>$general_item_category,2=>$itemgroupArr,4=>$supplierArr);
	echo  create_list_view("list_view", "Item Description", "150,100","300","320",0, $sql , "js_set_value", "id,item_description", "", 1, "0,item_category_id,item_group_id,0,supplier_id,0", $arr , "item_description", "",'setFilterGrid("list_view",-1);','0,0,0,0,0,0','',1) ;
		
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


if($action=="generate_report")
{ 
		$process = array(&$_POST);
		extract(check_magic_quote_gpc($process));
		$report_title=str_replace("'","",$report_title);
		$cbo_company_name=str_replace("'","",$cbo_company_name);
		$cbo_store_name=str_replace("'","",$cbo_store_name);
		$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
		$item_group_id=str_replace("'","",$item_group_id);
		
		$item_desc= explode(',', $item_account_id) ;
		$item_desc2 = '';
		foreach ($item_desc as $value) {
			if ($item_desc2 == '') {
				$item_desc2 .= "'".$value."'";
			}else{
				$item_desc2 .= ','."'".$value."'";
			}			
		}
		//echo $item_desc2;

		//print_r($item_desc);
		$cbo_year_name=str_replace("'","",$cbo_year_name);
		$cbo_month=str_replace("'","",$cbo_month);
		$cbo_month_end=str_replace("'","",$cbo_month_end);
		$cbo_end_year_name=str_replace("'","",$cbo_end_year_name);
		
		//$cu_start_date = cal_days_in_month(CAL_GREGORIAN,10,2005);
		
		
		$sql_data_smv=sql_select("select comapny_id,year, basic_smv from lib_capacity_calc_mst where year between $cbo_year_name and $cbo_end_year_name");
		foreach( $sql_data_smv as $row)
		{
			$basic_smv_arr[$row[csf("comapny_id")]][$row[csf("year")]]=$row[csf("basic_smv")];
		}


		$s_daysinmonth=cal_days_in_month(CAL_GREGORIAN, $cbo_month, $cbo_year_name);
		$e_daysinmonth=cal_days_in_month(CAL_GREGORIAN, $cbo_month_end, $cbo_end_year_name);
		$from_s_date=$cbo_year_name."-".$cbo_month."-"."01";
		$from_e_date=$cbo_year_name."-".$cbo_month."-".$s_daysinmonth;

		$to_s_date=$cbo_end_year_name."-".$cbo_month_end."-"."01";
		$to_end_date=$cbo_end_year_name."-".$cbo_month_end."-".$e_daysinmonth;

		//$from_month = $cbo_year_name."-".$cbo_month;
		//$to_month = $cbo_end_year_name."-".$cbo_month_end;
		$report_generat_date = 'From: '.$months[$cbo_month].' '.$cbo_year_name.' To: '.$months[$cbo_month_end].' '.$cbo_end_year_name;
		$months[$cbo_month_end].' '.$cbo_end_year_name;

		//echo $s_date."==".$cu_end_date."==".$e_date."==".$daysinmonth;

		if($db_type==2)
		{
			$from_s_date=change_date_format($from_s_date,'yyyy-mm-dd','-',1);
			$from_e_date=change_date_format($from_e_date,'yyyy-mm-dd','-',1);
			$to_s_date=change_date_format($to_s_date,'yyyy-mm-dd','-',1);
			$to_end_date=change_date_format($to_end_date,'yyyy-mm-dd','-',1);
		}

		if ($cbo_item_category_id !="") $category_cond= " and item_category in($cbo_item_category_id)"; else $category_cond=" and b.item_category_id not in(1,2,3,4,12,13,14,24,25,3143,71,72,73,74,75,76,77,78,79)";

		$issue_sql="select prod_id, cons_quantity from inv_transaction where status_active=1 and company_id in($cbo_company_name) and transaction_type=2 and transaction_date between '$s_date' and '$e_date' $category_cond ";
		//echo $issue_sql;die;
		$issue_result=sql_select($issue_sql);
		$issue_data=array();
		foreach($issue_result as $row)
		{
			$issue_data[$row[csf("prod_id")]]+=$row[csf("cons_quantity")];
		}

		//echo $cbo_company_name."===".$cbo_store_name;die;
		
		$sql_cond="";
		
		if ($cbo_item_category_id !="") $sql_cond= " and b.item_category_id in($cbo_item_category_id)"; else $sql_cond.=" and b.item_category_id not in(1,2,3,4,12,13,14,24,25,3143,71,72,73,74,75,76,77,78,79)";
		if ($item_group_id !="") $sql_cond.=" and b.item_group_id in($item_group_id)";
		if ($item_account_id !="") $sql_cond.=" and b.item_description in($item_desc2)";

		$sql="select a.id, a.prod_id, a.item_category, a.pi_wo_batch_no, a.receive_basis, b.item_group_id, b.item_description,b.unit_of_measure, c.id as lib_item_group_id, c.item_name,
				(case when a.transaction_date between '".$from_s_date."' and '".$from_e_date."' then a.cons_quantity else 0 end) as opening_qty,
				(case when a.transaction_date between '".$from_s_date."' and '".$from_e_date."' then a.cons_amount else 0 end) as cons_amount,
				(case when a.transaction_date between '".$to_s_date."' and '".$to_end_date."' then a.cons_quantity else 0 end) as cu_opening_qty,
				(case when a.transaction_date between '".$to_s_date."' and '".$to_end_date."' then a.cons_amount else 0 end) as cu_cons_amount
	 	from inv_transaction a, product_details_master b, lib_item_group c
	 	where a.prod_id=b.id and b.item_group_id=c.id and a.transaction_type=1 and a.company_id in($cbo_company_name) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond"; 
	 	//$item_category_id $group_id $store_name  $search_cond 
	 	


	 	//echo $sql;die;
		$result = sql_select($sql);
		//var_dump(count($result));
		$num_rows = count($result);
		$all_pi_id=''; 
		$mrr_pi_qnty=array();
		$all_data=array();
		$company_arr = sql_select("select id, company_name from lib_company where id in($cbo_company_name)");
		 
		foreach($result as $row)
		{

			if($row[csf("receive_basis")]==1) 
			{
				
				$mrr_pi_qnty[$row[csf("pi_wo_batch_no")]][$row[csf("prod_id")]]+=$row[csf("cons_quantity")];
				if($pi_check[$row[csf("pi_wo_batch_no")]]=="")
				{							
					$pi_check[$row[csf("pi_wo_batch_no")]]=$row[csf("pi_wo_batch_no")];
					$all_pi_id.=$row[csf("pi_wo_batch_no")].",";
					$all_data[$row[csf("prod_id")]]["pi_wo_batch_no"].=$row[csf("pi_wo_batch_no")].",";
				}
				
			}
			$all_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$all_data[$row[csf("prod_id")]]["item_category"]=$row[csf("item_category")];
			$all_data[$row[csf("prod_id")]]["item_group_id"]=$row[csf("item_group_id")];
			$all_data[$row[csf("prod_id")]]["item_description"]=$row[csf("item_description")];
			$all_data[$row[csf("prod_id")]]["unit_of_measure"]=$row[csf("unit_of_measure")];
			$all_data[$row[csf("prod_id")]]["item_group_name"]=$row[csf("item_name")];
			$all_data[$row[csf("prod_id")]]["opening_qty"]+=$row[csf("opening_qty")];
			$all_data[$row[csf("prod_id")]]["cons_amount"]+=$row[csf("cons_amount")];
			$all_data[$row[csf("prod_id")]]["cu_opening_qty"]+=$row[csf("cu_opening_qty")];
			$all_data[$row[csf("prod_id")]]["cu_cons_amount"]+=$row[csf("cu_cons_amount")];
			$all_data[$row[csf("prod_id")]]["tot_cons_quantity"]+=$row[csf("cons_quantity")];

		}

		$all_pi_id=chop($all_pi_id,",");
		if($all_pi_id!="")
		{
			//echo "select a.pi_id, a.item_prod_id, a.quantity from com_pi_item_details a, com_btb_lc_pi b where a.pi_id=b.pi_id and a.status_active=1 and b.status_active=1 and a.pi_id in($all_pi_id)";

			$sql_pipe_line=sql_select("select a.pi_id, a.item_prod_id, a.quantity from com_pi_item_details a, com_btb_lc_pi b where a.pi_id=b.pi_id and a.status_active=1 and b.status_active=1 and a.pi_id in($all_pi_id)");
			foreach($sql_pipe_line as $row)
			{
				$pipeLine_qty[$row[csf("pi_id")]][$row[csf("item_prod_id")]]+=$row[csf("quantity")];
			}
		}	
		
		//var_dump($sql_pipe_line);die;		
		$i=1;
		ob_start();	
		?>
		<div align="center" style="height:auto; margin:0 auto; padding:0; width:1400px">
			<table width="1380" cellpadding="0" cellspacing="0" id="caption" align="left">
				<thead>
					<tr style="border:none;">
						<td colspan="10" align="center" class="form_caption" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td> 
					</tr>
					<tr style="border:none;">
						<td colspan="10" class="form_caption" align="center" style="border:none; font-size:14px;">
						   <b>Company Name : 
						   	<? 
						   		foreach ($company_arr as $company){
						   			echo chop($company[csf("company_name")].' ',",");
						   		}

					   		?></b>                               
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="10" align="center" class="form_caption" style="border:none;font-size:12px; font-weight:bold">
							<? echo "Report Date : ".$report_generat_date;?>
						</td>
					</tr>
				</thead>
			</table>
			<table width="1380" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="rpt_table_header" align="left">		
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="60">Prod. ID</th>
						<th width="120">Item Category</th>
						<th width="200">Item Group</th>
						<th width="200">Item Description</th>
                        <th width="40">UOM</th>
						<th width="100">Form Month Rcv.Qty</th>
                        <th width="70">Avg Rate</th>
						<th width="100">Total Amount</th>
						<th width="100">To Month Rcv.Qty</th>
						<th width="50">Avg Rate</th>
						<th width="100">Total Amount</th>
						<th width="100">Variance Amount</th>
						<th>Variance Qty</th>
					</tr> 					
				</thead>
			</table>
			<div style="width:1400px; max-height:250px; overflow-y:scroll; overflow-x:hidden;" id="scroll_body"> 
			<table width="1380" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_id" align="left">
			<?

				foreach($all_data as $row)
				{
					if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
					//print_r($row);
					$pi_id_arr=	explode(",",chop($row["pi_wo_batch_no"],","));
					//print_r($pi_id_arr); echo "<br/>";
					$pipe_qty=0;
					foreach($pi_id_arr as $pi_id)
					{
						if($pipeLine_qty[$pi_id][$row["prod_id"]])
						{
							$pipe_qty+=$pipeLine_qty[$pi_id][$row["prod_id"]]-$mrr_pi_qnty[$pi_id][$row["prod_id"]];
						}
						//$pipe_qty+=$pipeLine_qty[$pi_id][$row["prod_id"]];
					}
					$prev_avg_rate=$row[("cons_amount")]/$row[("opening_qty")];
					$cu_avg_rate=$row[("cu_cons_amount")]/$row[("cu_opening_qty")];
					$prev_tot_amount=$row[("cons_amount")];
					$cu_tot_amount=$row[("cu_cons_amount")];
					$variance_amount=$prev_tot_amount-$cu_tot_amount;
					$variance_qty=$row[("opening_qty")]-$row[("cu_opening_qty")];
					?>

					<?// if all value 0 then dont show list view.
						if ($row[("opening_qty")] > 0 || $row[("cu_opening_qty")] > 0) 
						{			
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="60" align="center"><? echo $row["prod_id"]; ?></td>
						<td width="120" style="word-break:break-all;"><? echo $item_category[$row[("item_category")]]; ?></td>
						<td width="200" style="word-break:break-all;"><? echo $row[("item_group_name")]; ?></td>
						<td width="200" style="word-break:break-all;"><? echo $row[("item_description")]; ?></td>
						<td width="40" align="center" style="word-break:break-all;"><? echo $unit_of_measurement[$row[("unit_of_measure")]]; ?></td>
						
	                    <td width="100" align="right"><? echo number_format($row[("opening_qty")],2); ?></td>
						<td width="70" align="right"><? echo is_nan($prev_avg_rate) ?  0.00 : number_format($prev_avg_rate);?></td>
	                    <td width="100" align="right"><? echo number_format($prev_tot_amount,2); ?></td>
						<td width="100" align="right"><? echo number_format($row[("cu_opening_qty")],2); ?></td>
						<td width="50" align="right"><? echo is_nan($cu_avg_rate) ?  0.00 : number_format($cu_avg_rate);?></td>
						<td width="100" align="right"><? echo number_format($cu_tot_amount,2); ?></td>
						<td width="100" align="right"><? echo number_format($variance_amount,2); ?></td>
						<td align="right"><? echo number_format($variance_qty,2); ?></td>
					</tr>
					<?
						}
					$i++; 				
				}
			?>
			</table>
			</div>
			<table width="1380" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
				<tfoot>
					<tr>
						<th width="30"></th>
						<th width="60"></th>
						<th width="120"></th>
						<th width="200"></th>
						<th width="200"></th>
						<th width="40"></th>
						<th width="100"></th>
						<th width="70" style="text-align: right">Total: </th>
						<th width="100" id="value_prev_tot_amount" style="text-align: right"><? echo number_format($prev_tot_amount,2); ?></th>
						<th width="100"></th>
						<th width="50"></th>
						<th width="100" id="value_cu_tot_amount" style="text-align: right"><? echo number_format($cu_tot_amount,2); ?></th>
						<th width="100" id="value_variance_amount" style="text-align: right"><? echo number_format($variance_amount,2); ?></th>
						<th></th>
					</tr>
				</tfoot>
			</table>
        </div>
    <?	
	    $html = ob_get_contents();
	    ob_clean();
	    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	    foreach (glob("$user_id*.xls") as $filename) {
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
?>