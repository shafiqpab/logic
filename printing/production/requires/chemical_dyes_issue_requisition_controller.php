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

//$color_arr = return_library_array("SELECT id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name");

if ($action == "load_drop_down_location") 
{
	echo create_drop_down("cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-Select Location-", 0, "");
	exit();
}


if ($action == "load_drop_down_color")
{
	 
	 echo create_drop_down( "cbo_color_id", 150, "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0 and id='$data' ","id,color_name", 1, "-- Select --", $data, "",1 );
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
			else if(val==6) 
			{
				$('#search_by_td').html('IR/IB');
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
                            <th width="130" colspan="2">Requisition Date Range</th>
                            <th width="100">Req/Recipe By</th>
                            <th width="140" id="search_by_td_up">Enter Requisition No</th>
                            <th width="100">Search By</th>
                            <th width="100" id="search_by_td">Embl. Job No</th>
                            <th>
                                <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton"/>
                                <input type="hidden" name="hidden_sys_id" id="hidden_sys_id" class="text_boxes" value="">
                            </th>
                        </tr>
					</thead>
					<tr class="general">
						<td>
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px;">
                        </td>
						<td>
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px;">
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
                        <td>
							<?
                                $search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style",6=>"IR/IB");
                                echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                        </td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_req_recipe').value+'_'+document.getElementById('cbo_req_recipe_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_year_selection').value+'_'+'<? echo $company; ?>', 'create_requisition_search_list_view', 'search_div', 'chemical_dyes_issue_requisition_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;"/>
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
	$company = $data[8];
	$year = $data[7];

	//echo $year; die;



	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[7]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]";}
	$color_arr = return_library_array("SELECT id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name");
	
	$date_cond = "";
	if ($start_date != "" && $end_date != "") 
	{
		if ($db_type == 0) 
		{
			$date_con ="and a.requisition_date between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'";
		} 
		else 
		{
			$date_cond="and a.requisition_date between '".change_date_format($start_date, "yyyy-mm-dd", "-", 1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-", 1)."'";
		}
	} 
	if ($db_type == 0)  $rid_cond="group_concat(id)"; else $rid_cond="listagg(id,',') within group (order by id)";
	
	$search_field_cond = ""; $recipe_ids ="";
	if ($search_req_recipe != "") 
	{
		if ($req_recipe_by == 1)
			$search_field_cond = "and a.requ_prefix_num='$search_req_recipe'";
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
			
			if(str_replace("'","",$recipe_idstr)!='') $search_field_cond = "and a.recipe_id in ($recipe_idstr)";
		} 
	} 
	//die;
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond.="and c.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond.="and c.order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			//else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
			else if ($search_by==4) $po_cond=" and b.buyer_po_no = '$search_str' ";
			//else if ($search_by==5) $style_cond=" and a.style_ref_no = '$search_str' ";
			else if ($search_by==5) $style_cond=" and b.buyer_style_ref = '$search_str' ";
			else if ($search_by==6) $inter_ref=" and b.grouping = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond.="and c.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond.="and c.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.buyer_po_no like '%$search_str%'"; 
			else if ($search_by==5) $style_cond=" and b.buyer_style_ref like '%$search_str%'"; 
			else if ($search_by==6) $inter_ref=" and b.grouping like '%$search_str%'";      
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond.="and c.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond.="and c.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.buyer_po_no like '$search_str%'";
			else if ($search_by==5) $style_cond=" and b.buyer_style_ref like '$search_str%'";
			else if ($search_by==6) $inter_ref=" and b.grouping like '$search_str%'";      
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond.="and c.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond.="and c.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $po_cond=" and b.buyer_po_no like '%$search_str'";
			else if ($search_by==5) $style_cond=" and b.buyer_style_ref like '%$search_str'"; 
			else if ($search_by==6) $inter_ref=" and b.grouping like '%$search_str'";   
		}
	}
	
	$po_ids=''; $buyer_po_arr=array();
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
	if(($job_cond!="" && $search_by==3) || ($inter_ref!="" && $search_by==6))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $inter_ref and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
	}
	//echo $po_ids;
	if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	
	/*$po_sql ="SELECT a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);*/
	
	
	$po_sql="select a.subcon_job, a.within_group, a.party_id, b.id, b.order_no, b.order_quantity, b.buyer_po_id,b.buyer_po_no, b.buyer_style_ref, 
   b.buyer_buyer,b.order_no from subcon_ord_mst a, subcon_ord_dtls b where a.embellishment_job=b.job_no_mst and a.entry_form=204  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
		$buyer_po_arr[$row[csf("id")]]['buyer_buyer']=$row[csf("buyer_buyer")];
		$po_arr[$row[csf("id")]]=$row[csf("order_no")];
	}
	unset($po_sql_res);
	
	
	
	$spo_ids='';
	
	if($db_type==0)
	{
		$id_cond="group_concat(b.id)";
		$year_field = "YEAR(a.insert_date) as year";
	}
	else if($db_type==2)
	{
		$id_cond="listagg(b.id,',') within group (order by b.id)";
		$year_field = "to_char(a.insert_date,'YYYY') as year";
	}
	/*if(($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.embellishment_job=b.job_no_mst $search_com_cond $style_cond $po_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
	}*/
	
	//if ( $spo_ids!="") $spo_idsCond=" and a.order_ids in ($spo_ids)"; else $spo_idsCond="";

	//$company_arr = return_library_array("SELECT id,company_name from lib_company where status_active =1 and is_deleted=0", "id", "company_name");
	
	$recipe_arr = return_library_array("SELECT id, recipe_no from pro_recipe_entry_mst where status_active =1 and is_deleted=0 and entry_form=220", "id", "recipe_no");
	//$po_arr = return_library_array("select embellishment_job, order_no from subcon_ord_mst group by embellishment_job, order_no", 'embellishment_job', 'order_no');
	//$po_arr=return_library_array( "SELECT id,order_no from subcon_ord_dtls where status_active =1 and is_deleted=0",'id','order_no');

	$sql = "SELECT a.id, a.requ_no, a.requ_prefix_num, $year_field, a.company_id, a.requisition_date, a.recipe_id, a.order_ids as order_id, a.job_no, a.buyer_po_ids as buyer_po_id ,a.color_id 
	from dyes_chem_issue_requ_mst a, subcon_ord_dtls b, subcon_ord_mst c 
	where c.id=b.mst_id and c.EMBELLISHMENT_JOB=a.JOB_NO and a.company_id=$company and a.entry_form=221 and a.status_active =1 and a.is_deleted=0 $date_cond $search_field_cond $po_idsCond $search_com_cond $po_cond $style_cond $year_cond
	group by a.id, a.requ_no, a.requ_prefix_num, a.insert_date, a.company_id, a.requisition_date, a.recipe_id, a.order_ids, a.job_no, a.buyer_po_ids ,a.color_id 
	order by a.id DESC ";//$search_field_cond //$spo_idsCond
	//echo $sql;
	$result = sql_select($sql);
	?>
	<div align="center">
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="50">Req. No</th>
			<th width="40">Year</th>
			<th width="70">Requisition Date</th>
			<th width="110">Recipe No</th>
			<th width="110">Job No</th>
			<th width="100">Order No</th>
            <th width="100">Buyer Po</th>
            <th width="100">Buyer Style</th>
            <th>Color</th>
		</thead>
	</table>
	<div style="width:850px; max-height:250px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table" id="tbl_list_search">
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
				if($order_no=="") $order_no=$po_arr[$po_id]; else $order_no.=','.$po_arr[$po_id];
			}
			$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
			$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
			$order_no=implode(",",array_unique(explode(",",$order_no)));

			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')].'___'.$row[csf('recipe_id')]; ?>');">
				<td width="30"><? echo $i; ?></td>
				<td width="50" align="center"><p><? echo $row[csf('requ_prefix_num')]; ?></p></td>
				<td width="40" align="center"><p><? echo $row[csf('year')]; ?></p></td>
				<td width="70" align="center"><p><? echo change_date_format($row[csf('requisition_date')]); ?></p></td>
                <td width="110"><p><? echo $recipe_arr[$row[csf('recipe_id')]]; ?></p></td>
				<td width="110"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
				
				<td width="100" style="word-break:break-all"><p><? echo $order_no; ?></p></td>
                <td width="100" style="word-break:break-all"><p><? echo $buyer_po; ?></p></td>
                <td width="100" style="word-break:break-all"><p><? echo $buyer_style; ?></p></td>
                <td><?= $color_arr[$row[csf('color_id')]];?></td>
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
	
	//$recipe_arr = return_library_array("SELECT id, recipe_no from pro_recipe_entry_mst where status_active =1 and is_deleted=0", "id", "recipe_no");
		//die;
	/*$order_arr = array();
	$order_qty_arr = array();
	//$embl_sql ="SELECT a.embellishment_job, b.id, b.order_no from subcon_ord_mst a, subcon_ord_dtls b where a.entry_form=204 and a.embellishment_job=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$embl_sql ="SELECT a.embellishment_job, b.id, b.order_no, b.order_uom, c.qnty from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.entry_form=204 and a.embellishment_job=b.job_no_mst and  b.job_no_mst=c.job_no_mst and b.id=c.mst_id and c.qnty>0 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	//echo $embl_sql;
	$embl_sql_res=sql_select($embl_sql);
	foreach ($embl_sql_res as $row)
	{
		$order_arr[$row[csf("id")]]['job']=$row[csf("embellishment_job")];
		$order_arr[$row[csf("id")]]['po']=$row[csf("order_no")];
		$order_arr[$row[csf("id")]]['order_uom']=$row[csf("order_uom")];
		$order_qty_arr[$row[csf("id")]]['qnty']+=$row[csf("qnty")];
	}
	unset($embl_sql_res);*/

	/*echo "<pre>";
	print_r($order_qty_arr); */

	$recipe_arr = array();
	$recipe_sql ="SELECT id, recipe_no,recipe_for from pro_recipe_entry_mst where status_active =1 and is_deleted=0 and entry_form=220";
	$recipe_res=sql_select($recipe_sql);
	foreach ($recipe_res as $row)
	{
		$recipe_arr[$row[csf("id")]]['recipe_no']=$row[csf("recipe_no")];
		$recipe_arr[$row[csf("id")]]['recipe_for']=$row[csf("recipe_for")];
	}
	unset($recipe_res);

	
	/*$buyer_po_arr=array();
	$po_sql ="SELECT a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
*/
	$order_sql ="select a.job_no_prefix_num,b.id,b.buyer_po_no,b.buyer_style_ref, b.job_no_mst,b.order_no,b.order_uom,c.color_id,c.qnty from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.id=b.mst_id and b.id=c.mst_id and a.embellishment_job=c.job_no_mst and a.entry_form='204' $search_com_cond";
	//echo $order_sql; die;
	$order_sql_res=sql_select($order_sql); $all_subcon_job='';
	foreach ($order_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no_prefix_num")];
		$buyer_po_arr[$row[csf("id")]]['order_no']=$row[csf("order_no")];
		$buyer_po_arr[$row[csf("id")]]['order_uom']=$row[csf("order_uom")];
		$buyer_po_qty_arr[$row[csf("id")]][$row[csf("color_id")]]['qnty']+=$row[csf("qnty")];
		//$po_id_arr[$row[csf("id")]]=$row[csf("id")];
		$all_subcon_job .="'".$row[csf("job_no_mst")]."'".',';
	}
	
	$sql = sql_select("SELECT id, requ_no, company_id, location_id, requisition_date, requisition_basis, recipe_id, order_ids as order_id, job_no, buyer_po_ids as buyer_po_id, is_apply_last_update, store_id,color_id, added_weight from dyes_chem_issue_requ_mst where id=$data and status_active =1 and is_deleted=0");
	foreach ($sql as $row) 
	{

		$recipe_no=$recipe_arr[$row[csf("recipe_id")]]['recipe_no'];
		$recipe_for=$recipe_arr[$row[csf("recipe_id")]]['recipe_for'];

		$po_id=explode(",",$row[csf('order_id')]);
		foreach($po_id as $val) 
		{
			//echo $val;
			if($buyer_po=="") $buyer_po=$buyer_po_arr[$val]['po']; else $buyer_po.=','.$buyer_po_arr[$val]['po'];
			if($buyer_style=="") $buyer_style=$buyer_po_arr[$val]['style']; else $buyer_style.=','.$buyer_po_arr[$val]['style'];
			if($job_no=="") $job_no=$buyer_po_arr[$val]['job']; else $job_no.=','.$buyer_po_arr[$val]['job'];
			if($order_no=="") $order_no=$buyer_po_arr[$val]['order_no']; else $order_no.=','.$buyer_po_arr[$val]['order_no'];
			if($order_uom=="") $order_uom=$buyer_po_arr[$val]['order_uom']; else $order_uom.=','.$buyer_po_arr[$val]['order_uom'];
			$order_qty +=$buyer_po_qty_arr[$val][$row[csf('color_id')]]['qnty'];
		}
		$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
		$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
		$job_no=implode(",",array_unique(explode(",",$job_no)));
		$order_no=implode(",",array_unique(explode(",",$order_no)));
		$order_uom=implode(",",array_unique(explode(",",$order_uom)));
		$order_uom=implode(",",array_unique(explode(",",$order_uom)));
		if($order_uom==1){ $qty=$order_qty;}
		if($order_uom==2){ $qty=$order_qty*12;}

		/*$order_uom=$order_arr[$row[csf("order_id")]]['order_uom'];

		if($order_uom==1){ $qty=$order_qty_arr[$row[csf("order_id")]]['qnty'];}
		if($order_uom==2){  $qty=$order_qty_arr[$row[csf("order_id")]]['qnty']*12;}*/

		echo "document.getElementById('txt_req_no').value = '" . $row[csf("requ_no")] . "';\n";
		echo "document.getElementById('update_id').value = '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('cbo_company_name').value = '" . $row[csf("company_id")] . "';\n";
		echo "document.getElementById('cbo_location_name').value = '" . $row[csf("location_id")] . "';\n";
		echo "document.getElementById('txt_requisition_date').value = '" . change_date_format($row[csf("requisition_date")]) . "';\n";
		echo "document.getElementById('cbo_receive_basis').value = '" . $row[csf("requisition_basis")] . "';\n";
		
		echo "document.getElementById('txt_recipe_id').value = '" . $row[csf("recipe_id")] . "';\n";
		echo "document.getElementById('txt_recipe_no').value = '" . $recipe_no . "';\n";
		echo "document.getElementById('cbo_store_name').value = '" . $row[csf("store_id")] . "';\n";
		
		echo "document.getElementById('txt_order_id').value = '" . $row[csf("order_id")] . "';\n";
		echo "document.getElementById('txt_order_no').value = '" . $order_no . "';\n";
		echo "document.getElementById('txt_job_no').value = '" . $job_no . "';\n";
		
		echo "document.getElementById('txtbuyerPoId').value = '" . $row[csf("buyer_po_id")] . "';\n";
		echo "document.getElementById('txtbuyerPo').value = '" . $buyer_po . "';\n";
		echo "document.getElementById('txtstyleRef').value = '" . $buyer_style . "';\n";
		echo "load_drop_down('requires/chemical_dyes_issue_requisition_controller', '".$row[csf("color_id")]."', 'load_drop_down_color', 'color_td' );\n";
		echo "document.getElementById('cbo_color_id').value = '" . $row[csf("color_id")] . "';\n";
		echo "document.getElementById('cbo_recipe_for').value = '" . $recipe_for . "';\n";
		echo "document.getElementById('txt_added_weight').value = '" . $row[csf("added_weight")] . "';\n";
		echo "document.getElementById('txt_ord_qnty').value = '" . $qty . "';\n";
	
		
		
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
		/*echo "get_php_form_data('" . $row[csf("recipe_id")] . "', 'populate_data_from_recipe_popup', 'requires/chemical_dyes_issue_requisition_controller' );\n";*/

		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_chemical_dyes_issue_requisition',1);\n";
		exit();
	}
}

if ($action == 'populate_data_from_recipe_popup') {
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
	$data_array = sql_select("SELECT id, entry_form, batch_id, total_liquor, new_batch_weight,batch_qty from pro_recipe_entry_mst where id in($data) and status_active=1 and is_deleted=0 and entry_form=220");
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
			else if(val==6)
			{
				$('#search_by_td').html('IR/IB');
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
                            <th width="160" colspan="2">Recipe Date Range</th>
                            <th width="100">Enter Recipe No</th>
                            <th width="100">Search By</th>
                            <th width="100" id="search_by_td">Embl. Job No</th>
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
                                $search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style",6=>"IR/IB");
                                echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:90px" placeholder="" />
                        </td>
                        <td>
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_recipe').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_year_selection').value+'_'+'<? echo $recipe_id; ?>', 'create_recipe_search_list_view', 'search_div', 'chemical_dyes_issue_requisition_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"  style="width:100px;"/>
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
	$recipe_id = $data[8];
	$year = $data[7];
	//$search_string = trim($data[0]);

	if($db_type==0) { $year_cond=" and YEAR(insert_date)=$data[7]";   }
	if($db_type==2) {$year_cond=" and to_char(insert_date,'YYYY')=$data[7]";}
	$color_arr = return_library_array("SELECT id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name");
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
	/*if($search_type==1)
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
	}*/
	
	$sysid_cond = ""; $rec_des_cond = ""; $job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if ($search_type == 1) 
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond.="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond.="and a.order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $search_com_cond.=" and b.buyer_po_no = '$search_str' ";
			else if ($search_by==5) $search_com_cond.=" and b.buyer_style_ref = '$search_str' ";
			else if ($search_by==6) $inter_ref=" and b.grouping = '$search_str' ";
		}
		if ($recipe_no != '') $recipe_no_cond = " and recipe_no_prefix_num=$recipe_no";
		if ($rec_des != '') $rec_des_cond = " and recipe_description='$rec_des'";
	} 
	else if ($search_type == 4 || $search_type == 0) 
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond.="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond.="and a.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) $search_com_cond.=" and b.buyer_po_no like '%$search_str%'"; 
			else if ($search_by==5) $search_com_cond.=" and b.buyer_style_ref like '%$search_str%'";   
		}
		if ($recipe_no != '') $recipe_no_cond = " and recipe_no_prefix_num like '%$recipe_no%' ";
		if ($rec_des != '') $rec_des_cond = " and recipe_description like '%$rec_des%'";
		else if ($search_by==6) $inter_ref=" and b.grouping like '%$search_str%'"; 
	} 
	else if ($search_type == 2) 
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond.="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond.="and a.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $search_com_cond.=" and b.buyer_po_no like '$search_str%'";
			else if ($search_by==5) $search_com_cond.=" and b.buyer_style_ref like '$search_str%'"; 
			else if ($search_by==6) $inter_ref=" and b.grouping like '$search_str%'";     
		}
		if ($recipe_no != '') $recipe_no_cond = " and recipe_no_prefix_num like '$recipe_no%' ";
		if ($rec_des != '') $rec_des_cond = " and recipe_description like '$rec_des%'";
	} 
	else if ($search_type == 3) 
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond.="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond.="and a.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $search_com_cond.=" and b.buyer_po_no like '%$search_str'";
			else if ($search_by==5) $search_com_cond.=" and b.buyer_style_ref like '%$search_str'";
			else if ($search_by==6) $inter_ref=" and b.grouping like '%$search_str'";    
		}
		if ($recipe_no != '') $recipe_no_cond = " and recipe_no_prefix_num like '%$recipe_no' ";
		if ($rec_des != '') $rec_des_cond = " and recipe_description like '%$rec_des'";
	}


	$po_ids='';  
	//if($db_type==0) $id_cond="group_concat(b.id)";
	//else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
	if(($job_cond!="" && $search_by==3) || ($inter_ref!="" && $search_by==6))
	{
  		//$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond $inter_ref", "id");
		
		$po_ids_sql ="select b.id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond $inter_ref";
	// echo $po_ids_sql; die;
	$po_ids_res=sql_select($po_ids_sql);
	
		foreach ($po_ids_res as $row)
		{
			 
			$po_ids .="'".$row[csf("id")]."'".',';
		}
		$po_ids=chop($po_ids,',');
	}
   		//echo $po_ids; die;
	if ($po_ids!="") $po_idsCond=" and buyer_po_ids in ($po_ids)"; else $po_idsCond="";



	$buyer_po_arr=array(); $buyer_po_qty_arr=array();
	$order_sql ="select a.job_no_prefix_num,b.id,b.buyer_po_no,b.buyer_style_ref, b.job_no_mst,b.order_no,b.order_uom,c.color_id,c.qnty from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.id=b.mst_id and b.id=c.mst_id and a.embellishment_job=c.job_no_mst and a.entry_form='204' $search_com_cond";
	//echo $order_sql; die;
	$order_sql_res=sql_select($order_sql); $all_subcon_job='';
	foreach ($order_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no_prefix_num")];
		$buyer_po_arr[$row[csf("id")]]['jobnomst']=$row[csf("job_no_mst")];
		$buyer_po_arr[$row[csf("id")]]['order_no']=$row[csf("order_no")];
		$buyer_po_arr[$row[csf("id")]]['order_uom']=$row[csf("order_uom")];
		$buyer_po_qty_arr[$row[csf("id")]][$row[csf("color_id")]]['qnty']+=$row[csf("qnty")];
		//$po_id_arr[$row[csf("id")]]=$row[csf("id")];
		$all_subcon_job .="'".$row[csf("job_no_mst")]."'".',';
	}
	//echo $all_subcon_job; die;
	$all_subcon_job_arr=array_unique(explode(",",(chop($all_subcon_job,','))));
	//print_r($all_subcon_job_arr); die;
	if($search_com_cond!='' || $job_cond!=''){
		$con = connect();
		foreach($all_subcon_job_arr as $key=>$row_val)
		{
			//echo $row_val; die;
			$r_id2=execute_query("insert into tmp_job_no (userid, job_no, entry_form) values ($user_id,$row_val,220)");
		}
		//print_r($issue_item_arr);
		//$wo_ids=implode(",",array_unique(explode(",",(chop($wo_id,',')))));
		if($db_type==0)
		{
			if($r_id2)
			{
				mysql_query("COMMIT");  
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			//echo $r_id2; die;
			if($r_id2)
			{
				oci_commit($con);  
			}
		} 
		$subcon_cond=" and job_no in (select job_no from tmp_job_no where userid=$user_id and entry_form=220) ";
	}

	/*


	$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4)|| ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	
	if ($po_ids!="") $po_idsCond=" and c.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	
	$order_arr = array();
	$order_qnty_arr=array();
	$embl_sql ="SELECT a.id, a.embellishment_job, a.party_id, a.order_id, b.id as po_id, b.order_no, b.main_process_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, c.color_id, b.buyer_po_no, b.buyer_style_ref, b.order_uom, sum(c.qnty) as qty 
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c 
	where a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and a.id=b.mst_id  and b.id=c.mst_id and a.entry_form=204 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by a.id, a.embellishment_job, a.party_id, a.order_id, b.id, b.order_no, b.main_process_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, c.color_id, b.buyer_po_no, b.buyer_style_ref, b.order_uom";
	$embl_sql_res=sql_select($embl_sql);
	foreach ($embl_sql_res as $row)
	{
		$order_arr[$row[csf("po_id")]]['job']=$row[csf("embellishment_job")];
		$order_arr[$row[csf("po_id")]]['po']=$row[csf("order_no")];
		$order_qnty_arr[$row[csf("embellishment_job")]][$row[csf("buyer_po_id")]][$row[csf("body_part")]][$row[csf("gmts_item_id")]][$row[csf("embl_type")]][$row[csf("color_id")]][$row[csf("po_id")]]+=$row[csf("qty")];
	}
	unset($embl_sql_res);
	$color_arr = return_library_array("SELECT id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');*/
	
	 $sql = "SELECT id, recipe_no_prefix_num, recipe_no, recipe_for, recipe_date, within_group, po_ids as po_id, buyer_id, gmts_item, body_part, embl_name, embl_type, color_id, store_id, buyer_po_ids as buyer_po_id,job_no from pro_recipe_entry_mst where company_id='$company_id' and entry_form=220 and status_active=1 and is_deleted=0 $recipe_no_cond $rec_des_cond $year_cond $date_cond $po_idsCond $spo_idsCond $po_id_cond  $subcon_cond order by id DESC";

	

	/*$sql = "SELECT a.id, a.recipe_no_prefix_num, a.recipe_no, a.recipe_date, a.color_id, a.store_id, a.job_no,a.recipe_for, b.job_no_prefix_num, b.embellishment_job, c.id as order_id, c.order_no, c.order_uom, c.buyer_po_id, c.buyer_po_no, c.body_part, c.gmts_item_id, c.embl_type, c.buyer_style_ref, sum(d.qnty) as qty
	from pro_recipe_entry_mst a, subcon_ord_mst b, subcon_ord_dtls c, subcon_ord_breakdown d
	where a.job_no=b.embellishment_job and a.po_id=c.id and b.embellishment_job=c.job_no_mst and c.id=d.mst_id and  c.job_no_mst=d.job_no_mst and d.qnty>0 and b.entry_form=204 and a.company_id='$company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_recipe_cond $search_com_cond $date_cond $po_idsCond group by a.id, a.recipe_no_prefix_num, a.recipe_no, a.recipe_date, a.color_id, a.store_id, a.job_no,a.recipe_for, b.job_no_prefix_num, b.embellishment_job, c.id, c.order_no, c.order_uom, c.buyer_po_id,c.buyer_po_no, c.body_part, c.gmts_item_id, c.embl_type, c.buyer_style_ref order by a.id Desc";*/
	$nameArray=sql_select($sql);

	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table">
		<thead>
			<th width="35">SL</th>
			<th width="70">Recipe No</th>
			<th width="80">Recipe Date</th>
			<th width="120">Embl. Job No</th>
			<th width="120">WO No</th>
            <th width="150">Buyer Po</th>
			<th width="100">Buyer Style</th>
			<th>Color<input type="hidden" name="txt_recipe_row_id" id="txt_recipe_row_id" value="<?php echo $recipe_row_id; ?>"/></th>
		</thead>
	</table>
	<div style="width:870px; overflow-y:scroll; max-height:220px;" id="buyer_list_view" align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table" id="tbl_list_search">
			<?
            $i = 1;
            $recipe_row_id = '';
            $hidden_recipe_id = explode(",", $recipe_id);           
            foreach ($nameArray as $row) 
            {
                if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
    
                if (in_array($row[csf('id')], $hidden_recipe_id)) 
                {
                    if ($recipe_row_id == "") $recipe_row_id = $i; else $recipe_row_id .= "," . $i;
                }

                $party_name=$buyer_po=$buyer_style=$job_no=$order_no=$order_uom=$buyer_po=""; $order_qty=0;
				if($row[csf('within_group')]==1) $party_name=$company_arr[$row[csf('buyer_id')]];
				else if($row[csf('within_group')]==2) $party_name=$buyer_arr[$row[csf('buyer_id')]];
				$po_id=explode(",",$row[csf('po_id')]);
				foreach($po_id as $val) 
				{
					//echo $val;
					if($buyer_po=="") $buyer_po=$buyer_po_arr[$val]['po']; else $buyer_po.=','.$buyer_po_arr[$val]['po'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$val]['style']; else $buyer_style.=','.$buyer_po_arr[$val]['style'];
					if($job_no=="") $job_no=$buyer_po_arr[$val]['job']; else $job_no.=','.$buyer_po_arr[$val]['job'];
					if($jobnomst=="") $jobnomst=$buyer_po_arr[$val]['jobnomst']; else $jobnomst.=','.$buyer_po_arr[$val]['jobnomst'];
					if($order_no=="") $order_no=$buyer_po_arr[$val]['order_no']; else $order_no.=','.$buyer_po_arr[$val]['order_no'];
					if($order_uom=="") $order_uom=$buyer_po_arr[$val]['order_uom']; else $order_uom.=','.$buyer_po_arr[$val]['order_uom'];
					$order_qty +=$buyer_po_qty_arr[$val][$row[csf('color_id')]]['qnty'];
				}
				$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				$job_no=implode(",",array_unique(explode(",",$job_no)));
				$jobnomst=implode(",",array_unique(explode(",",$jobnomst)));
				$order_no=implode(",",array_unique(explode(",",$order_no)));
				$order_uom=implode(",",array_unique(explode(",",$order_uom)));
				if($order_uom==1){ $qty=$order_qty;}
				if($order_uom==2){ $qty=$order_qty*12;}
               	/* if($row[csf('order_uom')]==1){ $qty=$order_qnty_arr[$row[csf("embellishment_job")]][$row[csf("buyer_po_id")]][$row[csf("body_part")]][$row[csf("gmts_item_id")]][$row[csf("embl_type")]][$row[csf("color_id")]][$row[csf("order_id")]];}
				if($row[csf('order_uom')]==2){  $qty=$order_qnty_arr[$row[csf("embellishment_job")]][$row[csf("buyer_po_id")]][$row[csf("body_part")]][$row[csf("gmts_item_id")]][$row[csf("embl_type")]][$row[csf("color_id")]][$row[csf("order_id")]]*12;}*/
    
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value('<? echo $row[csf('id')].'___'.$row[csf('recipe_no')].'___'.$row[csf('job_no')].'___'.$row[csf('po_id')].'___'.$order_no.'___'.$row[csf('buyer_po_id')].'___'.$buyer_po.'___'.$buyer_style.'___'.$row[csf('store_id')].'___'.$row[csf('color_id')].'___'.$row[csf('recipe_for')].'___'.$qty; ?>')">
                    <td width="35" align="center"><? echo $i; ?></td>
                    <td width="70" align="center"><? echo $row[csf('recipe_no_prefix_num')]; ?></td>
                    <td width="80" align="center"><? echo change_date_format($row[csf('recipe_date')]); ?>&nbsp;</td>
                    <td width="120"><? echo $job_no; ?>&nbsp;</td>
                    <td width="120"><? echo $order_no; ?>&nbsp;</td>
                    <td width="150"><? echo $buyer_po;//$buyer_po_arr[$row[csf("buyer_po_id")]]['po']; ?>&nbsp;</td>
                    <td width="100"><? echo $buyer_style;//$buyer_po_arr[$row[csf("buyer_po_id")]]['style']; ?>&nbsp;</td>
                    <td><p><? echo $color_arr[$row[csf('color_id')]]; ?>&nbsp;</p></td>
                </tr>
                <?
                $i++;
            }
            ?>
        </table>
    </div>
	<?
	$r_id3=execute_query("delete from tmp_job_no where userid=$user_id and entry_form=220");
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
			
				$color_arr = return_library_array("SELECT id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name");
			
				$pastweight_arr=array();
				$reqsn_data_arr=array();
				if($mst_id!=0)
				{
					$reqsn_sql="select id, multicolor_id, past_weight, product_id, required_qnty, adjust_percent, adjust_type, req_qny_edit, item_lot from dyes_chem_issue_requ_dtls where mst_id='$mst_id' and status_active=1 and is_deleted=0";
					$reqsn_sql_res = sql_select($reqsn_sql);
					foreach ($reqsn_sql_res as $row)
					{
						$pastweight_arr[$row[csf("multicolor_id")]]=$row[csf("past_weight")];
						
						$reqsn_data_arr[$row[csf("multicolor_id")]][$row[csf("product_id")]][$row[csf("item_lot")]]['qty']=$row[csf("required_qnty")];
						$reqsn_data_arr[$row[csf("multicolor_id")]][$row[csf("product_id")]][$row[csf("item_lot")]]['id']=$row[csf("id")];
						$reqsn_data_arr[$row[csf("multicolor_id")]][$row[csf("product_id")]][$row[csf("item_lot")]]['adjust_percent']=$row[csf("adjust_percent")];
						$reqsn_data_arr[$row[csf("multicolor_id")]][$row[csf("product_id")]][$row[csf("item_lot")]]['adjust_type']=$row[csf("adjust_type")];
						$reqsn_data_arr[$row[csf("multicolor_id")]][$row[csf("product_id")]][$row[csf("item_lot")]]['req_qny_edit']=$row[csf("req_qny_edit")];
					}
					unset($reqsn_sql_res);
				}
				//echo "<pre>";print_r($reqsn_data_arr);die;

				$item_group_arr = return_library_array("SELECT id, item_name from lib_item_group where status_active =1 and is_deleted=0", "id", "item_name");
                $multicolor_array = array();
                $prod_data_array = array();
                $new_prod_arr = array();
                $sql = "SELECT color_id, new_prod_id from pro_recipe_entry_dtls where mst_id=$recipe_id and status_active=1 and is_deleted=0 order by id ASC";
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
                
                $sql = "SELECT id, color_id, prod_id, comments, ratio, seq_no, store_id, item_lot from pro_recipe_entry_dtls where mst_id=$recipe_id and status_active=1 and is_deleted=0 order by seq_no ASC";
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
                    $prodIds.=$row[csf("prod_id")].",";
                }
                unset($nameArray);
                //echo $sql;
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
					$StoreProdIds_cond=chop($StoreProdIds_cond,'or ');
                    $prodIds_cond.=")";
					$StoreProdIds_cond.=")";
    
                }
                else
                {
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
                unset($sql_result);
				
				$sql_prod_store = "SELECT id, prod_id, store_id, lot, cons_qty  from inv_store_wise_qty_dtls where status_active=1 and is_deleted=0 $StoreProdIds_cond
				union all
				select max(id) as id, prod_id, store_id, batch_lot as lot, sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as cons_qty from inv_transaction where status_active=1 and is_deleted=0 and item_category=22 $StoreProdIds_cond
				group by  prod_id, store_id, batch_lot";
				//echo $sql_prod_store;
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
							$store_id=$exdata['store_id'];
							$prod_store_stock=$prod_store_data_array[$prod_id][$store_id][$exdata['item_lot']];
							$exprod_data=explode("**",$prod_data_array[$prod_id]);
							
							$item_category_id=$exprod_data[0]; $item_group_id=$exprod_data[1]; $sub_group_name=$exprod_data[2]; $item_description=$exprod_data[3]; $unit_of_measure=$exprod_data[4];
							$reqsnqty=$reqsn_data_arr[$mcolor_id][$prod_id][$exdata['item_lot']]['qty'];
							$dtls_id=$reqsn_data_arr[$mcolor_id][$prod_id][$exdata['item_lot']]['id'];
							$adjust_percent=$reqsn_data_arr[$mcolor_id][$prod_id][$exdata['item_lot']]['adjust_percent'];
							$adjust_type=$reqsn_data_arr[$mcolor_id][$prod_id][$exdata['item_lot']]['adjust_type'];
							$req_qny_edit=$reqsn_data_arr[$mcolor_id][$prod_id][$exdata['item_lot']]['req_qny_edit'];
							
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
                                <td width="70" align="right" id="storeStock_<? echo $k.'_'.$i; ?>" style="word-break:break-all;" title="<? echo "prod id: ".$prod_id."= Store id: ".$store_id."= Item lot : ".$exdata['item_lot']; ?>"><? echo number_format($prod_store_stock,2); ?></td>
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

			$new_requ_no = explode("*", return_mrr_number(str_replace("'", "", $cbo_company_name), '', 'PDCR', date("Y", time()), 5, "select requ_no_prefix, requ_prefix_num from dyes_chem_issue_requ_mst where company_id=$cbo_company_name and entry_form=221 and $year_cond=" . date('Y', time()) . " order by id desc ", "requ_no_prefix", "requ_prefix_num"));
			$id = return_next_id("id", "dyes_chem_issue_requ_mst", 1);
			//$id = return_next_id_by_sequence( "dyes_chem_issue_requ_dtls_pk","dyes_chem_issue_requ_dtls", $con);
			$field_array = "id, requ_no, requ_no_prefix, requ_prefix_num, company_id, location_id, requisition_date, requisition_basis, recipe_id, order_ids, job_no, buyer_po_ids, inserted_by, insert_date, entry_form, store_id, color_id, added_weight";
			$data_array = "(" . $id . ",'" . $new_requ_no[0] . "','" . $new_requ_no[1] . "'," . $new_requ_no[2] . "," . $cbo_company_name . "," . $cbo_location_name . "," . $txt_requisition_date . "," . $cbo_receive_basis . "," . $txt_recipe_id . "," . $txt_order_id . "," . $txt_job_no . "," . $txtbuyerPoId . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',221," . $cbo_store_name . "," . $cbo_color_id . "," . $txt_added_weight . ")";

			$mst_id = $id;
			$requ_no = $new_requ_no[0];
		} 
		else 
		{
			$field_array_up = "location_id*requisition_date*requisition_basis*recipe_id*order_ids*job_no*color_id*buyer_po_ids*added_weight*updated_by*update_date";
			$data_array_up = "" . $cbo_location_name . "*" . $txt_requisition_date . "*" . $cbo_receive_basis . "*" . $txt_recipe_id . "*" . $txt_order_id . "*" . $txt_job_no . "*" .$cbo_color_id . "*" . $txtbuyerPoId . "*" . $txt_added_weight . "*" . $user_id . "*'" . $pc_date_time . "'";
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
			//echo  "10**INSERT INTO dyes_chem_issue_requ_mst (".$field_array.") VALUES ".$data_array."";disconnect($con); disconnect($con); die;
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
		//echo  "10**INSERT INTO dyes_chem_issue_requ_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls.""; disconnect($con); die;
		$rID_dtls = sql_insert("dyes_chem_issue_requ_dtls", $field_array_dtls, $data_array_dtls, 0);
		if($rID_dtls==1 && $flag==1) $flag=1; else $flag=0;
		//echo  "10**INSERT INTO dyes_chem_issue_requ_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls.""; disconnect($con); die;
		//echo "10**".$rID ."&&". $rID_att ."&&". $rID_dtls;disconnect($con); disconnect($con); die;
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
		//echo "10**".$rID.'='.$rID_att.'='.$rID_dtls; disconnect($con); disconnect($con); die;
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
			$field_array_up = "location_id*requisition_date*requisition_basis*recipe_id*is_apply_last_update*order_ids*job_no*color_id*buyer_po_ids*added_weight*updated_by*update_date";
			$data_array_up = "" . $cbo_location_name . "*" . $txt_requisition_date . "*" . $cbo_receive_basis . "*" . $txt_recipe_id . "*0*" . $txt_order_id . "*" . $txt_job_no . "*" .$cbo_color_id . "*" . $txtbuyerPoId . "*" . $txt_added_weight . "*" . $user_id . "*'" . $pc_date_time . "'";
		} 
		else 
		{
			$field_array_up = "location_id*requisition_date*requisition_basis*recipe_id*order_ids*job_no*color_id*buyer_po_ids*added_weight*updated_by*update_date";
			$data_array_up = "" . $cbo_location_name . "*" . $txt_requisition_date . "*" . $cbo_receive_basis . "*" . $txt_recipe_id . "*" . $txt_order_id . "*" . $txt_job_no . "*" .$cbo_color_id . "*" . $txtbuyerPoId . "*" . $txt_added_weight . "*" . $user_id . "*'" . $pc_date_time . "'";
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
		//disconnect($con); disconnect($con); die;
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
			//echo "10**INSERT INTO dyes_chem_issue_requ_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls."";disconnect($con); disconnect($con); die;
			$rID_dtls = sql_insert("dyes_chem_issue_requ_dtls", $field_array_dtls, $data_array_dtls, 0);
			if($rID_dtls==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		/*if ($flag==1)
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
		}*/
		
		//echo "10**".$rID.'='.$rID_att.'='.$rID_dtls.'='.$delete_att.'='.$delete_dtls; disconnect($con); disconnect($con); die;

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
		
		$mrrsql= sql_select("select a.issue_number, a.req_no, a.req_id from inv_issue_master a, dyes_chem_issue_dtls b 
		where a.id=b.mst_id and a.req_id=$updateid and a.entry_form in (250) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
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
	//$imge_arr=return_library_array( "SELECT master_tble_id, image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	
	$buyer_po_arr=array(); $buyer_po_qty_arr=array();
	$order_sql ="select a.job_no_prefix_num,a.within_group,b.id,b.buyer_po_no,b.buyer_style_ref, b.job_no_mst,b.order_no,b.buyer_buyer,b.order_uom,c.color_id,c.qnty, c.amount from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.id=b.mst_id and b.id=c.mst_id and a.embellishment_job=c.job_no_mst and a.entry_form='204' $search_com_cond";
	//echo $order_sql; die;
	$order_sql_res=sql_select($order_sql); $all_subcon_job='';
	foreach ($order_sql_res as $row)
	{

		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
		$buyer_po_arr[$row[csf("id")]]['buyer_buyer']=$row[csf("buyer_buyer")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no_prefix_num")];
		$buyer_po_arr[$row[csf("id")]]['order_no']=$row[csf("order_no")];
		$buyer_po_arr[$row[csf("id")]]['order_uom']=$row[csf("order_uom")];
		$buyer_po_arr[$row[csf("id")]]['within_group']=$row[csf("within_group")];
		$buyer_po_qty_arr[$row[csf("id")]][$row[csf("color_id")]]['qnty']+=$row[csf("qnty")];
		$buyer_po_qty_arr[$row[csf("id")]][$row[csf("color_id")]]['amount']+=$row[csf("amount")];
		$buyer_po_qty_arr[$row[csf("id")]][$row[csf("color_id")]]['rate']=$row[csf("amount")]/$row[csf("qnty")];;
		//$po_id_arr[$row[csf("id")]]=$row[csf("id")];
		$all_subcon_job .="'".$row[csf("job_no_mst")]."'".',';

	}


	$sql_mst = "SELECT id, requ_no, requ_no_prefix, requ_prefix_num, company_id, location_id, requisition_date, requisition_basis, recipe_id, order_ids as order_id, job_no, buyer_po_id as buyer_po_id, entry_form from dyes_chem_issue_requ_mst where id='$data[1]' and status_active =1 and is_deleted=0";
	//echo $sql_mst;
	$dataArray = sql_select($sql_mst); $party_name="";
	$recipe_id=$dataArray[0][csf('recipe_id')];
	$txt_req_no=$dataArray[0][csf('requ_no')];
	
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


	$po_id=explode(",",$dataArray[0][csf('order_id')]);
	foreach($po_id as $val) 
	{
		//echo $val;
		if($buyer_po=="") $buyer_po=$buyer_po_arr[$val]['po']; else $buyer_po.=','.$buyer_po_arr[$val]['po'];
		if($buyer_style=="") $buyer_style=$buyer_po_arr[$val]['style']; else $buyer_style.=','.$buyer_po_arr[$val]['style'];
		if($job_no=="") $job_no=$buyer_po_arr[$val]['job']; else $job_no.=','.$buyer_po_arr[$val]['job'];
		
		if($order_no=="") $order_no=$buyer_po_arr[$val]['order_no']; else $order_no.=','.$buyer_po_arr[$val]['order_no'];
		if($order_uom=="") $order_uom=$buyer_po_arr[$val]['order_uom']; else $order_uom.=','.$buyer_po_arr[$val]['order_uom'];
		$within_group=$buyer_po_arr[$val]['within_group'];
		//if($within_group=="") $within_group=$buyer_po_arr[$val]['within_group']; else $within_group.=','.$buyer_po_arr[$val]['within_group'];
		$order_qty +=$buyer_po_qty_arr[$val][$recipe_arr[$recipe_id]['color']]['qnty'];
		$order_amount +=$buyer_po_qty_arr[$val][$recipe_arr[$recipe_id]['color']]['amount'];

		if ($within_group==1) {
			if($buyer_buyer=="") $buyer_buyer=$buyer_library[$buyer_po_arr[$val]['buyer_buyer']]; else $buyer_buyer.=','.$buyer_library[$buyer_po_arr[$val]['buyer_buyer']];
        }else{
           if($buyer_buyer=="") $buyer_buyer=$buyer_po_arr[$val]['buyer_buyer']; else $buyer_buyer.=','.$buyer_po_arr[$val]['buyer_buyer'];
        }
//$order_qty +=$buyer_po_qty_arr[$val][$recipe_arr[$recipe_id]['color']]['qnty'];
	}
	$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
	$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
	
	$job_no=implode(",",array_unique(explode(",",$job_no)));
	$order_no=implode(",",array_unique(explode(",",$order_no)));
	$order_uom=implode(",",array_unique(explode(",",$order_uom)));
	$within_group=implode(",",array_unique(explode(",",$within_group)));
	foreach($po_id as $val) 
	{
		if ($within_group==1) {
			if($buyer_buyer=="") $buyer_buyer=$buyer_library[$buyer_po_arr[$val]['buyer_buyer']]; else $buyer_buyer.=','.$buyer_library[$buyer_po_arr[$val]['buyer_buyer']];
        }else{
           if($buyer_buyer=="") $buyer_buyer=$buyer_po_arr[$val]['buyer_buyer']; else $buyer_buyer.=','.$buyer_po_arr[$val]['buyer_buyer'];
        }
	}
	if($order_uom==1){ $qty=$order_qty;}
	if($order_uom==2){ $qty=$order_qty*12;}
	$order_rate=$order_amount/$order_qty;
	$buyer_buyer=implode(",",array_unique(explode(",",$buyer_buyer)));
	?>
    <div style="width:1140px;">
        <table width="100%" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="70" align="right" valign="top"> 
                    <img  src='../../<? echo $com_dtls[2]; ?>' height='70'/>
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
                <td><? echo $order_no; ?></td>
                <td><strong>Buyer Po:</strong></td>
                <td> <? echo $buyer_po; ?></td>
                <td><strong>Buyer Style:</strong></td>
                <td><? echo $buyer_style; ?></td>
            </tr>
            <tr>
                <td><strong>Buyer's Buyer:</strong></td>
                <td>
                <?
	                $buyer_buyer; ?>
                 	
                 </td>
                
                <td><strong>Rate/Dzn:</strong></td>
                <td> <? echo $order_rate; ?></td>
                <td><strong>Order Qty:</strong></td>
                <td><? echo $qty; ?></td>
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
				$reqsn_sql="SELECT id, multicolor_id, past_weight, product_id, required_qnty, adjust_percent, adjust_type, req_qny_edit, item_lot 
				from dyes_chem_issue_requ_dtls where mst_id='$mst_id' and status_active=1 and is_deleted=0";
				//echo $reqsn_sql;
				$reqsn_sql_res = sql_select($reqsn_sql);
				foreach ($reqsn_sql_res as $row)
				{
					$pastweight_arr[$row[csf("multicolor_id")]]=$row[csf("past_weight")];
					
					$reqsn_data_arr[$row[csf("multicolor_id")]][$row[csf("product_id")]][$row[csf("item_lot")]]['qty']=$row[csf("required_qnty")];
					$reqsn_data_arr[$row[csf("multicolor_id")]][$row[csf("product_id")]][$row[csf("item_lot")]]['id']=$row[csf("id")];
					$reqsn_data_arr[$row[csf("multicolor_id")]][$row[csf("product_id")]][$row[csf("item_lot")]]['adjust_percent']=$row[csf("adjust_percent")];
					$reqsn_data_arr[$row[csf("multicolor_id")]][$row[csf("product_id")]][$row[csf("item_lot")]]['adjust_type']=$row[csf("adjust_type")];
					$reqsn_data_arr[$row[csf("multicolor_id")]][$row[csf("product_id")]][$row[csf("item_lot")]]['req_qny_edit']=$row[csf("req_qny_edit")];
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
				
				$sql_prod_store = "SELECT id, prod_id, store_id, cons_qty, lot  from inv_store_wise_qty_dtls where status_active=1 and is_deleted=0 $StoreProdIds_cond
				union all
				Select max(id) as id, prod_id, store_id, sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as cons_qty, batch_lot as lot
				from inv_transaction where status_active=1 and is_deleted=0 $StoreProdIds_cond
				group by  prod_id, store_id, batch_lot";
				//echo $sql_prod_store;
				$sql_prod_store_result = sql_select($sql_prod_store);
				foreach ($sql_prod_store_result as $row) 
				{
					$prod_store_data_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("lot")]]= $row[csf("cons_qty")];
				}
				//print_r($prod_store_data_array[41428][11]);die;
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
						$store_wise_stock=$prod_store_data_array[$exdata['prod_id']][$exdata['store_id']][$exdata['item_lot']];
						$exprod_data=explode("**",$prod_data_array[$prod_id]);
						
						$item_category_id=$exprod_data[0]; $item_group_id=$exprod_data[1]; $sub_group_name=$exprod_data[2]; $item_description=$exprod_data[3]; $unit_of_measure=$exprod_data[4];
						$reqsn_qty=$reqsn_data_arr[$mcolor_id][$prod_id][$exdata['item_lot']]['qty'];
						$adjust_percent=$reqsn_data_arr[$mcolor_id][$prod_id][$exdata['item_lot']]['adjust_percent'];
						$adjust_type=$reqsn_data_arr[$mcolor_id][$prod_id][$exdata['item_lot']]['adjust_type'];
						$req_qny_edit=$reqsn_data_arr[$mcolor_id][$prod_id][$exdata['item_lot']]['req_qny_edit'];
						
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>">
                            <td><? echo $i.''; ?></td>
                            <td><p><? echo $item_category[$item_category_id]; ?>&nbsp;</p></td>
                            <td><p><? echo $item_group_arr[$item_group_id]; ?>&nbsp;</p></td>
                            <td align="center"><? echo $exdata['item_lot']; ?></td>
                            <td><p><? echo $sub_group_name; ?>&nbsp;</p></td>
                            <td><p><? echo $item_description; ?>&nbsp;</p></td>
                            <td align="center"><p><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</p></td>
                            <td align="right" title="<?= $exdata['prod_id']."=".$exdata['store_id']."=".$exdata['item_lot']; ?>"><p><? echo number_format($store_wise_stock,2); ?></p></td>
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
			echo get_spacial_instruction($txt_req_no,"1100px",221);
			echo signature_table(258, $com_id, "1100px");
			?>
        </div>
    </div>
	<?
	exit();
}
?>
