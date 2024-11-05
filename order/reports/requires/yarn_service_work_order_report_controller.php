<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "" );   	 
	exit();
}

/*if($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_supplier_id", 120, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b where a.status_active =1 and a.is_deleted=0 and a.id=b.supplier_id and b.tag_company='$data' and a.id in (select supplier_id from lib_supplier_party_type where party_type in (4)) order by a.supplier_name","id,supplier_name", 1, "--Select Supplier--", $selected, "" );   	 
	exit();
}*/



if ($action == "FSO_No_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$data=explode("_", $data);
	$cbo_company_id=$data[0];
	$buyer_name=$data[1];
	?>

	<script>
		var hide_fso_id='<? echo $hide_fso_id; ?>';
		var selected_id = new Array, selected_name = new Array();

		function check_all_data(is_checked)
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
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

		function set_all()
		{
			var old=document.getElementById('txt_fso_row_id').value;
			if(old!="")
			{
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{
					js_set_value( old[i] )
				}
			}
		}

		function js_set_value( str)
		{

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );


			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 )
			{
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );

			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id =''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hide_fso_id').val( id );
			$('#hide_fso_no').val( name );
		}

	</script>

</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:710px;">
				<table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Company</th>
						<th>Buyer Name</th>
						<th>Booking Year</th>
						<th>Within Group</th>
						<th>FSO NO.</th>
						<th>Booking NO.</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
						<input type="hidden" name="hide_fso_no" id="hide_fso_no" value="" />
						<input type="hidden" name="hide_fso_id" id="hide_fso_id" value="" />

					</thead>
					<tbody>
						<tr>
							<td>
								<?
								echo create_drop_down( "cbo_company_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $cbo_company_id, "",1 );
								?>
							</td>
							<td align="center">
								<?
								echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and b.tag_company=$cbo_company_id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
								?>
							</td>
							<td><? echo create_drop_down( "booking_year", 70, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
							<td><? echo create_drop_down( "cbo_within_group", 80, $yes_no,"", 1, "-- Select --", $selected, "",0,"" );?></td>
							<td>
								<input type="text" style="width:100px" class="text_boxes" name="txt_fso_no" id="txt_fso_no" />
							</td>
							<td>
								<input type="text" style="width:100px" class="text_boxes" name="txt_wo_no" id="txt_wo_no" />
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $cbo_company_id; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('booking_year').value+'**'+document.getElementById('cbo_within_group').value+'**'+document.getElementById('txt_fso_no').value+'**'+document.getElementById('txt_wo_no').value+'**'+'<? echo $hidden_fso_id;?>', 'create_fso_no_search_list_view', 'search_div', 'yarn_service_work_order_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_fso_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$buyer_id=$data[1];
	$year=$data[2];
	$within_group=$data[3];
	$fso_no=trim($data[4]);
	$booking_no=trim($data[5]);

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$search_cond = "";

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_cond_with_1=" and c.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_cond_with_2=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_cond_with_1 =  "";
				$buyer_cond_with_2 =  "";
			}
		}
		else
		{
			$buyer_cond_with_1 =  "";
			$buyer_cond_with_2 =  "";
		}
	}
	else
	{
		$buyer_cond_with_1 =  " and c.buyer_id=$data[1]";
		$buyer_cond_with_2 =  " and a.buyer_id=$data[1]";
	}


	if($fso_no != "")
	{
		$search_cond .= " and a.job_no_prefix_num = '$fso_no'" ;
	}
	if($booking_no != "")
	{
		$search_cond .= " and a.sales_booking_no like '%$booking_no%'" ;
	}
	if($db_type==0)
	{
		if($year!=0) $search_cond .=" and YEAR(a.insert_date)=$year"; else $search_cond .="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($year!=0) $search_cond .=" $year_field_con=$year"; else $search_cond .="";
	}

	$sql_2 ="select a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,a.buyer_id
	from fabric_sales_order_mst a, fabric_sales_order_dtls b
	where a.id = b.mst_id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and a.company_id = $company_id $search_cond and a.within_group = '2' $buyer_cond_with_2
	group by a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,a.buyer_id
	order by id desc";

	$sql_1 = "select a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,c.buyer_id
	from fabric_sales_order_mst a, fabric_sales_order_dtls b ,wo_booking_mst c
	where a.id = b.mst_id and a.sales_booking_no = c.booking_no and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1
	and b.is_deleted = 0 and a.company_id = $company_id $search_cond and a.within_group = '1' $buyer_cond_with_1
	group by a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,c.buyer_id";

	if($within_group == 1)
	{
		$sql = $sql_1 ;
	}
	else if($within_group == 2)
	{
		$sql = $sql_2;
	}else
	{
		$sql = $sql_1." union all ". $sql_2 ;
	}
//echo $sql;
	?>

	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" align="left">
		<thead>
			<th width="40">SL</th>
			<th width="130">Company</th>
			<th width="120">Buyer</th>
			<th width="150">FSO No</th>
			<th width="">Booking No</th>
		</thead>
	</table>
	<div style="width:700px; overflow-y:scroll; max-height:250px; float: left;" id="buyer_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="682" class="rpt_table" id="tbl_list_search" >
			<?php
			$i=1; $fso_row_id="";
			$nameArray=sql_select( $sql );
			foreach ($nameArray as $selectResult)
			{
				if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
				?>

				<tr height="20" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
					<td width="40" align="center"><?php echo "$i"; ?>
					<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $selectResult[csf('id')]; ?>"/>
					<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $selectResult[csf('job_no')]; ?>"/>
				</td>
				<td width="130"><p><?php echo $company_arr[$selectResult[csf('company_id')]]; ?></p></td>
				<td width="120" title="<? echo $selectResult[csf('buyer_id')];?>"><?php echo $buyer_arr[$selectResult[csf('buyer_id')]]; ?></td>
				<td width="150"><p><?php echo $selectResult[csf('job_no')];?></p></td>
				<td width=""><?php echo $selectResult[csf('sales_booking_no')];?></td>
			</tr>
			<?
			$i++;
		}
		?>
	</table>
</div>

<table width="600" cellspacing="0" cellpadding="0" style="border:none" align="left">
	<tr>
		<td align="center" height="30" valign="bottom">
			<div style="width:100%">
				<div style="width:50%; float:left" align="left">
					<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
				</div>
				<div style="width:50%; float:left" align="left">
					<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
				</div>
			</div>
		</td>
	</tr>
</table>

<?
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
						<th colspan="6">
							<?
							echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
							?>
						</th>
					</tr>
					<tr>
						<th width="170"> Service Type</th>
						<th width="170">Supplier Name</th>
						<th width="100">WO No</th>
						<th width="150" colspan="2">Booking  Date</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('searchorderfrm_1','search_div','','','','');"  /></th>
					</tr>
				</thead>
				<tbody>
					<tr class="general">
						<td>
							<? echo create_drop_down( "cbo_service_type", 160, $yarn_issue_purpose,"", 1, "-- Select --", $selected, "",0,'12,15,38,46,50,51');?>
						</td>
						<td>
							<?

							echo create_drop_down( "cbo_supplier_name", 160, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and c.tag_company=$company and a.status_active =1 and b.party_type in(21) group by a.id,a.supplier_name order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );
							?>
						</td>
						<td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
						<td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" /></td>
						<td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" /></td>
						<td>
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_service_type').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value, 'create_sys_search_list_view', 'search_div', 'yarn_service_work_order_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td align="center" valign="middle" colspan="6">
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


	if($db_type==0)
	{
		$sql = "select
		a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id,a.service_type, a.supplier_id, a.booking_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,year(a.insert_date) as year
		from
		wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b left join   sample_development_mst d on  b.job_no_id=d.id
		where
		a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.entry_form=94 and b.entry_form=94 $company $supplier  $sql_cond  $service_type_cond  $booking_cond
		group by a.id order by a.id DESC";
	}

	else if($db_type==2)
	{
		$sql = "select
		a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id,a.service_type, a.supplier_id, a.booking_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,TO_CHAR(a.insert_date,'YYYY') as year
		from
		wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b left join   sample_development_mst d on  b.job_no_id=d.id
		where
		a.id=b.mst_id  and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.entry_form=94 and b.entry_form=94 $company $supplier  $sql_cond  $service_type_cond  $booking_cond
		group by
		a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id,a.service_type, a.supplier_id, a.booking_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,a.insert_date order by a.id DESC";
	}
	//echo $sql;

	?>	<div style="width:860px; "  align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table" >
			<thead>
				<th width="30">SL</th>
				<th width="50">Wo No</th>
				<th width="40">Year</th>
				<th width="100"> Service Type</th>
				<th width="100">Currency</th>
				<th width="50">Exchange Rate</th>
				<th width="100">Pay Mode</th>
				<th width="170">Supplier Name</th>
				<th width="70">Booking Date</th>
				<th>Delevary Date</th>
			</thead>
		</table>
		<div style="width:860px; margin-left:3px; overflow-y:scroll; max-height:300px;" id="buyer_list_view">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table" id="tbl_list_search" >
				<?

				$i=1;
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>+'_'+'<? echo $selectResult[csf('ydw_no')]; ?>'+'_'+<? echo $selectResult[csf('service_type')]; ?>); ">

						<td width="30" align="center"> <p><? echo $i; ?></p></td>
						<td width="50" align="center"><p> <? echo $selectResult[csf('yarn_dyeing_prefix_num')]; ?></p></td>
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

if ($action=="report_generate")
{
	extract($_REQUEST);
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$wo_no=str_replace("'","",$txt_wo_no);
	$cbo_service_type_id=str_replace("'","",$cbo_service_type_id);
	$fso_number_show=str_replace("'","",$fso_number_show);
	$hidd_po=str_replace("'","",$fso_number);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$txt_ir_no=str_replace("'","",$txt_ir_no);
	if ($cbo_company==0) $company_id=""; else $company_id=" and a.company_id=$cbo_company";
	if ($cbo_buyer) $buyer_id=" and c.po_buyer=$cbo_buyer"; else $buyer_id=""; 
	if ($cbo_service_type_id) $service_type_cond=" and a.service_type=$cbo_service_type_id"; else $service_type_cond=""; 

	if ($wo_no) $wo_no_cond=" and a.ydw_no like '%$wo_no%'"; else $wo_no_cond="";

	if ($hidd_po) $po_id_cond=" and b.job_no_id in ( $hidd_po )"; else $po_id_cond="";  
	
	
	if($db_type==0)
	{
		if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and a.booking_date between '".$date_from."' and '".$date_to."'";
	}
	if($db_type==2)
	{
		if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and a.booking_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
	}

	if (str_replace("'","",$txt_ir_no)!="") $ir_cond=" and c.grouping like '%$txt_ir_no%' "; else $ir_cond="";

	if(str_replace("'","",$wo_no)!="" || str_replace("'","",$fso_number_show)!="" || str_replace("'","",$txt_ir_no)!="")
	{
		$sql= "SELECT a.id,  c.grouping,  a.job_no, a.booking_no  from wo_booking_mst a, wo_po_details_master b, wo_po_break_down c where a.job_no=b.job_no and a.job_no=c.job_no_mst and b.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$cbo_company $ir_cond";
		// echo $sql;
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
	if(!empty($all_booking_no_arr))
	{
		$job_booking_cond = " ".where_con_using_array($all_booking_no_arr,1,'c.sales_booking_no')." ";
	}


	$company_library=return_library_array( "select id,company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
	$supplierArr=return_library_array( "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0", "id", "supplier_name");	
	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
	$count_arr=return_library_array( "Select id, yarn_count from  lib_yarn_count where  status_active=1 and is_deleted=0",'id','yarn_count');
	$style_ref_no_arr=return_library_array( "select job_no, style_ref_no from wo_po_details_master where status_active=1 and is_deleted=0", "job_no", "style_ref_no");
	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

	$sql="select a.id,a.ydw_no,a.booking_date,a.currency,a.delivery_date,a.service_type,a.supplier_id,a.pay_mode, b.job_no,b.job_no_id,b.dyeing_charge,b.count,b.yarn_description,b.color_range,sum(b.yarn_wo_qty) yarn_wo_qty,sum(b.amount) amount,b.remarks,b.product_id, c.sales_booking_no,c.buyer_id,c.po_buyer,c.style_ref_no,c.within_group 
		from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b,fabric_sales_order_mst c
		where a.id=b.mst_id and c.id=b.job_no_id and b.job_no=c.job_no and a.company_id=$cbo_company $wo_no_cond $service_type_cond $po_id_cond $buyer_id $booking_date_cond $job_booking_cond and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=94    
		group by a.id,a.ydw_no,a.booking_date,a.currency,a.delivery_date,a.service_type,a.supplier_id,a.pay_mode,b.job_no,b.job_no_id,b.dyeing_charge,b.count,b.yarn_description,b.color_range,b.remarks,b.product_id,c.sales_booking_no,c.buyer_id,c.po_buyer,c.style_ref_no,c.within_group order by a.id DESC";
	// echo $sql;
		
	$sql_result=sql_select($sql);
	$all_booking_arr = array();
	foreach ($sql_result as $row)
	{
		if($allbookingNoChk[$row[csf('sales_booking_no')]] == "")
		{
			$allbookingNoChk[$row[csf('sales_booking_no')]] = $row[csf('sales_booking_no')];
			array_push($all_booking_arr, $row[csf('sales_booking_no')]);
		}
	}

	$sql_job = "SELECT a.id,  c.grouping,  a.job_no, a.booking_no  from wo_booking_mst a, wo_po_details_master b, wo_po_break_down c where a.job_no=b.job_no and a.job_no=c.job_no_mst and b.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company ".where_con_using_array($all_booking_arr,1,'a.booking_no')." ";

	$sql_job_rslt=sql_select($sql_job);
	$job_info_arr = array();
	foreach ($sql_job_rslt as $val)
	{
		$job_info_arr[$val[csf("booking_no")]]["job_no"] = $val[csf("job_no")];
		$job_info_arr[$val[csf("booking_no")]]["grouping"] .= $val[csf("grouping")].',';
	}
	// echo "<pre>";
	// print_r($job_info_arr);


	$prod_ids="";$service_wo_ids="";
	foreach ($sql_result as $val) 
	{
		$prod_ids.=$val[csf("product_id")].",";
		$service_wo_ids.=$val[csf("id")].",";
	}
 	$prod_ids=chop($prod_ids,",");
	$prod_id=explode(",",$prod_ids); 
	$prod_id=array_unique($prod_id); 
	$prod_id=array_chunk($prod_id,999);
	$prod_id_cond=" and";
	foreach($prod_id as $dtls_id)
	{
		if($prod_id_cond==" and")  $prod_id_cond.="(id in(".implode(',',$dtls_id).")"; else $prod_id_cond.=" or id in(".implode(',',$dtls_id).")";
	}
	$prod_id_cond.=")";
 	$sql_prod=sql_select("select id,lot,yarn_type,color from product_details_master where status_active=1 and is_deleted=0 $prod_id_cond");
	foreach($sql_prod as $row)
	{
		 $prod_id_arr[$row[csf("id")]]["lot"] 		= $row[csf("lot")];
		 $prod_id_arr[$row[csf("id")]]["yarn_type"] = $row[csf("yarn_type")];
		 $prod_id_arr[$row[csf("id")]]["color"] 	= $row[csf("color")];
	}

	$service_wo_ids=chop($service_wo_ids,",");
	$service_wo_id=explode(",",$service_wo_ids); 
	$service_wo_id=array_unique($service_wo_id); 
	$service_wo_id=array_chunk($service_wo_id,999);
	$service_wo_cond=" and";
	foreach($service_wo_id as $dtls_id)
	{
		if($service_wo_cond==" and")  $service_wo_cond.="(a.booking_id in(".implode(',',$dtls_id).")"; else $service_wo_cond.=" or a.booking_id in(".implode(',',$dtls_id).")";
	}
	$service_wo_cond.=")";
 	$sql_issue=sql_select("select  a.booking_id,a.issue_purpose,a.buyer_job_no,b.prod_id, 
 		sum(case when b.transaction_type=2 and a.entry_form in(3) then b.cons_quantity end) as issue_qty,
 		sum(case when  b.transaction_type=2 and a.entry_form in(3) then b.return_qnty end) as iss_returnable_qty 
 		from inv_issue_master a, inv_transaction b 
 		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=1 and b.item_category=1 and a.entry_form in(3) $service_wo_cond 
 		group by  a.booking_id,a.issue_purpose,a.buyer_job_no,b.prod_id");
	foreach($sql_issue as $row)
	{
		$yarn_issue_arr[$row[csf("booking_id")]][$row[csf("issue_purpose")]][$row[csf("buyer_job_no")]][$row[csf("prod_id")]]["issue_qty"] = $row[csf("issue_qty")];
		$yarn_issue_arr[$row[csf("booking_id")]][$row[csf("issue_purpose")]][$row[csf("buyer_job_no")]][$row[csf("prod_id")]]["iss_returnable_qty"] = $row[csf("iss_returnable_qty")];
	}
	$sql_issue_return=sql_select("select  a.booking_id,b.prod_id, 
         sum(case when b.transaction_type=4 and a.entry_form in(9) then b.cons_quantity end) as issue_return_qty
         from inv_receive_master a, inv_transaction b 
         where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=1 and b.item_category=1 and a.entry_form in(9) $service_wo_cond 
         group by  a.booking_id,b.prod_id");
	foreach($sql_issue_return as $row)
	{
		$yarn_issue_retn_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]["issue_return_qty"] = $row[csf("issue_return_qty")];
	}
		ob_start();
		?>
		<fieldset>
			<table width="2650" cellspacing="0" >
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none; font-size:18px;" colspan="20">
						<? echo $company_library[str_replace("'","",$cbo_company_id)]; ?>                                
					</td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none;font-size:16px; font-weight:bold" colspan="20"> <? echo $report_title; ?></td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none;font-size:12px; font-weight:bold" colspan="20"> <? if( $date_from!="" && $date_to!="" ) echo "From ".change_date_format($date_from)." To ".change_date_format($date_to);?></td>
				</tr>
			</table>
			<table width="2650" cellspacing="0" border="1" class="rpt_table" rules="all">
				<thead>
					<tr>
                        <th width="30">SL</th>    
                        <th width="70">Buyer</th>
                        <th width="100">Style Reference</th>
                        <th width="150">FSO No</th>
                        <th width="150">IR/IB</th>
                        <th width="100">Fabric Booking No</th>
                        <th width="100">Service Type</th>
                        <th width="180">Service Company</th>
                        <th width="110">Wo No</th>
                        <th width="110">WO Date</th>
                        <th width="80">Delivery Date</th>
                        <th width="90">Y. Count</th>
                        <th width="190">Yarn Composition</th>
                        <th width="60">Y. Type</th>
                        <th width="60">Y. Color</th>
                        <th width="120">Lot</th>
                        <th width="80">WO Qty.</th>
                        <th width="90">Issue</th>
                        <th width="70">Returnable </th>
                        <th width="100">Issue Returned </th>
                        <th width="100">Balance</th>
                        <th width="100">Currency</th>
                        <th width="50">Rate</th>
                        <th width="100">Amount</th>
                        <th>Remarks</th>
					</tr>
				</thead>
			</table>
			<div style="max-height:350px; overflow-y:scroll; width:2650px" id="scroll_body" >
				<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="2630" rules="all" id="table_body" >
				<?
				$i=1;
				foreach($sql_result as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$supplier_str="";
					if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5) $supplier_str=$company_library[$row[csf("supplier_id")]]; else  $supplier_str=$supplierArr[$row[csf("supplier_id")]];
					if($row[csf("entry_form")]=="") $row[csf("entry_form")]=0;
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
						<td width="30"><? echo $i;?></td>	
						<td width="70"><?  if($row[csf("within_group")]==1){echo $buyerArr[$row[csf("po_buyer")]];}else{ echo $buyerArr[$row[csf("buyer_id")]];}  ?></td>
						<td width="100" style="word-break:break-all"><? echo $row[csf("style_ref_no")]; ?></td>
						<td width="150" style="word-break:break-all"><? echo $row[csf("job_no")]; ?></td>
						<td width="150" style="word-break:break-all"><? //echo $job_info_arr[$row[csf("sales_booking_no")]]['grouping']; 
						$int_ref = $job_info_arr[$row[csf('sales_booking_no')]]["grouping"];
						echo implode(",", array_unique(explode(",",chop($int_ref ,","))));
						?></td>
						<td width="100" style="word-break:break-all" align="center"><? echo $row[csf("sales_booking_no")]; ?>&nbsp;</td>
						<td width="100" style="word-break:break-all"><? echo $yarn_issue_purpose[$row[csf("service_type")]]; ?>&nbsp;</td>
						<td width="180" style="word-break:break-all"><? echo  $supplier_str; ?></td>
						<td width="110" style="word-break:break-all"><? echo $row[csf("ydw_no")];?></td>
						<td width="110" align="center" style="word-break:break-all"><? echo change_date_format($row[csf("booking_date")]); ?></td>
						<td width="80" align="center" style="word-break:break-all"><? echo change_date_format($row[csf("delivery_date")]);  ?></td>
                        <td width="90" style="word-break:break-all; text-align: center;"><? echo $count_arr[$row[csf("count")]];?></td>
                        <td width="190" style="word-break:break-all"><? echo $row[csf("yarn_description")];?></td>
                        <td width="60" style="word-break:break-all"><? echo $yarn_type[$prod_id_arr[$row[csf("product_id")]]["yarn_type"]];  ?></td>
                        <td width="60" style="word-break:break-all"><? echo $color_arr[$prod_id_arr[$row[csf("product_id")]]["color"]]; ?></td>
						<td width="120" style="word-break:break-all"><? echo $prod_id_arr[$row[csf("product_id")]]["lot"]; ?></td>
						<td width="80" style="word-break:break-all; text-align: right;"><? echo $row[csf("yarn_wo_qty")]; ?></td>
						<td width="90" align="right"><a href='#report_details' onClick="openmypage('<? echo $row[csf("id")]; ?>','<? echo $row[csf("service_type")]; ?>','<? echo $row[csf("job_no")]; ?>','<? echo $row[csf("product_id")]; ?>','<? echo 1; ?>','issue_popup');"><? $issue_qty=$yarn_issue_arr[$row[csf("id")]][$row[csf("service_type")]][$row[csf("job_no")]][$row[csf("product_id")]]["issue_qty"]; echo number_format($issue_qty,2); ?></a>
						</td>
						<td width="70" align="right"><? $returnable_issue_qty=$yarn_issue_arr[$row[csf("id")]][$row[csf("service_type")]][$row[csf("job_no")]][$row[csf("product_id")]]["iss_returnable_qty"]; echo number_format($returnable_issue_qty,2);
						 ?></td>
						<td width="100" align="right"><a href='#report_details' onClick="openmypage('<? echo $row[csf("id")]; ?>','<? echo $row[csf("service_type")]; ?>','<? echo $row[csf("job_no")]; ?>','<? echo $row[csf("product_id")]; ?>','<? echo 2; ?>','issue_popup');"><? $issue_return=$yarn_issue_retn_arr[$row[csf("id")]][$row[csf("product_id")]]["issue_return_qty"] ;echo number_format($issue_return,2); ?></a>
						</td>
						<td width="100" align="right" title="Balance = (Returnable - Issue Returned)"><? $tot_balance=$returnable_issue_qty-$issue_return;echo number_format($tot_balance,2); ?></td>
						<td width="100" align="center"><? echo $currency[$row[csf("currency")]]; ?></td>
						<td width="50" align="right"><? echo $row[csf("dyeing_charge")]; ?></td>
						<td width="100" align="right"><? echo number_format($row[csf("amount")],2); ?></td>
						<td style="word-break:break-all"><? echo $row[csf("remarks")]; ?></td>
					</tr>
					<?
					$i++;
					$total_wo_qnty 					+=  $row[csf("yarn_wo_qty")];
					$total_issue_qnty 				+=  $issue_qty;
					$total_returnable_issue_qnty 	+=  $returnable_issue_qty;
					$total_issue_return_qnty 		+=  $issue_return;
					$total_balance 					+=  $tot_balance;
				}
				?>
				</table>
            </div>
            <table rules="all" class="rpt_table" width="2650" cellspacing="0" cellpadding="0" border="1">
                <tfoot>
                    <tr>
                        <th width="30">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="150">&nbsp;</th>
                        <th width="150">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="180">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="190">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="120">Total :</th>
                        <th width="80" align="right" style="word-break:break-all" id="value_total_wo_qnty"><? echo number_format($total_wo_qnty,2,".",""); ?></th>
                        <th width="90" align="right" style="word-break:break-all" id="value_total_issue"><? echo number_format($total_issue_qnty,2,".",""); ?></th>
                        <th width="70" align="right" style="word-break:break-all" id="value_total_issue_returnable"><? echo number_format($total_returnable_issue_qnty,2,".",""); ?></th>
                       <th width="100" align="right" style="word-break:break-all" id="value_total_issue_return"><? echo number_format($total_issue_return_qnty,2,".",""); ?></th>
                        <th width="100" align="right" style="word-break:break-all" id="value_total_balance"><? echo number_format($total_balance,2,".",""); ?></th>
                        <th width="100" align="right" style="word-break:break-all"></th>
                        <th width="50" align="right" style="word-break:break-all"></th>
                        <th width="100" align="right" style="word-break:break-all"></th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
		</fieldset>
		<?

	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) 
    {
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

if($action=="issue_popup")
{
	echo load_html_head_contents("Issue Info", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	?>
	<fieldset style="width:440px; margin-left:3px">
		<div id="scroll_body" align="center">
			<?
			 if($type==1)
             {
             	?>
				<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
					<thead>
	                	<tr>
	                        <th width="30">Sl</th>
	                        <th width="80">Issue Date</th>
	                        <th width="130">Issue ID</th>
	                        <th width="60">Issue Qnty</th>
	                        <th>Remarks</th>
					</thead>
	                <tbody>
	                <?
						$sql_issue=sql_select("select  a.issue_date,a.issue_number,a.remarks, 
					 		sum(case when b.transaction_type=2 and a.entry_form in(3) then b.cons_quantity end) as issue_qty
					 		from inv_issue_master a, inv_transaction b 
					 		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.booking_id=$booking_id and a.item_category=1 and b.item_category=1 and a.entry_form in(3) and b.prod_id=$product_id
					 		group by  a.issue_date,a.issue_number,a.remarks");
					
						$i=1;				
						foreach($sql_issue as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><p><? echo $i; ?></p></td>
	                            <td align="center" width="80"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td> 
	                            <td align="center" width="130"><p><? echo $row[csf('issue_number')]; ?></p></td>
	                            <td align="right" width="30"><p><? echo  number_format($row[csf('issue_qty')],2); ?></p></td>
	                            <td align="center"><p><? echo $row[csf('remarks')]; ?></p></td>
	                        </tr>
							<?
							$tot_qty+=$row[csf('issue_qty')];
							$i++;
						}
					
					?>
	                </tbody>
	                <tfoot>
	                	<tr class="tbl_bottom">
	                    	<td colspan="3" align="right">Total</td>
	                        <td align="right"><? echo number_format($tot_qty,2); ?> </td>
	                        <td></td>
	                    </tr>
	                </tfoot>
	            </table>
	         	<?
	     	}
	     	else
	     	{
	     		?>
				<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
					<thead>
	                	<tr>
	                        <th width="30">Sl</th>
	                        <th width="80">Return Date</th>
	                        <th width="120">Return ID</th>
	                        <th width="50">Issue Return Qnty</th>
	                        <th width="50">Reject Qnty</th>
	                        <th>Remarks</th>
					</thead>
	                <tbody>
	                <?
						$sql_issue_return=sql_select("select a.recv_number,a.receive_date,a.remarks, 
				         sum(case when b.transaction_type=4 and a.entry_form in(9) then b.cons_quantity end) as issue_return_qty,
				         sum(case when b.transaction_type=4 and a.entry_form in(9) then b.cons_reject_qnty end) as cons_reject_qnty
				         from inv_receive_master a, inv_transaction b 
				         where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.booking_id=$booking_id and a.item_category=1 and b.item_category=1 and  a.entry_form in(9) and b.prod_id=$product_id
				         group by  a.recv_number,a.receive_date,a.remarks");
						$i=1;				
						foreach($sql_issue_return as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><p><? echo $i; ?></p></td>
	                            <td align="center" width="80"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td> 
	                            <td align="center" width="130"><p><? echo $row[csf('recv_number')]; ?></p></td>
	                            <td align="right" width="30"><p><? echo  number_format($row[csf('issue_return_qty')],2); ?></p></td>
	                            <td align="right" width="30"><p><? echo  number_format($row[csf('cons_reject_qnty')],2); ?></p></td>
	                            <td align="center"><p><? echo $row[csf('remarks')]; ?></p></td>
	                        </tr>
							<?
							$tot_ret_qty+=$row[csf('issue_return_qty')];
							$tot_recj_qty+=$row[csf('cons_reject_qnty')];
							$i++;
						}
					
					?>
	                </tbody>
	                <tfoot>
	                	<tr class="tbl_bottom">
	                    	<td colspan="3" align="right">Total</td>
	                        <td align="right"><? echo number_format($tot_ret_qty,2); ?> </td>
	                        <td align="right"><? echo number_format($tot_recj_qty,2); ?> </td>
	                        <td></td>
	                    </tr>
	                </tfoot>
	            </table>
	         	<?
	     	}

	    	 	?>
            
        </div>
    </fieldset>
    <?
	exit();
}




?>
