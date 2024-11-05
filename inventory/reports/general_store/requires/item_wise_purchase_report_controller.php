<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)"; 
}

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

// if ($action=="load_drop_down_store")
// {
// 	$data=explode('_',$data);
// 	//print_r ($data);  
// 	echo create_drop_down( "cbo_store_name", 120, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] and b.category_type=$data[1] order by a.store_name","id,store_name", 1, "-- Select --", 0, "",0 );  	 
// 	exit();
// }
if ($action=="load_drop_down_store2")
{
	$data=explode('_',$data);
	// print_r($data);die;
	echo create_drop_down( "cbo_store_name", 120, "select distinct a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] and b.category_type in ($data[1]) order by a.store_name","id,store_name", 1, "-- Select --", 0, "",0 );
	exit();
}

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 120, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' and b.category_type in(4,8,9,10,11,15,16,17,18,19,20,21,22,32,33,34,35,36,37,38,39,40,41,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,89,90,91,92,93,94,99) and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1,"--Select store--",0,"");
	exit();
}

if ($action=="load_drop_down_supplier")
{	  
	echo create_drop_down( "cbo_supplier_name", 120, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where  a.id=b.supplier_id and a.id=c.supplier_id and  c.tag_company=$data and b.party_type in (1,6,7,8,90,92) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", "", "" );  	 
	exit();
	
}

if($action=="com_wise_all_data")
{
	 
	 
	 $print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=6 and report_id=311 and is_deleted=0 and status_active=1");
	$print_report_format_arr=explode(",",$print_report_format);
	$js_print_report_format_arr= json_encode($print_report_format_arr);
	
	echo  $js_print_report_format_arr;
	exit();
}

if ($action=="item_account_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	$company_id = $data[0];
	$item_category_id = $data[1];
	$item_group_id = $data[2];
?>	
    <script>
	 var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	 
	 function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			//alert (tbl_row_count);
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				 eval($('#tr_'+i).attr("onclick"));  
			}
		}
		
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
	function js_set_value(id)
	{
		var str=id.split("_");
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		var strdt=str[2];
		str=str[1];
	
		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
			selected_name.push( strdt );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i,1 );
		}
		var id = '';
		var ddd='';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			ddd += selected_name[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		ddd = ddd.substr( 0, ddd.length - 1 );
		$('#item_account_id').val( id );
		$('#item_account_val').val( ddd );
	} 
		  
	</script>
     <input type="hidden" id="item_account_id" />
     <input type="hidden" id="item_account_val" />
 <?
	$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$sizeArr = return_library_array("select id,size_name from lib_size where status_active=1 and is_deleted=0","id","size_name");
	$item_group_cond = ($item_group_id != "") ? " and item_group_id in ($item_group_id)" : ""; 
	$sql="SELECT id,item_account,item_category_id,item_group_id,item_number,item_description,supplier_id,item_size,gmts_size from  product_details_master where item_category_id=$item_category_id $item_group_cond and company_id = $company_id and status_active=1 and is_deleted=0"; 
	//echo $sql;
	$arr=array(2=>$general_item_category,3=>$itemgroupArr,6=>$sizeArr,7=>$supplierArr);
	echo  create_list_view("list_view", "Product ID,Item Account,Item Category,Item Group,Item Number,Item Description,Size,Supplier", "70,70,110,150,100,150,100,50","880","450",0, $sql , "js_set_value", "id,item_description", "", 0, "0,0,item_category_id,item_group_id,0,0,item_size,supplier_id", $arr , "id,item_account,item_category_id,item_group_id,item_number,item_description,item_size,supplier_id", "",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0','',1);
	exit();
}

/*if ($action=="item_group_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
?>	
    <script>
		  function js_set_value(id)
		  { 
			  document.getElementById('item_name_id').value=id;
			  parent.emailwindow.hide();
		  }
	</script>
        <input type="hidden" id="item_name_id" />
    <?
	$sql="SELECT id,item_name from  lib_item_group where item_category=$data[1] and status_active=1 and is_deleted=0"; //id=$data[1] and
	
	echo  create_list_view("list_view", "Item Name", "350","500","330",0, $sql , "js_set_value", "id,item_name", "", 1, "0", $arr , "item_name", "item_wise_purchase_report_controller",'setFilterGrid("list_view",-1);','0') ;
	exit();
}*/


if($action=="item_group_popup")
{
  	echo load_html_head_contents("Item  Name Info","../../../../", 1, 1, '','1','');
	extract($_REQUEST);
	$data=explode('_',$data);
	$company_id = $data[0];
	$item_category_id = $data[1];
	$item_group_id = $data[2];
?>
	<script>
	
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});
		
		var selected_id = new Array(); var selected_name = new Array();
		
		function check_all_data() 
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
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
			var old=document.getElementById('txt_item_process_row_id').value; 
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
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hidden_item_process_id').val(id);
			$('#hidden_process_name').val(name);
		}
    </script>

</head>

<body>
<div align="center">
	<fieldset style="width:370px;margin-left:10px">
    	<input type="hidden" name="hidden_item_process_id" id="hidden_item_process_id" class="text_boxes" value="">
        <input type="hidden" name="hidden_process_name" id="hidden_process_name" class="text_boxes" value="">
        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
                <thead>
                    <th width="50">SL</th>
                    <th>Item Group Name</th>
                </thead>
            </table>
            <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
                <?
                    $i=1; $item_process_row_id='';  
					$hidden_item_group_id=explode(",",$item_group_id);
					$sql="SELECT id,item_name from  lib_item_group where item_category=$data[1] and status_active=1 and is_deleted=0";
					$item_group_arr = sql_select($sql);
                    foreach($item_group_arr as $row)
                    {
                            
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                        if(in_array($row[csf("id")],$hidden_item_group_id)) 
                        { 
                                if($item_process_row_id=="") $item_process_row_id=$i; else $item_process_row_id.=",".$i;
                        }

                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
                                <td width="50" align="center"><?php echo "$i"; ?>
                                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>	
                                    <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf("item_name")]; ?>"/>
                                </td>	
                                <td><p><? echo $row[csf("item_name")]; ?></p></td>
                        </tr>
                        <?
                        $i++;
                            


                    }
                ?>
                    <input type="hidden" name="txt_item_process_row_id" id="txt_item_process_row_id" value="<?php echo $item_process_row_id; ?>"/>
                </table>
            </div>
             <table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
        </form>
    </fieldset>
</div>    
</body>           
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_all();
</script>
</html>
<?
exit();
}

if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//echo $cbo_item_category_id.tst;die;
	$item_category_id="";
	if ($cbo_company_name==0) $company_id =""; else $company_id =" and a.company_id='$cbo_company_name'";
	if ($cbo_item_category_id !="") $item_category_id=" and c.item_category_id in($cbo_item_category_id)";
	if ($item_account_id==0) $item_code=""; else $item_code=" and b.prod_id in ($item_account_id)";
	if ($item_group_id==0) $group_id=""; else $group_id=" and c.item_group_id in ($item_group_id)";
	if ($cbo_supplier_name==0) $supplier_id=""; else $supplier_id=" and a.supplier_id='$cbo_supplier_name'";
	if ($cbo_store_name==0){ $store_id="";}else{$store_id=" and a.store_id='$cbo_store_name'";}
	//if ($item_group_id==0){ $group_id="";}else{$group_id=" and c.item_group_id='$item_group_id'";}
	if($db_type==2)
	{	
		if( $from_date==0 && $to_date==0 ) $transaction_date=""; else $transaction_date= " and b.transaction_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";	
	}
	if($db_type==0)
	{	
		if( $from_date==0 && $to_date==0 ) $transaction_date=""; else $transaction_date= " and b.transaction_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";	
	}
 	
 	
 	//library array-------------------
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name"); 
	$storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name"); 
	$itemgroupArr = return_library_array("select id,item_name from lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	$pi_numArr = return_library_array("select id, pi_number from com_pi_master_details where status_active=1 and is_deleted=0","id","pi_number");
	$wo_numArr = return_library_array("select id, wo_number from wo_non_order_info_mst where status_active=1 and is_deleted=0","id","wo_number");
	$req_arr = return_library_array("select id, requ_no from inv_purchase_requisition_mst where status_active=1 and is_deleted=0","id","requ_no");

	$sql = "select a.id, a.item_category, a.receive_basis, a.receive_date, a.currency_id, a.recv_number, a.supplier_id, a.booking_id, b.pi_wo_batch_no, b.transaction_date, b.cons_quantity, b.cons_uom, b.cons_rate, b.cons_amount, c.id as pro_id, c.item_description, c.item_category_id, c.item_group_id, a.store_id, c.item_size 
	from inv_receive_master a, inv_transaction b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and c.status_active=1 and c.is_deleted=0 $company_id $item_category_id $item_code $supplier_id $store_id $transaction_date $group_id 
	order by c.item_category_id, a.store_id, a.receive_date, a.recv_number ";
	//echo $sql;
	$result = sql_select($sql);	
	$r=1;
	ob_start();	
	?>
	<div style="width:800px;" id="scroll_body" > 
     <fieldset style="width:800px;">
        <table style="width:780px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" > 
            <tr class="form_caption" style="border:none;">
                <td colspan="10" align="center" style="border:none;font-size:20px; font-weight:bold" ><strong>Item Wise Purchase/Receive Details</strong></td> 
            </tr>
            <tr style="border:none;">
                <td colspan="10" align="center" style="border:none; font-size:17px;"><strong>
                    Company Name : <? echo $companyArr[str_replace("'","",$cbo_company_name)]; ?>  </strong>                              
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="10" align="center" style="border:none;font-size:12px; font-weight:bold">
                    <? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date)."   To : ".change_date_format($to_date)."" ;?>
                </td>
            </tr>
            <?
			 $all_data=array();
			
			foreach($result as $row)
			{
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['item_category_id']=$row[csf('item_category_id')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['item_group_id']=$row[csf('item_group_id')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['store_id']=$row[csf('store_id')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]][csf('pro_id')]=$row[csf('pro_id')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['transaction_date']=$row[csf('transaction_date')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['supplier_id']=$row[csf('supplier_id')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]][csf('cons_quantity')]=$row[csf('cons_quantity')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['cons_uom']=$row[csf('cons_uom')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['currency_id']=$row[csf('currency_id')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]][csf('cons_rate')]=$row[csf('cons_rate')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]][csf('cons_amount')]=$row[csf('cons_amount')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['booking_id']=$row[csf('booking_id')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]][csf('recv_number')]=$row[csf('recv_number')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]][csf('receive_basis')]=$row[csf('receive_basis')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['item_size']=$row[csf('item_size')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['item_description']=$row[csf('item_description')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['pi_wo_batch_no']=$row[csf('pi_wo_batch_no')];
				
				if(!in_array($row[csf('pro_id')],$data_a))
				{
					$data_a[$row[csf('pro_id')]]=$row[csf('pro_id')];
				}
			} 
			
			$k=1;
		 	$comp_total_qnty=0;
			$comp_total_amount=0;
			foreach($data_a as $val)
			{
				$item_data=$all_data[$val];
				$item_total_qnty=0;
				$item_total_amount=0;
				
				foreach($item_data as $store_id=>$store_data)
				{
					$total_qnty=0;
					$total_amount=0;
					foreach($store_data as $row_id=>$row_data)
					{
						
						if($new_itm[$store_data[$row_id]['item_description']]=="")
						{
							$new_itm[$store_data[$row_id]['item_description']]=$store_data[$row_id]['item_description'];
						?>
							<tr>
								<td colspan="4" style="font-size:14px"><b>Item Name :<? echo $store_data[$row_id]['item_description'].','.$store_data[$row_id]['item_size'];?></b></td>
								<td colspan="3" style="font-size:14px"><b>Item Category :<? echo $item_category[$store_data[$row_id]['item_category_id']]; ?></b></td>
								<td colspan="3" style="font-size:14px"><b>Item Group :<? echo $itemgroupArr[$store_data[$row_id]['item_group_id']]; ?></b></td>
							</tr>
						<?
						}
						if($new_arr[$store_data[$row_id]['item_description']][$store_id]=="")
						{
							$st=1;
							$new_arr[$store_data[$row_id]['item_description']][$store_id]= $store_id;
						?>
								<tr><td colspan="10" style="font-size:12px"><b>Store :<? echo $storeArr[$store_id]; ?></b></td></tr>
							</table>
							
							<table style="width:780px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_2" >
							 <thead>
									<th width="30" >SL</th>
									<th width="80" >Receive Date</th>
									<th width="100" >Supplier</th>
									<th width="80" >Quantity</th>
									<th width="70" >UoM</th>
									<th width="70" >Currency</th>
									<th width="70" >Rate</th>
									<th width="70" >Amount</th>
									<th width="70" >Basis Ref.</th>
									<th width="120" >MRIR</th>                    
								</thead></tr>
								<tbody>
							<?
						}
						if ($k%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						if($row_data["transaction_type"]==1) 
							$stylecolor='style="color:#A61000"';
						else
							$stylecolor='style="color:#000000"'; 								
						?>
                            <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                                <td align="center"><? echo $k; ?></td>
                                <td align="center"><? echo change_date_format($row_data['transaction_date']); ?></td>
                                <td align="center"><? echo $supplierArr[$row_data['supplier_id']]; ?></td>
                                <td align="right"><? echo $row_data[csf('cons_quantity')]; ?></td>
                                <td align="center"><? echo $unit_of_measurement[$row_data['cons_uom']]; ?></td>
                                <td align="center"><? echo $currency[$row_data['currency_id']]; ?></td>
                                <td align="right"><? echo number_format($row_data[csf('cons_rate')],2,'.',''); ?></td>
                                <td align="right"><? echo number_format($row_data[csf('cons_amount')],2,'.',''); ?></td>
								<? 
									$receive_basis=$row_data[csf('receive_basis')];
									if ($receive_basis==1)
									{
								?>
										<td><? echo $pi_numArr[$row_data['pi_wo_batch_no']]; ?></td>
								<?
									}
									else if($receive_basis==2)
									{
								?>
										<td><? echo $wo_numArr[$row_data['pi_wo_batch_no']]; ?></td>
								<?
									}
									else if($receive_basis==4)
									{
								?>
										<td><? echo $receive_basis_arr[$row_data[csf('receive_basis')]]; ?></td>
								<?
									}
									else if($receive_basis==6)
									{
								?>
										<td><? echo $receive_basis_arr[$row_data[csf('receive_basis')]]; ?></td>
                                <?
									}
									else if($receive_basis==7)
									{
								?>
										<td><? echo $req_arr[$row_data['pi_wo_batch_no']]; ?></td>
								<?
									}
								?>
                                <td align="center"><?  echo $row_data[csf('recv_number')]; ?></td>
                                
                            </tr>
                        <?
					 	$k++;	
					 	$total_qnty+=$row_data[csf('cons_quantity')];
						$total_amount+=$row_data[csf('cons_amount')];
					} 
						?>
                    	<tr>
                        	<td colspan="3" align="right"><b>Store Total: </b></td>
							<td align="right" ><b><? echo number_format($total_qnty,0,'',','); ?></b></td>
                            <td align="right" colspan="4"><b><? echo number_format($total_amount,2,'.',''); ?></b></td>
                            <td colspan="2">&nbsp;</td>
						</tr>
                        <?
					$item_total_qnty+=$total_qnty;
					$item_total_amount+=$total_amount;
				}
				?>
				<tr>
					<td colspan="3" align="right"><b>Item Total: </b></td>
					<td align="right" ><b><? echo number_format($item_total_qnty,0,'',','); ?></b></td>
					<td align="right" colspan="4"><b><? echo number_format($item_total_amount,2,'.',''); ?></b></td>
					<td colspan="2">&nbsp;</td>
				</tr>
				<?
		 	$comp_total_qnty+=$item_total_qnty;
			$comp_total_amount+=$item_total_amount;
			}
			?>
				<tr>
					<td colspan="3" align="right"><b>Grand Total: </b></td>
					<td align="right" ><b><? echo number_format($comp_total_qnty,0,'',','); ?></b></td>
					<td align="right" colspan="4"><b><? echo number_format($comp_total_amount,2,'.',''); ?></b></td>
					<td colspan="2">&nbsp;</td>
				</tr>
           </tbody>
        </table>
    </fieldset>
   </div>
     <?
	$html = ob_get_contents();
	ob_clean();
	//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename"; 
	exit();	
}

if($action=="generate_report2")
{ 
	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//echo $cbo_item_category_id.tst;die;
	$item_category_id="";
	if ($cbo_company_name==0) $company_id =""; else $company_id =" and a.company_id='$cbo_company_name'";
	if ($cbo_item_category_id !="") $item_category_id=" and c.item_category_id in($cbo_item_category_id)";
	if ($item_account_id==0) $item_code=""; else $item_code=" and b.prod_id in ($item_account_id)";
	if ($item_group_id==0) $group_id=""; else $group_id=" and c.item_group_id in ($item_group_id)";
	if ($cbo_supplier_name==0) $supplier_id=""; else $supplier_id=" and a.supplier_id='$cbo_supplier_name'";
	if ($cbo_store_name==0){ $store_id="";}else{$store_id=" and a.store_id='$cbo_store_name'";}
	//if ($item_group_id==0){ $group_id="";}else{$group_id=" and c.item_group_id='$item_group_id'";}
	if($db_type==2)
	{	
		if( $from_date==0 && $to_date==0 ) $transaction_date=""; else $transaction_date= " and b.transaction_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";	
	}
	if($db_type==0)
	{	
		if( $from_date==0 && $to_date==0 ) $transaction_date=""; else $transaction_date= " and b.transaction_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";	
	}
 	
 	
 	//library array-------------------
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name"); 
	$storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name"); 
	$itemgroupArr = return_library_array("select id,item_name from lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	$pi_numArr = return_library_array("select id, pi_number from com_pi_master_details where status_active=1 and is_deleted=0","id","pi_number");
	$wo_numArr = return_library_array("select id, wo_number from wo_non_order_info_mst where status_active=1 and is_deleted=0","id","wo_number");
	$req_arr = return_library_array("select id, requ_no from inv_purchase_requisition_mst where status_active=1 and is_deleted=0","id","requ_no");

	$sql = "select a.id, c.id as pro_id, a.item_category, c.item_group_id, c.item_description, c.item_size, c.item_number, c.brand_name as brand, c.model, a.receive_date, a.challan_no, a.supplier_id, a.store_id, b.cons_quantity, b.cons_uom, a.currency_id, b.cons_rate, b.cons_amount, a.receive_basis, b.pi_wo_batch_no, a.recv_number,  a.booking_id, b.transaction_date, c.item_category_id
	from inv_receive_master a, inv_transaction b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and c.status_active=1 and c.is_deleted=0 $company_id $item_category_id $item_code $supplier_id $store_id $transaction_date $group_id 
	order by c.item_category_id, a.store_id, a.receive_date, a.recv_number ";
	//echo $sql;
	$result = sql_select($sql);	
	// echo "<pre>";
	// 	print_r($result);
	$r=1;
	ob_start();	
	?>
	<div style="width:1500px;" id="" > 
     <fieldset style="width:1500px;">
        <table style="width:1480px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" > 
            <tr class="form_caption" style="border:none;">
                <td colspan="10" align="center" style="border:none;font-size:20px; font-weight:bold" ><strong>Item Wise Purchase/Receive Details</strong></td> 
            </tr>
            <tr style="border:none;">
                <td colspan="10" align="center" style="border:none; font-size:17px;"><strong>
                    Company Name : <? echo $companyArr[str_replace("'","",$cbo_company_name)]; ?>  </strong>                              
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="10" align="center" style="border:none;font-size:12px; font-weight:bold">
                    <? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date)."   To : ".change_date_format($to_date)."" ;?>
                </td>
            </tr>
		</table>
		<div style="width:1500px; " id="scroll_body">
		<table style="width:1480px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_2" >
			<thead>
				<th width="30" >SL</th>
				<th width="80" >Item Category</th>
				<th width="100" >Item Group</th>
				<th width="80" >Item Name</th>
				<th width="70" >Size</th>
				<th width="70" >Item Number</th>
				<th width="70" >Brand</th>
				<th width="70" >Model</th>
				<th width="70" >Receive Date</th>
				<th width="70" >Challan</th>                    
				<th width="90" >Supplier</th>
				<th width="90" >Store Name</th>
				<th width="70" >Quantity</th>
				<th width="70" >UOM</th>
				<th width="70" >Currency</th>
				<th width="70" >Rate</th>
				<th width="70" >Amount</th>
				<th width="120" >Basis Ref.</th>
				<th width="120" >MRIR</th>
			</thead>
			<tbody>
            <?
			 $all_data=array();
			
			foreach($result as $row)
			{
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['item_category_id']=$row[csf('item_category_id')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['item_group_id']=$row[csf('item_group_id')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['item_description']=$row[csf('item_description')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['size']=$row[csf('item_size')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['item_number']=$row[csf('item_number')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['brand']=$row[csf('brand')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['model']=$row[csf('model')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['receive_date']=$row[csf('receive_date')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['supplier']=$row[csf('supplier_id')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['store']=$row[csf('store_id')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['cons_quantity']=$row[csf('cons_quantity')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['cons_uom']=$row[csf('cons_uom')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['currency_id']=$row[csf('currency_id')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['cons_rate']=$row[csf('cons_rate')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['cons_amount']=$row[csf('cons_amount')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['receive_basis']=$row[csf('receive_basis')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['pi_wo_batch_no']=$row[csf('pi_wo_batch_no')];
				$all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['recv_number']=$row[csf('recv_number')];

				// $all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]][csf('pro_id')]=$row[csf('pro_id')];
				// $all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['transaction_date']=$row[csf('transaction_date')];
				// $all_data[$row[csf('pro_id')]][$row[csf('store_id')]][$row[csf('id')]]['booking_id']=$row[csf('booking_id')];
												 				 									
				if(!in_array($row[csf('pro_id')],$data_a))
				{
					$data_a[$row[csf('pro_id')]]=$row[csf('pro_id')];
				}
			} 
			
			// echo "<pre>";
			// 	print_r($all_data);

			$k=1;
		 	$comp_total_qnty=0;
			$comp_total_amount=0;
			foreach( $all_data as $val)
			{				 
				
				foreach($val as $store_id=>$store_data)
				{
					 
					foreach($store_data as $row_id=>$row_data)
					{
						
			          ?>														
						 
                            <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                                <td align="center"><? echo $k; ?></td>
								<td align="center"><? echo $item_category[$row_data['item_category_id']]; ?></td>
								<td align="center"><? echo $itemgroupArr[$row_data['item_group_id']]; ?></td>
								<td align="center"><? echo $row_data['item_description']; ?></td>
								<td align="center"><? echo $row_data['size']; ?></td>
								<td align="center"><? echo $row_data['item_number']; ?></td>
								<td align="center"><? echo $row_data['brand']; ?></td>
								<td align="center"><? echo $row_data['model']; ?></td>
								<td align="center"><? echo $row_data['receive_date']; ?></td>
								<td align="center"><? echo $row_data['challan_no']; ?></td>
								<td align="center"><? echo $supplierArr[$row_data['supplier']]; ?></td>
								<td align="center"><? echo $storeArr[$row_data['store']]; ?></td>
								<td align="center"><? echo $row_data['cons_quantity']; ?></td>
								<td align="center"><? echo $unit_of_measurement[$row_data['cons_uom']]; ?></td>
								<td align="center"><? echo $currency[$row_data['currency_id']]; ?></td>
								<td align="center"><? echo number_format($row_data['cons_rate'],2,'.',''); ?></td>
								<td align="center"><? echo number_format($row_data['cons_amount'],2,'.',''); ?></td>


                                
								<? 
									$receive_basis=$row_data['receive_basis'];
									if ($receive_basis==1)
									{
								?>
										<td><? echo $pi_numArr[$row_data['pi_wo_batch_no']]; ?></td>
								<?
									}
									else if($receive_basis==2)
									{
								?>
										<td><? echo $wo_numArr[$row_data['pi_wo_batch_no']]; ?></td>
								<?
									}
									else if($receive_basis==4)
									{
								?>
										<td><? echo $receive_basis_arr[$row_data['receive_basis']]; ?></td>
								<?
									}
									else if($receive_basis==6)
									{
								?>
										<td><? echo $receive_basis_arr[$row_data['receive_basis']]; ?></td>
                                <?
									}
									else if($receive_basis==7)
									{
								?>
										<td><? echo $req_arr[$row_data['pi_wo_batch_no']]; ?></td>
								<?
									}
								?>
                                <td align="center"><?  echo $row_data['recv_number']; ?></td>
                                
                            </tr>
                        <?
					 	$k++;	
					 	$total_qnty+=$row_data['cons_quantity'];
						$total_amount+=$row_data['cons_amount'];
					} 
						?>
                    	<tr>
                        	<td colspan="12" align="right"><b>Store Total: </b></td>
							<td align="right" ><b><? echo number_format($total_qnty,0,'',','); ?></b></td>
                            <td align="right" colspan="4"><b><? echo number_format($total_amount,2,'.',''); ?></b></td>
                            <td colspan="2">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="12" align="right"><b>Item Total: </b></td>
							<td align="right" ><b><? echo number_format($total_qnty,0,'',','); ?></b></td>
							<td align="right" colspan="4"><b><? echo number_format($total_amount,2,'.',''); ?></b></td>
							<td colspan="2">&nbsp;</td>
						</tr>
                        <?
							$total_qnty=0;
							$total_amount=0;
				}
						?>
				
			<?
		 	 
			  }
			?>
				 
           </tbody>
        </table>
		</div>
    </fieldset>
   </div>
     <?
	$html = ob_get_contents();
	ob_clean();
	//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename"; 
	exit();	
}

?>