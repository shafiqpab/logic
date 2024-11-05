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

if ($action == "load_drop_down_location") 
{
	$data = explode("_", $data);
	echo create_drop_down("cbo_location", 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-Select Location-", 0, "load_drop_down('requires/wash_recipe_entry_controller', document.getElementById('cbo_company_id').value+'__'+this.value+'__'+document.getElementById('cbo_recipe_for').value, 'load_drop_down_store', 'store_td');");
	exit();
}

if ($action == "load_drop_down_buyer") 
{
	$exdata=explode("_",$data);
	if($exdata[1]==1)
	{
		echo create_drop_down( "cbo_buyer_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Party--", "", "",1);
	}
	else if($exdata[1]==2)
	{
		echo create_drop_down("cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$exdata[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buy.buyer_name", "id,buyer_name", 1, "--Select Party--", $selected, "", 1);
	}
	exit();
}

if ($action=="load_drop_down_store")
{
	$exdata=explode("__",$data);
	
	if($exdata[1]!=0) $store_location_cond=" and b.store_location_id='$exdata[1]'"; else $store_location_cond="";
	if($exdata[2]==3)
	{
		echo create_drop_down( "cbo_store_id", 70,"select a.id, a.store_name from lib_store_location a, lib_store_location_category b  where a.id=b.store_location_id and a.is_deleted=0 and a.company_id='$exdata[0]' and a.status_active=1 and b.category_type not in (5,6,7) $store_location_cond $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-Store-", $selected, "","");
	}
	else
	{
		echo create_drop_down( "cbo_store_id", 70, $blank_array,"", "1", "-Store-", 0, "","1","","","","","","" );	
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

if($action=="washorder_popup")
{
	echo load_html_head_contents("Embl. Order Popup Info","../../../", 1, 1, $unicode,'','');
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
			if(val==1 || val==0) $('#search_by_td').html('Wash Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer Po');
			else if(val==5) $('#search_by_td').html('Buyer Style');
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
                        <th width="100" id="search_by_td">Wash Job No</th>
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
								echo create_drop_down( "cbo_party_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Select Party-", "", "",0);   
							} 
							else if($cbo_within_group==2)
							{
								echo create_drop_down( "cbo_party_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-Select Party-", "", "" );   
							}
							?><input type="hidden" id="selected_str_data">
						</td>
						<td>
							<?
                                $search_by_arr=array(1=>"Wash Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
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
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( '<? echo $cbo_company_id; ?>'+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_year_selection').value+'_'+'<? echo $cbo_within_group; ?>', 'create_order_search_list_view', 'search_div', 'wash_recipe_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
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
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
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
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	
	$buyer_po_arr=array();
	
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
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
                            <th width="80">Process Name</th>
                            <th width="80">Wash Type</th>
                            <th width="90">Color</th>
                            <th>Qty</th>
						</thead>
					</table>
					<div style="width:870px; overflow-y:scroll; max-height:300px;">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table" id="list_view" >
							<?
							$sql= "select a.job_no_prefix_num, a.subcon_job, $year_select as year, a.party_id, a.id, a.order_id, b.id as po_id, b.order_no, b.main_process_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, c.color_id, sum(c.qnty) as qty  from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and b.id=c.mst_id and a.entry_form=295 and a.within_group=$within_group and a.status_active=1 $order_rcv_date $company $search_com_cond  $party_id_cond $po_idsCond group by a.job_no_prefix_num, a.subcon_job, a.insert_date, a.party_id, a.id, a.order_id, b.id, b.order_no, b.main_process_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, c.color_id order by a.id DESC";
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
								$str=$row[csf('party_id')].'___'.$row[csf('subcon_job')].'___'.$row[csf('po_id')].'___'.$row[csf('order_no')].'___'.$row[csf('gmts_item_id')].'___'.$row[csf('body_part')].'___'.$row[csf('main_process_id')].'___'.$row[csf('embl_type')].'___'.$row[csf('color_id')].'___'.$color_arr[$row[csf('color_id')]].'___'.$row[csf("buyer_po_id")].'___'.$buyer_po_arr[$row[csf("buyer_po_id")]]['po'].'___'.$buyer_po_arr[$row[csf("buyer_po_id")]]['style'];
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
			if(val==1 || val==0) $('#search_by_td').html('Wash Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer Po');
			else if(val==5) $('#search_by_td').html('Buyer Style');
		}
    </script>
    </head>

    <body>
    <div align="center" style="width:100%;">
        <form name="searchlabdipfrm" id="searchlabdipfrm">
            <fieldset style="width:960px;">
                <table cellpadding="0" cellspacing="0" border="1" rules="all" width="800" class="rpt_table">
                    <thead>
                        <tr>
                            <th colspan="6"><? echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --"); ?></th>
                        </tr>
                        <tr>
                            <th>Recipe Date Range</th>
                            <th>System ID</th>
                            <th width="150">Recipe Description</th>
                            <th width="100">Search By</th>
                    		<th width="100" id="search_by_td">Wash Job No</th>
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
                            <input type="text" style="width:130px;" class="text_boxes" name="txt_search_sysId" id="txt_search_sysId" placeholder="Search"/>
                        </td>
                        <td>
                            <input type="text" style="width:130px;" class="text_boxes" name="txt_search_recDes" id="txt_search_recDes" placeholder="Search"/>
                        </td>
                        <td>
							<?
                                $search_by_arr=array(1=>"Wash Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
                                echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                            ?>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                        </td>
                        <td>
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_sysId').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_recDes').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_search_string').value, 'create_recipe_search_list_view', 'search_div', 'wash_recipe_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:80px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
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
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
	
	$data = explode("_", $data);
	
	$sysid = $data[0];
	$start_date = $data[1];
	$end_date = $data[2];
	$company_id = $data[3];
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[7]));
	$rec_des = trim($data[5]);
	$search_type = $data[6];

	if ($start_date != "" && $end_date != "") 
	{
		if ($db_type == 0) 
		{
			$date_cond = "and recipe_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-", 1) . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-", 1) . "'";
		} 
		else if ($db_type == 2) 
		{
			$date_cond = "and recipe_date between '" . change_date_format(trim($start_date), "mm-dd-yyyy", "/", 1) . "' and '" . change_date_format(trim($end_date), "mm-dd-yyyy", "/", 1) . "'";
		}
	} 
	else 
	{
		$date_cond = "";
	}

	$sysid_cond = ""; $rec_des_cond = ""; $job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
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
		if ($sysid != '') $sysid_cond = " and recipe_no_prefix_num=$sysid";
		if ($rec_des != '') $rec_des_cond = " and recipe_description='$rec_des'";
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
		if ($sysid != '') $sysid_cond = " and recipe_no_prefix_num like '%$sysid%' ";
		if ($rec_des != '') $rec_des_cond = " and recipe_description like '%$rec_des%'";
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
		if ($sysid != '') $sysid_cond = " and recipe_no_prefix_num like '$sysid%' ";
		if ($rec_des != '') $rec_des_cond = " and recipe_description like '$rec_des%'";
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
		if ($sysid != '') $sysid_cond = " and recipe_no_prefix_num like '%$sysid' ";
		if ($rec_des != '') $rec_des_cond = " and recipe_description like '%$rec_des'";
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
	if ($po_ids!="") $po_idsCond=" and buyer_po_id in ($po_ids)"; else $po_idsCond="";
	
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
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
	}
	else if($db_type==2)
	{
		$id_cond="listagg(b.id,',') within group (order by b.id)";
	}
	if(($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2))
	{
		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.subcon_job=b.job_no_mst $search_com_cond", "id");
	}
	
	if ( $spo_ids!="") $spo_idsCond=" and po_id in ($spo_ids)"; else $spo_idsCond="";
	
	?>
	<body>
		<div align="center">
			<fieldset style="width:1070px;">
				<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1070" class="rpt_table" >
						<thead>
							<th width="30">SL</th>
							<th width="50">Recipe No</th>
                            <th width="80">Recip For</th>
                            <th width="60">Recipe Date</th>
                            <th width="60">Within Group</th>
                            <th width="90">Order</th>
                            <th width="100">Buyer Po</th>
                			<th width="100">Buyer Style</th>
                            <th width="90">Party</th>
                            <th width="80">Gmts. Item</th>
                            <th width="80">Body Part</th>
                            <th width="80">Process Name</th>
                            <th width="80">Wash Type</th>
                            <th>Color</th>
						</thead>
					</table>
					<div style="width:1070px; overflow-y:scroll; max-height:300px;">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1050" class="rpt_table" id="tbl_list_search" >
							<?
							$sql = "select id, recipe_no_prefix_num, recipe_for, recipe_date, within_group, po_id, buyer_id, gmts_item, body_part, embl_name, embl_type, color_id, buyer_po_id from pro_recipe_entry_mst where company_id='$company_id' and entry_form=300 and status_active=1 and is_deleted=0 $sysid_cond $rec_des_cond $date_cond $po_idsCond $spo_idsCond order by id DESC";
							//echo $sql; die;
							$sql_res=sql_select($sql);

							$i=1; 
							foreach($sql_res as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								
								if($row[csf('embl_name')]==1) $new_subprocess_array= $emblishment_print_type;
								else if($row[csf('embl_name')]==2) $new_subprocess_array= $emblishment_embroy_type;
								else if($row[csf('embl_name')]==3) $new_subprocess_array= $emblishment_wash_type;
								else if($row[csf('embl_name')]==4) $new_subprocess_array= $emblishment_spwork_type;
								else if($row[csf('embl_name')]==5) $new_subprocess_array= $emblishment_gmts_type;
								else $new_subprocess_array=$blank_array;
								
								$party_name="";
								if($row[csf('within_group')]==1) $party_name=$company_arr[$row[csf('buyer_id')]];
								else if($row[csf('within_group')]==2) $party_name=$buyer_arr[$row[csf('buyer_id')]];
								
								$buyer_po=""; $buyer_style="";
								$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
								foreach($buyer_po_id as $po_id)
								{
									if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
									if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
								}
								$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
								$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $row[csf('id')];?>')"> 
									<td width="30" align="center"><?php echo $i; ?></td>	
									<td width="50" align="center"><?php echo $row[csf('recipe_no_prefix_num')]; ?></td>
                                    <td width="80" style="word-break:break-all"><?php echo $recipe_for[$row[csf('recipe_for')]]; ?></td>
                                    <td width="60"><?php echo change_date_format($row[csf('recipe_date')]); ?></td>
                                    <td width="60"><?php echo $yes_no[$row[csf('within_group')]]; ?></td>
                                    <td width="90" style="word-break:break-all"><?php echo $po_arr[$row[csf("po_id")]]; ?></td>
                                    <td width="100" style="word-break:break-all"><?php echo $buyer_po; ?></td>
                                    <td width="100" style="word-break:break-all"><?php echo $buyer_style; ?></td>
                                    
                                    <td width="90" style="word-break:break-all"><?php echo $party_name; ?></td>
                                    <td width="80" style="word-break:break-all"><?php echo $garments_item[$row[csf('gmts_item')]]; ?></td>
                                    <td width="80" style="word-break:break-all"><?php echo $body_part[$row[csf('body_part')]]; ?></td>
                                    <td width="80" style="word-break:break-all"><?php echo $emblishment_name_array[$row[csf('embl_name')]]; ?></td>
                                    <td width="80" style="word-break:break-all"><?php echo $new_subprocess_array[$row[csf('embl_type')]]; ?></td>
                                    <td style="word-break:break-all"><?php echo $color_arr[$row[csf('color_id')]]; ?></td>
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
	$order_arr = array();
	$embl_sql ="Select a.subcon_job, b.id, b.order_no from subcon_ord_mst a, subcon_ord_dtls b where a.entry_form=295 and a.subcon_job=b.job_no_mst";
	$embl_sql_res=sql_select($embl_sql);
	foreach ($embl_sql_res as $row)
	{
		$order_arr[$row[csf("id")]]['job']=$row[csf("subcon_job")];
		$order_arr[$row[csf("id")]]['po']=$row[csf("order_no")];
	}
	unset($embl_sql_res);
	
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	
	$buyer_po_arr=array();
	
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	
	$data_array = sql_select("select id, recipe_no, company_id, location_id, recipe_description, recipe_for, recipe_date, within_group, po_id, job_no, buyer_po_id, buyer_id, gmts_item, body_part, embl_name, embl_type, color_id, remarks from pro_recipe_entry_mst where id='$data' and entry_form=300");
	
	foreach ($data_array as $row) {
		echo "document.getElementById('txt_sys_id').value 					= '" . $row[csf("recipe_no")] . "';\n";

		echo "document.getElementById('update_id_check').value 				= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('cbo_recipe_for').value 				= '" . $row[csf("recipe_for")] . "';\n";
		echo "document.getElementById('cbo_company_id').value 				= '" . $row[csf("company_id")] . "';\n";
		echo "$('#cbo_company_id').attr('disabled','true')" . ";\n";

		echo "load_drop_down('requires/wash_recipe_entry_controller', '".$row[csf('company_id')].'_'.$row[csf("within_group")]."', 'load_drop_down_buyer', 'buyer_td_id' );\n";
		echo "load_drop_down('requires/wash_recipe_entry_controller', ".$row[csf('embl_name')].", 'load_drop_down_embl_type', 'embl_type_td' );\n";
		
		echo "document.getElementById('cbo_location').value 				= '" . $row[csf("location_id")] . "';\n";
		echo "document.getElementById('txt_recipe_date').value 				= '" . change_date_format($row[csf("recipe_date")]) . "';\n";
		echo "document.getElementById('cbo_within_group').value 			= '" . $row[csf("within_group")] . "';\n";
		echo "$('#cbo_within_group').attr('disabled','true')" . ";\n";
		echo "$('#cbo_recipe_for').attr('disabled','true')" . ";\n";

		echo "document.getElementById('txt_recipe_des').value 				= '" . $row[csf("recipe_description")] . "';\n";
		echo "document.getElementById('txt_order_id').value 				= '" . $row[csf("po_id")] . "';\n";
		echo "document.getElementById('txt_order').value 					= '" . $order_arr[$row[csf("po_id")]]['po'] . "';\n";
		echo "document.getElementById('cbo_buyer_name').value 				= '" . $row[csf("buyer_id")] . "';\n";
		echo "document.getElementById('hid_job_no').value 					= '" . $row[csf("job_no")] . "';\n";

		echo "document.getElementById('txt_pocolor_id').value 				= '" . $row[csf("color_id")] . "';\n";
		echo "document.getElementById('txt_po_color').value 				= '" . $color_arr[$row[csf("color_id")]] . "';\n";
		echo "document.getElementById('hid_item_id').value 					= '" . $row[csf("gmts_item")] . "';\n";
		echo "document.getElementById('hid_bodypart_id').value 				= '" . $row[csf("body_part")] . "';\n";
		
		echo "document.getElementById('txtbuyerPo').value 					= '" . $buyer_po_arr[$row[csf("buyer_po_id")]]['po'] . "';\n";
		echo "document.getElementById('txtbuyerPoId').value 				= '" . $row[csf("buyer_po_id")] . "';\n";
		echo "document.getElementById('txtstyleRef').value 					= '" . $buyer_po_arr[$row[csf("buyer_po_id")]]['style'] . "';\n";
		
		echo "document.getElementById('cboEmblName').value 					= '" . $row[csf("embl_name")] . "';\n";
		echo "document.getElementById('cboEmblType').value 					= '" . $row[csf("embl_type")] . "';\n";
		echo "document.getElementById('txt_remarks').value 					= '" . $row[csf("remarks")] . "';\n";
		echo "document.getElementById('update_id').value 					= '" . $row[csf("id")] . "';\n";
		
		echo "set_button_status(0, '" . $_SESSION['page_permission'] . "', 'fnc_recipe_entry',1);\n";
		exit();
	}
}

if ($action == "recipe_item_details") 
{
	$multicolor_array = array();
	$product_arr = array();
	$color_arr = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$item_group_arr=return_library_array( "select id,item_name from lib_item_group",'id','item_name');
	$sql = "select id, color_id, new_prod_id from pro_recipe_entry_dtls where mst_id='$data' and status_active=1 and is_deleted=0 order by id";
	$nameArray = sql_select($sql);
	foreach ($nameArray as $row) 
	{
		if (!in_array($row[csf("color_id")], $multicolor_array)) 
		{
			$multicolor_array[] = $row[csf("color_id")];
		}
		$product_arr[$row[csf("color_id")]]=$row[csf("new_prod_id")];
	}
	
	$sql="select id, store_id, item_category_id, item_group_id, sub_group_name, item_description, item_code, item_size, unit_of_measure, current_stock from product_details_master where  item_category_id in(5,6,7) and status_active=1 and is_deleted=0";
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
        <h3 align="left" id="accordion_h<? echo $multicolor_id; ?>" style="width:910px" class="accordion_h" onClick="fnc_item_details('<? echo $multicolor_id.'__'.$color_arr[$multicolor_id].'__'.$new_prod_id.'__'.$item_category_id.'__'.$group_id.'__'.$item_name.'__'.$sub_group_name.'__'.$item_description.'__'.$item_size.'__'.$trim_uom.'__'.$item_code.'__'.$store_id; ?>')">
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
	$color_id=$data[1];
	$update_id=$data[2];

	$item_group_arr=return_library_array( "select id,item_name from lib_item_group",'id','item_name');

	$recipe_data_arr=array(); $recipe_prod_id_arr=array(); $product_data_arr=array();
	if($update_id!="")
	{	//sum(b.req_qny_edit) as qnty
		//$iss_arr=return_library_array("select b.product_id, sum(b.required_qnty) as qnty from inv_issue_master a, dyes_chem_issue_dtls b, dyes_chem_requ_recipe_att c where a.req_no=c.mst_id and a.id=b.mst_id and a.entry_form=5 and a.issue_basis=7 and c.recipe_id=$update_id and b.sub_process=$sub_process_id group by b.product_id",'product_id','qnty');

		/*if($sub_process_id==93 || $sub_process_id==94 || $sub_process_id==95 || $sub_process_id==96 || $sub_process_id==97 || $sub_process_id==98)
		{
			$ration_cond="";
		}
		else
		{*/
			$ration_cond=" and ratio>0 ";
		//}
		$recipeData=sql_select("select id, prod_id, ratio, seq_no, comments from pro_recipe_entry_dtls where mst_id=$update_id and color_id=$color_id and status_active=1 and is_deleted=0 $ration_cond order by seq_no");
		foreach($recipeData as $row)
		{
			$recipe_data_arr[$row[csf('prod_id')]]['ratio']=$row[csf('ratio')];
			$recipe_data_arr[$row[csf('prod_id')]]['seq_no']=$row[csf('seq_no')];
			$recipe_data_arr[$row[csf('prod_id')]]['id']=$row[csf('id')];
			$recipe_data_arr[$row[csf('prod_id')]]['comments']=$row[csf('comments')];
			$recipe_prod_id_arr[]=$row[csf('prod_id')];
		}
	}

	//var_dump($recipe_prod_id_arr);

	$sql="select id, item_category_id, item_group_id, sub_group_name, item_description, item_size, unit_of_measure, current_stock from product_details_master where company_id='$company_id' and item_category_id in(5,6,7) and status_active=1 and is_deleted=0";
	//echo $sql;
	$nameArray=sql_select( $sql );
	foreach($nameArray as $row)
	{
		$product_data_arr[$row[csf('id')]]=$row[csf('item_category_id')]."**".$row[csf('item_group_id')]."**".$row[csf('sub_group_name')]."**".$row[csf('item_description')]."**".$row[csf('item_size')]."**".$row[csf('unit_of_measure')]."**".$row[csf('current_stock')];
	}

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
                    <th width="70">Prod. ID</th>
                    <th width="80">Stock Qty</th>
                    <th>Remarks</th>
                </thead>
            </tr>
        </table>
        <div style="width:920px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="902" class="rpt_table" id="tbl_list_search">
                <tbody>
				<?
				
					$i=1; //$max_seq_no='';
					if(count($recipe_prod_id_arr)>0)
					{
						foreach($recipe_prod_id_arr as $prodId)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$prodData=explode("**",$product_data_arr[$prodId]);
							$item_category_id=$prodData[0];
							$item_group_id=$prodData[1];
							$sub_group_name=$prodData[2];
							$item_description=$prodData[3];
							$item_size=$prodData[4];
							$unit_of_measure=$prodData[5];
							$current_stock=$prodData[6];

							$dtls_id=$recipe_data_arr[$prodId]['id'];
							$ratio=$recipe_data_arr[$prodId]['ratio'];
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
                                <td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
                                <td width="80" id="item_category_<? echo $i; ?>"><p><? echo $item_category[$item_category_id]; ?></p></td>
                                <td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
                                <td width="70" id="sub_group_name_<? echo $i; ?>"><p><? echo $sub_group_name; ?>&nbsp;</p></td>
                                <td width="130" id="item_description_<? echo $i; ?>"><p><? echo $item_description." ".$item_size; ?></p></td>
                                <td width="40" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
                                <td width="60" align="center" id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px;"  value="<? echo $ratio; ?>" onBlur="color_row(<? echo $i; ?>); seq_no_val(<? echo $i; ?>); "  <? echo $disbled; ?>></td>
                                <td width="50" align="center" id="seqno_<? echo $i; ?>"><input type="text" name="txt_seqno[]" id="txt_seqno_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" value="<? echo $seq_no; ?>" onBlur="row_sequence(<? echo $i; ?>);"></td>
                                <td width="70" align="center" id="product_id_<? echo $i; ?>"><? echo $prodId; ?><input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" value="<? echo $prodId; ?>"></td>
                                <td width="80" align="right" id="stock_qty_<? echo $i; ?>"><? echo number_format($current_stock,2,'.',''); ?><input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $dtls_id; ?>"></td>
                                <td align="center" id="comments_<? echo $i; ?>"><input type="text" name="txt_comments[]" id="txt_comments_<? echo $i; ?>" class="text_boxes" style="width:130px" value="<? echo $comments; ?>"></td>
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
									<td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
									<td width="80" id="item_category_<? echo $i; ?>"><p><? echo $item_category[$item_category_id]; ?></p></td>
									<td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
									<td width="70" id="sub_group_name_<? echo $i; ?>"><p><? echo $sub_group_name; ?>&nbsp;</p></td>
									<td width="130" id="item_description_<? echo $i; ?>"><p><? echo $item_description." ".$item_size; ?></p></td>
									<td width="40" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
									<td width="60" align="center" id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px;"  value="<? echo $ratio; ?>" onBlur="color_row(<? echo $i; ?>); seq_no_val(<? echo $i; ?>); "  <? echo $disbled; ?>></td>
									<td width="50" align="center" id="seqno_<? echo $i; ?>"><input type="text" name="txt_seqno[]" id="txt_seqno_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" value="<? echo $seq_no; ?>" onBlur="row_sequence(<? echo $i; ?>);"></td>
									<td width="70" align="center" id="product_id_<? echo $i; ?>"><? echo $prodId; ?><input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" value="<? echo $prodId; ?>"></td>
									<td width="80" align="right" id="stock_qty_<? echo $i; ?>"><? echo number_format($current_stock,2,'.',''); ?><input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value=""></td>
									<td align="center" id="comments_<? echo $i; ?>"><input type="text" name="txt_comments[]" id="txt_comments_<? echo $i; ?>" class="text_boxes" style="width:130px" value="<? echo $comments; ?>"></td>
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

	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	//echo '10**';die;
	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0) 
		{
			mysql_query("BEGIN");
		}

		$recipe_update_id = '';
		if(str_replace("'", "", $copy_id) == 2)
		{
			if (str_replace("'", "", $update_id) == "") 
			{
				if($db_type==0) $date_cond=" YEAR(insert_date)";
				else if($db_type==2) $date_cond="to_char(insert_date,'YYYY')";
				
				$new_sys_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'WRE', date("Y",time()), 5, "select recipe_no_prefix, recipe_no_prefix_num from pro_recipe_entry_mst where company_id=$cbo_company_id and entry_form=300 and $date_cond=".date('Y',time())." order by id DESC", "recipe_no_prefix", "recipe_no_prefix_num" ));
				
				$id = return_next_id("id", "pro_recipe_entry_mst", 1);
	
				$field_array = "id, entry_form, recipe_no_prefix, recipe_no_prefix_num, recipe_no, company_id, location_id, recipe_description, recipe_for, recipe_date, within_group, po_id, job_no, buyer_id, buyer_po_id, gmts_item, body_part, embl_name, embl_type, color_id, remarks, inserted_by, insert_date, status_active, is_deleted";
				//echo $txt_liquor;
				$data_array = "(".$id.",300,'".$new_sys_no[1]."','".$new_sys_no[2]."','".$new_sys_no[0]."',".$cbo_company_id.",".$cbo_location.",".$txt_recipe_des.",".$cbo_recipe_for.",".$txt_recipe_date.",".$cbo_within_group.",".$txt_order_id.",".$hid_job_no.",".$cbo_buyer_name.",".$txtbuyerPoId.",".$hid_item_id.",".$hid_bodypart_id.",".$cboEmblName.",".$cboEmblType.",".$txt_pocolor_id.",". $txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				//$rID=sql_insert("pro_recipe_entry_mst",$field_array,$data_array,0);
				//if($rID) $flag=1; else $flag=0;
				$recipe_update_id = $id;
				$recipe_no=$new_sys_no[0];
			} 
			else 
			{
				$field_array_update = "location_id*recipe_description*recipe_for*recipe_date*po_id*job_no*buyer_id*buyer_po_id*gmts_item*body_part*embl_name*embl_type*color_id*remarks*updated_by*update_date";
	
				$data_array_update = $cbo_location . "*" . $txt_recipe_des . "*" . $cbo_recipe_for . "*" . $txt_recipe_date . "*" . $txt_order_id . "*".$hid_job_no."*" . $cbo_buyer_name . "*" . $txtbuyerPoId . "*" . $hid_item_id . "*" . $hid_bodypart_id . "*" . $cboEmblName . "*" . $cboEmblType . "*" . $txt_pocolor_id . "*" . $txt_remarks . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
	
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
						$data_prod_array="(".$id.",".$cbo_company_id.",".$cbo_store_id.",".$cbo_item_category.",300,".$item_group_id.",".$txt_subgroup_name.",".$hidd_group_code.",".$txt_description.",'".$productname."',".$txt_item_size.",".$hidd_cons_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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
	
			$field_array_dtls = "id, mst_id, color_id, prod_id, comments, ratio, seq_no, new_prod_id, inserted_by, insert_date, status_active, is_deleted";
			$dtls_id = return_next_id("id", "pro_recipe_entry_dtls", 1);
			//$color_id = return_id($txt_multi_color, $color_arr, "lib_color", "id,color_name");
			
			if(str_replace("'","",$txt_multi_color)!="")
			{ 
				if (!in_array(str_replace("'","",$txt_multi_color),$new_array_color))
				{
					$color_id = return_id( str_replace("'","",$txt_multi_color), $color_arr, "lib_color", "id,color_name","300");  
					$new_array_color[$color_id]=str_replace("'","",$txt_multi_color);
				}
				else $color_id =  array_search(str_replace("'","",$txt_multi_color), $new_array_color); 
			}
			else
			{
				$color_id=0;
			}
	
			for ($i = 1; $i <= $total_row; $i++) 
			{
				$product_id = "product_id_" . $i;
				$txt_ratio = "txt_ratio_" . $i;
				$txt_comments = "txt_comments_" . $i;
				$txt_seqno = "txt_seqno_" . $i;
				if ($i != 1) $data_array_dtls .= ",";
				$data_array_dtls .= "(".$dtls_id.",".$recipe_update_id.",'".$color_id."','".str_replace("'","",$$product_id)."','".str_replace("'","",$$txt_comments)."','".str_replace("'","",$$txt_ratio)."','".str_replace("'","",$$txt_seqno)."','".$new_prod_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
	
				$dtls_id = $dtls_id + 1;
			}
		}
		else
		{
			if (str_replace("'", "", $update_id) == "") 
			{
				if($db_type==0) $date_cond=" YEAR(insert_date)";
				else if($db_type==2) $date_cond="to_char(insert_date,'YYYY')";
				
				$new_sys_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'WRE', date("Y",time()), 5, "select recipe_no_prefix, recipe_no_prefix_num from pro_recipe_entry_mst where company_id=$cbo_company_id and entry_form=300 and $date_cond=".date('Y',time())." order by id DESC", "recipe_no_prefix", "recipe_no_prefix_num" ));
				
				$id = return_next_id("id", "pro_recipe_entry_mst", 1);
	
				$field_array = "id, entry_form, recipe_no_prefix, recipe_no_prefix_num, recipe_no, company_id, location_id, recipe_description, recipe_for, recipe_date, within_group, po_id, job_no, buyer_id, buyer_po_id, gmts_item, body_part, embl_name, embl_type, color_id, remarks, inserted_by, insert_date, status_active, is_deleted";
				//echo $txt_liquor;
				$data_array = "(".$id.",300,'".$new_sys_no[1]."','".$new_sys_no[2]."','".$new_sys_no[0]."',".$cbo_company_id.",".$cbo_location.",".$txt_recipe_des.",".$cbo_recipe_for.",".$txt_recipe_date.",".$cbo_within_group.",".$txt_order_id.",".$hid_job_no.",".$cbo_buyer_name.",".$txtbuyerPoId.",".$hid_item_id.",".$hid_bodypart_id.",".$cboEmblName.",".$cboEmblType.",".$txt_pocolor_id.",". $txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				//$rID=sql_insert("pro_recipe_entry_mst",$field_array,$data_array,0);
				//if($rID) $flag=1; else $flag=0;
				$recipe_update_id = $id;
				$recipe_no=$new_sys_no[0];
			}
			
			$field_array_dtls = "id, mst_id, color_id, prod_id, comments, ratio, seq_no, new_prod_id, inserted_by, insert_date, status_active, is_deleted";
			$dtls_id = return_next_id("id", "pro_recipe_entry_dtls", 1);
			$sql = "select id, mst_id, color_id, prod_id, comments, ratio, seq_no, new_prod_id from pro_recipe_entry_dtls where mst_id=$update_id_check order by id";
			$nameArray = sql_select($sql);
			$tot_row = count($nameArray);
			$i = 1; $data_array_dtls= '';

			foreach ($nameArray as $row)
			{
				if ($i != 1) $data_array_dtls .= ",";
				$data_array_dtls.="(".$dtls_id.",".$recipe_update_id.",'".$row[csf('color_id')]."','".$row[csf('prod_id')]."','".$row[csf('comments')]."','".$row[csf('ratio')]."','".$row[csf('seq_no')]."','".$row[csf('new_prod_id')]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$dtls_id=$dtls_id+1;
				$i++;
			}
		}
		//echo "insert into product_details_master (".$field_prod_array.") Values ".$data_prod_array."";die;
		

		//echo "10**insert into pro_recipe_entry_dtls (".$field_array_dtls.") Values ".$data_array_dtls.""; die;
		/*$rID2=sql_insert("pro_recipe_entry_dtls",$field_array_dtls,$data_array_dtls,1);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		} */

		//test all insert
		$flag = 1;
		if (str_replace("'", "", $update_id) == "") 
		{
			$rID = sql_insert("pro_recipe_entry_mst", $field_array, $data_array, 0);
			if ($rID==1 && $flag==1) $flag = 1; else $flag = 0;
		} 
		else 
		{
			$rID = sql_update("pro_recipe_entry_mst", $field_array_update, $data_array_update, "id", $update_id, 1);
			if ($rID==1 && $flag==1) $flag = 1; else $flag = 0;
		}
		$rID2 = sql_insert("pro_recipe_entry_dtls", $field_array_dtls, $data_array_dtls, 1);
		if ($rID2==1 && $flag==1) $flag = 1; else $flag = 0;
		
		
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
			if ($flag == 1) 
			{
				mysql_query("COMMIT");
				echo "0**" . $recipe_update_id . "**" . $recipe_no;
			} 
			else 
			{
				mysql_query("ROLLBACK");
				echo "5**0**0";
			}
		}
		else if ($db_type == 2 || $db_type == 1) 
		{
			if ($flag == 1) 
			{
				oci_commit($con);
				echo "0**" . $recipe_update_id . "**" . $recipe_no;
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
			die;
		}
		
		$prod_number=return_field_value( "sys_no", "subcon_embel_production_dtls"," recipe_id=$update_id and status_active=1 and is_deleted=0");
		if($prod_number){
			echo "emblProduction**".str_replace("'","",$txt_job_no)."**".$prod_number;
			die;
		}

		$field_array_update = "location_id*recipe_description*recipe_for*recipe_date*po_id*job_no*buyer_id*buyer_po_id*gmts_item*body_part*embl_name*embl_type*color_id*remarks*updated_by*update_date";

		$data_array_update = $cbo_location . "*" . $txt_recipe_des . "*" . $cbo_recipe_for . "*" . $txt_recipe_date . "*" . $txt_order_id . "*".$hid_job_no."*" . $cbo_buyer_name . "*" . $txtbuyerPoId . "*" . $hid_item_id . "*" . $hid_bodypart_id . "*" . $cboEmblName . "*" . $cboEmblType . "*" . $txt_pocolor_id . "*" . $txt_remarks . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

		//$rID=sql_update("pro_recipe_entry_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		//if($rID) $flag=1; else $flag=0;
		$recipe_update_id = str_replace("'", "", $update_id);
		$recipe_no=str_replace("'", "", $txt_sys_id);
		
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
					$data_prod_array="(".$id.",".$cbo_company_id.", ".$cbo_store_id.",".$cbo_item_category.",300,".$item_group_id.",".$txt_subgroup_name.",".$hidd_group_code.",".$txt_description.",'".$productname."',".$txt_item_size.",".$hidd_cons_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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
		
		
		$field_array_dtls = "id, mst_id, color_id, prod_id, comments, ratio, seq_no, new_prod_id, inserted_by, insert_date, status_active, is_deleted";
		$field_array_dtls_update = "color_id*prod_id*comments*ratio*seq_no*new_prod_id*updated_by*update_date";
		$dtls_id = return_next_id("id", "pro_recipe_entry_dtls", 1);
		//$color_id = return_id($txt_multi_color, $color_arr, "lib_color", "id,color_name","300");
		//$new_array_color=array();
		if(str_replace("'","",$txt_multi_color)!="")
		{ 
			if (!in_array(str_replace("'","",$txt_multi_color),$new_array_color))
			{
				$color_id = return_id( str_replace("'","",$txt_multi_color), $color_arr, "lib_color", "id,color_name","300");  
				$new_array_color[$color_id]=str_replace("'","",$txt_multi_color);
			}
			else $color_id =  array_search(str_replace("'","",$txt_multi_color), $new_array_color); 
		}
		else
		{
			$color_id=0;
		}
		
		for ($i = 1; $i <= $total_row; $i++) 
		{
			$product_id = "product_id_" . $i;
			$txt_comments = "txt_comments_" . $i;
			$txt_ratio = "txt_ratio_" . $i;
			$updateIdDtls = "updateIdDtls_" . $i;
			$txt_seqno = "txt_seqno_" . $i;

			if (str_replace("'", "", $$updateIdDtls) != "") 
			{
				$id_arr[] = str_replace("'", '', $$updateIdDtls);
				$data_array_dtls_update[str_replace("'", '', $$updateIdDtls)] = explode("*", (str_replace("'", "", $color_id) . "*".str_replace("'", "", $$product_id) . "*'" . str_replace("'", "", $$txt_comments) . "'*'" . str_replace("'", "", $$txt_ratio) . "'*'" . str_replace("'", "", $$txt_seqno) . "'*'" . str_replace("'", "", $new_prod_id) . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
			} 
			else 
			{
				if ($i != 1) $data_array_dtls .= ",";
				$data_array_dtls .= "(" . $dtls_id . "," . $recipe_update_id . ",'" . $color_id . "','" . str_replace("'", "", $$product_id) . "','" . str_replace("'", "", $$txt_comments)."','".str_replace("'", "", $$txt_ratio) . "','" . str_replace("'", "", $$txt_seqno) . "','".$new_prod_id."'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";
	
				$dtls_id = $dtls_id + 1;
			}
		}

		// Update test all
		$flag = 1;
		$rID = sql_update("pro_recipe_entry_mst", $field_array_update, $data_array_update, "id", $update_id, 1);
		if ($rID==1 && $flag==1) $flag = 1; else $flag = 0;
		/*if ($data_array_dtls_update2 != "") 
		{
			$rID = sql_update("pro_recipe_entry_dtls", $field_array_dtls_update2, $data_array_dtls_update2, "id", $update_dtls_id, 1);
			if ($rID) $flag = 1; else $flag = 0;
		}*/
		if ($data_array_dtls_update != "") 
		{
			$rID2 = execute_query(bulk_update_sql_statement("pro_recipe_entry_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr), 1);
			if ($rID2==1 && $flag==1) $flag = 1; else $flag = 0;
		}

		if ($data_array_dtls != "") 
		{
			//echo "insert into pro_recipe_entry_dtls (".$field_array_dtls.") Values ".$data_array_dtls."";die;
			$rID3 = sql_insert("pro_recipe_entry_dtls", $field_array_dtls, $data_array_dtls, 1);
			if ($rID3==1 && $flag==1) $flag = 1; else $flag = 0;
		}
		
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
		//echo "10**".$rID.'='.$rID2.'='.$rID3; 
		//print_r( $data_array_dtls_update);
		//die;
		if ($db_type == 0) 
		{
			if ($flag == 1) 
			{
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", '', $update_id) . "**" . $recipe_no;
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
				echo "1**" . str_replace("'", '', $update_id) . "**" . $recipe_no;
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
			die;
		}
		
		$prod_number=return_field_value( "sys_no", "subcon_embel_production_dtls"," recipe_id=$update_id and status_active=1 and is_deleted=0");
		if($prod_number){
			echo "emblProduction**".str_replace("'","",$txt_job_no)."**".$prod_number;
			die;
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
		//die;
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
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	
	$order_arr = array();
	$embl_sql ="Select a.subcon_job, b.id, b.order_no from subcon_ord_mst a, subcon_ord_dtls b where a.entry_form=295 and a.subcon_job=b.job_no_mst";
	$embl_sql_res=sql_select($embl_sql);
	foreach ($embl_sql_res as $row)
	{
		$order_arr[$row[csf("id")]]['job']=$row[csf("subcon_job")];
		$order_arr[$row[csf("id")]]['po']=$row[csf("order_no")];
	}
	unset($embl_sql_res);
	
	$buyer_po_arr=array();
	$po_sql ="Select a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);

	$sql_mst = "select id, recipe_no, company_id, location_id, recipe_description, recipe_for, recipe_date, within_group, po_id, job_no, buyer_id, buyer_po_id, gmts_item, body_part, embl_name, embl_type, color_id, remarks from pro_recipe_entry_mst where id='$mst_id'";
	//echo $sql_mst;
	$dataArray = sql_select($sql_mst); $party_name="";
	if($dataArray[0][csf('within_group')]==1) $party_name=$company_library[$dataArray[0][csf('buyer_id')]];
	else if($dataArray[0][csf('within_group')]==2) $party_name=$buyer_library[$dataArray[0][csf('buyer_id')]];
	
	
	if($dataArray[0][csf('embl_name')]==1) $new_subprocess_array= $emblishment_print_type;
	else if($dataArray[0][csf('embl_name')]==2) $new_subprocess_array= $emblishment_embroy_type;
	else if($dataArray[0][csf('embl_name')]==3) $new_subprocess_array= $emblishment_wash_type;
	else if($dataArray[0][csf('embl_name')]==4) $new_subprocess_array= $emblishment_spwork_type;
	else if($dataArray[0][csf('embl_name')]==5) $new_subprocess_array= $emblishment_gmts_type;
	else $new_subprocess_array=$blank_array;
	
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
                <td><strong>Party Name:</strong></td>
                <td><? echo $party_name; ?></td>
                <td><strong>Job No:</strong></td>
                <td> <? echo $dataArray[0][csf('job_no')]; ?></td>
                <td><strong>Order No:</strong></td>
                <td><? echo $order_arr[$dataArray[0][csf('po_id')]]['po']; ?></td>
            </tr>
            <tr>
                <td><strong>Color:</strong></td>
                <td><? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
                <td><strong>Recipe For:</strong></td>
                <td><? echo $recipe_for[$dataArray[0][csf('recipe_for')]]; ?></td>
                <td><strong>Within Group:</strong></td>
                <td> <? echo $yes_no[$dataArray[0][csf('within_group')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Process Name:</strong></td>
                <td><? echo $emblishment_name_array[$dataArray[0][csf('embl_name')]]; ?></td>
                <td><strong>Wash Type:</strong></td>
                <td><? echo $new_subprocess_array[$dataArray[0][csf('embl_type')]]; ?> </td>
                <td><strong>Gmts. Item:</strong></td>
				<td> <? echo $garments_item[$dataArray[0][csf('gmts_item')]]; ?> </td> 
            </tr>
            <tr>
            	<td><strong>Body Part:</strong></td>
                <td><? echo $body_part[$dataArray[0][csf('body_part')]]; ?></td>
                <td><strong>Buyer Po:</strong></td>
                <td><? echo $buyer_po_arr[$dataArray[0][csf('buyer_po_id')]]['po']; ?> </td>
                <td><strong>Buyer Style:</strong></td>
				<td><? echo $buyer_po_arr[$dataArray[0][csf('buyer_po_id')]]['style']; ?> </td> 
            </tr>
            <tr>
            	<td><strong>Buyer Name:</strong></td>
				<td><? echo $buyer_library[$buyer_po_arr[$dataArray[0][csf('buyer_po_id')]]['buyer']]; ?> </td> 
                <td><strong>Remarks:</strong></td>
                <td colspan="3"><? echo $dataArray[0][csf('remarks')]; ?></td>
            </tr>
        </table>
        <?
			$data_arr = sql_select("select image_location from common_photo_library  where master_tble_id='$mst_id' and form_name='embl_recipe_entry'");
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
                    <th width="140">Item Group</th>
                    <th width="140">Sub Group</th>
                    <th width="200">Item Description</th>
                    <th width="50">UOM</th>
                    <th width="80">Ratio in %</th>
                    <th>Remarks</th>
                </thead>
				<?
				$multicolor_array = array();
				$prod_data_array = array();
				$color_remark_array = array();
				$sql = "select color_id from pro_recipe_entry_dtls where mst_id=$mst_id and status_active=1 and is_deleted=0 order by id ASC";
				$nameArray = sql_select($sql); $tot_rows=0; $prodIds='';
				foreach ($nameArray as $row) 
				{
					if (!in_array($row[csf("color_id")], $multicolor_array)) 
					{
						$multicolor_array[] = $row[csf("color_id")];
					}
				}
				unset($nameArray);
				
				$sql = "select id, color_id, prod_id, comments, ratio, seq_no from pro_recipe_entry_dtls where mst_id=$mst_id and ratio is not null and status_active=1 and is_deleted=0 order by seq_no ASC";
				$nameArray = sql_select($sql); $tot_rows=0; $prodIds='';
				foreach ($nameArray as $row) 
				{
					$color_remark_array[$row[csf("color_id")]][$row[csf("id")]]['prod_id']= $row[csf("prod_id")];
					$color_remark_array[$row[csf("color_id")]][$row[csf("id")]]['comments']= $row[csf("comments")];
					$color_remark_array[$row[csf("color_id")]][$row[csf("id")]]['ratio']= $row[csf("ratio")];
					$color_remark_array[$row[csf("color_id")]][$row[csf("id")]]['seq_no']= $row[csf("seq_no")];
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
				
				$sql = "select id, item_category_id, item_group_id, sub_group_name, item_description, unit_of_measure from product_details_master where status_active=1 and is_deleted=0 and company_id='$com_id' $prodIds_cond and item_category_id in(5,6,7) order by id";
				
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
                            <td><? echo $sub_group_name; ?>&nbsp;</td>
                            <td><? echo $item_description; ?>&nbsp;</td>
                            <td align="center"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
                            <td align="right"><? echo number_format($exdata['ratio'], 6, '.', ''); ?>&nbsp;</td>
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
                        <td align="right" colspan="6"><strong>Color Total</strong></td>
                        <td align="right"><? echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
					<?
				}
				?>
            </table>
            <br>
			<?
				echo signature_table(138, $com_id, "930px");
			?>
        </div>
    </div>
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
                echo  create_list_view ( "list_view", "Item Catagory,Group Code,Item Group Name,Order UOM,Cons. UOM", "110,80,150,80,50","550","340",0, $sql, "js_set_value", "id,item_group_code,item_name,order_uom,trim_uom", "", 1, "item_category,0,0,order_uom,trim_uom", $arr , "item_category,item_group_code,item_name,order_uom,trim_uom", "", 'setFilterGrid("list_view",-1);','0,0,0,0,0' );
            ?>
            <input type="hidden" id="item_str" />
        </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>                                  
	<?
	exit();																																																					}

?>
