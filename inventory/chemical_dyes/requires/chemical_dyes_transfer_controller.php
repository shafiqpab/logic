<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$item_cate_credential_cond="5,6,7,22,23";

// user credential data prepare start
$userCredential = sql_select("SELECT store_location_id FROM user_passwd where id=$user_id");
$store_location_id = $userCredential[0][csf('store_location_id')];


if ($action=="load_room_rack_self_bin")
{
	$explodeData = explode('*', $data);
	$explodeData[11] = 'storeUpdateUptoDisable()';
	$data=implode('*', $explodeData);
	load_room_rack_self_bin("requires/chemical_dyes_transfer_controller",$data);
}

if ($action=="load_drop_down_store")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_store_name_to", 152, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[0]' and b.category_type in(5,6,7,22,23) and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1,"--Select store--",0,"reset_on_change(this.id);load_drop_down('requires/chemical_dyes_transfer_controller', this.value+'_'+$data[0], 'load_drop_floor','to_floor_td');storeUpdateUptoDisable();");
	exit();
}

if($action == "load_drop_floor")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];

	/*echo "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name";*/
	echo create_drop_down( "cbo_floor_to", 152, "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name","floor_id,floor_room_rack_name", 1, "--Select Floor--", 0, "reset_on_change(this.id);load_drop_down('requires/chemical_dyes_transfer_controller', this.value+'_'+$company_id+'_'+$store_id, 'load_drop_room','to_room_td');storeUpdateUptoDisable();" );
}

if($action == "load_drop_room")
{
	$data = explode("_", $data);
	$floor_id=$data[0];
	$company_id = $data[1];
	$store_id = $data[2];

	echo create_drop_down( "cbo_room_to", 152, "select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.room_id and b.floor_id='$floor_id' and b.store_id=$store_id and a.company_id='$company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name","room_id,floor_room_rack_name", 1, "--Select Room--", 0, "reset_on_change(this.id);load_drop_down('requires/chemical_dyes_transfer_controller', this.value+'_'+$company_id+'_'+$store_id, 'load_drop_rack','to_rack_td');storeUpdateUptoDisable();" );
}

if($action == "load_drop_rack")
{
	$data = explode("_", $data);
	$room_id=$data[0];
	$company_id = $data[1];
	$store_id = $data[2];
	echo create_drop_down( "txt_rack_to", 152, "select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and b.room_id='$room_id' and b.store_id=$store_id and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name","rack_id,floor_room_rack_name", 1, "--Select Rack--", 0, "reset_on_change(this.id);load_drop_down('requires/chemical_dyes_transfer_controller', this.value+'_'+$company_id+'_'+$store_id, 'load_drop_shelf','to_shelf_td');storeUpdateUptoDisable();" );
}

if($action == "load_drop_shelf")
{
	$data = explode("_", $data);
	$rack=$data[0];
	$company_id = $data[1];
	$store_id = $data[2];
	echo create_drop_down( "txt_shelf_to", 152, "select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and b.rack_id='$rack' and b.store_id=$store_id and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name","shelf_id,floor_room_rack_name", 1, "--Select Shelf--", 0, "reset_on_change(this.id);load_drop_down('requires/chemical_dyes_transfer_controller', this.value+'_'+$company_id+'_'+$store_id, 'load_drop_bin','to_bin_td');storeUpdateUptoDisable();" );
}

if($action == "load_drop_bin")
{
	$data = explode("_", $data);
	$shelf=$data[0];
	$company_id = $data[1];
	$store_id = $data[2];
	echo create_drop_down( "cbo_bin_to", 152, "select b.bin_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.bin_id and b.shelf_id=$shelf and b.store_id='$store_id' and a.company_id='$company_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.bin_id,a.floor_room_rack_name order by a.floor_room_rack_name","bin_id,floor_room_rack_name", 1, "--Select Bin--", 0, "" );
}


/*if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/chemical_dyes_transfer_controller",$data);
}*/

/*if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 160, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data $store_location_credential_cond and b.category_type in(5,6,7,22,23) group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select --", 0, "",0 );  	 
	exit();
}

if ($action=="load_drop_down_store_to")
{

	echo create_drop_down( "cbo_store_name_to", 160, "select a.id,a.store_name from lib_store_location a,lib_store_location_category  b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data and b.category_type in (5,6,7,22,23) group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "",0 );  	 
	   exit();
}*/


if($action=="populate_data_lib_data")
{
	$sql = sql_select("select auto_transfer_rcv, id from variable_settings_inventory where company_name = $data and variable_list = 29 and is_deleted = 0 and status_active = 1");
	$variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$data' and item_category_id in (5,6,7,22,23) and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	
	$sql_variable_requisition =  sql_select("select user_given_code_status as USER_GIVEN_CODE_STATUS, ID from variable_settings_inventory where company_name = $data and variable_list =30 and item_category_id=5 and is_deleted = 0 and status_active = 1");
	if($sql[0][csf("auto_transfer_rcv")]=="") $sql[0][csf("auto_transfer_rcv")]=0;
	echo $sql[0][csf("auto_transfer_rcv")]."__".$variable_inventory."__".$sql_variable_requisition[0]["USER_GIVEN_CODE_STATUS"];
	exit();
}

if($action=="chk_issue_requisition_variable")
{
	
    //$sql =  sql_select("select user_given_code_status as USER_GIVEN_CODE_STATUS,id from variable_settings_inventory where company_name = $data and variable_list =30 and item_category_id=5 and is_deleted = 0 and status_active = 1");
//	$return_data="";
//    if(count($sql)>0)
//	{
//		$return_data=$sql[0]['USER_GIVEN_CODE_STATUS'];
//	}
//	else
//	{ 
//		$return_data=0; 
//	}
//	
//	echo $return_data;
//	die;
}

// user credential data prepare end 

if ($action=="upto_variable_settings")
{
	//extract($_REQUEST);
	/*echo "select store_method from variable_settings_inventory where company_name='$cbo_company_id' and item_category_id=8 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1";die;*/
	//echo $variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$cbo_company_id' and item_category_id in (5,6,7,22,23) and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	//exit();
}

if ($action=="itemDescription_popup")
{
	echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
		
		/*$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });*/
		
		function js_set_value(data)
		{
			$('#product_id').val(data);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:910px;">
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:910px;margin-left:10px">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="750" class="rpt_table">
                <thead>
                   <th>Item Category</th>
                   <th>Item Group</th>
                    <th>Search By</th>
                    <th width="280" id="search_by_td_up">Please Enter Item Details</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                        <input type="hidden" name="product_id" id="product_id" class="text_boxes" value="">
                    </th>
                </thead>
                <tr class="general">
                    <td>
						<?
                            echo create_drop_down( "cbo_item_category", 160, $item_category,'', 1, '--Select Category--', $cbo_item_category, '','0',"$item_cate_credential_cond" );
                        ?>
                    </td>
                    <td>
                    <?
					  if($cbo_item_category!=0) $item_category_cond=" and item_category='$cbo_item_category'";
                      echo create_drop_down( "cbo_item_group_id", 130,"select id,item_name  from lib_item_group where   status_active=1 $item_category_cond","id,item_name", 1, "-- Select --", "", "","","","","","");
					?>
                    </td>
                    <td>
						<?
							$search_by_arr=array(1=>"Item Details",2=>"Product Id.");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td id="search_by_td">
                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('cbo_item_group_id').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('cbo_item_category').value+'_'+<? echo $cbo_store_name; ?>+'_<? echo $variable_lot; ?>', 'create_product_search_list_view', 'search_div', 'chemical_dyes_transfer_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
                    </td>
                </tr>
            </table>
        	<div style="margin-top:10px" id="search_div"></div> 
		</fieldset>
	</form>
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=='create_product_search_list_view')
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[3];
	$from_store =$data[5];
	$variable_lot =$data[6];
	$item_group_cond="";
	if($data[2]!=0) $str_cond.=" and a.item_group_id=$data[2] ";
	if($data[4]!=0) $str_cond.=" and a.item_category_id=$data[4]";
	if($from_store>0) $str_cond.=" and b.store_id=$from_store ";
	
	if($search_by==1) $search_field=" a.product_name_details";	 
	else if($search_by==2)  $search_field=" a.id";
	
 	//$sql="select id, company_id, supplier_id, product_name_details, lot,item_group_id, current_stock, brand from product_details_master where item_category_id in (5,6,7,22,23) and company_id=$company_id and $search_field like '$search_string' and current_stock>0 and status_active=1 and is_deleted=0 $item_group_cond  $item_category_cond";
	$lot_cond_prod="";
	if($variable_lot==1) $lot_cond_prod=" and b.lot is not null";
	$sql="select A.ID, A.COMPANY_ID, A.SUPPLIER_ID, A.PRODUCT_NAME_DETAILS, A.ITEM_GROUP_ID, A.CURRENT_STOCK AS ITEM_GLOBAL_STOCK, A.BRAND, A.UNIT_OF_MEASURE, B.ID AS STORE_PROD_ID, B.CONS_QTY AS CURRENT_STOCK, B.LOT, A.ITEM_DESCRIPTION, A.ITEM_CATEGORY_ID
	from product_details_master a, inv_store_wise_qty_dtls b 
	where a.id=b.prod_id and a.company_id=$company_id and $search_field like '$search_string' and b.cons_qty>0 and a.status_active in(1,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $str_cond $lot_cond_prod";
	
	//echo $sql;
	$sql_result=sql_select($sql);
	$item_group_arr=return_library_array( "select id,item_name  from lib_item_group where   status_active=1 ",'id','item_name');
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	//$arr=array(1=>$item_category,2=>$supplier_arr,3=>$item_group_arr);
	//echo  create_list_view("tbl_list_search", "Item ID,Category,Supplier,Item Group,Item Desciption,Lot No,Stock", "80,120,120,120,180,100","900","250",0, $sql, "js_set_value", "id,store_prod_id", "", 1, "0,item_category_id,supplier_id,item_group_id,0,0,0", $arr, "id,item_category_id,supplier_id,item_group_id,item_description,lot,current_stock", '','','0,0,0,0,0,0,2');
	
	?>
    <table cellpadding="0" cellspacing="0" border="1" rules="all" width="900" class="rpt_table">
        <thead>
			<tr>
				<th colspan="8"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
			</tr>
            <tr>
                <th width="30">SL</th>
                <th width="80">Item ID</th>
                <th width="120">Item Category</th>
                <th width="120">Supplier</th>
                <th width="120">Item Group</th>
                <th width="180">Item Desciption</th>
                <th width="100">Lot No</th> 
                <th>Stock</th>
            </tr>
        </thead>
    </table>
    <div style="width:900px; max-height:270; overflow-y:scroll;">
	    <table cellpadding="0" cellspacing="0" border="1" rules="all" width="882" class="rpt_table" id="list_view">
	    	<tbody>
	        	<?
				$i=1;$current_total=0;
				foreach($sql_result as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
	                <tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $i; ?>" onClick="js_set_value('<? echo $row['ID'].'__'.$row['STORE_PROD_ID'];?>')" style="cursor:pointer" align="center">
	                    <td width="30"><? echo $i; ?></td>
	                    <td width="80" style="word-break:break-all"><? echo $row['ID']; ?></td>
	                    <td width="120" style="word-break:break-all"><? echo $item_category[$row['ITEM_CATEGORY_ID']]; ?></td>
	                    <td width="120" style="word-break:break-all"><? echo $supplier_arr[$row['SUPPLIER_ID']]; ?></td>
	                    <td width="120" style="word-break:break-all"><? echo $item_group_arr[$row['ITEM_GROUP_ID']]; ?></td>
	                    <td width="180" style="word-break:break-all"><? echo $row['ITEM_DESCRIPTION']; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $row['LOT']; ?></td>
	                    <td align="right"><? echo number_format($row['CURRENT_STOCK'],4,'.',''); ?></td>
	                </tr>
	                <?
	                $i++;
					$current_total+=$row['CURRENT_STOCK'];
				}
				?>
	        </tbody>  
			<tfoot>
				<tr>			
					<td colspan="7" align="right"><b> Grand Total</b></td>
					<td align="right"><?= number_format($current_total,4) ?></td>
				</tr>
			</tfoot>
	    </table>
    </div>
    <?
	
	exit();
}

if($action=='populate_data_from_product_master')
{
	$data_ref=explode("**",$data);
	$prod_id=$data_ref[0];
	$from_store_id=$data_ref[1];
	$store_prod_id=$data_ref[2];
	
	$data_array=sql_select("select a.product_name_details, a.lot, a.current_stock as item_global_stock, a.avg_rate_per_unit, a.brand, a.item_category_id, a.unit_of_measure, b.cons_qty as current_stock, b.lot as batch_lot
	from product_details_master a, inv_store_wise_qty_dtls b 
	where a.id=b.prod_id and b.status_active=1 and b.is_deleted=0 and a.id='$prod_id' and b.store_id=$from_store_id and b.id=$store_prod_id");
	
	
	foreach ($data_array as $row)
	{ 
		$ebatch_lot=$row[csf("batch_lot")];
		echo "document.getElementById('hidden_product_id').value 			= '".$prod_id."';\n";
		echo "document.getElementById('txt_item_desc').value 				= '".$row[csf("product_name_details")]."';\n";
		echo "document.getElementById('txt_current_stock').value 			= '".$row[csf("current_stock")]."';\n";
		echo "document.getElementById('hidden_current_stock').value 		= '".$row[csf("current_stock")]."';\n";
		echo "document.getElementById('txt_rate').value 					= '".$row[csf("avg_rate_per_unit")]."';\n";
		echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category_id")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= ".$row[csf("unit_of_measure")].";\n";
		echo "document.getElementById('txt_yarn_lot_dis').value 			= '".$ebatch_lot."';\n";
		echo "document.getElementById('txt_yarn_lot').value 				= '".$ebatch_lot."';\n";
		//
	}
	exit();
}

if ($action=="itemTransfer_popup")
{
	echo load_html_head_contents("Item Transfer Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value(data)
		{
			$('#transfer_id').val(data);
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
<div align="center" style="width:980px;">
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:960px;margin-left:10px">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="750" class="rpt_table">
                <thead>
                    <th width="100">Transfer Year</th>
                    <th width="150">Search By</th>
                    <th width="150" id="search_by_td_up">Please Enter Transfer ID</th>
                    <th>Transfer Date Range</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                        <input type="hidden" name="transfer_id" id="transfer_id" class="text_boxes" value="">
                    </th>
                </thead>
                <tr class="general">
                	<td align="center"><? echo create_drop_down( "cbo_year_selection", 100, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td>
						<?
							$search_by_arr=array(1=>"Transfer ID",2=>"Challan No.");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td id="search_by_td">
                        <input type="text" style="width:137px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                    </td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_transfer_criteria; ?>+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_transfer_search_list_view', 'search_div', 'chemical_dyes_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                    
                </tr>
                <tr>                  
                    <td align="center" height="40" valign="middle" colspan="5">
                        <? echo load_month_buttons(1);  ?>
                    </td>
                </tr>   
            </table>
        	<div style="margin-top:10px" id="search_div"></div> 
		</fieldset>
	</form>
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=='create_transfer_search_list_view')
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	$trans_criteria =$data[3];
	$selected_year =$data[4];
	$from_date =$data[5];
	$to_date =$data[6];
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	if($search_by==1)
		$search_field="transfer_system_id";	
	else
		$search_field="challan_no";

	if($db_type==0)
	{ 
		if ($from_date!="" &&  $to_date!="") $transfer_date_cond = "and b.transfer_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'"; else $transfer_date_cond ="";
		$year_cond=" and YEAR(insert_date)=$selected_year";  
	}
	else
	{
		if ($from_date!="" &&  $to_date!="") $transfer_date_cond = "and b.transfer_date between '".change_date_format($from_date, "", "",1)."' and '".change_date_format($to_date, "", "",1)."'"; else $transfer_date_cond ="";
		$year_cond=" and to_char(b.insert_date,'YYYY')=$selected_year";
	}
	
	if($db_type==0) $year_field="YEAR(b.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(b.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
 	//$sql="select id, $year_field transfer_prefix_number, transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria,item_category from inv_item_transfer_mst where entry_form=55 and company_id=$company_id  $transfer_date_cond $year_cond and $search_field like '$search_string' and transfer_criteria in($trans_criteria) and status_active=1 and is_deleted=0 order by id";

 	$sql="select b.transfer_criteria, b.company_id, b.to_company, b.is_posted_account, b.transfer_prefix_number, b.transfer_system_id, b.challan_no, b.transfer_date, a.id, a.mst_id, a.from_store, a.to_store, a.trans_id ,a.to_trans_id, a.item_category, $year_field 
	from inv_item_transfer_dtls a, inv_item_transfer_mst b 
	where b.entry_form=55 and b.company_id=$company_id  $transfer_date_cond $year_cond and $search_field like '$search_string' and b.transfer_criteria in($trans_criteria) and b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	order by a.mst_id desc";
 	$qry_result=sql_select($sql);
 	foreach ($qry_result as  $row) 
	{
		//
		$trans_arr[$row[csf("mst_id")]]["transfer_criteria"] =$row[csf("transfer_criteria")];
		$trans_arr[$row[csf("mst_id")]]["year"] =$row[csf("year")];
		$trans_arr[$row[csf("mst_id")]]["company_id"] =$row[csf("company_id")];
		$trans_arr[$row[csf("mst_id")]]["to_company"] =$row[csf("to_company")];
		$trans_arr[$row[csf("mst_id")]]["transfer_prefix_number"] =$row[csf("transfer_prefix_number")];
		$trans_arr[$row[csf("mst_id")]]["transfer_system_id"] =$row[csf("transfer_system_id")];
		$trans_arr[$row[csf("mst_id")]]["challan_no"] =$row[csf("challan_no")];
		$trans_arr[$row[csf("mst_id")]]["transfer_date"] =$row[csf("transfer_date")];
		$trans_arr[$row[csf("mst_id")]]["from_store"] .=$row[csf("from_store")].',';
		$trans_arr[$row[csf("mst_id")]]["to_store"] .=$row[csf("to_store")].',';
		$trans_arr[$row[csf("mst_id")]]["item_category"] .=$row[csf("item_category")].',';
	}
	//echo $sql;
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="950" >
        <thead>
            <th width="30">SL</th>
            <th width="40">Transfer ID</th>
            <th width="40">Year</th>
            <th width="80">Challan No</th>
            <th width="100">Company</th>
            <th width="100">To Company</th>
            <th width="60">Transfer Date</th>
            <th width="120">Transfer Criteria</th>
            <th width="120">From Store</th>
            <th width="120">To Store</th>
            <th>Item Category</th>
        </thead>
        </table>
        <div style="width:950px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="930" class="rpt_table" id="tbl_po_list">
        <tbody>
            <? 
            $i=1; 
            foreach($trans_arr as $mst_id=> $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
               	// $from_store = $row['from_store'];
				$from_store=array_unique(explode(",",$row['from_store']));
				$to_store=array_unique(explode(",",$row['to_store']));
				$item_categorys=array_unique(explode(",",$row['item_category']));
				$from_store_name=""; $to_store_name="";	 $category_name="";	
				foreach ($from_store as $store_id){
					if($from_store_name=="") $from_store_name=$store_arr[$store_id]; else $from_store_name.=','.$store_arr[$store_id];
				}

				foreach ($to_store as $store_id){
					if($to_store_name=="") $to_store_name=$store_arr[$store_id]; else $to_store_name.=','.$store_arr[$store_id];
				}

				foreach ($item_categorys as $cat){
					if($category_name=="") $category_name=$item_category[$cat]; else $category_name.=','.$item_category[$cat];
				}

				
				//$buyer_style=implode(",",array_unique(explode(",",$buyer_styles)));
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $mst_id; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="40" style="text-align:center;"><? echo $row['transfer_prefix_number']; ?></td>
                    <td width="40" style="text-align:center;"><? echo $row['year']; ?></td>
                    <td width="80"><? echo $row['challan_no']; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $company_arr[$row['company_id']]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $company_arr[$row['to_company']]; ?></td>
                    <td width="60" style="text-align:center;"><? echo change_date_format($row['transfer_date']); ?></td>
                    <td width="120" style="text-align:center;"><? echo $item_transfer_criteria[$row['transfer_criteria']]; ?></td>	
                    <td width="120" style="text-align:center;"><? echo chop($from_store_name,','); ?></td>	
                    <td width="120" style="text-align:center;"><? echo chop($to_store_name,','); ?></td>	
                    <td style="word-break:break-all"><? echo chop($category_name,','); ?></td>
                </tr>
				<? 
                $i++; 
            } 
            ?>
        </tbody>
    </table>
	<?
	/*$arr=array(3=>$company_arr,5=>$item_transfer_criteria,6=>$item_category);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria,Item Category", "70,60,100,120,90,140","760","250",0, $sql, "js_set_value", "id", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category", '','','0,0,0,0,3,0,0');*/
	
	exit();
}

if($action=='populate_data_from_transfer_master')
{
	
	$data_array=sql_select("select is_posted_account,transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, to_company, requisition_no, requisition_id from inv_item_transfer_mst where id='$data'");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row[csf("transfer_criteria")]."';\n";
		
		//echo "active_inactive(".$row[csf("transfer_criteria")].");\n";
		
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_requisition_no').value 			= '".$row[csf("requisition_no")]."';\n";
		echo "document.getElementById('hidden_req_id').value 				= '".$row[csf("requisition_id")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";
		echo "document.getElementById('cbo_company_id_to').value 			= '".$row[csf("to_company")]."';\n";


		//echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n"; 
		if($row[csf("is_posted_account")]==1)
		{
			echo "disable_enable_fields( 'cbo_transfer_criteria*cbo_company_id*cbo_company_id_to*txt_challan_no*txt_transfer_date', 1, '', '' );\n"; // disable true
		}
		else
		{
			echo "disable_enable_fields( 'cbo_transfer_criteria*cbo_company_id*cbo_company_id_to', 1, '', '' );\n"; // disable true

		}
		
		$msg="Already Posted in Accounts";
        if($row[csf("is_posted_account")]==1){
			echo "$('#posted_account_td').text('".$msg."');\n";
		}else{
			
		}
		
		exit();
	}
}

if($action=="show_transfer_listview")
{
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id in (5,6,7,22,23) ","id","product_name_details");
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	
	$sql="select id, mst_id, from_store, to_store, from_prod_id, transfer_qnty, yarn_lot from inv_item_transfer_dtls where mst_id='$data' and status_active = '1' and is_deleted = '0'";
	
	$arr=array(0=>$store_arr,1=>$store_arr,2=>$product_arr,5=>$brand_arr);
	 
	echo  create_list_view("list_view", "From Store,To Store,Item Description,Lot,Transfered Qnty", "130,130,220,100","680","200",0, $sql, "get_php_form_data", "id", "'populate_transfer_details_form_data'", 0, "from_store,to_store,from_prod_id,0,0", $arr, "from_store,to_store,from_prod_id,yarn_lot,transfer_qnty", "requires/chemical_dyes_transfer_controller",'','0,0,0,0,5');
	
	exit();
}

if($action=='populate_transfer_details_form_data')
{

	//echo "select b.is_posted_account,a.id,a. mst_id, a.from_store, a.to_store, a.from_prod_id, a.to_prod_id, a.transfer_qnty,a.trans_id ,a.to_trans_id,  a.rate,a.item_category, a.transfer_value, a.yarn_lot, a.brand_id from inv_item_transfer_dtls a, inv_item_transfer_mst b where a.id='$data' and b.id=a.mst_id";die;
	$data_array=sql_select("select b.transfer_criteria,b.company_id,b.to_company,b.is_posted_account,a.id,a. mst_id, a.from_store, a.to_store,a.floor_id,a.room,a.rack,a.shelf,a.to_floor_id,a.to_room,a.to_rack,a.to_shelf, a.from_prod_id, a.to_prod_id, a.transfer_qnty,a.trans_id ,a.to_trans_id,  a.rate,a.item_category, a.transfer_value, a.yarn_lot, a.brand_id, a.uom, a.requisition_dtls_id from inv_item_transfer_dtls a, inv_item_transfer_mst b where a.id='$data' and b.id=a.mst_id");
	foreach ($data_array as $row)
	{ 
		if ($row[csf("transfer_criteria")]==1) {
			$company_id=$row[csf("to_company")];
		}
		else
		{
			$company_id=$row[csf("company_id")];
		}
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "load_room_rack_self_bin('requires/chemical_dyes_transfer_controller*5_6_7_22_23', 'store','from_store_td', $('#cbo_company_id').val(),'"."',this.value);\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("from_store")]."';\n";
		if($row[csf("floor_id")]>0)
		{
			echo "load_room_rack_self_bin('requires/chemical_dyes_transfer_controller*5_6_7_22_23', 'floor','floor_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."',this.value);\n";
		}
		echo "document.getElementById('cbo_floor').value 				= '".$row[csf("floor_id")]."';\n";
		if($row[csf("room")]>0)
		{
			echo "load_room_rack_self_bin('requires/chemical_dyes_transfer_controller*5_6_7_22_23', 'room','room_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."',this.value);\n";
		}
		echo "document.getElementById('cbo_room').value 				= '".$row[csf("room")]."';\n";
		if($row[csf("rack")]>0)
		{
			echo "load_room_rack_self_bin('requires/chemical_dyes_transfer_controller*5_6_7_22_23', 'rack','rack_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		}
		echo "document.getElementById('txt_rack').value 				= '".$row[csf("rack")]."';\n";
		if($row[csf("shelf")]>0)
		{
			echo "load_room_rack_self_bin('requires/chemical_dyes_transfer_controller*5_6_7_22_23', 'shelf','shelf_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";
		}
		echo "document.getElementById('txt_shelf').value 				= '".$row[csf("shelf")]."';\n";

		echo "load_room_rack_self_bin('requires/chemical_dyes_transfer_controller*5_6_7_22_23*cbo_store_name_to', 'store','to_store_td', '".$company_id."','"."',this.value);\n";
		echo "document.getElementById('cbo_store_name_to').value 				= '".$row[csf("to_store")]."';\n";
		if($row[csf("to_floor_id")]>0)
		{
			echo "load_room_rack_self_bin('requires/chemical_dyes_transfer_controller*5_6_7_22_23*cbo_floor_to', 'floor','floor_td_to', '".$company_id."','"."','".$row[csf('to_store')]."',this.value);\n";
		}
		echo "document.getElementById('cbo_floor_to').value 			= '".$row[csf("to_floor_id")]."';\n";
		if($row[csf("to_room")]>0)
		{
			echo "load_room_rack_self_bin('requires/chemical_dyes_transfer_controller*5_6_7_22_23*cbo_room_to', 'room','room_td_to', '".$company_id."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."',this.value);\n";
		}
		echo "document.getElementById('cbo_room_to').value 				= '".$row[csf("to_room")]."';\n";
		if($row[csf("to_rack")]>0)
		{
			echo "load_room_rack_self_bin('requires/chemical_dyes_transfer_controller*5_6_7_22_23*txt_rack_to', 'rack','rack_td_to', '".$company_id."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."',this.value);\n";
		}
		echo "document.getElementById('txt_rack_to').value 				= '".$row[csf("to_rack")]."';\n";
		if($row[csf("to_shelf")]>0)
		{
			echo "load_room_rack_self_bin('requires/chemical_dyes_transfer_controller*5_6_7_22_23*txt_shelf_to', 'shelf','shelf_td_to', '".$company_id."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."','".$row[csf('to_rack')]."',this.value);\n";
		}
		echo "document.getElementById('txt_shelf_to').value 			= '".$row[csf("to_shelf")]."';\n";


		echo "load_drop_down('requires/chemical_dyes_transfer_controller', $company_id, 'load_drop_down_store','store_td_to');\n";
		echo "document.getElementById('cbo_store_name_to').value 		= '".$row[csf("to_store")]."';\n";

		echo "load_drop_down('requires/chemical_dyes_transfer_controller','".$row[csf("to_store")].'_'.$company_id."', 'load_drop_floor','to_floor_td');\n";
		echo "document.getElementById('cbo_floor_to').value 			= '".$row[csf("to_floor_id")]."';\n";
		if($row[csf("to_floor_id")])
		{
			echo "load_drop_down('requires/chemical_dyes_transfer_controller','".$row[csf("to_floor_id")].'_'.$company_id.'_'.$row[csf("to_store")]."', 'load_drop_room','to_room_td');\n";
		}
		echo "document.getElementById('cbo_room_to').value 				= '".$row[csf("to_room")]."';\n";
		if($row[csf("to_room")])
		{
			echo "load_drop_down('requires/chemical_dyes_transfer_controller','".$row[csf("to_room")].'_'.$company_id.'_'.$row[csf("to_store")]."', 'load_drop_rack','to_rack_td');\n";
		}
		echo "document.getElementById('txt_rack_to').value 				= '".$row[csf("to_rack")]."';\n";
		if($row[csf("to_rack")])
		{
			echo "load_drop_down('requires/chemical_dyes_transfer_controller','".$row[csf("to_rack")].'_'.$company_id.'_'.$row[csf("to_store")]."', 'load_drop_shelf','to_shelf_td');\n";
		}
		echo "document.getElementById('txt_shelf_to').value 			= '".$row[csf("to_shelf")]."';\n";
		if($row[csf("to_shelf")])
		{
			echo "load_drop_down('requires/chemical_dyes_transfer_controller','".$row[csf("to_shelf")].'_'.$company_id.'_'.$row[csf("to_store")]."', 'load_drop_bin','to_bin_td');\n";
		}
		echo "document.getElementById('cbo_bin_to').value 				= '".$to_bin_box."';\n";

		echo "document.getElementById('txt_yarn_lot').value 				= '".$row[csf("yarn_lot")]."';\n";
		echo "document.getElementById('txt_yarn_lot_dis').value 			= '".$row[csf("yarn_lot")]."';\n";
		echo "document.getElementById('txt_transfer_qnty').value 			= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('txt_rate').value 					= '".$row[csf("rate")]."';\n";
		echo "document.getElementById('txt_transfer_value').value 			= '".$row[csf("transfer_value")]."';\n";
		echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";
		//echo "document.getElementById('txt_yarn_brand').value 				= '".$brand_arr[$row[csf("brand_id")]]."';\n";
		echo "document.getElementById('hidden_transfer_qnty').value 		= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('hidden_product_id').value 			= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('previous_from_prod_id').value 		= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('previous_to_prod_id').value 			= '".$row[csf("to_prod_id")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("uom")]."';\n";
		echo "document.getElementById('hidden_req_dtls_id').value 		= '".$row[csf("requisition_dtls_id")]."';\n";
		echo "disable_enable_fields('cbo_store_name*cbo_item_category*txt_item_desc',1);\n";
		
		//$sql=sql_select("select product_name_details,current_stock,avg_rate_per_unit from product_details_master where id=".$row[csf('from_prod_id')]);
		$sql=sql_select("select a.product_name_details, a.current_stock as item_global_stock, a.avg_rate_per_unit, sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as current_stock  from product_details_master a, inv_transaction b where a.id=b.prod_id and a.id='".$row[csf('from_prod_id')]."' and b.store_id='".$row[csf("from_store")]."' and b.status_active=1 and b.is_deleted=0 group by a.product_name_details, a.current_stock, a.avg_rate_per_unit" );
		
		$stock=$sql[0][csf("current_stock")]+$row[csf("transfer_qnty")];
		
		echo "document.getElementById('txt_item_desc').value 				= '".$sql[0][csf("product_name_details")]."';\n";
		echo "document.getElementById('txt_current_stock').value 			= '".$stock."';\n";
		echo "document.getElementById('hidden_current_stock').value 		= '".$stock."';\n";
		
		//$sql_trans=sql_select("select id, transaction_type from inv_transaction where mst_id=".$row[csf('mst_id')]." and item_category=".$row[csf("item_category")]." and transaction_type in(5,6) order by id asc");
        //echo "select id, transaction_type from inv_transaction where mst_id=$row[mst_id] and item_category=1 and transaction_type in(5,6) order by id asc";die;
		echo "document.getElementById('update_trans_issue_id').value 		= '".$row[csf("trans_id")]."';\n";
		echo "document.getElementById('update_trans_recv_id').value 		= '".$row[csf("to_trans_id")]."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n"; 
		if($row[csf("is_posted_account")]==1)
		{
			echo "disable_enable_fields( 'txt_transfer_qnty', 1, '', '' );\n"; // disable true
		}
		else
		{
			echo "disable_enable_fields( 'txt_transfer_qnty', 0, '', '' );\n"; // disable true
		}
		exit();
	}
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$variable_lot=str_replace("'","",$variable_lot);
	
	      
	$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
	$transter_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
	$update_trans_issue_id=str_replace("'","",$update_trans_issue_id);
	$update_trans_recv_id=str_replace("'","",$update_trans_recv_id);
	$txt_transfer_qnty=str_replace("'","",$txt_transfer_qnty);
	$txt_rate=str_replace("'","",$txt_rate);
	$txt_transfer_value=str_replace("'","",$txt_transfer_value);
	
	$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and transaction_type in (1,4,5)", "max_date");
	if (strtotime($transter_date) < strtotime($max_recv_date)) 
    {
		echo "20**Transfer Date Can not Be Less Than Last Receive Date Of This Lot";
		die;
	}
	

	
	$up_cond="";$prev_iss_qnty=0;$up_tr_cond=$up_lot_cond="";
	if($update_trans_issue_id > 0 && $update_trans_recv_id > 0 )
	{
		$up_tr_cond=" and id not in($update_trans_issue_id,$update_trans_recv_id)";
		
		if($variable_lot==1 && str_replace("'", "", $txt_yarn_lot)!="") 
		{
			$up_lot_cond=" and trim(batch_lot)=trim('".str_replace("'", "", $txt_yarn_lot)."')";
		}
		
		$trans_sql=sql_select("select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as bal
		from inv_transaction where status_active=1 and is_deleted=0 and prod_id=$hidden_product_id and store_id=$cbo_store_name_to $up_tr_cond $up_lot_cond");
		$stockQnty=$trans_sql[0][csf("bal")]*1;
		
		if($stockQnty < 0)
		{
			 echo "20**Transfer Quantity Not Allow Over Stock Quantity.";disconnect($con);die;
		}
		$up_cond=" and b.id not in($update_trans_issue_id,$update_trans_recv_id)";
		$prev_issu_sql=sql_select("select cons_quantity from inv_transaction where id=$update_trans_issue_id and status_active=1");
		$prev_iss_qnty=$prev_issu_sql[0][csf("cons_quantity")];
	}
	else
	{
		if($update_trans_issue_id > 0)
		{
			$up_cond=" and b.id not in($update_trans_issue_id)";
			$prev_issu_sql=sql_select("select cons_quantity from inv_transaction where id=$update_trans_issue_id and status_active=1");
			$prev_iss_qnty=$prev_issu_sql[0][csf("cons_quantity")];
		}
	}
	
	$store_up_conds="";
	if($update_trans_issue_id >0) 
	{
		if($update_trans_recv_id >0) $store_up_conds=" and id not in($update_trans_issue_id,$update_trans_recv_id)";
		else $store_up_conds=" and id not in($update_trans_issue_id)";
	}
	
	$store_stock_sql="select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as BALANCE_STOCK, sum((case when transaction_type in(1,4,5) then store_amount else 0 end)-(case when transaction_type in(2,3,6) then store_amount else 0 end)) as BALANCE_AMT 
	from inv_transaction 
	where status_active=1 and prod_id=$hidden_product_id and store_id=$cbo_store_name $store_up_conds";
	//echo "20**$store_stock_sql";disconnect($con);die;
	$store_stock_sql_result=sql_select($store_stock_sql);
	$store_item_rate=0;
	if($store_stock_sql_result[0]["BALANCE_AMT"]!=0 && $store_stock_sql_result[0]["BALANCE_STOCK"]!=0)
	{
		$store_item_rate=$store_stock_sql_result[0]["BALANCE_AMT"]/$store_stock_sql_result[0]["BALANCE_STOCK"];
	}
	$issue_store_value=$txt_transfer_qnty*$store_item_rate;
	

	if($prev_iss_qnty=="") $prev_iss_qnty=0;
	$trans_lot_cond="";
	if($variable_lot==1 && str_replace("'", "", $txt_yarn_lot)!="") 
	{
		$trans_lot_cond=" and trim(b.batch_lot)=trim('".str_replace("'", "", $txt_yarn_lot)."')";
	}
	
	$store_sql="select a.product_name_details, a.current_stock as item_global_stock, a.avg_rate_per_unit, sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as current_stock 
	from product_details_master a, inv_transaction b 
	where a.id=b.prod_id and a.id=$hidden_product_id and b.store_id=$cbo_store_name	and b.status_active=1 and b.is_deleted=0 $up_cond $trans_lot_cond  
	group by a.product_name_details, a.current_stock, a.avg_rate_per_unit";
	//echo "10**".$store_sql;die;
	$store_result=sql_select($store_sql);
	$item_global_stock=$store_result[0][csf("item_global_stock")]+$prev_iss_qnty;
	$store_current_stock=$store_result[0][csf("current_stock")];
	if(str_replace("'","",$operation)!=2 )
	{
		if($txt_transfer_qnty > $store_current_stock || $txt_transfer_qnty > $item_global_stock )
		{
			echo "20**Transfer Qnty Not Allow Over Stock"; die;
		}
	}
	
	
	if(str_replace("'","",$cbo_transfer_criteria)==1)
	{
		$sql_lot_variable = sql_select("select auto_transfer_rcv, company_name from variable_settings_inventory where variable_list = 29 and is_deleted = 0 and status_active = 1");
		$lib_lot_data=array();
		foreach($sql_lot_variable as $row)
		{
			$lib_lot_data[$row[csf("company_name")]]=$row[csf("auto_transfer_rcv")];
		}
		unset($sql_lot_variable);
		if($lib_lot_data[str_replace("'","",$cbo_company_id)] != $lib_lot_data[str_replace("'","",$cbo_company_id_to)])
		{
			echo "20**Variable Setting Of Lot Maintain Should Be Same For Both Company"; die;
		}
	}

	if(str_replace("'","",$update_id)!="")
	{
		$is_acknowledge = return_field_value("b.id id", "inv_item_transfer_mst a,inv_item_trans_acknowledgement b", "a.id=b.challan_id and  a.id=$update_id and a.status_active=1 and a.is_acknowledge=1", "id");
		if($is_acknowledge != "" )
		{
			echo "20**Update not allowed. This Transfer Challan is already Acknowledged.\nAcknowledge System ID = $is_acknowledge";
			die;
		}
	}
	
	
	if($txt_rate<=0 || $txt_rate=="")
	{
		echo "20**Rate Not Found";
		die;
	}
	
	if( $operation !=2 )
	{
		if(str_replace("'","",$update_dtls_id)!="") $upConds=" and id <> $update_dtls_id ";
		$sql_dup=sql_select("select id from INV_ITEM_TRANSFER_DTLS where status_active=1 and is_deleted=0 and mst_id=$update_id and from_prod_id=$hidden_product_id $upConds ");
		if(count($sql_dup)>0)
		{
			echo "20**Duplicate Item Not Allow in Same MRR.";die;
		}
	}
	
	//echo "10** select auto_transfer_rcv from variable_settings_inventory where company_name=$cbo_company_id_to and item_category_id=5 and status_active=1 and variable_list= 27";die;
		
    $variable_auto_rcv = return_field_value("auto_transfer_rcv", "variable_settings_inventory", " company_name=$cbo_company_id_to and item_category_id=5 and status_active=1 and variable_list= 27", "auto_transfer_rcv");

	if($variable_auto_rcv == '')
	{
		$variable_auto_rcv = 1; // if auto receive yes(1), then no need to acknowledgement
	}
	
	
	//LIFO/FIFO Start-----------------------------------------------//
	$isLIFOfifo=return_field_value("store_method","variable_settings_inventory","company_name=$cbo_company_id and variable_list=17 and item_category_id=$cbo_item_category and status_active=1 and is_deleted=0");
	if($isLIFOfifo==2) $cond_lifofifo=" DESC"; else $cond_lifofifo=" ASC";
	
	//echo "10** $variable_auto_rcv";die;
	
	    
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		
		//echo "10**".$variable_auto_rcv; die;
		
		$transfer_recv_num=''; $transfer_update_id='';
		
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			$new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst",$con,1,$cbo_company_id,'CTE',55,date("Y",time()) ));
			$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);

			
			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, transfer_criteria, to_company, from_order_id, to_order_id, requisition_no, requisition_id, entry_form, inserted_by, insert_date";
			
			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$txt_transfer_date.",".$cbo_transfer_criteria.",".$cbo_company_id_to.",0,0,".$txt_requisition_no.",".$hidden_req_id.",55,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			//echo "insert into inv_item_transfer_mst (".$field_array.") values ".$data_array;die;
			
			$transfer_recv_num=$new_transfer_system_id[0];
			$transfer_update_id=$id;
		}
		else
		{
			$field_array_update="challan_no*transfer_date*to_company*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$cbo_company_id_to."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$transfer_recv_num=str_replace("'","",$txt_system_id);
			$transfer_update_id=str_replace("'","",$update_id);

			if($variable_auto_rcv == 2) // if auto receive yes(1), then no need to acknowledgement
			{
				//echo "10**fail=2";die;
				$pre_saved_store=sql_select("select a.id, b.from_store, b.to_store from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id = b.mst_id and a.entry_form in(55) and a.id = $transfer_update_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 group by a.id, b.from_store, b.to_store");

				if( ($pre_saved_store[0][csf("from_store")]  !=str_replace("'", "", $cbo_store_name)) || ($pre_saved_store[0][csf("to_store")]  !=str_replace("'", "", $cbo_store_name_to)) )
				{
					echo "20**Duplicate From Store and To Store is not allowed in same MRR";
					disconnect($con);
					die;
				}
			}
		}
		
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con); 
		$field_array_trans="id, mst_id,transaction_criteria, company_id, prod_id, item_category, transaction_type, transaction_date, store_id,floor_id,room,rack,self, cons_uom, cons_quantity, cons_rate, cons_amount, balance_qnty, balance_amount, inserted_by, insert_date, batch_lot,store_rate,store_amount";
		
		//$id_dtls=return_next_id( "id", "inv_item_transfer_dtls", 1 ) ;
		$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);

		$field_array_dtls="id, mst_id, from_prod_id, to_prod_id, yarn_lot, item_group, from_store,floor_id,room,rack,shelf, to_store,to_floor_id,to_room,to_rack,to_shelf, item_category, transfer_qnty, rate, transfer_value, uom, requisition_mst_id, requisition_dtls_id,trans_id,to_trans_id, inserted_by, insert_date,store_rate,store_amount";

		$field_array_dtls_ac="id, mst_id, dtls_id, is_acknowledge, from_prod_id, to_prod_id, yarn_lot, from_store, floor_id, room, rack, shelf, to_store, to_floor_id, to_room, to_rack, to_shelf, item_category, transfer_qnty, rate, transfer_value, uom, requisition_mst_id, requisition_dtls_id, remarks, inserted_by, insert_date,store_rate,store_amount";
		
		
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			//echo $hidden_product_id;die;
			$data_prod=sql_select("select SUPPLIER_ID, CURRENT_STOCK, AVG_RATE_PER_UNIT, STOCK_VALUE, SUB_GROUP_NAME, ITEM_SIZE, MODEL, ITEM_NUMBER, ITEM_CODE from product_details_master where id=$hidden_product_id");
			
			$presentStock=$data_prod[0]['CURRENT_STOCK']-$txt_transfer_qnty;
			$presentAvgRate=$data_prod[0]['AVG_RATE_PER_UNIT'];
			$presentStockValue=0;
			if ($presentStock != 0){				
				$presentStockValue=$presentStock*$presentAvgRate;
			}			
			
			$field_array_prodUpdate="avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*updated_by*update_date";
			$data_array_prodUpdate=number_format($presentAvgRate,10,'.','')."*".$txt_transfer_qnty."*".$presentStock."*".number_format($presentStockValue,8,'.','')."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$prod_lot_cond=""; 
			if($variable_lot==1 && str_replace("'", "", $txt_yarn_lot)!="") 
			{
				$prod_lot_cond=" and trim(lot)=trim('".str_replace("'", "", $txt_yarn_lot)."')";
			}
			$supplier_id=$data_prod[0]['SUPPLIER_ID'];
			$sub_group_name=$data_prod[0]['SUB_GROUP_NAME'];
			$item_size=$data_prod[0]['ITEM_SIZE'];
			$model=$data_prod[0]['MODEL'];
			$item_number=$data_prod[0]['ITEM_NUMBER'];
			$item_code=$data_prod[0]['ITEM_CODE'];
			$prod_conds="";
			if(str_replace("'","",$sub_group_name)=='') $prod_conds.=" and sub_group_name is null"; else $prod_conds.=" and sub_group_name='$sub_group_name'";
			if(str_replace("'","",$item_size)=='') $prod_conds .=" and item_size is null"; else $prod_conds.=" and item_size='$item_size'";
			if(str_replace("'","",$model)=='') $prod_conds .=" and model is null"; else $prod_conds.=" and model='$model'";
			if(str_replace("'","",$item_number)=='') $prod_conds .=" and item_number is null"; else $prod_conds.=" and item_number='$item_number'";
			if(str_replace("'","",$item_code)=='') $prod_conds .=" and item_code is null"; else $prod_conds.=" and item_code='$item_code'";
			
			$row_prod=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id_to and item_category_id=$cbo_item_category and trim(product_name_details)=trim($txt_item_desc) and status_active in(1,3) and is_deleted=0 $prod_lot_cond $prod_conds");
			if(count($row_prod)>0)
			{
				$product_id=$row_prod[0][csf('id')];
				$stock_qnty=$row_prod[0][csf('current_stock')];
				$stock_value=$row_prod[0][csf('stock_value')];
				$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];
				$curr_stock_qnty=$stock_qnty+$txt_transfer_qnty;
				$add_stock_value=$txt_transfer_qnty*$presentAvgRate;
				$curr_stock_value=0;
				if ($curr_stock_qnty != 0){
					$curr_stock_value=$stock_value+$add_stock_value;
					$avg_rate_per_unit=$curr_stock_value/$curr_stock_qnty;
				}
				//$curr_stock_value=$curr_stock_qnty*$avg_rate_per_unit;

				if($variable_auto_rcv==1)
				{
					$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";				
					$data_array_prod_update="".number_format($avg_rate_per_unit,10,'.','')."*".$txt_transfer_qnty."*".$curr_stock_qnty."*".number_format($curr_stock_value,8,'.','')."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				}	
				
			}
			else
			{
				//$product_id=return_next_id( "id", "product_details_master", 1 ) ;
				$product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				
				$curr_stock_qnty=$txt_transfer_qnty;				

				if($variable_auto_rcv==1)
				{
					$avg_rate_per_unit=$data_prod[0]['AVG_RATE_PER_UNIT'];
					$stock_value=0;
					if ($curr_stock_qnty != 0){						
						$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
					}	
					$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, inserted_by, insert_date) 
					select	
					'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, trim(product_name_details), lot, item_code, unit_of_measure, avg_rate_per_unit, $txt_transfer_qnty, $curr_stock_qnty, $stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, inserted_by, insert_date from product_details_master where id=$hidden_product_id";					
					
				}
				else
				{
					$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, inserted_by, insert_date) 
					select	
					'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, trim(product_name_details), lot, item_code, unit_of_measure, avg_rate_per_unit, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, inserted_by, insert_date from product_details_master where id=$hidden_product_id";
				}				
				//echo $sql_prod_insert;die;
			}
			
                        
			 //----------------Check Last Receive Date for Transfer Out---------------->>>>>>>
			$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and transaction_type in (1,4,5)", "max_date");      
			if($max_recv_date !="")
			{
				$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_recv_date) 
				{
					echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}
			}
			
			//----------------Check Last Issue Date for Transfer In---------------->>>>>>>
			$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$product_id and transaction_type in (2,3,6)", "max_date");      
			if($max_issue_date !="")
			{
				$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_issue_date) 
				{
					echo "20**Transfer In Date Can not Be Less Than Last Issue Date Of This Lot";
					disconnect($con);
					die;
				}
			}	
                       
                        
			$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_transfer_criteria.",".$cbo_company_id.",".$hidden_product_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_uom.",".$txt_transfer_qnty.",".number_format($txt_rate,10,'.','').",".number_format($txt_transfer_value,8,'.','').",".$txt_transfer_qnty.",".number_format($txt_transfer_value,8,'.','').",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_yarn_lot.",".number_format($store_item_rate,10,'.','').",".number_format($issue_store_value,8,'.','').")";

			$recv_trans_id=0;
			if($variable_auto_rcv==1)
			{
				$recv_trans_id=return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$data_array_trans.=",(".$recv_trans_id.",".$transfer_update_id.",".$cbo_transfer_criteria.",".$cbo_company_id_to.",".$product_id.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_uom.",".$txt_transfer_qnty.",".number_format($txt_rate,10,'.','').",".number_format($txt_transfer_value,8,'.','').",".$txt_transfer_qnty.",".number_format($txt_transfer_value,8,'.','').",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_yarn_lot.",".number_format($store_item_rate,10,'.','').",".number_format($issue_store_value,8,'.','').")";
			}
			
		
			$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$hidden_product_id.",".$product_id.",".$txt_yarn_lot.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".number_format($txt_rate,10,'.','').",".number_format($txt_transfer_value,8,'.','').",".$cbo_uom.",".$hidden_req_id.",".$hidden_req_dtls_id.",".$id_trans.",".$recv_trans_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".number_format($store_item_rate,10,'.','').",".number_format($issue_store_value,8,'.','').")";

			if($variable_auto_rcv==2) // acknowledgement_dtls_table
			{
				$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
				$data_array_dtls_ac="(".$id_dtls_ac.",".$transfer_update_id.",".$id_dtls.",0,".$hidden_product_id.",".$product_id.",".$txt_yarn_lot.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".number_format($txt_rate,10,'.','').",".number_format($txt_transfer_value,8,'.','').",".$cbo_uom.",".$hidden_req_id.",".$hidden_req_dtls_id.",'".$txt_remarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".number_format($store_item_rate,10,'.','').",".number_format($issue_store_value,8,'.','').")";
			}
			
			// #### store wise table balancein here
			if($variable_lot==1 && str_replace("'", "", $txt_yarn_lot)!="") 
			{
				$sotre_lot_cond=" and lot='".str_replace("'", "", $txt_yarn_lot)."'";
				$dyes_lot=str_replace("'", "", $txt_yarn_lot);
			}
			else
			{
				$sotre_lot_cond="";
				$dyes_lot='';
			}
			
			$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_qty_dtls where prod_id=$hidden_product_id and category_id=$cbo_item_category and store_id=$cbo_store_name and company_id=$cbo_company_id $sotre_lot_cond");
			$store_presentStock=$store_presentStockValue=$store_presentAvgRate=0;
			
			foreach($sql_store as $result)
			{
				$store_presentStock	=$result[csf("current_stock")];
				$store_presentStockValue =$result[csf("stock_value")];
				$store_presentAvgRate	=$result[csf("avg_rate_per_unit")];
				$store_issue_id=$result[csf("id")];
			}
			
			$store_StockValue_issue=$store_presentStockValue-$issue_store_value;
			$store_currentStock_issue=$store_presentStock-$txt_transfer_qnty;
			if($store_currentStock_issue>0 && $store_StockValue_issue>0)
			{
				$store_avgRate_issue=$store_StockValue_issue/$store_currentStock_issue;
			}
			else
			{
				$store_avgRate_issue=0;
			}
			
			$field_array_store_issue="rate*last_issued_qnty*cons_qty*amount*updated_by*update_date"; 
			$data_array_store_issue="".number_format($store_avgRate_issue,10,".","")."*".$txt_transfer_qnty."*".$store_currentStock_issue."*".number_format($store_StockValue_issue,8,".","")."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";
			
			if($variable_auto_rcv==1)
			{
				$sql_store_rcv = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_qty_dtls where prod_id=$product_id and category_id=$cbo_item_category and store_id=$cbo_store_name_to and company_id=$cbo_company_id_to $sotre_lot_cond");
				$store_presentStock_rcv=$store_presentStockValue_rcv=$store_presentAvgRate_rcv=0;
				
				foreach($sql_store_rcv as $result)
				{
					$store_presentStock_rcv	=$result[csf("current_stock")];
					$store_presentStockValue_rcv =$result[csf("stock_value")];
					$store_presentAvgRate_rcv	=$result[csf("avg_rate_per_unit")];
					$store_rcv_id=$result[csf("id")];
				}
				//echo "10**$store_rcv_id";oci_rollback($con);disconnect($con);die;
				//echo "10**=$store_rcv_id=";die;
				if($store_rcv_id!="")
				{
					$store_StockValue_rcv=$store_presentStockValue_rcv+$issue_store_value;
					$store_currentStock_rcv=$store_presentStock_rcv+$txt_transfer_qnty;
					if($store_currentStock_rcv>0 && $store_StockValue_rcv>0)
					{
						$store_avgRate_rcv=$store_StockValue_rcv/$store_currentStock_rcv;
					}
					else
					{
						$store_avgRate_rcv=0;
					}
					$field_array_store_rcv="rate*last_purchased_qnty*cons_qty*amount*updated_by*update_date*last_receive_date"; 
					$data_array_store_rcv="".number_format($store_avgRate_rcv,10,".","")."*".$txt_transfer_qnty."*".$store_currentStock_rcv."*".number_format($store_StockValue_rcv,8,".","")."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$txt_transfer_date."";
				}
				else
				{
					$field_array_store_insert="id,company_id,store_id,category_id,prod_id,cons_qty,rate,amount,last_purchased_qnty,inserted_by,insert_date,lot,first_receive_date,last_receive_date"; 
					//$sdtlsid = return_next_id("id", "inv_store_wise_qty_dtls", 1);
					$sdtlsid = return_next_id_by_sequence("INV_STORE_WISE_QTY_DTLS_PK_SEQ", "inv_store_wise_qty_dtls", $con);
					$data_array_store_insert= "(".$sdtlsid.",".$cbo_company_id_to.",".$cbo_store_name_to.",".$cbo_item_category.",".$product_id.",".$txt_transfer_qnty.",".number_format($store_item_rate,10,".","").",".number_format($issue_store_value,8,".","").",".$txt_transfer_qnty.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."','".$dyes_lot."',".$txt_transfer_date.",".$txt_transfer_date.")";
				}
			}
			
			
			$transfer_qnty = $txt_transfer_qnty;
			$transfer_value = $txt_transfer_value;
			$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
			$update_array = "balance_qnty*balance_amount*updated_by*update_date";
			$updateID_array=array();
			$update_data=array();
			
			//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);
			
			$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_product_id and balance_qnty>0 and transaction_type in (1,4,5) and item_category=$cbo_item_category and store_id=$cbo_store_name order by id $cond_lifofifo");
			foreach($sql as $result)
			{				
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				
				$recv_trans_id = $result[csf("id")]; // this row will be updated
				$balance_qnty = $result[csf("balance_qnty")];
				$balance_amount = $result[csf("balance_amount")];
				$cons_rate = $result[csf("cons_rate")];
				$transferQntyBalance = $balance_qnty-$transfer_qnty; // minus issue qnty
				$transferStockBalance = $balance_amount-($transfer_qnty*$cons_rate);
				if($transferQntyBalance>=0)
				{					
					$amount = $transfer_qnty*$cons_rate;
					//for insert
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",55,".$hidden_product_id.",".$transfer_qnty.",".number_format($cons_rate,10,".","").",".number_format($amount,8,".","").",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					$updateID_array[]=$recv_trans_id; 
					$update_data[$recv_trans_id]=explode("*",("".$transferQntyBalance."*".$transferStockBalance."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					break;
				}
				else if($transferQntyBalance<0)
				{
					$transferQntyBalance = $transfer_qnty-$balance_qnty;				
					$transfer_qnty = $balance_qnty;				
					$amount = $transfer_qnty*$cons_rate;
					
					//for insert
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",55,".$hidden_product_id.",".$balance_qnty.",".number_format($cons_rate,10,".","").",".number_format($amount,8,".","").",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					$updateID_array[]=$recv_trans_id; 
					$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					$transfer_qnty = $transferQntyBalance;
				}
				//$mrrWiseIsID++;
			}//end foreach
 		// LIFO/FIFO END-----------------------------------------------//

		}
		else
		{
                    
			 //----------------Check Last Receive Date for Transfer Out---------------->>>>>>>
			$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and transaction_type in (1,4,5)", "max_date");      
			if($max_recv_date !="")
			{
				$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_recv_date) 
				{
					echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
					disconnect($con);
					die;
				}
			}
			
			 //----------------Check Last Issue Date for Transfer In---------------->>>>>>>
			$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and transaction_type in (2,3,6)", "max_date");      
			if($max_issue_date !="")
			{
				$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_issue_date) 
				{
					echo "20**Transfer In Date Can not Be Less Than Last Issue Date Of This Lot";
					disconnect($con);
					die;
				}
			}
                    
			//Two field Missing here--trans_id,to_trans_id,
			$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_transfer_criteria.",".$cbo_company_id.",".$hidden_product_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_uom.",".$txt_transfer_qnty.",".number_format($txt_rate,10,'.','').",".number_format($txt_transfer_value,8,'.','').",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_yarn_lot.",".number_format($store_item_rate,10,'.','').",".number_format($issue_store_value,8,'.','').")";			
			
			$to_id_trans=0;
			if($variable_auto_rcv==1)
			{
				$to_id_trans=return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$data_array_trans.=",(".$to_id_trans.",".$transfer_update_id.",".$cbo_transfer_criteria.",".$cbo_company_id_to.",".$hidden_product_id.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_uom.",".$txt_transfer_qnty.",".number_format($txt_rate,10,'.','').",".number_format($txt_transfer_value,8,'.','').",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_yarn_lot.",".number_format($store_item_rate,10,'.','').",".number_format($issue_store_value,8,'.','').")";
			}
			
			$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$hidden_product_id.",".$hidden_product_id.",".$txt_yarn_lot.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".number_format($txt_rate,10,'.','').",".number_format($txt_transfer_value,8,'.','').",".$cbo_uom.",".$hidden_req_id.",".$hidden_req_dtls_id.",".$id_trans.",".$to_id_trans.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".number_format($store_item_rate,10,'.','').",".number_format($issue_store_value,8,'.','').")";			
			

			if($variable_auto_rcv==2) // acknowledgement_dtls_table
			{
				$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
				$data_array_dtls_ac="(".$id_dtls_ac.",".$transfer_update_id.",".$id_dtls.",0,".$hidden_product_id.",".$hidden_product_id.",".$txt_yarn_lot.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".number_format($txt_rate,10,'.','').",".number_format($txt_transfer_value,8,'.','').",".$cbo_uom.",".$hidden_req_id.",".$hidden_req_dtls_id.",'".$txt_remarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".number_format($store_item_rate,10,'.','').",".number_format($issue_store_value,8,'.','').")";
			}
			
			if($variable_lot==1 && str_replace("'", "", $txt_yarn_lot)!="") 
			{
				$sotre_lot_cond=" and lot='".str_replace("'", "", $txt_yarn_lot)."'";
				$dyes_lot=str_replace("'", "", $txt_yarn_lot);
			}
			else
			{
				$sotre_lot_cond="";
				$dyes_lot='';
			}
			
			$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_qty_dtls where prod_id=$hidden_product_id and category_id=$cbo_item_category and store_id=$cbo_store_name and company_id=$cbo_company_id $sotre_lot_cond");
			$store_presentStock=$store_presentStockValue=$store_presentAvgRate=0;
			foreach($sql_store as $result)
			{
				$store_presentStock	=$result[csf("current_stock")];
				$store_presentStockValue =$result[csf("stock_value")];
				$store_presentAvgRate	=$result[csf("avg_rate_per_unit")];
				$store_issue_id=$result[csf("id")];
			}
			
			$store_StockValue_issue=$store_presentStockValue-$issue_store_value;
			$store_currentStock_issue=$store_presentStock-$txt_transfer_qnty;
			if($store_currentStock_issue>0 && $store_StockValue_issue>0)
			{
				$store_avgRate_issue=$store_StockValue_issue/$store_currentStock_issue;
			}
			else
			{
				$store_avgRate_issue=0;
			}
			
			$field_array_store_issue="rate*last_issued_qnty*cons_qty*amount*updated_by*update_date"; 
			$data_array_store_issue="".number_format($store_avgRate_issue,10,".","")."*".$txt_transfer_qnty."*".$store_currentStock_issue."*".number_format($store_StockValue_issue,8,".","")."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";
			
			
			if($variable_auto_rcv==1)
			{
				$sql_store_rcv = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_qty_dtls where prod_id=$hidden_product_id and category_id=$cbo_item_category and store_id=$cbo_store_name_to and company_id=$cbo_company_id $sotre_lot_cond");
				$store_presentStock_rcv=$store_presentStockValue_rcv=$store_presentAvgRate_rcv=0;
				
				foreach($sql_store_rcv as $result)
				{
					$store_presentStock_rcv	=$result[csf("current_stock")];
					$store_presentStockValue_rcv =$result[csf("stock_value")];
					$store_presentAvgRate_rcv	=$result[csf("avg_rate_per_unit")];
					$store_rcv_id=$result[csf("id")];
				}
				
				if($store_rcv_id!="")
				{
					$store_StockValue_rcv=$store_presentStockValue_rcv+$issue_store_value;
					$store_currentStock_rcv=$store_presentStock_rcv+$txt_transfer_qnty;
					$store_avgRate_rcv=$store_StockValue_rcv/$store_currentStock_rcv;
					$field_array_store_rcv="rate*last_purchased_qnty*cons_qty*amount*updated_by*update_date*last_receive_date"; 
					$data_array_store_rcv="".number_format($store_avgRate_rcv,10,".","")."*".$txt_transfer_qnty."*".$store_currentStock_rcv."*".number_format($store_StockValue_rcv,8,".","")."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$txt_transfer_date."";
				}
				else
				{
					$field_array_store_insert="id,company_id,store_id,category_id,prod_id,cons_qty,rate,amount,last_purchased_qnty,inserted_by,insert_date,lot,first_receive_date,last_receive_date"; 
					
					//$sdtlsid = return_next_id("id", "inv_store_wise_qty_dtls", 1);
					$sdtlsid = return_next_id_by_sequence("INV_STORE_WISE_QTY_DTLS_PK_SEQ", "inv_store_wise_qty_dtls", $con);
						
					$data_array_store_insert= "(".$sdtlsid.",".$cbo_company_id.",".$cbo_store_name_to.",".$cbo_item_category.",".$hidden_product_id.",".$txt_transfer_qnty.",".number_format($store_item_rate,10,".","").",".number_format($issue_store_value,8,".","").",".$txt_transfer_qnty.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."','".$dyes_lot."',".$txt_transfer_date.",".$txt_transfer_date.")";
				}
			}
			
			
			$transfer_qnty = $txt_transfer_qnty;
			$transfer_value = $txt_transfer_value;
			$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
			$update_array = "balance_qnty*balance_amount*updated_by*update_date";
			$updateID_array=array();
			$update_data=array();
			$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_product_id and balance_qnty>0 and transaction_type in (1,4,5) and item_category=$cbo_item_category and store_id=$cbo_store_name order by id $cond_lifofifo");
			foreach($sql as $result)
			{				
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				
				$recv_trans_id = $result[csf("id")]; // this row will be updated
				$balance_qnty = $result[csf("balance_qnty")];
				$balance_amount = $result[csf("balance_amount")];
				$cons_rate = $result[csf("cons_rate")];
				$transferQntyBalance = $balance_qnty-$transfer_qnty; // minus issue qnty
				$transferStockBalance = $balance_amount-($transfer_qnty*$cons_rate);
				if($transferQntyBalance>=0)
				{					
					$amount = $transfer_qnty*$cons_rate;
					//for insert
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",55,".$hidden_product_id.",".$transfer_qnty.",".number_format($cons_rate,10,".","").",".number_format($amount,8,".","").",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					$updateID_array[]=$recv_trans_id; 
					$update_data[$recv_trans_id]=explode("*",("".$transferQntyBalance."*".$transferStockBalance."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					break;
				}
				else if($transferQntyBalance<0)
				{
					$transferQntyBalance = $transfer_qnty-$balance_qnty;				
					$transfer_qnty = $balance_qnty;				
					$amount = $transfer_qnty*$cons_rate;
					
					//for insert
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",55,".$hidden_product_id.",".$balance_qnty.",".number_format($cons_rate,10,".","").",".number_format($amount,8,".","").",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					$updateID_array[]=$recv_trans_id; 
					$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					$transfer_qnty = $transferQntyBalance;
				}
				//$mrrWiseIsID++;
			}//end foreach
		}
		
		//--------------Store Wise Stock------------------
		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		
		$prodUpdate=$prod=$mrrWiseIssueID=$upTrID=$rID=$rID2=$rID3=$rID4=$update_store1=$update_store2=true;
		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
		}
		else
		{
			$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		}
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;oci_rollback($con);die;
		$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);

		if($store_issue_id!="")
		{
			$update_store1=sql_update("inv_store_wise_qty_dtls",$field_array_store_issue,$data_array_store_issue,"id",$store_issue_id,1);
		}
		//echo "10**$store_rcv_id";oci_rollback($con);disconnect($con);die;
		if($variable_auto_rcv==1)
		{
			if($store_rcv_id!="")
			{
				$update_store2=sql_update("inv_store_wise_qty_dtls",$field_array_store_rcv,$data_array_store_rcv,"id",$store_rcv_id,1);
			}
			else
			{
				//echo "10**insert into inv_store_wise_qty_dtls (".$field_array_store_insert.") values ".$data_array_store_insert;die;
				$update_store2 = sql_insert("inv_store_wise_qty_dtls",$field_array_store_insert,$data_array_store_insert,1);
			}
		}
		
		if($data_array_mrr!="")
		{		
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1); 
		}
		
		if(count($updateID_array)>0)
		{
			$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array)); 
		}
		
		//echo "10**".str_replace("'","",$cbo_transfer_criteria);oci_rollback($con);die;
		
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$hidden_product_id,1); 
			
			if(count($row_prod)>0)
			{
				if($variable_auto_rcv==1) 
				{
					$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
				}
			}
			else
			{
				$prod=execute_query($sql_prod_insert,0);
			}
		}
		//echo "10**$variable_auto_rcv";oci_rollback($con);die;

		if($variable_auto_rcv==2) // inv_item_transfer_dtls_ac
		{
			//echo "10**insert into inv_item_transfer_dtls_ac (".$field_array_dtls_ac.") values ".$data_array_dtls_ac;die;
			$rID4=sql_insert("inv_item_transfer_dtls_ac",$field_array_dtls_ac,$data_array_dtls_ac,0);
		}
		
		//echo "10**$rID && $rID2 && $rID3 && $rID4 && $update_store1 && $update_store2 && $prodUpdate && $prod && $mrrWiseIssueID && $upTrID";oci_rollback($con);disconnect($con);die;
		
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $update_store1 && $update_store2 && $prodUpdate && $prod && $mrrWiseIssueID && $upTrID)
			{
				mysql_query("COMMIT");  
				echo "0**".$transfer_update_id."**".$transfer_recv_num."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4 && $update_store1 && $update_store2 && $prodUpdate && $prod && $mrrWiseIssueID && $upTrID)
			{
				oci_commit($con);   
				echo "0**".$transfer_update_id."**".$transfer_recv_num."**0";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		
		disconnect($con);
		die;
	}	
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$product_id=str_replace("'","",$previous_to_prod_id);

		$is_posted=sql_select("select is_posted_account from inv_item_transfer_mst where id=$update_id and status_active=1");

		if($is_posted[0][csf("is_posted_account")] == 1)
		{
			echo "20**Already Posted in Accounts, so update and delete is not allowed";
			disconnect($con);die;
		}

		
		if ($variable_auto_rcv == 1) 
		{
			$all_trans_id=$update_trans_issue_id.",".$update_trans_recv_id;
		}
		else
		{
			$all_trans_id=$update_trans_issue_id;
		}

		$field_array_update="challan_no*transfer_date*to_company*updated_by*update_date";
		$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$cbo_company_id_to."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
	
		$field_array_trans="prod_id*transaction_date*store_id*floor_id*room*rack*self*cons_uom*cons_quantity*cons_rate*cons_amount*balance_qnty*balance_amount*updated_by*update_date*batch_lot*store_rate*store_amount";
		$updateTransID_array=array();
		$field_array_dtls="from_prod_id*to_prod_id*yarn_lot*from_store*floor_id*room*rack*shelf*to_store*to_floor_id*to_room*to_rack*to_shelf*item_category*transfer_qnty*rate*transfer_value*uom*updated_by*update_date*store_rate*store_amount";
		//LIFO/FIFO Start-----------------------------------------------//
		
		
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			
			//----------------Check Last Receive Date for Transfer Out---------------->>>>>>>
			$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$previous_from_prod_id and transaction_type in (1,4,5)", "max_date");      
			if($max_recv_date !="")
			{
				$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_recv_date) 
				{
					echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}
			}
			
			//----------------Check Last Issue Date for Transfer In---------------->>>>>>>
			$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$previous_to_prod_id and transaction_type in (2,3,6)", "max_date");      
			if($max_issue_date !="")
			{
				$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_issue_date) 
				{
					echo "20**Transfer In Date Can not Be Less Than Last Issue Date Of This Lot";
					disconnect($con);
					die;
				}
			}
			
			$updateProdID_array=array();
			$stock_from=sql_select("select a.current_stock, a.avg_rate_per_unit, a.stock_value, b.transfer_qnty, b.transfer_value, b.store_rate, b.store_amount 
			from product_details_master a, inv_item_transfer_dtls b 
			where a.id=b.FROM_PROD_ID and a.id=$previous_from_prod_id and b.id=$update_dtls_id and a.status_active in(1,3) and b.status_active=1");
			$prev_transfer_qnty=$stock_from[0][csf('transfer_qnty')];
			$prev_transfer_value=$stock_from[0][csf('transfer_value')];
			$prev_store_rate=$stock_from[0][csf('store_rate')];
			$prev_store_amount=$stock_from[0][csf('store_amount')];	
			
			
			$presentStock=($stock_from[0][csf('current_stock')]+$prev_transfer_qnty)-$txt_transfer_qnty;
			$presentStockValue=($stock_from[0][csf('stock_value')]+$prev_transfer_value)-$txt_transfer_value;
			$presentAvgRate=0;
			if($presentStockValue !=0 && $presentStock !=0) $presentAvgRate=$presentStockValue/$presentStock;				
			$field_array_prodUpdate="avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*updated_by*update_date";
			$data_array_prodUpdate=number_format($presentAvgRate,10,'.','')."*".$txt_transfer_qnty."*".$presentStock."*".number_format($presentStockValue,8,'.','')."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			if($variable_auto_rcv == 1 ) // if auto receive Yes(1), then no need to acknowledgement
			{
				$stock_to=sql_select("select current_stock, avg_rate_per_unit, stock_value from product_details_master where id=$previous_to_prod_id");
				$curr_stock_qnty_to=($stock_to[0][csf('current_stock')]-$prev_transfer_qnty)+$txt_transfer_qnty;
				$stock_stock_value_to=($stock_to[0][csf('stock_value')]-$prev_transfer_value)+$txt_transfer_value;
				$cur_st_rate_to=0;
				if($stock_stock_value_to !=0 && $curr_stock_qnty_to !=0) $cur_st_rate_to=$stock_stock_value_to/$curr_stock_qnty_to;
				$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";				
				$data_array_prod_update=number_format($cur_st_rate_to,10,'.','')."*".$txt_transfer_qnty."*".$curr_stock_qnty_to."*".number_format($stock_stock_value_to,8,'.','')."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";	
			}
			
			$updateTransID_array[]=$update_trans_issue_id; 
			$updateTransID_data[$update_trans_issue_id]=explode("*",("".$hidden_product_id."*".$txt_transfer_date."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_uom."*".$txt_transfer_qnty."*".number_format($txt_rate,10,'.','')."*".number_format($txt_transfer_value,8,'.','')."*'0'*'0'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_yarn_lot."*".number_format($store_item_rate,10,'.','')."*".number_format($issue_store_value,8,'.','').""));

			if($variable_auto_rcv == 1 && str_replace("'","",$update_trans_recv_id)>0)
			{
				$updateTransID_array[]=$update_trans_recv_id; 
				$updateTransID_data[$update_trans_recv_id]=explode("*",("'".$product_id."'*".$txt_transfer_date."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_uom."*".$txt_transfer_qnty."*".number_format($txt_rate,10,'.','')."*".number_format($txt_transfer_value,8,'.','')."*".$txt_transfer_qnty."*".$txt_transfer_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_yarn_lot."*".number_format($store_item_rate,10,'.','')."*".number_format($issue_store_value,8,'.','').""));
			}
			
			$data_array_dtls=$hidden_product_id."*".$product_id."*".$txt_yarn_lot."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_item_category."*".$txt_transfer_qnty."*".number_format($txt_rate,10,'.','')."*".number_format($txt_transfer_value,8,'.','')."*".$cbo_uom."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".number_format($store_item_rate,10,'.','')."*".number_format($issue_store_value,8,'.','')."";
			
			
			// #### store wise table balancein here
			if($variable_lot==1 && str_replace("'", "", $txt_yarn_lot)!="") 
			{
				$sotre_lot_cond=" and lot='".str_replace("'", "", $txt_yarn_lot)."'";
				$dyes_lot=str_replace("'", "", $txt_yarn_lot);
			}
			else
			{
				$sotre_lot_cond="";
				$dyes_lot='';
			}
			
			$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_qty_dtls where prod_id=$hidden_product_id and category_id=$cbo_item_category and store_id=$cbo_store_name and company_id=$cbo_company_id $sotre_lot_cond");
			$store_presentStock=$store_presentStockValue=$store_presentAvgRate=0;
			foreach($sql_store as $result)
			{
				$store_presentStock	=$result[csf("current_stock")]+$prev_transfer_qnty;
				$store_presentStockValue = $result[csf("stock_value")]+$prev_store_amount;
				$store_issue_id=$result[csf("id")];
			}
			
			$store_StockValue_issue=$store_presentStockValue-$issue_store_value;
			$store_currentStock_issue=$store_presentStock-$txt_transfer_qnty;
			$store_avgRate_issue=0;
			if($store_StockValue_issue>0 && $store_currentStock_issue>0) $store_avgRate_issue=$store_StockValue_issue/$store_currentStock_issue;
			
			$field_array_store_issue="rate*last_issued_qnty*cons_qty*amount*updated_by*update_date"; 
			$data_array_store_issue="".number_format($store_avgRate_issue,10,".","")."*".$txt_transfer_qnty."*".$store_currentStock_issue."*".number_format($store_StockValue_issue,8,".","")."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";
			
			if(str_replace("'","",$update_trans_recv_id)>0)
			{
				$prev_store_id=return_field_value("store_id","inv_transaction","id=$update_trans_recv_id and status_active=1","store_id");
				$sql_store_rcv = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_qty_dtls where prod_id=$product_id and category_id=$cbo_item_category and store_id=$prev_store_id and company_id=$cbo_company_id_to $sotre_lot_cond");
				$store_presentStock_rcv=$store_presentStockValue_rcv=$store_presentAvgRate_rcv=0;
				foreach($sql_store_rcv as $result)
				{
					$store_presentStock_rcv	=$result[csf("current_stock")]-$prev_transfer_qnty;
					$store_presentStockValue_rcv =$result[csf("stock_value")]-$prev_store_amount;
					$store_rcv_id=$result[csf("id")];
				}
				
				$store_StockValue_rcv=$store_presentStockValue_rcv+$issue_store_value;
				$store_currentStock_rcv=$store_presentStock_rcv+$txt_transfer_qnty;
				$store_avgRate_rcv=0;
				if($store_StockValue_rcv>0 && $store_currentStock_rcv>0) $store_avgRate_rcv=$store_StockValue_rcv/$store_currentStock_rcv;
				
				$field_array_store_rcv="store_id*rate*last_purchased_qnty*cons_qty*amount*updated_by*update_date*last_receive_date"; 
				$data_array_store_rcv="".$cbo_store_name_to."*".number_format($store_avgRate_rcv,10,".","")."*".$txt_transfer_qnty."*".$store_currentStock_rcv."*".number_format($store_StockValue_rcv,8,".","")."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$txt_transfer_date."";
			}
			
			//echo "10**$data_array_store_rcv"."==".$store_rcv_id;die;
			
			//transaction table START--------------------------//
			$sql = sql_select("select a.id,a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_trans_issue_id and b.entry_form=55 and a.item_category=$cbo_item_category "); 
			$adjust_data = array();
			foreach($sql as $result)
			{
				$adjust_data[$result[csf("id")]]["issue_qnty"]+=$result[csf("issue_qnty")]; 
				$adjust_data[$result[csf("id")]]["amount"]+=$result[csf("amount")]; 
			}
			
			
			$transfer_qnty = $txt_transfer_qnty;
			$transfer_value = $txt_transfer_value;
			$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
			$update_array = "balance_qnty*balance_amount*updated_by*update_date";
			$updateID_array=array();
			$update_data=array();			
			//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);			
			$sql_trans = "select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_product_id and balance_qnty>0 and transaction_type in (1,4,5) and item_category=$cbo_item_category and store_id=$cbo_store_name 
			union all
			select a.id, b.rate cons_rate, b.issue_qnty as balance_qnty, b.amount as balance_amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_trans_issue_id and b.entry_form=55 and a.item_category=$cbo_item_category
			order by id $cond_lifofifo";
			//echo "10**$sql_trans";die;
			$sql = sql_select($sql_trans);
			$mrr_bal_trans_data=array();
			foreach($sql as $val)
			{
				$mrr_bal_trans_data[$val[csf("id")]]["ID"]=$val[csf("id")];
				$mrr_bal_trans_data[$val[csf("id")]]["CONS_RATE"]=$val[csf("cons_rate")];
				$mrr_bal_trans_data[$val[csf("id")]]["BALANCE_QNTY"]+=$val[csf("balance_qnty")];
				$mrr_bal_trans_data[$val[csf("id")]]["BALANCE_AMOUNT"]+=$val[csf("balance_amount")];
			}
			//echo "10**<pre>";print_r($mrr_bal_trans_data);die;
			
			/*$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_product_id and balance_qnty>0 and transaction_type in (1,4,5) and item_category=$cbo_item_category order by id $cond_lifofifo");*/
			$p=1;
			foreach($mrr_bal_trans_data as $result)
			{				
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				$recv_trans_id = $result[csf("id")]; // this row will be updated
				$balance_qnty = $result[csf("balance_qnty")];
				$balance_amount = $result[csf("balance_amount")];
				$cons_rate = $result[csf("cons_rate")];
				$transferQntyBalance = $balance_qnty-$transfer_qnty; // minus issue qnty
				$transferStockBalance = $balance_amount-($transfer_qnty*$cons_rate);
				if($transferQntyBalance>=0)
				{					
					
					if($p>1)
					{
						$updateID_array[]=$recv_trans_id; 
						$update_data[$recv_trans_id]=explode("*",("".$result[csf("balance_qnty")]."*".$result[csf("balance_amount")]."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					}
					else
					{
						$amount = $transfer_qnty*$cons_rate;
						//for insert
						if($data_array_mrr!="") $data_array_mrr .= ",";  
						$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$update_trans_issue_id.",55,".$hidden_product_id.",".$transfer_qnty.",".number_format($cons_rate,10,".","").",".number_format($amount,8,".","").",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
						//for update
						$updateID_array[]=$recv_trans_id; 
						$update_data[$recv_trans_id]=explode("*",("".$transferQntyBalance."*".$transferStockBalance."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					}
					$p++;
					//break;
				}
				else if($transferQntyBalance<0)
				{
					$transferQntyBalance = $transfer_qnty-$balance_qnty;				
					$transfer_qnty = $balance_qnty;				
					$amount = $transfer_qnty*$cons_rate;
					
					//for insert
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$update_trans_issue_id.",55,".$hidden_product_id.",".$balance_qnty.",".number_format($cons_rate,10,".","").",".number_format($amount,8,".","").",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					$updateID_array[]=$recv_trans_id; 
					$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					$transfer_qnty = $transferQntyBalance;
				}
				//$mrrWiseIsID++;
			}//end foreach
			
 			// LIFO/FIFO END-----------------------------------------------//
		}
		else
		{
			////Item Category not update here....Live invalid issue
			$updateTransID_array[]=$update_trans_issue_id; 
			$updateTransID_data[$update_trans_issue_id]=explode("*",("".$hidden_product_id."*".$txt_transfer_date."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_uom."*".$txt_transfer_qnty."*".number_format($txt_rate,10,'.','')."*".number_format($txt_transfer_value,10,'.','')."*0*0*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_yarn_lot."*".number_format($store_item_rate,10,'.','')."*".number_format($issue_store_value,8,'.','').""));
			
			if($variable_auto_rcv == 1 ) // if auto receive Yes(1), then no need to acknowledgement
			{
				$updateTransID_array[]=$update_trans_recv_id; 			
				$updateTransID_data[$update_trans_recv_id]=explode("*",("".$hidden_product_id."*".$txt_transfer_date."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_uom."*".$txt_transfer_qnty."*".number_format($txt_rate,10,'.','')."*".number_format($txt_transfer_value,8,'.','')."*0*0*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_yarn_lot."*".number_format($store_item_rate,10,'.','')."*".number_format($issue_store_value,8,'.','').""));
			}
			
			$data_array_dtls=$hidden_product_id."*".$hidden_product_id."*".$txt_yarn_lot."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_item_category."*".$txt_transfer_qnty."*".number_format($txt_rate,10,'.','')."*".number_format($txt_transfer_value,8,'.','')."*".$cbo_uom."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".number_format($store_item_rate,10,'.','')."*".number_format($issue_store_value,8,'.','')."";
			
			// #### store wise table balancein here
			if($variable_lot==1 && str_replace("'", "", $txt_yarn_lot)!="") 
			{
				$sotre_lot_cond=" and lot='".str_replace("'", "", $txt_yarn_lot)."'";
				$dyes_lot=str_replace("'", "", $txt_yarn_lot);
			}
			else
			{
				$sotre_lot_cond="";
				$dyes_lot='';
			}
			
			$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_qty_dtls where prod_id=$hidden_product_id and category_id=$cbo_item_category and store_id=$cbo_store_name and company_id=$cbo_company_id $sotre_lot_cond");
			$store_presentStock=$store_presentStockValue=$store_presentAvgRate=0;
			foreach($sql_store as $result)
			{
				$store_presentStock	=$result[csf("current_stock")]+$prev_transfer_qnty;
				$store_presentStockValue = $result[csf("stock_value")]+$prev_store_amount;
				$store_issue_id=$result[csf("id")];
			}
			$store_StockValue_issue=$store_presentStockValue-$issue_store_value;
			$store_currentStock_issue=$store_presentStock-$txt_transfer_qnty;
			$store_avgRate_issue=0;
			if($store_currentStock_issue>0 && $store_StockValue_issue>0) $store_avgRate_issue=$store_StockValue_issue/$store_currentStock_issue;
			
			$field_array_store_issue="rate*last_issued_qnty*cons_qty*amount*updated_by*update_date"; 
			$data_array_store_issue="".number_format($store_avgRate_issue,10,".","")."*".$txt_transfer_qnty."*".$store_currentStock_issue."*".number_format($store_StockValue_issue,8,".","")."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";
			
			if(str_replace("'","",$update_trans_recv_id)>0)
			{
				$prev_store_id=return_field_value("store_id","inv_transaction","id=$update_trans_recv_id and status_active=1","store_id");
				$sql_store_rcv = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_qty_dtls where prod_id=$hidden_product_id and category_id=$cbo_item_category and store_id=$prev_store_id and company_id=$cbo_company_id $sotre_lot_cond");
				$store_presentStock_rcv=$store_presentStockValue_rcv=$store_presentAvgRate_rcv=0;
				foreach($sql_store_rcv as $result)
				{
					$store_presentStock_rcv	=$result[csf("current_stock")]-$prev_transfer_qnty;
					$store_presentStockValue_rcv =$result[csf("stock_value")]-$prev_store_amount;
					$store_rcv_id=$result[csf("id")];
				}
	
				if($prev_store_id==str_replace("'","",$cbo_store_name_to))
				{
					if($store_rcv_id!="")
					{
						$store_StockValue_rcv=$store_presentStockValue_rcv+$issue_store_value;
						$store_currentStock_rcv=$store_presentStock_rcv+$txt_transfer_qnty;
						if($store_currentStock_rcv>0 && $store_StockValue_rcv>0)
						{
							$store_avgRate_rcv=$store_StockValue_rcv/$store_currentStock_rcv;
						}
						else{
							$store_avgRate_rcv=$store_item_rate;
						}
						
						$field_array_store_rcv="store_id*rate*last_purchased_qnty*cons_qty*amount*updated_by*update_date*last_receive_date"; 
						$data_array_store_rcv="".$cbo_store_name_to."*".number_format($store_avgRate_rcv,10,".","")."*".$txt_transfer_qnty."*".$store_currentStock_rcv."*".number_format($store_StockValue_rcv,8,".","")."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$txt_transfer_date."";
					}
				}
				else
				{
					if($store_rcv_id!="")
					{
						$prev_store_sql="select a.product_name_details, a.current_stock as item_global_stock, a.avg_rate_per_unit, sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as current_stock 
						from product_details_master a, inv_transaction b 
						where a.id=b.prod_id and a.id=$hidden_product_id and b.store_id=$prev_store_id and b.status_active=1 and b.is_deleted=0 $trans_lot_cond  
						group by a.product_name_details, a.current_stock, a.avg_rate_per_unit";
						$prev_store_result=sql_select($prev_store_sql);
						$item_global_stock=$prev_store_result[0][csf("item_global_stock")];
						$store_current_stock=$prev_store_result[0][csf("current_stock")];
						if($txt_transfer_qnty > $store_current_stock || $txt_transfer_qnty > $item_global_stock )
						{
							echo "20**Previous Store Stock Quantity Not Avalilable"; disconnect($con);die;
						}
						$store_StockValue_rcv=$store_presentStockValue_rcv;
						$store_currentStock_rcv=$store_presentStock_rcv;
						if($store_currentStock_rcv>0 && $store_StockValue_rcv>0){
							$store_avgRate_rcv=$store_StockValue_rcv;
						}
						$field_array_store_rcv_up="store_id*rate*last_purchased_qnty*cons_qty*amount*updated_by*update_date*last_receive_date"; 
						$data_array_store_rcv_up="".$prev_store_id."*".number_format($store_avgRate_rcv,10,".","")."*".$txt_transfer_qnty."*".$store_currentStock_rcv."*".number_format($store_StockValue_rcv,8,".","")."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$txt_transfer_date."";
					}
	
					$sql_new_store_rcv = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_qty_dtls where prod_id=$hidden_product_id and category_id=$cbo_item_category and store_id=$cbo_store_name_to and company_id=$cbo_company_id $sotre_lot_cond");
					$newStore_presentStock_rcv=$newStore_presentStockValue_rcv=$new_store_presentAvgRate_rcv=0;
					foreach($sql_new_store_rcv as $result)
					{
						$newStore_presentStock_rcv	=$result[csf("current_stock")];
						$newStore_presentStockValue_rcv =$result[csf("stock_value")];
						$newStore_rcv_id=$result[csf("id")];
					}
	
					if($newStore_rcv_id!='')
					{
						$newStore_StockValue_rcv=$newStore_presentStockValue_rcv+$txt_transfer_value;
						$newStore_currentStock_rcv=$newStore_presentStock_rcv+$txt_transfer_qnty;
						if($newStore_currentStock_rcv>0 && $newStore_StockValue_rcv>0)
						{
							$newStore_avgRate_rcv=$newStore_StockValue_rcv/$newStore_currentStock_rcv;
						}
						else{
							$newStore_avgRate_rcv=$store_item_rate;
						}
	
						$field_array_store_rcv_new="store_id*rate*last_purchased_qnty*cons_qty*amount*updated_by*update_date*last_receive_date"; 
						$data_array_store_rcv_new="".$prev_store_id."*".number_format($newStore_avgRate_rcv,10,".","")."*".$txt_transfer_qnty."*".$newStore_currentStock_rcv."*".number_format($newStore_StockValue_rcv,8,".","")."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$txt_transfer_date."";
					}
					else
					{
						$field_array_store_insert="id,company_id,store_id,category_id,prod_id,cons_qty,rate,amount,last_purchased_qnty,inserted_by,insert_date,lot,first_receive_date,last_receive_date"; 
						//$sdtlsid = return_next_id("id", "inv_store_wise_qty_dtls", 1);
						$sdtlsid = return_next_id_by_sequence("INV_STORE_WISE_QTY_DTLS_PK_SEQ", "inv_store_wise_qty_dtls", $con);
						$data_array_store_insert= "(".$sdtlsid.",".$cbo_company_id.",".$cbo_store_name_to.",".$cbo_item_category.",".$hidden_product_id.",".$txt_transfer_qnty.",".number_format($store_item_rate,10,".","").",".number_format($issue_store_value,10,".","").",".$txt_transfer_qnty.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."','".$dyes_lot."',".$txt_transfer_date.",".$txt_transfer_date.")";
					}
				}
				
			}
			
			//transaction table START--------------------------//
			/*$sql = sql_select("select a.id,a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_trans_issue_id and b.entry_form=55 and a.item_category=$cbo_item_category "); 
			$adjust_data = array();
			foreach($sql as $result)
			{
				$adjust_data[$result[csf("id")]]["issue_qnty"]+=$result[csf("issue_qnty")]; 
				$adjust_data[$result[csf("id")]]["amount"]+=$result[csf("amount")]; 
			}*/
			
			$transfer_qnty = $txt_transfer_qnty;
			$transfer_value = $txt_transfer_value;
			$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
			$update_array = "balance_qnty*balance_amount*updated_by*update_date";
			$updateID_array=array();
			$update_data=array();			
			//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);			
			$sql_trans = "select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_product_id and balance_qnty>0 and transaction_type in (1,4,5) and item_category=$cbo_item_category and store_id=$cbo_store_name 
			union all
			select a.id, b.rate cons_rate, b.issue_qnty as balance_qnty, b.amount as balance_amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_trans_issue_id and b.entry_form=55 and a.item_category=$cbo_item_category
			order by id $cond_lifofifo";
			//echo "10**$sql_trans";die;
			$sql = sql_select($sql_trans);
			$mrr_bal_trans_data=array();
			foreach($sql as $val)
			{
				$mrr_bal_trans_data[$val[csf("id")]]["ID"]=$val[csf("id")];
				$mrr_bal_trans_data[$val[csf("id")]]["CONS_RATE"]=$val[csf("cons_rate")];
				$mrr_bal_trans_data[$val[csf("id")]]["BALANCE_QNTY"]+=$val[csf("balance_qnty")];
				$mrr_bal_trans_data[$val[csf("id")]]["BALANCE_AMOUNT"]+=$val[csf("balance_amount")];
			}
			//echo "10**<pre>";print_r($mrr_bal_trans_data);die;
			
			/*$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_product_id and balance_qnty>0 and transaction_type in (1,4,5) and item_category=$cbo_item_category order by id $cond_lifofifo");*/
			$p=1;
			foreach($mrr_bal_trans_data as $result)
			{				
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				$recv_trans_id = $result[csf("id")]; // this row will be updated
				$balance_qnty = $result[csf("balance_qnty")];
				$balance_amount = $result[csf("balance_amount")];
				$cons_rate = $result[csf("cons_rate")];
				$transferQntyBalance = $balance_qnty-$transfer_qnty; // minus issue qnty
				$transferStockBalance = $balance_amount-($transfer_qnty*$cons_rate);
				if($transferQntyBalance>=0)
				{					
					
					if($p>1)
					{
						$updateID_array[]=$recv_trans_id; 
						$update_data[$recv_trans_id]=explode("*",("".$result[csf("balance_qnty")]."*".$result[csf("balance_amount")]."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					}
					else
					{
						$amount = $transfer_qnty*$cons_rate;
						//for insert
						if($data_array_mrr!="") $data_array_mrr .= ",";  
						$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$update_trans_issue_id.",55,".$hidden_product_id.",".$transfer_qnty.",".number_format($cons_rate,10,".","").",".number_format($amount,8,".","").",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
						//for update
						$updateID_array[]=$recv_trans_id; 
						$update_data[$recv_trans_id]=explode("*",("".$transferQntyBalance."*".$transferStockBalance."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					}
					$p++;
					//break;
				}
				else if($transferQntyBalance<0)
				{
					$transferQntyBalance = $transfer_qnty-$balance_qnty;				
					$transfer_qnty = $balance_qnty;				
					$amount = $transfer_qnty*$cons_rate;
					
					//for insert
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$update_trans_issue_id.",55,".$hidden_product_id.",".$balance_qnty.",".number_format($cons_rate,10,".","").",".number_format($amount,8,".","").",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					$updateID_array[]=$recv_trans_id; 
					$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					$transfer_qnty = $transferQntyBalance;
				}
				//$mrrWiseIsID++;
			}//end foreach
			
 			// LIFO/FIFO END-----------------------------------------------//
		}
		
		//echo  "10**".bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array);die;
		
		$rID=$rID2=$rID3=$rID4=$update_store1=$update_store2=$prodUpdate=$prod=$query=$query2=$mrrWiseIssueID=$upTrID=$up_prev_store=true;
		$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		//echo "10**".bulk_update_sql_statement("inv_transaction","id",$field_array_trans,$updateTransID_data,$updateTransID_array);die;
		$rID2=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans,$updateTransID_data,$updateTransID_array));
		$rID3=sql_update("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,"id",$update_dtls_id,1);
		if($store_issue_id!="")
		{
			$update_store1=sql_update("inv_store_wise_qty_dtls",$field_array_store_issue,$data_array_store_issue,"id",$store_issue_id,1);
		}
		
		//echo "10**".$store_rcv_id; die;
		if(str_replace("'","",$update_trans_recv_id)>0)
		{
			if($prev_store_id==str_replace("'","",$cbo_store_name_to))
			{
				if($store_rcv_id!="")
				{
					$update_store2=sql_update("inv_store_wise_qty_dtls",$field_array_store_rcv,$data_array_store_rcv,"id",$store_rcv_id,1);
				}
			}
			else
			{
				if($store_rcv_id!="")
				{
					$up_prev_store=sql_update("inv_store_wise_qty_dtls",$field_array_store_rcv_up,$data_array_store_rcv_up,"id",$store_rcv_id,1);
				}
				$update_store2 = sql_insert("inv_store_wise_qty_dtls",$field_array_store_insert,$data_array_store_insert,1);
			}
		}
		
		if(count($updateID_array)>0)
		{
			$query=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
			$query2 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_trans_issue_id and entry_form=55 ");
		}
		if($data_array_mrr!="")
		{		
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
		}
		if(count($updateID_array)>0)
		{
			$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array)); 
		}
			
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$hidden_product_id,1);
			
			if(count($row_prod)>0 && $variable_auto_rcv == 1)
			{
				$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
			}
			else
			{
				$prod=execute_query($sql_prod_insert,0);
			}
			//echo "10**$prod".count($row_prod);die;
		}

		if($variable_auto_rcv==2) //acknowledgement details table update, 
		{
			$rID4=sql_update("inv_item_transfer_dtls_ac",$field_array_dtls,$data_array_dtls,"dtls_id",$update_dtls_id,1);	
		}
		
		//echo "10**$rID && $rID2 && $rID3 && $update_store1 && $update_store2  && $prodUpdate && $prod && $query && $query2 && $mrrWiseIssueID && $upTrID && $up_prev_store";oci_rollback($con);disconnect($con);die;
		
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $update_store1 && $update_store2 && $prodUpdate && $prod && $query && $query2 && $mrrWiseIssueID && $upTrID && $up_prev_store)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**"."&nbsp;"."**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			
			if($rID && $rID2 && $rID3 && $update_store1 && $update_store2 && $prodUpdate && $prod && $query && $query2 && $mrrWiseIssueID && $upTrID && $up_prev_store)
			{
				oci_commit($con);   
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**0**"."&nbsp;"."**1";
			}
		}	
		disconnect($con);
		die;
 	}
 	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$is_posted=sql_select("select is_posted_account from inv_item_transfer_mst where id=$update_id and status_active=1");

		if($is_posted[0][csf("is_posted_account")] == 1)
		{
			echo "20**Already Posted in Accounts, so update and delete is not allowed";
			disconnect($con);die;
		}

		
		if ($variable_auto_rcv == 1) 
		{
			$all_trans_id=$update_trans_issue_id.",".$update_trans_recv_id;
		}
		else
		{
			$all_trans_id=$update_trans_issue_id;
		}
		$updateTransID_array=explode(',', $all_trans_id);
		//echo "10**";
		//print_r($updateTransID_array); disconnect($con);die;
		//$field_array_update="status_active*is_deleted*updated_by*update_date";
		//$data_array_update="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$field_array_trans="updated_by*update_date*status_active*is_deleted";
		$data_array_trans="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		$field_array_dtls="updated_by*update_date*status_active*is_deleted";
		$data_array_dtls="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		//$field_array_trans="prod_id*transaction_date*store_id*floor_id*room*rack*self*cons_uom*cons_quantity*cons_rate*cons_amount*balance_qnty*balance_amount*updated_by*update_date*batch_lot";
		//$updateTransID_array=array();
		
		//$field_array_dtls="from_prod_id*to_prod_id*yarn_lot*from_store*floor_id*room*rack*shelf*to_store*to_floor_id*to_room*to_rack*to_shelf*item_category*transfer_qnty*rate*transfer_value*uom*updated_by*update_date";
		
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$updateProdID_array=array();
			$field_array_adjust="current_stock*avg_rate_per_unit*stock_value";
			
			$stock_from=sql_select("select current_stock, avg_rate_per_unit from product_details_master where id=$previous_from_prod_id");
			$adjust_curr_stock_from=$stock_from[0][csf('current_stock')]+str_replace("'", '',$hidden_transfer_qnty);
			$cur_st_rate_from=$stock_from[0][csf('avg_rate_per_unit')];
			$cur_st_value_from=0;
			if ($adjust_curr_stock_from != 0){				
				$cur_st_value_from=$adjust_curr_stock_from*$cur_st_rate_from;
			}			
			
			$previous_from_prod_id=str_replace("'","",$previous_from_prod_id);
			$updateProdID_array[]=$previous_from_prod_id; 
			
			$data_array_adjust[$previous_from_prod_id]=explode("*",("".$adjust_curr_stock_from."*".$cur_st_rate_from."*".$cur_st_value_from));
			
			$stock_to=sql_select("select current_stock, avg_rate_per_unit from product_details_master where id=$previous_to_prod_id");
			$adjust_curr_stock_to=$stock_to[0][csf('current_stock')]-str_replace("'", '',$hidden_transfer_qnty);

			if($variable_auto_rcv == 1 ) // if auto receive Yes(1), then no need to acknowledgement
			{
				$cur_st_rate_to=$stock_to[0][csf('avg_rate_per_unit')];
				$cur_st_value_to=0;
				if ($adjust_curr_stock_to != 0){					
					$cur_st_value_to=$adjust_curr_stock_to*$cur_st_rate_to;
				}				
				
				$previous_to_prod_id=str_replace("'","",$previous_to_prod_id);
				$updateProdID_array[]=$previous_to_prod_id; 
				$data_array_adjust[$previous_to_prod_id]=explode("*",("".$adjust_curr_stock_to."*".$cur_st_rate_to."*".$cur_st_value_to));
			}	
			
			
			$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_qty_dtls where prod_id=$hidden_product_id and category_id=$cbo_item_category and store_id=$cbo_store_name and company_id=$cbo_company_id $sotre_lot_cond");
			$store_presentStock=$store_presentStockValue=$store_presentAvgRate=0;
			
			foreach($sql_store as $result)
			{
				$store_presentStock	=$result[csf("current_stock")]+str_replace("'", '',$hidden_transfer_qnty);
				$store_presentStockValue = $result[csf("stock_value")]+(str_replace("'", '',$hidden_transfer_qnty)*$result[csf("avg_rate_per_unit")]);
				$store_presentAvgRate	=$result[csf("avg_rate_per_unit")];
				$store_issue_id=$result[csf("id")];
			}
			
			$store_StockValue_issue=$store_presentStockValue;
			$store_currentStock_issue=$store_presentStock;
			if($store_StockValue_issue>0 && $store_currentStock_issue>0)
			{
				$store_avgRate_issue=$store_StockValue_issue/$store_currentStock_issue;
			}
			else
			{
				$store_avgRate_issue=0;
			}
			
			$field_array_store_issue="rate*last_issued_qnty*cons_qty*amount*updated_by*update_date"; 
			$data_array_store_issue="".number_format($store_avgRate_issue,$dec_place[3],".","")."*".$txt_transfer_qnty."*".$store_currentStock_issue."*".number_format($store_StockValue_issue,$dec_place[4],".","")."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";
			if(str_replace("'","",$update_trans_recv_id)>0)
			{
				$prev_store_id=return_field_value("store_id","inv_transaction","id=$update_trans_recv_id and status_active=1","store_id");
				$sql_store_rcv = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_qty_dtls where prod_id=$product_id and category_id=$cbo_item_category and store_id=$prev_store_id and company_id=$cbo_company_id_to $sotre_lot_cond");
				$store_presentStock_rcv=$store_presentStockValue_rcv=$store_presentAvgRate_rcv=0;
				foreach($sql_store_rcv as $result)
				{
					$store_presentStock_rcv	=$result[csf("current_stock")]-str_replace("'", '',$hidden_transfer_qnty);
					$store_presentStockValue_rcv =$result[csf("stock_value")]-(str_replace("'", '',$hidden_transfer_qnty)*$result[csf("avg_rate_per_unit")]);
					$store_presentAvgRate_rcv	=$result[csf("avg_rate_per_unit")];
					$store_rcv_id=$result[csf("id")];
				}
				
				$store_StockValue_rcv=$store_presentStockValue_rcv;
				$store_currentStock_rcv=$store_presentStock_rcv;
				if($store_StockValue_rcv>0 && $store_currentStock_rcv>0)
				{
					$store_avgRate_rcv=$store_StockValue_rcv/$store_currentStock_rcv;
				}
				else
				{
					$store_avgRate_rcv=0;
				}
				
				$field_array_store_rcv="store_id*rate*last_purchased_qnty*cons_qty*amount*updated_by*update_date*last_receive_date"; 
				$data_array_store_rcv="".$cbo_store_name_to."*".number_format($store_avgRate_rcv,$dec_place[3],".","")."*".$txt_transfer_qnty."*".$store_currentStock_rcv."*".number_format($store_StockValue_rcv,$dec_place[4],".","")."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$txt_transfer_date."";
			}
			
			//echo "10**$data_array_store_rcv"."==".$store_rcv_id;die;
			
			//transaction table START--------------------------//
			$update_array = "balance_qnty*balance_amount*updated_by*update_date";
			$sql = sql_select("select a.id,a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_trans_issue_id and b.entry_form=55 and a.item_category=$cbo_item_category "); 
			$updateID_array = array();
			$update_data = array();
			foreach($sql as $result)
			{
				$adjBalance = $result[csf("balance_qnty")]+$result[csf("issue_qnty")];
				$adjAmount = $result[csf("balance_amount")]+$result[csf("amount")];
				$updateID_array[]=$result[csf("id")]; 
				$update_data[$result[csf("id")]]=explode("*",("".$adjBalance."*".$adjAmount."*'".$user_id."'*'".$pc_date_time."'"));
			}

			//transaction table END----------------------------//
			
		}
		else
		{
			// #### store wise table balancein here
			if($variable_lot==1 && str_replace("'", "", $txt_yarn_lot)!="") 
			{
				$sotre_lot_cond=" and lot='".str_replace("'", "", $txt_yarn_lot)."'";
				$dyes_lot=str_replace("'", "", $txt_yarn_lot);
			}
			else
			{
				$sotre_lot_cond="";
				$dyes_lot='';
			}
			
			$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_qty_dtls where prod_id=$hidden_product_id and category_id=$cbo_item_category and store_id=$cbo_store_name and company_id=$cbo_company_id $sotre_lot_cond");
			$store_presentStock=$store_presentStockValue=$store_presentAvgRate=0;
			
			foreach($sql_store as $result)
			{
				$store_presentStock	=$result[csf("current_stock")]+str_replace("'", '',$hidden_transfer_qnty);
				$store_presentStockValue = $result[csf("stock_value")]+(str_replace("'", '',$hidden_transfer_qnty)*$result[csf("avg_rate_per_unit")]);
				$store_presentAvgRate	=$result[csf("avg_rate_per_unit")];
				$store_issue_id=$result[csf("id")];
			}
			
			$store_StockValue_issue=$store_presentStockValue;
			$store_currentStock_issue=$store_presentStock;
			if($store_currentStock_issue>0 && $store_StockValue_issue>0)
			{
				$store_avgRate_issue=$store_StockValue_issue/$store_currentStock_issue;
			}
			else
			{
				$store_avgRate_issue=0;
			}
			
			$field_array_store_issue="rate*last_issued_qnty*cons_qty*amount*updated_by*update_date"; 
			$data_array_store_issue="".number_format($store_avgRate_issue,$dec_place[3],".","")."*".$txt_transfer_qnty."*".$store_currentStock_issue."*".number_format($store_StockValue_issue,$dec_place[4],".","")."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";
			
			if(str_replace("'","",$update_trans_recv_id)>0)
			{
				$prev_store_id=return_field_value("store_id","inv_transaction","id=$update_trans_recv_id and status_active=1","store_id");
				$sql_store_rcv = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_qty_dtls where prod_id=$hidden_product_id and category_id=$cbo_item_category and store_id=$prev_store_id and company_id=$cbo_company_id $sotre_lot_cond");
				//echo "10**select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_qty_dtls where prod_id=$hidden_product_id and category_id=$cbo_item_category and store_id=$prev_store_id and company_id=$cbo_company_id $sotre_lot_cond"; die;
				$store_presentStock_rcv=$store_presentStockValue_rcv=$store_presentAvgRate_rcv=0;
				
				foreach($sql_store_rcv as $result)
				{
					$store_presentStock_rcv	=$result[csf("current_stock")]-str_replace("'", '',$hidden_transfer_qnty);
					$store_presentStockValue_rcv =$result[csf("stock_value")]-(str_replace("'", '',$hidden_transfer_qnty)*$result[csf("avg_rate_per_unit")]);
					$store_presentAvgRate_rcv	=$result[csf("avg_rate_per_unit")];
					$store_rcv_id=$result[csf("id")];
				}
	
				if($store_rcv_id!="")
				{
					$store_StockValue_rcv=$store_presentStockValue_rcv;
					$store_currentStock_rcv=$store_presentStock_rcv;
					if($store_currentStock_rcv>0 && $store_StockValue_rcv>0)
					{
						$store_avgRate_rcv=$store_StockValue_rcv/$store_currentStock_rcv;
					}
					else{
						$store_avgRate_rcv=$txt_transfer_value/$txt_transfer_qnty;
					}
					
					$field_array_store_rcv="store_id*rate*last_purchased_qnty*cons_qty*amount*updated_by*update_date*last_receive_date"; 
					$data_array_store_rcv="".$cbo_store_name_to."*".number_format($store_avgRate_rcv,$dec_place[3],".","")."*".$txt_transfer_qnty."*".$store_currentStock_rcv."*".number_format($store_StockValue_rcv,$dec_place[4],".","")."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$txt_transfer_date."";
				}
			}
			
			
			//transaction table START--------------------------//
			$update_array = "balance_qnty*balance_amount*updated_by*update_date";
			$sql = sql_select("select a.id,a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_trans_issue_id and b.entry_form=55 and a.item_category=$cbo_item_category "); 
			$updateID_array = array();
			$update_data = array();
			foreach($sql as $result)
			{
				$adjBalance = $result[csf("balance_qnty")]+$result[csf("issue_qnty")];
				$adjAmount = $result[csf("balance_amount")]+$result[csf("amount")];
				$updateID_array[]=$result[csf("id")]; 
				$update_data[$result[csf("id")]]=explode("*",("".$adjBalance."*".$adjAmount."*'".$user_id."'*'".$pc_date_time."'"));
			}

			//transaction table END----------------------------//
		}
		//echo "10**".$store_rcv_id; die;
		
		$rID2=$rID3=$update_store1=$update_store2=$prodUpdate_adjust=$query2=$rID4=$upTrID=true;
		//$rID=sql_updates("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		//echo "10**".bulk_update_sql_statement("inv_transaction","id",$field_array_trans,$updateTransID_data,$updateTransID_array);die;
		$rID2=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans,$data_array_trans,$updateTransID_array));
		$rID3=sql_update("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,"id",$update_dtls_id,1);
		$query2=execute_query(bulk_update_sql_statement("inv_mrr_wise_issue_details","issue_trans_id",$field_array_trans,$data_array_trans,$updateTransID_array));
		//echo "10**".$field_array_dtls.'=='.$data_array_dtls;
		if($store_issue_id!="")
		{
			$update_store1=sql_update("inv_store_wise_qty_dtls",$field_array_store_issue,$data_array_store_issue,"id",$store_issue_id,1);
		}
		
		if(str_replace("'","",$update_trans_recv_id)>0)
		{
			//echo "10**".$prev_store_id.'=='.str_replace("'","",$cbo_store_name_to); die;
			$update_store2=sql_update("inv_store_wise_qty_dtls",$field_array_store_rcv,$data_array_store_rcv,"id",$store_rcv_id,1);
		}
		
		if(count($updateID_array)>0)
		{
			$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array)); 
		}
		
		
		//echo "10**".$up_prev_store; die;
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$prodUpdate_adjust=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_adjust,$data_array_adjust,$updateProdID_array));
		}

		if($variable_auto_rcv==2) //acknowledgement details table update, 
		{
			$rID4=sql_update("inv_item_transfer_dtls_ac",$field_array_dtls,$data_array_dtls,"dtls_id",$update_dtls_id,1);	
		}
		
		//echo "10**$rID2=$rID3=$update_store1=$update_store2=$prodUpdate_adjust=$query2=$rID4";oci_rollback($con);disconnect($con);die;
		//1 && 1 && 1 && 1 && 1 && 1 && 1 && 1 && 1 && 0 && 1 && 1 && 1
		
		if($db_type==0)
		{
			if($rID2 && $rID3 && $update_store1 && $update_store2 && $prodUpdate_adjust && $query2 && $rID4 && $upTrID)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**"."&nbsp;"."**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			
			if($rID2 && $rID3 && $update_store1 && $update_store2 && $prodUpdate_adjust && $query2 && $rID4 && $upTrID)
			{
				oci_commit($con);   
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**0**"."&nbsp;"."**1";
			}
		}	
		disconnect($con);
		die;
 	}
}


function sql_updates($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit,$return_query='')
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
	if($return_query==1){return $strQuery ;}

	return $strQuery;die;
	global $con;
	if( strpos($strQuery, "WHERE")==false)  return "0";
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	
	if ($exestd){user_activities($exestd);}
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

if($action=="yarn_transfer_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$sql="select id, transfer_system_id, challan_no, transfer_date, transfer_criteria, item_category, to_company, from_order_id, to_order_id from inv_item_transfer_mst a where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$item_group_arr=return_library_array( "select id,item_name  from lib_item_group where   status_active=1 ",'id','item_name');
	$product_sql = sql_select("select ID, ITEM_GROUP_ID, ITEM_DESCRIPTION from product_details_master where item_category_id in (5,6,7,22,23) ");
	foreach($product_sql as $row)
	{
		$product_arr[$row["ID"]]["ITEM_DESCRIPTION"]=$row["ITEM_DESCRIPTION"];
		$product_arr[$row["ID"]]["ITEM_GROUP_ID"]=$row["ITEM_GROUP_ID"];
	}
	
?>
<div style="width:930px;">
    <table width="900" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">  
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
					?>
						Plot No: <? echo $result['plot_no']; ?> 
						Level No: <? echo $result['level_no']?>
						Road No: <? echo $result['road_no']; ?> 
						Block No: <? echo $result['block_no'];?> 
						City No: <? echo $result['city'];?> 
						Zip Code: <? echo $result['zip_code']; ?> 
						Province No: <?php echo $result['province'];?> 
						Country: <? echo $country_arr[$result['country_id']]; ?><br> 
						Email Address: <? echo $result['email'];?> 
						Website No: <? echo $result['website'];
					}
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Report</u></strong></td>
        </tr>
        <tr>
        	<td width="120"><strong>Transfer ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
            <td width="130"><strong>Transfer Criteria:</strong></td> <td width="175px"><? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?></td>
            <td width="125"><strong>To Company</strong></td><td width="175px"><? echo $company_library[$dataArray[0][csf('to_company')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Transfer Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
            <td><strong>Challan No.:</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
        </tr>
    </table>
        <br>
    <div style="width:100%;">
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="40">SL</th>
            <th width="100" >From Store</th>
            <th width="100" >To Store</th>
            <th width="110" >Item Category</th> 
            <th width="100" >Item Group</th>
            <th width="150" >Item Description</th>
            <th width="50" > Lot</th>
            <th width="100" >Transfered Qnty</th>
        </thead>
        <tbody> 
   
<?
	$sql_dtls="select id, from_store, to_store, from_prod_id, yarn_lot, transfer_qnty,item_category from inv_item_transfer_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0";
	$sql_result= sql_select($sql_dtls);
	$i=1;
	foreach($sql_result as $row)
	{
		if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
			
			$transfer_qnty=$row[csf('transfer_qnty')];
			$transfer_qnty_sum += $transfer_qnty;
			
		?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td align="center"><? echo $i; ?></td>
                <td><? echo $store_library[$row[csf("from_store")]]; ?></td>
                <td><? echo $store_library[$row[csf("to_store")]]; ?></td>
                <td><? echo $item_category[$row[csf("item_category")]]; ?></td>
                <td align="center"><? echo $item_group_arr[$product_arr[$row[csf("from_prod_id")]]["ITEM_GROUP_ID"]]; ?></td>
                <td align="center"><? echo $product_arr[$row[csf("from_prod_id")]]["ITEM_DESCRIPTION"]; ?></td>
                <td align="center"><? echo $row[csf("yarn_lot")]; ?></td>
                <td align="right"><? echo $row[csf("transfer_qnty")]; ?></td>
			</tr>
			<? $i++; } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" align="right"><strong>Total :</strong></td>
                <td align="right"><?php echo $transfer_qnty_sum; ?></td>
               
            </tr>                           
        </tfoot>
      </table>
        <br>
		 <?
            echo signature_table(262, $data[0], "900px");
         ?>
      </div>
   </div>   
 <?
 exit();	
}




//Start Requisition No here------------------------------//
if ($action=="item_requisition_popup_search")
{
	echo load_html_head_contents("Transfer Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value(data)
		{
			// alert(data);
			var data_info=data.split("_");
			if(data_info[4]==1 && (data_info[3]==0 || data_info[3]==2))
			{
				alert("Chemical Transfer Requisition is no Approved");
				return;
			}
			else
			{
				$('#requisition_info').val(data);
				parent.emailwindow.hide();
			}
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:850px; margin: 0 auto;">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:850px">
	        <legend>Enter search words</legend>
	            <table cellpadding="0" cellspacing="0" width="850" class="rpt_table" border="1" rules="all">
	                <thead>
	                    <th width="200" >Search By</th>
	                    <th width="200" id="search_by_td_up">Please Enter Requisition ID</th>
	                    <th width="250">Date Range</th>
	                    <th>
	                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
	                        <input type="hidden" name="requisition_info" id="requisition_info" class="text_boxes" value="">
							<input type="hidden" name="cbo_company_id_to" id="cbo_company_id_to" class="text_boxes" value="<?= $cbo_company_id_to ?>">
	                    </th>
	                </thead>
	                <tr class="general">
	                    <td>
							<?
								$search_by_arr=array(1=>"Transfer ID",2=>"Challan No.");
								$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";
								echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
	                        ?>
	                    </td>
	                    <td id="search_by_td">
	                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
	                    </td>
	                    <td>
	                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" readonly>
	                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" readonly>
	                    </td>
	                    <td>
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_transfer_criteria; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_company_id_to').value, 'create_requisition_search_list_view', 'search_div', 'chemical_dyes_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
	                    </td>
	                </tr>
	                <tr>
	                    <td colspan="4" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
	                </tr>
	            </table>	        	
				<div style="margin-top: 10px">
					<div style="margin-top:10px" id="search_div"></div> 
				</div>
			</fieldset>
		</form>
	</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=='create_requisition_search_list_view')
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	$company_to =$data[6];
	$transfer_criteria_id =$data[3];
	
	if($search_by==1)
		$search_field="a.transfer_system_id";	
	else
		$search_field="a.challan_no";
	$to_company_cond="";
	if($data[6]!=0){
		$to_company_cond="and a.to_company=$data[6]";
	}

	if ($data[4]!="" &&  $data[5]!="") 
	{
		if($db_type==0)
		{
			$transfer_date = "and a.transfer_date between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$transfer_date = "and a.transfer_date between '".change_date_format($data[4],'','',1)."' and '".change_date_format($data[5],'','',1)."'";
		}
	}
	else 
		$transfer_date ="";
	
	if($db_type==0) $year_field="a.YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later

	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	if ($db_type==2) $app_nes_setup_date=change_date_format(date('d-m-Y'), "", "",1);
    else if ($db_type==0) $app_nes_setup_date=change_date_format(date('d-m-Y'),'yyyy-mm-dd');
	/*$approval_status="SELECT approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '$app_nes_setup_date' and company_id='$company_id')) and page_id=41 and status_active=1 and is_deleted=0";
    $app_need_setup=sql_select($approval_status);
    $approval_need=$app_need_setup[0][csf("approval_need")];*/
	$approval_need=$app_need_setup=0;
	
	if($db_type==0)
	{
		$sql="SELECT a.id, a.transfer_prefix_number, $year_field, a.challan_no, a.company_id, a.transfer_date, a.transfer_criteria, a.item_category, a.to_company,a.is_approved, b.transfer_qnty,a.ready_to_approve,a.requisition_status  
		from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b where a.id=b.mst_id and a.company_id=$company_id and $search_field like '$search_string' and a.transfer_criteria=$transfer_criteria_id $transfer_date and a.entry_form in(516) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $to_company_cond order by a.id desc";
	}
	else
	{
		$sql="SELECT a.id, a.transfer_prefix_number, a.transfer_system_id, $year_field, a.challan_no, a.company_id, a.transfer_date, a.transfer_criteria, a.item_category, a.to_company,a.is_approved, b.transfer_qnty,a.ready_to_approve,a.requisition_status 
		from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b where a.id=b.mst_id and a.company_id=$company_id and $search_field like '$search_string' and a.transfer_criteria=$transfer_criteria_id $transfer_date and a.entry_form in(516) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $to_company_cond order by a.id desc";
	}
	//echo $sql;//die; 
	$sql_res=sql_select($sql);
	$check_transfer_requ_id=array();
	$transfer_requ_arr=array();
	foreach ($sql_res as $row) 
	{
		if ($check_transfer_requ_id[$row[csf('id')]]=="")
		{
			$check_transfer_requ_id[$row[csf('id')]]=$row[csf('id')];
			$transfer_requ_arr[$row[csf('id')]]['id']=$row[csf('id')];
			$transfer_requ_arr[$row[csf('id')]]['transfer_prefix_number']=$row[csf('transfer_prefix_number')];
			$transfer_requ_arr[$row[csf('id')]]['transfer_system_id']=$row[csf('transfer_system_id')];
			$transfer_requ_arr[$row[csf('id')]]['year']=$row[csf('year')];
			$transfer_requ_arr[$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
			$transfer_requ_arr[$row[csf('id')]]['company_id']=$row[csf('company_id')];
			$transfer_requ_arr[$row[csf('id')]]['transfer_date']=$row[csf('transfer_date')];
			$transfer_requ_arr[$row[csf('id')]]['ready_to_approve']=$row[csf('ready_to_approve')];
			$transfer_requ_arr[$row[csf('id')]]['requisition_status']=$row[csf('requisition_status')];
			$transfer_requ_arr[$row[csf('id')]]['transfer_criteria']=$row[csf('transfer_criteria')];
			$transfer_requ_arr[$row[csf('id')]]['item_category']=$row[csf('item_category')];
			$transfer_requ_arr[$row[csf('id')]]['to_company']=$row[csf('to_company')];
			$transfer_requ_arr[$row[csf('id')]]['is_approved']=$row[csf('is_approved')];
		}
		$transfer_requ_arr[$row[csf('id')]]['transfer_qnty']+=$row[csf('transfer_qnty')];
		
	}

	$item_transfer_sql="SELECT b.requisition_mst_id, b.transfer_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id=b.mst_id and a.entry_form=55 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$item_transfer_result=sql_select($item_transfer_sql);
	$item_transfer_arr=array();
	foreach($item_transfer_result as $row)
	{
		$item_transfer_arr[$row[csf('requisition_mst_id')]]['transfer_qnty']+=$row[csf('transfer_qnty')];
	}

	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="840">
        <thead>
            <th width="30">SL</th>
            <th width="80">Requisition ID</th>
            <th width="50">Year</th>
            <th width="70">Challan No</th>
            <th width="60">Company</th>
            <th width="100">Requisition Date</th>
			<th width="120">Ready To Approve</th>
			<th width="100">Approval Status</th>
            <th width="105">Transfer Criteria</th>
            <th>To Company</th>
        </thead>
    </table>
    <div style="width:840px; max-height:270px;overflow-y:scroll;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="822" class="rpt_table" id="tbl_list_search">
	        <tbody>
	            <? 
	            $i=1;
	            foreach($transfer_requ_arr as $row)
	            {  
	                if ($i%2==0) $bgcolor="#E9F3FF"; 
	                else $bgcolor="#FFFFFF";
	                $balance_qty= $row["transfer_qnty"] - $item_transfer_arr[$row['id']]['transfer_qnty'];
	                //echo  $balance_qty.'**'.$row["transfer_system_id"].'system';
	                if ($balance_qty > 0)
	                {           	
	                	?>
		                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row["id"].'_'.$row["transfer_system_id"].'_'.$row["to_company"].'_'.$row["is_approved"].'_'.$approval_need; ?>")' style="cursor:pointer" >
		                    <td width="30"><? echo $i; ?></td>
		                    <td width="80"><? echo $row['transfer_prefix_number']; ?></td>
		                    <td width="50"><? echo $row['year']; ?></td>
		                    <td width="70"><? echo $row['challan_no']; ?></td>
		                    <td width="60"><? echo $company_arr[$row['company_id']]; ?></td>
		                    <td width="100"><? echo change_date_format($row['transfer_date']); ?></td>
		                    <td width="120"><? if($row['ready_to_approve']==1){echo "Yes";}else{echo "No";}?></td>
		                    <td width="100"><? if($row['is_approved']==1){echo "Yes";}else{echo "No";} ?></td>
						    <td width="105"><? echo $item_transfer_criteria[$row['transfer_criteria']]; ?></td>
		                    <td><? echo $company_arr[$row['to_company']]; ?></td>
		                </tr>
						<?
					}	 
	                $i++; 
	            } 
	            ?>
	        </tbody>
    	</table>
    </div>
    <script>
		setFilterGrid("tbl_list_search",-1);
	</script>	
	<?
	exit();
}

if($action=="show_item_requisition_listview")
{
	$data_ref=explode("**",$data);	
	if($data_ref[1]) $mst_cond="and mst_id<>$data_ref[1]";
	$item_transfer_sql="SELECT requisition_dtls_id as REQUISITION_DTLS_ID, transfer_qnty as TRANSFER_QNTY from inv_item_transfer_dtls where requisition_mst_id=$data_ref[0] $mst_cond and status_active=1";
	//echo $item_transfer_sql;
	$item_transfer_result=sql_select($item_transfer_sql);
	$item_transfer_arr=array();
	if(count($item_transfer_result)>0)
	{
		foreach($item_transfer_result as $row)
		{
			$item_transfer_arr[$row['REQUISITION_DTLS_ID']]['REQUISITION_DTLS_ID']=$row['REQUISITION_DTLS_ID'];
			$item_transfer_arr[$row['REQUISITION_DTLS_ID']]['TRANSFER_QNTY']+=$row['TRANSFER_QNTY'];
		}
	}

	//echo '<pre>';print_r($item_transfer_arr);die;

	$sql="SELECT  c.id as ID, c.from_prod_id as FROM_PROD_ID, c.transfer_qnty as TRANSFER_QNTY, c.item_category as ITEM_CATEGORY, b.item_name as ITEM_NAME, a.item_code as ITEM_CODE, a.product_name_details as ITEM_DESCRIPTION
	from product_details_master a, lib_item_group b, inv_item_transfer_requ_dtls c
	where c.mst_id=$data_ref[0] and c.entry_form=516 and c.from_prod_id=a.id and a.item_group_id=b.id and c.status_active=1 and a.status_active in(1,3) ";
	//echo $sql;
	$dataArray=sql_select($sql);
	?>
	<table width="420" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" >
		<thead>
			<tr>
				<th width="100">Item Category</th>
				<th width="70">Item Group</th>
				<th width="50">Item code</th>
				<th width="100">Item Desc.</th>
				<th width="50">Req. Qty</th>
				<th>Bal. Qty</th>
			</tr>
		</thead>
		<tbody>
			<?
			$i=1;
			foreach($dataArray as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF";
				else $bgcolor="#FFFFFF";
				$tbl_id=$chk_array[$row["ID"]];
				$balance_qty=$row['TRANSFER_QNTY']-$item_transfer_arr[$row["ID"]]['TRANSFER_QNTY'];
				if ($balance_qty > 0) 
				{
					?>
					<tr bgcolor="<?=$bgcolor;?>" style="cursor: pointer;" onClick="get_php_form_data('<?echo $row['ID'];?>','requisition_transfer_details_form_data','requires/chemical_dyes_transfer_controller');">
						<td><? echo $item_category[$row['ITEM_CATEGORY']];?></td>
						<td><? echo $row['ITEM_NAME'];?></td>
						<td><? echo $row['ITEM_CODE'];?></td>
						<td><? echo $row['ITEM_DESCRIPTION'];?></td>
						<td align="right"><? echo $row['TRANSFER_QNTY'];?></td>
						<td align="right"><? echo $balance_qty; ?></td>
					</tr>
					<?
				}	
				$i++;
			}
			?>
		</tbody>
	</table>
	<?	
	exit();
}

if($action=='requisition_transfer_details_form_data')
{
	$sql="SELECT c.id as ID, c.from_prod_id as FROM_PROD_ID, c.transfer_qnty as TRANSFER_QNTY, c.item_category as ITEM_CATEGORY, c.from_store as FROM_STORE, c.to_store as TO_STORE, a.product_name_details as PRODUCT_NAME_DETAILS, a.unit_of_measure as UNIT_OF_MEASURE, a.current_stock as ITEM_GLOBAL_STOCK, a.avg_rate_per_unit as AVG_RATE_PER_UNIT, a.order_uom as ORDER_UOM, d.batch_lot as BATCH_LOT, sum((case when d.transaction_type in(1,4,5) then d.cons_quantity else 0 end)- (case when d.transaction_type in(2,3,6) then d.cons_quantity else 0 end)) as CURRENT_STOCK
	from product_details_master a, inv_item_transfer_requ_dtls c, inv_transaction d
	where c.id=$data and c.entry_form=516 and c.from_prod_id=a.id and c.from_prod_id=d.prod_id and d.store_id=c.from_store and c.status_active=1 and a.status_active in(1,3) and d.status_active=1 and d.status_active=1 and d.is_deleted=0 
	group by c.id, c.from_prod_id, c.transfer_qnty, c.item_category, c.from_store, c.to_store, a.product_name_details, a.unit_of_measure, a.current_stock, a.avg_rate_per_unit, a.order_uom, d.batch_lot order by c.id";

	$item_transfer_sql="SELECT requisition_dtls_id as REQUISITION_DTLS_ID, transfer_qnty as TRANSFER_QNTY from inv_item_transfer_dtls where requisition_dtls_id=$data and status_active=1";
	$item_transfer_result=sql_select($item_transfer_sql);
	$item_transfer_arr=array();
	if(count($item_transfer_result)>0)
	{
		foreach($item_transfer_result as $row)
		{
			$item_transfer_arr[$row['REQUISITION_DTLS_ID']]['REQUISITION_DTLS_ID']=$row['REQUISITION_DTLS_ID'];
			$item_transfer_arr[$row['REQUISITION_DTLS_ID']]['TRANSFER_QNTY']+=$row['TRANSFER_QNTY'];
		}
	}
	// echo $sql;die;
	$data_array = sql_select($sql);

	foreach ($data_array as $row) 
	{ 			
		$transfer_qnty=$row["TRANSFER_QNTY"]-$item_transfer_arr[$row['ID']]['TRANSFER_QNTY'];
		echo "document.getElementById('hidden_product_id').value 			= '".$row["FROM_PROD_ID"]."';\n";
		echo "document.getElementById('hidden_req_dtls_id').value 			= '".$data."';\n";
		echo "document.getElementById('cbo_item_category').value 			= '".$row["ITEM_CATEGORY"]."';\n";
		echo "document.getElementById('txt_item_desc').value 				= '".$row["PRODUCT_NAME_DETAILS"]."';\n";
		echo "document.getElementById('txt_transfer_qnty').value 			= '".$transfer_qnty."';\n";
		echo "document.getElementById('txt_current_stock').value 			= '".$row["CURRENT_STOCK"]."';\n";
		echo "document.getElementById('hidden_current_stock').value 		= '".$row["CURRENT_STOCK"]."';\n";
		echo "document.getElementById('txt_rate').value 					= '".$row["AVG_RATE_PER_UNIT"]."';\n";
		echo "document.getElementById('txt_transfer_value').value 			= '".$row["AVG_RATE_PER_UNIT"]*$transfer_qnty."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row["ORDER_UOM"]."';\n";
		echo "document.getElementById('txt_yarn_lot').value 				= '".$row["BATCH_LOT"]."';\n";
		echo "document.getElementById('txt_yarn_lot_dis').value 			= '".$row["BATCH_LOT"]."';\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row["FROM_STORE"]."';\n";
		echo "document.getElementById('cbo_store_name_to').value 			= '".$row["TO_STORE"]."';\n";

		echo "$('#cbo_item_category').attr('disabled', true);\n";
		echo "$('#txt_item_desc').attr('disabled', true);\n";
		echo "$('#cbo_store_name').attr('disabled', true);\n";
		echo "$('#cbo_store_name_to').attr('disabled', true);\n";
		echo "reset_on_change(".$row['TO_STORE'].");load_drop_down('requires/chemical_dyes_transfer_controller', ".$row['TO_STORE']."+'_'+document.getElementById('cbo_company_id').value, 'load_drop_floor','to_floor_td');\n";

	 	echo "load_room_rack_self_bin('requires/chemical_dyes_transfer_controller*5_6_7_22_23', 'floor','floor_td', document.getElementById('cbo_company_id').value,'"."','".$row["FROM_STORE"]."',this.value);storeUpdateUptoDisable();\n";

		//  echo "calculate_value()";
		
		exit();
	}
}

?>
