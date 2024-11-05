<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_id = $_SESSION['logic_erp']["user_id"];
//--------------------------- Start-------------------------------------------
$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
//$sample_arr=return_library_array( "select id, sample_name from lib_sample",'id','sample_name');
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
$count_arr=return_library_array( "Select id, yarn_count from  lib_yarn_count where  status_active=1",'id','yarn_count');

if($action=="load_drop_down_supplier")
{
	$dataEx = explode("_", $data);
	if($dataEx[0]==5 || $dataEx[0]==3){
		echo create_drop_down( "cbo_supplier_name", 160, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Company --", "", "",0,"" );
	}
	else
	{
		if($dataEx[2]==15)
		{
			echo create_drop_down( "cbo_supplier_name", 160, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company c where a.id=c.supplier_id  and a.status_active =1 and a.id in(select supplier_id from lib_supplier_party_type where party_type in(93)) group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"","");// or a.id in(select supplier_id from lib_supplier_party_type where party_type=21)
		}
		else
		{
			echo create_drop_down( "cbo_supplier_name", 160, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company c where a.id=c.supplier_id  and a.status_active =1 and a.id in(select supplier_id from lib_supplier_party_type where party_type in(2,21,93)) group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"","");// or a.id in(select supplier_id from lib_supplier_party_type where party_type=21)
		}
	}
	exit();
}
if($action=="load_drop_down_supplier_new")
{
	$dataEx = explode("_", $data);
	if($dataEx[0]==5 || $dataEx[0]==3){
		echo create_drop_down( "cbo_supplier_name", 160, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Company --", "", "",0,"" );
	}
	else
	{
		if($dataEx[2]==15)
		{
			$sql = "SELECT a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company c where a.id=c.supplier_id  and a.status_active =1 and a.id in(select supplier_id from lib_supplier_party_type where party_type in(93)) group by a.id,a.supplier_name  UNION ALL SELECT a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company c , wo_yarn_dyeing_mst d  where a.id=c.supplier_id  and a.status_active IN(1,3) and a.id in(select supplier_id from lib_supplier_party_type where party_type in(93))  and d.supplier_id = a.id group by a.id,a.supplier_name order by supplier_name";
			echo create_drop_down( "cbo_supplier_name", 160, "$sql","id,supplier_name", 1, "--Select Supplier--",$selected,"","");// or a.id in(select supplier_id from lib_supplier_party_type where party_type=21)
		}
		else
		{
			 $sql = "SELECT DISTINCT a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company c where a.id=c.supplier_id  and a.status_active =1 and a.id in(select supplier_id from lib_supplier_party_type where party_type in(2,21,93)) group by a.id,a.supplier_name UNION ALL SELECT DISTINCT a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company c ,wo_yarn_dyeing_mst d where a.id=d.supplier_id  and a.status_active IN(1,3) and a.id in(select supplier_id from lib_supplier_party_type where party_type in(2,21,93)) group by a.id,a.supplier_name   order by supplier_name  ";
			echo create_drop_down( "cbo_supplier_name", 160, "$sql","id,supplier_name", 1, "--Select Supplier--",$selected,"","");// or a.id in(select supplier_id from lib_supplier_party_type where party_type=21)
		}
	}
	exit();
}

if($action=="load_drop_down_inhouse_company")
{
	list($comapny,$party_type,$paymode_type)=explode('_',$data);
	if ($paymode_type==3 || $paymode_type==5) {
		echo create_drop_down( "cbo_supplier_name", 160, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $selected, "",0 );
	}
	else
	{
		if($party_type==15 || $party_type==50 || $party_type==51){$party_type=' and b.party_type in(93)';}//Twisting
		else if($party_type==38){$party_type=' and b.party_type in(94)';}//Re-Waxing
		else{$party_type=' and b.party_type in(93,94)';}

		echo create_drop_down( "cbo_supplier_name", 160, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and c.tag_company=$comapny and a.status_active =1 $party_type group by a.id,a.supplier_name order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "get_php_form_data(this.value, 'set_attention', 'requires/yarn_service_work_order_controller' );",0 );
	}
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 145, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_buyer_withInGroup")
{
	list($within_group,$company_id)=explode('_',$data);
	if($within_group == 2)
	{
		echo create_drop_down( "cbo_buyer_name", 145, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	}
	else
	{
		echo create_drop_down( "cbo_buyer_name", 145, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", "id,company_name",1, "-- Select Buyer --", $company_id,"",0);
	}

	exit();
}
if($action=="button_setting")
{
	echo "$('#print_booking').hide();";
	echo "$('#print_booking2').hide();";
	echo "$('#print_booking5').hide();";
	echo "$('#print_booking6').hide();";

	$sql = "select b.button_id from lib_report_template a, lib_report_template_details b where a.template_name = $data and a.module_id = 2 and a.report_id = 14 and a.id = b.mst_id";
	$sql_result = sql_select($sql);
	// echo "<pre>";
	// print_r($sql_result);
	//print_r($report_id);
	foreach($sql_result as $res){
		if($res[csf('button_id')]==79 )
		{
			echo "$('#print_booking').show();";
		}
		elseif ($res[csf('button_id')]==80 )
		{
			echo "$('#print_booking2').show();";
		}
		elseif ($res[csf('button_id')]==867 )
		{
			echo "$('#print_booking5').show();";
		}
		elseif ($res[csf('button_id')]==868 )
		{
			echo "$('#print_booking6').show();";
		}
	}
	exit();		
} 

if ($action=="load_variable_settings")
{
	$sql_result = sql_select("select variable_list, color_from_library from variable_order_tracking where company_name=$data and variable_list in (23) and status_active=1 and is_deleted=0 order by variable_list ASC");
	$color_from_lib=0;
 	foreach($sql_result as $result)
	{
		if($result[csf('variable_list')]==23) $color_from_lib=$result[csf('color_from_library')];
	}
	echo $color_from_lib;
 	exit();
}

if($action=="color_popup")
{
	echo load_html_head_contents("Color Select PopUP","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data)
		{
			document.getElementById('color_name').value=data;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center">
        <form>
            <input type="hidden" id="color_name" name="color_name" />
            <?
           /* if($buyer_name=="" || $buyer_name=="")
            {*/
            	$sql="select id, color_name FROM lib_color WHERE status_active=1 and is_deleted=0";
            /*}
            else
            {
            	$sql="select a.color_name,a.id FROM lib_color a, lib_color_tag_buyer b  WHERE a.id=b.color_id and b.buyer_id=$buyer_name and  status_active=1 and is_deleted=0";
            }*/
            echo  create_list_view("list_view", "Color Name", "160","210","420",0, $sql , "js_set_value", "color_name", "", 1, "0", $arr , "color_name", "requires/sample_booking_non_order_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0,0,0,0,0,0,2,2,2,2,2') ;
            ?>
        </form>
        </div>
	</body>
	</html>
	<?
	exit();
}

if($action=="set_attention")
{
	$sql="select contact_person from lib_supplier where id=$data";
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#txt_attention').val('".$row[csf("contact_person")]."');\n";
	}
	exit();
}

if ($action=="job_search_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][94] );
    //echo "var field_level_data= ". $data_arr . ";\n";
	?>
	<script>
		<?
		if (!empty($data_arr)) {
			echo "var field_level_data= ". $data_arr . ";\n";
		}
		else
		{
			echo "var field_level_data='';\n";
		}
		?>


		function js_set_value(str)
		{
		$("#hidden_job_no").val(str); // wo/pi id
		parent.emailwindow.hide();
		}
	</script>

	<div align="center" style="width:715px;" >
		<form name="searchjob"  id="searchjob" autocomplete="off">
			<table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" id="table1">
				<thead>
					<th width="145">Company</th>
					<?
					if($is_sales_order == 1)
					{
						?>
						<th width="100">Within Group</th>
						<?
					}
					?>
					<th width="140">Buyer/Customer</th>
					<th width="140">Cust. Buyer</th>
					<th width="100">
						<?
						$orderTitle= ($is_sales_order == 1)? "Sales Order No." : "Order No";
						echo $orderTitle;
						?>
					</th>
					<?
					if($is_sales_order == 1)
					{
						?>
						<th width="100">IR/IB</th>
						<th width="100">Sales Job/ Booking No</th>
						<?
					}else{
						?>
						<th width="60">Job No</th>
						<th width="60">Ref No</th>
						<th width="60">File No</th>
						<?
					}
					?>
					<th>
						<input type="reset" name="re_button" id="re_button" value="Reset" style="width:90px" class="formbutton" onClick="reset_form('searchjob','search_div','')"  />

						<input type="hidden" name="txt_is_sales" id="txt_is_sales" value="<? echo $is_sales_order;?>">
					</th>
				</thead>
				<tbody>
					<tr>
						<td>
							<?
							echo create_drop_down( "cbo_company_name", 145, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", str_replace("'","",$company), "load_drop_down( 'yarn_service_work_order_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",1);
							?>&nbsp;
						</td>
						<?
						if($is_sales_order == 1)
						{
							?>
							<td><? echo create_drop_down( "cbo_within_group", 100, $yes_no,"", 1, "-- Select --","","");?></td>
							<?
						}
						?>
						<td align="center" id="buyer_td">
							<?
							$buyer_qrery="select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name";
							echo create_drop_down( "cbo_buyer_name", 140, $buyer_qrery,"id,buyer_name", 1, "-- Select Buyer --",0);
							?>
						</td>
						<td align="center" id="buyer_td">
							<?
							$buyer_qrery="select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name";
							echo create_drop_down( "cbo_customer_buyer", 140, $buyer_qrery,"id,buyer_name", 1, "-- Select Buyer --",0);
							?>
						</td>
						<td align="center">
							<input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:90px" />
						</td>
						<?
						if($is_sales_order == 1)
						{
							?>
							<td><input type="text" name="txt_ir_no" id="txt_ir_no" class="text_boxes" style="width:100px" /></td>
							<td><input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:100px" /></td>
							<?
						}else{
							?>
							<td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:60px" /></td>
							<td><input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:60px" /></td>
							<td><input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:60px" /></td>
							<?
						}
						?>
						<td align="center">
							<?
							if($is_sales_order == 1)
							{
								?>
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_is_sales').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_customer_buyer').value+'_'+document.getElementById('txt_ir_no').value, 'create_job_search_list_view', 'search_div', 'yarn_service_work_order_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:90px;" />
								<?
							}
							else
							{
								?>
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_is_sales').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('txt_file_no').value, 'create_job_search_list_view', 'search_div', 'yarn_service_work_order_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:90px;" />
								<?
							}
							?>
						</td>
					</tr>
				</tbody>
			</table>
			<br>
			<div align="center" valign="top" id="search_div"> </div>
			<input type="hidden" id="hidden_job_no">
		</form>
	</div>
	</body>

	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script type="text/javascript">
		setFieldLevelAccess('<? echo $company; ?>');
		</script>
	</html>

	<?
	exit();
}

if ($action=="create_job_search_list_view")
{
	$data=explode('_',$data);
	//print_r($data);
	$cbo_company_name=str_replace("'","",$data[0]);
	$cbo_buyer_name=str_replace("'","",$data[1]);
	$txt_order_no=str_replace("'","",$data[2]);
	$is_sales_order=str_replace("'","",$data[3]);

	if($is_sales_order == 2)
	{
		//list($cbo_company_name,$cbo_buyer_name,$txt_order_no)=explode("_",$data);
		if($cbo_company_name!=0) $cbo_company_name="and a.company_name='$cbo_company_name'"; else $cbo_company_name="";
		if($cbo_buyer_name!=0) $cbo_buyer_name="and a.buyer_name='$cbo_buyer_name'"; else $cbo_buyer_name="";
		if($txt_order_no!="") $order_cond="and b.po_number='$txt_order_no'"; else $order_cond="";
		if($data[4]!="") $job_cond="and a.job_no_prefix_num='".$data[4]."'"; else $job_cond="";
		if($data[5]!="") $ref_cond="and b.grouping like '%".$data[5]."'"; else $ref_cond="";
		if($data[6]!="") $file_cond="and b.file_no like '%".$data[6]."'"; else $file_cond="";

		$approval_allow = sql_select("select b.id, b.page_id, b.approval_need, b.allow_partial, b.validate_page,a.setup_date from approval_setup_mst a,approval_setup_dtls b
		where a.id=b.mst_id and a.company_id='$cbo_company_name' and a.status_active=1 and b.page_id=25 and b.status_active=1 and b.is_deleted=0 order by b.id desc ");

		if ($approval_allow[0][csf("approval_need")] == 1 && $approval_allow[0][csf("allow_partial")] == 1)
			$approval_cond = "and c.approved in (1,3)";
		else if ($approval_allow[0][csf("approval_need")] == 1 && $approval_allow[0][csf("allow_partial")] == 2)
			$approval_cond = "and c.approved in (1)";
		else if ($approval_allow[0][csf("approval_need")] == 1 && $approval_allow[0][csf("allow_partial")] == 0)
			$approval_cond = "and c.approved in (1,3)";
		else $approval_cond = "";

		$sql="select a.id,a.job_no, a.company_name, a.buyer_name, a.style_ref_no,a.order_uom,b.po_number from wo_po_details_master a,wo_po_break_down b, wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and b.job_no_mst=c.job_no and b.shiping_status not in(3) $cbo_company_name $cbo_buyer_name $order_cond $job_cond $ref_cond $file_cond $approval_cond group by a.id,a.job_no, a.company_name, a.buyer_name, a.style_ref_no,a.order_uom,b.po_number ";

		?>
		<div style="width:615px;" align="left">

			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="595" class="rpt_table" >
				<thead>
					<th width="40">SL</th>
					<th width="80">Job No</th>
					<th width="100">Buyer</th>
					<th>Style Ref.No</th>
					<th width="100">Order No</th>
					<th width="50">Order Uom</th>
				</thead>
			</table>
			<div style="width:615px; overflow-y:scroll; max-height:250px;" id="buyer_list_view">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="595" class="rpt_table" id="list_view" >
					<?
					$i=1;
					$job_sql=sql_select( $sql );
					foreach ($job_sql as $rows)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer" onClick="js_set_value('<? echo $rows[csf('id')].','.$rows[csf('job_no')]; ?>'); ">

							<td width="40" align="center"><p> <? echo $i; ?></p></td>
							<td width="80" align="center"><? echo $rows[csf("job_no")]; ?></td>
							<td width="100"><p><?  echo  $buyer_arr[$rows[csf('buyer_name')]]; ?></p></td>
							<td><p><? echo $rows[csf('style_ref_no')]; ?></p></td>
							<td width="100"><p><? echo $rows[csf('po_number')]; ?></p></td>
							<td width="50" align="center"><? echo $unit_of_measurement[$rows[csf('order_uom')]]; ?></td>
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
	if($is_sales_order==1)
	{
    	//list($cbo_company_name,$cbo_buyer_name,$txt_order_no,$txt_booking_no,$cbo_within_group,$txt_is_sales)=explode("_",$data);
		$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
		$txt_booking_no=str_replace("'","",$data[4]);
		$cbo_within_group=str_replace("'","",$data[5]);
		$cbo_customer_buyer=str_replace("'","",$data[6]);
		$txt_ir_no=str_replace("'","",$data[7]);

		if($cbo_company_name!=0)
			$cbo_company_name="and a.company_id='$cbo_company_name'";
		else
			$cbo_company_name="";

		if($cbo_buyer_name!=0)
		{
			$cbo_buyer_cond_1=" and c.buyer_id='$cbo_buyer_name'";
			$cbo_buyer_cond_2=" and a.buyer_id='$cbo_buyer_name'";
		}
		else
		{
			$cbo_buyer_cond_1="";
			$cbo_buyer_cond_2="";
		}

		//for customer buyer
		$cbo_buyer_cond_3="";
		if($cbo_customer_buyer != 0)
		{
			$cbo_buyer_cond_3=" and a.customer_buyer = '".$cbo_customer_buyer."'";
		}

		if($txt_order_no!="")
			$order_cond="and a.job_no like '%".trim($txt_order_no)."%'";
		else
			$order_cond="";

		if($txt_booking_no!="")
			$booking_cond="and a.sales_booking_no like '%".trim($txt_booking_no)."%'";
		else
			$booking_cond="";
		//if($cbo_within_group!=0) $within_group_cond=" and a.within_group = '$cbo_within_group'"; else $within_group_cond="";

		if($txt_ir_no!="")
		{

			$sql= "SELECT a.id, c.grouping, a.job_no, a.booking_no from wo_booking_mst a, wo_po_details_master b, wo_po_break_down c where a.job_no=b.job_no and a.job_no=c.job_no_mst and b.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.grouping like '%$txt_ir_no%' $cbo_company_name ";
			//echo $sql;
			$jobBookingArray=sql_select($sql);
			$all_booking_no_arr = array();
			foreach ($jobBookingArray as $row)
			{
				if($bookingNoChk[$row[csf('booking_no')]] == "")
				{
					$bookingNoChk[$row[csf('booking_no')]] = $row[csf('booking_no')];
					array_push($all_booking_no_arr, $row[csf('booking_no')]);
				}
			}

			if(!empty($all_booking_no_arr))
			{
				$job_booking_cond = " ".where_con_using_array($all_booking_no_arr,1,'a.sales_booking_no')." ";
			}


		}

		if($db_type == 1) $select_uom = " group_concat(b.order_uom) as order_uom"; else $select_uom = " listagg(b.order_uom,',' ) within group (order by b.order_uom) order_uom";
		$sql1= "select a.id, a.company_id, c.buyer_id, a.style_ref_no, a.job_no, a.within_group, a.sales_booking_no, a.customer_buyer, $select_uom
		from fabric_sales_order_mst a, fabric_sales_order_dtls b, wo_booking_mst c
		where a.id = b.mst_id and a.booking_id = c.id and a.status_active=1 and b.status_active=1 and b.status_active=1 $cbo_company_name $cbo_buyer_cond_1 $cbo_buyer_cond_3 $order_cond $booking_cond $job_booking_cond and a.within_group = 1
		group by a.id, a.company_id, c.buyer_id, a.style_ref_no, a.job_no, a.within_group, a.sales_booking_no, a.customer_buyer";

		$sql2= "select a.id, a.company_id, a.buyer_id, a.style_ref_no, a.job_no, a.within_group, a.sales_booking_no, a.customer_buyer, $select_uom
		from fabric_sales_order_mst a, fabric_sales_order_dtls b
		where a.id = b.mst_id and a.status_active=1 and b.status_active=1 and b.status_active=1 $cbo_company_name $cbo_buyer_cond_2 $cbo_buyer_cond_3 $order_cond $booking_cond and a.within_group = 2
		group by a.id, a.company_id, a.buyer_id, a.style_ref_no, a.job_no, a.within_group, a.sales_booking_no, a.customer_buyer
		order by id";
		if($cbo_within_group==1)
		{
			$sql = $sql1;
		}
		else if($cbo_within_group==2)
		{
			$sql = $sql2;
		}
		else
		{
			$sql = $sql1. " union all ". $sql2;
		}
		//echo $sql;
		$job_sql=sql_select( $sql );

		$all_booking_arr = array();
		foreach ($job_sql as $row)
		{
			if($row[csf('within_group')]==1)
			{
				if($allbookingNoChk[$row[csf('sales_booking_no')]] == "")
				{
					$allbookingNoChk[$row[csf('sales_booking_no')]] = $row[csf('sales_booking_no')];
					array_push($all_booking_arr, $row[csf('sales_booking_no')]);
				}
			}
		}

		$sql_job = "SELECT a.id,  c.grouping,  a.job_no, a.booking_no  from wo_booking_mst a, wo_po_details_master b, wo_po_break_down c where a.job_no=b.job_no and a.job_no=c.job_no_mst and b.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company ".where_con_using_array($all_booking_arr,1,'a.booking_no')." ";
		//echo $sql_job;

		$sql_job_rslt=sql_select($sql_job);
		$job_info_arr = array();
		foreach ($sql_job_rslt as $val)
		{
			//$job_info_arr[$val[csf("booking_no")]]["job_no"] = $val[csf("job_no")];
			$job_info_arr[$val[csf("booking_no")]]["grouping"] .= $val[csf("grouping")].',';
		}
		//var_dump($job_info_arr);

		?>
		<div style="width:890px;"; align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table" >
				<thead>
					<th width="40">SL</th>
					<th width="100">Buyer/ Customer</th>
					<th width="100">Cust. Buyer</th>
					<th width="140">Sales Order No</th>
					<th width="140">Sales Job/ Booking No</th>
					<th width="140">IR/ IB</th>
					<th width="100">Within Group</th>
					<th width="">Order Uom</th>
				</thead>
			</table>
			<div style="width:890px; overflow-y:scroll; max-height:250px;" id="buyer_list_view">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table" id="list_view" >
					<?
					$i=1;

					foreach ($job_sql as $rows)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer" onClick="js_set_value('<? echo $rows[csf('id')].','.$rows[csf('job_no')].','.$rows[csf('sales_booking_no')]; ?>'); ">
							<td width="40" align="center"><p> <? echo $i; ?></p></td>
							<td width="100">
								<p>
									<?
									/*if($rows[csf("within_group")] == 1)
									{
										echo $company_arr[$rows[csf('buyer_id')]];
									}
									else
									{*/
										echo  $buyer_arr[$rows[csf('buyer_id')]];
									//}
										?>
									</p>
								</td>
							<td width="100"><p><? echo  $buyer_arr[$rows[csf('customer_buyer')]]; ?></p></td>
                            <td width="140" align="center"><? echo $rows[csf("job_no")]; ?></td>
                            <td width="140"><p><? echo $rows[csf('sales_booking_no')]; ?></p></td>
                            <td width="140">
								<p>
									<?
									$job_data = $job_info_arr[$rows[csf('sales_booking_no')]]["grouping"];
									echo implode(",", array_unique(explode(",",chop($job_data ,","))));
									?>
								</p>
							</td>
                            <td width="100" align="center"><p><? if ($rows[csf('within_group')] == 1) echo "Yes"; else echo "No"; ?></p></td>
                            <td width="" align="center">
                                <?
                                $uom= ""; $uom_arr = array();
                                $uom_arr =  array_unique(explode(",", $rows[csf('order_uom')]));
                                foreach ($uom_arr as $val)
                                {
                                    $uom .= $unit_of_measurement[$val].",";
                                }
                                echo chop($uom,",");
                                ?>
                            </td>
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

//for booking_search_popup
if ($action=="booking_search_popup")
{
	echo load_html_head_contents("Booking Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(str)
		{
			// wo/pi id
			$("#hdn_booking_no").val(str);
			parent.emailwindow.hide();
		}
	</script>

	<div align="center" style="width:900px;" >
		<form name="searchjob"  id="searchjob" autocomplete="off">
			<table width="900" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
				<thead>
					<th width="145">Company</th>
					<th width="145">Buyer</th>
					<th width="80">Booking No</th>
					<th width="80">Style No</th>
					<th width="100">Job No</th>
					<th width="80">Order No</th>
					<th width="80">Budget Version</th>
					<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('searchjob','search_div','')"  /></th>
				</thead>
				<tbody>
					<tr>
						<td>
							<?
							echo create_drop_down( "cbo_company_name", 145, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", str_replace("'","",$company)/*$selected */, "load_drop_down( 'yarn_dyeing_charge_booking_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
							?>

							<input class="datepicker" type="hidden" style="width:130px" name="txt_booking_date" id="txt_booking_date"  value="<? echo str_replace("'","",$txt_booking_date)?>" disabled /></td>
						</td>

						<td align="center" id="buyer_td">
							<?
							$blank_array="select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name";
							echo create_drop_down( "cbo_buyer_name", 145, $blank_array,"id,buyer_name", 1, "-- Select Buyer --",0);
							?>
						</td>
						<td align="center">
							<input type="text" name="txt_fab_booking_no" id="txt_fab_booking_no" class="text_boxes" style="width:75px" />
						</td>
						<td align="center">
							<input type="text" name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:75px" />
						</td>
						<td align="center">
							<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px" value="<? echo $txt_job_no;?>" />
						</td>
						<td align="center">
							<input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:75px" />
						</td>
						<td align="center" >
							<?
							$pre_cost_class_arr = array(1=>'Pre Cost 2',2=>'Pre Cost 1');
							echo create_drop_down( "cbo_budget_version", 100, $pre_cost_class_arr,"", 0, "-- Select Version --",$budget_version,'',1);
							?>
						</td>
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_style_no').value+'_'+document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_fab_booking_no').value+'_'+document.getElementById('cbo_budget_version').value+'_'+'<? echo $is_sales_order;?>', 'create_booking_search_list_view', 'search_div', 'yarn_service_work_order_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:70px;" />
						</td>
					</tr>
				</tbody>
			</table>
			<br>
			<div align="center" valign="top" id="search_div"> </div>
		</form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

//create_booking_search_list_view
if ($action=="create_booking_search_list_view")
{
	$data=explode("_",$data);
	$cbo_company_name=str_replace("'","",$data[0]);
	$cbo_buyer_name=str_replace("'","",$data[1]);
	$txt_job_no=str_replace("'","",$data[2]);
	$txt_style_no=str_replace("'","",$data[3]);
	$txt_order_no=str_replace("'","",$data[4]);
	$fab_booking_no=str_replace("'","",$data[5]);
	$budget_version=str_replace("'","",$data[6]);
	$is_sales=str_replace("'","",$data[7]);

	$sql_cond="";
	if($cbo_company_name!=0) $sql_cond=" and a.company_name='$cbo_company_name'";
	if($cbo_buyer_name!=0) $sql_cond.=" and a.buyer_name='$cbo_buyer_name'";
	if($txt_job_no!="") $sql_cond.=" and a.job_no LIKE '%$txt_job_no%'";
	if($fab_booking_no!="") $sql_cond.=" and d.booking_no LIKE '%$fab_booking_no%'";
	if($txt_style_no!="") $sql_cond.=" and a.style_ref_no like '%$txt_style_no%'";
	if($txt_order_no!="") $sql_cond.=" and b.po_number like '%$txt_order_no%'";

	//for version 1
	if($budget_version==1)
	{
		$entry_form="and c.entry_from=111";
	}
	//for version 2
	else
	{
		$entry_form="and c.entry_from=158";
	}
	$entry_form = '';

	//wo_booking_dtls
	if($db_type==0)
	{
		if($is_sales!=1)
		{
			$sql="select a.id, d.booking_no, a.company_name, a.buyer_name, a.style_ref_no, year(a.insert_date) as year from  wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c,wo_booking_dtls d where a.job_no=b.job_no_mst and a.job_no=c.job_no  and d.po_break_down_id=b.id and d.job_no=a.job_no and c.job_no=d.job_no and d.booking_type in(1,4) and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1  $sql_cond $entry_form group by a.job_no order by a.insert_date desc";
		}
		else
		{
			$sql="select a.company_id as company_name,a.job_no_prefix_num as po_number,a.job_no,a.po_job_no,a.sales_booking_no as booking_no,a.buyer_id,a.po_buyer,a.customer_buyer,a.style_ref_no,a.within_group, to_char(a.insert_date,'YYYY') as year from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.job_no='$txt_job_no' ";
		}
	}
	else if($db_type==2)
	{
		if($is_sales!=1)
		{
			$sql="select a.id, c.job_no,d.booking_no, a.company_name, a.buyer_name, a.style_ref_no, to_char(a.insert_date,'YYYY') as year from  wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c, wo_booking_dtls d where a.job_no=b.job_no_mst  and a.job_no=c.job_no  and d.po_break_down_id=b.id and d.job_no=a.job_no and c.job_no=d.job_no and d.booking_type in(1,4) and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $sql_cond $entry_form group by a.id,d.booking_no,c.job_no,a.company_name, a.buyer_name, a.style_ref_no,a.insert_date order by a.insert_date desc";
		}
		else
		{
			$sql="select a.company_id as company_name,a.job_no_prefix_num as po_number,a.job_no,a.po_job_no,a.sales_booking_no as booking_no,a.buyer_id as buyer_name,a.po_buyer,a.customer_buyer,a.style_ref_no,a.within_group, to_char(a.insert_date,'YYYY') as year from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.job_no='$txt_job_no' ";
		}
	}
	//echo $sql;
	$nameArray=sql_select( $sql );
	$booking_nostr="";
	foreach ($nameArray as $row)
	{
		if($booking_nostr=="") $booking_nostr="'".$row[csf('booking_no')]."'"; else $booking_nostr.=",'".$row[csf('booking_no')]."'";
	}

	$fabbooking_no=implode(",",array_filter(array_unique(explode(",",$booking_nostr))));
	$bookingNos=count(explode(",",$booking_nostr)); $bookingNoCond="";
	if($db_type==2 && $po_ids>500)
	{
		$bookingNoCond=" and (";
		$bookingArr=array_chunk(explode(",",$bookingNos),499);
		foreach($bookingArr as $ids)
		{
			$ids=implode(",",$ids);
			$bookingNoCond.=" b.booking_no in($ids) or";
		}
		$bookingNoCond=chop($bookingNoCond,'or ');
		$bookingNoCond.=")";
	}
	else $bookingNoCond=" and b.booking_no in($fabbooking_no)";

	$poDataArr=array();
	$poIdDataArray=sql_select("SELECT a.id, a.po_number, a.file_no, a.grouping, b.booking_no from wo_po_break_down a, wo_booking_dtls b where b.po_break_down_id=a.id and a.status_active=1 and b.status_active=1 and b.booking_type in(1) $bookingNoCond ");
	foreach ($poIdDataArray as $row)
	{
		$poDataArr[$row[csf('booking_no')]][$row[csf('id')]]['po']=$row[csf('po_number')];
		$poDataArr[$row[csf('booking_no')]][$row[csf('id')]]['file']=$row[csf('file_no')];
		$poDataArr[$row[csf('booking_no')]][$row[csf('id')]]['ref']=$row[csf('grouping')];
	}
	unset($poIdDataArray);
	//echo $sql;
	echo '<input type="hidden" id="hdn_booking_no" name="hdn_booking_no>';
	?>
	<div style="width:800px;"align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="790" class="rpt_table" >
			<thead>
				<th width="40">SL</th>
				<th width="50">Year</th>
				<th width="120">Booking No</th>
				<th width="130">Buyer</th>
				<th width="120">Job No</th>
				<th width="150">Style Ref</th>
				<th>Order No.</th>
			</thead>
		</table>
		<div style="width:790px; overflow-y:scroll; max-height:250px;" id="buyer_list_view">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="770" class="rpt_table" id="tbl_list_search" >
				<?
				$i=1;
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					$poNo="";
					$fileNo="";
					$refNo="";
					$jobDataArr=$poDataArr[$row[csf('booking_no')]];

					foreach($jobDataArr as $pid=>$pval)
					{
						//echo print_r($pid);
						if($poNo=="") $poNo=$pval['po']; else $poNo.=','.$pval['po'];
						if($fileNo=="") $fileNo=$pval['file']; else $fileNo.=','.$pval['file'];
						if($refNo=="") $refNo=$pval['ref']; else $refNo.=','.$pval['ref'];
					}

					$po_number=implode(",",array_filter(array_unique(explode(",",$poNo))));
					$po_number = ($is_sales==1)? $row[csf('po_number')] : $po_number;
					
					if($is_sales==1)
					{
						$job_no = ($selectResult[csf('within_group')]==1)?$selectResult[csf('po_job_no')]:$selectResult[csf('job_no')];
						$buyer_id = ($selectResult[csf('within_group')]==1)?$selectResult[csf('po_buyer')]:$selectResult[csf('buyer_name')]; 
					}
					else{
						$job_no = $selectResult[csf('job_no')];
						$buyer_id = $selectResult[csf('buyer_name')]; 
					}

					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$file_no=implode(",",array_filter(array_unique(explode(",",$fileNo))));
					$int_ref_no=implode(",",array_filter(array_unique(explode(",",$refNo))));
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $selectResult[csf('booking_no')]; ?>'+','+'<? echo $budget_version; ?>'); ">
						<td width="40"><p> <? echo $i; ?></p></td>
						<td width="50"  align="center"> <p><? echo $selectResult[csf('year')]; ?></p></td>
						<td width="120"  align="center"> <p><? echo $selectResult[csf("booking_no")]; ?></p></td>
						<td width="130"><p><?  echo  $buyer_arr[$buyer_id]; ?></p></td>
						<td width="120"> <p><?  echo $job_no; ?></p></td>
						<td width="150"> <p><?  echo $selectResult[csf('style_ref_no')]; ?></p></td>
						<td ><p> <? echo $po_number;?></p></td>
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

if($action=="lot_search_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$company=str_replace("'","",$company);
	$job_no=str_replace("'","",$job_no);
	$is_sales_order=str_replace("'","",$is_sales_order);
	$cbo_service_type=str_replace("'","",$cbo_service_type);

	$count_arr 		= return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$supplier_arr 	= return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$prod_data 		= sql_select("select id, supplier_id, yarn_count_id, yarn_type from product_details_master where item_category_id=1 and company_id=$company and status_active=1 and is_deleted=0");

	foreach ($prod_data as $row) {
		$supplierArr[$row[csf('supplier_id')]] = $supplier_arr[$row[csf('supplier_id')]];
		$countArr[$row[csf('yarn_count_id')]]  = $count_arr[$row[csf('yarn_count_id')]];
		$yarn_type_arr[$row[csf('yarn_type')]] = $yarn_type[$row[csf('yarn_type')]];
	}
	?>
	<script>
		var selected_id = new Array();
		function js_set_value2(str)
		{
			$("#hidden_product").val(str);
			parent.emailwindow.hide();
		}

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style )
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str, i )
		{
			toggle( document.getElementById( 'search' + i ), '#FFFFCC' );

			if( jQuery.inArray( str, selected_id ) == -1 )
			{
				selected_id.push(str);
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == str ) break;
				}
				selected_id.splice( i, 1 );
			}

			var product_id = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				product_id += selected_id[i] + '#';
			}

			product_id = product_id.substr( 0, product_id.length - 1 );
			$("#hidden_product").val(product_id);
			return;
		}
	</script>
	</head>
	<body>
		<div style="" align="center" >
			<fieldset>
				<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
					<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" style="">
						<thead>
							<tr>
								<th width="">Supplier</th>
								<th width="">Count</th>
								<th width="">Yarn Description</th>
								<th width="">Type</th>
								<th width="">Lot</th>
								<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:70px;"></th>
							</tr>
						</thead>
						<tr>
							<td><? echo create_drop_down("cbo_supplier", 120, $supplier_arr, "", 1, "-- Select --", '', "", 0); ?></td>
							<td><? echo create_drop_down("cbo_count", 100, $count_arr, "", 1, "-- Select --", '', "", 0); ?></td>
							<td><input type="text" name="txt_desc" id="txt_desc" class="text_boxes" style="width:130"></td>
							<td><? echo create_drop_down("cbo_type", 100, $yarn_type_arr, "", 1, "-- Select --", '', "", 0); ?></td>
							<td>
								<input name="txt_lot_no" id="txt_lot_no" class="text_boxes" style="width:100px" placeholder="Write">
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show"   onClick="show_list_view (document.getElementById('cbo_supplier').value+'**'+document.getElementById('cbo_count').value+'**'+document.getElementById('txt_desc').value+'**'+document.getElementById('cbo_type').value+'**'+document.getElementById('txt_lot_no').value+'**'+'<? echo $company; ?>'+'**'+'<? echo $job_no; ?>'+'**'+'<? echo $is_sales_order; ?>'+'**'+'<? echo $cbo_service_type; ?>'+'**'+'<? echo $cbo_with_order; ?>'+'**'+'<? echo $fab_booking_no; ?>', 'create_lot_search_list_view', 'search_div', 'yarn_service_work_order_controller', 'setFilterGrid(\'table_charge\',-1)');" style="width:70px;"/>
							</td>
						</tr>
					</table>
					<br>
					<table align="center">
						<tr>
							<td align="center" valign="top" id="search_div"></td>
						</tr>
					</table>
				</form>
			</fieldset>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		$("#flt13_table_charge").css("display","none");
	</script>
	</html>
	<?
}

if($action=="create_lot_search_list_view")
{
	$data=explode('**',$data);
	if ($data[0] == 0) $supp_cond = ""; else $supp_cond = " and a.supplier_id='" . trim($data[0]) . "' ";
	if ($data[1] == 0) $yarn_count_cond = ""; else $yarn_count_cond = " and a.yarn_count_id='" . trim($data[1]) . "' ";
	if ( trim($data[2]) == "") $yarn_desc_cond = ""; else $yarn_desc_cond = " and a.product_name_details like '%" . trim($data[2]) . "%'";
	if ($data[3] == 0) $yarn_type_cond = ""; else $yarn_type_cond = " and a.yarn_type='" . trim($data[3]) . "' ";
	if (trim($data[4]) == "") $lot_no_cond = ""; else $lot_no_cond = " and a.lot like '%" . trim($data[4]) . "%'";

	$companyID 		= $data[5];
	$job_no 		= $data[6];
	$is_sales_order = $data[7];
	$service_type 	= $data[8];
	$cbo_with_order = $data[9];
	$fab_booking_no = $data[10];

	if ($job_no =="") $job_cond = ""; else $job_cond = " and b.job_no='$job_no' ";
	if ($fab_booking_no =="")
	{
		$fab_booking_no_cond = "";
		$fab_booking_no_cond2 = "";
	}
	else
	{
		$fab_booking_no_cond = " and c.booking_no='".$fab_booking_no."' ";
		//$fab_booking_no_cond2 = " and b.booking_no='".$fab_booking_no."' ";
		$fab_booking_no_cond2 = "";
	}

	$color_library 	= return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name");
	$count_arr 		= return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$supplier_arr 	= return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$buyer_arr 		= return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	?>
	</head>
	<body>
		<div>
			<fieldset>
				<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
					<table width="1138" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center"  rules="all">
						<thead>
							<tr>
								<th width="40">Sl No</th>
								<th width="70">Booking No</th>
								<th width="70">Buyer ID</th>
								<th width="70">Count</th>
								<th width="170">Composition</th>
								<th width="80">Type</th>
								<th width="80">Color</th>
								<th width="80">Lot No</th>
								<th width="140">Supplier</th>
								<th width="80">Current Stock</th>
								<th width="80"><? echo $allo_available_cap =  ($cbo_with_order==1)?"Allocated Qnty":"Available Qnty";  ?></th>
								<th width="80">Age (Days)</th>
								<th width="">DOH</th>
							</tr>
						</thead>
					</table>
					<?
						//for variable settings
						$arr = array();
						$arr['company_id'] = $companyID;
						$arr['category_id'] = 1;
						$arr['vs_auto_allocation'] = 1;
						$data_vs = get_vs_allocated_qty($arr);

						$variable_set_allocation = $data_vs['is_allocated'];
						$is_sales_order_variable_settings = $data_vs['is_sales_order'];
						$is_auto_allocation = $data_vs['is_auto_allocation'];
						//end

						if($is_sales_order==1) //Sales Yes
						{
							if($is_sales_order_variable_settings == 1 && $is_auto_allocation != 1)
							{
								$sql="select a.id, a.supplier_id, a.lot, a.current_stock, c.qnty as allocated_qnty, a.available_qnty, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_count_id, a.yarn_type, a.color, a.dyed_type, b.buyer_id, b.job_no, c.booking_no from inv_material_allocation_mst c, fabric_sales_order_mst b, product_details_master a where a.company_id=".$companyID." and c.job_no=b.job_no and c.booking_no=b.sales_booking_no and c.item_id=a.id ".$job_cond." ".$fab_booking_no_cond." and c.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.qnty>0 ".$supp_cond." ".$yarn_count_cond." ".$yarn_type_cond." ".$yarn_desc_cond." ".$lot_no_cond." group by a.id, a.supplier_id, a.lot, a.current_stock, c.qnty, a.available_qnty, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_count_id, a.yarn_type, a.color, a.dyed_type, b.buyer_id, b.job_no, c.booking_no";
							}
							else if ($variable_set_allocation == 1 && $is_auto_allocation == 1)
							{
								$sql="select a.id,  a.supplier_id, a.lot, a.current_stock, a.allocated_qnty, a.available_qnty, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_count_id, a.yarn_type, a.color, a.dyed_type from product_details_master a where a.company_id=$companyID and a.status_active=1 and a.is_deleted=0 and a.item_category_id =1 and a.available_qnty>0 $supp_cond $yarn_count_cond $yarn_type_cond $yarn_desc_cond $lot_no_cond group by a.id, a.supplier_id, a.lot, a.current_stock, a.allocated_qnty, a.available_qnty,  a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_count_id, a.yarn_type, a.color, a.dyed_type, a.available_qnty";
							}
						}
						else
						{
							if($variable_set_allocation==1)
							{
								if($cbo_with_order==1)
								{
									$sql="select a.id, a.supplier_id,a.lot,a.current_stock,c.qnty as allocated_qnty,a.available_qnty,a.yarn_comp_type1st,a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd,a.yarn_count_id, a.yarn_type,a.color,a.dyed_type,b.buyer_name as buyer_id,b.job_no,c.booking_no from inv_material_allocation_mst c,wo_po_details_master b,product_details_master a where a.company_id=$companyID and c.job_no=b.job_no and c.item_id=a.id $job_cond and c.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.qnty>0 $supp_cond $yarn_count_cond $yarn_type_cond $yarn_desc_cond $lot_no_cond group by a.id, a.supplier_id,a.lot,a.current_stock,c.qnty,a.available_qnty, a.yarn_comp_type1st,a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd,a.yarn_count_id,a.yarn_type,a.color,a.dyed_type,b.buyer_name,b.job_no,c.booking_no,a.available_qnty order by c.booking_no";
									//echo $sql;/$fab_booking_no_cond
								}
								else
								{
									$sql="select a.id, a.supplier_id,a.lot,a.current_stock,a.allocated_qnty,a.available_qnty,a.yarn_comp_type1st,a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd,a.yarn_count_id, a.yarn_type,a.color,a.dyed_type from product_details_master a where a.company_id=$companyID and a.status_active=1 and a.is_deleted=0 and a.item_category_id =1 and a.available_qnty>0 and a.current_stock>0  $supp_cond $yarn_count_cond $yarn_type_cond $yarn_desc_cond $lot_no_cond group by a.id, a.supplier_id,a.lot,a.current_stock,a.allocated_qnty,a.available_qnty, a.yarn_comp_type1st,a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd,a.yarn_count_id,a.yarn_type,a.color,a.dyed_type,a.available_qnty";
								}
							}
							else
							{
								 $sql="select a.id, a.supplier_id,a.lot,a.current_stock,a.allocated_qnty,a.available_qnty,a.yarn_comp_type1st,a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd,a.yarn_count_id, a.yarn_type,a.color,a.dyed_type from product_details_master a where a.company_id=$companyID and a.status_active=1 and a.is_deleted=0 and a.item_category_id =1 and a.available_qnty>0 and a.current_stock>0 $supp_cond $yarn_count_cond $yarn_type_cond $yarn_desc_cond $lot_no_cond group by a.id, a.supplier_id,a.lot,a.current_stock,a.allocated_qnty, a.yarn_comp_type1st,a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd,a.yarn_count_id,a.yarn_type,a.color,a.dyed_type,a.available_qnty";
							}
						}
						//echo $sql;
						$sql_result=sql_select($sql);
						foreach($sql_result as $row)
						{
							//$booking_no = $row_allo[csf('booking_no')];
							//$job_no = $row[csf('job_no')];
							$yarn_count_id = $row[csf('yarn_count_id')];
							$yarn_comp_type1st = $row[csf('yarn_comp_type1st')];
							$yarn_comp_percent1st = $row[csf('yarn_comp_percent1st')];
							$yarn_type_id = $row[csf('yarn_type')];
							$job_total_allocation_arr[$row[csf('job_no')]][$row[csf('id')]] += $row[csf('allocated_qnty')];
							$product_type_arr[$row[csf('id')]] = $row[csf('dyed_type')];

							$product_ids.=$row[csf('id')].",";
						}

						if($yarn_count_id!=""){$count_id_cond = "and b.count=$yarn_count_id";}
						if($yarn_comp_type1st!=""){$yarn_comp_type1st_cond = "and b.yarn_comp_type1st=$yarn_comp_type1st";}
						if($yarn_comp_percent1st!=""){$yarn_comp_percent1st_cond = "and b.yarn_comp_percent1st=$yarn_comp_percent1st";}
						if($product_ids!=""){$product_id_cond = "and b.product_id in(".chop($product_ids,",").")";}

						$ydsw_sql="select x.id, x.ydw_no,x.service_type,x.job_no,x.product_id,sum(x.yarn_wo_qty) yarn_wo_qty from(select a.id, a.ydw_no,a.service_type,b.job_no,b.product_id,sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.entry_form in(41,42,135,94) and b.entry_form in(41,42,135,94) $job_cond $product_id_cond $fab_booking_no_cond2 group by a.id, a.ydw_no,a.service_type,b.job_no,b.product_id
						union all
						select a.id, a.ydw_no,a.service_type,b.job_no,b.product_id,sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.entry_form in(114,125,340) and b.entry_form in(114,125,340) $job_cond $count_id_cond $yarn_comp_type1st_cond $yarn_comp_percent1st_cond $fab_booking_no_cond2 group by a.id, a.ydw_no,a.service_type,b.job_no,b.product_id )x group by x.id, x.ydw_no,x.service_type,x.job_no,x.product_id";
						//echo $ydsw_sql;
						$check_ydsw = sql_select($ydsw_sql);
						$prod_wise_ydsw=array();
						$ydsw_from_avail_prod = array();
						$ydsw_from_avail_prod_no = array();
						foreach ($check_ydsw as $row)
						{
							if($row[csf("job_no")]!="")
							{
								$prod_wise_ydsw[$row[csf("job_no")]][$row[csf("product_id")]] += $row[csf("yarn_wo_qty")];
							}

							if( $row[csf("job_no")]=="" && ( $row[csf("service_type")]==7 || $row[csf("service_type")]==15 || $row[csf("service_type")]== 38 || $row[csf("service_type")]== 46 || $row[csf("service_type")]== 50 || $row[csf("service_type")]== 51) )
							{
								$ydsw_from_avail_prod[$row[csf("product_id")]] += $row[csf("yarn_wo_qty")];
								$ydsw_from_avail_prod_no[$row[csf("product_id")]].=$row[csf("ydw_no")].",";
							}

							$work_order_ids.=$row[csf('id')].",";
						}
						/*echo "<pre>";
						print_r($prod_wise_ydsw);
						echo "</pre>";*/

						//echo $work_order_ids."test";

						if($work_order_ids!="")
						{
							$work_order_ids_cond = "and a.booking_id in(".chop($work_order_ids,",").")";
							$wo_issue_sql_result = sql_select("select b.prod_id,b.cons_quantity as issue_qty from inv_issue_master a,inv_transaction b where a.id=b.mst_id and a.item_category=1 and a.entry_form=3 and a.issue_basis=1 and b.transaction_type=2 and b.item_category=1 $work_order_ids_cond");
							$work_order_issue_qty = array();
							foreach ($wo_issue_sql_result as $row)
							{
								$work_order_issue_qty[$row[csf('prod_id')]] += $row[csf('issue_qty')];
							}

							$wo_issue_rtn_sql_result = sql_select("select b.prod_id,b.cons_quantity as issue_rtn_qty from inv_receive_master a,inv_transaction b where a.id=b.mst_id and a.item_category=1 and a.entry_form=9 and a.receive_basis=1 and b.transaction_type=4 and b.item_category=1 $work_order_ids_cond");
							$work_order_issue_rtn_qty = array();
							foreach ($wo_issue_rtn_sql_result as $row)
							{
								$work_order_issue_rtn_qty[$row[csf('prod_id')]] += $row[csf('issue_rtn_qty')];
							}
						}

						//print_r($work_order_issue_qty);
						//print_r($prod_wise_ydsw);
						if($job_no!="")
						{
							$all_booking_no = '';
							$get_job_booking = sql_select("select a.booking_no from wo_booking_dtls a where a.job_no='$job_no' and a.status_active=1 and a.is_deleted=0 group by  booking_no");
							foreach ($get_job_booking as $booking_row)
							{
								$all_booking_no .= "'" . $booking_row[csf('booking_no')] . "',";
							}
							$booking_nos = rtrim($all_booking_no, ',');

							if($booking_nos!="")
								$booking_nos=$booking_nos;
							else
								$booking_nos=0;

							if ($db_type == 0)
							{
								$all_knit_id = return_field_value("group_concat(distinct(b.id)) as knit_id", "ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b", "a.id=b.mst_id and a.booking_no in($booking_nos) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "knit_id");
							}
							else
							{
								$all_knit_id = return_field_value("LISTAGG(b.id, ',') WITHIN GROUP (ORDER BY b.id) as knit_id", "ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b", "a.id=b.mst_id and a.booking_no in($booking_nos) and a.status_active=1 and a.is_deleted=0 ", "knit_id"); //  and b.status_active=1 and b.is_deleted=0 : ommit cause program can be deleted even after issue
								$all_knit_id = implode(",", array_unique(explode(",", $all_knit_id)));
							}
						}

						if($all_knit_id!="") $all_knit_id=$all_knit_id;else $all_knit_id=0;

						$req_sql = "select a.booking_no,c.knit_id,c.prod_id,c.requisition_no, c.yarn_qnty
						from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b,ppl_yarn_requisition_entry c where a.id=b.mst_id and b.id=c.knit_id and b.id in ($all_knit_id) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0"; // and b.status_active=1 and b.is_deleted=0 : ommit cause program can be deleted even after issue
						//echo $req_sql;
						$req_result = sql_select($req_sql);
						foreach($req_result as $row)
						{
							$booking_requsition_arr[$row[csf("booking_no")]][$row[csf("prod_id")]] += $row[csf("yarn_qnty")];
						}

						/*
						echo "<pre>";
						print_r($job_requsition_arr);
						die();*/

						$date_array = array();
						if(!empty($product_id_arr))
						{
							$returnRes_date = "select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=1 and prod_id in(".implode(",",$product_id_arr).") group by prod_id";
							$result_returnRes_date = sql_select($returnRes_date);
							foreach ($result_returnRes_date as $row)
							{
								$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
								$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
							}
						}
						?>
						<div style=" overflow-y:scroll; max-height:250px;font-size:12px; width:1160px;">
							<table width="1140" cellspacing="0" cellpadding="0" border="0" class="rpt_table"  style="cursor:pointer" rules="all" id="table_charge">
								<tbody>
									<?
									if(!empty($sql_result))
									{
										$i=1;
										$job_total_allocation_qty = 0;
										$previous_ydsw_qty = 0;
										$previous_requsition_qty = 0;
										$allocation_balance = 0;


										foreach($sql_result as $row)
										{
											$prev_service_work_order_no='';
											$product_type = $product_type_arr[$row[csf("id")]];
											$wo_issue_qty = $work_order_issue_qty[$row[csf("id")]];
											$wo_issue_rtn_qty = $work_order_issue_rtn_qty[$row[csf("id")]];
											$actual_wo_issue_qty = ($wo_issue_qty-$wo_issue_rtn_qty);
											$wo_pending_qty = $ydsw_from_avail_prod[$row[csf("id")]]-$actual_wo_issue_qty;
											$available_qnty = ( $row[csf("available_qnty")] - $wo_pending_qty );
											//echo $row[csf("available_qnty")].'='.$ydsw_from_avail_prod[$row[csf("id")]].'='.$work_order_issue_qty[$row[csf("id")]]."==".$work_order_issue_rtn_qty[$row[csf("id")]]."<br>";

											if($cbo_with_order==1)
											{
												$job_total_allocation_qty = $job_total_allocation_arr[$row[csf("job_no")]][$row[csf('id')]];
												//echo $row[csf("job_no")]."==".$row[csf("id")]."==".$row[csf("booking_no")];

												$previous_requsition_qty = $booking_requsition_arr[$row[csf("booking_no")]][$row[csf("id")]];

												$previous_ydsw_qty = $prod_wise_ydsw[$row[csf("job_no")]][$row[csf("id")]];
												$allocation_title = $row[csf("id")]. "->(".$job_total_allocation_qty ." - (".$previous_ydsw_qty."+".$previous_requsition_qty.") )";
												$allocation_balance = ($job_total_allocation_qty - ($previous_ydsw_qty+$previous_requsition_qty));
											}

											if($is_sales_order==1)
											{
												if($is_sales_order_variable_settings == 1 && $is_auto_allocation != 1)
												{
													$yarn_qnty = $job_total_allocation_arr[$row[csf("job_no")]][$row[csf('id')]];
												}
												else if ($variable_set_allocation == 1 && $is_auto_allocation == 1)
												{
													$yarn_qnty=$available_qnty;
													$prev_service_work_order_no=$ydsw_from_avail_prod_no[$row[csf("id")]];
												}

												if($row[csf('within_group')]==1)
												{
													$buyer_id = $row[csf('po_buyer')];
												}
												else
												{
													$buyer_id = $row[csf('buyer_id')];
												}
											}
											else
											{
												if($variable_set_allocation==1)
												{
													$yarn_qnty=($cbo_with_order==1)?$allocation_balance:$available_qnty;
													if($cbo_with_order!=1)
													{
														$prev_service_work_order_no=$ydsw_from_avail_prod_no[$row[csf("id")]];
													}
												}
												else
												{
													$yarn_qnty= $available_qnty;
													$prev_service_work_order_no=$ydsw_from_avail_prod_no[$row[csf("id")]];
												}
											}

											$prev_service_work_order_no=implode(",", array_filter(array_unique(explode(",", $prev_service_work_order_no))));

											if($yarn_qnty > 0)
											{
												if ($i%2==0)
													$bgcolor="#E9F3FF";
												else
													$bgcolor="#FFFFFF";

												$compos = '';
												if ($row[csf('yarn_comp_percent2nd')] != 0)
												{
													$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%";
												}
												else
												{
													$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%";
												}

												$ageOfDays = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d"));
												$daysOnHand = datediff("d", $date_array[$row[csf("id")]]['max_date'], date("Y-m-d"));
												$js_function=($service_type==15 || $service_type==50 || $service_type==51)?"":"2";
												if($row[csf("yarn_comp_percent2nd")]==0) $yarn_percnt="";else $yarn_percnt=$row[csf("yarn_comp_percent2nd")];
												?>
												<input type="hidden" name="txt_product_id" id="txt_product_id<?php echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
												<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value<? echo $js_function;?>('<? echo $composition[$row[csf("yarn_comp_type1st")]]." ". $row[csf("yarn_comp_percent1st")]." ". $composition[$row[csf("yarn_comp_type2nd")]]." ".$yarn_percnt." ". $yarn_type[$row[csf("yarn_type")]]." ". $color_arr[$row[csf("color")]]; ?>*<? echo $row[csf("yarn_count_id")]; ?>*<? echo $row[csf("lot")]; ?>*<? echo $row[csf("id")]; ?>*<? echo $yarn_qnty; ?>',<? echo $i;?>)" id="search<? echo $i;?>">
													<td width="40" align="center"><? echo $i; ?></td>
													<td width="70" align="center"><? echo $row[csf('booking_no')]; ?></td>
													<td width="70" align="center" style="min-width:70px !important;"><? echo $buyer_arr[$buyer_id]; ?></td>
													<td width="70" align="center"><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></td>
													<td width="170" align="center"><p><? echo $compos; ?></p></td>
													<td width="80" align="center"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
													<td width="80" align="center"><p><? echo $color_library[$row[csf('color')]]; ?></p></td>
													<td width="80" align="center" title="<? echo $row[csf("id")];?>"><p><? echo $row[csf('lot')]; ?></p></td>
													<td width="140" align="center"><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></td>
													<td style="max-width:80px !important;" width="80" align="right"><? echo number_format($row[csf('current_stock')], 2); ?></td>
													<td width="80" style="max-width:80px !important;" align="right" title="<? echo $allocation_title;?>"><? echo number_format($yarn_qnty, 2); ?></td>
													<td width="80" align="center"><? echo $ageOfDays; ?></td>
													<td width="" align="center"><? echo $daysOnHand; ?></td>
												</tr>
												<?
												$i++;
											}
											else if(!empty($prev_service_work_order_no))
											{
												$msg = "This lot already attach with service booking no :" .$prev_service_work_order_no;
												echo "<tr><td colspan='13' align='center' style='color:red'>".$msg."</td></tr>";
											}
										}
									}
									else
									{
										$msg = ($cbo_with_order==1)?"No Allocated Yarn Found":"No Yarn Found";
										echo "<tr><td colspan='13' align='center'>".$msg."</td></tr>";
									}
									?>
									<input type="hidden" id="hidden_product" style="width:100px;" />
								</tbody>
							</table>
							<table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
								<tr>
									<td align="center" height="30" valign="bottom">
										<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px;" />
									</td>
								</tr>
							</table>
						</div>
					</form>
				</fieldset>
			</div>
		</body>
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
		</html>
		<?
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	/*
	|--------------------------------------------------------------------------
	| variable settings Planning
	| if variable list is auto allocation yarn form requisition and
	| auto allocation yarn form requisition is yes then
	| available_qty = allocation_qty - (requsition_qty + yarn_dyeing_qty + yarn_service_qty)
	| otherwise available qty = product_details_mst table's available_qty
	|--------------------------------------------------------------------------
	|
	*/
	$arr = array();
	$arr['company_id'] = $cbo_company_name;
	$arr['category_id'] = 1;
	$arr['vs_auto_allocation'] = 1;
	$data_vs = get_vs_allocated_qty($arr);

	$variable_set_allocation = $data_vs['is_allocated'];
	$is_sales_order_variable_settings = $data_vs['is_sales_order'];
	$is_auto_allocation = $data_vs['is_auto_allocation'];
	//end
	/*
	echo "20**Duplicate Product is Not Allow in Same Work Order.";
	print_r($data_vs);
	disconnect($con);
	die;*/

	if ($operation==0) // insert here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if(str_replace("'","",$update_id)!="") //update
		{
			//check sys id for update or insert
			$id= return_field_value("id"," wo_yarn_dyeing_mst","id=$update_id");
			$field_array="service_type*supplier_id*booking_date*delivery_date*currency*ecchange_rate*pay_mode*ready_to_approved*source*attention*tenor*updated_by*update_date*status_active*is_deleted*booking_without_order*ref_no";
			$data_array="".$cbo_service_type."*".$cbo_supplier_name."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_pay_mode."*".$cbo_ready_to_approved."*".$cbo_source."*".$txt_attention."*".$txt_tenor."*'".$user_id."'*'".$pc_date_time."'*1*0*".$cbo_with_order."*".$txt_ref_no;
			$return_no=str_replace("'",'',$txt_booking_no);
		}
		else // new insert
		{
			$id = return_next_id_by_sequence("WO_YARN_DYEING_MST_YDW_PK_SEQ", "wo_yarn_dyeing_mst", $con);
			$new_sys_number = explode("*", return_next_id_by_sequence("WO_YARN_DYEING_MST_YSW_PK_SEQ", "wo_yarn_dyeing_mst",$con,1,$cbo_company_name,'YSW',94,date("Y",time()),0 ));

			$field_array="id,yarn_dyeing_prefix,yarn_dyeing_prefix_num,ydw_no,entry_form,company_id,service_type,supplier_id,booking_date,delivery_date,currency,ecchange_rate,pay_mode,ready_to_approved,source,attention,tenor,is_sales,ref_no,inserted_by,insert_date,status_active,is_deleted,item_category_id,booking_without_order";
			$data_array="(".$id.",'".$new_sys_number[1]."','".$new_sys_number[2]."','".$new_sys_number[0]."',94,".$cbo_company_name.",".$cbo_service_type.",".$cbo_supplier_name.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_currency.",".$txt_exchange_rate.",".$cbo_pay_mode.",".$cbo_ready_to_approved.",".$cbo_source.",".$txt_attention.",".$txt_tenor.",".$cbo_is_sales_order.",".$txt_ref_no.",'".$user_id."','".$pc_date_time."',1,0,24,".$cbo_with_order.")";
			$return_no=str_replace("'",'',$new_sys_number[0]);
		}

		//for transaction log
		$log_entry_form = 94;
		$log_ref_id = $id;
		$log_ref_number = $return_no;
		//end for transaction log

		$dtlsid=return_next_id("id","wo_yarn_dyeing_dtls", 1);
		$field_array_dts="id,mst_id,job_no,job_no_id,entry_form,product_id,count,yarn_description,uom,yarn_wo_qty,dyeing_charge,amount,no_of_bag,no_of_cone,min_require_cone,remarks,status_active,is_deleted,fab_booking_no";
		$dtls_ids="";
		$job_no=$dyeing_charge="";
		$previous_wo_qnty = 0;
		$total_wo_qnty = 0;
		$total_allocated_qty = 0;
		for($j=0; $j<$tot_row; $j++)
		{
			$txt_job_no 		= "txt_job_no_".$j;
			$txt_job_id 		= "txt_job_id_".$j;
			$txt_lot 			= "txt_lot_".$j;
			$txt_pro_id 		= "txt_pro_id_".$j;
			$cbo_count 			= "cbo_count_".$j;
			$txt_item_des 		= "txt_item_des_".$j;
			$cbo_uom 			= "cbo_uom_".$j;
			$txt_wo_qty 		= "txt_wo_qty_".$j;
			$txt_rate 			= "txt_rate_".$j;
			$txt_amount 		= "txt_amount_".$j;
			$txt_bag 			= "txt_bag_".$j;
			$txt_cone 			= "txt_cone_".$j;
			$txt_min_req_cone 	= "txt_min_req_cone_".$j;
			$txt_remarks 		= "txt_remarks_".$j;
			$txt_allocated_qty 	= "txt_allocated_qty_".$j;
			$fab_booking_no 	= "fab_booking_no_".$j;
			$txtwoqty        	= $$txt_wo_qty;

			$allocation_mst_insert = 1;
			$allocation_dtls_insert = 1;
			$allocation_mst_update = 1;
			$prod_update=1;

			if($txt_booking_no !='')
			{
				$duplicate = is_duplicate_field("b.id", "wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b", "a.id=b.mst_id and a.ydw_no=$txt_booking_no and b.job_no_id=".$$txt_job_id." and b.entry_form=94 and b.count = ".$$cbo_count." and a.status_active=1 and b.status_active=1 and b.uom=".$$cbo_uom." and b.product_id = ".$$txt_pro_id);
				if ($duplicate == 1)
				{
					echo "20**Duplicate Product is Not Allow in Same Work Order.";
					disconnect($con);die;
				}
			}

			if($data_array_dts!="")
				$data_array_dts.=",";

			$data_array_dts.="(".$dtlsid.",".$id.",'".$$txt_job_no."','".$$txt_job_id."',94,'".$$txt_pro_id."','".$$cbo_count."','".$$txt_item_des."',".$$cbo_uom.",'".$$txt_wo_qty."','".$$txt_rate."','".$$txt_amount."','".$$txt_bag."','".$$txt_cone."','".$$txt_min_req_cone."','".$$txt_remarks."',1,0,'".$$fab_booking_no."')";
			$dtls_ids.=$dtlsid.",";
			$dtlsid=$dtlsid+1;
			$job_no = $$txt_job_no;
			$pro_id = $$txt_pro_id;
			$dyeing_charge = $$txt_rate;
			$allocated_qty	 = $$txt_allocated_qty;

			if( (str_replace("'", "", $cbo_is_sales_order)==1) )
			{
				//for palmal business
				if($is_sales_order_variable_settings == 1 && $is_auto_allocation != 1)
				{
					$arr = array();
					$arr['product_id'] = $$txt_pro_id;
					$arr['is_auto_allocation'] = 0;
					$arr['job_no'] = $$txt_job_no;
					$arr['booking_no'] = $$fab_booking_no;
					$arr['po_id'] = $$txt_job_id;
					$allocation_arr = get_allocation_balance($arr);
					$booking_requisition_qty = $allocation_arr['booking_requisition'][$$fab_booking_no][$$txt_pro_id]['qty']*1;
					$yarn_dyeing_service_qty = $allocation_arr['yarn_dyeing_service'][$$txt_job_no][$$txt_pro_id]['qty']*1;
					$booking_allocation_qty = $allocation_arr['booking_allocation'][$$fab_booking_no][$$txt_pro_id]*1;
					$available_qty = $booking_allocation_qty - ($booking_requisition_qty + $yarn_dyeing_service_qty);

					if($txtwoqty > $available_qty)
					{
						echo "40**Quantity is not available for work order.\nAvailable quantity = ".$available_qty;
						disconnect($con);
						exit();
					}
				}

				//for urmi business
				if ($variable_set_allocation == 1 && $is_auto_allocation == 1)
				{
				 	$dyed_type = return_field_value("dyed_type","product_details_master","id=$pro_id");
					$check_allocation_info = sql_select("SELECT a.id AS ID, a.qnty AS QNTY FROM inv_material_allocation_mst a WHERE a.job_no = '".$job_no."' and a.item_id = ".$pro_id." and a.status_active = 1 and a.is_deleted = 0");

					if (!empty($check_allocation_info))
					{
						foreach($check_allocation_info as $row)
						{
							$allocation_id = $row['ID'];
							$allocation_qnty = $row['QNTY'] + str_replace("'", '', $$txt_wo_qty);
							$field_allocation = "qnty*updated_by*update_date";
							$data_allocation = "" . $allocation_qnty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

							$allocation_dtls_id = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
							$field_allocation_dtls = "id, mst_id, job_no, po_break_down_id, booking_no, item_category, allocation_date, item_id, qnty, is_dyied_yarn, is_sales, inserted_by, insert_date";
							$data_allocation_dtls = "(" . $allocation_dtls_id . ", " . $allocation_id . ", '" . $$txt_job_no . "', ".$$txt_job_id.", '".$$fab_booking_no."', 1, '" . date("d-M-Y") . "', " . $$txt_pro_id . ", " . $allocation_qnty . ", $dyed_type, 1, " . $_SESSION['logic_erp']['user_id'] . ", '" . $pc_date_time . "')";
							$allocation_mst_update = sql_update("inv_material_allocation_mst", $field_allocation, $data_allocation, "id", "" . $allocation_id . "", 0);
							$allocation_dtls_delete = execute_query("delete from inv_material_allocation_dtls where mst_id='$allocation_id'", 1);
							$allocation_dtls_insert = sql_insert("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, 0);
							$allocation_mst_insert=1;
						}
					}
					else
					{
						//for inv_material_allocation_mst table
						$allocation_id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
						$field_allocation = "id ,mst_id, entry_form, job_no, po_break_down_id, booking_no, item_category, allocation_date, item_id, qnty, is_dyied_yarn, is_sales, inserted_by, insert_date";
						$data_allocation = "(" . $allocation_id . ",".$id.",94,'" . $$txt_job_no . "',".$$txt_job_id.",'".$$fab_booking_no."',1,'" . date("d-M-Y") . "'," . $$txt_pro_id . "," . $$txt_wo_qty . ",$dyed_type,1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

						//for inv_material_allocation_dtls table
						$allocation_dtls_id = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
						$field_allocation_dtls = "id, mst_id, job_no, po_break_down_id, booking_no, item_category, allocation_date, item_id, qnty, is_dyied_yarn, is_sales, inserted_by, insert_date";
						$data_allocation_dtls = "(" . $allocation_dtls_id . "," . $allocation_id . ",'" . $$txt_job_no . "',".$$txt_job_id.",'".$$fab_booking_no."',1,'" . date("d-M-Y") . "'," . $$txt_pro_id . "," . $$txt_wo_qty . ",$dyed_type,1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

						$allocation_mst_insert = sql_insert("inv_material_allocation_mst", $field_allocation, $data_allocation, 0);
						$allocation_dtls_insert = sql_insert("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, 0);
						$allocation_mst_update=1;
					}

					//for product_details_master table
					$wo_qty = $$txt_wo_qty;
					$pro_id = $$txt_pro_id;
					$prod_update = execute_query("update product_details_master set allocated_qnty=(allocated_qnty+$wo_qty),available_qnty=(available_qnty-$wo_qty),update_date='" . $pc_date_time . "' where id=$pro_id", 0);

					//for transaction log
					$sql_prod = sql_select("SELECT CURRENT_STOCK, ALLOCATED_QNTY, AVAILABLE_QNTY, DYED_TYPE FROM PRODUCT_DETAILS_MASTER WHERE ID = ".$pro_id);
					foreach($sql_prod as $row)
					{
						$log_prod_id = $pro_id;
						$log_current_stock = $row['CURRENT_STOCK'];
						$log_allocated_qty = $row['ALLOCATED_QNTY'];
						$log_available_qty = $row['AVAILABLE_QNTY'];
						$log_dyed_type = $row['DYED_TYPE'];
					}
				}
			}
			else
			{
				if( str_replace("'","",$cbo_with_order)==1 )
				{
					$total_allocated_qty = return_field_value("sum(qnty) as allocated_qnty"," inv_material_allocation_dtls","job_no='".$job_no."' and status_active=1 and is_deleted=0 and item_id=".$pro_id." group by job_no","allocated_qnty");

					$prev_booking = return_field_value("sum(yarn_wo_qty) as yarn_wo_qty"," wo_yarn_dyeing_dtls","job_no='".$job_no."' and status_active=1 and is_deleted=0 and product_id=".$pro_id." group by job_no","yarn_wo_qty");

					$issue_return_qnty = return_field_value("sum(c.cons_quantity) as issue_return_qnty"," wo_yarn_dyeing_dtls a, inv_receive_master b, inv_transaction c "," a.mst_id=b.booking_id and b.id=c.mst_id  and a.job_no='".$job_no."' and c.prod_id=".$pro_id." and b.entry_form=9 and b.item_category=1 and c.item_category=1 and c.transaction_type=4 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 group by a.job_no","issue_return_qnty");

					$previous_wo_qnty = ($prev_booking-$issue_return_qnty);
					$total_wo_qnty = ($txtwoqty+$previous_wo_qnty);
					if(($total_wo_qnty*1)>($total_allocated_qty*1))
					{
						echo "40**Work Order Quantity Does Not Allow More Then Allocated qty.";
						disconnect($con);
						die;
					}
				}
				else
				{
					// valid with available qty
					if($txtwoqty>$allocated_qty)
					{
						echo "40**Work Order Quantity Does Not Allow More Then Available qty.";
						disconnect($con);
						die;
					}
				}
			}
		} // end for loop

		$data_array_fin_prod="";
		$new_array_color=array();
		if(str_replace("'",'',$cbo_service_type) == 15 || str_replace("'",'',$cbo_service_type) == 50 || str_replace("'",'',$cbo_service_type) == 51)
		{
			$fin_prod_id=return_next_id("id", "wo_yarn_dyeing_dtls_fin_prod", 1);
			if (str_replace("'", "", trim($txt_fin_color)) != "")
			{
				if (!in_array(str_replace("'", "", trim($txt_fin_color)),$new_array_color))
				{
					$color_id = return_id( str_replace("'", "", trim($txt_fin_color)), $color_arr, "lib_color", "id,color_name","94");
					$new_array_color[$color_id]=str_replace("'", "", trim($txt_fin_color));
				}
				else
				{
					$color_id =  array_search(str_replace("'", "", trim($txt_fin_color)), $new_array_color);
				}
			}
			else
			{
				$color_id=0;
			}

			$field_array_fin_prod="id,mst_id,dtls_id,yarn_count,yarn_comp,yarn_perc,yarn_type,yarn_color,status_active,is_deleted,job_no,yarn_rate";
			$data_array_fin_prod="(".$fin_prod_id.",".$id.",'".trim($dtls_ids,", ")."',".$cbo_fin_count.",".$cbo_fin_composition.",".$txt_fin_perc.",".$cbo_fin_type.",".$color_id.",1,0,'".$job_no."',".$dyeing_charge.")";
		}

		if(str_replace("'","",$update_id)!="") //update
		{
			$rID=sql_update("wo_yarn_dyeing_mst",$field_array,$data_array,"id",$id,0);
		}
		else
		{
			//echo "10**"."insert into wo_yarn_dyeing_mst (".$field_array.") values ".$data_array;die;
			$rID=sql_insert("wo_yarn_dyeing_mst",$field_array,$data_array,0);
		}

		$dtlsrID=sql_insert("wo_yarn_dyeing_dtls",$field_array_dts,$data_array_dts,0);

		if($data_array_fin_prod!="")
		{
			//echo "10**"."insert into wo_yarn_dyeing_dtls_fin_prod (".$field_array_fin_prod.") values ".$data_array_fin_prod;die;
			$finProdrID=sql_insert("wo_yarn_dyeing_dtls_fin_prod",$field_array_fin_prod,$data_array_fin_prod,0);
		}
		else
		{
			$finProdrID=1;
		}

		/*echo '10**'.$rID.'**'.$dtlsrID.'**'.$finProdrID .'**'. $allocation_mst_insert .'**'. $allocation_dtls_insert .'**'. $allocation_mst_update .'**'. $prod_update;
		oci_rollback($con);
		disconnect($con);
		die;*/

		if($db_type==0)
		{
			if($rID && $dtlsrID && $finProdrID && $allocation_mst_insert && $allocation_dtls_insert && $allocation_mst_update && $prod_update)
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id);
			}
		}

		if($db_type==1 || $db_type==2)
		{
			if($rID && $dtlsrID && $finProdrID && $allocation_mst_insert && $allocation_dtls_insert && $allocation_mst_update && $prod_update)
			{
				//for transaction log
				$log_data = array();
				$log_data['entry_form'] = $log_entry_form;
				$log_data['ref_id'] = $log_ref_id;
				$log_data['ref_number'] = $log_ref_number;
				$log_data['product_id'] = $log_prod_id;
				$log_data['current_stock'] = $log_current_stock;
				$log_data['allocated_qty'] = $log_allocated_qty;
				$log_data['available_qty'] = $log_available_qty;
				$log_data['dyed_type'] = $log_dyed_type;
				$log_data['insert_date'] = $pc_date_time;
				manage_allocation_transaction_log($log_data);
				//end for transaction log

				oci_commit($con);
				echo "0**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id);
			}
		}

		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$update_dtls_ids=implode(",",explode("_",str_replace("'","",$update_dtls_ids)));
		$original_pro_data_arr=explode(",",str_replace("'","",$original_pro_data));

		//check update id
		if( str_replace("'","",$update_id) == "")
		{
			echo "15";
			disconnect($con);
			exit();
		}

		$approved_sql = "select a.is_approved from wo_yarn_dyeing_mst a where a.id=$update_id  and a.status_active=1 and a.is_approved!=0 and a.is_deleted=0";
        $approved_arr=sql_select($approved_sql);
        if(count($approved_arr)>0)
        {
            if($approved_arr[0][csf('is_approved')]==1)
            {
                echo "13**Update Or Delete not allowed. Approved Found"; disconnect($con);die;
            }
            else
            {
                echo "13**Update Or Delete not allowed. Partial Approved Found"; disconnect($con);die;
            }
        }
		
		//mst part.......................
		$field_array="service_type*supplier_id*booking_date*delivery_date*currency*ecchange_rate*pay_mode*ready_to_approved*source*attention*tenor*is_sales*updated_by*update_date*status_active*is_deleted*booking_without_order*ref_no";
		$data_array="".$cbo_service_type."*".$cbo_supplier_name."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_pay_mode."*".$cbo_ready_to_approved."*".$cbo_source."*".$txt_attention."*".$txt_tenor."*".$cbo_is_sales_order."*'".$user_id."'*'".$pc_date_time."'*1*0*".$cbo_with_order."*".$txt_ref_no;

		$dtlsid=return_next_id("id","wo_yarn_dyeing_dtls", 1);
		$field_array_dts="id,mst_id,job_no,job_no_id,entry_form,product_id,count,yarn_description,uom,yarn_wo_qty,dyeing_charge,amount,no_of_bag,no_of_cone,min_require_cone,remarks,status_active,is_deleted,fab_booking_no";

		$dtls_ids='';
		$job_no='';
		$dyeing_charge='';
		$product_ids='';
		$total_wo_qnty==0;
		$prev_booking==0;
		$issue_return_qnty==0;
		$previous_wo_qnty==0;
		$wo_qnty=0;

		for($j=0; $j<$tot_row; $j++)
		{
			$txt_job_no 		= "txt_job_no_".$j;
			$txt_job_id 		= "txt_job_id_".$j;
			$txt_lot 			= "txt_lot_".$j;
			$txt_pro_id 		= "txt_pro_id_".$j;
			$cbo_count 			= "cbo_count_".$j;
			$txt_item_des 		= "txt_item_des_".$j;
			$cbo_uom 			= "cbo_uom_".$j;
			$txt_wo_qty 		= "txt_wo_qty_".$j;
			$txt_rate 			= "txt_rate_".$j;
			$txt_amount 		= "txt_amount_".$j;
			$txt_bag 			= "txt_bag_".$j;
			$txt_cone 			= "txt_cone_".$j;
			$txt_min_req_cone 	= "txt_min_req_cone_".$j;
			$txt_remarks 		= "txt_remarks_".$j;
			$txt_allocated_qty 	= "txt_allocated_qty_".$j;
			$dtls_update_id 	= "dtls_update_id_".$j;
			$fab_booking_no 	= "fab_booking_no_".$j;

			if($txt_booking_no !='')
			{
				$duplicate = is_duplicate_field("b.id", "wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b", "a.id=b.mst_id and a.ydw_no=$txt_booking_no and b.job_no_id=$$txt_job_id and b.entry_form=94 and b.count = $$cbo_count and a.status_active=1 and b.status_active=1 and b.uom=$$cbo_uom and b.id <> $$dtls_update_id and b.product_id = ".$$txt_pro_id);
				if ($duplicate == 1)
				{
					echo "20**Duplicate Product is Not Allow in Same Work Order.";
					disconnect($con);die;
				}
			}

			//yarn issue checking
			$issue_sql = "select LISTAGG(b.issue_number_prefix_num, '; ')
			WITHIN GROUP (ORDER BY b.id) as ISSUE_MRR_NO,sum(c.cons_quantity) as ISSUE_QNTY from inv_issue_master b, inv_transaction c where b.id=c.mst_id and b.entry_form=3 and b.buyer_job_no='".$$txt_job_no."' and b.booking_no=$txt_booking_no and c.prod_id=".$$txt_pro_id." and b.item_category=1 and c.item_category=1 and c.transaction_type=2 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 group by b.buyer_job_no";
			$issue_result =  sql_select($issue_sql);
			$total_issue_qty = $issue_result[0]['ISSUE_QNTY'];

			$return_sql = "select LISTAGG(b.recv_number_prefix_num, '; ')
			WITHIN GROUP (ORDER BY b.id) as RTN_MRR_NO,sum(c.cons_quantity) as ISSUE_RET_QNTY from inv_receive_master b, inv_transaction c where b.id=c.mst_id and b.entry_form=9 and b.booking_no=$txt_booking_no and c.prod_id=".$$txt_pro_id ." and b.item_category=1 and c.item_category=1 and c.transaction_type=4 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 group by b.booking_no";

			$return_result =  sql_select($return_sql);
			$total_issue_return_qty = $return_result[0]['ISSUE_RET_QNTY'];
			$actual_issue_qty = number_format($total_issue_qty-$total_issue_return_qty,2,".","");
			$work_order_qty = number_format($$txt_wo_qty,2,".","");

			//echo "30**".$actual_issue_qty."==".$total_issue_qty."===".$total_issue_return_qty; die;
			if( count($issue_result)>0 && $work_order_qty<$actual_issue_qty)
			{
				$issue_mrr_no = $issue_result[0]['ISSUE_MRR_NO'];
				$issue_rtn_mrr_no = $return_result[0]['RTN_MRR_NO'];
				
				$rtn_mrr_msg = (count($return_result)>0)?"\nIssue return found.\nReturn MRR no=$issue_rtn_mrr_no\nIssue return quantity=$total_issue_return_qty":"";

				echo "30**Issue Found, Work order quantity can not be less than Issue quantity.\nIssue MRR no=$issue_mrr_no\nIssue Quantity= $total_issue_qty".$rtn_mrr_msg;
				disconnect($con);die;
			}

			if($data_array_dts!="") $data_array_dts.=",";
			$data_array_dts.="(".$dtlsid.",".$update_id.",'".$$txt_job_no."','".$$txt_job_id."',94,'".$$txt_pro_id."','".$$cbo_count."','".$$txt_item_des."',".$$cbo_uom.",'".$$txt_wo_qty."','".$$txt_rate."','".$$txt_amount."','".$$txt_bag."','".$$txt_cone."','".$$txt_min_req_cone."','".$$txt_remarks."',1,0,'".$$fab_booking_no."')";

			$dtls_ids.=$dtlsid.",";
			$dtlsid=$dtlsid+1;
			$job_no = $$txt_job_no;
			$job_id = $$txt_job_id;
			$pro_id = $$txt_pro_id;
			$dyeing_charge = $$txt_rate;
			$product_ids .= $$txt_pro_id.",";
			$total_wo_qnty += $$txt_wo_qty;
			$txtwoqty = $$txt_wo_qty;
			$allocated_qty	 = $$txt_allocated_qty;

			$allocation_mst_insert = 1;
			$allocation_dtls_insert = 1;
			$allocation_mst_update = 1;
			$prod_update=1;

			//for sales order
			if( (str_replace("'","",$cbo_is_sales_order) == 1 ) )
			{
				$origin_data_arr = explode("_", $original_pro_data_arr[$j]);
				$origin_prod_id = $origin_data_arr[0];
				$original_pro_qnty =  $origin_data_arr[1];
				$dyed_type = return_field_value("dyed_type","product_details_master","id=$pro_id");

				//for transaction log
				$log_entry_form = 94;
				$log_ref_id = str_replace("'","",$update_id);
				$log_ref_number = str_replace("'","",$txt_booking_no);
				//end for transaction log

				if($pro_id != $origin_prod_id) // New lot
				{
					// CHECK YARN STOCK
					$check_issue = sql_select("select sum(b.cons_quantity) cons_quantity from inv_issue_master a,inv_transaction b where a.id=b.mst_id and b.receive_basis=1 and b.transaction_type=2 and b.item_category=1 and b.job_no='$job_no' and b.prod_id=$origin_prod_id and a.booking_id=$update_id and b.status_active=1 and b.is_deleted=0");
					if($check_issue[0][csf("cons_quantity")] > 0 || $check_issue[0][csf("cons_quantity")] != null)
					{
						echo "17**Issue found.You can not change this lot.";
						disconnect($con);
						exit();
					}

					//for palmal business
					if($is_sales_order_variable_settings == 1 && $is_auto_allocation != 1)
					{
						$arr = array();
						$arr['product_id'] = $$txt_pro_id;
						$arr['is_auto_allocation'] = 0;
						$arr['job_no'] = $$txt_job_no;
						$arr['booking_no'] = $$fab_booking_no;
						$arr['po_id'] = $$txt_job_id;
						$allocation_arr = get_allocation_balance($arr);
						$booking_requisition_qty = $allocation_arr['booking_requisition'][$$fab_booking_no][$$txt_pro_id]['qty']*1;
						$yarn_dyeing_service_qty = $allocation_arr['yarn_dyeing_service'][$$txt_job_no][$$txt_pro_id]['qty']*1;
						$booking_allocation_qty = $allocation_arr['booking_allocation'][$$fab_booking_no][$$txt_pro_id]*1;
						$available_qty = $booking_allocation_qty - ($booking_requisition_qty + $yarn_dyeing_service_qty);

						if($txtwoqty > $available_qty)
						{
							echo "40**Quantity is not available for work order.\nAvailable quantity = ".$available_qty;
							disconnect($con);
							exit();
						}
					}

					//for urmi business
					if ($variable_set_allocation == 1 && $is_auto_allocation == 1)
					{
						//CHECK PREVIOUS PRODUCT ALLOCATION
						$sql_allocation = "select a.* from inv_material_allocation_mst a where a.job_no='".$job_no."' and a.item_id=".$origin_prod_id." and a.status_active=1 and a.is_deleted=0";
						$check_allocation_array = sql_select($sql_allocation);
						if (!empty($check_allocation_array))
						{
							$mst_id = $check_allocation_array[0][csf('id')];
							// UPDATE PREVIOUS PRODUCT ALLOCATION MST
							execute_query("update inv_material_allocation_mst set qnty=(qnty-$original_pro_qnty),updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$mst_id and job_no='$job_no' and item_id=$origin_prod_id", 0);
							// UPDATE PREVIOUS PRODUCT ALLOCATION DTLS
							execute_query("update inv_material_allocation_dtls set qnty=(qnty-$original_pro_qnty),updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$mst_id and job_no='$job_no' and item_id=$origin_prod_id", 0);

							// UPDATE PREVIOUS PRODUCT DETAILS
							execute_query("update product_details_master set allocated_qnty=(allocated_qnty-$original_pro_qnty) where id=$origin_prod_id", 0);
							execute_query("update product_details_master set available_qnty=(current_stock-allocated_qnty),update_date='" . $pc_date_time . "' where id=$origin_prod_id  ", 0);

							//for transaction log
							$sql_prod = sql_select("SELECT CURRENT_STOCK, ALLOCATED_QNTY, AVAILABLE_QNTY, DYED_TYPE FROM PRODUCT_DETAILS_MASTER WHERE ID = ".$origin_prod_id);
							foreach($sql_prod as $row)
							{
								$log_prod_id = $origin_prod_id;
								$log_current_stock = $row['CURRENT_STOCK'];
								$log_allocated_qty = $row['ALLOCATED_QNTY'];
								$log_available_qty = $row['AVAILABLE_QNTY'];
								$log_dyed_type = $row['DYED_TYPE'];
							}
						}

						//for new product
						$check_allocation_info = sql_select("select a.* from inv_material_allocation_mst a where a.job_no='$job_no' and a.item_id=$pro_id and a.status_active=1 and a.is_deleted=0");
						if (!empty($check_allocation_info))
						{
							$allocation_id = $check_allocation_info[0][csf('id')];
							$allocation_qnty = $check_allocation_info[0][csf('qnty')];
							$new_allocate_data = ($allocation_qnty-$original_pro_qnty + $$txt_wo_qty);
							$pro_allocate_data = ($$txt_wo_qty-$original_pro_qnty);

							execute_query("update inv_material_allocation_mst set qnty=$new_allocate_data,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$allocation_id",0);
							execute_query("update inv_material_allocation_dtls set qnty=$new_allocate_data,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$allocation_id and item_id=$pro_id", 0);
							execute_query("update product_details_master set allocated_qnty=(allocated_qnty+$pro_allocate_data),update_date='" . $pc_date_time . "' where id=$pro_id", 0);
							execute_query("update product_details_master set available_qnty=(current_stock-allocated_qnty),update_date='" . $pc_date_time . "' where id=$pro_id  ", 0);

							//for transaction log
							$sql_prod = sql_select("SELECT CURRENT_STOCK, ALLOCATED_QNTY, AVAILABLE_QNTY, DYED_TYPE FROM PRODUCT_DETAILS_MASTER WHERE ID = ".$pro_id);
							foreach($sql_prod as $row)
							{
								$log_prod_id1 = $pro_id;
								$log_current_stock1 = $row['CURRENT_STOCK'];
								$log_allocated_qty1 = $row['ALLOCATED_QNTY'];
								$log_available_qty1 = $row['AVAILABLE_QNTY'];
								$log_dyed_type1 = $row['DYED_TYPE'];
							}
						}
						else
						{
							$allocation_id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
							$field_allocation = "id, mst_id, entry_form, job_no, po_break_down_id, booking_no, item_category, allocation_date, item_id, qnty, is_dyied_yarn, is_sales, inserted_by, insert_date";
							$data_allocation = "(" . $allocation_id . ",".$update_id.",94,'" . $job_no . "',".$job_id.",'".$$fab_booking_no."',1" . ",'" . date("d-M-Y") . "'," . $pro_id . "," . $$txt_wo_qty . ",0,1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

							$allocation_dtls_id = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
							$field_allocation_dtls = "id, mst_id, job_no, po_break_down_id, booking_no, item_category, allocation_date, item_id, qnty, is_dyied_yarn, is_sales, inserted_by, insert_date";
							$data_allocation_dtls = "(" . $allocation_dtls_id . "," . $allocation_id . ",'" . $job_no . "',".$job_id.",'".$$fab_booking_no."',1,'" . date("d-M-Y") . "'," . $pro_id . "," . $$txt_wo_qty . ",0,1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

							$allocation_mst_insert = sql_insert("inv_material_allocation_mst", $field_allocation, $data_allocation, 0);
							if ($allocation_mst_insert)
							{
								$allocation_dtls_insert = sql_insert("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, 0);
							}

							$prod_update = execute_query("update product_details_master set allocated_qnty=(allocated_qnty+$$txt_wo_qty),update_date='" . $pc_date_time . "' where id=$pro_id", 0);
							execute_query("update product_details_master set available_qnty=(current_stock-allocated_qnty),update_date='" . $pc_date_time . "' where id=$pro_id  ", 0);

							//for transaction log
							$sql_prod = sql_select("SELECT CURRENT_STOCK, ALLOCATED_QNTY, AVAILABLE_QNTY, DYED_TYPE FROM PRODUCT_DETAILS_MASTER WHERE ID = ".$pro_id);
							foreach($sql_prod as $row)
							{
								$log_prod_id2 = $pro_id;
								$log_current_stock2 = $row['CURRENT_STOCK'];
								$log_allocated_qty2 = $row['ALLOCATED_QNTY'];
								$log_available_qty2 = $row['AVAILABLE_QNTY'];
								$log_dyed_type2 = $row['DYED_TYPE'];
							}
						}
					}
				}
				else
				{
					//for palmal business
					if($is_sales_order_variable_settings == 1 && $is_auto_allocation != 1)
					{
						$arr = array();
						$arr['product_id'] = $$txt_pro_id;
						$arr['is_auto_allocation'] = 0;
						$arr['job_no'] = $$txt_job_no;
						$arr['booking_no'] = $$fab_booking_no;
						$arr['po_id'] = $$txt_job_id;
						$allocation_arr = get_allocation_balance($arr);
						$booking_requisition_qty = $allocation_arr['booking_requisition'][$$fab_booking_no][$$txt_pro_id]['qty']*1;
						$yarn_dyeing_service_qty = $allocation_arr['yarn_dyeing_service'][$$txt_job_no][$$txt_pro_id]['qty']*1;
						$booking_allocation_qty = $allocation_arr['booking_allocation'][$$fab_booking_no][$$txt_pro_id]*1;
						$available_qty = ($booking_allocation_qty + $original_pro_qnty) - ($booking_requisition_qty + $yarn_dyeing_service_qty);

						if($txtwoqty > $available_qty)
						{
							echo "40**Quantity is not available $is_sales_order_variable_settings ==== $is_auto_allocation for work order.\nAvailable quantity = ".$available_qty;
							disconnect($con);
							exit();
						}
					}

					//for urmi business
					if ($variable_set_allocation == 1 && $is_auto_allocation == 1)
					{
						// CHECK YARN STOCK
						$check_allocation_info = sql_select("select a.* from inv_material_allocation_mst a where a.job_no='$job_no' and a.item_id=$pro_id and a.status_active=1 and a.is_deleted=0");
						if (!empty($check_allocation_info))
						{
							$allocation_id = $check_allocation_info[0][csf('id')];
							$allocation_qnty = $check_allocation_info[0][csf('qnty')];
							$new_allocate_data = ($allocation_qnty-$original_pro_qnty + $$txt_wo_qty);
							$pro_allocate_data = ($$txt_wo_qty-$original_pro_qnty);

							$allocation_mst_insert = execute_query("update inv_material_allocation_mst set qnty=$new_allocate_data,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$allocation_id",0);

							if ($allocation_mst_insert)
							{
								$allocation_dtls_insert = execute_query("update inv_material_allocation_dtls set qnty=$new_allocate_data,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$allocation_id and item_id=$pro_id", 0);
							}

							if($allocation_mst_insert && $allocation_dtls_insert )
							{
								$prod_update = execute_query("update product_details_master set allocated_qnty=(allocated_qnty+$pro_allocate_data),update_date='" . $pc_date_time . "' where id=$pro_id", 0);

								if($prod_update)
								{
									execute_query("update product_details_master set available_qnty=(current_stock-allocated_qnty),update_date='" . $pc_date_time . "' where id=$pro_id  ", 0);
								}
							}

							//for transaction log
							$sql_prod = sql_select("SELECT CURRENT_STOCK, ALLOCATED_QNTY, AVAILABLE_QNTY, DYED_TYPE FROM PRODUCT_DETAILS_MASTER WHERE ID = ".$pro_id);
							foreach($sql_prod as $row)
							{
								$log_prod_id = $pro_id;
								$log_current_stock = $row['CURRENT_STOCK'];
								$log_allocated_qty = $row['ALLOCATED_QNTY'];
								$log_available_qty = $row['AVAILABLE_QNTY'];
								$log_dyed_type = $row['DYED_TYPE'];
							}
						}
					}
				}
			}
			else
			{
				if( str_replace("'","",$cbo_with_order)==1 )
				{
					$total_allocated_qty = return_field_value("sum(qnty) as allocated_qnty"," inv_material_allocation_dtls","job_no='$job_no' and status_active=1 and is_deleted=0 and item_id=$pro_id group by job_no","allocated_qnty");

					$prev_booking = return_field_value("sum(yarn_wo_qty) as yarn_wo_qty"," wo_yarn_dyeing_dtls","job_no='$job_no' and product_id=$pro_id and entry_form=94 and status_active=1 and is_deleted=0 and id not in($update_dtls_ids) group by job_no","yarn_wo_qty");

					$issue_return_qnty = return_field_value("sum(c.cons_quantity) as issue_return_qnty"," wo_yarn_dyeing_dtls a, inv_receive_master b, inv_transaction c "," a.mst_id=b.booking_id and b.id=c.mst_id  and a.job_no='$job_no' and c.prod_id=$pro_id and b.entry_form=9 and b.item_category=1 and c.item_category=1 and c.transaction_type=4 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 group by a.job_no","issue_return_qnty");

					$previous_wo_qnty = ($prev_booking-$issue_return_qnty);
					$total_wo_qnty = ($txtwoqty+$previous_wo_qnty);
					if(($total_wo_qnty*1)>($total_allocated_qty))
					{
						echo "40**Work Order Quantity Does Not Allow More Then Allocated qty."; disconnect($con);die;
					}
				}
				else
				{
					if($txtwoqty>$allocated_qty)
					{
						echo "40**Work Order Quantity Does Not Allow More Then Available qty."; disconnect($con);die;
					}
				}
			}
		}

		$data_array_fin_prod="";
		if(str_replace("'",'',$cbo_service_type) == 15 || str_replace("'",'',$cbo_service_type) == 50 || str_replace("'",'',$cbo_service_type) == 51)
		{
			if (str_replace("'", "", trim($txt_fin_color)) != "")
			{
				//$color_id = return_id(str_replace("'", "", trim($txt_fin_color)), $color_library, "lib_color", "id,color_name");
				if (!in_array(str_replace("'", "", trim($txt_fin_color)),$new_array_color))
				{
					$color_id = return_id( str_replace("'", "", trim($txt_fin_color)), $color_arr, "lib_color", "id,color_name","94");
					$new_array_color[$color_id]=str_replace("'", "", trim($txt_fin_color));
				}
				else $color_id =  array_search(str_replace("'", "", trim($txt_fin_color)), $new_array_color);
			}
			else $color_id = 0;

			$field_array_fin_prod="dtls_id*yarn_count*yarn_comp*yarn_perc*yarn_type*yarn_color*job_no*yarn_rate";
			$data_array_fin_prod="'".trim($dtls_ids,", ")."'*".$cbo_fin_count."*".$cbo_fin_composition."*".$txt_fin_perc."*".$cbo_fin_type."*".$color_id."*'".$job_no."'*".$dyeing_charge;
		}

		$rID=sql_update("wo_yarn_dyeing_mst",$field_array,$data_array,"id",$update_id,1);
		if($data_array_dts!="")
		{
			//echo "10**"."insert into wo_yarn_dyeing_dtls (".$field_array_dts.") values ".$data_array_dts;die;
			$dtlsrID=sql_insert("wo_yarn_dyeing_dtls",$field_array_dts,$data_array_dts,1);
			//echo "10**delete from wo_yarn_dyeing_dtls where dtls_id in(".$update_dtls_ids.")";die;
			$delete_previous_dtls_rows = execute_query("delete from wo_yarn_dyeing_dtls where id in(".$update_dtls_ids.")", 0);
		}

		if($data_array_fin_prod!="")
		{
			$finProdrID=sql_update("wo_yarn_dyeing_dtls_fin_prod",$field_array_fin_prod,$data_array_fin_prod,"id",$hdn_fin_update_id,1);
		}
		else
		{
			$finProdrID=1;
		}

		/*echo "10** $rID && $dtlsrID && $finProdrID && $delete_previous_dtls_rows && $allocation_dtls_insert && $allocation_mst_insert && $prod_update";
		oci_rollback($con);
		disconnect($con);
		die();*/

		if($db_type==0)
		{
			if($rID && $dtlsrID && $finProdrID && $delete_previous_dtls_rows && $allocation_dtls_insert && $allocation_mst_insert && $prod_update)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
		}
		if($db_type==2)
		{
			if($rID && $dtlsrID && $finProdrID && $delete_previous_dtls_rows && $allocation_dtls_insert && $allocation_mst_insert && $prod_update)
			{
				//for transaction log
				$log_data = array();
				$log_data['entry_form'] = $log_entry_form;
				$log_data['ref_id'] = $log_ref_id;
				$log_data['ref_number'] = $log_ref_number;
				$log_data['product_id'] = $log_prod_id;
				$log_data['current_stock'] = $log_current_stock;
				$log_data['allocated_qty'] = $log_allocated_qty;
				$log_data['available_qty'] = $log_available_qty;
				$log_data['dyed_type'] = $log_dyed_type;
				$log_data['insert_date'] = $pc_date_time;
				manage_allocation_transaction_log($log_data);

				if($pro_id != $origin_prod_id) // New lot
				{
					$log_data = array();
					$log_data['entry_form'] = $log_entry_form;
					$log_data['ref_id'] = $log_ref_id;
					$log_data['ref_number'] = $log_ref_number;
					$log_data['product_id'] = $log_prod_id1;
					$log_data['current_stock'] = $log_current_stock1;
					$log_data['allocated_qty'] = $log_allocated_qty1;
					$log_data['available_qty'] = $log_available_qty1;
					$log_data['dyed_type'] = $log_dyed_type1;
					$log_data['insert_date'] = $pc_date_time;
					manage_allocation_transaction_log($log_data);

					$log_data = array();
					$log_data['entry_form'] = $log_entry_form;
					$log_data['ref_id'] = $log_ref_id;
					$log_data['ref_number'] = $log_ref_number;
					$log_data['product_id'] = $log_prod_id2;
					$log_data['current_stock'] = $log_current_stock2;
					$log_data['allocated_qty'] = $log_allocated_qty2;
					$log_data['available_qty'] = $log_available_qty2;
					$log_data['dyed_type'] = $log_dyed_type2;
					$log_data['insert_date'] = $pc_date_time;
					manage_allocation_transaction_log($log_data);
				}
				//end for transaction log

				oci_commit($con);
				echo "1**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$update_id=str_replace("'","",$update_id);
		$dtls_update_id=str_replace("'","",$update_dtls_ids);
		$txt_booking_no=str_replace("'","",$txt_booking_no);
		// master table delete here---------------------------------------
		if($update_id=="" || $update_id==0)
		{
			echo "15**0";
			disconnect($con);
			die;
		}

		$approved_sql = "select a.is_approved from wo_yarn_dyeing_mst a where a.id=$update_id  and a.status_active=1 and a.is_approved!=0 and a.is_deleted=0";
        $approved_arr=sql_select($approved_sql);
        if(count($approved_arr)>0)
        {
            if($approved_arr[0][csf('is_approved')]==1)
            {
                echo "13**Update Or Delete not allowed. Approved Found"; disconnect($con);die;
            }
            else
            {
                echo "13**Update Or Delete not allowed. Partial Approved Found"; disconnect($con);die;
            }
        }

		if((str_replace("'",'',$cbo_service_type) == 15 || str_replace("'",'',$cbo_service_type) == 50 || str_replace("'",'',$cbo_service_type) == 51) && str_replace("'",'',$hdn_fin_update_id)!="")
		{
			$product_ids="";
			for($j=0; $j<$tot_row; $j++)
			{
				$txt_pro_id 		= "txt_pro_id_".$j;
				$product_ids .= $$txt_pro_id.",";

				$product_ids = rtrim($product_ids,", ");
				$issue_sql = sql_select("select LISTAGG(b.issue_number_prefix_num, '; ')
				WITHIN GROUP (ORDER BY b.id) as ISSUE_MRR_NO,sum(c.cons_quantity) as cons_quantity from wo_yarn_dyeing_mst a,inv_issue_master b, inv_transaction c where b.booking_id=a.id and b.id=c.mst_id and a.ydw_no='$txt_booking_no' and b.booking_no='$txt_booking_no' and c.prod_id in($product_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.item_category=1 and c.item_category=1 and c.transaction_type=2");
			
				$total_issue_qty = $issue_sql[0][csf('cons_quantity')];

				if($total_issue_qty!="" || $total_issue_qty>0)
				{
					$issue_mrr_no = $issue_sql[0]['ISSUE_MRR_NO'];
					echo "22**Issue Found.\nIssue MRR no=$issue_mrr_no\nYarn Service Work Order can not be deleted."; disconnect($con);die;
				}

				$dtlsrID = sql_update("wo_yarn_dyeing_dtls",'status_active*is_deleted','0*1',"mst_id",$update_id,0);
			}
		}
		else
		{
			$txt_pro_id = "txt_pro_id_0";
			$product_ids = $$txt_pro_id;

			$product_ids = rtrim($product_ids,", ");
			$issue_sql = sql_select("select LISTAGG(b.issue_number_prefix_num, '; ')
			WITHIN GROUP (ORDER BY b.id) as ISSUE_MRR_NO,sum(c.cons_quantity) as cons_quantity from wo_yarn_dyeing_mst a,inv_issue_master b, inv_transaction c where b.booking_id=a.id and b.id=c.mst_id and a.ydw_no='$txt_booking_no' and b.booking_no='$txt_booking_no' and c.prod_id in($product_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.item_category=1 and c.item_category=1 and c.transaction_type=2");
		
			$total_issue_qty = $issue_sql[0][csf('cons_quantity')];

			if($total_issue_qty!="" || $total_issue_qty>0)
			{
				$issue_mrr_no = $issue_sql[0]['ISSUE_MRR_NO'];
				echo "22**Issue Found.\nIssue MRR no=$issue_mrr_no\nYarn Service Work Order can not be deleted."; disconnect($con);die;
			}

			$dtlsrID = sql_update("wo_yarn_dyeing_dtls",'status_active*is_deleted','0*1',"id",$dtls_update_id,0);
		}
		
		if((str_replace("'",'',$cbo_service_type) == 15 || str_replace("'",'',$cbo_service_type) == 50 || str_replace("'",'',$cbo_service_type) == 51) && str_replace("'",'',$hdn_fin_update_id)!="")
		{
			$finProductID = sql_update("wo_yarn_dyeing_dtls_fin_prod",'status_active*is_deleted','0*1',"mst_id",$update_id,1);
		}
		else
		{
			$finProductID=1;
		}

		for($j=0; $j<$tot_row; $j++)
		{
			$txt_job_no 		= "txt_job_no_".$j;
			$txt_job_id 		= "txt_job_id_".$j;
			$txt_lot 			= "txt_lot_".$j;
			$txt_pro_id 		= "txt_pro_id_".$j;
			$cbo_count 			= "cbo_count_".$j;
			$txt_item_des 		= "txt_item_des_".$j;
			$cbo_uom 			= "cbo_uom_".$j;
			$txt_wo_qty 		= "txt_wo_qty_".$j;

			$pro_id = $$txt_pro_id;
			if( (str_replace("'","",$cbo_is_sales_order)==1) )
			{
				// IF AUTO ALLOCATION IS SET IN VARIABLE SETTINGS
				if ($variable_set_allocation == 1 && $is_auto_allocation == 1)
				{
					$wo_qty = $$txt_wo_qty;
					$pro_id = $$txt_pro_id;

					$allocationId = return_field_value("id","inv_material_allocation_mst","mst_id=$update_id and entry_form=94 and item_category = 1 and item_id=$pro_id");
					$prod_update = execute_query("update product_details_master set allocated_qnty=(allocated_qnty-$wo_qty),available_qnty=(available_qnty+$wo_qty),update_date='" . $pc_date_time . "' where id=$pro_id", 0);
					$allocation_mst_deleted = sql_update("inv_material_allocation_mst",'status_active*is_deleted','0*1',"id",$allocationId,0);
					$allocation_dtls_deleted = sql_update("inv_material_allocation_dtls",'status_active*is_deleted','0*1',"mst_id",$allocationId,0);

					//for transaction log
					$sql_prod = sql_select("SELECT CURRENT_STOCK, ALLOCATED_QNTY, AVAILABLE_QNTY, DYED_TYPE FROM PRODUCT_DETAILS_MASTER WHERE ID = ".$pro_id);
					foreach($sql_prod as $row)
					{
						$log_entry_form = 94;
						$log_ref_id = str_replace("'","",$update_id);
						$log_ref_number = str_replace("'","",$txt_booking_no);
						$log_prod_id = $pro_id;
						$log_current_stock = $row['CURRENT_STOCK'];
						$log_allocated_qty = $row['ALLOCATED_QNTY'];
						$log_available_qty = $row['AVAILABLE_QNTY'];
						$log_dyed_type = $row['DYED_TYPE'];
					}
				}
				else
				{
					$allocation_mst_deleted = 1;
					$allocation_dtls_deleted = 1;
					$prod_update=1;
				}
			}
			else
			{
				$allocation_mst_deleted = 1;
				$allocation_dtls_deleted = 1;
				$prod_update=1;
			}
		} //Loop End

		/*
		echo "10**".$dtlsrID ."&&". $finProductID ."&&". $allocation_mst_deleted ."&&". $allocation_dtls_deleted."&&".$prod_update;
		oci_rollback($con);
		disconnect($con);
		die();*/

		if($db_type==0 )
		{
			if($dtlsrID && $finProductID && $allocation_mst_deleted && $allocation_dtls_deleted && $prod_update)
			{
				mysql_query("COMMIT");
				echo "2**".$txt_booking_no."**".$update_id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($dtlsrID && $finProductID && $allocation_mst_deleted && $allocation_dtls_deleted && $prod_update)
			{
				//for transaction log
				$log_data = array();
				$log_data['entry_form'] = $log_entry_form;
				$log_data['ref_id'] = $log_ref_id;
				$log_data['ref_number'] = $log_ref_number;
				$log_data['product_id'] = $log_prod_id;
				$log_data['current_stock'] = $log_current_stock;
				$log_data['allocated_qty'] = $log_allocated_qty;
				$log_data['available_qty'] = $log_available_qty;
				$log_data['dyed_type'] = $log_dyed_type;
				$log_data['insert_date'] = $pc_date_time;
				manage_allocation_transaction_log($log_data);
				//end for transaction log

				oci_commit($con);
				echo "2**".$txt_booking_no."**".$update_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="show_dtls_list_view")
{
	$data_arr = explode("_",$data);
	?>
	<table class="rpt_table" width="1200" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
			<tr>
				<th width="40">SL</th>
				<th width="90">Job No/ Sales Order No</th>
				<th width="90">Sale Job/ Booking No</th>
				<th width="60">Count</th>
				<th width="200">Description</th>
				<th width="60">UOM</th>
				<th width="80">WO QTY</th>
				<th width="80">Rate</th>
				<th width="100">Amount</th>
				<th width="60">No of Bag</th>
				<th width="60">No of Cone</th>
				<th width="100">Minimum Require Cone</th>
				<th >Remarks</th>
				<th width="100">Finish Product</th>
			</tr>
		</thead>
		<tbody>
			<?
			if($data_arr[1]==15 || $data_arr[1]==50 || $data_arr[1]==51)
			{
				$composition_array=return_library_array( "select id, composition_name from  lib_composition_array where  status_active=1", "id", "composition_name"  );

				$sql = sql_select("SELECT b.id as wo_dtls_id,a.service_type, b.job_no, b.job_no_id, b.dyeing_charge, b.fab_booking_no, b.product_id , b.COUNT, b.yarn_description, b.color_range, b.no_of_bag, b.no_of_cone, b.min_require_cone, b.remarks, b.yarn_wo_qty, b.amount FROM wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b WHERE a.id = b.mst_id AND b.status_active = 1 AND b.is_deleted = 0 and a.id=".$data_arr[0]);
				
				$sql_fin_prod_rslt=sql_select("select id as fin_prod_pk,dtls_id,job_no,yarn_count,yarn_comp,yarn_perc,yarn_type,yarn_color from wo_yarn_dyeing_dtls_fin_prod c where c.mst_id=".$data_arr[0]);

				$fin_data_arr = array();
				foreach($sql_fin_prod_rslt as $fin_row)
				{
					$fin_dtls_id_arr = explode(",",$fin_row[csf("dtls_id")]);
					$wo_qty = $wo_amount = $rate = 0;
					$booking_no = "";
				
					foreach($sql as $row)
					{
						foreach($fin_dtls_id_arr as $fin_dtils_id)
						{							
							if($fin_dtils_id == $row[csf("wo_dtls_id")])
							{	
								$wo_qty += $row[csf("yarn_wo_qty")];
								$wo_amount += $row[csf("amount")];
								$booking_no = $row[csf("fab_booking_no")];
								$rate = $row[csf("dyeing_charge")];
							}							
						}

						$ysw_data[$fin_row[csf("fin_prod_pk")]]['job_no'] = $fin_row[csf("job_no")];
						$ysw_data[$fin_row[csf("fin_prod_pk")]]['fab_booking_no'] = $booking_no ;
						$ysw_data[$fin_row[csf("fin_prod_pk")]]['dyeing_charge'] = $rate;
						
						$ysw_data[$fin_row[csf("fin_prod_pk")]]['original_pro_data'] .= $row[csf("product_id")].'_'.$row[csf("yarn_wo_qty")].",";
						$ysw_data[$fin_row[csf("fin_prod_pk")]]['count'] .= $row[csf("count")]."*";
						$ysw_data[$fin_row[csf("fin_prod_pk")]]['yarn_description'] .= $row[csf("yarn_description")]."*";
						$ysw_data[$fin_row[csf("fin_prod_pk")]]['color_range'] .= $row[csf("color_range")].",";
						$ysw_data[$fin_row[csf("fin_prod_pk")]]['no_of_bag'] .= $row[csf("no_of_bag")].",";
						$ysw_data[$fin_row[csf("fin_prod_pk")]]['no_of_cone'] .= $row[csf("no_of_cone")].",";
						$ysw_data[$fin_row[csf("fin_prod_pk")]]['min_require_cone'] .= $row[csf("min_require_cone")].",";
						$ysw_data[$fin_row[csf("fin_prod_pk")]]['remarks'] .= $row[csf("remarks")].",";
					}

					$fin_prod = $count_arr[$fin_row[csf("yarn_count")]]." ". $composition_array[$fin_row[csf("yarn_comp")]]." " . $fin_row[csf("yarn_perc")]."% " . $yarn_type[$fin_row[csf("yarn_type")]]." " . $color_arr[$fin_row[csf("yarn_color")]];

					$ysw_data[$fin_row[csf("fin_prod_pk")]]['yarn_wo_qty'] = $wo_qty;
					$ysw_data[$fin_row[csf("fin_prod_pk")]]['amount'] = $wo_amount;
					$ysw_data[$fin_row[csf("fin_prod_pk")]]['fin_prod_pk'] = $fin_row[csf("fin_prod_pk")];
					$ysw_data[$fin_row[csf("fin_prod_pk")]]['dtls_id'] = $fin_row[csf("dtls_id")];
					$ysw_data[$fin_row[csf("fin_prod_pk")]]['yarn_count'] = $fin_prod;
				}

				//echo "<pre>";
				//print_r($ysw_data);
				//die();

				$i=1;
				foreach($ysw_data as $fin_prod_pk=>$fin_row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$counts=array_unique(explode("*",chop($fin_row["count"],"*")));
					$count_names=$desc="";
					foreach ($counts as $count) {
						$count_names.=$count_arr[$count].",";
					}
					$yarn_description = array_unique(explode("*",chop($fin_row["yarn_description"],"*")));
					foreach ($yarn_description as $description) {
						$desc.=$description."<br />";
					}
					$no_of_bag = chop($fin_row["no_of_bag"],",");
					$no_of_cone = chop($fin_row["no_of_cone"],",");
					$min_require_cone = chop($fin_row["min_require_cone"],",");
					$remarks = chop($fin_row["remarks"],",");
								
					$is_twisting = ($data_arr[1]==15 || $data_arr[1]==50 || $data_arr[1]==51)? "create_row('".$fin_row["dtls_id"]."');":"";
					?>

					<tr bgcolor="<? echo $bgcolor;?>" onClick="<? echo $is_twisting; ?>get_php_form_data('<? echo $fin_row["dtls_id"]."**".chop($fin_row["original_pro_data"],",")."**".$fin_row["fin_prod_pk"]; ?>', 'child_form_input_data', 'requires/yarn_service_work_order_controller')" style="cursor:pointer;">
						<td align="center"><p><? echo $i; ?></p></td>
						<td align="center"><p><? echo $fin_row["job_no"]; ?></p></td>
						<td align="center"><p><? echo $fin_row["fab_booking_no"]; ?></p></td>
						<td><p><? echo rtrim($count_names,", "); ?></p></td>
						<td><p><? echo $desc; ?></p></td>
						<td align="center"><p><? echo $unit_of_measurement[12]; ?></p></td>
						<td align="right"><p><? echo number_format($fin_row["yarn_wo_qty"],2); ?></p></td>
						<td align="right"><p><? echo number_format($fin_row["dyeing_charge"],2); ?></p></td>
						<td align="right"><p><? echo number_format($fin_row["amount"],2); ?></p></td>
						<td align="center"><p><? echo $no_of_bag; ?></p></td>
						<td align="center"><p><? echo $no_of_cone; ?></p></td>
						<td align="center"><p><? echo $min_require_cone; ?></p></td>
						<td><p><? echo $remarks; ?></p></td>
						<td align="center"><p><? echo $fin_prod; ?></p></td>
					</tr>
					<?
					$i++;
				} 
			}
			else
			{
				$sql = sql_select("select id,job_no,job_no_id,count,yarn_description,yarn_color,color_range,uom,product_id,yarn_wo_qty,dyeing_charge,amount,no_of_bag, no_of_cone,min_require_cone,referance_no,remarks, fab_booking_no from wo_yarn_dyeing_dtls where status_active=1 and is_deleted=0 and mst_id='".$data_arr[0]."'");
				$i=1;
				foreach($sql as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$count_names=$count_arr[$row[csf("count")]];
					$desc = $row[csf("yarn_description")];			
					$is_twisting = "";
					$row[csf("original_pro_data")] = $row[csf("product_id")].'_'.$row[csf("yarn_wo_qty")];
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="<? echo $is_twisting; ?>get_php_form_data('<? echo $row[csf("id")]."**".$row[csf("original_pro_data")]; ?>', 'child_form_input_data', 'requires/yarn_service_work_order_controller')" style="cursor:pointer;">
						<td align="center"><p><? echo $i; ?></p></td>
						<td align="center"><p><? echo $row[csf("job_no")]; ?></p></td>
						<td align="center"><p><? echo $row[csf("fab_booking_no")]; ?></p></td>
						<td><p><? echo rtrim($count_names,", "); ?></p></td>
						<td><p><? echo $desc; ?></p></td>
						<td align="center"><p><? echo $unit_of_measurement[12]; ?></p></td>
						<td align="right"><p><? echo number_format($row[csf("yarn_wo_qty")],2); ?></p></td>
						<td align="right"><p><? echo number_format($row[csf("dyeing_charge")],2); ?></p></td>
						<td align="right"><p><? echo number_format($row[csf("amount")],2); ?></p></td>
						<td align="center"><p><? echo $row[csf("no_of_bag")]; ?></p></td>
						<td align="center"><p><? echo $row[csf("no_of_cone")]; ?></p></td>
						<td align="center"><p><? echo $row[csf("min_require_cone")]; ?></p></td>
						<td><p><? echo $row[csf("remarks")]; ?></p></td>
						<td align="center"><p>&nbsp;</p></td>
					</tr>
					<?
					$i++;
				}
			}	
			?>
		</tbody>
	</table>
	<?
	exit();
}

if($action=="populate_master_from_data")
{
	$data=explode("_", $data);
	$sql="select  a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id,a.service_type, a.supplier_id, a.booking_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,a.tenor,a.is_sales, a.ready_to_approved,booking_without_order,ref_no,a.is_approved from wo_yarn_dyeing_mst a where a.ydw_no='".$data[1]."' and a.status_active=1 and a.id=$data[0]";
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "load_drop_down( 'requires/yarn_service_work_order_controller', '".$row[csf("pay_mode")]."', 'load_drop_down_supplier', 'supplier_td');\n";
		echo "$('#txt_booking_no').val('".$row[csf("ydw_no")]."');\n";
		echo "$('#cbo_company_name').val('".$row[csf("company_id")]."');\n";
		echo "$('#cbo_service_type').val('".$row[csf("service_type")]."');\n";
		echo "$('#cbo_supplier_name').val('".$row[csf("supplier_id")]."');\n";
		echo "$('#txt_booking_date').val('".change_date_format($row[csf("booking_date")])."');\n";
		echo "$('#txt_delivery_date').val('".change_date_format($row[csf("delivery_date")])."');\n";
		echo "$('#cbo_currency').val('".$row[csf("currency")]."');\n";
		echo "$('#txt_exchange_rate').val('".$row[csf("ecchange_rate")]."');\n";
		echo "$('#cbo_pay_mode').val('".$row[csf("pay_mode")]."');\n";
		echo "$('#txt_attention').val('".$row[csf("attention")]."');\n";
		echo "$('#txt_tenor').val('".$row[csf("tenor")]."');\n";
		echo "$('#cbo_source').val('".$row[csf("source")]."');\n";
		echo "$('#update_id').val(".$row[csf("id")].");\n";
		echo "$('#cbo_is_sales_order').val('".$row[csf("is_sales")]."');\n";
		echo "$('#cbo_ready_to_approved').val('".$row[csf("ready_to_approved")]."');\n";
		echo "$('#txt_ref_no').val('".$row[csf("ref_no")]."');\n";
		echo "$('#cbo_is_sales_order').attr('disabled','disabled');\n";
		echo "$('#cbo_with_order').attr('disabled','disabled');\n";
		echo "$('#cbo_with_order').val('".$row[csf("booking_without_order")]."');\n";
		if($row[csf("booking_without_order")]==1){
			echo "$('#txt_job_no_0').removeAttr('disabled','disabled');\n";
			echo "$('#txt_job_no_0').attr('placeholder', 'Doubole Click for Job');\n";
		}

		if($row[csf("is_approved")]==1) echo "$('#is_approved').text('Approved');\n";
        else if($row[csf("is_approved")]==3)  echo "$('#is_approved').text('Partial Approved');\n";
        else  echo "$('#is_approved').text('');\n";

		echo "set_fin_visibility('".$row[csf("service_type")]."');\n";
		echo "reset_form('yarn_service_work_order','','','','','txt_booking_no*cbo_company_name*cbo_service_type*cbo_pay_mode*txt_booking_date*txt_attention*txt_tenor*cbo_currency*txt_exchange_rate*cbo_supplier_name*cbo_source*txt_delivery_date*cbo_is_sales_order*cbo_ready_to_approved*cbo_with_order*txt_ref_no*update_id*cbo_uom_0');\n";
	}
	exit();
}

if ($action=="yern_service_wo_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	if($db_type==0) $select_field_grp="group by a.id order by supplier_name";
	else if($db_type==2) $select_field_grp="group by a.id,a.supplier_name order by supplier_name";
	?>
	<script>
		function js_set_value(id)
		{
			$("#hidden_sys_number").val(id);
			parent.emailwindow.hide();
		}
		var permission= '<? echo $permission; ?>';
	</script>
	</head>
	<body>
		<div align="center" style="width:830px;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="800" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
					<thead>
						<tr>
							<th colspan="10">
								<?
								echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
								?>
							</th>
						</tr>
						<tr>
							<th width="130"> Service Type</th>
							<th width="130">Supplier Name</th>
							<th width="80">WO No</th>
							<? if($is_sales==2){ ?>
							<th width="80">Ref No</th>
							<th width="80">File No</th>
							<? }else { ?>
							<th width="100">Job No</th>
							<th width="100">Booking No</th>
							<th width="100">Sales Order No</th>
							<th width="100">IR/IB</th>
							<? } ?>
							<th width="150" colspan="2">Booking  Date</th>
							<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('searchorderfrm_1','search_div','','','','');"  /></th>
						</tr>
					</thead>
					<tbody>
						<tr class="general">
							<td>
								<? echo create_drop_down( "cbo_service_type", 120, $yarn_issue_purpose,"", 1, "-- Select --", $selected, "",0,'12,15,38,46,50,51,81,82');?>
							</td>
							<td>
								<?

								echo create_drop_down( "cbo_supplier_name", 120, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and c.tag_company=$company and a.status_active =1 and b.party_type in(21) group by a.id,a.supplier_name order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );
								?>
							</td>
							<td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
							<? if($is_sales==2){ ?>
							<td><input name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:100px"></td>
							<td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:100px"></td>
							<input type="hidden" id="txt_job_no">
							<input type="hidden" id="txt_booking_no">
							<input type="hidden" id="txt_sales_order_no">
							<input type="hidden" id="txt_ir_no">
							<? } else{ ?>
							<td><input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px"></td>
							<td><input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:100px"></td>
							<td><input name="txt_sales_order_no" id="txt_sales_order_no" class="text_boxes" style="width:100px"></td>
							<td><input name="txt_ir_no" id="txt_ir_no" class="text_boxes" style="width:100px"></td>
								<input type="hidden" id="txt_ref_no">
								<input type="hidden" id="txt_file_no">
							<? } ?>
							<td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" /></td>
							<td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" /></td>
							<td>
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_service_type').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('txt_file_no').value+'_'+<? echo $is_sales; ?>+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('txt_sales_order_no').value+'_'+document.getElementById('txt_ir_no').value, 'create_sys_search_list_view', 'search_div', 'yarn_service_work_order_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td align="center" valign="middle" colspan="8">
								<? echo load_month_buttons(1);  ?>
								<input type="hidden" id="hidden_sys_number" value="hidden_sys_number" />
								<input type="hidden" id="hidden_id" value="hidden_id" />
								<!--END-->
							</td>
						</tr>
					</tbody>
				</table>
				<div id="search_div"></div>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_sys_search_list_view")
{
	$ex_data = explode("_",$data);
	$supplier = $ex_data[0];
	$fromDate = $ex_data[1];
	$toDate = $ex_data[2];
	$company = $ex_data[3];
	$service_type=$ex_data[4];
	$is_sales=$ex_data[10];

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

	if( $supplier!=0 )  $supplier="and a.supplier_id='$supplier'"; else  $supplier="";
	if($db_type==0)
	{
		$booking_year_cond=" and year(a.insert_date)=$ex_data[5]";
		if( $fromDate!=0 && $toDate!=0 ) $sql_cond= "and a.booking_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
	}
	if($db_type==2)
	{
		$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$ex_data[5]";
		if( $fromDate!=0 && $toDate!=0 ) $sql_cond= "and a.booking_date  between '".change_date_format($fromDate,'mm-dd-yyyy','/',1)."' and '".change_date_format($toDate,'mm-dd-yyyy','/',1)."'";
	}
	if( $company!=0 )  $company=" and a.company_id='$company'"; else  $company="";
	if( $service_type!=0 )  $service_type_cond="and a.service_type='$service_type'"; else  $service_type_cond="";


	if($ex_data[7]==4 || $ex_data[7]==0)
	{
		if (str_replace("'","",$ex_data[6])!="") $booking_cond=" and a.yarn_dyeing_prefix_num like '%$ex_data[6]%'  $booking_year_cond  "; else $booking_cond="";
	}
	if($ex_data[7]==1)
	{
		if (str_replace("'","",$ex_data[6])!="") $booking_cond=" and a.yarn_dyeing_prefix_num ='$ex_data[6]' "; else $booking_cond="";
	}
	if($ex_data[7]==2)
	{
		if (str_replace("'","",$ex_data[6])!="") $booking_cond=" and a.yarn_dyeing_prefix_num like '$ex_data[6]%'  $booking_year_cond  "; else $booking_cond="";
	}
	if($ex_data[7]==3)
	{
		if (str_replace("'","",$ex_data[6])!="") $booking_cond=" and a.yarn_dyeing_prefix_num like '%$ex_data[6]'  $booking_year_cond  "; else $booking_cond="";
	}
	if($is_sales==2)
	{
		if(str_replace("'","",$ex_data[8])!='')
		{
			if (str_replace("'","",$ex_data[8])!="") $ref_cond=" and c.grouping like '%$ex_data[8]'"; else $ref_cond="";
		}
		if(str_replace("'","",$ex_data[9])!='')
		{
			if (str_replace("'","",$ex_data[9])!="") $file_cond=" and c.file_no like '%$ex_data[9]'"; else $file_cond="";
		}
	}
	else
	{
		if (str_replace("'","",$ex_data[11])!="") $job_cond=" and a.job_no like '%$ex_data[11]%' "; else $job_cond="";
		if (str_replace("'","",$ex_data[12])!="") $f_booking_cond=" and b.fab_booking_no like '%$ex_data[12]%' "; else $f_booking_cond="";
		if (str_replace("'","",$ex_data[13])!="") $sales_order_cond=" and b.job_no like '%$ex_data[13]%' "; else $sales_order_cond="";
		if (str_replace("'","",$ex_data[14])!="") $ir_cond=" and c.grouping like '%$ex_data[14]%' "; else $ir_cond="";

		if(str_replace("'","",$ex_data[11])!="" || str_replace("'","",$ex_data[12])!="" || str_replace("'","",$ex_data[13])!="" || str_replace("'","",$ex_data[14])!="")
		{
			$sql= "SELECT a.id,  c.grouping,  a.job_no, a.booking_no  from wo_booking_mst a, wo_po_details_master b, wo_po_break_down c where a.job_no=b.job_no and a.job_no=c.job_no_mst and b.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company $job_cond $ir_cond";
			//echo $sql;
			$jobBookingArray=sql_select($sql);
			$all_booking_no_arr = array();
			foreach ($jobBookingArray as $row)
			{
				if($bookingNoChk[$row[csf('booking_no')]] == "")
				{
					$bookingNoChk[$row[csf('booking_no')]] = $row[csf('booking_no')];
					array_push($all_booking_no_arr, $row[csf('booking_no')]);
				}
			}
		}

		//var_dump($booking_no_arr);

		if(!empty($all_booking_no_arr))
		{
			$job_booking_cond = " ".where_con_using_array($all_booking_no_arr,1,'b.fab_booking_no')." ";
		}
	}


	if($db_type==0)
	{
		$sql = "SELECT a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id,a.service_type, a.supplier_id, a.booking_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,year(a.insert_date) as year from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b left join sample_development_mst d on  b.job_no_id=d.id where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.entry_form=94 and b.entry_form=94 $company $supplier  $sql_cond  $service_type_cond  $booking_cond group by a.id order by a.id DESC";
	}

	else if($db_type==2)
	{
		if($is_sales==2)
		{
			$sql = "SELECT a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id,a.service_type, a.supplier_id, a.booking_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,TO_CHAR(a.insert_date,'YYYY') as year from wo_yarn_dyeing_mst a join wo_yarn_dyeing_dtls b on a.id=b.mst_id left join wo_po_break_down c on b.job_no_id=c.job_id left join sample_development_mst d on  b.job_no_id=d.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.entry_form=94 and b.entry_form=94 $company $supplier $sql_cond $service_type_cond $booking_cond $ref_cond $file_cond  group by a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id,a.service_type, a.supplier_id, a.booking_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,a.insert_date order by a.id DESC";
		}
		else{
			$sql = "SELECT a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id,a.service_type, a.supplier_id, a.booking_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,TO_CHAR(a.insert_date,'YYYY') as year,a.is_sales,b.job_no, b.fab_booking_no from wo_yarn_dyeing_mst a join wo_yarn_dyeing_dtls b on a.id=b.mst_id left join sample_development_mst d on  b.job_no_id=d.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.entry_form=94 and b.entry_form=94 $company $supplier $sql_cond $service_type_cond $booking_cond  $sales_order_cond $f_booking_cond $job_booking_cond group by a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id,a.service_type, a.supplier_id, a.booking_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,a.insert_date,a.is_sales,b.job_no, b.fab_booking_no order by a.id DESC";
		}

	}
	//echo $sql;

	$nameArray=sql_select($sql);
	$all_booking_arr = array();
	foreach ($nameArray as $row)
	{
		if($allbookingNoChk[$row[csf('fab_booking_no')]] == "")
		{
			$allbookingNoChk[$row[csf('fab_booking_no')]] = $row[csf('fab_booking_no')];
			array_push($all_booking_arr, $row[csf('fab_booking_no')]);
		}
	}

	$sql_job = "SELECT a.id,  c.grouping,  a.job_no, a.booking_no  from wo_booking_mst a, wo_po_details_master b, wo_po_break_down c where a.job_no=b.job_no and a.job_no=c.job_no_mst and b.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company ".where_con_using_array($all_booking_arr,1,'a.booking_no')." ";
	//echo $sql_job;

	$sql_job_rslt=sql_select($sql_job);
	$job_info_arr = array();
	foreach ($sql_job_rslt as $val)
	{
		$job_info_arr[$val[csf("booking_no")]]["job_no"] = $val[csf("job_no")];
		$job_info_arr[$val[csf("booking_no")]]["grouping"] .= $val[csf("grouping")].',';
	}
	//var_dump($job_info_arr);


	if($is_sales==1)
	{
		$tbl_width = 1220;
	}
	else{
		$tbl_width = 820;
	}

	?>	<div style="width:<? echo $tbl_width + 20; ?>px;"  align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width;?>" class="rpt_table" >
			<thead>
				<th width="30">SL</th>
				<th width="50">Wo No</th>
				<? if($is_sales==1){ ?>
				<th width="100">Job No</th>
				<th width="100">Booking No</th>
				<th width="100">Sales Order No </th>
				<th width="100">IR/IB</th>
				<? } ?>
				<th width="40">Year</th>
				<th width="100">Service Type</th>
				<th width="100">Currency</th>
				<th width="50">Exchange Rate</th>
				<th width="100">Pay Mode</th>
				<th width="170">Supplier Name</th>
				<th width="70">Booking Date</th>
				<th>Delevary Date</th>
			</thead>
		</table>
		<div style="width:<? echo $tbl_width + 20; ?>px; margin-left:3px; overflow-y:scroll; max-height:300px;" id="buyer_list_view">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width;?>" class="rpt_table" id="tbl_list_search" >
				<?

				$i=1;

				foreach ($nameArray as $selectResult)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>+'_'+'<? echo $selectResult[csf('ydw_no')]; ?>'+'_'+<? echo $selectResult[csf('service_type')]; ?>); ">

						<td width="30" align="center"> <p><? echo $i; ?></p></td>
						<td width="50" align="center"><p> <? echo $selectResult[csf('yarn_dyeing_prefix_num')]; ?></p></td>
						<? if($is_sales==1){ ?>
							<td width="100" align="center"><p> <? echo $job_info_arr[$selectResult[csf('fab_booking_no')]]["job_no"]; ?></p></td>
							<td width="100" align="center"><p> <? echo $selectResult[csf('fab_booking_no')]; ?></p></td>
							<td width="100" align="center"><p>
								<? if($selectResult[csf('is_sales')]==1) echo $selectResult[csf('job_no')]; ?>
							</p></td>
							<td width="100" align="center"><p>
								<?
 								$job_data = $job_info_arr[$selectResult[csf('fab_booking_no')]]["grouping"];
								 echo implode(",", array_unique(explode(",",chop($job_data ,","))));
								?>
							</p></td>
						<? } ?>
						<td width="40" align="center"><p> <? echo $selectResult[csf('year')]; ?></p></td>
						<td width="100"><p><? echo $yarn_issue_purpose[$selectResult[csf('service_type')]]; ?></p></td>
						<td width="100"><? echo $currency[$selectResult[csf('currency')]]; ?></td>
						<td width="50"><? echo $selectResult[csf('ecchange_rate')]; ?></td>
						<td width="100"><p> <? echo $pay_mode[$selectResult[csf('pay_mode')]]; ?></p></td>
						<td width="170"> <p><? if($selectResult[csf('pay_mode')]==3 || $selectResult[csf('pay_mode')]==5){echo $company_library[$selectResult[csf('supplier_id')]];}else{echo $supplier_arr[$selectResult[csf('supplier_id')]];} ?></p></td>
						<td width="70"><p><? echo change_date_format($selectResult[csf('booking_date')]); ?></p></td>
						<td><p><? echo change_date_format($selectResult[csf('delivery_date')]); ?></p></td>
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

if($action=="child_form_input_row")
{
	$data = implode(",",array_unique(explode("_",$data)));
	$sql = "select a.id,a.job_no,a.product_id,b.lot,job_no_id,a.count,a.yarn_type,a.yarn_description,a.color_range,a.uom,a.yarn_wo_qty,a.dyeing_charge,a.amount,a.no_of_bag, a.no_of_cone,a.min_require_cone,a.remarks,a.referance_no,a.sample_name, a.fab_booking_no from wo_yarn_dyeing_dtls a,product_details_master b where a.product_id=b.id and a.id in($data)";
	//echo $sql;die;
	$sql_re=sql_select($sql);
	$i=0;$data="";
	foreach($sql_re as $row)
	{
		$data .= $row[csf("yarn_description")]." ". $yarn_type[$row[csf("yarn_type")]]." ". $row[csf("count")]."*". $row[csf("lot")]."*".$row[csf("id")]."*".$row[csf("fab_booking_no")]."#";
	}
	echo rtrim($data,"# ");

	exit();
}

if($action=="child_form_input_data")
{
	$dtls_data = explode("**",$data);
	$dtlsidarr = explode("_",$dtls_data[0]);
	//print_r($dtlsidarr);

	//echo $dtls_id; die();
	$original_pro_data = implode(",",array_unique(explode(",",$dtls_data[1])));
	$dtls_id = implode(",",array_unique(explode(",",$dtlsidarr[0])));

	$sql = "select b.is_sales, b.booking_without_order, a.id,a.mst_id, a.job_no, a.product_id, a.job_no_id, a.count, a.yarn_description, a.color_range, a.uom, a.yarn_wo_qty, a.dyeing_charge, a.amount, a.no_of_bag, a.no_of_cone, a.min_require_cone, a.remarks, a.referance_no, a.sample_name, c.allocated_qnty, c.available_qnty, a.fab_booking_no, b.company_id ,b.pay_mode from wo_yarn_dyeing_dtls a, wo_yarn_dyeing_mst b, product_details_master c where a.mst_id=b.id and a.product_id=c.id and a.id in($dtls_id)";
	//echo $sql;

	$sql_re=sql_select($sql);
	$company_id = 0;
	foreach($sql_re as $row)
	{
		$company_id = $row[csf('company_id')];
	}

	//for variable settings
	$arr = array();
	$arr['company_id'] = $company_id;
	$arr['category_id'] = 1;
	$arr['vs_auto_allocation'] = 1;
	$data_vs = get_vs_allocated_qty($arr);
	//end
	//echo "<pre>";
	//print_r($data_vs);
	//echo "</pre>";

	$i=0;
	foreach($sql_re as $row)
	{
		$product_id = $row[csf('product_id')];
		$work_order_id = $row[csf('mst_id')];

		if($row[csf('is_sales')]==1)
		{
			if($data_vs['is_allocated'] == 1 && $data_vs['is_sales_order'] == 1 && $data_vs['is_auto_allocation'] == 2 )
			{
				$arr = array();
				$arr['product_id'] = $row[csf('product_id')];
				$arr['is_auto_allocation'] = 0;
				$arr['job_no'] = $row[csf('job_no')];
				$arr['booking_no'] = $row[csf('fab_booking_no')];
				$arr['po_id'] = 0;
				$allocation_arr = get_allocation_balance($arr);
				$booking_requisition_qty = $allocation_arr['booking_requisition'][$row[csf('fab_booking_no')]][$row[csf('product_id')]]['qty']*1;
				$yarn_dyeing_service_qty = $allocation_arr['yarn_dyeing_service'][$row[csf('job_no')]][$row[csf('product_id')]]['qty']*1;
				$booking_allocation_qty = $allocation_arr['booking_allocation'][$row[csf('fab_booking_no')]][$row[csf('product_id')]]*1;
				$available_qty = $booking_allocation_qty - ($booking_requisition_qty + $yarn_dyeing_service_qty);
				$available_qnty = ($available_qty+$row[csf("yarn_wo_qty")]);
			}
			else
			{
				$available_qnty = ($row[csf('available_qnty')]+$row[csf("yarn_wo_qty")]);
			}
		}
		else
		{
			if($row[csf('booking_without_order')]==1)
			{
				$available_qnty = $row[csf('allocated_qnty')];
			}
			else
			{
				$available_qnty = $row[csf('available_qnty')];
			}
		}

		echo "$('#txt_wo_qty_$i').attr('placeholder', '".$available_qnty."');\n";
		echo "$('#txt_allocated_qty_$i').val('".$available_qnty."');\n";

		if($row[csf('booking_without_order')]==1)
		{
			echo "$('#txt_job_id_$i').val('".$row[csf("job_no_id")]."');\n";
			echo "$('#txt_job_no_$i').val('".$row[csf("job_no")]."');\n";
		}
		else
		{
			echo "$('#txt_job_id_$i').val('".$row[csf("job_no_id")]."');\n";
			echo "$('#txt_job_no_$i').val('".$row[csf("job_no")]."');\n";
		}

		echo "load_drop_down( 'requires/yarn_service_work_order_controller', '".$row[csf("pay_mode")]."', 'load_drop_down_supplier_new', 'supplier_td');\n";

		echo "$('#txt_pro_id_$i').val(".$row[csf("product_id")].");\n";
		$lot=return_field_value("lot"," product_details_master","id=".$row[csf("product_id")]."","lot");
		if($row[csf("product_id")]>0)
		{
			echo "$('#txt_lot_$i').val('$lot');\n";
			echo "$('#cbo_count_$i').val(".$row[csf("count")].").attr('disabled',true);\n";
			echo "$('#txt_item_des_$i').val('".$row[csf("yarn_description")]."').attr('disabled',true);\n";
		}
		else
		{
			echo "$('#txt_lot_$i').val('$lot');\n";
			echo "$('#cbo_count_$i').val(".$row[csf("count")].");\n";
			echo "$('#txt_item_des_$i').val('".$row[csf("yarn_description")]."');\n";
		}
		$job_ref=$row[csf("job_no")];
		//for booking no
		echo "$('#txt_fab_booking_no_$i').val('".$row[csf("fab_booking_no")]."');\n";
		echo "$('#cbo_uom_$i').val(".$row[csf("uom")].");\n";
		echo "$('#txt_wo_qty_$i').val(".$row[csf("yarn_wo_qty")].");\n";
		echo "$('#txt_rate_$i').val(".$row[csf("dyeing_charge")].");\n";
		echo "$('#txt_amount_$i').val(".$row[csf("amount")].");\n";
		echo "$('#txt_bag_$i').val(".$row[csf("no_of_bag")].");\n";
		echo "$('#txt_cone_$i').val(".$row[csf("no_of_cone")].");\n";
		echo "$('#txt_min_req_cone_$i').val(".$row[csf("min_require_cone")].");\n";
		echo "$('#txt_remarks_$i').val('".$row[csf("remarks")]."');\n";
		echo "$('#txt_ref_no_$i').val('".$row[csf("referance_no")]."');\n";
		//update id here
		echo "$('#dtls_update_id_$i').val(".$row[csf("id")].");\n";
		echo "set_button_status(1, permission, 'fnc_yarn_service_wo',1,0);\n";
		$i++;
	}
	echo "$('#update_dtls_ids').val('".$dtls_data[0]."');\n";
	echo "$('#original_pro_data').val('".$original_pro_data."');\n";

	$sql_fin_prod=sql_select("select id,yarn_count,yarn_comp,yarn_perc,yarn_type,yarn_color from wo_yarn_dyeing_dtls_fin_prod where id=".$dtls_data[2]);

	if(!empty($sql_fin_prod))
	{
		echo "$('#is_twisting').css('display','block');\n";
		echo "$('#cbo_fin_count').val('".$sql_fin_prod[0][csf("yarn_count")]."');\n";
		echo "$('#cbo_fin_composition').val('".$sql_fin_prod[0][csf("yarn_comp")]."');\n";
		echo "$('#txt_fin_perc').val('".$sql_fin_prod[0][csf("yarn_perc")]."');\n";
		echo "$('#cbo_fin_type').val('".$sql_fin_prod[0][csf("yarn_type")]."');\n";
		echo "$('#txt_fin_color').val('".$color_arr[$sql_fin_prod[0][csf("yarn_color")]]."');\n";
		echo "$('#hdn_fin_update_id').val('".$sql_fin_prod[0][csf("id")]."');\n";
	}
	echo "$('#operation_type').val(1);\n";
	exit();
}

if($action=="terms_condition_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		var permission='<? echo $permission; ?>';
		function add_break_down_tr(i)
		{
			var row_num=$('#tbl_termcondi_details tr').length-1;
			if (row_num!=i)
			{
				return false;
			}
			else
			{
				i++;

				$("#tbl_termcondi_details tr:last").clone().find("input,select").each(function() {
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						'name': function(_, name) { return name + i },
						'value': function(_, value) { return value }
					});
				}).end().appendTo("#tbl_termcondi_details");
				$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
				$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
				$('#termscondition_'+i).val("");
				$( "#tbl_termcondi_details tr:last" ).find( "td:first" ).html(i);
			}

		}

		function fn_deletebreak_down_tr(rowNo)
		{

			var numRow = $('table#tbl_termcondi_details tbody tr').length;
			if(numRow==rowNo && rowNo!=1)
			{
				$('#tbl_termcondi_details tbody tr:last').remove();
			}

		}

		function fnc_fabric_booking_terms_condition( operation )
		{
			var row_num=$('#tbl_termcondi_details tr').length-1;
			var data_all="";
			for (var i=1; i<=row_num; i++)
			{

				if (form_validation('termscondition_'+i,'Term Condition')==false)
				{
					return;
				}

				data_all=data_all+get_submitted_data_string('txt_booking_no*termscondition_'+i,"../../../");
			//alert(data_all);
		}
		var data="action=save_update_delete_fabric_booking_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
	//	alert(data);
		//freeze_window(operation);
		http.open("POST","yarn_service_work_order_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_terms_condition_reponse;
	}
	function fnc_fabric_booking_terms_condition_reponse()
	{
		if(http.readyState == 4)
		{
	   // alert(http.responseText);
	   var reponse=trim(http.responseText).split('**');
	   if (reponse[0].length>2) reponse[0]=10;
	   if(reponse[0]==0 || reponse[0]==1)
	   {
				//$('#txt_terms_condision_book_con').val(reponse[1]);
				parent.emailwindow.hide();
				set_button_status(1, permission, 'fnc_fabric_booking_terms_condition',1,1);
			}
		}
	}
	</script>

	</head>

	<body>
		<div align="center" style="width:100%;" >
			<? echo load_freeze_divs ("../../../",$permission);  ?>
			<fieldset>
				<form id="termscondi_1" autocomplete="off">
					<input type="text" id="txt_booking_no" name="txt_booking_no" value="<? echo str_replace("'","",$txt_booking_no) ?>" class="text_boxes" readonly />
					<input type="hidden" id="txt_terms_condision_book_con" name="txt_terms_condision_book_con" >

					<table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
						<thead>
							<tr>
								<th width="50">Sl</th><th width="530">Terms</th><th ></th>
							</tr>
						</thead>
						<tbody>
							<?
						//echo "select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no";
						$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
						if ( count($data_array)>0)
						{
							$i=0;
							foreach( $data_array as $row )
							{
								$i++;
								?>
								<tr id="settr_1" align="center">
									<td>
										<? echo $i;?>
									</td>
									<td>
										<input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>" />
									</td>
									<td>
										<input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
										<input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
									</td>
								</tr>
								<?
							}
						}
						else
						{

						$data_array=sql_select("select id, terms from  lib_yarn_dyeing_terms_con where is_default=1");// quotation_id='$data'
						foreach( $data_array as $row )
						{
							$i++;
							?>
							<tr id="settr_1" align="center">
								<td>
									<? echo $i;?>
								</td>
								<td>
									<input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>" />
								</td>
								<td>
									<input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
									<input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> );" />
								</td>
							</tr>
							<?
						}
					}
					?>
				</tbody>
			</table>

			<table width="650" cellspacing="0" class="" border="0">
				<tr>
					<td align="center" height="15" width="100%"> </td>
				</tr>
				<tr>
					<td align="center" width="100%" class="button_container">
						<?
						echo load_submit_buttons( $permission, "fnc_fabric_booking_terms_condition", 0,0 ,"reset_form('termscondi_1','','','','')",1) ;
						?>
					</td>
				</tr>
			</table>
		</form>
	</fieldset>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?

	exit();
}

if($action=="save_update_delete_fabric_booking_terms_condition")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		$con = connect();

		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$id=return_next_id( "id", "wo_booking_terms_condition", 1 ) ;
		$field_array="id,booking_no,terms";
		for ($i=1;$i<=$total_row;$i++)
		{
			$termscondition="termscondition_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_booking_no.",".$$termscondition.")";
			$id=$id+1;
		}
		$rID_de3=execute_query( "delete from wo_booking_terms_condition where  booking_no =".$txt_booking_no."",0);

		$rID=sql_insert("wo_booking_terms_condition",$field_array,$data_array,1);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**".$txt_booking_no;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$txt_booking_no;
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_de3)
			{
				oci_commit($con);
				echo "0**".$txt_booking_no;
			}

		}
		else{
			oci_rollback($con);
			echo "10**".$txt_booking_no;
		}
		disconnect($con);
		die;
	}
}

if($action=="check_conversion_rate")
{
	$data=explode("**",$data);
	if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$currency_rate=set_conversion_rate( $data[0], $conversion_date );
	echo "1"."_".$currency_rate;
	exit();
}

if($action=="show_trim_booking_report")
{
	//echo "uuuu";die;
	extract($_REQUEST);
	$cbo_service_type=str_replace("'","",$cbo_service_type);
	$form_name=str_replace("'","",$form_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$update_id=str_replace("'","",$update_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$pay_mode = str_replace("'","",$cbo_pay_mode);
	$new_supplier_name = str_replace("'","",$cbo_supplier_name);

	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$supplier_name=return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");

	$sql="select id from electronic_approval_setup where company_id=$cbo_company_name and page_id in(2843) and is_deleted=0";
	$res_result_arr = sql_select($sql);
	$approval_arr=array();
	foreach($res_result_arr as $row){
		$approval_arr[$row["ID"]]["ID"]=$row["ID"];
	}

	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<style type="text/css">
		table.mainTable tr td {
			border: 1px solid #000;
		}

	</style>
	<div style="width:950px" align="center">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
			<tr>
				<td width="700">
					<table width="100%" cellpadding="0" cellspacing="0" >
						<tr>
							<td align="center" style="font-size:20px;">
								<?php
								echo "<b>$company_library[$cbo_company_name]</b>";
								?>
							</td>
							<td>
								<div style="float:right;width:24px; margin-right:80px; text-align:right">
				 					<div style="height:13px; width:15px;" id="qrcode"></div> 
           						</div>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:14px">
								<?
								$nameArray=sql_select( "select plot_no,bin_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
								foreach ($nameArray as $result)
								{
									?>

									Email Address: <? echo $result[csf('email')];?>
									Website No: <? echo $result[csf('website')];
									if($result[csf('bin_no')]!='') echo "<br> BIN:".$result[csf('bin_no')];
								}

								?>
								
							</td>
							
						</tr>
						<tr>
							<td align="center" style="font-size:20px">
								<strong style="font-size: 16px"><? echo "Yarn ". $yarn_issue_purpose[$cbo_service_type];?> Work Order </strong>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<?
		//echo "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention  from wo_yarn_dyeing_mst a where a.id=$update_id";die;

		$nameArray=sql_select( "SELECT a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency,a.ref_no, a.is_sales, b.job_no, b.fab_booking_no,a.is_approved from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.id=$update_id");
		foreach ($nameArray as $result)
		{
			$work_order=$result[csf('ydw_no')];
			$is_approved=$result[csf('is_approved')];
			$supplier_id=$result[csf('supplier_id')];
			$booking_date=$result[csf('booking_date')];
			$currency_val=$result[csf('currency')];
			$attention=$result[csf('attention')];
			$delivery_date=$result[csf('delivery_date')];
			$delivery_date_end=$result[csf('delivery_date_end')];
			$dy_delivery_start=$result[csf('dy_delivery_date_start')];
			$dy_delivery_end=$result[csf('dy_delivery_date_end')];
			$currency_id=$result[csf('currency')];
			$job_no=$result[csf('job_no')];
			$ref_no=$result[csf('ref_no')];
			$is_sales=$result[csf('is_sales')];
			$fab_booking_no=$result[csf('fab_booking_no')];
		}
		$internal_ref_arr=array();
		/*if ($job_no!="") { $internal_ref_arr=return_library_array( "select job_no_mst,grouping,file_no from  wo_po_break_down where job_no_mst='$job_no'",'job_no_mst','grouping');}*/
		if($is_sales==2 && $job_no!='')
		{
			$po_dtls_data=sql_select("SELECT job_no_mst,grouping,file_no from wo_po_break_down where job_no_mst='$job_no' and status_active=1 and is_deleted=0");
			foreach ($po_dtls_data as $row) {
				$internal_ref_arr[$row[csf('job_no_mst')]]['grouping'][] =$row[csf('grouping')];
				$internal_ref_arr[$row[csf('job_no_mst')]]['file_no'][] =$row[csf('file_no')];
			}
		}

		if($is_sales==1 && $fab_booking_no!='')
		{
			$sales_info = sql_select("SELECT a.po_break_down_id, a.booking_no, b.grouping, b.file_no from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no='$fab_booking_no' group by a.po_break_down_id, a.booking_no, b.grouping, b.file_no order by b.file_no ");

			foreach ($sales_info as $row)
			{
				$internal_ref_arr[$row[csf('booking_no')]]['grouping'][] = $row[csf('grouping')];
				$internal_ref_arr[$row[csf('booking_no')]]['file_no'][] = $row[csf('file_no')];
			}
		}

		$varcode_work_order_no=$work_order;
		?>
		<br><br>
		<table width="970" style="" align="center">
			<tr>
				<td width="360"  style="font-size:12px">
					<table width="360" style="" align="left">
						<tr>
							<td   width="120"><b>To</b>   </td>
							<td width="230">:&nbsp;&nbsp;<b>
								<?
								if($pay_mode==3 || $pay_mode==5)
								{
									echo $company_library[$new_supplier_name];
								}
								else
								{
									echo $supplier_name[$new_supplier_name];
								}
								?></b>
								</td>
							</tr>

							<tr>
								<td  ><b>Wo No.</b>   </td>
								<td  >:&nbsp;&nbsp;<b><? echo $work_order;?></b></td>
							</tr>
							<tr>
								<td style="font-size:12px"><b>Internal Ref. No </b></td>
								<td>:&nbsp;&nbsp;
									<?
										if($is_sales==1)
										{
											echo implode(",", array_unique($internal_ref_arr[$fab_booking_no]['grouping']));
										}
										else
										{
											echo implode(",", array_unique($internal_ref_arr[$job_no]['grouping']));
										}
									?>
								</td>
							</tr>
							<tr>
								<td style="font-size:12px"><b>File No</b></td>
								<td>:&nbsp;&nbsp;
									<?
									if($is_sales==1)
									{
										echo implode(",", array_unique($internal_ref_arr[$fab_booking_no]['file_no']));
									}
									else
									{
										echo implode(",", array_unique($internal_ref_arr[$job_no]['file_no']));
									}

									?>
								</td>
							</tr>
							<tr>
								<td style="font-size:12px"><b>Attention</b></td>
								<td >:&nbsp;&nbsp;<? echo $attention; ?></td>
							</tr>
						</table>
					</td>
					<td width="365" style="font-size:12px">
						<table width="365" style="" align="left">
							<tr>
								<td  width="120"><b>Job No.</b>   </td>
								<td width="120" >:&nbsp;&nbsp;<? echo $job_no;?></td>
								<td width="140"><b>Ref. No</b>   </td>
								<td width="120" colspan="2" >:&nbsp;<? echo $ref_no;?></td>
							</tr>

							<tr>
								<td style="font-size:12px"><b>Currency</b></td>
								<td >:&nbsp;&nbsp;<? echo $currency[$currency_val]; ?></td>
							</tr>
							<tr>
								<td style="font-size:12px"><b>Booking Date</b></td>
								<td >:&nbsp;&nbsp;<? if($booking_date!="0000-00-00" || $booking_date!="") echo change_date_format($booking_date); else echo ""; ?></td>
							</tr>
							<tr>
								<td  width="120"><b>Delivery Date</b>   </td>
								<td width="230" >:&nbsp;&nbsp;<? if($delivery_date!="0000-00-00" || $delivery_date!="") echo change_date_format($delivery_date); else echo "";?>    </td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</br>

		<?
	  			/*$multi_job_arr=array();
				$style_no=sql_select("select a.style_ref_no,a.buyer_name,b.po_number from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and  b.status_active=1 and b.is_deleted=0");

				 foreach($style_no as $row_s)
				 {

				$multi_job_arr[$row_s[csf('job_no')]]['buyer']=$row_s[csf('buyer_name')];
				$multi_job_arr[$row_s[csf('job_no')]]['po_no']=$row_s[csf('po_number')];
			}	*/
			$buyer_sample=return_library_array( "select id, buyer_name from  sample_development_mst",'id','buyer_name');
			$sql="select a.id, a.ydw_no,b.job_no_id,b.sample_name,b.id as dtls_id
			from
			wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
			where
			a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id";
					//echo $sql;
			$sql_result=sql_select($sql);$total_samp_deve_id="";$total_buyer="";$total_dtls_id='';$total_sample_name='';
			foreach($sql_result as $row)
			{
				if($total_dtls_id=='') $total_dtls_id=$row[csf("dtls_id")]; else $total_dtls_id=$total_dtls_id.",".$row[csf("dtls_id")];

				if($total_samp_deve_id=="") $total_samp_deve_id=$row[csf("job_no_id")]; else $total_samp_deve_id=$total_samp_deve_id.",".$row[csf("job_no_id")];

				if($total_buyer=="") $total_buyer=$buyer_sample[$row[csf('job_no_id')]]; else $total_buyer=$total_buyer.",".$buyer_sample[$row[csf('job_no_id')]];

				if($total_sample_name=="") $total_sample_name=$row[csf("sample_name")]; else $total_sample_name=$total_sample_name.",".$row[csf("sample_name")];

			}
		//var_dump($total_dtls_id);
		//die;

			?>




			<table class="mainTable" width="970" style="" align="center" border="1"  cellpadding="0" cellspacing="0" rules="all">
				<tr>
					<td width="30" align="center"><strong>Sl</strong></td>
					<td width="60" align="center"><strong>Lot</strong></td>
					<td width="30" align="center"><strong>Yarn Count</strong></td>
					<td width="160" align="center"><strong>Yarn Description</strong></td>
					<td width="60" align="center"><strong>Brand</strong></td>

					<td width="60" align="center"><strong>WO Qty</strong></td>
					<td width="50" align="right"><strong>Rate</strong></td>
					<td width="80" align="right"><strong>Amount</strong></td>
					<td width="80" align="right"><strong>No of Bag</strong></td>
					<td width="80" align="right"><strong>No of Cone</strong></td>
					<td  align="center" width="60" ><strong>Min Req. Cone</strong></td>
					<td  align="left" ><strong>Remarks</strong></td>
				</tr>
				<?

				$sql_brand=sql_select("select id,lot,brand from product_details_master where status_active=1");
				foreach($sql_brand as $row_barand)
				{
					$product_lot[$row_barand[csf("id")]]['lot']=$row_barand[csf("lot")];
					$product_lot[$row_barand[csf("id")]]['brand']=$row_barand[csf("brand")];
				}

				if($db_type==0) $select_f_grp="group by mst_id,yarn_color, color_range,
				id,product_id,job_no,job_no_id,yarn_description,count,dyeing_charge,min_require_cone,referance_no,no_of_cone,no_of_bag, remarks order by id ";
				else if($db_type==2) $select_f_grp="group by mst_id,yarn_color, color_range,
				id,product_id,job_no,job_no_id,yarn_description,count,dyeing_charge,min_require_cone,referance_no,no_of_cone,no_of_bag, remarks order by id ";

				$sql_color="select id,mst_id,product_id,job_no,no_of_bag,no_of_cone,job_no_id,yarn_color,yarn_description,count,color_range,sum(yarn_wo_qty) as yarn_wo_qty,dyeing_charge,sum(amount) as amount,min_require_cone,referance_no, remarks
				from
				wo_yarn_dyeing_dtls
				where
				status_active=1 and id in($total_dtls_id) $select_f_grp ";
			//echo $sql_color;die;
				$sql_result=sql_select($sql_color);$total_qty=0;$total_amount=0;$i=1;$buyer=0;$order_no="";
				$mstID="";
				foreach($sql_result as $row)
				{
					$mstID=$row[csf("mst_id")];
					$product_id=$row[csf("product_id")];
				//var_dump($product_id);
					if($row[csf("product_id")]!="")
					{
						$lot_amt=$product_lot[$row[csf("product_id")]]['lot'];
						$brand=$product_lot[$row[csf("product_id")]]['brand'];
					}

			//$yarn_count_des=explode(" ",$row[csf("yarn_description")]);

					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";


					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td align="center"><? echo $i; ?></td>
						<td align="center"><? echo $lot_amt; ?></td>
						<td align="center"><? echo $count_arr[$row[csf("count")]]; //$count_arr[$row[csf("count")]]; ?></td>
						<td>
							<?
							echo $row[csf("yarn_description")];
							?>
						</td>
						<td><? echo $brand_arr[$brand]; ?></td>

						<td align="right"><? echo $row[csf("yarn_wo_qty")]; $total_qty+=$row[csf("yarn_wo_qty")]; ?>&nbsp;</td>
						<td align="right"><? echo $row[csf("dyeing_charge")]; ?>&nbsp;</td>
						<td align="right"><? echo number_format($row[csf("amount")],2);  $total_amount+=$row[csf("amount")];?></td>
						<td align="right"><? echo $row[csf("no_of_bag")]; ?></td>
						<td align="right"><? echo $row[csf("no_of_cone")]; ?></td>
						<td align="right"><? echo $row[csf("min_require_cone")]; ?></td>
						<td align="left"><? echo $row[csf("remarks")]; ?></td>
					</tr>
					<?
					$i++;
					$yarn_count_des="";
					$style_no="";
				}
				?>
				<tr>
					<td colspan="5" align="right"><strong>Total:</strong>&nbsp;&nbsp;</td>
					<td align="right" ><b><? echo number_format($total_qty,2, '.', ''); ?></b></td>
					<td align="right">&nbsp;</td>
					<td align="right"><b><? echo number_format($total_amount,2); ?></b></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<?
				$mcurrency="";
				$dcurrency="";
				if($currency_id==1)
				{
					$mcurrency='Taka';
					$dcurrency='Paisa';
				}
				if($currency_id==2)
				{
					$mcurrency='USD';
					$dcurrency='CENTS';
				}
				if($currency_id==3)
				{
					$mcurrency='EURO';
					$dcurrency='CENTS';
				}
				?>
				<tr>
					<td colspan="13" align="center">Total Dyeing Amount (in word): &nbsp;<?  echo number_to_words(def_number_format($total_amount,2,""),$mcurrency, $dcurrency);?> </td>
				</tr>
			</table>
			<br><br>
			<table class="mainTable" width="450" style="" align="center" border="1"  cellpadding="0" cellspacing="0" rules="all">
				<caption><b> Combo Details</b> </caption>
				<?
				if($db_type	==0) $ys_cond = " and b.id =c.dtls_id";
				else $ys_cond = "  cast (c.dtls_id as varchar(4000))= b.id";

				$sql_yarn_qnty= sql_select("select id,yarn_wo_qty as yarn_wo_qty from wo_yarn_dyeing_dtls where mst_id=$mstID and status_active=1 and is_deleted=0");
				foreach ($sql_yarn_qnty as $row) {
					$yarn_qnty_arr[$row[csf("id")]]["yarn_wo_qty"]+=$row[csf("yarn_wo_qty")];
				}

				$sql_color_dtls="select sum(b.yarn_wo_qty) as yarn_wo_qty,sum(b.amount) as amount,b.yarn_color,c.yarn_count,c.yarn_color,c.yarn_comp,c.yarn_type,c.dtls_id
				from
				wo_yarn_dyeing_dtls b,wo_yarn_dyeing_dtls_fin_prod c
				where  b.mst_id= c.mst_id and b.status_active=1 and c.status_active=1 and b.id in($total_dtls_id) group by b.yarn_color,c.yarn_count,c.yarn_color,c.yarn_comp,c.yarn_type,c.dtls_id ";
				//echo $sql_color;die;
				$dtls_result=sql_select($sql_color_dtls);
				?>
				<tr>
					<td width="30" align="center"><strong>Sl</strong></td>
					<td width="180" align="center"><strong>Product Name</strong></td>
					<td width="100" align="center"><strong>Color</strong></td>
					<td width="80" align="center"><strong>Qnty</strong></td>
				</tr>
				<?
				$total_color_qty=0;$d=1;
				foreach($dtls_result as $row)
				{
					$dtlsID=explode(",", $row[csf("dtls_id")]);
					$yarn_wo_qty=0;
					foreach ($dtlsID as $rows) {
						$yarn_wo_qty+=$yarn_qnty_arr[$rows]["yarn_wo_qty"];
					}



					if ($d%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";//yarn_comp
					$product_name=$count_arr[$row[csf("yarn_count")]].','.$composition[$row[csf("yarn_comp")]].','.$yarn_type[$row[csf("yarn_type")]];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td align="center"><? echo $d; ?></td>
						<td align="center"><? echo $product_name; //$count_arr[$row[csf("count")]]; ?></td>
						<td>
							<?
							echo $color_arr[$row[csf("yarn_color")]];
							?>
						</td>
						<td align="right"><? echo $yarn_wo_qty;// $row[csf("yarn_wo_qty")]; ?></td>

					</tr>
					<?
					$d++;
					$total_color_qty+= $yarn_wo_qty;
				}
				?>
				<tr>
					<td colspan="3" align="right"><strong>Total:</strong>&nbsp;&nbsp;</td>
					<td align="right" ><b><? echo number_format($total_color_qty,2, '.', ''); ?></b></td>
				</tr>

			</table>


			<!--==============================================AS PER GMTS COLOR START=========================================  -->
			<table width="950" style="" align="center"><tr><td><strong>Note:</strong></td></tr></table>

			<? echo get_spacial_instruction($work_order,"100%",94);?>

		</div>
		<div>
		<br>
			<table width="780" align="center">
					<tr>
						<div style="text-align:center;font-size:xx-large; font-style:italic; margin-top:20px; color:#FF0000;">
								<?
								if(count($approval_arr)>0)
								{				
									if($is_approved == 0){echo "Draft";}else{}
								}
								?>
						</div>
					</tr>
			</table>
		<br>
			<?
			echo signature_table(122, $cbo_company_name, "970px","",1);
			// echo "****".custom_file_name($txt_booking_no,$style_sting,$txt_job_no);
			?>
		</div>
		<script>
			// alert('ok')
		</script>
		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/jquery.qrcode.min.js"></script>
		
		<!-- <script type="text/javascript" src="../../js/jquerybarcode.js"></script> -->
		<!-- <script>
			fnc_generate_Barcode('<? //echo $varcode_work_order_no; ?>','barcode_img_id');
		</script> -->
		
		<script>
			var main_value='<? echo $work_order; ?>'+'***1***'+'<?echo $cbo_service_type?>';
			// alert(main_value);
			$('#qrcode').qrcode(main_value)			
		</script>
		<?
		exit();
}

if($action=="show_with_multiple_job_without_rate")
{
	extract($_REQUEST);
	$form_name=str_replace("'","",$form_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$update_id=str_replace("'","",$update_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$pay_mode = str_replace("'","",$cbo_pay_mode);
	$new_supplier_name = str_replace("'","",$cbo_supplier_name);

	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$supplier_name=return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");

//echo $new_supplier_name;
//echo $pay_mode;
//echo $company_library[$new_supplier_name];
//die;
//select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name

// $company_library=return_library_array( "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id", "company_name"  );
// $supplier_name=return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");

// $buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
// $brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');
//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<div style="width:950px" align="center">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
			<tr>
				<td width="750">
					<table width="100%" cellpadding="0" cellspacing="0" >
						<tr>
							<td align="center" style="font-size:20px;">
								<?php
echo $company_library[$cbo_company_name];
?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:14px">
								<?
								$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
								foreach ($nameArray as $result)
								{
									?>
									Email Address: <? echo $result[csf('email')];?>
									Website No: <? echo $result[csf('website')];
								}
								?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:20px">
								<strong>Yarn Dyeing Work Order </strong>
							</td>
						</tr>
					</table>
				</td>
				<td width="250" id="barcode_img_id">
				</td>
			</tr>
		</table>
		<?
	//echo "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention  from wo_yarn_dyeing_mst a where a.id=$update_id";die;
		$nameArray=sql_select( "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency from wo_yarn_dyeing_mst a where a.id=$update_id");
		foreach ($nameArray as $result)
		{
			$work_order=$result[csf('ydw_no')];
			$supplier_id=$result[csf('supplier_id')];
			$booking_date=$result[csf('booking_date')];
			$currency=$result[csf('currency')];
			$attention=$result[csf('attention')];
			$delivery_date=$result[csf('delivery_date')];
			$delivery_date_end=$result[csf('delivery_date_end')];
			$dy_delivery_start=$result[csf('dy_delivery_date_start')];
			$dy_delivery_end=$result[csf('dy_delivery_date_end')];
		}
		$varcode_work_order_no=$work_order;

		?>
		<table width="950" style="" align="center">
			<tr>
				<td width="350"  style="font-size:12px">
					<table width="350" style="" align="left">
						<tr  >
							<td   width="120"><b>To</b>   </td>
							<td width="230">:&nbsp;&nbsp;
								<?

								if($pay_mode==3 || $pay_mode==5)
								{
									echo $company_library[1];
									//echo $company_library[$new_supplier_name];
								}
								else
								{
									echo $supplier_name[$new_supplier_name];
								}
								 // if($pay_mode==3 || $pay_mode==5)
								 // {
								 // 	 echo $company_library[$supplier_id];
								 // }
								 // else
								 // {
								 // 	 echo $supplier_arr[$supplier_id];
								 // }
								//echo $supplier_arr[$supplier_id];


								?>

							</td>

						</tr>

						<tr>
							<td  ><b>Wo No.</b>   </td>
							<td  >:&nbsp;&nbsp;<? echo $work_order;?>    </td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Attention</b></td>
							<td >:&nbsp;&nbsp;<? echo $attention; ?></td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Booking Date</b></td>
							<td >:&nbsp;&nbsp;<? if($booking_date!="0000-00-00" || $booking_date!="") echo change_date_format($booking_date); else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>Currency</b></td>
							<td >:&nbsp;&nbsp;<? echo $currency[$currency_val]; ?></td>
						</tr>
					</table>
				</td>
				<td width="350"  style="font-size:12px">
					<table width="350" style="" align="left">
						<tr>
							<td  width="120"><b>G/Y Issue Start</b>   </td>
							<td width="230" >:&nbsp;&nbsp;<? if($delivery_date!="0000-00-00" || $delivery_date!="") echo change_date_format($delivery_date); else echo "";?>    </td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>G/Y Issue End</b></td>
							<td >:&nbsp;&nbsp;<? if($delivery_date_end!="0000-00-00" || $delivery_date_end!="") echo change_date_format($delivery_date_end);  else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery Start </b></td>
							<td >:&nbsp;&nbsp;<? if($dy_delivery_start!="0000-00-00" || $dy_delivery_start!="") echo change_date_format($dy_delivery_start); else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery End</b></td>
							<td >:&nbsp;&nbsp;<? if($dy_delivery_end!="0000-00-00" || $dy_delivery_end!="") echo change_date_format($dy_delivery_end); else echo "";?></td>
						</tr>
					</table>
				</td>
				<td width="250"  style="font-size:12px">
					<?
					$image_location=return_field_value("image_location","common_photo_library","master_tble_id='$update_id' and form_name='$form_name'","image_location");
					?>
					<img src="<? echo '../../'.$image_location; ?>" width="120" height="100" border="2" />
				</td>
			</tr>
		</table>
	</br>

	<table width="950" style="" align="center" border="1"  cellpadding="0" cellspacing="0" rules="all">
		<tr>
			<td width="30" align="center"><strong>Sl</strong></td>
			<td width="65" align="center"><strong>Color</strong></td>
			<td width="80" align="center"><strong>Color Range</strong></td>
			<td  align="center" width="50"><strong>Ref No.</strong></td>
			<td  align="center" width="70"><strong>Style Ref.No.</strong></td>
			<td width="30" align="center"><strong>Yarn Count</strong></td>
			<td width="140" align="center"><strong>Yarn Description</strong></td>
			<td width="60" align="center"><strong>Brand</strong></td>
			<td width="50" align="center"><strong>Lot</strong></td>
			<td width="60" align="center"><strong>WO Qty</strong></td>
			<td  align="center" width="50"><strong>Min Req. Cone</strong></td>
			<td  align="center" width="80"><strong>Sample Develop Id</strong></td>
			<td  align="center" width="80"><strong>Buyer</strong></td>
			<td  align="center" ><strong>Sample Name</strong></td>
		</tr>
		<?
		$sql_brand=sql_select("select id,lot,brand from product_details_master where status_active=1");
		foreach($sql_brand as $row_barand)
		{
			$product_lot[$row_barand[csf("id")]]['lot']=$row_barand[csf("lot")];
			$product_lot[$row_barand[csf("id")]]['brand']=$row_barand[csf("brand")];
		}
		$buyer_sample=return_library_array( "select id, buyer_name from  sample_development_mst",'id','buyer_name');
		$style_ref_sample=return_library_array( "select id, style_ref_no from  sample_development_mst",'id','style_ref_no');

		if($db_type==0)
		{
			$sql="select a.id, a.ydw_no,b.id as dtls_id,b.product_id,b.job_no,b.job_no_id,b.sample_name,b.yarn_color,b.yarn_description,b.count,b.color_range,sum(b.yarn_wo_qty) as yarn_wo_qty,b.dyeing_charge,sum(b.amount) as amount,b.min_require_cone,b.referance_no
			from
			wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
			where
			a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id
			group by b.count, b.job_no_id, b.yarn_color, b.color_range
			order by b.id";
		}
		else if($db_type==2)
		{
			$sql="select a.id, a.ydw_no,b.id as dtls_id,b.product_id,b.job_no,b.job_no_id, listagg(CAST(b.sample_name AS VARCHAR(4000)),',')  within group (order by b.sample_name) as sample_name,b.yarn_color,b.yarn_description,b.count,b.color_range,sum(b.yarn_wo_qty) as yarn_wo_qty,b.dyeing_charge,sum(b.amount) as amount,b.min_require_cone,b.referance_no
			from
			wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
			where
			a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id
			group by b.job_no_id, b.yarn_color, b.color_range,a.id,b.product_id,b.job_no,b.job_no_id,b.yarn_color,b.yarn_description,b.count,b.color_range,b.dyeing_charge,b.min_require_cone,b.referance_no, a.ydw_no,b.id
			order by b.id";
		}

		$sql_result=sql_select($sql);$total_qty=0;$total_amount=0;$i=1;$buyer=0;$order_no="";
		foreach($sql_result as $row)
		{
			$product_id=$row[csf("product_id")];
			//var_dump($product_id);
			if($product_id)
			{
				$sql_brand=sql_select("select lot,brand from product_details_master where id in($product_id)");
				foreach($sql_brand as $row_barand)
				{
					$lot_amt=$row_barand[csf("lot")];
					$brand=$row_barand[csf("brand")];
				}

			}

		//$yarn_count_des=explode(" ",$row[csf("yarn_description")]);
			$all_style_arr[]=$style_ref_sample[$row[csf("job_no_id")]];
			$all_job_arr[]=$row[csf("job_no")];

			if ($i%2==0)
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";

			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td align="center"><? echo $i; ?></td>
				<td><? echo $color_arr[$row[csf("yarn_color")]]; ?></td>
				<td><? echo $color_range[$row[csf("color_range")]]; ?></td>
				<td align="center"><? echo $row[csf("referance_no")]; ?></td>
				<td align="center">
					<?
					echo $style_ref_sample[$row[csf("job_no_id")]];
					?>
				</td>
				<td align="center"><? echo $count_arr[$row[csf("count")]]; //$count_arr[$row[csf("count")]]; ?></td>
				<td>
					<?
					echo $row[csf("yarn_description")];
					?>
				</td>
				<td><? echo $brand_arr[$brand]; ?></td>
				<td align="center"><? echo $lot_amt; ?></td>
				<td align="right"><? echo $row[csf("yarn_wo_qty")]; $total_qty+=$row[csf("yarn_wo_qty")]; ?>&nbsp;</td>
				<td align="center"><? echo $row[csf("min_require_cone")]; ?></td>
				<td align="center"><? echo $row[csf("job_no_id")]; ?></td>
				<td align="center"><? echo $buyer_arr[$buyer_sample[$row[csf("job_no_id")]]]; ?> </td>
				<td align="center">
					<?
					$sample_name_arr=array_unique(explode(",",$row[csf('sample_name')]));
					$sample_name_group="";
					foreach($sample_name_arr as $val)
					{
						if($sample_name_group=="") $sample_name_group=$sample_arr[$val]; else $sample_name_group=$sample_name_group.",".$sample_arr[$val];
					}
					echo $sample_name_group;
					?>
				</td>
			</tr>
			<?
			$i++;
			$yarn_count_des="";
			$style_no="";
		}
		?>
		<tr>
			<td colspan="9" align="right"><strong>Total:</strong>&nbsp;&nbsp;</td>
			<td align="right" ><b><? echo $total_qty; ?></b></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</table>


	<!--==============================================AS PER GMTS COLOR START=========================================  -->
	<table width="950" style="" align="center"><tr><td><strong>Note:</strong></td></tr></table>

	<? echo get_spacial_instruction($work_order,"100%",94);?>

</div>
<div>
	<?
	echo signature_table(122, $cbo_company_name, "950px");
	echo "****".custom_file_name($txt_booking_no,implode(',',$all_style_arr),implode(',',$all_job_arr));
	?>
</div>
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
<script>
	fnc_generate_Barcode('<? echo $varcode_work_order_no; ?>','barcode_img_id');
</script>
<?
exit();
}

if($action=="sales_order_report")
{
	//echo "uuuu";die;
	extract($_REQUEST);
	$cbo_service_type=str_replace("'","",$cbo_service_type);
	$form_name=str_replace("'","",$form_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$update_id=str_replace("'","",$update_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	if($show_val_column == 1)
	{
		$colspan = "13";
	}else{
		$colspan = "12";
	}
	?>
	<div style="width:950px" align="center">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
			<tr>
				<td width="700">
					<table width="100%" cellpadding="0" cellspacing="0" >
						<tr>
							<td align="center" style="font-size:20px;">
								<?php
	echo "<b>$company_library[$cbo_company_name]</b>";
	?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:14px">
								<?
								$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
								foreach ($nameArray as $result)
								{
									?>

									Email Address: <? echo $result[csf('email')];?>
									Website No: <? echo $result[csf('website')];
								}

								?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:20px">
								<strong style="font-size: 16px"><? echo "Yarn ". $yarn_issue_purpose[$cbo_service_type];?> Work Order </strong>
							</td>
						</tr>
					</table>
				</td>
				<td width="250" id="barcode_img_id">

				</td>
			</tr>
		</table>
		<?
		$nameArray=sql_select( "SELECT a.id,a.inserted_by, a.ydw_no,a.booking_date,a.supplier_id,a.pay_mode,a.is_approved,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency,b.job_no, c.sales_booking_no, c.style_ref_no from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b, fabric_sales_order_mst c where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.job_no_id = c.id and a.id=$update_id group by a.id,a.inserted_by, a.ydw_no,a.booking_date,a.supplier_id,a.pay_mode,a.is_approved,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency,b.job_no, c.sales_booking_no, c.style_ref_no");



		$salesBookingChk = array();
		$sales_booking_arr = array();
		foreach ($nameArray as $result)
		{
			$work_order=$result[csf('ydw_no')];
			$is_approved=$result[csf('is_approved')];
			$supplier_id=$result[csf('supplier_id')];
			$booking_date=$result[csf('booking_date')];
			$currency_val=$result[csf('currency')];
			$attention=$result[csf('attention')];
			$delivery_date=$result[csf('delivery_date')];
			$delivery_date_end=$result[csf('delivery_date_end')];
			$dy_delivery_start=$result[csf('dy_delivery_date_start')];
			$dy_delivery_end=$result[csf('dy_delivery_date_end')];
			$currency_id=$result[csf('currency')];
			$job_no=$result[csf('job_no')];
			$pay_mode=$result[csf('pay_mode')];
			$style_ref_no=$result[csf('style_ref_no')];
			$inserted_by=$result[csf('inserted_by')];

			if($salesBookingChk[$result[csf('sales_booking_no')]] == "")
			{
				$salesBookingChk[$result[csf('sales_booking_no')]] = $result[csf('sales_booking_no')];
				array_push($sales_booking_arr, $result[csf('sales_booking_no')]);
			}
			//$booking_no=$result[csf('sales_booking_no')];


		}


		$sales_info = sql_select("SELECT a.po_break_down_id, a.booking_no, b.grouping from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id ".where_con_using_array($sales_booking_arr,1,'a.booking_no')." group by a.po_break_down_id, a.booking_no, b.grouping");

		foreach ($sales_info as $row)
		{
			//$salesInfoArr[$row[csf('booking_no')]]['grouping'] .= $row[csf('grouping')].',';
			$po_int_ref_arr[$row[csf('grouping')]] = $row[csf('grouping')];
		}

		$int_ref = implode(",", $po_int_ref_arr);


		$varcode_work_order_no=$work_order;
		?>
		<table width="1050" style="" align="center">
			<tr>
				<td width="350"  style="font-size:12px">
					<table width="350" style="" align="left">
						<tr>
							<td   width="120"><b>To</b>   </td>
							<td width="230">:&nbsp;&nbsp;<b><? if($result[csf('pay_mode')]==3 || $result[csf('pay_mode')]==5){echo $company_library[$result[csf('supplier_id')]];}else{echo $supplier_arr[$result[csf('supplier_id')]];} ?></b></td>
						</tr>

						<tr>
							<td  ><b>Wo No.</b>   </td>
							<td  >:&nbsp;&nbsp;<b><? echo $work_order;?></b></td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Attention</b></td>
							<td >:&nbsp;&nbsp;<? echo $attention; ?></td>
						</tr>
					</table>
				</td>
				<td width="550" style="font-size:12px">
					<table width="550" style="" align="left">
                			<!-- <tr>
								<td  width="120"><b>Sales Order No.</b>   </td>
								<td width="120" >:&nbsp;&nbsp;<? echo $job_no;?></td>
							</tr> -->

							<tr>
								<td style="font-size:12px"><b>Currency</b></td>
								<td >:&nbsp;&nbsp;<? echo $currency[$currency_val]; ?></td>
								<td style="font-size:12px"><b>Style Ref</b></td>
								<td >:&nbsp;&nbsp; <? echo $style_ref_no; ?></td>
							</tr>
                            <!-- <tr>
                                <td style="font-size:12px"><b>Booking No</b></td>
                                <td >:&nbsp;&nbsp;<? //echo $booking_no; ?></td>
                            </tr>  -->
                            <tr>
                            	<td style="font-size:12px"><b>Booking Date</b></td>
                            	<td >:&nbsp;&nbsp;<? if($booking_date!="0000-00-00" || $booking_date!="") echo change_date_format($booking_date); else echo ""; ?></td>
								<td valign="top" style="font-size:12px"><b>IR/IB</b></td>
								<td>:&nbsp;&nbsp; <? echo $int_ref; ?></td>
                            </tr>
                            <tr>
                            	<td  width="120"><b>Delivery Date</b>   </td>
                            	<td width="230" >:&nbsp;&nbsp;<? if($delivery_date!="0000-00-00" || $delivery_date!="") echo change_date_format($delivery_date); else echo "";?>    </td>
                            </tr>

                        </table>
                    </td>


                </tr>
				<tr>
					<td width="350"></td>
					<td width="350"  style="font-size:22px;color:red;font-weight:bold;text-align:right;"><? if ($is_approved==1) echo "Approved"; else if ($is_approved==3) echo "Partial Approved"; ?></td>
				</tr>

            </table>
        </br>

        <?
        $buyer_sample=return_library_array( "select id, buyer_name from  sample_development_mst",'id','buyer_name');
        $sql="select a.id, a.ydw_no,b.job_no_id,b.sample_name,b.id as dtls_id,b.job_no
        from
        wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
        where
        a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id";
					//echo $sql;
        $sql_result=sql_select($sql);$total_samp_deve_id="";$total_buyer="";$total_dtls_id='';$total_sample_name='';
        foreach($sql_result as $row)
        {
        	if($total_dtls_id=='') $total_dtls_id=$row[csf("dtls_id")]; else $total_dtls_id=$total_dtls_id.",".$row[csf("dtls_id")];

        	if($total_samp_deve_id=="") $total_samp_deve_id=$row[csf("job_no_id")]; else $total_samp_deve_id=$total_samp_deve_id.",".$row[csf("job_no_id")];

        	if($total_buyer=="") $total_buyer=$buyer_sample[$row[csf('job_no_id')]]; else $total_buyer=$total_buyer.",".$buyer_sample[$row[csf('job_no_id')]];

        	if($total_sample_name=="") $total_sample_name=$row[csf("sample_name")]; else $total_sample_name=$total_sample_name.",".$row[csf("sample_name")];

        }

        ?>


        <table width="1050" style="" align="center" border="1"  cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
        	<thead>
        		<tr>
        			<th width="30" align="center"><strong>Sl</strong></th>
        			<th width="100" align="center"><strong>Sales Order</strong></th>
        			<th width="100" align="center"><strong>Booking No.</strong></th>
        			<th width="60" align="center"><strong>Lot</strong></th>
        			<th width="30" align="center"><strong>Yarn Count</strong></th>
        			<th width="160" align="center"><strong>Yarn Description</strong></th>
        			<th width="60" align="center"><strong>Brand</strong></th>

        			<th width="60" align="center"><strong>WO Qty</strong></th>
        			<?
        			if($show_val_column == 1)
        			{
        				?>
        				<th width="50" align="right"><strong>Rate</strong></th>

        			<th width="80" align="right"><strong>Amount</strong></th>
					<?
        			}
        			?>
        			<th width="80" align="right"><strong>No of Bag</strong></th>
        			<th width="80" align="right"><strong>No of Cone</strong></th>
        			<th  align="center" width="60" ><strong>Min Req. Cone</strong></th>
        			<th  align="left" ><strong>Remarks</strong></td>
        			</tr>
        		</thead>
        		<?

        		$sql_brand=sql_select("select id,lot,brand from product_details_master where status_active=1");
        		foreach($sql_brand as $row_barand)
        		{
        			$product_lot[$row_barand[csf("id")]]['lot']=$row_barand[csf("lot")];
        			$product_lot[$row_barand[csf("id")]]['brand']=$row_barand[csf("brand")];
        		}

        		if($db_type==0) $select_f_grp="group by yarn_color, color_range,
        		id,product_id,job_no,job_no_id,yarn_description,count,dyeing_charge,min_require_cone,referance_no,no_of_cone,no_of_bag, remarks order by id ";
        		else if($db_type==2) $select_f_grp="group by yarn_color, color_range,
        		id,product_id,job_no,job_no_id,yarn_description,count,dyeing_charge,min_require_cone,referance_no,no_of_cone,no_of_bag, remarks order by id ";

			/* $sql_color="select id,product_id,job_no,no_of_bag,no_of_cone,job_no_id,yarn_color,yarn_description,count,color_range,sum(yarn_wo_qty) as yarn_wo_qty,dyeing_charge,sum(amount) as amount,min_require_cone,referance_no, remarks
			from
					wo_yarn_dyeing_dtls, fabric_sales_order_mst c
			where
			b.job_no_id = c.id and status_active=1 and id in($total_dtls_id) $select_f_grp ";*/

			$sql_color="select a.id,a.product_id,a.job_no,a.no_of_bag,a.no_of_cone,a.job_no_id,a.yarn_color,a.yarn_description,a.count,a.color_range,sum(a.yarn_wo_qty) as yarn_wo_qty,a.dyeing_charge,sum(a.amount) as amount,
			a.min_require_cone,a.referance_no, a.remarks, b.sales_booking_no from wo_yarn_dyeing_dtls a, fabric_sales_order_mst b
			where a.job_no_id = b.id and a.status_active=1 and a.id in($total_dtls_id) group by a.yarn_color, a.color_range, a.id,a.product_id,a.job_no,a.job_no_id,a.yarn_description,
			a.count,a.dyeing_charge,a.min_require_cone,a.referance_no,a.no_of_cone,a.no_of_bag, a.remarks ,b.sales_booking_no
			order by a.id";



			//echo $sql_color;die;
			$sql_result=sql_select($sql_color);$total_qty=0;$total_amount=0;$i=1;$buyer=0;$order_no="";
			foreach($sql_result as $row)
			{
				$product_id=$row[csf("product_id")];
				//var_dump($product_id);
				if($row[csf("product_id")]!="")
				{
					$lot_amt=$product_lot[$row[csf("product_id")]]['lot'];
					$brand=$product_lot[$row[csf("product_id")]]['brand'];
				}

			//$yarn_count_des=explode(" ",$row[csf("yarn_description")]);

				if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";


				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center"><? echo $i; ?></td>
					<td align="center"><? echo $row[csf("job_no")]; ?></td>
					<td align="center"><? echo $row[csf("sales_booking_no")]; ?></td>
					<td align="center"><? echo $lot_amt; ?></td>
					<td align="center"><? echo $count_arr[$row[csf("count")]]; //$count_arr[$row[csf("count")]]; ?></td>
					<td>
						<?
						echo $row[csf("yarn_description")];
						?>
					</td>
					<td><? echo $brand_arr[$brand]; ?></td>

					<td align="right"><? echo $row[csf("yarn_wo_qty")]; $total_qty+=$row[csf("yarn_wo_qty")]; ?>&nbsp;</td>
					<?
					if($show_val_column == 1)
					{
						?>
						<td align="right"><? echo $row[csf("dyeing_charge")]; ?>&nbsp;</td>

					<td align="right"><? echo number_format($row[csf("amount")],2);  $total_amount+=$row[csf("amount")];?></td>
					<?
					}
					?>
					<td align="right"><? echo $row[csf("no_of_bag")]; ?></td>
					<td align="right"><? echo $row[csf("no_of_cone")]; ?></td>
					<td align="right"><? echo $row[csf("min_require_cone")]; ?></td>
					<td align="left"><? echo $row[csf("remarks")]; ?></td>
				</tr>
				<?
				$i++;
				$yarn_count_des="";
				$style_no="";
			}
			?>
			<tr>
				<td colspan="7" align="right"><strong>Total:</strong>&nbsp;&nbsp;</td>
				<td align="right" ><b><? echo number_format($total_qty,2,".",""); ?></b></td>

				<?
				if($show_val_column == 1)
				{
					?>
					<td align="right">&nbsp;</td>
					<?
				}
				?>
				<td align="right"><b><? echo number_format($total_amount,2); ?></b></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<?
			$mcurrency="";
			$dcurrency="";
			if($currency_id==1)
			{
				$mcurrency='Taka';
				$dcurrency='Paisa';
			}
			if($currency_id==2)
			{
				$mcurrency='USD';
				$dcurrency='CENTS';
			}
			if($currency_id==3)
			{
				$mcurrency='EURO';
				$dcurrency='CENTS';
			}
			?>
			<tr>
				<td colspan="14" align="center">Total Dyeing Amount (in word): &nbsp;<?  echo number_to_words(def_number_format($total_amount,2,""),$mcurrency, $dcurrency);?> </td>
			</tr>
		</table>


		<!--==============================================AS PER GMTS COLOR START=========================================  -->
		<table width="1050" style="" align="center"><tr><td><strong>Note:</strong></td></tr></table>

		<? echo get_spacial_instruction($work_order,"100%",94);?>

	</div>
	<div>
		<?
		$appSql = "select a.APPROVED_BY, a.APPROVED_DATE,b.USER_FULL_NAME,c.CUSTOM_DESIGNATION from approval_mst a,USER_PASSWD b,LIB_DESIGNATION c where a.mst_id =$update_id and a.APPROVED_BY=b.id and b.DESIGNATION=c.id and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0
		union all
		select b.id as APPROVED_BY, a.INSERT_DATE as APPROVED_DATE,b.USER_FULL_NAME,c.CUSTOM_DESIGNATION from wo_yarn_dyeing_mst a,USER_PASSWD b,LIB_DESIGNATION c where a.id =$update_id and a.INSERTED_BY=b.id and b.DESIGNATION=c.id and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0";
		//echo $appSql;die;
		$appSqlRes=sql_select($appSql);
		$userDtlsArr=array();
		foreach($appSqlRes as $row){
			$userDtlsArr[$row['APPROVED_BY']]="<div><b>".$row['USER_FULL_NAME']."</b></div><div><b>".$row['CUSTOM_DESIGNATION']."</b></div><div><small>".$row['APPROVED_DATE']."</small></div>";
		}
		echo get_app_signature(122, $cbo_company_name, "950px",$template_id, 50,$inserted_by,$userDtlsArr);
		//echo signature_table(122, $cbo_company_name, "950px");
		echo "****".custom_file_name($txt_booking_no,$style_sting,$txt_job_no);
		?>
	</div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		fnc_generate_Barcode('<? echo $varcode_work_order_no; ?>','barcode_img_id');
	</script>
	<?
	exit();
}

if($action=="sales_order_report_two")
{
	//echo "uuuu";die;
	extract($_REQUEST);
	$cbo_service_type=str_replace("'","",$cbo_service_type);
	$form_name=str_replace("'","",$form_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$update_id=str_replace("'","",$update_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	if($show_val_column == 1)
	{
		$colspan = "13";
	}else{
		$colspan = "12";
	}
	?>
	<div style="width:950px" align="center">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
			<tr>
				<td width="700">
					<table width="100%" cellpadding="0" cellspacing="0" >
						<tr>
							<td align="center" style="font-size:20px;">
								<?php
								echo "<b>$company_library[$cbo_company_name]</b>";
								?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:14px">
								<?
								$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
								foreach ($nameArray as $result)
								{
									?>

									Email Address: <? echo $result[csf('email')];?>
									Website No: <? echo $result[csf('website')];
								}

								?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:20px">
								<strong style="font-size: 16px"><? echo "Yarn ". $yarn_issue_purpose[$cbo_service_type];?> Work Order </strong>
							</td>
						</tr>
					</table>
				</td>
				<td width="250" id="barcode_img_id">

				</td>
			</tr>
		</table>
		<?
		$nameArray=sql_select( "SELECT a.id,a.inserted_by, a.ydw_no,a.booking_date,a.supplier_id,a.pay_mode,a.is_approved,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency,b.job_no, c.sales_booking_no, c.style_ref_no from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b, fabric_sales_order_mst c where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.job_no_id = c.id and a.id=$update_id group by a.id,a.inserted_by, a.ydw_no,a.booking_date,a.supplier_id,a.pay_mode,a.is_approved,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency,b.job_no, c.sales_booking_no, c.style_ref_no");



		$salesBookingChk = array();
		$sales_booking_arr = array();
		foreach ($nameArray as $result)
		{
			$work_order=$result[csf('ydw_no')];
			$is_approved=$result[csf('is_approved')];
			$supplier_id=$result[csf('supplier_id')];
			$booking_date=$result[csf('booking_date')];
			$currency_val=$result[csf('currency')];
			$attention=$result[csf('attention')];
			$delivery_date=$result[csf('delivery_date')];
			$delivery_date_end=$result[csf('delivery_date_end')];
			$dy_delivery_start=$result[csf('dy_delivery_date_start')];
			$dy_delivery_end=$result[csf('dy_delivery_date_end')];
			$currency_id=$result[csf('currency')];
			$job_no=$result[csf('job_no')];
			$pay_mode=$result[csf('pay_mode')];
			$style_ref_no=$result[csf('style_ref_no')];
			$inserted_by=$result[csf('inserted_by')];

			if($salesBookingChk[$result[csf('sales_booking_no')]] == "")
			{
				$salesBookingChk[$result[csf('sales_booking_no')]] = $result[csf('sales_booking_no')];
				array_push($sales_booking_arr, $result[csf('sales_booking_no')]);
			}
			//$booking_no=$result[csf('sales_booking_no')];


		}


		$sales_info = sql_select("SELECT a.po_break_down_id, a.booking_no, b.grouping from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id ".where_con_using_array($sales_booking_arr,1,'a.booking_no')." group by a.po_break_down_id, a.booking_no, b.grouping");

		foreach ($sales_info as $row)
		{
			//$salesInfoArr[$row[csf('booking_no')]]['grouping'] .= $row[csf('grouping')].',';
			$po_int_ref_arr[$row[csf('grouping')]] = $row[csf('grouping')];
		}

		$int_ref = implode(",", $po_int_ref_arr);


		$varcode_work_order_no=$work_order;
		?>
		<table width="1050" style="" align="center">
			<tr>
				<td width="350"  style="font-size:12px">
					<table width="350" style="" align="left">
						<tr>
							<td   width="120"><b>To</b>   </td>
							<td width="230">:&nbsp;&nbsp;<b><? if($result[csf('pay_mode')]==3 || $result[csf('pay_mode')]==5){echo $company_library[$result[csf('supplier_id')]];}else{echo $supplier_arr[$result[csf('supplier_id')]];} ?></b></td>
						</tr>

						<tr>
							<td  ><b>Wo No.</b>   </td>
							<td  >:&nbsp;&nbsp;<b><? echo $work_order;?></b></td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Attention</b></td>
							<td >:&nbsp;&nbsp;<? echo $attention; ?></td>
						</tr>
					</table>
				</td>
				<td width="550" style="font-size:12px">
					<table width="550" style="" align="left">
                			<!-- <tr>
								<td  width="120"><b>Sales Order No.</b>   </td>
								<td width="120" >:&nbsp;&nbsp;<? echo $job_no;?></td>
							</tr> -->

							<tr>
								<td style="font-size:12px"><b>Currency</b></td>
								<td >:&nbsp;&nbsp;<? echo $currency[$currency_val]; ?></td>
								<td style="font-size:12px"><b>Style Ref</b></td>
								<td >:&nbsp;&nbsp; <? echo $style_ref_no; ?></td>
							</tr>
                            <!-- <tr>
                                <td style="font-size:12px"><b>Booking No</b></td>
                                <td >:&nbsp;&nbsp;<? //echo $booking_no; ?></td>
                            </tr>  -->
                            <tr>
                            	<td style="font-size:12px"><b>Booking Date</b></td>
                            	<td >:&nbsp;&nbsp;<? if($booking_date!="0000-00-00" || $booking_date!="") echo change_date_format($booking_date); else echo ""; ?></td>
								<td valign="top" style="font-size:12px"><b>IR/IB</b></td>
								<td>:&nbsp;&nbsp; <? echo $int_ref; ?></td>
                            </tr>
                            <tr>
                            	<td  width="120"><b>Delivery Date</b>   </td>
                            	<td width="230" >:&nbsp;&nbsp;<? if($delivery_date!="0000-00-00" || $delivery_date!="") echo change_date_format($delivery_date); else echo "";?>    </td>
                            </tr>

                        </table>
                    </td>


                </tr>
				<tr>
					<td width="350"></td>
					<td width="350"  style="font-size:22px;color:red;font-weight:bold;text-align:right;"><? if ($is_approved==1) echo "Approved"; else if ($is_approved==3) echo "Partial Approved"; ?></td>
				</tr>

            </table>
        </br>

        <?
        $buyer_sample=return_library_array( "select id, buyer_name from  sample_development_mst",'id','buyer_name');
        $sql="select a.id, a.ydw_no,b.job_no_id,b.sample_name,b.id as dtls_id,b.job_no
        from
        wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
        where
        a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id";
					//echo $sql;
        $sql_result=sql_select($sql);$total_samp_deve_id="";$total_buyer="";$total_dtls_id='';$total_sample_name='';
        foreach($sql_result as $row)
        {
        	if($total_dtls_id=='') $total_dtls_id=$row[csf("dtls_id")]; else $total_dtls_id=$total_dtls_id.",".$row[csf("dtls_id")];

        	if($total_samp_deve_id=="") $total_samp_deve_id=$row[csf("job_no_id")]; else $total_samp_deve_id=$total_samp_deve_id.",".$row[csf("job_no_id")];

        	if($total_buyer=="") $total_buyer=$buyer_sample[$row[csf('job_no_id')]]; else $total_buyer=$total_buyer.",".$buyer_sample[$row[csf('job_no_id')]];

        	if($total_sample_name=="") $total_sample_name=$row[csf("sample_name")]; else $total_sample_name=$total_sample_name.",".$row[csf("sample_name")];

        }

        ?>


        <table width="1050" style="" align="center" border="1"  cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
        	<thead>
        		<tr>
        			<th width="30" align="center"><strong>Sl</strong></th>
        			<th width="100" align="center"><strong>Sales Order</strong></th>
        			<th width="100" align="center"><strong>Booking No.</strong></th>
        			<th width="60" align="center"><strong>Lot</strong></th>
        			<th width="30" align="center"><strong>Yarn Count</strong></th>
        			<th width="160" align="center"><strong>Yarn Description</strong></th>
        			<th width="60" align="center"><strong>Brand</strong></th>

        			<th width="60" align="center"><strong>WO Qty</strong></th>
        			<?
        			if($show_val_column == 1)
        			{
        				?>
        				<th width="50" align="right"><strong>Rate</strong></th>

        			<th width="80" align="right"><strong>Amount</strong></th>
					<?
        			}
        			?>
        			<th width="80" align="right"><strong>No of Bag</strong></th>
        			<th width="80" align="right"><strong>No of Cone</strong></th>
        			<th  align="center" width="60" ><strong>Min Req. Cone</strong></th>
        			<th  align="left" ><strong>Remarks </strong></td>
        			</tr>
        		</thead>
        		<?

        		$sql_brand=sql_select("select id,lot,brand from product_details_master where status_active=1");
        		foreach($sql_brand as $row_barand)
        		{
        			$product_lot[$row_barand[csf("id")]]['lot']=$row_barand[csf("lot")];
        			$product_lot[$row_barand[csf("id")]]['brand']=$row_barand[csf("brand")];
        		}

        		if($db_type==0) $select_f_grp="group by yarn_color, color_range,
        		id,product_id,job_no,job_no_id,yarn_description,count,dyeing_charge,min_require_cone,referance_no,no_of_cone,no_of_bag, remarks order by id ";
        		else if($db_type==2) $select_f_grp="group by yarn_color, color_range,
        		id,product_id,job_no,job_no_id,yarn_description,count,dyeing_charge,min_require_cone,referance_no,no_of_cone,no_of_bag, remarks order by id ";

			/* $sql_color="select id,product_id,job_no,no_of_bag,no_of_cone,job_no_id,yarn_color,yarn_description,count,color_range,sum(yarn_wo_qty) as yarn_wo_qty,dyeing_charge,sum(amount) as amount,min_require_cone,referance_no, remarks
			from
					wo_yarn_dyeing_dtls, fabric_sales_order_mst c
			where
			b.job_no_id = c.id and status_active=1 and id in($total_dtls_id) $select_f_grp ";*/

			$sql_color="SELECT a.id,a.mst_id,a.product_id,a.job_no,a.no_of_bag,a.no_of_cone,a.job_no_id,a.yarn_color,a.yarn_description,a.count,a.color_range,sum(a.yarn_wo_qty) as yarn_wo_qty,a.dyeing_charge,sum(a.amount) as amount,
			a.min_require_cone,a.referance_no, a.remarks, b.sales_booking_no from wo_yarn_dyeing_dtls a, fabric_sales_order_mst b
			where a.job_no_id = b.id and a.status_active=1 and a.id in($total_dtls_id) group by a.yarn_color, a.color_range, a.id,a.mst_id,a.product_id,a.job_no,a.job_no_id,a.yarn_description,
			a.count,a.dyeing_charge,a.min_require_cone,a.referance_no,a.no_of_cone,a.no_of_bag, a.remarks ,b.sales_booking_no
			order by a.id";



			//echo $sql_color;die;
			$sql_result=sql_select($sql_color);$total_qty=0;$total_amount=0;$i=1;$buyer=0;$order_no="";
			foreach($sql_result as $row)
			{
				$mstID=$row[csf("mst_id")];
				$product_id=$row[csf("product_id")];
				//var_dump($product_id);
				if($row[csf("product_id")]!="")
				{
					$lot_amt=$product_lot[$row[csf("product_id")]]['lot'];
					$brand=$product_lot[$row[csf("product_id")]]['brand'];
				}

			//$yarn_count_des=explode(" ",$row[csf("yarn_description")]);

				if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";


				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center"><? echo $i; ?></td>
					<td align="center"><? echo $row[csf("job_no")]; ?></td>
					<td align="center"><? echo $row[csf("sales_booking_no")]; ?></td>
					<td align="center"><? echo $lot_amt; ?></td>
					<td align="center"><? echo $count_arr[$row[csf("count")]]; //$count_arr[$row[csf("count")]]; ?></td>
					<td>
						<?
						echo $row[csf("yarn_description")];
						?>
					</td>
					<td><? echo $brand_arr[$brand]; ?></td>

					<td align="right"><? echo $row[csf("yarn_wo_qty")]; $total_qty+=$row[csf("yarn_wo_qty")]; ?>&nbsp;</td>
					<?
					if($show_val_column == 1)
					{
						?>
						<td align="right"><? echo $row[csf("dyeing_charge")]; ?>&nbsp;</td>

					<td align="right"><? echo number_format($row[csf("amount")],2);  $total_amount+=$row[csf("amount")];?></td>
					<?
					}
					?>
					<td align="right"><? echo $row[csf("no_of_bag")]; ?></td>
					<td align="right"><? echo $row[csf("no_of_cone")]; ?></td>
					<td align="right"><? echo $row[csf("min_require_cone")]; ?></td>
					<td align="left"><? echo $row[csf("remarks")]; ?></td>
				</tr>
				<?
				$i++;
				$yarn_count_des="";
				$style_no="";
			}
			?>
			<tr>
				<td colspan="7" align="right"><strong>Total:</strong>&nbsp;&nbsp;</td>
				<td align="right" ><b><? echo number_format($total_qty,2,".",""); ?></b></td>

				<?
				if($show_val_column == 1)
				{
					?>
					<td align="right">&nbsp;</td>
					<?
				}
				?>
				<td align="right"><b><? echo number_format($total_amount,2); ?></b></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<?
			$mcurrency="";
			$dcurrency="";
			if($currency_id==1)
			{
				$mcurrency='Taka';
				$dcurrency='Paisa';
			}
			if($currency_id==2)
			{
				$mcurrency='USD';
				$dcurrency='CENTS';
			}
			if($currency_id==3)
			{
				$mcurrency='EURO';
				$dcurrency='CENTS';
			}
			?>
			<tr>
				<td colspan="14" align="center">Total Dyeing Amount (in word): &nbsp;<?  echo number_to_words(def_number_format($total_amount,2,""),$mcurrency, $dcurrency);?> </td>
			</tr>
		</table>

		<br><br>
		<table class="mainTable" width="450" style="" align="center" border="1"  cellpadding="0" cellspacing="0" rules="all">
			<caption><b> Combo Details</b> </caption>
			<?
			if($db_type	==0) $ys_cond = " and b.id =c.dtls_id";
			else $ys_cond = "  cast (c.dtls_id as varchar(4000))= b.id";

			$sql_yarn_qnty= sql_select("select id,yarn_wo_qty as yarn_wo_qty from wo_yarn_dyeing_dtls where mst_id=$mstID and status_active=1 and is_deleted=0");
			foreach ($sql_yarn_qnty as $row) {
				$yarn_qnty_arr[$row[csf("id")]]["yarn_wo_qty"]+=$row[csf("yarn_wo_qty")];
			}

			$sql_color_dtls="select sum(b.yarn_wo_qty) as yarn_wo_qty,sum(b.amount) as amount,b.yarn_color,c.yarn_count,c.yarn_color,c.yarn_comp,c.yarn_type,c.dtls_id
			from
			wo_yarn_dyeing_dtls b,wo_yarn_dyeing_dtls_fin_prod c
			where  b.mst_id= c.mst_id and b.status_active=1 and c.status_active=1 and b.id in($total_dtls_id) group by b.yarn_color,c.yarn_count,c.yarn_color,c.yarn_comp,c.yarn_type,c.dtls_id ";
			//echo $sql_color;die;
			$dtls_result=sql_select($sql_color_dtls);
			?>
			<tr>
				<td width="30" align="center"><strong>Sl</strong></td>
				<td width="180" align="center"><strong>Product Name</strong></td>
				<td width="100" align="center"><strong>Color</strong></td>
				<td width="80" align="center"><strong>Qnty</strong></td>
			</tr>
			<?
			$total_color_qty=0;$d=1;
			foreach($dtls_result as $row)
			{
				$dtlsID=explode(",", $row[csf("dtls_id")]);
				$amount_dtls=0;
				foreach ($dtlsID as $rows) {
					$amount_dtls+=$yarn_qnty_arr[$rows]["yarn_wo_qty"];
				}



				if ($d%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";//yarn_comp
				$product_name=$count_arr[$row[csf("yarn_count")]].','.$composition[$row[csf("yarn_comp")]].','.$yarn_type[$row[csf("yarn_type")]];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center"><? echo $d; ?></td>
					<td align="center"><? echo $product_name; //$count_arr[$row[csf("count")]]; ?></td>
					<td>
						<?
						echo $color_arr[$row[csf("yarn_color")]];
						?>
					</td>
					<td align="right"><? echo $amount_dtls;// $row[csf("yarn_wo_qty")]; ?></td>

				</tr>
				<?
				$d++;
				$total_color_qty+= $amount_dtls;
			}
			?>
			<tr>
				<td colspan="3" align="right"><strong>Total:</strong>&nbsp;&nbsp;</td>
				<td align="right" ><b><? echo $total_color_qty; ?></b></td>
			</tr>

		</table>

		<!--==============================================AS PER GMTS COLOR START=========================================  -->
		<table width="1050" style="" align="center"><tr><td><strong>Note:</strong></td></tr></table>

		<? echo get_spacial_instruction($work_order,"100%",94);?>

	</div>
	<div>
		<?
		$appSql = "select a.APPROVED_BY, a.APPROVED_DATE,b.USER_FULL_NAME,c.CUSTOM_DESIGNATION from approval_mst a,USER_PASSWD b,LIB_DESIGNATION c where a.mst_id =$update_id and a.APPROVED_BY=b.id and b.DESIGNATION=c.id and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0
		union all
		select b.id as APPROVED_BY, a.INSERT_DATE as APPROVED_DATE,b.USER_FULL_NAME,c.CUSTOM_DESIGNATION from wo_yarn_dyeing_mst a,USER_PASSWD b,LIB_DESIGNATION c where a.id =$update_id and a.INSERTED_BY=b.id and b.DESIGNATION=c.id and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0";
		//echo $appSql;die;
		$appSqlRes=sql_select($appSql);
		$userDtlsArr=array();
		foreach($appSqlRes as $row){
			$userDtlsArr[$row['APPROVED_BY']]="<div><b>".$row['USER_FULL_NAME']."</b></div><div><b>".$row['CUSTOM_DESIGNATION']."</b></div><div><small>".$row['APPROVED_DATE']."</small></div>";
		}
		echo get_app_signature(122, $cbo_company_name, "950px",$template_id, 50,$inserted_by,$userDtlsArr);
		//echo signature_table(122, $cbo_company_name, "950px");
		echo "****".custom_file_name($txt_booking_no,$style_sting,$txt_job_no);
		?>
	</div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		fnc_generate_Barcode('<? echo $varcode_work_order_no; ?>','barcode_img_id');
	</script>
	<?
	exit();
}

//==============================================end============

if ($action=="load_drop_down_sample")
{
	//echo create_drop_down( "cbo_buyer_name", 145, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );

	$sql="Select b.id, b.sample_name from  lib_sample b,  sample_development_dtls a where a.sample_name=b.id and b.status_active=1 and a.sample_mst_id=$data";
	echo create_drop_down( "cbo_sample_name", 70, $sql,"id,sample_name", 1, "-select-", $selected,"","0" );
	exit();
}

if($action=="dyeing_search_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$company=str_replace("'","",$company);
	?>
	<script>
		function js_set_value(str)
		{
			$("#hidden_rate").val(str);
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<div align="center" style="width:590px;" >
			<fieldset>
				<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
					<table width="570" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
						<thead>
							<tr>
								<th width="40">Sl No.</th>
								<th width="170">Const. Compo.</th>
								<th width="100">Process Name</th>
								<th width="100">Color</th>
								<th width="90">Rate</th>
								<th>UOM</th>
							</tr>
						</thead>
					</table>
					<?
					$sql="select id,comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,status_active from lib_subcon_charge where comapny_id=$company and process_id=30 and status_active=1";
					//echo $sql;
					$sql_result=sql_select($sql);
					?>
					<div style="width:570px; overflow-y:scroll; max-height:240px;font-size:12px; overflow-x:hidden; cursor:pointer;">
						<table width="570" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" id="table_charge">
							<tbody>
								<?
								$i=1;
								foreach($sql_result as $row)
								{
									if ($i%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<?  echo $bgcolor; ?>" onClick="js_set_value(<? echo $row[csf("in_house_rate")]; ?>)">
										<td width="40" align="center"><? echo $i;  ?></td>
										<td width="170"><? echo $row[csf("const_comp")]; ?></td>
										<td width="100" align="center"><? echo $conversion_cost_head_array[$row[csf("process_id")]]; ?></td>
										<td width="100"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
										<td width="90" align="right"><? echo number_format($row[csf("in_house_rate")],2); ?></td>
										<td><? echo $unit_of_measurement[$row[csf("uom_id")]]; ?></td>
									</tr>
									<?
									$i++;
								}
								?>
								<input type="hidden" id="hidden_rate" />
							</tbody>
						</table>
					</div>
				</form>
			</fieldset>
		</div>
	</body>
	<script  type="text/javascript">setFilterGrid("table_charge",-1)</script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>

	<?
	exit();
}

if($action=="lot_search_popup23")//Old
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$brand_arr=return_library_array( "Select id, brand_name from  lib_brand where  status_active=1",'id','brand_name');
	extract($_REQUEST);
	$company=str_replace("'","",$company);
	$job_no=str_replace("'","",$job_no);
	//echo $job_no;die;
	?>
	<script>
		function js_set_value(str)
		{
			$("#hidden_product").val(str);
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<div style="width:595px;" >
			<fieldset>
				<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
					<table width="595" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
						<thead>
							<tr>
								<th width="40">Sl No.</th>
								<th width="100">Lot No</th>
								<th width="90">Brand</th>
								<th width="200">Product Name Details</th>
								<th width="80">Stock</th>
								<th>UOM</th>
							</tr>
						</thead>
					</table>
					<?

					$sql="select id,product_name_details,lot,item_code,unit_of_measure,yarn_count_id,brand,current_stock,yarn_comp_type1st,yarn_comp_percent1st,yarn_comp_type2nd,yarn_comp_percent2nd,yarn_type,color from product_details_master where company_id='$company' and item_category_id=1";

						//echo $sql;
					$sql_result=sql_select($sql);
					?>
					<div style="width:595px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;">
						<table width="595" cellspacing="0" cellpadding="0" border="1" class="rpt_table" id="table_charge" style="cursor:pointer" rules="all">
							<tbody>
								<?
								$i=1;
								foreach($sql_result as $row)
								{
									if ($i%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $composition[$row[csf("yarn_comp_type1st")]]." ". $row[csf("yarn_comp_percent1st")]." ". $composition[$row[csf("yarn_comp_type2nd")]]." ". $row[csf("yarn_comp_percent2nd")]." ". $yarn_type[$row[csf("yarn_type")]]." ". $color_arr[$row[csf("color")]]; ?>,<? echo $row[csf("yarn_count_id")]; ?>,<? echo $row[csf("lot")]; ?>,<? echo $row[csf("id")]; ?>')">
										<td width="40" align="center"><p><? echo $i;  ?></p></td>
										<td width="100" align="center"><p><? echo $row[csf("lot")]; ?></p></td>
										<td width="90"><p><? echo $brand_arr[$row[csf("brand")]]; ?></p></td>
										<td width="200">v<? echo $row[csf("product_name_details")]; ?></p></td>
										<td width="80" align="right"><p><? echo $row[csf("current_stock")]; ?></p></td>
										<td><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td>
									</tr>
									<?
									$i++;
								}
								?>
								<input type="hidden" id="hidden_product" style="width:200px;" />
							</tbody>
						</table>
					</div>
				</form>
			</fieldset>
		</div>
	</body>
	<script  type="text/javascript">setFilterGrid("table_charge",-1)</script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
		//}
	exit();
}

if($action=="show_with_multiple_job")
{
	//echo "xxxx";die;
	extract($_REQUEST);
	$form_name=str_replace("'","",$form_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$update_id=str_replace("'","",$update_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<div style="width:900px" align="center">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
			<tr>
				<td width="750">
					<table width="100%" cellpadding="0" cellspacing="0" >
						<tr>
							<td align="center" style="font-size:20px;">
								<?php
echo $company_library[$cbo_company_name];
?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:14px">
								<?
								$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
								foreach ($nameArray as $result)
								{
									?>

									Email Address: <? echo $result[csf('email')];?>
									Website No: <? echo $result[csf('website')];
								}

								?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:20px">
								<strong>Yarn Dyeing Work Order </strong>
							</td>
						</tr>
					</table>
				</td>
				<td width="250" id="barcode_img_id">

				</td>
			</tr>
		</table>
		<?
		//echo "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency from wo_yarn_dyeing_mst a where a.id=$update_id";
		$nameArray=sql_select( "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency from wo_yarn_dyeing_mst a where a.id=$update_id");
		foreach ($nameArray as $result)
		{
			$work_order=$result[csf('ydw_no')];
			$supplier_id=$result[csf('supplier_id')];
			$booking_date=$result[csf('booking_date')];
			$currency=$result[csf('currency')];
			$attention=$result[csf('attention')];
			$delivery_date=$result[csf('delivery_date')];
			$delivery_date_end=$result[csf('delivery_date_end')];
			$dy_delivery_start=$result[csf('dy_delivery_date_start')];
			$dy_delivery_end=$result[csf('dy_delivery_date_end')];
			$currency_id=$result[csf('currency')];

		}
		$varcode_work_order_no=$work_order;

		?>
		<table width="900" style="" align="center">
			<tr>
				<td width="350"  style="font-size:12px">
					<table width="350" style="" align="left">
						<tr  >
							<td   width="120"><b>To</b>   </td>
							<td width="230">:&nbsp;&nbsp;<? echo $supplier_arr[$supplier_id];?></td>
						</tr>

						<tr>
							<td  ><b>Wo No.</b>   </td>
							<td  >:&nbsp;&nbsp;<? echo $work_order;?>    </td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Attention</b></td>
							<td >:&nbsp;&nbsp;<? echo $attention; ?></td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Booking Date</b></td>
							<td >:&nbsp;&nbsp;<? if($booking_date!="0000-00-00" || $booking_date!="") echo change_date_format($booking_date); else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>Currency</b></td>
							<td >:&nbsp;&nbsp;<? echo $currency[$currency_val]; ?></td>
						</tr>
					</table>
				</td>
				<td width="350"  style="font-size:12px">
					<table width="350" style="" align="left">
						<tr>
							<td  width="120"><b>G/Y Issue Start</b>   </td>
							<td width="230" >:&nbsp;&nbsp;<? if($delivery_date!="0000-00-00" || $delivery_date!="") echo change_date_format($delivery_date); else echo "";?>    </td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>G/Y Issue End</b></td>
							<td >:&nbsp;&nbsp;<? if($delivery_date_end!="0000-00-00" || $delivery_date_end!="") echo change_date_format($delivery_date_end);  else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery Start </b></td>
							<td >:&nbsp;&nbsp;<? if($dy_delivery_start!="0000-00-00" || $dy_delivery_start!="") echo change_date_format($dy_delivery_start); else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery End</b></td>
							<td >:&nbsp;&nbsp;<? if($dy_delivery_end!="0000-00-00" || $dy_delivery_end!="") echo change_date_format($dy_delivery_end); else echo "";?></td>
						</tr>
					</table>
				</td>
				<td width="200"  style="font-size:12px">
					<?
					$image_location=return_field_value("image_location","common_photo_library","master_tble_id='$update_id' and form_name='$form_name'","image_location");
					?>
					<img src="<? echo '../../'.$image_location; ?>" width="120" height="100" border="2" />
				</td>
			</tr>
		</table>
	</br>

	<table width="1080" style="" align="center" border="1"  cellpadding="0" cellspacing="0" rules="all">
		<tr>
			<td width="30" align="center"><strong>Sl</strong></td>
			<td width="65" align="center"><strong>Color</strong></td>
			<td width="80" align="center"><strong>Color Range</strong></td>
			<td  align="center" width="50"><strong>Ref No.</strong></td>
			<td  align="center" width="70"><strong>Style Ref.No.</strong></td>
			<td width="30" align="center"><strong>Yarn Count</strong></td>
			<td width="140" align="center"><strong>Yarn Description</strong></td>
			<td width="60" align="center"><strong>Brand</strong></td>
			<td width="50" align="center"><strong>Lot</strong></td>
			<td width="60" align="center"><strong>WO Qty</strong></td>
			<td width="50" align="center"><strong>Dyeing Rate</strong></td>
			<td width="80" align="center"><strong>Amount</strong></td>
			<td  align="center" width="50"><strong>Min Req. Cone</strong></td>
			<td  align="center" width="80"><strong>Sample Develop Id</strong></td>
			<td  align="center" width="80"><strong>Buyer</strong></td>
			<td  align="center" ><strong>Sample Name</strong></td>
		</tr>
		<?
		$sql_brand=sql_select("select id,lot,brand from product_details_master where status_active=1");
		foreach($sql_brand as $row_barand)
		{
			$product_lot[$row_barand[csf("id")]]['lot']=$row_barand[csf("lot")];
			$product_lot[$row_barand[csf("id")]]['brand']=$row_barand[csf("brand")];
		}
		$buyer_sample=return_library_array( "select id, buyer_name from  sample_development_mst",'id','buyer_name');
		$style_ref_sample=return_library_array( "select id, style_ref_no from  sample_development_mst",'id','style_ref_no');

		if($db_type==0)
		{
			$sql="select a.id, a.ydw_no,b.id as dtls_id,b.product_id,b.job_no,b.job_no_id,b.sample_name,b.yarn_color,b.yarn_description,b.count,b.color_range,sum(b.yarn_wo_qty) as yarn_wo_qty,b.dyeing_charge,sum(b.amount) as amount,b.min_require_cone,b.referance_no
			from
			wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
			where
			a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id
			group by b.count, b.job_no_id, b.yarn_color, b.color_range
			order by b.id";
		}
		else if($db_type==2)
		{
			$sql="select a.id, a.ydw_no,b.id as dtls_id,b.product_id,b.job_no,b.job_no_id, listagg(CAST(b.sample_name AS VARCHAR(4000)),',')  within group (order by b.sample_name) as sample_name,b.yarn_color,b.yarn_description,b.count,b.color_range,sum(b.yarn_wo_qty) as yarn_wo_qty,b.dyeing_charge,sum(b.amount) as amount,b.min_require_cone,b.referance_no
			from
			wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
			where
			a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id
			group by b.job_no_id, b.yarn_color, b.color_range,a.id,b.product_id,b.job_no,b.job_no_id,b.yarn_color,b.yarn_description,b.count,b.color_range,b.dyeing_charge,b.min_require_cone,b.referance_no, a.ydw_no,b.id
			order by b.id";
		}

			//echo $sql;die;
		$sql_result=sql_select($sql);$total_qty=0;$total_amount=0;$i=1;$buyer=0;$order_no="";
		foreach($sql_result as $row)
		{
			$product_id=$row[csf("product_id")];
			if($row[csf("product_id")]!="")
			{
				$lot_amt=$product_lot[$row[csf("product_id")]]['lot'];
				$brand=$product_lot[$row[csf("product_id")]]['brand'];
			}

			if ($i%2==0)
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";

			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td align="center"><? echo $i; ?></td>
				<td><? echo $color_arr[$row[csf("yarn_color")]]; ?></td>
				<td><? echo $color_range[$row[csf("color_range")]]; ?></td>
				<td align="center"><? echo $row[csf("referance_no")]; ?></td>
				<td align="center">
					<?
					echo $style_ref_sample[$row[csf("job_no_id")]];
					?>
				</td>
				<td align="center"><? echo $count_arr[$row[csf("count")]]; //$count_arr[$row[csf("count")]]; ?></td>
				<td>
					<?
					echo $row[csf("yarn_description")];
					?>
				</td>
				<td><? echo $brand_arr[$brand]; ?></td>
				<td align="center"><? echo $lot_amt; ?></td>
				<td align="right"><? echo $row[csf("yarn_wo_qty")]; $total_qty+=$row[csf("yarn_wo_qty")]; ?>&nbsp;</td>
				<td align="right"><? echo $row[csf("dyeing_charge")]; ?>&nbsp;</td>
				<td align="right"><? echo number_format($row[csf("amount")],2);  $total_amount+=$row[csf("amount")];?> &nbsp;</td>
				<td align="center"><? echo $row[csf("min_require_cone")]; ?></td>
				<td align="center"><? echo $row[csf("job_no_id")]; ?></td>
				<td align="center"><? echo $buyer_arr[$buyer_sample[$row[csf("job_no_id")]]]; ?> </td>
				<td align="center">
					<?
					$sample_name_arr=array_unique(explode(",",$row[csf('sample_name')]));
					$sample_name_group="";
					foreach($sample_name_arr as $val)
					{
						if($sample_name_group=="") $sample_name_group=$sample_arr[$val]; else $sample_name_group=$sample_name_group.",".$sample_arr[$val];
					}
					echo $sample_name_group;
					?>
				</td>
			</tr>
			<?
			$i++;
		}
		?>
		<tr>
			<td colspan="9" align="right"><strong>Total:</strong>&nbsp;&nbsp;</td>
			<td align="right" ><b><? echo $total_qty; ?></b></td>
			<td align="right">&nbsp;</td>
			<td align="right"><b><? echo number_format($total_amount,2); ?></b></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<?
		$mcurrency="";
		$dcurrency="";
		if($currency_id==1)
		{
			$mcurrency='Taka';
			$dcurrency='Paisa';
		}
		if($currency_id==2)
		{
			$mcurrency='USD';
			$dcurrency='CENTS';
		}
		if($currency_id==3)
		{
			$mcurrency='EURO';
			$dcurrency='CENTS';
		}
		?>
		<tr>
			<td colspan="16" align="center">Total Dyeing Amount (in word): &nbsp;<? echo number_to_words(def_number_format($total_amount,2,""),$mcurrency, $dcurrency); //echo number_to_words($total_amount,"USD", "CENTS");?> </td>
		</tr>
	</table>


	<!--==============================================AS PER GMTS COLOR START=========================================  -->
	<table width="1080" style="" align="center"><tr><td><strong>Note:</strong></td></tr></table>
	<? echo get_spacial_instruction($work_order,"100%",94);?>

	</div>
	<div>
		<?
		echo signature_table(122, $cbo_company_name, "1080px");
		echo "****".custom_file_name($txt_booking_no,$style_sting,$txt_job_no);
		?>
	</div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		fnc_generate_Barcode('<? echo $varcode_work_order_no; ?>','barcode_img_id');
	</script>
	<?
	exit();
}

if($action=="show_without_rate_booking_report")
{
	//echo "uuuu";die;
	extract($_REQUEST);
	$form_name=str_replace("'","",$form_name);
	$cbo_service_type=str_replace("'","",$cbo_service_type);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$update_id=str_replace("'","",$update_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$supplier_name=return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$pay_mode = str_replace("'","",$cbo_pay_mode);
	$new_supplier_name = str_replace("'","",$cbo_supplier_name);

	?>
		<style type="text/css">
			table.mainTable tr td {
				border: 1px solid #000;
			}

		</style>
		<div style="width:900px" align="center">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
				<tr>
					<td width="700">
						<table width="100%" cellpadding="0" cellspacing="0" >
							<tr>
								<td align="center" style="font-size:20px;">
									<?php
echo "<b>$company_library[$cbo_company_name]</b>";
?>
								</td>
							</tr>
							<tr>
								<td align="center" style="font-size:14px">
									<?
									$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
									foreach ($nameArray as $result)
									{
										?>

										Email Address: <? echo $result[csf('email')];?>
										Website No: <? echo $result[csf('website')];
									}

									?>
								</td>
							</tr>
							<tr>
								<td align="center" style="font-size:20px">
									<strong style="font-size: 16px"><? echo "Yarn ". $yarn_issue_purpose[$cbo_service_type];?> Work Order </strong>
								</td>
							</tr>
						</table>
					</td>
					<td width="250" id="barcode_img_id">

					</td>
				</tr>
			</table>
			<?
			//echo "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention  from wo_yarn_dyeing_mst a where a.id=$update_id";die;
			$nameArray=sql_select( "SELECT a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.ref_no,a.currency,b.job_no,a.is_sales, b.fab_booking_no from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.id=$update_id");
			foreach ($nameArray as $result)
			{
				$work_order=$result[csf('ydw_no')];
				$supplier_id=$result[csf('supplier_id')];
				$booking_date=$result[csf('booking_date')];
				$currency_val=$result[csf('currency')];
				$attention=$result[csf('attention')];
				$delivery_date=$result[csf('delivery_date')];
				$delivery_date_end=$result[csf('delivery_date_end')];
				$dy_delivery_start=$result[csf('dy_delivery_date_start')];
				$dy_delivery_end=$result[csf('dy_delivery_date_end')];
				$currency_id=$result[csf('currency')];
				$job_no=$result[csf('job_no')];
				$ref_no=$result[csf('ref_no')];
				$is_sales=$result[csf('is_sales')];
				$fab_booking_no=$result[csf('fab_booking_no')];
			}
			$internal_ref_arr=array();
			if($is_sales==2 && $job_no!='')
			{
				$po_dtls_data=sql_select("SELECT job_no_mst,grouping,file_no from wo_po_break_down where job_no_mst='$job_no' and status_active=1 and is_deleted=0");
				foreach ($po_dtls_data as $row) {
					$internal_ref_arr[$row[csf('job_no_mst')]]['grouping'][] =$row[csf('grouping')];
					$internal_ref_arr[$row[csf('job_no_mst')]]['file_no'][] =$row[csf('file_no')];
				}
			}

			if($is_sales==1 && $fab_booking_no!='')
			{
				$sales_info = sql_select("SELECT a.po_break_down_id, a.booking_no, b.grouping, b.file_no from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no='$fab_booking_no' group by a.po_break_down_id, a.booking_no, b.grouping, b.file_no order by b.file_no ");

				foreach ($sales_info as $row)
				{
					$internal_ref_arr[$row[csf('booking_no')]]['grouping'][] = $row[csf('grouping')];
					$internal_ref_arr[$row[csf('booking_no')]]['file_no'][] = $row[csf('file_no')];
				}
			}

			$varcode_work_order_no=$work_order;
			?>
			<table width="920" style="" align="center">
				<tr>
					<td width="350"  style="font-size:12px">
						<table width="350" style="" align="left">
							<tr  >
								<td   width="120"><b>To</b>   </td>
								<td width="230">:&nbsp;&nbsp;<b><?
								if($pay_mode==3 || $pay_mode==5)
								{
									echo $company_library[$new_supplier_name];
								}
								else
								{
									echo $supplier_name[$new_supplier_name];
								}

	                               // echo $supplier_arr[$supplier_id];

								?></b></td>
							</tr>

							<tr>
								<td  ><b>Wo No.</b>   </td>
								<td  >:&nbsp;&nbsp;<b><? echo $work_order;?></b></td>
							</tr>
							<tr>
								<td style="font-size:12px"><b>Internal Ref. No</b></td>
								<td >:&nbsp;&nbsp;
									<?
										if($is_sales==1)
										{
											echo implode(",", array_unique($internal_ref_arr[$fab_booking_no]['grouping']));
										}
										else{
										echo implode(",", array_unique($internal_ref_arr[$job_no]['grouping']));
										}
									?>
								</td>
							</tr>
							<tr>
								<td style="font-size:12px"><b>File No</b></td>
								<td >:&nbsp;&nbsp;
								<?
									if($is_sales==1)
									{
										echo implode(",", array_unique($internal_ref_arr[$fab_booking_no]['file_no']));
									}
									else
									{
										echo implode(",", array_unique($internal_ref_arr[$job_no]['file_no']));
									}

								?>
								</td>
							</tr>
							<tr>
								<td style="font-size:12px"><b>Attention</b></td>
								<td >:&nbsp;&nbsp;<? echo $attention; ?></td>
							</tr>
						</table>
					</td>
					<td width="370" style="font-size:12px">
						<table width="370" style="" align="left">
							<tr>
								<td  width="120"><b>Job No.</b>   </td>
								<td width="120" >:&nbsp;&nbsp;<? echo $job_no;?></td>

								<td  width="140"><b>Ref. No</b>   </td>
								<td width="240" colspan="2" >:&nbsp;&nbsp;<? echo $ref_no;?></td>
							</tr>

							<tr>
								<td style="font-size:12px"><b>Currency</b></td>
								<td >:&nbsp;&nbsp;<? echo $currency[$currency_val]; ?></td>
							</tr>
							<tr>
								<td style="font-size:12px"><b>Booking Date</b></td>
								<td >:&nbsp;&nbsp;<? if($booking_date!="0000-00-00" || $booking_date!="") echo change_date_format($booking_date); else echo ""; ?></td>
							</tr>
							<tr>
								<td  width="120"><b>Delivery Date</b>   </td>
								<td width="230" >:&nbsp;&nbsp;<? if($delivery_date!="0000-00-00" || $delivery_date!="") echo change_date_format($delivery_date); else echo "";?>    </td>
							</tr>

						</table>
					</td>


				</tr>
			</table>
		</br>

		<?
		$buyer_sample=return_library_array( "select id, buyer_name from  sample_development_mst",'id','buyer_name');
		$sql="select a.id, a.ydw_no,b.job_no_id,b.sample_name,b.id as dtls_id
		from
		wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
		where
		a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id";
				//echo $sql;
		$sql_result=sql_select($sql);$total_samp_deve_id="";$total_buyer="";$total_dtls_id='';$total_sample_name='';
		foreach($sql_result as $row)
		{
			if($total_dtls_id=='') $total_dtls_id=$row[csf("dtls_id")]; else $total_dtls_id=$total_dtls_id.",".$row[csf("dtls_id")];

			if($total_samp_deve_id=="") $total_samp_deve_id=$row[csf("job_no_id")]; else $total_samp_deve_id=$total_samp_deve_id.",".$row[csf("job_no_id")];

			if($total_buyer=="") $total_buyer=$buyer_sample[$row[csf('job_no_id')]]; else $total_buyer=$total_buyer.",".$buyer_sample[$row[csf('job_no_id')]];

			if($total_sample_name=="") $total_sample_name=$row[csf("sample_name")]; else $total_sample_name=$total_sample_name.",".$row[csf("sample_name")];

		}
		?>
		<table class="mainTable" width="920" style="" align="center" border="1"  cellpadding="0" cellspacing="0" rules="all">
			<tr>
				<td width="30" align="center"><strong>Sl</strong></td>
				<td width="60" align="center"><strong>Lot</strong></td>
				<td width="30" align="center"><strong>Yarn Count</strong></td>
				<td width="160" align="center"><strong>Yarn Description</strong></td>
				<td width="60" align="center"><strong>Brand</strong></td>

				<td width="60" align="center"><strong>WO Qty</strong></td>
				<td width="80" align="center"><strong>No of Bag</strong></td>
				<td width="80" align="center"><strong>No of Cone</strong></td>
				<td  align="center" width="60"><strong>Min Req. Cone</strong></td>
				<td  align="center" ><strong>Remarks</strong></td>
			</tr>
			<?

			$sql_brand=sql_select("select id,lot,brand from product_details_master where status_active=1");
			foreach($sql_brand as $row_barand)
			{
				$product_lot[$row_barand[csf("id")]]['lot']=$row_barand[csf("lot")];
				$product_lot[$row_barand[csf("id")]]['brand']=$row_barand[csf("brand")];
			}

			if($db_type==0) $select_f_grp="group by mst_id,yarn_color, color_range,
			id,product_id,job_no,job_no_id,yarn_description,count,dyeing_charge,min_require_cone,referance_no,no_of_cone,no_of_bag, remarks order by id ";
			else if($db_type==2) $select_f_grp="group by mst_id,yarn_color, color_range,
			id,product_id,job_no,job_no_id,yarn_description,count,dyeing_charge,min_require_cone,referance_no,no_of_cone,no_of_bag, remarks order by id ";

			$sql_color="select id,mst_id,product_id,job_no,no_of_bag,no_of_cone,job_no_id,yarn_color,yarn_description,count,color_range,sum(yarn_wo_qty) as yarn_wo_qty,dyeing_charge,sum(amount) as amount,min_require_cone,referance_no, remarks
			from
			wo_yarn_dyeing_dtls
			where
			status_active=1 and id in($total_dtls_id) $select_f_grp ";
			$sql_result=sql_select($sql_color);$total_qty=0;$total_amount=0;$i=1;$buyer=0;$order_no="";
			$mstID="";
			foreach($sql_result as $row)
			{
				$mstID=$row[csf("mst_id")];
				$product_id=$row[csf("product_id")];

				if($row[csf("product_id")]!="")
				{
					$lot_amt=$product_lot[$row[csf("product_id")]]['lot'];
					$brand=$product_lot[$row[csf("product_id")]]['brand'];
				}
				if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center"><? echo $i; ?></td>
					<td align="right"><? echo $lot_amt; ?></td>
					<td align="right"><? echo $count_arr[$row[csf("count")]]; //$count_arr[$row[csf("count")]]; ?></td>
					<td>
						<?
						echo $row[csf("yarn_description")];
						?>
					</td>
					<td><? echo $brand_arr[$brand]; ?></td>

					<td align="right"><? echo number_format($row[csf("yarn_wo_qty")],2); $total_qty+=$row[csf("yarn_wo_qty")]; ?>&nbsp;</td>

					<td align="right"><? echo $row[csf("no_of_bag")]; ?></td>
					<td align="right"><? echo $row[csf("no_of_cone")]; ?></td>
					<td align="right"><? echo $row[csf("min_require_cone")]; ?></td>
					<td align="left"><? echo $row[csf("remarks")]; ?></td>
				</tr>
				<?
				$i++;
				$yarn_count_des="";
				$style_no="";
			}
			?>
			<tr>
				<td colspan="5" align="right"><strong>Total:</strong>&nbsp;&nbsp;</td>
				<td align="right" ><b><? echo number_format($total_qty,2); ?></b></td>
				<td align="right">&nbsp;</td>
				<td align="right"><b><? //echo number_format($total_amount,2); ?></b></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<?
			$mcurrency="";
			$dcurrency="";
			if($currency_id==1)
			{
				$mcurrency='Taka';
				$dcurrency='Paisa';
			}
			if($currency_id==2)
			{
				$mcurrency='USD';
				$dcurrency='CENTS';
			}
			if($currency_id==3)
			{
				$mcurrency='EURO';
				$dcurrency='CENTS';
			}
			?>
		</table>
		<br/>
		<table class="mainTable" width="450" style="" align="center" border="1"  cellpadding="0" cellspacing="0" rules="all">
			<caption><b> Combo Details</b> </caption>
			<?
			if($db_type	==0) $ys_cond = " and b.id =c.dtls_id";
			else $ys_cond = "  cast (c.dtls_id as varchar(4000))= b.id";
			$sql_color_dtls="select sum(b.yarn_wo_qty) as yarn_wo_qty,sum(b.amount) as amount,b.yarn_color,c.yarn_count,c.yarn_color,c.yarn_comp,c.yarn_type,c.dtls_id
			from
			wo_yarn_dyeing_dtls b,wo_yarn_dyeing_dtls_fin_prod c
			where  b.mst_id= c.mst_id and b.status_active=1 and c.status_active=1 and b.id in($total_dtls_id) group by b.yarn_color,c.yarn_count,c.yarn_color,c.yarn_comp,c.yarn_type,c.dtls_id ";

			$sql_yarn_qnty= sql_select("select id,yarn_wo_qty as yarn_wo_qty from wo_yarn_dyeing_dtls where mst_id=$mstID and status_active=1 and is_deleted=0");
			foreach ($sql_yarn_qnty as $row) {
				$yarn_qnty_arr[$row[csf("id")]]["yarn_wo_qty"]+=$row[csf("yarn_wo_qty")];
			}

			$dtls_result=sql_select($sql_color_dtls);
			?>
			<tr>
				<td width="30" align="center"><strong>Sl</strong></td>
				<td width="180" align="center"><strong>Product Name</strong></td>
				<td width="100" align="center"><strong>Color</strong></td>
				<td width="80" align="center"><strong>Qnty</strong></td>
			</tr>
			<?
			$total_color_qty=0;$d=1;
			foreach($dtls_result as $row)
			{
				$dtlsID=explode(",", $row[csf("dtls_id")]);
				$amount_dtls=0;
				foreach ($dtlsID as $rows) {
					$amount_dtls+=$yarn_qnty_arr[$rows]["yarn_wo_qty"];
				}
				if ($d%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";//yarn_comp
				$product_name=$count_arr[$row[csf("yarn_count")]].','.$composition[$row[csf("yarn_comp")]].','.$yarn_type[$row[csf("yarn_type")]];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center"><? echo $d; ?></td>
					<td align="center"><? echo $product_name; //$count_arr[$row[csf("count")]]; ?></td>
					<td>
						<?
						echo $color_arr[$row[csf("yarn_color")]];
						?>
					</td>
					<td align="right"><? echo  $amount_dtls; ?></td>

				</tr>
				<?
				$d++;
				$total_color_qty+= $amount_dtls;
			}
			?>
			<tr>
				<td colspan="3" align="right"><strong>Total:</strong>&nbsp;&nbsp;</td>
				<td align="right" ><b><? echo $total_color_qty; ?></b></td>
			</tr>

		</table>


		<!--==============================================AS PER GMTS COLOR START=========================================  -->
		<table width="920" style="" align="center"><tr><td><strong>Note:</strong></td></tr></table>

		<? echo get_spacial_instruction($work_order,"100%",94);?>
	</div>
	<div>
		<?
		echo signature_table(122, $cbo_company_name, "920px");
		echo "****".custom_file_name($txt_booking_no,$style_sting,$txt_job_no);
		?>
	</div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		fnc_generate_Barcode('<? echo $varcode_work_order_no; ?>','barcode_img_id');
	</script>
	<?
	exit();
}

if($action == "populate_field_level_access_data")
{
	list($page_id,$company_id) = explode("_", $data);
	$sql = "select a.field_name,a.is_disable,a.defalt_value from field_level_access a where a.page_id = '$page_id' and a.is_deleted = 0 and a.company_id = '$company_id' and a.user_id = '$user_id' " ;

	//echo $sql;
	$res = sql_select($sql);
	if(count($res) > 0)
	{
		foreach($res as $row)
		{
			$fieldName = $row[csf("field_name")];
			$isDisable = $row[csf("is_disable")];
			echo "$('#".$row[csf("field_name")]."').val(".$row[csf("defalt_value")].");\n";

			if($isDisable == 1)
			{
				echo "$('#".$row[csf("field_name")]."').attr('disabled','disabled');\n";
				echo "$('#cbo_with_order').val(1).attr('disabled','disabled');\n";
				echo "$('.job_field').attr('disabled','disabled').removeAttr('placeholder');\n";
				echo "$('#job_title').css('color','#444').removeClass('must_entry_caption').removeAttr('title').attr('disabled');\n";
			}
			else
			{
				if( ( $fieldName == 'cbo_is_sales_order' ) &&  ($isDisable ==2) )
				{
					echo "$('#job_title').css('color','blue').addClass('must_entry_caption').attr('Must Entry Field');\n";
					echo "$('.job_field').removeAttr('disabled').attr('placeholder','Doubole Click for Sales Order');\n";
				}
			}

			//for job title
			if($row[csf("field_name")] == 'cbo_is_sales_order')
			{
				echo "change_job_title(".$row[csf("defalt_value")].");\n";
				//echo "change_job_priority(2);\n";
			}
		}
	}
	else
	{
		echo "$('#cbo_is_sales_order').removeAttr('disabled','disabled');\n";
		echo "$('#cbo_is_sales_order').val('2');\n";
		echo "change_job_title(2);\n";
		echo "$('.job_field').removeAttr('disabled').attr('placeholder','Doubole Click for job');\n";
		echo "$('#cbo_with_order').removeAttr('disabled','disabled');\n";
		echo "change_job_priority(2);\n";

	}
	//echo "change_job_priority(2);\n";
	exit();
}

//actn_available_qty
if($action=="actn_available_qty")
{
	$exp_data = explode("*",$data);
	//for variable settings
	$arr = array();
	$arr['company_id'] = $exp_data[0];
	$arr['category_id'] = 1;
	$arr['vs_auto_allocation'] = 1;
	$data_vs = get_vs_allocated_qty($arr);
	//end

	$arr = array();
	$arr['product_id'] = $exp_data[3];
	$arr['is_auto_allocation'] = $data_vs['is_auto_allocation'];
	$arr['job_no'] = $exp_data[1];
	$arr['booking_no'] = $exp_data[2];
	$arr['po_id'] = 0;
	$allocation_arr = get_allocation_balance($arr);

	if($data_vs['is_allocated'] == 1 && $data_vs['is_sales_order'] == 1 && $data_vs['is_auto_allocation'] == 2 )
	{
		$booking_requisition_qty = $allocation_arr['booking_requisition'][$exp_data[2]][$exp_data[3]]['qty']*1;
		$yarn_dyeing_service_qty = $allocation_arr['yarn_dyeing_service'][$exp_data[1]][$exp_data[3]]['qty']*1;
		$booking_allocation_qty = $allocation_arr['booking_allocation'][$exp_data[2]][$exp_data[3]]*1;
		$available_qty = $booking_allocation_qty - ($booking_requisition_qty + $yarn_dyeing_service_qty);

		echo "$('#txt_wo_qty_0').attr('placeholder', '".$available_qty."');\n";
		echo "$('#txt_allocated_qty_').val('".$available_qty."');\n";
	}
	exit();
}
?>