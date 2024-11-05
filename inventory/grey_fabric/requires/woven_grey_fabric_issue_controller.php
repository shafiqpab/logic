<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//--------------------------------------------------------------------------------------------
//load drop down knitting company
if ($action=="load_drop_down_knit_com")
{
	$exDataArr = explode("**",$data);
	$knit_source=$exDataArr[0];
	$company=$exDataArr[1];
	$issuePurpose=$exDataArr[2];

	if($company=="" || $company==0) $company_cond2 = ""; else $company_cond2 = "and c.tag_company=$company";

	if($knit_source==0 || $knit_source=="")
	{
		echo create_drop_down( "cbo_dyeing_company", 170, $blank_array,"", 1, "-- Select --", 0, "",0 );
	}
	else if($knit_source==1)
	{
		echo create_drop_down( "cbo_dyeing_company", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $company, "" );
	}
	else if($knit_source==3 && ($issuePurpose==4 || $issuePurpose==8 || $issuePurpose==11))
	{
		echo create_drop_down( "cbo_dyeing_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and b.supplier_id=c.supplier_id and b.party_type in(21,24,25,26) and a.status_active=1 $company_cond2 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
	}
	else
	{
		echo create_drop_down( "cbo_dyeing_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and b.supplier_id=c.supplier_id and a.status_active=1 $company_cond2 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
	}
	exit();
}
	/*if ($action=="load_room_rack_self_bin")
	{
		load_room_rack_self_bin("requires/woven_grey_fabric_issue_controller",$data);
	}*/

//load drop down store
	if ($action=="load_drop_down_store")
	{
		echo create_drop_down( "cbo_store_name", 170, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' and a.status_active=1 and a.is_deleted=0 and b.category_type in(13,14) group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "",0 );
		exit();
	}
	/*if ($action=="load_drop_down_store")
	{
		echo create_drop_down( "cbo_store_name", 170, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' and b.category_type=14 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "",0 );
		exit();
	}*/

	//load drop down Color
	if ($action=="load_drop_down_color")
	{
		$data=explode("_",$data);
		$all_po_id_or_booking_no=$data[0];
		$issue_purpose=$data[1];
		$color_id=$data[2];
		$basis=$data[3];
		$hidden_order_id=$data[4];
		$is_sales=$data[5];

		if(empty($hidden_order_id)) $hidden_order_id = $all_po_id_or_booking_no;

		//echo $data[1];
		

		$sql="SELECT c.id, c.color_name from fabric_sales_order_dtls a, lib_color c,fabric_sales_order_mst b where b.id = a.mst_id and b.entry_form =547 and  a.color_id=c.id and a.id in($hidden_order_id) and  a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.id, c.color_name";
		//echo $sql;
		echo create_drop_down( "cbo_color_id", 170, $sql,"id,color_name", 0, "-- Select Color --", $color_id, "",0 );
		exit();
	}

	if ($action=="is_roll_maintain")
	{

		$sql = sql_select("select variable_list, fabric_roll_level, batch_maintained from variable_settings_production
			where company_name=$data and variable_list in (3,13) and status_active=1 and is_deleted=0");

		//echo $sql;
		echo "$('#hidden_is_roll_maintain').val(0);\n";
		echo "$('#hidden_is_batch_maintain').val(0);\n";
		foreach($sql as $key=>$val)
		{
		//if($val[csf('variable_list')]==3) echo "$('#hidden_is_roll_maintain').val(".$val[csf("fabric_roll_level")].");\n";
			if($val[csf('variable_list')]==13) echo "$('#hidden_is_batch_maintain').val(".$val[csf("batch_maintained")].");\n";
		}
		$isRackBalance=return_field_value("rack_balance","variable_settings_inventory","company_name=$data and variable_list=21 and status_active=1 and is_deleted=0");
		if($isRackBalance!=1) $isRackBalance=0;
		echo "$('#hidden_is_rack_balance').val(".$isRackBalance.");\n";
		//echo "new_item_controll();\n";
		exit();
	}
	//load drop down supplier
	if ($action=="load_drop_down_supplier")
	{
		echo create_drop_down( "cbo_supplier", 170, "select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$data and b.party_type in (1,20,90) and c.status_active=1 and c.is_deleted=0","id,supplier_name", 1, "-- Select --", 0, "",0 );
		exit();
	}
if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."'  and module_id=6 and report_id=33 and is_deleted=0 and status_active=1");
	//echo $print_report_format;die;
	//$field_name, $table_name, $query_cond, $return_fld_name, $new_conn
	$print_report_format_arr=explode(",",$print_report_format);
	echo "$('#print_1').hide();\n";
	echo "$('#print_2').hide();\n";
	echo "$('#print_7').hide();\n";
	echo "$('#print_8').hide();\n";
	echo "$('#print_vat').hide();\n";
	echo "$('#print_9').hide();\n";
	

	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==94){echo "$('#print_1').show();\n";}
			if($id==35){echo "$('#print_2').show();\n";}
			if($id==36){echo "$('#print_7').show();\n";}
			if($id==37){echo "$('#print_8').show();\n";}
			if($id==95){echo "$('#print_vat').show();\n";}
			if($id==64){echo "$('#print_9').show();\n";}
			
		}
	}
	/*else
	{
		echo "$('#print_1').show();\n";
		echo "$('#print_2').show();\n";
		echo "$('#print_7').show();\n";
		echo "$('#print_8').show();\n";
		echo "$('#print_vat').show();\n";
		echo "$('#print_9').show();\n";
		
	}*/
	exit();
}


//batch pop up here-------------//
	if ($action=="batch_popup")
	{
		echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
		extract($_REQUEST);

		?>

		<script>

			function js_set_value(id,batchNo,batchColor)
			{
				$("#txt_batch_id").val(id);
				$("#txt_batch_no").val(batchNo);
				parent.emailwindow.hide();
			}
		</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="600" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<tr>
							<th width="150">Search By</th>
							<th width="150" align="center" id="search_by_td_up">Enter Batch No</th>
							<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								$sql="select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$cbo_company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name";
							//echo $sql;
							//$search_by = array(1=>'Batch No', 2=>'Buyer');
								$search_by = array(1=>'Batch No');
								$dd="change_search_event(this.value, '0*1', '0*".$sql."', '../../../')";
								echo create_drop_down("cbo_search_by", 120, $search_by, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td width="180" align="center" id="search_by_td">
								<input type="text" id="txt_search_common" name="txt_search_common" class="text_boxes" style="width:100px" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" style="width:100px;" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+'<? echo $cbo_company_id; ?>', 'create_batch_search_list', 'search_div', 'woven_grey_fabric_issue_controller', 'setFilterGrid(\'view\',-1)');" />

								<!-- Hidden field here-->
								<input type="hidden" id="txt_batch_id" value="" />
								<input type="hidden" id="txt_batch_no" value="" />
								<!-- END -->
							</td>
						</tr>

					</tbody>
				</tr>
			</table>
			<div align="center" valign="top" id="search_div"> </div>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_batch_search_list")
{
	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
 	$company = $ex_data[2]; // company id

 	$cond="";
 	if($txt_search_by==1 && $txt_search_common!="")
 		$cond = " and a.batch_no like '%$txt_search_common%'";
 	if($txt_search_by==2 && $txt_search_common!=0)
 		$cond = " and a.buyer_name='$txt_search_common'";

 	$color_arr = return_library_array( "select id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
 	if($db_type==0)
 	{
 		$sql = "select a.id, a.batch_no, a.booking_no, a.color_id, group_concat(c.po_number) as po_number
 		from
 		pro_batch_create_mst a, pro_batch_create_dtls b left join wo_po_break_down c on b.po_id=c.id
 		where
 		a.id=b.mst_id and a.company_id=$company and a.entry_form=0 $cond
 		group by a.id, a.batch_no, a.booking_no, a.color_id
 		order by a.batch_no";
 	}
 	else
 	{
 		$sql = "select a.id, a.batch_no, a.booking_no, a.color_id, LISTAGG(c.po_number, ',') WITHIN GROUP (ORDER BY c.id) as po_number
 		from
 		pro_batch_create_mst a, pro_batch_create_dtls b left join wo_po_break_down c on b.po_id=c.id
 		where
 		a.id=b.mst_id and a.company_id=$company and a.entry_form=0 $cond
 		group by a.id, a.batch_no, a.booking_no, a.color_id
 		order by a.batch_no";
 	}
 	//echo $sql;die;
 	$result = sql_select($sql);

 	?>
 	<div align="left" style="margin-left:50px; margin-top:10px">
 		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" >
 			<thead>
 				<th width="50">SL</th>
 				<th width="100">Batch No</th>
 				<th width="100">Color</th>
 				<th width="120">Booking No</th>
 				<th width="250">Po Number</th>
 			</thead>
 		</table>

 		<div style="width:770px; max-height:250px; overflow-y:scroll" id="container_batch" >
 			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" id="view">
 				<?
 				$i=1;
 				$previouDataRow="";
 				foreach ($result as $row)
 				{
 					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
 					?>
 					<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')];?>,'<? echo $row[csf('batch_no')];?>','<? echo $color_arr[$row[csf('color_id')]]; ?>')"  >
 						<td width="50"><? echo $i; ?>  </td>
 						<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
 						<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
 						<td width="120"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
 						<td width="250"><p><? echo $row[csf('po_number')]; ?>&nbsp;</p></td>
 					</tr>
 					<?
 					$i++;
 				}
 				?>
 			</table>
 		</div>
 	</div>
 	<?
 	exit();
 }

 if($action=="job_no_for_duplication_check")
 {
 	$data=explode("**",$data);
 	$update_id=$data[0];
 	$dtls_id=$data[1];

 	if($dtls_id=="") $dtls_id_cond=""; else $dtls_id_cond=" and i.id!=$dtls_id";

	//echo "select b.job_no_mst as job_no from inv_grey_fabric_issue_dtls i, order_wise_pro_details a, wo_po_break_down b where i.id=a.dtls_id and a.po_breakdown_id=b.id and i.mst_id=$update_id and a.entry_form=577 and a.trans_type=2 and a.status_active=1 and a.is_deleted=0 $dtls_id_cond";die;
 	$job_no=return_field_value("b.job_no_mst as job_no","inv_grey_fabric_issue_dtls i, order_wise_pro_details a, wo_po_break_down b","i.id=a.dtls_id and a.po_breakdown_id=b.id and i.mst_id=$update_id and a.entry_form=577 and a.trans_type=2 and a.status_active=1 and a.is_deleted=0 $dtls_id_cond","job_no");
 	echo $job_no;
 	exit();
 }

// wo/pi popup here----------------------//
 if ($action=="fabbook_popup")
 {
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
 	extract($_REQUEST);
 	?>

 	<script>
 		var update_id='<? echo $update_id; ?>';
 		var dtls_tbl_id='<? echo $dtls_tbl_id; ?>';
 		function fn_check()
 		{
 			show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $issue_purpose; ?>+'_'+<? echo $cbo_basis; ?>+'_'+document.getElementById('cbo_is_sales').value, 'create_fabbook_search_view', 'search_div', 'woven_grey_fabric_issue_controller', 'setFilterGrid(\'view\',-1)');
 		}

 		function js_set_value(booking_dtls,curr_job_no,check_or_not)
 		{
 			if(check_or_not==1)
 			{
 				if(update_id!="")
 				{
 					var job_no = trim(return_global_ajax_value(update_id+"**"+dtls_tbl_id, 'job_no_for_duplication_check', '', 'woven_grey_fabric_issue_controller'));
 					if(job_no!="")
 					{
 						if(job_no!==curr_job_no)
 						{
 							alert("Job Mix Not Allowed");
 							return;
 						}
 					}
 				}
			//alert(dtls_tbl_id+"**"+curr_job_no);
		}

		$("#hidden_booking_number").val(booking_dtls);
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="850" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
				<thead>
					<tr>
						<th>Buyer Name</th>
						<th>Search By</th>
						<th align="center" id="search_by_td_up">Enter <? if($cbo_basis==3) echo "Program"; else "Booking"; ?> No </th>
						<th width="200">Date Range</th>
						<th>Is Sales</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
					</tr>
				</thead>
				<tbody>
					<tr align="center">
						<td>
							<?
							echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );
							?>
						</td>
						<td>
							<?
							if($cbo_basis==1)
							{
								$search_by = array(1=>'Booking No', 2=>'Buyer Order', 3=>'Job No', 4=>'Style Ref');
							}
							else
							{
								$search_by = array(1=>'Booking No', 2=>'Buyer Order', 3=>'Job No', 4=>'Program No');
							}
							$dd="change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../../');";
							echo create_drop_down( "cbo_search_by", 130, $search_by, "", 0, "-- --", "4", $dd, 0);
							?>
						</td>
						<td width="180" align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td align="center">
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
						</td>
						<td align="center">
							<?
							$sales_yes_no = array(1 => "Yes", 0 => "No" );
							echo create_drop_down("cbo_is_sales", 70, $sales_yes_no, "", 0, "--Select--", 0, "");
							?>
						</td>
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="fn_check()" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td align="center" height="40" valign="middle" colspan="6">
							<? echo load_month_buttons(1);  ?>
							<!-- Hidden field here -->
							<input type="text" id="hidden_booking_number" value="" />
							<!-- END -->
						</td>
					</tr>
				</tbody>
			</tr>
		</table>
		<div align="center" valign="top" id="search_div"> </div>
	</form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_fabbook_search_view")
{
	$ex_data = explode("_",$data);
	$buyer = $ex_data[0];
	$txt_search_by = $ex_data[1];
	$txt_search_common = trim($ex_data[2]);
	$txt_date_from = $ex_data[3];
	$txt_date_to = $ex_data[4];
	$company = $ex_data[5];
	$booking_type=$ex_data[6];
	$cbo_basis=$ex_data[7];
	$is_sales = $ex_data[8]; // 0_4_9665_3_4_17_11_3_0

	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$company_arr = return_library_array("select id, company_name from lib_company",'id','company_name');

	if($cbo_basis==3)
	{
		if(trim($txt_search_common)=="" && ($txt_date_from=="" && $txt_date_to==""))
		{
			echo '<h1 style="text-align: center; color: red;">'."Common Search Field Or Date Range Can't be Blank".'</h1>';die;
		}

		if($buyer==0) $buyer="%%";
		if(trim($txt_search_common)!="")
		{
			if( $is_sales==1 && $txt_search_by==1) // Booking from sales order
			{
				$po_id=return_field_value("id","fabric_sales_order_mst","sales_booking_no like '%$txt_search_common%' and status_active=1 and is_deleted=0","id");

				if($po_id!="") $po_no_cond=" and c.po_id in(".$po_id.")";
				if($po_id!="") $trans_in_po_no_cond=" and d.to_order_id in(".$po_id.")";
			}

			else if( $is_sales==0 && $txt_search_by==1) // Booking without sales order
			{
				$po_id_sql="SELECT po_break_down_id AS po_id FROM wo_booking_dtls a 
				WHERE booking_no like '%$txt_search_common%' and status_active=1 and is_deleted=0 GROUP BY po_break_down_id";
				$sql_data_result=sql_select($po_id_sql);
				$po_arr=array();
				foreach ($sql_data_result as $order_key => $row) 
				{
					$po_arr[$row[csf('po_id')]]=$row[csf('po_id')];
				}
				$po_id = implode(',', array_unique(explode(',', implode(',', $po_arr))));
				
				//$po_id=return_field_value("po_break_down_id","wo_booking_dtls","booking_no like '%$txt_search_common%' and status_active=1 and is_deleted=0","po_break_down_id");

				if($po_id!="") $po_no_cond=" and c.po_id in(".$po_id.")";
				if($po_id!="") $trans_in_po_no_cond=" and d.to_order_id in(".$po_id.")";
			}	

			else if($txt_search_by==2 || $txt_search_by==3) // Buyer Order || Job No from order table
			{
				if($txt_search_by==2){$poJobNoCond_search="b.po_number like '%$txt_search_common%'";}else{$poJobNoCond_search="a.job_no like '%$txt_search_common%'";}

				if($db_type==0)
				{
					$po_id=return_field_value("group_concat(b.id) as po_id,","wo_po_details_master a,wo_po_break_down b","$poJobNoCond_search and a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1","po_id");
				}
				else
				{
					$po_id=return_field_value("LISTAGG(b.id, ',') WITHIN GROUP (ORDER BY b.id) as po_id","wo_po_details_master a,wo_po_break_down b","$poJobNoCond_search and a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1","po_id");
				}

				if($po_id!="") $sales_order_and_job_cond=" and c.po_id in(".$po_id.")";
				if($po_id!="") $trans_in_order_and_job_cond=" and d.to_order_id in(".$po_id.")";
			}
			else if($txt_search_by==4) // Program No
			{
				$program_cond=" and b.id like '$txt_search_common'";
				$to_program_cond=" and d.to_program like '$txt_search_common'";
			}
		}
		else
		{
			$program_cond="";
			$to_program_cond="";
		}


		if( $txt_date_from!="" && $txt_date_to!="" )
		{
			if($db_type==0)
			{
				$date_cond= " and b.program_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
			}
			else
			{
				$date_cond= " and b.program_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
			}
		}
		else $date_cond="";

		/*$sql = "select a.id, a.booking_no, a.buyer_id, a.body_part_id, a.determination_id, a.fabric_desc, a.gsm_weight, a.dia, b.color_range,b.color_id, b.id as knit_id, b.knitting_source, b.knitting_party, b.stitch_length, b.program_date,a.is_sales,c.po_id,a.within_group  from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company and a.buyer_id like '$buyer' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $search_field_cond group by b.id, a.id, a.booking_no, a.buyer_id, a.body_part_id, a.determination_id, a.fabric_desc, a.gsm_weight, a.dia, b.color_range,b.color_id, b.knitting_source, b.knitting_party, b.stitch_length, b.program_date,a.is_sales,c.po_id,a.within_group";*/
		
		/*if ($is_sales==0) 
		{
			$is_sales_cond = " and c.is_sales in(0,2)";
		}
		else{
			$is_sales_cond = " and c.is_sales=$is_sales";
		}*/

		

		if ($booking_type==8) // Sample Without Order
		{
			$sql = "SELECT a.id, a.within_group, a.buyer_id, a.body_part_id, a.determination_id, a.fabric_desc, a.gsm_weight, a.dia, b.color_range, b.id as knit_id, b.knitting_source, b.knitting_party, b.stitch_length, b.program_date, a.is_sales, 
			c.po_id
			from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c 
			where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.is_sales=2 and a.buyer_id like '$buyer' $program_cond $po_no_cond $sales_order_and_job_cond
			group by b.id, a.id, a.body_part_id, a.determination_id, a.fabric_desc, a.gsm_weight, a.dia, b.color_range,b.color_id, b.knitting_source, b.knitting_party, b.stitch_length, b.program_date,
			a.is_sales,c.po_id, a.within_group, a.buyer_id
			union all
			select a.id, a.within_group, a.buyer_id, a.body_part_id, a.determination_id, a.fabric_desc, a.gsm_weight, a.dia, b.color_range, b.id as knit_id, b.knitting_source, b.knitting_party, b.stitch_length, 
			b.program_date, a.is_sales, c.to_order_id as po_id
			from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, inv_item_transfer_mst c, inv_item_transfer_dtls d
			where c.id=d.mst_id and d.to_program=b.id and a.id=b.mst_id and d.active_dtls_id_in_transfer=1 and c.company_id=$company and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.is_sales=2
			and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.buyer_id like '$buyer' $to_program_cond $trans_in_po_no_cond $trans_in_order_and_job_cond
			group by a.id, a.body_part_id, a.determination_id, a.fabric_desc, a.gsm_weight, a.dia, b.color_range, b.id, b.knitting_source, b.knitting_party, b.stitch_length, b.program_date, 
			a.is_sales, c.to_order_id, a.within_group, a.buyer_id";
			//echo $sql;die;
		}
		else
		{
			$sql = "SELECT a.id, a.within_group, a.buyer_id, a.body_part_id, a.determination_id, a.fabric_desc, a.gsm_weight, a.dia, b.color_range, b.id as knit_id, b.knitting_source, b.knitting_party, b.stitch_length, b.program_date, a.is_sales, 
			c.po_id
			from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c 
			where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.is_sales=$is_sales and a.buyer_id like '$buyer' $program_cond $po_no_cond $sales_order_and_job_cond
			group by b.id, a.id, a.body_part_id, a.determination_id, a.fabric_desc, a.gsm_weight, a.dia, b.color_range,b.color_id, b.knitting_source, b.knitting_party, b.stitch_length, b.program_date,
			a.is_sales,c.po_id, a.within_group, a.buyer_id
			union all
			select a.id, a.within_group, a.buyer_id, a.body_part_id, a.determination_id, a.fabric_desc, a.gsm_weight, a.dia, b.color_range, b.id as knit_id, b.knitting_source, b.knitting_party, b.stitch_length, 
			b.program_date, a.is_sales, c.to_order_id as po_id
			from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, inv_item_transfer_mst c, inv_item_transfer_dtls d
			where c.id=d.mst_id and d.to_program=b.id and a.id=b.mst_id and d.active_dtls_id_in_transfer=1 and c.company_id=$company and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.is_sales=$is_sales
			and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.buyer_id like '$buyer' $to_program_cond $trans_in_po_no_cond $trans_in_order_and_job_cond
			group by a.id, a.body_part_id, a.determination_id, a.fabric_desc, a.gsm_weight, a.dia, b.color_range, b.id, b.knitting_source, b.knitting_party, b.stitch_length, b.program_date, 
			a.is_sales, c.to_order_id, a.within_group, a.buyer_id"; //  and d.to_program in($txt_search_common)
			//echo $sql;die;
		}
		//echo $sql;
		$result = sql_select($sql);
		$knitID="";$poIDs="";$sales_id="";
		foreach ($result as $rows)
		{	
			//$is_sales = $rows[csf('is_sales')];
			$knitID.=$rows[csf('knit_id')].",";
			$poIDs.=$rows[csf('po_id')].",";
			//if ($rows[csf('is_sales')]==1 && $rows[csf('within_group')]==2 ) {
			//remove within group beacase starlet group want to see sales information both within group yes and no.
			if ($rows[csf('is_sales')]==1 ) {
				$sales_id.=$rows[csf('po_id')].",";
			}
		}
		$knitID=chop($knitID,",");
		$poIDs=chop($poIDs,",");
		$sales_id=chop($sales_id,",");
		$knit_ids=implode(",",array_filter(array_unique(explode(",",$knitID))));
		$po_IDs=implode(",",array_filter(array_unique(explode(",",$poIDs))));
		$sales_IDs=implode(",",array_filter(array_unique(explode(",",$sales_id))));

		if($knit_ids!="")
		{
			$knit_ids=explode(",",$knit_ids);
			$knit_ids_chnk=array_chunk($knit_ids,999);
			$knit_ids_cond=" and";
			$knit_ids_cond_2=" and";
			foreach($knit_ids_chnk as $dtls_id)
			{
				if($knit_ids_cond==" and")  $knit_ids_cond.="(dtls_id in(".implode(',',$dtls_id).")"; else $knit_ids_cond.=" or dtls_id in(".implode(',',$dtls_id).")";
				if($knit_ids_cond_2==" and")  $knit_ids_cond_2.="(a.knit_id in(".implode(',',$dtls_id).")"; else $knit_ids_cond_2.=" or a.knit_id in(".implode(',',$dtls_id).")";
			}
			$knit_ids_cond.=")";
			$knit_ids_cond_2.=")";
		}
		if($po_IDs!="")
		{
			$po_IDs=explode(",",$po_IDs);
			$po_IDs_chnk=array_chunk($po_IDs,999);
			$po_IDs_cond=" and";
			foreach($po_IDs_chnk as $dtls_id)
			{
				if($po_IDs_cond==" and")  $po_IDs_cond.="(a.id in(".implode(',',$dtls_id).")"; else $po_IDs_cond.=" or a.id in(".implode(',',$dtls_id).")";
			}
			$po_IDs_cond.=")";
		}
		if($sales_IDs!="")
		{
			$sales_IDs=explode(",",$sales_IDs);
			$sales_IDs_chnk=array_chunk($sales_IDs,999);
			$sales_IDs_cond=" and";
			foreach($sales_IDs_chnk as $dtls_id)
			{
				if($sales_IDs_cond==" and")  $sales_IDs_cond.="(id in(".implode(',',$dtls_id).")"; else $sales_IDs_cond.=" or id in(".implode(',',$dtls_id).")";
			}
			$sales_IDs_cond.=")";
		}
		// echo $po_IDs_cond.'='.$sales_IDs_cond;die;
		if($db_type==0)
		{
			$plan_details_array=return_library_array( "select dtls_id, group_concat(distinct(po_id)) as po_id from ppl_planning_entry_plan_dtls where company_id=$company and dtls_id in($knitID)  group by dtls_id", "dtls_id", "po_id"  );
		}
		else
		{
			$plan_details_array=return_library_array( "select dtls_id, LISTAGG(po_id, ',') WITHIN GROUP (ORDER BY po_id) as po_id from ppl_planning_entry_plan_dtls where company_id=$company $knit_ids_cond group by dtls_id", "dtls_id", "po_id"  );
		}

		//for PO
		$po_array=array();
		if($db_type==0)
		{
			$po_sql=sql_select("select a.id, a.job_no_mst, a.po_number, b.style_ref_no,a.grouping from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.id in($poIDs) and a.status_active=1 and a.is_deleted=0");
		}
		else
		{
			$po_sql=sql_select("select a.id, a.job_no_mst, a.po_number, b.style_ref_no,a.grouping from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $po_IDs_cond and a.status_active=1 and a.is_deleted=0");
		}
		foreach($po_sql as $row)
		{
			$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no_mst')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$internalRef_array[$row[csf('job_no_mst')]]['grouping']=$row[csf('grouping')];
		}
		/*echo "<pre>";
		print_r($po_array);*/
		$po_booking_array=array();
		
		$po_booking_sql=sql_select("select a.id, b.booking_no from wo_po_break_down a, wo_booking_dtls b where a.id=b.po_break_down_id $po_IDs_cond and a.status_active=1 and a.is_deleted=0 
				and b.booking_type in (1,4) group by a.id, b.booking_no order by a.id");
		
		foreach($po_booking_sql as $row)
		{
			$po_booking_array[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
		}
		/*echo "<pre>";
		print_r($po_booking_array);*/

		//for Requisiton
		$rqsn_array=array();
		if($db_type==0)
		{
			$reqsn_dataArray=sql_select("select a.knit_id, a.requisition_no, group_concat(distinct(yarn_count_id)) as yarn_count_id, group_concat(distinct(lot)) as lot from ppl_yarn_requisition_entry a, product_details_master b where a.prod_id=b.id and a.knit_id in($knitID) and a.status_active=1 and a.is_deleted=0 group by a.knit_id, a.requisition_no");
		}
		else
		{
			$reqsn_dataArray=sql_select("select a.knit_id, a.requisition_no, LISTAGG(b.yarn_count_id, ',') WITHIN GROUP (ORDER BY b.yarn_count_id) as yarn_count_id, LISTAGG(CAST(b.lot AS VARCHAR2(4000))) WITHIN GROUP (ORDER BY b.id) as lot from ppl_yarn_requisition_entry a, product_details_master b where a.prod_id=b.id $knit_ids_cond_2 and a.status_active=1 and a.is_deleted=0 group by a.knit_id, a.requisition_no");
		}
		foreach($reqsn_dataArray as $row)
		{
			$rqsn_array[$row[csf('knit_id')]]['rqsn_no'].=$row[csf('requisition_no')];
			$rqsn_array[$row[csf('knit_id')]]['count'].=implode(",",array_unique(explode(",",$row[csf('yarn_count_id')])));
			$rqsn_array[$row[csf('knit_id')]]['lot'].=implode(",",array_unique(explode(",",$row[csf('lot')])));
		}

		//for Sales order
		if($db_type==0)
		{
			$sales_no_sql=sql_select("select id,job_no,buyer_id,sales_booking_no,po_buyer from fabric_sales_order_mst where within_group=2 and id in($sales_id) and status_active=1 and is_deleted=0");
		}
		else
		{
			$sales_no_sql=sql_select("select id,job_no,buyer_id,sales_booking_no,within_group,po_job_no,po_buyer,style_ref_no from fabric_sales_order_mst where status_active=1 and is_deleted=0 $sales_IDs_cond ");
		}
		foreach ($sales_no_sql as $row) 
		{
			$sales_no_arr[$row[csf('id')]]["job_no"]=$row[csf('job_no')];
			$sales_no_arr[$row[csf('id')]]["buyer_id"]=$row[csf('buyer_id')];
			$sales_no_arr[$row[csf('id')]]["booking_no"]=$row[csf('sales_booking_no')];
			$sales_no_arr[$row[csf('id')]]["within_group"]=$row[csf('within_group')];
			$sales_no_arr[$row[csf('id')]]["po_job_no"]=$row[csf('po_job_no')];
			$sales_no_arr[$row[csf('id')]]["po_buyer"]=$row[csf('po_buyer')];
			$sales_no_arr[$row[csf('id')]]["style"]=$row[csf('style_ref_no')];
		}
		/*echo "<pre>";
		print_r($sales_no_arr);*/

		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1090" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="55">Plan Id</th>
				<th width="60">Program Id</th>
				<th width="75">Program Date</th>
				<th width="50">Reqsn. No</th>
				<th width="100">Booking No</th>
				<th width="70">Buyer</th>
				<th width="110">PO No/FSO No</th>
				<th width="90">Job No</th>
				<th width="100">Internal Ref. No</th>
				<th width="130">Fabric Desc</th>
				<th width="55">Gsm</th>
				<th width="55">Dia</th>
				<th>Color Range</th>
			</thead>
		</table>
		<div style="width:1090px; max-height:280px; overflow-y:scroll" id="container_batch" align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1072" class="rpt_table" id="view">
				<?
				$i=1;
				foreach ($result as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					$reqn_no=$rqsn_array[$row[csf('knit_id')]]['rqsn_no'];

					//$po_id=array_unique(explode(",",$plan_details_array[$row[csf('knit_id')]]));
					$po_id = array_unique(explode(",",$row[csf('po_id')]));
					$po_no=''; $job_no=''; $style_ref_no='';

					foreach($po_id as $val)
					{
						//echo $val.'<br>';
						//if($po_no=='') $po_no=$po_array[$val]['no']; else $po_no.=",".$po_array[$val]['no'];						
						//if($po_no_sales=='') $po_no_sales=$sales_no_arr[$val]["job_no"]; else $po_no_sales.=",".$sales_no_arr[$val]["job_no"];
						if($job_no=='') $job_no=$po_array[$val]['job_no'];
						//if($style_ref_no=='') $style_ref_no=$po_array[$val]['style'];
						$po_no=$po_array[$val]['no'];
						$po_no_sales=$sales_no_arr[$val]["job_no"];
						$po_no_booking_no=$sales_no_arr[$val]["booking_no"];
						$sales_within_group=$sales_no_arr[$val]["within_group"];
						$sales_po_job_no=$sales_no_arr[$val]["po_job_no"];
						//$job_no=$po_array[$val]['job_no'];
						$style_ref_no=$po_array[$val]['style'];
						$sales_style_ref_no=$sales_no_arr[$val]["style"];
						$po_booking=$po_booking_array[$val]['booking_no'];
					}
					//$po_no_id=implode(",",array_unique(explode(",",$plan_details_array[$row[csf('knit_id')]])));
					$po_no_id=implode(",",array_unique(explode(",",$row[csf('po_id')])));

					if($row[csf('is_sales')]==1 && $sales_within_group==1)
					{
						$buyer_name = $buyer_arr[$sales_no_arr[$po_no_id]["po_buyer"]];
						$buyerID = $sales_no_arr[$po_no_id]["po_buyer"];
					}
					else if($row[csf('is_sales')]==1 && $sales_within_group==2)
					{
						$buyer_name = $buyer_arr[$sales_no_arr[$po_no_id]["buyer_id"]];
						$buyerID = $sales_no_arr[$po_no_id]["buyer_id"];
					}
					else
					{
						$buyer_name = $buyer_arr[$row[csf('buyer_id')]];
						$buyerID = $row[csf('buyer_id')];
					}

					if($row[csf('is_sales')]==1){$po_NO= $po_no_sales;}else{ $po_NO=$po_no;}
					if($row[csf('is_sales')]==1 && $sales_within_group==1){ $jobNo = $sales_po_job_no;}
					if($row[csf('is_sales')]==0){ $jobNo = $job_no;}
					if($row[csf('is_sales')]==1){$style_ref_no= $sales_style_ref_no;}else{ $style_ref_no=$style_ref_no;}

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('knit_id')]; ?>__<? echo $row[csf('knit_id')]; ?>__<? echo $buyerID; ?>__<? echo $style_ref_no; ?>__<? echo $po_NO; ?>__<? echo $po_no_id; ?>__<? echo $row[csf('is_sales')]; ?>__<? echo $row[csf('color_id')]; ?>','<? echo $jobNo; ?>',1);">
						<td width="30"><? echo $i; ?></td>
						<td width="55" align="center"><? echo $row[csf('id')]; ?></td>
						<td width="60" align="center"><? echo $row[csf('knit_id')]; ?></td>
						<td width="75" align="center"><? echo change_date_format($row[csf('program_date')]); ?></td>
						<td width="50" align="center"><? echo $reqn_no; ?>&nbsp;</td>
						<td width="100"><p><? if($row[csf('is_sales')]==1) {echo $po_no_booking_no;}else{ echo $po_booking;} ?></p></td>
						<td width="70" title="<? echo $po_no_id; ?>"><p><? echo $buyer_name; ?></p></td>

						<td width="110"><p><? if($row[csf('is_sales')]==1){echo $po_no_sales;}else{echo $po_no;} ?></p></td>

						<td width="90"><p><? echo $jobNo; ?></p></td>
						<td width="100"><p><? echo $internalRef_array[$jobNo]['grouping']; ?></p></td>
						<td width="130"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
						<td width="55"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
						<td width="55"><p><? echo $row[csf('dia')]; ?></p></td>
						<td><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
					</tr>
					<?
					$i++;
				}
				?>
			</table>
		</div>
		<?
	}
	else
	{
		$sql_cond="";
		if(trim($txt_search_common)!="")
		{
			if(trim($txt_search_by)==1) // for Booking No
			{
				$sql_cond=" and a.booking_no like '%$txt_search_common%'";
			}
			else if(trim($txt_search_by)==2) // for buyer order
			{
				$sql_cond =" and b.po_number like '%$txt_search_common%'";
				//$sql_cond = " and b.po_number LIKE '%$txt_search_common%'";	// wo_po_break_down
			}
			else if(trim($txt_search_by)==3) // for job no
			{
				$sql_cond =" and b.job_no_mst like '%$txt_search_common%'";
				//$sql_cond .= " and a.job_no LIKE '%$txt_search_common%'";
			}
			else if(trim($txt_search_by)==4) // for Styel Ref
			{
				$sql_cond =" and d.style_ref_no like '%$txt_search_common%'";
			}
		}
		else
		{
			$sql_cond="";
		}

		if( $txt_date_from!="" && $txt_date_to!="" )
		{
			if($db_type==0)
			{
				$sql_cond .= " and a.booking_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
			}
			else
			{
				$sql_cond .= " and a.booking_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
			}
		}

		if( trim($buyer)!=0 ) $sql_cond .= " and a.buyer_id='$buyer'";
		if( trim($company)!=0 ) $sql_company_cond = " a.company_id='$company' and";
		if( trim($booking_type)==1 ) $sql_cond .= " and a.booking_type!=4";
		else if( trim($booking_type)==4 ) $sql_cond .= " and a.booking_type=4";

		//echo $sql_cond.jahid."<br>";

		if(trim($booking_type)==8 || trim($booking_type)==3 || trim($booking_type)==26 || trim($booking_type)==29 || trim($booking_type)==30 || trim($booking_type)==31 )
		{
			$sql = "SELECT a.id, a.booking_no, a.booking_date, a.buyer_id, a.po_break_down_id, a.item_category, a.delivery_date, a.job_no as job_no_mst
			from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
			where
			$sql_company_cond
			a.booking_no=b.booking_no and
			a.item_category=2 and
			a.status_active=1 and
			a.is_deleted=0 and
			b.status_active=1 and
			b.is_deleted=0
			$sql_cond
			group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.po_break_down_id, a.item_category, a.delivery_date, a.job_no";
		}
		else
		{
			/*
				$po_arr=array();
				$po_data=sql_select("select a.style_ref_no, b.id, b.po_number, b.pub_shipment_date, b.po_quantity, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst");
				foreach($po_data as $row)
				{
					$po_arr[$row[csf('id')]]=$row[csf('po_number')]."**".$row[csf('pub_shipment_date')]."**".$row[csf('po_quantity')]."**".$row[csf('po_qnty_in_pcs')]."**".$row[csf('style_ref_no')];
				}

				$sql = "select a.id, a.booking_no, a.booking_date, a.buyer_id, c.po_break_down_id, a.item_category, a.delivery_date, c.job_no as job_no_mst, d.style_ref_no
				from wo_booking_mst a, wo_booking_dtls c, wo_po_break_down b , wo_po_details_master d
				where
				$sql_company_cond
				a.booking_no=c.booking_no and
				c.po_break_down_id=b.id and
				b.job_no_mst = d.job_no and
				a.item_category=2 and
				a.status_active=1 and
				a.is_deleted=0 and
				b.status_active=1 and
				b.is_deleted=0 and
				c.status_active=1 and
				c.is_deleted=0
				$sql_cond
				group by a.id, a.booking_no, a.booking_date, a.buyer_id, c.po_break_down_id, a.item_category, a.delivery_date, c.job_no, d.style_ref_no";

			*/
			$sql = "SELECT a.id, a.booking_no, a.booking_date, a.buyer_id, c.po_break_down_id, a.item_category, a.delivery_date, c.job_no as job_no_mst, d.style_ref_no,(d.total_set_qnty*b.po_quantity) as po_qnty_in_pcs ,min(b.pub_shipment_date) as pub_shipment_date,b.po_number,b.id as po_id,b.grouping 
				from wo_booking_mst a, wo_booking_dtls c, wo_po_break_down b , wo_po_details_master d
				where
				$sql_company_cond
				a.booking_no=c.booking_no and
				c.po_break_down_id=b.id and
				b.job_no_mst = d.job_no and
				a.item_category=2 and
				a.status_active=1 and
				a.is_deleted=0 and
				b.status_active=1 and
				b.is_deleted=0 and
				c.status_active=1 and
				c.is_deleted=0
				$sql_cond
				group by a.id, a.booking_no, a.booking_date, a.buyer_id, c.po_break_down_id, a.item_category, a.delivery_date, c.job_no, d.style_ref_no, d.total_set_qnty,b.po_quantity,b.po_number,b.id,b.grouping";

			}
		//echo $sql;
		//item_category=2 knit fabrics
		//echo $sql;die;
			$result = sql_select($sql);
			$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
			?>
			<div align="left">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1070" class="rpt_table" >
					<thead>
						<th width="30">SL</th>
						<th width="105">Booking No</th>
						<th width="90">Book. Date</th>
						<th width="100">Buyer</th>
						<th width="90">Item Cat.</th>
						<th width="90">Job No</th>
						<th width="100">Internal Ref. No</th>
						<th width="90">Order Qnty</th>
						<th width="80">Ship. Date</th>
						<th width="80">Style Ref.</th>
						<th >Order No</th>
					</thead>
				</table>
				<div style="width:1090px; max-height:280px; overflow-y:scroll" id="container_batch" >
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1070" class="rpt_table" id="view">
						<?
						$i=1;
						foreach ($result as $row)
						{
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";

						/*$po_qnty_in_pcs=0; $po_no=''; $po_no_id=''; $min_shipment_date=''; $style_ref_no="";
						if($row[csf('po_break_down_id')]!="")
						{
                            $po_id=explode(",",$row[csf('po_break_down_id')]);
                            foreach ($po_id as $id)
                            {
                            	$po_data=explode("**",$po_arr[$id]);
                            	$po_number=$po_data[0];
                            	$pub_shipment_date=$po_data[1];
                            	$po_qnty=$po_data[2];
                            	$poQntyPcs=$po_data[3];
                            	$style_ref_no=$po_data[4];

                            	if($po_no=="") $po_no=$po_number; else $po_no.=",".$po_number;
                            	if($po_no_id=="") $po_no_id=$id; else $po_no_id.=",".$id;

                            	if($min_shipment_date=='')
                            	{
                            		$min_shipment_date=$pub_shipment_date;
                            	}
                            	else
                            	{
                            		if($pub_shipment_date<$min_shipment_date) $min_shipment_date=$pub_shipment_date; else $min_shipment_date=$min_shipment_date;
                            	}

                            	$po_qnty_in_pcs+=$poQntyPcs;
                            	$style_ref_no = $style_ref_no;
                            }
                        }*/
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>__<? echo $row[csf('booking_no')]; ?>__<? echo $row[csf('buyer_id')]; ?>__<? echo $row[csf('style_ref_no')]; ?>__<? echo $row[csf('po_number')]; ?>__<? echo $row[csf('po_id')];?>__0__0','<? echo $row[csf('job_no_mst')]; ?>',0);">
                        	<td width="30"><? echo $i; ?></td>
                        	<td width="105"><p><? echo $row[csf('booking_no')]; ?></p></td>
                        	<td width="90" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                        	<td width="100"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
                        	<td width="90"><p><? echo $item_category[$row[csf('item_category')]]; ?></p></td>
                        	<td width="90"><p><? echo $row[csf('job_no_mst')]; ?>&nbsp;</p></td>
                        	<td width="90"><p><? echo $row[csf('grouping')]; ?>&nbsp;</p></td>
                        	<td width="90" align="right"><? echo $row[csf('po_qnty_in_pcs')];//$po_qnty_in_pcs; ?>&nbsp;</td>
                        	<td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]);//change_date_format($min_shipment_date); ?>&nbsp;</td>
                        	<td width="80" align="center"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
                        	<td><p><? echo $row[csf('po_number')];//$po_no; ?>&nbsp;</p></td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                </table>
            </div>
        </div>
        <?
    }
    exit();
}


//Roll Pop up Search Here----------------------------------//
if ($action=="roll_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);

	//for update mode -----------
	$concate = $cbo_company_id;
	if(!$hidden_roll_id) $$hidden_roll_id="";
	if(!$hidden_roll_qnty) $hidden_roll_qnty="";
	if(!$txt_batch_id) $txt_batch_id="";
	$concate .= "**".$hidden_roll_id."**".$hidden_roll_qnty."**".$txt_batch_id;

	?>

	<script>

		function fn_show_check()
		{
			show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+'<? echo $concate; ?>', 'create_roll_search_list', 'search_div', 'woven_grey_fabric_issue_controller', 'setFilterGrid(\'view\',-1)');
			set_previous_data();
		}


		function set_previous_data()
		{
			var old=document.getElementById('previous_data_row_id').value;
			if(old!="")
			{
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{
					fn_onkeyup(old[i]);
				//js_set_value( old[i] )
			}
		}
	}


	function js_set_value( str )
	{
		var chkProduct = $("#txt_prod_id"+str).val();
		var po_number = $("#txt_po_number"+str).val();
		var flag=0;
		$("input[name='chk[]']:checked").each(function ()
		{
			var rid = $(this).attr('id');
			var ridArr = rid.split("_");
			var str = ridArr[1];
			if($("#txt_prod_id"+str).val()!=chkProduct && chkProduct!="")
			{
				alert("Product Mix Not Allow");
				flag=1;
				return false;
			}
			 /*else if($("#txt_po_number"+str).val()!=po_number && po_number!="")
			 {
				 alert("Order No Mix Not Allow");
				 flag=1;
				 return false;
				}  */
			});

		if(flag==1) return;

		if($("#txt_issue_qnty_"+str).val()*1 <= 0 || $("#txt_issue_qnty_"+str).val()=='-')
		{
			document.getElementById('chk_'+str).checked=false;
			document.getElementById( 'search' + str ).style.backgroundColor = '#FFFFCC';
			return;
		}

		if( document.getElementById("chk_"+str).checked )
		{
			document.getElementById('chk_'+str).checked=false;
			document.getElementById( 'search' + str ).style.backgroundColor = '#FFFFCC';
		}
		else
		{
			document.getElementById('chk_'+str).checked=true;
			document.getElementById( 'search' + str ).style.backgroundColor = 'yellow';
		}

	}


	function fn_onkeyup(str)
	{

		if( $("#txt_issue_qnty_"+str).val()*1 > $("#txt_balance_qnty_"+str).val()*1 )
		{
			$("#txt_issue_qnty_"+str).val($("#txt_balance_qnty_"+str).val());
			return;
		}
		else if($("#txt_issue_qnty_"+str).val()*1<=0 || $("#txt_issue_qnty_"+str).val()=='-')
		{
			document.getElementById('chk_'+str).checked=false;
			document.getElementById( 'search' + str ).style.backgroundColor = '#FFFFCC';
			return;
		}

		var chkProduct = $("#txt_prod_id"+str).val();
		var po_number = $("#txt_po_number"+str).val();
		$("input[name='chk[]']:checked").each(function ()
		{
			var rid = $(this).attr('id');
			var ridArr = rid.split("_");
			var str = ridArr[1];
			if($("#txt_prod_id"+str).val()!=chkProduct && chkProduct!="")
			{
				alert("Product Mix Not Allow");
				return false;
			}
			 /*else if($("#txt_po_number"+str).val()!=po_number && po_number!="")
			 {
				 alert("Order No Mix Not Allow");
				 return false;
				}*/

			});

		document.getElementById('chk_'+str).checked=true;
		document.getElementById('search'+str).style.backgroundColor='yellow';
	}


	var selected_id = new Array;
	var issue_qnty = new Array;
	var chkProduct = "";

	function fnonClose()
	{
		$("input[name='chk[]']:checked").each(function ()
		{
			var rid = $(this).attr('id');
			var ridArr = rid.split("_");
			var str = ridArr[1];

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				issue_qnty.push( $('#txt_issue_qnty_' + str).val() );
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() )
					{
						break;
					}
				}
				selected_id.splice( i, 1 );
				issue_qnty.splice( i, 1 );
			}

			var id = qnty = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				qnty += issue_qnty[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			qnty = qnty.substr( 0, qnty.length - 1 );

			$('#txt_selected_id').val( id );
			$('#txt_issue_qnty').val( qnty );
		});

		parent.emailwindow.hide();
	}

</script>

</head>

<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="600" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
				<thead>
					<tr>
						<th width="150">Search By</th>
						<th width="150" align="center" id="search_by_td_up">Select Buyer</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td align="center">
							<?
							$sql="select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$cbo_company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name";
							//echo $sql;
							$search_by = array(1=>'Buyer', 2=>'Job No', 3=>'Style Ref');
							$dd="change_search_event(this.value, '1*0*0', '".$sql."*0*0', '../../../')";
							echo create_drop_down("cbo_search_by", 120, $search_by, "", 1, "--Select--", "", $dd, 0);
							?>
						</td>
						<td width="180" align="center" id="search_by_td">
							<input type="text" id="txt_search_common" name="txt_search_common" class="text_boxes" style="width:100px" />
						</td>
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" style="width:100px;" onClick="fn_show_check()" />

							<!-- Hidden field here -->
							<input type="hidden" id="txt_selected_id" value="" />
							<input type="hidden" id="txt_issue_qnty" value="" />
							<!-- END -->
						</td>
					</tr>

				</tbody>
			</tr>
		</table>
		<div align="center" valign="top" id="search_div"> </div>
		<table width="750">
			<tr>
				<td align="center" >
					<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnonClose();" style="width:100px" />
				</td>
			</tr>
		</table>
	</form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}


if($action=="create_roll_search_list")
{

	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$expArr = explode("**",$ex_data[2]);
 	$company = $expArr[0]; // company id
	$rolID = $expArr[1];  //roll id
	$rollQnty = $expArr[2]; //roll qnty
	$batchID = $expArr[3]; //batch ID

	$cond="";
	$issue_qnty_arr = array();
	if($rolID!="")
	{
		$issueQnty = explode(",",$rollQnty); //roll wise input issue qnty
		$exp_rollID = explode(",",$rolID);
		for($j=0;$j<count($exp_rollID);$j++)
		{
			$issue_qnty_arr[$exp_rollID[$j]] = $issueQnty[$j];
		}
	}

	$rollCond=$rollCondIssue="";
	if($rolID!="")
	{
		//$rollCond = " and c.id not in ($rolID)"; //use for update mode
		$rollCondIssue = " and roll_id not in ($rolID)"; //use for update mode
	}

	if($txt_search_by==1 && $txt_search_common!="")
		$cond = " and a.buyer_name='$txt_search_common'";
	elseif($txt_search_by==2 && $txt_search_common!="")
		$cond = " and a.job_no='$txt_search_common'";
	elseif($txt_search_by==3 && $txt_search_common!="")
		$cond = " and a.style_ref_no='$txt_search_common'";

	if($batchID!="")
	{
		if($db_type==0)
		{
			$rollNo = sql_select("select group_concat(b.po_id) as po_id, group_concat(b.roll_no) as roll_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id='$batchID' and b.roll_no<>0");
		}
		else
		{
			$rollNo = sql_select("select LISTAGG(b.po_id, ',') WITHIN GROUP (ORDER BY b.po_id) as po_id, LISTAGG(b.roll_no, ',') WITHIN GROUP (ORDER BY b.roll_no) as roll_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id='$batchID' and b.roll_no<>0");
		}
		foreach ($rollNo as $row);
		$rollNo = $row[csf('roll_no')];
		$po_id = $row[csf('po_id')];
		if($rollNo!="") $cond .= " and c.roll_no in ($rollNo)";
		if($po_id!="") $cond .= " and b.id in ($po_id)";
	}
	else $mstID="";

	if($db_type==0)
	{
		$sql = "select b.po_number, c.id, c.po_breakdown_id, c.roll_no, d.prod_id, e.product_name_details, e.lot,sum(c.qnty) as rcvqnty
		from
		wo_po_details_master a, wo_po_break_down b, pro_roll_details c, pro_grey_prod_entry_dtls d, product_details_master e
		where
		a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and c.dtls_id=d.id and d.prod_id=e.id and c.entry_form=2 and c.roll_no!=0 and e.current_stock>0
		$cond
		group by c.id,c.po_breakdown_id,c.roll_no
		order by e.product_name_details";
	}
	else
	{
		$sql = "select b.po_number, c.id, c.po_breakdown_id, c.roll_no, d.prod_id, e.product_name_details, e.lot, sum(c.qnty) as rcvqnty
		from
		wo_po_details_master a, wo_po_break_down b, pro_roll_details c, pro_grey_prod_entry_dtls d, product_details_master e
		where
		a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and c.dtls_id=d.id and d.prod_id=e.id and c.entry_form=2 and c.roll_no!=0 and e.current_stock>0
		$cond
		group by c.id, c.po_breakdown_id,c.roll_no, b.po_number, d.prod_id, e.product_name_details, e.lot
		order by e.product_name_details";
	}
 	//echo $sql;die;
	$result = sql_select($sql);

	?>
	<div align="left" style="margin-left:50px">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" >
			<thead>
				<th width="50">SL</th>
				<th width="60">Roll No</th>
				<th width="100">Order No</th>
				<th width="250">Fabric Description</th>
				<th width="100">Yarn Lot</th>
				<th width="100">Roll Qnty</th>
				<th width="100">Issue Qnty</th>
			</thead>
		</table>

		<div style="width:770px; max-height:250px; overflow-y:scroll" id="container_batch" >
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" id="view">
				<?
				$i=1;
				$previouDataRow="";
				foreach ($result as $row)
				{
				//echo "sum(qnty) pro_roll_details po_breakdown_id=".$row[csf('po_breakdown_id')]." and roll_no=".$row[csf('roll_no')]." and entry_form=577 $rollCondIssue";
					$issueqnty = return_field_value("sum(qnty)","pro_roll_details","po_breakdown_id=".$row[csf('po_breakdown_id')]." and roll_no=".$row[csf('roll_no')]." and entry_form=577 $rollCondIssue");
				//echo $issueqnty."==";
					if($issueqnty=="") $issueqnty=0;
					$balanceQnty = $row[csf('rcvqnty')]-$issueqnty;
				//if($balanceQnty<=0)return;


					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$isChecked="";
					if($issue_qnty_arr[$row[csf('id')]]!="")
					{
						$row[csf('qnty')]=$issue_qnty_arr[$row[csf('id')]];
						$bgcolor="yellow";
						$isChecked = "checked";
						if($previouDataRow == "") $previouDataRow=$i; else $previouDataRow .= ",".$i;
					}

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i;?>" style="cursor:pointer" >
						<td width="50"><? echo $i; ?>
						<input type="checkbox" id="chk_<? echo $i;?>" name="chk[]" class="check" style="visibility:hidden" onClick="js_set_value(<? echo $i;?>)" <? echo $isChecked; ?> />
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>"/>
						<input type="hidden" id="txt_prod_id<?php echo $i ?>" value="<? echo $row[csf('prod_id')]; ?>" />
						<input type="hidden" id="txt_po_number<?php echo $i ?>" value="<? echo $row[csf('po_number')]; ?>" />
					</td>
					<td width="60" align="right" onClick="js_set_value(<? echo $i;?>)"><p><? echo $row[csf('roll_no')]; ?></p></td>
					<td width="100" onClick="js_set_value(<? echo $i;?>)"><p><? echo $row[csf('po_number')]; ?></p></td>
					<td width="250" onClick="js_set_value(<? echo $i;?>)"><p><? echo $row[csf('product_name_details')]; ?></p></td>
					<td width="100" onClick="js_set_value(<? echo $i;?>)"><p><? echo $row[csf('lot')]; ?></p></td>
					<td width="100" onClick="js_set_value(<? echo $i;?>)"><input type="text" id="txt_balance_qnty_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $balanceQnty; ?>" style="width:70px" disabled readonly /></td>
					<td width="100"><input type="text" name="txt_issue_qnty[]" id="txt_issue_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? if($isChecked=="checked") echo $row[csf('qnty')]; else echo $balanceQnty; ?>"  onKeyUp="fn_onkeyup(<? echo $i;?>)" /></td>

				</tr>
				<?
				$i++;
			}
			?>
			<!-- for update, contain row id which is seleted -->
			<input type="hidden" id="previous_data_row_id" value="<? echo $previouDataRow; ?>" />
		</table>
	</div>
</div>
<?
exit();

}

// child form data populate after call after close roll pop up
if($action == "populate_child_from_data")
{
	$i=0;
	$exp_data = explode("**",$data);
	$roll_ID = $exp_data[0];
	if($roll_ID=="") exit; // if roll id emtpy
	$issueQnty = explode(",",$exp_data[1]); //roll wise input issue qnty
	$exp_rollID = explode(",",$roll_ID);

	$issue_qnty_arr = array();
	for($j=0;$j<count($exp_rollID);$j++)
	{
		$issue_qnty_arr[$exp_rollID[$j]] = $issueQnty[$j];
	}

	if($db_type==0)
	{
	//echo $issueQnty;
		$sql = "select GROUP_CONCAT(c.id) as id, GROUP_CONCAT(c.roll_no) as roll_no, GROUP_CONCAT(c.po_breakdown_id) as po_breakdown_id, count(c.roll_no) as roll_no_count, sum(c.qnty) as qnty, d.prod_id, e.product_name_details
		from pro_roll_details c, pro_grey_prod_entry_dtls d, product_details_master e
		where
		c.entry_form=2 and
		c.dtls_id=d.id and
		d.prod_id=e.id and
		c.roll_no!=0 and
		c.id in ($roll_ID) group by d.prod_id, e.product_name_details";
	}
	else
	{
		$sql = "select LISTAGG(c.id, ',') WITHIN GROUP (ORDER BY c.id) as id, LISTAGG(c.roll_no, ',') WITHIN GROUP (ORDER BY c.roll_no) as roll_no, LISTAGG(c.po_breakdown_id, ',') WITHIN GROUP (ORDER BY c.po_breakdown_id) as po_breakdown_id, count(c.roll_no) as roll_no_count, sum(c.qnty) as qnty, d.prod_id, e.product_name_details
		from pro_roll_details c, pro_grey_prod_entry_dtls d, product_details_master e
		where
		c.entry_form=2 and
		c.dtls_id=d.id and
		d.prod_id=e.id and
		c.roll_no!=0 and
		c.id in ($roll_ID) group by d.prod_id, e.product_name_details";
	}
	//echo $sql;
	$result = sql_select($sql);
	foreach($result as $row)
	{
		$i=$i+1;

		$exp_concat = explode(",",$row[csf("id")]);
		$issue_qnty = 0;
		$roll_wise_qnty="";
		foreach($exp_concat as $key=>$val)
		{
			if($roll_wise_qnty!="") $roll_wise_qnty.=",";
			$roll_wise_qnty .= $issue_qnty_arr[$val];
			$issue_qnty += $issue_qnty_arr[$val];
		}

		if($db_type==0)
		{
			$lot=return_field_value("group_concat(distinct(yarn_lot)) as lot","pro_grey_prod_entry_dtls","prod_id='".$row[csf('prod_id')]."' and yarn_lot<>'' and status_active=1 and is_deleted=0","lot");
			$count_id=return_field_value("group_concat_concat(distinct(yarn_count)) as count","pro_grey_prod_entry_dtls","prod_id='".$row[csf('prod_id')]."' and yarn_count<>'' and yarn_count<>0 and status_active=1 and is_deleted=0","count");
		}
		else
		{
			$lot=return_field_value("LISTAGG(cast(yarn_lot as VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY yarn_lot) as lot","pro_grey_prod_entry_dtls","prod_id='".$row[csf('prod_id')]."' and yarn_lot is not null and status_active=1 and is_deleted=0","lot");
			$count_id=return_field_value("LISTAGG(yarn_count, ',') WITHIN GROUP (ORDER BY yarn_count) as count","pro_grey_prod_entry_dtls","prod_id='".$row[csf('prod_id')]."' and yarn_count is not null and yarn_count<>0 and status_active=1 and is_deleted=0","count");
		}

		$lot=implode(",",array_unique(explode(",",$lot)));
		$count=implode(",",array_unique(explode(",",$count_id)));

		echo "$('#txtNoOfRoll').val('".$row[csf('roll_no_count')]."');\n";

		echo "$('#txtRollNo').val('".$row[csf('id')]."');\n";
		echo "$('#txtRollPOid').val('".$row[csf('po_breakdown_id')]."');\n";
		echo "$('#txtRollPOQnty').val('".$roll_wise_qnty."');\n";

		echo "$('#txtItemDescription').val('".$row[csf('product_name_details')]."');\n";
		echo "$('#hiddenProdId').val('".$row[csf('prod_id')]."');\n";
		echo "$('#txtReqQnty').val('".$issue_qnty."');\n";
		echo "$('#txtYarnLot').val('".$lot."');\n";

		//echo "$('#cbo_yarn_count').val('".$row[csf('yarn_count_id')]."');\n";
		echo "set_multiselect('cbo_yarn_count','0','1','".$count."','0');\n";
		//echo "disable_enable_fields('show_textcbo_yarn_count','1','','');\n";
		echo "if($('#cbo_issue_purpose').val()==3 || $('#cbo_issue_purpose').val()==8) $('#txtIssueQnty').val('".$issue_qnty."');\n";
		echo "get_php_form_data('".$row[csf('po_breakdown_id')]."'+\"**\"+".$row[csf('prod_id')].", \"populate_data_about_order\", \"requires/woven_grey_fabric_issue_controller\" );";
	}

	exit();

}


if($action=="populate_data_about_order")
{
	$data=explode("**",$data);
	$order_id=$data[0];
	$prod_id=$data[1];
	$company_id=$data[2];
	$program_no=$data[3];
	$receive_basis=$data[4];
	$store_id=$data[5];
	$floor_id=$data[6];
	$room_id=$data[7];
	$rack_id=$data[8];
	$self_id=$data[9];


	
	$sql=sql_select("select
		sum(case when a.entry_form in(2,550) then a.quantity end) as grey_fabric_recv,
		sum(case when a.entry_form in(577) then a.quantity end) as grey_fabric_issued,
		sum(case when a.entry_form=578 then a.quantity end) as grey_fabric_recv_return,
		sum(case when a.entry_form=579 then a.quantity end) as grey_fabric_issue_return,
		sum(case when a.entry_form in(13,81,362) and a.trans_type=5 then a.quantity end) as grey_fabric_trans_recv,
		sum(case when a.entry_form in(13,80,362) and a.trans_type=6 then a.quantity end) as grey_fabric_trans_issued
		from order_wise_pro_details a, inv_transaction b where a.trans_id = b.id and b.status_active=1 and a.trans_id<>0 and a.prod_id=$prod_id and a.po_breakdown_id in($order_id) and a.is_deleted=0 and a.status_active=1 and b.store_id='$store_id' and b.floor_id='$floor_id' and b.room='$room_id' and b.rack='$rack_id' and b.self='$self_id'");

	$grey_fabric_recv=$sql[0][csf('grey_fabric_recv')]+$sql[0][csf('grey_fabric_trans_recv')]+$sql[0][csf('grey_fabric_issue_return')];
	$grey_fabric_issued=$sql[0][csf('grey_fabric_issued')]+$sql[0][csf('grey_fabric_trans_issued')]+$sql[0][csf('grey_fabric_recv_return')];
	$yet_issue=$grey_fabric_recv-$grey_fabric_issued;
	

	echo "$('#txt_fabric_received').val('".$grey_fabric_recv."');\n";
	echo "$('#txt_cumulative_issued').val('".$grey_fabric_issued."');\n";
	echo "$('#txt_yet_to_issue').val('".$yet_issue."');\n";
	$stock_qty=return_field_value("current_stock","product_details_master","id='$prod_id'");
	echo "$('#txt_global_stock').val('".$stock_qty."');\n";
	if($order_id!="")
	{
		$sql_order = "SELECT b.id,a.job_no, a.style_ref_no, a.buyer_id, a.customer_buyer, a.within_group from fabric_sales_order_mst a,fabric_sales_order_dtls b where a.id = b.mst_id and a.entry_form =547 and b.id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0";
		//echo "console.log('".$sql_order."');\n";
		$sales_no_sql=sql_select($sql_order);
		foreach ($sales_no_sql as $row) 
		{
			$sales_no_arr[$row[csf('id')]]["job_no"]=$row[csf('job_no')];
			$sales_no_arr[$row[csf('id')]]["style_ref_no"]=$row[csf('style_ref_no')];
			if ($row[csf('within_group')]==1) 
			{
				$sales_no_arr[$row[csf('id')]]["buyer_id"]=$row[csf('customer_buyer')];
			}
			else
			{
				$sales_no_arr[$row[csf('id')]]["buyer_id"]=$row[csf('buyer_id')];
			}				
		}
		$order_no=$sales_no_sql[0][csf("job_no")];
		$style_ref_no=$sales_no_sql[0][csf("style_ref_no")];
		if ($row[csf('within_group')]==1) 
		{
			$buyer_id=$sales_no_sql[0][csf("customer_buyer")];
		}
		else 
		{
			$buyer_id=$sales_no_sql[0][csf("buyer_id")];
		}			
		
		echo "$('#txt_style_ref').val('".implode(",",array_unique(explode(",",$style_ref_no)))."');\n";
		echo "$('#cbo_buyer_name').val('".$buyer_id."');\n";
		echo "$('#txt_order_no').val('".$order_no."');\n";
		echo "$('#hidden_order_id').val('".$sales_no_sql[0][csf("id")]."');\n";
	}
	exit();
}


if($action=="populate_data_about_sample")
{
	$data=explode("**",$data);
	$booking_id=$data[0];
	$prod_id=$data[1];

	$recv_id='';
	if($db_type==0)
	{
		$recv_id=return_field_value("group_concat(distinct(id)) as id","inv_receive_master","booking_without_order=1 and booking_id=$booking_id and status_active=1 and is_deleted=0 and entry_form in(2)","id");
	}
	else
	{
		$recv_id=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as id","inv_receive_master","booking_without_order=1 and booking_id=$booking_id and status_active=1 and is_deleted=0 and entry_form in(2)","id");
	}

	if($recv_id=="") $recv_id=0;
	$all_booking_id=$booking_id.",".$recv_id;
	$sql = "SELECT sum(qnty) as qnty, sum(qnty2) as qnty2 from
	(
	SELECT sum(case when b.receive_basis!=9 and b.booking_id=$booking_id then c.grey_receive_qnty else 0 end ) as qnty, sum(case when b.receive_basis=9 and b.booking_id in($recv_id) then c.grey_receive_qnty else 0 end) as qnty2 from inv_receive_master b, pro_grey_prod_entry_dtls c where b.id=c.mst_id and b.entry_form in(2,550) and b.booking_without_order=1 and c.trans_id!=0 and b.status_active=1 and b.is_deleted=0 and b.booking_id in ($all_booking_id) and c.prod_id=$prod_id
	union all
	SELECT sum(c.cons_quantity) as qnty, 0 as qnty2 from inv_receive_master b, inv_transaction c where b.id=c.mst_id and b.entry_form=579 and b.receive_purpose=8 and c.item_category=14 and c.transaction_type=4 and b.booking_id=$booking_id and b.status_active=1 and b.is_deleted=0 and c.prod_id=$prod_id
	union all
	SELECT sum(c.transfer_qnty) as qnty, 0 as qnty2 from inv_item_transfer_mst b, inv_item_transfer_dtls c where b.id=c.mst_id and c.item_category=14 and b.transfer_criteria in(6,8) and b.to_order_id=$booking_id and b.status_active=1 and b.is_deleted=0 and c.from_prod_id=$prod_id
	) product_details_master";

	$result=sql_select($sql);

	$iss_sql="SELECT sum(qnty) as qnty from
	(
	SELECT sum(c.issue_qnty) as qnty from inv_issue_master b, inv_grey_fabric_issue_dtls c where b.id=c.mst_id and b.item_category=14 and b.entry_form=577 and b.issue_purpose in(3,8,26,29,30,31) and b.issue_basis in(1,3) and b.booking_id=$booking_id and c.prod_id=$prod_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	union all
	SELECT sum(c.cons_quantity) as qnty from inv_issue_master b, inv_transaction c, inv_receive_master d where b.id=c.mst_id and b.received_id=d.id and ((d.booking_id=$booking_id and d.receive_basis!=9) or (d.booking_id in ($recv_id) and d.receive_basis=9)) and d.booking_without_order=1 and b.item_category=14 and b.entry_form=578 and c.transaction_type=3 and c.item_category=14 and c.prod_id=$prod_id and c.status_active=1 and c.is_deleted=0
	union all
	SELECT sum(c.transfer_qnty) as qnty from inv_item_transfer_mst b, inv_item_transfer_dtls c where b.id=c.mst_id and b.transfer_criteria in(7,8) and c.item_category=14 and b.from_order_id=$booking_id and b.status_active=1 and b.is_deleted=0 and c.from_prod_id=$prod_id and c.status_active=1 and c.is_deleted=0
	) inv_issue_master";
	// echo $iss_sql;
	$result_iss=sql_select($iss_sql);

	$grey_fabric_recv=$result[0][csf('qnty')]+$result[0][csf('qnty2')];
	$grey_fabric_issued=$result_iss[0][csf('qnty')];
	$yet_issue=$grey_fabric_recv-$grey_fabric_issued;

	echo "$('#txt_fabric_received').val('".$grey_fabric_recv."');\n";
	echo "$('#txt_cumulative_issued').val('".$grey_fabric_issued."');\n";
	echo "$('#txt_yet_to_issue').val('".$yet_issue."');\n";

	$stock_qty=return_field_value("current_stock","product_details_master","id='$prod_id'");
	echo "$('#txt_global_stock').val('".$stock_qty."');\n";

	exit();
}


if($action=="itemDescription_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	if($hidden_order_id!="")
	{
		$sql_rate=sql_select("select b.prod_id,sum(c.quantity*b.cons_rate)/sum(c.quantity) as avg_rate from inv_receive_master a, inv_transaction b,order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id  and b.prod_id=c.prod_id and b.cons_amount>0 and a.item_category=14 and b.transaction_type=1 and a.entry_form in (2,550)  and c.entry_form in (2,550) and c.trans_type=1 and b.item_category=14 and c.po_breakdown_id in ($hidden_order_id) and b.status_active=1 and b.is_deleted=0 group by b.prod_id");
		foreach($sql_rate as $r_val)
		{
			$stock_arr[$r_val[csf('prod_id')]]=$r_val[csf('avg_rate')];
		}
	}
	else
	{
		$stock_arr = return_library_array( "select id, avg_rate_per_unit from product_details_master where item_category_id=14",'id','avg_rate_per_unit');
	}

	?>
	<script>
		function js_set_value(prod_id)
		{
			$("#txt_selected_id").val(prod_id);
			parent.emailwindow.hide();
		}

		$(document).ready(function(e) {
			setFilterGrid('tbl_search',-1);
		});
	</script>
	<?
	$po_id='';$prog_cond='';
	if($cbo_basis==1)
	{
		if($cbo_issue_purpose==4 || $cbo_issue_purpose==11)
		{
			$po_sql=sql_select("select po_break_down_id from wo_booking_dtls where booking_no='$txt_booking_no' and status_active=1");
			foreach($po_sql as $row)
			{
				if($po_id_check[$row[csf("po_break_down_id")]]=="")
				{
					$po_id_check[$row[csf("po_break_down_id")]]=$row[csf("po_break_down_id")];
					$po_id.=$row[csf("po_break_down_id")].",";
				}
			}
			$po_id=chop($po_id,",");
		}
	}
	else if($cbo_basis==3)
	{
		$po_id=$hidden_order_id;
		

		$prog_cond = " and c.from_program = '$txt_booking_no'" ;
	}
	else
	{
		$po_id=$hidden_order_id;
	}
	//echo $po_id.'='.$cbo_issue_purpose.'**'.$txt_booking_id;die;
	if($po_id!="" && $po_id!=0)
	{
		 //echo $cbo_basis." string ".$po_id;//0
		$program_no_arr=array();
		if($cbo_basis==3)
		{
			if($db_type==0)
			{
				$recv_id=return_field_value("group_concat(id) as id","inv_receive_master","booking_without_order=0 and booking_id=$txt_booking_no and status_active=1 and is_deleted=0 and entry_form in(2) and receive_basis=2","id");
			}
			else
			{
				$recv_id=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as id","inv_receive_master","booking_without_order=0 and booking_id=$txt_booking_no and status_active=1 and is_deleted=0 and entry_form in(2) and receive_basis=2","id");
			}
			if($recv_id=="") $recv_id=0;
			$all_booking_id=$txt_booking_no.",".$recv_id;


		
			if($db_type==0)
			{
				$sql = "SELECT id, product_name_details, current_stock, po_id,floor_id, room, rack, self, yarn_lot, yarn_count, stitch_length, sum(qnty) as qnty, sum(qnty2) as qnty2,is_sales,within_group 
				from ( 
					select a.id, a.product_name_details, a.current_stock, b.po_breakdown_id as po_id, e.floor_id, e.room,  e.rack, e.self, c.yarn_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length,
					sum(case when d.receive_basis=2 and d.booking_id=$txt_booking_no and b.entry_form=2 then b.quantity else 0 end ) as qnty,
					sum(case when d.receive_basis=9 and d.booking_id in($recv_id) and b.entry_form=550 then b.quantity else 0 end) as qnty2,b.is_sales,d.within_group
					from product_details_master a, inv_receive_master d, order_wise_pro_details b, pro_grey_prod_entry_dtls c, inv_transaction e where a.id=b.prod_id and b.dtls_id=c.id and d.id=c.mst_id and a.current_stock>0 and a.item_category_id=14 and b.entry_form in(2,550) and b.trans_type=1 and b.po_breakdown_id in ($po_id) and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.booking_id in ($all_booking_id) and c.trans_id = e.id and e.store_id = '$cbo_store_name' group by a.id, a.product_name_details, a.current_stock, b.po_breakdown_id, c.yarn_lot, c.yarn_count, c.stitch_length, e.floor_id, e.room, e.rack, e.self, b.is_sales,d.within_group
					union all
					select a.id, a.product_name_details, a.current_stock, b.po_breakdown_id as po_id, d.floor_id, d.room, d.rack, d.self, c.yarn_lot as yarn_lot, c.y_count as yarn_count, c.stitch_length, sum(b.quantity) as qnty, 0 as qnty2, b.is_sales, 0 as within_group from product_details_master a, order_wise_pro_details b, inv_item_transfer_dtls c, inv_transaction d where a.id=b.prod_id and b.dtls_id=c.id and a.current_stock>0 and a.item_category_id=14 and b.entry_form in(13,81) and b.trans_type=5 and c.item_category=14 and b.po_breakdown_id in ($po_id) and c.to_program in ($txt_booking_no) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.to_trans_id=d.id and d.store_id ='$cbo_store_name' group by a.id, a.product_name_details, a.current_stock, b.po_breakdown_id, c.yarn_lot, c.y_count, c.stitch_length, d.floor_id, d.room, d.rack, d.self, b.is_sales
					union all
					select a.id, a.product_name_details, a.current_stock, b.po_breakdown_id as po_id, c.floor_id, c.room, c.rack, c.self, c.batch_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length, sum(b.quantity) as qnty, 0 as qnty2,b.is_sales,0 as within_group from product_details_master a, order_wise_pro_details b, inv_transaction c where a.id=b.prod_id and b.trans_id=c.id and a.current_stock>0 and a.item_category_id=14 and c.transaction_type=4 and b.entry_form=579 and b.trans_type=4 and c.item_category=14 and b.po_breakdown_id in ($po_id) and b.status_active=1 and b.is_deleted=0 and c.store_id = '$cbo_store_name' group by a.id, a.product_name_details, a.current_stock, c.batch_lot, c.yarn_count, b.po_breakdown_id, c.stitch_length, c.floor_id, c.room, c.rack, c.self, b.is_sales
				) product_details_master 
				group by id, po_id, floor_id, room, rack, self, yarn_lot, yarn_count, stitch_length,is_sales,within_group";
			}
			else
			{
				$program_no_arr=array();
				if($db_type==0)
				{
					$recv_id=return_field_value("group_concat(id) as id","inv_receive_master","booking_without_order=0 and booking_id=$txt_booking_no and status_active=1 and is_deleted=0 and entry_form in(2) and receive_basis=2","id");
				}
				else
				{
					$recv_id=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as id","inv_receive_master","booking_without_order=0 and booking_id=$txt_booking_no and status_active=1 and is_deleted=0 and entry_form in(2) and receive_basis=2","id");
				}
				if($recv_id=="") $recv_id=0;
				$all_booking_id=$txt_booking_no.",".$recv_id;
				
				$sql = "SELECT id, product_name_details, current_stock, po_id, floor_id, room, rack, self, yarn_lot, yarn_count, stitch_length, sum(qnty) as qnty, sum(qnty2) as qnty2,is_sales,within_group from (
				select a.id, a.product_name_details, a.current_stock,b.po_breakdown_id as po_id, e.floor_id, e.room,  e.rack, e.self, c.yarn_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length, sum(case when d.receive_basis=2 and d.booking_id=$txt_booking_no and b.entry_form=2 then b.quantity else 0 end ) as qnty,
				sum(case when d.receive_basis=9 and d.booking_id in($recv_id) and b.entry_form=550 then b.quantity else 0 end) as qnty2 ,b.is_sales,d.within_group
				from product_details_master a, inv_receive_master d, order_wise_pro_details b, pro_grey_prod_entry_dtls c, inv_transaction e where a.id=b.prod_id and b.dtls_id=c.id and d.id=c.mst_id and a.current_stock>0 and a.item_category_id=14 and b.entry_form in(2,550) and b.trans_type=1 and b.po_breakdown_id in ($po_id) and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.booking_id in ($all_booking_id) and c.trans_id = e.id and e.store_id = '$cbo_store_name' group by a.id, a.product_name_details, a.current_stock, b.po_breakdown_id, c.yarn_lot, c.yarn_count, c.stitch_length, e.floor_id, e.room,  e.rack, e.self, b.is_sales,d.within_group
				union all
				select a.id, a.product_name_details, a.current_stock, b.po_breakdown_id as po_id, d.floor_id, d.room, d.rack, d.self, c.yarn_lot as yarn_lot, c.y_count as yarn_count, c.stitch_length, 0 as qnty, sum(b.quantity) as qnty2,b.is_sales,0 as within_group from product_details_master a, order_wise_pro_details b, inv_item_transfer_dtls c, inv_transaction d where a.id=b.prod_id and b.dtls_id=c.id and a.current_stock>0 and a.item_category_id=14 and b.entry_form in(13,81,362) and b.trans_type=5 and c.item_category=14 and b.po_breakdown_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.to_trans_id = d.id and d.store_id = '$cbo_store_name' group by a.id, a.product_name_details, a.current_stock, b.po_breakdown_id, c.yarn_lot, c.y_count, c.stitch_length, d.floor_id, d.room, d.rack, d.self, b.is_sales
				union all
				select a.id, a.product_name_details, a.current_stock, b.po_breakdown_id as po_id, c.floor_id, c.room, c.rack, c.self, c.batch_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length, sum(b.quantity) as qnty, 0 as qnty2,b.is_sales,0 as within_group from product_details_master a, order_wise_pro_details b, inv_transaction c where a.id=b.prod_id and b.trans_id=c.id and a.current_stock>0 and a.item_category_id=14 and c.transaction_type=4 and b.entry_form=579 and b.trans_type=4 and c.item_category=14 and b.po_breakdown_id in ($po_id) and b.status_active=1 and b.is_deleted=0 and c.store_id = '$cbo_store_name' group by a.id, a.product_name_details, a.current_stock, b.po_breakdown_id, c.batch_lot, c.yarn_count, c.stitch_length, c.floor_id, c.room, c.rack, c.self, b.is_sales ) product_details_master group by id, product_name_details, current_stock, floor_id, room, rack, self, is_sales,within_group, yarn_lot, yarn_count, stitch_length,po_id";
				
			}
		}
		else
		{
			/* if($db_type==0)
			{
				$field = "group_concat(b.po_breakdown_id) po_id,
				group_concat(c.yarn_lot) yarn_lot,
				group_concat(c.yarn_count) yarn_count,
				group_concat(c.stitch_length) stitch_length";
				$field2 = "group_concat(b.po_breakdown_id) po_id,
				group_concat(c.yarn_lot) yarn_lot,
				group_concat(c.y_count) yarn_count,
				group_concat(c.stitch_length) stitch_length";
				$field3 = "group_concat(b.po_breakdown_id) po_id,
				group_concat(c.batch_lot) yarn_lot,
				group_concat(c.yarn_count) yarn_count,
				group_concat(c.stitch_length) stitch_length";
			}
			else
			{
				$field = "listagg(b.po_breakdown_id, ',') within group (order by b.po_breakdown_id) as po_id,
				listagg(cast(c.yarn_lot as varchar(4000)), ',') within group (order by c.yarn_lot) as yarn_lot,
				listagg(cast(c.yarn_count as varchar(4000)), ',') within group (order by c.yarn_count) as yarn_count,
				listagg(cast(c.stitch_length as varchar(4000)), ',') within group (order by c.stitch_length) as stitch_length";
				$field2 = "listagg(b.po_breakdown_id, ',') within group (order by b.po_breakdown_id) as po_id,
				listagg(cast(c.yarn_lot as varchar(4000)), ',') within group (order by c.yarn_lot) as yarn_lot,
				listagg(cast(c.y_count as varchar(4000)), ',') within group (order by c.y_count) as yarn_count,
				listagg(cast(c.stitch_length as varchar(4000)), ',') within group (order by c.stitch_length) as stitch_length";
				$field3 = "listagg(b.po_breakdown_id, ',') within group (order by b.po_breakdown_id) as po_id,
				listagg(cast(c.batch_lot as varchar(4000)), ',') within group (order by c.batch_lot) as yarn_lot,
				listagg(cast(c.yarn_count as varchar(4000)), ',') within group (order by c.yarn_count) as yarn_count,
				listagg(cast(c.stitch_length as varchar(4000)), ',') within group (order by c.stitch_length) as stitch_length";
			} */

			if($db_type==0)
			{
				$field = "b.po_breakdown_id as po_id, c.yarn_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length as stitch_length";
				$field2 = "b.po_breakdown_id as po_id, c.yarn_lot as yarn_lot, c.y_count as yarn_count, c.stitch_length as stitch_length";
				$field3 = "b.po_breakdown_id as po_id, c.batch_lot as yarn_lot,	c.yarn_count as yarn_count,	c.stitch_length as stitch_length";
			}else{
				
				$field = "b.po_breakdown_id as po_id, cast(c.yarn_lot as varchar(4000)) as yarn_lot, cast(c.yarn_count as varchar(4000)) as yarn_count, cast(c.stitch_length as varchar(4000)) as stitch_length";
				$field2 = "b.po_breakdown_id as po_id, cast(c.yarn_lot as varchar(4000)) yarn_lot, cast(c.y_count as varchar(4000)) as yarn_count, cast(c.stitch_length as varchar(4000)) as stitch_length";
				$field3 = "b.po_breakdown_id as po_id, cast(c.batch_lot as varchar(4000)) as yarn_lot, cast(c.yarn_count as varchar(4000)) as yarn_count, cast(c.stitch_length as varchar(4000)) as stitch_length";
			}

			$sql="select id, product_name_details, current_stock, po_id,floor_id,room, rack, self, yarn_lot, yarn_count, stitch_length, sum(qnty) as qnty,is_sales,within_group 
			from 
			(
				select a.id, a.product_name_details, a.current_stock,b.is_sales,0 as within_group, d.floor_id, d.room, d.rack, d.self,
				$field, sum(b.quantity) as qnty
				from product_details_master a, order_wise_pro_details b, pro_grey_prod_entry_dtls c, inv_transaction d
				where a.id=b.prod_id and b.dtls_id=c.id and a.current_stock>0 and a.item_category_id=14 and b.entry_form in(550) and b.trans_type=1 and b.po_breakdown_id in ($po_id) and b.trans_id!=0 and c.trans_id= d.id and d.store_id = '$cbo_store_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				group by a.id, a.product_name_details, a.current_stock,b.is_sales, d.floor_id, d.room, d.rack, d.self,b.po_breakdown_id, c.yarn_lot, c.yarn_count, c.stitch_length
				union all
				select a.id, a.product_name_details, a.current_stock,b.is_sales,0 as within_group, d.floor_id, d.room, d.rack, d.self,
				$field2, sum(b.quantity) as qnty
				from product_details_master a, order_wise_pro_details b, inv_item_transfer_dtls c, inv_transaction d
				where a.id=b.prod_id and b.dtls_id=c.id and a.current_stock>0 and a.item_category_id=14 and b.entry_form in(13,81) and b.trans_type=5 and c.item_category=14 and b.po_breakdown_id in ($po_id) and c.to_trans_id = d.id and d.store_id = '$cbo_store_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				group by a.id, a.product_name_details, a.current_stock, b.is_sales, d.floor_id, d.room, d.rack, d.self,b.po_breakdown_id, c.yarn_lot, c.y_count, c.stitch_length
				union all 
				select a.id, a.product_name_details, a.current_stock,b.is_sales,0 as within_group, c.floor_id, c.room, c.rack, c.self,
				$field3, sum(b.quantity) as qnty 
				from product_details_master a, order_wise_pro_details b, inv_transaction c
				where a.id=b.prod_id and b.trans_id=c.id and a.current_stock>0 and a.item_category_id=14 and c.transaction_type=4 and b.entry_form=579 and b.trans_type=4 and c.item_category=14 and c.store_id = '$cbo_store_name' and b.po_breakdown_id in ($po_id) and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details, a.current_stock,b.is_sales, c.floor_id, c.room, c.rack, c.self,b.po_breakdown_id, c.batch_lot,	c.yarn_count, c.stitch_length
			) product_details_master
			group by id, product_name_details, current_stock, po_id, floor_id, room, rack, self, yarn_lot, yarn_count, stitch_length, is_sales, within_group";

			if($db_type==0)
			{
				$program_data=sql_select("select b.prod_id, c.po_breakdown_id, group_concat(a.booking_id) as program_no from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and a.receive_basis=2 and c.entry_form=2 and c.trans_type=1 and c.po_breakdown_id in ($po_id) and c.status_active=1 and c.is_deleted=0 group by b.prod_id, c.po_breakdown_id");
			}
			else
			{
				$program_data=sql_select("select b.prod_id, c.po_breakdown_id, LISTAGG(cast(a.booking_id as VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY a.booking_id) as program_no from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and a.receive_basis=2 and c.entry_form=2 and c.trans_type=1 and c.po_breakdown_id in ($po_id) and c.status_active=1 and c.is_deleted=0 group by b.prod_id, c.po_breakdown_id");
			}

			foreach($program_data as $pRow)
			{
				$program_no_arr[$pRow[csf('prod_id')]][$pRow[csf('po_breakdown_id')]]=$pRow[csf('program_no')];
			}
		}
	}
	else if(($po_id=="" || $po_id==0) && ($cbo_issue_purpose==8 || $cbo_issue_purpose==3 || $cbo_issue_purpose==26 || $cbo_issue_purpose==29 || $cbo_issue_purpose==30 || $cbo_issue_purpose==31))
	{
		if($db_type==0)
		{
			$recv_id=return_field_value("group_concat(distinct(id)) as id","inv_receive_master","booking_without_order=1 and booking_id=$txt_booking_id and status_active=1 and is_deleted=0 and entry_form in(2)","id");
		}
		else
		{
			$recv_id=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as id","inv_receive_master","booking_without_order=1 and booking_id=$txt_booking_id and status_active=1 and is_deleted=0 and entry_form in(2)","id");
		}

		if($recv_id=="")
		{
			$sql = "SELECT id, product_name_details, current_stock, floor_id, room ,rack, self, yarn_lot, yarn_count, stitch_length, sum(qnty) as qnty, sum(qnty2) as qnty2,is_sales,within_group from 
			(
				SELECT a.id, a.product_name_details, a.current_stock, d.floor_id, d.room, d.rack, d.self, c.yarn_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length, sum(c.grey_receive_qnty) as qnty, 0 as qnty2,NULL as is_sales,b.within_group
				from product_details_master a, inv_receive_master b, pro_grey_prod_entry_dtls c, inv_transaction d
				where a.id=c.prod_id and b.id=c.mst_id and a.current_stock>0 and a.item_category_id=14 and b.entry_form in(2,550) and b.booking_without_order=1 and c.trans_id!=0 and b.booking_id=$txt_booking_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.trans_id = d.id and d.store_id = '$cbo_store_name'
				group by a.id, a.product_name_details, a.current_stock, b.booking_id, c.yarn_lot, c.yarn_count, c.stitch_length, d.floor_id, d.room, d.rack, d.self, b.within_group
				union all
				SELECT a.id, a.product_name_details, a.current_stock, c.floor_id, c.room, c.rack, c.self, c.batch_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length, sum(c.cons_quantity) as qnty, 0 as qnty2 ,NULL as is_sales,b.within_group
				from product_details_master a, inv_receive_master b, inv_transaction c
				where a.id=c.prod_id and b.id=c.mst_id and a.current_stock>0 and a.item_category_id=14 and b.entry_form=579 and b.receive_purpose=8 and c.item_category=14 and c.transaction_type=4 and b.booking_id=$txt_booking_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.store_id = '$cbo_store_name'
				group by a.id, a.product_name_details, a.current_stock, b.booking_id, c.batch_lot, c.yarn_count, c.stitch_length, c.floor_id, c.room, c.rack, c.self, b.within_group
				union all
				SELECT a.id, a.product_name_details, a.current_stock, d.floor_id, d.room, d.rack, d.self, c.yarn_lot as yarn_lot, c.y_count as yarn_count, c.stitch_length, sum(c.transfer_qnty) as qnty, sum(c.transfer_qnty) as qnty2,NULL as is_sales,0 as within_group
				from inv_item_transfer_mst b, inv_item_transfer_dtls c, product_details_master a, inv_transaction d
				where b.id=c.mst_id and c.from_prod_id=a.id and a.current_stock>0 and a.item_category_id=14 and b.entry_form in(80,432) and c.item_category=14 and b.to_order_id=$txt_booking_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.to_trans_id = d.id and d.store_id ='$cbo_store_name'
				group by a.id, a.product_name_details, a.current_stock, c.yarn_lot, c.y_count, c.stitch_length, d.floor_id, d.room, d.rack, d.self 
			)
			product_details_master 
			group by id, product_name_details, current_stock, floor_id, room, rack, self, yarn_lot, yarn_count, stitch_length,is_sales,within_group ";
		}
		else
		{
			$all_booking_id=$txt_booking_id.",".$recv_id;
			$sql = "SELECT id, product_name_details, current_stock, floor_id, room, rack, self, yarn_lot, yarn_count, stitch_length, sum(qnty) as qnty, sum(qnty2) as qnty2,is_sales,within_group  from (
			SELECT a.id, a.product_name_details, a.current_stock, d.floor_id, d.room, d.rack, d.self, c.yarn_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length, sum(case when b.receive_basis!=9 and b.booking_id=$txt_booking_id then c.grey_receive_qnty else 0 end ) as qnty, sum(case when b.receive_basis=9 and b.booking_id in($recv_id) then c.grey_receive_qnty else 0 end) as qnty2,NULL as is_sales,b.within_group
			from product_details_master a, inv_receive_master b, pro_grey_prod_entry_dtls c, inv_transaction d
			where a.id=c.prod_id and b.id=c.mst_id and a.current_stock>0 and a.item_category_id=14 and b.entry_form in(2,550) and b.booking_without_order=1 and c.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.booking_id in ($all_booking_id) and c.trans_id=d.id and d.store_id = '$cbo_store_name' group by a.id, a.product_name_details, a.current_stock, c.yarn_lot, c.yarn_count, c.stitch_length, d.floor_id, d.room, d.rack, d.self, b.within_group
			union all
			SELECT a.id, a.product_name_details, a.current_stock, c.floor_id, c.room, c.rack, c.self, c.batch_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length, sum(c.cons_quantity) as qnty, 0 as qnty2,NULL as is_sales,b.within_group
			from product_details_master a, inv_receive_master b, inv_transaction c
			where a.id=c.prod_id and b.id=c.mst_id and a.current_stock>0 and a.item_category_id=14 and b.entry_form=579 and b.receive_purpose=8 and c.item_category=14 and c.transaction_type=4 and b.booking_id=$txt_booking_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.store_id = '$cbo_store_name' group by a.id, a.product_name_details, a.current_stock, c.batch_lot, c.yarn_count, c.stitch_length,c.floor_id, c.room, c.rack, c.self, b.within_group
			union all
			SELECT a.id, a.product_name_details, a.current_stock, d.floor_id, d.room, d.rack, d.self, c.yarn_lot as yarn_lot, c.y_count as yarn_count, c.stitch_length, sum(c.transfer_qnty) as qnty, sum(c.transfer_qnty) as qnty2,NULL as is_sales,0 as within_group
			from inv_item_transfer_mst b, inv_item_transfer_dtls c, product_details_master a, inv_transaction d
			where b.id=c.mst_id and c.from_prod_id=a.id and a.current_stock>0 and a.item_category_id=14 and c.item_category=14 and b.transfer_criteria in(6,8) and b.to_order_id=$txt_booking_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.to_trans_id=d.id and d.store_id ='$cbo_store_name'
			group by a.id, a.product_name_details, a.current_stock, c.yarn_lot, c.y_count, c.stitch_length, d.floor_id, d.room, d.rack, d.self )
			product_details_master
			group by id, product_name_details, current_stock,floor_id,room, rack, self, yarn_lot, yarn_count, stitch_length,is_sales,within_group ";
		}
	}
	else
	{
		echo "No Order Found Against this Booking/Program No.";die;
	}
	//echo $sql;
	$result = sql_select($sql);
	if(count($result)<1) {echo "No Production Found.";die;}

	$yarn_count = return_library_array("select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$buyer_arr = return_library_array( "select id, short_name from lib_buyer",'id','short_name');

	$po_array=array(); $grey_iss_array=array();
	if (($cbo_issue_purpose==8 || $cbo_issue_purpose==3 || $cbo_issue_purpose==26 || $cbo_issue_purpose==29 || $cbo_issue_purpose==30 || $cbo_issue_purpose==31) && $cbo_basis==1)
	{

		$po_sql=sql_select("select a.id, a.buyer_id as buyer_name, b.style_id as style_ref_no, '' as po_number, '' as job_no from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$cbo_company_id and a.id=$txt_booking_id");

		if($recv_id=="")
		{
			$iss_sql=sql_select("SELECT c.prod_id as id, b.booking_id, d.floor_id, d.room, d.rack, d.self, c.yarn_lot, c.yarn_count, c.stitch_length, sum(c.issue_qnty) as qnty
				from inv_issue_master b, inv_grey_fabric_issue_dtls c, inv_transaction d
				where b.id=c.mst_id and b.item_category=14 and b.entry_form=577 and b.issue_purpose in(3,8,26,29,30,31) and b.issue_basis=1 and b.booking_id=$txt_booking_id and b.status_active=1 and b.is_deleted=0 and c.trans_id = d.id and d.status_active=1 and d.store_id='$cbo_store_name'
				group by c.prod_id, b.booking_id, c.yarn_lot, c.yarn_count, c.stitch_length, d.floor_id, d.room, d.rack, d.self
				union all
				select c.prod_id as id, d.booking_id, c.floor_id, c.room, c.rack, c.self, c.batch_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length, sum(c.cons_quantity) as qnty
				from inv_issue_master b, inv_transaction c, inv_receive_master d
				where b.id=c.mst_id and b.received_id=d.id and d.booking_id=$txt_booking_id and d.booking_without_order=1 and b.item_category=14 and b.entry_form=578 and c.transaction_type=3 and c.item_category=14 and c.store_id ='$cbo_store_name' group by c.prod_id, d.booking_id, c.batch_lot, c.yarn_count, c.stitch_length,c.floor_id,c.room, c.rack, c.self
				union all
				select c.from_prod_id as id, b.from_order_id as booking_id, d.floor_id, d.room, d.rack, d.self, c.yarn_lot as yarn_lot, c.y_count as yarn_count, c.stitch_length, sum(c.transfer_qnty) as qnty
				from inv_item_transfer_mst b, inv_item_transfer_dtls c, inv_transaction d
				where b.id=c.mst_id and b.entry_form=81 and c.item_category=14 and b.from_order_id=$txt_booking_id and b.status_active=1 and b.is_deleted=0 and c.trans_id = d.id and d.store_id ='$cbo_store_name'
				group by c.from_prod_id, b.from_order_id, c.yarn_lot, c.y_count, c.stitch_length,d.floor_id, d.room, d.rack, d.self");
		}
		else
		{
			$iss_sql=sql_select("SELECT c.prod_id as id, b.booking_id, d.floor_id, d.room, d.rack, d.self, c.yarn_lot, c.yarn_count, c.stitch_length, sum(c.issue_qnty) as qnty
				from inv_issue_master b, inv_grey_fabric_issue_dtls c, inv_transaction d
				where b.id=c.mst_id and b.item_category=14 and b.entry_form=577 and b.issue_purpose in(3,8,26,29,30,31) and b.issue_basis=1 and b.booking_id=$txt_booking_id and b.status_active=1 and b.is_deleted=0 and c.trans_id = d.id and d.status_active=1 and d.store_id='$cbo_store_name' 
				group by c.prod_id, b.booking_id, c.yarn_lot, c.yarn_count, c.stitch_length, d.floor_id, d.room, d.rack, d.self
				union all
				select c.prod_id as id, d.booking_id, c.floor_id, c.room, c.rack, c.self, c.batch_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length, sum(c.cons_quantity) as qnty from inv_issue_master b, inv_transaction c, inv_receive_master d where b.id=c.mst_id and b.received_id=d.id and ((d.booking_id=$txt_booking_id and d.receive_basis!=9) or (d.booking_id in ($recv_id) and d.receive_basis=9)) and d.booking_without_order=1 and b.item_category=14 and b.entry_form=578 and c.transaction_type=3 and c.item_category=14 and c.store_id ='$cbo_store_name' group by c.prod_id, d.booking_id, c.batch_lot, c.yarn_count, c.stitch_length, c.floor_id, c.room, c.rack, c.self
				union all
				select c.from_prod_id as id, b.from_order_id as booking_id, d.floor_id, d.room, d.rack, d.self, c.yarn_lot as yarn_lot, c.y_count as yarn_count, c.stitch_length, sum(c.transfer_qnty) as qnty
				from inv_item_transfer_mst b, inv_item_transfer_dtls c, inv_transaction d
				where b.id=c.mst_id and b.entry_form=81 and c.item_category=14 and b.from_order_id=$txt_booking_id and b.status_active=1 and b.is_deleted=0 and c.trans_id = d.id and d.store_id ='$cbo_store_name' group by c.from_prod_id, b.from_order_id, c.yarn_lot, c.y_count, c.stitch_length, d.floor_id, d.room, d.rack, d.self");
		}

		foreach($iss_sql as $row)
		{
			$grey_iss_array[$row[csf('id')]][$txt_booking_id][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]]+=$row[csf('qnty')];
		}
	}
	else if($cbo_issue_purpose==8 && $cbo_basis==3) // Issue Purpose: Sample Without Order and Issue Basis 3: Kniting Plan
	{
		$iss_sql=sql_select("SELECT c.prod_id as id, b.booking_id, d.floor_id, d.room, d.rack, d.self, c.yarn_lot, c.yarn_count, c.stitch_length, sum(c.issue_qnty) as qnty
		from inv_issue_master b, inv_grey_fabric_issue_dtls c, inv_transaction d
		where b.id=c.mst_id and b.item_category=14 and b.entry_form=577 and b.issue_purpose in(3,8,26,29,30,31) and b.issue_basis=3 and b.booking_id=$txt_booking_id and b.status_active=1 and b.is_deleted=0 and c.trans_id = d.id and d.status_active=1 and d.store_id='$cbo_store_name' 
		group by c.prod_id, b.booking_id, c.yarn_lot, c.yarn_count, c.stitch_length, d.floor_id, d.room, d.rack, d.self
		union all
		select c.prod_id as id, d.booking_id, c.floor_id, c.room, c.rack, c.self, c.batch_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length, sum(c.cons_quantity) as qnty from inv_issue_master b, inv_transaction c, inv_receive_master d where b.id=c.mst_id and b.received_id=d.id and ((d.booking_id=$txt_booking_id and d.receive_basis!=9) or (d.booking_id in ($recv_id) and d.receive_basis=9)) and d.booking_without_order=1 and b.item_category=14 and b.entry_form=578 and c.transaction_type=3 and c.item_category=14 and c.store_id ='$cbo_store_name' group by c.prod_id, d.booking_id, c.batch_lot, c.yarn_count, c.stitch_length, c.floor_id, c.room, c.rack, c.self
		union all
		select c.from_prod_id as id, b.from_order_id as booking_id, d.floor_id, d.room, d.rack, d.self, c.yarn_lot as yarn_lot, c.y_count as yarn_count, c.stitch_length, sum(c.transfer_qnty) as qnty
		from inv_item_transfer_mst b, inv_item_transfer_dtls c, inv_transaction d
		where b.id=c.mst_id and b.entry_form=81 and c.item_category=14 and b.from_order_id=$txt_booking_id and b.status_active=1 and b.is_deleted=0 and c.trans_id = d.id and d.store_id ='$cbo_store_name' group by c.from_prod_id, b.from_order_id, c.yarn_lot, c.y_count, c.stitch_length, d.floor_id, d.room, d.rack, d.self");

		foreach($iss_sql as $row)
		{
			$grey_iss_array[$row[csf('id')]][$txt_booking_id][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]]+=$row[csf('qnty')];
		}
	}
	else
	{
		if($cbo_basis==3)
		{
			$sales_no_sql=sql_select("select id, job_no, buyer_id, customer_buyer, within_group, po_job_no, style_ref_no from fabric_sales_order_mst where id in($po_id) and status_active=1 and is_deleted=0");
			foreach ($sales_no_sql as $row) 
			{
				$sales_no_arr[$row[csf('id')]]["job_no"]=$row[csf('job_no')];
				$sales_no_arr[$row[csf('id')]]["buyer_id"]=$row[csf('buyer_id')];
				$sales_no_arr[$row[csf('id')]]["po_job_no"]=$row[csf('po_job_no')];
				$sales_no_arr[$row[csf('id')]]["style_ref_no"]=$row[csf('style_ref_no')];

				if ($row[csf('within_group')]==1) 
				{
					$sales_no_arr[$row[csf('id')]]["buyer_id"]=$row[csf('customer_buyer')];
				}
				else 
				{
					$sales_no_arr[$row[csf('id')]]["buyer_id"]=$row[csf('buyer_id')];
				}
			}
		}

		$po_sql=sql_select("select a.buyer_name, a.job_no, a.style_ref_no, b.id, b.po_number
		from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c
		where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.company_name=$cbo_company_id and c.booking_no='$txt_booking_no'");

		if($db_type==0)
		{
			$field = "group_concat(b.po_breakdown_id) po_id,
			group_concat(c.yarn_lot) yarn_lot,
			group_concat(c.yarn_count) yarn_count,
			group_concat(c.stitch_length) stitch_length";
			$field2 = "group_concat(b.po_breakdown_id) po_id,
			group_concat(c.yarn_lot) yarn_lot,
			group_concat(c.y_count) yarn_count,
			group_concat(c.stitch_length) stitch_length";
			$field3 = "group_concat(b.po_breakdown_id) po_id,
			group_concat(c.batch_lot) yarn_lot,
			group_concat(c.yarn_count) yarn_count,
			group_concat(c.stitch_length) stitch_length";
		}
		else
		{
			$field = "
			listagg(b.po_breakdown_id, ',') within group (order by b.po_breakdown_id) as po_id,
			listagg(c.yarn_lot, ',') within group (order by c.yarn_lot) as yarn_lot,
			listagg(c.yarn_count, ',') within group (order by c.yarn_count) as yarn_count,
			listagg(c.stitch_length, ',') within group (order by c.stitch_length) as stitch_length";
			$field2 = "
			listagg(b.po_breakdown_id, ',') within group (order by b.po_breakdown_id) as po_id,
			listagg(c.yarn_lot, ',') within group (order by c.yarn_lot) as yarn_lot,
			listagg(c.y_count, ',') within group (order by c.y_count) as yarn_count,
			listagg(c.stitch_length, ',') within group (order by c.stitch_length) as stitch_length";
			$field3 = "
			listagg(b.po_breakdown_id, ',') within group (order by b.po_breakdown_id) as po_id,
			listagg(c.batch_lot, ',') within group (order by c.batch_lot) as yarn_lot,
			listagg(c.yarn_count, ',') within group (order by c.yarn_count) as yarn_count,
			listagg(c.stitch_length, ',') within group (order by c.stitch_length) as stitch_length";
		}
		$iss_sql=sql_select("SELECT c.prod_id as id, d.floor_id, d.room, d.rack, d.self,
			$field,
			sum(b.quantity) as qnty
			from order_wise_pro_details b, inv_grey_fabric_issue_dtls c, inv_transaction d
			where b.dtls_id=c.id and b.entry_form=577 and b.trans_type=2 and b.po_breakdown_id in ($po_id) and b.status_active=1 and b.is_deleted=0 and c.trans_id = d.id and d.status_active=1 and d.store_id = '$cbo_store_name'
			group by c.prod_id, d.floor_id, d.room, d.rack, d.self
			union all
			select b.prod_id as id, d.floor_id, d.room, d.rack, d.self,
			$field2,
			sum(b.quantity) as qnty
			from order_wise_pro_details b, inv_item_transfer_dtls c, inv_transaction d
			where b.dtls_id=c.id and b.entry_form in(13,80) and b.trans_type=6 and c.item_category=14 and b.po_breakdown_id in ($po_id) and b.status_active=1 and b.is_deleted=0 and c.trans_id = d.id and d.store_id= '$cbo_store_name'
			group by b.prod_id, d.floor_id, d.room, d.rack, d.self
			union all
			select b.prod_id as id, c.floor_id, c.room, c.rack, c.self,
			$field3,
			sum(b.quantity) as qnty
			from order_wise_pro_details b, inv_transaction c
			where b.trans_id=c.id and c.transaction_type=3 and b.entry_form=578 and b.trans_type=3 and c.item_category=14 and b.po_breakdown_id in ($po_id) and b.status_active=1 and b.is_deleted=0 and c.store_id= '$cbo_store_name'
			group by b.prod_id, c.floor_id, c.room, c.rack, c.self");

		
		foreach($iss_sql as $row)
		{
			$stitch_length = implode(",",array_unique(explode(",",$row[csf('stitch_length')])));
			$grey_iss_array[$row[csf('id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]] += $row[csf('qnty')];
		}
	}
	

	foreach($po_sql as $row)
	{
		$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
		$po_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
		$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
	}

	echo "<div align=\"center\" style=\"width:100%\">";
	echo "<input type=\"hidden\" id=\"txt_selected_id\" />\n";

	$isRackBalance=1;
	//variable ignore intensionally with consult with rashel vai.
	if($isRackBalance==1)
	{
		$width="1205";
		$column='<th width="60">Shelf</th><th>Balance Qty.</th>';
	}
	else
	{
		$width="1145";
		$column='<th>Shelf</th>';
	}

	$lib_room_rack_shelf_sql = "select b.company_id,b.location_id,b.store_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name floor_name,c.floor_room_rack_name room_name,d.floor_room_rack_name rack_name,e.floor_room_rack_name shelf_name from lib_floor_room_rack_dtls b
	 	left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
	 	left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
	 	left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
	 	left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
	 	where b.status_active=1 and b.is_deleted=0 and b.company_id=$cbo_company_id";
	 	$lib_room_rack_shelf_arr=sql_select($lib_room_rack_shelf_sql);
	 	foreach ($lib_room_rack_shelf_arr as $room_rack_shelf_row) 
	 	{
	 		$company  = $room_rack_shelf_row[csf("company_id")];
	 		$floor_id = $room_rack_shelf_row[csf("floor_id")];
	 		$room_id  = $room_rack_shelf_row[csf("room_id")];
	 		$rack_id  = $room_rack_shelf_row[csf("rack_id")];
	 		$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
	 		$bin_id   = $room_rack_shelf_row[csf("bin_id")];

	 		if($floor_id!="" && $room_id=="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
	 			$lib_floor_arr[$floor_id] = $room_rack_shelf_row[csf("floor_name")];
	 		}

	 		if($floor_id!="" && $room_id!="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
	 			$lib_room_arr[$floor_id][$room_id] = $room_rack_shelf_row[csf("room_name")];
	 		}

	 		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id=="" && $bin_id==""){
	 			$lib_rack_arr[$floor_id][$room_id][$rack_id] = $room_rack_shelf_row[csf("rack_name")];
	 		}

	 		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id==""){
	 			$lib_shelf_arr[$floor_id][$room_id][$rack_id][$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
	 		}
	 	}

		?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width; ?>" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="60">Prod. Id</th>
			<th width="130">Item Description</th>
			<th width="80">Order No/FSO No</th>
			<th width="50">Buyer</th>
			<th width="80">Job</th>
			<th width="90">Style Ref.</th>
			<th width="60">Prog. No</th>
			<th width="60">Stitch Length</th>
			<th width="70">Yarn Lot</th>
			<th width="90">Count</th>
			<th width="90">Floor</th>
			<th width="90">Room</th>
			<th width="60">Rack</th>
			<? echo $column; ?>
		</thead>
	</table>
	<div style="width:<? echo $width; ?>px; max-height:310px; overflow-y:scroll" id="container_batch" >
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width-20; ?>" class="rpt_table" id="tbl_search" >
			<?

			foreach ($result as $row)
			{
				$rcv_string = $row[csf('floor_id')]."==".$row[csf('room')]."==".$row[csf('rack')]."==".$row[csf('self')]."==".$row[csf('is_sales')]."==".$row[csf('within_group')];
				$rcvDataArr[$row[csf('id')]][$rcv_string]["product_name_details"] = $row[csf('product_name_details')];
				$rcvDataArr[$row[csf('id')]][$rcv_string]["current_stock"] = $row[csf('current_stock')];
				$rcvDataArr[$row[csf('id')]][$rcv_string]["po_id"] .= $row[csf('po_id')].",";
				$rcvDataArr[$row[csf('id')]][$rcv_string]["yarn_lot"] .= $row[csf('yarn_lot')].",";
				$rcvDataArr[$row[csf('id')]][$rcv_string]["yarn_count"] .= $row[csf('yarn_count')].",";
				$rcvDataArr[$row[csf('id')]][$rcv_string]["stitch_length"] .= $row[csf('stitch_length')].",";
				$rcvDataArr[$row[csf('id')]][$rcv_string]["qnty"] += $row[csf('qnty')];
				$rcvDataArr[$row[csf('id')]][$rcv_string]["qnty2"] += $row[csf('qnty2')];
				$rcvDataArr[$row[csf('id')]][$rcv_string]["id"] = $row[csf('id')];
				$rcvDataArr[$row[csf('id')]][$rcv_string]["is_sales"] = $row[csf('is_sales')];
				$rcvDataArr[$row[csf('id')]][$rcv_string]["within_group"] = $row[csf('within_group')];
			}

			$i=1;
			foreach ($rcvDataArr as $rcv_product_id=>$rcv_product_data)
			{
				foreach ($rcv_product_data as $recvProdStr => $row)
				{
					$recvProdStrARR=explode("==",$recvProdStr);
					$floorID = $recvProdStrARR[0];
					$roomID = $recvProdStrARR[1];
					$rackID = $recvProdStrARR[2];
					$shelfID = $recvProdStrARR[3];
					$is_sales = $recvProdStrARR[4];
					$within_group = $recvProdStrARR[5];


					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$iss_qnty=0; $program_no='';
					
					/* $floorID=$row[csf('floor_id')];
					$roomID=$row[csf('room')];
					$rackID=$row[csf('rack')];
					$shelfID=$row[csf('self')]; */
					
					$floorName = $lib_floor_arr[$floorID];
					$roomName = $lib_room_arr[$floorID][$roomID];
					$rackName = $lib_rack_arr[$floorID][$roomID][$rackID];
					$shelfName = $lib_shelf_arr[$floorID][$roomID][$rackID][$shelfID];

					$recv_qnty=$row['qnty']+$row['qnty2'];
					$stritch_length = implode(",",array_unique(explode(",",chop($row['stitch_length'],','))));

					if(($cbo_issue_purpose==8 || $cbo_issue_purpose==3 || $cbo_issue_purpose==26 || $cbo_issue_purpose==29 || $cbo_issue_purpose==30 || $cbo_issue_purpose==31) && $cbo_basis==1)
					{
						$row[csf('booking_id')]=$txt_booking_id;
						// balance is showing against sample booking bokking Id is different when receive basis production
						$job_arr="";
						$style_ref=$po_array[$row[csf('booking_id')]]['style_ref'];
						$buyer_name=$buyer_arr[$po_array[$row[csf('booking_id')]]['buyer']];
						$iss_qnty=$grey_iss_array[$row[csf('id')]][$row[csf('booking_id')]][$floorID][$roomID][$rackID][$shelfID];
					}
					else if($cbo_issue_purpose==8 && $cbo_basis==3)
					{
						$row[csf('booking_id')]=$txt_booking_id;
						$job_arr="";
						$style_ref=$po_array[$row[csf('booking_id')]]['style_ref'];
						$buyer_name=$buyer_arr[$po_array[$row[csf('booking_id')]]['buyer']];
						$iss_qnty=$grey_iss_array[$row[csf('id')]][$row[csf('booking_id')]][$floorID][$roomID][$rackID][$shelfID];
					}
					else
					{
						$po_id=array_unique(explode(",",$row['po_id']));
						$po_no=''; $saler_order_no=''; $job_no_array=array(); $buyer_name='';  $sales_buyer_name=''; $style_ref='';$saler_job_no='';$saler_style_no='';

						if($cbo_basis==3)
						{
							$program_no=$txt_booking_no;
						}

						foreach($po_id as $val)
						{
							if($po_no=='') $po_no=$po_array[$val]['no']; else $po_no.=", ".$po_array[$val]['no'];
							
							if($saler_order_no=='') $saler_order_no=$sales_no_arr[$val]["job_no"]; else $saler_order_no.=", ".$sales_no_arr[$val]["job_no"];
							if($saler_job_no=='') $saler_job_no=$sales_no_arr[$val]["po_job_no"]; else $saler_job_no.=", ".$sales_no_arr[$val]["po_job_no"];
							if($saler_style_no=='') $saler_style_no=$sales_no_arr[$val]["style_ref_no"]; else $saler_style_no.=", ".$sales_no_arr[$val]["style_ref_no"];

							if($sales_buyer_name=='') $sales_buyer_name=$buyer_arr[$sales_no_arr[$val]["buyer_id"]]; else $sales_buyer_name.=",".$buyer_arr[$sales_no_arr[$val]["buyer_id"]];

							if(!in_array($po_array[$val]['job_no'],$job_no_array))
							{
								$job_no_array[]=$po_array[$val]['job_no'];
								if($buyer_name=='') $buyer_name=$buyer_arr[$po_array[$val]['buyer']]; else $buyer_name.=",".$buyer_arr[$po_array[$val]['buyer']];
								if($style_ref=='') $style_ref=$po_array[$val]['style_ref']; else $style_ref.=",".$po_array[$val]['style_ref'];
							}

							if($cbo_basis!=3)
							{
								if($program_no_arr[$row['id']][$val]>0)
								{
									$program_no.=$program_no_arr[$row['id']][$val].",";
								}
							}
						}
						$job_arr=implode(",",$job_no_array);
						$iss_qnty=$grey_iss_array[$row['id']][$floorID][$roomID][$rackID][$shelfID];;
						$program_no=implode(",",array_unique(explode(",",chop($program_no,','))));
					}

					$count=''; $count_id=array_unique(explode(",",chop($row['yarn_count'],',')));
					foreach($count_id as $val)
					{
						if($count=='') $count=$yarn_count[$val]; else $count.=",".$yarn_count[$val];
					}

					$avgRate=$stock_arr[$row['id']];
					$yarn_lot=implode(",",array_unique(explode(",",chop($row['yarn_lot'],','))));
					$data=$row['id']."_".$row['product_name_details']."_".$yarn_lot."_".implode(",",$count_id)."_".$rackID."_".$shelfID."_".$row['current_stock']."_".$stritch_length."_".$avgRate."_".$floorID."_".$roomID."_".$floorName."_".$roomName."_".$rackName."_".$shelfName;

					$buyer_name=implode(",",array_unique(explode(",",$buyer_name)));
					$balance_qnty=$recv_qnty-$iss_qnty;
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $data; ?>')">
						<td width="30"><? echo $i; ?></td>
						<td width="60"><? echo $row['id']; ?></td>
						<td width="130"><p><? echo $row['product_name_details']; ?></p></td>
						<td width="80"><p>
							<? 
							if($row['is_sales']==1)
							{
								echo chop($saler_order_no,', ');
							}
							else
							{
								echo $po_no;
							} 
							?></p>
						</td>
						<td width="50"><p>&nbsp;
							<? 
							if($row['is_sales']==1)
							{ 
								echo chop($sales_buyer_name,', ');
							}
							else
							{ 
								echo $buyer_name;
							} 
							?></p>
						</td>
						<td width="80"><p><? if($row['is_sales']==1) echo chop($saler_job_no,', '); else echo $job_arr; ?>&nbsp;</p></td>
						<td width="90"><p><? if($row['is_sales']==1) echo chop($saler_style_no,', '); else echo $style_ref; ?>&nbsp;</p></td>
						<td width="60"><p><? echo $program_no; ?>&nbsp;</p></td>
						<td width="60"><p><? echo $stritch_length; ?>&nbsp;</p></td>
						<td width="70"><p><? echo $yarn_lot; ?>&nbsp;</p></td>
						<td width="90"><p><? echo $count; ?>&nbsp;</p></td>
						<td width="90"><p><? echo $floorName; ?>&nbsp;</p></td>
						<td width="90"><p><? echo $roomName; ?>&nbsp;</p></td>
						<td width="60"><p><? echo $rackName ?>&nbsp;</p></td>
						<?
						if($isRackBalance==1)
						{
							?>
							<td width="60"><p><? echo $shelfName; ?>&nbsp;</p></td>
							<td align="right" title="<? echo 'rcv='.$recv_qnty.',iss='.$iss_qnty;?>"><? echo number_format($balance_qnty,2); ?>&nbsp;</td>
							<?
						}
						else
						{
							?>
							<td><p><? echo $shelfName; ?>&nbsp;</p></td>
							<?
						}
						?>
					</tr>
					<?
					$i++;
				}
			}




			/* $i=1;
			foreach ($result as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$iss_qnty=0; $program_no='';
				
				$floorID=$row[csf('floor_id')];
				$roomID=$row[csf('room')];
				$rackID=$row[csf('rack')];
				$shelfID=$row[csf('self')];
				
		 		$floorName = $lib_floor_arr[$floorID];
		 		$roomName = $lib_room_arr[$floorID][$roomID];
		 		$rackName = $lib_rack_arr[$floorID][$roomID][$rackID];
		 		$shelfName = $lib_shelf_arr[$floorID][$roomID][$rackID][$shelfID];

				$recv_qnty=$row[csf('qnty')]+$row[csf('qnty2')];
				$stritch_length = implode(",",array_unique(explode(",",$row[csf('stitch_length')])));

				if(($cbo_issue_purpose==8 || $cbo_issue_purpose==3 || $cbo_issue_purpose==26 || $cbo_issue_purpose==29 || $cbo_issue_purpose==30 || $cbo_issue_purpose==31) && $cbo_basis==1)
				{
					$row[csf('booking_id')]=$txt_booking_id;
					// balance is showing against sample booking bokking Id is different when receive basis production
					$job_arr="";
					$style_ref=$po_array[$row[csf('booking_id')]]['style_ref'];
					$buyer_name=$buyer_arr[$po_array[$row[csf('booking_id')]]['buyer']];
					$iss_qnty=$grey_iss_array[$row[csf('id')]][$row[csf('booking_id')]][$floorID][$roomID][$rackID][$shelfID];
				}
				else if($cbo_issue_purpose==8 && $cbo_basis==3)
				{
					$row[csf('booking_id')]=$txt_booking_id;
					$job_arr="";
					$style_ref=$po_array[$row[csf('booking_id')]]['style_ref'];
					$buyer_name=$buyer_arr[$po_array[$row[csf('booking_id')]]['buyer']];
					$iss_qnty=$grey_iss_array[$row[csf('id')]][$row[csf('booking_id')]][$floorID][$roomID][$rackID][$shelfID];
				}
				else
				{
					$po_id=array_unique(explode(",",$row[csf('po_id')]));
					$po_no=''; $saler_order_no=''; $job_no_array=array(); $buyer_name='';  $sales_buyer_name=''; $style_ref='';

					if($cbo_basis==3)
					{
						$program_no=$txt_booking_no;
					}

					foreach($po_id as $val)
					{
						if($po_no=='') $po_no=$po_array[$val]['no']; else $po_no.=", ".$po_array[$val]['no'];
						if($saler_order_no=='') $saler_order_no=$sales_no_arr[$val]["job_no"]; else $saler_order_no.=", ".$sales_no_arr[$val]["job_no"];
						if($sales_buyer_name=='') $sales_buyer_name=$buyer_arr[$sales_no_arr[$val]["buyer_id"]]; else $sales_buyer_name.=",".$buyer_arr[$sales_no_arr[$val]["buyer_id"]];

						if(!in_array($po_array[$val]['job_no'],$job_no_array))
						{
							$job_no_array[]=$po_array[$val]['job_no'];
							if($buyer_name=='') $buyer_name=$buyer_arr[$po_array[$val]['buyer']]; else $buyer_name.=",".$buyer_arr[$po_array[$val]['buyer']];
							if($style_ref=='') $style_ref=$po_array[$val]['style_ref']; else $style_ref.=",".$po_array[$val]['style_ref'];
						}

						if($cbo_basis!=3)
						{
							if($program_no_arr[$row[csf('id')]][$val]>0)
							{
								$program_no.=$program_no_arr[$row[csf('id')]][$val].",";
							}
						}
					}
					$job_arr=implode(",",$job_no_array);
					$iss_qnty=$grey_iss_array[$row[csf('id')]][$floorID][$roomID][$rackID][$shelfID];;
					$program_no=implode(",",array_unique(explode(",",chop($program_no,','))));
				}

				$count=''; $count_id=array_unique(explode(",",$row[csf('yarn_count')]));
				foreach($count_id as $val)
				{
					if($count=='') $count=$yarn_count[$val]; else $count.=",".$yarn_count[$val];
				}

				$avgRate=$stock_arr[$row[csf('id')]];
				$yarn_lot=implode(",",array_unique(explode(",",$row[csf('yarn_lot')])));
				$data=$row[csf('id')]."_".$row[csf('product_name_details')]."_".$yarn_lot."_".implode(",",$count_id)."_".$rackID."_".$shelfID."_".$row[csf('current_stock')]."_".$stritch_length."_".$avgRate."_".$floorID."_".$roomID."_".$floorName."_".$roomName."_".$rackName."_".$shelfName;

				$buyer_name=implode(",",array_unique(explode(",",$buyer_name)));
				$balance_qnty=$recv_qnty-$iss_qnty;
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $data; ?>')">
					<td width="30"><? echo $i; ?></td>
					<td width="60"><? echo $row[csf('id')]; ?></td>
					<td width="130"><p><? echo $row[csf('product_name_details')]; ?></p></td>
					<td width="80"><p><? if($row[csf('is_sales')]==1 && $row[csf('within_group')]==2){echo $saler_order_no;}else{echo $po_no;} ?></p></td>
					<td width="50"><p>&nbsp;<? if($row[csf('is_sales')]==1 && $row[csf('within_group')]==2){ echo $sales_buyer_name;}else{ echo $buyer_name;} ?></p></td>
					<td width="80"><p><? echo $job_arr; ?>&nbsp;</p></td>
					<td width="90"><p><? echo $style_ref; ?>&nbsp;</p></td>
					<td width="60"><p><? echo $program_no; ?>&nbsp;</p></td>
					<td width="60"><p><? echo $stritch_length; ?>&nbsp;</p></td>
					<td width="70"><p><? echo $yarn_lot; ?>&nbsp;</p></td>
					<td width="90"><p><? echo $count; ?>&nbsp;</p></td>
					<td width="90"><p><? echo $floorName; ?>&nbsp;</p></td>
					<td width="90"><p><? echo $roomName; ?>&nbsp;</p></td>
					<td width="60"><p><? echo $rackName ?>&nbsp;</p></td>
					<?
					if($isRackBalance==1)
					{
						?>
						<td width="60"><p><? echo $shelfName; ?>&nbsp;</p></td>
						<td align="right" title="<? echo 'rcv='.$recv_qnty.',iss='.$iss_qnty;?>"><? echo number_format($balance_qnty,2); ?>&nbsp;</td>
						<?
					}
					else
					{
						?>
						<td><p><? echo $shelfName; ?>&nbsp;</p></td>
						<?
					}
					?>
				</tr>
				<?
				$i++;
			} */
		?>
		</table>
	</div>
	<?
	exit();

}

if($action=="itemDescription_popup__BK")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	if($hidden_order_id!="")
	{
		$sql_rate=sql_select("select b.prod_id,sum(c.quantity*b.cons_rate)/sum(c.quantity) as avg_rate from inv_receive_master a, inv_transaction b,order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id  and b.prod_id=c.prod_id and b.cons_amount>0 and a.item_category=14 and b.transaction_type=1 and a.entry_form in (2,550)  and c.entry_form in (2,550) and c.trans_type=1 and b.item_category=14 and c.po_breakdown_id in ($hidden_order_id) and b.status_active=1 and b.is_deleted=0 group by b.prod_id");
		foreach($sql_rate as $r_val)
		{
			$stock_arr[$r_val[csf('prod_id')]]=$r_val[csf('avg_rate')];
		}
	}
	else
	{
		$stock_arr = return_library_array( "select id, avg_rate_per_unit from product_details_master where item_category_id=14",'id','avg_rate_per_unit');
	}

	?>
	<script>
		function js_set_value(prod_id)
		{
			$("#txt_selected_id").val(prod_id);
			parent.emailwindow.hide();
		}

		$(document).ready(function(e) {
			setFilterGrid('tbl_search',-1);
		});
	</script>
	<?
	$po_id='';$prog_cond='';
	if($cbo_basis==1)
	{
		if($cbo_issue_purpose==4 || $cbo_issue_purpose==11)
		{
			$po_sql=sql_select("select po_break_down_id from wo_booking_dtls where booking_no='$txt_booking_no' and status_active=1");
			foreach($po_sql as $row)
			{
				if($po_id_check[$row[csf("po_break_down_id")]]=="")
				{
					$po_id_check[$row[csf("po_break_down_id")]]=$row[csf("po_break_down_id")];
					$po_id.=$row[csf("po_break_down_id")].",";
				}
			}
			$po_id=chop($po_id,",");
		}
	}
	else if($cbo_basis==3)
	{
		/*if($db_type==0)
		{
			$po_id=return_field_value("group_concat(distinct(po_id)) as po_id","ppl_planning_entry_plan_dtls","dtls_id='$txt_booking_no' and status_active=1 and is_deleted=0","po_id");
		}
		else
		{
			$po_id=return_field_value("LISTAGG(po_id, ',') WITHIN GROUP (ORDER BY po_id) as po_id","ppl_planning_entry_plan_dtls","dtls_id='$txt_booking_no' and status_active=1 and is_deleted=0","po_id");
			$po_id=implode(",",array_unique(explode(",",$po_id)));
		}*/
		$po_id=$hidden_order_id;
		/*$po_sql ="SELECT po_id from ppl_planning_entry_plan_dtls where dtls_id='$txt_booking_no' and status_active=1 and is_deleted=0 group by po_id
		union all
		select a.to_order_id as po_id from inv_item_transfer_mst a, inv_item_transfer_dtls b 
		where a.id=b.mst_id and b.to_program='$txt_booking_no' and a.status_active=1 and a.is_deleted=0 and a.entry_form in(13,362) group by a.to_order_id";
		// echo $po_sql;die;
		$sql_data_result = sql_select($po_sql);
		$po_arr=array();
	    foreach ($sql_data_result as $order_key => $row) 
	    {
	    	$po_arr[$row[csf('po_id')]]=$row[csf('po_id')];
	    }
	    $po_id = implode(',', array_unique(explode(',', implode(',', $po_arr))));*/

		$prog_cond = " and c.from_program = '$txt_booking_no'" ;
	}
	else
	{
		$po_id=$hidden_order_id;
	}
	//echo $po_id.'='.$cbo_issue_purpose.'**'.$txt_booking_id;die;
	if($po_id!="" && $po_id!=0)
	{
		// echo "string".$po_id;//0
		$program_no_arr=array();
		if($cbo_basis==3)
		{
			if($db_type==0)
			{
				$recv_id=return_field_value("group_concat(id) as id","inv_receive_master","booking_without_order=0 and booking_id=$txt_booking_no and status_active=1 and is_deleted=0 and entry_form in(2) and receive_basis=2","id");
			}
			else
			{
				$recv_id=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as id","inv_receive_master","booking_without_order=0 and booking_id=$txt_booking_no and status_active=1 and is_deleted=0 and entry_form in(2) and receive_basis=2","id");
			}
			if($recv_id=="") $recv_id=0;
			$all_booking_id=$txt_booking_no.",".$recv_id;
			if($db_type==0)
			{
				$sql = "select id, product_name_details, current_stock, po_id,floor_id, room, rack, self, yarn_lot, yarn_count, stitch_length, sum(qnty) as qnty, sum(qnty2) as qnty2,is_sales,within_group from ( select a.id, a.product_name_details, a.current_stock, group_concat(distinct(b.po_breakdown_id)) as po_id, e.floor_id, e.room,  e.rack, e.self, c.yarn_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length,
				sum(case when d.receive_basis=2 and d.booking_id=$txt_booking_no and b.entry_form=2 then b.quantity else 0 end ) as qnty,
				sum(case when d.receive_basis=9 and d.booking_id in($recv_id) and b.entry_form=550 then b.quantity else 0 end) as qnty2,b.is_sales,d.within_group
				from product_details_master a, inv_receive_master d, order_wise_pro_details b, pro_grey_prod_entry_dtls c, inv_transaction e where a.id=b.prod_id and b.dtls_id=c.id and d.id=c.mst_id and a.current_stock>0 and a.item_category_id=14 and b.entry_form in(2,550) and b.trans_type=1 and b.po_breakdown_id in ($po_id) and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.booking_id in ($all_booking_id) and c.trans_id = e.id and e.store_id = '$cbo_store_name' group by a.id, a.product_name_details, a.current_stock, c.yarn_lot, c.yarn_count, c.stitch_length, e.floor_id, e.room, e.rack, e.self, b.is_sales,d.within_group
				union all
				select a.id, a.product_name_details, a.current_stock, group_concat(distinct(b.po_breakdown_id)) as po_id, d.floor_id, d.room, d.rack, d.self, c.yarn_lot as yarn_lot, c.y_count as yarn_count, c.stitch_length, sum(b.quantity) as qnty, 0 as qnty2, b.is_sales, 0 as within_group from product_details_master a, order_wise_pro_details b, inv_item_transfer_dtls c, inv_transaction d where a.id=b.prod_id and b.dtls_id=c.id and a.current_stock>0 and a.item_category_id=14 and b.entry_form in(13,81) and b.trans_type=5 and c.item_category=14 and b.po_breakdown_id in ($po_id) and c.to_program in ($txt_booking_no) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.to_trans_id=d.id and d.store_id ='$cbo_store_name' group by a.id, a.product_name_details, a.current_stock, c.yarn_lot, c.y_count, c.stitch_length, d.floor_id, d.room, d.rack, d.self, b.is_sales
				union all
				select a.id, a.product_name_details, a.current_stock, group_concat(distinct(b.po_breakdown_id)) as po_id, c.floor_id, c.room, c.rack, c.self, c.batch_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length, sum(b.quantity) as qnty, 0 as qnty2,b.is_sales,0 as within_group from product_details_master a, order_wise_pro_details b, inv_transaction c where a.id=b.prod_id and b.trans_id=c.id and a.current_stock>0 and a.item_category_id=14 and c.transaction_type=4 and b.entry_form=579 and b.trans_type=4 and c.item_category=14 and b.po_breakdown_id in ($po_id) and b.status_active=1 and b.is_deleted=0 and c.store_id = '$cbo_store_name' group by b.prod_id, c.batch_lot, c.yarn_count, c.stitch_length, c.floor_id, c.room, c.rack, c.self, b.is_sales) product_details_master group by id, po_id, floor_id, room, rack, self, yarn_lot, yarn_count, stitch_length,is_sales,within_group";
			}
			else
			{
				$program_no_arr=array();
				if($cbo_basis==3)
				{
					if($db_type==0)
					{
						$recv_id=return_field_value("group_concat(id) as id","inv_receive_master","booking_without_order=0 and booking_id=$txt_booking_no and status_active=1 and is_deleted=0 and entry_form in(2) and receive_basis=2","id");
					}
					else
					{
						$recv_id=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as id","inv_receive_master","booking_without_order=0 and booking_id=$txt_booking_no and status_active=1 and is_deleted=0 and entry_form in(2) and receive_basis=2","id");
					}
					if($recv_id=="") $recv_id=0;
					$all_booking_id=$txt_booking_no.",".$recv_id;
					if($db_type==0)
					{
						$sql = "select id, product_name_details, current_stock, po_id, floor_id, room, rack, self, yarn_lot, yarn_count, stitch_length, sum(qnty) as qnty, sum(qnty2) as qnty2,is_sales,within_group from ( select a.id, a.product_name_details, a.current_stock, group_concat(distinct(b.po_breakdown_id)) as po_id, e.floor_id, e.room,  e.rack, e.self, c.yarn_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length,
						sum(case when d.receive_basis=2 and d.booking_id=$txt_booking_no and b.entry_form=2 then b.quantity else 0 end ) as qnty, sum(case when d.receive_basis=9 and d.booking_id in($recv_id) and b.entry_form=550 then b.quantity else 0 end) as qnty2,b.is_sales,d.within_group
						from product_details_master a, inv_receive_master d, order_wise_pro_details b, pro_grey_prod_entry_dtls c, inv_transaction e where a.id=b.prod_id and b.dtls_id=c.id and d.id=c.mst_id and a.current_stock>0 and a.item_category_id=14 and b.entry_form in(2,550) and b.trans_type=1 and b.po_breakdown_id in ($po_id) and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.booking_id in ($all_booking_id) and c.trans_id = e.id and e.store_id = '$cbo_store_name' group by a.id, a.product_name_details, a.current_stock, c.yarn_lot, c.yarn_count, c.stitch_length, e.floor_id, e.room,  e.rack, e.self, b.is_sales,d.within_group
						union all
						select a.id, a.product_name_details, a.current_stock, group_concat(distinct(b.po_breakdown_id)) as po_id, d.floor_id, d.room, d.rack, d.self, c.yarn_lot as yarn_lot, c.y_count as yarn_count, c.stitch_length, 0 as qnty, sum(b.quantity) as qnty2,b.is_sales,0 as within_group from product_details_master a, order_wise_pro_details b, inv_item_transfer_dtls c, inv_transaction d where a.id=b.prod_id and b.dtls_id=c.id and a.current_stock>0 and a.item_category_id=14 and b.entry_form in(13,81) and b.trans_type=5 and c.item_category=14 and b.po_breakdown_id in ($po_id) and c.to_program in ($txt_booking_no) and c.to_trans_id=d.id and d.store_id = '$cbo_store_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details, a.current_stock, c.yarn_lot, c.y_count, c.stitch_length, d.floor_id, d.room, d.rack, d.self, b.is_sales
						union all
						select a.id, a.product_name_details, a.current_stock, group_concat(distinct(b.po_breakdown_id)) as po_id, c.floor_id, c.room, c.rack, c.self, c.batch_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length, sum(b.quantity) as qnty, 0 as qnty2,b.is_sales,0 as within_group from product_details_master a, order_wise_pro_details b, inv_transaction c where a.id=b.prod_id and b.trans_id=c.id and a.current_stock>0 and a.item_category_id=14 and c.transaction_type=4 and b.entry_form=579 and b.trans_type=4 and c.item_category=14 and b.po_breakdown_id in ($po_id) and b.status_active=1 and b.is_deleted=0 and c.store_id = '$cbo_store_name' group by b.prod_id, c.batch_lot, c.yarn_count, c.stitch_length, c.floor_id, c.room, c.rack, c.self, b.is_sales) product_details_master group by id, po_id, floor_id,room, rack, self, yarn_lot, yarn_count, stitch_length,is_sales,within_group";
					}
					else
					{
						/*$sql = "select id, product_name_details, current_stock, po_id, floor_id, room, rack, self, yarn_lot, yarn_count, stitch_length, sum(qnty) as qnty, sum(qnty2) as qnty2,is_sales,within_group from (
						select a.id, a.product_name_details, a.current_stock,LISTAGG(b.po_breakdown_id, ',') WITHIN GROUP (ORDER BY b.po_breakdown_id) as po_id, e.floor_id, e.room,  e.rack, e.self, c.yarn_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length, sum(case when d.receive_basis=2 and d.booking_id=$txt_booking_no and b.entry_form=2 then b.quantity else 0 end ) as qnty,
						sum(case when d.receive_basis=9 and d.booking_id in($recv_id) and b.entry_form=550 then b.quantity else 0 end) as qnty2 ,b.is_sales,d.within_group
						from product_details_master a, inv_receive_master d, order_wise_pro_details b, pro_grey_prod_entry_dtls c, inv_transaction e where a.id=b.prod_id and b.dtls_id=c.id and d.id=c.mst_id and a.current_stock>0 and a.item_category_id=14 and b.entry_form in(2,550) and b.trans_type=1 and b.po_breakdown_id in ($po_id) and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.booking_id in ($all_booking_id) and c.trans_id = e.id and e.store_id = '$cbo_store_name' group by a.id, a.product_name_details, a.current_stock, c.yarn_lot, c.yarn_count, c.stitch_length, e.floor_id, e.room,  e.rack, e.self, b.is_sales,d.within_group
						union all
						select a.id, a.product_name_details, a.current_stock, LISTAGG(b.po_breakdown_id, ',') WITHIN GROUP (ORDER BY b.po_breakdown_id) as po_id, d.floor_id, d.room, d.rack, d.self, c.yarn_lot as yarn_lot, c.y_count as yarn_count, c.stitch_length, 0 as qnty, sum(b.quantity) as qnty2,b.is_sales,0 as within_group from product_details_master a, order_wise_pro_details b, inv_item_transfer_dtls c, inv_transaction d where a.id=b.prod_id and b.dtls_id=c.id and a.current_stock>0 and a.item_category_id=14 and b.entry_form in(13,81,362) and b.trans_type=5 and c.item_category=14 and b.po_breakdown_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.to_trans_id = d.id and d.store_id = '$cbo_store_name' group by a.id, a.product_name_details, a.current_stock, c.yarn_lot, c.y_count, c.stitch_length, d.floor_id, d.room, d.rack, d.self, b.is_sales
						union all
						select a.id, a.product_name_details, a.current_stock, LISTAGG(b.po_breakdown_id, ',') WITHIN GROUP (ORDER BY b.po_breakdown_id) as po_id, c.floor_id, c.room, c.rack, c.self, c.batch_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length, sum(b.quantity) as qnty, 0 as qnty2,b.is_sales,0 as within_group from product_details_master a, order_wise_pro_details b, inv_transaction c where a.id=b.prod_id and b.trans_id=c.id and a.current_stock>0 and a.item_category_id=14 and c.transaction_type=4 and b.entry_form=579 and b.trans_type=4 and c.item_category=14 and b.po_breakdown_id in ($po_id) and b.status_active=1 and b.is_deleted=0 and c.store_id = '$cbo_store_name' group by a.id, a.product_name_details, a.current_stock, c.batch_lot, c.yarn_count, c.stitch_length, c.floor_id, c.room, c.rack, c.self, b.is_sales ) product_details_master group by id, product_name_details, current_stock, po_id, floor_id, room, rack, self, yarn_lot, yarn_count, stitch_length,is_sales,within_group";*/

						$sql = "SELECT id, product_name_details, current_stock, LISTAGG(po_id, ',') WITHIN GROUP (ORDER BY po_id) as po_id, floor_id, room, rack, self, LISTAGG(yarn_lot, ',') WITHIN GROUP (ORDER BY yarn_lot) as yarn_lot, LISTAGG(yarn_count, ',') WITHIN GROUP (ORDER BY yarn_count) as yarn_count, LISTAGG(stitch_length, ',') WITHIN GROUP (ORDER BY stitch_length) as stitch_length, sum(qnty) as qnty, sum(qnty2) as qnty2,is_sales,within_group from (
						select a.id, a.product_name_details, a.current_stock,LISTAGG(b.po_breakdown_id, ',') WITHIN GROUP (ORDER BY b.po_breakdown_id) as po_id, e.floor_id, e.room,  e.rack, e.self, c.yarn_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length, sum(case when d.receive_basis=2 and d.booking_id=$txt_booking_no and b.entry_form=2 then b.quantity else 0 end ) as qnty,
						sum(case when d.receive_basis=9 and d.booking_id in($recv_id) and b.entry_form=550 then b.quantity else 0 end) as qnty2 ,b.is_sales,d.within_group
						from product_details_master a, inv_receive_master d, order_wise_pro_details b, pro_grey_prod_entry_dtls c, inv_transaction e where a.id=b.prod_id and b.dtls_id=c.id and d.id=c.mst_id and a.current_stock>0 and a.item_category_id=14 and b.entry_form in(2,550) and b.trans_type=1 and b.po_breakdown_id in ($po_id) and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.booking_id in ($all_booking_id) and c.trans_id = e.id and e.store_id = '$cbo_store_name' group by a.id, a.product_name_details, a.current_stock, c.yarn_lot, c.yarn_count, c.stitch_length, e.floor_id, e.room,  e.rack, e.self, b.is_sales,d.within_group
						union all
						select a.id, a.product_name_details, a.current_stock, LISTAGG(b.po_breakdown_id, ',') WITHIN GROUP (ORDER BY b.po_breakdown_id) as po_id, d.floor_id, d.room, d.rack, d.self, c.yarn_lot as yarn_lot, c.y_count as yarn_count, c.stitch_length, 0 as qnty, sum(b.quantity) as qnty2,b.is_sales,0 as within_group from product_details_master a, order_wise_pro_details b, inv_item_transfer_dtls c, inv_transaction d where a.id=b.prod_id and b.dtls_id=c.id and a.current_stock>0 and a.item_category_id=14 and b.entry_form in(13,81,362) and b.trans_type=5 and c.item_category=14 and b.po_breakdown_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.to_trans_id = d.id and d.store_id = '$cbo_store_name' group by a.id, a.product_name_details, a.current_stock, c.yarn_lot, c.y_count, c.stitch_length, d.floor_id, d.room, d.rack, d.self, b.is_sales
						union all
						select a.id, a.product_name_details, a.current_stock, LISTAGG(b.po_breakdown_id, ',') WITHIN GROUP (ORDER BY b.po_breakdown_id) as po_id, c.floor_id, c.room, c.rack, c.self, c.batch_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length, sum(b.quantity) as qnty, 0 as qnty2,b.is_sales,0 as within_group from product_details_master a, order_wise_pro_details b, inv_transaction c where a.id=b.prod_id and b.trans_id=c.id and a.current_stock>0 and a.item_category_id=14 and c.transaction_type=4 and b.entry_form=579 and b.trans_type=4 and c.item_category=14 and b.po_breakdown_id in ($po_id) and b.status_active=1 and b.is_deleted=0 and c.store_id = '$cbo_store_name' group by a.id, a.product_name_details, a.current_stock, c.batch_lot, c.yarn_count, c.stitch_length, c.floor_id, c.room, c.rack, c.self, b.is_sales ) product_details_master group by id, product_name_details, current_stock, floor_id, room, rack, self, is_sales,within_group";
					}
				}
				else
				{
					if($db_type==0)
					{//======================================
						$sql = "select id, product_name_details, current_stock, po_id,floor_id,room, rack, self, yarn_lot, yarn_count, stitch_length, sum(qnty) as qnty, sum(qnty2) as qnty2, is_sales, within_group 
						from
						( select a.id, a.product_name_details, a.current_stock, group_concat(distinct(b.po_breakdown_id)) as po_id, d.floor_id, d.room, d.rack, d.self, c.yarn_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length, sum(case when b.entry_form=2 then b.quantity else 0 end ) as qnty,
      						sum(case when b.entry_form=550 then b.quantity else 0 end ) as qnty2, b.is_sales, NULL as within_group
      						from product_details_master a, order_wise_pro_details b, pro_grey_prod_entry_dtls c, inv_transaction d where a.id=b.prod_id and b.dtls_id=c.id and a.current_stock>0 and a.item_category_id=14 and b.entry_form in(2,550) and b.trans_type=1 and b.po_breakdown_id in ($po_id) and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.trans_id = d.id and d.status_active = 1 and d.store_id = '$cbo_store_name'
						group by a.id, a.product_name_details, a.current_stock, c.yarn_lot, c.yarn_count, c.stitch_length, d.floor_id, d.room, d.rack, d.self, b.is_sales
						union all
						select a.id, a.product_name_details, a.current_stock, group_concat(distinct(b.po_breakdown_id)) as po_id, c.floor_id, c.room, c.to_rack as rack, c.to_shelf as self, c.yarn_lot as yarn_lot, c.y_count as yarn_count, c.stitch_length, sum(b.quantity) as qnty, sum(b.quantity) as qnty2, b.is_sales,NULL as within_group from product_details_master a, order_wise_pro_details b, inv_item_transfer_dtls c, inv_transaction d where a.id=b.prod_id and b.dtls_id=c.id and a.current_stock>0 and a.item_category_id=14 and b.entry_form in(13,81) and b.trans_type=5 and c.item_category=14 and b.po_breakdown_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.trans_id = d.id and d.status_active=1 and d.store_id = '$cbo_store_name' 
						group by a.id, a.product_name_details, a.current_stock, c.yarn_lot, c.y_count, c.stitch_length, c.floor_id, c.room, c.to_rack, c.to_shelf, b.is_sales
						union all
						select a.id, a.product_name_details, a.current_stock, group_concat(distinct(b.po_breakdown_id)) as po_id,c.floor_id,c.room, c.rack, c.self, c.batch_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length, sum(b.quantity) as qnty, sum(b.quantity) as qnty2,b.is_sales,NULL as within_group from product_details_master a, order_wise_pro_details b, inv_transaction c where a.id=b.prod_id and b.trans_id=c.id and a.current_stock>0 and a.item_category_id=14 and c.transaction_type=4 and b.entry_form=579 and b.trans_type=4 and c.item_category=14 and b.po_breakdown_id in ($po_id) and b.status_active=1 and b.is_deleted=0
						group by b.prod_id, c.batch_lot, c.yarn_count, c.stitch_length,c.floor_id,c.room, c.rack, c.self,b.is_sales
						) product_details_master
						group by id, po_id, floor_id, room, rack, self, yarn_lot, yarn_count, stitch_length ,is_sales,within_group";
					}
					else
					{
						if($db_type==0)
						{
							$field = "group_concat(b.po_breakdown_id) po_id,
							group_concat(c.yarn_lot) yarn_lot,
							group_concat(c.yarn_count) yarn_count,
							group_concat(c.stitch_length) stitch_length";
							$field2 = "group_concat(b.po_breakdown_id) po_id,
							group_concat(c.yarn_lot) yarn_lot,
							group_concat(c.y_count) yarn_count,
							group_concat(c.stitch_length) stitch_length";
							$field3 = "group_concat(b.po_breakdown_id) po_id,
							group_concat(c.batch_lot) yarn_lot,
							group_concat(c.yarn_count) yarn_count,
							group_concat(c.stitch_length) stitch_length";
						}
						else
						{
							$field = "listagg(b.po_breakdown_id, ',') within group (order by b.po_breakdown_id) as po_id,
							listagg(cast(c.yarn_lot as varchar(4000)), ',') within group (order by c.yarn_lot) as yarn_lot,
							listagg(cast(c.yarn_count as varchar(4000)), ',') within group (order by c.yarn_count) as yarn_count,
							listagg(cast(c.stitch_length as varchar(4000)), ',') within group (order by c.stitch_length) as stitch_length";
							$field2 = "listagg(b.po_breakdown_id, ',') within group (order by b.po_breakdown_id) as po_id,
							listagg(cast(c.yarn_lot as varchar(4000)), ',') within group (order by c.yarn_lot) as yarn_lot,
							listagg(cast(c.y_count as varchar(4000)), ',') within group (order by c.y_count) as yarn_count,
							listagg(cast(c.stitch_length as varchar(4000)), ',') within group (order by c.stitch_length) as stitch_length";
							$field3 = "listagg(b.po_breakdown_id, ',') within group (order by b.po_breakdown_id) as po_id,
							listagg(cast(c.batch_lot as varchar(4000)), ',') within group (order by c.batch_lot) as yarn_lot,
							listagg(cast(c.yarn_count as varchar(4000)), ',') within group (order by c.yarn_count) as yarn_count,
							listagg(cast(c.stitch_length as varchar(4000)), ',') within group (order by c.stitch_length) as stitch_length";
						}
						$sql="select id, product_name_details, current_stock, po_id, floor_id, room, rack, self, yarn_lot, yarn_count, stitch_length, sum(qnty) as qnty, sum(qnty2) as qnty2, is_sales, within_group
							from
							(
								select a.id, a.product_name_details, a.current_stock,b.is_sales,NULL as within_group, d.floor_id, d.room, d.rack, d.self,
								$field, sum(case when b.entry_form=2 then b.quantity else 0 end ) as qnty,
	      						sum(case when b.entry_form=550 then b.quantity else 0 end ) as qnty2
								from product_details_master a, order_wise_pro_details b, pro_grey_prod_entry_dtls c, inv_transaction d
								where a.id=b.prod_id and b.dtls_id=c.id and a.current_stock>0 and a.item_category_id=14 and b.entry_form in(2,550) and b.trans_type=1 and b.po_breakdown_id in ($po_id) and b.trans_id!=0
								and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.trans_id = d.id and d.status_active=1 and d.store_id = '$cbo_store_name'
								group by a.id, a.product_name_details, a.current_stock,b.is_sales, d.floor_id, d.room, d.rack, d.self
								union all
								select a.id, a.product_name_details, a.current_stock,b.is_sales,NULL as within_group, d.floor_id, d.room, d.rack, d.self,
								$field2, sum(b.quantity) as qnty, sum(b.quantity) as qnty2
								from product_details_master a, order_wise_pro_details b, inv_item_transfer_dtls c, inv_transaction d
								where a.id=b.prod_id and b.dtls_id=c.id and a.current_stock>0 and a.item_category_id=14 and b.entry_form in(13,81) and b.trans_type=5 and c.item_category=14 and b.po_breakdown_id in ($po_id)
								and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.trans_id = d.id and d.status_active=1 and d.store_id = '$cbo_store_name'
								group by a.id, a.product_name_details, a.current_stock,b.is_sales, d.floor_id, d.room, d.rack, d.self

								union all
								select a.id, a.product_name_details, a.current_stock,b.is_sales,NULL as within_group, c.floor_id, c.room, c.rack, c.self, 
								$field3, sum(b.quantity) as qnty, sum(b.quantity) as qnty2
								from product_details_master a, order_wise_pro_details b, inv_transaction c
								where a.id=b.prod_id and b.trans_id=c.id and a.current_stock>0 and a.item_category_id=14 and c.transaction_type=4 and b.entry_form=579 and b.trans_type=4 and c.item_category=14 			and b.po_breakdown_id in ($po_id) and c.store_id = '$cbo_store_name' and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details, a.current_stock,b.is_sales, c.floor_id, c.room, c.rack, c.self
							) product_details_master
							group by id, product_name_details, current_stock, po_id, floor_id, room, rack, self, yarn_lot, yarn_count, stitch_length,is_sales,within_group";
					}

					if($db_type==0)
					{
						$program_data=sql_select("select b.prod_id, c.po_breakdown_id, group_concat(a.booking_id) as program_no from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and a.receive_basis=2 and c.entry_form=2 and c.trans_type=1 and c.po_breakdown_id in ($po_id) and c.status_active=1 and c.is_deleted=0 group by b.prod_id, c.po_breakdown_id");
					}
					else
					{
						$program_data=sql_select("select b.prod_id, c.po_breakdown_id, LISTAGG(cast(a.booking_id as VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY a.booking_id) as program_no from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and a.receive_basis=2 and c.entry_form=2 and c.trans_type=1 and c.po_breakdown_id in ($po_id) and c.status_active=1 and c.is_deleted=0 group by b.prod_id, c.po_breakdown_id");
					}

					foreach($program_data as $pRow)
					{
						$program_no_arr[$pRow[csf('prod_id')]][$pRow[csf('po_breakdown_id')]]=$pRow[csf('program_no')];
					}

				}
			}
		}
		else
		{
			if($db_type==0)
			{
				$field = "group_concat(b.po_breakdown_id) po_id,
				group_concat(c.yarn_lot) yarn_lot,
				group_concat(c.yarn_count) yarn_count,
				group_concat(c.stitch_length) stitch_length";
				$field2 = "group_concat(b.po_breakdown_id) po_id,
				group_concat(c.yarn_lot) yarn_lot,
				group_concat(c.y_count) yarn_count,
				group_concat(c.stitch_length) stitch_length";
				$field3 = "group_concat(b.po_breakdown_id) po_id,
				group_concat(c.batch_lot) yarn_lot,
				group_concat(c.yarn_count) yarn_count,
				group_concat(c.stitch_length) stitch_length";
			}
			else
			{
				$field = "listagg(b.po_breakdown_id, ',') within group (order by b.po_breakdown_id) as po_id,
				listagg(cast(c.yarn_lot as varchar(4000)), ',') within group (order by c.yarn_lot) as yarn_lot,
				listagg(cast(c.yarn_count as varchar(4000)), ',') within group (order by c.yarn_count) as yarn_count,
				listagg(cast(c.stitch_length as varchar(4000)), ',') within group (order by c.stitch_length) as stitch_length";
				$field2 = "listagg(b.po_breakdown_id, ',') within group (order by b.po_breakdown_id) as po_id,
				listagg(cast(c.yarn_lot as varchar(4000)), ',') within group (order by c.yarn_lot) as yarn_lot,
				listagg(cast(c.y_count as varchar(4000)), ',') within group (order by c.y_count) as yarn_count,
				listagg(cast(c.stitch_length as varchar(4000)), ',') within group (order by c.stitch_length) as stitch_length";
				$field3 = "listagg(b.po_breakdown_id, ',') within group (order by b.po_breakdown_id) as po_id,
				listagg(cast(c.batch_lot as varchar(4000)), ',') within group (order by c.batch_lot) as yarn_lot,
				listagg(cast(c.yarn_count as varchar(4000)), ',') within group (order by c.yarn_count) as yarn_count,
				listagg(cast(c.stitch_length as varchar(4000)), ',') within group (order by c.stitch_length) as stitch_length";
			}
			$sql="select id, product_name_details, current_stock, po_id,floor_id,room, rack, self, yarn_lot, yarn_count, stitch_length, sum(qnty) as qnty,is_sales,within_group 
			from 
			(
				select a.id, a.product_name_details, a.current_stock,b.is_sales,0 as within_group, d.floor_id, d.room, d.rack, d.self,
				$field, sum(b.quantity) as qnty
				from product_details_master a, order_wise_pro_details b, pro_grey_prod_entry_dtls c, inv_transaction d
				where a.id=b.prod_id and b.dtls_id=c.id and a.current_stock>0 and a.item_category_id=14 and b.entry_form in(2,550) and b.trans_type=1 and b.po_breakdown_id in ($po_id) and b.trans_id!=0 and c.trans_id= d.id and d.store_id = '$cbo_store_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				group by a.id, a.product_name_details, a.current_stock,b.is_sales, d.floor_id, d.room, d.rack, d.self
				union all
				select a.id, a.product_name_details, a.current_stock,b.is_sales,0 as within_group, d.floor_id, d.room, d.rack, d.self,
				$field2, sum(b.quantity) as qnty
				from product_details_master a, order_wise_pro_details b, inv_item_transfer_dtls c, inv_transaction d
				where a.id=b.prod_id and b.dtls_id=c.id and a.current_stock>0 and a.item_category_id=14 and b.entry_form in(13,81) and b.trans_type=5 and c.item_category=14 and b.po_breakdown_id in ($po_id) and c.to_trans_id = d.id and d.store_id = '$cbo_store_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				group by a.id, a.product_name_details, a.current_stock, b.is_sales, d.floor_id, d.room, d.rack, d.self
				union all 
				select a.id, a.product_name_details, a.current_stock,b.is_sales,0 as within_group, c.floor_id, c.room, c.rack, c.self,
				$field3, sum(b.quantity) as qnty 
				from product_details_master a, order_wise_pro_details b, inv_transaction c
				where a.id=b.prod_id and b.trans_id=c.id and a.current_stock>0 and a.item_category_id=14 and c.transaction_type=4 and b.entry_form=579 and b.trans_type=4 and c.item_category=14 and c.store_id = '$cbo_store_name' and b.po_breakdown_id in ($po_id) and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details, a.current_stock,b.is_sales, c.floor_id, c.room, c.rack, c.self 
			) product_details_master
			group by id, product_name_details, current_stock, po_id, floor_id, room, rack, self, yarn_lot, yarn_count, stitch_length, is_sales, within_group";

			if($db_type==0)
			{
				$program_data=sql_select("select b.prod_id, c.po_breakdown_id, group_concat(a.booking_id) as program_no from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and a.receive_basis=2 and c.entry_form=2 and c.trans_type=1 and c.po_breakdown_id in ($po_id) and c.status_active=1 and c.is_deleted=0 group by b.prod_id, c.po_breakdown_id");
			}
			else
			{
				$program_data=sql_select("select b.prod_id, c.po_breakdown_id, LISTAGG(cast(a.booking_id as VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY a.booking_id) as program_no from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and a.receive_basis=2 and c.entry_form=2 and c.trans_type=1 and c.po_breakdown_id in ($po_id) and c.status_active=1 and c.is_deleted=0 group by b.prod_id, c.po_breakdown_id");
			}

			foreach($program_data as $pRow)
			{
				$program_no_arr[$pRow[csf('prod_id')]][$pRow[csf('po_breakdown_id')]]=$pRow[csf('program_no')];
			}
		}
	}
	else if(($po_id=="" || $po_id==0) && ($cbo_issue_purpose==8 || $cbo_issue_purpose==3 || $cbo_issue_purpose==26 || $cbo_issue_purpose==29 || $cbo_issue_purpose==30 || $cbo_issue_purpose==31))
	{
		if($db_type==0)
		{
			$recv_id=return_field_value("group_concat(distinct(id)) as id","inv_receive_master","booking_without_order=1 and booking_id=$txt_booking_id and status_active=1 and is_deleted=0 and entry_form in(2)","id");
		}
		else
		{
			$recv_id=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as id","inv_receive_master","booking_without_order=1 and booking_id=$txt_booking_id and status_active=1 and is_deleted=0 and entry_form in(2)","id");
		}

		if($recv_id=="")
		{
			$sql = "SELECT id, product_name_details, current_stock, floor_id, room ,rack, self, yarn_lot, yarn_count, stitch_length, sum(qnty) as qnty, sum(qnty2) as qnty2,is_sales,within_group from 
			(
				SELECT a.id, a.product_name_details, a.current_stock, d.floor_id, d.room, d.rack, d.self, c.yarn_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length, sum(c.grey_receive_qnty) as qnty, 0 as qnty2,NULL as is_sales,b.within_group
				from product_details_master a, inv_receive_master b, pro_grey_prod_entry_dtls c, inv_transaction d
				where a.id=c.prod_id and b.id=c.mst_id and a.current_stock>0 and a.item_category_id=14 and b.entry_form in(2,550) and b.booking_without_order=1 and c.trans_id!=0 and b.booking_id=$txt_booking_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.trans_id = d.id and d.store_id = '$cbo_store_name'
				group by a.id, a.product_name_details, a.current_stock, b.booking_id, c.yarn_lot, c.yarn_count, c.stitch_length, d.floor_id, d.room, d.rack, d.self, b.within_group
				union all
				SELECT a.id, a.product_name_details, a.current_stock, c.floor_id, c.room, c.rack, c.self, c.batch_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length, sum(c.cons_quantity) as qnty, 0 as qnty2 ,NULL as is_sales,b.within_group
				from product_details_master a, inv_receive_master b, inv_transaction c
				where a.id=c.prod_id and b.id=c.mst_id and a.current_stock>0 and a.item_category_id=14 and b.entry_form=579 and b.receive_purpose=8 and c.item_category=14 and c.transaction_type=4 and b.booking_id=$txt_booking_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.store_id = '$cbo_store_name'
				group by a.id, a.product_name_details, a.current_stock, b.booking_id, c.batch_lot, c.yarn_count, c.stitch_length, c.floor_id, c.room, c.rack, c.self, b.within_group
				union all
				SELECT a.id, a.product_name_details, a.current_stock, d.floor_id, d.room, d.rack, d.self, c.yarn_lot as yarn_lot, c.y_count as yarn_count, c.stitch_length, sum(c.transfer_qnty) as qnty, sum(c.transfer_qnty) as qnty2,NULL as is_sales,0 as within_group
				from inv_item_transfer_mst b, inv_item_transfer_dtls c, product_details_master a, inv_transaction d
				where b.id=c.mst_id and c.from_prod_id=a.id and a.current_stock>0 and a.item_category_id=14 and b.entry_form in(80,432) and c.item_category=14 and b.to_order_id=$txt_booking_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.to_trans_id = d.id and d.store_id ='$cbo_store_name'
				group by a.id, a.product_name_details, a.current_stock, c.yarn_lot, c.y_count, c.stitch_length, d.floor_id, d.room, d.rack, d.self 
			)
			product_details_master 
			group by id, product_name_details, current_stock, floor_id, room, rack, self, yarn_lot, yarn_count, stitch_length,is_sales,within_group ";
		}
		else
		{
			$all_booking_id=$txt_booking_id.",".$recv_id;
			$sql = "SELECT id, product_name_details, current_stock, floor_id, room, rack, self, yarn_lot, yarn_count, stitch_length, sum(qnty) as qnty, sum(qnty2) as qnty2,is_sales,within_group  from (
			SELECT a.id, a.product_name_details, a.current_stock, d.floor_id, d.room, d.rack, d.self, c.yarn_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length, sum(case when b.receive_basis!=9 and b.booking_id=$txt_booking_id then c.grey_receive_qnty else 0 end ) as qnty, sum(case when b.receive_basis=9 and b.booking_id in($recv_id) then c.grey_receive_qnty else 0 end) as qnty2,NULL as is_sales,b.within_group
			from product_details_master a, inv_receive_master b, pro_grey_prod_entry_dtls c, inv_transaction d
			where a.id=c.prod_id and b.id=c.mst_id and a.current_stock>0 and a.item_category_id=14 and b.entry_form in(2,550) and b.booking_without_order=1 and c.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.booking_id in ($all_booking_id) and c.trans_id=d.id and d.store_id = '$cbo_store_name' group by a.id, a.product_name_details, a.current_stock, c.yarn_lot, c.yarn_count, c.stitch_length, d.floor_id, d.room, d.rack, d.self, b.within_group
			union all
			SELECT a.id, a.product_name_details, a.current_stock, c.floor_id, c.room, c.rack, c.self, c.batch_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length, sum(c.cons_quantity) as qnty, 0 as qnty2,NULL as is_sales,b.within_group
			from product_details_master a, inv_receive_master b, inv_transaction c
			where a.id=c.prod_id and b.id=c.mst_id and a.current_stock>0 and a.item_category_id=14 and b.entry_form=579 and b.receive_purpose=8 and c.item_category=14 and c.transaction_type=4 and b.booking_id=$txt_booking_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.store_id = '$cbo_store_name' group by a.id, a.product_name_details, a.current_stock, c.batch_lot, c.yarn_count, c.stitch_length,c.floor_id, c.room, c.rack, c.self, b.within_group
			union all
			SELECT a.id, a.product_name_details, a.current_stock, d.floor_id, d.room, d.rack, d.self, c.yarn_lot as yarn_lot, c.y_count as yarn_count, c.stitch_length, sum(c.transfer_qnty) as qnty, sum(c.transfer_qnty) as qnty2,NULL as is_sales,0 as within_group
			from inv_item_transfer_mst b, inv_item_transfer_dtls c, product_details_master a, inv_transaction d
			where b.id=c.mst_id and c.from_prod_id=a.id and a.current_stock>0 and a.item_category_id=14 and c.item_category=14 and b.transfer_criteria in(6,8) and b.to_order_id=$txt_booking_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.to_trans_id=d.id and d.store_id ='$cbo_store_name'
			group by a.id, a.product_name_details, a.current_stock, c.yarn_lot, c.y_count, c.stitch_length, d.floor_id, d.room, d.rack, d.self )
			product_details_master
			group by id, product_name_details, current_stock,floor_id,room, rack, self, yarn_lot, yarn_count, stitch_length,is_sales,within_group ";
		}
	}
	else
	{
		echo "No Order Found Against this Booking/Program No.";die;
	}
	//echo $sql;
	$result = sql_select($sql);
	if(count($result)<1) {echo "No Production Found.";die;}

	$yarn_count = return_library_array("select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$buyer_arr = return_library_array( "select id, short_name from lib_buyer",'id','short_name');

	$po_array=array(); $grey_iss_array=array();
	if (($cbo_issue_purpose==8 || $cbo_issue_purpose==3 || $cbo_issue_purpose==26 || $cbo_issue_purpose==29 || $cbo_issue_purpose==30 || $cbo_issue_purpose==31) && $cbo_basis==1)
	{

		$po_sql=sql_select("select a.id, a.buyer_id as buyer_name, b.style_id as style_ref_no, '' as po_number, '' as job_no from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$cbo_company_id and a.id=$txt_booking_id");

		if($recv_id=="")
		{
			$iss_sql=sql_select("SELECT c.prod_id as id, b.booking_id, d.floor_id, d.room, d.rack, d.self, c.yarn_lot, c.yarn_count, c.stitch_length, sum(c.issue_qnty) as qnty
				from inv_issue_master b, inv_grey_fabric_issue_dtls c, inv_transaction d
				where b.id=c.mst_id and b.item_category=14 and b.entry_form=577 and b.issue_purpose in(3,8,26,29,30,31) and b.issue_basis=1 and b.booking_id=$txt_booking_id and b.status_active=1 and b.is_deleted=0 and c.trans_id = d.id and d.status_active=1 and d.store_id='$cbo_store_name'
				group by c.prod_id, b.booking_id, c.yarn_lot, c.yarn_count, c.stitch_length, d.floor_id, d.room, d.rack, d.self
				union all
				select c.prod_id as id, d.booking_id, c.floor_id, c.room, c.rack, c.self, c.batch_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length, sum(c.cons_quantity) as qnty
				from inv_issue_master b, inv_transaction c, inv_receive_master d
				where b.id=c.mst_id and b.received_id=d.id and d.booking_id=$txt_booking_id and d.booking_without_order=1 and b.item_category=14 and b.entry_form=578 and c.transaction_type=3 and c.item_category=14 and c.store_id ='$cbo_store_name' group by c.prod_id, d.booking_id, c.batch_lot, c.yarn_count, c.stitch_length,c.floor_id,c.room, c.rack, c.self
				union all
				select c.from_prod_id as id, b.from_order_id as booking_id, d.floor_id, d.room, d.rack, d.self, c.yarn_lot as yarn_lot, c.y_count as yarn_count, c.stitch_length, sum(c.transfer_qnty) as qnty
				from inv_item_transfer_mst b, inv_item_transfer_dtls c, inv_transaction d
				where b.id=c.mst_id and b.entry_form=81 and c.item_category=14 and b.from_order_id=$txt_booking_id and b.status_active=1 and b.is_deleted=0 and c.trans_id = d.id and d.store_id ='$cbo_store_name'
				group by c.from_prod_id, b.from_order_id, c.yarn_lot, c.y_count, c.stitch_length,d.floor_id, d.room, d.rack, d.self");
		}
		else
		{
			$iss_sql=sql_select("SELECT c.prod_id as id, b.booking_id, d.floor_id, d.room, d.rack, d.self, c.yarn_lot, c.yarn_count, c.stitch_length, sum(c.issue_qnty) as qnty
				from inv_issue_master b, inv_grey_fabric_issue_dtls c, inv_transaction d
				where b.id=c.mst_id and b.item_category=14 and b.entry_form=577 and b.issue_purpose in(3,8,26,29,30,31) and b.issue_basis=1 and b.booking_id=$txt_booking_id and b.status_active=1 and b.is_deleted=0 and c.trans_id = d.id and d.status_active=1 and d.store_id='$cbo_store_name' 
				group by c.prod_id, b.booking_id, c.yarn_lot, c.yarn_count, c.stitch_length, d.floor_id, d.room, d.rack, d.self
				union all
				select c.prod_id as id, d.booking_id, c.floor_id, c.room, c.rack, c.self, c.batch_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length, sum(c.cons_quantity) as qnty from inv_issue_master b, inv_transaction c, inv_receive_master d where b.id=c.mst_id and b.received_id=d.id and ((d.booking_id=$txt_booking_id and d.receive_basis!=9) or (d.booking_id in ($recv_id) and d.receive_basis=9)) and d.booking_without_order=1 and b.item_category=14 and b.entry_form=578 and c.transaction_type=3 and c.item_category=14 and c.store_id ='$cbo_store_name' group by c.prod_id, d.booking_id, c.batch_lot, c.yarn_count, c.stitch_length, c.floor_id, c.room, c.rack, c.self
				union all
				select c.from_prod_id as id, b.from_order_id as booking_id, d.floor_id, d.room, d.rack, d.self, c.yarn_lot as yarn_lot, c.y_count as yarn_count, c.stitch_length, sum(c.transfer_qnty) as qnty
				from inv_item_transfer_mst b, inv_item_transfer_dtls c, inv_transaction d
				where b.id=c.mst_id and b.entry_form=81 and c.item_category=14 and b.from_order_id=$txt_booking_id and b.status_active=1 and b.is_deleted=0 and c.trans_id = d.id and d.store_id ='$cbo_store_name' group by c.from_prod_id, b.from_order_id, c.yarn_lot, c.y_count, c.stitch_length, d.floor_id, d.room, d.rack, d.self");
		}

		foreach($iss_sql as $row)
		{
			$grey_iss_array[$row[csf('id')]][$txt_booking_id][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]]+=$row[csf('qnty')];
		}
	}
	else if($cbo_issue_purpose==8 && $cbo_basis==3) // Issue Purpose: Sample Without Order and Issue Basis 3: Kniting Plan
	{
		$iss_sql=sql_select("SELECT c.prod_id as id, b.booking_id, d.floor_id, d.room, d.rack, d.self, c.yarn_lot, c.yarn_count, c.stitch_length, sum(c.issue_qnty) as qnty
		from inv_issue_master b, inv_grey_fabric_issue_dtls c, inv_transaction d
		where b.id=c.mst_id and b.item_category=14 and b.entry_form=577 and b.issue_purpose in(3,8,26,29,30,31) and b.issue_basis=3 and b.booking_id=$txt_booking_id and b.status_active=1 and b.is_deleted=0 and c.trans_id = d.id and d.status_active=1 and d.store_id='$cbo_store_name' 
		group by c.prod_id, b.booking_id, c.yarn_lot, c.yarn_count, c.stitch_length, d.floor_id, d.room, d.rack, d.self
		union all
		select c.prod_id as id, d.booking_id, c.floor_id, c.room, c.rack, c.self, c.batch_lot as yarn_lot, c.yarn_count as yarn_count, c.stitch_length, sum(c.cons_quantity) as qnty from inv_issue_master b, inv_transaction c, inv_receive_master d where b.id=c.mst_id and b.received_id=d.id and ((d.booking_id=$txt_booking_id and d.receive_basis!=9) or (d.booking_id in ($recv_id) and d.receive_basis=9)) and d.booking_without_order=1 and b.item_category=14 and b.entry_form=578 and c.transaction_type=3 and c.item_category=14 and c.store_id ='$cbo_store_name' group by c.prod_id, d.booking_id, c.batch_lot, c.yarn_count, c.stitch_length, c.floor_id, c.room, c.rack, c.self
		union all
		select c.from_prod_id as id, b.from_order_id as booking_id, d.floor_id, d.room, d.rack, d.self, c.yarn_lot as yarn_lot, c.y_count as yarn_count, c.stitch_length, sum(c.transfer_qnty) as qnty
		from inv_item_transfer_mst b, inv_item_transfer_dtls c, inv_transaction d
		where b.id=c.mst_id and b.entry_form=81 and c.item_category=14 and b.from_order_id=$txt_booking_id and b.status_active=1 and b.is_deleted=0 and c.trans_id = d.id and d.store_id ='$cbo_store_name' group by c.from_prod_id, b.from_order_id, c.yarn_lot, c.y_count, c.stitch_length, d.floor_id, d.room, d.rack, d.self");

		foreach($iss_sql as $row)
		{
			$grey_iss_array[$row[csf('id')]][$txt_booking_id][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]]+=$row[csf('qnty')];
		}
	}
	else
	{
		if($cbo_basis==3){
			$sales_no_sql=sql_select("select id,job_no,buyer_id from fabric_sales_order_mst where within_group=2 and id in($po_id) and status_active=1 and is_deleted=0");
			foreach ($sales_no_sql as $row) {
				$sales_no_arr[$row[csf('id')]]["job_no"]=$row[csf('job_no')];
				$sales_no_arr[$row[csf('id')]]["buyer_id"]=$row[csf('buyer_id')];
			}
		}

		$po_sql=sql_select("select a.buyer_name, a.job_no, a.style_ref_no, b.id, b.po_number
		from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c
		where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.company_name=$cbo_company_id and c.booking_no='$txt_booking_no'");

		if($db_type==0)
		{
			$field = "group_concat(b.po_breakdown_id) po_id,
			group_concat(c.yarn_lot) yarn_lot,
			group_concat(c.yarn_count) yarn_count,
			group_concat(c.stitch_length) stitch_length";
			$field2 = "group_concat(b.po_breakdown_id) po_id,
			group_concat(c.yarn_lot) yarn_lot,
			group_concat(c.y_count) yarn_count,
			group_concat(c.stitch_length) stitch_length";
			$field3 = "group_concat(b.po_breakdown_id) po_id,
			group_concat(c.batch_lot) yarn_lot,
			group_concat(c.yarn_count) yarn_count,
			group_concat(c.stitch_length) stitch_length";
		}
		else
		{
			$field = "
			listagg(b.po_breakdown_id, ',') within group (order by b.po_breakdown_id) as po_id,
			listagg(c.yarn_lot, ',') within group (order by c.yarn_lot) as yarn_lot,
			listagg(c.yarn_count, ',') within group (order by c.yarn_count) as yarn_count,
			listagg(c.stitch_length, ',') within group (order by c.stitch_length) as stitch_length";
			$field2 = "
			listagg(b.po_breakdown_id, ',') within group (order by b.po_breakdown_id) as po_id,
			listagg(c.yarn_lot, ',') within group (order by c.yarn_lot) as yarn_lot,
			listagg(c.y_count, ',') within group (order by c.y_count) as yarn_count,
			listagg(c.stitch_length, ',') within group (order by c.stitch_length) as stitch_length";
			$field3 = "
			listagg(b.po_breakdown_id, ',') within group (order by b.po_breakdown_id) as po_id,
			listagg(c.batch_lot, ',') within group (order by c.batch_lot) as yarn_lot,
			listagg(c.yarn_count, ',') within group (order by c.yarn_count) as yarn_count,
			listagg(c.stitch_length, ',') within group (order by c.stitch_length) as stitch_length";
		}
		$iss_sql=sql_select("SELECT c.prod_id as id, d.floor_id, d.room, d.rack, d.self,
			$field,
			sum(b.quantity) as qnty
			from order_wise_pro_details b, inv_grey_fabric_issue_dtls c, inv_transaction d
			where b.dtls_id=c.id and b.entry_form=577 and b.trans_type=2 and b.po_breakdown_id in ($po_id) and b.status_active=1 and b.is_deleted=0 and c.trans_id = d.id and d.status_active=1 and d.store_id = '$cbo_store_name'
			group by c.prod_id, d.floor_id, d.room, d.rack, d.self
			union all
			select b.prod_id as id, d.floor_id, d.room, d.rack, d.self,
			$field2,
			sum(b.quantity) as qnty
			from order_wise_pro_details b, inv_item_transfer_dtls c, inv_transaction d
			where b.dtls_id=c.id and b.entry_form in(13,80) and b.trans_type=6 and c.item_category=14 and b.po_breakdown_id in ($po_id) and b.status_active=1 and b.is_deleted=0 and c.trans_id = d.id and d.store_id= '$cbo_store_name'
			group by b.prod_id, d.floor_id, d.room, d.rack, d.self
			union all
			select b.prod_id as id, c.floor_id, c.room, c.rack, c.self,
			$field3,
			sum(b.quantity) as qnty
			from order_wise_pro_details b, inv_transaction c
			where b.trans_id=c.id and c.transaction_type=3 and b.entry_form=578 and b.trans_type=3 and c.item_category=14 and b.po_breakdown_id in ($po_id) and b.status_active=1 and b.is_deleted=0 and c.store_id= '$cbo_store_name'
			group by b.prod_id, c.floor_id, c.room, c.rack, c.self");

		
		foreach($iss_sql as $row)
		{
			$stitch_length = implode(",",array_unique(explode(",",$row[csf('stitch_length')])));
			$grey_iss_array[$row[csf('id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]] += $row[csf('qnty')];
		}
	}
	

	foreach($po_sql as $row)
	{
		$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
		$po_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
		$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
	}

	echo "<div align=\"center\" style=\"width:100%\">";
	echo "<input type=\"hidden\" id=\"txt_selected_id\" />\n";

	$isRackBalance=1;
	//variable ignore intensionally with consult with rashel vai.
	if($isRackBalance==1)
	{
		$width="1205";
		$column='<th width="60">Shelf</th><th>Balance Qty.</th>';
	}
	else
	{
		$width="1145";
		$column='<th>Shelf</th>';
	}

	$lib_room_rack_shelf_sql = "select b.company_id,b.location_id,b.store_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name floor_name,c.floor_room_rack_name room_name,d.floor_room_rack_name rack_name,e.floor_room_rack_name shelf_name from lib_floor_room_rack_dtls b
	 	left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
	 	left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
	 	left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
	 	left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
	 	where b.status_active=1 and b.is_deleted=0 and b.company_id=$cbo_company_id";
	 	$lib_room_rack_shelf_arr=sql_select($lib_room_rack_shelf_sql);
	 	foreach ($lib_room_rack_shelf_arr as $room_rack_shelf_row) 
	 	{
	 		$company  = $room_rack_shelf_row[csf("company_id")];
	 		$floor_id = $room_rack_shelf_row[csf("floor_id")];
	 		$room_id  = $room_rack_shelf_row[csf("room_id")];
	 		$rack_id  = $room_rack_shelf_row[csf("rack_id")];
	 		$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
	 		$bin_id   = $room_rack_shelf_row[csf("bin_id")];

	 		if($floor_id!="" && $room_id=="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
	 			$lib_floor_arr[$floor_id] = $room_rack_shelf_row[csf("floor_name")];
	 		}

	 		if($floor_id!="" && $room_id!="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
	 			$lib_room_arr[$floor_id][$room_id] = $room_rack_shelf_row[csf("room_name")];
	 		}

	 		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id=="" && $bin_id==""){
	 			$lib_rack_arr[$floor_id][$room_id][$rack_id] = $room_rack_shelf_row[csf("rack_name")];
	 		}

	 		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id==""){
	 			$lib_shelf_arr[$floor_id][$room_id][$rack_id][$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
	 		}
	 	}

		?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width; ?>" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="60">Prod. Id</th>
			<th width="130">Item Description</th>
			<th width="80">Order No/FSO No</th>
			<th width="50">Buyer</th>
			<th width="80">Job</th>
			<th width="90">Style Ref.</th>
			<th width="60">Prog. No</th>
			<th width="60">Stitch Length</th>
			<th width="70">Yarn Lot</th>
			<th width="90">Count</th>
			<th width="90">Floor</th>
			<th width="90">Room</th>
			<th width="60">Rack</th>
			<? echo $column; ?>
		</thead>
	</table>
	<div style="width:<? echo $width; ?>px; max-height:310px; overflow-y:scroll" id="container_batch" >
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width-20; ?>" class="rpt_table" id="tbl_search" >
			<?
			$i=1;
			foreach ($result as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$iss_qnty=0; $program_no='';
				
				$floorID=$row[csf('floor_id')];
				$roomID=$row[csf('room')];
				$rackID=$row[csf('rack')];
				$shelfID=$row[csf('self')];
				
		 		$floorName = $lib_floor_arr[$floorID];
		 		$roomName = $lib_room_arr[$floorID][$roomID];
		 		$rackName = $lib_rack_arr[$floorID][$roomID][$rackID];
		 		$shelfName = $lib_shelf_arr[$floorID][$roomID][$rackID][$shelfID];

				$recv_qnty=$row[csf('qnty')]+$row[csf('qnty2')];
				$stritch_length = implode(",",array_unique(explode(",",$row[csf('stitch_length')])));

				if(($cbo_issue_purpose==8 || $cbo_issue_purpose==3 || $cbo_issue_purpose==26 || $cbo_issue_purpose==29 || $cbo_issue_purpose==30 || $cbo_issue_purpose==31) && $cbo_basis==1)
				{
					$row[csf('booking_id')]=$txt_booking_id;
					// balance is showing against sample booking bokking Id is different when receive basis production
					$job_arr="";
					$style_ref=$po_array[$row[csf('booking_id')]]['style_ref'];
					$buyer_name=$buyer_arr[$po_array[$row[csf('booking_id')]]['buyer']];
					$iss_qnty=$grey_iss_array[$row[csf('id')]][$row[csf('booking_id')]][$floorID][$roomID][$rackID][$shelfID];
				}
				else if($cbo_issue_purpose==8 && $cbo_basis==3)
				{
					$row[csf('booking_id')]=$txt_booking_id;
					$job_arr="";
					$style_ref=$po_array[$row[csf('booking_id')]]['style_ref'];
					$buyer_name=$buyer_arr[$po_array[$row[csf('booking_id')]]['buyer']];
					$iss_qnty=$grey_iss_array[$row[csf('id')]][$row[csf('booking_id')]][$floorID][$roomID][$rackID][$shelfID];
				}
				else
				{
					$po_id=array_unique(explode(",",$row[csf('po_id')]));
					$po_no=''; $saler_order_no=''; $job_no_array=array(); $buyer_name='';  $sales_buyer_name=''; $style_ref='';

					if($cbo_basis==3)
					{
						$program_no=$txt_booking_no;
					}

					foreach($po_id as $val)
					{
						if($po_no=='') $po_no=$po_array[$val]['no']; else $po_no.=", ".$po_array[$val]['no'];
						if($saler_order_no=='') $saler_order_no=$sales_no_arr[$val]["job_no"]; else $saler_order_no.=", ".$sales_no_arr[$val]["job_no"];
						if($sales_buyer_name=='') $sales_buyer_name=$buyer_arr[$sales_no_arr[$val]["buyer_id"]]; else $sales_buyer_name.=",".$buyer_arr[$sales_no_arr[$val]["buyer_id"]];

						if(!in_array($po_array[$val]['job_no'],$job_no_array))
						{
							$job_no_array[]=$po_array[$val]['job_no'];
							if($buyer_name=='') $buyer_name=$buyer_arr[$po_array[$val]['buyer']]; else $buyer_name.=",".$buyer_arr[$po_array[$val]['buyer']];
							if($style_ref=='') $style_ref=$po_array[$val]['style_ref']; else $style_ref.=",".$po_array[$val]['style_ref'];
						}

						if($cbo_basis!=3)
						{
							if($program_no_arr[$row[csf('id')]][$val]>0)
							{
								$program_no.=$program_no_arr[$row[csf('id')]][$val].",";
							}
						}
					}
					$job_arr=implode(",",$job_no_array);
					$iss_qnty=$grey_iss_array[$row[csf('id')]][$floorID][$roomID][$rackID][$shelfID];;
					$program_no=implode(",",array_unique(explode(",",chop($program_no,','))));
				}

				$count=''; $count_id=array_unique(explode(",",$row[csf('yarn_count')]));
				foreach($count_id as $val)
				{
					if($count=='') $count=$yarn_count[$val]; else $count.=",".$yarn_count[$val];
				}

				$avgRate=$stock_arr[$row[csf('id')]];
				$yarn_lot=implode(",",array_unique(explode(",",$row[csf('yarn_lot')])));
				$data=$row[csf('id')]."_".$row[csf('product_name_details')]."_".$yarn_lot."_".implode(",",$count_id)."_".$rackID."_".$shelfID."_".$row[csf('current_stock')]."_".$stritch_length."_".$avgRate."_".$floorID."_".$roomID."_".$floorName."_".$roomName."_".$rackName."_".$shelfName;

				$buyer_name=implode(",",array_unique(explode(",",$buyer_name)));
				$balance_qnty=$recv_qnty-$iss_qnty;
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $data; ?>')">
					<td width="30"><? echo $i; ?></td>
					<td width="60"><? echo $row[csf('id')]; ?></td>
					<td width="130"><p><? echo $row[csf('product_name_details')]; ?></p></td>
					<td width="80"><p><? if($row[csf('is_sales')]==1 && $row[csf('within_group')]==2){echo $saler_order_no;}else{echo $po_no;} ?></p></td>
					<td width="50"><p>&nbsp;<? if($row[csf('is_sales')]==1 && $row[csf('within_group')]==2){ echo $sales_buyer_name;}else{ echo $buyer_name;} ?></p></td>
					<td width="80"><p><? echo $job_arr; ?>&nbsp;</p></td>
					<td width="90"><p><? echo $style_ref; ?>&nbsp;</p></td>
					<td width="60"><p><? echo $program_no; ?>&nbsp;</p></td>
					<td width="60"><p><? echo $stritch_length; ?>&nbsp;</p></td>
					<td width="70"><p><? echo $yarn_lot; ?>&nbsp;</p></td>
					<td width="90"><p><? echo $count; ?>&nbsp;</p></td>
					<td width="90"><p><? echo $floorName; ?>&nbsp;</p></td>
					<td width="90"><p><? echo $roomName; ?>&nbsp;</p></td>
					<td width="60"><p><? echo $rackName ?>&nbsp;</p></td>
					<?
					if($isRackBalance==1)
					{
						?>
						<td width="60"><p><? echo $shelfName; ?>&nbsp;</p></td>
						<td align="right" title="<? echo 'rcv='.$recv_qnty.',iss='.$iss_qnty;?>"><? echo number_format($balance_qnty,2); ?>&nbsp;</td>
						<?
					}
					else
					{
						?>
						<td><p><? echo $shelfName; ?>&nbsp;</p></td>
						<?
					}
					?>
				</tr>
				<?
				$i++;
			}
		?>
		</table>
	</div>
	<?
	exit();

}

// child form data populate after call after close item description pop up
if($action=="populate_child_from_data_item_desc")
{
	$yarn_count = return_library_array("select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$prod_id = $data;
	//echo $prod_id;
	$sql = "select id,product_name_details,current_stock
	from product_details_master
	where id=$prod_id";

	//echo $sql;die;
	$result = sql_select($sql);
	foreach($result as $row)
	{
		if($db_type==0)
		{
			$lot=return_field_value("group_concat(distinct(yarn_lot)) as lot","pro_grey_prod_entry_dtls","prod_id='".$row[csf('id')]."' and yarn_lot<>'' and status_active=1 and is_deleted=0","lot");
			$count_id=return_field_value("group_concat(distinct(yarn_count)) as count","pro_grey_prod_entry_dtls","prod_id='".$row[csf('id')]."' and yarn_count<>'' and yarn_count<>0 and status_active=1 and is_deleted=0","count");
		}
		else
		{
			$lot=return_field_value("LISTAGG(cast(yarn_lot as varchar2(4000)), ',') WITHIN GROUP (ORDER BY yarn_lot) as lot","pro_grey_prod_entry_dtls","prod_id='".$row[csf('id')]."' and yarn_lot is not null and status_active=1 and is_deleted=0","lot");
			$count_id=return_field_value("LISTAGG(cast(yarn_count as varchar2(4000)), ',') WITHIN GROUP (ORDER BY yarn_count)","pro_grey_prod_entry_dtls","prod_id='".$row[csf('id')]."' and yarn_count is not null and yarn_count<>0 and status_active=1 and is_deleted=0","count");

			$lot=implode(",",array_unique(explode(",",$lot)));
		}
		$count_id=implode(",",array_unique(explode(",",$count_id)));
		/*$count_id=array_unique(explode(",",$count_id)); $count='';
		foreach($count_id as $val)
		{
			if($count=='') $count=$val; else $count.=",".$val;
		}*/

		echo "$('#txtItemDescription').val('".$row[csf('product_name_details')]."');\n";
		echo "$('#hiddenProdId').val('".$row[csf('id')]."');\n";
		echo "$('#txtYarnLot').val('".$lot."');\n";
		//echo "$('#cbo_yarn_count').val('".$count."');\n";
		echo "set_multiselect('cbo_yarn_count','0','1','".$count."','0');\n";
		//echo "disable_enable_fields('show_textcbo_yarn_count','1','','');\n";
	}

	exit();

}

if ($action=="po_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$prev_method=$distribution_method;
	$issueQnty=$issueQnty;
	$distribiution_method=array(1=>"Distribute Based On Lowest Shipment Date",2=>"Manually");
	if($receive_basis==4 && $dtls_tbl_id!="")
	{
		$hide_qty_array=return_library_array( " select d.po_breakdown_id, d.quantity  from inv_grey_fabric_issue_dtls c, order_wise_pro_details d
			where c.id=d.dtls_id and c.trans_id>0 and d.trans_id>0 and d.entry_form=577 and c.program_no=$program_no and d.dtls_id in($dtls_tbl_id) and c.status_active=1 and d.status_active=1 ",'po_breakdown_id','quantity');
	}
	else if($dtls_tbl_id!="")
	{
		$hide_qty_array=return_library_array( "select po_breakdown_id, quantity from order_wise_pro_details where dtls_id=$dtls_tbl_id and entry_form=577 and status_active=1 and is_deleted=0",'po_breakdown_id','quantity');
	}
	if($isRoll==1) $readonlyCond="readonly"; else $readonlyCond="";

	?>
	<script>
		var receive_basis=<? echo $receive_basis; ?>;

		function distribute_qnty(str)
		{
			if(str==1)
			{
				var tot_po_qnty=$('#tot_po_qnty').val()*1;
				var txt_prop_grey_qnty=$('#txt_prop_grey_qnty').val()*1;
				var tblRow = $("#tbl_search tr").length;
				var balance =txt_prop_grey_qnty;
				var len=totalGrey=0;
				var tot_placeholder_value=0;

				$("#tbl_search").find('tr').each(function()
				{
					var txtOrginal=$(this).find('input[name="txtOrginal[]"]').val()*1;
					var placeholder_value =$(this).find('input[name="txtGreyQnty[]"]').attr('placeholder');
					var issued_qnty =$(this).find('input[name="hideQnty[]"]').val();
					if(txtOrginal==0)
					{
						$(this).remove();
					}
					else
					{
						tot_placeholder_value = tot_placeholder_value*1+placeholder_value*1+issued_qnty*1;
					}
				});

				if(txt_prop_grey_qnty>tot_placeholder_value)
				{
					var exceeds_qty=txt_prop_grey_qnty-tot_placeholder_value;
					alert("Total Issue Qty Exceeds Total Balance Qty (By "+exceeds_qty+" Qty).");
					$('#txt_prop_grey_qnty').val('');
					$("#tbl_search").find('tr').each(function()
					{
						var issued_qnty =$(this).find('input[name="hideQnty[]"]').val()*1;
						if(issued_qnty==0) issued_qnty='';
						$(this).find('input[name="txtGreyQnty[]"]').val(issued_qnty);
					});
					sum_total();
					return;
				}



				$("#tbl_search").find('tr').each(function()
				{
					len=len+1;

					var txtOrginal=$(this).find('input[name="txtOrginal[]"]').val()*1;
					var placeholder_value =$(this).find('input[name="txtGreyQnty[]"]').attr('placeholder')*1+$(this).find('input[name="hideQnty[]"]').val()*1;

					if(txtOrginal==0)
					{
						$(this).remove();
					}
					else
					{
						if(balance>0)
						{
							if(placeholder_value<0) placeholder_value=0;
							if(balance>placeholder_value)
							{
								var grey_qnty=placeholder_value;
								balance=balance-placeholder_value;
							}
							else
							{
								var grey_qnty=balance;
								balance=0;
							}

							if(tblRow==len)
							{
								var grey_qnty=txt_prop_grey_qnty-totalGrey;
							}

							totalGrey = totalGrey*1+grey_qnty*1;

							$(this).find('input[name="txtGreyQnty[]"]').val(grey_qnty.toFixed(2));
						}
						else
						{
							$(this).find('input[name="txtGreyQnty[]"]').val('');
						}
					}
				});
			}
			else
			{
				$('#txt_prop_grey_qnty').val('');
				$("#tbl_search").find('tr').each(function()
				{
					$(this).find('input[name="txtGreyQnty[]"]').val('');
				});
			}

			sum_total();
		}

		function check_balance(row_no)
		{
			var placeholder_value =$('#txtGreyQnty_'+row_no).attr('placeholder')*1;
			var issued_qnty =$('#hideQnty_'+ row_no).val()*1;
			var qnty =$('#txtGreyQnty_'+row_no).val()*1;

			if(qnty>(placeholder_value+issued_qnty))
			{
				alert("Issue Qty Exceeds Balance Qty.");
				if(issued_qnty==0) issued_qnty='';
				$('#txtGreyQnty_'+row_no).val(issued_qnty);
			}
		}


		var selected_id = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_search' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function set_all()
		{
			var old=document.getElementById('txt_po_row_id').value;
			if(old!="")
			{
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{
					js_set_value( old[k] )
				}
			}
		}

		function js_set_value( str ) {
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );

			$('#po_id').val( id );

		}

		function fnc_close()
		{
			var checkEqual =  $("#txt_total_sum").val()*1-$("#txt_prop_grey_qnty").val()*1;
			//if( checkEqual!=0 ){ alert("Issue Qnty and Sum Qnty Not Match"); return; }

			var save_string='';	 var tot_grey_qnty=0; var no_of_roll='';
			var po_id_array = new Array();

			$("#tbl_search").find('tr').each(function()
			{
				var txtPoId=$(this).find('input[name="txtPoId[]"]').val();
				var txtGreyQnty=$(this).find('input[name="txtGreyQnty[]"]').val();
				tot_grey_qnty=tot_grey_qnty*1+txtGreyQnty*1;

				if(txtGreyQnty*1>0)
				{
					if(save_string=="")
					{
						save_string=txtPoId+"**"+txtGreyQnty;
					}
					else
					{
						save_string+=","+txtPoId+"**"+txtGreyQnty;
					}

					if( jQuery.inArray( txtPoId, po_id_array) == -1 )
					{
						po_id_array.push(txtPoId);
					}
				}
			});

			if(save_string=="")
			{
				alert("Please Select At Least One Item");
				return;
			}
			tot_grey_qnty=tot_grey_qnty.toFixed(2);
			$('#save_string').val( save_string );
			$('#tot_grey_qnty').val( tot_grey_qnty );
			$('#all_po_id').val( po_id_array );
			$('#distribution_method').val( $('#cbo_distribiution_method').val() );
			parent.emailwindow.hide();
		}

		function sum_total()
		{
			var tblRow = $("#tbl_search tr").length;
			var ddd={dec_type:1}
			math_operation( "txt_total_sum", "txtGreyQnty_", "+", tblRow, ddd );
		}

		$(document).ready(function(e) {
           // distribute_qnty($('#cbo_distribiution_method').val());
           sum_total();
       });

   </script>

</head>
<body>

	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:620px;margin-left:5px">
			<!-- previous data here -->
			<input type="hidden" name="prev_save_string" id="prev_save_string" class="text_boxes" value="<? echo $save_data; ?>">
			<input type="hidden" name="prev_total_qnty" id="prev_total_qnty" class="text_boxes" value="<? echo $issueQnty; ?>">
			<input type="hidden" name="prev_method" id="prev_method" class="text_boxes" value="<? echo $distribution_method; ?>">
			<!--- END -->
			<input type="hidden" name="save_string" id="save_string" class="text_boxes" value="<? echo $save_data; ?>">
			<input type="hidden" name="tot_grey_qnty" id="tot_grey_qnty" class="text_boxes" value="">
			<input type="hidden" name="all_po_id" id="all_po_id" class="text_boxes" value="">
			<input type="hidden" name="distribution_method" id="distribution_method" class="text_boxes" value="">
			<?

			$trans_po_id=""; $program_poIds='';
			if($receive_basis==4)
			{
				if($hidden_order_id!="")
				{
					$reqQnty = "SELECT  sum(a.grey_qty) as qnty,a.id from fabric_sales_order_dtls a,fabric_sales_order_mst b where a.job_no_mst = b.job_no and a.mst_id = b.id and a.id in($hidden_order_id) and b.entry_form = 547 and a.status_active=1 group by a.id";
					
					$reqQnty_res = sql_select($reqQnty);
					foreach($reqQnty_res as $req_val)
					{
						$req_qty_array[$req_val[csf('id')]]=$req_val[csf('qnty')];
					}
				}
			}
			else if($receive_basis==5)
			{
				$sql_req = "SELECT b.reqn_qty,b.po_id FROM pro_fab_reqn_for_batch_woven_mst a ,pro_fab_reqn_for_batch_woven_dtls b  where a.id = b.mst_id and  a.is_deleted =0 and a.status_active =1 and  b.is_deleted =0 and b.status_active =1 b.po_id in($hidden_order_id) ";
				//echo $sql_req;die;
				$req_res = sql_select($sql_req);
				$requisition_arr = array();
				foreach($req_res as $row)
				{
					$req_qty_array[$row[csf('po_id')]]+=$row[csf('reqn_qty')];
				}
			}

			if($receive_basis==4)
			{
				?>
				<div style="width:600px; margin-top:10px" align="center">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300" align="center">
						<thead>
							<th>Total Issue Qnty</th>
							<th>Distribution Method</th>
						</thead>
						<tr class="general">
							<td><input type="text" name="txt_prop_grey_qnty" id="txt_prop_grey_qnty" class="text_boxes_numeric" value="<? if($prev_method==1) echo $issueQnty; ?>" style="width:120px"  onBlur="distribute_qnty($('#cbo_distribiution_method').val())" <? echo $readonlyCond; ?> /></td>
							<td>
								<?
								echo create_drop_down( "cbo_distribiution_method", 250, $distribiution_method,"",0,"--Select--",$prev_method,"distribute_qnty(this.value);",0 );
								?>
							</td>
						</tr>
					</table>
				</div>
				<div style="margin-left:1px; margin-top:10px">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="610">
						<thead>
							<th width="110">PO No</th>
							<th width="70">Ship. Date</th>
							<th width="80">Gmts. Qty</th>
							<th width="80">Req. Qty</th>
							<th width="80">Prod. Qty</th>
							<th width="80">Cumu. Issued Qty</th>
							<th>Issue Qty</th>
						</thead>
					</table>
					<div style="width:630px; max-height:220px; overflow-y:scroll" id="container" align="left">
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="610" id="tbl_search">
							<?
							$i=1; $tot_po_qnty=0; $propDataArray=array();

							$propData=sql_select("select a.po_breakdown_id, 
								sum(case when a.entry_form in(550) then a.quantity end) as grey_fabric_recv, 
								sum(case when a.entry_form in(577) then a.quantity end) as grey_fabric_issued, 
								sum(case when a.entry_form=578 then a.quantity end) as grey_fabric_recv_return, 
								sum(case when a.entry_form=579 then a.quantity end) as grey_fabric_issue_return, 
								sum(case when a.entry_form in(13,81) and a.trans_type=5 then a.quantity end) as grey_fabric_trans_recv, 
								sum(case when a.entry_form in(13,80) and a.trans_type=6 then a.quantity end) as grey_fabric_trans_issued 
								from order_wise_pro_details a, inv_transaction b
								where a.trans_id = b.id and a.trans_id<>0 and a.prod_id=$prod_id and a.is_deleted=0 and a.status_active=1 and b.store_id = '$store_id' and b.floor_id='$floor_id' and b.room = '$room_id' and b.rack='$rack_id' and b.self = '$self_id' group by a.po_breakdown_id");
							foreach($propData as $prop_row)
							{
								$propDataArray[$prop_row[csf('po_breakdown_id')]]['recv']=$prop_row[csf('grey_fabric_recv')]+$prop_row[csf('grey_fabric_issue_return')]+$prop_row[csf('grey_fabric_trans_recv')];
								$propDataArray[$prop_row[csf('po_breakdown_id')]]['iss']=$prop_row[csf('grey_fabric_issued')]+$prop_row[csf('grey_fabric_recv_return')]+$prop_row[csf('grey_fabric_trans_issued')];
								$yet_issue=($prop_row[csf('grey_fabric_recv')]+$prop_row[csf('grey_fabric_issue_return')]+$prop_row[csf('grey_fabric_trans_recv')])-($prop_row[csf('grey_fabric_issued')]+$prop_row[csf('grey_fabric_recv_return')]+$prop_row[csf('grey_fabric_trans_issued')]);
								$propDataArray[$prop_row[csf('po_breakdown_id')]]['bl']=$yet_issue;
							}

							$po_sql="select b.id, a.job_no as po_number, 1 as total_set_qnty, b.grey_qty as po_quantity, a.delivery_date as pub_shipment_date from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.job_no=b.job_no_mst and a.entry_form = 547 and b.id in ($hidden_order_id) and a.is_deleted =0 and b.is_deleted=0 ";

							//echo $po_sql;
							if($save_string=="" && $type==1) $save_data=$prevQnty;
							$po_data_array=array();
							$explSaveData = explode(",",$save_data);
							foreach($explSaveData as $val)
							{
								$woQnty = explode("**",$val);
								$po_data_array[$woQnty[0]]=$woQnty[1];
							}
							//print_r($explSaveData);
							$nameArray=sql_select($po_sql);
							foreach($nameArray as $row)
							{
								if ($i%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";

								$po_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
								$tot_po_qnty+=$po_qnty_in_pcs;
								$qnty = $po_data_array[$row[csf('id')]];
								$hideQnty=$hide_qty_array[$row[csf('id')]];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td width="110">
										<p><? echo $row[csf('po_number')]; ?></p>
										<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
										<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="1">
									</td>
									<td width="70" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
									<td width="80" align="right">
										<? echo $po_qnty_in_pcs; ?>
										<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_qnty_in_pcs; ?>">
									</td>
									<td width="80" align="right"><? echo $req_qty_array[$row[csf('id')]]; ?></td>
									<td width="80" align="right"><? echo number_format($propDataArray[$row[csf('id')]]['recv'],2,'.',''); ?></td>
									<td width="80" align="right"><? echo number_format($propDataArray[$row[csf('id')]]['iss'],2,'.',''); ?></td>
									<td align="center">
										<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" value="<? echo $qnty; ?>" onKeyUp="check_balance(<? echo $i; ?>);sum_total();" placeholder="<? echo number_format($propDataArray[$row[csf('id')]]['bl'],2,'.',''); ?>" >
										<input type="hidden" name="hideQnty[]" id="hideQnty_<? echo $i; ?>" value="<? echo $hideQnty; ?>">
									</td>
								</tr>
								<?
								$i++;
							}
							?>
							<input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes" value="<? echo $tot_po_qnty; ?>">
						</table>
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="610">
							<tr class="tbl_bottom">
								<td width="110">&nbsp;</td><td width="70" align="right">&nbsp;</td><td width="80" align="right">&nbsp;</td><td width="80" align="right">&nbsp;</td><td width="80" align="right">&nbsp;</td><td width="80" align="right">Sum</td><td style="text-align:center"><input type="text" id="txt_total_sum" class="text_boxes_numeric" style="width:70px" readonly /></td>
							</tr>
						</table>
					</div>
					<table width="610" id="table_id">
						<tr>
							<td align="center" >
								<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
							</td>
						</tr>
					</table>
				</div>
				<?
			}
			else if($receive_basis==5)
			{
				?>
				<div style="width:600px; margin-top:10px" align="center">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300" align="center">
						<thead>
							<th>Total Issue Qnty</th>
							<th>Distribution Method</th>
						</thead>
						<tr class="general">
							<td><input type="text" name="txt_prop_grey_qnty" id="txt_prop_grey_qnty" class="text_boxes_numeric" value="<? if($prev_method==1) echo $issueQnty; ?>" style="width:120px"  onBlur="distribute_qnty($('#cbo_distribiution_method').val())" <? echo $readonlyCond; ?> /></td>
							<td>
								<?
								echo create_drop_down( "cbo_distribiution_method", 250, $distribiution_method,"",0,"--Select--",$prev_method,"distribute_qnty(this.value);",0 );
								?>
							</td>
						</tr>
					</table>
				</div>
				<div style="margin-left:1px; margin-top:10px">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="610">
						<thead>
							<th width="110">PO No</th>
							<th width="70">Req. Date</th>
							<th width="80">Gmts. Qty</th>
							<th width="80">Req. Qty</th>
							<th width="80">Prod. Qty</th>
							<th width="80">Cumu. Issued Qty</th>
							<th>Issue Qty</th>
						</thead>
					</table>
					<div style="width:630px; max-height:220px; overflow-y:scroll" id="container" align="left">
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="610" id="tbl_search">
							<?
							$i=1; $tot_po_qnty=0; $propDataArray=array();

							$propData=sql_select("select a.po_breakdown_id, 
								sum(case when a.entry_form in(550) then a.quantity end) as grey_fabric_recv, 
								sum(case when a.entry_form in(577) then a.quantity end) as grey_fabric_issued, 
								sum(case when a.entry_form=578 then a.quantity end) as grey_fabric_recv_return, 
								sum(case when a.entry_form=579 then a.quantity end) as grey_fabric_issue_return, 
								sum(case when a.entry_form in(13,81) and a.trans_type=5 then a.quantity end) as grey_fabric_trans_recv, 
								sum(case when a.entry_form in(13,80) and a.trans_type=6 then a.quantity end) as grey_fabric_trans_issued 
								from order_wise_pro_details a, inv_transaction b
								where a.trans_id = b.id and a.trans_id<>0 and a.prod_id=$prod_id and a.is_deleted=0 and a.status_active=1 and b.store_id = '$store_id' and b.floor_id='$floor_id' and b.room = '$room_id' and b.rack='$rack_id' and b.self = '$self_id' group by a.po_breakdown_id");
							foreach($propData as $prop_row)
							{
								$propDataArray[$prop_row[csf('po_breakdown_id')]]['recv']=$prop_row[csf('grey_fabric_recv')]+$prop_row[csf('grey_fabric_issue_return')]+$prop_row[csf('grey_fabric_trans_recv')];
								$propDataArray[$prop_row[csf('po_breakdown_id')]]['iss']=$prop_row[csf('grey_fabric_issued')]+$prop_row[csf('grey_fabric_recv_return')]+$prop_row[csf('grey_fabric_trans_issued')];
								$yet_issue=($prop_row[csf('grey_fabric_recv')]+$prop_row[csf('grey_fabric_issue_return')]+$prop_row[csf('grey_fabric_trans_recv')])-($prop_row[csf('grey_fabric_issued')]+$prop_row[csf('grey_fabric_recv_return')]+$prop_row[csf('grey_fabric_trans_issued')]);
								$propDataArray[$prop_row[csf('po_breakdown_id')]]['bl']=$yet_issue;
							}

							

							$po_sql="SELECT b.id, a.job_no as po_number, 1 as total_set_qnty, c.reqn_qty as po_quantity, d.reqn_date as pub_shipment_date from fabric_sales_order_mst a, fabric_sales_order_dtls b ,pro_fab_reqn_for_batch_woven_dtls c,pro_fab_reqn_for_batch_woven_mst d  where a.job_no=b.job_no_mst and a.entry_form = 547 and b.id in ($hidden_order_id) and c.po_id = b.id and d.id = c.mst_id and d.is_deleted = 0  and c.is_deleted =0 and c.status_active =1 and a.is_deleted =0 and b.is_deleted=0 ";

							//echo $po_sql;
							if($save_string=="" && $type==1) $save_data=$prevQnty;
							$po_data_array=array();
							$explSaveData = explode(",",$save_data);
							foreach($explSaveData as $val)
							{
								$woQnty = explode("**",$val);
								$po_data_array[$woQnty[0]]=$woQnty[1];
							}
							//print_r($explSaveData);
							$nameArray=sql_select($po_sql);
							foreach($nameArray as $row)
							{
								if ($i%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";

								$po_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
								$tot_po_qnty+=$po_qnty_in_pcs;
								$qnty = $po_data_array[$row[csf('id')]];
								$hideQnty=$hide_qty_array[$row[csf('id')]];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td width="110">
										<p><? echo $row[csf('po_number')]; ?></p>
										<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
										<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="1">
									</td>
									<td width="70" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
									<td width="80" align="right">
										<? echo $po_qnty_in_pcs; ?>
										<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_qnty_in_pcs; ?>">
									</td>
									<td width="80" align="right"><? echo $req_qty_array[$row[csf('id')]]; ?></td>
									<td width="80" align="right"><? echo number_format($propDataArray[$row[csf('id')]]['recv'],2,'.',''); ?></td>
									<td width="80" align="right"><? echo number_format($propDataArray[$row[csf('id')]]['iss'],2,'.',''); ?></td>
									<td align="center">
										<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" value="<? echo $qnty; ?>" onKeyUp="check_balance(<? echo $i; ?>);sum_total();" placeholder="<? echo number_format($propDataArray[$row[csf('id')]]['bl'],2,'.',''); ?>" >
										<input type="hidden" name="hideQnty[]" id="hideQnty_<? echo $i; ?>" value="<? echo $hideQnty; ?>">
									</td>
								</tr>
								<?
								$i++;
							}
							?>
							<input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes" value="<? echo $tot_po_qnty; ?>">
						</table>
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="610">
							<tr class="tbl_bottom">
								<td width="110">&nbsp;</td><td width="70" align="right">&nbsp;</td><td width="80" align="right">&nbsp;</td><td width="80" align="right">&nbsp;</td><td width="80" align="right">&nbsp;</td><td width="80" align="right">Sum</td><td style="text-align:center"><input type="text" id="txt_total_sum" class="text_boxes_numeric" style="width:70px" readonly /></td>
							</tr>
						</table>
					</div>
					<table width="610" id="table_id">
						<tr>
							<td align="center" >
								<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
							</td>
						</tr>
					</table>
				</div>
				<?
			}
			?>
		</fieldset>
	</form>

</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action=="po_search_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	?>
	<script>

		function fn_show_check()
		{
			if( form_validation('cbo_buyer_name','Buyer Name')==false )
			{
				return;
			}
			show_list_view ( $('#txt_search_common').val()+'_'+$('#cbo_search_by').val()+'_'+<? echo $cbo_company_id; ?>+'_'+$('#cbo_buyer_name').val()+'_'+'<? echo $hidden_order_id; ?>', 'create_po_search_view', 'search_div', 'woven_grey_fabric_issue_controller', 'setFilterGrid(\'tbl_search\',-1);hidden_field_reset();');
			set_all();
		}


		var selected_id = new Array(); var selected_name = new Array(); var buyer_id=''; var style_ref_array= new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_search' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function set_all()
		{

			var old=document.getElementById('txt_po_row_id').value;
			if(old!="")
			{
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{
					js_set_value( old[k] )
				}
			}
		}

		function js_set_value( str )
		{

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}

			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}

			if(buyer_id=='') buyer_id=$('#txt_buyer' + str).val();

			var style_ref=$('#txt_styleRef' + str).val();

			if( jQuery.inArray( style_ref, style_ref_array) == -1 )
			{
				style_ref_array.push(style_ref);
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hidden_order_id').val(id);
			$('#hidden_order_no').val(name);
			$('#hide_buyer').val(buyer_id);
			$('#hide_style_ref').val(style_ref_array);

		}

		function hidden_field_reset()
		{
			$('#hidden_order_id').val('');
			$('#hidden_order_no').val( '' );
			$('#hide_buyer').val( '' );
			$('#hide_style_ref').val( '' );
			selected_id = new Array();
			selected_name = new Array();
		}

		function fnc_close()
		{
			parent.emailwindow.hide();
		}

	</script>

</head>
<body>

	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:620px;margin-left:5px">
			<input type="hidden" name="hidden_order_id" id="hidden_order_id" class="text_boxes" value="">
			<input type="hidden" name="hidden_order_no" id="hidden_order_no" class="text_boxes" value="">
			<input type="hidden" name="hide_buyer" id="hide_buyer" class="text_boxes" value="">
			<input type="hidden" name="hide_style_ref" id="hide_style_ref" class="text_boxes" value="">
			<table cellpadding="0" cellspacing="0" width="620" class="rpt_table">
				<thead>
					<th>Buyer</th>
					<th>Search By</th>
					<th>Search</th>
					<th>
						<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
						<input type="hidden" name="po_id" id="po_id" value="">
					</th>
				</thead>
				<tr class="general">
					<td align="center">
						<?
						echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $buyer_name, "","" );
						?>
					</td>
					<td align="center">
						<?
						$search_by_arr=array(1=>"PO No",2=>"Job No");

						echo create_drop_down( "cbo_search_by", 170, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
					</td>
					<td align="center">
						<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
					</td>
					<td align="center">
						<input type="button" name="button2" class="formbutton" value="Show" onClick="fn_show_check()" style="width:100px;" />
					</td>
				</tr>
			</table>
			<div id="search_div" style="margin-top:10px"></div>
		</fieldset>
	</form>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_po_search_view")
{
	$data = explode("_",$data);

	$search_string=trim($data[0]);
	$search_by=$data[1];

	$search_con="";
	if($search_by==1 && $search_string!="")
		$search_con = " and b.po_number like '%$search_string%'";
	else if($search_by==2 && $search_string!="")
		$search_con =" and a.job_no_prefix_num =$search_string";

	$company_id =$data[2];
	$buyer_id =$data[3];
	$all_po_id=$data[4];

	$hidden_po_id=explode(",",$all_po_id);

	if($buyer_id==0) { echo "<b>Please Select Buyer First</b>"; die; }


	$sql = "select a.job_no, a.style_ref_no, a.buyer_name, a.order_uom, b.id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date
	from wo_po_details_master a, wo_po_break_down b
	where a.job_no=b.job_no_mst and a.company_name=$company_id and a.buyer_name=$buyer_id $search_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

	//echo $sql;die;
	?>
	<div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" >
			<thead>
				<th width="40">SL</th>
				<th width="100">Job No</th>
				<th width="110">Style No</th>
				<th width="110">PO No</th>
				<th width="90">PO Quantity</th>
				<th width="50">UOM</th>
				<th>Shipment Date</th>
			</thead>
		</table>
		<div style="width:618px; overflow-y:scroll; max-height:220px;" id="buyer_view" align="center">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="tbl_search" >
				<?
				$i=1; $po_row_id='';
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					if(in_array($selectResult[csf('id')],$hidden_po_id))
					{
						if($po_row_id=="") $po_row_id=$i; else $po_row_id.=",".$i;
					}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
						<td width="40" align="center"><?php echo "$i"; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
						<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $selectResult[csf('po_number')]; ?>"/>
						<input type="hidden" name="txt_buyer" id="txt_buyer<?php echo $i ?>" value="<? echo $selectResult[csf('buyer_name')]; ?>"/>
						<input type="hidden" name="txt_styleRef" id="txt_styleRef<?php echo $i ?>" value="<? echo $selectResult[csf('style_ref_no')]; ?>"/>
					</td>
					<td width="100"><p><? echo $selectResult[csf('job_no')]; ?></p></td>
					<td width="110"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
					<td width="110"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
					<td width="90" align="right"><? echo $selectResult[csf('po_qnty_in_pcs')]; ?></td>
					<td width="50" align="center"><p><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></p></td>
					<td align="center"><? echo change_date_format($selectResult[csf('pub_shipment_date')]); ?></td>
				</tr>
				<?
				$i++;
			}
			?>
			<input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<?php echo $po_row_id; ?>"/>
		</table>
	</div>
	<table width="620" cellspacing="0" cellpadding="0" style="border:none" align="center">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%">
					<div style="width:50%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
					</div>
					<div style="width:50%; float:left" align="left">
						<input type="button" name="close" onClick="fnc_close();" class="formbutton" value="Close" style="width:100px" />
					</div>
				</div>
			</td>
		</tr>
	</table>
</div>
<?

exit();
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hiddenProdId and store_id = $cbo_store_name and transaction_type in (1,4,5) and status_active = 1", "max_date");
	$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
	$issue_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_issue_date)));
	if ($issue_date < $max_recv_date)
	{
		echo "20**Issue Date Can not Be Less Than Last Receive Date Of This Lot";
		die;
	}
	
	$txt_booking_id=$hidden_sales_orde_requisition_id;
	$txt_booking_no=$txt_sales_orde_requisition;
	

	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{

		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		/*$hidden_is_sales = str_replace("'", "", $hidden_is_sales);
		if ($hidden_is_sales=="") 
		{
			$hidden_is_sales=0;
		}*/
		// echo "10**".$hidden_is_sales;die;
		//batch duplication check---------------------------------------------//
		if( str_replace("'","",$hidden_is_batch_maintain)==2 && str_replace("'","",$cbo_issue_purpose)!=3)
		{
			$chk = is_duplicate_field("batch_no","pro_batch_create_mst","batch_no=$txt_batch_no and batch_no!=0");
			if($chk==1)
			{
				echo "20**Duplicate Batch Number.";disconnect($con);
				exit();
			}
		}

		//$currentStock = return_field_value("current_stock","product_details_master","id=".$hiddenProdId);
		$avg_rate=$currentStock=$stock_value=0;
		$sql = sql_select("select avg_rate_per_unit,current_stock,stock_value from product_details_master where id=$hiddenProdId");
		foreach($sql as $result)
		{
			$avg_rate = $result[csf("avg_rate_per_unit")];
			$currentStock = $result[csf("current_stock")];
			$stock_value = $result[csf("stock_value")];
		}

		$txtIssueQnty = str_replace("'","",$txtIssueQnty);
		if($txtIssueQnty>$currentStock)
		{
			echo "20**Issue Quantity Exceeds The Current Stock Quantity";disconnect($con);
			exit();
		}
		//table lock here
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}


		//####################################################--------------------
		//batch creation table insert here------------------------//
		if(str_replace("'","",$txt_batch_id)!="") $batchID = $txt_batch_id; else $batchID = 0;
		$columns = "fso_id,fso_no";
		if($cbo_basis == 5) $columns = "req_id,req_no";
		if( str_replace("'","",$hidden_is_batch_maintain)==2 && str_replace("'","",$cbo_issue_purpose)!=3)
		{
			//pro_batch_create_mst table insert here------------------------//
			//batch_against,batch_for,batch_weight
			if(str_replace("'","",$cbo_issue_purpose)==8) $booking_without_order=1; else $booking_without_order=0;
			// $batchID = return_next_id("id", "pro_batch_create_mst", 1);
			$batchID = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
			$field_array_batch_mst= "id,batch_no,entry_form,batch_date,company_id,".$columns.",booking_without_order,batch_weight,inserted_by,insert_date";
			$data_array_batch_mst= "(".$batchID.",".$txt_batch_no.",577,".$txt_issue_date.",".$cbo_company_id.",".$txt_booking_id.",".$txt_booking_no.",".$booking_without_order.",".$txtIssueQnty.",'".$user_id."','".$pc_date_time."')";

			// $batchQry=sql_insert("pro_batch_create_mst",$field_array_batch_mst,$data_array_batch_mst,1);
			 //pro_batch_create_mst table insert end------------------------//


			 //pro_batch_create_dtls table insert start------------------------//
 			 //$batchDtlsID = return_next_id("id", "pro_batch_create_dtls", 1);
			$batchDtlsID = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
			$field_array_batch_dtls= "id,mst_id,po_id,item_description,roll_no,batch_qnty,inserted_by,insert_date";
			$data_array_batch_dtls="(".$batchDtlsID.",".$batchID.",".$hiddenProdId.",".$txtItemDescription.",".$txtRollNo.",".$txtIssueQnty.",'".$user_id."','".$pc_date_time."')";

			 //$batchQryDtls=sql_insert("pro_batch_create_dtls",$field_array_batch_dtls,$data_array_batch_dtls,1);

			 //pro_batch_create_dtls table insert end------------------------//
		}
		else
		{
			$batchQry=true;$batchQryDtls=true;
		}
		//batch creation table insert end------------------------//

		//echo "10**".$field_array."=".$data_array."##".$batchQry;
		//mysql_query("ROLLBACK");die;

		$mrr_no='';
		//issue master table entry here Start---------------------------------------//
		if( str_replace("'","",$txt_system_no) == "" ) //new insert
		{
			//$id=return_next_id("id", "inv_issue_master", 1);
			$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later

			//$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'KGI', date("Y",time()), 5, "select issue_number_prefix,issue_number_prefix_num from inv_issue_master where company_id=$cbo_company_id and entry_form=577 and $year_cond=".date('Y',time())." order by id DESC ", "issue_number_prefix", "issue_number_prefix_num" ));

			$new_mrr_number = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,$cbo_company_id,"KGI",577,date("Y",time()),14 ));

			$field_array="id,issue_number_prefix, issue_number_prefix_num, issue_number, issue_basis, issue_purpose, entry_form, item_category, company_id, buyer_id, style_ref,".$columns.", batch_no, issue_date, knit_dye_source, knit_dye_company, challan_no, service_booking_no, order_id, inserted_by, insert_date";
			$data_array="(".$id.",'".$new_mrr_number[1]."','".$new_mrr_number[2]."','".$new_mrr_number[0]."',".$cbo_basis.",".$cbo_issue_purpose.",577,14,".$cbo_company_id.",".$cbo_buyer_name.",".$txt_style_ref.",".$txt_booking_id.",".$txt_booking_no.",".$batchID.",".$txt_issue_date.",".$cbo_dyeing_source.",".$cbo_dyeing_company.",".$txt_challan_no.",".$txt_service_booking_no.",".$hidden_order_id.",'".$user_id."','".$pc_date_time."')";
			//$rID=sql_insert("inv_issue_master",$field_array,$data_array,1);
			$mrr_no=$new_mrr_number[0];
		}
		else
		{
			$id = str_replace("'","",$hidden_system_id);
			$columns2= str_replace(",","*",$columns);
			$field_array="issue_basis*issue_purpose*entry_form*item_category*company_id*buyer_id*style_ref*".$columns2."*batch_no*issue_date*knit_dye_source*knit_dye_company*challan_no*service_booking_no*order_id*updated_by*update_date";
			$data_array="".$cbo_basis."*".$cbo_issue_purpose."*577*14*".$cbo_company_id."*".$cbo_buyer_name."*".$txt_style_ref."*".$txt_booking_id."*".$txt_booking_no."*".$txt_batch_no."*".$txt_issue_date."*".$cbo_dyeing_source."*".$cbo_dyeing_company."*".$txt_challan_no."*".$txt_service_booking_no."*".$hidden_order_id."*'".$user_id."'*'".$pc_date_time."'";
			//echo $field_array."<br>".$data_array;."-".;
			//$rID=sql_update("inv_issue_master",$field_array,$data_array,"id",$id,0);
			$mrr_no=str_replace("'","",$txt_system_no);
		}
		//issue master table entry here END---------------------------------------//
		//if($rID) $flag=1; else $flag=0;

		//$transactionID = return_next_id("id", "inv_transaction", 1);
		$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				//####################################################-----------------
		//this is for transaction table insert-------------

		$cons_amnt=str_replace("'","",$txtIssueQnty)*str_replace("'","",$hiddenAvgRate);
		if(str_replace("'","",$cbo_floor_hidden)=="") { $cbo_floor_hidden=0; }
		if(str_replace("'","",$cbo_room_hidden)=="") { $cbo_room_hidden=0; }
		if(str_replace("'","",$txt_rack_hidden)=="") { $txt_rack_hidden=0; }
		if(str_replace("'","",$txt_shelf_hidden)=="") { $txt_shelf_hidden=0; }

		$tr_field_array = "id,mst_id,requisition_no,company_id,prod_id,item_category,transaction_type,transaction_date,store_id,cons_quantity,cons_rate,cons_amount,floor_id,room,rack,self,inserted_by,insert_date";
		$tr_data_array = "(".$transactionID.",".$id.",".$txt_program_no.",".$cbo_company_id.",".$hiddenProdId.",14,2,".$txt_issue_date.",".$cbo_store_name.",".$txtIssueQnty.",".$hiddenAvgRate.",'".$cons_amnt."',".$cbo_floor_hidden.",".$cbo_room_hidden.",".$txt_rack_hidden.",".$txt_shelf_hidden.",'".$user_id."','".$pc_date_time."')";
		//$transID = sql_insert("inv_transaction",$tr_field_array,$tr_data_array,1);
		//inventory TRANSACTION table data entry  END-------------------------------//


		//####################################################------------------------
		//inv_grey_fabric_issue_dtls table insert start------------------------------//
		//$dtls_id=return_next_id("id", "inv_grey_fabric_issue_dtls", 1);
		$dtls_id = return_next_id_by_sequence("INV_GREY_FAB_ISS_DTLS_PK_SEQ", "inv_grey_fabric_issue_dtls", $con);

		$cbo_yarn_count=explode(",",str_replace("'","",$cbo_yarn_count));
		asort($cbo_yarn_count);
		$cbo_yarn_count=implode(",",$cbo_yarn_count);

		$txtYarnLot=explode(",",str_replace("'","",$txtYarnLot));
		asort($txtYarnLot);
		$txtYarnLot=implode(",",$txtYarnLot);

		$stitchLeng="";
		$txt_stitch_length=explode(",",$txt_stitch_length);
		foreach ($txt_stitch_length as $lenVal) {
			$stitchLeng.=trim($lenVal," ").",";
		}
		$txt_stitch_length=chop($stitchLeng,",");

		$field_array_dtls="id,mst_id,trans_id,distribution_method,program_no,no_of_roll,roll_no,roll_po_id,prod_id,roll_wise_issue_qnty, issue_qnty, rate, amount, color_id, yarn_lot, yarn_count, store_name,floor_id,room, rack,self,stitch_length,remarks,inserted_by,insert_date";
		$data_array_dtls="(".$dtls_id.",".$id.",".$transactionID.",".$distribution_method_id.",".$txt_program_no.",".$txtNoOfRoll.",".$txtRollNo.",".$txtRollPOid.",".$hiddenProdId.",".$txtRollPOQnty.",".$txtIssueQnty.",".$hiddenAvgRate.",'".$cons_amnt."',".$cbo_color_id.",'".$txtYarnLot."','".$cbo_yarn_count."',".$cbo_store_name.",".$cbo_floor_hidden.",".$cbo_room_hidden.",".$txt_rack_hidden.",".$txt_shelf_hidden.",".$txt_stitch_length.",".$txt_remarks.",'".$user_id."','".$pc_date_time."')";
		//$dtlsrID=sql_insert("inv_grey_fabric_issue_dtls",$field_array_dtls,$data_array_dtls,1);
		//inv_grey_fabric_issue_dtls table insert end------------------------------//



		//####################################################-----------------
		//product master table data UPDATE START-----------------------------//

		$currentStock = $currentStock-$txtIssueQnty;
		$currentStockValue = $stock_value-$cons_amnt;

		if ($currentStock>0)
		{
			$adjust_avg_rate=number_format($currentStockValue/$currentStock,4,'.','');
		}
		else{
			$adjust_avg_rate=0;
		}
		// if Qty is zero then rate & value will be zero
		if ($currentStock<=0) 
		{
			$currentStockValue=0;
			$adjust_avg_rate=0;
		}
		
		$prod_update_data = "".$txtIssueQnty."*".$currentStock."*".$adjust_avg_rate."*'".$currentStockValue."'*'".$user_id."'*'".$pc_date_time."'";
		$prod_field_array = "last_issued_qnty*current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";
 		//$prodUpdate = sql_update("product_details_master",$prod_field_array,$prod_update_data,"id",$hiddenProdId,0);
		//product master table data UPDATE END-------------------------------//

		$txtIssueQnty = str_replace("'","",$txtIssueQnty);
		$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
		$update_array = "balance_qnty*balance_amount*updated_by*update_date";
		$updateID_array=array();
		$update_data=array();
		//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);


		$isLIFOfifo=return_field_value("store_method","variable_settings_inventory","company_name=$cbo_company_id and variable_list=17 and item_category_id=14 and status_active=1 and is_deleted=0");
		if($isLIFOfifo==2) $cond_lifofifo=" DESC"; else $cond_lifofifo=" ASC";

		$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hiddenProdId and balance_qnty>0 and transaction_type in (1,4,5) and item_category=14 order by transaction_date $cond_lifofifo");
		foreach($sql as $result)
		{
			$recv_trans_id = $result[csf("id")]; // this row will be updated
			$balance_qnty = $result[csf("balance_qnty")];
			$balance_amount = $result[csf("balance_amount")];
			$cons_rate = $result[csf("cons_rate")];
			if($cons_rate=="") { $cons_rate=str_replace("'","",$hiddenAvgRate); }

			$issueQntyBalance = $balance_qnty-$txtIssueQnty; // minus issue qnty
			$issueStockBalance = $balance_amount-($txtIssueQnty*$cons_rate);
			if($issueQntyBalance>=0)
			{
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				$amount = $txtIssueQnty*$cons_rate;
				//for insert
				if($data_array_mrr!="") $data_array_mrr .= ",";
				$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$transactionID.",577,".$hiddenProdId.",".$txtIssueQnty.",'".$cons_rate."',".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
				//for update
				$updateID_array[]=$recv_trans_id;
				$update_data[$recv_trans_id]=explode("*",("".$issueQntyBalance."*".$issueStockBalance."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				break;
			}
			else if($issueQntyBalance<0)
			{
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				$issueQntyBalance = $txtIssueQnty-$balance_qnty;
				$txtIssueQnty = $balance_qnty;
				$amount = $txtIssueQnty*$cons_rate;

				//for insert
				if($data_array_mrr!="") $data_array_mrr .= ",";
				$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$transactionID.",577,".$hiddenProdId.",".$balance_qnty.",'".$cons_rate."',".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
				//for update
				$updateID_array[]=$recv_trans_id;
				$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				$txtIssueQnty = $issueQntyBalance;
			}
			//$mrrWiseIsID++;
		}//end foreach
		// LIFO/FIFO END-----------------------------------------------//

		//####################################################--------------------
		//order_wise_pro_details table data insert Start---------------//
		$proportQ=true;
		$data_array_prop="";
		$save_string=explode(",",str_replace("'","",$save_data));

		if(count($save_string)>0 && str_replace("'","",$save_data)!="")
		{
			//$id_proport = return_next_id( "id", "order_wise_pro_details", 1 );
			$field_array_proportionate="id,trans_id,trans_type,entry_form,dtls_id,po_breakdown_id,prod_id,quantity,issue_purpose,is_sales,inserted_by,insert_date";
			//order_wise_pro_details table data insert START-----//
			$po_array=array();
			for($i=0;$i<count($save_string);$i++)
			{
				$order_dtls=explode("**",$save_string[$i]);
				$order_id=$order_dtls[0];
				$order_qnty=$order_dtls[1];

				/*if( array_key_exists($order_id,$po_array) )
					$po_array[$order_id]+=$order_qnty;
				else
				$po_array[$order_id]=$order_qnty;*/

				$po_array[$order_id]+=$order_qnty;
			}

			foreach($po_array as $key=>$val)
			{
				$id_proport = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$order_id=$key; $order_qnty=$val;

				if($data_array_prop!="") $data_array_prop.= ",";

				$data_array_prop.="(".$id_proport.",".$transactionID.",2,577,".$dtls_id.",".$order_id.",".$hiddenProdId.",".$order_qnty.",".$cbo_issue_purpose.",".$hidden_is_sales.",".$user_id.",'".$pc_date_time."')";
				//$id_proport = $id_proport+1;
			}
			/*if($data_array_prop!="")
			{
				$proportQ=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
			}*/
		}//end if
		//order_wise_pro_details table data insert END -----//

		 // echo "10**insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		//####################################################--------------------
		//roll table entry----------------------------------------//
		if( str_replace("'","",$hidden_is_roll_maintain)==1)
		{
			$data_array_roll="";
			//$rollDtlsID = return_next_id("id", "pro_roll_details", 1);
			$field_array_roll= "id,mst_id,dtls_id,po_breakdown_id,entry_form,roll_no,roll_id,qnty,inserted_by,insert_date";

			$poIDarr = explode(",",str_replace("'","",$txtRollPOid));
			$rollNoarr = explode(",",str_replace("'","",$txtRollNo));
			$rollPoQntyarr = explode(",",str_replace("'","",$txtRollPOQnty));
			$lopSize = count($poIDarr);
			for($i=0;$i<$lopSize;$i++)
			{
				$rollDtlsID = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				$rollno = return_field_value("roll_no","pro_roll_details","id=".$rollNoarr[$i]);
				if($i>0) $data_array_roll .=",";
				$data_array_roll.= "(".$rollDtlsID.",".$id.",".$dtls_id.",".$poIDarr[$i].",577,'".$rollno."',".$rollNoarr[$i].",".$rollPoQntyarr[$i].",'".$user_id."','".$pc_date_time."')";
			 	//$rollDtlsID = $rollDtlsID+1;
			}
			//$rollDtls=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,1);
		}
		else
		{
			$rollDtls=true;
		}
		//roll table entry end------------------------------------//


		//echo "10**".$rID." && ".$dtlsrID." && ".$prodUpdate." && ".$proportQ." && ".$batchQry." && ".$batchQryDtls." && ".$rollDtls." && ".$batchQryDtls." && ".$rollDtls; die;

 		 //Query Execution Start

		if( str_replace("'","",$txt_system_no) == "" )
		{
			$rID=sql_insert("inv_issue_master",$field_array,$data_array,1);
		}
		else
		{
			$rID=sql_update("inv_issue_master",$field_array,$data_array,"id",$id,0);
		}
		//echo "10**"."insert into inv_transaction (".$tr_field_array.") values ".$tr_data_array;die;

		$transID = sql_insert("inv_transaction",$tr_field_array,$tr_data_array,1);
		$dtlsrID=sql_insert("inv_grey_fabric_issue_dtls",$field_array_dtls,$data_array_dtls,1);
		$prodUpdate = sql_update("product_details_master",$prod_field_array,$prod_update_data,"id",$hiddenProdId,0);

		if( str_replace("'","",$hidden_is_batch_maintain)==2 && str_replace("'","",$cbo_issue_purpose)!=3)
		{
			$batchQry=sql_insert("pro_batch_create_mst",$field_array_batch_mst,$data_array_batch_mst,1);
			$batchQryDtls=sql_insert("pro_batch_create_dtls",$field_array_batch_dtls,$data_array_batch_dtls,1);
		}
		if(count($save_string)>0 && str_replace("'","",$save_data)!="")
		{
			if($data_array_prop!="")
			{
				$proportQ=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
			}
		}

		if( str_replace("'","",$hidden_is_roll_maintain)==1)
		{
			$rollDtls=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,1);
		}

		$mrrWiseIssueID=true; $upTrID=true;
		if($data_array_mrr!="")
		{
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
		}

		//echo "10**"."insert into inv_mrr_wise_issue_details (".$field_array_mrr.") values ".$data_array_mrr;die;
		//transaction table stock update here------------------------//
		if(count($updateID_array)>0)
		{
			$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
		}
		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);
		//oci_rollback($con);
		//echo "10**".$rID." && ".$transID." && ".$dtlsrID." && ".$prodUpdate." && ".$proportQ." && ".$batchQry." && ".$batchQryDtls." && ".$rollDtls." && ".$mrrWiseIssueID." && ".$upTrID; die;
		//die;
		if($db_type==0)
		{
			if($rID && $dtlsrID && $transID && $prodUpdate && $proportQ && $batchQry && $batchQryDtls && $rollDtls && $mrrWiseIssueID && $upTrID)
			{
				mysql_query("COMMIT");
				echo "0**".$mrr_no."**".$id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$mrr_no."**".$id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $dtlsrID && $transID && $prodUpdate && $proportQ && $batchQry && $batchQryDtls && $rollDtls && $mrrWiseIssueID && $upTrID)
			{
				oci_commit($con);
				echo "0**".$mrr_no."**".$id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$mrr_no."**".$id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here
		/*$hidden_is_sales = str_replace("'", "", $hidden_is_sales);
		if ($hidden_is_sales=="") 
		{
			$hidden_is_sales=0;
		}*/
		// echo "10**".$hidden_is_sales;die;

		//batch duplication check---------------------------------------------//
		if( str_replace("'","",$hidden_is_batch_maintain)==2 && str_replace("'","",$cbo_issue_purpose)!=3)
		{
			$chk = is_duplicate_field("batch_no","pro_batch_create_mst","batch_no=$txt_batch_no and id!=$txt_batch_id and batch_no!=0");
			if($chk==1)
			{
				echo "20**Duplicate Batch Number.";disconnect($con);
				exit();
			}
		}


		//========================<<<<<<< batch validation starts here >>>>>>>>>============================
		$issue_sql = sql_select("SELECT a.issue_number, a.booking_no, b.program_no, b.prod_id, a.issue_basis, c.po_breakdown_id, c.is_sales, b.issue_qnty, c.quantity
			from inv_issue_master a, inv_grey_fabric_issue_dtls b left join order_wise_pro_details c on b.id=c.dtls_id and c.entry_form=577 and c.status_active=1
			where a.id=b.mst_id and a.company_id=$cbo_company_id and a.entry_form=577 and b.status_active=1 and b.id=$dtls_tbl_id");
		$booking_nos="";
		foreach ($issue_sql as $val) 
		{
			$ref_program = $val[csf("program_no")];
			$ref_prod_id = $val[csf("prod_id")];
			$ref_booking_no = $val[csf("booking_no")];
			$ref_is_sales = $val[csf("is_sales")];
			
			if($val[csf("po_breakdown_id")] !="")
			{
				$OrderProductWiseIssue[$ref_prod_id][$val[csf("po_breakdown_id")]] +=$val[csf("quantity")];


				$ref_order_nos .= $val[csf("po_breakdown_id")].",";
				$programOrderProd_Issue[$ref_program][$ref_prod_id][$val[csf("po_breakdown_id")]] +=$val[csf("quantity")];
				$this_Issue_order_wise[$ref_prod_id][$val[csf("po_breakdown_id")]] +=$val[csf("quantity")];
			}
			else if($val[csf("issue_basis")] ==1)
			{
				if($booking_nos == ""){
					$booking_nos ="'".$ref_booking_no."'";
				}
				else
				{
					$booking_nos .= ",'".$ref_booking_no."'";
				}
				$issueBasisBooking[$ref_booking_no] = $ref_booking_no;
				$allSampleNonOrderBooking[$ref_booking_no] = $ref_booking_no;
				$this_Issue_nor_ord_sample_wise[$ref_prod_id][$ref_booking_no] +=$val[csf("issue_qnty")];
				$SampleNonOrdProductWiseIssue[$ref_prod_id][$ref_booking_no] +=$val[csf("issue_qnty")];
			}
			else if($val[csf("issue_basis")] ==3)
			{
				//N. B. Loop will iterate only ones so query had done here.
				$prog_booking = sql_select("SELECT b.booking_no from ppl_planning_info_entry_dtls a, ppl_planning_info_entry_mst b where a.mst_id=b.id and a.id=$ref_program");
				$ref_booking_no = $prog_booking[0][csf("booking_no")];
				if($booking_nos == ""){
					$booking_nos ="'".$ref_booking_no."'";
				}else{
					$booking_nos .= ",'".$ref_booking_no."'";
				}

				$issueBasisProgram[$ref_program] = $ref_program;
				$ProgramBookingArr[$ref_program] = $ref_booking_no;
				$allSampleNonOrderBooking[$ref_booking_no] = $ref_booking_no;
				$this_Issue_nor_ord_sample_wise[$ref_prod_id][$ref_booking_no] +=$val[csf("issue_qnty")];
				$SampleNonOrdProductWiseIssue[$ref_prod_id][$ref_booking_no] +=$val[csf("issue_qnty")];
			}
		}

		if($ref_order_nos != "")
		{
			$ref_order_cond = " and c.po_breakdown_id in (". chop($ref_order_nos,",").")";

			//N. B. Other issues qnty without current id

			$ref_issue_sql = sql_select("SELECT a.issue_number, a.booking_no, b.program_no, b.prod_id, c.po_breakdown_id, b.issue_qnty, c.quantity
			from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c
			where a.company_id=$cbo_company_id and b.prod_id=$ref_prod_id and c.entry_form=577 and a.id=b.mst_id and b.id=c.dtls_id and  c.status_active=1 $ref_order_cond and a.entry_form=577 and b.status_active=1 and b.id !=$dtls_tbl_id and c.is_sales=$ref_is_sales");
			//"and a.issue_basis=3"
			foreach ($ref_issue_sql as $val) 
			{
				$OrderProductWiseIssue[$ref_prod_id][$val[csf("po_breakdown_id")]] +=$val[csf("quantity")];
			}

			$ref_order_cond_batch = " and b.po_id in (". chop($ref_order_nos,",").")";
			$batch_sql = sql_select("SELECT b.po_id, sum(b.batch_qnty) as total_batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_id and b.prod_id = $ref_prod_id $ref_order_cond_batch and a.batch_against=1 and a.entry_form=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.is_sales=$ref_is_sales group by b.po_id");

			foreach ($batch_sql as $val) 
			{
				$programOrderProd_Batch[$ref_prod_id][$val[csf("po_id")]] +=$val[csf("total_batch_qnty")];
			}

			$save_po_string=explode(",",str_replace("'","",$save_data));
			if(count($save_po_string)>0 && str_replace("'","",$save_data)!="")
			{
				$t_po_array=array();
				for($i=0;$i<count($save_po_string);$i++)
				{
					$order_dtls=explode("**",$save_po_string[$i]);
					$order_id=$order_dtls[0];
					$order_qnty=$order_dtls[1];

					if( array_key_exists($order_id,$t_po_array) )
						$t_po_array[$order_id]+=$order_qnty;
					else
						$t_po_array[$order_id]=$order_qnty;
				}
			}
			/*echo "10**";
			print_r($t_po_array);
			die;*/

			if(str_replace("'", "", $hiddenProdId) == $ref_prod_id)
			{
				foreach ($OrderProductWiseIssue[$ref_prod_id] as $PO=>$QNTY) 
				{
					$issue_bal = $QNTY - $this_Issue_order_wise[$ref_prod_id][$PO] + $t_po_array[$PO];
					//all - this system qnty + current given qnty

					if($programOrderProd_Batch[$ref_prod_id][$PO] > $issue_bal){
						echo "20**Issue can not be less than batch qnty.\nIssue qnty= $issue_bal\nBatch qnty =".$programOrderProd_Batch[$ref_prod_id][$PO];
						die;
					}
				}
			}
			else
			{
				//if product id not match then current given qnty ommited from issue calculation
				foreach ($OrderProductWiseIssue[$ref_prod_id] as $PO=>$QNTY) 
				{
					$issue_bal = $QNTY - $this_Issue_order_wise[$ref_prod_id][$PO];
					//all - this system qnty 

					if($programOrderProd_Batch[$ref_prod_id][$PO] > $issue_bal){
						echo "20**Issue can not be less than batch qnty.\nIssue qnty : $issue_bal\nbatch qnty : ".$programOrderProd_Batch[$ref_prod_id][$PO];
						die;
					}
				}
			}

			//echo "20**here break";
			//die;

		}
		elseif (!empty($issueBasisBooking) || !empty($issueBasisProgram)) 
		{
			$ref_booking_cond = " and a.booking_no in (". $booking_nos .")";

			$ref_issue_sql = sql_select("SELECT a.issue_number, a.booking_no, b.program_no, b.prod_id, b.issue_qnty
			from inv_issue_master a, inv_grey_fabric_issue_dtls b 
			where a.id=b.mst_id and a.company_id=$cbo_company_id and b.prod_id=$ref_prod_id $ref_booking_cond and a.entry_form=577 and b.status_active=1 and a.issue_basis=1 and b.id !=$dtls_tbl_id");
			foreach ($ref_issue_sql as $val) 
			{
				$SampleNonOrdProductWiseIssue[$ref_prod_id][$val[csf("booking_no")]] +=$val[csf("issue_qnty")];
			}


			$prog_booking = sql_select("SELECT a.id from ppl_planning_info_entry_dtls a, ppl_planning_info_entry_mst b where a.mst_id=b.id and b.booking_no = $booking_nos");
			foreach ($prog_booking as $val) 
			{
				$ref_program_nos .= $val[csf("id")].",";
			}
			$ref_program_nos = chop($ref_program_nos,",");

			$ref_issue_sql = sql_select("SELECT a.issue_number, a.booking_no, b.program_no, b.prod_id, a.issue_basis, b.issue_qnty
			from inv_issue_master a, inv_grey_fabric_issue_dtls b
			where a.id=b.mst_id and a.company_id=$cbo_company_id and b.prod_id=$ref_prod_id and b.program_no in ($ref_program_nos) and a.entry_form=577 and a.issue_basis=3 and b.status_active=1 and b.id !=$dtls_tbl_id");

			foreach ($ref_issue_sql as $val) 
			{
				$SampleNonOrdProductWiseIssue[$ref_prod_id][$ProgramBookingArr[$ref_program]] +=$val[csf("issue_qnty")];
			}

			$batch_sql = sql_select("SELECT a.booking_no, sum(b.batch_qnty) as total_batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_id and b.prod_id = $ref_prod_id $ref_booking_cond and a.batch_against=3 and a.entry_form=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no");

			foreach ($batch_sql as $val) 
			{
				$sampleNonOrdProd_Batch[$ref_prod_id][$val[csf("booking_no")]] +=$val[csf("total_batch_qnty")];
			}

			if(!empty($issueBasisProgram)){
				$current_booking_sql = sql_select("SELECT b.booking_no from ppl_planning_info_entry_dtls a, ppl_planning_info_entry_mst b where a.mst_id=b.id and a.id = $txt_program_no");
				$current_booking = $current_booking_sql[0][csf("booking_no")];

			}else{
				$current_booking= str_replace("'", "", $txt_booking_no);
			}

			//echo "10**"."SELECT a.booking_no from ppl_planning_info_entry_dtls a, ppl_planning_info_entry_mst b where a.mst_id=b.id and b.id = $txt_program_no";die;

			//echo "10**".str_replace("'", "", $hiddenProdId) ."==". $ref_prod_id ."&&". $current_booking ."==". $ref_booking_no;die;
			if(str_replace("'", "", $hiddenProdId) == $ref_prod_id && $current_booking == $ref_booking_no)
			{
				$issue_bal = $SampleNonOrdProductWiseIssue[$ref_prod_id][$ref_booking_no] - $this_Issue_nor_ord_sample_wise[$ref_prod_id][$ref_booking_no] +  str_replace("'", "", $txtIssueQnty);
			}
			else
			{
				$issue_bal = $SampleNonOrdProductWiseIssue[$ref_prod_id][$ref_booking_no] - $this_Issue_nor_ord_sample_wise[$ref_prod_id][$ref_booking_no];
			}

			if($sampleNonOrdProd_Batch[$ref_prod_id][$val[csf("booking_no")]] > $issue_bal)
			{
				echo "20**Issue can not be less than batch.\nIssue qnty : $issue_bal\nBatch qnty : ".$sampleNonOrdProd_Batch[$ref_prod_id][$val[csf("booking_no")]];
				die;
			}
			//echo "20**Here ".$sampleNonOrdProd_Batch[$ref_prod_id][$val[csf("booking_no")]] . " > $issue_bal";
			//die;
		}

		//=====================<<<<<  batch validation Ends here >>>>>>>==============================



 		//####################################################--------------------
		//batch creation table insert here------------------------//
		if( str_replace("'","",$hidden_is_batch_maintain)==2 && str_replace("'","",$cbo_issue_purpose)!=3 && str_replace("'","",$txt_batch_id)!="" )
		{
			if(str_replace("'","",$cbo_issue_purpose)==8) $booking_without_order=1; else $booking_without_order=0;
			 //pro_batch_create_mst table update here------------------------//
			$field_array_batch_mst= "batch_no*entry_form*batch_date*company_id*booking_no_id*booking_no*booking_without_order*batch_weight*updated_by*update_date";
			$data_array_batch_mst= "".$txt_batch_no."*577*".$txt_issue_date."*".$cbo_company_id."*".$txt_booking_id."*".$txt_booking_no."*".$booking_without_order."*".$txtIssueQnty."*'".$user_id."'*'".$pc_date_time."'";
 			 //$batchQry=sql_update("pro_batch_create_mst",$field_array_batch_mst,$data_array_batch_mst,"id",$txt_batch_id,0);
			 //pro_batch_create_mst table update end------------------------//


			 //pro_batch_create_dtls table insert start------------------------//
			$field_array_batch_dtls= "po_id*item_description*roll_no*batch_qnty*updated_by*update_date";
			$data_array_batch_dtls= "".$hiddenProdId."*".$txtItemDescription."*".$txtRollNo."*".$txtIssueQnty."*'".$user_id."'*'".$pc_date_time."'";
 			 //$batchQryDtls=sql_update("pro_batch_create_dtls",$field_array_batch_dtls,$data_array_batch_dtls,"mst_id",$txt_batch_id,0);
			 //pro_batch_create_dtls table insert end------------------------//

		}
		else
		{
			$batchQry=true;$batchQryDtls=true;
		}
		//batch creation table insert end------------------------//


  		//################################################################
		//issue master update START--------------------------------------//
		$id = str_replace("'","",$hidden_system_id);

		/*#### Stop not eligible field from update operation start ####*/
		// issue_basis*company_id*knit_dye_source*knit_dye_company*
		// $cbo_basis."*".$cbo_company_id."*". $cbo_dyeing_source."*".$cbo_dyeing_company."*".
		/*#### Stop not eligible field from update operation end ####*/
		$columns = "fso_id*fso_no";
		if($cbo_basis == 5) $columns = "req_id*req_no";

		$field_array="issue_purpose*entry_form*item_category*buyer_id*style_ref*".$columns."*booking_no*batch_no*issue_date*challan_no*service_booking_no*order_id*updated_by*update_date";
		$data_array="".$cbo_issue_purpose."*577*14*".$cbo_buyer_name."*".$txt_style_ref."*".$txt_booking_id."*".$txt_booking_no."*".$txt_batch_id."*".$txt_issue_date."*".$txt_challan_no."*".$txt_service_booking_no."*".$hidden_order_id."*'".$user_id."'*'".$pc_date_time."'";
		//echo "20**".$field_array."<br>".$data_array;
		//$rID=sql_update("inv_issue_master",$field_array,$data_array,"id",$id,0);
		//issue master update END---------------------------------------//

		$cons_amnt=str_replace("'","",$txtIssueQnty)*str_replace("'","",$hiddenAvgRate);

		//####################################################-----------------
		//product table update data before -----------------------------//
		$before_prod_id = return_field_value("prod_id","inv_grey_fabric_issue_dtls","id=".$dtls_tbl_id);
		$sqlRes = sql_select("select b.id,b.current_stock,b.stock_value,a.issue_qnty,a.amount from inv_grey_fabric_issue_dtls a, product_details_master b where a.prod_id=b.id and a.id=$dtls_tbl_id");
		foreach($sqlRes as $resR);
		$before_prod_id = $resR[csf("id")];
		$before_issue_qnty = $resR[csf("issue_qnty")];
		$before_issue_value = $resR[csf("amount")];
		$before_current_stock = $resR[csf("current_stock")];
		$before_current_stock_value = $resR[csf("stock_value")];

		$adjust_current_stock = $before_current_stock+$before_issue_qnty;
		$adjust_current_stock_value = $before_current_stock_value+$before_issue_value;

		//####################################################------------------------
		//inv_grey_fabric_issue_dtls table update start------------------------------//
		$dtls_id=$dtls_tbl_id;

		$cbo_yarn_count=explode(",",str_replace("'","",$cbo_yarn_count));
		asort($cbo_yarn_count);
		$cbo_yarn_count=implode(",",$cbo_yarn_count);

		$txtYarnLot=explode(",",str_replace("'","",$txtYarnLot));
		asort($txtYarnLot);
		$txtYarnLot=implode(",",$txtYarnLot);

		$field_array_dtls="distribution_method*program_no*no_of_roll*roll_no*roll_po_id*prod_id*roll_wise_issue_qnty*issue_qnty*rate*amount*color_id*yarn_lot*yarn_count*store_name*floor_id*room*rack*self*stitch_length*remarks*updated_by*update_date";
		$data_array_dtls="".$distribution_method_id."*".$txt_program_no."*".$txtNoOfRoll."*".$txtRollNo."*".$txtRollPOid."*".$hiddenProdId."*".$txtRollPOQnty."*".$txtIssueQnty."*".$hiddenAvgRate."*'".$cons_amnt."'*".$cbo_color_id."*'".$txtYarnLot."'*'".$cbo_yarn_count."'*".$cbo_store_name."*".$cbo_floor_hidden."*".$cbo_room_hidden."*".$txt_rack_hidden."*".$txt_shelf_hidden."*".$txt_stitch_length."*".$txt_remarks."*'".$user_id."'*'".$pc_date_time."'";
 		//$dtlsrID=sql_update("inv_grey_fabric_issue_dtls",$field_array_dtls,$data_array_dtls,"id",$dtls_id,0);
		//inv_grey_fabric_issue_dtls table update end------------------------------//

		//####################################################-----------------
		//this is for transaction table update start -------------
		$transactionID = $trans_tbl_id;
		if(str_replace("'","",$cbo_floor_hidden)=="") { $cbo_floor_hidden=0; }
		if(str_replace("'","",$cbo_room_hidden)=="") { $cbo_room_hidden=0; }
		if(str_replace("'","",$txt_rack_hidden)=="") { $txt_rack_hidden=0; }
		if(str_replace("'","",$txt_shelf_hidden)=="") { $txt_shelf_hidden=0; }
		$tr_field_array = "company_id*requisition_no*prod_id*item_category*transaction_type*transaction_date*store_id*cons_quantity*cons_rate*cons_amount*floor_id*room*rack*self*updated_by*update_date";
		$tr_data_array = "".$cbo_company_id."*".$txt_program_no."*".$hiddenProdId."*14*2*".$txt_issue_date."*".$cbo_store_name."*".$txtIssueQnty."*".$hiddenAvgRate."*'".$cons_amnt."'*".$cbo_floor_hidden."*".$cbo_room_hidden."*".$txt_rack_hidden."*".$txt_shelf_hidden."*'".$user_id."'*'".$pc_date_time."'";
 		//$transID = sql_update("inv_transaction",$tr_field_array,$tr_data_array,"id",$transactionID,0);
		//inventory TRANSACTION table data update  END-------------------------------//


		//####################################################-----------------
		//product master table data UPDATE START-----------------------------//
		$hiddenProdId = str_replace("'","",$hiddenProdId); //current product id
		$txtIssueQnty = str_replace("'","",$txtIssueQnty); //current issue qnty

		//current adjust
		//$currentStock = return_field_value("current_stock","product_details_master","id=".$hiddenProdId);
		$avg_rate=$currentStock=$stock_value=0;
		$sql = sql_select("select avg_rate_per_unit,current_stock,stock_value from product_details_master where id=$hiddenProdId");
		foreach($sql as $result)
		{
			$avg_rate = $result[csf("avg_rate_per_unit")];
			$currentStock = $result[csf("current_stock")];
			$stock_value = $result[csf("stock_value")];
		}

		$updateID_array=array();
		$update_data=array();
		if($before_prod_id==$hiddenProdId)
		{
			if($txtIssueQnty>$adjust_current_stock)
			{
				//check_table_status( $_SESSION['menu_id'],0);
				echo "20**Issue Quantity Exceeds The Current Stock Quantity";disconnect($con);
				exit();
			}

			$adjust_current_stock = $adjust_current_stock - $txtIssueQnty;
			$adjust_current_stock_value = $adjust_current_stock_value - $cons_amnt;
			
			if ($adjust_current_stock>0)
			{
				$adjust_avg_rate=number_format($adjust_current_stock_value/$adjust_current_stock,4,'.','');
			}
			else{
				$adjust_avg_rate=0;
			}
			// if Qty is zero then rate & value will be zero
			if ($adjust_current_stock<=0) 
			{
				$adjust_current_stock_value=0;
				$adjust_avg_rate=0;
			}

			$prod_update_data = "".$txtIssueQnty."*".$adjust_current_stock."*".$adjust_avg_rate."*'".$adjust_current_stock_value."'*'".$user_id."'*'".$pc_date_time."'";
			$prod_field_array = "last_issued_qnty*current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";
 			//$prodUpdate = sql_update("product_details_master",$prod_field_array,$prod_update_data,"id",$hiddenProdId,0);
		}
		else
		{
			//before adjust
			if ($adjust_current_stock>0)
			{
				$adjust_avg_rate=number_format($adjust_current_stock_value/$adjust_current_stock,4,'.','');
			}
			else{
				$adjust_avg_rate=0;
			}
			// if Qty is zero then rate & value will be zero
			if ($adjust_current_stock<=0) 
			{
				$adjust_current_stock_value=0;
				$adjust_avg_rate=0;
			}

			$updateID_array[]=$before_prod_id;
			$update_data[$before_prod_id]=explode("*",("0*".$adjust_current_stock."*'".$adjust_avg_rate."*'".$adjust_current_stock_value."'*'".$user_id."'*'".$pc_date_time."'"));

			if($txtIssueQnty>$currentStock)
			{
				//check_table_status( $_SESSION['menu_id'],0);
				echo "20**Issue Quantity Exceeds The Current Stock Quantity";disconnect($con);
				exit();
			}

			$currentStock = $currentStock-$txtIssueQnty;
			$stock_value = $stock_value-$cons_amnt;

			if ($currentStock>0)
			{
				$adjust_avg_rate=number_format($stock_value/$currentStock,4,'.','');
			}
			else{
				$adjust_avg_rate=0;
			}
			// if Qty is zero then rate & value will be zero
			if ($currentStock<=0) 
			{
				$stock_value=0;
				$adjust_avg_rate=0;
			}

			$updateID_array[]=$hiddenProdId;
			$update_data[$hiddenProdId]=explode("*",("".$txtIssueQnty."*".$currentStock."*".$adjust_avg_rate."*'".$stock_value."'*'".$user_id."'*'".$pc_date_time."'"));
			$prod_field_array = "last_issued_qnty*current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";
			//echo bulk_update_sql_statement("product_details_master","id",$prod_field_array,$update_data,$updateID_array);die;
			//$prodUpdate=execute_query(bulk_update_sql_statement("product_details_master","id",$prod_field_array,$update_data,$updateID_array));
		}
		//product master table data UPDATE END-------------------------------//

		//transaction table START--------------------------//
		$update_array = "balance_qnty*balance_amount*updated_by*update_date";
		$sql = sql_select("select a.id,a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$trans_tbl_id and b.entry_form=577 and a.item_category=14");
		$updateID_array = array();
		$update_data = array();
		foreach($sql as $result)
		{
			$adjBalance = $result[csf("balance_qnty")]+$result[csf("issue_qnty")];
			$adjAmount = $result[csf("balance_amount")]+$result[csf("amount")];
			$updateID_array[]=$result[csf("id")];
			$update_data[$result[csf("id")]]=explode("*",("".$adjBalance."*".$adjAmount."*'".$user_id."'*'".$pc_date_time."'"));

			$trans_data_array[$result[csf("id")]]['qnty']=$adjBalance;
			$trans_data_array[$result[csf("id")]]['amnt']=$adjAmount;
		}

		$transId=implode(",",$updateID_array);
		if($transId=="") { $transId=0; }
		if($hiddenProdId==$before_prod_id) { $balance_cond="(balance_qnty>0 or id in($transId))"; }
		else { $balance_cond="balance_qnty>0"; }
		//print_r($update_data);
		//LIFO/FIFO Start-----------------------------------------------//
		$isLIFOfifo=return_field_value("store_method","variable_settings_inventory","company_name=$cbo_company_id and variable_list=17 and item_category_id=14 and status_active=1 and is_deleted=0");
		if($isLIFOfifo==2) $cond_lifofifo=" DESC"; else $cond_lifofifo=" ASC";

		$txtIssueQnty = str_replace("'","",$txtIssueQnty);
		$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
		//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);

		$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hiddenProdId and $balance_cond and transaction_type in (1,4,5) and item_category=14 order by transaction_date $cond_lifofifo");
		foreach($sql as $result)
		{
			$recv_trans_id = $result[csf("id")]; // this row will be updated
			if($trans_data_array[$recv_trans_id]['qnty']=="")
			{
				$balance_qnty = $result[csf("balance_qnty")];
				$balance_amount = $result[csf("balance_amount")];
			}
			else
			{
				$balance_qnty = $trans_data_array[$recv_trans_id]['qnty'];
				$balance_amount = $trans_data_array[$recv_trans_id]['amnt'];
			}
			//echo $balance_qnty."**";
			$cons_rate = $result[csf("cons_rate")];
			if($cons_rate=="") { $cons_rate=$txt_rate; }

			$issueQntyBalance = $balance_qnty-$txtIssueQnty; // minus issue qnty
			$issueStockBalance = $balance_amount-($txtIssueQnty*$cons_rate);
			if($issueQntyBalance>=0)
			{
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				$amount = $txtIssueQnty*$cons_rate;
				//for insert
				if($data_array_mrr!="") $data_array_mrr .= ",";
				$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$trans_tbl_id.",577,".$hiddenProdId.",".$txtIssueQnty.",'".$cons_rate."',".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
				//for update
				if(!in_array($recv_trans_id,$updateID_array))
				{
					$updateID_array[]=$recv_trans_id;
				}

				$update_data[$recv_trans_id]=explode("*",("".$issueQntyBalance."*".$issueStockBalance."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				break;
			}
			else if($issueQntyBalance<0)
			{
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				$issueQntyBalance = $txtIssueQnty-$balance_qnty;
				$txtIssueQnty = $balance_qnty;
				$amount = $txtIssueQnty*$cons_rate;

				//for insert
				if($data_array_mrr!="") $data_array_mrr .= ",";
				$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$trans_tbl_id.",577,".$hiddenProdId.",".$balance_qnty.",'".$cons_rate."',".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
				//for update
				if(!in_array($recv_trans_id,$updateID_array))
				{
					$updateID_array[]=$recv_trans_id;
				}

				$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				$txtIssueQnty = $issueQntyBalance;
			}
			//$mrrWiseIsID++;
		}//end foreach

		 //echo "10**insert into inv_item_transfer_dtls (".$field_array_mrr.") values ".$data_array_mrr;
		// print_r($update_data);
		// die;
 		//####################################################--------------------
		//order_wise_pro_details table data insert Start---------------//
		$proportQ=true;
		$data_array_prop="";
		$save_string=explode(",",str_replace("'","",$save_data));
		if(count($save_string)>0 && str_replace("'","",$save_data)!="")
		{
			//order_wise_pro_details table data insert START-----//
			$field_array_proportionate="id,trans_id,trans_type,entry_form,dtls_id,po_breakdown_id,prod_id,quantity,issue_purpose,is_sales,inserted_by,insert_date";
			$po_array=array();
			for($i=0;$i<count($save_string);$i++)
			{
				$order_dtls=explode("**",$save_string[$i]);
				$order_id=$order_dtls[0];
				$order_qnty=$order_dtls[1];

				if( array_key_exists($order_id,$po_array) )
					$po_array[$order_id]+=$order_qnty;
				else
					$po_array[$order_id]=$order_qnty;
			}
			$i=0;
			foreach($po_array as $key=>$val)
			{
				if( $i>0 ) $data_array_prop.=",";
				//if( $id_proport=="" ) $id_proport = return_next_id( "id", "order_wise_pro_details", 1 ); else $id_proport = $id_proport+1;
				$id_proport = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$order_id=$key;
				$order_qnty=$val;
				$data_array_prop.="(".$id_proport.",".$transactionID.",2,577,".$dtls_id.",".$order_id.",".$hiddenProdId.",".$order_qnty.",".$cbo_issue_purpose.",".$hidden_is_sales.",".$user_id.",'".$pc_date_time."')";
				$i++;
			}
			/*if($data_array_prop!="")
			{
				$proportQ=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
			}*/
		}//end if
		//order_wise_pro_details table data insert END -----//


		//####################################################--------------------
		//roll table entry----------------------------------------//
		if( str_replace("'","",$hidden_is_roll_maintain)==1)
		{
			$data_array_roll="";
			//$rollDtlsID = return_next_id("id", "pro_roll_details", 1);

			$field_array_roll = "id,mst_id,dtls_id,po_breakdown_id,entry_form,roll_no,roll_id,qnty,inserted_by,insert_date";

			$poIDarr = explode(",",str_replace("'","",$txtRollPOid));
			$rollNoarr = explode(",",str_replace("'","",$txtRollNo));
			$rollPoQntyarr = explode(",",str_replace("'","",$txtRollPOQnty));
			$lopSize = count($poIDarr);
			for($i=0;$i<$lopSize;$i++)
			{
				$rollDtlsID = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				$rollno = return_field_value("roll_no","pro_roll_details","id=".$rollNoarr[$i]);
				if($i>0) $data_array_roll .=",";
				$data_array_roll.= "(".$rollDtlsID.",".$id.",".$dtls_id.",".$poIDarr[$i].",577,'".$rollno."',".$rollNoarr[$i].",".$rollPoQntyarr[$i].",'".$user_id."','".$pc_date_time."')";
			 	//$rollDtlsID = $rollDtlsID+1;
			}
			//$rollDtls=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,1);
		}
		else
		{
			$rollDtls=true;
		}
		//roll table entry end------------------------------------//

		if( str_replace("'","",$hidden_is_batch_maintain)==2 && str_replace("'","",$cbo_issue_purpose)!=3 && str_replace("'","",$txt_batch_id)!="" )
		{
			$batchQry=sql_update("pro_batch_create_mst",$field_array_batch_mst,$data_array_batch_mst,"id",$txt_batch_id,0);
			$batchQryDtls=sql_update("pro_batch_create_dtls",$field_array_batch_dtls,$data_array_batch_dtls,"mst_id",$txt_batch_id,0);
		}

		$rID=sql_update("inv_issue_master",$field_array,$data_array,"id",$id,0);
		$dtlsrID=sql_update("inv_grey_fabric_issue_dtls",$field_array_dtls,$data_array_dtls,"id",$dtls_id,0);
		$transID = sql_update("inv_transaction",$tr_field_array,$tr_data_array,"id",$transactionID,0);

		if($before_prod_id==$hiddenProdId)
		{
			$prodUpdate = sql_update("product_details_master",$prod_field_array,$prod_update_data,"id",$hiddenProdId,0);
		}
		else
		{
			$prodUpdate=execute_query(bulk_update_sql_statement("product_details_master","id",$prod_field_array,$update_data,$updateID_array));
		}

		//****************************************** BEFORE ENTRY ADJUST START *****************************************//
		$deletePropor = execute_query("DELETE FROM order_wise_pro_details WHERE dtls_id = $dtls_tbl_id and entry_form=577");
		$deleteRoll = execute_query("DELETE FROM pro_roll_details WHERE dtls_id = $dtls_tbl_id and entry_form=577");
		$deleteMrr = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$trans_tbl_id and entry_form=577");
		//****************************************** BEFORE ENTRY ADJUST START *****************************************//

		if(count($save_string)>0 && str_replace("'","",$save_data)!="")
		{
			if($data_array_prop!="")
			{
				$proportQ=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
			}
		}

		if( str_replace("'","",$hidden_is_roll_maintain)==1)
		{
			$rollDtls=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,1);
		}

		$mrrWiseIssueID=true; $upTrID=true;
		if(count($updateID_array)>0)
		{
			$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
		}

		if($data_array_mrr!="")
		{
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
		}

		//oci_rollback($con);
		//echo "10**".$rID."==".$dtlsrID."==".$transID."==".$prodUpdate."==".$proportQ."==".$batchQry."==".$batchQryDtls."==".$rollDtls; die;
		//mysql_query("ROLLBACK");

		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);

		if($db_type==0)
		{
			if($rID && $dtlsrID && $transID && $prodUpdate && $proportQ && $batchQry && $batchQryDtls && $rollDtls && $deletePropor && $deleteRoll && $deleteMrr && $upTrID && $mrrWiseIssueID)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$hidden_system_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$hidden_system_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $dtlsrID && $transID && $prodUpdate && $proportQ && $batchQry && $batchQryDtls && $rollDtls && $deletePropor && $deleteRoll && $deleteMrr && $upTrID && $mrrWiseIssueID)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$hidden_system_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$hidden_system_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		 //-------
	}
}

if($action=="mrr_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(sys_number,posted_account,id)
		{
 		$("#hidden_sys_number").val(sys_number); // mrr number
		$("#hidden_posted_account").val(posted_account); // check Posted account
		$("#hidden_id").val(id); // check Posted account

		parent.emailwindow.hide();
	}
</script>

</head>

<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="880" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
				<thead>
					<tr>
						<th>Supplier</th>
						<th>Search By</th>
						<th align="center" id="search_by_td_up" width="170">Please Enter Issue No</th>
						<th>Issue Date Range</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td align="center">
							<?
							echo create_drop_down( "cbo_supplier", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(21,24,25,26) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
							?>
						</td>
						<td align="center">
							<?
							$search_by = array(1=>'Issue No',2=>'Challan No',3=>'In House',4=>'Out Bound Subcontact',5=>'Job No',6=>'Wo No',7=>'Buyer');
							$dd="change_search_event(this.value, '0*0*1*1*0*0*1', '0*0*select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name*select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name*0*0*select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name', '../../../') ";
							echo create_drop_down( "cbo_search_by", 150, $search_by,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td width="" align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td align="center">
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
						</td>
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_mrr_search_view', 'search_div', 'woven_grey_fabric_issue_controller', 'setFilterGrid(\'view\',-1)')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td align="center" height="40" valign="middle" colspan="5">
							<? echo load_month_buttons(1);  ?>
							<!-- Hidden field here-->
							<input type="hidden" id="hidden_sys_number" value="hidden_sys_number" />
							<input type="hidden" id="hidden_posted_account" value="hidden_posted_account" />
							<input type="hidden" id="hidden_id" value="hidden_id" />
							<!-- END -->
						</td>
					</tr>
				</tbody>
			</tr>
		</table>
		<div align="center" valign="top" id="search_div" style="margin-top:10px"> </div>
	</form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_mrr_search_view")
{
	$ex_data = explode("_",$data);
	$supplier = $ex_data[0];
	$txt_search_by = $ex_data[1];
	$txt_search_common = trim($ex_data[2]);
	$fromDate = $ex_data[3];
	$toDate = $ex_data[4];
	$company = $ex_data[5];
	$sql_cond="";
	if( str_replace("'","",$fromDate)!="" && str_replace("'","",$toDate)!="" )
	{
		if($db_type==0)
		{
			$sql_cond .= " and a.issue_date between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
		}
		else
		{
			$sql_cond .= " and a.issue_date between '".change_date_format($fromDate,'','',1)."' and '".change_date_format($toDate,'','',1)."'";
		}
	}

	if($supplier>0) $sql_cond .= " and a.supplier_id='$supplier'";
	if($company>0) $sql_cond .= " and a.company_id='$company'";

	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier",'id','supplier_name');
	$company_arr = return_library_array("select id, company_name from lib_company",'id','company_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer",'id','buyer_name');
	$issuQnty_arr=array();

	if($txt_search_common!="")
	{
		if($txt_search_by==5)
		{
			$issuQntyData = sql_select("select a.mst_id, sum(b.quantity) as qty from inv_grey_fabric_issue_dtls a, order_wise_pro_details b, wo_po_break_down c where a.id=b.dtls_id and b.po_breakdown_id=c.id and b.entry_form=577 and c.job_no_mst like '%$txt_search_common' and a.status_active=1 and a.is_deleted=0 group by a.mst_id");
			$mst_ids='';
			foreach($issuQntyData as $row)
			{
				$mst_ids.=$row[csf('mst_id')].",";
				$issuQnty_arr[$row[csf('mst_id')]]=$row[csf('qty')];
			}
			$mst_ids=chop($mst_ids,',');
			if($mst_ids=="") $mst_ids=0;
			$sql_cond .= " and a.id in($mst_ids)";
		}
		else
		{
			$issuQnty_arr = return_library_array("select mst_id, sum(issue_qnty) as qty from inv_grey_fabric_issue_dtls where status_active=1 and is_deleted=0 group by mst_id",'mst_id','qty');

			if($txt_search_by==1)
			{
				$sql_cond .= " and a.issue_number like '%$txt_search_common'";
			}
			else if($txt_search_by==2)
			{
				$sql_cond .= " and a.challan_no like '%$txt_search_common%'";
			}
			else if($txt_search_by==3)
			{
				$sql_cond .= " and a.knit_dye_source=1 and a.knit_dye_company='$txt_search_common'";
			}
			else if($txt_search_by==4)
			{
				$sql_cond .= " and a.knit_dye_source=3 and a.knit_dye_company='$txt_search_common'";
			}
			else if($txt_search_by==5)
			{
				$sql_cond .= " and a.buyer_job_no like '%$txt_search_common%'";
			}
			else if($txt_search_by==6)
			{
				$sql_cond .= " and a.booking_no like '%$txt_search_common%'";
			}
			else if($txt_search_by==7)
			{
				$sql_cond .= " and a.buyer_id = '$txt_search_common'";
			}
		}
	}
	else
	{
		$issuQnty_arr = return_library_array("select mst_id, sum(issue_qnty) as qty from inv_grey_fabric_issue_dtls where status_active=1 and is_deleted=0 group by mst_id",'mst_id','qty');
	}

	if($db_type==0) $year_field="YEAR(a.insert_date)";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY')";
	else $year_field="";//defined Later

	$sql = "SELECT a.id, a.issue_number_prefix_num, a.issue_number, $year_field as year, a.issue_basis, a.issue_purpose, a.buyer_id, a.booking_id, a.booking_no, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.challan_no, a.is_posted_account, c.is_sales, d.buyer_id as sales_buyer
		from inv_issue_master a, inv_grey_fabric_issue_dtls b left join order_wise_pro_details c on b.trans_id=c.trans_id and b.id=c.dtls_id and c.status_active=1 and c.is_deleted=0 left join fabric_sales_order_mst d on c.po_breakdown_id=d.id and c.is_sales=1
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.entry_form=577 $sql_cond and b.status_active=1 and b.is_deleted=0
		group by a.id, a.issue_number_prefix_num, a.issue_number, a.insert_date, a.issue_basis, a.issue_purpose, a.buyer_id, a.booking_id, a.booking_no, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.challan_no, a.is_posted_account, c.is_sales, d.buyer_id
		order by id";
		// , c.po_breakdown_id
	//echo $sql;//die;
	$result = sql_select( $sql );
	?>
	<div>
		<div style="width:945px;">
			<table cellspacing="0" cellpadding="0" width="100%" class="rpt_table" border="1" rules="all">
				<thead>
					<th width="30">SL</th>
					<th width="70">Issue No</th>
					<th width="60">Year</th>
					<th width="80">Date</th>
					<th width="100">Purpose</th>
					<th width="80">Challan No</th>
					<th width="100">Issue Qnty</th>
					<th width="120">Booking No</th>
					<th width="120">Dyeing Company</th>
					<th>Buyer</th>
				</thead>
			</table>
		</div>
		<div style="width:945px;overflow-y:scroll;max-height:230px;" id="search_div" >
			<table cellspacing="0" cellpadding="0" width="927" class="rpt_table" id="view" border="1" rules="all">
				<?php
				$i=1;
				foreach( $result as $row )
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					/*if ($row[csf("is_sales")] == 1)  // for is sales
					{
						$salse_id=$row[csf("po_breakdown_id")];
						$sales_buyer_id = return_field_value("buyer_id","fabric_sales_order_mst","id=$salse_id");
					}*/
                   // $issuQnty = return_field_value("sum(cons_quantity)","inv_transaction","mst_id=".$row[csf("id")]." and item_category=14 and transaction_type=2 and status_active=1 and is_deleted=0");
					$issuQnty =$issuQnty_arr[$row[csf("id")]];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onclick="js_set_value('<? echo $row[csf("issue_number")];?>','<? echo $row[csf("is_posted_account")];?>','<? echo $row[csf("id")];?>');">
						<td width="30"><?php echo $i; ?></td>
						<td width="70"><p>&nbsp;&nbsp;<?php echo $row[csf("issue_number_prefix_num")];?></p></td>
						<td width="60" align="center"><p><?php echo $row[csf("year")];?></p></td>
						<td width="80" align="center"><p><?php echo change_date_format($row[csf("issue_date")]); ?></p></td>
						<td width="100"><p><?php echo $yarn_issue_purpose[$row[csf("issue_purpose")]]; ?></p></td>
						<td width="80"><p><?php echo $row[csf("challan_no")]; ?></p></td>
						<td width="100" align="right"><p><?php echo number_format($issuQnty,2,'.',''); ?>&nbsp;</p></td>
						<td width="120"><p><?php echo $row[csf("booking_no")]; ?></p></td>
						<td width="120"><p><?php
						if($row[csf("knit_dye_source")]==1) $knit_com=$company_arr[$row[csf("knit_dye_company")]]; else $knit_com=$supplier_arr[$row[csf("knit_dye_company")]];
						echo $knit_com;
						?></p>
					</td>
					<td width=""><p>
						<?php 
						if ($row[csf("is_sales")] == 1) echo $buyer_arr[$sales_buyer_id]; 
						else echo $buyer_arr[$row[csf("buyer_id")]]; ?>	
					</p></td>
				</tr>
				<?php
				$i++;
			}
			?>
		</table>
	</div>
</div>
<?

exit();

}

if($action=="populate_data_from_data")
{
	$sql = "SELECT a.id, a.issue_number, a.issue_basis, a.issue_purpose, a.entry_form, a.item_category, a.company_id, a.location_id, a.supplier_id, a.store_id, a.buyer_id, a.buyer_job_no, a.style_ref, a.booking_id, a.booking_no, a.batch_no, a.issue_date, a.sample_type, a.knit_dye_source, a.knit_dye_company, a.challan_no, a.loan_party, a.lap_dip_no, a.gate_pass_no, a.item_color, a.color_range, a.remarks, a.other_party, a.order_id, a.service_booking_no, c.is_sales, c.po_breakdown_id,a.req_id,a.req_no,a.fso_no,a.fso_id
		from inv_issue_master a, inv_grey_fabric_issue_dtls b left join order_wise_pro_details c on b.trans_id=c.trans_id and b.id=c.dtls_id and c.status_active=1 and c.is_deleted=0
		where a.id=b.mst_id and a.id='$data' and a.entry_form=577 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id, a.issue_number, a.issue_basis, a.issue_purpose, a.entry_form, a.item_category, a.company_id, a.location_id, a.supplier_id, a.store_id, a.buyer_id, a.buyer_job_no, a.style_ref, a.booking_id, a.booking_no, a.batch_no, a.issue_date, a.sample_type, a.knit_dye_source, a.knit_dye_company, a.challan_no, a.loan_party, a.lap_dip_no, a.gate_pass_no, a.item_color, a.color_range, a.remarks, a.other_party, a.order_id, a.service_booking_no, c.is_sales, c.po_breakdown_id,a.req_id,a.req_no,a.fso_no,a.fso_id";
	//echo $sql;
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#hidden_system_id').val(".$row[csf("id")].");\n";
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		//echo"get_php_form_data( 'requires/woven_grey_fabric_issue_controller', ".$row[csf("company_id")].", 'load_drop_down_store', 'store_td' );\n";
		echo "get_php_form_data(".$row[csf("company_id")].", 'is_roll_maintain', 'requires/woven_grey_fabric_issue_controller');\n";
 		//echo"load_drop_down( 'requires/woven_grey_fabric_issue_controller', ".$row[csf("company_id")].", 'load_drop_down_store', 'store_td' );\n";
		echo "$('#cbo_basis').val(".$row[csf("issue_basis")].");\n";
		echo "$('#cbo_basis').attr('disabled','true')".";\n";
		echo "$('#cbo_issue_purpose').val(".$row[csf("issue_purpose")].");\n";
		echo "$('#cbo_issue_purpose').attr('disabled','true')".";\n";
		echo "enable_disable();\n";
		echo "$('#txt_issue_date').val('".change_date_format($row[csf("issue_date")])."');\n";
		echo "$('#txt_booking_id').val(".$row[csf("booking_id")].");\n";
		echo "$('#txt_booking_no').val('".$row[csf("booking_no")]."');\n";

		if($row[csf("issue_basis")] == 4 )
		{
			echo "$('#hidden_sales_orde_requisition_id').val(".$row[csf("fso_id")].");\n";
			echo "$('#txt_sales_orde_requisition').val('".$row[csf("fso_no")]."');\n";
		}
		else if($row[csf("issue_basis")] == 5 )
		{
			echo "$('#hidden_sales_orde_requisition_id').val(".$row[csf("req_id")].");\n";
			echo "$('#txt_sales_orde_requisition').val('".$row[csf("req_no")]."');\n";
		}
		echo "$('#cbo_dyeing_source').val(".$row[csf("knit_dye_source")].");\n";
		echo "$('#cbo_dyeing_source').attr('disabled','true')".";\n";
		echo "load_drop_down( 'requires/woven_grey_fabric_issue_controller', ".$row[csf("knit_dye_source")]."+'**'+".$row[csf("company_id")].", 'load_drop_down_knit_com', 'dyeing_company_td' );\n";
		echo "$('#cbo_dyeing_company').val(".$row[csf("knit_dye_company")].");\n";
		echo "$('#cbo_dyeing_company').attr('disabled','true')".";\n";
		echo "$('#txt_booking_no').attr('disabled','true')".";\n";

		echo "$('#txt_challan_no').val('".$row[csf("challan_no")]."');\n";

		if($db_type==0)
		{
			$batchdata = return_field_value("concat_ws('**',batch_no,color_id)","pro_batch_create_mst","id='".$row[csf("batch_no")]."'");
		}
		else
		{
			$batchdata = return_field_value("batch_no || '**' || color_id as batch_data","pro_batch_create_mst","id='".$row[csf("batch_no")]."'","batch_data");
		}

		$batchdata=explode("**",$batchdata);
		$batchNo=$batchdata[0];
		$batchColor=return_field_value("color_name","lib_color","id='".$batchdata[1]."'");
		echo "$('#txt_batch_no').val('".$batchNo."');\n";
		echo "$('#txt_batch_id').val(".$row[csf("batch_no")].");\n";

		echo "$('#txt_service_booking_no').val('".$row[csf("service_booking_no")]."');\n";

		if ($row[csf("is_sales")]==1)
		{
			$sales_id = $row[csf("po_breakdown_id")];
			/*$within_group = return_field_value("within_group","fabric_sales_order_mst","id=$sales_id");
			if ($within_group==1) 
			{
				$sales_buyer_id = return_field_value("customer_buyer","fabric_sales_order_mst","id=$sales_id");
			}
			else
			{
				$sales_buyer_id = return_field_value("buyer_id","fabric_sales_order_mst","id=$sales_id");
			}*/
			$sales_style_ref = return_field_value("style_ref_no","fabric_sales_order_mst","id=$sales_id");
			echo "$('#cbo_buyer_name').val(".$row[csf("buyer_id")].");\n";
			echo "$('#txt_style_ref').val('".$sales_style_ref."');\n";
		}
		else
		{
			echo "$('#cbo_buyer_name').val(".$row[csf("buyer_id")].");\n";
			echo "$('#txt_style_ref').val('".$row[csf("style_ref")]."');\n";
		}

		if($row[csf("issue_purpose")]==8)
		{
			echo "load_drop_down( 'requires/woven_grey_fabric_issue_controller', '".$row[csf("booking_no")]."'+'_'+".$row[csf("issue_purpose")].", 'load_drop_down_color','color_td');\n";
			echo "set_multiselect('cbo_color_id','0','0','','0');\n";
		}

		if($row[csf("order_id")]!="")
		{
			$orSql=sql_select("select id as order_id,job_no as po_number,buyer_id from fabric_sales_order_mst where id in(".$row[csf("order_id")].") and status_active=1 and is_deleted=0");

			if (empty($orSql)) {
				$orSql = sql_select("select po_number from wo_po_break_down where id in (".$row[csf("order_id")].")");
			}
			$orderNumbers="";
			foreach($orSql as $key=>$val)
			{
				if($orderNumbers!="") $orderNumbers .=",";
				$orderNumbers .= $val[csf("po_number")];
			}
			echo "$('#txt_order_no').val('".$orderNumbers."');\n";
			echo "$('#hidden_order_id').val('".$row[csf("order_id")]."');\n";
		}
		//echo "enable_disable();\n";
		echo "$('#hidden_is_sales').val('".$row[csf("is_sales")]."');\n"; 
	}

	exit();
}

if($action=="show_dtls_list_view")
{
	//$sql = "select b.id, a.company_id,a.issue_number, a.challan_no, b.program_no, b.issue_qnty, b.color_id, b.yarn_lot, b.yarn_count, b.store_name,b.floor_id,b.room, b.rack, b.self, d.product_name_details 	from inv_issue_master a, inv_grey_fabric_issue_dtls b, product_details_master d where a.id=b.mst_id and b.prod_id=d.id and a.entry_form=577 and a.id=$data";

	$sql = "SELECT b.id, a.company_id,a.issue_number, a.challan_no, b.program_no, b.issue_qnty, b.color_id, b.yarn_lot, b.yarn_count, b.store_name,e.floor_id,e.room, e.rack, e.self, d.product_name_details
	    from inv_issue_master a, inv_grey_fabric_issue_dtls b, product_details_master d, inv_transaction e
	    where a.id=b.mst_id and b.prod_id=d.id and a.entry_form=577 and a.id=$data and b.trans_id = e.id and b.status_active=1 and b.is_deleted=0";
	//echo $sql;
	$result = sql_select($sql);

	$yarn_count = return_library_array("select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$store_arr = return_library_array("select id, store_name from lib_store_location",'id','store_name');
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
	//$floorRoomRackShelf_array=return_library_array( "SELECT floor_room_rack_id, floor_room_rack_name FROM lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");
	//$arr=array(5=>$yarn_count,6=>$store_arr);

	$lib_room_rack_shelf_sql = "select b.company_id,b.location_id,b.store_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name floor_name,c.floor_room_rack_name room_name,d.floor_room_rack_name rack_name,e.floor_room_rack_name shelf_name from lib_floor_room_rack_dtls b
	 	left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
	 	left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
	 	left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
	 	left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
	 	where b.status_active=1 and b.is_deleted=0 ";
	$lib_room_rack_shelf_arr=sql_select($lib_room_rack_shelf_sql);
 	foreach ($lib_room_rack_shelf_arr as $room_rack_shelf_row) 
 	{
 		$company  = $room_rack_shelf_row[csf("company_id")];
 		$floor_id = $room_rack_shelf_row[csf("floor_id")];
 		$room_id  = $room_rack_shelf_row[csf("room_id")];
 		$rack_id  = $room_rack_shelf_row[csf("rack_id")];
 		$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
 		$bin_id   = $room_rack_shelf_row[csf("bin_id")];

 		if($floor_id!="" && $room_id=="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
 			$lib_floor_arr[$floor_id] = $room_rack_shelf_row[csf("floor_name")];
 		}

 		if($floor_id!="" && $room_id!="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
 			$lib_room_arr[$floor_id][$room_id] = $room_rack_shelf_row[csf("room_name")];
 		}

 		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id=="" && $bin_id==""){
 			$lib_rack_arr[$floor_id][$room_id][$rack_id] = $room_rack_shelf_row[csf("rack_name")];
 		}

 		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id==""){
 			$lib_shelf_arr[$floor_id][$room_id][$rack_id][$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
 		}
 	}
 	unset($lib_room_rack_shelf_arr);

	?>
	<table cellspacing="0" cellpadding="0" width="500" class="rpt_table" border="1" rules="all">
		<thead>
			<th width="35">SL</th>
			<th width="200">Item Description</th>
			<th width="90">Issued Qnty</th>
		</thead>
	</table>
	<div id="search_div" >
		<table cellspacing="0" cellpadding="0" width="500" class="rpt_table" id="view" border="1" rules="all">
			<?php
			$i=1;
			foreach( $result as $row )
			{
				if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";

				$count_id=array_unique(explode(",",$row[csf("yarn_count")])); $count='';
				foreach($count_id as $val)
				{
					if($count=='') $count=$yarn_count[$val]; else $count.=",".$yarn_count[$val];
				}

				$color='';
				$color_id=array_unique(explode(",",$row[csf('color_id')]));
				foreach($color_id as $val)
				{
					if($val>0) $color.=$color_arr[$val].",";
				}
				$color=chop($color,',');

				$store_id=$row[csf('store_name')];
				$company_id=$row[csf('company_id')];
				/*$floor_id=$row[csf('floor_id')];
				$room_id=$row[csf('room')];
				$rack_id=$row[csf('rack')];
				$self_id=$row[csf('self')];*/
				//if($rack_id>0){$rackIDcond="and b.rack_id=$rack_id";}else{$rackIDcond="";}


				$floor_name = $lib_floor_arr[$row[csf("floor_id")]];
 				$room_name = $lib_room_arr[$row[csf("floor_id")]][$row[csf("room")]];
 				$rack_name = $lib_rack_arr[$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]];
 				$shelf_name = $lib_shelf_arr[$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]];

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onclick="get_php_form_data('<? echo $row[csf("id")]; ?>','populate_child_from_data_for_update','requires/woven_grey_fabric_issue_controller');set_form_data('<? echo $row[csf("floor_id")]."**".$row[csf("room")]."**".$row[csf("rack")]."**".$row[csf("self")]."**".$floor_name."**".$room_name."**".$rack_name."**".$shelf_name; ?>');">
					<td width="35"><?php echo $i; ?></td>
					<td width="200"><p><?php echo $row[csf("product_name_details")]; ?></p></td>
					<td width="90" align="right"><p><?php echo number_format($row[csf("issue_qnty")],2); ?></p></td>
				</tr>
				<?php
				$i++;
			}
			?>
		</table>
	</div>
	<?
	

	exit();
}

if($action=="populate_child_from_data_for_update")
{
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
	
	$sql = "SELECT b.id, a.issue_number,a.company_id, a.challan_no, a.order_id, a.booking_id, a.issue_basis, a.issue_purpose, b.trans_id, b.distribution_method, b.program_no, b.no_of_roll, b.roll_no, b.roll_po_id, b.roll_wise_issue_qnty, b.prod_id, b.issue_qnty, b.color_id, b.yarn_lot, b.yarn_count, b.store_name, b.rack, b.self, b.stitch_length, b.remarks, d.product_name_details, d.current_stock, c.cons_rate as rate
	from inv_issue_master a, inv_grey_fabric_issue_dtls b, inv_transaction c, product_details_master d
	where a.id=b.mst_id and b.prod_id=d.id  and a.entry_form=577 and b.id=$data and a.id=b.mst_id and c.id=b.trans_id and b.prod_id=c.prod_id and c.prod_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0";
	//echo $sql;
	$result = sql_select($sql);
	foreach($result as $row)
	{
		echo "$('#cbo_store_name').val('".$row[csf('store_name')]."');\n";
		echo "$('#txtNoOfRoll').val('".$row[csf('no_of_roll')]."');\n";
		echo "$('#txtRollNo').val('".$row[csf('roll_no')]."');\n";
		echo "$('#txtRollPOid').val('".$row[csf('roll_po_id')]."');\n";
		echo "$('#txtRollPOQnty').val('".$row[csf('roll_wise_issue_qnty')]."');\n";

		echo "$('#txtItemDescription').val('".$row[csf('product_name_details')]."');\n";
		echo "$('#txt_global_stock').val('".$row[csf('current_stock')]."');\n";
		echo "$('#hiddenProdId').val('".$row[csf('prod_id')]."');\n";

		echo "$('#txtReqQnty').val('".$row[csf('issue_qnty')]."');\n";
		echo "$('#txtIssueQnty').val('".$row[csf('issue_qnty')]."');\n";
		echo "$('#hiddenIssueQnty').val('".$row[csf('issue_qnty')]."');\n";
		echo "$('#hiddenAvgRate').val('".$row[csf('rate')]."');\n";

		echo "$('#txtYarnLot').val('".$row[csf('yarn_lot')]."');\n";
		//echo "$('#cbo_yarn_count').val('".$row[csf('yarn_count')]."');\n";
		if($row[csf('yarn_count')]==0) $count=""; else $count=$row[csf('yarn_count')];

		echo "$('#txt_rack_hidden').val('".$row[csf('rack')]."');\n";
		echo "$('#txt_shelf_hidden').val('".$row[csf('self')]."');\n";
		echo "$('#txt_remarks').val('".$row[csf('remarks')]."');\n";
		echo "$('#txt_stitch_length').val('".$row[csf('stitch_length')]."');\n";

		if($row[csf('program_no')]==0) $program_no=""; else $program_no=$row[csf('program_no')];
		echo "$('#txt_program_no').val('".$program_no."');\n";
		
		echo "$('#hidden_is_sales').val('1');\n";
		
		//echo "disable_enable_fields('show_textcbo_yarn_count','1','','');\n";
		//issue qnty popup data arrange
		$sqlIN = sql_select("select trans_id,po_breakdown_id,quantity from order_wise_pro_details where dtls_id=".$row[csf("id")]." and entry_form=577 and trans_type=2 and status_active=1 and is_deleted=0");
		$poWithValue="";
		$poID="";
		$transaction_id="";
		foreach($sqlIN as $res)
		{
			if($poWithValue!="") $poWithValue .=",";
			if($poID!="") $poID .=",";
			$poWithValue .= $res[csf("po_breakdown_id")]."**".$res[csf("quantity")];
			$poID .=$res[csf("po_breakdown_id")];
			$transaction_id = $res[csf("trans_id")];
		}

		echo "$('#save_data').val('".$poWithValue."');\n";
		echo "$('#all_po_id').val('".$poID."');\n";
		echo "$('#distribution_method_id').val('".$row[csf('distribution_method')]."');\n";
		// echo '====#'.$poID.'@====';die;
		if($poID!="")
		{
			echo "load_drop_down( 'requires/woven_grey_fabric_issue_controller', '$poID'+'_'+".$row[csf('issue_purpose')]."+'_'+'".$row[csf('color_id')]."'+'_'+'".$row[csf('issue_basis')]."'+'_'+'".$row[csf('order_id')]."'+'_'+'".$is_salesOrder."', 'load_drop_down_color','color_td');\n";
			echo "set_multiselect('cbo_color_id','0','0','','0');\n";
		}
		
		//echo "$('#cbo_color_id').val('".$row[csf('color_id')]."');\n";
		echo "set_multiselect('cbo_color_id','0','1','".$row[csf('color_id')]."','0');\n";
		//--------hidden id for update-------
		echo "$('#dtls_tbl_id').val('".$row[csf('id')]."');\n";
		echo "$('#trans_tbl_id').val('".$row[csf('trans_id')]."');\n";
		echo "$('#hidden_yet_issue_qnty').val(".($row[csf('current_stock')]+$row[csf('issue_qnty')]).");\n";

		if($row[csf('order_id')]!="" && $row[csf('order_id')]!=0)
		{
			echo "get_php_form_data('".$row[csf('order_id')]."'+\"**\"+'".$row[csf('prod_id')]."'+\"**\"+'".$row[csf('company_id')]."'+\"**\"+'".$program_no."'+\"**\"+".$row[csf('issue_basis')].", \"populate_data_about_order\", \"requires/woven_grey_fabric_issue_controller\" );";
		}
		else if(!empty($poID))
		{
			echo "get_php_form_data('".$poID."'+\"**\"+'".$row[csf('prod_id')]."'+\"**\"+'".$row[csf('company_id')]."'+\"**\"+'".$program_no."'+\"**\"+".$row[csf('issue_basis')].", \"populate_data_about_order\", \"requires/woven_grey_fabric_issue_controller\" );";
		}
		echo "set_button_status(1, permission, 'fnc_grey_fabric_issue_entry',1,0);\n";
	}
	exit();
}

if ($action=="grey_fabric_issue_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$print_with_vat=$data[4];
	$is_sales=$data[7];
	//print_r ($data);

	$sql="SELECT id, issue_number, issue_number_prefix_num, issue_date, issue_basis, issue_purpose, knit_dye_source, knit_dye_company, booking_id, batch_no, buyer_id, challan_no, style_ref, order_id from  inv_issue_master where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;
	$supplier_library=array(); $supplier_short_library=array();
	$supplier_data=sql_select( "select id,supplier_name,short_name from lib_supplier");
	foreach ($supplier_data as $row)
	{
		$supplier_library[$row[csf('id')]]=$row[csf('supplier_name')];
		$supplier_short_library[$row[csf('id')]]=$row[csf('short_name')];
	}

	$dataArray=sql_select($sql);
	$grey_issue_basis=array(1=>"Booking",2=>"Independent",3=>"Knitting Plan");
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$batch_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no"  );
	$booking_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no"  );
	$booking_non_order_arr=return_library_array( "select id, booking_no from  wo_non_ord_samp_booking_mst", "id", "booking_no"  );
	$po_arr=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number"  );
	$job_arr=return_library_array( "select id, job_no_mst from  wo_po_break_down","id","job_no_mst");
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand",'id','brand_name');
	$mc_dia_arr = return_library_array("select id, dia_width from lib_machine_name",'id','dia_width');


	$lib_room_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "room_id","floor_room_rack_name" );
	$lib_rack_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "rack_id","floor_room_rack_name" );
	$lib_shelf_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "shelf_id","floor_room_rack_name" );
	$lib_bin_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "bin_id","floor_room_rack_name" );

	?>
	<div style="width:930px;">
		<table width="900" cellspacing="0" align="right" style="margin-bottom:10px">
			<tr>
				<td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			</tr>
			<tr class="form_caption">

				<?
				$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
				?>
				<td  align="left">
					<?
					foreach($data_array as $img_row)
					{
						?>
						<img src='../../../<? echo $img_row[csf('image_location')]; ?>' height='70' width='160' align="middle" />
						<?
					}
					?>
				</td>

				<td colspan="4" align="center" style="font-size:14px">
					<?

					echo show_company($data[0],'','');//Aziz
					/*$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
					?>
						 <? echo $result[csf('plot_no')]; ?>
						 <? echo $result[csf('level_no')];?>
						 <? echo $result[csf('road_no')]; ?>
						 <? echo $result[csf('block_no')];?>
						 <? echo $result[csf('city')];?>
						 <? echo $result[csf('zip_code')]; ?>
						 <?php echo $result[csf('province')];?>
						 <? echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email Address: <? echo $result[csf('email')];?>
						Website No: <? echo $result[csf('website')];
					}*/
					?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><u>Knit Grey Fabric Delivery Challan/Gate pass</u></strong></td>
			</tr>
			<tr>
				<td rowspan="3" colspan="2" width="300" valign="top"><strong>Dyeing Company:</strong>
				<?
				$supp_add=$dataArray[0][csf('knit_dye_company')];
				$nameArray=sql_select( "select address_1,web_site,email,country_id from lib_supplier where id=$supp_add");
				foreach ($nameArray as $result)
				{
					$address="";
					if($result!="") $address=$result['address_1'];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
				}
				if ($dataArray[0][csf('knit_dye_source')]==1) echo $company_library[$dataArray[0][csf('knit_dye_company')]].'<br>'.'Address :- '.$address; else if ($dataArray[0][csf('knit_dye_source')]==3) echo $supplier_library[$dataArray[0][csf('knit_dye_company')]].'<br>'.'Address :- '.$address; ?></td>
				<td width="120"><strong>Issue ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
				<td width="125"><strong>Issue Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Issue Basis :</strong></td> <td width="175px"><? echo $grey_issue_basis[$dataArray[0][csf('issue_basis')]]; ?></td>
				<td><strong>Issue Purpose:</strong></td><td width="175px"><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Dyeing Source:</strong></td><td width="175px"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
				<td><strong>F. Booking No:</strong></td><td width="175px">
					<?
					if($dataArray[0][csf('issue_basis')]==1)
					{
						if(($dataArray[0][csf('issue_purpose')]==8 || $dataArray[0][csf('issue_purpose')]==3 || $dataArray[0][csf('issue_purpose')]==26 || $dataArray[0][csf('issue_purpose')]==29 || $dataArray[0][csf('issue_purpose')]==30 || $dataArray[0][csf('issue_purpose')]==31) && $dataArray[0][csf('issue_basis')]==1)
						{
							echo $booking_non_order_arr[$dataArray[0][csf('booking_id')]];
						}
						else
						{
							echo $booking_arr[$dataArray[0][csf('booking_id')]];
						}
					}
					else
					{
						echo $dataArray[0][csf('booking_id')];
					}
					?>
				</td>
			</tr>
			<tr>
				<td><strong>Challan No:</strong></td><td width="175px"><? if($dataArray[0][csf('challan_no')]=="") echo $dataArray[0][csf('issue_number_prefix_num')]; else echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>Batch Number:</strong></td><td width="175px"><? echo $batch_arr[$dataArray[0][csf('batch_no')]]; ?></td>
				<?
				if ($data[5]==6 && $data[6]==0) // Print 2 && Cancel
				{
					?>
					<td><strong></strong></td><td width="175px"></td>
					<?
				}
				else
				{
					?>
					<td><strong>Buyer Name:</strong></td><td width="175px"><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td>
					<?
				}
				?>
			</tr>
			<tr>
				<td>
					<?
					if($dataArray[0][csf('issue_purpose')]==8)
					{

						if($db_type==0)
						{
							$prodID=return_field_value("group_concat(b.prod_id) as prod_id","inv_issue_master c, inv_grey_fabric_issue_dtls a, pro_grey_prod_entry_dtls b","c.id=a.mst_id and a.prod_id=b.prod_id and a.mst_id='$data[1]' and c.entry_form=577 and c.issue_basis=1 and c.issue_purpose=8 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0","prod_id");
						}
						else
						{
							$prodID=return_field_value("LISTAGG(b.prod_id, ',') WITHIN GROUP (ORDER BY b.prod_id)  as prod_id","inv_issue_master c, inv_grey_fabric_issue_dtls a, pro_grey_prod_entry_dtls b","c.id=a.mst_id and a.prod_id=b.prod_id and a.mst_id='$data[1]' and c.entry_form=577 and c.issue_basis=1 and c.issue_purpose=8 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0","prod_id");
						}
					}
					else
					{
						if($db_type==0)
						{
							$po_id=return_field_value("group_concat(b.po_breakdown_id) as po_id","inv_grey_fabric_issue_dtls a, order_wise_pro_details b","a.id=b.dtls_id and a.mst_id='$data[1]' and b.entry_form=577 and b.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","po_id");
						}
						else
						{
							$po_id=return_field_value("LISTAGG(b.po_breakdown_id, ',') WITHIN GROUP (ORDER BY b.po_breakdown_id) as po_id","inv_grey_fabric_issue_dtls a, order_wise_pro_details b","a.id=b.dtls_id and a.mst_id='$data[1]' and b.entry_form=577 and b.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","po_id");
						}
					}
					
					$po_exp=array_unique(explode(',',$po_id));
					$po_no=''; $job='';
					if ($is_sales==1) 
					{
						$sales_no_sql=sql_select("select id,job_no, style_ref_no, buyer_id, customer_buyer, within_group, po_job_no from fabric_sales_order_mst where id in($po_id) and status_active=1 and is_deleted=0");
						foreach ($sales_no_sql as $row) 
						{
							$fso_po_arr[$row[csf('id')]]=$row[csf('job_no')];
							$fso_po_job_arr[$row[csf('id')]]=$row[csf('po_job_no')];			
						}

						foreach($po_exp as $row)
						{
							if($po_no=='') $po_no=$fso_po_arr[$row]; else $po_no.=', '.$fso_po_arr[$row];
							if($job=='') $job=$fso_po_job_arr[$row]; else $job.=','.$fso_po_job_arr[$row];
						}
					}
					else 
					{
						foreach($po_exp as $row)
						{
							if($po_no=='') $po_no=$po_arr[$row]; else $po_no.=', '.$po_arr[$row];
							if($job=='') $job=$job_arr[$row]; else $job.=','.$job_arr[$row];
						}
					}				
					$job=implode(",",array_unique(explode(',',$job)));
					?>
					<strong>Job No:</strong>
				</td>
				<td width="175px" colspan="3"><? echo $job;//$job_arr[$dataArray[0][csf('order_id')]]; ?></td>
				<?
				if ($data[5]==6 && $data[6]==0) // Print 2 && Cancel
				{
					?>
					<td><strong></strong></td><td width="175px"></td>
					<?
				}
				else
				{
					?>
					<td><strong>Style Ref.:</strong></td>
					<td style="word-break:break-all" width="175px"><? echo $dataArray[0][csf('style_ref')]; ?></td>
					<?
				}
				?>						
			</tr>
			<tr>
				<td><strong>Order No:</strong></td><td colspan="5"><? echo $po_no;//$po_arr[$dataArray[0][csf('order_id')]]; ?></td>
			</tr>
			<?
			if($print_with_vat==1)
			{
				?>
				<tr>
					<td><strong>VAT Number :</strong></td>
					<td><p>
						<?
						$vat_no=return_field_value("vat_number","lib_company","id=".$data[0],"vat_number");
						echo $vat_no;
						?></p></td>
					<td><strong>Bar Code:</strong></td><td colspan="3" id="barcode_img_id"></td>
				</tr>
				<?
			}
			else
			{
				?>
				<tr>
					<td valign="top">&nbsp;</strong></td>
					<td>&nbsp;</td>
					<td><strong>Bar Code:</strong></td><td colspan="3" id="barcode_img_id"></td>
				</tr>
				<?
			}
			?>
		</table>
		<div style="width:100%;">
				<table align="left" cellspacing="0" width="930"  border="1" rules="all" class="rpt_table" >
					<thead bgcolor="#dddddd" align="center">
						<th width="20">SL</th>
						<th width="50">Prog. No</th>
						<th width="130">Item Des.</th>
						<th width="50">Stich Length</th>
						<th width="40">GSM</th>
						<th width="40">Fin. Dia</th>
						<th width="30">M/C Dia</th>
						<th width="70">Color</th>
						<th width="40">Roll</th>
						<th width="70">Issue Qty</th>
						<th width="40">UOM</th>
						<th width="50">Count</th>
						<th width="40">Supplier</th>
						<th width="80">Yarn Lot</th>
						<th width="30">Rack</th>
						<th width="30">Shelf</th>
						<th width="80">Store</th>
						<th>Remarks</th>
					</thead>
					<tbody>
						<?
						$color_arr = return_library_array( "select id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
						$product_array=array();
						$product_sql = sql_select("select id, item_description, supplier_id,item_category_id,product_name_details, lot,gsm,dia_width,color,brand,unit_of_measure,yarn_count_id from product_details_master where item_category_id in(1,14)");
						foreach($product_sql as $row)
						{
							$product_array[$row[csf("id")]]['product_name_details']=$row[csf("product_name_details")];
							$product_array[$row[csf("id")]]['item_description']=$row[csf("item_description")];
							$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
							$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
							$product_array[$row[csf("id")]]['brand']=$row[csf("brand")];
							$product_array[$row[csf("id")]]['color']=$row[csf("color")];
							$product_array[$row[csf("id")]]['uom']=$row[csf("unit_of_measure")];

							if($row[csf("item_category_id")]==1)
							{
								$product_array[$row[csf("lot")].'l']['lot']=$row[csf("brand")];
								// $product_array[$row[csf("lot")]]['supp']=$supplier_short_library[$row[csf("supplier_id")]];
								$product_array[$row[csf("lot")]][$row[csf("yarn_count_id")]]['supp']=$supplier_short_library[$row[csf("supplier_id")]];
							}
						}
						
						$machine_id_arr=array();
						if($dataArray[0][csf('issue_purpose')]==8)
						{
							if ($db_type==0) $mc_concat="group_concat(b.machine_dia)";
							else if ($db_type==2) $mc_concat="LISTAGG(b.machine_dia, ',') WITHIN GROUP (ORDER BY b.machine_dia)";
							$sql_prod_mc_id=sql_select("select b.prod_id, $mc_concat as machine_dia from  pro_grey_prod_entry_dtls b where  b.status_active=1 and b.is_deleted=0 and b.prod_id in($prodID) group by  b.prod_id");
						}
						else
						{
							if ($db_type==0) $mc_concat="group_concat(b.machine_no_id)";
							else if ($db_type==2) $mc_concat="LISTAGG(b.machine_no_id, ',') WITHIN GROUP (ORDER BY b.machine_no_id)";
							$sql_prod_mc_id=sql_select("select a.po_breakdown_id, b.prod_id, $mc_concat as machine_no_id,b.machine_dia from order_wise_pro_details a, pro_grey_prod_entry_dtls b where b.id=a.dtls_id and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.po_breakdown_id, b.prod_id,b.machine_dia");
						}
						foreach($sql_prod_mc_id as $row)
						{
							$machine_id_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]=$row[csf("machine_no_id")];
							$machine_dia_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]=$row[csf("machine_dia")];
							$machine_id_arr_non_order[$row[csf("prod_id")]]=$row[csf("machine_dia")];
						}
						// echo "<pre>";
						// print_r($machine_id_arr);die;

						$sql_dtls = "select b.id, b.program_no, b.prod_id, a.booking_id, b.issue_qnty, b.no_of_roll, b.yarn_lot, b.yarn_count, b.store_name, b.color_id, b.rack, b.self, b.stitch_length,b.remarks,a.issue_purpose from inv_issue_master a, inv_grey_fabric_issue_dtls b where a.id=b.mst_id and a.entry_form=577 and a.id=$data[1] and b.status_active=1 and b.is_deleted=0";
						//echo $sql_dtls;

						$sql_result= sql_select($sql_dtls);
						$sql_count=count($sql_result);
						$i=1; $all_program_no='';
						foreach($sql_result as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if($row[csf('program_no')]!='')
							{
								if ($all_program_no=='') $all_program_no=$row[csf('program_no')]; else $all_program_no.=', '.$row[csf('program_no')];
							}

							$roll_qty_sum+=$row[csf('no_of_roll')];
							$issue_qnty_sum+=$row[csf('issue_qnty')];

							/*$item_des=explode(',',$product_array[$row[csf("prod_id")]]['product_name_details']);
							//print_r ($item_des);
							if($item_des[0]!='' && $item_des[1]!='')
							{
								$item_name_details=$item_des[0].', '.$item_des[1];
							}
							else
							{
								$item_name_details='';
							}*/
							$item_name_details = $product_array[$row[csf("prod_id")]]['item_description'];
							$yarn_lot=$row[csf("yarn_lot")];
							$yarn_lot_id=explode(',',$yarn_lot);
							$yarn_lot_supp='';
							/*foreach ($yarn_lot_id as $val)
							{
								if($yarn_lot_supp=='') $yarn_lot_supp=$product_array[$val]['supp']; else $yarn_lot_supp.=", ".$product_array[$val]['supp'];
							}*/

							$yarn_count=$row[csf("yarn_count")];
							$count_id=explode(',',$yarn_count);
							$count_val='';
							foreach ($count_id as $val)
							{
								if($count_val=='') $count_val=$yarn_count_arr[$val]; else $count_val.=", ".$yarn_count_arr[$val];

								foreach ($yarn_lot_id as $lot)
								{
									// echo $lot.']['.$val.'<br>';
									if($yarn_lot_supp=='') $yarn_lot_supp=$product_array[$lot][$val]['supp']; else $yarn_lot_supp.=", ".$product_array[$lot][$val]['supp'];
								}
							}

							
							if($row[csf("issue_purpose")]==8)
							{
									$prod_id_ex=array_unique(explode(',',$prodID));
									$mc_dia="";
									foreach($prod_id_ex as $prodIDs )
									{
										$mc_id_val=array_unique(explode(',',$machine_id_arr_non_order[$row[csf("prod_id")]]));
										//$mc_dia=$machine_dia_arr[$poId][$row[csf("prod_id")]];
										foreach($mc_id_val as $mc_id)
										{
											if($row[csf("issue_purpose")]==8)
											{
												if($mc_id!=0)
												{
													if($mc_dia==""){$mc_dia=$mc_id;} else {$mc_dia.=', '.$mc_id;}//
												}
													
											}
											
										}
									}
							}
							else
							{
								$pono_id=array_unique(explode(',',$po_id));
								$mc_dia="";
								foreach($pono_id as $poId )
								{
									//$mc_id_val=array_unique(explode(',',$machine_id_arr[$poId][$row[csf("prod_id")]]));
									$mc_id_val=array_unique(explode(',',$machine_dia_arr[$poId][$row[csf("prod_id")]]));
									foreach($mc_id_val as $mc_id)
									{
										//if($mc_dia=="") $mc_dia=$mc_dia_arr[$mc_id]; else $mc_dia.=', '.$mc_dia_arr[$mc_id];//
										if($mc_dia=="") $mc_dia=$mc_id; else $mc_dia.=', '.$mc_id;//
									}
								}
							}
							
							$color_ids=array_unique(explode(",",$row[csf('color_id')]));
							$color_name="";
							foreach($color_ids as $cid)
							{
								if($color_name!="") $color_name.=", ".$color_arr[$cid];else $color_name=$color_arr[$cid];
							}
							$color_names=implode(", ",array_unique(explode(", ",$color_name)));

							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td align="center"><? echo $i; ?></td>
								<td><p><? if($row[csf("program_no")]==0) echo "&nbsp;"; else echo $row[csf("program_no")]; ?></p></td>
								<td><p><? echo $item_name_details; ?></p></td>
								<td><p><? echo $row[csf("stitch_length")]; ?></p></td>
								<td align="center"><p><? echo $product_array[$row[csf("prod_id")]]['gsm']; ?></p></td>
								<td align="center"><p><? echo $product_array[$row[csf("prod_id")]]['dia_width']; ?></p></td>
								<td align="center"><p><? echo $mc_dia; ?></p></td>
								<td style="word-break:break-all;"><p><? echo $color_names;//$color_arr[$row[csf("color_id")]]; ?></p></td>
								<td align="right"><? echo $row[csf("no_of_roll")]; ?></td>
								<td align="right"><? echo number_format($row[csf("issue_qnty")],2); ?></td>
								<td align="center"><p><? echo $unit_of_measurement[$product_array[$row[csf("prod_id")]]['uom']]; ?></p></td>
								<td align="center"><p><? echo $count_val; ?></p></td>
								<td><p><? echo $yarn_lot_supp; ?></p></td>
								<td width="80" style="word-wrap:break-word; word-break: break-all;"><p><? echo $yarn_lot; ?></p></td>
								<td align="center"><p><? echo $lib_rack_arr[$row[csf("rack")]]; ?></p></td>
								<td align="center"><p><? echo $lib_shelf_arr[$row[csf("self")]]; ?></p></td>
								<td><p><? echo $store_library[$row[csf("store_name")]]; ?></p></td>
								<td><div style="word-wrap:break-word; width:70px;"><? echo $row[csf("remarks")]; ?></div></td>
							</tr>
							<?
								//if ($sql_count>1) {$inWordTxt="Quantity";}else{$inWordTxt=$unit_of_measurement[$product_array[$row[csf("prod_id")]]['uom']];}
							$inWordTxt=$unit_of_measurement[$product_array[$row[csf("prod_id")]]['uom']];

							$i++;
						} ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="8" align="right"><strong>Total :</strong></td>
							<td align="right"><?php echo $roll_qty_sum; ?></td>
							<td align="right"><?php echo number_format($issue_qnty_sum,2); ?></td>
							<td align="right" colspan="8"><?php //echo $req_qny_edit_sum; ?></td>
						</tr>
						<tr>
							<td colspan="18"><h4 align="center">In Words : &nbsp;<? echo number_to_words($issue_qnty_sum,$inWordTxt);?></h4></td>
						</tr>
					</tfoot>
				</table>
	<br>
	<br>&nbsp;
	<!--================================================================-->
	<?
	if($dataArray[0][csf('issue_basis')]==3)
	{
			if ($data[5]!=6) //operation 6(print 2)
			{
				?>
				<table width="850" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
					<thead>
						<tr>
							<th colspan="7" align="center">Requisition Details</th>
						</tr>
						<tr>
							<th width="40">SL</th>
							<th width="100">Requisition No</th>
							<th width="110">Lot No</th>
							<th width="220">Yarn Description</th>
							<th width="110">Brand</th>
							<th width="90">Requisition Qty</th>
							<th>Remarks</th>
						</tr>
					</thead>
					<?
					$i=1; $tot_reqsn_qnty=0;
					$product_details_array=array();
					$sql_prod="select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and company_id='$data[0]' and status_active=1 and is_deleted=0";
					$result_prod = sql_select($sql_prod);

					foreach($result_prod as $row)
					{
						$compos='';
						if($row[csf('yarn_comp_percent2nd')]!=0)
						{
							$compos=$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]."%"." ".$composition[$row[csf('yarn_comp_type2nd')]]." ".$row[csf('yarn_comp_percent2nd')]."%";
						}
						else
						{
							$compos=$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]."%"." ".$composition[$row[csf('yarn_comp_type2nd')]];
						}
						$product_details_array[$row[csf('id')]]['count']=$count_arr[$row[csf('yarn_count_id')]];
						$product_details_array[$row[csf('id')]]['comp']=$compos;
						$product_details_array[$row[csf('id')]]['type']=$yarn_type[$row[csf('yarn_type')]];
						$product_details_array[$row[csf('id')]]['lot']=$row[csf('lot')];
						$product_details_array[$row[csf('id')]]['brand']=$brand_arr[$row[csf('brand')]];
					}

					$sql_knit="select knit_id, requisition_no, prod_id, sum(yarn_qnty) as yarn_qnty from ppl_yarn_requisition_entry where knit_id in ($all_program_no) and status_active=1 and is_deleted=0 group by requisition_no, prod_id, knit_id";
					$nameArray=sql_select( $sql_knit );
					foreach ($nameArray as $selectResult)
					{
						?>
						<tr>
							<td width="40" align="center"><? echo $i; ?></td>
							<td width="100" align="center">&nbsp;<? echo $selectResult[csf('requisition_no')]; ?></td>
							<td width="110" align="center">&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['lot']; ?></td>
							<td width="220">&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['count']." ".$product_details_array[$selectResult[csf('prod_id')]]['comp']." ".$product_details_array[$selectResult[csf('prod_id')]]['type']; ?></td>
							<td width="110" align="center">&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['brand']; ?></td>
							<td width="90" align="right"><? echo number_format($selectResult[csf('yarn_qnty')],2); ?>&nbsp;</td>
							<td>&nbsp;<? //echo $selectResult[csf('requisition_no')]; ?></td>
						</tr>
						<?
						$tot_reqsn_qnty+=$selectResult[csf('yarn_qnty')];
						$i++;
					}
					?>
					<tfoot>
						<th colspan="5" align="right"><b>Total</b></th>
						<th align="right"><? echo number_format($tot_reqsn_qnty,2); ?>&nbsp;</th>
						<th>&nbsp;</th>
					</tfoot>
				</table>
				<?
			}
			if($data[5]!=1)
			{
				$z=1; $k=1;
				$colorArray=sql_select("select a.id, a.mst_id, a.knitting_source, a.knitting_party, a.program_date,a. color_range, a.stitch_length, a.machine_dia, a.machine_gg, a.program_qnty, a.remarks, b.knit_id, c.booking_no, c.body_part_id, c.fabric_desc, c.gsm_weight, c.dia from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, ppl_planning_info_entry_mst c where a.mst_id= c.id and a.id=b.knit_id and b.knit_id in ($all_program_no) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0");
				$booking_no="";
				foreach ($colorArray as $book)
				{
					if ($booking_no=="") $booking_no=$book[csf('booking_no')]; else $booking_no.=','.$book[csf('booking_no')];
				}
				$booking_count=count(array_unique(explode(',',$booking_no)));

				if ($data[5]!=6) //operation 6(print 2)
				{
					?>

					<table width="710" cellpadding="0" cellspacing="0" border="1" rules="all" style="margin-top:20px;" class="rpt_table">
						<thead>
							<th width="40">SL</th>
							<th width="250">Fabrication</th>
							<th width="120">Color</th>
							<th width="120">GGSM OR S/L</th>
							<th>FGSM</th>
						</thead>
						<tbody>
							<tr>
								<td width="40" align="center"><? echo $z; ?></td>
								<td width="250" align="center"><? echo $body_part[$colorArray[0][csf('body_part_id')]].', '.$colorArray[0][csf('fabric_desc')]; ?></td>
								<td width="120" align="center"><? echo $color_range[$colorArray[0][csf('color_range')]]; ?></td>
								<td width="120" align="center"><? echo $colorArray[0][csf('stitch_length')]; ?></td>
								<td align="center"><? echo $colorArray[0][csf('gsm_weight')]; ?></td>
							</tr>
						</tbody>
					</table>
					<table style="margin-top:20px;" width="650" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
						<thead>
							<th width="40">SL</th>
							<th width="50">Prog. No</th>
							<th width="110">Finish Dia</th>
							<th width="170">Machine Dia & Gauge</th>
							<th width="110">Program Qty</th>
							<th>Remarks</th>
						</thead>
						<tbody>
							<tr>
								<td width="40" align="center"><? echo $k; ?></td>
								<td width="50" align="center">&nbsp;<? echo $colorArray[0][csf('knit_id')]; ?></td>
								<td width="110" align="center"><? echo $colorArray[0][csf('dia')]; ?></td>
								<td width="170" align="center"><? echo $colorArray[0][csf('machine_dia')]."X".$colorArray[0][csf('machine_gg')]; ?></td>
								<td width="110" align="right"><? echo number_format($colorArray[0][csf('program_qnty')],2); ?>&nbsp;</td>
								<td><? echo $colorArray[0][csf('remarks')]; ?></td>
							</tr>
							<?
							$tot_prog_qty+=$colorArray[0][csf('program_qnty')];
							?>
							<tr>
								<td colspan="4" align="right"><strong>Total : </strong></td>
								<td align="right"><? echo number_format($tot_prog_qty,2); ?></td>
								<td>&nbsp;</td>
							</tr>
						</tbody>
					</table>
					<?
				}
				?>


				<table style="margin-top:20px;" width="500" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
					<thead>
						<tr><th colspan="4" align="center">Comments (Booking No: <? if ($booking_count>1) echo "Multiple Booking."; else echo $colorArray[0][csf('booking_no')]; ?>)</th></tr>
						<tr>
							<th>Req. Qty</th>
							<th>Cuml. Issue Qty</th>
							<th>Balance Qty</th>
							<th>Remarks</th>
						</tr>
					</thead>
					<?
					if($db_type==0)
					{
						$reqs_no= " group_concat(b.requisition_no)";
					}
					else if($db_type==2)
					{
						$reqs_no="LISTAGG(b.requisition_no, ',') WITHIN GROUP (ORDER BY b.requisition_no)";
					}
					$all_booking_sql=sql_select("select $reqs_no as requisition_no from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, ppl_planning_info_entry_mst c where a.mst_id= c.id and a.id=b.knit_id and c.booking_no='".$colorArray[0][csf('booking_no')]."' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 group by c.booking_no");
					$all_requ_no=$all_booking_sql[0][csf('requisition_no')];

					if( $dataArray[0][csf('issue_purpose')]==8 )
					{
						$sql = "select a.id, sum(b.grey_fabric) as fabric_qty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no='".$colorArray[0][csf('booking_no')]."' group by a.id";
					}
					else if( $dataArray[0][csf('issue_purpose')]==2)
					{
						$sql = "select a.id, sum(b.yarn_wo_qty) as fabric_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no='".$colorArray[0][csf('booking_no')]."' group by a.id";
					}
					else
					{
						$sql = "select a.id, sum(b.grey_fab_qnty) as fabric_qty from wo_booking_mst a,  wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no='".$colorArray[0][csf('booking_no')]."' group by a.id";
					}
						//echo $sql;
					$result = sql_select($sql);

					$total_issue_qty=return_field_value("sum(a.cons_quantity) as issue_qty"," inv_transaction a, inv_issue_master b","b.id=a.mst_id and a.requisition_no in ($all_program_no) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=2 and b.item_category=14","issue_qty");
					$total_return_qty=return_field_value("sum(a.cons_quantity) as return_qty"," inv_transaction a, inv_receive_master b","b.id=a.mst_id and b.booking_id in ($all_program_no) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=4 and b.item_category=14","return_qty");
					if ($booking_count==1)
					{
						?>
						<tbody>
							<tr>
								<td align="center">
									<? echo number_format($result[0][csf('fabric_qty')],3); ?>
								</td>
								<td align="center">
									<? $cumulative_qty=$total_issue_qty-$total_return_qty; echo number_format($cumulative_qty,3); ?>
								</td>
								<td align="center">
									<? $balance_qty=$result[0][csf('fabric_qty')]-$cumulative_qty; echo number_format($balance_qty,3);?>
								</td>
								<td align="center">
									<? if ($result[0][csf('fabric_qty')]>$cumulative_qty) echo "Less"; else if ($result[0][csf('fabric_qty')]<$cumulative_qty) echo "Over"; else echo "";?>
								</td>
							</tr>
						</tbody>
					</table>
					<?
				}
			}
		}
		else if($dataArray[0][csf('issue_basis')]==1)
		{
			if($data[5]==4 && $data[6]==0)
			{
			}
			else
			{
				if($dataArray[0][csf('issue_purpose')]==8 )
				{
					$sql = "select a.id, a.booking_no, sum(b.grey_fabric) as fabric_qty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='".$dataArray[0][csf('booking_id')]."' group by a.id, a.booking_no";
				}
				else if( $dataArray[0][csf('issue_purpose')]==2)
				{
					$sql = "select a.id, a.ydw_no as booking_no, sum(b.yarn_wo_qty) as fabric_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='".$dataArray[0][csf('booking_id')]."' group by a.id, a.ydw_no";
				}
				else
				{
					$sql = "select a.id, a.booking_no, sum(b.grey_fab_qnty) as fabric_qty from wo_booking_mst a,  wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='".$dataArray[0][csf('booking_id')]."' group by a.id, a.booking_no";
				}
				$result = sql_select($sql);

				$total_issue_qty=return_field_value("sum(a.cons_quantity) as total_issue_qty"," inv_transaction a, inv_issue_master b","b.id=a.mst_id and b.booking_id='".$dataArray[0][csf('booking_id')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=2 and  b.issue_purpose='".$dataArray[0][csf('issue_purpose')]."' and b.item_category=14","total_issue_qty");
				$total_return_qty=return_field_value("sum(a.cons_quantity) as total_issue_qty"," inv_transaction a, inv_receive_master b","b.id=a.mst_id and b.booking_id='".$dataArray[0][csf('booking_id')]."' and b.receive_purpose='".$dataArray[0][csf('issue_purpose')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=4 and b.item_category=14","total_issue_qty");
				?>
				<table style="margin-top:20px;" width="500" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
					<thead>
						<tr><th colspan="4" align="center">Comments  (Booking : <? echo $result[0][csf('booking_no')]; ?>)</th></tr>
						<tr>
							<th>Req. Qty</th>
							<th>Cuml. Issue Qty</th>
							<th>Balance Qty</th>
							<th>Remarks</th>
						</tr>
					</thead>

					<tbody>
						<tr>
							<td align="center">
								<? echo number_format($result[0][csf('fabric_qty')],3); ?>
							</td>
							<td align="center">
								<? $cumulative_qty=$total_issue_qty-$total_return_qty; echo number_format($cumulative_qty,3); ?>
							</td>
							<td align="center">
								<? $balance_qty=$result[0][csf('fabric_qty')]-$cumulative_qty; echo number_format($balance_qty,3);?>
							</td>
							<td align="center">
								<? if($result[0][csf('fabric_qty')]>$cumulative_qty) echo "Less"; else if ($result[0][csf('fabric_qty')]<$cumulative_qty) echo "Over"; else echo ""; ?>
							</td>
						</tr>
					</tbody>
				</table>
				<?
			}
		}
		echo signature_table(17, $data[0], "900px");
		?>
	</div>
	</div>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>

		function generateBarcode( valuess )
		{
				var value = valuess;//$("#barcodeValue").val();
			// alert(value)
				var btype = 'code39';//$("input[name=btype]:checked").val();
				var renderer ='bmp';// $("input[name=renderer]:checked").val();

				var settings = {
					output:renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 1,
					barHeight: 30,
					moduleSize:5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};
				$("#barcode_img_id").html('11');
				value = {code:value, rect: false};

				$("#barcode_img_id").show().barcode(value, btype, settings);
			}
			generateBarcode('<? echo $data[2]; ?>');
		</script>
		<?
		exit();
}
if ($action=="grey_fabric_issue_print_7")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$print_with_vat=$data[4];
	//print_r ($data);

	$sql="select id, issue_number, issue_number_prefix_num, issue_date, issue_basis, issue_purpose, knit_dye_source, knit_dye_company, booking_id, batch_no, buyer_id, challan_no, style_ref, order_id from  inv_issue_master where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;
	$supplier_library=array(); $supplier_short_library=array();
	$supplier_data=sql_select( "select id,supplier_name,short_name from lib_supplier");
	foreach ($supplier_data as $row)
	{
		$supplier_library[$row[csf('id')]]=$row[csf('supplier_name')];
		$supplier_short_library[$row[csf('id')]]=$row[csf('short_name')];
	}

	$dataArray=sql_select($sql);
	$grey_issue_basis=array(1=>"Booking",2=>"Independent",3=>"Knitting Plan");
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$batch_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no"  );
	$booking_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no"  );
	$booking_non_order_arr=return_library_array( "select id, booking_no from  wo_non_ord_samp_booking_mst", "id", "booking_no"  );
	$po_arr=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number"  );
	$internal_ref_arr=return_library_array( "select id, grouping from  wo_po_break_down", "id", "grouping"  );
	$job_arr=return_library_array( "select id, job_no_mst from  wo_po_break_down","id","job_no_mst");
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand",'id','brand_name');
	$mc_dia_arr = return_library_array("select id, dia_width from lib_machine_name",'id','dia_width');


	$lib_room_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "room_id","floor_room_rack_name" );
	$lib_rack_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "rack_id","floor_room_rack_name" );
	$lib_shelf_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "shelf_id","floor_room_rack_name" );
	$lib_bin_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "bin_id","floor_room_rack_name" );

	?>
	<div style="width:930px;">
		<table width="900" cellspacing="0" align="right" style="margin-bottom:10px">
			<tr>
				<td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			</tr>
			<tr class="form_caption">

				<?
				$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
				?>
				<td  align="left">
					<?
					foreach($data_array as $img_row)
					{
						?>
						<img src='../../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50' align="middle" />
						<?
					}
					?>
				</td>

				<td colspan="4" align="center" style="font-size:14px">
					<?

					echo show_company($data[0],'','');//Aziz
					/*$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
					?>
						 <? echo $result[csf('plot_no')]; ?>
						 <? echo $result[csf('level_no')];?>
						 <? echo $result[csf('road_no')]; ?>
						 <? echo $result[csf('block_no')];?>
						 <? echo $result[csf('city')];?>
						 <? echo $result[csf('zip_code')]; ?>
						 <?php echo $result[csf('province')];?>
						 <? echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email Address: <? echo $result[csf('email')];?>
						Website No: <? echo $result[csf('website')];
					}*/
					?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><u>Knit Grey Fabric Delivery Challan</u></strong></td>
			</tr>
			<tr>
				<td rowspan="3" colspan="2" width="300" valign="top"><strong>Dyeing Company:</strong>
					<?
					$supp_add=$dataArray[0][csf('knit_dye_company')];
					$nameArray=sql_select( "select address_1,web_site,email,country_id from lib_supplier where id=$supp_add");
					foreach ($nameArray as $result)
					{
						$address="";
						if($result!="") $address=$result['address_1'];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
					}
					if ($dataArray[0][csf('knit_dye_source')]==1) echo $company_library[$dataArray[0][csf('knit_dye_company')]].'<br>'.'Address :- '.$address; else if ($dataArray[0][csf('knit_dye_source')]==3) echo $supplier_library[$dataArray[0][csf('knit_dye_company')]].'<br>'.'Address :- '.$address; ?></td>
					<td width="120"><strong>Issue ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
					<td width="125"><strong>Issue Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
				</tr>
				<tr>
					<td><strong>Issue Basis :</strong></td> <td width="175px"><? echo $grey_issue_basis[$dataArray[0][csf('issue_basis')]]; ?></td>
					<td><strong>Issue Purpose:</strong></td><td width="175px"><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Dyeing Source:</strong></td><td width="175px"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
					<td><strong>F. Booking No:</strong></td><td width="175px">
						<?
						if($dataArray[0][csf('issue_basis')]==1)
						{
							if(($dataArray[0][csf('issue_purpose')]==8 || $dataArray[0][csf('issue_purpose')]==3 || $dataArray[0][csf('issue_purpose')]==26 || $dataArray[0][csf('issue_purpose')]==29 || $dataArray[0][csf('issue_purpose')]==30 || $dataArray[0][csf('issue_purpose')]==31) && $dataArray[0][csf('issue_basis')]==1)
							{
								echo $booking_non_order_arr[$dataArray[0][csf('booking_id')]];
							}
							else
							{
								echo $booking_arr[$dataArray[0][csf('booking_id')]];
							}
						}
						else
						{
							echo $dataArray[0][csf('booking_id')];
						}
						?>
					</td>
				</tr>
				<tr>
					<td><strong>Challan No:</strong></td><td width="175px"><? if($dataArray[0][csf('challan_no')]=="") echo $dataArray[0][csf('issue_number_prefix_num')]; else echo $dataArray[0][csf('challan_no')]; ?></td>
					<td><strong>Batch Number:</strong></td><td width="175px"><? echo $batch_arr[$dataArray[0][csf('batch_no')]]; ?></td>
					<td><strong>Buyer Name:</strong></td><td width="175px"><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td>
				</tr>
				<tr>
					<td>
						<?
						if($db_type==0)
						{
							$po_id=return_field_value("group_concat(b.po_breakdown_id) as po_id","inv_grey_fabric_issue_dtls a, order_wise_pro_details b","a.id=b.dtls_id and a.mst_id='$data[1]' and b.entry_form=577 and b.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","po_id");
						}
						else
						{
							$po_id=return_field_value("LISTAGG(b.po_breakdown_id, ',') WITHIN GROUP (ORDER BY b.po_breakdown_id) as po_id","inv_grey_fabric_issue_dtls a, order_wise_pro_details b","a.id=b.dtls_id and a.mst_id='$data[1]' and b.entry_form=577 and b.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","po_id");
						}

						$po_exp=array_unique(explode(',',$po_id));
						$internal_ref=''; $job='';
						foreach($po_exp as $row)
						{
							if($internal_ref=='') $internal_ref=$internal_ref_arr[$row]; else $internal_ref.=', '.$internal_ref_arr[$row];
							if($job=='') $job=$job_arr[$row]; else $job.=','.$job_arr[$row];
						}
						$internal_ref=implode(", ",array_unique(explode(', ',$internal_ref)));
						$job=implode(",",array_unique(explode(',',$job)));
						?>
						<strong>Job No:</strong></td>
						<td width="175px" colspan="3"><? echo $job;//$job_arr[$dataArray[0][csf('order_id')]]; ?></td>
						<td><strong>Style Ref.:</strong></td><td width="175px"><? echo $dataArray[0][csf('style_ref')]; ?></td>
					</tr>
					<tr>
						<td><strong>Internal Ref No:</strong></td><td colspan="5"><? echo $internal_ref;//$po_arr[$dataArray[0][csf('order_id')]]; ?></td>
					</tr>
					<?
					if($print_with_vat==1)
					{
						?>
						<tr>
							<td><strong>VAT Number :</strong></td>
							<td><p>
								<?
								$vat_no=return_field_value("vat_number","lib_company","id=".$data[0],"vat_number");
								echo $vat_no;
								?></p></td>
								<td><strong>Bar Code:</strong></td><td colspan="3" id="barcode_img_id"></td>
							</tr>
							<?
						}
						else
						{
							?>
							<tr>
								<td valign="top">&nbsp;</strong></td>
								<td>&nbsp;</td>
								<td><strong>Bar Code:</strong></td><td colspan="3" id="barcode_img_id"></td>
							</tr>
							<?
						}
						?>
					</table>
					<div style="width:100%;">
						<table align="left" cellspacing="0" width="1130"  border="1" rules="all" class="rpt_table" >
							<thead bgcolor="#dddddd" align="center">
								<th width="20">SL</th>
								<th width="100">Order No</th>
								<th width="50">Prog. No</th>
								<th width="130">Item Des.</th>
								<th width="50">Stich Length</th>
								<th width="40">GSM</th>
								<th width="40">Fin. Dia</th>
								<th width="80">M/C Dia</th>
								<th width="70">Color</th>
								<th width="40">Roll</th>
								<th width="70">Issue Qty</th>
								<th width="40">UOM</th>
								<th width="50">Count</th>
								<th width="40">Supplier</th>
								<th width="80">Yarn Lot</th>
								<th width="30">Rack</th>
								<th width="30">Shelf</th>
								<th width="80">Store</th>
								<th>Remarks</th>
							</thead>
							<tbody>
								<?
								$color_arr = return_library_array( "select id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
								$product_array=array();
								$product_sql = sql_select("select id, item_description, supplier_id,item_category_id,product_name_details, lot,gsm,dia_width,color,brand,unit_of_measure from product_details_master where item_category_id in(1,14)");
								foreach($product_sql as $row)
								{
									$product_array[$row[csf("id")]]['product_name_details']=$row[csf("product_name_details")];
									$product_array[$row[csf("id")]]['item_description']=$row[csf("item_description")];
									$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
									$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
									$product_array[$row[csf("id")]]['brand']=$row[csf("brand")];
									$product_array[$row[csf("id")]]['color']=$row[csf("color")];
									$product_array[$row[csf("id")]]['uom']=$row[csf("unit_of_measure")];

									if($row[csf("item_category_id")]==1)
									{
										$product_array[$row[csf("lot")].'l']['lot']=$row[csf("brand")];
										$product_array[$row[csf("lot")]]['supp']=$supplier_short_library[$row[csf("supplier_id")]];
									}
								}
								if ($db_type==0) $mc_concat="group_concat(b.machine_no_id)";
								else if ($db_type==2) $mc_concat="LISTAGG(b.machine_no_id, ',') WITHIN GROUP (ORDER BY b.machine_no_id)";
								$machine_id_arr=array();
								$sql_prod_mc_id=sql_select("select a.po_breakdown_id, b.prod_id, $mc_concat as machine_no_id from order_wise_pro_details a, pro_grey_prod_entry_dtls b where b.id=a.dtls_id and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.po_breakdown_id, b.prod_id");
								foreach($sql_prod_mc_id as $row)
								{
									$machine_id_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]=$row[csf("machine_no_id")];
								}
								// echo "<pre>";
								// print_r($machine_id_arr);die;

								//$sql_dtls = "select b.id, b.program_no, b.prod_id, a.booking_id, b.issue_qnty, b.no_of_roll, b.yarn_lot, b.yarn_count, b.store_name, b.color_id, b.rack, b.self, b.stitch_length,b.remarks from inv_issue_master a, inv_grey_fabric_issue_dtls b where a.id=b.mst_id and a.entry_form=577 and a.id=$data[1] and b.status_active=1 and b.is_deleted=0";
								$sql_dtls = "select b.id, b.program_no, b.prod_id, a.booking_id, b.no_of_roll, b.yarn_lot, b.yarn_count, b.store_name, b.color_id, b.rack, b.self, b.stitch_length,b.remarks, c.po_breakdown_id as order_id ,c.quantity as issue_qnty,d.product_name_details
								from inv_issue_master a, inv_grey_fabric_issue_dtls  b,order_wise_pro_details c,product_details_master d
								where a.id=b.mst_id and b.prod_id=c.prod_id and b.trans_id=c.trans_id and b.id=c.dtls_id and d.id=c.prod_id and d.id=b.prod_id and a.id=$data[1] and a.company_id=$data[0]";
								//echo $sql_dtls;

								$sql_result= sql_select($sql_dtls);
								$sql_count=count($sql_result);
								$i=1; $all_program_no='';
								foreach($sql_result as $row)
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									if($row[csf('program_no')]!='')
									{
										if ($all_program_no=='') $all_program_no=$row[csf('program_no')]; else $all_program_no.=', '.$row[csf('program_no')];
									}

									$roll_qty_sum+=$row[csf('no_of_roll')];
									$issue_qnty_sum+=$row[csf('issue_qnty')];

			/*$item_des=explode(',',$product_array[$row[csf("prod_id")]]['product_name_details']);
			//print_r ($item_des);
			if($item_des[0]!='' && $item_des[1]!='')
			{
				$item_name_details=$item_des[0].', '.$item_des[1];
			}
			else
			{
				$item_name_details='';
			}*/
			//$item_name_details = $product_array[$row[csf("prod_id")]]['item_description'];

			$yarn_count=$row[csf("yarn_count")];
			$count_id=explode(',',$yarn_count);
			$count_val='';
			foreach ($count_id as $val)
			{
				if($count_val=='') $count_val=$yarn_count_arr[$val]; else $count_val.=", ".$yarn_count_arr[$val];
			}

			$yarn_lot=$row[csf("yarn_lot")];
			$yarn_lot_id=explode(',',$yarn_lot);
			$yarn_lot_supp='';
			foreach ($yarn_lot_id as $val)
			{
				if($yarn_lot_supp=='') $yarn_lot_supp=$product_array[$val]['supp']; else $yarn_lot_supp.=", ".$product_array[$val]['supp'];
			}

			$pono_id=array_unique(explode(',',$po_id));
			$mc_dia="";
			foreach($pono_id as $poId )
			{
				$mc_id_val=array_unique(explode(',',$machine_id_arr[$poId][$row[csf("prod_id")]]));
				foreach($mc_id_val as $mc_id)
				{
					if($mc_dia=="") $mc_dia=trim($mc_dia_arr[$mc_id]); else $mc_dia.=', '.trim($mc_dia_arr[$mc_id]);//
				}
			}
			$mc_dia=implode(", ",array_unique(explode(', ',$mc_dia)));
			$color_ids=array_unique(explode(",",$row[csf('color_id')]));
			$color_name="";
			foreach($color_ids as $cid)
			{
				if($color_name!="") $color_name.=", ".$color_arr[$cid];else $color_name=$color_arr[$cid];
			}
			$color_names=implode(", ",array_unique(explode(", ",$color_name)));

			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td align="center"><? echo $i; ?></td>
				<td><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
				<td><p><? if($row[csf("program_no")]==0) echo "&nbsp;"; else echo $row[csf("program_no")]; ?></p></td>
				<td><p><? echo $row[csf("product_name_details")]; ?></p></td>
				<td><p><? echo $row[csf("stitch_length")]; ?></p></td>
				<td align="center"><p><? echo $product_array[$row[csf("prod_id")]]['gsm']; ?></p></td>
				<td align="center"><p><? echo $product_array[$row[csf("prod_id")]]['dia_width']; ?></p></td>
				<td align="center" style="word-break:break-all;"><p><? echo $mc_dia; ?></p></td>
				<td style="word-break:break-all;"><p><? echo $color_names;//$color_arr[$row[csf("color_id")]]; ?></p></td>
				<td align="right"><? echo $row[csf("no_of_roll")]; ?></td>
				<td align="right"><? echo number_format($row[csf("issue_qnty")],2); ?></td>
				<td align="center"><p><? echo $unit_of_measurement[$product_array[$row[csf("prod_id")]]['uom']]; ?></p></td>
				<td align="center"><p><? echo $count_val; ?></p></td>
				<td><p><? echo $yarn_lot_supp; ?></p></td>
				<td width="80" style="word-wrap:break-word; word-break: break-all;"><p><? echo $yarn_lot; ?></p></td>
				<td align="center"><p><? echo $lib_rack_arr[$row[csf("rack")]]; ?></p></td>
				<td align="center"><p><? echo $lib_shelf_arr[$row[csf("self")]]; ?></p></td>
				<td><p><? echo $store_library[$row[csf("store_name")]]; ?></p></td>
				<td><div style="word-wrap:break-word; width:70px;"><? echo $row[csf("remarks")]; ?></div></td>
			</tr>
			<?
				//if ($sql_count>1) {$inWordTxt="Quantity";}else{$inWordTxt=$unit_of_measurement[$product_array[$row[csf("prod_id")]]['uom']];}
			$inWordTxt=$unit_of_measurement[$product_array[$row[csf("prod_id")]]['uom']];

			$i++;
		} ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="9" align="right"><strong>Total :</strong></td>
			<td align="right"><?php echo $roll_qty_sum; ?></td>
			<td align="right"><?php echo number_format($issue_qnty_sum,2); ?></td>
			<td align="right" colspan="8"><?php //echo $req_qny_edit_sum; ?></td>
		</tr>
		<tr>
			<td colspan="19"><h4 align="center">In Words : &nbsp;<? echo number_to_words($issue_qnty_sum,$inWordTxt);?></h4></td>
		</tr>
	</tfoot>
	</table>
	<br>
	<br>&nbsp;
	<!--================================================================-->
	<?
	if($dataArray[0][csf('issue_basis')]==3)
	{

	if($data[5]!=1)
	{
		$z=1; $k=1;
		$colorArray=sql_select("select a.id, a.mst_id, a.knitting_source, a.knitting_party, a.program_date,a. color_range, a.stitch_length, a.machine_dia, a.machine_gg, a.program_qnty, a.remarks, b.knit_id, c.booking_no, c.body_part_id, c.fabric_desc, c.gsm_weight, c.dia from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, ppl_planning_info_entry_mst c where a.mst_id= c.id and a.id=b.knit_id and b.knit_id in ($all_program_no) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0");
		$booking_no="";
		foreach ($colorArray as $book)
		{
			if ($booking_no=="") $booking_no=$book[csf('booking_no')]; else $booking_no.=','.$book[csf('booking_no')];
		}
		$booking_count=count(array_unique(explode(',',$booking_no)));

		?>


		<table style="margin-top:20px;" width="500" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
			<thead>
				<tr><th colspan="4" align="center">Comments (Booking No: <? if ($booking_count>1) echo "Multiple Booking."; else echo $colorArray[0][csf('booking_no')]; ?>)</th></tr>
				<tr>
					<th>Req. Qty</th>
					<th>Cuml. Issue Qty</th>
					<th>Balance Qty</th>
					<th>Remarks</th>
				</tr>
			</thead>
			<?
			if($db_type==0)
			{
				$reqs_no= " group_concat(b.requisition_no)";
			}
			else if($db_type==2)
			{
				$reqs_no="LISTAGG(b.requisition_no, ',') WITHIN GROUP (ORDER BY b.requisition_no)";
			}
			$all_booking_sql=sql_select("select $reqs_no as requisition_no from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, ppl_planning_info_entry_mst c where a.mst_id= c.id and a.id=b.knit_id and c.booking_no='".$colorArray[0][csf('booking_no')]."' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 group by c.booking_no");
			$all_requ_no=$all_booking_sql[0][csf('requisition_no')];

			if( $dataArray[0][csf('issue_purpose')]==8 )
			{
				$sql = "select a.id, sum(b.grey_fabric) as fabric_qty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no='".$colorArray[0][csf('booking_no')]."' group by a.id";
			}
			else if( $dataArray[0][csf('issue_purpose')]==2)
			{
				$sql = "select a.id, sum(b.yarn_wo_qty) as fabric_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no='".$colorArray[0][csf('booking_no')]."' group by a.id";
			}
			else
			{
				$sql = "select a.id, sum(b.grey_fab_qnty) as fabric_qty from wo_booking_mst a,  wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no='".$colorArray[0][csf('booking_no')]."' group by a.id";
			}
                    //echo $sql;
			$result = sql_select($sql);

			$total_issue_qty=return_field_value("sum(a.cons_quantity) as issue_qty"," inv_transaction a, inv_issue_master b","b.id=a.mst_id and a.requisition_no in ($all_program_no) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=2 and b.item_category=14","issue_qty");
			$total_return_qty=return_field_value("sum(a.cons_quantity) as return_qty"," inv_transaction a, inv_receive_master b","b.id=a.mst_id and b.booking_id in ($all_program_no) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=4 and b.item_category=14","return_qty");
			if ($booking_count==1)
			{
				?>
				<tbody>
					<tr>
						<td align="center">
							<? echo number_format($result[0][csf('fabric_qty')],3); ?>
						</td>
						<td align="center">
							<? $cumulative_qty=$total_issue_qty-$total_return_qty; echo number_format($cumulative_qty,3); ?>
						</td>
						<td align="center">
							<? $balance_qty=$result[0][csf('fabric_qty')]-$cumulative_qty; echo number_format($balance_qty,3);?>
						</td>
						<td align="center">
							<? if ($result[0][csf('fabric_qty')]>$cumulative_qty) echo "Less"; else if ($result[0][csf('fabric_qty')]<$cumulative_qty) echo "Over"; else echo "";?>
						</td>
					</tr>
				</tbody>
			</table>
			<?
		}
	}
	}
	else if($dataArray[0][csf('issue_basis')]==1)
	{
	if($data[5]==4 && $data[6]==0)
	{
	}
	else
	{
		if($dataArray[0][csf('issue_purpose')]==8 )
		{
			$sql = "select a.id, a.booking_no, sum(b.grey_fabric) as fabric_qty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='".$dataArray[0][csf('booking_id')]."' group by a.id, a.booking_no";
		}
		else if( $dataArray[0][csf('issue_purpose')]==2)
		{
			$sql = "select a.id, a.ydw_no as booking_no, sum(b.yarn_wo_qty) as fabric_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='".$dataArray[0][csf('booking_id')]."' group by a.id, a.ydw_no";
		}
		else
		{
			$sql = "select a.id, a.booking_no, sum(b.grey_fab_qnty) as fabric_qty from wo_booking_mst a,  wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='".$dataArray[0][csf('booking_id')]."' group by a.id, a.booking_no";
		}
		$result = sql_select($sql);

		$total_issue_qty=return_field_value("sum(a.cons_quantity) as total_issue_qty"," inv_transaction a, inv_issue_master b","b.id=a.mst_id and b.booking_id='".$dataArray[0][csf('booking_id')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=2 and  b.issue_purpose='".$dataArray[0][csf('issue_purpose')]."' and b.item_category=14","total_issue_qty");
		$total_return_qty=return_field_value("sum(a.cons_quantity) as total_issue_qty"," inv_transaction a, inv_receive_master b","b.id=a.mst_id and b.booking_id='".$dataArray[0][csf('booking_id')]."' and b.receive_purpose='".$dataArray[0][csf('issue_purpose')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=4 and b.item_category=14","total_issue_qty");
		?>
		<table style="margin-top:20px;" width="500" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
			<thead>
				<tr><th colspan="4" align="center">Comments  (Booking : <? echo $result[0][csf('booking_no')]; ?>)</th></tr>
				<tr>
					<th>Req. Qty</th>
					<th>Cuml. Issue Qty</th>
					<th>Balance Qty</th>
					<th>Remarks</th>
				</tr>
			</thead>

			<tbody>
				<tr>
					<td align="center">
						<? echo number_format($result[0][csf('fabric_qty')],3); ?>
					</td>
					<td align="center">
						<? $cumulative_qty=$total_issue_qty-$total_return_qty; echo number_format($cumulative_qty,3); ?>
					</td>
					<td align="center">
						<? $balance_qty=$result[0][csf('fabric_qty')]-$cumulative_qty; echo number_format($balance_qty,3);?>
					</td>
					<td align="center">
						<? if($result[0][csf('fabric_qty')]>$cumulative_qty) echo "Less"; else if ($result[0][csf('fabric_qty')]<$cumulative_qty) echo "Over"; else echo ""; ?>
					</td>
				</tr>
			</tbody>
		</table>
		<?
	}
	}
	echo signature_table(17, $data[0], "900px");
	?>
	</div>
	</div>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>

	function generateBarcode( valuess )
	{
			var value = valuess;//$("#barcodeValue").val();
		 // alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();

			var settings = {
				output:renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize:5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			$("#barcode_img_id").html('11');
			value = {code:value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $data[2]; ?>');
	</script>
	<?
	exit();
}
if ($action=="grey_fabric_issue_print_9")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$print_with_vat=$data[4];
	//print_r ($data);

	$sql="select id, issue_number, issue_number_prefix_num, issue_date, issue_basis, issue_purpose,service_booking_no, knit_dye_source, knit_dye_company, booking_id, batch_no, buyer_id, challan_no, style_ref, order_id from  inv_issue_master where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;
	$supplier_library=array(); $supplier_short_library=array();
	$supplier_data=sql_select( "select id,supplier_name,short_name from lib_supplier");
	foreach ($supplier_data as $row)
	{
		$supplier_library[$row[csf('id')]]=$row[csf('supplier_name')];
		$supplier_short_library[$row[csf('id')]]=$row[csf('short_name')];
	}

	$dataArray=sql_select($sql);
	$grey_issue_basis=array(1=>"Booking",2=>"Independent",3=>"Knitting Plan");
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$batch_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no"  );
	$booking_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no"  );
	$booking_non_order_arr=return_library_array( "select id, booking_no from  wo_non_ord_samp_booking_mst", "id", "booking_no"  );
	//$po_arr=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number"  );
	//$internal_ref_arr=return_library_array( "select id, grouping from  wo_po_break_down", "id", "grouping"  );
	//$job_arr=return_library_array( "select id, job_no_mst from  wo_po_break_down","id","job_no_mst");
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_array = return_library_array("select id, color_name from lib_color",'id','color_name');
	$size_array = return_library_array("select id, size_name from lib_size",'id','size_name');
	$brand_arr = return_library_array("select id, brand_name from lib_brand",'id','brand_name');
	$mc_dia_arr = return_library_array("select id, dia_width from lib_machine_name",'id','dia_width');
	
	  $s_wo_no=$dataArray[0][csf('service_booking_no')];
	  $order_id=$dataArray[0][csf('order_id')];
	  $sql_data_po=sql_select("select b.id as po_id,b.po_number,b.job_no_mst,b.grouping from wo_booking_dtls a,wo_po_break_down b where  b.id=a.po_break_down_id and b.status_active=1  and a.status_active=1 and b.id in($order_id) and a.status_active=1 group by b.id,b.po_number,b.job_no_mst,b.grouping");
	 // echo "select b.id as po_id,b.po_number,b.job_no_mst,b.grouping from wo_booking_dtls a,wo_po_break_down b where  b.id=a.po_break_down_id and b.status_active=1  and a.status_active=1 and b.id in($order_id) and a.status_active=1 group by b.id,b.po_number,b.job_no_mst,b.grouping";
	 foreach($sql_data_po as $row)
	 {
		 $po_arr[$row[csf("po_id")]]=$row[csf("po_number")];
		 $internal_ref_arr[$row[csf("po_id")]]=$row[csf("grouping")];
		 $job_arr[$row[csf("po_id")]]=$row[csf("job_no_mst")];
	 }
	 unset($sql_data_po);

	$lib_room_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "room_id","floor_room_rack_name" );
	$lib_rack_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "rack_id","floor_room_rack_name" );
	$lib_shelf_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "shelf_id","floor_room_rack_name" );
	$lib_bin_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "bin_id","floor_room_rack_name" );

	?>
	<div style="width:930px;">
		<table width="900" cellspacing="0" align="right" style="margin-bottom:10px">
			<tr>
				<td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
                
			</tr>
			<tr class="form_caption">

				<?
				$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
				?>
				<td  align="left">
					<?
					foreach($data_array as $img_row)
					{
						?>
						<img src='../../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50' align="middle" />
						<?
					}
					?>
				</td>

				<td colspan="4" align="center" style="font-size:14px">
					<?

					echo show_company($data[0],'','');//Aziz
					/*$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
					?>
						 <? echo $result[csf('plot_no')]; ?>
						 <? echo $result[csf('level_no')];?>
						 <? echo $result[csf('road_no')]; ?>
						 <? echo $result[csf('block_no')];?>
						 <? echo $result[csf('city')];?>
						 <? echo $result[csf('zip_code')]; ?>
						 <?php echo $result[csf('province')];?>
						 <? echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email Address: <? echo $result[csf('email')];?>
						Website No: <? echo $result[csf('website')];
					}*/
					 $sql_data_conv="select a.po_break_down_id as po_id,c.id as conv_dtl_id,a.booking_no,
					  b.lib_yarn_count_deter_id as deter_id,c.fabric_description as fab_dtls_id,b.body_part_id,b.fabric_description as fab_desc,b.gsm_weight,b.body_part_type from wo_booking_dtls a,wo_pre_cost_fabric_cost_dtls b,wo_pre_cost_fab_conv_cost_dtls c where a.pre_cost_fabric_cost_dtls_id=c.id and c.job_id=b.job_id and b.id=c.fabric_description and b.status_active=1  and c.status_active=1 and  a.po_break_down_id in($order_id) and a.booking_type=3 and a.wo_qnty>0 group by a.po_break_down_id,c.id ,b.body_part_id,b.fabric_description,b.gsm_weight,c.fabric_description,a.booking_no, b.lib_yarn_count_deter_id,b.body_part_type";
					$result_data_conv=sql_select($sql_data_conv);
					 $cuff_flat_deter=""; $other_type_deter="";
					 foreach($result_data_conv as $row)
					 {
						 $fab_desc_arr[$row[csf("po_id")]][$row[csf("fab_dtls_id")]]['fab_desc']=$body_part[$row[csf("body_part_id")]].','.$row[csf("fab_desc")]; 
						 $determin_arr[$row[csf("po_id")]][$row[csf("fab_dtls_id")]]['deter_id']=$row[csf("deter_id")]; 
						 $body_part_type_arr[$row[csf("po_id")]][$row[csf("fab_dtls_id")]]=$row[csf("body_part_type")]; 
						 if($row[csf("body_part_type")]==40 || $row[csf("body_part_type")]==50 )
						 {
							 $cuff_flat_deter.=$determin_arr[$row[csf("po_id")]][$row[csf("fab_dtls_id")]]['deter_id'].',';
						 }
						 else
						 {
							$other_type_deter.=$determin_arr[$row[csf("po_id")]][$row[csf("fab_dtls_id")]]['deter_id'].','; 
						 }
						 $sb_booking_no=$row[csf("booking_no")];
					 }
					 $other_type_deter=rtrim($other_type_deter,',');
					 $cuff_flat_deter=rtrim($cuff_flat_deter,',');
					?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><u>Knit Grey Fabric Delivery Challan </u></strong></td>
			</tr>
			<tr>
               <td width="140"><strong>Dyeing Company:</strong></td>
               <td width="175">
					<?
					$supp_add=$dataArray[0][csf('knit_dye_company')];
					$nameArray=sql_select( "select address_1,web_site,email,country_id from lib_supplier where id=$supp_add");
					foreach ($nameArray as $result)
					{
						$address="";
						if($result!="") $address=$result['address_1'];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
					}
					if ($dataArray[0][csf('knit_dye_source')]==1) echo $company_library[$dataArray[0][csf('knit_dye_company')]]; else if ($dataArray[0][csf('knit_dye_source')]==3) 	
					echo $supplier_library[$dataArray[0][csf('knit_dye_company')]];
					
					 ?></td>
					<td width="120"><strong>Issue ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
					<td width="125"><strong>Issue Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
				</tr>
                <td><strong>Buyer :</strong></td> <td width="175px"><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td>
					<td><strong>Issue Basis :</strong></td> <td width="175px"><? echo $grey_issue_basis[$dataArray[0][csf('issue_basis')]]; ?></td>
					<td><strong>Issue Purpose:</strong></td><td width="175px"><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
				</tr>
                
				<tr>
					
                    <td><strong>Address :</strong></td> <td width="175px"><? if ($dataArray[0][csf('knit_dye_source')]==1) echo $address; else if ($dataArray[0][csf('knit_dye_source')]==3) echo $address; ?></td>
                   <td><strong>Dyeing Source:</strong></td><td width="175px"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
                    <td><strong>F. Booking No:</strong></td><td width="175px">
						<?
						if($dataArray[0][csf('issue_basis')]==1)
						{
							if(($dataArray[0][csf('issue_purpose')]==8 || $dataArray[0][csf('issue_purpose')]==3 || $dataArray[0][csf('issue_purpose')]==26 || $dataArray[0][csf('issue_purpose')]==29 || $dataArray[0][csf('issue_purpose')]==30 || $dataArray[0][csf('issue_purpose')]==31) && $dataArray[0][csf('issue_basis')]==1)
							{
								echo $booking_non_order_arr[$dataArray[0][csf('booking_id')]];
							}
							else
							{
								echo $booking_arr[$dataArray[0][csf('booking_id')]];
							}
						}
						else
						{
							echo $dataArray[0][csf('booking_id')];
						}
						?>
					</td>
				</tr>
				
                <tr>
					<td>
						<?
						if($db_type==0)
						{
							$po_id=return_field_value("group_concat(b.po_breakdown_id) as po_id","inv_grey_fabric_issue_dtls a, order_wise_pro_details b","a.id=b.dtls_id and a.mst_id='$data[1]' and b.entry_form=577 and b.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","po_id");
						}
						else
						{
							$po_id=return_field_value("LISTAGG(b.po_breakdown_id, ',') WITHIN GROUP (ORDER BY b.po_breakdown_id) as po_id","inv_grey_fabric_issue_dtls a, order_wise_pro_details b","a.id=b.dtls_id and a.mst_id='$data[1]' and b.entry_form=577 and b.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","po_id");
						}

						$po_exp=array_unique(explode(',',$po_id));
						$internal_ref=''; $job='';$po_no='';
						foreach($po_exp as $row)
						{
							if($internal_ref=='') $internal_ref=$internal_ref_arr[$row]; else $internal_ref.=', '.$internal_ref_arr[$row];
							if($job=='') $job=$job_arr[$row]; else $job.=','.$job_arr[$row];
							if($po_no=='') $po_no=$po_arr[$row]; else $po_no.=','.$po_arr[$row];
						}
						$internal_ref=implode(", ",array_unique(explode(', ',$internal_ref)));
						$job=implode(",",array_unique(explode(',',$job)));
						?>
						<strong>Job No:</strong></td>
						<td width="175px" colspan="3"><? echo $job;//$job_arr[$dataArray[0][csf('order_id')]]; ?></td>
						
					</tr>
				<tr>
					<td><strong>Order No:</strong></td><td width="175px"><? echo $po_no; ?></td>
					<td><strong>Style Ref.:</strong></td><td width="175px"><? echo $dataArray[0][csf('style_ref')]; ?></td>
					<td><strong>Internal Ref No:</strong></td><td colspan="5"><? echo $internal_ref;//$po_arr[$dataArray[0][csf('order_id')]]; ?></td>
				</tr>
				
					<tr>
						<td><strong>S.WO No:</strong></td><td colspan="5"><? echo $sb_booking_no; ?></td>
					</tr>
					<?
					if($print_with_vat==1)
					{
						?>
						<tr>
							<td><strong>VAT Number :</strong></td>
							<td><p>
								<?
								$vat_no=return_field_value("vat_number","lib_company","id=".$data[0],"vat_number");
								echo $vat_no;
								?></p></td>
								<td><strong>Bar Code:</strong></td><td colspan="3" id="barcode_img_id"></td>
							</tr>
							<?
						}
						else
						{
							?>
							<tr>
								<td valign="top">&nbsp;</strong></td>
								<td>&nbsp;</td>
								<td><strong>Bar Code:</strong></td><td colspan="3" id="barcode_img_id"></td>
							</tr>
							<?
						}
						?>
					</table>
                    <?
                    
					
					 //echo $cuff_flat_deter.'='.$other_type_deter;
					// print_r($desc_arr);
					 $sql_data_booking="select c.id as conv_dtl_id,b.job_no,b.po_break_down_id as po_id,b.sensitivity,b.uom,b.gmts_color_id as gmt_color,b.fabric_color_id,b.gmts_size,b.item_size,b.labdip_no,b.req_qty,b.option_shade,b.fabric_color_id,b.rate,(b.wo_qnty) as wo_qnty,(b.amount) as amount,c.fabric_description as fab_dtls_id from  wo_pre_cost_fab_conv_cost_dtls c,wo_booking_dtls b where  b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.booking_type=3 and b.entry_form_id=229 and  b.po_break_down_id in($order_id) and b.process=31 and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.wo_qnty>0 order by b.po_break_down_id";
					$data_booking=sql_select($sql_data_booking);
					 $cuff_booking_arr=array();$pre_booking_arr=array();
					 foreach($data_booking as $row)
					 {
						$fabric_desc= $fab_desc_arr[$row[csf("po_id")]][$row[csf("fab_dtls_id")]]['fab_desc'];
						$body_part_typeID=$body_part_type_arr[$row[csf("po_id")]][$row[csf("fab_dtls_id")]];
						if($body_part_typeID==40 || $body_part_typeID==50)
						{
							
						$cuff_booking_arr[$row[csf("po_id")]][$fabric_desc][$row[csf("gmt_color")]][$row[csf("gmts_size")]]['wo_qnty']+=$row[csf("wo_qnty")];
						$cuff_booking_arr[$row[csf("po_id")]][$fabric_desc][$row[csf("gmt_color")]][$row[csf("gmts_size")]]['amount']+=$row[csf("amount")];
						$cuff_booking_arr[$row[csf("po_id")]][$fabric_desc][$row[csf("gmt_color")]][$row[csf("gmts_size")]]['rate']=$row[csf("rate")];
						$cuff_booking_arr[$row[csf("po_id")]][$fabric_desc][$row[csf("gmt_color")]][$row[csf("gmts_size")]]['item_size']=$row[csf("item_size")];
						$cuff_booking_arr[$row[csf("po_id")]][$fabric_desc][$row[csf("gmt_color")]][$row[csf("gmts_size")]]['uom']=$row[csf("uom")];
						$cuff_booking_arr[$row[csf("po_id")]][$fabric_desc][$row[csf("gmt_color")]][$row[csf("gmts_size")]]['labdip_no']=$row[csf("labdip_no")];
						$cuff_booking_arr[$row[csf("po_id")]][$fabric_desc][$row[csf("gmt_color")]][$row[csf("gmts_size")]]['option_shade']=$row[csf("option_shade")];
						$cuff_booking_arr[$row[csf("po_id")]][$fabric_desc][$row[csf("gmt_color")]][$row[csf("gmts_size")]]['fabric_color_id']=$row[csf("fabric_color_id")];
						$cuff_booking_arr[$row[csf("po_id")]][$fabric_desc][$row[csf("gmt_color")]][$row[csf("gmts_size")]]['req_qty']+=$row[csf("req_qty")];
						}
						else
						{
						$pre_booking_arr[$row[csf("po_id")]][$fabric_desc][$row[csf("gmt_color")]][$row[csf("gmts_size")]]['wo_qnty']+=$row[csf("wo_qnty")];
						$pre_booking_arr[$row[csf("po_id")]][$fabric_desc][$row[csf("gmt_color")]][$row[csf("gmts_size")]]['amount']+=$row[csf("amount")];
						$pre_booking_arr[$row[csf("po_id")]][$fabric_desc][$row[csf("gmt_color")]][$row[csf("gmts_size")]]['rate']=$row[csf("rate")];
						$pre_booking_arr[$row[csf("po_id")]][$fabric_desc][$row[csf("gmt_color")]][$row[csf("gmts_size")]]['item_size']=$row[csf("item_size")];
						$pre_booking_arr[$row[csf("po_id")]][$fabric_desc][$row[csf("gmt_color")]][$row[csf("gmts_size")]]['uom']=$row[csf("uom")];
						$pre_booking_arr[$row[csf("po_id")]][$fabric_desc][$row[csf("gmt_color")]][$row[csf("gmts_size")]]['labdip_no']=$row[csf("labdip_no")];
						$pre_booking_arr[$row[csf("po_id")]][$fabric_desc][$row[csf("gmt_color")]][$row[csf("gmts_size")]]['option_shade']=$row[csf("option_shade")];
						$pre_booking_arr[$row[csf("po_id")]][$fabric_desc][$row[csf("gmt_color")]][$row[csf("gmts_size")]]['fabric_color_id']=$row[csf("fabric_color_id")];
						$pre_booking_arr[$row[csf("po_id")]][$fabric_desc][$row[csf("gmt_color")]][$row[csf("gmts_size")]]['req_qty']+=$row[csf("req_qty")];
						}
					 }
					// print_r($booking_arr);
					foreach($pre_booking_arr as $po_id=>$po_data)
					{
					  $po_row_span=0;
					  foreach($po_data as $fab_desc=>$desc_data)
					  {
						   $fab_row_span=0;
						   foreach($desc_data as $color_id=>$color_data)
						   {
								$colo_row_span=0;
								foreach($color_data as $size_id=>$row)
								{
									$po_row_span++;$fab_row_span++;$colo_row_span++;
								}
								$po_row_arr[$po_id]=$po_row_span;
								$fab_row_arr[$po_id][$fab_desc]=$fab_row_span;
								$color_row_arr[$po_id][$fab_desc][$color_id]=$colo_row_span;
						   }
					  }
					}
					
					//print_r($po_row_arr);
					?>
                  <div style="width:100%; margin:5px;">
                     
                    <div style="width:100%;margin:5px;">
						<table align="left" cellspacing="0" width="1040"  border="1" rules="all" class="rpt_table" >
							<thead bgcolor="#dddddd" align="center">
								<th width="20">SL</th>
								<th width="120">PO No</th>
								<th width="200">Fabric Desc.</th>
								<th width="60">Labdip No</th>
								<th width="50">Option</th>
								<th width="70">Gmts Color</th>
								<th width="70">Item Color</th>
								<th width="40">Gmt Size</th>
                                <th width="40">Item Size</th>
								<th width="70">PCS</th>
								<th width="40">Uom</th>
								<th width="70">Wo Qty</th>
								<th width="40">Rate</th>
								<th>Amount</th>
							</thead>
							<tbody>
                            	<?
								$i=1;$tot_amount=$total_wo_qnty=0;$m=1;
                                foreach($pre_booking_arr as $po_id=>$po_data)
								{
								  $p=0;
								  foreach($po_data as $fab_desc=>$desc_data)
								  {
									   $f=0;
									   foreach($desc_data as $color_id=>$color_data)
								  	   {
										    $c=0;
											foreach($color_data as $size_id=>$row)
								  	   		{
											if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
                                <tr bgcolor="<? echo $bgcolor; ?>">
                                <?
                                if($p==0)
								{
									
								?>
                                <td rowspan="<? echo $po_row_arr[$po_id];?>" align="center"><? echo $m; ?></td>
                                <td rowspan="<? echo $po_row_arr[$po_id];?>" ><p><? echo $po_arr[$po_id]; ?></p></td>
                                 <?
                                } 
								if($f==0)
								{
								?>
                                <td rowspan="<? echo $fab_row_arr[$po_id][$fab_desc];?>"><div style="word-wrap:break-word; width:200px;"><? echo $fab_desc; ?></div></td>
                               
                                <?
								}
								?>
                                
                                <?
								if($c==0)
								{
								?>
                              
                               <td rowspan="<? echo $color_row_arr[$po_id][$fab_desc][$color_id];?>"><p><? echo $row[("labdip_no")]; ?></p></td>
                                <td rowspan="<? echo $color_row_arr[$po_id][$fab_desc][$color_id];?>"><p><? echo $row[("option_shade")]; ?></p></td>
                                <td rowspan="<? echo $color_row_arr[$po_id][$fab_desc][$color_id];?>" align="center"><p><? echo $color_array[$color_id]; ?></p></td>
                                <td rowspan="<? echo $color_row_arr[$po_id][$fab_desc][$color_id];?>" align="center"><p><? echo $color_array[$row[("fabric_color_id")]]; ?></p></td>
                                <?
								}
								?>
                                <td align="center" style="word-break:break-all;"><p><? echo $size_array[$size_id]; ?></p></td>
                                <td align="center" style="word-break:break-all;"><p><? echo $row[("item_size")]; ?></p></td>
                                <td align="right"><? echo number_format($row[("req_qty")],2); ?></td>
                                <td style="word-break:break-all;"><p><? echo  $unit_of_measurement[$row[("uom")]]; ?></p></td>
                                <td align="right"><? echo number_format($row[("wo_qnty")],2); ?></td>
                                <td align="right"><? echo number_format($row[("rate")],2); ?></td>
                                <td align="right"><? echo number_format($row[("amount")],2); ?></td>
                                </tr>
                                <?		$i++;$p++;$f++;$c++;
										$tot_amount+=$row[("amount")];$total_wo_qnty+=$row[("wo_qnty")];
										}
									}
									 $m++;
								  }
								  ?>
                                  
                                  <?
								 
								}
								?>
                                	<tr>
                                    <td colspan="11" align="right"><b> Sub Total</b> </td>
                                    <td align="right"><b><? echo number_format($total_wo_qnty,2); ?></b></td>
                                    <td align="right"><b><? //echo number_format($row[("amount")],2); ?></td>
                                    <td align="right"><b><? echo number_format($tot_amount,2); ?></b></td>
                                    </tr>
                            </tbody>
                         </table>
            </div>
            <br clear="all">
      	  <div style="width:100%; margin:5px;">
            <table align="left" cellspacing="0" width="930"  border="1" rules="all" class="rpt_table" >
                <thead bgcolor="#dddddd" align="center">
                    <th width="20">SL</th>
                    <th width="100">Order No</th>
                    <th width="50">Prog. No</th>
                    <th width="130">Item Des.</th>
                    <th width="50">Stich Length</th>
                    <th width="40">GSM</th>
                    <th width="40">Fin. Dia</th>
                   
                    <th width="70">Color</th>
                    <th width="40">Roll</th>
                    <th width="70">Issue Qty</th>
                    <th width="40">UOM</th>
                    <th width="50">Count</th>
                    <th width="40">Supplier</th>
                    <th width="80">Yarn Lot</th>
                 
                    <th>Remarks</th>
                </thead>
                <tbody>
								<?
				$color_arr = return_library_array( "select id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
				$product_array=array();
				$product_sql = sql_select("select id, item_description, supplier_id,item_category_id,product_name_details, lot,gsm,dia_width,color,brand,unit_of_measure from product_details_master where item_category_id in(1,14)");
				foreach($product_sql as $row)
				{
					$product_array[$row[csf("id")]]['product_name_details']=$row[csf("product_name_details")];
					$product_array[$row[csf("id")]]['item_description']=$row[csf("item_description")];
					$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
					$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
					$product_array[$row[csf("id")]]['brand']=$row[csf("brand")];
					$product_array[$row[csf("id")]]['color']=$row[csf("color")];
					$product_array[$row[csf("id")]]['uom']=$row[csf("unit_of_measure")];

					if($row[csf("item_category_id")]==1)
					{
						$product_array[$row[csf("lot")].'l']['lot']=$row[csf("brand")];
						$product_array[$row[csf("lot")]]['supp']=$supplier_short_library[$row[csf("supplier_id")]];
					}
				}
				if ($db_type==0) $mc_concat="group_concat(b.machine_no_id)";
				else if ($db_type==2) $mc_concat="LISTAGG(b.machine_no_id, ',') WITHIN GROUP (ORDER BY b.machine_no_id)";
				$machine_id_arr=array();
				$sql_prod_mc_id=sql_select("select a.po_breakdown_id, b.prod_id, $mc_concat as machine_no_id from order_wise_pro_details a, pro_grey_prod_entry_dtls b where b.id=a.dtls_id and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.po_breakdown_id, b.prod_id");
				foreach($sql_prod_mc_id as $row)
				{
					$machine_id_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]=$row[csf("machine_no_id")];
				}
				// echo "<pre>";
				// print_r($machine_id_arr);die;

				//$sql_dtls = "select b.id, b.program_no, b.prod_id, a.booking_id, b.issue_qnty, b.no_of_roll, b.yarn_lot, b.yarn_count, b.store_name, b.color_id, b.rack, b.self, b.stitch_length,b.remarks from inv_issue_master a, inv_grey_fabric_issue_dtls b where a.id=b.mst_id and a.entry_form=577 and a.id=$data[1] and b.status_active=1 and b.is_deleted=0";
				 $sql_dtls = "select b.id, b.program_no, b.prod_id, a.booking_id, b.no_of_roll, b.yarn_lot, b.yarn_count, b.store_name, b.color_id, b.rack, b.self, b.stitch_length,b.remarks, c.po_breakdown_id as order_id ,c.quantity as issue_qnty,d.product_name_details
				from inv_issue_master a, inv_grey_fabric_issue_dtls  b,order_wise_pro_details c,product_details_master d
				where a.id=b.mst_id and b.prod_id=c.prod_id and b.trans_id=c.trans_id and b.id=c.dtls_id and d.id=c.prod_id and d.id=b.prod_id and a.id=$data[1] and a.company_id=$data[0] and d.detarmination_id in($other_type_deter) ";
								//echo $sql_dtls; // 

			$sql_result= sql_select($sql_dtls);
			$sql_count=count($sql_result);
			$i=1; $all_program_no='';
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($row[csf('program_no')]!='')
				{
					if ($all_program_no=='') $all_program_no=$row[csf('program_no')]; else $all_program_no.=', '.$row[csf('program_no')];
				}

				$roll_qty_sum+=$row[csf('no_of_roll')];
				$issue_qnty_sum+=$row[csf('issue_qnty')];
			$yarn_count=$row[csf("yarn_count")];
			$count_id=explode(',',$yarn_count);
			$count_val='';
			foreach ($count_id as $val)
			{
				if($count_val=='') $count_val=$yarn_count_arr[$val]; else $count_val.=", ".$yarn_count_arr[$val];
			}

			$yarn_lot=$row[csf("yarn_lot")];
			$yarn_lot_id=explode(',',$yarn_lot);
			$yarn_lot_supp='';
			foreach ($yarn_lot_id as $val)
			{
				if($yarn_lot_supp=='') $yarn_lot_supp=$product_array[$val]['supp']; else $yarn_lot_supp.=", ".$product_array[$val]['supp'];
			}

			$pono_id=array_unique(explode(',',$po_id));
			$mc_dia="";
			foreach($pono_id as $poId )
			{
				$mc_id_val=array_unique(explode(',',$machine_id_arr[$poId][$row[csf("prod_id")]]));
				foreach($mc_id_val as $mc_id)
				{
					if($mc_dia=="") $mc_dia=trim($mc_dia_arr[$mc_id]); else $mc_dia.=', '.trim($mc_dia_arr[$mc_id]);//
				}
			}
			$mc_dia=implode(", ",array_unique(explode(', ',$mc_dia)));
			$color_ids=array_unique(explode(",",$row[csf('color_id')]));
			$color_name="";
			foreach($color_ids as $cid)
			{
				if($color_name!="") $color_name.=", ".$color_arr[$cid];else $color_name=$color_arr[$cid];
			}
			$color_names=implode(", ",array_unique(explode(", ",$color_name)));

			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td align="center"><? echo $i; ?></td>
				<td><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
				<td><p><? if($row[csf("program_no")]==0) echo "&nbsp;"; else echo $row[csf("program_no")]; ?></p></td>
				<td><p><? echo $row[csf("product_name_details")]; ?></p></td>
				<td><p><? echo $row[csf("stitch_length")]; ?></p></td>
				<td align="center"><p><? echo $product_array[$row[csf("prod_id")]]['gsm']; ?></p></td>
				<td align="center"><p><? echo $product_array[$row[csf("prod_id")]]['dia_width']; ?></p></td>
				
				<td style="word-break:break-all;"><p><? echo $color_names;//$color_arr[$row[csf("color_id")]]; ?></p></td>
				<td align="right"><? echo $row[csf("no_of_roll")]; ?></td>
				<td align="right"><? echo number_format($row[csf("issue_qnty")],2); ?></td>
				<td align="center"><p><? echo $unit_of_measurement[$product_array[$row[csf("prod_id")]]['uom']]; ?></p></td>
				<td align="center"><p><? echo $count_val; ?></p></td>
				<td><p><? echo $yarn_lot_supp; ?></p></td>
				<td width="80" style="word-wrap:break-word; word-break: break-all;"><p><? echo $yarn_lot; ?></p></td>
				
				<td><div style="word-wrap:break-word; width:70px;"><? echo $row[csf("remarks")]; ?></div></td>
			</tr>
			<?
				//if ($sql_count>1) {$inWordTxt="Quantity";}else{$inWordTxt=$unit_of_measurement[$product_array[$row[csf("prod_id")]]['uom']];}
			$inWordTxt=$unit_of_measurement[$product_array[$row[csf("prod_id")]]['uom']];
			$tot_issue_qty+=$row[csf("issue_qnty")];
			$tot_roll_qty+=$row[csf("no_of_roll")];

			$i++;
		} ?>
        <tr>
            <td colspan="8" align="right"><b>Sub Total</b> </td>
            <td align="right"><b><? echo number_format($tot_roll_qty,2); ?></b></td>
            <td align="right"><b><? echo number_format($tot_issue_qty,2); ?></b></td>
            <td align="right" colspan="5"><? //echo number_format($row[("amount")],2); ?></td>
           
       </tr>
	</tbody>
	</table>
    </div>
		</div>
       	 <br clear="all">
       	 <div style="width:100%; margin:5px;">
                     <?
                     if(count($cuff_booking_arr)>0)
					 {
					 ?>
                    <div style="width:100%;margin:5px;">
						<table align="left" cellspacing="0" width="1040"  border="1" rules="all" class="rpt_table" >
                        <caption><b> Cuff & Flat Knit</b></caption>
							<thead bgcolor="#dddddd" align="center">
								<th width="20">SL</th>
								<th width="120">PO No</th>
								<th width="200">Fabric Desc.</th>
								<th width="60">Labdip No</th>
								<th width="50">Option</th>
								<th width="70">Gmts Color</th>
								<th width="70">Item Color</th>
								<th width="40">Gmt Size</th>
                                <th width="40">Item Size</th>
								<th width="70">PCS</th>
								<th width="40">Uom</th>
								<th width="70">Wo Qty</th>
								<th width="40">Rate</th>
								<th>Amount</th>
							</thead>
							<tbody>
                            	<?
								foreach($cuff_booking_arr as $po_id=>$po_data)
								{
								  $po_row_span=0;
								  foreach($po_data as $fab_desc=>$desc_data)
								  {
									   $fab_row_span=0;
									   foreach($desc_data as $color_id=>$color_data)
									   {
											$colo_row_span=0;
											foreach($color_data as $size_id=>$row)
											{
												$po_row_span++;$fab_row_span++;$colo_row_span++;
											}
											$po_row_arr[$po_id]=$po_row_span;
											$fab_row_arr[$po_id][$fab_desc]=$fab_row_span;
											$color_row_arr[$po_id][$fab_desc][$color_id]=$colo_row_span;
									   }
								  }
								}
								$ii=1;$cuff_tot_amount=$cuff_total_wo_qnty=0;$mm=1;
                                foreach($cuff_booking_arr as $po_id=>$po_data)
								{
								  $pp=0;
								  foreach($po_data as $fab_desc=>$desc_data)
								  {
									   $ff=0;
									   foreach($desc_data as $color_id=>$color_data)
								  	   {
										    $cc=0;
											foreach($color_data as $size_id=>$row)
								  	   		{
												if ($ii%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
                                <tr bgcolor="<? echo $bgcolor; ?>">
                                <?
                                if($pp==0)
								{
									
								?>
                                <td rowspan="<? echo $po_row_arr[$po_id];?>" align="center"><? echo $mm; ?></td>
                                <td rowspan="<? echo $po_row_arr[$po_id];?>" ><p><? echo $po_arr[$po_id]; ?></p></td>
                                 <?
                                } 
								if($ff==0)
								{
								?>
                                <td rowspan="<? echo $fab_row_arr[$po_id][$fab_desc];?>"><div style="word-wrap:break-word; width:200px;"><? echo $fab_desc; ?></div></td>
                               
                                <?
								}
								if($cc==0)
								{
								?>
                              
                                <td rowspan="<? echo $color_row_arr[$po_id][$fab_desc][$color_id];?>"><p><? echo $row[("labdip_no")]; ?></p></td>
                                <td rowspan="<? echo $color_row_arr[$po_id][$fab_desc][$color_id];?>"><p><? echo $row[("option_shade")]; ?></p></td>
                                <td rowspan="<? echo $color_row_arr[$po_id][$fab_desc][$color_id];?>" align="center"><p><? echo $color_array[$color_id]; ?></p></td>
                                <td rowspan="<? echo $color_row_arr[$po_id][$fab_desc][$color_id];?>" align="center"><p><? echo $color_array[$row[("fabric_color_id")]]; ?></p></td>
                                <?
								}
								?>
                                <td align="center" style="word-break:break-all;"><p><? echo $size_array[$size_id];; ?></p></td>
                                 <td align="center" style="word-break:break-all;"><p><? echo $row[("item_size")]; ?></p></td>
                                <td align="right"><? echo number_format($row[("req_qty")],2); ?></td>
                                <td style="word-break:break-all;"><p><? echo  $unit_of_measurement[$row[("uom")]]; ?></p></td>
                                <td align="right"><? echo number_format($row[("wo_qnty")],2); ?></td>
                                <td align="right"><? echo number_format($row[("rate")],2); ?></td>
                                <td align="right"><? echo number_format($row[("amount")],2); ?></td>
                                </tr>
                                <?		$ii++;$pp++;$ff++;$cc++;
										$cuff_tot_amount+=$row[("amount")];$cuff_total_wo_qnty+=$row[("wo_qnty")];
										}
									}
									$mm++;
								  }
								   
								}
								?>
                                	<tr>
                                    <td colspan="11" align="right"><b> Sub Total </b> </td>
                                    <td align="right"><b><? echo number_format($cuff_total_wo_qnty,2); ?></b></td>
                                    <td align="right"><b><? //echo number_format($row[("amount")],2); ?></td>
                                    <td align="right"><b><? echo number_format($cuff_tot_amount,2); ?></b></td>
                                    </tr>
                                    
                                    <tr>
                                    <td colspan="11" align="right"><b> Grand Total</b> </td>
                                    <td align="right"><b><? echo number_format($cuff_total_wo_qnty+$total_wo_qnty,2); ?></b></td>
                                    <td align="right"><b><? //echo number_format($row[("amount")],2); ?></td>
                                    <td align="right"><b><? echo number_format($tot_amount+$cuff_tot_amount,2); ?></b></td>
                                    </tr>
                            </tbody>
                         </table>
            </div>
            <br clear="all">
      	  <div style="width:100%; margin:5px;">
            <table align="left" cellspacing="0" width="930"  border="1" rules="all" class="rpt_table" >
                <thead bgcolor="#dddddd" align="center">
                    <th width="20">SL</th>
                    <th width="100">Order No</th>
                    <th width="50">Prog. No</th>
                    <th width="130">Item Des.</th>
                    <th width="50">Stich Length</th>
                    <th width="40">GSM</th>
                    <th width="40">Fin. Dia</th>
                   
                    <th width="70">Color</th>
                    <th width="40">Roll</th>
                    <th width="70">Issue Qty</th>
                    <th width="40">UOM</th>
                    <th width="50">Count</th>
                    <th width="40">Supplier</th>
                    <th width="80">Yarn Lot</th>
                   
                    <th>Remarks</th>
                </thead>
                <tbody>
								<?
				$color_arr = return_library_array( "select id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
				$product_array=array();
				$product_sql = sql_select("select id, item_description, supplier_id,item_category_id,product_name_details, lot,gsm,dia_width,color,brand,unit_of_measure from product_details_master where item_category_id in(1,14)");
				foreach($product_sql as $row)
				{
					$product_array[$row[csf("id")]]['product_name_details']=$row[csf("product_name_details")];
					$product_array[$row[csf("id")]]['item_description']=$row[csf("item_description")];
					$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
					$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
					$product_array[$row[csf("id")]]['brand']=$row[csf("brand")];
					$product_array[$row[csf("id")]]['color']=$row[csf("color")];
					$product_array[$row[csf("id")]]['uom']=$row[csf("unit_of_measure")];

					if($row[csf("item_category_id")]==1)
					{
						$product_array[$row[csf("lot")].'l']['lot']=$row[csf("brand")];
						$product_array[$row[csf("lot")]]['supp']=$supplier_short_library[$row[csf("supplier_id")]];
					}
				}
				if ($db_type==0) $mc_concat="group_concat(b.machine_no_id)";
				else if ($db_type==2) $mc_concat="LISTAGG(b.machine_no_id, ',') WITHIN GROUP (ORDER BY b.machine_no_id)";
				$machine_id_arr=array();
				$sql_prod_mc_id=sql_select("select a.po_breakdown_id, b.prod_id, $mc_concat as machine_no_id from order_wise_pro_details a, pro_grey_prod_entry_dtls b where b.id=a.dtls_id and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.po_breakdown_id, b.prod_id");
				foreach($sql_prod_mc_id as $row)
				{
					$machine_id_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]=$row[csf("machine_no_id")];
				}
				// echo "<pre>";
				// print_r($machine_id_arr);die;

				//$sql_dtls = "select b.id, b.program_no, b.prod_id, a.booking_id, b.issue_qnty, b.no_of_roll, b.yarn_lot, b.yarn_count, b.store_name, b.color_id, b.rack, b.self, b.stitch_length,b.remarks from inv_issue_master a, inv_grey_fabric_issue_dtls b where a.id=b.mst_id and a.entry_form=577 and a.id=$data[1] and b.status_active=1 and b.is_deleted=0";
				$sql_dtls = "select b.id, b.program_no, b.prod_id, a.booking_id, b.no_of_roll, b.yarn_lot, b.yarn_count, b.store_name, b.color_id, b.rack, b.self, b.stitch_length,b.remarks, c.po_breakdown_id as order_id ,c.quantity as issue_qnty,d.product_name_details

				from inv_issue_master a, inv_grey_fabric_issue_dtls  b,order_wise_pro_details c,product_details_master d
				where a.id=b.mst_id and b.prod_id=c.prod_id and b.trans_id=c.trans_id and b.id=c.dtls_id and d.id=c.prod_id and d.id=b.prod_id and a.id=$data[1] and a.company_id=$data[0] and d.detarmination_id in($cuff_flat_deter) ";
								//echo $sql_dtls;  

			$sql_result= sql_select($sql_dtls);
			$sql_count=count($sql_result);
			$i=1; $all_program_no='';$cuff_tot_issue_qty=0;$cuff_tot_roll_qty=0;
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($row[csf('program_no')]!='')
				{
					if ($all_program_no=='') $all_program_no=$row[csf('program_no')]; else $all_program_no.=', '.$row[csf('program_no')];
				}

				$roll_qty_sum+=$row[csf('no_of_roll')];
				$issue_qnty_sum+=$row[csf('issue_qnty')];

			$yarn_count=$row[csf("yarn_count")];
			$count_id=explode(',',$yarn_count);
			$count_val='';
			foreach ($count_id as $val)
			{
				if($count_val=='') $count_val=$yarn_count_arr[$val]; else $count_val.=", ".$yarn_count_arr[$val];
			}

			$yarn_lot=$row[csf("yarn_lot")];
			$yarn_lot_id=explode(',',$yarn_lot);
			$yarn_lot_supp='';
			foreach ($yarn_lot_id as $val)
			{
				if($yarn_lot_supp=='') $yarn_lot_supp=$product_array[$val]['supp']; else $yarn_lot_supp.=", ".$product_array[$val]['supp'];
			}

			$pono_id=array_unique(explode(',',$po_id));
			$mc_dia="";
			foreach($pono_id as $poId )
			{
				$mc_id_val=array_unique(explode(',',$machine_id_arr[$poId][$row[csf("prod_id")]]));
				foreach($mc_id_val as $mc_id)
				{
					if($mc_dia=="") $mc_dia=trim($mc_dia_arr[$mc_id]); else $mc_dia.=', '.trim($mc_dia_arr[$mc_id]);//
				}
			}
			$mc_dia=implode(", ",array_unique(explode(', ',$mc_dia)));
			$color_ids=array_unique(explode(",",$row[csf('color_id')]));
			$color_name="";
			foreach($color_ids as $cid)
			{
				if($color_name!="") $color_name.=", ".$color_arr[$cid];else $color_name=$color_arr[$cid];
			}
			$color_names=implode(", ",array_unique(explode(", ",$color_name)));

			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td align="center"><? echo $i; ?></td>
				<td><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
				<td><p><? if($row[csf("program_no")]==0) echo "&nbsp;"; else echo $row[csf("program_no")]; ?></p></td>
				<td><p><? echo $row[csf("product_name_details")]; ?></p></td>
				<td><p><? echo $row[csf("stitch_length")]; ?></p></td>
				<td align="center"><p><? echo $product_array[$row[csf("prod_id")]]['gsm']; ?></p></td>
				<td align="center"><p><? echo $product_array[$row[csf("prod_id")]]['dia_width']; ?></p></td>
				
				<td style="word-break:break-all;"><p><? echo $color_names;//$color_arr[$row[csf("color_id")]]; ?></p></td>
				<td align="right"><? echo $row[csf("no_of_roll")]; ?></td>
				<td align="right"><? echo number_format($row[csf("issue_qnty")],2); ?></td>
				<td align="center"><p><? echo $unit_of_measurement[$product_array[$row[csf("prod_id")]]['uom']]; ?></p></td>
				<td align="center"><p><? echo $count_val; ?></p></td>
				<td><p><? echo $yarn_lot_supp; ?></p></td>
				<td width="80" style="word-wrap:break-word; word-break: break-all;"><p><? echo $yarn_lot; ?></p></td>
				
				<td><div style="word-wrap:break-word; width:70px;"><? echo $row[csf("remarks")]; ?></div></td>
			</tr>
			<?
				//if ($sql_count>1) {$inWordTxt="Quantity";}else{$inWordTxt=$unit_of_measurement[$product_array[$row[csf("prod_id")]]['uom']];}
			$inWordTxt=$unit_of_measurement[$product_array[$row[csf("prod_id")]]['uom']];
			$cuff_tot_issue_qty+=$row[csf("issue_qnty")];
			$cuff_tot_roll_qty+=$row[csf("no_of_roll")];

			$i++;
		} ?>
        <tr>
            <td colspan="8" align="right"> <b>Sub Total</b> </td>
            <td align="right"><b><? echo number_format($cuff_tot_roll_qty,2); ?></b></td>
            <td align="right"><b><? echo number_format($cuff_tot_issue_qty,2); ?></b></td>
            <td align="right" colspan="5"><b><? //echo number_format($row[("amount")],2); ?></b></td>
       </tr>
        <tr>
            <td colspan="8" align="right"> <b>Total</b> </td>
            <td align="right"><b><? echo number_format($cuff_tot_roll_qty+$tot_roll_qty,2); ?></b></td>
            <td align="right"><b><? echo number_format($cuff_tot_issue_qty+$tot_issue_qty,2); ?></b></td>
            <td align="right" colspan="5"><b><? //echo number_format($row[("amount")],2); ?></b></td>
       </tr>
	</tbody>
	</table>
    	</div>
        <?
		 }
		?>
         <br clear="all">
       <table  width="90%" class="rpt_table" style="border:1px solid black; margin:5px;"   border="0" cellpadding="0" cellspacing="0">
        <thead>
            <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Special Instruction</th>
            </tr>
        </thead>
        <tbody>
        <?
        $data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no='".$s_wo_no."'");// quotation_id='$data'
        if ( count($data_array)>0)
        {
            $i=0;
            foreach( $data_array as $row )
            {
                $i++;
                ?>
                    <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;">
                        <? echo $row[csf('terms')]; ?>
                        </td>
                    </tr>
                <?
            }
        }
       
        ?>
    </tbody>
    </table>
		</div>
	<br>&nbsp;
	<!--================================================================-->
	<?
	echo signature_table(17, $data[0], "900px");
	?>
	
    
					</div>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>

	function generateBarcode( valuess )
	{
			var value = valuess;//$("#barcodeValue").val();
		 // alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();

			var settings = {
				output:renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize:5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			$("#barcode_img_id").html('11');
			value = {code:value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $data[2]; ?>');
	</script>
    </div>
	<?
	exit();
}

if ($action=="grey_fabric_issue_print_8")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$update_id=$data[1];
	$issue_no=$data[2];
	$print_with_vat=$data[4];
	//print_r ($data);die;
	$sql="select id, issue_number, issue_number_prefix_num, issue_date, issue_basis, issue_purpose, knit_dye_source, knit_dye_company, booking_id, batch_no, buyer_id, challan_no, style_ref, order_id from inv_issue_master where id=$update_id and company_id=$company";
	$dataArray=sql_select($sql);
	unset($sql);

	$supplier_library=array(); $supplier_short_library=array();
	$supplier_data=sql_select( "select id,supplier_name,short_name from lib_supplier");
	foreach ($supplier_data as $row)
	{
		$supplier_library[$row[csf('id')]]=$row[csf('supplier_name')];
		$supplier_short_library[$row[csf('id')]]=$row[csf('short_name')];
	}
	
	$grey_issue_basis=array(1=>"Booking",2=>"Independent",3=>"Knitting Plan");
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$batch_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no"  );
	//$booking_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no"  );
	//$booking_non_order_arr=return_library_array( "select id, booking_no from  wo_non_ord_samp_booking_mst", "id", "booking_no"  );
	//$po_arr=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number"  );
	//$internal_ref_arr=return_library_array( "select id, grouping from  wo_po_break_down", "id", "grouping"  );
	//$job_arr=return_library_array( "select id, job_no_mst from  wo_po_break_down","id","job_no_mst");
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand",'id','brand_name');
	$mc_dia_arr = return_library_array("select id, dia_width from lib_machine_name",'id','dia_width');


	$lib_room_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "room_id","floor_room_rack_name" );
	$lib_rack_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "rack_id","floor_room_rack_name" );
	$lib_shelf_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "shelf_id","floor_room_rack_name" );
	$lib_bin_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "bin_id","floor_room_rack_name" );

	$sql="SELECT b.id, b.program_no, b.prod_id, b.no_of_roll, b.yarn_lot, b.yarn_count, b.store_name, b.color_id, b.rack, b.self, b.stitch_length, b.remarks, d.product_name_details, d.item_description, d.supplier_id, d.item_category_id, d.lot, d.gsm, d.dia_width, d.color, d.brand, d.unit_of_measure, c.is_sales, c.po_breakdown_id, sum(c.quantity) as issue_qnty, a.issue_number, a.issue_date, a.issue_basis, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_id, a.batch_no, a.buyer_id, a.challan_no, a.style_ref, a.order_id 
	from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c, product_details_master d
	where a.id=b.mst_id and b.prod_id=c.prod_id and b.trans_id=c.trans_id and b.id=c.dtls_id and d.id=c.prod_id and d.id=b.prod_id and a.id=$update_id and a.company_id=$company and a.item_category=14 and a.entry_form=577 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.trans_type=2 and c.is_sales=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 
	group by b.id, b.program_no, b.prod_id, b.no_of_roll, b.yarn_lot, b.yarn_count, b.store_name, b.color_id, b.rack, b.self, b.stitch_length, b.remarks, d.product_name_details, d.item_description, d.supplier_id, d.item_category_id, d.lot, d.gsm, d.dia_width, d.color, d.brand, d.unit_of_measure, c.is_sales, c.po_breakdown_id, a.issue_number, a.issue_date, a.issue_basis, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_id, a.batch_no, a.buyer_id, a.challan_no, a.style_ref, a.order_id";
	$sql_res = sql_select($sql);
	foreach ($sql_res as $val)
	{
		if($val[csf("is_sales")] == 1){
			$sales_ids .= $val[csf("po_breakdown_id")].",";
		}
	}
	//$po_ids = rtrim($po_ids,",");
	$sales_ids = rtrim($sales_ids,",");

	if ($sales_ids != "")
	{
		$sales_sql="SELECT a.id as po_id, a.booking_id, a.job_no, a.sales_booking_no, a.within_group, a.buyer_id, a.style_ref_no, a.po_buyer, a.po_job_no 
		from fabric_sales_order_mst a
		where a.id in ($sales_ids) and a.entry_form=109 and a.status_active=1 and a.is_deleted=0";
		$sales_sql_result=sql_select($sales_sql);
		foreach($sales_sql_result as $row)
		{
			if ($row[csf('within_group')] == 1)
			{
				$sales_array[$row[csf('po_id')]]['po_buyer']	 = $row[csf('po_buyer')];
				$sales_array[$row[csf('po_id')]]['po_job_no']	 = $row[csf('po_job_no')];
				$sales_array[$row[csf('po_id')]]['booking_id']	 = $row[csf('booking_id')];
				$sales_array[$row[csf('po_id')]]['style_ref_no'] = $row[csf('style_ref_no')];
				$sales_array[$row[csf('po_id')]]['within_group'] = $row[csf('within_group')];
				//$bookingID.=".$row_data[csf('booking_id')].".',';
			}
			else
			{
				$sales_array[$row[csf('po_id')]]['job']			 = $row[csf('job_no')];
				$sales_array[$row[csf('po_id')]]['booking_no']	 = $row[csf('sales_booking_no')];
				$sales_array[$row[csf('po_id')]]['buyer_id']	 = $row[csf('buyer_id')];
				$sales_array[$row[csf('po_id')]]['style_ref_no'] = $row[csf('style_ref_no')];
				$sales_array[$row[csf('po_id')]]['within_group'] = $row[csf('within_group')];
			}			
		}
	}

	/*if ($po_ids != "")
	{
		$job_sql="select a.style_ref_no, a.job_no, a.buyer_name, b.id as po_id, b.po_number from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c where a.job_no=b.job_no_mst and b.job_no_mst=c.job_no and b.id in($po_ids) group by a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number";
		$job_sql_result=sql_select($job_sql);
		foreach($job_sql_result as $row)
		{
			$job_array[$row[csf('po_id')]]['job_no']       = $row[csf('job_no')];
			$job_array[$row[csf('po_id')]]['buyer_name']   = $row[csf('buyer_name')];
			$job_array[$row[csf('po_id')]]['style_ref_no'] = $row[csf('style_ref_no')];
			$job_array[$row[csf('po_id')]]['po_number']    = $row[csf('po_number')];
		}		
	}*/	
	?>
	<div style="width:930px;">
		<table width="900" cellspacing="0" align="right" style="margin-bottom:10px">
			<tr>
				<td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<?
				$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
				?>
				<td  align="left">
					<?
					foreach($data_array as $img_row)
					{
						?>
						<img src='../../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50' align="middle" />
						<?
					}
					?>
				</td>

				<td colspan="4" align="center" style="font-size:14px">
					<?
					echo show_company($data[0],'','');//Aziz
					/*$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
					?>
						 <? echo $result[csf('plot_no')]; ?>
						 <? echo $result[csf('level_no')];?>
						 <? echo $result[csf('road_no')]; ?>
						 <? echo $result[csf('block_no')];?>
						 <? echo $result[csf('city')];?>
						 <? echo $result[csf('zip_code')]; ?>
						 <?php echo $result[csf('province')];?>
						 <? echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email Address: <? echo $result[csf('email')];?>
						Website No: <? echo $result[csf('website')];
					}*/
					?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><u>Knit Grey Fabric Delivery Challan</u></strong></td>
			</tr>
			<tr>
				<td rowspan="3" colspan="2" width="300" valign="top"><strong>Dyeing Company:</strong>
					<?
					$supp_add=$dataArray[0][csf('knit_dye_company')];
					$nameArray=sql_select( "select address_1,web_site,email,country_id from lib_supplier where id=$supp_add");
					foreach ($nameArray as $result)
					{
						$address="";
						if($result!="") $address=$result['address_1'];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
					}
					if ($dataArray[0][csf('knit_dye_source')]==1) echo $company_library[$dataArray[0][csf('knit_dye_company')]].'<br>'.'Address :- '.$address; else if ($dataArray[0][csf('knit_dye_source')]==3) echo $supplier_library[$dataArray[0][csf('knit_dye_company')]].'<br>'.'Address :- '.$address; ?></td>
					<td width="120"><strong>Issue ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
					<td width="125"><strong>Issue Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Issue Basis :</strong></td> <td width="175px"><? echo $grey_issue_basis[$dataArray[0][csf('issue_basis')]]; ?></td>
				<td><strong>Issue Purpose:</strong></td><td width="175px"><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Dyeing Source:</strong></td><td width="175px"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
				<td><strong>Sales/Booking No:</strong></td>
				<?
					if ($sales_ids != "") {
						if ($sales_sql_result[0][csf('within_group')] == 1)
						{
							$buyer = $buyer_library[$sales_sql_result[0][csf('po_buyer')]];
							$job = $sales_sql_result[0][csf('po_job_no')];
							$booking_no = return_field_value("booking_no","wo_booking_mst","id=$sales_sql_result[0][csf('booking_id')]");						
							$style_ref_no = $sales_sql_result[0][csf('style_ref_no')];
						}
						else 
						{
							$buyer = $buyer_library[$sales_sql_result[0][csf('buyer_id')]];
							$job = $sales_sql_result[0][csf('job_no')];
							$booking_no = $sales_sql_result[0][csf('sales_booking_no')];
							$style_ref_no = $sales_sql_result[0][csf('style_ref_no')];
						}	
					}
					/*if ($po_ids != "")
					{
						$buyer = $buyer_library[$job_sql_result[0][csf('buyer_name')]];
						$job = $job_sql_result[0][csf('job_no')];
						$booking_no = return_field_value("booking_no","wo_booking_dtls","job_no=$job_no");					
						$style_ref_no = $job_sql_result[0][csf('style_ref_no')];
					}*/ 
				?>	
				<td width="175px">
					<?
					/*if($dataArray[0][csf('issue_basis')]==1)
					{
						if(($dataArray[0][csf('issue_purpose')]==8 || $dataArray[0][csf('issue_purpose')]==3 || $dataArray[0][csf('issue_purpose')]==26 || $dataArray[0][csf('issue_purpose')]==29 || $dataArray[0][csf('issue_purpose')]==30 || $dataArray[0][csf('issue_purpose')]==31) && $dataArray[0][csf('issue_basis')]==1)
						{
							echo $booking_non_order_arr[$dataArray[0][csf('booking_id')]];
						}
						else
						{
							echo $booking_arr[$dataArray[0][csf('booking_id')]];
						}
					}
					else
					{
						echo $dataArray[0][csf('booking_id')];
					}*/
					echo $booking_no;
					?>
				</td>
			</tr>
			<tr>
				<td><strong>Challan No:</strong></td><td width="175px"><? if($dataArray[0][csf('challan_no')]=="") echo $dataArray[0][csf('issue_number_prefix_num')]; else echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>Batch Number:</strong></td><td width="175px"><? echo $batch_arr[$dataArray[0][csf('batch_no')]]; ?></td>				
				<td><strong>Buyer Name:</strong></td><td width="175px"><? echo $buyer; ?></td>
			</tr>
			<tr>
				<td>
					<strong>Sales Order No:</strong></td>	
					<td width="175px" colspan="3"><? echo $job; ?></td>
					<td><strong>Style Ref.:</strong></td><td width="175px"><? echo $style_ref_no; ?>		
				</td>
			</tr>
			<?
			if($print_with_vat==1)
			{
				?>
				<tr>
					<td><strong>VAT Number :</strong></td>
					<td><p>
						<?
						$vat_no=return_field_value("vat_number","lib_company","id=".$data[0],"vat_number");
						echo $vat_no;
						?></p></td>
						<td><strong>Bar Code:</strong></td><td colspan="3" id="barcode_img_id"></td>
				</tr>
				<?
			}
			else
			{
				?>
				<tr>
					<td valign="top">&nbsp;</strong></td>
					<td>&nbsp;</td>
					<td><strong>Bar Code:</strong></td><td colspan="3" id="barcode_img_id"></td>
				</tr>
				<?
			}
			?>
		</table>
		<div style="width:100%;">
			<table align="left" cellspacing="0" width="1130"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="20">SL</th>
					<th width="100">Sales Order No</th>
					<th width="50">Prog. No</th>
					<th width="130">Item Des.</th>
					<th width="50">Stich Length</th>
					<th width="40">GSM</th>
					<th width="40">Fin. Dia</th>
					<th width="80">M/C Dia</th>
					<th width="70">Color</th>
					<th width="40">Roll</th>
					<th width="70">Issue Qty</th>
					<th width="40">UOM</th>
					<th width="50">Count</th>
					<th width="40">Supplier</th>
					<th width="80">Yarn Lot</th>
					<th width="30">Rack</th>
					<th width="30">Shelf</th>
					<th width="80">Store</th>
					<th>Remarks</th>
				</thead>
				<tbody>
					<?
					$color_arr = return_library_array( "select id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
					$product_array=array();
					$product_sql = sql_select("select id, item_description, supplier_id,item_category_id,product_name_details, lot,gsm,dia_width,color,brand,unit_of_measure from product_details_master where item_category_id in(1,14)");
					foreach($product_sql as $row)
					{
						$product_array[$row[csf("id")]]['product_name_details']=$row[csf("product_name_details")];
						$product_array[$row[csf("id")]]['item_description']=$row[csf("item_description")];
						$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
						$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
						$product_array[$row[csf("id")]]['brand']=$row[csf("brand")];
						$product_array[$row[csf("id")]]['color']=$row[csf("color")];
						$product_array[$row[csf("id")]]['uom']=$row[csf("unit_of_measure")];

						if($row[csf("item_category_id")]==1)
						{
							$product_array[$row[csf("lot")].'l']['lot']=$row[csf("brand")];
							$product_array[$row[csf("lot")]]['supp']=$supplier_short_library[$row[csf("supplier_id")]];
						}
					}
					if ($db_type==0) $mc_concat="group_concat(b.machine_no_id)";
					else if ($db_type==2) $mc_concat="LISTAGG(b.machine_no_id, ',') WITHIN GROUP (ORDER BY b.machine_no_id)";
					$machine_id_arr=array();
					$sql_prod_mc_id=sql_select("select a.po_breakdown_id, b.prod_id, $mc_concat as machine_no_id from order_wise_pro_details a, pro_grey_prod_entry_dtls b where b.id=a.dtls_id and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.po_breakdown_id, b.prod_id");
					foreach($sql_prod_mc_id as $row)
					{
						$machine_id_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]=$row[csf("machine_no_id")];
					}

					$i=1; $all_program_no='';
					foreach($sql_res as $row)
					{
						if ($row[csf('is_sales')]==1)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if($row[csf('program_no')]!='')
							{
								if ($all_program_no=='') $all_program_no=$row[csf('program_no')]; else $all_program_no.=', '.$row[csf('program_no')];
							}

							$roll_qty_sum   += $row[csf('no_of_roll')];
							$issue_qnty_sum += $row[csf('issue_qnty')];

							$yarn_count = $row[csf("yarn_count")];
							$count_id = explode(',',$yarn_count);
							$count_val = "";
							foreach ($count_id as $val)
							{
								if($count_val=='') $count_val=$yarn_count_arr[$val]; 
								else $count_val.=", ".$yarn_count_arr[$val];
							}

							$yarn_lot = $row[csf("yarn_lot")];
							$yarn_lot_id = explode(',',$yarn_lot);
							$yarn_lot_supp = "";
							foreach ($yarn_lot_id as $val)
							{
								if($yarn_lot_supp=='') $yarn_lot_supp=$product_array[$val]['supp']; 
								else $yarn_lot_supp.=", ".$product_array[$val]['supp'];
							}

							$pono_id=array_unique(explode(',',$po_id));
							$mc_dia="";
							foreach($pono_id as $poId )
							{
								$mc_id_val=array_unique(explode(',',$machine_id_arr[$poId][$row[csf("prod_id")]]));
								foreach($mc_id_val as $mc_id)
								{
									if($mc_dia=="") $mc_dia=trim($mc_dia_arr[$mc_id]); else $mc_dia.=', '.trim($mc_dia_arr[$mc_id]);
								}
							}
							$mc_dia=implode(", ",array_unique(explode(', ',$mc_dia)));
							$color_ids=array_unique(explode(",",$row[csf('color_id')]));
							$color_name="";
							foreach($color_ids as $cid)
							{
								if($color_name!="") $color_name.=", ".$color_arr[$cid];else $color_name=$color_arr[$cid];
							}
							$color_names=implode(", ",array_unique(explode(", ",$color_name)));

							//if ($row[csf("is_sales")] == 1){
							if ($sales_array[$row[csf('po_breakdown_id')]]['within_group']==1)
							{
								$job_no=$sales_array[$row[csf('po_breakdown_id')]]['po_job_no'];
							}
							else
							{
								$job_no=$sales_array[$row[csf('po_breakdown_id')]]['job'];
							}	
							//}
							//else $job_no=$job_array[$row[csf('po_breakdown_id')]]['job_no'];

							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td align="center"><? echo $i; ?></td>
								<td><p><? echo $job_no; ?></p></td>
								<td><p><? if($row[csf("program_no")]==0) echo "&nbsp;"; else echo $row[csf("program_no")]; ?></p></td>
								<td><p><? echo $row[csf("product_name_details")]; ?></p></td>
								<td><p><? echo $row[csf("stitch_length")]; ?></p></td>
								<td align="center"><p><? echo $product_array[$row[csf("prod_id")]]['gsm']; ?></p></td>
								<td align="center"><p><? echo $product_array[$row[csf("prod_id")]]['dia_width']; ?></p></td>
								<td align="center" style="word-break:break-all;"><p><? echo $mc_dia; ?></p></td>
								<td style="word-break:break-all;"><p><? echo $color_names;//$color_arr[$row[csf("color_id")]]; ?></p></td>
								<td align="right"><? echo $row[csf("no_of_roll")]; ?></td>
								<td align="right"><? echo number_format($row[csf("issue_qnty")],2); ?></td>
								<td align="center"><p><? echo $unit_of_measurement[$product_array[$row[csf("prod_id")]]['uom']]; ?></p></td>
								<td align="center"><p><? echo $count_val; ?></p></td>
								<td><p><? echo $yarn_lot_supp; ?></p></td>
								<td width="80" style="word-wrap:break-word; word-break: break-all;"><p><? echo $yarn_lot; ?></p></td>
								<td align="center"><p><? echo $lib_rack_arr[$row[csf("rack")]]; ?></p></td>
								<td align="center"><p><? echo $lib_shelf_arr[$row[csf("self")]]; ?></p></td>
								<td><p><? echo $store_library[$row[csf("store_name")]]; ?></p></td>
								<td><div style="word-wrap:break-word; width:70px;"><? echo $row[csf("remarks")]; ?></div></td>
							</tr>
							<?
							$inWordTxt=$unit_of_measurement[$product_array[$row[csf("prod_id")]]['uom']];
							$i++;
						} 
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="9" align="right"><strong>Total :</strong></td>
						<td align="right"><?php echo $roll_qty_sum; ?></td>
						<td align="right"><?php echo number_format($issue_qnty_sum,2); ?></td>
						<td align="right" colspan="8"><?php //echo $req_qny_edit_sum; ?></td>
					</tr>
					<tr>
						<td colspan="19"><h4 align="center">In Words : &nbsp;<? echo number_to_words($issue_qnty_sum,$inWordTxt);?></h4></td>
					</tr>
				</tfoot>
			</table>
			<br>
			<br>&nbsp;
			<!--================================================================-->
			<?
			if($dataArray[0][csf('issue_basis')]==3)
			{
				$z=1; $k=1;
				$colorArray=sql_select("select a.id, a.mst_id, a.knitting_source, a.knitting_party, a.program_date,a. color_range, a.stitch_length, a.machine_dia, a.machine_gg, a.program_qnty, a.remarks, b.knit_id, c.booking_no, c.body_part_id, c.fabric_desc, c.gsm_weight, c.dia from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, ppl_planning_info_entry_mst c where a.mst_id= c.id and a.id=b.knit_id and b.knit_id in ($all_program_no) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0");
				$booking_no="";
				foreach ($colorArray as $book)
				{
					if ($booking_no=="") $booking_no=$book[csf('booking_no')]; else $booking_no.=','.$book[csf('booking_no')];
				}
				$booking_count=count(array_unique(explode(',',$booking_no)));
				?>

				<table style="margin-top:20px;" width="500" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
					<thead>
						<tr><th colspan="4" align="center">Comments (Booking No: <? if ($booking_count>1) echo "Multiple Booking."; else echo $colorArray[0][csf('booking_no')]; ?>)</th></tr>
						<tr>
							<th>Req. Qty</th>
							<th>Cuml. Issue Qty</th>
							<th>Balance Qty</th>
							<th>Remarks</th>
						</tr>
					</thead>
					<?
					$grey_qty=return_field_value("sum(b.grey_qty) as grey_qty","fabric_sales_order_mst a, fabric_sales_order_dtls b","a.id=b.mst_id and a.id in ($sales_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","grey_qty");		
					$total_issue_qty=return_field_value("sum(a.cons_quantity) as issue_qty"," inv_transaction a, inv_issue_master b","b.id=a.mst_id and a.requisition_no in ($all_program_no) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=2 and b.item_category=14","issue_qty");
					$total_return_qty=return_field_value("sum(a.cons_quantity) as return_qty"," inv_transaction a, inv_receive_master b","b.id=a.mst_id and b.booking_id in ($all_program_no) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=4 and b.item_category=14","return_qty");
					if ($booking_count==1)
					{
						?>
						<tbody>
							<tr>
								<td align="center">
									<? echo number_format($grey_qty,3); ?>
								</td>
								<td align="center">
									<? $cumulative_qty=$total_issue_qty-$total_return_qty; echo number_format($cumulative_qty,3); ?>
								</td>
								<td align="center">
									<? $balance_qty=$grey_qty-$cumulative_qty; echo number_format($balance_qty,3);?>
								</td>
								<td align="center">
									<? if ($grey_qty>$cumulative_qty) echo "Less"; else if ($grey_qty<$cumulative_qty) echo "Over"; else echo "";?>
								</td>
							</tr>
						</tbody>
					</table>
					<?
				}
			}
			echo signature_table(17, $data[0], "900px");
			?>
		</div>
	</div>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();		
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();

			var settings = {
				output:renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize:5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			$("#barcode_img_id").html('11');
			value = {code:value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $data[2]; ?>');
	</script>
	<?
	exit();
}

if ($action=="service_booking_popup")
{
	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		var permission="<? echo $_SESSION['page_permission']; ?>";
		function js_set_value(booking_no)
		{
			document.getElementById('selected_booking').value=booking_no;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" width="900">
					<thead>
						<tr>
							<th colspan="6">
								<?
								echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
								?>
							</th>
						</tr>
						<tr>
							<th width="160">Company Name</th>
							<th width="172">Buyer Name</th>
							<th width="120">Booking No</th>
							<th width="120">Job No</th>
							<th width="200">Date Range</th>
							<th><input type="reset" id="rst" class="formbutton" style="width:100px" onClick="reset_form('searchorderfrm_1','search_div','','','')" ></th>
						</tr>
					</thead>
					<tbody>
						<tr class="general">
							<td> <input type="hidden" id="selected_booking">
								<?
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --",  $company, "load_drop_down( 'woven_grey_fabric_issue_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
								?>
							</td>
							<td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --" ); ?>
						</td>
						<td>
							<input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes" style="width:100px"  placeholder="Write Booking No">
						</td>
						<td>
							<input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:100px" placeholder="Write Job No">
						</td>
						<td>
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('txt_job_no').value, 'create_booking_search_list_view', 'search_div', 'woven_grey_fabric_issue_controller', 'setFilterGrid(\'table_body\',-1)')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td align="center" valign="middle" colspan="6"><? echo load_month_buttons(1);  ?></td>
					</tr>
				</tbody>
			</table>
			<div id="search_div"></div>
		</form>
	</div>
	</body>
	<script>
		load_drop_down( 'woven_grey_fabric_issue_controller', <? echo $company;?>, 'load_drop_down_buyer', 'buyer_td' );
	</script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	$company_id=$data[0];
	$buyer_id=$data[1];
	$date_form=$data[2];
	$date_to=$data[3];
	$search_catgory=$data[4];
	$booking_no=$data[5];
	$job_no=$data[6];
	$sql_cond="";
	if ($company_id!=0) $sql_cond =" and a.company_id='$company_id'"; else { echo "Please Select Company First."; die; }
	if ($buyer_id!=0) $sql_cond .=" and a.buyer_id='$buyer_id'";

	if($db_type==0)
	{
		if ($date_form!="" &&  $date_to!="")  $sql_cond .= "and a.booking_date  between '".change_date_format($date_form, "yyyy-mm-dd", "-")."' and '".change_date_format($date_to, "yyyy-mm-dd", "-")."'";
	}
	if($db_type==2)
	{
		if ($date_form!="" &&  $date_to!="") $sql_cond .= "and a.booking_date  between '".change_date_format($date_form, "yyyy-mm-dd", "-",1)."' and '".change_date_format($date_to, "yyyy-mm-dd", "-",1)."'";
	}
	if($job_no!="")
	{
		if($search_catgory==1)
		{
			$sql_cond .=" and b.job_no_prefix_num='$job_no'";
		}
		else if($search_catgory==2)
		{
			$sql_cond .=" and b.job_no like '$job_no%'";
		}
		else if($search_catgory==3)
		{
			$sql_cond .=" and b.job_no like '%$job_no'";
		}
		else
		{
			$sql_cond .=" and b.job_no like '%$job_no%'";
		}
	}

	if($booking_no!="") $sql_cond .=" and a.booking_no_prefix_num=$booking_no";

	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier_arr=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$sql= "select a.id, a.process, a.booking_no_prefix_num, a.booking_no ,a.booking_date,a.delivery_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id
	from wo_booking_mst a,wo_booking_dtls b
	where a.booking_no=b.booking_no and a.booking_type=3 and b.booking_type=3 and a.entry_form=229 and a.status_active=1 and a.is_deleted=0 and a.process=31 and b.status_active=1 and b.is_deleted=0 $sql_cond group by a.id, a.process, a.booking_no_prefix_num, a.booking_no ,a.booking_date,a.delivery_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id order by a.id DESC";

	?>
	<table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" width="920" align="center">
		<thead>
			<tr>
				<th width="40">SL</th>
				<th width="50">Booking No</th>
				<th width="70">Booking Date</th>
				<th width="100">Company</th>
				<th width="100">Buyer</th>
				<th width="70">Delivery Date</th>
				<th width="120">Item Category</th>
				<th width="110">Fabric Source</th>
				<th>Supplier</th>
			</tr>
		</thead>
	</table>
	<div id="scroll_body" style="width:920px; max-height:350; overflow-y:scroll" align="center">
		<table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" width="900" id="table_body">
			<tbody>
				<?
				$sql_result=sql_select($sql);
				$i=1;
				foreach($sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf("booking_no")]; ?>')" style="cursor:pointer;">
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="50" align="center"><p><? echo $row[csf("booking_no_prefix_num")]; ?>&nbsp;</p></td>
						<td width="70" align="center"><p><? if($row[csf("booking_date")]!="" && $row[csf("booking_date")]!="0000-00-00") echo change_date_format($row[csf("booking_date")]); ?>&nbsp;</p></td>
						<td width="100"><p><? echo $comp_arr[$row[csf("company_id")]]; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $buyer_arr[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
						<td width="70" align="center"><p><? echo change_date_format($row[csf("delivery_date")]); ?>&nbsp;</p></td>

						<td width="120"><p><? echo $item_category[$row[csf("item_category")]]; ?>&nbsp;</p></td>
						<td width="110"><p><? echo $fabric_source[$row[csf("fabric_source")]]; ?>&nbsp;</p></td>
						<td><p><? echo $suplier_arr[$row[csf("supplier_id")]]; ?>&nbsp;</p></td>
					</tr>
					<?
					$i++;
				}
				?>
			</tbody>
		</table>
	</div>
	<?
	exit();
}


if ($action=="sales_requisition_popup")
{
	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		var permission="<? echo $_SESSION['page_permission']; ?>";
		function js_set_value(sys_number,id)
		{
			document.getElementById('selected_system_no').value=sys_number;
			document.getElementById('selected_system_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" width="750">
					<thead>
						<tr>
							<th colspan="6">
								<?
								echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
								?>
							</th>
						</tr>
						<tr>
							<th width="160">Company Name</th>
							<th width="120">
								<?
									if($cbo_basis == 4)
									{
										echo 'Sales Order';
									}
									else{
										echo 'Requisition';
									}
								?>
							</th>
							<th width="200">Date Range</th>
							<th><input type="reset" id="rst" class="formbutton" style="width:100px" onClick="reset_form('searchorderfrm_1','search_div','','','')" ></th>
						</tr>
					</thead>
					<tbody>
						<tr class="general">
							<td>
								<input type="hidden" id="selected_system_id">
								<input type="hidden" id="selected_system_no">
								<?
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --",  $company, "");
								?>
								<input type="hidden" name="txt_issue_basis" id="txt_issue_basis" value="<?=$cbo_basis;?>">
							</td>
						</td>
						<td>
							<input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:100px" placeholder="Write">
						</td>
						<td>
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('txt_issue_basis').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_job_no').value, 'create_sales_order_requisition_search_list_view', 'search_div', 'woven_grey_fabric_issue_controller', 'setFilterGrid(\'table_body\',-1)')" style="width:100px;" />
						</td>
						</tr>
						<tr>
							<td align="center" valign="middle" colspan="6"><? echo load_month_buttons(1);  ?></td>
						</tr>
					</tbody>
				</table>
				
			</form>
		</div>
		<div align="center" id="search_div" style="width:100%;"></div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="create_sales_order_requisition_search_list_view")
{
	$data=explode('_',$data);
	// echo "<pre>";
	// print_r($data);
	// echo "</pre>";
	$company_id=$data[0];
	$basis=$data[1];
	$date_form=$data[2];
	$date_to=$data[3];
	$search_catgory=$data[4];
	$job_no=$data[5];
	$sql_cond="";
	
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if( $basis == 4 )
	{
		if($job_no!="")
		{
			if($search_catgory==1)
			{
				$sql_cond .=" and job_no_prefix_num='$job_no'";
			}
			else if($search_catgory==2)
			{
				$sql_cond .=" and job_no like '$job_no%'";
			}
			else if($search_catgory==3)
			{
				$sql_cond .=" and job_no like '%$job_no'";
			}
			else
			{
				$sql_cond .=" and job_no like '%$job_no%'";
			}
		}
		if ($company_id!=0) $sql_cond =" and company_id='$company_id'"; else { echo "Please Select Company First."; die; }

		if($db_type==0)
		{
			if ($date_form!="" &&  $date_to!="")  $sql_cond .= "and booking_date  between '".change_date_format($date_form, "yyyy-mm-dd", "-")."' and '".change_date_format($date_to, "yyyy-mm-dd", "-")."'";
		}
		if($db_type==2)
		{
			if ($date_form!="" &&  $date_to!="") $sql_cond .= "and booking_date  between '".change_date_format($date_form, "yyyy-mm-dd", "-",1)."' and '".change_date_format($date_to, "yyyy-mm-dd", "-",1)."'";
		}

		$sql = "SELECT id, job_no as system_no, sales_booking_no, booking_date as sys_date, buyer_id, style_ref_no,company_id from fabric_sales_order_mst where entry_form=547 and status_active=1 and is_deleted=0 $sql_cond  order by id desc";
	}
	else if( $basis == 5 )
	{
		if($job_no!="")
		{
			if($search_catgory==1)
			{
				$sql_cond .=" and a.reqn_number_prefix_num='$job_no'";
			}
			else if($search_catgory==2)
			{
				$sql_cond .=" and a.reqn_number like '$job_no%'";
			}
			else if($search_catgory==3)
			{
				$sql_cond .=" and a.reqn_number like '%$job_no'";
			}
			else
			{
				$sql_cond .=" and a.reqn_number like '%$job_no%'";
			}
		}
		if ($company_id!=0) $sql_cond =" and a.company_id='$company_id'"; else { echo "Please Select Company First."; die; }

		if($db_type==0)
		{
			if ($date_form!="" &&  $date_to!="")  $sql_cond .= "and a.reqn_date  between '".change_date_format($date_form, "yyyy-mm-dd", "-")."' and '".change_date_format($date_to, "yyyy-mm-dd", "-")."'";
		}
		if($db_type==2)
		{
			if ($date_form!="" &&  $date_to!="") $sql_cond .= "and a.reqn_date  between '".change_date_format($date_form, "yyyy-mm-dd", "-",1)."' and '".change_date_format($date_to, "yyyy-mm-dd", "-",1)."'";
		}

		$sql ="SELECT a.id, a.reqn_number as system_no,  a.reqn_date as sys_date,a.company_id  from pro_fab_reqn_for_batch_woven_mst a, pro_fab_reqn_for_batch_woven_dtls b
		where a.id=b.mst_id and b.entry_form is null and a.status_active=1 and a.is_deleted=0 $sql_cond  group by a.id, a.insert_date,  a.reqn_number, a.reqn_date,a.company_id 
		order by a.id";
	}
	
	//echo $sql;
	$result = sql_select($sql);
	$width = 730 ;
	if( $basis == 5 )
	{
		$width = 600 ;
	}
	?>
	<table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" width="<?=$width;?>" align="center">
		<thead>
			<tr>
				<th width="40">SL</th>
				<th width="150">Company</th>
				<?php if ($basis == 4): ?>
					<th width="150">Buyer</th>
					<th width="150">Booking No</th>
				<?php endif ?>
				
				<th width="150">
					<?php
					if( $basis == 4 )
					{
						echo "Sales Order No";
					}
					else if( $basis == 4 )
					{
						echo "Requisition No";
					}

					?>
					
				</th>
				<th >
					<?php
					if( $basis == 4 )
					{
						echo "Booking Date";
					}
					else if( $basis == 4 )
					{
						echo "Requisition Date";
					}

					?>
				</th>
				
			</tr>
		</thead>
	</table>
	<div id="scroll_body" style="width:<?=$width+23;?>px; max-height:350; overflow-y:scroll" align="center">
		<table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" width="<?=$width;?>" id="table_body">
			<tbody>
				<?
				$sql_result=sql_select($sql);
				$i=1;
				foreach($sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf("system_no")]; ?>','<? echo $row[csf("id")]; ?>')" style="cursor:pointer;">
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="150"><p><? echo $comp_arr[$row[csf("company_id")]]; ?>&nbsp;</p></td>
						<?php if ($basis == 4): ?>
							<td width="150"><p><? echo $buyer_arr[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
							<td width="150"><p><? echo $row[csf("sales_booking_no")]; ?>&nbsp;</p></td>
						<?php endif ?>
						
						<td width="150"><p><? echo $row[csf("system_no")]; ?>&nbsp;</p></td>
						<td  align="center"><p><? if($row[csf("sys_date")]!="" && $row[csf("sys_date")]!="0000-00-00") echo change_date_format($row[csf("sys_date")]); ?>&nbsp;</p></td>
					</tr>
					<?
					$i++;
				}
				?>
			</tbody>
		</table>
	</div>
	<?
	exit();
}

if ($action == "show_color_listview")
{ 
	$data = explode("**", $data);
	$issue_basis = $data[0];
	$cbo_company_id = $data[1];
	$system_id = $data[2];
	$system_no = $data[3];

	if($issue_basis == 4)
	{
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="400" class="rpt_table">
			<caption>Fabric List View</caption>
			<thead>
				<th width="25">SL</th>
				<th width="150">Fabric Description</th>
				<th width="70">S. Order Qnty</th>
				<th width="70">Cu. Issue Qnty</th>
				<th>Stock Qnty</th>
			</thead>
			<?
			$i = 1;
			

			
			$sql = "SELECT  sum(a.grey_qty) as qnty,listagg(distinct a.id , ',' on overflow truncate with count) within group (order by a.id) as po_id,listagg(distinct a.color_id , ',' on overflow truncate with count) within group (order by a.color_id) as color_id,a.fabric_desc from fabric_sales_order_dtls a,fabric_sales_order_mst b where a.job_no_mst = b.job_no and a.mst_id = b.id and a.job_no_mst='$system_no' and b.entry_form = 547 and a.status_active=1 group by a.fabric_desc";
			//echo $sql;	
			$req_res = sql_select($sql);
			$order_ids = array();
			foreach ($req_res as $row)
			{
				$po_ids = explode(",",$row[csf('po_id')]);
				foreach( $po_ids as $po_id)
				{
					$order_ids[$po_id] = $po_id;
				}
			}
			$order_cond = where_con_using_array($order_ids,0,"c.po_breakdown_id");

			$issue_sql = "SELECT b.issue_qnty, c.po_breakdown_id, d.item_description FROM inv_issue_master a,inv_grey_fabric_issue_dtls b,product_details_master d,inv_transaction e,order_wise_pro_details c WHERE     a.id = b.mst_id AND b.prod_id = d.id  AND a.entry_form = 577  AND b.trans_id = e.id  AND b.status_active = 1  AND b.is_deleted = 0  AND c.trans_id = e.id  AND c.dtls_id = b.id and c.is_deleted =0 and c.status_active=1 $order_cond";
			//echo $sql;
			$issue_sql = sql_select($issue_sql);

			$issue_data = array();

			foreach ($issue_sql as $row)
			{
				$issue_qnty_array[$row[csf('item_description')]][$row[csf('po_breakdown_id')]]+= $row[csf('issue_qnty')];
			}

			foreach ($req_res as $row)
			{
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				$issue_qnty = 0;
				$po_ids = explode(",",$row[csf('po_id')]);
				foreach( $po_ids as $po_id)
				{
					$issue_qnty+= $issue_qnty_array[$row[csf('fabric_desc')]][$po_id];
				}
				

				$balance = $row[csf('qnty')] - $issue_qnty;
				$data =  $row[csf('fabric_desc')]. "**". $row[csf('qnty')] ."**". $balance;
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
					onClick="put_country_data('<? echo $row[csf('color_id')]; ?>','<? echo $row[csf('po_id')]; ?>','<? echo $data; ?>')">
					<td width="25"><? echo $i; ?></td>
					<td width="150"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
					<td width="70" align="right"><? echo number_format($row[csf('qnty')], 2,".",""); ?></td>
					<td width="70" align="right"><? echo number_format($issue_qnty, 2,".",""); ?></td>
					<td align="right"><? echo number_format($balance, 2,".",""); ?></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
		<?
	}
	else if($issue_basis == 5)
	{
		$sql_req = "SELECT sum(b.reqn_qty) as reqn_qty,listagg(distinct b.po_id , ',' on overflow truncate with count) within group (order by b.po_id) as po_id,listagg(distinct b.color_id , ',' on overflow truncate with count) within group (order by b.color_id) as color_id,b.item_description FROM pro_fab_reqn_for_batch_woven_mst a ,pro_fab_reqn_for_batch_woven_dtls b  where a.id = b.mst_id and  a.is_deleted =0 and a.status_active =1 and  b.is_deleted =0 and b.status_active =1 and a.id = $system_id group by b.item_description";
		//echo $sql_req;die;
		$req_res = sql_select($sql_req);
		$order_ids = array();
		foreach ($req_res as $row)
		{
			$po_ids = explode(",",$row[csf('po_id')]);
			foreach( $po_ids as $po_id)
			{
				$order_ids[$po_id] = $po_id;
			}
		}
		$order_cond = where_con_using_array($order_ids,0,"c.po_breakdown_id");

		$issue_sql = "SELECT b.issue_qnty, c.po_breakdown_id, d.item_description FROM inv_issue_master a,inv_grey_fabric_issue_dtls b,product_details_master d,inv_transaction e,order_wise_pro_details c WHERE     a.id = b.mst_id AND b.prod_id = d.id  AND a.entry_form = 577  AND b.trans_id = e.id  AND b.status_active = 1  AND b.is_deleted = 0  AND c.trans_id = e.id  AND c.dtls_id = b.id and c.is_deleted =0 and c.status_active=1 $order_cond";
			//echo $sql;
		$issue_sql = sql_select($issue_sql);

		$issue_data = array();

		foreach ($issue_sql as $row)
		{
			$issue_qnty_array[$row[csf('item_description')]][$row[csf('po_breakdown_id')]]+= $row[csf('issue_qnty')];
		}
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="400" class="rpt_table">
			<caption>Fabric List View</caption>
			<thead>
				<th width="25">SL</th>
				<th width="150">Fabric Description</th>
				<th width="70">Req. Qnty</th>
				<th width="70">Cu. Issue Qnty</th>
				<th >Stock Qnty</th>
			</thead>
			<?
			$i = 1;
			
			foreach($req_res as $row)
			{
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
		
				$issue_qnty = 0;
				$po_ids = explode(",",$row[csf('po_id')]);
				foreach( $po_ids as $po_id)
				{
					$issue_qnty+= $issue_qnty_array[$row[csf('item_description')]][$po_id];
				}

				$balance = $row[csf('reqn_qty')] - $issue_qnty;
				$data =  $row[csf('item_description')]. "**". $row[csf('reqn_qty')] ."**". $balance;
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
					onClick="put_country_data('<? echo $row[csf('color_id')]; ?>','<? echo $row[csf('po_id')]; ?>','<? echo $data; ?>')">
					<td width="25"><? echo $i; ?></td>
					
					<td width="150"><p><? echo trim($row[csf('item_description')]); ?></p></td>
					<td width="70" align="right"><? echo number_format($row[csf('reqn_qty')], 2,".",""); ?></td>
					<td width="70" align="right"><? echo number_format($issue_qnty, 2,".",""); ?></td>
					<td align="right"><? echo number_format($balance, 2,".",""); ?></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
		<?
	}
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
}


if($action=="multiple_issue_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style )
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
	
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ )
			{
				var attrData=$('#tr_' +i).attr('onclick');
				var splitArr = attrData.split("'");
				js_set_value( splitArr[1] );
			}
		}
	
		var selected_id=Array();
		var selected_name=Array();
		function js_set_value(mrr)
		{
			var splitArr = mrr.split("_");
			//$("#hidden_return_number").val(splitArr[1]); // mrr number
			$("#hnd_issue_id").val(splitArr[1]); // id
			toggle( document.getElementById( 'tr_' + splitArr[0] ), '#FFFFCC' );
	
			if( jQuery.inArray(splitArr[1], selected_id ) == -1 )
			{			
				//selected_name.push(splitArr[1]);
				selected_id.push( splitArr[1]);
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == splitArr[1]) break;
				} 			
				//selected_name.splice( i, 1 );
				selected_id.splice( i, 1 );
			}
	
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + ',';
				//name += selected_name[i] + ',';
			}
	
			id = id.substr( 0, id.length - 1 );
			//name = name.substr( 0, name.length - 1 );
	
			$('#hnd_issue_id').val(id);
			//$('#hidden_return_number').val(name);
		}
 	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="950" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<tr>
							<th width="120" class="must_entry_caption">LC Company</th>
							<th width="120" class="must_entry_caption">Issue To</th>
							<th width="150">Issue No</th>
							<th width="100">Challan No</th>
							<th width="220">Date Range</th>
							<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								echo create_drop_down("cbo_lc_company", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $company, "");
								?>
							</td>
                            <td>
                            <?
								if($company=="" || $company==0) $company_cond2 = ""; else $company_cond2 = "and c.tag_company=$company";
						
							if($dyeing_source==0 || $dyeing_source=="")
							{
								echo create_drop_down( "cbo_issue_to", 170, $blank_array,"", 1, "-- Select --", 0, "",0 );
							}
							else if($dyeing_source==1)
							{
								echo create_drop_down( "cbo_issue_to", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $company, "" );
							}
							else if($dyeing_source==3 && ($issue_purpose==4 || $issue_purpose==8 || $issue_purpose==11))
							{
								echo create_drop_down( "cbo_issue_to", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and b.supplier_id=c.supplier_id and b.party_type in(21,24,25,26) and a.status_active=1 $company_cond2 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
							}
							else
							{
								echo create_drop_down( "cbo_issue_to", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and b.supplier_id=c.supplier_id and a.status_active=1 $company_cond2 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
							}
							?>
                            </td>
							<td>
								<input type="text" style="width:150px" class="text_boxes"  name="text_issue_no" id="text_issue_no" />
							</td>
							<td>
								<input type="text" style="width:100px" class="text_boxes"  name="text_challan_no" id="text_challan_no" />
							</td>
							<td align="center">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />&nbsp;&nbsp;&nbsp;
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_lc_company').value+'_'+document.getElementById('cbo_issue_to').value+'_'+document.getElementById('text_issue_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('text_challan_no').value, 'multiple_issue_no_listview', 'search_div', 'woven_grey_fabric_issue_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td align="center" height="40" valign="middle" colspan="6">
								<? echo load_month_buttons(1);  ?>
							</td>
						</tr>
					</tbody>
				</tr>
			</table>
			<div align="center" style="margin-top:10px" valign="top" id="search_div"> </div>
		</form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="multiple_issue_no_listview")
{
	echo '<input type="hidden" id="hnd_issue_id" value="" />';
	$ex_data = explode("_",$data);
	$company 	   = $ex_data[0];
	$issue_to 	   = $ex_data[1];
	$issue_no 	   = $ex_data[2];
	$txt_date_from = $ex_data[3];
	$txt_date_to   = $ex_data[4];
	$cbo_year 	   = $ex_data[5];
	$challan_no    = $ex_data[6];
	
	//for company
	if($company == 0)
	{
		echo "<p style='font-size:25px; color:#F00'>Please Select LC Company.</p>";
		die;
	}
	else
	{
		$company_cond = " and a.company_id = ".$company;
	}
	
	//for issue to
	$issue_to_cond = '';
	if($issue_to == 0)
	{
		echo "<p style='font-size:25px; color:#F00'>Please Select Issue To</p>";
		die;
	}
	else
	{
		$issue_to_cond = " and a.knit_dye_company = ".$issue_to;
	}
	// if($issue_to != 0)
	// {
	// 	$issue_to_cond = " and a.knit_dye_company = ".$issue_to;
	// }
	
	//for issue no
	$issue_no_cond = '';
	if($issue_to != 0)
	{
		$issue_no_cond = " and a.issue_number like '%".trim($issue_no)."'";
	}

	//for issue no
	$challan_no_cond = '';
	if($challan_no != '')
	{
		$challan_no_cond = " and a.challan_no like '%".trim($challan_no)."'";
	}
	
	
	//for date
	$date_cond = '';
	if( $txt_date_from != '' && $txt_date_to != '' )
	{
		$date_cond = " and a.issue_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
	}
	else
	{
		if (trim($cbo_year) != 0)
		{
			$date_cond = " and to_char(a.insert_date,'YYYY') = ".$cbo_year;
		}
	}
	
	$year_field = "to_char(a.insert_date,'YYYY') as year,";
	$sql = "select a.id, a.issue_number_prefix_num, a.issue_number, a.issue_basis, a.issue_purpose, a.entry_form, a.item_category, a.company_id, a.location_id, a.supplier_id, a.store_id, a.buyer_id, a.buyer_job_no, a.style_ref, a.booking_id, a.booking_no, a.issue_date, a.sample_type, a.knit_dye_source, a.knit_dye_company, a.challan_no, a.loan_party, a.lap_dip_no, a.gate_pass_no, a.item_color, a.color_range, a.remarks, a.ready_to_approve, a.loan_party,a.is_posted_account , $year_field a.is_approved,sum(b.cons_quantity) issue_quantity 
	from inv_issue_master a,inv_transaction b 
	where a.id=b.mst_id and b.transaction_type=2 and b.item_category=14 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=577".$company_cond.$issue_to_cond.$issue_no_cond.$date_cond.$challan_no_cond."
	group by a.id, a.issue_number_prefix_num, a.issue_number, a.issue_basis, a.issue_purpose, a.entry_form, a.item_category, a.company_id, a.location_id, a.supplier_id, a.store_id, a.buyer_id, a.buyer_job_no, a.style_ref, a.booking_id, a.booking_no, a.issue_date, a.sample_type, a.knit_dye_source, a.knit_dye_company, a.challan_no, a.loan_party, a.lap_dip_no, a.gate_pass_no, a.item_color, a.color_range, a.remarks, a.ready_to_approve, a.loan_party,a.insert_date,a.is_posted_account,a.is_approved order by a.issue_number";
	//echo $sql; //die;
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	//$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$arr=array(1=>$company_arr);
	echo create_list_view("list_view", "Year, LC Company, Issue No, Issue Date, Issue Qty","60,150,100,70,100","530","230",0, $sql , "js_set_value", "id", "1", 1, "0,company_id,0,0,0", $arr, "year,company_id,issue_number,issue_date,issue_quantity","","","0,0,0,3,1","",1);
	exit();
}

//for multiple_issue_no_print
if ($action=="multiple_issue_no_print")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$issue_id = $data[0];
	$buyer_arrs = return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');	
	$sql = "select id, issue_number, issue_basis, location_id, buyer_job_no, booking_id, booking_no, loan_party, knit_dye_source, challan_no, issue_purpose, buyer_id, issue_date, knit_dye_company, gate_pass_no, remarks from inv_issue_master where id in($issue_id)";
	//echo $sql;die;

	$dataArray = sql_select($sql);

	$sql_issue = "SELECT a.id,a.company_id, a.issue_number, a.issue_basis, a.issue_purpose, a.issue_date, a.booking_id, a.booking_no, 
	a.knit_dye_company, a.knit_dye_source, a.buyer_id,
	b.id as trans_id, b.requisition_no, b.pi_wo_batch_no, b.prod_id, b.transaction_date, b.store_id, b.floor_id, b.room, sum(b.cons_quantity) as issue_qnty, d.remarks, 
	c.detarmination_id, d.yarn_lot as lot, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_type2nd, c.yarn_comp_percent1st, c.yarn_type, c.color, c.supplier_id, b.booking_without_order, a.service_booking_no, a.style_ref, d.color_id, d.stitch_length, d.yarn_count, d.no_of_roll, a.challan_no
	FROM inv_issue_master a, inv_transaction b, product_details_master c, inv_grey_fabric_issue_dtls d
	WHERE a.id=d.mst_id and a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id 
	and a.entry_form=577 and a.item_category=14 and a.id in(".$issue_id.") and a.status_active=1 and a.is_deleted=0 
	and b.item_category=14 and b.transaction_type=2
	and b.status_active=1 and b.is_deleted=0 
	and c.status_active=1 and c.is_deleted=0 
	GROUP BY a.id, a.company_id, a.issue_number, a.issue_basis, a.issue_purpose, a.issue_date, a.booking_id, a.booking_no, 
	a.knit_dye_company, a.knit_dye_source, a.buyer_id, 
	b.id, b.requisition_no, b.pi_wo_batch_no, b.prod_id, b.transaction_date, b.store_id, b.floor_id, b.room, d.remarks, 
	c.detarmination_id, d.yarn_lot, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_type2nd, c.yarn_comp_percent1st, c.yarn_type, c.color, c.supplier_id, b.booking_without_order, a.service_booking_no, a.style_ref, d.color_id, d.stitch_length, d.yarn_count, d.no_of_roll, a.challan_no order by a.issue_number ASC";
	//echo $sql_issue; //
	$sql_issue_rslt = sql_select($sql_issue);
	$pdata = array();
	$trans_id = '';	
	$requisition_no = '';	
	$progNo = '';	
	$supplier_id = '';	
	$store_id = '';	
	$yarn_count = '';	
	$color = '';
	$challan_no = '';
	$company = 0;	
	$knit_dye_company = 0;	
	$knit_dye_source = 0;	
	$prod_id_arr=array();
	foreach($sql_issue_rslt as $row)
	{
		$company = $row[csf('company_id')];
		$knit_dye_company = $row[csf('knit_dye_company')];
		$knit_dye_source = $row[csf('knit_dye_source')];
		
		$trans_id .= $row[csf('trans_id')].",";
		$supplier_id .= $row[csf('supplier_id')].",";
		$store_id .= $row[csf('store_id')].",";
		$yarn_count .= $row[csf('yarn_count')].","; 
		$color .= $row[csf('color_id')].","; 
		$challan_no .= $row[csf('challan_no')].","; 

		if($row[csf('issue_basis')] == 3)
		{
			$requisition_no .= $row[csf('requisition_no')].",";	
		}
		if($row[csf('issue_basis')] == 3 && $row[csf('issue_purpose')] == 8)
		{
			$progNo .= $row[csf('requisition_no')].",";	
		}

		if($row[csf('issue_basis')] == 1)
		{
			$booking_id .= $row[csf('booking_id')].",";	
		}
		array_push($prod_id_arr,$row[csf('prod_id')]);
	}
	
	$store_ids = implode(",",array_unique(explode(",",chop($store_id,',')))); 
	$supplier_ids = implode(",",array_unique(explode(",",chop($supplier_id,',')))); 
	$yarn_count_ids = implode(",",array_unique(explode(",",chop($yarn_count,',')))); 
	$colorids = implode(",",array_unique(explode(",",chop($color,','))));
	$trans_ids = implode(",",array_unique(explode(",",chop($trans_id,',')))); 
	$requisition_numbers = implode(",",array_unique(explode(",",chop($requisition_no,','))));
	$program_numbers = implode(",",array_unique(explode(",",chop($progNo,','))));  
	$booking_ids = implode(",",array_unique(explode(",",chop($booking_id,',')))); 
	$challan_no = implode(",",array_unique(explode(",",chop($challan_no,',')))); 
	//var_dump($booking_numbers);
	//for company
	$company_library = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
	//for buyer
	$sqlBuyer = sql_select("select id as ID, buyer_name as BUYER_NAME, short_name as SHORT_NAME from lib_buyer");
	foreach($sqlBuyer as $row)
	{
		$buyer_arr[$row['ID']] = $row['SHORT_NAME'];
		$buyer_dtls_arr[$row['ID']] = $row['BUYER_NAME'];
	}
	unset($sqlBuyer);		
	//for store
	/* if($store_ids!="")
	{
		$store_library = return_library_array("select id, store_name from lib_store_location where status_active=1 and is_deleted=0 and id in(".$store_ids.")", "id", "store_name");
	} */

	$store_library = return_library_array("select id, store_name from lib_store_location where status_active=1 and is_deleted=0 ", "id", "store_name");
			
	//for supplier
	/* if($supplier_ids!="")
	{
		$supplier_library = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and id in ($supplier_ids)", "id", "supplier_name");
	} */

	$supplier_library = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0", "id", "supplier_name");
	
	//for yarn count
/* 	if($yarn_count_ids!="")
	{
		$yarn_count_arr = return_library_array("select id,yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 and id in ($yarn_count_ids)", "id", "yarn_count");
	} */

	$yarn_count_arr = return_library_array("select id,yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 ", "id", "yarn_count");
	
	//for color
	// if($colorids!="")
	// {
		$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0 ", "id", "color_name");
	//}

	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", 'floor_room_rack_id', 'floor_room_rack_name');

	if($booking_ids!="")
	{
		$sample_sql= "SELECT a.id, a.company_id, c.style_ref_no, c.internal_ref from wo_non_ord_samp_booking_mst  a left join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no  left join sample_development_mst c on b.style_id=c.id and b.status_active=1 and b.is_deleted=0  where a.id in(".$booking_ids.") and a.entry_form_id=140 and  b.entry_form_id=140 and   a.company_id='$company'". set_user_lavel_filtering(' and a.buyer_id','buyer_id')."  and a.booking_type=4 and  a.status_active=1 and a.is_deleted=0  order by b.id desc";
		//echo $sample_sql;
		$sample_sql_Array=sql_select($sample_sql);
		if(count($sample_sql_Array)>0) 
		{
			$sample_alldata_arr = array();
			foreach ($sample_sql_Array as $row) 
			{
				$sample_alldata_arr[$row[csf("id")]]['style_ref_no'] = $row[csf("style_ref_no")];				
				$sample_alldata_arr[$row[csf("id")]]['internal_ref'] = $row[csf("internal_ref")];				
			}
			//var_dump($sample_alldata_arr);
		}
	}

	if($trans_ids!="")
	{
		$job_sql="SELECT a.trans_id, a.po_breakdown_id,c.buyer_name, c.job_no, c.style_ref_no, b.grouping, b.po_number from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c where a.trans_id in(".$trans_ids.") and a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and b.status_active=1 and b.is_deleted=0 and c.is_deleted=0 and c.status_active=1 group by a.trans_id, a.po_breakdown_id, c.job_no, c.style_ref_no, c.buyer_name, b.grouping, b.po_number order by c.job_no, a.po_breakdown_id";
		//echo $job_sql;
		$job_result_Array=sql_select($job_sql);
		if(count($job_result_Array)>0) 
		{
			$job_alldata_arr = array();
			foreach($job_result_Array as $row)
			{
				$tot_rows++;				
				$job_alldata_arr[$row[csf("trans_id")]]['job_no']   	= $row[csf("job_no")];				
				$job_alldata_arr[$row[csf("trans_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
				$job_alldata_arr[$row[csf("trans_id")]]['buyer_name']   = $row[csf("buyer_name")];
				$job_alldata_arr[$row[csf("trans_id")]]['order_no']     = $row[csf("po_number")];

				if(!empty($job_alldata_arr[$row[csf("trans_id")]]['grouping']))
				{
					$job_alldata_arr[$row[csf("trans_id")]]['grouping'] .= ",".$row[csf("grouping")];
				}
				else
				{
					$job_alldata_arr[$row[csf("trans_id")]]['grouping'] = $row[csf("grouping")];
				}				
				$buyer_name.=$row[csf("buyer_name")].",";
			}
			unset($job_result_Array);	

			$buyer_ids = implode(",",array_unique(explode(",",chop($buyer_name,',')))); 
			if($buyer_ids!="")
			{
				$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0 and id in($buyer_ids)",'id','buyer_name');	
			}	
		}
	}

	if($requisition_numbers !="" )
	{
		$requsition_sql = "select a.knit_id as program_no, a.requisition_no from ppl_yarn_requisition_entry a where a.status_active=1 and a.is_deleted=0 and a.requisition_no in(".$requisition_numbers.") group by a.knit_id, a.requisition_no";
		$requsition_result_array=sql_select($requsition_sql);
		$requisition_data = array();
		foreach ($requsition_result_array as $row) 
		{
			$requisition_data[$row[csf("requisition_no")]]['program_no'] = $row[csf("program_no")];
		}
	}
	if($program_numbers !="" )
	{
		$prog_sql = "select a.dtls_id as program_no, a.buyer_id,d.style_ref_no,x.machine_dia from ppl_planning_entry_plan_dtls a,PPL_PLANNING_INFO_ENTRY_DTLS x,sample_development_yarn_dtls b,sample_development_dtls c,sample_development_mst d where a.mst_id=x.mst_id and x.id=a.dtls_id and  a.booking_no=b.booking_no  and b.mst_id=c.sample_mst_id and c.sample_mst_id=d.id and a.status_active=1 and a.is_deleted=0 and a.dtls_id in(".$program_numbers.") group by a.dtls_id , a.buyer_id,d.style_ref_no ,x.machine_dia ";
		$prog_result_array=sql_select($prog_sql);
		$program_data_arr = array();
		foreach ($prog_result_array as $row) 
		{
			$program_data_arr[$row[csf("program_no")]]['buyer_id'] = $row[csf("buyer_id")];
			$program_data_arr[$row[csf("program_no")]]['style_ref_no'] = $row[csf("style_ref_no")];
			$program_mc_dia_data_arr[$row[csf("program_no")]]['machine_dia'].= $row[csf("machine_dia")].",";
		}
		/*echo "<pre>";
		print_r($program_data_arr);
		echo "</pre>";*/
	}
	
	if($dataArray[0][csf('issue_purpose')]==8)
	{

		if($db_type==0)
		{
			$prodID=return_field_value("group_concat(b.prod_id) as prod_id","inv_issue_master c, inv_grey_fabric_issue_dtls a, pro_grey_prod_entry_dtls b","c.id=a.mst_id and a.prod_id=b.prod_id and a.mst_id ($issue_id) and c.entry_form=577 and c.issue_basis=1 and c.issue_purpose=8 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0","prod_id");
		}
		else
		{
			$prodID=return_field_value("LISTAGG(b.prod_id, ',') WITHIN GROUP (ORDER BY b.prod_id)  as prod_id","inv_issue_master c, inv_grey_fabric_issue_dtls a, pro_grey_prod_entry_dtls b","c.id=a.mst_id and a.prod_id=b.prod_id and a.mst_id in($issue_id) and c.entry_form=577 and c.issue_basis=1 and c.issue_purpose=8 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0","prod_id");
		}
	}
	else
	{
		if($db_type==0)
		{
			$po_id=return_field_value("group_concat(b.po_breakdown_id) as po_id","inv_grey_fabric_issue_dtls a, order_wise_pro_details b","a.id=b.dtls_id and a.mst_id in ($issue_id) and b.entry_form=577 and b.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","po_id");
		}
		else
		{
			/* echo "select LISTAGG(b.po_breakdown_id, ',') WITHIN GROUP (ORDER BY b.po_breakdown_id) as po_id from inv_grey_fabric_issue_dtls a, order_wise_pro_details b where a.id=b.dtls_id and a.mst_id='$issue_id' and b.entry_form=577 and b.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; */

			$po_id=return_field_value("LISTAGG(b.po_breakdown_id, ',') WITHIN GROUP (ORDER BY b.po_breakdown_id) as po_id","inv_grey_fabric_issue_dtls a, order_wise_pro_details b","a.id=b.dtls_id and a.mst_id in($issue_id) and b.entry_form=577 and b.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","po_id");
		}
	}
	
	$product_sql = sql_select("select id, item_description, supplier_id,item_category_id,product_name_details, lot,gsm,dia_width,color,brand,unit_of_measure from product_details_master where item_category_id in(1,14) ".where_con_using_array($prod_id_arr, '0', 'id')."");
	$product_array=array();
	foreach($product_sql as $row)
	{
		$product_array[$row[csf("id")]]['item_description']=$row[csf("item_description")];
		$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
	}

	$machine_dia_arr=array();
	$machine_id_arr_non_order=array();
	if($dataArray[0][csf('issue_purpose')]==8)
	{
		if ($db_type==0) $mc_concat="group_concat(b.machine_dia)";
		else if ($db_type==2) $mc_concat="LISTAGG(b.machine_dia, ',') WITHIN GROUP (ORDER BY b.machine_dia)";
		$sql_prod_mc_id=sql_select("select b.prod_id, $mc_concat as machine_dia from  pro_grey_prod_entry_dtls b where  b.status_active=1 and b.is_deleted=0 and b.prod_id in($prodID) group by  b.prod_id");
	}
	else
	{
		if ($db_type==0) $mc_concat="group_concat(b.machine_no_id)";
		else if ($db_type==2) $mc_concat="LISTAGG(b.machine_no_id, ',') WITHIN GROUP (ORDER BY b.machine_no_id)";
		$sql_prod_mc_id=sql_select("select a.po_breakdown_id, b.prod_id, $mc_concat as machine_no_id,b.machine_dia from order_wise_pro_details a, pro_grey_prod_entry_dtls b where b.id=a.dtls_id and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.po_breakdown_id, b.prod_id,b.machine_dia");
	}
	foreach($sql_prod_mc_id as $row)
	{
		$machine_dia_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]=$row[csf("machine_dia")];
		$machine_id_arr_non_order[$row[csf("prod_id")]]=$row[csf("machine_dia")];
	}
	// echo "<pre>";
	// print_r($machine_id_arr);die;
	
	

	?>
	<div style="width:1600px;">
		<table cellspacing="0" border="0" width="100%">
				<tr>
					<td colspan="17" align="center" >
						<strong style="font-size:xx-large;"><? echo $company_library[$company]; ?></strong>
						<br><? echo chop(show_company($company, '', ''),","); ?>
					</td>
				
				</tr>
				<tr>
					<td colspan="17" align="center"> 
					<? 
					if($knit_dye_source == 1)
					{
						$working_com_location = show_company($knit_dye_company, '', '');
						$working_company_name = $company_library[$knit_dye_company]; 
					}
					else
					{
						$working_company_name =  return_field_value("supplier_name", "lib_supplier", "id=".$knit_dye_company,"supplier_name");
						$working_com_location = return_field_value("address_1", "lib_supplier", "id=".$knit_dye_company,"address_1");
					}

				
					?>
					<p style="font-size:20px;"><strong>Issue To: <? echo $working_company_name; ?></strong></p>
					</td>
				</tr>
				<tr>
					<td colspan="17" align="center"> <? echo chop($working_com_location,","); ?></td>
				</tr>		
				<tr class="form_caption">
					<td colspan="17" style="font-size:x-large" align="center"><strong><u>Knit Grey Issue Challan</u></strong></td>
					
				</tr>
				<tr>
					<td colspan="17" style="font-size:x-large" align="center">Challan no: <? echo trim($challan_no,',');?></td>
				</tr>
                <tr>
					<td colspan="15">&nbsp;</td>
					<td colspan="2" style="padding-right:119px;" align="right"> Print Date : <? echo date('d-m-Y'); ?></td>
				</tr>			
		</table>
		<div style="width:100%;">
			<table align="center" cellspacing="0" border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="30" align="center">SL</th>
					<th width="100">Buyer</th>
					<th width="130">Job no</th>
					<th width="100">S.booking no</th>
					<th width="80">style</th>
					<th width="100">order</th>
					<th width="100">Item Description</th>
					<th width="60">f/gsm</th>
					<th width="60">s/l</th>
					<th width="60">m/dia</th>
					<th width="60">f/dia</th>
					<th width="60">y.count</th>
					<th width="80">y.lot</th>
					<th width="80" >color</th>
					<th width="80" align=="right">roll</th>
					<th width="80" align=="right">Issue Qnty(kgs)</th>
					<th>Remarks</th>
				</thead>
				<?
				$i = 1;
				foreach ($sql_issue_rslt as $row)
				{
					$bgcolor =($i%2==0)?"#E9F3FF":"#FFFFFF";

					$yarn_count=$row[csf("yarn_count")];
					$count_id=explode(',',$yarn_count);
					$count_val='';
					foreach ($count_id as $val)
					{
						if($count_val=='') $count_val=$yarn_count_arr[$val]; else $count_val.=", ".$yarn_count_arr[$val];
					}

					$buyer_name = "";

					if($job_alldata_arr[$row[csf("trans_id")]]['buyer_name'] !="")
					{
						$buyer_name = $buyer_arr[$job_alldata_arr[$row[csf("trans_id")]]['buyer_name']];
					}
					else if($row[csf('issue_basis')] == 3 && $row[csf('issue_purpose')]==8)
					{
						$buyer_name =$buyer_arrs[$program_data_arr[$row[csf("requisition_no")]]['buyer_id']];
						$styelRefs =$program_data_arr[$row[csf("requisition_no")]]['style_ref_no'];
						$mc_dia_nonOrdSample =$program_mc_dia_data_arr[$row[csf("requisition_no")]]['machine_dia'];
					}
					else
					{
						if($row[csf('issue_basis')] == 1)
						{
							if ($dataArray[0][csf('issue_purpose')] == 8 )
							{	
								$buyer_name =$buyer_arr[$row[csf('buyer_id')]];
							}
						}
					}

					$color_ids=array_unique(explode(",",$row[csf('color_id')]));
					$color_name="";
					foreach($color_ids as $cid)
					{
						if($color_name!="") $color_name.=", ".$color_arr[$cid];else $color_name=$color_arr[$cid];
					}
					$color_names=implode(", ",array_unique(explode(", ",$color_name)));

					if($row[csf("issue_purpose")]==8)
					{
						$prod_id_ex=array_unique(explode(',',$prodID));
						$mc_dia="";
						foreach($prod_id_ex as $prodIDs )
						{
							$mc_id_val=array_unique(explode(',',$machine_id_arr_non_order[$row[csf("prod_id")]]));
							foreach($mc_id_val as $mc_id)
							{
								if($row[csf("issue_purpose")]==8)
								{
									if($mc_id!=0)
									{
										if($mc_dia==""){$mc_dia=$mc_id;} else {$mc_dia.=', '.$mc_id;}//
									}
										
								}
								
							}
						}
					}
					else
					{
						$pono_id=array_unique(explode(',',$po_id));
						$mc_dia="";
						foreach($pono_id as $poId )
						{
							
							$mc_id_val=array_unique(explode(',',$machine_dia_arr[$poId][$row[csf("prod_id")]]));
							foreach($mc_id_val as $mc_id)
							{
								if($mc_dia=="") $mc_dia=$mc_id; else $mc_dia.=', '.$mc_id;//
							}
						}
					}

					if(!in_array($row[csf('issue_number')],$issue_array))
					{
						if($i!=1)
						{
							?>
								<tr bgcolor="#CCCCCC">
									<td colspan="14" align="right"> Total :</td>
									<td align="right"><?php echo number_format($total_roll,2,'.',''); ?></td>
									<td align="right"><?php echo number_format($total_issue_qnty,2,'.',''); ?></td>
									<td>&nbsp;</th>
								</tr>
							<?
								$total_roll=0;
								$total_issue_qnty=0;

							
						}
							?>
							<tr>
								<td colspan="17" bgcolor="#EEEEEE" style="word-break:break-all"><b>
									<?php echo "Issue No:- ".$row[csf('issue_number')];  ?></b></td>
							</tr>
							<?
							$issue_array[$i]=$row[csf('issue_number')];
						
					}

					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="100" align="center"><? echo $buyer_name;?></td>
						<td width="130" align="center"><? echo $job_alldata_arr[$row[csf("trans_id")]]['job_no'];?></td>
						<td width="100" align="center"><? echo $row[csf("service_booking_no")];?></td> 
						<td width="80" align="center"><? if($row[csf('issue_basis')] == 3 && $row[csf('issue_purpose')]==8){echo $styelRefs;}else{ echo $row[csf("style_ref")];}?></td>
						<td width="100" align="center"><? echo $job_alldata_arr[$row[csf("trans_id")]]['order_no'];?></td>
						<td width="100" align="center"><? echo $product_array[$row[csf("prod_id")]]['item_description'];?></td>
						<td width="60" align="center"><? echo $product_array[$row[csf("prod_id")]]['gsm'];?></td>
						<td width="60" align="center"><? echo $row[csf('stitch_length')]; ?></td>
						<td width="60" align="center"><? if($row[csf('issue_basis')] == 3 && $row[csf('issue_purpose')]==8){echo rtrim($mc_dia_nonOrdSample, ', ');}else{echo rtrim($mc_dia, ', ');}?></td>
						<td width="60" align="center"><? echo $product_array[$row[csf("prod_id")]]['dia_width'];?></td>
						<td width="60" align="center"><? echo $count_val;?></td>
						<td width="80" align="right"><? echo $row[csf('lot')]; ?></td>
						<td width="80" align="right"><? echo $color_names;?></td>
						<td width="80" align="right"><? echo $row[csf("no_of_roll")];?></td>
						<td width="80" align="right"><? echo number_format($row[csf('issue_qnty')], 0, '', ',');?></td>
						<td align="center"><? echo $row[csf('remarks')]; ?></td>
					</tr>
					<?php
					$total_roll += $row[csf('no_of_roll')];
					$total_issue_qnty += $row[csf('issue_qnty')];
				
				

					$gRollQty += $row[csf('no_of_roll')];
					$gIssueQty += $row[csf('issue_qnty')];

					$i++;
				}
				if(count($sql_issue_rslt)>0)
				{
					?>
					<tr bgcolor="#CCCCCC">
						<td colspan="14" align="right"> Total :</td>
						<td align="right"><?php echo number_format($total_roll,2,'.',''); ?></td>
						<td align="right"><?php echo number_format($total_issue_qnty,2,'.',''); ?></td>
						
						<td>&nbsp;</th>
					</tr>
					<?
				}

				?>
				<tr>
					<td align="right" colspan="14" ><b>Grand Total :</b></td>
					
					<td align="right"><? echo number_format($gRollQty, 2, '.', ','); ?></td>
					<td align="right"><? echo number_format($gIssueQty, 2, '.', ','); ?></td>
					<td align="right">&nbsp;</td>
				</tr>
				
			</table>
			<br>
			<?
			echo signature_table(17, $data[1], "1200px");
			?>
		</div>
	</div>
	<?
	exit();
}

?>