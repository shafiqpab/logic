<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//--------------------------------------------------------------------------------------------

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 170, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_drop_down( 'requires/machine_wash_issue_requisition_controller', $('#cbo_company_name').val()+'_'+$('#cbo_location_name').val(), 'load_drop_down_store', 'store_td' );" );
	exit();
}
 
 if($action=="load_report_format")
 {
	 $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id=289 and is_deleted=0 and status_active=1");
	 echo trim($print_report_format);
	 exit();
 
 }

if($action=="machineNo_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$cbo_company_id=str_replace("'","",$cbo_company_id); 
	?>
    <script>
    function js_set_value(data)
    {
		var data=data.split("_");
		$("#hidden_machine_id").val(data[0]);
		$("#hidden_machine_name").val(data[1]); 
		parent.emailwindow.hide();
    }
	</script>
    
    <input type="hidden" id="hidden_machine_id" name="hidden_machine_id">
    <input type="hidden" id="hidden_machine_name" name="hidden_machine_name">
    
<? 
	 $location_name=return_library_array( "select location_name,id from  lib_location where is_deleted=0", "id", "location_name"  );
	 $floor=return_library_array( "select floor_name,id from  lib_prod_floor where is_deleted=0", "id", "floor_name"  );
	 $arr=array(0=>$location_name,1=>$floor,3=>$machine_category);  
	 
	 $sql="SELECT seq_no,location_id,floor_id,machine_no,category_id,machine_group,dia_width,gauge,id from lib_machine_name where is_deleted=0 and status_active=1 and company_id='$cbo_company_id' and category_id in(2,4,6,12,14,35) order by seq_no";
	//echo $sql;
     echo create_list_view ( "list_view", "Location Name,Floor Name,Machine No,Category,Machine Group,Dia Width,Gauge", "150,140,100,100,120,80","840","300",1, $sql, "js_set_value", "id,machine_no","", 1, "location_id,floor_id,0,category_id,0,0", $arr, "location_id,floor_id,machine_no,category_id,machine_group,dia_width,gauge", "../inventory/chemical_dyes/requires/machine_wash_issue_requisition_controller", 'setFilterGrid("list_view",-1);','') ;

	exit();	 
}

if ($action=="load_drop_down_store")
{	// fn_sub_process_enable(this.value);
	list($company_id,$location_id)=explode('_',$data);
	if ($_SESSION['logic_erp']['store_location_id'] != '' && $_SESSION['logic_erp']['store_location_id'] != 0) {$store_location_credential_cond = "and a.id in(".$_SESSION['logic_erp']['store_location_id'].")";} else { $store_location_credential_cond = "";}
	//if($location_id>0){$locationCon="and b.store_location_id=$location_id";}
	$locationCon="";
	if($location_id) $locationCon="and a.location_id=$location_id";
	echo create_drop_down( "cbo_store_name", 160, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and b.category_type in(5,6,7,23) $locationCon $store_location_credential_cond group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select --", $selected, "", "0" );  	 
	exit();
}

if($action=="mrr_popup")
{
	echo load_html_head_contents("Requisition Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);  
?>
     
<script>
	function js_set_value(data)
	{
		$("#hidden_sys_id").val(data); 
		parent.emailwindow.hide();
	}
	
	/*$(document).ready(function(e) {
		setFilterGrid('tbl_list_search',-1);
	});*/
</script>

</head>
<body>
    <div align="center" style="width:860px;">
        <form name="searchfrm" id="searchfrm">
            <fieldset style="width:855px;">
                <table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table">
                    <thead>
                        <th>Requisition Date Range</th>
                        <th>Search By</th>
                        <th width="250" id="search_by_td_up">Enter Requisition No</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                            <input type="hidden" name="hidden_sys_id" id="hidden_sys_id" class="text_boxes" value="">
                        </th>
                    </thead>
                    <tr class="general">
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;">To<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;">
                        </td>
                        <td>
                            <?
                                $search_by_arr=array(1=>"Requisition No");
                                //$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
                                echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                            ?>
                        </td>
                        <td id="search_by_td">
                            <input type="text" style="width:130px;" class="text_boxes_numeric" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td>
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $company; ?>', 'create_requisition_search_list_view', 'search_div', 'machine_wash_issue_requisition_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
                <div style="width:100%; margin-top:5px; margin-left:3px;" id="search_div" align="left"></div>
            </fieldset>
        </form>
    </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_requisition_search_list_view")
{
	$data = explode("_",$data);
	$search_string=trim($data[0]);
	$search_by=$data[1];
	$start_date =trim($data[2]);
	$end_date =trim($data[3]);
	$company =$data[4];
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and requisition_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and requisition_date between '".change_date_format($start_date,"yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date,"yyyy-mm-dd", "-",1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	$search_field_cond="";
	if($search_string!="")
	{
		$search_field_cond="and requ_prefix_num='$search_string'";
	}
	
    $company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');
	
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql="select id,requ_no,requ_prefix_num,$year_field,company_id,requisition_date,machine_id,method,tot_liquor from dyes_chem_issue_requ_mst where company_id=$company and requisition_basis=4 and entry_form=259 and status_active=1 $date_cond $search_field_cond order by id";
	$result = sql_select($sql);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="130">Company</th>
            <th width="100">Requisition No</th>
            <th width="70">Year</th>
            <th width="100">Requisition Date </th>
            <th width="110">Total Liquor (ltr) </th> 
            <th width="110">Method</th> 
            <th>machine No</th>               
        </thead>
	</table>
	<div style="width:850px; max-height:250px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>);"> 
                    <td width="40"><? echo $i; ?></td>
                    <td width="130"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                    <td width="100"><p><? echo $row[csf('requ_prefix_num')]; ?></p></td> 
                    <td width="70" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="100" align="center"><p><? echo change_date_format($row[csf('requisition_date')]); ?></p></td>
                    <td width="107" style="padding-right:3px" align="right"><p><? echo $row[csf('tot_liquor')]; ?></p></td>
                    <td width="110"><p><? echo $dyeing_method[$row[csf('method')]]; ?>&nbsp;</p></td>
                    <td><p><? echo $machine_arr[$row[csf('machine_id')]]; ?></p></td>
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

if($action=="populate_data_from_data")
{
	$sql = sql_select("select id, requ_no, company_id, location_id, requisition_date,requisition_basis,method,machine_id,tot_liquor, store_id from dyes_chem_issue_requ_mst where id=$data");
	foreach($sql as $row)
	{
		echo "document.getElementById('txt_mrr_no').value = '".$row[csf("requ_no")]."';\n"; 
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n"; 
		echo "document.getElementById('update_id_check').value = '".$row[csf("id")]."';\n"; 
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('cbo_location_name').value = '".$row[csf("location_id")]."';\n"; 
		echo "document.getElementById('cbo_store_name').value = '".$row[csf("store_id")]."';\n";  
		echo "document.getElementById('txt_requisition_date').value = '".change_date_format($row[csf("requisition_date")])."';\n"; 
		echo "document.getElementById('cbo_receive_basis').value = '".$row[csf("requisition_basis")]."';\n"; 
		echo "document.getElementById('cbo_method').value = '".$row[csf("method")]."';\n";
		echo "document.getElementById('machine_id').value = '".$row[csf("machine_id")]."';\n"; 
		echo "document.getElementById('txt_tot_liquor').value = '".$row[csf("tot_liquor")]."';\n"; 
		
		$machine_name="";
		if($row[csf("machine_id")]>0)
		{
			$machine_name=return_field_value("machine_no","lib_machine_name","id=".$row[csf('machine_id')]);
		}
		echo "document.getElementById('txt_machine_no').value = '".$machine_name."';\n";
		echo "set_button_status(1, '".$permission."', 'fnc_chemical_dyes_issue_requisition',1,1);\n";  
	 
		exit();
	}
}

/*if ($action == "itemLot_popup")
{
	echo load_html_head_contents("Item Lot Info", "../../../", 1, 1, '', 1, '');
	extract($_REQUEST);
	?>
	<script>
	var selected_id = new Array, selected_name = new Array();
	selected_attach_id = new Array();
	
	function toggle(x, origColor) {
	var newColor = 'yellow';
	if (x.style) {
	x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
	}
	}
	
	function js_set_value(id) {
	var str = id.split("_");
	toggle(document.getElementById('tr_' + str[0]), '#FFFFFF');
	var strdt = str[2];
	str = str[1];
	
	if (jQuery.inArray(str, selected_id) == -1) {
	selected_id.push(str);
	selected_name.push(strdt);
	}
	else {
	for (var i = 0; i < selected_id.length; i++) {
	if (selected_id[i] == str) break;
	}
	selected_id.splice(i, 1);
	selected_name.splice(i, 1);
	}
	var id = '';
	var ddd = '';
	for (var i = 0; i < selected_id.length; i++) {
	id += selected_id[i] + ',';
	ddd += selected_name[i] + ',';
	}
	id = id.substr(0, id.length - 1);
	ddd = ddd.substr(0, ddd.length - 1);
	$('#item_lot').val(id);
	//$('#prod_id').val( ddd );
	}
	</script>
	<input type="hidden" id="prod_id"/><input type="hidden" id="item_lot"/>
	<?
	if ($db_type == 0) {
	$sql = "SELECT distinct batch_lot from inv_transaction where prod_id='$txt_prod_id' and status_active=1 and is_deleted=0 and batch_lot!=' '  order by batch_lot desc";
	} elseif ($db_type == 2) {
	$sql = "SELECT distinct batch_lot from inv_transaction where prod_id='$txt_prod_id' and status_active=1 and is_deleted=0 and batch_lot!=' '  order by batch_lot desc";
	}
	//echo $sql;
	
	echo create_list_view("list_view", "Item Lot", "200", "330", "250", 0, $sql, "js_set_value", "batch_lot", "", 1, "", 0, "batch_lot", "recipe_entry_controller", 'setFilterGrid("list_view",-1);', '0', '', 1);
	die;
}*/


if($action=="item_details")
{
	$data=explode("**",$data);
	$company_id=$data[0];
	$update_id=$data[1];
	$store_id=$data[2];
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1270" class="rpt_table" >
        <thead>
			<tr>
                <th colspan="15"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?=$lab_msg;?> </th>
            </tr>
            <th width="40">SL</th>
            <th width="60">Prod. ID</th>
            <th width="90">Item Category</th>
            <th width="110">Group</th>
            <th width="80">Sub Group</th>
            <th width="130">Item Description</th>
			<th width="80">Item Lot</th>
            <th width="50">UOM</th>
            <th width="80">Stock Qty</th>
            <th width="80">Dose Base</th>
            <th width="75">Ratio</th>
            <th width="60">Seq.No</th>
            <th width="80">Recipe Qty.</th>
            <th width="90">Reqn. Qty.</th>
            <th>Remark</th>
        </thead>
    </table>
    <div style="width:1270px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1290" class="rpt_table" id="tbl_list_search">
            <tbody>
            <?
                $item_group_arr=return_library_array( "select id, item_name from lib_item_group", "id", "item_name" );
				if(str_replace("'","",$update_id)>0)
				{
					$dtls_data=sql_select("select a.store_id, b.id as dtls_id, b.product_id, b.seq_no, b.dose_base as dose_base_curr, b.item_lot, b.ratio, b. recipe_qnty, b.required_qnty, b.remarks from dyes_chem_issue_requ_dtls b, dyes_chem_issue_requ_mst a where a.id = b.mst_id and b.mst_id=$update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.product_id");
					//$dtls_data=sql_select("select b.id as dtls_id, b.product_id, b.store_id, b.dose_base as dose_base_curr, b.item_lot, b.ratio, b. recipe_qnty, b.required_qnty, b.remarks from dyes_chem_issue_requ_dtls b where b.mst_id=$update_id and b.status_active=1 and b.is_deleted=0 order by b.product_id");
					$dtls_data_arr=array();$recipe_prod_id_arr=array(); $product_data_arr=array();
					foreach($dtls_data as $row)
					{
						$prod_key=$row[csf('product_id')]."_".$row[csf('store_id')]."_".$row[csf('item_lot')];
						$dtls_data_arr[$prod_key]["dtls_id"]=$row[csf("dtls_id")];
						$dtls_data_arr[$prod_key]["dose_base_curr"]=$row[csf("dose_base_curr")];
						$dtls_data_arr[$prod_key]["ratio"]=$row[csf("ratio")];
						$dtls_data_arr[$prod_key]["seq_no"]=$row[csf("seq_no")];
						$dtls_data_arr[$prod_key]["item_lot"]=$row[csf("item_lot")];
						$dtls_data_arr[$prod_key]["recipe_qnty"]=$row[csf("recipe_qnty")];
						$dtls_data_arr[$prod_key]["required_qnty"]=$row[csf("required_qnty")];
						$dtls_data_arr[$prod_key]["remarks"]=$row[csf("remarks")];
						$recipe_prod_id_arr[$prod_key]=$prod_key;

					}
					/*echo "<pre>";
					print_r($recipe_prod_id_arr);*/
				}
				
				$sql_machinge_lib="SELECT a.id as id, a.company_id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.unit_of_measure, 1 as dose_base, b.store_id, b.lot, b.cons_qty 
				from product_details_master a, inv_store_wise_qty_dtls b , use_for_lab_machine_finishing c
				where a.id=b.prod_id and b.prod_id=c.prod_id and c.use_for=2 and a.company_id='$company_id' and b.store_id=$store_id and a.item_category_id in(5,7,6,23) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
				order by id";
				
				//echo $sql;
				$sql_machinge_lib_result=sql_select( $sql_machinge_lib );
				$machine_useable=0;
				if(count($sql_machinge_lib_result)>0)
				{
					$machine_useable=1;
					$machine_lib_data=array();
					foreach($sql_machinge_lib_result as $val)
					{
						$machine_lib_data[$val[csf("company_id")]][$val[csf("item_category_id")]][$val[csf("item_group_id")]][trim($val[csf("sub_group_name")])][trim($val[csf("item_description")])]=$val[csf("id")];
					}
				}
				$sql="SELECT a.id as id, a.company_id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.unit_of_measure, 1 as dose_base, b.store_id, b.lot, b.cons_qty 
				from product_details_master a, inv_store_wise_qty_dtls b
				where a.id=b.prod_id   and a.company_id='$company_id' and b.store_id=$store_id and a.item_category_id in(5,7,6,23)  and b.cons_qty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
				order by id";
				//echo $sql;
				//echo $machine_useable.test;die;
				$nameArray=sql_select( $sql );
				foreach($nameArray as $row)
				{
					if($machine_useable==1)
					{
						if($machine_lib_data[$row[csf("company_id")]][$row[csf("item_category_id")]][$row[csf("item_group_id")]][trim($row[csf("sub_group_name")])][trim($row[csf("item_description")])]>0)
						{
							$prod_key=$row[csf('id')]."_".$row[csf('store_id')]."_".$row[csf('lot')];
							$product_data_arr[$prod_key]=$row[csf('id')]."**".$row[csf('item_category_id')]."**".$row[csf('item_group_id')]."**".$row[csf('sub_group_name')]."**".$row[csf('item_description')]."**".$row[csf('unit_of_measure')]."**".$row[csf('dose_base')]."**".$row[csf('cons_qty')];
						}
					}
					else
					{
						$prod_key=$row[csf('id')]."_".$row[csf('store_id')]."_".$row[csf('lot')];
						$product_data_arr[$prod_key]=$row[csf('id')]."**".$row[csf('item_category_id')]."**".$row[csf('item_group_id')]."**".$row[csf('sub_group_name')]."**".$row[csf('item_description')]."**".$row[csf('unit_of_measure')]."**".$row[csf('dose_base')]."**".$row[csf('cons_qty')];
					}
				}
				
				
				$i=1;
				if(count($recipe_prod_id_arr)>0)
				{
					foreach($recipe_prod_id_arr as $prodId)
					{
						$prod_ref=explode("_",$prodId);
						$product_id=$prod_ref[0];
						$store_id=$prod_ref[1];
						$product_lot=$prod_ref[2];
						$prodData=explode("**",$product_data_arr[$prodId]);
						$prod_id=$prodData[0];
						$item_category_id=$prodData[1];
						$item_group_id=$prodData[2];
						$sub_group_name=$prodData[3];
						$item_description=$prodData[4];
						$unit_of_measure=$prodData[5];
						$dose_base_no=$prodData[6];
						$store_stock=$prodData[7];

						$dtls_id=$dtls_data_arr[$prodId]["dtls_id"];
						$dose_base_curr=$dtls_data_arr[$prodId]["dose_base_curr"];
						$ratio=$dtls_data_arr[$prodId]["ratio"];
						$seq_no="";
						$seq_no=$dtls_data_arr[$prodId]["seq_no"];
						$item_lot=$dtls_data_arr[$prodId]["item_lot"];
						$recipe_qnty=$dtls_data_arr[$prodId]["recipe_qnty"];
						$required_qnty=$dtls_data_arr[$prodId]["required_qnty"];
						$remarks=$dtls_data_arr[$prodId]["remarks"];
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$bgcolor="yellow";

						
						if($dtls_id>0) $doseBase=$dose_base_curr; else $doseBase=$dose_base_no; 
						
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="vertical-align:middle"> 
							<td width="40" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
							<td width="60" id="product_id_<? echo $i; ?>"><? echo $product_id; ?>
								<input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px" value="<? echo $product_id;?>">
							</td>
							<td width="90"><p><? echo $item_category[$item_category_id]; ?></p>
								 <input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px" value="<? echo $item_category_id; ?>">
							</td>
							<td width="110" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$item_group_id]; ?>&nbsp;</p></td>
							<td width="80" id="sub_group_name_<? echo $i; ?>"><p><? echo $sub_group_name; ?>&nbsp;</p></td>
							<td width="130" id="item_description_<? echo $i; ?>"><p><? echo $item_description; ?></p></td> 
							<td width="80" id="item_lot_<? echo $i; ?>"><input type="text" name="txt_item_lot[]" id="txt_item_lot_<? echo $i; ?>" class="text_boxes" style="width:68px" value="<? echo $item_lot; ?>" readonly>
							</td>
							
							<td width="50" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$unit_of_measure]; ?></td>
                            <td width="80" align="center" id="stock_qty_<? echo $i; ?>"><? echo number_format($store_stock,4,'.',''); ?>
                             <input type="hidden" name="stock_qty_chk[]" id="stock_qty_chk_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px" value="<? echo number_format($store_stock,4,'.','');?>">
                            </td>
							<td width="80" align="center" id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 70, $dose_base, "", 1, "- Select Dose Base -",$doseBase,"",1); ?></td>
							<td width="75" align="center" id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" onKeyUp="calculate_requs_qty(1,<? echo $i; ?>)" value="<? echo $ratio; ?>"  onBlur="color_row(<? echo $i; ?>);seq_no_val(<? echo $i; ?>);"></td>
                            <td width="60" align="center" id="seqno_<? echo $i; ?>"><input type="text" name="txt_seqno[]" id="txt_seqno_<? echo $i; ?>" class="text_boxes_numeric" style="width:30px" value="<? echo $seq_no; ?>" onBlur="row_sequence(<? echo $i; ?>);"></td>
							<td width="80" align="center" id="recipe_qnty_<? echo $i; ?>"><input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:67px" value="<? echo $recipe_qnty; ?>" disabled ></td>
							<td  width="75"  align="center" id="reqn_qnty_<? echo $i; ?>">
	                            <input type="text" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i;?>" class="text_boxes_numeric" onKeyUp="calculate_requs_qty(2,<? echo $i; ?>)" value="<? echo $required_qnty; ?>" style="width:75px">
	                            <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $dtls_id; ?>">
							</td>
	                        <td align="center"  id="remark_<? echo $i; ?>"><input type="text" name="txt_remark[]" id="txt_remark_<? echo $i; ?>" class="text_boxes" style="width:100px"  value="<? echo $remarks; ?>" ></td> 

						</tr>
						<?
						$i++;
					}
				}

			//	if (str_replace("'","",$update_id) == '')
				//{
					foreach($product_data_arr as $prodId=>$data)
					{
						if(!in_array($prodId,$recipe_prod_id_arr))
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$prod_ref=explode("_",$prodId);
							$product_id=$prod_ref[0];
							$store_id=$prod_ref[1];
							$product_lot=$prod_ref[2];
							$prodData=explode("**",$data);
							$Prod_ID=$prodData[0];
							$item_category_id=$prodData[1];
							$item_group_id=$prodData[2];
							$sub_group_name=$prodData[3];
							$item_description=$prodData[4];
							$unit_of_measure=$prodData[5];
							$dose_base_no=$prodData[6];
							$store_stock=$prodData[7];
							

							$ratio=''; $recipe_qnty=''; $required_qnty="";$remarks='';
							if($item_category_id==6)
							{
								$selected_dose=2;
							}
							else
							{
								$selected_dose=1;
							}

							$dose_base_check=number_format($dose_base_no,7,'.','');
							$store_stock=number_format($store_stock,4,'.','');
							if($store_stock>0)
							{
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								// $doseBase=$dose_base_no; 
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="vertical-align:middle"> 
									<td width="40" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
									<td width="60" id="product_id_<? echo $i; ?>"><? echo $product_id; ?>
										<input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px" value="<? echo $product_id;?>">
									</td>
									<td width="90"><p><? echo $item_category[$item_category_id]; ?></p>
										 <input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px" value="<? echo $item_category_id; ?>">
									</td>
									<td width="110" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$item_group_id]; ?>&nbsp;</p></td>
									<td width="80" id="sub_group_name_<? echo $i; ?>"><p><? echo $sub_group_name; ?>&nbsp;</p></td>
									<td width="130" id="item_description_<? echo $i; ?>"><p><? echo $item_description; ?></p></td> 
									<td width="80" id="item_lot_<? echo $i; ?>"><input type="text" name="txt_item_lot[]" id="txt_item_lot_<? echo $i; ?>" class="text_boxes" style="width:68px" value="<? echo $product_lot; ?>"  readonly>
								</td>
								
									<td width="50" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$unit_of_measure]; ?></td>
                                    <td width="80" align="center" title="<?=$store_stock;?>" id="stock_qty_<? echo $i; ?>"><? echo number_format($store_stock,4,'.',''); ?>
                                    <input type="hidden" name="stock_qty_chk[]" id="stock_qty_chk_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px" value="<? echo number_format($store_stock,4,'.','');?>">
                                    </td>
									<td width="80" align="center" id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 70, $dose_base, "", 1, "- Select Dose Base -",$selected_dose); ?></td>
									<td width="75" align="center" id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" onKeyUp="calculate_requs_qty(1,<? echo $i; ?>)" value="<? echo $ratio; ?>" onBlur="color_row(<? echo $i; ?>);seq_no_val(<? echo $i; ?>); "></td>
									<td width="60" align="center" id="seqno_<? echo $i; ?>"><input type="text" name="txt_seqno[]" id="txt_seqno_<? echo $i; ?>" class="text_boxes_numeric" style="width:30px" value="<? //echo $seq_no; ?>" onBlur="row_sequence(<? echo $i; ?>);"></td>
                                    <td width="80" align="center" id="recipe_qnty_<? echo $i; ?>"><input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:67px" value="<? echo $recipe_qnty ?>" disabled ></td>
									<td  width="75"  align="center" id="reqn_qnty_<? echo $i; ?>">
			                            <input type="text" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i;?>" class="text_boxes_numeric" onKeyUp="calculate_requs_qty(2,<? echo $i; ?>)" value="<? echo $required_qnty; ?>" style="width:75px">
			                            <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? //echo $prodId; ?>">
									</td>
			                        <td align="center"  id="remark_<? echo $i; ?>"><input type="text" name="txt_remark[]" id="txt_remark_<? echo $i; ?>" class="text_boxes" style="width:100px"  value="<? echo $remarks; ?>" ></td> 
								</tr>
								<?
								//$max_seq_no[]=$selectResult[csf('seq_no')];
								$i++;
							}
						}
					}
				//}
            ?>
            </tbody>
        </table>
    </div>
<?
	exit();	
}
 
if($action=="save_update_delete")
{
	//echo "string";die;
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0)  // Insert Here
	{

		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0) $year_cond="YEAR(insert_date)"; 
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";//defined Later	
		
		
		
		$new_requ_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '','MWIR', date("Y",time()), 5, "select requ_no_prefix,requ_prefix_num from dyes_chem_issue_requ_mst where company_id=$cbo_company_name and requisition_basis=4 and $year_cond=".date('Y',time())." order by id desc ", "requ_no_prefix", "requ_prefix_num" ));
		$id=return_next_id( "id", "dyes_chem_issue_requ_mst", 1 ) ;
		$field_array="id,requ_no,requ_no_prefix,requ_prefix_num,company_id,entry_form,location_id,requisition_date,requisition_basis,method,machine_id,tot_liquor,inserted_by,insert_date,store_id,copy_from";
		$data_array="(".$id.",'".$new_requ_no[0]."','".$new_requ_no[1]."',".$new_requ_no[2].",".$cbo_company_name.",259,".$cbo_location_name.",".$txt_requisition_date.",".$cbo_receive_basis.",".$cbo_method.",".$machine_id.",".$txt_tot_liquor.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'," . $cbo_store_name . "," . $txt_copy_from . ")";

	
		$field_array_dtls="id,mst_id,requ_no,requisition_basis,product_id,item_category,dose_base,ratio,recipe_qnty,required_qnty,req_qny_edit,remarks,seq_no,item_lot,inserted_by,insert_date,store_id";
		if (str_replace("'", "", $copy_id) == 1)  //Copy Yes
		{
			$id_dtls=return_next_id( "id", "dyes_chem_issue_requ_dtls", 1 ) ;
			$sql_copy = "select  mst_id,requ_no,requisition_basis,product_id,item_category,dose_base,ratio,recipe_qnty,required_qnty,req_qny_edit,remarks,seq_no,item_lot,inserted_by,insert_date,store_id from dyes_chem_issue_requ_dtls where mst_id=$update_id_check  and status_active=1  order by id";
			$nameArray_copy = sql_select($sql_copy);//txt_seqno_
			
			$i = 1;

			foreach ($nameArray_copy as $row) 
			{
				 
				$txt_prod_id=str_replace("'", "", $row[csf('product_id')]);
				$txt_item_cat=str_replace("'", "", $row[csf('item_category')]);
				$cbo_dose_base=str_replace("'", "", $row[csf('dose_base')]);
				
				$txt_ratio=str_replace("'", "", $row[csf('ratio')]);
				$txt_recipe_qnty=str_replace("'", "", $row[csf('recipe_qnty')]);
				$txt_reqn_qnty=str_replace("'", "", $row[csf('required_qnty')]);
				
				$txt_reqn_qnty_edit=str_replace("'", "", $row[csf('req_qny_edit')]);
				$txt_remark_edit=str_replace("'", "", $row[csf('remarks')]);
				$txt_seq_no=str_replace("'", "", $row[csf('seq_no')]);
				
				$txt_item_lot=str_replace("'", "", $row[csf('item_lot')]);
				$cbo_store_name=str_replace("'", "", $row[csf('store_id')]);
				$cbo_receive_basis=str_replace("'", "", $row[csf('requisition_basis')]);
				 
				
				if ($i != 1) $data_array_dtls .= ",";
				$data_array_dtls .="(".$id_dtls.",".$id.",'".$new_requ_no[0]."','".$cbo_receive_basis."','".$txt_prod_id."','".$txt_item_cat."','".$cbo_dose_base."','".$txt_ratio."','".$txt_recipe_qnty."','".$txt_reqn_qnty."','".$txt_reqn_qnty_edit."','".$txt_remark_edit."','".$txt_seq_no."','".$txt_item_lot."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','" . $cbo_store_name . "')";
				$id_dtls = $id_dtls + 1;
				$i++;
			}
			
		}
		else
		{
		//echo "string";die;
		$id_dtls=return_next_id( "id", "dyes_chem_issue_requ_dtls", 1 ) ;
			for($i=1;$i<=$total_row;$i++)
			{
				$txt_prod_id="txt_prod_id_".$i;
				$txt_item_cat="txt_item_cat_".$i;
				$cbo_dose_base="cbo_dose_base_".$i;
				$txt_ratio="txt_ratio_".$i;
				$txt_item_lot="txt_item_lot_".$i;
				$txt_recipe_qnty="txt_recipe_qnty_".$i;
				$txt_reqn_qnty="txt_reqn_qnty_".$i;
				$txt_reqn_qnty_edit="txt_reqn_qnty_".$i;
				$txt_remark_edit="txt_remark_".$i;
				$txt_seq_no="txt_seqno_".$i;
				$updateIdDtls="updateIdDtls_".$i;
				//$txt_seq_no=$i;
				$item_lot=str_replace("'", "", $$txt_item_lot);
				
				if($data_array_dtls!="") $data_array_dtls .=",";
				$data_array_dtls .="(".$id_dtls.",".$id.",'".$new_requ_no[0]."',".$cbo_receive_basis.",".$$txt_prod_id.",".$$txt_item_cat.",".$$cbo_dose_base.",".$$txt_ratio.",".$$txt_recipe_qnty.",".$$txt_reqn_qnty.",".$$txt_reqn_qnty_edit.",".$$txt_remark_edit.",".$$txt_seq_no.",'".$item_lot."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'," . $cbo_store_name . ")";
				$id_dtls=$id_dtls+1;
				
			}
		}
		
		//echo "10**INSERT INTO dyes_chem_issue_requ_mst (".$field_array.") VALUES ".$data_array."**".$total_row;die;
		$rID=sql_insert("dyes_chem_issue_requ_mst",$field_array,$data_array,1);
		//echo "string";die;
		//  echo "10**INSERT INTO dyes_chem_issue_requ_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls.""; die;
		$rID_dtls=sql_insert("dyes_chem_issue_requ_dtls",$field_array_dtls,$data_array_dtls,1); 
		//  echo "10**$rID==$rID_dtls";die;
		//echo "10**".$rID ."&&". $rID_dtls;die;
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $rID_dtls)
			{
				mysql_query("COMMIT");  
				echo "0**".$new_requ_no[0]."**".$id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_requ_no[0]."**".$id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_dtls)
			{
				oci_commit($con); 
				echo "0**".$new_requ_no[0]."**".$id;
			}
			else
			{
				oci_rollback($con);  
				echo "10**".$new_requ_no[0]."**".$id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array_up="location_id*requisition_date*method*machine_id*tot_liquor*updated_by*update_date";
		$data_array=$cbo_location_name."*".$txt_requisition_date."*".$cbo_method."*".$machine_id."*".$txt_tot_liquor."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$id_dtls=return_next_id( "id", "dyes_chem_issue_requ_dtls", 1 ) ; 
		$field_array_dtls="id,mst_id,requ_no,requisition_basis,product_id,item_category,dose_base,ratio,recipe_qnty,required_qnty,req_qny_edit,remarks,seq_no,item_lot,inserted_by,insert_date,store_id";
		for($i=1;$i<=$total_row;$i++)
		{
			$txt_prod_id="txt_prod_id_".$i;
			$txt_item_cat="txt_item_cat_".$i;
			$cbo_dose_base="cbo_dose_base_".$i;
			$txt_ratio="txt_ratio_".$i;
			$txt_item_lot="txt_item_lot_".$i;
			$txt_recipe_qnty="txt_recipe_qnty_".$i;
			$txt_reqn_qnty="txt_reqn_qnty_".$i;
			$txt_reqn_qnty_edit="txt_reqn_qnty_".$i;
			$txt_remark_edit="txt_remark_".$i;
			$updateIdDtls="updateIdDtls_".$i;
			$txt_seq_no="txt_seqno_".$i;
			//$txt_seq_no=$i;
			$item_lot=str_replace("'", "", $$txt_item_lot);
			if($data_array_dtls!="") $data_array_dtls .=",";
			$data_array_dtls .="(".$id_dtls.",".$update_id.",".$txt_mrr_no.",".$cbo_receive_basis.",".$$txt_prod_id.",".$$txt_item_cat.",".$$cbo_dose_base.",".$$txt_ratio.",".$$txt_recipe_qnty.",".$$txt_reqn_qnty.",".$$txt_reqn_qnty_edit.",".$$txt_remark_edit.",".$$txt_seq_no.",'".$item_lot."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'," . $cbo_store_name . ")";
			$id_dtls=$id_dtls+1;
		}
		
		$rID=sql_update("dyes_chem_issue_requ_mst",$field_array_up,$data_array,"id",$update_id,1);
		$delete_dtls=execute_query("delete from dyes_chem_issue_requ_dtls where mst_id=$update_id",0);
		//echo "10**INSERT INTO dyes_chem_issue_requ_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls."";
		$rID_dtls=sql_insert("dyes_chem_issue_requ_dtls",$field_array_dtls,$data_array_dtls,1); 
		//echo "10**".($rID ."&&". $delete_dtls ."&&". $rID_dtls);die;
		if($db_type==0)
		{
			if($rID && $rID_dtls && $delete_dtls)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_mrr_no)."**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_mrr_no)."**".str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_dtls && $delete_dtls)
			{
				oci_commit($con); 
				echo "1**".str_replace("'","",$txt_mrr_no)."**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);  
				echo "10**".str_replace("'","",$txt_mrr_no)."**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN"); //str_replace("'","",$update_id)
		}
	$update_id=	str_replace("'","",$update_id);
	$rID=execute_query( "update dyes_chem_issue_requ_mst set status_active=0,is_deleted=1,updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'   where  id=$update_id",0);
	$rID1=execute_query( "update dyes_chem_issue_requ_dtls set status_active=0,is_deleted=1,updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'   where  mst_id=$update_id",0);
	
	if($db_type==0){
			if($rID  && $rID1){
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_mrr_no)."**".str_replace("'","",$update_id);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_mrr_no)."**".str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 ){
			if($rID  && $rID1){
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_mrr_no)."**".str_replace("'","",$update_id);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_mrr_no)."**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
			
	}
}

if($action=="chemical_dyes_issue_requisition_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$sql="select requ_no, location_id, requisition_date, method, requisition_basis, machine_id, tot_liquor from dyes_chem_issue_requ_mst a where a.id='$data[1]' and a.company_id='$data[0]'";
	$dataArray=sql_select($sql);
	$nameArray=sql_select( "select company_name, plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
	$company_name=$nameArray[0][csf('company_name')];
	$location_id=$nameArray[0][csf('location_id')];
	$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
//echo $company_name.'=='.$location_id;
	$com_dtls = fnc_company_location_address($data[0], $location_id, 2);
?>
<div style="width:1390px;">
    <table width="1390" cellspacing="0" align="center" >
        <tr>
            <td colspan="10" align="center" style="font-size:xx-large"><strong><? echo $company_name; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">  
				<?
					echo $com_dtls[1];
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u><? //echo $data[2]; ?>Dyes and Chemical Requisition</u></strong></td>
        </tr>
    </table>
    <table width="1390" cellspacing="0" align="center" >
        <tr>
        	<td width="90"><strong>Req. ID </strong></td><td width="160px"><? echo $dataArray[0][csf('requ_no')]; ?></td>
            <td width="100"><strong>Req. Date</strong></td><td width="160px"><? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
            <td><strong>Issue Basis</strong></td> <td><? echo $receive_basis_arr[$dataArray[0][csf('requisition_basis')]]; ?></td>
       </tr>
       <tr>
            <td><strong>Machine No</strong></td>
            <td>
				<? 
					$machine_data=sql_select("select machine_no from lib_machine_name where id='".$dataArray[0][csf("machine_id")]."'");
                	echo $machine_data[0][csf('machine_no')]; 
                ?>
            </td>
            <td><strong>Total Liq.(ltr)</strong></td><td><? echo $dataArray[0][csf("tot_liquor")]; ?></td>
            <td>Requisition for</td><td><? echo $dyeing_method[$dataArray[0][csf("method")]]; ?></td>
        </tr>
    </table>
	<div style="width:1390px; margin-top:10px" >
        <table align="right" cellspacing="0" width="1390" border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <tr bgcolor="#CCCCFF">
                    <th colspan="17" align="center"><strong>Dyes and Chemical Requisition</strong></th>
                </tr>
                <tr>   
                    <th width="40">SL</th>
                    <th width="90">Item Category</th>
                    <th width="110">Group</th>
                    <th width="130">Item Description</th>
					<th width="100">Item Lot</th>
                    <th width="50">UOM</th>
                    <th width="80">Dose Base</th>
                    <th width="75">Ratio</th>
					<th width="40">Seq. No</th>
                    <th width="80">Recipe Qty.</th>
                    <th width="80">Issue Qty.</th>
                    <th width="60" >KG</th>
                    <th width="60" >GM</th>
                    <th width="60" >MG</th>
                    <th width="70" >Avg. Rate</th>
                    <th width="70">Issue Vaule</th>
                    <th>Remark</th>
                </tr>
            </thead>
			<?  
            	$group_arr=return_library_array( "select id,item_name from lib_item_group where item_category in (5,6,7) and status_active=1 and is_deleted=0",'id','item_name');
				$sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.unit_of_measure, a.avg_rate_per_unit, b.dose_base,b.seq_no, b.ratio, b.recipe_qnty, b.req_qny_edit,b.remarks,b.item_lot from product_details_master a, dyes_chem_issue_requ_dtls b where a.id=b.product_id and b.mst_id=$data[1] and b.status_active=1 and b.is_deleted=0 and a.item_category_id in (5,7,6,23) and a.status_active=1 and a.is_deleted=0 order by b.seq_no";
				$sql_result= sql_select($sql);
				$i=1; $iss_qnty_kg_grand=$iss_qnty_gm_grand=$iss_qnty_mg_grand=0;
				foreach($sql_result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					
					$req_qny_edit=explode(".",(string)$row[csf("req_qny_edit")]);
					$iss_qnty_kg=$req_qny_edit[0];
					if($iss_qnty_kg=="") $iss_qnty_kg=0;
					
					$iss_qnty_gm=substr($req_qny_edit[1],0,3);//$rem[0]; // floor($mg/1000);
					$iss_qnty_mg=substr($req_qny_edit[1],3,3);//$rem[1];//round($mg-$iss_qnty_gm*1000);
					$iss_qnty_gm= str_pad($iss_qnty_gm,3,"0",STR_PAD_RIGHT);
					$iss_qnty_mg= str_pad($iss_qnty_mg,3,"0",STR_PAD_RIGHT);
					
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                        <td><? echo $group_arr[$row[csf("item_group_id")]]; ?></td>
                        <td><? echo $row[csf("item_description")]; ?></td>
						 <td><? echo $row[csf("item_lot")]; ?></td>
                        <td align="center"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
                        <td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
                        <td align="center"><? echo number_format($row[csf("ratio")],6,'.',''); ?></td>
						<td align="center"><? echo $row[csf("seq_no")]; ?></td>
                        <td align="right"><? echo number_format($row[csf("recipe_qnty")],6,'.',''); ?></td>
                        <td align="right"><? echo number_format($row[csf("req_qny_edit")],6,'.',''); ?></td>
                        <td align="right"><? echo $iss_qnty_kg; ?></td>
                        <td align="right"><? echo $iss_qnty_gm; ?></td>
                        <td align="right"><? echo $iss_qnty_mg; ?></td>
                        <td align="right"><? echo  number_format($row[csf("avg_rate_per_unit")],6,'.',''); ?></td>
                        <td align="right"><? $req_value=$row[csf("req_qny_edit")]*$row[csf("avg_rate_per_unit")]; echo number_format($req_value,6,'.',''); ?></td>
                        <td align="left"><p><? echo $row[csf("remarks")]; ?></p></td>
                    </tr>
                <? 
					$i++;
					$recipe_qnty_grand+=$row[csf("recipe_qnty")];
					$req_qny_edit_grand+=$row[csf("req_qny_edit")];
					$req_value_grand+=$req_value;

					$iss_qnty_kg_grand+=$iss_qnty_kg;
					$iss_qnty_gm_grand+=$iss_qnty_gm;
					$iss_qnty_mg_grand+=$iss_qnty_mg;
				}
				?>
             <tr>
                <td colspan="9" align="right"><strong> Total :</strong></td>
                <td align="right"><?php echo number_format($recipe_qnty_grand,6,'.',''); ?></td>
                <td align="right"><?php echo number_format($req_qny_edit_grand,6,'.',''); ?></td>
				<td align="right"><?php echo $iss_qnty_kg_grand; ?></td>
				<td align="right"><?php echo $iss_qnty_gm_grand; ?></td>
				<td align="right"><?php echo $iss_qnty_mg_grand; ?></td>
                <td>&nbsp;</td>
                <td align="right"><?php echo number_format($req_value_grand,6,'.',''); ?></td>
                <td>&nbsp;</td>
            </tr> 
		</table>
        <br>
		 <?
            echo signature_table(15, $data[0], "1390px");
         ?>
	</div>
</div>         
<?
exit();
}


if($action=="chemical_dyes_issue_requisition_print2")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$sql="select requ_no, location_id, requisition_date, method, requisition_basis, machine_id, tot_liquor from dyes_chem_issue_requ_mst a where a.id='$data[1]' and a.company_id='$data[0]'";
	$dataArray=sql_select($sql);
$location_id=$dataArray[0][csf('company_name')];
	$nameArray=sql_select( "select company_name, plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
	$company_name=$nameArray[0][csf('company_name')];
	$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
	$com_dtls = fnc_company_location_address($data[0], $location_id, 2);
	
?>
<div style="width:950px;">
    <table width="950" cellspacing="0" align="center" >
        <tr>
            <td colspan="10" align="center" style="font-size:xx-large"><strong><? echo $company_name; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">  
				<?
					 echo $com_dtls[1];
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u><? //echo $data[2]; ?>Dyes and Chemical Requisition</u></strong></td>
        </tr>
    </table>
    <table width="950" cellspacing="0" align="center" >
        <tr>
        	<td width="90"><strong>Req. ID </strong></td><td width="160px"><? echo $dataArray[0][csf('requ_no')]; ?></td>
            <td width="100"><strong>Req. Date</strong></td><td width="160px"><? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
            <td><strong>Issue Basis</strong></td> <td><? echo $receive_basis_arr[$dataArray[0][csf('requisition_basis')]]; ?></td>
       </tr>
       <tr>
            <td><strong>Machine No</strong></td>
            <td>
				<? 
					$machine_data=sql_select("select machine_no from lib_machine_name where id='".$dataArray[0][csf("machine_id")]."'");
                	echo $machine_data[0][csf('machine_no')]; 
                ?>
            </td>
            <td><strong>Total Liq.(ltr)</strong></td><td><? echo $dataArray[0][csf("tot_liquor")]; ?></td>
            <td>Requisition for</td><td><? echo $dyeing_method[$dataArray[0][csf("method")]]; ?></td>
        </tr>
    </table>
	<div style="width:1240px; margin-top:10px" >
        <table align="right" cellspacing="0" width="1240" border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <tr bgcolor="#CCCCFF">
                    <th colspan="15" align="center"><strong>Dyes and Chemical Requisition</strong></th>
                </tr>
                <tr>   
                    <th width="40">SL</th>
                    <th width="90">Item Category</th>
                    <th width="110">Group</th>
                    <th width="130">Item Description</th>
                    <th width="100">Item Lot</th>
					<th width="50">UOM</th>
                    <th width="80">Dose Base</th>
                    <th width="75">Ratio</th>
					<th width="40">Seq. No</th>
                    <th width="80">Recipe Qty.</th>
                    <th width="80">Issue Qty.</th>
                    <th width="60" >KG</th>
                    <th width="60" >GM</th>
                    <th width="60">MG</th>
                    <th>Remark</th>
                </tr>
            </thead>
			<?  
            	$group_arr=return_library_array( "select id,item_name from lib_item_group where item_category in (5,6,7) and status_active=1 and is_deleted=0",'id','item_name');
				$sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.unit_of_measure, a.avg_rate_per_unit, b.dose_base, b.ratio, b.recipe_qnty, b.req_qny_edit,b.remarks,b.seq_no,b.item_lot from product_details_master a, dyes_chem_issue_requ_dtls b where a.id=b.product_id and b.mst_id=$data[1] and b.status_active=1 and b.is_deleted=0 and a.item_category_id in (5,7,6,23) and a.status_active=1 and a.is_deleted=0 order by b.seq_no";
				$sql_result= sql_select($sql);
				$i=1; 
				foreach($sql_result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					
					$req_qny_edit=explode(".",(string)$row[csf("req_qny_edit")]);
					$iss_qnty_kg=$req_qny_edit[0];
					if($iss_qnty_kg=="") $iss_qnty_kg=0;
					
					$iss_qnty_gm=substr($req_qny_edit[1],0,3);//$rem[0]; // floor($mg/1000);
					$iss_qnty_mg=substr($req_qny_edit[1],3,3);//$rem[1];//round($mg-$iss_qnty_gm*1000);
					$iss_qnty_gm= str_pad($iss_qnty_gm,3,"0",STR_PAD_RIGHT);
					$iss_qnty_mg= str_pad($iss_qnty_mg,3,"0",STR_PAD_RIGHT);
					
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                        <td><? echo $group_arr[$row[csf("item_group_id")]]; ?></td>
                        <td><? echo $row[csf("item_description")]; ?></td>
						 <td><? echo $row[csf("item_lot")]; ?></td>
                        <td align="center"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
                        <td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
                        <td align="center"><? echo number_format($row[csf("ratio")],6,'.',''); ?></td>
						<td align="center"><? echo $row[csf("seq_no")]; ?></td>
                        <td align="right"><? echo number_format($row[csf("recipe_qnty")],6,'.',''); ?></td>
                        <td align="right"><? echo number_format($row[csf("req_qny_edit")],6,'.',''); ?></td>
                        <td align="right"><? echo $iss_qnty_kg; ?></td>
                        <td align="right"><? echo $iss_qnty_gm; ?></td>
                        <td align="right"><? echo $iss_qnty_mg; ?></td>
                        <td align="left"><? echo $row[csf("remarks")]; ?></td>
                    </tr>
                <? 
					$i++;
					$recipe_qnty_grand+=$row[csf("recipe_qnty")];
					$req_qny_edit_grand+=$row[csf("req_qny_edit")];
					$req_value_grand+=$req_value;

					$iss_qnty_kg_grand+=$iss_qnty_kg;
					$iss_qnty_gm_grand+=$iss_qnty_gm;
					$iss_qnty_mg_grand+=$iss_qnty_mg;
				}
				?>
             <tr>
                <td colspan="9" align="right"><strong> Total :</strong></td>
                <td align="right"><?php echo number_format($recipe_qnty_grand,6,'.',''); ?></td>
                <td align="right"><?php echo number_format($req_qny_edit_grand,6,'.',''); ?></td>
				<td align="right"><?php echo $iss_qnty_kg_grand; ?></td>
                <td align="right"><?php echo $iss_qnty_gm_grand; ?></td>
				<td align="right"><?php echo $iss_qnty_mg_grand; ?></td>
                <td>&nbsp;</td>
            </tr> 
		</table>
        <br>
		 <?
            echo signature_table(15, $data[0], "950px");
         ?>
	</div>
</div>         
<?
exit();
}

?>