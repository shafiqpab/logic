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
				$('#search_by_td').html('AOP. Job No');
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
	<div align="center" style="width:980px; margin: 0 auto;">
		<form name="searchfrm" id="searchfrm">
			<fieldset style="width:980px;">
				<table cellpadding="0" cellspacing="0" width="880" border="1" rules="all" class="rpt_table">
					<thead>
                    	<tr>
                            <th colspan="10"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                        <tr>
                            <th width="130" colspan="2">Requisition Date Range</th>
                            <th width="100">Req/Recipe By</th>
                            <th width="100" id="search_by_td_up">Enter Requisition No</th>
                            <th width="90">Search By</th>
                            <th width="90" id="search_by_td">AOP Job No</th>
                            <th width="100">Batch No</th>
                            <th width="100">Design No</th>
                            <th width="100">AOP Ref.</th>
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
							echo create_drop_down("cbo_req_recipe_by", 90, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td>
							<input type="text" style="width:90px;" class="text_boxes" name="txt_search_req_recipe" id="txt_search_req_recipe"/>
						</td>
                        <td>
							<?
                                $search_by_arr=array(1=>"AOP Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
                                echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                        </td>
                        <td>
                        	<input type="text" name="txt_batch_number" id="txt_batch_number" class="text_boxes" style="width:100px" />
                    	</td>
                        <td align="center">
                            <input type="text" name="txt_design_no" id="txt_design_no" class="text_boxes" style="width:100px" placeholder="" />
                        </td>
                        <td align="center">
                            <input type="text" name="txt_aop_ref" id="txt_aop_ref" class="text_boxes" style="width:100px" placeholder="" />
                        </td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_req_recipe').value+'_'+document.getElementById('cbo_req_recipe_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+'<? echo $company; ?>'+'_'+document.getElementById('txt_aop_ref').value+'_'+document.getElementById('txt_batch_number').value+'_'+document.getElementById('txt_design_no').value, 'create_requisition_search_list_view', 'search_div', 'chemical_dyes_issue_requisition_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;"/>
						</td>
					</tr>
					<tr>
						<td colspan="10" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
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
	$aop_ref=trim(str_replace("'","",$data[8]));
	$batch_no=trim(str_replace("'","",$data[9]));
	$design_no=trim(str_replace("'","",$data[10]));

	if ($batch_no!='')
	{ 
		//$po_ids = return_field_value("id", "pro_batch_create_mst", "batch_no='$batch_no'", "id");
		$aop_batch_cond= "and c.batch_no like '%$batch_no%'"; 
	} 
	else
	{
		$aop_batch_cond="";
	}

	if ($design_no!='')
	{ 

		$design_no_cond= "and b.design_no like '%$design_no%'"; 
	} 
	else
	{
		$design_no_cond="";
	}	
	
	
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
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
			else if ($search_by==5) $style_cond=" and a.style_ref_no = '$search_str' ";
		}
		if ($aop_ref!='') $aop_ref_cond=" and a.aop_reference = '$aop_ref' ";
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
		if ($aop_ref!='') $aop_ref_cond=" and a.aop_reference like '%$aop_ref%'";
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
		if ($aop_ref!='') $aop_ref_cond=" and a.aop_reference like '$aop_ref%'";
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
		if ($aop_ref!='') $aop_ref_cond=" and a.aop_reference like '%$aop_ref'";
	}
	//echo  $aop_ref_cond;
	/*$po_ids=''; $buyer_po_arr=array();
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	//echo $po_ids;
	if ($po_ids!="") $po_idsCond=" and a.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	*/

	$po_ids=''; $buyer_po_arr=array();
	if($db_type==0) $id_cond="group_concat(b.id) as id";
	else if($db_type==2) $id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as id";
	
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	//echo $po_ids."==".$po_cond."==";
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
		//echo $po_ids."==";
	}
	else if($po_ids=="" && (($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5)))
	{
		echo "Not Found"; die;
	}
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $poIdsCond";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
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
	if(($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2))
	{
		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.subcon_job=b.job_no_mst $search_com_cond $design_no_cond", "id");
	}
	
	if ( $spo_ids!="") $spo_idsCond=" and a.order_id in ($spo_ids)"; else $spo_idsCond="";

	$company_arr = return_library_array("select id,company_name from lib_company", "id", "company_name");
	
	$recipe_arr = return_library_array("select id, recipe_no_prefix_num from pro_recipe_entry_mst", "id", "recipe_no_prefix_num");

	/*if($aop_ref_cond!='')
	{
		$ord_sql = "select b.id,a.job_no_prefix_num,a.aop_reference,b.order_no from subcon_ord_mst a ,subcon_ord_dtls b where  company_id =$company $aop_ref_cond and a.entry_form=278 and  a.subcon_job=b.job_no_mst";
		$ordArray=sql_select( $ord_sql ); $po_arr=array(); $ref_arr=array(); $job_arr=array();
		foreach ($ordArray as $row)
		{
			$po_arr[$row[csf('id')]] = $row[csf('order_no')];
			$ref_arr[$row[csf('id')]] = $row[csf('aop_reference')];
			$job_arr[$row[csf('id')]] = $row[csf('job_no_prefix_num')];
			$po_id[] .= $row[csf("id")];
		}
		$order_id_cond=" and a.order_id in (".implode(",",$po_id).") ";
	} 
	else
	{
		$ord_sql = "select b.id,a.job_no_prefix_num,a.aop_reference,b.order_no from subcon_ord_mst a ,subcon_ord_dtls b where company_id =$company and a.entry_form=278 and  a.subcon_job=b.job_no_mst";
		$ordArray=sql_select( $ord_sql ); $po_arr=array(); $ref_arr=array(); $job_arr=array();
		foreach ($ordArray as $row)
		{
			$po_arr[$row[csf('id')]] = $row[csf('order_no')];
			$ref_arr[$row[csf('id')]] = $row[csf('aop_reference')];
			$job_arr[$row[csf('id')]] = $row[csf('job_no_prefix_num')];
		}
		//$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
		$order_id_cond='';
	}
*/
	if($aop_ref_cond!='' || $design_no_cond!='')
	{
		$ord_sql = "SELECT b.id,a.subcon_job,a.aop_reference,b.order_no,b.buyer_style_ref,b.buyer_po_no, b.design_no from subcon_ord_mst a ,subcon_ord_dtls b where  a.company_id =$company $aop_ref_cond $design_no_cond and a.entry_form=278 and  a.subcon_job=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
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
		$order_id_cond=" and a.order_id in (".implode(",",$po_id).") ";
	}
	else
	{
		$ord_sql = "SELECT b.id,a.subcon_job,a.aop_reference,b.order_no,b.buyer_style_ref,b.buyer_po_no, b.design_no from subcon_ord_mst a ,subcon_ord_dtls b where a.company_id =$company and a.entry_form=278 and  a.subcon_job=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
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
		$order_id_cond='';
	}
	//$po_arr = return_library_array("select subcon_job, order_no from subcon_ord_mst group by subcon_job, order_no", 'subcon_job', 'order_no');
	//$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no'); batch_no

	$sql = "select a.id, a.requ_no, a.requ_prefix_num, $year_field, a.company_id, a.requisition_date, a.recipe_id, a.order_id, a.job_no,a. buyer_po_id,b.po_id,c.entry_form ,c.within_group ,c.is_sales,c.sales_order_no,c.booking_no ,c.sales_order_id,c.batch_no from dyes_chem_issue_requ_mst a,pro_recipe_entry_mst b,pro_batch_create_mst c where a.recipe_id=b.id and b.batch_id=c.id and a.company_id=$company and a.entry_form=290 and a.status_active=1 and b.status_active=1 and c.status_active=1 $date_cond $search_field_cond $po_idsCond $spo_idsCond $order_id_cond  $aop_batch_cond order by id DESC";
	
	//$search_field_cond
	//echo $sql;
	$result = sql_select($sql);
	?>
	<div align="center">
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="975" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="40">Req. No</th>
			<th width="40">Year</th>
			<th width="60">Req. Date</th>
			<th width="50">Recipe No</th>
			<th width="50">AOP Job No.</th>
			<th width="110">Buyer Job No</th>
			<th width="110">Work Order</th>
            <th width="110">Buyer Po</th>
            <th width="110">Buyer Style</th>
            <th width="100">Batch No</th>
            <th width="80">Design No</th>
            <th>AOP Ref.</th>
		</thead>
	</table>
	<div style="width:980px; max-height:250px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="975" class="rpt_table" id="tbl_list_search">
		<?
		$i = 1;
		foreach ($result as $row) 
		{
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
			
			$buyer_po=""; $buyer_style="";
			$order_id=array_unique(explode(",",$row[csf("po_id")])); 
        	$buyer_job=''; $buyer_po=''; $buyer_style=''; $buyer_po_id='';
        	$order_no=$po_arr[$row[csf('order_id')]]['order'];
        	$design_no=$po_arr[$row[csf('order_id')]]['design_no'];
        	foreach($order_id as $val)
			{
				if($row[csf("entry_form")]==0)
				{
					if($row[csf("is_sales")]==1)
					{
						$buyer_po=$row[csf("sales_order_no")];
						$order_no=$row[csf("booking_no")];
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
					if($row[csf('within_group')]==1) {
						$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
						foreach($buyer_po_id as $po_id)
						{
							if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
							if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
							$buyer_job=$buyer_po_arr[$po_id]['job'];
						}
					}else{
						if($buyer_po=="") $buyer_po=$po_arr[$row[csf("order_id")]]['buyer_po_no']; else $buyer_po.=", ".$po_arr[$row[csf("order_id")]]['buyer_po_no'];
						if($buyer_style=="") $buyer_style=$po_arr[$row[csf("order_id")]]['buyer_style_ref']; else $buyer_style.=", ".$po_arr[$row[csf("order_id")]]['buyer_style_ref'];
					}
					
					//$buyer_po=$buyer_po_arr[$row[csf("buyer_po_id")]]['po'];
					//$buyer_style=$buyer_po_arr[$row[csf("buyer_po_id")]]['style'];
					$buyer_po_id=$row[csf("buyer_po_id")];

				}
			}
			$aop_job=implode(", ",array_unique(explode(", ",$aop_jobs)));
			$order_no=implode(", ",array_unique(explode(", ",$order_no)));

			$buyer_job=implode(", ",array_unique(explode(", ",$buyer_job)));
			$buyer_po=implode(", ",array_unique(explode(", ",$buyer_po)));
			$buyer_style=implode(", ",array_unique(explode(", ",$buyer_style)));
			
			foreach($buyer_po_id as $po_id)
			{
				if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
				if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
			}
			$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
			$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')].'___'.$row[csf('recipe_id')].'___'.$ref_arr[$row[csf('order_id')]]; ?>');">
				<td width="30"><? echo $i; ?></td>
				<td width="40"><p><? echo $row[csf('requ_prefix_num')]; ?></p></td>
				<td width="40" align="center"><p><? echo $row[csf('year')]; ?></p></td>
				<td width="60" align="center"><p><? echo change_date_format($row[csf('requisition_date')]); ?></p></td>
                <td width="50" align="center"><p><? echo $recipe_arr[$row[csf('recipe_id')]]; ?></p></td>
                <td width="50" align="center"><p><? echo $job_arr[$row[csf('order_id')]]; ?></p></td>
				<td width="110"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
				<td width="110" style="word-break:break-all"><p><? echo $order_no; ?></p></td>
                <td width="110" style="word-break:break-all"><p><? echo $buyer_po; ?></p></td>
                <td width="110" style="word-break:break-all"><p><? echo $buyer_style; ?></p></td>
                <td width="100" style="word-break:break-all"><p><? echo $row[csf('batch_no')];  ?></p></td>
                <td width="80" style="word-break:break-all"><p><? echo $design_no;  ?></p></td>
                <td style="word-break:break-all"><p><? echo $ref_arr[$row[csf('order_id')]]; ?></p></td>
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
	$user_arr = return_library_array("select id, user_full_name from user_passwd", 'id', 'user_full_name');
	$recipe_arr = return_library_array("select id, recipe_no from pro_recipe_entry_mst", "id", "recipe_no");
	
	$order_arr = array();
	/*$embl_sql ="Select a.subcon_job, b.id, b.order_no from subcon_ord_mst a, subcon_ord_dtls b where a.entry_form=278 and a.subcon_job=b.job_no_mst";
	$embl_sql_res=sql_select($embl_sql);
	foreach ($embl_sql_res as $row)
	{
		$order_arr[$row[csf("id")]]['job']=$row[csf("subcon_job")];
		$order_arr[$row[csf("id")]]['po']=$row[csf("order_no")];
	}
	unset($embl_sql_res);*/

	$order_sql ="SELECT b.id,b.order_no,b.buyer_style_ref,b.buyer_po_no,a.within_group from subcon_ord_dtls b , subcon_ord_mst a where a.subcon_job=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$order_sql_res=sql_select($order_sql);
	foreach ($order_sql_res as $row)
	{
		$order_arr[$row[csf("id")]]['order_no']=$row[csf("order_no")];
		$order_arr[$row[csf("id")]]['buyer_style_ref']=$row[csf("buyer_style_ref")];
		$order_arr[$row[csf("id")]]['buyer_po_no']=$row[csf("buyer_po_no")];
		$order_arr[$row[csf("id")]]['within_group']=$row[csf("within_group")];
	}
	unset($order_sql_res);
	
	$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst ";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	
	$sql = sql_select("select id, requ_no, company_id, location_id, requisition_date, requisition_basis, recipe_id, order_id, job_no, buyer_po_id, is_apply_last_update, store_id from dyes_chem_issue_requ_mst where id=$data");
	foreach ($sql as $row) 
	{
		if($order_arr[$row[csf("order_id")]]['within_group']==1){
			$buyer_po=$buyer_po_arr[$row[csf("buyer_po_id")]]['po'];
			$buyer_style=$buyer_po_arr[$row[csf("buyer_po_id")]]['style'];
		}
		else{
			$buyer_po=$order_arr[$row[csf("order_id")]]['buyer_po_no'];
			$buyer_style=$order_arr[$row[csf("order_id")]]['buyer_style_ref'];
		}
		echo "document.getElementById('txt_req_no').value = '" . $row[csf("requ_no")] . "';\n";
		echo "document.getElementById('update_id').value = '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('cbo_company_name').value = '" . $row[csf("company_id")] . "';\n";
		echo "document.getElementById('cbo_location_name').value = '" . $row[csf("location_id")] . "';\n";
		echo "document.getElementById('txt_requisition_date').value = '" . change_date_format($row[csf("requisition_date")]) . "';\n";
		echo "document.getElementById('cbo_receive_basis').value = '" . $row[csf("requisition_basis")] . "';\n";
		
		echo "document.getElementById('txt_recipe_id').value = '" . $row[csf("recipe_id")] . "';\n";
		echo "document.getElementById('txt_recipe_no').value = '" . $recipe_arr[$row[csf("recipe_id")]] . "';\n";
		
		echo "document.getElementById('txt_order_id').value = '" . $row[csf("order_id")] . "';\n";
		echo "document.getElementById('txt_order_no').value = '" . $order_arr[$row[csf("order_id")]]['order_no'] . "';\n";
		echo "document.getElementById('txt_job_no').value = '" . $row[csf("job_no")] . "';\n";
		
		echo "document.getElementById('cbo_store_name').value = '" . $row[csf("store_id")] . "';\n";
		echo "document.getElementById('txtbuyerPoId').value = '" . $row[csf("buyer_po_id")] . "';\n";
		echo "document.getElementById('txtbuyerPo').value = '" . $buyer_po . "';\n";
		echo "document.getElementById('txtstyleRef').value = '" . $buyer_style . "';\n";

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
		//echo "get_php_form_data('" . $row[csf("recipe_id")] . "', 'populate_data_from_recipe_popup', 'requires/chemical_dyes_issue_requisition_controller' );\n";

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
			if(val==1 || val==0)
			{
				$('#search_by_td').html('AOP. Job No');
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
                <table cellpadding="0" cellspacing="0" width="850" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="9"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "--Searching Type--" ); ?></th>
                        </tr>
                        <tr>
                            
                            <th width="100">Enter Recipe No</th>
                            <th width="150">Search By</th>
                            <th width="100" id="search_by_td">AOP Job No</th>
                            <th width="100">Batch No</th>
                            <th width="100">Design No</th>
                            <th width="100">AOP Ref.</th>
                            <th width="130" colspan="2">Recipe Date Range</th>
                            <th>
                                <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton"/>
                                <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                                <input type="hidden" name="hidden_recipe_id" id="hidden_recipe_id" class="text_boxes" value="">
                            </th>
                        </tr>
                    </thead>
                    <tr class="general">
                        
                        <td><input type="text" style="width:90px;" class="text_boxes" name="txt_search_recipe" id="txt_search_recipe"/></td>
                        <td>
                            <?
                                $search_by_arr=array(1=>"AOP Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
                                echo create_drop_down( "cbo_type",150, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                        </td>
                         <td>
                        	<input type="text" name="txt_batch_number" id="txt_batch_number" class="text_boxes" style="width:100px" />
                    	</td>
                    	<td>
                        	<input type="text" name="txt_design_no" id="txt_design_no" class="text_boxes" style="width:100px" />
                    	</td>
                        <td align="center">
                            <input type="text" name="txt_aop_ref" id="txt_aop_ref" class="text_boxes" style="width:100px" placeholder="" />
                        </td>
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" placeholder="From" style="width:60px;"></td>
                        <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To" style="width:60px;"></td>
                        <td>
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_recipe').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+'<? echo $recipe_id; ?>'+'_'+document.getElementById('txt_aop_ref').value+'_'+document.getElementById('txt_batch_number').value+'_'+document.getElementById('txt_design_no').value, 'create_recipe_search_list_view', 'search_div', 'chemical_dyes_issue_requisition_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"  style="width:100px;"/>
                        </td>
                    </tr>
                    <tr class="general">
                        <td colspan="9" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
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
	//print_r($data);
	
	$start_date = $data[0];
	$end_date = $data[1];
	$recipe_no = $data[2];
	$search_by = $data[3];
	$search_str = trim($data[4]);
	$company_id = $data[5];
	$search_type = $data[6];
	$recipe_id = $data[7];
	$aop_ref=trim(str_replace("'","",$data[8]));
	$batch_no=trim(str_replace("'","",$data[9]));
	$design_no=trim(str_replace("'","",$data[10]));
	//$search_string = trim($data[0]);
	
	//echo $batch_no; die;
	/*if ($batch_no!='')
	{ 
		$po_ids = return_field_value("id", "pro_batch_create_mst", "batch_no='$batch_no'", "id");
		$aop_batch_cond= "and a.batch_id='$po_ids'"; 
	} 
	else
	{
		$aop_batch_cond="";
	}	
	*/
	if ($design_no!='')
	{ 

		$design_no_cond= "and design_no like '%$design_no%'";

		$design_no_cond1 = "and c.design_no like '%$design_no%'";
		$design_no_cond2 = "and d.design_no like '%$design_no%'";
	} 
	else
	{
		$design_no_cond="";
	}

	if ($batch_no!=''){
		$batch_po_arr=array();
		$aop_batch_cond='';
		$batch_po_arr_sql ="select id from pro_batch_create_mst where batch_no like '%$batch_no%' ";
		$batch_po_sql_res=sql_select($batch_po_arr_sql);
		foreach ($batch_po_sql_res as $row)
		{
			$batch_po_arr[]=$row[csf("id")];
		}
		//unset($order_sql_res);
		$aop_batch_cond=implode(",",$batch_po_arr);
		//echo $batch_id; die; 
		if ($aop_batch_cond!="") $aop_batch_cond=" and a.batch_id in ($aop_batch_cond)"; else $aop_batch_cond="";

	}

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
		if ($aop_ref!='') $aop_ref_cond=" and b.aop_reference = '$aop_ref' ";
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
		if ($aop_ref!='') $aop_ref_cond=" and b.aop_reference like '%$aop_ref%'"; 
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
		if ($aop_ref!='') $aop_ref_cond=" and b.aop_reference like '$aop_ref%'";  
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
		if ($aop_ref!='') $aop_ref_cond=" and b.aop_reference like '%$aop_ref'";
		if($recipe_no!='') $search_recipe_cond=" and a.recipe_no_prefix_num like '%$recipe_no'";
	}
	
	$po_ids='';
	
	/*if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4)|| ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	
	if ($po_ids!="") $po_idsCond=" and c.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	
	*/
	$po_ids=''; $buyer_po_arr=array();
	if($db_type==0) $id_cond="group_concat(b.id) as id";
	else if($db_type==2) $id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as id";
	
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	//echo $po_ids."==".$po_cond."==";
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
					$po_idsCond.=" and ( c.buyer_po_id in ( $ids) ";
					$poIdsCond.=" and ( b.id in ( $ids) ";
				}
				else
				{
					$po_idsCond.=" or  c.buyer_po_id in ( $ids) ";
					$poIdsCond.=" or  b.id in ( $ids) ";
				}
			}
			$po_idsCond.=")";
			$poIdsCond.=")";
		}
		else
		{
			$ids=implode(",",$po_ids);
			$po_idsCond.=" and c.buyer_po_id in ($ids) ";
			$poIdsCond.=" and b.id in ($ids) ";
		}
		//echo $po_ids."==";
	}
	else if($po_ids=="" && (($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5)))
	{
		echo "Not Found"; die;
	}

	/*if(($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2))
	{
		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst b, subcon_ord_dtls c", "b.subcon_job=c.job_no_mst $search_com_cond", "id");
	}
	$spo_idsCond="";
	if(($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2))
	{
		if ( $spo_ids!="") 
			{
				$spo_idsCond=" and c.job_dtls_id in ($spo_ids)";
			} 
			else
			{
			 	echo "Not Found"; die;
			}
	}*/
	$po_sql ="Select a.style_ref_no,a.job_no_prefix_num, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $poIdsCond";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no_prefix_num")];
	}
	unset($po_sql_res);
	
	$spo_ids='';
	/*$order_arr = array();
	$embl_sql ="Select a.subcon_job, b.id, b.order_no from subcon_ord_mst a, subcon_ord_dtls b where a.entry_form=278 and a.subcon_job=b.job_no_mst";
	$embl_sql_res=sql_select($embl_sql);
	foreach ($embl_sql_res as $row)
	{
		$order_arr[$row[csf("id")]]['job']=$row[csf("subcon_job")];
		$order_arr[$row[csf("id")]]['po']=$row[csf("order_no")];
	}
	unset($embl_sql_res);*/

	$order_arr = array();
	$order_sql ="SELECT id,order_no,buyer_style_ref,buyer_po_no,design_no from subcon_ord_dtls where status_active =1 and is_deleted =0 $design_no_cond";
		$order_sql_res=sql_select($order_sql);
	foreach ($order_sql_res as $row)
	{
		$order_arr[$row[csf("id")]]['order_no']=$row[csf("order_no")];
		$order_arr[$row[csf("id")]]['buyer_style_ref']=$row[csf("buyer_style_ref")];
		$order_arr[$row[csf("id")]]['buyer_po_no']=$row[csf("buyer_po_no")];
		$order_arr[$row[csf("id")]]['design_no']=$row[csf("design_no")];
		$order_arr[$row[csf("id")]]['job']=$row[csf("subcon_job")];
	}
	unset($order_sql_res);
	
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$batch_no_arr = return_library_array("select id, batch_no from pro_batch_create_mst", 'id', 'batch_no');
	
	$main_batch_allow = return_field_value("dyeing_fin_bill", "variable_settings_subcon", "company_id =$company_id and variable_list=13 and is_deleted=0 and status_active=1");
	$entry_form_cond='';
	
	if($main_batch_allow==1) $entry_form_cond=" and b.entry_form in(0,281) and b.process_id like '%35%' "; else $entry_form_cond="and a.entry_form =281 ";

	
	if($main_batch_allow==1)
	{
		$sql = "select a.id, a.batch_id, a.recipe_no_prefix_num, a.recipe_no, a.recipe_date, a.color_id, a.job_no, b.entry_form ,b.is_sales, b.sales_order_no, b.booking_no , b.sales_order_id, a.po_id, a.store_id ,b.within_group, d.design_no
		from pro_recipe_entry_mst a, pro_batch_create_mst b, pro_batch_create_dtls c, subcon_ord_dtls d  
		where d.id=a.po_id and a.batch_id=b.id and b.id=c.mst_id and a.entry_form=285 and a.company_id='$company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_recipe_cond $search_com_cond $date_cond $po_idsCond $aop_batch_cond $design_no_cond2 group by   a.id,a.batch_id, a.recipe_no_prefix_num, a.recipe_no, a.recipe_date, a.color_id, a.job_no ,b.entry_form,b.is_sales,b.sales_order_no,b.booking_no ,b.sales_order_id,a.po_id,a.store_id,b.within_group, d.design_no
		order by a.id Desc";
	}
	else
	{
		 $sql = "select a.id, a.batch_id, a.recipe_no_prefix_num, a.recipe_no, a.recipe_date, a.color_id, a.job_no, b.aop_reference,a.po_id, b.job_no_prefix_num, c.id as po_id, c.order_no, c.buyer_po_id, b.entry_form, a.store_id ,b.within_group, c.design_no
		 from pro_recipe_entry_mst a, subcon_ord_mst b, subcon_ord_dtls c 
		 where a.po_id=c.id and b.subcon_job=c.job_no_mst and b.entry_form=278 and a.company_id='$company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $aop_batch_cond $search_recipe_cond $search_com_cond $date_cond $po_idsCond  $aop_ref_cond $design_no_cond1 
		 order by a.id Desc";

		/*$sql = "select a.id, a.recipe_no_prefix_num, a.recipe_no, a.recipe_date, a.color_id, a.job_no, b.aop_reference, b.job_no_prefix_num, c.id as order_id, c.order_no, c.buyer_po_id from pro_recipe_entry_mst a, wo_booking_mst b, wo_booking_dtls c where b.booking_no=c.booking_no and a.company_id='$company_id' and a.buyer_po_id=c.po_break_down_id and c.process=35 and b.booking_type=3 and b.pay_mode in (3,5) and b.lock_another_process=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   order by a.id Desc";
*/
	}
	//echo $sql; 
	if($main_batch_allow==1)
	{
		
		$ord_sql = "select id, booking_no from wo_booking_mst where status_active=1"; //$company_id a.booking_no=b.booking_no and b.process=35 and a.booking_type=3 and a.pay_mode in (3,5) and a.lock_another_process!=1
		$ordArray=sql_select( $ord_sql ); $main_po_arr=array();
		foreach ($ordArray as $row)
		{
			$main_po_arr[$row[csf('id')]]['order'] = $row[csf('booking_no')];
		}
		
	}
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="970" class="rpt_table">
		<thead>
			<th width="35">SL</th>
			<th width="50">Recipe No</th>
			<th width="50">AOP Job No</th>
			<th width="60">Recipe Date</th>
			<th width="100">WO No</th>
			<th width="100">Buyer Job No</th>
            <th width="100">Batch No</th>
            <th width="100">Design No</th>
            <th width="100">Buyer Po</th>
			<th width="100">Buyer Style</th>
			<th width="100">Color</th>
			<th>AOP Ref.<input type="hidden" name="txt_recipe_row_id" id="txt_recipe_row_id" value="<?php echo $recipe_row_id; ?>"/></th>
		</thead>
	</table>
	<div style="width:980px; overflow-y:scroll; max-height:220px;" id="buyer_list_view" align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="970" class="rpt_table" id="tbl_list_search">
			<?
            $i = 1;
            $recipe_row_id = '';
            $hidden_recipe_id = explode(",", $recipe_id);
            $nameArray = sql_select($sql);
            foreach ($nameArray as $selectResult) 
            { 
            	$order_id=array_unique(explode(",",$selectResult[csf("po_id")])); 
            	$buyer_job=''; $buyer_po=''; $buyer_style=''; $buyer_po_id='';
				// $order_no=$order_arr[$selectResult[csf('order_id')]]['order_no'];
				$order_no=$order_arr[$selectResult[csf('po_id')]]['order_no'];
				$design_no=$selectResult[csf('design_no')];
            	foreach($order_id as $val)
				{
					if($selectResult[csf("entry_form")]==0)
					{
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

						if($selectResult[csf("within_group")]==1){
							$buyer_job=$buyer_po_arr[$selectResult[csf("buyer_po_id")]]['job'];
							$buyer_po_id=$selectResult[csf("buyer_po_id")];
							$buyer_po=$buyer_po_arr[$selectResult[csf("buyer_po_id")]]['po'];
							$buyer_style=$buyer_po_arr[$selectResult[csf("buyer_po_id")]]['style'];
						}else{
							// $buyer_po=$order_arr[$selectResult[csf('order_id')]]['buyer_po_no'];
							// $buyer_style=$order_arr[$selectResult[csf('order_id')]]['buyer_style_ref'];

							$buyer_po=$order_arr[$selectResult[csf('po_id')]]['buyer_po_no'];
							$buyer_style=$order_arr[$selectResult[csf('po_id')]]['buyer_style_ref'];
						}
						
						
					}

					/*if($selectResult[csf("entry_form")]==0)
					{
						//echo $val;
						$buyer_job=$buyer_po_arr[$val]['job'];
						$buyer_po=$buyer_po_arr[$val]['po'];
						$buyer_style=$buyer_po_arr[$val]['style'];
						if($order_no=="") $order_no=$main_po_arr[$val]['order']; else $order_no.=", ".$main_po_arr[$val]['order'];
						$buyer_po_id.=$val.",";
					}
					else if($selectResult[csf("entry_form")]==281 || $selectResult[csf("entry_form")]==278)
					{
						
					}*/
				}
				$aop_job=implode(", ",array_unique(explode(", ",$aop_jobs)));
				$order_no=implode(", ",array_unique(explode(", ",$order_no)));

				$buyer_job=implode(", ",array_unique(explode(", ",$buyer_job)));
				$buyer_po=implode(", ",array_unique(explode(", ",$buyer_po)));
				$buyer_style=implode(", ",array_unique(explode(", ",$buyer_style)));

				$buyer_po_id=chop(implode(", ",array_unique(explode(", ",$buyer_po_id))),",");
                if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
    
                if (in_array($selectResult[csf('id')], $hidden_recipe_id)) 
                {
                    if ($recipe_row_id == "") $recipe_row_id = $i; else $recipe_row_id .= "," . $i;
                }
    
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value('<? echo $selectResult[csf('id')].'___'.$selectResult[csf('recipe_no')].'___'.$buyer_job.'___'.$selectResult[csf('po_id')].'___'.$order_no.'___'.$buyer_po_id.'___'.$buyer_po.'___'.$buyer_style.'___'.$selectResult[csf('aop_reference')].'___'.$selectResult[csf('store_id')]; ?>')">
                    <td width="35" align="center"><? echo $i; ?></td>
                    <td width="50" align="center"><? echo $selectResult[csf('recipe_no_prefix_num')]; ?></td>
                    <td width="50" align="center"><? echo $selectResult[csf('job_no_prefix_num')]; ?></td>
                    <td width="60" align="center"><? echo change_date_format($selectResult[csf('recipe_date')]); ?>&nbsp;</td>
                    <td width="100"><? echo $order_no; ?>&nbsp;</td>
                    <td width="100"><? echo $buyer_job; ?>&nbsp;</td>
                    <td width="100"><? echo $batch_no_arr[$selectResult[csf('batch_id')]]; ?>&nbsp;</td>
                    <td width="100"><? echo $design_no; ?>&nbsp;</td>
                    <td width="100"><? echo $buyer_po; ?>&nbsp;</td>
                    <td width="100"><? echo $buyer_style; ?>&nbsp;</td>
                    <td width="100"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?>&nbsp;</p></td>
                    <td><p><? echo $selectResult[csf('aop_reference')]; ?>&nbsp;</p></td>
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
            <th width="80">Item Lot</th>
			<th width="110">Item Category</th>
			<th width="100">Group</th>
			<th width="80">Sub Group</th>
			<th width="180">Item Description</th>
			<th width="50">UOM</th>
			<th width="40">Seq. No.</th>
			<th width="80">Ratio in %</th>
			<th width="80">Req. Qty.</th>
            <th width="50">Adj %</th>
            <th width="80">Adj type</th>
            <th>Tot. Qty.</th>
		</thead>
	</table>
	<div style="width:1120px; overflow-y:scroll; max-height:230px;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table" id="tbl_list_search">
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
               $sql = "select color_id, new_prod_id from pro_recipe_entry_dtls where mst_id=$recipe_id and status_active=1 and is_deleted=0 order by id ASC";
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
                
                $sql = "select id, color_id, prod_id, comments, ratio, seq_no, item_lot from pro_recipe_entry_dtls where mst_id=$recipe_id and status_active=1 and is_deleted=0 order by seq_no ASC";
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
                
                $sql = "select id, item_category_id, item_group_id, sub_group_name, item_description, unit_of_measure, product_name_details from product_details_master where status_active=1 and is_deleted=0 and item_category_id in(5,6,7,22,23) order by id";
                
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
                    <tr bgcolor="#EEEFF0">
                        <td colspan="3" align="left"><b><? echo $k.'.  '. $color_arr[$mcolor_id]; ?></b></td>
                        
                        <td colspan="3" bgcolor="#CCCCFF"><b>Product Name:-<? echo $product_name_details; ?><input type="hidden" name="hidd_nprod_id[]" id="hidd_nprod_id_<? echo $k; ?>" value="<? echo $new_prod_id; ?>"></b></td>
                        <td colspan="3" align="right" bgcolor="#CCFFFF"><b>Paste Weight</b></td>
                        <td colspan="4" bgcolor="#CCFFFF">&nbsp;<input type="text" name="txt_past_weight[]" id="txt_past_weight_<? echo $k; ?>" class="text_boxes_numeric" value="<? echo $past_weight; ?>" style="width:60px;" onChange="fnc_req_calculate(1,<? echo $k; ?>);" ><input type="hidden" name="multicolor_id[]" id="multicolor_id_<? echo $k; ?>" value="<? echo $mcolor_id; ?>"></td>
                    </tr>
                    <?
                    foreach ($color_remark_array[$mcolor_id] as $rid=>$exdata) 
                    {
                        if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                        $prod_id=0; $exprod_data=""; $item_category_id=0; $item_group_id=0; $sub_group_name=""; $item_description=""; $unit_of_measure=0;
                        $prod_id=$exdata['prod_id'];
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
                            <td width="50" align="center" id="product_id_<? echo $k.'_'.$i; ?>"><? echo $prod_id; ?></td>
                            <td width="80" align="center" id="td_lot_<? echo $k.'_'.$i; ?>"><? echo $exdata['item_lot']; ?></td>
                            <td width="110" style="word-break:break-all;"><? echo $item_category[$item_category_id]; ?>&nbsp;<input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $k.'_'.$i; ?>" value="<? echo $item_category_id; ?>"></td>
                            <td width="100" id="item_group_id_<? echo $k.'_'.$i; ?>" style="word-break:break-all;"><? echo $item_group_arr[$item_group_id]; ?>&nbsp;<input type="hidden" name="txt_group_id[]" id="txt_group_id_<? echo $k.'_'.$i; ?>" value="<? echo $item_group_id; ?>"></td>
                            <td width="80" id="sub_group_name_<? echo $k.'_'.$i; ?>" style="word-break:break-all;"><? echo $sub_group_name; ?>&nbsp;</td>
                            <td width="180" id="item_description_<? echo $k.'_'.$i; ?>" style="word-break:break-all;"><? echo $item_description; ?>&nbsp;</td>
                            <td width="50" align="center" id="uom_<? echo $k.'_'.$i; ?>" style="word-break:break-all;"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
                            <td width="40" align="center" id="seq_no_<? echo $k.'_'.$i; ?>" style="word-break:break-all;"><? echo $exdata['seq_no']; ?></td>
                            <td width="80" align="right" id="ratio_<? echo $k.'_'.$i; ?>"><? echo $exdata['ratio']; ?></td>
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
                        $tot_ratio+=$exdata['ratio'];
                        $grand_tot_ratio+=$exdata['ratio'];
                        $i++;
                    }
                    ?>
                    <tr class="tbl_bottom">
                        <td align="right" colspan="8"><strong>Color Total</strong>
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
                    <td align="right" colspan="9"><strong>Grand Total</strong>
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
	//echo "10**".$operation."mmm" ; die;
	if($operation == 0)  // Insert Here
	{
		//echo "10**".$operation."rrr" ; die;
		$con = connect();
		if ($db_type == 0) 
		{
			mysql_query("BEGIN");
		}

		$mst_id = ""; $requ_no = "";
		
		$batch_entry_form = return_field_value("b.entry_form", "pro_recipe_entry_mst a, pro_batch_create_mst b", "a.batch_id=b.id and a.id=$txt_recipe_id", "entry_form");
		if($batch_entry_form ==0) $is_main_batch=1; else $is_main_batch=0;
		//echo "10**".$txtbuyerPoId; die;

		if (str_replace("'", "", $update_id) == "")
		{
			if ($db_type == 0) $year_cond = "YEAR(insert_date)";
			else if ($db_type == 2) $year_cond = "to_char(insert_date,'YYYY')";
			else $year_cond = "";//defined Later

			$new_requ_no = explode("*", return_mrr_number(str_replace("'", "", $cbo_company_name), '', 'AOPDCR', date("Y", time()), 5, "select requ_no_prefix, requ_prefix_num from dyes_chem_issue_requ_mst where company_id=$cbo_company_name and entry_form=290 and $year_cond=" . date('Y', time()) . " order by id desc ", "requ_no_prefix", "requ_prefix_num"));
			$id = return_next_id("id", "dyes_chem_issue_requ_mst", 1);
			$field_array = "id, requ_no, requ_no_prefix, requ_prefix_num, company_id, location_id, requisition_date, requisition_basis, recipe_id, order_id, job_no, buyer_po_id,is_main_batch, inserted_by, insert_date, entry_form, store_id";
			$data_array = "(" . $id . ",'" . $new_requ_no[0] . "','" . $new_requ_no[1] . "'," . $new_requ_no[2] . "," . $cbo_company_name . "," . $cbo_location_name . "," . $txt_requisition_date . "," . $cbo_receive_basis . "," . $txt_recipe_id . "," . $txt_order_id . "," . $txt_job_no . "," . $txtbuyerPoId . "," . $is_main_batch . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',290," . $cbo_store_name . ")";

			$mst_id = $id;
			$requ_no = $new_requ_no[0];
		} 
		else 
		{
			$field_array_up = "location_id*requisition_date*requisition_basis*recipe_id*order_id*job_no*buyer_po_id*is_main_batch*updated_by*update_date";
			$data_array_up = "" . $cbo_location_name . "*" . $txt_requisition_date . "*" . $cbo_receive_basis . "*" . $txt_recipe_id . "*" . $txt_order_id . "*" . $txt_job_no . "*" . $txtbuyerPoId . "*" . $is_main_batch . "*" . $user_id . "*'" . $pc_date_time . "'";
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
		$field_array_dtls = "id, mst_id, requ_no, recipe_id, multicolor_id, nprod_id, past_weight, product_id, item_category, ratio, required_qnty, adjust_percent, adjust_type, req_qny_edit, seq_no, inserted_by, insert_date, store_id, item_lot ";
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
	
				if ($data_array_dtls != "") $data_array_dtls .= ",";
				$data_array_dtls .= "(".$id_dtls.",".$mst_id.",'".$requ_no."',".$txt_recipe_id.",".$$multicolor_id.",".$$hidd_nprod_id.",".$$txt_past_weight.",".$$txt_prod_id.",".$$txt_item_cat.",'".trim($txt_ratio)."',".$$txt_reqn_qnty.",".$$txt_adj_per.",".$$cbo_adj_type.",".$$txt_tot_qnty.",".$$txt_seq_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'," . $cbo_store_name . ",".$$product_lot.")";
				$id_dtls = $id_dtls + 1;
				$nprod_qty_arr[str_replace("'", "", $$hidd_nprod_id)]+=str_replace("'", "", $$txt_reqn_qnty);
			}
		}
		//echo "10**";
		//print_r($data_array_dtls); die;
		$flag=1;
		if (str_replace("'", "", $update_id) == "") 
		{
			//echo  "10**INSERT INTO dyes_chem_issue_requ_mst (".$field_array.") VALUES ".$data_array."";
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
		}
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
	else if($operation == 1)  // Update Here
	{
		//echo "10**".$operation."sss"; die; 
		$con = connect();
		if ($db_type == 0) 
		{
			mysql_query("BEGIN");
		}
		
		
		
		$updateid=str_replace("'", "", $update_id);
		$req_no=str_replace("'", "", $txt_req_no);
		$mrrsql= sql_select("select  issue_number,req_no, req_id  from  inv_issue_master where req_id=$updateid  and  entry_form = 308 and  status_active=1 and  is_deleted=0");
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
					echo "50**Update restricted, This Information is used in another Table."."  Requisition Number ".$do_rcv_number=str_replace("'","",$all_req_no)."  Issue Number ".$do_rcv_number=str_replace("'","",$all_issue_id); 
					disconnect($con); 
					oci_rollback($con); die;
				}
			}
		//echo "10**1"; die; 
		//echo "10**".str_replace("'", "", $is_apply_last_update); die;
		$last_update_arr = return_library_array("select recipe_id, is_apply_last_update from dyes_chem_requ_recipe_att where mst_id=$update_id", "recipe_id", "is_apply_last_update");
		$is_apply_last_update = str_replace("'", "", $is_apply_last_update);

		$batch_entry_form = return_field_value("b.entry_form", "pro_recipe_entry_mst a, pro_batch_create_mst b", "a.batch_id=b.id and a.id=$txt_recipe_id", "entry_form");
		if($batch_entry_form ==0) $is_main_batch=1; else $is_main_batch=0;
		if ($is_apply_last_update == 1) 
		{
			$field_array_up = "location_id*requisition_date*requisition_basis*recipe_id*is_apply_last_update*order_id*job_no*buyer_po_id*is_main_batch*updated_by*update_date";
			$data_array_up = "" . $cbo_location_name . "*" . $txt_requisition_date . "*" . $cbo_receive_basis . "*" . $txt_recipe_id . "*0*" . $txt_order_id . "*" . $txt_job_no . "*" . $txtbuyerPoId . "*" . $is_main_batch . "*" . $user_id . "*'" . $pc_date_time . "'";
		} 
		else 
		{
			$field_array_up = "location_id*requisition_date*requisition_basis*recipe_id*order_id*job_no*buyer_po_id*is_main_batch*updated_by*update_date";
			$data_array_up = "" . $cbo_location_name . "*" . $txt_requisition_date . "*" . $cbo_receive_basis . "*" . $txt_recipe_id . "*" . $txt_order_id . "*" . $txt_job_no . "*" . $txtbuyerPoId . "*" . $is_main_batch . "*" . $user_id . "*'" . $pc_date_time . "'";
		}
		//echo "10**".$data_array_up;
		//echo "10**2"; 
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
		$field_array_dtls = "id, mst_id, requ_no, recipe_id, multicolor_id, nprod_id, past_weight, product_id, item_category, ratio, required_qnty, adjust_percent, adjust_type, req_qny_edit, seq_no, inserted_by, insert_date, store_id, item_lot ";
		$k=0;//echo "10**3"; 
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
	
				if ($data_array_dtls != "") $data_array_dtls .= ",";
				$data_array_dtls .= "(".$id_dtls.",".$mst_id.",'".$requ_no."',".$txt_recipe_id.",".$$multicolor_id.",".$$hidd_nprod_id.",".$$txt_past_weight.",".$$txt_prod_id.",".$$txt_item_cat.",'".trim($txt_ratio)."',".$$txt_reqn_qnty.",".$$txt_adj_per.",".$$cbo_adj_type.",".$$txt_tot_qnty.",".$$txt_seq_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'," . $cbo_store_name . ",".$$product_lot.")";
				$id_dtls = $id_dtls + 1;
				$nprod_qty_arr[str_replace("'", "", $$hidd_nprod_id)]+=str_replace("'", "", $$txt_reqn_qnty);
				//echo $colorrow.'='; 
			}
		}
		$flag=1;//echo "10**4"; 
		$rID = sql_update("dyes_chem_issue_requ_mst", $field_array_up, $data_array_up, "id", $update_id, 0);
		//echo "10**aa".$rID; die;
		//10**310**nnn1
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		$delete_att = execute_query("delete from dyes_chem_requ_recipe_att where mst_id=$update_id", 0);
		if($delete_att==1 && $flag==1) $flag=1; else $flag=0;
		$delete_dtls = execute_query("delete from dyes_chem_issue_requ_dtls where mst_id=$update_id", 0);
		if($delete_dtls==1 && $flag==1) $flag=1; else $flag=0;
		$rID_att = sql_insert("dyes_chem_requ_recipe_att", $field_array_att, $data_array_att, 0);
		if($rID_att==1 && $flag==1) $flag=1; else $flag=0;

		if ($data_array_dtls != "") 
		{
			//echo "10**INSERT INTO dyes_chem_issue_requ_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls."";
			$rID_dtls = sql_insert("dyes_chem_issue_requ_dtls", $field_array_dtls, $data_array_dtls, 0);
			if($rID_dtls==1 && $flag==1) $flag=1; else $flag=0;
		}
		//echo "10**5"; 
		if ($flag==1)
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
		}
		//echo "10**6"; 
		//echo "10**".$rID.'='.$rID_att.'='.$rID_dtls.'='.$delete_att.'='.$delete_dtls.'='.$flag.'='."zdfdf"; die;

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
	
	$order_arr = array();
	$embl_sql ="Select a.subcon_job, a.within_group, b.id,a.aop_reference, b.order_no, b.buyer_buyer, b.buyer_po_no, b.buyer_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.entry_form=278 and a.subcon_job=b.job_no_mst";
	$embl_sql_res=sql_select($embl_sql);
	foreach ($embl_sql_res as $row) 
	{
		$order_arr[$row[csf("id")]]['job']=$row[csf("subcon_job")];
		$order_arr[$row[csf("id")]]['po']=$row[csf("order_no")];
		$order_arr[$row[csf("id")]]['aop_reference']=$row[csf("aop_reference")];
		$order_arr[$row[csf("id")]]['within_group']=$row[csf("within_group")];
		$order_arr[$row[csf("id")]]['buyer_buyer'] =$row[csf("buyer_buyer")];
		$order_arr[$row[csf("id")]]['buyer_po_no'] =$row[csf("buyer_po_no")];
		$order_arr[$row[csf("id")]]['buyer_style_ref'] =$row[csf("buyer_style_ref")];
	}
	unset($embl_sql_res);
	
	$buyer_po_arr=array();
	
		/*$sql= "select a.id, a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, $date_diff_cond as date_diff, $year_select_cond as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer ".
	*/
	
	/*$po_sql ="Select a.style_ref_no, b.id, b.po_number,a.buyer_name,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['buyerBuyer']=$row[csf("buyer_name")];
		$buyer_po_arr[$row[csf("id")]]['internalRef']=$row[csf("grouping")];	
	}
	unset($po_sql_res);*/

	$buyer_po_arr=array();
	$po_sql ="Select b.id,a.job_no,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['internalRef']=$row[csf("grouping")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
	}
	unset($po_sql_res);
	/*$sql_mst = "select  id, requ_no, requ_no_prefix, requ_prefix_num, company_id, location_id, requisition_date, requisition_basis, recipe_id, order_id, job_no, buyer_po_id, entry_form from dyes_chem_issue_requ_mst where id='$data[1]'";
	//echo $sql_mst;
	
	$recipe_id=$dataArray[0][csf('recipe_id')];
	
	$recipe_arr=array();
	if($recipe_id!="")
	{*/
		//echo "select  a.id, a.recipe_no ,a.within_group, a.po_ids, a.buyer_id, a.buyer_po_ids, a.color_id, a.batch_id, b.batch_no,b.batch_weight, c.id as req_id,c.requ_no,c.requ_no_prefix,c.requ_prefix_num,c.company_id,c.location_id,c.requisition_date,c.requisition_basis,c.recipe_id,c.order_id,c.job_no,c.buyer_po_id,c.entry_form from pro_recipe_entry_mst a, pro_batch_create_mst b , dyes_chem_issue_requ_mst c where  a.batch_id=b.id and c.recipe_id=a.id and c.id='$data[1]' and a.status_active=1 and b.status_active=1";
		//echo 
		
		//unset($dataArray);
	/*}
*/
	//$dataArray = sql_select($sql_mst); $party_name="";

	$dataArray = sql_select("select  a.id, a.recipe_no ,b.within_group, a.po_id, a.buyer_id, a.buyer_po_ids, a.color_id, a.batch_id, b.batch_no,b.batch_weight, c.id as req_id,c.requ_no,c.requ_no_prefix,c.requ_prefix_num,c.company_id,c.location_id,c.requisition_date,c.requisition_basis,c.recipe_id,c.order_id,c.job_no,c.buyer_po_id,c.entry_form from pro_recipe_entry_mst a, pro_batch_create_mst b , dyes_chem_issue_requ_mst c where  a.batch_id=b.id and c.recipe_id=a.id and c.id='$data[1]' and a.status_active=1 and b.status_active=1");
	foreach ($dataArray as $row)
	{
		$party_name='';
		if($row[csf('within_group')]==1) $party_name=$company_library[$row[csf('buyer_id')]];
		else if($row[csf('within_group')]==2) $party_name=$buyer_library[$row[csf('buyer_id')]];
		
		$recipe_arr[$row[csf('id')]]['party']=$party_name; 
		$recipe_arr[$row[csf('id')]]['color']=$row[csf('color_id')]; 
		$recipe_arr[$row[csf('id')]]['recipe_no']=$row[csf('recipe_no')]; 
		$recipe_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')]; 
		$recipe_arr[$row[csf('id')]]['batch_weight']=$row[csf('batch_weight')]; 
	}
	$job_no=$po_no=$buyer_po_no=$buyer_style_ref=$within_group=$buyer_buyer=$aop_ref='';
	$order_id=array_unique(explode(",",$dataArray[0][csf('po_id')]));
	$recipe_id=$dataArray[0][csf('recipe_id')];
	foreach($order_id as $val)
	{
		//echo $val.'==';
		if($job_no=="") $job_no=$order_arr[$val]['job']; else $job_no.=", ".$order_arr[$val]['job'];
		if($po_no=="") $po_no=$order_arr[$val]['po']; else $po_no.=", ".$order_arr[$val]['po'];
		if($buyer_po_no=="") $buyer_po_no=$order_arr[$val]['buyer_po_no']; else $buyer_po_no.=", ".$order_arr[$val]['buyer_po_no'];
		if($buyer_style_ref=="") $buyer_style_ref=$order_arr[$val]['buyer_style_ref']; else $buyer_style_ref.=", ".$order_arr[$val]['buyer_style_ref'];
		if($aop_ref=="") $aop_ref=$order_arr[$val]['aop_reference']; else $aop_ref.=", ".$order_arr[$val]['aop_reference'];

		if($dataArray[0][csf('within_group')]==1) 
		{
			if($buyer_buyer=="") $buyer_buyer=$buyer_library[$order_arr[$val]['buyer_buyer']]; else $buyer_buyer.=", ".$buyer_library[$order_arr[$val]['buyer_buyer']];
		}
		else
		{
			if($buyer_buyer=="") $buyer_buyer=$order_arr[$val]['buyer_buyer']; else $buyer_buyer.=", ".$order_arr[$val]['buyer_buyer'];
		}
	}
	if($dataArray[0][csf('buyer_po_ids')]=='') $buyer_po_ids=$dataArray[0][csf('buyer_po_id')]; else $buyer_po_ids=$dataArray[0][csf('buyer_po_ids')];
	$buyer_po_id=array_unique(explode(",",$buyer_po_ids));

	foreach($buyer_po_id as $val)
	{
		if($internalRef=="") $internalRef=$buyer_po_arr[$val]['internalRef']; else $internalRef.=", ".$buyer_po_arr[$val]['internalRef'];
		if($buyer_job=="") $buyer_job=$buyer_po_arr[$val]['job']; else $buyer_job.=", ".$buyer_po_arr[$val]['job'];
	}
	//echo "<pre>";
	//print_r($all_ref_arr);
	$job_no = implode(",", array_unique(explode(", ",$job_no)));
	$po_no = implode(",", array_unique(explode(", ",$po_no)));
	$buyer_po_no = implode(",", array_unique(explode(", ",$buyer_po_no)));
	$buyer_style_ref = implode(",", array_unique(explode(", ",$buyer_style_ref)));
	$buyer_buyer = implode(",", array_unique(explode(", ",$buyer_buyer)));
	$aop_ref = implode(",", array_unique(explode(", ",$aop_ref)));
	$internalRef = implode(",", array_unique(explode(", ",$internalRef)));
	$buyer_job = implode(",", array_unique(explode(", ",$buyer_job)));
	//print_r($recipe_arr);
	?>
    <div style="width:1100px; font-size:6px">
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
                            <td align="center" style="font-size:18px"><strong><? echo $data[2]; ?></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        </br>
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
                <td><strong>AOP Job No:</strong></td>
                <td><? echo $job_no; ?></td>
               	<td><strong>Work Order:</strong></td>
                <td><? echo $po_no; ?></td>
                <td><strong>Color:</strong></td>
                <td><? echo $color_arr[$recipe_arr[$recipe_id]['color']]; ?></td>
            </tr>
            <tr>
            	<td><strong>Job No:</strong></td>
                <td> <? echo $dataArray[0][csf('job_no')]; ?></td>
                <td><strong>Buyer Po:</strong></td>
                <td> <? echo $buyer_po_no; ?></td>
                <td><strong>Buyer Style:</strong></td>
                <td><? echo $buyer_style_ref; ?></td>
            </tr>
           	<tr>
            	<td><strong>AOP Ref.:</strong></td>
                <td><? echo $aop_ref; ?></td>
                <td><strong>Internal Ref.</strong></td>
                <td width="150" id="ref_td"> <? echo $internalRef; ?></td>
                <td><strong>Cust. Buyer:</strong>
                </td><td id="buyer_td"><?  echo $buyer_buyer; ?> ;<td>
            </tr>
            <tr>
                <td><strong>Batch No.:</strong></td>
                <td> <? echo $recipe_arr[$recipe_id]['batch_no']; ?></td>
                <td><strong>Batch Weight:</strong></td>
                <td><? echo $recipe_arr[$recipe_id]['batch_weight']; ?></td>
                <td><strong>Recipe No.:</strong></td>
                <td> <? echo $recipe_arr[$recipe_id]['recipe_no']; ?></td>
            </tr>
        </table>
        <br>
        <div style="width:100%;">
            <table align="right" cellspacing="0" width="1100" border="1" rules="all" class="rpt_table">
                <thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
                    <th width="30">SL</th>
                    <th width="100">Item Cat.</th>
                    <th width="100">Item Group</th>
                    <th width="80">Item Lot</th>
                    <th width="80">Sub Group</th>
                    <th width="180">Item Description</th>
                    <th width="50">UOM</th>
                    <th width="50">Seq. No.</th>
                    <th width="80">Ratio in %</th>
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
				$sql = "select color_id from pro_recipe_entry_dtls where mst_id=$recipe_id and status_active=1 and is_deleted=0 order by id ASC";
				$nameArray = sql_select($sql); $tot_rows=0; $prodIds='';
				foreach ($nameArray as $row) 
				{
					if (!in_array($row[csf("color_id")], $multicolor_array)) 
					{
						$multicolor_array[] = $row[csf("color_id")];
					}
				}
				unset($nameArray);
				
				$sql = "select id, color_id, prod_id, comments, ratio, seq_no, item_lot from pro_recipe_entry_dtls where mst_id=$recipe_id and status_active=1 and is_deleted=0 order by seq_no ASC";
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
				
				$sql = "select id, item_category_id, item_group_id, sub_group_name, item_description, unit_of_measure from product_details_master where status_active=1 and is_deleted=0 and company_id='$com_id' $prodIds_cond and item_category_id in(5,6,7,22,23) order by id";
				
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
					?>
                    <tr bgcolor="#EEEFF0">
                        <td colspan="6" align="left"><b><? echo $k.'.  '. $color_arr[$mcolor_id].';'; ?></b></td>
                        <td colspan="7" align="left"><b>Paste Weight:- <? echo $pastweight_arr[$mcolor_id]; ?></b></td>
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
                            <td align="center"><? echo $exdata['item_lot']; ?></td>
                            <td><p><? echo $sub_group_name; ?>&nbsp;</p></td>
                            <td><p><? echo $item_description; ?>&nbsp;</p></td>
                            <td align="center"><p><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</p></td>
                            <td align="center"><? echo $exdata['seq_no']; ?></td>
                            <td align="right"><? echo number_format($exdata['ratio'], 2, '.', ''); ?></td>
                            <td align="right"><? echo number_format($reqsn_qty, 4, '.', ''); ?></td>
                            <td align="right"><? echo number_format($adjust_percent, 4, '.', ''); ?></td>
                            <td><p><? echo $increase_decrease[$adjust_type]; ?>&nbsp;</p></td>
                            <td align="right"><? echo number_format($req_qny_edit, 4, '.', ''); ?></td>
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
                        <td align="right" colspan="8"><strong>Color Total</strong></td>
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
                    <td align="right" colspan="8"><strong>Grand Total</strong></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($grand_tot_req, 4, '.', ''); ?>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($grand_tot_edit_req, 4, '.', ''); ?>&nbsp;</td>
                </tr>
            </table>
            <br>
			<?
			echo signature_table(169, $com_id, "1100px");
			?>
        </div>
    </div>
	<?
	exit();
}


if ($action == "print_requisition_item_wise_summary") 
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	$imge_arr=return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	
	$order_arr = array();
	$embl_sql ="Select a.subcon_job, a.within_group, b.id,a.aop_reference, b.order_no, b.buyer_buyer, b.buyer_po_no, b.buyer_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.entry_form=278 and a.subcon_job=b.job_no_mst";
	$embl_sql_res=sql_select($embl_sql);
	foreach ($embl_sql_res as $row) 
	{
		$order_arr[$row[csf("id")]]['job']=$row[csf("subcon_job")];
		$order_arr[$row[csf("id")]]['po']=$row[csf("order_no")];
		$order_arr[$row[csf("id")]]['aop_reference']=$row[csf("aop_reference")];
		$order_arr[$row[csf("id")]]['within_group']=$row[csf("within_group")];
		$order_arr[$row[csf("id")]]['buyer_buyer'] =$row[csf("buyer_buyer")];
		$order_arr[$row[csf("id")]]['buyer_po_no'] =$row[csf("buyer_po_no")];
		$order_arr[$row[csf("id")]]['buyer_style_ref'] =$row[csf("buyer_style_ref")];
	}
	unset($embl_sql_res);
	
	$buyer_po_arr=array();
	
		

	$buyer_po_arr=array();
	$po_sql ="Select b.id,a.job_no,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['internalRef']=$row[csf("grouping")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
	}
	unset($po_sql_res);
	

	$dataArray = sql_select("select  a.id, a.recipe_no ,b.within_group, a.po_id, a.buyer_id, a.buyer_po_ids, b.color_id, a.batch_id, b.batch_no,b.batch_weight, c.id as req_id,c.requ_no,c.requ_no_prefix,c.requ_prefix_num,c.company_id,c.location_id,c.requisition_date,c.requisition_basis,c.recipe_id,c.order_id,c.job_no,c.buyer_po_id,c.entry_form from pro_recipe_entry_mst a, pro_batch_create_mst b , dyes_chem_issue_requ_mst c where  a.batch_id=b.id and c.recipe_id=a.id and c.id='$data[1]' and a.status_active=1 and b.status_active=1");
	foreach ($dataArray as $row)
	{
		$party_name='';
		if($row[csf('within_group')]==1) $party_name=$company_library[$row[csf('buyer_id')]];
		else if($row[csf('within_group')]==2) $party_name=$buyer_library[$row[csf('buyer_id')]];
		
		$recipe_arr[$row[csf('id')]]['party']=$party_name; 
		$recipe_arr[$row[csf('id')]]['color']=$row[csf('color_id')]; 
		$recipe_arr[$row[csf('id')]]['recipe_no']=$row[csf('recipe_no')]; 
		$recipe_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')]; 
		$recipe_arr[$row[csf('id')]]['batch_weight']=$row[csf('batch_weight')]; 
	}
	$job_no=$po_no=$buyer_po_no=$buyer_style_ref=$within_group=$buyer_buyer=$aop_ref='';
	$order_id=array_unique(explode(",",$dataArray[0][csf('po_id')]));
	$recipe_id=$dataArray[0][csf('recipe_id')];
	foreach($order_id as $val)
	{
		//echo $val.'==';
		if($job_no=="") $job_no=$order_arr[$val]['job']; else $job_no.=", ".$order_arr[$val]['job'];
		if($po_no=="") $po_no=$order_arr[$val]['po']; else $po_no.=", ".$order_arr[$val]['po'];
		if($buyer_po_no=="") $buyer_po_no=$order_arr[$val]['buyer_po_no']; else $buyer_po_no.=", ".$order_arr[$val]['buyer_po_no'];
		if($buyer_style_ref=="") $buyer_style_ref=$order_arr[$val]['buyer_style_ref']; else $buyer_style_ref.=", ".$order_arr[$val]['buyer_style_ref'];
		if($aop_ref=="") $aop_ref=$order_arr[$val]['aop_reference']; else $aop_ref.=", ".$order_arr[$val]['aop_reference'];

		if($dataArray[0][csf('within_group')]==1) 
		{
			if($buyer_buyer=="") $buyer_buyer=$buyer_library[$order_arr[$val]['buyer_buyer']]; else $buyer_buyer.=", ".$buyer_library[$order_arr[$val]['buyer_buyer']];
		}
		else
		{
			if($buyer_buyer=="") $buyer_buyer=$order_arr[$val]['buyer_buyer']; else $buyer_buyer.=", ".$order_arr[$val]['buyer_buyer'];
		}
	}
	if($dataArray[0][csf('buyer_po_ids')]=='') $buyer_po_ids=$dataArray[0][csf('buyer_po_id')]; else $buyer_po_ids=$dataArray[0][csf('buyer_po_ids')];
	$buyer_po_id=array_unique(explode(",",$buyer_po_ids));

	foreach($buyer_po_id as $val)
	{
		if($internalRef=="") $internalRef=$buyer_po_arr[$val]['internalRef']; else $internalRef.=", ".$buyer_po_arr[$val]['internalRef'];
		if($buyer_job=="") $buyer_job=$buyer_po_arr[$val]['job']; else $buyer_job.=", ".$buyer_po_arr[$val]['job'];
	}
	//echo "<pre>";
	//print_r($all_ref_arr);
	$job_no = implode(",", array_unique(explode(", ",$job_no)));
	$po_no = implode(",", array_unique(explode(", ",$po_no)));
	$buyer_po_no = implode(",", array_unique(explode(", ",$buyer_po_no)));
	$buyer_style_ref = implode(",", array_unique(explode(", ",$buyer_style_ref)));
	$buyer_buyer = implode(",", array_unique(explode(", ",$buyer_buyer)));
	$aop_ref = implode(",", array_unique(explode(", ",$aop_ref)));
	$internalRef = implode(",", array_unique(explode(", ",$internalRef)));
	$buyer_job = implode(",", array_unique(explode(", ",$buyer_job)));
	//print_r($recipe_arr);
	?>
    <div style="width:1000px; font-size:6px">
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
                            <td align="center" style="font-size:18px"><strong><? echo $data[2]; ?></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        </br>
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
                <td><strong>AOP Job No:</strong></td>
                <td><? echo $job_no; ?></td>
               	<td><strong>Work Order:</strong></td>
                <td><? echo $po_no; ?></td>
                <td><strong>Batch Color:</strong></td>
                <td><? echo $color_arr[$recipe_arr[$recipe_id]['color']]; ?></td>
            </tr>
            <tr>
            	<td><strong>Job No:</strong></td>
                <td> <? echo $dataArray[0][csf('job_no')]; ?></td>
                <td><strong>Buyer Po:</strong></td>
                <td> <? echo $buyer_po_no; ?></td>
                <td><strong>Buyer Style:</strong></td>
                <td><? echo $buyer_style_ref; ?></td>
            </tr>
           	<tr>
            	<td><strong>AOP Ref.:</strong></td>
                <td><? echo $aop_ref; ?></td>
                <td><strong>Internal Ref.</strong></td>
                <td width="150" id="ref_td"> <? echo $internalRef; ?></td>
                <td><strong>Cust. Buyer:</strong>
                </td><td id="buyer_td"><?  echo $buyer_buyer; ?> ;<td>
            </tr>
            <tr>
                <td><strong>Batch No.:</strong></td>
                <td> <? echo $recipe_arr[$recipe_id]['batch_no']; ?></td>
                <td><strong>Batch Weight:</strong></td>
                <td><? echo $recipe_arr[$recipe_id]['batch_weight']; ?></td>
                <td><strong>Recipe No.:</strong></td>
                <td> <? echo $recipe_arr[$recipe_id]['recipe_no']; ?></td>
            </tr>
        </table>
        <br>
        <div style="width:100%;">
            <table align="left" cellspacing="0" width="1000" border="1" rules="all" class="rpt_table">
                <thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
                    <th width="30">SL</th>
                    <th width="120">Item Cat.</th>
                    <th width="100">Item Group</th>
                    <th width="100">Item Lot</th>
                    <th width="100">Sub Group</th>
                    <th width="180">Item Description</th>
                    <th width="50">UOM</th>
                    <th width="100">Req. Qty.</th>
                </thead>
				<?
				$mst_id = $data[1];
				$com_id = $data[0];
				
				$sql = "select id, color_id, prod_id, comments, ratio, seq_no, item_lot from pro_recipe_entry_dtls where mst_id=$recipe_id and status_active=1 and is_deleted=0 order by seq_no ASC";
				$nameArray = sql_select($sql); $tot_rows=0; $prodIds='';
				foreach ($nameArray as $row) 
				{
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
				$sql = "select id, item_category_id, item_group_id, sub_group_name, item_description, unit_of_measure from product_details_master where status_active=1 and is_deleted=0 and company_id='$com_id' $prodIds_cond and item_category_id in(5,6,7,22,23) order by id";
				//echo $sql;
				$sql_result = sql_select($sql);

				foreach ($sql_result as $row) 
				{
					$prod_data_array[$row[csf("id")]]= $row[csf("item_category_id")] . "**" . $row[csf("item_group_id")] . "**" . $row[csf("sub_group_name")] . "**" . $row[csf("item_description")] . "**" . $row[csf("unit_of_measure")];
				}
				unset($sql_result);
				
					 
					$reqsn_data_arr2=array();
					$reqsn_sql2="select product_id,item_category,item_lot,sum(required_qnty) as req_qny_edit from dyes_chem_issue_requ_dtls where mst_id='$mst_id' and status_active=1 and is_deleted=0 group by product_id,item_category,item_lot";
				//echo $reqsn_sql2;
					$reqsn_sql_res2 = sql_select($reqsn_sql2);
					$i=1; $grand_tot_req=0;
					foreach ($reqsn_sql_res2 as $exdata) 
					{
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						$prod_id=0; $exprod_data=""; $item_category_id=0; $item_group_id=0; $sub_group_name=""; $item_description=""; $unit_of_measure=0; $reqsn_qty=0;
						$prod_id=$exdata[csf('product_id')];
						$exprod_data=explode("**",$prod_data_array[$prod_id]);
						$item_category_id=$exprod_data[0]; $item_group_id=$exprod_data[1]; $sub_group_name=$exprod_data[2]; $item_description=$exprod_data[3]; $unit_of_measure=$exprod_data[4];
						$reqsn_qty=$exdata[csf('req_qny_edit')];
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>">
                            <td><? echo $i.''; ?></td>
                            <td><p><? echo $item_category[$item_category_id]; ?>&nbsp;</p></td>
                            <td><p><? echo $item_group_arr[$item_group_id]; ?>&nbsp;</p></td>
                            <td align="center"><? echo $exdata[csf('item_lot')]; ?></td>
                            <td><p><? echo $sub_group_name; ?>&nbsp;</p></td>
                            <td><p><? echo $item_description; ?>&nbsp;</p></td>
                            <td align="center"><p><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</p></td>
                            <td align="right"><? echo number_format($reqsn_qty, 4, '.', ''); ?></td>
                        </tr>
						<?
						$grand_tot_req+=$reqsn_qty;
						$i++;
					}
				?>
                <tr class="tbl_bottom">
                    <td align="right" colspan="7"><strong>Grand Total</strong></td>
                    <td align="right"><? echo number_format($grand_tot_req, 4, '.', ''); ?>&nbsp;</td>
                </tr>
            </table>
            <br>
			<?
			echo signature_table(169, $com_id, "1100px");
			?>
        </div>
    </div>
	<?
	exit();
}
?>
