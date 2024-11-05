<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_store")
{
	$data=explode("**",$data);
	if($data[1]==2) $disable=1; else $disable=0;
	echo create_drop_down( "cbo_store_name", 100, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id='$data[0]' and  b.category_type in(8,9,10,11,15,16,17,18,19,20,21,22) group by a.id,a.store_name","id,store_name", 1, "--Select Store--", 0, "",$disable );
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 120, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' $company_location_credential_cond order by location_name","id,location_name", 1, "-- Select --", $selected, "",0 );     	 
	exit();
}

if ($action=="load_drop_down_department")
{
	echo create_drop_down( "cbo_department", 120, "select a.id,a.department_name from  lib_department a, lib_division b where b.id=a.division_id and a.status_active =1 and a.is_deleted=0 and b.company_id='$data' order by department_name","id,department_name", 1, "-- Select --", $selected,"load_drop_down( 'requires/department_wise_issue_report_v2_controller', this.value , 'load_drop_down_section', 'section_td' );",0 );     	 
	exit();
}

if ($action=="load_drop_down_section")
{
	echo create_drop_down( "cbo_section", 90, "select id,section_name from lib_section where status_active =1 and is_deleted=0 and department_id='$data' order by section_name","id,section_name", 1, "-- Select --", $selected, "",0 );
	exit();
}


if ($action=="item_account_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);  
	?>	
    <script>
	 var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	 
	 function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			//alert (tbl_row_count);
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				 eval($('#tr_'+i).attr("onclick"));  
			}
		}
		
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
	} 
		  
		  
	</script>
     <input type="hidden" id="item_account_id" />
     <input type="hidden" id="item_account_val" />
 	<?
 	$group_cond = "";
 	if($data[2] !=""){$group_cond=" and item_group_id in($data[2])";}
	$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
		
	$sql="SELECT id,item_account,item_category_id,item_group_id,item_description,supplier_id from  product_details_master where item_category_id in ($data[1]) $group_cond and status_active=1 and is_deleted=0"; 
	$arr=array(1=>$item_category,2=>$itemgroupArr,4=>$supplierArr);
	echo  create_list_view("list_view", "Item Account,Item Category,Item Group,Item Description,Supplier,Product ID", "70,110,150,150,100,70","780","400",0, $sql , "js_set_value", "id,item_description", "", 0, "0,item_category_id,item_group_id,0,supplier_id,0", $arr , "item_account,item_category_id,item_group_id,item_description,supplier_id,id", "",'setFilterGrid("list_view",-1);','0,0,0,0,0,0','',1) ;
	exit();
}

if($action=="item_group_such_popup")
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

if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$txt_reference_id=str_replace("'","",$txt_reference_id);
	$from_date=str_replace("'","",$from_date);
	$to_date=str_replace("'","",$to_date);
	$search_cond="";
	if($cbo_company_name){ $search_cond.=" and a.company_id='$cbo_company_name'"; }
	if($cbo_item_category_id!=''){ $search_cond.=" and b.item_category in ($cbo_item_category_id)"; }
	if($item_account_id!=""){ $search_cond.=" and b.prod_id in ($item_account_id)"; }
	if($item_group_id!=''){ $search_cond.=" and c.item_group_id in($item_group_id)"; }
	if($cbo_department){ $search_cond.=" and b.department_id='$cbo_department'"; }
	if($cbo_section){ $search_cond.=" and b.section_id='$cbo_section'"; }
	if($cbo_store_name){ $search_cond.=" and b.store_id=$cbo_store_name"; }
	if($cbo_location){ $search_cond.=" and b.location_id=$cbo_location"; }
	if($cbo_search_by == 1 && trim($txt_reference_id) != "")
	{
		$search_cond.= " and a.req_no like '%".$txt_reference_id."%'";
	}
	else if($cbo_search_by == 2 && trim($txt_reference_id) != "")
	{
		$search_cond.= " and a.issue_number_prefix_num like  '%".$txt_reference_id."%'";
	}

	if( $from_date!='' && $to_date!='' )
	{
		if($db_type==0)
		{
			$from_date=change_date_format($from_date,'yyyy-mm-dd');
			$to_date=change_date_format($to_date,'yyyy-mm-dd');
			$today= change_date_format(date("Y-m-d"));
		 }
		if($db_type==2)
		{
			$from_date=change_date_format($from_date,'','',1);
			$to_date=change_date_format($to_date,'','',1);
			$today= change_date_format(date("Y-m-d"),'','',1);
		}
		$search_cond.= " and b.transaction_date between '$from_date' and '$to_date'";	
	}
 	
 	//library array-------------------
	$company_lib=return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
	$department_lib=return_library_array("select id,department_name from lib_department",'id','department_name');

	$main_sql = "SELECT a.id as ID, b.prod_id as PROD_ID, b.department_id as DEPARTMENT_ID, b.cons_uom as CONS_UOM, b.cons_quantity as CONS_QUANTITY, b.cons_amount as CONS_AMOUNT, c.item_category_id as ITEM_CATEGORY_ID, c.item_description as ITEM_DESCRIPTION, c.avg_rate_per_unit as AVG_RATE
	from inv_issue_master a, inv_transaction b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=2 and a.entry_form=21 and a.status_active=1 and b.status_active=1 $search_cond
	order by c.item_category_id,b.prod_id desc";
	// echo $main_sql; 

	$main_result = sql_select($main_sql);
	$all_data_arr=array();
	foreach($main_result as $row)
	{
		$all_data_arr[$row['PROD_ID']]['prod_id']=$row['PROD_ID'];
		$all_data_arr[$row['PROD_ID']]['item_category_id']=$row['ITEM_CATEGORY_ID'];
		$all_data_arr[$row['PROD_ID']]['item_description']=$row['ITEM_DESCRIPTION'];
		$all_data_arr[$row['PROD_ID']]['uom']=$row['CONS_UOM'];
		$all_data_arr[$row['PROD_ID']]['avg_rate']=$row['AVG_RATE'];

		if($row['DEPARTMENT_ID']){$department_arr[$row['DEPARTMENT_ID']]=$row['DEPARTMENT_ID'];}
		$dataArr[$row['DEPARTMENT_ID']][$row['PROD_ID']]['iss_qnty']+=$row['CONS_QUANTITY'];
		$dataArr[$row['DEPARTMENT_ID']][$row['PROD_ID']]['iss_amount']+=$row['CONS_AMOUNT'];		
		$dataArr[$row['DEPARTMENT_ID']][$row['PROD_ID']]['iss_id'].=$row['ID'].",";		
		$all_prod_id[$row['PROD_ID']]=$row['PROD_ID'];
	}
	unset($main_result);

	if($db_type==2){ $date_column_sql=" min(a.transaction_date) || ',' || max(a.transaction_date )  as total_date "; }
	else{ $date_column_sql=" concat(a.min(transaction_date),',',max(a.transaction_date))  as total_date "; }
	$all_prod_in=where_con_using_array($all_prod_id,0,'a.prod_id');
	$inv_sql="SELECT a.prod_id as PROD_ID, $date_column_sql,
	sum(case when a.transaction_date<'$from_date' and a.transaction_type in (1,4,5) then a.cons_quantity else 0 end) as OPENING_TOTAL_RECEIVE,
	sum(case when a.transaction_date<'$from_date' and a.transaction_type in (1,4,5) then a.cons_amount else 0 end) as OPENING_TOTAL_RECEIVE_AMT,
	sum(case when a.transaction_date<'$from_date' and a.transaction_type in (2,3,6) then a.cons_quantity else 0 end) as OPENING_TOTAL_ISSUE,
	sum(case when a.transaction_date<'$from_date' and a.transaction_type in (2,3,6) then a.cons_amount else 0 end) as OPENING_TOTAL_ISSUE_AMT,
	sum(case when a.transaction_type=1 and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as RCV_QTY,
	sum(case when a.transaction_type=1 and a.transaction_date between '$from_date' and '$to_date' then a.cons_amount else 0 end) as RCV_AMT,
	sum(case when a.transaction_type=3 and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as RCV_RTN_QTY,
	sum(case when a.transaction_type=3 and a.transaction_date between '$from_date' and '$to_date' then a.cons_amount else 0 end) as RCV_RTN_AMT,
	sum(case when a.transaction_type=2 and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as ISS_QTY,
	sum(case when a.transaction_type=2 and a.transaction_date between '$from_date' and '$to_date' then a.cons_amount else 0 end) as ISS_AMT,
	sum(case when a.transaction_type=4 and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as ISS_RTN_QTY,
	sum(case when a.transaction_type=4 and a.transaction_date between '$from_date' and '$to_date' then a.cons_amount else 0 end) as ISS_RTN_AMT,
	sum(case when a.transaction_type in (1,4,5) then a.cons_quantity else 0 end) as TOTAL_RCV_QTY,
	sum(case when a.transaction_type in (1,4,5) then a.cons_amount else 0 end) as TOTAL_RCV_AMT,
	sum(case when a.transaction_type in (2,3,6) then a.cons_quantity else 0 end) as TOTAL_ISS_QTY,
	sum(case when a.transaction_type in (2,3,6) then a.cons_amount else 0 end) as TOTAL_ISS_AMT
	from inv_transaction a
	where a.transaction_type in (1,2,3,4,5,6) and a.status_active=1 $all_prod_in 
	group by a.prod_id 
	order by a.prod_id desc";
	// echo $inv_sql;

	$inv_result = sql_select($inv_sql);
	$inv_data=array();
	foreach($inv_result as $row)
	{
		$inv_data[$row['PROD_ID']]['opening_total_receive']=$row['OPENING_TOTAL_RECEIVE'];
		$inv_data[$row['PROD_ID']]['opening_total_receive_amt']=$row['OPENING_TOTAL_RECEIVE_AMT'];
		$inv_data[$row['PROD_ID']]['opening_total_issue']=$row['OPENING_TOTAL_ISSUE'];
		$inv_data[$row['PROD_ID']]['opening_total_issue_amt']=$row['OPENING_TOTAL_ISSUE_AMT'];
		$inv_data[$row['PROD_ID']]['rcv_qty']=$row['RCV_QTY'];
		$inv_data[$row['PROD_ID']]['rcv_amt']=$row['RCV_AMT'];
		$inv_data[$row['PROD_ID']]['rcv_rtn_qty']=$row['RCV_RTN_QTY'];
		$inv_data[$row['PROD_ID']]['rcv_rtn_amt']=$row['RCV_RTN_AMT'];
		$inv_data[$row['PROD_ID']]['iss_qty']=$row['ISS_QTY'];
		$inv_data[$row['PROD_ID']]['iss_amt']=$row['ISS_AMT'];
		$inv_data[$row['PROD_ID']]['iss_rtn_qty']=$row['ISS_RTN_QTY'];
		$inv_data[$row['PROD_ID']]['iss_rtn_amt']=$row['ISS_RTN_AMT'];
		$inv_data[$row['PROD_ID']]['total_rcv_qty']=$row['TOTAL_RCV_QTY'];
		$inv_data[$row['PROD_ID']]['total_rcv_amt']=$row['TOTAL_RCV_AMT'];
		$inv_data[$row['PROD_ID']]['total_iss_qty']=$row['TOTAL_ISS_QTY'];
		$inv_data[$row['PROD_ID']]['total_iss_amt']=$row['TOTAL_ISS_AMT'];
		$date_total=explode(",",$row['TOTAL_DATE']);
		if($db_type==2)
		{
			$ageOfdays = datediff("d",change_date_format($date_total[0],'','',1),$today);
			$daysOnHand = datediff("d",change_date_format($date_total[1],'','',1),$today);
		}
		else
		{
			$ageOfdays = datediff("d",change_date_format($date_total[0]),$today);
			$daysOnHand = datediff("d",change_date_format($date_total[1]),$today);
		}
		$inv_data[$row['PROD_ID']]['ageOfdays']=$ageOfdays ;
		$inv_data[$row['PROD_ID']]['daysOnHand']=$daysOnHand ;
	}
	unset($inv_result);

	$department_count=count($department_arr);
	if($department_count>0)
	{
		$tbl_width=1920+$department_count*160;
	}
	else
	{
		$tbl_width=2080;
	}
	ob_start();	
	?>
	<style>
		.wrd_brk{word-break: break-all;}
		.center{text-align: center;}
		.right{text-align: right;}
	</style>

	<div style="width:100%;"> 
     <fieldset style="width:<?=$tbl_width+20;?>px;">
        <table style="width:<?=$tbl_width;?>px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" > 
            <tr class="form_caption" style="border:none;">
                <td align="center" style="border:none;font-size:20px; font-weight:bold" ><strong>Department Wise Issue Report- Detail</strong></td> 
            </tr>
            <tr style="border:none;">
                <td align="center" style="border:none; font-size:17px;"><strong>
                    Company Name : <? echo $company_lib[str_replace("'","",$cbo_company_name)]; ?>  </strong>                              
                </td>
            </tr>
            <tr style="border:none;">
                <td align="center" style="border:none;font-size:12px; font-weight:bold">
                    <? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date)."   To : ".change_date_format($to_date)."" ;?>
                </td>
            </tr>
       </table> 
	   <table style="width:<?=$tbl_width;?>px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" > 
	   		<thead>
				   <tr>
						<th width="50" rowspan="3">SL</th>
						<th colspan="4">Description</th>
						<th colspan="2">Opening</th>
						<th colspan="5"></th>
						<th colspan="<?=$department_count>0 ? $department_count*2:2;?>">Department wise Issue Qty with Value</th>
						<th colspan="6">Issue</th>
						<th width="80" rowspan="3">Closing Stock</th>
						<th width="80" rowspan="3">Avg. Rate</th>
						<th width="80" rowspan="3">Stock Value</th>
						<th width="80" rowspan="3">DOH</th>
						<th rowspan="3">Age/Days</th>
				   </tr>
				   <tr>
						<th width="80" rowspan="2">Prod. ID</th>
						<th width="100" rowspan="2">Item Category</th>
						<th width="150" rowspan="2">Item Description</th>
						<th width="80" rowspan="2">UOM</th>
						<th width="80" rowspan="2">Opening Stock</th>
						<th width="80" rowspan="2">Opening Value</th>
						<th width="80" rowspan="2">Receive</th>
						<th width="80" rowspan="2">Receive Value</th>
						<th width="80" rowspan="2">Issue Return</th>
						<th width="80" rowspan="2">Total Received</th>
						<th width="80" rowspan="2">Total Receive Value</th>
						<?
							if($department_count>0)
							{
								foreach($department_arr as $val)
								{
									?>
										<th colspan="2"><?=$department_lib[$val];?></th>
									<?
								}
							}
							else
							{
								?>
									<th colspan="2"></th>
								<?
							}
						?>
						<th width="80" rowspan="2">Others Issue</th>
						<th width="80" rowspan="2">Value</th>
						<th width="80" rowspan="2">Receive Return</th>
						<th width="80" rowspan="2">Return Value</th>
						<th width="80" rowspan="2">Total Issue</th>
						<th width="80" rowspan="2">Total Issue Value</th>
				   </tr>
				   <tr>
				   		<?
							if($department_count>0)
							{
								foreach($department_arr as $val)
								{
									?>
										<th width="80">Issue Qty.</th>
										<th width="80">Value</th>
									<?
								}
							}
							else
							{
								?>
									<th width="80">Issue Qty.</th>
									<th width="80">Value</th>
								<?
							}
						?>
				   </tr>
			</thead>
			<tbody>
				<?
					$grand_tot_department_arr=array();
					foreach($all_data_arr as $row)
					{
						$i++;
						if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						$opening_stock_qty=$inv_data[$row['prod_id']]['opening_total_receive']-$inv_data[$row['prod_id']]['opening_total_issue'];
						$opening_stock_amt=$inv_data[$row['prod_id']]['opening_total_receive_amt']-$inv_data[$row['prod_id']]['opening_total_issue_amt'];
						$total_rcv_qty=$opening_stock_qty+$inv_data[$row['prod_id']]['rcv_qty']+$inv_data[$row['prod_id']]['iss_rtn_qty'];
						$total_rcv_amt=$opening_stock_amt+$inv_data[$row['prod_id']]['rcv_amt']+$inv_data[$row['prod_id']]['iss_rtn_amt'];
						$total_iss_qty=$dataArr[0][$row['prod_id']]['iss_qnty']+$inv_data[$row['prod_id']]['rcv_rtn_qty'];
						$stock_qty=$inv_data[$row['prod_id']]['total_rcv_qty']-$inv_data[$row['prod_id']]['total_iss_qty'];
						$stock_amt=$inv_data[$row['prod_id']]['total_rcv_amt']-$inv_data[$row['prod_id']]['total_iss_amt'];
						// $stock_rate=$stock_amt/$stock_qty;
						?>
							<tr bgcolor="<?=$bgcolor;?>" onclick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
								<td class="center"><?=$i;?></td>								
								<td class="center"><?=$row['prod_id'];?></td>								
								<td class="wrd_brk"><?=$general_item_category[$row['item_category_id']];?></td>
								<td class="wrd_brk"><?=$row['item_description'];?></td>								
								<td class="wrd_brk center"><?=$unit_of_measurement[$row['uom']];?></td>								
								<td class="wrd_brk right"><?=fn_number_format($opening_stock_qty,2);?></td>								
								<td class="wrd_brk right"><?=fn_number_format($opening_stock_amt,2);?></td>								
								<td class="wrd_brk right"><?=fn_number_format($inv_data[$row['prod_id']]['rcv_qty'],2);?></td>
								<td class="wrd_brk right"><?=fn_number_format($inv_data[$row['prod_id']]['rcv_amt'],2);?></td>
								<td class="wrd_brk right"><?=fn_number_format($inv_data[$row['prod_id']]['iss_rtn_qty'],2);?></td>				
								<td class="wrd_brk right"><?=fn_number_format($total_rcv_qty,2);?></td>								
								<td class="wrd_brk right"><?=fn_number_format($total_rcv_amt,2);?></td>	
								<?
									if($department_count>0)
									{
										foreach($department_arr as $val)
										{
											?>
												<td class="wrd_brk right"><?=fn_number_format($dataArr[$val][$row['prod_id']]['iss_qnty'],2);?></td>
												<td class="wrd_brk right"><?=fn_number_format($dataArr[$val][$row['prod_id']]['iss_amount'],2);?></td>
											<?
											$total_iss_qty+=$dataArr[$val][$row['prod_id']]['iss_qnty'];
											$grand_tot_department_arr[$val]['iss_qnty']+=$dataArr[$val][$row['prod_id']]['iss_qnty'];
											$grand_tot_department_arr[$val]['iss_amount']+=$dataArr[$val][$row['prod_id']]['iss_amount'];
										}
									}
									else
									{
										?>
											<td></td>
											<td></td>
										<?
									}
								?>					
								<td class="wrd_brk right">
									<a href='#report_details' onClick="openmypage_trans('<? echo chop($dataArr[0][$row['prod_id']]['iss_id'],','); ?>','<? echo $cbo_company_name; ?>');"><?=fn_number_format($dataArr[0][$row['prod_id']]['iss_qnty'],2);?></a>
								</td>
								<td class="wrd_brk right"><?=fn_number_format($dataArr[0][$row['prod_id']]['iss_amount'],2);?></td>
								<td class="wrd_brk right"><?=fn_number_format($inv_data[$row['prod_id']]['rcv_rtn_qty'],2);?></td>
								<td class="wrd_brk right"><?=fn_number_format($inv_data[$row['prod_id']]['rcv_rtn_amt'],2);?></td>	
								<td class="wrd_brk right"><?=fn_number_format($total_iss_qty,2);?></td>	
								<td class="wrd_brk right"><?=fn_number_format($total_iss_qty*$row['avg_rate'],2);?></td>	
								<td class="wrd_brk right"><?=fn_number_format($stock_qty,2);?></td>	
								<td class="wrd_brk right"><?=fn_number_format($row['avg_rate'],2);?></td>	
								<td class="wrd_brk right"><?=fn_number_format($stock_amt,2);?></td>	
								<td class="wrd_brk center"><?=$inv_data[$row['prod_id']]['daysOnHand'];?></td>	
								<td class="wrd_brk center"><?=$inv_data[$row['prod_id']]['ageOfdays'];?></td>	
							</tr>
						<?
						$grand_tot_opening_stock_qty+=$opening_stock_qty;
						$grand_tot_opening_stock_amt+=$opening_stock_amt;
						$grand_tot_rcv_qty+=$inv_data[$row['prod_id']]['rcv_qty'];
						$grand_tot_rcv_amt+=$inv_data[$row['prod_id']]['rcv_amt'];
						$grand_tot_iss_rtn_qty+=$inv_data[$row['prod_id']]['iss_rtn_qty'];
						$grand_tot_total_rcv_qty+=$total_rcv_qty;
						$grand_tot_total_rcv_amt+=$total_rcv_amt;
						$grand_tot_others_iss_qty+=$dataArr[0][$row['prod_id']]['iss_qnty'];
						$grand_tot_others_iss_amt+=$dataArr[0][$row['prod_id']]['iss_amount'];
						$grand_tot_rcv_rtn_qty+=$inv_data[$row['prod_id']]['rcv_rtn_qty'];
						$grand_tot_rcv_rtn_amt+=$inv_data[$row['prod_id']]['rcv_rtn_amt'];
						$grand_tot_total_iss_qty+=$total_iss_qty;
						$grand_tot_total_iss_amt+=$total_iss_qty*$row['avg_rate'];
						$grand_tot_stock_qty+=$stock_qty;
						$grand_tot_stock_amt+=$stock_amt;
						$grand_tot_daysOnHand+=$inv_data[$row['prod_id']]['daysOnHand'];
						$grand_tot_ageOfdays+=$inv_data[$row['prod_id']]['ageOfdays'];
					}
				?>
			</tbody>
			<tfoot>
				<tr bgcolor="#EFEFEF">
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="right">Total:&nbsp;</td>
					<td class="wrd_brk right"><?=fn_number_format($grand_tot_opening_stock_qty,2);?></td>
					<td class="wrd_brk right"><?=fn_number_format($grand_tot_opening_stock_amt,2);?></td>
					<td class="wrd_brk right"><?=fn_number_format($grand_tot_rcv_qty,2);?></td>
					<td class="wrd_brk right"><?=fn_number_format($grand_tot_rcv_amt,2);?></td>
					<td class="wrd_brk right"><?=fn_number_format($grand_tot_iss_rtn_qty,2);?></td>
					<td class="wrd_brk right"><?=fn_number_format($grand_tot_total_rcv_qty,2);?></td>
					<td class="wrd_brk right"><?=fn_number_format($grand_tot_total_rcv_amt,2);?></td>
					<?
						if($department_count>0)
						{
							foreach($department_arr as $val)
							{
								?>
									<td class="wrd_brk right"><?=fn_number_format($grand_tot_department_arr[$val]['iss_qnty'],2);?></td>
									<td class="wrd_brk right"><?=fn_number_format($grand_tot_department_arr[$val]['iss_amount'],2);?></td>
								<?
							}
						}
						else
						{
							?>
								<td></td>
								<td></td>
							<?
						}
					?>
					<td class="wrd_brk right"><?=fn_number_format($grand_tot_others_iss_qty,2);?></td>
					<td class="wrd_brk right"><?=fn_number_format($grand_tot_others_iss_amt,2);?></td>
					<td class="wrd_brk right"><?=fn_number_format($grand_tot_rcv_rtn_qty,2);?></td>
					<td class="wrd_brk right"><?=fn_number_format($grand_tot_rcv_rtn_amt,2);?></td>
					<td class="wrd_brk right"><?=fn_number_format($grand_tot_total_iss_qty,2);?></td>
					<td class="wrd_brk right"><?=fn_number_format($grand_tot_total_iss_amt,2);?></td>
					<td class="wrd_brk right"><?=fn_number_format($grand_tot_stock_qty,2);?></td>
					<td></td>
					<td class="wrd_brk right"><?=fn_number_format($grand_tot_stock_amt,2);?></td>
					<td class="wrd_brk center"><?=$grand_tot_daysOnHand;?></td>
					<td class="wrd_brk center"><?=$grand_tot_ageOfdays;?></td>
				</tr>
			</tfoot>
	   </table>
     </div>
    </fieldset>
   </div>
     <?
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("$user_id*.xls") as $filename) {
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html####$filename"; 
	exit();	
}
if($action=="item_issue_popup")
{
	echo load_html_head_contents("Item Details Info", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$iss_sql = "SELECT a.id as ID, a.issue_number as ISSUE_NUMBER, a.issue_date as ISSUE_DATE, a.challan_no as CHALLAN_NO
	from inv_issue_master a
	where a.entry_form=21 and a.status_active=1 and id in($issue_id) order by a.id";
	// echo $iss_sql;
	$iss_result=sql_select($iss_sql);
	?>
	<style>
		.center{text-align: center;}
	</style>
	<div  style="width:360px" >
	<fieldset style="width:350px">
		<table style="width:340px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th width="40">Sl</th>
					<th width="110">Issue. ID</th>
					<th width="110">Chalan No</th>
					<th>Issue. Date</th>
				</tr>
			</thead>
			<tbody>
				<?
					$i=1;
					foreach($iss_result as $row)
					{
						if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						?>
							<tr bgcolor="<?=$bgcolor;?>" onclick="change_color('trpop_<?=$i; ?>','<?=$bgcolor; ?>')" id="trpop_<?=$i; ?>">
								<td class="center"><?=$i;?></td>
								<td class="center"><?=$row['ISSUE_NUMBER'];?></td>
								<td class="center"><?=$row['CHALLAN_NO'];?></td>
								<td class="center"><?=change_date_format($row['ISSUE_DATE']);?></td>
							</tr>
						<?
						$i++;
					}
				?>
			</tbody>
		</table>
	</fieldset>
	</div>
	<?
	exit();
}
?>