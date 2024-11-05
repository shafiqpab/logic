<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="wo_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

		function js_set_value(id)
		{
			var str=id.split("_");
			$('#hide_wo_id').val( str[0] );
			$('#hide_wo_no').val( str[1] );
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:380px;">
					<table width="370" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>Company</th>
							<th>Search Wo</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:70px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_wo_id" id="hide_wo_id" value="" />
							<input type="hidden" name="hide_wo_no" id="hide_wo_no" value="" />
						</thead>
						<tbody>
							<tr>
								<td align="center">
									<?
									echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- All Company --",$companyID,"", 1 );
									?>
								</td>
								<td align="center">
									<input type="text" style="width:130px" class="text_boxes" name="txt_search_wo" id="txt_search_wo" placeholder="Wo No" />
								</td>
		                        <td align="center">
		                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('txt_search_wo').value+'**'+'<? echo $cbo_year_id; ?>', 'create_job_no_search_list_view', 'search_div', 'finish_fabric_service_issue_receive_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:70px;" />
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
	$txt_search_wo=$data[1];
	$year_id=$data[2];

	if($txt_search_wo!="") $wo_cond=" and a.booking_no_prefix_num=$txt_search_wo";
	//$year="year(insert_date)";
	if($db_type==0) $year_field_by="year(insert_date)";
	else if($db_type==2) $year_field_by="to_char(insert_date,'YYYY')";
	else $year_field_by="";

	if($year_id!=0) $year_cond=" and $year_field_by='$year_id'"; else $year_cond="";


	// $sql= "select id, job_no, job_no_prefix_num, buyer_name, style_ref_no, $year_field_by as year from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id $buyer_id_cond $job_cond $style_cond $year_cond order by id DESC";
	$sql = "SELECT a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, $year_field_by as year, a.supplier_id from wo_booking_mst a where a.booking_type=3 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $wo_cond $year_cond order by a.id DESC";
	// echo $sql;die;

	echo create_list_view("tbl_list_search", "Booking No,Year,Booking Date", "130,80,60","350","270",0, $sql , "js_set_value", "id,booking_no", "", 1, "0,0,0", $arr , "booking_no,year,booking_date", "",'','0,0,0','','') ;
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
	$cbo_service_company= str_replace("'","",$cbo_service_company);
	$txt_wo_no_show= trim(str_replace("'","",$txt_wo_no_show));
	$txt_wo_no= trim(str_replace("'","",$txt_wo_no));
	$txt_date_from= str_replace("'","",$txt_date_from);
	$txt_date_to= str_replace("'","",$txt_date_to);

	if($cbo_service_company!=0) $service_company_cond=" and a.dyeing_company=$cbo_service_company";
	if ($txt_wo_no_show!="") $wo_no_cond=" and b.booking_no='$txt_wo_no_show'";
	if($txt_date_from && $txt_date_to)
    {
        if($db_type==0)
        {
            $date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
            $date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
            //$issue_date_cond="and a.receive_date between '$date_from' and '$date_to'";
            $booking_date_cond="and a.booking_date between '$date_from' and '$date_to'";
        }
        if($db_type==2)
        {
            $date_from=change_date_format($txt_date_from,'','',1);
            $date_to=change_date_format($txt_date_to,'','',1);
            //$issue_date_cond="and a.receive_date between '$date_from' and '$date_to'";
            $booking_date_cond="and a.booking_date between '$date_from' and '$date_to'";
        }
    }

	$service_company_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );


	ob_start();

	?>
		<fieldset style="width:1000px;">
			<table cellpadding="0" cellspacing="0" width="1000" align="left">
				<tr class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="7" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="7" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="7" style="font-size:14px"><strong> <? echo "Date : ".change_date_format(str_replace("'","",$txt_date_from));?></strong> To <strong> <? echo change_date_format(str_replace("'","",$txt_date_to));?></strong></td>
				</tr>
			</table>
			<table width="1070" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
				<thead>
					<tr>
						<th width="40">Sl</th>
						<th width="120">Service Company Name</th>
                        <th width="100">Work Order no</th>
                        <th width="100">Process</th>
                        <th width="100">Requierd</th>
                        <th width="100">Issue Qty</th>
                        <th width="100">Issue Balance</th>
						<th width="100">Finish Fabric Received Qty</th>
                       	<th width="100">Grey Used</th>
                       	<th width="100">Grey Used Balance</th>
                       	<th width="">Process Loss (%)</th>
					</tr>
				</thead>
			</table>
			<div style="width:1090px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="1070" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body"  align="left">
					<?
					$con = connect();
		            $r_id=execute_query("delete from tmp_booking_id where userid=$user_id");
		            oci_commit($con);

					$main_booking_sql="SELECT a.id, b.booking_no, a.booking_date, a.booking_type,sum(b.wo_qnty) as wo_qnty
					from wo_booking_dtls b, wo_booking_mst a
					where b.booking_no=a.booking_no and b.booking_type=3 $wo_no_cond $booking_date_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0
					group by a.id, b.booking_no, a.booking_date, a.booking_type";

					//echo $main_booking_sql;
					$mainBookingArray=sql_select($main_booking_sql);
					$woIdsChkArr = array();
					$requierd_qty_arr = array();
					$woChkArr = array();
					foreach ($mainBookingArray as $val)
					{

						if ($woChkArr[$val[csf("id")]]=="")
						{
							$woChkArr[$val[csf("id")]]=$val[csf("id")];

							$requierd_qty_arr[$val[csf("booking_no")]] += $val[csf("wo_qnty")];

							$booking_id = $val[csf('id')];
							$booking_type = $val[csf('booking_type')];
	                        // echo "insert into tmp_booking_id (userid, booking_id, type) values ($user_id, $booking_id, $booking_type)";
	                        $r_id=execute_query("insert into tmp_booking_id (userid, booking_id, type) values ($user_id, $booking_id, $booking_type)");
						}
					}
					oci_commit($con);

					$sql_query = "SELECT a.dyeing_company, a.entry_form, b.booking_no, b.batch_issue_qty as issue_qty, b.process_id, c.id, c.booking_type, b.grey_used from inv_receive_mas_batchroll a, pro_grey_batch_dtls b, wo_booking_mst c, tmp_booking_id d where a.id=b.mst_id and b.booking_no=c.booking_no and c.id=d.booking_id and d.userid=$user_id and c.booking_type=3 and a.company_id=$cbo_company_id $service_company_cond $wo_no_cond and a.entry_form in(91,92) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 "; //$issue_date_cond
					//echo $sql_query;die;
					$nameArray=sql_select($sql_query);
					//$woChkArr = array();
					foreach ($nameArray as $val)
					{
						if ($val[csf("entry_form")]==91)
						{
							$dataArray[$val[csf("booking_no")]]["dyeing_company"] = $val[csf("dyeing_company")];
							$dataArray[$val[csf("booking_no")]]["issue_qnty"] += $val[csf("issue_qty")];
							$dataArray[$val[csf("booking_no")]]["process_name"] .= $conversion_cost_head_array[$val[csf("process_id")]].',';
						}
						else
						{
							$dataArray[$val[csf("booking_no")]]["dyeing_company"] = $val[csf("dyeing_company")];
							$dataArray[$val[csf("booking_no")]]["received_qty"] += $val[csf("issue_qty")];
							$dataArray[$val[csf("booking_no")]]["grey_used"] += $val[csf("grey_used")];
							$dataArray[$val[csf("booking_no")]]["process_name"] .= $conversion_cost_head_array[$val[csf("process_id")]].',';
						}
					}
					//echo "<pre>";print_r($dataArray);
					$r_id=execute_query("delete from tmp_booking_id where userid=$user_id");
		            oci_commit($con);

					$i=1;
					$grand_requierd_qty=$grand_receive=$grand_issue=$grand_balance=0;
					foreach ($dataArray as $wo_no => $row)
					{
						$requierd_qty =$requierd_qty_arr[$wo_no];
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";


						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							<td width="40" align="center"><p style="word-break: break-all;"><? echo $i; ?></p></td>
							<td width="120" align="center"><p><? echo $service_company_arr[$row["dyeing_company"]]; ?></p></td>
							<td width="100" align="center"><p><? echo $wo_no; ?></p></td>
							<td width="100" align="center"><p><? echo implode(',',array_unique(explode(",",chop($row["process_name"] ,",")))); ?></p></td>
							<td width="100" align="right"><p><? echo number_format($requierd_qty,2,'.','');?></p></td>
							<td width="100" align="right"><p>
								<a href="##" onClick="openmypage_dtls('<? echo $wo_no; ?>','details_popup','net_issue')" style="text-decoration: none;">
									<? echo number_format($row["issue_qnty"],2,'.','');?>
								</a></p>
							</td>
							<td width="100" align="right" title="Issue Balance=Requierd-Issue Qty"><p><? echo number_format($requierd_qty-$row["issue_qnty"],2,'.','');?></p></td>
							<td width="100" align="right"><p>
								<a href="##" onClick="openmypage_dtls('<? echo $wo_no; ?>','details_popup','net_receive')" style="text-decoration: none;">
									<? echo number_format($row["received_qty"],2,'.','');?>
								</a></p>
							</td>
							<td width="100" align="right" title=""><p><? echo number_format($row["grey_used"],2,'.','');?></p></td>
							<td width="100" align="right" title="Issue Qty-Grey Used"><p><? $g_used_balance = ($row["issue_qnty"]-$row["grey_used"]);echo number_format($g_used_balance,2,'.','');?></p></td>
							<td width="" align="right" title="(Grey Used-Finish Fabric Received Qty)/Grey Used*100"><p>
								<?
								if($row["grey_used"] >0 )
								{
									echo number_format(($row["grey_used"]-$row["received_qty"])/$row["grey_used"]*100,2,'.','');
								}
								else
								{
									echo '0.00';
								}
								?>
							</p></td>
						</tr>
						<?
						$i++;

						$grand_requierd_qty+=$requierd_qty;
						$grand_issue+=$row["issue_qnty"];
						$grand_issue_bal+=$requierd_qty-$row["issue_qnty"];
						$grand_receive+=$row["received_qty"];
						$grand_grey_used +=$row["grey_used"];
						$grand_g_used_balance +=$g_used_balance;
					}
					?>
				</table>
				<table width="1070" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"  align="left">
					<tfoot>
						<th width="40" align="center"></th>
						<th width="120" align="right"></p></th>
						<th width="100" align="right"></p></th>
						<th width="100" align="right"><p>Grand Total</p></th>
						<th width="100" align="right"><? echo number_format($grand_requierd_qty,2,'.','');?></th>
						<th width="100" align="right"><? echo number_format($grand_issue,2,'.','');?></th>
						<th width="100" align="right"><? echo number_format($grand_issue_bal,2,'.','');?></th>
						<th width="100" align="right"><? echo number_format($grand_receive,2,'.','');?></th>
						<th width="100" align="right"><? echo number_format($grand_grey_used,2,'.','');?></th>
						<th width="100" align="right"><? echo number_format($grand_g_used_balance,2,'.','');?></th>
						<th width="" align="right"></th>
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

if($action=="details_popup")
{
	echo load_html_head_contents("Finish Fabric Service Issue Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');

		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}

	</script>
	<div style="width:550px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:531px; margin-left:3px">
		<div id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="530" cellpadding="0" cellspacing="0">
            	<thead>
					<th colspan="10"><b>

						<?
						if($type=='net_issue')
						{
							echo 'Issue Details Information';
						}
						else if($type=='net_receive')
						{
							echo 'Receive Details Information';
						}

						?>
					</b></th>
				</thead>
				<thead>
                    <th width="30">SL</th>
                    <th width="100">
						<?
						if($type=='net_issue')
						{
							echo 'Issue No';
						}
						else if($type=='net_receive')
						{
							echo 'Receive No';
						}
						?>
					</th>
                    <th width="100">Year</th>
                    <th width="150">Service Source</th>
                    <th width="150">Service Company</th>
				</thead>
                <?

                $i=1;

				$con = connect();
				$r_id=execute_query("delete from tmp_booking_id where userid=$user_id");
				oci_commit($con);

				$main_booking_sql="SELECT a.id, b.booking_no, a.booking_date, a.booking_type,sum(b.wo_qnty) as wo_qnty
				from wo_booking_dtls b, wo_booking_mst a
				where b.booking_no=a.booking_no and b.booking_type=3 and b.booking_no='$wo_no' and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0
				group by a.id, b.booking_no, a.booking_date, a.booking_type";

				//echo $main_booking_sql;
				$mainBookingArray=sql_select($main_booking_sql);
				$woIdsChkArr = array();
				$requierd_qty_arr = array();
				$woChkArr = array();
				foreach ($mainBookingArray as $val)
				{

					if ($woChkArr[$val[csf("id")]]=="")
					{
						$woChkArr[$val[csf("id")]]=$val[csf("id")];

						$requierd_qty_arr[$val[csf("booking_no")]] += $val[csf("wo_qnty")];

						$booking_id = $val[csf('id')];
						$booking_type = $val[csf('booking_type')];
						// echo "insert into tmp_booking_id (userid, booking_id, type) values ($user_id, $booking_id, $booking_type)";
						$r_id=execute_query("insert into tmp_booking_id (userid, booking_id, type) values ($user_id, $booking_id, $booking_type)");
					}
				}
				oci_commit($con);

				$sql = "SELECT a.id, $year_field a.recv_number_prefix_num, a.recv_number, a.dyeing_source, a.dyeing_company, a.receive_date, b.booking_without_order
				from inv_receive_mas_batchroll  a,pro_grey_batch_dtls b where a.company_id=$company_id and a.entry_form=91 and a.status_active=1 and a.is_deleted=0 and a.id = b.mst_id and b.status_active=1 and b.is_deleted=0 and a.dyeing_source=$source_id $search_field_cond $date_cond  $year_cond
				group by a.id, a.insert_date, a.recv_number_prefix_num, a.recv_number, a.dyeing_source, a.dyeing_company, a.receive_date,b.booking_without_order
				order by a.id";

				if($db_type==0)
				{
					$year_field="YEAR(a.insert_date) as year,";
					$year_cond=" and YEAR(a.insert_date)= '$chalan_year'";
				}
				else if($db_type==2)
				{
					$year_field="to_char(a.insert_date,'YYYY') as year,";
					$year_cond=" and to_char(a.insert_date,'YYYY') = '$chalan_year'";
				}
				else $year_field="";//defined Later

				$sql_query = "SELECT a.id, a.dyeing_company, $year_field a.entry_form, a.recv_number, a.dyeing_source from inv_receive_mas_batchroll a, pro_grey_batch_dtls b, wo_booking_mst c, tmp_booking_id d where a.id=b.mst_id and b.booking_no=c.booking_no and c.id=d.booking_id and d.userid=$user_id and c.booking_type=3 and a.company_id=$company and a.entry_form in(91,92) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.dyeing_company, a.insert_date, a.entry_form, a.recv_number, a.dyeing_source order by a.id";
				//echo $sql_query;//die;
				$nameArray=sql_select($sql_query);

				$r_id=execute_query("delete from tmp_booking_id where userid=$user_id");
				oci_commit($con);

				$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
				$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');


				foreach($nameArray as $row)
				{
					//var_dump($row);

					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$dye_comp="&nbsp;";
					if($row[csf('dyeing_source')]==1)
						$dye_comp=$company_arr[$row[csf('dyeing_company')]];
					else
						$dye_comp=$supllier_arr[$row[csf('dyeing_company')]];

					if($type=='net_issue')
					{
						if($row[csf("entry_form")]==91)
						{
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30" align="center"><? echo $i; ?></td>
								<td width="100" align="center"><p><? echo $row[csf('recv_number')]; ?></p></td>
								<td width="100" align="center"><p><? echo $row[csf('year')]; ?></p></td>
								<td width="150" align="center"><p><? echo $knitting_source[$row[csf('dyeing_source')]]; ?></p></td>
								<td width="150" align="center"><p><? echo $dye_comp; ?></p></td>
							</tr>
							<?

						}
					}
					else if($type=='net_receive')
					{
						if($row[csf("entry_form")]==92)
						{
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30" align="center"><? echo $i; ?></td>
								<td width="100" align="center"><p><? echo $row[csf('recv_number')]; ?></p></td>
								<td width="100" align="center"><p><? echo $row[csf('year')]; ?></p></td>
								<td width="150" align="center"><p><? echo $knitting_source[$row[csf('dyeing_source')]]; ?></p></td>
								<td width="150" align="center"><p><? echo $dye_comp; ?></p></td>
							</tr>
							<?

						}
					}


					$i++;

                }
                ?>

            </table>
		</div>
	</fieldset>
	<?
    exit();
}
?>
