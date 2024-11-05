<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

include('../../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

$user_id=$_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];
$location_id = $userCredential[0][csf('location_id')];

$company_credential_cond = "";

if ($company_id >0) {
    $company_credential_cond = " and comp.id in($company_id)";
}

if ($store_location_id !='') {
    $store_location_credential_cond = " and a.id in($store_location_id)";
}

if ($location_id !='') {
    $location_credential_cond = " and id in($location_id)";
}

if($item_cate_id !='') {
    $item_cate_credential_cond = $item_cate_id ;
}
//========== user credential end ==========
if ($action == "itemLot_popup")
{
	echo load_html_head_contents("Item Lot Info", "../../../", 1, 1, '', 1, '');
	extract($_REQUEST);
	?>
	<script>
 	function js_set_value(str)
	{
		$("#item_lot").val(str);
		parent.emailwindow.hide();
	}
	</script>
	<input type="hidden" id="prod_id"/><input type="hidden" id="item_lot"/>
	<?
	if ($db_type == 0) {
	$sql = "SELECT distinct batch_lot from inv_transaction where prod_id='$txt_prod_id' and status_active=1 and is_deleted=0 and batch_lot!=' '  order by batch_lot desc";
	} elseif ($db_type == 2) {
	$sql = "SELECT distinct batch_lot from inv_transaction where prod_id='$txt_prod_id' and status_active=1 and is_deleted=0 and batch_lot is not null order by batch_lot desc";
	}
	
	//echo $sql;
	
	echo create_list_view("list_view", "Item Lot", "200", "330", "250", 0, $sql, "js_set_value", "batch_lot", "", 1, "", 0, "batch_lot", "recipe_entry_controller", 'setFilterGrid("list_view",-1);', '0', '');
	
	//echo create_list_view("list_view", "Item Lot", "50,80,80,70,80,130,70,80,110,100,70,90,90,90", "1250", "200", 0, $sql, "js_set_value", "id", "", 1, "0,0,batch_id,batch_id,batch_id,0,0,order_source,0,buyer_id,color_id,color_range,0,0", $arr, "id,labdip_no,batch_id,batch_id,batch_id,recipe_description,recipe_date,order_source,style_or_order,buyer_id,color_id,color_range,pickup,surplus_solution", "", "", '0,0,0,0,0,0,3,0,0,0,0,0,0,0', '');
	die;
}

if ($action == "load_drop_down_location")
{
	$data = explode("_", $data);
	echo create_drop_down("cbo_location", 150, "SELECT id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-Select Location-", 0, "load_drop_down('requires/aop_recipe_entry_controller', document.getElementById('cbo_company_id').value+'__'+this.value+'__'+document.getElementById('cbo_recipe_for').value, 'load_drop_down_store', 'store_td');");
	exit();
}

if ($action == "load_drop_down_buyer")
{
	$exdata=explode("_",$data);
	if($exdata[1]==1)
	{
		echo create_drop_down( "cbo_buyer_name", 150, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Party--", "", "",1);
	}
	else if($exdata[1]==2)
	{
		echo create_drop_down("cbo_buyer_name", 150, "SELECT buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$exdata[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buy.buyer_name", "id,buyer_name", 1, "--Select Party--", $selected, "", 1);
	}
	exit();
}

if ($action=="load_drop_down_store")
{
	$exdata=explode("__",$data);

	if($exdata[1]!=0) $store_location_cond=" and a.location_id='$exdata[1]'"; else $store_location_cond="";
	if($exdata[2]==3 || $exdata[2]==2 || $exdata[2]==1 )
	{
		//echo "select a.id, a.store_name from lib_store_location a, lib_store_location_category b  where a.id=b.store_location_id and a.is_deleted=0 and a.company_id='$exdata[0]' and a.status_active=1 and b.category_type in (5,6,7) $store_location_cond $store_location_credential_cond group by a.id, a.store_name order by a.store_name";
		echo create_drop_down( "cbo_store_id", 150,"SELECT a.id, a.store_name from lib_store_location a, lib_store_location_category b  where a.id=b.store_location_id and a.is_deleted=0 and a.company_id='$exdata[0]' and a.status_active=1 and b.category_type in (5,6,7,22,23) $store_location_cond $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-Store-", $selected, "","");
	}
	else
	{
		echo create_drop_down( "cbo_store_id", 70, $blank_array,"", "1", "-Store-", 0, "","0","","","","","","" );
	}
	exit();
}

if ($action=="load_drop_down_embl_type")
{
	if($data==1) $new_subprocess_array= $emblishment_print_type;
	else if($data==2) $new_subprocess_array= $emblishment_embroy_type;
	else if($data==3) $new_subprocess_array= $emblishment_wash_type;
	else if($data==4) $new_subprocess_array= $emblishment_spwork_type;
	else if($data==5) $new_subprocess_array= $emblishment_gmts_type;
	else $new_subprocess_array=$blank_array;
	echo create_drop_down( "cboEmblType", 150, $new_subprocess_array,"", 1, "--Select--",0,"", 1,"" );
	exit();
}

if($action=="emblorder_popup")
{
	echo load_html_head_contents("AOP Order Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	//echo $data.'=='.$cbo_company_id;
	?>
	<script>
		function js_set_value(str)
		{
			$("#selected_str_data").val(str);
			parent.emailwindow.hide();
		}

		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0)
			{
				$('#search_by_td').html('AOP Job No');
			}
			else if(val==2)
			{
				$('#search_by_td').html('W/O No');
			}
			else if(val==3)
			{
				$('#search_by_td').html('Buyer Job');
			}
			else if(val==4)
			{
				$('#search_by_td').html('Buyer Po');
			}
			else if(val==5)
			{
				$('#search_by_td').html('Buyer Style');
			}
		}
	</script>
</head>
<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>
						<th colspan="7"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
					</tr>
					<tr>
						<th width="140">Party Name</th>
						<th width="100">Search By</th>
                        <th width="100" id="search_by_td">AOP Job No</th>
						<th width="60">Year</th>
						<th width="130" colspan="2">Date Range</th>
						<th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /> </th>
					</tr>
				</thead>
				<tbody>
					<tr class="general">
						<td>
							<?
							if($cbo_within_group==1)
							{
								echo create_drop_down( "cbo_party_name", 140, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Select Party-", "", "",0);
							}
							else if($cbo_within_group==2)
							{
								echo create_drop_down( "cbo_party_name", 140, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-Select Party-", "", "" );
							}
							?><input type="hidden" id="selected_str_data">
						</td>
						<td>
							<?
                                $search_by_arr=array(1=>"AOP Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
                                echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                            ?>
                        </td>
						<td align="center">
                            <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:90px" placeholder="" />
                        </td>
						<td align="center"><? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                        <td align="center"><input name="txt_date_from" id="txt_date_from" class="datepicker" placeholder="From" style="width:60px"></td>
                        <td align="center"><input name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To" style="width:60px"></td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( '<? echo $cbo_company_id; ?>'+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_year_selection').value+'_'+'<? echo $cbo_within_group; ?>', 'create_order_search_list_view', 'search_div', 'aop_recipe_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
                        </tr>
                        <tr class="general">
                            <td colspan="7" align="center" valign="middle"><? echo load_month_buttons(); ?></td>
                        </tr>

						</tbody>
					</table>
				</form>
                <div id="search_div"></div>
			</div>
		</body>
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_order_search_list_view")
{
	$exdata=explode('_',$data);
	$cbo_company_id=$exdata[0];
	$party_id=$exdata[1];
	$form_date=$exdata[2];
	$to_date=$exdata[3];

	$search_by=$exdata[4];
	$search_str=trim($exdata[5]);
	$search_type =$exdata[6];
	$year =$exdata[7];
	$within_group=$exdata[8];

	if($cbo_company_id!=0) $company=" and a.company_id='$cbo_company_id'"; else { echo "Please Select Company First."; die; }

	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";

			if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
			else if ($search_by==5) $style_cond=" and a.style_ref_no = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";

			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str%'";
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str%'";
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";

			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";
			else if ($search_by==4) $po_cond=" and b.po_number like '$search_str%'";
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '$search_str%'";
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";

			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str'";
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str'";
		}
	}

	$po_ids='';

	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	if(($job_cond!="" && $search_by==3) || ($style_cond!="" && $search_by==4)|| ($po_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
	}

	if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";

	if($party_id!=0) $party_id_cond=" and a.party_id='$party_id'"; else $party_id_cond="";

	if($db_type==0)
	{
		if ($form_date!="" &&  $to_date!="") $order_rcv_date = "and a.receive_date between '".change_date_format($form_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'"; else $order_rcv_date ="";

		$year_select="YEAR(a.insert_date)";
		$year_cond=" and YEAR(a.insert_date)=$year";
	}
	else
	{
		if ($form_date!="" &&  $to_date!="") $order_rcv_date = "and a.receive_date between '".change_date_format($form_date, "", "",1)."' and '".change_date_format($to_date, "", "",1)."'"; else $order_rcv_date ="";
		$year_select="TO_CHAR(a.insert_date,'YYYY')";
		$year_cond=" and to_char(a.insert_date,'YYYY')=$year";
	}

	if($within_group==1)
	{
		$party_arr=return_library_array( "SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
	}
	$color_arr=return_library_array( "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name" );

	$buyer_po_arr=array();

	$po_sql ="SELECT a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	?>
    <body>
		<div align="center">
			<fieldset style="width:875px;">
				<form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table" >
						<thead>
							<th width="30">SL</th>
							<th width="60">Job</th>
                            <th width="90">WO No.</th>
                            <th width="90">Buyer Po</th>
                            <th width="90">Buyer Style</th>
                            <th width="90">Gmts. Item</th>
                            <th width="80">Body Part</th>
                            <th width="80">AOP Name</th>
                            <th width="80">AOP Type</th>
                            <th width="90">Color</th>
                            <th>Qty</th>
						</thead>
					</table>
					<div style="width:870px; overflow-y:scroll; max-height:300px;">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table" id="list_view" >
							<?
							$sql= "SELECT a.job_no_prefix_num, a.subcon_job, $year_select as year, a.party_id, a.id, a.order_id,a.within_group, b.id as po_id, b.order_no, b.main_process_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, c.color_id, sum(c.qnty) as qty  from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and b.id=c.mst_id and a.entry_form=204 and a.within_group=$within_group and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_rcv_date $company $search_com_cond  $party_id_cond $po_idsCond group by a.job_no_prefix_num, a.subcon_job, a.insert_date, a.party_id, a.id, a.order_id,a.within_group, b.id, b.order_no, b.main_process_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, c.color_id order by a.id DESC";
							//echo $sql; die;
							$sql_res=sql_select($sql);

							$i=1;
							foreach($sql_res as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								if($row[csf('main_process_id')]==1) $new_subprocess_array= $emblishment_print_type;
								else if($row[csf('main_process_id')]==2) $new_subprocess_array= $emblishment_embroy_type;
								else if($row[csf('main_process_id')]==3) $new_subprocess_array= $emblishment_wash_type;
								else if($row[csf('main_process_id')]==4) $new_subprocess_array= $emblishment_spwork_type;
								else if($row[csf('main_process_id')]==5) $new_subprocess_array= $emblishment_gmts_type;
								else $new_subprocess_array=$blank_array;

								$str="";
								$str=$row[csf('party_id')].'___'.$row[csf('subcon_job')].'___'.$row[csf('po_id')].'___'.$row[csf('order_no')].'___'.$row[csf('gmts_item_id')].'___'.$row[csf('body_part')].'___'.$row[csf('main_process_id')].'___'.$row[csf('embl_type')].'___'.$row[csf('color_id')].'___'.$color_arr[$row[csf('color_id')]].'___'.$row[csf("buyer_po_id")].'___'.$buyer_po_arr[$row[csf("buyer_po_id")]]['po'].'___'.$buyer_po_arr[$row[csf("buyer_po_id")]]['style'].'___'.$row[csf('within_group')];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $str;?>')">
									<td width="30" align="center"><?php echo $i; ?></td>
									<td width="60" align="center"><?php echo $row[csf('job_no_prefix_num')]; ?></td>
                                    <td width="90" style="word-break:break-all"><?php echo $row[csf('order_no')]; ?></td>
                                    <td width="90" style="word-break:break-all"><?php echo $buyer_po_arr[$row[csf("buyer_po_id")]]['po']; ?></td>
                                    <td width="90" style="word-break:break-all"><?php echo $buyer_po_arr[$row[csf("buyer_po_id")]]['style']; ?></td>
                                    <td width="90" style="word-break:break-all"><?php echo $garments_item[$row[csf('gmts_item_id')]]; ?></td>
                                    <td width="80" style="word-break:break-all"><?php echo $body_part[$row[csf('body_part')]]; ?></td>
                                    <td width="80" style="word-break:break-all"><?php echo $emblishment_name_array[$row[csf('main_process_id')]]; ?></td>
                                    <td width="80" style="word-break:break-all"><?php echo $new_subprocess_array[$row[csf('embl_type')]]; ?></td>
                                    <td width="90" style="word-break:break-all"><?php echo $color_arr[$row[csf('color_id')]]; ?></td>
                                    <td align="right"><?php echo number_format($row[csf('qty')]*12,2); ?></td>
								</tr>
								<?
								$i++;
							}
							?>
						</table>
					</div>
				</form>
			</fieldset>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "systemid_popup")
{
	echo load_html_head_contents("Recipe No Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
    <script>
        function js_set_value(id)
        {
            $('#hidden_update_id').val(id);
            parent.emailwindow.hide();
        }

		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0)
			{
				$('#search_by_td').html('AOP Job No');
			}
			else if(val==2)
			{
				$('#search_by_td').html('W/O No');
			}
			else if(val==3)
			{
				$('#search_by_td').html('Buyer Job');
			}
			else if(val==4)
			{
				$('#search_by_td').html('Buyer Po');
			}
			else if(val==5)
			{
				$('#search_by_td').html('Buyer Style');
			}
		}
    </script>
    </head>

    <body>
    <div align="center" style="width:100%;">
        <form name="searchlabdipfrm" id="searchlabdipfrm">
            <fieldset style="width:950px;">
                <table cellpadding="0" cellspacing="0" border="1" rules="all" width="940" class="rpt_table">
                    <thead>
                        <tr>
                            <th colspan="9"><? echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --"); ?></th>
                        </tr>
                        <tr>
                            <th>Recipe Date Range</th>
                            <th>Recipe ID</th>
                            <th>Recipe Description</th>
                            <th>Search By</th>
                    		<th  id="search_by_td">AOP Job No</th>
                    		<th >AOP Batch No.</th>
                    		<th >Design No.</th>
                    		<th >AOP Ref.</th>
                            <th>
                                <input type="reset" name="reset" id="reset" value="Reset" style="width:80px;"  class="formbutton"/>
                                <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                                <input type="hidden" name="hidden_update_id" id="hidden_update_id" class="text_boxes" value="">
                            </th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px;">To<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px;">
                        </td>
                        <td>
                            <input type="text" style="width:90px;" class="text_boxes" name="txt_search_sysId" id="txt_search_sysId" placeholder="Search"/>
                        </td>
                        <td>
                            <input type="text" style="width:100px;" class="text_boxes" name="txt_search_recDes" id="txt_search_recDes" placeholder="Search"/>
                        </td>
                        <td>
							<?
                                $search_by_arr=array(1=>"AOP Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
                                echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                            ?>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:80px" placeholder="" />
                        </td>
                        <td align="center">
                        	<input type="text" style="width:80px" class="text_boxes"  name="txt_batch_no" id="txt_batch_no" />
                    	</td>
                    	<td align="center">
                        	<input type="text" style="width:80px" class="text_boxes"  name="txt_design_no" id="txt_design_no" />
                    	</td>
                        <td align="center">
                        	<input type="text" style="width:80px" class="text_boxes"  name="txt_aop_ref" id="txt_aop_ref" />
                    	</td>
                        <td>
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_sysId').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_recDes').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('txt_aop_ref').value+'_'+document.getElementById('txt_batch_no').value+'_'+'<? echo $cbo_recipe_for ?>'+'_'+document.getElementById('txt_design_no').value, 'create_recipe_search_list_view', 'search_div', 'aop_recipe_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:80px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="980px" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
                <div id="search_div"></div>
            </fieldset>
        </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
	exit();
}

if ($action == "create_recipe_search_list_view")
{
	$buyer_arr = return_library_array("SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0", 'id', 'buyer_name');
	$company_arr = return_library_array("SELECT id, company_name from lib_company where status_active =1 and is_deleted=0", 'id', 'company_name');
	$color_arr = return_library_array("SELECT id, color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');
	$po_arr=return_library_array( "SELECT id,order_no from subcon_ord_dtls where status_active =1 and is_deleted=0",'id','order_no');
	//echo $data; die;
	$data = explode("_", $data);

	$sysid = $data[0];
	$start_date = $data[1];
	$end_date = $data[2];
	$company_id = $data[3];
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[7]));
	$rec_des = trim($data[5]);
	$search_type = $data[6];
	$aop_ref = $data[8];
	$batch_no = $data[9];
	$recipefor = $data[10];
	$design_no = $data[11];

	if ($start_date != "" && $end_date != "")
	{
		if ($db_type == 0)
		{
			$date_cond = "and a.recipe_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-", 1) . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-", 1) . "'";
		}
		else if ($db_type == 2)
		{
			$date_cond = "and a.recipe_date between '" . change_date_format(trim($start_date), "mm-dd-yyyy", "/", 1) . "' and '" . change_date_format(trim($end_date), "mm-dd-yyyy", "/", 1) . "'";
		}
	}
	else
	{
		$date_cond = "";
	}

	$sysid_cond = ""; $rec_des_cond = ""; $job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond=""; $aop_ref_cond=""; $batch_no_cond="";$design_no_cond="";

	if ($design_no!= "")
	{
		$design_no_cond = "and b.design_no like '%".$design_no."%'";

		$search_com_cond = "and b.design_no like '%".$design_no."%'";
	}

	if ($search_type == 1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";

			if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
			else if ($search_by==5) $style_cond=" and a.style_ref_no = '$search_str' ";
		}
		if ($batch_no!='') $batch_no_cond= " and b.batch_no='$batch_no'";
		if ($aop_ref!='') $aop_ref_cond= " and a.aop_reference='$aop_ref'";
		if ($sysid != '') $sysid_cond = " and a.recipe_no_prefix_num=$sysid";
	}
	else if ($search_type == 4 || $search_type == 0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";

			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str%'";
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str%'";

		}
		if ($batch_no!='') $batch_no_cond= " and b.batch_no like '%$batch_no%'";
		if ($aop_ref!='') $aop_ref_cond= " and a.aop_reference like '%$aop_ref%'";
		if ($sysid != '') $sysid_cond = " and a.recipe_no_prefix_num like '%$sysid%' ";
		if ($rec_des != '') $rec_des_cond = " and a.recipe_description like '%$rec_des%'";
	}
	else if ($search_type == 2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";

			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";
			else if ($search_by==4) $po_cond=" and b.po_number like '$search_str%'";
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '$search_str%'";
		}
		if ($batch_no!='') $batch_no_cond= " and b.batch_no like '$batch_no%'";
		if ($aop_ref!='') $aop_ref_cond= " and a.aop_reference like '$aop_ref%'";
		if ($sysid != '') $sysid_cond = " and a.recipe_no_prefix_num like '$sysid%' ";
		if ($rec_des != '') $rec_des_cond = " and a.recipe_description like '$rec_des%'";
	}
	else if ($search_type == 3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";

			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str'";
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str'";
		}
		if ($batch_no!='') $batch_no_cond= " and b.batch_no like '%$batch_no'";
		if ($aop_ref!='') $aop_ref_cond= " and a.aop_reference like '%$aop_ref'";
		if ($sysid != '') $sysid_cond = " and a.recipe_no_prefix_num like '%$sysid' ";
		if ($rec_des != '') $rec_des_cond = " and a.recipe_description like '%$rec_des'";
	}

	if($aop_ref_cond!='' || $design_no_cond!='')
	{
		$ord_sql = "SELECT b.id,a.subcon_job,a.aop_reference,b.order_no,b.buyer_style_ref,b.buyer_po_no, b.design_no from subcon_ord_mst a ,subcon_ord_dtls b where  company_id =$company_id $aop_ref_cond $design_no_cond and a.entry_form=278 and  a.subcon_job=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
		$ordArray=sql_select( $ord_sql ); $po_arr=array(); $ref_arr=array();
		foreach ($ordArray as $row)
		{
			//$po_arr[$row[csf('id')]] = $row[csf('order_no')];
			$po_arr[$row[csf('id')]]['order'] = $row[csf('order_no')];
			$po_arr[$row[csf('id')]]['job'] = $row[csf('subcon_job')];
			$po_arr[$row[csf('id')]]['buyer_style_ref'] = $row[csf('buyer_style_ref')];
			$po_arr[$row[csf('id')]]['buyer_po_no'] = $row[csf('buyer_po_no')];
			$po_arr[$row[csf('id')]]['design_no'] = $row[csf('design_no')];
			$ref_arr[$row[csf('id')]] = $row[csf('aop_reference')];
			$po_id[] .= $row[csf("id")];
		}
		$po_id_cond=" and a.po_id in (".implode(",",$po_id).") ";
	}
	else
	{
		$ord_sql = "SELECT b.id,a.subcon_job,a.aop_reference,b.order_no,b.buyer_style_ref,b.buyer_po_no, b.design_no from subcon_ord_mst a ,subcon_ord_dtls b where company_id =$company_id and a.entry_form=278 and  a.subcon_job=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
		$ordArray=sql_select( $ord_sql ); $po_arr=array(); $ref_arr=array();
		foreach ($ordArray as $row)
		{
			//$po_arr[$row[csf('id')]] = $row[csf('order_no')];
			$po_arr[$row[csf('id')]]['order'] = $row[csf('order_no')];
			$po_arr[$row[csf('id')]]['job'] = $row[csf('subcon_job')];
			$po_arr[$row[csf('id')]]['buyer_style_ref'] = $row[csf('buyer_style_ref')];
			$po_arr[$row[csf('id')]]['buyer_po_no'] = $row[csf('buyer_po_no')];
			$po_arr[$row[csf('id')]]['design_no'] = $row[csf('design_no')];
			$ref_arr[$row[csf('id')]] = $row[csf('aop_reference')];
		}
		//$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
		$po_id_cond='';
	}

	//echo $aop_ref_cond; die;
	$po_ids=''; $buyer_po_arr=array();

	if($db_type==0) $id_cond="group_concat(b.id) as id";
	else if($db_type==2) $id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as id";
	//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		//echo "sdfdf"; die;
		$po_ids = return_field_value("$id_cond", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
	}
	//echo $po_ids;  die;
	if($db_type==2 && $po_ids!="") $po_ids = $po_ids->load();
	if ($po_ids!="")
	{
		$po_ids=explode(",",$po_ids);
		$po_idsCond=""; $poIdsCond="";
		//echo count($po_ids); die;
		if($db_type==2 && count($po_ids)>=999)
		{
			$chunk_arr=array_chunk($po_ids,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",",$val);
				if($po_idsCond=="")
				{
					$po_idsCond.=" and ( a.buyer_po_id in ( $ids) ";
					$poIdsCond.=" and ( b.id in ( $ids) ";
				}
				else
				{
					$po_idsCond.=" or  a.buyer_po_id in ( $ids) ";
					$poIdsCond.=" or  b.id in ( $ids) ";
				}
			}
			$po_idsCond.=")";
			$poIdsCond.=")";
		}
		else
		{
			$ids=implode(",",$po_ids);
			$po_idsCond.=" and a.buyer_po_id in ($ids) ";
			$poIdsCond.=" and b.id in ($ids) ";
		}
		//}
	}
	else if($po_ids=="" && (($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5)))
		{
			echo "Not Found"; die;
		}

	$po_sql ="SELECT a.style_ref_no,a.job_no_prefix_num, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $poIdsCond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no_prefix_num")];
	}
	unset($po_sql_res);
	$spo_ids='';

	if($db_type==0)
	{
		$id_cond="group_concat(b.id)";
	}
	else if($db_type==2)
	{
		$id_cond="listagg(b.id,',') within group (order by b.id)";
	}
	if(($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2))
	{
		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.subcon_job=b.job_no_mst $search_com_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
	}

	if ( $spo_ids!="") $spo_idsCond=" and a.po_id in ($spo_ids)"; else $spo_idsCond="";

	if($spo_ids=="" && (($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2)))
		{
			echo "Not Found"; die;
		}

	/*$batch_sql = "select a.id,a.entry_form,a.is_sales,a.sales_order_no,a.booking_no ,a.sales_order_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.is_sales=1 and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id DESC";
	$batch_sql_res=sql_select($batch_sql); $batch_arr=array();
	foreach ($batch_sql_res as $row)
	{
		$batch_arr[$row[csf("id")]]['is_sales']=$row[csf("is_sales")];
		$batch_arr[$row[csf("id")]]['sales_order_no']=$row[csf("sales_order_no")];
		$batch_arr[$row[csf("id")]]['booking_no']=$row[csf("booking_no")];
	}
	unset($po_sql_res);*/

	?>
	<body>
		<div align="center">
			<fieldset style="width:950px;">
				<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="940" class="rpt_table" >
						<thead>
							<th width="30">SL</th>
							<th width="50">Recipe No</th>
                            <th width="60">Recip For</th>
                            <th width="60">Recipe Date</th>
                            <th width="90">Batch No.</th>
                            <th width="90">Design No.</th>
                            <th width="100">Work Order</th>
                            <th width="100">Buyer Po</th>
                			<th width="100">Buyer Style</th>
                            <th width="100">Party</th>
                            <th width="90">Color</th>
                            <th>AOP Ref.</th>
						</thead>
					</table>
					<div style="width:950px; overflow-y:scroll; max-height:300px;">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="940" class="rpt_table" id="tbl_list_search" >
							<?
	
							if($recipefor==3)
							{ 	
								  $sql = "SELECT a.id, a.recipe_no_prefix_num, a.recipe_for, a.recipe_date, a.within_group, a.po_id, a.buyer_id, a.gmts_item, a.body_part, a.embl_name, a.embl_type, a.color_id, a.buyer_po_ids,a.batch_id,0 as batch_no,0 as entry_form,0 as is_sales,0 as sales_order_no,0 as booking_no ,0 as sales_order_id from pro_recipe_entry_mst a  where a.company_id='$company_id' and a.entry_form=285  and a.status_active=1 and a.is_deleted=0 and a.recipe_for=3  $sysid_cond $rec_des_cond $date_cond $po_idsCond $spo_idsCond $po_id_cond $batch_no_cond order by id DESC";
								
							}
							else
							{  
								$sql = "SELECT a.id, a.recipe_no_prefix_num, a.recipe_for, a.recipe_date, a.within_group, a.po_id, a.buyer_id, a.gmts_item, a.body_part, a.embl_name, a.embl_type, a.color_id, a.buyer_po_ids,a.batch_id,b.batch_no,b.entry_form,b.is_sales,b.sales_order_no,b.booking_no ,b.sales_order_id from pro_recipe_entry_mst a, pro_batch_create_mst b where a.company_id='$company_id' and a.entry_form=285 and a.batch_id=b.id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $sysid_cond $rec_des_cond $date_cond $po_idsCond $spo_idsCond $po_id_cond $batch_no_cond order by id DESC";
							}
							
							//echo $sql; die;
							$sql_res=sql_select($sql);

							$i=1;
							foreach($sql_res as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$party_name=""; $ref_no ="";
								if($row[csf('within_group')]==1) $party_name=$company_arr[$row[csf('buyer_id')]];
								else if($row[csf('within_group')]==2) $party_name=$buyer_arr[$row[csf('buyer_id')]];

								//$order_no=''; $order_id='';  $order_ids='';  $aop_jobs=''; $aop_job=''; $buyer_job='';
								$buyer_po=''; $buyer_style='';
								//$all_ref_arr=array(); $all_party_arr=array();


								if($row[csf("entry_form")]==0)
								{
									if($row[csf("is_sales")]==1)
									{
										$buyer_po=$row[csf("sales_order_no")];
										$order_no=$row[csf("booking_no")];
									}
									else
									{
										$buyer_po=$buyer_po_arr[$row[csf("po_id")]]['po'];
										$buyer_job=$buyer_po_arr[$row[csf("po_id")]]['job'];
										$buyer_style=$buyer_po_arr[$row[csf("po_id")]]['style'];
									}
								}
								else
								{
									if($row[csf('within_group')]==1) {
										$buyer_po_id=explode(",",$row[csf('buyer_po_ids')]);
										foreach($buyer_po_id as $po_id)
										{
											if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
											if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
										}
									}else{
										if($buyer_po=="") $buyer_po=$po_arr[$row[csf("po_id")]]['buyer_po_no']; else $buyer_po.=", ".$po_arr[$row[csf("po_id")]]['buyer_po_no'];
										if($buyer_style=="") $buyer_style=$po_arr[$row[csf("po_id")]]['buyer_style_ref']; else $buyer_style.=", ".$po_arr[$row[csf("po_id")]]['buyer_style_ref'];
									}
									
								}
								$buyer_po=implode(", ",array_unique(explode(", ",$buyer_po)));
								$buyer_style=implode(", ",array_unique(explode(", ",$buyer_style)));

								$all_ref_arr=array();
								$all_design_no_arr=array();
								$order_id=array_unique(explode(",",$row[csf("po_id")]));
								foreach($order_id as $val)
								{
									//if($order_no=="") $order_no=$po_arr[$val]; else $order_no.=", ".$po_arr[$val];
									//if($order_ids=="") $order_ids=$val; else $order_ids.=", ".$val;
									$all_ref_arr[] .= $ref_arr[$val];
								}
								//echo "<pre>";
								//print_r($all_ref_arr);
								$ref_no = implode(",", array_unique($all_ref_arr));
								$design_no = $po_arr[$row[csf("po_id")]]['design_no'];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $row[csf('id')]."_".$ref_no;?>')">
									<td width="30" align="center"><?php echo $i; ?></td>
									<td width="50" align="center"><?php echo $row[csf('recipe_no_prefix_num')]; ?></td>
                                    <td width="60" style="word-break:break-all"><?php echo $recipe_for[$row[csf('recipe_for')]]; ?></td>
                                    <td width="60"><?php echo change_date_format($row[csf('recipe_date')]); ?></td>
                                    <td width="90" style="word-break:break-all"><?php echo $row[csf("batch_no")]; ?></td>
                                    <td width="90" style="word-break:break-all"><?php echo $design_no; ?></td>
                                    <td width="100" style="word-break:break-all"><?php echo $po_arr[$row[csf("po_id")]]['order']; ?></td>
                                    <td width="100" style="word-break:break-all"><?php echo $buyer_po; ?></td>
                                    <td width="100" style="word-break:break-all"><?php echo $buyer_style; ?></td>

                                    <td width="100" style="word-break:break-all"><?php echo $party_name; ?></td>
                                    <td width="90" style="word-break:break-all"><?php echo $color_arr[$row[csf('color_id')]]; ?></td>
                                    <td style="word-break:break-all"><?php echo $ref_no; ?></td>
								</tr>
								<?
								$i++;
							}
							?>
						</table>
					</div>
				</form>
			</fieldset>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == 'populate_data_from_search_popup')
{

	$color_arr = return_library_array("SELECT id, color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');
	//$batch_arr = return_library_array("SELECT id, color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');
	//$order_arr = array();
	//$order_arr=return_library_array( "SELECT id,order_no from subcon_ord_dtls where status_active =1 and is_deleted=0",'id','order_no');
	$order_sql ="SELECT id,order_no,buyer_style_ref,buyer_po_no from subcon_ord_dtls where status_active =1 and is_deleted =0";
		$order_sql_res=sql_select($order_sql);
	foreach ($order_sql_res as $row)
	{
		$order_arr[$row[csf("id")]]['order_no']=$row[csf("order_no")];
		$order_arr[$row[csf("id")]]['buyer_style_ref']=$row[csf("buyer_style_ref")];
		$order_arr[$row[csf("id")]]['buyer_po_no']=$row[csf("buyer_po_no")];
	}
	unset($order_sql_res);

	$po_sql ="SELECT a.style_ref_no,a.buyer_name, b.id, b.po_number,a.job_no 
	from wo_po_details_master a, wo_po_break_down b 
	where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 group by a.style_ref_no,a.buyer_name, b.id, b.po_number,a.job_no";
	$po_sql_res=sql_select($po_sql); $buyer_po_arr=array(); 
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
		$buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
	}
	unset($po_sql_res);

	$batch_sql ="SELECT c.id as batch_id, c.batch_no, c.location_id, c.color_id, c.batch_weight, d.buyer_po_id,c.within_group
	from pro_batch_create_mst c, pro_batch_create_dtls d
	where c.id=d.mst_id and c.status_active=1 group by c.id ,c.batch_no,c.location_id,c.color_id,c.batch_weight,d.buyer_po_id,c.within_group";
	$batch_sql_res=sql_select($batch_sql); $batch_arr=array();
	foreach ($batch_sql_res as $row)
	{
		$batch_arr[$row[csf("batch_id")]]['batch_id']=$row[csf("batch_id")];
		$batch_arr[$row[csf("batch_id")]]['batch_no']=$row[csf("batch_no")];
		$batch_arr[$row[csf("batch_id")]]['location_id']=$row[csf("location_id")];
		$batch_arr[$row[csf("batch_id")]]['color_id']=$row[csf("color_id")];
		$batch_arr[$row[csf("batch_id")]]['batch_weight']=$row[csf("batch_weight")];
		$batch_arr[$row[csf("batch_id")]]['buyer_po_id'].=$row[csf("buyer_po_id")].",";
		$batch_arr[$row[csf("batch_id")]]['within_group']=$row[csf("within_group")];
	}
	unset($batch_sql_res);

	//echo "select id, recipe_no, company_id, location_id, recipe_description, recipe_for, recipe_date, within_group, po_id, job_no, buyer_po_id, buyer_id, gmts_item, body_part, embl_name, embl_type, color_id, remarks, batch_id,store_id,total_water_ratio from pro_recipe_entry_mst where id='$data' and entry_form=285";
	$data_array = sql_select("SELECT id, recipe_no, company_id, location_id, recipe_description, recipe_for, recipe_date, within_group, po_id, job_no, buyer_po_ids, buyer_id, gmts_item, body_part, embl_name, embl_type, color_id, remarks, batch_id,store_id,total_water_ratio from pro_recipe_entry_mst where id='$data' and entry_form=285 and status_active=1 and is_deleted=0");

	foreach ($data_array as $row)
	{
		$buyer_po_ids=chop($row[csf("buyer_po_ids")],',');
		if($batch_arr[$row[csf("batch_id")]]['within_group']==1)
		{
			$buyer_po_number=''; $buyerPoID='';
			$buyerpoid=array_unique(explode(",",$batch_arr[$row[csf("batch_id")]]['buyer_po_id']));
			foreach($buyerpoid as $buyer_po_id)
			{
				if($buyer_po_number=="") $buyer_po_number=$buyer_po_arr[$buyer_po_id]['po']; else $buyer_po_number.=','.$buyer_po_arr[$buyer_po_id]['po'];
				if($buyer_style=="") $buyer_style=$buyer_po_arr[$buyer_po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$buyer_po_id]['style'];
				if($buyerPoID=="") $buyerPoID=$buyer_po_id; else $buyerPoID.=','.$buyer_po_id;
			}
		}
		else
		{
			if($buyer_po_number=="") $buyer_po_number=$order_arr[$row[csf("po_id")]]['buyer_po_no']; else $buyer_po_number.=','.$order_arr[$row[csf("po_id")]]['buyer_po_no'];
			if($buyer_style=="") $buyer_style=$order_arr[$row[csf("po_id")]]['buyer_style_ref']; else $buyer_style.=','.$order_arr[$row[csf("po_id")]]['buyer_style_ref'];
			 //$buyer_po_number=$buyer_po_arr[$buyer_po_ids]['po'];
		}

		//$total_dayes_ratio = 100-$row[csf("total_water_ratio")];
		echo "document.getElementById('txt_sys_id').value 					= '" . $row[csf("recipe_no")] . "';\n";
		echo "document.getElementById('update_id_check').value 				= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('cbo_recipe_for').value 				= '" . $row[csf("recipe_for")] . "';\n";
		echo "document.getElementById('cbo_company_id').value 				= '" . $row[csf("company_id")] . "';\n";
		echo "document.getElementById('txt_batch_id').value 				= '" . $row[csf("batch_id")] . "';\n";
		echo "document.getElementById('txt_batch_number').value 			= '" . $batch_arr[$row[csf("batch_id")]]['batch_no'] . "';\n";
		echo "$('#cbo_company_id').attr('disabled','true')" . ";\n";
		//echo "load_drop_down('requires/aop_recipe_entry_controller', '".$row[csf('company_id')]."', 'load_drop_down_buyer', 'buyer_td_id' );\n";
		//echo "load_drop_down('requires/aop_recipe_entry_controller', ".$row[csf('embl_name')].", 'load_drop_down_embl_type', 'embl_type_td' );\n";
		echo "document.getElementById('cbo_location').value 				= '" . $row[csf("location_id")] . "';\n";
		echo "document.getElementById('txt_recipe_date').value 				= '" . change_date_format($row[csf("recipe_date")]) . "';\n";
		echo "$('#cbo_recipe_for').attr('disabled','true')" . ";\n";

		echo "document.getElementById('txt_recipe_des').value 				= '" . $row[csf("recipe_description")] . "';\n";
		echo "document.getElementById('txt_order_id').value 				= '" . $row[csf("po_id")] . "';\n";
		echo "document.getElementById('txt_order').value 					= '" . $order_arr[$row[csf("po_id")]]['order_no'] . "';\n";
		echo "document.getElementById('cbo_buyer_name').value 				= '" . $row[csf("buyer_id")] . "';\n";

		echo "document.getElementById('hidden_batch_color_id').value 		= '" . $batch_arr[$row[csf("batch_id")]]['color_id'] . "';\n";
		echo "document.getElementById('txt_batch_color').value 				= '" .  $color_arr[$batch_arr[$row[csf("batch_id")]]['color_id']] . "';\n";
		echo "document.getElementById('txt_batch_weight').value 			= '" .  $batch_arr[$row[csf("batch_id")]]['batch_weight'] . "';\n";

		echo "document.getElementById('txtbuyerPoId').value 				= '" . $buyerPoID . "';\n";
		echo "document.getElementById('txtbuyerPo').value 					= '" . $buyer_po_number . "';\n";
		echo "document.getElementById('hid_job_no').value 					= '" . $buyer_po_arr[$buyer_po_ids]['job'] . "';\n";
		echo "document.getElementById('txtstyleRef').value 					= '" . $buyer_style . "';\n";
		echo "document.getElementById('txt_water_ratio').value 				= '" . $row[csf("total_water_ratio")] . "';\n";
		//echo "document.getElementById('txt_days_ratio').value 				= " . $total_dayes_ratio . ";\n";
		echo "document.getElementById('txt_remarks').value 					= '" . $row[csf("remarks")] . "';\n";
		echo "document.getElementById('update_id').value 					= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('cbo_store_id').value 				= '" . $row[csf("store_id")] . "';\n";
		echo "$('#cbo_recipe_for').attr('disabled','true')" . ";\n";
		echo "$('#cbo_location').attr('disabled','true')" . ";\n";
		echo "$('#cbo_store_id').attr('disabled','true')" . ";\n";

		//echo "document.getElementById('txt_pocolor_id').value 				= '" . $row[csf("color_id")] . "';\n";
		//echo "document.getElementById('txt_po_color').value 				= '" . $color_arr[$row[csf("color_id")]] . "';\n";
		//echo "document.getElementById('cbo_within_group').value 			= '" . $row[csf("within_group")] . "';\n";
		//echo "$('#cbo_within_group').attr('disabled','true')" . ";\n";
		//echo "document.getElementById('cboEmblName').value 				= '" . $row[csf("embl_name")] . "';\n";
		//echo "document.getElementById('cboEmblType').value 				= '" . $row[csf("embl_type")] . "';\n";
		//echo "document.getElementById('hid_item_id').value 				= '" . $row[csf("gmts_item")] . "';\n";
		//echo "document.getElementById('hid_bodypart_id').value 			= '" . $row[csf("body_part")] . "';\n";


		echo "set_button_status(0, '" . $_SESSION['page_permission'] . "', 'fnc_recipe_entry',1);\n";
		exit();
	}
}

if ($action == "recipe_item_details")
{
	/*echo $data; die;
	$data=explode("**",$data);
	$company_id=$data[0];
	$store_id=$data[1];*/
	if ($company_id==0) $company_id =""; else $company_id =" and a.company_id='$company_id'";
	if ($store_id==0)  $store_id="";  else  $store_id=" and a.store_id=$store_id ";
	$multicolor_array = array();
	$product_arr = array();
	$color_arr = return_library_array("SELECT id,color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$item_group_arr=return_library_array( "SELECT id,item_name from lib_item_group where status_active=1 and is_deleted=0",'id','item_name');
	$sql = "SELECT id, color_id, new_prod_id from pro_recipe_entry_dtls where mst_id='$data' and status_active=1 and is_deleted=0 order by id";
	$nameArray = sql_select($sql);
	foreach ($nameArray as $row)
	{
		if (!in_array($row[csf("color_id")], $multicolor_array))
		{
			$multicolor_array[] = $row[csf("color_id")];
		}
		$product_arr[$row[csf("color_id")]]=$row[csf("new_prod_id")];
	}

	$sql="SELECT b.id, a.store_id, b.item_category_id, b.item_group_id, b.sub_group_name, b.item_description, b.item_size, b.unit_of_measure,
	sum((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end)-(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as current_stock
	from inv_transaction a, product_details_master b
	where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type in (1,2,3,4,5,6) and b.item_category_id in (5,6,7,22,23)
	and a.item_category in (5,6,7,22,23) and a.order_id=0
	group by  b.id, a.store_id, b.item_category_id, b.item_group_id, b.sub_group_name, b.item_description, b.item_size, b.unit_of_measure
	order by b.id";

	//$sql="select id, store_id, item_category_id, item_group_id, sub_group_name, item_description, item_code, item_size, unit_of_measure, current_stock from product_details_master where  item_category_id in(5,6,7) and status_active=1 and is_deleted=0";


	//echo $sql;
	$nameArray=sql_select( $sql );
	foreach($nameArray as $row)
	{
		$product_data_arr[$row[csf('id')]]=$row[csf('item_category_id')]."**".$row[csf('item_group_id')]."**".$row[csf('sub_group_name')]."**".$row[csf('item_description')]."**".$row[csf('item_size')]."**".$row[csf('unit_of_measure')]."**".$row[csf('item_code')]."**".$row[csf('store_id')];
	}

	foreach ($multicolor_array as $multicolor_id)
	{
		$new_prod_id=$product_arr[$multicolor_id];
		$prod_data=explode("**",$product_data_arr[$new_prod_id]);

		$item_category_id=$prod_data[0];
		$group_id=$prod_data[1];
		$item_name=$item_group_arr[$prod_data[1]];
		$sub_group_name=$prod_data[2];
		$item_description=$prod_data[3];
		$item_size=$prod_data[4];
		$trim_uom=$prod_data[5];
		$item_code=$prod_data[6];
		$store_id=$prod_data[7];
		?>
        <h3 align="left" id="accordion_h<? echo $multicolor_id; ?>" style="width:910px" class="accordion_h" onClick="fnc_item_details('<? echo $multicolor_id.'__'.$color_arr[$multicolor_id].'__'.$new_prod_id.'__'.$item_category_id.'__'.$group_id.'__'.$item_name.'__'.$sub_group_name.'__'.$item_description.'__'.$item_size.'__'.$trim_uom.'__'.$item_code; ?>')">
            <span id="accordion_h<? echo $multicolor_id; ?>span">+</span><? echo $color_arr[$multicolor_id]; ?>
        </h3>
		<?
	}
	exit();
}

if($action=="item_details")
{
	$data=explode("**",$data);
	$company_id=$data[0];
	$store_id=$data[1];
	$color_id=$data[2];
	$update_id=$data[3];
	
	$sql_lot_variable = sql_select("select auto_transfer_rcv, id from variable_settings_inventory where company_name = $company_id and variable_list = 29 and is_deleted = 0 and status_active = 1");
	$variable_lot=$sql_lot_variable[0][csf("auto_transfer_rcv")];
	
	//echo $variable_lot; die;

	$item_group_arr=return_library_array( "SELECT id,item_name from lib_item_group where status_active=1 and is_deleted=0",'id','item_name');

	$recipe_data_arr=array(); $recipe_prod_id_arr=array(); $product_data_arr=array();
	if($update_id!="")
	{
		//sum(b.req_qny_edit) as qnty
		//$iss_arr=return_library_array("select b.product_id, sum(b.required_qnty) as qnty from inv_issue_master a, dyes_chem_issue_dtls b, dyes_chem_requ_recipe_att c where a.req_no=c.mst_id and a.id=b.mst_id and a.entry_form=5 and a.issue_basis=7 and c.recipe_id=$update_id and b.sub_process=$sub_process_id group by b.product_id",'product_id','qnty');

		/*if($sub_process_id==93 || $sub_process_id==94 || $sub_process_id==95 || $sub_process_id==96 || $sub_process_id==97 || $sub_process_id==98)
		{
			$ration_cond="";
		}
		else
		{*/

		//}

		$ration_cond=" and ratio>0 ";
		$recipeData=sql_select("SELECT id, prod_id, ratio, seq_no, comments, item_lot, store_id from pro_recipe_entry_dtls where mst_id=$update_id and color_id=$color_id and status_active=1 and is_deleted=0 $ration_cond order by seq_no");
		foreach($recipeData as $row)
		{
			$prod_key=$row[csf('prod_id')]."_".$row[csf('store_id')]."_".$row[csf('item_lot')];
			$recipe_data_arr[$prod_key]['ratio']=$row[csf('ratio')];
			$recipe_data_arr[$prod_key]['seq_no']=$row[csf('seq_no')];
			$recipe_data_arr[$prod_key]['id']=$row[csf('id')];
			$recipe_data_arr[$prod_key]['comments']=$row[csf('comments')];
			$recipe_prod_id_arr[$prod_key]=$prod_key;
		}
	}

	//var_dump($recipe_prod_id_arr);
	if ($company_id==0) $company_id =""; else $company_id =" and a.company_id='$company_id'";
	if ($store_id==0)  $store_id="";  else  $store_id=" and b.store_id=$store_id ";

	//$sql="select id, item_category_id, item_group_id, sub_group_name, item_description, item_size, unit_of_measure, current_stock from product_details_master where company_id='$company_id' and store_id=$store_id and item_category_id in(5,6,7) and status_active=1 and is_deleted=0";
	/*$sql="SELECT b.id, a.store_id, b.item_category_id, b.item_group_id, b.sub_group_name, b.item_description, b.item_size, b.unit_of_measure,
	sum((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end)-(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as balance_stock
	from inv_transaction a, product_details_master b
	where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type in (1,2,3,4,5,6) and b.item_category_id in (5,6,7,22,23)
	and a.item_category in (5,6,7,22,23) and a.order_id=0 $company_id $store_id
	group by  b.id, a.store_id, b.item_category_id, b.item_group_id, b.sub_group_name, b.item_description, b.item_size, b.unit_of_measure
	order by b.id";*/

	$sql="SELECT a.id as id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.store_id, b.lot, b.cons_qty as store_stock
	from product_details_master a, inv_store_wise_qty_dtls b
	where a.id=b.prod_id $company_id  $store_id and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	union all
	select a.id as id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.store_id, null as lot,
	sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as store_stock
	from product_details_master a, inv_transaction b
	where a.id=b.prod_id $company_id $store_id and a.item_category_id in(22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	group by a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.store_id
	order by id";

	//echo $sql;die;
	$nameArray=sql_select( $sql );
	foreach($nameArray as $row)
	{
		$prod_key=$row[csf('id')]."_".$row[csf('store_id')]."_".$row[csf('lot')];
		$product_data_arr[$prod_key]=$row[csf('item_category_id')]."**".$row[csf('item_group_id')]."**".$row[csf('sub_group_name')]."**".$row[csf('item_description')]."**".$row[csf('item_size')]."**".$row[csf('unit_of_measure')]."**".$row[csf('store_stock')];
	}
	//echo "<pre>";print_r($product_data_arr);die;

	?>
    <div>
        <table cellpadding="1" cellspacing="1" border="0" width="920" rules="all" class="rpt_table">
            <tr>
                <thead>
                    <th width="30">SL.</th>
                    <th width="80">Item Category</th>
                    <th width="100">Item Group</th>
                    <th width="70">Sub Group</th>
                    <th width="130">Item Description</th>
                    <th width="40">UOM</th>
                    <th width="60">Ratio in %</th>
                    <th width="50">Seq. No</th>
                    <th width="60">Prod. ID</th>
                    <th width="70">Lot</th>
                    <th width="80">Stock Qty</th>
                    <th>Remarks</th>
                </thead>
            </tr>
        </table>
        <div style="width:920px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" id="tbl_list_search">
                <tbody>
				<?

					$i=1; //$max_seq_no='';
					if(count($recipe_prod_id_arr)>0)
					{
						foreach($recipe_prod_id_arr as $prodId)
						{
							
							if($variable_lot==1)
							{
								$lot_popup=''; 
								$place_holder='';
							}
							else 
							{
								$lot_popup='onDblClick="openmypage_itemLot('.$i.')"';
								$place_holder='Browse';
							}
							
							
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$prod_ref=explode("_",$prodId);
							$product_id=$prod_ref[0];
							$product_lot=$prod_ref[2];

							$prodData=explode("**",$product_data_arr[$prodId]);
							$item_category_id=$prodData[0];
							$item_group_id=$prodData[1];
							$sub_group_name=$prodData[2];
							$item_description=$prodData[3];
							$item_size=$prodData[4];
							$unit_of_measure=$prodData[5];
							$current_stock=$prodData[6];

							$dtls_id=$recipe_data_arr[$prodId]['id'];
							$ratio =$recipe_data_arr[$prodId]['ratio'];
							$seq_no=$recipe_data_arr[$prodId]['seq_no'];
							$comments=$recipe_data_arr[$prodId]['comments'];
							$bgcolor="yellow";

							$disbled="";
							//$iss_qty=$iss_arr[$prodId];
							if($update_id!="" && $ratio>0 && $iss_qty>0)
							{
								$disbled="disabled='disabled'";
							}

							?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
                                <td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i.'a'; ?></td>
                                <td width="80" id="item_category_<? echo $i; ?>" style="word-break:break-all;"><p><? echo $item_category[$item_category_id]; ?></p></td>
                                <td width="100" id="item_group_id_<? echo $i; ?>" style="word-break:break-all;"><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
                                <td width="70" id="sub_group_name_<? echo $i; ?>" style="word-break:break-all;"><p><? echo $sub_group_name; ?>&nbsp;</p></td>
                                <td width="130" id="item_description_<? echo $i; ?>" style="word-break:break-all;"><p><? echo $item_description." ".$item_size; ?></p></td>
                                <td width="40" align="center" id="uom_<? echo $i; ?>" style="word-break:break-all;"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
                                <td width="60" align="center" id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px;"  value="<? echo $ratio; ?>" onBlur="color_row(<? echo $i; ?>); seq_no_val(<? echo $i; ?>); "  <? echo $disbled; ?>></td>
                                <td width="50" align="center" id="seqno_<? echo $i; ?>"><input type="text" name="txt_seqno[]" id="txt_seqno_<? echo $i; ?>" class="text_boxes_numeric" style="width:35px" value="<? echo $seq_no; ?>" onBlur="row_sequence(<? echo $i; ?>);"></td>
                                <td width="60" align="center" id="product_id_<? echo $i; ?>"><? echo $product_id; ?><input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" value="<? echo $product_id; ?>"></td>
                                <td width="70" align="center" id="td_lot_<? echo $i; ?>" style="word-break:break-all;">  
                               <input type="text" name="txt_lot[]"  class="text_boxes" style="width:70px" id="txt_lot_<? echo $i; ?>" <? echo $lot_popup; ?> placeholder="<? echo $place_holder; ?>" value="<? echo $product_lot; ?>">  
                                 
                                </td>
                                <td width="80" align="right" id="stock_qty_<? echo $i; ?>"><? echo number_format($current_stock,2,'.',''); ?><input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $dtls_id; ?>"></td>
                                <td align="center" id="comments_<? echo $i; ?>"><input type="text" name="txt_comments[]" id="txt_comments_<? echo $i; ?>" class="text_boxes" style="width:100px" value="<? echo $comments; ?>"></td>
                            </tr>
							<?
							//$max_seq_no[]=$selectResult[csf('seq_no')];
							$i++;
						}
					}

					foreach($product_data_arr as $prodId=>$data)
					{
						if(!in_array($prodId,$recipe_prod_id_arr))
						{
							//echo $prodId.test;
							
							if($variable_lot==1)
							{
								$lot_popup=''; 
								$place_holder='';
							}
							else 
							{
								$lot_popup='onDblClick="openmypage_itemLot('.$i.')"';
								$place_holder='Browse';
							}
							$prod_ref=explode("_",$prodId);
							$product_id=$prod_ref[0];
							$product_lot=$prod_ref[2];
							$prodData=explode("**",$data);
							$item_category_id=$prodData[0];
							$item_group_id=$prodData[1];
							$sub_group_name=$prodData[2];
							$item_description=$prodData[3];
							$item_size=$prodData[4];
							$unit_of_measure=$prodData[5];
							$current_stock=$prodData[6];

							$ratio=''; $seq_no=''; $disbled="";$comments='';
							if($current_stock>0)
							{
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
									<td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i.'b'; ?></td>
									<td width="80" id="item_category_<? echo $i; ?>" style="word-break:break-all;"><p><? echo $item_category[$item_category_id]; ?></p></td>
									<td width="100" id="item_group_id_<? echo $i; ?>" style="word-break:break-all;"><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
									<td width="70" id="sub_group_name_<? echo $i; ?>" style="word-break:break-all;"><p><? echo $sub_group_name; ?>&nbsp;</p></td>
									<td width="130" id="item_description_<? echo $i; ?>" style="word-break:break-all;"><p><? echo $item_description." ".$item_size; ?></p></td>
									<td width="40" align="center" id="uom_<? echo $i; ?>" style="word-break:break-all;"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
									<td width="60" align="center" id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px;"  value="<? echo $ratio; ?>" onBlur="color_row(<? echo $i; ?>); seq_no_val(<? echo $i; ?>); "  <? echo $disbled; ?>></td>
									<td width="50" align="center" id="seqno_<? echo $i; ?>"><input type="text" name="txt_seqno[]" id="txt_seqno_<? echo $i; ?>" class="text_boxes_numeric" style="width:35px" value="<? echo $seq_no; ?>" onBlur="row_sequence(<? echo $i; ?>);"></td>
									<td width="60" align="center" id="product_id_<? echo $i; ?>"><? echo $product_id; ?><input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" value="<? echo $product_id; ?>"></td>
                                    <td width="70" align="center" id="td_lot_<? echo $i; ?>" style="word-break:break-all;">
  								 <input type="text" name="txt_lot[]"  class="text_boxes" style="width:70px" id="txt_lot_<? echo $i; ?>" <? echo $lot_popup; ?> placeholder="<? echo $place_holder; ?>" value="<? echo $product_lot; ?>">
                                     </td>
									<td width="80" align="right" id="stock_qty_<? echo $i; ?>"><? echo number_format($current_stock,2,'.',''); ?><input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value=""></td>
									<td align="center" id="comments_<? echo $i; ?>"><input type="text" name="txt_comments[]" id="txt_comments_<? echo $i; ?>" class="text_boxes" style="width:100px" value="<? echo $comments; ?>"></td>
								</tr>
								<?
								//$max_seq_no[]=$selectResult[csf('seq_no')];
								$i++;
							}
						}
					}
			//	}
				?>
                </tbody>
            </table>
        </div>
    </div>
	<?
	exit();
}

if ($action == "save_update_delete")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$color_arr = return_library_array("SELECT id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	//echo '10**';die;
	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0)
		{
			mysql_query("BEGIN");
		}

		$recipe_update_id = ''; $new_array_color=array();

		if(str_replace("'", "", $copy_id) == 2)
		{
			if (str_replace("'", "", $update_id) == "")
			{
				if($db_type==0) $date_cond=" YEAR(insert_date)";
				else if($db_type==2) $date_cond="to_char(insert_date,'YYYY')";

				$new_sys_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'AOPRE', date("Y",time()), 5, "select recipe_no_prefix, recipe_no_prefix_num from pro_recipe_entry_mst where company_id=$cbo_company_id and entry_form=285 and $date_cond=".date('Y',time())." order by id DESC", "recipe_no_prefix", "recipe_no_prefix_num" ));

				$id = return_next_id("id", "pro_recipe_entry_mst", 1);

				$field_array = "id, entry_form, recipe_no_prefix, recipe_no_prefix_num, recipe_no, company_id, batch_id , location_id, recipe_description, recipe_for, recipe_date, po_id, job_no, buyer_id, buyer_po_ids, gmts_item, body_part, color_id, remarks, store_id, within_group, total_water_ratio, inserted_by, insert_date, status_active, is_deleted";
				//echo $txt_liquor;
				$data_array = "(".$id.",285,'".$new_sys_no[1]."','".$new_sys_no[2]."','".$new_sys_no[0]."',".$cbo_company_id.",".$txt_batch_id.",".$cbo_location.",".$txt_recipe_des.",".$cbo_recipe_for.",".$txt_recipe_date.",".$txt_order_id.",".$hid_job_no.",".$cbo_buyer_name.",".$txtbuyerPoId.",".$hid_item_id.",".$hid_bodypart_id.",".$hidden_batch_color_id.",". $txt_remarks.",". $cbo_store_id.",". $cbo_within_group.",". $txt_water_ratio.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				//$rID=sql_insert("pro_recipe_entry_mst",$field_array,$data_array,0);
				//if($rID) $flag=1; else $flag=0;
				$recipe_update_id = $id;
				$recipe_no=$new_sys_no[0];
			}
			else
			{
				$field_array_update = "batch_id*location_id*recipe_description*recipe_for*recipe_date*po_id*job_no*buyer_id*buyer_po_ids*gmts_item*body_part*color_id*remarks*store_id*within_group*total_water_ratio*updated_by*update_date";

				$data_array_update = $txt_batch_id . "*" . $cbo_location . "*" . $txt_recipe_des . "*" . $cbo_recipe_for . "*" . $txt_recipe_date . "*" . $txt_order_id . "*".$hid_job_no."*" . $cbo_buyer_name . "*" . $txtbuyerPoId . "*" . $hid_item_id . "*" . $hid_bodypart_id . "*" . $txt_pocolor_id . "*" . $txt_remarks . "*" . $cbo_store_id . "*" . $cbo_within_group . "*" . $txt_water_ratio . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

				//$rID=sql_update("pro_recipe_entry_mst",$field_array_update,$data_array_update,"id",$update_id,1);
				//if($rID) $flag=1; else $flag=0;
				$recipe_update_id = str_replace("'", "", $update_id);
				$recipe_no=str_replace("'", "", $txt_sys_id);
			}

			if(str_replace("'","",$cbo_recipe_for)==3)
			{
				//echo '10**'.$cbo_recipe_for.'='.str_replace("'","",$hidd_newprod_id);die;
				if(str_replace("'","",$hidd_newprod_id)=="")
				{
					if($db_type==2)
					{
						$duplicate_cond='';
						if(str_replace("'","",$txt_subgroup_name)=='') $duplicate_cond.=" and sub_group_name is null"; else $duplicate_cond.=" and sub_group_name=$txt_subgroup_name";
						if(str_replace("'","",$txt_description)=='') $duplicate_cond .=" and item_description is null"; else $duplicate_cond.=" and item_description=$txt_description";
						if(str_replace("'","",$txt_item_size)=='') $duplicate_cond .=" and item_size is null"; else $duplicate_cond.=" and item_size=$txt_item_size";

						//$duplicate = is_duplicate_field("id","product_details_master","company_id=$cbo_company_id and item_category_id=$cbo_item_category and item_group_id=$item_group_id $duplicate_cond and is_deleted=0");

						$old_prod_id=return_field_value("id","product_details_master","company_id=$cbo_company_id and store_id=$cbo_store_id and item_category_id=$cbo_item_category and item_group_id=$item_group_id $duplicate_cond and is_deleted=0");

					}
					else
					{
						//$duplicate = is_duplicate_field("id","product_details_master","company_id=$cbo_company_id and item_category_id=$cbo_item_category and item_group_id=$item_group_id and sub_group_name=$txt_subgroup_name and item_description=$txt_description and item_size=$txt_item_size and is_deleted=0");

						$old_prod_id=return_field_value("id","product_details_master","company_id=$cbo_company_id and store_id=$cbo_store_id and item_category_id=$cbo_item_category and item_group_id=$item_group_id and sub_group_name=$txt_subgroup_name and item_description=$txt_description and item_size=$txt_item_size and is_deleted=0");
					}

					if($old_prod_id=="")
					{
						$item_group_arr=return_library_array( "select id,item_name from lib_item_group",'id','item_name');
						$id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
						$productname = $item_group_arr[str_replace("'", '', $item_group_id)]." ".str_replace("'", '', $txt_description)." ".str_replace("'", '', $txt_item_size);

						$field_prod_array="id, company_id, store_id, item_category_id, entry_form, item_group_id, sub_group_name, item_code, item_description, product_name_details, item_size,  unit_of_measure, inserted_by, insert_date, status_active, is_deleted";
						$data_prod_array="(".$id.",".$cbo_company_id.",".$cbo_store_id.",".$cbo_item_category.",285,".$item_group_id.",".$txt_subgroup_name.",".$hidd_group_code.",".$txt_description.",'".$productname."',".$txt_item_size.",".$hidd_cons_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
						$ins_prod_id=$id;
					}
				}
				else
				{
					$old_prod_id=str_replace("'","",$hidd_newprod_id);
				}
			}
			else
			{
				$old_prod_id=str_replace("'","",$hidd_newprod_id);
			}

			$new_prod_id=0;
			if($ins_prod_id!="")
			{
				$new_prod_id=$ins_prod_id;
			}
			else
			{
				$new_prod_id=$old_prod_id;
			}
			//echo '10**'.$new_prod_id.'='.str_replace("'","",$ins_prod_id); die;

			$field_array_dtls = "id, mst_id, color_id, prod_id, comments, ratio, seq_no, new_prod_id, water_ratio, inserted_by, insert_date, status_active, is_deleted, store_id, item_lot";
			$dtls_id = return_next_id("id", "pro_recipe_entry_dtls", 1);
			//$color_id = return_id($txt_multi_color, $color_arr, "lib_color", "id,color_name");

			if (str_replace("'", "", trim($txt_multi_color)) != "") {
				if (!in_array(str_replace("'", "", trim($txt_multi_color)),$new_array_color)){
					$color_id = return_id( str_replace("'", "", trim($txt_multi_color)), $color_arr, "lib_color", "id,color_name","285");
					$new_array_color[$color_id]=str_replace("'", "", trim($txt_multi_color));
				}
				else $color_id =  array_search(str_replace("'", "", trim($txt_multi_color)), $new_array_color);
			} else $color_id = 0;

			$rece_sql="SELECT a.batch_id,b.ratio,b.color_id,b.water_ratio  from pro_recipe_entry_mst a ,pro_recipe_entry_dtls b  where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.entry_form=285 and a.id =$update_id";
			foreach(sql_select($rece_sql) as $vals)
			{
				$receipe_arr[$vals[csf("batch_id")]][$vals[csf("color_id")]]['ratio']+=$vals[csf("ratio")];
				$receipe_arr[$vals[csf("batch_id")]][$vals[csf("color_id")]]['water_ratio']=$vals[csf("water_ratio")];
			}
			//echo "10**";echo"<pre>";//print_r($receipe_arr);
			if(($receipe_arr[str_replace("'","",$txt_batch_id)][$color_id]['ratio']+$receipe_arr[str_replace("'","",$txt_batch_id)][$color_id]['water_ratio'])>=100)
			{
				echo "555";die;
			}
			for ($i = 1; $i <= $total_row; $i++)
			{
				$product_id = "product_id_" . $i;
				$product_lot = "txt_lot_" . $i;
				$txt_ratio = "txt_ratio_" . $i;
				$txt_comments = "txt_comments_" . $i;
				$txt_seqno = "txt_seqno_" . $i;
				if ($i != 1) $data_array_dtls .= ",";
				$data_array_dtls .= "(".$dtls_id.",".$recipe_update_id.",'".$color_id."','".str_replace("'","",$$product_id)."','".str_replace("'","",$$txt_comments)."','".str_replace("'","",$$txt_ratio)."','".str_replace("'","",$$txt_seqno)."','".$new_prod_id."','".str_replace("'","",$txt_water_ratio)."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,".$cbo_store_id.",'".str_replace("'","",$$product_lot)."')";

				$dtls_id = $dtls_id + 1;
			}
		}
		else
		{
			if (str_replace("'", "", $update_id) == "")
			{
				if($db_type==0) $date_cond=" YEAR(insert_date)";
				else if($db_type==2) $date_cond="to_char(insert_date,'YYYY')";

				$new_sys_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'AOPRE', date("Y",time()), 5, "select recipe_no_prefix, recipe_no_prefix_num from pro_recipe_entry_mst where company_id=$cbo_company_id and entry_form=285 and $date_cond=".date('Y',time())." order by id DESC", "recipe_no_prefix", "recipe_no_prefix_num" ));

				$id = return_next_id("id", "pro_recipe_entry_mst", 1);

				$field_array = "id, entry_form, recipe_no_prefix, recipe_no_prefix_num, recipe_no, company_id, batch_id , location_id, recipe_description, recipe_for, recipe_date, po_id, job_no, buyer_id, buyer_po_ids, gmts_item, body_part, color_id, remarks, store_id, within_group, total_water_ratio, inserted_by, insert_date, status_active, is_deleted";
				//echo $txt_liquor;
				$data_array = "(".$id.",285,'".$new_sys_no[1]."','".$new_sys_no[2]."','".$new_sys_no[0]."',".$cbo_company_id.",".$txt_batch_id.",".$cbo_location.",".$txt_recipe_des.",".$cbo_recipe_for.",".$txt_recipe_date.",".$txt_order_id.",".$hid_job_no.",".$cbo_buyer_name.",".$txtbuyerPoId.",".$hid_item_id.",".$hid_bodypart_id.",".$hidden_batch_color_id.",". $txt_remarks.",". $cbo_store_id.",". $cbo_within_group.",". $txt_water_ratio.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				//$rID=sql_insert("pro_recipe_entry_mst",$field_array,$data_array,0);
				//if($rID) $flag=1; else $flag=0;
				$recipe_update_id = $id;
				$recipe_no=$new_sys_no[0];
			}

			$field_array_dtls = "id, mst_id, color_id, prod_id, comments, ratio, seq_no, new_prod_id, inserted_by, insert_date, status_active, is_deleted, store_id, item_lot, water_ratio";
			$dtls_id = return_next_id("id", "pro_recipe_entry_dtls", 1);
			$sql = "select id, mst_id, color_id, prod_id, comments, ratio, seq_no, new_prod_id, store_id, item_lot, water_ratio from pro_recipe_entry_dtls where mst_id=$update_id_check and status_active=1 and is_deleted=0 order by id";
			//echo "10**".$sql; die;
			$nameArray = sql_select($sql);
			$tot_row = count($nameArray);
			$i = 1; $data_array_dtls= '';
			foreach ($nameArray as $row)
			{
				if ($i != 1) $data_array_dtls .= ",";
				$data_array_dtls.="(".$dtls_id.",".$recipe_update_id.",'".$row[csf('color_id')]."','".$row[csf('prod_id')]."','".$row[csf('comments')]."','".$row[csf('ratio')]."','".$row[csf('seq_no')]."','".$row[csf('new_prod_id')]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,'".$row[csf('store_id')]."','".$row[csf('item_lot')]."','".$row[csf('water_ratio')]."')";
				$dtls_id=$dtls_id+1;
				$i++;
			}
		}
		$rID = $rID2 = $rID3 = true;
		if (str_replace("'", "", $update_id) == "")
		{
			$rID = sql_insert("pro_recipe_entry_mst", $field_array, $data_array, 0);
		}
		else
		{
			$rID = sql_update("pro_recipe_entry_mst", $field_array_update, $data_array_update, "id", $update_id, 1);
		}
		//echo "10**insert into pro_recipe_entry_dtls (".$field_array_dtls.") Values ".$data_array_dtls."";die;
		$rID2 = sql_insert("pro_recipe_entry_dtls", $field_array_dtls, $data_array_dtls, 1);
		if(str_replace("'","",$cbo_recipe_for)==3)
		{
			if(str_replace("'","",$new_prod_id)!="")
			{
				if(str_replace("'","",$data_prod_array)!="")
				{
					$rID3 = sql_insert("product_details_master", $field_prod_array, $data_prod_array, 0);
					if ($rID3==1 && $flag==1) $flag = 1; else $flag = 0;
				}
			}
		}
		//echo "10**".$rID.'=='.$rID2.'=='.$rID3.'=='.$flag;die;
		if ($db_type == 0)
		{
			if($rID && $rID2 && $rID3)
			{
				mysql_query("COMMIT");
				echo "0**" . $recipe_update_id . "**" . $recipe_no . "**" .str_replace("'","",$cbo_store_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "5**0**0";
			}
		}
		else if ($db_type == 2 || $db_type == 1)
		{
			if ($rID && $rID2 && $rID3)
			{
				oci_commit($con);
				echo "0**" . $recipe_update_id . "**" . $recipe_no . "**" .str_replace("'","",$cbo_store_id);
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation == 1)   // Update Here
	{
		$con = connect();
		if ($db_type == 0)
		{
			mysql_query("BEGIN");
		}

		$req_number=return_field_value( "requ_no", "dyes_chem_issue_requ_mst"," recipe_id=$update_id and status_active=1 and is_deleted=0 and entry_form=221");
		if($req_number){
			echo "emblRequ**".str_replace("'","",$update_id)."**".$req_number;
			disconnect($con); die;
		}

		$prod_number=return_field_value( "sys_no", "subcon_embel_production_dtls"," recipe_id=$update_id and status_active=1 and is_deleted=0");
		if($prod_number){
			echo "emblProduction**".str_replace("'","",$txt_job_no)."**".$prod_number;
			disconnect($con); die;
		}

		$field_array_update = "location_id*recipe_description*recipe_for*recipe_date*po_id*job_no*buyer_id*buyer_po_ids*color_id*remarks*total_water_ratio*updated_by*update_date";

		$data_array_update = $cbo_location . "*" . $txt_recipe_des . "*" . $cbo_recipe_for . "*" . $txt_recipe_date . "*" . $txt_order_id . "*".$hid_job_no."*" . $cbo_buyer_name . "*" . $txtbuyerPoId . "*" . $hidden_batch_color_id . "*" . $txt_remarks . "*" . $txt_water_ratio . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

		//$rID=sql_update("pro_recipe_entry_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		//if($rID) $flag=1; else $flag=0;
		$recipe_update_id = str_replace("'", "", $update_id);
		$recipe_no=str_replace("'", "", $txt_sys_id);
		$new_array_color=array();

		//echo "10**";
		if(str_replace("'","",$cbo_recipe_for)==3)
		{
			//echo '10**'.$cbo_recipe_for.'='.str_replace("'","",$hidd_newprod_id);die;
			if(str_replace("'","",$hidd_newprod_id)=="")
			{
				if($db_type==2)
				{
					$duplicate_cond='';
					if(str_replace("'","",$txt_subgroup_name)=='') $duplicate_cond.=" and sub_group_name is null"; else $duplicate_cond.=" and sub_group_name=$txt_subgroup_name";
					if(str_replace("'","",$txt_description)=='') $duplicate_cond .=" and item_description is null"; else $duplicate_cond.=" and item_description=$txt_description";
					if(str_replace("'","",$txt_item_size)=='') $duplicate_cond .=" and item_size is null"; else $duplicate_cond.=" and item_size=$txt_item_size";

					//$duplicate = is_duplicate_field("id","product_details_master","company_id=$cbo_company_id and item_category_id=$cbo_item_category and item_group_id=$item_group_id $duplicate_cond and is_deleted=0");
					//echo "select id from product_details_master where company_id=$cbo_company_id and item_category_id=$cbo_item_category and item_group_id=$item_group_id $duplicate_cond and is_deleted=0";
					$old_prod_id=return_field_value("id","product_details_master","company_id=$cbo_company_id and store_id=$cbo_store_id and item_category_id=$cbo_item_category and item_group_id=$item_group_id $duplicate_cond and is_deleted=0");

				}
				else
				{
					//$duplicate = is_duplicate_field("id","product_details_master","company_id=$cbo_company_id and item_category_id=$cbo_item_category and item_group_id=$item_group_id and sub_group_name=$txt_subgroup_name and item_description=$txt_description and item_size=$txt_item_size and is_deleted=0");

					$old_prod_id=return_field_value("id","product_details_master","company_id=$cbo_company_id and store_id=$cbo_store_id and item_category_id=$cbo_item_category and item_group_id=$item_group_id and sub_group_name=$txt_subgroup_name and item_description=$txt_description and item_size=$txt_item_size and is_deleted=0");
				}
				//echo "10**".$old_prod_id.'='; die;
				if($old_prod_id=="")
				{
					$item_group_arr=return_library_array( "select id,item_name from lib_item_group",'id','item_name');
					$id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
					$productname = $item_group_arr[str_replace("'", '', $item_group_id)]." ".str_replace("'", '', $txt_description)." ".str_replace("'", '', $txt_item_size);

					$field_prod_array="id, company_id, store_id, item_category_id, entry_form, item_group_id, sub_group_name, item_code, item_description, product_name_details, item_size,  unit_of_measure, inserted_by, insert_date, status_active, is_deleted";
					$data_prod_array="(".$id.",".$cbo_company_id.", ".$cbo_store_id.",".$cbo_item_category.",285,".$item_group_id.",".$txt_subgroup_name.",".$hidd_group_code.",".$txt_description.",'".$productname."',".$txt_item_size.",".$hidd_cons_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$ins_prod_id=$id;
				}
			}
			else
			{
				$old_prod_id=str_replace("'","",$hidd_newprod_id);
			}
		}
		else
		{
			$old_prod_id=str_replace("'","",$hidd_newprod_id);
		}
		//echo '10**'.$old_prod_id.'='.str_replace("'","",$ins_prod_id); die;

		/*if($duplicate==1)
		{
			echo "11**Duplicate Product is Not Allow in Same Return Number.";
			die;
		}*/
		$new_prod_id="";
		if($ins_prod_id!="")
		{
			$new_prod_id=$ins_prod_id;
		}
		else
		{
			$new_prod_id=$old_prod_id;
		}
		//echo '10**'.$new_prod_id.'='.str_replace("'","",$old_prod_id); die;


		$field_array_dtls = "id, mst_id, store_id, color_id, prod_id, comments, ratio, seq_no, new_prod_id, water_ratio,item_lot, inserted_by, insert_date, status_active, is_deleted";
		$field_array_dtls_update = "color_id*prod_id*comments*ratio*seq_no*new_prod_id*water_ratio*item_lot*updated_by*update_date";
		$dtls_id = return_next_id("id", "pro_recipe_entry_dtls", 1);
		//$color_id = return_id($txt_multi_color, $color_arr, "lib_color", "id,color_name","285");
		//$new_array_color=array();

		if (str_replace("'", "", trim($txt_multi_color)) != "") {
			if (!in_array(str_replace("'", "", trim($txt_multi_color)),$new_array_color)){
				$color_id = return_id( str_replace("'", "", trim($txt_multi_color)), $color_arr, "lib_color", "id,color_name","285");
				$new_array_color[$color_id]=str_replace("'", "", trim($txt_multi_color));
			}
			else $color_id =  array_search(str_replace("'", "", trim($txt_multi_color)), $new_array_color);
		} else $color_id = 0;

		$rece_sql="SELECT a.batch_id,b.ratio,b.color_id,b.water_ratio  from pro_recipe_entry_mst a ,pro_recipe_entry_dtls b  where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.entry_form=285 and a.id !=$update_id";
		foreach(sql_select($rece_sql) as $vals)
		{
			$receipe_arr[$vals[csf("batch_id")]][$vals[csf("color_id")]]['ratio']+=$vals[csf("ratio")];
			$receipe_arr[$vals[csf("batch_id")]][$vals[csf("color_id")]]['water_ratio']=$vals[csf("water_ratio")];
		}
		//echo "10**";echo"<pre>";//print_r($receipe_arr);
		if(($receipe_arr[str_replace("'","",$txt_batch_id)][$color_id]['ratio']+$receipe_arr[str_replace("'","",$txt_batch_id)][$color_id]['water_ratio'])>=100)
		{
			echo "555";disconnect($con); die;
		}
		
		for ($i = 1; $i <= $total_row; $i++)
		{
			$product_id = "product_id_" . $i;
			$txt_comments = "txt_comments_" . $i;
			$txt_ratio = "txt_ratio_" . $i;
			$updateIdDtls = "updateIdDtls_" . $i;
			$txt_seqno = "txt_seqno_" . $i;
			$product_lot = "txt_lot_" . $i;

			if (str_replace("'", "", $$updateIdDtls) != "")
			{
				if(str_replace("'", "", $$txt_seqno)=='')
				{
					$statusUpdateId.=str_replace("'", '', $$updateIdDtls).",";
				}
				else
				{
					$id_arr[] = str_replace("'", '', $$updateIdDtls);
					$data_array_dtls_update[str_replace("'", '', $$updateIdDtls)] = explode("*", (str_replace("'", "", $color_id) . "*".str_replace("'", "", $$product_id) . "*'" . str_replace("'", "", $$txt_comments) . "'*" . str_replace("'", "", $$txt_ratio) . "*" . str_replace("'", "", $$txt_seqno) . "*'" . str_replace("'", "", $new_prod_id) . "'*'" . str_replace("'", "", $txt_water_ratio) . "'*'" . str_replace("'", "", $$product_lot) . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
				}
			}
			else
			{
				if ($data_array_dtls != '') $data_array_dtls .= ",";
				$data_array_dtls .= "(" . $dtls_id . "," . $recipe_update_id . ",".$cbo_store_id.",'" . $color_id . "','" . str_replace("'", "", $$product_id) . "','" . str_replace("'", "", $$txt_comments)."',".str_replace("'", "", $$txt_ratio) . ",'" . str_replace("'", "", $$txt_seqno) . "','".$new_prod_id."',".$txt_water_ratio.",'".str_replace("'","",$$product_lot)."'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";

				$dtls_id = $dtls_id + 1;
			}
		}

		// Update test all
		$rID = $rID2 = $rID3 = $rID4 = $rID5 = true;
		$rID = sql_update("pro_recipe_entry_mst", $field_array_update, $data_array_update, "id", $update_id, 1);
		if ($data_array_dtls_update != "")
		{
			$rID2 = execute_query(bulk_update_sql_statement("pro_recipe_entry_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr), 1);
		}

		if ($data_array_dtls != "")
		{
			$rID3 = sql_insert("pro_recipe_entry_dtls", $field_array_dtls, $data_array_dtls, 1);
		}
		if(str_replace("'","",$cbo_recipe_for)==3)
		{
			if(str_replace("'","",$new_prod_id)!="")
			{
				if(str_replace("'","",$data_prod_array)!="")
				{
					$rID4 = sql_insert("product_details_master", $field_prod_array, $data_prod_array, 0);
				}
			}
		}
		$statusUpdateId=chop($statusUpdateId,",");
		if($statusUpdateId!="")
		{
			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";

			$rID5=sql_multirow_update("pro_recipe_entry_dtls",$field_array_status,$data_array_status,"id",$statusUpdateId,0);
		}
		//echo "10**".bulk_update_sql_statement("pro_recipe_entry_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr); die;
		//echo "10**insert into pro_recipe_entry_dtls (".$field_array_dtls.") Values ".$data_array_dtls."";die;
		//echo "10**insert into product_details_master (".$field_prod_array.") Values ".$data_prod_array."";die;
		//echo "10**".$rID.'='.$rID2.'='.$rID3.'='.$rID4.'='.$rID5; die;
		//print_r( $data_array_dtls_update);
		//die;
		if ($db_type == 0)
		{
			if ($rID && $rID2 && $rID3 && $rID4 && $rID5)
			{
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", '', $update_id) . "**" . $recipe_no  . "**" .str_replace("'","",$cbo_store_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**0**1";
			}
		}
		else if ($db_type == 2 || $db_type == 1)
		{
			if ($rID && $rID2 && $rID3 && $rID4 && $rID5)
			{
				oci_commit($con);
				echo "1**" . str_replace("'", '', $update_id) . "**" . $recipe_no  . "**" .str_replace("'","",$cbo_store_id);
			}
			else
			{
				oci_rollback($con);
				echo "6**0**1";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation == 2)   // Delete Here
	{
		$con = connect();
		if ($db_type == 0)
		{
			mysql_query("BEGIN");
		}

		$req_number=return_field_value( "requ_no", "dyes_chem_issue_requ_mst"," recipe_id=$update_id and status_active=1 and is_deleted=0 and entry_form=221");
		if($req_number){
			echo "emblRequ**".str_replace("'","",$update_id)."**".$req_number;
			disconnect($con); die;
		}

		$prod_number=return_field_value( "sys_no", "subcon_embel_production_dtls"," recipe_id=$update_id and status_active=1 and is_deleted=0");
		if($prod_number){
			echo "emblProduction**".str_replace("'","",$txt_job_no)."**".$prod_number;
			disconnect($con); die;
		}

		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$flag = 1;
		$rID=sql_update("pro_recipe_entry_mst",$field_array,$data_array,"id",$update_id,0);
		if ($rID==1 && $flag==1) $flag = 1; else $flag = 0;
		$rID2=sql_update("pro_recipe_entry_dtls",$field_array,$data_array,"mst_id",$update_id,0);
		if ($rID2==1 && $flag==1) $flag = 1; else $flag = 0;
		//echo "10**".$rID.'='.$rID2.'='.$rID3;
		//print_r( $data_array_dtls_update);
		//disconnect($con); die;
		if ($db_type == 0)
		{
			if ($flag == 1)
			{
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", '', $update_id) . "**" . $recipe_no;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**0**1";
			}
		}
		else if ($db_type == 2 || $db_type == 1)
		{
			if ($flag == 1)
			{
				oci_commit($con);
				echo "2**" . str_replace("'", '', $update_id) . "**" . $recipe_no;
			}
			else
			{
				oci_rollback($con);
				echo "6**0**1";
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "recipe_entry_print")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$mst_id = $data[1];
	$com_id = $data[0];
	$company_library = return_library_array("SELECT id, company_name from lib_company where status_active =1 and is_deleted=0", "id", "company_name");
	$buyer_library = return_library_array("SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0", "id", "buyer_name");
	$item_group_arr = return_library_array("SELECT id,item_name from lib_item_group where status_active =1 and is_deleted=0", 'id', 'item_name');
	$color_arr = return_library_array("SELECT id, color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');
	$imge_arr=return_library_array( "SELECT master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$location_arr = return_library_array("SELECT id, location_name from lib_location where status_active =1 and is_deleted=0", 'id', 'location_name');

	$order_arr = array();
	$embl_sql ="SELECT a.subcon_job, a.within_group, b.id,a.aop_reference, b.order_no, b.buyer_buyer, b.buyer_po_no, b.buyer_style_ref,b.print_type from subcon_ord_mst a, subcon_ord_dtls b where a.entry_form=278 and a.subcon_job=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$embl_sql_res=sql_select($embl_sql);
	foreach ($embl_sql_res as $row)
	{
		$order_arr[$row[csf("id")]]['job']=$row[csf("subcon_job")];
		$order_arr[$row[csf("id")]]['po']=$row[csf("order_no")];
		$order_arr[$row[csf("id")]]['ref']=$row[csf("aop_reference")];
		$order_arr[$row[csf("id")]]['within_group']=$row[csf("within_group")];
		$order_arr[$row[csf("id")]]['buyer_buyer'] =$row[csf("buyer_buyer")];
		$order_arr[$row[csf("id")]]['buyer_po_no'] =$row[csf("buyer_po_no")];
		$order_arr[$row[csf("id")]]['buyer_style_ref'] =$row[csf("buyer_style_ref")];
		$order_arr[$row[csf("id")]]['print_type'] =$print_type[$row[csf("print_type")]];
	}
	unset($embl_sql_res);

	/*$buyer_po_arr=array();
	$po_sql ="Select a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);*/

	$sql_mst = "SELECT  a.id, a.recipe_no, a.company_id, a.location_id, a.recipe_description, a.recipe_for, a.recipe_date, b.within_group, a.po_id, a.job_no, a.buyer_id, a.buyer_po_ids, a.gmts_item, a.body_part, a.embl_name, a.embl_type, a.color_id, a.batch_id, a.remarks, a.total_water_ratio, b.batch_no,b.batch_weight from pro_recipe_entry_mst a, pro_batch_create_mst b where  a.batch_id=b.id and a.id='$mst_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	//echo $sql_mst;
	$dataArray = sql_select($sql_mst); $party_name="";
	if($dataArray[0][csf('within_group')]==1) $party_name=$company_library[$dataArray[0][csf('buyer_id')]];
	else if($dataArray[0][csf('within_group')]==2) $party_name=$buyer_library[$dataArray[0][csf('buyer_id')]];

	$job_no=$po_no=$buyer_po_no=$buyer_style_ref=$within_group=$buyer_buyer=$aop_ref='';
	$order_id=array_unique(explode(",",$dataArray[0][csf('po_id')]));
	foreach($order_id as $val)
	{
		if($job_no=="") $job_no=$order_arr[$val]['job']; else $job_no.=", ".$order_arr[$val]['job'];
		if($po_no=="") $po_no=$order_arr[$val]['po']; else $po_no.=", ".$order_arr[$val]['po'];
		if($buyer_po_no=="") $buyer_po_no=$order_arr[$val]['buyer_po_no']; else $buyer_po_no.=", ".$order_arr[$val]['buyer_po_no'];
		if($buyer_style_ref=="") $buyer_style_ref=$order_arr[$val]['buyer_style_ref']; else $buyer_style_ref.=", ".$order_arr[$val]['buyer_style_ref'];
		if($aop_ref=="") $aop_ref=$order_arr[$val]['ref']; else $aop_ref.=", ".$order_arr[$val]['ref'];
		//echo $dataArray[0][csf('within_group')].'==';
		if($dataArray[0][csf('within_group')]==1)
		{
			if($buyer_buyer=="") $buyer_buyer=$buyer_library[$order_arr[$val]['buyer_buyer']]; else $buyer_buyer.=", ".$buyer_library[$order_arr[$val]['buyer_buyer']];
		}
		else
		{
			if($buyer_buyer=="") $buyer_buyer=$order_arr[$val]['buyer_buyer']; else $buyer_buyer.=", ".$order_arr[$val]['buyer_buyer'];
		}

		if($print_type_str=="") $print_type_str=$order_arr[$val]['print_type']; else $print_type_str.=", ".$order_arr[$val]['print_type'];
	}
	//echo "<pre>";
	//print_r($all_ref_arr);
	$job_no = implode(",", array_unique(explode(", ",$job_no)));
	$po_no = implode(",", array_unique(explode(", ",$po_no)));
	$buyer_po_no = implode(",", array_unique(explode(", ",$buyer_po_no)));
	$buyer_style_ref = implode(",", array_unique(explode(", ",$buyer_style_ref)));
	$buyer_buyer = implode(",", array_unique(explode(", ",$buyer_buyer)));
	$aop_ref = implode(",", array_unique(explode(", ",$aop_ref)));
	$print_type_str = implode(",", array_unique(explode(", ",$print_type_str)));
	?>
    <div style="width:930px; font-size:6px">
        <table width="100%" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="70" align="right">
                    <img  src='../../<? echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='100%' width='100%' />
                </td>
                <td>
                    <table width="800" cellspacing="0" align="center">
                        <tr>
                            <td align="center" style="font-size:20px"><strong ><? echo $company_library[$data[0]]; ?></strong></td>
                        </tr>
                        <tr>
                            <td align="center"  style="font-size:16px"><strong>Unit : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                        </tr>
                        <tr class="form_caption">
                            <td  align="center" style="font-size:14px">
                                <? echo show_company($data[0],'',''); ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:18px"><strong><? echo $data[2]; ?></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table width="100%" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="130"><strong>System ID:</strong></td>
                <td width="175"><? echo $dataArray[0][csf('recipe_no')]; ?></td>
                <td width="130"><strong>Recipe Desc.: </strong></td>
                <td width="175px"> <? echo $dataArray[0][csf('recipe_description')]; ?></td>
                <td width="130"><strong>Recipe Date: </strong></td>
                <td width="175"><? echo change_date_format($dataArray[0][csf('recipe_date')]); ?></td>
            </tr>
            <tr>
                <td><strong>AOP Job No:</strong></td>
                <td> <? echo $job_no; ?></td>
                <td><strong>Work Order:</strong></td>
                <td><? echo $po_no; ?></td>
                <td><strong>Color:</strong></td>
                <td><? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Recipe For:</strong></td>
                <td><? echo $recipe_for[$dataArray[0][csf('recipe_for')]]; ?></td>
                <td><strong>Buyer Job No:</strong></td>
                <td> <? echo $dataArray[0][csf('job_no')]; ?></td>
                <td><strong>Buyer Po:</strong></td>
                <td><? echo $buyer_po_no ; ?> </td>
            </tr>
            <tr>
            	<td><strong>Buyer Style:</strong></td>
				<td><? echo $buyer_style_ref ; ?> </td>
				<td><strong>Buyer's Buyer:</strong></td>
				<td>
					<?  echo $buyer_buyer; ?>
				</td>
				<td><strong>AOP Ref:</strong></td>
				<td><? echo $aop_ref; ?></td>
            </tr>
            <tr>
            	<td><strong>Batch No.:</strong></td>
                <td ><? echo $dataArray[0][csf('batch_no')] ; ?></td>
                <td><strong>Batch Weight:</strong></td>
                <td ><? echo $dataArray[0][csf('batch_weight')] ; ?></td>
                <td><strong>Water Ratio:</strong></td>
                <td ><? echo $dataArray[0][csf('total_water_ratio')] ; ?></td>
            </tr>
            <tr>
            	<td><strong>Remarks:</strong></td>
                <td colspan="3" ><? echo $dataArray[0][csf('remarks')]; ?></td>
                <td><strong>Print Type:</strong></td>
                <td ><? echo $print_type_str ; ?></td>
            </tr>
        </table>
        <?
			$data_arr = sql_select("SELECT image_location from common_photo_library  where master_tble_id='$mst_id' and form_name='embl_recipe_entry'");
						 //$image_location=return_field_value("image_location","common_photo_library","master_tble_id='$mst_id' and form_name='embl_recipe_entry'");
			foreach ($data_arr as $img_row)
			{
				?>
				<img  src='../../<? echo $img_row[csf('image_location')]; ?>' height='100px' width='150px' />
				<?
			}
		?>
        <div style="width:100%;">
            <table align="right" cellspacing="0" width="930" border="1" rules="all" class="rpt_table">
                <thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
                    <th width="30">SL</th>
                    <th width="100">Item Cat.</th>
                    <th width="130">Item Group</th>
                    <th width="100">Item Lot</th>
                    <th width="100">Sub Group</th>
                    <th width="180">Item Description</th>
                    <th width="50">UOM</th>
                    <th width="80">Ratio in %</th>
                    <th>Remarks</th>
                </thead>
				<?
				$multicolor_array = array();
				$prod_data_array = array();
				$color_remark_array = array();
				$sql = "SELECT color_id from pro_recipe_entry_dtls where mst_id=$mst_id and status_active=1 and is_deleted=0 order by id ASC";
				$nameArray = sql_select($sql); $tot_rows=0; $prodIds='';
				foreach ($nameArray as $row)
				{
					if (!in_array($row[csf("color_id")], $multicolor_array))
					{
						$multicolor_array[] = $row[csf("color_id")];
					}
				}
				unset($nameArray);

				$sql = "SELECT id, color_id, prod_id, comments, ratio, seq_no, item_lot from pro_recipe_entry_dtls where mst_id=$mst_id and ratio is not null and status_active=1 and is_deleted=0 order by seq_no ASC";
				$nameArray = sql_select($sql); $tot_rows=0; $prodIds='';
				foreach ($nameArray as $row)
				{
					$color_remark_array[$row[csf("color_id")]][$row[csf("id")]]['prod_id']= $row[csf("prod_id")];
					$color_remark_array[$row[csf("color_id")]][$row[csf("id")]]['comments']= $row[csf("comments")];
					$color_remark_array[$row[csf("color_id")]][$row[csf("id")]]['ratio']= $row[csf("ratio")];
					$color_remark_array[$row[csf("color_id")]][$row[csf("id")]]['seq_no']= $row[csf("seq_no")];
					$color_remark_array[$row[csf("color_id")]][$row[csf("id")]]['item_lot']= $row[csf("item_lot")];
					$tot_rows++;
					$prodIds.=$row[csf("prod_id")].",";
				}
				unset($nameArray);

				$prodIds=chop($prodIds,','); $prodIds_cond="";

				if($db_type==2 && $tot_rows>1000)
				{
					$prodIds_cond=" and (";
					$prodIdsArr=array_chunk(explode(",",$prodIds),999);
					foreach($prodIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$prodIds_cond.=" id in($ids) or ";
					}
					$prodIds_cond=chop($prodIds_cond,'or ');
					$prodIds_cond.=")";
				}
				else
				{
					$prodIds_cond=" and id in ($prodIds)";
				}

				$sql = "SELECT id, item_category_id, item_group_id, sub_group_name, item_description, unit_of_measure from product_details_master where status_active=1 and is_deleted=0 and company_id='$com_id' $prodIds_cond and item_category_id in(5,6,7,22,23) order by id";

				//echo $sql;
				$sql_result = sql_select($sql);

				foreach ($sql_result as $row)
				{
					$prod_data_array[$row[csf("id")]]= $row[csf("item_category_id")] . "**" . $row[csf("item_group_id")] . "**" . $row[csf("sub_group_name")] . "**" . $row[csf("item_description")] . "**" . $row[csf("unit_of_measure")];
				}
				unset($sql_result);

				$k=1; $grand_tot_ratio=0;

				foreach ($multicolor_array as $mcolor_id)
				{
					$i=1; $tot_ratio=0;
					?>
                    <tr bgcolor="#EEEFF0">
                        <td colspan="8" align="left"><b><? echo $k.'.  '; ?> <? echo $color_arr[$mcolor_id]; ?></b></td>
                    </tr>
					<?
					foreach ($color_remark_array[$mcolor_id] as $rid=>$exdata)
					{
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						$prod_id=0; $exprod_data=""; $item_category_id=0; $item_group_id=0; $sub_group_name=""; $item_description=""; $unit_of_measure=0;
						$prod_id=$exdata['prod_id'];
						$exprod_data=explode("**",$prod_data_array[$prod_id]);

						$item_category_id=$exprod_data[0]; $item_group_id=$exprod_data[1]; $sub_group_name=$exprod_data[2]; $item_description=$exprod_data[3]; $unit_of_measure=$exprod_data[4];

						?>
                        <tr bgcolor="<? echo $bgcolor; ?>">
                            <td><? echo $i; ?></td>
                            <td><? echo $item_category[$item_category_id]; ?></td>
                            <td><? echo $item_group_arr[$item_group_id]; ?>&nbsp;</td>
                            <td><? echo $exdata['item_lot']; ?>&nbsp;</td>
                            <td><? echo $sub_group_name; ?>&nbsp;</td>
                            <td><? echo $item_description; ?>&nbsp;</td>
                            <td align="center"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
                            <td align="right"><? echo $exdata['ratio']; ?>&nbsp;</td>
                            <td><? echo $exdata['comments']; ?>&nbsp;</td>
                        </tr>
						<?
						$tot_ratio+=$exdata['ratio'];
						$grand_tot_ratio+=$exdata['ratio'];
						$i++;
					}
					$k++;
					?>
                    <tr class="tbl_bottom">
                        <td align="right" colspan="7"><strong>Color Total</strong></td>
						<td align="right"><? echo number_format ($tot_ratio,4); ?>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
					<?
				}
				?>
            </table>
            <br>
			<?
				echo signature_table(164, $com_id, "930px");
			?>
        </div>
    </div>
    <script type="text/javascript">
     	//document.getElementById("ref_td").innerHTML='<? echo ": ".$internalRef; ?>'
     	document.getElementById("buyer_td").innerHTML='<? echo ": ".$buyer_arrs[$buyerBuyer]; ?>'
    </script>
	<?
	exit();
}

if ($action=="itemgroup_popup")																																																					{
	  echo load_html_head_contents("Item Group popup", "../../../", 1, 1,'','1','');
	  extract($_REQUEST);
	?>
	<script>
		function js_set_value(str)
		{
			//alert(str);
			document.getElementById('item_str').value=str;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:550px" >
			<?
                if ($category!=0) $item_category_list=" and item_category='$category'"; else { echo "Please Select Item Category."; die; }
                $sql="select id, item_category, item_group_code, item_name,  order_uom, trim_uom from lib_item_group where is_deleted=0 $item_category_list";
                $arr=array (0=>$item_category,3=>$unit_of_measurement,4=>$unit_of_measurement);
                echo  create_list_view ( "list_view", "Item Catagory,Group Code,Item Group Name,Work Order UOM,Cons. UOM", "110,80,150,80,50","550","340",0, $sql, "js_set_value", "id,item_group_code,item_name,order_uom,trim_uom", "", 1, "item_category,0,0,order_uom,trim_uom", $arr , "item_category,item_group_code,item_name,order_uom,trim_uom", "", 'setFilterGrid("list_view",-1);','0,0,0,0,0' );
            ?>
            <input type="hidden" id="item_str" />
        </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();																																																					}
if($action=="batch_popup")
{
  	echo load_html_head_contents("Batch Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
?>
	<script>
	function js_set_value( batch_info)
	{
		//alert (batch_id);
		document.getElementById('hidden_batch_info').value=batch_info;
		parent.emailwindow.hide();
	}
    </script>
</head>
<body>
<div align="center">
	<fieldset style="width:750px;margin-left:4px;">
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="740" class="rpt_table">
                <thead>
                    <tr>
                        <th colspan="7"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                    </tr>
                	<tr>
                        <th>Batch SL</th>
                        <th>Batch No</th>
                        <th>Design No</th>
                        <th>AOP Ref.</th>
                        <th colspan="2">
                        	Date Range
                        </th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" name="hidden_batch_info" id="hidden_batch_info" value="">
                        </th>
                    </tr>
                </thead>
                <tr class="general">
                    <td align="center">
                    	<input type="text" style="width:140px" class="text_boxes"  name="txt_search_batch_sl" id="txt_search_batch_sl" placeholder="Write Before ( - )" />
                        <?
                           //$search_by_arr=array(1=>"Batch No");
                            //echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td align="center">
                        <input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                     <td align="center">
                        <input type="text" style="width:140px" class="text_boxes"  name="txt_design_no" id="txt_design_no" />
                    </td>
                    <td align="center">
                        <input type="text" style="width:140px" class="text_boxes"  name="txt_aop_ref" id="txt_aop_ref" />
                    </td>
                    <td>
                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:90px" placeholder="From Date" >
                   		
                    </td>
                    <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:90px"  placeholder="To Date"  ></td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_search_batch_sl').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_aop_ref').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_design_no').value, 'create_batch_search_list_view', 'search_div', 'aop_recipe_entry_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                	<td colspan="7" height="20" valign="middle" align="center"><? echo load_month_buttons(1); ?></td>
                </tr>

            </table>
            <div id="search_div" style="margin-top:10px"></div>
        </form>
    </fieldset>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_batch_search_list_view")
{
	//print_r ($data);
	$data=explode('_',$data);
	$search_sl=$data[1];
	$batch_number_search =$data[0];
	$company_id =$data[2];
	$search_type =$data[3];
	$aop_ref =$data[4];
	$start_date=$data[5];
	$end_date=$data[6];
	$design_no=$data[7];

	if (!empty($start_date)  &&  !empty($end_date)) {
		if ($db_type == 0) {
			$date_cond = " and a.batch_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
		} else {
			$date_cond = " and a.batch_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}
	//echo $aop_ref; die;
	//echo "select dyeing_fin_bill from variable_settings_subcon where variable_list=13 and company_id =$company_id"; die;
	$main_batch_allow = return_field_value("dyeing_fin_bill", "variable_settings_subcon", "company_id =$company_id and variable_list=13 and is_deleted=0 and status_active=1");

	//echo $main_batch_allow; die;

	if ($design_no!= "")
	{
		$design_no_cond = "and b.design_no like '%".$design_no."%'";
	}


	$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
	if($search_sl!='') $search_sl_cond=" and a.id='$search_sl'"; else $search_sl_cond="";
	if($search_type==1)
	{
		if($aop_ref!='') $aop_cond=" and a.aop_reference='$aop_ref'"; else $aop_cond="";
	}
	else if($search_type==4 || $search_type==0)
	{
		if($aop_ref!='') $aop_cond=" and a.aop_reference like '%$aop_ref%'"; else $aop_cond="";
	}
	else if($search_type==2)
	{
		if($aop_ref!='') $aop_cond=" and a.aop_reference like '$aop_ref%'"; else $aop_cond="";
	}
	else if($search_type==3)
	{
		if($aop_ref!='') $aop_cond=" and a.aop_reference like '%$aop_ref'"; else $aop_cond="";
	}
	if($aop_ref!='' || $design_no!= "")
	{
		$ord_sql = "SELECT b.id,a.subcon_job,a.aop_reference,b.order_no,b.buyer_style_ref,b.buyer_po_no, b.design_no from subcon_ord_mst a ,subcon_ord_dtls b where company_id =$company_id $aop_cond $design_no_cond and a.entry_form=278 and  a.subcon_job=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
		$ordArray=sql_select( $ord_sql ); $po_arr=array(); $ref_arr=array();
		foreach ($ordArray as $row)
		{
			$po_arr[$row[csf('id')]]['order'] = $row[csf('order_no')];
			$po_arr[$row[csf('id')]]['job'] = $row[csf('subcon_job')];
			$po_arr[$row[csf('id')]]['buyer_style_ref'] = $row[csf('buyer_style_ref')];
			$po_arr[$row[csf('id')]]['buyer_po_no'] = $row[csf('buyer_po_no')];
			$po_arr[$row[csf('id')]]['design_no'] = $row[csf('design_no')];
			$ref_arr[$row[csf('id')]] = $row[csf('aop_reference')];
			$po_id[] .= $row[csf("id")];
		}
		$po_id_cond=" and b.po_id in (".implode(",",$po_id).") ";
	}
	else
	{
		$ord_sql = "SELECT b.id,a.subcon_job,a.aop_reference,a.party_id,b.order_no,b.buyer_style_ref,b.buyer_po_no, b.design_no from subcon_ord_mst a ,subcon_ord_dtls b where company_id =$company_id $design_no_cond and a.entry_form=278 and  a.subcon_job=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
		$ordArray=sql_select( $ord_sql ); $po_arr=array(); $ref_arr=array();
		foreach ($ordArray as $row)
		{
			$po_arr[$row[csf('id')]]['order'] = $row[csf('order_no')];
			$po_arr[$row[csf('id')]]['job'] = $row[csf('subcon_job')];
			$po_arr[$row[csf('id')]]['buyer_style_ref'] = $row[csf('buyer_style_ref')];
			$po_arr[$row[csf('id')]]['buyer_po_no'] = $row[csf('buyer_po_no')];
			$po_arr[$row[csf('id')]]['design_no'] = $row[csf('design_no')];
			$ref_arr[$row[csf('id')]] = $row[csf('aop_reference')];
		}
		//$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
		$po_id_cond='';
	}
	//echo "<pre>";
	//print_r($po_arr);
	if($main_batch_allow==1)
	{

		$ord_sql = "SELECT id, booking_no from wo_booking_mst where status_active=1 and is_deleted=0"; //$company_id a.booking_no=b.booking_no and b.process=35 and a.booking_type=3 and a.pay_mode in (3,5) and a.lock_another_process!=1
		$ordArray=sql_select( $ord_sql ); $main_po_arr=array();
		foreach ($ordArray as $row)
		{
			$main_po_arr[$row[csf('id')]]['order'] = $row[csf('booking_no')];
		}

	}


	if($search_type==1)
	{
		if($batch_number_search!='') $batch_number_cond=" and a.batch_no='$batch_number_search'"; else $batch_number_cond="";
	}
	else if($search_type==4 || $search_type==0)
	{
		if($batch_number_search!='') $batch_number_cond=" and a.batch_no like '%$batch_number_search%'"; else $batch_number_cond="";
	}
	else if($search_type==2)
	{
		if($batch_number_search!='') $batch_number_cond=" and a.batch_no like '$batch_number_search%'"; else $batch_number_cond="";
	}
	else if($search_type==3)
	{
		if($batch_number_search!='') $batch_number_cond=" and a.batch_no like '%$batch_number_search'"; else $batch_number_cond="";
	}
	$entry_form_cond='';
	if($main_batch_allow==1) $entry_form_cond=" and a.entry_form in(0,281) and a.process_id like '%35%' "; else $entry_form_cond="and a.entry_form =281 ";

	if($db_type==0)
	{
		$sql = "SELECT a.id,a.entry_form, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.dur_req_hr, a.dur_req_min,a.within_group,b.buyer_po_id,a.location_id,a.party_id,a.is_sales,a.sales_order_no,a.booking_no ,a.sales_order_id, b.po_id, b.item_description from pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $entry_form_cond $batch_number_cond $search_sl_cond $po_id_cond $date_cond group by a.id ,a.entry_form, a.batch_no, a.extention_no,b.buyer_po_id,a.location_id,a.party_id,a.is_sales,a.sales_order_no,a.booking_no ,a.sales_order_id , b.po_id, b.item_descriptionorder by a.id DESC";
	}
	elseif($db_type==2)
	{
		$sql = "SELECT a.id,a.entry_form, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.dur_req_hr, a.dur_req_min,a.within_group,b.buyer_po_id,a.location_id,a.party_id,a.is_sales,a.sales_order_no,a.booking_no ,a.sales_order_id,b.po_id, b.item_description from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $entry_form_cond $batch_number_cond $search_sl_cond $po_id_cond $date_cond group by a.id,a.entry_form, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.dur_req_hr, a.dur_req_min,b.buyer_po_id,a.location_id,a.party_id,a.within_group,a.is_sales,a.sales_order_no,a.booking_no ,a.sales_order_id, b.po_id, b.item_description order by a.id DESC";
	}
	//echo $sql;

	$nameArray=sql_select( $sql );
	$batch_id=array();
	$buyer_po_id_arr=array();
	foreach ($nameArray as $row) {
		//$ids = implode(",", array_unique(explode(",", $row[csf("po_id")])));
		//$po_ids .= $ids . ",";
		$batch_id[] .= $row[csf("id")];
		$buyer_po_id_arr[$row[csf("id")]]['po'].=$row[csf("buyer_po_id")].",";
	}

	/*$order_id=array_unique(explode(",",$buyer_po_id_arr[$row[csf("id")]]['po']));

	//echo $order_id; die;
	print_r($order_id);*/

	$sql_load_unload="SELECT id, batch_id,load_unload_id,result from pro_fab_subprocess where batch_id in (".implode(",",$batch_id).") and load_unload_id in (1,2) and entry_form=38 and is_deleted=0 and status_active=1";
	$load_unload_data=sql_select($sql_load_unload);
	foreach ($load_unload_data as $row)
	{
		if($row[csf('load_unload_id')]==1)
		{
			$load_unload_arr[$row[csf('batch_id')]] = $row[csf('load_unload_id')];
		}
		else if($row[csf('load_unload_id')]==2 )
		{
			$unloaded_batch[$row[csf('batch_id')]] = $row[csf('batch_id')];
		}
	}
	if($main_batch_allow==1) $entryFormCond=" and entry_form in(0,281) and process_id like '%35%' "; else $entryFormCond="and entry_form =281 ";

	$re_dyeing_from = return_library_array("SELECT  re_dyeing_from from pro_batch_create_mst where re_dyeing_from <>0 and status_active = 1 and is_deleted = 0 $entryFormCond","re_dyeing_from","re_dyeing_from");



	$po_sql ="SELECT a.style_ref_no,a.buyer_name, b.id, b.po_number,a.job_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  and a.status_active=1 and b.status_active=1";
	$po_sql_res=sql_select($po_sql); $buyer_po_arr=array();
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
		//$buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
	}
	unset($po_sql_res);
	//print_r($buyer_po_arr);
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1030" class="rpt_table" >
            <thead>

                <th width="30">SL</th>
                <th width="120">AOP Job No.</th>
                <th width="120">Work Order</th>
                <th width="90">Buyer Job No</th>
                <th width="90">Buyer PO</th>
                <th width="80">Buyer Style Ref</th>
                <th width="70">Batch No.</th>
                <th width="70">Design No.</th>
                <th width="70">Color</th>
                <th width="70">Batch Type</th>
                <th width="150">Item Description</th>
                <th>AOP Ref</th>
            </thead>
        </table>
        <div style="width:1038px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1030" class="rpt_table" id="list_view" >
            <?
				$i=1;
				$batch_type= array(0 =>"Main Batch" ,281 =>"AOP Batch");
				foreach ($nameArray as $selectResult)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$order_no=''; $order_id='';  $order_ids='';  $aop_jobs=''; $aop_job=''; $buyer_job=''; $buyer_po=''; $buyer_style='';$buyerpoid='';$buyer_po_number='';
					$all_ref_arr=array(); $all_design_no=array(); $all_party_arr=array();
					$order_id=array_unique(explode(",",$selectResult[csf("po_id")]));

					if($buyer_po_id_arr[$selectResult[csf("id")]]['po']!='')
					{
						$buyerpoid=array_unique(explode(",",$buyer_po_id_arr[$selectResult[csf("id")]]['po']));
					}


					//print_r($buyerpoid);
					foreach($buyerpoid as $b_id)
					{
						if($buyer_po_number=="") $buyer_po_number=$buyer_po_arr[$b_id]['po']; else $buyer_po_number.=','.$buyer_po_arr[$b_id]['po'];

					}

					foreach($order_id as $val)
					{
						//echo $val."==";
						if($order_no=="") $order_no=$po_arr[$val]['order']; else $order_no.=", ".$po_arr[$val]['order'];
						if($aop_jobs=="") $aop_jobs=$po_arr[$val]['job']; else $aop_jobs.=", ".$po_arr[$val]['job'];
						if($order_ids=="") $order_ids=$val; else $order_ids.=", ".$val;
						$all_ref_arr[] .= $ref_arr[$val];

						$all_design_no[] = $po_arr[$val]['design_no'];

						if($selectResult[csf("entry_form")]==0)
						{
							$batch_weight='';
							if($selectResult[csf("is_sales")]==1)
							{
								$buyer_po=$selectResult[csf("sales_order_no")];
								$order_no=$selectResult[csf("booking_no")];
							}
							else
							{
								$buyer_po=$buyer_po_arr[$val]['po'];
								$buyer_job=$buyer_po_arr[$val]['job'];
								$buyer_style=$buyer_po_arr[$val]['style'];
							}
						}
						else
						{
							$batch_weight=$selectResult[csf("batch_weight")];
							$buyer_job=$buyer_po_arr[$selectResult[csf("buyer_po_id")]]['job'];
							if($selectResult[csf("within_group")]==1)
							{
								$buyer_po=$buyer_po_arr[$selectResult[csf("buyer_po_id")]]['po'];
								$buyer_style=$buyer_po_arr[$selectResult[csf("buyer_po_id")]]['style'];
							}
							else{
								if($buyer_po=="") $buyer_po=$po_arr[$val]['buyer_po_no']; else $buyer_po.=", ".$po_arr[$val]['buyer_po_no'];
								if($buyer_style=="") $buyer_style=$po_arr[$val]['buyer_style_ref']; else $buyer_style.=", ".$po_arr[$val]['buyer_style_ref'];
							}
							
						}
					}
					$aop_job=implode(", ",array_unique(explode(", ",$aop_jobs)));
					$order_no=implode(", ",array_unique(explode(", ",$order_no)));

					$buyer_job=implode(", ",array_unique(explode(", ",$buyer_job)));
					$buyer_po=implode(", ",array_unique(explode(", ",$buyer_po)));
					$buyer_style=implode(", ",array_unique(explode(", ",$buyer_style)));
					//echo "<pre>";
					//print_r($aop_job);
					$ref_no = implode(",", array_unique($all_ref_arr));
					$design_no = implode(",", array_unique($all_design_no));
					$party_no = implode(",", array_unique($all_party_arr));
					//echo $party_no;
					if($re_dyeing_from[$selectResult[csf('id')]])
					{
						$ext_from = $re_dyeing_from[$selectResult[csf('id')]];
					}else{
						$ext_from = "0";
					}

					if($buyer_po_number!=''){ $buyer_po_number =$buyer_po_number;}else{$buyer_po_number=$buyer_po;}

					$str=$selectResult[csf('id')]."___".$selectResult[csf('batch_no')]."___".$order_ids."___".$order_no."___".$selectResult[csf('location_id')]."___".$buyer_job."___".$buyer_po_number."___".$buyer_style."___".$selectResult[csf('party_id')]."___".$selectResult[csf("buyer_po_id")]."___".$selectResult[csf("color_id")]."___".$color_arr[$selectResult[csf("color_id")]]."___".$batch_weight."___".$selectResult[csf("entry_form")]."___".$ref_no."___".$selectResult[csf('within_group')];
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $str;?>')">
                        <td width="30" align="center"><? echo $i; ?></td>
                        <td width="120" align="center"><p><? echo $aop_job; ?></p></td>
                        <td width="120" align="center"><p><? echo $order_no; ?></p></td>
                        <td width="90" align="center"><p><? echo $buyer_job; ?></p></td>
                        <td width="90"  align="center"><p><? echo $buyer_po; ?></td>
                        <td width="80"  align="center"><p><? echo $buyer_style; ?></td>
                        <td width="70"  align="center"><p><? echo $selectResult[csf('batch_no')]; ?> </p></td>
                        <td width="70"  align="center"><p><? echo $design_no; ?> </p></td>
                        <td width="70"align="center"><p><? echo  $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
                        <td width="70" align="center"><p><? echo  $batch_type[$selectResult[csf('entry_form')]]; ?></p></td>
                        <td width="150" align="center"><p><? echo  $selectResult[csf('item_description')]; ?></p></td>
                        <td align="center"><p><? echo  $ref_no; ?></p></td>
                    </tr>
                	<?
                	$i++;
				}
			?>
            </table>
        </div>
    </div>
    <?
	//echo  create_list_view("list_view", "Batch No,Ext. No,Batch Weight,Total Trims Weight, Batch Date, Color", "100,70,80,80,80,80","600","250",0, $sql, "js_set_value", "id", "", 1, "0,0,0,0,0,color_id", $arr, "batch_no,extention_no,batch_weight,total_trims_weight,batch_date,color_id", "",'','0,0,2,2,3,0');

exit();
}

/*if ($action == 'populate_batch_data_from_search_popup')
{
	//echo $data; die;

	$data=explode('**',$data);
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
	//$order_arr = array();
	$order_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
	$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no,a.buyer_name, b.id, b.po_number,a.job_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  and a.status_active=1 and b.status_active=1";
	$po_sql_res=sql_select($po_sql); $buyer_po_arr=array();
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
		$buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
	}
	unset($po_sql_res);

	if($db_type==0)
	{
		$sql = "select a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.dur_req_hr, a.dur_req_min,a.buyer_po_id,location_id, group_concat(b.po_id) as po_id from pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=281 and a.company_id=$data[1] and a.id=$data[0]  $batch_number_cond $search_sl_cond  group by a.id, a.batch_no, a.extention_no,a.buyer_po_id,location_id order by a.id DESC";
	}
	elseif($db_type==2)
	{
		$sql = "select a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.dur_req_hr, a.dur_req_min,a.buyer_po_id,location_id, listagg(b.po_id,',') within group (order by b.po_id) as po_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=281 and a.company_id=$data[1] and a.id=$data[0] $batch_number_cond $search_sl_cond  group by a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.dur_req_hr, a.dur_req_min,a.buyer_po_id,location_id order by a.id DESC";
	}

	//echo $sql = "select id, batch_no, extention_no, batch_weight, total_trims_weight, batch_date, color_id, dur_req_hr, dur_req_min,buyer_po_id,location_id from pro_batch_create_mst  where company_id=$data[1] and id=$data[0] and status_active=1 and is_deleted=0 and entry_form=281 ";
	$data_array = sql_select($sql);
	foreach ($data_array as $row) {

		echo "document.getElementById('cbo_company_id').value 				= '" . $row[csf("company_id")] . "';\n";
		echo "$('#cbo_company_id').attr('disabled','true')" . ";\n";
		//echo "load_drop_down('requires/aop_recipe_entry_controller', '".$row[csf('company_id')].'_'.$row[csf("within_group")]."', 'load_drop_down_buyer', 'buyer_td_id' );\n";


		echo "document.getElementById('cbo_location').value 				= '" . $row[csf("location_id")] . "';\n";
		echo "document.getElementById('txt_order_id').value 				= '" . $row[csf("buyer_po_id")] . "';\n";
		echo "document.getElementById('txt_order').value 					= '" . $order_arr[$row[csf("buyer_po_id")]] . "';\n";
		echo "document.getElementById('cbo_buyer_name').value 				= '" . $row[csf("buyer_id")] . "';\n";
		echo "document.getElementById('hid_job_no').value 					= '" . $buyer_po_arr[$row[csf("buyer_po_id")]]['job']. "';\n";

		echo "document.getElementById('txt_pocolor_id').value 				= '" . $row[csf("color_id")] . "';\n";
		echo "document.getElementById('txt_po_color').value 				= '" . $color_arr[$row[csf("color_id")]] . "';\n";
		echo "document.getElementById('hid_item_id').value 					= '" . $row[csf("gmts_item")] . "';\n";

		echo "document.getElementById('txtbuyerPo').value 					= '" . $buyer_po_arr[$row[csf("buyer_po_id")]]['po'] . "';\n";
		echo "document.getElementById('txtbuyerPoId').value 				= '" . $row[csf("buyer_po_id")] . "';\n";
		echo "document.getElementById('txtstyleRef').value 					= '" . $buyer_po_arr[$row[csf("buyer_po_id")]]['style'] . "';\n";
		echo "set_button_status(0, '" . $_SESSION['page_permission'] . "', 'fnc_recipe_entry',1);\n";
		exit();
	}
}*/

if($action=="check_water_ratio")
{
	$data=explode("**",$data);
	$company_id=$data[0];
	$store_id=$data[1];
	$color_id=$data[2];
	$update_id=$data[3];
	//echo "select um(water_ratio) as water_ratio from pro_recipe_entry_dtls where mst_id=$update_id and color_id=$color_id and status_active=1 and is_deleted=0 and ratio>0"; die;
	$water_ratio=return_field_value( "water_ratio","pro_recipe_entry_dtls"," mst_id=$update_id and color_id=$color_id and status_active=1 and is_deleted=0 and ratio>0","water_ratio");
	echo $water_ratio;
	exit();
}

if ($action=="populate_batch_id")
{
	$data=explode('_',$data);
	$main_batch_allow = return_field_value("dyeing_fin_bill", "variable_settings_subcon", "company_id =$data[0] and variable_list=13 and is_deleted=0 and status_active=1");
	$entry_form_cond='';
	if($main_batch_allow==1) $entry_form_cond=" and entry_form in(0,281) and process_id like '%35%' "; else $entry_form_cond="and entry_form =281 ";

	//$sql = "select a.id,a.entry_form, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.dur_req_hr, a.dur_req_min,a.within_group,b.buyer_po_id,a.location_id,a.party_id,a.is_sales,a.sales_order_no,a.booking_no ,a.sales_order_id,b.po_id, b.item_description from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id=$data[0] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $entry_form_cond $batch_number_cond $search_sl_cond $po_id_cond group by a.id,a.entry_form, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.dur_req_hr, a.dur_req_min,b.buyer_po_id,a.location_id,a.party_id,a.within_group,a.is_sales,a.sales_order_no,a.booking_no ,a.sales_order_id, b.po_id, b.item_description order by a.id DESC";

	$batch=return_field_value("id","pro_batch_create_mst","status_active=1 and is_deleted=0 $entry_form_cond and company_id=$data[0] and batch_no='".$data[1]."'");
	echo $batch;
	exit();
}


if($action=="load_data_to_form_batch")
{
	//print_r ($data);
	$data=explode('_',$data);
	$company_id =$data[0];
	$batch_id =$data[1];

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	//$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
	$po_id_cond='';

	if($db_type==0)
	{
		$sql = "SELECT a.id,a.entry_form, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.dur_req_hr, a.dur_req_min,a.within_group,b.buyer_po_ids,a.location_id,a.party_id,a.is_sales,a.sales_order_no,a.booking_no ,a.sales_order_id, b.po_id, b.item_description from pro_batch_create_mst a,  pro_batch_create_dtls b where  a.id=$batch_id and a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $entry_form_cond $batch_number_cond $search_sl_cond $po_id_cond group by a.id ,a.entry_form, a.batch_no, a.extention_no,b.buyer_po_ids,a.location_id,a.party_id,a.is_sales,a.sales_order_no,a.booking_no ,a.sales_order_id , b.po_id, b.item_descriptionorder by a.id DESC";
	}
	elseif($db_type==2)
	{
		$sql = "SELECT a.id,a.entry_form, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.dur_req_hr, a.dur_req_min,a.within_group,b.buyer_po_ids,a.location_id,a.party_id,a.is_sales,a.sales_order_no,a.booking_no ,a.sales_order_id,b.po_id, b.item_description from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=$batch_id and a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $entry_form_cond $batch_number_cond $search_sl_cond $po_id_cond group by a.id,a.entry_form, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.dur_req_hr, a.dur_req_min,b.buyer_po_ids,a.location_id,a.party_id,a.within_group,a.is_sales,a.sales_order_no,a.booking_no ,a.sales_order_id, b.po_id, b.item_description order by a.id DESC";
	}
	//echo $sql;

	$nameArray=sql_select( $sql );
	$po_sql ="SELECT a.style_ref_no,a.buyer_name, b.id, b.po_number,a.job_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  and a.status_active=1 and b.status_active=1 and b.status_active=1 and is_deleted=0";
	$po_sql_res=sql_select($po_sql); $buyer_po_arr=array();
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
		//$buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
	}
	unset($po_sql_res);
	$ord_sql = "SELECT b.id,a.subcon_job,a.aop_reference,a.party_id,b.order_no from subcon_ord_mst a ,subcon_ord_dtls b where company_id =$company_id and a.entry_form=278 and  a.subcon_job=b.job_no_mst and a.status_active=1 and b.status_active=1 and b.status_active=1 and is_deleted=0";
	$ordArray=sql_select( $ord_sql ); $po_arr=array(); $ref_arr=array();
	foreach ($ordArray as $row)
	{
		$po_arr[$row[csf('id')]]['order'] = $row[csf('order_no')];
		$po_arr[$row[csf('id')]]['job'] = $row[csf('subcon_job')];
		$ref_arr[$row[csf('id')]] = $row[csf('aop_reference')];
	}


	//print_r($buyer_po_arr);
	foreach ($nameArray as $selectResult)
	{
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		$order_no=''; $order_id='';  $order_ids='';  $aop_jobs=''; $aop_job=''; $buyer_job=''; $buyer_po=''; $buyer_style='';
		$all_ref_arr=array(); $all_party_arr=array();
		$order_id=array_unique(explode(",",$selectResult[csf("po_id")]));
		foreach($order_id as $val)
		{
			//echo $val."==";
			if($order_no=="") $order_no=$po_arr[$val]['order']; else $order_no.=", ".$po_arr[$val]['order'];
			if($aop_jobs=="") $aop_jobs=$po_arr[$val]['job']; else $aop_jobs.=", ".$po_arr[$val]['job'];
			if($order_ids=="") $order_ids=$val; else $order_ids.=", ".$val;
			$all_ref_arr[] .= $ref_arr[$val];
			$buyer_po_ids=chop($selectResult[csf("buyer_po_ids")],',');
			if($selectResult[csf("entry_form")]==0)
			{
				if($selectResult[csf("is_sales")]==1)
				{
					$buyer_po=$selectResult[csf("sales_order_no")];
					$order_no=$selectResult[csf("booking_no")];
				}
				else
				{
					$buyer_po =$buyer_po_arr[$val]['po'];
					$buyer_job =$buyer_po_arr[$val]['job'];
					$buyer_style =$buyer_po_arr[$val]['style'];
				}
			}
			else
			{
				$buyer_job =$buyer_po_arr[$buyer_po_ids]['job'];
				$buyer_po =$buyer_po_arr[$buyer_po_ids]['po'];
				$buyer_style =$buyer_po_arr[$buyer_po_ids]['style'];
			}
		}
		$aop_job=implode(", ",array_unique(explode(", ",$aop_jobs)));
		$order_no=implode(", ",array_unique(explode(", ",$order_no)));
		$buyer_job=implode(", ",array_unique(explode(", ",$buyer_job)));
		$buyer_po=implode(", ",array_unique(explode(", ",$buyer_po)));
		$buyer_style=implode(", ",array_unique(explode(", ",$buyer_style)));
		//echo "<pre>";
		//print_r($aop_job);
		$ref_no = implode(",", array_unique($all_ref_arr));
		$party_no = implode(",", array_unique($all_party_arr));

		echo "document.getElementById('txt_batch_id').value 		= '" . $selectResult[csf("id")] . "';\n";
		echo "document.getElementById('txt_batch_number').value 	= '" . $selectResult[csf("batch_no")] . "';\n";
		echo "document.getElementById('txt_order_id').value 		= '" . $order_ids . "';\n";
		echo "document.getElementById('txt_order').value 			= '" . $order_no . "';\n";
		//echo "document.getElementById('cbo_location').value 		= '" . $selectResult[csf("location_id")] . "';\n";
		echo "document.getElementById('hid_job_no').value 			= '" . $buyer_job . "';\n";
		echo "document.getElementById('txtbuyerPo').value 			= '" . $buyer_po . "';\n";
		echo "document.getElementById('txtstyleRef').value 			= '" . $buyer_style . "';\n";
		echo "document.getElementById('cbo_buyer_name').value 		= '" . $selectResult[csf("party_id")] . "';\n";
		echo "document.getElementById('txtbuyerPoId').value 		= '" . $buyer_po_ids . "';\n";
		echo "document.getElementById('hidden_batch_color_id').value = '" . $selectResult[csf("color_id")] . "';\n";
		echo "document.getElementById('txt_batch_color').value 		= '" . $color_arr[$selectResult[csf("color_id")]] . "';\n";
		if($selectResult[csf("entry_form")]==281)
		{
			echo "document.getElementById('txt_batch_weight').value = '" . $selectResult[csf("batch_weight")] . "';\n";
		}
		echo "document.getElementById('txt_batch_entry_form').value = '" . $selectResult[csf("entry_form")] . "';\n";
		echo "document.getElementById('txtAopRef').value 			= '" . $ref_no . "';\n";
		echo "document.getElementById('cbo_within_group').value 	= '" . $selectResult[csf("within_group")] . "';\n";
	}
exit();
}
?>
