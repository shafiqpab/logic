<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$knit_defect_inchi_array2=array(1=>"Select",2=>"Present",3=>"Not Found",4=>"Major",5=>"Minor",6=>"Acceptable",7=>"Good");

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];
//========== user credential start ========
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];
$location_id = $userCredential[0][csf('location_id')];

if ($location_id !='') {
    $location_credential_cond = " and id in($location_id)";
}
$knit_defect_array2=array(1=>"Fly Conta",2=>"PP conta",3=>"Patta/Barrie",4=>"Needle Mark",5=>"Sinker Mark",6=>"thick-thin",7=>"neps/knot",8=>"white speck",9=>"Black Speck",10=>"Star Mark",11=>"Dia/Edge Mark",12=>"Dead fibre",13=>"Running shade",14=>"Hairiness",15=>"crease mark",16=>"Uneven",17=>"Padder Crease",18=>"Absorbency",19=>"Bowing",20=>"Handfeel",21=>"Dia Up-down",22=>"Cut hole",23=>"Snagging/Pull out",24=>"Pin Hole",25=>"Bad Smell",26=>"Bend Mark");

$knit_defect_short_array2=array(1=>"",2=>"",3=>"PT/BR",4=>"N",5=>"SM",6=>"",7=>"NP",8=>"",9=>"",10=>"",11=>"",12=>"",13=>"RS",14=>"HR",15=>"CR",16=>"",17=>"",18=>"",19=>"BW",20=>"",21=>"",22=>"",23=>"",24=>"",25=>"",26=>"");

$defect_arr = return_library_array("select defect_name, defect_name from  lib_defect_name where type=1", "defect_name", "defect_name");
$defect_short_arr = return_library_array("select defect_name, short_name from  lib_defect_name where type=1", "defect_name", "short_name");

//====================Location ACTION========

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location_dyeing", 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select--", 0, "" );
	exit();
}
if ($action=="load_drop_down_location_lc")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location", 150, "select id,location_name from lib_location where company_id='$data[0]' $location_credential_cond and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select--", 0, "load_room_rack_self_bin('requires/finish_fabric_receive_controller*2', 'store','store_td', $('#cbo_company_id').val(), this.value);" );
	exit();
}
if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/finish_fabric_receive_controller",$data);
}

if($action=="load_drop_down_dyeing_com")
{
	$data = explode("_",$data);
	$company_id=$data[1];

	if($data[0]==1)
	{
		echo create_drop_down( "cbo_dyeing_company", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Dyeing Company--", 0, "load_location();load_drop_down( 'requires/finish_fabric_receive_controller', $data[0]+'_'+this.value, 'load_drop_down_machine_name','machine_name_td');","" );
	}
	else if($data[0]==3)
	{
		echo create_drop_down( "cbo_dyeing_company", 160, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=21 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select Dyeing Company--", 1, "load_location();" );
			//load_drop_down( 'requires/finish_fabric_receive_controller', this.value+'_'+$data[0], 'load_drop_down_machine_name','machine_name_td');
	}
	else
	{
		echo create_drop_down( "cbo_dyeing_company", 160, $blank_array,"",1, "--Select Dyeing Company--", 0, "load_location();load_drop_down( 'requires/finish_fabric_receive_controller', $data[0]+'_'+this.value, 'load_drop_down_machine_name','machine_name_td');" );
	}
	exit();
}
if($action=="load_drop_down_dyeing_com_new")
{
	$data = explode("_",$data);
	$company_id=$data[1];

	if($data[0]==1)
	{
		
		echo create_drop_down( "cbo_dyeing_company", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Dyeing Company--", 0, "load_location();load_drop_down( 'requires/finish_fabric_receive_controller', $data[0]+'_'+this.value, 'load_drop_down_machine_name','machine_name_td');","" );
	}
	else if($data[0]==3)
	{
		$sql = "SELECT a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=21 and a.status_active=1 group by a.id,a.supplier_name  UNION ALL SELECT a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b ,INV_RECEIVE_MASTER c where a.id=b.supplier_id and b.party_type=21 and a.status_active IN(1,3) AND c.supplier_id = a.id group by a.id,a.supplier_name order by supplier_name ";

		echo create_drop_down( "cbo_dyeing_company", 160, "$sql","id,supplier_name", 1, "--Select Dyeing Company--", 1, "load_location();" );
			//load_drop_down( 'requires/finish_fabric_receive_controller', this.value+'_'+$data[0], 'load_drop_down_machine_name','machine_name_td');
	}
	else
	{
		echo create_drop_down( "cbo_dyeing_company", 160, $blank_array,"",1, "--Select Dyeing Company--", 0, "load_location();load_drop_down( 'requires/finish_fabric_receive_controller', $data[0]+'_'+this.value, 'load_drop_down_machine_name','machine_name_td');" );
	}
	exit();
}

if($action=="load_drop_down_machine_name")
{
	$data=explode("_",$data);
	if($db_type==0)
	{
		echo create_drop_down( "cbo_machine_name", 142, "select id,concat(machine_no,'-',brand) as machine_name from lib_machine_name where category_id=4 and company_id='$data[1]' and status_active=1 and is_deleted=0 and is_locked=0","id,machine_name", 1, "-- Select Machine --", 0, "","" );
	}
	else
	{
		echo create_drop_down( "cbo_machine_name", 142, "select id,(machine_no || '-' || brand) as machine_name from lib_machine_name where category_id=4 and company_id='$data[1]' and status_active=1 and is_deleted=0 and is_locked=0","id,machine_name", 1, "-- Select Machine --", 0, "","" );
	}
}

//====================SYSTEM ID POPUP========
if ($action=="systemId_popup")
{
	echo load_html_head_contents("System ID Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(id)
		{
			$('#hidden_sys_id').val(id);
			parent.emailwindow.hide();
		}
	</script>
</head>

<body>
	<div align="center" style="width:840px;">
		<form name="searchsystemidfrm"  id="searchsystemidfrm">
			<fieldset style="width:830px;">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="800" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Production Date Range</th>
						<th>Search By</th>
						<th id="search_by_td_up">Please Enter System Id</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
							<input type="hidden" name="hidden_sys_id" id="hidden_sys_id" class="text_boxes" value="">
						</th>
					</thead>
					<tr class="general">
						<td>
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;">To<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;">
						</td>
						<td>
							<?
							$search_by_arr=array(1=>"System ID",2=>"Challan No.",3=>"Batch No.");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td id="search_by_td">
							<input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value, 'create_finish_search_list_view', 'search_div', 'finish_fabric_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<table width="100%" style="margin-top:5px;">
					<tr>
						<td colspan="5">
							<div style="width:100%; margin-top:10px; margin-left:3px;" id="search_div" align="left"></div>
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_finish_search_list_view")
{
	$data = explode("_",$data);
	$search_string=trim($data[0]);
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];

	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.receive_date between '".change_date_format(trim($start_date),"yyyy-mm-dd","-")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd","-")."'";
		}
		else
		{
			$date_cond="and a.receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}

	$batch_cond=""; $search_field_cond="";
	if(trim($data[0])!="")
	{
		if($search_by==1)
			$search_field_cond="and a.recv_number like '%$search_string'";
		else if($search_by==2)
			$search_field_cond="and a.challan_no like '$search_string%'";
		else
		{
			$batch_id="";
			//echo "select id, batch_no from pro_batch_create_mst where batch_no='$search_string' and entry_form in(0,7) and status_active=1 and is_deleted=0";
			$batchArr = return_library_array("select id, batch_no from pro_batch_create_mst where batch_no='$search_string' and entry_form in(0,7,37) and status_active=1 and is_deleted=0","id","batch_no");
			foreach($batchArr as $key=>$val)
			{
				if($batch_id=="") $batch_id=$key; else $batch_id.=','.$key;
			}
			if($batch_id!=""){ $batch_cond=" and b.batch_id in (".$batch_id.")";} else die;
		}
	}
	//echo $batch_cond;
	if($db_type==0)
	{
		$sql = "select a.id, a.recv_number, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.receive_date, a.challan_no, YEAR(a.insert_date) as year, sum(b.receive_qnty) as qnty, group_concat(b.batch_id) as batch_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id $search_field_cond $date_cond $batch_cond group by a.id, a.recv_number, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.receive_date, a.challan_no, a.insert_date order by a.id DESC";
	}
	else
	{
		$sql = "select a.id, a.recv_number, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.receive_date, a.challan_no, to_char(a.insert_date,'YYYY') as year, sum(b.receive_qnty) as qnty, LISTAGG(cast(b.batch_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.batch_id) as batch_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id $search_field_cond $date_cond $batch_cond group by a.id, a.recv_number, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.receive_date, a.challan_no, a.insert_date order by a.id DESC";
	}
	//echo $sql;//die;
	$result = sql_select($sql);

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst","id","batch_no");

	//$finish_recv_arr=return_library_array( "select mst_id, sum(receive_qnty) as recv from pro_finish_fabric_rcv_dtls where status_active=1 and is_deleted=0 group by mst_id",'mst_id','recv');
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="50">Year</th>
			<th width="70">Received ID</th>
			<th width="120">Dyeing Source</th>
			<th width="120">Dyeing Company</th>
			<th width="80">Production date</th>
			<th width="80">Production Qnty</th>
			<th width="80">Challan No</th>
			<th>Batch No</th>
		</thead>
	</table>
	<div style="width:820px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;
			foreach ($result as $row)
			{
				if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";

				if($row[csf('knitting_source')]==1)
					$dye_comp=$company_arr[$row[csf('knitting_company')]];
				else
					$dye_comp=$supllier_arr[$row[csf('knitting_company')]];

				//$recv_qnty=$finish_recv_arr[$row[csf('id')]];
				$recv_qnty=$row[csf('qnty')];

				$batch_id=array_unique(explode(",",$row[csf('batch_id')]));
				$batch_no="";
				foreach($batch_id as $val)
				{
					if($batch_no=='') $batch_no=$batch_arr[$val]; else $batch_no.=",".$batch_arr[$val];
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>);">
					<td width="40"><? echo $i; ?></td>
					<td width="50" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="70"><p>&nbsp;<? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
					<td width="120"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
					<td width="120"><p><? echo $dye_comp; ?>&nbsp;</p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
					<td width="80" align="right"><? echo number_format($recv_qnty,2); ?>&nbsp;</td>
					<td width="80"><p>&nbsp;<? echo $row[csf('challan_no')]; ?></p></td>
					<td><p>&nbsp;<? echo $batch_no; ?></p></td>
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

if($action=='populate_data_from_finish_fabric')
{
	$data_array=sql_select("select id, recv_number, company_id, receive_basis, store_id, location_id,knitting_location_id, knitting_source, knitting_company, receive_date, challan_no, store_id, booking_no, booking_id from inv_receive_master where id='$data'");


	foreach ($data_array as $row)
	{
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("recv_number")]."';\n";
		echo "document.getElementById('cbo_production_basis').value 		= '".$row[csf("receive_basis")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		echo "set_production_basis();\n";

		echo "$('#cbo_production_basis').attr('disabled','true')".";\n";
		echo "document.getElementById('txt_production_date').value 			= '".change_date_format($row[csf("receive_date")])."';\n";
		echo "document.getElementById('cbo_dyeing_source').value 			= '".$row[csf("knitting_source")]."';\n";

		echo "load_drop_down('requires/finish_fabric_receive_controller', ".$row[csf("knitting_source")]."+'_'+".$row[csf("company_id")].", 'load_drop_down_dyeing_com', 'dyeingcom_td' );\n";
		echo "document.getElementById('cbo_dyeing_company').value 			= '".$row[csf("knitting_company")]."';\n";
		echo "load_location();\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('cbo_location_dyeing').value 			= '".$row[csf("knitting_location_id")]."';\n";
		echo "document.getElementById('cbo_location').value 				= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("store_id")]."';\n";
		echo "load_drop_down( 'requires/finish_fabric_receive_controller',".$row[csf("knitting_source")]."+'_'+".$row[csf("knitting_company")].",'load_drop_down_machine_name','machine_name_td');\n";
		echo "document.getElementById('update_id').value 					= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_booking_no').value 					= '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('txt_booking_id').value 					= '".$row[csf("booking_id")]."';\n";
		echo "$('#txt_booking_no').attr('disabled','true')".";\n";

		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_finish_production',1,1);\n";
		exit();
	}
}

if ($action=="batch_number_popup")
{
	echo load_html_head_contents("Batch Number Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(id,batch_no,booking_no,job_no,is_sales,unloaded_batch, sales_order_type)
		{
			if(unloaded_batch =="")
			{
				alert("This batch is not unloaded in dyeing production.");
				return;
			}

			$('#hidden_batch_id').val(id);
			$('#hidden_batch_no').val(batch_no);
			$('#booking_no').val(booking_no);
			$('#job_no').val(job_no);
			$('#is_sales').val(is_sales);
			$('#sales_order_type').val(sales_order_type);
			parent.emailwindow.hide();
		}
	</script>
</head>

<body>
	<div align="center" style="width:800px;">
		<form name="searchbatchnofrm"  id="searchbatchnofrm">
			<fieldset style="width:790px; margin-left:10px">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" border="1" rules="all" width="770" class="rpt_table">
					<thead>
						<th width="240">Batch Date Range</th>
						<th width="170">Search By</th>
						<th id="search_by_td_up" width="200">Please Enter Batch No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
							<input type="hidden" name="txt_dyeing_company_id" id="txt_dyeing_company_id" class="text_boxes" value="<? echo $cbo_dyeing_company; ?>">
							<input type="hidden" name="hidden_batch_id" id="hidden_batch_id" class="text_boxes" value="">
							<input type="hidden" name="hidden_batch_no" id="hidden_batch_no" class="text_boxes" value="">
							<input type="hidden" name="job_no" id="job_no" class="text_boxes" value="">
							<input type="hidden" name="booking_no" id="booking_no" class="text_boxes" value="">
							<input type="hidden" name="is_sales" id="is_sales" class="text_boxes" value="">
							<input type="hidden" name="sales_order_type" id="sales_order_type" class="text_boxes" value="">
						</th>
					</thead>
					<tr class="general">
						<td>
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px;">To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px;">
						</td>
						<td>
							<?
							$search_by_arr=array(0=>"Batch No",1=>"Fabric Booking no.",2=>"Color");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td align="center" id="search_by_td" width="140px">
							<input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('txt_dyeing_company_id').value, 'create_batch_search_list_view', 'search_div', 'finish_fabric_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="width:100%; margin-top:5px" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_batch_search_list_view")
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$dyeing_company_id =$data[5];

	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and batch_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and batch_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}

	if(trim($data[0])!="")
	{
		if($search_by==0)
			$search_field_cond="and batch_no like '$search_string'";
		else if($search_by==1)
			$search_field_cond="and booking_no like '$search_string'";
		else
			$search_field_cond="and color_id in(select id from lib_color where color_name like '$search_string')";
	}
	else
	{
		$search_field_cond="";
	}

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	//$sql = "select id, batch_no, extention_no, batch_date, batch_weight, booking_no, color_id, batch_against, booking_without_order, re_dyeing_from,sales_order_id,sales_order_no,is_sales from pro_batch_create_mst where entry_form=0 and batch_for=1 and batch_against<>4 and company_id=$company_id and working_company_id=$dyeing_company_id and status_active=1 and is_deleted=0 $search_field_cond $date_cond";

	$sql = "SELECT a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight, a.booking_no, a.color_id, a.batch_against, 
	a.booking_without_order, a.re_dyeing_from, a.sales_order_id, a.sales_order_no, a.is_sales, b.sales_order_type, 1 as batch_entry_source
	from pro_batch_create_mst a left join fabric_sales_order_mst b on a.sales_order_id=b.id and a.is_sales=1
	where a.entry_form=0 and a.batch_for=1 and a.batch_against<>4 and a.company_id=$company_id and a.working_company_id=$dyeing_company_id
	and a.status_active=1 and a.is_deleted=0 $search_field_cond $date_cond union all select a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight, a.booking_no, a.color_id, a.batch_against, a.booking_without_order, a.re_dyeing_from, a.sales_order_id, a.sales_order_no, a.is_sales, b.sales_order_type, 2 as batch_entry_source from pro_batch_create_mst a, fabric_sales_order_mst b where a.entry_form=65 and a.sales_order_id=b.id and a.is_sales=1 and a.company_id=$company_id and a.working_company_id=$dyeing_company_id and a.status_active=1 and a.is_deleted=0 $search_field_cond $date_cond";

	//echo $sql;die;
	$nameArray=sql_select( $sql );
	$batch_id_arr = array();
	foreach ($nameArray as $selectResult)
	{
		$batch_id_arr[$selectResult[csf('id')]] = $selectResult[csf('id')];
	}
	$batch_id_arr = array_filter($batch_id_arr);
	if(!empty($batch_id_arr))
	{
        $all_batch_cond="";
		$batchCond="";
        if($db_type==2 && count($batch_id_arr)>999)
        {
        	$all_batch_id_arr_chunk=array_chunk($batch_id_arr,999) ;
        	foreach($all_batch_id_arr_chunk as $chunk_arr)
        	{
        		$chunk_arr_value=implode(",",$chunk_arr);
        		$batchCond.="  a.id in($chunk_arr_value) or ";
        	}

        	$all_batch_cond.=" and (".chop($batchCond,'or ').")";
        }
        else
        {
        	$all_batch_cond=" and a.id in(".implode(",",$batch_id_arr).")";
        }

        $unload_dyeing_production_arr = return_library_array("select a.id, a.batch_no from pro_batch_create_mst a, pro_fab_subprocess b where a.id=b.batch_id and b.entry_form =35 and b.load_unload_id=2 and a.working_company_id=$dyeing_company_id $all_batch_cond and a.status_active=1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted=0","id","batch_no");


		if($db_type==0)
		{
			$order_id_arr=return_library_array( "select mst_id, group_concat(po_id) as po_id from pro_batch_create_dtls where mst_id in(".implode(",",$batch_id_arr).") and status_active=1 and is_deleted=0 group by mst_id",'mst_id','order_id');
		}
		else
		{
			$order_id_arr=return_library_array( "select mst_id, LISTAGG(cast(po_id as VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY po_id) as po_id from pro_batch_create_dtls where mst_id in(".implode(",",$batch_id_arr).") and status_active=1 and is_deleted=0 group by mst_id",'mst_id','po_id');
		}

	}

	$po_arr=array();
	//if(!empty($order_id_arr)){
	if(count(array_filter($order_id_arr)) != 0){
		$po_data=sql_select("select id, po_number,file_no,grouping as ref, job_no_mst from wo_po_break_down where id in(".implode(",",$order_id_arr).") and status_active=1");
		foreach($po_data as $row)
		{
			$po_arr[$row[csf('id')]]['po_no']=$row[csf('po_number')];
			$po_arr[$row[csf('id')]]['file']=$row[csf('file_no')];
			$po_arr[$row[csf('id')]]['ref']=$row[csf('ref')];
			$po_arr[$row[csf('id')]]['job_no']=$row[csf('job_no_mst')];
		}
	}
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="920" class="rpt_table" >
		<thead>
			<th width="40">SL</th>
			<th width="90">Batch No</th>
			<th width="80">Extention No</th>
			<th width="80">Batch Date</th>
			<th width="80">Batch Qnty</th>
			<th width="115">Booking No</th>
			<th width="110">Color</th>
			<th width="170">Po/FSO No</th>
			<th width="60">File No</th>
			<th width="">Ref. No</th>
		</thead>
	</table>
	<div style="width:920px; overflow-y:scroll; max-height:250px;" id="buyer_list_view" align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" id="tbl_list_search" >
			<?
			$i=1;
			foreach ($nameArray as $selectResult)
			{
				$po_no='';  $file_no='';  $ref_no=''; $job_array=array(); $sales_order_type=0;
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$is_sales = $selectResult[csf('is_sales')];
				if($is_sales == 1){
					$po_no=$selectResult[csf('sales_order_no')];
					$file_no="";
					$ref_no="";
					$job_no=$selectResult[csf('sales_order_no')];
					$sales_order_type = $selectResult[csf('sales_order_type')];
				}else{
					$order_id=array_unique(explode(",",$order_id_arr[$selectResult[csf('id')]]));
					foreach($order_id as $value)
					{
						if($po_no=='') $po_no=$po_arr[$value]['po_no']; else $po_no.=",".$po_arr[$value]['po_no'];
						if($file_no=='') $file_no=$po_arr[$value]['file']; else $file_no.=",".$po_arr[$value]['file'];
						if($ref_no=='') $ref_no=$po_arr[$value]['ref']; else $ref_no.=",".$po_arr[$value]['ref'];
						$job_no=$po_arr[$value]['job_no'];
						if(!in_array($job_no,$job_array))
						{
							$job_array[]=$job_no;
						}
					}
					$job_no=implode(",",$job_array);
				}

				
				if($selectResult[csf('batch_entry_source')]==1)
				{
					$is_unload_dyeing = $unload_dyeing_production_arr[$selectResult[csf('id')]];
				}
				else
				{
					$is_unload_dyeing = 1;
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>,'<? echo $selectResult[csf('batch_no')]; ?>','<? echo $selectResult[csf('booking_no')]; ?>','<? echo $job_no; ?>','<? echo $is_sales; ?>','<? echo $is_unload_dyeing;?>','<? echo $sales_order_type;?>')">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="90" align="center"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
					<td width="80"><p><? if($selectResult[csf('extention_no')]!=0) echo $selectResult[csf('extention_no')]; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($selectResult[csf('batch_date')]); ?></td>
					<td width="80" align="right"><? echo $selectResult[csf('batch_weight')]; ?></td>
					<td width="115" align="center"><p><? echo $selectResult[csf('booking_no')]; ?></p></td>
					<td width="110" align="center"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
					<td width="170" align="center"><p><? echo $po_no; ?></p></td>
					<td width="60"><p><? echo $file_no; ?></p></td>
					<td width=""><p><? echo $ref_no; ?></p></td>
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

if($action=='populate_data_from_batch')
{
	$data = explode("_", str_replace("'", "", $data));
	$batch_id 		= $data[0];
	$determination_id 	= $data[1];
	$body_part_id 	= $data[2];
	$width_type 	= $data[3];
	$gsm_weight 	= $data[4];
	$dia_width 	= $data[5];

	$non_order_buyer_arr=return_library_array( "select id, buyer_id from wo_non_ord_samp_booking_mst",'id','buyer_id');

	if ($db_type==0) {
		$dia_cond_c = (trim($dia_width)!="")?" and c.dia_width = '$dia_width'":" and c.dia_width =''";
		$dia_cond = (trim($dia_width)!="")?" and original_width = '$dia_width'":" and width =''";
	}
	elseif ($db_type==2) 
	{
		$dia_cond_c = (trim($dia_width)!="")?" and c.dia_width = '$dia_width'":" and c.dia_width is null";
		$dia_cond = (trim($dia_width)!="")?" and original_width = '$dia_width'":" and width is null";
	}

	$data_array=sql_select("SELECT a.company_id, a.process_id, a.batch_no, a.batch_weight, a.extention_no, a.color_id, a.booking_without_order,b.is_sales,b.po_id,b.prod_id,b.width_dia_type , sum(b.batch_qnty) as batch_qnty,a.booking_no_id,a.service_booking_id,a.service_booking_no, d.sales_order_type from pro_batch_create_mst a left join fabric_sales_order_mst d on a.sales_order_id=d.id and a.is_sales=1,pro_batch_create_dtls b, product_details_master c where a.id=b.mst_id and b.prod_id = c.id and b.mst_id='$batch_id' and c.detarmination_id=$determination_id and b.width_dia_type=$width_type and b.body_part_id=$body_part_id and c.gsm=$gsm_weight $dia_cond_c and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by a.company_id, a.process_id, a.batch_no, a.extention_no, a.batch_weight, a.color_id, a.booking_without_order, b.is_sales, b.po_id, b.prod_id, b.width_dia_type, a.booking_no_id, a.service_booking_id, a.service_booking_no, d.sales_order_type");	

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	foreach($data_array as $row)
	{
		$sales_arr[] = $row[csf("po_id")];
		$prod_id_arr[] = $row[csf("prod_id")];
		$company_id = $row[csf("company_id")];
	}

	$prod_array=array();
	if(!empty($prod_id_arr)){
		$prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where id in(".implode(",",$prod_id_arr).") and item_category_id in(2,13) ");
		foreach($prodData as $row)
		{
			$prod_array[$row[csf('id')]]['desc']=$row[csf('product_name_details')];
			$prod_array[$row[csf('id')]]['comp']=$row[csf('item_description')];
			$prod_array[$row[csf('id')]]['dt_id']=$row[csf('detarmination_id')];
			$prod_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
			$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
			$prod_array[$row[csf('id')]]['uom']=$row[csf('unit_of_measure')];
		}
	}

	$recv_qnty=return_field_value("sum(b.receive_qnty)","inv_receive_master a, pro_finish_fabric_rcv_dtls b"," a.id=b.mst_id and a.entry_form=7 and b.batch_id=$batch_id and b.status_active=1 and b.is_deleted=0 and fabric_description_id=$determination_id and dia_width_type=$width_type and body_part_id=$body_part_id and gsm= $gsm_weight $dia_cond");

	$tot_batch_qnty=$tot_recv_qnty=0;
	foreach ($data_array as $row)
	{
		$process_name = '';
		$process_id_array = explode(",", $row[csf("process_id")]);
		foreach ($process_id_array as $val) {
			if ($process_name == "") $process_name = $conversion_cost_head_array[$val]; else $process_name .= "," . $conversion_cost_head_array[$val];
		}

		$tot_batch_qnty+=$row[csf('batch_qnty')];
		$determination_id=$prod_array[$row[csf('prod_id')]]['dt_id'];
		$gsm=$prod_array[$row[csf('prod_id')]]['gsm'];
		$dia=$prod_array[$row[csf('prod_id')]]['dia'];
		if($row[csf('is_sales')] == 1){
			echo "$('#txt_batch_no').attr('readonly','readonly');\n";
			echo "$('#txt_batch_extantion').attr('readonly','readonly');\n";
			echo "$('#cbo_body_part').attr('disabled','disabled');\n";
		}else{
			$uom_id = $prod_array[$row[csf('prod_id')]]['uom'];
		}

		echo "document.getElementById('txt_process_name').value 				= '".$process_name."';\n";
		echo "document.getElementById('txt_process_id').value 					= '".$row[csf("process_id")]."';\n";
		echo "document.getElementById('txt_batch_no').value 					= '".$row[csf("batch_no")]."';\n";
		echo "document.getElementById('txt_batch_id').value 					= '".$batch_id."';\n";
		echo "document.getElementById('is_sales').value 						= '".$row[csf('is_sales')]."';\n";
		echo "document.getElementById('txt_color').value 						= '".$color_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('txt_batch_extantion').value 				= '".$row[csf("extention_no")]."';\n";
		echo "document.getElementById('txt_service_booking').value 				= '".$row[csf("service_booking_no")]."';\n";
		echo "document.getElementById('service_booking_id').value 				= '".$row[csf("service_booking_id")]."';\n";
		echo "document.getElementById('service_booking_without_order').value 	= '".$row[csf("booking_without_order")]."';\n";

		echo "document.getElementById('batch_booking_without_order').value 		= '".$row[csf("booking_without_order")]."';\n";

		echo "document.getElementById('sales_order_type').value 		= '".$row[csf("sales_order_type")]."';\n";


		if($row[csf("booking_without_order")]==1)
		{
			echo "$('#txt_production_qty').removeAttr('readonly','readonly');\n";
			echo "$('#txt_reject_qty').removeAttr('readonly','readonly');\n";
			echo "$('#txt_grey_used').removeAttr('readonly','readonly');\n";
			echo "$('#txt_production_qty').removeAttr('onClick','onClick');\n";
			echo "$('#txt_production_qty').removeAttr('ondblclick','ondblclick');\n";
			echo "$('#txt_production_qty').removeAttr('placeholder','placeholder');\n";

			echo "document.getElementById('buyer_name').value 				= '".$buyer_arr[$non_order_buyer_arr[$row[csf("booking_no_id")]]]."';\n";
			echo "document.getElementById('buyer_id').value 				= '".$non_order_buyer_arr[$row[csf("booking_no_id")]]."';\n";
		}
		else
		{
			echo "$('#txt_production_qty').attr('readonly','readonly');\n";
			echo "$('#txt_reject_qty').attr('readonly','readonly');\n";
			echo "$('#txt_grey_used').attr('readonly','readonly');\n";
			echo "$('#txt_production_qty').attr('onClick','openmypage_po();');\n";
			echo "$('#txt_production_qty').attr('placeholder','Single Click to Search');\n";
		}
	}

	echo "$('#txt_production_qty').val('');\n";
	echo "$('#all_po_id').val('');\n";
	echo "$('#save_data').val('');\n";

	$variable_set_production=sql_select("select distribute_qnty, production_entry from variable_settings_production where variable_list =51 and company_name=$company_id and item_category_id=2 and auto_update=1 and status_active=1");

	$over_receive_limit = !empty($variable_set_production) ? $variable_set_production[0][csf('distribute_qnty')] : 0;

	$over_receive_limit_qnty_kg=0;
	if($variable_set_production[0][csf('production_entry')]*1 >0)
	{
		$over_receive_limit_qnty_kg =$variable_set_production[0][csf('production_entry')];
	}

	$show_yet_to_recv_qnty=$tot_batch_qnty-$recv_qnty;
	echo "document.getElementById('show_txt_batch_qnty').value 			= '".number_format($tot_batch_qnty,2,".","")."';\n";
	echo "document.getElementById('show_txt_yet_receive').value 		= '".number_format($show_yet_to_recv_qnty,2,".","")."';\n";



	if($over_receive_limit_qnty_kg>0)
	{
		echo "$('#over_production').text('Over Production ".$over_receive_limit_qnty_kg." KG').css('color','red');\n";
		$tot_batch_qnty = $over_receive_limit_qnty_kg + $tot_batch_qnty;
	}
	else if($over_receive_limit >0)
	{
		echo "$('#over_production').text('Over Production ".$over_receive_limit."%').css('color','red');\n";
		$tot_batch_qnty = ($over_receive_limit*$tot_batch_qnty)/100 + $tot_batch_qnty;
	}
	
	$yet_to_recv_qnty=$tot_batch_qnty-$recv_qnty;
	echo "document.getElementById('txt_batch_qnty').value 				= '".number_format($tot_batch_qnty,2,".","")."';\n";


	echo "document.getElementById('txt_total_received').value 			= '".number_format($recv_qnty,2,".","")."';\n";
	echo "document.getElementById('txt_yet_receive').value 				= '".number_format($yet_to_recv_qnty,2,".","")."';\n";




	exit();
}

if($action=='batch_data_display')
{
	$recv_qnty=return_field_value("sum(b.receive_qnty)","inv_receive_master a, pro_finish_fabric_rcv_dtls b"," a.id=b.mst_id and a.entry_form=7 and b.batch_id=$data and b.status_active=1 and b.is_deleted=0");
	$batch_qnty=return_field_value("sum(batch_qnty)","pro_batch_create_dtls","mst_id=$data and status_active=1 and is_deleted=0");
	$yet_to_recv_qnty=$batch_qnty-$recv_qnty;
	echo "document.getElementById('txt_batch_qnty').value 				= '".number_format($batch_qnty,2,".","")."';\n";
	echo "document.getElementById('txt_total_received').value 			= '".number_format($recv_qnty,2,".","")."';\n";
	echo "document.getElementById('txt_yet_receive').value 				= '".number_format($yet_to_recv_qnty,2,".","")."';\n";
	//echo create_drop_down( "cbo_body_part", 182, $body_part,"", 1, "-- Select Body Part --", 0, "",0 );
	$cbo_body_part = create_drop_down( 'cbo_body_part', 182, $body_part,'', 1, '-- Select Body Part --', 0, '',0 );
	echo "document.getElementById('body_part_td').innerHTML = '".$cbo_body_part."';\n";
	exit();
}
if($action=='check_batch_no_in_delivery')
{
	$data=explode("**",$data);
	$sql_batch_from_delivery=sql_select("select batch_id from pro_grey_prod_delivery_dtls where batch_id=$data[0] and entry_form=54 and status_active=1 and is_deleted=0");
	$batchID=0;
	foreach ($sql_batch_from_delivery as $value) {
		$batchID=1;
	}
	if ($batchID==1) {
		if($data[1]==1){
			//echo "$('#cbouom').attr('disabled',true);\n";
		}
	}
	exit();
}
if($action=='show_fabric_desc_listview')
{
	$batch_info = explode("_",$data);
	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id order by b.id asc";
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
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}

	$sales_arr=$prod_id_arr=$roll_id_arr=$program_arr=$sales_dtls_ids_arr=array();

	if ($db_type==0)
	{
		$data_array=sql_select("SELECT a.color_id,concat(b.po_id,',') as po_id,concat(b.prod_id,',') as prod_id, c.detarmination_id, b.body_part_id,concat(b.roll_id,',') as roll_id, sum(b.batch_qnty) as qnty,b.width_dia_type,c.item_description,c.unit_of_measure, c.product_name_details, c.detarmination_id, c.gsm, c.dia_width 
		from pro_batch_create_mst a,pro_batch_create_dtls b,product_details_master c 
		where a.id=b.mst_id and b.prod_id=c.id and a.id='$batch_info[0]' and b.status_active=1 and b.is_deleted=0 
		group by a.color_id, c.detarmination_id,b.body_part_id, b.width_dia_type,c.item_description,c.unit_of_measure, c.product_name_details, c.detarmination_id, c.gsm, c.dia_width ");
	}
	else
	{
		$data_array=sql_select("SELECT a.color_id,listagg(b.po_id, ',') within group (order by b.po_id) as po_id,listagg(b.prod_id, ',') within group (order by b.prod_id) as prod_id, c.detarmination_id, b.body_part_id,listagg(b.roll_id, ',') within group (order by b.roll_id) as roll_id, sum(b.batch_qnty) as qnty,b.width_dia_type,c.item_description,c.unit_of_measure, c.product_name_details, c.detarmination_id, c.gsm, c.dia_width from pro_batch_create_mst a,pro_batch_create_dtls b,product_details_master c 
		where a.id=b.mst_id and b.prod_id=c.id and a.id='$batch_info[0]' and b.status_active=1 and b.is_deleted=0 
		group by a.color_id, c.detarmination_id,b.body_part_id, b.width_dia_type ,c.item_description,c.unit_of_measure, c.product_name_details, c.detarmination_id, c.gsm, c.dia_width  
		order by   b.body_part_id");
	}

	foreach($data_array as $row)
	{
		$sales_arr[$row[csf("po_id")]] = $row[csf("po_id")];
		$prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
	}

	$prod_array=array();
	if(!empty($prod_id_arr))
	{
		$prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where id in(".implode(",",$prod_id_arr).") and item_category_id in(2,13)");
		foreach($prodData as $row)
		{
			$prod_array[$row[csf('id')]]['desc']=$row[csf('product_name_details')];
			$prod_array[$row[csf('id')]]['comp']=$row[csf('item_description')];
			$prod_array[$row[csf('id')]]['dt_id']=$row[csf('detarmination_id')];
			$prod_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
			$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
			$prod_array[$row[csf('id')]]['uom']=$row[csf('unit_of_measure')];
		}
	}

	if($batch_info[1] == 1 && !empty($sales_arr))
	{
		$fabric_uom=array();
		$salesArr= implode(",",array_unique(array_filter($sales_arr)));
		$salesArr=chop($salesArr,",");
		$get_uom_from_sales_order = sql_select("SELECT a.id,b.id dtls_id,b.determination_id,b.cons_uom,b.width_dia_type,b.color_id, b.body_part_id, b.avg_rate, a.currency_id 
		from fabric_sales_order_mst a,fabric_sales_order_dtls b 
		where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.id in(".implode(",",array_unique($sales_arr)).") group by a.id,b.id,b.determination_id,b.cons_uom,b.width_dia_type,b.color_id,b.body_part_id, b.avg_rate, a.currency_id");
		foreach ($get_uom_from_sales_order as $uom_row) 
		{
			//$fabric_uom[$uom_row[csf('determination_id')]][$uom_row[csf('width_dia_type')]][$uom_row[csf('color_id')]]=$uom_row[csf('cons_uom')];
			$fabric_uom[$uom_row[csf('determination_id')]][$uom_row[csf('width_dia_type')]]=$uom_row[csf('cons_uom')];
			$fso_currency[$uom_row[csf('id')]]=$uom_row[csf('currency_id')];
			$sales_rate_arr[$uom_row[csf('id')]][$uom_row[csf('body_part_id')]][$uom_row[csf('determination_id')]][$uom_row[csf('color_id')]]=$uom_row[csf('avg_rate')];
		}
	}

	$fabric_store_auto_update=return_field_value("auto_update","variable_settings_production","company_name='$batch_info[2]' and variable_list=15 and item_category_id=2 and is_deleted=0 and status_active=1");
	//if($fabric_store_auto_update==2) $fabric_store_auto_update=0; else $fabric_store_auto_update=1;
	if($fabric_store_auto_update==1) $fabric_store_auto_update=$fabric_store_auto_update; else $fabric_store_auto_update=0;

	$production_sql="SELECT b.batch_id, b.fabric_description_id, b.original_gsm, b.original_width, b.body_part_id, b.color_id, b.dia_width_type, sum(b.receive_qnty) as production_qty 
	FROM INV_RECEIVE_MASTER a, pro_finish_fabric_rcv_dtls b
	WHERE a.id=b.mst_id and A.ENTRY_FORM=7 and A.ITEM_CATEGORY=2 and A.STATUS_ACTIVE=1 and A.IS_DELETED=0  and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and B.BATCH_ID='$batch_info[0]'
	group by B.BATCH_ID, B.ORIGINAL_GSM, B.ORIGINAL_WIDTH, b.FABRIC_DESCRIPTION_ID, b.BODY_PART_ID, b.COLOR_ID, b.dia_width_type";
	// echo $production_sql;
	$production_sql_result=sql_select($production_sql);
	foreach ($production_sql_result as $key => $value) 
	{
		$production_qty_arr[$value[csf('fabric_description_id')]][$value[csf('original_gsm')]][$value[csf('original_width')]][$value[csf('body_part_id')]][$value[csf('color_id')]][$value[csf('dia_width_type')]]['production_qty']+=$value[csf('production_qty')];
	}

	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="510">
		<thead>
			<th width="30">SL</th>
			<th width="180">Fabric Description</th>
			<th width="100">Dia/ W. Type</th>
			<th width="80">Qnty</th>
			<th width="80">Production Qnty</th>
			<th>Balance</th>
		</thead>
		<tbody>
			<?
			$i=1;
			foreach($data_array as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				// $determination_id=$prod_array[$row[csf('prod_id')]]['dt_id'];
				// $gsm=$prod_array[$row[csf('prod_id')]]['gsm'];
				// $dia=$prod_array[$row[csf('prod_id')]]['dia'];

				$determination_id=$row[csf('detarmination_id')];
				$gsm=$row[csf('gsm')];
				$dia=$row[csf('dia_width')];

				$production_qty=$production_qty_arr[$determination_id][$gsm][$dia][$row[csf('body_part_id')]][$row[csf('color_id')]][$row[csf('width_dia_type')]]['production_qty'];

				if($batch_info[1] == 1){ // if sales order
					//$uom_id = $fabric_uom[$determination_id][$row[csf('width_dia_type')]][$row[csf('color_id')]];
					$uom_id = $fabric_uom[$determination_id][$row[csf('width_dia_type')]];
				}else{
					//$uom_id = $prod_array[$row[csf('prod_id')]]['uom'];
					$uom_id = $row[csf('unit_of_measure')];
				}

				if($determination_id==0 || $determination_id=="")
				{
					//$comps=$prod_array[$row[csf('prod_id')]]['comp'];
					$comps=$row[csf('item_description')];
				}
				else
				{
					$comps=$composition_arr[$determination_id];
				}

				$prod_id=$row[csf('prod_id')];
				$width_dia_type=$row[csf('width_dia_type')];

				//[note: fab desc. not check with determination id when click set_form_data() [Jahid Hasan]]
				// $prod_array[$row[csf('prod_id')]]['comp']
				$rate="";
				if($fabric_store_auto_update == 1)
				{
					$rate  = $sales_rate_arr[$row[csf('po_id')]][$row[csf('body_part_id')]][$determination_id][$row[csf('color_id')]];
					$currency_id = $fso_currency[$row[csf('po_id')]];
				}

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $row[csf('product_name_details')]."**".$determination_id."**".$gsm."**".$dia."**".$prod_id."**".$width_dia_type."**".$row[csf('qnty')]."**".$uom_id."**".$row[csf('body_part_id')]."**".$batch_info[1]."**".$rate."**".$currency_id; ?>")' style="cursor:pointer">
					<td><? echo $i; ?></td>
					<td title="ProdID=<? echo $row[csf('prod_id')];?>"><? echo $body_part[$row[csf('body_part_id')]].", ".$row[csf('product_name_details')]; ?></td>
					<td><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?></td>
					<td align="right"><? echo number_format($row[csf('qnty')],2,'.',''); ?></td>
					<td align="right"><? echo number_format($production_qty,2,'.',''); ?></td>
					<td align="right"><? echo number_format($row[csf('qnty')]-$production_qty,2,'.',''); ?></td>
				</tr>
				<?
				$i++;
			}
			?>
		</tbody>
	</table>
	<?
	exit();
}


if ($action=="fabricDescription_popup")
{
	echo load_html_head_contents("Fabric Description Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>

		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});

		function js_set_value(comp,gsm,detarmination_id)
		{
			$('#hidden_desc_no').val(comp);
			$('#hidden_gsm').val(gsm);
			//$('#hidden_dia_width').val(dia_width);
			$('#fabric_desc_id').val(detarmination_id);
			parent.emailwindow.hide();
		}

	</script>

</head>

<body>
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:520px;margin-left:10px">
			<input type="hidden" name="hidden_desc_no" id="hidden_desc_no" class="text_boxes" value="">
			<input type="hidden" name="hidden_gsm" id="hidden_gsm" class="text_boxes" value="">
			<input type="hidden" name="hidden_dia_width" id="hidden_dia_width" class="text_boxes" value="">
			<input type="hidden" name="fabric_desc_id" id="fabric_desc_id" class="text_boxes" value="">

			<div style="margin-left:10px; margin-top:10px">
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="480">
					<thead>
						<th width="40">SL</th>
						<th width="120">Construction</th>
						<th>Composition</th>
						<th width="100">GSM/Weight</th>
					</thead>
				</table>
				<div style="width:500px; max-height:280px; overflow-y:scroll" id="list_container" align="left">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="480" id="tbl_list_search">
						<?
						$composition_arr=array();
						$compositionData=sql_select("select mst_id, copmposition_id, percent from lib_yarn_count_determina_dtls where status_active=1 and is_deleted=0 order by id asc");
						foreach( $compositionData as $row )
						{
							$composition_arr[$row[csf('mst_id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
						}

						$i=1;
						$data_array=sql_select("select id,construction,fab_nature_id,gsm_weight from lib_yarn_count_determina_mst where fab_nature_id=2 and status_active=1 and is_deleted=0 order by id asc");
						foreach($data_array as $row)
						{
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							//unit_of_measure
							/*$comp='';$construction=$row[csf('construction')];
                            $determ_sql=sql_select("select copmposition_id, percent from lib_yarn_count_determina_dtls where mst_id=".$row[csf('id')]." and status_active=1 and is_deleted=0");
                            foreach( $determ_sql as $d_row )
                            {
                                $comp.=$composition[$d_row[csf('copmposition_id')]]." ".$d_row[csf('percent')]."% ";
                            }*/
                            $comp=$composition_arr[$row[csf('id')]];
                            $cons_comp=$row[csf('construction')].", ".$comp;
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $cons_comp; ?>','<? echo $row[csf('gsm_weight')]; ?>','<? echo $row[csf('id')]; ?>')" style="cursor:pointer" >
                            	<td width="40"><? echo $i; ?></td>
                            	<td width="120"><p><? echo $row[csf('construction')]; ?></p></td>
                            	<td><p><? echo $comp; ?></p></td>
                            	<td width="100"><? echo $row[csf('gsm_weight')]; ?></td>
                            </tr>
                            <?
                            $i++;
                        }
                        ?>
                    </table>
                </div>
            </div>
        </fieldset>
    </form>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action=="po_popup")
{
	echo load_html_head_contents("PO Info", "../../", 1, 1,'','','');

	extract($_REQUEST);
	$data=explode("_",$data);
	// print_r($data);
	$po_id = $data[0]; $type = $data[1];
	// echo $type.'Tipu';
	if($type==1)
	{
		$dtls_id=$data[2];
		$roll_maintained=$data[3];
		$save_data=$data[4];
		$prev_distribution_method=$data[5];
		$production_basis=$data[6];

		$cbo_body_part=$data[7];
		$fabric_desc_id=$data[8];
	}
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	$variable_set_production=sql_select("select distribute_qnty, auto_update, production_entry from variable_settings_production where variable_list =51 and company_name=$cbo_company_id and item_category_id=2 and status_active=1");  //and auto_update=1


	$production_vali_source_arr	=sql_select("select auto_update from variable_settings_production where variable_list=55 and company_name=$cbo_company_id and status_active=1");
	$production_vali_source 	= ($production_vali_source_arr[0][csf('auto_update')] == 2) ? 2 : 1;
	//N.B. if production validation source is batch then only batch validation will work, then all other validation work as well as it is


	$process_loss_method_variable	=sql_select("select process_loss_method from variable_order_tracking where company_name=$cbo_company_id and variable_list=18 and item_category_id=2 and status_active =1");
	$process_loss_method = ($process_loss_method_variable[0][csf("process_loss_method")] ==2) ? 2: 1;

	$over_receive_limit_qnty_kg=$over_receive_limit=0;
	if($variable_set_production[0][csf('production_entry')]*1 >0)
	{
		$over_receive_limit_qnty_kg = $variable_set_production[0][csf('production_entry')];
	}
	else if($variable_set_production[0][csf('distribute_qnty')]*1 >0)
	{
		$over_receive_limit = $variable_set_production[0][csf('distribute_qnty')];
	}

	$is_over_receive_unlimited = ($variable_set_production[0][csf('auto_update')] == 3) ? 1 : 0;
	if($is_over_receive_unlimited == 1)
	{
		$over_receive_limit=0;
		$over_receive_limit_qnty_kg=0;
	}


	/*	
		###
		|
		|	if over production variable is set to unlimited then required quantity and batch quantity validation will be overlooked or not checked as per decision of COO
		|	
		###
	*/

	//echo "string=".$is_over_receive_unlimited;die;
	?>
	<script>
		var production_basis=<? echo $production_basis; ?>;
		var roll_maintained=<? echo $roll_maintained; ?>;
		var overRecLim = <? echo $over_receive_limit; ?>;
		var overRecLimQntyKG = <? echo $over_receive_limit_qnty_kg; ?>;
		var isOverRcvUnlimited = <? echo $is_over_receive_unlimited; ?>;
		var productionValiSource = <? echo $production_vali_source; ?>;
		var process_loss_method = <? echo $process_loss_method; ?>;

		//var fabric_store_auto_update = <? echo $fabric_store_auto_update; ?>; 
		//var process_costing_maintain = <? echo $process_costing_maintain; ?>;
		function distribute_qnty(str)
		{
			//alert(str+'='+roll_maintained);
			if(str==1 && roll_maintained==0)
			{
				var tot_po_qnty=$('#tot_po_qnty').val()*1;
				var tot_requ_qnty=$('#tot_requ_qnty').val()*1;
				var txt_prop_finish_qnty=$('#txt_prop_finish_qnty').val()*1;
				var txt_prop_reject_qnty=$('#txt_prop_reject_qnty').val()*1;
				var txt_prop_grey_qnty=$('#txt_prop_grey_qnty').val()*1;
				var tblRow = $("#tbl_list_search tr").length;
				var len=totalFinish=0;
				var totalReject=0;
				var totalGrey=0;
				var rowNo=1;
				var grey_qnty=0;
				$("#tbl_list_search").find('tr').each(function()
				{
					len=len+1;

					var requ_qnty=$(this).find('input[name="txtfinish_required_qnty[]"]').val()*1;
					if(tot_requ_qnty > 0)
					{
						var perc=(requ_qnty/tot_requ_qnty)*100;
					}
					else if(production_basis == 5)
					{
						//N.B. here po_qnty is batch quantity
						var po_qnty=$(this).find('input[name="txtfinish_fabric_Qnty[]"]').val()*1; 
						var perc=(po_qnty/tot_po_qnty)*100;
					}
					else
					{
						var perc = 0;
					}
					perc = perc.toFixed(2)

					//alert("requ="+requ_qnty+",tot f="+tot_requ_qnty+",po_qnty="+po_qnty+",perc="+perc+'\n'+",txt_prop_finish_qnty="+txt_prop_finish_qnty);

					var finish_qnty=(perc*txt_prop_finish_qnty)/100;
					totalFinish = totalFinish*1+finish_qnty*1;
					totalFinish = totalFinish.toFixed(2);

					var reject_qnty=(perc*txt_prop_reject_qnty)/100;
					totalReject = totalReject*1+reject_qnty*1;
					totalReject = totalReject.toFixed(2);

					if(txt_prop_grey_qnty)
					{
						$(this).find('input[name="txtProcessQnty[]"]').val("");
						//As Total Grey given so at first process losses are cleared and will calculate through wgtlostcalculation();

						grey_qnty=(perc*txt_prop_grey_qnty)/100;
						totalGrey = totalGrey*1+grey_qnty*1;
						totalGrey = totalGrey.toFixed(2);
					}
					
					var txtProcessQnty=$(this).find('input[name="txtProcessQnty[]"]').val()*1;
					//alert(process_loss_method);
					if(txtProcessQnty)
					{
						grey_qnty=0;
						if(process_loss_method==1)
						{
							grey_qnty = (finish_qnty + reject_qnty) + (finish_qnty + reject_qnty) * txtProcessQnty/100;
						}
						else
						{
							grey_qnty = (finish_qnty + reject_qnty) / (1 - txtProcessQnty/100);
						}
					}

					if(tblRow==len)
					{
						var balance = txt_prop_finish_qnty-totalFinish;
						if(balance!=0) totalFinish=totalFinish*1+(balance*1);

						var rejbalance = txt_prop_reject_qnty-totalReject;
						if(rejbalance!=0) totalReject=totalReject*1+(rejbalance*1);

						var greybalance = txt_prop_grey_qnty-totalGrey;
						if(greybalance!=0) totalGrey=totalGrey*1+(greybalance*1);
					}

					if(production_basis==5)
					{
						var fabric_qnty = $(this).find('input[name="txtfinish_fabric_Qnty[]"]').val()*1;
						$(this).find('input[name="txtfinishQnty[]"]').val(finish_qnty.toFixed(2));
						$(this).find('input[name="txtrejectQnty[]"]').val(reject_qnty.toFixed(2));
						$(this).find('input[name="txtgreyQnty[]"]').val(grey_qnty.toFixed(2));
						 //alert(">>finish_qnty="+finish_qnty+",fabric_qnty fin="+fabric_qnty);
					}
					else
					{
						$(this).find('input[name="txtfinishQnty[]"]').val(finish_qnty.toFixed(2));
						$(this).find('input[name="txtrejectQnty[]"]').val(reject_qnty.toFixed(2));
						$(this).find('input[name="txtgreyQnty[]"]').val(grey_qnty.toFixed(2));
					}

					wgtlostcalculation();fn_check_balance(rowNo);
					rowNo=rowNo+1;
				});
			}
			else
			{
				$('#txt_prop_finish_qnty').val('');
				$('#txt_prop_reject_qnty').val('');
				$('#txt_prop_grey_qnty').val('');
				$('#tot_grey_qty').val('');
				$('#tot_wgtlost_qty').val('');

				$("#tbl_list_search").find('tr').each(function()
				{
					$(this).find('input[name="txtfinishQnty[]"]').val('');
					$(this).find('input[name="txtrejectQnty[]"]').val('');
					$(this).find('input[name="txtgreyQnty[]"]').val('');
					$(this).find('input[name="txtwgtlostQnty[]"]').val('');
				});
			}
		}

		function copy_process(sl)
		{
			sl = sl*1;
			if($('#process_loss_chkbox').is(':checked'))
			{
				var data_value = $('#txtProcessQnty_'+sl).val();
				$("#tbl_list_search").find('tbody tr').each(function()
				{
					var txtfinishQnty = $(this).find('input[name="txtfinishQnty[]"]').attr("id");
					var txtfinishQntyArr = txtfinishQnty.split("_");

					// copy only that and below  data
					if( sl <= txtfinishQntyArr[1]*1 )
					{
						$(this).find('input[name="txtProcessQnty[]"]').val(data_value);
					}
				});
			}
		}

		function wgtlostcalculation(id_no='')
		{
			var totalgrey_qty = 0; var total_wgtlostQnty=0;
			$("#tbl_list_search").find('tr').each(function()
			{
				var txtfinishQnty = $(this).find('input[name="txtfinishQnty[]"]').val()*1;
				var txtrejectQnty = $(this).find('input[name="txtrejectQnty[]"]').val()*1;
				var txtgreyQnty = $(this).find('input[name="txtgreyQnty[]"]').val()*1;

				var txtBatchQnty = $(this).find('input[name="txtBatchQnty[]"]').val()*1;
				var preGreyUsed = $(this).find('input[name="preGreyUsed[]"]').val()*1;
				// alert(txtgreyQnty);

				//var txtgreyQnty=0;
				var txtProcessQnty=$(this).find('input[name="txtProcessQnty[]"]').val()*1;
				//alert(txtProcessQnty);

				if(txtfinishQnty)
				{
					if(txtProcessQnty)
					{
						if(process_loss_method==1)
						{
							txtgreyQnty = (txtfinishQnty + txtrejectQnty) + (txtfinishQnty + txtrejectQnty) * txtProcessQnty/100;
						}
						else
						{
							txtgreyQnty = (txtfinishQnty + txtrejectQnty) / (1 - txtProcessQnty/100);
						}

						if((txtgreyQnty > txtBatchQnty -preGreyUsed) )
						{
							txtgreyQnty = txtBatchQnty -preGreyUsed;

							if(process_loss_method==1)
							{
								txtProcessQnty = ((100*txtgreyQnty)/(txtfinishQnty + txtrejectQnty)) -100;
							}
							else
							{
								txtProcessQnty = 100 - 100*(txtfinishQnty + txtrejectQnty)/txtgreyQnty;
							}
							//alert('process='+txtProcessQnty);
							
						}
					}
					else if(txtgreyQnty)
					{
						if((txtgreyQnty > txtBatchQnty -preGreyUsed) )
						{
							txtgreyQnty = txtBatchQnty -preGreyUsed;
						}
							
						if(process_loss_method==1)
						{
							txtProcessQnty = ((100*txtgreyQnty)/(txtfinishQnty + txtrejectQnty)) -100;
						}
						else
						{
							txtProcessQnty = 100 - 100*(txtfinishQnty + txtrejectQnty)/txtgreyQnty;
						}
						//alert('process='+txtProcessQnty);
					}

					$(this).find('input[name="txtgreyQnty[]"]').val(txtgreyQnty);
					$(this).find('input[name="txtProcessQnty[]"]').val(txtProcessQnty)
				}
				else
				{
					$(this).find('input[name="txtgreyQnty[]"]').val("");
					$(this).find('input[name="txtProcessQnty[]"]').val("")
				}




				totalgrey_qty = totalgrey_qty * 1 + txtgreyQnty * 1;

				if(txtgreyQnty>0){
					var calwgtlostQnty = txtgreyQnty-(txtfinishQnty+txtrejectQnty);
					var wgtlost = calwgtlostQnty
					$(this).find('input[name="txtwgtlostQnty[]"]').val(wgtlost.toFixed(2));

					total_wgtlostQnty +=wgtlost;
				}
				

				
			});

			$('#tot_grey_qty').val(totalgrey_qty.toFixed(2));
			$('#tot_wgtlost_qty').val(total_wgtlostQnty.toFixed(2));
		}

		function fn_check_balance(rowNo)
		{
			var row_num = $('#tbl_list_search tr').length;
			var po_id = $('#txtPoId_' + rowNo).val();
			var total_greyQty = 0;  var total_wgtlostQnty = ''; var total_batch_po_quantity = 0;
			if(roll_maintained==0)
			{
				for (var j = 1; j <= row_num; j++)
				{
					var txtBatchQnty = $('#txtBatchQnty_' + j).val();
					total_batch_po_quantity = total_batch_po_quantity*1 + txtBatchQnty*1;

					var po_id_check = $('#txtPoId_' + j).val();
					var txtgreyQnty = $('#txtgreyQnty_' + j).val()*1;
					total_greyQty = total_greyQty * 1 + txtgreyQnty * 1;
					total_wgtlostQnty = total_wgtlostQnty * 1 + $('#txtwgtlostQnty_' + j).val() * 1;
				}

				//alert(total_greyQty.toFixed(2));
			}
			else
			{
				//alert(total_greyQty.toFixed(2));
				total_batch_po_quantity = $('#txtBatchQnty_' + rowNo).val() * 1;

				total_greyQty = $('#txtgreyQnty_' + rowNo).val() * 1;
				total_wgtlostQnty = $('#txtwgtlostQnty_' + rowNo).val() * 1;
			}
			//alert(total_greyQty.toFixed(2));

			if(overRecLimQntyKG*1 > 0)
			{
				total_batch_po_quantity = overRecLimQntyKG*1 + total_batch_po_quantity*1;
			}
			else
			{
				total_batch_po_quantity = (overRecLim*total_batch_po_quantity*1)/100 + total_batch_po_quantity*1;
			}

			if(total_batch_po_quantity < total_greyQty)
			{
				$('#tot_grey_qty').val("");
				$('#tot_wgtlost_qty').val("");

				$('[name="txtgreyQnty[]"]').val("");
				$('[name="txtwgtlostQnty[]"]').val("");
			}
			else
			{
				$('#tot_grey_qty').val(total_greyQty.toFixed(2));
				$('#tot_wgtlost_qty').val(total_wgtlostQnty.toFixed(2));
			}

		}

		var selected_id = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i,1 );
			}
		}

		function check_po_batch_fabric_qnty(event,finish_fabric_qnty)
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				var _this = event;
				var text_id =  $(_this).attr('id');
				var id_prifix=text_id.split("_");

				var text_finish_qnty = ($("#"+text_id).val()*1)+($("#previous_recieve_qty_"+id_prifix[1]).val()*1);
				//alert(text_finish_qnty)
				
				var txtfinish_required_qnty = $("#txtfinish_required_qnty"+id_prifix[1]).val();

				if(overRecLimQntyKG*1 > 0)
				{
					txtfinish_required_qnty = overRecLimQntyKG*1 + txtfinish_required_qnty*1;
				}
				else
				{
					txtfinish_required_qnty = (overRecLim*txtfinish_required_qnty*1)/100 + txtfinish_required_qnty*1;
				}

				if(isOverRcvUnlimited ==0)
				{
					if(production_basis == 4)
					{
						if(text_finish_qnty>txtfinish_required_qnty*1)
						{
							alert("Finish qnty can not greater than fabric required qnty.\nrequired quantity : "+txtfinish_required_qnty);
							$("#"+text_id).val(0);
						}
					}
					else if(production_basis == 5)
					{
						if(productionValiSource==1)
						{
							if(txtfinish_required_qnty*1 > 0)
							{
								if(text_finish_qnty>txtfinish_required_qnty*1)
								{
									alert("Finish qnty can not greater than fabric required qnty.\nrequired quantity : "+txtfinish_required_qnty);
									$("#"+text_id).val(0);
								}
							}
						}

						if(overRecLimQntyKG*1 > 0)
						{
							finish_fabric_qnty = overRecLimQntyKG*1 + finish_fabric_qnty*1;
						}
						else
						{
							finish_fabric_qnty = (overRecLim*finish_fabric_qnty*1)/100 + finish_fabric_qnty*1;
						}
						
						if(text_finish_qnty>finish_fabric_qnty*1)
						{
							alert("Finish qnty can not greater than fabric batch qnty");
							if((finish_fabric_qnty*1)-($("#previous_recieve_qty_"+id_prifix[1]).val()*1))
							{
								$("#"+text_id).val(((finish_fabric_qnty*1)-($("#previous_recieve_qty_"+id_prifix[1]).val()*1)).toFixed(2));
							}
							else
							{
								$("#"+text_id).val(0);
							}

						}
					}
				}
			}
		}

		function toggle( x, origColor )
		{
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
				for(var i=0; i<old.length; i++)
				{
					js_set_value( old[i],0 )
				}
			}
		}

		function js_set_value( str )
		{
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

		function show_finish_fabric_recv()
		{
			var po_id=$('#po_id').val();
			show_list_view ( po_id+'_'+'1'+'_'+'<? echo $dtls_id; ?>'+'_'+'<? echo $roll_maintained; ?>'+'_'+'<? echo $save_data; ?>'+'_'+'<? echo $prev_distribution_method; ?>'+'_'+'<? echo $production_basis; ?>'+'_'+'<? echo $cbo_body_part; ?>'+'_'+'<? echo $fabric_desc_id; ?>', 'po_popup', 'search_div', 'finish_fabric_receive_controller', '');
		}

		function hidden_field_reset()
		{
			$('#po_id').val('');
			$('#save_string').val( '' );
			$('#tot_finish_qnty').val( '' );
			$('#tot_reject_qnty').val( '' );
			$('#tot_grey_qnty').val( '' );
			$('#tot_wgtlost_qnty').val( '' );
			selected_id = new Array();
		}

		function fnc_close()
		{
			var save_string='';	 var tot_finish_qnty=''; var tot_reject_qnty=''; var tot_greyQnty=''; var tot_wgtlost_Qnty=''; var totFinshingQty=0;
			var po_id_array = new Array(); var buyer_id_array = new Array(); var buyer_name_array = new Array();
			var total_batch_qnty = 0;
			var overValueIndependBasis = 0; var overValueBatchBasis = 0; var batchQuantityExceed=0;
			var vali_total_finish=0; var vali_total_pre_rcv = 0; var vali_grey_qnty=0;var checkProcessLossZero=0;
			$("#tbl_list_search").find('tr').each(function()
			{
				var txtfinishQnty=$(this).find('input[name="txtfinishQnty[]"]').val()*1;				

				if(txtfinishQnty > 0)
				{
					var previous_recieve_qty=$(this).find('input[name="previous_recieve_qty[]"]').val()*1;  
					var txtfinish_required_qnty=$(this).find('input[name="txtfinish_required_qnty[]"]').val()*1;
					
					if(overRecLimQntyKG*1 > 0)
					{
						txtfinish_required_qnty = overRecLimQntyKG*1 + txtfinish_required_qnty*1;
					}
					else
					{
						txtfinish_required_qnty = (overRecLim*txtfinish_required_qnty*1)/100 + txtfinish_required_qnty*1;
					}
					
					var finish_fabric_batch_qnty=$(this).find('input[name="txtfinish_fabric_Qnty[]"]').val()*1;

					if(production_basis == 5)
					{
						//finish_fabric_batch_qnty = (overRecLim*finish_fabric_batch_qnty*1)/100 + finish_fabric_batch_qnty*1;
						if(overRecLimQntyKG*1 > 0)
						{
							finish_fabric_batch_qnty = overRecLimQntyKG*1 + finish_fabric_batch_qnty*1;
						}
						else
						{
							finish_fabric_batch_qnty = (overRecLim*finish_fabric_batch_qnty*1)/100 + finish_fabric_batch_qnty*1;
						}

						total_batch_qnty +=finish_fabric_batch_qnty;

						if(txtfinishQnty + previous_recieve_qty > finish_fabric_batch_qnty)
						{
							batchQuantityExceed +=1;
						}

					}
					vali_total_finish +=txtfinishQnty;
					vali_total_pre_rcv +=previous_recieve_qty;

					//alert(txtfinishQnty +' + ' + previous_recieve_qty +' > '+ txtfinish_required_qnty*1 + ' overbatch='+finish_fabric_batch_qnty);
					if( (txtfinishQnty + previous_recieve_qty) > txtfinish_required_qnty*1)
					{
						//if(txtfinish_required_qnty*1 ==0 )
						if(production_basis == 4){
							overValueIndependBasis +=1;
						} 
						else if(txtfinish_required_qnty*1 > 0 && production_basis == 5)
						{
							overValueBatchBasis +=1;
						}
					}

					if(vali_grey_qnty==0)
					{
						var chkTxtGreyQnty= $(this).find('input[name="txtgreyQnty[]"]').val()*1;
						if(chkTxtGreyQnty <=0)
						{
							vali_grey_qnty +=1;
						}
					}
					if(checkProcessLossZero==0)
					{
						var chkTxtProcessLoss= $(this).find('input[name="txtProcessQnty[]"]').val()*1;
						if(chkTxtProcessLoss ==0 || chkTxtProcessLoss =="")
						{
							checkProcessLossZero +=1;
						}
					}

				}
				
			});
			
			if(checkProcessLossZero >0)
			{
				alert("Please write Process Loss");
				return;
			}
			if(vali_grey_qnty >0)
			{
				alert("Required grey quantity");
				return;
			}

			if(overValueIndependBasis >0 && (isOverRcvUnlimited == 0) && production_basis == 4)
			{
				alert("Finish qnty can not greater than fabric required qnty.");
				return;
			}

			if(overValueBatchBasis >0 && (isOverRcvUnlimited == 0) && production_basis == 5 && productionValiSource==1)
			{
				alert("Finish qnty can not greater than fabric required qnty.");
				return;
			}

			if(production_basis == 5 && (isOverRcvUnlimited == 0))
			{
				//total_batch_qnty = (overRecLim*total_batch_qnty*1)/100 + total_batch_qnty*1;

				if(overRecLimQntyKG*1 > 0)
				{
					total_batch_qnty = overRecLimQntyKG*1 + total_batch_qnty*1;
				}
				else
				{
					total_batch_qnty = (overRecLim*total_batch_qnty*1)/100 + total_batch_qnty*1;
				}

				if( (vali_total_finish + vali_total_pre_rcv) >total_batch_qnty*1)
				{
					alert("Total Finish qnty can not greater than total fabric required/batch qnty.");
					return;
				}

				if(batchQuantityExceed >0 )
				{
					alert("Finish qnty can not greater than fabric required/batch qnty.");
					return;
				}
			}

			$("#tbl_list_search").find('tr').each(function()
			{
				var txtPoId=$(this).find('input[name="txtPoId[]"]').val();
				var txtfinishQnty=$(this).find('input[name="txtfinishQnty[]"]').val()*1;
				var txtrejectQnty=$(this).find('input[name="txtrejectQnty[]"]').val()*1;
				var txtgreyQnty=$(this).find('input[name="txtgreyQnty[]"]').val()*1;
				var txtwgtlostQnty=$(this).find('input[name="txtwgtlostQnty[]"]').val()*1;
				var txtProcessQnty=$(this).find('input[name="txtProcessQnty[]"]').val()*1;


				var txtRoll=$(this).find('input[name="txtRoll[]"]').val();
				var buyerId=$(this).find('input[name="buyerId[]"]').val();
				var buyerName=$(this).find('input[name="buyerName[]"]').val();
				var sales_booking_no=$(this).find('input[name="sales_booking_no[]"]').val();

				tot_finish_qnty=tot_finish_qnty*1+txtfinishQnty*1;
				tot_reject_qnty=tot_reject_qnty*1+txtrejectQnty*1;
				tot_greyQnty=tot_greyQnty*1+txtgreyQnty*1;
				tot_wgtlost_Qnty=tot_wgtlost_Qnty*1+txtwgtlostQnty*1;

				totFinshingQty+=txtfinishQnty*1;

				if(roll_maintained==0)
				{
					txtRoll=0;
				}

				if(txtfinishQnty*1>0 || txtrejectQnty*1>0  || txtgreyQnty*1>0 || txtwgtlostQnty*1>0)
				{
					if(save_string=="")
					{
						save_string=txtPoId+"**"+txtfinishQnty+"**"+txtRoll+"**"+txtrejectQnty+"**"+txtgreyQnty+"**"+txtwgtlostQnty+"**"+sales_booking_no+"**"+txtProcessQnty;
					}
					else
					{
						save_string+="!!"+txtPoId+"**"+txtfinishQnty+"**"+txtRoll+"**"+txtrejectQnty+"**"+txtgreyQnty+"**"+txtwgtlostQnty+"**"+sales_booking_no+"**"+txtProcessQnty;
					}

					if( jQuery.inArray( txtPoId, po_id_array) == -1 )
					{
						po_id_array.push(txtPoId);
					}

					if( jQuery.inArray( buyerId, buyer_id_array) == -1 )
					{
						buyer_id_array.push(buyerId);
						buyer_name_array.push(buyerName);
					}
				}
			});
			//alert("failed");return;
			// alert(save_string);return;
			$('#save_string').val( save_string );
			$('#tot_finish_qnty').val(tot_finish_qnty);
			$('#tot_reject_qnty').val(tot_reject_qnty);
			$('#tot_grey_qnty').val(tot_greyQnty);
			$('#tot_wgtlost_qnty').val(tot_wgtlost_Qnty);
			$('#all_po_id').val( po_id_array );
			$('#buyer_id').val( buyer_id_array );
			$('#buyer_name').val( buyer_name_array );
			$('#distribution_method').val( $('#cbo_distribiution_method').val() );

			var fin_del_qty=$('#txt_get_value_delv_entry').val()*1;
			if(fin_del_qty>totFinshingQty)
			{
				alert('You can not decrease Quantity from Current Delv Quantity');
				return;
			}
			parent.emailwindow.hide();
		}

		function clear_process_loss_grey_field(sl)
		{
			$('#txtProcessQnty_'+sl).val('');
			//txtProcessQnty_ txtgreyQnty_
		}
		
	</script>
</head>

<body>
	<?
	if($type!=1)
	{
		//echo $production_basis."test";
		?>
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:940px;margin-left:10px">
				<input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
				<input type="hidden" name="tot_finish_qnty" id="tot_finish_qnty" class="text_boxes" value="">
				<input type="hidden" name="tot_reject_qnty" id="tot_reject_qnty" class="text_boxes" value="">
				<input type="hidden" name="tot_grey_qnty" id="tot_grey_qnty" class="text_boxes" value="">
				<input type="hidden" name="tot_wgtlost_qnty" id="tot_wgtlost_qnty" class="text_boxes" value="">
				<input type="hidden" name="all_po_id" id="all_po_id" class="text_boxes" value="">
				<input type="hidden" name="buyer_id" id="buyer_id" class="text_boxes" value="">
				<input type="hidden" name="buyer_name" id="buyer_name" class="text_boxes" value="">
				<input type="hidden" name="distribution_method" id="distribution_method" class="text_boxes" value="">
				<input type="hidden" name="cbo_body_part" id="cbo_body_part" class="text_boxes" value="<? echo $cbo_body_part;?>">
				<input type="hidden" name="fabric_desc_id" id="fabric_desc_id" class="text_boxes" value="<? echo $fabric_desc_id;?>">
		<?
	}
	//echo $production_basis."==".$type."==".$save_data;die;
	if($production_basis==4 && $type!=1) // // echo "Independent";
	{
		?>
		<div align="center">
			<table cellpadding="0" cellspacing="0" width="620" class="rpt_table" align="center">
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
						echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
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
						<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+'<? echo $all_po_id; ?>'+'_'+'<? echo $booking_no; ?>', 'create_po_search_list_view', 'search_div', 'finish_fabric_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1);hidden_field_reset();');set_all();" style="width:100px;" />
					</td>
				</tr>
			</table>
		</div>
		<div id="search_div" style="margin-top:10px" align="center">
			<?
			if($save_data!="")
			{
				?>
				<div style="width:500px; margin-top:10px" align="center">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="520" align="center">
						
						<thead>
							<? 	
							if($over_receive_limit_qnty_kg)
							{
								?>
								<tr>
									<th colspan="4" style="color: red;">Production Over Limit :  <? echo $over_receive_limit_qnty_kg;?>KG.</th>
								</tr>
								<?	
							}
							else if($over_receive_limit)
							{
								?>
								<tr>
									<th colspan="4" style="color: red;">Production Over Limit :  <? echo $over_receive_limit;?>%.</th>
								</tr>
								<?	
							}
							?>
							<tr>
								<th>Total Finish Qnty</th>
								<th>Total Reject Qnty</th>
								<th>Total Grey Qnty</th>
								<th>Distribution Method</th>
							</tr>
						</thead>
						<tr class="general">
							<td><input type="text" name="txt_prop_finish_qnty" id="txt_prop_finish_qnty" class="text_boxes_numeric" value="<? echo $txt_production_qty; ?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)">
								<input type="hidden" id="txt_get_value_delv_entry" name="txt_get_value_delv_entry">
							</td>
							<td><input type="text" name="txt_prop_reject_qnty" id="txt_prop_reject_qnty" class="text_boxes_numeric" value="<? echo $txt_reject_qty; ?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)"></td>
							<td>
								<input type="text" name="txt_prop_grey_qnty" id="txt_prop_grey_qnty" class="text_boxes_numeric" value="<? echo $txt_grey_used; ?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)" >
								<input type="hidden" id="txt_get_value_delv_entry" name="txt_get_value_delv_entry">
							</td>
							<td>
								<?
								$distribiution_method=array(1=>"Proportionately",2=>"Manually");
								echo create_drop_down( "cbo_distribiution_method", 140, $distribiution_method,"",0, "",$prev_distribution_method, "distribute_qnty(this.value);",0 );
								?>
							</td>
						</tr>
					</table>
				</div>
				<div style="margin-left:10px; margin-top:10px">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="940" align="left">
						<thead>
							<th width="110">PO No</th>
							<th width="60">File No</th>
							<th width="60">Ref. No</th>
							<th width="80">Shipment Date</th>
							<th width="90">PO Qty</th>
							<th width="80">Req Qty</th>
							<th width="95">Finish Qty</th>
							<th width="95">Reject Qty</th>
							<th width="80"><input type="checkbox" name="process_loss_chkbox" id="process_loss_chkbox"> Pro. Loss%</th>
							<th width="70">Grey Qty</th>
							<th width="70">Wgt. Lost</th>
							<?
							if($roll_maintained==1)
							{
								?>
								<th>Roll</th>
								<?
							}
							?>
						</thead>
					</table>
					<div style="width:960px; max-height:280px; overflow-y:scroll" id="list_container" align="left">
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="940" id="tbl_list_search">
							<?
							$i=1; $tot_po_qnty=$tot_grey_qnty=$tot_wgtlost_qty=$tot_requ_qnty=0; $po_array=array();

							$explSaveData = explode("!!",$save_data);
							for($z=0;$z<count($explSaveData);$z++)
							{
								if ($i%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";

								//$save_string=$row_po[csf("po_breakdown_id")]."**".$row_po[csf("quantity")]."**0**".$row_po[csf("returnable_qnty")]."**".$row_po[csf("grey_used_qty")]."**".$row_po[csf("wgt_lost_qty")]."**".''."**".$row_po[csf("process_loss_perc")];

								$po_wise_data = explode("**",$explSaveData[$z]);
								if($roll_maintained==1)
								{
									$order_id=$po_wise_data[0];
									$finish_qnty=$po_wise_data[1];
									$roll_no=$po_wise_data[2];
									$reject_qnty=$po_wise_data[3];
									$grey_qnty=$po_wise_data[4];
									$wgtlost_qnty=$po_wise_data[5];
									$process_loss=$po_wise_data[7];
								}
								else
								{
									$order_id=$po_wise_data[0];
									$finish_qnty=$po_wise_data[1];
									$reject_qnty=$po_wise_data[3];
									$grey_qnty=$po_wise_data[4];
									$wgtlost_qnty=$po_wise_data[5];
									$process_loss=$po_wise_data[7];
								}

								$po_data=sql_select("select a.buyer_name,b.file_no,b.grouping as ref, b.po_number, b.pub_shipment_date, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$order_id");

								$req_qty_arr = return_library_array("select po_break_down_id, sum(fabric_qty) as fabric_qty
									from 
									( 
									    select a.po_break_down_id, sum(a.grey_fab_qnty) as fabric_qty 
									    from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b 
									    where a.pre_cost_fabric_cost_dtls_id=b.id and a.status_active=1 and a.is_deleted=0 and b.lib_yarn_count_deter_id = '$fabric_desc_id' and b.body_part_id= '$cbo_body_part'  and a.po_break_down_id =$order_id and a.booking_type in (1,4) group by a.po_break_down_id 
									    union all 
									    select a.po_break_down_id, sum(a.wo_qnty) as fabric_qty
									    from wo_booking_dtls a, wo_pre_cost_fab_conv_cost_dtls c, wo_pre_cost_fabric_cost_dtls b
									    where a.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=b.id and a.po_break_down_id =$order_id and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.lib_yarn_count_deter_id = '$fabric_desc_id' and b.body_part_id= '$cbo_body_part' and a.booking_type =3 group by a.po_break_down_id
									) group by po_break_down_id","po_break_down_id","fabric_qty");

								if(!(in_array($order_id,$po_array)))
								{
									$tot_po_qnty+=$po_data[0][csf('po_qnty_in_pcs')];
									$po_array[]=$order_id;
								}
								$tot_grey_qnty+=$grey_qnty;$tot_wgtlost_qty+=$wgtlost_qnty;

								$tot_requ_qnty+=$req_qty_arr[$order_id];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td width="110">
										<p><? echo $po_data[0][csf('po_number')]; ?></p>
										<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $order_id; ?>">
										<input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" class="text_boxes" value="<? echo $po_data[0][csf('buyer_name')]; ?>">
										<input type="hidden" name="buyerName[]" id="buyerName_<? echo $i; ?>" class="text_boxes" value="<? echo $buyer_arr[$po_data[0][csf('buyer_name')]]; ?>">
										<input type="hidden" name="previous_recieve_qty[]" id="previous_recieve_qty_<? echo $i; ?>" class="text_boxes" value="0">
									</td>
									<td width="60" align="center"><? echo $po_data[0][csf('file_no')]; ?></td>
									<td width="60" align="center"><? echo $po_data[0][csf('ref')]; ?></td>
									<td width="80" align="center"><? echo change_date_format($po_data[0][csf('pub_shipment_date')]); ?></td>
									<td width="90" align="right">
										<? echo $po_data[0][csf('po_qnty_in_pcs')]; ?>
										<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_data[0][csf('po_qnty_in_pcs')]; ?>">
									</td>

									<td width="80" align="right">
										<? echo $req_qty_arr[$order_id];?>
										<input type="hidden" name="txtfinish_required_qnty[]" id="txtfinish_required_qnty<? echo $i; ?>" value="<? echo $req_qty_arr[$order_id];?>" >
									</td>

									<td width="95" align="center">
										<input type="text" name="txtfinishQnty[]" id="txtfinishQnty_<? echo $i; ?>" onBlur="wgtlostcalculation();" class="text_boxes_numeric" style="width:75px" value="<? echo $finish_qnty; ?>" onKeyUp="check_po_batch_fabric_qnty(this,'<? //echo $row[csf('qnty')]?>')" >

									</td>
									<td width="95" align="center">
										<input type="text" name="txtrejectQnty[]" id="txtrejectQnty_<? echo $i; ?>" onBlur="wgtlostcalculation();" class="text_boxes_numeric" style="width:75px" value="<? echo $reject_qnty; ?>">
									</td>

									<td width="80" align="center">
										<input type="text" name="txtProcessQnty[]" id="txtProcessQnty_<? echo $i; ?>" onBlur="wgtlostcalculation();" class="text_boxes_numeric" style="width:60px" value="<? echo $process_loss;?>">
										<input type="hidden" name="preGreyUsed[]" id="preGreyUsed_<? echo $i;?>" value="">
									</td>

									<td width="70" align="center">
										<input type="text" name="txtgreyQnty[]" id="txtgreyQnty_<? echo $i; ?>" onBlur="wgtlostcalculation();fn_check_balance(<? echo $i; ?>);" onkeyup="clear_process_loss_grey_field(<? echo $i;?>);" class="text_boxes_numeric" style="width:50px" value="<? echo $grey_qnty; ?>" disabled>

										<input type="hidden" name="txtBatchQnty[]" id="txtBatchQnty_<? echo $i; ?>" value="<? echo number_format($po_data[0][csf('po_qnty_in_pcs')],2,".","");?>" >
									</td>
									<td width="70" align="center">
										<input type="text" name="txtwgtlostQnty[]" id="txtwgtlostQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="<? echo $wgtlost_qnty; ?>">
									</td>

									<?
									if($roll_maintained==1)
									{
										?>
										<td align="center">
											<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? if($roll_no!=0) echo $roll_no; ?>" disabled="disabled"/>
										</td>
										<?
									}
									?>
								</tr>
								<?
								$i++;
							}
							?>
							<input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes" value="<? echo $tot_po_qnty; ?>">
							<input type="hidden" name="tot_requ_qnty" id="tot_requ_qnty" class="text_boxes" value="<? echo $tot_requ_qnty; ?>">
						</table>
						<table width="940"  border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
							<tr class="tbl_bottom">
								<th colspan="8" width="760" align="right"> Total </th>
								<th align="right"> &nbsp;<input type="text" name="tot_grey_qty" id="tot_grey_qty" style="width:50px;text-align:right" class="text_boxes" value="<? echo $tot_grey_qnty;?>" readonly> </th>
								<th align="right"> &nbsp; <input type="text" name="tot_wgtlost_qty" id="tot_wgtlost_qty" style="width:50px;text-align:right" class="text_boxes" value="<? echo $tot_wgtlost_qty;?>"  readonly></th>
							</tr>
						</table>
					</div>
					<table width="840">
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
		</div>
		<?
	}
	else // echo "Batch Based";
	{
		$IsGreyUsedDisabled = "";
		if($production_basis ==5 && $process_costing_maintain ==1 && $fabric_store_auto_update ==1 && $is_sales !=1 ){
			$IsGreyUsedDisabled = " disabled=disabled";
		}
		?>
		<div style="width:840px; margin-top:10px" align="center">
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="400" align="center">
				<thead>
					<tr>
					<?
						$txt_vali_source = ($production_vali_source==2) ? " Batch" : " Budget and Batch"
						?>
						<th colspan="4">
							Production Validation With :  <span style="color: #cd2500; font-weight: bold;"><? echo $txt_vali_source;?></span>.
							<br/>
							<?

							if($over_receive_limit_qnty_kg)
							{
								?>
								Production Over Limit :  <span style="color: #cd2500; font-weight: bold;"><? echo $over_receive_limit_qnty_kg;?> KG</span>
								<?	
							}
							else if($over_receive_limit)
							{
								?>
								Production Over Limit :  <span style="color: #cd2500; font-weight: bold;"><? echo $over_receive_limit;?>%.</span>
								<?	
							}

							?>
						</th>
					</tr>
					<tr>
						<th>Total Finish Qnty</th>
						<th>Total Reject Qnty</th>
						<th>Total Grey Qnty</th>
						<th>Distribution Method</th>
					</tr>
				</thead>
				<tr class="general">
					<td>
						<input type="text" name="txt_prop_finish_qnty" id="txt_prop_finish_qnty" class="text_boxes_numeric" value="<? echo $txt_production_qty; ?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)">
						<input type="hidden" id="txt_get_value_delv_entry" name="txt_get_value_delv_entry">
					</td>
					<td>
						<input type="text" name="txt_prop_reject_qnty" id="txt_prop_reject_qnty" class="text_boxes_numeric" value="<? echo $txt_reject_qty; ?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)">
					</td>
					<td>
						<input type="text" name="txt_prop_grey_qnty" id="txt_prop_grey_qnty" class="text_boxes_numeric" value="<? echo $txt_grey_used; ?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)"  <? echo $IsGreyUsedDisabled;?> >
						<input type="hidden" id="txt_get_value_delv_entry" name="txt_get_value_delv_entry">
					</td>
					<td>
						<?
						$distribiution_method=array(1=>"Proportionately",2=>"Manually");
						echo create_drop_down( "cbo_distribiution_method", 140, $distribiution_method,"",0, "",$prev_distribution_method, "distribute_qnty(this.value);",0 );
						?>
					</td>
				</tr>
			</table>
		</div>
		<div style="margin-left:10px; margin-top:10px">
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="920">
				<thead>
					<th width="110">PO/FSO No</th>
					<? if($is_sales==1){?>
						<th width="60">Booking No</th>
					<? }?>
					<th width="60">File No</th>
					<th width="60">Ref No</th>
					<th width="80">Shipment Date</th>
					<th width="90">PO Qty</th>
					<th width="80">Req Qty</th>
					<th width="95">Finish Qty</th>
					<th width="95">Reject Qty</th>
					<th width="80"><input type="checkbox" name="process_loss_chkbox" id="process_loss_chkbox"> Pro. loss%</th>
					<th width="70">Grey Qty</th>
					<th width="70">Wgt. Lost</th>
					<?
					if($roll_maintained==1)
					{
						?>
						<th>Roll</th>
						<?
					}
					?>
				</thead>
			</table>
			<div style="width:940px; max-height:280px; overflow-y:scroll" id="list_container" align="left">
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="920" id="tbl_list_search">
					<?
					$i=1; $tot_po_qnty=$tot_grey_qnty=$tot_wgtlost_qty=$tot_requ_qnty=0; $po_array=array();

					if($save_data!="" && $production_basis==4)
					{
						$po_id = explode(",",$po_id);
						$explSaveData = explode("!!",$save_data);
						for($z=0;$z<count($explSaveData);$z++)
						{
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";

							$po_wise_data = explode("**",$explSaveData[$z]);
							$order_id=$po_wise_data[0];
							$finish_qnty=$po_wise_data[1];
							$roll_no=$po_wise_data[2];
							$reject_qnty=$po_wise_data[3];
							$grey_qnty=$po_wise_data[4];
							$wgtlost_qnty=$po_wise_data[5];
							$booking_no=$po_wise_data[6];
							$process_loss=$po_wise_data[7];

							if(in_array($order_id,$po_id))
							{
								$po_data=sql_select("select a.buyer_name,b.file_no, b.grouping as ref, b.po_number, b.pub_shipment_date, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$order_id");

								$req_qty_arr = return_library_array("select po_break_down_id, sum(fabric_qty) as fabric_qty
									from 
									( 
									    select a.po_break_down_id, sum(a.fin_fab_qnty) as fabric_qty 
									    from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b 
									    where a.pre_cost_fabric_cost_dtls_id=b.id and a.status_active=1 and a.is_deleted=0 and b.lib_yarn_count_deter_id = '$fabric_desc_id' and b.body_part_id='$cbo_body_part' and a.po_break_down_id in ($order_id) and a.booking_type in (1,4) group by a.po_break_down_id 
									    union all 
									    select a.po_break_down_id, sum(a.wo_qnty) as fabric_qty
									    from wo_booking_dtls a, wo_pre_cost_fab_conv_cost_dtls c, wo_pre_cost_fabric_cost_dtls b
									    where a.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=b.id and a.po_break_down_id in ($order_id) and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.lib_yarn_count_deter_id = '$fabric_desc_id' and b.body_part_id='$cbo_body_part' and a.booking_type =3 group by a.po_break_down_id
									)
									group by po_break_down_id","po_break_down_id","fabric_qty");

								if(!(in_array($order_id,$po_array)))
								{
									$tot_po_qnty+=$po_data[0][csf('po_qnty_in_pcs')];
									$po_array[]=$order_id;
								}

								$tot_grey_qnty+=$grey_qnty;$tot_wgtlost_qty+=$wgtlost_qnty;
								$tot_requ_qnty +=$req_qty_arr[$order_id];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td width="110">
										<p><? echo $po_data[0][csf('po_number')]; ?></p>
										<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $order_id; ?>">
										<input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" class="text_boxes" value="<? echo $po_data[0][csf('buyer_name')]; ?>">
										<input type="hidden" name="buyerName[]" id="buyerName_<? echo $i; ?>" class="text_boxes" value="<? echo $buyer_arr[$po_data[0][csf('buyer_name')]]; ?>">
										<input type="hidden" name="previous_recieve_qty[]" id="previous_recieve_qty_<? echo $i; ?>" class="text_boxes" value="0">
									</td>
									<? if($is_sales==1){?>
										<td width="60" align="center"><? echo $booking_no; ?></td>
									<? }?>
									<td width="60" align="center"><? echo $po_data[0][csf('file_no')]; ?></td>
									<td width="60" align="center"><? echo $po_data[0][csf('ref')]; ?></td>

									<td width="80" align="center"><? echo change_date_format($po_data[0][csf('pub_shipment_date')]); ?></td>
									<td width="90" align="right">
										<? echo $po_data[0][csf('po_qnty_in_pcs')]; ?>
										<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_data[0][csf('po_qnty_in_pcs')]; ?>">
									</td>

									<td width="80" align="right">
										<? echo $req_qty_arr[$order_id];?>
										<input type="hidden" name="txtfinish_required_qnty[]" id="txtfinish_required_qnty<? echo $i; ?>" value="<? echo $req_qty_arr[$order_id];?>" >
									</td>

									<td width="95" align="center">
										<input type="text" name="txtfinishQnty[]" id="txtfinishQnty_<? echo $i; ?>" onBlur="wgtlostcalculation();" class="text_boxes_numeric" style="width:75px" value="<? echo $finish_qnty; ?>" onKeyUp="check_po_batch_fabric_qnty(this,'')">
									</td>
									<td width="95" align="center">
										<input type="text" name="txtrejectQnty[]" id="txtrejectQnty_<? echo $i; ?>" onBlur="wgtlostcalculation();" class="text_boxes_numeric" style="width:75px" value="<? echo $reject_qnty; ?>">
									</td>
									<td width="80" align="center">
										<input type="text" name="txtProcessQnty[]" id="txtProcessQnty_<? echo $i; ?>" onBlur="copy_process(<? echo $i; ?>);wgtlostcalculation();" class="text_boxes_numeric" style="width:60px" value="<? echo $process_loss;?>" >
										<input type="hidden" name="preGreyUsed[]" id="preGreyUsed_<? echo $i;?>" value="<??>">
									</td>
									<td width="70" align="center">

										<input type="text" name="txtgreyQnty[]" id="txtgreyQnty_<? echo $i; ?>" onBlur="wgtlostcalculation();fn_check_balance(<? echo $i; ?>);" onkeyup="clear_process_loss_grey_field(<? echo $i;?>);" class="text_boxes_numeric" style="width:50px" value="<? echo $grey_qnty; ?>" >

										<input type="hidden" name="txtBatchQnty[]" id="txtBatchQnty_<? echo $i; ?>"  value="<? echo number_format($po_data[0][csf('po_qnty_in_pcs')],2,'.','');?>"  >
									</td>
									<td width="70" align="center">
										<input type="text" name="txtwgtlostQnty[]" id="txtwgtlostQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="<? echo $wgtlost_qnty; ?>">                   </td>
										<?
										if($roll_maintained==1)
										{
											?>
											<td align="center">
												<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? if($roll_no!=0) echo $roll_no; ?>" disabled="disabled"/>
											</td>
											<?
										}
										?>
									</tr>
									<?
									$i++;
							}
						}

						if(count($po_array)<1)
						{
							$result=implode(",",$po_id);
						}
						else
						{
							$result=implode(",",array_diff($po_id, $po_array));
						}
						if($result!="")
						{
							if($roll_maintained==1)
							{
								$po_sql="select b.id, a.buyer_name,b.file_no, b.grouping as ref, b.po_number, b.pub_shipment_date, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, d.roll_no, d.qnty from wo_po_details_master a, wo_po_break_down b left join pro_roll_details d on b.id=d.po_breakdown_id and d.entry_form=3 and d.status_active=1 and d.is_deleted=0 where a.job_no=b.job_no_mst and b.id in ($result)";
							}
							else
							{
								$po_sql="select b.id, a.buyer_name,b.file_no, b.grouping as ref, b.po_number, b.pub_shipment_date, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($result)";

								$req_qty_arr = return_library_array("select po_break_down_id, sum(fabric_qty) as fabric_qty
									from 
									( 
									    select a.po_break_down_id, sum(a.fin_fab_qnty) as fabric_qty 
									    from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b 
									    where a.pre_cost_fabric_cost_dtls_id=b.id and a.status_active=1 and a.is_deleted=0 and b.lib_yarn_count_deter_id = '$fabric_desc_id' and b.body_part_id='$cbo_body_part' and a.po_break_down_id in ($result) and a.booking_type in (1,4) group by a.po_break_down_id 
									    union all 
									    select a.po_break_down_id, sum(a.wo_qnty) as fabric_qty
									    from wo_booking_dtls a, wo_pre_cost_fab_conv_cost_dtls c, wo_pre_cost_fabric_cost_dtls b
									    where a.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=b.id and a.po_break_down_id in ($result) and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.lib_yarn_count_deter_id = '$fabric_desc_id' and b.body_part_id='$cbo_body_part' and a.booking_type =3 group by a.po_break_down_id
									)
									group by po_break_down_id","po_break_down_id","fabric_qty");
							}

							$nameArray=sql_select($po_sql);
							foreach($nameArray as $row)
							{
								if ($i%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";

								if(!(in_array($row[csf('id')],$po_array)))
								{
									$tot_po_qnty+=$row[csf('po_qnty_in_pcs')];
									$po_array[]=$row[csf('id')];
								}

								$tot_requ_qnty +=$req_qty_arr[$row[csf('id')]];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td width="110">
										<p><? echo $row[csf('po_number')]; ?></p>
										<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
										<input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('buyer_name')]; ?>">
										<input type="hidden" name="buyerName[]" id="buyerName_<? echo $i; ?>" class="text_boxes" value="<? echo $buyer_arr[$row[csf('buyer_name')]]; ?>">
										<input type="hidden" name="previous_recieve_qty[]" id="previous_recieve_qty_<? echo $i; ?>" class="text_boxes" value="0">
									</td>
									<td width="60" align="center"><? echo $row[csf('file_no')];; ?></td>
									<td width="60" align="center"><? echo $row[csf('ref')]; ?></td>
									<td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
									<td width="90" align="right">
										<? echo $row[csf('po_qnty_in_pcs')]; ?>
										<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $row[csf('po_qnty_in_pcs')]; ?>">
									</td>
									<td width="80" align="right">
										<? echo $req_qty_arr[$row[csf('id')]];?>
										<input type="hidden" name="txtfinish_required_qnty[]" id="txtfinish_required_qnty<? echo $i; ?>" value="<? echo $req_qty_arr[$row[csf('id')]];?>" >
									</td>
									<td width="95" align="center">

										<input type="text" name="txtfinishQnty[]" id="txtfinishQnty_<? echo $i; ?>" onBlur="wgtlostcalculation();" class="text_boxes_numeric" style="width:75px" value="<? //echo $finish_qnty; ?>">
									</td>
									<td width="95" align="center">
										<input type="text" name="txtrejectQnty[]" id="txtrejectQnty_<? echo $i; ?>" onBlur="wgtlostcalculation();" class="text_boxes_numeric" style="width:75px" value="<? //echo $finish_qnty; ?>">
									</td>
									<td width="80" align="center">
										<input type="text" name="txtProcessQnty[]" id="txtProcessQnty_<? echo $i; ?>" onBlur="copy_process(<? echo $i; ?>);wgtlostcalculation();" class="text_boxes_numeric" style="width:60px" value="<? //echo $process_loss;?>" >
										<input type="hidden" name="preGreyUsed[]" id="preGreyUsed_<? echo $i;?>" value="<??>">
									</td>
									<td width="70" align="center">

										<input type="text" name="txtgreyQnty[]" id="txtgreyQnty_<? echo $i; ?>" onBlur="wgtlostcalculation();fn_check_balance(<? echo $i; ?>);" onkeyup="clear_process_loss_grey_field(<? echo $i;?>);" class="text_boxes_numeric" style="width:50px" value="<? //echo $grey_qnty; ?>" disabled>

										<input type="hidden" name="txtBatchQnty[]" id="txtBatchQnty_<? echo $i; ?>"  value="<? echo number_format($row[csf('po_qnty_in_pcs')],2,".","");?>"  >
									</td>
									<td width="70" align="center">
										<input type="text" name="txtwgtlostQnty[]" id="txtwgtlostQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="<? //echo $wgtlost_qnty; ?>">                   </td>
										<?
										if($roll_maintained==1)
										{
											?>
											<td align="center">
												<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? if($row[csf('roll_no')]!=0) echo $row[csf('roll_no')]; ?>" disabled="disabled"/>
											</td>
											<?
										}
										?>
									</tr>
									<?
									$i++;
							}
						}
					}
					else if($save_data!="" && $production_basis==5)
					{
						//echo $IsGreyUsedDisabled;die;
						$finish_qnty_array=array();
						$explSaveData = explode("!!",$save_data);

						if($roll_maintained==1)
						{
							for($y=0;$y<count($explSaveData);$y++)
							{
								$po_wise_data = explode("**",$explSaveData[$y]); $i;
								$order_id=$po_wise_data[0];
								$finish_qnty=$po_wise_data[1];
								$roll_no=$po_wise_data[2];
								$reject_qnty=$po_wise_data[3];
								$grey_qnty=$po_wise_data[4];
								$wgtlost_qnty=$po_wise_data[5];
								$process_loss_perc=$po_wise_data[7];

								$finish_qnty_array[$order_id][$roll_no]['qc_qty']=$finish_qnty;
								$finish_qnty_array[$order_id][$roll_no]['rej_qty']=$reject_qnty;
								$finish_qnty_array[$order_id][$roll_no]['grey_qnty']=$grey_qnty;
								$finish_qnty_array[$order_id][$roll_no]['wgt_qnty']=$wgtlost_qnty;
								$finish_qnty_array[$order_id][$roll_no]['process_loss']=$process_loss_perc;
							}
						}
						else
						{
							for($y=0;$y<count($explSaveData);$y++)
							{
								$po_wise_data = explode("**",$explSaveData[$y]);
								$order_id=$po_wise_data[0];
								$finish_qnty=$po_wise_data[1];
								$reject_qnty=$po_wise_data[3];
								$grey_qnty=$po_wise_data[4];
								$wgtlost_qnty=$po_wise_data[5];
								$process_loss_perc=$po_wise_data[7];

								$finish_qnty_array[$order_id]['qc_qty']=$finish_qnty;
								$finish_qnty_array[$order_id]['rej_qty']=$reject_qnty;
								$finish_qnty_array[$order_id]['grey_qnty']=$grey_qnty;
								$finish_qnty_array[$order_id]['wgt_qnty']=$wgtlost_qnty;
								$finish_qnty_array[$order_id]['process_loss']=$process_loss_perc;
							}
						}

						if($roll_maintained==1)
						{
							$po_sql="select b.id, a.buyer_name, b.file_no, b.grouping as ref,b.po_number, b.pub_shipment_date, a.total_set_qnty, b.po_quantity, c.roll_no, c.batch_qnty as qnty from wo_po_details_master a, wo_po_break_down b, pro_batch_create_dtls c where a.job_no=b.job_no_mst and b.id=c.po_id and c.mst_id='$txt_batch_id' and c.status_active=1 and c.is_deleted=0";
							$nameArray=sql_select($po_sql);
						}
						else
						{
							$dia_width_cond="";
							if(str_replace("'","",$txt_original_dia_width)=="")
							{
								if($db_type==0)
								{
									$dia_width_cond = " and d.dia_width = '$txt_original_dia_width'";
								}
								else
								{
									$dia_width_cond = " and d.dia_width is null";
								}
							}
							else
							{
								$dia_width_cond = " and d.dia_width = '$txt_original_dia_width'";
							}

							if($is_sales == 1)
							{
								/*$po_sql = "select a.id,a.po_buyer buyer_name,a.buyer_id,a.within_group, '' as file_no, '' as ref,a.job_no po_number, a.sales_booking_no,a.delivery_date pub_shipment_date, '' as total_set_qnty, sum(b.grey_qty) po_quantity, sum(c.batch_qnty) as qnty, d.color, 1 as is_sales from fabric_sales_order_mst a,fabric_sales_order_dtls b, pro_batch_create_dtls c, product_details_master d where a.id=c.po_id and a.id=b.mst_id and c.prod_id = d.id and c.mst_id='$txt_batch_id' and c.body_part_id = '$cbo_body_part' and d.detarmination_id = '$fabric_desc_id' and d.gsm = '$txt_original_gsm' and d.dia_width = '$txt_original_dia_width' and  c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_buyer,a.buyer_id,a.within_group,a.job_no, a.sales_booking_no,a.delivery_date, d.color";*/

								$po_sql = "select a.id,a.po_buyer buyer_name,a.buyer_id,a.within_group, '' as file_no, '' as ref,a.job_no po_number, a.sales_booking_no,a.delivery_date pub_shipment_date, '' as total_set_qnty, sum(c.batch_qnty) as qnty, e.color_id, 1 as is_sales from fabric_sales_order_mst a, pro_batch_create_dtls c, product_details_master d, pro_batch_create_mst e where a.id=c.po_id and c.prod_id = d.id and c.mst_id='$txt_batch_id' and c.body_part_id = '$cbo_body_part' and d.detarmination_id = '$fabric_desc_id' and d.gsm = '$txt_original_gsm' $dia_width_cond and c.status_active=1 and c.is_deleted=0 and c.mst_id = e.id group by a.id, a.po_buyer,a.buyer_id, a.within_group,a.job_no, a.sales_booking_no,a.delivery_date, e.color_id";

								$nameArray=sql_select($po_sql);
								foreach($nameArray as $row)
								{
									$po_id_arr[$row[csf('id')]] = $row[csf('id')];
									$color_id = $row[csf('color_id')];
									$po_wise_batch_qnty_arr[$row[csf('id')]] += $row[csf('qnty')];
								}

								$req_qty_arr = return_library_array("select a.id, sum(b.grey_qty) as grey_qty from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and b.body_part_id='$cbo_body_part' and b.determination_id ='$fabric_desc_id' and a.id in (".implode(',', $po_id_arr).") and b.color_id = $color_id and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by a.id","id","grey_qty");
							}
							else
							{
								//$po_sql="select b.id, a.buyer_name, b.file_no, b.grouping as ref,b.po_number, b.pub_shipment_date, a.total_set_qnty, b.po_quantity, sum(c.batch_qnty) as qnty, c.mst_id, c.prod_id, c.body_part_id, e.color_id from wo_po_details_master a, wo_po_break_down b, pro_batch_create_dtls c, product_details_master d, pro_batch_create_mst e where a.job_no=b.job_no_mst and b.id=c.po_id and c.prod_id = d.id and c.mst_id='$txt_batch_id' and c.body_part_id = '$cbo_body_part' and d.detarmination_id = '$fabric_desc_id' and d.gsm ='$txt_original_gsm' $dia_width_cond and c.status_active=1 and c.is_deleted=0 and c.mst_id=e.id group by b.id,a.buyer_name,b.file_no, b.grouping,b.po_number, b.pub_shipment_date, a.total_set_qnty, b.po_quantity, c.mst_id, c.prod_id, c.body_part_id, e.color_id";
								// and d.dia_width = '$txt_original_dia_width' 

								$po_sql="select b.id, a.buyer_name, b.file_no, b.grouping as ref,b.po_number, b.pub_shipment_date, a.total_set_qnty, b.po_quantity, sum(c.batch_qnty) as qnty, c.mst_id, c.body_part_id, e.color_id from wo_po_details_master a, wo_po_break_down b, pro_batch_create_dtls c, product_details_master d, pro_batch_create_mst e where a.job_no=b.job_no_mst and b.id=c.po_id and c.prod_id = d.id and c.mst_id='$txt_batch_id' and c.body_part_id = '$cbo_body_part' and d.detarmination_id = '$fabric_desc_id' and d.gsm ='$txt_original_gsm' $dia_width_cond and c.status_active=1 and c.is_deleted=0 and c.mst_id=e.id group by b.id,a.buyer_name,b.file_no, b.grouping,b.po_number, b.pub_shipment_date, a.total_set_qnty, b.po_quantity, c.mst_id, c.body_part_id, e.color_id";

								$nameArray=sql_select($po_sql);
								foreach($nameArray as $row)
								{
									$po_id_arr[$row[csf('id')]] = $row[csf('id')];
									$color_id = $row[csf('color_id')];
									$po_wise_batch_qnty_arr[$row[csf('id')]] += $row[csf('qnty')];
								}

								$req_qty_arr = return_library_array("select po_break_down_id, sum(fabric_qty) as fabric_qty
								from 
								( 
								    select a.po_break_down_id, sum(a.fin_fab_qnty) as fabric_qty 
								    from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b 
								    where a.pre_cost_fabric_cost_dtls_id=b.id and a.status_active=1 and a.is_deleted=0 and b.lib_yarn_count_deter_id = '$fabric_desc_id' and b.body_part_id= '$cbo_body_part' and a.po_break_down_id in (".implode(',', $po_id_arr).") and a.booking_type in (1,4) and a.fabric_color_id = $color_id group by a.po_break_down_id 
								    union all 
								    select a.po_break_down_id, sum(a.wo_qnty) as fabric_qty
								    from wo_booking_dtls a, wo_pre_cost_fab_conv_cost_dtls c, wo_pre_cost_fabric_cost_dtls b
								    where a.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=b.id and a.po_break_down_id in (".implode(',', $po_id_arr).") and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.lib_yarn_count_deter_id = '$fabric_desc_id' and b.body_part_id= '$cbo_body_part' and a.booking_type =3 and a.fabric_color_id = $color_id group by a.po_break_down_id
								) group by po_break_down_id","po_break_down_id","fabric_qty");
								
								//echo $po_sql;
							}

							if ($db_type==0) {
								$dia_cond_a = (trim($txt_original_dia_width)!="")?" and a.original_width = '$txt_original_dia_width'":" and a.width =''";
							}
							elseif ($db_type==2) 
							{
								$dia_cond_a = (trim($txt_original_dia_width)!="")?" and a.original_width = '$txt_original_dia_width'":" and a.width is null";
							}

							$dtls_id_cond = ($dtls_id !="") ? " and a.id!=$dtls_id" : "";

							$po_cumulative_sql=sql_select("select a.batch_id,a.body_part_id, a.fabric_description_id, c.po_breakdown_id as poid,  sum(c.quantity) as qntity, sum(c.grey_used_qty) as grey_used_qty from pro_finish_fabric_rcv_dtls a, inv_receive_master b, order_wise_pro_details c where a.mst_id=b.id and b.entry_form=7 and a.id= c.dtls_id and c.entry_form=7 and b.status_active=1 and b.is_deleted=0 $dtls_id_cond and  a.batch_id='$txt_batch_id' and a.body_part_id = '$cbo_body_part' and a.fabric_description_id = '$fabric_desc_id' and a.gsm='$txt_original_gsm' $dia_cond_a and a.status_active=1 and a.is_deleted=0 group by a.batch_id, a.body_part_id, a.fabric_description_id, c.po_breakdown_id");


							$po_wise_previous_receive=array();
							foreach($po_cumulative_sql as $pre_val)
							{
								$po_wise_previous_receive[$pre_val[csf('poid')]] +=$pre_val[csf('qntity')];
								$po_wise_previous_grey_used[$pre_val[csf('poid')]] +=$pre_val[csf('grey_used_qty')];
							}
						}

						$po_array=array(); $tot_po_qnty=$tot_grey_qnty=$tot_wgtlost_qty=$tot_requ_qnty=0;
						//$nameArray=sql_select($po_sql);
						foreach($nameArray as $row)
						{
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";

							if($row[csf('is_sales')]==1)
								$po_qnty_in_pcs= $req_qty_arr[$row[csf('id')]]; //$row[csf('po_quantity')];
							else
								$po_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
							/*if(!(in_array($row[csf('id')],$po_array)))
							{
								$tot_po_qnty+=$po_qnty_in_pcs;
								$po_array[]=$row[csf('id')];
							}*/
							$tot_po_qnty+= $row[csf('qnty')];
							if($roll_maintained==1)
							{
								$finish_qnty=$finish_qnty_array[$row[csf('id')]][$row[csf('roll_no')]]['qc_qty'];
								$reject_qnty=$finish_qnty_array[$row[csf('id')]][$row[csf('roll_no')]]['rej_qty'];
								$grey_qnty=$finish_qnty_array[$row[csf('id')]][$row[csf('roll_no')]]['grey_qnty'];
								$wgtlost_qnty=$finish_qnty_array[$row[csf('id')]][$row[csf('roll_no')]]['wgt_qnty'];
								$process_loss = $finish_qnty_array[$row[csf('id')]][$row[csf('roll_no')]]['process_loss'];
							}
							else
							{
								$finish_qnty=$finish_qnty_array[$row[csf('id')]]['qc_qty'];
								$reject_qnty=$finish_qnty_array[$row[csf('id')]]['rej_qty'];
								$grey_qnty=$finish_qnty_array[$row[csf('id')]]['grey_qnty'];
								$wgtlost_qnty=$finish_qnty_array[$row[csf('id')]]['wgt_qnty'];
								$process_loss = $finish_qnty_array[$row[csf('id')]]['process_loss'];
							}
							$tot_grey_qnty += $grey_qnty;
							$tot_wgtlost_qty += $wgtlost_qnty;

							if($row[csf('within_group')]==2)
							{
								$buyerId = $row[csf('buyer_id')];
							}else {
								$buyerId = $row[csf('buyer_name')];
							}
							$previous_po_qty=$po_wise_previous_receive[$row[csf('id')]];
							
							//if variable validation source batch then required qnty will be batch quantity.
							if($production_vali_source ==2 )
							{
								$tot_requ_qnty +=$row[csf('qnty')];
							}
							else
							{
								$tot_requ_qnty +=$req_qty_arr[$row[csf('id')]];
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
								<td width="110">
									<p><? echo $row[csf('po_number')]; ?></p>
									<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
									<input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" class="text_boxes" value="<? echo $buyerId; ?>">
									<input type="hidden" name="buyerName[]" id="buyerName_<? echo $i; ?>" class="text_boxes" value="<? echo $buyer_arr[$buyerId];?>">
									<input type="hidden" name="sales_booking_no[]" id="sales_booking_no_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('sales_booking_no')];?>">
									<input type="hidden" name="previous_recieve_qty[]" id="previous_recieve_qty_<? echo $i; ?>" class="text_boxes" value="<?php echo $previous_po_qty; ?>">
								</td>
								<? if($is_sales==1){?>
									<td width="60" align="center"><? echo $row[csf('sales_booking_no')]; ?></td>
								<? }?>
								<td width="60" align="center"><? echo $row[csf('file_no')]; ?></td>
								<td width="60" align="center"><? echo $row[csf('ref')]; ?></td>
								<td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
								<td width="90" align="right">
									<? echo number_format($po_qnty_in_pcs,2,".",""); ?>
									<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo number_format($po_qnty_in_pcs,2,".",""); ?>">
								</td>

								<td width="80" align="right">
									<? 
									if($production_vali_source ==2)
									{
										echo number_format($row[csf('qnty')],2,".","");
										?>
										<input type="hidden" name="txtfinish_required_qnty[]" id="txtfinish_required_qnty<? echo $i; ?>" value="<? echo number_format($row[csf('qnty')],2,".","");?>" >
										<?
									}
									else
									{
										echo number_format($req_qty_arr[$row[csf('id')]],2,".","");
										?>
										<input type="hidden" name="txtfinish_required_qnty[]" id="txtfinish_required_qnty<? echo $i; ?>" value="<? echo number_format($req_qty_arr[$row[csf('id')]],2,".","");?>" >
										<?
									}
									?>
								</td>

								<td width="95" align="center">
									<input type="text" name="txtfinishQnty[]" id="txtfinishQnty_<? echo $i; ?>" onBlur="wgtlostcalculation();" class="text_boxes_numeric" style="width:75px" value="<? echo $finish_qnty; ?>" onKeyUp="check_po_batch_fabric_qnty(this,'<? echo $row[csf('qnty')]?>')">

									<input type="hidden" name="txtfinish_fabric_Qnty[]" id="txtfinish_fabric_Qnty<? echo $i; ?>" value="<? echo $row[csf('qnty')];?>" >
								</td>
								<td width="95" align="center">
									<input type="text" name="txtrejectQnty[]" id="txtrejectQnty_<? echo $i; ?>"  onBlur="wgtlostcalculation();" class="text_boxes_numeric" style="width:75px" value="<? echo $reject_qnty; ?>">
								</td>
								<td width="80" align="center">
									<input type="text" name="txtProcessQnty[]" id="txtProcessQnty_<? echo $i; ?>" onBlur="copy_process(<? echo $i; ?>);wgtlostcalculation();" class="text_boxes_numeric" style="width:60px" value="<? echo $process_loss;?>" <? //echo $IsGreyUsedDisabled;?> >
									<input type="hidden" name="preGreyUsed[]" id="preGreyUsed_<? echo $i;?>" value="<? echo number_format($po_wise_previous_grey_used[$row[csf('id')]],2,'.','');?>">
								</td>
								<td width="70" align="center">
									<input type="text" name="txtgreyQnty[]" id="txtgreyQnty_<? echo $i; ?>" onBlur="wgtlostcalculation();fn_check_balance(<? echo $i; ?>);" onkeyup="clear_process_loss_grey_field(<? echo $i;?>);"  class="text_boxes_numeric" style="width:50px" value="<? echo $grey_qnty; ?>" <? echo $IsGreyUsedDisabled;?> >

									<input type="hidden" name="txtBatchQnty[]" id="txtBatchQnty_<? echo $i; ?>" value="<? echo number_format($po_wise_batch_qnty_arr[$row[csf('id')]],2,'.','');?>"  >
								</td>
								<td width="70" align="center">
									<input type="text" name="txtwgtlostQnty[]" id="txtwgtlostQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="<? echo $wgtlost_qnty; ?>" >
								</td>
								<?
								if($roll_maintained==1)
								{
									?>
									<td align="center">
										<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? if($row[csf('roll_no')]!=0) echo $row[csf('roll_no')]; ?>" disabled="disabled"/>
									</td>
									<?
								}
								?>
							</tr>
							<?
							$i++;
						}
					}
					else
					{

						if($type==1)
						{
							if($po_id!="")
							{
								if($roll_maintained==1)
								{
									$po_sql="select b.id, a.buyer_name, b.po_number, b.pub_shipment_date, a.total_set_qnty, b.po_quantity, d.roll_no, d.qnty from wo_po_details_master a, wo_po_break_down b left join pro_roll_details d on b.id=d.po_breakdown_id and d.entry_form=7 and d.status_active=1 and d.is_deleted=0 where a.job_no=b.job_no_mst and b.id in ($po_id)";
								}
								else
								{
									$po_sql="select b.id, a.buyer_name, b.po_number, b.pub_shipment_date, a.total_set_qnty, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($po_id)";

									$req_qty_arr = return_library_array("select po_break_down_id, sum(fabric_qty) as fabric_qty
									from 
									( 
									    select a.po_break_down_id, sum(a.fin_fab_qnty) as fabric_qty 
									    from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b 
									    where a.pre_cost_fabric_cost_dtls_id=b.id and a.status_active=1 and a.is_deleted=0 and b.lib_yarn_count_deter_id = '$fabric_desc_id' and b.body_part_id='$cbo_body_part' and a.po_break_down_id in ($po_id) and a.booking_type in (1,4) group by a.po_break_down_id 
									    union all 
									    select a.po_break_down_id, sum(a.wo_qnty) as fabric_qty
									    from wo_booking_dtls a, wo_pre_cost_fab_conv_cost_dtls c, wo_pre_cost_fabric_cost_dtls b
									    where a.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=b.id and a.po_break_down_id in ($po_id) and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.lib_yarn_count_deter_id = '$fabric_desc_id' and b.body_part_id='$cbo_body_part' and a.booking_type =3 group by a.po_break_down_id
									)
									group by po_break_down_id","po_break_down_id","fabric_qty");
								}
							}
							$nameArray=sql_select($po_sql);
						}
						else
						{
							if($roll_maintained==1)
							{
								$po_sql="select b.id, a.buyer_name,b.file_no, b.grouping as ref, b.po_number, b.pub_shipment_date, a.total_set_qnty, b.po_quantity, c.roll_no, c.batch_qnty as qnty, 0 as is_sales from wo_po_details_master a, wo_po_break_down b, pro_batch_create_dtls c where a.job_no=b.job_no_mst and b.id=c.po_id and c.mst_id='$txt_batch_id' and c.status_active=1 and c.is_deleted=0";
								$nameArray=sql_select($po_sql);
							}
							else
							{
								if ($db_type==0) {
									$dia_cond = (trim($txt_original_dia_width)!="")?" and d.dia_width = '$txt_original_dia_width'":" and d.dia_width =''";
								}
								elseif ($db_type==2) {
									$dia_cond = (trim($txt_original_dia_width)!="")?" and d.dia_width = '$txt_original_dia_width'":" and d.dia_width is null";
								}
								if($is_sales == 1)
								{
									/*$po_sql = "select a.id,a.po_buyer buyer_name,a.buyer_id,a.within_group, '' as file_no, '' as ref,a.job_no po_number,a.sales_booking_no, a.delivery_date pub_shipment_date, '' as total_set_qnty, sum(b.grey_qty) po_quantity, sum(c.batch_qnty) as qnty,c.mst_id as batch_id, e.color_id, c.prod_id,c.body_part_id, 1 as is_sales from fabric_sales_order_mst a,fabric_sales_order_dtls b, pro_batch_create_dtls c, product_details_master d, pro_batch_create_mst e where a.id=c.po_id and a.id=b.mst_id and c.prod_id = d.id and c.mst_id='$txt_batch_id' and c.body_part_id = '$cbo_body_part' and d.detarmination_id = '$fabric_desc_id' and d.gsm = '$txt_original_gsm' $dia_cond and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.mst_id = e.id group by a.id, a.po_buyer,a.buyer_id,a.within_group, a.job_no,a.sales_booking_no, a.delivery_date,c.mst_id, e.color_id, c.prod_id,c.body_part_id";*/

									$po_sql = "select a.id,a.po_buyer buyer_name,a.buyer_id,a.within_group, '' as file_no, '' as ref,a.job_no po_number,a.sales_booking_no, a.delivery_date pub_shipment_date, '' as total_set_qnty, sum(c.batch_qnty) as qnty,c.mst_id as batch_id, e.color_id, c.prod_id, c.body_part_id, 1 as is_sales from fabric_sales_order_mst a, pro_batch_create_dtls c, product_details_master d, pro_batch_create_mst e where a.id=c.po_id and c.prod_id=d.id and c.mst_id='$txt_batch_id' and c.body_part_id = '$cbo_body_part' and d.detarmination_id= '$fabric_desc_id' and d.gsm = '$txt_original_gsm' $dia_cond and c.status_active=1 and c.is_deleted=0 and c.mst_id=e.id group by a.id, a.po_buyer,a.buyer_id,a.within_group, a.job_no,a.sales_booking_no, a.delivery_date,c.mst_id, e.color_id, c.prod_id,c.body_part_id";

									$nameArray=sql_select($po_sql);

									foreach($nameArray as $row)
									{
										$po_id_arr[$row[csf('id')]] = $row[csf('id')];
										$color_id = $row[csf('color_id')];
										$po_wise_batch_qnty_arr[$row[csf('id')]] += $row[csf('qnty')];
									}

									$req_qty_arr = return_library_array("select a.id, sum(b.grey_qty) as grey_qty from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and b.body_part_id='$cbo_body_part'  and b.determination_id ='$fabric_desc_id' and b.color_id=$color_id and a.id in (".implode(',', $po_id_arr).") and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by a.id","id","grey_qty");
								}
								else
								{

									//$po_sql="select b.id, a.buyer_name, b.file_no, b.grouping as ref,b.po_number, b.pub_shipment_date, a.total_set_qnty, b.po_quantity, sum(c.batch_qnty) as qnty,c.mst_id as batch_id, e.color_id, c.prod_id,c.body_part_id, 0 as is_sales from wo_po_details_master a, wo_po_break_down b, pro_batch_create_dtls c, product_details_master d, pro_batch_create_mst e where a.job_no=b.job_no_mst and b.id=c.po_id and c.prod_id = d.id  and c.mst_id='$txt_batch_id' and c.body_part_id = '$cbo_body_part' and d.detarmination_id = '$fabric_desc_id' and d.gsm = '$txt_original_gsm'  $dia_cond and c.status_active=1 and c.is_deleted=0 and c.mst_id = e.id group by b.id, a.buyer_name,b.file_no, b.grouping, b.po_number, b.pub_shipment_date, a.total_set_qnty, b.po_quantity,c.mst_id, e.color_id, c.prod_id,c.body_part_id";

									$po_sql="select b.id, a.buyer_name, b.file_no, b.grouping as ref,b.po_number, b.pub_shipment_date, a.total_set_qnty, b.po_quantity, sum(c.batch_qnty) as qnty,c.mst_id as batch_id, e.color_id,c.body_part_id, 0 as is_sales from wo_po_details_master a, wo_po_break_down b, pro_batch_create_dtls c, product_details_master d, pro_batch_create_mst e where a.job_no=b.job_no_mst and b.id=c.po_id and c.prod_id = d.id  and c.mst_id='$txt_batch_id' and c.body_part_id = '$cbo_body_part' and d.detarmination_id = '$fabric_desc_id' and d.gsm = '$txt_original_gsm'  $dia_cond and c.status_active=1 and c.is_deleted=0 and c.mst_id = e.id group by b.id, a.buyer_name,b.file_no, b.grouping, b.po_number, b.pub_shipment_date, a.total_set_qnty, b.po_quantity,c.mst_id, e.color_id,c.body_part_id";


									$nameArray=sql_select($po_sql);
									foreach($nameArray as $row)
									{
										$po_id_arr[$row[csf('id')]] = $row[csf('id')];
										$color_id = $row[csf('color_id')];
										$po_wise_batch_qnty_arr[$row[csf('id')]] += $row[csf('qnty')];
									}

									$req_qty_arr = return_library_array("select po_break_down_id, sum(fabric_qty) as fabric_qty
									from 
									( 
									    select a.po_break_down_id, sum(a.fin_fab_qnty) as fabric_qty 
									    from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b 
									    where a.pre_cost_fabric_cost_dtls_id=b.id and a.status_active=1 and a.is_deleted=0 and b.lib_yarn_count_deter_id = '$fabric_desc_id' and b.body_part_id= '$cbo_body_part'  and a.po_break_down_id in (".implode(',', $po_id_arr).") and a.booking_type in (1,4) and a.fabric_color_id= $color_id group by a.po_break_down_id 
									    union all 
									    select a.po_break_down_id, sum(a.wo_qnty) as fabric_qty
									    from wo_booking_dtls a, wo_pre_cost_fab_conv_cost_dtls c, wo_pre_cost_fabric_cost_dtls b
									    where a.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=b.id and a.po_break_down_id in (".implode(',', $po_id_arr).") and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.lib_yarn_count_deter_id = '$fabric_desc_id' and b.body_part_id= '$cbo_body_part' and a.booking_type =3 and a.fabric_color_id= $color_id group by a.po_break_down_id
									) group by po_break_down_id","po_break_down_id","fabric_qty");
									
									//and b.gsm_weight = '$txt_original_gsm'
								}

								if ($db_type==0) {
									$dia_cond_a = (trim($txt_original_dia_width)!="")?" and a.original_width = '$txt_original_dia_width'":" and a.width =''";
								}
								elseif ($db_type==2) 
								{
									$dia_cond_a = (trim($txt_original_dia_width)!="")?" and a.original_width = '$txt_original_dia_width'":" and a.width is null";
								}
								$po_cumulative_sql=sql_select("select a.batch_id,a.body_part_id, a.fabric_description_id,c.po_breakdown_id as poid, sum(c.quantity) as qntity, sum(c.grey_used_qty) as grey_used_qty from pro_finish_fabric_rcv_dtls a, inv_receive_master b, order_wise_pro_details c where a.mst_id=b.id and b.entry_form=7 and a.id = c.dtls_id and c.entry_form=7 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  a.batch_id='$txt_batch_id' and a.body_part_id = '$cbo_body_part' and a.fabric_description_id = '$fabric_desc_id' and a.original_gsm='$txt_original_gsm' $dia_cond_a and a.status_active=1 and a.is_deleted=0 group by a.batch_id,a.body_part_id, a.fabric_description_id,c.po_breakdown_id");

								$po_wise_previous_receive=array();
								foreach($po_cumulative_sql as $pre_val)
								{
									$po_wise_previous_receive[$pre_val[csf('poid')]]+=$pre_val[csf('qntity')];
									$po_wise_previous_grey_used[$pre_val[csf('poid')]]+=$pre_val[csf('grey_used_qty')];
								}

							}
						}
						//echo $po_cumulative_sql;
						$po_array=array(); $tot_po_qnty=0; $tot_requ_qnty=0;
						//$nameArray=sql_select($po_sql);

						foreach($nameArray as $row)
						{
							if ($i%2==0){
								$bgcolor="#E9F3FF";
							}
							else{
								$bgcolor="#FFFFFF";
							}

							if($row[csf('is_sales')]==1){
								$po_qnty_in_pcs= $req_qty_arr[$row[csf('id')]]; //$row[csf('po_quantity')];
							}
							else{
								$po_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
							}

							if($production_basis == 4)
							{
								if(!(in_array($row[csf('id')],$po_array)))
								{
									$tot_po_qnty+=$po_qnty_in_pcs;
									$po_array[]=$row[csf('id')];
								}
							}
							else
							{
								$tot_po_qnty+=$row[csf('qnty')];

								$po_wise_batch_quantity = $po_wise_batch_qnty_arr[$row[csf('id')]];
							}

							if($row[csf('within_group')]==2)
							{
								$buyerId = $row[csf('buyer_id')];
							}else {
								$buyerId = $row[csf('buyer_name')];
							}
							$previous_po_qty=0;
							$previous_po_qty=$po_wise_previous_receive[$row[csf('id')]];

							if($production_vali_source ==2 && $production_basis==5)
							{
								$tot_requ_qnty += $row[csf('qnty')];
							}
							else
							{
								$tot_requ_qnty += $req_qty_arr[$row[csf('id')]];
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
								<td width="110" align="center">
									<p><? echo $row[csf('po_number')]; ?></p>
									<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
									<input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" class="text_boxes" value="<? echo $buyerId; ?>">
									<input type="hidden" name="buyerName[]" id="buyerName_<? echo $i; ?>" class="text_boxes" value="<? echo $buyer_arr[$buyerId];?>">
									<input type="hidden" name="sales_booking_no[]" id="sales_booking_no_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('sales_booking_no')];?>">
									<input type="hidden" name="previous_recieve_qty[]" id="previous_recieve_qty_<? echo $i; ?>" class="text_boxes" value="<? echo $previous_po_qty;?>">
								</td>
								<? if($is_sales==1){?>
									<td width="60" align="center"><? echo $row[csf('sales_booking_no')]; ?></td>
								<? }?>
								<td width="60" align="center"><? echo $row[csf('file_no')]; ?></td>
								<td width="60" align="center"><? echo $row[csf('ref')]; ?></td>
								<td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
								<td width="90" align="right">
									<? echo number_format($po_qnty_in_pcs,2,".",""); ?>
									<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo number_format($po_qnty_in_pcs,2,".",""); ?>">
								</td>

								<td width="80" align="right">
									<? 
									if($production_vali_source ==2 && $production_basis==5)
									{
										
										echo number_format($row[csf('qnty')],2,".","");
										?>
										<input type="hidden" name="txtfinish_required_qnty[]" id="txtfinish_required_qnty<? echo $i; ?>" value="<? echo number_format($row[csf('qnty')],2,".","");?>" >
										<?
									}
									else
									{
										echo number_format($req_qty_arr[$row[csf('id')]],2,".","");
										?>
										<input type="hidden" name="txtfinish_required_qnty[]" id="txtfinish_required_qnty<? echo $i; ?>" value="<? echo number_format($req_qty_arr[$row[csf('id')]],2,".","");?>" >
										<?
									}
									?>									
								</td>

								<td width="95" align="center">
									<input type="text" name="txtfinishQnty[]" id="txtfinishQnty_<? echo $i; ?>"  onBlur="wgtlostcalculation();" class="text_boxes_numeric" style="width:75px" value="" onKeyUp="check_po_batch_fabric_qnty(this,'<? echo $row[csf('qnty')]?>')" >

									<input type="hidden" name="txtfinish_fabric_Qnty[]" id="txtfinish_fabric_Qnty<? echo $i; ?>" value="<? echo $row[csf('qnty')];?>" >											
								</td>
								<td width="95" align="center">
									<input type="text" name="txtrejectQnty[]" id="txtrejectQnty_<? echo $i; ?>" onBlur="wgtlostcalculation();" class="text_boxes_numeric" style="width:75px" value="">
								</td>
								<td width="80" align="center">
									<input type="text" name="txtProcessQnty[]" id="txtProcessQnty_<? echo $i; ?>" onBlur="copy_process(<? echo $i; ?>);wgtlostcalculation();" class="text_boxes_numeric" style="width:60px" value="" <? //echo $IsGreyUsedDisabled;?> >
									<input type="hidden" name="preGreyUsed[]" id="preGreyUsed_<? echo $i;?>" value="<? echo number_format($po_wise_previous_grey_used[$row[csf('id')]],2,'.','');?>">
								</td>
								<td width="70" align="center">
									<input type="text" name="txtgreyQnty[]" onBlur="wgtlostcalculation();fn_check_balance(<? echo $i; ?>);" id="txtgreyQnty_<? echo $i; ?>" onkeyup="clear_process_loss_grey_field(<? echo $i;?>);" class="text_boxes_numeric" style="width:50px" value="" <? //echo $IsGreyUsedDisabled;?> >

									<input type="hidden" name="txtBatchQnty[]" id="txtBatchQnty_<? echo $i; ?>" value="<? echo ($type==1) ? number_format($po_qnty_in_pcs,2,".","") : number_format($po_wise_batch_qnty_arr[$row[csf('id')]],2,'.','');?>" >
								</td>
								<td width="70" align="center">
									<input type="text" name="txtwgtlostQnty[]"  id="txtwgtlostQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="">                   				
								</td>
								<?
								if($roll_maintained==1)
								{
									?>
									<td align="center">
										<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? if($row[csf('roll_no')]!=0) echo $row[csf('roll_no')]; ?>" disabled="disabled"/>
									</td>
									<?
								}
								?>
							</tr>
							<?
							$i++;
						}
					}
						?>
					<input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes" value="<? echo $tot_po_qnty; ?>">
					<input type="hidden" name="tot_requ_qnty" id="tot_requ_qnty" class="text_boxes" value="<? echo $tot_requ_qnty; ?>">
				</table>
				<table width="920"  border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
					<tr class="tbl_bottom">
						<th colspan="7" width="700" align="right"> Total </th>
						<th align="right"> &nbsp;<input type="text" name="tot_grey_qty" id="tot_grey_qty" style="width:50px; text-align:right" class="text_boxes" value="<? echo $tot_grey_qnty;?>" readonly> </th>
						<th align="right"> &nbsp; <input type="text" name="tot_wgtlost_qty" id="tot_wgtlost_qty" style="width:50px;text-align:right" class="text_boxes" value="<? echo $tot_wgtlost_qty;?>" readonly></th>
					</tr>
				</table>
			</div>
			<table width="860">
				<tr>
					<td align="center" >
						<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
					</td>
				</tr>
			</table>
		</div>
		<?
	}
	if($type!=1)
	{
		?>
			</fieldset>
		</form>
		<?
	}
	?>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		var txt_system_ID = <? if($update_id !="") {echo $update_id;} else {echo $update_id=0;} ?>;
		var txt_dtls_id = <? if($dtls_id !="") {echo $dtls_id;} else {echo $dtls_id=0;} ?>;
		get_php_form_data(txt_system_ID+'**'+txt_dtls_id, "check_fin_fab_dlv_qty_action", "finish_fabric_receive_controller" );
	</script>
	</html>
	<?
	exit();
}

	if ($action == "serviceBooking_popup")
	{
		echo load_html_head_contents("WO Info", "../../", 1, 1, '', '', '');
		extract($_REQUEST);
		$width = 1055;
		?>
		<script>

			function js_set_value(id, booking_no, type_id, knit_company,batch_no,batch_id) {
				$('#service_hidden_booking_id').val(id);
				$('#service_hidden_booking_no').val(booking_no);
				$('#service_booking_without_order').val(type_id);

				$('#hidden_knitting_company').val(knit_company);
				$('#hidden_batch_id').val(batch_id);
				$('#hidden_batch_no').val(batch_no);
				parent.emailwindow.hide();
			}

		</script>
	</head>
	<body>
		<div align="center" style="width:1030px;">
			<form name="searchwofrm" id="searchwofrm">
				<fieldset style="width:1030px; margin-left:3px">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" border="1" rules="all" width="720" class="rpt_table">
						<thead>
							<th>Search By</th>
							<th width="240">Enter WO/PI/Production No</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton"/>
								<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
								<input type="hidden" name="service_hidden_booking_id" id="service_hidden_booking_id" class="text_boxes" value="">
								<input type="hidden" name="service_hidden_booking_no" id="service_hidden_booking_no" class="text_boxes" value="">
								<input type="hidden" name="service_booking_without_order" id="service_booking_without_order" class="text_boxes" value="">
								<input type="hidden" name="hidden_knitting_company" id="hidden_knitting_company" class="text_boxes" value="">
								<input type="hidden" name="hidden_batch_id" id="hidden_batch_id" class="text_boxes" value="">
								<input type="hidden" name="hidden_batch_no" id="hidden_batch_no" class="text_boxes" value="">
							</th>
						</thead>
						<tr>
							<td align="center">
								<?
								if($cbo_knitting_source == 1){
									$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan", 4 => "Sales Order");
								}else{
									$receive_basis = array(0 => "Service Booking");
								}
								echo create_drop_down("cbo_receive_basis", 152, $receive_basis, "", 1, "-- Select --", $recieve_basis, "", "1", "");
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show"
								onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_receive_basis').value+'_'+document.getElementById('txt_company_id').value+'_<? echo $cbo_knitting_source; ?>_'+<? echo $cbo_knitting_company; ?>, 'create_wo_no_production_search_list_view', 'search_div', 'finish_fabric_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
								style="width:100px;"/>
							</td>
						</tr>
					</table>
					<div style="margin-top:10px;" id="search_div" align="left"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}
if ($action == "create_wo_no_production_search_list_view") {
	$data = explode("_", $data);
	$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");

	$search_string = "%" . trim($data[0]) . "%";
	$recieve_basis = $data[1];
	$company_id = $data[2];
	$knitting_source = $data[3];
	$knitting_company = $data[4];

	if($knitting_source==1)
	{
		$paymodeCondition = "and a.pay_mode in(3,5)";
		$paymodeWoNonordCondition = "and s.pay_mode in(3,5)";
	}else {
		$paymodeCondition = "and a.pay_mode not in(3,5)";
		$paymodeWoNonordCondition = "and s.pay_mode not in(3,5)";
	}

	if (trim($data[0]) != "") {
		$search_field_cond = "and a.booking_no like '$search_string'";
		$search_field_cond_sample = "and s.booking_no like '$search_string'";
	} else {
		$search_field_cond = "";
	}

	if ($knitting_company != 0) $supplier_con = "and a.supplier_id=$knitting_company"; else $supplier_con = "";

	$sql = "select a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.buyer_id, a.supplier_id, a.po_break_down_id, a.item_category, a.delivery_date, a.job_no as job_no_mst, 0 as type_id,d.batch_no,d.id batch_id from wo_booking_dtls c,wo_booking_mst a, wo_po_break_down b,pro_batch_create_mst d where c.booking_no=a.booking_no and a.job_no=b.job_no_mst and a.id=d.service_booking_id and a.company_id=$company_id and a.item_category=12 and a.booking_type=3 and c.process=31 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $supplier_con $paymodeCondition group by a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.buyer_id, a.po_break_down_id, a.item_category, a.delivery_date, a.job_no, a.supplier_id,d.batch_no,d.id
	union all
	select s.id, s.prefix_num as booking_no_prefix_num, s.booking_no, s.booking_date, s.buyer_id, s.supplier_id, null as po_break_down_id, s.item_category, null as delivery_date, null as job_no_mst, 1 as type_id,null as batch_no,0 as batch_id from wo_non_ord_knitdye_booking_mst s where s.company_id=$company_id and s.item_category=1 and s.status_active=1 and s.is_deleted=0 $paymodeWoNonordCondition $search_field_cond_sample
	order by type_id, id";
	$result = sql_select($sql);
	foreach ($result as $row) {
		$po_ids_arr[$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
		$buyer_id_arr[$row[csf('buyer_id')]]=$row[csf('buyer_id')];
	}

	$po_arr = array();
	if(!empty($po_ids_arr)){
		$po_data = sql_select("select b.id, b.po_number, b.pub_shipment_date, b.po_quantity, b.grouping, b.file_no, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in(".implode(",",$po_ids_arr).")");
		foreach ($po_data as $row) {
			$po_arr[$row[csf('id')]] = $row[csf('po_number')] . "**" . $row[csf('pub_shipment_date')] . "**" . $row[csf('po_quantity')] . "**" . $row[csf('po_qnty_in_pcs')] . "**" . $row[csf('grouping')] . "**" . $row[csf('file_no')];
		}
	}

	if(!empty($buyer_id_arr)){
		$buyer_short_arr = return_library_array("select id, short_name from lib_buyer where id in(".implode(",",$buyer_id_arr).")", "id", "short_name");
	}
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1010" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="105">Booking No</th>
			<th width="75">Booking Date</th>
			<th width="60">Buyer</th>
			<th width="87">Item Category</th>
			<th width="75">Delivary date</th>
			<th width="80">Job No</th>
			<th width="80">Order Qnty</th>
			<th width="75">Shipment Date</th>
			<th width="100">Internal Ref.</th>
			<th width="90">File No</th>
			<th>Order No</th>
		</thead>
	</table>
	<div style="width:1028px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1010" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		foreach ($result as $row) {
			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";

			$po_qnty_in_pcs = '';
			$po_no = '';
			$min_shipment_date = '';
			$internal_ref = '';
			$file_no = '';

			$po_id = explode(",", $row[csf('po_break_down_id')]);
			foreach ($po_id as $id) {
				$po_data = explode("**", $po_arr[$id]);
				$po_number = $po_data[0];
				$pub_shipment_date = $po_data[1];
				$po_qnty = $po_data[2];
				$poQntyPcs = $po_data[3];
				$internalRef = $po_data[4];
				$fileNo = $po_data[5];

				if ($po_no == "") $po_no = $po_number; else $po_no .= "," . $po_number;
				if ($internal_ref == '') $internal_ref = $internalRef; else $internal_ref .= "," . $internalRef;
				if ($file_no == '') $file_no = $fileNo; else $file_no .= "," . $fileNo;

				if ($min_shipment_date == '') {
					$min_shipment_date = $pub_shipment_date;
				} else {
					if ($pub_shipment_date < $min_shipment_date) $min_shipment_date = $pub_shipment_date; else $min_shipment_date = $min_shipment_date;
				}

				$po_qnty_in_pcs += $poQntyPcs;

			}

			$internal_ref = implode(",", array_unique(explode(",", $internal_ref)));
			$file_no = implode(",", array_unique(explode(",", $file_no)));
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('booking_no')]; ?>',<? echo $row[csf('type_id')]; ?>,'<? echo $row[csf('supplier_id')]; ?>','<? echo $row[csf('batch_no')]; ?>','<? echo $row[csf('batch_id')]; ?>');">
				<td width="30"><? echo $i; ?></td>
				<td width="105"><p><? echo $row[csf('booking_no')]; ?></p></td>
				<td width="75" align="center"><? echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</td>
				<td width="60"><p><? echo $buyer_short_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
				<?
				if ($row[csf('type')] == 0) {
					$category_name = $item_category[$row[csf('item_category')]];
				} else {
					$category_name = $conversion_cost_head_array[$row[csf('item_category')]];
				}
				?>
				<td width="87"><p><? echo $category_name; ?></p></td>
				<td width="75" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</td>
				<td width="80"><p><? echo $row[csf('job_no_mst')]; ?>&nbsp;</p></td>
				<td width="80" align="right"><? echo $po_qnty_in_pcs; ?>&nbsp;</td>
				<td width="75" align="center"><? echo change_date_format($min_shipment_date); ?>&nbsp;</td>
				<td width="100"><p><? echo $internal_ref; ?>&nbsp;</p></td>
				<td width="90"><p><? echo $file_no; ?>&nbsp;</p></td>
				<td><p><? echo $po_no; ?>&nbsp;</p></td>
			</tr>
			<?
			$i++;
		}
		?>
	</table>
</div>
<?
}

if ($action == "booking_popup")
{
	echo load_html_head_contents("WO Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$width = 1055;
	?>
	<script>
		var tableFilters =
		{
			col_11: "select",
			display_all_text:'Show All'
		}

		function js_set_value(data,is_approved,necessity_appoval)
		{
			if(necessity_appoval==1)
			{
				if(is_approved==1 )
				{
					$('#hidden_booking_data').val(data);
					parent.emailwindow.hide();
				}
				else
				{
					alert("Approved Booking First.");
					return;
				}
			}
			else
			{
				$('#hidden_booking_data').val(data);
					parent.emailwindow.hide();
			}
		}
	</script>
	</head>
	<body>
	<div align="center">
		<form name="searchwofrm"  id="searchwofrm" autocomplete=off>
			<fieldset style="width:98%;">
				<h3 align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Enter search words</h3>
				<div id="content_search_panel" >
					<table cellpadding="0" cellspacing="0" width="100%" class="rpt_table" border="1" rules="all">
						<thead>
							<th>Buyer</th>
							<th>Booking Date</th>
							<th>Search By</th>
							<th id="search_by_td_up" width="200">Please Enter Booking No</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
								<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
								<input type="hidden" name="hidden_booking_data" id="hidden_booking_data" class="text_boxes" value="">
							</th>
						</thead>

						<tr class="general">
							<td align="center">
								<?
								$user_wise_buyer = $_SESSION['logic_erp']['buyer_id'];
								$buyer_sql = "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name";
								echo create_drop_down( "cbo_buyer", 150, $buyer_sql,"id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
								?>
							</td>
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
							</td>
							<td align="center">
								<?
								$search_by_arr=array(1=>"Booking No",2=>"Job No");
								$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value +'_'+document.getElementById('cbo_buyer').value, 'create_booking_search_list_view', 'search_div', 'finish_fabric_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1, tableFilters);'); accordion_menu(accordion_h1.id,'content_search_panel','')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="5" align="center"  valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</table>
				</div>
				<table width="100%" style="margin-top:5px">
					<tr>
						<td colspan="5">
							<div style="width:100%; margin-top:10px; margin-left:3px" id="search_div" align="left"></div>
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}
if ($action == "create_booking_search_list_view") 
{
	$data = explode("_",$data);

	$search_string 	= "%".trim($data[0])."%";
	$search_by 		= $data[1];
	$company_id 	= $data[2];
	$date_from 		= trim($data[3]);
	$date_to 		= trim($data[4]);
	$buyer_id 		= $data[5];
	$buyer_arr 		= return_library_array( "select id, short_name from lib_buyer",'id','short_name');

	if($unit_id==0) $unit_id_cond=""; else $unit_id_cond=" and a.company_id=$unit_id";
	if($buyer_id==0) $buyer_id_cond= $buyer_id_cond; else $buyer_id_cond=" and a.buyer_id=$buyer_id";

	$search_field_cond="";
	if(trim($data[0])!="")
	{
		if($search_by==1)
			$search_field_cond="and a.booking_no like '$search_string'";
		else
			$search_field_cond="and a.job_no like '$search_string'";
	}

	$date_cond='';
	if($date_from!="" && $date_to!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.booking_date between '".change_date_format(trim($date_from), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($date_to), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.booking_date between '".change_date_format(trim($date_from),'','',1)."' and '".change_date_format(trim($date_to),'','',1)."'";
		}
	}

	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	//$import_booking_id_arr=return_library_array( "select id, booking_id from fabric_sales_order_mst where within_group=1 and status_active=1 and is_deleted=0",'id','booking_id');

	$apporved_date_arr=return_library_array( "select mst_id, max(approved_date) as approved_date from approval_history where current_approval_status=1 group by mst_id",'mst_id','approved_date');

	$necessity_appoval_sql= sql_select("select approval_need,setup_date from approval_setup_mst a, approval_setup_dtls b where a.id=b.mst_id and a.company_id =$company_id and b.page_id=5 and a.status_active=1 and b.status_active=1 order by a.setup_date desc");
	$necessity_appoval = $necessity_appoval_sql[0][csf("approval_need")];


	$season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0",'id','season_name');

	$sql= "SELECT a.booking_no_prefix_num, a.id, a.booking_no, a.booking_date, a.entry_form, a.booking_type, a.is_short, a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, a.po_break_down_id, b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_matrix as season, a.remarks FROM wo_booking_mst a,wo_booking_dtls d, wo_po_details_master b WHERE a.booking_no = d.booking_no and d.job_no =b.job_no and a.fabric_source in (1,2) and a.company_id=$company_id and a.status_active =1 and a.is_deleted =0 and a.item_category=2 $buyer_id_cond $search_field_cond $date_cond  group by a.booking_no_prefix_num,a.id, a.booking_no, a.booking_date, a.entry_form, a.booking_type, a.is_short,a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.po_break_down_id, a.is_approved, b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_matrix,a.remarks order by a.id DESC";

	//echo $sql."<br>";
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1045" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="65">Buyer</th>
			<th width="65">Unit</th>
			<th width="90">Booking No</th>
			<th width="50">Booking ID</th>
			<th width="90">Job No</th>
			<th width="110">Style Ref.</th>
			<th width="80">Booking Date</th>
			<th width="80">App. Date</th>
			<th width="80">Delivery Date</th>
			<th width="70">Currency</th>
			<th width="60">Approved</th>
			<th>PO No.</th>
		</thead>
	</table>
	<div style="width:1080px; max-height:265px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1045" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;
			$result = sql_select($sql);
			foreach ($result as $row)
			{
				//if(!in_array($row[csf('id')],$import_booking_id_arr))
				//{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if($row[csf('po_break_down_id')]!="")
				{
					$po_no='';
					$po_ids=explode(",",$row[csf('po_break_down_id')]);
					foreach($po_ids as $po_id)
					{
						if($po_no=="") $po_no=$po_arr[$po_id]; else $po_no.=",".$po_arr[$po_id];
					}
				}

				$data=$row[csf('id')].'__'.$row[csf('booking_no')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $data; ?>','<? echo $row[csf('is_approved')]; ?>','<? echo $necessity_appoval;?>')">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="65" align="center"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
					<td width="65" align="center"><? echo $company_arr[$row[csf('company_id')]]; ?></td>
					<td width="90" align="center"><? echo $row[csf('booking_no')]; ?></td>
					<td width="50" align="center"><? echo $row[csf('booking_no_prefix_num')]; ?></td>
					<td width="90" align="center"><? echo $row[csf('job_no')]; ?></td>
					<td width="110" align="center"><? echo $row[csf('style_ref_no')]; ?></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="80" align="center"><? echo change_date_format($apporved_date_arr[$row[csf('id')]]); ?></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
					<td width="70" align="center"><? echo $currency[$row[csf('currency_id')]]; ?></td>
					<td width="60" align="center"><? echo ($row[csf('is_approved')]==1)? "Yes":"No"; ?></td>
					<td style="word-break: break-all;"><? echo $po_no; ?></td>
				</tr>
				<?
				$i++;
				//}
			}

			//partial booking...........................................................start;
			$partial_sql= "SELECT a.booking_no_prefix_num, a.id, d.booking_no, a.booking_date, a.entry_form, a.booking_type, a.is_short, a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, listagg(d.po_break_down_id, ',') within group (order by d.po_break_down_id) as po_break_down_id, b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_matrix as season,a.remarks FROM wo_booking_mst a, wo_po_details_master b,wo_booking_dtls d WHERE a.booking_no=d.booking_no and d.job_no=b.job_no and a.fabric_source in (1,2) and a.company_id=$company_id and a.status_active =1 and a.is_deleted =0 and a.item_category=2 and a.entry_form=108 $buyer_id_cond $search_field_cond $date_cond group by a.booking_no_prefix_num,a.id, d.booking_no, a.booking_date, a.entry_form,a.booking_type, a.is_short,a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_matrix,a.remarks order by a.id DESC";
			$partial_result = sql_select($partial_sql);
			foreach ($partial_result as $row)
			{
				//if(!in_array($row[csf('id')],$import_booking_id_arr))
				//{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if($row[csf('po_break_down_id')]!="")
				{
					$po_no='';
					$po_ids=array_unique(explode(",",$row[csf('po_break_down_id')]));
					foreach($po_ids as $po_id)
					{
						if($po_no=="") $po_no=$po_arr[$po_id]; else $po_no.=",".$po_arr[$po_id];
					}
				}
				$data=$row[csf('id')].'__'.$row[csf('booking_no')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $data; ?>','<? echo $row[csf('is_approved')]; ?>','<? echo $necessity_appoval;?>')">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="65" align="center"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
					<td width="65" align="center"><? echo $company_arr[$row[csf('company_id')]]; ?></td>
					<td width="90" align="center"><? echo $row[csf('booking_no')]; ?></td>
					<td width="50" align="center"><? echo $row[csf('booking_no_prefix_num')]; ?></td>
					<td width="90" align="center"><? echo $row[csf('job_no')]; ?></td>
					<td width="110" align="center"><? echo $row[csf('style_ref_no')]; ?></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="80" align="center"><? echo change_date_format($apporved_date_arr[$row[csf('id')]]); ?></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
					<td width="70" align="center"><? echo $currency[$row[csf('currency_id')]]; ?></td>
					<td width="60" align="center"><? echo ($row[csf('is_approved')]==1)? "Yes":"No"; ?></td>
					<td style="word-break: break-all;"><? echo $po_no; ?></td>
				</tr>
				<?
				$i++;
				//}
			}
			//partial booking...........................................................end;
			?>
		</table>
	</div>
	<?
	exit();
}


if($action=="check_fin_fab_dlv_qty_action")
{
	$data=explode('**', $data);
	$data_array_delv_qty=sql_select("SELECT sum(current_delivery) as current_delivery from pro_grey_prod_delivery_dtls where grey_sys_id='$data[0]' and sys_dtls_id='$data[1]'");
	foreach ($data_array_delv_qty as $row)
	{
		echo "document.getElementById('txt_get_value_delv_entry').value = '".$row[csf("current_delivery")]."';\n";
		exit();
	}
}

if($action=="check_fin_fab_dlv_action")
{
	//$data_array_delv_qty=sql_select("select a.sys_number from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b where a.id=b.mst_id and b.grey_sys_id='$data' group by a.sys_number");


	//update_id+'**'+update_dtls_id+'**'+fabric_store_auto_update+'**'+batch_id

	$data=explode("**", $data);
	$update_id=$data[0];
	$update_dtls_id=$data[1];
	$fabric_store_auto_update=$data[2];
	$batch_id=$data[3];
	
	$production_dtls_id=sql_select("SELECT b.id as production_dtls_id, b.batch_id, b.prod_id, b.order_id, b.is_sales from inv_receive_master a,pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.id='$update_id' and b.mst_id='$update_id' and b.id = $update_dtls_id and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 group by b.id,b.batch_id,b.prod_id,b.order_id, b.is_sales");

	foreach ($production_dtls_id as $row)
	{
		$production_dtls_id=$row[csf("production_dtls_id")];
		$production_batch_id=$row[csf("batch_id")];
		$production_order_id=$row[csf("order_id")];
		$production_prod_id=$row[csf("prod_id")];
		$production_is_sales=$row[csf("is_sales")];
	}

	if ($fabric_store_auto_update==1)
	{
		if($production_is_sales == 1)
		{
			$data_array_recv_qty=sql_select("select a.issue_number as recv_issue_sys, sum(c.quantity) as quantity from inv_issue_master a,inv_finish_fabric_issue_dtls b , order_wise_pro_details c where a.id=b.mst_id and b.batch_id='$production_batch_id' and b.prod_id=$production_prod_id and c.po_breakdown_id in ($production_order_id) and  b.trans_id = c.trans_id  and a.entry_form in(224) and c.entry_form in(224)  and a.item_category=2 and a.status_active=1 and a.is_deleted=0 group by a.issue_number");
		}
		else
		{
			//$data_array_recv_qty=sql_select("select a.issue_number as recv_issue_sys from inv_issue_master a,inv_finish_fabric_issue_dtls b where a.id=b.mst_id  and b.batch_id='$production_batch_id' and b.prod_id=$production_prod_id and b.order_id=$production_order_id  and a.entry_form in(18) and a.item_category=2 and a.status_active=1 and a.is_deleted=0 group by a.issue_number");

			$data_array_recv_qty=sql_select("select a.issue_number as recv_issue_sys, sum(c.quantity) as quantity, sum(b.issue_qnty) as non_quantity from inv_issue_master a,inv_finish_fabric_issue_dtls b left join order_wise_pro_details c on b.trans_id = c.trans_id and c.po_breakdown_id in ($production_order_id)  where a.id=b.mst_id and b.batch_id='$production_batch_id' and b.prod_id=$production_prod_id and a.entry_form in(18) and a.item_category=2 and a.status_active=1 and a.is_deleted=0 group by a.issue_number");
		}

	}
	else
	{
		$data_array_recv_qty=sql_select("select a.recv_number  as recv_issue_sys from inv_receive_master a,pro_finish_fabric_rcv_dtls b where a.id=b.mst_id  and b.fin_prod_dtls_id='$production_dtls_id' and b.batch_id=$production_batch_id and a.entry_form in(37,225) and a.item_category=2 and a.status_active=1 and a.is_deleted=0 group by a.recv_number");	
	}

	$data_array_delivery_qty=sql_select("select a.sys_number as recv_issue_sys,sum(b.current_delivery) as current_delivery, 1 as delivery from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b where a.id=b.mst_id and b.grey_sys_id='$update_id' and b.sys_dtls_id=$update_dtls_id and b.status_active=1 and b.is_deleted=0 group by a.sys_number");
	//and b.batch_id=$production_batch_id

	if(!empty($data_array_delivery_qty))
	{
		foreach ($data_array_delivery_qty as $val)
		{
			echo "1"."**".$val[csf("recv_issue_sys")]."**".$val[csf("delivery")]."**".$val[csf("current_delivery")];
			die;
		}
	}

	if(!empty($data_array_recv_qty))
	{
		foreach ($data_array_recv_qty as $row)
		{
			echo "1"."**".$row[csf("recv_issue_sys")]."**".$row[csf("delivery")]."**".$row[csf("current_delivery")];
			die;
		}
	}
	else{

		echo "0"."**";
	}
}


/*if($action=="check_fin_fab_dlv_action_17_06_2020__")
{
	//$data_array_delv_qty=sql_select("select a.sys_number from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b where a.id=b.mst_id and b.grey_sys_id='$data' group by a.sys_number");

	$data=explode("**", $data);
	$update_id=$data[0];
	$update_dtls_id=$data[1];
	$fabric_store_auto_update=$data[2];
	$batch_id=$data[3];
	
	//$production_dtls_id=sql_select("SELECT b.id as production_dtls_id, b.batch_id, b.prod_id, b.order_id, b.is_sales from inv_receive_master a,pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.id='$update_id' and b.mst_id='$update_id' and b.id = $update_dtls_id and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 group by b.id,b.batch_id,b.prod_id,b.order_id, b.is_sales");
	
	$production_dtls_id=sql_select("SELECT b.id as production_dtls_id, b.batch_id, b.prod_id, b.order_id, c.po_breakdown_id, b.is_sales , sum(b.receive_qnty) as dtls_qnty, sum(quantity) as order_qnty from inv_receive_master a,pro_finish_fabric_rcv_dtls b left join order_wise_pro_details c on b.id = c.dtls_id and c.entry_form = 7 where a.id=b.mst_id and a.id='$update_id' and b.mst_id='$update_id' and b.id = $update_dtls_id and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 group by b.id,b.batch_id,b.prod_id,b.order_id, c.po_breakdown_id, b.is_sales");



	foreach ($production_dtls_id as $row)
	{
		$production_dtls_id=$row[csf("production_dtls_id")];
		$production_batch_id=$row[csf("batch_id")];
		$production_order_id=$row[csf("order_id")];
		$production_prod_id=$row[csf("prod_id")];
		$production_is_sales=$row[csf("is_sales")];

		if($row[csf("order_id")] == "")
		{
			$dtls_quantity = $row[csf("dtls_qnty")];
		}else{
			$order_wise_dtls[$row[csf("po_breakdown_id")]] += $row[csf("order_qnty")];
		}
	}

	if ($fabric_store_auto_update==1)
	{
		if($production_is_sales == 1)
		{
			$data_array_recv_qty=sql_select("select a.issue_number as recv_issue_sys, sum(c.quantity) as quantity from inv_issue_master a,inv_finish_fabric_issue_dtls b , order_wise_pro_details c where a.id=b.mst_id and b.batch_id='$production_batch_id' and b.prod_id=$production_prod_id and c.po_breakdown_id in ($production_order_id) and  b.trans_id = c.trans_id  and a.entry_form in(224) and c.entry_form in(224)  and a.item_category=2 and a.status_active=1 and a.is_deleted=0 group by a.issue_number");
		}
		else
		{
			//$data_array_recv_qty=sql_select("select a.issue_number as recv_issue_sys from inv_issue_master a,inv_finish_fabric_issue_dtls b where a.id=b.mst_id  and b.batch_id='$production_batch_id' and b.prod_id=$production_prod_id and b.order_id=$production_order_id  and a.entry_form in(18) and a.item_category=2 and a.status_active=1 and a.is_deleted=0 group by a.issue_number");

			$data_array_recv_qty=sql_select("select a.issue_number as recv_issue_sys, sum(c.quantity) as quantity, sum(b.issue_qnty) as non_quantity from inv_issue_master a,inv_finish_fabric_issue_dtls b left join order_wise_pro_details c on b.trans_id = c.trans_id and c.po_breakdown_id in ($production_order_id)  where a.id=b.mst_id and b.batch_id='$production_batch_id' and b.prod_id=$production_prod_id and a.entry_form in(18) and a.item_category=2 and a.status_active=1 and a.is_deleted=0 group by a.issue_number");
		}
	}
	else
	{
		$data_array_recv_qty=sql_select("select a.recv_number  as recv_issue_sys from inv_receive_master a,pro_finish_fabric_rcv_dtls b where a.id=b.mst_id  and b.fin_prod_dtls_id='$production_dtls_id' and b.batch_id=$production_batch_id and a.entry_form in(37,225) and a.item_category=2 and a.status_active=1 and a.is_deleted=0 group by a.recv_number");	
	}

	$data_array_delivery_qty=sql_select("select a.sys_number as recv_issue_sys, order_id, c.po_number, sum(b.current_delivery) as current_delivery, 1 as delivery from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b left join wo_po_break_down c on b.order_id = c.id where a.id=b.mst_id and b.grey_sys_id='$update_id' and b.sys_dtls_id=$update_dtls_id and b.batch_id=$production_batch_id group by a.sys_number, order_id, c.po_number");





	if(!empty($data_array_delivery_qty))
	{
		foreach ($data_array_delivery_qty as $val)
		{
			echo "1**".$order_wise_dtls[$val[csf("order_id")]]."==".$val[csf("current_delivery")]."<br>";
			if($dtls_quantity)
			{
				if($dtls_quantity <  $val[csf("current_delivery")])
				{
					echo "1"."**".$val[csf("recv_issue_sys")]."**".$val[csf("delivery")]."**"."You can not decrease Quantity from Current Delv Quantity.\nDelivery Found\nDelivery No is" .$val[csf("recv_issue_sys")]."\nDelivery Quantity = ".$val[csf("current_delivery")];
					die;
				}
			}
			else if($order_wise_dtls[$val[csf("order_id")]] <  $val[csf("current_delivery")])
			{
				echo "1"."**".$val[csf("recv_issue_sys")]."**".$val[csf("delivery")]."**"."You can not decrease Quantity from  Current Delv Quantity.\nDelivery Found\nDelivery No is" .$val[csf("recv_issue_sys")]."\nDelivery Quantity = ".$val[csf("current_delivery")]."\nOrder No:".$val[csf("po_number")];
				die;
			}
		}
	}

	echo "1**";die;

	if(!empty($data_array_recv_qty))
	{
		foreach ($data_array_recv_qty as $row)
		{
			echo "1"."**".$row[csf("recv_issue_sys")]."**".$row[csf("delivery")]."**".$row[csf("current_delivery")];
			die;
		}
	}
	else{

		echo "0"."**";
	}
}*/


if($action=="create_po_search_list_view")
{
	$data = explode("_",$data);

	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];

	if($search_by==1)
		$search_field='b.po_number';
	else
		$search_field='a.job_no';

	$company_id =$data[2];
	$buyer_id =$data[3];

	$all_po_id=$data[4];
	$booking_no=$data[5];

	if($all_po_id!="")
		$po_id_cond=" or b.id in($all_po_id)";
	else
		$po_id_cond="";

	$hidden_po_id=explode(",",$all_po_id);

	if($buyer_id==0) $buyer="%%"; else $buyer=$buyer_id;

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$sql = "select a.job_no, a.buyer_name, a.style_ref_no, a.order_uom, b.id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c where b.id =c.po_break_down_id and a.job_no=b.job_no_mst and a.company_name=$company_id and a.buyer_name like '$buyer' and $search_field like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.booking_no = '$booking_no' and c.status_active=1 group by a.job_no, a.buyer_name, a.style_ref_no, a.order_uom, b.id, b.po_number, (a.total_set_qnty*b.po_quantity) , b.pub_shipment_date ";

	?>
	<div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" >
			<thead>
				<th width="30">SL</th>
				<th width="90">Job No</th>
				<th width="100">Style No</th>
				<th width="100">PO No</th>
				<th width="80">PO Quantity</th>
				<th width="100">Buyer</th>
				<th>Shipment Date</th>
			</thead>
		</table>
		<div style="width:618px; overflow-y:scroll; max-height:240px;" id="buyer_list_view" align="center">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="tbl_list_search" >
				<?
				$i=1; $po_row_id='';
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					$roll_used=0;

					if(in_array($selectResult[csf('id')],$hidden_po_id))
					{
						if($po_row_id=="") $po_row_id=$i; else $po_row_id.=",".$i;

						$roll_data_array=sql_select("select roll_no from pro_roll_details where po_breakdown_id=".$selectResult[csf('id')]." and roll_used=1 and entry_form=1 and status_active=1 and is_deleted=0");
						if(count($roll_data_array)>0)
						{
							$roll_used=1;
						}
						else
							$roll_used=0;
					}

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)">
						<td width="30" align="center">
							<? echo $i; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
						</td>
						<td width="90"><p><? echo $selectResult[csf('job_no')]; ?></p></td>
						<td width="100"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
						<td width="100"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
						<td width="80" align="right"><? echo $selectResult[csf('po_qnty_in_pcs')]; ?></td>
						<td width="100"><p><? echo $buyer_arr[$selectResult[csf('buyer_name')]]; ?></p></td>
						<td align="center"><? echo change_date_format($selectResult[csf('pub_shipment_date')]); ?></td>
					</tr>
					<?
					$i++;
				}
				?>
				<input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<? echo $po_row_id; ?>"/>
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
							<input type="button" name="close" onClick="show_finish_fabric_recv();" class="formbutton" value="Close" style="width:100px" />
						</div>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<?
	exit();
}
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');

	$txt_rack = str_replace("'", "", $txt_rack);
	$txt_shelf = str_replace("'", "", $txt_shelf);
	$cbo_room = str_replace("'", "", $cbo_room);
	$cbo_floor = str_replace("'", "", $cbo_floor);
	$is_sales = str_replace("'", "", $is_sales);

	$txt_dia_width = strtoupper($txt_dia_width); //For avoiding case sensitivity of dia. 05/08/2023
	$txt_original_dia_width = strtoupper($txt_original_dia_width);

	if($txt_rack==""){$txt_rack=0;}
	if($txt_shelf==""){$txt_shelf=0;}
	if($cbo_room==""){$cbo_room=0;}
	if($cbo_floor==""){$cbo_floor=0;}
	if($is_sales==""){$is_sales=0;}
	//echo "10**";die;

	if(str_replace("'","",$cbo_production_basis)==5)
	{
		$variable_set_production=sql_select("select distribute_qnty, production_entry, auto_update from variable_settings_production where variable_list =51 and company_name=$cbo_company_id and item_category_id=2 and status_active=1"); //and auto_update=1
		
		$is_over_receive_unlimited = ($variable_set_production[0][csf('auto_update')] == 3) ? 1 : 0;
		$over_receive_limit=$over_receive_limit_qnty_kg=0;
		if($variable_set_production[0][csf('production_entry')]*1 >0)
		{
			$over_receive_limit_qnty_kg = $variable_set_production[0][csf('production_entry')];
		}
		else
		{
			$over_receive_limit = $variable_set_production[0][csf('distribute_qnty')];
		}


		if ($operation==1)
		{
			$up_cond = " and a.id!=$update_dtls_id";
		}

		$dia_width_cond=$dia_cond_a="";
		if(str_replace("'","",$txt_original_dia_width)=="")
		{
			if($db_type==0)
			{
				$dia_width_cond = " and upper(c.dia_width) = '".str_replace("'","",$txt_original_dia_width)."'";
				$dia_cond_a= " and upper(a.original_width) = '".str_replace("'","",$txt_original_dia_width)."'";
			}
			else
			{
				$dia_width_cond = " and c.dia_width is null";
				$dia_cond_a = " and a.original_width is null";
			}
		}
		else
		{
			$dia_width_cond = " and upper(c.dia_width) = '".str_replace("'","",$txt_original_dia_width)."'";
			$dia_cond_a = " and upper(a.original_width) = '".str_replace("'","",$txt_original_dia_width)."'";
		}

		$cumu_rcv_data=sql_select("select a.batch_id, sum(a.receive_qnty) as quantity from pro_finish_fabric_rcv_dtls a, inv_receive_master b where a.mst_id=b.id and b.entry_form=7 and b.status_active=1 and b.is_deleted=0 and a.batch_id=$txt_batch_id and a.body_part_id = $cbo_body_part and a.fabric_description_id = $fabric_desc_id and a.gsm=$txt_gsm $dia_cond_a $up_cond and a.status_active=1 and a.is_deleted=0 group by a.batch_id");
		$pre_rcv_qnty = $cumu_rcv_data[0][csf("quantity")] + str_replace("'", '',$txt_production_qty);  

		$batch_array=sql_select("select sum(b.batch_qnty) as qnty from pro_batch_create_mst a,pro_batch_create_dtls b,product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.id=$txt_batch_id and b.status_active=1 and b.is_deleted=0 and c.detarmination_id=$fabric_desc_id and c.gsm=$txt_gsm $dia_width_cond and b.body_part_id=$cbo_body_part");
		$total_batch_qnty =$batch_array[0][csf("qnty")];

		if($over_receive_limit_qnty_kg>0)
		{
			$total_batch_qnty = $over_receive_limit_qnty_kg + $total_batch_qnty;
		}
		else
		{
			$total_batch_qnty = ($over_receive_limit*$total_batch_qnty)/100 + $total_batch_qnty;
		}
		//$total_batch_qnty = ($over_receive_limit*$total_batch_qnty)/100 + $total_batch_qnty;

		//echo "30**Production Qnty Exceeds Batch Quantity. $pre_rcv_qnty > $total_batch_qnty="."select sum(b.batch_qnty) as qnty from pro_batch_create_mst a,pro_batch_create_dtls b,product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.id=$txt_batch_id and b.status_active=1 and b.is_deleted=0 and c.detarmination_id=$fabric_desc_id and c.gsm=$txt_gsm $dia_width_cond and b.body_part_id=$cbo_body_part";die;
		
		if($is_over_receive_unlimited ==0)
		{
			if($pre_rcv_qnty > $total_batch_qnty){
				echo "30**Production Qnty Exceeds Batch Quantity.\nBatch Quantity :".$total_batch_qnty.", Production : ".$pre_rcv_qnty;
				die;
			}
		}
	}
	//echo "10**";die;

	if(str_replace("'", "", $txt_gsm) == "")
	{
		echo "30**GSM not found";
		disconnect($con);
		die;
	}

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$finish_recv_num=''; $finish_update_id=''; $flag=1;

		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";

			$new_finish_recv_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,$cbo_company_id,'FFPE',7,date("Y",time()),0 ));
			$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);

			$field_array="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, receive_basis, company_id, receive_date, challan_no, store_id, location_id,knitting_location_id, knitting_source, knitting_company, booking_no, booking_id, inserted_by, insert_date";

			$data_array="(".$id.",'".$new_finish_recv_system_id[1]."',".$new_finish_recv_system_id[2].",'".$new_finish_recv_system_id[0]."',7,2,".$cbo_production_basis.",".$cbo_company_id.",".$txt_production_date.",".$txt_challan_no.",".$cbo_store_name.",".$cbo_location.",".$cbo_location_dyeing.",".$cbo_dyeing_source.",".$cbo_dyeing_company.",".$txt_booking_no.",".$txt_booking_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			$finish_recv_num=$new_finish_recv_system_id[0];
			$finish_update_id=$id;
		}
		else
		{
			$field_array_update="receive_basis*receive_date*challan_no*store_id*location_id*knitting_location_id*knitting_source*knitting_company*updated_by*update_date";

			$data_array_update=$cbo_production_basis."*".$txt_production_date."*".$txt_challan_no."*".$cbo_store_name."*".$cbo_location."*".$cbo_location_dyeing."*".$cbo_dyeing_source."*".$cbo_dyeing_company."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

			$finish_recv_num=str_replace("'","",$txt_system_id);
			$finish_update_id=str_replace("'","",$update_id);
		}

		//$color_id=return_id( $txt_color, $color_arr, "lib_color", "id,color_name");
		if(str_replace("'","",$txt_color)!="")
		{
			if (!in_array(str_replace("'","",$txt_color),$new_array_color))
			{
				$color_id = return_id( str_replace("'","",$txt_color), $color_arr, "lib_color", "id,color_name","7");
				//echo $$txtColorName.'='.$color_id.'<br>';
				$new_array_color[$color_id]=str_replace("'","",$txt_color);

			}
			else $color_id =  array_search(str_replace("'","",$txt_color), $new_array_color);
		}
		else
		{
			$color_id=0;
		}

		$ItemDesc=str_replace("'","",$txt_fabric_desc).", ".str_replace("'","",$txt_gsm).", ".str_replace("'","",$txt_dia_width);

		if(str_replace("'","",$cbo_production_basis)==4)
		{
			$order_id=explode(",",str_replace("'","",$all_po_id));

			if(count($order_id)>1)
			{
				echo "20**0";
				disconnect($con);
				die;
			}

			$batchData=sql_select("select a.id, a.batch_weight from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.batch_no=$txt_batch_no and a.color_id='$color_id' and b.po_id=$all_po_id and a.status_active=1 and a.is_deleted=0 and a.entry_form=7 group by a.id, a.batch_weight");

			if(count($batchData)>0)
			{
				$batch_id=$batchData[0][csf('id')];
				$curr_batch_weight=$batchData[0][csf('batch_weight')]+str_replace("'", '',$txt_production_qty);
				$field_array_batch_update="batch_weight*updated_by*update_date";
				$data_array_batch_update=$curr_batch_weight."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			}
			else
			{
				if(is_duplicate_field( "batch_no", "pro_batch_create_mst", "batch_no=$txt_batch_no" )==1)
				{
					echo "11**0";
					disconnect($con);
					die;
				}

				$batch_id = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
				$field_array_batch="id, batch_no, entry_form, batch_date, company_id, color_id, batch_weight, booking_no, booking_no_id, inserted_by, insert_date";

				$data_array_batch="(".$batch_id.",".$txt_batch_no.",7,".$txt_production_date.",".$cbo_company_id.",'".$color_id."',".$txt_production_qty.",".$txt_booking_no.",".$txt_booking_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			}
		}
		else
		{
			$batch_id=str_replace("'","",$txt_batch_id);
		}

		$dia_width_cond="";
		if(str_replace("'","",$txt_dia_width)=="")
		{
			if($db_type==0)
			{
				$dia_width_cond = " and upper(dia_width) = $txt_dia_width";
			}
			else
			{
				$dia_width_cond = " and dia_width is null";
			}
		}
		else
		{
			$dia_width_cond = " and upper(dia_width) = $txt_dia_width";
		}

		if($is_sales==1)
		{
			$is_gmts_product=0;
		}
		else
		{
			$is_gmts_product=1;
		}
		$is_gmts_product_cond= " and is_gmts_product=$is_gmts_product";
		if(str_replace("'","",$fabric_desc_id)=="") $fabric_desc_id=0;
		if(str_replace("'","",$fabric_desc_id)==0)
		{
			$fabric_description=trim(str_replace("'","",$txt_fabric_desc));
			$row_prod=sql_select("select id, current_stock from product_details_master where company_id=$cbo_company_id and item_category_id=2 and detarmination_id=$fabric_desc_id and item_description='$fabric_description' and gsm=$txt_gsm  $dia_width_cond and color='$color_id' and unit_of_measure=$cbouom $is_gmts_product_cond and status_active=1 and is_deleted=0");
			//and dia_width=$txt_dia_width
		}
		else
		{
			$row_prod=sql_select("select id, current_stock from product_details_master where company_id=$cbo_company_id and item_category_id=2 and detarmination_id=$fabric_desc_id and gsm=$txt_gsm $dia_width_cond and color='$color_id' and unit_of_measure=$cbouom $is_gmts_product_cond and status_active=1 and is_deleted=0");
			//and dia_width=$txt_dia_width
		}

		if(count($row_prod)>0)
		{
			$prod_id=$row_prod[0][csf('id')];
			if(str_replace("'","",$fabric_store_auto_update)==1)
			{
				$stock_qnty=$row_prod[0][csf('current_stock')];

				$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_production_qty);
				$avg_rate_per_unit=0;$stock_value=0;

				$field_array_prod_update="store_id*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*color*updated_by*update_date";

				$data_array_prod_update=$cbo_store_name."*".$avg_rate_per_unit."*".$txt_production_qty."*".$curr_stock_qnty."*".$stock_value."*".$color_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

			}
		}
		else
		{
			$prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);

			$composition_arr=array();
			$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.is_deleted=0 and a.id=".$fabric_desc_id." order by b.id asc";
			$deter_array=sql_select($sql_deter);
			if(count($deter_array)>0)
			{
				foreach( $deter_array as $row )
				{
					if(array_key_exists($row[csf('id')],$composition_arr))
					{
						$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					}
					else
					{
						$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					}
				}
			}

			if(str_replace("'","",$fabric_store_auto_update)==1)
			{
				$stock_qnty=$txt_production_qty;
				$last_purchased_qnty=$txt_production_qty;
			}
			else
			{
				$stock_qnty=0;
				$last_purchased_qnty=0;
			}

			$avg_rate_per_unit=0; $stock_value=0;

			$prod_name_dtls= $composition_arr[str_replace("'","",$fabric_desc_id)].", ".trim(str_replace("'","",$txt_gsm)).", ".trim(str_replace("'","",$txt_dia_width));
			
			//$prod_name_dtls=trim(str_replace("'","",$txt_fabric_desc)).", ".trim(str_replace("'","",$txt_gsm)).", ".trim(str_replace("'","",$txt_dia_width));

			$field_array_prod="id, company_id, store_id, item_category_id, detarmination_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, color, gsm, dia_width, is_gmts_product, inserted_by, insert_date";

			$data_array_prod="(".$prod_id.",".$cbo_company_id.",".$cbo_store_name.",2,".$fabric_desc_id.",".$txt_fabric_desc.",'".$prod_name_dtls."',".$cbouom.",".$avg_rate_per_unit.",".$last_purchased_qnty.",".$stock_qnty.",".$stock_value.",".$color_id.",".$txt_gsm.",".$txt_dia_width . "," . $is_gmts_product .",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		}

		$hdn_currency_id=str_replace("'", '',$hdn_currency_id);
		$order_rate=0; $order_amount=0; $cons_rate=0; $cons_amount=0;$dyeing_charge=0;$grey_fabric_rate=0;
		if(str_replace("'","",$fabric_store_auto_update)==1)
		{
			$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			if($is_sales ==1)
			{
				$order_rate = $txt_rate;
				$order_amount = $txt_amount;

				if($db_type==0)
				{
					$conversion_date=change_date_format($txt_production_date, "Y-m-d", "-",1);
				}
				else
				{
					$conversion_date=change_date_format($txt_production_date, "d-M-y", "-",1);
				}
				
				$exchange_rate=set_conversion_rate( $hdn_currency_id, $conversion_date );
				
				$cons_rate= number_format((str_replace("'", '',$txt_rate)*$exchange_rate),2,".","");
				$cons_amount= number_format(($cons_rate*str_replace("'","",$txt_production_qty)),2,".","");
			}
			else
			{
				if( str_replace("'","",$process_costing_maintain)==1 && str_replace("'","",$cbo_production_basis) == 5 && str_replace("'","",$batch_booking_without_order) !=1)
				{
					$dyeing_charge_string=explode("*",str_replace("'","",$knitting_charge_string));
					$dyeing_charge=$dyeing_charge_string[0];
					$grey_fabric_rate=$dyeing_charge_string[1];

					$order_rate 	= str_replace("'","",$txt_hidden_rate)*1;
					$order_amount 	= str_replace("'","",$txt_hidden_amount)*1;
					
					//As rate is in TAKA so consumtion rate, amount will be same here of order rate, amount
					$cons_rate = $order_rate;
					$cons_amount = $order_amount;
				}
			}

			$field_array_trans="id, mst_id, receive_basis, pi_wo_batch_no, company_id, prod_id, item_category, transaction_type, transaction_date, store_id, order_uom, order_qnty, order_rate, order_amount, cons_uom, cons_quantity, cons_reject_qnty, cons_rate, cons_amount, balance_qnty, balance_amount, machine_id, rack, self,floor_id,room, inserted_by, insert_date, fabric_shade, body_part_id";

			$data_array_trans="(".$id_trans.",".$finish_update_id.",".$cbo_production_basis.",".$batch_id.",".$cbo_company_id.",".$prod_id.",2,1,".$txt_production_date.",".$cbo_store_name.",".$cbouom.",".$txt_production_qty.",".$order_rate.",".$order_amount.",".$cbouom.",".$txt_production_qty.",".$txt_reject_qty.",".$cons_rate.",".$cons_amount.",".$txt_production_qty.",".$cons_amount.",".$cbo_machine_name.",".$txt_rack.",".$txt_shelf.",".$cbo_floor.",".$cbo_room.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_fabric_shade.",".$cbo_body_part.")";
		}
		else
		{
			$id_trans=0;
		}

		$id_dtls = return_next_id_by_sequence("PRO_FIN_FAB_RCV_DTLS_PK_SEQ", "pro_finish_fabric_rcv_dtls", $con);
		//$rate=0; $amount=0;
		$field_array_dtls="id, mst_id, trans_id, prod_id, batch_id, body_part_id, fabric_description_id, gsm, width, color_id, receive_qnty, process_id, reject_qty, no_of_roll, order_id, buyer_id, machine_no_id, shift_name,fabric_shade, batch_status, rack_no, shelf_no,floor,room,dia_width_type, grey_used_qty,wgt_lost_qty,remarks,inserted_by, insert_date,is_sales,uom,original_gsm,original_width,booking_no,booking_id,qc_qnty,currency_id,rate,amount,dyeing_charge,grey_fabric_rate";

		$data_array_dtls="(".$id_dtls.",".$finish_update_id.",".$id_trans.",".$prod_id.",".$batch_id.",".$cbo_body_part.",".$fabric_desc_id.",".$txt_gsm.",".$txt_dia_width.",".$color_id.",".$txt_production_qty.",".$txt_process_id.",".$txt_reject_qty.",".$txt_no_of_roll.",".$all_po_id.",".$buyer_id.",".$cbo_machine_name.",".$cbo_shift_name.",".$cbo_fabric_shade.",".$cbo_batch_status.",".$txt_rack.",".$txt_shelf.",".$cbo_floor.",".$cbo_room.",".$cbo_dia_width_type.",".$txt_grey_used.",".$hidden_wgt_qty.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$is_sales.",".$cbouom.",".$txt_original_gsm.",".$txt_original_dia_width.",".$txt_service_booking.",".$service_booking_id.",".$txt_qc_qty.",'".$hdn_currency_id."',".$cons_rate.",".$cons_amount.",".$dyeing_charge.",".$grey_fabric_rate.")";

		$field_array_batch_dtls="id, mst_id, po_id, item_description, roll_no, batch_qnty, dtls_id,width_dia_type, inserted_by, insert_date";
		$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);

		$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, reject_qnty,grey_used_qty, inserted_by, insert_date";

		$save_string=explode("!!",str_replace("'","",$save_data));

		$po_array=array();
		for($i=0;$i<count($save_string);$i++)
		{
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);

			$order_dtls=explode("**",$save_string[$i]);
			$order_id=$order_dtls[0];
			$order_qnty_roll_wise=$order_dtls[1];
			$roll_no=$order_dtls[2];
			$reject_qnty_roll_wise=$order_dtls[3];
			$grey_used_qty=$order_dtls[4];
			$wgt_lost_qty=$order_dtls[5];
			$process_loss_perc=$order_dtls[7];

			if($data_array_roll!="") $data_array_roll.=",";

			$data_array_roll.="(".$id_roll.",".$finish_update_id.",".$id_dtls.",'".$order_id."',4,'".$order_qnty_roll_wise."','".$roll_no."','".$reject_qnty_roll_wise."','".$grey_used_qty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			if(array_key_exists($order_id,$po_array))
			{
				$po_array[$order_id]['qc_qty']+=$order_qnty_roll_wise;
				$po_array[$order_id]['rej_qty']+=$reject_qnty_roll_wise;
				$po_array[$order_id]['grey_qty']+=$grey_used_qty;
				$po_array[$order_id]['wgt_qty']+=$wgt_lost_qty;
				$po_array[$order_id]['process_loss']=$process_loss_perc;
			}
			else
			{
				$po_array[$order_id]['qc_qty']=$order_qnty_roll_wise;
				$po_array[$order_id]['rej_qty']=$reject_qnty_roll_wise;
				$po_array[$order_id]['grey_qty']=$grey_used_qty;
				$po_array[$order_id]['wgt_qty']+=$wgt_lost_qty;
				$po_array[$order_id]['process_loss']=$process_loss_perc;
			}

			if(str_replace("'","",$cbo_production_basis)==4)
			{
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);

				if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
				$data_array_batch_dtls .="(".$id_dtls_batch.",'".$batch_id."',".$order_id.",'".$ItemDesc."','".$roll_no."',".$order_qnty_roll_wise.",".$id_dtls.",".$cbo_dia_width_type.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id_dtls_batch = $id_dtls_batch+1;

				if($data_array_roll_for_batch!="") $data_array_roll_for_batch.=",";
				$data_array_roll_for_batch.="(".$id_roll.",".$batch_id.",".$id_dtls_batch.",".$order_id.",2,".$order_qnty_roll_wise.",'".$roll_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
		}

		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, returnable_qnty, grey_used_qty, process_loss_perc, wgt_lost_qty, is_sales, inserted_by, insert_date";
		foreach($po_array as $key=>$val)
		{
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$order_id=$key;
			$order_qnty_qc=$val['qc_qty'];
			$order_qnty_rej=$val['rej_qty'];
			$order_qnty_grey=$val['grey_qty'];
			$order_qnty_wgt=$val['wgt_qty'];
			$process_loss=$val['process_loss'];

			if($data_array_prop!="") $data_array_prop.= ",";
			$data_array_prop.="(".$id_prop.",".$id_trans.",1,7,".$id_dtls.",'".$order_id."','".$prod_id."','".$color_id."','".$order_qnty_qc."','".$order_qnty_rej."','".$order_qnty_grey."','".$process_loss."','".$order_qnty_wgt."',".$is_sales.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}

		if(str_replace("'","",$process_costing_maintain)==1)
		{
			if(str_replace("'","",$cbo_production_basis)==5 && str_replace("'","",$fabric_store_auto_update)==1 && str_replace("'","",$batch_booking_without_order) !=1 && str_replace("'","",$is_sales) !=1)
			{
				$field_array_material="id,mst_id,dtls_id,entry_form,prod_id,item_category,used_qty,rate,amount,inserted_by,insert_date,
				status_active, is_deleted";
				$process_string=explode("*",str_replace("'","",$process_string));
				if($process_string[0]!="" && $process_string[0]!=0)
				{
					$id_material_used = return_next_id_by_sequence("PRO_MATERIAL_USED_DTLS_PK_SEQ", "pro_material_used_dtls", $con);
					$net_used=str_replace("'","",$txt_grey_used);//number_format(,4,".","");
					$gray_prod_id=str_replace("'","",$process_string[0]);
					$gray_rate=str_replace("'","",$process_string[2]);
					$gray_rate=number_format($gray_rate,4,".","");
					$used_amount=$gray_rate*$net_used;
					$used_amount=number_format($used_amount,4,".","");
					$data_array_material_used="(".$id_material_used.",".$finish_update_id.",".$id_dtls.",7,'".$gray_prod_id."',13,'".$net_used."','".$gray_rate."','".$used_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				}
			}
		}
		/*echo "10**";
		echo $data_array_material_used;
		oci_rollback($con);	die;*/

		$rID=$rID6=$rID2=$rID3=$rID4=$rID5=$rID7=$rID8=$rID9=$rID10=true;

		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_receive_master",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0;
		}

		if(str_replace("'","",$cbo_production_basis)==4) // Independent
		{
			if(count($batchData)>0)
			{
				$rID6=sql_update("pro_batch_create_mst",$field_array_batch_update,$data_array_batch_update,"id",$batch_id,0);
				if($flag==1)
				{
					if($rID6) $flag=1; else $flag=0;
				}
			}
			else
			{
				$rID6=sql_insert("pro_batch_create_mst",$field_array_batch,$data_array_batch,0);
				if($flag==1)
				{
					if($rID6) $flag=1; else $flag=0;
				}
			}
		}

		if(count($row_prod)>0)
		{
			if(str_replace("'","",$fabric_store_auto_update)==1)
			{
				$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,0);
				if($flag==1)
				{
					if($rID2) $flag=1; else $flag=0;
				}
			}
		}
		else
		{
			$rID2=sql_insert("product_details_master",$field_array_prod,$data_array_prod,0);
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}
		}

		if(str_replace("'","",$fabric_store_auto_update)==1)
		{
			$rID3=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
			if($flag==1)
			{
				if($rID3) $flag=1; else $flag=0;
			}

			/*echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;
			oci_rollback($con);
			die;*/
		}
		//echo "10**insert into pro_finish_fabric_rcv_dtls (".$field_array_dtls.") values ".$data_array_dtls;oci_rollback($con);die;
		$rID4=sql_insert("pro_finish_fabric_rcv_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1)
		{
			if($rID4) $flag=1; else $flag=0;
		}

		if($data_array_roll!="" && str_replace("'","",$roll_maintained)==1 && str_replace("'","",$batch_booking_without_order)!=1)
		{
			$rID5=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			if($flag==1)
			{
				if($rID5) $flag=1; else $flag=0;
			}
		}

		//echo "10**insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;oci_rollback($con);die;
		if($data_array_prop!="" && str_replace("'","",$batch_booking_without_order)!=1)
		{
			$rID7=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			if($flag==1)
			{
				if($rID7) $flag=1; else $flag=0;
			}
		}

		if(str_replace("'","",$batch_booking_without_order)==1 && str_replace("'","",$cbo_production_basis)==4)
		{
			$data_array_batch_dtls="(".$id_dtls_batch.",'".$batch_id."',0,'".$ItemDesc."','".$txt_no_of_roll."',".$txt_production_qty.",".$id_dtls.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}

		if($data_array_batch_dtls!="")
		{
			//echo "10**insert into pro_batch_create_dtls (".$field_array_batch_dtls.") values ".$data_array_batch_dtls;die;
			$rID8=sql_insert("pro_batch_create_dtls",$field_array_batch_dtls,$data_array_batch_dtls,1);
			if($flag==1)
			{
				if($rID8) $flag=1; else $flag=0;
			}
		}

		if($data_array_roll_for_batch!="" && str_replace("'","",$roll_maintained)==1 && str_replace("'","",$batch_booking_without_order)!=1)
		{
			//echo "insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll_for_batch;die;
			$rID9=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll_for_batch,1);
			if($flag==1)
			{
				if($rID9) $flag=1; else $flag=0;
			}
		}

		if(str_replace("'","",$process_costing_maintain)==1 && str_replace("'","",$fabric_store_auto_update)==1 && str_replace("'","",$batch_booking_without_order)!=1)
		{
			if($data_array_material_used!="")
			{
				//echo "5**insert into pro_material_used_dtls (".$field_array_material.") values ".$data_array_material_used;die;
				$rID10=sql_insert("pro_material_used_dtls",$field_array_material,$data_array_material_used,1);
				if($flag==1)
				{
					if($rID10) $flag=1; else $flag=0;
				}
			}
		}


		//echo "10**$rID=$rID6=$rID2=$rID3=$rID4=$rID5=$rID7=$rID8=$rID9*";
		//oci_rollback($con);
		//die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**".$finish_update_id."**".$finish_recv_num."**0";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".$finish_update_id."**".$finish_recv_num."**0";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**"."&nbsp;"."**0";
			}
		}

		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$flag=1;
		//$color_id=return_id( $txt_color, $color_arr, "lib_color", "id,color_name");
		if(str_replace("'","",$txt_color)!="")
		{
			if (!in_array(str_replace("'","",$txt_color),$new_array_color))
			{
				$color_id = return_id( str_replace("'","",$txt_color), $color_arr, "lib_color", "id,color_name","7");
				//echo $$txtColorName.'='.$color_id.'<br>';
				$new_array_color[$color_id]=str_replace("'","",$txt_color);

			}
			else $color_id =  array_search(str_replace("'","",$txt_color), $new_array_color);
		}
		else
		{
			$color_id=0;
		}
		$ItemDesc=str_replace("'","",$txt_fabric_desc).", ".str_replace("'","",$txt_gsm).", ".str_replace("'","",$txt_dia_width);
		if(str_replace("'","",$cbo_production_basis)==4)
		{
			$order_id=explode(",",str_replace("'","",$all_po_id));
			if(count($order_id)>1)
			{
				echo "20**0";
				disconnect($con);
				die;
			}

			$batchData=sql_select("select a.id, a.batch_weight from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.batch_no=$txt_batch_no and a.color_id='$color_id' and b.po_id=$all_po_id and a.status_active=1 and a.is_deleted=0 and a.entry_form=7 group by a.id, a.batch_weight");

			if(count($batchData)>0)
			{
				$batch_id=$batchData[0][csf('id')];
				if($batch_id==str_replace("'","",$txt_batch_id))
				{
					$curr_batch_weight=$batchData[0][csf('batch_weight')]+str_replace("'", '',$txt_production_qty)-str_replace("'", '',$hidden_receive_qnty);
					$field_array_batch_update="batch_weight*updated_by*update_date";
					$data_array_batch_update=$curr_batch_weight."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				}
				else
				{
					$batch_weight= return_field_value("batch_weight","pro_batch_create_mst","id=$txt_batch_id");
					$adjust_batch_weight=$batch_weight-str_replace("'", '',$hidden_receive_qnty);

					$curr_batch_weight=$batchData[0][csf('batch_weight')]+str_replace("'", '',$txt_production_qty);
					$field_array_batch_update="batch_weight*updated_by*update_date";
					$data_array_batch_update=$curr_batch_weight."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

				}
			}
			else
			{
				$batch_weight= return_field_value("batch_weight","pro_batch_create_mst","id=$txt_batch_id");
				$adjust_batch_weight=$batch_weight-str_replace("'", '',$hidden_receive_qnty);

				if(is_duplicate_field( "batch_no", "pro_batch_create_mst", "batch_no=$txt_batch_no" )==1)
				{
					echo "11**0";
					disconnect($con);
					die;
				}

				$batch_id = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);

				$field_array_batch="id, batch_no, entry_form, batch_date, company_id, color_id, batch_weight, booking_no, booking_no_id, inserted_by, insert_date";

				$data_array_batch="(".$batch_id.",".$txt_batch_no.",7,".$txt_production_date.",".$cbo_company_id.",'".$color_id."',".$txt_production_qty.",".$txt_booking_no.",".$txt_booking_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			}

			$batch_dtls_id= return_field_value("id","pro_batch_create_dtls","mst_id=$txt_batch_id and dtls_id=$update_dtls_id");

		}
		else
		{
			$batch_id=str_replace("'","",$txt_batch_id);
		}

		$field_array_update="receive_basis*receive_date*challan_no*store_id*location_id*knitting_location_id*knitting_source*knitting_company*updated_by*update_date";
		$data_array_update=$cbo_production_basis."*".$txt_production_date."*".$txt_challan_no."*".$cbo_store_name."*".$cbo_location."*".$cbo_location_dyeing."*".$cbo_dyeing_source."*".$cbo_dyeing_company."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$dia_width_cond="";
		if(str_replace("'","",$txt_dia_width)=="")
		{
			if($db_type==0)
			{
				$dia_width_cond = " and upper(dia_width) = $txt_dia_width";
			}
			else
			{
				$dia_width_cond = " and dia_width is null";
			}
		}
		else
		{
			$dia_width_cond = " and upper(dia_width) = $txt_dia_width";
		}

		if($is_sales==1)
		{
			$is_gmts_product=0;
		}
		else
		{
			$is_gmts_product=1;
		}
		$is_gmts_product_cond= " and is_gmts_product=$is_gmts_product";

		if(str_replace("'","",$fabric_desc_id)=="") $fabric_desc_id=0;
		if(str_replace("'","",$fabric_desc_id)==0)
		{
			$fabric_description=trim(str_replace("'","",$txt_fabric_desc));
			$row_prod=sql_select("select id, current_stock from product_details_master where company_id=$cbo_company_id and item_category_id=2 and detarmination_id=$fabric_desc_id and item_description='$fabric_description' and gsm=$txt_gsm $dia_width_cond and color='$color_id' and unit_of_measure=$cbouom $is_gmts_product_cond and status_active=1 and is_deleted=0");
			// and dia_width=$txt_dia_width
		}
		else
		{
			$row_prod=sql_select("select id, current_stock from product_details_master where company_id=$cbo_company_id and item_category_id=2 and detarmination_id=$fabric_desc_id and gsm=$txt_gsm $dia_width_cond and color='$color_id' and unit_of_measure=$cbouom $is_gmts_product_cond and status_active=1 and is_deleted=0");
			// and dia_width=$txt_dia_width
		}

		if(count($row_prod)>0)
		{
			$prod_id=$row_prod[0][csf('id')];
			if($prod_id==str_replace("'","",$previous_prod_id))
			{
				if(str_replace("'","",$fabric_store_auto_update)==1)
				{
					$stock_qnty=$row_prod[0][csf('current_stock')];
					$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_production_qty)-str_replace("'", '',$hidden_receive_qnty);
					$avg_rate_per_unit=0; $stock_value=0;

					$field_array_prod_update="store_id*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*color*updated_by*update_date";
					$data_array_prod_update=$cbo_store_name."*".$avg_rate_per_unit."*".$txt_production_qty."*".$curr_stock_qnty."*".$stock_value."*'".$color_id."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

					if($curr_stock_qnty<0)
					{
						echo "30**Stock cannot be less than zero.";disconnect($con);die;
					}
				}
			}
			else
			{
				if(str_replace("'","",$fabric_store_auto_update)==1)
				{
					$stock= return_field_value("current_stock","product_details_master","id=$previous_prod_id");
					$adjust_curr_stock=$stock-str_replace("'", '',$hidden_receive_qnty);
					$cur_st_value=0; $cur_st_rate=0;

					$field_array_adjust="current_stock*avg_rate_per_unit*stock_value";
					$data_array_adjust=$adjust_curr_stock."*".$cur_st_value."*".$cur_st_rate;

					if($adjust_curr_stock<0)
					{
						echo "30**Stock cannot be less than zero.";disconnect($con);die;
					}

					$stock_qnty=$row_prod[0][csf('current_stock')];
					$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_production_qty);
					$avg_rate_per_unit=0; $stock_value=0;

					$field_array_prod_update="store_id*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*color*updated_by*update_date";
					$data_array_prod_update=$cbo_store_name."*".$avg_rate_per_unit."*".$txt_production_qty."*".$curr_stock_qnty."*".$stock_value."*'".$color_id."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				}
			}
		}
		else
		{
			if(str_replace("'","",$fabric_store_auto_update)==1)
			{
				$stock= return_field_value("current_stock","product_details_master","id=$previous_prod_id");
				$adjust_curr_stock=$stock-str_replace("'", '',$hidden_receive_qnty);
				$cur_st_value=0; $cur_st_rate=0;

				$field_array_adjust="current_stock*avg_rate_per_unit*stock_value";
				$data_array_adjust=$adjust_curr_stock."*".$cur_st_value."*".$cur_st_rate;

				if($adjust_curr_stock<0)
				{
					echo "30**Stock cannot be less than zero.";disconnect($con);die;
				}
			}

			$prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);

			$composition_arr=array();
			$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.is_deleted=0 and a.id=".$fabric_desc_id." order by b.id asc";
			$deter_array=sql_select($sql_deter);
			if(count($deter_array)>0)
			{
				foreach( $deter_array as $row )
				{
					if(array_key_exists($row[csf('id')],$composition_arr))
					{
						$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					}
					else
					{
						$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					}
				}
			}


			if(str_replace("'","",$fabric_store_auto_update)==1)
			{
				$stock_qnty=$txt_production_qty;
				$last_purchased_qnty=$txt_production_qty;
			}
			else
			{
				$stock_qnty=0;
				$last_purchased_qnty=0;
			}

			$avg_rate_per_unit=0; $stock_value=0;

			$prod_name_dtls=$composition_arr[str_replace("'","",$fabric_desc_id)].", ".trim(str_replace("'","",$txt_gsm)).", ".trim(str_replace("'","",$txt_dia_width));

			//$prod_name_dtls=trim(str_replace("'","",$txt_fabric_desc)).", ".trim(str_replace("'","",$txt_gsm)).", ".trim(str_replace("'","",$txt_dia_width));

			$field_array_prod="id, company_id, store_id, item_category_id, detarmination_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, color, gsm, dia_width, is_gmts_product, inserted_by, insert_date";

			$data_array_prod="(".$prod_id.",".$cbo_company_id.",".$cbo_store_name.",2,".$fabric_desc_id.",".$txt_fabric_desc.",'".$prod_name_dtls."',".$cbouom.",".$avg_rate_per_unit.",".$last_purchased_qnty.",".$stock_qnty.",".$stock_value.",".$color_id.",".$txt_gsm.",".$txt_dia_width ."," .$is_gmts_product. ",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
		}

		$hdn_currency_id = str_replace("'", "", $hdn_currency_id);
		$order_rate=0; $order_amount=0; $cons_rate=0; $cons_amount=0; $dyeing_charge=0; $grey_fabric_rate=0;
		if(str_replace("'","",$fabric_store_auto_update)==1)
		{
			if($is_sales ==1)
			{
				$order_rate = $txt_rate;
				$order_amount = $txt_amount;

				if($db_type==0)
				{
					$conversion_date=change_date_format($txt_production_date, "Y-m-d", "-",1);
				}
				else
				{
					$conversion_date=change_date_format($txt_production_date, "d-M-y", "-",1);
				}
				
				$exchange_rate=set_conversion_rate( $hdn_currency_id, $conversion_date );
				
				$cons_rate= number_format((str_replace("'", '',$txt_rate)*$exchange_rate),2,".","");
				$cons_amount= number_format(($cons_rate*str_replace("'","",$txt_production_qty)),2,".","");
			}
			else
			{
				if( str_replace("'","",$process_costing_maintain)==1 && str_replace("'","",$cbo_production_basis) == 5 && str_replace("'","",$batch_booking_without_order) !=1)
				{
					$order_rate 	= str_replace("'","",$txt_hidden_rate)*1;
					$order_amount 	= str_replace("'","",$txt_hidden_amount)*1;
					$cons_rate 		= str_replace("'","",$txt_hidden_rate)*1;
					$cons_amount 	= str_replace("'","",$txt_hidden_amount)*1;

					$dyeing_charge_data=explode("*",str_replace("'","",$knitting_charge_string));
					$dyeing_charge=$dyeing_charge_data[0];
					$grey_fabric_rate=$dyeing_charge_data[1];
					$material_deleted_id=$dyeing_charge_data[2];
				}
			}

			$sqlBl = sql_select("select cons_quantity,cons_amount,balance_qnty,balance_amount from inv_transaction where id=$update_trans_id");
			$before_receive_qnty	= $sqlBl[0][csf("cons_quantity")];
			$beforeAmount			= $sqlBl[0][csf("cons_amount")];
			$beforeBalanceQnty		= $sqlBl[0][csf("balance_qnty")];
			$beforeBalanceAmount	= $sqlBl[0][csf("balance_amount")];

			$adjBalanceQnty		=$beforeBalanceQnty-$before_receive_qnty+str_replace("'", '',$txt_production_qty);
			$adjBalanceAmount	=$beforeBalanceAmount-$beforeAmount+$con_amount;

			$field_array_trans_update="receive_basis*pi_wo_batch_no*prod_id*transaction_date*store_id*order_uom*cons_uom*order_qnty*order_rate*order_amount*cons_quantity*cons_reject_qnty*cons_rate*cons_amount*balance_qnty*balance_amount*machine_id*rack*self*floor_id*room*updated_by*update_date*fabric_shade*body_part_id";

			$data_array_trans_update=$cbo_production_basis."*'".$batch_id."'*".$prod_id."*".$txt_production_date."*".$cbo_store_name."*".$cbouom."*".$cbouom."*".$txt_production_qty."*".$order_rate."*".$order_amount."*".$txt_production_qty."*".$txt_reject_qty."*".$cons_rate."*".$cons_amount."*".$adjBalanceQnty."*".$adjBalanceAmount."*".$cbo_machine_name."*".$txt_rack."*".$txt_shelf."*".$cbo_floor."*".$cbo_room."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_fabric_shade."*".$cbo_body_part;

		}

		$field_array_dtls_update="prod_id*batch_id*body_part_id*fabric_description_id*gsm*width*color_id*receive_qnty*process_id*reject_qty*no_of_roll*order_id*buyer_id*machine_no_id*shift_name*fabric_shade*batch_status*rack_no*shelf_no*floor*room*dia_width_type*grey_used_qty*wgt_lost_qty*remarks*updated_by*update_date*is_sales*uom*original_gsm*original_width*booking_no*booking_id*qc_qnty*currency_id*rate*amount*dyeing_charge*grey_fabric_rate";

		$data_array_dtls_update=$prod_id."*'".$batch_id."'*".$cbo_body_part."*".$fabric_desc_id."*".$txt_gsm."*".$txt_dia_width."*".$color_id."*".$txt_production_qty."*".$txt_process_id."*".$txt_reject_qty."*".$txt_no_of_roll."*".$all_po_id."*".$buyer_id."*".$cbo_machine_name."*".$cbo_shift_name."*".$cbo_fabric_shade."*".$cbo_batch_status."*".$txt_rack."*".$txt_shelf."*".$cbo_floor."*".$cbo_room."*".$cbo_dia_width_type."*".$txt_grey_used."*".$hidden_wgt_qty."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$is_sales."*".$cbouom."*".$txt_original_gsm."*".$txt_original_dia_width."*".$txt_service_booking."*".$service_booking_id."*".$txt_qc_qty."*'".$hdn_currency_id."'*".$cons_rate."*".$cons_amount."*".$dyeing_charge."*".$grey_fabric_rate;

		$field_array_batch_dtls="id, mst_id, po_id, item_description, roll_no, batch_qnty, dtls_id,width_dia_type, inserted_by, insert_date";

		$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, reject_qnty, grey_used_qty,inserted_by, insert_date";

		$save_string=explode("!!",str_replace("'","",$save_data));
		$po_array=array();
		for($i=0;$i<count($save_string);$i++)
		{
			$order_dtls=explode("**",$save_string[$i]);
			/*$count_save_data=count($order_dtls);
			if($count_save_data==6)
			{
				$order_id=$order_dtls[0];
				$order_qnty_roll_wise=$order_dtls[1];
				$roll_no=$order_dtls[2];
				$reject_qnty_roll_wise=$order_dtls[3];
				$grey_qnty_roll_wise=$order_dtls[4];
				$wgt_qnty_roll_wise=$order_dtls[5];
			}
			else
			{
				$order_id=$order_dtls[0];
				$order_qnty_roll_wise=$order_dtls[1];
				$reject_qnty_roll_wise=$order_dtls[3];
				$grey_qnty_roll_wise=$order_dtls[4];
				$wgt_qnty_roll_wise=$order_dtls[5];
				$roll_no="";
			}*/

			$order_id=$order_dtls[0];
			$order_qnty_roll_wise=$order_dtls[1];
			$roll_no=$order_dtls[2];
			$reject_qnty_roll_wise=$order_dtls[3];
			$grey_qnty_roll_wise=$order_dtls[4];
			$wgt_qnty_roll_wise=$order_dtls[5];
			$process_loss_perc=$order_dtls[7];


			if($data_array_roll!="" ) $data_array_roll.=",";
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
			$data_array_roll.="(".$id_roll.",".$update_id.",".$update_dtls_id.",'".$order_id."',4,'".$order_qnty_roll_wise."','".$roll_no."','".$reject_qnty_roll_wise."','".$grey_qnty_roll_wise."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			if(array_key_exists($order_id,$po_array))
			{
				$po_array[$order_id]['qc_qty']+=$order_qnty_roll_wise;
				$po_array[$order_id]['rej_qty']+=$reject_qnty_roll_wise;
				$po_array[$order_id]['grey_qty']+=$grey_qnty_roll_wise;
				$po_array[$order_id]['wgt_qty']+=$wgt_qnty_roll_wise;
				$po_array[$order_id]['process_loss']=$process_loss_perc;
			}
			else
			{
				$po_array[$order_id]['qc_qty']=$order_qnty_roll_wise;
				$po_array[$order_id]['rej_qty']=$reject_qnty_roll_wise;
				$po_array[$order_id]['grey_qty']+=$grey_qnty_roll_wise;
				$po_array[$order_id]['wgt_qty']+=$wgt_qnty_roll_wise;
				$po_array[$order_id]['process_loss']=$process_loss_perc;
			}

			if(str_replace("'","",$cbo_production_basis)==4)
			{
				$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);

				if($data_array_batch_dtls!="" ) $data_array_batch_dtls.=",";

				$data_array_batch_dtls .="(".$id_dtls_batch.",'".$batch_id."',".$order_id.",'".$ItemDesc."','".$roll_no."',".$order_qnty_roll_wise.",".$update_dtls_id.",".$cbo_dia_width_type.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				if($data_array_roll_for_batch!="" ) $data_array_roll_for_batch.=",";
				$data_array_roll_for_batch.="(".$id_roll.",'".$batch_id."',".$id_dtls_batch.",".$order_id.",2,".$order_qnty_roll_wise.",'".$roll_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
		}

		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, returnable_qnty, grey_used_qty, process_loss_perc, wgt_lost_qty, is_sales, inserted_by, insert_date";

		foreach($po_array as $key=>$val)
		{
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);

			$order_id=$key;
			$order_qnty=$val;
			$order_qnty_qc=$val['qc_qty'];
			$order_qnty_rej=$val['rej_qty'];
			$order_qnty_grey=$val['grey_qty'];
			$order_qnty_wgt=$val['wgt_qty'];
			$process_loss=$val['process_loss'];

			if($data_array_prop!="" ) $data_array_prop.=",";
			$data_array_prop.="(".$id_prop.",".$update_trans_id.",1,7,".$update_dtls_id.",'".$order_id."','".$prod_id."','".$color_id."','".$order_qnty_qc."','".$order_qnty_rej."','".$order_qnty_grey."','".$process_loss."','".$order_qnty_wgt."',".$is_sales.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}


		if(str_replace("'","",$process_costing_maintain)==1)
		{
			if(str_replace("'","",$cbo_production_basis)==5 && str_replace("'","",$fabric_store_auto_update)==1 && str_replace("'","",$batch_booking_without_order) !=1 && str_replace("'","",$is_sales) !=1)
			{

				$field_array_material="id,mst_id,dtls_id,entry_form,prod_id,item_category,used_qty,rate,amount,inserted_by,insert_date,status_active, is_deleted";
				$field_array_material_update="used_qty*rate*amount*updated_by*update_date";
				$product_dtls=explode("*",str_replace("'","",$process_string));

				$used_prod_id=$product_dtls[0];
				$net_used=number_format($product_dtls[1],4,".","");
				$gray_rate=number_format($product_dtls[2],4,".","");
				$used_amount=number_format($gray_rate*$net_used,4,".","");
				$material_update_id=$product_dtls[3];
				if($material_update_id>0)
				{
					$material_id_arr[]=$material_update_id;
					$material_data_array_update[$material_update_id]=explode("*",("'".$net_used."'*'".$gray_rate."'* '".$used_amount."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
				else
				{
					$id_material_used = return_next_id_by_sequence("PRO_MATERIAL_USED_DTLS_PK_SEQ", "pro_material_used_dtls", $con);
					$data_array_material_used="(".$id_material_used.",".$update_id.",".$update_dtls_id.",7,'".$used_prod_id."',13,'".$net_used."','".$gray_rate."','".$used_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				}
			}
		}


		if(str_replace("'","",$cbo_production_basis)==4)
		{
			if(count($batchData)>0)
			{
				if($batch_id==str_replace("'","",$txt_batch_id))
				{
					$rID6=sql_update("pro_batch_create_mst",$field_array_batch_update,$data_array_batch_update,"id",$batch_id,0);
					if($flag==1)
					{
						if($rID6) $flag=1; else $flag=0;
					}
				}
				else
				{
					$rID_batch_adjust=sql_update("pro_batch_create_mst","batch_weight",$adjust_batch_weight,"id",$txt_batch_id,0);
					if($flag==1)
					{
						if($rID_batch_adjust) $flag=1; else $flag=0;
					}

					$rID6=sql_update("pro_batch_create_mst",$field_array_batch_update,$data_array_batch_update,"id",$batch_id,0);
					if($flag==1)
					{
						if($rID6) $flag=1; else $flag=0;
					}
				}
			}
			else
			{
				$rID_batch_adjust=sql_update("pro_batch_create_mst","batch_weight",$adjust_batch_weight,"id",$txt_batch_id,0);
				if($flag==1)
				{
					if($rID_batch_adjust) $flag=1; else $flag=0;
				}

				$rID6=sql_insert("pro_batch_create_mst",$field_array_batch,$data_array_batch,0);
				if($flag==1)
				{
					if($rID6) $flag=1; else $flag=0;
				}
			}

			$delete_batch_dtls=execute_query( "delete from pro_batch_create_dtls where mst_id=$txt_batch_id and dtls_id=$update_dtls_id",0);
			if($flag==1)
			{
				if($delete_batch_dtls) $flag=1; else $flag=0;
			}

			if(str_replace("'","",$roll_maintained)==1)
			{
				$delete_batch_roll=execute_query( "delete from pro_roll_details where mst_id=$txt_batch_id and dtls_id='$batch_dtls_id' and entry_form=2",0);
				if($flag==1)
				{
					if($delete_batch_roll) $flag=1; else $flag=0;
				}
			}
		}

		$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,1);
		if($flag==1)
		{
			if($rID) $flag=1; else $flag=0;
		}

		if(count($row_prod)>0)
		{
			$prod_id=$row_prod[0][csf('id')];
			if($prod_id==str_replace("'","",$previous_prod_id))
			{
				if(str_replace("'","",$fabric_store_auto_update)==1)
				{
					$rIDP=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,0);
					if($flag==1)
					{
						if($rIDP) $flag=1; else $flag=0;
					}
				}
			}
			else
			{
				if(str_replace("'","",$fabric_store_auto_update)==1)
				{
					$rID_adjust=sql_update("product_details_master",$field_array_adjust,$data_array_adjust,"id",$previous_prod_id,0);
					if($flag==1)
					{
						if($rID_adjust) $flag=1; else $flag=0;
					}

					$rIDP=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,0);
					if($flag==1)
					{
						if($rIDP) $flag=1; else $flag=0;
					}
				}
			}
		}
		else
		{
			if(str_replace("'","",$fabric_store_auto_update)==1)
			{
				$rID_adjust=sql_update("product_details_master",$field_array_adjust,$data_array_adjust,"id",$previous_prod_id,0);
				if($flag==1)
				{
					if($rID_adjust) $flag=1; else $flag=0;
				}
			}

			$rIDP=sql_insert("product_details_master",$field_array_prod,$data_array_prod,0);
			if($flag==1)
			{
				if($rIDP) $flag=1; else $flag=0;
			}
		}
		

		if(str_replace("'","",$fabric_store_auto_update)==1)
		{
			$rID3=sql_update("inv_transaction",$field_array_trans_update,$data_array_trans_update,"id",$update_trans_id,0);
			if($flag==1)
			{
				if($rID3) $flag=1; else $flag=0;
			}
		}

		/*echo "10**";
		echo $data_array_dtls_update;
		oci_rollback($con);
		die;*/

		$rID4=sql_update("pro_finish_fabric_rcv_dtls",$field_array_dtls_update,$data_array_dtls_update,"id",$update_dtls_id,0);
		if($flag==1)
		{
			if($rID4) $flag=1; else $flag=0;
		}
		if(str_replace("'","",$roll_maintained)==1)
		{
			$delete_roll=execute_query( "delete from pro_roll_details where dtls_id=$update_dtls_id and entry_form=4",0);
			if($flag==1)
			{
				if($delete_roll) $flag=1; else $flag=0;
			}
		}
		if($data_array_roll!="" && str_replace("'","",$roll_maintained)==1 && str_replace("'","",$batch_booking_without_order)!=1)
		{
			//echo "insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
			$rID5=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			if($flag==1)
			{
				if($rID5) $flag=1; else $flag=0;
			}
		}
		$delete_prop=execute_query( "delete from order_wise_pro_details where dtls_id=$update_dtls_id and trans_id=$update_trans_id and entry_form=7",0);
		if($flag==1)
		{
			if($delete_prop) $flag=1; else $flag=0;
		}
		//echo "10**insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;oci_rollback($con);die;
		if($data_array_prop!="" && str_replace("'","",$batch_booking_without_order)!=1)
		{
			$rID6=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			if($flag==1)
			{
				if($rID6) $flag=1; else $flag=0;
			}
		}

		if(str_replace("'","",$batch_booking_without_order)==1 && str_replace("'","",$cbo_production_basis)==4)
		{
			$data_array_batch_dtls="(".$id_dtls_batch.",'".$batch_id."',0,'".$ItemDesc."','".$txt_no_of_roll."',".$txt_production_qty.",".$update_dtls_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}

		if($data_array_batch_dtls!="")
		{
			//echo "insert into pro_batch_create_dtls (".$field_array_batch_dtls.") values ".$data_array_batch_dtls;die;
			$rID7=sql_insert("pro_batch_create_dtls",$field_array_batch_dtls,$data_array_batch_dtls,1);
			if($flag==1)
			{
				if($rID7) $flag=1; else $flag=0;
			}
		}

		if($data_array_roll_for_batch!="" && str_replace("'","",$roll_maintained)==1 && str_replace("'","",$batch_booking_without_order)!=1)
		{
			//echo "insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll_for_batch;die;
			$rID8=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll_for_batch,1);
			if($flag==1)
			{
				if($rID8) $flag=1; else $flag=0;
			}
		}
		//echo "10**";
		if(str_replace("'","",$process_costing_maintain)==1 && str_replace("'","",$fabric_store_auto_update)==1 && str_replace("'","",$batch_booking_without_order)!=1)
		{
			if(count($material_data_array_update)>0)
			{
				//echo bulk_update_sql_statement( "pro_material_used_dtls", "id", $field_array_material_update, $material_data_array_update, $material_id_arr )."<br>";
				$materialUpdate=execute_query(bulk_update_sql_statement( "pro_material_used_dtls", "id", $field_array_material_update, $material_data_array_update, $material_id_arr ));
				if($flag==1)
				{
					if($materialUpdate) $flag=1; else $flag=0;
				}
			}

			if($data_array_material_used!="")
			{
				//echo "insert into pro_material_used_dtls (".$field_array_material.") values ".$data_array_material_used;die;
				$rID8=sql_insert("pro_material_used_dtls",$field_array_material,$data_array_material_used,0);
				if($flag==1)
				{
					if($rID8) $flag=1; else $flag=0;
				}
			}
			if($material_deleted_id!="")
			{
				$deletedMaterial=execute_query( "delete from pro_material_used_dtls where id in($material_deleted_id) ",0);
				if($flag==1)
				{
					if($deletedMaterial) $flag=1; else $flag=0;
				}
			}
		}


		//echo "10**".$flag;oci_rollback($con);die;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**0**0**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**0**0**1";
			}
		}
		disconnect($con);
		die;
	}
}


if($action=="show_finish_fabric_listview")
{
	$machine_arr = return_library_array("select id, machine_no as machine_name from lib_machine_name","id","machine_name");
	$fabric_desc_arr=return_library_array("select id, item_description from product_details_master where item_category_id=2","id","item_description");
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
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}

	$sql="select id, prod_id, batch_id, body_part_id, fabric_description_id, gsm, width, color_id, receive_qnty, reject_qty, machine_no_id from pro_finish_fabric_rcv_dtls where mst_id='$data' and status_active = '1' and is_deleted = '0'";

	$result=sql_select($sql);
	$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst","id","batch_no");
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table">
		<thead>
			<th width="80">Batch</th>
			<th width="100">Body Part</th>
			<th width="150">Fabric Description</th>
			<th width="60">GSM</th>
			<th width="70">Dia / Width</th>
			<th width="80">Color</th>
			<th width="80">QC Pass Qty</th>
			<th width="80">Reject Qty</th>
			<th>Machine No</th>
		</thead>
	</table>
	<div style="width:820px; max-height:200px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="list_view">
			<?
			$i=1;
			foreach($result as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if($row[csf('fabric_description_id')]==0 || $row[csf('fabric_description_id')]=="")
					$fabric_desc=$fabric_desc_arr[$row[csf('prod_id')]];
				else
					$fabric_desc=$composition_arr[$row[csf('fabric_description_id')]];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_data_dtls_part(<? echo $row[csf('id')]; ?>,'populate_finish_details_form_data', 'requires/finish_fabric_receive_controller');">
					<td width="80"><p><? echo $batch_arr[$row[csf('batch_id')]]; ?></p></td>
					<td width="100"><p><? echo $body_part[$row[csf('body_part_id')]]; ?>&nbsp;</p></td>
					<td width="150"><p><? echo $fabric_desc; ?></p></td>
					<td width="60"><p><? echo $row[csf('gsm')]; ?>&nbsp;</p></td>
					<td width="70"><p><? echo $row[csf('width')]; ?>&nbsp;</p></td>
					<td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?>&nbsp;</p></td>
					<td width="80" align="right"><? echo number_format($row[csf('receive_qnty')],2); ?></td>
					<td width="80" align="right"><? echo number_format($row[csf('reject_qty')],2); ?>&nbsp;</td>
					<td><p><? echo $machine_arr[$row[csf('machine_no_id')]]; ?>&nbsp;</p></td>
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

if($action=='populate_finish_details_form_data')
{
	$data=explode("**",$data);
	$id=$data[0];
	$roll_maintained=$data[1];
	$company_id=$data[2];
	$process_costing_maintain=$data[3];

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst","id","batch_no");
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');

	$fabric_store_auto_update=return_field_value("auto_update","variable_settings_production","company_name='$company_id' and variable_list=15 and item_category_id=2 and is_deleted=0 and status_active=1");

	if($fabric_store_auto_update == 1)
	{
		$data_array=sql_select("select a.KNITTING_SOURCE, a.company_id,a.receive_basis, b.id, b.trans_id,b.shift_name,b.fabric_shade, b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id, b.gsm, b.width, b.color_id, b.receive_qnty, b.process_id, b.reject_qty,b.wgt_lost_qty, b.no_of_roll, b.machine_no_id, b.batch_status, b.rack_no, b.shelf_no,b.room,b.floor, b.order_id,b.mst_id, b.is_sales,b.buyer_id,b.dia_width_type,b.remarks,b.grey_used_qty, b.is_sales,b.uom, b.original_gsm, b.original_width, b.booking_no, b.booking_id,b.qc_qnty, b.currency_id, c.order_rate, c.order_amount, b.grey_fabric_rate, b.dyeing_charge from inv_receive_master a, pro_finish_fabric_rcv_dtls b, inv_transaction c where a.id=b.mst_id and b.trans_id = c.id and b.id='$id' and a.item_category=2 and a.entry_form=7");
	}
	else
	{
		$data_array=sql_select("select  a.KNITTING_SOURCE, a.company_id,a.receive_basis, b.id, b.trans_id,b.shift_name,b.fabric_shade, b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id, b.gsm, b.width, b.color_id, b.receive_qnty, b.process_id, b.reject_qty,b.wgt_lost_qty, b.no_of_roll, b.machine_no_id, b.batch_status, b.rack_no, b.shelf_no,b.room,b.floor, b.order_id,b.mst_id, b.is_sales,b.buyer_id,b.dia_width_type,b.remarks,b.grey_used_qty, b.is_sales,b.uom, b.original_gsm, b.original_width, b.booking_no, b.booking_id,b.qc_qnty, b.currency_id, 0 as order_rate, 0 as order_amount, b.grey_fabric_rate, b.dyeing_charge from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and b.id='$id' and a.item_category=2 and a.entry_form=7");
	}

	foreach ($data_array as $row)
	{
		$buyer_name='';
		$buyer=explode(",",$row[csf('buyer_id')]);
		foreach($buyer as $val )
		{
			if($buyer_name=='') $buyer_name=$buyer_arr[$val]; else $buyer_name.=",".$buyer_arr[$val];
		}

		$comp='';
		if($row[csf('fabric_description_id')]==0 || $row[csf('fabric_description_id')]=="")
		{
			$comp=return_field_value("item_description","product_details_master","id=".$row[csf('prod_id')]);
		}
		else
		{
			$determination_sql=sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=".$row[csf('fabric_description_id')]);

			if($determination_sql[0][csf('construction')]!="")
			{
				$comp=$determination_sql[0][csf('construction')].", ";
			}

			foreach( $determination_sql as $d_row )
			{
				$comp.=$composition[$d_row[csf('copmposition_id')]]." ".$d_row[csf('percent')]."% ";
			}
		}

		echo "document.getElementById('txt_batch_id').value 				= '".$row[csf("batch_id")]."';\n";
		echo "document.getElementById('txt_batch_no').value 				= '".$batch_arr[$row[csf("batch_id")]]."';\n";
		echo "document.getElementById('is_sales').value 					= '".$row[csf("is_sales")]."';\n";
		echo "document.getElementById('cbouom').value 						= '".$row[csf("uom")]."';\n";
		echo "document.getElementById('txt_service_booking').value 			= '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('service_booking_id').value 			= '".$row[csf("booking_id")]."';\n";


		$sql_batch_from_delivery=sql_select("select batch_id from pro_grey_prod_delivery_dtls where grey_sys_id='".$row[csf("mst_id")]."' and product_id='".$row[csf('prod_id')]."' and entry_form=54 and status_active=1 and is_deleted=0");
		$batchID=0;
		foreach ($sql_batch_from_delivery as $value) {
			$batchID=1;
		}
		if ($batchID==1 && $row[csf("is_sales")]==1) {
			echo "$('#cbouom').attr('disabled',true);\n";
		}
		else
		{
			echo "$('#cbouom').attr('disabled',false);\n";
		}

		if($row[csf('receive_basis')]==5)
		{	

			//echo "get_php_form_data('".$row[csf("batch_id")]."', 'populate_data_from_batch', 'requires/finish_fabric_receive_controller' );\n";
			echo "get_php_form_data('".$row[csf('batch_id')] ."'+'_'+'".  $row[csf('fabric_description_id')] ."'+'_'+'". $row[csf('body_part_id')] ."'+'_'+'". $row[csf('dia_width_type')]."'+'_'+'". $row[csf('gsm')]."'+'_'+'". $row[csf('original_width')]."', 'populate_data_from_batch', 'requires/finish_fabric_receive_controller' );\n";

			echo "show_list_view('".$row[csf("batch_id")]."_".$row[csf("is_sales")]."','show_fabric_desc_listview','list_fabric_desc_container','requires/finish_fabric_receive_controller','');\n";

			echo "$('#txt_gsm').attr('disabled',true);\n";
			//echo "$('#txt_dia_width').attr('disabled',true);\n";
			echo "$('#cbouom').attr('disabled',true);\n";
		}
		else
		{
			echo "get_php_form_data('".$row[csf("order_id")]."', 'load_color', 'requires/finish_fabric_receive_controller' );\n";
		}

		$process_name='';
		$process_id_array=explode(",",$row[csf("process_id")]);
		foreach($process_id_array as $val)
		{
			if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
		}

		echo "load_drop_down('requires/finish_fabric_receive_controller', '', 'load_drop_down_all_body_part','body_part_td');";

		echo "load_drop_down('requires/finish_fabric_receive_controller', ".$row[csf("knitting_source")]."+'_'+".$row[csf("company_id")].", 'load_drop_down_dyeing_com_new', 'dyeingcom_td' );\n";
		
		echo "document.getElementById('cbo_body_part').value 				= '".$row[csf("body_part_id")]."';\n";
		echo "$('#cbo_body_part').attr('disabled',true);\n";
		echo "document.getElementById('txt_fabric_desc').value 				= '".$comp."';\n";
		echo "document.getElementById('fabric_desc_id').value 				= '".$row[csf("fabric_description_id")]."';\n";
		echo "document.getElementById('txt_color').value 					= '".$color_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('txt_gsm').value 						= '".$row[csf("gsm")]."';\n";
		echo "document.getElementById('cbo_dia_width_type').value 			= '".$row[csf("dia_width_type")]."';\n";
		echo "document.getElementById('txt_original_gsm').value 			= '".$row[csf("original_gsm")]."';\n";
		echo "document.getElementById('txt_original_dia_width').value 		= '".$row[csf("original_width")]."';\n";
		echo "document.getElementById('txt_dia_width_show').value 			= '".$row[csf("original_width")]."';\n";
		echo "document.getElementById('txt_dia_width').value 				= '".$row[csf("width")]."';\n";
		echo "document.getElementById('txt_production_qty').value 			= '".$row[csf("receive_qnty")]."';\n";
		echo "document.getElementById('txt_process_name').value 			= '".$process_name."';\n";
		echo "document.getElementById('txt_process_id').value 				= '".$row[csf("process_id")]."';\n";
		echo "document.getElementById('cbo_fabric_shade').value 			= '".$row[csf("fabric_shade")]."';\n";
		echo "document.getElementById('txt_reject_qty').value 				= '".$row[csf("reject_qty")]."';\n";
		echo "document.getElementById('txt_qc_qty').value 					= '".$row[csf("qc_qnty")]."';\n";
		echo "document.getElementById('txt_no_of_roll').value 				= '".$row[csf("no_of_roll")]."';\n";
		echo "document.getElementById('buyer_name').value 					= '".$buyer_name."';\n";
		echo "document.getElementById('buyer_id').value 					= '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('cbo_machine_name').value 			= '".$row[csf("machine_no_id")]."';\n";
		echo "document.getElementById('cbo_batch_status').value 			= '".$row[csf("batch_status")]."';\n";
		echo "document.getElementById('cbo_shift_name').value 				= '".$row[csf("shift_name")]."';\n";
		echo "load_room_rack_self_bin('requires/finish_fabric_receive_controller', 'floor','floor_td', '".$row[csf('company_id')]."','"."',$('#cbo_store_name').val(),this.value);\n";
		echo "document.getElementById('cbo_floor').value 					= '".$row[csf("floor")]."';\n";
		echo "load_room_rack_self_bin('requires/finish_fabric_receive_controller', 'room','room_td', '".$row[csf('company_id')]."','"."',$('#cbo_store_name').val(), '".$row[csf('floor')]."',this.value);\n";
		echo "document.getElementById('cbo_room').value 					= '".$row[csf("room")]."';\n";
		echo "load_room_rack_self_bin('requires/finish_fabric_receive_controller', 'rack','rack_td', '".$row[csf('company_id')]."','"."',$('#cbo_store_name').val(), '".$row[csf('floor')]."', '".$row[csf('room')]."',this.value);\n";
		echo "document.getElementById('txt_rack').value 					= '".$row[csf("rack_no")]."';\n";
		echo "load_room_rack_self_bin('requires/finish_fabric_receive_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','"."',$('#cbo_store_name').val(), '".$row[csf('floor')]."','".$row[csf('room')]."','".$row[csf('rack_no')]."',this.value);\n";
		echo "document.getElementById('txt_shelf').value 					= '".$row[csf("shelf_no")]."';\n";
		echo "document.getElementById('hidden_receive_qnty').value 			= '".$row[csf("receive_qnty")]."';\n";
		echo "document.getElementById('hidden_wgt_qty').value 				= '".$row[csf("wgt_lost_qty")]."';\n";
		echo "document.getElementById('all_po_id').value 					= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('previous_prod_id').value 			= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('update_trans_id').value 				= '".$row[csf("trans_id")]."';\n";
		echo "document.getElementById('txt_grey_used').value 				= '".$row[csf("grey_used_qty")]."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('is_sales').value 					= '".$row[csf("is_sales")]."';\n";
		echo "document.getElementById('hdn_currency_id').value 				= '".$row[csf("currency_id")]."';\n";


		if($process_costing_maintain==1 && $fabric_store_auto_update == 1)
		{
			echo "document.getElementById('txt_rate').value 					= '".number_format($row[csf("order_rate")],4,'.','')."';\n";
			echo "document.getElementById('txt_amount').value 					= '".number_format($row[csf("order_amount")],2,'.','')."';\n";
		}
		else
		{
			echo "document.getElementById('txt_rate').value 					= '".$row[csf("order_rate")]."';\n";
			echo "document.getElementById('txt_amount').value 					= '".$row[csf("order_amount")]."';\n";
		}
		
		echo "document.getElementById('txt_hidden_rate').value 				= '".$row[csf("order_rate")]."';\n";
		echo "document.getElementById('txt_hidden_amount').value 			= '".$row[csf("order_amount")]."';\n";
		echo "document.getElementById('check_production_qty').value 		= '".$row[csf("receive_qnty")]."';\n";

		$save_string=''; $fso_for_vali="";
		if($roll_maintained==1)
		{
			$data_roll_array=sql_select("select id, po_breakdown_id, qnty, roll_no, reject_qnty, grey_used_qty from pro_roll_details where dtls_id='$id' and entry_form=4 and status_active=1 and is_deleted=0");
			foreach($data_roll_array as $row_roll)
			{
				if($save_string=="")
				{
					$save_string=$row_roll[csf("po_breakdown_id")]."**".$row_roll[csf("qnty")]."**".$row_roll[csf("roll_no")]."**".$row_roll[csf("reject_qnty")]."**".$row_roll[csf("grey_used_qty")]."**".''."**".''."**".'';

					//save_string=txtPoId+"**"+txtfinishQnty+"**"+txtRoll+"**"+txtrejectQnty+"**"+txtgreyQnty+"**"+txtwgtlostQnty+"**"+sales_booking_no+"**"+txtProcessQnty;
				}
				else
				{
					$save_string.="!!".$row_roll[csf("po_breakdown_id")]."**".$row_roll[csf("qnty")]."**".$row_roll[csf("roll_no")] ."**".$row_roll[csf("reject_qnty")]."**".$row_roll[csf("grey_used_qty")]."**".''."**".''."**".'';
				}
			}

			$fso_for_vali .= $row_roll[csf("po_breakdown_id")].",";
		}
		else
		{
			$data_po_array=sql_select("select po_breakdown_id, quantity, returnable_qnty,grey_used_qty,wgt_lost_qty,process_loss_perc from order_wise_pro_details where dtls_id='$id' and entry_form=7 and status_active=1 and is_deleted=0");
			foreach($data_po_array as $row_po)
			{
				if($save_string=="")
				{
					$save_string=$row_po[csf("po_breakdown_id")]."**".$row_po[csf("quantity")]."**0**".$row_po[csf("returnable_qnty")]."**".$row_po[csf("grey_used_qty")]."**".$row_po[csf("wgt_lost_qty")]."**".''."**".$row_po[csf("process_loss_perc")];
				}
				else
				{
					$save_string.="!!".$row_po[csf("po_breakdown_id")]."**".$row_po[csf("quantity")]."**0**".$row_po[csf("returnable_qnty")]."**".$row_po[csf("grey_used_qty")]."**".$row_po[csf("wgt_lost_qty")]."**".''."**".$row_po[csf("process_loss_perc")];;
				}

				$fso_for_vali .= $row_po[csf("po_breakdown_id")].",";

				$hidden_grey_used_po += $row_po[csf("grey_used_qty")]*1;
			}
		}
		echo "document.getElementById('save_data').value 				= '".$save_string."';\n";
		echo "document.getElementById('hidden_grey_used_po').value 		= '".$hidden_grey_used_po."';\n";

		$fso_for_vali = chop($fso_for_vali,",");

		if($row[csf("is_sales")] == 1)
		{
			$is_fso_service = sql_select("select sales_order_type from fabric_sales_order_mst where id in ($fso_for_vali)");
		}
		$sales_order_type = $is_fso_service[0][csf("sales_order_type")];
		echo "document.getElementById('sales_order_type').value 				= '".$sales_order_type."';\n";
		//-------------------------------------------------------------------------------------------------
		//echo $process_costing_maintain."=".$fabric_store_auto_update."=".$row[csf('receive_basis')]."=".$row[csf('is_sales')];die;

		if($process_costing_maintain==1 && $fabric_store_auto_update == 1 && $row[csf('batch_booking_without_order')] !=1 && $row[csf('receive_basis')]==5 && $row[csf('is_sales')] !=1)
		{
			$dyeing_charge=$row[csf("dyeing_charge")];

			//grey_fabric_rate, b.dyeing_charge

			$material_data=sql_select("select * from pro_material_used_dtls where dtls_id='$id' and entry_form=7");
			foreach($material_data as $value)
			{
				$grey_used=$value[csf('used_qty')];
				$material_update_id=$value[csf('id')];
				$grey_rate=$value[csf('rate')];
				$grey_amount=$value[csf('amount')];
				$grey_grey_product_id=$value[csf('prod_id')];
				$process_string="$grey_grey_product_id*$grey_used*$grey_rate*$material_update_id";
				echo "document.getElementById('process_string').value 	= '".$process_string."';\n";
			}

			$total_rate=$row[csf("order_rate")]-$dyeing_charge;	
			
			$total_amount=($row[csf("rate")])*$row[csf("receive_qnty")];
			$knitting_charge_string="$dyeing_charge*$total_rate";
			$save_rate_string="$grey_rate*$dyeing_charge";
			echo "document.getElementById('knitting_charge_string').value 	= '".$knitting_charge_string."';\n";
			echo "document.getElementById('save_rate_string').value 	= '".$save_rate_string."';\n";
			echo "document.getElementById('txt_grey_used').value 	    = '".number_format($grey_used,2,".","")."';\n";
			echo "document.getElementById('hidden_dying_charge').value 	= '".number_format($dyeing_charge,2,".","")."';\n";
			echo "$('#txt_grey_used').attr('onclick', 'proces_costing_popup()');\n";
		}
		if(trim($row[csf('grey_used_qty')]))
		{
			echo "document.getElementById('txt_grey_used').value 	    = '".number_format($row[csf('grey_used_qty')],2,".","")."';\n";
		}
		//-----------------------------------------------------------------------------------------------------


		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_finish_production',1,1);\n";
		exit();
	}
}

if($action=="roll_maintained")
{
	$roll_maintained=0; $process_cost_maintain=0;
	$production_variable = sql_select("select auto_update, item_category_id, production_entry, process_costing_maintain, variable_list from variable_settings_production where company_name='$data' and variable_list in (15, 34, 66) and status_active=1");
	foreach($production_variable as $row)
	{
		if($row[csf("variable_list")] ==34)
		{
			$process_cost_maintain = $row[csf("process_costing_maintain")];
		}
		elseif($row[csf("variable_list")] ==66)
		{
			$variable_textile_sales_maintain = $row[csf("production_entry")];
		}
		elseif($row[csf("variable_list")] ==15 && $row[csf("item_category_id")] ==2)
		{
			$fabric_store_auto_update = $row[csf("auto_update")];
		}
	}

	if($variable_textile_sales_maintain ==2)
	{
		$variable_textile_sales_maintain = 2;
	}else{
		$variable_textile_sales_maintain = 0;
	}
	
	if($roll_maintained==1) $roll_maintained=$roll_maintained; else $roll_maintained=0;
	if($fabric_store_auto_update==1) $fabric_store_auto_update=$fabric_store_auto_update; else $fabric_store_auto_update=0;
	// if($fabric_store_auto_update==2) $fabric_store_auto_update=0; else $fabric_store_auto_update=1;
	if($process_cost_maintain==1) $process_cost_maintain=$process_cost_maintain; else $process_cost_maintain=0;

	echo "document.getElementById('roll_maintained').value 				= '".$roll_maintained."';\n";
	echo "document.getElementById('fabric_store_auto_update').value 	= '".$fabric_store_auto_update."';\n";
	echo "document.getElementById('process_costing_maintain').value 	= '".$process_cost_maintain."';\n";
	echo "document.getElementById('textile_sales_maintain').value 		= '".$variable_textile_sales_maintain."';\n";

	echo "reset_form('finishFabricEntry_1','list_fabric_desc_container','','','set_production_basis();','cbo_production_basis*cbo_company_id*txt_production_date*txt_challan_no*roll_maintained*fabric_store_auto_update*process_costing_maintain*textile_sales_maintain');\n";
	//echo "$('#cbo_batch_status').val(1)";
	exit();
}

if($action=="load_color")
{
	$sql="select c.color_name from wo_booking_mst a, wo_booking_dtls b, lib_color c where a.booking_no=b.booking_no and b.fabric_color_id=c.id and b.po_break_down_id in($data) and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.fabric_color_id, c.color_name";
	//echo $sql;die;
	echo "var str_color = [". substr(return_library_autocomplete( $sql, "color_name" ), 0, -1). "];\n";
	echo "$('#txt_color').autocomplete({
		source: str_color
	});\n";
	exit();
}

if($action=="check_batch_no")
{
	$data=explode("**",$data);

	$batch_cond="";
	if(trim($data[2])!="")
	{
		$batch_cond=" and extention_no=".trim($data[2])."";
	}
	else
	{
		if($db_type==2) $batch_cond=" and extention_no is null";
		else			$batch_cond=" and extention_no==''";
	}

	//$sql="select id, batch_no from pro_batch_create_mst where batch_no='".trim($data[0])."' and company_id='".$data[1]."' and is_deleted=0 and status_active=1 and entry_form=0 $batch_cond order by id desc";

	$sql="select a.id, a.batch_no from pro_batch_create_mst a, pro_fab_subprocess b where a.id=b.batch_id and b.entry_form =35 and b.load_unload_id=2 and a.batch_no='".trim($data[0])."' and a.company_id='".$data[1]."' and a.is_deleted=0 and a.status_active=1 and b.status_active =1 and b.is_deleted=0 and a.entry_form=0 $batch_cond order by a.id desc";

	$data_array=sql_select($sql,1);
	if(count($data_array)>0)
	{
		echo $data_array[0][csf('id')];
	}
	else
	{
		echo "0";
	}
	exit();
}
if($action=="check_batch_no_scan")
{
	$data=explode("**",$data);
	$batch_id=(int) $data[0];
	$company=(int) $data[1];
	$sql="select batch_no from pro_batch_create_mst where id='".$batch_id."'";
	$data_array=sql_select($sql,1);
	if(count($data_array)>0)
	{
		echo $data_array[0][csf('batch_no')];
	}
	else
	{
		echo "";
	}
	exit();
}
if($action=="load_drop_down_body_part")
{
	$data = explode("**",$data);
	$prod_id=$data[0];
	$batch_id=$data[1];

	$fabric_desc=$data[2];
	$fabric_desc_id=$data[3];
	$txt_gsm=$data[4];
	$txt_dia_width=$data[5];
	$txt_original_gsm=$data[6];
	$txt_original_dia_width=$data[7];
	$cbo_dia_width_type=$data[8];
	$txt_grey_used=$data[9];
	$cbouom=$data[10];
	if($txt_dia_width!=""){$dia_widthCond="and c.dia_width=$txt_dia_width";}

	$booking_without_order=''; $booking_id=''; $po_id='';
	//$sql="select a.booking_without_order, a.booking_no_id, b.po_id, b.body_part_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=$batch_id and b.prod_id=$prod_id and b.status_active=1 and b.is_deleted=0";


	$sql="select a.booking_without_order, a.booking_no_id, b.po_id, b.body_part_id from pro_batch_create_mst a, pro_batch_create_dtls b,product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.id=$batch_id and b.prod_id in($prod_id)  
	 and unit_of_measure=$cbouom $dia_widthCond and c.gsm=$txt_gsm and c.detarmination_id=$fabric_desc_id and c.product_name_details='$fabric_desc' and b.status_active=1 and b.is_deleted=0";


	$result=sql_select($sql);
	foreach($result as $row)
	{
		$booking_without_order=$row[csf('booking_without_order')];
		$booking_id=$row[csf('booking_no_id')];
		if($row[csf('po_id')]>0) $po_id.=$row[csf('po_id')].",";
		$batch_body_part_id.=$row[csf('body_part_id')].",";
	}

	if($booking_without_order==1)
	{
		if($db_type==0)
		{
			$show_body_part_id=return_field_value("group_concat(a.body_part_id) as body_part_id","pro_grey_prod_entry_dtls a, inv_receive_master b","a.mst_id=b.id and a.prod_id=$prod_id and b.booking_id=$booking_id and b.booking_without_order=1 and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0",'body_part_id');
		}
		else
		{
			$show_body_part_id=return_field_value("LISTAGG(a.body_part_id, ',') WITHIN GROUP (ORDER BY a.body_part_id) as body_part_id","pro_grey_prod_entry_dtls a, inv_receive_master b","a.mst_id=b.id and a.prod_id=$prod_id and b.booking_id=$booking_id and b.booking_without_order=1 and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0",'body_part_id');
		}
	}
	else
	{
		$po_id=implode(",",array_unique(explode(",",substr($po_id,0,-1))));
		if($db_type==0)
		{
			$show_body_part_id=return_field_value("group_concat(a.body_part_id) as body_part_id","pro_grey_prod_entry_dtls a, order_wise_pro_details b","a.id=b.dtls_id  and b.entry_form in(2,13,22) and b.trans_type in(1,5) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.prod_id=$prod_id and b.po_breakdown_id in ($po_id)",'body_part_id'); //
		}
		else
		{
			$show_body_part_id=return_field_value("LISTAGG(a.body_part_id, ',') WITHIN GROUP (ORDER BY a.body_part_id) as body_part_id","pro_grey_prod_entry_dtls a, order_wise_pro_details b","a.id=b.dtls_id and b.entry_form in(2,13,22) and b.trans_type in(1,5) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.prod_id=$prod_id and b.po_breakdown_id in ($po_id)",'body_part_id'); //
		}
	}
	

	$all_body_part =array_filter(array_unique(explode(",",$batch_body_part_id.$show_body_part_id)));

	$show_body_part_id=implode(",",$all_body_part);

	//$show_body_part_id=implode(",",array_unique(explode(",",$show_body_part_id)));
	echo create_drop_down( "cbo_body_part", 182, $body_part,"", 1, "-- Select Body Part --", 0, "",0, $show_body_part_id );
	exit();
}

if($action=="load_drop_down_all_body_part")
{
	echo create_drop_down( "cbo_body_part", 182, $body_part,"", 1, "-- Select Body Part --", 0, "",0 );
	exit();
}

if($action=="process_name_popup")
{

	echo load_html_head_contents("Process Name Info","../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>

		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});

		var selected_id = new Array(); var selected_name = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;

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
			var old=document.getElementById('txt_process_row_id').value;
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

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hidden_process_id').val(id);
			$('#hidden_process_name').val(name);
		}
	</script>

</head>

<body>
	<div align="center">
		<fieldset style="width:370px;margin-left:10px">
			<input type="hidden" name="hidden_process_id" id="hidden_process_id" class="text_boxes" value="">
			<input type="hidden" name="hidden_process_name" id="hidden_process_name" class="text_boxes" value="">
			<form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
					<thead>
						<th width="50">SL</th>
						<th>Process Name</th>
					</thead>
				</table>
				<div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
						<?
						$i=1; $process_row_id=''; $process_id_print_array=array(25,26,32,33,34,35,36,37,38,39,40,60,61,62,64,65,67,68,69,70,71,72,73,74,75,77,78,79,80,81,82,83,84,85,86,87,88,89,92,93,94,100,125,127,128,129,132,133,134,135,136,137,138,156,166,66,91,143,150,156,157,94,167,159,160,161,163,168,169,164,165,31,63,65,90,130,131,139,140,141,142,144,145,146,147,148,151,162,170,172,173,171,174,175,180,184,185,186,187,188,189,190,191,192,193,231,421,420);
						$hidden_process_id=explode(",",$txt_process_id);
						foreach($conversion_cost_head_array as $id=>$name)
						{
							if(in_array($id,$process_id_print_array))
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								if(in_array($id,$hidden_process_id))
								{
									if($process_row_id=="") $process_row_id=$i; else $process_row_id.=",".$i;
								}
					/*$mandatory=0;
					if(in_array($id,$mandatory_subprocess_array))
					{
						$mandatory=1;
					}*/
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
						<td width="50" align="center"><?php echo "$i"; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>
						<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name; ?>"/>
						<input type="hidden" name="txt_mandatory" id="txt_mandatory<?php echo $i ?>" value="<? echo $mandatory; ?>"/>
					</td>
					<td><p><? echo $name; ?></p></td>
				</tr>
				<?
				$i++;
			}
		}
		?>
		<input type="hidden" name="txt_process_row_id" id="txt_process_row_id" value="<?php echo $process_row_id; ?>"/>
	</table>
</div>
<table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
</form>
</fieldset>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_all();
</script>
</html>
<?
exit();
}

if($action=="finish_fab_production_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplierArr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
	$locationArr=return_library_array( "select id, location_name from  lib_location", "id", "location_name");
	$storeArr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$productNameArr=return_library_array( "select id, product_name_details from  product_details_master", "id", "product_name_details");
	$machineArr=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	$sql_dtls="SELECT id, prod_id, batch_id, color_id, body_part_id, fabric_description_id, gsm, width, receive_qnty, reject_qty, no_of_roll, machine_no_id, rack_no, shelf_no, order_id, buyer_id,grey_used_qty,remarks,is_sales,process_id from pro_finish_fabric_rcv_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0";
	$sql_dtls_result =sql_select($sql_dtls);
	$sales_order_arr = $po_id_arr = array();
	foreach($sql_dtls_result as $row)
	{
		if($row[csf('is_sales')] == 1){
			$sales_order_arr[] = $row[csf('order_id')];
		}else{
			$po_id_arr[] = $row[csf('order_id')];
		}
		$batch_id_arr[]=$row[csf('batch_id')];
		$poId_arr[$row[csf('order_id')]]=$row[csf('order_id')];
		$dtlsId_arr[$row[csf('id')]]=$row[csf('id')];
	}
	if(!empty($batch_id_arr))
	{
		$batch_sql="select id, batch_no, booking_no from pro_batch_create_mst where id in(".implode(",",array_unique($batch_id_arr)).")";
		$batch_sql_result =sql_select($batch_sql);
		$batchArr = array();
		foreach($batch_sql_result as $row)
		{
			$batchArr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$batchArr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
		}

		//$batchArr=return_library_array( "select id, batch_no from  pro_batch_create_mst where id in(".implode(",",array_unique($batch_id_arr)).")", "id", "batch_no");
		//$bookingArr=return_library_array( "select id, booking_no from  pro_batch_create_mst where id in(".implode(",",array_unique($batch_id_arr)).")", "id", "booking_no");
	}
	$po_arr=array();
	if(!empty($po_id_arr)){
		$po_data=sql_select("select id, po_number,file_no,grouping as ref, job_no_mst from wo_po_break_down where id in(".implode(",",array_unique($po_id_arr)).")");
		foreach($po_data as $row)
		{
			$po_arr[$row[csf('id')]]['no']=$row[csf('po_number')];
			$po_arr[$row[csf('id')]]['file']=$row[csf('file_no')];
			$po_arr[$row[csf('id')]]['ref']=$row[csf('ref')];
			$po_arr[$row[csf('id')]]['job']=$row[csf('job_no_mst')];
		}
	}

	$sales_arr=array();
	if(!empty($sales_order_arr)){
		$po_data=sql_select("select id, job_no,po_buyer,po_job_no,sales_booking_no from fabric_sales_order_mst where id in(".implode(",",array_unique($sales_order_arr)).")");
		foreach($po_data as $row)
		{
			$sales_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$sales_arr[$row[csf('id')]]['po_buyer']=$row[csf('po_buyer')];
			$sales_arr[$row[csf('id')]]['po_job_no']=$row[csf('po_job_no')];
			$sales_arr[$row[csf('id')]]['sales_booking_no']=$row[csf('sales_booking_no')];
		}
	}

	$process_loss_arr=array();
	if(!empty($dtlsId_arr)){
		$po_data=sql_select("SELECT dtls_id, po_breakdown_id, process_loss_perc from order_wise_pro_details where entry_form=7 and dtls_id in(".implode(",",array_unique($dtlsId_arr)).") and po_breakdown_id in(".implode(",",array_unique($poId_arr)).")");
		foreach($po_data as $row)
		{
			$process_loss_arr[$row[csf('dtls_id')]][$row[csf('po_breakdown_id')]]=$row[csf('process_loss_perc')];
		}
	}
	// echo "<pre>"; print_r($process_loss_arr);

	$sql_mst="select id, recv_number, company_id, receive_basis, store_id, location_id, knitting_source, knitting_company, receive_date, challan_no, store_id from inv_receive_master where id='$data[1]'";
	$dataArray=sql_select($sql_mst);
	?>
	<div style="width:1180px; font-size:6px">
		<table width="1180" cellspacing="0" align="right" border="0">
			<tr>
				<td colspan="6" align="center" style="font-size:x-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			</tr>
			<tr>
				<td colspan="6" align="center">
					<?
					$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
					foreach ($nameArray as $result)
					{
						?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')]; ?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')]; ?>
						Zip Code: <? echo $result[csf('zip_code')];
					}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:20px"><u><strong><? echo $data[3]; ?></strong></u></td>
			</tr>
			<tr>
				<td width="130"><strong>System ID:</strong></td><td width="175"><? echo $dataArray[0][csf('recv_number')]; ?></td>
				<td width="130"><strong>Prod. Basis:</strong></td><td width="175px"> <? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
				<td width="130"><strong>Prod. Date:</strong></td><td width="175"><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Dyeing Source:</strong></td><td><? echo $knitting_source[$dataArray[0][csf('knitting_source')]]; ?></td>
				<td><strong>Dyeing Company:</strong></td><td><? if($dataArray[0][csf('knitting_source')]==1) echo $company_library[$dataArray[0][csf('knitting_company')]]; else if($dataArray[0][csf('knitting_source')]==3) echo $supplierArr[$dataArray[0][csf('knitting_company')]]; ?></td>
				<td><strong>Challan No:</strong></td><td><? echo $dataArray[0][csf('challan_no')]; ?></td>
			</tr>
			<tr>
				<td><strong>Location:</strong></td><td><? echo $locationArr[$dataArray[0][csf('location_id')]]; ?></td>
				<td><strong>Store Name:</strong></td><td><? echo $storeArr[$dataArray[0][csf('store_id')]]; ?></td>
				<td><strong>&nbsp;</strong></td><td>&nbsp;</td>
			</tr>
			<tr style=" height:20px">
				<td  colspan="3" id="barcode_img_id"></td>
			</tr>
			<tr style=" height:20px">
				<td colspan="6">&nbsp;</td>
			</tr>
		</table>
		<br>
		<div style="width:100%; margin-left:175px">
			<table align="right" cellspacing="0" width="1360" border="1" rules="all" class="rpt_table" >
				<?
				$i=1;
				foreach($sql_dtls_result as $row)
				{
					if($i == 1)
					{
						?>
						<thead bgcolor="#dddddd" align="center">
							<th width="30">SL</th>
							<th width="70">Buyer</th>
							<th width="80">Job No</th>
							<th width="80">Booking No</th>

							<? if($row[csf('is_sales')] == 1){ ?>
								<th width="100">FSO No</th>
								<th width="100">Booking No</th>
							<? }else{
								?>
								<th width="100">Order No</th>
								<?
							} ?>
							<th width="60">File No</th>
							<th width="60">Ref No</th>
							<th width="70">Batch</th>
							<th width="70">Color</th>
							<th width="70">Body Part</th>
							<th width="120">Fabric Description</th>
							<th width="100">Process Name</th>
							<th width="50">GSM</th>
							<th width="50">Dia/ Width</th>
							<th width="70">QC Pass Qty</th>
							<th width="70">Grey Used Qty</th>
							<th width="60">Reject Qty</th>
							<th width="60">Pro.Loss%</th>
							<th width="50">No Of Roll</th>
							<th width="50">Rack</th>
							<th width="50">Shelf</th>

							<th width="60">Machine</th>
							<th width="120">Remarks</th>
						</thead>
						<?
					}
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$buyer_name='';
					$buyer=array_unique(explode(",",$row[csf('buyer_id')]));
					foreach($buyer as $val )
					{
						if($buyer_name=='') $buyer_name=$buyer_arr[$val]; else $buyer_name.=",".$buyer_arr[$val];
					}

					$job_no=''; $order_no='';$file_no='';$ref_no='';

					if($row[csf('is_sales')] == 1)
					{
						$job_no=$sales_arr[$row[csf('order_id')]]['po_job_no'];
						$order_no=$sales_arr[$row[csf('order_id')]]['job_no'];
						$sales_booking_no=$sales_arr[$row[csf('order_id')]]['sales_booking_no'];

						$file_no="";
						$ref_no="";
					}
					else
					{
						$order_id=array_unique(explode(",",$row[csf('order_id')]));
						foreach($order_id as $po_id )
						{
							if($job_no=='') $job_no=$po_arr[$po_id]['job']; else $job_no.=",".$po_arr[$po_id]['job'];
							if($order_no=='') $order_no=$po_arr[$po_id]['no']; else $order_no.=",".$po_arr[$po_id]['no'];
							if($file_no=='') $file_no=$po_arr[$po_id]['file']; else $file_no.=",".$po_arr[$po_id]['file'];
							if($ref_no=='') $ref_no=$po_arr[$po_id]['ref']; else $ref_no.=",".$po_arr[$po_id]['ref'];
						}
					}
					$ref_no=implode(",",array_unique(explode(",",$ref_no)));
					$job_no=implode(",",array_unique(explode(",",$job_no)));

					$process_id=array_unique(explode(",",$row[csf('process_id')]));
					// print_r($process_id);
					$process_name="";
					foreach($process_id as $pro_id )
					{
						if($process_name=='') $process_name=$conversion_cost_head_array[$pro_id]; else $process_name.=",".$conversion_cost_head_array[$pro_id];
					}
					$process_name=implode(",",array_unique(explode(",",$process_name)));
					// echo $process_name;
					$process_loss=$process_loss_arr[$row[csf('id')]][$row[csf('order_id')]];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td><? echo $i; ?></td>
						<td><p><? echo $buyer_name; ?></p></td>
						<td><p><? echo $job_no; ?></p></td>
						<td><p><? echo $batchArr[$row[csf('batch_id')]]['booking_no']; ?></p></td>
						<td><p style="word-wrap: break-word; word-break: break-all;"><? echo $order_no; ?></p></td>
						<? if($row[csf('is_sales')] == 1){ ?>
							<td><p><? echo $sales_booking_no; ?></p></td>
						<? } ?>
						<td><p><? echo $file_no; ?></p></td>
						<td><p style="word-wrap: break-word; word-break: break-all;"><? echo $ref_no; ?></p></td>
						<td><p><? echo $batchArr[$row[csf('batch_id')]]['batch_no']; ?></p></td>
						<td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
						<td><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
						<td><p><? echo $productNameArr[$row[csf('prod_id')]]; ?></p></td>
						<td><p><? echo $process_name; ?></p></td>
						<td><p><? echo $row[csf('gsm')]; ?></p></td>
						<td><p><? echo $row[csf('width')]; ?></p></td>
						<td align="right"><? echo number_format($row[csf('receive_qnty')],2); ?></td>
						<td align="right"><? echo number_format($row[csf('grey_used_qty')],2); ?></td>
						<td align="right"><? echo number_format($row[csf('reject_qty')],2); ?></td>
						<td align="right" title="<? echo $row[csf('id')].'='.$row[csf('order_id')]; ?>"><? echo $process_loss; ?></td>
						<td align="right"><? echo number_format($row[csf('no_of_roll')],0); ?></td>
						<td><p><? echo $row[csf('rack_no')]; ?></p></td>
						<td><p><? echo $row[csf('shelf_no')]; ?></p></td>
						<td><? echo $machineArr[$row[csf('machine_no_id')]]; ?></td>
						<td><p><? echo $row[csf('remarks')]; ?></p></td>
					</tr>
					<?
					$tot_receive+=$row[csf('receive_qnty')];
					$tot_used_qty+=$row[csf('grey_used_qty')];
					$tot_reject+=$row[csf('reject_qty')];
					$tot_roll+=$row[csf('no_of_roll')];
					$i++;
					$tble_td=$row[csf('is_sales')];
				}
				?>
				<tr class="tbl_bottom">
					<? if($tble_td == 1){ ?>
						<th width="100"></th>
						<th width="100"></th>
					<? }else{
						?>
						<th width="100"></th>
						<?
					} ?>
					<td align="right" colspan="13"><strong>Total</strong></td>
					<td align="right"><? echo number_format($tot_receive,2); ?></td>
					<td align="right"><? echo number_format($tot_used_qty,2); ?></td>
					<td align="right"><? echo number_format($tot_reject,2); ?></td>
					<td align="right"></td>
					<td align="right"><? echo number_format($tot_roll); ?></td>
					<td colspan="">&nbsp;</td>
				</tr>
			</table>
			<br>
			<?
			echo signature_table(67, $data[0], "1250px");
			?>
		</div>
	</div>
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
			 //alert(value)
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
			//$("#barcode_img_id").html('11');
			value = {code:value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $data[2]; ?>');
	</script>
	<?
}

if($action=="finish_fab_production_print2") // Print 2 for Amy Syntex
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplierArr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
	$locationArr=return_library_array( "select id, location_name from  lib_location", "id", "location_name");
	$storeArr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$productNameArr=return_library_array( "select id, product_name_details from  product_details_master", "id", "product_name_details");
	$machineArr=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	$sql_dtls="SELECT id, prod_id, batch_id, color_id, body_part_id, fabric_description_id, gsm, width, receive_qnty, reject_qty, no_of_roll, machine_no_id, rack_no, shelf_no, order_id, buyer_id,grey_used_qty,remarks,is_sales,process_id from pro_finish_fabric_rcv_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0";
	$sql_dtls_result =sql_select($sql_dtls);
	$sales_order_arr = $po_id_arr = array();
	foreach($sql_dtls_result as $row)
	{
		if($row[csf('is_sales')] == 1){
			$sales_order_arr[] = $row[csf('order_id')];
		}else{
			$po_id_arr[] = $row[csf('order_id')];
		}
		$batch_id_arr[]=$row[csf('batch_id')];
		$poId_arr[$row[csf('order_id')]]=$row[csf('order_id')];
		$dtlsId_arr[$row[csf('id')]]=$row[csf('id')];
	}
	if(!empty($batch_id_arr))
	{
		$batch_sql="select id, batch_no, booking_no from pro_batch_create_mst where id in(".implode(",",array_unique($batch_id_arr)).")";
		$batch_sql_result =sql_select($batch_sql);
		$batchArr = array();
		foreach($batch_sql_result as $row)
		{
			$batchArr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$batchArr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
		}

		//$batchArr=return_library_array( "select id, batch_no from  pro_batch_create_mst where id in(".implode(",",array_unique($batch_id_arr)).")", "id", "batch_no");
		//$bookingArr=return_library_array( "select id, booking_no from  pro_batch_create_mst where id in(".implode(",",array_unique($batch_id_arr)).")", "id", "booking_no");
	}
	$po_arr=array();
	if(!empty($po_id_arr)){
		$po_data=sql_select("select id, po_number,file_no,grouping as ref, job_no_mst from wo_po_break_down where id in(".implode(",",array_unique($po_id_arr)).")");
		foreach($po_data as $row)
		{
			$po_arr[$row[csf('id')]]['no']=$row[csf('po_number')];
			$po_arr[$row[csf('id')]]['file']=$row[csf('file_no')];
			$po_arr[$row[csf('id')]]['ref']=$row[csf('ref')];
			$po_arr[$row[csf('id')]]['job']=$row[csf('job_no_mst')];
		}
	}

	$sales_arr=array();
	if(!empty($sales_order_arr)){
		$po_data=sql_select("select id, job_no,po_buyer,po_job_no,sales_booking_no from fabric_sales_order_mst where id in(".implode(",",array_unique($sales_order_arr)).")");
		foreach($po_data as $row)
		{
			$sales_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$sales_arr[$row[csf('id')]]['po_buyer']=$row[csf('po_buyer')];
			$sales_arr[$row[csf('id')]]['po_job_no']=$row[csf('po_job_no')];
			$sales_arr[$row[csf('id')]]['sales_booking_no']=$row[csf('sales_booking_no')];
		}
	}

	$process_loss_arr=array();
	if(!empty($dtlsId_arr)){
		$po_data=sql_select("SELECT dtls_id, po_breakdown_id, process_loss_perc from order_wise_pro_details where entry_form=7 and dtls_id in(".implode(",",array_unique($dtlsId_arr)).") and po_breakdown_id in(".implode(",",array_unique($poId_arr)).")");
		foreach($po_data as $row)
		{
			$process_loss_arr[$row[csf('dtls_id')]][$row[csf('po_breakdown_id')]]=$row[csf('process_loss_perc')];
		}
	}
	// echo "<pre>"; print_r($process_loss_arr);

	$sql_mst="select id, recv_number, company_id, receive_basis, store_id, location_id, knitting_source, knitting_company, receive_date, challan_no, store_id from inv_receive_master where id='$data[1]'";
	$dataArray=sql_select($sql_mst);
	?>
	<div style="width:1180px; font-size:6px">
		<table width="1180" cellspacing="0" align="right" border="0">
			<tr>
				<td colspan="6" align="center" style="font-size:x-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			</tr>
			<tr>
				<td colspan="6" align="center">
					<?
					$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
					foreach ($nameArray as $result)
					{
						?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')]; ?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')]; ?>
						Zip Code: <? echo $result[csf('zip_code')];
					}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:20px"><u><strong><? echo $data[3]; ?></strong></u></td>
			</tr>
			<tr>
				<td width="130"><strong>System ID:</strong></td><td width="175"><? echo $dataArray[0][csf('recv_number')]; ?></td>
				<td width="130"><strong>Prod. Basis:</strong></td><td width="175px"> <? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
				<td width="130"><strong>Prod. Date:</strong></td><td width="175"><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Dyeing Source:</strong></td><td><? echo $knitting_source[$dataArray[0][csf('knitting_source')]]; ?></td>
				<td><strong>Dyeing Company:</strong></td><td><? if($dataArray[0][csf('knitting_source')]==1) echo $company_library[$dataArray[0][csf('knitting_company')]]; else if($dataArray[0][csf('knitting_source')]==3) echo $supplierArr[$dataArray[0][csf('knitting_company')]]; ?></td>
				<td><strong>Challan No:</strong></td><td><? echo $dataArray[0][csf('challan_no')]; ?></td>
			</tr>
			<tr>
				<td><strong>Location:</strong></td><td><? echo $locationArr[$dataArray[0][csf('location_id')]]; ?></td>
				<td><strong>Store Name:</strong></td><td><? echo $storeArr[$dataArray[0][csf('store_id')]]; ?></td>
				<td><strong>&nbsp;</strong></td><td>&nbsp;</td>
			</tr>
			<tr style=" height:20px">
				<td  colspan="3" id="barcode_img_id"></td>
			</tr>
			<tr style=" height:20px">
				<td colspan="6">&nbsp;</td>
			</tr>
		</table>
		<br>
		<div style="width:100%; margin-left:175px">
			<table align="right" cellspacing="0" width="1360" border="1" rules="all" class="rpt_table" >
				<?
				$i=1;
				foreach($sql_dtls_result as $row)
				{
					if($i == 1)
					{
						?>
						<thead bgcolor="#dddddd" align="center">
							<th width="30">SL</th>
							<th width="70">Buyer</th>
							<th width="80">Job No</th>
							<th width="80">Booking No</th>

							<? if($row[csf('is_sales')] == 1){ ?>
								<th width="100">FSO No</th>
								<th width="100">Booking No</th>
							<? }else{
								?>
								<th width="100">Order No</th>
								<?
							} ?>
							<th width="60">Ref No</th>
							<th width="70">Batch</th>
							<th width="70">Color</th>
							<th width="70">Body Part</th>
							<th width="120">Fabric Description</th>
							<th width="100">Process Name</th>
							<th width="50">GSM</th>
							<th width="50">Dia/ Width</th>
							<th width="70">QC Pass Qty</th>
							<th width="70">Grey Used Qty</th>
							<th width="60">Reject Qty</th>
							<th width="60">Pro.Loss%</th>
							<th width="50">No Of Roll</th>

							<th width="60">Machine</th>
							<th width="120">Remarks</th>
						</thead>
						<?
					}
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$buyer_name='';
					$buyer=array_unique(explode(",",$row[csf('buyer_id')]));
					foreach($buyer as $val )
					{
						if($buyer_name=='') $buyer_name=$buyer_arr[$val]; else $buyer_name.=",".$buyer_arr[$val];
					}

					$job_no=''; $order_no='';$file_no='';$ref_no='';

					if($row[csf('is_sales')] == 1)
					{
						$job_no=$sales_arr[$row[csf('order_id')]]['po_job_no'];
						$order_no=$sales_arr[$row[csf('order_id')]]['job_no'];
						$sales_booking_no=$sales_arr[$row[csf('order_id')]]['sales_booking_no'];

						$file_no="";
						$ref_no="";
					}
					else
					{
						$order_id=array_unique(explode(",",$row[csf('order_id')]));
						foreach($order_id as $po_id )
						{
							if($job_no=='') $job_no=$po_arr[$po_id]['job']; else $job_no.=",".$po_arr[$po_id]['job'];
							if($order_no=='') $order_no=$po_arr[$po_id]['no']; else $order_no.=",".$po_arr[$po_id]['no'];
							if($file_no=='') $file_no=$po_arr[$po_id]['file']; else $file_no.=",".$po_arr[$po_id]['file'];
							if($ref_no=='') $ref_no=$po_arr[$po_id]['ref']; else $ref_no.=",".$po_arr[$po_id]['ref'];
						}
					}
					$ref_no=implode(",",array_unique(explode(",",$ref_no)));
					$job_no=implode(",",array_unique(explode(",",$job_no)));

					$process_id=array_unique(explode(",",$row[csf('process_id')]));
					// print_r($process_id);
					$process_name="";
					foreach($process_id as $pro_id )
					{
						if($process_name=='') $process_name=$conversion_cost_head_array[$pro_id]; else $process_name.=",".$conversion_cost_head_array[$pro_id];
					}
					$process_name=implode(",",array_unique(explode(",",$process_name)));
					// echo $process_name;
					$process_loss=$process_loss_arr[$row[csf('id')]][$row[csf('order_id')]];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td><? echo $i; ?></td>
						<td><p><? echo $buyer_name; ?></p></td>
						<td><p><? echo $job_no; ?></p></td>
						<td><p><? echo $batchArr[$row[csf('batch_id')]]['booking_no']; ?></p></td>
						<td><p style="word-wrap: break-word; word-break: break-all;"><? echo $order_no; ?></p></td>
						<? if($row[csf('is_sales')] == 1){ ?>
							<td><p><? echo $sales_booking_no; ?></p></td>
						<? } ?>
						<td><p style="word-wrap: break-word; word-break: break-all;"><? echo $ref_no; ?></p></td>
						<td><p><? echo $batchArr[$row[csf('batch_id')]]['batch_no']; ?></p></td>
						<td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
						<td><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
						<td><p><? echo $productNameArr[$row[csf('prod_id')]]; ?></p></td>
						<td><p><? echo $process_name; ?></p></td>
						<td><p><? echo $row[csf('gsm')]; ?></p></td>
						<td><p><? echo $row[csf('width')]; ?></p></td>
						<td align="right"><? echo number_format($row[csf('receive_qnty')],2); ?></td>
						<td align="right"><? echo number_format($row[csf('grey_used_qty')],2); ?></td>
						<td align="right"><? echo number_format($row[csf('reject_qty')],2); ?></td>
						<td align="right" title="<? echo $row[csf('id')].'='.$row[csf('order_id')]; ?>"><? echo number_format($process_loss,2); ?></td>
						<td align="right"><? echo number_format($row[csf('no_of_roll')],0); ?></td>
						<td><? echo $machineArr[$row[csf('machine_no_id')]]; ?></td>
						<td><p><? echo $row[csf('remarks')]; ?></p></td>
					</tr>
					<?
					$tot_receive+=$row[csf('receive_qnty')];
					$tot_used_qty+=$row[csf('grey_used_qty')];
					$tot_reject+=$row[csf('reject_qty')];
					$tot_roll+=$row[csf('no_of_roll')];
					$i++;
					$tble_td=$row[csf('is_sales')];
				}
				?>
				<tr class="tbl_bottom">
					<? if($tble_td == 1){ ?>
						<th width="100"></th>
						<th width="100"></th>
					<? }else{
						?>
						<th width="100"></th>
						<?
					} ?>
					<td align="right" colspan="12"><strong>Total</strong></td>
					<td align="right"><? echo number_format($tot_receive,2); ?></td>
					<td align="right"><? echo number_format($tot_used_qty,2); ?></td>
					<td align="right"><? echo number_format($tot_reject,2); ?></td>
					<td align="right"></td>
					<td align="right"><? echo number_format($tot_roll); ?></td>
					<td colspan="">&nbsp;</td>
				</tr>
			</table>
			<br>
			<?
			echo signature_table(67, $data[0], "1250px");
			?>
		</div>
	</div>
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
			 //alert(value)
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
			//$("#barcode_img_id").html('11');
			value = {code:value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $data[2]; ?>');
	</script>
	<?
}

if ($action == "knit_defect_popup")
{
	echo load_html_head_contents("Finish Fabric product Entry", "../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo "blala";
	//echo $update_dtls_id."**".$roll_maintained."**".$company_id."**"."didar";die;
	$variable_data = sql_select("select fabric_grade, get_upto_first, get_upvalue_first, get_upto_second,company_name, get_upvalue_second from variable_settings_production where  company_name=$company_id and variable_list=45 order by company_name,get_upvalue_first asc"); //company_name=$company_id and
	//echo "select fabric_grade, get_upto_first, get_upvalue_first, get_upto_second,company_name, get_upvalue_second from variable_settings_production where  company_name=$company_id and variable_list=36 order by company_name,get_upvalue_first asc";
	$exc_perc = array();
	$i = 0;
	$variable_data_count = count($variable_data);
	foreach ($variable_data as $row) {
		if ($exp[$row[csf("company_name")]] == '') $i = 0;
		$exc_perc[$row[csf("company_name")]]['limit'][$i] = $row[csf("get_upvalue_first")] . "__" . $row[csf("get_upvalue_second")];
		$exc_perc[$row[csf("company_name")]]['grade'][$i] = $row[csf("fabric_grade")];
		$i++;
		$exp[$row[csf("company_name")]] = 1;

	}
	//print_r($exc_perc);
	//$js_variable_data_arr=json_encode($exc_perc);


	echo load_html_head_contents("Finish Fabric Production Entry", "../../", 1, 1, $unicode, '', '');
	//$supplier_arr = return_library_array("select id, short_name from  lib_supplier", "id", "short_name");

	/*$machine_dia_arr=return_library_array("select id, dia_width from lib_machine_name","id","dia_width");
	$color_arr=return_library_array("select id, color_name from  lib_color","id","color_name");
	$yarn_count_arr=return_library_array("select id, yarn_count from  lib_yarn_count","id","yarn_count");
	$supplier_arr=return_library_array("select a.lot, b.short_name from product_details_master a, lib_supplier b where a.supplier_id=b.id and a.item_category_id=1","lot","short_name");*/

	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
		$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	}

	if ($roll_maintained == 1) {
		/*$data_array=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.body_part_id, b.febric_description_id, b.machine_no_id, b.gsm, b.width, b.color_id, b.yarn_lot, b.yarn_count, c.barcode_no, c.id as roll_id, c.roll_no, c.qnty
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
		WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2) and c.entry_form in(2) and c.status_active=1 and c.is_deleted=0 and b.id=$update_dtls_id");
		$roll_dtls_data_arr=array();
		foreach($data_array as $row)
		{
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["id"]=$row[csf("id")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["entry_form"]=$row[csf("entry_form")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["company_id"]=$row[csf("company_id")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["receive_basis"]=$row[csf("receive_basis")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["booking_no"]=$row[csf("booking_no")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["booking_id"]=$row[csf("booking_id")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["knitting_source"]=$row[csf("knitting_source")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["knitting_company"]=$row[csf("knitting_company")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["dtls_id"]=$row[csf("dtls_id")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["prod_id"]=$row[csf("prod_id")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["febric_description_id"]=$constructtion_arr[$row[csf("febric_description_id")]]." ".$composition_arr[$row[csf('febric_description_id')]];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["machine_no_id"]=$row[csf("machine_no_id")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["gsm"]=$row[csf("gsm")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["width"]=$row[csf("width")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["color_id"]=$row[csf("color_id")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["yarn_lot"]=$row[csf("yarn_lot")];

			$roll_dtls_data_arr[$row[csf("barcode_no")]]["yarn_count"]=$row[csf("yarn_count")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["barcode_no"]=$row[csf("barcode_no")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["roll_id"]=$row[csf("roll_id")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["roll_no"]=$row[csf("roll_no")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["qnty"]=$row[csf("qnty")];
		}*/
	} else {
		$data_array = sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company,
			b.id as dtls_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.machine_no_id, b.gsm, b.width, b.color_id, b.receive_qnty as qnty
			FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b WHERE a.id=b.mst_id and a.entry_form in(7) and b.id=$update_dtls_id");

		$roll_dtls_data_arr = array();
		foreach ($data_array as $row) {

			$constraction_comp = $constructtion_arr[$row[csf("fabric_description_id")]] . " " . $composition_arr[$row[csf('fabric_description_id')]];
			$machine_dia = return_field_value("dia_width", "lib_machine_name", "id=" . $row[csf("machine_no_id")], "dia_width");
			$gsm = $row[csf("gsm")];
			$width = $row[csf("width")];
			$color_id_arr = array_unique(explode(",", $row[csf("color_id")]));
			$all_color = "";
			foreach ($color_id_arr as $color_id) {
				$all_color .= return_field_value("color_name", "lib_color", "id='$color_id'", "color_name") . ",";
			}
			$all_color = chop($all_color, ",");
			$qnty = $row[csf("qnty")];

			/*$yarn_count_arr = array_unique(explode(",", $row[csf("yarn_count")]));
			$all_yarn_count = "";
			foreach ($yarn_count_arr as $count_id) {
				$all_yarn_count .= return_field_value("yarn_count", "lib_yarn_count", "id='$count_id'", "yarn_count") . ",";
			}
			$all_yarn_count = chop($all_yarn_count, ",");
			$yarn_lot = $row[csf("yarn_lot")];*/

			/*$lot_arr = array_unique(explode(",", $row[csf("yarn_lot")]));
			foreach ($lot_arr as $lot) {
				$supplier_id = return_field_value("max(supplier_id) as supplier_id", "product_details_master", "item_category_id=1 and lot='$lot'", "supplier_id");
				$all_supplier .= $supplier_arr[$supplier_id] . ",";
			}
			$all_supplier = chop($all_supplier, ",");*/
		}
		$disable = "disabled";
	}
	?>
	<script>
		var pemission = '<? echo $_SESSION['page_permission']; ?>';

		var exc_perc =<? echo json_encode($exc_perc); ?>;
        //alert(exc_perc);
        function fabric_grading(comp, point) {
            //alert(comp)
            var newp = exc_perc[comp]["limit"];
            newp = JSON.stringify(newp);
            var newstr = newp.split(",");
            for (var m = 0; m < newstr.length; m++) {
            	var limit = exc_perc[comp]["limit"][m].split("__");
            	if ((limit[1] * 1) == 0 && (point * 1) >= (limit[0] * 1)) {
            		return ( exc_perc[comp]["grade"][m]);
            	}
            	if ((point * 1) >= (limit[0] * 1) && (point * 1) <= (limit[1] * 1)) {
            		return exc_perc[comp]["grade"][m];
            	}
                // alert( newstr[m]+"=="+m)
            }
            return '';
        }

        var roll_maintain = '<? echo $roll_maintained; ?>';
        function fn_barcode() {
        	var dtls_id = $('#hide_dtls_id').val();
        	var roll_maintain = $('#hide_roll_maintain').val();
        	if (dtls_id == "" && roll_maintain != 1) {
        		alert("Select Data First.");
        		return;
        	}
        	else {
        		var title = 'Barcode Or Details Info';
        		var page_link = 'finish_fabric_receive_controller.php?update_dtls_id=' + dtls_id + '&roll_maintained=' + roll_maintain + '&action=barcode_defect_popup';
        		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=350px,center=1,resize=1,scrolling=0', '../');
        		emailwindow.onclose = function () {
        			var bar_code_ref = this.contentDoc.getElementById("hide_barcode_id").value.split("**");
        			if (bar_code_ref[1] != "") {
        				get_php_form_data(bar_code_ref[0], 'barcode_roll_find', 'finish_fabric_receive_controller');
        			}
        		}
        	}
        }


        if (roll_maintain == 1) {
        	$('#txt_barcode').live('keydown', function (e) {
        		if (e.keyCode === 13) {
        			e.preventDefault();
        			var bar_code = $('#txt_barcode').val();
        			get_php_form_data(bar_code, 'barcode_roll_find', 'finish_fabric_receive_controller');
        		}
        	});

        	$(document).ready(function (e) {
        		var roll_maintain = $('#hide_roll_maintain').val() * 1;
        		if (roll_maintain > 0) {
        			$('#txt_barcode').focus();
        		}
        		else {
        			$('#txt_qc_name').focus();
        		}

        	});
        }

        function caculate_roll_length() {
        	var roll_weight = $('#txt_roll_weight').val() * 1;
        	var roll_width = $('#txt_roll_width').val() * 1;
        	var gsm = $('#txt_gsm').val() * 1;
        	var roll_length = ((roll_weight * 1000) / (gsm * roll_width * 0.0254) * 1.09361);
        	$('#txt_roll_length').val(number_format(roll_length, 4, '.', ''));
        }

        function fn_panelty_point(i) {

        	var defect_count = $('#defectcount_' + i).val() * 1;
        	var found_inche = $('#foundInche_' + i).val() * 1;
        	var company_id = $('#company_id').val();
        	var found_inche_calc = "";
        	if (found_inche == 1) found_inche_calc = 1;
        	else if (found_inche == 2) found_inche_calc = 2;
        	else if (found_inche == 3) found_inche_calc = 3;
        	else if (found_inche == 4) found_inche_calc = 4;
        	else if (found_inche == 5) found_inche_calc = 2;
        	else if (found_inche == 6) found_inche_calc = 4;
        	var penalty_val = defect_count * found_inche_calc;
        	$('#penaltycount_' + i).val(penalty_val);
        	var ddd = {dec_type: 4, comma: 0, currency: ''}
        	var numRow = $('table#dtls_part tbody tr').length;
        	math_operation("total_penalty_point", "penaltycount_", "+", numRow, ddd);
        	var penalty_ratio = (($('#total_penalty_point').val() * 1) * 36 * 100) / (($('#txt_roll_length').val() * 1) * ($('#txt_roll_width').val() * 1));
        	$('#total_point').val(number_format(penalty_ratio, 4, '.', ''));
            //alert(penalty_ratio);
           /* if(penalty_ratio<21) fab_grade="A";
             else if(penalty_ratio<29 && penalty_ratio>20) fab_grade="B";
             else fab_grade="Reject";*/

             $('#fabric_grade').val(fabric_grading(company_id, penalty_ratio));
         }
         function generate_report_file(data,action,page)
         {
         	window.open("finish_fabric_receive_controller.php?data=" + data+'&action='+action, true );
         }

         function fnc_finish_defect_entry(operation) {
         	if (operation == 2) {
         		show_msg('13');
         		return;
         	}

         	if(operation == 4)
         	{
         		generate_report_file($('#update_id').val() ,
         			'KnittingProductionPrint', 'requires/finish_fabric_receive_controller');
         		return;


         	}

         	if (form_validation('txt_roll_length*txt_qc_date*fabric_grade', 'Roll Length*QC Date*Fabric Grade') == false) {
         		return;
         	}
         	var table_length = $('#dtls_part tbody tr').length;
         	var data_string = "";
         	var k = 1;
         	var count_tbl_length = 0;
         	for (var i = 1; i <= table_length; i++) {
         		var defect_name = $('#defectId_' + i).val();
         		var defect_count = $('#defectcount_' + i).val();
         		var found_in_inche = $('#foundInche_' + i).val();
         		var found_inche_val = "";
         		var penalty_point = $('#penaltycount_' + i).val() * 1;

         		if (penalty_point > 0) {
         			if (found_in_inche == 5) found_inche_val = 2;
         			else if (found_in_inche == 6) found_inche_val = 4;
         			else found_inche_val = found_in_inche;
         			data_string += '&defectId_' + k + '=' + defect_name + '&defectcount_' + k + '=' + defect_count + '&foundInche_' + k + '=' + found_in_inche + '&foundIncheVal_' + k + '=' + found_inche_val + '&penaltycount_' + k + '=' + penalty_point;
         			count_tbl_length++;
         			k++;

         		}
         	}
         	data_string = data_string + '&count_tbl_length=' + count_tbl_length;


         	var data = "action=save_update_delete_defect&operation=" + operation + get_submitted_data_string('hide_dtls_id*hide_roll_maintain*txt_barcode*txt_roll_no*roll_id*txt_qc_name*txt_roll_width*txt_roll_weight*txt_roll_length*txt_reject_qnty*txt_qc_date*total_penalty_point*total_point*fabric_grade*fabric_comments*update_id', "../../") + data_string;
            //alert(data);return;
            //alert(data);
            freeze_window(operation);

            http.open("POST", "finish_fabric_receive_controller.php", true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_finish_defect_entry_response;
        }

        function fnc_finish_defect_entry_response() {
        	if (http.readyState == 4) {
                //release_freezing();return;
                var reponse = trim(http.responseText).split('**');
                if (reponse[0] == 20) {
                	alert(reponse[1]);
                	release_freezing();
                	return;
                }
                show_msg(reponse[0]);
                if ((reponse[0] == 0 || reponse[0] == 1)) {
                	document.getElementById('update_id').value = reponse[1];
                	var prod_dtls_id = $('#hide_dtls_id').val();
                	$('#dtls_list_container').html("");
                	show_list_view(prod_dtls_id, 'show_qc_listview', 'dtls_list_container', 'finish_fabric_receive_controller', '');
                	set_button_status(0, pemission, 'fnc_finish_defect_entry', 1);
                	$('#master_part').find('input', 'select').val("");
                	release_freezing();
                }
                else {
                	release_freezing();
                }

            }
        }

        function fn_recet_details() {
        	$('#dtls_part').find('input').val("");
        	$('#dtls_part').find('select').val(0);
        }
    </script>
    <body onLoad="set_hotkey()">
    	<? echo load_freeze_divs("../../", $_SESSION['page_permission']); ?>
    	<form name="defectQcResult_1" id="defectQcResult_1" autocomplete="off">
    		<div style="width:1160px">
    			<input type="hidden" id="hide_dtls_id" value="<? echo $update_dtls_id; ?>"/>
    			<input type="hidden" id="hide_roll_maintain" value="<? echo $roll_maintained; ?>"/>
    			<input type="hidden" id="company_id" value="<? echo $company_id; ?>"/>
    			<table width="1100" border="0">
    				<tr>
    					<td width="400" valign="top">
    						<table cellpadding="0" cellspacing="0" border="1" width="400" class="rpt_table" rules="all" id="master_part">
	    						<tr bgcolor="#E9F3FF">
	    							<td width="200">Barcode Number</td>
	    							<td align="center"><input type="text" id="txt_barcode" name="txt_barcode"
	    								class="text_boxes" style="width:150px;"
	    								onDblClick="fn_barcode()"
	    								placeholder="Browse or Scan" <? echo $disable; ?> >
	    							</td>
	    						</tr>
								<tr bgcolor="#FFFFFF">
									<td>Roll Number</td>
									<td align="center">
										<input type="text" id="txt_roll_no" name="txt_roll_no" class="text_boxes"
										style="width:150px;"  placeholder="write" >
										<input type="hidden" id="roll_id" name="roll_id">
									</td>
								</tr>
								<tr bgcolor="#FFFFFF">
									<td>QC Date</td>
									<td align="center"><input type="text" id="txt_qc_date" name="txt_qc_date"
										class="datepicker" style="width:150px;" placeholder="wirte"
										readonly></td>
								</tr>
								<tr bgcolor="#E9F3FF">
									<td>QC Name</td>
									<td align="center"><input type="text" id="txt_qc_name" name="txt_qc_name"
										class="text_boxes" style="width:150px;" placeholder="write">
									</td>
								</tr>
								<tr bgcolor="#FFFFFF">
									<td>Roll Width (inch)</td>
									<td align="center"><input type="text" id="txt_roll_width" name="txt_roll_width"
										class="text_boxes_numeric" style="width:150px;"
										placeholder="write" onBlur="caculate_roll_length();"></td>
								</tr>
								<tr bgcolor="#E9F3FF">
									<td>Roll Wgt. (Kg)</td>
									<td align="center"><input type="text" id="txt_roll_weight" name="txt_roll_weight"
										class="text_boxes_numeric" style="width:150px;"
										placeholder="write" value="<? echo $qnty; ?>"></td>
								</tr>
								<tr bgcolor="#FFFFFF">
									<td>Roll Length (Yds)</td>
									<td align="center"><input type="text" id="txt_roll_length" name="txt_roll_length"
										class="text_boxes_numeric" style="width:150px;"
										placeholder="write"></td>
								</tr>
								<tr bgcolor="#E9F3FF">
									<td>Reject Qty</td>
									<td align="center"><input type="text" id="txt_reject_qnty" name="txt_reject_qnty"
										class="text_boxes_numeric" style="width:150px;"
										placeholder="write"></td>
								</tr>
								<tr bgcolor="#FFFFFF">
									<td>Construction & Composition</td>
									<td align="center"><input type="text" id="txt_constract_comp" name="txt_constract_comp"
										class="text_boxes" style="width:150px;" readonly
										placeholder="Display" value="<? echo $constraction_comp; ?>">
									</td>
								</tr>
								<tr bgcolor="#E9F3FF">
									<td>GSM</td>
									<td align="center"><input type="text" id="txt_gsm" name="txt_gsm" class="text_boxes"
										style="width:150px;" readonly placeholder="Display"
										value="<? echo $gsm; ?>"></td>
								</tr>
								<tr bgcolor="#FFFFFF">
									<td>Dia</td>
									<td align="center"><input type="text" id="txt_dia" name="txt_dia" class="text_boxes"
										style="width:150px;" readonly placeholder="Display"
										value="<? echo $width; ?>"></td>
								</tr>
								<tr bgcolor="#E9F3FF">
									<td>M/C Dia</td>
									<td align="center"><input type="text" id="txt_mc_dia" name="txt_mc_dia"
										class="text_boxes" style="width:150px;" readonly
										placeholder="Display" value="<? echo $machine_dia; ?>"></td>
								</tr>
								<tr bgcolor="#FFFFFF">
									<td>Color</td>
									<td align="center"><input type="text" id="txt_color" name="txt_color" class="text_boxes"
										style="width:150px;" readonly placeholder="Display"
										value="<? echo $all_color; ?>"></td>
								</tr>

	                            <!--
								<tr bgcolor="#E9F3FF">
									<td>Yarn Count</td>
									<td align="center"><input type="text" id="txt_yarn_count" name="txt_yarn_count"
										class="text_boxes" style="width:150px;"
										placeholder="Display" value="<? // echo  $all_yarn_count; ?>" disabled="disabled">
									</td>
								</tr>
								<tr bgcolor="#FFFFFF">
									<td>Yarn Lot</td>
									<td align="center"><input type="text" id="txt_yarn_lot" name="txt_yarn_lot"
										class="text_boxes" style="width:150px;"
										placeholder="Display" value="<? //  echo $yarn_lot; ?>" disabled="disabled"></td>
								</tr>
								<tr bgcolor="#E9F3FF">
									<td>Spinning Mill</td>
									<td align="center"><input type="text" id="txt_spning_mill" name="txt_spning_mill"
										class="text_boxes" style="width:150px;"
										placeholder="Display" value="<? // echo $all_supplier; ?>" disabled="disabled"></td>
								</tr>

								-->
							</table>
						</td>

						<td width="50">&nbsp;</td>
						<td width="600">
							<table cellpadding="0" cellspacing="0" border="1" width="600" class="rpt_table" rules="all">
								<tr>
									<td colspan="5" align="center"><input type="button" id="reset_details" class="formbuttonplasminus" value="Reset Defect Counter" style="width:200px;" onClick="fn_recet_details();"></td>
								</tr>
							</table>

							<table cellpadding="0" cellspacing="0" border="1" width="600" class="rpt_table" rules="all"
								id="dtls_part">
								<thead>
									<tr>
										<th width="50">SL</th>
										<th width="150">Defect Name</th>
										<th width="100">Defect Count</th>
										<th width="150">Found in (Inch)</th>
										<th>Penalty Point</th>
									</tr>
								</thead>
								<tbody>
									<?
									$i = 1;
									// echo "<pre>";print_r($knit_defect_array);
									foreach ($knit_defect_array as $defect_id => $val) 
									{
										if ($i % 2 == 0)
											$bgcolor = "#E9F3FF";
										else
											$bgcolor = "#FFFFFF";
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
											<td align="center"><? echo $i; ?></td>
											<td><p><? echo $val; ?></p>
												<input type="hidden" id="defectId_<? echo $i; ?>" name="defectId[]" class="defectId" value="<? echo $defect_id; ?>">
												<input type="hidden" class="UpdefectId" id="UpdefectId_<? echo $i; ?>" name="UpdefectId[]"
												value="">
											</td>
											<td><p><input type="text" id="defectcount_<? echo $i; ?>" name="defectcount[]" class="text_boxes_numeric" style="width:90px" onBlur="fn_panelty_point(<? echo $i; ?>)"></p>
											</td>
											<?
											if ($defect_id == 1) $defect_show = '5,6'; else $defect_show = '1,2,3,4';
											?>
											<td>
												<p><? echo create_drop_down("foundInche_" . $i, 152, $knit_defect_inchi_array, "", 1, "-- Select --", 0, "fn_panelty_point(" . $i . ")", '', $defect_show); ?></p>
												<input type="hidden" id="foundInchePoint_<? echo $i; ?>"
												name="foundInchePoint[]" value="">
											</td>
											<td><p><input type="text" id="penaltycount_<? echo $i; ?>" name="penaltycount[]" class="text_boxes_numeric" style="width:130px" readonly></p></td>
										</tr>
										<?
										$i++;
									}
									?>
								</tbody>
								<tfoot>
									<tr bgcolor="#CCCCCC">
										<td colspan="4" align="right">Total Penalty Point: &nbsp;</td>
										<td align="center"><input type="text" class="text_boxes_numeric"
											id="total_penalty_point" name="total_penalty_point"
											style="width:130px" readonly></td>
									</tr>
									<tr bgcolor="#CCCCCC">
										<td colspan="4" align="right">Total Point: &nbsp;</td>
										<td align="center"><input type="text" class="text_boxes_numeric" id="total_point" name="total_point" style="width:130px" readonly></td>
									</tr>
									<tr bgcolor="#CCCCCC">
										<td colspan="4" align="right">Fabric Grade: &nbsp;</td>
										<td align="center"><input type="text" class="text_boxes" id="fabric_grade" name="fabric_grade" style="width:130px" readonly></td>
									</tr>
									<tr>
										<td>Comments</td>
										<td colspan="4"><input type="text" class="text_boxes" id="fabric_comments" name="fabric_comments" style="width:98%"></td>
									</tr>
								</tfoot>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3" align="center" class="button_container">
							<?
							echo load_submit_buttons($_SESSION['page_permission'], "fnc_finish_defect_entry", 0, 1, "reset_form('','','','')", 1);//set_auto_complete(1);
							?>
							<input type="hidden" id="update_id" name="update_id"/>
						</td>
					</tr>
				</table>
				<div id="dtls_list_container" style="margin-top:5px;" align="center">
					<?
					//$sql_dtls = sql_select("select id, pro_dtls_id, barcode_no, roll_id, roll_no, total_penalty_point, total_point, fabric_grade, comments from pro_qc_result_mst where pro_dtls_id=$update_dtls_id and status_active = 1");

					$sql_dtls = sql_select("SELECT a.id, a.pro_dtls_id, a.barcode_no, a.roll_id, a.roll_no, a.total_penalty_point, a.total_point, a.fabric_grade, a.comments from pro_qc_result_mst a, pro_finish_fabric_rcv_dtls b, inv_receive_master c where a.pro_dtls_id=$update_dtls_id and a.pro_dtls_id=b.id and b.mst_id=c.id and c.entry_form=7 and a.entry_form not in (267,283) and a.status_active = 1 ");

					if (count($sql_dtls) > 0) 
					{
						?>
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table">
							<thead>
								<tr>
									<th width="50">SL</th>
									<th width="100">Roll No</th>
									<th width="100">Penalty Point</th>
									<th width="100">Total Point</th>
									<th width="100">Fabric Grade</th>
									<th>Comments</th>
								</tr>
							</thead>
							<tbody>
								<?
								$i = 1;
								foreach ($sql_dtls as $row) 
								{
									if ($i % 2 == 0)
										$bgcolor = "#E9F3FF";
									else
										$bgcolor = "#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor; ?>"
										onClick='get_php_form_data(<? echo $row[csf("id")]; ?>, "populate_qc_from_grey_recv", "finish_fabric_receive_controller" );'
										style="cursor:pointer;">
										<td align="center"><? echo $i; ?></td>
										<td align="center"><? echo $row[csf("roll_no")]; ?></td>
										<td align="right"><? echo number_format($row[csf("total_penalty_point")], 2); ?></td>
										<td align="right"><? echo number_format($row[csf("total_point")], 2); ?></td>
										<td><? echo $row[csf("fabric_grade")]; ?></td>
										<td><? echo $row[csf("comments")]; ?></td>
									</tr>
									<?
									$i++;
								}
								?>
							</tbody>
	                        <!--<tfoot>
								<tr>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
								</tr>
							</tfoot>-->
						</table>
							<?
					}
					?>
				</div>
			</div>
		</form>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "save_update_delete_defect")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$prod_dtls_id = str_replace("'", "", $hide_dtls_id);
		$barcode_no = str_replace("'", "", $txt_barcode);
		$roll_maintain = str_replace("'", "", $hide_roll_maintain);

		if (str_replace("'", "", $prod_dtls_id) != "") {
			if ($roll_maintain == 1)
			{
				$pre_count =  sql_select("select count(id) as count from pro_qc_result_mst where pro_dtls_id=$prod_dtls_id and barcode_no='$barcode_no' and status_active=1 and is_deleted=0");
				if ($pre_count[0][csf("count")] > 0)
				{
					echo "20**Barcode Number is Already Exists";
					die;
				}
			}
			else
			{
				//$pre_count = sql_select("select count(id) as count from pro_qc_result_mst where pro_dtls_id=$prod_dtls_id and status_active=1 and is_deleted=0");
				$pre_count = sql_select("SELECT count(a.id) as count a.id from pro_qc_result_mst a, pro_finish_fabric_rcv_dtls b, inv_receive_master c where a.pro_dtls_id=$prod_dtls_id and a.pro_dtls_id=b.id and b.mst_id=c.id and c.entry_form=7 and a.entry_form not in (267,283) and a.status_active = 1 ");

				if($pre_count[0][csf("count")] > 0)
				{
					echo "20**Duplicate Fabric is Not Allow in Same QC.";
					die;
				}
			}
		}

		//$id = return_next_id("id", "pro_qc_result_mst", 1);
		$id = return_next_id_by_sequence("PRO_QC_RESULT_MST_SEQ", "pro_qc_result_mst", $con);

		$field_array_mst = "id, pro_dtls_id, roll_maintain, barcode_no, roll_id, roll_no, qc_name, roll_width, roll_weight, roll_length, reject_qnty, qc_date, total_penalty_point, total_point, fabric_grade, comments, inserted_by, insert_date";

		$data_array_mst = "(" . $id . "," . $hide_dtls_id . ",0,'',''," . $txt_roll_no . "," . $txt_qc_name . "," . $txt_roll_width . "," . $txt_roll_weight . "," . $txt_roll_length . "," . $txt_reject_qnty . "," . $txt_qc_date . "," . $total_penalty_point . "," . $total_point . "," . $fabric_grade . "," . $fabric_comments . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

		//echo "10**insert into pro_qc_result_mst (".$field_array_mst.") values ".$data_array_mst;die;
		$qc_update_id = $id;

		$count_tbl_length = str_replace("'", "", $count_tbl_length);

		//$dtls_id = return_next_id("id", "pro_qc_result_dtls", 1);
		$field_array_dtls = "id, mst_id, defect_name, defect_count, found_in_inch, found_in_inch_point, penalty_point, inserted_by, insert_date";
		$data_array_dtls = "";

		for ($i = 0; $i <= $count_tbl_length; $i++) {
			$dtls_id = return_next_id_by_sequence("PRO_QC_RESULT_DTLS_SEQ", "pro_qc_result_dtls", $con);
			$defectId = 'defectId_' . $i;
			$defectcount = 'defectcount_' . $i;
			$foundInche = 'foundInche_' . $i;
			$foundIncheVal = 'foundIncheVal_' . $i;
			$penaltycount = 'penaltycount_' . $i;

			if ($data_array_dtls != "") $data_array_dtls .= ",";

			$data_array_dtls .= "(" . $dtls_id . "," . $id . ",'" . $$defectId . "','" . $$defectcount . "','" . $$foundInche . "','" . $$foundIncheVal . "','" . $$penaltycount . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			//$dtls_id++;
		}
		//echo "10**insert into pro_qc_result_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID = $rID2 = true;
		$rID = sql_insert("pro_qc_result_mst", $field_array_mst, $data_array_mst, 0);
		$rID2 = sql_insert("pro_qc_result_dtls", $field_array_dtls, $data_array_dtls, 0);
		//echo "10**$rID**$rID2";die;
		if ($db_type == 0) {
			if ($rID && $rID2) {
				mysql_query("COMMIT");
				echo "0**" . $qc_update_id;
			} else {
				mysql_query("ROLLBACK");
				echo "5**0";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID2) {
				oci_commit($con);
				echo "0**" . $qc_update_id;
			} else {
				oci_rollback($con);
				echo "5**0";
			}
		}

		disconnect($con);
		die;
	} else if ($operation == 1)   // Update Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$roll_maintain = str_replace("'", "", $hide_roll_maintain);
		if ($roll_maintain == 1)
		{
			$pre_count =  sql_select("select count(id) as count from pro_qc_result_mst where pro_dtls_id=$hide_dtls_id and barcode_no=$txt_barcode and id <> $update_id and status_active=1 and is_deleted=0");
			if ($pre_count[0][csf("count")] > 0)
			{
				echo "20**Barcode Number is Already Exists.";
				die;
			}
		}

		$field_array_update = "pro_dtls_id*roll_no*qc_name*roll_width*roll_weight*roll_length*reject_qnty*qc_date*total_penalty_point*total_point*fabric_grade*comments*update_by*update_date";

		$data_array_update = $hide_dtls_id ."*" . $txt_roll_no . "*" . $txt_qc_name . "*" . $txt_roll_width . "*" . $txt_roll_weight . "*" . $txt_roll_length . "*" . $txt_reject_qnty . "*" . $txt_qc_date . "*" . $total_penalty_point . "*" . $total_point . "*" . $fabric_grade . "*" . $fabric_comments . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

		$deleteDetails = execute_query("delete from  pro_qc_result_dtls where mst_id=$update_id");

		//$dtls_id = return_next_id("id", "pro_qc_result_dtls", 1);
		$field_array_dtls = "id, mst_id, defect_name, defect_count, found_in_inch, found_in_inch_point, penalty_point, inserted_by, insert_date";
		$data_array_dtls = "";

		for ($i = 1; $i <= $count_tbl_length; $i++) {
			$dtls_id = return_next_id_by_sequence("PRO_QC_RESULT_DTLS_SEQ", "pro_qc_result_dtls", $con);
			$defectId = 'defectId_' . $i;
			$defectcount = 'defectcount_' . $i;
			$foundInche = 'foundInche_' . $i;
			$foundIncheVal = 'foundIncheVal_' . $i;
			$penaltycount = 'penaltycount_' . $i;

			if ($data_array_dtls != "") $data_array_dtls .= ",";

			$data_array_dtls .= "(" . $dtls_id . "," . $update_id . ",'" . $$defectId . "','" . $$defectcount . "','" . $$foundInche . "','" . $$foundIncheVal . "','" . $$penaltycount . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			//$dtls_id++;
		}
		//echo "insert into pro_qc_result_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;

		$rID = sql_update("pro_qc_result_mst", $field_array_update, $data_array_update, "id", $update_id, 0);
		$rID2 = sql_insert("pro_qc_result_dtls", $field_array_dtls, $data_array_dtls, 0);

		//echo "10**$rID###$rID2";die;
		$qc_update_id = str_replace("'", "", $update_id);

		if ($db_type == 0) {
			if ($rID && $deleteDetails && $rID2) {
				mysql_query("COMMIT");
				echo "1**" . $qc_update_id;
			} else {
				mysql_query("ROLLBACK");
				echo "6**0";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $deleteDetails && $rID2) {
				oci_commit($con);
				echo "1**" . $qc_update_id;
			} else {
				oci_rollback($con);
				echo "6**0";
			}
		}
		disconnect($con);
		die;
	}
	exit();
}

if ($action == "show_qc_listview")
{
	$sql_dtls = sql_select("select id, pro_dtls_id, barcode_no, roll_id, roll_no, total_penalty_point, total_point, fabric_grade, comments from pro_qc_result_mst where pro_dtls_id=$data and status_active=1");
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table">
		<thead>
			<tr>
				<th width="50">SL</th>
				<th width="100">Roll No</th>
				<th width="100">Penalty Point</th>
				<th width="100">Total Point</th>
				<th width="100">Fabric Grade</th>
				<th>Comments</th>
			</tr>
		</thead>
		<tbody>
			<?
			$i = 1;
			foreach ($sql_dtls as $row) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>"
					onClick='get_php_form_data(<? echo $row[csf("id")]; ?>, "populate_qc_from_grey_recv", "finish_fabric_receive_controller" );'
					style="cursor:pointer;">
					<td align="center"><? echo $i; ?></td>
					<td><? echo $row[csf("roll_no")]; ?></td>
					<td align="right"><? echo number_format($row[csf("total_penalty_point")], 2); ?></td>
					<td align="right"><? echo number_format($row[csf("total_point")], 2); ?></td>
					<td><? echo $row[csf("fabric_grade")]; ?></td>
					<td><? echo $row[csf("comments")]; ?></td>
				</tr>
				<?
				$i++;
			}
			?>
		</tbody>
        <!--<tfoot>
        	<tr>
            	<th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>-->
    </table>
    <?
    exit();
}

if ($action == "populate_qc_from_grey_recv")
{
	$supplier_arr = return_library_array("select id, short_name from  lib_supplier", "id", "short_name");
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
		$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	}
	$sql_qc = sql_select("SELECT id, pro_dtls_id, roll_maintain, barcode_no, roll_id, roll_no, qc_name, roll_width, roll_weight, roll_length, reject_qnty, qc_date, total_penalty_point, total_point, fabric_grade, comments from pro_qc_result_mst where id=$data");

	foreach ($sql_qc as $row) {
		echo "document.getElementById('update_id').value 				= '" . $row[csf("id")] . "';\n";
		//echo "document.getElementById('txt_barcode').value 				= '" . $row[csf("barcode_no")] . "';\n";
		echo "document.getElementById('txt_roll_no').value 				= '" . $row[csf("roll_no")] . "';\n";
		//echo "document.getElementById('roll_id').value 			= '" . $row[csf("roll_id")] . "';\n";

		echo "document.getElementById('txt_qc_name').value 			= '" . $row[csf("qc_name")] . "';\n";
		echo "document.getElementById('txt_roll_width').value 				= '" . $row[csf("roll_width")] . "';\n";
		echo "document.getElementById('txt_roll_weight').value 			= '" . $row[csf("roll_weight")] . "';\n";
		echo "document.getElementById('txt_roll_length').value 		= '" . $row[csf("roll_length")] . "';\n";
		echo "document.getElementById('txt_reject_qnty').value 		= '" . $row[csf("reject_qnty")] . "';\n";

		$data_array = sql_select("SELECT  b.body_part_id, b.febric_description_id, b.machine_no_id, b.gsm, b.width, b.color_id, b.yarn_lot, b.yarn_count
			FROM  pro_grey_prod_entry_dtls b
			WHERE b.id=" . $row[csf("pro_dtls_id")]);


		echo "document.getElementById('txt_constract_comp').value 				= '" . $constructtion_arr[$data_array[0][csf("febric_description_id")]] . ' ' . $composition_arr[$data_array[0][csf("febric_description_id")]] . "';\n";


		echo "document.getElementById('txt_gsm').value 				= '" . $data_array[0][csf("gsm")] . "';\n";
		echo "document.getElementById('txt_dia').value 				= '" . $data_array[0][csf("width")] . "';\n";
		$machine_dia = return_field_value("dia_width", "lib_machine_name", "id=" . $data_array[0][csf("machine_no_id")], "dia_width");
		echo "document.getElementById('txt_mc_dia').value 				= '" . $machine_dia . "';\n";

		$color_id_arr = array_unique(explode(",", $data_array[0][csf("color_id")]));
		$all_color = "";
		foreach ($color_id_arr as $color_id) {
			$all_color .= return_field_value("color_name", "lib_color", "id='$color_id'", "color_name") . ",";
		}
		$all_color = chop($all_color, ",");
		echo "document.getElementById('txt_color').value 				= '" . $all_color . "';\n";
		/*$yarn_count_arr = array_unique(explode(",", $data_array[0][csf("yarn_count")]));
		$all_yarn_count = "";
		foreach ($yarn_count_arr as $count_id) {
			$all_yarn_count .= return_field_value("yarn_count", "lib_yarn_count", "id='$count_id'", "yarn_count") . ",";
		}
		$all_yarn_count = chop($all_yarn_count, ",");
		echo "document.getElementById('txt_yarn_count').value 				= '" . $all_yarn_count . "';\n";
		echo "document.getElementById('txt_yarn_lot').value 				= '" . $data_array[0][csf("yarn_lot")] . "';\n";
		$lot_arr = array_unique(explode(",", $data_array[0][csf("yarn_lot")]));
		foreach ($lot_arr as $lot) {
			$supplier_id = return_field_value("max(supplier_id) as supplier_id", "product_details_master", "item_category_id=1 and lot='$lot'", "supplier_id");
			$all_supplier .= $supplier_arr[$supplier_id] . ",";
		}
		$all_supplier = chop($all_supplier, ',');*/


		// $sup=return_field_value("supplier_id", "lib_supplier", "id=1");
		// return_field_value("buyer_name", "lib_buyer", "id=1");

		//echo "document.getElementById('txt_spning_mill').value 				= '" . $all_supplier . "';\n";
		echo "document.getElementById('txt_qc_date').value 				= '" . change_date_format($row[csf("qc_date")]) . "';\n";

		$dtls_part_tbody_data = "";
		$dtls_sql = sql_select("select id, defect_name, defect_count, found_in_inch, found_in_inch_point, penalty_point from pro_qc_result_dtls where mst_id=" . $row[csf('id')] . " and status_active=1 and is_deleted=0");
		$dtls_data_arr = array();
		foreach ($dtls_sql as $dtls_row) {
			$dtls_data_arr[$dtls_row[csf("defect_name")]]["dtls_id"] = $dtls_row[csf("id")];
			$dtls_data_arr[$dtls_row[csf("defect_name")]]["defect_name"] = $dtls_row[csf("defect_name")];
			$dtls_data_arr[$dtls_row[csf("defect_name")]]["defect_count"] = $dtls_row[csf("defect_count")];
			$dtls_data_arr[$dtls_row[csf("defect_name")]]["found_in_inch"] = $dtls_row[csf("found_in_inch")];
			$dtls_data_arr[$dtls_row[csf("defect_name")]]["found_in_inch_point"] = $dtls_row[csf("found_in_inch_point")];
			$dtls_data_arr[$dtls_row[csf("defect_name")]]["penalty_point"] = $dtls_row[csf("penalty_point")];
		}
		$i = 1;
		echo "$('#dtls_part').find('input').not('.defectId, .UpdefectId').val('');\n";
		foreach ($knit_defect_array as $defect_id => $val) {
			if ($dtls_data_arr[$defect_id]["defect_name"] > 0) {
				echo "document.getElementById('UpdefectId_$i').value 				= '" . $dtls_data_arr[$defect_id]["defect_name"] . "';\n";
				echo "document.getElementById('defectcount_$i').value 				= '" . $dtls_data_arr[$defect_id]["defect_count"] . "';\n";
				echo "document.getElementById('foundInche_$i').value 				= '" . $dtls_data_arr[$defect_id]["found_in_inch"] . "';\n";
				echo "document.getElementById('foundInchePoint_$i').value 				= '" . $dtls_data_arr[$defect_id]["found_in_inch_point"] . "';\n";
				echo "document.getElementById('penaltycount_$i').value 				= '" . $dtls_data_arr[$defect_id]["penalty_point"] . "';\n";
			}
			$i++;
		}
		echo "document.getElementById('total_penalty_point').value 				= '" . $row[csf("total_penalty_point")] . "';\n";
		echo "document.getElementById('total_point').value 				= '" . $row[csf("total_point")] . "';\n";
		echo "document.getElementById('fabric_grade').value 				= '" . $row[csf("fabric_grade")] . "';\n";
		echo "document.getElementById('fabric_comments').value 				= '" . $row[csf("comments")] . "';\n";
		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_finish_defect_entry',1);\n";
		exit();
	}
}


if ($action == "knit_defect_popup2")
{

	echo load_html_head_contents("Finish Fabric product Entry", "../../", 1, 1, $unicode);
	extract($_REQUEST);

	$buyer_point=sql_select( "SELECT a.buyer_id,b.range_serial,b.grade from buyer_wise_grade_mst a,buyer_wise_grade_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.buyer_id='$buyer_id'");
	$buyer_point_arr=array();
	foreach($buyer_point as $key=>$value)
	{
		$buyer_point_arr[$value[csf("range_serial")]]=$value[csf("grade")];
	}
	//print_r($buyer_point_arr);
	$fabric_grade_arr=$fabric_shade;
	foreach($fabric_grade_arr as $key=>$val)
	{
		$fabric_grade2[$key]=$val;
	}
	//print_r($fabric_grade);
	$buyer_point_arr=json_encode($buyer_point_arr);
	$fabric_grade2=json_encode($fabric_grade2);

	$already_qc=return_field_value("sum(roll_weight) as qnty", "pro_qc_result_mst", "pro_dtls_id=$update_dtls_id and status_active=1 and is_deleted=0", "qnty") ;
	$already_roll=return_field_value("count(id) as qnty", "pro_qc_result_mst", "pro_dtls_id=$update_dtls_id and status_active=1 and is_deleted=0", "qnty") ;
	//echo $already_roll." and ".$already_qc;
	$variable_data = sql_select("select fabric_grade, get_upto_first, get_upvalue_first, get_upto_second,company_name, get_upvalue_second from variable_settings_production where  company_name=$company_id and variable_list=45 order by company_name,get_upvalue_first asc");
	$exc_perc = array();
	$i = 0;
	$variable_data_count = count($variable_data);
	foreach ($variable_data as $row)
	{
		if ($exp[$row[csf("company_name")]] == '') $i = 0;
		$exc_perc[$row[csf("company_name")]]['limit'][$i] = $row[csf("get_upvalue_first")] . "__" . $row[csf("get_upvalue_second")];
		$exc_perc[$row[csf("company_name")]]['grade'][$i] = $row[csf("fabric_grade")];
		$i++;
		$exp[$row[csf("company_name")]] = 1;

	}

	echo load_html_head_contents("Finish Fabric Production Entry", "../../", 1, 1, $unicode, '', '');
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
		$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	}

	if ($roll_maintained == 1)
	{

	}
	else
	{
		$data_array = sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company,
			b.id as dtls_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.machine_no_id, b.gsm, b.width, b.color_id, b.receive_qnty as qnty
			FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b WHERE a.id=b.mst_id and a.entry_form in(7) and b.id=$update_dtls_id");

		$roll_dtls_data_arr = array();
		foreach ($data_array as $row) {

			$constraction_comp = $constructtion_arr[$row[csf("fabric_description_id")]] . " " . $composition_arr[$row[csf('fabric_description_id')]];
			$machine_dia = return_field_value("dia_width", "lib_machine_name", "id=" . $row[csf("machine_no_id")], "dia_width");
			$gsm = $row[csf("gsm")];
			$width = $row[csf("width")];
			$color_id_arr = array_unique(explode(",", $row[csf("color_id")]));
			$all_color = "";
			foreach ($color_id_arr as $color_id) {
				$all_color .= return_field_value("color_name", "lib_color", "id='$color_id'", "color_name") . ",";
			}
			$all_color = chop($all_color, ",");
			$qnty = $row[csf("qnty")];


		}
		$disable = "disabled";
	}
	?>
	<script>
		var buyer_point_arr='<? echo $buyer_point_arr;?>';
		var buyer_point_arr=JSON.parse(buyer_point_arr);

		var grades='<? echo $fabric_grade2;?>';
		var grades=JSON.parse(grades);


		var pemission = '<? echo $_SESSION['page_permission']; ?>';

		var exc_perc =<? echo json_encode($exc_perc); ?>;
        //alert(exc_perc);
        function fabric_grading(comp, point) {
            //alert(comp)
            var newp = exc_perc[comp]["limit"];
            newp = JSON.stringify(newp);
            var newstr = newp.split(",");
            for (var m = 0; m < newstr.length; m++) {
            	var limit = exc_perc[comp]["limit"][m].split("__");
            	if ((limit[1] * 1) == 0 && (point * 1) >= (limit[0] * 1)) {
            		return ( exc_perc[comp]["grade"][m]);
            	}
            	if ((point * 1) >= (limit[0] * 1) && (point * 1) <= (limit[1] * 1)) {
            		return exc_perc[comp]["grade"][m];
            	}
                // alert( newstr[m]+"=="+m)
            }
            return '';
        }

        var roll_maintain = '<? echo $roll_maintained; ?>';
        function fn_barcode() {
        	var dtls_id = $('#hide_dtls_id').val();
        	var roll_maintain = $('#hide_roll_maintain').val();
        	if (dtls_id == "" && roll_maintain != 1) {
        		alert("Select Data First.");
        		return;
        	}
        	else {
        		var title = 'Barcode Or Details Info';
        		var page_link = 'finish_fabric_receive_controller.php?update_dtls_id=' + dtls_id + '&roll_maintained=' + roll_maintain + '&action=barcode_defect_popup';
        		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=350px,center=1,resize=1,scrolling=0', '../');
        		emailwindow.onclose = function () {
        			var bar_code_ref = this.contentDoc.getElementById("hide_barcode_id").value.split("**");
        			if (bar_code_ref[1] != "") {
        				get_php_form_data(bar_code_ref[0], 'barcode_roll_find', 'finish_fabric_receive_controller');
        			}
        		}
        	}
        }


        if (roll_maintain == 1) {
        	$('#txt_barcode').live('keydown', function (e) {
        		if (e.keyCode === 13) {
        			e.preventDefault();
        			var bar_code = $('#txt_barcode').val();
        			get_php_form_data(bar_code, 'barcode_roll_find', 'finish_fabric_receive_controller');
        		}
        	});

        	$(document).ready(function (e) {
        		var roll_maintain = $('#hide_roll_maintain').val() * 1;
        		if (roll_maintain > 0) {
        			$('#txt_barcode').focus();
        		}
        		else {
        			$('#txt_qc_name').focus();
        		}

        	});
        }

        function caculate_roll_length() {
        	var roll_weight = $('#txt_roll_weight').val() * 1;
        	var roll_width = $('#txt_roll_width').val() * 1;
        	var gsm = $('#txt_gsm').val() * 1;
        	var roll_length = ((roll_weight * 1000) / (gsm * roll_width * 0.0254) * 1.09361);
        	$('#txt_roll_length').val(number_format(roll_length, 4, '.', ''));
        }

        function fn_panelty_point(i) {

        	var defectId = $('#defectId_' + i).val() * 1;
        	var defect_count = $('#defectcount_' + i).val() * 1;
        	var found_inche = $('#foundInche_' + i).val() * 1;
        	var company_id = $('#company_id').val();
        	var found_inche_calc = "";
        	if (found_inche == 1) found_inche_calc = 1;
        	else if (found_inche == 2) found_inche_calc = 2;
        	else if (found_inche == 3) found_inche_calc = 3;
        	else if (found_inche == 4) found_inche_calc = 4;
        	else if (found_inche == 5) found_inche_calc = 2;
        	else if (found_inche == 6) found_inche_calc = 4;
        	//found_inche_calc=buyer_point_arr[defectId][found_inche];
        	if(!found_inche_calc)found_inche_calc=0;
        	var penalty_val = defect_count * found_inche_calc;
        	$('#penaltycount_' + i).val(penalty_val);
        	var ddd = {dec_type: 4, comma: 0, currency: ''}
        	var numRow = $('table#dtls_part tbody tr').length;
        	math_operation("total_penalty_point", "penaltycount_", "+", numRow, ddd);
        	var penalty_ratio = (($('#total_penalty_point').val() * 1) * 36 * 100) / (($('#txt_roll_weight').val() * 1) * ($('#txt_roll_length').val() * 1));
        	$('#total_point').val(number_format(penalty_ratio, 4, '.', ''));
        	var floor_ratio=Math.floor(penalty_ratio);
        	var ttl_penalty=$('#total_penalty_point').val() * 1;
        	$('#fabric_grade').val('');
        	ttl_penalty=Math.floor(ttl_penalty);
        	//alert(ttl_penalty);
        	var grade_val= trim(buyer_point_arr[ttl_penalty]) ;
        	var grade_val= trim(grades[grade_val]) ;

             //$('#fabric_grade').val(fabric_grading(company_id, penalty_ratio));
             $('#fabric_grade').val(grade_val);
         }
         function generate_report_file(data,action,page)
         {
         	window.open("finish_fabric_receive_controller.php?data=" + data+'&action='+action, true );
         }

         function fnc_finish_defect_entry(operation) {

         	if (operation == 2) {
         		show_msg('13');
         		return;
         	}

         	if(operation == 4)
         	{
         		generate_report_file($('#update_id').val() ,
         			'KnittingProductionPrint', 'requires/finish_fabric_receive_controller');
         		return;


         	}

         	if (form_validation('txt_roll_length*fabric_grade', 'Roll Length*Grade') == false) {
         		return;
         	}
         	var table_length = $('#dtls_part tbody tr').length;
         	var table_length2 = $('#dtls_part2 tbody tr').length;
         	var data_string = "";
         	var data_string2 = "";
         	var k = 1;
         	var count_tbl_length = 0;
         	for (var i = 1; i <= table_length; i++) {
         		var defect_name = $('#defectId_' + i).val();
         		var UpdefectId = $('#UpdefectId_' + i).val();
         		var department_name = $('#department_' + i).val();
         		var defect_count = $('#defectcount_' + i).val();
         		var found_in_inche = $('#foundInche_' + i).val();
         		var found_inche_val = "";
         		var penalty_point = $('#penaltycount_' + i).val() * 1;


         		if (penalty_point > 0) {
         			if (found_in_inche == 5) found_inche_val = 2;
         			else if (found_in_inche == 6) found_inche_val = 4;
         			else found_inche_val = found_in_inche;
         			data_string += '&defectId_' + k + '=' + defect_name + '&defectcount_' + k + '=' + defect_count+ '&UpdefectId_' + k + '=' + UpdefectId + '&foundInche_' + k + '=' + found_in_inche + '&foundIncheVal_' + k + '=' + found_inche_val + '&penaltycount_' + k + '=' + penalty_point+ '&department_' + k + '=' + department_name;
         			count_tbl_length++;
         			k++;

         		}
         	}

         	for(var j=1;j<=table_length2;j++)
         	{
         		var defect_name2 = $('#defectId2_' + j).val();
         		var UpdefectId2 = $('#UpdefectId2_' + j).val();
         		var department_name2 = $('#department2_' + j).val();
         		var found_in_inche2 = $('#foundInche2_' + j).val();
         		data_string2 += '&defectId2_' + j + '=' + defect_name2  + '&foundInche2_' + j + '=' + found_in_inche2 + '&UpdefectId2_' + j + '=' + UpdefectId2 + '&department2_' + j + '=' + department_name2;


         	}
         	var no_of_roll = '<? echo $no_of_roll; ?>';
         	var qc_pass_qty = '<? echo $qc_pass_qty; ?>';
         	var batch_main_id = '<? echo $batch_id; ?>';
         	data_string = data_string + '&count_tbl_length=' + count_tbl_length+ '&batch_main_id=' + batch_main_id+ '&no_of_roll=' + no_of_roll+ '&qc_pass_qty=' + qc_pass_qty+ '&table_length2=' + table_length2+ '&data_string2=' + data_string2;


         	var data = "action=save_update_delete_defect2&operation=" + operation + get_submitted_data_string('hide_dtls_id*hide_roll_maintain*txt_roll_no*roll_id*txt_roll_weight*txt_roll_length*total_penalty_point*total_point*fabric_grade*fabric_comments*txt_length_percent*txt_width_percent*txt_twisting_percent*txt_gsm*txt_dia*update_id', "../../") + data_string;
            //alert(data);return;
            //alert(data);
            freeze_window(operation);

            http.open("POST", "finish_fabric_receive_controller.php", true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_finish_defect_entry_response;
        }

        function fnc_finish_defect_entry_response() {
        	if (http.readyState == 4) {
                //release_freezing();return;
                var reponse = trim(http.responseText).split('**');
                if (reponse[0] == 20) {
                	alert(reponse[1]);
                	release_freezing();
                	return;
                }
                show_msg(reponse[0]);
                if ((reponse[0] == 0 || reponse[0] == 1)) {
                	document.getElementById('update_id').value = reponse[1];
                	var prod_dtls_id = $('#hide_dtls_id').val();
                	$('#dtls_list_container').html("");
                	show_list_view(prod_dtls_id, 'show_qc_listview2', 'dtls_list_container', 'finish_fabric_receive_controller', '');
                	set_button_status(0, pemission, 'fnc_finish_defect_entry', 1);
                	$("#total_penalty_point").val('');
                	$("#total_point").val('');
                	$("#fabric_grade").val('');
                	fn_recet_details();
                	$('#master_part').find('input', 'select').val('');
                	release_freezing();
                }
                else {
                	release_freezing();
                }

            }
        }

        function fn_recet_details22() {
        	$('#dtls_part').find('input').val("");
        	$('#dtls_part').find('select').val(0);


        }

        function fn_recet_details()
        {
        	var table_length = $('#dtls_part tbody tr').length;
        	var table_length2 = $('#dtls_part2 tbody tr').length;
        	for (var i = 1; i <= table_length; i++)
        	{
        		$('#department_' + i).val('0');
        		$('#defectcount_' + i).val('');
        		$('#foundInche_' + i).val('0');
        		$('#penaltycount_' + i).val('');
        	}
        	for (var j = 1; j <= table_length2; j++)
        	{
        		$('#department2_' + j).val('0');
        		$('#foundInche2_' + j).val('0');
        	}

        }
        function calculate_yds()
        {
        	var dia=$("#txt_dia").val()*1;
        	var gsm=$("#txt_gsm").val()*1;
        	var weight=$("#txt_roll_weight").val()*1;
        	//var yds  =  weight / ( (dia*gsm) /1550 / 1000);
        	var yds=(43056/(dia*gsm))*weight;
        	yds=yds.toFixed(2);
        	$("#txt_roll_length").val(yds);
        }


    </script>
    <body onLoad="set_hotkey()">
    	<? echo load_freeze_divs("../../", $_SESSION['page_permission']); ?>
    	<form name="defectQcResult_1" id="defectQcResult_1" autocomplete="off">
    		<div style="width:1260px">
    			<input type="hidden" id="hide_dtls_id" value="<? echo $update_dtls_id; ?>"/>
    			<input type="hidden" id="hide_roll_maintain" value="<? echo $roll_maintained; ?>"/>
    			<input type="hidden" id="company_id" value="<? echo $company_id; ?>"/>


    			<table width="1100" border="0">
    				<tr>
    					<td width="500" valign="top">
    						<table cellpadding="0" cellspacing="0" border="1" width="500" class="rpt_table" rules="all"
    						id="master_part">

    						<tr>
    							<td><strong>Roll Number</strong></td>
    							<td><strong>Actual Dia</strong></td>
    							<td><strong>Actual GSM</strong></td>
    							<td><strong>Roll Wt.(Kg)</strong></td>
    							<td><strong>Roll Wt.(Yds)</strong></td>

    						</tr>

    						<tr>
    							<td align="left">
    								<input type="text" id="txt_roll_no" name="txt_roll_no" class="text_boxes"
    								style="width:120px;" onDblClick="" readonly disabled="" placeholder="" >
    								<input type="hidden" id="roll_id" name="roll_id">
    							</td>
    							<td align="left">
    								<input type="text" id="txt_dia" name="txt_dia" onBlur="calculate_yds();"  class="text_boxes"
    								style="width:55px;"
    								value="<? //echo $width; ?>">
    							</td>
    							<td align="left"> <input type="text" id="txt_gsm"  onblur="calculate_yds();"   name="txt_gsm" class="text_boxes"
    								style="width:55px;"
    								value="<? //echo $gsm; ?>">
    							</td>
    							<td align="left">
    								<input type="text" id="txt_roll_weight"  onblur="calculate_yds();"  name="txt_roll_weight"
    								class="text_boxes_numeric" style="width:55px;"
    								placeholder="" value="<?//echo $qnty; ?>">
    							</td>
    							<td align="left"><input type="text" id="txt_roll_length" name="txt_roll_length"
    								class="text_boxes_numeric" readonly style="width:55px;"
    								>
    							</td>



    						</tr>
    						<tr>
    							<td colspan="3" align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Shrinkage %</strong></td>
    							<td colspan="2">&nbsp;</td>
    						</tr>

    						<tr>
    							<td><strong>Length(%)</strong></td>
    							<td><strong>Width(%)</strong></td>
    							<td><strong>Twisting(%)</strong></td>
    							<td colspan="2">&nbsp;</td>
    						</tr>

    						<tr>
    							<td>
    								<input type="text_boxes_numeric" id="txt_length_percent" name="txt_length_percent"
    								class="text_boxes_numeric" style="width:70px;" >
    							</td>
    							<td>
    								<input type="text_boxes_numeric" id="txt_width_percent" name="txt_width_percent" class="text_boxes_numeric" style="width:70px;" ></td>
    								<td>
    									<input type="text_boxes_numeric" id="txt_twisting_percent" name="txt_twisting_percent"
    									class="text_boxes_numeric" style="width:70px;" >
    								</td>
    								<td colspan="2">&nbsp;</td>
    							</tr>
    						</table>
    					</td>
    					<td width="50">&nbsp;</td>
    					<td width="760">
    						<table cellpadding="0" cellspacing="0" border="1" width="760" class="rpt_table" rules="all">
    							<tr>
    								<td colspan="7" align="center"><input type="button" id="reset_details"
    									class="formbuttonplasminus"
    									value="Reset Defect Counter" style="width:200px;"
    									onClick="fn_recet_details();"></td>
    								</tr>
    							</table>

    							<table cellpadding="0" cellspacing="0" border="1" width="760" class="rpt_table" rules="all"
    							id="dtls_part">
    							<thead>
    								<tr>
    									<th width="25">SL</th>
    									<th width="100">Defect Name</th>
    									<th width="120">Defect Short Name</th>
    									<th width="90">Defect Count</th>
    									<th width="120">Found in (Inch)</th>
    									<th width="90">Penalty Point</th>
    									<th width="80">Department</th>
    								</tr>
    							</thead>
    							<tbody>
    								<?
    								$i = 1;


    								foreach ($defect_arr as $defect_id => $val) {
    									if ($i % 2 == 0)
    										$bgcolor = "#E9F3FF";
    									else
    										$bgcolor = "#FFFFFF";
    									?>
    									<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
    										<td style="width: 25px;" align="center"><? echo $i; ?></td>
    										<td><p><? echo $finish_qc_defect_array[$val]; ?></p>
    											<input type="hidden" id="defectId_<? echo $i; ?>" style="width: 100px;" name="defectId[]" class="defectId" value="<? echo $defect_id; ?>">
    											<input type="hidden" class="UpdefectId" id="UpdefectId_<? echo $i; ?>" name="UpdefectId[]"
    											value="" >
    										</td>

    										<td><p><? echo $defect_short_arr[$defect_id]; ?> </p>
    											<input style="width: 120px;"  type="hidden" id="defectShortId_<? echo $i; ?>" name="defectShortId[]" class="defectShortId" value="<? echo $defect_id; ?>">
    											<input type="hidden" class="UpdefectShortId" id="UpdefectShortId_<? echo $i; ?>" name="UpdefectShortId[]"
    											value="">

    										</td>


    										<td><p><input type="text" id="defectcount_<? echo $i; ?>" name="defectcount[]"
    											class="text_boxes_numeric" style="width:90px"
    											onBlur="fn_panelty_point(<? echo $i; ?>)"></p></td>
    											<?
    											if ($defect_id == 1) $defect_show = '5,6'; else $defect_show = '1,2,3,4';
    											?>
    											<td>
    												<p><? echo create_drop_down("foundInche_" . $i, 132, $knit_defect_inchi_array, "", 1, "-- Select --", 0, "fn_panelty_point(" . $i . ")", '', ''); ?></p>
    												<input type="hidden" id="foundInchePoint_<? echo $i; ?>"
    												name="foundInchePoint[]" value="">
    											</td>
    											<td><p><input type="text" id="penaltycount_<? echo $i; ?>" name="penaltycount[]"
    												class="text_boxes_numeric" style="width:90px" readonly></p></td>

    												<td>
    													<p><?
    													$department_show="2,3,4,15";
    													echo create_drop_down("department_" . $i, 120, $production_process, "", 1, "-- Select --", 0, "", '', $department_show); ?></p>
    													<input type="hidden" id="hiddenDepartment_<? echo $i; ?>"
    													name="hiddenDepartment[]" value="">
    												</td>

    											</tr>
    											<?
    											$i++;
    										}
    										?>
    									</tbody>
    									<tfoot>
    										<tr bgcolor="#CCCCCC">
    											<td colspan="6" align="right">Total Penalty Point: &nbsp;</td>
    											<td align="center"><input type="text" class="text_boxes_numeric"
    												id="total_penalty_point" name="total_penalty_point"
    												style="width:130px" readonly></td>
    											</tr>
    											<tr bgcolor="#CCCCCC">
    												<td colspan="6" align="right" title="(Total Penalty Point *36*100)/ ( Roll Kg* Roll Yds  )">Total Point: &nbsp;</td>
    												<td align="center"><input type="text" class="text_boxes_numeric" id="total_point"
    													name="total_point" style="width:130px" readonly></td>
    												</tr>
    												<tr bgcolor="#CCCCCC">
    													<td colspan="6" align="right">Fabric Grade: &nbsp;</td>
    													<td align="center"><input type="text" class="text_boxes" id="fabric_grade"
    														name="fabric_grade" style="width:130px" readonly></td>
    													</tr>
    													<tr>
    														<td>Comments</td>
    														<td colspan="6"><input type="text" class="text_boxes" id="fabric_comments"
    															name="fabric_comments" style="width:98%"></td>
    														</tr>
    													</tfoot>
    												</table>
    												<br><br><br>


    												<table cellpadding="0" cellspacing="0" border="1" width="760" class="rpt_table" rules="all" id="dtls_part2">
    													<thead>
    														<tr>
    															<td width="25"><strong>SL</strong></td>
    															<td align="left" width="100"><strong>Defect Name</strong></td>
    															<td  align="left"  width="120"><strong>Defect Short Name</strong></td>
    															<td align="left"   width="120"><strong>Found in (Inch)</strong></td>
    															<td  align="left"  width="80"><strong>Department</strong></td>
    														</tr>
    													</thead>
    													<tbody>
    														<?
    														$i = 1;



    														foreach ($knit_defect_array2 as $defect_id => $val) {
    															if ($i % 2 == 0)
    																$bgcolor = "#E9F3FF";
    															else
    																$bgcolor = "#FFFFFF";
    															?>
    															<tr bgcolor="<? echo $bgcolor; ?>" id="tr2_<? echo $i; ?>">
    																<td style="width: 25px;" align="center"><? echo $i; ?></td>
    																<td><p><? echo $val; ?></p>
    																	<input type="hidden" id="defectId2_<? echo $i; ?>" style="width: 100px;" name="defectId2[]" class="defectId2" value="<? echo $defect_id; ?>">
    																	<input type="hidden" class="UpdefectId2" id="UpdefectId2_<? echo $i; ?>" name="UpdefectId2[]"
    																	value="" >
    																</td>

    																<td><p><? echo $knit_defect_short_array2[$defect_id]; ?> </p>
    																	<input style="width: 120px;"  type="hidden" id="defectShortId2_<? echo $i; ?>" name="defectShortId2[]" class="defectShortId2" value="<? echo $defect_id; ?>">
    																	<input type="hidden" class="UpdefectShortId2" id="UpdefectShortId2_<? echo $i; ?>" name="UpdefectShortId2[]"
    																	value="">

    																</td>



    																<?

    																if ($defect_id == 1) $defect_show = '5,6'; else $defect_show = '1,2,3,4';
    																?>
    																<td>
    																	<p><? echo create_drop_down("foundInche2_" . $i, 152, $knit_defect_inchi_array2, "", 0, "-- Select --", 0, "", '', ''); ?></p>
    																	<input type="hidden" id="foundInchePoint2_<? echo $i; ?>"
    																	name="foundInchePoint2[]" value="">
    																</td>


    																<td>
    																	<p><?
    																	$department_show="2,3,4,15";
    																	echo create_drop_down("department2_" . $i, 140, $production_process, "", 1, "-- Select --", 0, "", '', $department_show); ?></p>
    																	<input type="hidden" id="hiddenDepartment2_<? echo $i; ?>"
    																	name="hiddenDepartment2[]" value="">
    																</td>

    															</tr>
    															<?
    															$i++;
    														}
    														?>
    													</tbody>

    												</table>



    											</td>
    										</tr>
    										<tr>
    											<td colspan="3">&nbsp;</td>
    										</tr>
    										<tr>
    											<td colspan="3" align="center" class="button_container">
    												<?
									echo load_submit_buttons($_SESSION['page_permission'], "fnc_finish_defect_entry", 0, 1, "reset_form('','','','')", 1);//set_auto_complete(1);
									?>
									<input type="hidden" id="update_id" name="update_id"/>
								</td>
							</tr>
						</table>


						<div id="dtls_list_container" style="margin-top:5px;" align="center">
							<?
							$sql_dtls = sql_select("SELECT id, pro_dtls_id, barcode_no, roll_id, roll_no, total_penalty_point, total_point, fabric_grade, comments,roll_weight from pro_qc_result_mst where pro_dtls_id=$update_dtls_id and status_active = 1 order by  roll_no asc ");
							if (count($sql_dtls) > 0) {
								?>
								<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table">
									<thead>
										<tr>
											<th width="50">SL</th>
											<th width="100">Roll No</th>
											<th width="100">Penalty Point</th>
											<th width="100">Total Point</th>
											<th width="100">Fabric Grade</th>
											<th width="100">Roll Weight</th>
											<th>Comments</th>
										</tr>
									</thead>
									<tbody>
										<?
										$i = 1;
										$total_weight=0;

										foreach ($sql_dtls as $row) {
											if ($i % 2 == 0)
												$bgcolor = "#E9F3FF";
											else
												$bgcolor = "#FFFFFF";
											?>
											<tr bgcolor="<? echo $bgcolor; ?>"
												onClick='get_php_form_data(<? echo $row[csf("id")]; ?>, "populate_qc_from_grey_recv2", "finish_fabric_receive_controller" );'
												style="cursor:pointer;">
												<td align="center"><? echo $i; ?></td>
												<td align="center"><? echo $row[csf("roll_no")]; ?></td>
												<td align="right"><? echo number_format($row[csf("total_penalty_point")], 2); ?></td>
												<td align="right"><? echo number_format($row[csf("total_point")], 2); ?></td>
												<td><? echo $row[csf("fabric_grade")]; ?></td>
												<td><? echo $qty_r= number_format( $row[csf("roll_weight")],2); ?></td>
												<td><? echo $row[csf("comments")]; ?></td>
											</tr>
											<?
											$total_weight+=$qty_r;
											$i++;
										}
										?>
									</tbody>
									<tfoot>
										<tr>
											<td colspan="5" align="right"><strong>Total:</strong></td>
											<td colspan="2" align="left"><strong><? echo number_format($total_weight,2); ?></strong></td>
										</tr>
									</tfoot>

								</table>
								<?
							}
							?>
						</div>
					</div>
				</form>
			</body>
			<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
			</html>
			<?
			exit();
		}


		if ($action == "save_update_delete_defect2")
		{
			$process = array(&$_POST);
			extract(check_magic_quote_gpc($process));


			$auto_roll_gen=return_field_value("count(b.id) as cnt", "pro_finish_fabric_rcv_dtls a,pro_qc_result_mst b", "a.id=b.pro_dtls_id and a.status_active=1   and a.batch_id=$batch_main_id", "cnt") ;
			$auto_roll_gen=($auto_roll_gen*1)+1;


	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		$already_qc=return_field_value("sum(roll_weight) as qnty", "pro_qc_result_mst", "pro_dtls_id=$hide_dtls_id and status_active=1 and is_deleted=0", "qnty") ;
		$already_roll=sql_select(" SELECT count(id) as ids from pro_qc_result_mst where pro_dtls_id=$hide_dtls_id and status_active=1 and is_deleted=0 ") ;
		$already_roll=$already_roll[0][csf("ids")];

		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$prod_dtls_id = str_replace("'", "", $hide_dtls_id);
		$barcode_no = str_replace("'", "", $txt_barcode);
		$roll_maintain = str_replace("'", "", $hide_roll_maintain);

		$qc_pass_qty = str_replace("'", "", $qc_pass_qty);
		$weight = str_replace("'", "", $txt_roll_weight);

		if($no_of_roll<($already_roll+1))
		{
			echo "20**Roll over than Production No of Roll";
			die;
		}
		if($qc_pass_qty<($already_qc+$weight))
		{
			echo "20**Weight Qnty over than Qc Pass Qty";
			die;
		}
		//echo 10;die;

		//$id = return_next_id("id", "pro_qc_result_mst", 1);
		$id = return_next_id_by_sequence("PRO_QC_RESULT_MST_SEQ", "pro_qc_result_mst", $con);
		$field_array_mst = "id, pro_dtls_id, roll_maintain, roll_id, roll_no,actual_gsm,actual_dia,length_percent,width_percent,twisting_percent ,roll_weight, roll_length, total_penalty_point, total_point, fabric_grade, comments, inserted_by, insert_date";

		$data_array_mst = "(" . $id . "," . $hide_dtls_id . ",0,'','" . $auto_roll_gen . "'," . $txt_gsm . "," . $txt_dia . "," . $txt_length_percent . "," . $txt_width_percent . "," . $txt_twisting_percent . "," . $txt_roll_weight . "," . $txt_roll_length . "," . $total_penalty_point . "," . $total_point . "," . $fabric_grade . "," . $fabric_comments . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";


		$qc_update_id = $id;

		$count_tbl_length = str_replace("'", "", $count_tbl_length);

		//$dtls_id = return_next_id("id", "pro_qc_result_dtls", 1);
		$field_array_dtls = "id, mst_id, defect_name, defect_count, found_in_inch, found_in_inch_point, penalty_point,department, inserted_by, insert_date";
		$field_array_dtls2 = "id, mst_id, defect_name, found_in_inch,department,form_type, inserted_by, insert_date";
		$data_array_dtls = "";
		$data_array_dtls2 = "";

		for ($i = 1; $i <= $count_tbl_length; $i++) {
			$dtls_id = return_next_id_by_sequence("PRO_QC_RESULT_DTLS_SEQ", "pro_qc_result_dtls", $con);
			$defectId = 'defectId_' . $i;
			$defectcount = 'defectcount_' . $i;
			$foundInche = 'foundInche_' . $i;
			$foundIncheVal = 'foundIncheVal_' . $i;
			$penaltycount = 'penaltycount_' . $i;
			$department = 'department_' . $i;

			if ($data_array_dtls != "") $data_array_dtls .= ",";

			$data_array_dtls .= "(" . $dtls_id . "," . $id . ",'" . $$defectId . "','" . $$defectcount . "','" . $$foundInche . "','" . $$foundIncheVal . "','" . $$penaltycount . "','" . $$department . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			//$dtls_id++;
		}

		for ($j = 1; $j <= $table_length2; $j++)
		{
			$dtls_id = return_next_id_by_sequence("PRO_QC_RESULT_DTLS_SEQ", "pro_qc_result_dtls", $con);
			$defectId2 = 'defectId2_' . $j;
			$foundInche2 = 'foundInche2_' . $j;
			$department2 = 'department2_' . $j;
			if($$foundInche2 || $$department2)
			{
				if ($data_array_dtls2 != "") $data_array_dtls2 .= ",";
				$data_array_dtls2 .= "(" . $dtls_id . "," . $id . ",'" . $$defectId2 . "','" . $$foundInche2 . "','" . $$department2 . "','2'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
 				//$dtls_id++;

			}

		}


		//echo "10**insert into pro_qc_result_dtls (".$field_array_dtls2.") values ".$data_array_dtls2;die;
		$rID = $rID2 = true;
		$rID = sql_insert("pro_qc_result_mst", $field_array_mst, $data_array_mst, 0);
		$rID2 = sql_insert("pro_qc_result_dtls", $field_array_dtls, $data_array_dtls, 0);
		if($data_array_dtls2)
		{
			$rID3 = sql_insert("pro_qc_result_dtls", $field_array_dtls2, $data_array_dtls2, 0);
		}
		//echo "10**$rID**$rID2";die;
		if ($db_type == 0) {
			if ($rID && $rID2) {
				mysql_query("COMMIT");
				echo "0**" . $qc_update_id;
			} else {
				mysql_query("ROLLBACK");
				echo "5**0";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID2) {
				oci_commit($con);
				echo "0**" . $qc_update_id;
			} else {
				oci_rollback($con);
				echo "5**0";
			}
		}

		disconnect($con);
		die;
	} else if ($operation == 1)   // Update Here
	{
		$con = connect();
		$already_qc=return_field_value("sum(roll_weight) as qnty", "pro_qc_result_mst", " id <> $update_id and pro_dtls_id=$hide_dtls_id and status_active=1 and is_deleted=0", "qnty") ;
		$already_roll=return_field_value("count(id) as qnty", "pro_qc_result_mst", "pro_dtls_id=$hide_dtls_id and status_active=1 and is_deleted=0", "qnty") ;

		$no_of_roll = str_replace("'", "", $no_of_roll);
		$qc_pass_qty = str_replace("'", "", $qc_pass_qty);
		$weight = str_replace("'", "", $txt_roll_weight);
		if($no_of_roll<($already_roll))
		{
			echo "20**Roll over than Production No of Roll";
			die;
		}

		if($qc_pass_qty<($already_qc+$weight))
		{
			echo "20**Weight Qnty over than Qc Pass Qty";
			die;
		}


		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$roll_maintain = str_replace("'", "", $hide_roll_maintain);
		if ($roll_maintain == 1)
		{
			$pre_count =  sql_select("select count(id) as count from pro_qc_result_mst where pro_dtls_id=$hide_dtls_id and barcode_no=$txt_barcode and id <> $update_id and status_active=1 and is_deleted=0");
			if ($pre_count[0][csf("count")] > 0)
			{
				echo "20**Barcode Number is Already Exists.";
				die;
			}
		}

		$field_array_update = "pro_dtls_id*actual_gsm*actual_dia*length_percent*width_percent*twisting_percent*roll_weight*roll_length*total_penalty_point*total_point*fabric_grade*comments*update_by*update_date";
		$data_array_update = $hide_dtls_id ."*" . $txt_gsm . "*" . $txt_dia . "*" . $txt_length_percent . "*" . $txt_width_percent . "*" . $txt_twisting_percent . "*" . $txt_roll_weight . "*" . $txt_roll_length . "*" . $total_penalty_point . "*" . $total_point . "*" . $fabric_grade . "*" . $fabric_comments . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

		$deleteDetails = execute_query("delete from  pro_qc_result_dtls where mst_id=$update_id");

		//$dtls_id = return_next_id("id", "pro_qc_result_dtls", 1);
		$field_array_dtls = "id, mst_id, defect_name, defect_count, found_in_inch, found_in_inch_point, penalty_point,department, inserted_by, insert_date";
		$field_array_dtls2 = "id, mst_id, defect_name, found_in_inch,department,form_type, inserted_by, insert_date";

		$data_array_dtls = "";
		$data_array_dtls2 = "";

		for ($i = 1; $i <= $count_tbl_length; $i++) {
			$dtls_id = return_next_id_by_sequence("PRO_QC_RESULT_DTLS_SEQ", "pro_qc_result_dtls", $con);
			$defectId = 'defectId_' . $i;
			$UpdefectId = 'UpdefectId_' . $i;
			$defectcount = 'defectcount_' . $i;
			$foundInche = 'foundInche_' . $i;
			$foundIncheVal = 'foundIncheVal_' . $i;
			$penaltycount = 'penaltycount_' . $i;
			$department = 'department_' . $i;

			if ($data_array_dtls != "") $data_array_dtls .= ",";

			$data_array_dtls .= "(" . $dtls_id . "," . $update_id . ",'" . $$defectId . "','" . $$defectcount . "','" . $$foundInche . "','" . $$foundIncheVal . "','" . $$penaltycount . "','" . $$department . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			//$dtls_id++;
		}

		for ($j = 1; $j <= $table_length2; $j++)
		{
			$dtls_id = return_next_id_by_sequence("PRO_QC_RESULT_DTLS_SEQ", "pro_qc_result_dtls", $con);
			$defectId2 = 'defectId2_' . $j;
			$UpdefectId2 = 'UpdefectId2_' . $j;
			$foundInche2 = 'foundInche2_' . $j;
			$department2 = 'department2_' . $j;
			if($$foundInche2 || $$department2)
			{
				if ($data_array_dtls2 != "") $data_array_dtls2 .= ",";
				$data_array_dtls2 .= "(" . $dtls_id . "," . $update_id . ",'" . $$defectId2 . "','" . $$foundInche2 . "','" . $$department2 . "','2'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
 				//$dtls_id++;

			}

		}


		//echo "10**insert into pro_qc_result_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;

		$rID = sql_update("pro_qc_result_mst", $field_array_update, $data_array_update, "id", $update_id, 0);
		$rID2 = sql_insert("pro_qc_result_dtls", $field_array_dtls, $data_array_dtls, 0);
		if($data_array_dtls2)
		{
			$rID3 = sql_insert("pro_qc_result_dtls", $field_array_dtls2, $data_array_dtls2, 0);
		}

		//echo "10**$rID###$rID2";die;
		$qc_update_id = str_replace("'", "", $update_id);

		if ($db_type == 0) {
			if ($rID && $deleteDetails && $rID2 ) {
				mysql_query("COMMIT");
				echo "1**" . $qc_update_id;
			} else {
				mysql_query("ROLLBACK");
				echo "6**0";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $deleteDetails && $rID2) {
				oci_commit($con);
				echo "1**" . $qc_update_id;
			} else {
				oci_rollback($con);
				echo "6**0";
			}
		}
		disconnect($con);
		die;
	}
	exit();
}

if ($action == "populate_qc_from_grey_recv2")
{
	$sql_qc = sql_select("SELECT id, pro_dtls_id, roll_maintain, barcode_no, roll_id, roll_no, qc_name, roll_width, roll_weight, roll_length, reject_qnty, qc_date, total_penalty_point, total_point, fabric_grade, comments,length_percent,width_percent,twisting_percent,actual_gsm,actual_dia from pro_qc_result_mst where id=$data");
	echo "fn_recet_details();\n";
	foreach ($sql_qc as $row) {
		echo "document.getElementById('update_id').value 				= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('txt_roll_no').value 				= '" . $row[csf("roll_no")] . "';\n";
		echo "document.getElementById('txt_roll_weight').value 			= '" . $row[csf("roll_weight")] . "';\n";
		echo "document.getElementById('txt_roll_length').value 		= '" . $row[csf("roll_length")] . "';\n";
		echo "document.getElementById('txt_length_percent').value 		= '" . $row[csf("length_percent")] . "';\n";
		echo "document.getElementById('txt_width_percent').value 		= '" . $row[csf("width_percent")] . "';\n";
		echo "document.getElementById('txt_twisting_percent').value 		= '" . $row[csf("twisting_percent")] . "';\n";
		echo "document.getElementById('txt_gsm').value 		= '" . $row[csf("actual_gsm")] . "';\n";
		echo "document.getElementById('txt_dia').value 		= '" . $row[csf("actual_dia")] . "';\n";


		$dtls_part_tbody_data = "";
		$dtls_sql = sql_select("SELECT id, defect_name, defect_count, found_in_inch, found_in_inch_point, penalty_point,department from pro_qc_result_dtls where mst_id=" . $row[csf('id')] . " and status_active=1 and is_deleted=0 and (form_type is null or form_type =0)");
		$dtls_data_arr = array();
		foreach ($dtls_sql as $dtls_row) {
			$dtls_data_arr[$dtls_row[csf("defect_name")]]["dtls_id"] = $dtls_row[csf("id")];
			$dtls_data_arr[$dtls_row[csf("defect_name")]]["defect_name"] = $dtls_row[csf("defect_name")];
			$dtls_data_arr[$dtls_row[csf("defect_name")]]["defect_count"] = $dtls_row[csf("defect_count")];
			$dtls_data_arr[$dtls_row[csf("defect_name")]]["found_in_inch"] = $dtls_row[csf("found_in_inch")];
			$dtls_data_arr[$dtls_row[csf("defect_name")]]["found_in_inch_point"] = $dtls_row[csf("found_in_inch_point")];
			$dtls_data_arr[$dtls_row[csf("defect_name")]]["penalty_point"] = $dtls_row[csf("penalty_point")];
			$dtls_data_arr[$dtls_row[csf("defect_name")]]["department"] = $dtls_row[csf("department")];
		}
		//print_r($dtls_data_arr);

		$dtls_sql2 = sql_select("SELECT id, defect_name, found_in_inch,department from pro_qc_result_dtls where mst_id=" . $row[csf('id')] . " and status_active=1 and is_deleted=0 and  form_type =2");

		$dtls_data_arr2 = array();
		foreach ($dtls_sql2 as $dtls_row)
		{
			$dtls_data_arr2[$dtls_row[csf("defect_name")]]["dtls_id"] = $dtls_row[csf("id")];
			$dtls_data_arr2[$dtls_row[csf("defect_name")]]["defect_name"] = $dtls_row[csf("defect_name")];
			$dtls_data_arr2[$dtls_row[csf("defect_name")]]["found_in_inch"] = $dtls_row[csf("found_in_inch")];
			$dtls_data_arr2[$dtls_row[csf("defect_name")]]["department"] = $dtls_row[csf("department")];
		}


		$i = 1;
		//echo "fn_recet_details();\n";
		//echo "$('#dtls_part2').find('input').not('.defectId2, .UpdefectId2,.defectShortId2,.UpdefectShortId2').val('');\n";
		foreach ($defect_arr as $defect_id => $val) {
			if ($dtls_data_arr[$defect_id]["defect_name"] > 0) {
				echo "document.getElementById('UpdefectId_$i').value 				= '" . $dtls_data_arr[$defect_id]["defect_name"] . "';\n";
				echo "document.getElementById('UpdefectShortId_$i').value 				= '" . $dtls_data_arr[$defect_id]["defect_name"] . "';\n";
				echo "document.getElementById('defectcount_$i').value 				= '" . $dtls_data_arr[$defect_id]["defect_count"] . "';\n";
				echo "document.getElementById('foundInche_$i').value 				= '" . $dtls_data_arr[$defect_id]["found_in_inch"] . "';\n";
				echo "document.getElementById('foundInchePoint_$i').value 				= '" . $dtls_data_arr[$defect_id]["found_in_inch_point"] . "';\n";
				echo "document.getElementById('penaltycount_$i').value 				= '" . $dtls_data_arr[$defect_id]["penalty_point"] . "';\n";
				echo "document.getElementById('department_$i').value 				= '" . $dtls_data_arr[$defect_id]["department"] . "';\n";
			}
			$i++;
		}
		$j=1;
		foreach ($knit_defect_array2 as $defect_id => $val) {
			if ($dtls_data_arr2[$defect_id]["defect_name"] > 0) {
				echo "document.getElementById('UpdefectId2_$j').value 				= '" . $dtls_data_arr2[$defect_id]["defect_name"] . "';\n";
				echo "document.getElementById('UpdefectShortId2_$j').value 				= '" . $dtls_data_arr2[$defect_id]["defect_name"] . "';\n";

				echo "document.getElementById('foundInche2_$j').value 				= '" . $dtls_data_arr2[$defect_id]["found_in_inch"] . "';\n";


				echo "document.getElementById('department2_$j').value 				= '" . $dtls_data_arr2[$defect_id]["department"] . "';\n";
			}
			$j++;
		}


		echo "document.getElementById('total_penalty_point').value 				= '" . $row[csf("total_penalty_point")] . "';\n";
		echo "document.getElementById('total_point').value 				= '" . $row[csf("total_point")] . "';\n";
		echo "document.getElementById('fabric_grade').value 				= '" . $row[csf("fabric_grade")] . "';\n";
		echo "document.getElementById('fabric_comments').value 				= '" . $row[csf("comments")] . "';\n";
		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_finish_defect_entry',1);\n";
		exit();
	}
}
if ($action == "show_qc_listview2")
{
	$sql_dtls = sql_select("SELECT id, pro_dtls_id, barcode_no, roll_id, roll_no, total_penalty_point, total_point, fabric_grade, comments,roll_weight from pro_qc_result_mst where pro_dtls_id=$data and status_active=1 order by roll_no asc");
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table">
		<thead>
			<tr>
				<th width="50">SL</th>
				<th width="100">Roll No</th>
				<th width="100">Penalty Point</th>
				<th width="100">Total Point</th>
				<th width="100">Fabric Grade</th>
				<th width="100">Roll Weight</th>
				<th>Comments</th>
			</tr>
		</thead>
		<tbody>
			<?
			$i = 1;
			$total_weight=0;
			foreach ($sql_dtls as $row) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>"
					onClick='get_php_form_data(<? echo $row[csf("id")]; ?>, "populate_qc_from_grey_recv2", "finish_fabric_receive_controller" );'
					style="cursor:pointer;">
					<td align="center"><? echo $i; ?></td>
					<td><? echo $row[csf("roll_no")]; ?></td>
					<td align="right"><? echo number_format($row[csf("total_penalty_point")], 2); ?></td>
					<td align="right"><? echo number_format($row[csf("total_point")], 2); ?></td>
					<td><? echo $row[csf("fabric_grade")]; ?></td>
					<td><? echo $qty_r=number_format($row[csf("roll_weight")],2); ?></td>
					<td><? echo $row[csf("comments")]; ?></td>
				</tr>
				<?
				$total_weight+=$qty_r;
				$i++;
			}
			?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="5" align="right"><strong>Total:</strong></td>
				<td colspan="2" align="left"><strong><? echo number_format($total_weight,2); ?></strong></td>
			</tr>
		</tfoot>
	</table>
	<?
	exit();
}

if($action == 'rate_info_popup')
{
	echo load_html_head_contents("System ID Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(rate)
		{
			$("#hidden_rate").val(rate);
			parent.emailwindow.hide();
		}
	</script>
</head>
<?
	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id ";


	$color_id =return_field_value("id","pro_batch_create_mst","color_name=".$fabric_color."");

	$data_array=sql_select($sql_deter);

	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				$constructionArr[$row[csf('id')]]=$constructionArr[$row[csf('id')]];
				list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
				$copmpositionArr[$row[csf('id')]]=$cps;
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				$constructionArr[$row[csf('id')]]=$row[csf('construction')];
				list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
				$copmpositionArr[$row[csf('id')]]=$cps;
			}
		}
	}

	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1", "id", "color_name" );
	$sales_order_info = sql_select("select mst_id,body_part_id,determination_id,color_id,avg_rate from fabric_sales_order_dtls where mst_id in ($order_id)  and status_active=1 and is_deleted=0");
	
	
?>
<body>
	<div align="center" style="width:470px;">
		<form name="searchsystemidfrm"  id="searchsystemidfrm">
			<fieldset style="width:470px;">
				<input type="hidden" name="hidden_rate" id="hidden_rate" class="text_boxes" value="">  
				<legend>Rates List</legend>
				<table cellpadding="0" cellspacing="0" width="450" border="1" rules="all" class="rpt_table" align="left">
					<thead>
						<th width="100">Body Part</th>
						<th width="150">Fabric Description</th>
						<th width="120">Color</th>
						<th width="80">Rate</th>
					</thead>
				</table>
				<div style="width:470px; max-height:300px; overflow-y:scroll" id="list_container" align="left"> 
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="450" id="tbl_list_search">  
						<?
						foreach ($sales_order_info as $row) 
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							$avg_rate = number_format($row[csf('avg_rate')],2,".","");
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $avg_rate; ?>')" style="cursor:pointer" >
							<td width="100"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
							<td width="150"><p><? echo $composition_arr[$row[csf('determination_id')]]; ?></p></td>
							<td width="120"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="80" align="right"><? echo $avg_rate; ?></td>
						</tr>
						<?
						}
						?>
					</tbody>
					
				</table>
				</div> 
			</fieldset>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}
if($action == "chk_if_over_production_unlimited")
{
	$variable_set_production=sql_select("select distribute_qnty, auto_update from variable_settings_production where variable_list =51 and company_name=$data and item_category_id=2");

	if($variable_set_production[0][csf('auto_update')] == 3){
		$is_over_receive_unlimited = 1;
	}else{
		$is_over_receive_unlimited = 0;
	}
	echo $is_over_receive_unlimited;
	exit();
}

/*if($action=="yarn_lot_popup_refer")
{
	echo load_html_head_contents("Yarn Lot Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$save_data=explode(",",$save_data);
	foreach($save_data as $data_arr)
	{
		$data_arr=explode("**",$data_arr);
		$po_arr[]=$data_arr[0];
	}
	$po_id_all=implode(",",$po_arr);
	if($po_id_all=="") $po_id_all=0;
	$row_cond="";
	$row_limit="";
	if($db_type==0) {$txt_receive_date=change_date_format($txt_receive_date,'yyyy-mm-dd','-'); $row_limit=" limit 1";}
	else { $txt_receive_date=change_date_format($txt_receive_date,'yyyy-mm-dd','-',1); $row_cond=" and rownum=1";}
	?>
	<script>

		function fnc_process_cost()
		{
			var process_string="";
			var knitting_rate_string="";
			var all_deleted_id='';
			var receive_qty=<? echo $txt_receive_qnty; ?>;
			var rate_with_knitting_charge=0;
			var total_amount=0;
			var knitting_charge=$("#txt_knitting_charge").val()*1;
			var total_used_qty=0;
			$("#tbl_lot_list").find('tr').each(function()
			{
				var txt_used=$(this).find('input[name="txt_used_qty[]"]').val()*1;
				if(txt_used>0)
				{
					total_used_qty=total_used_qty+txt_used;
					var txt_prod_id=$(this).find('input[name="txt_prod_id[]"]').val();
					var txt_cons_rate=$(this).find('input[name="txt_cons_rate[]"]').val();
					var txt_net_used=$(this).find('input[name="txt_net_used[]"]').val();
					var txt_material_update_id=$(this).find('input[name="update_material_id[]"]').val();

					if(txt_net_used==0) txt_net_used=txt_used;

					var txt_grey_cost=txt_cons_rate*txt_used;
					total_amount+=txt_grey_cost;
					var grey_rate=txt_grey_cost/txt_net_used;
					process_string=txt_prod_id+"*"+txt_used+"*"+txt_cons_rate+"*"+txt_material_update_id;
					knitting_charge=(knitting_charge*txt_used)/txt_net_used;
				}
				else
				{
					if($(this).find('input[name="update_material_id[]"]').val()>0)
					{
						all_deleted_id=$(this).find('input[name="update_material_id[]"]').val();
					}
				}
			});

			var total_rate=total_amount/receive_qty;
			if( total_used_qty<receive_qty ){
				alert("Total Used Qty Must be Greater or Equal to Receive Qty.");
				return;
			}

			knitting_rate_string=knitting_charge+"*"+total_rate+"*"+all_deleted_id;
			$('#hidden_process_string').val( process_string );
			$('#hidden_knitting_rate').val( knitting_rate_string );
			parent.emailwindow.hide();
		}

		function fnc_ommit_data(id)
		{
			var tr_length=$("#txt_lot_row_id").val();
			for(var j=1;j<tr_length; j++)
			{
				if(j!=id) $("#txt_used_qty_"+j).val('');
			}
		}

	</script>
	<input type="hidden" name="hidden_process_string" id="hidden_process_string" value="" />
	<input type="hidden" name="hidden_knitting_rate" id="hidden_knitting_rate" value="" />
	<div>
		<?php
		$color_id 		= return_field_value("id","lib_color", "color_name='$name_color'");
		$yarn_count_arr = return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
		$brand_arr 		= return_library_array( "select id,brand_name  from  lib_brand",'id','brand_name ');
		if($recieve_basis==9 || $recieve_basis==10)
		{
			// GET PROCESS IDS FROM FINISH FABRIC PRODUCTION
			if($recieve_basis==10){
				$grey_sys_id = sql_select("select LISTAGG(cast(grey_sys_id as varchar2(4000)),',') WITHIN GROUP (ORDER BY grey_sys_id) as grey_sys_id from PRO_GREY_PROD_DELIVERY_DTLS where batch_id=$txt_batch_id");
				$booking_id = $grey_sys_id[0][csf("grey_sys_id")];
			}

			$process_id_result = sql_select("select process_id,booking_id,booking_no from pro_finish_fabric_rcv_dtls where mst_id=$booking_id and fabric_description_id=$fabric_description_id and color_id=$color_id and status_active=1");
			foreach ($process_id_result as $process) {
				$process_ids .= $process[csf("process_id")] . ",";
				$sbooking_id = $process[csf("booking_id")];
			}

			$process_ids = rtrim($process_ids,", ");

			if($sbooking_id!="")
			{
				// SERVICE BOOKING CHARGE
				$service_booking_charge = sql_select("select a.booking_no,a.material_id, b.gmts_color_id,d.body_part_id, d.lib_yarn_count_deter_id,a.currency_id,a.exchange_rate,sum(b.amount)/sum(b.wo_qnty) as rate from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_fab_conv_cost_dtls c,wo_pre_cost_fabric_cost_dtls d where a.id=$sbooking_id and a.status_active=1 and a.is_deleted=0 and a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=d.id and d.lib_yarn_count_deter_id=$fabric_description_id and b.gmts_color_id=$color_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.booking_type=3 group by a.booking_no,a.material_id, b.gmts_color_id,d.body_part_id, d.lib_yarn_count_deter_id,a.currency_id,a.exchange_rate");
				foreach ($service_booking_charge as $charge_value) {
					$service_charge+=$charge_value[csf('rate')]*$charge_value[csf('exchange_rate')];
					$material_id = $charge_value[csf('material_id')];
				}

				// WITH MATERIAL OR WITHOUT MATERIAL
				if($material_id == 1){
					$dyeing_charge = $service_charge;
				}else{
					// GET RATE FROM ISSUE BY BATCH
					$dyes_chemical_issue=sql_select("select sum(b.cons_amount) cons_amount from inv_issue_master a, inv_transaction b where a.id=b.mst_id and b.transaction_type=2 and a.entry_form=5 and b.item_category in (5,6,7,23) and a.batch_no='$txt_batch_id'");
					$dyeing_charge = $service_charge+$process_overhead_rate+ ($dyes_chemical_issue[0][csf("cons_amount")]/$txt_receive_qnty);
				}

			}
			else
			{
				// GET PROCESS IDS FROM DATABASE BY ENTRY FORM
				$get_all_inserted_process_ids=sql_select("select process_id from pro_fab_subprocess where entry_form in(30,31,32,33,34,35,47,48) and batch_id=$txt_batch_id and status_active=1 and is_deleted=0");
				foreach ($get_all_inserted_process_ids as $process) {
					$process_ids .= ",".$process[csf("process_id")];
				}
					//30 => 'Slitting/Squeezing', 31 => 'Drying', 32 => 'Heat Setting', 33 => 'Compacting', 34 => 'Special Finish', 35 => 'Dyeing Production' 
					//47 => "Singeing", 48 => "Stentering"
				$process_ids = implode(",",array_unique(explode(",",rtrim($process_ids,", "))));

				// GET PROCESS WISE OVERHEAD FROM LIBRARY
				$get_process_overhead_from_library = sql_select("select rate,process_id from lib_finish_process_charge where process_id in($process_ids) and cons_comp_id=$fabric_description_id and status_active=1");
				$process_overhead_rate = 0;
				foreach ($get_process_overhead_from_library as $process_rate) {
					$process_overhead_rate += $process_rate[csf("rate")];
					$process_name_arr[$conversion_cost_head_array[$process_rate[csf("process_id")]]]+=$process_rate[csf("rate")];
				}
				// GET RATE FROM ISSUE BY BATCH
				$dyes_chemical_issue=sql_select("select sum(b.cons_amount) cons_amount from inv_issue_master a, inv_transaction b where a.id=b.mst_id and b.transaction_type=2 and a.entry_form=5 and b.item_category in (5,6,7,23) and a.batch_no='$txt_batch_id'");
				$dyeing_charge = $process_overhead_rate+ ($dyes_chemical_issue[0][csf("cons_amount")]/$txt_receive_qnty);

				foreach ($process_name_arr as $pname => $prate) {
					$process_info_str .= $pname ."=".number_format($prate,2).", ";
				}
				$process_info_str=chop($process_info_str,",");
				if($process_info_str!="")
				{
					if(($dyes_chemical_issue[0][csf("cons_amount")]/$txt_receive_qnty)>0)
					{
						$process_info_str .=", Dyes and Chemical= ".number_format(($dyes_chemical_issue[0][csf("cons_amount")]/$txt_receive_qnty),2);
					}
					
				}
				

				$dyeing_charge_info_title =$process_info_str;
			}
		}
		else
		{
			if($recieve_basis==11)
			{
				if($cbo_currency==1)
				{
					$dyeing_charge = $kitting_charge_2nd;
				}
				else
				{
					$dyeing_charge = $kitting_charge_2nd*$txt_exchange_rate;
				}

				$dyeing_charge_info_title="Service Booking Charge = ". $dyeing_charge;
				// need to address disscuss with CTO
			}
			else
			{
				$conversition_cost_sql=sql_select("select b.process, a.currency_id,a.exchange_rate,sum(b.amount)/sum(b.wo_qnty) as rate from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fab_conv_cost_dtls c,wo_pre_cost_fabric_cost_dtls d where a.booking_no=b.booking_no and a.id=".$booking_id." and b.fabric_color_id=".$color_id." and b.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=d.id and c.job_no=d.job_no and b.process in (31,25,26,32,33,34,35,36,37,38,39,40,60,61,62,64,67,68,69,70,71,72,73,74,75,77,78,79,80,81,82,83,84,85,86,87,88,89,92,93,94,100,125,127,128,129,132,133,134,135,136,137,138,63,65,66,76,90,91,156) and lib_yarn_count_deter_id=".$fabric_description_id." group by a.currency_id,a.exchange_rate,b.process ");
				$dyeing_charge=0;
				foreach($conversition_cost_sql as $charge_value)
				{
					$process_name_arr[$conversion_cost_head_array[$charge_value[csf("process")]]]+=$charge_value[csf('rate')]*$charge_value[csf('exchange_rate')];
					$dyeing_charge+=$charge_value[csf('rate')]*$charge_value[csf('exchange_rate')];
				}
				foreach ($process_name_arr as $pname => $prate) 
				{
					$process_info_str .= $pname ."=".number_format($prate,2).", ";
				}
				$process_info_str=chop($process_info_str,",");
				$dyeing_charge_info_title =$process_info_str;
			}

		}

		$processloss_sql=sql_select("select sum(process_loss) as process_loss from conversion_process_loss where  mst_id=".$fabric_description_id." and process_id in(31,25,26,32,33,34,35,36,37,38,39,40,60,61,62,64,67,68,69,70,71,72,73,74,75,77,78,79,80,81,82,83,84,85,86,87,88,89,92,93,94,100,125, 127,128, 129,132,133,134,135,136,137,138,63,31,30,65,66,76,90,91)");

		$process_loss=$processloss_sql[0][csf('process_loss')];
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id order by b.id asc";
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
					$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
			}
		}
		?>
		<table cellspacing="0" cellpadding="0" border="0" rules="all" width="840" class="" align="center">
			<tr>
				<td colspan="5" align="center" style="font-size:16px">
					<strong>Dyeing process loss <?php echo $process_loss." %";?></strong>
				</td>
				<td colspan="5" align="center" style="font-size:16px" title="<? echo $dyeing_charge_info_title; ?>">
					<strong>Dyeing Charge <?php echo number_format($dyeing_charge,2)."Tk./Kg";?></strong>
				</td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="60">Prod Id</th>
				<th width="80">Lot</th>
				<th width="250">Fabric Description</th>
				<th width="80">Brand</th>
				<th width="100">Avg Grey Fabric Rate /Kg (Tk.) </th>
				<th width="70">Net Qty</th>
				<th >Used Qty</th>
			</thead>
		</table>
		<div style="width:840px; max-height:280px; overflow-y:scroll">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table" id="tbl_lot_list">
				<?php
				$i=1;
				$sql_cond="";
				if($serch_count_arr>0) $sql_cond=" and c.yarn_count_id in (".implode(",",$serch_count_arr).") ";
				if($serch_composition_arr>0) $sql_cond.=" and c.yarn_comp_type1st in (".implode(",",$serch_composition_arr).") ";
				if($serch_type_arr>0) $sql_cond.=" and c.yarn_type in (".implode(",",$serch_type_arr).") ";

				$grey_fabric_in_roll_lvl =return_field_value("fabric_roll_level","variable_settings_production","company_name='$cbo_company_id' and item_category_id=13 and variable_list=3 and is_deleted=0 and status_active=1");

				if($grey_fabric_in_roll_lvl==1) $grey_fabric_in_roll_lvl=$grey_fabric_in_roll_lvl; else $grey_fabric_in_roll_lvl=0;

				if($recieve_basis==11 && $booking_without_order==1)
				{
					

					if($grey_fabric_in_roll_lvl==1)
					{
						$sql="select c.detarmination_id ,c.id,c.lot,c.brand, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type,sum(d.qnty) issue_qty,sum(d.amount) cons_amount
						from inv_issue_master a, inv_grey_fabric_issue_dtls b, product_details_master c, pro_roll_details d
						where a.id=b.mst_id and b.prod_id=c.id and b.id=d.dtls_id and a.id=d.mst_id and a.entry_form in(61) and b.trans_id>0 and a.item_category=13 and d.entry_form in (61) and d.po_breakdown_id in(".$booking_id.") and d.booking_without_order=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.detarmination_id=".$fabric_description_id."  group by c.id,c.lot,c.brand,c.supplier_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type,c.detarmination_id";

					}
					else
					{
						$sql="select c.detarmination_id ,c.id,c.lot,c.brand, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type,sum(b.issue_qnty) as issue_qty, sum(b.amount) as cons_amount from inv_issue_master a, inv_grey_fabric_issue_dtls b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id  and a.entry_form in(16) and b.trans_id>0 and a.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.detarmination_id=".$fabric_description_id." and a.booking_no ='$booking_no' group by c.id,c.lot,c.brand,c.supplier_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type,c.detarmination_id";

					}

				}
				else
				{
					$sales_cond = ($is_sales==1)?" and d.is_sales=1":"";
					$sql="SELECT c.detarmination_id ,c.id,c.lot,c.brand, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type,sum(b.cons_quantity) issue_qty,sum(b.cons_amount) cons_amount from  order_wise_pro_details d, product_details_master c, inv_transaction b , inv_issue_master a where    d.po_breakdown_id in(".$po_id_all.") and  d.trans_type = 2 AND d.entry_form IN (16, 61)  and d.prod_id=c.id  and c.detarmination_id=".$fabric_description_id."   AND  b.mst_id  = a.id  AND b.id = d.trans_id  AND b.prod_id = d.prod_id     and a.entry_form in (16,61)   and a.item_category=13 and b.id=d.trans_id      and a.status_active=1    and b.item_category=13 and b.status_active=1   and c.detarmination_id=".$fabric_description_id." $sales_cond group by c.id,c.lot,c.brand,c.supplier_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type,c.detarmination_id";
				}
				//echo $sql;
				if($update_dtls_id!="")
				{
					$update_sql=sql_select("select id,prod_id,used_qty,rate,amount from pro_material_used_dtls where mst_id=$update_id and dtls_id =$update_dtls_id");
					$update_data_arr=array();
					foreach($update_sql as $val)
					{
						$update_data_arr[$val[csf('prod_id')]]['prod_id']=$val[csf('prod_id')];
						$update_data_arr[$val[csf('prod_id')]]['id']=$val[csf('id')];
						$update_data_arr[$val[csf('prod_id')]]['used_qty']=$val[csf('used_qty')];
						$update_data_arr[$val[csf('prod_id')]]['rate']=$val[csf('rate')];
						$update_data_arr[$val[csf('prod_id')]]['amount']=$val[csf('amount')];
						$check_arr[]=$val[csf('prod_id')];
					}
				}
					//echo $sql;
				$nameArray=sql_select($sql);
				foreach ($nameArray as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					$composition_string = $composition_arr[$row[csf('detarmination_id')]];
					$net_used=$txt_receive_qnty;
					$process_loss_used=($net_used*100)/(100-$process_loss);
					//echo $process_loss_used;
					if(in_array($row[csf("id")], $check_arr))
					{
						?>
						<tr bgcolor="#FFFF99" style="text-decoration:none;" id="search<? echo $i;?>">
							<td width="30" align="center">
								<?php echo "$i"; ?>
								<input type="hidden" name="update_material_id[]" id="update_material_id<?php echo $i; ?>" value="<?php echo $update_data_arr[$row[csf('id')]]['id']; ?>"/>
								<input type="hidden" name="txt_net_used[]" id="txt_net_used<?php echo $i; ?>" value="<?php  echo $net_used; ?>"/>
								<input type="hidden" name="txt_cons_rate[]" id="txt_cons_rate<?php echo $i; ?>" value="<?php echo $update_data_arr[$row[csf('id')]]['rate']; ?>"/>
								<input type="hidden" name="txt_prod_id[]" id="txt_prod_id<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>" />
								<input type="hidden" name="txt_yarn_count_id[]" id="txt_yarn_count_id<?php echo $i; ?>" value="<?php echo $row[csf('yarn_count_id')]; ?>" />
								<input type="hidden" name="txt_brand[]" id="txt_brand<?php echo $i; ?>" value="<?php echo $brand_arr[$row[csf('brand')]]; ?>" />	         <input type="hidden" name="txt_lot[]" id="txt_lot<?php echo $i; ?>" value="<?php echo $row[csf('lot')]; ?>" />
							</td>
							<td width="60"><p><?php  echo $row[csf('id')];?></p></td>
							<td width="80"><p><?php  echo $row[csf('lot')];?></p></td>
							<td width="250"><p><?php echo $composition_string;?></p></td>

							<td width="80"><p><?php  echo $brand_arr[$row[csf('brand')]];?></p></td>
							<td width="100" align="right"><p><?php  echo $update_data_arr[$row[csf('id')]]['rate'];?></p></td>
							<td width="70" align="right"><p><?php   echo $net_used;?></p></td>
							<td><input type="text" id="txt_used_qty_<? echo $i;  ?>" name="txt_used_qty[]"  style="width:80px" class="text_boxes_numeric" value="<? if($process_loss_used==0) { $process_loss_used=$update_data_arr[$row[csf('id')]]['used_qty'];} echo number_format($process_loss_used,2,".",""); ?>" placeholder="<?  echo number_format($process_loss_used,2,".",""); ?>" onKeyUp="fnc_ommit_data(<? echo $i;  ?>)"/></td>
						</tr>
						<?
						$i++;
					}
					else
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;" id="search<? echo $i;?>">
							<td width="30" align="center"><?php echo "$i"; ?>
								<input type="hidden" name="update_material_id[]" id="update_material_id<?php echo $i; ?>" value="0"/>
								<input type="hidden" name="txt_net_used[]" id="txt_net_used<?php echo $i; ?>" value="<?php  echo $net_used; ?>"/>
								<input type="hidden" name="txt_cons_rate[]" id="txt_cons_rate<?php echo $i; ?>" value="<?php echo $row[csf('cons_amount')]/$row[csf('issue_qty')]; ?>"/>
								<input type="hidden" name="txt_prod_id[]" id="txt_prod_id<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>" />
								<input type="hidden" name="txt_yarn_count_id[]" id="txt_yarn_count_id<?php echo $i; ?>" value="<?php echo $row[csf('yarn_count_id')]; ?>" />
								<input type="hidden" name="txt_brand[]" id="txt_brand<?php echo $i; ?>" value="<?php echo $brand_arr[$row[csf('brand')]]; ?>" />
								<input type="hidden" name="txt_lot[]" id="txt_lot<?php echo $i; ?>" value="<?php echo $row[csf('lot')]; ?>" />
							</td>
							<td width="60"><p><?php echo $row[csf('id')];?></p></td>
							<td width="80"><p><?php echo $row[csf('lot')];?></p></td>
							<td width="250"><p><?php echo $composition_string;?></p></td>
							<td width="80"><p><?php echo $brand_arr[$row[csf('brand')]];?></p></td>
							<td width="100" align="right"><p><?php echo $row[csf('cons_amount')]/$row[csf('issue_qty')];?></p></td>
							<td width="70" align="right"><p><?php echo $net_used;?></p></td>
							<td><input type="text" id="txt_used_qty_<? echo $i; ?>" name="txt_used_qty[]"  style="width:80px" class="text_boxes_numeric" value="<? if($i==1 && $update_dtls_id=="") echo number_format($process_loss_used,2,".",""); ?>" placeholder="<?  echo number_format($process_loss_used,2,".",""); ?>" onKeyUp="fnc_ommit_data(<? echo $i;  ?>)"/></td>
						</tr>
						<?
						$i++;
					}
				}
				?>
				<input type="hidden" name="txt_lot_row_id" id="txt_lot_row_id" value="<?php echo $i; ?>"/>
			</table>
		</div>
		<table width="840" cellspacing="0" cellpadding="0" border="1" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:100%; float:left" align="center">
							<input type="hidden" name="txt_knitting_charge" id="txt_knitting_charge" value="<?php echo $dyeing_charge; ?>"/>
							<input type="button" name="close" onClick="fnc_process_cost();" class="formbutton" value="Close" style="width:100px" />
						</div>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<?
	exit();
}*/

if ($action == "check_conversion_rate") 
{
	$data = explode("**", $data);
	if ($db_type == 0) {
		$conversion_date = change_date_format($data[1], "Y-m-d", "-", 1);
	} else {
		$conversion_date = change_date_format($data[1], "d-M-y", "-", 1);
	}
	$exchange_rate = set_conversion_rate($data[0], $conversion_date);
	echo $exchange_rate;
	exit();
}

if($action=="yarn_lot_popup")
{
	echo load_html_head_contents("Yarn Lot Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	$save_data=explode("!!",$save_data);
	foreach($save_data as $data_arr)
	{
		$data_arr=explode("**",$data_arr);
		$po_arr[]=$data_arr[0];
	}
	$po_id_all=implode(",",$po_arr);
	if($po_id_all=="") $po_id_all=0;
	$row_cond="";
	$row_limit="";
	if($db_type==0) {$txt_receive_date=change_date_format($txt_receive_date,'yyyy-mm-dd','-'); $row_limit=" limit 1";}
	else { $txt_receive_date=change_date_format($txt_receive_date,'yyyy-mm-dd','-',1); $row_cond=" and rownum=1";}
	?>
	<script>

		function fnc_process_cost()
		{
			var process_string="";
			var knitting_rate_string="";
			var all_deleted_id='';
			var receive_qty=<? echo $txt_receive_qnty; ?>;
			var hidden_grey_used_po='<? echo $hidden_grey_used_po; ?>';
			var rate_with_knitting_charge=0;
			var total_amount=0;
			var knitting_charge=$("#txt_knitting_charge").val()*1;
			var hdn_batch_dtls_qnty=$("#hdn_batch_dtls_qnty").val()*1;  
			var hdn_pre_grey_used_qnty=$("#hdn_pre_grey_used_qnty").val()*1;  
			var total_used_qty=0;
			$("#tbl_lot_list").find('tr').each(function()
			{
				var txt_used=$(this).find('input[name="txt_used_qty[]"]').val()*1;
				if(txt_used>0)
				{
					total_used_qty=total_used_qty+txt_used;
					var txt_prod_id=$(this).find('input[name="txt_prod_id[]"]').val();
					var txt_cons_rate=$(this).find('input[name="txt_cons_rate[]"]').val();
					var txt_net_used=$(this).find('input[name="txt_net_used[]"]').val();
					var txt_material_update_id=$(this).find('input[name="update_material_id[]"]').val();

					if(txt_net_used==0) txt_net_used=txt_used;

					var txt_grey_cost=txt_cons_rate*txt_used;
					total_amount+=txt_grey_cost;
					var grey_rate=txt_grey_cost/txt_net_used;
					process_string=txt_prod_id+"*"+txt_used+"*"+txt_cons_rate+"*"+txt_material_update_id;
					knitting_charge=(knitting_charge*txt_used)/txt_net_used;
				}
				else
				{
					if($(this).find('input[name="update_material_id[]"]').val()>0)
					{
						all_deleted_id=$(this).find('input[name="update_material_id[]"]').val();
					}
				}
			});

			var total_rate=total_amount/receive_qty;

			if(hdn_pre_grey_used_qnty + total_used_qty > hdn_batch_dtls_qnty)
			{
				alert("Total Used Qnty Can not greater than batch Qnty.\nBatch Qnty: "+hdn_batch_dtls_qnty+ '\nprevious grey used: '+ hdn_pre_grey_used_qnty+'\ncurrent using: '+total_used_qty);
				return;
			}
			/*if( total_used_qty<receive_qty ){
				alert("Total Used Qty Must be Greater or Equal to Receive Qty.");
				return;
			}*/

			if( total_used_qty != hidden_grey_used_po ){
				alert("Total Grey used is not equal to PO wise grey used qnty.\nPO wise gery qnty : "+hidden_grey_used_po);
				$("#txt_used_qty_1").focus();
				return;
			}

			knitting_rate_string=knitting_charge+"*"+total_rate+"*"+all_deleted_id;
			$('#hidden_process_string').val( process_string );
			$('#hidden_knitting_rate').val( knitting_rate_string );
			parent.emailwindow.hide();
		}

		function fnc_ommit_data(id)
		{
			var tr_length=$("#txt_lot_row_id").val();
			for(var j=1;j<tr_length; j++)
			{
				if(j!=id) $("#txt_used_qty_"+j).val('');
			}
		}
	</script>
	<input type="hidden" name="hidden_process_string" id="hidden_process_string" value="" />
	<input type="hidden" name="hidden_knitting_rate" id="hidden_knitting_rate" value="" />
	<div>
		<?php
		$color_id 		= return_field_value("id","lib_color", "color_name='$name_color'");
		$yarn_count_arr = return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
		$brand_arr 		= return_library_array( "select id,brand_name  from  lib_brand",'id','brand_name ');
		//$batch_weight 	= return_field_value("batch_weight", "pro_batch_create_mst", "id='$txt_batch_id'", "batch_weight");
		
		$dia_width_cond="";
		if(str_replace("'","",$txt_dia_width)=="")
		{
			if($db_type==0)
			{
				$dia_width_cond = " and c.dia_width = '$txt_dia_width'"; 
				$dia_width_cond_2 = " and b.width='$txt_dia_width'"; 
			}
			else
			{
				$dia_width_cond = " and c.dia_width is null";
				$dia_width_cond_2 = " and b.width is null";
			}
		}
		else
		{
			$dia_width_cond = " and c.dia_width = '$txt_dia_width'";
			$dia_width_cond_2 = " and b.width='$txt_dia_width'"; 
		}

		$batch_sql = sql_select("select a.batch_weight, sum(b.batch_qnty) as batch_dtls_qnty from pro_batch_create_mst a, pro_batch_create_dtls b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.id=$txt_batch_id and c.detarmination_id=$fabric_description_id and c.gsm=$txt_gsm $dia_width_cond and b.status_active=1 and b.status_active=1 group by a.batch_weight");
		// and c.dia_width='$txt_dia_width'
		foreach ($batch_sql as $val) 
		{
			$batch_weight = $val[csf("batch_weight")];
			$batch_dtls_qnty = $val[csf("batch_dtls_qnty")];
		}

		if($update_dtls_id) 
		{
			$up_dtls_cond = " and b.id <> $update_dtls_id";
		} 
		else 
		{
			$up_dtls_cond = "";
		}
		$pre_grey_used_sql = sql_select("select b.grey_used_qty from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id = b.mst_id and a.entry_form=7 and b.status_active =1 and b.is_deleted=0 and b.batch_id =$txt_batch_id and b.fabric_description_id=$fabric_description_id and b.gsm=$txt_gsm $dia_width_cond_2 $up_dtls_cond");
		// and b.width='$txt_dia_width'
		foreach ($pre_grey_used_sql as $val) 
		{
			$pre_grey_used_qnty += $val[csf("grey_used_qty")];
		}

		/*$process_id_result = sql_select("select process_id,booking_id,booking_no from pro_finish_fabric_rcv_dtls where mst_id=$booking_id and fabric_description_id=$fabric_description_id and color_id=$color_id and status_active=1");
		foreach ($process_id_result as $process) {
			$process_ids .= $process[csf("process_id")] . ",";
			$sbooking_id = $process[csf("booking_id")];
		}*/

		$process_ids = str_replace("'", "", $txt_process_id); //rtrim($process_ids,", ");

		if($booking_id!="")
		{
			// SERVICE BOOKING CHARGE
			$service_booking_charge = sql_select("select a.booking_no,a.material_id, b.gmts_color_id,d.body_part_id, d.lib_yarn_count_deter_id,a.currency_id,a.exchange_rate,sum(b.amount)/sum(b.wo_qnty) as rate from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_fab_conv_cost_dtls c,wo_pre_cost_fabric_cost_dtls d where a.id=$booking_id and a.status_active=1 and a.is_deleted=0 and a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=d.id and d.lib_yarn_count_deter_id=$fabric_description_id and b.gmts_color_id=$color_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.booking_type=3 group by a.booking_no,a.material_id, b.gmts_color_id,d.body_part_id, d.lib_yarn_count_deter_id,a.currency_id,a.exchange_rate");
			foreach ($service_booking_charge as $charge_value) {
				$service_charge+=$charge_value[csf('rate')]*$charge_value[csf('exchange_rate')];
				$material_id = $charge_value[csf('material_id')];
			}

			// WITH MATERIAL OR WITHOUT MATERIAL
			if($material_id == 1){
				$dyeing_charge = $service_charge;
			}else{
				// GET RATE FROM ISSUE BY BATCH
				$dyes_chemical_issue=sql_select("select sum(b.cons_amount) cons_amount from inv_issue_master a, inv_transaction b where a.id=b.mst_id and b.transaction_type=2 and a.entry_form=5 and b.item_category in (5,6,7,23) and a.batch_no='$txt_batch_id'");
				$dyeing_charge = $service_charge+$process_overhead_rate+ ($dyes_chemical_issue[0][csf("cons_amount")]/$batch_weight);
			}
		}
		else
		{
			// GET PROCESS IDS FROM DATABASE BY ENTRY FORM
			$get_all_inserted_process_ids=sql_select("select process_id from pro_fab_subprocess where entry_form in(30,31,32,33,34,35,47,48) and batch_id=$txt_batch_id and status_active=1 and is_deleted=0");

			foreach ($get_all_inserted_process_ids as $process)
			{
				$process_ids_arr[$process[csf("process_id")]] = $process[csf("process_id")];
			}
			
			//30 => 'Slitting/Squeezing', 31 => 'Drying', 32 => 'Heat Setting', 33 => 'Compacting', 34 => 'Special Finish', 35 => 'Dyeing Production' 47 => "Singeing", 48 => "Stentering"
			
			//check library dyeing charge source
			$charge_source_sql=sql_select("select distribute_qnty from variable_settings_production where variable_list =57 and status_active =1 and company_name = $cbo_company_id");
			if($charge_source_sql[0][csf("distribute_qnty")] == 2)
			{
				$dye_charge_source = $charge_source_sql[0][csf("distribute_qnty")];
			}
			else
			{
				$dye_charge_source =1;
			}

			if($dye_charge_source==1)
			{
				$process_ids = implode(",",array_filter(array_unique($process_ids_arr)));
				// GET PROCESS WISE OVERHEAD FROM LIBRARY
				$get_process_overhead_from_library = sql_select("select rate,process_id from lib_finish_process_charge where process_id in($process_ids) and cons_comp_id=$fabric_description_id and status_active=1");
				$process_overhead_rate = 0;
				foreach ($get_process_overhead_from_library as $process_rate) {
					$process_overhead_rate += $process_rate[csf("rate")];
					$process_name_arr[$conversion_cost_head_array[$process_rate[csf("process_id")]]]+=$process_rate[csf("rate")];
				}

				// GET RATE FROM ISSUE BY BATCH
				$dyes_chemical_issue=sql_select("select sum(b.cons_amount) cons_amount from inv_issue_master a, inv_transaction b where a.id=b.mst_id and b.transaction_type=2 and a.entry_form=5 and b.item_category in (5,6,7,23) and a.batch_no='$txt_batch_id'");
				//echo $process_overhead_rate."+".($dyes_chemical_issue[0][csf("cons_amount")]/$batch_weight);die;
				$dyeing_charge = $process_overhead_rate+ ($dyes_chemical_issue[0][csf("cons_amount")]/$batch_weight);

				if(($dyes_chemical_issue[0][csf("cons_amount")]/$batch_weight)>0)
				{
					$process_info_str .="Dyes and Chemical= ".number_format(($dyes_chemical_issue[0][csf("cons_amount")]/$batch_weight),2).", ";
				}
			}
			else if($dye_charge_source==2)
			{
				//Adding Dyes and Chemical cost process from budget if source variable settings is from budget
				$process_ids_arr[101]=101;
				$process_ids = implode(",",array_filter(array_unique($process_ids_arr)));
				//Get Exchange rate from library with order's currency id
				$currency_sql=sql_select("select b.id,a.currency_id from wo_po_details_master a, wo_po_break_down b where a.job_no = b.job_no_mst  and b.id in ($all_po_id) and b.status_active=1 and b.is_deleted=0");
				foreach ($currency_sql as $val) 
				{
					if ($db_type == 0) {
						$conversion_date = change_date_format($txt_production_date, "Y-m-d", "-", 1);
					} else {
						$conversion_date = change_date_format($txt_production_date, "d-M-y", "-", 1);
					}
					$exchange_rate = set_conversion_rate($val[csf("currency_id")], $conversion_date);
					$po_wise_exchange_arr[$val[csf("id")]] =  $exchange_rate;
				}
				//$process_ids

				$sql_po="SELECT a.buyer_name,b.id,b.job_no_mst,b.shipment_date,b.po_quantity,b.pub_shipment_date,b.grouping as ref_no,c.color_number_id as color_id, d.lib_yarn_count_deter_id as deter_id, f.cons_process, f.charge_unit, f.color_break_down from wo_po_break_down b, wo_po_details_master a, wo_po_color_size_breakdown c, wo_pre_cost_fabric_cost_dtls d, wo_pre_cos_fab_co_avg_con_dtls e, wo_pre_cost_fab_conv_cost_dtls f where  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description and c.id=e.color_size_table_id and e.cons !=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1  and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 and b.id in ($all_po_id) and c.color_number_id='$color_id' and d.lib_yarn_count_deter_id=$fabric_description_id and f.cons_process in ($process_ids) group by a.buyer_name,b.id,b.job_no_mst,b.shipment_date,b.po_quantity,b.pub_shipment_date,b.grouping,c.color_number_id, d.lib_yarn_count_deter_id, f.cons_process, f.charge_unit, f.color_break_down  order by b.id";
				$sql_po_result = sql_select($sql_po);
				$process_overhead_rate = 0;
				foreach ($sql_po_result as $val) 
				{
					$color_break_down=$val[csf('color_break_down')];
					$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
					$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
					if($val[csf('cons_process')]==31 && $color_break_down!='')
					{
						//$po_color_fab_array[$val[csf('id')]][$val[csf('color_id')]][$val[csf('deter_id')]][$val[csf('cons_process')]]['color_break_down'] = $color_break_down;
						$arr_1=explode("__",$color_break_down);
						for($ci=0;$ci<count($arr_1);$ci++)
						{
							$arr_2=explode("_",$arr_1[$ci]);
							if($arr_2[0] ==$color_id)
							{
								$process_overhead_rate += $arr_2[1]*$po_wise_exchange_arr[$val[csf("id")]];
								$process_name_arr[$conversion_cost_head_array[$val[csf('cons_process')]]]+=$arr_2[1]*$po_wise_exchange_arr[$val[csf("id")]];
							}
						}
					}
					else
					{
						$process_overhead_rate += $val[csf('charge_unit')]*$po_wise_exchange_arr[$val[csf("id")]];
						$process_name_arr[$conversion_cost_head_array[$val[csf('cons_process')]]]+=$val[csf('charge_unit')]*$po_wise_exchange_arr[$val[csf("id")]];
					}
				}

				$dyeing_charge = $process_overhead_rate;
			}

			foreach ($process_name_arr as $pname => $prate) {
				$process_info_str .= $pname ."=".number_format($prate,2).", ";
			}
			$process_info_str=chop($process_info_str,", ");

			$dyeing_charge_info_title =$process_info_str;
		}

		$processloss_sql=sql_select("select sum(process_loss) as process_loss from conversion_process_loss where  mst_id=".$fabric_description_id." and process_id in(31,25,26,32,33,34,35,36,37,38,39,40,60,61,62,64,67,68,69,70,71,72,73,74,75,77,78,79,80,81,82,83,84,85,86,87,88,89,92,93,94,100,125, 127,128, 129,132,133,134,135,136,137,138,63,31,30,65,66,76,90,91)");
		$process_loss=$processloss_sql[0][csf('process_loss')];
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id order by b.id asc";
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
					$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
			}
		}
		?>
		<table cellspacing="0" cellpadding="0" border="0" rules="all" width="840" class="" align="center">
			<tr>
				<td colspan="5" align="center" style="font-size:16px">
					<strong>Dyeing process loss <?php echo $process_loss." %";?></strong>
					<input type="hidden" name="hdn_batch_dtls_qnty" id="hdn_batch_dtls_qnty" value="<?php echo $batch_dtls_qnty; ?>"/>
					<input type="hidden" name="hdn_pre_grey_used_qnty" id="hdn_pre_grey_used_qnty" value="<?php echo $pre_grey_used_qnty; ?>"/>
				</td>
				<td colspan="5" align="center" style="font-size:16px" title="<? echo $dyeing_charge_info_title; ?>">
					<strong>Dyeing Charge <?php echo number_format($dyeing_charge,2)."Tk./Kg";?></strong>
				</td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="60">Prod Id</th>
				<th width="80">Lot</th>
				<th width="250">Fabric Description</th>
				<th width="80">Brand</th>
				<th width="100">Avg Grey Fabric Rate /Kg (Tk.) </th>
				<th width="70">Net Qty</th>
				<th >Used Qty</th>
			</thead>
		</table>
		<div style="width:840px; max-height:280px; overflow-y:scroll">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table" id="tbl_lot_list">
				<?php
				$i=1;

				$sales_cond = ($is_sales==1)?" and d.is_sales=1":"";
				$sql="SELECT c.detarmination_id ,c.id,c.lot,c.brand, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type,sum(b.cons_quantity) issue_qty,sum(b.cons_amount) cons_amount from  order_wise_pro_details d, product_details_master c, inv_transaction b , inv_issue_master a where d.po_breakdown_id in(".$po_id_all.") and  d.trans_type = 2 AND d.entry_form IN (16, 61) and d.prod_id=c.id  and c.detarmination_id=".$fabric_description_id."  AND  b.mst_id=a.id  AND b.id = d.trans_id  AND b.prod_id = d.prod_id and a.entry_form in (16,61) and a.item_category=13 and b.id=d.trans_id and a.status_active=1 and b.item_category=13 and b.status_active=1 and c.detarmination_id=".$fabric_description_id." $sales_cond group by c.id,c.lot,c.brand,c.supplier_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type,c.detarmination_id";
				
				//echo $sql;
				if($update_dtls_id!="")
				{
					//echo "select id,prod_id,used_qty,rate,amount from pro_material_used_dtls where mst_id=$update_id and dtls_id =$update_dtls_id";
					$update_sql=sql_select("select id,prod_id,used_qty,rate,amount from pro_material_used_dtls where mst_id=$update_id and dtls_id =$update_dtls_id");
					$update_data_arr=array();
					foreach($update_sql as $val)
					{
						$update_data_arr[$val[csf('prod_id')]]['prod_id']=$val[csf('prod_id')];
						$update_data_arr[$val[csf('prod_id')]]['id']=$val[csf('id')];
						$update_data_arr[$val[csf('prod_id')]]['used_qty']=$val[csf('used_qty')];
						$update_data_arr[$val[csf('prod_id')]]['rate']=$val[csf('rate')];
						$update_data_arr[$val[csf('prod_id')]]['amount']=$val[csf('amount')];
						$check_arr[]=$val[csf('prod_id')];
					}
				}
					//echo $sql;
				$nameArray=sql_select($sql);
				foreach ($nameArray as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					$composition_string = $composition_arr[$row[csf('detarmination_id')]];
					$net_used=$txt_receive_qnty;
					$process_loss_used=($net_used*100)/(100-$process_loss);
					//echo $process_loss_used;
					if(in_array($row[csf("id")], $check_arr))
					{
						?>
						<tr bgcolor="#FFFF99" style="text-decoration:none;" id="search<? echo $i;?>">
							<td width="30" align="center">
								<?php echo "$i"; ?>
								<input type="hidden" name="update_material_id[]" id="update_material_id<?php echo $i; ?>" value="<?php echo $update_data_arr[$row[csf('id')]]['id']; ?>"/>
								<input type="hidden" name="txt_net_used[]" id="txt_net_used<?php echo $i; ?>" value="<?php  echo $net_used; ?>"/>
								<input type="hidden" name="txt_cons_rate[]" id="txt_cons_rate<?php echo $i; ?>" value="<?php echo $update_data_arr[$row[csf('id')]]['rate']; ?>"/>
								<input type="hidden" name="txt_prod_id[]" id="txt_prod_id<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>" />
								<input type="hidden" name="txt_yarn_count_id[]" id="txt_yarn_count_id<?php echo $i; ?>" value="<?php echo $row[csf('yarn_count_id')]; ?>" />
								<input type="hidden" name="txt_brand[]" id="txt_brand<?php echo $i; ?>" value="<?php echo $brand_arr[$row[csf('brand')]]; ?>" />	         
								<input type="hidden" name="txt_lot[]" id="txt_lot<?php echo $i; ?>" value="<?php echo $row[csf('lot')]; ?>" />
							</td>
							<td width="60"><p><?php  echo $row[csf('id')];?></p></td>
							<td width="80"><p><?php  echo $row[csf('lot')];?></p></td>
							<td width="250"><p><?php echo $composition_string;?></p></td>

							<td width="80"><p><?php  echo $brand_arr[$row[csf('brand')]];?></p></td>
							<td width="100" align="right"><p><?php  echo $update_data_arr[$row[csf('id')]]['rate'];?></p></td>
							<td width="70" align="right"><p><?php   echo $net_used;?></p></td>
							<td>
								<input type="text" id="txt_used_qty_<? echo $i;  ?>" name="txt_used_qty[]"  style="width:80px" class="text_boxes_numeric" value="<? /*if($process_loss_used==0) { $process_loss_used=$update_data_arr[$row[csf('id')]]['used_qty'];} echo number_format($process_loss_used,2,".","");*/ echo $update_data_arr[$row[csf('id')]]['used_qty']; ?>" placeholder="<?  echo number_format($process_loss_used,2,".",""); ?>" onKeyUp="fnc_ommit_data(<? echo $i;  ?>)"/>
							</td>
						</tr>
						<?
						$i++;
					}
					else
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;" id="search<? echo $i;?>">
							<td width="30" align="center"><?php echo "$i"; ?>
								<input type="hidden" name="update_material_id[]" id="update_material_id<?php echo $i; ?>" value="0"/>
								<input type="hidden" name="txt_net_used[]" id="txt_net_used<?php echo $i; ?>" value="<?php  echo $net_used; ?>"/>
								<input type="hidden" name="txt_cons_rate[]" id="txt_cons_rate<?php echo $i; ?>" value="<?php echo $row[csf('cons_amount')]/$row[csf('issue_qty')]; ?>"/>
								<input type="hidden" name="txt_prod_id[]" id="txt_prod_id<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>" />
								<input type="hidden" name="txt_yarn_count_id[]" id="txt_yarn_count_id<?php echo $i; ?>" value="<?php echo $row[csf('yarn_count_id')]; ?>" />
								<input type="hidden" name="txt_brand[]" id="txt_brand<?php echo $i; ?>" value="<?php echo $brand_arr[$row[csf('brand')]]; ?>" />
								<input type="hidden" name="txt_lot[]" id="txt_lot<?php echo $i; ?>" value="<?php echo $row[csf('lot')]; ?>" />
							</td>
							<td width="60"><p><?php echo $row[csf('id')];?></p></td>
							<td width="80"><p><?php echo $row[csf('lot')];?></p></td>
							<td width="250"><p><?php echo $composition_string;?></p></td>
							<td width="80"><p><?php echo $brand_arr[$row[csf('brand')]];?></p></td>
							<td width="100" align="right"><p><?php echo $row[csf('cons_amount')]/$row[csf('issue_qty')];?></p></td>
							<td width="70" align="right"><p><?php echo $net_used;?></p></td>
							<td><input type="text" id="txt_used_qty_<? echo $i; ?>" name="txt_used_qty[]"  style="width:80px" class="text_boxes_numeric" value="<? if($i==1 && $update_dtls_id=="") echo number_format($process_loss_used,2,".",""); ?>" placeholder="<?  echo number_format($process_loss_used,2,".",""); ?>" onKeyUp="fnc_ommit_data(<? echo $i;  ?>)"/></td>
						</tr>
						<?
						$i++;
					}
				}
				?>
				<input type="hidden" name="txt_lot_row_id" id="txt_lot_row_id" value="<?php echo $i; ?>"/>
			</table>
		</div>
		<table width="840" cellspacing="0" cellpadding="0" border="1" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:100%; float:left" align="center">
							<input type="hidden" name="txt_knitting_charge" id="txt_knitting_charge" value="<?php echo $dyeing_charge; ?>"/>
							<input type="button" name="close" onClick="fnc_process_cost();" class="formbutton" value="Close" style="width:100px" />
						</div>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<?
	exit();
}

?>