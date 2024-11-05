<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


$user_id = $_SESSION['logic_erp']['user_id'];
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
if ($supplier_id !='') {
    $supplier_credential_cond = " and a.id in($supplier_id)";
}

if ($company_location_id !='') {
    $location_credential_cond = " and id in($company_location_id)";
}

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 132, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' and b.category_type=4 and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
	exit();
}

/*if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 132, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "" );
	exit();
}*/

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 132, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' $location_credential_cond order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/trims_issue_entry_controller', this.value+'_'+document.getElementById('cbo_sewing_company').value+'_'+document.getElementById('cbo_sewing_source').value+'_'+document.getElementById('cbo_issue_purpose').value, 'load_drop_down_floor', 'floor_td');",0 );     	 
}

if($action=="load_drop_down_sewing_com")
{
	$data = explode("_",$data);
	$company_id=$data[1];
	
	if($data[0]==1)
	{
		echo create_drop_down( "cbo_sewing_company", 132, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Sewing Company--", "$company_id", "load_location(this.value);","" );
	}
	else if($data[0]==3)
	{	
		echo create_drop_down( "cbo_sewing_company", 132, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company_id' and b.party_type in(4,5,22) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Sewing Company--", 0, "load_location(this.value);" );
	}
	else
	{
		echo create_drop_down( "cbo_sewing_company", 132, $blank_array,"",1, "--Select Sewing Company--", 0, "load_location(this.value);" );
	}
	exit();
}


// Floor drop down
if ($action=="load_drop_down_floor")
{
	$data=explode("_",$data);
	if($data[2]==1)
	{
		if($data[3]==36) $prod_source=5;
		else if($data[3]==37) $prod_source=3;
		else if($data[3]==41) $prod_source=1;
		else if($data[3]==42) $prod_source=11;
		else $prod_source='1,3,5,11';
		echo create_drop_down( "cbo_floor", 146, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process in($prod_source) and location_id=$data[0] and company_id=$data[1] order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "get_php_form_data(document.getElementById('cbo_floor').value,'line_disable_enable','requires/trims_issue_entry_controller'); load_drop_down( 'requires/trims_issue_entry_controller', this.value+'_'+$data[0]+'_'+$data[1], 'load_drop_down_sewing_line_floor', 'sewing_line_td' );",0 ); 
	}
	else
	{
		 echo create_drop_down( "cbo_floor", 146, $blank_array,"", 1, "-- Select Floor --", 0, "load_drop_down( 'requires/trims_issue_entry_controller', this.value+'_'+$data[0]+'_'+$data[1], 'load_drop_down_sewing_line_floor', 'sewing_line_td' );" );
	}
}

//  line drop down
if($action=="load_drop_down_sewing_line_floor")
{
	$explode_data = explode("_",$data);	

	echo create_drop_down( "cbo_sewing_line", 100, "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 and floor_name = $explode_data[0] and company_name=$explode_data[2] and location_name=$explode_data[1] order by line_name","id,line_name", 1, "--- Select ---", $selected, "",0,0 );	
	exit();
}


$trim_group_arr =array();
$data_array=sql_select("select id, item_name, trim_uom, conversion_factor from lib_item_group");
foreach($data_array as $row)
{
	$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
	$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('trim_uom')];
	$trim_group_arr[$row[csf('id')]]['conversion_factor']=$row[csf('conversion_factor')];
}

if($action=="create_itemDesc_search_list_view")
{
	$data=explode("**",$data);
	$po_id=$data[0];
	$prod_id=$data[1];
	$store_id=$data[2];
    $rack_shelf_array=array();
	$dataArray=sql_select("select a.prod_id, a.rack_no, a.self_no from inv_goods_placement a, order_wise_pro_details b 
	where a.prod_id=b.prod_id and a.dtls_id=b.dtls_id and a.entry_form=24 and b.entry_form=24 and b.trans_type=1 and b.po_breakdown_id in ($data[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.prod_id, a.rack_no, a.self_no");
	foreach($dataArray as $row)
	{
		$rack_shelf_array[$row[csf('prod_id')]].=$row[csf('rack_no')]."**".$row[csf('self_no')].",";
	}
	
	//print_r($rack_shelf_array);die; $cumilite_issue
	
	//$cumilite_issue_sql=sql_select("select prod_id, sum(quantity) as issue_qnty from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(25,49,112) and trans_type in (2,3,6) and po_breakdown_id in ($data[0]) group by  prod_id","prod_id","issue_qnty");
	
	$cumilite_issue_sql=sql_select("select b.prod_id, sum(case when b.trans_type in (2,3,6) then  b.quantity else 0 end) as issue_qnty, sum(case when b.trans_type in (4,5) then  b.quantity else 0 end) as issue_rtn_qnty, sum(case when c.transaction_type in (2,3,6) then  c.cons_quantity else 0 end) as issue_cons_qnty, sum(case when c.transaction_type in (4,5) then  c.cons_quantity else 0 end) as issue_rtn_cons_qnty  from order_wise_pro_details b, inv_transaction c  
	where b.trans_id=c.id and b.status_active=1 and b.is_deleted=0 and b.entry_form in(25,49,73,78,112) and b.trans_type in (2,3,4,5,6) and c.transaction_type in (2,3,4,5,6) and b.po_breakdown_id in ($data[0]) and c.store_id=$store_id
	group by  b.prod_id");
	$cumilite_issue_data=array();
	foreach($cumilite_issue_sql as $row)
	{
		$cumilite_issue_data[$row[csf("prod_id")]]["issue_qnty"]=$row[csf("issue_qnty")]-$row[csf("issue_rtn_qnty")];
		$cumilite_issue_data[$row[csf("prod_id")]]["issue_cons_qnty"]=$row[csf("issue_cons_qnty")]-$row[csf("issue_rtn_cons_qnty")];
	}
	
	
	
	//$cu_issue_sql="select prod_id, sum(quantity) as issue_qnty from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(25,49,112) and trans_type in (2,3,6) group by  prod_id";
	
	
	
	$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
	
  	//$sql = "select a.id, a.item_group_id, a.product_name_details, a.brand_supplier, a.color, a.gmts_size, a.item_color, a.item_size, a.current_stock, sum(b.quantity) as recv_qty from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and a.item_category_id='4' and a.entry_form=24 and b.trans_type=1 and b.po_breakdown_id in ($data) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.current_stock>0 group by a.id, a.item_group_id, a.product_name_details, a.brand_supplier, a.color, a.gmts_size, a.current_stock, a.item_color, a.item_size";// old
	//$sql = "select sum(case when b.trans_type=1 and b.entry_form=24 then b.quantity end) as recv_qntyy,sum(case when b.trans_type=2 and b.entry_form=25 then b.quantity end) as issue_qnty, a.id, a.item_group_id, a.product_name_details, a.brand_supplier, a.color, a.gmts_size, a.item_color, a.item_size, a.current_stock, sum(b.quantity) as recv_qty from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and a.item_category_id='4' and a.entry_form=24 and b.trans_type in(1,2) and b.po_breakdown_id in ($data[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.current_stock>0 and b.entry_form in(24,25) group by a.id, a.item_group_id, a.product_name_details, a.brand_supplier, a.color, a.gmts_size, a.current_stock, a.item_color, a.item_size";
	
	
	
	/*$sql = "select a.id, a.item_group_id, a.product_name_details, a.brand_supplier, a.color, a.gmts_size, a.item_color, a.item_size, a.current_stock, sum(b.quantity) as recv_qty, sum(c.cons_quantity) as recv_cons_qty 
	from product_details_master a, order_wise_pro_details b, inv_transaction c
	where a.id=b.prod_id and b.trans_id=c.id and a.item_category_id='4' and a.entry_form=24 and b.trans_type=1 and b.po_breakdown_id in ($data[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.current_stock>0 
	group by a.id, a.item_group_id, a.product_name_details, a.brand_supplier, a.color, a.gmts_size, a.current_stock, a.item_color, a.item_size";*/
	
	$sql = "select a.id, a.item_group_id, a.product_name_details, a.brand_supplier, a.color, a.gmts_size, a.item_color, a.item_size, a.current_stock, sum(b.quantity) as recv_qty, sum(c.cons_quantity) as recv_cons_qty 
	from product_details_master a, order_wise_pro_details b, inv_transaction c
	where a.id=b.prod_id and b.trans_id=c.id and a.item_category_id='4' and a.entry_form=24 and b.trans_type in(1,5) and b.entry_form in(24,78,112) and b.po_breakdown_id in ($data[0]) and c.store_id=$store_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.current_stock>0 
	group by a.id, a.item_group_id, a.product_name_details, a.brand_supplier, a.color, a.gmts_size, a.current_stock, a.item_color, a.item_size";
	
	//echo $sql;
	$result = sql_select($sql);
	//echo $sql;
	//echo $dataArrayy="select  sum(case when trans_type=1 and entry_form=24 then quantity end) as recv_qnty,sum(case when trans_type=2 and entry_form=25 then quantity end) as issue_qnty from order_wise_pro_details where po_breakdown_id in($po_id) and prod_id='$prod_id' and status_active=1 and is_deleted=0";

	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="590" class="rpt_table" id="table_header">
    	<thead>
			<th width="40">Prod. ID</th>
			<th width="70">Item Group</th>
			<th width="100">Item Desc.</th>               
			<th width="60">Item Color</th>
			<th width="50">Item Size</th>
			<th width="50">Rack</th>
            <th width="50">Shelf</th>
			<th width="60">Recv. Qty.</th>
            <th width="50">Cumulative Issue </th>
            <th>Issue Balance </th>
		</thead>
    </table>
		
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="590" class="rpt_table" id="tbl_list_search">  
		<?
		$i=1;
		foreach ($result as $row)
		{  
			$rack_shelf=explode(",",substr($rack_shelf_array[$row[csf('id')]],0,-1));
			
			foreach($rack_shelf as $value)
			{
				//print_r($rack_shelf);echo "jahid";
				$value=explode("**",$value);
				$rack=$value[0];
				$shelf=$value[1];
				
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$cu_issue=$balance=0;
				$current_stock=$row[csf('current_stock')];
				$cu_issue=$cumilite_issue_data[$row[csf('id')]]["issue_qnty"]*$trim_group_arr[$row[csf('item_group_id')]]['conversion_factor'];
				$cu_cons_issue=$cumilite_issue_data[$row[csf('id')]]["issue_cons_qnty"];
				if($cu_issue=="") $cu_issue=0;
				$receive_qnty=$row[csf('recv_qty')]*$trim_group_arr[$row[csf('item_group_id')]]['conversion_factor'];
				$balance=$receive_qnty-$cu_issue;
				$balance_cons=$row[csf('recv_cons_qty')]-$cu_cons_issue;
				//$current_stock=$current_stock_arr[$row[csf('id')]];
				
				//$data=$row[csf('id')]."**".$row[csf('item_group_id')]."**".$row[csf('product_name_details')]."**".$color_arr[$row[csf('item_color')]]."**".$row[csf('color')]."**".$row[csf('item_size')]."**".$row[csf('gmts_size')]."**".$row[csf('brand_supplier')]."**".$trim_group_arr[$row[csf('item_group_id')]]['uom']."**".$rack."**".$shelf."**".$row[csf('item_color')]."**".$current_stock."**".$cu_cons_issue."**".$balance_cons."**".$row[csf('recv_cons_qty')]."**".$trim_group_arr[$row[csf('item_group_id')]]['conversion_factor']; 
				
				$data=$row[csf('id')]."**".$row[csf('item_group_id')]."**".$row[csf('product_name_details')]."**".$color_arr[$row[csf('item_color')]]."**".$row[csf('color')]."**".$row[csf('item_size')]."**".$row[csf('gmts_size')]."**".$row[csf('brand_supplier')]."**".$trim_group_arr[$row[csf('item_group_id')]]['uom']."**".$rack."**".$shelf."**".$row[csf('item_color')]."**".$current_stock."**".$cu_issue."**".$balance."**".$receive_qnty."**".$trim_group_arr[$row[csf('item_group_id')]]['conversion_factor'];
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick='set_form_data("<? echo $data; ?>")' > 
					<td width="40"><? echo $row[csf('id')]; ?></td>
					<td width="70"><p><? echo $trim_group_arr[$row[csf('item_group_id')]]['name']; ?></p></td>  
					<td width="100"><p><? echo $row[csf('product_name_details')]; ?></p></td>             
					<td width="60"><p><? echo $color_arr[$row[csf('item_color')]]; ?>&nbsp;</p></td>
					<td width="50"><p><? echo $row[csf('item_size')]; ?>&nbsp;</p></td>
					<td width="50"><p><? echo $rack; ?>&nbsp;</p></td>
					<td width="50"><p><? echo $shelf; ?>&nbsp;</p></td>
					<td align="right" width="60"><? echo number_format($receive_qnty,2,'.',''); ?></td>
                    <td align="right" width="50"><p><? echo number_format($cu_issue,2,'.',''); ?>&nbsp;</p></td>
					<td align="right"><p><? echo number_format($balance,2,'.',''); ?>&nbsp;</p></td>
				</tr>
			<?
			$i++;
			}
		}
		?>
	</table>
<?	
exit();
}

if($action=="create_itemDesc_search_list_view_on_booking")
{
	$rack_shelf_array=array();
	$dataArray=sql_select("select a.prod_id, a.rack_no, a.self_no from inv_goods_placement a, inv_receive_master b where a.mst_id=b.id and a.entry_form=24 and b.entry_form=24 and b.booking_id=$data and b.booking_without_order=1 and b.receive_basis=2 and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.prod_id, a.rack_no, a.self_no");
	foreach($dataArray as $row)
	{
		$rack_shelf_array[$row[csf('prod_id')]].=$row[csf('rack_no')]."**".$row[csf('self_no')].",";
	}
	$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");

	$sql = "select a.id, a.item_group_id, a.product_name_details, a.brand_supplier, a.color, a.gmts_size, a.item_color, a.item_size, a.current_stock, sum(b.receive_qnty) as recv_qty from inv_receive_master r, product_details_master a, inv_trims_entry_dtls b where r.id=b.mst_id and a.id=b.prod_id and a.item_category_id=4 and a.entry_form=24 and r.entry_form=24 and r.booking_id=$data and r.booking_without_order=1 and r.receive_basis=2 and r.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.current_stock>0 group by a.id, a.item_group_id, a.product_name_details, a.brand_supplier, a.color, a.gmts_size, a.current_stock, a.item_color, a.item_size";
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="490" class="rpt_table" id="tbl_list_search">
		<thead>
			<th width="40">Prod. ID</th>
			<th width="70">Item Group</th>
			<th width="100">Item Desc.</th>               
			<th width="60">Item Color</th>
			<th width="50">Item Size</th>
			<th width="50">Rack</th>
            <th width="50">Shelf</th>
			<th>Recv. Qty.</th>
		</thead>
		<?
		$i=1;
		foreach ($result as $row)
		{  
			$rack_shelf=explode(",",substr($rack_shelf_array[$row[csf('id')]],0,-1));
			//print_r($rack_shelf);
			foreach($rack_shelf as $value)
			{
				$value=explode("**",$value);
				$rack=$value[0];
				$shelf=$value[1];
				
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
				$data=$row[csf('id')]."**".$row[csf('item_group_id')]."**".$row[csf('product_name_details')]."**".$color_arr[$row[csf('item_color')]]."**".$row[csf('color')]."**".$row[csf('item_size')]."**".$row[csf('gmts_size')]."**".$row[csf('brand_supplier')]."**".$trim_group_arr[$row[csf('item_group_id')]]['uom']."**".$rack."**".$shelf."**".$row[csf('item_color')]."**".$row[csf('current_stock')]; 
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick='set_form_data("<? echo $data ?>")' > 
					<td width="40"><? echo $row[csf('id')]; ?></td>
					<td width="70"><p><? echo $trim_group_arr[$row[csf('item_group_id')]]['name']; ?></p></td>  
					<td width="100"><p><? echo $row[csf('product_name_details')]; ?></p></td>             
					<td width="60"><p><? echo $color_arr[$row[csf('item_color')]]; ?>&nbsp;</p></td>
					<td width="50"><p><? echo $row[csf('item_size')]; ?>&nbsp;</p></td>
					<td width="50"><p><? echo $rack; ?>&nbsp;</p></td>
					<td width="50"><p><? echo $shelf; ?>&nbsp;</p></td>
					<td align="right"><? echo number_format($row[csf('recv_qty')],2,'.',''); ?></td>
				</tr>
			<?
			$i++;
			}
		}
		?>
	</table>
<?	
exit();
}

if ($action=="po_search_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);  

?> 
	<script> 
		
		function fn_show_check()
		{
			/*if( form_validation('cbo_buyer_name','Buyer Name')==false )
			{
				return;
			}*/			
			show_list_view ( $('#txt_search_common').val()+'_'+$('#cbo_search_by').val()+'_'+<? echo $cbo_company_id; ?>+'_'+$('#cbo_buyer_name').val()+'_'+'<? echo $all_po_id; ?>', 'create_po_search_list_view', 'search_div', 'trims_issue_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);hidden_field_reset();');
			set_all();
		}
		
		var selected_id = new Array(); var selected_name = new Array(); var buyer_id=''; var buyer_name='';
		
		function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				if($('#search'+i).css('display')!='none')
				{
					js_set_value( i );
				}
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function set_all()
		{
			var old=document.getElementById('txt_po_row_id').value; 
			if(old!="")
			{   
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{   
					js_set_value( old[k] ) 
				} 
			}
		}
		
		function js_set_value( str ) 
		{
			var color=document.getElementById('search' + str ).style.backgroundColor;
			var txt_buyer=$('#txt_buyer' + str).val();
			
			//if(color!='yellow' && selected_id.length>0 && $('#txt_buyer' + str).css('display') != 'none')
			if(color!='yellow' && selected_id.length>0 && $('#txt_buyer' + str).is(':visible'))
			{
				if(buyer_name=="")
				{
					buyer_name=txt_buyer;
				}
				else if(buyer_name*1!=txt_buyer*1)
				{
					alert("Buyer Mix Not Allowed");
					return;
				}
			}
			
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			
			buyer_id=$('#txt_buyer' + str).val();

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hidden_order_id').val(id);
			$('#hidden_order_no').val(name);
			$('#hide_buyer').val(buyer_id);
			
		}
		
		function hidden_field_reset()
		{
			$('#hidden_order_id').val('');
			$('#hidden_order_no').val( '' );
			$('#hide_buyer').val( '' );
			selected_id = new Array();
			selected_name = new Array();
		}
		
    </script>

</head>
<body>
	<div align="center">
        <form name="searchdescfrm" id="searchdescfrm" autocomplete=off>
            <fieldset style="width:780px;margin-left:5px">
                <input type="hidden" name="hidden_order_id" id="hidden_order_id" class="text_boxes" value="">
                <input type="hidden" name="hidden_order_no" id="hidden_order_no" class="text_boxes" value="">
                <input type="hidden" name="hide_buyer" id="hide_buyer" class="text_boxes" value="">
                <table cellpadding="0" cellspacing="0" width="630" class="rpt_table" border="1" rules="all">
                    <thead>
                        <th>Buyer</th>
                        <th>Search By</th>
                        <th>Search</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" name="po_id" id="po_id" value="">
                        </th> 
                    </thead>
                    <tr class="general">
                        <td align="center">
                            <?
                                echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $buyer_name, "","" ); 
                            ?>       
                        </td>
                        <td align="center">	
                            <?
                                $search_by_arr=array(1=>"PO No",2=>"Job No",3=>"Style Ref.");
                                echo create_drop_down( "cbo_search_by", 170, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                            ?>
                        </td>                 
                        <td align="center">				
                            <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                        </td> 						
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="fn_show_check()" style="width:100px;" />
                        </td>
                    </tr>
                </table>
            <div id="search_div" style="margin-top:10px"></div>
            </fieldset>
        </form>
    </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_po_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	
	if($search_by==1)
		$search_field='b.po_number';
	else if($search_by==2)
		$search_field='a.job_no';
	else
		$search_field='a.style_ref_no';	
		
	$company_id =$data[2];
	$buyer_id =$data[3];
	
	$all_po_id=$data[4];
	
	if($all_po_id!="")
		$po_id_cond=" or b.id in($all_po_id)";
	else 
		$po_id_cond="";
	
	$hidden_po_id=explode(",",$all_po_id);
	
	if(str_replace("'","",$buyer_id)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$buyer_id";
	}
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
	
	$sql = "select a.job_no_prefix_num, a.job_no, a.style_ref_no, a.buyer_name, a.order_uom, $year_field b.id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id and $search_field like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond"; 
	//echo $sql;
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="778" class="rpt_table" style="margin-left:2px">
            <thead>
                <th width="40">SL</th>
                <th width="110">Buyer</th>
                <th width="60">Year</th>
                <th width="70">Job No</th>
                <th width="110">Style Ref.</th>
                <th width="110">PO No</th>
                <th width="90">PO Quantity</th>
                <th width="60">UOM</th>
                <th>Shipment Date</th>
            </thead>
        </table>
        <div style="width:778px; overflow-y:scroll; max-height:240px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="760" class="rpt_table" id="tbl_list_search" >
            <?
				$i=1; $po_row_id='';
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
						
					if(in_array($selectResult[csf('id')],$hidden_po_id)) 
					{
						if($po_row_id=="") $po_row_id=$i; else $po_row_id.=",".$i;
					}
							
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
                        <td width="40" align="center">
                            <? echo $i; ?>
                            <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
                            <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $selectResult[csf('po_number')]; ?>"/>
                            <input type="hidden" name="txt_buyer" id="txt_buyer<?php echo $i ?>" value="<? echo $selectResult[csf('buyer_name')]; ?>"/>
                        </td>
                        <td width="110"><p><? echo $buyer_arr[$selectResult[csf('buyer_name')]]; ?></p></td>	
                        <td align="center" width="60"><p><? echo $selectResult[csf('year')]; ?></p></td>
                        <td width="70"><p><? echo $selectResult[csf('job_no_prefix_num')]; ?></p></td>
                        <td width="110"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
                        <td width="110"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                        <td width="90" align="right"><? echo $selectResult[csf('po_qnty_in_pcs')]; ?>&nbsp;</td> 
                        <td width="60" align="center"><p><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></p></td>
                        <td align="center"><? echo change_date_format($selectResult[csf('pub_shipment_date')]); ?></td>	
                    </tr>
                    <?
                    $i++;
				}
			?>
				<input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<? echo $po_row_id; ?>"/>
            </table>
        </div>
         <table width="620" cellspacing="0" cellpadding="0" style="border:none" align="center">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%"> 
                        <div style="width:50%; float:left" align="left">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                        </div>
                        <div style="width:50%; float:left" align="left">
                            <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
	</div>           
<?
	
exit();
}

if ($action=="booking_search_popup")
{
    echo load_html_head_contents("Sample Trims Booking Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);  

?> 
	<script> 

		function js_set_value( id, name, buyer_id ) 
		{
			$('#hidden_booking_id').val(id);
			$('#hidden_booking_no').val(name);
			$('#hide_buyer').val(buyer_id);
			parent.emailwindow.hide();
		}
    </script>

</head>
<body>
	<div align="center">
        <form name="searchdescfrm" id="searchdescfrm" autocomplete=off>
            <fieldset style="width:780px;margin-left:5px">
                <input type="hidden" name="hidden_booking_id" id="hidden_booking_id" class="text_boxes" value="">
                <input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes" value="">
                <input type="hidden" name="hide_buyer" id="hide_buyer" class="text_boxes" value="">
                <table cellpadding="0" cellspacing="0" width="760" class="rpt_table" border="1" rules="all">
                    <thead>
                        <th>Buyer</th>
                        <th>Booking No</th>
                        <th>Booking Date</th>
                        <th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" /></th> 
                    </thead>
                    <tr class="general">
                        <td align="center">
                            <?
                                echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $buyer_name, "","" ); 
                            ?>       
                        </td>
                      	<td align="center">				
                            <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                        </td>      
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px">To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
                        </td>            
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id; ?>, 'create_trims_booking_search_list_view', 'search_div', 'trims_issue_entry_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
            <div id="search_div" style="margin-top:10px"></div>
            </fieldset>
        </form>
    </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_trims_booking_search_list_view")
{
	$data = explode("_",$data);
	$buyer_id =$data[0];
	$search_string="%".trim($data[1]);
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];

	if(str_replace("'","",$buyer_id)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_id=$buyer_id";
	}
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and booking_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and booking_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	
	if($db_type==0) $year_field="YEAR(insert_date)"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY')";
	else $year_field="";//defined Later
	
	$sql = "select id, buyer_id, booking_no_prefix_num, booking_no, booking_date, delivery_date, currency_id, source, supplier_id, $year_field as year FROM wo_non_ord_samp_booking_mst WHERE company_id=$company_id and status_active =1 and is_deleted=0 and item_category=4 and booking_no like '$search_string' $buyer_id_cond $date_cond order by id";
	$result = sql_select($sql);
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="772" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="80">Booking No</th>
			<th width="60">Year</th>
			<th width="110">Buyer</th>
			<th width="90">Booking Date</th>               
			<th width="130">Supplier</th>
			<th width="90">Delivary date</th>
			<th width="80">Source</th>
			<th>Currency</th>
		</thead>
	</table>
	<div style="width:772px; max-height:250px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" id="list_view">  
		<?
			$i=1;
			foreach ($result as $row)
			{  
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('booking_no')]; ?>','<? echo $row[csf('buyer_id')]; ?>');"> 
					<td width="30"><? echo $i; ?></td>
					<td width="80"><p>&nbsp;&nbsp;<? echo $row[csf('booking_no_prefix_num')]; ?></p></td>
					<td width="60" align="center"><? echo $row[csf('year')]; ?></td>
					<td width="110"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
					<td width="90" align="center"><? echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</td>               
					<td width="130"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p></td>
					<td width="90" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</td>
					<td width="80"><p><? echo $source[$row[csf('source')]]; ?></p></td>
					<td><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
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

if($action=="po_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
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
				
				if(txt_prop_issue_qnty>0)
				{
					$("#tbl_list_search tbody").find('tr').each(function()
					{
						var txtPoQnty_placeholder=$(this).find('input[name="txtIssueQnty[]"]').attr('placeholder')*1;
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
						//alert(issue_qnty+"=="+txtPoQnty_placeholder);
						if(issue_qnty>txtPoQnty_placeholder)
						{
							$(this).find('input[name="txtIssueQnty[]"]').val("");
						}
						else
						{
							$(this).find('input[name="txtIssueQnty[]"]').val(issue_qnty.toFixed(2));
						}
						
	
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
		
		function fn_placeholde_check(i)
		{
			if($('#txtIssueQnty_'+i).val()*1>$('#txtIssueQnty_'+i).attr('placeholder')*1)
			{
				$('#txtIssueQnty_'+i).val("");
				alert("Issue Quantity Not Allow Over Balance Quantity");
				return;
			}
		}
		
		function fnc_close()
		{
			var save_string='';	 var tot_trims_qnty=''; var po_id_array = new Array(); var po_no='';
			var conversion_factor=$('#conversion_factor').val()*1;
			$("#tbl_list_search").find('tbody tr').each(function()
			{
				var txtPoId=$(this).find('input[name="txtPoId[]"]').val();
				var txtPoName=$(this).find('input[name="txtPoName[]"]').val();
				var txtIssueQnty=(($(this).find('input[name="txtIssueQnty[]"]').val()*1)/conversion_factor).toFixed(4);

				tot_trims_qnty=tot_trims_qnty*1+$(this).find('input[name="txtIssueQnty[]"]').val()*1;
				//alert($(this).find('input[name="txtIssueQnty[]"]').val()/conversion_factor);
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
            <input type="hidden" name="conversion_factor" id="conversion_factor" class="text_boxes" value="<? echo $conversion_factor; ?>">
            <div style="width:600px; margin-top:10px" align="center">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300" align="center">
                    <thead>
                        <th>Total Issue Qnty</th>
                        <th>Distribution Method</th>
                    </thead>
                    <tr class="general">
                        <td><input type="text" name="txt_prop_issue_qnty" id="txt_prop_issue_qnty" class="text_boxes_numeric" value="<? if($prev_method==1) echo $issueQnty; ?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)" /></td>
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
                        <th>Issue Qnty</th>
                    </thead>
                </table>
                <div style="width:550px; max-height:280px; overflow-y:scroll" id="list_container" align="left">
                    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="530" id="tbl_list_search">
                        <tbody>
                        <?
                        $i=1; $tot_issue_qnty=''; $po_qnty_array=array();
                        
                        $explSaveData = explode(",",$save_data); 	
                        for($z=0;$z<count($explSaveData);$z++)
                        {
                            $po_wise_data = explode("_",$explSaveData[$z]);
                            $order_id=$po_wise_data[0];
                            $issue_qnty=$po_wise_data[1]*$conversion_factor;
                            $po_qnty_array[$order_id]=number_format($issue_qnty,2,".","");
                        }
						
                        //print_r($po_array);die;
						//echo $all_po_id;die;
						
                        if($all_po_id!="")
                        {
							
							$all_po_idd=explode(",",$all_po_id);
							$all_po_idd="'".implode("','", $all_po_idd)."'";
							//echo $all_po_idd;
							$conversion_fac=return_field_value("b.conversion_factor as conversion_factor","product_details_master a, lib_item_group b "," a.item_group_id=b.id and a.id=$prod_id","conversion_factor");
							$check_rec_po=sql_select("select b.po_breakdown_id, sum((case when b.trans_type in(1,4,5) then b.quantity else 0 end)-(case when b.trans_type in(2,3,6) then b.quantity else 0 end)) as balance  from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.store_id=$cbo_store_name and b.po_breakdown_id in ($all_po_idd) and b.prod_id=$prod_id and b.trans_type in(1,2,3,4,5,6) and b.entry_form in(24,25,49,73,78,112) group by b.po_breakdown_id");
							foreach ($check_rec_po as $row)
							{
								$po_array[$row[csf('po_breakdown_id')]]=$row[csf('balance')]*$conversion_fac;
							}
							
							//print_r($po_array);
							
                            $po_sql="select b.id, a.buyer_name, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date 
							from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details c 
							where a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and c.trans_type in(1,5) and c.entry_form in(24,78) and b.id in ($all_po_id) and c.status_active=1 and c.is_deleted=0 group by b.id, a.buyer_name, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date";
					    }
                       // echo "<pre>";print_r($po_array);
                        $nameArray=sql_select($po_sql);
                        foreach($nameArray as $row)
                        {
                            if ($i%2==0)
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                                
                            $issue_qnty=$po_qnty_array[$row[csf('id')]];
                            $tot_issue_qnty+=$issue_qnty;
							$po_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
							$tot_po_qnty+=$po_qnty_in_pcs;
							$order_idd=$po_array[$row[csf('id')]];
							//echo $order_idd;
							if($order_idd!="")
	                        {
								$bgcolorr="green";
							}
							else
							{
								$bgcolorr="red";
							}
							
							if($po_array[$row[csf('id')]]>0)
							{
								?>
                                <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                    <td width="150">
                                        <p style="color:<? echo $bgcolorr; ?>"><b><? echo $row[csf('po_number')]; ?></b></p>
                                        <input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
                                        <input type="hidden" name="txtPoName[]" id="txtPoName_<? echo $i; ?>" value="<? echo $row[csf('po_number')]; ?>">
                                    </td>
                                    <td align="center" width="100"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                                    <td width="120" align="right">
                                        <? echo $po_qnty_in_pcs; ?>&nbsp;
                                        <input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_qnty_in_pcs; ?>">
                                    </td>
                                    <td align="right">
                                        <input type="text" name="txtIssueQnty[]" id="txtIssueQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:140px" onKeyUp="calculate_total();" value="<? echo $issue_qnty; ?>" placeholder="<? echo number_format(($po_array[$row[csf('id')]]+$issue_qnty),2,".",""); ?>" onBlur="fn_placeholde_check(<? echo $i; ?>)">
                                    </td>
                                </tr>
								<?
                                $i++;
							}
                        }
                        ?>
                        	<input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes" value="<? echo $tot_po_qnty; ?>">
                        </tbody>
                        <tfoot class="tbl_bottom">
                            <td colspan="3">Total</td>
                            <td id="total_issue"><? echo $tot_issue_qnty; ?></td>
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

if ($action=="get_trim_cum_info")
{
	$data=explode("**",$data);
	$po_id=$data[0];
	$prod_id=$data[1];
	
	//$current_stock=return_field_value("(sum(case when transaction_type=1 then cons_quantity else 0 end) - sum(case when transaction_type=2 then cons_quantity else 0 end)) as current_stock","inv_transaction","status_active=1 and is_deleted=0 and prod_id=$prod_id and item_category=4","current_stock");
	//$current_stock=return_field_value("current_stock","product_details_master","id=$prod_id");
	$stockData=sql_select("select a.current_stock, b.conversion_factor from product_details_master a, lib_item_group b where a.item_group_id=b.id and a.id=$prod_id");
	$current_stock=$stockData[0][csf('current_stock')];
	$conversion_factor=$stockData[0][csf('conversion_factor')];
	if($conversion_factor<=0) $conversion_factor=1;
	
	$dataArray=sql_select("select sum(case when trans_type=1 and entry_form=24 then quantity end) as recv_qnty, sum(case when trans_type=2 and entry_form=25 then quantity end) as issue_qnty from order_wise_pro_details where po_breakdown_id in($po_id) and prod_id='$prod_id' and status_active=1 and is_deleted=0");
	
	$recv_qnty=$dataArray[0][csf('recv_qnty')]*$conversion_factor;
    $yet_to_issue = ($recv_qnty-($dataArray[0][csf('issue_qnty')]*$conversion_factor));
	
    echo "$('#txt_received_qnty').val(".number_format(($recv_qnty),2,".","").");\n";
    echo "$('#txt_cumulative_issued').val('".number_format(($dataArray[0][csf('issue_qnty')]*$conversion_factor),2,".","")."');\n";
    echo "$('#txt_yet_to_issue').val('".number_format(($yet_to_issue),2,".","")."');\n";
	echo "$('#txt_global_stock').val('".number_format(($current_stock),2,".","")."');\n";
	exit();
}

if ($action=="get_trim_cum_info_for_trims_booking")
{
	$data=explode("**",$data);
	$txt_booking_id=$data[0];
	$prod_id=$data[1];
	
	//$current_stock=return_field_value("current_stock","product_details_master","id=$prod_id");
	//$current_stock=return_field_value("(sum(case when transaction_type=1 then cons_quantity else 0 end) - sum(case when transaction_type=2 then cons_quantity else 0 end)) as current_stock","inv_transaction","status_active=1 and is_deleted=0 and prod_id=$prod_id and item_category=4","current_stock");
	
	$stockData=sql_select("select a.current_stock, b.conversion_factor from product_details_master a, lib_item_group b where a.item_group_id=b.id and a.id=$prod_id");
	$current_stock=$stockData[0][csf('current_stock')];
	$conversion_factor=$stockData[0][csf('conversion_factor')];
	if($conversion_factor<=0) $conversion_factor=1;

	$recv_qnty=return_field_value("sum(b.receive_qnty) as recv_qnty","inv_receive_master a, inv_trims_entry_dtls b","a.id=b.mst_id and a.receive_basis=2 and entry_form=24 and a.item_category=4 and a.booking_id=$txt_booking_id and a.booking_without_order=1 and b.prod_id=$prod_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","recv_qnty");
	$recv_qnty=$recv_qnty*$conversion_factor;
	
	$iss_qnty=return_field_value("sum(b.issue_qnty) as iss_qnty","inv_issue_master a, inv_trims_issue_dtls b","a.id=b.mst_id and a.issue_basis=2 and entry_form=25 and item_category=4 and booking_id=$txt_booking_id and b.prod_id=$prod_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","iss_qnty");
	
    $yet_to_issue = $recv_qnty-$iss_qnty;
	
    echo "$('#txt_received_qnty').val(".$recv_qnty.");\n";
    echo "$('#txt_cumulative_issued').val('".$iss_qnty."');\n";
    echo "$('#txt_yet_to_issue').val('".$yet_to_issue."');\n";
	echo "$('#txt_global_stock').val('".$current_stock."');\n";
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		//product master table information
		$avg_rate=$stock_qnty=$stock_value=0;
		$sql = sql_select("select avg_rate_per_unit,current_stock,stock_value from product_details_master where id=$hidden_prod_id");
		foreach($sql as $result)
		{
			$avg_rate = $result[csf("avg_rate_per_unit")];
			$stock_qnty = $result[csf("current_stock")];
			$stock_value = $result[csf("stock_value")];
		}
		
		//$stock_qnty=return_field_value("(sum(case when transaction_type=1 then cons_quantity else 0 end) - sum(case when transaction_type=2 then cons_quantity else 0 end)) as current_stock","inv_transaction","status_active=1 and is_deleted=0 and prod_id=$hidden_prod_id and item_category=4","current_stock");
		
		if(str_replace("'","",$txt_issue_qnty)>$stock_qnty)
		{
			echo "17**Issue Quantity Exceeds The Global Current Stock Quantity"; 
			die;			
		}
		
		$trims_issue_num=''; $trims_update_id='';
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'TIE', date("Y",time()), 5, "select issue_number_prefix, issue_number_prefix_num from inv_issue_master where company_id=$cbo_company_id and entry_form=25 and $year_cond=".date('Y',time())." order by id desc ", "issue_number_prefix", "issue_number_prefix_num"));
		 	
			$id=return_next_id( "id", "inv_issue_master", 1 ) ;
			$field_array="id, issue_number_prefix, issue_number_prefix_num,issue_number, issue_purpose, entry_form, item_category, company_id, issue_basis, booking_id, booking_no, issue_date, challan_no, store_id, knit_dye_source, knit_dye_company, location_id, remarks, inserted_by, insert_date";
			
			$data_array="(".$id.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$cbo_issue_purpose.",25,4,".$cbo_company_id.",".$cbo_basis.",".$txt_booking_id.",".$txt_booking_no.",".$txt_issue_date.",".$txt_issue_chal_no.",".$cbo_store_name.",".$cbo_sewing_source.",".$cbo_sewing_company.",".$cbo_location_name.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//echo "insert into inv_issue_master (".$field_array.") values ".$data_array;die;
			/*$rID=sql_insert("inv_issue_master",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0; */
			
			$trims_issue_num=$new_system_id[0];
			$trims_update_id=$id;
		}
		else
		{
			$field_array_update="issue_purpose*issue_basis*booking_id*booking_no*issue_date*challan_no*store_id*knit_dye_source*knit_dye_company*location_id*remarks*updated_by*update_date";
			$data_array_update=$cbo_issue_purpose."*".$cbo_basis."*".$txt_booking_id."*".$txt_booking_no."*".$txt_issue_date."*".$txt_issue_chal_no."*".$cbo_store_name."*".$cbo_sewing_source."*".$cbo_sewing_company."*".$cbo_location_name."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			/*$rID=sql_update("inv_issue_master",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; */
			
			$trims_issue_num=str_replace("'","",$txt_system_id);
			$trims_update_id=str_replace("'","",$update_id);
		}
		
		$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$txt_issue_qnty=str_replace("'","",$txt_issue_qnty);
		$issue_stock_value = $avg_rate*str_replace("'","",$txt_issue_qnty);
		
		$field_array_trans="id, mst_id, company_id, receive_basis, pi_wo_batch_no, prod_id, item_category, transaction_type, transaction_date, cons_uom, cons_quantity, cons_rate, cons_amount, issue_challan_no, store_id, inserted_by, insert_date";
		
		$data_array_trans="(".$id_trans.",".$trims_update_id.",".$cbo_company_id.",".$cbo_basis.",".$txt_booking_id.",".$hidden_prod_id.",4,2,".$txt_issue_date.",".$cbo_uom.",".$txt_issue_qnty.",".$avg_rate.",".$issue_stock_value.",".$txt_issue_chal_no.",".$cbo_store_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		//echo "insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		/*$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}*/ 
		
		$id_dtls=return_next_id( "id", "inv_trims_issue_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, trans_id, prod_id, item_group_id, item_description, brand_supplier, rack_no, shelf_no, uom, issue_qnty, rate, amount, order_id, gmts_color_id, gmts_size_id, item_color_id, item_size, save_string, inserted_by, insert_date,floor_id,sewing_line";
		
		$data_array_dtls="(".$id_dtls.",".$trims_update_id.",".$id_trans.",".$hidden_prod_id.",".$cbo_item_group.",".$txt_item_description.",".$txt_brand_supref.",".$txt_rack.",".$txt_shelf.",".$cbo_uom.",".$txt_issue_qnty.",".$avg_rate.",".$issue_stock_value.",".$all_po_id.",".$gmts_color_id.",".$gmts_size_id.",".$txt_item_color_id.",".$txt_item_size.",".$save_data.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_floor.",".$cbo_sewing_line.")";
		
		//echo "insert into inv_finish_fabric_issue_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		/*$rID3=sql_insert("inv_trims_issue_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		}*/ 
		
		//product master table data UPDATE START----------------------//
		$currentStock   = $stock_qnty-$txt_issue_qnty;
		$StockValue	 	= $currentStock*$avg_rate;
		$avgRate	 	= number_format($avg_rate,$dec_place[3],'.',''); 

		$field_array_prod= "avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*updated_by*update_date"; 
		$data_array_prod=$avgRate."*".$txt_issue_qnty."*".$currentStock."*".$StockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		//$prodUpdate= sql_update("product_details_master",$field_array_prod,$data_array_prod,"id",$hidden_prod_id,1);
		/*if($flag==1) 
		{
			if($prodUpdate) $flag=1; else $flag=0; 
		} */
		//------------------ product_details_master END--------------//
		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";
		$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		$save_data=explode(",",str_replace("'","",$save_data));
		for($i=0;$i<count($save_data);$i++)
		{
			$order_dtls=explode("_",$save_data[$i]);
			
			$order_id=$order_dtls[0];
			$issue_qnty=$order_dtls[1];
			$conversion_fac=return_field_value("b.conversion_factor as conversion_factor","product_details_master a, lib_item_group b "," a.item_group_id=b.id and a.id=$hidden_prod_id","conversion_factor");
			$trim_stock=0;
			$sql_trim = sql_select("select sum((case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5)  then b.quantity else 0 end)-(case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6)  then b.quantity else 0 end)) as balance 
		from order_wise_pro_details b, inv_transaction c
		where  b.trans_id=c.id and c.item_category=4 and c.company_id=$cbo_company_id and c.store_id =$cbo_store_name and b.po_breakdown_id =$order_id  and c.status_active=1  and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		
			$trim_stock=$sql_trim[0][csf("balance")]*$conversion_fac;
			
			if(str_replace("'","",$issue_qnty)>$trim_stock)
			{
				echo "11**Transfer Quantity Not Allow Over Order Stock.";
				disconnect($con);
				die;
			}
			
			if($i==0) $add_comma=""; else $add_comma=",";
			$data_array_prop.="$add_comma(".$id_prop.",".$id_trans.",2,25,".$id_dtls.",".$order_id.",".$hidden_prod_id.",".$issue_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id_prop = $id_prop+1;
			$all_order_id.=$order_id.",";
		}
		
		$all_order_id=chop($all_order_id,",");
		
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;	
		/*if($data_array_prop!="")
		{
			$rID6=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			if($flag==1) 
			{
				if($rID6) $flag=1; else $flag=0; 
			} 
		}*/
		
		//LIFO/FIFO Start-----------------------------------------------//
		$isLIFOfifo=return_field_value("store_method","variable_settings_inventory","company_name=$cbo_company_id and variable_list=17 and item_category_id=4 and status_active=1 and is_deleted=0");
		if($isLIFOfifo==2) $cond_lifofifo=" DESC"; else $cond_lifofifo=" ASC";
		
		$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
		$update_array = "balance_qnty*balance_amount*updated_by*update_date";
		$updateID_array=array();
		$update_data=array();
		$issue_qnty=$txt_issue_qnty;
		$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);
		
		$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_prod_id and balance_qnty>0 and transaction_type in (1,4,5) and item_category=4 order by transaction_date,id $cond_lifofifo");
		foreach($sql as $result)
		{				
			$recv_trans_id = $result[csf("id")]; // this row will be updated
			$balance_qnty = $result[csf("balance_qnty")];
			$balance_amount = $result[csf("balance_amount")];
			$cons_rate = $result[csf("cons_rate")];
			$issueQntyBalance = $balance_qnty-$issue_qnty; // minus issue qnty
			$issueStockBalance = $balance_amount-($issue_qnty*$cons_rate);
			if($issueQntyBalance>=0)
			{					
				$amount = $issue_qnty*$cons_rate;
				//for insert
				if($data_array_mrr!="") $data_array_mrr .= ",";  
				$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",25,".$hidden_prod_id.",".$issue_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
				//for update
				$updateID_array[]=$recv_trans_id; 
				$update_data[$recv_trans_id]=explode("*",("".$issueQntyBalance."*".$issueStockBalance."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				break;
			}
			else if($issueQntyBalance<0)
			{
				$issueQntyBalance = $issue_qnty-$balance_qnty;				
				$issue_qnty = $balance_qnty;				
				$amount = $issue_qnty*$cons_rate;
				
				//for insert
				if($data_array_mrr!="") $data_array_mrr .= ",";  
				$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",25,".$hidden_prod_id.",".$balance_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
				//for update
				$updateID_array[]=$recv_trans_id; 
				$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				$issue_qnty = $issueQntyBalance;
			}
			$mrrWiseIsID++;
		}//end foreach
		
		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_issue_master",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("inv_issue_master",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; 
		}
		
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		$rID3=sql_insert("inv_trims_issue_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		$prodUpdate= sql_update("product_details_master",$field_array_prod,$data_array_prod,"id",$hidden_prod_id,1);
		if($flag==1) 
		{
			if($prodUpdate) $flag=1; else $flag=0; 
		} 
		
		if(str_replace("'","",$cbo_basis)==1)
		{
			if($data_array_prop!="")
			{
				$rID6=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
				if($flag==1) 
				{
					if($rID6) $flag=1; else $flag=0; 
				} 
			}
		}
		
		if($data_array_mrr!="")
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
		}	
 		// LIFO/FIFO END-----------------------------------------------//
		
		//check_table_status( $_SESSION['menu_id'],0);
				
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$trims_update_id."**".$trims_issue_num."**0"."**".$all_order_id."**".str_replace("'","",$cbo_store_name);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**"."&nbsp;"."**0"."**0";
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "0**".$trims_update_id."**".$trims_issue_num."**0"."**".$all_order_id."**".str_replace("'","",$cbo_store_name);
			}
			else
			{
				oci_rollback($con);
				echo "5**0**"."&nbsp;"."**0"."**0";
			}
		}
		
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
		
		$field_array_update="issue_purpose*issue_basis*booking_id*booking_no*issue_date*challan_no*store_id*knit_dye_source*knit_dye_company*location_id*remarks*updated_by*update_date";
		$data_array_update=$cbo_issue_purpose."*".$cbo_basis."*".$txt_booking_id."*".$txt_booking_no."*".$txt_issue_date."*".$txt_issue_chal_no."*".$cbo_store_name."*".$cbo_sewing_source."*".$cbo_sewing_company."*".$cbo_location_name."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		/*$rID=sql_update("inv_issue_master",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0; */
		
		/*$stock=sql_select("select current_stock, avg_rate_per_unit from product_details_master where id=$previous_prod_id");
		$adjust_curr_stock=$stock[0][csf('current_stock')]+str_replace("'", '',$hidden_issue_qnty);
		$adjust_rate=$stock[0][csf('avg_rate_per_unit')];
		$adjust_value=$adjust_curr_stock*$adjust_rate;
		
		$adjust_prod=sql_update("product_details_master","current_stock*stock_value",$adjust_curr_stock."*".$adjust_value,"id",$previous_prod_id,0);
		if($flag==1) 
		{
			if($adjust_prod) $flag=1; else $flag=0;  
		} */
		
		//product master table information
		$sql = sql_select("select avg_rate_per_unit,current_stock,stock_value from product_details_master where id=$hidden_prod_id");
		$avg_rate=$stock_qnty=$stock_value=0;
		foreach($sql as $result)
		{
			$avg_rate = $result[csf("avg_rate_per_unit")];
			$stock_qnty = $result[csf("current_stock")];
			$stock_value = $result[csf("stock_value")];
		}
		
		//$stock_qnty=return_field_value("(sum(case when transaction_type=1 then cons_quantity else 0 end) - sum(case when transaction_type=2 then cons_quantity else 0 end)) as current_stock","inv_transaction","status_active=1 and is_deleted=0 and prod_id=$hidden_prod_id and item_category=4","current_stock");
		
		$txt_issue_qnty=str_replace("'","",$txt_issue_qnty);
		$issue_stock_value = $avg_rate*str_replace("'","",$txt_issue_qnty);
		
		$field_array_trans_update="receive_basis*pi_wo_batch_no*prod_id*transaction_date*cons_uom*cons_quantity*cons_rate*cons_amount*issue_challan_no*store_id*updated_by*update_date";
		$data_array_trans_update=$cbo_basis."*".$txt_booking_id."*".$hidden_prod_id."*".$txt_issue_date."*".$cbo_uom."*".$txt_issue_qnty."*".$avg_rate."*".$issue_stock_value."*".$txt_issue_chal_no."*".$cbo_store_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		/*$rID2=sql_update("inv_transaction",$field_array_trans_update,$data_array_trans_update,"id",$update_trans_id,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} */

		$field_array_dtls_update="trans_id*prod_id*item_group_id*item_description*brand_supplier*rack_no*shelf_no*uom*issue_qnty*rate*amount*order_id*gmts_color_id*gmts_size_id*item_color_id*item_size*save_string*floor_id*sewing_line*updated_by*update_date";
		
		$data_array_dtls_update=$update_trans_id."*".$hidden_prod_id."*".$cbo_item_group."*".$txt_item_description."*".$txt_brand_supref."*".$txt_rack."*".$txt_shelf."*".$cbo_uom."*".$txt_issue_qnty."*".$avg_rate."*".$issue_stock_value."*".$all_po_id."*".$gmts_color_id."*".$gmts_size_id."*".$txt_item_color_id."*".$txt_item_size."*".$save_data."*".$cbo_floor."*".$cbo_sewing_line."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
        /*$rID3=sql_update("inv_trims_issue_dtls",$field_array_dtls_update,$data_array_dtls_update,"id",$update_dtls_id,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} */
		
		
		
		if(str_replace("'","",$previous_prod_id)==str_replace("'","",$hidden_prod_id))
		{
			$currentStock   = $stock_qnty-$txt_issue_qnty+str_replace("'", '',$hidden_issue_qnty);
			$StockValue	 	= $currentStock*$avg_rate;
			$avgRate	 	= number_format($avg_rate,$dec_place[3],'.',''); 
			
			$latest_current_stock=$stock_qnty+str_replace("'", '',$hidden_issue_qnty);	
			
			$field_array_prod= "avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*updated_by*update_date"; 
			$data_array_prod=$avgRate."*".$txt_issue_qnty."*".$currentStock."*".$StockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			/*$prodUpdate= sql_update("product_details_master",$field_array_prod,$data_array_prod,"id",$hidden_prod_id,1);
			if($flag==1) 
			{
				if($prodUpdate) $flag=1; else $flag=0; 
			} */
		}
		else
		{
			//$adjust_stock_qnty=return_field_value("(sum(case when transaction_type=1 then cons_quantity else 0 end) - sum(case when transaction_type=2 then cons_quantity else 0 end)) as current_stock","inv_transaction","status_active=1 and is_deleted=0 and prod_id=$hidden_prod_id and item_category=4","current_stock");
			//$stock[0][csf('current_stock')];
			$stock=sql_select("select current_stock, avg_rate_per_unit from product_details_master where id=$previous_prod_id");
			$adjust_curr_stock=$stock[0][csf('current_stock')]+str_replace("'", '',$hidden_issue_qnty);
			$adjust_rate=$stock[0][csf('avg_rate_per_unit')];
			$adjust_value=$adjust_curr_stock*$adjust_rate;
			/*$adjust_prod=sql_update("product_details_master","current_stock*stock_value",$adjust_curr_stock."*".$adjust_value,"id",$previous_prod_id,0);
			if($flag==1) 
			{
				if($adjust_prod) $flag=1; else $flag=0;  
			} */
			//product master table data UPDATE START----------------------//
			$currentStock   = $stock_qnty-$txt_issue_qnty;
			$StockValue	 	= $currentStock*$avg_rate;
			$avgRate	 	= number_format($avg_rate,$dec_place[3],'.',''); 
			
			$latest_current_stock=$stock_qnty;
			
			$field_array_prod= "avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*updated_by*update_date"; 
			$data_array_prod=$avgRate."*".$txt_issue_qnty."*".$currentStock."*".$StockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			/*$prodUpdate= sql_update("product_details_master",$field_array_prod,$data_array_prod,"id",$hidden_prod_id,1);
			if($flag==1) 
			{
				if($prodUpdate) $flag=1; else $flag=0; 
			} */
		}
		
		if(str_replace("'","",$txt_issue_qnty)>$latest_current_stock)
		{
			echo "17**Issue Quantity Exceeds The Global Current Stock Quantity"; 
			die;			
		}
		
		//------------------ product_details_master END--------------//

		/*$delete_prop=execute_query( "delete from order_wise_pro_details where dtls_id=$update_dtls_id and trans_id=$update_trans_id and entry_form=25",0);
		if($flag==1) 
		{
			if($delete_prop) $flag=1; else $flag=0; 
		}*/
		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";
		$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		$save_data=explode(",",str_replace("'","",$save_data));
		$all_order_id="";
		for($i=0;$i<count($save_data);$i++)
		{
			$order_dtls=explode("_",$save_data[$i]);
			$order_id=$order_dtls[0];
			$issue_qnty=$order_dtls[1];
			
			
			$conversion_fac=return_field_value("b.conversion_factor as conversion_factor","product_details_master a, lib_item_group b "," a.item_group_id=b.id and a.id=$hidden_prod_id","conversion_factor");
			
			
			$trim_stock=0;
			$sql_trim = sql_select("select sum((case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5)  then b.quantity else 0 end)-(case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6)  then b.quantity else 0 end)) as balance 
		from order_wise_pro_details b, inv_transaction c
		where  b.trans_id=c.id and b.dtls_id<>$update_dtls_id and b.trans_id<>$update_trans_id and c.item_category=4 and c.company_id=$cbo_company_id and c.store_id =$cbo_store_name and b.po_breakdown_id =$order_id  and c.status_active=1  and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		
			$trim_stock=$sql_trim[0][csf("balance")]*$conversion_fac;
			
			if(str_replace("'","",$issue_qnty)>$trim_stock)
			{
				echo "11**Transfer Quantity Not Allow Over Order Stock.";
				disconnect($con);
				die;
			}
			
			if($i==0) $add_comma=""; else $add_comma=",";
			$data_array_prop.="$add_comma(".$id_prop.",".$update_trans_id.",2,25,".$update_dtls_id.",".$order_id.",".$hidden_prod_id.",".$issue_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id_prop = $id_prop+1;
			$all_order_id.=$order_id.",";
		}
		
		$all_order_id=chop($all_order_id,",");
		
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;	
		/*if($data_array_prop!="")
		{
			$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			} 
		}*/
		
		//transaction table Update START--------------------------//
		$trans_data_array=array();
		$update_array = "balance_qnty*balance_amount*updated_by*update_date";
		$sql = sql_select("select a.id,a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount, b.recv_trans_id from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_trans_id and b.entry_form=25 and a.item_category=4"); 
		$updateID_array = array();
		$update_data = array();
		foreach($sql as $result)
		{
			$adjBalance = $result[csf("balance_qnty")]+$result[csf("issue_qnty")];
			$adjAmount = $result[csf("balance_amount")]+$result[csf("amount")];
			$updateID_array[]=$result[csf("id")]; 
			$update_data[$result[csf("id")]]=explode("*",("".$adjBalance."*".$adjAmount."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
			
			$trans_data_array[$result[csf("id")]]['qnty']=$adjBalance;
			$trans_data_array[$result[csf("id")]]['amnt']=$adjAmount;
			$recv_trans_id.=$result[csf("recv_trans_id")].",";
		}
		
		$recv_trans_id=chop($recv_trans_id,",");

		/*if(count($updateID_array)>0)
		{
			$query=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
			if($flag==1) 
			{
				if($query) $flag=1; else $flag=0; 
			} 
			
			$query2 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_trans_id and entry_form=25");
			{
				if($query2) $flag=1; else $flag=0; 
			} 
		}*/
		//transaction table Update END----------------------------//
		
		//LIFO/FIFO Start-----------------------------------------------//
		$isLIFOfifo=return_field_value("store_method","variable_settings_inventory","company_name=$cbo_company_id and variable_list=17 and item_category_id=4 and status_active=1 and is_deleted=0");
		if($isLIFOfifo==2) $cond_lifofifo=" DESC"; else $cond_lifofifo=" ASC";
		
		$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
		$update_array = "balance_qnty*balance_amount*updated_by*update_date";
		$updateID_array=array();
		$update_data=array();
		$issueQnty = $txt_issue_qnty;
		$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);
		
		if(str_replace("'","",$previous_prod_id)==str_replace("'","",$hidden_prod_id)) $balance_cond=" and( balance_qnty>0 or id in($recv_trans_id))";
		else $balance_cond=" and balance_qnty>0";
		
		$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_prod_id and transaction_type in (1,4,5) $balance_cond  and item_category=4 order by transaction_date, id $cond_lifofifo");// and balance_qnty>0
		foreach($sql as $result)
		{				
			$recv_trans_id = $result[csf("id")]; // this row will be updated
			
			if($trans_data_array[$recv_trans_id]['qnty']=="")
			{
				$balance_qnty = $result[csf("balance_qnty")];
				$balance_amount = $result[csf("balance_amount")];
			}
			else
			{
				$balance_qnty = $trans_data_array[$recv_trans_id]['qnty'];
				$balance_amount = $trans_data_array[$recv_trans_id]['amnt'];
			}
			
			if($balance_qnty>0)
			{ 
				$cons_rate = $result[csf("cons_rate")];
				$issueQntyBalance = $balance_qnty-$issueQnty; // minus issue qnty
				$issueStockBalance = $balance_amount-($issueQnty*$cons_rate);
				
				if($issueQntyBalance>=0)
				{			
					$amount = $issueQnty*$cons_rate;
					//for insert
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$update_trans_id.",25,".$hidden_prod_id.",".$issueQnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					$updateID_array[]=$recv_trans_id; 
					$update_data[$recv_trans_id]=explode("*",("".$issueQntyBalance."*".$issueStockBalance."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					break;
				}
				else if($issueQntyBalance<0)
				{
					$issueQntyBalance = $issueQnty-$balance_qnty;	
					$amount = $issue_qnty*$cons_rate;
					
					//for insert
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$update_trans_id.",25,".$hidden_prod_id.",".$balance_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					$updateID_array[]=$recv_trans_id; 
					$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					$issueQnty = $issueQntyBalance;
				}
				$mrrWiseIsID++;
			}
		}//end foreach
		
		//echo "10**".bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array); die;
		
		$rID=sql_update("inv_issue_master",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0; 
		
		$rID2=sql_update("inv_transaction",$field_array_trans_update,$data_array_trans_update,"id",$update_trans_id,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		
		$rID3=sql_update("inv_trims_issue_dtls",$field_array_dtls_update,$data_array_dtls_update,"id",$update_dtls_id,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		
		if(str_replace("'","",$previous_prod_id)==str_replace("'","",$hidden_prod_id))
		{
			$prodUpdate= sql_update("product_details_master",$field_array_prod,$data_array_prod,"id",$hidden_prod_id,1);
			if($flag==1) 
			{
				if($prodUpdate) $flag=1; else $flag=0; 
			} 
		}
		else
		{
			$adjust_prod=sql_update("product_details_master","current_stock*stock_value",$adjust_curr_stock."*".$adjust_value,"id",$previous_prod_id,0);
			if($flag==1) 
			{
				if($adjust_prod) $flag=1; else $flag=0;  
			} 
			
			$prodUpdate= sql_update("product_details_master",$field_array_prod,$data_array_prod,"id",$hidden_prod_id,1);
			if($flag==1) 
			{
				if($prodUpdate) $flag=1; else $flag=0; 
			} 
		}
		$delete_prop=execute_query( "delete from order_wise_pro_details where dtls_id=$update_dtls_id and trans_id=$update_trans_id and entry_form=25",0);
		if($flag==1) 
		{
			if($delete_prop) $flag=1; else $flag=0; 
		}
		
		
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;	
		if(str_replace("'","",$cbo_basis)==1)
		{
			
			if($data_array_prop!="")
			{
				$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
				if($flag==1) 
				{
					if($rID4) $flag=1; else $flag=0; 
				} 
			}
		}
		
		if(count($updateID_array)>0)
		{
			$query=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
			if($flag==1) 
			{
				if($query) $flag=1; else $flag=0; 
			} 
			
			$query2 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_trans_id and entry_form=25");
			{
				if($query2) $flag=1; else $flag=0; 
			} 
		}
		
		if($data_array_mrr!="")
		{		
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
			if($flag==1) 
			{
				if($mrrWiseIssueID) $flag=1; else $flag=0; 
			} 
		}
		
		//transaction table stock update here------------------------//
		/*if(count($updateID_array)>0)
		{
			$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
			if($flag==1) 
			{
				if($upTrID) $flag=1; else $flag=0; 
			} 
		}*/
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**0"."**".$all_order_id."**".str_replace("'","",$cbo_store_name);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**0**1"."**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**0"."**".$all_order_id."**".str_replace("'","",$cbo_store_name);
			}
			else
			{
				oci_rollback($con);
				echo "6**0**0**1"."**0";
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="trims_issue_popup_search")
{
	echo load_html_head_contents("Trims Issue Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
	
		function js_set_value(id)
		{
			$('#hidden_issue_id').val(id);
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
                    <th id="search_by_td_up" width="180">Enter Issue ID No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                    	<input type="hidden" name="hidden_issue_id" id="hidden_issue_id" class="text_boxes" value="">  
                    </th> 
                </thead>
                <tr>
                    <td align="center">
                    	<?
							echo create_drop_down("cbo_store_name", 130, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$cbo_company_id' and b.category_type=4 and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- All store --", 0, "" );
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
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_store_name').value, 'create_trims_issue_search_list_view', 'search_div', 'trims_issue_entry_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
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
	
	if($store_id==0) $store_name=""; else $store_name="and store_id=$store_id";
	
	$trims_issue_basis=array(1=>"With Order",2=>"Without Order");
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and issue_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and issue_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	
	if(trim($data[0])!="")
	{
		if($search_by==1)	
			$search_field_cond="and issue_number_prefix_num='$data[0]'";
		else	
			$search_field_cond="and challan_no like '$search_string'";
	}
	else
	{
		$search_field_cond="";
	}
	
	if($db_type==0) $year_field="YEAR(insert_date)"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY')";
	else $year_field="";//defined Later
	
	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, supplier_id, company_location_id FROM user_passwd where id=$user_id");
	$cre_company_id = $userCredential[0][csf('company_id')];
	$cre_supplier_id = $userCredential[0][csf('supplier_id')];
	$cre_store_location_id = $userCredential[0][csf('store_location_id')];
	$cre_company_location_id = $userCredential[0][csf('company_location_id')];
	
	if ($cre_company_id !='') {
		$company_credential_cond = " and company_id in($cre_company_id)";
	}
	if ($cre_store_location_id !='') {
		$store_location_credential_cond = " and store_id in($cre_store_location_id)"; 
	}
	
	if ($cre_company_location_id !='') {
		$location_credential_cond = " and location_id in($cre_company_location_id)";
	}
	
	$sql = "select id, issue_number_prefix_num, $year_field as year, issue_number, challan_no, store_id, location_id, issue_date, booking_no, issue_basis from inv_issue_master where entry_form=25 and status_active=1 and is_deleted=0 and company_id=$company_id $store_name  $search_field_cond $date_cond $company_credential_cond $store_location_credential_cond $location_credential_cond order by id"; 
	//echo $sql;
	$store_arr = return_library_array("select id, store_name from lib_store_location","id","store_name");
	$location_arr = return_library_array("select id, location_name from lib_location","id","location_name");
	$arr=array(2=>$trims_issue_basis,5=>$store_arr,6=>$location_arr);
	
	echo create_list_view("list_view", "Issue ID,Year,Issue Basis,Booking No.,Challan No,Store,Location,Issue date", "70,60,80,110,80,100,110","770","240",0, $sql, "js_set_value", "id", "", 1, "0,0,issue_basis,0,0,store_id,location_id,0", $arr, "issue_number_prefix_num,year,issue_basis,booking_no,challan_no,store_id,location_id,issue_date", "",'','0,0,0,0,0,0,0,3');
	
	exit();
}

if($action=='populate_data_from_trims_issue')
{
	
	$data_array=sql_select("select id, company_id, issue_basis,issue_purpose, booking_id, booking_no, issue_number, challan_no, store_id, issue_date, knit_dye_source, knit_dye_company, location_id, remarks from inv_issue_master where id=$data");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("issue_number")]."';\n";
		echo "document.getElementById('cbo_issue_purpose').value 			= '".$row[csf("issue_purpose")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_basis').value 					= '".$row[csf("issue_basis")]."';\n";
		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		echo "enable_disable();\n";
		
		if($row[csf("issue_basis")]==2)
		{
			echo "show_list_view('".$row[csf("booking_id")]."','create_itemDesc_search_list_view_on_booking','list_fabric_desc_container','requires/trims_issue_entry_controller','setFilterGrid(\'tbl_list_search\',0);');\n";
		}
		
		echo "document.getElementById('cbo_sewing_source').value 			= '".$row[csf("knit_dye_source")]."';\n";
		
		echo "load_drop_down( 'requires/trims_issue_entry_controller', ".$row[csf("knit_dye_source")]."+'_'+".$row[csf("company_id")].", 'load_drop_down_sewing_com','sewing_com');\n";
		
		echo "document.getElementById('cbo_sewing_company').value 		    = '".$row[csf("knit_dye_company")]."';\n";
		echo "load_location();\n";
		echo "document.getElementById('cbo_location_name').value 			= '".$row[csf("location_id")]."';\n";

		echo "document.getElementById('txt_issue_date').value 				= '".change_date_format($row[csf("issue_date")])."';\n";
		echo "document.getElementById('txt_booking_no').value 				= '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('txt_booking_id').value 				= '".$row[csf("booking_id")]."';\n";
		echo "document.getElementById('txt_issue_chal_no').value 			= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("store_id")]."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('update_id').value 					= '".$row[csf("id")]."';\n";
		
		
		
		
		
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_trims_issue',1,1);\n";  
		exit();
	}
}

if($action=="show_trims_listview")
{
	$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
	$sql="select id, item_group_id, item_description, brand_supplier, issue_qnty, item_color_id, item_size, uom, order_id from inv_trims_issue_dtls where mst_id='$data' and status_active = '1' and is_deleted = '0'";
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="670" class="rpt_table">
		<thead>
			<th width="70">Item Group</th>
			<th width="120">Item Description</th>               
			<th width="70">Item Color</th>
			<th width="50">Item Size</th>
			<th width="70">Supp Ref</th>
			<th width="40">UOM</th>
            <th width="80">Issue Qnty</th>
            <th>Buyer Order</th>
		</thead>
	</table>
	<div style="width:687px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="670" class="rpt_table" id="tbl_list_search_dtls">  
		<?
			$i=1;
			foreach ($result as $row)
			{  
				if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
				
				$order_no="";
				if($row[csf("order_id")]!="")
				{
					if($db_type==0)
					{
						$order_no=return_field_value("group_concat(po_number) as po_no","wo_po_break_down","id in (".$row[csf("order_id")].")","po_no");	
					}
					else
					{
						$order_no=return_field_value("LISTAGG(po_number, ',') WITHIN GROUP (ORDER BY id) as po_no","wo_po_break_down","id in (".$row[csf("order_id")].")","po_no");		
					}
				}
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $row[csf('id')]; ?>,'populate_trims_details_form_data', 'requires/trims_issue_entry_controller');"> 
					<td width="70"><p><? echo $trim_group_arr[$row[csf('item_group_id')]]['name']; ?></p></td>  
					<td width="120"><p><? echo $row[csf('item_description')]; ?></p></td>             
					<td width="70"><p><? echo $color_arr[$row[csf('item_color_id')]]; ?></p></td>
					<td width="50"><p><? echo $row[csf('item_size')]; ?></p></td>
					<td width="70"><p><? echo $row[csf('brand_supplier')]; ?></p></td>
                    <td width="40"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
					<td align="right" width="80"><? echo number_format($row[csf('issue_qnty')],2); ?></td>
                    <td><p><? echo $order_no; ?>&nbsp;</p></td>
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
	$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
	$issue_mst=sql_select("select a.issue_basis, a.booking_id from inv_issue_master a, inv_trims_issue_dtls b where a.id=b.mst_id and b.id='$data'");
	$issue_basis=$issue_mst[0][csf('issue_basis')];
	$booking_id=$issue_mst[0][csf('booking_id')];
	$data_array=sql_select("select b.id, b.trans_id, b.prod_id, b.item_group_id, b.item_description, b.brand_supplier, b.rack_no, b.shelf_no, b.issue_qnty, b.gmts_color_id, b.gmts_size_id, b.uom, b.order_id, b.item_color_id, b.item_size,b.save_string,b.floor_id,b.sewing_line,a.location_id, a.store_id, a.knit_dye_source, a.knit_dye_company, a.issue_purpose from inv_trims_issue_dtls b,inv_issue_master a  where b.id='$data' and a.id=b.mst_id");
	foreach ($data_array as $row)
	{ 
		
		$conversion_fac=return_field_value("b.conversion_factor as conversion_factor","product_details_master a, lib_item_group b "," a.item_group_id=b.id and a.id=".$row[csf("prod_id")]."","conversion_factor");
		
		echo "load_drop_down( 'requires/trims_issue_entry_controller', '".$row[csf("location_id")]."__".$row[csf("knit_dye_company")]."__".$row[csf("knit_dye_source")]."__".$row[csf("issue_purpose")]."', 'load_drop_down_floor', 'floor_td');\n";
		echo "load_drop_down( 'requires/trims_issue_entry_controller', '".$row[csf("floor_id")]."', 'load_drop_down_sewing_line_floor', 'sewing_line_td' );\n";
		
		
		
		
		echo "document.getElementById('cbo_item_group').value 				= '".$row[csf("item_group_id")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("uom")]."';\n";
		echo "document.getElementById('txt_item_description').value 		= '".$row[csf("item_description")]."';\n";
		echo "document.getElementById('txt_issue_qnty').value 				= '".$row[csf("issue_qnty")]."';\n";
		echo "document.getElementById('hidden_issue_qnty').value 			= '".$row[csf("issue_qnty")]."';\n";
		echo "document.getElementById('txt_brand_supref').value 			= '".$row[csf("brand_supplier")]."';\n";
		echo "document.getElementById('txt_rack').value 					= '".$row[csf("rack_no")]."';\n";
		echo "document.getElementById('txt_shelf').value 					= '".$row[csf("shelf_no")]."';\n";
		echo "document.getElementById('txt_item_color').value 				= '".$color_arr[$row[csf("item_color_id")]]."';\n";
		echo "document.getElementById('txt_item_color_id').value 			= '".$row[csf("item_color_id")]."';\n";
		echo "document.getElementById('gmts_color_id').value 				= '".$row[csf("gmts_color_id")]."';\n";
		echo "document.getElementById('txt_item_size').value 				= '".$row[csf("item_size")]."';\n";
		echo "document.getElementById('gmts_size_id').value 				= '".$row[csf("gmts_size_id")]."';\n";
		echo "document.getElementById('all_po_id').value 					= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('hidden_prod_id').value 				= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('previous_prod_id').value 			= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('update_trans_id').value 				= '".$row[csf("trans_id")]."';\n";
		echo "document.getElementById('save_data').value 					= '".$row[csf("save_string")]."';\n";
		echo "document.getElementById('txt_conversion_faction').value 		= '".$conversion_fac."';\n";
		
		echo "document.getElementById('cbo_floor').value 					= '".$row[csf("floor_id")]."';\n";
		echo "document.getElementById('cbo_sewing_line').value 				= '".$row[csf("sewing_line")]."';\n";
		
		
		
		if($issue_basis==2)
		{
			$order_no="";
			$buyer_name=return_field_value("buyer_id","wo_non_ord_samp_booking_mst","id='$booking_id'");
			
			echo "get_php_form_data('".$booking_id."'+'**'+".$row[csf("prod_id")].",'get_trim_cum_info_for_trims_booking','requires/trims_issue_entry_controller')".";\n";
			//echo "show_list_view('".$booking_id."','create_itemDesc_search_list_view_on_booking','list_fabric_desc_container','requires/trims_issue_entry_controller','');\n";
		}
		else
		{
			if($db_type==0)
			{
				$order_data=sql_select("select group_concat(a.po_number) as po_no, group_concat(distinct(b.buyer_name)) as buyer_name from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.id in(".$row[csf("order_id")].")");
			}
			else
			{
				$order_data=sql_select("select LISTAGG(cast(a.po_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as po_no, LISTAGG(b.buyer_name, ',') WITHIN GROUP (ORDER BY b.id) as buyer_name from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.id in(".$row[csf("order_id")].")");
			}
			
			$order_no=implode(",",array_unique(explode(",",$order_data[0][csf('po_no')])));//$order_data[0][csf('po_no')];
			$buyer_name=implode(",",array_unique(explode(",",$order_data[0][csf('buyer_name')])));//$order_data[0][csf('buyer_name')];
			
			echo "get_php_form_data('".$row[csf("order_id")]."'+'**'+".$row[csf("prod_id")].", 'get_trim_cum_info', 'requires/trims_issue_entry_controller')".";\n";
			echo "show_list_view('".$row[csf("order_id")]."'+'**'+".$row[csf("prod_id")]."+'**'+".$row[csf("store_id")].", 'create_itemDesc_search_list_view','list_fabric_desc_container','requires/trims_issue_entry_controller','setFilterGrid(\'tbl_list_search\',0);');\n";
			//echo "setFilterGrid('tbl_list_search',0);\n";
		}
		
		echo "document.getElementById('cbo_buyer_name').value 				= '".$buyer_name."';\n";
		echo "document.getElementById('txt_buyer_order').value 				= '".$order_no."';\n";

		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_trims_issue',1,1);\n";  
		exit();
	}
}

if ($action=="goods_placement_popup")
{
	echo load_html_head_contents("Goods Placement Entry Form", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$dtls_data=sql_select("select item_group_id, item_description, issue_qnty, prod_id from inv_trims_issue_dtls where id=$update_dtls_id");
?> 
	<script>
		
		var permission='<? echo $permission; ?>';
		
		function fn_addRow( i )
		{ 
			var row_num=$('#txt_tot_row').val();
			row_num++;
			
			var clone= $("#tr_"+i).clone();
			clone.attr({
				id: "tr_" + row_num,
			});
			
			clone.find("input,select").each(function(){
				  
			$(this).attr({ 
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
			  'name': function(_, name) { return name },
			  'value': function(_, value) { return value }              
			});
			 
			}).end();
			
			$("#tr_"+i).after(clone);
			
			$('#txtSelfNo_'+row_num).val('');
			$('#txtBoxBinNo_'+row_num).val('');
			$('#txtCtnNo_'+row_num).val('');
			$('#txtCtnQnty_'+row_num).val('');
			
			$('#increase_'+row_num).removeAttr("value").attr("value","+");
			$('#decrease_'+row_num).removeAttr("value").attr("value","-");
			$('#increase_'+row_num).removeAttr("onclick").attr("onclick","fn_addRow("+row_num+");");
			$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
			
			$('#txt_tot_row').val(row_num);
		}
		
		function fn_deleteRow(rowNo) 
		{ 		
			var row_num=$('#tbl_list tbody tr').length;
			if(row_num!=1)
			{
				$("#tr_"+rowNo).remove();
			}
		}
		
		
		function fnc_goods_placement_entry(operation)
		{
			
			var dataString=""; var j=0;
			$("#tbl_list").find('tbody tr').each(function()
			{
				var txtRoomNo=$(this).find('select[name="txtRoomNo[]"]').val();
				var txtRackNo=$(this).find('select[name="txtRackNo[]"]').val();
				var txtSelfNo=$(this).find('select[name="txtSelfNo[]"]').val();
				var txtBoxBinNo=$(this).find('select[name="txtBoxBinNo[]"]').val();
				var txtCtnNo=$(this).find('select[name="txtCtnNo[]"]').val();
				var txtCtnQnty=$(this).find('input[name="txtCtnQnty[]"]').val();
				//alert(txtRackNo);
				if(!(txtRackNo==""))
				{
					j++;
					
					dataString+='&txtRoomNo_' + j + '=' + txtRoomNo + '&txtRackNo_' + j + '=' + txtRackNo + '&txtSelfNo_' + j + '=' + txtSelfNo + '&txtBoxBinNo_' + j + '=' + txtBoxBinNo + '&txtCtnNo_' + j + '=' + txtCtnNo + '&txtCtnQnty_' + j + '=' + txtCtnQnty;
				}
			});
			
			if(j==0)
			{
				alert("Please Insert At Least One Rack No.");
				return;	
			}
			
			var data="action=save_update_delete_goods_placement&operation="+operation+'&tot_row='+j+get_submitted_data_string('dtls_id',"../../../")+dataString;
			//alert(data);return;
			freeze_window(operation);
			
			http.open("POST","trims_issue_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fnc_goods_placement_entry_Reply_info;
		}
		
		function fnc_goods_placement_entry_Reply_info()
		{
			if(http.readyState == 4) 
			{
				//alert(http.responseText);release_freezing();return;
				var reponse=trim(http.responseText).split('**');	
					
				show_msg(reponse[0]);
				
				if((reponse[0]==0 || reponse[0]==1 || reponse[0]==2))
				{
					reset_form('goodsPlacement_1','','','','','dtls_id');
					load_dtls_part();
				}
				
				set_button_status(reponse[2], permission, 'fnc_goods_placement_entry',1);	
				release_freezing();	
			}
		}
		
		function load_dtls_part()
		{
			var list_view_goods_placement = return_global_ajax_value( <? echo $update_dtls_id; ?>+"**"+<? echo $dtls_data[0][csf('prod_id')]; ?>, 'load_php_dtls_form', '', 'trims_issue_entry_controller');

			if(list_view_goods_placement!='')
			{
				$("#tbl_list tbody tr").remove();
				$("#tbl_list tbody").append(list_view_goods_placement);
				
				var row_num=$("#tbl_list tbody tr").length;
				$('#txt_tot_row').val(row_num);
			}
		}
    </script>

</head>

<body>
<div align="center">
	<? echo load_freeze_divs ("../../../",$permission,1); ?>
	<form name="goodsPlacement_1" id="goodsPlacement_1">
		<fieldset style="width:580px;">
        	<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
                <thead>
                    <th width="160">Item Group</th>
                    <th width="200">Item Description</th>
                    <th>Issue Qnty</th>
                </thead>
                <tr bgcolor="#FFFFFF">
                    <td><p>&nbsp;<? echo $trim_group_arr[$dtls_data[0][csf('item_group_id')]]['name']; ?></p></td>
                    <td><p>&nbsp;<? echo $dtls_data[0][csf('item_description')]; ?></p></td>
                    <td align="right"><? echo number_format($dtls_data[0][csf('issue_qnty')],2); ?>&nbsp;</td>
                    <input type="hidden" name="dtls_id" id="dtls_id" class="text_boxes" value="<? echo $update_dtls_id; ?>">
                    <input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="">
                </tr>
            </table>
        </fieldset> 
        <fieldset style="width:770px; margin-top:10px">
            <legend>New Entry</legend>
            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="760" id="tbl_list">
            	<thead>
                    <th width="110">Room No</th>
                    <th width="110">Rack No</th>
                    <th width="110">Self No</th>
                    <th width="110">Box/Bin</th>
                    <th width="110">Ctn. No</th>
                    <th width="110">Ctn. Qnty</th>
                    <th></th>
                </thead>
                <tbody>
                </tbody>    
            </table>
			<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="760">
             	<tr>
                    <td colspan="7">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="7" align="center" class="button_container">
						<? 
							echo load_submit_buttons($permission, "fnc_goods_placement_entry", 0,0,"reset_form('goodsPlacement_1','','','','','txt_tot_row*dtls_id');$('#tbl_list tbody tr:not(:first)').remove();",1);
                        ?>
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px" />
                    </td>	  
                </tr>
			</table>
		</fieldset>
	</form>
</div>
</body>  
<script>
 	get_php_form_data(<? echo $update_dtls_id; ?>, "populate_data_goods_placement", "trims_issue_entry_controller" );
	load_dtls_part();
</script>	
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	
</html>
<?
}

if ($action=="save_update_delete_goods_placement")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="id, entry_form, mst_id, trans_id, dtls_id, prod_id, room_no, rack_no, self_no, box_bin_no, ctn_no, ctn_qnty, inserted_by, insert_date";
		
		$dtls_data=sql_select("select mst_id, trans_id, prod_id from inv_trims_issue_dtls where id=$dtls_id");
		
		$data_array='';
		
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$txtRoomNo="txtRoomNo_".$j;
			$txtRackNo="txtRackNo_".$j;
			$txtSelfNo="txtSelfNo_".$j;
			$txtBoxBinNo="txtBoxBinNo_".$j;
			$txtCtnNo="txtCtnNo_".$j;
			$txtCtnQnty="txtCtnQnty_".$j;
			
			if($id=="") $id=return_next_id( "id", "inv_goods_placement", 1 ) ; else $id = $id+1;
			
			if($data_array!="") $data_array.=",";
			
			$data_array.="(".$id.",25,".$dtls_data[0][csf('mst_id')].",".$dtls_data[0][csf('trans_id')].",".$dtls_id.",".$dtls_data[0][csf('prod_id')].",'".$$txtRoomNo."','".$$txtRackNo."','".$$txtSelfNo."','".$$txtBoxBinNo."','".$$txtCtnNo."','".$$txtCtnQnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		}
		
		if($data_array!="")
		{
			//echo $data_array;die;
			$rID=sql_insert("inv_goods_placement",$field_array,$data_array,1);
			if($rID) $flag=1; else $flag=0; 
		}
			
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'", '', $dtls_id)."**1";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "0**".str_replace("'", '', $dtls_id)."**1";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
			}
		}
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
		
		$field_array="id, entry_form, mst_id, trans_id, dtls_id, prod_id, room_no, rack_no, self_no, box_bin_no, ctn_no, ctn_qnty, inserted_by, insert_date";
		$dtls_data=sql_select("select mst_id, trans_id, prod_id from inv_trims_issue_dtls where id=$dtls_id");
		
		$data_array='';
		
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$txtRoomNo="txtRoomNo_".$j;
			$txtRackNo="txtRackNo_".$j;
			$txtSelfNo="txtSelfNo_".$j;
			$txtBoxBinNo="txtBoxBinNo_".$j;
			$txtCtnNo="txtCtnNo_".$j;
			$txtCtnQnty="txtCtnQnty_".$j;
			
			if($id=="") $id=return_next_id( "id", "inv_goods_placement", 1 ) ; else $id = $id+1;
			if($data_array!="") $data_array.=",";
			
			$data_array.="(".$id.",25,".$dtls_data[0][csf('mst_id')].",".$dtls_data[0][csf('trans_id')].",".$dtls_id.",".$dtls_data[0][csf('prod_id')].",'".$$txtRoomNo."','".$$txtRackNo."','".$$txtSelfNo."','".$$txtBoxBinNo."','".$$txtCtnNo."','".$$txtCtnQnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		}
		
		$delete=execute_query("delete from inv_goods_placement where dtls_id=$dtls_id and entry_form=25",0);
		if($delete) $flag=1; else $flag=0;
		
		if($data_array!="")
		{
			//echo $data_array;die;
			$rID=sql_insert("inv_goods_placement",$field_array,$data_array,1);
			if($flag==1)
			{
				if($rID) $flag=1; else $flag=0;
			} 
		}
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**1";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "1**".str_replace("'", '', $update_id)."**1";
			}
			else
			{
				oci_rollback($con);
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Update Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$delete=execute_query("delete from inv_goods_placement where dtls_id=$dtls_id and entry_form=25",0);
		if($db_type==0)
		{
			if($delete)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($delete)
			{
				oci_commit($con); 
				echo "1**".str_replace("'", '', $update_id)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="load_php_dtls_form")
{
	$data=explode("**",$data);
	$dtls_id=$data[0];
	$prod_id=$data[1];
	
	if($db_type==0)
	{
		$recv_dataArray=sql_select("select group_concat(distinct(room_no)) as room_no, group_concat(distinct(rack_no)) as rack_no, group_concat(distinct(self_no)) as self_no, group_concat(distinct(box_bin_no)) as box_bin_no, group_concat(distinct(ctn_no)) as ctn_no from inv_goods_placement where prod_id=$prod_id and entry_form=24 and status_active=1 and is_deleted=0");
	}
	else
	{
		$recv_dataArray=sql_select("select LISTAGG(room_no, ',') WITHIN GROUP (ORDER BY id) as room_no, LISTAGG(rack_no, ',') WITHIN GROUP (ORDER BY id) as rack_no, LISTAGG(self_no, ',') WITHIN GROUP (ORDER BY id) as self_no, LISTAGG(id, ',') WITHIN GROUP (ORDER BY box_bin_no) as box_bin_no, LISTAGG(ctn_no, ',') WITHIN GROUP (ORDER BY id) as ctn_no from inv_goods_placement where prod_id=$prod_id and entry_form=24 and status_active=1 and is_deleted=0");	
	}
	
	$room_no_arr=explode(",",$recv_dataArray[0][csf('room_no')]);
	$room_no_arr=array_combine($room_no_arr,$room_no_arr);
	
	$rack_no_arr=explode(",",$recv_dataArray[0][csf('rack_no')]);
	$rack_no_arr=array_combine($rack_no_arr,$rack_no_arr);
	
	$self_no_arr=explode(",",$recv_dataArray[0][csf('self_no')]);
	$self_no_arr=array_combine($self_no_arr,$self_no_arr);
	
	$box_bin_no_arr=explode(",",$recv_dataArray[0][csf('box_bin_no')]);
	$box_bin_no_arr=array_combine($box_bin_no_arr,$box_bin_no_arr);
	
	$ctn_no_arr=explode(",",$recv_dataArray[0][csf('ctn_no')]);
	$ctn_no_arr=array_combine($ctn_no_arr,$ctn_no_arr);

	$sql="select room_no, rack_no, self_no, box_bin_no, ctn_no, ctn_qnty from inv_goods_placement where dtls_id=$dtls_id and entry_form=25 and status_active=1 and is_deleted=0";
	$result=sql_select($sql);
	$count=count($result);
	
	if($count==0 ) // New Insert
	{
	?>
        <tr id="tr_1">
            <td>
            	<select class="combo_boxes" id="txtRoomNo_1" name="txtRoomNo[]" style="width:110px">
                    <option value="">-- Select --</option>
					<?
                        foreach($room_no_arr as $key=>$value)
                        {
                        ?>
                            <option value="<? echo $key; ?>"><? echo $value; ?></option>
                        <?	
                        }
                        // echo create_drop_down( "txtRoomNo_1", 110, $room_no_arr,"",1, "-- Select --", 0, "",'','','','','','','','txtRoomNo[]' );
                    ?>
                </select>
            </td>
            <td>
            	<select class="combo_boxes" id="txtRackNo_1" name="txtRackNo[]" style="width:110px">
                    <option value="">-- Select --</option>
					<?
                        foreach($rack_no_arr as $key=>$value)
                        {
                        ?>
                            <option value="<? echo $key; ?>"><? echo $value; ?></option>
                        <?	
                        }
                        //  echo create_drop_down( "txtRackNo_1", 110, $rack_no_arr,"",1, "-- Select --", 0, "",'','','','','','','','txtRackNo[]' );
                    ?>
                </select>
            </td>
            <td>
            	<select class="combo_boxes" id="txtSelfNo_1" name="txtSelfNo[]" style="width:110px">
                    <option value="">-- Select --</option>
					<?
                        foreach($self_no_arr as $key=>$value)
                        {
                        ?>
                            <option value="<? echo $key; ?>"><? echo $value; ?></option>
                        <?	
                        }
                        // echo create_drop_down( "txtSelfNo_1", 110, $self_no_arr,"",1, "-- Select --", 0, "",'','','','','','','','txtSelfNo[]' ); 
                    ?>
                </select>
            </td>
            <td>
            	<select class="combo_boxes" id="txtBoxBinNo_1" name="txtBoxBinNo[]" style="width:110px">
                    <option value="">-- Select --</option>
					<?
                        foreach($box_bin_no_arr as $key=>$value)
                        {
                        ?>
                            <option value="<? echo $key; ?>"><? echo $value; ?></option>
                        <?	
                        }
                        // echo create_drop_down( "txtBoxBinNo_1", 110, $box_bin_no_arr,"",1, "-- Select --", 0, "",'','','','','','','','txtBoxBinNo[]' );
                    ?>
                </select>
            </td>
            <td>
            	<select class="combo_boxes" id="txtCtnNo_1" name="txtCtnNo[]" style="width:110px">
                    <option value="">-- Select --</option>
					<?
                        foreach($ctn_no_arr as $key=>$value)
                        {
                        ?>
                            <option value="<? echo $key; ?>"><? echo $value; ?></option>
                        <?	
                        }
                        // create_drop_down( "txtCtnNo_1", 110, $ctn_no_arr,"",1, "-- Select --", 0, "",'','','','','','','','txtCtnNo[]' );
                    ?>
                </select>
            </td>
            <td>
                <input type="text" name="txtCtnQnty[]" id="txtCtnQnty_1" class="text_boxes_numeric" style="width:100px;"/>
            </td>
            <td>
                <input type="button" id="increase_1" name="increase[]" style="width:35px" class="formbuttonplasminus" value="+" onClick="fn_addRow(1)" />
                <input type="button" id="decrease_1" name="decrease[]" style="width:35px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/>
            </td>
        </tr>
    <?
	}
	else // From Update
	{
		$i=0;
		foreach($result as $row)
		{
			$i++;
		?>
			<tr id="tr_<? echo $i; ?>">
            	<td>
                    <select class="combo_boxes" id="txtRoomNo_<? echo $i; ?>" name="txtRoomNo[]" style="width:110px">
                        <option value="">-- Select --</option>
                        <?
                            foreach($room_no_arr as $key=>$value)
                            {
                            ?>
                                <option value=<? echo $key; if($row[csf('room_no')]==$key) { ?> selected <? } ?>><? echo $value; ?></option>
                            <?	
                            }
                        ?>
                    </select>
                </td>
                <td>
                    <select class="combo_boxes" id="txtRackNo_<? echo $i; ?>" name="txtRackNo[]" style="width:110px">
                        <option value="">-- Select --</option>
                        <?
                            foreach($rack_no_arr as $key=>$value)
                            {
                            ?>
                                <option value=<? echo $key; if($row[csf('rack_no')]==$key) { ?> selected <? } ?>><? echo $value; ?></option>
                            <?	
                            }
                        ?>
                    </select>
                </td>
                <td>
                    <select class="combo_boxes" id="txtSelfNo_<? echo $i; ?>" name="txtSelfNo[]" style="width:110px">
                        <option value="">-- Select --</option>
                        <?
                            foreach($self_no_arr as $key=>$value)
                            {
                            ?>
                                <option value=<? echo $key; if($row[csf('self_no')]==$key) { ?> selected <? } ?>><? echo $value; ?></option>
                            <?	
                            }
                        ?>
                    </select>
                </td>
                <td>
                    <select class="combo_boxes" id="txtBoxBinNo_<? echo $i; ?>" name="txtBoxBinNo[]" style="width:110px">
                        <option value="">-- Select --</option>
                        <?
                            foreach($box_bin_no_arr as $key=>$value)
                            {
                            ?>
                                <option value=<? echo $key; if($row[csf('box_bin_no')]==$key) { ?> selected <? } ?>><? echo $value; ?></option>
                            <?	
                            }
                        ?>
                    </select>
                </td>
                <td>
                    <select class="combo_boxes" id="txtCtnNo_<? echo $i; ?>" name="txtCtnNo[]" style="width:110px">
                        <option value="">-- Select --</option>
                        <?
                            foreach($ctn_no_arr as $key=>$value)
                            {
                            ?>
                                <option value=<? echo $key; if($row[csf('ctn_no')]==$key) { ?> selected <? } ?>><? echo $value; ?></option>
                            <?	
                            }
                        ?>
                    </select>
                </td>
                <td>
                    <input type="text" name="txtCtnQnty[]" id="txtCtnQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:100px;" value="<? echo $row[csf('ctn_qnty')]; ?>"/>
                </td>
                <td>
                    <input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:35px" class="formbuttonplasminus" value="+" onClick="fn_addRow(<? echo $i; ?>)" />
                    <input type="button" id="decrease_<? echo $i;?>" name="decrease[]" style="width:35px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);"/>
                </td>
            </tr>
		<?
		}		
	}
	
	exit();
}

if ($action=="populate_data_goods_placement")
{
	$result=sql_select("select id from inv_goods_placement where dtls_id=$data and entry_form=25 and status_active=1 and is_deleted=0");
	
	if(count($result)>0) $button_status=1; else $button_status=0;
	
	echo "set_button_status($button_status, '".$_SESSION['page_permission']."', 'fnc_goods_placement_entry',1,1);\n";  
	exit();
}

if ($action=="trims_issue_entry_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	
	$buyer_short=return_library_array( "select id,short_name from lib_buyer", "id","short_name"  );
	$store_library=return_library_array( "select id,store_name from lib_store_location", "id","store_name"  );
	$company_arr = return_library_array("select id, company_name from lib_company","id","company_name");
	$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
	
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor","id","floor_name");
	$sewing_line_arr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	
	$sql="select id, issue_number, issue_date, challan_no,issue_purpose, store_id, knit_dye_source, knit_dye_company, location_id, remarks from inv_issue_master where id='$data[1]' and status_active=1 and is_deleted=0 and entry_form=25 ";
	//echo $sql;
	$dataArray=sql_select($sql);
?>
<div style="width:930px;">
    <table width="900" cellspacing="0" align="right" border="0" style="margin-bottom:10px">
        <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_arr[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
            <td colspan="6" align="center">
				<?
					$address='';
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach($nameArray as $result)
					{ 
						$address=$result[csf('plot_no')];
						if($address=="") $address=$result[csf('road_no')]; else $address.=", ".$result[csf('road_no')];
						if($address=="") $address=$result[csf('block_no')]; else $address.=", ".$result[csf('block_no')];
						if($address=="") $address=$result[csf('city')]; else $address.=", ".$result[csf('city')];
                    }
					echo $address;
					$location='';
					if($dataArray[0][csf('knit_dye_source')]==1)
					{
						$caption="Location";
						$issueTo=$company_arr[$dataArray[0][csf('knit_dye_company')]];
						$location=return_field_value("location_name","lib_location","id='".$dataArray[0][csf('location_id')]."'");
					}
					else
					{
						$caption="Address";
						$supplierData=sql_select("select address_1, address_2, supplier_name from lib_supplier where id='".$dataArray[0][csf('knit_dye_company')]."'");
						$issueTo=$supplierData[0][csf('supplier_name')];
						$location=$supplierData[0][csf('address_1')];
						if($location=="") $location=$supplierData[0][csf('address_2')]; else $location.=", ".$supplierData[0][csf('address_2')];
					}
                ?> 
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u>Accessories Issue Challan</u></strong></center></td>
        </tr>
        <tr>
            <td width="90"><strong>Issue No:</strong></td> <td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
            <td width="90"><strong>Issue Date :</strong></td><td width="175px"><? echo  change_date_format($dataArray[0][csf('issue_date')]); ?></td>
            <td width="95"><strong>Issue Purpose :</strong></td><td width="175px"><? echo  $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Challan No :</strong></td><td width="175px" ><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Store Name :</strong></td> <td><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
            <td><strong>Source :</strong></td><td><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Issue To :</strong></td><td><? echo $issueTo; ?></td>
            <td><strong><? echo $caption; ?> :</strong></td><td colspan="3"><? echo $location; ?></td>
        </tr>
        <tr>
           <td colspan="2" id="barcode_img_id"></td><td><strong>Remarks :</strong></td><td colspan="3"><? echo $dataArray[0][csf('remarks')]; ?></td>
        </tr>
    </table>
    <br>
	<div style="width:100%;">
        <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="120" align="center">Item Group</th>
                <th width="140" align="center">Item Des.</th>
                <th width="70" align="center">Item Color</th>
                <th width="70" align="center">Job No.</th>
                <th width="70" align="center">Buyer</th>
                <th width="70" align="center">Style Ref.</th>
                <th width="120" align="center">Buyer Order</th>
                <th width="60" align="center">UOM </th>
                <th width="70" align="center">Item Size</th>
                <th width="80" align="center">Issue Qty</th>                
                <th width="70" align="center">Floor</th>
                <th width="80" align="center">Sewing Line</th>              
                <th width="70" align="center">Rack</th>
                <th width="70" align="center">Self</th>
            </thead>
			<?
                $i=1; 
                $mst_id=$dataArray[0][csf('id')];
                //$sql_dtls="select b.id, b.item_group_id, b.item_description, b.gmts_color_id, b.gmts_size_id, b.order_id, b.uom, b.issue_qnty, c.room_no, c.rack_no, c.self_no, c.box_bin_no, c.ctn_no, c.ctn_qnty from inv_trims_issue_dtls b left join inv_goods_placement c on b.id=c.dtls_id and c.entry_form=25 where b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'";
				 $sql_dtls="select id, item_group_id, item_description, item_color_id, item_size, order_id, uom, issue_qnty, rack_no, shelf_no,sewing_line,floor_id from inv_trims_issue_dtls where mst_id='$mst_id' and status_active='1' and is_deleted='0'";
                //echo $sql_dtls;
                $sql_result=sql_select($sql_dtls);
                
                foreach($sql_result as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        
                    $order_no=$row[csf('order_id')];
					if($db_type==0)
					{
                    	//$po_number = return_field_value("group_concat(po_number) as po_number","wo_po_break_down"," id in ($order_no)","po_number");
						$job_data=sql_select("select 
							group_concat(DISTINCT a.job_no) as job_no,
							group_concat(DISTINCT a.style_ref_no) as style_ref_no,
							group_concat(DISTINCT a.buyer_name) as buyer_name,
							group_concat(b.po_number) as po_number
						 from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($order_no) group by a.job_no,a.style_ref_no,a.buyer_name");
					
						$job_no=$job_data[0][csf('job_no')];
						$style_ref_no=$job_data[0][csf('style_ref_no')];
						$buyer=$job_data[0][csf('buyer_name')];
						$buyer_name='';
						foreach(explode(',',$buyer) as $buyer_id){
							if($buyer_name)$buyer_name=$buyer_short[$buyer_id].',';	else $buyer_name=$buyer_short[$buyer_id];
						}
						$po_no=$job_data[0][csf('po_no')];
					}
					else
					{
						//$po_number = return_field_value("LISTAGG(po_number, ',') WITHIN GROUP (ORDER BY id) as po_number","wo_po_break_down"," id in ($order_no)","po_number");
						$job_data=sql_select("select 
						LISTAGG(cast(a.job_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as job_no,
						LISTAGG(cast(a.style_ref_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as style_ref_no,
						LISTAGG(cast(a.buyer_name as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as buyer_name,
						LISTAGG(cast(b.po_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_no
						
						 from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($order_no) group by a.job_no,a.style_ref_no,a.buyer_name");
						$job_no=implode(',',array_unique(explode(',',$job_data[0][csf('job_no')])));
						$style_ref_no=implode(',',array_unique(explode(',',$job_data[0][csf('style_ref_no')])));
						$buyer=array_unique(explode(',',$job_data[0][csf('buyer_name')]));
						
						$buyer_name='';
						foreach($buyer as $buyer_id){
							if($buyer_name)$buyer_name=$buyer_short[$buyer_id].',';	else $buyer_name=$buyer_short[$buyer_id];
						}
						
						
						$po_no=implode(',',array_unique(explode(',',$job_data[0][csf('po_no')])));
					}
					
					
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i; ?></td>
                        <td><p><? echo $trim_group_arr[$row[csf('item_group_id')]]['name']; ?></p></td>
                        <td><p><? echo $row[csf('item_description')]; ?></p></td>
                        <td><p><? echo $color_arr[$row[csf('item_color_id')]]; ?></p></td>
                        <td><p><? echo $job_no; ?></p></td>
                        <td><p><? echo $buyer_name; ?></p></td>
                        <td><p><? echo $style_ref_no; ?></p></td>
                        <td><p><? echo $po_no; ?></p></td>
                        <td align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                        <td align="center"><? echo $row[csf('item_size')]; ?></td>
                        <td align="right"><? echo number_format($row[csf('issue_qnty')],2,'.',''); ?></td>
                        <td align="center"><? echo $floor_arr[$row[csf('floor_id')]]; ?></td>
                        <td align="right"><? echo $sewing_line_arr[$row[csf('sewing_line')]]; ?></td>                       
                        <td align="center"><p><? echo $row[csf('rack_no')]; ?></p></td>
                        <td align="center"><p><? echo $row[csf('shelf_no')]; ?></p></td>
                    </tr>
                <?
                    $i++;
                }
			?>
		   </table>
           <br>
           <?
          	 echo signature_table(36, $data[0], "900px");
		   ?>
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
//for Urmi
if ($action=="trims_issue_entry_print2")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$buyer_short=return_library_array( "select id,short_name from lib_buyer", "id","short_name"  );
	$store_library=return_library_array( "select id,store_name from lib_store_location", "id","store_name"  );
	$company_arr = return_library_array("select id, company_name from lib_company","id","company_name");
	$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
	$po_number_arr = return_library_array("select id, po_number from wo_po_break_down","id","po_number");
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor","id","floor_name");
	$sewing_line_arr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	
	$sql="select id, issue_number, issue_date, 	issue_basis, challan_no,issue_purpose, store_id, knit_dye_source, knit_dye_company, location_id, remarks from inv_issue_master where id='$data[1]' and status_active=1 and is_deleted=0 and entry_form=25 ";
	//echo $sql;
	
	$dataArray=sql_select($sql);
	$issue_basis=$dataArray[0][csf('issue_basis')];
?>
<div style="width:930px;">
    <table width="900" cellspacing="0" align="right" border="0" style="margin-bottom:10px">
        <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_arr[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
            <td colspan="6" align="center">
				<?
					$address='';
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach($nameArray as $result)
					{ 
						$address=$result[csf('plot_no')];
						if($address=="") $address=$result[csf('road_no')]; else $address.=", ".$result[csf('road_no')];
						if($address=="") $address=$result[csf('block_no')]; else $address.=", ".$result[csf('block_no')];
						if($address=="") $address=$result[csf('city')]; else $address.=", ".$result[csf('city')];
                    }
					echo $address;
					$location='';
					if($dataArray[0][csf('knit_dye_source')]==1)
					{
						$caption="Location";
						$issueTo=$company_arr[$dataArray[0][csf('knit_dye_company')]];
						$location=return_field_value("location_name","lib_location","id='".$dataArray[0][csf('location_id')]."'");
					}
					else
					{
						$caption="Address";
						$supplierData=sql_select("select address_1, address_2, supplier_name from lib_supplier where id='".$dataArray[0][csf('knit_dye_company')]."'");
						$issueTo=$supplierData[0][csf('supplier_name')];
						$location=$supplierData[0][csf('address_1')];
						if($location=="") $location=$supplierData[0][csf('address_2')]; else $location.=", ".$supplierData[0][csf('address_2')];
					}
                ?> 
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u>Accessories Issue Challan</u></strong></center></td>
        </tr>
        <tr>
            <td width="90"><strong>Issue No:</strong></td> <td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
            <td width="90"><strong>Issue Date :</strong></td><td width="175px"><? echo  change_date_format($dataArray[0][csf('issue_date')]); ?></td>
            <td width="95"><strong>Issue Purpose :</strong></td><td width="175px"><? echo  $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Challan No :</strong></td><td width="175px" ><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Store Name :</strong></td> <td><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
            <td><strong>Source :</strong></td><td><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Issue To :</strong></td><td><? echo $issueTo; ?></td>
            <td><strong><? echo $caption; ?> :</strong></td><td colspan="3"><? echo $location; ?></td>
        </tr>
        <tr>
           <td colspan="2" id="barcode_img_id"></td><td><strong>Remarks :</strong></td><td colspan="3"><? echo $dataArray[0][csf('remarks')]; ?></td>
        </tr>
    </table>
    <br>
	<div style="width:100%;">
        <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="120" align="center">Item Group</th>
                <th width="140" align="center">Item Des.</th>
                <th width="70" align="center">Item Color</th>
                <th width="70" align="center">Job No.</th>
                <th width="70" align="center">Buyer</th>
                <th width="70" align="center">Style Ref.</th>
                <th width="120" align="center">Buyer Order</th>
                <th width="60" align="center">UOM </th>
                <th width="70" align="center">Item Size</th>
                <th width="80" align="center">Issue Qty</th>
                <th width="70" align="center">Floor</th>
                <th width="80" align="center">Sewing Line</th>
                <th width="70" align="center">Rack</th>
                <th width="70" align="center">Self</th>
            </thead>
			<?
                $i=1; 
                $mst_id=$dataArray[0][csf('id')];
                //$sql_dtls="select b.id, b.item_group_id, b.item_description, b.gmts_color_id, b.gmts_size_id, b.order_id, b.uom, b.issue_qnty, c.room_no, c.rack_no, c.self_no, c.box_bin_no, c.ctn_no, c.ctn_qnty from inv_trims_issue_dtls b left join inv_goods_placement c on b.id=c.dtls_id and c.entry_form=25 where b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'";
				if($issue_basis==2)//Without Order
				{
				 $sql_dtls="select id, item_group_id, item_description, item_color_id, item_size, order_id, uom, issue_qnty, rack_no, shelf_no,sewing_line,floor_id from inv_trims_issue_dtls where mst_id='$mst_id' and status_active='1' and is_deleted='0'";
                //echo $sql_dtls;
                $sql_result=sql_select($sql_dtls);
				}
				else if($issue_basis==1)
				{
					$sql_dtls="select b.id, b.item_group_id, b.item_description, b.item_color_id, b.item_size, b.order_id, b.uom, c.po_breakdown_id as po_id,c.quantity as issue_qnty, b.rack_no, b.shelf_no,b.sewing_line,b.floor_id from inv_trims_issue_dtls b,order_wise_pro_details c where c.dtls_id=b.id and c.prod_id=b.prod_id and c.entry_form=25 and  b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'";
				 $sql_result=sql_select($sql_dtls);
				}
                
                foreach($sql_result as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        
                    $order_no=$row[csf('order_id')];
					if($db_type==0)
					{
                    	//$po_number = return_field_value("group_concat(po_number) as po_number","wo_po_break_down"," id in ($order_no)","po_number");
						$job_data=sql_select("select 
							group_concat(DISTINCT a.job_no) as job_no,
							group_concat(DISTINCT a.style_ref_no) as style_ref_no,
							group_concat(DISTINCT a.buyer_name) as buyer_name
							
						 from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($order_no) group by a.job_no,a.style_ref_no,a.buyer_name");
					
						$job_no=$job_data[0][csf('job_no')];
						$style_ref_no=$job_data[0][csf('style_ref_no')];
						$buyer=$job_data[0][csf('buyer_name')];
						$buyer_name='';
						foreach(explode(',',$buyer) as $buyer_id){
							if($buyer_name)$buyer_name=$buyer_short[$buyer_id].',';	else $buyer_name=$buyer_short[$buyer_id];
						}
						//$po_no=$job_data[0][csf('po_no')];
					}
					else
					{
						//$po_number = return_field_value("LISTAGG(po_number, ',') WITHIN GROUP (ORDER BY id) as po_number","wo_po_break_down"," id in ($order_no)","po_number");
						$job_data=sql_select("select 
						LISTAGG(cast(a.job_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as job_no,
						LISTAGG(cast(a.style_ref_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as style_ref_no,
						LISTAGG(cast(a.buyer_name as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as buyer_name
						
						
						 from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($order_no) group by a.job_no,a.style_ref_no,a.buyer_name");
						$job_no=implode(',',array_unique(explode(',',$job_data[0][csf('job_no')])));
						$style_ref_no=implode(',',array_unique(explode(',',$job_data[0][csf('style_ref_no')])));
						$buyer=array_unique(explode(',',$job_data[0][csf('buyer_name')]));
						
						$buyer_name='';
						foreach($buyer as $buyer_id){
							if($buyer_name)$buyer_name=$buyer_short[$buyer_id].',';	else $buyer_name=$buyer_short[$buyer_id];
						}
						
						
						//$po_no=implode(',',array_unique(explode(',',$job_data[0][csf('po_no')])));
					}
					
					
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i; ?></td>
                        <td><p><? echo $trim_group_arr[$row[csf('item_group_id')]]['name']; ?></p></td>
                        <td><p><? echo $row[csf('item_description')]; ?></p></td>
                        <td><p><? echo $color_arr[$row[csf('item_color_id')]]; ?></p></td>
                        <td><p><? echo $job_no; ?></p></td>
                        <td><p><? echo $buyer_name; ?></p></td>
                        <td><p><? echo $style_ref_no; ?></p></td>
                        <td><p><? echo $po_number_arr [$row[csf('po_id')]]; ?></p></td>
                        <td align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                        <td align="center"><? echo $row[csf('item_size')]; ?></td>
                        <td align="right"><? echo number_format($row[csf('issue_qnty')],2,'.',''); ?></td>
                        <td align="center"><? echo $floor_arr[$row[csf('floor_id')]]; ?></td>
               			<td align="right"><? echo $sewing_line_arr[$row[csf('sewing_line')]]; ?></td>
                        <td align="center"><p><? echo $row[csf('rack_no')]; ?></p></td>
                        <td align="center"><p><? echo $row[csf('shelf_no')]; ?></p></td>
                    </tr>
                <?
                    $i++;
                }
			?>
		   </table>
           <br>
           <?
          	 echo signature_table(36, $data[0], "900px");
		   ?>
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
