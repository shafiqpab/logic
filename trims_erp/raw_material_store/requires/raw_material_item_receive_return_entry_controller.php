<? 
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id,store_location_id,item_cate_id,supplier_id FROM user_passwd where id='$user_id'");
$company_id = $userCredential[0][csf('company_id')];
$supplier_id = $userCredential[0][csf('supplier_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];

if ($company_id !='') {
    $company_credential_cond = "and comp.id in($company_id)";
}
if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)"; 
}
if($item_cate_id !='') {
    $item_cate_credential_cond = $item_cate_id ;  
}
else
{
	 $item_cate_credential_cond="".implode(",",array_flip($item_category))."";
}
if ($supplier_id !='') {
    $supplier_credential_cond = "and a.id in($supplier_id)";
}

//========== user credential end ==========


$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if($db_type==2 || $db_type==1 )
{
	$mrr_date_check="and to_char(insert_date,'YYYY')=".date('Y',time())."";
	$group_concat="wm_concat";
}
else if ($db_type==0)
{
	$mrr_date_check="and year(insert_date)=".date('Y',time())."";
	$group_concat="group_concat";
}
if ($action=="load_room_rack_self_bin")
	{
		load_room_rack_self_bin("requires/raw_material_item_receive_return_entry_controller",$data);
	}
/*if ($action=="load_drop_down_store")
{	
	echo create_drop_down( "cbo_store_name", 130, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type in (8,9,10,11,15,16,17,18,19,20,21,22) and a.status_active=1 and a.is_deleted=0 and FIND_IN_SET($data,a.company_id) group by a.id order by a.store_name","id,store_name", 1, "-- Select --", "", "","" );  	 
	exit();
}*/

if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_supplier", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(1,6,7,8,90) and c.tag_company in($data)  $supplier_credential_cond and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );	 

	exit();
}

?>
<?
if($action=="mrr_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST); 
	//echo "$company"; 
?>
     
<script>
	function js_set_value(mrr)
	{
 		$("#hidden_recv_number").val(mrr); // mrr number
		parent.emailwindow.hide();
	}
</script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>                	 
                    <th width="170">Supplier</th>
                     <th width="170">Item Category</th>
                    <th width="180">Date Range</th>
                    <th width="130">MRR No</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center">
                        <?  
 							echo create_drop_down( "cbo_supplier", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and  b.party_type in(1,6,7,8) $supplier_credential_cond and a.status_active=1 and a.is_deleted=0  group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
                        ?>
                    </td>
                    <td  align="center">
                     <? //create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index ) 
					 echo create_drop_down( "cbo_item_category", 170, $item_category,"", 1, "-- Select --", 0, "", 0,"101","","","1,2,3,5,6,7,12,13,14" );
                     
					 ?>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date"  type="text" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" type="text" />
                    </td> 
                    <td align="center">
                        <input name="txt_mrr_no" id="txt_mrr_no" class="text_boxes" style="width:100px" type="text"/>
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier').value+'_'+document.getElementById('cbo_item_category').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('txt_mrr_no').value, 'create_mrr_search_list_view', 'search_div', 'raw_material_item_receive_return_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="6">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here-------->
                     <input type="hidden" id="hidden_recv_number" value="" />
                    <!-- ---------END------------->
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

if($action=="create_mrr_search_list_view")
{
	$ex_data = explode("_",$data);
	//print_r($ex_data);
	$supplier = $ex_data[0];
	$txt_item_category1 = $ex_data[1];
	//$txt_search_by = $ex_data[2];
	$fromDate = $ex_data[2];
	$toDate = $ex_data[3];
	//echo $ex_data[5];
	$company = $ex_data[4];
	$mrr_no = $ex_data[5];
	//print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));
	
	if ($fromDate!="" && $toDate!="") $sql_cond .= " and a.receive_date between '".date("j-M-Y",strtotime($fromDate))."' and '".date("j-M-Y",strtotime($toDate))."'"; else $sql_cond ="";
	//$sql_cond="";
	if(($company)!="") $sql_cond .= " and a.company_id='$company'"; 
	if(($supplier)!=0) $suplier_cond = " and a.supplier_id='$supplier' "; else $suplier_cond ='';
	if(($txt_item_category1)!=0) $item_category_cond= " and b.item_category='$txt_item_category1' "; else $item_category_cond="";
	if(str_replace("'","",$mrr_no)!="") $mrr_cond="and a.recv_number like ('%$mrr_no%')"; else  $mrr_cond=""; 
	
	//echo "select a.recv_number,a.supplier_id,a.item_category,a.challan_no,c.lc_number,a.receive_date,a.receive_basis,sum(b.cons_quantity) as receive_qnty from inv_receive_master a,inv_transaction b,com_btb_lc_master_details c where a.lc_no=c.id and a.item_category='$txt_item_category' and a.id=b.mst_id  and a.status_active=1 and a.company_id='$company' "; 
	
	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id='$user_id'");
	$cre_company_id = $userCredential[0][csf('company_id')];
	$cre_supplier_id = $userCredential[0][csf('supplier_id')];
	$cre_store_location_id = $userCredential[0][csf('store_location_id')];
	$cre_item_cate_id = $userCredential[0][csf('item_cate_id')];
	
	$credientian_cond="";
	if($cre_company_id!="") $credientian_cond=" and a.company_id in($cre_company_id)";
	if($cre_supplier_id!="") $credientian_cond.=" and a.supplier_id in($cre_supplier_id)";
	if($cre_store_location_id!="") $credientian_cond.=" and b.store_id in($cre_store_location_id)";
	//if($cre_item_cate_id!="") $credientian_cond.=" and b.item_category in($cre_item_cate_id)";
	
	 $sql = "select a.recv_number_prefix_num, a.recv_number, a.supplier_id, b.item_category, a.challan_no, c.lc_number, a.receive_date, a.receive_basis, sum(b.cons_quantity) as receive_qnty 
	from inv_transaction b, inv_receive_master a left join com_btb_lc_master_details c on a.lc_no=c.id 
	where b.item_category in(101) and a.id=b.mst_id and  a.status_active=1 and a.entry_form in(263,266) and b.balance_qnty>0 $sql_cond $suplier_cond $item_category_cond  $mrr_cond $credientian_cond 
	group by b.mst_id,a.recv_number , a.recv_number_prefix_num  ,a.supplier_id ,a.challan_no ,a.receive_date,a.receive_basis,b.item_category, c.lc_number ";
	$supplier_arr = return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	//echo $sql;
	$arr=array(1=>$item_category,2=>$supplier_arr,6=>$receive_basis_arr);
	echo  create_list_view("list_view", "MRR No, Item Category, Supplier Name, Challan No, LC No, Receive Date, Receive Basis, Receive Qnty","60,100,120,100,100,100,100,120","830","250",0, $sql , "js_set_value", "recv_number","", 1, "0,item_category,supplier_id,0,0,0,receive_basis,0", $arr, "recv_number_prefix_num,item_category,supplier_id,challan_no,lc_number,receive_date,receive_basis,receive_qnty", "",'','0,0,0,0,0,0,1,1') ;
	//echo  create_list_view("list_view", "Requisition No,Requisition Date,Company,Item Category,Location,Department,Section,status", "80,80,100,100,100,90,90,80","780","350",0, $sql , "js_set_value", "id", "",1,"0,0,company_id,item_category_id,location_id,department_id,section_id,status_active", $arr , "requ_prefix_num,requisition_date,company_id,item_category_id,location_id,department_id,section_id,status_active","purchase_requisition_controller","",'0,3,0,0,0,0,0,0') ;	
	exit();
}

if($action=="serial_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST); 
	//echo $serialStringID."jahid"; die;
	//echo $current_prod_id; die;

 	$serialStringID = str_replace("'","",$serialStringID);
 	//$serialStringNo = str_replace("'","",$serialStringNo);
	$txt_received_id = str_replace("'","",$txt_received_id);
	$current_prod_id = str_replace("'","",$current_prod_id);
	
 	?>
	<script>
	var selected_id = new Array();
	var selected_no = new Array();	
	
	 
	var serialNoArr="<? echo $serialStringID; ?>";
 	var chk_selected_no = new Array();
	var chk_selected_id = new Array();
	if(serialNoArr!=""){chk_selected_no=serialNoArr.split(",");}
	
	 
	
	function check_all_data() 
	{
		var tbl_row_count = document.getElementById( 'hidden_all_id' ).value.split(","); 
 		//tbl_row_count = tbl_row_count-1;
		for( var i = 0; i < tbl_row_count.length; i++ ) {
 			if( jQuery.inArray( $('#txt_serial_id' + tbl_row_count[i]).val(), chk_selected_id ) != -1 )
			js_set_value( tbl_row_count[i] );
		}
	}
	
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
				//x.style.backgroundColor = ( $serialStringID != "")? newColor : origColor;
			}
		} 
		
	function js_set_value( str ) {
		toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
		
		if( jQuery.inArray( $('#txt_serial_id' + str).val(), selected_id ) == -1 ) {
			selected_id.push( $('#txt_serial_id' + str).val() );
			selected_no.push( $('#txt_serial_no' + str).val() );
 		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == $('#txt_serial_id' + str).val() ) break;
			}
			selected_id.splice( i, 1 );
			selected_no.splice( i, 1 );
		}
		var id = '';	var no = '';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			no += selected_no[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		no = no.substr( 0, no.length - 1 );
  		$('#txt_string_id').val( id );
		$('#txt_string_no').val( no );
	}
	 
	function fn_onClosed()
	{
		var txt_string = $('#txt_string').val();
		if(txt_string==""){ alert("Please Select The Serial"); return;}
		parent.emailwindow.hide();
	}
	 
	</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	<form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
    	<table width="500" cellspacing="0" cellpadding="0" border="0" class="rpt_table" id="tbl_header" >
				<thead>
					<tr>                	 
						<th width="300">Serial No</th>
                        <th width="">Warranty Date</th>
 					</tr>
				</thead>
        </table>        
        <div style="width:500px; overflow-y:scroll; min-height:220px">
		<table width="480" cellspacing="0" cellpadding="0" border="0" class="rpt_table" id="tbl_serial" style="overflow:scroll; min-height:300px" >
 				<tbody>
                	<?
						if(trim(str_replace("'","",$serialStringID))=="")  $serialStringID=0; else $serialStringID=str_replace("'","",$serialStringID);
						$i=1;
						$sql="select a.id,a.recv_trans_id,a.issue_trans_id,a.prod_id,a.serial_no,b.expire_date,a.is_issued from inv_serial_no_details a, inv_transaction b where a.prod_id=$current_prod_id and a.recv_trans_id=b.id and a.prod_id=b.prod_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.mst_id=$txt_received_id and (is_issued=0 or a.id in($serialStringID))";
						//echo $sql;
						$result = sql_select($sql);
						$count=count($result );
						foreach($result as $row) 
						{
							if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if($new_data=="") $new_data=$row[csf("id")]; else $new_data .=",".$row[csf("id")];				
						?>	
							<tr bgcolor="<? echo $bgcolor; ?>"  onClick="js_set_value(<? echo $row[csf("id")]; ?>)" id="search<? echo $row[csf("id")]; ?>" style="cursor:pointer">
								<td  width="300">
									<? echo trim($row[csf("serial_no")]); ?> 
									<input type="hidden" id="txt_serial_id<? echo $row[csf("id")]; ?>" value="<? echo $row[csf("id")]; ?>" >
                                    <input type="hidden" id="txt_serial_no<? echo $row[csf("id")]; ?>" value="<? echo $row[csf("serial_no")]; ?>" >
								</td>
								<td  width="">
									<? echo change_date_format($row[csf("expire_date")]); 
									
									if($count==$i)
									{
									?> 
                                    <input type="hidden" id="hidden_all_id" value="<? echo $new_data; ?>" >
                                    <? } ?>
								</td>
							</tr> 
					<? 
						
							$i++;
						}

				?>
				</tbody>         
			</table>  
            </div>
            <div><input type="button" name="btn_close" class="formbutton" style="width:100px" value="Close" onClick="fn_onClosed()" /></div>  
            <!-- Hidden field here-------->
			<input type="hidden" id="txt_string_id" value="" />
            <input type="hidden" id="txt_string_no" value="" />				 
			<!-- ---------END-------------> 
			</form>
	   </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    
    <script>
		if( serialNoArr!="" )
		{
			serialNoArr=serialNoArr.split(",");
			for(var k=0;k<serialNoArr.length; k++)
			{
				js_set_value(serialNoArr[k] );
				//alert(serialNoArr[k]);
			}
		}
	</script>
	</html>
	<?
}
?>
<?


if($action=="populate_data_from_data")
{
	$sql = "select a.id, a.recv_number, a.company_id, a.supplier_id, a.exchange_rate
			from inv_receive_master a 
			where a.recv_number='$data' and a.entry_form in(263,266) ";
	//echo $sql;
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#txt_received_id').val('".$row[csf("id")]."');\n";
		echo "$('#txt_mrr_no').val('".$row[csf("recv_number")]."');\n";
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
 		echo "$('#cbo_return_to').val(".$row[csf("supplier_id")].");\n";
		//right side list view
		echo"show_list_view('".$row[csf("id")]."','show_product_listview','list_product_container','requires/raw_material_item_receive_return_entry_controller','');\n";
   	}	
	exit();	
}
?>
<?
//right side product list create here--------------------//
if($action=="show_product_listview")
{ 
	
	 $sql = "select b.store_id,b.item_category,b.transaction_type,c.item_group_id,c.item_description,b.cons_quantity,b.cons_rate,b.mst_id as mrr_id,b.id as tr_id, b.balance_qnty, c.id as prod_id
	from  inv_transaction b, product_details_master c 
	where b.prod_id=c.id and b.mst_id='$data' and b.transaction_type in(1,4) and b.balance_qnty>0 and b.item_category  in (101)";
	//echo $sql;
  	$result = sql_select($sql);
	//print_r( $result) ;
	$store_name=return_library_array("select id,store_name from lib_store_location", "id","store_name"); 
	$item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
	$i=1; 
 	?>
    	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" rules="all">
        <caption>Display Received Items </caption>
        	<thead>
                <tr>
                	<th>SL</th>
                    <th>Item Cetagory</th>
                    <th>Group Name</th>
                    <th>Description</th>
                    <th>Recv. Qty</th>
                    <th>Balance Qty.</th>
                </tr>
            </thead>
            <tbody>
            	<?
				
				foreach($result as $row)
				{ 
					if ($i%2==0)$bgcolor="#E9F3FF";						
					else $bgcolor="#FFFFFF";  
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("tr_id")];?>","item_details_form_input","requires/raw_material_item_receive_return_entry_controller")' style="cursor:pointer" >
                        <td><? echo $i; ?></td>
                        
                        <!--<td><? //echo $item_name_arr[$row[csf("store_id")]]; txt_prod_id ?></td>-->
                        <td><? echo $item_category[$row[csf("item_category")]]; ?></td>
                        <td><? echo $item_name_arr[$row[csf("item_group_id")]]; ?></td>
                        <td><? echo $row[csf("item_description")]; ?></td>
                        <td><? echo $row[csf("cons_quantity")]; ?></td>
                        <td><? echo $row[csf("balance_qnty")]; ?></td>
					</tr>
					<?
					$i++; 
				 } 
				 ?>
            </tbody>
        </table>
     </fieldset>   
	<?	 
	exit();
}
//child form data input here-----------------------------//
if($action=="item_details_form_input")
{
	 $sql = "select b.id as prod_id,b.item_group_id,b.item_description,b.current_stock,a.company_id,a.id,a.item_category,a.balance_qnty,a.cons_quantity,a.cons_rate,a.cons_uom,a.store_id,a.floor_id,a.room,a.rack,a.self,a.bin_box,a.order_rate,a.order_qnty, a.order_amount
			from inv_transaction a,product_details_master b
 			where a.id=$data and a.status_active=1 and a.prod_id=b.id and b.status_active=1";
 	//echo $sql;die; 
	//txt_return_rate
	$store_name=return_library_array("select id,store_name from lib_store_location", "id","store_name"); 
	$item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
	$result = sql_select($sql);
	foreach($result as $row)
	{

		echo "load_room_rack_self_bin('requires/raw_material_item_receive_return_entry_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94_101', 'store','store_td', '".$row[csf('company_id')]."','"."',this.value);\n";
		echo "$('#cbo_store_name').val('".$row[csf("store_id")]."');\n";
		echo "load_room_rack_self_bin('requires/raw_material_item_receive_return_entry_controller', 'floor','floor_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."',this.value);\n";
		echo "$('#cbo_floor').val('".$row[csf("floor_id")]."');\n";
		echo "load_room_rack_self_bin('requires/raw_material_item_receive_return_entry_controller', 'room','room_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."',this.value);\n";
		echo "$('#cbo_room').val('".$row[csf("room")]."');\n";
		echo "load_room_rack_self_bin('requires/raw_material_item_receive_return_entry_controller', 'rack','rack_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		echo "$('#txt_rack').val('".$row[csf("rack")]."');\n";
		echo "load_room_rack_self_bin('requires/raw_material_item_receive_return_entry_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";	
		echo "$('#txt_shelf').val('".$row[csf("self")]."');\n";
		echo "load_room_rack_self_bin('requires/raw_material_item_receive_return_entry_controller', 'bin','bin_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."','".$row[csf('self')]."',this.value);\n";	
		echo "$('#cbo_bin').val('".$row[csf("bin_box")]."');\n";

		echo "$('#category').val('".$row[csf("item_category")]."');\n";
 		echo "$('#txt_item_category').val('".$item_category[$row[csf("item_category")]]."');\n";
		echo "$('#txt_item_group').val('".$item_name_arr[$row[csf("item_group_id")]]."');\n";
		echo "$('#txt_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#check_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#store').val('".$row[csf("store_id")]."');\n";
		//echo "$('#store').val('id');\n";
		//echo "$('#category_store_uom').val('".$row[csf("id")]."');\n";
		echo "$('#txt_item_description').val('".$row[csf("item_description")]."');\n";
		echo "$('#transaction_id').val('".$row[csf("id")]."');\n";
		//echo "$('#txt_return_qnty').val('".$row[csf("cons_quantity")]."');\n";
		//echo "$('#txt_item_description').val('".$row[csf("cons_rate")]."');\n";
		//echo "$('#txt_return_qnty').val('');\n";
		echo "$('#txt_return_rate').val('".$row[csf("cons_rate")]."');\n";
		echo "$('#txt_mrr_stock').val('".$row[csf("balance_qnty")]."');\n";
		
		$cumilitive_rtn=return_field_value("sum(b.issue_qnty) as issue_qnty","inv_mrr_wise_issue_details b","b.status_active=1 and b.prod_id='".$row[csf("prod_id")]."' and b.recv_trans_id='".$row[csf("id")]."'","issue_qnty" );
		$yet_to_issue=$row[csf("cons_quantity")]-$cumilitive_rtn;
		
		echo "$('#txt_cons_quantity').val('".$row[csf("cons_quantity")]."');\n";
		echo "$('#hidden_receive_trans_id').val('".$row[csf("id")]."');\n";
		//echo "$('#txt_mrr_stock').val('".$row[csf("cons_quantity")]."');\n";
		echo "$('#txt_uom').val('".$unit_of_measurement[$row[csf("cons_uom")]]."');\n";
		echo "$('#uom').val('".$row[csf("cons_uom")]."');\n";
		echo "$('#txt_cumulative_issued').val('$cumilitive_rtn');\n";
		echo "$('#txt_yet_to_issue').val('$yet_to_issue');\n";
		echo "$('#txt_global_stock').val('".$row[csf("current_stock")]."');\n";
		//echo "set_button_status(0, permission, 'fnc_general_receive_return_entry',1,1);\n";
		//echo "$('#txt_return_qnty').val('');\n";
		echo "$('#cbo_store_name').attr('disabled','disabled');\n";
		echo "$('#cbo_floor').attr('disabled','disabled');\n";
		echo "$('#cbo_room').attr('disabled','disabled');\n";
		echo "$('#txt_rack').attr('disabled','disabled');\n";
		echo "$('#txt_shelf').attr('disabled','disabled');\n";
		echo "$('#cbo_bin').attr('disabled','disabled');\n";

	}

	exit();		
}
?>
<?
//data save update delete here------------------------------//
if($action=="save_update_delete")
{	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));	

	$is_update_cond = ($operation == 1 ) ? " and id <> $update_id" : "" ;

    $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$txt_prod_id and transaction_type in (1,4,5) and store_id = $store $is_update_cond and status_active = 1", "max_date");    
    if($max_recv_date != "")
    {
    	$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
		$issue_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_return_date)));
		if ($issue_date < $max_recv_date) 
	    {
            echo "20**Issue Date Can not Be Less Than Last Receive Date Of This Lot";
            die;
		}
    }  
	
        
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0**0"; die;}
 		
		//---------------Check Duplicate product in Same return number ------------------------//
		$duplicate = is_duplicate_field("b.id","inv_issue_master a, inv_transaction b","a.id=b.mst_id and a.id=$txt_mrr_retrun_id and b.prod_id=$txt_prod_id and b.transaction_type=3"); 
		if($duplicate==1) 
		{
			echo "20**Duplication is Not Allowed in Same Return Number And Same Product.";
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}
		$txt_return_qnty=str_replace("'","",$txt_return_qnty);
		$txt_global_stock=return_field_value("current_stock as current_stock","product_details_master","status_active=1 and is_deleted=0 and id=$txt_prod_id","current_stock");
		if($txt_return_qnty>$txt_global_stock)
		{
			echo "30**Return Quantity Not Over Global Stock.";
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}
		
		$txt_mrr_stock=return_field_value("balance_qnty as balance_qnty"," inv_transaction","status_active=1 and is_deleted=0 and id=$hidden_receive_trans_id","balance_qnty");
		if($txt_return_qnty>$txt_mrr_stock)
		{
			echo "30**Return Quantity Not Over MRR Stock.";
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}

		//------------------------------Check Brand END---------------------------------------//
		
 		if(str_replace("'","",$txt_mrr_retrun_no)!="")
		{
			$new_return_number[0] = str_replace("'","",$txt_mrr_retrun_no);
			$id=str_replace("'","",$txt_mrr_retrun_id);
			//General master table UPDATE here START----------------------//		
 			$field_array_receive="entry_form*company_id*supplier_id*issue_date*challan_no*received_id*received_mrr_no*updated_by*update_date*remarks";
			
			$data_array_receive="264*".$cbo_company_id."*".$cbo_return_to."*".$txt_return_date."*".$txt_challan_no."*".$txt_received_id."*".$txt_mrr_no."*'".$user_id."'*'".$pc_date_time."'*".$txt_remark."";
			
		}
		else  	
		{	 
			//General master table entry here START---------------------------------------txt_challan_no//		
			//$id=return_next_id("id", "inv_issue_master", 1);		
			//$new_return_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'GRR', date("Y",time()), 5, "select issue_number_prefix,issue_number_prefix_num from inv_issue_master where company_id=$cbo_company_id and entry_form=264 $mrr_date_check order by id DESC ", "issue_number_prefix", "issue_number_prefix_num" ));
			
			$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
			$new_return_number = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,str_replace("'","",$cbo_company_id),'RMRR',264,date("Y",time())));
			
 			$field_array_receive="id, issue_number_prefix, issue_number_prefix_num, issue_number, entry_form, company_id, supplier_id, issue_date,challan_no, received_id, received_mrr_no, inserted_by, insert_date,remarks";
			$data_array_receive="(".$id.",'".$new_return_number[1]."','".$new_return_number[2]."','".$new_return_number[0]."',264,".$cbo_company_id.",".$cbo_return_to.",".$txt_return_date.",".$txt_challan_no.",".$txt_received_id.",".$txt_mrr_no.",'".$user_id."','".$pc_date_time."',".$txt_remark.")";
		}
		
		
		//adjust product master table START-------------------------------------//
 		$txt_return_qnty = str_replace("'","",$txt_return_qnty);
		$txt_return_value = str_replace("'","",$txt_return_value);
		$sql = sql_select("select item_group_id,item_description,avg_rate_per_unit,last_purchased_qnty,current_stock,stock_value from product_details_master where id=$txt_prod_id ");	//print_r($sql); die;
		$presentStock=$presentStockValue=$presentAvgRate=0;
		$item_description="";
		foreach($sql as $result)
		{
			$presentStock			=$result[csf("current_stock")];
			$presentStockValue		=$result[csf("stock_value")];
			$presentAvgRate			=$result[csf("avg_rate_per_unit")];
			$item_group_id 			=$result[csf("item_group_id")];
			$item_description		=$result[csf("item_description")];
		}
		$nowStock 		= $presentStock-$txt_return_qnty;
		$nowStockValue 	= $presentStockValue-$txt_return_value;
		$nowAvgRate		= number_format($nowStockValue/$nowStock,$dec_place[3],".","");	
		$field_array_product="last_issued_qnty*current_stock*stock_value*updated_by*update_date";
		$data_array_product="".$txt_return_qnty."*".$nowStock."*".$nowStockValue."*'".$user_id."'*'".$pc_date_time."'";
		
	 	 //transaction table insert here START--------------------------------//
		$txt_issue_qnty = str_replace("'","",$txt_return_qnty);
		$txt_rate = str_replace("'","",$txt_return_rate);
 		$issue_stock_value = str_replace("'","",$txt_return_value);
		//$transactionID = return_next_id("id", "inv_transaction", 1);
		$avg_rate_amount=str_replace("'","",$txt_issue_qnty)*$presentAvgRate;
		$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);  				
		$field_array_trans = "id,mst_id,company_id,supplier_id,prod_id,item_category,transaction_type,transaction_date,store_id,floor_id,room,rack,self,bin_box,cons_uom,cons_quantity,cons_rate,cons_amount,inserted_by,insert_date,rcv_rate,rcv_amount";
 		$data_array_trans = "(".$transactionID.",".$id.",".$cbo_company_id.",".$cbo_return_to.",".$txt_prod_id.",".$category.",3,".$txt_return_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$uom.",".$txt_issue_qnty.",'".$presentAvgRate."','".$avg_rate_amount."','".$user_id."','".$pc_date_time."',".$txt_rate.",".$issue_stock_value.")"; 

		//$transID = sql_insert("inv_transaction",$field_array_trans,$data_array_trans,1);
		
		
		
		
		
		$hidden_receive_trans_id=str_replace("'","",$hidden_receive_trans_id);
		//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);
		$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
		$field_array1 = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
		$data_array1 = "(".$mrrWiseIsID.",".$hidden_receive_trans_id.",".$transactionID.",264,".$txt_prod_id.",".$txt_return_qnty.",".$txt_return_rate.",".$txt_return_value.",'".$user_id."','".$pc_date_time."')";
		
		//echo "10**insert into ($field_array_receive) values $data_array_receive";die;
		
		
		$update_array = "balance_qnty*balance_amount*updated_by*update_date";
		$sql_receive=sql_select("select id,balance_qnty,balance_amount from inv_transaction where id=$hidden_receive_trans_id and balance_qnty>0");
		$balance_qnty = $sql_receive[0][csf("balance_qnty")];
		$balance_amount= $sql_receive[0][csf("balance_amount")];
		$issueQntyBalance = $balance_qnty-$txt_return_qnty; // minus issue qnty
		$issueStockBalance = $balance_amount-$txt_return_value;
		$update_data="'".$issueQntyBalance."'*'".$issueStockBalance."'*'".$user_id."'*'".$pc_date_time."'";
		
		
		
		/*$update_array = "balance_qnty*balance_amount*updated_by*update_date";
		$update_data="".$issueQntyBalance."*".$issueStockBalance."*'".$user_id."'*'".$pc_date_time."'";
		
		$cons_rate=0; 
		$data_array1="";
		$updateID_array=array();
		$update_data=array();
		$issueQnty = $txt_return_qnty;
		
		$isLIFOfifo=return_field_value("store_method","variable_settings_inventory","company_name=$cbo_company_id and variable_list=16","store_method");
		if($isLIFOfifo==2) $cond_lifofifo=" DESC"; else $cond_lifofifo=" ASC"; 
		
		$sql = sql_select("select id,cons_rate,balance_qnty,balance_amount from inv_transaction where id=$transaction_id and prod_id=$txt_prod_id and balance_qnty>0 and transaction_type in(1,4,5) and item_category=$category order by transaction_date $cond_lifofifo");			
		foreach($sql as $result)
		{				
			$issue_trans_id = $result[csf("id")]; // this row will be updated
			$balance_qnty = $result[csf("balance_qnty")];
			$balance_amount = $result[csf("balance_amount")];
			$cons_rate = $result[csf("cons_rate")]; 
			$issueQntyBalance = $balance_qnty-$issueQnty; // minus issue qnty
			$issueStockBalance = $balance_amount-($issueQnty*$cons_rate);
			if($issueQntyBalance>=0)
			{					
				$amount = $issueQnty*$cons_rate;
				//for insert
				if($data_array1!="") $data_array1 .= ",";  
				//$data_array1 .= "(".$mrrWiseIsID.",".$issue_trans_id.")";
				$data_array1 .= "(".$mrrWiseIsID.",".$issue_trans_id.",".$transactionID.",264,".$txt_prod_id.",".$issueQnty.",".$cons_rate.",".$amount.",'".$user_id."','".$pc_date_time."')";
				//for update
				$updateID_array[]=$issue_trans_id; 
				$update_data[$issue_trans_id]=explode("*",("".$issueQntyBalance."*".$issueStockBalance."*'".$user_id."'*'".$pc_date_time."'"));
				break;
			}
			else if($issueQntyBalance<0)
			{
				
				$issueQntyBalance  = $issueQnty-$balance_qnty;				
				$issueQnty = $balance_qnty;				
				$amount = $issueQnty*$cons_rate;
				
				//for insert
				if($data_array1!="") $data_array1 .= ",";  
				//$data_array1 .= "(".$mrrWiseIsID.",".$issue_trans_id.")";
				$data_array1 .= "(".$mrrWiseIsID.",".$issue_trans_id.",".$transactionID.",264,".$txt_prod_id.",".$issueQnty.",".$cons_rate.",".$amount.",'".$user_id."','".$pc_date_time."')";
				//echo "20**".$data_array;die;
				//for update
				$updateID_array[]=$issue_trans_id; 
				$update_data[$issue_trans_id]=explode("*",("0*0*'".$user_id."'*'".$pc_date_time."'"));
				$issueQnty = $issueQntyBalance;
			}
			$mrrWiseIsID++;
			
		}//end foreach */

		$rID=$transID=$prodUpdate=$upTrID=$mrrWiseIssueID=$serialUpdate=true;

 		if(str_replace("'","",$txt_mrr_retrun_no)!="")
		{
			$rID=sql_update("inv_issue_master",$field_array_receive,$data_array_receive,"id",$id,1);
		}
		else
		{
			$rID=sql_insert("inv_issue_master",$field_array_receive,$data_array_receive,1);
		}
		$transID = sql_insert("inv_transaction",$field_array_trans,$data_array_trans,1);
		$prodUpdate = sql_update("product_details_master",$field_array_product,$data_array_product,"id",$txt_prod_id,1);

		//transaction table stock update here------------------------//
		if($balance_qnty>0)
		{
 			$upTrID=sql_update("inv_transaction",$update_array,$update_data,"id",$hidden_receive_trans_id,1);
		}
		//mrr wise issue data insert here----------------------------//
		if($data_array1!="")
		{		
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array1,$data_array1,1);
			
		}
		//echo $mrrWiseIssueID;die;
		$txt_serial_id 	= str_replace("'","",$txt_serial_id);
		if($txt_serial_id!="")
		{
			if( strpos(trim($txt_serial_no), ",")>0)
			{
				$se_data=explode(",",str_replace("'","",$txt_serial_no));
				if( (count($se_data)<=str_replace("'","",$txt_issue_qnty)))
				{
					$field_array_serial="issue_trans_id*is_issued*updated_by*update_date";
					$txt_serial_id_arr=explode(",",$txt_serial_id);
					if(count($txt_serial_id_arr)>0)
					{
						foreach($txt_serial_id_arr as $serial_id)
						{
							$update_data_serial[$serial_id]=explode("*",("".$transactionID."*1*'".$user_id."'*'".$pc_date_time."'"));
						}
					}
					
					$serialUpdate=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_serial,$txt_serial_id_arr),1);
					//$serialUpdate = execute_query("update inv_serial_no_details set issue_trans_id=$transactionID , is_issued=1 where id in ($txt_serial_id)",1);
				}
				else
				{
					echo "50";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}
			}
			else
			{
				$field_array_serial="issue_trans_id*is_issued*updated_by*update_date";
				$txt_serial_id_arr=explode(",",$txt_serial_id);
				if(count($txt_serial_id_arr)>0)
				{
					foreach($txt_serial_id_arr as $serial_id)
					{
						$update_data_serial[$serial_id]=explode("*",("".$transactionID."*1*'".$user_id."'*'".$pc_date_time."'"));
					}
				}
				
				$serialUpdate=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_serial,$txt_serial_id_arr),1);
				
				//$serialUpdate 	= execute_query("update inv_serial_no_details set issue_trans_id=$transactionID, is_issued=1 where id in ($txt_serial_id)",1);
				
			}
		}
		
		
 		/*if($txt_serial_id!="")
		{
			if( strpos(trim($txt_serial_no), ",")>0)
			{
				$se_data=explode(",",str_replace("'","",$txt_serial_no));
				if( (count($se_data)<=str_replace("'","",$txt_return_qnty)))
				{
					$serialUpdate = execute_query("update inv_serial_no_details set rev_rtn_trans_id=$transactionID , is_issued=1 where id in ($txt_serial_id)",1);
					
				}
				else
				{
					echo "50";
					die;
				}
			}
			else
			{
				$serialUpdate 	= execute_query("update inv_serial_no_details set rev_rtn_trans_id=$transactionID , is_issued=1 where id in ($txt_serial_id)",1);
			}
		}*/
		
		//echo "10**$rID";oci_rollback($con);die;
		//echo "10**$rID && $transID && $prodUpdate && $mrrWiseIssueID && $upTrID && $serialUpdate";oci_rollback($con);die;
		//&& $transID && $prodUpdate && $mrrWiseIssueID && $upTrID && $serialUpdate && $test
		if($db_type==0)
		{
			if($rID && $transID && $prodUpdate && $mrrWiseIssueID && $upTrID && $serialUpdate)
			{
				mysql_query("COMMIT");  
				echo "0**".$new_return_number[0]."**".$id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_return_number[0];
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $transID && $prodUpdate && $mrrWiseIssueID && $upTrID && $serialUpdate)
			{
				oci_commit($con);
				echo "0**".$new_return_number[0]."**".$id;
			}
			else
			{
				oci_rollback($con); 
				echo "10**".$new_return_number[0];
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
				
	}
	
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$txt_return_qnty=str_replace("'","",$txt_return_qnty);
		$txt_return_value = str_replace("'","",$txt_return_value);
		$txt_global_stock=str_replace("'","",$txt_global_stock);
		$txt_prod_id = str_replace("'","",$txt_prod_id);
		$update_id = str_replace("'","",$update_id);
		$txt_mrr_retrun_id = str_replace("'","",$txt_mrr_retrun_id);
		$hidden_receive_trans_id = str_replace("'","",$hidden_receive_trans_id);
		$before_receive_trans_id = str_replace("'","",$before_receive_trans_id);
		$prev_return_qnty = str_replace("'","",$prev_return_qnty);
		
		//table lock hare
		
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0**0"; die;}
		
		//check update id
		if( str_replace("'","",$update_id) == "" )
		{
			echo "10";
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			exit(); 
		}
		//****************************************** CHECK SAME PRODUCT RECEIVE_RTN *****************************************//
		
		$check_prod_id=str_replace("'","",$check_prod_id);
		//echo $check_prod_id;die;
		if($check_prod_id!="")
		{
			$sql_chack_prod=sql_select("select id from inv_transaction where mst_id=$txt_mrr_retrun_id and id!=$update_id and prod_id=$check_prod_id");
			if(($sql_chack_prod[0][csf("id")])>0)
			{
				echo "60";
				//check_table_status( $_SESSION['menu_id'],0);
				disconnect($con); 
				die;
			}
			
		}
		//echo "jahid";die;
		
		
		
		//****************************************** BEFORE ENTRY ADJUST START *****************************************//
		//product master table information
		//before stock update
		
		
		
		$sql = sql_select( "select a.id,a.avg_rate_per_unit,a.current_stock,a.stock_value, b.cons_quantity, b.cons_amount from product_details_master a, inv_transaction b where a.id=b.prod_id and b.id=$update_id and b.transaction_type=3" );
		$before_prod_id=$before_issue_qnty=$before_stock_qnty=$before_stock_value=0;
		foreach($sql as $result)
		{
			$before_prod_id 	= $result[csf("id")];
 			$before_stock_qnty 	= $result[csf("current_stock")];
			$before_stock_value = $result[csf("stock_value")];
			//before quantity and stock value
			$before_issue_qnty	= $result[csf("cons_quantity")];
			$before_issue_value	= $result[csf("cons_amount")];
		}
		
		//current product ID
		
		
		
		$sql = sql_select("select avg_rate_per_unit,current_stock,stock_value from product_details_master where id=$txt_prod_id ");
		$curr_avg_rate=$curr_stock_qnty=$curr_stock_value=0;
		foreach($sql as $result)
		{
			$current_avg_rate 		= $result[csf("avg_rate_per_unit")];
			$current_stock_qnty 	= $result[csf("current_stock")];
			$current_stock_value 	= $result[csf("stock_value")];
		}
		
		//echo $curr_avg_rate."*".$curr_stock_qnty." *".$curr_stock_value;die;
		
		//weighted and average rate START here------------------------//
		//product master table data UPDATE START----------------------//		
		$update_array_product	= "last_issued_qnty*current_stock*stock_value*updated_by*update_date";
		if($before_prod_id==$txt_prod_id)
		{
			$adj_stock_qnty = $current_stock_qnty+$before_issue_qnty-$txt_return_qnty; // CurrentStock + Before Issue Qnty - Current Issue Qnty
			$adj_stock_val  = $current_stock_value+$before_issue_value-$txt_return_value; // CurrentStockValue + Before Issue Value - Current Issue Value
			//$adj_avgrate	= number_format($adj_stock_val/$adj_stock_qnty,$dec_place[3],'.','');
			$latest_current_stock=$current_stock_qnty+$before_issue_qnty;
			if($adj_stock_qnty<0) //Aziz
			{
				echo "30**Stock cannot be less than zero.";
				//check_table_status( $_SESSION['menu_id'],0);
				disconnect($con); 
				die;
			}
			 
			$data_array_product	= "".$txt_return_qnty."*".$adj_stock_qnty."*".number_format($adj_stock_val,$dec_place[4],'.','')."*'".$user_id."'*'".$pc_date_time."'";
 			//$query1 		= sql_update("product_details_master",$update_array_product,$data_array_product,"id",$before_prod_id,0);
			
			//now current stock
			//$curr_avg_rate 		= $adj_avgrate;
			$curr_stock_qnty 	= $adj_stock_qnty;
			$curr_stock_value 	= $adj_stock_val;
			
			
			$txt_mrr_stock=return_field_value("balance_qnty as balance_qnty"," inv_transaction","status_active=1 and is_deleted=0 and id=$before_receive_trans_id","balance_qnty");
			$txt_mrr_stock=$txt_mrr_stock+str_replace("'","",$txt_return_qnty);
		}
		else
		{
			$updateID_array = $update_data_product = array();
			//before product adjust
			if($adj_before_stock_qnty<0) //Aziz
			{
				echo "30**Stock cannot be less than zero.";
				//check_table_status( $_SESSION['menu_id'],0);
				disconnect($con); 
				die;
			}
			$adj_before_stock_qnty 	= $before_stock_qnty+$before_issue_qnty; // CurrentStock + Before Issue Qnty
			$adj_before_stock_val  	= $before_stock_value+$before_issue_value; // CurrentStockValue + Before Issue Value
			//$adj_before_avgrate		= number_format($adj_before_stock_val/$adj_before_stock_qnty,$dec_place[3],'.','');
			
			$latest_current_stock=return_field_value("current_stock as current_stock","product_details_master","status_active=1 and is_deleted=0 and id=$txt_prod_id","current_stock");
			$txt_mrr_stock=return_field_value("balance_qnty as balance_qnty"," inv_transaction","status_active=1 and is_deleted=0 and id=$hidden_receive_trans_id","balance_qnty");
			 
			$updateID_array[]=$before_prod_id;
			$update_data_product[$before_prod_id]=explode("*",("".$before_issue_qnty."*".$adj_before_stock_qnty."*".number_format($adj_before_stock_val,$dec_place[4],'.','')."*'".$user_id."'*'".$pc_date_time."'"));
  			//$query5		= sql_update("product_details_master",$field_array,$data_array,"id",$before_prod_id,1);
			
			//current product adjust
			$adj_current_stock_qnty = 	$current_stock_qnty-$txt_return_qnty; // CurrentStock + Before Issue Qnty
			$adj_current_stock_val  = 	$current_stock_value-$txt_return_value; // CurrentStockValue + Before Issue Value
			$adj_current_avgrate	 =	number_format($adj_current_stock_val/$adj_current_stock_qnty,$dec_place[3],'.','');
			$updateID_array[] = $txt_prod_id;
			$update_data_product[$txt_prod_id]=explode("*",("".$txt_return_qnty."*".$adj_current_stock_qnty."*".number_format($adj_current_stock_val,$dec_place[4],'.','')."*'".$user_id."'*'".$pc_date_time."'"));
			//$query1=bulk_update_sql_statement("product_details_master","id",$update_array_product,$update_data_product,$updateID_array);die;
			/*$query1=bulk_update_sql_statement("product_details_master","id",$update_array_product,$update_data_last,$updateID_array_last);
			echo $query1;die;*/
			//now current stock
			//$curr_avg_rate 		= $adj_current_avgrate;
			$curr_stock_qnty 	= $adj_current_stock_qnty;
			$curr_stock_value 	= $adj_current_stock_val;
		}
		
		$txt_return_qnty=str_replace("'","",$txt_return_qnty);
		if(($txt_return_qnty*1)>($latest_current_stock*1))
		{
			echo "30**Return Quantity Not Over Global Stock.";
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con); 
			die;
		}
		
		
		if($txt_return_qnty>$txt_mrr_stock)
		{
			echo "30**Return Quantity Not Over MRR Stock.";
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con); 
			die;
		}
		
		//General Item master table UPDATE here START----------------------//		
		$field_array_receive="entry_form*company_id*supplier_id*issue_date*challan_no*received_id*received_mrr_no*updated_by*update_date*remarks";
		$data_array_receive="264*".$cbo_company_id."*".$cbo_return_to."*".$txt_return_date."*".$txt_challan_no."*".$txt_received_id."*".$txt_mrr_no."*'".$user_id."'*'".$pc_date_time."'*".$txt_remark."";
		
		

		
		$avg_rate_amount=str_replace("'","",$txt_return_qnty)*$current_avg_rate;
		$field_array_trans = "company_id*supplier_id*prod_id*item_category*transaction_type*transaction_date*store_id*floor_id*room*rack*self*bin_box*cons_uom*cons_quantity*cons_rate*cons_amount*updated_by*update_date*rcv_rate*rcv_amount";
		$data_array_trans = "".$cbo_company_id."*".$cbo_return_to."*".$txt_prod_id."*".$category."*3*".$txt_return_date."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$uom."*".$txt_return_qnty."*'".$current_avg_rate."'*'".$avg_rate_amount."'*'".$user_id."'*'".$pc_date_time."'*".$txt_return_rate."*".$txt_return_value.""; 
		
		
		
		
		$update_array_trans = "balance_qnty*balance_amount*updated_by*update_date";
		if($before_prod_id==$txt_prod_id)
		{
			if($before_receive_trans_id>0)
			{
				$sql_result = sql_select("select a.id,a.balance_qnty,a.balance_amount from inv_transaction a where a.id=$before_receive_trans_id"); 
				$sql_issue = sql_select("select id,cons_quantity,cons_amount from inv_transaction where id=$update_id");
				
				$adjBalance = ($sql_result[0][csf("balance_qnty")]+$sql_issue[0][csf("cons_quantity")])-$txt_return_qnty;
				$adjAmount = ($sql_result[0][csf("balance_amount")]+$sql_issue[0][csf("cons_amount")])-$txt_return_value;
				$update_data_trans="".$adjBalance."*".$adjAmount."*'".$user_id."'*'".$pc_date_time."'";
			}
		}
		else
		{
			if($before_receive_trans_id>0)
			{
				$sql_result = sql_select("select a.id,a.balance_qnty,a.balance_amount from inv_transaction a where a.id=$before_receive_trans_id and a.transaction_type =1"); 
				$sql_issue = sql_select("select id,cons_quantity,cons_amount from inv_transaction where id=$update_id");
				
				$adjBalance = ($sql_result[0][csf("balance_qnty")]+$sql_issue[0][csf("cons_quantity")]);
				$adjAmount = ($sql_result[0][csf("balance_amount")]+$sql_issue[0][csf("cons_amount")]);
				$update_data_trans="".$adjBalance."*".$adjAmount."*'".$user_id."'*'".$pc_date_time."'";
			}
			
			if($hidden_receive_trans_id>0)
			{
				$sql_result = sql_select("select a.id,a.balance_qnty,a.balance_amount from inv_transaction a where a.id=$hidden_receive_trans_id and a.transaction_type =1"); 
				$adjBalance = ($sql_result[0][csf("balance_qnty")]-$txt_return_qnty);
				$adjAmount = ($sql_result[0][csf("balance_amount")]-$txt_return_value);
				$update_data_trans_after="".$adjBalance."*".$adjAmount."*'".$user_id."'*'".$pc_date_time."'";
			}
			
			
			
		}
		
		//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);
		$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);  
		$field_array_mrr = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
		$data_array_mrr = "(".$mrrWiseIsID.",".$hidden_receive_trans_id.",".$update_id.",264,".$txt_prod_id.",".$txt_return_qnty.",".$txt_return_rate.",".$txt_return_value.",'".$user_id."','".$pc_date_time."')";
		
		$query1=$rID=$rID_issue_trans=$up_trID=$up_trID_after=$query2=$mrrWiseIssueID=1;
		if($before_prod_id==$txt_prod_id)
		{
 			$query1 = sql_update("product_details_master",$update_array_product,$data_array_product,"id",$before_prod_id,1);
			
			$rID=sql_update("inv_issue_master",$field_array_receive,$data_array_receive,"id",$txt_mrr_retrun_id,1);
			$rID_issue_trans=sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,1);
			$up_trID=sql_update("inv_transaction",$update_array_trans,$update_data_trans,"id",$before_receive_trans_id,1);
			$query2 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_id and entry_form=264",1);
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);

		}
		else
		{
			$query1=execute_query(bulk_update_sql_statement("product_details_master","id",$update_array_product,$update_data_product,$updateID_array),1);
			$rID=sql_update("inv_issue_master",$field_array_receive,$data_array_receive,"id",$txt_mrr_retrun_id,1);
			$rID_issue_trans=sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,1);
			$up_trID=sql_update("inv_transaction",$update_array_trans,$update_data_trans,"id",$before_receive_trans_id,1);
			$up_trID_after=sql_update("inv_transaction",$update_array_trans,$update_data_trans_after,"id",$hidden_receive_trans_id,1);
			$query2 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_id and entry_form=264",1);
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
		}
		
		
		
		$serialUpdate=true;
		$serialDelete=true;
		$txt_serial_id 	= trim(str_replace("'","",$txt_serial_id));
		//print_r($txt_serial_id);die;
		if($txt_serial_id!="")
		{
			$field_array_serial="issue_trans_id*is_issued*updated_by*update_date";
			$before_serial_id=trim(str_replace("'","",$before_serial_id));$txt_serial_id=trim(str_replace("'","",$txt_serial_id));$update_id=trim(str_replace("'","",$update_id));
			if( strpos(trim($txt_serial_no), ",")>0)
			{
				$se_data=explode(",",str_replace("'","",$txt_serial_no));
				if( (count($se_data)<=str_replace("'","",$txt_issue_qnty)))
				{
					if($before_serial_id !="")
					{
						$txt_before_serial_id_arr=explode(",",$before_serial_id);
						if(count($txt_before_serial_id_arr)>0)
						{
							foreach($txt_before_serial_id_arr as $serial_id)
							{
								$update_data_before_serial[$serial_id]=explode("*",("0*0*'".$user_id."'*'".$pc_date_time."'"));
							}
							$serialDelete=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_before_serial,$txt_before_serial_id_arr),1);
							//$serialDelete=execute_query("update inv_serial_no_details set issue_trans_id=0 , is_issued=0 where id in ($before_serial_id)",0);
						}
					}
					$txt_serial_id_arr=explode(",",$txt_serial_id);
					if(count($txt_serial_id_arr)>0)
					{
						foreach($txt_serial_id_arr as $serial_id)
						{
							$update_data_serial[$serial_id]=explode("*",("".$update_id."*1*'".$user_id."'*'".$pc_date_time."'"));
						}
					}
					$serialUpdate=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_serial,$txt_serial_id_arr),1);
					
					//$serialUpdate = execute_query("update inv_serial_no_details set issue_trans_id=$update_id , is_issued=1 where id in ($txt_serial_id)",0);
				}
				else
				{
					echo "50";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con); 
					die;
				}
			}
			else
			{
				
				/*if($before_serial_id !="")
				{
				$serialDelete	=execute_query("update inv_serial_no_details set issue_trans_id=0 , is_issued=0 where id in ($before_serial_id)",0);
				}
				
				$serialUpdate 	= execute_query("update inv_serial_no_details set issue_trans_id=$update_id , is_issued=1 where id in ($txt_serial_id)",0);*/
				
				
				if($before_serial_id !="")
				{
					//echo "nahid";die;
					$txt_before_serial_id_arr=explode(",",$before_serial_id);
					if(count($txt_before_serial_id_arr)>0)
					{
						foreach($txt_before_serial_id_arr as $serial_id)
						{
							$update_data_before_serial[$serial_id]=explode("*",("0*0*'".$user_id."'*'".$pc_date_time."'"));
						}
						$serialDelete=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_before_serial,$txt_before_serial_id_arr),1);
						//$serialDelete=execute_query("update inv_serial_no_details set issue_trans_id=0 , is_issued=0 where id in ($before_serial_id)",0);
					}
				}
				//echo $serialDelete;die;
				$txt_serial_id_arr=explode(",",$txt_serial_id);
				if(count($txt_serial_id_arr)>0)
				{
					foreach($txt_serial_id_arr as $serial_id)
					{
						$update_data_serial[$serial_id]=explode("*",("".$update_id."*1*'".$user_id."'*'".$pc_date_time."'"));
					}
				}
				$serialUpdate=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_serial,$txt_serial_id_arr),1);
			}
		}
		
 		/*if($txt_serial_id!="")
		{
			$before_serial_id=trim(str_replace("'","",$before_serial_id));$txt_serial_id=trim(str_replace("'","",$txt_serial_id));$update_id=trim(str_replace("'","",$update_id));
			$txt_serial_id_arr=explode(",",$txt_serial_id);
			$before_serial_id_arr=explode(",",$before_serial_id);
			$array_difference=array_diff($before_serial_id_arr,$txt_serial_id_arr);
			//print_r(implode(",",$array_difference));die;
			
			if( strpos(trim($txt_serial_no), ",")>0)
			{
				$se_data=explode(",",str_replace("'","",$txt_serial_no));
				
				if( (count($se_data)<=str_replace("'","",$txt_return_qnty)))
				{
					if(!empty($array_difference))
					{
						$serialDelete=execute_query("update inv_serial_no_details set rev_rtn_trans_id=0 , is_issued=0 where id in (".implode(",",$array_difference).")",1);
					}
					else
					{
						$serialUpdate = execute_query("update inv_serial_no_details set rev_rtn_trans_id=$update_id , is_issued=1 where id in ($txt_serial_id)",1);
					}
				}
				else
				{
					echo "50";
					die;
				}
			}
			else
			{
				if(!empty($array_difference))
				{
					$serialDelete	=execute_query("update inv_serial_no_details set rev_rtn_trans_id=0 , is_issued=0 where id in(".implode(",",$array_difference).")",1);
				}
				else
				{
					$serialUpdate 	= execute_query("update inv_serial_no_details set rev_rtn_trans_id=$update_id , is_issued=1 where id in ($txt_serial_id)");
				}
			}
		}*/
		
		
		if($db_type==0)
		{
			if($query1 && $rID && $rID_issue_trans && $up_trID && $up_trID_after && $query2 && $mrrWiseIssueID  && $serialUpdate && $serialDelete)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_mrr_retrun_no)."**".$txt_mrr_retrun_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_mrr_retrun_no)."**".$txt_mrr_retrun_id;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($query1 && $rID && $rID_issue_trans && $up_trID && $up_trID_after && $query2 && $mrrWiseIssueID  && $serialDelete&& $serialUpdate)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_mrr_retrun_no)."**".$txt_mrr_retrun_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_mrr_retrun_no)."**".$txt_mrr_retrun_id;
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
 	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		$con = connect(); 
		if($db_type==0)	{ mysql_query("BEGIN"); }
		// master table delete here---------------------------------------
		$mst_id = str_replace("'","",$txt_mrr_retrun_id);
		if($mst_id=="" || $mst_id==0)
		{ 
			echo "16**Delete not allowed. Problem occurred"; 
			disconnect($con);
			die;
		}
		else 
		{
			$update_id = str_replace("'","",$update_id);
			$product_id = str_replace("'","",$txt_prod_id);
			if( str_replace("'","",$update_id) == "" )
			{
				echo "16**Delete not allowed. Problem occurred"; 
				//check_table_status( $_SESSION['menu_id'],0);
				disconnect($con);
				die; 
			}

			//echo "10**select id from inv_transaction where transaction_type in(2,3,6) and prod_id=$product_id and status_active=1 and is_deleted=0 and id >$update_id"; die;
			$chk_next_transaction=return_field_value("id","inv_transaction","transaction_type in(1,2,3,4,5,6) and prod_id=$product_id and status_active=1 and is_deleted=0 and issue_trans_id >$update_id ","id");
			if($chk_next_transaction !="")
			{ 
				echo "17**Delete not allowed.This item is used in another transaction"; 
				disconnect($con);
				die;
			}
			else
			{
				//echo "10**select id from inv_mrr_wise_issue_details where prod_id=$product_id and status_active=1 and is_deleted=0 and issue_trans_id=$update_id"; die;
				$mrr_table_id=return_field_value("id","inv_mrr_wise_issue_details","prod_id=$product_id and status_active=1 and is_deleted=0 and issue_trans_id=$update_id ","id");

				$sql = sql_select("select a.prod_id,a.cons_quantity,a.cons_rate,a.cons_amount,b.current_stock,b.stock_value from inv_transaction a, product_details_master b  where a.status_active=1 and a.id=$update_id and a.prod_id=b.id");
			
				$before_prod_id=$before_receive_qnty=$before_rate=$beforeAmount=$before_brand="";
				$beforeStock=$beforeStockValue=0;
				foreach( $sql as $row)
				{
					$before_prod_id 		= $row[csf("prod_id")]; 
					$before_receive_qnty 	= $row[csf("cons_quantity")]; //stock qnty
					$before_rate 			= $row[csf("cons_rate")]; 
					$beforeAmount			= $row[csf("cons_amount")]; //stock value
					$beforeStock			=$row[csf("current_stock")];
					$beforeStockValue		=$row[csf("stock_value")];
					//$beforeAvgRate			=$row[csf("avg_rate_per_unit")];	
				}
				//stock value minus here---------------------------//
				$adj_beforeStock			=$beforeStock+$before_receive_qnty;
				$adj_beforeStockValue		=$beforeStockValue+$beforeAmount;
				//$adj_beforeAvgRate			=number_format(($adj_beforeStockValue/$adj_beforeStock),$dec_place[3],'.','');		
			
				$field_array_product="current_stock*stock_value*updated_by*update_date";
				$data_array_product = "".$adj_beforeStock."*".number_format($adj_beforeStockValue,$dec_place[4],'.','')."*'".$user_id."'*'".$pc_date_time."'";
				
				$field_array_trans="updated_by*update_date*status_active*is_deleted";
				$data_array_trans="".$user_id."*'".$pc_date_time."'*0*1";

				$field_array_mrr="updated_by*update_date*status_active*is_deleted";
				$data_array_mrr="".$user_id."*'".$pc_date_time."'*0*1";
				
				$rID=sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,1);
				$rID2=sql_update("product_details_master",$field_array_product,$data_array_product,"id",$product_id,1);
				$rID3=sql_update("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,"id",$mrr_table_id,1);
			}
		}
		
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_mrr_retrun_no)."**".$txt_mrr_retrun_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "2**".str_replace("'","",$txt_mrr_retrun_no)."**".$txt_mrr_retrun_id;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_mrr_retrun_no)."**".$txt_mrr_retrun_id;
			}
			else
			{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$txt_mrr_retrun_no)."**".$txt_mrr_retrun_id;
			}
		}
		disconnect($con);
		die;
	}
}
/* Return ID List View Action*/
if($action=="return_number_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);  
?>
     
<script>
	function js_set_value(mrr)
	{
 		$("#hidden_return_number").val(mrr); // mrr number
		parent.emailwindow.hide();
	}
</script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>                	 
                    <th width="250" align="center" id="search_by_td_up">Enter Return Number</th>
                    <th width="300">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr class="general">                    
                    <td width="" align="center" id="search_by_td">				
                        <input type="text" style="width:150px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td>    
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date" />
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_return_search_list_view', 'search_div', 'raw_material_item_receive_return_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="5">
					<? echo load_month_buttons(1);  ?>
                     <input type="hidden" id="hidden_return_number" value="" />
                </td>
            </tr>    
            </tbody>
         </tr>         
        </table>
        <br>    
        <div align="center" valign="top" id="search_div" style="width:870px;"> </div> 
        </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}
if($action=="create_return_search_list_view")
{
	$ex_data = explode("_",$data);
	$search_by = $ex_data[0];
	$search_common = $ex_data[0];
	$txt_date_from = $ex_data[1];
	$txt_date_to = $ex_data[2];
	$company = $ex_data[3];
	
	$sql_cond="";
	if( $txt_date_from!="" || $txt_date_to!="" ) $sql_cond .= " and a.issue_date  between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
	if($company!="") $sql_cond .= " and a.company_id='$company'";
	if($search_common!="") $sql_cond .= "and a.issue_number_prefix_num='$search_common'";
	
	
	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id='$user_id'");
	$cre_company_id = $userCredential[0][csf('company_id')];
	$cre_supplier_id = $userCredential[0][csf('supplier_id')];
	$cre_store_location_id = $userCredential[0][csf('store_location_id')];
	$cre_item_cate_id = $userCredential[0][csf('item_cate_id')];
	
	$credientian_cond="";
	if($cre_company_id!="") $credientian_cond=" and a.company_id in($cre_company_id)";
	if($cre_supplier_id!="") $credientian_cond.=" and a.supplier_id in($cre_supplier_id)";
	if($cre_store_location_id!="") $credientian_cond.=" and b.store_id in($cre_store_location_id)";
	//if($cre_item_cate_id!="") $credientian_cond.=" and b.item_category in($cre_item_cate_id)";
	
	$sql = "select a.id, a.issue_number_prefix_num, a.issue_number, a.company_id, a.supplier_id, a.issue_date, a.item_category, a.received_id, a.received_mrr_no   
			from inv_issue_master a, inv_transaction b 
			where a.id=b.mst_id and b.transaction_type=3 and a.status_active=1 and a.entry_form=264  and  b.item_category=101  $sql_cond $credientian_cond 
			group by a.id, a.issue_number_prefix_num, a.issue_number, a.company_id, a.supplier_id, a.issue_date, a.item_category, a.received_id, a.received_mrr_no 
			order by a.id";
	//echo $sql;
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$arr=array(1=>$company_arr,2=>$supplier_arr);
 	echo create_list_view("list_view", "Return No, Company Name, Returned To, Return Date, Receive MRR","150,150,120,120","850","260",0, $sql , "js_set_value", "issue_number,id", "", 1, "0,company_id,supplier_id,0,0", $arr, "issue_number,company_id,supplier_id,issue_date,received_mrr_no","","",'0,0,0,3,0') ;	
 	exit();
}
if($action=="populate_master_from_data")
{  
	$sql = "select id,issue_number,company_id,supplier_id,issue_date,challan_no,item_category,received_id,received_mrr_no   
			from inv_issue_master 
			where id='$data' and entry_form=264";
	//echo $sql;
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#txt_mrr_retrun_no').val('".$row[csf("issue_number")]."');\n";
 		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
 		echo "$('#cbo_return_to').val('".$row[csf("supplier_id")]."');\n";
		echo "$('#txt_challan_no').val('".$row[csf("challan_no")]."');\n";
		echo "$('#txt_return_date').val('".change_date_format($row[csf("issue_date")])."');\n";
		echo "$('#txt_mrr_no').val('".$row[csf("received_mrr_no")]."');\n";
		echo "$('#txt_received_id').val('".$row[csf("received_id")]."');\n";
		//right side list view
		
 		echo "set_button_status(1, permission, 'fnc_general_receive_return_entry',1,1);\n";
		
		if($db_type==0)
		{
			$sql = sql_select("select $group_concat(b.id )as tr_id , a.id as rec_id
			from inv_receive_master a, inv_transaction b 
			where a.id=b.mst_id  and a.recv_number='".$row[csf("received_mrr_no")]."' and b.transaction_type=1 and b.item_category  in (101) group by a.id");
		}
		else
		{
			$sql = sql_select("select LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as tr_id , a.id as rec_id
			from inv_receive_master a, inv_transaction b 
			where a.id=b.mst_id  and a.recv_number='".$row[csf("received_mrr_no")]."' and b.transaction_type=1 and b.item_category in (101) group by a.id");

		}
		foreach($sql as $row_t)
		{
			echo "$('#transaction_id').val('".$row_t[csf("tr_id")]."');\n";
			echo"show_list_view('".$row_t[csf("rec_id")]."','show_product_listview','list_product_container','requires/raw_material_item_receive_return_entry_controller','');\n";
		}
   	}	
	exit();	
}
/*After Save List View*/
if($action=="show_dtls_list_view")
{
	$ex_data = explode("**",$data);
	$return_number = $ex_data[0];
	$ret_mst_id = str_replace("'","",$ex_data[1] );
	//echo $ret_mst_id;die;
	$cond="";
	if($ret_mst_id!="") $cond .= " and a.id='$ret_mst_id'";
	$item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");

	$sql = "select a.issue_number, a.company_id, a.supplier_id, a.issue_date, a.received_id, a.received_mrr_no, b.id, b.item_category, b.prod_id, b.cons_quantity, b.cons_uom, b.rcv_rate as cons_rate, b.rcv_amount as cons_amount, c.item_description, c.item_group_id 
	from inv_issue_master a, inv_transaction b,product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=3 and b.status_active=1 and b.is_deleted=0 $cond ";
	//echo $sql;
	$result = sql_select($sql);
	$i=1;
	$rettotalQnty=0;
	$rcvtotalQnty=0;
	$totalAmount=0;
	?> 
     	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" style="width:850px" rules="all" >
        	<thead>
            	<tr>
                	<th>SL</th>
                    <th>Item Category</th>
                    <th>Item Group</th>
                    <th>Item Description</th>
                    <th>Returned Qty.</th>
                    <th>UOM</th>
                    <th>Rate</th>
                    <th>Return Value</th> 
                </tr>
            </thead>
            <tbody>
            	<? 
				foreach($result as $row){					
					if($i%2==0)
						$bgcolor="#E9F3FF";
					else 
						$bgcolor="#FFFFFF";
					$pro_id=$row[csf("prod_id")];
					//echo "select b.balance_qnty,b.cons_quantity from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id='$pro_id' and b.transaction_type=1 and b.item_category not in (1,2,3,4,5,6,7,12,13,14) and  a.recv_number='".$row[csf("received_mrr_no")]."'";
					$sqlTr = sql_select("select a.id, b.balance_qnty,b.cons_quantity from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id='$pro_id' and b.transaction_type=1 and b.item_category  in (101) and  a.recv_number='".$row[csf("received_mrr_no")]."'");
					$rcvQnty = $sqlTr[0][csf('balance_qnty')];
					
					$total_cons_quantity=$sqlTr[0][csf('cons_quantity')];
					$rettotalQnty +=$row[csf("cons_quantity")];
					$totalAmount +=$row[csf("cons_amount")];		
 				?>
                	<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("id")];?>,<? echo $rcvQnty;?>,<? echo $total_cons_quantity;?>","child_form_input_data","requires/raw_material_item_receive_return_entry_controller")' style="cursor:pointer" >
                        <td width="30"><?php echo $i; ?></td>
                        <td width="100"><p><?php echo $item_category[$row[csf("item_category")]]; ?></p></td>
                        <td width="200"><p><?php echo $item_name_arr[$row[csf("item_group_id")]]; ?></p></td>
                        <td width="80"><p><?php echo $row[csf("item_description")]; ?></p></td>
                        <td width="70" align="right" style="padding-right:3px;"><p><?php echo number_format($row[csf("cons_quantity")],2); ?></p></td>
                        <td width="70"><p><?php echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                        <td width="70" align="right"><p><?php echo $row[csf("cons_rate")]; ?></p></td>
                        <td width="70" align="right"><p><?php echo number_format($row[csf("cons_amount")],2); ?></p></td>
                   </tr>
                <? $i++; } ?>
                	<tfoot>
                        <th colspan="4">Total</th>                         
                        <th><?php echo number_format($rettotalQnty,2)  //$total_order_qnty; ?></th> 
                        <th colspan="2"></th>
                        <th><?php echo number_format($totalAmount,2,'.',','); ?></th>
                   </tfoot>
            </tbody>
        </table>
    <?
	exit();
}
if($action=="child_form_input_data")
{
	$ex_data = explode(",",$data);
	$data2 = $ex_data[0]; 	// transaction id
	$rcvQnty = $ex_data[1];
	$total_cons_quantity= $ex_data[2]; 
 	$sql = "select c.id as prod_id, c.item_description, c.item_group_id, c.current_stock,b.company_id, b.id as tr_id, b.item_category, b.store_id,b.floor_id,b.room,b.rack,b.self, b.cons_uom, b.rcv_rate as cons_rate, b.cons_quantity, b.rcv_amount as cons_amount, a.received_id, a.remarks
			from  inv_issue_master a, inv_transaction b, product_details_master c
 			where a.id=b.mst_id and b.prod_id=c.id and b.id=$data2 and b.status_active=1 and b.transaction_type=3 and b.item_category in (101) ";
 	//echo $sql;die;
	$store_name=return_library_array("select id,store_name from lib_store_location", "id","store_name"); 
	$item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
	$result = sql_select($sql);
	foreach($result as $row)
	{
		$rcv_sql=sql_select("select b.id as trans_id, b.cons_quantity, b.balance_qnty from inv_mrr_wise_issue_details a, inv_transaction b where a.recv_trans_id=b.id and a.issue_trans_id='".$row[csf("tr_id")]."' and a.status_active=1 and b.status_active=1");
		$recv_trans_id=$rcv_sql[0][csf("trans_id")];
		$receive_quantity=$rcv_sql[0][csf("cons_quantity")];
		$balance_quantity=$rcv_sql[0][csf("balance_qnty")];
		
		//$recv_trans_id=return_field_value("recv_trans_id","inv_mrr_wise_issue_details","issue_trans_id='".$row[csf('tr_id')]."'","recv_trans_id" );
		//$receive_quantity=return_field_value("cons_quantity","inv_transaction ","id=$recv_trans_id","cons_quantity" );
		
		$rcvQnty=$balance_quantity+$row[csf("cons_quantity")];
 		echo "$('#txt_item_category').val('".$item_category[$row[csf("item_category")]]."');\n";
		echo "$('#category').val('".$row[csf("item_category")]."');\n";

		echo "load_room_rack_self_bin('requires/raw_material_item_receive_return_entry_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94_101', 'store','store_td', '".$row[csf('company_id')]."','"."',this.value);\n";
		echo "$('#cbo_store_name').val('".$row[csf("store_id")]."');\n";
		echo "$('#store').val('".$row[csf("store_id")]."');\n";

		echo "load_room_rack_self_bin('requires/raw_material_item_receive_return_entry_controller', 'floor','floor_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."',this.value);\n";
		echo "$('#cbo_floor').val('".$row[csf("floor_id")]."');\n";
		echo "load_room_rack_self_bin('requires/raw_material_item_receive_return_entry_controller', 'room','room_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."',this.value);\n";
		echo "$('#cbo_room').val('".$row[csf("room")]."');\n";
		echo "load_room_rack_self_bin('requires/raw_material_item_receive_return_entry_controller', 'rack','rack_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		echo "$('#txt_rack').val('".$row[csf("rack")]."');\n";
		echo "load_room_rack_self_bin('requires/raw_material_item_receive_return_entry_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";	
		echo "$('#txt_shelf').val('".$row[csf("self")]."');\n";

		echo "$('#txt_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#before_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#txt_item_group').val('".$item_name_arr[$row[csf("item_group_id")]]."');\n";
		echo "$('#txt_item_description').val('".$row[csf("item_description")]."');\n";
		echo "$('#txt_remark').val('".$row[csf("remarks")]."');\n";
		echo "$('#txt_return_qnty').val('".$row[csf("cons_quantity")]."');\n";	
		echo "$('#prev_return_qnty').val('".$row[csf("cons_quantity")]."');\n";
		
		echo "$('#txt_mrr_stock').val('".$rcvQnty."');\n";
		echo "$('#txt_cons_quantity').val('".$total_cons_quantity."');\n";
		
		echo "$('#txt_uom').val('".$unit_of_measurement[$row[csf("cons_uom")]]."');\n";
		echo "$('#uom').val('".$row[csf("cons_uom")]."');\n";
		echo "$('#txt_return_rate').val('".$row[csf("cons_rate")]."');\n";
		echo "$('#privous_rate').val('".$row[csf("cons_rate")]."');\n";			
		echo "$('#txt_return_value').val(".$row[csf("cons_amount")].");\n";
		echo "$('#update_id').val(".$row[csf("tr_id")].");\n";
		//$recv_trans_id=return_field_value("recv_trans_id","inv_mrr_wise_issue_details","issue_trans_id='".$row[csf('tr_id')]."'","recv_trans_id" );
		//$receive_quantity=return_field_value("cons_quantity","inv_transaction ","id=$recv_trans_id","cons_quantity" );
		
		
		
		
		$cumilitive_rtn=return_field_value("sum(b.issue_qnty) as issue_qnty","inv_mrr_wise_issue_details b","b.status_active=1 and b.prod_id='".$row[csf("prod_id")]."' and b.recv_trans_id='$recv_trans_id'","issue_qnty" );
		
		if($db_type==0)
		{
			$serialString = return_field_value("group_concat(serial_no) as sr","inv_serial_no_details","rev_rtn_trans_id=".$row[csf("tr_id")]." group by rev_rtn_trans_id","sr");
			$serialStringId = return_field_value("group_concat(id) as id","inv_serial_no_details","rev_rtn_trans_id=".$row[csf("tr_id")]." group by rev_rtn_trans_id","id");
		}
		else
		{
			$serialString = return_field_value("LISTAGG(CAST(serial_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY serial_no) as sr","inv_serial_no_details","rev_rtn_trans_id=".$row[csf("tr_id")]." group by rev_rtn_trans_id","sr");
			$serialStringId = return_field_value("LISTAGG(CAST(id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as id","inv_serial_no_details","rev_rtn_trans_id=".$row[csf("tr_id")]." group by rev_rtn_trans_id","id");
		}
		echo "$('#txt_serial_no').val('".$serialString."');\n";
		echo "$('#txt_serial_id').val('".$serialStringId."');\n";
		echo "$('#before_serial_id').val('".$serialStringId."');\n";
		echo "$('#hidden_receive_trans_id').val('$recv_trans_id');\n";
		echo "$('#before_receive_trans_id').val('$recv_trans_id');\n";
		$yet_to_iss=(($receive_quantity+$row[csf("cons_quantity")])-$cumilitive_rtn);
		$global_stock=($row[csf("cons_quantity")]*1)+($row[csf("current_stock")]*1);
		
		echo "$('#txt_fabric_received').val('$receive_quantity');\n";	
		echo "$('#txt_cumulative_issued').val(".$cumilitive_rtn.");\n";
		echo "$('#txt_yet_to_issue').val(".$yet_to_iss.");\n";	
		echo "$('#txt_global_stock').val(".$global_stock.");\n";	
		
	}
	echo "set_button_status(1, permission, 'fnc_general_receive_return_entry',1,1);\n";
	echo "$('#cbo_company_id').attr('disabled',true);\n";
	echo "$('#txt_mrr_no').attr('disabled',true);\n";
  	exit();
}

if ($action=="general_item_receive_return_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$sql=" select id, issue_number, issue_date, received_id, challan_no, supplier_id,remarks from  inv_issue_master where id=$data[1] and company_id='$data[0]' ";
	//echo $sql;
	$dataArray=sql_select($sql);

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$rec_id_arr=return_library_array( "select id, recv_number from  inv_receive_master", "id", "recv_number"  );
	
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
            <td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Reprot</u></strong></td>
        </tr>
        <tr>
        	<td width="120"><strong>Return ID:</strong></td><td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
            <td width="100"><strong>Return Date :</strong></td> <td width="230px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
            <td width="100"><strong>Receive ID:</strong></td><td width="175px"><? echo $rec_id_arr[$dataArray[0][csf('received_id')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Challan No:</strong></td> <td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Returned To:</strong></td><td width="230px"><? echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
            <td><strong>Remark :</strong></td><td width="175px"><? echo $dataArray[0][csf('remarks')]; ?></td>
        </tr>
    </table>
    <br>
	<div style="width:100%;">
		<table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="40">SL</th>
                <th width="80" align="center">Item Category</th>
                <th width="150" align="center">Item Group</th>
                <th width="200" align="center">Item Description</th>
                <th width="50" align="center">UOM</th> 
                <th width="80" align="center">Returned. Qnty.</th>
                <th width="50" align="center">Rate</th>
                <th width="70" align="center">Return Value</th>
                <th width="80" align="center">Store</th> 
            </thead>
<?
	$i=1;
	$item_name_arr=return_library_array( "select id, item_name from  lib_item_group", "id", "item_name"  );
	if($db_type==0)
	{
		$sql_dtls= "select b.mst_id as id, b.item_category, b.cons_uom, b.cons_quantity, b.rcv_rate as cons_rate, b.rcv_amount as cons_amount, b.store_id, c.item_group_id, concat(c.sub_group_name,' ',c.item_description,' ',c.item_size ) as product_name_details,c.inserted_by from   inv_transaction b,  product_details_master c where b.prod_id=c.id and b.mst_id='$data[1]' and  b.transaction_type=3";
	}
	else
	{
		$sql_dtls= "select b.mst_id as id, b.item_category, b.cons_uom, b.cons_quantity, b.rcv_rate as cons_rate, b.rcv_amount as cons_amount, b.store_id, c.item_group_id,(c.sub_group_name || ' ' || c.item_description || ' ' || c.item_size ) as product_name_details,c.inserted_by from  inv_transaction b,  product_details_master c where b.prod_id=c.id and b.mst_id='$data[1]' and  b.transaction_type=3";
	}
	//echo $sql_dtls;
	$sql_result=sql_select($sql_dtls);
	$inserted_by=$sql_result[0][csf("inserted_by")];
	foreach($sql_result as $row)
	{
		if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
			$cons_quantity=$row[csf('cons_quantity')];
			$cons_quantity_sum += $cons_quantity;
			
			$cons_amount=$row[csf('cons_amount')];
			$cons_amount_sum += $cons_amount;
			
			$desc=$row[csf('item_description')];
			
			if($row[csf('item_size')]!="")
			{
				$desc.=", ".$row[csf('item_size')];
			}
		?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td><? echo $i; ?></td>
                <td><? echo $item_category[$row[csf('item_category')]]; ?></td>
                <td><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
                <td><? echo $row[csf('product_name_details')]; ?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
                <td align="right"><? echo number_format($row[csf('cons_quantity')],2); ?></td>
                <td align="right"><? echo number_format($row[csf('cons_rate')],2); ?></td>
                <td align="right"><? echo number_format($row[csf('cons_amount')],2); ?></td>
                <td><? echo $store_library[$row[csf('store_id')]]; ?></td>
			</tr>
			<?
			$i++;
			}
			?>
        	<tr> 
                <td align="right" colspan="5" >Total</td>
                <td align="right"><? echo number_format($cons_quantity_sum,2); ?></td>
                <td align="right" colspan="2" ><? echo number_format($cons_amount_sum,2); ?></td>
                <td align="right">&nbsp;</td>
			</tr>
		</table> 
        <br>
		 <?
            echo signature_table(270, $data[0], "900px","","",$inserted_by);
         ?>
      </div>
   </div> 
 <? 
 exit();  
 }
?>