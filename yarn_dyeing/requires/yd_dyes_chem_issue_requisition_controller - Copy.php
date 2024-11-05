<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
//--------------------------------------------------------------------------------------------

$color_arr = return_library_array("SELECT id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name");

if ($action == "load_drop_down_location") 
{
	echo create_drop_down("cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-Select Location-", 0, "");
	exit();
}

if ($action == "requisition_popup") 
{
	echo load_html_head_contents("Requisition Popup Info", "../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(data) 
		{
			$("#hidden_sys_id").val(data);
			parent.emailwindow.hide();
		}

        function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0)
			{
				$('#search_by_td').html('Embl. Job No');
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
	<div align="center" style="width:880px; margin: 0 auto;">
		<form name="searchfrm" id="searchfrm">
			<fieldset style="width:780px;">
				<table cellpadding="0" cellspacing="0" width="780" border="1" rules="all" class="rpt_table">
					<thead>
                    	<tr>
                            <th colspan="7"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                        <tr>
                            <th width="230" colspan="2">Requisition Date Range</th>
                            <th width="100">Req/Recipe By</th>
                            <th width="140" id="search_by_td_up">Enter Requisition No</th>
                            <th width="100" style="display: none;">Search By</th>
                            <th width="100" style="display: none;" id="search_by_td">Embl. Job No</th>
                            <th>
                                <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton"/>
                                <input type="hidden" name="hidden_sys_id" id="hidden_sys_id" class="text_boxes" value="">
                            </th>
                        </tr>
					</thead>
					<tr class="general">
						<td>
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px;">
                        </td>
						<td>
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px;">
						</td>
						<td>
							<?
							$search_by_arr = array(1 => "Requisition No", 2 => "Recipe No");
							$dd = "change_search_event(this.value, '0*0', '0*0', '../') ";
							echo create_drop_down("cbo_req_recipe_by", 150, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td>
							<input type="text" style="width:130px;" class="text_boxes" name="txt_search_req_recipe" id="txt_search_req_recipe"/>
						</td>
                        <td style="display: none;" >
							<?
                                $search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
                                echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                            ?>
                        </td>
                        <td style="display: none;" >
                            <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                        </td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_req_recipe').value+'_'+document.getElementById('cbo_req_recipe_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+'<? echo $company; ?>', 'create_requisition_search_list_view', 'search_div', 'yd_dyes_chem_issue_requisition_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;"/>
						</td>
					</tr>
					<tr>
						<td colspan="7" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				
			</fieldset>
		</form>
        <div id="search_div"></div>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "create_requisition_search_list_view") 
{
	$data = explode("_", $data);
	$search_req_recipe = trim($data[0]);
	$req_recipe_by = $data[1];
	$start_date = trim($data[2]);
	$end_date = trim($data[3]);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$company = $data[7];
	
	$date_cond = "";
	if ($start_date != "" && $end_date != "") 
	{
		if ($db_type == 0) 
		{
			$date_con ="and requisition_date between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'";
		} 
		else 
		{
			$date_cond="and requisition_date between '".change_date_format($start_date, "yyyy-mm-dd", "-", 1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-", 1)."'";
		}
	} 
	if ($db_type == 0)  $rid_cond="group_concat(id)"; else $rid_cond="listagg(id,',') within group (order by id)";
	
	$search_field_cond = ""; $recipe_ids ="";
	if ($search_req_recipe != "") 
	{
		if ($req_recipe_by == 1)
			$search_field_cond = "and requ_prefix_num='$search_req_recipe'";
		else if ($req_recipe_by == 2) 
		{
			$recipe_cond="and recipe_no_prefix_num='$search_req_recipe'";
			//echo "select $rid_cond as id from pro_recipe_entry_mst where 1=1 $recipe_cond";
			$recipe_ids = return_field_value("$rid_cond as id", "pro_recipe_entry_mst", "1=1 $recipe_cond", "id");
			$recipe_idstr='';
			$ex_recipe_ids=array_unique(explode(",",$recipe_ids));
			foreach($ex_recipe_ids as $rids)
			{
				if($recipe_idstr=="") $recipe_idstr="'".$rids."'"; else $recipe_idstr.=",'".$rids."'";
			}
			
			if(str_replace("'","",$recipe_idstr)!='') $search_field_cond = "and recipe_id in ($recipe_idstr)";
		} 
	} 
	//die;
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
	
	/*$po_ids=''; $buyer_po_arr=array();
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
	}
	//echo $po_ids;
	if ($po_ids!="") $po_idsCond=" and buyer_po_id in ($po_ids)"; else $po_idsCond="";*/
	
	/*$po_sql ="SELECT a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);*/
	
	
	/*$po_sql="select a.subcon_job, a.within_group, a.party_id, b.id, b.order_no, b.order_quantity, b.buyer_po_id,b.buyer_po_no, b.buyer_style_ref, 
   b.buyer_buyer from subcon_ord_mst a, subcon_ord_dtls b where a.embellishment_job=b.job_no_mst and a.entry_form=204  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
		$buyer_po_arr[$row[csf("id")]]['buyer_buyer']=$row[csf("buyer_buyer")];
	}
	unset($po_sql_res);*/
	
	
	
	$spo_ids='';
	
	if($db_type==0)
	{
		$id_cond="group_concat(b.id)";
		$year_field = "YEAR(insert_date) as year";
	}
	else if($db_type==2)
	{
		$id_cond="listagg(b.id,',') within group (order by b.id)";
		$year_field = "to_char(insert_date,'YYYY') as year";
	}
	if(($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2))
	{
		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.embellishment_job=b.job_no_mst $search_com_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
	}
	
	if ( $spo_ids!="") $spo_idsCond=" and order_id in ($spo_ids)"; else $spo_idsCond="";

	$company_arr = return_library_array("SELECT id,company_name from lib_company where status_active =1 and is_deleted=0", "id", "company_name");
	
	$recipe_arr = return_library_array("SELECT id, recipe_no from yd_recipe_entry_mst where status_active =1 and is_deleted=0", "id", "recipe_no");
	//$po_arr = return_library_array("select embellishment_job, order_no from subcon_ord_mst group by embellishment_job, order_no", 'embellishment_job', 'order_no');
	$po_arr=return_library_array( "SELECT id,order_no from subcon_ord_dtls where status_active =1 and is_deleted=0",'id','order_no');

	$sql = "SELECT id, requ_no, requ_prefix_num, $year_field, company_id, requisition_date, recipe_id, order_id, job_no, buyer_po_id,order_id,color_id from dyes_chem_issue_requ_mst where company_id=$company and entry_form=437 and status_active =1 and is_deleted=0 $date_cond $search_field_cond $po_idsCond $spo_idsCond order by id DESC ";//$search_field_cond
	//echo $sql;
	$result = sql_select($sql);
	?>
	<div align="center">
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="150">Req. No</th>
			<th width="50">Year</th>
			<th width="70">Requisition Date</th>
			<th>Recipe No</th>
		</thead>
	</table>
	<div style="width:650px; max-height:250px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="630" class="rpt_table" id="tbl_list_search">
		<?
		$i = 1;
		foreach ($result as $row) 
		{
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
			
			$buyer_po=""; $buyer_style="";
			$order_id=explode(",",$row[csf('order_id')]);
			foreach($order_id as $po_id)
			{
				if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
				if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
			}
			$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
			$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));

			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')].'___'.$row[csf('recipe_id')]; ?>');">
				<td width="30"><? echo $i; ?></td>
				<td width="150" align="center"><p><? echo $row[csf('requ_no')]; ?></p></td>
				<td width="50" align="center"><p><? echo $row[csf('year')]; ?></p></td>
				<td width="70" align="center"><p><? echo change_date_format($row[csf('requisition_date')]); ?></p></td>
                <td><p><? echo $recipe_arr[$row[csf('recipe_id')]]; ?></p></td>
				
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

if ($action == "populate_data_from_data") 
{
	
	
	$user_arr = return_library_array("SELECT id, user_full_name from user_passwd", 'id', 'user_full_name');
	
	$recipe_arr = return_library_array("SELECT id, recipe_no from yd_recipe_entry_mst where status_active =1 and is_deleted=0", "id", "recipe_no");
		//die;
	$order_arr = array();
	$embl_sql ="SELECT a.embellishment_job, b.id, b.order_no from subcon_ord_mst a, subcon_ord_dtls b where a.entry_form=204 and a.embellishment_job=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$embl_sql_res=sql_select($embl_sql);
	foreach ($embl_sql_res as $row)
	{
		$order_arr[$row[csf("id")]]['job']=$row[csf("embellishment_job")];
		$order_arr[$row[csf("id")]]['po']=$row[csf("order_no")];
	}
	unset($embl_sql_res);

	
	$buyer_po_arr=array();
	$po_sql ="SELECT a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	
	$sql = sql_select("SELECT id, requ_no, requ_no_prefix, requ_prefix_num, company_id, location_id, requisition_date, requisition_basis, recipe_id, inserted_by, insert_date, entry_form, store_id, machine_id , is_apply_last_update from dyes_chem_issue_requ_mst where id=$data and status_active =1 and is_deleted=0 and entry_form=437");
	foreach ($sql as $row) 
	{
		echo "document.getElementById('txt_req_no').value = '" . $row[csf("requ_no")] . "';\n";
		echo "document.getElementById('update_id').value = '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('cbo_company_name').value = '" . $row[csf("company_id")] . "';\n";
		echo "document.getElementById('cbo_location_name').value = '" . $row[csf("location_id")] . "';\n";
		echo "document.getElementById('txt_requisition_date').value = '" . change_date_format($row[csf("requisition_date")]) . "';\n";
		echo "document.getElementById('cbo_receive_basis').value = '" . $row[csf("requisition_basis")] . "';\n";
		
		echo "document.getElementById('txt_recipe_id').value = '" . $row[csf("recipe_id")] . "';\n";
		echo "document.getElementById('txt_recipe_no').value = '" . $recipe_arr[$row[csf("recipe_id")]] . "';\n";
		echo "document.getElementById('cbo_store_name').value = '" . $row[csf("store_id")] . "';\n";
		echo "document.getElementById('txt_batch_qty').value = '" . $row[csf("store_id")] . "';\n";
		echo "document.getElementById('cbo_machine_no').value = '" . $row[csf("store_id")] . "';\n";
		
		/*echo "document.getElementById('txt_order_id').value = '" . $row[csf("order_id")] . "';\n";
		echo "document.getElementById('txt_order_no').value = '" . $order_arr[$row[csf("order_id")]]['po'] . "';\n";
		echo "document.getElementById('txt_job_no').value = '" . $row[csf("job_no")] . "';\n";
		
		echo "document.getElementById('txtbuyerPoId').value = '" . $row[csf("buyer_po_id")] . "';\n";
		echo "document.getElementById('txtbuyerPo').value = '" . $buyer_po_arr[$row[csf("buyer_po_id")]]['po'] . "';\n";
		echo "document.getElementById('txtstyleRef').value = '" . $buyer_po_arr[$row[csf("buyer_po_id")]]['style'] . "';\n";
		echo "document.getElementById('cbo_color_id').value = '" . $row[csf("color_id")] . "';\n";*/
	
		
		
		if ($row[csf("is_apply_last_update")] == 2) 
		{
			$s = 0; $msg = "";
			$recipe_data = sql_select("SELECT a.is_apply_last_update, b.id, b.updated_by, b.update_date from dyes_chem_requ_recipe_att a, pro_recipe_entry_mst b where a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and a.recipe_id=b.id and a.mst_id=" . $row[csf("id")] . "");
			foreach ($recipe_data as $recpRow) 
			{
				if ($recpRow[csf("is_apply_last_update")] == 2) 
				{
					$s++;
					$user_name = $user_arr[$recpRow[csf("updated_by")]];
					$update_dateTime = date("H:s:i d-M-Y", strtotime($recpRow[csf("update_date")]));
					if ($msg == "")
						$msg = "Recipe No- " . $recpRow[csf("id")] . " by " . $user_name . " on " . $update_dateTime;
					else
						$msg .= ", Recipe No- " . $recpRow[csf("id")] . " by " . $user_name . " on " . $update_dateTime;
				}
			}
			if ($s <= 1) 
			{
				echo "document.getElementById('last_update_message').innerHTML 	= 'After Requisition Recipe has been changed by $user_name on $update_dateTime To Revise Requisition Click Apply Last Update Button and Update.';\n";
			} 
			else 
			{
				echo "document.getElementById('last_update_message').innerHTML 	= 'After Requisition Recipe has been changed " . $msg . " To Revise Requisition Click Apply Last Update Button and Update.';\n";
			}
		} 
		else 
		{
			echo "document.getElementById('last_update_message').innerHTML 		= '';\n";
		}
		/*echo "get_php_form_data('" . $row[csf("recipe_id")] . "', 'populate_data_from_recipe_popup', 'requires/yd_dyes_chem_issue_requisition_controller' );\n";*/

		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_chemical_dyes_issue_requisition',1);\n";
		exit();
	}
}

if ($action == 'populate_data_from_recipe_popup')
 {
	/*if($db_type==0)
	{
		$data_array=sql_select("select group_concat(id) as recipe_id, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor from pro_recipe_entry_mst where id in($data)");
	}
	else
	{
		$data_array=sql_select("select listagg(id,',') within group (order by id) as recipe_id, listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor from pro_recipe_entry_mst where id in($data)");
	}*/
	$recipe_id = '';
	$total_liquor = 0;
	$batch_new_qty = 0;
	$batch_id = '';
	$all_batch_id = '';
	$data_array = sql_select("SELECT id, entry_form, batch_id, total_liquor, new_batch_weight,batch_qty from pro_recipe_entry_mst where id in($data) and status_active=1 and is_deleted=0");
	foreach ($data_array as $row) {
		$total_liquor += $row[csf("total_liquor")];
		if ($row[csf("entry_form")] == 60) {
			$batch_new_qty += $row[csf("new_batch_weight")];
		} else if ($row[csf("entry_form")] == 59) //New Add from Recipe page
		{
			$batch_new_qty += $row[csf("batch_qty")];
		} else {
			$batch_id .= $row[csf("batch_id")] . ",";
		}
		$all_batch_id .= $row[csf("batch_id")] . ",";
		$recipe_id .= $row[csf("id")] . ",";


	}
	//$batch_id=implode(",",array_unique(explode(",",$data_array[0][csf("batch_id")])));
	$recipe_id = chop($recipe_id, ',');
	$batch_id = chop($batch_id, ',');
	$all_batch_id = chop($all_batch_id, ',');
	if ($batch_id == "") $batch_id = 0;
	if ($db_type == 0) {
		$batchdata_array = sql_select("SELECT group_concat(batch_no) as batch_no, sum(case when id in($batch_id) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($all_batch_id)");
	} else {
		$batchdata_array = sql_select("SELECT listagg(CAST(batch_no  AS VARCHAR2(4000)),',') within group (order by id) as batch_no, sum(case when id in($batch_id) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($all_batch_id)");
	}
	$batch_weight += $batchdata_array[0][csf("batch_weight")];
	echo "document.getElementById('txt_recipe_id').value 				= '" . $recipe_id . "';\n";
	echo "document.getElementById('txt_recipe_no').value 				= '" . $recipe_id . "';\n";
	echo "document.getElementById('txt_batch_no').value 				= '" . $batchdata_array[0][csf("batch_no")] . "';\n";
	echo "document.getElementById('txt_batch_id').value 				= '" . $all_batch_id . "';\n";
	echo "document.getElementById('txt_tot_liquor').value 				= '" . $total_liquor . "';\n";
	//echo "document.getElementById('txt_batch_weight').value 			= '".$batch_weight."';\n";
	echo "document.getElementById('txt_batch_weight').value 			= '" . number_format($batch_new_qty,2,'.','') . "';\n";
	//echo "document.getElementById('cbo_color_id').value 				= '" . $color_id . "';\n";

	exit();
}

if ($action == "recipe_popup") 
{
	echo load_html_head_contents("Recipe No Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(id) 
        {
            $('#hidden_recipe_id').val(id);
            parent.emailwindow.hide();
        }
		
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0)
			{
				$('#search_by_td').html('Embl. Job No');
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
                <table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "--Searching Type--" ); ?></th>
                        </tr>
                        <tr>
                            <th width="200" colspan="2">Recipe Date Range</th>
                            <th width="200">Enter Recipe No</th>
                            <th style="display: none;" width="100">Search By</th>
                            <th style="display: none;" width="100" id="search_by_td">Embl. Job No</th>
                            <th>
                                <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton"/>
                                <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                                <input type="hidden" name="hidden_recipe_id" id="hidden_recipe_id" class="text_boxes" value="">
                            </th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" placeholder="From" style="width:100px;"></td>
                        <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To" style="width:100px;"></td>
                        <td><input type="text" style="width:187px;" class="text_boxes" name="txt_search_recipe" id="txt_search_recipe"/></td>
                        <td style="display: none;">
                            <?
                                $search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
                                echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                            ?>
                        </td>
                        <td style="display: none;">
                            <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:187px" placeholder="" />
                        </td>
                        <td>
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_recipe').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+'<? echo $recipe_id; ?>', 'create_recipe_search_list_view', 'search_div', 'yd_dyes_chem_issue_requisition_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"  style="width:100px;"/>
                        </td>
                    </tr>
                    <tr class="general">
                        <td colspan="6" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
            </form>
            <div id="search_div"></div>
        </div>
    </body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action == "create_recipe_search_list_view") 
{
	$data = explode("_", $data);
	$start_date = $data[0];
	$end_date = $data[1];
	$recipe_no = $data[2];
	$search_by = $data[3];
	$search_str = trim($data[4]);
	$company_id = $data[5];
	$search_type = $data[6];
	$recipe_id = $data[7];
	//$search_string = trim($data[0]);

	if ($start_date != "" && $end_date != "") 
	{
		if ($db_type==0) 
		{
			$date_cond = "and a.recipe_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-") . "'";
		} 
		else 
		{
			$date_cond = "and a.recipe_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-", 1) . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-", 1) . "'";
		}
	} 
	else $date_cond = "";
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond=""; $search_recipe_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and b.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and c.order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
			else if ($search_by==5) $style_cond=" and a.style_ref_no = '$search_str' ";
		}
		if($recipe_no!='') $search_recipe_cond=" and a.recipe_no_prefix_num='$recipe_no'";
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and b.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and c.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str%'"; 
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str%'";   
		}
		if($recipe_no!='') $search_recipe_cond=" and a.recipe_no_prefix_num like '%$recipe_no%'";
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and b.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and c.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '$search_str%'"; 
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '$search_str%'";   
		}
		if($recipe_no!='') $search_recipe_cond=" and a.recipe_no_prefix_num like '$recipe_no%'";
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and b.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and c.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str'";  
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str'";  
		}
		if($recipe_no!='') $search_recipe_cond=" and a.recipe_no_prefix_num like '%$recipe_no'";
	}
	
	$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4)|| ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	
	if ($po_ids!="") $po_idsCond=" and c.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	
	$order_arr = array();
	$embl_sql ="SELECT a.embellishment_job, b.id, b.order_no from subcon_ord_mst a, subcon_ord_dtls b where a.entry_form=204 and a.embellishment_job=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$embl_sql_res=sql_select($embl_sql);
	foreach ($embl_sql_res as $row)
	{
		$order_arr[$row[csf("id")]]['job']=$row[csf("embellishment_job")];
		$order_arr[$row[csf("id")]]['po']=$row[csf("order_no")];
	}
	unset($embl_sql_res);
	
	$color_arr = return_library_array("SELECT id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	
	
	
	/*$buyer_po_arr=array();
	$po_sql ="SELECT a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);*/
	
	
	//$sql = "SELECT a.id, a.recipe_no_prefix_num, a.recipe_no, a.recipe_date, a.color_id, a.store_id, a.job_no, b.job_no_prefix_num, c.id as order_id, c.order_no, c.buyer_po_id,c.buyer_po_no, c.buyer_style_ref from pro_recipe_entry_mst a, subcon_ord_mst b, subcon_ord_dtls c where a.job_no=b.embellishment_job and a.po_id=c.id and b.embellishment_job=c.job_no_mst and b.entry_form=204 and a.company_id='$company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_recipe_cond $search_com_cond $date_cond $po_idsCond order by a.id Desc";
	
	$sql = "SELECT a.id, a.labdip_no, a.recipe_no, a.batch_id,a.recipe_date,a.batch_qty,a.color_id,b.batch_number from yd_recipe_entry_mst a,yd_batch_mst b where a.entry_form=435 and a.company_id='$company_id' and  a.batch_id=b.id and a.status_active=1 and a.is_deleted=0 "
	
	//$sql = "select a.id, a.labdip_no, a.recipe_date, a.order_source, a.style_or_order, a.batch_id, a.color_id, a.color_range, sum(b.total_liquor) as total_liquor, group_concat(b.sub_process_id order by b.id) as sub_process_id, group_concat(concat_ws('**',b.sub_process_id,b.prod_id,b.seq_no) order by b.id) as seq_no from pro_recipe_entry_mst a, pro_recipe_entry_dtls b, pro_batch_create_mst c where a.id=b.mst_id  and a.entry_form=151 and a.batch_id=c.id and a.company_id='$company_id' and b.ratio>0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $date_cond group by a.id";
	
	//echo $sql; die;
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table">
		<thead>
			<th width="35">SL</th>
            <th width="150">Recipe Id</th>
			<th width="150">Recipe No</th>
 			<th width="80">Recipe Date</th>
            <th width="150">Batch No</th>
			<th>Color<input type="hidden" name="txt_recipe_row_id" id="txt_recipe_row_id" value="<?php echo $recipe_row_id; ?>"/></th>
		</thead>
	</table>
	<div style="width:870px; overflow-y:scroll; max-height:220px;" id="buyer_list_view" align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table" id="tbl_list_search">
			<?
            $i = 1;
            $recipe_row_id = '';
            $hidden_recipe_id = explode(",", $recipe_id);
            $nameArray = sql_select($sql);
            foreach ($nameArray as $selectResult) 
            {
                if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
    
                if (in_array($selectResult[csf('id')], $hidden_recipe_id)) 
                {
                    if ($recipe_row_id == "") $recipe_row_id = $i; else $recipe_row_id .= "," . $i;
                }
    
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value('<? echo $selectResult[csf('id')].'___'.$selectResult[csf('recipe_no')].'___'.$selectResult[csf('batch_qty')].'___'.$selectResult[csf('store_id')].'___'.$selectResult[csf('color_id')].'___'.$selectResult[csf('batch_id')].'___'.$selectResult[csf('batch_number')]; ?>')">
                    <td width="35" align="center"><? echo $i; ?></td>
                    <td width="150" align="center"><? echo $selectResult[csf('id')]; ?></td>
                    <td width="150" align="center"><? echo $selectResult[csf('recipe_no')]; ?></td>
                     <td width="80" align="center"><? echo change_date_format($selectResult[csf('recipe_date')]); ?>&nbsp;</td>
                    <td width="150" align="center"><? echo $selectResult[csf('batch_number')]; ?></td>
                     <td><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?>&nbsp;</p></td>
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

if ($action == "item_details_backup") 
{
	$data = explode("**", $data);
	$company_id = $data[0];
	$recipe_id = $data[1];
	$mst_id = $data[2];
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1200" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="40">Prod. ID</th>
            <th width="80">Item Lot</th>
			<th width="110">Item Category</th>
			<th width="100">Group</th>
			<th width="80">Sub Group</th>
			<th width="180">Item Description</th>
			<th width="50">UOM</th>
            <th width="70">Stock</th>
			<th width="40">Seq. No.</th>
			<th width="80">Ratio in %</th>
			<th width="80">Req. Qty.</th>
            <th width="50">Adj %</th>
            <th width="80">Adj type</th>
            <th>Tot. Qty.</th>
		</thead>
	</table>
	<div style="width:1200px; overflow-y:scroll; max-height:230px;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1180" class="rpt_table" id="tbl_list_search">
            <tbody>
            <?
				$pastweight_arr=array();
				$reqsn_data_arr=array();
				if($mst_id!=0)
				{
					$reqsn_sql="select id, multicolor_id, past_weight, product_id, required_qnty, adjust_percent, adjust_type, req_qny_edit from dyes_chem_issue_requ_dtls where mst_id='$mst_id' and status_active=1 and is_deleted=0";
					$reqsn_sql_res = sql_select($reqsn_sql);
					foreach ($reqsn_sql_res as $row)
					{
						$pastweight_arr[$row[csf("multicolor_id")]]=$row[csf("past_weight")];
						
						$reqsn_data_arr[$row[csf("multicolor_id")]][$row[csf("product_id")]]['qty']=$row[csf("required_qnty")];
						$reqsn_data_arr[$row[csf("multicolor_id")]][$row[csf("product_id")]]['id']=$row[csf("id")];
						$reqsn_data_arr[$row[csf("multicolor_id")]][$row[csf("product_id")]]['adjust_percent']=$row[csf("adjust_percent")];
						$reqsn_data_arr[$row[csf("multicolor_id")]][$row[csf("product_id")]]['adjust_type']=$row[csf("adjust_type")];
						$reqsn_data_arr[$row[csf("multicolor_id")]][$row[csf("product_id")]]['req_qny_edit']=$row[csf("req_qny_edit")];
					}
					unset($reqsn_sql_res);
				}
				//echo "<pre>";print_r($reqsn_data_arr);die;

				$item_group_arr = return_library_array("SELECT id, item_name from lib_item_group where status_active =1 and is_deleted=0", "id", "item_name");
                $multicolor_array = array();
                $prod_data_array = array();
                $new_prod_arr = array();
                $sql = "SELECT color_id, new_prod_id from yd_recipe_entry_dtls where mst_id=$recipe_id and status_active=1 and is_deleted=0 order by id ASC";
                $nameArray = sql_select($sql); $tot_rows=0; $prodIds='';
                foreach ($nameArray as $row) 
                {
                    if (!in_array($row[csf("color_id")], $multicolor_array)) 
                    {
                        $multicolor_array[] = $row[csf("color_id")];
                    }
					$new_prod_arr[$row[csf("color_id")]]=$row[csf("new_prod_id")];
                }
                unset($nameArray);
                
               $sql = "SELECT id, color_id, prod_id, comments, ratio, seq_no, store_id, item_lot from yd_recipe_entry_dtls where mst_id=$recipe_id and status_active=1 and is_deleted=0 order by seq_no ASC";
                $nameArray = sql_select($sql); $tot_rows=0; $prodIds='';
                foreach ($nameArray as $row) 
                {
                    $color_remark_array[$row[csf("color_id")]][$row[csf("id")]]['prod_id']= $row[csf("prod_id")];
                    $color_remark_array[$row[csf("color_id")]][$row[csf("id")]]['comments']= $row[csf("comments")];
                    $color_remark_array[$row[csf("color_id")]][$row[csf("id")]]['ratio']= $row[csf("ratio")];
                    $color_remark_array[$row[csf("color_id")]][$row[csf("id")]]['seq_no']= $row[csf("seq_no")];
					$color_remark_array[$row[csf("color_id")]][$row[csf("id")]]['store_id']= $row[csf("store_id")];
					$color_remark_array[$row[csf("color_id")]][$row[csf("id")]]['item_lot']= $row[csf("item_lot")];
                    $tot_rows++;
                    if($row[csf("prod_id")]!='')
                    {
                    	$prodIds.=$row[csf("prod_id")].",";
                    }
                    
                }
                unset($nameArray);
                //echo $sql;
                $prodIds=chop($prodIds,','); $prodIds_cond=$StoreProdIds_cond="";
                //echo $prodIds;
                if($db_type==2 && $tot_rows>1000)
                {
                    $prodIds_cond=" and (";
					$StoreProdIds_cond=" and (";
                    $prodIdsArr=array_chunk(explode(",",$prodIds),999);
                    foreach($prodIdsArr as $ids)
                    {
                        $ids=implode(",",$ids);
                        $prodIds_cond.=" id in($ids) or ";
						$StoreProdIds_cond.=" prod_id in($ids) or ";
                    }
                    $prodIds_cond=chop($prodIds_cond,'or ');
					$StoreProdIds_cond=chop($StoreProdIds_cond,'or ');
                    $prodIds_cond.=")";
					$StoreProdIds_cond.=")";
    
                }
                else
                {
                	$prodIds=implode(",",array_unique(explode(",",$prodIds)));
                    $prodIds_cond=" and id in ($prodIds)";
					$StoreProdIds_cond=" and prod_id in ($prodIds)";
                }
                
               	$sql = "SELECT id, item_category_id, item_group_id, sub_group_name, item_description, unit_of_measure, product_name_details from product_details_master where status_active=1 and is_deleted=0 and item_category_id in(5,6,7,22,23) $prodIds_cond";
                //echo $sql;
                $sql_result = sql_select($sql);
                foreach ($sql_result as $row) 
                {
                    $prod_data_array[$row[csf("id")]]= $row[csf("item_category_id")] . "**" . $row[csf("item_group_id")] . "**" . $row[csf("sub_group_name")] . "**" . $row[csf("item_description")] . "**" . $row[csf("unit_of_measure")]. "**" . $row[csf("product_name_details")];
                }
                //echo "<pre>";
                //print_r($prod_data_array);
                unset($sql_result);
				
				$sql_prod_store = "SELECT id, prod_id, store_id, lot, cons_qty  from inv_store_wise_qty_dtls where status_active=1 and is_deleted=0 $StoreProdIds_cond";
				$sql_prod_store_result = sql_select($sql_prod_store);
                foreach ($sql_prod_store_result as $row) 
                {
                    $prod_store_data_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("lot")]]= $row[csf("cons_qty")];
                }
                unset($sql_prod_store_result);
				
                $k=1; $grand_tot_ratio=0;
                foreach ($multicolor_array as $mcolor_id) 
                {
                    $i=1; $tot_ratio=0;
					$past_weight=$pastweight_arr[$mcolor_id];
					$new_prod_id=$new_prod_arr[$mcolor_id];
					
					$product_name_details="";
					$ex_prod=explode("**",$prod_data_array[$new_prod_id]);
					$product_name_details=$ex_prod[5];
                    ?>
                    <tr bgcolor="#EEEFF0">
                        <td colspan="3" align="left"><b><? echo $k.'.  '. $color_arr[$mcolor_id]; ?></b></td>
                        
                        <td colspan="3" bgcolor="#CCCCFF"><b>Product Name:-<? echo $product_name_details; ?><input type="hidden" name="hidd_nprod_id[]" id="hidd_nprod_id_<? echo $k; ?>" value="<? echo $new_prod_id; ?>"></b></td>
                        <td colspan="3" align="right" bgcolor="#CCFFFF"><b>Paste Weight</b></td>
                        <td colspan="4" bgcolor="#CCFFFF">&nbsp;<input type="text" name="txt_past_weight[]" id="txt_past_weight_<? echo $k; ?>" class="text_boxes_numeric" value="<? echo $past_weight; ?>" style="width:60px;" onChange="fnc_req_calculate(1,<? echo $k; ?>);" ><input type="hidden" name="multicolor_id[]" id="multicolor_id_<? echo $k; ?>" value="<? echo $mcolor_id; ?>"></td>
                    </tr>
                    <?
                    foreach ($color_remark_array[$mcolor_id] as $rid=>$exdata) 
                    {
                    	if($exdata['ratio'] >0 )
						{
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
							$prod_id=0; $exprod_data=""; $item_category_id=0; $item_group_id=0; $sub_group_name=""; $item_description=""; $unit_of_measure=0;
							$prod_id=$exdata['prod_id'];
							//echo $prod_id.'==';
							$store_id=$exdata['store_id'];
							$prod_store_stock=$prod_store_data_array[$prod_id][$store_id][$exdata['item_lot']];
							$exprod_data=explode("**",$prod_data_array[$prod_id]);
							
							$item_category_id=$exprod_data[0]; $item_group_id=$exprod_data[1]; $sub_group_name=$exprod_data[2]; $item_description=$exprod_data[3]; $unit_of_measure=$exprod_data[4];
							$reqsnqty=$reqsn_data_arr[$mcolor_id][$prod_id]['qty'];
							$dtls_id=$reqsn_data_arr[$mcolor_id][$prod_id]['id'];
							$adjust_percent=$reqsn_data_arr[$mcolor_id][$prod_id]['adjust_percent'];
							$adjust_type=$reqsn_data_arr[$mcolor_id][$prod_id]['adjust_type'];
							$req_qny_edit=$reqsn_data_arr[$mcolor_id][$prod_id]['req_qny_edit'];
							
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td width="30" align="center" id="sl_<? echo $k.'_'.$i; ?>"><? echo $i.''; ?></td>
								<td width="40" align="center" id="product_id_<? echo $k.'_'.$i; ?>"><? echo $prod_id; ?></td>
                                <td width="80" align="center" id="td_lot_<? echo $k.'_'.$i; ?>"><? echo $exdata['item_lot']; ?></td>
								<td width="110" style="word-break:break-all;"><? echo $item_category[$item_category_id]; ?>&nbsp;<input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $k.'_'.$i; ?>" value="<? echo $item_category_id; ?>"></td>
								<td width="100" id="item_group_id_<? echo $k.'_'.$i; ?>" style="word-break:break-all;"><? echo $item_group_arr[$item_group_id]; ?>&nbsp;<input type="hidden" name="txt_group_id[]" id="txt_group_id_<? echo $k.'_'.$i; ?>" value="<? echo $item_group_id; ?>"></td>
								<td width="80" id="sub_group_name_<? echo $k.'_'.$i; ?>" style="word-break:break-all;"><? echo $sub_group_name; ?>&nbsp;</td>
								<td width="180" id="item_description_<? echo $k.'_'.$i; ?>" style="word-break:break-all;"><? echo $item_description; ?>&nbsp;</td>
								<td width="50" align="center" id="uom_<? echo $k.'_'.$i; ?>" style="word-break:break-all;"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
                                <td width="70" align="right" id="storeStock_<? echo $k.'_'.$i; ?>" style="word-break:break-all;"><? echo number_format($prod_store_stock,2); ?></td>
								<td width="50" align="center" id="seq_no_<? echo $k.'_'.$i; ?>" style="word-break:break-all;"><? echo $exdata['seq_no']; ?></td>
								<td width="80" align="right" id="ratio_<? echo $k.'_'.$i; ?>"><? echo number_format($exdata['ratio'], 6, '.', ''); ?></td>
								<td width="80" align="center" id="reqn_qnty_<? echo $k.'_'.$i; ?>">
									<input type="text" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $k.'_'.$i; ?>" class="text_boxes_numeric" value="<? if($reqsnqty!="") echo number_format($reqsnqty, 4, '.', ''); else echo "";?>" style="width:60px" readonly >
									<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $k.'_'.$i; ?>" value="<? echo $dtls_id; ?>"></td>
								<td width="50" align="right" id="adj_per_<? echo $k.'_'.$i; ?>">
								<input type="text" name="txt_adj_per[]" id="txt_adj_per_<? echo $k.'_'.$i; ?>" class="text_boxes_numeric" style="width:40px" value="<? if($adjust_percent!="") echo number_format($adjust_percent, 0); else echo "";?>" onBlur="calculate_requs_qty('<? echo $k.'_'.$i; ?>')">
								</td>
								<td width="80" align="right" id="adj_type_<? echo $k.'_'.$i; ?>"><? echo create_drop_down("cbo_adj_type_".$k.'_'.$i, 80, $increase_decrease, "", 1, "- Select -", $adjust_type, "calculate_requs_qty('".$k.'_'.$i."')"); ?></td>
								<td align="right" id="tot_qnty_<? echo $k.'_'.$i; ?>"><input type="text" name="txt_tot_qnty[]" id="txt_tot_qnty_<? echo $k.'_'.$i; ?>" class="text_boxes_numeric" value="<? if($req_qny_edit!="") echo number_format($req_qny_edit, 4, '.', ''); else echo "";?>" style="width:60px" readonly ></td>
							</tr>
							<?
							unset($reqsnqty);
							$i++;
							$tot_ratio+=$exdata['ratio'];
                        	$grand_tot_ratio+=$exdata['ratio'];
						}
                    }
                    ?>
                    <tr class="tbl_bottom">
                        <td align="right" colspan="10"><strong>Color Total</strong>
                        	<input type="hidden" name="hidd_colorrow<? echo $k; ?>" id="hidd_colorrow<? echo $k; ?>" value="<? echo $i; ?>"></td>
                        <td align="right" id="ratiotot_<? echo $k; ?>"><? //echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                        <td align="right" id="color_reqsnqty_<? echo $k; ?>">&nbsp;</td>
                        <td align="right" id="color_adj_per_<? echo $k; ?>">&nbsp;</td>
                        <td align="right" id="color_adj_type_<? echo $k; ?>">&nbsp;</td>
                        <td align="right" id="color_tot_qnty_<? echo $k; ?>">&nbsp;</td>
                    </tr>
                    <?
					$k++;
                }
                ?>
                <tr class="tbl_bottom">
                    <td align="right" colspan="10"><strong>Grand Total</strong>
                    	<input type="hidden" name="hidd_totcolor" id="hidd_totcolor" value="<? echo $k; ?>"></td>
                    <td align="right" id="td_ratiotot"><? //echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                    <td align="right" id="td_reqsnqty">&nbsp;</td>
                    <td align="right" id="td_adj_per">&nbsp;</td>
                    <td align="right">&nbsp;</td>
                    <td align="right" id="td_tot_qnty">&nbsp;</td>
                </tr>
             </tbody>
		</table>
	</div>
	<?
	exit();
}

if ($action == "item_details") 
{

	$data = explode("**", $data);
	$company_id = $data[0];
	$sub_process_id = trim($data[1]);
	$recipe_id = $data[2];
	
	
	/*$data = explode("**", $data);
	$company_id = $data[0];
	$recipe_id = $data[1];
	$mst_id = $data[2];*/
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1350" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="83">Sub Process</th>
			<th width="50">Prod. ID</th>
            <th width="50">Lot</th>
			<th width="80">Item Category</th>
			<th width="90">Group</th>
			<th width="80">Sub Group</th>
			<th width="110">Item Description</th>
			<th width="40">UOM</th>
            <th width="80">Stock</th>
			<th width="50">Seq. No.</th>
			<th width="75">Dose Base</th>
			<th width="72">Ratio</th>
			<th width="80">Recipe Qnty.</th>
			<th width="53">Adj%.</th>
			<th width="87">Adj. Type</th>
			<th width="80">Reqn. Qnty.</th>
			<th>Comment</th>
		</thead>
	</table>
	<div style="width:1350px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1330" class="rpt_table"
		id="tbl_list_search">
		<tbody>
			<?
			$item_group_arr = return_library_array("select id, item_name from lib_item_group", "id", "item_name");
			//$batchWeight_arr = return_library_array("select id, batch_weight from pro_batch_create_mst", "id", "batch_weight");
			
			$sql_comment="select b.sub_process_id,b.prod_id as id ,b.item_lot,b.comments from yd_recipe_entry_dtls b where mst_id in (select c.id from yd_recipe_entry_mst c where c.id in($recipe_id))";
			//echo $sql_comment;
			$res_comment=sql_select($sql_comment);

			$topping_data=array();
			foreach ($res_comment as $row) {
				
				if(!empty($row[csf('comments')]))
				{
					$topping_data[$row[csf('sub_process_id')]][$row[csf('id')]][$row[csf('item_lot')]].=$row[csf('comments')]."***";
				}
			}

			$sql_comment="select b.sub_process_id,b.prod_id as id ,b.item_lot,b.comments from yd_recipe_entry_dtls b where mst_id in (select c.id from yd_recipe_entry_mst c where c.copy_from in($recipe_id))";
			//echo $sql_comment;
			$res_comment=sql_select($sql_comment);

			$copy_data=array();
			foreach ($res_comment as $row) {
				if(!empty($row[csf('comments')]))
				{
					$copy_data[$row[csf('sub_process_id')]][$row[csf('id')]][$row[csf('item_lot')]].=$row[csf('comments')]."***";
				}
 			}
			//new dev
			/*$recId = array();
			$getEntryFrom = sql_select("select entry_form from yd_recipe_entry_mst where id in($recipe_id)");
			foreach ($getEntryFrom as $dataEntryF)
		    {
				$recId = $dataEntryF;
			}
			$uniqEntrF = array_unique($recId);

			if (in_array(435, $uniqEntrF)) 
			{*/
				/* $sql = "(select p.total_liquor,p.recipe_type,p.surplus_solution,p.pickup, p.batch_id, p.entry_form,p.batch_qty,c.batch_no, a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.sub_process_id, b.dose_base, b.ratio, b.seq_no,b.sub_seq, b.new_batch_weight, b.new_total_liquor,b.liquor_ratio as liquor_ratio_dtls,b.total_liquor as total_liquor_dtls, b.item_lot, b.store_id ,b.comments
				from yd_recipe_entry_mst p, product_details_master a, yd_recipe_entry_dtls b,yd_batch_mst c  
				where p.id=b.mst_id and a.id=b.prod_id and p.batch_id=c.id and b.mst_id in($recipe_id) and b.sub_process_id in($sub_process_id) and b.ratio>0  and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0) 
				union 
				(
				select p.total_liquor,p.recipe_type,p.surplus_solution,p.pickup, p.batch_id, p.entry_form,p.batch_qty,c.batch_no, b.prod_id as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.sub_process_id, b.dose_base, b.ratio, b.seq_no,b.sub_seq, b.new_batch_weight, b.new_total_liquor,b.liquor_ratio as liquor_ratio_dtls,b.total_liquor as total_liquor_dtls, b.item_lot, b.store_id ,b.comments
				from yd_recipe_entry_mst p, yd_recipe_entry_dtls b,yd_batch_mst c  
				where p.id=b.mst_id and p.batch_id=c.id and b.mst_id in($recipe_id) and b.sub_process_id in($sub_process_id)   and b.status_active=1 and b.is_deleted=0 and p.working_company_id='$company_id'  and b.status_active=1 and b.is_deleted=0 and b.prod_id=0 and b.sub_process_id in(93,94,95,96,97,98) and p.status_active=1 and p.is_deleted=0) order by sub_seq,seq_no";*/
			//}
			
			
			/* $sql = "(select p.total_liquor,p.recipe_type,p.surplus_solution,p.pickup, p.batch_id, p.entry_form,p.batch_qty,c.batch_no, a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.sub_process_id, b.dose_base, b.ratio, b.seq_no,b.sub_seq, b.new_batch_weight, b.new_total_liquor,b.liquor_ratio as liquor_ratio_dtls,b.total_liquor as total_liquor_dtls, b.item_lot, b.store_id ,b.comments
				from yd_recipe_entry_mst p, product_details_master a, yd_recipe_entry_dtls b,yd_batch_mst c  
				where p.id=b.mst_id and a.id=b.prod_id and p.batch_id=c.id and b.mst_id in($recipe_id) and b.sub_process_id in($sub_process_id) and b.ratio>0  and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0) 
				union 
				(
				select p.total_liquor,p.recipe_type,p.surplus_solution,p.pickup, p.batch_id, p.entry_form,p.batch_qty,c.batch_no, b.prod_id as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.sub_process_id, b.dose_base, b.ratio, b.seq_no,b.sub_seq, b.new_batch_weight, b.new_total_liquor,b.liquor_ratio as liquor_ratio_dtls,b.total_liquor as total_liquor_dtls, b.item_lot, b.store_id ,b.comments
				from yd_recipe_entry_mst p, yd_recipe_entry_dtls b,yd_batch_mst c  
				where p.id=b.mst_id and p.batch_id=c.id and b.mst_id in($recipe_id) and b.sub_process_id in($sub_process_id)   and b.status_active=1 and b.is_deleted=0 and p.working_company_id='$company_id'  and b.status_active=1 and b.is_deleted=0 and b.prod_id=0 and b.sub_process_id in(93,94,95,96,97,98) and p.status_active=1 and p.is_deleted=0) order by sub_seq,seq_no";*/
				
				//,p.recipe_type,p.surplus_solution,p.pickup,
				 $sql = "select p.total_liquor, p.batch_id, p.entry_form,p.batch_qty,c.batch_number as batch_no, a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.sub_process_id, b.dose_base, b.ratio, b.seq_no,b.sub_seq, b.new_batch_weight, b.new_total_liquor,b.liquor_ratio as liquor_ratio_dtls,b.total_liquor as total_liquor_dtls, b.item_lot, b.store_id ,b.comments
				from yd_recipe_entry_mst p, product_details_master a, yd_recipe_entry_dtls b,yd_batch_mst c  
				where p.id=b.mst_id and a.id=b.prod_id and p.batch_id=c.id and b.mst_id in($recipe_id) and b.ratio>0  and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0"; 
				 
			 
			//echo $sql;
			$i = 1;
			$subprocessDataArr = array();
			$subprocessProdQntyArr = array();
			$prodDataArr = array();
			$nameArray = sql_select($sql);
			foreach ($nameArray as $selectResult)
			 {

				$current_stock = $selectResult[csf('current_stock')];
				$recipe_type = $selectResult[csf('recipe_type')];
				$surplus_solution = $selectResult[csf('surplus_solution')];
				//$pickup = $selectResult[csf('pickup')];
				$batch_wgt = $selectResult[csf('batch_qty')];//pickup,p.batch_qty
				$current_stock_check=number_format($current_stock,7,'.','');
				//(Batch weight*Pick Up/100)+Surplus Solution;
				$total_solution=($batch_wgt*$pickup/100)+$surplus_solution;
				//if($current_stock_check>0)
				//{
					$subprocessDataArr[$selectResult[csf('sub_process_id')]] .= $selectResult[csf('id')] . ",";
					$prodDataArr[$selectResult[csf('id')]] = $selectResult[csf('item_category_id')] . "**" . $selectResult[csf('item_group_id')] . "**" . $selectResult[csf('sub_group_name')] . "**" . $selectResult[csf('item_description')] . "**" . $selectResult[csf('item_size')] . "**" . $selectResult[csf('unit_of_measure')];
					$ratio = $selectResult[csf('ratio')];
					if ($selectResult[csf('dose_base')] == 1) 
					{
						 
						$perc_calculate_qnty = $selectResult[csf('total_liquor_dtls')];
						$recipe_qnty = ($perc_calculate_qnty * $ratio) / 1000;
 					} 
					else if ($selectResult[csf('dose_base')] == 2) 
					{
						$perc_calculate_qnty = $selectResult[csf('batch_qty')];
  						$recipe_qnty = ($perc_calculate_qnty * $ratio) / 100;
 					}

					$subprocessProdQntyArr[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]]['dosebase'] .= $selectResult[csf('dose_base')] . ",";
					$subprocessProdQntyArr[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]]['comments'] .= $selectResult[csf('comments')] . "***";
					/*
					if(!empty($topping_data[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]]))
					{
						$subprocessProdQntyArr[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]]['comments'] .= $topping_data[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]] . "***";
					}
					if(!empty($copy_data[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]]))
					{
						$subprocessProdQntyArr[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]]['comments'] .= $copy_data[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]] . "***";
					}*/
					$subprocessProdQntyArr[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]]['recipe_qnty'] += $recipe_qnty;
					$subprocessProdQntyArr[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]]['lq_or_bw_qnty'] += $perc_calculate_qnty;
					$subprocessProdQntyArr[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]]['seq_no'] = $selectResult[csf('seq_no')];
					$subprocessProdQntyArr[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]]['sub_seq'] = $selectResult[csf('sub_seq')];
					$subprocessProdQntyArr[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]]['store_id'] = $selectResult[csf('store_id')];
					$subprocessProdQntyArr[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]]['total_liquor_dtls'] = $selectResult[csf('total_liquor_dtls')];
					$subprocessProdQntyArr[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]]['current_stock'] = $selectResult[csf('current_stock')];
					$prodIds_arr[$selectResult[csf('id')]]=$selectResult[csf('id')];

				//}
			}
			$StoreProdIds_cond="";
			if($db_type==2 && count($prodIds_arr)>1000)
			{
				$StoreProdIds_cond=" and (";
				$prodIdsArr=array_chunk($prodIds_arr,999);
				foreach($prodIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$StoreProdIds_cond.=" prod_id in($ids) or ";
				}
				$StoreProdIds_cond=chop($StoreProdIds_cond,'or ');
				$StoreProdIds_cond.=")";
			}
			else
			{
				$StoreProdIds_cond=" and prod_id in (".implode(",",$prodIds_arr).")";
			}
			
			$sql_prod_store = "SELECT id, prod_id, store_id, cons_qty, lot  from inv_store_wise_qty_dtls where status_active=1 and is_deleted=0 $StoreProdIds_cond";
			//echo $sql_prod_store;
			$sql_prod_store_result = sql_select($sql_prod_store);
			foreach ($sql_prod_store_result as $row) 
			{
				$prod_store_data_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("lot")]]= $row[csf("cons_qty")];
			}
			unset($sql_prod_store_result);
			
			//echo "<pre>";print_r($subprocessProdQntyArr);die;
			//echo $sub_process_id;
			$sub_process_id = explode(",", $sub_process_id);
			$dosebase_mismatch_prod_id_arr = array();
			foreach ($subprocessProdQntyArr as $process_id=>$subprocess_data) 
			{
				//$subprocessData = array_unique(explode(",", substr($subprocessDataArr[$process_id], 0, -1)));
				foreach ($subprocess_data as $prod_id=>$prod_data) 
				{
					foreach ($prod_data as $item_lot=>$item_data) 
					{
						$current_stock = $subprocessProdQntyArr[$process_id][$prod_id][$item_lot]['current_stock'];
						$current_stock_check=number_format($current_stock,7,'.','');
						$store_wise_stock=$prod_store_data_array[$prod_id][$item_data['store_id']][$item_lot];
						//if($current_stock_check>0)
						//{
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
	
							$subprocessData = explode("**", $prodDataArr[$prod_id]);
							$item_category_id = $subprocessData[0];
							$item_group_id = $subprocessData[1];
							$sub_group_name = $subprocessData[2];
							$item_description = $subprocessData[3] . " " . $subprocessData[4];
							$unit_of_measure = $subprocessData[5];
							$seq_no = $subprocessProdQntyArr[$process_id][$prod_id][$item_lot]['seq_no'];
	
							$dosebaseData = array_unique(explode(",", substr($subprocessProdQntyArr[$process_id][$prod_id][$item_lot]['dosebase'], 0, -1)));
							if (count($dosebaseData) > 1)
							 {
								$dosebase = 0;
								$ratio = 0;
								$recipe_qnty = 0;
								$dosebase_mismatch_prod_id[$process_id] .= $prod_id . ",";
								//echo "KKF";
							}
							 else 
							{
								$dosebase = implode(",", $dosebaseData);
								$recipe_qnty = number_format($subprocessProdQntyArr[$process_id][$prod_id][$item_lot]['recipe_qnty'], 6, '.', '');
								$lq_or_bw_qnty = $subprocessProdQntyArr[$process_id][$prod_id][$item_lot]['lq_or_bw_qnty'];
								if ($dosebase == 1) {
									$ratio = number_format(($recipe_qnty * 1000) / $lq_or_bw_qnty, 6, '.', '');
	
								} 
								else 
								{
									$ratio = number_format(($recipe_qnty * 100) / $lq_or_bw_qnty, 6, '.', '');
	
								}
							}
							if ($ratio == 'nan' || $ratio == '') $ratio = 0;
							$total_liquor_dtls = $subprocessProdQntyArr[$process_id][$prod_id][$item_lot]['total_liquor_dtls'];
							$title="GPLL=Recipe Qty(".$recipe_qnty.")*1000/LQ or bw Qnty(".$lq_or_bw_qnty.")".' and '."% On BW==Recipe Qty(".$recipe_qnty.")*100/LQ or bw Qnty(".$lq_or_bw_qnty.")";
							if($process_id==93 || $process_id==94 || $process_id==95 || $process_id==96 || $process_id==97 || $process_id==98)
							{
								$ratio = 0;$recipe_qnty=0;	
							}
							else  $ratio =$ratio;$recipe_qnty=$recipe_qnty;
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="vertical-align:middle">
								<td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
								<td width="83" id="subprocess_id_<? echo $i; ?>"><? echo $dyeing_sub_process[$process_id]; ?>
								<input type="hidden" name="txt_subprocess_id[]" id="txt_subprocess_id_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $process_id; ?>">
								</td>
								<td width="50" id="product_id_<? echo $i; ?>" align="center"><? echo $prod_id; ?>
								<input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px" value="<? echo $prod_id; ?>">
								</td>
								<td width="50" id="lot_<? echo $i; ?>" align="center"><? echo $item_lot; ?>
								<input type="hidden" name="txt_lot[]" id="txt_lot_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px" value="<? echo $item_lot; ?>">
								</td>
								<td width="80"><p><? echo $item_category[$item_category_id]; ?></p>
								<input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px" value="<? echo $item_category_id; ?>">
								</td>
								<td width="90" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$item_group_id]; ?> &nbsp;</p></td>
								<td width="80" id="sub_group_name_<? echo $i; ?>"><p><? echo $sub_group_name; ?>&nbsp;</p></td>
								<td width="110" id="item_description_<? echo $i; ?>"><p><? echo $item_description; ?></p></td>
								<td width="40" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$unit_of_measure]; ?></td>
                                <td width="80" align="right" id="stock_<? echo $i; ?>"><? echo number_format($store_wise_stock,2); ?></td>
								<td width="50" align="center" id="seq_no_<? echo $i; ?>"><? echo $seq_no; ?></td>
								<td width="75" align="center"
								id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 70, $dose_base, "", 1, "- Select Dose Base -", $dosebase, "", 1); ?></td>
								<td width="72" align="center" title="<? echo 'Total Liquor ' . $total_liquor_dtls.' And '.$title; ?>" id="ratio_<? echo $i; ?>">
                                <input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" value="<? echo $ratio; ?>" disabled>
								</td>
								<td width="80" align="center" id="recipe_qnty_<? echo $i; ?>">
									<input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:67px" value="<? echo $recipe_qnty; ?>" disabled>
								</td>
								<td width="53" align="center" id="adj_per_<? echo $i; ?>">
									<input type="text" name="txt_adj_per[]" id="txt_adj_per_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" value="" onKeyUp="calculate_requs_qty(<? echo $i; ?>)">
								</td>
								<td width="87" align="center"
								id="adj_type_<? echo $i; ?>"><? echo create_drop_down("cbo_adj_type_$i", 80, $increase_decrease, "", 1, "- Select -", "", "calculate_requs_qty($i)"); ?></td>
								<td align="center" id="reqn_qnty_<? echo $i; ?>" width="80">
									<input type="text" name="reqn_qnty_edit[]" id="reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $recipe_qnty; ?>" style="width:75px" disabled> 
									<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="">
									<input type="hidden" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $recipe_qnty; ?>">
									<input type="hidden" name="txt_seq_no[]" id="txt_seq_no_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $seq_no; ?>">
                                    <input type="hidden" name="txt_sub_seq_no[]" id="txt_sub_seq_no_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $item_data['sub_seq']; ?>">
								</td>
								<td>
									<p style="max-width: 100px;word-wrap: break-word;">
										<?php 
											$comments= implode(", ", array_filter(array_unique(explode("***", chop($item_data['comments'],"***")))));
										?>
										<input class="text_boxes" type="text" name="comment_<? echo $i; ?>" id="txt_comment_<? echo $i; ?>" value="<?=$comments;?>" >
									</p>
								</td>
							</tr>
							<?
							$i++;
						}
					}
				}
				?>
			</tbody>
		</table>
	</div>
	<div>
	</div>
	<?
	exit();
}

if ($action == "save_update_delete") 
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0) 
		{
			mysql_query("BEGIN");
		}

		$mst_id = ""; $requ_no = "";

		if (str_replace("'", "", $update_id) == "") 
		{
			if ($db_type == 0) $year_cond = "YEAR(insert_date)";
			else if ($db_type == 2) $year_cond = "to_char(insert_date,'YYYY')";
			else $year_cond = "";//defined Later

			$new_requ_no = explode("*", return_mrr_number(str_replace("'", "", $cbo_company_name), '', 'YDDCR', date("Y", time()), 5, "select requ_no_prefix, requ_prefix_num from dyes_chem_issue_requ_mst where company_id=$cbo_company_name and entry_form=437 and $year_cond=" . date('Y', time()) . " order by id desc ", "requ_no_prefix", "requ_prefix_num"));
			$id = return_next_id("id", "dyes_chem_issue_requ_mst", 1);
			//$id = return_next_id_by_sequence( "dyes_chem_issue_requ_dtls_pk","dyes_chem_issue_requ_dtls", $con);
			$field_array = "id, requ_no, requ_no_prefix, requ_prefix_num, company_id, location_id, requisition_date, requisition_basis, recipe_id, inserted_by, insert_date, entry_form, store_id, machine_id";
			$data_array = "(" . $id . ",'" . $new_requ_no[0] . "','" . $new_requ_no[1] . "'," . $new_requ_no[2] . "," . $cbo_company_name . "," . $cbo_location_name . "," . $txt_requisition_date . "," . $cbo_receive_basis . "," . $txt_recipe_id . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',437," . $cbo_store_name . "," . $cbo_machine_no . ")"; 

			$mst_id = $id;
			$requ_no = $new_requ_no[0];
		} 
		else 
		{
			$field_array_up = "location_id*requisition_date*requisition_basis*recipe_id*machine_id*updated_by*update_date";
			$data_array_up = "" . $cbo_location_name . "*" . $txt_requisition_date . "*" . $cbo_receive_basis . "*" . $txt_recipe_id . "*" . $cbo_machine_no . "*" . $user_id . "*'" . $pc_date_time . "'";
			$mst_id = str_replace("'", "", $update_id);
			$requ_no = str_replace("'", "", $txt_req_no);
		}

		$id_att = return_next_id("id", "dyes_chem_requ_recipe_att", 1);
		$field_array_att = "id,mst_id,recipe_id";
		$recipe_id_all = explode(",", str_replace("'", "", $txt_recipe_id));
		foreach ($recipe_id_all as $recipe_id) 
		{
			if ($data_array_att != "") $data_array_att .= ",";
			$data_array_att .= "(" . $id_att . "," . $mst_id . "," . $recipe_id . ")";
			$id_att = $id_att + 1;
		}

		$id_dtls = return_next_id("id", "dyes_chem_issue_requ_dtls", 1);
		$field_array_dtls = "id, mst_id, requ_no, recipe_id, multicolor_id, nprod_id, past_weight, product_id, item_category, ratio, required_qnty, adjust_percent, adjust_type, req_qny_edit, seq_no, inserted_by, insert_date, store_id, item_lot";
		$k=0; $nprod_id_all=""; $nprod_qty_arr=array();
		//echo "10**".$tcolor_row.'=';
		for ($j=1; $j<=$tcolor_row; $j++)
		{
			$txt_past_weight = "txt_past_weight_" . $j;
			$multicolor_id = "multicolor_id_" . $j;
			$hidd_nprod_id = "hidd_nprod_id_" . $j;
			$hidd_colorrow = "hidd_colorrow" . $j;
			
			if($nprod_id_all=="") $nprod_id_all=str_replace("'", "", $$hidd_nprod_id); else $nprod_id_all.=','.str_replace("'", "", $$hidd_nprod_id);
			
			$tot_row=(str_replace("'", "", $$hidd_colorrow)*1)-1;
			
			for ($i = 1; $i <= $tot_row; $i++) 
			{
				$k++;
				$txt_prod_id = "product_id_" . $k;
				$product_lot = "product_lot_" . $k;
				$txt_item_cat = "txt_item_cat_" . $k;
				$txt_ratio = "ratio_" . $k;
				$txt_reqn_qnty = "txt_reqn_qnty_" . $k;
				
				$txt_adj_per = "txt_adj_per_" . $k;
				$cbo_adj_type = "cbo_adj_type_" . $k;
				$txt_tot_qnty = "txt_tot_qnty_" . $k;
				
				$updateIdDtls = "updateIdDtls_" . $k;
				$txt_seq_no = "seq_no_" . $k;
				$txt_ratio = str_replace("'", "", $$txt_ratio);
				if($txt_ratio)
				{
					//$id_dtls = return_next_id_by_sequence( "dyes_chem_issue_requ_dtls_pk","dyes_chem_issue_requ_dtls", $con);
					if ($data_array_dtls != "") $data_array_dtls .= ",";
					$data_array_dtls .= "(".$id_dtls.",".$mst_id.",'".$requ_no."',".$txt_recipe_id.",".$$multicolor_id.",".$$hidd_nprod_id.",".$$txt_past_weight.",".$$txt_prod_id.",".$$txt_item_cat.",'".trim($txt_ratio)."',".$$txt_reqn_qnty.",".$$txt_adj_per.",".$$cbo_adj_type.",".$$txt_tot_qnty.",".$$txt_seq_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'," . $cbo_store_name . "," . $$product_lot . ")";
					$id_dtls = $id_dtls + 1;
					$nprod_qty_arr[str_replace("'", "", $$hidd_nprod_id)]+=str_replace("'", "", $$txt_reqn_qnty);
				}
			}
		}
		//echo "10**";
		//print_r($data_array_dtls); die;
		$flag=1;
		if (str_replace("'", "", $update_id) == "") 
		{
			//echo  "10**INSERT INTO dyes_chem_issue_requ_mst (".$field_array.") VALUES ".$data_array."";die;
			$rID = sql_insert("dyes_chem_issue_requ_mst", $field_array, $data_array, 0);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
			 
		} 
		else 
		{
			$rID = sql_update("dyes_chem_issue_requ_mst", $field_array_up, $data_array_up, "id", $update_id, 0);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
		} 

		$rID_att = sql_insert("dyes_chem_requ_recipe_att", $field_array_att, $data_array_att, 0);
		if($rID_att==1 && $flag==1) $flag=1; else $flag=0;
		//echo  "10**INSERT INTO dyes_chem_issue_requ_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls.""; die;
		$rID_dtls = sql_insert("dyes_chem_issue_requ_dtls", $field_array_dtls, $data_array_dtls, 0);
		if($rID_dtls==1 && $flag==1) $flag=1; else $flag=0;
		//echo  "10**INSERT INTO dyes_chem_issue_requ_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls.""; die;
		//echo "10**".$rID ."&&". $rID_att ."&&". $rID_dtls;die;
		//check_table_status( $_SESSION['menu_id'],0);
		/*if ($flag==1) 
		{
			if( $nprod_id_all !="")
			{
				$sql_prod="select id, current_stock from from product_details_master where company_id=$cbo_company_name and item_category_id in(5,6,7,22,23) and status_active=1 and is_deleted=0";
				$current_stock_arr=array();
				$sql_prod_res=sql_select( $sql_prod );
				foreach($sql_prod_res as $row)
				{
					$current_stock_arr[$row[csf('id')]]=$row[csf('current_stock')];
				}
				unset($sql_prod_res);
				
				$exnprod_id=explode(",",$nprod_id_all);
				
				foreach($exnprod_id as $npid)
				{
					$stock=$nprod_qty_arr[$npid]+($current_stock_arr[$npid]*1);
					execute_query( "update product_details_master set current_stock='$stock' where id ='".$npid."'",1);
				}
			}
		}*/
		//echo "10**".$rID.'='.$rID_att.'='.$rID_dtls; die;
		if ($db_type == 0) 
		{
			if ($flag==1) 
			{
				mysql_query("COMMIT");
				echo "0**" . $requ_no . "**" . $mst_id;
			} 
			else 
			{
				mysql_query("ROLLBACK");
				echo "10**" . $requ_no . "**" . $mst_id;
			}
		} 
		else if ($db_type == 2 || $db_type == 1) 
		{
			if ($flag==1) 
			{
				oci_commit($con);
				echo "0**" . $requ_no . "**" . $mst_id;
			} 
			else 
			{
				oci_rollback($con);
				echo "10**" . $requ_no . "**" . $mst_id;
			}
		}
		disconnect($con);
		die;
	} 
	else if ($operation == 1)  // Update Here
	{
		$con = connect();
		if ($db_type == 0) 
		{
			mysql_query("BEGIN");
		}

		$last_update_arr = return_library_array("SELECT recipe_id, is_apply_last_update from dyes_chem_requ_recipe_att where mst_id=$update_id and status_active=1 and is_deleted=0", "recipe_id", "is_apply_last_update");
		$is_apply_last_update = str_replace("'", "", $is_apply_last_update);

		if ($is_apply_last_update == 1) 
		{
			//$field_array_up = "location_id*requisition_date*requisition_basis*recipe_id*is_apply_last_update*order_id*job_no*color_id*buyer_po_id*updated_by*update_date";
			//$data_array_up = "" . $cbo_location_name . "*" . $txt_requisition_date . "*" . $cbo_receive_basis . "*" . $txt_recipe_id . "*0*" . $txt_order_id . "*" . $txt_job_no . "*" .$cbo_color_id . "*" . $txtbuyerPoId . "*" . $user_id . "*'" . $pc_date_time . "'";
			$field_array_up = "location_id*requisition_date*requisition_basis*recipe_id*is_apply_last_update*machine_id*updated_by*update_date";
			$data_array_up = "" . $cbo_location_name . "*" . $txt_requisition_date . "*" . $cbo_receive_basis . "*" . $txt_recipe_id . "*0*" . $cbo_machine_no . "*" . $user_id . "*'" . $pc_date_time . "'";
		} 
		else 
		{
			$field_array_up = "location_id*requisition_date*requisition_basis*recipe_id*machine_id*updated_by*update_date";
			$data_array_up = "" . $cbo_location_name . "*" . $txt_requisition_date . "*" . $cbo_receive_basis . "*" . $txt_recipe_id . "*" . $cbo_machine_no . "*" . $user_id . "*'" . $pc_date_time . "'";
		}
		//echo "10**".$data_array_up;

		$mst_id = str_replace("'", "", $update_id);
		$requ_no = str_replace("'", "", $txt_req_no);

		$id_att = return_next_id("id", "dyes_chem_requ_recipe_att", 1);
		$field_array_att = "id,mst_id,recipe_id,is_apply_last_update";
		$recipe_id_all = explode(",", str_replace("'", "", $txt_recipe_id));
		foreach ($recipe_id_all as $recipe_id) 
		{
			if ($is_apply_last_update == 1) 
			{
				$apply_last_update = 0;
			} 
			else 
			{
				$apply_last_update = $last_update_arr[$recipe_id];
			}

			if ($data_array_att != "") $data_array_att .= ",";
			$data_array_att .= "(" . $id_att . "," . $mst_id . "," . $recipe_id . "," . $apply_last_update . ")";
			$id_att = $id_att + 1;
		}
		//echo "10**";
		
		//$id_dtls = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
		$field_array_dtls = "id, mst_id, requ_no, recipe_id, multicolor_id, nprod_id, past_weight, product_id, item_category, ratio, required_qnty, adjust_percent, adjust_type, req_qny_edit, seq_no, inserted_by, insert_date, store_id, item_lot";
		$k=0;
		$nprod_id_all=""; $nprod_qty_arr=array();
		$id_dtls = return_next_id("id", "dyes_chem_issue_requ_dtls", 1);
		for ($j=1; $j<=$tcolor_row; $j++)
		{
			$txt_past_weight = "txt_past_weight_" . $j;
			$multicolor_id = "multicolor_id_" . $j;
			$hidd_nprod_id = "hidd_nprod_id_" . $j;
			$hidd_colorrow = "hidd_colorrow" . $j;
			
			$tot_row=(str_replace("'", "", $$hidd_colorrow)*1)-1;
			
			if($nprod_id_all=="") $nprod_id_all=str_replace("'", "", $$hidd_nprod_id); else $nprod_id_all.=','.str_replace("'", "", $$hidd_nprod_id);
			
			for ($i = 1; $i <= $tot_row; $i++) 
			{
				$k++;
				$txt_prod_id = "product_id_" . $k;
				$product_lot = "product_lot_" . $k;
				$txt_item_cat = "txt_item_cat_" . $k;
				$txt_ratio = "ratio_" . $k;
				$txt_reqn_qnty = "txt_reqn_qnty_" . $k;
				
				$txt_adj_per = "txt_adj_per_" . $k;
				$cbo_adj_type = "cbo_adj_type_" . $k;
				$txt_tot_qnty = "txt_tot_qnty_" . $k;
				
				$updateIdDtls = "updateIdDtls_" . $k;
				$txt_seq_no = "seq_no_" . $k;
				$txt_ratio = str_replace("'", "", $$txt_ratio);
				if($txt_ratio)
				{
					//$id_dtls = return_next_id_by_sequence( "dyes_chem_issue_requ_dtls_pk","dyes_chem_issue_requ_dtls", $con);
					//echo "10**".$$txt_reqn_qnty."==";
					if ($data_array_dtls != "") $data_array_dtls .= ",";
					$data_array_dtls .= "(".$id_dtls.",".$mst_id.",'".$requ_no."',".$txt_recipe_id.",".$$multicolor_id.",".$$hidd_nprod_id.",".$$txt_past_weight.",".$$txt_prod_id.",".$$txt_item_cat.",'".trim($txt_ratio)."',".$$txt_reqn_qnty.",".$$txt_adj_per.",".$$cbo_adj_type.",".$$txt_tot_qnty.",".$$txt_seq_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'," . $cbo_store_name . "," . $$product_lot . ")";
					$id_dtls = $id_dtls + 1;
					$nprod_qty_arr[str_replace("'", "", $$hidd_nprod_id)]+=str_replace("'", "", $$txt_reqn_qnty);
				}			
				
				//echo $colorrow.'='; 
			}
		}
		//die;
		$flag=1;
		$rID = sql_update("dyes_chem_issue_requ_mst", $field_array_up, $data_array_up, "id", $update_id, 0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		$delete_att = execute_query("delete from dyes_chem_requ_recipe_att where mst_id=$update_id", 0);
		if($delete_att==1 && $flag==1) $flag=1; else $flag=0;
		$delete_dtls = execute_query("delete from dyes_chem_issue_requ_dtls where mst_id=$update_id", 0);
		if($delete_dtls==1 && $flag==1) $flag=1; else $flag=0;
		$rID_att = sql_insert("dyes_chem_requ_recipe_att", $field_array_att, $data_array_att, 0);
		if($rID_att==1 && $flag==1) $flag=1; else $flag=0;

		if ($data_array_dtls != "") 
		{
			//echo "10**INSERT INTO dyes_chem_issue_requ_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls."";die;
			$rID_dtls = sql_insert("dyes_chem_issue_requ_dtls", $field_array_dtls, $data_array_dtls, 0);
			if($rID_dtls==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		if ($flag==1)
		{
			if( $nprod_id_all !="")
			{
				//echo "10**"; 
				
				$sql_prod="SELECT id, current_stock from  product_details_master where company_id=$cbo_company_name and item_category_id in(5,6,7,22,23) and status_active=1 and is_deleted=0";
				$current_stock_arr=array();
				$sql_prod_res=sql_select( $sql_prod );
				foreach($sql_prod_res as $row)
				{
					$current_stock_arr[$row[csf('id')]]=$row[csf('current_stock')];
				}
				unset($sql_prod_res);
				
				$exnprod_id=explode(",",$nprod_id_all);
				
				foreach($exnprod_id as $npid)
				{
					$stock=$nprod_qty_arr[$npid]+($current_stock_arr[$npid]*1);
					execute_query( "update product_details_master set current_stock='$stock' where id ='".$npid."'",1);
				}
			}
		}
		
		//echo "10**".$rID.'='.$rID_att.'='.$rID_dtls.'='.$delete_att.'='.$delete_dtls; die;

		if ($db_type == 0) 
		{
			if ($flag==1) 
			{
				mysql_query("COMMIT");
				echo "1**" . $requ_no . "**" . $mst_id;
			} 
			else 
			{
				mysql_query("ROLLBACK");
				echo "10**" . $requ_no . "**" . $mst_id;
			}
		} 
		else if ($db_type == 2 || $db_type == 1) 
		{
			if ($flag==1) 
			{
				oci_commit($con);
				echo "1**" . $requ_no . "**" . $mst_id;
			} 
			else 
			{
				oci_rollback($con);
				echo "10**" . $requ_no . "**" . $mst_id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  //Delete here======================================================================================
	{
		
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$updateid=str_replace("'", "", $update_id);
		$req_no=str_replace("'", "", $txt_req_no);
		
		if ($updateid== "" || $req_no== "") 
		{
			echo "15";
			disconnect($con);
			exit();
		}
		
		for($i=1;$i<=$total_row; $i++)
		{
			$updateIdDtls="updateIdDtls_".$i;
			if(str_replace("'","",$$updateIdDtls)!="")
			{
				$inv_transaction_data_arr[str_replace("'",'',$$updateIdDtls)]=explode("*",("0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				$deleted_id_arr[]=str_replace("'",'',$$updateIdDtls);
			}
			
		}
		
		$mrrsql= sql_select("select  issue_number,req_no, req_id  from  inv_issue_master where req_id=$updateid  and  entry_form = 298 and  status_active=1 and  is_deleted=0");
			$mrr_data=array();
			foreach($mrrsql as $row)
			{
				
				$all_req_no.=$row[csf('req_no')].",";
				$all_issue_id.=$row[csf('issue_number')].",";
				
			}
			$all_req_no=chop($all_req_no,",");
			$all_issue_id=chop($all_issue_id,",");
			$all_req_trans_id_count=count($mrrsql)	;
			if($all_req_trans_id_count)
			{
				if($all_req_trans_id_count>0)
				{
					echo "50**Delete restricted, This Information is used in another Table."."  Requisition Number ".$do_rcv_number=str_replace("'","",$all_req_no)."  Issue Number ".$do_rcv_number=str_replace("'","",$all_issue_id); 
					disconnect($con); 
					oci_rollback($con); die;
				}
			}
			$field_arr="status_active*is_deleted*updated_by*update_date";
			$data_arr="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$field_arr_up="status_active*is_deleted";
			$data_arr_up="0*1";
			$rID=sql_update("dyes_chem_issue_requ_mst",$field_arr,$data_arr,"id",$update_id,0);
			$rID1=execute_query(bulk_update_sql_statement("dyes_chem_issue_requ_dtls","id",$field_arr,$inv_transaction_data_arr,$deleted_id_arr));
			$rID2=sql_update("dyes_chem_requ_recipe_att",$field_arr_up,$data_arr_up,"mst_id",$update_id,0);
		
		//echo "10**".$rID."==".$rID1."==".$rID2; die;
		if ($db_type == 0) {
			if ($rID && $rID1 &&  $rID2) {
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $txt_req_no);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", "", $txt_req_no);
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID1 &&  $rID2) {
				oci_commit($con);
				echo "2**" . str_replace("'", "", $txt_req_no);
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_req_no);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "chemical_dyes_issue_requisition_print") 
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company=$data[0];
	$location=$data[3];
	$company_library = return_library_array("SELECT id, company_name from lib_company where status_active =1 and is_deleted=0", "id", "company_name");
	$buyer_library = return_library_array("SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0", "id", "buyer_name");
	$item_group_arr = return_library_array("SELECT id,item_name from lib_item_group where status_active =1 and is_deleted=0", 'id', 'item_name');
	$color_arr = return_library_array("SELECT id, color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');
	
	$location_arr = return_library_array("SELECT id, location_name from lib_location where status_active =1 and is_deleted=0", 'id', 'location_name');
	$imge_arr=return_library_array( "SELECT master_tble_id, image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	
	$order_arr = array();
	$embl_sql ="SELECT a.embellishment_job, b.id, b.order_no from subcon_ord_mst a, subcon_ord_dtls b where a.entry_form=204 and a.embellishment_job=b.job_no_mst and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0";
	$embl_sql_res=sql_select($embl_sql);
	foreach ($embl_sql_res as $row)
	{
		$order_arr[$row[csf("id")]]['job']=$row[csf("embellishment_job")];
		$order_arr[$row[csf("id")]]['po']=$row[csf("order_no")];
	}
	unset($embl_sql_res);
	
	$buyer_po_arr=array();
	$po_sql ="SELECT a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.status_active =1 and b.is_deleted=0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);

	$sql_mst = "SELECT id, requ_no, requ_no_prefix, requ_prefix_num, company_id, location_id, requisition_date, requisition_basis, recipe_id, order_id, job_no, buyer_po_id, entry_form from dyes_chem_issue_requ_mst where id='$data[1]' and status_active =1 and is_deleted=0";
	//echo $sql_mst;
	$dataArray = sql_select($sql_mst); $party_name="";
	$recipe_id=$dataArray[0][csf('recipe_id')];
	
	$recipe_arr=array();
	if($recipe_id!="")
	{
		$recipe_sql=sql_select("SELECT id, within_group, buyer_id, color_id from pro_recipe_entry_mst where entry_form=220 and id='$recipe_id' and status_active=1 and is_deleted=0");
		foreach ($recipe_sql as $row) 
		{
			$party_name='';
			if($row[csf('within_group')]==1) $party_name=$company_library[$row[csf('buyer_id')]];
			else if($row[csf('within_group')]==2) $party_name=$buyer_library[$row[csf('buyer_id')]];
			
			$recipe_arr[$row[csf('id')]]['party']=$party_name; 
			$recipe_arr[$row[csf('id')]]['color']=$row[csf('color_id')]; 
		}
		unset($recipe_sql);
	}
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
    <div style="width:1140px;">
        <table width="100%" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="70" align="right"> 
                    <img  src='../../<? echo $com_dtls[2]; ?>' height='100%' width='100%' />
                </td>
                <td>
                    <table width="100%" cellspacing="0" align="center">
                        <tr>
                            <td align="center" style="font-size:20px"><strong ><? echo $com_dtls[0]; ?></strong> </td>
                        </tr>
                        <tr>
                            <td align="center"  style="font-size:16px"><strong>Unit : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                        </tr>
                        <tr class="form_caption">
                            <td  align="center" style="font-size:14px"> <? echo $com_dtls[1]; ?></td>  
                        </tr>
                        <tr>
                            <td align="center" style="font-size:18px"><strong><? echo $data[2]; ?></strong></td>
                        </tr>
                    </table>
                </td>
                <td width="170" valign="top" ><span style="font-size: 12px; float: right;">Print Date : <? echo date('d-m-Y H:i A'); ?></span></td>
            </tr>
        </table>
        <table width="100%" cellpadding="0" cellspacing="0" >  
            <tr>
                <td width="130"><strong>Requisition No:</strong></td>
                <td width="175"><? echo $dataArray[0][csf('requ_no')]; ?></td>
                <td width="130"><strong>Requisition Date: </strong></td>
                <td width="175px"> <? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
                <td width="130"><strong>Requisition Basis: </strong></td>
                <td width="175"><? echo $receive_basis_arr[$dataArray[0][csf('requisition_basis')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Party Name:</strong></td>
                <td><? echo $recipe_arr[$recipe_id]['party']; ?></td>
                <td><strong>Job No:</strong></td>
                <td> <? echo $dataArray[0][csf('job_no')]; ?></td>
                <td><strong>Color:</strong></td>
                <td><? echo $color_arr[$recipe_arr[$recipe_id]['color']]; ?></td>
            </tr>
            <tr>
                <td><strong>Order:</strong></td>
                <td><? echo $order_arr[$dataArray[0][csf('order_id')]]['po']; ?></td>
                <td><strong>Buyer Po:</strong></td>
                <td> <? echo $buyer_po_arr[$dataArray[0][csf('buyer_po_id')]]['po']; ?></td>
                <td><strong>Buyer Style:</strong></td>
                <td><? echo $buyer_po_arr[$dataArray[0][csf('buyer_po_id')]]['style']; ?></td>
            </tr>
        </table>
        <br>
        <div style="width:100%;">
            <table align="right" cellspacing="0" width="1140" border="1" rules="all" class="rpt_table">
                <thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
                    <th width="30">SL</th>
                    <th width="100">Item Cat.</th>
                    <th width="100">Item Group</th>
                    <th width="80">Item Lot</th>
                    <th width="80">Sub Group</th>
                    <th width="180">Item Description</th>
                    <th width="50">UOM</th>
                    <th width="70">Stock</th>
                    <th width="40">Seq. No.</th>
                    <th width="70">Ratio in %</th>
                    <th width="90">Req. Qty.</th>
                    <th width="80">Adj. %</th>
                    <th width="50">Adj. Type</th>
                    <th>Tot. Qty.</th>
                </thead>
				<?
				$mst_id = $data[1];
				$com_id = $data[0];
				$pastweight_arr=array();
				$reqsn_data_arr=array();
				$reqsn_sql="SELECT id, multicolor_id, past_weight, product_id, required_qnty, adjust_percent, adjust_type, req_qny_edit from dyes_chem_issue_requ_dtls where mst_id='$mst_id' and status_active=1 and is_deleted=0";
				//echo $reqsn_sql;
				$reqsn_sql_res = sql_select($reqsn_sql);
				foreach ($reqsn_sql_res as $row)
				{
					$pastweight_arr[$row[csf("multicolor_id")]]=$row[csf("past_weight")];
					
					$reqsn_data_arr[$row[csf("multicolor_id")]][$row[csf("product_id")]]['qty']=$row[csf("required_qnty")];
					$reqsn_data_arr[$row[csf("multicolor_id")]][$row[csf("product_id")]]['id']=$row[csf("id")];
					$reqsn_data_arr[$row[csf("multicolor_id")]][$row[csf("product_id")]]['adjust_percent']=$row[csf("adjust_percent")];
					$reqsn_data_arr[$row[csf("multicolor_id")]][$row[csf("product_id")]]['adjust_type']=$row[csf("adjust_type")];
					$reqsn_data_arr[$row[csf("multicolor_id")]][$row[csf("product_id")]]['req_qny_edit']=$row[csf("req_qny_edit")];
				}
				unset($reqsn_sql_res);

				$multicolor_array = array();
				$prod_data_array = array();
				$color_remark_array = array();
				$sql = "SELECT color_id from pro_recipe_entry_dtls where mst_id=$recipe_id and status_active=1 and is_deleted=0 order by id ASC";
				$nameArray = sql_select($sql); $tot_rows=0; $prodIds='';
				foreach ($nameArray as $row) 
				{
					if (!in_array($row[csf("color_id")], $multicolor_array)) 
					{
						$multicolor_array[] = $row[csf("color_id")];
					}
				}
				unset($nameArray);
				
				$sql = "SELECT id, color_id, prod_id, comments, ratio, seq_no, store_id, item_lot from pro_recipe_entry_dtls where mst_id=$recipe_id and status_active=1 and is_deleted=0 order by seq_no ASC";
				$nameArray = sql_select($sql); $tot_rows=0; $prodIds='';
				foreach ($nameArray as $row) 
				{
					$color_remark_array[$row[csf("color_id")]][$row[csf("id")]]['prod_id']= $row[csf("prod_id")];
					$color_remark_array[$row[csf("color_id")]][$row[csf("id")]]['store_id']= $row[csf("store_id")];
					$color_remark_array[$row[csf("color_id")]][$row[csf("id")]]['comments']= $row[csf("comments")];
					$color_remark_array[$row[csf("color_id")]][$row[csf("id")]]['ratio']= $row[csf("ratio")];
					$color_remark_array[$row[csf("color_id")]][$row[csf("id")]]['seq_no']= $row[csf("seq_no")];
					$color_remark_array[$row[csf("color_id")]][$row[csf("id")]]['item_lot']= $row[csf("item_lot")];
					$tot_rows++;
					$prodIds.=$row[csf("prod_id")].",";
				}
				unset($nameArray);
				
				$prodIds=chop($prodIds,','); $prodIds_cond=$StoreProdIds_cond="";
				
				if($db_type==2 && $tot_rows>1000)
				{
					$prodIds_cond=" and (";
					$StoreProdIds_cond=" and (";
					$prodIdsArr=array_chunk(explode(",",$prodIds),999);
					foreach($prodIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$prodIds_cond.=" id in($ids) or ";
						$StoreProdIds_cond.=" prod_id in($ids) or ";
					}
					$prodIds_cond=chop($prodIds_cond,'or ');
					$prodIds_cond.=")";
					
					$StoreProdIds_cond=chop($StoreProdIds_cond,'or ');
					$StoreProdIds_cond.=")";

				}
				else
				{
					$prodIds_cond=" and id in ($prodIds)";
					$StoreProdIds_cond=" and prod_id in ($prodIds)";
				}
				
				$sql = "SELECT id, item_category_id, item_group_id, sub_group_name, item_description, unit_of_measure from product_details_master where status_active=1 and is_deleted=0 and company_id='$com_id' $prodIds_cond and item_category_id in(5,6,7,22,23) order by id";
				
				//echo $sql;
				$sql_result = sql_select($sql);

				foreach ($sql_result as $row) 
				{
					$prod_data_array[$row[csf("id")]]= $row[csf("item_category_id")] . "**" . $row[csf("item_group_id")] . "**" . $row[csf("sub_group_name")] . "**" . $row[csf("item_description")] . "**" . $row[csf("unit_of_measure")];
				}
				unset($sql_result);
				
				$sql_prod_store = "SELECT id, prod_id, store_id, cons_qty  from inv_store_wise_qty_dtls where status_active=1 and is_deleted=0 $StoreProdIds_cond";
				//echo $sql_prod_store;
				$sql_prod_store_result = sql_select($sql_prod_store);
				foreach ($sql_prod_store_result as $row) 
				{
					$prod_store_data_array[$row[csf("prod_id")]][$row[csf("store_id")]]= $row[csf("cons_qty")];
				}
				unset($sql_prod_store_result);
				
				$k=1; $grand_tot_req=0;

				foreach ($multicolor_array as $mcolor_id) 
				{
					$i=1; $tot_req=$tot_edit_req=0;
					?>
                    <tr bgcolor="#EEEFF0">
                        <td colspan="7" align="left"><b><? echo $k.'.  '. $color_arr[$mcolor_id].';'; ?></b></td>
                        <td colspan="7" align="left"><b>Paste Weight:- <? echo $pastweight_arr[$mcolor_id]; ?></b></td>
                    </tr>
					<?
					foreach ($color_remark_array[$mcolor_id] as $rid=>$exdata) 
					{
						if($exdata['ratio'] >0 ){
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						$prod_id=0; $exprod_data=""; $item_category_id=0; $item_group_id=0; $sub_group_name=""; $item_description=""; $unit_of_measure=0; $reqsn_qty=0;
						$prod_id=$exdata['prod_id'];
						$store_wise_stock=$prod_store_data_array[$exdata['prod_id']][$exdata['store_id']];
						$exprod_data=explode("**",$prod_data_array[$prod_id]);
						
						$item_category_id=$exprod_data[0]; $item_group_id=$exprod_data[1]; $sub_group_name=$exprod_data[2]; $item_description=$exprod_data[3]; $unit_of_measure=$exprod_data[4];
						$reqsn_qty=$reqsn_data_arr[$mcolor_id][$prod_id]['qty'];
						$adjust_percent=$reqsn_data_arr[$mcolor_id][$prod_id]['adjust_percent'];
						$adjust_type=$reqsn_data_arr[$mcolor_id][$prod_id]['adjust_type'];
						$req_qny_edit=$reqsn_data_arr[$mcolor_id][$prod_id]['req_qny_edit'];
						
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>">
                            <td><? echo $i.''; ?></td>
                            <td><p><? echo $item_category[$item_category_id]; ?>&nbsp;</p></td>
                            <td><p><? echo $item_group_arr[$item_group_id]; ?>&nbsp;</p></td>
                            <td align="center"><? echo $exdata['item_lot']; ?></td>
                            <td><p><? echo $sub_group_name; ?>&nbsp;</p></td>
                            <td><p><? echo $item_description; ?>&nbsp;</p></td>
                            <td align="center"><p><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</p></td>
                            <td align="right"><p><? echo number_format($store_wise_stock,2); ?></p></td>
                            <td align="center"><? echo $exdata['seq_no']; ?></td>
                            <td align="right"><? echo number_format($exdata['ratio'], 4, '.', ''); ?></td>
                            <td align="right"><? echo number_format($reqsn_qty, 4, '.', ''); ?></td>
                            <td align="right"><? echo number_format($adjust_percent, 4, '.', ''); ?></td>
                            <td><p><? echo $increase_decrease[$adjust_type]; ?>&nbsp;</p></td>
                            <td align="right"><? echo number_format($req_qny_edit, 4, '.', ''); ?></td>
                        </tr>
						<?
					}
						$tot_req+=$reqsn_qty;
						$tot_edit_req+=$req_qny_edit;
						$grand_tot_req+=$reqsn_qty;
						$grand_tot_edit_req+=$req_qny_edit;
						$i++;
					}
					$k++;
					?>
                    <tr class="tbl_bottom">
                        <td align="right" colspan="9"><strong>Color Total</strong></td>
                        <td>&nbsp;</td>
                        <td align="right"><? echo number_format($tot_req, 4, '.', ''); ?>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right"><? echo number_format($tot_edit_req, 4, '.', ''); ?>&nbsp;</td>
                    </tr>
					<?
				}
				?>
                <tr class="tbl_bottom">
                    <td align="right" colspan="9"><strong>Grand Total</strong></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($grand_tot_req, 4, '.', ''); ?>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($grand_tot_edit_req, 4, '.', ''); ?>&nbsp;</td>
                </tr>
            </table>
        </div>
         <br>
        <div style="width:100%;">
             <table align="left" cellspacing="0" width="340" border="1" rules="all" class="rpt_table">
                <thead bgcolor="#dddddd" align="lrft">
                    <th width="30">SL</th>
                    <th width="180">Item Description</th>
                    <th width="50">UOM</th>
                    <th>Tot. Qty.</th>
                </thead>
				<?
				
				
				 $reqsnsql="SELECT  b.unit_of_measure,b.item_description,sum(a.req_qny_edit) as req_qny_edit  from dyes_chem_issue_requ_dtls a,product_details_master b where a.mst_id='$mst_id' and  a.product_id=b.id and a.status_active=1 and a.is_deleted=0 group by b.item_description,b.unit_of_measure";
				
				$reqsnsql_res=sql_select($reqsnsql);
				$k=1; $grand_tot_req=0;

				foreach ($reqsnsql_res as $row) 
				{
					 	$tot_req=$tot_edit_req=0;
						if ($k % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						
						$itemdescription=$row[csf("item_description")];
						$reqqnyedit=$row[csf("req_qny_edit")];
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>">
                            <td><? echo $k.''; ?></td>
                            <td><p><? echo $itemdescription; ?>&nbsp;</p></td>
                            <td align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?>&nbsp;</p></td>
                            <td align="right"><? echo number_format($reqqnyedit, 4, '.', ''); ?></td>
                        </tr>
						<?
						$grandtoteditreq+=$reqqnyedit;
					$k++;
				}
				?>
                <tr class="tbl_bottom">
                    <td align="right" colspan="3"><strong>Grand Total</strong></td>
                    <td align="right"><? echo number_format($grandtoteditreq, 4, '.', ''); ?>&nbsp;</td>
                </tr>
            </table>
            <br>
			<?
			echo signature_table(139, $com_id, "1100px");
			?>
        </div>
    </div>
	<?
	exit();
}
?>
