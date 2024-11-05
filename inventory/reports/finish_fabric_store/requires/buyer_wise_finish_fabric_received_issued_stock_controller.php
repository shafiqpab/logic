<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	if($data[1]==1) $party="1,3,21,90"; else $party="80";
	echo create_drop_down( "cbo_buyer_id", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}

if($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 115, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","" );
	exit();
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

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
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( ddd );
		}
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click');
			}
		}
	</script>
	</head>
	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:580px;">
					<table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>Buyer</th>
							<th>Search Job</th>
							<th>Search Style</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:70px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
							<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
						</thead>
						<tbody>
							<tr>
								<td align="center">
									<?
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
									?>
								</td>
								<td align="center">
									<input type="text" style="width:130px" class="text_boxes" name="txt_search_job" id="txt_search_job" placeholder="Job No" />
								</td>
								<td align="center">
									<input type="text" style="width:130px" class="text_boxes" name="txt_search_style" id="txt_search_style" placeholder="Style Ref." />
								</td>
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_search_job').value+'**'+document.getElementById('txt_search_style').value+'**'+'<? echo $cbo_year_id; ?>', 'create_job_no_search_list_view', 'search_div', 'buyer_wise_finish_fabric_received_issued_stock_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:70px;" />
	                        </td>
	                    </tr>
	                </tbody>
	            </table>
	            <div style="margin-top:15px" id="search_div"></div>
	        </fieldset>
	    </form>
	</div>
	</body>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	//$month_id=$data[5];
	//echo $month_id;

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$data[1]";
	}

	if($data[2]!='') $job_cond=" and job_no_prefix_num=$data[2]"; else $job_cond="";
	if($data[3]!='') $style_cond=" and style_ref_no like '$data[3]'"; else $style_cond="";

	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field_by="year(insert_date)";
	else if($db_type==2) $year_field_by="to_char(insert_date,'YYYY')";
	else $year_field_by="";

	if($year_id!=0) $year_cond=" and $year_field_by='$year_id'"; else $year_cond="";

	$arr=array (0=>$buyer_arr);
	$sql= "select id, job_no, job_no_prefix_num, buyer_name, style_ref_no, $year_field_by as year from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id $buyer_id_cond $job_cond $style_cond $year_cond order by id DESC";

	echo create_list_view("tbl_list_search", "Buyer Name,Job No,Year,Style Ref. No", "170,130,80,60","610","270",0, $sql , "js_set_value", "id,job_no", "", 1, "buyer_name,0,0,0", $arr , "buyer_name,job_no,year,style_ref_no", "",'','0,0,0,0','',1) ;
	exit();
}

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$costing_per_id_library=return_library_array( "select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per");
$consumtion_library=return_library_array( "select job_no, avg_finish_cons from wo_pre_cost_fabric_cost_dtls", "job_no", "avg_finish_cons");

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_id= str_replace("'","",$cbo_company_id);
	$txt_style_no= trim(str_replace("'","",$txt_style_no));

	if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond=" and a.buyer_name=$cbo_buyer_id";

	$job_no=trim(str_replace("'","",$txt_job_no_show));
	$search_cond='';


	if($job_no)
	{
		$job_no = "'".implode("','",array_filter(array_unique(explode(",",$job_no))))."'";
		$jobCond=""; $job_no_arr=explode(",",$job_no);
		if($db_type==2 && count($job_no_arr)>999)
		{
			$job_no_chunk_arr=array_chunk($job_no_arr,999) ;
			foreach($job_no_chunk_arr as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$jobCond.=" a.job_no in($chunk_arr_value) or ";
			}

			$search_cond.=" and (".chop($jobCond,'or ').")";
		}
		else
		{
			$search_cond=" and a.job_no in($job_no)";
		}
	}

	//echo $search_cond;die;

	if ($txt_style_no) $search_cond.=" and a.style_ref_no LIKE '%$txt_style_no%'";

	$cbo_item_category= str_replace("'","",$cbo_item_category);
	$cbo_product_category= str_replace("'","",$cbo_product_category);

	$cbo_product_category_cond = "";
	if($cbo_product_category) $cbo_product_category_cond = " and a.product_category=$cbo_product_category";

	$cbo_location_id= str_replace("'","",$cbo_location_id);
	$location_cond = "";

	//if($cbo_location_id>0) $location_cond = " and c.location_id=$cbo_location_id";

	if($cbo_location_id>0) $location_cond = " and e.location_id=$cbo_location_id";

	
	ob_start();

	$product_array=array();
	$sql_product="select id, color from product_details_master where item_category_id=2 and status_active=1 and is_deleted=0";
	$sql_product_result=sql_select($sql_product);
	foreach( $sql_product_result as $row )
	{
		$product_array[$row[csf('id')]]=$row[csf('color')];
	}
	unset($sql_product_result);


	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	?>
		<fieldset style="width:1630px;">
			<table cellpadding="0" cellspacing="0" width="910">
				<tr class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="11" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="11" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="11" style="font-size:14px"><strong> <? echo "Date : ".change_date_format(str_replace("'","",$txt_date_from));?></strong> To <strong> <? echo change_date_format(str_replace("'","",$txt_date_to));?></strong></td>
				</tr>
			</table>
			<table width="1150" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th colspan="6">Received Summary</th>
						<th colspan="4">Issued Summary</th>
						<th rowspan="2">Balance</th>
					</tr>
					<tr>
						<th width="100">Date</th>
						<th width="120">Buyer</th>
                        <th width="100">Trans In</th>
                        <th width="100">Issue Return</th>
                        <th width="100">Receive Qty</th>
						<th width="100">Total Received</th>

                       	<th width="100">Trans Out</th>
                       	<th width="100">Receive Return</th>
                       	<th width="100">Issue Qnty</th>
						<th width="100">Total Issued</th>


					</tr>
				</thead>
			</table>
			<div style="width:1168px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="1150" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
					<?

					$sql_query = "SELECT c.transaction_date,a.buyer_name, c.id as trans_id,
					(case when c.transaction_type = 1 then d.quantity else 0 end) as receive_qnty,
					(case when c.transaction_type=2 then d.quantity else 0 end) as issue_qnty,
					(case when c.transaction_type=3 then d.quantity else 0 end) as rec_ret_qnty,
					(case when c.transaction_type =4 then d.quantity else 0 end) as issue_ret_qnty,
					(case when c.transaction_type=5 then d.quantity else 0 end) as rec_trns_qnty,
					(case when c.transaction_type =6 then d.quantity else 0 end) as issue_trns_qnty, d.id

					from  wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d, lib_store_location e
					where a.job_no=b.job_no_mst and c.id=d.trans_id and d.po_breakdown_id=b.id and c.store_id=e.id and a.status_active=1 and a.is_deleted=0
					and d.entry_form in (7,37,66,68,15,18,71,126,134,17,19,195,196,46,52,202,209,258) and c.item_category=$cbo_item_category and c.status_active=1 and c.is_deleted=0  and d.trans_id!=0 and d.status_active=1 and d.is_deleted=0 and a.company_name='$cbo_company_id' and c.transaction_date between $txt_date_from and $txt_date_to $buyer_id_cond $cbo_product_category_cond $search_cond $location_cond order by c.transaction_date,a.buyer_name";

					// echo $sql_query;

					$nameArray=sql_select($sql_query);
					$transIdChkArr = array();
					foreach ($nameArray as $val)
					{
						if($transIdChkArr[$val[csf("id")]] =="")
						{
							$transIdChkArr[$val[csf("id")]] = $val[csf("id")];
							$dataArray[$val[csf("transaction_date")]][$val[csf("buyer_name")]]["receive_qnty"] += $val[csf("receive_qnty")];
							$dataArray[$val[csf("transaction_date")]][$val[csf("buyer_name")]]["issue_qnty"] += $val[csf("issue_qnty")];
							$dataArray[$val[csf("transaction_date")]][$val[csf("buyer_name")]]["rec_ret_qnty"] += $val[csf("rec_ret_qnty")];
							$dataArray[$val[csf("transaction_date")]][$val[csf("buyer_name")]]["issue_ret_qnty"] += $val[csf("issue_ret_qnty")];
							$dataArray[$val[csf("transaction_date")]][$val[csf("buyer_name")]]["rec_trns_qnty"] += $val[csf("rec_trns_qnty")];
							$dataArray[$val[csf("transaction_date")]][$val[csf("buyer_name")]]["issue_trns_qnty"] += $val[csf("issue_trns_qnty")];
						}
					}

					$trans_date_row_span_arr = array();
					foreach ($dataArray as $transaction_date => $transaction_data)
					{
						$buyer_td_span = 0;
						foreach ($transaction_data as $buyer_id => $row)
						{
							$buyer_td_span++;
						}
						$trans_date_row_span_arr[$transaction_date]=$buyer_td_span;
					}

					$i=1;
					$grand_total_rcv=$grand_rec_trns=$grand_issue_ret=$grand_receive=$grand_total_iss=$grand_issue_trns=$grand_rec_ret=$grand_issue=0;

					foreach ($dataArray as $transaction_date => $transaction_data)
					{
						$y=1;
						foreach ($transaction_data as $buyer_id => $row)
						{
							$trans_date_td_span =$trans_date_row_span_arr[$transaction_date];
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<?
								if ($y==1)
								{
								?>
								<td width="100" rowspan="<? echo $trans_date_td_span;?>" align="center"><p style="word-break: break-all;"><? echo change_date_format($transaction_date); ?></p></td>
								<?
								}
								$total_rcv =  $row["rec_trns_qnty"]+$row["issue_ret_qnty"]+$row["receive_qnty"];
								$total_iss =  $row["issue_trns_qnty"]+$row["rec_ret_qnty"]+$row["issue_qnty"];
								$balance = $total_rcv- $total_iss;
								?>
								<td width="120" align="right"><p><? echo $buyer_arr[$buyer_id]; ?></p></td>
								<td width="100" align="right"><p><? echo number_format($row["rec_trns_qnty"]);?></p></td>
								<td width="100" align="right"><p><? echo number_format($row["issue_ret_qnty"]);?></p></td>
								<td width="100" align="right"><p><? echo number_format($row["receive_qnty"]);?></p></td>
								<td width="100" align="right"><p><? echo number_format($total_rcv);?></p></td>
								<td width="100" align="right"><p><? echo number_format($row["issue_trns_qnty"]);?></p></td>
								<td width="100" align="right"><p><? echo number_format($row["rec_ret_qnty"]);?></p></td>
								<td width="100" align="right"><p><? echo number_format($row["issue_qnty"]);?></p></td>
								<td width="100" align="right"><p><? echo number_format($total_iss);?></p></td>
								<td width="" align="right"><p><? echo number_format($balance);?></p></td>

							</tr>
							<?
							$i++;$y++;

							$grand_total_rcv +=  $total_rcv;
							$grand_rec_trns+= $row["rec_trns_qnty"];
							$grand_issue_ret +=$row["issue_ret_qnty"];
							$grand_receive+=$row["receive_qnty"];
							$grand_total_iss +=$total_iss;
							$grand_issue_trns +=$row["issue_trns_qnty"];
							$grand_rec_ret+=$row["rec_ret_qnty"];
							$grand_issue+=$row["issue_qnty"];
							$grand_balance +=$balance;
						}
					}

					?>
				</table>
				<table width="1150" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
					<tfoot>
						<th width="220" colspan="">Grand Total</th>
						<th width="100" align="right"><? echo number_format($grand_rec_trns);?></th>
						<th width="100" align="right"><? echo number_format($grand_issue_ret);?></th>
						<th width="100" align="right"><? echo number_format($grand_receive);?></th>
						<th width="100" align="right"><? echo number_format($grand_total_rcv);?></th>
						<th width="100" align="right"><? echo number_format($grand_issue_trns);?></th>
						<th width="100" align="right"><? echo number_format($grand_rec_ret);?></th>
						<th width="100" align="right"><? echo number_format($grand_issue);?></th>
						<th width="100" align="right"><? echo number_format($grand_total_iss);?></th>
						<th width="" align="right"><? echo number_format($grand_balance);?></th>
					</tfoot>
				</table>
			</div>
		</fieldset>
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
    echo "$html**$filename";
    exit();
}

if($action=="report_generate2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_id= str_replace("'","",$cbo_company_id);
	$txt_style_no= trim(str_replace("'","",$txt_style_no));

	if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond=" and a.buyer_name=$cbo_buyer_id";

	$job_no=trim(str_replace("'","",$txt_job_no_show));
	$search_cond='';


	if($job_no)
	{
		$job_no = "'".implode("','",array_filter(array_unique(explode(",",$job_no))))."'";
		$jobCond=""; $job_no_arr=explode(",",$job_no);
		if($db_type==2 && count($job_no_arr)>999)
		{
			$job_no_chunk_arr=array_chunk($job_no_arr,999) ;
			foreach($job_no_chunk_arr as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$jobCond.=" a.job_no in($chunk_arr_value) or ";
			}

			$search_cond.=" and (".chop($jobCond,'or ').")";
		}
		else
		{
			$search_cond=" and a.job_no in($job_no)";
		}
	}

	//echo $search_cond;die;

	if ($txt_style_no) $search_cond.=" and a.style_ref_no LIKE '%$txt_style_no%'";

	$cbo_item_category= str_replace("'","",$cbo_item_category);
	$cbo_product_category= str_replace("'","",$cbo_product_category);

	$cbo_product_category_cond = "";
	if($cbo_product_category) $cbo_product_category_cond = " and a.product_category=$cbo_product_category";

	$cbo_location_id= str_replace("'","",$cbo_location_id);
	$location_cond = "";

	//if($cbo_location_id>0) $location_cond = " and c.location_id=$cbo_location_id";

	if($cbo_location_id>0) $location_cond = " and e.location_id=$cbo_location_id";

	
	ob_start();

	$product_array=array();
	$sql_product="select id, color from product_details_master where item_category_id=2 and status_active=1 and is_deleted=0";
	$sql_product_result=sql_select($sql_product);
	foreach( $sql_product_result as $row )
	{
		$product_array[$row[csf('id')]]=$row[csf('color')];
	}
	unset($sql_product_result);


	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	?>
	<fieldset style="width:1130px;">
		<table cellpadding="0" cellspacing="0" width="910">
			<tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="11" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="11" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="11" style="font-size:14px"><strong> <? echo "Date : ".change_date_format(str_replace("'","",$txt_date_from));?></strong> To <strong> <? echo change_date_format(str_replace("'","",$txt_date_to));?></strong></td>
			</tr>
		</table>
		<table width="1050" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
			<thead>
				<tr>
					<th colspan="6">Received Summary</th>
					<th colspan="4">Issued Summary</th>
				</tr>
				<tr>
					<th width="100">Date</th>
					<th width="120">Buyer</th>
                    <th width="100">Trans In</th>
                    <th width="100">Issue Return</th>
                    <th width="100">Receive Qty</th>
					<th width="100">Total Received</th>

                   	<th width="100">Trans Out</th>
                   	<th width="100">Receive Return</th>
                   	<th width="100">Issue Qnty</th>
					<th width="">Total Issued</th>
				</tr>
			</thead>
		</table>
		<div style="width:1068px; max-height:350px; overflow-y:scroll;" id="scroll_body">
			<table width="1050" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
				<?
				if ($job_no!="" || $cbo_product_category!=0 || $txt_style_no!="")
				{
					$sql_query = "SELECT c.transaction_date,a.buyer_name, c.id as trans_id,
					(case when c.transaction_type = 1 then d.quantity else 0 end) as receive_qnty,
					(case when c.transaction_type=2 then d.quantity else 0 end) as issue_qnty,
					(case when c.transaction_type=3 then d.quantity else 0 end) as rec_ret_qnty,
					(case when c.transaction_type =4 then d.quantity else 0 end) as issue_ret_qnty,
					(case when c.transaction_type=5 then d.quantity else 0 end) as rec_trns_qnty,
					(case when c.transaction_type =6 then d.quantity else 0 end) as issue_trns_qnty, d.id

					from  wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d, lib_store_location e
					where a.job_no=b.job_no_mst and c.id=d.trans_id and d.po_breakdown_id=b.id and c.store_id=e.id and a.status_active=1 and a.is_deleted=0
					and d.entry_form in (7,37,14,66,68,15,18,71,126,134,17,19,195,196,46,52) and c.item_category=$cbo_item_category and c.status_active=1 and c.is_deleted=0  and d.trans_id!=0 and d.status_active=1 and d.is_deleted=0 and a.company_name='$cbo_company_id' and c.transaction_date between $txt_date_from and $txt_date_to $buyer_id_cond $cbo_product_category_cond $search_cond $location_cond
					order by c.transaction_date,a.buyer_name";
				}
				else
				{
					$sql_query = "SELECT c.transaction_date,a.buyer_name, c.id as trans_id,
					(case when c.transaction_type = 1 then d.quantity else 0 end) as receive_qnty,
					(case when c.transaction_type=2 then d.quantity else 0 end) as issue_qnty,
					(case when c.transaction_type=3 then d.quantity else 0 end) as rec_ret_qnty,
					(case when c.transaction_type =4 then d.quantity else 0 end) as issue_ret_qnty,
					(case when c.transaction_type=5 then d.quantity else 0 end) as rec_trns_qnty,
					(case when c.transaction_type =6 then d.quantity else 0 end) as issue_trns_qnty, d.id

					from  wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d, lib_store_location e
					where a.job_no=b.job_no_mst and c.id=d.trans_id and d.po_breakdown_id=b.id and c.store_id=e.id and a.status_active=1 and a.is_deleted=0
					and d.entry_form in (7,37,14,66,68,15,18,71,126,134,17,19,195,196,46,52) and c.item_category=$cbo_item_category and c.status_active=1 and c.is_deleted=0  and d.trans_id!=0 and d.status_active=1 and d.is_deleted=0 and a.company_name='$cbo_company_id' and c.transaction_date between $txt_date_from and $txt_date_to $buyer_id_cond $cbo_product_category_cond $search_cond $location_cond 
					UNION ALL
					SELECT c.transaction_date,d.buyer_id as buyer_name, c.id as trans_id,
					(case when c.transaction_type = 1 then c.cons_quantity else 0 end) as receive_qnty, 
					(case when c.transaction_type=2 then c.cons_quantity else 0 end) as issue_qnty, 
					(case when c.transaction_type=3 then c.cons_quantity else 0 end) as rec_ret_qnty, 
					(case when c.transaction_type =4 then c.cons_quantity else 0 end) as issue_ret_qnty, 
					(case when c.transaction_type=5 then c.cons_quantity else 0 end) as rec_trns_qnty, 
					(case when c.transaction_type =6 then c.cons_quantity else 0 end) as issue_trns_qnty, c.id

					FROM inv_transaction c, pro_batch_create_mst f, wo_non_ord_samp_booking_mst d, lib_store_location e
					WHERE c.company_id=3 and c.pi_wo_batch_no = f.id and f.booking_no=d.booking_no and c.store_id=e.id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.item_category=2
					 and c.company_id='$cbo_company_id' and c.transaction_date between $txt_date_from and $txt_date_to $buyer_id_cond $search_cond $location_cond 
					order by transaction_date,buyer_name"; //order by c.transaction_date,a.buyer_name
					//and d.booking_no='OG-SMN-22-00065'
				}
				

				// echo $sql_query;die;

				$nameArray=sql_select($sql_query);
				$transIdChkArr = array();
				foreach ($nameArray as $val)
				{
					if($transIdChkArr[$val[csf("id")]] =="")
					{
						$transIdChkArr[$val[csf("id")]] = $val[csf("id")];
						$dataArray[$val[csf("transaction_date")]][$val[csf("buyer_name")]]["receive_qnty"] += $val[csf("receive_qnty")];
						$dataArray[$val[csf("transaction_date")]][$val[csf("buyer_name")]]["issue_qnty"] += $val[csf("issue_qnty")];
						$dataArray[$val[csf("transaction_date")]][$val[csf("buyer_name")]]["rec_ret_qnty"] += $val[csf("rec_ret_qnty")];
						$dataArray[$val[csf("transaction_date")]][$val[csf("buyer_name")]]["issue_ret_qnty"] += $val[csf("issue_ret_qnty")];
						$dataArray[$val[csf("transaction_date")]][$val[csf("buyer_name")]]["rec_trns_qnty"] += $val[csf("rec_trns_qnty")];
						$dataArray[$val[csf("transaction_date")]][$val[csf("buyer_name")]]["issue_trns_qnty"] += $val[csf("issue_trns_qnty")];
					}
				}

				$trans_date_row_span_arr = array();
				foreach ($dataArray as $transaction_date => $transaction_data)
				{
					$buyer_td_span = 0;
					foreach ($transaction_data as $buyer_id => $row)
					{
						$buyer_td_span++;
					}
					$trans_date_row_span_arr[$transaction_date]=$buyer_td_span;
				}

				$i=1;
				$grand_total_rcv=$grand_rec_trns=$grand_issue_ret=$grand_receive=$grand_total_iss=$grand_issue_trns=$grand_rec_ret=$grand_issue=0;

				foreach ($dataArray as $transaction_date => $transaction_data)
				{
					$y=1;
					foreach ($transaction_data as $buyer_id => $row)
					{
						$trans_date_td_span =$trans_date_row_span_arr[$transaction_date];
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							<?
							if ($y==1)
							{
							?>
							<td width="100" rowspan="<? echo $trans_date_td_span;?>" align="center"><p style="word-break: break-all;"><? echo change_date_format($transaction_date); ?></p></td>
							<?
							}
							$total_rcv =  $row["rec_trns_qnty"]+$row["issue_ret_qnty"]+$row["receive_qnty"];
							$total_iss =  $row["issue_trns_qnty"]+$row["rec_ret_qnty"]+$row["issue_qnty"];
							// $balance = $total_rcv- $total_iss;
							?>
							<td width="120" align="right"><p><? echo $buyer_arr[$buyer_id]; ?></p></td>
							<td width="100" align="right"><p><? echo number_format($row["rec_trns_qnty"]);?></p></td>
							<td width="100" align="right"><p><? echo number_format($row["issue_ret_qnty"]);?></p></td>
							<td width="100" align="right"><p><? echo number_format($row["receive_qnty"]);?></p></td>
							<td width="100" align="right"><p><? echo number_format($total_rcv);?></p></td>
							<td width="100" align="right"><p><? echo number_format($row["issue_trns_qnty"]);?></p></td>
							<td width="100" align="right"><p><? echo number_format($row["rec_ret_qnty"]);?></p></td>
							<td width="100" align="right"><p><? echo number_format($row["issue_qnty"]);?></p></td>
							<td width="" align="right"><p><? echo number_format($total_iss);?></p></td>
						</tr>
						<?
						$i++;$y++;

						$grand_total_rcv +=  $total_rcv;
						$grand_rec_trns+= $row["rec_trns_qnty"];
						$grand_issue_ret +=$row["issue_ret_qnty"];
						$grand_receive+=$row["receive_qnty"];
						$grand_total_iss +=$total_iss;
						$grand_issue_trns +=$row["issue_trns_qnty"];
						$grand_rec_ret+=$row["rec_ret_qnty"];
						$grand_issue+=$row["issue_qnty"];
						// $grand_balance +=$balance;
					}
				}

				?>
			</table>
			<table width="1050" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
				<tfoot>
					<th width="220" colspan="">Grand Total</th>
					<th width="100" align="right"><? echo number_format($grand_rec_trns);?></th>
					<th width="100" align="right"><? echo number_format($grand_issue_ret);?></th>
					<th width="100" align="right"><? echo number_format($grand_receive);?></th>
					<th width="100" align="right"><? echo number_format($grand_total_rcv);?></th>
					<th width="100" align="right"><? echo number_format($grand_issue_trns);?></th>
					<th width="100" align="right"><? echo number_format($grand_rec_ret);?></th>
					<th width="100" align="right"><? echo number_format($grand_issue);?></th>
					<th width="" align="right"><? echo number_format($grand_total_iss);?></th>
				</tfoot>
			</table>
		</div>
	</fieldset>
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
    echo "$html**$filename";
    exit();
}

if($action == "generate_details_report")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));

    $cbo_company_id= str_replace("'","",$cbo_company_id);
    $txt_style_no= trim(str_replace("'","",$txt_style_no));
	$cbo_year = trim(str_replace("'", "", $cbo_year_selection));
    //var_dump($cbo_buyer_id); echo "<br/><br/><br/><br/>";

    if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond=" and a.buyer_name=$cbo_buyer_id";

    $job_no=trim(str_replace("'","",$txt_job_no_show));
    $search_cond='';


    if($job_no)
    {
        $job_no = "'".implode("','",array_filter(array_unique(explode(",",$job_no))))."'";
        $jobCond=""; $job_no_arr=explode(",",$job_no);
        if($db_type==2 && count($job_no_arr)>999)
        {
            $job_no_chunk_arr=array_chunk($job_no_arr,999) ;
            foreach($job_no_chunk_arr as $chunk_arr)
            {
                $chunk_arr_value=implode(",",$chunk_arr);
                $jobCond.=" a.job_no in($chunk_arr_value) or ";
            }

            $search_cond.=" and (".chop($jobCond,'or ').")";
        }
        else
        {
            $search_cond=" and a.job_no in($job_no)";
        }
    }

    //echo $search_cond;die;

    if ($txt_style_no) $search_cond.=" and a.style_ref_no LIKE '%$txt_style_no%'";

    $cbo_item_category= str_replace("'","",$cbo_item_category);
    $cbo_product_category= str_replace("'","",$cbo_product_category);

    $cbo_product_category_cond = "";
    if($cbo_product_category) $cbo_product_category_cond = " and a.product_category=$cbo_product_category";

    $cbo_location_id= str_replace("'","",$cbo_location_id);
	$location_cond = "";
	if($cbo_location_id>0) $location_cond = " and e.location_id=$cbo_location_id";


    /*$product_array=array();
    $sql_product="select id, color, unit_of_measure from product_details_master where item_category_id=2 and status_active=1 and is_deleted=0";
    //$sql_product="select id, item_category_id, color, unit_of_measure from product_details_master where item_category_id in(2,3) and status_active=1 and is_deleted=0";
    $sql_product_result=sql_select($sql_product);
    foreach( $sql_product_result as $row )
    {
        $product_array[$row[csf('id')]]=$row[csf('color')];
    }
    unset($sql_product_result);*/

	ob_start();
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

	$sql_query = "SELECT c.transaction_date, a.buyer_name, c.id as trans_id, c.prod_id,	p.unit_of_measure,
	(case when c.transaction_type = 1 then d.quantity else 0 end) as receive_qnty,
	(case when c.transaction_type=2 then d.quantity else 0 end) as issue_qnty,
	(case when c.transaction_type=3 then d.quantity else 0 end) as rec_ret_qnty,
	(case when c.transaction_type =4 then d.quantity else 0 end) as issue_ret_qnty,
	(case when c.transaction_type=5 then d.quantity else 0 end) as rec_trns_qnty,
	(case when c.transaction_type =6 then d.quantity else 0 end) as issue_trns_qnty, d.id
	from  wo_po_details_master a, wo_po_break_down b, order_wise_pro_details d, inv_transaction c, product_details_master p, lib_store_location e
	where a.job_no=b.job_no_mst and b.id=d.po_breakdown_id and d.trans_id=c.id and c.prod_id=p.id and c.store_id=e.id and d.entry_form in (7,37,66,68,15,18,71,126,134,17,19,195,196,46,52) and c.item_category = $cbo_item_category and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_name='$cbo_company_id' and c.transaction_date between $txt_date_from and $txt_date_to $buyer_id_cond $cbo_product_category_cond $search_cond $location_cond
	order by c.transaction_date, c.id, a.buyer_name";
	// echo $sql_query;//die;

	$nameArray=sql_select($sql_query);
	$transIdChkArr = array();
	foreach ($nameArray as $val)
	{
		$all_buyer[$val[csf("buyer_name")]]=$val[csf("buyer_name")];
		if($transIdChkArr[$val[csf("id")]] =="")
		{
			$transIdChkArr[$val[csf("id")]] = $val[csf("id")];
			$dataArray[date("F",strtotime($val[csf("transaction_date")]))][$val[csf("transaction_date")]][$val[csf("unit_of_measure")]][$val[csf("buyer_name")]]["receive_qnty"] += $val[csf("receive_qnty")];
			$dataArray[date("F",strtotime($val[csf("transaction_date")]))][$val[csf("transaction_date")]][$val[csf("unit_of_measure")]][$val[csf("buyer_name")]]["issue_qnty"] += $val[csf("issue_qnty")];
			$dataArray[date("F",strtotime($val[csf("transaction_date")]))][$val[csf("transaction_date")]][$val[csf("unit_of_measure")]][$val[csf("buyer_name")]]["rec_ret_qnty"] += $val[csf("rec_ret_qnty")];
			$dataArray[date("F",strtotime($val[csf("transaction_date")]))][$val[csf("transaction_date")]][$val[csf("unit_of_measure")]][$val[csf("buyer_name")]]["issue_ret_qnty"] += $val[csf("issue_ret_qnty")];
			$dataArray[date("F",strtotime($val[csf("transaction_date")]))][$val[csf("transaction_date")]][$val[csf("unit_of_measure")]][$val[csf("buyer_name")]]["rec_trns_qnty"] += $val[csf("rec_trns_qnty")];
			$dataArray[date("F",strtotime($val[csf("transaction_date")]))][$val[csf("transaction_date")]][$val[csf("unit_of_measure")]][$val[csf("buyer_name")]]["issue_trns_qnty"] += $val[csf("issue_trns_qnty")];
			//$dataArray[date("F",strtotime($val[csf("transaction_date")]))][$val[csf("transaction_date")]]["uom"] = $val[csf("unit_of_measure")];


			$YeardataArray[date("Y",strtotime($val[csf("transaction_date")]))][date("F",strtotime($val[csf("transaction_date")]))][$val[csf("unit_of_measure")]][$val[csf("buyer_name")]]["receive_qnty"] += $val[csf("receive_qnty")];
			$YeardataArray[date("Y",strtotime($val[csf("transaction_date")]))][date("F",strtotime($val[csf("transaction_date")]))][$val[csf("unit_of_measure")]][$val[csf("buyer_name")]]["issue_qnty"] += $val[csf("issue_qnty")];
			$YeardataArray[date("Y",strtotime($val[csf("transaction_date")]))][date("F",strtotime($val[csf("transaction_date")]))][$val[csf("unit_of_measure")]][$val[csf("buyer_name")]]["rec_ret_qnty"] += $val[csf("rec_ret_qnty")];
			$YeardataArray[date("Y",strtotime($val[csf("transaction_date")]))][date("F",strtotime($val[csf("transaction_date")]))][$val[csf("unit_of_measure")]][$val[csf("buyer_name")]]["issue_ret_qnty"] += $val[csf("issue_ret_qnty")];
			$YeardataArray[date("Y",strtotime($val[csf("transaction_date")]))][date("F",strtotime($val[csf("transaction_date")]))][$val[csf("unit_of_measure")]][$val[csf("buyer_name")]]["rec_trns_qnty"] += $val[csf("rec_trns_qnty")];
			$YeardataArray[date("Y",strtotime($val[csf("transaction_date")]))][date("F",strtotime($val[csf("transaction_date")]))][$val[csf("unit_of_measure")]][$val[csf("buyer_name")]]["issue_trns_qnty"] += $val[csf("issue_trns_qnty")];
		}
	}
	//echo count($all_buyer);
	//echo "<pre>";print_r($YeardataArray);echo "</pre>";die;

	$div_width=770+(count($all_buyer)*480);
	$tbl_width=750+(count($all_buyer)*480);
	$col_span=7+(count($all_buyer)*3*2);
    ?>

	<div style="width:<? echo $div_width;?>px;" id="table_header" align="left">
        <table width="<? echo $tbl_width;?>" cellpadding="5" cellspacing="0" border="0" rules="all" id="caption" align="center" >
			<tbody>
	            <tr>
	                <td colspan="<? echo $col_span;?>" align="center">
						<strong style="font-size:18px; height: 15px; text-align: center; padding:8px;"><? echo $report_title;?></strong>
					</td>
	            </tr>
	            <tr>
	                <td colspan="<? echo $col_span;?>"  align="center" style="font-size:16px; height: 15px; text-align: center; padding:8px;">
						<strong><? echo $company_arr[str_replace("'","", $cbo_company_id)]; ?></strong>
					</td>
	            </tr>
	            <tr>
	                <td colspan="<? echo $col_span;?>" align="center" style="font-size:14px; height: 15px; text-align: center; padding:8px;">
						<strong> <? echo "Date : ".change_date_format(str_replace("'","",$txt_date_from));?></strong> To
						<strong> <? echo change_date_format(str_replace("'","",$txt_date_to));?></strong>
					</td>
	            </tr>
			</tbody>
        </table>
		<div style="width:<? echo $div_width;?>px;">
		        <table width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0"  border="1" rules="all" class="rpt_table">
					<thead>
		            <tr>
		                <th colspan="<? echo 4+(count($all_buyer)*3);?>">Date Wise Received Details</th>
		                <th colspan="<? echo 3+(count($all_buyer)*3);?>">Date Wise Issued Details</th>
		            </tr>
		            <tr>
		                <th width="100" rowspan="2">Month</th>
		                <th width="100" rowspan="2">Date</th>
		                <th width="100" rowspan="2">UOM</th>
		                <?
		                foreach ($all_buyer as $buyer){
							?>
							<th colspan="3"><? echo $buyer_arr[$buyer]; ?></th>
							<?
		                }
		                ?>
		                <th width="100" rowspan="2">Total Received Quantity</th>
		                <?
		                foreach ($all_buyer as $buyer){
							?>
							<th colspan="3"><? echo $buyer_arr[$buyer]; ?></th>
							<?

						}
		                ?>
		                <th width="100" rowspan="2">Total Issued Quantity</th>
		                <th width="100" rowspan="2">Stock Quantity</th>
		                <th rowspan="2">Remarks</th>
		            </tr>
		            <tr>
		            	<?
		                foreach ($all_buyer as $buyer){
							?>
							<th width="80">Trans In</th>
		                    <th width="80">Issue Return</th>
		                    <th width="80">Rcv Qnty</th>
							<?
		                }
		                foreach ($all_buyer as $buyer){
							?>
							<th width="80">Trans Out</th>
		                    <th width="80">Receive Return</th>
		                    <th width="80">Issue Qnty</th>
							<?
						}
		                ?>
		            </tr>
		            </thead>
		        </table>

		        <div style="width:<? echo $div_width;?>px; max-height:350px; overflow-y:scroll;" id="scroll_body">
		            <table width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
		            	<tbody>
		                <?
		                    $i=1; $j=1;
		                    $grand_total_rcv=$grand_rec_trns=$grand_issue_ret=$grand_receive=$grand_total_iss=$grand_issue_trns=$grand_rec_ret=$grand_issue=0;
							foreach ($dataArray as $month_name => $month_data)
							{
								foreach ($month_data as $transaction_date => $transaction_data)
								{
									foreach ($transaction_data as $uom => $uom_data)
									{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="100" align="center"><p><? echo $month_name; ?></p></td>
											<td width="100" align="center"><p><? echo change_date_format($transaction_date); ?></p></td>
											<td width="100" align="center"><p><? echo $unit_of_measurement[$uom]; ?></p></td>

											<?
											$all_receive=0;
											foreach ($all_buyer as $buyer){
												?>
												<td width="80" align="right"><? echo number_format($uom_data[$buyer]["rec_trns_qnty"],2)?></td>
												<td width="80" align="right"><? echo number_format($uom_data[$buyer]["issue_ret_qnty"],2)?></td>
												<td width="80" align="right"><? echo number_format($uom_data[$buyer]["receive_qnty"],2)?></td>
												<?
												$month_total[$buyer]["rec_trns_qnty"]+=$uom_data[$buyer]["rec_trns_qnty"];
												$month_total[$buyer]["issue_ret_qnty"]+=$uom_data[$buyer]["issue_ret_qnty"];
												$month_total[$buyer]["receive_qnty"]+=$uom_data[$buyer]["receive_qnty"];

												$grand_tot_data[$buyer]["rec_trns_qnty"]+=$uom_data[$buyer]["rec_trns_qnty"];
												$grand_tot_data[$buyer]["issue_ret_qnty"]+=$uom_data[$buyer]["issue_ret_qnty"];
												$grand_tot_data[$buyer]["receive_qnty"]+=$uom_data[$buyer]["receive_qnty"];

												$year_total[$month_name][$buyer]["rec_trns_qnty"]+=$uom_data[$buyer]["rec_trns_qnty"];
												$year_total[$month_name][$buyer]["issue_ret_qnty"]+=$uom_data[$buyer]["issue_ret_qnty"];
												$year_total[$month_name][$buyer]["receive_qnty"]+=$uom_data[$buyer]["receive_qnty"];

												$all_receive+=$uom_data[$buyer]["rec_trns_qnty"]+$uom_data[$buyer]["issue_ret_qnty"]+$uom_data[$buyer]["receive_qnty"];
											}
											?>
											<td width="100" align="right"><? echo number_format($all_receive,2); $monthly_rcv+=$all_receive; $grant_tot_rcv+=$all_receive;?></td>
											<?
											$all_issue=0;
											foreach ($all_buyer as $buyer){
												?>
												<td width="80" align="right"><? echo number_format($uom_data[$buyer]["issue_trns_qnty"],2)?></td>
												<td width="80" align="right"><? echo number_format($uom_data[$buyer]["rec_ret_qnty"],2)?></td>
												<td width="80" align="right"><? echo number_format($uom_data[$buyer]["issue_qnty"],2)?></td>
												<?
												$month_total[$buyer]["issue_trns_qnty"]+=$uom_data[$buyer]["issue_trns_qnty"];
												$month_total[$buyer]["rec_ret_qnty"]+=$uom_data[$buyer]["rec_ret_qnty"];
												$month_total[$buyer]["issue_qnty"]+=$uom_data[$buyer]["issue_qnty"];

												$grand_tot_data[$buyer]["issue_trns_qnty"]+=$uom_data[$buyer]["issue_trns_qnty"];
												$grand_tot_data[$buyer]["rec_ret_qnty"]+=$uom_data[$buyer]["rec_ret_qnty"];
												$grand_tot_data[$buyer]["issue_qnty"]+=$uom_data[$buyer]["issue_qnty"];

												$year_total[$month_name][$buyer]["issue_trns_qnty"]+=$uom_data[$buyer]["issue_trns_qnty"];
												$year_total[$month_name][$buyer]["rec_ret_qnty"]+=$uom_data[$buyer]["rec_ret_qnty"];
												$year_total[$month_name][$buyer]["issue_qnty"]+=$uom_data[$buyer]["issue_qnty"];

												$all_issue+=$uom_data[$buyer]["issue_trns_qnty"]+$uom_data[$buyer]["rec_ret_qnty"]+$uom_data[$buyer]["issue_qnty"];
											}

											$stock=$all_receive-$all_issue;
											?>
											<td width="100" align="right"><? echo number_format($all_issue,2); $monthly_issue+=$all_issue; $grant_tot_issue+=$all_issue;?></td>
											<td width="100" align="right"><? echo number_format($stock,2); $monthly_stock+=$stock; $grant_tot_stock+=$stock;?></td>
											<td><p><? ?></p></td>

										</tr>

										<?
										$i++;$y++;
									}
								}

								 $bgcolor_tot="#C1D099";
								?>
		                        <tr bgcolor="<? echo $bgcolor_tot;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>" style="font-weight:bold;">
		                            <td colspan="3" align="right" style=" padding:3px 0;"><p><? echo $month_name." Sub Total="; ?></p></td>
		                            <?
		                            foreach ($all_buyer as $buyer){
										?>
										<td width="80" align="right" style=" padding:3px 0;"><? echo number_format($month_total[$buyer]["rec_trns_qnty"])?></td>
										<td width="80" align="right" style=" padding:3px 0;"><? echo number_format($month_total[$buyer]["issue_ret_qnty"])?></td>
										<td width="80" align="right" style=" padding:3px 0;"><? echo number_format($month_total[$buyer]["receive_qnty"])?></td>
										<?
		                            }
		                            ?>
		                            <td width="100" align="right" style=" padding:3px 0;"><? echo number_format($monthly_rcv);?></td>
		                            <?
		                            foreach ($all_buyer as $buyer){
										?>
										<td width="80" align="right" style=" padding:3px 0;"><? echo number_format($month_total[$buyer]["issue_trns_qnty"])?></td>
										<td width="80" align="right" style=" padding:3px 0;"><? echo number_format($month_total[$buyer]["rec_ret_qnty"])?></td>
										<td width="80" align="right" style=" padding:3px 0;"><? echo number_format($month_total[$buyer]["issue_qnty"])?></td>
										<?
		                            }
		                            ?>
		                            <td width="100" align="right" style=" padding:3px 0;"><? echo number_format($monthly_issue)?></td>
		                            <td width="100" align="right" style=" padding:3px 0;"><? echo number_format($monthly_stock)?></td>
		                            <td><p><? ?></p></td>
		                        </tr>
		                        <?
								unset($month_total);$monthly_rcv=$monthly_issue=$monthly_stock="";
							}
							$grand_tot_bg_color = "#E7E7A2";
		                ?>

		                    	<tr bgcolor="<? echo $grand_tot_bg_color;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>" style="font-weight:bold;">
		                        	<td colspan="3" align="right" style=" padding: 5px 0;"><p><? echo " All Month Grand Total="; ?></p></td>
		                            <?
		                            foreach ($all_buyer as $buyer){
										?>
										<td width="80" align="right" style=" padding: 5px 0;"><? echo number_format($grand_tot_data[$buyer]["rec_trns_qnty"])?></td>
										<td width="80" align="right" style=" padding: 5px 0;"><? echo number_format($grand_tot_data[$buyer]["issue_ret_qnty"])?></td>
										<td width="80" align="right" style=" padding: 5px 0;"><? echo number_format($grand_tot_data[$buyer]["receive_qnty"])?></td>
										<?
		                            }
		                            ?>
		                            <td width="100" align="right" style=" padding: 5px 0;"><? echo number_format($grant_tot_rcv);?></td>
		                            <?
		                            foreach ($all_buyer as $buyer){
										?>
										<td width="80" align="right" style=" padding: 5px 0;"><? echo number_format($grand_tot_data[$buyer]["issue_trns_qnty"])?></td>
										<td width="80" align="right" style=" padding: 5px 0;"><? echo number_format($grand_tot_data[$buyer]["rec_ret_qnty"])?></td>
										<td width="80" align="right" style=" padding: 5px 0;"><? echo number_format($grand_tot_data[$buyer]["issue_qnty"])?></td>
										<?
		                            }
		                            ?>
		                            <td width="100" align="right" style=" padding: 5px 0;"><? echo number_format($grant_tot_issue)?></td>
		                            <td width="100" align="right" style=" padding: 5px 0;"><? echo number_format($grant_tot_stock)?></td>
		                            <td><p><? ?></p></td>
		                        </tr>
		            	</tbody>
		            </table>
		        </div>
		        <div class="rpt_table" style="margin-top: 30px;">
		            <table width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0"  border="1" rules="all" class="rpt_table">
		                <thead>
		                <tr>
		                    <th colspan="<? echo 4+(count($all_buyer)*3);?>">Month Wise Received Summary</th>
		                    <th colspan="<? echo 3+(count($all_buyer)*3);?>">Month Wise Issued Summary</th>
		                </tr>
		                <tr>
		                    <th width="100" rowspan="2">Month</th>
		                    <th width="100" rowspan="2">Year</th>
		                    <th width="100" rowspan="2">UOM</th>
		                    <?
		                    foreach ($all_buyer as $buyer){
		                        ?>
		                        <th colspan="3"><? echo $buyer_arr[$buyer]; ?></th>
		                        <?
		                    }
		                    ?>
		                    <th width="100" rowspan="2">Total Received Quantity</th>
		                    <?
		                    foreach ($all_buyer as $buyer){
		                        ?>
		                        <th colspan="3"><? echo $buyer_arr[$buyer]; ?></th>
		                        <?

		                    }
		                    ?>
		                    <th width="100" rowspan="2">Total Issued Quantity</th>
		                    <th width="100" rowspan="2">Stock Quantity</th>
		                    <th rowspan="2">Remarks</th>
		                </tr>
		                <tr>
		                    <?
		                    foreach ($all_buyer as $buyer){
		                        ?>
		                        <th width="80">Trans In</th>
		                        <th width="80">Issue Return</th>
		                        <th width="80">Rcv Qnty</th>
		                        <?
		                    }
		                    foreach ($all_buyer as $buyer){
		                        ?>
		                        <th width="80">Trans Out</th>
		                        <th width="80">Receive Return</th>
		                        <th width="80">Issue Qnty</th>
		                        <?
		                    }
		                    ?>
		                </tr>
		                </thead>
		            </table>

		            <div style="width:<? echo $div_width.'px;';?> max-height:350px; overflow-y:scroll;" id="scroll_body">
		                    <table width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body_id" >
		                        <tbody>
		                    <?
		                    $i=0;
		                        foreach($YeardataArray as $Year_name => $Year_data)
		                        {
									foreach($Year_data as $month_name => $month_data)
		                        	{
										foreach($month_data as $uom => $row)
		                        		{
											?>
											<tr bgcolor="<? echo $bgcolor?>" onClick="change_color('tr_<? echo $i;?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i;?>">
												<td align="center" width="100"><? echo $month_name; ?></td>
												<td align="center" width="100"><? echo $Year_name; ?></td>
												<td align="center" width="100"><? echo $unit_of_measurement[$uom]; ?></td>
											<?
											$monthwise_total_receive=0;
												foreach($all_buyer as $buyer){
											?>
													<td width="80" align="right"><? echo number_format($row[$buyer]["rec_trns_qnty"],2);?></td>
													<td width="80" align="right"><? echo number_format( $row[$buyer]["issue_ret_qnty"],2);?></td>
													<td width="80" align="right"><? echo number_format($row[$buyer]["receive_qnty"],2);?></td>
											<?
											$monthwise_total_receive += $row[$buyer]["rec_trns_qnty"]+$row[$buyer]["issue_ret_qnty"]+$row[$buyer]["receive_qnty"];
												}

											?>
											<td width="100" align="right"> <? echo number_format($monthwise_total_receive,2); ?></td>
											<?
											$monthwise_total_issue=0;
												foreach ($all_buyer as $buyer){

													?>
											<td width="80" align="right"><? echo number_format($row[$buyer]["issue_trns_qnty"],2);?></td>
											<td width="80" align="right"><? echo number_format($row[$buyer]["rec_ret_qnty"],2);?></td>
											<td width="80" align="right"><? echo number_format($row[$buyer]["issue_qnty"],2);?></td>
											<?
											$monthwise_total_issue += $row[$buyer]["issue_trns_qnty"]+$row[$buyer]["rec_ret_qnty"]+$row[$buyer]["issue_qnty"];
												}
												$stock=$monthwise_total_receive-$monthwise_total_issue;

											?>
											<td width="100" align="right">  <? echo number_format($monthwise_total_issue,2); ?></td>
											<td width="100" align="right"> <? echo number_format($stock,2); ?></td>
											<td> </td>

											</tr>
											<?
											$i++;
										}
									}
		                        }
		                    $grand_tot_bg_color = "#E7E7A2";
		                ?>

		                    	<tr bgcolor="<? echo $grand_tot_bg_color;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>" style="font-weight:bold;">
		                        	<td colspan="3" align="right" style=" padding: 5px 0;"><p><? echo " All Month Grand Total="; ?></p></td>
		                            <?
		                            foreach ($all_buyer as $buyer){
										?>
										<td width="80" align="right" style=" padding: 5px 0;"><? echo number_format($grand_tot_data[$buyer]["rec_trns_qnty"])?></td>
										<td width="80" align="right" style=" padding: 5px 0;"><? echo number_format($grand_tot_data[$buyer]["issue_ret_qnty"])?></td>
										<td width="80" align="right" style=" padding: 5px 0;"><? echo number_format($grand_tot_data[$buyer]["receive_qnty"])?></td>
										<?
		                            }
		                            ?>
		                            <td width="100" align="right" style=" padding: 5px 0;"><? echo number_format($grant_tot_rcv);?></td>
		                            <?
		                            foreach ($all_buyer as $buyer){
										?>
										<td width="80" align="right" style=" padding: 5px 0;"><? echo number_format($grand_tot_data[$buyer]["issue_trns_qnty"])?></td>
										<td width="80" align="right" style=" padding: 5px 0;"><? echo number_format($grand_tot_data[$buyer]["rec_ret_qnty"])?></td>
										<td width="80" align="right" style=" padding: 5px 0;"><? echo number_format($grand_tot_data[$buyer]["issue_qnty"])?></td>
										<?
		                            }
		                            ?>
		                            <td width="100" align="right" style=" padding: 5px 0;"><? echo number_format($grant_tot_issue)?></td>
		                            <td width="100" align="right" style=" padding: 5px 0;"><? echo number_format($grant_tot_stock)?></td>
		                            <td><p><? ?></p></td>
		                        </tr>
		                        </tbody>
		                    </table>

		            </div>
		        </div>

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
    echo "$html**$filename";
    exit();
}

if($action=="receive_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:570px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

			$(document).ready(function(e) {
				setFilterGrid('tbl_list_search',-1);
			});

		</script>
		<?
		ob_start();
		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="1625" cellpadding="0" cellspacing="0" align="center" >
				<thead>
					<tr>
						<th colspan="18">Receive Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="70">Receive Date</th>
						<th width="80">Dyeing Source</th>
						<th width="110">Dyeing Company</th>
						<th width="100">Challan No</th>
						<th width="80">Color</th>
						<th width="80">Batch No</th>
						<th width="60">Rack No</th>
						<th width="80">Grey Qty.</th>
						<th width="80">Fin. Rcv. Qty.</th>
                      <!--  <th width="80">Trans. In Qty.</th>-->
						<th width="70">Process Loss Qty.</th>
						<th width="60">QC ID</th>
						<th width="80">QC Name</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th>Collar/Cuff Pcs</th>
					</tr>
				</thead>
				<tbody id="tbl_list_search">
					<?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
					$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");

					$row_info_sql = "select dtls_id,entry_form,qc_pass_qnty,reject_qnty from pro_roll_details where status_active=1 and is_deleted=0 and entry_form in (66,68) and po_breakdown_id in($po_id)";
					$row_info = sql_select($row_info_sql);
					$roll_arr = array();
					foreach ($row_info as $roll_row) {
						$roll_arr[$roll_row[csf("dtls_id")]][$roll_row[csf("entry_form")]] += $roll_row[csf("qc_pass_qnty")]+$roll_row[csf("reject_qnty")];
					}

					$finish_production_info_sql = "select a.recv_number, sum(b.grey_used_qty)grey_used_qty,b.body_part_id,b.fabric_description_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7) and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id='$companyID' and b.order_id=$po_id group by a.recv_number,a.entry_form,b.body_part_id,b.fabric_description_id";
					$finish_production_info = sql_select($finish_production_info_sql);
					$finish_production = array();
					foreach ($finish_production_info as $fin_row) {
						$finish_production[$fin_row[csf("recv_number")]][$fin_row[csf("fabric_description_id")]][$fin_row[csf("body_part_id")]] += $fin_row[csf("grey_used_qty")];
					}
					$sql_transfer_in="
					select a.id, a.company_id,a.transfer_system_id as recv_number,a.challan_no as challan_no,a.transfer_date as receive_date, b.uom, b.from_prod_id,b.feb_description_id,b.to_rack as rack_no,b.gsm,b.dia_width,b.batch_id, sum(b.transfer_qnty) as quantity_in,c.prod_id,c.color_id from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(15,134) and c.trans_type=5 and c.color_id='$color' and a.transfer_criteria=4 and a.to_order_id in($po_id) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 group by a.id, a.company_id,a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id,b.feb_description_id,b.to_rack,b.gsm,b.dia_width,b.batch_id,a.challan_no,c.prod_id,c.color_id ";
					$transfer_in=sql_select($sql_transfer_in);
					/*foreach ($transfer_in as $row) {
						$trans_in_arr[$row[csf("recv_number")]][$row[csf("fabric_description_id")]][$row[csf("body_part_id")]] += $row[csf("grey_used_qty")];
					}*/

					$mrr_sql="select a.recv_number, a.booking_no,a.receive_date,a.knitting_source,a.knitting_company,a.challan_no,a.emp_id,a.qc_name,b.rack_no as rack_no, b.prod_id,b.batch_id,b.body_part_id,b.fabric_description_id,b.gsm,b.width, sum(c.quantity) as quantity, sum(c.returnable_qnty) as returnable_qnty,c.color_id,c.entry_form,c.dtls_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (7,37,66,68) and c.entry_form in (7,37,66,68)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.color_id='$color' and c.trans_id!=0 and c.trans_type=1 group by a.recv_number, a.receive_date,a.booking_no, a.emp_id,b.rack_no,b.prod_id,b.body_part_id,b.fabric_description_id,c.color_id,a.knitting_source,a.knitting_company,a.challan_no,a.qc_name,b.batch_id,b.gsm,b.width,c.entry_form,c.dtls_id";

					$i=1;
					//echo $mrr_sql;

					$dtlsArray=sql_select($mrr_sql);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];
						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}
						if($row[csf('entry_form')]==37){ // without roll
							$booking_qty=$finish_production[$row[csf("booking_no")]][$row[csf("fabric_description_id")]][$row[csf("body_part_id")]];
						}else{
							$booking_qty=$roll_arr[$row[csf("dtls_id")]][$row[csf("entry_form")]];
						}
						$process_loss=($row[csf('quantity')]/$booking_qty)*100;
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="80"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
							<td width="110"><p><? echo $knitting_company; ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="80"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td width="60"><p><? echo $row[csf('rack_no')]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($booking_qty,2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                          <!--  <td width="80" align="right"><p><? //echo number_format($row[csf('quantity')],2); ?></p></td>-->
							<td width="70" title="Fin Recv Qty/Grey Qty*100" align="right"><p><? echo number_format($process_loss); ?></p></td>
							<td width="60" align="center"><p><? echo $row[csf('emp_id')]; ?></p></td>
							<td width="80" align="center"><p><? echo $row[csf('qc_name')]; ?></p></td>

							<td width="200" ><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
							<td width="50" align="center"><p><? echo $row[csf('gsm')]; ?>&nbsp;</p></td>
							<td width="50" align="center"><p><? echo $row[csf('width')]; ?></p></td>
							<td><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$tot_booking_qty+=$booking_qty;
						$tot_reject_qty+=$row[csf('returnable_qnty')];
						$i++;
					}

					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<td align="right"><? echo number_format($tot_booking_qty,2); ?> </td>

                        <td align="right"><? echo number_format($tot_qty,2); ?> </td>
                       <!-- <td align="right"><? //echo number_format($tot_qty_in,2); ?> </td>-->
						<td colspan="5"> </td>
						<td align="right">&nbsp;<? //echo number_format($tot_qty,2); ?>&nbsp;</td>
						<td align="right">&nbsp;<? //echo number_format($tot_reject_qty,2); ?>&nbsp;</td>
					</tr>

				</tfoot>

			</table>
			<br>
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center" style="display:none">
				<thead>
					<tr>
						<th colspan="6">Transfer In Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="130">Transfer ID</th>
						<th width="70">Transfer Date</th>
						<th width="200">Fabric Des.</th>
						<th width="50">UOM</th>
						<th>Qty</th>
					</tr>
				</thead>
				<tbody>
					<?

					 $sql_transfer_out="select a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id, sum(b.transfer_qnty) as transfer_out_qnty from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(15,134) and c.trans_type=5 and c.color_id='$color' and a.transfer_criteria=4 and a.to_order_id in($po_id) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 group by a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id";
					$transfer_out=sql_select($sql_transfer_out);
					$i=1;


					foreach($transfer_out as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="130"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
							<td width="75"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
							<td width="200" ><p><? echo $product_arr[$row[csf('from_prod_id')]]; ?></p></td>
							<td width="50" ><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?> &nbsp;</p></td>
							<td  align="right"><p><? echo number_format($row[csf('transfer_out_qnty')],2); ?></p></td>
						</tr>
						<?
						$tot_trans_qty+=$row[csf('transfer_out_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total</td>
						<td align="right"><? echo number_format($tot_trans_qty,2); ?>&nbsp;</td>
					</tr>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total Receive Balance</td>
						<td align="right">&nbsp;<? $tot_balance=$tot_qty+$tot_trans_qty; echo number_format($tot_balance,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
		<?

		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}
			//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);
		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});

		</script>
	</fieldset>
	<?
	exit();
}//Knit Finish end

if($action=="issue_ret_popup")
{
	echo load_html_head_contents("Issue Ret. Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1080px;">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

			/*	$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        }); */

    </script>
    <?
    ob_start();
    ?>
    <div id="scroll_body" align="center">
    	<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
    		<tr>
    			<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
    			<td> <div id="report_container"> </div> </td>
    		</tr>
    	</table>
    	<table border="1" class="rpt_table" rules="all" width="1080" cellpadding="0" cellspacing="0" align="center">
    		<thead>
    			<tr>
    				<th colspan="11">Issue Return Details</th>
    			</tr>
    			<tr>
    				<th width="30">Sl</th>
    				<th width="110">System ID</th>
    				<th width="80">Ret. Date</th>
    				<th width="80">Dyeing Source</th>
    				<th width="120">Dyeing Company</th>
    				<th width="100">Challan No</th>
    				<th width="100">Color</th>
    				<th width="100">Batch No</th>
    				<th width="80">Rack</th>
    				<th width="80">Ret. Qty</th>
    				<th width="">Fabric Des.</th>

    			</tr>
    		</thead>
    		<tbody>
    			<?
    			$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
    			$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
    			$sql_issue="select a.knitting_source,a.knitting_company,b.batch_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68)";
    			$result_issue=sql_select($sql_issue);
    			$issue_arr=array();
    			foreach($result_issue as $row)
    			{
    				$issue_arr[$row[csf('batch_id')]]['rack']=$row[csf('rack')];
    				$issue_arr[$row[csf('batch_id')]]['knit_dye_source']=$row[csf('knitting_source')];
    				$issue_arr[$row[csf('batch_id')]]['knit_dye_company']=$row[csf('knitting_company')];
    			}

    			$i=1;

    			$ret_sql="select a.recv_number,a.challan_no,a.issue_id, a.receive_date,b.prod_id,b.pi_wo_batch_no, sum(c.quantity) as quantity,sum(c.returnable_qnty) as returnable_qnty,c.color_id from  inv_receive_master a, inv_transaction b, order_wise_pro_details c
    			where a.id=b.mst_id and b.id=c.trans_id and a.entry_form in (52,126) and c.entry_form in (52,126)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.color_id='$color' and c.trans_id!=0 group by a.recv_number,a.challan_no, a.receive_date,b.prod_id,a.issue_id, b.pi_wo_batch_no,c.color_id";
					//echo $ret_sql;

    			$retDataArray=sql_select($ret_sql);

    			foreach($retDataArray as $row)
    			{
    				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
    				$rack=$issue_arr[$row[csf('pi_wo_batch_no')]]['rack'];
						//echo $row[csf('pi_wo_batch_no')].'='.$batch_no_arr[$row[csf('pi_wo_batch_no')]];
    				$knit_dye_source=$issue_arr[$row[csf('pi_wo_batch_no')]]['knit_dye_source'];
    				$knit_dye_company=$issue_arr[$row[csf('pi_wo_batch_no')]]['knit_dye_company'];

    				if($knit_dye_source==1)
    				{
    					$knitting_company=$company_arr[$knit_dye_company];
    				}
    				else
    				{
    					$knitting_company=$supplier_name_arr[$knit_dye_company];
    				}


    				?>
    				<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
    					<td width="30"><p><? echo $i; ?></p></td>
    					<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
    					<td width="80"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
    					<td width="80"><p><? echo $knitting_source[$knit_dye_source]; ?></p></td>
    					<td width="120" ><p><? echo $knitting_company; ?></p></td>
    					<td width="100" ><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
    					<td  width="100" align="right"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
    					<td  width="100" align="right"><p><? echo $batch_no_arr[$row[csf('pi_wo_batch_no')]]; ?></p></td>
    					<td  width="80" align="right"><p><? echo $row[csf('Rack')]; ?></p></td>
    					<td  width="80" align="right"><p><? echo $row[csf('quantity')]; ?></p></td>

    					<td align="right"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
    				</tr>
    				<?
    				$tot_issue_return_qty+=$row[csf('quantity')];
						//$tot_returnable_qnty+=$row[csf('returnable_qnty')];
    				$i++;
    			}
    			?>
    		</tbody>
    		<tfoot>
    			<tr class="tbl_bottom">
    				<td colspan="9" align="right">Total</td>
    				<td align="right">&nbsp;<? echo number_format($tot_issue_return_qty,2); ?>&nbsp;</td>
    				<td align="right">&nbsp;</td>
    			</tr>
    		</tfoot>
    	</table>
    </div>
    <?
	    $html=ob_get_contents();
	    ob_flush();

	    foreach (glob(""."*.xls") as $filename)
	    {
	    	@unlink($filename);
	    }
				//html to xls convert
	    $name=time();
	    $name=$user_id."_".$name.".xls";
	    $create_new_excel = fopen(''.$name, 'w');
	    $is_created = fwrite($create_new_excel,$html);
	    ?>
	    <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
	    <script>
	    	$(document).ready(function(e) {
	    		document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
	    	});

	    </script>
	</fieldset>

	<?
	exit();
}

if($action=="woven_receive_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="550" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th width="30">Sl</th>
						<th width="100">Receive ID</th>
						<th width="75">Receive Date</th>
						<th width="240">Fabric Description</th>
						<th>Qty</th>
					</tr>
				</thead>
				<tbody>
					<?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;

					$mrr_sql="select a.recv_number, a.receive_date, b.prod_id, sum(c.quantity) as quantity
					from  inv_receive_master a, inv_transaction b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id  and a.entry_form in (17) and c.entry_form in (17)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.color_id='$color' group by a.recv_number, a.receive_date, b.prod_id";
					$dtlsArray=sql_select($mrr_sql);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="75"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="240" ><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
							<td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
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

if($action=="issue_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$cutting_floor_library=return_library_array( "select id, floor_name from lib_prod_floor where production_process=1 ", "id", "floor_name"  );


	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
			d.close();
		}

	</script>

	<?
	ob_start();
	?>
	<fieldset style="width:970px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="1040" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="10">Issue To Cutting Info</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">Issue No</th>
						<th width="120">Issue to Company</th>
						<th width="100">Challan No</th>
						<th width="100">Issue Date</th>
						<th width="100">Color</th>
						<th width="100">Batch No</th>
						<th width="60">Rack No</th>
						<th width="80">Qty</th>
                       <!-- <th width="80">Trans. Out Qty.</th>-->
						<th width="">Fabric Des.</th>
					</tr>
				</thead>
				<tbody>
					<?
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
					$i=1;

					$sql_trans_out="select a.id, a.transfer_system_id as issue_number,a.to_company, a.transfer_date as issue_date,a.challan_no, b.uom, b.from_prod_id, (c.quantity) as quantity_out, b.batch_id,to_rack as rack_no,c.prod_id, c.quantity,c.color_id from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(15,134) and c.trans_type=6 and c.color_id='$color' and a.transfer_criteria=4 and a.from_order_id in($po_id) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 ";
					//group by a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id, b.batch_id,c.prod_id, c.quantity,c.color_id
					$trans_out=sql_select($sql_trans_out);

					$mrr_sql="select a.id,a.company_id, a.issue_number, a.issue_date,a.challan_no, b.prod_id,b.batch_id,b.rack_no, b.cutting_unit, c.quantity,c.color_id
					from  inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(18,71)  and c.entry_form in(18,71) and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID'  and c.color_id='$color'";
					//echo $mrr_sql;

					$dtlsArray=sql_select($mrr_sql);
					$tot_out_qty=0;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
							<td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
							<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="100"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td width="60" ><p><? echo $row[csf('rack_no')]; ?></p></td>
							<td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
                           <!-- <td width="80" align="right" ><p><? //echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>-->
							<td  align="right"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}

					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="8" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?></td>
                        <td align="right"><? //echo number_format($tot_out_qty,2); ?></td>

					</tr>

				</tfoot>
			</table>

			<br>
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center" style="display:none">
				<thead>
					<tr>
						<th colspan="6">Transfer Out Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="130">Transfer ID</th>
						<th width="70">Transfer Date</th>
						<th width="200">Fabric Des.</th>
						<th width="50">UOM</th>
						<th>Qty</th>
					</tr>
				</thead>
				<tbody>
					<?
					//$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$i=1;
					//$sql_transfer_out="select a.id, a.transfer_system_id, a.transfer_date, a.from_order_id, b.uom, b.from_prod_id, sum(b.transfer_qnty) as transfer_out_qnty from  inv_item_transfer_mst a, inv_item_transfer_dtls b where a.company_id=$companyID and a.id=b.mst_id and a.transfer_criteria=4 and a.from_order_id in($po_id) and b.from_prod_id in ($prod_id) and a.item_category=2 group by a.from_order_id, b.from_prod_id, a.id, a.transfer_system_id, a.transfer_date, a.from_order_id, b.from_prod_id, b.uom";

					$sql_transfer_out="select a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id, sum(b.transfer_qnty) as transfer_out_qnty from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(15,134) and c.trans_type=6 and c.color_id='$color' and a.transfer_criteria=4 and a.from_order_id in($po_id) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 group by a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id";
					$transfer_out=sql_select($sql_transfer_out);
					foreach($transfer_out as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="130"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
							<td width="75"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
							<td width="200" ><p><? echo $product_arr[$row[csf('from_prod_id')]]; ?></p></td>
							<td width="50" ><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?> &nbsp;</p></td>
							<td  align="right"><p><? echo number_format($row[csf('transfer_out_qnty')],2); ?></p></td>
						</tr>
						<?
						$tot_trans_qty+=$row[csf('transfer_out_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total</td>
						<td align="right"><? echo number_format($tot_trans_qty,2); ?>&nbsp;</td>
					</tr>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total Issue Balance</td>
						<td align="right"><? $tot_iss_bal=$tot_qty+$tot_trans_qty; echo number_format($tot_iss_bal,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>

		</table>
	</div>
	<?

		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}
				//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);
		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});

		</script>
	</fieldset>
	<?
	exit();
} // Issue End

if($action=="receive_ret_popup")
{
	echo load_html_head_contents("Recv Ret. Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$cutting_floor_library=return_library_array( "select id, floor_name from lib_prod_floor where production_process=1 ", "id", "floor_name"  );


	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
			d.close();
		}

	</script>

	<?
	ob_start();
	?>
	<div id="report_id" align="center" style="width:960px">
		<table border="0" class="rpt_table" rules="all" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
				<td> <div id="report_container"> </div> </td>
			</tr>
		</table>
		<table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0" align="center">
			<thead>
				<tr>
					<th colspan="10">Receive Return Details</th>
				</tr>
				<tr>
					<th width="30">Sl</th>
					<th width="110">Recv.Ret.ID</th>
					<th width="120">Recv.Ret.Company</th>
					<th width="100">Challan No</th>
					<th width="70">Return Date</th>
					<th width="100">Color</th>
					<th width="100">Batch No</th>
					<th width="70">Rack No</th>
					<th  width="70">Return Qty</th>
					<th width="">Fabric Des.</th>
				</tr>
			</thead>
			<tbody>
				<?
				$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );

				$sql_issue="select a.knitting_source,a.knitting_company,b.batch_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68)";
				$result_issue=sql_select($sql_issue);
				$issue_arr=array();
				foreach($result_issue as $row)
				{
					$issue_arr[$row[csf('batch_id')]]['rack']=$row[csf('rack')];
					$issue_arr[$row[csf('batch_id')]]['knit_dye_source']=$row[csf('knitting_source')];
					$issue_arr[$row[csf('batch_id')]]['knit_dye_company']=$row[csf('knitting_company')];
				}

				$i=1;
				$ret_sql="select a.issue_number,a.company_id,a.challan_no,a.issue_date,b.pi_wo_batch_no,b.rack,c.color_id, b.prod_id, sum(c.quantity) as quantity
				from inv_issue_master a, inv_transaction b, order_wise_pro_details c
				where a.id=b.mst_id and b.id=c.trans_id and a.entry_form in (46) and c.entry_form in (46)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.color_id='$color'  and c.trans_id!=0 group by a.issue_number,a.company_id,a.challan_no,a.issue_date,b.pi_wo_batch_no,c.color_id,b.rack, b.prod_id";
				$retDataArray=sql_select($ret_sql);

				foreach($retDataArray as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					$knit_dye_source=$issue_arr[$row[csf('pi_wo_batch_no')]]['knit_dye_source'];
					$knit_dye_company=$issue_arr[$row[csf('pi_wo_batch_no')]]['knit_dye_company'];
							//$rack=$issue_arr[$row[csf('pi_wo_batch_no')]]['rack'];
					if($knit_dye_source==1)
					{
						$knitting_company=$company_arr[$knit_dye_company];
					}
					else
					{
						$knitting_company=$supplier_name_arr[$knit_dye_company];
					}
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><p><? echo $i; ?></p></td>
						<td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
						<td width="130"><p><? echo $knitting_company; ?></p></td>
						<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
						<td width="70"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
						<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
						<td width="100"><p><? echo $batch_no_arr[$row[csf('pi_wo_batch_no')]]; ?></p></td>
						<td width="70"><p><? echo $row[csf('rack')]; ?></p></td>

						<td  align="right" width="70"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
						<td><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
					</tr>
					<?
					$tot_ret_qty+=$row[csf('quantity')];
					$i++;
				}
				?>
			</tbody>
			<tfoot>
				<tr class="tbl_bottom">
					<td colspan="8" align="right">Total</td>
					<td align="right"><? echo number_format($tot_ret_qty,2); ?>&nbsp;</td>
					<td> </td>
				</tr>

			</tfoot>
		</table>

		<?
		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}
			//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);
		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});

		</script>
	</div>
	<?

}

if($action=="woven_issue_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
			d.close();
		}

	</script>

	<?
	ob_start();
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="550" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<th width="30">Sl</th>
					<th width="100">Issue ID</th>
					<th width="75">Issue Date</th>
					<th width="230">Fabric Description</th>
					<th>Qty</th>
				</thead>
				<tbody>
					<?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;

					$mrr_sql="select a.id, a.issue_number, a.issue_date, b.prod_id, c.quantity
					from  inv_issue_master a, inv_transaction b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=19 and c.entry_form=19 and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and c.color_id='$color'";
					$dtlsArray=sql_select($mrr_sql);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
							<td width="75"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
							<td width="200" ><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
							<td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
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

if($action=="knit_stock_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $po_id."**".$color;die;
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="550" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th width="50">Sl</th>
						<th width="80">Product ID</th>
						<th width="200">Batch No</th>
						<th width="100">Rack</th>
						<th>Qty</th>
					</tr>
				</thead>
				<tbody>
					<?
		if($db_type==0)
		{
			$mrr_sql="select a.id as batch_id, a.batch_no, b.prod_id, b.rack,
			sum((case when c.trans_type in(1,4,5) and c.entry_form in (7,37,66,68,15,52) then c.quantity else 0 end)-(case when c.trans_type in(2,3,6) and c.entry_form in (18,71,15,46) then c.quantity else 0 end)) as balance
			from  pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c
			where a.id=b.pi_wo_batch_no and b.id=c.trans_id  and b.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_id) and c.color_id='$color' and c.entry_form in (7,37,66,68,15,18,71,46,52)
			group by a.id, a.batch_no, b.prod_id,b.rack";
		}
		else
		{
			$mrr_sql="select a.id as batch_id, a.batch_no, b.prod_id, b.rack,
			sum((case when c.trans_type in(1,4,5) and c.entry_form in (7,37,66,68,15,52) then c.quantity else 0 end)-(case when c.trans_type in(2,3,6) and c.entry_form in (18,71,15,46) then c.quantity else 0 end)) as balance
			from  pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c
			where a.id=b.pi_wo_batch_no and b.id=c.trans_id  and b.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_id) and c.color_id='$color' and c.entry_form in (7,37,66,68,15,18,71,46,52)
			group by a.id, a.batch_no, b.prod_id,b.rack
			having sum((case when c.trans_type in(1,4,5) and c.entry_form in (7,37,66,68,15,52) then c.quantity else 0 end)-(case when c.trans_type in(2,3,6) and c.entry_form in (18,71,15,46) then c.quantity else 0 end))>0";
		}

		$dtlsArray=sql_select($mrr_sql);
		$i=1;
		foreach($dtlsArray as $row)
		{
			if ($i%2==0)
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
			?>
			<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
				<td align="center"><p><? echo $i; ?></p></td>
				<td align="center"><p><? echo $row[csf('prod_id')]; ?></p></td>
				<td><p><? echo $row[csf('batch_no')]; ?></p></td>
				<td align="center"><p><? echo $row[csf('rack')]; ?></p></td>
				<td align="right"><p><? echo number_format($row[csf('balance')],2); ?></p></td>
			</tr>
			<?
			$tot_qty+=$row[csf('balance')];
			$i++;
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
