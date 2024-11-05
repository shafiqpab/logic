<?php
header('Content-type:text/html; charset=utf-8'); 
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header('location:login.php');
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_level=$_SESSION['logic_erp']["user_level"];

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


if ($action=="load_drop_down_location")
{
    $data=explode("_",$data);
    if($data[1]==1) $dropdown_name="cbo_location_name";
    else $dropdown_name="cbo_party_location";
    
    echo create_drop_down( $dropdown_name, 162, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );   
    exit();
}


if($action == "job_search_popup_job")
{
	echo load_html_head_contents('Search Yarn Dyeing Job', '../../../', 1, 0, $unicode);
	extract($_REQUEST);

	?>
	<script>
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('YD Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Style');
			else if(val==4) $('#search_by_td').html('Buyer Job');
		}

		function js_set_value(id)
		{ 
			$("#hidden_mst_id").val(id);
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_<?php echo $tblRow;?>" id="searchorderfrm_<?php echo $tblRow;?>" autocomplete="off">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" style="width: 100%;">
					<thead>
		                <tr>
		                    <th colspan="10"><?php echo create_drop_down('cbo_string_search_type', 163, $string_search_type, '', 1, '-- Searching Type --'); ?></th>
		                </tr>
		                <tr>
		                    <th width="150" class="must_entry_caption" >Company Name</th>
		                    <th width="100" class="must_entry_caption" >Within Group</th>
		                    <th width="162">Party Name</th>
		                    <th width="80">Search By</th>
		                    <th width="80" id="search_by_td">YD Job No</th>
		                    <th width="70">Prod. Type</th>
		                    <th width="70">Order Type</th>
		                    <th width="70">Y/D Type</th>
		                    <th width="160">Order Rcv Date Range</th>
		                    <th>
		                    	<input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width: 100%" />
		                    </th>
		                </tr>
		            </thead>
		            <tbody>
                		<tr class="general">
                			<td>
		                        <?php echo create_drop_down('cbo_company_name', 150, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 1, '-- Select Company --', $selected, "load_drop_down( 'yd_store_receive_controller', this.value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_party', 'party_td' );"); ?>
		                    </td>
		                    <td> 
	                            <?php echo create_drop_down('cbo_within_group', 100, $yes_no, '', 1, '-- Select Within Group --', $selected, "load_drop_down( 'yd_store_receive_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_party', 'party_td' );",0); ?>
	                        </td>
	                        <td id="party_td"> 
	                            <?php echo create_drop_down('cbo_party_name', 162, $blank_array, '', 1, '-- Select Party --', $selected, "",1); ?>
	                        </td>
	                        <td>
	                        	<?
									$search_by_arr=array(1=>"YD Job No",2=>"W/O No",3=>"Buyer Style",4=>"Buyer Job");
									echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
								?>
	                        </td>
	                        <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:80px" placeholder="Write" />
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
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_pro_type').value+'_'+document.getElementById('cbo_order_type').value+'_'+document.getElementById('cbo_yd_type').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value, 'create_job_search_list_view', 'search_div', 'yd_store_receive_controller', 'setFilterGrid(\'tbl_data_list\',-1)')" style="width:70px;" />
                            </td>
                		</tr>
                		<tr>
                            <td colspan="10" align="center" valign="middle">
                                <? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="text_boxes" style="width:70px">
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

if($action=="create_job_search_list_view")
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
		$condition .= " and a.company_id=$cbo_company_name";
	}

	if($cbo_within_group!=0)
	{
		$condition .= " and a.within_group=$cbo_within_group";
	}

	if($cbo_party_name!=0)
	{
		$condition .= " and a.party_id=$cbo_party_name";
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
        if ($txt_date_from!="" &&  $txt_date_to!="") $date_con = "and a.receive_date between'".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'"; else $date_con ="";
    }
    else
    {
        if ($txt_date_from!="" &&  $txt_date_to!="") $date_con = "and a.receive_date between'".change_date_format($txt_date_from, "", "",1)."' and '".change_date_format($txt_date_to, "", "",1)."'"; else $date_con ="";
    }


    if($search_type==1)
    {
        if($search_str!="")
        {
            if($search_by==1) $condition="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $condition="and a.order_no='$search_str'";
			else if ($search_by==3) $condition=" and b.style_ref = '$search_str' ";
			else if ($search_by==4) $condition=" and b.job_no_prefix_num = '$search_str' ";
        }
        
    }
    else if($search_type==2)
    {
        if($search_str!="")
        {
            if($search_by==1) $condition="and a.job_no_prefix_num like '$search_str%'";
			else if($search_by==2) $condition="and a.order_no like '$search_str%'";
			else if ($search_by==3) $condition=" and b.style_ref like  '$search_str%' ";
			else if ($search_by==4) $condition=" and b.sales_order_no like  '$search_str%' ";
        }
        
    }
    else if($search_type==3)
    {
        if($search_str!="")
        {
            if($search_by==1) $condition="and a.job_no_prefix_num like '%$search_str'";
			else if($search_by==2) $condition="and a.order_no like '%$search_str'";
			else if ($search_by==3) $condition=" and b.style_ref like  '%$search_str' ";
			else if ($search_by==4) $condition=" and b.sales_order_no like  '%$search_str' ";
        }
        
    }
    else if($search_type==4 || $search_type==0)
    {
        if($search_str!="")
        {
            if($search_by==1) $condition="and a.job_no_prefix_num like '%$search_str%'";
			else if($search_by==2) $condition="and a.order_no like '%$search_str%'";
			else if ($search_by==3) $condition=" and b.style_ref like  '%$search_str%' ";
			else if ($search_by==4) $condition=" and b.sales_order_no like  '%$search_str%' ";
        }
        
    }


	$sql = "select a.job_no_prefix_num, b.style_ref, a.party_id, a.pro_type, a.within_group, a.yd_job, a.order_no, a.order_id, a.order_type, a.yd_type, a.rec_start_date, a.rec_end_date, b.count_type, b.sales_order_no from yd_ord_mst a, yd_ord_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.check_box_confirm=1 $condition $date_con group by a.job_no_prefix_num, b.style_ref, a.party_id, a.pro_type, a.within_group, a.yd_job, a.order_no, a.order_id, a.order_type, a.yd_type, a.rec_start_date, a.rec_end_date, b.count_type, b.sales_order_no";

	$result = sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="1030" >
		<thead>
            <th width="30">SL</th>
            <th width="100">Party Name</th>
            <th width="60">Prod. Type</th>
            <th width="80">Within Group</th>
            <th width="100">Job No</th>
            <th width="100">WO No</th>
            <th width="80">Buyer Style</th>
            <th width="80">Buyer Job</th>
            <th width="80">Order Type</th>
            <th width="80">YD Type</th>
            <th width="80">Count Type</th>
            <th colspan="2">Order Rcv Date Range</th>
        </thead>
	</table>
	<div style="width:1040px; max-height:370px;overflow-y:scroll;" >
		<table class="rpt_table" border="1" id="tbl_data_list" cellpadding="0" cellspacing="0" rules="all" width="1030" >
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
				<tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $data[csf('yd_job')]; ?>")' style="cursor:pointer">
					<td align="center" width="30"><? echo $i; ?></td>
		            <td align="center" width="100"><? echo $party_name; ?></td>
		            <td align="center" width="60"><? echo $w_pro_type_arr[$data[csf('pro_type')]]; ?></td>
		            <td align="center" width="80"><? echo $yes_no[$data[csf('within_group')]]; ?></td>
		            <td align="center" width="100"><? echo $data[csf('yd_job')]; ?></td>
		            <td align="center" width="100"><? echo $data[csf('order_no')]; ?></td>
		            <td align="center" width="80"><? echo $data[csf('style_ref')]; ?></td>
		            <td align="center" width="80"><? echo $data[csf('sales_order_no')]; ?></td>
		            <td align="center" width="80"><? echo $w_order_type_arr[$data[csf('order_type')]]; ?></td>
		            <td align="center" width="80"><? echo $yarn_type[$data[csf('yd_type')]]; ?></td>
		            <td align="center" width="80"><? echo $count_type_arr[$data[csf('count_type')]]; ?></td>
		            <td align="center" ><? echo $data[csf('rec_start_date')]; ?></td>
		            <td align="center" ><? echo $data[csf('rec_end_date')]; ?></td>
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

if($action=="load_php_yd_job_data_to_form")
{
	$sql = "select a.id, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.order_no, a.pro_type, a.order_type from yd_ord_mst a where a.yd_job='$data' and a.status_active=1 and a.is_deleted=0 and a.check_box_confirm=1";

	$data_array = sql_select($sql);
    unset($sql);

    foreach($data_array as $data)
    {
    	echo "document.getElementById('cbo_company_name').value = '".$data[csf('company_id')]."';\n";

    	echo "load_drop_down( 'requires/yd_store_receive_controller',".$data[csf('company_id')]."+'_'+1, 'load_drop_down_location', 'location_td' );\n";
    	echo "document.getElementById('cbo_location_name').value = '".$data[csf('location_id')]."';\n";
    	echo "$('#cbo_location_name').attr('disabled','disabled');\n";

    	echo "document.getElementById('cbo_within_group').value = '".$data[csf('within_group')]."';\n";

    	echo "load_drop_down( 'requires/yd_store_receive_controller',".$data[csf('company_id')]."+'_'+".$data[csf('within_group')].", 'load_drop_down_party', 'party_td' );\n";
    	echo "document.getElementById('cbo_party_name').value = '".$data[csf('party_id')]."';\n";
    	echo "$('#cbo_party_name').attr('disabled','disabled');\n";

    	echo "load_drop_down( 'requires/yd_store_receive_controller',".$data[csf('party_id')]."+'_'+2, 'load_drop_down_location', 'party_location_td' );\n";
    	echo "document.getElementById('cbo_party_location').value = '".$data[csf('party_location')]."';\n";
    	echo "$('#cbo_party_location').attr('disabled','disabled');\n";

    	echo "document.getElementById('txt_wo_no').value = '".$data[csf('order_no')]."';\n";
    	echo "document.getElementById('cbo_pro_type').value = '".$data[csf('pro_type')]."';\n";
    	echo "document.getElementById('cbo_order_type').value = '".$data[csf('order_type')]."';\n";

    	// $update_id = "'".$data[csf('id')]."'";

    	//echo "show_list_view(".$update_id.",'job_details_list_view','receive_details','requires/yd_store_receive_controller','');\n";
    }
}

if($action=="load_php_yd_receive_data_to_form")
{
	$sql = "select a.id, yd_receive, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.order_no, a.pro_type, a.order_type, a.receive_basis, a.job_no from yd_store_receive_mst a where a.id='$data' and a.status_active=1 and a.is_deleted=0 and a.entry_form=571";

	$data_array = sql_select($sql);
    unset($sql);

    foreach($data_array as $data)
    {

    	echo "document.getElementById('txt_receive_no').value = '".$data[csf('yd_receive')]."';\n";
    	echo "document.getElementById('hdn_update_id').value = '".$data[csf('id')]."';\n";
    	echo "document.getElementById('txt_job_no').value = '".$data[csf('job_no')]."';\n";

    	echo "document.getElementById('cbo_receive_basis').value = '".$data[csf('receive_basis')]."';\n";
    	echo "document.getElementById('cbo_company_name').value = '".$data[csf('company_id')]."';\n";

    	echo "load_drop_down( 'requires/yd_store_receive_controller',".$data[csf('company_id')]."+'_'+1, 'load_drop_down_location', 'location_td' );\n";
    	echo "document.getElementById('cbo_location_name').value = '".$data[csf('location_id')]."';\n";
    	echo "$('#cbo_location_name').attr('disabled','disabled');\n";

    	echo "document.getElementById('cbo_within_group').value = '".$data[csf('within_group')]."';\n";

    	echo "load_drop_down( 'requires/yd_store_receive_controller',".$data[csf('company_id')]."+'_'+".$data[csf('within_group')].", 'load_drop_down_party', 'party_td' );\n";
    	echo "document.getElementById('cbo_party_name').value = '".$data[csf('party_id')]."';\n";
    	echo "$('#cbo_party_name').attr('disabled','disabled');\n";

    	echo "load_drop_down( 'requires/yd_store_receive_controller',".$data[csf('party_id')]."+'_'+2, 'load_drop_down_location', 'party_location_td' );\n";
    	echo "document.getElementById('cbo_party_location').value = '".$data[csf('party_location')]."';\n";
    	echo "$('#cbo_party_location').attr('disabled','disabled');\n";

    	echo "document.getElementById('txt_wo_no').value = '".$data[csf('order_no')]."';\n";
    	echo "document.getElementById('cbo_pro_type').value = '".$data[csf('pro_type')]."';\n";
    	echo "document.getElementById('cbo_order_type').value = '".$data[csf('order_type')]."';\n";

    	// $update_id = "'".$data[csf('id')]."'";

    	//echo "show_list_view(".$update_id.",'job_details_list_view','receive_details','requires/yd_store_receive_controller','');\n";
    }
}

if($action=="job_details_list_view")
{
	$data=explode('_', $data);

	$receiveBasis = $data[1];

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');

	$sql = "select a.id, b.id as dtls_id, b.style_ref, b.sales_order_no, b.sales_order_id, b.buyer_buyer, b.lot, b.count_type, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, b.no_bag, b.cone_per_bag, b.uom, b.order_quantity, b.process_loss, b.adj_type, b.total_order_quantity, b.order_quantity, a.order_type from yd_ord_mst a, yd_ord_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.check_box_confirm=1 and a.yd_job='$data[0]' order by b.id";

	$sql1 = "select b.dtls_id, sum(b.receive_qty) as receive_qty from yd_store_receive_mst a, yd_store_receive_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no='$data[0]' and a.entry_form=571 group by b.dtls_id";

	$result = sql_select($sql);

	$receive_result = sql_select($sql1);

	$receive_array = array();

	foreach($receive_result as $data)
	{
		$receive_array[$data[csf('dtls_id')]]+= $data[csf('receive_qty')];
	}

	$readonly = '';
	if($receiveBasis==21){
		$readonly = "readonly";
	}

	$tblRow = 1;
	foreach($result as $data)
	{

		$receive_qty = $receive_array[$data[csf('dtls_id')]];


		if($data[csf('order_type')]==1)
		{
			$balance = $data[csf('total_order_quantity')]-$receive_qty;
		}
		elseif($data[csf('order_type')]==2)
		{
			$balance = $data[csf('order_quantity')]-$receive_qty;
		}
	?>
        <tr id="row_<?php echo $tblRow;?>">
            <td align="center" width="80">
            	<input style="width: 80px" class="text_boxes" type="text" name="txtstyleRef[]" id="txtstyleRef_<?php echo $tblRow;?>" value="<?php echo $data[csf('style_ref')];?>">
            </td>
            <td width="60">
            	<input style="width: 60px" readonly class="text_boxes" type="text" name="txtsaleOrder[]" id="txtsaleOrder_<?php echo $tblRow;?>" value="<?php echo $data[csf('sales_order_no')];?>">
            	<input  class="text_boxes_numeric" type="hidden" name="txtsaleOrderID[]" id="txtsaleOrderID_<?php echo $tblRow;?>" value="<?php echo $data[csf('sales_order_id')];?>">
            </td>
            <td width="80">
            	<input style="width: 80px" readonly class="text_boxes" type="text" name="buyerBuyer[]" id="buyerBuyer_<?php echo $tblRow;?>" value="<?php echo $data[csf('buyer_buyer')];?>">
            </td>
            <td width="80">
            	<input style="width: 80px" class="text_boxes" type="text" name="txtlot[]" id="txtlot_<?php echo $tblRow;?>" value="<?php //echo $data[csf('lot')];?>">
            </td>
            <td width="40">
            	<input style="width: 40px" <?php echo $readonly; ?> class="text_boxes" type="text" name="txtGrayLot[]" id="txtGrayLot_<?php echo $tblRow;?>" value="<?php echo $data[csf('lot')];?>">
            	<input readonly class="text_boxes" type="hidden" name="txtHiddenGrayLot[]" id="txtHiddenGrayLot_<?php echo $tblRow;?>" value="<?php echo $data[csf('lot')];?>">
            </td>
            <td width="60">
            	<input class="text_boxes" type="hidden" name="txtcountTypeId[]" id="txtcountTypeId_<?php echo $tblRow;?>" value="<?php echo $data[csf('count_type')];?>">
            	<?
                $count_type_arr = array(1 => "Single",2 => "Double");
                echo create_drop_down( "txtcountType_".$tblRow, 60, $count_type_arr,'', 1, '--- Select---', $data[csf('count_type')], "",1,'','','','','','',"txtcountType[]");
                ?>
            </td>
            <td width="60">
            	<input class="text_boxes" type="hidden" name="txtcountId[]" id="txtcountId_<?php echo $tblRow;?>" value="<?php echo $data[csf('count_id')];?>">
            	<?
                   if ($within_group==2) 
                   {
                    	
                    	$sql="select distinct(b.id) as id,b.yarn_count from lib_yarn_count b where b.status_active=1 and b.is_deleted=0";
                   }
                   else
                   {
						
						$sql="select distinct(b.id) as id,b.yarn_count from lib_yarn_count b where b.status_active=1 and b.is_deleted=0";
                   }

                	echo create_drop_down( "cboCount_".$tblRow, 60, $sql,"id,yarn_count", 1, "-- Select --",$data[csf('count_id')],"",1,'','','','','','',"cboCount[]"); 
                ?>
            </td>
            <td width="40">
            	<input class="text_boxes" type="hidden" name="cboYarnTypeId[]" id="cboYarnTypeId_<?php echo $tblRow;?>" value="<?php echo $data[csf('yarn_type_id')];?>">

            	<? echo create_drop_down( "cboYarnType_".$tblRow, 60, $yarn_type,"", 1, "-- Select --",$data[csf('yarn_type_id')],"",1,'','','','','','',"cboYarnType[]"); ?>
            </td>
            <td width="100">
            	<input class="text_boxes" type="hidden" name="txtydCompositionId[]" id="txtydCompositionId_<?php echo $tblRow;?>" value="<?php echo $data[csf('yarn_composition_id')];?>">
            	<? echo create_drop_down( "cboComposition_".$tblRow, 100, $composition,"", 1, "-- Select --",$data[csf('yarn_composition_id')],"",1,'','','','','','',"cboComposition[]"); ?>
            </td>
            <td width="40">
            	<input class="text_boxes" type="hidden" name="txtYarnColorId[]" id="txtYarnColorId_<?php echo $tblRow;?>" value="<?php echo $data[csf('yd_color_id')]; ?>">
            	<? echo create_drop_down( "txtYarnColor_".$tblRow, 80, $color_arr,"", 1, "-- Select --",$data[csf('yd_color_id')],"",1,'','','','','','',"txtYarnColor[]"); ?>
            </td>
            <td width="40">
            	<input style="width: 40px" class="text_boxes_numeric" type="text" name="txtnoBag[]" id="txtnoBag_<?php echo $tblRow;?>" value="<?php echo $data[csf('no_bag')];?>">
            </td>
            <td width="50">
            	<input style="width: 50px" class="text_boxes_numeric" type="text" name="txtConeBag[]" id="txtConeBag_<?php echo $tblRow;?>" value="<?php echo $data[csf('cone_per_bag')];?>">
            </td>
            <td width="40">
            	<input class="text_boxes" type="hidden" name="cboUomId[]" id="cboUomId_<?php echo $tblRow;?>" value="<?php echo $data[csf('uom')];?>">

            	<? echo create_drop_down( "cboUom_".$tblRow, 60, $unit_of_measurement,"", 1, "-- Select --",$data[csf('uom')],"", 1,'','','','','','',"cboUom[]"); ?>
            </td>
            <td width="50">
            	<input style="width: 50px" readonly class="text_boxes_numeric" type="text" name="txtOrderqty[]" id="txtOrderqty_<?php echo $tblRow;?>" value="<?php echo $data[csf('order_quantity')];?>">
            	<input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenOrderqty[]" id="txtHiddenOrderqty_<?php echo $tblRow;?>" value="<?php echo $data[csf('order_quantity')];?>">
            </td>
            <td width="50">
            	<input style="width: 50px" readonly class="text_boxes_numeric" type="text" name="txtProcessLoss[]" id="txtProcessLoss_<?php echo $tblRow;?>" value="<?php echo $data[csf('process_loss')];?>">
            	<input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenProcessLoss[]" id="txtHiddenProcessLoss_<?php echo $tblRow;?>" value="<?php echo $data[csf('process_loss')];?>">
            </td>
            <td width="50">
            	<input readonly class="text_boxes" type="hidden" name="txtadjTypeId[]" id="txtadjTypeId_<?php echo $tblRow;?>" value="<?php echo $data[csf('adj_type')];?>">
            	<?
                	echo create_drop_down( "txtadjType_".$tblRow, 60, $adj_type_arr,'', 1, '--- Select---',$data[csf('adj_type')], "",1,'','','','','','',"txtadjType[]");
                ?>
            </td>
            <td width="50">
            	<input style="width: 50px" readonly class="text_boxes_numeric" type="text" name="txtTotalqty[]" id="txtTotalqty_<?php echo $tblRow;?>" value="<?php echo $data[csf('total_order_quantity')];?>">
            	<input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenTotalqty[]" id="txtHiddenTotalqty_<?php echo $tblRow;?>" value="<?php echo $data[csf('total_order_quantity')];?>">
            </td>
            <td width="50">
            	<input style="width: 50px" readonly class="text_boxes_numeric" type="text" name="txtPreviousReceiveqty[]" id="txtPreviousReceiveqty_<?php echo $tblRow;?>" value="<?php echo $receive_qty;?>">
            	<input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenPreviousReceiveqty[]" id="txtHiddenPreviousReceiveqty_<?php echo $tblRow;?>" value="<?php echo $receive_qty;?>">
            </td>
            <td width="50">
            	<input style="width: 50px" readonly class="text_boxes_numeric" type="text" name="txtbalanceqty[]" id="txtbalanceqty_<?php echo $tblRow;?>" value="<?php echo $balance;?>">
            	<input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenbalanceqty[]" id="txtHiddenbalanceqty_<?php echo $tblRow;?>" value="<?php echo $balance;?>">
            </td>
            <td width="50">
            	<input style="width: 50px" class="text_boxes_numeric" type="text" name="txtReceiveQty[]" id="txtReceiveQty_<?php echo $tblRow;?>" placeholder="<?php echo $balance;?>" onKeyup="calculate_receive_qty(this.id,this.value);">
            	<input class="text_boxes_numeric" type="hidden" name="txtHiddenReceiveId[]" id="txtHiddenReceiveId_<?php echo $tblRow;?>">
            	<input class="text_boxes_numeric" type="hidden" name="txtHiddendtlsId[]" id="txtHiddendtlsId_<?php echo $tblRow;?>" value="<?php echo $data[csf('dtls_id')];?>">
            </td>
        </tr>
    <?php
    	$tblRow++;
	}

	exit();

}

if ($action=="save_update_delete")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    $user_id=$_SESSION['logic_erp']['user_id'];

    if ($operation==0) // Insert Start Here
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }

	    $cbo_receive_basis    		= str_replace("'",'',$cbo_receive_basis);
	    $txt_job_no   				= str_replace("'",'',$txt_job_no);
	    $txt_challan_no   			= str_replace("'",'',$txt_challan_no);
	    $txt_receive_date 			= str_replace("'",'',$txt_receive_date);
	    $cbo_company_name    		= str_replace("'",'',$cbo_company_name);
	    $cbo_location_name    		= str_replace("'",'',$cbo_location_name);
	    $cbo_within_group    		= str_replace("'",'',$cbo_within_group);
	    $cbo_party_name    			= str_replace("'",'',$cbo_party_name);
	    $cbo_party_location   		= str_replace("'",'',$cbo_party_location);
	    $txt_wo_no   				= str_replace("'",'',$txt_wo_no);
	    $cbo_pro_type 				= str_replace("'",'',$cbo_pro_type);
	    $cbo_order_type    			= str_replace("'",'',$cbo_order_type);
	    $hdn_update_id    			= str_replace("'",'',$hdn_update_id);

	    if($db_type==0){
            $txt_receive_date=change_date_format(str_replace("'",'',$txt_receive_date),'yyyy-mm-dd');
        }else{
            $txt_receive_date=change_date_format(str_replace("'",'',$txt_receive_date), "", "",1);
        }

	    if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
	    else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";

	    $new_receive_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'YDSR', date("Y",time()), 5, "select receive_no_prefix,receive_no_prefix_num from yd_store_receive_mst where entry_form=571 and company_id=$cbo_company_name $insert_date_con and status_active=1 and is_deleted=0 order by id desc ", "receive_no_prefix", "receive_no_prefix_num" ));

	    $id=return_next_id("id","yd_store_receive_mst",1);
	    $id1=return_next_id( "id", "yd_store_receive_dtls",1);

	    $field_array="id, entry_form, yd_receive, receive_no_prefix, receive_no_prefix_num, receive_basis, job_no, challan_no, receive_date, company_id, location_id, within_group, party_id, party_location,order_no,pro_type,order_type,inserted_by, insert_date";

	    $data_array="(".$id.", 571, '".$new_receive_no[0]."', '".$new_receive_no[1]."', '".$new_receive_no[2]."', '".$cbo_receive_basis."', '".$txt_job_no."', '".$txt_challan_no."', '".$txt_receive_date."', '".$cbo_company_name."', '".$cbo_location_name."', '".$cbo_within_group."', '".$cbo_party_name."', '".$cbo_party_location."','".$txt_wo_no."','".$cbo_pro_type."', '".$cbo_order_type."',".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."')";

	    $txt_receive_no=$new_receive_no[0];

	    $field_array2="id, mst_id, receive_no_mst, style_ref, sales_order_no, sales_order_id, buyer_buyer, lot, gray_lot, count_type, count_id, yarn_type_id, yarn_composition_id, yd_color_id, no_bag, cone_per_bag, uom, order_quantity, process_loss, adj_type, total_order_quantity, receive_qty,dtls_id, inserted_by, insert_date";

	    $data_array2=""; $add_commaa=0;
	    for($i=1; $i<=$total_row; $i++)
	    {

	    	$txtstyleRef            = "txtstyleRef_".$i;
	        $txtsaleOrder           = "txtsaleOrder_".$i;
	        $txtsaleOrderID         = "txtsaleOrderID_".$i;
	        $buyerBuyer           	= "buyerBuyer_".$i;
	        $txtlot             	= "txtlot_".$i;
	        $txtGrayLot             = "txtGrayLot_".$i;
	        $txtcountTypeId       	= "txtcountTypeId_".$i;
	        $txtcountId         	= "txtcountId_".$i;
	        $cboYarnTypeId         	= "cboYarnTypeId_".$i;
	        $txtydCompositionId     = "txtydCompositionId_".$i;
	        $txtYarnColorId         = "txtYarnColorId_".$i;
	        $txtnoBag         		= "txtnoBag_".$i;
	        $txtConeBag         	= "txtConeBag_".$i;
	        $cboUomId         		= "cboUomId_".$i;
	        $txtOrderqty            = "txtOrderqty_".$i;
	        $txtProcessLoss         = "txtProcessLoss_".$i;          
	        $txtadjTypeId           = "txtadjTypeId_".$i;
	        $txtHiddenTotalqty      = "txtHiddenTotalqty_".$i;
	        $previousReceiveqty     = "previousReceiveqty_".$i;
	        $txtReceiveQty          = "txtReceiveQty_".$i;
	        $txtHiddenReceiveId     = "txtHiddenReceiveId_".$i;
	        $txtHiddendtlsId     	= "txtHiddendtlsId_".$i;

	        if ($add_commaa!=0) $data_array2 .=","; $add_comma=0;

	        $data_array2 .="(".$id1.",".$id.",'".$txt_receive_no."',".$$txtstyleRef.",".$$txtsaleOrder.",".$$txtsaleOrderID.",".$$buyerBuyer.",".$$txtlot.",".$$txtGrayLot.",".$$txtcountTypeId.",".$$txtcountId.",".$$cboYarnTypeId.",".$$txtydCompositionId.",".$$txtYarnColorId.",".$$txtnoBag.",".$$txtConeBag.",".$$cboUomId.",".$$txtOrderqty.",".$$txtProcessLoss.",".$$txtadjTypeId.",".$$txtHiddenTotalqty.",".$$txtReceiveQty.",".$$txtHiddendtlsId.",'".$user_id."','".$pc_date_time."')";

           	$id1=$id1+1; $add_commaa++;

		}

		$flag=true;

		//echo "10**INSERT INTO yd_store_receive_mst (".$field_array.") VALUES ".$data_array; die;
	    $rID=sql_insert("yd_store_receive_mst",$field_array,$data_array,1);

	    if($rID==1) $flag=1; else $flag=0;
        if($flag==1){

        	//echo "10**INSERT INTO yd_store_receive_dtls (".$field_array2.") VALUES ".$data_array2; die;
            $rID2=sql_insert("yd_store_receive_dtls",$field_array2,$data_array2,1);
            if($rID2==1) $flag=1; else $flag=0;
        }

        if($db_type==0){

	        if($flag==1){

	            mysql_query("COMMIT");  
	            echo "0**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
	        }
	        else{

	            mysql_query("ROLLBACK"); 
	            echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
	        }
		}
		else if($db_type==2){

		    if($flag==1){
		        oci_commit($con);
		        echo "0**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
		    }else{
		        oci_rollback($con);
		        echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
		    }
		}
        
        disconnect($con);
        die;
	}
	elseif ($operation==1) // Update Start Here
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }

	    $cbo_receive_basis    		= str_replace("'",'',$cbo_receive_basis);
	    $txt_job_no   				= str_replace("'",'',$txt_job_no);
	    $txt_challan_no   			= str_replace("'",'',$txt_challan_no);
	    $txt_receive_date 			= str_replace("'",'',$txt_receive_date);
	    $cbo_company_name    		= str_replace("'",'',$cbo_company_name);
	    $cbo_location_name    		= str_replace("'",'',$cbo_location_name);
	    $cbo_within_group    		= str_replace("'",'',$cbo_within_group);
	    $cbo_party_name    			= str_replace("'",'',$cbo_party_name);
	    $cbo_party_location   		= str_replace("'",'',$cbo_party_location);
	    $txt_wo_no   				= str_replace("'",'',$txt_wo_no);
	    $cbo_pro_type 				= str_replace("'",'',$cbo_pro_type);
	    $cbo_order_type    			= str_replace("'",'',$cbo_order_type);
	    $hdn_update_id    			= str_replace("'",'',$hdn_update_id);
	   	$txt_receive_no    			= str_replace("'",'',$txt_receive_no);

	    if($db_type==0){
            $txt_receive_date=change_date_format(str_replace("'",'',$txt_receive_date),'yyyy-mm-dd');
        }else{
            $txt_receive_date=change_date_format(str_replace("'",'',$txt_receive_date), "", "",1);
        }

        $field_array="receive_basis*job_no*challan_no*receive_date*company_id*location_id*within_group*party_id*party_location*order_no*pro_type*order_type*updated_by*update_date";

        $data_array="'".$cbo_receive_basis."'*'".$txt_job_no."'*'".$txt_challan_no."'*'".$txt_receive_date."'*'".$cbo_company_name."'*'".$cbo_location_name."'*'".$cbo_within_group."'*'".$cbo_party_name."'*'".$cbo_party_location."'*'".$txt_wo_no."'*'".$cbo_order_type."'*'".$cbo_pro_type."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

        $field_array2="style_ref*sales_order_no*sales_order_id*buyer_buyer*lot*gray_lot*count_type*count_id* yarn_type_id*yarn_composition_id*yd_color_id*no_bag*cone_per_bag*uom*order_quantity*process_loss*adj_type* total_order_quantity*receive_qty*dtls_id*updated_by*update_date";

        $data_array2=array(); $add_commaa=0; $receive_qty=0;$detailsOrderqty =0;


        $current_delivery_sql = "select b.dtls_id, a.yd_receive, sum(b.receive_qty) as receive_qty from yd_store_receive_mst a, yd_store_receive_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no='$txt_job_no' and a.entry_form=295 group by b.dtls_id, a.yd_receive";

        $current_delivery_result = sql_select($current_delivery_sql);

		$current_delivery_arr = array();

		foreach($current_delivery_result as $data)
		{
			$current_delivery_arr[$data[csf('dtls_id')]]['receive_qty']+= $data[csf('receive_qty')];
			$current_delivery_arr[$data[csf('dtls_id')]]['yd_receive'] .= $data[csf('yd_receive')];
		}

        for($i=1; $i<=$total_row; $i++)
	    {
	    	$txtstyleRef            = "txtstyleRef_".$i;
	        $txtsaleOrder           = "txtsaleOrder_".$i;
	        $txtsaleOrderID         = "txtsaleOrderID_".$i;
	        $buyerBuyer           	= "buyerBuyer_".$i;
	        $txtlot             	= "txtlot_".$i;
	        $txtGrayLot             = "txtGrayLot_".$i;
	        $txtcountTypeId       	= "txtcountTypeId_".$i;
	        $txtcountId         	= "txtcountId_".$i;
	        $cboYarnTypeId         	= "cboYarnTypeId_".$i;
	        $txtydCompositionId     = "txtydCompositionId_".$i;
	        $txtYarnColorId         = "txtYarnColorId_".$i;
	        $txtnoBag         		= "txtnoBag_".$i;
	        $txtConeBag         	= "txtConeBag_".$i;
	        $cboUomId         		= "cboUomId_".$i;
	        $txtOrderqty            = "txtOrderqty_".$i;
	        $txtProcessLoss         = "txtProcessLoss_".$i;          
	        $txtadjTypeId           = "txtadjTypeId_".$i;
	        $txtHiddenTotalqty      = "txtHiddenTotalqty_".$i;
	        $previousReceiveqty     = "previousReceiveqty_".$i;
	        $txtReceiveQty          = "txtReceiveQty_".$i;
	        $txtHiddenReceiveId     = "txtHiddenReceiveId_".$i;
	        $txtHiddendtlsId     	= "txtHiddendtlsId_".$i;

	       	$dtlsUpdateId =str_replace("'",'',$$txtHiddenReceiveId);
	       	$receive_qty =str_replace("'",'',$$txtReceiveQty);

	       	$delivery_qty  = $current_delivery_arr[$dtlsUpdateId]['receive_qty'];

	       	if($delivery_qty>$receive_qty)
	       	{
	       		echo "13**Receive quantity Can Not Be Greater Than Delivery Quantity";
	       		die;
	       	}

	        if(str_replace("'",'',$$txtHiddenReceiveId)!="")
            {
            	$data_array2[$dtlsUpdateId]=explode("*",("".$$txtstyleRef."*".$$txtsaleOrder."*".$$txtsaleOrderID."*".$$buyerBuyer."*".$$txtlot."*".$$txtGrayLot."*".$$txtcountTypeId."*".$$txtcountId."*".$$cboYarnTypeId."*".$$txtydCompositionId."*".$$txtYarnColorId."*".$$txtnoBag."*".$$txtConeBag."*".$$cboUomId."*".$$txtOrderqty."*".$$txtProcessLoss."*".$$txtadjTypeId."*".$$txtHiddenTotalqty."*".$$txtReceiveQty."*".$$txtHiddendtlsId."*".$user_id."*'".$pc_date_time."'"));

                $hdn_dtls_id_arr[]=str_replace("'",'',$dtlsUpdateId);
            }
	    }

        $flag=true;
        $rID=sql_update("yd_store_receive_mst",$field_array,$data_array,"id",$hdn_update_id,0);
        if($rID) $flag=1; else $flag=0;

        if($data_array2!="" && $flag==1)
        {
			//echo "10**".bulk_update_sql_statement( "yd_store_receive_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr); die;
            $rID2=execute_query(bulk_update_sql_statement( "yd_store_receive_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr),1);
            if($rID2) $flag=1; else $flag=0;
        }

        if($db_type==0){

	        if($flag==1){

	            mysql_query("COMMIT");  
	            echo "0**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$hdn_update_id)."**".str_replace("'",'',$txt_job_no);
	        }
	        else{

	            mysql_query("ROLLBACK"); 
	            echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$hdn_update_id)."**".str_replace("'",'',$txt_job_no);
	        }
		}
		else if($db_type==2){

		    if($flag==1){
		        oci_commit($con);
		        echo "0**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$hdn_update_id)."**".str_replace("'",'',$txt_job_no);
		    }else{
		        oci_rollback($con);
		        echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$hdn_update_id)."**".str_replace("'",'',$txt_job_no);
		    }
		}
        
        disconnect($con);
        die;
    }
    elseif ($operation==2) // Update Start Here
    {
    	$con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");  
        }


        $update_id    = str_replace("'",'',$hdn_update_id);
        $txt_job_no   				= str_replace("'",'',$txt_job_no);;
	   	$txt_receive_no    			= str_replace("'",'',$txt_receive_no);

	   	$current_delivery_sql = "select b.dtls_id, a.yd_receive, sum(b.receive_qty) as receive_qty from yd_store_receive_mst a, yd_store_receive_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no='$txt_job_no' and a.entry_form=295 group by b.dtls_id, a.yd_receive";

        $current_delivery_result = sql_select($current_delivery_sql);

		$current_delivery_arr = array();

		foreach($current_delivery_result as $data)
		{
			$current_delivery_arr[$data[csf('dtls_id')]]['receive_qty']+= $data[csf('receive_qty')];
			$current_delivery_arr[$data[csf('dtls_id')]]['yd_receive'] .= $data[csf('yd_receive')];
		}

		for($i=1; $i<=$total_row; $i++)
	    {

	        $txtReceiveQty          = "txtReceiveQty_".$i;
	        $txtHiddenReceiveId     = "txtHiddenReceiveId_".$i;

	       	$dtlsUpdateId =str_replace("'",'',$$txtHiddenReceiveId);
	       	$receive_qty =str_replace("'",'',$$txtReceiveQty);

	       	$delivery_qty  = $current_delivery_arr[$dtlsUpdateId]['receive_qty'];

	       	if($delivery_qty>0)
	       	{
	       		echo "13**Update Or Delete Not Allowed. Delivery Found.";
	       		die;
	       	}
	    }

        $flag=0;
        $field_array="status_active*is_deleted*updated_by*update_date";
        $data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
        $rID=sql_update("yd_store_receive_mst",$field_array,$data_array,"id",$update_id,0);

        if($rID) $flag=1; else $flag=0; 
        
        if($flag==1)
        {
            $rID1=sql_update("yd_store_receive_dtls",$field_array,$data_array,"mst_id",$update_id,1);
            if($rID1) $flag=1; else $flag=0; 
        }   
        
        if($db_type==0)
        {
            if($flag==1)
            {
                mysql_query("COMMIT");  
                echo "2**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);
            }
            else
            {
                mysql_query("ROLLBACK"); 
                echo "10**";
            }
        }
        else if($db_type==2)
        {
            if($rID)
            {
                oci_commit($con);
                echo "2**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);
            }
            else
            {
                oci_rollback($con);
                echo "10**";
            }
        }
        disconnect($con);
        die; 
    }
}

if($action=="receive_dtls_list_view")
{
	$data=explode('_', $data);

	$receive_id = $data[0];
	$receive_no = $data[1];
	$receiveBasis = $data[2];
	$job_no = $data[3];

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');

	$sql = "select a.id, b.id as receive_dtls_id, b.style_ref, b.sales_order_no, b.sales_order_id, b.buyer_buyer, b.lot, b.gray_lot, b.count_type, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, b.no_bag, b.cone_per_bag, b.uom, b.order_quantity, b.process_loss, b.adj_type, b.total_order_quantity, b.receive_qty, b.dtls_id, a.order_type from yd_store_receive_mst a, yd_store_receive_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=571 and a.id=$receive_id";

	$sql1 = "select b.dtls_id, sum(b.receive_qty) as receive_qty from yd_store_receive_mst a, yd_store_receive_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no='$job_no' and a.entry_form=571 group by b.dtls_id";

	$result = sql_select($sql);

	$receive_result = sql_select($sql1);

	$receive_array = array();

	foreach($receive_result as $data)
	{
		$receive_array[$data[csf('dtls_id')]]+= $data[csf('receive_qty')];
	}

	$readonly = '';
	if($receiveBasis==21){
		$readonly = "readonly";
	}

	$tblRow = 1;
	foreach($result as $data)
	{

		$receive_qty = $receive_array[$data[csf('dtls_id')]]-$data[csf('receive_qty')];

		if($data[csf('order_type')]==1)
		{
			$balance = $data[csf('total_order_quantity')]-$receive_qty;
		}
		elseif($data[csf('order_type')]==2)
		{
			$balance = $data[csf('order_quantity')]-$receive_qty;
		}
	?>
        <tr id="row_<?php echo $tblRow;?>">
            <td align="center" width="80">
            	<input style="width: 80px" class="text_boxes" type="text" name="txtstyleRef[]" id="txtstyleRef_<?php echo $tblRow;?>" value="<?php echo $data[csf('style_ref')];?>">
            </td>
            <td width="60">
            	<input style="width: 60px" readonly class="text_boxes" type="text" name="txtsaleOrder[]" id="txtsaleOrder_<?php echo $tblRow;?>" value="<?php echo $data[csf('sales_order_no')];?>">
            	<input  class="text_boxes_numeric" type="hidden" name="txtsaleOrderID[]" id="txtsaleOrderID_<?php echo $tblRow;?>" value="<?php echo $data[csf('sales_order_id')];?>">
            </td>
            <td width="60">
            	<input style="width: 60px" readonly class="text_boxes" type="text" name="buyerBuyer[]" id="buyerBuyer_<?php echo $tblRow;?>" value="<?php echo $data[csf('buyer_buyer')];?>">
            </td>
            <td width="80">
            	<input style="width: 80px" class="text_boxes" type="text" name="txtlot[]" id="txtlot_<?php echo $tblRow;?>" value="<?php echo $data[csf('lot')];?>">
            </td>
            <td width="80">
            	<input style="width: 80px" <?php echo $readonly; ?> class="text_boxes" type="text" name="txtGrayLot[]" id="txtGrayLot_<?php echo $tblRow;?>" value="<?php echo $data[csf('gray_lot')];?>">
            	<input readonly class="text_boxes" type="hidden" name="txtHiddenGrayLot[]" id="txtHiddenGrayLot_<?php echo $tblRow;?>" value="<?php echo $data[csf('gray_lot')];?>">
            </td>
            <td width="60">
            	<input style="width: 60px" class="text_boxes" type="hidden" name="txtcountTypeId[]" id="txtcountTypeId_<?php echo $tblRow;?>" value="<?php echo $data[csf('count_type')];?>">
            	<?
                $count_type_arr = array(1 => "Single",2 => "Double");
                echo create_drop_down( "txtcountType_".$tblRow, 60, $count_type_arr,'', 1, '--- Select---', $data[csf('count_type')], "",1,'','','','','','',"txtcountType[]");
                ?>
            </td>
            <td width="60">
            	<input class="text_boxes" type="hidden" name="txtcountId[]" id="txtcountId_<?php echo $tblRow;?>" value="<?php echo $data[csf('count_id')];?>">
            	<?
                   if ($within_group==2) 
                   {
                    	
                    	$sql="select distinct(b.id) as id,b.yarn_count from lib_yarn_count b where b.status_active=1 and b.is_deleted=0";
                   }
                   else
                   {
						
						$sql="select distinct(b.id) as id,b.yarn_count from lib_yarn_count b where b.status_active=1 and b.is_deleted=0";
                   }

                	echo create_drop_down( "cboCount_".$tblRow, 60, $sql,"id,yarn_count", 1, "-- Select --",$data[csf('count_id')],"",1,'','','','','','',"cboCount[]"); 
                ?>
            </td>
            <td width="40">
            	<input class="text_boxes" type="hidden" name="cboYarnTypeId[]" id="cboYarnTypeId_<?php echo $tblRow;?>" value="<?php echo $data[csf('yarn_type_id')];?>">

            	<? echo create_drop_down( "cboYarnType_".$tblRow, 60, $yarn_type,"", 1, "-- Select --",$data[csf('yarn_type_id')],"",1,'','','','','','',"cboYarnType[]"); ?>
            </td>
            <td width="100">
            	<input class="text_boxes" type="hidden" name="txtydCompositionId[]" id="txtydCompositionId_<?php echo $tblRow;?>" value="<?php echo $data[csf('yarn_composition_id')];?>">
            	<? echo create_drop_down( "cboComposition_".$tblRow, 100, $composition,"", 1, "-- Select --",$data[csf('yarn_composition_id')],"",1,'','','','','','',"cboComposition[]"); ?>
            </td>
            <td width="40">
            	<input class="text_boxes" type="hidden" name="txtYarnColorId[]" id="txtYarnColorId_<?php echo $tblRow;?>" value="<?php echo $data[csf('yd_color_id')]; ?>">
            	<? echo create_drop_down( "txtYarnColor_".$tblRow, 60, $color_arr,"", 1, "-- Select --",$data[csf('yd_color_id')],"",1,'','','','','','',"txtYarnColor[]"); ?>
            </td>
            <td width="40">
            	<input style="width: 40px" class="text_boxes_numeric" type="text" name="txtnoBag[]" id="txtnoBag_<?php echo $tblRow;?>" value="<?php echo $data[csf('no_bag')];?>">
            </td>
            <td width="50">
            	<input style="width: 50px" class="text_boxes_numeric" type="text" name="txtConeBag[]" id="txtConeBag_<?php echo $tblRow;?>" value="<?php echo $data[csf('cone_per_bag')];?>">
            </td>
            <td width="50">
            	<input class="text_boxes" type="hidden" name="cboUomId[]" id="cboUomId_<?php echo $tblRow;?>" value="<?php echo $data[csf('uom')];?>">

            	<? echo create_drop_down( "cboUom_".$tblRow, 50, $unit_of_measurement,"", 1, "-- Select --",$data[csf('uom')],"", 1,'','','','','','',"cboUom[]"); ?>
            </td>
            <td width="50">
            	<input style="width: 50px" readonly class="text_boxes_numeric" type="text" name="txtOrderqty[]" id="txtOrderqty_<?php echo $tblRow;?>" value="<?php echo $data[csf('order_quantity')];?>">
            	<input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenOrderqty[]" id="txtHiddenOrderqty_<?php echo $tblRow;?>" value="<?php echo $data[csf('order_quantity')];?>">
            </td>
            <td width="50">
            	<input style="width: 50px" readonly class="text_boxes_numeric" type="text" name="txtProcessLoss[]" id="txtProcessLoss_<?php echo $tblRow;?>" value="<?php echo $data[csf('process_loss')];?>">
            	<input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenProcessLoss[]" id="txtHiddenProcessLoss_<?php echo $tblRow;?>" value="<?php echo $data[csf('process_loss')];?>">
            </td>
            <td width="50">
            	<input readonly class="text_boxes" type="hidden" name="txtadjTypeId[]" id="txtadjTypeId_<?php echo $tblRow;?>" value="<?php echo $data[csf('adj_type')];?>">
            	<?
                	echo create_drop_down( "txtadjType_".$tblRow, 50, $adj_type_arr,'', 1, '--- Select---',$data[csf('adj_type')], "",1,'','','','','','',"txtadjType[]");
                ?>
            </td>
            <td width="50">
            	<input style="width: 50px" readonly class="text_boxes_numeric" type="text" name="txtTotalqty[]" id="txtTotalqty_<?php echo $tblRow;?>" value="<?php echo $data[csf('total_order_quantity')];?>">
            	<input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenTotalqty[]" id="txtHiddenTotalqty_<?php echo $tblRow;?>" value="<?php echo $data[csf('total_order_quantity')];?>">
            </td>
            <td width="50">
            	<input style="width: 50px" readonly class="text_boxes_numeric" type="text" name="txtPreviousReceiveqty[]" id="txtPreviousReceiveqty_<?php echo $tblRow;?>" value="<?php echo $receive_qty;?>">
            	<input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenPreviousReceiveqty[]" id="txtHiddenPreviousReceiveqty_<?php echo $tblRow;?>" value="<?php echo $receive_qty;?>">
            </td>
            <td width="50">
            	<input style="width: 50px" readonly class="text_boxes_numeric" type="text" name="txtbalanceqty[]" id="txtbalanceqty_<?php echo $tblRow;?>" value="<?php echo $balance;?>">
            	<input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenbalanceqty[]" id="txtHiddenbalanceqty_<?php echo $tblRow;?>" value="<?php echo $balance;?>">
            </td>
            <td width="50">
            	<input style="width: 50px" class="text_boxes_numeric" type="text" onKeyup="calculate_receive_qty(this.id,this.value);" name="txtReceiveQty[]" placeholder="<?php echo $balance;?>" id="txtReceiveQty_<?php echo $tblRow;?>" value="<?php echo $data[csf('receive_qty')];?>">
            	<input class="text_boxes_numeric" type="hidden" name="txtHiddenReceiveId[]" id="txtHiddenReceiveId_<?php echo $tblRow;?>" value="<?php echo $data[csf('receive_dtls_id')];?>">
            	<input class="text_boxes_numeric" type="hidden" name="txtHiddendtlsId[]" id="txtHiddendtlsId_<?php echo $tblRow;?>" value="<?php echo $data[csf('dtls_id')];?>">
            </td>
        </tr>
    <?php
    	$tblRow++;
	}

	exit();

}

if($action == "job_search_popup_rcv")
{
	echo load_html_head_contents('Search Yarn Dyeing Job', '../../../', 1, 0, $unicode);
	extract($_REQUEST);

	?>
	<script>
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('YD Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Style');
			else if(val==4) $('#search_by_td').html('Buyer Job');
		}

		function js_set_value(id,job_no)
		{ 
			$("#hidden_mst_id").val(id);
			$("#hidden_job_no").val(job_no);
			parent.emailwindow.hide();
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
		                    </th>
		                </tr>
		            </thead>
		            <tbody>
                		<tr class="general">
                			<td>
		                        <?php echo create_drop_down('cbo_company_name', 150, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 1, '-- Select Company --', $selected, "load_drop_down( 'yd_store_receive_controller', this.value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_party', 'party_td' );"); ?>
		                    </td>
		                    <td> 
	                            <?php echo create_drop_down('cbo_within_group', 80, $yes_no, '', 1, '-- Select Within Group --', $selected, "load_drop_down( 'yd_store_receive_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_party', 'party_td' );",0); ?>
	                        </td>
	                        <td id="party_td"> 
	                            <?php echo create_drop_down('cbo_party_name', 162, $blank_array, '', 1, '-- Select Party --', $selected, "",1); ?>
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
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_pro_type').value+'_'+document.getElementById('cbo_order_type').value+'_'+document.getElementById('cbo_yd_type').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_receive_no').value, 'create_receive_search_list_view', 'search_div', 'yd_store_receive_controller', 'setFilterGrid(\'tbl_data_list\',-1)')" style="width:70px;" />
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
		$condition .= " and a.company_id=$cbo_company_name";
	}

	if($txt_receive_no!=0)
	{
		$condition .= " and a.receive_no_prefix_num=$txt_receive_no";
	}

	if($cbo_within_group!=0)
	{
		$condition .= " and a.within_group=$cbo_within_group";
	}

	if($cbo_party_name!=0)
	{
		$condition .= " and a.party_id=$cbo_party_name";
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
        if ($txt_date_from!="" &&  $txt_date_to!="") $date_con = "and a.receive_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'"; else $date_con ="";
    }
    else
    {
        if ($txt_date_from!="" &&  $txt_date_to!="") $date_con = "and a.receive_date between '".change_date_format($txt_date_from, "", "",1)."' and '".change_date_format($txt_date_to, "", "",1)."'"; else $date_con ="";
    }


    if($search_type==1)
    {
        if($search_str!="")
        {
            if($search_by==1) $condition="and a.job_no='$search_str'";
			else if($search_by==2) $condition="and a.order_no='$search_str'";
			else if ($search_by==3) $condition=" and b.style_ref = '$search_str' ";
			else if ($search_by==4) $condition=" and b.sales_order_no = '$search_str' ";
        }
        
    }
    else if($search_type==2)
    {
        if($search_str!="")
        {
            if($search_by==1) $condition="and a.job_no like '$search_str%'";
			else if($search_by==2) $condition="and a.order_no like '$search_str%'";
			else if ($search_by==3) $condition=" and b.style_ref like  '$search_str%' ";
			else if ($search_by==4) $condition=" and b.sales_order_no like  '$search_str%' ";
        }
        
    }
    else if($search_type==3)
    {
        if($search_str!="")
        {
            if($search_by==1) $condition="and a.job_no like '%$search_str'";
			else if($search_by==2) $condition="and a.order_no like '%$search_str'";
			else if ($search_by==3) $condition=" and b.style_ref like  '%$search_str' ";
			else if ($search_by==4) $condition=" and b.sales_order_no like  '%$search_str' ";
        }
        
    }
    else if($search_type==4 || $search_type==0)
    {
        if($search_str!="")
        {
            if($search_by==1) $condition="and a.job_no like '%$search_str%'";
			else if($search_by==2) $condition="and a.order_no like '%$search_str%'";
			else if ($search_by==3) $condition=" and b.style_ref like  '%$search_str%' ";
			else if ($search_by==4) $condition=" and b.sales_order_no like  '%$search_str%' ";
        }
        
    }


	$sql = "select a.yd_receive, a.id, b.style_ref, a.party_id, a.pro_type, a.within_group, a.job_no, a.order_no, a.order_id, a.order_type, a.receive_date, b.count_type, b.sales_order_no from yd_store_receive_mst a, yd_store_receive_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=571 $condition $date_con group by a.yd_receive, a.id, b.style_ref, a.party_id, a.pro_type, a.within_group, a.job_no, a.order_no, a.order_id, a.order_type, a.receive_date, b.count_type, b.sales_order_no order by a.id desc";

	$result = sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="1095" >
		<thead>
            <th width="30">SL</th>
            <th width="100">Party Name</th>
            <th width="60">Prod. Type</th>
            <th width="80">Within Group</th>
            <th width="100">Receive No</th>
            <th width="100">Job No</th>
            <th width="100">WO No</th>
            <th width="80">Buyer Style</th>
            <th width="80">Buyer Job</th>
            <th width="80">Order Type</th>
            <th width="80">Count Type</th>
            <th width="100">Receive Date</th>
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
				<tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value(<? echo $data[csf('id')]; ?>,"<? echo $data[csf('job_no')]; ?>")' style="cursor:pointer">
					<td align="center" width="30"><? echo $i; ?></td>
		            <td align="center" width="100"><? echo $party_name; ?></td>
		            <td align="center" width="60"><? echo $w_pro_type_arr[$data[csf('pro_type')]]; ?></td>
		            <td align="center" width="80"><? echo $yes_no[$data[csf('within_group')]]; ?></td>
		            <td align="center" width="100"><? echo $data[csf('yd_receive')]; ?></td>
		            <td align="center" width="100"><? echo $data[csf('job_no')]; ?></td>
		            <td align="center" width="100"><? echo $data[csf('order_no')]; ?></td>
		            <td align="center" width="80"><? echo $data[csf('style_ref')]; ?></td>
		            <td align="center" width="80"><? echo $data[csf('sales_order_no')]; ?></td>
		            <td align="center" width="80"><? echo $w_order_type_arr[$data[csf('order_type')]]; ?></td>
		            <td align="center" width="80"><? echo $count_type_arr[$data[csf('count_type')]]; ?></td>
		            <td align="center" width="100"><? echo $data[csf('receive_date')]; ?></td>
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