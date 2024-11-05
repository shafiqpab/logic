<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];


if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-Select Location-", $selected, "load_drop_down('requires/embl_qc_controller', document.getElementById('cbo_company_id').value+'__'+this.value, 'load_drop_down_floor', 'floor_td');","","","","","",3 );
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
		echo create_drop_down( "cbo_floor_id", 150, $blank_array,"", 1, "--Select Floor--", 0, "",1 );
	}
	else
	{
		//echo "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=3 and b.company_id=$company_id and b.status_active=1 and b.is_deleted=0 and a.production_process=8 $location_cond group by a.id, a.floor_name order by a.floor_name";
		echo create_drop_down( "cbo_floor_id", 150, "SELECT a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=3 and b.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_process=8  group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "--Select Floor--", 0, "",1 );
	}
  	exit();	 
}
if ($action == "load_drop_down_buyer") 
{
	$exdata=explode("_",$data);
	if($exdata[1]==1)
	{
		echo create_drop_down( "cbo_buyer_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Party--", $exdata[2], "",1);
	}
	else if($exdata[1]==2)
	{
		echo create_drop_down("cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$exdata[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buy.buyer_name", "id,buyer_name", 1, "--Select Party--", $exdata[2], "", 1);
	}
	exit();
}

if($action=="production_popup")
{
	echo load_html_head_contents("Production Pop-up","../../../", 1, 1, $unicode,'','');
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
			else if(val==6) $('#search_by_td').html('IR/IB');
		}
	</script>
</head>
<body>
<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="680" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead> 
                <tr>
                    <th colspan="7"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",1 ); ?></th>
                </tr>
                <tr>               	 
                    <th width="140">Production</th>
                    <th width="100">Search By</th>
                    <th width="100" id="search_by_td">Buyer Po</th>
                    <th width="60">Job Year</th>
                    <th width="130" colspan="2">Production Date Range</th>                            
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /> </th>
                </tr>           
            </thead>
            <tbody>
                <tr class="general">
                    <td><input type="hidden" id="selected_str_data">
                        <input type="text" name="txt_search_production" id="txt_search_production" class="text_boxes" style="width:90px" placeholder="Search Production" />
                    </td>
                    <td>
						<?
                            $search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style",6=>"IR/IB");
                            echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",4,'search_by(this.value)',"","" );
                        ?>
                    </td>
                    <td align="center">
                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                    </td>
                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td align="center"><input name="txt_date_from" id="txt_date_from" class="datepicker" placeholder="From" style="width:60px"></td>
                    <td align="center"><input name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To" style="width:60px"></td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( '<? echo $cbo_company_id; ?>'+'_'+document.getElementById('txt_search_production').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_year_selection').value, 'create_production_search_list_view', 'search_div', 'embl_qc_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
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

if($action=="create_production_search_list_view")
{
	$exdata=explode('_',$data);
	$cbo_company_id=$exdata[0];
	$search_production=$exdata[1];
	$form_date=$exdata[2];
	$to_date=$exdata[3];
	
	$search_by=$exdata[4];
	$search_str=trim($exdata[5]);
	$search_type =$exdata[6];
	$year =$exdata[7];
	$within_group=$exdata[8];
	
	if($cbo_company_id!=0) $company=" and a.company_id='$cbo_company_id'"; else { echo "Please Select Company First."; die; }
	
	/*$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
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
	
	if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";*/

	$sysid_cond = ""; $rec_des_cond = ""; $job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond=""; $int_ref_no="";
	if ($search_type == 1) 
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond.=" and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond.=" and a.order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and e.job_no = '$search_str' ";
			else if ($search_by==4) $search_com_cond.=" and b.buyer_po_no = '$search_str' ";
			else if ($search_by==5) $search_com_cond.=" and b.buyer_style_ref = '$search_str' ";
			else if ($search_by==6) $int_ref_no.=" and e.grouping = '$search_str' ";
		}
		if ($recipe_no != '') $recipe_no_cond = " and recipe_no_prefix_num=$recipe_no";
		if ($rec_des != '') $rec_des_cond = " and recipe_description='$rec_des'";
	} 
	else if ($search_type == 4 || $search_type == 0) 
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond.=" and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond.=" and a.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and e.job_no like '%$search_str%'";  
			else if ($search_by==4) $search_com_cond.=" and b.buyer_po_no like '%$search_str%'"; 
			else if ($search_by==5) $search_com_cond.=" and b.buyer_style_ref like '%$search_str%'";
			else if ($search_by==6) $int_ref_no.=" and e.grouping like '%$search_str%'";
		}
		if ($recipe_no != '') $recipe_no_cond = " and recipe_no_prefix_num like '%$recipe_no%' ";
		if ($rec_des != '') $rec_des_cond = " and recipe_description like '%$rec_des%'";
	} 
	else if ($search_type == 2) 
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond.=" and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond.=" and a.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and e.job_no like '$search_str%'";  
			else if ($search_by==4) $search_com_cond.=" and b.buyer_po_no like '$search_str%'";
			else if ($search_by==5) $search_com_cond.=" and b.buyer_style_ref like '$search_str%'";  
			else if ($search_by==6) $int_ref_no.=" and e.grouping like '$search_str%'";  
		}
		if ($recipe_no != '') $recipe_no_cond = " and recipe_no_prefix_num like '$recipe_no%' ";
		if ($rec_des != '') $rec_des_cond = " and recipe_description like '$rec_des%'";
	} 
	else if ($search_type == 3) 
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond.=" and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond.=" and a.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and e.job_no like '%$search_str'";  
			else if ($search_by==4) $search_com_cond.=" and b.buyer_po_no like '%$search_str'";
			else if ($search_by==5) $search_com_cond.=" and b.buyer_style_ref like '%$search_str'";  
			else if ($search_by==6) $int_ref_no.=" and e.grouping like '%$search_str'";  
		}
		if ($recipe_no != '') $recipe_no_cond = " and recipe_no_prefix_num like '%$recipe_no' ";
		if ($rec_des != '') $rec_des_cond = " and recipe_description like '%$rec_des'";
	}

	if($db_type==0)
	{ 
		if ($form_date!="" &&  $to_date!="") $prod_date_cond = "and b.production_date between '".change_date_format($form_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'"; else $prod_date_cond ="";
		
		$year_select="YEAR(a.insert_date)";
		$year_cond=" and YEAR(a.insert_date)=$year";
	}
	else
	{
		if ($form_date!="" &&  $to_date!="") $prod_date_cond = "and b.production_date between '".change_date_format($form_date, "", "",1)."' and '".change_date_format($to_date, "", "",1)."'"; else $prod_date_cond ="";
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
	
	$production_qty_arr=array();
	$prod_data_arr="select a.id, sum(b.qcpass_qty) as qty from subcon_embel_production_mst a, subcon_embel_production_dtls b where a.id=b.mst_id and a.entry_form=222 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id";
	$prod_data_res=sql_select($prod_data_arr);
	
	foreach($prod_data_res as $row)
	{
		$production_qty_arr[$row[csf('id')]]=$row[csf('qty')];
	}
	unset($prod_data_res);
	
	/*$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);*/
	$buyer_po_arr=array();
	/*$order_sql ="SELECT a.job_no_prefix_num, a.within_group,b.id,b.buyer_po_no,b.buyer_style_ref, b.job_no_mst,b.order_no,b.buyer_buyer,b.main_process_id,b.gmts_item_id, b.embl_type, b.body_part,c.qnty,c.color_id from subcon_ord_mst a, subcon_ord_dtls b , subcon_ord_breakdown c 
		left join wo_booking_dtls e on c.order_id=e.booking_mst_id $job_cond and e.is_deleted=0 and e.status_active=1
		where a.id=b.mst_id and a.embellishment_job=c.job_no_mst and b.id=c.mst_id and a.entry_form='204' $search_com_cond ";*/

		$order_sql ="SELECT a.job_no_prefix_num, a.within_group,a.party_id,b.id,b.buyer_po_no,b.buyer_style_ref, b.job_no_mst,b.order_no,b.buyer_buyer,b.main_process_id,b.gmts_item_id, b.embl_type, b.body_part,c.qnty,c.color_id from subcon_ord_mst a, subcon_ord_dtls b , subcon_ord_breakdown c, wo_booking_dtls e where a.id=b.mst_id and a.embellishment_job=c.job_no_mst and b.id=c.mst_id and c.order_id=e.booking_mst_id $job_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1 and a.entry_form='204' and a.within_group=1 $search_com_cond 
		union all 
		SELECT a.job_no_prefix_num, a.within_group,a.party_id,b.id,b.buyer_po_no,b.buyer_style_ref, b.job_no_mst,b.order_no,b.buyer_buyer,b.main_process_id,b.gmts_item_id, b.embl_type, b.body_part,c.qnty,c.color_id from subcon_ord_mst a, subcon_ord_dtls b , subcon_ord_breakdown c where a.id=b.mst_id and a.embellishment_job=c.job_no_mst and b.id=c.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form='204' and a.within_group=2 $search_com_cond ";
	//$search_com_cond
	 ///echo $order_sql; die;
	$order_sql_res=sql_select($order_sql); $all_subcon_job='';
	foreach ($order_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no_prefix_num")];
		$buyer_po_arr[$row[csf("id")]]['order_no']=$row[csf("order_no")];
		$buyer_po_arr[$row[csf("id")]]['buyer_buyer']=$row[csf("buyer_buyer")];
		$buyer_po_arr[$row[csf("id")]]['within_group']=$row[csf("within_group")];
		$buyer_po_arr[$row[csf("id")]]['qty'] +=$row[csf("qnty")];
		$buyer_po_arr[$row[csf("id")]]['party_id']=$row[csf("party_id")];
		$buyer_po_arr[$row[csf("id")]]['main_process_id']=$row[csf("main_process_id")];
		$buyer_po_arr[$row[csf("id")]]['gmts_item_id']=$row[csf("gmts_item_id")];
		$buyer_po_arr[$row[csf("id")]]['embl_type']=$row[csf("embl_type")];
		$buyer_po_arr[$row[csf("id")]]['body_part']=$row[csf("body_part")];
		$buyer_po_arr[$row[csf("id")]]['color_id']=$row[csf("color_id")];

		//$po_id_arr[$row[csf("id")]]=$row[csf("id")];
		$all_subcon_job .="'".$row[csf("job_no_mst")]."'".',';
	}

	$all_subcon_job_arr=array_unique(explode(",",(chop($all_subcon_job,','))));
	//print_r($all_subcon_job_arr); die;
	if($search_com_cond!='' || $job_cond!=''){
		$con = connect();
		foreach($all_subcon_job_arr as $key=>$row_val)
		{
			//echo $row_val; die;
			$r_id2=execute_query("insert into tmp_job_no (userid, job_no, entry_form) values ($user_id,$row_val,223)");
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
		$subcon_cond=" and a.job_no in (select job_no from tmp_job_no where userid=$user_id and entry_form=223) ";
	}
	if($search_production!='') $search_production_cond=" and a.prefix_no_num='$search_production'"; else $search_production_cond="";
	?>
    <body>
		<div align="center">
			<fieldset style="width:1070px;">
				<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1070" class="rpt_table" align="center">
						<thead>
							<th width="30">SL</th>
							<th width="60">Production</th>
                            <th width="100">Job</th>
                            <th width="90">Order</th>
                            <th width="100">Buyer Po</th>
            				<th width="100">Buyer Style</th>
                            <th width="90">Gmts. Item</th>
                            <th width="80">Body Part</th>
                            <th width="80">Embl. Name</th>
                            <th width="80">Embl. Type</th>
                            <th width="90">Color</th>
                            <th width="50">Order Qty</th>
                            <th width="50">Prod Qty</th>
                            <th>Balance Qty</th>
						</thead>
					</table>
					<div style="width:1070px; overflow-y:scroll; max-height:300px;">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1050" class="rpt_table" id="list_view" >
							<?
							/*$sql= "select  a.id, a.job_no_prefix_num, a.embellishment_job, a.party_id, a.within_group, b.id as order_id, b.order_no, b.order_uom,b.buyer_po_id, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.color_id, sum(c.qnty) as qty, e.id as production_id, e.prefix_no_num, e.sys_no from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, pro_recipe_entry_mst d, subcon_embel_production_mst e where a.embellishment_job=b.job_no_mst and b.id=c.mst_id and a.embellishment_job=d.job_no and b.job_no_mst=c.job_no_mst and b.id=d.po_id and d.id=e.recipe_id
							and b.main_process_id=d.embl_name and b.gmts_item_id=d.gmts_item and b.embl_type=d.embl_type and b.body_part=d.body_part and c.color_id=d.color_id and a.entry_form=204 and d.entry_form=220 and e.entry_form=222 and a.status_active=1 and c.status_active=1 and c.is_deleted=0  $order_rcv_date $company $search_com_cond $search_production_cond $po_idsCond group by a.job_no_prefix_num, a.embellishment_job, a.insert_date, a.party_id, a.id, a.within_group, b.id, b.order_no, b.buyer_po_id, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.color_id, e.id, e.prefix_no_num, e.sys_no,b.order_uom order by e.id DESC";*/

							if($int_ref_no!=""){
								$sql="SELECT a.id, a.prefix_no_num, a.sys_no, a.location_id, a.recipe_id, a.job_no, a.order_ids as order_id, a.buyer_po_ids as buyer_po_id, a.floor_id, a.machine_id, a.product_date,b.production_date from subcon_embel_production_mst a, subcon_embel_production_dtls b, sub_material_mst c, sub_material_dtls d, wo_po_break_down e  where  a.id = b.mst_id and c.embl_job_no=b.job_no and c.id=d.mst_id and e.id=d.buyer_po_id and a.entry_form=222 and a.status_active = 1 and a.is_deleted = 0 and b.status_active=1 and b.is_deleted=0 $company $location_cond $system_no_cond $subcon_cond $search_production_cond $prod_date_cond $year_cond $spo_idsCond $int_ref_no group by  a.id, a.prefix_no_num, a.sys_no, a.location_id, a.recipe_id, a.job_no, a.order_ids, a.buyer_po_ids, a.floor_id, a.machine_id, a.product_date,b.production_date order by a.id DESC";
							}else{
								$sql="SELECT a.id, a.prefix_no_num, a.sys_no, a.location_id, a.recipe_id, a.job_no, a.order_ids as order_id, a.buyer_po_ids as buyer_po_id, a.floor_id, a.machine_id, a.product_date,b.production_date from subcon_embel_production_mst a, subcon_embel_production_dtls b  where a.id=b.mst_id and a.entry_form=222 and a.status_active = 1 and a.is_deleted = 0 and b.status_active=1 and b.is_deleted=0 $company $location_cond $system_no_cond $subcon_cond $search_production_cond $prod_date_cond $year_cond $spo_idsCond  group by  a.id, a.prefix_no_num, a.sys_no, a.location_id, a.recipe_id, a.job_no, a.order_ids, a.buyer_po_ids, a.floor_id, a.machine_id, a.product_date,b.production_date order by a.id DESC";
							}
							
							// echo $sql; die;
							$sql_res=sql_select($sql);

							$i=1; 
							foreach($sql_res as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$po_id=explode(",",$row[csf('order_id')]);
								$buyer_po=$buyer_style=$job_no=$order_no=$buyerBuyer=$party_id=$gmts_item_id=$body_part_id=$embl_type=$color_id=$main_process_id='';
								foreach($po_id as $val) 
								{
									//echo $val;
									if($buyer_po=="") $buyer_po=$buyer_po_arr[$val]['po']; else $buyer_po.=','.$buyer_po_arr[$val]['po'];
									if($buyer_style=="") $buyer_style=$buyer_po_arr[$val]['style']; else $buyer_style.=','.$buyer_po_arr[$val]['style'];
									if($job_no=="") $job_no=$buyer_po_arr[$val]['job']; else $job_no.=','.$buyer_po_arr[$val]['job'];
									if($order_no=="") $order_no=$buyer_po_arr[$val]['order_no']; else $order_no.=','.$buyer_po_arr[$val]['order_no'];
									if($party_id=="") $party_id=$buyer_po_arr[$val]['party_id']; else $party_id.=','.$buyer_po_arr[$val]['party_id'];
									if($gmts_item_id=="") $gmts_item_id=$buyer_po_arr[$val]['gmts_item_id']; else $gmts_item_id.=','.$buyer_po_arr[$val]['gmts_item_id'];
									if($body_part_id=="") $body_part_id=$buyer_po_arr[$val]['body_part']; else $body_part_id.=','.$buyer_po_arr[$val]['body_part'];
									if($embl_type=="") $embl_type=$buyer_po_arr[$val]['embl_type']; else $embl_type.=','.$buyer_po_arr[$val]['embl_type'];
									if($color_id=="") $color_id=$buyer_po_arr[$val]['color_id']; else $color_id.=','.$buyer_po_arr[$val]['color_id'];


									if($main_process_id=="") $main_process_id=$buyer_po_arr[$val]['main_process_id']; else $main_process_id.=','.$buyer_po_arr[$val]['main_process_id'];
									//if($buyer_buyer=="") $buyer_buyer=$buyer_po_arr[$val]['buyer_buyer']; else $buyer_buyer.=','.$buyer_po_arr[$val]['buyer_buyer'];
									$within_group=$buyer_po_arr[$val]['within_group'];
									if ($within_group==1) {
										if($buyer_buyer=="") $buyer_buyer=$buyer_library[$buyer_po_arr[$val]['buyer_buyer']]; else $buyer_buyer.=','.$buyer_library[$buyer_po_arr[$val]['buyer_buyer']];
							        }else{
							           if($buyer_buyer=="") $buyer_buyer=$buyer_po_arr[$val]['buyer_buyer']; else $buyer_buyer.=','.$buyer_po_arr[$val]['buyer_buyer'];
							        }
									if($within_group=="") $within_group=$buyer_po_arr[$val]['within_group']; else $within_group.=','.$buyer_po_arr[$val]['within_group'];
									$qty +=$buyer_po_arr[$val]['qty'];
								}
								$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
								$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
								$job_no=implode(",",array_unique(explode(",",$job_no)));
								$order_no=implode(",",array_unique(explode(",",$order_no)));
								$buyer_buyer=implode(",",array_unique(explode(",",$buyer_buyer)));
								$party_id=implode(",",array_unique(explode(",",$party_id)));
								$gmts_item_id=implode(",",array_unique(explode(",",$gmts_item_id)));
								$body_part_id=implode(",",array_unique(explode(",",$body_part_id)));
								$embl_type=implode(",",array_unique(explode(",",$embl_type)));
								$color_id=implode(",",array_unique(explode(",",$color_id)));
								$within_group=implode(",",array_unique(explode(",",$within_group)));
								$main_process_id=implode(",",array_unique(explode(",",$main_process_id)));
								if($main_process_id==1) $new_subprocess_array= $emblishment_print_type;
								else if($main_process_id==2) $new_subprocess_array= $emblishment_embroy_type;
								else if($main_process_id==3) $new_subprocess_array= $emblishment_wash_type;
								else if($main_process_id==4) $new_subprocess_array= $emblishment_spwork_type;
								else if($main_process_id==5) $new_subprocess_array= $emblishment_gmts_type;
								else $new_subprocess_array=$blank_array;
								$prod_qty=0; $balance_qty=0;
								$prod_qty=$production_qty_arr[$row[csf('id')]];
								$balance_qty=$qty-$prod_qty;




								/*

								if($row[csf('order_uom')]==1){ $qty=$row[csf('qty')];}
								if($row[csf('order_uom')]==2){  $qty=$row[csf('qty')]*12;}
								
								$prod_qty=0; $balance_qty=0;
								$prod_qty=$production_qty_arr[$row[csf('production_id')]];
								$balance_qty=$qty-$prod_qty;
								$buyer_po=""; $buyer_style="";
								//$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
								$order_id=explode(",",$row[csf('order_id')]);
								foreach($order_id as $po_id)
								{
									if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
									if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
								}
								$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
								$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));*/
								
								$str="";
								$str=$row[csf('id')].'___'.$row[csf('sys_no')].'___'.$row[csf('job_no')].'___'.$row[csf('order_id')].'___'.$order_no.'___'.$party_id.'___'.$within_group.'___'.$qty.'___'.$row[csf('buyer_po_id')].'___'.$buyer_po.'___'.$buyer_style.'___'.$row[csf('floor_id')];
								
								
							
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $str;?>')"> 
									<td width="30" align="center"><?php echo $i; ?></td>	
									<td width="60" align="center"><?php echo $row[csf('prefix_no_num')]; ?></td>
                                    <td width="100" align="center"><?php echo $row[csf('job_no')]; ?></td>
                                    <td width="90" style="word-break:break-all"><?php echo $order_no; ?></td>
                                    <td width="100" style="word-break:break-all"><p><? echo $buyer_po; ?></p></td>
                					<td width="100" style="word-break:break-all"><p><? echo $buyer_style; ?></p></td>
                                    <td width="90" style="word-break:break-all"><?php echo $garments_item[$gmts_item_id]; ?></td>
                                    <td width="80" style="word-break:break-all"><?php echo $body_part[$body_part_id]; ?></td>
                                    <td width="80" style="word-break:break-all"><?php echo $emblishment_name_array[$main_process_id]; ?></td>
                                    <td width="80" style="word-break:break-all"><?php echo $new_subprocess_array[$embl_type]; ?></td>
                                    <td width="90" style="word-break:break-all"><?php echo $color_arr[$color_id]; ?></td>
                                    <td width="50" align="right"><?php echo number_format($qty,2); ?></td>
                                    <td width="50" align="right"><?php if($prod_qty>0) {echo number_format($prod_qty,2);} else {echo "";} ?></td>
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
	//die;
	$r_id3=execute_query("delete from tmp_job_no where userid=$user_id and entry_form=223");
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
	exit();	
}

if($action=="reject_qty_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
    ?>
    <script>
		function fnc_close()
		{
			var tot_row=$('#tbl_share_details_entry tbody tr').length;
			
			var data_break_down="";
			for(var i=1; i<=tot_row; i++)
			{
				/*if (form_validation('txtFabricReject_'+i+'*txtPrintReject_'+i+'*txtPartShort_'+i,'Fabric Reject*Print Reject*Part Short')==false)
				{
					return;
				}*/
				if($("#txtFabricReject_"+i).val()=="") $("#txtFabricReject_"+i).val(0)
				if($("#txtPrintReject_"+i).val()=="") $("#txtPrintReject_"+i).val(0);
				if($("#txtPartShort_"+i).val()=="") $("#txtPartShort_"+i).val(0);
				if($("#hiddenid_"+i).val()=="") $("#hiddenid_"+i).val(0);
				if(data_break_down=="")
				{
					data_break_down+=$('#txtFabricReject_'+i).val()+'_'+$('#txtPrintReject_'+i).val()+'_'+$('#txtPartShort_'+i).val()+'_'+$('#hiddenid_'+i).val();
				}
				else
				{
					data_break_down+="__"+$('#txtFabricReject_'+i).val()+'_'+$('#txtPrintReject_'+i).val()+'_'+$('#txtPartShort_'+i).val()+'_'+$('#hiddenid_'+i).val();
				}
			}
			$('#hidden_break_tot_row').val( data_break_down );
			//alert(data_break_down);//return;
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center" style="width:100%;" >
		<form name="ratepopup_1"  id="ratepopup_1" autocomplete="off">
			<table class="rpt_table" width="430px" cellspacing="0" cellpadding="0" rules="all" id="tbl_share_details_entry">
				<thead>
					<th width="130">Fabric Reject (Defect Qty)</th>
                    <th width="130">Print Reject (Defect Qty)</th>
					<th>Part Short (Defect Qty)</th>
				</thead>
				<tbody>
					<input type="hidden" name="hidden_break_tot_row" id="hidden_break_tot_row" class="text_boxes" style="width:90px" />
					<?
					if($hdnDtlsUpdateId !='')
					{
							$data=explode('_',$data_break);
							?>
							<tr>
								<td>
                                <input type="text" id="txtFabricReject_1" name="txtFabricReject_1"  class="text_boxes_numeric" style="width:130px"  value="<? echo $data[0]; ?>"/>	
								</td>
                                <td>
                                <input type="text" id="txtPrintReject_1" name="txtPrintReject_1"  class="text_boxes_numeric" style="width:130px"  value="<? echo $data[1]; ?>"/>	
                                </td>
                                <td>
                                 <input type="text" id="txtPartShort_1" name="txtPartShort_1"  class="text_boxes_numeric" style="width:130px"  value="<? echo $data[2]; ?>"/>	
                                   <input type="hidden" id="hiddenid_1" name="hiddenid_1"  style="width:15px;" class="text_boxes" value="<? echo $job_dtls_id; ?>" />
								</td>
							</tr>
							<?
					}
					else
					{
						$data=explode('_',$data_break);
						?>
                        <tr>
                            <td><input type="text" id="txtFabricReject_1" name="txtFabricReject_1"  class="text_boxes_numeric" style="width:130px"  value=""/></td>
                            <td><input type="text" id="txtPrintReject_1" name="txtPrintReject_1"  class="text_boxes_numeric" style="width:130px"  value=""/></td>
                            <td>
                            <input type="text" id="txtPartShort_1" name="txtPartShort_1"  class="text_boxes_numeric" style="width:130px"  value=""/>
                            <input type="hidden" id="hiddenid_1" name="hiddenid_1"  class="text_boxes_numeric" style="width:130px"  value="<? echo $job_dtls_id; ?>" />
                             </td>
                        </tr>
						<?
					}
					?> 
				</tbody>
				<tfoot>
					<th colspan="4">&nbsp;</th> 
				</tfoot>
			</table> 
			<table>
				<tr>
					<td align="center"><input type="button" name="main_close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" /></td>
				</tr>
			</table>
		</form>
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
	$production_id=$data[1];
	$update_id=$data[2];

	$item_group_arr=return_library_array( "select id,item_name from lib_item_group",'id','item_name');
	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name" );
	$size_arr=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
	
	$prod_data_arr=array(); //$recipe_prod_id_arr=array(); $product_data_arr=array();
	if($production_id!=0)
	{	
		//echo "select b.id, b.color_size_id , b.physical_qty, b.qcpass_qty, b.operator_name, b.shift_id,po_id as po_id from subcon_embel_production_mst a, subcon_embel_production_dtls b where a.id='$production_id' and a.entry_form=222 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; die;
		$prodData=sql_select("select b.id, b.color_size_id , b.physical_qty, b.qcpass_qty, b.operator_name, b.shift_id,po_id as po_id from subcon_embel_production_mst a, subcon_embel_production_dtls b where a.id='$production_id' and a.entry_form=222 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach($prodData as $row)
		{
			$prod_data_arr[$row[csf('color_size_id')]]['qcpass_qty']=$row[csf('qcpass_qty')];
			$prod_data_arr[$row[csf('color_size_id')]]['physical_qty']=$row[csf('physical_qty')];
			$prod_data_arr[$row[csf('color_size_id')]]['shift_id']=$row[csf('shift_id')];
			$all_po_ids.=$row[csf('po_id')].',';
		}
		unset($prodData);
	}
	//echo $all_po_ids; die;
	$all_subcon_job_arr=array_unique(explode(",",(chop($all_po_ids,','))));

	$con = connect();
	foreach($all_subcon_job_arr as $key=>$row_val)
	{
		//echo $row_val; die;
		$r_id2=execute_query("insert into tmp_job_no (userid, job_id, entry_form) values ($user_id,$row_val,223)");
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
	$subcon_cond=" and b.id in (select job_id from tmp_job_no where userid=$user_id and entry_form=223) ";

	$qc_data_arr=array(); $prev_qc_arr=array(); //$product_data_arr=array();
	$buyer_po_arr=array();
	$order_sql ="select a.job_no_prefix_num,b.id,b.buyer_po_no,b.buyer_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.entry_form='204' $subcon_cond"; 
	//echo $order_sql; die;
	$order_sql_res=sql_select($order_sql);
	foreach ($order_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
	}
	//if($update_id!=0)
	//{	
	$qcData=sql_select("select b.id, b.mst_id, b.color_size_id, b.qcpass_qty, b.physical_qty, b.reje_qty, b.shift_id, b.remarks,defect_qty from subcon_embel_production_mst a, subcon_embel_production_dtls b where a.recipe_id='$production_id' and a.id=b.mst_id and a.entry_form=223 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($qcData as $row)
	{
		if($row[csf('mst_id')]==$update_id)
		{
			$qc_data_arr[$row[csf('color_size_id')]]['qcpass_qty']=$row[csf('qcpass_qty')];
			$qc_data_arr[$row[csf('color_size_id')]]['physical_qty']=$row[csf('physical_qty')];
			$qc_data_arr[$row[csf('color_size_id')]]['reje_qty']=$row[csf('reje_qty')];
			$qc_data_arr[$row[csf('color_size_id')]]['id']=$row[csf('id')];
			$qc_data_arr[$row[csf('color_size_id')]]['shift_id']=$row[csf('shift_id')];
			$qc_data_arr[$row[csf('color_size_id')]]['remarks']=$row[csf('remarks')];
			$qc_data_arr[$row[csf('color_size_id')]]['defect_qty']=$row[csf('defect_qty')];
		}
		else
		{
			$prev_qc_arr[$row[csf('color_size_id')]]['qcpass_qty']+=$row[csf('qcpass_qty')];
			$prev_qc_arr[$row[csf('color_size_id')]]['physical_qty']+=$row[csf('physical_qty')];
			$prev_qc_arr[$row[csf('color_size_id')]]['reje_qty']+=$row[csf('reje_qty')];
			$prev_qc_arr[$row[csf('color_size_id')]]['defect_qty']=$row[csf('defect_qty')];
		}
	}
	unset($qcData);
	//}
	//print_r($prev_qc_arr);

	$sql= "select  a.id, a.job_no_prefix_num, a.embellishment_job, a.party_id, a.within_group, b.id as order_id, b.order_no , b.buyer_po_id , b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.id as color_size_id, c.color_id, c.size_id, sum(c.qnty) as qty, d.id as recipe_id, d.recipe_no_prefix_num, d.recipe_no from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, pro_recipe_entry_mst d, subcon_embel_production_mst e where a.embellishment_job=b.job_no_mst and b.id=c.mst_id and a.embellishment_job=d.job_no and b.job_no_mst=c.job_no_mst and d.id=e.recipe_id and a.embellishment_job=e.job_no
	and b.main_process_id=d.embl_name and b.gmts_item_id=d.gmts_item and b.embl_type=d.embl_type and c.body_part=d.body_part and c.color_id=d.color_id
	and a.entry_form=204 and d.entry_form=220 and e.entry_form=222 and a.status_active=1 and c.status_active=1 and c.is_deleted=0  and a.company_id='$company_id' and e.id='$production_id' $subcon_cond group by a.id, a.job_no_prefix_num, a.embellishment_job, a.insert_date, a.party_id, a.within_group, b.id, b.order_no , b.buyer_po_id , b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.id, c.color_id, c.size_id, d.id, d.recipe_no_prefix_num, d.recipe_no order by d.recipe_no_prefix_num, c.id ASC";
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
		
		$production_qty=0; $qc_qty=0; $rej_qty=0; $prev_qc_qty=0; $prev_rej_qty=0; $physical_qty=0; $remarks='';
		$production_qty=$prod_data_arr[$row[csf('color_size_id')]]['qcpass_qty'];
		$physical_qty=$prod_data_arr[$row[csf('color_size_id')]]['physical_qty'];
		
		$prev_qc_qty=$prev_qc_arr[$row[csf('color_size_id')]]['qcpass_qty'];
		$prev_rej_qty=$prev_qc_arr[$row[csf('color_size_id')]]['reje_qty'];
		$prev_physical_qty=$prev_qc_arr[$row[csf('color_size_id')]]['physical_qty'];
		$prev_defect_qty=$prev_qc_arr[$row[csf('color_size_id')]]['defect_qty'];
		
		if($update_id!=0)
		{
			$upid=$qc_data_arr[$row[csf('color_size_id')]]['id'];
			$qc_qty=$qc_data_arr[$row[csf('color_size_id')]]['qcpass_qty'];
			$rej_qty=$qc_data_arr[$row[csf('color_size_id')]]['reje_qty'];
			$physical_qty=$qc_data_arr[$row[csf('color_size_id')]]['physical_qty'];
			$defect_qty=$qc_data_arr[$row[csf('color_size_id')]]['defect_qty'];
			$remarks=$qc_data_arr[$row[csf('color_size_id')]]['remarks'];
			$bal=$production_qty-($prev_qc_qty);
			$physical_bal=$physical_qty-($prev_physical_qty);
		}
		else 
		{
			$qc_qty='';//$production_qty-($prev_qc_qty);
			$bal=$production_qty-($prev_qc_qty);
			$physical_bal=$physical_qty-($prev_physical_qty);
		}
		
		
		//$shift_id=$prod_data_arr[$row[csf('color_size_id')]]['shift_id'];
		//if($update_id!=0) $qc_qty=$qc_qty; else $qc_qty=$production_qty-($qc_qty);
		
		//if($qc_qty==0) $qc_qty=$production_qty;
		
		
			//echo $row[csf('order_id')];
			$po_id=explode(",",$row[csf('order_id')]);
			if (in_array($row[csf('order_id')], $all_subcon_job_arr))
			{
				$buyer_po='';
				foreach($po_id as $val) 
				{
					if($buyer_po=="") $buyer_po=$buyer_po_arr[$val]['po']; else $buyer_po.=','.$buyer_po_arr[$val]['po'];
				}
				$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				?>
				<tr class="general" name="tr[]" id="tr_<? echo $i;?>">
					<td><input type="text" name="txtSl[]" id="txtSl_<? echo $i;?>" class="text_boxes_numeric" style="width:20px" disabled value="<? echo $i; ?>" /></td>
					<td>
						<input type="text" name="txtRecipeNo[]" id="txtRecipeNo_<? echo $i;?>" class="text_boxes" style="width:90px" placeholder="Display"disabled value="<? echo $row[csf('recipe_no')]; ?>" />
						<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i;?>" style="width:50px" value="<? echo $upid; ?>" />
						<input type="hidden" name="colorSizeId[]" id="colorSizeId_<? echo $i;?>" style="width:50px" value="<? echo $row[csf('color_size_id')]; ?>" />
						<input type="hidden" name="dtlsPoId[]" id="dtlsPoIdId_<? echo $i;?>" style="width:50px" value="<? echo $row[csf('order_id')]; ?>" />
					</td>
					<td>
					<input type="text" name="txtBuyerPO[]" id="txtBuyerPO_<? echo $i;?>" class="text_boxes" style="width:100px" placeholder="Display"disabled value="<? echo $buyer_po; ?>" />
					<input type="hidden" name="txtBuyerPOId[]" id="txtBuyerPOId_<? echo $i;?>" style="width:50px" value="<? echo $row[csf('buyer_po_id')]; ?>" />
				</td>
					<td>
						<input type="text" name="txtGmtsItem[]" id="txtGmtsItem_<? echo $i;?>" class="text_boxes" style="width:80px" placeholder="Display"disabled value="<? echo $garments_item[$row[csf('gmts_item_id')]]; ?>" />
						<input type="hidden" name="txtGmtsItemId[]" id="txtGmtsItemId_<? echo $i;?>" style="width:50px" value="<? echo $row[csf('gmts_item_id')]; ?>" />
					</td>
					<td>	
						<input type="text" name="txtBodyPart[]" id="txtBodyPart_<? echo $i;?>" class="text_boxes" style="width:90px" placeholder="Display" disabled value="<? echo $body_part[$row[csf('body_part')]]; ?>" />
						<input type="hidden" name="txtBodyPartId[]" id="txtBodyPartId_<? echo $i;?>" style="width:50px" class="text_boxes" value="<? echo $row[csf('body_part')]; ?>" />
					</td>
					<td>
						<input type="text" name="txtEmblName[]" d="txtEmblName_<? echo $i;?>" class="text_boxes" style="width:90px" placeholder="Display" disabled value="<? echo $emblishment_name_array[$row[csf('main_process_id')]]; ?>" />
						<input type="hidden" name="txtEmblNameId[]" id="txtEmblNameId_<? echo $i;?>" value="<? echo $row[csf('main_process_id')]; ?>" />
					</td>
					<td>
						<input type="text" name="txtEmblType[]" id="txtEmblType_<? echo $i;?>" class="text_boxes" style="width:90px" placeholder="Display" disabled value="<? echo $new_subprocess_array[$row[csf('embl_type')]]; ?>"/>
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
					<td>
						<input type="text" name="txtProdQty[]" id="txtProdQty_<? echo $i;?>" class="text_boxes_numeric" style="width:55px" placeholder="Dispaly" value="<? echo $production_qty; ?>" disabled />
					</td>
					 <td>
		                <input type="text" name="txtPhysicalQty[]" id="txtPhysicalQty_<? echo $i;?>" class="text_boxes_numeric" style="width:55px" placeholder="Write" value="<? echo $physical_qty; ?>" onChange="fnc_calculate_qcqty(<? echo $i; ?>);fnc_total_calculate();" /><!--onBlur="fnc_calculate_qcqty(<? //echo $i;?>);"-->
		            </td>
		            <td>
		                <input type="text" name="txtRejQty[]" id="txtRejQty_<? echo $i;?>" class="text_boxes_numeric" style="width:55px" placeholder="Write" value="<? echo $rej_qty; ?>" onChange="fnc_calculate_qcqty(<? echo $i; ?>);fnc_total_calculate();"  onClick="openmypage_reject_qty(1,'<? echo $row[csf('order_id')]; ?>',<? echo $i; ?>)"  readonly/><!--onBlur="fnc_calculate_qcqty(<? //echo $i;?>);"-->
		                <input type="hidden" name="hdnDtlsdata[]" id="hdnDtlsdata_<? echo $i; ?>" value="<? echo $defect_qty; ?>">
		            </td>
		            <td>
		                <input type="text" name="txtQcQty[]" id="txtQcQty_<? echo $i;?>" class="text_boxes_numeric" style="width:50px" value="<? echo $qc_qty; ?>" placeholder="<? echo $prev_qc_qty; ?>" onChange="fnc_calculate_qcqty(<? echo $i; ?>); fnc_total_calculate();"/>
		            </td>
		            <td><input type="text" name="txtremarks[]" id="txtremarks_<? echo $i; ?>" style="width:40px" value="<? echo $remarks; ?>" class="text_boxes" placeholder="Remark" onClick="openmypage_remarks(<? echo $i; ?>);" /></td>
				</tr>
				<?
				$i++;
			}
		
	}
	$r_id3=execute_query("delete from tmp_job_no where userid=$user_id and entry_form=223");
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

/*/Search Saved data/*/
if($action=="embel_qc_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	//echo $cbo_company_id;
	?>
	  <script>
		function js_set_value( str) 
		{
			$('#hidden_qc_data').val( str );
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
			else if(val==6) $('#search_by_td').html('IR/IB');
		}
	  </script>
    </head>
    <body>
		<div align="center" style="width:100%;" >
             <fieldset>
                <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
                    <table width="" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                        <thead> 
                        	<tr>
                                <th colspan="8"><? echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --"); ?></th>
                            </tr>
                            <tr>              	 
                                <th width="150">Location</th>
                                <th width="100">Production ID</th>
                                <th width="100">QC ID</th>
                                <th width="100">Search By</th>
                            	<th width="100" id="search_by_td">Embl. Job No</th>
                                <th width="160" colspan="2">QC Date Range</th>
                                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:90px;" /></th> 
                            </tr>          
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td><? echo create_drop_down( "cbo_location", 150, "select id, location_name from lib_location where company_id='$cbo_company_id' and status_active =1 and is_deleted=0 order by location_name ASC","id,location_name", 1, "-Select Location-", 0, "","","","","","",3 ); ?></td>
                                <td><input name="txt_prod_no" id="txt_prod_no" class="text_boxes" style="width:90px"></td>
                                <td><input name="txt_qc_no" id="txt_qc_no" class="text_boxes" style="width:90px"></td>
                                <td>
									<?
                                        $search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style",6=>"IR/IB");
                                        echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                                    ?>
                                </td>
                                <td>
                                    <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:90px" placeholder="" />
                                </td>
                                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" placeholder="From Date" style="width:70px"></td>
                                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" style="width:70px"></td> 
                                <td align="center">
                                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( '<? echo $cbo_company_id; ?>'+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('txt_prod_no').value+'_'+document.getElementById('txt_qc_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_year_selection').value, 'create_qc_no_list_view', 'search_div', 'embl_qc_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:90px;" />
                                    
                                     <input type="hidden" id="hidden_qc_data" name="hidden_qc_data" value="" />
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

if($action=="create_qc_no_list_view")
{
	$data=explode('_',$data);
	$company_id=$data[0];
	$location_id=$data[1];
	$prod_no=$data[2];
	$qc_no=$data[3];
	$date_from=$data[4];
	$date_to=$data[5];
	$search_type=$data[6];
	$search_by = $data[7];
	$search_str = trim($data[8]);
	$year = trim($data[9]);
	
	
	if($db_type==0)
	{ 
		$year_cond=" and YEAR(a.insert_date)=$year";
	}
	else
	{
		$year_cond=" and to_char(a.insert_date,'YYYY')=$year";
	}
	
	if($db_type==0)
	{
		$date_from= change_date_format($date_from,'yyyy-mm-dd');
		$date_to= change_date_format($date_to,'yyyy-mm-dd');
	}
	else
	{
		$date_from= change_date_format($date_from, "", "",1) ;
		$date_to= change_date_format($date_to, "", "",1);
	}
	
	
	
	
	
	if($company_id==0) { echo "Select Company first"; die; }
	
	if($location_id !="0") $location_cond= "and a.location_id = $location_id"; else $location_cond= "";
	//if($prod_no!="") $system_no_cond=" and a.sys_no like '%".trim($prod_no)."%'"; else $system_no_cond="";
	
	if($prod_no!="") $system_no_cond=" and a.prefix_no_num='".trim($prod_no)."'"; else $system_no_cond="";
	
	
	
	 
	
	$production_ids='';
 	if($db_type==0) $id_cond="group_concat(b.po_id)";
	else if($db_type==2) $id_cond="listagg(b.po_id,',') within group (order by b.po_id)";
	//echo "select $id_cond as id from subcon_embel_production_mst a, subcon_embel_production_dtls b where a.id=b.mst_id and a.entry_form=222 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' $location_cond  $po_idsCond $spo_idsCond  $system_no_cond"; die;
 	if(($prod_no!=""))
	{
		$production_ids = return_field_value("$id_cond as po_id", "subcon_embel_production_mst a, subcon_embel_production_dtls b", "a.id=b.mst_id and a.entry_form=222 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' $location_cond  $po_idsCond $spo_idsCond  $system_no_cond", "po_id");
	}
	
	//echo $production_ids; die;
 	if ($production_ids!="") $production_idsCond=" and b.po_id in ($production_ids)"; else $production_idsCond="";
	
	
	
	
	
	if($qc_no!="") $qc_no_cond=" and a.prefix_no_num='".trim($qc_no)."'"; else $qc_no_cond="";
	//echo "sdlkjklsdj";
	//if($data[4]!="" && $data[5]!="") $date_cond=" and product_date between '".$date_from."' and '".$date_to."'"; else $date_cond="";
	
	$order_arr=array(); $colorid_arr=array();
	/*$order_sql = sql_select("select a.embellishment_job, a.within_group, a.party_id, b.id, a.order_no, c.id as color_zise_id, c.color_id as color_id, c.qnty as qty from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.embellishment_job=b.job_no_mst and b.id=c.mst_id and a.entry_form=204 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ");
	foreach($order_sql as $row)
	{
		$order_arr[$row[csf('id')]]['po']=$row[csf('order_no')];
		$order_arr[$row[csf('id')]]['within_group']=$row[csf('within_group')];
		$order_arr[$row[csf('id')]]['party_id']=$row[csf('party_id')];
		$order_arr[$row[csf('id')]]['qty']+=$row[csf('qty')];
		$colorid_arr[$row[csf('color_zise_id')]]['color_id']=$row[csf('color_id')];
	}
	unset($order_sql);*/
	
	$prodno_arr = return_library_array("select id, sys_no from subcon_embel_production_mst where entry_form=222", 'id', 'sys_no');
	$prodData=sql_select("select id, mst_id, color_size_id, production_date, operator_name, shift_id from subcon_embel_production_dtls where status_active=1 and is_deleted=0");
	foreach($prodData as $row)
	{
		$prod_data_arr[$row[csf('mst_id')]]['production_date']=change_date_format($row[csf('production_date')]);
		$prod_data_arr[$row[csf('mst_id')]]['operator_name']=$row[csf('operator_name')];
		$prod_data_arr[$row[csf('mst_id')]]['shift_id']=$row[csf('shift_id')];
	}
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";$int_ref_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and d.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $po_cond=" and c.po_number = '$search_str' ";
			else if ($search_by==5) $style_cond=" and d.style_ref_no = '$search_str' ";

			if ($search_by==3) $job_cond2=" and b.job_no_mst = '$search_str' ";
			else if ($search_by==4) $po_cond2=" b.and buyer_po_no = '$search_str' ";
			else if ($search_by==5) $style_cond2=" and b.buyer_style_ref = '$search_str' ";
			else if ($search_by==6) $int_ref_cond.=" and e.grouping = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and d.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) $po_cond=" and c.po_number like '%$search_str%'"; 
			else if ($search_by==5) $style_cond=" and d.style_ref_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond2=" and b.job_no_mst like '%$search_str%'";  
			else if ($search_by==4) $po_cond2=" and b.buyer_po_no like '%$search_str%'"; 
			else if ($search_by==5) $style_cond2=" and b.buyer_style_ref like '%$search_str%'";
			else if ($search_by==6) $int_ref_cond.=" and e.grouping like '%$search_str%'";
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and d.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $po_cond=" and c.po_number like '$search_str%'"; 
			else if ($search_by==5) $style_cond=" and d.style_ref_no like '$search_str%'"; 
			
			if ($search_by==3) $job_cond2=" and b.job_no_mst like '$search_str%'";  
			else if ($search_by==4) $po_cond2=" and b.buyer_po_nolike '$search_str%'";
			else if ($search_by==5) $style_cond2=" and b.buyer_style_ref like '$search_str%'";
			else if ($search_by==6) $int_ref_cond.=" and e.grouping like '$search_str%'";
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and d.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $po_cond=" and c.po_number like '%$search_str'";  
			else if ($search_by==5) $style_cond=" and d.style_ref_no like '%$search_str'";  

			if ($search_by==3) $job_con2=" and b.job_no_mst like '%$search_str'";  
			else if ($search_by==4) $po_cond2=" and b.buyer_po_no like '%$search_str'";
			else if ($search_by==5) $style_cond2=" and b.buyer_style_ref like '%$search_str'"; 
			else if ($search_by==6) $int_ref_cond.=" and e.grouping like '%$search_str'"; 
		}
	}
	
	if($db_type==0)
	{ 
		if ($date_from!="" &&  $date_to!="") $production_date = "and b.production_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; else $production_date ="";
	}
	else
	{
		if ($date_from!="" &&  $date_to!="") $production_date = "and b.production_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'"; else $production_date ="";
	}
	
	
	$po_ids='';
	
	/*if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4)|| ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	
	if ($po_ids!="") $po_idsCond=" and a.buyer_po_id in ($po_ids)"; else $po_idsCond="";*/
	
	/*$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);*/

	$spo_ids='';
	
	if(($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2) || ($job_cond!="" && $search_by==3 ) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		if($db_type==0){ $id_conds="group_concat(b.id)"; }
		else if($db_type==2){ $id_conds="listagg(b.id,',') within group (order by b.id)"; }
		// $spo_ids = return_field_value("$id_conds as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.id=b.mst_id $search_com_cond and a.status_active =1 and a.is_deleted=0", "id");
		$spo_ids_sql = sql_select("SELECT $id_conds as ID,a.within_group as WITHIN_GROUP from subcon_ord_mst a, subcon_ord_dtls b, wo_po_break_down c, wo_po_details_master d where a.id=b.mst_id and b.buyer_po_id=c.id and c.job_id=d.id $search_com_cond $job_cond $style_cond $po_cond and a.status_active =1 and a.is_deleted=0 and a.within_group=1 group by a.within_group
		union all
		SELECT $id_conds as id,a.within_group as WITHIN_GROUP from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id $search_com_cond $job_cond2 $style_cond2 $po_cond2 and a.status_active =1 and a.is_deleted=0 and a.within_group=2 group by a.within_group ");

		foreach($spo_ids_sql as $row)
		{
			if($spo_ids!='' ){ $spo_ids.=','.$row['ID'];}else{ $spo_ids=$row['ID'];;}
		}

	}


	// if ( $spo_ids!="") $spo_idsCond=" and a.order_id in ($spo_ids)"; else $spo_idsCond="";
	if ( $spo_ids!="") $spo_idsCond=" and b.po_id in ($spo_ids)"; else $spo_idsCond="";

	

	//------------
	
	
	$buyer_po_arr=array();
	$order_sql ="select a.job_no_prefix_num, a.within_group,a.party_id,b.id,b.buyer_po_no,b.buyer_style_ref, b.job_no_mst,b.order_no,b.buyer_buyer,b.main_process_id,b.gmts_item_id, b.embl_type, b.body_part,c.qnty,c.color_id,c.id as color_zise_id  from subcon_ord_mst a, subcon_ord_dtls b , subcon_ord_breakdown c where a.id=b.mst_id and a.embellishment_job=c.job_no_mst and b.id=c.mst_id and a.entry_form='204' $search_com_cond $order_rcv_date";
	//$search_com_cond
	// echo $order_sql; die;
	$order_sql_res=sql_select($order_sql); $all_subcon_job='';
	foreach ($order_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no_prefix_num")];
		$buyer_po_arr[$row[csf("id")]]['order_no']=$row[csf("order_no")];
		$buyer_po_arr[$row[csf("id")]]['buyer_buyer']=$row[csf("buyer_buyer")];
		$buyer_po_arr[$row[csf("id")]]['within_group']=$row[csf("within_group")];
		$buyer_po_arr[$row[csf("id")]]['qty'] +=$row[csf("qnty")];
		$buyer_po_arr[$row[csf("id")]]['party_id']=$row[csf("party_id")];
		$buyer_po_arr[$row[csf("id")]]['main_process_id']=$row[csf("main_process_id")];
		$buyer_po_arr[$row[csf("id")]]['gmts_item_id']=$row[csf("gmts_item_id")];
		$buyer_po_arr[$row[csf("id")]]['embl_type']=$row[csf("embl_type")];
		$buyer_po_arr[$row[csf("id")]]['body_part']=$row[csf("body_part")];
		$buyer_po_arr[$row[csf("id")]]['color_id']=$row[csf("color_id")];
		$colorid_arr[$row[csf('color_zise_id')]]['color_id']=$row[csf('color_id')];

		//$po_id_arr[$row[csf("id")]]=$row[csf("id")];
		$all_subcon_job .="'".$row[csf("job_no_mst")]."'".',';
	}

	unset($po_sql_res);
	$color_name_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	?>
	<body>
		<div align="center">
			<fieldset style="width:820px;margin-left:10px">
				<form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table" >
						<thead>
							<th width="30">SL</th>
							<th width="60">QC No</th>
                            <th width="110">Production No</th>
                            <th width="110">Job NO</th>
                            <th width="100">Order</th>
                            <th width="110">Color</th>
                            <th width="100">Buyer Po</th>
                            <th width="100">Buyer Style</th>
                            <th>QC Qty</th>
						</thead>
					</table>
					<div style="width:820px; overflow-y:scroll; max-height:300px;">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_list_search" >
							<?
							if($db_type==0) $color_size_id_str="group_concat(b.color_size_id)";
							else if($db_type==2) $color_size_id_str="listagg(b.color_size_id,',') within group (order by b.color_size_id)";
							if($int_ref_cond!=""){
							   $sql="SELECT a.id, a.prefix_no_num, a.sys_no, a.location_id, a.recipe_id, a.job_no, a.order_ids as order_id, a.buyer_po_ids as buyer_po_id,a.floor_id, $color_size_id_str as color_size_id, sum(b.qcpass_qty) as qc_qty from subcon_embel_production_mst a, subcon_embel_production_dtls b,sub_material_mst  c, sub_material_dtls d,  wo_po_break_down e where  a.id = b.mst_id
							   and b.job_no=c.embl_job_no and c.id=d.mst_id and d.buyer_po_id=e.id and a.entry_form=223 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' $location_cond  $production_date $qc_no_cond $po_idsCond $spo_idsCond $year_cond $production_idsCond $int_ref_cond group by a.id, a.prefix_no_num, a.sys_no, a.floor_id,a.location_id, a.recipe_id, a.job_no, a.order_ids, a.buyer_po_ids order by a.id DESC";// $date_cond
							}else{
								$sql="SELECT a.id, a.prefix_no_num, a.sys_no, a.location_id, a.recipe_id, a.job_no, a.order_ids as order_id, a.buyer_po_ids as buyer_po_id,a.floor_id, $color_size_id_str as color_size_id, sum(b.qcpass_qty) as qc_qty from subcon_embel_production_mst a, subcon_embel_production_dtls b where a.id=b.mst_id and a.entry_form=223 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' $location_cond  $production_date $qc_no_cond $po_idsCond $spo_idsCond $year_cond $production_idsCond group by a.id, a.prefix_no_num, a.sys_no, a.floor_id,a.location_id, a.recipe_id, a.job_no, a.order_ids, a.buyer_po_ids order by a.id DESC";// $date_cond
							}
							//  echo $sql; die;
							$sql_res=sql_select($sql);

							$i=1; 
							foreach($sql_res as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$po_id=explode(",",$row[csf('order_id')]);
								$buyer_po=$buyer_style=$job_no=$order_no=$buyerBuyer=$party_id=$gmts_item_id=$body_part_id=$embl_type=$color_id=$main_process_id='';
								foreach($po_id as $val) 
								{
									//echo $val;
									if($buyer_po=="") $buyer_po=$buyer_po_arr[$val]['po']; else $buyer_po.=','.$buyer_po_arr[$val]['po'];
									if($buyer_style=="") $buyer_style=$buyer_po_arr[$val]['style']; else $buyer_style.=','.$buyer_po_arr[$val]['style'];
									if($job_no=="") $job_no=$buyer_po_arr[$val]['job']; else $job_no.=','.$buyer_po_arr[$val]['job'];
									if($order_no=="") $order_no=$buyer_po_arr[$val]['order_no']; else $order_no.=','.$buyer_po_arr[$val]['order_no'];
									if($party_id=="") $party_id=$buyer_po_arr[$val]['party_id']; else $party_id.=','.$buyer_po_arr[$val]['party_id'];
									if($gmts_item_id=="") $gmts_item_id=$buyer_po_arr[$val]['gmts_item_id']; else $gmts_item_id.=','.$buyer_po_arr[$val]['gmts_item_id'];
									if($body_part_id=="") $body_part_id=$buyer_po_arr[$val]['body_part']; else $body_part_id.=','.$buyer_po_arr[$val]['body_part'];
									if($embl_type=="") $embl_type=$buyer_po_arr[$val]['embl_type']; else $embl_type.=','.$buyer_po_arr[$val]['embl_type'];
									if($color_id=="") $color_id=$buyer_po_arr[$val]['color_id']; else $color_id.=','.$buyer_po_arr[$val]['color_id'];


									if($main_process_id=="") $main_process_id=$buyer_po_arr[$val]['main_process_id']; else $main_process_id.=','.$buyer_po_arr[$val]['main_process_id'];
									//if($buyer_buyer=="") $buyer_buyer=$buyer_po_arr[$val]['buyer_buyer']; else $buyer_buyer.=','.$buyer_po_arr[$val]['buyer_buyer'];
									$within_group=$buyer_po_arr[$val]['within_group'];
									if ($within_group==1) {
										if($buyer_buyer=="") $buyer_buyer=$buyer_library[$buyer_po_arr[$val]['buyer_buyer']]; else $buyer_buyer.=','.$buyer_library[$buyer_po_arr[$val]['buyer_buyer']];
							        }else{
							           if($buyer_buyer=="") $buyer_buyer=$buyer_po_arr[$val]['buyer_buyer']; else $buyer_buyer.=','.$buyer_po_arr[$val]['buyer_buyer'];
							        }
									if($within_group=="") $within_group=$buyer_po_arr[$val]['within_group']; else $within_group.=','.$buyer_po_arr[$val]['within_group'];
									$qty +=$buyer_po_arr[$val]['qty'];
								}
								$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
								$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
								$job_no=implode(",",array_unique(explode(",",$job_no)));
								$order_no=implode(",",array_unique(explode(",",$order_no)));
								$buyer_buyer=implode(",",array_unique(explode(",",$buyer_buyer)));
								$party_id=implode(",",array_unique(explode(",",$party_id)));
								$gmts_item_id=implode(",",array_unique(explode(",",$gmts_item_id)));
								$body_part_id=implode(",",array_unique(explode(",",$body_part_id)));
								$embl_type=implode(",",array_unique(explode(",",$embl_type)));
								$color_id=implode(",",array_unique(explode(",",$color_id)));
								$within_group=implode(",",array_unique(explode(",",$within_group)));
								/*$main_process_id=implode(",",array_unique(explode(",",$main_process_id)));
								if($main_process_id==1) $new_subprocess_array= $emblishment_print_type;
								else if($main_process_id==2) $new_subprocess_array= $emblishment_embroy_type;
								else if($main_process_id==3) $new_subprocess_array= $emblishment_wash_type;
								else if($main_process_id==4) $new_subprocess_array= $emblishment_spwork_type;
								else if($main_process_id==5) $new_subprocess_array= $emblishment_gmts_type;
								else $new_subprocess_array=$blank_array;
								$prod_qty=0; $balance_qty=0;
								$prod_qty=$production_qty_arr[$row[csf('id')]];
								$balance_qty=$qty-$prod_qty;*/


								$str_data="";
								
								$str_data=$row[csf('id')].'***'.$row[csf('sys_no')].'***'.$row[csf('location_id')].'***'.$row[csf('recipe_id')].'***'.$prodno_arr[$row[csf('recipe_id')]].'***'.$row[csf('job_no')].'***'.$row[csf('order_id')].'***'.$order_arr[$row[csf('order_id')]]['po'].'***'.$within_group.'***'.$party_id.'***'.$qty.'***'.$prod_data_arr[$row[csf('id')]]['production_date'].'***'.$prod_data_arr[$row[csf('id')]]['operator_name'].'***'.$row[csf('buyer_po_id')].'***'.$buyer_po.'***'.$buyer_style.'***'.$prod_data_arr[$row[csf('id')]]['shift_id'].'***'.$row[csf('floor_id')];
								
								$colorsize_id_ex=explode(",",$row[csf('color_size_id')]);
								$color_name_str="";
								foreach($colorsize_id_ex as $breakdown_id)
								{
									if(	$color_name_str=="") $color_name_str=$color_name_arr[$colorid_arr[$breakdown_id]['color_id']]; else $color_name_str.=','.$color_name_arr[$colorid_arr[$breakdown_id]['color_id']];
								}
								
								$color_name_str=implode(",",array_filter(array_unique(explode(",",$color_name_str))));
								
								//echo $color_name_str;
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $str_data; ?>')"> 
									<td width="30" align="center"><?php echo $i; ?></td>	
									<td width="60" align="center"><?php echo $row[csf('prefix_no_num')]; ?></td>
                                    <td width="110" style="word-break:break-all"><?php echo $prodno_arr[$row[csf('recipe_id')]]; ?></td>
                                    <td width="110" style="word-break:break-all"><?php echo $row[csf('job_no')]; ?></td>
                                    <td width="100" style="word-break:break-all"><?php echo $order_no; ?></td>
                                    
                                    <td width="110" style="word-break:break-all"><?php echo $color_name_str; ?></td>
                                    <td width="100" style="word-break:break-all"><?php echo $buyer_po; ?></td>
                                    <td width="100" style="word-break:break-all"><?php echo $buyer_style; ?></td>
                                    <td align="right"><?php echo $row[csf('qc_qty')]; ?></td>
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
		
		$new_return_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'PQC', date("Y",time()), 5, "select id,prefix_no,prefix_no_num from  subcon_embel_production_mst where company_id=$cbo_company_id and entry_form=223 $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
		
		$id=return_next_id( "id", "subcon_embel_production_mst", 1 ) ; 
		$field_array="id, prefix_no, prefix_no_num, sys_no, company_id, location_id, recipe_id, job_no, order_ids, buyer_po_ids,floor_id, inserted_by, insert_date, status_active, is_deleted, entry_form";
		$data_array="(".$id.",'".$new_return_no[1]."','".$new_return_no[2]."','".$new_return_no[0]."',".$cbo_company_id.",".$cbo_location.",".$txt_production_id.",".$txt_job_no.",".$txt_order_id.",".$txtbuyerPoId.",".$cbo_floor_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,223)"; 
		
		$production_no=$new_return_no[0];
		
		$data_array_dtls=""; $issave=1;
		$id_dtls=return_next_id( "id", "subcon_embel_production_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, color_size_id, po_id, job_no, production_date, reje_qty, qcpass_qty, physical_qty, operator_name, shift_id, remarks, defect_qty,fabric_reject_qty, print_reject_qty, part_short_qty, inserted_by, insert_date, status_active, is_deleted";
		//echo "10**"; //die; 
		for($i=1;$i<=$total_row;$i++)
		{
			$colorSizeId="colorSizeId_".$i; 
			$dtlsPoId="dtlsPoId_".$i;
			$txtRejQty="txtRejQty_".$i;
			$txtQcQty="txtQcQty_".$i;
			$txtPhysicalQty="txtPhysicalQty_".$i;
			//$cboShift="cboShift_".$i;
			$txtremarks="txtremarks_".$i;
			$updateIdDtls="updateIdDtls_".$i;
			$hdnDtlsdata= "hdnDtlsdata_".$i;
			$exdata=explode("_",$$hdnDtlsdata);
			$FabricReject=str_replace(",",'',$exdata[0]);
			$PrintReject=str_replace(",",'',$exdata[1]);
			$PartShort=str_replace(",",'',$exdata[2]);
			
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$id_dtls.",".$id.",'".$$colorSizeId."',".$$dtlsPoId.",".$txt_job_no.",".$txt_prod_date.",'".$$txtRejQty."','".$$txtQcQty."','".$$txtPhysicalQty."',".$txt_super_visor.",".$cboShift.",'".$$txtremarks."','".$$hdnDtlsdata."','".$FabricReject."','".$PrintReject."','".$PartShort."','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."',1,0)"; 
			$id_dtls=$id_dtls+1;
		}
		//die;
		//echo "10**insert into subcon_embel_production_mst ($field_array) values $data_array "; die;
		//echo "10**insert into subcon_embel_production_dtls ($field_array_dtls) values $data_array_dtls "; die;
		$flag=1;
		$rID=sql_insert("subcon_embel_production_mst",$field_array,$data_array,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		$rID1=sql_insert("subcon_embel_production_dtls",$field_array_dtls,$data_array_dtls,0);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		
		//echo "10**".$rID."**".$rID1; die;
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
		
		$field_array_update="location_id*recipe_id*job_no*order_ids*buyer_po_ids*floor_id*updated_by*update_date";
		$data_array_update="".$cbo_location."*".$txt_production_id."*".$txt_job_no."*".$txt_order_id."*".$txtbuyerPoId."*".$cbo_floor_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 		
		
		$data_array_dtls_update=array();
		$field_array_dtls_update="color_size_id*po_id*job_no*production_date*reje_qty*qcpass_qty*physical_qty*operator_name*shift_id*remarks*defect_qty*fabric_reject_qty*print_reject_qty* part_short_qty*updated_by*update_date";
		
		$production_no=str_replace("'","",$txt_qc_id);
		
		$data_array_dtls="";
		$id_dtls=return_next_id( "id", "subcon_embel_production_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, color_size_id, po_id, job_no, production_date, reje_qty, qcpass_qty,physical_qty, operator_name, shift_id, remarks, defect_qty,fabric_reject_qty, print_reject_qty, part_short_qty,inserted_by, insert_date, status_active, is_deleted";
		$issave=1;
		for($i=1;$i<=$total_row;$i++)
		{
			$colorSizeId="colorSizeId_".$i; 
			$dtlsPoId="dtlsPoId_".$i; 
			$txtRejQty="txtRejQty_".$i;
			$txtQcQty="txtQcQty_".$i;
			$txtPhysicalQty="txtPhysicalQty_".$i;
			//$cboShift="cboShift_".$i;
			$txtremarks="txtremarks_".$i;
			$updateIdDtls="updateIdDtls_".$i;
			$hdnDtlsdata= "hdnDtlsdata_".$i;
			$exdata=explode("_",$$hdnDtlsdata);
			$FabricReject=str_replace(",",'',$exdata[0]);
			$PrintReject=str_replace(",",'',$exdata[1]);
			$PartShort=str_replace(",",'',$exdata[2]);
			
			$updateIds = str_replace("'","",$$updateIdDtls);
			
			if( $updateIds != "")
			{
				$updateIdDtls_array[]=$updateIds;
				//if($data_array_dtls_update != "") $data_array_dtls_update .= ","; 	
				$data_array_dtls_update[$updateIds] = explode("*",("'".$$colorSizeId."'*".$$dtlsPoId."*".$txt_job_no."*".$txt_prod_date."*'".$$txtRejQty."'*'".$$txtQcQty."'*'".$$txtPhysicalQty."'*".$txt_super_visor."*".$cboShift."*'".$$txtremarks."'*'".$$hdnDtlsdata."'*'".$FabricReject."'*'".$PrintReject."'*'".$PartShort."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
			}else
			{
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$id_dtls.",".$update_id.",'".$$colorSizeId."',".$$dtlsPoId.",".$txt_job_no.",".$txt_prod_date.",'".$$txtRejQty."','".$$txtQcQty."','".$$txtPhysicalQty."',".$txt_super_visor.",".$cboShift.",'".$$txtremarks."','".$$hdnDtlsdata."','".$FabricReject."','".$PrintReject."','".$PartShort."','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."',1,0)"; 
					
				$id_dtls=$id_dtls+1;
			}
		}
		//echo "10**".bulk_update_sql_statement("subcon_embel_production_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $updateIdDtls_array); die;
		//echo "10**insert into subcon_embel_production_dtls ($field_array_dtls) values $data_array_dtls "; die;
		
		$flag=1;
		$rID=sql_update("subcon_embel_production_mst",$field_array_update,$data_array_update,"id",$update_id,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		if($data_array_dtls !=""){
			$rID1=sql_insert("subcon_embel_production_dtls",$field_array_dtls,$data_array_dtls,0);
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		if($data_array_dtls_update !="")
		{
			$rID2=execute_query(bulk_update_sql_statement( "subcon_embel_production_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $updateIdDtls_array ));
			if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		//echo "10**".$rID."**".$rID1."**".$rID2;die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_id)."**".$production_no;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$update_id)."**".$production_no;
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$update_id)."**".$production_no;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id)."**".$production_no;
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

if($action=="embl_production_entry_print")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$size_arr=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$recipe_arr = return_library_array("select id, recipe_no from pro_recipe_entry_mst", 'id', 'recipe_no');
	
	$order_arr=array();
	$order_sql = sql_select("select a.embellishment_job, a.order_no, a.within_group, a.party_id, b.main_process_id, sum(c.qnty) as qty from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.embellishment_job=b.job_no_mst and b.id=c.mst_id and a.entry_form=204 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0  group by a.embellishment_job, a.order_no, a.within_group, a.party_id, b.main_process_id");
	foreach($order_sql as $row)
	{
		$order_arr[$row[csf('embellishment_job')]]['po']=$row[csf('order_no')];
		$order_arr[$row[csf('embellishment_job')]]['within_group']=$row[csf('within_group')];
		$order_arr[$row[csf('embellishment_job')]]['party_id']=$row[csf('party_id')];
		$order_arr[$row[csf('embellishment_job')]]['qty']=$row[csf('qty')];
		$order_arr[$row[csf('embellishment_job')]]['embl_name']=$row[csf('main_process_id')];
	}
	unset($order_sql);
	
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
	$prodData=sql_select("select id, mst_id, color_size_id, production_date, $pdate_cond as production_hour, operator_name from subcon_embel_production_dtls where status_active=1 and is_deleted=0 and mst_id='$data[1]'");
	foreach($prodData as $row)
	{
		$prod_data_arr[$row[csf('mst_id')]]['production_date']=change_date_format($row[csf('production_date')]);
		$prod_data_arr[$row[csf('mst_id')]]['production_hour']=$row[csf('production_hour')];
		$prod_data_arr[$row[csf('mst_id')]]['operator_name']=$row[csf('operator_name')];
	}
	unset($prodData);
	
	$prodData=sql_select("select id, color_size_id, production_date, production_hour, qcpass_qty, operator_name, shift_id from subcon_embel_production_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0");
	foreach($prodData as $row)
	{
		$prod_data_arr[$row[csf('color_size_id')]]['id']=$row[csf('id')];
		$prod_data_arr[$row[csf('color_size_id')]]['qcpass_qty']=$row[csf('qcpass_qty')];
		$prod_data_arr[$row[csf('color_size_id')]]['shift_id']=$row[csf('shift_id')];
	}
	
	unset($prodData);
	
	$sql_mst = "select id, prefix_no_num, sys_no, location_id, recipe_id, job_no, order_ids as order_id from subcon_embel_production_mst where entry_form=222 and id='$data[1]'";
	$dataArray = sql_select($sql_mst); $party_name="";
	if(  $order_arr[$dataArray[0][csf('job_no')]]['within_group'] ==1) $party_name=$company_library[$order_arr[$dataArray[0][csf('job_no')]]['party_id']];
	else if($order_arr[$dataArray[0][csf('job_no')]]['within_group']==2) $party_name=$buyer_library[$order_arr[$dataArray[0][csf('job_no')]]['party_id']];
	
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
                <td><? echo $order_arr[$dataArray[0][csf('job_no')]]['po']; ?></td>
                <td><strong>Embel. Name:</strong></td>
                <td><? echo $emblishment_name_array[$order_arr[$dataArray[0][csf('job_no')]]['embl_name']]; ?></td>
            </tr>
            <tr>
                <td><strong>Recipe No:</strong></td>
                <td><? echo $recipe_arr[$dataArray[0][csf('recipe_id')]]; ?></td>
                <td><strong>Remarks:</strong></td>
                <td><? //echo $recipe_for[$dataArray[0][csf('recipe_for')]]; ?></td>
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
                    <th width="90">Operator/Superviser</th>
                    <th>Shift</th>
                </thead>
				<?
				
				$mst_id = $data[1];
				$com_id = $data[0];
				$job_no = $dataArray[0][csf('job_no')];
				$recipe_id = $dataArray[0][csf('recipe_id')];
				

				$sql= "select  a.id, a.embellishment_job, b.main_process_id, c.id as color_size_id, c.item_id, c.embellishment_type, c.body_part, c.color_id, c.size_id, sum(c.qnty) as qty from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, pro_recipe_entry_mst d where a.embellishment_job=b.job_no_mst and b.id=c.mst_id and a.embellishment_job=d.job_no and a.order_id=d.po_id 
							
				and b.main_process_id=d.embl_name and c.item_id=d.gmts_item and c.embellishment_type=d.embl_type and c.body_part=d.body_part and c.color_id=d.color_id
				and a.entry_form=204 and d.entry_form=220 and a.status_active=1 and c.status_active=1 and c.is_deleted=0  and a.company_id='$com_id' and d.id='$recipe_id' group by a.id, a.embellishment_job, b.main_process_id, c.id, c.item_id, c.embellishment_type, c.body_part, c.color_id, c.size_id, d.recipe_no_prefix_num order by d.recipe_no_prefix_num DESC";
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
                        <td><? echo $garments_item[$row[csf('item_id')]]; ?></td>
                        <td><? echo $body_part[$row[csf('body_part')]]; ?>&nbsp;</td>
                        <td><? echo $new_subprocess_array[$row[csf('embellishment_type')]]; ?>&nbsp;</td>
                        <td><? echo $color_arr[$row[csf('color_id')]]; ?>&nbsp;</td>
                        <td align="center"><? echo $size_arr[$row[csf('size_id')]]; ?>&nbsp;</td>
                        <td align="right"><? echo number_format($prod_data_arr[$row[csf('color_size_id')]]['qcpass_qty'], 2, '.', ''); ?>&nbsp;</td>
                        <td><? echo $prod_data_arr[$mst_id]['operator_name']; ?>&nbsp;</td>
                        <td><? echo $shift_name[$prod_data_arr[$row[csf('color_size_id')]]['shift_id']]; ?>&nbsp;</td>
                    </tr>
					<?
					$i++;
					$grand_tot_qty+=$prod_data_arr[$row[csf('color_size_id')]]['qcpass_qty'];
				}
				?>
                <tr class="tbl_bottom">
                    <td align="right" colspan="6"><strong>Grand Total</strong></td>
                    <td align="right"><? echo number_format($grand_tot_qty, 2, '.', ''); ?>&nbsp;</td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
            
            <br>
			<? echo signature_table(140, $com_id, "930px"); ?>
        </div>
    </div>
	<?
	exit();
}

if ($action == 'show_production_no')
{
	$ex_data=explode("__",$data);
	$company_id=$ex_data[0];
	$job_no=$ex_data[1];
	$embl_job_arr=array();
	$embl_job_sql="select a.embellishment_job, a.within_group, a.party_id, b.id, b.order_no, b.order_quantity as qty from subcon_ord_mst a, subcon_ord_dtls b where a.entry_form=204 and a.embellishment_job=b.job_no_mst";
	$embl_job_res=sql_select($embl_job_sql);
	foreach ($embl_job_res as $row)
	{
		$embl_job_arr[$row[csf("id")]]['order']=$row[csf("order_no")];
		$embl_job_arr[$row[csf("id")]]['within_group']=$row[csf("within_group")];
		$embl_job_arr[$row[csf("id")]]['party_id']=$row[csf("party_id")];
		$embl_job_arr[$row[csf("id")]]['qty']=$row[csf("qty")];
	}
	unset($embl_job_res);
	
	$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	$job_no_cond="";
	if($job_no!="") $job_no_cond="and a.job_no='$job_no'";
	
	$qc_qty_arr=array();
	$qc_sql="select a.recipe_id, sum(b.qcpass_qty) as qty , sum(b.reje_qty) as reje_qty from subcon_embel_production_mst a, subcon_embel_production_dtls b where a.id=b.mst_id and a.entry_form=223 and a.company_id='$company_id' $job_no_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.recipe_id";
	$qc_sql_res = sql_select($qc_sql);
	foreach ($qc_sql_res as $row)
	{
		$qc_qty_arr[$row[csf("recipe_id")]]['qcpass_qty']=$row[csf("qty")];
		$qc_qty_arr[$row[csf("recipe_id")]]['reje_qty']=$row[csf("reje_qty")];
	}
	unset($qc_sql_res);
	
 
	
	$sql = "select a.id, a.sys_no, a.prefix_no_num,a.floor_id, a.job_no, a.order_ids as order_id, a.buyer_po_ids as buyer_po_id, sum(b.qcpass_qty) as qty from subcon_embel_production_mst a, subcon_embel_production_dtls b where a.id=b.mst_id and a.entry_form=222 and a.company_id='$company_id' $job_no_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	group by a.id, a.sys_no, a.prefix_no_num, a.floor_id,a.job_no, a.order_ids, a.buyer_po_ids order by a.id desc";
	$data_array = sql_select($sql);
	?>
	<!--<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="500">-->
     <table width="310" class="rpt_table" cellpadding="0" cellspacing="0" rules="all" border="1">
		<thead>
			<th width="40"><P>Prod ID</P></th>
			<th width="60"><P>Job No.</P></th>
			<th width="50"><P>Buyer PO</P></th>
            <th width="40"><P>Prod. Qty.</P></th>
            <th width="40"><P>QC Pass Qty.</P></th>
            <th width="40"><P>Reject Qty.</P></th>
            <th width="40"><P>Balance</P></th>
		</thead>
	</table>
	<div style="width:330px; max-height:250px; overflow-y:scroll" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="310" class="rpt_table" id="tbl_prod_list_search">
			<?
			$i = 1;
			foreach ($data_array as $row)
			{
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				
				$within_group=0; $order_no=""; $party_id=0; $order_qty=0;
				$within_group=$embl_job_arr[$row[csf("order_id")]]['within_group'];
				$order_no=$embl_job_arr[$row[csf("order_id")]]['order'];
				$party_id=$embl_job_arr[$row[csf("order_id")]]['party_id'];
				$order_qty=$embl_job_arr[$row[csf("order_id")]]['qty'];
				
				$buyer_po=""; $buyer_style="";
				
				if($within_group==1)
				{
					$buyer_po=$buyer_po_arr[$row[csf("buyer_po_id")]]['po'];
					$buyer_style=$buyer_po_arr[$row[csf("buyer_po_id")]]['style'];
				}
				
				$str="";
				$str=$row[csf('id')].'___'.$row[csf('sys_no')].'___'.$row[csf('job_no')].'___'.$row[csf('order_id')].'___'.$order_no.'___'.$party_id.'___'.$within_group.'___'.$order_qty.'___'.$row[csf('buyer_po_id')].'___'.$buyer_po.'___'.$buyer_style.'___'.$row[csf('floor_id')];
				
				$qc_qty=0; $balance_qty=0;$totalrejeqty=0;
				$qc_qty=$qc_qty_arr[$row[csf("id")]]['qcpass_qty'];
				$reje_qty=$qc_qty_arr[$row[csf("id")]]['reje_qty'];
				$totalrejeqty=$reje_qty+$qc_qty;
				$balance_qty=$row[csf("qty")]-$totalrejeqty;
				
				if($balance_qty>0)
				{
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $str; ?>")' style="cursor:pointer">
						<td width="40" align="center"><P><? echo $row[csf('prefix_no_num')]; ?></P></td>
						<td width="60"><P><? echo $row[csf('job_no')]; ?></P></td>
						<td width="50"><P><? echo $buyer_po; ?></P></td>
						<td width="40" align="right"><P><? echo $row[csf("qty")]; ?></P></td>
                        <td width="40" align="right"><P><? echo $qc_qty; ?></P></td>
                        <td width="40" align="right"><P><? echo $reje_qty; ?></P></td>
						<td width="40" align="right"><P><? echo $balance_qty; ?></P></td>
					</tr>
					<?
					$i++;
				}
			}
			?>
		</table>
	</div>
	<?
	exit();
}

if($action=="remarks_popup")
{
	echo load_html_head_contents("Remarks","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <script>
	function js_set_value(val)
	{
		document.getElementById('text_new_remarks').value=val;
		parent.emailwindow.hide();
	}
	</script>
    </head>
<body>
<div align="center">
	<fieldset style="width:400px;margin-left:4px;">
        <form name="remarksfrm_1"  id="remarksfrm_1" autocomplete="off">
            <table cellpadding="0" cellspacing="0" width="370" >
                <tr>
                    <td align="center"><input type="hidden" name="auto_id" id="auto_id" value="<? echo $data; ?>" />
                      <textarea id="text_new_remarks" name="text_new_remarks" class="text_area" title="Maximum 1000 Character" maxlength="1000" style="width:330px; height:270px" placeholder="Remarks Here. Maximum 1000 Character." ><? echo $data; ?></textarea>
                    </td>
                </tr>
                <tr>
                	<td align="center">
                 <input type="button" id="formbuttonplasminus" align="middle" class="formbutton" style="width:100px" value="Close" onClick="js_set_value(document.getElementById('text_new_remarks').value)" />
                 	</td>
                </tr>
            </table>
        </form>
    </fieldset>
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}
?>