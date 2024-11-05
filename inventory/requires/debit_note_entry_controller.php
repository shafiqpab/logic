<?php 
header('Content-type:text/html; charset=utf-8');
session_start();

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
// if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$permission=$_SESSION['page_permission'];

//Start......


if ($action=="load_drop_down_com_location")
{
	$sql="select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name";
	$result=sql_select($sql);
	$index=$selected;
	if(count($result)==1)
	{
		$index=$result[0][csf('id')];
	}
	//echo $sql."**".$index;
	echo create_drop_down( "cbo_location_name", 170, $sql,"id,location_name", 1, "-- Select --", $index, "" );	
	exit();		 
}


//load drop down Working company
if ($action == "load_drop_down_working_com")
{
	if ($data == 1)
	{
		echo create_drop_down("cbo_working_company", 170, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name", 1, "-- Select --", "", "");
	}
	else if ($data == 2)
	{
		echo create_drop_down("cbo_working_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	}
	else if ($data == 0)
	{
		echo create_drop_down("cbo_working_company", 170, $blank_array, "", 1, "-- Select --", $selected, "", "", "");
	}
	exit();
}

if ($action == "load_drop_down_basis")
{
	$basis_arr=[1=>'Independent',2=>'Issue ID',3=>'WO/Booking',4=>'Adjustment',5=>'Program No'];

	if ($data == 2 || $data == 16)
	{
		echo create_drop_down( "cbo_basis", 170, $basis_arr,"", 1, "-- Select Basis --", "", "active_inactive(this.value);", "", "1,3,4");
	}
	else if ($data == 1)
	{
		echo create_drop_down( "cbo_basis", 170, $basis_arr,"", 1, "-- Select Basis --", "", "active_inactive(this.value);", "", "2,5");
	}
	else if ($data == 0)
	{
		echo create_drop_down("cbo_basis", 170, $blank_array, "", 1, "-- Select --", $selected, "", "", "");
	}
	exit();
}

if($action=="debit_number_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>

		<script>
			function js_set_value(mrr)
			{
				$("#hidden_debit_id").val(mrr);
				parent.emailwindow.hide();
			}

			function popup_print()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				$('#list_view tbody tr:first').hide();
				document.getElementById('list_container_batch').style.overflow="auto";
				document.getElementById('list_container_batch').style.maxHeight="none";

				d.write(document.getElementById('popup_data').innerHTML);

				document.getElementById('list_container_batch').style.overflowY="scroll";
				document.getElementById('list_container_batch').style.maxHeight="240px";

				$('#list_view tbody tr:first').show();
				d.close();
			}
		</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="780" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<tr>
							<th width="300" align="center">Enter Debit Note Number</th>
							<th width="320">Date Range</th>
							<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
						</tr>
					</thead>
					<tbody>
						<tr class="general">
							<td width="" align="center">
								<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:120px" placeholder="From Date" />
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:120px" placeholder="To Date" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( +document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_debit_no_search_list_view', 'search_div', 'debit_note_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td align="center" height="40" valign="middle" colspan="5">
								<? echo load_month_buttons(1);  ?>
								<!-- Hidden field here-->
								<input type="hidden" id="hidden_debit_id" value="" />
								<!--END-->
							</td>
						</tr>
					</tbody>
				</tr>
			</table>
			<div style="margin-top:5px" align="center" valign="top" id="search_div"> </div>
		</form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_debit_no_search_list_view")
{
	$ex_data = explode("_",$data);

	$search_common = $ex_data[0];
	$txt_date_from = $ex_data[1];
	$txt_date_to = $ex_data[2];
	$company = $ex_data[3];

	$sql_cond="";
	
	if($search_common>0) $sql_cond .= " and sys_number like '%$search_common'";
	
	
	if( $txt_date_from!="" && $txt_date_to!="" )
	{
		if($db_type==0)
		{
			$sql_cond .= " and a.debit_note_date  between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else
		{
			$sql_cond .= " and a.debit_note_date  between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
		}
	}

	if($company!="") $sql_cond .= " and a.company_id=$company";

	if($db_type==0) $year_field="YEAR(a.insert_date) as year,";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	else $year_field="";

	$sql = "SELECT a.id as mst_id, a.sys_number, a.sys_number_prefix_num, a.debit_note_date,$year_field a.debit_note_against,a.goods_name,a.basis, b.id as tr_id, b.recv_issue_id,b.prod_id,b.fab_prod_id, b.febric_description , b.yarn_lot, b.debit_note_qnty, b.total_debit_amount 
	from  debit_note_entry_mst a, debit_note_entry_dtls b
	where a.id=b.mst_id and a.entry_form=633 and b.entry_form=633 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond  order by a.id desc";

	$sql_result=sql_select($sql);
	if(count($sql_result)==0)
	{
		?>
		<div class="alert alert-danger">Data not found! Please try again.</div>
		<?
		die();
	}

	?>
	<div id="popup_data" style=" width:780px;">
		<table class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0" width="780">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="110">Debit No</th>
					<th width="80">Product Id</th>
					<th width="80">Lot</th>
					<th width="240">Item Description</th>
					<th width="70">Debit Qnty</th>
					<th width="70">Debit Amount</th>
					<th width="">Date</th>
				</tr>
			</thead>
		</table>
		<div style="width:780px; max-height:240px; overflow-y:scroll" id="list_container_batch">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="760" class="rpt_table" id="list_view">
				<tbody>
					<?
					
					$i=1;
					$prod_id ='';
					$lot ='';
					$product_name_details ='';
					foreach($sql_result as $row)
					{
						if($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						if($row[csf("debit_note_against")]==1)
						{
							if($row[csf("goods_name")]==16 && $row[csf("basis")]==3)
							{
								$prod_id = $row[csf("prod_id")];
							}
							else if($row[csf("goods_name")]==1 && $row[csf("basis")]==5)
							{
								$prod_id = $row[csf("fab_prod_id")];
							}
							else if($row[csf("goods_name")]==1 && $row[csf("basis")]==2)
							{
								$prod_id = $row[csf("prod_id")];
							}
						}	
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf("mst_id")]; ?>')" style="cursor:pointer;">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="110" align="center" style="word-break:break-all"><p><? echo $row[csf("sys_number")]; ?>&nbsp;</p></td>
							<td width="80" align="center" style="word-break:break-all"><p><? echo $prod_id; ?>&nbsp;</p></td>
							<td width="80" align="center" style="word-break:break-all"><? echo $row[csf("yarn_lot")]; ?>&nbsp;</td>
							<td width="240" align="center" style="word-break:break-all"><? echo $row[csf("febric_description")]; ?></td>
							<td width="70" align="center" style="word-break:break-all"><? echo $row[csf("debit_note_qnty")]; ?></td>
							<td width="70" align="center" style="word-break:break-all"><? echo number_format($row[csf("total_debit_amount")],2); ?></td>
							<td width="" align="center" style="word-break:break-all"><? if($row[csf("debit_note_date")]!="" && $row[csf("debit_note_date")]!="0000-00-00") echo change_date_format($row[csf("debit_note_date")]);?></td>
							
						</tr>
						<?
						$i++;
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
	<div style="margin-top:5px" align="center" valign="top"><input type="button" id="btn_print" class="formbutton" style="width:100px;" value="Print" onClick="popup_print()"/> </div>
	<?
	exit();
}

// wo/pi popup here----------------------//
if ($action == "woissueadj_popup")
{
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert(splitData[0]);
            $("#hidden_tbl_id").val(splitData[0]); // wo/Issue id
            $("#hidden_woissue_number").val(splitData[1]); // wo/Issue number

            parent.emailwindow.hide();
        }
	
    </script>

	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
				<tr>
					<th width="150">WO No</th>
					<th width="150">Program No</th>
					<th width="150">Issue No</th>
					<th width="200">Date Range</th>
					<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px"
						class="formbutton"/></th>
					</tr>
				</thead>
				<tbody>
					<tr class="general">
					
						<td>
							<input type="text" style="width:130px" class="text_boxes" name="txt_wo_no" id="txt_wo_no"
							<? echo ($debit_basis == 3) ? "" : "disabled='disabled'"; ?> placeholder="write"/>
						</td>
						<td>
							<input type="text" style="width:130px" class="text_boxes" name="txt_prog_no" id="txt_prog_no"
							<? echo ($debit_basis == 5) ? "" : "disabled='disabled'"; ?> placeholder="write"/>
						</td>
						<td>
							<input type="text" style="width:130px" class="text_boxes" name="txt_issue_no" id="txt_issue_no"
							<? echo ($debit_basis == 2) ? "" : "disabled='disabled'"; ?> placeholder="write"/>
						</td>
						
						<td align="center">
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"
							placeholder="From Date"/>
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
							placeholder="To Date"/>
						</td>
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show"
							onClick="show_list_view ( document.getElementById('txt_wo_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_<? echo $company; ?>'+'_<? echo $debit_basis; ?>'+'_<? echo $debit_note_against; ?>'+'_<? echo $service_goods_name; ?>'+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_issue_no').value+'_'+document.getElementById('txt_prog_no').value, 'create_woissue_search_list_view', 'search_div', 'debit_note_entry_controller', 'setFilterGrid(\'list_view\',-1)')"
							style="width:100px;"/>
						</td>
					</tr>
					<tr>
						<td align="center" height="40" valign="middle" colspan="7">
							<? echo load_month_buttons(1); ?>
							<!-- Hidden field here -->
							<input type="hidden" id="hidden_tbl_id" value=""/>
							<input type="hidden" id="hidden_woissue_number" value=""/>					
							<!-- -END -->
						</td>
					</tr>
				</tbody>
			</tr>
		</table>
		<div align="center" style="margin-top:5px" valign="top" id="search_div"></div>
	</form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	
	</html>
	<?
	exit;
}

//after select wo/Issue number get form data here---------------------------//
if ($action == "create_woissue_search_list_view")
{
	$ex_data = explode("_", $data);
	$txt_wo_no = trim($ex_data[0]);
	$txt_date_from = trim($ex_data[1]);
	$txt_date_to = trim($ex_data[2]);
	$company = trim($ex_data[3]);
	$debit_basis = trim($ex_data[4]);
	$debit_note_against = trim($ex_data[5]);
	$service_goods_name = trim($ex_data[6]);
	$year = $ex_data[7];
	$issue_no = trim($ex_data[8]);
	$program_no = trim($ex_data[9]);
	//var_dump($program_no);die;
	
	if($debit_note_against == 1)
	{
		if ($service_goods_name ==16 && $debit_basis == 3)
		{

			$sql_cond = "";
			if ($txt_date_from != "" && $txt_date_to != "") {
				if ($db_type == 0) {
					$sql_cond .= " and a.wo_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
				} else {
					$sql_cond .= " and a.wo_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
				}
			}

			if ($txt_wo_no != "") $sql_cond .= " and a.wo_number_prefix_num=$txt_wo_no";
			if ($year!=0) $cbo_year_con=" and to_char(a.insert_date,'YYYY')=$year"; else $cbo_year_con='';

			$sql = "SELECT a.id,a.wo_number,a.wo_number_prefix_num, a.wo_date
				from wo_non_order_info_mst a,inv_receive_master b where a.id=b.booking_id and a.status_active=1 and a.is_deleted=0 AND b.status_active = 1 and b.is_deleted = 0 and b.entry_form = 1 and b.item_category = 1 and b.receive_basis=2 and b.receive_purpose=16 and a.entry_form=144 and a.company_name=$company and a.pay_mode!=2 and a.payterm_id<>5 $sql_cond  $cbo_year_con 
				group by a.id, a.wo_number, a.wo_number_prefix_num, a.wo_date order by a.id";

			//echo $sql;//die;
			$result = sql_select($sql);
			if(count($result)==0)
			{
				?>
				<div class="alert alert-danger">Data not found! Please try again.</div>
				<?
				die();
			}
			?>
			<style>
				.wrd_brk{word-break: break-all;word-wrap: break-word;}          
			</style>
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width='550'>
				<thead>
					<tr>
						<th width="30" class="wrd_brk">SL</th>
						<th width="200" class="wrd_brk">WO No</th>
						<th width="100" class="wrd_brk">WO Prefix No</th>
						<th width="" class="wrd_brk">WO Date</th>
					</tr>
				</thead>
			</table>
			<div style="width:550px; max-height:220px; overflow-y:scroll" id="scroll_body">
				<table width="530px" cellspacing="0" border="1" class="rpt_table" rules="all" id="list_view">
					<tbody >
						<? 
						$i = 1;
						foreach ($result as $row) 
						{
							if ($i % 2 == 0) $bgcolor = "#E9F3FF";
							else $bgcolor = "#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>"
								style="cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>_<? echo $row[csf('wo_number')]; ?>');">
								<td width="30" class="wrd_brk" ><? echo $i; ?></td>
								<td width="200" class="wrd_brk" ><? echo $row[csf("wo_number")]; ?></td>
								<td width="100" class="wrd_brk" ><? echo $row[csf("wo_number_prefix_num")]; ?></td>
								<td width="" class="wrd_brk" ><? echo change_date_format($row[csf("wo_date")]); ?></td>
							</tr>
							<? $i++;
						} ?>
					</tbody>
				</table>
			</div>

			<?
		}
		else if ($service_goods_name ==1 && $debit_basis == 5)
		{

			$sql_cond = "";
			if ($txt_date_from != "" && $txt_date_to != "") {
				if ($db_type == 0) {
					$sql_cond .= " and a.program_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
				} else {
					$sql_cond .= " and a.program_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
				}
			}

			if ($program_no != "") $sql_cond .= " and a.id=$program_no";
			if ($year!=0) $cbo_year_con=" and to_char(a.insert_date,'YYYY')=$year"; else $cbo_year_con='';

			$sql = "SELECT a.id, a.program_date
				from ppl_planning_info_entry_dtls a,inv_receive_master b where cast(a.id as varchar(4000)) = b.booking_no and a.status_active=1 and a.is_deleted=0 AND b.status_active = 1 and b.is_deleted = 0 and b.entry_form = 2 and b.item_category = 13 and b.receive_basis=2  and b.company_id=$company $sql_cond $cbo_year_con 
				group by a.id, a.program_date order by a.id";

			//echo $sql;//die;
			$result = sql_select($sql);

			if(count($result)==0)
			{
				?>
				<div class="alert alert-danger">Data not found! Please try again.</div>
				<?
				die();
			}

			?>
			<style>
				.wrd_brk{word-break: break-all;word-wrap: break-word;}          
			</style>
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width='450'>
				<thead>
					<tr>
						<th width="30" class="wrd_brk">SL</th>
						<th width="200" class="wrd_brk">Program No</th>
						<th width="" class="wrd_brk">Program Date</th>
					</tr>
				</thead>
			</table>
			<div style="width:450px; max-height:220px; overflow-y:scroll" id="scroll_body">
				<table width="430px" cellspacing="0" border="1" class="rpt_table" rules="all" id="list_view">
					<tbody >
						<? 
						$i = 1;
						foreach ($result as $row) 
						{
							if ($i % 2 == 0) $bgcolor = "#E9F3FF";
							else $bgcolor = "#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>"
								style="cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>_<? echo $row[csf('id')]; ?>');">
								<td width="30" class="wrd_brk" ><? echo $i; ?></td>
								<td width="200" class="wrd_brk" ><? echo $row[csf("id")]; ?></td>
								<td width="" class="wrd_brk" ><? echo change_date_format($row[csf("program_date")]); ?></td>
							</tr>
							<? $i++;
						} ?>
					</tbody>
				</table>
			</div>

			<?
		}
		else if ($service_goods_name ==1 && $debit_basis == 2)
		{

			$sql_cond = "";
			if ($txt_date_from != "" && $txt_date_to != "") {
				if ($db_type == 0) {
					$sql_cond .= " and issue_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
				} else {
					$sql_cond .= " and issue_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
				}
			}

			if ($issue_no != "") $sql_cond .= " and issue_number_prefix_num=$issue_no";
			if ($year!=0) $cbo_year_con=" and to_char(insert_date,'YYYY')=$year"; else $cbo_year_con='';

			$sql = "SELECT id, issue_number, issue_number_prefix_num, issue_date from inv_issue_master where status_active=1 and is_deleted=0 and entry_form = 3 and item_category = 1 and issue_basis=3 and issue_purpose=1 and company_id=$company $sql_cond $cbo_year_con group by id, issue_number, issue_number_prefix_num, issue_date order by id";

			//echo $sql;//die;
			$result = sql_select($sql);

			if(count($result)==0)
			{
				?>
				<div class="alert alert-danger">Data not found! Please try again.</div>
				<?
				die();
			}

			?>
			<style>
				.wrd_brk{word-break: break-all;word-wrap: break-word;}          
			</style>
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width='450'>
				<thead>
					<tr>
						<th width="30" class="wrd_brk">SL</th>
						<th width="180" class="wrd_brk">Issue No</th>
						<th width="100" class="wrd_brk">Issue Prefix Num</th>
						<th width="" class="wrd_brk">Issue Date</th>
					</tr>
				</thead>
			</table>
			<div style="width:450px; max-height:220px; overflow-y:scroll" id="scroll_body">
				<table width="430px" cellspacing="0" border="1" class="rpt_table" rules="all" id="list_view">
					<tbody >
						<? 
						$i = 1;
						foreach ($result as $row) 
						{
							if ($i % 2 == 0) $bgcolor = "#E9F3FF";
							else $bgcolor = "#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>"
								style="cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>_<? echo $row[csf('issue_number')]; ?>');">
								<td width="30" class="wrd_brk" ><? echo $i; ?></td>
								<td width="180" class="wrd_brk" ><? echo $row[csf("issue_number")]; ?></td>
								<td width="100" class="wrd_brk" ><? echo $row[csf("issue_number_prefix_num")]; ?></td>
								<td width="" class="wrd_brk" ><? echo change_date_format($row[csf("issue_date")]); ?></td>
							</tr>
							<? $i++;
						} ?>
					</tbody>
				</table>
			</div>

			<?
		}
	}
		
	
	exit();
}

if($action == "item_desc_popup")
{
	echo load_html_head_contents("Item Desc Info","../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert(splitData[0]);
            $("#hidden_yarn_rcv_id").val(splitData[0]); // wo/pi id
            $("#hidden_prod_id").val(splitData[1]); 
            $("#hidden_req_no").val(splitData[2]); 

            parent.emailwindow.hide();
        }
	</script>
	</head>
	<fieldset style="width:890px">

		<input type="hidden" name="hidden_yarn_rcv_id" id="hidden_yarn_rcv_id" value="">
		<input type="hidden" name="hidden_prod_id" id="hidden_prod_id" value="">
		<input type="hidden" name="hidden_req_no" id="hidden_req_no" value="">
		 <? if($debit_note_against == 1){
				if ($service_goods_name ==16 && $debitBasis == 3)
				{	
					?>	
					<table width="880" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
						<thead>
							<tr>
								<th width="30">SL</th>
								<th width="100">WO No</th>
								<th width="100">MRR No</th>
								<th width="250">Product Details</th>
								<th width="100">Yarn Lot</th>
								<th width="50">UOM</th>
								<th width="75">Receive Qty</th>
								<th width="50">Rate<br>(tk)</th>
								<th width="75">Amount<br>(tk)</th>
							</tr>
						</thead>
					</table>
					<div style="width:900px; overflow-y:scroll; max-height:300px" id="scroll_body">
						<table width="880" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
						<?

						$sql = "SELECT a.wo_number,b.id as rcv_id,b.recv_number,c.prod_id,c.order_rate,c.order_qnty,c.order_amount, d.product_name_details,d.lot,d.unit_of_measure, c.cons_rate, c.cons_amount
						from wo_non_order_info_mst a,inv_receive_master b, inv_transaction c, product_details_master d where a.id=b.booking_id and b.id=c.mst_id and a.id=c.pi_wo_batch_no and c.prod_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=144 and a.company_name=$companyID and a.id=$woIissueAdjId and a.pay_mode!=2 and a.payterm_id<>5 and b.entry_form = 1 and b.item_category = 1 and b.receive_basis=2 and b.receive_purpose=16 and c.transaction_type=1";
						//echo $sql; 
						$result = sql_select($sql);

						if(count($result)==0)
						{
							?>
							<div class="alert alert-danger">Data not found! Please try again.</div>
							<?
							die();
						}
						$main_data_arr = array();
						$prod_ids_arr = array();
						$prodIdChk = array();
						foreach ($result as $row)
						{
							$main_data_arr[$row[csf("rcv_id")]][$row[csf("prod_id")]]['wo_number'] = $row[csf("wo_number")];
							$main_data_arr[$row[csf("rcv_id")]][$row[csf("prod_id")]]['recv_number'] = $row[csf("recv_number")];
							$main_data_arr[$row[csf("rcv_id")]][$row[csf("prod_id")]]['order_qnty'] += $row[csf("order_qnty")];
							$main_data_arr[$row[csf("rcv_id")]][$row[csf("prod_id")]]['cons_rate'] = $row[csf("cons_rate")];
							$main_data_arr[$row[csf("rcv_id")]][$row[csf("prod_id")]]['cons_amount'] += $row[csf("cons_amount")];
							$main_data_arr[$row[csf("rcv_id")]][$row[csf("prod_id")]]['product_name_details'] = $row[csf("product_name_details")];
							$main_data_arr[$row[csf("rcv_id")]][$row[csf("prod_id")]]['lot'] = $row[csf("lot")];
							$main_data_arr[$row[csf("rcv_id")]][$row[csf("prod_id")]]['uom'] = $row[csf("unit_of_measure")];
						}
						unset($result);
						// echo "<pre>";print_r($main_data_arr);

						$i = 1;
						foreach ($main_data_arr as $k_rcv_id=>$v_rcv_id)
						{
							foreach ($v_rcv_id as $k_prod_id=>$row) 
							{
								//echo "<pre>";print_r($row);
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $k_rcv_id; ?>_<? echo $k_prod_id; ?>');" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
									<td width="30">
										<? echo $i; ?>
									</td>
										<td width="100"><? echo $row['wo_number'];?></td>
										<td width="100"><? echo $row['recv_number'];?></td>
										<td width="250"><? echo $row['product_name_details'];?></td>
										<td width="100"><? echo $row['lot'];?></td>
										<td width="50"><? echo $unit_of_measurement[$row['uom']];?></td>
										<td width="75" align="right"><? echo $row['order_qnty'];?></td>
										<td width="50" align="right"><? echo $row['cons_rate'];?></td>
										<td width="75" align="right"><? echo $row['cons_amount'];?></td>
									</tr>
								<?
								$i++;
							}
						}
						?>

						</table>
					</div>
			  <?} 
			  	else if($service_goods_name ==1 && $debitBasis == 5)
				{ ?>
					<table width="670" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
						<thead>
							<tr>
								<th width="30">SL</th>
								<th width="250">Product Details</th>
								<th width="150">Yarn Lot</th>
								<th width="50">UOM</th>
								<th width="100">Rec./Prod.Qty</th>
							</tr>
						</thead>
					</table>
					<div style="width:690; overflow-y:scroll; max-height:300px" id="scroll_body" >
						<table width="670" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body" >
						<?
						$fabric_desc_arr = return_library_array("select id, item_description from product_details_master where item_category_id=13", "id", "item_description");

						$sql = "SELECT a.id as prog_no, a.program_date, c.prod_id as fab_prod_id, c.yarn_lot, c.uom, c.febric_description_id, c.grey_receive_qnty
						from ppl_planning_info_entry_dtls a,inv_receive_master b, pro_grey_prod_entry_dtls c where cast(a.id as varchar(4000)) = b.booking_no and b.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and b.entry_form = 2 and b.item_category = 13 and b.receive_basis=2 and b.company_id=$companyID and a.id=$woIissueAdjId ";	
						//echo $sql;

						$result = sql_select($sql);

						if(count($result)==0)
						{
							?>
							<div class="alert alert-danger">Data not found! Please try again.</div>
							<?
							die();
						}

						$main_data_arr = array();
						$feb_desc_chk = array();
						$feb_desc_arr = array();
						foreach ($result as $row)
						{
							$main_data_arr[$row[csf("prog_no")]]['febric_description_id'] .= $row[csf("febric_description_id")].",";
							$main_data_arr[$row[csf("prog_no")]]['yarn_lot'] .= $row[csf("yarn_lot")].",";
							$main_data_arr[$row[csf("prog_no")]]['fab_prod_id'] .= $row[csf("fab_prod_id")].",";
							$main_data_arr[$row[csf("prog_no")]]['uom'] .= $row[csf("uom")].",";
							$main_data_arr[$row[csf("prog_no")]]['grey_receive_qnty'] += $row[csf("grey_receive_qnty")];

							if($feb_desc_chk[$row[csf("febric_description_id")]] == "")
							{
								$feb_desc_chk[$row[csf("febric_description_id")]] = $row[csf("febric_description_id")];
								array_push($feb_desc_arr,$row[csf("febric_description_id")]);
							}

						}
						unset($result);
						//echo "<pre>";print_r($main_data_arr);

						$composition_arr = array();
						$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($feb_desc_arr,0,'a.id')."";
						$data_array = sql_select($sql_deter);
						if (count($data_array) > 0) {
							foreach ($data_array as $row) {

								if (array_key_exists($row[csf('id')], $composition_arr)) {
									$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
								} else {
									$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
								}
							}
							unset($data_array);
						}

						$i = 1;
						foreach ($main_data_arr as $k_prog_no=>$row)
						{
							//echo "<pre>";print_r($row);
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
							$fabric_desc = '';
							$febric_description_ids = array_unique(explode(",",chop($row['febric_description_id'] ,",")));
							
							foreach ($febric_description_ids as $febric_desc_id)
							{
									
								if ($febric_desc_id == 0 || $febric_desc_id == "")
								{
									$fab_prod_ids = array_unique(explode(",",chop($row['fab_prod_id'] ,",")));
									foreach ($fab_prod_ids as $fab_prod_id)
									{
										$fabric_desc .= $fabric_desc_arr[$fab_prod_id].',';
									}
								}
								else
								{
									$fabric_desc .= $composition_arr[$febric_desc_id].",";
								}
								
							}
						

						
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $k_prog_no; ?>_<? echo $k_prog_no; ?>');" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
									<td width="30"><? echo $i; ?></td>
									<td width="250"><? echo chop($fabric_desc,',');?></td>
									<td width="150"><? echo implode(",",array_unique(explode(",",chop($row['yarn_lot'] ,","))));?></td>
									<td width="50"><? echo $unit_of_measurement[implode(",",array_unique(explode(",",chop($row['uom'] ,","))))];?></td>
									<td width="100"><? echo number_format($row['grey_receive_qnty'],2);?></td>
								</tr>
							<?
							$i++;
							
						}
						?>

						</table>
					</div>
			  
		     <? } else if($service_goods_name ==1 && $debitBasis == 2)
				{ ?>
					<table width="940" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
						<thead>
							<tr>
								<th width="30">SL</th>
								<th width="80">Issue ID</th>
								<th width="80">Lot No</th>
								<th width="100">Supplier</th>
								<th width="50">Yarn Count</th>
								<th width="100">Composition</th>
								<th width="80">Yarn Type</th>
								<th width="80">Color</th>
								<th width="80">Store</th>
								<th width="80">Issue Qnty</th>
								<th width="50">UOM</th>
								<th width="50">No of Bag</th>
								<th width="">Req. No</th>
							</tr>
						</thead>
					</table>
					<div style="width:980; overflow-y:scroll; max-height:300px" id="scroll_body" >
						<table width="940" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body" >
						<?

						$supplier_arr = return_library_array("select id, short_name from lib_supplier", 'id', 'short_name');
						$company_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
						$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
						$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
						$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

						$sql = "SELECT a.id, a.issue_number, b.requisition_no, b.prod_id, b.store_id, b.cons_uom, b.cons_quantity, b.no_of_bags, b.supplier_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type, c.color, c.lot,  c.is_within_group from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=3 and a.item_category=1 and b.item_category=1 and a.issue_basis=3 and b.receive_basis=3 and a.issue_purpose=1 and a.company_id=$companyID and a.id=$woIissueAdjId ";

						//echo $sql; 
						$result = sql_select($sql);

						if(count($result)==0)
						{
							?>
							<div class="alert alert-danger">Data not found! Please try again.</div>
							<?
							die();
						}

						$main_data_arr = array();
						$feb_desc_chk = array();
						$feb_desc_arr = array();
						foreach ($result as $row)
						{
							$main_data_arr[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("requisition_no")]]['yarn_count_id'] = $row[csf("yarn_count_id")];
							$main_data_arr[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("requisition_no")]]['yarn_comp_type1st'] = $row[csf("yarn_comp_type1st")];
							$main_data_arr[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("requisition_no")]]['yarn_comp_percent1st'] = $row[csf("yarn_comp_percent1st")];
							$main_data_arr[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("requisition_no")]]['yarn_comp_type2nd'] = $row[csf("yarn_comp_type2nd")];
							$main_data_arr[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("requisition_no")]]['yarn_comp_percent2nd'] = $row[csf("yarn_comp_percent2nd")];
							$main_data_arr[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("requisition_no")]]['yarn_type'] = $row[csf("yarn_type")];
							$main_data_arr[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("requisition_no")]]['color'] = $row[csf("color")];
							$main_data_arr[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("requisition_no")]]['lot'] = $row[csf("lot")];
							$main_data_arr[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("requisition_no")]]['store_id'] = $row[csf("store_id")];
							$main_data_arr[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("requisition_no")]]['cons_uom'] = $row[csf("cons_uom")];
							$main_data_arr[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("requisition_no")]]['cons_quantity'] += $row[csf("cons_quantity")];
							$main_data_arr[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("requisition_no")]]['no_of_bags'] = $row[csf("no_of_bags")];
							$main_data_arr[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("requisition_no")]]['supplier_id'] = $row[csf("supplier_id")];
							$main_data_arr[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("requisition_no")]]['is_within_group'] = $row[csf("is_within_group")];

						}
						unset($result);
						

						$i = 1;
						foreach ($main_data_arr as $k_id=>$v_id)
						{
							foreach ($v_id as $k_prod_id=>$v_prod_id)
							{
								foreach ($v_prod_id as $k_req_no=>$row)
								{
									//echo "<pre>";print_r($row);
									if ($i % 2 == 0)
										$bgcolor = "#E9F3FF";
									else
										$bgcolor = "#FFFFFF";
									$fabric_desc = '';

									if($row['is_within_group'] == 1)
									{
										$supplier_name = $company_arr[$row['supplier_id']];
									}
									else
									{
										$supplier_name = $supplier_arr[$row['supplier_id']];
									}

									$composition_string = $composition[$row['yarn_comp_type1st']] . " " . $row['yarn_comp_percent1st'];
									if ($row['yarn_comp_type2nd'] != 0)
										$composition_string .= " " . $composition[$row['yarn_comp_type2nd']] . " " . $row['yarn_comp_percent2nd'];

								
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $k_id; ?>_<? echo $k_prod_id; ?>_<? echo $k_req_no; ?>');" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
										<td width="30"><? echo $i;?></td>
										<td width="80"><? echo $k_id;?></td>
										<td width="80" title="<? echo $k_prod_id;?>"><? echo $row['lot'];?></td>
										<td width="100"><? echo $supplier_name; ?></td>
										<td width="50"><? echo $yarn_count_arr[$row["yarn_count_id"]]; ?></td>
										<td width="100"><? echo $composition_string; ?></td>
										<td width="80"><? echo $yarn_type[$row["yarn_type"]]; ?></td>
										<td width="80"><? echo $color_name_arr[$row["color"]]; ?></td>
										<td width="80"><? echo $store_arr[$row["store_id"]]; ?></td>
										<td width="80"><? echo number_format($row["cons_quantity"], 2, '.', ''); ?></td>
										<td width="50"><? echo $unit_of_measurement[$row["cons_uom"]]; ?></td>
										<td width="50"><? echo $row["no_of_bags"]; ?></td>
										<td width=""><? echo $k_req_no; ?></td>
									</tr>
									<?
									$i++;
								}
							}
							
						}
						?>

						</table>
					</div>
			  
		     <? } 
			}
		 ?>	

	</fieldset>
	<script type="text/javascript">
		setFilterGrid('table_body',-1);
	</script>
	<?
	exit();	
}

if($action=="populate_data_from_data")
{
	//print_r($data);
	$ex_data = explode("_",$data);
	$receiveId = $ex_data[0];
	$prodId = $ex_data[1];
	$debitBasis = $ex_data[2];
	$companyID = $ex_data[3];
	$serviceGoodsName = $ex_data[4];
	$debitNoteAgainst = $ex_data[5];
	$req_no = $ex_data[6];

	$count_arr = return_library_array( "select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0",'id','yarn_count');
    $brand_arr = return_library_array( "select id, brand_name from lib_brand where status_active=1 and is_deleted=0",'id','brand_name');
    $color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	if($debitNoteAgainst==1)
	{
		if( $serviceGoodsName==16 && $debitBasis==3 )
		{
			$sql = "SELECT a.id as rcv_id,a.recv_number,b.prod_id,b.order_rate,b.order_qnty,b.order_amount, b.cons_rate, b.cons_quantity, b.cons_amount, c.product_name_details, c.lot, c.unit_of_measure, c.yarn_count_id, c.brand, c.color
			from inv_receive_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form = 1 and a.item_category = 1 and a.receive_basis=2 and a.receive_purpose=16 and b.transaction_type=1 and a.company_id=$companyID and a.id=$receiveId and b.prod_id=$prodId";
			//echo $sql;die();
			$result = sql_select($sql);

			if(count($result)==0)
			{
				?>
				<div class="alert alert-danger">Data not found! Please try again.</div>
				<?
				die();
			}
		
			$sql_recv_rtn = "SELECT a.id, a.received_id,b.prod_id, b.cons_quantity
			from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.item_category=1 and b.item_category=1 and b.transaction_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  a.company_id=$companyID and a.received_id=$receiveId and b.prod_id=$prodId";

			//echo $sql_recv_rtn;die();
			$result_recv_rtn = sql_select($sql_recv_rtn);
			if(count($result_recv_rtn) !="")
			{
				$rcv_rtn_arr = array();
				foreach ($result_recv_rtn as $row) 
				{
					$rcv_rtn_arr[$row[csf("received_id")]][$row[csf("prod_id")]]["cons_quantity"] += $row[csf("cons_quantity")];
				}
			}

			
			foreach($result as $row)
			{
				$rcv_rtn_qnty = $rcv_rtn_arr[$row[csf("rcv_id")]][$row[csf("prod_id")]]["cons_quantity"]; 
				$returnable_balance_qnty = $row[csf("cons_quantity")]-$rcv_rtn_qnty; 
				$txt_returnable_bl_value = $row[csf("cons_rate")]*$returnable_balance_qnty;

				$product_name_details = str_replace(array("\r", "\n"), '', $row[csf("product_name_details")]);

				echo "$('#txt_item_description').val('".$product_name_details."');\n";
				echo "$('#txt_prod_id').val('".$row[csf("prod_id")]."');\n";
				echo "$('#txt_rcvissue_id').val('".$row[csf("rcv_id")]."');\n";
				echo "$('#txt_yarn_lot').val('".$row[csf("lot")]."');\n";
				echo "$('#cbo_uom').val('".$row[csf("unit_of_measure")]."');\n";
				echo "$('#txt_yarn_rate').val('".$row[csf("cons_rate")]."');\n";
				echo "$('#txt_yarn_receive_qty').val('".$row[csf("cons_quantity")]."');\n";
				echo "$('#txt_yarn_count').val('".$count_arr[$row[csf("yarn_count_id")]]."');\n";
				echo "$('#txt_yarn_brand').val('".$brand_arr[$row[csf("brand")]]."');\n";
				echo "$('#txt_color').val('".$color_arr[$row[csf("color")]]."');\n";
				echo "$('#txt_returnable_balance_qnty').val('".$returnable_balance_qnty."');\n";
				echo "$('#txt_returnable_bl_value').val('".number_format($txt_returnable_bl_value,2,'.','')."');\n";
				echo "$('#txt_return_qnty').val('".$rcv_rtn_qnty."');\n";
				echo "disable_enable_fields( 'txt_wo_issue_adj', 1, '', '' );\n"; // disable true
			}
		}
		else if( $serviceGoodsName==1 && $debitBasis==5 )
		{
			$fabric_desc_arr = return_library_array("select id, item_description from product_details_master where item_category_id=13", "id", "item_description");

			$sql = "SELECT a.id as prog_no, a.program_date, c.prod_id as fab_prod_id, c.yarn_lot, c.uom, c.febric_description_id, c.grey_receive_qnty, c.width, c.yarn_count, c.gsm, c.stitch_length, c.color_id
			from ppl_planning_info_entry_dtls a,inv_receive_master b, pro_grey_prod_entry_dtls c where cast(a.id as varchar(4000)) = b.booking_no and b.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and b.entry_form = 2 and b.item_category = 13 and b.receive_basis=2 and b.company_id=$companyID and a.id=$receiveId ";	
			//echo $sql;die; 
			$result = sql_select($sql);

			if(count($result)==0)
			{
				?>
				<div class="alert alert-danger">Data not found! Please try again.</div>
				<?
				die();
			}


			$main_data_arr = array();
			$feb_desc_chk = array();
			$feb_desc_arr = array();
			foreach ($result as $row)
			{
				$main_data_arr[$row[csf("prog_no")]]['febric_description_id'] .= $row[csf("febric_description_id")].",";
				$main_data_arr[$row[csf("prog_no")]]['yarn_lot'] .= $row[csf("yarn_lot")].",";
				$main_data_arr[$row[csf("prog_no")]]['fab_prod_id'] .= $row[csf("fab_prod_id")].",";
				$main_data_arr[$row[csf("prog_no")]]['width'] .= $row[csf("width")].",";
				$main_data_arr[$row[csf("prog_no")]]['yarn_count'] .= $row[csf("yarn_count")].",";
				$main_data_arr[$row[csf("prog_no")]]['gsm'] .= $row[csf("gsm")].",";
				$main_data_arr[$row[csf("prog_no")]]['stitch_length'] .= $row[csf("stitch_length")].",";
				$main_data_arr[$row[csf("prog_no")]]['color_id'] .= $row[csf("color_id")].",";
				$main_data_arr[$row[csf("prog_no")]]['uom'] = $row[csf("uom")];
				$main_data_arr[$row[csf("prog_no")]]['grey_receive_qnty'] += $row[csf("grey_receive_qnty")];

				if($feb_desc_chk[$row[csf("febric_description_id")]] == "")
				{
					$feb_desc_chk[$row[csf("febric_description_id")]] = $row[csf("febric_description_id")];
					array_push($feb_desc_arr,$row[csf("febric_description_id")]);
				}
			}
			unset($result);
			//echo "<pre>";print_r($main_data_arr);

			$composition_arr = array();
			$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($feb_desc_arr,0,'a.id')."";
			$data_array = sql_select($sql_deter);
			if (count($data_array) > 0) {
				foreach ($data_array as $row) {

					if (array_key_exists($row[csf('id')], $composition_arr)) {
						$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
					} else {
						$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
					}
				}
				unset($data_array);
			}

			foreach($main_data_arr as $prog_no=>$row)
			{
				$reqsition_sql = sql_select("SELECT  requisition_no from ppl_yarn_requisition_entry where knit_id=$prog_no and status_active=1 and is_deleted=0 group by requisition_no");
				$requisition_no_chk = array();
				$requisition_no_arr = array();
				foreach ($reqsition_sql as $req_row) 
				{
					if($requisition_no_chk[$req_row[csf("requisition_no")]] == "")
					{
						$requisition_no_chk[$req_row[csf("requisition_no")]] = $req_row[csf("requisition_no")];
						array_push($requisition_no_arr,$req_row[csf("requisition_no")]);
					}
				}
			
				$yarn_issue_rslt = sql_select("SELECT c.id, c.brand, sum(b.cons_quantity) as issue_qty, sum(b.return_qnty) as returnable_qnty, sum(b.cons_rate) as cons_rate,count(b.cons_rate) as count_cons_rate 
				from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and a.entry_form=3 and b.prod_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.item_category=1 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($requisition_no_arr,0,'b.requisition_no')." group by c.id, c.brand");
				
				$yarn_issue_qnty = 0;
				$yarn_returnable_qnty = 0;
				$count_cons_rate = 0;
				$yarn_rate = 0;
				$yarn_brand = '';
				foreach ($yarn_issue_rslt as $issue_row)
				{
					$yarn_issue_qnty += $issue_row[csf('issue_qty')];
					$yarn_returnable_qnty += $issue_row[csf('returnable_qnty')];
					$yarn_issue_rate += $issue_row[csf('cons_rate')];
					$count_cons_rate += $issue_row[csf('count_cons_rate')];
					$yarn_brand .= $issue_row[csf('brand')].',';
				}
				unset($yarn_issue_rslt);
			
				$yarn_issue_rtn_rslt = sql_select("SELECT sum(b.cons_quantity) as return_qty 
				from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=9 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=4 and b.item_category=1 and a.receive_basis=3 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($requisition_no_arr,0,'b.requisition_no')." ");
				//echo $sql;die;
				$yarn_issue_rtn_qnty = 0;
				foreach ($yarn_issue_rtn_rslt as $rtn_row)
				{
					$yarn_issue_rtn_qnty += $rtn_row[csf('return_qty')];
				}
				unset($yarn_issue_rtn_rslt);

				$net_use_qnty = $yarn_issue_qnty-$yarn_issue_rtn_qnty;
				$rcv_balance = $net_use_qnty-$row["grey_receive_qnty"];
				$rtnable_balance_qnty = $yarn_returnable_qnty-$yarn_issue_rtn_qnty;
				$yarn_rate = $yarn_issue_rate*1/$count_cons_rate*1;

				
				$fabric_desc = '';
				$feb_description_id = '';
				$febric_description_ids = array_unique(explode(",",chop($row['febric_description_id'] ,",")));
				
				foreach ($febric_description_ids as $febric_desc_id)
				{
						
					if ($febric_desc_id == 0 || $febric_desc_id == "")
					{
						$fab_prod_ids = array_unique(explode(",",chop($row['fab_prod_id'] ,",")));
						foreach ($fab_prod_ids as $fab_prod_id)
						{
							$fabric_desc .= $fabric_desc_arr[$fab_prod_id].',';
						}
					}
					else
					{
						$fabric_desc .= $composition_arr[$febric_desc_id].",";
						$feb_description_id .=$febric_desc_id.",";
					}
				}

				$yarn_count_ids = array_unique(explode(",",chop($row['yarn_count'] ,",")));
				$yarn_count_names = '';
				foreach ($yarn_count_ids as $yarn_count_id)
				{
					$yarn_count_names .= $count_arr[$yarn_count_id].",";
				}

				$color_ids = array_unique(explode(",",chop($row['color_id'] ,",")));
				$color_name = '';
				$color_name_id = '';
				foreach ($color_ids as $color_id)
				{
					$color_name .= $color_arr[$color_id].",";
					$color_name_id .= $color_id.",";
				}
				
				$yarn_brand_ids = array_unique(explode(",",chop($yarn_brand ,",")));
				$brand_name = '';
				$yarn_brand_id = '';
				foreach ($yarn_brand_ids as $brand_id)
				{
					$brand_name .= $brand_arr[$brand_id].",";
					$yarn_brand_id .= $brand_id.",";
				}

				$fab_prod_id = implode(",",array_unique(explode(",",chop($row['fab_prod_id'] ,","))));
				$yarn_lot = implode(",",array_unique(explode(",",chop($row['yarn_lot'] ,","))));
				$finish_dia = implode(",",array_unique(explode(",",chop($row['width'] ,","))));
				$yarn_count_ids = implode(",",array_unique(explode(",",chop($row['yarn_count'] ,","))));
				$finish_gsm = implode(",",array_unique(explode(",",chop($row['gsm'] ,","))));
				$stitch_length = implode(",",array_unique(explode(",",chop($row['stitch_length'] ,","))));

				if($yarn_rate>=0)
				{
					$txt_returnable_bl_value = $yarn_rate*$rtnable_balance_qnty;
					$yarn_rate = $yarn_rate;
				}
				else
				{
					$yarn_rate = 0;
				}
				
				
				echo "$('#txt_item_description').val('".chop($fabric_desc,',')."');\n";
				echo "$('#txt_item_description_id').val('".chop($feb_description_id,',')."');\n";
				echo "$('#txt_rcvissue_id').val('".$prog_no."');\n";
				echo "$('#txt_fab_prod_id').val('".$fab_prod_id."');\n";
				echo "$('#txt_yarn_lot').val('".$yarn_lot."');\n";
				echo "$('#txt_fin_dia').val('".$finish_dia."');\n";
				echo "$('#txt_yarn_brand').val('".chop($brand_name,',')."');\n";
				echo "$('#txt_yarn_brand_id').val('".chop($yarn_brand_id,',')."');\n";
				echo "$('#txt_yarn_count').val('".chop($yarn_count_names,',')."');\n";
				echo "$('#txt_yarn_count_id').val('".$yarn_count_ids."');\n";
				echo "$('#txt_finish_gsm').val('".chop($finish_gsm,',')."');\n";
				echo "$('#txt_sl').val('".chop($stitch_length,',')."');\n";
				echo "$('#txt_color').val('".chop($color_name,',')."');\n";
				echo "$('#txt_color_id').val('".chop($color_name_id,',')."');\n";
				echo "$('#cbo_uom').val('".$row["uom"]."');\n";
				echo "$('#txt_fabric_receive').val('".$row["grey_receive_qnty"]."');\n";
				echo "$('#txt_yarn_issue_qty').val('".$yarn_issue_qnty."');\n";
				echo "$('#txt_returnable_qnty').val('".$yarn_returnable_qnty."');\n";
				echo "$('#txt_return_qnty').val('".$yarn_issue_rtn_qnty."');\n";
				echo "$('#txt_net_used_qty').val('".$net_use_qnty."');\n";
				echo "$('#txt_receive_balance').val('".$rcv_balance."');\n";
				echo "$('#txt_returnable_balance_qnty').val('".$rtnable_balance_qnty."');\n";
				echo "$('#txt_yarn_rate').val('".number_format($yarn_rate,2)."');\n";
				echo "$('#txt_returnable_bl_value').val('".$txt_returnable_bl_value."');\n";

				echo "disable_enable_fields( 'txt_wo_issue_adj', 1, '', '' );\n"; // disable true
			}
		}
		else if( $serviceGoodsName==1 && $debitBasis==2 )
		{

			$sql = "SELECT a.id, a.issue_number, b.requisition_no, b.prod_id, b.store_id, b.cons_uom, sum(b.cons_quantity) as issue_qnty, sum(b.return_qnty) as returnable_qnty, b.no_of_bags, b.supplier_id, b.cons_rate, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type, c.color, c.lot, c.is_within_group, c.brand from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=3 and a.item_category=1 and b.item_category=1 and a.issue_basis=3 and b.receive_basis=3 and a.issue_purpose=1 and a.company_id=$companyID and a.id=$receiveId and b.prod_id=$prodId and b.requisition_no=$req_no group by a.id, a.issue_number, b.requisition_no, b.prod_id, b.store_id, b.cons_uom, b.no_of_bags, b.supplier_id, b.cons_rate, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type, c.color, c.lot, c.is_within_group, c.brand";

			//echo $sql;die;

			$result = sql_select($sql);

			if(count($result)==0)
			{
				?>
				<div class="alert alert-danger">Data not found! Please try again.</div>
				<?
				die();
			}

			foreach($result as $row)
			{
				$requisition_no =  $row[csf("requisition_no")];
				$prod_id =  $row[csf("prod_id")];

				$knitting_sql = sql_select("SELECT a.knit_id,a.prod_id,c.width, c.gsm, c.stitch_length, c.color_id 
				from ppl_yarn_requisition_entry a, inv_receive_master b, pro_grey_prod_entry_dtls c where  cast(a.knit_id as varchar(4000)) = b.booking_no and b.id=c.mst_id and a.requisition_no=$requisition_no and a.prod_id=$prod_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
				group by a.knit_id,a.prod_id,c.width, c.gsm, c.stitch_length, c.color_id");

				$color_ids = '';
				$finish_dia = '';
				foreach ($knitting_sql as $knit_row) 
				{
					$color_ids .= $knit_row[csf('color_id')].'';
					$finish_dia .= $knit_row[csf('width')].'';
					$finish_gsm .= $knit_row[csf('gsm')].'';
					$stitch_length .= $knit_row[csf('stitch_length')].'';
				}

				$color_ids = array_unique(explode(",",chop($color_ids ,",")));
				$color_name = '';
				$color_name_id = '';
				foreach ($color_ids as $color_id)
				{
					$color_name .= $color_arr[$color_id].",";
					$color_name_id .= $color_id.",";
				}
				$finish_dia = implode(",",array_unique(explode(",",chop($finish_dia ,","))));
				$finish_gsm = implode(",",array_unique(explode(",",chop($finish_gsm ,","))));
				$stitch_length = implode(",",array_unique(explode(",",chop($stitch_length ,","))));

				$yarn_issue_rtn_rslt = sql_select("SELECT sum(b.cons_quantity) as return_qty 
				from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=9 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=4 and b.item_category=1 and a.receive_basis=3 and b.status_active=1 and b.is_deleted=0 and a.issue_id=$receiveId and b.prod_id=$prodId and a.requisition_no=$req_no and b.requisition_no=$req_no ");
				//echo $sql;die;
				$yarn_issue_rtn_qnty = 0;
				foreach ($yarn_issue_rtn_rslt as $rtn_row)
				{
					$yarn_issue_rtn_qnty += $rtn_row[csf('return_qty')];
				}
				unset($yarn_issue_rtn_rslt);

				$composition_string = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')];
				if ($row[csf('yarn_comp_type2nd')] != 0)
					$composition_string .= " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')];

				$net_use_qnty = $row[csf("issue_qnty")]-$yarn_issue_rtn_qnty;
				$rtnable_balance_qnty = $row[csf("returnable_qnty")]-$yarn_issue_rtn_qnty;	
				$returnable_bl_value = $row[csf("cons_rate")]*$rtnable_balance_qnty;
				
				echo "$('#txt_item_description').val('".chop($composition_string,',')."');\n";
				echo "$('#txt_rcvissue_id').val('".$row[csf('id')]."');\n";
				echo "$('#txt_prod_id').val('".$row[csf('prod_id')]."');\n";
				echo "$('#txt_requisition_no').val('".$row[csf('requisition_no')]."');\n";
				echo "$('#txt_yarn_lot').val('".$row[csf('lot')]."');\n";
				echo "$('#txt_yarn_brand_id').val('".$row[csf('brand')]."');\n";
				echo "$('#txt_yarn_brand').val('".$brand_arr[$row[csf('brand')]]."');\n";
				echo "$('#txt_yarn_count_id').val('".$row[csf('yarn_count_id')]."');\n";
				echo "$('#txt_yarn_count').val('".$count_arr[$row[csf('yarn_count_id')]]."');\n";
				echo "$('#txt_color').val('".chop($color_name,',')."');\n";
				echo "$('#txt_color_id').val('".chop($color_name_id,',')."');\n";
				echo "$('#txt_fin_dia').val('".$finish_dia."');\n";
				echo "$('#txt_finish_gsm').val('".chop($finish_gsm,',')."');\n";
				echo "$('#txt_sl').val('".chop($stitch_length,',')."');\n";
				echo "$('#cbo_uom').val('".$row[csf("cons_uom")]."');\n";
				echo "$('#txt_yarn_issue_qty').val('".$row[csf("issue_qnty")]."');\n";
				echo "$('#txt_returnable_qnty').val('".$row[csf("returnable_qnty")]."');\n";
				echo "$('#txt_return_qnty').val('".$yarn_issue_rtn_qnty."');\n";
				echo "$('#txt_net_used_qty').val('".$net_use_qnty."');\n";
				echo "$('#txt_returnable_balance_qnty').val('".$rtnable_balance_qnty."');\n";
				echo "$('#txt_yarn_rate').val('".$row[csf("cons_rate")]."');\n";
				echo "$('#txt_returnable_bl_value').val('".number_format($returnable_bl_value,2,'.','')."');\n";

				echo "disable_enable_fields( 'txt_wo_issue_adj', 1, '', '' );\n"; // disable true
			}
		}
	}
	
	
	exit();
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();
	if($db_type==0)	{ mysql_query("BEGIN"); }

	$txt_rcvissue_id = str_replace("'","",$txt_rcvissue_id);
	$txt_prod_id = str_replace("'","",$txt_prod_id);
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	$debit_note_against = str_replace("'","",$cbo_debit_note_against);
	$service_goods_name = str_replace("'","",$cbo_service_goods_name);
	$basis = str_replace("'","",$cbo_basis);
	$requisition_no = str_replace("'","",$txt_requisition_no);
	
	if( $operation==0 ) // Insert Here
	{
		//---------------Check Duplicate product  ------------------------//
		
		if($debit_note_against==1)
		{
			if($service_goods_name==16 && $basis==3)
			{
				$duplicate = is_duplicate_field("b.id","debit_note_entry_mst a, debit_note_entry_dtls b","a.id=b.mst_id and b.recv_issue_id=$txt_rcvissue_id and b.prod_id=$txt_prod_id and b.status_active=1 and b.is_deleted=0 and a.debit_note_against=1 and a.goods_name=16  and a.basis=3");
				if($duplicate==1)
				{
					echo "20**This Selected Data already saved. Please Select Another Data.";
					disconnect($con);
					die;
				}
			}
			else if($service_goods_name==16 && $basis==1)
			{
				echo "20**This Basis Under Construction...";
				disconnect($con);
				die;
			}
			else if($service_goods_name==16 && $basis==4)
			{
				echo "20**This Basis Under Construction...";
				disconnect($con);
				die;
			}
			else if($service_goods_name==1 && $basis==5)
			{
				$duplicate = is_duplicate_field("b.id","debit_note_entry_mst a, debit_note_entry_dtls b","a.id=b.mst_id and b.recv_issue_id=$txt_rcvissue_id and b.status_active=1 and b.is_deleted=0 and a.debit_note_against=1 and a.goods_name=1  and a.basis=5");
				if($duplicate==1)
				{
					echo "20**This Selected Data already saved. Please Select Another Data.";
					disconnect($con);
					die;
				}
			}
			else if($service_goods_name==1 && $basis==2)
			{
				$duplicate = is_duplicate_field("b.id","debit_note_entry_mst a, debit_note_entry_dtls b","a.id=b.mst_id and b.recv_issue_id=$txt_rcvissue_id and b.prod_id=$txt_prod_id and b.requisition_no=$requisition_no and b.status_active=1 and b.is_deleted=0 and a.debit_note_against=1 and a.goods_name=1  and a.basis=2");
				if($duplicate==1)
				{
					echo "20**This Selected Data already saved. Please Select Another Data.";
					disconnect($con);
					die;
				}
			}
			else if($service_goods_name==2 && $basis==1)
			{
				echo "20**This Basis Under Construction...";
				disconnect($con);
				die;
			}
			else if($service_goods_name==2 && $basis==3)
			{
				echo "20**This Basis Under Construction...";
				disconnect($con);
				die;
			}
			else if($service_goods_name==2 && $basis==4)
			{
				echo "20**This Basis Under Construction...";
				disconnect($con);
				die;
			}
		}
		else
		{
			echo "20**Service Related Task Under Construction...";
			disconnect($con);
			die;
		}
		

		//master table entry here START---------------------------------------//
		
		if(str_replace("'","",$txt_system_no)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";

			$id = return_next_id_by_sequence("DEBIT_NOTE_ENTRY_MST_PK_SEQ", "debit_note_entry_mst", $con);
			
			$new_debit_number = explode("*", return_next_id_by_sequence("DEBIT_NOTE_ENTRY_MST_PK_SEQ", "debit_note_entry_mst",$con,1,$cbo_company_id,'DNE',633,date("Y",time()) ));
			//echo "20**".$new_debit_number[1];die;
			$field_array="id, sys_number_prefix, sys_number_prefix_num, sys_number, entry_form, company_id, location_id, debit_note_against, goods_name, within_group, working_company_id, basis, wo_issue_id, wo_issue_no, challan_no, goods_rcv_challan_no, debit_note_to,debit_note_date, remarks, inserted_by, insert_date";
			$data_array="(".$id.",'".$new_debit_number[1]."','".$new_debit_number[2]."','".$new_debit_number[0]."',633,".$cbo_company_id.",".$cbo_location_name.",".$cbo_debit_note_against.",".$cbo_service_goods_name.",".$cbo_within_group.",".$cbo_working_company.",".$cbo_basis.",".$txt_wo_issue_adj_id.",".$txt_wo_issue_adj.",".$txt_issue_challan.",".$txt_goods_rcv_challan.",".$cbo_debit_note_to.",".$txt_debit_note_date.",".$txt_remarks.",'".$user_id."','".$pc_date_time."')";
		}
		else
		{
			$new_debit_number[0] = str_replace("'","",$txt_system_no);
			$id=str_replace("'","",$txt_mst_id);
		
			$field_array="entry_form*company_id*location_id*debit_note_against*goods_name*within_group*working_company_id*basis*wo_issue_id*wo_issue_no*challan_no*goods_rcv_challan_no*debit_note_to*debit_note_date*remarks*updated_by*update_date";
			$data_array="633*".$cbo_company_id."*".$cbo_location_name."*".$cbo_debit_note_against."*".$cbo_service_goods_name."*".$cbo_within_group."*".$cbo_working_company."*".$cbo_basis."*".$txt_wo_issue_adj_id."*".$txt_wo_issue_adj."*".$txt_issue_challan."*".$txt_goods_rcv_challan."*".$cbo_debit_note_to."*".$txt_debit_note_date."*".$txt_remarks."*'".$user_id."'*'". $pc_date_time . "'";
		}

		//master table entry here END---------------------------------------//

		//Details table insert here START--------------------------------//
		$dtlsid = return_next_id_by_sequence("DEBIT_NOTE_ENTRY_DTLS_PK_SEQ", "debit_note_entry_dtls", $con);
		$field_array_dtls = "id,mst_id,entry_form,recv_issue_id,prod_id,process_name,knitting_charge,process_loss_deduc_qnty,knitting_qnty,debit_note_qnty,dyeing_charge,total_debit_amount,dyeing_qnty,recv_qnty,issue_qnty,net_used_qnty,returnable_qnty,return_qnty,fabric_recv_qnty,recv_balance,returnable_balance_qnty,rate,returnable_balance_val,process_loss_deduc_val,debit_note_val,total_knitting_cost,total_dyeing_cost,finish_dia,gsm,stich_length,fabric_fault,febric_description_id,febric_description,yarn_lot,yarn_brand,yarn_brand_id,yarn_count,yarn_count_id,color,color_id,uom,fab_prod_id,requisition_no, inserted_by, insert_date";

		$txt_prod_id = ( $txt_prod_id =="")?0:$txt_prod_id;

		$data_array_dtls= "(".$dtlsid.",".$id.",633,".$txt_rcvissue_id.",".$txt_prod_id.",".$txt_process_name.",".$txt_knit_charge.",".$txt_plodq.",".$txt_knit_qnty.",".$txt_debit_note_qnty.",".$txt_dying_charge.",".$txt_debit_amount.",".$txt_dyeing_qnty.",".$txt_yarn_receive_qty.",".$txt_yarn_issue_qty.",".$txt_net_used_qty.",".$txt_returnable_qnty.",".$txt_return_qnty.",".$txt_fabric_receive.",".$txt_receive_balance.",".$txt_returnable_balance_qnty.",".$txt_yarn_rate.",".$txt_returnable_bl_value.",".$txt_process_loss_value.",".$txt_debit_note_value.",".$txt_total_knitting_cost.",".$txt_total_dyeing_cost.",".$txt_fin_dia.",".$txt_finish_gsm.",".$txt_sl.",".$txt_fab_fault.",".$txt_item_description_id.",".$txt_item_description.",".$txt_yarn_lot.",".$txt_yarn_brand.",".$txt_yarn_brand_id.",".$txt_yarn_count.",".$txt_yarn_count_id.",".$txt_color.",".$txt_color_id.",".$cbo_uom.",".$txt_fab_prod_id.",".$txt_requisition_no.",'".$user_id."','".$pc_date_time."')";

	
 		//Details table insert here END ---------------------------------//

		$prodUpdate=$rID=$dtlsrID=$proportQ=$rID_allocation_mst=$rID_allocation_dtls=$rID_de=$rID_dep=$storeRID=true;

		if(str_replace("'","",$txt_system_no)=="")
		{
			//echo "10** insert into debit_note_entry_mst ($field_array) values $data_array";die;
			$rID=sql_insert("debit_note_entry_mst",$field_array,$data_array,0);
		}
		else
		{
			
			$rID = sql_update("debit_note_entry_mst",$field_array,$data_array,"id",$id,0);
		}
		 
		//echo "10** insert into debit_note_entry_dtls ($field_array_dtls) values $data_array_dtls";die;

		$dtlsrID = sql_insert("debit_note_entry_dtls",$field_array_dtls,$data_array_dtls,0);

		/* oci_rollback($con);
		echo "10**".$rID." && ".$dtlsrID;oci_rollback($con);disconnect($con);die;	 */ 

		if($db_type==0)
		{
			if( $rID && $dtlsrID)
			{
				mysql_query("COMMIT");
				echo "0**".$id."**".$new_debit_number[0];
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$new_debit_number[0];
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if( $rID && $dtlsrID)
			{
				oci_commit($con);
				echo "0**".$id."**".$new_debit_number[0];
			}
			else
			{
				oci_rollback($con);
				echo "10**".$new_debit_number[0];
			}
		}

		disconnect($con);
		die;

	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		
		//check update id
		if( str_replace("'","",$update_id) == ""  )
		{
			echo "15";
			disconnect($con);
			exit();
		}

		//master table UPDATE here START----------------------//
		$field_array_upd="location_id*within_group*working_company_id*challan_no*goods_rcv_challan_no*debit_note_to*debit_note_date*remarks*updated_by*update_date";
		$data_array_upd="".$cbo_location_name."*".$cbo_within_group."*".$cbo_working_company."*".$txt_issue_challan."*".$txt_goods_rcv_challan."*".$cbo_debit_note_to."*".$txt_debit_note_date."*".$txt_remarks."*'".$user_id."'*'". $pc_date_time . "'";
		//echo "10**".$data_array_upd;die;
		//master table entry here END---------------------------------------//
		
		
		//Details table update here START--------------------------------//

		$field_array_upd_dtls = "recv_issue_id*prod_id*process_name*knitting_charge*process_loss_deduc_qnty*knitting_qnty*debit_note_qnty*dyeing_charge*total_debit_amount*dyeing_qnty*recv_qnty*issue_qnty*net_used_qnty*returnable_qnty*return_qnty*fabric_recv_qnty*recv_balance*returnable_balance_qnty*rate*returnable_balance_val*process_loss_deduc_val*debit_note_val*total_knitting_cost*stich_length*gsm*fabric_fault*finish_dia*total_dyeing_cost*febric_description_id*febric_description*yarn_lot*yarn_brand*yarn_brand_id*yarn_count*yarn_count_id*color*color_id*uom*fab_prod_id*requisition_no*updated_by*update_date";

		$data_array_upd_dtls= "".$txt_rcvissue_id."*".$txt_prod_id."*".$txt_process_name."*".$txt_knit_charge."*".$txt_plodq."*".$txt_knit_qnty."*".$txt_debit_note_qnty."*".$txt_dying_charge."*".$txt_debit_amount."*".$txt_dyeing_qnty."*".$txt_yarn_receive_qty."*".$txt_yarn_issue_qty."*".$txt_net_used_qty."*".$txt_returnable_qnty."*".$txt_return_qnty."*".$txt_fabric_receive."*".$txt_receive_balance."*".$txt_returnable_balance_qnty."*".$txt_yarn_rate."*".$txt_returnable_bl_value."*".$txt_process_loss_value."*".$txt_debit_note_value."*".$txt_total_knitting_cost."*".$txt_sl."*".$txt_finish_gsm."*".$txt_fab_fault."*".$txt_fin_dia."*".$txt_total_dyeing_cost."*".$txt_item_description_id."*".$txt_item_description."*".$txt_yarn_lot."*".$txt_yarn_brand."*".$txt_yarn_brand_id."*".$txt_yarn_count."*".$txt_yarn_count_id."*".$txt_color."*".$txt_color_id."*".$cbo_uom."*".$txt_fab_prod_id."*".$txt_requisition_no."*'".$user_id."'*'".$pc_date_time."'";
		
 	
 		//Details table update here END ---------------------------------//

		$id=str_replace("'","",$txt_mst_id);
		$rID=sql_update("debit_note_entry_mst",$field_array_upd,$data_array_upd,"id",$id,0);
		
		$dtlsrID = sql_update("debit_note_entry_dtls",$field_array_upd_dtls,$data_array_upd_dtls,"id",$update_id,0);

		//echo "10**".$rID." && ".$dtlsrID;oci_rollback($con);disconnect($con);die;

		if($db_type==0)
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");
				echo "1**".$id."**".str_replace("'","",$txt_system_no);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_system_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $dtlsrID)
			{
				oci_commit($con);
				echo "1**".$id."**".str_replace("'","",$txt_system_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_system_no);
			}
		}

		disconnect($con);
		die;


	}
}
if($action=="child_form_input_data")
{
	$data=explode('**',$data);
	$id = $data[0];
	$recvIssueId = $data[1];
	$prod_id = $data[2];


	$sql = "SELECT id as tr_id,recv_issue_id,prod_id,fab_prod_id,process_name,knitting_charge,process_loss_deduc_qnty,knitting_qnty,debit_note_qnty,dyeing_charge,total_debit_amount,dyeing_qnty,recv_qnty,issue_qnty,net_used_qnty,returnable_qnty,return_qnty,fabric_recv_qnty,recv_balance,returnable_balance_qnty,rate,returnable_balance_val,process_loss_deduc_val,debit_note_val,total_knitting_cost,total_dyeing_cost,finish_dia,gsm,stich_length,fabric_fault, yarn_lot, febric_description as product_name_details, uom, yarn_brand, yarn_brand_id, yarn_count, yarn_count_id, color_id, color, requisition_no, febric_description_id
	from  debit_note_entry_dtls 
	where entry_form=633 and status_active=1 and is_deleted=0 and id=$id ";

	$result = sql_select($sql);

	foreach($result as $row)
	{
		echo "$('#txt_item_description').val('".$row[csf("product_name_details")]."');\n";
		echo "$('#txt_item_description_id').val('".$row[csf("febric_description_id")]."');\n";
		echo "$('#txt_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#txt_fab_prod_id').val('".$row[csf("fab_prod_id")]."');\n";
		echo "$('#txt_rcvissue_id').val('".$row[csf("recv_issue_id")]."');\n";
		echo "$('#txt_requisition_no').val('".$row[csf("requisition_no")]."');\n";
		echo "$('#txt_yarn_lot').val('".$row[csf("yarn_lot")]."');\n";
		echo "$('#txt_fin_dia').val('".$row[csf("finish_dia")]."');\n";
		echo "$('#txt_yarn_brand').val('".$row[csf("yarn_brand")]."');\n";
		echo "$('#txt_yarn_brand_id').val('".$row[csf("yarn_brand_id")]."');\n";
		echo "$('#txt_yarn_count').val('".$row[csf("yarn_count")]."');\n";
		echo "$('#txt_yarn_count_id').val('".$row[csf("yarn_count_id")]."');\n";
		echo "$('#txt_color').val('".$row[csf("color")]."');\n";
		echo "$('#txt_color_id').val('".$row[csf("color_id")]."');\n";
		echo "$('#txt_finish_gsm').val('".$row[csf("gsm")]."');\n";
		echo "$('#txt_sl').val('".$row[csf("stich_length")]."');\n";
		echo "$('#txt_process_name').val('".$row[csf("process_name")]."');\n";
		echo "$('#txt_fab_fault').val('".$row[csf("fabric_fault")]."');\n";
		echo "$('#txt_knit_charge').val('".$row[csf("knitting_charge")]."');\n";
		echo "$('#txt_plodq').val('".$row[csf("process_loss_deduc_qnty")]."');\n";
		echo "$('#txt_knit_qnty').val('".$row[csf("knitting_qnty")]."');\n";
		echo "$('#txt_debit_note_qnty').val('".$row[csf("debit_note_qnty")]."');\n";
		echo "$('#txt_dying_charge').val('".$row[csf("dyeing_charge")]."');\n";
		echo "$('#txt_debit_amount').val('".number_format($row[csf("total_debit_amount")],2,'.','')."');\n";
		echo "$('#txt_dyeing_qnty').val('".$row[csf("dyeing_qnty")]."');\n";
		echo "$('#txt_yarn_receive_qty').val('".$row[csf("recv_qnty")]."');\n";
		echo "$('#txt_yarn_rate').val('".number_format($row[csf("rate")],2,'.','')."');\n";
		echo "$('#txt_yarn_issue_qty').val('".$row[csf("issue_qnty")]."');\n";
		echo "$('#txt_net_used_qty').val('".$row[csf("net_used_qnty")]."');\n";
		echo "$('#txt_returnable_qnty').val('".$row[csf("returnable_qnty")]."');\n";
		echo "$('#txt_return_qnty').val('".$row[csf("return_qnty")]."');\n";
		echo "$('#txt_returnable_balance_qnty').val('".$row[csf("returnable_balance_qnty")]."');\n";
		echo "$('#txt_returnable_bl_value').val('".number_format($row[csf("returnable_balance_val")],2,'.','')."');\n";
		echo "$('#txt_process_loss_value').val('".number_format($row[csf("process_loss_deduc_val")],2,'.','')."');\n";
		echo "$('#txt_debit_note_value').val('".number_format($row[csf("debit_note_val")],2,'.','')."');\n";
		echo "$('#txt_total_knitting_cost').val('".number_format($row[csf("total_knitting_cost")],2,'.','')."');\n";
		echo "$('#txt_total_dyeing_cost').val('".number_format($row[csf("total_dyeing_cost")],2,'.','')."');\n";
		echo "$('#cbo_uom').val('".$row[csf("uom")]."');\n";
		echo "$('#update_id').val('".$row[csf("tr_id")]."');\n";
		echo "disable_enable_fields( 'txt_item_description', 1, '', '' );\n"; // disable true
	}

	echo "set_button_status(1, permission, 'fnc_debit_note_entry',1,1);\n";
	exit();
}

if($action=="populate_master_from_data")
{
	
	$sql = "SELECT id, sys_number, entry_form, company_id, location_id, debit_note_against, goods_name, within_group, working_company_id, basis, wo_issue_id,wo_issue_no, challan_no, goods_rcv_challan_no, debit_note_to,debit_note_date, remarks from debit_note_entry_mst where id=$data";
	$res = sql_select($sql);
	

	foreach($res as $row)
	{
		echo "set_button_status(0, permission, 'fnc_debit_note_entry',1,1);";

		echo "$('#txt_mst_id').val('".$row[csf("id")]."');\n";
		echo "$('#txt_system_no').val('".$row[csf("sys_number")]."');\n";
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		echo "$('#cbo_debit_note_against').val('".$row[csf("debit_note_against")]."');\n";
		echo "load_drop_down( 'requires/debit_note_entry_controller', ".$row[csf("debit_note_against")].", 'load_drop_down_goods_name', 'service_goods_placeholder_td' );\n";
		echo "$('#cbo_service_goods_name').val('".$row[csf("goods_name")]."');\n";
		echo "load_drop_down( 'requires/debit_note_entry_controller', ".$row[csf("goods_name")].", 'load_drop_down_basis', 'basis_td' );\n";
		echo "$('#cbo_within_group').val('".$row[csf("within_group")]."');\n";
		echo "$('#cbo_working_company').val('".$row[csf("working_company_id")]."');\n";
		echo "$('#cbo_basis').val('".$row[csf("basis")]."');\n";
		echo "active_inactive('".$row[csf("basis")]."');\n";
		echo "$('#txt_wo_issue_adj').val('".$row[csf("wo_issue_no")]."');\n";
		echo "$('#txt_wo_issue_adj_id').val('".$row[csf("wo_issue_id")]."');\n";
		echo "$('#txt_issue_challan').val('".$row[csf("challan_no")]."');\n";
		echo "$('#txt_goods_rcv_challan').val('".$row[csf("goods_rcv_challan_no")]."');\n";
		echo "$('#cbo_debit_note_to').val('".$row[csf("debit_note_to")]."');\n";
		echo "$('#txt_debit_note_date').val('".change_date_format($row[csf("debit_note_date")])."');\n";
		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		echo "disable_enable_fields( 'cbo_company_id*cbo_debit_note_against*cbo_service_goods_name*cbo_basis*txt_wo_issue_adj', 1, '', '' );\n"; // disable true
	}
	exit();
}

if($action=="show_dtls_list_view")
{
	$count_arr = return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

	$sql = "SELECT a.id as mst_id, a.sys_number, a.sys_number_prefix_num, a.debit_note_date, a.debit_note_against,a.goods_name,a.basis, b.id as tr_id, b.recv_issue_id,b.prod_id,b.fab_prod_id, b.febric_description, b.yarn_lot, b.uom, b.debit_note_qnty, b.total_debit_amount 
	from  debit_note_entry_mst a, debit_note_entry_dtls b
	where a.id=b.mst_id and a.entry_form=633 and b.entry_form=633 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$data ";
	//echo $sql;

	$result   = sql_select($sql);
	$i=1;
	$rettotalQnty=0;
	$rcvtotalQnty=0;
	$rejtotalQnty=0;
	$totalAmount=0;
	?>
	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" style="width:980px" rules="all">
		<thead>
			<tr>
				<th>SL</th>
				<th>System No</th>
				<th>Item Description</th>
				<th>Product ID</th>
				<th>Lot</th>
				<th>UOM</th>
				<th>Debit Qty</th>
				<th>Debit Amount</th>

			</tr>
		</thead>
		<tbody>
			<?
			foreach($result as $row){
				if($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
				
				if($row[csf("debit_note_against")]==1)
				{
					if($row[csf("goods_name")]==16 && $row[csf("basis")]==3)
					{
						$prod_id = $row[csf("prod_id")];
					}
					else if($row[csf("goods_name")]==1 && $row[csf("basis")]==5)
					{
						$prod_id = $row[csf("fab_prod_id")];
					}
					else if($row[csf("goods_name")]==1 && $row[csf("basis")]==2)
					{
						$prod_id = $row[csf("prod_id")];
					}
				}	
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("tr_id")]."**".$row[csf("recv_issue_id")]."**".$prod_id;?>","child_form_input_data","requires/debit_note_entry_controller")' style="cursor:pointer" >
					<td width="30"><? echo $i; ?></td>
					<td width="100" align="center"><p><? echo $row[csf("sys_number")]; ?></p></td>
					<td width="250" align="center"><p><? echo $row[csf("febric_description")]; ?></p></td>
					<td width="60" align="center"><p><? echo $prod_id; ?></p></td>
					<td width="70" align="center"><p><? echo $row[csf("yarn_lot")]; ?></p></td>
					<td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("uom")]]; ?></p></td>
					<td width="80" align="center"><? echo number_format($row[csf("debit_note_qnty")],2,'.',''); ?></td>
					<td width="80" align="center"><? echo number_format($row[csf("total_debit_amount")],2,'.',''); ?></td>
				</tr>
				<? $i++; } ?>
				
			</tbody>
		</table>
		<?
		exit();
}


if($action='load_drop_down_goods_name')
{
	if($data == 1){
		echo create_drop_down("cbo_service_goods_name", 170, $yarn_issue_purpose, '', 1 , "--Select--","","load_drop_down( 'requires/debit_note_entry_controller', this.value, 'load_drop_down_basis', 'basis_td' );","","1,2,16");
	}else{
		//$service_arr = $service_type;
		echo create_drop_down("cbo_service_goods_name", 170, $service_arr, '', 1 , "--Select--","","load_drop_down( 'requires/debit_note_entry_controller', this.value 'load_drop_down_basis', 'basis_td");
	}
	
}







