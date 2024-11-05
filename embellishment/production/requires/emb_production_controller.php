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
	echo create_drop_down( "cbo_location", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-Select Location-", $selected, "load_drop_down('requires/emb_production_controller', document.getElementById('cbo_company_id').value+'__'+this.value, 'load_drop_down_floor', 'floor_td');","","","","","",3 );
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
		//echo "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=3 and b.company_id=$company_id and b.status_active=1 and b.is_deleted=0 and a.production_process=9 $location_cond group by a.id, a.floor_name order by a.floor_name";
		echo create_drop_down( "cbo_floor_id", 150, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=5 and b.company_id=$company_id and b.status_active=1 and b.is_deleted=0 and a.production_process=9 $location_cond group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "--Select Floor--", 0, "load_machine();","" );
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
		$sql="select id,concat(machine_no,'-',brand) as machine_name from lib_machine_name where category_id=5 and company_id=$company_id and status_active=1 and is_deleted=0 and is_locked=0 $floor_cond order by machine_name";
	}
	else if($db_type==2)
	{
		$sql="select id, machine_no || '-' || brand as machine_name from lib_machine_name where category_id=5 and company_id=$company_id and status_active=1 and is_deleted=0 and is_locked=0 $floor_cond order by machine_name";
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
	if(!$exdata[1])$exdata[1]=1;
	if($exdata[1]==1)
	{
		echo create_drop_down( "cbo_buyer_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Party--", $exdata[2], "");
	}
	else if($exdata[1]==2)
	{
		echo create_drop_down("cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$exdata[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buy.buyer_name", "id,buyer_name", 1, "--Select Party--", $exdata[2], "");
	}
	else
	{
		echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "--Select Party--", $selected, "" );
	}
	exit();
}

if ($action=="load_drop_down_buyer_pop")
{
	//echo $data; die;
	$data=explode('_',$data);
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_buyer_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "");
		exit();
	}
	else
	{
		echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$data[2], "" );
		exit();
	}
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( '<? echo $cbo_company_id; ?>'+'_'+document.getElementById('txt_search_recipe').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_year_selection').value, 'create_recipe_search_list_view', 'search_div', 'emb_production_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
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
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
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
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name" );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
	
	$production_qty_arr=array();
	$prod_data_arr="select a.recipe_id, sum(b.qcpass_qty) as qty from subcon_embel_production_mst a, subcon_embel_production_dtls b where a.id=b.mst_id and a.entry_form=222 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.recipe_id";
	$prod_data_res=sql_select($prod_data_arr);
	
	foreach($prod_data_res as $row)
	{
		$production_qty_arr[$row[csf('recipe_id')]]=$row[csf('qty')];
	}
	unset($prod_data_res);
	
	$po_sql ="Select a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
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
                            <th width="70">Emb. Name</th>
                            <th width="70">Emb. Type</th>
                            <th width="70">Color</th>
                            <th width="70">Order Qty</th>
                            <th width="70">Prod Qty</th>
                            <th>Balance Qty</th>
						</thead>
					</table>
					<div style="width:1070px; overflow-y:scroll; max-height:300px;">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1050" class="rpt_table" id="list_view" >
							<?
							$sql= "SELECT a.id, a.job_no_prefix_num, a.embellishment_job, a.party_id, a.within_group, b.id as order_id, b.order_no, b.buyer_po_id, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.color_id, sum(c.qnty) as qty, d.id as recipe_id, d.recipe_no_prefix_num, d.recipe_no from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, pro_recipe_entry_mst d where a.embellishment_job=b.job_no_mst and b.id=c.mst_id and b.job_no_mst=c.job_no_mst and a.embellishment_job=d.job_no and b.id=d.po_id and b.main_process_id=d.embl_name and b.gmts_item_id=d.gmts_item and b.embl_type=d.embl_type and b.body_part=d.body_part and c.color_id=d.color_id and a.entry_form=204 and d.entry_form=220 and a.status_active=1 and b.status_active=1 and d.status_active=1 and c.status_active=1 and c.is_deleted=0  $order_rcv_date $company $search_com_cond $search_recipe_cond $po_idsCond group by a.id, a.job_no_prefix_num, a.embellishment_job, a.party_id, a.within_group, b.id, b.order_no, b.buyer_po_id, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.color_id, d.id, d.recipe_no_prefix_num, d.recipe_no order by d.recipe_no_prefix_num DESC";
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

	$item_group_arr=return_library_array( "SELECT id,item_name from lib_item_group",'id','item_name');
	$color_arr=return_library_array( "SELECT id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name" );
	$size_arr=return_library_array( "SELECT id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
	
	$prod_data_arr=array(); //$recipe_prod_id_arr=array(); $product_data_arr=array();
	if($update_id!=0)
	{	
		$prodData=sql_select("SELECT id, color_size_id, production_date, production_hour, qcpass_qty, operator_name, shift_id, remarks from subcon_embel_production_dtls where mst_id=$update_id and status_active=1 and is_deleted=0");
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
	}
	//var_dump($recipe_prod_id_arr);

	$sql= "SELECT  a.id, a.job_no_prefix_num, a.embellishment_job, a.party_id, a.within_group, b.id as order_id, b.order_no, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, b.buyer_po_id, c.id as color_size_id, c.color_id, c.size_id, sum(c.qnty) as qty, d.id as recipe_id, d.recipe_no_prefix_num, d.recipe_no from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, pro_recipe_entry_mst d where a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and  b.id=c.mst_id and a.embellishment_job=d.job_no and b.id=d.po_id and b.main_process_id=d.embl_name and b.gmts_item_id=d.gmts_item and b.embl_type=d.embl_type and b.body_part=d.body_part and c.color_id=d.color_id and a.entry_form=311 and d.entry_form=312 and a.status_active=1 and c.status_active=1 and c.is_deleted=0  and a.company_id='$company_id' and d.id='$recipe_id' group by a.id, a.job_no_prefix_num, a.embellishment_job, a.party_id, a.within_group, b.id, b.order_no, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, b.buyer_po_id, c.id, c.color_id, c.size_id, d.id, d.recipe_no_prefix_num, d.recipe_no order by c.id ASC";
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
		
		$upid=$prod_data_arr[$row[csf('color_size_id')]]['id'];
		$qcpass_qty=$prod_data_arr[$row[csf('color_size_id')]]['qcpass_qty'];
		$operator_name=$prod_data_arr[$row[csf('color_size_id')]]['operator_name'];
		$shift_id=$prod_data_arr[$row[csf('color_size_id')]]['shift_id'];
		$remarks=$prod_data_arr[$row[csf('color_size_id')]]['remarks'];
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
			<td><input name="txtordqty_<? echo $k; ?>" id="txtordqty_<? echo $k; ?>" value="<? echo $orderQty; ?>" class="text_boxes_numeric" type="text" style="width:50px" disabled/></td>
			<td><input type="text" name="txtProdQty[]" id="txtProdQty_<? echo $i;?>" class="text_boxes_numeric" style="width:50px" placeholder="Write" value="<? echo $qcpass_qty; ?>" onBlur="fnc_total_calculate();" /></td>
            <td><input type="text" name="txtRemarks[]" id="txtRemarks_<? echo $i;?>" class="text_boxes" style="width:60px" placeholder="Write" value="<? echo $remarks; ?>" /></td>
		</tr>
		<?
		$i++;
	}
	exit();
}

/*if($action=="load_php_dtls_form")
{
	//echo $data;
	$exdata=explode("**",$data);
	$jobno=''; $update_id=0;
	$update_id=$exdata[0];
	$jobno=$exdata[1];
	$color_arrey=return_library_array( "select id,color_name from lib_color",'id','color_name');
	$size_arrey=return_library_array( "select id,size_name from  lib_size",'id','size_name');
	$buyer_po_arr=array();
	
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	$updtls_data_arr=array(); $pre_qty_arr=array();
	
	$sql_rec="select a.id, a.mst_id, a.quantity, a.uom, a.job_dtls_id, a.job_break_id, a.buyer_po_id, a.remarks from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.embl_job_no='$jobno' and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_rec_res =sql_select($sql_rec);
	
	foreach ($sql_rec_res as $row)
	{
		if($row[csf("mst_id")]==$update_id)
		{
			$updtls_data_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['dtlsid']=$row[csf("id")];
			$updtls_data_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['qty']=$row[csf("quantity")];
			$updtls_data_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['remarks']=$row[csf("remarks")];
		}
		else
		{
			$pre_qty_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['qty']+=$row[csf("quantity")];
		}
	}
	
	$sql_job="select a.id, a.embellishment_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, b.main_process_id, b.order_uom, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
		where a.entry_form=311 and a.embellishment_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id=c.mst_id and a.embellishment_job='$jobno' order by c.id ASC";
	//echo $sql_job;
	
	$sql_result =sql_select($sql_job);
	$k=0;
	$num_rowss=count($sql_result);
	foreach ($sql_result as $row)
	{
		$k++;
		
		if($row[csf("main_process_id")]==1) $emb_type=$emblishment_print_type;
		else if($row[csf("main_process_id")]==2) $emb_type=$emblishment_embroy_type;
		else if($row[csf("main_process_id")]==3) $emb_type=$emblishment_wash_type;
		else if($row[csf("main_process_id")]==4) $emb_type=$emblishment_spwork_type;
		else if($row[csf("main_process_id")]==5) $emb_type=$emblishment_gmts_type;
		else $emb_type="";
		
		$quantity=0; $dtlsup_id=""; $balanceQty=0; $prerec_qty=0; $orderQty=0; $remarks='';
		$prerec_qty=$pre_qty_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
		$orderQty=number_format($row[csf("qnty")]*12,4,'.','');
		$balanceQty=number_format($orderQty-$prerec_qty,4,'.','');
		
		if($update_id!=0)
		{
			$quantity=$updtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
			$remarks=$updtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['remarks'];
		}
		else $quantity='';
		if($quantity==0) $quantity='';
		if($balanceQty==0) $balanceQty=0;
		
		$dtlsup_id=$updtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['dtlsid'];
		?>
		 <tr>
            <td><input type="hidden" name="ordernoid_<? echo $k; ?>" id="ordernoid_<? echo $k; ?>" value="<? echo $row[csf("po_id")]; ?>">
                <input type="hidden" name="jobno_<? echo $k; ?>" id="jobno_<? echo $k; ?>" value="<? echo $row[csf("embellishment_job")]; ?>">
                <input type="hidden" name="updatedtlsid_<? echo $k; ?>" id="updatedtlsid_<? echo $k; ?>" value="<? echo $dtlsup_id; ?>">
                <input type="hidden" name="breakdownid_<? echo $k; ?>" id="breakdownid_<? echo $k; ?>" value="<? echo $row[csf("breakdown_id")]; ?>">
                <input type="text" name="txtorderno_<? echo $k; ?>" id="txtorderno_<? echo $k; ?>" class="text_boxes" style="width:90px" value="<? echo $row[csf("order_no")]; ?>" readonly />
            </td>
            <td><input name="txtbuyerPo_<? echo $k; ?>" id="txtbuyerPo_<? echo $k; ?>" type="text" class="text_boxes" style="width:90px" value="<? echo $buyer_po_arr[$row[csf("buyer_po_id")]]['po']; ?>" readonly />
                <input name="txtbuyerPoId_<? echo $k; ?>" id="txtbuyerPoId_<? echo $k; ?>" type="hidden" class="text_boxes" style="width:70px" value="<? echo $row[csf("buyer_po_id")]; ?>" />
            </td>
            <td><input name="txtstyleRef_<? echo $k; ?>" id="txtstyleRef_<? echo $k; ?>" type="text" class="text_boxes" style="width:90px" value="<? echo $buyer_po_arr[$row[csf("buyer_po_id")]]['style']; ?>" readonly /></td>
            <td><? echo create_drop_down( "cboProcessName_".$k, 80, $emblishment_name_array,"", 1, "--Select--",$row[csf("main_process_id")],"", 1,"" ); ?></td>
            <td id="reType_<? echo $k; ?>"><? echo create_drop_down( "cboReType_".$k, 80, $emb_type,"", 1, "Select Item", $row[csf("embl_type")], "",1); ?></td>
            <td><? echo create_drop_down( "cboGmtsItem_".$k, 90, $garments_item,"", 1, "-- Select --",$row[csf("gmts_item_id")], "",1,"" ); ?></td>
            <td><? echo create_drop_down( "cboBodyPart_".$k, 90, $body_part,"", 1, "-- Select --",$row[csf("body_part")], "",1,"" ); ?></td>
            <td>
                <input type="text" id="txtmaterialdescription_<? echo $k; ?>" name="txtmaterialdescription_<? echo $k; ?>" class="text_boxes" style="width:110px" value="<? echo $row[csf("description")]; ?>" readonly title="Maximum 200 Character" >
            </td>
            <td><input type="text" id="txtcolor_<? echo $k; ?>" name="txtcolor_<? echo $k; ?>" class="text_boxes" value="<? echo $color_arrey[$row[csf("color_id")]]; ?>" style="width:50px" readonly/></td>
            <td><input type="text" id="txtsize_<? echo $k; ?>" name="txtsize_<? echo $k; ?>" class="text_boxes" style="width:50px" value="<? echo $size_arrey[$row[csf("size_id")]]; ?>" readonly/></td>
            <td><input name="txtordqty_<? echo $k; ?>" id="txtordqty_<? echo $k; ?>" value="<? echo $orderQty; ?>" class="text_boxes_numeric" type="text" style="width:50px" disabled/></td>
            <td><input name="txtpreqty_<? echo $k; ?>" id="txtpreqty_<? echo $k; ?>" value="<? echo $prerec_qty; ?>" class="text_boxes_numeric" type="text" style="width:50px" disabled/></td>
            <td><input name="txtreceiveqty_<? echo $k; ?>" id="txtreceiveqty_<? echo $k; ?>" class="text_boxes_numeric" type="text" onKeyUp="check_receive_qty_ability(this.value,<? echo $k; ?>); fnc_total_calculate();" value="<? echo $quantity; ?>" placeholder="<? echo $balanceQty; ?>" pre_rec_qty="<? echo $prerec_qty; ?>" order_qty="<? echo $orderQty; ?>" style="width:50px" /></td>
            <td><? echo create_drop_down( "cbouom_".$k,50, $unit_of_measurement,"", 1, "-Select-",1,"", 1,"" );?></td>
            <td><input type="text" name="txtremarks_<? echo $k; ?>" id="txtremarks_<? echo $k; ?>" style="width:40px" value="<? echo $remarks; ?>" class="text_boxes" placeholder="Remark" onClick="openmypage_remarks(<? echo $k; ?>);" /></td>
            <td><input type="text" name="txtremarks_<? echo $k; ?>" id="txtremarks_<? echo $k; ?>" style="width:40px" value="<? echo $remarks; ?>" class="text_boxes" placeholder="Remark" onClick="openmypage_remarks(<? echo $k; ?>);" /></td>
        </tr>
	<?	
	}
	exit();
}*/

/*/Search Saved data/*/
if($action=="embel_production_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	//echo $cbo_company_id;
	
	$data=explode("_",$data);
	
	//print_r($data);
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
                    <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                        <thead> 
                        	<tr>
                                <th colspan="8"><? echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --"); ?></th>
                            </tr>
                            <tr>              	 
                                <th width="150">Location</th>
                                <th width="100">Production ID</th>
                                <th width="100" style="display:none">Recipe No</th>
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
                                <td style="display:none"><input name="txt_recipe_no" id="txt_recipe_no" class="text_boxes" style="width:90px"></td>
                                <td>
									<?
                                        $search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style", 6=>"IR/IB");
                                        echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                                    ?>
                                </td>
                                <td>
                                    <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                                </td>
                                
                                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" placeholder="From Date" style="width:60px"></td>
                                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" style="width:60px"></td> 
                                <td align="center">
                                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( '<? echo  $data[0]; ?>'+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('txt_prod_no').value+'_'+document.getElementById('txt_recipe_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_year_selection').value+'_'+'<? echo  $data[1]; ?>', 'create_production_no_list_view', 'search_div', 'emb_production_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:90px;" />
                                    
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
	//echo '<pre>';print_r($data);
	$company_id=$data[0];
	$location_id=$data[1];
	$prod_no=$data[2];
	$recipe_no=$data[3];
	$date_from=$data[4];
	$date_to=$data[5];
	$search_type=$data[6];
	$search_by=str_replace("'","",$data[7]);
	$search_str=trim(str_replace("'","",$data[8]));
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[9]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[9]";}
	
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
	

	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";$within_no_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $within_no_cond="and d.job_no_prefix_num='$search_str'";
			else if($search_by==2) $within_no_cond="and d.order_no='$search_str'";
			
			if ($search_by==3) $within_no_cond=" and d.job_no_prefix_num = '$search_str' ";
			//else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
			//else if ($search_by==5) $style_cond=" and a.style_ref_no = '$search_str' ";
			
			else if ($search_by==4) $within_no_cond=" and c.buyer_po_no = '$search_str' ";
			else if ($search_by==5) $within_no_cond=" and c.buyer_style_ref = '$search_str' ";
			else if ($search_by==6) $grouping=" and e.grouping = '$search_str' ";
			
		}
		if($prod_no!="") $system_no_cond=" and a.prefix_no_num='".trim($prod_no)."'"; else $system_no_cond="";
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $within_no_cond="and d.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $within_no_cond="and d.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and d.job_no_prefix_num like '%$search_str%'";  
			//else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str%'"; 
			//else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str%'";  
			
			else if ($search_by==4) $within_no_cond=" and c.buyer_po_no like '%$search_str%' ";
			else if ($search_by==5) $within_no_cond=" and c.buyer_style_ref like '%$search_str%' "; 
			else if ($search_by==6) $grouping=" and e.grouping like '%$search_str%' ";
			
		}
		if($prod_no!="") $system_no_cond=" and a.prefix_no_num like '%".trim($prod_no)."%'"; else $system_no_cond="";
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $within_no_cond="and d.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $within_no_cond="and d.order_no like '$search_str%'";  
			
			if ($search_by==3) $within_no_cond=" and d.job_no_prefix_num like '$search_str%'";  
			//else if ($search_by==4) $po_cond=" and b.po_number like '$search_str%'"; 
			//else if ($search_by==5) $style_cond=" and a.style_ref_no like '$search_str%'";  
			else if ($search_by==4) $within_no_cond=" and c.buyer_po_no like '$search_str%' ";
			else if ($search_by==5) $within_no_cond=" and c.buyer_style_ref like '$search_str%' ";  
			else if ($search_by==6) $grouping=" and e.grouping like '$search_str%' ";
			
		}
		if($prod_no!="") $system_no_cond=" and a.prefix_no_num like '".trim($prod_no)."%'"; else $system_no_cond="";
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $within_no_cond="and d.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $within_no_cond="and d.order_no like '%$search_str'";  
			
			if ($search_by==3) $within_no_cond=" and d.job_no_prefix_num like '%$search_str'";  
			//else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str'";  
			//else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str'"; 
			else if ($search_by==4) $within_no_cond=" and c.buyer_po_no like '%$search_str' ";
			else if ($search_by==5) $within_no_cond=" and c.buyer_style_ref like '%$search_str' ";  
			else if ($search_by==6) $grouping=" and e.grouping like '%$search_str' ";
			
		}
		if($prod_no!="") $system_no_cond=" and a.prefix_no_num like '%".trim($prod_no)."'"; else $system_no_cond="";
	}
	
	
	if($company_id==0) { echo "Select Company first"; die; }
	
	if($location_id !="0") $location_cond= "and a.location_id = $location_id"; else $location_cond= "";
	
	//if($recipe_no!="") $recipe_no_cond=" and a.sys_no like '%".trim($recipe_no)."%'"; else $recipe_no_cond="";
	
	$within_group =$data[10];
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	//echo "sdlkjklsdj";
	//if($data[4]!="" && $data[5]!="") $date_cond=" and product_date between '".$date_from."' and '".$date_to."'"; else $date_cond="";
	$order_arr=array();
	
	$order_sql = sql_select("SELECT a.embellishment_job, a.within_group, a.party_id, b.id, b.order_no, sum(c.qnty) as qty, b.buyer_po_no,b. buyer_style_ref from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and a.entry_form=311 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by a.embellishment_job, a.within_group, a.party_id, b.id, b.order_no, b.buyer_po_no,b. buyer_style_ref");
	foreach($order_sql as $row)
	{
		$order_arr[$row[csf('id')]]['po']=$row[csf('order_no')];
		$order_arr[$row[csf('id')]]['within_group']=$row[csf('within_group')];
		$order_arr[$row[csf('id')]]['party_id']=$row[csf('party_id')];
		$order_arr[$row[csf('id')]]['qty']=$row[csf('qty')];
		$order_arr[$row[csf('id')]]['buyer_po_no']=$row[csf('buyer_po_no')];
		$order_arr[$row[csf('id')]]['buyer_style_ref']=$row[csf('buyer_style_ref')];
	}
	unset($order_sql);
	
	
	$recipe_arr = return_library_array("select id, recipe_no from pro_recipe_entry_mst", 'id', 'recipe_no');
	
	
	$pdate_cond=($db_type==2 ? " TO_CHAR(production_hour,'HH24:MI') " : " TIME_FORMAT( production_hour, '%H:%i' ) ");
	
	
		if ($db_type == 0) 
		{
			$pdate_cond="TIME_FORMAT( production_hour, '%H:%i')";
		} 
		else 
		{
			$pdate_cond="TO_CHAR(production_hour,'HH24:MI')";
		}
	
	
	
	//echo $pdate_cond; die;
	
	$prodData=sql_select("select id, mst_id, color_size_id, production_date, $pdate_cond as production_hour, operator_name, shift_id from subcon_embel_production_dtls where status_active=1 and is_deleted=0");
	
	
	
	foreach($prodData as $row)
	{
		$prod_data_arr[$row[csf('mst_id')]]['production_date']=change_date_format($row[csf('production_date')]);
		$prod_data_arr[$row[csf('mst_id')]]['production_hour']=$row[csf('production_hour')];
		$prod_data_arr[$row[csf('mst_id')]]['operator_name']=$row[csf('operator_name')];
		$prod_data_arr[$row[csf('mst_id')]]['shift_id']=$row[csf('shift_id')];
	}
	
	$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	//print_r($buyer_po_arr);
	
	
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
                            <th width="120">Job NO</th>
                            <th width="110">Order</th>
                            <th width="110">Buyer Po</th>
                            <th width="100">IR/IB</th>
            				<th>Buyer Style</th>
						</thead>
					</table>
					<div style="width:870px; overflow-y:scroll; max-height:300px;">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table" id="tbl_list_search" >
							<?
							
							if ($db_type == 0) 
							{
								//$pdate_cond="TIME_FORMAT( production_hour, '%H:%i')";
								$production_hour="TIME_FORMAT(b.production_hour, 'hh:mi:ss') as production_hour";
							} 
							else 
							{
								//$pdate_cond="TO_CHAR(production_hour,'HH24:MI')";
								$production_hour="to_char(b.production_hour, 'hh:mi:ss') as production_hour";
							}
							
							if($within_group == 1){
								$sql="SELECT  $production_hour,b.shift_id,a.id, a.prefix_no_num, a.sys_no, a.location_id, a.recipe_id, a.job_no, b.po_id as order_id,e.grouping, b.buyer_po_id, a.floor_id, a.machine_id, a.product_date,b.production_date from subcon_embel_production_mst a, subcon_embel_production_dtls b,subcon_ord_dtls c,subcon_ord_mst d, wo_po_break_down e where e.id=c.buyer_po_id and a.id=b.mst_id and d.id=c.mst_id and b.po_id=c.id and a.entry_form=315 and a.status_active = 1 and a.is_deleted = 0 and a.company_id='$company_id' $location_cond $system_no_cond $recipe_no_cond $spo_idsCond $po_idsCond $date_cond $year_cond $within_no_cond $grouping group by  b.production_hour,b.shift_id,e.grouping, a.id, a.prefix_no_num, a.sys_no, a.location_id, a.recipe_id, a.job_no, b.po_id  , b.buyer_po_id, a.floor_id, a.machine_id, a.product_date,b.production_date  order by a.id DESC";// $date_cond
							}else{
								$sql="SELECT  $production_hour,b.shift_id,a.id, a.prefix_no_num, a.sys_no, a.location_id, a.recipe_id, a.job_no, b.po_id as order_id, b.buyer_po_id, a.floor_id, a.machine_id, a.product_date,b.production_date from subcon_embel_production_mst a, subcon_embel_production_dtls b,subcon_ord_dtls c,subcon_ord_mst d where a.id=b.mst_id and d.id=c.mst_id and b.po_id=c.id and a.entry_form=315 and a.status_active = 1 and a.is_deleted = 0 and a.company_id='$company_id' $location_cond $system_no_cond $recipe_no_cond $spo_idsCond $po_idsCond $date_cond $year_cond $within_no_cond group by  b.production_hour,b.shift_id, a.id, a.prefix_no_num, a.sys_no, a.location_id, a.recipe_id, a.job_no, b.po_id  , b.buyer_po_id, a.floor_id, a.machine_id, a.product_date,b.production_date  order by a.id DESC";// $date_cond
							}
							 
							 
							//echo $sql;die;
							$sql_res=sql_select($sql);

							$i=1; 
							foreach($sql_res as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$str_data="";
								$hour_string= $row[csf('production_hour')];
								//print_r($hour_ex);
								
								
								
									if($order_arr[$row[csf('order_id')]]['within_group']==1)
									{
										$buyer_po=$buyer_po_arr[$row[csf("buyer_po_id")]]['po'];
										$buyer_style=$buyer_po_arr[$row[csf("buyer_po_id")]]['style'];
									}
									if($order_arr[$row[csf('order_id')]]['within_group']==2)
									{
										$buyer_po=$order_arr[$row[csf('order_id')]]['buyer_po_no'];
										$buyer_style=$order_arr[$row[csf('order_id')]]['buyer_style_ref'];
									}
									
								
								$str_data=$row[csf('id')].'***'.$row[csf('sys_no')].'***'.$row[csf('location_id')].'***'.$row[csf('recipe_id')].'***'.$recipe_arr[$row[csf('recipe_id')]].'***'.$row[csf('job_no')].'***'.$row[csf('order_id')].'***'.$order_arr[$row[csf('order_id')]]['po'].'***'.$order_arr[$row[csf('order_id')]]['within_group'].'***'.$order_arr[$row[csf('order_id')]]['party_id'].'***'.$order_arr[$row[csf('order_id')]]['qty'].'***'.$prod_data_arr[$row[csf('id')]]['production_date'].'***'.$prod_data_arr[$row[csf('id')]]['production_hour'].'***'.$prod_data_arr[$row[csf('id')]]['operator_name'].'***'.$row[csf('buyer_po_id')].'***'.$buyer_po_arr[$row[csf("buyer_po_id")]]['po'].'***'.$buyer_po_arr[$row[csf("buyer_po_id")]]['style'].'***'.$prod_data_arr[$row[csf('id')]]['shift_id'].'***'.$row[csf('floor_id')].'***'.$row[csf('machine_id')].'***'.$hour_string.'***'.$row[csf('shift_id')];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $str_data; ?>')"> 
									<td width="30" align="center"><?php echo $i; ?></td>	
									<td width="120" align="center"><?php echo $row[csf('prefix_no_num')]; ?></td>
                                    <td width="70"><?php echo change_date_format($row[csf('production_date')]); ?>&nbsp;</td>
                                    <td width="120"><?php echo $row[csf('job_no')]; ?></td>
                                    <td width="110" style="word-break:break-all"><?php echo $order_arr[$row[csf('order_id')]]['po']; ?></td>
                                    <td width="110" style="word-break:break-all"><?php echo $buyer_po;//$buyer_po_arr[$row[csf("buyer_po_id")]]['po']; ?>&nbsp;</td>
									<td width="100" style="word-break:break-all"><?php echo $row[csf('grouping')]; ?></td>
                                    <td style="word-break:break-all"><?php echo $buyer_style;//$buyer_po_arr[$row[csf("buyer_po_id")]]['style']; ?>&nbsp;</td>
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
if($action=="create_production_no_list_view_b")
{
	
	
	$data=explode('_',$data);
	//echo '<pre>';print_r($data);
	$company_id=$data[0];
	$location_id=$data[1];
	$prod_no=$data[2];
	$recipe_no=$data[3];
	$date_from=$data[4];
	$date_to=$data[5];
	$search_type=$data[6];
	$search_by=str_replace("'","",$data[7]);
	$search_str=trim(str_replace("'","",$data[8]));
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[9]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[9]";}
	
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
	
	/*$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.prefix_no_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and a.prefix_no_num = '$search_str' ";
			else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
			else if ($search_by==5) $style_cond=" and a.style_ref_no = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.prefix_no_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.prefix_no_num like '%$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str%'"; 
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str%'";   
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.prefix_no_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.prefix_no_num like '$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '$search_str%'";
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '$search_str%'";  
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.prefix_no_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.prefix_no_num like '%$search_str'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str'";
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str'";  
		}
	}*/
	
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";$within_no_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $within_no_cond="and d.job_no_prefix_num='$search_str'";
			else if($search_by==2) $within_no_cond="and d.order_no='$search_str'";
			
			if ($search_by==3) $within_no_cond=" and d.job_no_prefix_num = '$search_str' ";
			//else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
			//else if ($search_by==5) $style_cond=" and a.style_ref_no = '$search_str' ";
			
			else if ($search_by==4) $within_no_cond=" and c.buyer_po_no = '$search_str' ";
			else if ($search_by==5) $within_no_cond=" and c.buyer_style_ref = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $within_no_cond="and d.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $within_no_cond="and d.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and d.job_no_prefix_num like '%$search_str%'";  
			//else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str%'"; 
			//else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str%'";  
			
			else if ($search_by==4) $within_no_cond=" and c.buyer_po_no like '%$search_str%' ";
			else if ($search_by==5) $within_no_cond=" and c.buyer_style_ref like '%$search_str%' "; 
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $within_no_cond="and d.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $within_no_cond="and d.order_no like '$search_str%'";  
			
			if ($search_by==3) $within_no_cond=" and d.job_no_prefix_num like '$search_str%'";  
			//else if ($search_by==4) $po_cond=" and b.po_number like '$search_str%'"; 
			//else if ($search_by==5) $style_cond=" and a.style_ref_no like '$search_str%'";  
			else if ($search_by==4) $within_no_cond=" and c.buyer_po_no like '$search_str%' ";
			else if ($search_by==5) $within_no_cond=" and c.buyer_style_ref like '$search_str%' ";  
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $within_no_cond="and d.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $within_no_cond="and d.order_no like '%$search_str'";  
			
			if ($search_by==3) $within_no_cond=" and d.job_no_prefix_num like '%$search_str'";  
			//else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str'";  
			//else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str'"; 
			else if ($search_by==4) $within_no_cond=" and c.buyer_po_no like '%$search_str' ";
			else if ($search_by==5) $within_no_cond=" and c.buyer_style_ref like '%$search_str' ";  
		}
	}
	
	
	/*$po_ids=''; $buyer_po_arr=array();
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	//echo $po_ids;
	if ($po_ids!="") $po_idsCond=" and a.buyer_po_id in ($po_ids)"; else $po_idsCond="";*/
	
	/*$spo_ids='';
	if(($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2))
	{
		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.embellishment_job=b.job_no_mst $search_com_cond", "id");
	}
	if ( $spo_ids!="") $spo_idsCond=" and b.po_id in ($spo_ids)"; else $spo_idsCond="";*/
	
	if($company_id==0) { echo "Select Company first"; die; }
	
	if($location_id !="0") $location_cond= "and a.location_id = $location_id"; else $location_cond= "";
	if($prod_no!="") $system_no_cond=" and a.sys_no like '%".trim($prod_no)."%'"; else $system_no_cond="";
	if($recipe_no!="") $recipe_no_cond=" and a.sys_no like '%".trim($recipe_no)."%'"; else $recipe_no_cond="";
	
	$within_group =$data[10];
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	//echo "sdlkjklsdj";
	//if($data[4]!="" && $data[5]!="") $date_cond=" and product_date between '".$date_from."' and '".$date_to."'"; else $date_cond="";
	$order_arr=array();
	
	$order_sql = sql_select("SELECT a.embellishment_job, a.within_group, a.party_id, b.id, b.order_no, sum(c.qnty) as qty, b.buyer_po_no,b. buyer_style_ref from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and a.entry_form=311 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by a.embellishment_job, a.within_group, a.party_id, b.id, b.order_no, b.buyer_po_no,b. buyer_style_ref");
	foreach($order_sql as $row)
	{
		$order_arr[$row[csf('id')]]['po']=$row[csf('order_no')];
		$order_arr[$row[csf('id')]]['within_group']=$row[csf('within_group')];
		$order_arr[$row[csf('id')]]['party_id']=$row[csf('party_id')];
		$order_arr[$row[csf('id')]]['qty']=$row[csf('qty')];
		$order_arr[$row[csf('id')]]['buyer_po_no']=$row[csf('buyer_po_no')];
		$order_arr[$row[csf('id')]]['buyer_style_ref']=$row[csf('buyer_style_ref')];
	}
	unset($order_sql);
	
	
	$recipe_arr = return_library_array("select id, recipe_no from pro_recipe_entry_mst", 'id', 'recipe_no');
	
	
	$pdate_cond=($db_type==2 ? " TO_CHAR(production_hour,'HH24:MI') " : " TIME_FORMAT( production_hour, '%H:%i' ) ");
	
	
		if ($db_type == 0) 
		{
			$pdate_cond="TIME_FORMAT( production_hour, '%H:%i')";
		} 
		else 
		{
			$pdate_cond="TO_CHAR(production_hour,'HH24:MI')";
		}
	
	
	
	//echo $pdate_cond; die;
	
	$prodData=sql_select("select id, mst_id, color_size_id, production_date, $pdate_cond as production_hour, operator_name, shift_id from subcon_embel_production_dtls where status_active=1 and is_deleted=0");
	
	
	
	foreach($prodData as $row)
	{
		$prod_data_arr[$row[csf('mst_id')]]['production_date']=change_date_format($row[csf('production_date')]);
		$prod_data_arr[$row[csf('mst_id')]]['production_hour']=$row[csf('production_hour')];
		$prod_data_arr[$row[csf('mst_id')]]['operator_name']=$row[csf('operator_name')];
		$prod_data_arr[$row[csf('mst_id')]]['shift_id']=$row[csf('shift_id')];
	}
	
	$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	//print_r($buyer_po_arr);
	
	
	?>
	<body>
		<div align="center">
			<fieldset style="width:770px;margin-left:10px">
				<form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="770" class="rpt_table" >
						<thead>
							<th width="30">SL</th>
							<th width="120">Production No</th>
                            <th width="70">Prod. Date</th>
                            <th width="120">Job NO</th>
                            <th width="110">Order</th>
                            <th width="110">Buyer Po</th>
            				<th>Buyer Style</th>
						</thead>
					</table>
					<div style="width:770px; overflow-y:scroll; max-height:300px;">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" id="tbl_list_search" >
							<?
							
							if ($db_type == 0) 
							{
								//$pdate_cond="TIME_FORMAT( production_hour, '%H:%i')";
								$production_hour="TIME_FORMAT(b.production_hour, 'hh:mi:ss') as production_hour";
							} 
							else 
							{
								//$pdate_cond="TO_CHAR(production_hour,'HH24:MI')";
								$production_hour="to_char(b.production_hour, 'hh:mi:ss') as production_hour";
							}
												
							 $sql="SELECT  $production_hour,b.shift_id,a.id, a.prefix_no_num, a.sys_no, a.location_id, a.recipe_id, a.job_no, b.po_id as order_id, b.buyer_po_id, a.floor_id, a.machine_id, a.product_date,b.production_date from subcon_embel_production_mst a, subcon_embel_production_dtls b,subcon_ord_dtls c,subcon_ord_mst d where a.id=b.mst_id and d.id=c.mst_id and b.po_id=c.id and a.entry_form=315 and a.status_active = 1 and a.is_deleted = 0 and a.company_id='$company_id' $location_cond $system_no_cond $recipe_no_cond $spo_idsCond $po_idsCond $date_cond $year_cond $within_no_cond group by  b.production_hour,b.shift_id, a.id, a.prefix_no_num, a.sys_no, a.location_id, a.recipe_id, a.job_no, b.po_id  , b.buyer_po_id, a.floor_id, a.machine_id, a.product_date,b.production_date  order by a.id DESC";// $date_cond
							 
							//echo $sql;die;
							$sql_res=sql_select($sql);

							$i=1; 
							foreach($sql_res as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$str_data="";
								$hour_string= $row[csf('production_hour')];
								//print_r($hour_ex);
								
								
								
									if($order_arr[$row[csf('order_id')]]['within_group']==1)
									{
										$buyer_po=$buyer_po_arr[$row[csf("buyer_po_id")]]['po'];
										$buyer_style=$buyer_po_arr[$row[csf("buyer_po_id")]]['style'];
									}
									if($order_arr[$row[csf('order_id')]]['within_group']==2)
									{
										$buyer_po=$order_arr[$row[csf('order_id')]]['buyer_po_no'];
										$buyer_style=$order_arr[$row[csf('order_id')]]['buyer_style_ref'];
									}
									
								
								$str_data=$row[csf('id')].'***'.$row[csf('sys_no')].'***'.$row[csf('location_id')].'***'.$row[csf('recipe_id')].'***'.$recipe_arr[$row[csf('recipe_id')]].'***'.$row[csf('job_no')].'***'.$row[csf('order_id')].'***'.$order_arr[$row[csf('order_id')]]['po'].'***'.$order_arr[$row[csf('order_id')]]['within_group'].'***'.$order_arr[$row[csf('order_id')]]['party_id'].'***'.$order_arr[$row[csf('order_id')]]['qty'].'***'.$prod_data_arr[$row[csf('id')]]['production_date'].'***'.$prod_data_arr[$row[csf('id')]]['production_hour'].'***'.$prod_data_arr[$row[csf('id')]]['operator_name'].'***'.$row[csf('buyer_po_id')].'***'.$buyer_po_arr[$row[csf("buyer_po_id")]]['po'].'***'.$buyer_po_arr[$row[csf("buyer_po_id")]]['style'].'***'.$prod_data_arr[$row[csf('id')]]['shift_id'].'***'.$row[csf('floor_id')].'***'.$row[csf('machine_id')].'***'.$hour_string.'***'.$row[csf('shift_id')];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $str_data; ?>')"> 
									<td width="30" align="center"><?php echo $i; ?></td>	
									<td width="120" align="center"><?php echo $row[csf('prefix_no_num')]; ?></td>
                                    <td width="70"><?php echo change_date_format($row[csf('production_date')]); ?>&nbsp;</td>
                                    <td width="120"><?php echo $row[csf('job_no')]; ?></td>
                                    <td width="110" style="word-break:break-all"><?php echo $order_arr[$row[csf('order_id')]]['po']; ?></td>
                                    <td width="110" style="word-break:break-all"><?php echo $buyer_po;//$buyer_po_arr[$row[csf("buyer_po_id")]]['po']; ?>&nbsp;</td>
                                    <td style="word-break:break-all"><?php echo $buyer_style;//$buyer_po_arr[$row[csf("buyer_po_id")]]['style']; ?>&nbsp;</td>
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

if ($action=="load_php_data_to_form")
{
	//echo "SELECT a.id , a.sys_no, a.company_id,a.location_id, a.party_id,a.within_group, a.embl_job_no,b.order_id,sum (b.quantity) as qty,b.buyer_po_id from sub_material_mst a,sub_material_dtls b  where  embl_job_no='$data' and entry_form=312 group by a.id, a.sys_no, a.company_id,a.location_id, a.party_id,a.within_group, a.embl_job_no,b.order_id,b.buyer_po_id";
	$nameArray=sql_select( "SELECT a.id , a.sys_no, a.company_id,a.location_id, a.party_id,a.within_group, a.embl_job_no,b.order_id,sum (b.quantity) as qty,b.buyer_po_id from sub_material_mst a,sub_material_dtls b  where a.id=b.mst_id and  a.embl_job_no='$data' and a.entry_form=313 group by a.id, a.sys_no, a.company_id,a.location_id, a.party_id,a.within_group, a.embl_job_no,b.order_id,b.buyer_po_id " );  
	foreach ($nameArray as $row)
	{	
		//echo "document.getElementById('txt_production_id').value 		= '".$row[csf("sys_no")]."';\n";
		echo "document.getElementById('cbo_company_id').value 	    = '".$row[csf("company_id")]."';\n";
		echo "$('#cbo_company_id').attr('disabled','true')".";\n"; 
		 
		echo "document.getElementById('cbo_within_group').value		= '".$row[csf("within_group")]."';\n"; 
		echo "load_drop_down( 'requires/emb_production_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_location', 'location_td' );\n";
		
		//echo "load_drop_down( 'requires/emb_material_receive_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer', 'buyer_td' );\n"; 		
		 
		echo "document.getElementById('cbo_location').value	        = '".$row[csf("location_id")]."';\n";
		echo "load_drop_down( 'requires/emb_production_controller', document.getElementById('cbo_company_id').value+'__'+document.getElementById('cbo_location').value, 'load_drop_down_floor', 'floor_td' );\n"; 
		echo "document.getElementById('cbo_buyer_name').value		= '".$row[csf("party_id")]."';\n";
		echo "document.getElementById('txt_order_id').value		    = '".$row[csf("order_id")]."';\n"; 
		//echo " alert(33);\n"; 
		echo "document.getElementById('txt_order_qty').value		= '".$row[csf("qty")]."';\n";
		//echo "document.getElementById('txtorderNo').value		    = '".$row[csf("order_no")]."';\n"; 
		echo "document.getElementById('txtbuyerPo').value		    = '".$row[csf("buyer_po_id")]."';\n"; 
		echo "document.getElementById('txtstyleRef').value		    = '".$row[csf("")]."';\n"; 
		//echo "document.getElementById('cbo_floor_id').value	        = '".$row[csf("floor_id")]."';\n"; 
		//echo "document.getElementById('cbo_machine_id').value	    = '".$row[csf("machine_id")]."';\n"; 
		//echo "document.getElementById('txt_receive_date').value 	= '".change_date_format($row[csf("subcon_date")])."';\n";  
		//echo "document.getElementById('txt_job_no').value			= '".$row[csf("embl_job_no")]."';\n"; 
		  
	   // echo "document.getElementById('update_id').value            = '".$row[csf("id")]."';\n";
		//echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_material_receive',1);\n";
		
		//echo "$('#cbo_within_group').attr('disabled','true')".";\n"; 
		//echo "$('#cbo_party_name').attr('disabled','true')".";\n"; 
	}
	exit();
}
if($action=="load_php_dtls_form")
{
	//echo $data;
	$exdata=explode("**",$data);
	$jobno=''; $update_id=0;
	$update_id=$exdata[0];
	$jobno=$exdata[1];
	$color_arr =return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$size_arr =return_library_array( "select id,size_name from  lib_size where status_active=1 and is_deleted=0",'id','size_name');
	$buyer_po_arr=array();
	
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	// echo "<pre>";
	// print_r($buyer_po_arr);
	unset($po_sql_res);
	
	$updtls_data_arr=array(); $pre_qty_arr=array();
	
	$sql_iss="SELECT a.id, a.mst_id, a.quantity, a.uom, a.job_dtls_id, a.job_break_id, a.buyer_po_id from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.embl_job_no='$jobno' and b.trans_type=2 and b.entry_form=313 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

	 //echo $sql_iss; die;
	$sql_iss_res =sql_select($sql_iss);
	
	foreach ($sql_iss_res as $row)
	{
		if($row[csf("mst_id")]==$update_id)
		{
			$updtls_data_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['dtlsid']=$row[csf("id")];
			$updtls_data_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['qty']=$row[csf("quantity")];
		}
		else
		{
			$pre_qty_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['qty']+=$row[csf("quantity")];
		}
	}
	 //print_r($updtls_data_arr);
	// print_r($pre_qty_arr);
	unset($sql_iss_res);
	
	$rec_data_arr=array();
	$sql_rec="SELECT a.sys_no, b.quantity, b.job_dtls_id, b.job_break_id, b.buyer_po_id from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.embl_job_no='$jobno' and a.trans_type=1 and a.entry_form=312 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_rec_res =sql_select($sql_rec);
	foreach ($sql_rec_res as $row)
	{
		$rec_data_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['sys_challan']=$row[csf("sys_no")];
		$rec_data_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['qty']+=$row[csf("quantity")];
	}
	// echo "<pre>";
	// echo $rec_data_arr;
	unset($sql_rec_res);
	
	$sql_job="SELECT a.id, a.embellishment_job,a.within_group, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, b.main_process_id, b.order_uom, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty, b.buyer_po_no, b.buyer_style_ref 
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
		where a.entry_form=311 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and a.id=b.mst_id and b.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and b.id=c.mst_id and a.embellishment_job='$jobno' order by c.id ASC";
	//echo $sql_job;
	/*echo '<pre>';
	print_r($pre_qty_arr); die;*/

	$sql_res_issue = "SELECT b.buyer_po_id, b.quantity, b.job_break_id AS color_size_id FROM sub_material_mst a,sub_material_dtls b where a.entry_form=313 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
    $sql_res_issue_result = sql_select($sql_res_issue);
	foreach ($sql_res_issue_result as $row)
	{
		$issue_qnty_arr[$row[csf("buyer_po_id")]][$row[csf("color_size_id")]] += $row[csf("quantity")];	 
	}

	$sql_res_prod = "SELECT b.mst_id, b.id, b.buyer_po_id, b.qcpass_qty, b.color_size_id, b.remarks FROM subcon_embel_production_mst a, subcon_embel_production_dtls b where a.entry_form=315 and a.id=b.mst_id and a.job_no='$jobno' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
    $sql_res_prod_result = sql_select($sql_res_prod); $prod_qnty_arr=array(); $preprod_qnty_arr=array();
	foreach ($sql_res_prod_result as $row)
	{
		if($row[csf("mst_id")]==$update_id)
		{
			$prod_qnty_arr[$row[csf("color_size_id")]]['qty']+= $row[csf("qcpass_qty")];	
			$prod_qnty_arr[$row[csf("color_size_id")]]['uid']= $row[csf("id")];	
			$prod_qnty_arr[$row[csf("color_size_id")]]['remarks']= $row[csf("remarks")];	
		}
		else
		{
			$preprod_qnty_arr[$row[csf("color_size_id")]]['qty']+= $row[csf("qcpass_qty")];	
		}
	}
	// echo '<pre>';
	// print_r($prod_qnty_arr); die;
	
	$sql_result =sql_select($sql_job);
	$k=0;
	$num_rowss=count($sql_result);
	foreach ($sql_result as $row)
	{
		$k++;
		
		if($row[csf("main_process_id")]==1) $emb_type=$emblishment_print_type;
		else if($row[csf("main_process_id")]==2) $emb_type=$emblishment_embroy_type;
		else if($row[csf("main_process_id")]==3) $emb_type=$emblishment_wash_type;
		else if($row[csf("main_process_id")]==4) $emb_type=$emblishment_spwork_type;
		else if($row[csf("main_process_id")]==5) $emb_type=$emblishment_gmts_type;
		else $emb_type="";
		
		$qty=0; $dtlsup_id=""; $sys_challan=""; $balance_qty=0; $rec_qty=0; $pre_issue_qty=0;
		$rec_qty=$rec_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
		//echo "<pre>";print_r($rec_data_arr);
		$pre_issue_qty=$pre_qty_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
		$pre_prod_qty=$preprod_qnty_arr[$row[csf("breakdown_id")]]['qty'];
		$balance_qty=number_format($pre_issue_qty-$pre_prod_qty,4,'.',''); 
		$uid='';$remarks='';
		if($update_id!=0)
		{
			$qty=$prod_qnty_arr[$row[csf("breakdown_id")]]['qty'];//$updtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
			$uid=$prod_qnty_arr[$row[csf("breakdown_id")]]['uid'];//$updtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['dtlsid'];
			$remarks=$prod_qnty_arr[$row[csf("breakdown_id")]]['remarks'];
		}
		else $qty=$balance_qty;
		
		if($qty==0) $qty='';
		if($balance_qty==0) $balance_qty=0;
		
		$sys_challan=$rec_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['sys_challan'];
		
		$dtlsup_id=$updtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['dtlsid'];
		
		//$order_qnty_pcs=number_format($row[csf("qnty")]*12,4,'.','');
		
		$order_qnty_pcs=($row[csf("order_uom")]==1) ? number_format($row[csf("qnty")],4,'.','') : number_format($row[csf("qnty")]*12,4,'.','');
		
			if($row[csf('within_group')]==1){
				$buyer_po=$buyer_po_arr[$row[csf("buyer_po_id")]]['po'];
				$buyer_style=$buyer_po_arr[$row[csf("buyer_po_id")]]['style']; 
			}
			if($row[csf('within_group')]==2)
			{
				$buyer_po=$row[csf('buyer_po_no')];
				$buyer_style=$row[csf('buyer_style_ref')];
			}
		
		?>
		<tr>
			<td>
				<input type="text" name="txtSl[]" id="txtSl_<? echo $k; ?>" class="text_boxes_numeric" style="width:30px" value="<? echo $k;?>" disabled />
			</td>
			<td>
				<input name="txtorderNo_<? echo $k; ?>" id="txtorderNo_<? echo $k; ?>" type="text" class="text_boxes" style="width:100px" value="<? echo $row[csf("order_no")]; ?>" readonly />
				<!-- Order's Dtls ID become next processes PO_ID -->
				<input name="txtPoId[]" id="txtPoId_<? echo $k; ?>" type="hidden" class="text_boxes" style="width:100px" value="<? echo $row[csf("po_id")]; ?>" />
			</td>
			<td>
				<input name="txtbuyerPo_<? echo $k; ?>" id="txtbuyerPo_<? echo $k; ?>" type="text" class="text_boxes" style="width:90px" value="<? echo $buyer_po;//$buyer_po_arr[$row[csf("buyer_po_id")]]['po']; ?>" readonly />
				<input name="txtbuyerPoId[]" id="txtbuyerPoId_<? echo $k; ?>" type="hidden" class="text_boxes" style="width:70px" value="<? echo $row[csf("buyer_po_id")]; ?>" />
			</td>
			<td>
				<input name="txtstyleRef_<? echo $k; ?>" id="txtstyleRef_<? echo $k; ?>" type="text" class="text_boxes" style="width:90px" 	value="<? echo $buyer_style;//$buyer_po_arr[$row[csf("buyer_po_id")]]['style']; ?>" readonly />
			</td>
			<td>
				<input type="text" name="txtGmtsItem[]" id="txtGmtsItem_<? echo $k;?>" value="<? echo $garments_item[$row[csf('gmts_item_id')]];?>" class="text_boxes" style="width:80px" placeholder="Display"disabled /> 
				<input type="hidden" name="txtGmtsItemId[]" id="txtGmtsItemId_<? echo $k;?>" value="<? echo $row[csf('gmts_item_id')];?>" style="width:50px" />
			</td>
			<td>
				<input type="text" name="txtBodyPart_" id="txtBodyPart_<? echo $k;?>" value="<? echo $body_part[$row[csf('body_part')]];?>" class="text_boxes" style="width:80px" placeholder="Display"disabled /> 
				<input type="hidden" name="txtBodyPartId[]" id="txtBodyPartId_<? echo $k;?>" value="<? echo $row[csf('gmts_item_id')];?>" style="width:50px" />
			</td>
			<td>
				<input type="text" name="txtEmblName_" id="txtEmblName_<? echo $k;?>" value="<? echo $emblishment_name_array[$row[csf('main_process_id')]];?>" class="text_boxes" style="width:80px" placeholder="Display"disabled /> 
				<input type="hidden" name="txtEmblNameId[]" id="txtEmblNameId_<? echo $k;?>" value="<? echo $row[csf('main_process_id')];?>" style="width:50px" />
			</td>
			<td>
				<input type="text" name="txtEmblType_" id="txtEmblType_<? echo $k;?>" value="<? echo $emb_type[$row[csf('embl_type')]];?>" class="text_boxes" style="width:80px" placeholder="Display"disabled /> 
				<input type="hidden" name="txtEmblTypeId[]" id="txtEmblTypeId_<? echo $k;?>" value="<? echo $row[csf('embl_type')];?>" style="width:50px" />
			</td>
			<td>
				<input type="text" name="txtColor_" id="txtColor_<? echo $k;?>" value="<? echo $color_arr[$row[csf('color_id')]];?>" class="text_boxes" style="width:80px" placeholder="Display"disabled /> 
				<input type="hidden" name="txtColorId_[]" id="txtColorId_<? echo $k;?>" value="<? echo $row[csf('color_id')];?>" style="width:50px" />
				<input type="hidden" name="txtColorSizeId[]" id="txtColorSizeId_<? echo $k;?>" value="<? echo $row[csf('breakdown_id')];?>" style="width:50px" />
                <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $k;?>" value="<? echo $uid; ?>" style="width:50px" />
			</td>
			<td>
				<input type="text" name="txtSize_" id="txtSize_<? echo $k;?>" value="<? echo $size_arr[$row[csf('size_id')]];?>" class="text_boxes" style="width:80px" placeholder="Display"disabled /> 
				<input type="hidden" name="txtSizeId_[]" id="txtSizeId_<? echo $k;?>" value="<? echo $row[csf('size_id')];?>" style="width:50px" />
			</td>
			<td>
				<input name="txtOrderId_<? echo $k;?>" id="txtOrderId_<? echo $k;?>" class="text_boxes_numeric" type="text" value="<? echo $order_qnty_pcs;?>" style="width:50px" disabled/>
			</td>
			<td>
				<input name="txtissueqty_<? echo $k;?>" id="txtissueqty_<? echo $k;?>" class="text_boxes_numeric" value="<? echo $pre_issue_qty; ?>" type="text" onKeyUp="check_iss_qty_ability(this.value,1); fnc_total_calculate();" style="width:50px" />
			</td>
			<td>
				<input type="text" name="txtProdQty[]" id="txtProdQty_<? echo $k;?>" class="text_boxes_numeric" style="width:50px" placeholder="<? echo $balance_qty; ?>" onBlur="fnc_total_calculate();" onKeyUp="check_iss_qty_ability(this.value,<? echo $k;?>);check_cur_iss_qty_ability(this.value,<? echo $k;?>);" value="<? echo $qty; ?>" />
			</td>
			<td>
				<? echo create_drop_down( "cbouom_$k",50, $unit_of_measurement,"", 1, "-Select-",1,"", 1,"" );?>
					
			</td>
			<td>
				<input type="text" name="txtRemarks[]" id="txtRemarks_<? echo $k;?>" class="text_boxes" style="width:60px" placeholder="Write" value="<? echo $remarks;?>" />
			</td>

     </tr>
	<?	
	}
	exit();
}

if($action=="load_php_dtls_form_update")
{
	//echo $data;
	$exdata=explode("***",$data);
	$jobno=''; $update_id=0;
	$update_id=$exdata[0];
	$jobno=$exdata[1];
	$color_arr =return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$size_arr =return_library_array( "select id,size_name from  lib_size where status_active=1 and is_deleted=0",'id','size_name');
	$buyer_po_arr=array();
	
	$po_sql ="SELECT a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	
	$updtls_data_arr=array(); $pre_qty_arr=array();
	
	 $sql_iss="SELECT a.id, a.mst_id, a.quantity, a.uom, a.job_dtls_id, a.job_break_id, a.buyer_po_id from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.embl_job_no='$jobno' and b.trans_type=2 and b.entry_form=313 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

	 //echo $sql_iss; die;
	$sql_iss_res =sql_select($sql_iss);
	
	foreach ($sql_iss_res as $row)
	{
		if($row[csf("mst_id")]==$update_id)
		{
			$updtls_data_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['dtlsid']=$row[csf("id")];
			$updtls_data_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['qty']=$row[csf("quantity")];
		}
		else
		{
			$pre_qty_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['qty']+=$row[csf("quantity")];
		}
	}
	 //print_r($updtls_data_arr);
	// print_r($pre_qty_arr);
	unset($sql_iss_res);
	
	$rec_data_arr=array();
	$sql_rec="SELECT a.sys_no, b.quantity, b.job_dtls_id, b.job_break_id, b.buyer_po_id from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.embl_job_no='$jobno' and a.trans_type=1 and a.entry_form=312 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	//echo $sql_rec; 
	$sql_rec_res =sql_select($sql_rec);
	foreach ($sql_rec_res as $row)
	{
		$rec_data_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['sys_challan']=$row[csf("sys_no")];
		$rec_data_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['qty']+=$row[csf("quantity")];
	}
	unset($sql_rec_res);

	
	/*$sql_job="SELECT a.id, a.embellishment_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, b.main_process_id, b.order_uom, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
		where a.entry_form=311 and a.embellishment_job=b.job_no_mst and a.id=b.mst_id and b.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id=c.mst_id and a.embellishment_job='$jobno' order by c.id ASC";*/

		$sql_job="SELECT b.color_size_id, b.buyer_po_id, b.gmts_item_id,b.embl_type_id as embl_type,b.remarks,b.qcpass_qty, b.color_id, b.embl_name_id, b.production_hour 
		from subcon_embel_production_mst a, subcon_embel_production_dtls b 
		where a.entry_form=315 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='$exdata[2]' ";

       $sql_res="SELECT id, size_id, qnty FROM subcon_ord_breakdown ";
        $sql_res =sql_select($sql_res);
		foreach ($sql_res as $row)
		{
			$color_size_data_arr[$row[csf("id")]]['size_id']=$row[csf("size_id")];
			$color_size_data_arr[$row[csf("id")]]['qty']+=$row[csf("qnty")];
		}

       
       $sql_res_dtls="SELECT b.buyer_po_id, b.quantity, b.job_break_id AS color_size_id FROM sub_material_mst a,sub_material_dtls b where a.entry_form=313 and a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  ";
        $sql_res_dtls =sql_select($sql_res_dtls);
		foreach ($sql_res_dtls as $row)
		{
			$issue_qnty_arr[$row[csf("buyer_po_id")]][$row[csf("color_size_id")]]=$row[csf("quantity")];
		}

	    //echo $sql_res_dtls;

	    $sql_res_ord_dtls="SELECT buyer_po_id, order_uom FROM subcon_ord_dtls where status_active=1 and is_deleted=0  ";
        $sql_res_ord_dtls =sql_select($sql_res_ord_dtls);
		foreach ($sql_res_ord_dtls as $row)
		{
			$ord_wise_uom_arr[$row[csf("buyer_po_id")]]=$row[csf("order_uom")];
		}
 
		 
	
	$sql_result =sql_select($sql_job);
	$k=0;
	$num_rowss=count($sql_result);
	foreach ($sql_result as $row)
	{
		$k++;
		
		if($row[csf("embl_name_id")]==1) $emb_type=$emblishment_print_type;
		else if($row[csf("embl_name_id")]==2) $emb_type=$emblishment_embroy_type;
		else if($row[csf("embl_name_id")]==3) $emb_type=$emblishment_wash_type;
		else if($row[csf("embl_name_id")]==4) $emb_type=$emblishment_spwork_type;
		else if($row[csf("embl_name_id")]==5) $emb_type=$emblishment_gmts_type;
		else $emb_type="";
		
		$qty=0; $dtlsup_id=""; $sys_challan=""; $balance_qty=0; $rec_qty=0; $pre_issue_qty=0;
		$rec_qty=$rec_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
		//echo "<pre>";print_r($rec_data_arr);
		$pre_issue_qty=$pre_qty_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
		$balance_qty=number_format($rec_qty-$pre_issue_qty,4,'.','');
		if($update_id!=0)
		{
			$qty=$updtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
		}
		else $qty=$balance_qty;
		
		if($qty==0) $qty='';
		if($balance_qty==0) $balance_qty=0;
		
		$sys_challan=$rec_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['sys_challan'];
		
		$dtlsup_id=$updtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['dtlsid'];
		$order_qnty_pcs=number_format($row[csf("qnty")]*12,4,'.','');
		$uom=$ord_wise_uom_arr[$row[csf("buyer_po_id")]];
		$order_qnty=$color_size_data_arr[$row[csf('color_size_id')]]['qty'];
		if($uom==2)$order_qnty=$order_qnty*12;
		?>
		<tr>
			<td><input type="text" name="txtSl[]" id="txtSl_<? echo $k; ?>" class="text_boxes_numeric" style="width:30px" value="<? echo $k;?>" disabled /></td>
			<td>
				<input name="txtorderNo_<? echo $k; ?>" id="txtorderNo_<? echo $k; ?>" type="text" class="text_boxes" style="width:100px" value="<? echo $row[csf("order_no")]; ?>" readonly />
				<input name="txtPoId_<? echo $k; ?>" id="txtPoId_<? echo $k; ?>" type="hidden" class="text_boxes" style="width:100px" value="<? echo $row[csf("po_id")]; ?>" />
			</td>
			<td>
				<input name="txtbuyerPo_<? echo $k; ?>" id="txtbuyerPo_<? echo $k; ?>" type="text" class="text_boxes" style="width:90px" value="<? echo $buyer_po_arr[$row[csf("buyer_po_id")]]['po']; ?>" readonly />
				<input name="txtbuyerPoId_<? echo $k; ?>" id="txtbuyerPoId_<? echo $k; ?>" type="hidden" class="text_boxes" style="width:70px" value="<? echo $row[csf("buyer_po_id")]; ?>" />
			</td>
			<td>
				<input name="txtstyleRef_<? echo $k; ?>" id="txtstyleRef_<? echo $k; ?>" type="text" class="text_boxes" style="width:90px" value="<? echo $buyer_po_arr[$row[csf("buyer_po_id")]]['style']; ?>" readonly /></td>
			<td>
				<input type="text" name="txtGmtsItem[]" id="txtGmtsItem_<? echo $k;?>" value="<? echo $garments_item[$row[csf('gmts_item_id')]];?>" class="text_boxes" style="width:80px" placeholder="Display"disabled /> 
				<input type="hidden" name="txtGmtsItemId[]" id="txtGmtsItemId_<? echo $k;?>" value="<? echo $row[csf('gmts_item_id')];?>" style="width:50px" />
			</td>
			<td>
				<input type="text" name="txtBodyPart_" id="txtBodyPart_<? echo $k;?>" value="<? echo $body_part[$row[csf('body_part')]];?>" class="text_boxes" style="width:80px" placeholder="Display"disabled /> 
				<input type="hidden" name="txtBodyPartId[]" id="txtBodyPartId_<? echo $k;?>" value="<? echo $row[csf('gmts_item_id')];?>" style="width:50px" />
			</td>
			<td>
				<input type="text" title="<? echo $row[csf('embl_name_id')]; ?>" name="txtEmblName_" id="txtEmblName_<? echo $k;?>" value="<? echo $emblishment_name_array[$row[csf('embl_name_id')]];?>" class="text_boxes" style="width:80px" placeholder="Display"disabled /> 
				<input type="hidden" name="txtEmblNameId[]" id="txtEmblNameId_<? echo $k;?>" value="<? echo $row[csf('embl_name_id')];?>" style="width:50px" />
			</td>
			<td>
				<input type="text" name="txtEmblType_" id="txtEmblType_<? echo $k;?>" value="<? echo $emb_type[$row[csf('embl_type')]];?>" class="text_boxes" style="width:80px" placeholder="Display"disabled /> 
				<input type="hidden" name="txtEmblTypeId[]" id="txtEmblTypeId_<? echo $k;?>" value="<? echo $row[csf('embl_type')];?>" style="width:50px" />
			</td>

			<!-- <td><? //echo create_drop_down( "cboGmtsItem_$k", 90, $garments_item,"", 1, "-- Select --",$break_item[$k], "","","" ); ?></td> -->

			<td>
				<input type="text" name="txtColor_" id="txtColor_<? echo $k;?>" value="<? echo $color_arr[$row[csf('color_id')]];?>" class="text_boxes" style="width:80px" placeholder="Display"disabled /> 
				<input type="hidden" name="txtColorId_[]" id="txtColorId_<? echo $k;?>" value="<? echo $row[csf('color_id')];?>" style="width:50px" />
				<input type="hidden" name="txtColorSizeId_[]" id="txtColorSizeId_<? echo $k;?>" value="<? echo $row[csf('color_size_id')];?>" style="width:50px" />
			</td>
			<td>
				<input type="text" name="txtSize_" id="txtSize_<? echo $k;?>" value="<? echo $size_arr[$color_size_data_arr[$row[csf('color_size_id')]]['size_id']];?>" class="text_boxes" style="width:80px" placeholder="Display"disabled /> 
				<input type="hidden" name="txtSizeId_[]" id="txtSizeId_<? echo $k;?>" value="<? echo $color_size_data_arr[$row[csf('color_size_id')]]['size_id'];?>" style="width:50px" />
			</td>
			<td>
				<input name="txtOrderId_<? echo $k;?>" id="txtOrderId_<? echo $k;?>" class="text_boxes_numeric" type="text" value="<? echo  $order_qnty;?>" style="width:50px" disabled/></td>
			<td>
				<input name="txtissueqty_<? echo $k;?>" id="txtissueqty_<? echo $k;?>" class="text_boxes_numeric" value="<? echo $issue_qnty_arr[$row[csf("buyer_po_id")]][$row[csf('color_size_id')]]; ?>" type="text" onKeyUp="check_iss_qty_ability(this.value,1); fnc_total_calculate();" style="width:50px" />
			</td>
			<td>
				<input type="text" name="txtProdQty[]" id="txtProdQty_<? echo $k;?>" class="text_boxes_numeric" style="width:50px" value="<? echo $row[csf("qcpass_qty")];?>"  placeholder="Write" onBlur="fnc_total_calculate();" onKeyUp="check_iss_qty_ability(this.value,<? echo $k;?>);" />
			</td>
			<td>
				<? echo create_drop_down( "cbouom_$k",50, $unit_of_measurement,"", 1, "-Select-",1,"", 1,"" );?>
					
			</td>
			<td>
				<input type="text" name="txtRemarks[]" id="txtRemarks_<? echo $k;?>" class="text_boxes" style="width:60px" placeholder="Write" value="<? echo $row[csf("remarks")];?>" />
			</td>

     </tr>
	<?	
	}
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
		
		$new_return_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'EMBP', date("Y",time()), 5, "select id,prefix_no,prefix_no_num from  subcon_embel_production_mst where company_id=$cbo_company_id and entry_form=315 $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
		
		$id=return_next_id( "id", "subcon_embel_production_mst", 1 ) ; 
		$field_array="id, prefix_no, prefix_no_num, sys_no,company_id,location_id,product_date,job_no,order_id,buyer_po_id,floor_id,machine_id, inserted_by, insert_date, status_active, is_deleted, entry_form";
		$data_array="(".$id.",'".$new_return_no[1]."','".$new_return_no[2]."','".$new_return_no[0]."',".$cbo_company_id.",".$cbo_location.",".$txt_prod_date.",".$txt_job_no.",".$txt_order_id.",".$txtbuyerPoId.",".$cbo_floor_id.",".$cbo_machine_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,315)";

		//echo "10**INSERT INTO subcon_embel_production_mst (".$field_array.") VALUES ".$data_array; die; 
         
		$production_no=$new_return_no[0];
		
		$data_array_dtls=""; $issave=1;
		$id_dtls=return_next_id( "id", "subcon_embel_production_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, qcpass_qty, color_size_id, production_date, production_hour, operator_name, shift_id, po_id, buyer_po_id, remarks, inserted_by, insert_date, status_active, is_deleted";
		//$txt_hour="";
		$txt_hour=str_replace("'","",$txt_prod_date)." ".str_replace("'","",$txt_reporting_hour);
		$txt_reporting="to_date('".$txt_hour."','DD MONTH YYYY HH24:MI:SS')";


		//echo "10**".$txt_hour."**"; die;
		//echo "10**INSERT INTO subcon_embel_production_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls;die;
		//echo "10**".$total_row; die; 
		 
		for($i=1;$i<=$total_row;$i++)
		{
			//echo "10**".$i; //die;
            /*$txtPoId="txtPoId_".$i;
			$txtbuyerPo="txtbuyerPoId_".$i; 
			$txtstyleRef="txtstyleRef_".$i; 
			$gmts_item_id="txtGmtsItemId_".$i;  
			$txtBodyPart="txtBodyPartId_".$i; 
			$txtEmblName="txtEmblNameId_".$i; 
			$txtEmblType="txtEmblTypeId_".$i; 
			$cboGmtsItem="cboGmtsItem_".$i; 
			$color_id="txtColorId_".$i; 
			$color_size_id="txtColorSizeId_".$i;
			$size_id="txtSizeId_".$i; 
			$txtordqty="txtOrderId_".$i; 
			$txtProdQty="txtProdQty_".$i; 
			$txtissueqty="txtissueqty_".$i; 
			$cbouom="cbouom_".$i; 
			$txtRemarks="txtRemarks_".$i;*/
			$txtPoId="txtPoId_".$i;
			$txtbuyerPo="txtbuyerPoId_".$i; 
			$color_size_id="colorSizeId_".$i;
			$txtProdQty="txtProdQty_".$i; 
			$cbouom="cbouom_".$i; 
			$txtRemarks="txtRemarks_".$i;
			

			if($db_type==0)
			{
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls .="(".$id_dtls.",".$id.",'".$$txtProdQty."', ".$$color_size_id.",".$txt_prod_date.",".$txt_reporting_hour.",".$txt_super_visor.",".$cboShift.",".$$txtPoId.",".$$txtbuyerPo.",'".$$txtRemarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
					
				$id_dtls=$id_dtls+1;
				//$issave=0;
			}			
			else
			{
				//if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.=" INTO subcon_embel_production_dtls (".$field_array_dtls.") VALUES(".$id_dtls.",".$id.",'".$$txtProdQty."',".$$color_size_id.",".$txt_prod_date.",".$txt_reporting.",".$txt_super_visor.",".$cboShift.",".$$txtPoId.",".$$txtbuyerPo.",'".$$txtRemarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
				
				
				//$data_array_dtls.="(".$id_dtls.",".$id.",".$$txtProdQty.",".$$color_size_id.",".$txt_prod_date.",".$$txtPoId.",".$$txtbuyerPo.",".$$txtRemarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
				$id_dtls=$id_dtls+1;
			}
		}
		//echo "10**".$issave."**"; die;
		$flag=1;
		$rID=sql_insert("subcon_embel_production_mst",$field_array,$data_array,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		//echo "10**";
		if($db_type==0)
		{
			$rID1=sql_insert("subcon_embel_production_dtls",$field_array_dtls,$data_array_dtls,0);
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		else if($db_type==2)
		{
			$query="INSERT ALL".$data_array_dtls." SELECT * FROM dual";
			$rID1=execute_query($query);
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		//echo "10**".$rID.'-'.$rID1.'-'.$flag; die;
		
		//echo '5**'.$rID.'**'.$rID1.'**'.$flag.'**'.$issave; die;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$id)."**".$production_no;
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
				echo "0**".str_replace("'",'',$id)."**".$production_no;
				//$update_hour=execute_query("UPDATE subcon_embel_production_dtls set production_hour=$txt_reporting where mst_id=$id  ");
				oci_commit($con);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".$production_no;
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
		$field_array="location_id*product_date*floor_id*machine_id*updated_by*update_date";
		$data_array="".$cbo_location."*".$txt_prod_date."*".$cbo_floor_id."*".$cbo_machine_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_update("subcon_embel_production_mst",$field_array,$data_array,"id","".$update_id."",0);
        
        
		$data_array_dtls=""; 
		$issave=1;
		$id_dtls=return_next_id( "id", "subcon_embel_production_dtls", 1 ) ;
		 
		$field_array_dtls="id, mst_id, qcpass_qty, color_size_id, production_date, production_hour, operator_name, shift_id, po_id, buyer_po_id, remarks, inserted_by, insert_date, status_active, is_deleted";

		//echo "10**".$field_array_dtls; die;
		//echo $update_id;
		$txt_hour=str_replace("'","",$txt_prod_date)." ".str_replace("'","",$txt_reporting_hour);
		$txt_reporting="to_date('".$txt_hour."','DD MONTH YYYY HH24:MI:SS')";

		$dtls_del= execute_query("DELETE from subcon_embel_production_dtls where mst_id=$update_id ",0);
		// for($i=1;$i<=$total_row;$i++)
		// {

		// 	$txtPoId="txtPoId_".$i;
		// 	$txtbuyerPo="txtbuyerPoId_".$i; 
		// 	$txtstyleRef="txtstyleRef_".$i; 
		// 	$gmts_item_id="txtGmtsItemId_".$i;  
		// 	$txtBodyPart="txtBodyPartId_".$i; 
		// 	$txtEmblName="txtEmblNameId_".$i; 
		// 	$txtEmblType="txtEmblTypeId_".$i; 
		// 	$cboGmtsItem="cboGmtsItem_".$i; 
		// 	$color_id="txtColorId_".$i; 
		// 	$color_size_id="txtColorSizeId_".$i; 
		// 	/*$product_date="txt_prod_date"; 
		// 	$reporting_hour="txt_reporting_hour"; */
		// 	$size_id="txtSizeId_".$i; 
		// 	$txtordqty="txtOrderId_".$i; 
		// 	$txtProdQty="txtProdQty_".$i; 
		// 	$txtissueqty="txtissueqty_".$i; 
		// 	$cbouom="cbouom_".$i; 
		// 	$txtRemarks="txtRemarks_".$i; 
			 
		// 	$txt_hour="";
		// 	$txt_hour=str_replace("'","",$txt_prod_date)." ".str_replace("'","",$txt_reporting_hour);
		// 	//$txtreporting_hour="to_date('".$txt_hour."','DD MONTH YYYY HH24:MI:SS')";
		// 	$txt_reporting_hour="";

		// 	//if($data_array_dtls!="") $data_array_dtls.=",";
		// 	//$data_array_dtls.="INSERT INTO subcon_embel_production_dtls (".$field_array_dtls.") VALUES (".$id_dtls.",".$id.",".$$txtProdQty.", ".$$gmts_item_id.",".$$color_id.",".$$color_size_id.",".$txt_prod_date.",".$txtreporting_hour.",".$$txtEmblName.",".$$txtEmblType.",".$$txtbuyerPo.",".$$txtRemarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)"; 
		// 	if ($data_array_dtls != "") $data_array_dtls .= ",";
		// 	$data_array_dtls .= "(" . $id_dtls . "," . $update_id . ",".$$txtProdQty.",'".$$gmts_item_id."','".$$color_id."','".$$color_size_id."',".$txt_prod_date.",'".$txt_reporting_hour."',".$txt_super_visor.",".$cboShift.",'".$$txtEmblName."','".$$txtEmblType."',".$$txtPoId.",".$$txtbuyerPo.",'".$$txtRemarks."'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";

		// 	$id_dtls=$id_dtls+1;
			
		// 		//echo $issave;
		// }
		for($i=1;$i<=$total_row;$i++)
		{
			
			$txtPoId="txtPoId_".$i;
			$txtbuyerPo="txtbuyerPoId_".$i; 
			$color_size_id="colorSizeId_".$i;
			$txtProdQty="txtProdQty_".$i; 
			$cbouom="cbouom_".$i; 
			$txtRemarks="txtRemarks_".$i;
			

			if($db_type==0)
			{
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls .="(".$id_dtls.",".$update_id.",'".$$txtProdQty."', ".$$color_size_id.",".$txt_prod_date.",".$txt_reporting_hour.",".$txt_super_visor.",".$cboShift.",".$$txtPoId.",".$$txtbuyerPo.",'".$$txtRemarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
					
				$id_dtls=$id_dtls+1;
				//$issave=0;
			}			
			else
			{
				//if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.=" INTO subcon_embel_production_dtls (".$field_array_dtls.") VALUES(".$id_dtls.",".$update_id.",'".$$txtProdQty."',".$$color_size_id.",".$txt_prod_date.",".$txt_reporting.",".$txt_super_visor.",".$cboShift.",".$$txtPoId.",".$$txtbuyerPo.",'".$$txtRemarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";	
				
				
				$id_dtls=$id_dtls+1;
			}
		}
	
		//echo "10**INSERT INTO subcon_embel_production_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls;die; 
		//$rID_dtls=sql_insert("subcon_embel_production_dtls",$field_array_dtls,$data_array_dtls,0);

		//echo "10**$rID_dtls &&  $rID && $dtls_del";die;
		if($db_type==0)
		{
			$rID_dtls=sql_insert("subcon_embel_production_dtls",$field_array_dtls,$data_array_dtls,0);
			 
		}
		else if($db_type==2)
		{
			$query="INSERT ALL".$data_array_dtls." SELECT * FROM dual";
			$rID_dtls=execute_query($query);
			 
		}
 
		if($db_type==0)
		{
			if($rID_dtls &&  $rID && $dtls_del)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_production_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_production_id);
			}
		}
		else if($db_type==2)
		{
			if($rID_dtls &&  $rID && $dtls_del)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_production_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_production_id);
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

if ($action=="job_popup")
{
	echo load_html_head_contents("Job Popup Info","../../../", 1, 1, $unicode,'','');
	$data=explode("_",$data);
	//print_r($data);
	?>
	<script>
		function js_set_value(id)
		{ 
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_order_popup(company,within_group,party)
		{
			var company = $('#cbo_company_id').val();
			var party_name = $('#cbo_buyer_name').val();
			var location_name = $('#cbo_location').val();
			load_drop_down( 'emb_production_controller', company+'_'+within_group+'_'+party, 'load_drop_down_buyer', 'party_td' );
			$('#cbo_buyer_name').attr('disabled','disabled');
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
<body onLoad="fnc_load_party_order_popup(<? echo $data[0];?>,<? echo $data[3];?>,<? echo $data[2];?>)">
<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="940" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead> 
                <tr>
                    <th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>               	 
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="100">Within Group</th>                           
                    <th width="140">Party Name</th>
                    <th width="100">Search By</th>
                    <th width="100" id="search_by_td">Embl. Job No</th>
                    <th width="100">Year</th>
                    <th width="170">Date Range</th>                            
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                </tr>           
            </thead>
            <tbody>
                <tr class="general">
                    <td><input type="hidden" id="selected_job">  <!--  echo $data;-->
                        <? 
                        echo create_drop_down( "cbo_company_id", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "",1); ?>
                    </td>
                    <td>
                        <? echo create_drop_down( "cbo_within_group", 100, $yes_no,"", 0, "--  --", $data[3], "fnc_load_party_popup(1,this.value);" ); ?>  
                    </td>
                    <td id="party_td">
                        <? echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- Select Party --", $selected, "" ); ?>   
                    </td>
                    <td>
						<?
                            $search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style", 6=>"IR/IB");
                            echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                        ?>
                    </td>
                    <td align="center">
                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                    </td>
                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 100, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value, 'create_job_search_list_view', 'search_div', 'emb_production_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>       
                    </tr>
                    <tr>
                        <td colspan="8" align="center" valign="middle">
                            <? echo load_month_buttons();  ?>
                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" valign="top" id=""><div id="search_div"></div></td>
                    </tr>
                </tbody>
            </table>    
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_job_search_list_view")
{	
	$data=explode('_',$data);
	//echo "<pre>";print_r($data);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}
	
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { $company=""; echo "PLease Select Company name"; die;}
	if ($data[1]!=0) $buyer=" and a.party_id='$data[1]'"; else { $buyer=""; echo "PLease Select Party name"; die;}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	
	if($search_type==1)
	{
		if($search_str!="")  //b.buyer_po_no,b. buyer_style_ref
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and b.order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			//else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
			//else if ($search_by==5) $style_cond=" and a.style_ref_no = '$search_str' ";
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no = '$search_str' ";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref = '$search_str' ";
			else if ($search_by==6) $grouping=" and d.grouping = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and b.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";   
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str%'";   
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str%'"; 
			else if ($search_by==6) $grouping=" and d.grouping like '%$search_str%' ";
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and b.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '$search_str%'"; 
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '$search_str%'";  
			else if ($search_by==6) $grouping=" and d.grouping like '$search_str%' "; 
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and b.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str'";  
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str'";  
			else if ($search_by==6) $grouping=" and d.grouping like '%$search_str' "; 
		}
	}
	
	
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $order_rcv_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $order_rcv_date ="";
	}
	
	
	$po_ids='';
 	if($db_type==0) $id_cond="group_concat(b.id) as id";
	//else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	else if($db_type==2) $id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as id";
	if(($job_cond!="" && $search_by==3) || ($style_cond!="" && $search_by==5)|| ($po_cond!="" && $search_by==4))
	{
		// echo "select $id_cond from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond"; die;
		$po_ids = return_field_value("$id_cond", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst 
			$job_cond", "id");
	}
	 //echo $po_ids; die;
	 
	 
	 
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
					$po_idsCond.=" and ( b.buyer_po_id in ( $ids) ";
					$poIdsCond.=" and ( b.id in ( $ids) ";
				}
				else
				{
					$po_idsCond.=" or  b.buyer_po_id in ( $ids) ";
					$poIdsCond.=" or  b.id in ( $ids) ";
				}
			}
			$po_idsCond.=")";
			$poIdsCond.=")";
		}
		else
		{
			$ids=implode(",",$po_ids);
			$po_idsCond.=" and b.buyer_po_id in ($ids) ";
			$poIdsCond.=" and b.id in ($ids) ";
		}
	}
	else if($po_ids=="" && ($job_cond!="" && $search_by==2) || ($po_cond!="" && $search_by==3) || ($style_cond!="" && $search_by==4))
	{
		die;
		//$po_idsCond.=" and b.buyer_po_id in ($ids) ";
	}
	
	
	
	$buyer_po_arr=array();
	
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $poIdsCond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	
	$comp = return_library_array( "select id, company_name from lib_company",'id','company_name');
	$color_arr = return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	//$arr=array (3=>$comp,6=>$yes_no,7=>$color_lib_arr);
	
	if($db_type==0)
	{
		$ins_year_cond="year(a.insert_date)";
		$color_id_str="group_concat(c.color_id)";
		$buyer_po_id_cond="year(b.buyer_po_id)";
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str="listagg(c.color_id,',') within group (order by c.color_id)";
		$buyer_po_id_cond="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
	}

	if($within_group == 1){
		$sql= "SELECT a.id, a.embellishment_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, d.grouping, a.delivery_date, $color_id_str as color_id, $buyer_po_id_cond as buyer_po_id,b.buyer_po_no,b.buyer_style_ref 
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, wo_po_break_down d
		where a.entry_form=311 and d.id=b.buyer_po_id and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_deleted=0  $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond $style_cond $po_cond and b.id=c.mst_id   $year_cond $grouping
		group by a.id, d.grouping, a.embellishment_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date,b.buyer_po_no,b.buyer_style_ref 
		order by a.id DESC";
	}else{
		$sql= "SELECT a.id, a.embellishment_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, $color_id_str as color_id, $buyer_po_id_cond as buyer_po_id,b.buyer_po_no,b.buyer_style_ref 
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
		where a.entry_form=311 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_deleted=0  $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond $style_cond $po_cond and b.id=c.mst_id   $year_cond
		group by a.id, a.embellishment_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date,b.buyer_po_no,b.buyer_style_ref 
		order by a.id DESC";
	}
	  

	//echo $sql;die;

	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="985" >
        <thead>
            <th width="30">SL</th>
            <th width="60">Job No</th>
            <th width="60">Year</th>
            <th width="120">W/O No</th>
            <th width="100">IR/IB</th>
            <th width="100">Buyer Po</th>
            <th width="100">Buyer Style</th>
            <th width="80">Ord Receive Date</th>
            <th width="80">Delivery Date</th>
            <th>Color</th>
        </thead>
        </table>
        <div style="width:985px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="965" class="rpt_table" id="tbl_po_list">
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$excolor_id=array_unique(explode(",",$row[csf('color_id')]));
				$color_name="";	
				foreach ($excolor_id as $color_id)
				{
					if($color_name=="") $color_name = $color_arr[$color_id]; else $color_name.=','.$color_arr[$color_id];
				}
				$buyer_po=""; $buyer_style="";
				$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
				foreach($buyer_po_id as $po_id)
				{
					if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
				}
				$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				$job_val= $row[csf('embellishment_job')] ;				
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $job_val; ?>')" style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    <td width="120" style="word-break:break-all"><? echo $row[csf('order_no')]; ?></td>
                    <td width="100" style="word-break:break-all"><?  if ($within_group==1)echo $buyer_po; else echo $row[csf('buyer_po_no')];//$buyer_po; ?></td>
					<td width="100" style="word-break:break-all"><?  echo $row['grouping'] ?></td>
                    <td width="100" style="word-break:break-all"><? if ($within_group==1)echo $buyer_style; else echo $row[csf('buyer_style_ref')];//echo $buyer_style; ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?></td>	
                    <td style="word-break:break-all"><? echo $color_name; ?></td>
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

if($action=="embl_production_entry_print")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$size_arr=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$recipe_arr = return_library_array("select id, recipe_no from pro_recipe_entry_mst", 'id', 'recipe_no');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	$order_arr=array();
	$order_sql = sql_select("select a.embellishment_job, a.within_group, a.party_id, b.id, b.order_no, b.main_process_id, sum(c.qnty) as qty from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and a.entry_form=204 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by a.embellishment_job, a.within_group, a.party_id, b.main_process_id, b.id, b.order_no");
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
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
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
	$sql="select id, prefix_no_num, sys_no, location_id, recipe_id, job_no, order_id from subcon_embel_production_mst where entry_form=222 and status_active = 1 and is_deleted = 0 and company_id='$company_id' $location_cond $system_no_cond $recipe_no_cond order by id DESC";

	$recipe_arr = return_library_array("select id, recipe_no from pro_recipe_entry_mst", 'id', 'recipe_no');
	$pdate_cond=($db_type==2)? " TO_CHAR(production_hour,'HH24:MI') " : " TIME_FORMAT( production_hour, '%H:%i' ) ";
	$prod_data_arr=array(); $qty_data_arr=array();
	$prodData=sql_select("select id, mst_id, color_size_id, production_date, $pdate_cond as production_hour, qcpass_qty, operator_name, shift_id, remarks from subcon_embel_production_dtls where status_active=1 and is_deleted=0 and mst_id='$data[1]'");
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
	
	$sql_mst = "select id, prefix_no_num, sys_no, location_id, recipe_id, job_no, order_id, buyer_po_id from subcon_embel_production_mst where entry_form=222 and id='$data[1]'";
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
				

				$sql= "select  a.id, a.embellishment_job, b.id as order_id, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.id as color_size_id, c.color_id, c.size_id, sum(c.qnty) as qty from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, pro_recipe_entry_mst d where a.embellishment_job=b.job_no_mst and b.id=c.mst_id and a.embellishment_job=d.job_no and b.id=d.po_id  and b.job_no_mst=c.job_no_mst
							
				and b.main_process_id=d.embl_name and b.gmts_item_id=d.gmts_item and b.embl_type=d.embl_type and b.body_part=d.body_part and c.color_id=d.color_id
				and a.entry_form=204 and d.entry_form=220 and a.status_active=1 and c.status_active=1 and c.is_deleted=0  and a.company_id='$com_id' and d.id='$recipe_id' group by a.id, a.embellishment_job, b.id,b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.id, c.color_id, c.size_id, d.recipe_no_prefix_num order by c.id ASC";
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