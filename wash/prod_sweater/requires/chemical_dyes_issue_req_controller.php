<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
//--------------------------------------------------------------------------------------------

$color_arr = return_library_array("select id,color_name from lib_color", "id", "color_name");

if ($action == "load_drop_down_location") 
{
	echo create_drop_down("cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-Select Location-", 0, "");
	exit();
}

if ($action == "company_wise_report_button_setting")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."'  and module_id=20 and report_id=102 and is_deleted=0 and status_active=1");

	$print_report_format_arr=explode(",",$print_report_format);
	echo "$('#print').hide();\n";
	
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==78){echo "$('#print').show();\n";}			
		}
	}	
	exit();
}

if ($action == "requisition_popup") 
{
	echo load_html_head_contents("Requisition Popup Info", "../../../", 1, 1, $unicode);
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
			if(val==1 || val==0) $('#search_by_td').html('Wash Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Job No');
			else if(val==4) $('#search_by_td').html('Po No');
			else if(val==5) $('#search_by_td').html('Style Ref.');
		}
 </script>

</head>
<body>
	<div align="center" style="width:780px; margin: 0 auto;">
		<form name="searchfrm" id="searchfrm">
			<fieldset style="width:780px;">
				<table cellpadding="0" cellspacing="0" width="780" border="1" rules="all" class="rpt_table">
					<thead>
                    	<tr>
                            <th colspan="7"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                        <tr>
                            <th width="130" colspan="2">Requisition Date Range</th>
                            <th width="100">Req/Recipe By</th>
                            <th width="140" id="search_by_td_up">Enter Requisition No</th>
                            <th width="100">Search By</th>
                            <th width="100" id="search_by_td">Job No</th>
                            <th>
                                <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton"/>
                                <input type="hidden" name="hidden_sys_id" id="hidden_sys_id" class="text_boxes" value="">
                            </th>
                        </tr>
					</thead>
					<tr class="general">
						<td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px;"></td>
						<td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px;"></td>
						<td>
							<?
							$search_by_arr = array(1 => "Requisition No", 2 => "Recipe No");
							$dd = "change_search_event(this.value, '0*0', '0*0', '../') ";
							echo create_drop_down("cbo_req_recipe_by", 150, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td><input type="text" style="width:130px;" class="text_boxes" name="txt_search_req_recipe" id="txt_search_req_recipe"/></td>
                        <td>
							<?
                                $search_by_arr=array(3=>"Job No",4=>"Po No",5=>"Style Ref.");
                                echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",3,'search_by(this.value)',0 );
                            ?>
                        </td>
                        <td><input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" /></td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_req_recipe').value+'_'+document.getElementById('cbo_req_recipe_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+'<? echo $company; ?>', 'create_requisition_search_list_view', 'search_div', 'chemical_dyes_issue_req_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;"/>
						</td>
					</tr>
					<tr>
						<td colspan="7" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
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
	
	$po_ids=''; $buyer_po_arr=array();
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	//echo $po_ids;
	if ($po_ids!="") $po_idsCond=" and order_id in ($po_ids)"; else $po_idsCond="";
	
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
	$company_arr = return_library_array("select id,company_name from lib_company", "id", "company_name");
	
	$recipe_arr = return_library_array("select id, recipe_no from pro_recipe_entry_mst", "id", "recipe_no");

	$sql = "select id, requ_no, requ_prefix_num, $year_field, company_id, requisition_date, recipe_id, order_id, job_no from dyes_chem_issue_requ_mst where company_id=$company and entry_form=391 and status_active=1 and is_deleted=0 $date_cond $search_field_cond $po_idsCond order by id DESC";//$search_field_cond
	//echo $sql;
	$result = sql_select($sql);
	foreach($result as $row)
	{
		$all_order_id[$row[csf("order_id")]]=$row[csf("order_id")];
	}
	
	if(count($all_order_id)>0)
	{
		$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in (".implode(",",$all_order_id).")";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		}
		unset($po_sql_res);
	}
	?>
	<div align="center">
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="60">Requisition No</th>
			<th width="60">Year</th>
			<th width="70">Requisition Date</th>
			<th width="110">Recipe No</th>
			<th width="110">Job No</th>
			<th width="100">Order No</th>
            <th>Style Ref.</th>
		</thead>
	</table>
	<div style="width:780px; max-height:250px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="760" class="rpt_table" id="tbl_list_search">
		<?
		$i = 1;
		foreach ($result as $row) 
		{
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
			
			$buyer_po=""; $buyer_style="";
			$buyer_po_id=explode(",",$row[csf('order_id')]);
			foreach($buyer_po_id as $po_id)
			{
				if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
				if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
			}
			$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
			$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));

			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')].'___'.$row[csf('recipe_id')]; ?>');">
				<td width="30"><? echo $i; ?></td>
				<td width="60"><p><? echo $row[csf('requ_prefix_num')]; ?></p></td>
				<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
				<td width="70" align="center"><p><? echo change_date_format($row[csf('requisition_date')]); ?></p></td>
                <td width="110"><p><? echo $recipe_arr[$row[csf('recipe_id')]]; ?></p></td>
				<td width="110"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
				
				<td width="100" style="word-break:break-all"><p><?=$buyer_po; ?></p></td>
                <td style="word-break:break-all"><p><?=$buyer_style; ?></p></td>
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
	$data=explode("**",$data);
	$user_arr = return_library_array("select id, user_full_name from user_passwd", 'id', 'user_full_name');
	
	$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');
	//$recipe_sql = "select a.id,a.recipe_no, b.subcon_job, c.id as order_id, c.order_no, c.buyer_po_id, d.batch_no, d.dyeing_machine, sum(e.roll_no) as roll_no, d.operation_type from pro_recipe_entry_mst a, subcon_ord_mst b, subcon_ord_dtls c ,pro_batch_create_mst d, pro_batch_create_dtls e where  b.subcon_job=c.job_no_mst and a.batch_id=d.id and d.id=e.mst_id and e.po_id=c.id and a.entry_form=300 and b.entry_form=295 and d.entry_form=316  and a.company_id=$data[0] and a.id=$data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by  a.id, a.recipe_no, b.subcon_job, c.id, c.order_no, c.buyer_po_id, d.batch_no, d.dyeing_machine,d.operation_type order by a.id Desc";
	
	$recipe_sql = "select a.id, a.labdip_no, a.recipe_no, a.recipe_date, a.color_id, a.job_no, b.job_no_prefix_num, b.buyer_name, b.style_ref_no, c.id as order_id, c.po_number, d.batch_no, d.machine_no, d.operation_type, b.job_no, sum(e.qty_pcs) as qty_pcs 
	from pro_recipe_entry_mst a, wo_po_details_master b, wo_po_break_down c, pro_bundle_batch_mst d, pro_bundle_batch_dtls e 
	where b.job_no=c.job_no_mst and a.batch_id=d.id and d.id=e.mst_id and e.po_id=c.id and a.entry_form=390 and a.company_id='$data[0]' and a.id=$data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_recipe_cond $search_com_cond $date_cond $po_idsCond 
	group by a.id, a.labdip_no, a.recipe_no, a.recipe_date, a.color_id, a.job_no, b.job_no_prefix_num, b.buyer_name, b.style_ref_no, c.id, c.po_number, d.batch_no, d.machine_no, d.operation_type, b.job_no 
	order by a.id Desc";
	$recipe_sql_res=sql_select($recipe_sql);
	foreach ($recipe_sql_res as $row)
	{
		$recipe_arr[$row[csf("id")]]['job'] 	 	=$row[csf("job_no")];
		$recipe_arr[$row[csf("id")]]['po'] 		 	=$row[csf("po_number")];
		$recipe_arr[$row[csf("id")]]['recipe_no'] 	=$row[csf("recipe_no")];
		$recipe_arr[$row[csf("id")]]['batch_no'] 	=$row[csf("batch_no")];
		$recipe_arr[$row[csf("id")]]['machine_no'] 	=$machine_arr[$row[csf("machine_no")]];
		$recipe_arr[$row[csf("id")]]['roll_no'] 	=$row[csf("qty_pcs")];
		$recipe_arr[$row[csf("id")]]['operation_type'] 	=$row[csf("operation_type")];
	}
	unset($recipe_sql_res);
	
	$sql = sql_select("select id, requ_no, company_id, location_id, requisition_date, requisition_basis, recipe_id, order_id, job_no, is_apply_last_update, store_id from dyes_chem_issue_requ_mst where id=$data[2]");
	foreach ($sql as $row) 
	{
		echo "document.getElementById('txt_req_no').value = '" . $row[csf("requ_no")] . "';\n";
		echo "document.getElementById('update_id').value = '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('cbo_company_name').value = '" . $row[csf("company_id")] . "';\n";
		echo "document.getElementById('cbo_location_name').value = '" . $row[csf("location_id")] . "';\n";
		echo "document.getElementById('cbo_store_name').value = '" . $row[csf("store_id")] . "';\n";
		echo "document.getElementById('txt_requisition_date').value = '" . change_date_format($row[csf("requisition_date")]) . "';\n";
		echo "document.getElementById('cbo_receive_basis').value = '" . $row[csf("requisition_basis")] . "';\n";
		
		echo "document.getElementById('txt_recipe_id').value = '" . $row[csf("recipe_id")] . "';\n";
		echo "document.getElementById('txt_recipe_no').value = '" . $recipe_arr[$row[csf("recipe_id")]]['recipe_no'] . "';\n";
		
		echo "document.getElementById('txt_order_id').value = '" . $row[csf("order_id")] . "';\n";
		echo "document.getElementById('txt_order_no').value = '" . $recipe_arr[$row[csf("recipe_id")]]['po'] . "';\n";
		echo "document.getElementById('txt_job_no').value = '" . $row[csf("job_no")] . "';\n";
		
		//echo "document.getElementById('txtbuyerPoId').value = '" . $row[csf("buyer_po_id")] . "';\n";
		//echo "document.getElementById('txtbuyerPo').value = '" . $buyer_po_arr[$row[csf("buyer_po_id")]]['po'] . "';\n";
		echo "document.getElementById('txtstyleRef').value = '" . $buyer_po_arr[$row[csf("order_id")]]['style'] . "';\n";
		echo "document.getElementById('txtBatchNo').value = '" . $recipe_arr[$row[csf("recipe_id")]]['batch_no'] . "';\n";
		echo "document.getElementById('txtMachineNo').value = '" . $recipe_arr[$row[csf("recipe_id")]]['machine_no'] . "';\n";
		echo "document.getElementById('txtBatchQty').value = '" . $recipe_arr[$row[csf("recipe_id")]]['roll_no'] . "';\n";
		echo "document.getElementById('txtoperation').value = '" . $wash_operation_arr[$recipe_arr[$row[csf("recipe_id")]]['operation_type'] ] . "';\n";
		echo "document.getElementById('hidden_operation_id').value = '" . $recipe_arr[$row[csf("recipe_id")]]['operation_type'] . "';\n";

		if ($row[csf("is_apply_last_update")] == 2) 
		{
			$s = 0; $msg = "";
			$recipe_data = sql_select("select a.is_apply_last_update, b.id, b.updated_by, b.update_date from dyes_chem_requ_recipe_att a, pro_recipe_entry_mst b where a.recipe_id=b.id and a.mst_id=" . $row[csf("id")] . "");
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
		//echo "get_php_form_data('" . $row[csf("recipe_id")] . "', 'populate_data_from_recipe_popup', 'requires/chemical_dyes_issue_req_controller' );\n";

		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_chemical_dyes_issue_requisition',1);\n";
		exit();
	}
}

if ($action == "recipe_popup") 
{
	echo load_html_head_contents("Recipe No Info", "../../../", 1, 1, '', '', '');
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
			if(val==3) $('#search_by_td').html('Job No');
			else if(val==4) $('#search_by_td').html('Po No');
			else if(val==5) $('#search_by_td').html('Style Ref');
			else if(val==6) $('#search_by_td').html('Batch No');
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
                            <th width="160" colspan="2">Recipe Date Range</th>
                            <th width="100">Enter Recipe No</th>
                            <th width="100">Search By</th>
                            <th width="100" id="search_by_td">Job No</th>
                            <th>
                                <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton"/>
                                <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                                <input type="hidden" name="hidden_recipe_id" id="hidden_recipe_id" class="text_boxes" value="">
                            </th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" placeholder="From" style="width:70px;"></td>
                        <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To" style="width:70px;"></td>
                        <td><input type="text" style="width:90px;" class="text_boxes" name="txt_search_recipe" id="txt_search_recipe"/></td>
                        <td>
                            <?
                                $search_by_arr=array(3=>"Job No",4=>"Po No",5=>"Style Ref",6=>"Batch No");
                                echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:90px" placeholder="" />
                        </td>
                        <td>
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_recipe').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+'<? echo $recipe_id; ?>', 'create_recipe_search_list_view', 'search_div', 'chemical_dyes_issue_req_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"  style="width:100px;"/>
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
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
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
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond=""; $search_recipe_cond=""; $batchcond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and b.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and c.order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and b.job_no_prefix_num= '$search_str' ";
			else if ($search_by==4) $po_cond=" and c.po_number = '$search_str' ";
			else if ($search_by==5) $style_cond=" and b.style_ref_no = '$search_str' ";
			else if ($search_by==6) $batchcond=" and d.batch_no= '$search_str' ";
		}
		if($recipe_no!='') $search_recipe_cond=" and a.recipe_no_prefix_num='$recipe_no'";
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and b.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and c.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and b.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) $po_cond=" and c.po_number like '%$search_str%'"; 
			else if ($search_by==5) $style_cond=" and b.style_ref_no like '%$search_str%'";   
			else if ($search_by==6) $batchcond=" and d.batch_no like '%$search_str%' ";
		}
		if($recipe_no!='') $search_recipe_cond=" and a.recipe_no_prefix_num like '%$recipe_no%'";
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and b.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and c.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and b.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $po_cond=" and c.po_number like '$search_str%'"; 
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '$search_str%'";  
			else if ($search_by==6) $batchcond=" and d.batch_no like '$search_str%' "; 
		}
		if($recipe_no!='') $search_recipe_cond=" and a.recipe_no_prefix_num like '$recipe_no%'";
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and b.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and c.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and b.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $po_cond=" and c.po_number like '%$search_str'";  
			else if ($search_by==5) $style_cond=" and b.style_ref_no like '%$search_str'";  
			else if ($search_by==6) $batchcond=" and d.batch_no like '%$search_str' ";
		}
		if($recipe_no!='') $search_recipe_cond=" and a.recipe_no_prefix_num like '%$recipe_no'";
	}
	
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');
	$con = connect();
	if($db_type==0)	{ mysql_query("BEGIN");}
	if($recipe_id!='') $search_recipe_id=" and recipe_id=$recipe_id'";
	
	/*echo "select recipe_id as issue_id from dyes_chem_issue_requ_mst where  recipe_id is not null and status_active=1 and is_deleted=0 and company_id=$company_id and requisition_basis=8 and entry_form=299 $search_recipe_id group by recipe_id "; die;*/
	$sql_insert_id=sql_select("select recipe_id as issue_id from dyes_chem_issue_requ_mst where  recipe_id is not null and status_active=1 and is_deleted=0 and company_id=$company_id and requisition_basis=8 and entry_form=391 $search_recipe_id group by recipe_id ");
	$issue_id="";
	foreach($sql_insert_id as $iss_id)
	{
		$issue_row_id=$iss_id[csf('issue_id')];
		if($issue_row_id!=0)
		{
			$issue_id=$iss_id[csf('issue_id')];
			//echo "insert into tmp_poid (userid, poid) values ($user_id,$issue_id)"; 
			$r_id2=execute_query("insert into tmp_poid (userid, poid) values ($user_id,$issue_id)");
			if($issue_id=="") $issue_id=$iss_id[csf('issue_id')];else $issue_id.=",".$iss_id[csf('issue_id')];
		}
		
	}
	
	//echo $r_id2; die;
	if($db_type==0)
	{
		if($r_id2)
		{
			mysql_query("COMMIT");  
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		if($r_id2)
		{
			oci_commit($con);  
		}
	}
	
	//echo $issue_id.'aziz'; die;
	
	//$sql = "select a.id, a.labdip_no, a.recipe_no, a.recipe_date, a.color_id, a.job_no, b.job_no_prefix_num, c.id as order_id, c.order_no, c.buyer_po_id, d.batch_no, d.dyeing_machine, sum(e.roll_no) as roll_no,d.operation_type,b.subcon_job from pro_recipe_entry_mst a, subcon_ord_mst b, subcon_ord_dtls c ,pro_batch_create_mst d, pro_batch_create_dtls e where  b.subcon_job=c.job_no_mst and a.batch_id=d.id and d.id=e.mst_id and e.po_id=c.id and a.entry_form=300 and b.entry_form=295 and d.entry_form=316  and a.company_id='$company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_recipe_cond $search_com_cond $date_cond $po_idsCond  and a.id not in(select poid from tmp_poid where userid=$user_id)  group by a.id, a.labdip_no, a.recipe_no, a.recipe_date, a.color_id, a.job_no, b.job_no_prefix_num, c.id,c.order_no, c.buyer_po_id,d.batch_no, d.dyeing_machine,d.operation_type,b.subcon_job order by a.id Desc";
	
	$sql = "select a.id, a.labdip_no, a.recipe_no, a.recipe_date, a.color_id, a.job_no, b.job_no_prefix_num, b.buyer_name, b.style_ref_no, c.id as order_id, c.po_number, d.batch_no, d.machine_no, d.operation_type, b.job_no, p.store_id, sum(e.qty_pcs) as qty_pcs 
	from pro_recipe_entry_dtls p, pro_recipe_entry_mst a, wo_po_details_master b, wo_po_break_down c, pro_bundle_batch_mst d, pro_bundle_batch_dtls e 
	where p.MST_ID=a.id and b.job_no=c.job_no_mst and a.batch_id=d.id and d.id=e.mst_id and e.po_id=c.id and a.entry_form=390 and a.company_id='$company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $job_cond $style_cond $po_cond $search_com_cond $search_recipe_cond $batchcond 
	group by a.id, a.labdip_no, a.recipe_no, a.recipe_date, a.color_id, a.job_no, b.job_no_prefix_num, b.buyer_name, b.style_ref_no, c.id, c.po_number, d.batch_no, d.machine_no, d.operation_type, b.job_no, p.store_id 
	order by a.id Desc";
	
	//echo $sql; die;
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="970" class="rpt_table">
		<thead>
			<th width="35">SL</th>
			<th width="70">Labdip No</th>
			<th width="80">Recipe Date</th>
            <th width="80">Batch No</th>
            <th width="80">Operation</th>
			<th width="110">Job No</th>
            <th width="110">Po No</th>
			<th width="100">Style Ref.</th>
			<th>Color<input type="hidden" name="txt_recipe_row_id" id="txt_recipe_row_id" value="<?php echo $recipe_row_id; ?>"/></th>
		</thead>
	</table>
	<div style="width:970px; overflow-y:scroll; max-height:220px;" id="buyer_list_view" align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table" id="tbl_list_search">
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
                <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>" onClick="js_set_value('<?=$selectResult[csf('id')].'___'.$selectResult[csf('recipe_no')].'___'.$selectResult[csf('job_no')].'___'.$selectResult[csf('order_id')].'___'.$selectResult[csf('po_number')].'___'.$selectResult[csf('style_ref_no')].'___'.$selectResult[csf('batch_no')].'___'.$machine_arr[$selectResult[csf('machine_no')]].'___'.$selectResult[csf('qty_pcs')].'___'.$selectResult[csf('operation_type')].'___'.$wash_operation_arr[$selectResult[csf('operation_type')]].'___'.$selectResult[csf('store_id')]; ?>')">
                    <td width="35" align="center"><? echo $i; ?></td>
                    <td width="70" align="center"><? echo $selectResult[csf('labdip_no')]; ?></td>
                    <td width="80" align="center"><? echo change_date_format($selectResult[csf('recipe_date')]); ?>&nbsp;</td>
                    <td width="80"><? echo $selectResult[csf('batch_no')]; ?>&nbsp;</td>
                    <td width="80"><?=$wash_operation_arr[$selectResult[csf('operation_type')]]; ?>&nbsp;</td>
                    <td width="110"><?=$selectResult[csf('job_no')]; ?>&nbsp;</td>
                    <td width="110"><?=$selectResult[csf('po_number')]; ?>&nbsp;</td>
                    <td width="100"><?=$selectResult[csf("style_ref_no")]; ?>&nbsp;</td>
                    <td><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?>&nbsp;</p></td>
                </tr>
                <?
                $i++;
            }
            ?>
        </table>
    </div>
	<?
	$r_id3=execute_query("delete from tmp_poid where userid=$user_id");
		if($db_type==0)
		{
			if($r_id3)
			{
				mysql_query("COMMIT");  
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($r_id3)
			{
				oci_commit($con);  
			}
		}
	exit();
}

if ($action == "item_details") 
{
	$data = explode("**", $data);
	$company_id = $data[0];
	$recipe_id = $data[1];
	$mst_id = $data[2];
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1120" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="50">Prod. ID</th>
            <th width="100">Lot</th>
			<th width="110">Item Category</th>
			<th width="100">Group</th>
			<th width="100">Sub Group</th>
			<th width="200">Item Description</th>
			<th width="50">UOM</th>
			<th width="50">Seq. No.</th>
			<th style="display: none;" width="80">Ratio in %</th>
			<th width="80">Req. Qty.</th>
            <th width="60">Adj %</th>
            <th width="90">Adj type</th>
            <th>Tot. Qty.</th>
		</thead>
	</table>
	<div style="width:1120px; overflow-y:scroll; max-height:230px;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table" id="tbl_list_search" align="left">
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
			
				$item_group_arr = return_library_array("select id, item_name from lib_item_group", "id", "item_name");
                $multicolor_array = array();
                $prod_data_array = array();
                $new_prod_arr = array();
                $sql = "select sub_process_id, prod_id from pro_recipe_entry_dtls where mst_id=$recipe_id and ratio>0 and status_active=1 and is_deleted=0 order by id ASC";
                $nameArray = sql_select($sql); $tot_rows=0; $prodIds='';
                foreach ($nameArray as $row) 
                {
                    if (!in_array($row[csf("sub_process_id")], $multicolor_array)) 
                    {
                        $multicolor_array[] = $row[csf("sub_process_id")];
                    }
					$new_prod_arr[$row[csf("sub_process_id")]]=$row[csf("prod_id")];
                }
                unset($nameArray);
                
				$sql="select a.ID, a.BATCH_QTY, b.id as DTLSID, b.SUB_PROCESS_ID, b.TOTAL_LIQUOR, b.PROD_ID, b.DOSE_BASE, b.RATIO,  b.COMMENTS, b.SEQ_NO, b.ITEM_LOT 
				from PRO_RECIPE_ENTRY_MST a, PRO_RECIPE_ENTRY_DTLS b 
				where a.id=b.MST_ID and a.id='$recipe_id' and b.ratio>0 and a.status_active=1 and b.status_active=1";
	
                //$sql = "select id, sub_process_id, prod_id, comments, ratio, seq_no from pro_recipe_entry_dtls where mst_id=$recipe_id and ratio>0 and status_active=1 and is_deleted=0 order by seq_no ASC";
                $nameArray = sql_select($sql); $tot_rows=0; $prodIds='';
                foreach ($nameArray as $row) 
                {
					$recipe_qnty=0;
					if($row["DOSE_BASE"]==1)
					{
						$recipe_qnty=(($row["TOTAL_LIQUOR"]*$row["RATIO"])/1000);
					}
					else
					{
						$recipe_qnty=(($row["BATCH_QTY"]*$row["RATIO"])/100);
					}
                    $color_remark_array[$row["SUB_PROCESS_ID"]][$row["DTLSID"]]['prod_id']= $row["PROD_ID"];
                    $color_remark_array[$row["SUB_PROCESS_ID"]][$row["DTLSID"]]['comments']= $row["COMMENTS"];
                    $color_remark_array[$row["SUB_PROCESS_ID"]][$row["DTLSID"]]['ratio']=$recipe_qnty;
                    $color_remark_array[$row["SUB_PROCESS_ID"]][$row["DTLSID"]]['seq_no']= $row["SEQ_NO"];
					$color_remark_array[$row["SUB_PROCESS_ID"]][$row["DTLSID"]]['item_lot']= $row["ITEM_LOT"];
                    $tot_rows++;
                    $prodIds.=$row["PROD_ID"].",";
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
                
                $sql = "select id, item_category_id, item_group_id, sub_group_name, item_description, unit_of_measure, product_name_details from product_details_master where status_active=1 and is_deleted=0 and item_category_id in(5,6,7,23) order by id";
                
                //echo $sql;
                $sql_result = sql_select($sql);
    
                foreach ($sql_result as $row) 
                {
                    $prod_data_array[$row[csf("id")]]= $row[csf("item_category_id")] . "**" . $row[csf("item_group_id")] . "**" . $row[csf("sub_group_name")] . "**" . $row[csf("item_description")] . "**" . $row[csf("unit_of_measure")]. "**" . $row[csf("product_name_details")];
                }
                unset($sql_result);
                
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
                    <tr >
                        <td bgcolor="#CCCCFF" colspan="13" align="left"><b><? echo $k.'.  '. $wash_wet_process[$mcolor_id]; ?></b></td>
                        
                        <td bgcolor="#CCCCFF" style="display:none;"><b><? echo $product_name_details; ?><input type="hidden" name="hidd_nprod_id[]" id="hidd_nprod_id_<? echo $k; ?>" value="<? echo $new_prod_id; ?>"></b></td>
                        <td  style="display: none;" align="right" bgcolor="#CCFFFF"><b>Paste Weight</b></td>
                        <td  style="display: none;" bgcolor="#CCFFFF">&nbsp;<input type="text" name="txt_past_weight[]" id="txt_past_weight_<? echo $k; ?>" class="text_boxes_numeric" value="<? echo $past_weight; ?>" style="width:60px;"><input type="hidden" name="multicolor_id[]" id="multicolor_id_<? echo $k; ?>" value="<? echo $mcolor_id; ?>"></td>
                    </tr>
                    <? 
                    foreach ($color_remark_array[$mcolor_id] as $rid=>$exdata) 
                    {
                        if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                        $prod_id=0; $exprod_data=""; $item_category_id=0; $item_group_id=0; $sub_group_name=""; $item_description=""; $unit_of_measure=0;
                        $prod_id=$exdata['prod_id'];
                        $ratio=$exdata['ratio'];
                        $exprod_data=explode("**",$prod_data_array[$prod_id]);
                        
                        $item_category_id=$exprod_data[0]; $item_group_id=$exprod_data[1]; $sub_group_name=$exprod_data[2]; 
                        $item_description=$exprod_data[3]; $unit_of_measure=$exprod_data[4];
						$reqsnqty=$reqsn_data_arr[$mcolor_id][$prod_id]['qty'];
						$dtls_id=$reqsn_data_arr[$mcolor_id][$prod_id]['id'];
						$adjust_percent=$reqsn_data_arr[$mcolor_id][$prod_id]['adjust_percent'];
						$adjust_type=$reqsn_data_arr[$mcolor_id][$prod_id]['adjust_type'];
						$req_qny_edit=$reqsn_data_arr[$mcolor_id][$prod_id]['req_qny_edit'];
                        
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>">
                            <td width="30" align="center" id="sl_<? echo $k.'_'.$i; ?>"><? echo $i.''; ?></td>
                            <td width="50" align="center" id="product_id_<? echo $k.'_'.$i; ?>"><? echo $prod_id; ?></td>
                            <td width="100" id="lot_<? echo $k.'_'.$i; ?>" align="center" style="word-break:break-all"><p><? echo $exdata['item_lot']; ?></p></td>
                            <td width="110" style="word-break:break-all;"><? echo $item_category[$item_category_id]; ?>&nbsp;<input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $k.'_'.$i; ?>" value="<? echo $item_category_id; ?>"></td>
                            <td width="100" id="item_group_id_<? echo $k.'_'.$i; ?>" style="word-break:break-all;"><? echo $item_group_arr[$item_group_id]; ?>&nbsp;<input type="hidden" name="txt_group_id[]" id="txt_group_id_<? echo $k.'_'.$i; ?>" value="<? echo $item_group_id; ?>"></td>
                            <td width="100" id="sub_group_name_<? echo $k.'_'.$i; ?>" style="word-break:break-all;"><? echo $sub_group_name; ?>&nbsp;</td>
                            <td width="200" id="item_description_<? echo $k.'_'.$i; ?>" style="word-break:break-all;"><? echo $item_description; ?>&nbsp;</td>
                            <td width="50" align="center" id="uom_<? echo $k.'_'.$i; ?>" style="word-break:break-all;"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
                            <td width="50" align="center" id="seq_no_<? echo $k.'_'.$i; ?>" style="word-break:break-all;"><? echo $exdata['seq_no']; ?></td>
                            <td  style="display: none;" width="80" align="right" id="ratio_<? echo $k.'_'.$i; ?>"><? echo number_format($exdata['ratio'], 6, '.', ''); ?></td>
                            <td width="80" align="center" id="reqn_qnty_<? echo $k.'_'.$i; ?>">
								<input type="text" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $k.'_'.$i; ?>" class="text_boxes_numeric" value="<? if($reqsnqty!="") echo number_format($reqsnqty, 4, '.', ''); else echo number_format($ratio, 4, '.', '');?>" style="width:60px" onBlur="calculate_requs_qty('<? echo $k.'_'.$i; ?>')">
								<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $k.'_'.$i; ?>" value="<? echo $dtls_id; ?>"></td>
                            <td width="60" align="right" id="adj_per_<? echo $k.'_'.$i; ?>">
							<input type="text" name="txt_adj_per[]" id="txt_adj_per_<? echo $k.'_'.$i; ?>" class="text_boxes_numeric" style="width:40px" value="<? if($adjust_percent!="") echo number_format($adjust_percent, 0); else echo "";?>" onBlur="calculate_requs_qty('<? echo $k.'_'.$i; ?>')">
                            </td>
                            <td width="90" align="right" id="adj_type_<? echo $k.'_'.$i; ?>"><? echo create_drop_down("cbo_adj_type_".$k.'_'.$i, 80, $increase_decrease, "", 1, "- Select -", $adjust_type, "calculate_requs_qty('".$k.'_'.$i."')"); ?></td>
                            <td align="right" id="tot_qnty_<? echo $k.'_'.$i; ?>"><input type="text" name="txt_tot_qnty[]" id="txt_tot_qnty_<? echo $k.'_'.$i; ?>" class="text_boxes_numeric" value="<? if($req_qny_edit!="") echo number_format($req_qny_edit, 4, '.', ''); else echo number_format($ratio, 4, '.', '');?>" style="width:60px" readonly ></td>
                        </tr>
                        <?
                        if($req_qny_edit!="") 
                        {
                        	$tot_ratio+=$req_qny_edit;
                        	$grand_tot_ratio+=$req_qny_edit;
                        }
                        else
                        {
                        	$tot_ratio+=$exdata['ratio'];
                        	$grand_tot_ratio+=$exdata['ratio'];
                        }
                        $i++;
                    }
                    ?>
                    <tr class="tbl_bottom">
                        <td align="right" colspan="8"><strong>Color Total</strong>
                        	<input type="hidden" name="hidd_colorrow<? echo $k; ?>" id="hidd_colorrow<? echo $k; ?>" value="<? echo $i; ?>"></td>
                        <td align="right" id="ratiotot_<? echo $k; ?>">&nbsp;</td>
                        <td align="right" id="color_reqsnqty_<? echo $k; ?>">&nbsp;</td>
                        <td align="right" id="color_adj_per_<? echo $k; ?>">&nbsp;</td>
                        <td align="right" id="color_adj_type_<? echo $k; ?>">&nbsp;</td>
                        <td align="right" id="color_tot_qnty_<? echo $k; ?>"><? echo number_format($tot_ratio, 6, '.', ''); ?></td>
                    </tr>
                    <?
					$k++;
                }
                ?>
                <tr class="tbl_bottom">
                    <td align="right" colspan="8"><strong>Grand Total</strong>
                    	<input type="hidden" name="hidd_totcolor" id="hidd_totcolor" value="<? echo $k; ?>"></td>
                    <td align="right" id="td_ratiotot">&nbsp;</td>
                    <td align="right" id="td_reqsnqty">&nbsp;</td>
                    <td align="right" id="td_adj_per">&nbsp;</td>
                    <td align="right">&nbsp;</td>
                    <td align="right" id="td_tot_qnty"><? echo number_format($grand_tot_ratio, 6, '.', ''); ?></td>
                </tr>
             </tbody>
		</table>
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

			$new_requ_no = explode("*", return_mrr_number(str_replace("'", "", $cbo_company_name), '', 'SDCR', date("Y", time()), 5, "select requ_no_prefix, requ_prefix_num from dyes_chem_issue_requ_mst where company_id=$cbo_company_name and entry_form=391 and $year_cond=" . date('Y', time()) . " order by id desc ", "requ_no_prefix", "requ_prefix_num"));
			$id = return_next_id("id", "dyes_chem_issue_requ_mst", 1);
			$field_array = "id, requ_no, requ_no_prefix, requ_prefix_num, company_id, location_id, requisition_date, requisition_basis, recipe_id, order_id, job_no, operation_type, inserted_by, insert_date, entry_form, store_id";
			$data_array = "(" . $id . ",'" . $new_requ_no[0] . "','" . $new_requ_no[1] . "'," . $new_requ_no[2] . "," . $cbo_company_name . "," . $cbo_location_name . "," . $txt_requisition_date . "," . $cbo_receive_basis . "," . $txt_recipe_id . "," . $txt_order_id . "," . $txt_job_no . "," . $hidden_operation_id . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',391,".$cbo_store_name.")";

			$mst_id = $id;
			$requ_no = $new_requ_no[0];
		} 
		else 
		{
			$field_array_up = "location_id*requisition_date*requisition_basis*recipe_id*order_id*job_no*operation_type*updated_by*update_date";
			$data_array_up = "" . $cbo_location_name . "*" . $txt_requisition_date . "*" . $cbo_receive_basis . "*" . $txt_recipe_id . "*" . $txt_order_id . "*" . $txt_job_no . "*" . $hidden_operation_id . "*" . $user_id . "*'" . $pc_date_time . "'";
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
				$txt_item_cat = "txt_item_cat_" . $k;
				$txt_ratio = "ratio_" . $k;
				$txt_reqn_qnty = "txt_reqn_qnty_" . $k;
				
				$txt_adj_per = "txt_adj_per_" . $k;
				$cbo_adj_type = "cbo_adj_type_" . $k;
				$txt_tot_qnty = "txt_tot_qnty_" . $k;
				
				$updateIdDtls = "updateIdDtls_" . $k;
				$txt_seq_no = "seq_no_" . $k;
				$txt_lot = "txt_lot_" . $k;
				
				$txt_ratio = str_replace("'", "", $$txt_ratio);
	
				if ($data_array_dtls != "") $data_array_dtls .= ",";
				$data_array_dtls .= "(".$id_dtls.",".$mst_id.",'".$requ_no."',".$txt_recipe_id.",".$$multicolor_id.",".$$hidd_nprod_id.",".$$txt_past_weight.",".$$txt_prod_id.",".$$txt_item_cat.",'".trim($txt_ratio)."',".$$txt_reqn_qnty.",".$$txt_adj_per.",".$$cbo_adj_type.",".$$txt_tot_qnty.",".$$txt_seq_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_store_name.",".$$txt_lot.")";
				$id_dtls = $id_dtls + 1;
				$nprod_qty_arr[str_replace("'", "", $$hidd_nprod_id)]+=str_replace("'", "", $$txt_reqn_qnty);
			}
		}
		//echo "10**";
		//print_r($data_array_dtls); die;
		$flag=1;
		if (str_replace("'", "", $update_id) == "") 
		{
			//echo  "INSERT INTO dyes_chem_issue_requ_mst (".$field_array.") VALUES ".$data_array."";
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
		//echo "10**".$rID ."&&". $rID_att ."&&". $rID_dtls;die;
		//check_table_status( $_SESSION['menu_id'],0);
		if ($flag==1) 
		{
			if( $nprod_id_all !="")
			{
				$sql_prod="select id, current_stock from product_details_master where company_id=$cbo_company_name and item_category_id in(5,6,7) and status_active=1 and is_deleted=0";
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
		//echo "10**".$rID.'='.$rID_att.'='.$rID_dtls; oci_rollback($con);disconnect($con);die;
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

		$last_update_arr = return_library_array("select recipe_id, is_apply_last_update from dyes_chem_requ_recipe_att where mst_id=$update_id", "recipe_id", "is_apply_last_update");
		$is_apply_last_update = str_replace("'", "", $is_apply_last_update);

		if ($is_apply_last_update == 1) 
		{
			$field_array_up = "location_id*requisition_date*requisition_basis*recipe_id*is_apply_last_update*order_id*job_no*operation_type*updated_by*update_date";
			$data_array_up = "" . $cbo_location_name . "*" . $txt_requisition_date . "*" . $cbo_receive_basis . "*" . $txt_recipe_id . "*0*" . $txt_order_id . "*" . $txt_job_no. "*" . $hidden_operation_id . "*" . $user_id . "*'" . $pc_date_time . "'";
		} 
		else 
		{
			$field_array_up = "location_id*requisition_date*requisition_basis*recipe_id*order_id*job_no*operation_type*updated_by*update_date";
			$data_array_up = "" . $cbo_location_name . "*" . $txt_requisition_date . "*" . $cbo_receive_basis . "*" . $txt_recipe_id . "*" . $txt_order_id . "*" . $txt_job_no . "*" . $hidden_operation_id . "*" . $user_id . "*'" . $pc_date_time . "'";
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
		$id_dtls = return_next_id("id", "dyes_chem_issue_requ_dtls", 1);
		$field_array_dtls = "id, mst_id, requ_no, recipe_id, multicolor_id, nprod_id, past_weight, product_id, item_category, ratio, required_qnty, adjust_percent, adjust_type, req_qny_edit, seq_no, inserted_by, insert_date, store_id, item_lot";
		$k=0;
		$nprod_id_all=""; $nprod_qty_arr=array();
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
				$txt_item_cat = "txt_item_cat_" . $k;
				$txt_ratio = "ratio_" . $k;
				$txt_reqn_qnty = "txt_reqn_qnty_" . $k;
				
				$txt_adj_per = "txt_adj_per_" . $k;
				$cbo_adj_type = "cbo_adj_type_" . $k;
				$txt_tot_qnty = "txt_tot_qnty_" . $k;
				
				$updateIdDtls = "updateIdDtls_" . $k;
				$txt_seq_no = "seq_no_" . $k;
				$txt_lot = "txt_lot_" . $k;
				$txt_ratio = str_replace("'", "", $$txt_ratio);
	
				if ($data_array_dtls != "") $data_array_dtls .= ",";
				$data_array_dtls .= "(".$id_dtls.",".$mst_id.",'".$requ_no."',".$txt_recipe_id.",".$$multicolor_id.",".$$hidd_nprod_id.",".$$txt_past_weight.",".$$txt_prod_id.",".$$txt_item_cat.",'".trim($txt_ratio)."',".$$txt_reqn_qnty.",".$$txt_adj_per.",".$$cbo_adj_type.",".$$txt_tot_qnty.",".$$txt_seq_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_store_name.",".$$txt_lot.")";
				$id_dtls = $id_dtls + 1;
				$nprod_qty_arr[str_replace("'", "", $$hidd_nprod_id)]+=str_replace("'", "", $$txt_reqn_qnty);
				//echo $colorrow.'='; 
			}
		}
		$flag=1;
		$rID = sql_update("dyes_chem_issue_requ_mst", $field_array_up, $data_array_up, "id", $update_id, 0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		$delete_att = execute_query("delete from dyes_chem_requ_recipe_att where mst_id=$update_id", 0);
		if($delete_att==1 && $flag==1) $flag=1; else $flag=0;
		
		$delete_dtls = execute_query("delete from dyes_chem_issue_requ_dtls where mst_id=$update_id", 0);
		
		
		if($delete_dtls==1 && $flag==1) $flag=1; else $flag=0;
		//echo "10**".$delete_dtls."==".$flag; die;
		$rID_att = sql_insert("dyes_chem_requ_recipe_att", $field_array_att, $data_array_att, 0);
		if($rID_att==1 && $flag==1) $flag=1; else $flag=0;

		if ($data_array_dtls != "") 
		{
			//echo "10**INSERT INTO dyes_chem_issue_requ_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls."";
			$rID_dtls = sql_insert("dyes_chem_issue_requ_dtls", $field_array_dtls, $data_array_dtls, 0);
			if($rID_dtls==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		if ($flag==1)
		{
			if( $nprod_id_all !="")
			{
				$sql_prod="select id, current_stock from  product_details_master where company_id=$cbo_company_name and item_category_id in(5,6,7) and status_active=1 and is_deleted=0";
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
		
		$mrrsql= sql_select("select  issue_number, req_no, req_id  from  inv_issue_master where req_id=$updateid and  entry_form = 298 and  status_active=1 and  is_deleted=0");
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
					echo "50**Delete restricted, This Information is used in another Table."."  Issue Number ".$do_rcv_number=str_replace("'","",$all_req_no)."  Issue Number ".$do_rcv_number=str_replace("'","",$all_issue_id); 
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
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	$imge_arr=return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	
	/*$order_arr = array();
	$embl_sql ="Select a.subcon_job, b.id, b.order_no from subcon_ord_mst a, subcon_ord_dtls b where a.entry_form=295 and a.subcon_job=b.job_no_mst";
	$embl_sql_res=sql_select($embl_sql);
	foreach ($embl_sql_res as $row)
	{
		$order_arr[$row[csf("id")]]['job']=$row[csf("subcon_job")];
		$order_arr[$row[csf("id")]]['po']=$row[csf("order_no")];
	}
	unset($embl_sql_res);*/
	
	$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');
	
	//$recipe_sql = "select a.id,a.recipe_no, a.within_group, a.buyer_id, a.color_id, b.subcon_job, c.id as order_id, c.order_no, c.buyer_po_id, d.batch_no, d.dyeing_machine, sum(e.roll_no) as roll_no from pro_recipe_entry_mst a, subcon_ord_mst b, subcon_ord_dtls c ,pro_batch_create_mst d, pro_batch_create_dtls e where  b.subcon_job=c.job_no_mst and a.batch_id=d.id and d.id=e.mst_id and e.po_id=c.id and a.entry_form=300 and b.entry_form=295 and d.entry_form=316  and a.company_id=$data[0] and a.id=$data[2] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by  a.id, a.recipe_no , a.within_group, a.buyer_id, a.color_id, b.subcon_job, c.id, c.order_no, c.buyer_po_id, d.batch_no, d.dyeing_machine order by a.id Desc";
	$recipe_sql = "select a.id, a.labdip_no, a.recipe_no, a.recipe_date, a.color_id, a.job_no, b.job_no_prefix_num, b.buyer_name, b.style_ref_no, c.id as order_id, c.po_number, d.batch_no, d.machine_no, d.operation_type, b.job_no, sum(e.qty_pcs) as qty_pcs from pro_recipe_entry_mst a, wo_po_details_master b, wo_po_break_down c, pro_bundle_batch_mst d, pro_bundle_batch_dtls e where b.job_no=c.job_no_mst and a.batch_id=d.id and d.id=e.mst_id and e.po_id=c.id and a.entry_form=390 and a.company_id='$data[0]' and a.id=$data[2] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.labdip_no, a.recipe_no, a.recipe_date, a.color_id, a.job_no, b.job_no_prefix_num, b.buyer_name, b.style_ref_no, c.id, c.po_number, d.batch_no, d.machine_no, d.operation_type, b.job_no order by a.id Desc";
	$recipe_sql_res=sql_select($recipe_sql);
	foreach ($recipe_sql_res as $row)
	{
		$recipe_arr[$row[csf("id")]]['job'] 	 	=$row[csf("subcon_job")];
		$recipe_arr[$row[csf("id")]]['po'] 		 	=$row[csf("order_no")];
		$recipe_arr[$row[csf("id")]]['recipe_no'] 	=$row[csf("recipe_no")];
		$recipe_arr[$row[csf("id")]]['batch_no'] 	=$row[csf("batch_no")];
		$recipe_arr[$row[csf("id")]]['machine_no'] 	=$machine_arr[$row[csf("machine_no")]];
		$recipe_arr[$row[csf("id")]]['roll_no'] 	=$row[csf("qty_pcs")];
		$recipe_arr[$row[csf("id")]]['within_group'] 	=$row[csf("within_group")];
		$recipe_arr[$row[csf("id")]]['buyer_id'] 	=$row[csf("buyer_id")];
		$recipe_arr[$row[csf("id")]]['color_id'] 	=$row[csf("color_id")];
	}
	unset($recipe_sql_res);

	$sql_mst = "select id, requ_no, requ_no_prefix, requ_prefix_num, company_id, location_id, requisition_date, requisition_basis, recipe_id, order_id, job_no, entry_form,operation_type from dyes_chem_issue_requ_mst where id='$data[1]'";
	//echo $sql_mst;
	$dataArray = sql_select($sql_mst); $party_name="";
	$recipe_id=$dataArray[0][csf('recipe_id')];
	
	//$recipe_arr=array();
	if($recipe_id!="")
	{
		$recipe_sql=sql_select("SELECT id, within_group, buyer_id, color_id, remarks, recipe_no from pro_recipe_entry_mst where entry_form=390 and id='$recipe_id' and status_active=1 and is_deleted=0");
		foreach ($recipe_sql as $row) 
		{
			$party_name='';
			/*if($row[csf('within_group')]==1) $party_name=$company_library[$row[csf('buyer_id')]];
			else if($row[csf('within_group')]==2) $party_name=$buyer_library[$row[csf('buyer_id')]];*/
			$remarks=$row[csf('remarks')];
			$recipe_no=$row[csf('recipe_no')];
			$party_name=$company_library[$row[csf('buyer_id')]];
			$recipe_arr[$row[csf('id')]]['party']=$party_name; 
			$recipe_arr[$row[csf('id')]]['color']=$row[csf('color_id')]; 
		}
		unset($recipe_sql);
	}
	if($recipe_arr[$recipe_id]['within_group']==1) $party_name=$company_library[$recipe_arr[$recipe_id]['buyer_id']];
	else if($recipe_arr[$recipe_id]['within_group']==2) $party_name=$buyer_library[$recipe_arr[$recipe_id]['buyer_id']];
	
	?>
    <div style="width:1030px; font-size:6px">
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
                            <td  align="center" style="font-size:14px"> <? echo show_company($data[0],'',''); ?></td>  
                        </tr>
                        <tr>
                            <td align="center" style="font-size:18px"><strong><? echo $data[3]; ?></strong></td>
                        </tr>
                    </table>
                </td>
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
                <td><? echo $party_name; ?></td>
                <td><strong>Job No:</strong></td>
                <td> <? echo $dataArray[0][csf('job_no')]; ?></td>
                <td><strong>Color:</strong></td>
                <td><? echo $color_arr[$recipe_arr[$recipe_id]['color_id']]; ?></td>
            </tr>
            <tr>
                <td><strong>Batch No:</strong></td>
                <td><? echo $recipe_arr[$recipe_id]['batch_no']; ?></td>
                <td><strong>Machine No:</strong></td>
                <td> <? echo $recipe_arr[$recipe_id]['machine_no']; ?></td>
                <td><strong>Gmts Qty. (Pcs):</strong></td>
                <td><? echo $recipe_arr[$recipe_id]['roll_no']; ?></td>
            </tr>
            <tr>
                <td><strong>Recipe No :</strong></td>
                <td><?=$recipe_no; ?></td>
                <td><strong>Order No:</strong></td>
                <td><? echo $buyer_po_arr[$dataArray[0][csf('order_id')]]['po']; ?></td>
                <td><strong>Style Ref.:</strong></td>
                <td><? echo $buyer_po_arr[$dataArray[0][csf('order_id')]]['style']; ?></td>
            </tr>
             <tr>
                <td><strong>Operation:</strong></td>
                <td><? echo $wash_operation_arr[$dataArray[0][csf('operation_type')]]; ?></td>
                <td><strong>Remarks:</strong></td>
                <td><?=$remarks; ?></td>
            </tr>
        </table>
        <br>
        <div style="width:100%;">
            <table align="right" cellspacing="0" width="1030" border="1" rules="all" class="rpt_table" style="font-size:14px">
                <thead bgcolor="#dddddd" align="center"><!-- -->
                    <th width="30">SL</th>
                    <th width="100">Item Cat.</th>
                    <th width="100">Item Group</th>
                    <th width="80">Sub Group</th>
                    <th width="150">Item Description</th>
                    <th width="50">UOM</th>
                    <th width="50">Seq. No.</th>
                    <th width="60">Dosage %</th>
                    <th width="60">Req. Qty.</th>
                    <th width="60">Adj. %</th>
                    <th width="50">Adj. Type</th>
                    <th width="60">Tot. Qty.</th>
                    <th>Comments</th>
                </thead>
				<?
				$mst_id = $data[1];
				$com_id = $data[0];
				$pastweight_arr=array();
				$reqsn_data_arr=array();
				$reqsn_sql="select id, multicolor_id, past_weight, product_id, required_qnty, adjust_percent, adjust_type, req_qny_edit from dyes_chem_issue_requ_dtls where mst_id='$mst_id' and status_active=1 and is_deleted=0";
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
				$sql = "select sub_process_id from pro_recipe_entry_dtls where mst_id=$recipe_id and status_active=1 and ratio>0 and is_deleted=0 order by id ASC";
				$nameArray = sql_select($sql); $tot_rows=0; $prodIds='';
				foreach ($nameArray as $row) 
				{
					if (!in_array($row[csf("sub_process_id")], $multicolor_array)) 
					{
						$multicolor_array[] = $row[csf("sub_process_id")];
					}
				}
				unset($nameArray);
				
				$sql = "select id, sub_process_id, color_id, prod_id, comments, ratio, seq_no, total_liquor, recipe_time, recipe_temperature, recipe_ph, liquor_ratio from pro_recipe_entry_dtls where mst_id=$recipe_id and status_active=1 and ratio>0  and is_deleted=0 order by seq_no ASC";
				$nameArray = sql_select($sql); $tot_rows=0; $prodIds='';
				foreach ($nameArray as $row) 
				{
					$color_remark_array[$row[csf("sub_process_id")]][$row[csf("id")]]['prod_id']= $row[csf("prod_id")];
					$color_remark_array[$row[csf("sub_process_id")]][$row[csf("id")]]['comments']= $row[csf("comments")];
					$color_remark_array[$row[csf("sub_process_id")]][$row[csf("id")]]['ratio']= $row[csf("ratio")];
					$color_remark_array[$row[csf("sub_process_id")]][$row[csf("id")]]['seq_no']= $row[csf("seq_no")];
					
					$sub_process_remark_array[$row[csf("sub_process_id")]]['total_liquor'] = $row[csf("total_liquor")];
					$sub_process_remark_array[$row[csf("sub_process_id")]]['recipe_time'] 			= $row[csf("recipe_time")];
					$sub_process_remark_array[$row[csf("sub_process_id")]]['recipe_temperature'] 	= $row[csf("recipe_temperature")];
					$sub_process_remark_array[$row[csf("sub_process_id")]]['recipe_ph'] 			= $row[csf("recipe_ph")];
					$sub_process_remark_array[$row[csf("sub_process_id")]]['liquor_ratio'] 			= $row[csf("liquor_ratio")];
					
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
				
				$sql = "select id, item_category_id, item_group_id, sub_group_name, item_description, unit_of_measure from product_details_master where status_active=1 and is_deleted=0 and company_id='$com_id' $prodIds_cond and item_category_id in(5,6,7,23) order by id";
				
				//echo $sql;
				$sql_result = sql_select($sql);

				foreach ($sql_result as $row) 
				{
					$prod_data_array[$row[csf("id")]]= $row[csf("item_category_id")] . "**" . $row[csf("item_group_id")] . "**" . $row[csf("sub_group_name")] . "**" . $row[csf("item_description")] . "**" . $row[csf("unit_of_measure")];
				}
				unset($sql_result);
				
				$k=1; $grand_tot_req=0;

				foreach ($multicolor_array as $mcolor_id) 
				{
					$i=1; $tot_req=$tot_edit_req=0;
					$liquor_ratio = $sub_process_remark_array[$mcolor_id]['total_liquor'];
					?>
                    <tr bgcolor="#EEEFF0">
                    	<td colspan="6" align="left" style="border-right: none;"><b>Wash Type:- <?=$wash_wet_process[$mcolor_id] . ', Total Liquor (ltr):-'.$liquor_ratio; ?>,</b></td>
                        <td colspan="7" style="border-left: none;">
                            &nbsp;&nbsp;&nbsp;<strong>Dosage: </strong><?=$sub_process_remark_array[$mcolor_id]['liquor_ratio']; ?>, 
                            &nbsp;&nbsp;&nbsp;<strong>Time:</strong><?=$sub_process_remark_array[$mcolor_id]['recipe_time']; ?> min,
                            &nbsp;&nbsp;&nbsp;<strong>Temp:</strong><?=$sub_process_remark_array[$mcolor_id]['recipe_temperature']; ?> &#8451; ,
                            &nbsp;&nbsp;&nbsp;<strong>PH:</strong><?=$sub_process_remark_array[$mcolor_id]['recipe_ph']; ?>
                        </td>
                    </tr>
					<?
					foreach ($color_remark_array[$mcolor_id] as $rid=>$exdata) 
					{
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						$prod_id=0; $exprod_data=""; $item_category_id=0; $item_group_id=0; $sub_group_name=""; $item_description=""; $unit_of_measure=0; $reqsn_qty=0;
						$prod_id=$exdata['prod_id'];
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
                            <td><p><? echo $sub_group_name; ?>&nbsp;</p></td>
                            <td><p><? echo $item_description; ?>&nbsp;</p></td>
                            <td align="center"><p><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</p></td>
                            <td align="center"><? echo $exdata['seq_no']; ?></td>
                            <td align="right"><? echo number_format($exdata['ratio'], 4, '.', ''); ?></td>
                            <td align="right"><? echo number_format($reqsn_qty, 4, '.', ''); ?></td>
                            <td align="right"><? echo number_format($adjust_percent, 4, '.', ''); ?></td>
                            <td><p><? echo $increase_decrease[$adjust_type]; ?>&nbsp;</p></td>
                            <td align="right"><? echo number_format($req_qny_edit, 4, '.', ''); ?></td>
                            <td><p><? echo $exdata['comments']; ?>&nbsp;</p></td>
                        </tr>
						<?
						$tot_req+=$reqsn_qty;
						$tot_edit_req+=$req_qny_edit;
						$grand_tot_req+=$reqsn_qty;
						$grand_tot_edit_req+=$req_qny_edit;
						$i++;
					}
					$k++;
					?>
                    <tr class="tbl_bottom">
                        <td align="right" colspan="7"><strong>Wash Type <i>[<?=$wash_wet_process[$mcolor_id]; ?>]</i> Total:</strong></td>
                        <td>&nbsp;</td>
                        <td align="right"><? echo number_format($tot_req, 4, '.', ''); ?>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right"><? echo number_format($tot_edit_req, 4, '.', ''); ?>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
					<?
				}
				?>
                <tr class="tbl_bottom">
                    <td align="right" colspan="7"><strong>Grand Total:</strong></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($grand_tot_req, 4, '.', ''); ?>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($grand_tot_edit_req, 4, '.', ''); ?>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
            <br>
			<?=signature_table(196, $com_id, "1030px"); ?>
        </div>
    </div>
	<?
	exit();
}

if ($action == "chemical_dyes_issue_requisition_printb1") 
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$party_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');
	//$po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
	$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	
	$sql_mst = "select id, labdip_no, company_id, working_company_id, location_id, recipe_description, batch_id, method, recipe_date, within_group, style_or_order, booking_id, total_liquor, batch_ratio, liquor_ratio, remarks, buyer_id, color_id, color_range, store_id, remarks from pro_recipe_entry_mst where id='$data[2]'";
	//echo $sql_mst;
	$dataArray = sql_select($sql_mst);
	
	$batch_id=$dataArray[0][csf('batch_id')];

	$cust_arr=array();
	$cust_buyer_style_array=sql_select("SELECT a.buyer_name, a.style_ref_no, a.style_description, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach ($cust_buyer_style_array as $cust_val) 
	{
		$cust_arr[$cust_val[csf('id')]]['cust_buyer']		=$buyer_library[$cust_val[csf('buyer_name')]]; 
		$cust_arr[$cust_val[csf('id')]]['cust_style_ref']	=$cust_val[csf('style_ref_no')]; 
		$cust_arr[$cust_val[csf('id')]]['style_des']	=$cust_val[csf('style_description')]; 
		$po_arr[$cust_val[csf('id')]]=$cust_val[csf('po_number')];
	}

	$batch_array = array();
	
	if ($db_type == 0) 
	{
		$sql = "select a.id, a.batch_no, a.extention_no, a.batch_against, a.batch_weight, a.operation_type, a.batch_date, a.color_id, a.entry_form, a.process_id, a.machine_no, group_concat(b.po_id) as po_id, group_concat(b.gmtsitemid) as prod_id, sum(b.qty_pcs) as qty_pcs from pro_bundle_batch_mst a, pro_bundle_batch_dtls b where a.id=b.mst_id and a.working_company='$data[0]' and a.id='$batch_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.operation_type, a.extention_no order by a.id DESC";
	} 
	else if ($db_type == 2) 
	{
		$sql = "select a.id, a.batch_no, a.batch_against, a.extention_no, a.batch_weight, a.operation_type, a.batch_date, a.color_id, a.entry_form, a.process_id, a.machine_no, listagg(b.po_id,',') within group (order by b.po_id) as po_id, listagg(b.gmtsitemid,',') within group (order by b.gmtsitemid) as prod_id, sum(b.qty_pcs) as qty_pcs from pro_bundle_batch_mst a, pro_bundle_batch_dtls b where a.id=b.mst_id and a.working_company='$data[0]' and a.id='$batch_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.batch_against, a.extention_no, a.batch_weight, a.operation_type, a.batch_date, a.color_id, a.entry_form, a.process_id, a.machine_no order by a.id DESC";
	}
	
	//echo $sql;
	$result_sql = sql_select($sql);
	foreach ($result_sql as $row) 
	{
		$order_no = ''; $cust_buyers=""; $cust_style_ref=""; $style_desc="";
		$order_id = array_unique(explode(",", $row[csf("po_id")]));
		if ($row[csf("entry_form")] == 36 || $row[csf("entry_form")] == 150) 
		{
			$batch_type = "<b> SUBCONTRACT ORDER </b>";
			foreach ($order_id as $val) 
			{
				if ($order_no == "") $order_no = $order_array[$val]; else $order_no .= ", " . $order_array[$val];

				if ($cust_buyers=="") $cust_buyers =$cust_arr[$val]['cust_buyer']; else $cust_buyers .=", ".$cust_arr[$val]['cust_buyer'];
				if ($cust_style_ref=="") $cust_style_ref =$cust_arr[$val]['cust_style_ref']; else $cust_style_ref .=", ".$cust_arr[$val]['cust_style_ref'];
			}
		} 
		else 
		{
			$batch_type = "<b> SELF ORDER </b>";
			foreach ($order_id as $val) 
			{
				if ($order_no == "") $order_no = $po_arr[$val]; else $order_no .= ", " . $po_arr[$val];
				if ($cust_buyers=="") $cust_buyers =$cust_arr[$val]['cust_buyer']; else $cust_buyers .=", ".$cust_arr[$val]['cust_buyer'];
				if ($cust_style_ref=="") $cust_style_ref =$cust_arr[$val]['cust_style_ref']; else $cust_style_ref .=", ".$cust_arr[$val]['cust_style_ref'];
				if ($style_desc=="") $style_desc =$cust_arr[$val]['style_des']; else $style_desc .=", ".$cust_arr[$val]['style_des'];
			}
		}
		$batch_array[$row[csf("id")]]['batch_no'] = $row[csf("batch_no")];
		//$batch_array[$row[csf("id")]]['batch_against'] = $row[csf("batch_against")];
		//$batch_array[$row[csf("id")]]['total_trims_weight'] = $row[csf("total_trims_weight")];
		$batch_array[$row[csf("id")]]['batch_qty'] = $row[csf("batch_weight")]; //batch weight
		$batch_array[$row[csf("id")]]['qty_pcs'] = $row[csf("qty_pcs")]; //batch weight
		$batch_array[$row[csf("id")]]['order'] = $order_no;
		$batch_array[$row[csf("id")]]['cust_buyer'] = $cust_buyers;
		$batch_array[$row[csf("id")]]['cust_style_ref'] = $cust_style_ref;
		$batch_array[$row[csf("id")]]['style_des'] = $style_desc;
		//$batch_array[$row[csf("id")]]['operation_type'] = $row[csf("operation_type")];
	}
	
	$sqlDtls="select sum(total_liquor) as total_liquor from pro_recipe_entry_dtls where mst_id='$data[2]' and status_active=1 and is_deleted=0";
	$dataArrdtls = sql_select($sqlDtls);
	
	$sql_reqmst = "select id, requ_no, requ_no_prefix, requ_prefix_num, company_id, location_id, requisition_date, requisition_basis, recipe_id, order_id, job_no, entry_form,operation_type from dyes_chem_issue_requ_mst where id='$data[1]'";
	//echo $sql_mst;
	$dataReqArray = sql_select($sql_reqmst); $party_name="";
	$recipe_id=$dataReqArray[0][csf('recipe_id')];
	
	?>
    <div style="width:930px;">
        <table width="930" cellspacing="0" align="right" border="0">
            <tr>
                <td width="130" align="right" rowspan="3"> 
                    <img src='../../../<? echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='100' width='120' />
                </td>
                <td colspan="5" align="center" style="font-size:x-large"><strong><?=$company_library[$data[0]]; ?></strong></td>
            </tr>
            <tr>
                <td colspan="6" align="center">
					<?
					$nameArray = sql_select("select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$data[0]");
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
                <td colspan="6" align="center" style="font-size:20px"><u><strong><?=$data[4]; ?></strong></u></td>
            </tr>
         </table> 
         <br>&nbsp; 
         <table align="right" cellspacing="0" width="930" border="1" rules="all" class="rpt_table"> 
         	<tr>
                <td colspan="6" align="center"><strong><?=$dataArray[0][csf('recipe_description')]; ?></strong></td>
            </tr>
            <tr>
                <td width="130"><strong>Customer:</strong></td>
                <td width="175"><?=$batch_array[$dataArray[0][csf('batch_id')]]['cust_buyer']; ?></td>
                <td width="130"><strong>Style: </strong></td>
                <td width="175px"><?=$batch_array[$dataArray[0][csf('batch_id')]]['cust_style_ref']; ?></td>
                <td width="130"><strong>Batch No.:</strong></td>
                <td width="175"><?=$batch_array[$dataArray[0][csf('batch_id')]]['batch_no']; ?></td>
            </tr>
            <tr>
                <td><strong>Machine No:</strong></td>
                <td><?=$machine_arr[$dataArray[0][csf("machine_no")]]; ?></td>
                <td><strong>Color:</strong></td>
                <td><?=$color_arr[$dataArray[0][csf('color_id')]]; ?></td>
                <td><strong>Recipe No.:</strong></td>
                <td><?=$dataArray[0][csf('id')]; ?></td>
            </tr>
            <tr>
                <td><strong>Weight (Kg):</strong></td>
                <td><?=number_format(($batch_array[$dataArray[0][csf('batch_id')]]['batch_qty']/1000), 6, '.', ''); ?></td>
                <td><strong>Liquor (Ltr):</strong></td>
                <td><?=$dataArrdtls[0][csf('total_liquor')]; ?></td>
                <td><strong>Liquor Ratio:</strong></td>
                <td><? $liqureRatio=(1+$dataArrdtls[0][csf('total_liquor')])/($batch_array[$dataArray[0][csf('batch_id')]]['batch_qty']/1000);
				
				echo "1:".number_format($liqureRatio, 0); ?></td>
            </tr>
            <tr>
                <td><strong>Style Description:</strong></td>
                <td><?=$batch_array[$dataArray[0][csf('batch_id')]]['style_des']; ?></td>
                <td><strong>Quantity (Pcs):</strong></td>
                <td><?=$batch_array[$dataArray[0][csf('batch_id')]]['qty_pcs']; ?></td>
                <td><strong>Requisition No.:</strong></td>
                <td><?=$dataReqArray[0][csf('requ_no')]; ?></td>
            </tr>
        </table>
        <br>&nbsp;
        <div style="width:100%;">
            <table align="right" cellspacing="0" width="930" border="1" rules="all" class="rpt_table">
                <thead bgcolor="#dddddd" align="center">
                    <th width="30">SL</th>
                    <th width="80">Sequence</th>
                    <th width="130">Item Cat.</th>
                    <th width="180">Item Group</th>
                    <th width="220">Item Description</th>
                    <th width="50">Dosage %</th>
                    <th>Req. (Gram)</th>
                </thead>
				<?
				$i = 1; $j = 1; $mst_id = $data[2]; $com_id = $data[0];

				$process_array = array(); $sub_process_data_array = array(); $sub_process_remark_array = array();
				$sql = "select id, sub_process_id as sub_process_id,process_remark,total_liquor,liquor_ratio,recipe_time,recipe_temperature,recipe_ph from pro_recipe_entry_dtls where mst_id=$mst_id and status_active=1 and is_deleted=0 order by id";
				$nameArray = sql_select($sql);
				foreach ($nameArray as $row) 
				{
					if (!in_array($row[csf("sub_process_id")], $process_array)) 
					{
						$process_array[] = $row[csf("sub_process_id")];
						$sub_process_remark_array[$row[csf("sub_process_id")]]['remark'] = $row[csf("process_remark")];
					}
					$sub_process_remark_array[$row[csf("sub_process_id")]]['total_liquor'] = $row[csf("total_liquor")];

					$sub_process_remark_array[$row[csf("sub_process_id")]]['recipe_time'] 			= $row[csf("recipe_time")];
					$sub_process_remark_array[$row[csf("sub_process_id")]]['recipe_temperature'] 	= $row[csf("recipe_temperature")];
					$sub_process_remark_array[$row[csf("sub_process_id")]]['recipe_ph'] 			= $row[csf("recipe_ph")];
					$sub_process_remark_array[$row[csf("sub_process_id")]]['liquor_ratio'] 			= $row[csf("liquor_ratio")];

				}

				if ($db_type == 2) 
				{
					$sql = "select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, b.id as dtls_id, b.sub_process_id, b.process_remark, b.comments, b.item_lot, b.dose_base, b.ratio from product_details_master a, pro_recipe_entry_dtls b where a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$com_id' and a.item_category_id in (5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 order by a.item_category_id, b.dose_base";
				} 
				else if ($db_type == 0) 
				{
					$sql = "select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, b.id as dtls_id, b.sub_process_id, b.process_remark, b.comments, b.item_lot, b.dose_base, b.ratio, b.total_liquor,b.liquor_ratio from product_details_master a, pro_recipe_entry_dtls b where a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$com_id' and a.item_category_id in (5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 order by a.item_category_id, b.dose_base";
				}
				//echo $sql;
				$sql_result = sql_select($sql); $i=1;
				foreach ($sql_result as $row) 
				{
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					$seq=""; $amt_gl=""; $amt_per=""; 
					if($row[csf("item_category_id")]==6) $seq="B"; else $seq="A";
					$req_gram=0;
					if($row[csf("dose_base")]==1)
					{
						$amt_gl=$row[csf("ratio")];
						$req_gram=($amt_gl/1)*$dataArrdtls[0][csf('total_liquor')];
					}
					else 
					{
						$amt_gl=$row[csf("ratio")];
						$req_gram=($amt_gl/100)*1000*($batch_array[$dataArray[0][csf('batch_id')]]['batch_qty']/1000);
					}
					?>
					<tr bgcolor="<?=$bgcolor; ?>">
						<td align="center"><?=$i; ?></td>
                        <td align="center"><?=$seq; ?></td>
						<td><p><?=$item_category[$row[csf("item_category_id")]]; ?></p></td>
						<td><p><?=$item_group_arr[$row[csf("item_group_id")]]; ?></p></td>
						<td><p><?=$row[csf("item_description")]; ?></p></td>
						<td align="center"><p><?=$amt_gl; ?></p></td>
						
						<td align="right"><? echo number_format($req_gram, 6, '.', ''); ?>&nbsp;</td>
					</tr>
					<?
					$grand_gram += $req_gram;
					$i++;
				}
				?>
                <tr class="tbl_bottom">
                    <td align="right" colspan="6"><strong>Grand Total (Gram):</strong></td>
                    <td align="right"><?=number_format($grand_gram, 6, '.', ''); ?>&nbsp;</td>
                </tr>
            </table>
         </div>  
         <br>&nbsp; 
         <div> 
            <table align="right" cellspacing="0" width="930" border="1" rules="all" class="rpt_table"> 
                <tr>
                    <td colspan="2" align="center" bgcolor="#CCCCCC"><strong>Operations Sequence</strong></td>
                </tr>
                <tr>
                    <td width="650"><strong>Add ................ at ....................... & Load Garments:</strong></td>
                    <td rowspan="2">START: ............./.............</td>
                </tr>
                <tr>
                    <td><strong>Run Time .................. @ ...............:</strong></td>
                </tr>
                <tr>
                    <td><strong>Check Milling, If ok --> Drain & Rinse ...............:</strong></td>
                    <td>FINISH: ............./.............</td>
                </tr>
                <tr>
                    <td colspan="2" height="2px" style="background-color:#CCC">&nbsp;</td>
                </tr>
                <tr>
                    <td><strong>Fill Water, Add ........... - Run ..............:</strong></td>
                    <td rowspan="2">START: ............./.............</td>
                </tr>
                <tr>
                    <td><strong>Check ....................:</strong></td>
                </tr>
                <tr>
                    <td><strong>Heat at ........... (...................), Add ........... --- Run .................:</strong></td>
                    <td rowspan="2">FINISH: ............./.............</td>
                </tr>
                <tr>
                    <td><strong>Rest ....................., Run ................., Unload:</strong></td>
                </tr>
                <tr>
                    <td colspan="2" height="2px" style="background-color:#CCC">&nbsp;</td>
                </tr>
                <tr>
                    <td><strong>Hydro ..................:</strong></td>
                    <td>START: ............./.............</td>
                </tr>
                <tr>
                    <td><strong>&nbsp;</strong></td>
                    <td>FINISH: ............./.............</td>
                </tr>
                <tr>
                    <td colspan="2" height="2px" style="background-color:#CCC">&nbsp;</td>
                </tr>
                <tr>
                    <td><strong>Load Garments in Dryer & Run ................... at ..................:</strong></td>
                    <td>START: ............./.............</td>
                </tr>
                <tr>
                    <td><strong>................... and Unload Garments:</strong></td>
                    <td>FINISH: ............./.............</td>
                </tr>
            </table>
        </div>
        <br> &nbsp; 
        <div align="left"> 
            <table align="right" cellspacing="0" width="930" border="1" rules="all" class="rpt_table"> 
                <tr>
                    <td align="center" bgcolor="#CCCCCC"><strong>Checklist</strong></td>
                    <td align="center" bgcolor="#CCCCCC"><strong>Y / N</strong></td>
                </tr>
                <tr>
                    <td width="750"><strong>Machine Cleaning</strong></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><strong>PPE </strong></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><strong>&nbsp;</strong></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><strong>&nbsp;</strong></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><strong>&nbsp;</strong></td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </div>
        <br>&nbsp;
        <div>
            <table width="930" cellspacing="0" align="right" border="0">
                <tr>
                    <td><strong><u>Special Remarks :</u></strong></td>
                </tr>
                <tr>
                    <td>..........................................................................................................................................................................................................</td>
                </tr>
                <tr>
                    <td>..........................................................................................................................................................................................................</td>
                </tr>
                <tr>
                    <td>..........................................................................................................................................................................................................</td>
                </tr>
                <tr>
                    <td>..........................................................................................................................................................................................................</td>
                </tr>
            </table>
        </div>
        <?=signature_table(196, $com_id, "930px"); ?>
    </div>
	<?
	exit();
}
?>
