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
	echo create_drop_down( "cbo_location", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-Select Location-", $selected, "load_drop_down('requires/dry_production_controller', document.getElementById('cbo_company_id').value+'__'+this.value, 'load_drop_down_floor', 'floor_td');","","","","","",3 );
	exit();	 
}

if ($action == "company_wise_report_button_setting")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."'  and module_id=20 and report_id=104 and is_deleted=0 and status_active=1");

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

if ($action=="load_drop_down_type")
{
	$data=explode("*",$data);
	$process_id=$data[0];
	$order_id=$data[1];
	
    $process_sql ="select  id,mst_id,order_id,embellishment_type,description,process from subcon_ord_breakdown where mst_id=$order_id and process in (2,3) and status_active=1 and is_deleted=0"; 
	$process_sql_res=sql_select($process_sql);
	foreach ($process_sql_res as $row)
	{
		
		 
		  $embellishment_type_id[$row[csf('process')]]['embellishment_type'].=$row[csf("embellishment_type")].",";
		  
		 // $prod_data_arr[$row[csf('mst_id')]]['production_date']=change_date_format($row[csf('production_date')]);
		 
	}
	$embellishment_type=chop($embellishment_type_id[$process_id]['embellishment_type'],",");
	//function create_drop_down($field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name, $additionalClass, $additionalAttributes)
	
	
	/*if($data[0]==2) 
	{
		$typeArray=$wash_dry_process;
	}
	else if ($data[0]==3) 
	{
		$typeArray=$wash_laser_desing;
	} 
	else
	{ $typeArray=$blank_array;
	}*/
	
	if($embellishment_type!="")
	{
		//$embellishment_type=$embellishment_type;
		
		if($data[0]==2) 
		{
			$typeArray=$wash_dry_process;
		}
		else if ($data[0]==3) 
		{
			$typeArray=$wash_laser_desing;
		} 
		else
		{
			 $typeArray=$blank_array;
		}
	}
	else
	{
		$typeArray=$blank_array;
	}
	// echo $embellishment_type; die;
	
	echo create_drop_down( "txtWashType_1", 92, $typeArray,"", 1, "-Select Type-", $selected ,"loadStock(this.value);",0,$embellishment_type,'','','','','',"txtWashType[]"); 
	
	
	//echo "$('#txtWashType_1').attr('disabled',false)";
	//echo "$('#txtWashType_1').attr('disabled','false')".";\n";
	exit();
}

if ($action=="load_drop_down_floor")
{
	$data=explode("__",$data);
	$company_id=$data[0];
	$location_id=$data[1];
	//if($location_id==0 || $location_id=="") $location_cond=""; else $location_cond=" and b.location_id=$location_id";
	if($location_id==0 || $location_id=="") $location_cond=""; else $location_cond=" and a.location_id=$location_id";

	if($company_id==0 && $location_id==0)
	{
		echo create_drop_down( "cbo_floor_id", 150, $blank_array,"", 1, "--Select Floor--", 0, "",0 );
	}
	else
	{
		//echo create_drop_down( "cbo_floor_id", 150, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=3 and b.company_id=$company_id and b.status_active=1 and b.is_deleted=0 and a.production_process=8 $location_cond group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "--Select Floor--", 0, "load_machine();","" );

		//echo "select a.id,a.floor_name from lib_prod_floor a, lib_location b where a.location_id=b.id and a.company_id='$company_id' and a.location_id='$location_id' and a.production_process in(7,21) and a.is_deleted=0  and a.status_active=1 $location_cond order by a.floor_name";die;

		echo create_drop_down( "cbo_floor_id", 150, "select a.id,a.floor_name from lib_prod_floor a, lib_location b where a.location_id=b.id and a.company_id='$company_id' and a.location_id='$location_id' and a.production_process in(7,21) and a.is_deleted=0  and a.status_active=1 $location_cond  order by a.floor_name",'id,floor_name', 1, '--- Select Floor ---', 0, "load_machine();"  );
		//load_drop_down( 'requires/dry_production_controller',$company_id+'_'+this.value, 'load_drop_down_machine', 'machine_td' );
	}
  	exit();	 
}

if ($action=="load_drop_down_machine")
{
	$data_ex=explode("_",$data);
	$company_id=$data_ex[0];
	$floor_id=$data_ex[1];
	//echo $company_id."_".$floor_id; die;
	if($floor_id==0 || $floor_id=="") $floor_cond=""; else $floor_cond=" and floor_id=$floor_id";
	if($db_type==0)
	{
		$sql="select id,concat(machine_no,'-',brand) as machine_name from lib_machine_name where category_id=3 and company_id=$company_id and status_active=1 and is_deleted=0 and is_locked=0 $floor_cond order by machine_name";
	}
	else if($db_type==2)
	{
		$sql="select id, machine_no || '-' || brand as machine_name from lib_machine_name where category_id=6 and company_id=$company_id and status_active=1 and is_deleted=0 and is_locked=0 $floor_cond order by machine_name"; //category_id=3
		//echo $sql; die;
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
	if($exdata[1]==1) // $company_cond 
	{
		echo create_drop_down( "cbo_buyer_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "--Select Party--", $exdata[2], "",1);
	}
	else if($exdata[1]==2)
	{
		echo create_drop_down("cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$exdata[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buy.buyer_name", "id,buyer_name", 1, "--Select Party--", $exdata[2], "", 1);
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
                            $search_by_arr=array(1=>"Wash Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( '<? echo $cbo_company_id; ?>'+'_'+document.getElementById('txt_search_recipe').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_year_selection').value, 'create_recipe_search_list_view', 'search_div', 'dry_production_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
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
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	
	$production_qty_arr=array();
	$prod_data_arr="select a.recipe_id, sum(b.qcpass_qty) as qty from subcon_embel_production_mst a, subcon_embel_production_dtls b where a.id=b.mst_id and a.entry_form=301 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.recipe_id";
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
                            <th width="70">Process Name</th>
                            <th width="70">Wash Type</th>
                            <th width="70">Color</th>
                            <th width="70">Order Qty</th>
                            <th width="70">Prod Qty</th>
                            <th>Balance Qty</th>
						</thead>
					</table>
					<div style="width:1070px; overflow-y:scroll; max-height:300px;">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1050" class="rpt_table" id="list_view" >
							<?
							$sql= "select a.id, a.job_no_prefix_num, a.subcon_job, a.party_id, a.within_group, b.id as order_id, b.order_no, b.buyer_po_id, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.color_id, sum(c.qnty) as qty, d.id as recipe_id, d.recipe_no_prefix_num, d.recipe_no from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, pro_recipe_entry_mst d where a.subcon_job=b.job_no_mst and b.id=c.mst_id and a.subcon_job=d.job_no and b.id=d.po_id 
							
							and b.main_process_id=d.embl_name and b.gmts_item_id=d.gmts_item and b.embl_type=d.embl_type and b.body_part=d.body_part and c.color_id=d.color_id
							
							and a.entry_form=295 and d.entry_form=300 and a.status_active=1 and b.status_active=1 and d.status_active=1 $order_rcv_date $company $search_com_cond $search_recipe_cond $po_idsCond group by a.id, a.job_no_prefix_num, a.subcon_job, a.party_id, a.within_group, b.id, b.order_no, b.buyer_po_id, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.color_id, d.id, d.recipe_no_prefix_num, d.recipe_no order by d.recipe_no_prefix_num DESC";
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
								$str=$row[csf('recipe_id')].'___'.$row[csf('recipe_no')].'___'.$row[csf('subcon_job')].'___'.$row[csf('order_id')].'___'.$row[csf('order_no')].'___'.$row[csf('party_id')].'___'.$row[csf('within_group')].'___'.$row[csf('qty')].'___'.$row[csf('buyer_po_id')].'___'.$buyer_po_arr[$row[csf("buyer_po_id")]]['po'].'___'.$buyer_po_arr[$row[csf("buyer_po_id")]]['style'];
								
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


if ($action == "batch_popup") 
{
	echo load_html_head_contents("Batch Info", "../../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	?>
    <script>
        function js_set_value(datas) 
        {
            //alert (batch_id);
            document.getElementById('selected_str_data').value = datas;
            parent.emailwindow.hide();
        }
    </script>
    </head>
    <body>
    <div align="center">
        <fieldset style="width:750px;margin-left:0px;">
            <form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
                <table cellpadding="0" cellspacing="0" border="1" rules="all" width="750" class="rpt_table">
                    <thead>
                        <tr>
                            <th colspan="5"><? echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --"); ?></th>
                        </tr>
                        <tr>
                            <th>Batch</th>
                            <th>From Date</th>
                            <th>To Date</th>
                            <th>
                                <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton"/>
                                <input type="hidden" name="selected_str_data" id="selected_str_data" value="">
                            </th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td style="display: none"><? echo create_drop_down("cbo_search_by", 150, $order_source, "", 1, "--Select--", 2, 0, 0); ?></td>
                        <td><input type="text" style="width:240px" class="text_boxes" name="txt_search_common" id="txt_search_common"/></td>
                        <td><input id="txt_date_from" class="datepicker" type="text" value="" style="width:70px;" name="txt_date_from"></td>
                        <td><input id="txt_date_to" class="datepicker" type="text" value="" style="width:70px;" name="txt_date_to"></td>
                        <td align="center"><input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+'<? echo $cbo_company_id; ?>'+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_batch_search_list_view', 'search_div', 'dry_production_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" align="center">
							<? echo load_month_buttons(1); ?>
                        </td>
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
	exit();
}

if ($action == "create_batch_search_list_view") 
{
	//print_r ($data);
	$data = explode('_', $data);
	$search_common = trim($data[0]);
	$search_by = $data[1];
	$company_id = $data[2];
	$search_type = $data[3];
	$txt_date_from = $data[4];
	$txt_date_to = $data[5];

	if ($search_common == "") 
	{
		if($db_type==0)
		{ 
			$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd'); 
			$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd'); 
		}
		else
		{ 
			$txt_date_from=change_date_format($txt_date_from, "", "",1); 
			$txt_date_to=change_date_format($txt_date_to, "", "",1); 
		}

		if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $date_cond=""; else $date_cond=" and a.batch_date between '$txt_date_from' and '$txt_date_to'";
	}
	else $date_cond="";

	if ($search_type == 1) 
	{
		if ($search_common != '') $batch_cond = " and a.batch_no='$search_common'"; else $batch_cond = "";
	} 
	else if ($search_type == 4 || $search_type == 0) 
	{
		if ($search_common != '') $batch_cond = " and a.batch_no like '%$search_common%'"; else $batch_cond = "";
	} 
	else if ($search_type == 2) 
	{
		if ($search_common != '') $batch_cond = " and a.batch_no like '$search_common%'"; else $batch_cond = "";
	} 
	else if ($search_type == 3) 
	{
		if ($search_common != '') $batch_cond = " and a.batch_no like '%$search_common'"; else $batch_cond = "";
	}

	$po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
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
	
	$order_arr=array(); $colorid_arr=array();
	$order_sql = sql_select("select a.subcon_job, a.within_group, a.party_id, b.id, b.order_no, b.order_quantity, b.buyer_po_id from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.entry_form=295 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($order_sql as $row)
	{
		$order_arr[$row[csf('id')]]['po']=$row[csf('order_no')];
		$order_arr[$row[csf('id')]]['job']=$row[csf('subcon_job')];
		$order_arr[$row[csf('id')]]['within_group']=$row[csf('within_group')];
		$order_arr[$row[csf('id')]]['party_id']=$row[csf('party_id')];
		$order_arr[$row[csf('id')]]['qty']=$row[csf('order_quantity')];
		$order_arr[$row[csf('id')]]['buyer_po_id']=$row[csf('buyer_po_id')];
	}
	unset($order_sql);
	
	if($db_type==0) $poid_cond="group_concat(b.po_id)";
	else $poid_cond="listagg(b.po_id,',') within group (order by b.po_id)";
	
	$sql = "select a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight, a.color_id ,a.operation_type, a.sub_operation, $poid_cond as poid, sum( b.roll_no) as qtypcs from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form in (316)  and a.process_id='1' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batch_cond $date_cond
	group by a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight, a.color_id,a.operation_type, a.sub_operation order by a.id DESC";
	$nameArray = sql_select($sql);
	//echo $sql;
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="618" class="rpt_table">
            <thead>
            <th width="30">SL</th>
            <th width="70">Batch No</th>
            <th width="40">Ex.</th>
            <th width="90">Color</th>
            <th width="80">Batch Weight</th>
            <th width="80">Batch Qty(Pcs)</th>
            <th width="70">Batch Date</th>
            <th>PO No.</th>
            </thead>
        </table>
        <div style="width:618px; overflow-y:scroll; max-height:240px;" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="list_view">
				<?
				$i = 1;
				foreach ($nameArray as $row) 
				{
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					//$order_no = '';
					$order_id = array_unique(explode(",", $row[csf('poid')]));
 					$order_no =""; $subcon_job=''; $party_id=''; $within_group=''; $qty=0; $buyer_po_id=''; $sub_operation='';
					foreach ($order_id as $idpo)
					{
						if($order_no=="") $order_no=$order_arr[$idpo]['po']; else $order_no.= ",".$order_arr[$idpo]['po'];
						if($subcon_job=="") $subcon_job=$order_arr[$idpo]['job']; else $subcon_job.= ",".$order_arr[$idpo]['job'];
						if($party_id=="") $party_id=$order_arr[$idpo]['party_id']; else $party_id.= ",".$order_arr[$idpo]['party_id'];
						if($within_group=="") $within_group=$order_arr[$idpo]['within_group']; else $within_group.= ",".$order_arr[$idpo]['within_group'];
						if($buyer_po_id=="") $buyer_po_id=$order_arr[$idpo]['buyer_po_id']; else $buyer_po_id.= ",".$order_arr[$idpo]['buyer_po_id'];
						
						$qty+=$order_arr[$idpo]['qty'];
					}	
					
					$order_no=implode(", ",array_unique(explode(",",$order_no)));
					$subcon_job=implode(", ",array_unique(explode(",",$subcon_job)));
					$party_id=implode(", ",array_unique(explode(",",$party_id)));
					$within_group=implode(", ",array_unique(explode(",",$within_group)));	
					$buyer_po_id=implode(", ",array_unique(explode(",",$within_group)));
					
					$exbuyer_po_id=	array_unique(explode(", ", $buyer_po_id));	
					$buyer_po=""; $buyer_style="";
					foreach ($exbuyer_po_id as $idbuyerpo)
					{
						if($buyer_po=="") $buyer_po=$buyer_po_arr[$idbuyerpo]['po']; else $buyer_po.= ",".$buyer_po_arr[$idbuyerpo]['po'];
						if($buyer_style=="") $buyer_style=$buyer_po_arr[$idbuyerpo]['style']; else $buyer_style.= ",".$buyer_po_arr[$idbuyerpo]['style'];
					}
					
					$buyer_po=implode(", ",array_unique(explode(",",$buyer_po)));	
					$buyer_style=implode(", ",array_unique(explode(",",$buyer_style)));
					$suboperation=array_unique(explode(",", $row[csf('sub_operation')]));
					foreach ($suboperation as $sub)
					{
						$sub_operation .=$wash_sub_operation_arr[$sub];
					}
					//$sub_operation = implode(","$wash_sub_operation_arr[array_unique(explode(",", $row[csf('sub_operation')]))]);
					//echo $sub_operation.'=='; 
					$str=$row[csf('id')].'___'.$row[csf('batch_no')].'___'.$subcon_job.'___'.chop($row[csf('poid')],',').'___'.$order_no.'___'.$party_id.'___'.$within_group.'___'.$qty.'___'.$buyer_po_id.'___'.$buyer_po.'___'.$buyer_style.'___'.$row[csf('operation_type')].'___'.chop($sub_operation,',');
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value('<? echo $str; ?>')">
                        <td width="30"><? echo $i; ?></td>
                        <td width="70"><p><? echo $row[csf('batch_no')]; ?></p></td>
                        <td width="40"><? echo $row[csf('extention_no')]; ?>&nbsp;</td>
                        <td width="90"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                        <td width="80" align="right"><p><? echo $row[csf('batch_weight')]; ?></p></td>
                        <td width="80" align="right"><p>&nbsp;<? echo $row[csf('qtypcs')]; ?></p></td>
                        <td width="70" align="center"><p><? echo change_date_format($row[csf('batch_date')]); ?></p></td>
                        <td><p><? echo $order_no ; ?>&nbsp;</p></td>
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

if($action=="order_details")
{
	$data=explode("***",$data);
	$company_id=$data[0];
	$batch_id=$data[1];
	$update_id=$data[2];

	$item_group_arr=return_library_array( "select id,item_name from lib_item_group",'id','item_name');
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	
	$buyer_po_arr=array();
	
	$po_sql ="Select a.style_ref_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
	}
	unset($po_sql_res);
	
	$prod_data_arr=array(); //$recipe_prod_id_arr=array(); $product_data_arr=array();
	if($update_id!=0)
	{	
		//echo "select id, color_size_id, production_date, production_hour, qcpass_qty, operator_name, shift_id, remarks from subcon_embel_production_dtls where mst_id=$update_id and status_active=1 and is_deleted=0";
		$prodData=sql_select("select id, po_id, production_date, production_hour, qcpass_qty, reje_qty,rewash_qty, operator_name, shift_id, remarks from subcon_embel_production_dtls where mst_id=$update_id and status_active=1 and is_deleted=0");
		foreach($prodData as $row)
		{
			$data_arr[$row[csf('po_id')]]['production_date']=$row[csf('production_date')];
			$data_arr[$row[csf('po_id')]]['production_hour']=$row[csf('production_hour')];
			$data_arr[$row[csf('po_id')]]['id']=$row[csf('id')];
			$data_arr[$row[csf('po_id')]]['qcpass_qty']=$row[csf('qcpass_qty')];
			$data_arr[$row[csf('po_id')]]['reje_qty']=$row[csf('reje_qty')];
			$data_arr[$row[csf('po_id')]]['rewash_qty']=$row[csf('rewash_qty')];
			$data_arr[$row[csf('po_id')]]['operator_name']=$row[csf('operator_name')];
			$data_arr[$row[csf('po_id')]]['shift_id']=$row[csf('shift_id')];
			$data_arr[$row[csf('po_id')]]['remarks']=$row[csf('remarks')];
		}
		unset($prodData);
	}
	//echo "<pre>";
	//print_r($data_arr);

	/*$sql= "select  a.id, a.job_no_prefix_num, a.subcon_job, a.party_id, a.within_group, b.id as order_id, b.order_no, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, b.buyer_po_id, c.id as color_size_id, c.color_id, c.size_id, sum(c.qnty) as qty, d.id as recipe_id, d.recipe_no_prefix_num, d.recipe_no from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, pro_recipe_entry_mst d where a.subcon_job=b.job_no_mst and b.id=c.mst_id and a.subcon_job=d.job_no and b.id=d.po_id 
							
	and b.main_process_id=d.embl_name and b.gmts_item_id=d.gmts_item and b.embl_type=d.embl_type and b.body_part=d.body_part and c.color_id=d.color_id
	
	and a.entry_form=295 and d.entry_form=300 and a.status_active=1 and a.company_id='$company_id' and d.id='$recipe_id' group by a.id, a.job_no_prefix_num, a.subcon_job, a.party_id, a.within_group, b.id, b.order_no, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, b.buyer_po_id, c.id, c.color_id, c.size_id, d.id, d.recipe_no_prefix_num, d.recipe_no order by c.id ASC";*/

	/*$sql = "select a.id, a.batch_no,a.process_id , a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.entry_form, b.roll_no, b.po_id, b.batch_qnty as qty ,c.subcon_job, c.party_id, c.within_group, d.id as order_id, d.order_no, d.buyer_po_id, d.main_process_id, d.gmts_item_id, d.embl_type, d.body_part ,e.id as color_size_id from pro_batch_create_mst a, pro_batch_create_dtls b ,subcon_ord_mst c, subcon_ord_dtls d,subcon_ord_breakdown e where a.id=b.mst_id and b.po_id=d.id and c.subcon_job=d.job_no_mst and d.id=e.mst_id and a.id= $batch_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in (316)";*/
	$sql = "select a.id, a.batch_no, a.process_id, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.entry_form, b.roll_no, b.po_id, b.batch_qnty as qty, c.subcon_job, c.party_id, c.within_group, d.id as order_id, d.order_no, d.buyer_po_id, d.main_process_id, d.gmts_item_id, d.gmts_color_id
	from pro_batch_create_mst a, pro_batch_create_dtls b, subcon_ord_mst c, subcon_ord_dtls d where a.id=b.mst_id and b.po_id=d.id and c.subcon_job=d.job_no_mst and c.id=d.mst_id and a.id= $batch_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in (316)";

	//echo $sql; die;
	$prod_data_arr=sql_select($sql);

	$i=1; 
	foreach($prod_data_arr as $row)
	{
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		$process_id = array_unique(explode(",", $row[csf('process_id')]));
		foreach ($process_id as $val)
		{
			if ($process_name == "") $process_name =$wash_type[$val]; else $process_name.= ",".$wash_type[$val];
		}
		$process_name=implode(", ",array_unique(explode(",",$process_name)));
		//echo "<pre>";
		//print_r($prod_data_arr); 
		//echo $prod_data_arr[$row[csf('color_size_id')]]['qcpass_qty']; die;
		$upid=$data_arr[$row[csf('po_id')]]['id'];
		$qcpass_qty=$data_arr[$row[csf('po_id')]]['qcpass_qty'];
		$rej_qty=$data_arr[$row[csf('po_id')]]['reje_qty'];
		$rewash_qty=$data_arr[$row[csf('po_id')]]['rewash_qty'];
		$operator_name=$data_arr[$row[csf('po_id')]]['operator_name'];
		$shift_id=$data_arr[$row[csf('po_id')]]['shift_id'];
		$remarks=$data_arr[$row[csf('po_id')]]['remarks'];
		//echo $data_arr[$row[csf('color_size_id')]]['qcpass_qty']."=="; die;
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" name="tr[]" id="tr_<? echo $i;?>">
			<td align="center"><? echo $i; ?></td>
            <td style="word-break:break-all"><? echo $buyer_po_arr[$row[csf("buyer_po_id")]]['style']; ?>&nbsp;</td>
            <td style="word-break:break-all"><? echo $buyer_po_arr[$row[csf("buyer_po_id")]]['po']; ?>&nbsp;</td>
            <td style="word-break:break-all"><? echo $row[csf("order_no")]; ?>&nbsp;</td>
            <td style="word-break:break-all"><? echo $garments_item[$row[csf('gmts_item_id')]]; ?>&nbsp;</td>
            <td style="word-break:break-all"><? echo $color_arr[$row[csf('gmts_color_id')]]; ?>&nbsp;</td>
            <td style="word-break:break-all"><? echo $process_name; ?>&nbsp;</td>
            <td align="right"><input type="text" name="OrderQty[]" id="OrderQty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $qcpass_qty; ?>" readonly/></td>
             <td align="right" id="yourElementId"><input type="text" name="totalprevdryqty[]" id="totalprevdryqty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? ?>" readonly/></td>
            
			<td align="right"><input type="text" name="txtProdQty[]" id="txtProdQty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" placeholder="Write" value="<? echo $qcpass_qty; ?>" onBlur="fnc_total_calculate();" />
            
            
            <input type="hidden" name="prevdryqty[]" id="prevdryqty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" placeholder="Write" value="<?  ?>" />
            
            
            <input type="hidden" name="prevrejectQty[]" id="prevrejectQty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" placeholder="" value="<?  ?>" />
            <input type="hidden" name="prevreturnQty[]" id="prevreturnQty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" readonly value="<?  ?>" />
            
            </td>
            
            <td><input type="text" name="txtRejQty[]" id="txtRejQty_<? echo $i;?>" class="text_boxes_numeric" style="width:70px" placeholder="Write" value="<? echo $rej_qty; ?>" onBlur="fnc_total_calculate(<? echo $i;?>);" /></td>
            
            <td align="right"><input type="text" name="txtReWashQty[]" id="txtReWashQty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" placeholder="Write" value="<? echo $rewash_qty; ?>" onBlur="fnc_total_calculate(<? echo $i;?>);" /></td>
            <td>
            	<input type="text" name="txtRemarks[]" id="txtRemarks_<? echo $i;?>" class="text_boxes" style="width:90px" placeholder="Write" value="<? echo $remarks; ?>" />
                <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i;?>" style="width:50px" value="<? echo $upid; ?>" />
                <input type="hidden" name="txtbuyerPoId[]" id="txtbuyerPoId_<? echo $i;?>" style="width:50px" value="<? echo $row[csf('buyer_po_id')]; ?>" />
                <input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i;?>" style="width:50px" value="<? echo $row[csf('po_id')]; ?>" />
				<input type="hidden" name="colorSizeId[]" id="colorSizeId_<? echo $i;?>" style="width:50px" value="<? //echo $row[csf('color_size_id')]; ?>" />
            </td>
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
             <fieldset>
                <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
                    <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                        <thead> 
                        	<tr>
                                <th colspan="7"><? echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --"); ?></th>
                            </tr>
                            <tr>              	 
                                <th width="150">Location</th>
                                <th width="100">Production ID</th>
                                <th width="100">Search By</th>
                            	<th width="100" id="search_by_td">Wash Job No</th>
                                <th width="130" colspan="2">Prod. Date Range</th>
                                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:90px;" /></th> 
                            </tr>          
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td><? echo create_drop_down( "cbo_location", 150, "select id, location_name from lib_location where company_id='$cbo_company_id' and status_active =1 and is_deleted=0 order by location_name ASC","id,location_name", 1, "-Select Location-", 0, "","","","","","",3 ); ?></td>
                                <td><input name="txt_prod_no" id="txt_prod_no" class="text_boxes" style="width:90px"></td>
                                <td style="display: none;"><input name="txt_recipe_no" id="txt_recipe_no" class="text_boxes" style="width:90px"></td>
                                <td>
									<?
                                        $search_by_arr=array(1=>"Wash Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
                                        echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                                    ?>
                                </td>
                                <td>
                                    <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                                </td>
                                
                                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" placeholder="From Date" style="width:60px"></td>
                                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" style="width:60px"></td> 
                                <td align="center">
                                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( '<? echo $cbo_company_id; ?>'+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('txt_prod_no').value+'_'+document.getElementById('txt_recipe_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value, 'create_production_no_list_view', 'search_div', 'dry_production_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:90px;" />
                                    
                                     <input type="hidden" id="hidden_production_data" name="hidden_production_data" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="7" align="center" valign="middle">
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
			if($search_by==1) $search_job_cond="and a.job_no='$search_str'";
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
			if($search_by==1) $search_job_cond="and a.job_no like '%$search_str%'";  
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
			if($search_by==1) $search_job_cond="and a.job_no like '$search_str%'";  
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
			if($search_by==1) $search_job_cond="and a.job_no like '%$search_str'";  
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
	if ($po_ids!="") $po_idsCond=" and a.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	
	$spo_ids='';
	if(($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2))
	{
		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.subcon_job=b.job_no_mst $search_com_cond", "id");
	}
	if ( $spo_ids!="") $spo_idsCond=" and a.order_id in ($spo_ids)"; else $spo_idsCond="";
	
	if($company_id==0) { echo "Select Company first"; die; }
	
	if($location_id !="0") $location_cond= "and a.location_id = $location_id"; else $location_cond= "";
	if($prod_no!="") $system_no_cond=" and a.sys_no like '%".trim($prod_no)."%'"; else $system_no_cond="";
	if($recipe_no!="") $recipe_no_cond=" and a.sys_no like '%".trim($recipe_no)."%'"; else $recipe_no_cond="";
	//echo "sdlkjklsdj";
	//if($data[4]!="" && $data[5]!="") $date_cond=" and product_date between '".$date_from."' and '".$date_to."'"; else $date_cond="";
	$batchData=sql_select("select id, batch_no, operation_type, sub_operation from pro_batch_create_mst where status_active=1 and is_deleted=0");
	foreach($batchData as $row)
	{
		$batch_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
		$batch_arr[$row[csf('id')]]['sub_operation']=$row[csf('sub_operation')];
		$batch_arr[$row[csf('id')]]['operation_type']=$row[csf('operation_type')];
	}

	foreach($prodData as $row)
	{
		$prod_data_arr[$row[csf('mst_id')]]['production_date']=change_date_format($row[csf('production_date')]);
		$prod_data_arr[$row[csf('mst_id')]]['production_hour']=$row[csf('production_hour')];
		$prod_data_arr[$row[csf('mst_id')]]['operator_name']=$row[csf('operator_name')];
		$prod_data_arr[$row[csf('mst_id')]]['shift_id']=$row[csf('shift_id')];
	}

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
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);

	$order_arr=array();
	$order_sql = sql_select("SELECT a.subcon_job, a.within_group, a.party_id, b.id, b.order_no, b.order_quantity as qty, b.order_uom from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.entry_form=295 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($order_sql as $row)
	{
		$order_arr[$row[csf('id')]]['order_no']=$row[csf('order_no')];
		$order_arr[$row[csf('id')]]['within_group']=$row[csf('within_group')];
		$order_arr[$row[csf('id')]]['party_id']=$row[csf('party_id')];
		$order_arr[$row[csf('id')]]['qty']+=$row[csf('qty')];
		$order_arr[$row[csf('id')]]['uom']=$row[csf('order_uom')];
	}
	unset($order_sql);
	
	//$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst", 'id', 'batch_no');
	
	?>
	<body>
		<div align="center">
			<fieldset style="width:670px;margin-left:10px">
				<form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="670" class="rpt_table" >
						<thead>
							<th width="30">SL</th>
							<th width="150">Production No</th>
                            <th width="90">Prod. Date</th>
                            <th width="150">Job NO</th>
                            <th>Order</th>
						</thead>
					</table>
					<div style="width:670px; overflow-y:scroll; max-height:300px;">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="tbl_list_search" >
							<?
							$sql="SELECT a.id, a.prefix_no_num, a.sys_no, a.location_id, a.recipe_id, a.job_id, a.job_no, b.po_id , a.order_id, a.floor_id, a.machine_id, a.product_date,b.production_date from subcon_embel_production_mst a, subcon_embel_production_dtls b where a.id=b.mst_id and a.entry_form=342 and a.status_active = 1 and a.is_deleted = 0 and a.company_id='$company_id' $location_cond $system_no_cond $recipe_no_cond $spo_idsCond $po_idsCond $date_cond $search_job_cond group by a.id, a.prefix_no_num, a.sys_no, a.location_id, a.recipe_id, a.job_id, a.job_no, b.po_id , a.order_id, a.floor_id, a.machine_id, a.product_date,b.production_date order by a.id DESC";// $date_cond
							//echo $sql; //die;
							$sql_res=sql_select($sql);

							$i=1;  $sub_operation=''; $batch_no='';  $operation_type='';
							foreach($sql_res as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$suboperation=array_unique(explode(",", $batch_arr[$row[csf('recipe_id')]]['sub_operation']));
								foreach ($suboperation as $sub)
								{
									$sub_operation .=$wash_sub_operation_arr[$sub];
								}
								//$batch_no=$batch_arr[$row[csf('recipe_id')]]['batch_no'];
								//$operation_type=$batch_arr[$row[csf('recipe_id')]]['operation_type'];
								$str_data=""; 
								if($order_arr[$row[csf('po_id')]]['uom']==2) $ord_qty_pcs==$order_arr[$row[csf('po_id')]]['qty']*12; else $ord_qty_pcs==$order_arr[$row[csf('po_id')]]['qty'];
								//$ord_qty_pcs=$order_arr[$row[csf('po_id')]]['qty']*12;
								$str_data=$row[csf('id')].'***'.$row[csf('sys_no')].'***'.$row[csf('location_id')].'***'.$row[csf('job_no')].'***'.$row[csf('order_id')].'***'.$order_arr[$row[csf('po_id')]]['order_no'].'***'.$order_arr[$row[csf('po_id')]]['within_group'].'***'.$order_arr[$row[csf('po_id')]]['party_id'].'***'.$ord_qty_pcs.'***'.$prod_data_arr[$row[csf('id')]]['production_date'].'***'.$prod_data_arr[$row[csf('id')]]['production_hour'].'***'.$prod_data_arr[$row[csf('id')]]['operator_name'].'***'.$prod_data_arr[$row[csf('id')]]['shift_id'].'***'.$row[csf('floor_id')].'***'.$row[csf('machine_id')].'***'.$row[csf('job_id')];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $str_data; ?>')"> 
									<td width="30" align="center"><?php echo $i; ?></td>	
									<td width="150" align="center"><?php echo $row[csf('sys_no')]; ?></td>
                                    <td width="90"><?php echo change_date_format($row[csf('production_date')]); ?>&nbsp;</td>
                                    <td width="150"><?php echo $row[csf('job_no')]; ?></td>
                                    <td style="word-break:break-all"><?php echo $order_arr[$row[csf('po_id')]]['order_no']; ?></td>
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
		
		if(str_replace("'","",$update_id) !='')
		{
			$field_array_update="location_id*job_no*job_id*order_id*floor_id*machine_id*updated_by*update_date";
			$data_array_update="".$cbo_location."*".$txt_job_no."*".$txt_job_id."*'".$txt_order_id."'*".$cbo_floor_id."*".$cbo_machine_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			$id=str_replace("'","",$update_id);
			$production_no=str_replace("'","",$txt_production_id);
		}
		else
		{
			$new_return_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'DP', date("Y",time()), 5, "select id,prefix_no,prefix_no_num from  subcon_embel_production_mst where company_id=$cbo_company_id and entry_form=342 $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
			$id=return_next_id( "id", "subcon_embel_production_mst", 1 ) ; 
			$field_array="id, prefix_no, prefix_no_num, sys_no, company_id, location_id, job_no, job_id, order_id, floor_id, machine_id, inserted_by, insert_date, status_active, is_deleted, entry_form";
			$data_array="(".$id.",'".$new_return_no[1]."','".$new_return_no[2]."','".$new_return_no[0]."',".$cbo_company_id.",".$cbo_location.",".$txt_job_no.",".$txt_job_id.",".$hid_order_id.",".$cbo_floor_id.",".$cbo_machine_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,342)";
			$production_no=$new_return_no[0];
		}
		
		$data_array_dtls=""; $issave=1;
		$id_dtls=return_next_id( "id", "subcon_embel_production_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, po_id, buyer_po_id, job_no, production_date, production_hour, qcpass_qty, reje_qty, order_qty, operator_name, shift_id, process_id, wash_type_id, remarks, inserted_by, insert_date, status_active, is_deleted";
		//echo "10**"; //die; 
		for($i=1;$i<=$total_row;$i++)
		{
			//$colorSizeId="colorSizeId_".$i; 
			
			$txtbuyerPoId="txtbuyerPoId_".$i;
			$txtPoId="txtPoId_".$i;
			$txtProdQty="txtProdQty_".$i;
			$txtRejQty="txtRejQty_".$i;
			$hidOrderQty="hidOrderQty_".$i;
			$txtProcesId="txtProcesId_".$i;
			$txtWashType="txtWashType_".$i;
			$txtRemarks="txtRemarks_".$i;
			$updateIdDtls="updateIdDtls_".$i;
			if($db_type==0)
			{
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$id_dtls.",".$id.",'".$$txtPoId."','".$$txtbuyerPoId."',".$txt_job_no.",".$txt_prod_date.",".$txt_reporting_hour.",'".$$txtProdQty."','".$$txtRejQty."','".$$hidOrderQty."',".$txt_super_visor.",".$cboShift.",'".$$txtProcesId."',".$$txtWashType.",'".$$txtRemarks."','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."',1,0)"; 
					
				$id_dtls=$id_dtls+1;
				$issave=1;
			}
			else
			{
				$txt_hour="";
				$txt_hour=str_replace("'","",$txt_prod_date)." ".str_replace("'","",$txt_reporting_hour);
				$txtreporting_hour="to_date('".$txt_hour."','DD MONTH YYYY HH24:MI:SS')";
				//$field_array_dtls="id, mst_id, color_size_id, po_id, buyer_po_id, job_no, production_date, production_hour, qcpass_qty, operator_name, shift_id, remarks, inserted_by, insert_date, status_active, is_deleted";
				if($data_array_dtls!="") $data_array_dtls.=",";
				$field_array_dtls="id, mst_id, po_id, buyer_po_id, job_no, production_date, production_hour, qcpass_qty, reje_qty, order_qty, operator_name, shift_id, process_id, wash_type_id, remarks, inserted_by, insert_date, status_active, is_deleted";
				$data_array_dtls.="INSERT INTO subcon_embel_production_dtls (".$field_array_dtls.") VALUES (".$id_dtls.",".$id.",'".$$txtPoId."','".$$txtbuyerPoId."',".$txt_job_no.",".$txt_prod_date.",".$txtreporting_hour.",'".$$txtProdQty."','".$$txtRejQty."','".$$hidOrderQty."',".$txt_super_visor.",".$cboShift.",'".$$txtProcesId."',".$$txtWashType.",'".$$txtRemarks."','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."',1,0)";// die;
				$id_dtls=$id_dtls+1;
				//echo "10**$issave**".$data_array_dtls; die;
				if($issave==1)
				{
					$issave=execute_query($data_array_dtls);
					$data_array_dtls='';
					//echo "10**$issave**".$data_array_dtls; die;
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
			if(str_replace("'","",$update_id) !='')
			{
				$rID=sql_update("subcon_embel_production_mst",$field_array_update,$data_array_update,"id",$update_id,0);
			}
			else
			{
				$rID=sql_insert("subcon_embel_production_mst",$field_array,$data_array,0);
			}
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
		//echo "10**".$issave; die;
		//echo "10**".$rID."**".$rID1."**".$issave."**".$flag; die;
		//10**0**1**1**0
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$id)."**".$production_no."**".str_replace("'",'',$txt_job_id)."**".str_replace("'",'',$txt_job_no);
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
				echo "0**".str_replace("'",'',$id)."**".$production_no."**".str_replace("'",'',$txt_job_id)."**".str_replace("'",'',$txt_job_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id);
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
		
		/*$qc_no=return_field_value( "sys_no", "subcon_embel_production_mst"," recipe_id=$update_id and status_active=1 and is_deleted=0 and entry_form=302");
		if($qc_no){
			echo "emblQc**".str_replace("'","",$txt_production_id)."**".$qc_no;
			die;
		}*/
		
		/*$prod_sql_dtls="Select id from subcon_embel_production_dtls where mst_id=$update_id and status_active=1 and is_deleted=0";
		$all_dtls_id_arr=array();
		//echo "10**".$rec_sql_dtls; die;
		$nameArray=sql_select( $prod_sql_dtls ); 
		foreach($nameArray as $row)
		{
			$all_dtls_id_arr[]=$row[csf('id')];
		}
		unset($nameArray);*/
		
		$field_array_update="location_id*job_no*job_id*order_id*floor_id*machine_id*updated_by*update_date";
		
		$data_array_update="".$cbo_location."*".$txt_job_no."*".$txt_job_id."*'".$txt_order_id."'*".$cbo_floor_id."*".$cbo_machine_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 		
		
		$data_array_dtls_update="";
		//$field_array_dtls="id, mst_id, po_id, buyer_po_id, job_no, production_date, production_hour, qcpass_qty, reje_qty, order_qty, operator_name, shift_id, process_id, wash_type_id, remarks, inserted_by, insert_date, status_active, is_deleted";
		$field_array_dtls_update="po_id*buyer_po_id*job_no*production_date*production_hour*qcpass_qty*reje_qty*shift_id*process_id*wash_type_id*remarks*updated_by*update_date";
		
		$production_no=str_replace("'","",$txt_production_id);
		
		$data_array_dtls="";
		/*$id_dtls=return_next_id( "id", "subcon_embel_production_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, color_size_id, po_id, buyer_po_id, job_no, production_date, production_hour, qcpass_qty, reje_qty, rewash_qty, operator_name, shift_id, remarks, inserted_by, insert_date, status_active, is_deleted";*/
		$issave=1; //echo "10**";
		for($i=1;$i<=$total_row;$i++)
		{
			$txtbuyerPoId="txtbuyerPoId_".$i;
			$txtPoId="txtPoId_".$i;
			$txtProdQty="txtProdQty_".$i;
			$txtRejQty="txtRejQty_".$i;
			$hidOrderQty="hidOrderQty_".$i;
			$txtProcesId="txtProcesId_".$i;
			$txtWashType="txtWashType_".$i;
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
				//$field_array_dtls_update="po_id*buyer_po_id*job_no*production_date*production_hour*qcpass_qty*reje_qty*shift_id*process_id*wash_type_id*remarks*updated_by*update_date";
				//if($data_array_dtls_update != "") $data_array_dtls_update .= ","; 	
				$data_array_dtls_update[$updateIds] = explode("*",("".$$txtPoId."*".$$txtbuyerPoId."*".$txt_job_no."*".$txt_prod_date."*".$txtreportingHour."*'".$$txtProdQty."'*'".$$txtRejQty."'*".$cboShift."*'".$$txtProcesId."'*".$$txtWashType."*'".$$txtRemarks."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				$issave=1;
				$id_arr_pro[]=$updateIds;
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
				//echo "10**".bulk_update_sql_statement( "subcon_embel_production_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $updateIdDtls_array); die;
				$rID2=execute_query(bulk_update_sql_statement( "subcon_embel_production_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $updateIdDtls_array ),1);
				if($rID2==1 && $flag==1) $flag=1; else $flag=0;
			}
		}
		
		$field_arr_del="status_active*is_deleted*updated_by*update_date";
		$data_arr_del="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$distance_delete_id="";
		//echo "10**";
		//print_r($id_arr_pro);
		//die;
	
		/*if(implode(',',$id_arr_pro)!="")
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
		}*/
		
		//echo "10**".$rID."**".$rID2; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_id)."**".$production_no."**".str_replace("'",'',$txt_job_id)."**".str_replace("'",'',$txt_job_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$update_id)."**".$production_no."**".str_replace("'",'',$txt_job_id)."**".str_replace("'",'',$txt_job_no);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$update_id)."**".$production_no."**".str_replace("'",'',$txt_job_id)."**".str_replace("'",'',$txt_job_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id)."**".$production_no."**".str_replace("'",'',$txt_job_id)."**".str_replace("'",'',$txt_job_no);
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
		//echo "10**".str_replace("'",'',$update_id); die;
		$qc_no=return_field_value( "sys_no", "subcon_embel_production_mst"," recipe_id=$update_id and status_active=1 and is_deleted=0 and entry_form=302");
		if($qc_no){
			echo "emblQc**".str_replace("'","",$txt_production_id)."**".$qc_no;
			disconnect($con); die;
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

function sql_updates($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit)
{

	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);

	if(count($arrUpdateFields)!=count($arrUpdateValues)){
		return "0";
	}

	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value;
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues;
	}
	$strQuery .=" WHERE ";

	$arrRefFields=explode("*",$arrRefFields);
	$arrRefValues=explode("*",$arrRefValues);
	if(is_array($arrRefFields))
	{
		$arrayRef = array_combine($arrRefFields,$arrRefValues);
		$Arraysize = count($arrayRef);
		$i = 1;
		foreach($arrayRef as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrRefFields."=".$arrRefValues."";
	}

	global $con;
	if( strpos($strQuery, "WHERE")==false)  return "0";
	echo "10**".$strQuery; die;
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	if ($exestd)
		return "1";
	else
		return "0";

	die;
	if ( $commit==1 )
	{
		if (!oci_error($stid))
		{
			oci_commit($con);
			return "1";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else
		return 1;
	die;
}

if($action=="wash_production_entry_print")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$size_arr=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst", 'id', 'batch_no');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	$order_arr=array();
	$order_sql = sql_select("select a.subcon_job, a.within_group, a.party_id, b.id, b.order_no, b.main_process_id, sum(c.qnty) as qty from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and b.id=c.mst_id and a.entry_form=295 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.subcon_job, a.within_group, a.party_id, b.main_process_id, b.id, b.order_no");
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
	$cust_buyer_style_array=sql_select("SELECT id, subcon_job, order_id, order_no from subcon_ord_mst where entry_form=295 and status_active=1 and is_deleted=0");
	foreach ($cust_buyer_style_array as $cust_val) 
	{
		$cust_arr[$cust_val[csf('subcon_job')]]['order_no']=$cust_val[csf('order_no')]; 
		$cust_arr[$cust_val[csf('subcon_job')]]['job']=$cust_val[csf('subcon_job')]; 
	}
	unset($cust_buyer_style_array);*/
	$sql="select id, prefix_no_num, sys_no, location_id, recipe_id, job_no, order_id from subcon_embel_production_mst where entry_form=301 and status_active = 1 and is_deleted = 0 and company_id='$company_id' $location_cond $system_no_cond $recipe_no_cond order by id DESC";

	$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst", 'id', 'batch_no');
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
	
	$sql_mst = "select id, prefix_no_num, sys_no, location_id, recipe_id, job_no, order_id, buyer_po_id from subcon_embel_production_mst where entry_form=301 and id='$data[1]'";
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
                <td><? echo $batch_arr[$dataArray[0][csf('recipe_id')]]; ?></td>
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
				

				$sql= "select  a.id, a.subcon_job, b.id as order_id, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.id as color_size_id, c.color_id, c.size_id, sum(c.qnty) as qty from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, pro_recipe_entry_mst d where a.subcon_job=b.job_no_mst and b.id=c.mst_id and a.subcon_job=d.job_no and b.id=d.po_id 
							
				and b.main_process_id=d.embl_name and b.gmts_item_id=d.gmts_item and b.embl_type=d.embl_type and b.body_part=d.body_part and c.color_id=d.color_id
				and a.entry_form=295 and d.entry_form=300 and a.status_active=1 and a.company_id='$com_id' and d.id='$recipe_id' group by a.id, a.subcon_job, b.id,b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.id, c.color_id, c.size_id, d.recipe_no_prefix_num order by c.id ASC";
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


if ($action=="job_popup")
{
	echo load_html_head_contents("Job Popup Info","../../../", 1, 1, $unicode,'','');
	?>
	<script>
		function js_set_value(id)
		{ 
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_popup(type,within_group)
		{
			var company = $('#cbo_company_name').val();
			var party_name = $('#cbo_party_name').val();
			var location_name = $('#cbo_location_name').val();
			load_drop_down( 'wash_order_entry_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Wash Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer PO');
			else if(val==5) $('#search_by_td').html('Buyer Style');
		}
	</script>
</head>
<body>
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
                    <th width="100" id="search_by_td">Wash Job No</th>
                    <th width="100">Year</th>
                    <th width="170">Date Range</th>                            
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                </tr>           
            </thead>
            <tbody>
                <tr class="general">
                    <td><input type="hidden" id="selected_job"><? $data=explode("_",$data); ?>  <!--  echo $data;-->
                        <? 
                        echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
                    </td>
                    <td>
                        <?php echo create_drop_down( "cbo_within_group", 100, $yes_no,"", 0, "--  --", $data[3], "fnc_load_party_popup(1,this.value);" ); ?>
                    </td>
                    <td id="buyer_td">
                        <? echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );   	 
                        ?>
                    </td>
                    <td>
						<?
                            $search_by_arr=array(1=>"Wash Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer PO",5=>"Buyer Style");
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value, 'create_job_search_list_view', 'search_div', 'dry_production_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
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
	$party_id=str_replace("'","",$data[1]);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}

	if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			else if ($search_by==3) $search_com_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no = '$search_str' ";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		
		if($search_str!="")
		{
			//echo $search_type; die;
			
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'"; 
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'"; 
			else if ($search_by==3) $search_com_cond=" and a.job_no_prefix_num like '%$search_str%'";
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str%'"; 
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str%'";   
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
			else if ($search_by==3) $search_com_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '$search_str%'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '$search_str%'";  
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			else if ($search_by==3) $search_com_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str'";  
		}
	}	

	if($party_id!=0) $party_id_cond=" and a.party_id='$party_id'"; else $party_id_cond="";

	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $order_rcv_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $order_rcv_date ="";
	}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	if($within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
	
	/*$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}*/
	//echo $po_ids;
	//if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$buyer_po_arr=array();
	if($within_group==1)
	{
		$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		}
		unset($po_sql_res);
	}
	
	if($db_type==0)
	{
		$ins_year_cond="year(a.insert_date)";
		$color_id_str="group_concat(c.color_id)";
		$buyer_po_id_str="group_concat(b.buyer_po_id)";
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str="listagg(b.gmts_color_id,',') within group (order by b.gmts_color_id)";
		$buyer_po_id_str="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
	}
	$sql= "select a.id, a.subcon_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date,b.buyer_po_no,b.buyer_style_ref, $color_id_str as color_id, $buyer_po_id_str as buyer_po_id
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	where a.entry_form=295 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $order_rcv_date $company $buyer $withinGroup $search_com_cond   $withinGroup and b.id=c.mst_id  and c.process in(2,3) 
	group by a.id, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date,b.buyer_po_no,b.buyer_style_ref
	order by a.id DESC";
	 //echo $sql;
	 $data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="885" >
        <thead>
            <th width="30">SL</th>
            <th width="60">Job No</th>
            <th width="60">Year</th>
            <th width="120">W/O No</th>
            <th width="100">Buyer PO</th>
            <th width="100">Buyer Style</th>
            <th width="80">Ord Receive Date</th>
            <th width="80">Delivery Date</th>
            <th>Color</th>
        </thead>
        </table>
        <div style="width:885px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="865" class="rpt_table" id="tbl_po_list">
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
					if($color_name=="") $color_name=$color_arr[$color_id]; else $color_name.=','.$color_arr[$color_id];
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
				
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('subcon_job')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    <td width="120"><? echo $row[csf('order_no')]; ?></td>
                    <td width="100" style="word-break:break-all"><? if ($within_group==1)echo $buyer_po; else echo $row[csf('buyer_po_no')];//echo $buyer_po; ?></td>
                    <td width="100" style="word-break:break-all"><? if ($within_group==1)echo $buyer_style; echo $row[csf('buyer_style_ref')];//echo $buyer_style; ?></td>
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
 
if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id, subcon_job, company_id, location_id, party_id, currency_id, exchange_rate, party_location, floor_id, delivery_date, rec_start_date, rec_end_date, receive_date, within_group, party_location, order_id, order_no, conv_factor,gmts_type from subcon_ord_mst where id='$data' and status_active=1" );
	foreach ($nameArray as $row)
	{
		$buyer_data=$row[csf("company_id")].'_'.$row[csf("within_group")].'_'.$row[csf("party_id")];
		$floor_data=$row[csf("company_id")].'__'.$row[csf("location_id")];
		echo "document.getElementById('txt_job_no').value 			= '".$row[csf("subcon_job")]."';\n";  
		echo "document.getElementById('txt_job_id').value 			= '".$row[csf("id")]."';\n";  
		echo "document.getElementById('cbo_company_id').value 		= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_within_group').value 	= '".$row[csf("within_group")]."';\n";  
		echo "document.getElementById('cbo_location').value 		= '".$row[csf("location_id")]."';\n";  
		echo "load_drop_down( 'requires/dry_production_controller', '".$buyer_data."', 'load_drop_down_buyer', 'buyer_td' );\n";
		//echo "load_drop_down( 'requires/dry_production_controller', '".$floor_data."', 'load_drop_down_floor', 'floor_td' );\n";
		//echo "document.getElementById('cbo_floor_id').value 		= '".$row[csf("floor_id")]."';\n";
		echo "document.getElementById('hid_order_id').value          = '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('txt_order_no').value         = '".$row[csf("order_no")]."';\n";
		echo "$('#txt_order_no').attr('disabled','true')".";\n";
		//echo "$('#cbo_party_name').attr('disabled','true')".";\n";
		echo "set_button_status(0,'".$_SESSION['page_permission']."', 'fnc_embel_entry',1);\n";	
	}
	exit();	
}

if ($action == 'load_stock_by_prodQty') 
{
	$data = explode('**', $data);
  	$txtPoId = $data[0];
	$txtProcesId = $data[1];
	$txtWashType = $data[2];
	$OrderQty = $data[3];
	$updateIdDtls = $data[4]*1;
	$txtProdQty = $data[5];
	
	$job_no = $data[6];
	
	
	
	
	
	  $sql_iss="select a.id, a.mst_id, a.quantity, a.uom, a.job_dtls_id, a.buyer_po_id,a.requisition_dtls_id from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.entry_form=297 and b.embl_job_no='$job_no'  and b.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			$sql_iss_res =sql_select($sql_iss);  $issue_qty_arr=array();
			foreach ($sql_iss_res as $rows)
			{
				$issue_qty_arr[$rows[csf("job_dtls_id")]]['quantity']+=$rows[csf("quantity")];
			}
			unset($sql_iss_res);
			
			$issue_return_data_arr=array();
		 $sql_issue_return="select a.sys_no, b.quantity, b.job_dtls_id, b.buyer_po_id from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=4 and a.embl_job_no='$job_no'  and a.entry_form=436 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			$sql_issue_return_res =sql_select($sql_issue_return);
			foreach ($sql_issue_return_res as $row)
			{
 				$issue_return_data_arr[$row[csf("job_dtls_id")]]['qty']+=$row[csf("quantity")];
			}
			unset($sql_issue_return_res);
	
	
	            $issue_qty=$issue_qty_arr[$txtPoId]['quantity'];
				$issue_return_qty=$issue_return_data_arr[$txtPoId]['qty'];
				$actual_issue_return_qty=$issue_qty-$issue_return_qty;
				
				//echo $actual_issue_return_qty; die;
	
	
	
	
	
	
	
	
	    $sql_delv_return= "select  b.order_id,sum(b.delivery_qty) as delivery_qty from subcon_delivery_mst a, subcon_delivery_dtls b, subcon_ord_dtls c
where a.id=b.mst_id and b.order_id=c.id and a.entry_form='360' and  b.entry_form='360' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  b.order_id=$txtPoId and  b.next_process=3 group by b.order_id";
	   $sql_delv_return_res =sql_select($sql_delv_return);
	   
	  
	   
	   /* $delv_return_qty_arr=array();
	    foreach ($sql_delv_return_res as $rows)
		{
			$delv_return_qty_arr[$rows[csf("order_id")]]['delivery_qty']=$rows[csf("delivery_qty")];
		}*/

	     $prev_prd_sql="select  b.po_id,b.process_id, b.wash_type_id,sum(b.qcpass_qty) as prv_dry_qty,sum(b.reje_qty) as reje_qty 
			 from subcon_embel_production_mst a,subcon_embel_production_dtls b
			where a.id=b.mst_id and a.entry_form in(342) and b.po_id=$txtPoId and b.process_id=$txtProcesId and b.wash_type_id=$txtWashType and  a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.po_id,b.process_id, b.wash_type_id";
			$prevprdsql=sql_select($prev_prd_sql);
			/*$dry_prod_arr=array();
			foreach($prevprdsql as $row)
			{
				$dry_prod_arr[$row[csf('process_id')]][$row[csf('wash_type_id')]][$row[csf('po_id')]]['prv_dry_qty']=$row[csf('prv_dry_qty')];
				$dry_prod_arr[$row[csf('process_id')]][$row[csf('wash_type_id')]][$row[csf('po_id')]]['reje_qty']=$row[csf('reje_qty')];
			}*/

			$totalPrevBalanceQty=0;$prv_dry_qty=0;$reje_qty=0;$balance_qty=0;$totalprevdryqty=0; $prv_dry_qty=0;$reje_qty=0;$delv_return_qty=0;
				
				
				$qty_pcs=$actual_issue_return_qty;//$OrderQty*1;
				$prv_dry_qty= $prevprdsql[0][csf('prv_dry_qty')]*1;;
				$reje_qty= $prevprdsql[0][csf('reje_qty')]*1;;
				$delv_return_qty= $sql_delv_return_res[0][csf('delivery_qty')]*1;
				
				$totalprevdryqty=$prv_dry_qty+$reje_qty;
				$totalPrevBalanceQty=$totalprevdryqty-$delv_return_qty;
				
				
				
				if($updateIdDtls>0)
				{
					//echo $prv_dry_qty."hhh".$reje_qty."hhhss".$delv_return_qty; die;
					
					$totalPrevBalanceQty=$totalPrevBalanceQty-$txtProdQty;
					$balance_qty=$qty_pcs-$totalPrevBalanceQty;
				}
				else
				{
					$totalPrevBalanceQty=$totalPrevBalanceQty;
					$balance_qty=$qty_pcs-$totalPrevBalanceQty;
					echo "document.getElementById('txtProdQty_1').value = '".$balance_qty."';\n";
				}
				
				
	
		echo "document.getElementById('totalprevdryqty_1').value = '".$totalPrevBalanceQty."';\n";
		echo "document.getElementById('prevdryqty_1').value = '".$prv_dry_qty."';\n";
		echo "document.getElementById('prevrejectQty_1').value = '".$reje_qty."';\n";
		
	

	exit();
}
if($action=="show_fabric_desc_listview")
{
	//echo $data; die;
	//$data=explode('_',$data);

	/*$order_id=$data[0];
	$process_id=$data[1];
	$company_id=$data[3];*/
	//echo "select id, style_ref_no from fabric_sales_order_mst where po_id in ($data[0])";die;
	//$batch_arr=return_library_array( "select id, prod_id, item_description from lib_subcon_charge",'id','const_comp');	
	//$gsm_arr=return_library_array( "select id,gsm from lib_subcon_charge",'id','gsm');
	
	//$style_array=return_library_array( "select id, style_ref_no from fabric_sales_order_mst where id in ($data[0])",'id','style_ref_no');
	//$uom_array=return_library_array( "select id, unit_of_measure from product_details_master ",'id','unit_of_measure');

	/*$production_qty_array=array();
	$prod_sql="Select batch_id, cons_comp_id, sum(product_qnty) as product_qnty from subcon_production_dtls where batch_id='$data[2]' and status_active=1 and is_deleted=0 group by  batch_id, cons_comp_id";
	$prod_data_sql=sql_select($prod_sql);
	foreach($prod_data_sql as $row)
	{
		$production_qty_array[$row[csf('batch_id')]][$row[csf('cons_comp_id')]]=$row[csf('product_qnty')];
	}*/

	/*$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, b.id, b.po_number,a.job_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
	}
	unset($po_sql_res);*/
	//var_dump($production_qty_array);
	//$main_batch_allow = return_field_value("dyeing_fin_bill", "variable_settings_subcon", "company_id =$company_id and variable_list=13 and is_deleted=0 and status_active=1");
	//$entry_form_cond='';
	//if($main_batch_allow==1) $entry_form_cond=" and a.entry_form in(0,281) and a.process_id like '%35%' "; else $entry_form_cond="and a.entry_form =281 ";

    $data=explode('_',$data);
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	/*$sql= "select a.id, a.subcon_job, a.job_no_prefix_num, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.order_id, a.delivery_date,b.buyer_po_no,b.id as po_id ,b.buyer_style_ref,b.gmts_item_id, b.gmts_color_id, b.buyer_po_id,b.order_quantity,b.order_uom,c.process, c.embellishment_type
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	where a.entry_form=295 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and b.id=c.mst_id and a.id='$data' and c.process in(2,3)
	group by a.id, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.order_id, a.delivery_date,b.id,b.buyer_po_no,b.buyer_style_ref,b.gmts_item_id, b.gmts_color_id, b.buyer_po_id,b.order_quantity,b.order_uom,c.process, c.embellishment_type
	order by a.id DESC";*/
	
	
	/* $sql_job="select a.id, a.within_group, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.buyer_po_no,b.buyer_style_ref,b.party_buyer_name, b.gmts_item_id, 3 as main_process_id, b.order_uom, b.gmts_color_id as color_id, b.gmts_size_id, b.order_quantity as qnty, $process_type_cond as process_type,e.delivery_qty,e.id as delivery_return_dtls_id,e.delivery_dtls_id,e.remarks,e.next_process,e.order_last_sequence,e.order_last_process, e.order_sequnce_break_id
			from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c,subcon_delivery_mst d,subcon_delivery_dtls e
			where a.entry_form=295 and a.subcon_job=b.job_no_mst  and a.id=b.mst_id and  d.entry_form=360 and e.entry_form=360  and d.id=e.mst_id and b.id=e.order_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id=c.mst_id  and d.id=$update_id 
			group by a.id, a.within_group, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, b.buyer_po_no,b.buyer_style_ref,b.party_buyer_name, a.delivery_date, b.id, b.buyer_po_id, b.gmts_item_id, b.order_uom, b.gmts_color_id, b.gmts_size_id, b.order_quantity,e.delivery_qty,e.id,e.delivery_dtls_id,e.remarks,e.next_process,e.order_last_sequence,e.order_last_process, e.order_sequnce_break_id
			order by b.id ASC";*/
			
			
			 $sql_iss="select a.id, a.mst_id, a.quantity, a.uom, a.job_dtls_id, a.buyer_po_id,a.requisition_dtls_id from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.entry_form=297 and b.embl_job_no='$data[1]'  and b.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			$sql_iss_res =sql_select($sql_iss);  $issue_qty_arr=array();
			foreach ($sql_iss_res as $rows)
			{
				$issue_qty_arr[$rows[csf("job_dtls_id")]]['quantity']+=$rows[csf("quantity")];
			}
			unset($sql_iss_res);
			
			$issue_return_data_arr=array();
			$sql_issue_return="select a.sys_no, b.quantity, b.job_dtls_id, b.buyer_po_id from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=4 and a.embl_job_no='$data[1]'  and a.entry_form=436 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			$sql_issue_return_res =sql_select($sql_issue_return);
			foreach ($sql_issue_return_res as $row)
			{
 				$issue_return_data_arr[$row[csf("job_dtls_id")]]['qty']+=$row[csf("quantity")];
			}
			unset($sql_issue_return_res);
			
			
	
	  $sql_delv_return= "select  b.order_id,b.delivery_qty from subcon_delivery_mst a, subcon_delivery_dtls b, subcon_ord_dtls c
where a.id=b.mst_id and b.order_id=c.id and a.entry_form='360' and  b.entry_form='360' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.mst_id='$data[0]' and  b.next_process=3";
	   $sql_delv_return_res =sql_select($sql_delv_return); $delv_return_qty_arr=array();
	    foreach ($sql_delv_return_res as $rows)
		{
			$delv_return_qty_arr[$rows[csf("order_id")]]['delivery_qty']+=$rows[csf("delivery_qty")];
		}
	
	 $prev_prd_sql="select  b.po_id,b.process_id, b.wash_type_id, b.qcpass_qty  as prv_dry_qty,b.reje_qty as reje_qty 
			 from subcon_embel_production_mst a,subcon_embel_production_dtls b
			where a.id=b.mst_id and a.entry_form in(342) and  a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$prevprdsql=sql_select($prev_prd_sql);
			$dry_prod_arr=array();
			foreach($prevprdsql as $row)
			{
				$dry_prod_arr[$row[csf('process_id')]][$row[csf('wash_type_id')]][$row[csf('po_id')]]['prv_dry_qty']+=$row[csf('prv_dry_qty')];
				$dry_prod_arr[$row[csf('process_id')]][$row[csf('wash_type_id')]][$row[csf('po_id')]]['reje_qty']+=$row[csf('reje_qty')];
			}
	
	$sql= "select a.id, a.subcon_job, a.job_no_prefix_num, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.order_id, a.delivery_date,b.buyer_po_no,b.id as po_id ,b.buyer_style_ref,b.gmts_item_id, b.gmts_color_id, b.buyer_po_id,b.order_quantity,b.order_uom
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	where a.entry_form=295 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and b.id=c.mst_id and a.id='$data[0]' and c.process in(2,3)
	group by a.id, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.order_id, a.delivery_date,b.id,b.buyer_po_no,b.buyer_style_ref,b.gmts_item_id, b.gmts_color_id, b.buyer_po_id,b.order_quantity,b.order_uom
	order by a.id DESC";
	
	// and b.po_id in ($data[0]) group by a.batch_no, a.extention_no, a.color_id, b.id, b.prod_id, b.item_description 
	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="350">
         <thead>  	 	 	 	
            <th width="30">SL</th>
            <th width="60">Buyer Style</th>
            <th width="100">Buyer PO </th>
            <th width="70">Gmts Item</th>
            <th width="70">Gmts Color</th>
            <th>Order Qty</th>
        </thead>
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				/*$excolor_id=array_unique(explode(",",$row[csf('color_id')]));
				$color_name="";	
				foreach ($excolor_id as $color_id)
				{
					if($color_name=="") $color_name=$color_arr[$color_id]; else $color_name.=','.$color_arr[$color_id];
				}
				$buyer_po=""; $buyer_style="";
				$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
				foreach($buyer_po_id as $po_id)
				{
					if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
				}
				$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));*/
				
				$prv_dry_qty=0;$reje_qty=0;$delv_return_qty=0;$totalprevdryqty=0;$balance_qty=0;$totalPrevBalanceQty=0; $qty_pcs=0; $issue_qty=0;$issue_return_qty=0;$actual_issue_return_qty=0;
				if($row[csf('order_uom')]==2) $qty_pcs=$row[csf('order_quantity')]*12; else $qty_pcs=$row[csf('order_quantity')];
				
				
				
				//$prv_dry_qty=$dry_prod_arr[$row[csf('po_id')]]['prv_dry_qty'];
				//$reje_qty=$dry_prod_arr[$row[csf('po_id')]]['reje_qty'];
				$delv_return_qty=$delv_return_qty_arr[$row[csf("po_id")]]['delivery_qty'];
				
				//$totalprevdryqty=$prv_dry_qty+$reje_qty;
				//$totalPrevBalanceQty=$totalprevdryqty-$delv_return_qty;
				//$balance_qty=$qty_pcs-$totalPrevBalanceQty;
				
				$issue_qty=$issue_qty_arr[$row[csf("po_id")]]['quantity'];
				$issue_return_qty=$issue_return_data_arr[$row[csf("po_id")]]['qty'];
				$actual_issue_return_qty=$issue_qty-$issue_return_qty;
				
				
                ?>
                 <tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $row[csf('po_id')]."**".$row[csf('buyer_style_ref')]."**".$row[csf('buyer_po_no')]."**".$row[csf('buyer_po_id')]."**".$row[csf('order_no')]."**".$garments_item[$row[csf('gmts_item_id')]]."**".$color_arr[$row[csf('gmts_color_id')]]."**".$qty_pcs."**".$row[csf('order_id')]."**".$prv_dry_qty."**".$reje_qty."**".$totalprevdryqty."**".$balance_qty."**".$delv_return_qty."**".$actual_issue_return_qty; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf('buyer_style_ref')]; ?></td>
                    <td width="120"><? echo $row[csf('buyer_po_no')]; ?></td>
                    <td width="80" ><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></td>
                    <td width="80" ><? echo $color_arr[$row[csf('gmts_color_id')]]; ?></td>
                    <td ><? echo $qty_pcs; ?></td>
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


if ($action=="dry_production_list_view")
{
	$data=explode('_',$data);
	?>	
	<div style="width:100%;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="60">Buyer Style</th>
                <th width="100">Buyer PO </th>
                <th width="90" align="center">Process Name</th>
                <th width="120" align="center">Wash Type</th>
                <th width="80" align="center">Order Qty</th>
                <th width="80" align="center">Production Qty (Pcs)</th>                    
                <th align="center">Reject Qty (Pcs)</th>
            </thead>
        </table>
    </div>
    <div style="width:100%;max-height:180px; overflow:y-scroll" id="sewing_production_list_view" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
        <?php  
			$i=1;
			/*$main_batch_allow = return_field_value("dyeing_fin_bill", "variable_settings_subcon", "company_id =$data[1] and variable_list=13 and is_deleted=0 and status_active=1");
			$entry_form_cond='';
			if($main_batch_allow==1) $entry_form_cond=" entry_form in(0,281) and process_id like '%35%'"; else $entry_form_cond=" entry_form =281 ";*/

			$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
			
			
			
			 $sql_iss="select a.id, a.mst_id, a.quantity, a.uom, a.job_dtls_id, a.buyer_po_id,a.requisition_dtls_id from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.entry_form=297 and b.embl_job_no='$data[2]'  and b.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			$sql_iss_res =sql_select($sql_iss);  $issue_qty_arr=array();
			foreach ($sql_iss_res as $rows)
			{
				$issue_qty_arr[$rows[csf("job_dtls_id")]]['quantity']+=$rows[csf("quantity")];
			}
			unset($sql_iss_res);
			
			$issue_return_data_arr=array();
			$sql_issue_return="select a.sys_no, b.quantity, b.job_dtls_id, b.buyer_po_id from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=4 and a.embl_job_no='$data[2]'  and a.entry_form=436 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			$sql_issue_return_res =sql_select($sql_issue_return);
			foreach ($sql_issue_return_res as $row)
			{
 				$issue_return_data_arr[$row[csf("job_dtls_id")]]['qty']+=$row[csf("quantity")];
			}
			unset($sql_issue_return_res);
			
			
			
			
			
			
			
			
			
			 $sql_delv_return= "select  b.order_id,b.delivery_qty from subcon_delivery_mst a, subcon_delivery_dtls b, subcon_ord_dtls c
where a.id=b.mst_id and b.order_id=c.id and a.entry_form='360' and  b.entry_form='360' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.mst_id='$data[1]' and  b.next_process=3";
	   $sql_delv_return_res =sql_select($sql_delv_return); $delv_return_qty_arr=array();
	    foreach ($sql_delv_return_res as $rows)
		{
			$delv_return_qty_arr[$rows[csf("order_id")]]['delivery_qty']+=$rows[csf("delivery_qty")];
		}
			
			$order_sql= "select b.id , b.buyer_po_no ,b.buyer_style_ref,b.gmts_item_id, b.gmts_color_id, b.buyer_po_id,b.order_quantity from subcon_ord_dtls b where b.mst_id='$data[1]' group by b.id,b.buyer_po_no,b.buyer_style_ref,b.gmts_item_id, b.gmts_color_id, b.buyer_po_id,b.order_quantity";
			
			$order_sql_result=sql_select($order_sql); $order_array=array();
			
			foreach ($order_sql_result as $row)
			{
				$order_array[$row[csf("id")]]["po_id"]=$row[csf("po_id")];
				$order_array[$row[csf("id")]]["buyer_po_no"]=$row[csf("buyer_po_no")];
				$order_array[$row[csf("id")]]["buyer_style_ref"]=$row[csf("buyer_style_ref")];
				$order_array[$row[csf("id")]]["gmts_item_id"]=$row[csf("gmts_item_id")];
				$order_array[$row[csf("id")]]["gmts_color_id"]=$row[csf("gmts_color_id")];
				$order_array[$row[csf("id")]]["buyer_po_id"]=$row[csf("buyer_po_id")];
				$order_array[$row[csf("id")]]["order_quantity"]=$row[csf("order_quantity")];
			}
			
			/*$prev_prd_sql="select  b.po_id,b.qcpass_qty  as prv_dry_qty,b.reje_qty as reje_qty 
			 from subcon_embel_production_mst a,subcon_embel_production_dtls b
			where a.id=b.mst_id and a.entry_form in(342) and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$prevprdsql=sql_select($prev_prd_sql);
			$dry_prod_arr=array();
			foreach($prevprdsql as $row)
			{
				$dry_prod_arr[$row[csf('po_id')]]['prv_dry_qty']+=$row[csf('prv_dry_qty')];
				$dry_prod_arr[$row[csf('po_id')]]['reje_qty']+=$row[csf('reje_qty')];
			}
			*/
			
			//print_r($order_array); die;
			$pdate_cond=($db_type==2)? " TO_CHAR(production_hour,'HH24:MI') " : " TIME_FORMAT( production_hour, '%H:%i' ) ";
			$sql="select id, mst_id,po_id, color_size_id, production_date, $pdate_cond as production_hour, qcpass_qty, reje_qty,operator_name, shift_id, remarks, process_id, wash_type_id, order_qty from subcon_embel_production_dtls where status_active=1 and is_deleted=0 and mst_id='$data[0]' order by id ASC";
			
			//$machine_arr=return_library_array( "dry_production_controller.php"; 
			$sql_result =sql_select($sql);
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
				/*$process_id=explode(',',$row[csf('process')]);
				$process_val='';
				foreach ($process_id as $val)
				{
					if($process_val=='') $process_val=$conversion_cost_head_array[$val]; else $process_val.=",".$conversion_cost_head_array[$val];
				}

				if($main_batch_allow==1)
				{
					$within_group=1;
				}
				else
				{
					$within_group=$batch_array[$key]['within_group'];
				}*/
				$prv_dry_qty=0;$reje_qty=0;$totalprevdryqty=0; $order_quantity=0; $delv_return_qty=0; $issue_qty=0;$issue_return_qty=0;$actual_issue_return_qty=0;
				$po_id=$row[csf('po_id')];
				//$order_array[$po_id]['po_id'];
				$buyer_po_no=$order_array[$po_id]['buyer_po_no'];
				$buyer_style_ref=$order_array[$po_id]['buyer_style_ref'];
				$gmts_item_id=$order_array[$po_id]['gmts_item_id'];
				$gmts_color_id=$order_array[$po_id]['gmts_color_id'];
				$buyer_po_id=$order_array[$po_id]['buyer_po_id'];
				$order_quantity=$order_array[$po_id]['order_quantity'];
				
				$delv_return_qty=$delv_return_qty_arr[$po_id]['delivery_qty'];
				
				//$prv_dry_qty=$dry_prod_arr[$row[csf('po_id')]]['prv_dry_qty'];
				//$reje_qty=$dry_prod_arr[$row[csf('po_id')]]['reje_qty'];
				//$totalprevdryqty=$prv_dry_qty+$reje_qty;
				
				$issue_qty=$issue_qty_arr[$row[csf("po_id")]]['quantity'];
				$issue_return_qty=$issue_return_data_arr[$row[csf("po_id")]]['qty'];
				$actual_issue_return_qty=$issue_qty-$issue_return_qty;
				

				$click_data=$po_id."**".$buyer_style_ref."**".$buyer_po_no."**".$buyer_po_id."**".' '."**".$garments_item[$gmts_item_id]."**".$color_arr[$gmts_color_id]."**".$order_quantity."**".$row[csf('process_id')]."**".$row[csf('wash_type_id')]."**".$row[csf('qcpass_qty')]."**".$row[csf('reje_qty')]."**".$row[csf('remarks')]."**".$row[csf('id')]."**".$prv_dry_qty."**".$reje_qty."**".$row[csf('order_qty')]."**".$totalprevdryqty."**".$delv_return_qty."**".$actual_issue_return_qty;

				if($row[csf('process_id')]==2) $typeArray=$wash_dry_process; else  $typeArray=$wash_laser_desing;
				?>
                 <tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data_update("<? echo $click_data; ?>")' style="cursor:pointer" >
                    <td width="30" align="center"><? echo $i; ?></td>
                     <td width="60" align="center"><p><? echo $buyer_style_ref; ?></p></td>
                      <td width="100" align="center"><p><? echo $buyer_po_no; ?></p></td>
                    <td width="90" align="center"><p><? echo $wash_type[$row[csf('process_id')]]; ?></p></td>
                    <td width="120" align="center"><p><? echo $typeArray[$row[csf('wash_type_id')]]; ?></p></td>
                    <td width="80" align="center"><p><? echo $row[csf('order_qty')]; ?></p></td>
                    <td width="80" align="center"><p><? echo $row[csf('qcpass_qty')]; ?></p></td>
                    <td  align="center"><p><? echo $row[csf('reje_qty')]; ?></p></td>
                </tr>
			<?php
            $i++;
        }
        ?>
        </table>
	</div>
	<?	
}
?>