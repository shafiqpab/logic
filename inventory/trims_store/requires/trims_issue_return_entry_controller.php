<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, supplier_id, company_location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$supplier_id = $userCredential[0][csf('supplier_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$company_location_id = $userCredential[0][csf('company_location_id')];

if ($company_id !='') {
    $company_credential_cond = " and comp.id in($company_id)";
}
if ($store_location_id !='') {
    $store_location_credential_cond = " and a.id in($store_location_id)"; 
}

 //-------------------START ----------------------------------------



if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/trims_issue_return_entry_controller",$data);
}

if ($action=="upto_variable_settings")
{
	extract($_REQUEST);
	echo $variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$cbo_company_id' and item_category_id=4 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	exit();
}


if($action=="varible_inventory")
{
	$sql_variable_inventory=sql_select("select id, independent_controll, rate_optional, is_editable, rate_edit  from variable_settings_inventory where company_name=$data and variable_list=20 and status_active=1 and menu_page_id=73");
	if(count($sql_variable_inventory)>0)
	{
		echo "1**".$sql_variable_inventory[0][csf("independent_controll")]."**".$sql_variable_inventory[0][csf("rate_optional")]."**".$sql_variable_inventory[0][csf("is_editable")]."**".$sql_variable_inventory[0][csf("rate_edit")];
	}
	else
	{
		echo "0**".$sql_variable_inventory[0][csf("independent_controll")]."**".$sql_variable_inventory[0][csf("rate_optional")]."**".$sql_variable_inventory[0][csf("is_editable")]."**".$sql_variable_inventory[0][csf("rate_edit")];
	}
	/*$variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name=$data and item_category_id=8 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	echo "**".$variable_inventory;
	$variable_lot=return_field_value("auto_transfer_rcv","variable_settings_inventory","company_name=$data and variable_list=32 and status_active=1 and is_deleted=0");
	echo "**".$variable_lot;*/
	die;
}

if ($action=="trims_issue_popup_search")
{
	echo load_html_head_contents("Trims Issue Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
	
		function js_set_value(data)
		{
			$('#hidden_data').val(data);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:780px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:775px; margin-left:5px">
		<legend>Enter search words</legend>           
            <table cellpadding="2" cellspacing="0" width="770" class="rpt_table" rules="all" border="1">
                <thead>
                    <th>Store</th>
                    <th>Issue Date Range</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="180">Enter Issue ID</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                    	<input type="hidden" name="hidden_data" id="hidden_data" class="text_boxes" value="">  
                    </th> 
                </thead>
                <tr>
                    <td align="center">
                    	<?
							echo create_drop_down( "cbo_store_name", 130, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$cbo_company_id' and b.category_type=4 and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- All store --", 0, "" );
						?>       
                    </td>
                    <td align="center">
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To
					  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					</td>
                    <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Issue ID",2=>"Challan No");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";							
							echo create_drop_down( "cbo_search_by", 120, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                    </td>     
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_store_name').value, 'create_trims_issue_search_list_view', 'search_div', 'trims_issue_return_entry_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
           <div style="margin-top:8px;" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_trims_issue_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$store_id =$data[5];
	
	if($store_id==0) $store_name="%%"; else $store_name=$store_id;
	
	$trims_issue_basis=array(1=>"With Order",2=>"Without Order");
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.issue_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.issue_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	
	if(trim($data[0])!="")
	{
		if($search_by==1)	
			$search_field_cond="and a.issue_number like '$search_string'";
		else	
			$search_field_cond="and a.challan_no like '$search_string'";
	}
	else
	{
		$search_field_cond="";
	}
	
	if($db_type==0) $year_field="YEAR(a.insert_date)"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY')";
	else $year_field="";//defined Later
	
	$sql = "select a.id, a.issue_number_prefix_num, $year_field as year, a.issue_number, a.challan_no, a.store_id,b.floor_id,b.room,b.rack_no,b.shelf_no,b.bin, a.issue_date, a.booking_no, a.issue_basis from inv_issue_master a,inv_trims_issue_dtls b where a.id=b.mst_id and a.entry_form=25 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and a.store_id like '$store_name' $search_field_cond $date_cond order by a.id"; 

	$store_arr = return_library_array("select id, store_name from lib_store_location","id","store_name");
	$arr=array(2=>$trims_issue_basis,5=>$store_arr);
	
	echo create_list_view("list_view", "Issue ID,Year,Issue Basis,Booking No.,Challan No,Store,Issue date", "70,70,120,130,100,120","770","240",0, $sql, "js_set_value", "id,issue_number,issue_basis,store_id,floor_id,room,rack_no,shelf_no,bin", "", 1, "0,0,issue_basis,0,0,store_id,0", $arr, "issue_number_prefix_num,year,issue_basis,booking_no,challan_no,store_id,issue_date", "",'','0,0,0,0,0,0,3');
	
	exit();
}
if ($action=="populate_load_room_rack_self_bin") {
	$data=explode("**", $data);
	$sql = sql_select("select a.id,a.company_id, a.issue_number, a.store_id,b.floor_id,b.room,b.rack_no,b.shelf_no,b.bin from inv_issue_master a,inv_trims_issue_dtls b where a.id=$data[0] and a.id=b.mst_id and a.entry_form=25 and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[1] order by a.id"); 

	$variable_inventory_sql=sql_select("select store_method, rack_balance from variable_settings_inventory  where company_name=$data[1] and item_category_id=4 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$store_method=$variable_inventory_sql[0][csf("store_method")];
	
	foreach ($sql as $row)
	{ 
		echo "document.getElementById('store_update_upto').value 			= '".$store_method."';\n";
		echo "load_room_rack_self_bin('requires/trims_issue_return_entry_controller*4', 'store','store_td', '".$row[csf('company_id')]."','"."',this.value);\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("store_id")]."';\n";
		echo "load_room_rack_self_bin('requires/trims_issue_return_entry_controller', 'floor','floor_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."',this.value);\n";
		echo "$('#cbo_floor').val('".$row[csf("floor_id")]."');\n";
		echo "load_room_rack_self_bin('requires/trims_issue_return_entry_controller', 'room','room_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."',this.value);\n";
		echo "$('#cbo_room').val('".$row[csf("room")]."');\n";
		echo "load_room_rack_self_bin('requires/trims_issue_return_entry_controller', 'rack','rack_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		echo "$('#txt_rack').val('".$row[csf("rack_no")]."');\n";
		echo "load_room_rack_self_bin('requires/trims_issue_return_entry_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack_no')]."',this.value);\n";	
		echo "$('#txt_shelf').val('".$row[csf("shelf_no")]."');\n";
		echo "load_room_rack_self_bin('requires/trims_issue_return_entry_controller', 'bin','bin_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack_no')]."','".$row[csf('shelf_no')]."',this.value);\n";	
		echo "$('#cbo_bin').val('".$row[csf("bin")]."');\n";
		exit();
	}
}

if($action=="create_itemDesc_search_list_view")
{
	$po_arr = return_library_array("select id, po_number from wo_po_break_down","id","po_number");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$trim_group_arr =array();
	$data_array=sql_select("select id, item_name, trim_uom, conversion_factor from lib_item_group where item_category=4");
	foreach($data_array as $row)
	{
		$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
		$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('trim_uom')];
		$trim_group_arr[$row[csf('id')]]['conversion_factor']=$row[csf('conversion_factor')];
	}
	unset($data_array);
	
	$sql="select a.prod_id, a.item_group_id, a.item_description, a.item_color_id, a.item_size, a.rack_no, a.shelf_no, a.order_id, a.issue_qnty 
	from  inv_trims_issue_dtls a, product_details_master b 
	where a.prod_id=b.id and mst_id='$data' and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 order by prod_id";
	//echo $sql;
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="490" class="rpt_table">
		<thead>
			<th width="40">Prod. ID</th>
			<th width="70">Item Group</th>
			<th width="100">Item Desc.</th>               
			<th width="60">Item Color</th>
			<th width="50">Item Size</th>
			<th width="50">Rack</th>
            <th width="50">Shelf</th>
			<th>Issue Qty.</th>
		</thead>
		<?
		$i=1;
		foreach ($result as $row)
		{  
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
			$order_id=explode(",",$row[csf('order_id')]); $po_no='';
			foreach($order_id as $po_id)
			{
				$po_no.=$po_arr[$po_id].",";
			}
			$po_no=chop($po_no,',');
			$data=$row[csf('prod_id')]."**".$row[csf('item_group_id')]."**".$row[csf('item_description')]."**".$color_arr[$row[csf('item_color_id')]]."**".$row[csf('item_size')]."**".$row[csf('rack_no')]."**".$row[csf('shelf_no')]."**".$row[csf('issue_qnty')]."**".$row[csf('order_id')]."**".$po_no."**".$row[csf('supplier_id')]."**".$trim_group_arr[$row[csf('item_group_id')]]['conversion_factor']; 
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick='set_form_data("<? echo $data ?>")' > 
				<td width="40"><? echo $row[csf('prod_id')]; ?></td>
				<td width="70"><p><? echo $trim_group_arr[$row[csf('item_group_id')]]['name']; ?></p></td>  
				<td width="100"><p><? echo $row[csf('item_description')]; ?></p></td>             
				<td width="60"><p><? echo $color_arr[$row[csf('item_color_id')]]; ?>&nbsp;</p></td>
				<td width="50"><p><? echo $row[csf('item_size')]; ?>&nbsp;</p></td>
				<td width="50"><p><? echo $row[csf('rack_no')]; ?>&nbsp;</p></td>
				<td width="50"><p><? echo $row[csf('shelf_no')]; ?>&nbsp;</p></td>
				<td align="right"><? echo number_format($row[csf('issue_qnty')],2,'.',''); ?></td>
			</tr>
		<?
		$i++;
		}
		?>
	</table>
<?	
exit();
}

if ($action=="get_trim_return_cum_info")
{
	$data=explode("**",$data);
	$issue_id=$data[0];
	$prod_id=$data[1];
	$rack=$data[2];
	$shelf=$data[3];
	$issue_qnty=$data[4];
	
	if($rack!="" || $db_type==0)
	{
		$rack_cond="rack='$rack'";
	}
	else $rack_cond="rack is null";
	
	$null_cond='';
	if($db_type==2)
	{
		$null_cond="NVL";
	}
	else
	{
		$null_cond="IFNULL";
	}
	
	if($shelf=="") $shelf=0;

	$productData=sql_select("select unit_of_measure, avg_rate_per_unit from product_details_master where id=$prod_id and status_active=1 and is_deleted=0");
	$cumulative_returned=return_field_value("sum(cons_quantity) as return_qty","inv_transaction","issue_id=$issue_id and item_category=4 and transaction_type=4 and $null_cond(self,0)=$shelf and prod_id='$prod_id' and $rack_cond and status_active=1 and is_deleted=0","return_qty");
    $net_used = $issue_qnty-$cumulative_returned;
	
    echo "$('#cbo_uom').val(".$productData[0][csf('unit_of_measure')].");\n";
	echo "$('#txt_rate').val(".$productData[0][csf('avg_rate_per_unit')].");\n";
    echo "$('#txt_cumulative_returned').val('".$cumulative_returned."');\n";
    echo "$('#txt_net_used').val('".$net_used."');\n";
	exit();
}

if($action=="po_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo "test";die;
	?> 
	<script>
		function distribute_qnty(str)
		{
			if(str==1)
			{
				var tot_po_qnty=$('#tot_po_qnty').val()*1;
				var txt_prop_issue_qnty=$('#txt_prop_issue_qnty').val()*1;
				var tblRow = $("#tbl_list_search tbody tr").length;
				var len=totalIssue=0;
				//alert(str);
				if(txt_prop_issue_qnty>0)
				{
					$("#tbl_list_search tbody").find('tr').each(function()
					{
						len=len+1;
						
						var po_qnty=$(this).find('input[name="txtPoQnty[]"]').val()*1;
						var perc=(po_qnty/tot_po_qnty)*100;
						
						var issue_qnty=(perc*txt_prop_issue_qnty)/100;
						
						totalIssue = totalIssue*1+issue_qnty*1;
						totalIssue = totalIssue.toFixed(2);						
						if(tblRow==len)
						{
							var balance = txt_prop_issue_qnty-totalIssue;
							if(balance!=0) issue_qnty=issue_qnty+(balance);							
						}
						
						$(this).find('input[name="txtIssueQnty[]"]').val(issue_qnty.toFixed(2));
	
					});
				}
			}
			else
			{
				$('#txt_prop_issue_qnty').val('');
				$("#tbl_list_search").find('tr').each(function()
				{
					$(this).find('input[name="txtIssueQnty[]"]').val('');
				});
			}
			
			calculate_total();
		}
		
		function calculate_total()
		{
			var tblRow = $("#tbl_list_search tbody tr").length;
			var total_issue=0;
			for(var i=1;i<=tblRow;i++)
			{
				var issue_qnty=$('#txtIssueQnty_'+i).val()*1;
				total_issue=total_issue*1+issue_qnty;
			}
			
			$('#total_issue').html(total_issue);
		}
		
		function fnc_close()
		{
			var save_string='';	 var tot_trims_qnty=''; var po_id_array = new Array(); var po_no='';
			var conversion_factor=$('#conversion_faction').val()*1;
			$("#tbl_list_search").find('tbody tr').each(function()
			{
				var txtPoId=$(this).find('input[name="txtPoId[]"]').val();
				var txtPoName=$(this).find('input[name="txtPoName[]"]').val();
				var txtIssueQnty=(($(this).find('input[name="txtIssueQnty[]"]').val()*1)/conversion_factor).toFixed(2);
				//alert(conversion_factor);
				tot_trims_qnty=(tot_trims_qnty*1+$(this).find('input[name="txtIssueQnty[]"]').val()*1).toFixed(2);
				
				if(txtIssueQnty*1>0)
				{
					if(save_string=="")
					{
						save_string=txtPoId+"_"+txtIssueQnty;
					}
					else
					{
						save_string+=","+txtPoId+"_"+txtIssueQnty;
					}
					
					if(jQuery.inArray( txtPoId, po_id_array) == -1 ) 
					{
						po_id_array.push(txtPoId);
						if(po_no=="") po_no=txtPoName; else po_no+=","+txtPoName;
					}
				}
			});
			
			$('#save_string').val( save_string );
			$('#tot_trims_qnty').val( tot_trims_qnty );
			$('#all_po_id').val( po_id_array );
			$('#all_po_no').val( po_no );
			$('#distribution_method').val( $('#cbo_distribiution_method').val() );	
			
			parent.emailwindow.hide();
		}
    </script>

</head>

<body>
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:620px;margin-left:10px">
            <input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
            <input type="hidden" name="tot_trims_qnty" id="tot_trims_qnty" class="text_boxes" value="">
            <input type="hidden" name="all_po_id" id="all_po_id" class="text_boxes" value="">
            <input type="hidden" name="all_po_no" id="all_po_no" class="text_boxes" value="">
            <input type="hidden" name="distribution_method" id="distribution_method" class="text_boxes" value="">
            <input type="hidden" name="conversion_faction" id="conversion_faction" class="text_boxes" value="<? echo $conversion_faction; ?>">
            <div style="width:600px; margin-top:10px" align="center">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300" align="center">
                    <thead>
                        <th>Total Issue Qnty</th>
                        <th>Distribution Method</th>
                    </thead>
                    <tr class="general">
                        <td><input type="text" name="txt_prop_issue_qnty" id="txt_prop_issue_qnty" class="text_boxes_numeric" value="<? echo $returnQnty; ?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)" /></td>
                        <td>
                            <?
                                $distribiution_method=array(1=>"Proportionately",2=>"Manually");
                                echo create_drop_down( "cbo_distribiution_method", 160, $distribiution_method,"",0,"--Select--",$prev_method,"distribute_qnty(this.value);",0 );
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
			<div style="margin-left:30px; margin-top:10px">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="530">
                    <thead>
                        <th width="150">PO No</th>
                        <th width="100">Shipment Date</th>
                        <th width="120">PO Qnty</th>
                        <th>Return Qnty</th>
                    </thead>
                </table>
                <div style="width:550px; max-height:280px; overflow-y:scroll" id="list_container" align="left">
                    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="530" id="tbl_list_search">
                        <tbody>
                        <?
                        $i=1; $tot_issue_qnty=''; $po_array=array();
                        $explSaveData = explode(",",$save_data); 	
                        for($z=0;$z<count($explSaveData);$z++)
                        {
                            $po_wise_data = explode("_",$explSaveData[$z]);
                            $order_id=$po_wise_data[0];
                            $issue_qnty=$po_wise_data[1]*$conversion_faction;
                            
                            $po_array[$order_id]=$issue_qnty;
                        }
						//echo $all_po_id."=test";
                        if($all_po_id!="")
                        {
                            $po_sql="select b.id, a.buyer_name, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date 
							from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details c 
							where a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and c.trans_type=2 and c.entry_form=25 and b.id in ($all_po_id) and c.status_active=1 and c.is_deleted=0 and c.prod_id=$prod_id
							group by b.id, a.buyer_name, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date";
                        }
                        //echo $po_sql;
                        $nameArray=sql_select($po_sql);
                        foreach($nameArray as $row)
                        {
                            if ($i%2==0)
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                                
                            $issue_qnty=$po_array[$row[csf('id')]];
                            $tot_issue_qnty+=$issue_qnty;
							$po_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
							$tot_po_qnty+=$po_qnty_in_pcs;
                         ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="150">
                                    <p><? echo $row[csf('po_number')]; ?></p>
                                    <input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
                                    <input type="hidden" name="txtPoName[]" id="txtPoName_<? echo $i; ?>" value="<? echo $row[csf('po_number')]; ?>">
                                </td>
                                <td align="center" width="100"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                                <td width="120" align="right">
                                    <? echo $po_qnty_in_pcs; ?>&nbsp;
                                    <input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_qnty_in_pcs; ?>">
                                </td>
                                <td align="right">
                                    <input type="text" name="txtIssueQnty[]" id="txtIssueQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:140px" onKeyUp="calculate_total();" value="<? echo number_format($issue_qnty,2,'.',''); ?>">
                                </td>
                            </tr>
                        <?
                        $i++;
                        }
                        ?>
                        	<input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes" value="<? echo number_format($tot_po_qnty,2,'.',''); ?>">
                        </tbody>
                        <tfoot class="tbl_bottom">
                            <td colspan="3">Total</td>
                            <td id="total_issue"><? echo number_format($tot_issue_qnty,2,'.',''); ?></td>
                        </tfoot>
                    </table>
                </div>
                <table width="580">
                     <tr>
                        <td align="center" >
                            <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
	</form>
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
	
	$trim_group_arr =array();
	$data_array=sql_select("select id, item_name, trim_uom, conversion_factor from lib_item_group where item_category=4");
	foreach($data_array as $row)
	{
		$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
		$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('trim_uom')];
		$trim_group_arr[$row[csf('id')]]['conversion_factor']=$row[csf('conversion_factor')];
	}
	unset($data_array);
	//$txt_rate=return_field_value("avg_rate_per_unit as avg_rate_per_unit", "product_details_master", "id=$hidden_prod_id and status_active=1 and is_deleted=0", "avg_rate_per_unit");
	

	if($operation == 1) $is_update_cond = " and id <> $update_trans_id"; else $is_update_cond="";
	if($operation!=2) 
	{
		$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_prod_id and store_id=$cbo_store_name $is_update_cond and status_active = 1 and is_deleted = 0", "max_date");      
		if($max_issue_date !="")
		{
			$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
			$return_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_return_date)));
	
			if ($return_date < $max_issue_date) 
			{
				echo "20**Return Date Can not Be Less Than Last Transaction Date Of This Lot";
				die;
			}
		}
	}
	
	if($operation!=0) 
	{
		$max_recv_id = return_field_value("max(id) as max_id", "inv_transaction", "prod_id=$hidden_prod_id and store_id=$cbo_store_name and transaction_type in (2,3,6) and status_active=1 and is_deleted=0", "max_id");      
		if($max_recv_id != "" && str_replace("'", "", $update_trans_id)>0)
		{
			if ($max_recv_id > str_replace("'", "", $update_trans_id)) 
			{
				echo "20**Next Transaction Found, Update Or Delete Not Allow";die;
			}
		}
	}
	
	$store_update_upto=str_replace("'","",$store_update_upto);
	if($store_update_upto > 1)
	{
		$cbo_floor=str_replace("'","",$cbo_floor);
		$cbo_room=str_replace("'","",$cbo_room);
		$txt_rack=str_replace("'","",$txt_rack);
		$txt_shelf=str_replace("'","",$txt_shelf);
		$cbo_bin=str_replace("'","",$cbo_bin);
		if($store_update_upto==2)
		{
			$cbo_room=0;
			$txt_rack=0;
			$txt_shelf=0;
			$cbo_bin=0;
		}
		else if($store_update_upto==3)
		{
			$txt_rack=0;
			$txt_shelf=0;
			$cbo_bin=0;
		}
		else if($store_update_upto==4)
		{
			$txt_shelf=0;
			$cbo_bin=0;
		}
		else if($store_update_upto==5)
		{
			$cbo_bin=0;
		}
	}
	else
	{
		$cbo_floor=0;
		$cbo_room=0;
		$txt_rack=0;
		$txt_shelf=0;
		$cbo_bin=0;
	}
	
	$up_conds="";
	if(str_replace("'","",$update_trans_id)>0) $up_conds=" and b.trans_id<>$update_trans_id";
	$store_order_sql=sql_select("select B.TRANS_TYPE, B.QUANTITY, B.ORDER_AMOUNT, C.ID AS TRANS_ID, C.TRANSACTION_TYPE, c.CONS_QUANTITY, c.CONS_AMOUNT
	from order_wise_pro_details b, inv_transaction c  
	where b.trans_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.entry_form in(24,25,49,73,78,112) and b.po_breakdown_id in (".str_replace("'","",$all_po_id).") and c.store_id=$cbo_store_name and b.prod_id=$hidden_prod_id $up_conds");
	$ord_bal_qnty=$ord_bal_amt=$cons_bal_qnty=$cons_bal_amt=0;
	foreach($store_order_sql as $val)
	{
		if($val["TRANS_TYPE"]==1 || $val["TRANS_TYPE"]==4 || $val["TRANS_TYPE"]==5)
		{
			$ord_bal_qnty+=$val["QUANTITY"];
			$ord_bal_amt+=$val["ORDER_AMOUNT"];
		}
		else
		{
			$ord_bal_qnty-=$val["QUANTITY"];
			$ord_bal_amt-=$val["ORDER_AMOUNT"];
		}
		
		if($trns_id_check[$val["TRANS_ID"]]=="")
		{
			$trns_id_check[$val["TRANS_ID"]]=$val["TRANS_ID"];
			if($val["TRANSACTION_TYPE"]==1 || $val["TRANSACTION_TYPE"]==4 || $val["TRANSACTION_TYPE"]==5)
			{
				$cons_bal_qnty+=$val["CONS_QUANTITY"];
				$cons_bal_amt+=$val["CONS_AMOUNT"];
			}
			else
			{
				$cons_bal_qnty-=$val["CONS_QUANTITY"];
				$cons_bal_amt-=$val["CONS_AMOUNT"];
			}
		}
	}
	
	
	$trim_ord_rate=$trim_ord_cons_rate=0;
	if($cons_bal_qnty!=0)
	{

		if($ord_bal_amt!=0 && $ord_bal_qnty!=0) $trim_ord_rate=$ord_bal_amt/$ord_bal_qnty;
		if($cons_bal_amt!=0 && $cons_bal_qnty!=0) $trim_ord_cons_rate=$cons_bal_amt/$cons_bal_qnty;

		// $trim_ord_rate=$ord_bal_amt/$ord_bal_qnty;
		// $trim_ord_cons_rate=$cons_bal_amt/$cons_bal_qnty;
	}
	else
	{
		$store_order_sql=sql_select("select C.ID AS TRANS_ID, C.TRANSACTION_TYPE, c.CONS_QUANTITY, c.CONS_AMOUNT, sum(B.QUANTITY) as QUANTITY, sum(B.ORDER_AMOUNT) as ORDER_AMOUNT
		from order_wise_pro_details b, inv_transaction c  
		where b.trans_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.entry_form in(25,78,112) and b.po_breakdown_id in (".str_replace("'","",$all_po_id).") and c.store_id=$cbo_store_name and b.prod_id=$hidden_prod_id and c.mst_id=$issue_id and c.transaction_type in(2,5,6)
		group by C.ID, C.TRANSACTION_TYPE, c.CONS_QUANTITY, c.CONS_AMOUNT");
		$trim_ord_rate=$store_order_sql[0]["ORDER_AMOUNT"]/$store_order_sql[0]["QUANTITY"];
		$trim_ord_cons_rate=$store_order_sql[0]["CONS_AMOUNT"]/$store_order_sql[0]["CONS_QUANTITY"];
	}
	
	$txt_amount=str_replace("'","",$txt_return_qnty)*$trim_ord_cons_rate;
	
	if ($operation==0)  // Insert Here cbo_store_name
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		//	if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		
		$issue_qnty=return_field_value("sum(cons_quantity) as issue_qnty","inv_transaction"," mst_id=$issue_id and prod_id=$hidden_prod_id and item_category=4 and transaction_type=2 and status_active=1","issue_qnty");
		$prev_issue_rtn_qnty=return_field_value("sum(cons_quantity) as issue_qnty","inv_transaction"," issue_id=$issue_id and prod_id=$hidden_prod_id and item_category=4 and transaction_type=4 and status_active=1","issue_qnty");
		$cu_issue_qnty=$issue_qnty-$prev_issue_rtn_qnty;
		if(str_replace("'","",$txt_return_qnty)>$cu_issue_qnty)
		{
			echo "30**Return Quantity Not Allow More Then Issue Qnty";disconnect($con);die;
		}
		
		$trims_recv_num=''; $trims_update_id='';
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
			$new_trims_recv_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,$cbo_company_id,'TIR',73,date("Y",time()) ));
		 	
			$field_array="id,recv_number_prefix,recv_number_prefix_num,recv_number,entry_form,item_category,company_id,receive_basis,receive_date,challan_no,store_id,floor,room,rack,shelf,bin,issue_id,inserted_by,insert_date";
			$data_array="(".$id.",'".$new_trims_recv_system_id[1]."',".$new_trims_recv_system_id[2].",'".$new_trims_recv_system_id[0]."',73,4,".$cbo_company_id.",".$cbo_basis.",".$txt_return_date.",".$txt_challan_no.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$issue_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$trims_recv_num=$new_trims_recv_system_id[0];
			$trims_update_id=$id;
		}
		else
		{
			$field_array_update="store_id*floor*room*rack*shelf*bin*receive_date*challan_no*receive_basis*issue_id*updated_by*update_date";
			$data_array_update=$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$txt_return_date."*".$txt_challan_no."*".$cbo_basis."*".$issue_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$trims_recv_num=str_replace("'","",$txt_system_id);
			$trims_update_id=str_replace("'","",$update_id);
		}
		
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$field_array_trans="id, mst_id, company_id, store_id,floor_id,room,rack,self,bin_box, prod_id, item_category, transaction_type, transaction_date, order_uom, order_qnty, order_rate, order_amount, cons_uom, cons_quantity, cons_rate, cons_amount, balance_qnty, balance_amount, issue_id, issue_challan_no, inserted_by, insert_date";
		$data_array_trans="(".$id_trans.",".$trims_update_id.",".$cbo_company_id.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$hidden_prod_id.",4,4,".$txt_return_date.",".$cbo_uom.",".$txt_return_qnty.",".$trim_ord_cons_rate.",".$txt_amount.",".$cbo_uom.",".$txt_return_qnty.",".$trim_ord_cons_rate.",".$txt_amount.",".$txt_return_qnty.",".$txt_amount.",".$issue_id.",".$txt_challan_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		$prodData = sql_select("select current_stock,avg_rate_per_unit from product_details_master where id=$hidden_prod_id and status_active=1 and is_deleted=0");
		$currentStock   = $prodData[0][csf('current_stock')]+str_replace("'","",$txt_return_qnty);
		$stockValue	 	= 0;
		if ($currentStock != 0){
			$stockValue	 	= $currentStock*$prodData[0][csf('avg_rate_per_unit')];			
		}

		$field_array_prod= "last_purchased_qnty*current_stock*stock_value*updated_by*update_date"; 
		$data_array_prod=$txt_return_qnty."*".$currentStock."*".$stockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		

		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, order_rate, order_amount, inserted_by, insert_date";
		
		$save_data=explode(",",str_replace("'","",$save_data));
		for($i=0;$i<count($save_data);$i++)
		{
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$order_dtls=explode("_",$save_data[$i]);
			
			$order_id=$order_dtls[0];
			$return_qnty=$order_dtls[1];
			$order_amount=$return_qnty*$trim_ord_rate;
			
			if($data_array_prop!="") $data_array_prop.=",";
			$data_array_prop.="(".$id_prop.",".$id_trans.",4,73,0,".$order_id.",".$hidden_prod_id.",".$return_qnty.",'".$trim_ord_rate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$all_order_id.=$order_id.",";
		}
		
		
		$rID=$rID2=$prodUpdate=$rID3=true;
		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_receive_master",$field_array,$data_array,0);
		}
		else
		{
			$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,1);
		}

		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		$prodUpdate= sql_update("product_details_master",$field_array_prod,$data_array_prod,"id",$hidden_prod_id,1); 
		$ordProdUpdate=true;
		if((str_replace("'","",$cbo_basis)==1 || str_replace("'","",$cbo_basis)==3) && $data_array_prop!="")
		{
			if($data_array_prop!="")
			{
				$rID3=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			}
		}
		
		//echo "10**$rID=$rID2=$prodUpdate=$rID3".$flag;oci_rollback($con);die;
		if($db_type==0)
		{
			if($rID && $rID2 && $prodUpdate && $rID3)
			{
				mysql_query("COMMIT");  
				echo "0**".$trims_update_id."**".$trims_recv_num."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $prodUpdate && $rID3)
			{
				oci_commit($con); 
				echo "0**".$trims_update_id."**".$trims_recv_num."**0";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$issue_qnty=return_field_value("sum(cons_quantity) as issue_qnty","inv_transaction"," mst_id=$issue_id and prod_id=$hidden_prod_id and item_category=4 and transaction_type=2 and status_active=1","issue_qnty");
		$prev_issue_rtn_qnty=return_field_value("sum(cons_quantity) as issue_qnty","inv_transaction"," issue_id=$issue_id and prod_id=$hidden_prod_id and item_category=4 and transaction_type=4 and status_active=1 and id<>$update_trans_id","issue_qnty");
		
		$cu_issue_qnty=$issue_qnty-$prev_issue_rtn_qnty;
		
		if(str_replace("'","",$txt_return_qnty)>$cu_issue_qnty)
		{
			echo "30**Return Quantity Not Allow More Then Issue Qnty";disconnect($con);die;
		}
		
		$field_array_update="store_id*floor*room*rack*Shelf*bin*receive_date*challan_no*receive_basis*issue_id*updated_by*update_date";
		$data_array_update=$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$txt_return_date."*".$txt_challan_no."*".$cbo_basis."*".$issue_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		//product master table information
		$sql = sql_select("select avg_rate_per_unit,current_stock,stock_value from product_details_master where id=$hidden_prod_id");
		$avg_rate=$stock_qnty=$stock_value=0;
		foreach($sql as $result)
		{
			$avg_rate = $result[csf("avg_rate_per_unit")];
			$stock_qnty = $result[csf("current_stock")];
			$stock_value = $result[csf("stock_value")];
		}

		$field_array_trans_update="prod_id*store_id*floor_id*room*rack*self*bin_box*transaction_date*order_uom*order_qnty*order_rate*order_amount*cons_uom*cons_quantity*cons_rate*cons_amount*balance_qnty*balance_amount*issue_id*issue_challan_no*updated_by*update_date";
		$data_array_trans_update=$hidden_prod_id."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$txt_return_date."*".$cbo_uom."*".$txt_return_qnty."*".$trim_ord_cons_rate."*".$txt_amount."*".$cbo_uom."*".$txt_return_qnty."*".$trim_ord_cons_rate."*".$txt_amount."*".$txt_return_qnty."*".$txt_amount."*".$issue_id."*".$txt_challan_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		if(str_replace("'","",$previous_prod_id)==str_replace("'","",$hidden_prod_id))
		{
			$currentStock   = $stock_qnty+str_replace("'", '',$txt_return_qnty)-str_replace("'", '',$hidden_return_qnty);
			$stockValue=0;
			if ($currentStock != 0){
				$stockValue	 	= $currentStock*$avg_rate;
				$avgRate	 	= number_format($avg_rate,$dec_place[3],'.',''); 				
			}

			$field_array_prod= "last_purchased_qnty*current_stock*stock_value*updated_by*update_date"; 
			$data_array_prod=$txt_return_qnty."*".$currentStock."*".$stockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			
		}
		else
		{
			$stock=sql_select("select current_stock, avg_rate_per_unit from product_details_master where id=$previous_prod_id and status_active=1 and is_deleted=0");
			$adjust_curr_stock=$stock[0][csf('current_stock')]-str_replace("'", '',$hidden_return_qnty);
			$adjust_rate=$stock[0][csf('avg_rate_per_unit')];
			$adjust_value=$adjust_curr_stock*$adjust_rate;

			$currentStock   = $stock_qnty+str_replace("'", '',$txt_return_qnty);
			$stockValue	 	= 0;
			if ($currentStock != 0){
				$stockValue	 	= $currentStock*$avg_rate;				
			} 

			$field_array_prod= "last_purchased_qnty*current_stock*stock_value*updated_by*update_date"; 
			$data_array_prod=$txt_return_qnty."*".$currentStock."*".$stockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		}
		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, order_rate, order_amount, inserted_by, insert_date";
		$save_data=explode(",",str_replace("'","",$save_data));
		for($i=0;$i<count($save_data);$i++)
		{
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$order_dtls=explode("_",$save_data[$i]);
			
			$order_id=$order_dtls[0];
			$return_qnty=$order_dtls[1];
			$order_amount=$return_qnty*$trim_ord_rate;
			
			if($data_array_prop!="") $data_array_prop.=",";
			$data_array_prop.="(".$id_prop.",".$update_trans_id.",4,73,0,".$order_id.",".$hidden_prod_id.",".$return_qnty.",'".$trim_ord_rate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}
		
		
		$rID=$rID2=$adjust_prod=$prodUpdate=$delete_prop=$rID4=true;
		$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,1);
		$rID2=sql_update("inv_transaction",$field_array_trans_update,$data_array_trans_update,"id",$update_trans_id,0);
		if(str_replace("'","",$previous_prod_id)==str_replace("'","",$hidden_prod_id))
		{
			$prodUpdate= sql_update("product_details_master",$field_array_prod,$data_array_prod,"id",$hidden_prod_id,1);
		}
		else
		{
			$adjust_prod=sql_update("product_details_master","current_stock*stock_value",$adjust_curr_stock."*".$adjust_value,"id",$previous_prod_id,0);
			$prodUpdate= sql_update("product_details_master",$field_array_prod,$data_array_prod,"id",$hidden_prod_id,1);
		}
		//echo "10**".$flag;die;
		$delete_prop=execute_query( "delete from order_wise_pro_details where trans_id=$update_trans_id and entry_form=73",0);
		//echo "10**insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;	
		if((str_replace("'","",$cbo_basis)==1 || str_replace("'","",$cbo_basis)==3) && $data_array_prop!="")
		{
			if($data_array_prop!="")
			{
				$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			}
		}
		
		//echo "10** $rID=$rID2=$adjust_prod=$prodUpdate=$delete_prop=$rID4";die;
		
		if($db_type==0)
		{
			if($rID && $rID2 && $adjust_prod && $prodUpdate && $delete_prop && $rID4)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**0**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $adjust_prod && $prodUpdate && $delete_prop && $rID4)
			{
				oci_commit($con); 
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**0**0**1";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$update_id=str_replace("'","",$update_id);
		$previous_prod_id=str_replace("'","",$previous_prod_id);
		$cbo_store_name=str_replace("'","",$cbo_store_name);
		$update_trans_id=str_replace("'","",$update_trans_id);
		//echo "10**$update_id=$previous_prod_id=$update_trans_id";die;
		if($update_id>0 && $previous_prod_id>0 && $update_trans_id>0 && $cbo_store_name>0)
		{
			$previous_data_check=sql_select("select id as rcv_id, cons_quantity as rcv_qnty, cons_amount as rcv_amount  from inv_transaction where transaction_type=4 and id=$update_trans_id and prod_id=$previous_prod_id");
			$previous_check_id=$previous_data_check[0][csf("rcv_id")];
			$previous_qnty=$previous_data_check[0][csf("rcv_qnty")];
			$previous_amount=$previous_data_check[0][csf("rcv_amount")];
			//echo "10**select min(id) as next_id, min(mst_id) as mst_id, min(transaction_type) as transaction_type from inv_transaction where id > $previous_check_id and prod_id=$previous_prod_id";die;
			
			
			/*if($db_type==0) $row_count_cond=" limit 1"; else $row_count_cond=" and rownum<2";
			$next_operation_check=sql_select("select id as next_id, mst_id as mst_id, transaction_type as transaction_type from inv_transaction where id > $previous_check_id and prod_id=$previous_prod_id and status_active=1 $row_count_cond");
			//echo "10**jahid==";echo count($next_operation_check);die;
			if(count($next_operation_check)>0)
			{
				$next_id=$next_operation_check[0][csf("next_id")];
				$next_mst_id=$next_operation_check[0][csf("mst_id")];
				$next_transaction_type=$next_operation_check[0][csf("transaction_type")];

				if($next_transaction_type==1 || $next_transaction_type==4)
				{
					$next_mrr=return_field_value("recv_number as next_mrr_number","inv_receive_master","id=$next_mst_id","next_mrr_number");
				}
				else if($next_transaction_type==2 || $next_transaction_type==3)
				{
					$next_mrr=return_field_value("issue_number as next_mrr_number","inv_issue_master","id=$next_mst_id","next_mrr_number");
				}
				else
				{
					$next_mrr=return_field_value("transfer_system_id as next_mrr_number","inv_item_transfer_mst","id=$next_mst_id","next_mrr_number");
				}
				echo "20**Next Operation No:- $next_mrr  Found, Delete Not Allow.";
				disconnect($con);die;
				//check_table_status( $_SESSION['menu_id'],0);
			}*/
			
			$store_stock=sql_select("select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as store_stock_qnty from inv_transaction where prod_id=$previous_prod_id and store_id=$cbo_store_name and status_active=1");
			$store_stock_qnty=$store_stock[0][csf("store_stock_qnty")];
			
			$row_prod=sql_select( "select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id=$previous_prod_id and status_active=1 and is_deleted=0" );
			$prod_stock=$row_prod[0][csf('current_stock')];
			
			if($store_stock_qnty <= 0 && $prod_stock <= 0)
			{
				echo "20**Global Stock Or Store Wise Stock Less Then Zero, \n Please Delete Next Issue Or Receive Return, \n More Information Please See Item Ledger.";
				disconnect($con);die;
			}
			
			$prod_id=$row_prod[0][csf('id')];
			$curr_stock_qnty=$row_prod[0][csf('current_stock')]-$previous_qnty;
			$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];
			$curr_stock_value=0;
			if ($curr_stock_qnty != 0){
				$curr_stock_value=$row_prod[0][csf('stock_value')]-$previous_amount;
				$avg_rate_per_unit=number_format($curr_stock_value/$curr_stock_qnty,$dec_place[3],'.','');
			} 
			
			$field_array_prod_update="avg_rate_per_unit*current_stock*stock_value*updated_by*update_date";
			$data_array_prod_update=$avg_rate_per_unit."*".$curr_stock_qnty."*".$curr_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			
			$row_propotionate=sql_select( "select id, po_breakdown_id, quantity, order_rate, order_amount 
			from order_wise_pro_details where trans_id=$previous_check_id and status_active=1 and is_deleted=0" );
			$propotionate_data=array();
			foreach($row_propotionate as $row)
			{
				$all_order_id.=$row[csf("po_breakdown_id")].",";
				$propotionate_data[$row[csf("po_breakdown_id")]]["quantity"]+=$row[csf("quantity")];
				$propotionate_data[$row[csf("po_breakdown_id")]]["order_amount"]+=$row[csf("order_amount")];
			}
			$all_order_id=chop($all_order_id,",");
			$field_array_prod_ord_update="avg_rate*stock_quantity*stock_amount*updated_by*update_date";
			if($all_order_id!="")
			{
				$prod_order_stock=sql_select("select id, po_breakdown_id, stock_quantity, stock_amount 
				from order_wise_stock where prod_id=$previous_prod_id and po_breakdown_id in($all_order_id) and status_active=1 and is_deleted=0 ");
				foreach($prod_order_stock as $row)
				{
					$current_stock_qnty=$row[csf('stock_quantity')]-$propotionate_data[$row[csf("po_breakdown_id")]]["quantity"];
					$current_stock_value=$row[csf('stock_amount')]-$propotionate_data[$row[csf("po_breakdown_id")]]["order_amount"];
					if($current_stock_value>0 && $current_stock_qnty>0)
					{
						$current_avg_rate=number_format($current_stock_value/$current_stock_qnty,$dec_place[3],'.','');
					}
					else
					{
						$current_avg_rate=0;
					}
					
					
					$ord_prod_id_arr[]=$row[csf('id')];
					$data_array_prod_ord_update[$row[csf('id')]]=explode("*",("".$current_avg_rate."*".$current_stock_qnty."*".$current_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
			}
			
			
			$field_arr="status_active*is_deleted*updated_by*update_date";
			$data_arr="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=$rID2=$rID3=$rID4=$ordProdUpdate=true;
			$rID=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$previous_prod_id,1);
			if(count($ord_prod_id_arr)>0)
			{
				$ordProdUpdate=execute_query(bulk_update_sql_statement("order_wise_stock","id",$field_array_prod_ord_update,$data_array_prod_ord_update,$ord_prod_id_arr));
			}
			//echo "10**$update_trans_id == $update_dtls_id == $update_trans_id";oci_rollback($con);check_table_status( $_SESSION['menu_id'],0);disconnect($con);die;
			$rID2=sql_update("inv_transaction",$field_arr,$data_arr,"id",$update_trans_id,1);
			//$rID3=sql_update("inv_trims_entry_dtls",$field_arr,$data_arr,"id",$update_dtls_id,1);
			if($all_order_id!="")
			{
				$rID4=sql_update("order_wise_pro_details",$field_arr,$data_arr,"trans_id",$update_trans_id,1);
			}
			
			//echo "10** $rID && $ordProdUpdate && $rID2 && $rID3 && $rID4";oci_rollback($con);disconnect($con);die;
			if($db_type==0)
			{
				if($rID && $ordProdUpdate && $rID2 && $rID3 && $rID4)
				{
					mysql_query("COMMIT");  
					echo "2**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**0";
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "7**0**0**1";
				}
			}
	
			if($db_type==2 || $db_type==1 )
			{
				if($rID && $ordProdUpdate && $rID2 && $rID3 && $rID4)
				{
					oci_commit($con);  
					echo "2**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**0";
				}
				else
				{
					oci_rollback($con);
					echo "7**0**0**1";
				}
			}
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}
	}
}


if ($action=="trims_issue_return_popup_search")
{
	echo load_html_head_contents("Trims Issue Reurn Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
	
		function js_set_value(ids)
		{
			var id = ids.split("_");
			$('#hidden_issue_id').val(id[0]);
			$('#hidden_posted_in_account').val(id[1]);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div style="width:780px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:775px; margin-left:5px">
		<legend>Enter search words</legend>           
            <table cellpadding="2" cellspacing="0" width="770" class="rpt_table" rules="all" border="1">
                <thead>
                    <th>Return Date Range</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="180">Enter Return ID</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                    	<input type="hidden" name="hidden_issue_id" id="hidden_issue_id" class="text_boxes" value=""> 
                        <input type="hidden" name="hidden_posted_in_account" id="hidden_posted_in_account" class="text_boxes" value="">  
                    </th> 
                </thead>
                <tr>
                    <td align="center">
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To
					  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					</td>
                    <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Return ID",2=>"Challan No",3=>"Issue ID");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../../') ";							
							echo create_drop_down( "cbo_search_by", 120, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                    </td>     
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_year_selection').value, 'create_trims_issue_return_search_list_view', 'search_div', 'trims_issue_return_entry_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
           <div style="margin-top:8px;" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_trims_issue_return_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$year_id =$data[5];
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	
	if(trim($data[0])!="")
	{
		if($search_by==1)	
			$search_field_cond="and a.recv_number like '$search_string'";
		else if($search_by==2)	
			$search_field_cond="and a.challan_no like '$search_string'";
		else
			$search_field_cond="and b.issue_number like '$search_string'";
	}
	else
	{
		$search_field_cond="";	
	}
	
	if($db_type==0) $year_field="YEAR(a.insert_date)"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY')";
	else $year_field="";//defined Later
	
	$year_condition="";
	if($year_id>0)
	{
		if($db_type==0) $year_condition=" and YEAR(a.insert_date)='$year_id'";
		else $year_condition=" and to_char(a.insert_date,'YYYY')='$year_id'";
	}
	
	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, supplier_id, company_location_id FROM user_passwd where id=$user_id");
	$cre_company_id = $userCredential[0][csf('company_id')];
	$cre_store_location_id = $userCredential[0][csf('store_location_id')];
	
	if ($cre_company_id !='') {
		$company_credential_cond = " and a.company_id in($cre_company_id)";
	}
	if ($cre_store_location_id !='') {
		$store_location_credential_cond = " and a.store_id in($cre_store_location_id)"; 
	}
	
	$sql = "select a.id, a.recv_number_prefix_num, $year_field as year, a.recv_number, a.challan_no, a.receive_date, a.is_posted_account, b.issue_number from inv_receive_master a, inv_issue_master b where a.issue_id=b.id and a.entry_form=73 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond $date_cond $company_credential_cond $store_location_credential_cond $year_condition order by a.id"; 

	$arr=array();
	
	echo create_list_view("list_view", "Return ID,Year,Challan No,Return date,Issue No", "100,100,120,100","770","240",0, $sql, "js_set_value", "id,is_posted_account", "", 1, "0,0,0,0,0", $arr, "recv_number_prefix_num,year,challan_no,receive_date,issue_number", "",'','0,0,0,3,0');
	
	exit();
}

if($action=='populate_data_from_trims_issue')
{
	$data_array=sql_select("select id, company_id, recv_number, receive_date, issue_id, challan_no, store_id,floor,room,rack,shelf from inv_receive_master where id=$data");
	$company_id=$data_array[0]["COMPANY_ID"];
	
	$variable_inventory_sql=sql_select("select store_method, rack_balance from variable_settings_inventory  where company_name=$company_id and item_category_id=4 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$store_method=$variable_inventory_sql[0][csf("store_method")];

	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("recv_number")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('store_update_upto').value 			= '".$store_method."';\n";

		echo "load_room_rack_self_bin('requires/trims_issue_return_entry_controller*4', 'store','store_td', '".$row[csf('company_id')]."','"."',this.value);\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("store_id")]."';\n";
		/*echo "load_room_rack_self_bin('requires/trims_issue_return_entry_controller', 'floor','floor_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."',this.value);\n";
		echo "$('#cbo_floor').val('".$row[csf("floor_id")]."');\n";
		echo "load_room_rack_self_bin('requires/trims_issue_return_entry_controller', 'room','room_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."',this.value);\n";
		echo "$('#cbo_room').val('".$row[csf("room")]."');\n";
		echo "load_room_rack_self_bin('requires/trims_issue_return_entry_controller', 'rack','rack_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		echo "$('#txt_rack').val('".$row[csf("rack")]."');\n";
		echo "load_room_rack_self_bin('requires/trims_issue_return_entry_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";	*/
		echo "$('#txt_shelf').val('".$row[csf("self")]."');\n";

		echo "document.getElementById('txt_return_date').value 				= '".change_date_format($row[csf("receive_date")])."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('issue_id').value 					= '".$row[csf("issue_id")]."';\n";
		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		
		$issData=sql_select("select issue_basis, issue_number from inv_issue_master where id='".$row[csf("issue_id")]."'");
		echo "document.getElementById('txt_issue_no').value 				= '".$issData[0][csf("issue_number")]."';\n";
		echo "document.getElementById('cbo_basis').value 					= '".$issData[0][csf("issue_basis")]."';\n";
		echo "enable_disable();\n";
		echo "document.getElementById('update_id').value 					= '".$row[csf("id")]."';\n";
		
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_trims_issue',1,1);\n";  
		exit();
	}
}

if($action=="show_trims_listview")
{
	$sql="select a.id, a.prod_id,a.floor_id,a.room,a.rack,a.self,a.bin_box, a.cons_quantity, b.item_group_id, b.item_description, b.unit_of_measure, b.item_color, b.item_size from inv_transaction a,  product_details_master b where a.prod_id=b.id and a.mst_id='$data' and a.item_category=4 and a.transaction_type=4 and a.status_active = '1' and a.is_deleted = '0' and b.status_active=1 and b.is_deleted=0";
	$result = sql_select($sql);
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$lib_floor=return_library_array("select b.floor_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.floor_id  and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0","floor_id","floor_room_rack_name");

	$lib_room=return_library_array("SELECT b.room_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name","room_id","floor_room_rack_name");

	$lib_rack=return_library_array("SELECT b.rack_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc","rack_id","floor_room_rack_name");
	
	$lib_self_no=return_library_array("SELECT b.shelf_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id  and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc","shelf_id","floor_room_rack_name");

	$lib_bin_box=return_library_array("SELECT b.bin_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.bin_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc","bin_id","floor_room_rack_name");
	$trim_group_arr =array();
	$data_array=sql_select("select id, item_name, trim_uom, conversion_factor from lib_item_group where item_category=4");
	foreach($data_array as $row)
	{
		$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
		$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('trim_uom')];
		$trim_group_arr[$row[csf('id')]]['conversion_factor']=$row[csf('conversion_factor')];
	}
	unset($data_array);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table">
		<thead>
			<th width="70">Product Id</th>
            <th width="100">Item Group</th>
			<th width="120">Item Description</th>               
			<th width="80">Item Color</th>
			<th width="70">Item Size</th>
			<th width="50">UOM</th>
            <th width="60">Floor</th>
            <th width="60">Room</th>
            <th width="50">Rack</th>
            <th width="50">Shelf</th>
            <th width="50">Bin</th>
            <th width="70">Return Qnty</th>
		</thead>
	</table>
	<div style="width:890px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table" id="tbl_list_search">  
		<?
			$i=1;
			foreach ($result as $row)
			{  
				if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $row[csf('id')]; ?>,'populate_trims_details_form_data', 'requires/trims_issue_return_entry_controller');"> 
                	<td width="70"><p><? echo $row[csf('prod_id')]; ?></p></td>
					<td width="100"><p><? echo $trim_group_arr[$row[csf('item_group_id')]]['name']; ?></p></td>  
					<td width="120"><p><? echo $row[csf('item_description')]; ?></p></td>             
					<td width="80"><p><? echo $color_arr[$row[csf('item_color')]]; ?></p></td>
					<td width="70"><p><? echo $row[csf('item_size')]; ?></p></td>
                    <td width="50"><p><? echo $unit_of_measurement[$row[csf('unit_of_measure')]]; ?></p></td>
                    <td width="60"><p><? echo $lib_floor[$row[csf('floor_id')]]; ?></p></td>
                    <td width="60"><p><? echo $lib_room[$row[csf('room')]]; ?></p></td>
                    <td width="50"><p><? echo $lib_rack[$row[csf('rack')]]; ?></p></td>
                    <td width="50"><p><? echo $lib_self_no[$row[csf('self')]]; ?></p></td>
                    <td width="50"><p><? echo $lib_bin_box[$row[csf('bin_box')]]; ?></p></td>
					<td width="70" align="right"><? echo number_format($row[csf('cons_quantity')],2); ?></td>
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


if($action=='populate_trims_details_form_data')
{
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$trim_group_arr =array();
	$data_array=sql_select("select id, item_name, trim_uom, conversion_factor from lib_item_group where item_category=4");
	foreach($data_array as $row)
	{
		$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
		$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('trim_uom')];
		$trim_group_arr[$row[csf('id')]]['conversion_factor']=$row[csf('conversion_factor')];
	}
	unset($data_array);
	$data_array=sql_select("select a.id, a.prod_id,a.company_id, a.store_id,a.floor_id,a.room,a.rack, a.self,a.bin_box, a.cons_quantity, a.cons_rate, a.cons_amount, a.issue_id, b.item_group_id, b.item_description, b.unit_of_measure, b.item_color, b.item_size from inv_transaction a, product_details_master b where a.prod_id=b.id and a.id='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('cbo_item_group').value 				= '".$row[csf("item_group_id")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("uom")]."';\n";
		echo "document.getElementById('txt_item_description').value 		= '".$row[csf("item_description")]."';\n";
		echo "document.getElementById('txt_return_qnty').value 				= '".$row[csf("cons_quantity")]."';\n";
		echo "document.getElementById('hidden_return_qnty').value 			= '".$row[csf("cons_quantity")]."';\n";

		echo "load_room_rack_self_bin('requires/trims_issue_return_entry_controller*4', 'store','store_td', '".$row[csf('company_id')]."','"."',this.value);\n";
		echo "$('#cbo_store_name').val('".$row[csf("store_id")]."');\n";
		echo "load_room_rack_self_bin('requires/trims_issue_return_entry_controller', 'floor','floor_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."',this.value);\n";
		echo "$('#cbo_floor').val('".$row[csf("floor_id")]."');\n";
		echo "load_room_rack_self_bin('requires/trims_issue_return_entry_controller', 'room','room_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."',this.value);\n";
		echo "$('#cbo_room').val('".$row[csf("room")]."');\n";
		echo "load_room_rack_self_bin('requires/trims_issue_return_entry_controller', 'rack','rack_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		echo "$('#txt_rack').val('".$row[csf("rack")]."');\n";
		echo "load_room_rack_self_bin('requires/trims_issue_return_entry_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";	
		echo "$('#txt_shelf').val('".$row[csf("self")]."');\n";
		echo "load_room_rack_self_bin('requires/trims_issue_return_entry_controller', 'bin','bin_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."','".$row[csf('self')]."',this.value);\n";	
		echo "$('#cbo_bin').val('".$row[csf("bin_box")]."');\n";

		//echo "document.getElementById('txt_rack').value 					= '".$row[csf("rack_no")]."';\n";
		//echo "document.getElementById('txt_shelf').value 					= '".$row[csf("self")]."';\n";
		echo "document.getElementById('txt_item_color').value 				= '".$color_arr[$row[csf("item_color_id")]]."';\n";
		echo "document.getElementById('txt_item_size').value 				= '".$row[csf("item_size")]."';\n";
		echo "document.getElementById('hidden_prod_id').value 				= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('previous_prod_id').value 			= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('update_trans_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_conversion_faction').value 		= '".$trim_group_arr[$row[csf('item_group_id')]]['conversion_factor']."';\n";
		
		if($row[csf("rack_no")]!="" || $db_type==0)
		{
			$rack_cond="rack='".$row[csf("rack_no")]."'";
			$rack_cond2="rack_no='".$row[csf("rack_no")]."'";
		}
		else 
		{
			$rack_cond="rack is null";
			$rack_cond2="rack_no is null";
		}
		
		$null_cond='';
		if($db_type==2)
		{
			$null_cond="NVL";
		}
		else
		{
			$null_cond="IFNULL";
		}
		
		if($row[csf("self")]=="") $row[csf("self")]=0;
		
		$issue_qnty=return_field_value("issue_qnty","inv_trims_issue_dtls","mst_id='".$row[csf("issue_id")]."' and $null_cond(shelf_no,0)='".$row[csf("self")]."' and prod_id='".$row[csf("prod_id")]."' and $rack_cond2 and status_active=1 and is_deleted=0","issue_qnty");
		
		$cumulative_returned=return_field_value("sum(cons_quantity) as return_qty","inv_transaction","issue_id='".$row[csf("issue_id")]."' and item_category=4 and transaction_type=4 and $null_cond(self,0)='".$row[csf("self")]."' and prod_id='".$row[csf("prod_id")]."' and $rack_cond and status_active=1 and is_deleted=0","return_qty");
		$net_used = $issue_qnty-$cumulative_returned;
		
		echo "$('#cbo_uom').val(".$row[csf('unit_of_measure')].");\n";
		echo "$('#txt_rate').val(".$row[csf('cons_rate')].");\n";
		echo "$('#txt_amount').val(".$row[csf('cons_amount')].");\n";
		echo "$('#txt_issued_qnty').val('".$issue_qnty."');\n";
		echo "$('#txt_cumulative_returned').val('".$cumulative_returned."');\n";
		echo "$('#txt_net_used').val('".$net_used."');\n";

		$order_no=""; $all_po_id=""; $save_string='';
		$data_po_array=sql_select("select a.po_breakdown_id, a.quantity, b.po_number from order_wise_pro_details a, wo_po_break_down b where a.po_breakdown_id=b.id and a.trans_id='".$row[csf("id")]."' and a.entry_form=73 and a.status_active=1 and a.is_deleted=0");
		foreach($data_po_array as $row_po)
		{ 
			if($save_string=="")
			{
				$save_string=$row_po[csf("po_breakdown_id")]."_".$row_po[csf("quantity")];
				$all_po_id=$row_po[csf("po_breakdown_id")];
				$order_no=$row_po[csf("po_number")];
			}
			else
			{
				$save_string.=",".$row_po[csf("po_breakdown_id")]."_".$row_po[csf("quantity")];
				$all_po_id.=",".$row_po[csf("po_breakdown_id")];
				$order_no.=",".$row_po[csf("po_number")];
			}
		}
		
		echo "document.getElementById('save_data').value 					= '".$save_string."';\n";
		echo "document.getElementById('all_po_id').value 					= '".$all_po_id."';\n";
		echo "document.getElementById('txt_buyer_order').value 				= '".$order_no."';\n";
		
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_trims_issue',1,1);\n";  
		exit();
	}
}

if ($action=="trims_issue_entry_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$store_library=return_library_array( "select id,store_name from lib_store_location", "id","store_name");
	//$issueNo_arr=return_library_array( "select id, issue_number from inv_issue_master where status_active=1 and is_deleted=0 and item_category=4 and entry_form=25", "id", "issue_number");
	$issue_sql=sql_select("select ID, ISSUE_NUMBER, ISSUE_PURPOSE from inv_issue_master where status_active=1 and is_deleted=0 and item_category=4 and entry_form=25");
	$issueNo_arr=$issuePurpose_arr=array();
	foreach($issue_sql as $val)
	{
		$issueNo_arr[$val["ID"]]=$val["ISSUE_NUMBER"];
		$issuePurpose_arr[$val["ID"]]=$val["ISSUE_PURPOSE"];
	}
	unset($issue_sql);
	$company_arr = return_library_array("select id, company_name from lib_company","id","company_name");
	$size_arr = return_library_array("select id, size_name from lib_size","id","size_name");
	$trim_group_arr =array();
	$data_array=sql_select("select id, item_name, trim_uom, conversion_factor from lib_item_group where item_category=4");
	foreach($data_array as $row)
	{
		$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
		$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('trim_uom')];
		$trim_group_arr[$row[csf('id')]]['conversion_factor']=$row[csf('conversion_factor')];
	}
	unset($data_array);
	$sql=" select id, recv_number, issue_id, challan_no, receive_date from  inv_receive_master where id='$data[1]' and entry_form=73";
	//echo $sql;
	$dataArray=sql_select($sql);
	
	?>
	<div style="width:930px;">
    <table width="900" cellspacing="0" align="right" border="0">
        <tr>
            <td colspan="7" align="center" style="font-size:24px"><strong><? echo $company_arr[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
            <td colspan="7" align="center" style="font-size:14px">
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
					?>
						Plot No: <? echo $result[csf('plot_no')]; ?> 
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?> 
						Block No: <? echo $result[csf('block_no')];?> 
						City No: <? echo $result[csf('city')];?> 
						Zip Code: <? echo $result[csf('zip_code')]; ?> 
						Province No: <?php echo $result[csf('province')];?> 
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
						Email Address: <? echo $result[csf('email')];?> 
						Website No: <? echo $result[csf('website')];
					}
                ?> 
            </td>
        </tr>
        <tr>
            <td colspan="7" align="center" style="font-size:16px"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
        </tr>
        <tr>
            <td width="160"><strong>Issue No:</strong></td> <td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
            <td width="120"><strong>Issue No:</strong></td><td width="175px"><? echo $issueNo_arr[$dataArray[0][csf('issue_id')]]; ?></td>
            <td width="125"><strong>Issue Date :</strong></td><td width="175px"><? echo  change_date_format($dataArray[0][csf('receive_date')]); ?></td>
        </tr>
        <tr>
            <td><strong>Challan No :</strong></td> <td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>&nbsp;</strong></td><td width="175px" ><? //echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>&nbsp;</strong></td><td width="175px"><? //echo  change_date_format($dataArray[0][csf('challan_date')]); ?></td>
        </tr>
         <tr>
           <td><strong>Bar Code:</strong></td><td  colspan="3" id="barcode_img_id"></td>
        </tr>
    </table>
    <br>
	<div style="width:100%;">
        <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="120" align="center">Item Group</th>
                <th width="220" align="center">Item Description</th>
                <th width="90" align="center">Item Color</th>
                <th width="50" align="center">UOM </th>
                <th width="70" align="center">Item Size</th>
                <th width="80" align="center">Return Qty.</th>
                <th width="70" align="center">Issue Purpose</th>
                <th width="60" align="center">Rack</th>
                <th align="center">Self</th>
            </thead>
			<?
                $i=1; 
                $mst_id=$dataArray[0][csf('id')];
				$sql_dtls="Select a.id, a.product_name_details, a.item_group_id, a.item_color, a.gmts_size, b.id, b.cons_uom, b.cons_quantity, b.rack, b.self, b.issue_id 
				from product_details_master a, inv_transaction b 
				where a.id=b.prod_id and b.transaction_type=4 and b.item_category=4 and b.mst_id='$data[1]' and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0";

               // echo $sql_dtls;
                $sql_result=sql_select($sql_dtls);
                
                foreach($sql_result as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                	?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i; ?></td>
                        <td><p><? echo $trim_group_arr[$row[csf('item_group_id')]]['name']; ?></p></td>
                        <td><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td><p><? echo $color_arr[$row[csf('item_color')]]; ?></p></td>
                        <td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
                        <td align="center"><? echo $size_arr[$row[csf('gmts_size')]]; ?></td>
                        <td align="right"><? echo number_format($row[csf('cons_quantity')],2,'.',''); ?></td>
                        <td align="center"><? echo $yarn_issue_purpose[$issuePurpose_arr[$row[csf('issue_id')]]]; ?></td>
                        <td align="center"><p><? echo $row[csf('rack')]; ?></p></td>
                        <td align="center"><p><? echo $row[csf('self')]; ?></p></td>
                    </tr>
                	<?
					$cons_quantity_sum+=$row[csf('cons_quantity')];
                    $i++;
                }?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" align="right">Total :</td>
                    <td align="right"><? echo number_format($cons_quantity_sum,2,'.',''); ?></td>
                    <td colspan="3">&nbsp;</td>
                </tr>                           
            </tfoot>
        </table>
        <br>
         <?
            echo signature_table(90, $data[0], "900px");
         ?>
		   </table>
		</div>
   </div>
    <script type="text/javascript" src="../../../js/jquery.js"></script>
    <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
     <script>

	function generateBarcode( valuess ){
		   
			var value = valuess;//$("#barcodeValue").val();
		 // alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			 
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			
			$("#barcode_img_id").show().barcode(value, btype, settings);
		  
		} 
  
	 generateBarcode('<? echo $data[2]; ?>');
	 
	 
	 </script>
<?
exit();
}
?>
