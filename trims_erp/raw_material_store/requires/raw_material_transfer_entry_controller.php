<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id,store_location_id FROM user_passwd where id='$user_id'");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];

if ($company_id !='') {
    $company_credential_cond = "and comp.id in($company_id)";
}
if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)"; 
}

// ========== user credential end ==========

// ===========================================================

$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");

if ($action=="upto_variable_settings")
{
	extract($_REQUEST);
	echo $variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$cbo_company_id' and item_category_id=101 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	exit();
}

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location", 160, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_room_rack_self_bin('requires/raw_material_transfer_entry_controller*101', 'store','from_store_td', $('#cbo_company_id').val(),this.value); " );
	//if( $('#cbo_transfer_criteria').val()*1==2 || $('#cbo_transfer_criteria').val()*1==4)  load_room_rack_self_bin('requires/raw_material_transfer_entry_controller*101*cbo_store_name_to', 'store','to_store_td', $('#cbo_company_id').val(),this.value);
	exit();
}

if ($action=="load_drop_down_location_to")
{
	$data=explode("_",$data);
	if($data[1]==2){
		//echo create_drop_down( "cbo_location_to", 160, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_room_rack_self_bin('requires/finish_fabric_transfer_controller*2', 'store','to_store_td', $('#cbo_company_id').val(),this.value);",1 );
	}else{
		echo create_drop_down( "cbo_location_to", 160, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_room_rack_self_bin('requires/raw_material_transfer_entry_controller*101*cbo_store_name_to', 'store','to_store_td', $('#cbo_company_id_to').val(),this.value);" );
	}
	exit();
}

if ($action=="load_room_rack_self_bin")
{
	$explodeData = explode('*', $data);
	$explodeData[11] = 'storeUpdateUptoDisable()';
	$data=implode('*', $explodeData);
	load_room_rack_self_bin("requires/raw_material_transfer_entry_controller",$data);
}

/*if ($action=="load_drop_down_store")
{

	echo create_drop_down( "cbo_store_name", 160, "select a.id,a.store_name from lib_store_location a,lib_store_location_category  b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data and b.category_type in (4) $store_location_credential_cond group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "get_php_form_data(this.value, 'get_from_store_location', 'requires/raw_material_transfer_entry_controller' );",0 );  	 
	exit();
}

if ($action=="load_drop_down_store_to")
{

	echo create_drop_down( "cbo_store_name_to", 160, "select a.id,a.store_name from lib_store_location a,lib_store_location_category  b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data and b.category_type in (4) $store_location_credential_cond group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "get_php_form_data(this.value, 'get_to_store_location', 'requires/raw_material_transfer_entry_controller' );",0 );  	 
	exit();
}*/

if($action=="varible_inventory")
{	 
	$sql_variable_inventory=sql_select("select id, independent_controll, rate_optional, is_editable, rate_edit  from variable_settings_inventory where company_name=$data and variable_list=20 and status_active=1 and menu_page_id=487");
	if(count($sql_variable_inventory)>0)
	{
		echo "1**".$sql_variable_inventory[0][csf("independent_controll")]."**".$sql_variable_inventory[0][csf("rate_optional")]."**".$sql_variable_inventory[0][csf("is_editable")]."**".$sql_variable_inventory[0][csf("rate_edit")];
	}
	else
	{
		echo "0**".$sql_variable_inventory[0][csf("independent_controll")]."**".$sql_variable_inventory[0][csf("rate_optional")]."**".$sql_variable_inventory[0][csf("is_editable")]."**".$sql_variable_inventory[0][csf("rate_edit")];
	}
	
	die;
}


if ($action=="itemDescription_popup")
{
	echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_item_category=str_replace("'","",$cbo_item_category);
	$cbo_transfer_criteria=str_replace("'","",$cbo_transfer_criteria);
?> 

	<script>
		
		/*$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });*/
		
		function js_set_value(data)
		{
			
			var data_ref=data.split("_");
			$('#product_id').val(data_ref[0]);
			//$('#job_id').val(data_ref[1]);
			$('#floor_id').val(data_ref[1]);
			$('#room').val(data_ref[2]);
			$('#rack').val(data_ref[3]);
			$('#shelf').val(data_ref[4]);
			$('#bin').val(data_ref[5]);
			//$('#po_id').val(data_ref[7]);
			//$('#order_no').val(data_ref[8]);
			//alert(data_ref[0]+'='+data_ref[1]+'='+data_ref[2]+'='+data_ref[3]+'='+data_ref[4]+'='+data_ref[5]+'='+data_ref[6]);
			parent.emailwindow.hide();
		}
		
		function order_style_empty(str)
		{
			if(str==1)
			{
				if($('#txt_style_no').val()!="")
				{
					$('#txt_order_no').attr('disabled',true);
				}
				else
				{
					$('#txt_order_no').attr('disabled',false);
				}
				if($('#txt_job_no').val()!="")
				{
					$('#txt_order_no').attr('disabled',true);
				}
				else
				{
					$('#txt_order_no').attr('disabled',false);
				}
			}
			else
			{
				if($('#txt_order_no').val()!="")
				{
					$('#txt_style_no').attr('disabled',true);
					$('#txt_job_no').attr('disabled',true);
				}
				else
				{
					$('#txt_style_no').attr('disabled',false);
					$('#txt_job_no').attr('disabled',false);
				}
			}
			
		}

		function open_style_job_order(type,company,store)
		{
			/*if( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}*/
			var page_link='raw_material_transfer_entry_controller.php?action=style_job_order_popup&company='+company+'&type='+type+'&store='+store;
			var title="Search Popup";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=315px,height=320px,center=1,resize=0,scrolling=0','../../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var item=this.contentDoc.getElementById("txt_selected_no").value;
				if(type==1)
				{
					$("#txt_style_no").val(item);
				}
				else if(type==2)
				{
					$("#txt_job_no").val(item);
				}
				else
				{
					$("#txt_order_no").val(item);
				}
			}
		}
	
    </script>

</head>

<body>
<div align="center" style="width:920px;">
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:870px;margin-left:10px;">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="800" class="rpt_table" align="center" style=" margin-left:100px;">
                <thead>
					<tr>
						<th colspan="5" align="center"><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
					</tr>
					<tr>
						<th width="150">Item Group</th>
						<th width="150">Search By</th>
						<th width="200" id="search_by_td_up">Please Enter Item Details</th>
						<th width="120">Section</th>
						
						<th>
						<input type="reset" name="reset" id="reset" value="Reset" style="width:80px;" class="formbutton" />
						<input type="hidden" name="product_id" id="product_id" class="text_boxes" value="">
						<input type="hidden" name="job_id" id="job_id" class="text_boxes" value="">
						<input type="hidden" name="floor_id" id="floor_id" class="text_boxes" value="">
						<input type="hidden" name="room" id="room" class="text_boxes" value="">
						<input type="hidden" name="rack" id="rack" class="text_boxes" value="">
						<input type="hidden" name="shelf" id="shelf" class="text_boxes" value="">
						<input type="hidden" name="bin" id="bin" class="text_boxes" value="">
						<input type="hidden" name="bin" id="po_id" class="po_id" value="">
						<input type="hidden" name="order_no" id="order_no" class="text_boxes" value="">
						
						</th>
					</tr>
                </thead>
                <tr class="general">
                    <td>
                    <?
                    //echo "select id,item_name  from lib_item_group where item_category=101 and status_active=1 order by item_name";
					if($cbo_item_category!=0) $item_category_cond=" and item_category='$cbo_item_category'"; else  $item_category_cond="";
                     	echo create_drop_down( "cbo_item_group_id", 130,"select id,item_name  from lib_item_group where item_category=101 and status_active=1 order by item_name","id,item_name", 1, "-- Select --", "", "","","","","","");
					?>
                    </td>
                    <td>
						<?
							$search_by_arr=array(1=>"Item Details",2=>"Product Id.");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td id="search_by_td">
                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td><? echo create_drop_down( "cboSection", 120, $trims_section,"", 1, "-- Select Section --","","",0,'','','','','','',"cboSection[]"); ?></td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('cbo_item_group_id').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_store_name; ?>+'_'+document.getElementById('cboSection').value+'_'+<? echo $cbo_transfer_criteria; ?>, 'create_product_search_list_view', 'search_div', 'raw_material_transfer_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:80px;" />
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
	
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
  	$size_arr = return_library_array("select id, size_name from lib_size","id","size_name");
	//echo $data;die;
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[3];
	$from_store_id =str_replace("'", "",$data[4]);
	$section =$data[5];
	//$order_no =$data[6];
	//$job_no =$data[7];
	$transfer_criteria =$data[6];
	
	$item_group_cond="";
	if($data[2]!=0) $item_group_cond=" and a.item_group_id=$data[2] ";
	if($section!=0) $section_cond=" and a.section_id=$section ";
	if($search_by==1) $search_field="and a.product_name_details";	 
	else if($search_by==2)  $search_field="and a.id";

	if($data[0]!=''){
		$search_field_cond="$search_field like '$search_string'"; 
	}
	/*$style_order_cond="";
	if($style_no!="" || $job_no!="" || $order_no!="")
	{
		if($style_no!="") $style_order_cond=" and d.style_ref_no='$style_no'";
		if($job_no!="") $style_order_cond.=" and d.job_no_prefix_num='$job_no'";
		if($order_no!="") $style_order_cond.=" and c.po_number='$order_no'";
	}
	else
	{
		echo "Please insert style or order.";die;
	}*/
	$store_cond="";
	if($from_store_id>0) $store_cond=" and e.store_id=$from_store_id"; 
	
	
 	//$sql="select id, company_id, supplier_id, product_name_details, lot,item_group_id, current_stock,a.color,a.item_size, brand from product_details_master where item_category_id in (4) and company_id=$company_id and $search_field like '$search_string' and current_stock>0 and status_active=1 and is_deleted=0 $item_group_cond  $item_category_cond";
	
	/*echo $sql="SELECT a.id, a.company_id, a.supplier_id, a.product_name_details, a.lot, a.item_group_id, a.current_stock, a.color, a.item_size, c.id as po_id, c.po_number, d.id as job_id, d.job_no, sum((case when b.trans_type in(1,4,5) then quantity else 0 end)-(case when b.trans_type in(2,3,6) then quantity else 0 end)) as job_balance, e.store_id, e.floor_id, e.room, e.rack, e.self, e.bin_box
	from product_details_master a, inv_transaction e, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d  
	where a.id=e.prod_id and e.id=b.trans_id and a.id=b.prod_id  and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.item_category_id in (101) and a.entry_form=334  and a.company_id=$company_id $search_field like '$search_string' and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $item_group_cond  $style_order_cond $store_cond
	group by a.id, a.company_id, a.supplier_id, a.product_name_details, a.lot, a.item_group_id, a.current_stock,a.color,a.item_size,a.color,a.item_size, c.id, c.po_number, d.id, d.job_no, e.store_id, e.floor_id, e.room, e.rack, e.self, e.bin_box 
	order by a.item_group_id";*/

	$sql="SELECT a.id, a.company_id, a.product_name_details, a.sub_group_name,a.item_code, a.section_id, a.item_group_id, a.current_stock, sum((case when e.transaction_type in(1,4,5) then e.cons_quantity else 0 end)-(case when e.transaction_type in(2,3,6) then e.cons_quantity else 0 end)) as balance, e.store_id, e.floor_id, e.room, e.rack, e.self, e.bin_box
	from product_details_master a, inv_transaction e
	where a.id=e.prod_id and a.item_category_id in (101) and a.entry_form=334  and a.company_id=$company_id $item_group_cond  $store_cond $section_cond $search_field_cond and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 
	group by a.id, a.company_id, a.product_name_details, a.sub_group_name,a.item_code, a.section_id, a.item_group_id, a.current_stock, e.store_id, e.floor_id, e.room, e.rack, e.self, e.bin_box
	order by a.item_group_id";
	
	//echo $sql;//die;

	$item_group_arr=return_library_array( "select id,item_name  from lib_item_group where   status_active=1 ",'id','item_name');
	$conversion_factor=return_library_array("select id, conversion_factor from lib_item_group","id","conversion_factor");
	//$arr=array(1=>$company_arr,2=>$supplier_arr,3=>$item_group_arr);
	$store_name_arr = return_library_array("select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$company_id and b.category_type in(4) and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id","store_name");

	$floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where company_id=$company_id and status_active=1 and is_deleted=0 group by floor_room_rack_id, floor_room_rack_name order by floor_room_rack_name","floor_room_rack_id","floor_room_rack_name");
    
	//echo  create_list_view("tbl_list_search", "Item ID,Company,Supplier,Item Group,Item Details,Job No,Stock", "80,120,120,120,180,110","900","250",0, $sql, "js_set_value", "id,job_id", "", 1, "0,company_id,supplier_id,item_group_id,0,0,0", $arr, "id,company_id,supplier_id,item_group_id,product_name_details,job_no,job_balance", '','','0,0,0,0,0,0,2');
	
	?>
    <table cellpadding="0" cellspacing="0" width="1290" class="rpt_table" border="1" rules="all" align="left">
    	<thead>
        	<tr>
            	<th width="40">SL</th>
                <th width="80">Item ID</th>
                <th width="120">Item Group</th>
                <th width="100">Sub Group</th>
                <th width="100">Item Code</th>
                <th width="180">Item Details</th>
                <th width="100">Section</th>
                <th width="100">Store Name</th>
                <th width="80">Floor</th>
                <th width="80">Room</th>
                <th width="80">Rack</th>
                <th width="80">Self</th>
                <th width="80">Bin/Box</th>
                <th>Stock</th>
            </tr>
        </thead>
    </table>
    <div style="width:1308px; overflow-y:scroll; max-height:250px; float:left;" id="scroll_body">
    <table cellpadding="0" cellspacing="0" width="1290" class="rpt_table" border="1" rules="all" id="tbl_list_search" align="left">
    	<tbody>
        <?
		
		$sql_result=sql_select($sql);
		$i=1;
		foreach($sql_result as $row)
		{
			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			$job_qnty=$conversion_factor[$row[csf("item_group_id")]]*$row[csf("balance")];
			
			$getSize=explode(",",$row[csf("product_name_details")]); 
			
			?>
        	<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf("id")].'_'.$row[csf("floor_id")].'_'.$row[csf("room")].'_'.$row[csf("rack")].'_'.$row[csf("self")].'_'.$row[csf("bin_box")]; ?>')" id="tr_<? echo $i; ?>" style="cursor:pointer;">
            	<td width="40" align="center"><? echo $i; ?></td>
                <td width="80"><? echo $row[csf("id")]; ?></td>
                <td width="120"><p><? echo $item_group_arr[$row[csf("item_group_id")]]; ?></p></td>
                <td width="100"><p><? echo $row[csf("sub_group_name")]; ?></p></td>
                <td width="100"><p><? echo $row[csf("item_code")]; ?></p></td>
                <td width="180"><p><? echo $row[csf("product_name_details")]; ?></p></td>
                <td width="100"><p><? echo $trims_section[$row[csf("section_id")]]; ?></p></td>
                <td width="100"><p><? echo $store_name_arr[$row[csf("store_id")]]; ?></p></td>
                <td width="80"><p><? echo $floor_room_rack_arr[$row[csf("floor_id")]]; ?></p></td>
                <td width="80"><p><? echo $floor_room_rack_arr[$row[csf("room")]]; ?></p></td>
                <td width="80"><p><? echo $floor_room_rack_arr[$row[csf("rack")]]; ?></p></td>
                <td width="80"><p><? echo $floor_room_rack_arr[$row[csf("self")]]; ?></p></td>
                <td width="80"><p><? echo $floor_room_rack_arr[$row[csf("bin_box")]]; ?></p></td>
                <td align="right"><? echo number_format($job_qnty,2,'.',''); ?></td>	
            </tr>
            <?
			$total_job_qnty+=$job_qnty;
			$i++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th align="right">Total:</th>
                <th align="right"><? echo number_format($total_job_qnty,2,'.',''); ?></th>
            </tr>
            
        </tfoot>
    </table>
    </div>
    <?
	
	
	exit();
}

if($action=="style_job_order_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	//echo $style_id;die;

	?>
    <script>
		
		function js_set_value(data)
		{
			$('#txt_selected_no').val(data);
			parent.emailwindow.hide();
		}
    </script>
    <?
	$company=str_replace("'","",$company);
	$type=str_replace("'","",$type);
	$store=str_replace("'","",$store);
	
	if($type==1)
	{
		$sql="select d.style_ref_no from product_details_master a, inv_transaction e, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d  where a.id=e.prod_id and e.id=b.trans_id and a.id=b.prod_id  and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.item_category_id in (4) and a.entry_form=24  and a.company_id=$company and e.store_id=$store and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 group by  d.style_ref_no";
		$arr=array();
		echo create_list_view("list_view", "Style Ref. NO.","280","300","300",0, $sql , "js_set_value", "style_ref_no", "", 1, "0", $arr, "style_ref_no", "","setFilterGrid('list_view',-1)","0,0","");
	}
	else if($type==2)
	{
		$sql="select d.job_no_prefix_num from product_details_master a, inv_transaction e, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d  where a.id=e.prod_id and e.id=b.trans_id and a.id=b.prod_id  and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.item_category_id in (4) and a.entry_form=24  and a.company_id=$company and e.store_id=$store and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 group by  d.job_no_prefix_num";
		//echo $sql; die;
		$arr=array();
		echo create_list_view("list_view", "JOB NO.","280","300","300",0, $sql , "js_set_value", "job_no_prefix_num", "", 1, "0", $arr, "job_no_prefix_num", "","setFilterGrid('list_view',-1)","0,0","");
	}
	else
	{
		$sql="select c.po_number from product_details_master a, inv_transaction e, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d  where a.id=e.prod_id and e.id=b.trans_id and a.id=b.prod_id  and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.item_category_id in (4) and a.entry_form=24  and a.company_id=$company and e.store_id=$store and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 group by  c.po_number";
		$arr=array();
		echo create_list_view("list_view", "PO NO.","280","300","300",0, $sql , "js_set_value", "po_number", "", 1, "0", $arr, "po_number", "","setFilterGrid('list_view',-1)","0,0","");
	}
	echo "<input type='hidden' id='txt_selected_no' />";
	
	?>
    <script language="javascript" type="text/javascript">
	</script>
    
    <?
	exit();
}

if ($action=="itemDescription_com_popup")
{
	echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_item_category=str_replace("'","",$cbo_item_category);
	$cbo_transfer_criteria=str_replace("'","",$cbo_transfer_criteria);
	?> 
	<script>
		
		/*$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });*/
		
		function js_set_value(data)
		{
			//$('#product_id').val(data);
			var data_ref=data.split("_");
			$('#product_id').val(data_ref[0]);
			$('#floor_id').val(data_ref[1]);
			$('#room').val(data_ref[2]);
			$('#rack').val(data_ref[3]);
			$('#shelf').val(data_ref[4]);
			$('#bin').val(data_ref[5]);
			parent.emailwindow.hide();
		}
	
    </script>

    </head>
    
    <body>
    <div align="center" style="width:920px;">
        <form name="searchdescfrm"  id="searchdescfrm">
            <fieldset style="width:900px;">
            <legend>Enter search words</legend>
                <table cellpadding="0" cellspacing="0" width="900" class="rpt_table">
                    <thead>
                        <th width="250">Item Group</th>
                        <th width="250">Search By</th>
                        <th width="250" id="search_by_td_up">Please Enter Item Details</th>
                        <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:80px;" class="formbutton" />
                        <input type="hidden" name="product_id" id="product_id" class="text_boxes" value="">
                        <input type="hidden" name="job_id" id="job_id" class="text_boxes" value="">
                        <input type="hidden" name="floor_id" id="floor_id" class="text_boxes" value="">
                        <input type="hidden" name="room" id="room" class="text_boxes" value="">
                        <input type="hidden" name="rack" id="rack" class="text_boxes" value="">
                        <input type="hidden" name="shelf" id="shelf" class="text_boxes" value="">
                        <input type="hidden" name="bin" id="bin" class="text_boxes" value="">
                        </th>
                    </thead>
                    <tr class="general">
                        <td align="center">
                        <?
                          if($cbo_item_category!=0) $item_category_cond=" and item_category='$cbo_item_category'"; else  $item_category_cond="";
                          echo create_drop_down( "cbo_item_group_id", 200,"select id,item_name  from lib_item_group where   status_active=1 $item_category_cond","id,item_name", 1, "-- Select --", "", "","","","","","");
                        ?>
                        </td>
                        <td align="center">
                            <?
                                $search_by_arr=array(1=>"Item Details",2=>"Product Id.");
                                $dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";
                                echo create_drop_down( "cbo_search_by", 200, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                            ?>
                        </td>
                        <td id="search_by_td" align="center">
                            <input type="text" style="width:200px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('cbo_item_group_id').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_store_name; ?>+'_'+<? echo $cbo_transfer_criteria; ?>, 'create_product_com_search_list_view', 'search_div', 'raw_material_transfer_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:80px;" />
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

if($action=='create_product_com_search_list_view')
{
	
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[3];
	$from_store_id =str_replace("'", "",$data[4]);
	$transfer_criteria =$data[5];
	
	$item_group_cond="";
	if($data[2]!=0) $item_group_cond=" and a.item_group_id=$data[2] ";
	if($search_by==1) $search_field=" a.product_name_details";	 
	else if($search_by==2)  $search_field=" a.id";
	
 	//$sql="select id, company_id, supplier_id, product_name_details, lot,item_group_id, current_stock, brand from product_details_master where item_category_id in (4) and company_id=$company_id and $search_field like '$search_string' and current_stock>0 and status_active=1 and is_deleted=0 $item_group_cond  $item_category_cond";
	
	$sql="SELECT a.id, a.company_id, a.supplier_id, a.product_name_details, a.item_group_id, a.current_stock, sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as prod_balance, b.floor_id, b.room, b.rack, b.self, b.bin_box
	from product_details_master a, inv_transaction b  
	where a.id=b.prod_id and a.item_category_id in (4) and a.entry_form=20  and a.company_id=$company_id and $search_field like '$search_string' and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $item_group_cond 
	group by a.id, a.company_id, a.supplier_id, a.product_name_details, a.item_group_id, a.current_stock, b.floor_id, b.room, b.rack, b.self, b.bin_box";
	
	//echo $sql;//die;

	$item_group_arr=return_library_array( "select id,item_name  from lib_item_group where   status_active=1 ",'id','item_name');
	$arr=array(1=>$company_arr,2=>$supplier_arr,3=>$item_group_arr);

	echo  create_list_view("tbl_list_search", "Item ID,Company,Supplier,Item Group,Item Details,Stock", "80,150,150,130,200","900","380",0, $sql, "js_set_value", "id,floor_id,room,rack,self,bin_box", "", 1, "0,company_id,supplier_id,item_group_id,0,0", $arr, "id,company_id,supplier_id,item_group_id,product_name_details,prod_balance", '','','0,0,0,0,0,2');
	
	exit();
}


if($action=='populate_data_from_product_master')
{
	
	//echo $chemical_lot; 
	$data_ref=explode("**",$data);
	$product_id=$data_ref[0];
	//$job_id=$data_ref[1];
	//$order_no=$data_ref[2];
	$store_id=$data_ref[1];
	$transfer_criteria=$data_ref[2];
	$floor_id=$data_ref[3];
	$room=$data_ref[4];
	$rack=$data_ref[5];
	$self=$data_ref[6];
	$bin=$data_ref[7];
	//$po_id=$data_ref[10];
	// echo $floor_id.'='.$room.'='.$rack.'='.$self.'='.$bin;die;
	
	$sqlCon="";
	if ($floor_id!="") { $sqlCon= " and e.floor_id=$floor_id"; }
	if($room!="") { $sqlCon.= " and e.room=$room"; }
	if($rack!="") { $sqlCon.= " and e.rack=$rack"; }
	if($self!="") { $sqlCon.= " and e.self=$self"; }
	if($bin!="") { $sqlCon.= " and e.bin_box=$bin"; }
	// echo $sqlCon;die;
	if($transfer_criteria==1)
	{
		$sql="SELECT a.id, a.product_name_details, a.current_stock, a.avg_rate_per_unit, a.supplier_id, a.unit_of_measure as item_uom, a.item_group_id, 0 as job_id, null as job_no, null as style_ref_no, sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as balance, b.company_id, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box
		from product_details_master a, inv_transaction b  
		where a.id=b.prod_id and a.item_category_id in (4) and a.id=$product_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by a.id, a.product_name_details, a.current_stock, a.avg_rate_per_unit, a.supplier_id, a.unit_of_measure, a.item_group_id, b.company_id, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box";
	}
	else
	{
		/*if($order_no!="")
		{
			$sql="SELECT a.id, a.product_name_details, a.current_stock, a.avg_rate_per_unit, a.supplier_id, a.unit_of_measure as item_uom, a.item_group_id, c.id as order_id, c.po_number, d.id as job_id, d.job_no, d.style_ref_no, sum((case when b.trans_type in(1,4,5) then quantity else 0 end)-(case when b.trans_type in(2,3,6) then quantity else 0 end)) as job_balance, e.company_id, e.store_id, e.floor_id, e.room, e.rack, e.self, e.bin_box
			from product_details_master a,  inv_transaction e, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d  
			where a.id=e.prod_id and e.id=b.trans_id and a.id=b.prod_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.item_category_id in(4) and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$product_id and c.po_number='$order_no' and c.id=$po_id and d.id=$job_id and e.store_id=$store_id $sqlCon
			group by a.id, a.product_name_details, a.current_stock, a.avg_rate_per_unit, a.supplier_id, a.unit_of_measure, a.item_group_id, c.id, c.po_number, d.id, d.job_no, d.style_ref_no, e.company_id, e.store_id, e.floor_id, e.room, e.rack, e.self, e.bin_box";
		}
		else
		{
			$sql="SELECT a.id, a.product_name_details, a.current_stock, a.avg_rate_per_unit, a.supplier_id, a.unit_of_measure as item_uom, a.item_group_id, c.id as order_id, c.po_number, d.id as job_id, d.job_no, d.style_ref_no, sum((case when b.trans_type in(1,4,5) then quantity else 0 end)-(case when b.trans_type in(2,3,6) then quantity else 0 end)) as job_balance, e.company_id, e.store_id, e.floor_id, e.room, e.rack, e.self, e.bin_box
			from product_details_master a,  inv_transaction e, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d  
			where a.id=e.prod_id and e.id=b.trans_id and a.id=b.prod_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.item_category_id in(4) and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$product_id and d.id=$job_id and e.store_id=$store_id $sqlCon
			group by a.id, a.product_name_details, a.current_stock, a.avg_rate_per_unit, a.supplier_id, a.unit_of_measure, a.item_group_id, c.id, c.po_number, d.id, d.job_no, d.style_ref_no, e.company_id, e.store_id, e.floor_id, e.room, e.rack, e.self, e.bin_box";
		}*/

		$sql="SELECT a.id, a.company_id, a.supplier_id, a.product_name_details, a.lot, a.item_group_id, a.current_stock, a.unit_of_measure as item_uom ,sum((case when e.transaction_type in(1,4,5) then e.cons_quantity else 0 end)-(case when e.transaction_type in(2,3,6) then e.cons_quantity else 0 end)) as balance, e.store_id, e.floor_id, e.room, e.rack, e.self, e.bin_box
		from product_details_master a, inv_transaction e
		where a.id=e.prod_id and a.item_category_id in (101) and a.entry_form=334 and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.store_id=$store_id and a.id=$product_id $sqlCon
		group by a.id, a.company_id, a.supplier_id, a.product_name_details, a.lot, a.item_group_id, a.current_stock, a.unit_of_measure, e.store_id, e.floor_id, e.room, e.rack, e.self, e.bin_box 
		order by a.item_group_id";
		//
		
		$sql_order_rate="select c.id as trans_id, a.id as prod_id, c.cons_quantity, c.cons_amount
		from product_details_master a, inv_transaction c
		where a.id=c.prod_id and a.item_category_id='101' and a.entry_form=334 and c.transaction_type in(1,5) and c.prod_id in($product_id) and c.store_id=$store_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.current_stock>0";
		//
		//echo $sql_order_rate;die;
		$sql_order_rate_result=sql_select($sql_order_rate);
		$order_item_data=array();
		foreach($sql_order_rate_result as $row)
		{
			if($trans_id_check[$row[csf("trans_id")]]=="")
			{
				$trans_id_check[$row[csf("trans_id")]]=$row[csf("trans_id")];
				$order_item_data[$row[csf("prod_id")]]["cons_quantity"]+=$row[csf("cons_quantity")];
				$order_item_data[$row[csf("prod_id")]]["cons_amount"]+=$row[csf("cons_amount")];
			}
		}
		
	}
	
	//echo "<pre>";print_r($order_item_data);die;
	//echo $sql;die;
	
	
	$coversion_sql=sql_select("select conversion_rate from currency_conversion_rate where currency=2 order by con_date desc");
	$coversion_rate=$coversion_sql[0][csf("conversion_rate")];
	unset($coversion_sql);

	//echo $sql;
	//$data_array=sql_select("select product_name_details, lot, current_stock, avg_rate_per_unit, brand,item_category_id, supplier_id, unit_of_measure as item_uom from product_details_master where id='$data'");
	
	$data_array=sql_select($sql);
	/*echo "<pre>";
	print_r($data_array);die;*/
	foreach ($data_array as $row)
	{
		if($transfer_criteria==1)
		{
			$rate= number_format($row[csf("avg_rate_per_unit")],2,".",""); 
			$stock_qnty=$row[csf("balance")];
		}
		else
		{
			$conversion_factor=return_field_value("conversion_factor as data","lib_item_group","id='".$row[csf("item_group_id")]."'","data"); 
			//$rate= number_format(($row[csf("avg_rate_per_unit")]/$coversion_rate),4,".",""); 
			$order_rate=($order_item_data[$row[csf("id")]]["cons_amount"]/$order_item_data[$row[csf("id")]]["cons_quantity"]);
			$rate=number_format(($order_rate/$coversion_rate),6,".",""); 
			$stock_qnty=$row[csf("balance")];
		}
		
		
		echo "document.getElementById('hidden_product_id').value 			= '".$product_id."';\n";
		echo "document.getElementById('txt_item_desc').value 				= '".$row[csf("product_name_details")]."';\n";
		echo "document.getElementById('txt_current_stock').value 			= '".number_format($stock_qnty,2,'.','')."';\n";
		echo "document.getElementById('hidden_current_stock').value 		= '".number_format($stock_qnty,2,'.','')."';\n";
		echo "document.getElementById('txt_rate').value 					= '".number_format($rate,6,'.','')."';\n";
		//echo "document.getElementById('txt_rate').value 					= '".$row[csf("avg_rate_per_unit")]."';\n";
		//echo "document.getElementById('txt_style_no').value 				= '".$row[csf("style_ref_no")]."';\n";
		//echo "document.getElementById('txt_order_id').value 				= '".$row[csf("order_id")]."';\n";
		//echo "document.getElementById('txt_order_no').value 				= '".$row[csf("po_number")]."';\n";
		//echo "document.getElementById('job_id').value 						= '".$row[csf("job_id")]."';\n";
		//echo "document.getElementById('cbo_supplier').value 				= '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("item_uom")]."';\n";
		//
		$from_company_id=str_replace("'", "", $row[csf("company_id")]);
		$from_store_id=str_replace("'", "", $row[csf("store_id")]);
		$floor_id=str_replace("'", "", $row[csf("floor_id")]);
		$room=str_replace("'", "", $row[csf("room")]);
		$rack=str_replace("'", "", $row[csf("rack")]);
		$self=str_replace("'", "", $row[csf("self")]);
		$bin=str_replace("'", "", $row[csf("bin_box")]);
		if($floor_id !=0)
		{
			echo "load_room_rack_self_bin('requires/raw_material_transfer_entry_controller*101', 'floor','floor_td', '".$from_company_id."','"."','".$from_store_id."',this.value);\n";
			echo "document.getElementById('cbo_floor').value 				= '".$floor_id."';\n";
		}
		if($room !=0)
		{
			echo "load_room_rack_self_bin('requires/raw_material_transfer_entry_controller*101', 'room','room_td', '".$from_company_id."','"."','".$from_store_id."','".$floor_id."',this.value);\n";
			echo "document.getElementById('cbo_room').value 				= '".$room."';\n";
		}
		if($rack !=0)
		{
			echo "load_room_rack_self_bin('requires/raw_material_transfer_entry_controller*101', 'rack','rack_td', '".$from_company_id."','"."','".$from_store_id."','".$floor_id."','".$room."',this.value);\n";
			echo "document.getElementById('txt_rack').value 				= '".$rack."';\n";
		}
		if($self !=0)
		{
			echo "load_room_rack_self_bin('requires/raw_material_transfer_entry_controller*101', 'shelf','shelf_td', '".$from_company_id."','"."','".$from_store_id."','".$floor_id."','".$room."','".$rack."',this.value);\n";
			echo "document.getElementById('txt_shelf').value 				= '".$self."';\n";
		}

		if($bin !=0)
		{
			echo "load_room_rack_self_bin('requires/raw_material_transfer_entry_controller*101', 'bin','bin_td', '".$from_company_id."','"."','".$from_store_id."','".$floor_id."','".$room."','".$rack."','".$self."',this.value);\n";
			echo "document.getElementById('cbo_bin').value 					= '".$bin."';\n";
		}
	}
    exit();
}

if($action=='get_from_store_location')
{
	
	//echo $chemical_lot;
	$data_array=sql_select("select store_location from  lib_store_location where id='$data'");
	foreach ($data_array as $row)
	{ 
		//echo "document.getElementById('from_store_location').value 				= '".$row[csf("store_location")]."';\n";
	}
        exit();
}
if($action=='get_to_store_location')
{
	//echo $chemical_lot;
	$data_array=sql_select("select store_location from  lib_store_location where id='$data'");
	foreach ($data_array as $row)
	{ 
		//echo "document.getElementById('to_store_location').value 				= '".$row[csf("store_location")]."';\n";
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
			var id=data.split("_");
			$('#transfer_id').val(id[0]);
			$('#hidden_posted_in_account').val(id[1]);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:800px;">
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:800px;margin-left:5px">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="800" class="rpt_table">
                <thead>
                    <th width="200">Search By</th>
                    <th width="200" id="search_by_td_up">Please Enter Transfer ID</th>
                    <th width="250">Date Range</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                        <input type="hidden" name="transfer_id" id="transfer_id" class="text_boxes" value="">
                        <input type="hidden" name="hidden_posted_in_account" id="hidden_posted_in_account" class="text_boxes" value="">
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
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px">To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
                    </td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_transfer_criteria; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value, 'create_transfer_search_list_view', 'search_div', 'raw_material_transfer_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                	<td colspan="4" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
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
	$transfer_criteria =$data[3];
	
	$date_form=$data[4];
	$date_to =$data[5];
	$year_id=$data[6];
	
	if($db_type==0)
	{
		$date_form=change_date_format($date_form,'yyyy-mm-dd');
		$date_to=change_date_format($date_to,'yyyy-mm-dd');
	}
	else
	{
		$date_form=change_date_format($date_form,'','',1);
		$date_to=change_date_format($date_to,'','',1);
	}
	
	if($date_form!="" && $date_to!="") $date_cond=" and transfer_date between '$date_form' and '$date_to'";
	
	if($search_by==1)
		$search_field="transfer_prefix_number";	
	else
		$search_field="challan_no";
		
	if($db_type==0) $year_field="YEAR(insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	
	if($year_id>0)
	{
		if($db_type==0)
		{
			$year_condition=" and YEAR(insert_date)='$year_id'";
		}
		else
		{
			$year_condition=" and to_char(insert_date,'YYYY')='$year_id'";
		}
	}
	
	if($transfer_criteria>0) $criteria_cond=" and transfer_criteria in($transfer_criteria)"; else $criteria_cond=" and transfer_criteria in(1,2)";
	
	$user_id = $_SESSION['logic_erp']['user_id'];
	$userCredential = sql_select("SELECT unit_id as company_id,store_location_id FROM user_passwd where id='$user_id'");
	$cre_company_id = $userCredential[0][csf('company_id')];
	$cre_store_location_id = $userCredential[0][csf('store_location_id')];
	
	if ($cre_company_id !='') {
		$company_credential_cond = "and company_id in($cre_company_id)";
	}
	if ($cre_store_location_id !='') {
		$store_location_credential_cond = "and a.id in($cre_store_location_id)"; 
	}
	
 	$sql="select id, $year_field transfer_prefix_number, transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, is_posted_account from inv_item_transfer_mst where entry_form=487 and company_id=$company_id and $search_field like '$search_string' $criteria_cond and status_active=1 and is_deleted=0 $date_cond $year_condition order by id desc";
	//echo $sql;
	$arr=array(3=>$company_arr,5=>$item_transfer_criteria);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria", "70,60,100,120,90","700","270",0, $sql, "js_set_value", "id,is_posted_account", "", 1, "0,0,0,company_id,0,transfer_criteria", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria", '','','0,0,0,0,3,0');
	exit();
}

if($action=='populate_data_from_transfer_master')
{
	
	$data_array=sql_select("select transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, to_company from inv_item_transfer_mst where id='$data'");

	$company_id=$data_array[0][csf("company_id")];
	$variable_inventory_sql=sql_select("select store_method, rack_balance from variable_settings_inventory  where company_name=$company_id and item_category_id=4 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$store_method=$variable_inventory_sql[0][csf("store_method")];

	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row[csf("transfer_criteria")]."';\n";
		echo "document.getElementById('store_update_upto').value 			= '".$store_method."';\n";
		
		echo "active_inactive(".$row[csf("transfer_criteria")].");\n";
		
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";
		if ($row[csf("transfer_criteria")]==2) {
			echo "document.getElementById('cbo_company_id_to').value 		= '".$row[csf("company_id")]."';\n";
		}
		else{
			echo "document.getElementById('cbo_company_id_to').value 		= '".$row[csf("to_company")]."';\n";
		}
		//echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";
		
		echo "$('#cbo_transfer_criteria').attr('disabled','disabled');\n";
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n"; 
		
		exit();
	}
}

if($action=="show_transfer_listview")
{
	//$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id in (101) ","id","product_name_details");
	$sql="SELECT a.id, a.from_store, a.to_store, a.from_prod_id, a.transfer_qnty,b.item_group_id,b.section_id,b.gmts_size,b.unit_of_measure,b.item_category_id,b.product_name_details  from inv_item_transfer_dtls a, product_details_master b where a.mst_id='$data' and a.from_prod_id=b.id and a.status_active = '1' and a.is_deleted = '0'";
	$item_group_arr=return_library_array( "select id,item_name  from lib_item_group where   status_active=1 ",'id','item_name');
	
	$result = sql_select($sql);
	$arr=array(0=>$store_arr,1=>$store_arr,2=>$product_arr);
	?>

	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table">
		<thead>
		    <th width="40">SL No</th>
		    <th width="80">Item Category</th>
		    <th width="100">Item Group</th>
		    <th width="80">Section</th>
		    <th width="120">Item Description</th>
		    <th width="60">Item Size</th>
		    <th width="80">From Store</th>
		    <th width="80">To Store</th>
		    <th width="60"> Qty.</th>
		    <th> UOM</th>
		</thead>
	</table>
	<div style="width:880px; max-height:250px; overflow-y:scroll">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="860" class="rpt_table" id="list_view">
		<? 
		$i=1;
		foreach ($result as $row)
		{
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
		    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="populate_data('<? echo $row[csf('id')]; ?>')"> 				
		        <td width="40">
				<? echo "$i"; ?>
		        </td>	
		        <td width="80"><p><? echo $item_category[$row[csf('item_category_id')]];?></p></td>
		        <td width="100"><p><? echo $item_group_arr[$row[csf('item_group_id')]];?></p></td>
		        <td width="80"><p><? echo $trims_section[$row[csf('section_id')]];?></p></td>
		        <td width="120"><p><? echo $row[csf('product_name_details')]; ?></p></td> 
		        <td width="60"><p><? echo $size_arr[$row[csf('gmts_size')]]; ?></p></td>
		        <td width="80"><p><? echo $store_arr[$row[csf('from_store')]]; ?></p></td>
		        <td width="80"><p><? echo $store_arr[$row[csf('to_store')]]; ?>&nbsp;</p></td>
		        <td align="right" width="60"><p><? echo $row[csf('transfer_qnty')]; ?>&nbsp;</p></td>
		        <td align="center"><p><? echo $unit_of_measurement[$row[csf('unit_of_measure')]]; ?></p></td>
		    </tr>
		    <?
			$i++;     
		}
		?>
		</table>
	</div>
	<?
	//echo  create_list_view("list_view", "From Store,To Store,Item Description,Transfered Qnty", "150,150,250","680","200",0, $sql, "get_php_form_data", "id", "'populate_transfer_details_form_data'", 0, "from_store,to_store,from_prod_id,0", $arr, "from_store,to_store,from_prod_id,transfer_qnty", "requires/raw_material_transfer_entry_controller",'','0,0,0,2');
	//echo  create_list_view("list_view", "Item Category,Item Group,Section,Item Description,Item Size,From Store,To Store,Qty.,UOM", "80,80,80,120,80,100,100,60","780","200",0, $sql, "get_php_form_data", "id", "'populate_transfer_details_form_data'", 0, "from_store,to_store,from_prod_id,0", $arr, "from_store,to_store,from_prod_id,transfer_qnty", "requires/raw_material_transfer_entry_controller",'','0,0,0,2');
	
	exit();
}

if($action=='populate_transfer_details_form_data')
{
	
	$data_array=sql_select("SELECT a.transfer_criteria,a.company_id,a.to_company,a.transfer_criteria, b.id, b.mst_id, b.from_store, b.to_store,b.floor_id,b.room,b.rack,b.shelf,b.bin_box,b.to_floor_id,b.to_room,b.to_rack,b.to_shelf,b.to_bin_box, b.from_prod_id, b.to_prod_id, b.transfer_qnty, b.rate, b.item_category, b.transfer_value, b.yarn_lot, b.brand_id, b.uom, b.job_id, b.po_number, b.style_ref_no, b.remarks,a.location_id,a.to_location_id from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id=b.mst_id and b.id='$data'");
	$coversion_sql=sql_select("select conversion_rate from currency_conversion_rate where currency=2 order by con_date desc");

	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$sql = "select id,location_id,company_id from lib_store_location where id=$store_id and company_id=$from_company and status_active=1 and is_deleted=0";
	$result = sql_select($sql);
	foreach ($result as $row) {
		$location_name_arr[$row[csf("id")]][$row[csf("company_id")]]=$location_arr[$row[csf("location_id")]];
	}

	$coversion_rate=$coversion_sql[0][csf("conversion_rate")];
	foreach ($data_array as $row)
	{
		if ($row[csf("transfer_criteria")]==1) {
			$company_id=$row[csf("to_company")];
		}
		else
		{
			$company_id=$row[csf("company_id")];
		}
		//$rate= number_format(($row[csf("rate")]/$coversion_rate),2,".","");

		$rate= number_format(($row[csf("rate")]/$coversion_rate),4,".","");
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_location').value 				= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('cbo_location_to').value 				= '".$row[csf("to_location_id")]."';\n";

		echo "load_room_rack_self_bin('requires/raw_material_transfer_entry_controller*101', 'store','from_store_td', $('#cbo_company_id').val(),'"."',this.value);\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("from_store")]."';\n";
		//echo "$('#from_store_location').val('" . $location_name_arr[$row[csf("from_store")]][$row[csf("company_id")]]. "');\n";
		echo "load_room_rack_self_bin('requires/raw_material_transfer_entry_controller*101', 'floor','floor_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."',this.value);\n";
		echo "document.getElementById('cbo_floor').value 				= '".$row[csf("floor_id")]."';\n";
		echo "load_room_rack_self_bin('requires/raw_material_transfer_entry_controller*101', 'room','room_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."',this.value);\n";
		echo "document.getElementById('cbo_room').value 				= '".$row[csf("room")]."';\n";
		echo "load_room_rack_self_bin('requires/raw_material_transfer_entry_controller*101', 'rack','rack_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		echo "document.getElementById('txt_rack').value 				= '".$row[csf("rack")]."';\n";
		echo "load_room_rack_self_bin('requires/raw_material_transfer_entry_controller*101', 'shelf','shelf_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";
		echo "document.getElementById('txt_shelf').value 				= '".$row[csf("shelf")]."';\n";
		echo "load_room_rack_self_bin('requires/raw_material_transfer_entry_controller', 'bin','bin_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."','".$row[csf('shelf')]."',this.value);\n";
		echo "document.getElementById('cbo_bin').value 				= '".$row[csf("bin_box")]."';\n";
		echo "disable_enable_fields('cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*',1);\n";


		echo "load_room_rack_self_bin('requires/raw_material_transfer_entry_controller*101*cbo_store_name_to', 'store','to_store_td', '".$company_id."','"."',this.value);\n";
		echo "document.getElementById('cbo_store_name_to').value 				= '".$row[csf("to_store")]."';\n";
		//echo "$('#to_store_location').val('" . $location_name_arr[$row[csf("to_store")]][$row[csf("company_id")]]. "');\n";

		echo "load_room_rack_self_bin('requires/raw_material_transfer_entry_controller*101*cbo_floor_to', 'floor','floor_td_to', '".$company_id."','"."','".$row[csf('to_store')]."',this.value);\n";
		echo "document.getElementById('cbo_floor_to').value 			= '".$row[csf("to_floor_id")]."';\n";
		echo "load_room_rack_self_bin('requires/raw_material_transfer_entry_controller*101*cbo_room_to', 'room','room_td_to', '".$company_id."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."',this.value);\n";
		echo "document.getElementById('cbo_room_to').value 				= '".$row[csf("to_room")]."';\n";
		echo "load_room_rack_self_bin('requires/raw_material_transfer_entry_controller*101*txt_rack_to', 'rack','rack_td_to', '".$company_id."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."',this.value);\n";
		echo "document.getElementById('txt_rack_to').value 				= '".$row[csf("to_rack")]."';\n";
		echo "load_room_rack_self_bin('requires/raw_material_transfer_entry_controller*101*txt_shelf_to', 'shelf','shelf_td_to', '".$company_id."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."','".$row[csf('to_rack')]."',this.value);\n";
		echo "document.getElementById('txt_shelf_to').value 			= '".$row[csf("to_shelf")]."';\n";

		echo "load_room_rack_self_bin('requires/raw_material_transfer_entry_controller*101*cbo_bin_to', 'bin','bin_td_to', '".$company_id."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."','".$row[csf('to_rack')]."','".$row[csf('to_shelf')]."',this.value);\n";
		echo "document.getElementById('cbo_bin_to').value 				= '".$row[csf("to_bin_box")]."';\n";
 
		$form_store_location=return_field_value("store_location","lib_store_location","id='".$row[csf("from_store")]."'","store_location");
		//$to_store_location=return_field_value("store_location","lib_store_location","id='".$row[csf("to_store")]."'","store_location");
		//echo "document.getElementById('from_store_location').value 			= '".$form_store_location."';\n";
		//echo "document.getElementById('to_store_location').value 			= '".$to_store_location."';\n";
		
		echo "document.getElementById('txt_transfer_qnty').value 			= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('txt_rate').value 					= '".number_format($rate,4,'.','')."';\n";
		
		echo "document.getElementById('txt_transfer_value').value 			= '".number_format(($row[csf("transfer_value")]/$coversion_rate),4,".","")."';\n";
		echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";
		//echo "document.getElementById('txt_yarn_brand').value 				= '".$brand_arr[$row[csf("brand_id")]]."';\n";
		echo "document.getElementById('hidden_transfer_qnty').value 		= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('hidden_product_id').value 			= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('previous_from_prod_id').value 		= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('previous_to_prod_id').value 			= '".$row[csf("to_prod_id")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("uom")]."';\n";
		
		echo "document.getElementById('txt_style_no').value 				= '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('txt_order_no').value 				= '".$row[csf("po_number")]."';\n";
		echo "document.getElementById('job_id').value 						= '".$row[csf("job_id")]."';\n";
		echo "$('#cbo_company_id').attr('disabled',true);\n";
		echo "$('#cbo_store_name').attr('disabled',true);\n";
		echo "$('#cbo_item_category').attr('disabled',true);\n";
		echo "$('#cbo_location').attr('disabled',true);\n";
		echo "$('#cbo_store_name_to').attr('disabled',true);\n";
		echo "storeUpdateUptoDisable();\n";

		/*$sql=sql_select("select product_name_details,current_stock,avg_rate_per_unit from product_details_master where id=".$row[csf('from_prod_id')]);
		
		$stock=$sql[0][csf("current_stock")]+$row[csf("transfer_qnty")];
		
		echo "document.getElementById('txt_item_desc').value 				= '".$sql[0][csf("product_name_details")]."';\n";
		echo "document.getElementById('txt_current_stock').value 			= '".$sql[0][csf("current_stock")]."';\n";
		echo "document.getElementById('hidden_current_stock').value 		= '".$stock."';\n";*/
		
		$product_id=$row[csf("from_prod_id")];
		/*$sql_balance=sql_select("select a.id, a.product_name_details, sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as job_balance
			from product_details_master a,  inv_transaction b 
			where a.id=b.prod_id and a.item_category_id in (101) and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$product_id group by a.id, a.product_name_details");*/

		$stock_sql=sql_select("select a.product_name_details, a.current_stock as item_global_stock, a.avg_rate_per_unit, sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as current_stock  from product_details_master a, inv_transaction b  where a.id=b.prod_id and a.id='".$row[csf('from_prod_id')]."' and b.store_id='".$row[csf("from_store")]."' and b.status_active=1 and b.is_deleted=0 group by a.product_name_details, a.current_stock, a.avg_rate_per_unit" );
		
		/*echo "select a.product_name_details, a.current_stock as item_global_stock, a.avg_rate_per_unit, sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as current_stock  from product_details_master a, inv_transaction b  where a.id=b.prod_id and a.id='".$row[csf('from_prod_id')]."' and b.store_id='".$row[csf("from_store")]."' and b.status_active=1 and b.is_deleted=0 group by a.product_name_details, a.current_stock, a.avg_rate_per_unit" ;*/



		if($row[csf("transfer_criteria")]==1)
		{
			echo "document.getElementById('cbo_currency').value 			= 1;\n";
		}
		else{
			echo "document.getElementById('cbo_currency').value 			= 2;\n";
			//$conversion_factor=return_field_value("conversion_factor as data","lib_item_group","id='".$sql_balance[0][csf("item_group_id")]."'","data");
			$conversion_factor=return_field_value("a.conversion_factor as data","lib_item_group a, product_details_master b","a.id=b.item_group_id and b.entry_form=334 and b.id=$product_id","data"); 
			//$stock=($stock_sql[0][csf("current_stock")]*$conversion_factor)+$row[csf("transfer_qnty")];
			$stock=($stock_sql[0][csf("current_stock")])+$row[csf("transfer_qnty")];
			//echo '=='.$stock_sql[0][csf("current_stock")].'=='.$conversion_factor.'==';
			//$stock=$sql_balance[0][csf("job_balance")]*$conversion_factor;
			//$stock=$stock+$row[csf("transfer_qnty")];
		}
		
		/*if($row[csf("transfer_criteria")]==1)
		{
			$sql_balance=sql_select("select a.id, a.product_name_details, sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as job_balance
			from product_details_master a,  inv_transaction b 
			where a.id=b.prod_id and a.item_category_id in (101) and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$product_id group by a.id, a.product_name_details");
			$stock=$sql_balance[0][csf("job_balance")]+$row[csf("transfer_qnty")];
			echo "document.getElementById('cbo_currency').value 			= 1;\n";
		}
		else
		{
			$job_id=$row[csf("job_id")];
		$order_no=$row[csf("po_number")];
		$store_id=$row[csf("from_store")];
		
	
		if($order_no!="")
		{
			$sql_balance=sql_select("select a.id, a.product_name_details, a.current_stock, a.item_group_id, d.id, sum((case when b.trans_type in(1,4,5) then quantity else 0 end)-(case when b.trans_type in(2,3,6) then quantity else 0 end)) as job_balance
			from product_details_master a, inv_transaction e, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d  
			where a.id=e.prod_id and e.id=b.trans_id and a.id=b.prod_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.item_category_id in (4) and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$product_id and c.po_number='$order_no' and d.id=$job_id and e.store_id=$store_id
			group by a.id, a.product_name_details, a.current_stock, a.item_group_id, d.id");
			}
			else
			{
				$sql_balance=sql_select("select a.id, a.product_name_details, a.current_stock, a.item_group_id, d.id, sum((case when b.trans_type in(1,4,5) then quantity else 0 end)-(case when b.trans_type in(2,3,6) then quantity else 0 end)) as job_balance
			from product_details_master a, inv_transaction e, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d  
			where a.id=e.prod_id and e.id=b.trans_id and a.id=b.prod_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.item_category_id in (4) and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$product_id and d.id=$job_id and e.store_id=$store_id
			group by a.id, a.product_name_details, a.current_stock, a.item_group_id, d.id");
			}
			
			$conversion_factor=return_field_value("conversion_factor as data","lib_item_group","id='".$sql_balance[0][csf("item_group_id")]."'","data"); 
			$stock=$sql_balance[0][csf("job_balance")]*$conversion_factor;
			$stock=$stock+$row[csf("transfer_qnty")];
			echo "document.getElementById('cbo_currency').value 			= 2;\n";
		}
		*/
		echo "document.getElementById('txt_item_desc').value 				= '".$stock_sql[0][csf("product_name_details")]."';\n";
		echo "document.getElementById('txt_current_stock').value 			= '".number_format($stock,2,'.','')."';\n";
		echo "document.getElementById('hidden_current_stock').value 		= '".number_format($stock,2,'.','')."';\n";
		//echo "$('#txt_item_desc').attr('disabled',true);\n";
		$prod_id=$row[csf("from_prod_id")].",".$row[csf("to_prod_id")];
		$sql_trans=sql_select("select id, transaction_type from inv_transaction where mst_id=".$row[csf('mst_id')]." and item_category=".$row[csf("item_category")]." and transaction_type in(5,6) and prod_id in($prod_id) order by id asc");
        //echo "select id, transaction_type from inv_transaction where mst_id=$row[mst_id] and item_category=1 and transaction_type in(5,6) order by id asc";die;
		echo "document.getElementById('update_trans_issue_id').value 		= '".$sql_trans[0][csf("id")]."';\n";
		echo "document.getElementById('update_trans_recv_id').value 		= '".$sql_trans[1][csf("id")]."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n"; 
		exit();
	}
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$job_no_id=str_replace("'","",$job_id);
	$order_no=str_replace("'","",$txt_order_no);
	$product_id=str_replace("'","",$hidden_product_id);
	$store_id=str_replace("'","",$cbo_store_name);
	$trans_qnty=str_replace("'","",$txt_transfer_qnty);
	$cbo_transfer_criteria=str_replace("'","",$cbo_transfer_criteria);
	
	$update_trans_issue_id=str_replace("'","",$update_trans_issue_id);
	$update_trans_recv_id=str_replace("'","",$update_trans_recv_id); 
	
	$up_tr_cond="";
	if($update_trans_issue_id > 0 && $update_trans_recv_id > 0 )
	{
		$up_tr_cond=" and id not in($update_trans_issue_id,$update_trans_recv_id)";
		$trans_sql=sql_select("select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as bal
		from inv_transaction where status_active=1 and is_deleted=0 and prod_id=$hidden_product_id and store_id=$cbo_store_name_to $up_tr_cond");
		$stockQnty=$trans_sql[0][csf("bal")]*1;
		$trnsQnty=str_replace("'","",$txt_transfer_qnty);
		if($stockQnty < 0)
		{
			 echo "20**Transfer Quantity Not Allow Over Stock Quantity.";disconnect($con);die;
		}
	}
	
	$trans_sql=sql_select("select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as bal
	from inv_transaction where status_active=1 and is_deleted=0 and prod_id=$hidden_product_id and store_id=$cbo_store_name $up_tr_cond");
	$stockQnty=$trans_sql[0][csf("bal")]*1;
	$trnsQnty=str_replace("'","",$txt_transfer_qnty);
	if($trnsQnty > $stockQnty)
	{
		 echo "20**Transfer Quantity Not Allow Over Stock Quantity.";disconnect($con);die;
	}

	if(str_replace("'","",$update_id)!='' &&  $operation !=0)
	{
		
		$sql = sql_select("select id, prod_id, transaction_type, cons_quantity, cons_amount  from inv_transaction where status_active=1 and is_deleted=0 and mst_id=$update_id and transaction_type in(5,6)");
		$max_id=0;
		foreach( $sql as $row)
		{
			$prod_ids .= $row[csf("prod_id")].",";
			$trans_ids .= $row[csf("id")].","; 	
			if ($row[csf("id")] > $max_id)
			{
		        $max_id = $row[csf("id")];
		    }
		}
		
	    $prod_ids=chop($prod_ids,",");
		//echo "10**select id from inv_transaction where prod_id in ($prod_ids) and status_active=1 and is_deleted=0 and id >$max_id  "; die;
		$chk_next_transaction=return_field_value("id","inv_transaction"," prod_id in ($prod_ids) and status_active=1 and is_deleted=0 and id >$max_id ","id");
		//echo "10**".$chk_next_transaction; die;
		if($chk_next_transaction !="")
		{ 
			echo "17**Not allowed. This item is used in another transaction"; die;
		}
	}
	
	/*$sqlCon="";
	if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and e.floor_id=$cbo_floor" ;}
	if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and e.room=$cbo_room" ;}
	if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and e.rack=$txt_rack" ;}
	if(str_replace("'","",$txt_shelf)!=0){$sqlCon.= " and e.self=$txt_shelf" ;}
	if(str_replace("'","",$cbo_bin)!=0){$sqlCon.= " and e.bin_box=$cbo_bin" ;}*/

	$sqlCon="";	
	$store_update_upto=str_replace("'","",$store_update_upto);
	if($store_update_upto > 1)
	{
		if($store_update_upto==6)
		{
			if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and e.floor_id=$cbo_floor" ;}
			if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and e.room=$cbo_room" ;}
			if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and e.rack=$txt_rack" ;}
			if(str_replace("'","",$txt_shelf)!=0){$sqlCon.= " and e.self=$txt_shelf" ;}
			if(str_replace("'","",$cbo_bin)!=0){$sqlCon.= " and e.bin_box=$cbo_bin" ;}
		}
		else if($store_update_upto==5)
		{
			if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and e.floor_id=$cbo_floor" ;}
			if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and e.room=$cbo_room" ;}
			if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and e.rack=$txt_rack" ;}
			if(str_replace("'","",$txt_shelf)!=0){$sqlCon.= " and e.self=$txt_shelf" ;}
		}
		else if($store_update_upto==4)
		{
			if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and e.floor_id=$cbo_floor" ;}
			if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and e.room=$cbo_room" ;}
			if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and e.rack=$txt_rack" ;}
		}
		else if($store_update_upto==3)
		{
			if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and e.floor_id=$cbo_floor" ;}
			if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and e.room=$cbo_room" ;}
		}
		else if($store_update_upto==2)
		{
			if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and e.floor_id=$cbo_floor" ;}
		}
	}
	$conversion_factor=return_field_value("a.conversion_factor as data","lib_item_group a, product_details_master b","a.id=b.item_group_id and b.entry_form=334 and b.id=$product_id","data");
	/*if($cbo_transfer_criteria==2)
	{
		if(str_replace("'","",$update_id)>0) $mst_cond=" and e.mst_id<>$update_id";
		if($order_no!="")
		{
			$sql_bal="select sum((case when b.trans_type in(1,4,5) then b.quantity else 0 end)-(case when b.trans_type in(2,3,6) then b.quantity else 0 end)) as job_balance
			from product_details_master a, inv_transaction e, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d  
			where a.id=e.prod_id and e.id=b.trans_id and a.id=b.prod_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.item_category_id in (4) and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$product_id and c.id=$txt_order_id and e.store_id=$store_id $sqlCon $mst_cond";
		}
		else
		{
			$sql_bal="select sum((case when b.trans_type in(1,4,5) then b.quantity else 0 end)-(case when b.trans_type in(2,3,6) then b.quantity else 0 end)) as job_balance
			from product_details_master a,  inv_transaction e, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d  
			where a.id=e.prod_id and e.id=b.trans_id and a.id=b.prod_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.item_category_id in (4) and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$product_id and d.id=$job_no_id and e.store_id=$store_id $sqlCon $mst_cond";
		}
		$sql_balance=sql_select($sql_bal);
		$balance_qty=$sql_balance[0][csf("job_balance")]*$conversion_factor;
	}
	else
	{
		$sql_balance=sql_select("select sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as job_balance
		from product_details_master a,  inv_transaction b 
		where a.id=b.prod_id and a.item_category_id in (4) and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$product_id");
		$balance_qty=$sql_balance[0][csf("job_balance")];
	}*/
	$sql_balance=sql_select("select sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as job_balance
		from product_details_master a,  inv_transaction b 
		where a.id=b.prod_id and a.item_category_id in (101) and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$product_id");
	$balance_qty=$sql_balance[0][csf("job_balance")];
	
	//echo "10**$sql_bal";die;
	
	if($trans_qnty>$balance_qty)
	{
		echo "35**Trasfer Quantity Can not be Greater Than Current Stock.";
		die;
	}
	$coversion_sql=sql_select("select conversion_rate from currency_conversion_rate where currency=2 order by con_date desc");
	$coversion_rate=$coversion_sql[0][csf("conversion_rate")];
	

	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$transfer_recv_num=''; $transfer_update_id='';
		$variable_auto_rcv = return_field_value("auto_transfer_rcv", "variable_settings_inventory", " company_name=$cbo_company_id and item_category_id=101 and status_active=1 and variable_list= 27", "auto_transfer_rcv");
		//echo "10** select auto_transfer_rcv from variable_settings_inventory where  company_name=$cbo_company_id and item_category_id=101 and status_active=1 and variable_list= 27"; die;
		if($variable_auto_rcv == "")
		{
			$variable_auto_rcv = 1; // if auto receive yes(1), then no need to acknowledgement
		}
		//echo "10**".$variable_auto_rcv; die;
		
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			//$new_transfer_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'TTE', date("Y",time()), 5, "select transfer_prefix, transfer_prefix_number from inv_item_transfer_mst where company_id=$cbo_company_id and transfer_criteria in(1,2) and entry_form=487 and $year_cond=".date('Y',time())." order by id desc ", "transfer_prefix", "transfer_prefix_number" ));
			$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);
			$new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst",$con,1,$cbo_company_id,'RMTE',487,date("Y",time()) ));
			//$cbo_store_name  $cbo_store_name_to//
		 
			//$id=return_next_id( "id", "inv_item_transfer_mst", 1 ) ;
			if(str_replace("'","",$cbo_transfer_criteria)==1)
			{
				$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, transfer_criteria, to_company,entry_form, item_category ,location_id,to_location_id, from_store_id, to_store_id,  inserted_by, insert_date";
			
				$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$txt_transfer_date.",".$cbo_transfer_criteria.",".$cbo_company_id_to.",487,101,".$cbo_location.",".$cbo_location_to.",".$cbo_store_name.",".$cbo_store_name_to.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
			else if(str_replace("'","",$cbo_transfer_criteria)==2)
			{
				$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, to_company, challan_no, transfer_date, transfer_criteria,entry_form,item_category,location_id,to_location_id, from_store_id, to_store_id, inserted_by, insert_date";
			
				$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$cbo_company_id_to.",".$txt_challan_no.",".$txt_transfer_date.",".$cbo_transfer_criteria.",487,101,".$cbo_location.",".$cbo_location_to.",".$cbo_store_name.",".$cbo_store_name_to.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
			
			//echo "insert into inv_item_transfer_mst (".$field_array.") values ".$data_array;die;
			//$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
			//if($rID) $flag=1; else $flag=80;
			 
			$transfer_recv_num=$new_transfer_system_id[0];
			$transfer_update_id=$id;
		}
		else
		{
			$field_array_update="challan_no*transfer_date*to_company*to_location_id*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$cbo_company_id_to."*".$cbo_location_to."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			//$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			//if($rID) $flag=1; else $flag=0; 
			
			$transfer_recv_num=str_replace("'","",$txt_system_id);
			$transfer_update_id=str_replace("'","",$update_id);

			if($variable_auto_rcv != 1) // if auto receive yes(1), then no need to acknowledgement
			{
				//echo "10**fail=2";die;
				$pre_saved_store=sql_select("select a.id, b.from_store, b.to_store from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id = b.mst_id and a.entry_form = 487 and a.id = $transfer_update_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 group by a.id, b.from_store, b.to_store");

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
		$field_array_trans="id, mst_id,transaction_criteria, company_id, prod_id, item_category, transaction_type, transaction_date, store_id, floor_id, room, rack, self, bin_box, cons_uom, cons_quantity, cons_rate, cons_amount, balance_qnty, balance_amount, inserted_by, insert_date";
		
		//$id_dtls=return_next_id( "id", "inv_item_transfer_dtls", 1 ) ;
		$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
		$field_array_dtls="id, mst_id, from_prod_id, to_prod_id, item_group, from_store,floor_id,room,rack,shelf,bin_box, to_store,to_floor_id,to_room,to_rack,to_shelf,to_bin_box, item_category, transfer_qnty, rate, transfer_value, uom, job_id, po_number, style_ref_no, remarks, inserted_by, insert_date";

		$field_array_dtls_ac="id, mst_id, dtls_id, is_acknowledge, from_prod_id, to_prod_id, item_group, from_store, floor_id, room, rack, shelf, bin_box, to_store, to_floor_id, to_room, to_rack, to_shelf, to_bin_box, item_category, transfer_qnty, rate, transfer_value, uom, job_id, po_number, style_ref_no, remarks, inserted_by, insert_date";
		
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$data_prod=sql_select("select supplier_id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id=$hidden_product_id");
			
			$presentStock=$data_prod[0][csf('current_stock')]-str_replace("'","",$txt_transfer_qnty);
			$presentAvgRate=$data_prod[0][csf('avg_rate_per_unit')];
			$presentStockValue=$presentStock*$presentAvgRate;
			
			$field_array_prodUpdate="avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*updated_by*update_date";
			$data_array_prodUpdate=$presentAvgRate."*".$txt_transfer_qnty."*".$presentStock."*".$presentStockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			//$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$hidden_product_id,1);
			
			/*if($flag==1) 
			{
				if($prodUpdate) $flag=1; else $flag=0; 
			} */
			
			$supplier_id=$data_prod[0][csf('supplier_id')];
			$row_prod=sql_select("select id, current_stock, avg_rate_per_unit from product_details_master where company_id=$cbo_company_id_to and item_category_id=$cbo_item_category  and trim(product_name_details)=trim($txt_item_desc)  and status_active=1 and is_deleted=0");
		
			if(count($row_prod)>0)
			{
				$product_id=$row_prod[0][csf('id')];
				$stock_qnty=$row_prod[0][csf('current_stock')];
				$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];

				$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_transfer_qnty);
				$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
				if($variable_auto_rcv==1)
				{
					$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
					$data_array_prod_update=$avg_rate_per_unit."*".$txt_transfer_qnty."*".$curr_stock_qnty."*".$stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				}
				
				/*$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				}*/ 
			}
			else
			{
				//$product_id=return_next_id( "id", "product_details_master", 1 ) ;
				$product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				$curr_stock_qnty=str_replace("'","",$txt_transfer_qnty);
				$avg_rate_per_unit=$data_prod[0][csf('avg_rate_per_unit')];
				$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
				if($variable_auto_rcv==1)
				{
					$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date) 
					select	
					'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, trim(product_name_details), lot, item_code, unit_of_measure, avg_rate_per_unit, $txt_transfer_qnty, $curr_stock_qnty, $stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date from product_details_master where id=$hidden_product_id";
				}
				else
				{
					$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date) 
					select	
					'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, trim(product_name_details), lot, item_code, unit_of_measure, avg_rate_per_unit, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date from product_details_master where id=$hidden_product_id";
				}
				//echo $sql_prod_insert;die;
				/*$prod=execute_query($sql_prod_insert,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				} */
			}
			
	         //------------Check Transfer Out Date with last Receive Date-----------------
	        $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and store_id=$cbo_store_name and transaction_type in (1,4,5) and status_active=1 and is_deleted=0", "max_date");      
	        if($max_recv_date != "")
	        {
	            $max_recv_date = date("Y-m-d", strtotime($max_recv_date));
	            $transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
	            if ($transfer_date < $max_recv_date) 
	            {
	                echo "35**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
	                disconnect($con);die;
	            }
	        }

	        //------------Check Transfer In Date with last Transaction Date--------------------
	        $max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$product_id and store_id=$cbo_store_name_to and status_active=1 and is_deleted=0", "max_date");      
	        if($max_issue_date != "")
	        {
	            $max_issue_date = date("Y-m-d", strtotime($max_issue_date));
	            $transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
	            if ($transfer_date < $max_issue_date) 
	            {
	                echo "35**Transfer In Date Can not Be Less Than Last Transaction Date Of This Lot";
	               	disconnect($con); die;
	            }
	        }

			
			$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_transfer_criteria.",".$cbo_company_id.",".$hidden_product_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//$id_trans_rcv=$id_trans+1;
			$id_trans_rcv=0;
			if($variable_auto_rcv==1)
			{
				$id_trans_rcv=return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$data_array_trans.=",(".$id_trans_rcv.",".$transfer_update_id.",".$cbo_transfer_criteria.",".$cbo_company_id_to.",".$product_id.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$txt_transfer_qnty.",".$txt_transfer_value.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
			
			$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$hidden_product_id.",".$product_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$job_id.",".$txt_order_no.",".$txt_style_no.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			
			if($variable_auto_rcv==2)
			{
				$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
				$data_array_dtls_ac="(".$id_dtls_ac.",".$transfer_update_id.",".$id_dtls.",0,".$hidden_product_id.",".$product_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$job_id.",".$txt_order_no.",".$txt_style_no.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
			
			/*$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_transfer_criteria.",".$cbo_company_id.",".$hidden_product_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$txt_transfer_qnty.",".$txt_transfer_value.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$recv_trans_id=$id_trans+1;
			
			$data_array_trans.=",(".$recv_trans_id.",".$transfer_update_id.",".$cbo_transfer_criteria.",".$cbo_company_id_to.",".$product_id.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$txt_transfer_qnty.",".$txt_transfer_value.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$hidden_product_id.",".$product_id.",0,".$cbo_store_name.",".$cbo_store_name_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";*/
			
			//LIFO/FIFO Start-----------------------------------------------//
			$isLIFOfifo=return_field_value("store_method","variable_settings_inventory","company_name=$cbo_company_id and variable_list=17 and item_category_id=$cbo_item_category and status_active=1 and is_deleted=0");
			if($isLIFOfifo==2) $cond_lifofifo=" DESC"; else $cond_lifofifo=" ASC";
			
			$transfer_qnty = str_replace("'","",$txt_transfer_qnty);
			$transfer_value = str_replace("'","",$txt_transfer_value);
			$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
			$update_array = "balance_qnty*balance_amount*updated_by*update_date";
			$updateID_array=array();
			$update_data=array();
			//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);
			
			$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_product_id and balance_qnty>0 and transaction_type in (1,4,5) and item_category=$cbo_item_category order by transaction_date $cond_lifofifo");
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
					$data_array_mrr .= "(".$mrrWiseIsID.",".$id_trans_rcv.",".$id_trans.",487,".$hidden_product_id.",".$transfer_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
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
					$data_array_mrr .= "(".$mrrWiseIsID.",".$id_trans_rcv.",".$id_trans.",487,".$hidden_product_id.",".$balance_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					$updateID_array[]=$recv_trans_id; 
					$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					$transfer_qnty = $transferQntyBalance;
				}
				//$mrrWiseIsID++;
			}//end foreach
			
			/*if($data_array_mrr!="")
			{		
				$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
				if($flag==1) 
				{
					if($mrrWiseIssueID) $flag=1; else $flag=0; 
				} 
			}
			
			//transaction table stock update here------------------------//
			if(count($updateID_array)>0)
			{
				$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
				if($flag==1) 
				{
					if($upTrID) $flag=1; else $flag=0; 
				} 
			}	*/
			// LIFO/FIFO END-----------------------------------------------//
		}
		else
		{
			$txt_rate=str_replace("'","",$txt_rate)*$coversion_rate;
			$txt_transfer_value=str_replace("'","",$txt_transfer_value)*$coversion_rate;
			if($txt_rate=="") $txt_rate=0;
			if($txt_transfer_value=="" ) $txt_transfer_value=0;
			
			 //------------Check Transfer Out Date with last Receive Date-----------------
	        $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and store_id=$cbo_store_name and transaction_type in (1,4,5) and status_active=1 and is_deleted=0", "max_date");      
	        if($max_recv_date != "")
	        {
	            $max_recv_date = date("Y-m-d", strtotime($max_recv_date));
	            $transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
	            if ($transfer_date < $max_recv_date) 
	            {
	                echo "35**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
	                disconnect($con);die;
	            }
	        }

	        //------------Check Transfer In Date with last Transaction Date--------------------
	        $max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and store_id=$cbo_store_name_to and status_active=1 and is_deleted=0", "max_date");      
	        if($max_issue_date != "")
	        {
	            $max_issue_date = date("Y-m-d", strtotime($max_issue_date));
	            $transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
	            if ($transfer_date < $max_issue_date) 
	            {
	                echo "35**Transfer In Date Can not Be Less Than Last Transaction Date Of This Lot";
	                disconnect($con);die;
	            }
	        }

			$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_transfer_criteria.",".$cbo_company_id.",".$hidden_product_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//$id_trans_rcv=$id_trans+1;
			$id_trans_rcv=0;
			if($variable_auto_rcv==1)
			{
				$id_trans_rcv=return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$data_array_trans.=",(".$id_trans_rcv.",".$transfer_update_id.",".$cbo_transfer_criteria.",".$cbo_company_id.",".$hidden_product_id.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$txt_transfer_qnty.",".$txt_transfer_value.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
			
			$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$hidden_product_id.",".$hidden_product_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$job_id.",".$txt_order_no.",".$txt_style_no.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			if($variable_auto_rcv==2) // Heare ack dtls
			{
				$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
				$data_array_dtls_ac="(".$id_dtls_ac.",".$transfer_update_id.",".$id_dtls.",0,".$hidden_product_id.",".$hidden_product_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$job_id.",".$txt_order_no.",".$txt_style_no.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
			
			//$id_prop=return_next_id( "id", "order_wise_pro_details", 1 ) ;
			/*$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$field_array_propotion="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, order_rate, order_amount, inserted_by, insert_date";
			$txt_style_no=str_replace("'","",$txt_style_no);
			$txt_order_no=str_replace("'","",$txt_order_no);
			$job_id=str_replace("'","",$job_id);
			$tot_trans_qnty=str_replace("'","",$txt_transfer_qnty)/$conversion_factor;
			if($txt_order_no!="")
			{
				$sql="select b.prod_id, b.po_breakdown_id, sum((case when b.trans_type in(1,4,5) then quantity else 0 end)-(case when b.trans_type in(2,3,6) then quantity else 0 end)) as order_balance
				from order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d  
				where b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and b.status_active=1 and b.is_deleted=0 and b.prod_id=$hidden_product_id and c.po_number='$txt_order_no' and d.id=$job_id
				group by b.prod_id, b.po_breakdown_id
				having sum((case when b.trans_type in(1,4,5) then quantity else 0 end)-(case when b.trans_type in(2,3,6) then quantity else 0 end))>0";
			}
			else
			{
				$sql="select b.prod_id, b.po_breakdown_id, sum((case when b.trans_type in(1,4,5) then quantity else 0 end)-(case when b.trans_type in(2,3,6) then quantity else 0 end)) as order_balance
				from order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d  
				where b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and b.status_active=1 and b.is_deleted=0 and b.prod_id=$hidden_product_id and d.id=$job_id
				group by b.prod_id, b.po_breakdown_id
				having sum((case when b.trans_type in(1,4,5) then quantity else 0 end)-(case when b.trans_type in(2,3,6) then quantity else 0 end))>0";
			}
			
			$prod_result=sql_select($sql);
			$tot_order_qnty=$total_order=0;
			foreach($prod_result as $row)
			{
				$tot_order_qnty+=$row[csf("order_balance")];
				$total_order++;
			}
			*/
			//$prod_order_data=array();
			$perc=$trims_qnty=$len=$totalTrims=0;$data_array_prop="";
			foreach($prod_result as $row)
			{
				$len=$len+1;
				$perc=($row[csf("order_balance")]/$tot_order_qnty)*100;
				$trims_qnty=($perc*$tot_trans_qnty)/100;
				$totalTrims+=$trims_qnty;
				if($total_order==$len)
				{
					$balance = $tot_trans_qnty-$totalTrims;
					if($balance!=0) $trims_qnty=$trims_qnty+($balance);	
				}
				
				$row_prod_order=sql_select( "select id, stock_quantity, avg_rate, stock_amount from order_wise_stock where prod_id=".$row[csf("prod_id")]." and po_breakdown_id=".$row[csf("po_breakdown_id")]." and status_active=1 and is_deleted=0" );
				$avg_rate=$row_prod_order[0][csf("avg_rate")];
				$order_amount=$trims_qnty*$avg_rate;
				
				if($data_array_prop!="") $data_array_prop.=",";
				$data_array_prop.="(".$id_prop.",".$id_trans.",6,487,".$id_dtls.",".$row[csf("po_breakdown_id")].",".$row[csf("prod_id")].",".$trims_qnty.",'".$avg_rate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				//$id_prop = $id_prop+1;
				if($variable_auto_rcv==1) // Heare ack dtls
				{
					$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					if($data_array_prop!="") $data_array_prop.=",";
					$data_array_prop.="(".$id_prop.",".$id_trans_rcv.",5,487,".$id_dtls.",".$row[csf("po_breakdown_id")].",".$row[csf("prod_id")].",".$trims_qnty.",'".$avg_rate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}
				//$id_prop = $id_prop+1;
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				
			}
		}
		//echo "10**$data_array_prop";die;
		
		//echo "insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		/*$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		
		//echo "insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} */
		
		
		//insert update all queries
		$rID=$rID2=$rID3=$prodUpdate=$prod=$mrrWiseIssueID=$upTrID=$rID4=$rID5=true;
		//echo "10**insert into inv_transaction ($field_array_trans) values $data_array_trans";die;
		//echo "10**insert into inv_item_transfer_mst ($field_array) values $data_array";die;

		if(str_replace("'","",$update_id)==""){
			$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
		} else {
			$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		}
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		
		/*if($data_array_prop!="") {
			$rID4=sql_insert("order_wise_pro_details",$field_array_propotion,$data_array_prop,0);
		}*/
		
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$hidden_product_id,1);
			if(count($row_prod)>0)
			{
				$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
			}
			else
			{
				$prod=execute_query($sql_prod_insert,0);
			}
			if($data_array_mrr!="")
			{		
				$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
			}
			if(count($updateID_array)>0)
			{
				$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
			}
		}
		//echo "10**".$variable_auto_rcv; die;
		if($variable_auto_rcv==2)
		{
			 //echo "10**insert into inv_item_transfer_dtls_ac (".$field_array_dtls_ac.") values ".$data_array_dtls_ac;die;
			$rID5=sql_insert("inv_item_transfer_dtls_ac",$field_array_dtls_ac,$data_array_dtls_ac,0);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			}
		}
		//echo "10** $rID && $rID2 && $rID3 && $rID4 && $rID5 && $prodUpdate && $prod && $mrrWiseIssueID && $upTrID ## $variable_auto_rcv";die;
		
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $prodUpdate && $prod && $mrrWiseIssueID && $upTrID)
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
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $prodUpdate && $prod && $mrrWiseIssueID && $upTrID)
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
		// $variable_auto_rcv = return_field_value("auto_transfer_rcv", "variable_settings_inventory", " company_name=$cbo_company_id and item_category_id=4 and status_active=1 and variable_list= 27", "auto_transfer_rcv");

		$variable_auto_rcv = return_field_value("auto_transfer_rcv", "variable_settings_inventory", " company_name=$cbo_company_id and item_category_id=101 and status_active=1 and variable_list= 27", "auto_transfer_rcv");

		if($variable_auto_rcv == "")
		{
			$variable_auto_rcv = 1; // if auto receive yes(1), then no need to acknowledgement
		}

		$acknowlage_check=sql_select("select is_acknowledge from inv_item_transfer_mst where id=$update_id and status_active=1");

		if($acknowlage_check[0][csf("is_acknowledge")] == 1)
		{
			echo "20**Acknowledgement done, so update and delete is not allowed";
			disconnect($con);die;
		}


		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$field_array_update="challan_no*transfer_date*to_company*to_location_id*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$cbo_company_id_to."*".$cbo_location_to."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		}
		else if(str_replace("'","",$cbo_transfer_criteria)==2)
		{
			$field_array_update="challan_no*transfer_date*to_location_id*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$cbo_location_to."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		}


		/*$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0; */
	
		$field_array_trans="prod_id*transaction_date*store_id*floor_id*room*rack*self*cons_uom*cons_quantity*cons_rate*cons_amount*balance_qnty*balance_amount*updated_by*update_date";
		$updateTransID_array=array();
		$update_trans_issue_id=str_replace("'","",$update_trans_issue_id); 
		$update_trans_recv_id=str_replace("'","",$update_trans_recv_id); 
		//echo "6**".$update_trans_issue_id; die;
		$field_array_dtls="from_prod_id*to_prod_id*from_store*floor_id*room*rack*shelf*to_store*to_floor_id*to_room*to_rack*to_shelf*transfer_qnty*rate*transfer_value*uom*job_id*po_number*style_ref_no*remarks*updated_by*update_date";
		
		if($variable_auto_rcv != 1) // if auto receive yes(1), then no need to acknowledgement
		{
			$pre_saved_store=sql_select("select a.id, b.from_store, b.to_store from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id = b.mst_id and a.entry_form = 487 and a.id = $update_id and b.id != $update_dtls_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 group by a.id, b.from_store, b.to_store");
			if(!empty($pre_saved_store))
			{
				if( ($pre_saved_store[0][csf("from_store")]  !=str_replace("'", "", $cbo_store_name)) || ($pre_saved_store[0][csf("to_store")]  !=str_replace("'", "", $cbo_store_name_to)) )
				{
					echo "17**Duplicate From Store and To Store is not allowed in same MRR";
					disconnect($con);
					die;
				}
			}
		}
		// I will start from heare for $variable_auto_rcv
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$updateProdID_array=array();
			$field_array_adjust="current_stock*avg_rate_per_unit*stock_value";
			
			$stock_from=sql_select("select current_stock, avg_rate_per_unit from product_details_master where id=$previous_from_prod_id");
			$adjust_curr_stock_from=$stock_from[0][csf('current_stock')]+str_replace("'", '',$hidden_transfer_qnty);
			$cur_st_rate_from=$stock_from[0][csf('avg_rate_per_unit')];
			$cur_st_value_from=$adjust_curr_stock_from*$cur_st_rate_from;
			
			$previous_from_prod_id=str_replace("'","",$previous_from_prod_id);
			$updateProdID_array[]=$previous_from_prod_id; 
			
			$data_array_adjust[$previous_from_prod_id]=explode("*",("".$adjust_curr_stock_from."*".$cur_st_rate_from."*".$cur_st_value_from));
			
			$stock_to=sql_select("select current_stock, avg_rate_per_unit from product_details_master where id=$previous_to_prod_id");
			$adjust_curr_stock_to=$stock_to[0][csf('current_stock')]-str_replace("'", '',$hidden_transfer_qnty);
			$cur_st_rate_to=$stock_to[0][csf('avg_rate_per_unit')];
			$cur_st_value_to=$adjust_curr_stock_to*$cur_st_rate_to;
			
			$previous_to_prod_id=str_replace("'","",$previous_to_prod_id);
			$updateProdID_array[]=$previous_to_prod_id; 
			
			$data_array_adjust[$previous_to_prod_id]=explode("*",("".$adjust_curr_stock_to."*".$cur_st_rate_to."*".$cur_st_value_to));
			
			/*$prodUpdate_adjust=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_adjust,$data_array_adjust,$updateProdID_array));
			if($flag==1) 
			{
				if($prodUpdate_adjust) $flag=1; else $flag=0; 
			}*/
			
			$data_prod=sql_select("select supplier_id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id=$hidden_product_id");
			
			$presentStock=$data_prod[0][csf('current_stock')]-str_replace("'","",$txt_transfer_qnty);
			$presentAvgRate=$data_prod[0][csf('avg_rate_per_unit')];
			$presentStockValue=$presentStock*$presentAvgRate;
			
			$field_array_prodUpdate="avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*updated_by*update_date";
			$data_array_prodUpdate=$presentAvgRate."*".$txt_transfer_qnty."*".$presentStock."*".$presentStockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			/*$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$hidden_product_id,1);
			if($flag==1) 
			{
				if($prodUpdate) $flag=1; else $flag=0; 
			} */
			
			$supplier_id=$data_prod[0][csf('supplier_id')];
			
			$row_prod=sql_select("select id, current_stock, avg_rate_per_unit from product_details_master where company_id=$cbo_company_id_to and item_category_id=$cbo_item_category  and product_name_details=trim($txt_item_desc ) and status_active=1 and is_deleted=0");
		
			if(count($row_prod)>0)
			{
				$product_id=$row_prod[0][csf('id')];
				$stock_qnty=$row_prod[0][csf('current_stock')];
				$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];
	
				$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_transfer_qnty);
				$stock_value=$curr_stock_qnty*$avg_rate_per_unit;	
				
				if($variable_auto_rcv == 1 )
				{
					$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
					$data_array_prod_update=$avg_rate_per_unit."*".$txt_transfer_qnty."*".$curr_stock_qnty."*".$stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				}
				/*$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				}*/
			}
			else
			{
				//$product_id=return_next_id( "id", "product_details_master", 1 ) ;
				$product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				$curr_stock_qnty=str_replace("'","",$txt_transfer_qnty);
				$avg_rate_per_unit=$data_prod[0][csf('avg_rate_per_unit')];
				$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
				
				if($variable_auto_rcv == 1 )
				{
					$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date) 
					select	
					'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, trim(product_name_details), lot, item_code, unit_of_measure, avg_rate_per_unit, $txt_transfer_qnty, $curr_stock_qnty, $stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date from product_details_master where id=$hidden_product_id";
				}
				else
				{
					$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date) 
					select	
					'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, trim(product_name_details), lot, item_code, unit_of_measure, avg_rate_per_unit, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date from product_details_master where id=$hidden_product_id";
				}
				
				
				/*$prod=execute_query($sql_prod_insert,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				}*/ 
			}
			
			 //------------Check Transfer Out Date with last Receive Date-----------------
            $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and store_id=$cbo_store_name and transaction_type in (1,4,5) and id <> $update_trans_recv_id and status_active=1 and is_deleted=0", "max_date");      
            if($max_recv_date != "")
            {
                $max_recv_date = date("Y-m-d", strtotime($max_recv_date));
                $transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
                if ($transfer_date < $max_recv_date) 
                {
                    echo "35**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
                    disconnect($con);die;
                }
            }

            //------------Check Transfer In Date with last Transaction Date--------------------
            $max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$product_id and store_id=$cbo_store_name_to and id not in ($update_trans_issue_id,$update_trans_recv_id) and status_active=1 and is_deleted=0", "max_date");      
            if($max_issue_date != "")
            {
                $max_issue_date = date("Y-m-d", strtotime($max_issue_date));
                $transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
                if ($transfer_date < $max_issue_date) 
                {
                    echo "35**Transfer In Date Can not Be Less Than Last Transaction Date Of This Lot";
                    disconnect($con);die;
                }
            }

			$updateTransID_array[]=$update_trans_issue_id; 
			$updateTransID_data[$update_trans_issue_id]=explode("*",("".$hidden_product_id."*".$txt_transfer_date."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*'0'*'0'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			if($variable_auto_rcv == 1 )
			{
				$updateTransID_array[]=$update_trans_recv_id; 
				$updateTransID_data[$update_trans_recv_id]=explode("*",("'".$product_id."'*".$txt_transfer_date."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*".$txt_transfer_qnty."*".$txt_transfer_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}				
			
			//$field_array_dtls="from_prod_id*to_prod_id*from_store*to_store*transfer_qnty*rate*transfer_value*uom*job_id*po_number*style_ref_no*updated_by*update_date";
			$data_array_dtls=$hidden_product_id."*".$product_id."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*".$cbo_uom."*".$job_id."*".$txt_order_no."*".$txt_style_no."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			//transaction table START--------------------------//
			$update_array = "balance_qnty*balance_amount*updated_by*update_date";
			$sql = sql_select("select a.id,a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_trans_issue_id and b.entry_form=487 and a.item_category=$cbo_item_category "); 
			$updateID_array = array();
			$update_data = array();
			foreach($sql as $result)
			{
				$adjBalance = $result[csf("balance_qnty")]+$result[csf("issue_qnty")];
				$adjAmount = $result[csf("balance_amount")]+$result[csf("amount")];
				$updateID_array[]=$result[csf("id")]; 
				$update_data[$result[csf("id")]]=explode("*",("".$adjBalance."*".$adjAmount."*'".$user_id."'*'".$pc_date_time."'"));
			}

			/*if(count($updateID_array)>0)
			{
				$query=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
				if($flag==1) 
				{
					if($query) $flag=1; else $flag=0; 
				} 
				
				$query2 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_trans_issue_id and entry_form=487 ");
				{
					if($query2) $flag=1; else $flag=0; 
				} 
			}*/
			//transaction table END----------------------------//
			
			//LIFO/FIFO Start-----------------------------------------------//
			$isLIFOfifo=return_field_value("store_method","variable_settings_inventory","company_name=$cbo_company_id and variable_list=17");
			if($isLIFOfifo==2) $cond_lifofifo=" DESC"; else $cond_lifofifo=" ASC";
			
			$transfer_qnty = str_replace("'","",$txt_transfer_qnty);
			$transfer_value = str_replace("'","",$txt_transfer_value);
			$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
			$update_array = "balance_qnty*balance_amount*updated_by*update_date";
			$updateID_array=array();
			$update_data=array();
			//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);
			
			
			$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_product_id and balance_qnty>0 and transaction_type in (1,4,5) and item_category=cbo_item_category order by transaction_date $cond_lifofifo");
			foreach($sql as $result)
			{				
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				$recv_trans_id = $result[csf("id")]; // this row will be updated
				$balance_qnty = $result[csf("balance_qnty")];
				$balance_amount = $result[csf("balance_amount")];
				$cons_rate = $result[csf("cons_rate")];
				$transferQntyBalance = $balance_qnty-$transfer_qnty; // minus issue qnty
				$transferStockBalance = $balance_amount-($transfer_qnty*$cons_rate);
				//echo "6**".$transferQntyBalance; die;
				if($transferQntyBalance>=0)
				{					
					$amount = $transfer_qnty*$cons_rate;
					//for insert
					if($recv_trans_id!='')
					{
						if($data_array_mrr!="") $data_array_mrr .= ",";  
						$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$update_trans_issue_id.",487,".$hidden_product_id.",".$transfer_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
						//for update
						$updateID_array[]=$recv_trans_id; 
						$update_data[$recv_trans_id]=explode("*",("".$transferQntyBalance."*".$transferStockBalance."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
						break;
					}
					
				}
				else if($transferQntyBalance<0)
				{
					$transferQntyBalance = $transfer_qnty-$balance_qnty;				
					$transfer_qnty = $balance_qnty;				
					$amount = $transfer_qnty*$cons_rate;
					if($recv_trans_id!='')
					{
					//for insert
						if($data_array_mrr!="") $data_array_mrr .= ",";  
						$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$update_trans_issue_id.",487,".$hidden_product_id.",".$balance_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
						//for update
						$updateID_array[]=$recv_trans_id; 
						$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
						$transfer_qnty = $transferQntyBalance;
					}
				}
				//$mrrWiseIsID++;
			}//end foreach
			
			/*if($data_array_mrr!="")
			{		
				$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
				if($flag==1) 
				{
					if($mrrWiseIssueID) $flag=1; else $flag=0; 
				} 
				
			}
			//transaction table stock update here------------------------//
			if(count($updateID_array)>0)
			{
				$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
				if($flag==1) 
				{
					if($upTrID) $flag=1; else $flag=0; 
				} 
			}	*/
 			// LIFO/FIFO END-----------------------------------------------//
		}
		else
		{
			$txt_rate=str_replace("'","",$txt_rate)*$coversion_rate;
			$txt_transfer_value=str_replace("'","",$txt_transfer_value)*$coversion_rate;
			if($txt_rate=="") $txt_rate=0;
			if($txt_transfer_value=="" ) $txt_transfer_value=0;
			
			$balance_qnty=str_replace("'","",$txt_transfer_qnty);
			$balance_amt=str_replace("'","",$txt_transfer_value);
			if($balance_qnty=="") $balance_qnty=0;
			if($balance_amt=="") $balance_amt=0;

			if($update_trans_issue_id!='')
			{
				$updateTransID_array[]=$update_trans_issue_id;
				$updateTransID_data[$update_trans_issue_id]=explode("*",("".$hidden_product_id."*".$txt_transfer_date."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*0*0*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			
			if($variable_auto_rcv == 1 && $update_trans_recv_id!='')
			{
				$updateTransID_array[]=$update_trans_recv_id;
				$updateTransID_data[$update_trans_recv_id]=explode("*",("".$hidden_product_id."*".$txt_transfer_date."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*".$balance_qnty."*".$balance_amt."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}			
			
			$data_array_dtls=$hidden_product_id."*".$hidden_product_id."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*".$cbo_uom."*".$job_id."*".$txt_order_no."*".$txt_style_no."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

			//$id_prop=return_next_id( "id", "order_wise_pro_details", 1 ) ;
		
			$field_array_propotion="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, order_rate, order_amount, inserted_by, insert_date";
			$txt_style_no=str_replace("'","",$txt_style_no);
			$txt_order_no=str_replace("'","",$txt_order_no);
			$job_id=str_replace("'","",$job_id);
			$tot_trans_qnty=str_replace("'","",$txt_transfer_qnty)/$conversion_factor;
			
			/*if($txt_order_no!="")
			{
				$sql="SELECT b.prod_id, b.po_breakdown_id, sum((case when b.trans_type in(1,4,5) then quantity else 0 end)-(case when b.trans_type in(2,3,6) then quantity else 0 end)) as order_balance
				from order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d  
				where b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and b.status_active=1 and b.is_deleted=0 and b.prod_id=$hidden_product_id and c.po_number='$txt_order_no' and d.id=$job_id
				group by b.prod_id, b.po_breakdown_id
				having sum((case when b.trans_type in(1,4,5) then quantity else 0 end)-(case when b.trans_type in(2,3,6) then quantity else 0 end))>0";
			}
			else
			{
				$sql="SELECT b.prod_id, b.po_breakdown_id, sum((case when b.trans_type in(1,4,5) then quantity else 0 end)-(case when b.trans_type in(2,3,6) then quantity else 0 end)) as order_balance
				from order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d  
				where b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and b.status_active=1 and b.is_deleted=0 and b.prod_id=$hidden_product_id and d.id=$job_id
				group by b.prod_id, b.po_breakdown_id
				having sum((case when b.trans_type in(1,4,5) then quantity else 0 end)-(case when b.trans_type in(2,3,6) then quantity else 0 end))>0";
			}
			
			
			
			$prod_result=sql_select($sql);
			$tot_order_qnty=$total_order=0;
			foreach($prod_result as $row)
			{
				$tot_order_qnty+=$row[csf("order_balance")];
				$total_order++;
			}
			
			//$prod_order_data=array();
			$perc=$trims_qnty=$len=$totalTrims=0;$data_array_prop="";
			foreach($prod_result as $row)
			{
				
				$len=$len+1;
				$perc=($row[csf("order_balance")]/$tot_order_qnty)*100;
				$trims_qnty=($perc*$tot_trans_qnty)/100;
				$totalTrims+=$trims_qnty;
				if($total_order==$len)
				{
					$balance = $tot_trans_qnty-$totalTrims;
					if($balance!=0) $trims_qnty=$trims_qnty+($balance);	
				}
				
				$row_prod_order=sql_select( "select id, stock_quantity, avg_rate, stock_amount from order_wise_stock where prod_id=".$row[csf("prod_id")]." and po_breakdown_id=".$row[csf("po_breakdown_id")]." and status_active=1 and is_deleted=0" );
				$avg_rate=$row_prod_order[0][csf("avg_rate")];
				$order_amount=$trims_qnty*$avg_rate;

				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				if($data_array_prop!="") $data_array_prop.=",";
				$data_array_prop.="(".$id_prop.",".$update_trans_issue_id.",6,487,".$update_dtls_id.",".$row[csf("po_breakdown_id")].",".$row[csf("prod_id")].",".$trims_qnty.",'".$avg_rate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				//$id_prop = $id_prop+1;
				if($variable_auto_rcv == 1 )
				{
					$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					if($data_array_prop!="") $data_array_prop.=",";
					$data_array_prop.="(".$id_prop.",".$update_trans_recv_id.",5,487,".$update_dtls_id.",".$row[csf("po_breakdown_id")].",".$row[csf("prod_id")].",".$trims_qnty.",'".$avg_rate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}				
				//$id_prop = $id_prop+1;
				
			}*/
		}
		
		$rID=$rID2=$rID3=$prodUpdate_adjust=$prodUpdate=$prod=$query=$query2=$mrrWiseIssueID=$upTrID=$rID4=$query3=$rID5=true;
		
		//all update and insert operation  
		$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,0);
		
		//echo "6**".$field_array_update."##".$data_array_update."##".$update_id;die;
		//echo "6**".bulk_update_sql_statement("inv_transaction","id",$field_array_trans,$updateTransID_data,$updateTransID_array); die;
		
		$rID2=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans,$updateTransID_data,$updateTransID_array));
		
		$rID3=sql_update("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,"id",$update_dtls_id,0);
		
		
		if($data_array_prop!="")
		{
			if($update_trans_issue_id=="") $update_trans_issue_id=0;
			if($update_trans_recv_id=="") $update_trans_recv_id=0;
			$query3 = execute_query("DELETE FROM order_wise_pro_details WHERE trans_id in($update_trans_issue_id,$update_trans_recv_id) and entry_form=487 ");
			$rID4=sql_insert("order_wise_pro_details",$field_array_propotion,$data_array_prop,0);
		}

		// echo "10**".$field_array_dtls."**".$data_array_dtls."**".$update_dtls_id;oci_rollback($con);disconnect($con); die;
        // echo "10**".$variable_auto_rcv;oci_rollback($con);disconnect($con); die;

		if($variable_auto_rcv==2)
		{

			$rID5=sql_update("inv_item_transfer_dtls_ac",$field_array_dtls,$data_array_dtls,"dtls_id",$update_dtls_id,1);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			}
		}
		
		//echo "6**".$rID3;die;
		
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$prodUpdate_adjust=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_adjust,$data_array_adjust,$updateProdID_array));
			$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$hidden_product_id,1);
			if(count($row_prod)>0)
			{
				$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
			}
			else
			{
				$prod=execute_query($sql_prod_insert,0);
			}
			if(count($updateID_array)>0)
			{
				$query=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
				$query2 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_trans_issue_id and entry_form=487 ");
			}
			
			if($data_array_mrr!="")
			{		
				$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
			}
			//transaction table stock update here------------------------//
			if(count($updateID_array)>0)
			{
				$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
			}	
		}

		//echo "6**$rID && $rID2 && $rID3 && $prodUpdate_adjust && $prodUpdate && $prod && $query && $query2 && $mrrWiseIssueID && $upTrID && $rID5 ## $variable_auto_rcv";die;
		
		
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $prodUpdate_adjust && $prodUpdate && $prod && $query && $query2 && $mrrWiseIssueID && $upTrID && $query3 && $rID4 && $rID5)
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
			
			if($rID && $rID2 && $rID3 && $prodUpdate_adjust && $prodUpdate && $prod && $query && $query2 && $mrrWiseIssueID && $upTrID && $query3 && $rID4 && $rID5)
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
	else if ($operation==2)   // Delete Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$update_id=str_replace("'","",$update_id);
		$previous_prod_id=str_replace("'","",$hidden_product_id);
		$update_dtls_id=str_replace("'","",$update_dtls_id);
		$update_trans_issue_id=str_replace("'","",$update_trans_issue_id);
		$update_trans_recv_id=str_replace("'","",$update_trans_recv_id);
		$all_trans_id=$update_trans_issue_id.",".$update_trans_recv_id;
		$cbo_store_name_to=$update_trans_issue_id.",".$cbo_store_name_to;
		//echo "10**$update_id=$previous_prod_id=$update_dtls_id=$update_trans_issue_id=$update_trans_recv_id";die;
		if($update_id>0 && $previous_prod_id>0 && $update_dtls_id>0 && $update_trans_issue_id>0 && $update_trans_recv_id>0)
		{
			
			/*if($db_type==0) $row_count_cond=" limit 1"; else $row_count_cond=" and rownum<2";
			$next_operation_check=sql_select("select id as next_id, mst_id as mst_id, transaction_type as transaction_type from inv_transaction where id > $update_trans_recv_id and prod_id=$previous_prod_id and status_active=1 $row_count_cond");
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
			
			$store_stock=sql_select("select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as store_stock_qnty from inv_transaction where prod_id=$previous_prod_id and store_id=$cbo_store_name_to and status_active=1");
			$store_stock_qnty=$store_stock[0][csf("store_stock_qnty")];
			
			if($store_stock_qnty <= 0)
			{
				echo "20**Store Wise Stock Less Then Zero, \n Please Delete Next Issue Or Receive Return, \n More Information Please See Item Ledger.";
				disconnect($con);die;
			}
			
			$field_arr="status_active*is_deleted*updated_by*update_date";
			$data_arr="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=$rID2=$rID3=true;
			$rID=execute_query("update inv_transaction set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id in($all_trans_id)");
			$rID2=sql_update("inv_item_transfer_dtls",$field_arr,$data_arr,"id",$update_dtls_id,1);
			$rID3=execute_query("update order_wise_pro_details set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where trans_id in($all_trans_id)");
			
			
			//echo "10** $rID && $rID2 && $rID3";oci_rollback($con);disconnect($con);die;
			if($db_type==0)
			{
				if($rID && $rID2 && $rID3)
				{
					mysql_query("COMMIT");  
					echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**0";
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "7**0**0**1";
				}
			}
	
			if($db_type==2 || $db_type==1 )
			{
				if($rID && $rID2 && $rID3)
				{
					oci_commit($con);  
					echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**0";
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

if($action=="trims_transfer_print")
{
	//echo load_html_head_contents("Trims Transfer Info", "../../", 1, 1,'','','');
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$sql="select id, transfer_system_id, challan_no, transfer_date, transfer_criteria, item_category, to_company, from_order_id, to_order_id,from_store_id, to_store_id ,location_id , to_location_id ,item_category, inserted_by from inv_item_transfer_mst a where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$inserted_by=$dataArray[0][csf('inserted_by')];

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	$item_group_arr = return_library_array("select id, item_name from lib_item_group","id","item_name");
	$prod_sql=sql_select("select id, product_name_details, item_group_id from product_details_master where item_category_id in (101) ");
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$sql = "select id,location_id,company_id from lib_store_location where id=$store_id and company_id=$from_company and status_active=1 and is_deleted=0";
	$product_arr=array();
	foreach($prod_sql as $row)
	{
		$product_arr[$row[csf("id")]]["product_name_details"]=$row[csf("product_name_details")];
		$product_arr[$row[csf("id")]]["item_group_id"]=$row[csf("item_group_id")];
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
						$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website, vat_number from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
					foreach ($nameArray as $result)
					{ 
						?>
						<? echo $result[csf('plot_no')]; ?>,
						Level No: <? echo $result[csf('level_no')]?>,
						<? echo $result[csf('road_no')]; ?>, 
						<? echo $result[csf('block_no')];?>, 
						<? echo $result[csf('city')];?>, 
						<? echo $result[csf('zip_code')]; ?>, 
						<?php echo $result[csf('province')];?>, 
						<? echo $country_arr[$result[csf('country_id')]]; ?><br> 
						Email Address: <? echo $result[csf('email')];?> 
						Website No: <? echo $result[csf('website')];?> <br>
						<!-- <b> Vat No : <? //echo $result[csf('vat_number')]; ?></b> --> <?
							//$party_id= $result[csf('party_id')];
					}
	                ?> 
	            </td>  
	        </tr>
	        <tr>
	            <td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo " Raw Material Transfer Challan"; //$data[2] ?> </u></strong></td>
	        </tr>
	        <tr><td colspan="6" align="center">&nbsp;</td></tr>
	        <tr>
	            <td width="130"><strong>Transfer Criteria</strong></td> <td width="175px">:<? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?></td>
	            <td width="130"><strong>Challan No.</strong></td><td width="175px">:<? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
	            <td width="125"><strong>Transfer Date</strong></td><td width="175px">:<? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
	        </tr>
	        <tr>
	            <td><strong>From Store</strong></td> <td width="175px">:<? echo $store_library[$dataArray[0][csf('from_store_id')]]; ?></td>
	            <td><strong>From Location</strong></td> <td width="175px">:<? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
	            <td ><strong>Item Category</strong></td><td width="175px">:<? echo $item_category[101]; ?></td>
	        </tr>
	        <tr>
	            <td><strong>To Store</strong></td> <td width="175px">:<? echo $store_library[$dataArray[0][csf('to_store_id')]]; ?></td>
	            <td><strong>To Location</strong></td> <td width="175px">:<? echo $location_arr[$dataArray[0][csf('to_location_id')]]; ?></td>
	            <td><strong>Man. Challan No.</strong></td><td width="175px">:<? echo $dataArray[0][csf('challan_no')]; ?></td>
	        </tr>
	    </table>
	    <table align="right" cellspacing="0" width="900"  border="0" rules="all" >
	    	<tr><td>&nbsp;</td></tr>
	    </table>
	    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" style="table-layout: fixed;" >
	        <thead>
	        	
	            <!-- SL	Prod. ID	Item Group	Section	Item Description			UOM	Qty.		Remarks -->

	            <tr bgcolor="#D1D1D1">
	                <td width="40"  align="center" style="font-size:13px; font-weight:bold;">SL</td>
	                <td width="150"  align="center" style="font-size:13px; font-weight:bold;">Prod. ID</td>
	                <td width="120"  align="center" style="font-size:13px; font-weight:bold;">Item Group</td>
	                <td width="120"  align="center" style="font-size:13px; font-weight:bold;">Section</td> 
	                <td width="200"  align="center" style="font-size:13px; font-weight:bold;">Item Description</td>
	                <td width="50"  align="center" style="font-size:13px; font-weight:bold;"> UOM</td>
	                <td width="80" align="center" style="font-size:13px; font-weight:bold;">Transfered Qnty</td>
	                <td  align="center" style="font-size:13px; font-weight:bold;">Remarks</td>
	            </tr>
	        </thead>
			<?
	        $sql_dtls="SELECT a.id,b.id as prod_id, a.from_store, a.to_store, a.from_prod_id, a.transfer_qnty,b.item_group_id,b.section_id,b.gmts_size,b.unit_of_measure,b.item_category_id,b.product_name_details, a.remarks  from inv_item_transfer_dtls a, product_details_master b where a.mst_id='$data[1]' and a.from_prod_id=b.id and a.status_active = '1' and a.is_deleted = '0'";
			//echo $sql_dtls;
	        $sql_result= sql_select($sql_dtls);
			
			$dtls_data=array();
			foreach($sql_result as $row)
			{
				//$all_job_id.=$row[csf("job_id")].",";
				//if($row[csf("po_number")]!="") $all_po_number.=$row[csf("po_number")].",";
				$dtls_data[$row[csf("id")]]["id"]=$row[csf("id")];
				$dtls_data[$row[csf("id")]]["from_store"]=$row[csf("from_store")];
				$dtls_data[$row[csf("id")]]["to_store"]=$row[csf("to_store")];
				$dtls_data[$row[csf("id")]]["from_prod_id"]=$row[csf("from_prod_id")];
				$dtls_data[$row[csf("id")]]["transfer_qnty"]=$row[csf("transfer_qnty")];
				$dtls_data[$row[csf("id")]]["item_category"]=$row[csf("item_category")];
				$dtls_data[$row[csf("id")]]["uom"]=$row[csf("unit_of_measure")];
				$dtls_data[$row[csf("id")]]["item_group_id"]=$row[csf("item_group_id")];
				$dtls_data[$row[csf("id")]]["section_id"]=$row[csf("section_id")];
				$dtls_data[$row[csf("id")]]["prod_id"]=$row[csf("prod_id")];
				$dtls_data[$row[csf("id")]]["remarks"]=$row[csf("remarks")];
				$dtls_data[$row[csf("id")]]["product_name_details"]=$row[csf("product_name_details")];
			}
	        $i=1;
	        foreach($dtls_data as $id=>$row)
	        {
				 if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td><? echo $i; ?></td>
					<td><? echo $row[("prod_id")]; ?></td>
					<td><? echo $item_group_arr[$row[("item_group_id")]];  ?></td>
					<td><? echo $trims_section[$row[("section_id")]]; ?></td>
					<td><? echo $row[("product_name_details")] ;//$product_arr[$row[("from_prod_id")]]["product_name_details"]; ?></td>
					<td align="center"><? echo $unit_of_measurement[$row[("uom")]]; ?></td>
					<td align="right"><? echo $row[("transfer_qnty")];  ?></td>
					<td align=""><? echo $row[("remarks")];  ?></td>
				</tr>
				<? 
				$i++;
				$job_total+= $row[('transfer_qnty')];
				$transfer_qnty_sum += $row[('transfer_qnty')];
			} 
			?>
	        <tr bgcolor="#CCCCCC">
	            <td colspan="6" align="right" style="font-size:14px; font-weight:bold;">Total :</td>
	            <td align="right"><?php echo $transfer_qnty_sum; ?></td>
	            <td></td>
	        </tr>                           
	  </table>
	    <br>
	     <?
	       //echo signature_table(105, $data[0], "900px");
	     echo signature_table(105, $data[0], "900px",'',70,$user_lib_name_arr[$inserted_by]);
	     ?>
	   </div>   
	 <?
	 exit();	
}

//populate_data_location
if ($action == "populate_data_location_from") {
	$data = explode("**", $data);
	$store_id = $data[0];
	$from_company = $data[1];
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$sql = "select location_id from lib_store_location where id=$store_id and company_id=$from_company and status_active=1 and is_deleted=0";
	$result = sql_select($sql);
	foreach ($result as $row) {
		//echo "$('#from_store_location').val('" . $location_arr[$row[csf("location_id")]] . "');\n";
	}
	exit();
}
//populate_data_location
if ($action == "populate_data_location_to") {
	$data = explode("**", $data);
	$store_id = $data[0];
	$from_company = $data[1];
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$sql = "select location_id from lib_store_location where id=$store_id and company_id=$from_company and status_active=1 and is_deleted=0";
	$result = sql_select($sql);
	foreach ($result as $row) {
		//echo "$('#to_store_location').val('" . $location_arr[$row[csf("location_id")]] . "');\n";
	}
	exit();
}
?>
