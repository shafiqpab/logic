<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 150, "SELECT id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-Select Location-", $selected, "load_drop_down('requires/embl_production_controller', document.getElementById('cbo_company_id').value+'__'+this.value, 'load_drop_down_floor', 'floor_td');","","","","","",3 );
	exit();	 
}

if ($action=="load_drop_down_floor")
{
	$data=explode("__",$data);
	$company_id=$data[0];
	$location_id=$data[1];
	if($location_id==0 || $location_id=="") $location_cond=""; else $location_cond=" and b.location_id=$location_id";

	if($company_id==0 && $location_id==0)
	{
		echo create_drop_down( "cbo_floor_id", 150, $blank_array,"", 1, "--Select Floor--", 0, "",0 );
	}
	else
	{
		//echo "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=3 and b.company_id=$company_id and b.status_active=1 and b.is_deleted=0 and a.production_process=8 $location_cond group by a.id, a.floor_name order by a.floor_name";
		echo create_drop_down( "cbo_floor_id", 150, "SELECT a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=3 and b.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_process=8 $location_cond group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "--Select Floor--", 0, "load_machine();","" );
	}
  	exit();	 
}

if ($action=="load_drop_down_machine")
{
	$data_ex=explode("_",$data);
	$company_id=$data_ex[0];
	$floor_id=$data_ex[1];
	if($floor_id==0 || $floor_id=="") $floor_cond=""; else $floor_cond=" and floor_id=$floor_id";
	if($db_type==0)
	{
		$sql="SELECT id,concat(machine_no,'-',brand) as machine_name from lib_machine_name where category_id=3 and company_id=$company_id and status_active=1 and is_deleted=0 and is_locked=0 $floor_cond order by machine_name";
	}
	else if($db_type==2)
	{
		$sql="SELECT id, machine_no || '-' || brand as machine_name from lib_machine_name where category_id=3 and company_id=$company_id and status_active=1 and is_deleted=0 and is_locked=0 $floor_cond order by machine_name";
	}

	if($company_id==0 && $floor_id==0)
	{
		echo create_drop_down( "cbo_machine_id", 150, $blank_array,"", 1, "--Select Machine--", 0, "",0 );
	}
	else
	{
		echo create_drop_down( "cbo_machine_id", 150, $sql,"id,machine_name", 1, "--Select Machine--", 0, "","" );
	}
	exit();
}

if ($action == "load_drop_down_buyer") 
{
	$exdata=explode("_",$data);
	if($exdata[1]==1)
	{
		echo create_drop_down( "cbo_buyer_name", 150, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Party--", $exdata[2], "",1);
	}
	else if($exdata[1]==2)
	{
		echo create_drop_down("cbo_buyer_name", 150, "SELECT buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$exdata[0]' $buyer_cond and buy.id in (SELECT buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buy.buyer_name", "id,buyer_name", 1, "--Select Party--", $exdata[2], "", 1);
	}
	else
	{
		echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "--Select Party--", $selected, "",1 );
	}
	exit();
}

if($action=="recipe_popup")
{
	echo load_html_head_contents("Recipe Pop-up","../../../", 1, 1, $unicode,'','');
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
			if(val==1 || val==0) $('#search_by_td').html('Embl. Job No');
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
                    <th colspan="7"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",1 ); ?></th>
                </tr>
                <tr>               	 
                    <th width="140">Recipe</th>
                    <th width="100">Search By</th>
                    <th width="100" id="search_by_td">Buyer Po</th>
                    <th width="60">Job Year</th>
                    <th width="130" colspan="2">Receive Date Range</th>                            
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /> </th>
                </tr>           
            </thead>
            <tbody>
                <tr class="general">
                    <td><input type="hidden" id="selected_str_data">
                        <input type="text" name="txt_search_recipe" id="txt_search_recipe" class="text_boxes" style="width:90px" placeholder="Search Recipe" />
                    </td>
                    <td>
						<?
                            $search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
                            echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",4,'search_by(this.value)',"","" );
                        ?>
                    </td>
                    <td align="center">
                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:90px" placeholder="" />
                    </td>
                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td align="center"><input name="txt_date_from" id="txt_date_from" class="datepicker" placeholder="From" style="width:60px"></td>
                    <td align="center"><input name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To" style="width:60px"></td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( '<? echo $cbo_company_id; ?>'+'_'+document.getElementById('txt_search_recipe').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_year_selection').value, 'create_recipe_search_list_view', 'search_div', 'embl_production_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
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

if($action=="create_recipe_search_list_view")
{
	$exdata=explode('_',$data);
	$cbo_company_id=$exdata[0];
	$search_recipe=$exdata[1];
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
			else if($search_by==2) $search_com_cond="and b.order_no='$search_str'";
			
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
			else if($search_by==2) $search_com_cond="and b.order_no like '%$search_str%'";  
			
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
			else if($search_by==2) $search_com_cond="and b.order_no like '$search_str%'";  
			
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
			else if($search_by==2) $search_com_cond="and b.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str'";  
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str'";  
		}
	}	
	
	$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4)|| ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
	}
	
	if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";

	if($search_recipe!='') $search_recipe_cond=" and d.recipe_no_prefix_num='$search_recipe'"; else $search_recipe_cond="";

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
	$buyer_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
	
	$production_qty_arr=array();
	$prod_data_arr="SELECT a.recipe_id, sum(b.qcpass_qty) as qty from subcon_embel_production_mst a, subcon_embel_production_dtls b where a.id=b.mst_id and a.entry_form=222 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.recipe_id";
	$prod_data_res=sql_select($prod_data_arr);
	
	foreach($prod_data_res as $row)
	{
		$production_qty_arr[$row[csf('recipe_id')]]=$row[csf('qty')];
	}
	unset($prod_data_res);
	
	$po_sql ="SELECT a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	?>
    <body>
		<div align="center">
			<fieldset style="width:1070px;">
				<form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1070" class="rpt_table" >
						<thead>
							<th width="30">SL</th>
							<th width="50">Recipe</th>
                            <th width="50">Job</th>
                            <th width="80">Buyer</th>
                            <th width="80">WO No</th>
                            <th width="90">Buyer Po</th>
							<th width="90">Buyer Style</th>
                            <th width="90">Gmts. Item</th>
                            <th width="80">Body Part</th>
                            <th width="70">Embl. Name</th>
                            <th width="70">Embl. Type</th>
                            <th width="70">Color</th>
                            <th width="70">Order Qty</th>
                            <th width="70">Prod Qty</th>
                            <th>Balance Qty</th>
						</thead>
					</table>
					<div style="width:1070px; overflow-y:scroll; max-height:300px;">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1050" class="rpt_table" id="list_view" >
							<?
							$sql= "SELECT a.id, a.job_no_prefix_num, a.embellishment_job, a.party_id, a.within_group, b.id as order_id, b.order_no, b.buyer_po_id, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.color_id, sum(c.qnty) as qty, d.id as recipe_id, d.recipe_no_prefix_num, d.recipe_no from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, pro_recipe_entry_mst d where a.embellishment_job=b.job_no_mst and b.id=c.mst_id and a.embellishment_job=d.job_no and b.id=d.po_id 		
							and b.main_process_id=d.embl_name and b.gmts_item_id=d.gmts_item and b.embl_type=d.embl_type and b.body_part=d.body_part and c.color_id=d.color_id 						
							and a.entry_form=204 and d.entry_form=220 and a.status_active=1 and b.status_active=1 and d.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 $order_rcv_date $company $search_com_cond $search_recipe_cond $po_idsCond group by a.id, a.job_no_prefix_num, a.embellishment_job, a.party_id, a.within_group, b.id, b.order_no, b.buyer_po_id, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.color_id, d.id, d.recipe_no_prefix_num, d.recipe_no order by d.recipe_no_prefix_num DESC";
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
								$str=$row[csf('recipe_id')].'___'.$row[csf('recipe_no')].'___'.$row[csf('embellishment_job')].'___'.$row[csf('order_id')].'___'.$row[csf('order_no')].'___'.$row[csf('party_id')].'___'.$row[csf('within_group')].'___'.$row[csf('qty')].'___'.$row[csf('buyer_po_id')].'___'.$buyer_po_arr[$row[csf("buyer_po_id")]]['po'].'___'.$buyer_po_arr[$row[csf("buyer_po_id")]]['style'];
								
								$prod_qty=0; $balance_qty=0;
								$prod_qty=$production_qty_arr[$row[csf('recipe_id')]];
								$balance_qty=$row[csf('qty')]-$prod_qty;
								
								$buyer_po=""; $buyer_style=""; $buyer_name='';
								$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
								foreach($buyer_po_id as $po_id)
								{
									if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
									if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
									if($buyer_name=="") $buyer_name=$buyer_arr[$buyer_po_arr[$po_id]['buyer']]; else $buyer_name.=','.$buyer_arr[$buyer_po_arr[$po_id]['buyer']];
								}
								$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
								$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
								$buyer_name=implode(",",array_unique(explode(",",$buyer_name)));
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $str;?>')"> 
									<td width="30" align="center"><?php echo $i; ?></td>	
									<td width="50" align="center"><?php echo $row[csf('recipe_no_prefix_num')]; ?></td>
                                    <td width="50" align="center"><?php echo $row[csf('job_no_prefix_num')]; ?></td>
                                    <td width="80" style="word-break:break-all"><?php echo $buyer_name; ?></td>
                                    <td width="80" style="word-break:break-all"><?php echo $row[csf('order_no')]; ?></td>
                                    
                                    <td width="90" style="word-break:break-all"><?php echo $buyer_po; ?></td>
                                    <td width="90" style="word-break:break-all"><?php echo $buyer_style; ?></td>
                                    <td width="90" style="word-break:break-all"><?php echo $garments_item[$row[csf('gmts_item_id')]]; ?></td>
                                    <td width="80" style="word-break:break-all"><?php echo $body_part[$row[csf('body_part')]]; ?></td>
                                    <td width="70" style="word-break:break-all"><?php echo $emblishment_name_array[$row[csf('main_process_id')]]; ?></td>
                                    <td width="70" style="word-break:break-all"><?php echo $new_subprocess_array[$row[csf('embl_type')]]; ?></td>
                                    <td width="70" style="word-break:break-all"><?php echo $color_arr[$row[csf('color_id')]]; ?></td>
                                    <td width="70" align="right"><?php echo number_format($row[csf('qty')],2); ?></td>
                                    <td width="70" align="right"><?php if($prod_qty>0) {echo number_format($prod_qty,2);} else {echo "";} ?></td>
                                    <td align="right"><?php if($balance_qty!=0) {echo number_format($balance_qty,2);} else {echo "";} ?></td>
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

if($action=="order_details")
{
	$data=explode("***",$data);
	$company_id=$data[0];
	$recipe_id=$data[1];
	$update_id=$data[2];
	$job_No=$data[3];

	$item_group_arr=return_library_array( "SELECT id,item_name from lib_item_group where status_active=1 and is_deleted=0",'id','item_name');
	$color_arr=return_library_array( "SELECT id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name" );
	$size_arr=return_library_array( "SELECT id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
	
	/*$prod_data_arr=array(); //$recipe_prod_id_arr=array(); $product_data_arr=array();
	if($update_id!=0)
	{	
		$prodData=sql_select("SELECT id, color_size_id, production_date, production_hour, qcpass_qty, operator_name, shift_id, remarks 
			from subcon_embel_production_dtls 
			where mst_id=$update_id and status_active=1 and is_deleted=0");
		foreach($prodData as $row)
		{
			$prod_data_arr[$row[csf('color_size_id')]]['production_date']=$row[csf('production_date')];
			$prod_data_arr[$row[csf('color_size_id')]]['production_hour']=$row[csf('production_hour')];
			$prod_data_arr[$row[csf('color_size_id')]]['id']=$row[csf('id')];
			$prod_data_arr[$row[csf('color_size_id')]]['qcpass_qty']=$row[csf('qcpass_qty')];
			$prod_data_arr[$row[csf('color_size_id')]]['operator_name']=$row[csf('operator_name')];
			$prod_data_arr[$row[csf('color_size_id')]]['shift_id']=$row[csf('shift_id')];
			$prod_data_arr[$row[csf('color_size_id')]]['remarks']=$row[csf('remarks')];
		}
		unset($prodData);
	}*/

	$prodData=sql_select("SELECT id, mst_id, color_size_id, production_date, production_hour, qcpass_qty, operator_name, shift_id, remarks from subcon_embel_production_dtls where job_no='$job_No' and status_active=1 and is_deleted=0");
	$prod_data_arr=$balance_data_arr=array(); //$recipe_prod_id_arr=array(); $product_data_arr=array();
	foreach ($prodData as $row)
	{
		if($row[csf("mst_id")]==$update_id)
		{
			$prod_data_arr[$row[csf('color_size_id')]]['production_date']=$row[csf('production_date')];
			$prod_data_arr[$row[csf('color_size_id')]]['production_hour']=$row[csf('production_hour')];
			$prod_data_arr[$row[csf('color_size_id')]]['id']=$row[csf('id')];
			$prod_data_arr[$row[csf('color_size_id')]]['qcpass_qty']=$row[csf('qcpass_qty')];
			$prod_data_arr[$row[csf('color_size_id')]]['operator_name']=$row[csf('operator_name')];
			$prod_data_arr[$row[csf('color_size_id')]]['shift_id']=$row[csf('shift_id')];
			$prod_data_arr[$row[csf('color_size_id')]]['remarks']=$row[csf('remarks')];
		}
		else
		{
			$balance_data_arr[$row[csf('color_size_id')]]['qcpass_qty']+=$row[csf('qcpass_qty')];
		}
	}
	/*foreach($prodData as $row)
	{
		$prod_data_arr[$row[csf('color_size_id')]]['production_date']=$row[csf('production_date')];
		$prod_data_arr[$row[csf('color_size_id')]]['production_hour']=$row[csf('production_hour')];
		$prod_data_arr[$row[csf('color_size_id')]]['id']=$row[csf('id')];
		$prod_data_arr[$row[csf('color_size_id')]]['qcpass_qty']=$row[csf('qcpass_qty')];
		$prod_data_arr[$row[csf('color_size_id')]]['operator_name']=$row[csf('operator_name')];
		$prod_data_arr[$row[csf('color_size_id')]]['shift_id']=$row[csf('shift_id')];
		$prod_data_arr[$row[csf('color_size_id')]]['remarks']=$row[csf('remarks')];
	}*/
	unset($prodData);
	

	$issue_sql= "SELECT a.id, a.unit_of_measure, b.id as dtls_id,b.trans_id,recipe_qnty,required_qnty,req_qny_edit 
		from product_details_master a, dyes_chem_issue_dtls b, inv_transaction t 
		where a.id=b.product_id and b.trans_id=t.id and b.status_active=1 
		and b.is_deleted=0 and b.recipe_id='$recipe_id'  and a.company_id='$company_id' and a.item_category_id in(5,6,7,23,22) and a.status_active=1 and a.status_active=1 and a.is_deleted=0 and t.status_active=1 and t.is_deleted=0 order by a.item_category_id";

	$issue_sql= "SELECT sum(quantity) as quantity ,job_break_id from sub_material_dtls where status_active=1 and is_deleted=0 group by job_break_id";
		
	$issueData=sql_select($issue_sql); $issue_data_arr=array();
	foreach($issueData as $row)
	{
		$issue_data_arr[$row[csf('job_break_id')]]['quantity']=$row[csf('quantity')];
	}
	unset($issueData);

	//var_dump($recipe_prod_id_arr);

	$sql= "SELECT  a.id, a.job_no_prefix_num, a.embellishment_job, a.party_id, a.within_group, b.id as order_id, b.order_no, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, b.buyer_po_id, c.id as color_size_id, c.color_id, c.size_id, sum(c.qnty) as qty, d.id as recipe_id, d.recipe_no_prefix_num, d.recipe_no 
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, pro_recipe_entry_mst d 
	where a.embellishment_job=b.job_no_mst and b.id=c.mst_id and a.embellishment_job=d.job_no and b.id=d.po_id and b.main_process_id=d.embl_name and b.gmts_item_id=d.gmts_item and b.embl_type=d.embl_type and b.body_part=d.body_part and c.color_id=d.color_id and a.entry_form=204 and d.entry_form=220 and a.status_active=1 and b.status_active=1 and d.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 and a.company_id='$company_id' and d.id='$recipe_id' group by a.id, a.job_no_prefix_num, a.embellishment_job, a.party_id, a.within_group, b.id, b.order_no, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, b.buyer_po_id, c.id, c.color_id, c.size_id, d.id, d.recipe_no_prefix_num, d.recipe_no order by c.id ASC";
	//echo $sql; die;

	$sql_res=sql_select($sql);

	$i=1; 
	foreach($sql_res as $row)
	{
		if($row[csf('main_process_id')]==1) $new_subprocess_array= $emblishment_print_type;
		else if($row[csf('main_process_id')]==2) $new_subprocess_array= $emblishment_embroy_type;
		else if($row[csf('main_process_id')]==3) $new_subprocess_array= $emblishment_wash_type;
		else if($row[csf('main_process_id')]==4) $new_subprocess_array= $emblishment_spwork_type;
		else if($row[csf('main_process_id')]==5) $new_subprocess_array= $emblishment_gmts_type;
		else $new_subprocess_array=$blank_array;
		
		$balanceQty=0;$order_qty=0; $quantity=$preProd_qty=0;
		
		$preProd_qty=$balance_data_arr[$row[csf('color_size_id')]]['qcpass_qty'];
		//$order_qty=$row[csf('qty')]*12;
		$order_qty=number_format($row[csf('qty')]*12,0,'.','');
		$issue_qty=$issue_data_arr[$row[csf('color_size_id')]]['quantity'];
		$balanceQty=number_format($issue_qty-$preProd_qty,4,'.','');
		

		if($update_id!=0)
		{
			$upid=$prod_data_arr[$row[csf('color_size_id')]]['id'];
			$shift_id=$prod_data_arr[$row[csf('color_size_id')]]['shift_id'];
			$operator_name=$prod_data_arr[$row[csf('color_size_id')]]['operator_name'];
			$quantity=$prod_data_arr[$row[csf('color_size_id')]]['qcpass_qty'];
			$remarks=$prod_data_arr[$row[csf('color_size_id')]]['remarks'];
		}
		else $quantity='';
		if($quantity==0) $quantity='';
		if($balanceQty==0) $balanceQty=0;
		?>
		<tr class="general" name="tr[]" id="tr_<? echo $i;?>">
			<td><input type="text" name="txtSl[]" id="txtSl_<? echo $i;?>" class="text_boxes_numeric" style="width:30px" disabled value="<? echo $i; ?>" /></td>
			<td>
				<input type="text" name="txtRecipeNo[]" id="txtRecipeNo_<? echo $i;?>" class="text_boxes" style="width:100px" placeholder="Display"disabled value="<? echo $row[csf('recipe_no')]; ?>" />
				<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i;?>" style="width:50px" value="<? echo $upid; ?>" />
				<input type="hidden" name="colorSizeId[]" id="colorSizeId_<? echo $i;?>" style="width:50px" value="<? echo $row[csf('color_size_id')]; ?>" />
			</td>
			<td>
				<input type="text" name="txtGmtsItem[]" id="txtGmtsItem_<? echo $i;?>" class="text_boxes" style="width:80px" placeholder="Display"disabled value="<? echo $garments_item[$row[csf('gmts_item_id')]]; ?>" />
				<input type="hidden" name="txtGmtsItemId[]" id="txtGmtsItemId_<? echo $i;?>" style="width:50px" value="<? echo $row[csf('gmts_item_id')]; ?>" />
			</td>
			<td>	
				<input type="text" name="txtBodyPart[]" id="txtBodyPart_<? echo $i;?>" class="text_boxes" style="width:100px" placeholder="Display" disabled value="<? echo $body_part[$row[csf('body_part')]]; ?>" />
				<input type="hidden" name="txtBodyPartId[]" id="txtBodyPartId_<? echo $i;?>" style="width:50px" class="text_boxes" value="<? echo $row[csf('body_part')]; ?>" />
			</td>
			<td>
				<input type="text" name="txtEmblName[]" d="txtEmblName_<? echo $i;?>" class="text_boxes" style="width:100px" placeholder="Display" disabled value="<? echo $emblishment_name_array[$row[csf('main_process_id')]]; ?>" />
				<input type="hidden" name="txtEmblNameId[]" id="txtEmblNameId_<? echo $i;?>" value="<? echo $row[csf('main_process_id')]; ?>" />
			</td>
			<td>
				<input type="text" name="txtEmblType[]" id="txtEmblType_<? echo $i;?>" class="text_boxes" style="width:100px" placeholder="Display" disabled value="<? echo $new_subprocess_array[$row[csf('embl_type')]]; ?>"/>
				<input type="hidden" name="txtEmblTypeId[]" id="txtEmblTypeId_<? echo $i;?>" value="<? echo $row[csf('embl_type')]; ?>" />                        
			</td>
			<td>
				<input type="text" name="txtColor[]" id="txtColor_<? echo $i;?>" class="text_boxes"  style="width:70px" placeholder="Display" disabled value="<? echo $color_arr[$row[csf('color_id')]]; ?>"/>
				<input type="hidden" name="txtColorId[]" id="txtColorId_<? echo $i;?>" value="<? echo $row[csf('color_id')]; ?>" />
			</td>
			<td>
				<input type="text" name="txtSize[]" id="txtSize_<? echo $i;?>" class="text_boxes"  style="width:60px" placeholder="Display" disabled value="<? echo $size_arr[$row[csf('size_id')]]; ?>"/>
				<input type="hidden" name="txtSizeId[]" id="txtSizeId_<? echo $i;?>" value="<? echo $row[csf('size_id')]; ?>" />
			</td>
			<td><input type="text" name="txtOrdQty[]" id="txtOrdQty_<? echo $i;?>" class="text_boxes_numeric" style="width:60px" placeholder="Write" value="<? echo $order_qty; ?>" disabled /></td>
			<td><input type="text" name="txtIssueQty[]" id="txtIssueQty_<? echo $i;?>" class="text_boxes_numeric" style="width:60px" placeholder="Write" value="<? echo $issue_qty; ?>" disabled /></td>

			<td>
				<input name="txtpreqty[]" id="txtpreqty_<? echo $i; ?>" value="<? echo $preProd_qty; ?>" class="text_boxes_numeric" type="text" style="width:50px" disabled/>
			</td>
			<td>
				<input type="text" name="txtProdQty[]" id="txtProdQty_<? echo $i;?>" class="text_boxes_numeric" style="width:50px" placeholder="<? echo $balanceQty; ?>" value="<? echo $quantity; ?>" onKeyup="validate_prod_qty(<? echo $i;?>)" onBlur="fnc_total_calculate(); pre_pro_qty="<? echo $preProd_qty; ?>" order_qty="<? echo $order_qty; ?>"" />
			</td>
            <td><input type="text" name="txtRemarks[]" id="txtRemarks_<? echo $i;?>" class="text_boxes" style="width:60px" placeholder="Write" value="<? echo $remarks; ?>" /></td>
		</tr>
		<?
		$i++;
	}
	exit();
}

/*/Search Saved data/*/
if($action=="embel_production_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	//echo $cbo_company_id;
	?>
	  <script>
		function js_set_value( str) 
		{
			$('#hidden_production_data').val( str );
			//alert(str);return;
			parent.emailwindow.hide();
		}
		
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Embl. Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer Po');
			else if(val==5) $('#search_by_td').html('Buyer Style');
		}
	  </script>
    </head>
    <body>
		<div align="center" style="width:100%;" >
             <fieldset>
                <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
                    <table width="870" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                        <thead> 
                        	<tr>
                                <th colspan="8"><? echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --"); ?></th>
                            </tr>
                            <tr>              	 
                                <th width="150">Location</th>
                                <th width="100">Production ID</th>
                                <th width="100">Recipe No</th>
                                <th width="100">Search By</th>
                            	<th width="100" id="search_by_td">Embl. Job No</th>
                                <th width="130" colspan="2">Prod. Date Range</th>
                                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:90px;" /></th> 
                            </tr>          
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td><? echo create_drop_down( "cbo_location", 150, "select id, location_name from lib_location where company_id='$cbo_company_id' and status_active =1 and is_deleted=0 order by location_name ASC","id,location_name", 1, "-Select Location-", 0, "","","","","","",3 ); ?></td>
                                <td><input name="txt_prod_no" id="txt_prod_no" class="text_boxes" style="width:90px"></td>
                                <td><input name="txt_recipe_no" id="txt_recipe_no" class="text_boxes" style="width:90px"></td>
                                <td>
									<?
                                        $search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
                                        echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                                    ?>
                                </td>
                                <td>
                                    <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                                </td>
                                
                                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" placeholder="From Date" style="width:60px"></td>
                                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" style="width:60px"></td> 
                                <td align="center">
                                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( '<? echo $cbo_company_id; ?>'+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('txt_prod_no').value+'_'+document.getElementById('txt_recipe_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value, 'create_production_no_list_view', 'search_div', 'embl_production_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:90px;" />
                                    
                                     <input type="hidden" id="hidden_production_data" name="hidden_production_data" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="8" align="center" valign="middle">
                                    <? echo load_month_buttons(1);  ?>
                                </td>
                            </tr>
                        </tbody>
                    </table> 
                </form>
             </fieldset>   
             <div id="search_div" ></div>
		</div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="create_production_no_list_view")
{
	$data=explode('_',$data);
	$company_id=$data[0];
	$location_id=$data[1];
	$prod_no=$data[2];
	$recipe_no=$data[3];
	$date_from=$data[4];
	$date_to=$data[5];
	$search_type=$data[6];
	$search_by=str_replace("'","",$data[7]);
	$search_str=trim(str_replace("'","",$data[8]));
	
	if($db_type==0)
	{
		$start_date= change_date_format($date_from,'yyyy-mm-dd');
		$end_date= change_date_format($date_to,'yyyy-mm-dd');
	}
	else
	{
		$start_date= change_date_format($date_from, "", "",1) ;
		$end_date= change_date_format($date_to, "", "",1);
	}
	
	$date_cond = "";
	if ($start_date != "" && $end_date != "") 
	{
		if ($db_type == 0) 
		{
			$date_cond ="and b.production_date between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'";
		} 
		else 
		{
			$date_cond="and b.production_date between '".change_date_format($start_date, "yyyy-mm-dd", "-", 1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-", 1)."'";
		}
	} 
	
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
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
	}
	//echo $po_ids;
	if ($po_ids!="") $po_idsCond=" and a.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	
	$spo_ids='';
	if(($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2))
	{
		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.embellishment_job=b.job_no_mst $search_com_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
	}
	if ( $spo_ids!="") $spo_idsCond=" and a.order_id in ($spo_ids)"; else $spo_idsCond="";
	
	if($company_id==0) { echo "Select Company first"; die; }
	
	if($location_id !="0") $location_cond= "and a.location_id = $location_id"; else $location_cond= "";
	if($prod_no!="") $system_no_cond=" and a.sys_no like '%".trim($prod_no)."%'"; else $system_no_cond="";
	if($recipe_no!="") $recipe_no_cond=" and a.sys_no like '%".trim($recipe_no)."%'"; else $recipe_no_cond="";
	//echo "sdlkjklsdj";
	//if($data[4]!="" && $data[5]!="") $date_cond=" and product_date between '".$date_from."' and '".$date_to."'"; else $date_cond="";
	$order_arr=array();
	$order_sql = sql_select("SELECT a.embellishment_job, a.within_group, a.party_id, b.id, b.order_no, sum(c.qnty) as qty from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.embellishment_job=b.job_no_mst and b.id=c.mst_id and a.entry_form=204 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.embellishment_job, a.within_group, a.party_id, b.id, b.order_no");
	foreach($order_sql as $row)
	{
		$order_arr[$row[csf('id')]]['po']=$row[csf('order_no')];
		$order_arr[$row[csf('id')]]['within_group']=$row[csf('within_group')];
		$order_arr[$row[csf('id')]]['party_id']=$row[csf('party_id')];
		$order_arr[$row[csf('id')]]['qty']=$row[csf('qty')];
	}
	unset($order_sql);
	
	$recipe_arr = return_library_array("SELECT id, recipe_no from pro_recipe_entry_mst where status_active=1 and is_deleted=0", 'id', 'recipe_no');
	$pdate_cond=($db_type==2)? " TO_CHAR(production_hour,'HH24:MI') " : " TIME_FORMAT( production_hour, '%H:%i' ) ";
	$prodData=sql_select("select id, mst_id, color_size_id, production_date, $pdate_cond as production_hour, operator_name, shift_id from subcon_embel_production_dtls where status_active=1 and is_deleted=0");
	foreach($prodData as $row)
	{
		$prod_data_arr[$row[csf('mst_id')]]['production_date']=change_date_format($row[csf('production_date')]);
		$prod_data_arr[$row[csf('mst_id')]]['production_hour']=$row[csf('production_hour')];
		$prod_data_arr[$row[csf('mst_id')]]['operator_name']=$row[csf('operator_name')];
		$prod_data_arr[$row[csf('mst_id')]]['shift_id']=$row[csf('shift_id')];
	}
	
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
			<fieldset style="width:870px;margin-left:10px">
				<form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table" >
						<thead>
							<th width="30">SL</th>
							<th width="120">Production No</th>
                            <th width="70">Prod. Date</th>
                            <th width="100">Recip No</th>
                            <th width="120">Job NO</th>
                            <th width="110">Order</th>
                            <th width="110">Buyer Po</th>
            				<th>Buyer Style</th>
						</thead>
					</table>
					<div style="width:870px; overflow-y:scroll; max-height:300px;">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table" id="tbl_list_search" >
							<?
							$sql="SELECT a.id, a.prefix_no_num, a.sys_no, a.location_id, a.recipe_id, a.job_no, a.order_id, a.buyer_po_id, a.floor_id, a.machine_id, a.product_date,b.production_date from subcon_embel_production_mst a, subcon_embel_production_dtls b where a.id=b.mst_id and a.entry_form=222 and a.status_active = 1 and a.is_deleted = 0 and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' $location_cond $system_no_cond $recipe_no_cond $spo_idsCond $po_idsCond $date_cond order by a.id DESC";// $date_cond
							//echo $sql; die;
							$sql_res=sql_select($sql);

							$i=1; 
							foreach($sql_res as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$str_data="";
								
								$str_data=$row[csf('id')].'***'.$row[csf('sys_no')].'***'.$row[csf('location_id')].'***'.$row[csf('recipe_id')].'***'.$recipe_arr[$row[csf('recipe_id')]].'***'.$row[csf('job_no')].'***'.$row[csf('order_id')].'***'.$order_arr[$row[csf('order_id')]]['po'].'***'.$order_arr[$row[csf('order_id')]]['within_group'].'***'.$order_arr[$row[csf('order_id')]]['party_id'].'***'.$order_arr[$row[csf('order_id')]]['qty'].'***'.$prod_data_arr[$row[csf('id')]]['production_date'].'***'.$prod_data_arr[$row[csf('id')]]['production_hour'].'***'.$prod_data_arr[$row[csf('id')]]['operator_name'].'***'.$row[csf('buyer_po_id')].'***'.$buyer_po_arr[$row[csf("buyer_po_id")]]['po'].'***'.$buyer_po_arr[$row[csf("buyer_po_id")]]['style'].'***'.$prod_data_arr[$row[csf('id')]]['shift_id'].'***'.$row[csf('floor_id')].'***'.$row[csf('machine_id')];
								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $str_data; ?>')"> 
									<td width="30" align="center"><?php echo $i; ?></td>	
									<td width="120" align="center"><?php echo $row[csf('prefix_no_num')]; ?></td>
                                    <td width="70"><?php echo change_date_format($row[csf('production_date')]); ?>&nbsp;</td>
                                    <td width="100"><?php echo $recipe_arr[$row[csf('recipe_id')]]; ?></td>
                                    <td width="120"><?php echo $row[csf('job_no')]; ?></td>
                                    <td width="110" style="word-break:break-all"><?php echo $order_arr[$row[csf('order_id')]]['po']; ?></td>
                                    <td width="110" style="word-break:break-all"><?php echo $buyer_po_arr[$row[csf("buyer_po_id")]]['po']; ?>&nbsp;</td>
                                    <td style="word-break:break-all"><?php echo $buyer_po_arr[$row[csf("buyer_po_id")]]['style']; ?>&nbsp;</td>
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

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)   // Insert Here==============================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0) $year_cond=" and YEAR(insert_date)";	
		else if($db_type==2) $year_cond=" and TO_CHAR(insert_date,'YYYY')";	
		
		$new_return_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'EMBP', date("Y",time()), 5, "select id,prefix_no,prefix_no_num from  subcon_embel_production_mst where company_id=$cbo_company_id and entry_form=222 $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
		
		$id=return_next_id( "id", "subcon_embel_production_mst", 1 ) ; 
		$field_array="id, prefix_no, prefix_no_num, sys_no, company_id, location_id, recipe_id, job_no, order_id, buyer_po_id, floor_id, machine_id, inserted_by, insert_date, status_active, is_deleted, entry_form";
		$data_array="(".$id.",'".$new_return_no[1]."','".$new_return_no[2]."','".$new_return_no[0]."',".$cbo_company_id.",".$cbo_location.",".$txt_recipe_id.",".$txt_job_no.",".$txt_order_id.",".$txtbuyerPoId.",".$cbo_floor_id.",".$cbo_machine_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,222)"; 
		
		$production_no=$new_return_no[0];
		
		$data_array_dtls=""; $issave=1;
		$id_dtls=return_next_id( "id", "subcon_embel_production_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, color_size_id, po_id, job_no, production_date, production_hour, qcpass_qty, operator_name, shift_id, remarks, inserted_by, insert_date, status_active, is_deleted";
		//echo "10**"; //die; 
		for($i=1;$i<=$total_row;$i++)
		{
			$colorSizeId="colorSizeId_".$i; 
			$txtProdQty="txtProdQty_".$i;
			$txtRemarks="txtRemarks_".$i;
			$updateIdDtls="updateIdDtls_".$i;
			if($db_type==0)
			{
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$id_dtls.",".$id.",'".$$colorSizeId."',".$txt_order_id.",".$txt_job_no.",".$txt_prod_date.",".$txt_reporting_hour.",'".$$txtProdQty."',".$txt_super_visor.",".$cboShift.",'".$$txtRemarks."','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."',1,0)"; 
					
				$id_dtls=$id_dtls+1;
				$issave=0;
			}
			else
			{
				$txt_hour="";
				$txt_hour=str_replace("'","",$txt_prod_date)." ".str_replace("'","",$txt_reporting_hour);
				$txtreporting_hour="to_date('".$txt_hour."','DD MONTH YYYY HH24:MI:SS')";
				
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="INSERT INTO subcon_embel_production_dtls (".$field_array_dtls.") VALUES (".$id_dtls.",".$id.",'".$$colorSizeId."',".$txt_order_id.",".$txt_job_no.",".$txt_prod_date.",".$txtreporting_hour.",'".$$txtProdQty."',".$txt_super_visor.",".$cboShift.",'".$$txtRemarks."','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."',1,0)"; 
				$id_dtls=$id_dtls+1;
				if($issave==1)
				{
					$issave=execute_query($data_array_dtls);
					$data_array_dtls='';
				}
				else
				{
					$issave=0;
				}
				//echo $issave;
			}
		}
		//die;
		//echo "10**insert into subcon_embel_production_mst ($field_array) values $data_array "; die;
		//echo "10**insert into subcon_embel_production_dtls ($field_array_dtls) values $data_array_dtls "; die;
		$flag=1;
		if($issave==1)
		{
			$rID=sql_insert("subcon_embel_production_mst",$field_array,$data_array,0);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
			if($db_type==0)
			{
				$rID1=sql_insert("subcon_embel_production_dtls",$field_array_dtls,$data_array_dtls,0);
				if($rID1==1 && $flag==1) $flag=1; else $flag=0;
			}
			else if($db_type==2)
			{
				$rID1=$issave;
				if($rID1==1 && $flag==1) $flag=1; else $flag=0;
			}
		}
		
		//echo "10**".$rID."**".$rID1; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$id)."**".$production_no."**".str_replace("'",'',$txt_job_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$id)."**".$production_no."**".str_replace("'",'',$txt_job_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".$production_no."**".str_replace("'",'',$txt_job_no);
			}
		}	
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here============================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$qc_no=return_field_value( "sys_no", "subcon_embel_production_mst"," recipe_id=$update_id and status_active=1 and is_deleted=0 and entry_form=223");
		if($qc_no){
			echo "emblQc**".str_replace("'","",$txt_production_id)."**".$qc_no;
			die;
		}
		
		$prod_sql_dtls="Select id from subcon_embel_production_dtls where mst_id=$update_id and status_active=1 and is_deleted=0";
		$all_dtls_id_arr=array();
		//echo "10**".$rec_sql_dtls; die;
		$nameArray=sql_select( $prod_sql_dtls ); 
		foreach($nameArray as $row)
		{
			$all_dtls_id_arr[]=$row[csf('id')];
		}
		unset($nameArray);
		
		$field_array_update="location_id*recipe_id*job_no*order_id*buyer_po_id*floor_id*machine_id*updated_by*update_date";
		$data_array_update="".$cbo_location."*".$txt_recipe_id."*".$txt_job_no."*".$txt_order_id."*".$txtbuyerPoId."*".$cbo_floor_id."*".$cbo_machine_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 		
		
		$data_array_dtls_update="";
		$field_array_dtls_update="color_size_id*po_id*job_no*production_date*production_hour*qcpass_qty*operator_name*shift_id*remarks*updated_by*update_date";
		
		$production_no=str_replace("'","",$txt_production_id);
		
		$data_array_dtls="";
		$id_dtls=return_next_id( "id", "subcon_embel_production_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, color_size_id, po_id, job_no, production_date, production_hour, qcpass_qty, operator_name, shift_id, remarks, inserted_by, insert_date, status_active, is_deleted";
		$issave=1; //echo "10**";
		for($i=1;$i<=$total_row;$i++)
		{
			$colorSizeId="colorSizeId_".$i; 
			$txtProdQty="txtProdQty_".$i;
			$txtRemarks="txtRemarks_".$i;
			$updateIdDtls="updateIdDtls_".$i;
			
			$updateIds = str_replace("'","",$$updateIdDtls);
			
			if($db_type==2)
			{
				$txt_hour="";
				$txt_hour=str_replace("'","",$txt_prod_date)." ".str_replace("'","",$txt_reporting_hour);
				$txtreportingHour="to_date('".$txt_hour."','DD MONTH YYYY HH24:MI:SS')";
			}
			else
			{
				$txtreportingHour=$txt_reporting_hour;
			}
			if( $updateIds != "")
			{
				$updateIdDtls_array[]=$updateIds;
				//if($data_array_dtls_update != "") $data_array_dtls_update .= ","; 	
				$data_array_dtls_update[$updateIds] = explode("*",("'".$$colorSizeId."'*".$txt_order_id."*".$txt_job_no."*".$txt_prod_date."*".$txtreportingHour."*'".$$txtProdQty."'*".$txt_super_visor."*".$cboShift."*'".$$txtRemarks."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				$issave=1;
				$id_arr_pro[]=$updateIds;
				
			}else{
				
				if($db_type==0)
				{
					if($data_array_dtls!="") $data_array_dtls.=",";
					$data_array_dtls.="(".$id_dtls.",".$update_id.",'".$$colorSizeId."',".$txt_order_id.",".$txt_job_no.",".$txt_prod_date.",".$txtreportingHour.",'".$$txtProdQty."',".$txt_super_visor.",".$cboShift.",'".$cboShift."','".$$txtRemarks."','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."',1,0)"; 
						
					$id_dtls=$id_dtls+1;
					$issave=0;
					$id_arr_pro[]=$id_dtls;
				}
				else
				{
					$txt_hour="";
					$txt_hour=str_replace("'","",$txt_prod_date)." ".str_replace("'","",$txt_reporting_hour);
					$txtreporting_hour="to_date('".$txt_hour."','DD MONTH YYYY HH24:MI:SS')";
					
					if($data_array_dtls!="") $data_array_dtls.=",";
					$data_array_dtls.="INSERT INTO subcon_embel_production_dtls (".$field_array_dtls.") VALUES (".$id_dtls.",".$update_id.",'".$$colorSizeId."',".$txt_order_id.",".$txt_job_no.",".$txt_prod_date.",".$txtreporting_hour.",'".$$txtProdQty."',".$txt_super_visor.",".$cboShift.",'".$$txtRemarks."','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."',1,0)"; 
					$id_dtls=$id_dtls+1;
					$id_arr_pro[]=$id_dtls;
					if($issave==1)
					{
						//echo $data_array_dtls;
						$issave=execute_query($data_array_dtls);
						$data_array_dtls="";
					}
					else $issave=0;
					//echo $issave;
				}
			}
		}
		//echo "10**".bulk_update_sql_statement("subcon_embel_production_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $updateIdDtls_array); die;
		//echo "10**insert into subcon_embel_production_dtls ($field_array_dtls) values $data_array_dtls "; die;
		//echo "10**".$data_array_dtls;die;
		$flag=1;
		if($issave==1)
		{
			$rID=sql_update("subcon_embel_production_mst",$field_array_update,$data_array_update,"id",$update_id,0);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
			if($data_array_dtls_update !=""){
				$rID2=execute_query(bulk_update_sql_statement( "subcon_embel_production_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $updateIdDtls_array ),1);
				if($rID2==1 && $flag==1) $flag=1; else $flag=0;
			}
		}
		else
		{
			if($db_type==0)
			{
				if($data_array_dtls !=""){
					$rID1=sql_insert("subcon_embel_production_dtls",$field_array_dtls,$data_array_dtls,1);
					if($rID1==1 && $flag==1) $flag=1; else $flag=0;
				}
			}
			else if($db_type==2)
			{
				$rID1=$issave;
				if($rID1==1 && $flag==1) $flag=1; else $flag=0;
			}
		}
		
		$field_arr_del="status_active*is_deleted*updated_by*update_date";
		$data_arr_del="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$distance_delete_id="";
	
		if(implode(',',$id_arr_pro)!="")
		{
			$distance_delete_id=implode(',',array_diff($all_dtls_id_arr,$id_arr_pro));
		}
		else
		{
			$distance_delete_id=implode(',',$all_dtls_id_arr);
		}
		if(str_replace("'",'',$distance_delete_id)!="")
		{
			$ex_delete_id=explode(",",$distance_delete_id);
			foreach($ex_delete_id as $id_val)
			{
				$rID3=sql_update("subcon_embel_production_dtls",$field_arr_del,$data_arr_del,"id","".$id_val."",1);
				if($rID3==1 && $flag==1) $flag=1; else $flag=0;
			}
		}
		
		//echo "10**".$rID."**".$rID1."**".$rID2."**".$rID3;
		//die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_id)."**".$production_no."**".str_replace("'",'',$txt_job_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$update_id)."**".$production_no."**".str_replace("'",'',$txt_job_no);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$update_id)."**".$production_no."**".str_replace("'",'',$txt_job_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id)."**".$production_no."**".str_replace("'",'',$txt_job_no);
			}
		}	
		disconnect($con);
 		die;
	}
	else if ($operation==2)   // Delete Here ============================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$qc_no=return_field_value( "sys_no", "subcon_embel_production_mst"," recipe_id=$update_id and status_active=1 and is_deleted=0 and entry_form=223");
		if($qc_no){
			echo "emblQc**".str_replace("'","",$txt_production_id)."**".$qc_no;
			die;
		}
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$flag=1;
		$rID=sql_delete("subcon_embel_production_mst",$field_array,$data_array,"id","".$update_id."",1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		$rID1=sql_delete("subcon_embel_production_dtls",$field_array,$data_array,"mst_id","".$update_id."",1);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		//echo "10**".$rID."**".$rID1; die;	
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$update_id);
			}
		}
		else if($db_type==2)
		{
			if ($flag==1) {
                oci_commit($con);
                echo "2**" .str_replace("'",'',$update_id);
            } else {
                oci_rollback($con);
                echo "10**" .str_replace("'",'',$update_id);
            }
		}
		disconnect($con);
		die;
	}
}

if($action=="embl_production_entry_print")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_library = return_library_array("SELECT id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
	$buyer_library = return_library_array("SELECT id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
	$size_arr=return_library_array( "SELECT id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
	$color_arr = return_library_array("SELECT id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$imge_arr=return_library_array( "SELECT master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$recipe_arr = return_library_array("SELECT id, recipe_no from pro_recipe_entry_mst where status_active=1 and is_deleted=0", 'id', 'recipe_no');
	$location_arr = return_library_array("SELECT id, location_name from lib_location where status_active=1 and is_deleted=0", 'id', 'location_name');
	$order_arr=array();
	$order_sql = sql_select("SELECT a.embellishment_job, a.within_group, a.party_id, b.id, b.order_no, b.main_process_id, sum(c.qnty) as qty from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.embellishment_job=b.job_no_mst and b.id=c.mst_id and a.entry_form=204 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.embellishment_job, a.within_group, a.party_id, b.main_process_id, b.id, b.order_no");
	foreach($order_sql as $row)
	{
		$order_arr[$row[csf('id')]]['po']=$row[csf('order_no')];
		$order_arr[$row[csf('id')]]['within_group']=$row[csf('within_group')];
		$order_arr[$row[csf('id')]]['party_id']=$row[csf('party_id')];
		$order_arr[$row[csf('id')]]['qty']=$row[csf('qty')];
		$order_arr[$row[csf('id')]]['embl_name']=$row[csf('main_process_id')];
	}
	unset($order_sql);
	
	$buyer_po_arr=array();
	$po_sql ="SELECT a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and status_active=1 and is_deleted=0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	
	/*$cust_arr=array();
	$cust_buyer_style_array=sql_select("SELECT id, embellishment_job, order_id, order_no from subcon_ord_mst where entry_form=204 and status_active=1 and is_deleted=0");
	foreach ($cust_buyer_style_array as $cust_val) 
	{
		$cust_arr[$cust_val[csf('embellishment_job')]]['order_no']=$cust_val[csf('order_no')]; 
		$cust_arr[$cust_val[csf('embellishment_job')]]['job']=$cust_val[csf('embellishment_job')]; 
	}
	unset($cust_buyer_style_array);*/
	$sql="SELECT id, prefix_no_num, sys_no, location_id, recipe_id, job_no, order_id from subcon_embel_production_mst where entry_form=222 and status_active = 1 and is_deleted = 0 and company_id='$company_id' $location_cond $system_no_cond $recipe_no_cond order by id DESC";

	$recipe_arr = return_library_array("SELECT id, recipe_no from pro_recipe_entry_mst where status_active=1 and is_deleted=0", 'id', 'recipe_no');
	$pdate_cond=($db_type==2)? " TO_CHAR(production_hour,'HH24:MI') " : " TIME_FORMAT( production_hour, '%H:%i' ) ";
	$prod_data_arr=array(); $qty_data_arr=array();
	$prodData=sql_select("SELECT id, mst_id, color_size_id, production_date, $pdate_cond as production_hour, qcpass_qty, operator_name, shift_id, remarks from subcon_embel_production_dtls where status_active=1 and is_deleted=0 and mst_id='$data[1]'");
	foreach($prodData as $row)
	{
		$prod_data_arr[$row[csf('mst_id')]]['production_date']=change_date_format($row[csf('production_date')]);
		$prod_data_arr[$row[csf('mst_id')]]['production_hour']=$row[csf('production_hour')];
		$prod_data_arr[$row[csf('mst_id')]]['operator_name']=$row[csf('operator_name')];
		$prod_data_arr[$row[csf('mst_id')]]['shift_id']=$row[csf('shift_id')];
		
		$qty_data_arr[$row[csf('color_size_id')]]['id']=$row[csf('id')];
		$qty_data_arr[$row[csf('color_size_id')]]['qcpass_qty']=$row[csf('qcpass_qty')];
		$qty_data_arr[$row[csf('color_size_id')]]['remarks']=$row[csf('remarks')];
	}
	unset($prodData);
	
	$sql_mst = "SELECT id, prefix_no_num, sys_no, location_id, recipe_id, job_no, order_id, buyer_po_id from subcon_embel_production_mst where entry_form=222 and id='$data[1]' and status_active=1 and is_deleted=0";
	$dataArray = sql_select($sql_mst); $party_name="";
	if(  $order_arr[$dataArray[0][csf('order_id')]]['within_group'] ==1) $party_name=$company_library[$order_arr[$dataArray[0][csf('order_id')]]['party_id']];
	else if($order_arr[$dataArray[0][csf('order_id')]]['within_group']==2) $party_name=$buyer_library[$order_arr[$dataArray[0][csf('order_id')]]['party_id']];
	
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
                <td width="130"><strong>Production ID:</strong></td>
                <td width="175"><? echo $dataArray[0][csf('sys_no')]; ?></td>
                <td width="130"><strong>Party Name: </strong></td>
                <td width="175px"><? echo $party_name; ?></td>
                <td width="130"><strong>Production Date: </strong></td>
                <td width="175"><? echo change_date_format($prod_data_arr[$dataArray[0][csf('id')]]['production_date']); ?></td>
            </tr>
            <tr>
                <td><strong>Job No:</strong></td>
                <td><? echo $dataArray[0][csf('job_no')]; ?></td>
                <td><strong>Order No:</strong></td>
                <td><? echo $order_arr[$dataArray[0][csf('order_id')]]['po']; ?></td>
                <td><strong>Embel. Name:</strong></td>
                <td><? echo $emblishment_name_array[$order_arr[$dataArray[0][csf('order_id')]]['embl_name']]; ?></td>
            </tr>
            <tr>
                <td><strong>Recipe No:</strong></td>
                <td><? echo $recipe_arr[$dataArray[0][csf('recipe_id')]]; ?></td>
                <td><strong>Buyer Po:</strong></td>
                <td> <? echo $buyer_po_arr[$dataArray[0][csf('buyer_po_id')]]['po']; ?></td>
                <td><strong>Buyer Style:</strong></td>
                <td><? echo $buyer_po_arr[$dataArray[0][csf('buyer_po_id')]]['style']; ?></td>
            </tr>
            <tr>
            	<td><strong>Operator:</strong></td>
                <td><? echo $prod_data_arr[$dataArray[0][csf('id')]]['operator_name']; ?></td>
            	<td><strong>Shift:</strong></td>
                <td><? echo $shift_name[$prod_data_arr[$dataArray[0][csf('id')]]['shift_id']]; ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
        <br>
        <div style="width:100%;">
            <table align="right" cellspacing="0" width="930" border="1" rules="all" class="rpt_table">
                <thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
                    <th width="30">SL</th>
                    <th width="140">Gmts Item</th>
                    <th width="120">Body Part</th>
                    <th width="110">Process/ Type</th>
                    <th width="120">Color</th>
                    <th width="70">Size</th>
                    <th width="80">Production Qty (Pcs)</th>
                    <th>Remarks</th>
                </thead>
				<?
				
				$mst_id = $data[1];
				$com_id = $data[0];
				$job_no = $dataArray[0][csf('job_no')];
				$recipe_id = $dataArray[0][csf('recipe_id')];
				

				$sql= "SELECT  a.id, a.embellishment_job, b.id as order_id, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.id as color_size_id, c.color_id, c.size_id, sum(c.qnty) as qty from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, pro_recipe_entry_mst d where a.embellishment_job=b.job_no_mst and b.id=c.mst_id and a.embellishment_job=d.job_no and b.id=d.po_id 				
				and b.main_process_id=d.embl_name and b.gmts_item_id=d.gmts_item and b.embl_type=d.embl_type and b.body_part=d.body_part and c.color_id=d.color_id
				and a.entry_form=204 and d.entry_form=220 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and a.company_id='$com_id' and d.id='$recipe_id' group by a.id, a.embellishment_job, b.id,b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.id, c.color_id, c.size_id, d.recipe_no_prefix_num order by c.id ASC";
				//echo $sql; die;
				$sql_res=sql_select($sql);
				
 				$i=1; $grand_tot_qty=0;

				foreach ($sql_res as $row) 
				{
					if($row[csf('main_process_id')]==1) $new_subprocess_array= $emblishment_print_type;
					else if($row[csf('main_process_id')]==2) $new_subprocess_array= $emblishment_embroy_type;
					else if($row[csf('main_process_id')]==3) $new_subprocess_array= $emblishment_wash_type;
					else if($row[csf('main_process_id')]==4) $new_subprocess_array= $emblishment_spwork_type;
					else if($row[csf('main_process_id')]==5) $new_subprocess_array= $emblishment_gmts_type;
					else $new_subprocess_array=$blank_array;
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i; ?></td>
                        <td><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></td>
                        <td><? echo $body_part[$row[csf('body_part')]]; ?>&nbsp;</td>
                        <td><? echo $new_subprocess_array[$row[csf('embl_type')]]; ?>&nbsp;</td>
                        <td><? echo $color_arr[$row[csf('color_id')]]; ?>&nbsp;</td>
                        <td align="center"><? echo $size_arr[$row[csf('size_id')]]; ?>&nbsp;</td>
                        <td align="right"><? echo number_format($qty_data_arr[$row[csf('color_size_id')]]['qcpass_qty'], 2, '.', ''); ?>&nbsp;</td>
                        <td><? echo $qty_data_arr[$row[csf('color_size_id')]]['remarks']; ?>&nbsp;</td>
                    </tr>
					<?
					$i++;
					$grand_tot_qty+=$qty_data_arr[$row[csf('color_size_id')]]['qcpass_qty'];
				}
				?>
                <tr class="tbl_bottom">
                    <td align="right" colspan="6"><strong>Grand Total</strong></td>
                    <td align="right"><? echo number_format($grand_tot_qty, 2, '.', ''); ?>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
            
            <br>
			<? echo signature_table(140, $com_id, "930px"); ?>
        </div>
    </div>
	<?
	exit();
}

?>