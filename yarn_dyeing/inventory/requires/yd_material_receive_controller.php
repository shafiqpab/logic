<?php
header('Content-type:text/html; charset=utf-8'); 
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header('location:login.php');
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_level=$_SESSION['logic_erp']["user_level"];

if($action == "load_drop_down_location") {
	echo create_drop_down('cbo_location_name', 163, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name", 'id,location_name', 1, '-- Select --', $selected, '');
	exit();
}
if ($action=="load_drop_down_party")
{
    $data=explode("_",$data);

    if($data[1]==1 && $data[0]!=0)
    {

        echo create_drop_down( "cbo_party_name", 162, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "");
    }
    elseif($data[1]==2 && $data[0]!=0)
    {
        echo create_drop_down( "cbo_party_name", 162, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",0, "" );
    }
    else
    {
    	echo create_drop_down('cbo_party_name', 162, $blank_array, '', 1, '-- Select Party --', $selected, "",1);
    }   
    exit();  
}


if ($action=="load_variable_settings")
{
 	echo "$('#work_order_material_auto_receive').val(0);\n";
  	$workorder_material_autoreceive=return_field_value("item_show_in_detail","variable_setting_yarn_dyeing ","company_name=$data and variable_list=1 and status_active=1 and is_deleted=0");
 	echo "$('#work_order_material_auto_receive').val(".$workorder_material_autoreceive.");\n";
	
 	exit();
}

if ($action=="load_drop_down_buyer") {
	// echo $data; die;
	$data = explode('_', $data);
	if($data[1]==1) {
		echo create_drop_down('cbo_party_name', 163, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", 'id,company_name', 1, '-- Select Party --', $data[2], '');
		exit();
	} else {
		echo create_drop_down('cbo_party_name', 163, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buyer_name", 'id,buyer_name', 1, '-- Select Party --', $data[2], '');
		exit();
	}
}

if ($action=="load_drop_down_buyer_pop") {
	// echo $data; die;
	$data = explode('_', $data);
	if($data[1]==1) {
		echo create_drop_down('cbo_party_name', 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", 'id,company_name', 1, '-- Select Party --', $data[2], '');
		exit();
	} else {
		echo create_drop_down('cbo_party_name', 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buyer_name", 'id,buyer_name', 1, '-- Select Party --', $data[2], '');
		exit();
	}
}


if($action == "job_search_popup_rcv1")
 {
	echo load_html_head_contents('Search Yarn Dyeing Job', '../../../', 1, 0, $unicode);
	extract($_REQUEST);
	$data=explode('_', $data);
	// print_r($data);
	?>
	<script>
		permission="<?php echo $permission; ?>";

		 function js_set_value(id)
		 {
  			document.getElementById('hdn_mat_mst_id').value = id;
			document.getElementById('hdn_company').value = <?php echo $data[0]; ?>;
			document.getElementById('hdn_location').value = <?php echo $data[1]; ?>;
			document.getElementById('hdn_party').value = <?php echo $data[2]; ?>;
            parent.jobPopup.hide();
		 }

		window.onload = function()
		{
            document.getElementById('cbo_company_name').removeAttribute('disabled');
        }
	</script>
</head>
<body>
<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" style="width: 100%;">
            <thead>
                <tr>
                    <th colspan="8"><?php echo create_drop_down('cbo_string_search_type', 163, $string_search_type, '', 1, '-- Searching Type --'); ?></th>
                </tr>
                <tr>
                    <th width="140">Company Name</th>
                    <th width="140">Receive ID</th>
                     <th width="140">Job No</th>
                    <th width="140">WO No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width: 100%" />
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="general">
                    <td>
                        <?php echo create_drop_down('cbo_company_name', 163, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 1, '-- Select Company --', $data[0], ''); ?>
                    </td>
                    <td>
                        <input class="text_boxes" type="text" name="txt_rcv_id" id="txt_rcv_id" style="width: 140px;">
                    </td>
                     <td>
                        <input class="text_boxes" type="text" name="txt_job_no" id="txt_job_no" />
                    </td>
                    <td>
                        <input class="text_boxes" type="text" name="txt_workorder_no" id="txt_workorder_no" style="width: 140px;">
                    </td>
                    <td align="center">
                        <input type="hidden" id="hdn_mat_mst_id">
                        <input type="hidden" id="hdn_company">
                        <input type="hidden" id="hdn_location">
                        <input type="hidden" id="hdn_party">
                        <input type="button" name="btnSearchJob" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_rcv_id').value+'_'+document.getElementById('txt_workorder_no').value+'_'+document.getElementById('txt_job_no').value, 'create_yd_materials_list_view', 'search_div', 'yd_material_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width: 100%;" />
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
<?php
exit();
}

if($action == 'create_yd_materials_list_view1')
 {
	// echo $data;die;
	$data=explode('_', $data);
    $search_type = $data[0];
    $condition = "";

    if($data[1]) {
        $condition.=" and a.company_id=$data[1]";
    } else {
        echo "<h3 style='margin-top: 10px;'>Please Select Company First.</h3>"; die;
    }

    if($data[0]==0 || $data[0]==4) { // no searching type or contents
        if ($data[2]!="") $condition.=" and a.yd_trans_no like '%$data[2]%'";
        if ($data[3]!="") $condition.=" and b.order_no like '%$data[3]%'";
		if ($data[4]!="") $condition.=" and b.yd_job like '%$data[4]%'";
    } else if($data[0]==1) { // exact
        if ($data[2]!="") $condition.=" and a.yd_trans_no = '$data[2]'";
        if ($data[3]!="") $condition.=" and b.order_no ='$data[3]'";
		if ($data[4]!="") $condition.=" and b.yd_job='$data[4]'";
    } else if($data[0]==2) { // Starts with
        if ($data[2]!="") $condition.=" and a.yd_trans_no like '$data[2]%'";
        if ($data[3]!="") $condition.=" and b.order_no like '$data[3]%'";
		if ($data[4]!="") $condition.=" and b.yd_job like '$data[4]%'";
    } else if($data[0]==3) { // Ends with
        if ($data[2]!="") $condition.=" and a.yd_trans_no like '%$data[2]'";
        if ($data[3]!="") $condition.=" and b.order_no like '%$data[3]'";
		if ($data[4]!="") $condition.=" and b.yd_job like '%$data[4]'";
    }

    $sql= "select a.id, a.yd_trans_no, a.chalan_no, a.receive_date, b.yd_job, b.order_no from yd_material_mst a, yd_ord_mst b
    	where a.is_deleted=0 and a.status_active=1 $condition and a.yd_job_id=b.id order by id DESC";
    // echo $sql;die;
    echo create_list_view('list_view', 'Receive ID,Job No,WO No', '140,140', 500, 300, 0, $sql, 'js_set_value', 'id', '', 1, '0,0,0', $arr, 'yd_trans_no,yd_job,order_no', '', '', '0,0,0,0');
    unset($sql);
    // create_list_view($table_id, $tbl_header_arr, $td_width_arr, $tbl_width, $tbl_height, $tbl_border, $query, $onclick_fnc_name, $onclick_fnc_param_db_arr, $onclick_fnc_param_sttc_arr, $show_sl, $field_printed_from_array_arr, $data_array_name_arr, $qry_field_list_array, $controller_file_path, $filter_grid_fnc, $fld_type_arr, $summary_flds, $check_box_all, $new_conn)
    exit();
}

if($action == "job_search_popup_rcv")
{
	echo load_html_head_contents('Search Yarn Dyeing Job', '../../../', 1, 0, $unicode);
	extract($_REQUEST);
 	$data=explode('_', $data);

	?>
	<script>
	permission="<?php echo $permission; ?>";
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('YD Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Style');
			else if(val==4) $('#search_by_td').html('Buyer Job');
		}

		/*function js_set_value(id,job_no)
		{ 
			$("#hidden_mst_id").val(id);
			$("#hidden_job_no").val(job_no);
			parent.emailwindow.hide();
		}*/
		
		function js_set_value(id)
		 {
			 
			// alert(id);
  			document.getElementById('hdn_mat_mst_id').value = id;
			document.getElementById('hdn_company').value = <?php echo $data[0]; ?>;
			document.getElementById('hdn_location').value = <?php echo $data[1]; ?>;
			document.getElementById('hdn_party').value = <?php echo $data[2]; ?>;
            parent.jobPopup.hide();
		 }

		window.onload = function()
		{
			var company_id = <?php echo $data[0]; ?>;
			var withinGroup = <?php echo $data[3]; ?>;
            document.getElementById('cbo_company_name').removeAttribute('disabled');
			load_drop_down('../requires/yd_material_receive_controller', company_id+'_'+withinGroup, 'load_drop_down_party', 'party_td' );
        }
	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_<?php echo $tblRow;?>" id="searchorderfrm_<?php echo $tblRow;?>" autocomplete="off">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" style="width: 100%;">
					<thead>
		                <tr>
		                    <th colspan="11"><?php echo create_drop_down('cbo_string_search_type', 163, $string_search_type, '', 1, '-- Searching Type --'); ?></th>
		                </tr>
		                <tr>
		                    <th width="150" class="must_entry_caption" >Company Name</th>
		                    <th width="80" class="must_entry_caption" >Within Group</th>
		                    <th width="162">Party Name</th>
		                    <th width="80">Receive No</th>
		                    <th width="90">Search By</th>
		                    <th width="70" id="search_by_td">YD Job No</th>
		                    <th width="70">Prod. Type</th>
		                    <th width="70">Order Type</th>
		                    <th width="70">Y/D Type</th>
		                    <th width="160">Receive Date Range</th>
		                    <th>
		                    	<input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width: 100%" />
                                 <input type="hidden" id="hdn_mat_mst_id">
                               <input type="hidden" id="hdn_company">
                              <input type="hidden" id="hdn_location">
                               <input type="hidden" id="hdn_party">
		                    </th>
		                </tr>
		            </thead>
		            <tbody>
                		<tr class="general">
                			<td>
		                        <?php echo create_drop_down('cbo_company_name', 150, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 1, '-- Select Company --',$data[0], "load_drop_down( 'yd_material_receive_controller', this.value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_party', 'party_td' );"); ?>
		                    </td>
		                    <td> 
	                            <?php echo create_drop_down('cbo_within_group', 80, $yes_no, '', 1, '-- Select Within Group --', $data[3], "load_drop_down( 'yd_material_receive_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_party', 'party_td' );",0); ?>
	                        </td>
	                        <td id="party_td"> 
	                            <?php echo create_drop_down('cbo_party_name', 162, $blank_array, '', 1, '-- Select Party --', $selected, "",""); ?>
	                        </td>
	                        <td > 
	                            <input name="txt_receive_no" id="txt_receive_no" class="text_boxes" style="width:80px" placeholder="Write Receive No">
	                        </td>
	                        <td>
	                        	<?
									$search_by_arr=array(1=>"YD Job No",2=>"W/O No",3=>"Buyer Style",4=>"Buyer Job");
									echo create_drop_down( "cbo_type",90, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
								?>
	                        </td>
	                        <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:70px" placeholder="Write" />
                            </td>
                            <td>
	                        	<? echo create_drop_down( "cbo_pro_type",70, $w_pro_type_arr,"",1, "--Select--",$selected,'',0 );?>
	                        </td>
	                        <td>
	                        	<? echo create_drop_down( "cbo_order_type",70, $w_order_type_arr,"",1, "--Select--",$selected,'',0 ); ?>
	                        </td>
	                        <td>
	                        	<? echo create_drop_down( "cbo_yd_type",70, $yd_type_arr,"",1, "--Select--",$selected,'',0 ); ?>
	                        </td>
	                        <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To">
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_pro_type').value+'_'+document.getElementById('cbo_order_type').value+'_'+document.getElementById('cbo_yd_type').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_receive_no').value, 'create_receive_search_list_view', 'search_div', 'yd_material_receive_controller', 'setFilterGrid(\'tbl_data_list\',-1)')" style="width:70px;" />
                            </td>
                		</tr>
                		<tr>
                            <td colspan="10" align="center" valign="middle">
                                <? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="text_boxes" style="width:70px">
                                <input type="hidden" name="hidden_job_no" id="hidden_job_no" class="text_boxes" style="width:70px">
                            </td>
                        </tr>
                	</tbody>
		        </table>
			</form>
		</div>
		<div id="search_div" align="center">
			
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?php
}

if($action=="create_receive_search_list_view")
{	
	$data=explode('_',$data);

	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp_arr=return_library_array("select id, company_name from lib_company",'id','company_name');

	$search_type 			=trim(str_replace("'","",$data[0]));
	$cbo_company_name  		=trim(str_replace("'","",$data[1]));
	$cbo_within_group 		=trim(str_replace("'","",$data[2]));
	$cbo_party_name 		=trim(str_replace("'","",$data[3]));
	$search_by 				=trim(str_replace("'","",$data[4]));
	$search_str 			=trim(str_replace("'","",$data[5]));
	$cbo_pro_type 			=trim(str_replace("'","",$data[6]));
	$cbo_order_type 		=trim(str_replace("'","",$data[7]));
	$cbo_yd_type 			=trim(str_replace("'","",$data[8]));
	$txt_date_from 			=trim(str_replace("'","",$data[9]));
	$txt_date_to 			=trim(str_replace("'","",$data[10]));
	$cbo_year_selection 	=trim(str_replace("'","",$data[11]));
	$txt_receive_no 		=trim(str_replace("'","",$data[12]));

	if($cbo_company_name==0)
	{
		echo "<p style='margin-top: 10px;'>Please Select Company Name first!!!</p>";
		die;
	}

	if($cbo_within_group==0)
	{
		echo "<p style='margin-top: 10px;'>Please Select Within Group first!!!</p>";
		die;
	}

	$condition = "";

	if($cbo_company_name!=0)
	{
		$condition .= " and c.company_id=$cbo_company_name";
	}

/*select 
   rowid, id, trans_no_prefix, trans_no_prefix_num, 
   yd_trans_no, company_id, location_id, 
   party_id, receive_date, chalan_no, 
   within_group, entry_form, issue_to, 
   receive_quantity, receive_id, remarks, 
   inserted_by, insert_date, updated_by, 
   update_date, status_active, is_deleted, 
   yd_job_id, trans_type, embl_job_no, 
   booking_without_order, booking_type, issue_chalan_no, 
   challan_id
from platformerpv3.yd_material_mst
order by id desc*/

	if($txt_receive_no!=0)
	{
		$condition .= " and c.trans_no_prefix_num=$txt_receive_no";
	}

	if($cbo_within_group!=0)
	{
		$condition .= " and c.within_group=$cbo_within_group";
	}

	if($cbo_party_name!=0)
	{
		$condition .= " and c.party_id=$cbo_party_name";
	}

	if($cbo_pro_type!=0)
	{
		$condition .= " and a.pro_type=$cbo_pro_type";
	}

	if($cbo_order_type!=0)
	{
		$condition .= " and a.order_type=$cbo_order_type";
	}

	if($cbo_yd_type!=0)
	{
		$condition .= " and a.yd_type=$cbo_yd_type";
	}


	$date_con = '';
	if($db_type==0)
    { 
        if ($txt_date_from!="" &&  $txt_date_to!="") $date_con = "and c.receive_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'"; else $date_con ="";
		$ins_year_cond="year(c.insert_date)";
    }
    else
    {
        if ($txt_date_from!="" &&  $txt_date_to!="") $date_con = "and c.receive_date between '".change_date_format($txt_date_from, "", "",1)."' and '".change_date_format($txt_date_to, "", "",1)."'"; else $date_con ="";
		$ins_year_cond="TO_CHAR(c.insert_date,'YYYY')"; 
    }

  
    if($search_type==1)
    {
        if($search_str!="")
        {
 			 
            if($search_by==1) $condition="and a.yd_job='$search_str'";
			else if($search_by==2) $condition="and a.order_no='$search_str'";
			else if ($search_by==3) $condition=" and b.style_ref = '$search_str' ";
			else if ($search_by==4) $condition=" and b.sales_order_no = '$search_str' ";
        }
        
    }
    else if($search_type==2)
    {
        if($search_str!="")
        {
            if($search_by==1) $condition="and a.yd_job like '$search_str%'";
			else if($search_by==2) $condition="and a.order_no like '$search_str%'";
			else if ($search_by==3) $condition=" and b.style_ref like  '$search_str%' ";
			else if ($search_by==4) $condition=" and b.sales_order_no like  '$search_str%' ";
        }
        
    }
    else if($search_type==3)
    {
        if($search_str!="")
        {
            if($search_by==1) $condition="and a.yd_job like '%$search_str'";
			else if($search_by==2) $condition="and a.order_no like '%$search_str'";
			else if ($search_by==3) $condition=" and b.style_ref like  '%$search_str' ";
			else if ($search_by==4) $condition=" and b.sales_order_no like  '%$search_str' ";
        }
        
    }
    else if($search_type==4 || $search_type==0)
    {
        if($search_str!="")
        {
            if($search_by==1) $condition="and a.yd_job like '%$search_str%'";
			else if($search_by==2) $condition="and a.order_no like '%$search_str%'";
			else if ($search_by==3) $condition=" and b.style_ref like  '%$search_str%' ";
			else if ($search_by==4) $condition=" and b.sales_order_no like  '%$search_str%' ";
        }
        
    }

// $sql= "select a.id, a.yd_trans_no, a.chalan_no, a.receive_date, b.yd_job, b.order_no from yd_material_mst a, yd_ord_mst b
    	//where a.is_deleted=0 and a.status_active=1 $condition and a.yd_job_id=b.id order by id DESC";
		
		  $sql= "select c.id, a.yd_job as job_no, a.job_no_prefix_num,a.within_group, $ins_year_cond as year, a.location_id, a.party_id, c.receive_date, a.order_no, a.delivery_date ,a.booking_without_order, a.booking_type,a.order_type, 
   a.yd_process, a.yd_type,a.pro_type,b.style_ref,b.count_type,b.sales_order_no,c.yd_trans_no,c.chalan_no,sum(d.receive_qty) as receive_qty from yd_ord_mst a,yd_ord_dtls b,yd_material_mst c,yd_material_dtls d 
   where a.id=b.mst_id 
   and a.entry_form=374 
   and a.status_active =1 
   and a.is_deleted =0  
   and c.yd_job_id=a.id  
   and c.id=d.mst_id 
   and b.id=d.job_dtls_id $date_con $condition group by  c.id, a.yd_job, a.job_no_prefix_num,a.within_group, c.insert_date, a.party_id, a.location_id, c.receive_date, a.order_no, a.delivery_date ,a.booking_without_order, a.booking_type,a.order_type, 
   a.yd_process, a.yd_type,a.pro_type,b.style_ref,b.count_type,b.sales_order_no,c.yd_trans_no,c.chalan_no order by c.id DESC";
		
	//$sql = "select a.yd_receive, a.id, b.style_ref, a.party_id, a.pro_type, a.within_group, a.job_no, a.order_no, a.order_id, a.order_type, a.receive_date, b.count_type, b.sales_order_no from yd_store_receive_mst a, yd_store_receive_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=571 $condition $date_con group by a.yd_receive, a.id, b.style_ref, a.party_id, a.pro_type, a.within_group, a.job_no, a.order_no, a.order_id, a.order_type, a.receive_date, b.count_type, b.sales_order_no order by a.yd_receive, a.id";

	$result = sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="1095" >
		<thead>
            <th width="30">SL</th>
            <th width="90">YD Job No</th>
             <th width="90">Receive No</th> 
             <th width="50">Year</th>  
            <th width="60">Prod. Type</th>
            <th width="60">Within Group</th>
             <th width="80">Party Name</th>
             <th width="80">Challan No</th>
             <th width="90">WO No</th>
            <th width="80">Buyer Style</th>
            <th width="80">Buyer Job</th>
            <th width="70">Order Type</th>
            <th width="70">Count Type</th>
            <th  width="80">Receive Date</th>
            <th>Receive Qty.</th>
        </thead>
	</table>
	<div style="width:1096px; max-height:370px;overflow-y:scroll;" >
		<table class="rpt_table" border="1" id="tbl_data_list" cellpadding="0" cellspacing="0" rules="all" width="1095" >
			<tbody>
				<?php
					$i=1;
					$count_type_arr = array(1 => "Single",2 => "Double");
					foreach($result as $data)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						if($data[csf('within_group')]==1)
						{
							$party_name = $comp_arr[$data[csf('party_id')]];

						}
						else
						{
							$party_name = $party_arr[$data[csf('party_id')]];
						}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value(<? echo $data[csf('id')]; ?>)' style="cursor:pointer">
					<td align="center" width="30"><? echo $i; ?></td>
                    <td align="center" width="90"><? echo $data[csf('job_no')]; ?></td>
                    <td align="center" width="90"><? echo $data[csf('yd_trans_no')]; ?></td>
                    <td align="center" width="50"><? echo $data[csf('year')]; ?></td> 
		            <td align="center" width="60"><? echo $w_pro_type_arr[$data[csf('pro_type')]]; ?></td>
		            <td align="center" width="60"><? echo $yes_no[$data[csf('within_group')]]; ?></td>
                    <td align="center" width="80"><? echo $party_name; ?></td>
                     <td align="center" width="80"><? echo $data[csf('chalan_no')]; ?></td>
 		            <td align="center" width="90"><? echo $data[csf('order_no')]; ?></td>
		            <td align="center" width="80"><? echo $data[csf('style_ref')]; ?></td>
		            <td align="center" width="80"><? echo $data[csf('sales_order_no')]; ?></td>
		            <td align="center" width="70"><? echo $w_order_type_arr[$data[csf('order_type')]]; ?></td>
		            <td align="center" width="70"><? echo $count_type_arr[$data[csf('count_type')]]; ?></td>
		            <td align="center" width="80"><? echo change_date_format($data[csf('receive_date')]); ?></td>
                     <td align="center"><? echo number_format($data[csf('receive_qty')],2,".",""); ?></td>
				</tr>
				<?php
					$i++;
					}
				?>
	        </tbody>
		</table>
	</div>
	<?php

	exit();
}

if($action=="job_search_popup_bbbbb")
{
 	echo load_html_head_contents('Search Yarn Dyeing Job', '../../../', 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_', $data);
    $company_id = $data[0];
    $within_group = $data[1];
    $party_id = $data[2]; 
?>
     
<script>
	    permission="<?php echo $permission; ?>";
        var party_id = "<?php echo $party_id ?>";
 		function js_set_value(id) 
		{
			document.getElementById('selected_order_id').value = id;
			document.getElementById('selected_job_no').value = <?php echo $data[0]; ?>;
			document.getElementById('selected_location').value = <?php echo $data[1]; ?>;
			document.getElementById('selected_party').value = <?php echo $data[2]; ?>;
            parent.jobPopup.hide();
		}

		window.onload = function() {
            document.getElementById('cbo_company_name').removeAttribute('disabled');
        }
</script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="880" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="1">
            <thead>
                 <tr>
                    <th colspan="8"><?php echo create_drop_down('cbo_string_search_type', 163, $string_search_type, '', 1, '-- Searching Type --'); ?></th>
                </tr>
                <tr>
                    <th width="163">Company Name</th>
                    <th width="150">Job No</th>
                    <th width="150">WO No</th>
                    <th width="150">Order Type</th>
                    <th width="150">Y/D Type</th> 
                    <th><input type="reset" name="re_button" id="re_button" class="formbutton" value="Reset" style="width:100px" /> </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                     <td>
                        <?php echo create_drop_down('cbo_company_name', 163, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 1, '-- Select Company --', $company_id, ''); ?>
                    </td>
                    <td>
                        <input class="text_boxes" type="text" name="txt_job_no" id="txt_job_no" />
                    </td>
                    <td>
                        <input class="text_boxes" type="text" name="txt_workorder_no" id="txt_workorder_no" />
                    </td>
                    <td>
						<?
                        $w_order_type_arr = array(1 => "Service",2 => "Sales");
                        echo create_drop_down("order_type", 150, $w_order_type_arr,"", 1, "-- Select Type --",$selected,"fnc_load_order_type(this.value);", "","","","","",7 ); 
                        ?>
                    </td>                                    
                    <td>
						<?
                        $yd_type_arr = array(1 => "Yarn Dyeing",2 => "Piece Dyeing",3 => "Thread Dyeing");
                        echo create_drop_down("yd_type", 150, $yd_type_arr,"", 1, "-- Select Y/D Type --",$selected,"", "","","","","",7 ); 
                        ?>
                    </td>
                    <td align="center">
                        <input type="hidden" id="selected_order_id">
                        <input type="hidden" id="selected_job_no">
                        <input type="hidden" id="selected_location">
                        <input type="hidden" id="selected_party">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_workorder_no').value+'_'+'<? echo $party_id ?>'+'_'+document.getElementById('order_type').value+'_'+document.getElementById('yd_type').value+'_'+'<? echo $within_group ?>', 'create_yd_order_list_view', 'search_div', 'yd_material_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;"/>
                    </td>
            </tr>    
            </tbody>
         </tr>         
        </table>   
        <br> 
        <div align="center" valign="top" id="search_div"> </div> 
        </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_yd_order_list_view_bbbb")
{
	$data=explode('_', $data);
    $search_type = $data[0];
    $company_id = $data[1];
    $yd_job = $data[2];
    $ord_no = $data[3];
    $party_id = $data[4];
	$order_type = $data[5];
	$yd_type = $data[6];
	$within_group = $data[7];
	
//	echo $within_group; die;
    $condition = '';
	if($within_group) 
	{
        $condition.=" and within_group=$data[7]";
    }

    if($company_id)
	{
        $condition.=" and company_id=$data[1]";
    } 
	else 
	{
        echo "<h3 style='margin-top: 10px;'>Please Select Company First.</h3>"; die;
    }
	
	if($party_id)
	{
        $condition.=" and a.party_id=$party_id";
    } 

	
	if($order_type)  
	{
        $condition.=" and order_type=$data[5]";
    }
	if($yd_type) 
	{
        $condition.=" and yd_type=$data[6]";
    }

    if($search_type==0 || $search_type==4)
	 { // no searching type or contents
        if ($yd_job!="") $condition.=" and a.yd_job like '%$yd_job%'";
        if ($ord_no!="") $condition.=" and a.order_no like '%$ord_no%'";
    } 
	else if($search_type==1)
	 { // exact
        if ($yd_job!="") $condition.=" and a.yd_job = '$yd_job'";
        if ($ord_no!="") $condition.=" and a.order_no ='$ord_no'";
    } 
	else if($search_type==2) 
	{ // Starts with
        if ($yd_job!="") $condition.=" and a.yd_job like '$yd_job%'";
        if ($ord_no!="") $condition.=" and a.order_no like '$ord_no%'";
    } 
	else if($search_type==3)
	 { // Ends with
        if ($yd_job!="") $condition.=" and a.yd_job like '%$yd_job'";
        if ($ord_no!="") $condition.=" and a.order_no like '%$ord_no'";
    }


	if($within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
 
    $sql= "select distinct a.id, a.yd_job, a.order_no,a.within_group, a.party_id,a.order_type,a.yd_type,a.pro_type,b.count_type  from yd_ord_mst a, yd_ord_dtls b where a.is_deleted=0 and a.status_active=1 and a.id=b.mst_id $condition order by id DESC";
$data_array=sql_select($sql);
	?>

		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table">
            <thead>    
            <th width="30">SL</th>
            <th width="100">Party Short Name</th>
            <th width="100">Prod Type</th>
            <th width="100">Within Group</th>
            <th width="100">Job No</th>
            <th width="100">WO No</th>
            <th width="100">Order Type</th>
            <th width="100">YD Type</th>
            <th  >Count Type</th>
            </thead>
         </table>
         <div style="width:900px; max-height:250px; overflow-y:scroll">
             <table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table" id="list_view">
             <? 
             $i=1;
             foreach ($data_array as $row)
             {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                     <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')];?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="100" align="center"><? echo $party_arr[$row[csf('party_id')]]; ?></td>
                    <td width="100" style="text-align:center;"><? echo  $w_pro_type_arr[$row[csf('pro_type')]]; ?></td>
                    <td width="100"><? echo $yes_no[$row[csf('within_group')]]; ?></td>
                    <td width="100"><? echo $row[csf('yd_job')]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $row[csf('order_no')]; ?></td>
                    <td width="100" style="word-break:break-all"><?  echo $w_order_type_arr[$row[csf('order_type')]]; ?></td>
                    <td width="100" style="text-align:center;"><? echo  $yd_type_arr[$row[csf('yd_type')]]; ?></td>
                    <td  style="word-break:break-all"><? echo $count_type_arr[$row[csf('count_type')]]; ?></td>
                </tr>
                    <?
             		$i++;     
             }
             ?>
            </table>
	<?
	exit();
	
	
}



if ($action=="job_search_popup")
{
 	echo load_html_head_contents('Search Yarn Dyeing Job', '../../../', 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_', $data);
    $company_id = $data[0];
    $within_group = $data[1];
    $party_id = $data[2];   
?>
     
<script>
	    permission="<?php echo $permission; ?>";
        var party_id = "<?php echo $party_id ?>";
 		function js_set_value(id) 
		{
			document.getElementById('selected_order_id').value = id;
			document.getElementById('selected_job_no').value = <?php echo $data[0]; ?>;
			document.getElementById('selected_location').value = <?php echo $data[1]; ?>;
			document.getElementById('selected_party').value = <?php echo $data[2]; ?>;
            parent.jobPopup.hide();
		}

		window.onload = function() 
		{
            //document.getElementById('cbo_company_name').removeAttribute('disabled');

            var company = $('#cbo_company_name').val();
            var within_group = $('#cbo_within_group').val();

            load_drop_down( 'yd_material_receive_controller', company+'_'+within_group, 'load_drop_down_buyer_pop', 'buyer_td' );

            $('#cbo_party_name').val(<?php echo $party_id;?>);
            document.getElementById('cbo_party_name').setAttribute('disabled', 'disabled');
        }
	
        
        function fnc_load_party_popup(type,within_group)
        {
            var company = $('#cbo_company_name').val();
            var party_name = $('#cbo_party_name').val();
            var location_name = $('#cbo_location_name').val();
            load_drop_down( 'yd_material_receive_controller', company+'_'+within_group, 'load_drop_down_buyer_pop', 'buyer_td' );
        }
 		
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('YD Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Style');
			else if(val==4) $('#search_by_td').html('Buyer Job');
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
                    <th width="80">Within Group</th>                           
                    <th width="100">Party Name</th>
                    <th width="100">Search By</th>
                    <th width="60" id="search_by_td">YD Job No</th>
                     <th width="60" style="display:none">YD Worder No</th>
                    <th width="100">Year</th>
                    <th width="180">Date Range</th>                            
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                </tr>           
            </thead>
            <tbody>
                <tr class="general">
                    <td><input type="hidden" id="selected_job"> 
                     <!--  echo $data;-->
                        <?  
                        echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --",$data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
                    </td>
                    <td>
                        <?php echo create_drop_down( "cbo_within_group", 80, $yes_no,"", 1, "-- Select --",$within_group, "fnc_load_party_popup(1,this.value);",1 ); ?>
                    </td>
                    <td id="buyer_td">
                        <?
                        
                        echo create_drop_down( "cbo_party_name", 100,$blank_array,"", 1, "-- Select Party --",'', "" );      
                        ?>
                    </td>
                    <td>
                        <?
                            $search_by_arr=array(1=>"YD Job No",2=>"W/O No",3=>"Buyer Style",4=>"Buyer Job");
                            echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                        ?>
                    </td>
                    <td align="center">
                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:80px" placeholder="" />
                    </td>
                    <td align="center" style="display:none">
                        <input type="text" name="txt_search_order" id="txt_search_order" class="text_boxes" style="width:80px" placeholder="" />
                    </td>
                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 100, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                    </td>
                    <td align="center">
                       <input type="hidden" id="selected_order_id">
                        <input type="hidden" id="selected_job_no">
                        <input type="hidden" id="selected_location">
                        <input type="hidden" id="selected_party">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_search_order').value+'_'+'<? echo $data[4];?>'+'_'+document.getElementById('cbo_string_search_type').value, 'create_yd_search_list_view', 'search_div', 'yd_material_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:70px;" /></td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" valign="middle">
                            <? echo load_month_buttons();  ?>
                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                        </td>
                    </tr>  
                </tbody>
            </table>    
            </form>
        </div>
        <div id="search_div"></div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action == "create_yd_search_list_view")
{
    $contact_person = return_library_array("select id, contact_person from lib_supplier", 'id', 'contact_person');
    $company_library=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $ex_data    = explode("_", $data);
    $company    = $ex_data[0];
    $party      = $ex_data[1];
    $fromDate   = $ex_data[2];
    $toDate     = $ex_data[3];
    //$yd_job_no     = $ex_data[5];
    $withinGroup = $ex_data[6];
    $yd_year = $ex_data[7];
    $yd_order = $ex_data[8];
	$search_type 			=trim(str_replace("'","",$ex_data[10]));
	
	
	$search_by 				=trim(str_replace("'","",$ex_data[4]));
	$search_str 			=trim(str_replace("'","",$ex_data[5]));
	//echo $search_type ;
    $sql_cond='';
    if($company!=0) $sql_cond.=" and a.company_id=$company"; 
    else { echo "Please Select Company First."; die; }

    if($withinGroup != 0) $sql_cond.= " and a.within_group=$withinGroup";
    if($yd_job_no != '') $sql_cond.= " and a.job_no_prefix_num=$yd_job_no";
    
    if($party != 0) $sql_cond.= " and a.party_id='$party'";
    if($yd_order != '') $sql_cond= " and a.order_no LIKE '%$yd_order%'";
    if($db_type==0){ 
        if ($fromDate!="" &&  $toDate!="") $sql_cond .= "and a.receive_date between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
        $ins_year_cond="year(a.insert_date)";
    }else{
        if ($fromDate!="" &&  $toDate!="") $sql_cond .= "and a.receive_date between '".change_date_format($fromDate, "", "",1)."' and '".change_date_format($toDate, "", "",1)."'";
        $ins_year_cond="TO_CHAR(a.insert_date,'YYYY')"; 
    }
    if($yd_year != 0) $sql_cond.= " and $ins_year_cond=$yd_year";
	
	 
	 
	 
	 
	 if($search_type==1)
    {
        if($search_str!="")
        {
            if($search_by==1) $condition="and a.yd_job='$search_str'";
			else if($search_by==2) $condition="and a.order_no='$search_str'";
			else if ($search_by==3) $condition=" and b.style_ref = '$search_str' ";
			else if ($search_by==4) $condition=" and b.sales_order_no = '$search_str' ";
        }
        
    }
    else if($search_type==2)
    {
        if($search_str!="")
        {
            if($search_by==1) $condition="and a.yd_job like '$search_str%'";
			else if($search_by==2) $condition="and a.order_no like '$search_str%'";
			else if ($search_by==3) $condition=" and b.style_ref like  '$search_str%' ";
			else if ($search_by==4) $condition=" and b.sales_order_no like  '$search_str%' ";
        }
        
    }
    else if($search_type==3)
    {
        if($search_str!="")
        {
            if($search_by==1) $condition="and a.yd_job like '%$search_str'";
			else if($search_by==2) $condition="and a.order_no like '%$search_str'";
			else if ($search_by==3) $condition=" and b.style_ref like  '%$search_str' ";
			else if ($search_by==4) $condition=" and b.sales_order_no like  '%$search_str' ";
        }
        
    }
    else if($search_type==4 || $search_type==0)
    {
        if($search_str!="")
        {
            if($search_by==1) $condition="and a.yd_job like '%$search_str%'";
			else if($search_by==2) $condition="and a.order_no like '%$search_str%'";
			else if ($search_by==3) $condition=" and b.style_ref like  '%$search_str%' ";
			else if ($search_by==4) $condition=" and b.sales_order_no like  '%$search_str%' ";
        }
        
    }

	 
	
	
    $sql= "select a.id, a.yd_job, a.job_no_prefix_num,a.within_group, $ins_year_cond as year, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date ,a.booking_without_order, a.booking_type,a.order_type, 
   a.yd_process, a.yd_type,a.pro_type,b.style_ref,b.count_type,b.sales_order_no from yd_ord_mst a,yd_ord_dtls b where a.id=b.mst_id and a.entry_form=374 and a.status_active =1 and a.is_deleted =0 $sql_cond $condition group by  a.id, a.yd_job, a.job_no_prefix_num,a.within_group, a.insert_date, a.party_id, a.location_id, a.receive_date, a.order_no, a.delivery_date ,a.booking_without_order, a.booking_type,a.order_type, 
   a.yd_process, a.yd_type,a.pro_type,b.style_ref,b.count_type,b.sales_order_no order by a.id DESC";
    $data_array=sql_select($sql);  
    ?>
    <div style="width:1350px;"  align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1300" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="120">Job No/Sales order no</th>
                <th width="80">Job Suffix</th>
                <th width="100">WO No.</th>
                <th width="100">Buyer Style</th>
                <th width="100">Buyer Job</th> 
                <th width="60">Within Group</th>
                <th width="100">Party</th> 
                <th width="80">Prod. Type</th>
                <th width="80">Order Type</th>
                <th width="80">Y/D Type</th>
                <th width="100">Y/D Process</th>
                <th width="80">Count Type</th>
                <th width="80">Ord. Receive Date</th>
                <th>Delivery Date</th>
            </thead>
        </table>
        <div style="width:1350px;  overflow-y:scroll; max-height:300px;" id="buyer_list_view">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1300" class="rpt_table"
            id="tbl_list_search">
            <?
            $i = 1;
            foreach ($data_array as $selectResult)
            {
                if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                $within_group=$selectResult[csf('within_group')];
                if($within_group==1)
                {
                    $com_buyer=$company_library[$selectResult[csf('party_id')]];
                }
                else
                {
                    $com_buyer=$party_arr[$selectResult[csf('party_id')]];
                }
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                    id="search<? echo $i; ?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>); ">
                    <td width="30" align="center"><p><? echo $i; ?></p></td>
                    <td width="120" align="center"><p> <? echo $selectResult[csf('yd_job')]; ?></p></td>
                    <td width="80" align="center"><p> <? echo $selectResult[csf('job_no_prefix_num')]; ?></p></td>
                    <td width="100" align="center"><p> <? echo $selectResult[csf('order_no')]; ?></p></td>
                    <td width="100" align="center"><p> <? echo $selectResult[csf('style_ref')]; ?></p></td>
                    <td width="100" align="center"><p> <? echo $selectResult[csf('sales_order_no')]; ?></p></td>
                    <td width="60" align="center"><p> <? echo $yes_no[$selectResult[csf('within_group')]]; ?></p></td>
                    <td width="100" align="center"><p> <? echo $com_buyer; ?></p></td>
                    <td width="80" align="center"><p> <? echo $w_pro_type_arr[$selectResult[csf('pro_type')]]; ?></p></td>
                    <td width="80" align="center"><p> <? echo $w_order_type_arr[$selectResult[csf('order_type')]]; ?></p></td>
                    <td width="80" align="center"><p> <? echo $yd_type_arr[$selectResult[csf('yd_type')]]; ?></p></td>
                    <td width="100" align="center"><p> <? echo $yd_process_arr[$selectResult[csf('yd_process')]]; ?></p></td>
                    <td width="80" align="center"><p> <? echo $count_type_arr[$selectResult[csf('count_type')]]; ?></p></td>
                    <td width="80"><p><? echo change_date_format($selectResult[csf('receive_date')]); ?></p></td>
                    <td><p><? echo change_date_format($selectResult[csf('delivery_date')]); ?></p></td>
                </tr>
                <?
                $i++;
            }
            ?>
        </table>
        </div>
    </div>
    <? exit();
}

if($action == "job_search_popuperer") 
{
	echo load_html_head_contents('Search Yarn Dyeing Job', '../../../', 1, 0, $unicode);
	extract($_REQUEST);
	$data=explode('_', $data);
    $company_id = $data[0];
    $within_group = $data[1];
    $party_id = $data[2];
	
	//echo $within_group; die;
	// print_r($data);die;
	?>
    <style>
        table.rpt_table tbody td input {
            width: 90%;
        } 
    </style>
	<script>
		permission="<?php echo $permission; ?>";
        var party_id = "<?php echo $party_id ?>";

		function js_set_value(id) 
		{
			document.getElementById('selected_order_id').value = id;
			document.getElementById('selected_job_no').value = <?php echo $data[0]; ?>;
			document.getElementById('selected_location').value = <?php echo $data[1]; ?>;
			document.getElementById('selected_party').value = <?php echo $data[2]; ?>;
            parent.jobPopup.hide();
		}

		window.onload = function() {
            document.getElementById('cbo_company_name').removeAttribute('disabled');
        }
	</script>
</head>
<body>
<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" style="width: 100%;">
            <thead>
                <tr>
                    <th colspan="8"><?php echo create_drop_down('cbo_string_search_type', 163, $string_search_type, '', 1, '-- Searching Type --'); ?></th>
                </tr>
                <tr>
                    <th width="163">Company Name</th>
                    <th width="150">Job No</th>
                    <th width="150">WO No</th>
                    <th width="150">Order Type</th>
                    <th width="150">Y/D Type</th> 
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width: 90%;" /> </th>
                </tr>
            </thead>
            <tbody>
                <tr class="general">
                    <td>
                        <?php echo create_drop_down('cbo_company_name', 163, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 1, '-- Select Company --', $company_id, ''); ?>
                    </td>
                    <td>
                        <input class="text_boxes" type="text" name="txt_job_no" id="txt_job_no" />
                    </td>
                    <td>
                        <input class="text_boxes" type="text" name="txt_workorder_no" id="txt_workorder_no" />
                    </td>
                    <td>
						<?
                        $w_order_type_arr = array(1 => "Service",2 => "Sales");
                        echo create_drop_down("order_type", 150, $w_order_type_arr,"", 1, "-- Select Type --",$selected,"fnc_load_order_type(this.value);", "","","","","",7 ); 
                        ?>
                    </td>                                    
                    <td>
						<?
                        $yd_type_arr = array(1 => "Yarn Dyeing",2 => "Piece Dyeing",3 => "Thread Dyeing");
                        echo create_drop_down("yd_type", 150, $yd_type_arr,"", 1, "-- Select Y/D Type --",$selected,"", "","","","","",7 ); 
                        ?>
                    </td>
                    <td align="center">
                        <input type="hidden" id="selected_order_id">
                        <input type="hidden" id="selected_job_no">
                        <input type="hidden" id="selected_location">
                        <input type="hidden" id="selected_party">
                        <input type="button" name="btnSearchJob" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_workorder_no').value+'_'+'<? echo $party_id ?>'+'_'+document.getElementById('order_type').value+'_'+document.getElementById('yd_type').value+'_'+'<? echo $within_group ?>', 'create_yd_order_list_view', 'search_div', 'yd_material_receive_controller', 'setFilterGrid(\'tbl_po_list\',-1)')"  style="width:100px;"/>
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
<?php
exit();
}
if($action == 'create_yd_order_list_viewsds') 
{
	 //echo $data;die;
	$data=explode('_', $data);
    $search_type = $data[0];
    $company_id = $data[1];
    $yd_job = $data[2];
    $ord_no = $data[3];
    $party_id = $data[4];
	$order_type = $data[5];
	$yd_type = $data[6];
	$within_group = $data[7];
	
//	echo $within_group; die;
    $condition = '';
	if($within_group) 
	{
        $condition.=" and within_group=$data[7]";
    }

    if($company_id)
	{
        $condition.=" and company_id=$data[1]";
    } 
	else 
	{
        echo "<h3 style='margin-top: 10px;'>Please Select Company First.</h3>"; die;
    }
	
	if($party_id)
	{
        $condition.=" and a.party_id=$party_id";
    } 

	
	if($order_type)  
	{
        $condition.=" and order_type=$data[5]";
    }
	if($yd_type) 
	{
        $condition.=" and yd_type=$data[6]";
    }

    if($search_type==0 || $search_type==4)
	 { // no searching type or contents
        if ($yd_job!="") $condition.=" and a.yd_job like '%$yd_job%'";
        if ($ord_no!="") $condition.=" and a.order_no like '%$ord_no%'";
    } 
	else if($search_type==1)
	 { // exact
        if ($yd_job!="") $condition.=" and a.yd_job = '$yd_job'";
        if ($ord_no!="") $condition.=" and a.order_no ='$ord_no'";
    } 
	else if($search_type==2) 
	{ // Starts with
        if ($yd_job!="") $condition.=" and a.yd_job like '$yd_job%'";
        if ($ord_no!="") $condition.=" and a.order_no like '$ord_no%'";
    } 
	else if($search_type==3)
	 { // Ends with
        if ($yd_job!="") $condition.=" and a.yd_job like '%$yd_job'";
        if ($ord_no!="") $condition.=" and a.order_no like '%$ord_no'";
    }


	if($within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
 
    $sql= "select distinct a.id, a.yd_job, a.order_no,a.within_group, a.party_id,a.order_type,a.yd_type,a.pro_type,b.count_type  from yd_ord_mst a, yd_ord_dtls b where a.is_deleted=0 and a.status_active=1 and a.id=b.mst_id $condition order by id DESC";
$data_array=sql_select($sql);
    $w_pro_type_arr = array(1 => "Bulk",2 => "Sample");
	 $w_order_type_arr = array(1 => "Service",2 => "Sales");
	 $yd_type_arr = array(1 => "Yarn Dyeing",2 => "Piece Dyeing",3 => "Thread Dyeing");
	  $count_type_arr = array(1 => "Single",2 => "Double");
	
	?>
     	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="885" >
        <thead>
            <th width="30">SL</th>
            <th width="100">Party Short Name</th>
            <th width="60">Prod Type</th>
            <th width="100">Within Group</th>
            <th width="100">Job No</th>
            <th width="100">WO No</th>
            <th width="100">Order Type</th>
            <th width="60">YD Type</th>
            <th>Count Type</th>
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
                 ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')];?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="100" align="center"><? echo $party_arr[$row[csf('party_id')]]; ?></td>
                    <td width="60" style="text-align:center;"><? echo  $w_pro_type_arr[$row[csf('pro_type')]]; ?></td>
                    <td width="100"><? echo $yes_no[$row[csf('within_group')]]; ?></td>
                    <td width="100"><? echo $row[csf('yd_job')]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $row[csf('order_no')]; ?></td>
                    <td width="100" style="word-break:break-all"><?  echo $w_order_type_arr[$row[csf('order_type')]]; ?></td>
                    <td width="60" style="text-align:center;"><? echo  $yd_type_arr[$row[csf('yd_type')]]; ?></td>
                     <td style="word-break:break-all"><? echo $count_type_arr[$row[csf('count_type')]]; ?></td>
                </tr>
				<? 
                $i++; 
            } 
            ?>
        </tbody>
    </table>
    <?



    unset($sql);
    exit();
}



if($action == "issue_search_popup") 
{
	echo load_html_head_contents('Search Yarn Dyeing Job', '../../../', 1, 0, $unicode);
	extract($_REQUEST);
	$data=explode('_', $data);
    $company_id = $data[0];
    $within_group = $data[1];
    $party_id = $data[2];
	// print_r($data);die;
	?>
    <style>
        table.rpt_table tbody td input {
            width: 90%;
        } 
    </style>
	<script>
		permission="<?php echo $permission; ?>";
        var party_id = "<?php echo $party_id ?>";

		function js_set_value(id) 
		{
			//alert(id);
			document.getElementById('selected_order_id').value = id;
			document.getElementById('selected_job_no').value = <?php echo $data[0]; ?>;
			document.getElementById('selected_location').value = <?php echo $data[1]; ?>;
			document.getElementById('selected_party').value = <?php echo $data[2]; ?>;
            parent.jobPopup.hide();
		}

		window.onload = function()
		 {
            document.getElementById('cbo_company_name').removeAttribute('disabled');
        }
	</script>
</head>
<body>
<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" style="width: 100%;">
            <thead>
                <tr>
                    <th colspan="8"><?php echo create_drop_down('cbo_string_search_type', 163, $string_search_type, '', 1, '-- Searching Type --'); ?></th>
                </tr>
                <tr>
                    <th style="width: 20%;">Company Name</th>
                    <th style="width: 25%;">Job No</th>
                    <th style="width: 25%;">WO No</th>
                    <th style="width: 20%;">
                    	<input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width: 90%;" />
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="general">
                    <td>
                        <?php echo create_drop_down('cbo_company_name', 163, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 1, '-- Select Company --', $company_id, ''); ?>
                    </td>
                    <td>
                        <input class="text_boxes" type="text" name="txt_job_no" id="txt_job_no" />
                    </td>
                    <td>
                        <input class="text_boxes" type="text" name="txt_workorder_no" id="txt_workorder_no" />
                    </td>
                    <td align="center">
                        <input type="hidden" id="selected_order_id">
                        <input type="hidden" id="selected_job_no">
                        <input type="hidden" id="selected_location">
                        <input type="hidden" id="selected_party">
                        <input type="button" name="btnSearchJob" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_workorder_no').value+'_'+party_id, 'create_yd_issue_order_list_view', 'search_div', 'yd_material_receive_controller', 'setFilterGrid(\'list_view\',-1)')" />
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
<?php
exit();
}
if($action == 'create_yd_issue_order_list_view') 
{
	// echo $data;die;
	$data=explode('_', $data);
    $search_type = $data[0];
    $company_id = $data[1];
    $yd_job = $data[2];
    $ord_no = $data[3];
    $party_id = $data[4];
	
	//echo $ord_no; die;
    $condition = '';

    if($company_id) {
        $condition.=" and a.company_id=$data[1]";
    } else {
        echo "<h3 style='margin-top: 10px;'>Please Select Company First.</h3>"; die;
    }

    if($search_type==0 || $search_type==4)
	 { // no searching type or contents
        if ($yd_job!="") $condition.=" and a.yd_job like '%$yd_job%'";
        if ($ord_no!="") $condition.=" and a.order_no like '%$ord_no%'";
    } 
	else if($search_type==1)
	 { // exact
        if ($yd_job!="") $condition.=" and a.yd_job = '$yd_job'";
        if ($ord_no!="") $condition.=" and a.order_no ='$ord_no'";
    } 
	else if($search_type==2) 
	{ // Starts with
        if ($yd_job!="") $condition.=" and a.yd_job like '$yd_job%'";
        if ($ord_no!="") $condition.=" and a.order_no like '$ord_no%'";
    } 
	else if($search_type==3)
	 { // Ends with
        if ($yd_job!="") $condition.=" and a.yd_job like '%$yd_job'";
        if ($ord_no!="") $condition.=" and a.order_no like '%$ord_no'";
    }

      $sql= "select distinct a.id, a.yd_job, a.order_no, a.party_id,d.id as issue_id,d.issue_number from yd_ord_mst a, yd_ord_dtls b , inv_transaction  c,inv_issue_master d where a.is_deleted=0 and a.status_active=1 and a.id=b.mst_id and d.id=c.mst_id  and b.order_id=d.booking_id   and b.product_id=c.prod_id and c.transaction_type = 2 and c.item_category = 1  and  b.sales_order_no=c.job_no $condition order by id DESC";
	
	// $sql = "SELECT a.yd_job, b.id, b.style_ref, b.lot, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, b.no_bag, b.cone_per_bag, b.no_cone, b.avg_wgt, b.order_quantity, b.sales_order_id, b.sales_order_no, b.product_id,b.uom,c.cons_quantity from yd_ord_mst a, yd_ord_dtls b, inv_transaction  c,inv_issue_master d  where a.id='$mat_mst_id' and a.id = b.mst_id  and d.id=c.mst_id  and b.order_id=d.booking_id  and b.product_id=c.prod_id and a.status_active=1 and c.transaction_type = 2 AND c.item_category = 1  and  b.sales_order_no=c.job_no and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";  

 // echo $sql;

    echo create_list_view('list_view', 'Job No,WO No,Issue No', '140,140', 500, 300, 0, $sql, 'js_set_value', 'id,issue_id,issue_number', '', 1, '0,0,0', $column_arr, 'yd_job,order_no,issue_number', '', '', '0,0,0');

    unset($sql);
    exit();
}

/*if($action == "issue_search_popup") 
{
	echo load_html_head_contents('Search Yarn Dyeing Job', '../../../', 1, 0, $unicode);
	extract($_REQUEST);
	$data=explode('_', $data);
    $company_id = $data[0];
    $within_group = $data[1];
    $party_id = $data[2];
	// print_r($data);die;
	?>
    <style>
        table.rpt_table tbody td input {
            width: 90%;
        } 
    </style>
	<script>
		permission="<?php echo $permission; ?>";
        var party_id = "<?php echo $party_id ?>";

		function js_set_value(id) {
			document.getElementById('selected_order_id').value = id;
			document.getElementById('selected_job_no').value = <?php echo $data[0]; ?>;
			document.getElementById('selected_location').value = <?php echo $data[1]; ?>;
			document.getElementById('selected_party').value = <?php echo $data[2]; ?>;
            parent.jobPopup.hide();
		}

		window.onload = function() {
            document.getElementById('cbo_company_name').removeAttribute('disabled');
        }
	</script>
</head>
<body>
<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" style="width: 100%;">
            <thead>
                <tr>
                    <th colspan="8"><?php echo create_drop_down('cbo_string_search_type', 163, $string_search_type, '', 1, '-- Searching Type --'); ?></th>
                </tr>
                <tr>
                    <th style="width: 20%;">Company Name</th>
                    <th style="width: 25%;">Job No</th>
                    <th style="width: 25%;">WO No</th>
                    <th style="width: 20%;">
                    	<input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width: 90%;" />
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="general">
                    <td>
                        <?php echo create_drop_down('cbo_company_name', 163, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 1, '-- Select Company --', $company_id, ''); ?>
                    </td>
                    <td>
                        <input class="text_boxes" type="text" name="txt_job_no" id="txt_job_no" />
                    </td>
                    <td>
                        <input class="text_boxes" type="text" name="txt_workorder_no" id="txt_workorder_no" />
                    </td>
                    <td align="center">
                        <input type="hidden" id="selected_order_id">
                        <input type="hidden" id="selected_job_no">
                        <input type="hidden" id="selected_location">
                        <input type="hidden" id="selected_party">
                        <input type="button" name="btnSearchJob" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_workorder_no').value+'_'+party_id, 'create_yd_issue_order_list_view', 'search_div', 'yd_material_receive_controller', 'setFilterGrid(\'list_view\',-1)')" />
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
<?php
exit();
}
if($action == 'create_yd_issue_order_list_view') 
{
	// echo $data;die;
	$data=explode('_', $data);
    $search_type = $data[0];
    $company_id = $data[1];
    $yd_job = $data[2];
    $ord_no = $data[3];
    $party_id = $data[4];
	
	//echo $ord_no; die;
    $condition = '';

    if($company_id) {
        $condition.=" and company_id=$data[1]";
    } else {
        echo "<h3 style='margin-top: 10px;'>Please Select Company First.</h3>"; die;
    }

    if($search_type==0 || $search_type==4)
	 { // no searching type or contents
        if ($yd_job!="") $condition.=" and a.yd_job like '%$yd_job%'";
        if ($ord_no!="") $condition.=" and a.order_no like '%$ord_no%'";
    } 
	else if($search_type==1)
	 { // exact
        if ($yd_job!="") $condition.=" and a.yd_job = '$yd_job'";
        if ($ord_no!="") $condition.=" and a.order_no ='$ord_no'";
    } 
	else if($search_type==2) 
	{ // Starts with
        if ($yd_job!="") $condition.=" and a.yd_job like '$yd_job%'";
        if ($ord_no!="") $condition.=" and a.order_no like '$ord_no%'";
    } 
	else if($search_type==3)
	 { // Ends with
        if ($yd_job!="") $condition.=" and a.yd_job like '%$yd_job'";
        if ($ord_no!="") $condition.=" and a.order_no like '%$ord_no'";
    }

    $sql= "select distinct a.id, a.yd_job, a.order_no, a.party_id from yd_ord_mst a, yd_ord_dtls b where a.is_deleted=0 and a.status_active=1 and a.id=b.mst_id $condition order by id";

    // echo $sql;

   // echo create_list_view('list_view', 'Job No,WO No', '140', 500, 300, 0, $sql, 'js_set_value', 'id', '', 1, '0,0,0', $column_arr, 'yd_job,order_no', '', '', '0,0,0');
	?>
	
	 <div style="width:530px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="530" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="140">Job No</th>
                <th>WO No</th>
                
            </thead>
        </table>
        <div style="width:530px; overflow-y:scroll; max-height:300px;" id="buyer_list_view">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="530" class="rpt_table"
            id="tbl_list_search">
            <?

            $i = 1;
            $nameArray = sql_select($sql);
            //var_dump($nameArray);die;
            foreach ($nameArray as $selectResult)
            {
                $job_no = implode(",", array_unique(explode(",", $selectResult[csf("job_no")])));
                $job_no_id = implode(",", array_unique(explode(",", $selectResult[csf("job_no_id")])));
                

                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                    id="search<? echo $i; ?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>+'_'+'<? echo $selectResult[csf('id')]; ?>'+'_'+'<? echo $selectResult[csf('within_group')]; ?>'); ">

                    <td width="30" align="center"><p><? echo $i; ?></p></td>
                    <td width="60" align="center"><p> <? echo $selectResult[csf('yd_job')]; ?></p></td>
                    <td  align="center"><p> <? echo $selectResult[csf('order_no')]; ?></p></td>
                    
                </tr>
                <?
                $i++;
            }
            ?>
        </table>
        </div>
    </div>
	
<?
    unset($sql);
    exit();
}
*/
if($action == 'populate_issue_data_from_search_popup') 
{
	$data=explode('_', $data);
	$search_type = $data[0];
	$mat_mst_id = $data[1];
	
 

	    $sql = "SELECT a.yd_job, b.id, b.style_ref, b.lot, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, b.no_bag, b.cone_per_bag, b.no_cone, b.avg_wgt, b.order_quantity, b.sales_order_id, b.sales_order_no, b.product_id,b.uom,c.cons_quantity from yd_ord_mst a, yd_ord_dtls b, inv_transaction  c,inv_issue_master d
        where a.id='$mat_mst_id' and a.id = b.mst_id  and d.id=c.mst_id  and b.order_id=d.booking_id  and b.product_id=c.prod_id and a.status_active=1 and c.transaction_type = 2 AND c.item_category = 1  and  b.sales_order_no=c.job_no and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";  
	$data_array = sql_select($sql);
    unset($sql);
	$counter = 1;
	foreach ($data_array as $row) 
	{
        $cons_quantity  = $row[csf('cons_quantity')];
  		echo "$('#txtRcvQty_$counter').val($cons_quantity);\n";
 		$counter++;
	}
 
   
           
        
         

    // echo 'location id: '.$data_array[0][csf('location_id')];die;

 /*   echo "document.getElementById('cbo_company_name').value = '".$data_array[0][csf('company_id')]."';\n";
    echo "document.getElementById('cbo_within_group').value = '".$data_array[0][csf('within_group')]."';\n";
    echo "load_drop_down('requires/yd_material_receive_controller', document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer', 'buyer_td' );";
    echo "document.getElementById('cbo_party_name').value = '".$data_array[0][csf('party_id')]."';\n";
    echo "document.getElementById('cbo_location_name').value = '".$data_array[0][csf('location_id')]."';\n";
    echo "document.getElementById('txt_receive_challan').value = '".$data_array[0][csf('chalan_no')]."';\n";
    echo "document.getElementById('txt_receive_no').value = '".$data_array[0][csf('yd_trans_no')]."';\n";
    echo "document.getElementById('txt_receive_date').value = '".change_date_format($data_array[0][csf('receive_date')], "dd-mm-yyyy", "-")."';\n";
    echo "document.getElementById('txt_job_no').value = '".$data_array[0][csf('yd_job')]."';\n";
    echo "document.getElementById('hdn_job_no_id').value = '".$data_array[0][csf('id')]."';\n";
    echo "document.getElementById('hdn_update_id').value = '".$mat_mst_id."';\n";
    echo "document.getElementById('hdn_booking_type_id').value = '".$data_array[0][csf('booking_type')]."';\n";
    echo "document.getElementById('hdn_booking_without_order').value = '".$data_array[0][csf('booking_without_order')]."';\n";*/

    exit();
}

if($action == 'populate_mst_data_from_search_popup') 
{
	$data=explode('_', $data);
	$search_type = $data[0];
	$mat_mst_id = $data[1];
	
	//echo $search_type; die;

	if($search_type == 1) 
	{
		$sql = "select a.id, a.company_id, a.order_no, a.location_id, a.within_group, a.party_id, a.receive_date, a.yd_job, a.booking_type, a.booking_without_order
		from yd_ord_mst a
		where a.id='$mat_mst_id'";
	}
 	else if($search_type == 3) 
	{
		$sql = "select a.id as mat_mst_id, a.yd_trans_no, a.company_id, a.location_id, a.within_group, a.party_id, a.chalan_no, a.receive_date, b.id, b.yd_job, a.booking_type, a.booking_without_order,a.issue_chalan_no,a.challan_id
  				from yd_material_mst a, yd_ord_mst b
 				where a.id = '$mat_mst_id' and a.yd_job_id = b.id";
	}
	// echo $sql;die;
	$data_array = sql_select($sql);
    unset($sql);

    // echo 'location id: '.$data_array[0][csf('location_id')];die;

    echo "document.getElementById('cbo_company_name').value = '".$data_array[0][csf('company_id')]."';\n";
    echo "document.getElementById('cbo_within_group').value = '".$data_array[0][csf('within_group')]."';\n";
    echo "load_drop_down('requires/yd_material_receive_controller', document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer', 'buyer_td' );";
    echo "document.getElementById('cbo_party_name').value = '".$data_array[0][csf('party_id')]."';\n";
    echo "document.getElementById('cbo_location_name').value = '".$data_array[0][csf('location_id')]."';\n";
    echo "document.getElementById('txt_receive_challan').value = '".$data_array[0][csf('chalan_no')]."';\n";
    echo "document.getElementById('txt_receive_no').value = '".$data_array[0][csf('yd_trans_no')]."';\n";
    echo "document.getElementById('txt_receive_date').value = '".change_date_format($data_array[0][csf('receive_date')], "dd-mm-yyyy", "-")."';\n";
    echo "document.getElementById('txt_job_no').value = '".$data_array[0][csf('yd_job')]."';\n";
    echo "document.getElementById('hdn_job_no_id').value = '".$data_array[0][csf('id')]."';\n";
    echo "document.getElementById('hdn_update_id').value = '".$mat_mst_id."';\n";
    echo "document.getElementById('hdn_booking_type_id').value = '".$data_array[0][csf('booking_type')]."';\n";
    echo "document.getElementById('hdn_booking_without_order').value = '".$data_array[0][csf('booking_without_order')]."';\n";
	if($search_type == 3) 
	{
		echo "document.getElementById('txt_issue_no').value = '".$data_array[0][csf('issue_chalan_no')]."';\n";
		echo "document.getElementById('hid_challan_id').value = '".$data_array[0][csf('challan_id')]."';\n";
	}

    exit();
}

if($action == "populate_dtls_data_from_search_popup") 
{
	$counter = 1;
	$data_array_dtls = "";
	$data = explode('_', $data);
	$operationStatus = $data[0];
	$mst_id = $data[1];
	$company = $data[2];
	$issue_id = $data[3];
	$total_rcv_qty = 0;

 
	      $sql =  sql_select("select item_show_in_detail,id from variable_setting_yarn_dyeing where company_name = $company and variable_list =1 and is_deleted = 0 and status_active = 1");
			$variable_yarn_dyeing_Material_Auto_Receive="";
			if(count($sql)>0)
			{
				$variable_yarn_dyeing_Material_Auto_Receive=$sql[0][csf('item_show_in_detail')];
				
				/*if($variable_yarn_dyeing_Material_Auto_Receive==1)
				{
    					 $search_com_cond="and booking_mst_id=$printing_order_id";
 					     $attached_po_sql ="SELECT po_break_down_id from wo_booking_dtls where   status_active =1 and  is_deleted =0  $search_com_cond group by po_break_down_id";   
					     $attached_po_res=sql_select($attached_po_sql); 
						if(count($attached_po_res)<>0)
						{
							$po_break_down_ids='';
							foreach ($attached_po_res as $row)
							{
							   $po_break_down_ids .= $row[csf("po_break_down_id")].',';
							}
					 
						}
						
						$po_break_down_ids=chop($po_break_down_ids,",")  ; 
						
						if($po_break_down_ids!="") $search_com_cond="and d.po_break_down_id in ($po_break_down_ids)";  
 						$disableed="disabled";
   				}*/
			}
			else
			{
				$disableed='';
				
			}



		
	if($operationStatus == 2 || $operationStatus == 3) 
	{
		$sql = "SELECT a.yd_job, b.id, b.style_ref, c.lot, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, b.cone_per_bag,  b.avg_wgt, b.order_quantity, c.receive_qty, c.id AS update_dtls_id, c.sales_order_id, c.sales_order_no, c.product_id,b.uom,b.buyer_buyer,c.no_of_cone as no_cone,c.no_of_bag as no_bag
    	from yd_ord_mst a, yd_ord_dtls b, yd_material_dtls c
    	where c.mst_id='$mst_id' and a.id=b.mst_id and a.id=c.job_id and b.id=c.job_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by b.id";//b.no_bag,b.no_cone,;
	}
	else 
	{
		
				if($variable_yarn_dyeing_Material_Auto_Receive==1)
				{
 		
					$sql = "SELECT a.yd_job, b.id, b.style_ref, b.lot, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, b.no_bag, b.cone_per_bag, b.no_cone, b.avg_wgt, b.order_quantity, b.sales_order_id, b.sales_order_no, b.product_id,b.uom,b.buyer_buyer,sum(c.cons_quantity) as receive_qty  from yd_ord_mst a, yd_ord_dtls b, inv_transaction  c,inv_issue_master d where a.id='$mst_id' and a.id = b.mst_id  and d.id=c.mst_id  and b.order_id=d.booking_id  and b.product_id=c.prod_id and b.yd_color_id=c.dyeing_color_id and a.status_active=1 and c.transaction_type =2 and c.item_category = 1 and d.id=$issue_id  and  b.sales_order_no=c.job_no and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
					group by a.yd_job, b.id, b.style_ref, b.lot, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, b.no_bag, b.cone_per_bag, b.no_cone, b.avg_wgt, b.order_quantity, b.sales_order_id, b.sales_order_no, b.product_id,b.uom ,b.buyer_buyer order by b.id";  
				}
				else
				{   
					
					$sql = "SELECT a.yd_job, b.id, b.style_ref, b.lot, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, b.no_bag, b.cone_per_bag, b.no_cone, b.avg_wgt, b.order_quantity, b.sales_order_id, b.sales_order_no, b.product_id,b.uom,b.buyer_buyer from yd_ord_mst a, yd_ord_dtls b
        where a.id='$mst_id' and a.id = b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id";
					
				}
		
		
	}
	$data_array = sql_select($sql);
  // echo $sql;
    unset($sql);


    $sql1 = "SELECT a.yd_job, b.id, b.order_quantity, c.receive_qty, c.id AS update_dtls_id, c.sales_order_id from yd_ord_mst a, yd_ord_dtls b, yd_material_dtls c where a.id=b.mst_id and a.id=c.job_id and b.id=c.job_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by b.id";

    $receive_array = sql_select($sql1);

    $previous_data_arr = array();

    foreach($receive_array as $data)
    {
    	$previous_data_arr[$data[csf('id')]]['receive_qty'] +=$data[csf('receive_qty')];
    	$previous_data_arr[$data[csf('id')]]['order_quantity'] +=$data[csf('order_quantity')];
    }
	
	
     $count_arr = return_library_array("select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1", 'id', 'yarn_count');
	//$count_arr = return_library_array("select id,construction from lib_yarn_count_determina_mst where is_deleted=0 and status_active=1", 'id', 'construction');
    $comp_arr = return_library_array("select id,composition_name from lib_composition_array where is_deleted=0 and status_active=1", 'id', 'composition_name');
    $color_arr = return_library_array("select id,color_name from lib_color where is_deleted=0 and status_active=1", 'id', 'color_name');
    $yarn_arr = return_library_array("select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1", 'id', 'yarn_count');
	$total_order_quantity=0;
	foreach ($data_array as $row) 
	{
        $total_rcv_qty += $row[csf('receive_qty')];
		$uomId=$row[csf("uom")];
		$total_order_quantity += $row[csf('order_quantity')];

		$balance = $row[csf('order_quantity')] - $previous_data_arr[$row[csf('id')]]['receive_qty']+$row[csf('receive_qty')];
        $total_balance += $balance;

?>
    <tr>
        <td>
            <input id="hdnJobDtlsId_<?php echo $counter; ?>" type="hidden" name="hdnJobDtlsId_<?php echo $counter; ?>" value="<?php echo $row[csf('id')]; ?>" />         
            <input id="hdnItemColor_<?php echo $counter; ?>" type="hidden" name="hdnItemColor_<?php echo $counter; ?>" value="<?php echo $row[csf('yd_color_id')]; ?>" />
            <input id="hdnYarnType_<?php echo $counter; ?>" type="hidden" name="hdnYarnType_<?php echo $counter; ?>" value="<?php echo $row[csf('yarn_type_id')]; ?>" />
            <input id="hdnDtlsId_<?php echo $counter; ?>" type="hidden" name="hdnDtlsId_<?php echo $counter; ?>" value="<?php echo $row[csf('update_dtls_id')]; ?>" />
            <input id="hdnSalesOrdId_<?php echo $counter; ?>" type="hidden" name="hdnSalesOrdId_<?php echo $counter; ?>" value="<?php echo $row[csf('sales_order_id')]; ?>" />
            <input id="hdnSalesOrdNo_<?php echo $counter; ?>" type="hidden" name="hdnSalesOrdNo_<?php echo $counter; ?>" value="<?php echo $row[csf('sales_order_no')]; ?>" />
            <input id="hdnProductId_<?php echo $counter; ?>" type="hidden" name="hdnSalesOrdNo_<?php echo $counter; ?>" value="<?php echo $row[csf('product_id')]; ?>" />

            <input id="txtStyle_<?php echo $counter; ?>" class="text_boxes" type="text" name="txtStyle_<?php echo $counter; ?>" style="width:70px" value="<?php echo $row[csf('style_ref')]; ?>" disabled />
        </td>
        <td>
            <input id="txtSalesOrderNo_<?php echo $counter; ?>" class="text_boxes" type="text" name="txtSalesOrderNo_<?php echo $counter; ?>" style="width:120px" value="<?php echo $row[csf('sales_order_no')]; ?>" disabled />
        </td>
        <td>
            <input id="txtcustbuyer_<?php echo $counter; ?>" class="text_boxes" type="text" name="txtcustbuyer<?php echo $counter; ?>" style="width:120px" value="<?php echo $row[csf('buyer_buyer')]; ?>" disabled />
        </td>
        <td width="100">
            <input id="txtLot_<?php echo $counter; ?>" class="text_boxes" type="text" name="txtLot_<?php echo $counter; ?>" style="width:100px" value="<?php echo $row[csf('lot')]; ?>" />
        </td>

        <td>
            <input id="txtCount_<?php echo $counter; ?>" class="text_boxes" type="text" name="txtCount_<?php echo $counter; ?>" style="width:50px" value="<?php echo $count_arr[$row[csf('count_id')]]; ?>" disabled />
        </td>
        <td>
            <input id="txtYearnType_<?php echo $counter; ?>" class="text_boxes" type="text" name="txtYearnType_<?php echo $counter; ?>" style="width:100px" value="<?php echo $yarn_type[$row[csf('yarn_type_id')]]; ?>" disabled />
        </td>
        <td>
            <input id="txtYarnComp_<?php echo $counter; ?>" class="text_boxes" type="text" name="txtYarnComp_<?php echo $counter; ?>" style="width:150px" value="<?php echo $comp_arr[$row[csf('yarn_composition_id')]]; ?>" disabled />
        </td>
        <td>
            <input id="txtItemColor_<?php echo $counter; ?>" class="text_boxes" type="text" name="txtItemColor_<?php echo $counter; ?>" style="width:80px" value="<?php echo $color_arr[$row[csf('yd_color_id')]]; ?>" disabled />
        </td>
        <td>
            <input id="txtNoOfBag_<?php echo $counter; ?>" class="text_boxes_numeric" type="text" name="txtNoOfBag_<?php echo $counter; ?>" style="width:40px" value="<?php echo $row[csf('no_bag')]; ?>"  />
        </td>
        <td>
            <input id="txtConePerBag_<?php echo $counter; ?>" class="text_boxes_numeric" type="text" name="txtConePerBag_<?php echo $counter; ?>" style="width:40px" value="<?php echo $row[csf('no_cone')]; ?>"  />
        </td>
        <td>
            <input id="txtNoOfCone_<?php echo $counter; ?>" class="text_boxes_numeric" type="text" name="txtNoOfCone_<?php echo $counter; ?>" style="width:50px" value="<?php echo $row[csf('cone_per_bag')]; ?>"  />
        </td>
        <td>
            <input id="txtAvgWtPerCone_<?php echo $counter; ?>" class="text_boxes_numeric" type="text" name="txtAvgWtPerCone_<?php echo $counter; ?>" style="width:70px" value="<?php echo $row[csf('avg_wgt')]; ?>" disabled />
        </td>
        <td> 
			<?php echo create_drop_down( "cboUom_".$counter, 50, $unit_of_measurement,"", 1, "-- Select --",$uomId,"", 1,'','','','','','',"cboUom[]"); ?>
         </td>
        <td>
            <input name="txtRcvQty_<?php echo $counter; ?>" id="txtRcvQty_<?php echo $counter; ?>" class="text_boxes_numeric" type="text" onKeyUp="fnc_total_calculate();check_balance_qnty(this.id);" style="width:60px" value="<?php if(number_format($row[csf('receive_qty')],2,".","")>0){ echo number_format($row[csf('receive_qty')],2,".","");  } ?>" placeholder="<?php echo number_format($balance,2,".",""); ?>" />
            
            <input name="txtorderQty_<?php echo $counter; ?>" id="txtorderQty_<?php echo $counter; ?>" class="text_boxes_numeric" type="hidden"  style="width:50px" value="<?php echo number_format($row[csf('order_quantity')],2,".","");  ; ?>" />
        </td>
    </tr>
<?php
    $counter++;
}
?>
<tfoot>
    <tr class="tbl_bottom" name="tr_btm" id="tr_btm">
        <td colspan="13">Total:</td>
        <td><input name="txtTotRcvQty" id="txtTotRcvQty" class="text_boxes_numeric" type="text" style="width:60px" placeholder="Display" value="<?php echo number_format($total_rcv_qty,2,".",""); ; ?>" disabled />
        
        <input name="txttotalorderquantity" id="txttotalorderquantity" class="text_boxes_numeric" type="hidden" style="width:50px" placeholder="Display" value="<?php echo number_format($total_order_quantity,2,".",""); ; ?>" disabled />
        <input name="txtTotalBalanceQuantity" id="txtTotalBalanceQuantity" class="text_boxes_numeric" type="hidden" value="<?php echo number_format($total_balance,2,".",""); ; ?>" disabled />
        </td>
    </tr>
</tfoot>
<?php
exit();
}

if($action == 'save_update_delete') 
{
	// echo '<pre>';print_r($_POST); exit();
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));

	// $cbo_company_name = trim($cbo_company_name, "'");
	// $cbo_location_name = trim($cbo_location_name, "'");
	// $cbo_within_group = trim($cbo_within_group, "'");
	// $cbo_party_name = trim($cbo_party_name, "'");

	// echo "10**" . $operation;die;

	// save
	if($operation == 0) 
	{
		$flag            = 1;
		$add_comma       = false;
		$data_array_dtls = "";
		$con             = connect();
		$id_mst          = return_next_id('id', 'yd_material_mst', 1);
		$txt_receive_no  = "";
        $entryForm = 387;
		
		if($db_type==0){ $insert_date_con="and YEAR(insert_date)=".date('Y',time()).""; }
		else if($db_type==2){ $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time()).""; }
		
		//echo "10**"."select id,trans_no_prefix,trans_no_prefix_num from yd_material_mst where company_id=$cbo_company_name and trans_type=1 and entry_form=387 $insert_date_con order by id desc"; die;
//echo "10**";
		$new_receive_no = explode("*", return_mrr_number( str_replace("'", "", $cbo_company_name), '', 'YDMR', date("Y",time()), 5, "select id,trans_no_prefix,trans_no_prefix_num from yd_material_mst where company_id=$cbo_company_name and trans_type=1 and entry_form=387 $insert_date_con order by id desc", 'trans_no_prefix', 'trans_no_prefix_num'));
		
		 
		$txt_receive_no = $new_receive_no[0];

		// return_mrr_number($company, $location, $category, $year, $num_length, $main_query, $str_fld_name, $num_fld_name, $old_mrr_no)

		// echo "select id,prefix_no,prefix_no_num from sub_material_mst where company_id=$cbo_company_name and trans_type='1' and entry_form=387 $insert_date_con order by id desc";die;

	// print_r($new_receive_no);die;
		
		if($db_type==0) mysql_query("BEGIN");

		$field_array_mst="id,entry_form,yd_job_id,yd_trans_no,trans_no_prefix,trans_no_prefix_num,company_id,location_id,within_group,party_id,chalan_no,receive_date,receive_quantity,booking_without_order,booking_type,issue_chalan_no,challan_id,inserted_by,insert_date,trans_type";
		$data_array_mst="(".$id_mst.",".$entryForm.",".$hdn_job_no_id.",'".$new_receive_no[0]."','".$new_receive_no[1]."',".$new_receive_no[2].",".$cbo_company_name.",".$cbo_location_name.",".$cbo_within_group.",".$cbo_party_name.",".$txt_receive_challan.",".$txt_receive_date.",".$txtTotRcvQty.",".$hdn_booking_without_order.",".$hdn_booking_type_id.",".$txt_issue_no.",".$hid_challan_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";

		// echo "10**insert into yd_material_mst(".$field_array_mst.") values ".$data_array_mst.""; die;

		$id_dtls = return_next_id('id', 'yd_material_dtls', 1);

		for($i = 1; $i <= $total_row; $i++) {
			$color     = 'hdnItemColor_'.$i;
			$uom       = 'cboUom_'.$i;
			$rcvQty    = 'txtRcvQty_'.$i;
			$jobDtlsId = 'hdnJobDtlsId_'.$i;
			$yarnType  = 'hdnYarnType_'.$i;
			$noOfBag   = 'txtNoOfBag_'.$i;
			$noOfCone   = 'txtConePerBag_'.$i;
			$rcvCone   = 'txtNoOfCone_'.$i;
            $ordId   = 'hdnSalesOrdId_'.$i;
            $ordNo   = 'hdnSalesOrdNo_'.$i;
            $productId   = 'hdnProductId_'.$i;
            $txtLot   = 'txtLot_'.$i;

			$field_array_dtls = "id,mst_id,entry_form,job_id,job_dtls_id,color_id,uom,receive_qty,item_id,no_of_bag,no_of_cone,rec_cone,rec_challan,sales_order_id,sales_order_no,product_id,lot,inserted_by,insert_date";
			 
			$data_array_dtls .= $add_commaa ? ',' : ''; // if $add_comma is true, add a comma in the end of $data_array_dtls

			$data_array_dtls .= "(".$id_dtls.",".$id_mst.",".$entryForm.",".$hdn_job_no_id.",".$$jobDtlsId.",".$$color.",".$$uom.",".$$rcvQty.",".$$yarnType.",".$$noOfBag.",".$$noOfCone.",".$$rcvCone.",".$txt_receive_challan.",".$$ordId.",".$$ordNo.",".$$productId.",".$$txtLot.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			$add_commaa = true;	// first entry is done. add a comma for next entries
			$id_dtls++;	// increment details id by 1
		}


        // echo '10**'.$id_mst;die;

		// echo "10**INSERT INTO yd_material_mst(".$field_array_mst.") VALUES ".$data_array_mst.""; die;
		$rID = sql_insert("yd_material_mst", $field_array_mst, $data_array_mst, 0);
		
		$flag = ($flag && $rID);	// return true if $flag is true and mst table insert is successful

		// echo $flag, $rID;die;
		// echo "10**insert into yd_material_dtls(".$field_array_dtls.") values ".$data_array_dtls.""; die;
		$rID2 = sql_insert('yd_material_dtls', $field_array_dtls, $data_array_dtls, 0);

		$flag = ($flag && $rID2);	// return true if $flag is true and dtls table insert is successful

		if($db_type==0) {
			if($flag) {
				mysql_query("COMMIT");				
				echo "0**".str_replace("'", '', $txt_receive_no)."**".$id_mst."**".str_replace("'",'',$txt_job_no);
			} else {
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'", '', $txt_receive_no)."**".$id_mst."**".str_replace("'",'',$txt_job_no);
			}
		}
		else if($db_type==2) {
			if($flag) {
				oci_commit($con);
				echo "0**".str_replace("'", '', $txt_receive_no)."**".$id_mst."**".str_replace("'",'',$txt_job_no);
			} else {
				oci_rollback($con);
				echo "10**".str_replace("'", '', $txt_receive_no)."**".$id_mst."**".str_replace("'",'',$txt_job_no);
			}
		}

		disconnect($con);
		die;
	}

	// update
	if($operation == 1) {
		$flag = 1;
		$con = connect();

		if($db_type==0) mysql_query("BEGIN");

		$field_array_mst="location_id*chalan_no*receive_date*receive_quantity*issue_chalan_no*challan_id*updated_by*update_date";
		$data_array_mst="".$cbo_location_name."*".$txt_receive_challan."*".$txt_receive_date."*".$txtTotRcvQty."*".$txt_issue_no."*".$hid_challan_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$field_array_dtls = "color_id*uom*receive_qty*item_id*no_of_bag*no_of_cone*rec_cone*rec_challan*lot*updated_by*update_date";

		for($i = 1; $i <= $total_row; $i++) {
			$color     = 'hdnItemColor_'.$i;
			$uom       = 'cboUom_'.$i;
			$rcvQty    = 'txtRcvQty_'.$i;
			$matDtlsId = 'hdnDtlsId_'.$i;
			$yarnType  = 'hdnYarnType_'.$i;
            $noOfBag   = 'txtNoOfBag_'.$i;
			$noOfCone   = 'txtConePerBag_'.$i;
			$rcvCone   = 'txtNoOfCone_'.$i;
			$txtLot   = 'txtLot_'.$i;

			$data_array_dtls[str_replace("'","",$$matDtlsId)]=explode("*",("".$$color."*".$$uom."*".$$rcvQty."*".$$yarnType."*".$$noOfBag."*".$$noOfCone."*".$$rcvCone."*".$txt_receive_challan."*".$$txtLot."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			$id_arr[]=str_replace("'", "", $$matDtlsId);
		}

		// sql_update($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit)
		$rID = sql_update("yd_material_mst", $field_array_mst, $data_array_mst, "id", $hdn_update_id, 0);
		$flag = ($flag && $rID);	// return true if $flag is true and mst table update is successful

		// echo "10**" . bulk_update_sql_statement("yd_material_dtls", "id", $field_array_dtls, $data_array_dtls, $id_arr);die;

		$rID2 = execute_query(bulk_update_sql_statement("yd_material_dtls", "id", $field_array_dtls, $data_array_dtls, $id_arr), 1);

		$flag = ($flag && $rID2);	// return true if $flag is true and dtls table update is successful

		$flag = ($flag && $rID2);	// return true if $flag is true and dtls table insert is successful

		if($db_type==0) {
			if($flag) {
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$hdn_update_id)."**".str_replace("'",'',$txt_job_no);
				// echo "0**".str_replace("'", '', $txt_receive_no)."**".$id_mst."**".str_replace("'",'',$txt_job_no);
			} else {
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$hdn_update_id)."**".str_replace("'",'',$txt_job_no);
			}
		}
		else if($db_type==2) {
			if($flag) {
				oci_commit($con);
				echo "1**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$hdn_update_id)."**".str_replace("'",'',$txt_job_no);
			} else {
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$hdn_update_id)."**".str_replace("'",'',$txt_job_no);
			}
		}

		disconnect($con);
		die;
	}
	else if ($operation==2)   // delete
	{
		$con = connect();
		
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
 		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$data_array_dtls="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		
		$flag=1;
		$rID=sql_update("yd_material_mst",$field_array,$data_array,"id",$hdn_update_id,0); 
		if($rID==1 && $flag==1) $flag=1; else $flag=0; 
		//echo "INSERT INTO sub_material_dtls (".$field_array.") VALUES ".$data_array_dtls; disconnect($con); die;

		$rID1=sql_update("yd_material_dtls",$field_array,$data_array_dtls,"mst_id",$hdn_update_id,1);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;  
			
		if($db_type==0) {
			if($flag) {
				mysql_query("COMMIT");
				echo "2**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$hdn_update_id)."**".str_replace("'",'',$txt_job_no);
				// echo "0**".str_replace("'", '', $txt_receive_no)."**".$id_mst."**".str_replace("'",'',$txt_job_no);
			} else {
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$hdn_update_id)."**".str_replace("'",'',$txt_job_no);
			}
		}
		else if($db_type==2) {
			if($flag) {
				oci_commit($con);
				echo "2**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$hdn_update_id)."**".str_replace("'",'',$txt_job_no);
			} else {
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$hdn_update_id)."**".str_replace("'",'',$txt_job_no);
			}
		}
		disconnect($con); die; 
	}
	
}

if($action=="service_yd_material_receive_print")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$size_arr=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	$comp_arr = return_library_array("select id,composition_name from lib_composition_array where is_deleted=0 and status_active=1", 'id', 'composition_name');
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$count_arr = return_library_array("select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1", 'id', 'yarn_count');
	$dataArray=sql_select( "SELECT id, yd_trans_no, embl_job_no, company_id, receive_quantity, location_id, party_id, receive_date,receive_quantity, chalan_no, within_group from yd_material_mst where id=$data[1] and entry_form=387 and is_deleted=0 and  status_active=1");

	$sql_dtls = "SELECT a.company_id, a.order_no, a.location_id, a.within_group, a.party_id, a.receive_date, a.yd_job, b.id, b.style_ref, b.lot, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, c.NO_OF_BAG as no_bag, b.cone_per_bag, c.NO_OF_CONE as no_cone, b.avg_wgt, b.order_quantity, c.receive_qty, c.id as update_dtls_id, b.sales_order_no, b.uom, b.item_color_id,b.buyer_buyer
	from yd_ord_mst a, yd_ord_dtls b, yd_material_dtls c
	where c.mst_id=$data[1] and a.id=b.mst_id and b.id = c.job_dtls_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";
	$sql_arr=sql_select($sql_dtls);

	$style=$sql_arr[0]["STYLE_REF"];
	$sales_order_no=$sql_arr[0]["SALES_ORDER_NO"];
	$buyer_buyer=$sql_arr[0]["BUYER_BUYER"];
	$yd_job=$sql_arr[0]["YD_JOB"];
	$order_no=$sql_arr[0]["ORDER_NO"];

	if($data[3]==1){
		$party=$company_library[$dataArray[0][csf('party_id')]];
	}else{
		$party=$party_arr[$dataArray[0][csf('party_id')]];
	}

	?> 
    
    <div style="width:1020px; font-size:6px">
        <table width="100%" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="70" align="right" style="display: none;"> 
                    <img  src='../../<? echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='100%' width='100%' />
                </td>
                <td>
                    <table width="800" cellspacing="0" align="center">
                        <tr>
                            <td align="center" style="font-size:20px"><strong ><? echo $company_library[$data[0]]; ?></strong></td>
                        </tr>
                        <tr>
                            <td align="center"  style="font-size:16px"><strong> <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px"><strong><u><? echo $data[2]; ?></u></strong></td>
                        </tr>
						<tr>
                            <td align="center" style="font-size:15px"><strong><u>Receive No: <? echo $dataArray[0]["YD_TRANS_NO"]; ?></u></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>   
          <br>
        <table width="100%" cellpadding="0" cellspacing="0" >  
            <tr>
            	<td width="130"><strong>Challan No</strong></td>
                <td width="175"  align="left">: <? echo $dataArray[0][csf('chalan_no')]; ?></td>
				<td width="150"><strong>Cust. Buyer</strong></td>
                <td width="175"  align="left">: <? echo $buyer_buyer; ?></td>
                <td width="130"><strong>WO No</strong></td>
                <td width="150" align="left">: <? echo $order_no; ?></td>
            </tr>
            <tr>
            	<td><strong>Party </strong></td>
                <td >: <? echo $party; ?></td>
                <td ><strong>Issue Date </strong></td>
                <td >: <? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
            	<td><strong>Job </strong></td>
                <td>: <? echo $yd_job; ?></td>
            </tr>
            <tr>
            	<td width="130"><strong>Style</strong></td>
                <td width="175">: <? echo $style; ?></td>
            	<td><strong>Job/Sales order no </strong></td>
                <td>: <? echo $sales_order_no; ?></td>              
            </tr>
        </table>
        <br>
            <table cellspacing="0" width="1200" border="1" rules="all" class="rpt_table">
                <thead bgcolor="#dddddd" align="center">
                    <th width="30">SL</th>
                    <th width="90">Order</th>
                    <th width="100">Cust Buyer</th>
                    <th width="90">Job/Sales Order</th>
                    <th width="100">Yarn Composition</th>
                    <th width="90">Yarn Type</th>
                    <th width="120">Item Color</th>
                    <th width="70">Color Range</th>
                    <th width="80">Count</th>
                    <th width="80">Raw Yarn Lot</th>
                    <th width="60">No of Bag</th>
                    <th width="60">Cone Per Bag</th>
                    <th width="60">No of Cone</th>
                    <th width="60">UOM</th>
                    <th width="60">AVG. Wt. Per Cone</th>
                    <th width="60">Rceived Qty.</th>
                    <th>Remarks</th>
                </thead>
				<?
				$color_wish_array=array();
				foreach($sql_arr as $row){
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["ORDER_NO"]=$row["ORDER_NO"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["SALES_ORDER_NO"]=$row["SALES_ORDER_NO"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["YARN_COMPOSITION_ID"]=$row["YARN_COMPOSITION_ID"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["YARN_TYPE_ID"]=$row["YARN_TYPE_ID"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["ORDER_QUANTITY"]+=$row["ORDER_QUANTITY"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["RECEIVE_QTY"]+=$row["RECEIVE_QTY"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["AVG_WGT"]=$row["AVG_WGT"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["NO_CONE"]=$row["NO_CONE"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["NO_BAG"]=$row["NO_BAG"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["YD_COLOR_ID"]=$row["YD_COLOR_ID"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["UOM"]=$row["UOM"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["LOT"]=$row["LOT"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["CONE_PER_BAG"]=$row["CONE_PER_BAG"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["COUNT_ID"]=$row["COUNT_ID"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["ITEM_COLOR_ID"]=$row["ITEM_COLOR_ID"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["BUYER_BUYER"]=$row["BUYER_BUYER"];
				}
				unset($sql_arr); 

 				$i=1;$total_qty=0;
				foreach ($color_wish_array as $yd_color_id=> $lot_arr) 
				{			
					foreach($lot_arr as $lot_data=> $count_arrs)
					{	
						foreach($count_arrs as $key=> $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td><? echo $i; ?></td>
								<td style="word-break:break-all"><? echo $row["ORDER_NO"]; ?></td>
								<td style="word-break:break-all"><? echo $row["BUYER_BUYER"] ?></td>
								<td style="word-break:break-all"><? echo $row["SALES_ORDER_NO"]; ?></td>
								<td style="word-break:break-all"><? echo $comp_arr[$row["YARN_COMPOSITION_ID"]]; ?></td>
								<td style="word-break:break-all"><? echo $yarn_type[$row['YARN_TYPE_ID']]; ?></td>
								<td style="word-break:break-all"><? echo $color_arr[$row['YD_COLOR_ID']]; ?>&nbsp;</td>
								<td style="word-break:break-all"><? echo $color_range[$row['ITEM_COLOR_ID']]; ?>&nbsp;</td>
								<td style="word-break:break-all"><? echo $count_arr[$row['COUNT_ID']]; ?>&nbsp;</td>
								<td style="word-break:break-all" align="center"><? echo $row['LOT']; ?>&nbsp;</td>
								<td align="right"><? echo $row['NO_BAG']; ?>&nbsp;</td>
								<td align="right"><? echo $row['CONE_PER_BAG']; ?>&nbsp;</td>
								<td align="right"><? echo $row['NO_CONE']; ?>&nbsp;</td>
								<td align="right"><? echo $unit_of_measurement[$row['UOM']]; ?>&nbsp;</td>
								<td align="right"><? echo $row['AVG_WGT']; ?>&nbsp;</td>
								<td align="right"><? echo number_format($row['RECEIVE_QTY'],2,".",""); ?>&nbsp;</td>
								<td style="word-break:break-all"><? echo $row['remarks']; ?>&nbsp;</td>
							</tr>
							<? $i++;	
							$total_qty+=number_format($row['RECEIVE_QTY'],2,".","");
						}
				    }		
			   }		
				?>
				<tr>
					<td colspan="15" align="right"><b> Total</b></td>
					<td align="right" ><?=number_format($total_qty,2,".","") ?></td>
					<td></td>
				</tr> 
            </table>
            <br>
			<?// echo signature_table(154, $com_id, "1200px"); ?>
    </div>
        <?php
	exit();
}