<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

if ($action=="order_popup")
{
	echo load_html_head_contents("Order Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 
	<script>
		
		function js_set_value(data)
		{
			$('#order_id').val(data);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:970px;">
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:960px;margin-left:10px">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="950" class="rpt_table" border="1" rules="all">
                <thead>
                    <th>Buyer Name</th>
                    <th>Style Ref.</th>
                    <th>Job No</th>
                    <th>File No</th>
                    <th>Ref. No</th>
                    <th>Order No</th>
                    <th width="">Shipment Date Range</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                        <input type="hidden" name="order_id" id="order_id" class="text_boxes" value="">
                    </th>
                </thead>
                <tr class="general">
                    <td>
						<?
							echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
						?>
                    </td>
                    <td>
                        <input type="text" style="width:100px;" class="text_boxes" name="txt_style_ref" id="txt_style_ref" />
                    </td>
                    <td>
                        <input type="text" style="width:100px;" class="text_boxes" name="txt_job_no" id="txt_job_no" />
                    </td>
                    <td>
                        <input type="text" style="width:100px;" class="text_boxes_numeric" name="txt_file_no" id="txt_file_no" />
                    </td>
                    <td>
                        <input type="text" style="width:100px;" class="text_boxes" name="txt_ref_no" id="txt_ref_no" />
                    </td>
                    <td>
                        <input type="text" style="width:100px;" class="text_boxes" name="txt_order_no" id="txt_order_no" />
                    </td>
                    <td>
                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" readonly>
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To Date" readonly>
                    </td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $type; ?>'+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref_no').value+'_'+<? echo $cbo_company_to_id; ?>+'_'+document.getElementById('txt_style_ref').value+'_'+document.getElementById('txt_job_no').value, 'create_po_search_list_view', 'search_div', 'finish_fabric_order_to_order_transfer_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="8" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
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

if($action=='create_po_search_list_view')
{
	$data=explode('_',$data);
	
	if ($data[0]==0) $buyer="%%"; else $buyer=$data[0];
	$search_string="%".trim($data[1])."%";
	$company_id=$data[2];
	
	$file_no=trim($data[6]);
	$ref_no=trim($data[7]);
	$cbo_company_to_id=$data[8];
	$txt_style_ref=trim($data[9]);
	$txt_job_no=trim($data[10]);
	
	$file_no_cond=""; $ref_no_cond="";
	if($file_no!="")
	{
		$file_no_cond=" and b.file_no='".$file_no."'";
	}
	
	if($ref_no!="")
	{
		$ref_no_cond=" and b.grouping='".$ref_no."'";
	}
		
	if($txt_style_ref!="")
	{
		$ref_no_cond=" and a.style_ref_no like '%".$txt_style_ref."%'";
	}	
	if($txt_job_no!="")
	{
		$ref_no_cond=" and a.job_no like '%".$txt_job_no."%'";
	}

	if ($data[3]!="" &&  $data[4]!="")
	{
		if($db_type==0)
		{
			$shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3],'','',1)."' and '".change_date_format($data[4],'','',1)."'";
		} 
	}
	else $shipment_date ="";
	
	$type=$data[5]; 
	$arr=array (2=>$company_arr,3=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	if($type=="from") $status_cond=" and b.status_active in(1,3)"; else $status_cond=" and b.status_active=1";
	if($type=="from") $company_cond=" and a.company_name=$company_id"; else $company_cond=" and a.company_name=$cbo_company_to_id";

	$sql= "select a.job_no_prefix_num, $year_field, a.job_no,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.buyer_name like '$buyer' and b.po_number like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $status_cond $company_cond $shipment_date $file_no_cond $ref_no_cond order by b.id, b.pub_shipment_date";  
	 
	echo create_list_view("list_view", "Job No,Year,Company,Buyer,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date, File No, Ref. No", "60,50,60,60,120,80,110,70,80,90","950","200",0, $sql , "js_set_value", "id", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date,file_no,grouping", "",'','0,0,0,0,0,1,0,1,3,0,0');
	
	exit();
}

if($action=='populate_data_from_order')
{
	$data=explode("**",$data);
	$po_id=$data[0];
	$which_order=$data[1];
	
	$data_array=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$po_id");
	foreach ($data_array as $row)
	{ 
		$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
		foreach($gmts_item_id as $item_id)
		{
			if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
		}
		
		echo "document.getElementById('txt_".$which_order."_order_id').value 			= '".$po_id."';\n";
		echo "document.getElementById('txt_".$which_order."_order_no').value 			= '".$row[csf("po_number")]."';\n";
		echo "document.getElementById('txt_".$which_order."_po_qnty').value 			= '".$row[csf("po_quantity")]."';\n";
		echo "document.getElementById('cbo_".$which_order."_buyer_name').value 			= '".$row[csf("buyer_name")]."';\n";
		echo "document.getElementById('txt_".$which_order."_style_ref').value 			= '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('txt_".$which_order."_job_no').value 				= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_".$which_order."_gmts_item').value 			= '".$gmts_item."';\n";
		echo "document.getElementById('txt_".$which_order."_shipment_date').value 		= '".change_date_format($row[csf("shipment_date")])."';\n";
		echo "document.getElementById('txt_".$which_order."_file_no').value 			= '".$row[csf("file_no")]."';\n";
		echo "document.getElementById('txt_".$which_order."_ref_no').value 				= '".$row[csf("grouping")]."';\n";

		exit();
	}
}

if($action=="load_drop_down_item_desc")
{
	$item_description=array();
	$sql="select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.po_breakdown_id=$data and b.entry_form in(7,37) and b.trans_type=1 and b.status_active=1 and b.is_deleted=0";
	$dataArray=sql_select($sql);	
	foreach($dataArray as $row)
	{
		$item_description[$row[csf('id')]]=$row[csf('product_name_details')];
	}
	
	echo create_drop_down( "cbo_item_desc", 403, $item_description,'', 1, "--Select Item Description--",'0','','1');

	echo '<input type="text" name="show_only_item_desc" id="show_only_item_desc" class="text_boxes" style="width:392px; display:none;" placeholder="Item Description" disabled />';  
	exit();
}

if($action=="show_dtls_list_view")
{
	$sql = "select a.id, a.product_name_details, c.batch_id, a.color, c.rack_no, c.shelf_no, sum(b.quantity) as receive_qnty, a.unit_of_measure	 
			from 			
				product_details_master a, order_wise_pro_details b, pro_finish_fabric_rcv_dtls c
			where  
				a.id=b.prod_id and b.dtls_id=c.id and a.item_category_id=2 and b.entry_form in(7,37) and b.po_breakdown_id=$data and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.trans_id>0 and c.trans_id>0 
				group by a.id, a.product_name_details, a.unit_of_measure, c.batch_id, c.batch_id, c.rack_no, c.shelf_no, a.color";	
	//echo $sql;
	$data_array=sql_select($sql);	
	
	$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');	
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');	
	
	?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="480">
        <thead>
            <th>Fabric Description</th>
            <th width="90">Batch No & Color</th>
            <th width="80">Finish Qty</th>
            <th width="50">Rack</th>
            <th width="50">Shelf</th>
        </thead>
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
             ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $row[csf('id')]."**".$row[csf('batch_id')]."**".$batch_arr[$row[csf('batch_id')]]."**".$row[csf('rack_no')]."**".$row[csf('shelf_no')]."**".$row[csf('unit_of_measure')]."**".$color_arr[$row[csf('color')]]."**".$row[csf('product_name_details')]; ?>")' style="cursor:pointer">
                    <td><p><? echo $row[csf('product_name_details')]; ?></p></td>
                    <td>
                    	<p>
                    		&nbsp;
                    		<? echo $batch_arr[$row[csf('batch_id')]]; ?>
                    		<br>
                    		&nbsp; <? echo $color_arr[$row[csf('color')]]; ?>
                    	</p>
                    </td>
                    <td align="right"><? echo number_format($row[csf('receive_qnty')],2); ?></td>
                    <td><p>&nbsp;<? echo $row[csf('rack_no')]; ?></p></td>
                    <td><p>&nbsp;<? echo $row[csf('shelf_no')]; ?></p></td>
                </tr>
            <? 
            $i++; 
            } 
            ?>
        </tbody>
    </table>
<?
	exit();
}

if ($action=="orderToorderTransfer_popup")
{
	echo load_html_head_contents("Order To Order Transfer Info", "../../../", 1, 1,'','','');
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
<div align="center" style="width:780px;">
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:770px;margin-left:10px">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="650" class="rpt_table" border="1" rules="all">
                <thead>
                    <th>Search By</th>
                    <th width="180" id="search_by_td_up">Please Enter Transfer ID</th>
                    <th width="240" >Date Range</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                        <input type="hidden" name="transfer_id" id="transfer_id" class="text_boxes" value="">
                    </th>
                </thead>
                <tr class="general">
                    <td>
						<?
							$search_by_arr=array(1=>"Transfer ID", 2=>"Challan No.", 3=>"Batch No.",);
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../../') ";
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td id="search_by_td">
                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>

                    <td align="center">
                         <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:80px" placeholder="From Date"/>
                         To
                         <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:80px" placeholder="To Date"/>
                    </td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id; ?>, 'create_transfer_search_list_view', 'search_div', 'finish_fabric_order_to_order_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="4" align="center" width="90%"><? echo load_month_buttons(1); ?></td>
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
	$txt_date_form =$data[2];
	$txt_date_to =$data[3];
	$company_id =$data[4];
	//print_r($data);die;
	
	if($search_by==1)
	{
 		 if ($data[0]!="") $search_field=" and a.transfer_system_id like '$search_string' "; else $search_field="";
	}
	 
	else if($search_by==2)

	{
		 if ($data[0]!="") $search_field=" and a.challan_no like '$search_string' "; else $search_field="";
	}

 	else
	{
		 if ($data[0]!="") $search_field=" and c.batch_no like '$search_string' "; else $search_field="";
	}

 

	if($txt_date_form!="" && $txt_date_to!="")
	{ 
		if($db_type==0)
		{
			$txt_date_form=change_date_format($txt_date_form,"yyyy-mm-dd");
			$txt_date_to=change_date_format($txt_date_to,"yyyy-mm-dd");
		}
		else
		{
			$txt_date_form=change_date_format($txt_date_form,'','',1);
			$txt_date_to=change_date_format($txt_date_to,'','',1);	
		}
		
		$date_con=" and a.transfer_date between '$txt_date_form' and '$txt_date_to'";
	}
 	else  
 	{
		$date_con="";
	}


	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
 	     $sql="select a.id, a.transfer_prefix_number, a.transfer_system_id, $year_field, a.challan_no, a.company_id, a.transfer_date, a.transfer_criteria, a.item_category, b.batch_id, c.batch_no from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_batch_create_mst c where a.id=b.mst_id and b.batch_id=c.id   and a.item_category=2 and a.company_id=$company_id  $search_field  $date_con and a.transfer_criteria=4 and a.status_active=1 and a.is_deleted=0 ";
	
	$arr=array(4=>$company_arr,6=>$item_transfer_criteria,7=>$item_category);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Batch No,Company,Transfer Date,Transfer Criteria,Item Category", "80,70,60,60,90,90,120","760","250",0, $sql, "js_set_value", "id", "", 1, "0,0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,batch_no,company_id,transfer_date,transfer_criteria,item_category", '','','0,0,0,0,0,3,0,0');
	
	exit();
}

if($action=='populate_data_from_transfer_master')
{
	$data_array=sql_select("select transfer_system_id, challan_no, company_id, transfer_date, item_category, from_order_id,to_order_id from inv_item_transfer_mst where id='$data'");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";
		
		echo "get_php_form_data('".$row[csf("from_order_id")]."**from'".",'populate_data_from_order','requires/finish_fabric_order_to_order_transfer_controller');\n";
		echo "get_php_form_data('".$row[csf("to_order_id")]."**to'".",'populate_data_from_order','requires/finish_fabric_order_to_order_transfer_controller');\n";
		
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n"; 
		
		exit();
	}
}

if($action=="show_transfer_listview")
{
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=2","id","product_name_details");
	
	$sql="select id, from_prod_id, transfer_qnty, item_category, uom, to_rack as rack, to_shelf as shelf from inv_item_transfer_dtls where mst_id='$data' and status_active = '1' and is_deleted = '0'";
	
	$arr=array(0=>$item_category,1=>$product_arr,3=>$unit_of_measurement);
	 
	echo  create_list_view("list_view", "Item Category,Item Description,Transfered Qnty,UOM, Rack, Shelf", "120,250,100,70,80","750","200",0, $sql, "get_php_form_data", "id", "'populate_transfer_details_form_data'", 0, "item_category,from_prod_id,0,uom,0,0", $arr, "item_category,from_prod_id,transfer_qnty,uom,rack,shelf", "requires/finish_fabric_order_to_order_transfer_controller",'','0,0,2,0');
	exit();
}

if($action=='populate_transfer_details_form_data')
{
	$data_array=sql_select("select id, mst_id, from_prod_id, transfer_qnty,no_of_roll, item_category, uom, batch_id, to_rack, to_shelf, rack, shelf from inv_item_transfer_dtls where id='$data'");

//echo "select id, mst_id, from_prod_id, transfer_qnty,no_of_roll, item_category, uom, batch_id, to_rack, to_shelf, rack, shelf from inv_item_transfer_dtls where id='$data'";

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$product_name_details_arr=array();
	$product_color_arr=array();
	$desc_and_color_arr=sql_select("select id, product_name_details, color from product_details_master");

	foreach ($desc_and_color_arr as $value) {
		$product_name_details_arr[$value[csf("id")]]=$value[csf("product_name_details")];
		$product_color_arr[$value[csf("id")]]=$value[csf("color")];
	}


	foreach ($data_array as $row)
	{ 
		$batch_no=return_field_value("batch_no","pro_batch_create_mst","id='".$row[csf("batch_id")]."'");	
		
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_item_desc').value 				= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('txt_transfer_qnty').value 			= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('txt_roll_no').value 					= '".$row[csf("no_of_roll")]."';\n";
		echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("uom")]."';\n";
		echo "document.getElementById('txt_batch_no').value 				= '".$batch_no."';\n";
		echo "document.getElementById('txt_batch_id').value 				= '".$row[csf("batch_id")]."';\n";
		echo "document.getElementById('txt_torack').value 					= '".$row[csf("to_rack")]."';\n";
		echo "document.getElementById('txt_toshelf').value 					= '".$row[csf("to_shelf")]."';\n";
		echo "document.getElementById('txt_rack').value 					= '".$row[csf("rack")]."';\n";
		echo "document.getElementById('txt_shelf').value 					= '".$row[csf("shelf")]."';\n";



		echo "document.getElementById('show_only_item_desc').value 			= '".$product_name_details_arr[$row[csf("from_prod_id")]]." , ".$color_arr[$product_color_arr[$row[csf("from_prod_id")]]]."';\n";

		echo "$('#cbo_item_desc').hide();\n";
		echo "$('#show_only_item_desc').show();\n";


		

		//$sql_trans=sql_select("select id, transaction_type from inv_transaction where mst_id=".$row[csf('mst_id')]." and item_category=2 and transaction_type in(5,6) order by id asc");
		$sql_trans=sql_select("select trans_id from order_wise_pro_details where dtls_id=".$row[csf('id')]." and entry_form=15 and trans_type in(5,6) order by trans_type DESC");
		
		echo "document.getElementById('update_trans_issue_id').value 		= '".$sql_trans[0][csf("trans_id")]."';\n";
		echo "document.getElementById('update_trans_recv_id').value 		= '".$sql_trans[1][csf("trans_id")]."';\n";
		
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n"; 
		
		exit();
	}
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
        $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id = $cbo_item_desc and transaction_type in (1,4,5)", "max_date");      
	$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
	$transfer_date = date("Y-m-d", strtotime(str_replace("'","",$txt_transfer_date)));
	if ($transfer_date < $max_recv_date) 
        {
            echo "20**Issue Date Can not Be Less Than Last Receive Date Of This Lot";
            die;
	}
        
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$transfer_recv_num=''; $transfer_update_id='';
		
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			//$new_transfer_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'FFOTOTE', date("Y",time()), 5, "select transfer_prefix, transfer_prefix_number from inv_item_transfer_mst where company_id=$cbo_company_id and transfer_criteria=4 and item_category=$cbo_item_category and $year_cond=".date('Y',time())." order by id desc ", "transfer_prefix", "transfer_prefix_number" ));
		 	
			//$id=return_next_id( "id", "inv_item_transfer_mst", 1 ) ;
			$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);
			$new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst",$con,1,$cbo_company_id,'FFOTOTE',15,date("Y",time()),2 ));
					 
			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, entry_form, transfer_criteria, to_company, from_order_id, to_order_id, item_category, inserted_by, insert_date";
			
			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$txt_transfer_date.",15,4,0,".$txt_from_order_id.",".$txt_to_order_id.",".$cbo_item_category.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//echo "insert into inv_item_transfer_mst (".$field_array.") values ".$data_array;die;
			/*$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;*/
			
			$transfer_recv_num=$new_transfer_system_id[0];
			$transfer_update_id=$id;
		}
		else
		{
			$field_array_update="challan_no*transfer_date*from_order_id*to_order_id*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$txt_from_order_id."*".$txt_to_order_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			/*$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; */
			
			$transfer_recv_num=str_replace("'","",$txt_system_id);
			$transfer_update_id=str_replace("'","",$update_id);
		}
		
		$rate=0; $amount=0;
		
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$field_array_trans="id, mst_id, company_id, prod_id, pi_wo_batch_no, item_category, transaction_type, transaction_date, order_id, cons_uom, cons_quantity, cons_rate, cons_amount, rack, self, inserted_by, insert_date,no_of_roll";
		
		$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$cbo_item_desc.",".$txt_batch_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$txt_from_order_id.",".$cbo_uom.",".$txt_transfer_qnty.",'".$rate."','".$amount."',".$txt_rack.",".$txt_shelf.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_roll_no.")";
		
		//$id_trans_recv=$id_trans+1;
		$id_trans_recv = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$data_array_trans.=",(".$id_trans_recv.",".$transfer_update_id.",".$cbo_company_id.",".$cbo_item_desc.",".$txt_batch_id.",".$cbo_item_category.",5,".$txt_transfer_date.",".$txt_to_order_id.",".$cbo_uom.",".$txt_transfer_qnty.",'".$rate."','".$amount."',".$txt_torack.",".$txt_toshelf.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_roll_no.")";
		
		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		/*$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} */
		
		//$id_dtls=return_next_id( "id", "inv_item_transfer_dtls", 1 ) ;
		$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
		$field_array_dtls="id, mst_id, from_prod_id, item_category, transfer_qnty, rate, transfer_value, uom, batch_id, rack, shelf, to_rack, to_shelf, inserted_by, insert_date,no_of_roll";
		
		$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$cbo_item_desc.",".$cbo_item_category.",".$txt_transfer_qnty.",'".$rate."','".$amount."',".$cbo_uom.",".$txt_batch_id.",".$txt_rack.",".$txt_shelf.",".$txt_torack.",".$txt_toshelf.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_roll_no.")";
		
		//echo "insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		/*$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} */
		
		$color_id=return_field_value("color","product_details_master","id=$cbo_item_desc");
		
		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, inserted_by, insert_date";
		
		$data_array_prop="(".$id_prop.",".$id_trans.",6,15,".$id_dtls.",".$txt_from_order_id.",".$cbo_item_desc.",'".$color_id."',".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		//$id_prop=$id_prop+1;
		$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		$data_array_prop.=",(".$id_prop.",".$id_trans_recv.",5,15,".$id_dtls.",".$txt_to_order_id.",".$cbo_item_desc.",'".$color_id."',".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; 
		}
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		
		$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		} 
		
		//echo $flag;die;
		//echo "10**$rID==$rID2==$rID3==$rID4==$flag";die;
		if($db_type==0)
		{
			if($flag==1)
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
			if($flag==1)
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

		$field_array_update="challan_no*transfer_date*from_order_id*to_order_id*updated_by*update_date";
		$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$txt_from_order_id."*".$txt_to_order_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		/*$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;*/
		
		$field_array_trans="prod_id*pi_wo_batch_no*transaction_date*order_id*cons_uom*cons_quantity*cons_rate*cons_amount*rack*self*updated_by*update_date*no_of_roll";
		$updateTransID_array=array();
		$update_trans_issue_id=str_replace("'","",$update_trans_issue_id);
		$update_trans_recv_id=str_replace("'","",$update_trans_recv_id); 
		 
		$rate=0; $amount=0;
		
		$updateTransID_array[]=$update_trans_issue_id; 
		$updateTransID_data[$update_trans_issue_id]=explode("*",("".$cbo_item_desc."*".$txt_batch_id."*".$txt_transfer_date."*".$txt_from_order_id."*".$cbo_uom."*".$txt_transfer_qnty."*'".$rate."'*'".$amount."'*".$txt_rack."*".$txt_shelf."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_roll_no));
		
		$updateTransID_array[]=$update_trans_recv_id; 
		$updateTransID_data[$update_trans_recv_id]=explode("*",("".$cbo_item_desc."*".$txt_batch_id."*".$txt_transfer_date."*".$txt_to_order_id."*".$cbo_uom."*".$txt_transfer_qnty."*'".$rate."'*'".$amount."'*".$txt_torack."*".$txt_toshelf."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_roll_no));
		
		/*$rID2=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans,$updateTransID_data,$updateTransID_array));
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}*/
		
		$field_array_dtls="from_prod_id*transfer_qnty*rate*transfer_value*uom*batch_id*rack*shelf*to_rack*to_shelf*updated_by*update_date*no_of_roll";
		$data_array_dtls=$cbo_item_desc."*".$txt_transfer_qnty."*'".$rate."'*'".$amount."'*".$cbo_uom."*".$txt_batch_id."*".$txt_rack."*".$txt_shelf."*".$txt_torack."*".$txt_toshelf."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_roll_no; 
		
		/*$rID3=sql_update("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,"id",$update_dtls_id,1);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		}
		
		$query = execute_query("DELETE FROM order_wise_pro_details WHERE dtls_id=$update_dtls_id and entry_form=15");
		{
			if($query) $flag=1; else $flag=0; 
		} */
		
		$color_id=return_field_value("color","product_details_master","id=$cbo_item_desc");
		
		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, inserted_by, insert_date";
		
		$data_array_prop="(".$id_prop.",".$update_trans_issue_id.",6,15,".$update_dtls_id.",".$txt_from_order_id.",".$cbo_item_desc.",'".$color_id."',".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		//$id_prop=$id_prop+1;
		$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		$data_array_prop.=",(".$id_prop.",".$update_trans_recv_id.",5,15,".$update_dtls_id.",".$txt_to_order_id.",".$cbo_item_desc.",'".$color_id."',".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;

		$rID2=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans,$updateTransID_data,$updateTransID_array));
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}
		
		$rID3=sql_update("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,"id",$update_dtls_id,1);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		}
		
		$query = execute_query("DELETE FROM order_wise_pro_details WHERE dtls_id=$update_dtls_id and entry_form=15");
		{
			if($query) $flag=1; else $flag=0; 
		} 
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		} 
		
		if($db_type==0)
		{
			if($flag==1)
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
			if($flag==1)
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


if ($action=="finish_fabric_order_to_order_transfer_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$sql="select id, transfer_system_id, transfer_date, challan_no, from_order_id, to_order_id, item_category from inv_item_transfer_mst a where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
	$color_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );

	$pro_color_id_arr = return_library_array( "select id, color from product_details_master",'id','color');
	
	$po_array=array();
	$sql_po=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, b.po_number, b.po_quantity, b.pub_shipment_date, b.file_no, b.grouping, b.id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$data[0]'");
	foreach($sql_po as $row_po)
	{
		$po_array[$row_po[csf('id')]]['no']=$row_po[csf('po_number')];
		$po_array[$row_po[csf('id')]]['job']=$row_po[csf('job_no')];
		$po_array[$row_po[csf('id')]]['buyer']=$row_po[csf('buyer_name')];
		$po_array[$row_po[csf('id')]]['qnty']=$row_po[csf('po_quantity')];
		$po_array[$row_po[csf('id')]]['date']=$row_po[csf('pub_shipment_date')];
		$po_array[$row_po[csf('id')]]['style']=$row_po[csf('style_ref_no')];
		$po_array[$row_po[csf('id')]]['file_no']=$row_po[csf('file_no')];
		$po_array[$row_po[csf('id')]]['ref_no']=$row_po[csf('grouping')];
	}
	
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=2","id","product_name_details");
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
        	<td width="125"><strong>Transfer ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
            <td width="125"><strong>Transfer Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
            <td width="125"><strong>Challan No.:</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
        </tr>
        <tr>
            <td><strong>From order No:</strong></td> <td width="175px"><? echo $po_array[$dataArray[0][csf('from_order_id')]]['no']; ?></td>
            <td><strong>From ord Qnty:</strong></td> <td width="175px"><? echo $po_array[$dataArray[0][csf('from_order_id')]]['qnty']; ?></td>
            <td><strong>From ord Buyer:</strong></td> <td width="175px"><? echo $buyer_library[$po_array[$dataArray[0][csf('from_order_id')]]['buyer']]; ?></td>
        </tr>
        <tr>
            <td><strong>From Style Ref.:</strong></td> <td width="175px"><? echo $po_array[$dataArray[0][csf('from_order_id')]]['style']; ?></td>
            <td><strong>From Job No:</strong></td> <td width="175px"><? echo $po_array[$dataArray[0][csf('from_order_id')]]['job']; ?></td>
            <td><strong>From Ship. Date:</strong></td> <td width="175px"><? echo change_date_format($po_array[$dataArray[0][csf('from_order_id')]]['date']); ?></td>
        </tr>
        <tr>
            <td><strong>From File No:</strong></td> <td width="175px"><? echo $po_array[$dataArray[0][csf('from_order_id')]]['file_no']; ?></td>
            <td><strong>From Ref. No:</strong></td> <td width="175px"><? echo $po_array[$dataArray[0][csf('from_order_id')]]['ref_no']; ?></td>
        </tr>
        <tr>
            <td><strong>To order No:</strong></td> <td width="175px"><? echo $po_array[$dataArray[0][csf('to_order_id')]]['no']; ?></td>
            <td><strong>To ord Qnty:</strong></td> <td width="175px"><? echo $po_array[$dataArray[0][csf('to_order_id')]]['qnty']; ?></td>
            <td><strong>To ord Buyer:</strong></td> <td width="175px"><? echo $buyer_library[$po_array[$dataArray[0][csf('to_order_id')]]['buyer']]; ?></td>
        </tr>
        <tr>
            <td><strong>To Style Ref.:</strong></td> <td width="175px"><? echo $po_array[$dataArray[0][csf('to_order_id')]]['style']; ?></td>
            <td><strong>To Job No:</strong></td> <td width="175px"><? echo $po_array[$dataArray[0][csf('to_order_id')]]['job']; ?></td>
            <td><strong>To Ship. Date:</strong></td> <td width="175px"><? echo change_date_format($po_array[$dataArray[0][csf('to_order_id')]]['date']); ?></td>
        </tr>
        <tr>
            <td><strong>To File No:</strong></td> <td width="175px"><? echo $po_array[$dataArray[0][csf('to_order_id')]]['file_no']; ?></td>
            <td><strong>To Ref. No:</strong></td> <td width="175px"><? echo $po_array[$dataArray[0][csf('to_order_id')]]['ref_no']; ?></td>
        </tr>
    </table>
    <br>
    <div style="width:100%;">
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="120" >Item Category</th>
            <th width="250" >Item Description</th>
            <th width="100" >Batch No.</th>
            <th width="100" >Color</th>
            <th width="100" >Transfered Qnty</th>
            <th width="70" >UOM</th>
            <th width="70" >Number of Roll</th>
            
        </thead>
        <tbody> 
   
<?
	$sql_dtls="select id, item_category, item_group, from_prod_id,batch_id, transfer_qnty,no_of_roll, uom from inv_item_transfer_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0";
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
                <td><? echo $item_category[$row[csf("item_category")]]; ?></td>
                <td><? echo $product_arr[$row[csf("from_prod_id")]]; ?></td>
                <td><? echo $batch_arr[$row[csf("batch_id")]]; ?></td>
                <td><? echo $color_arr[$pro_color_id_arr[$row[csf("from_prod_id")]]]; ?></td>
                <td align="right"><? echo $row[csf("transfer_qnty")]; ?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
                <td align="center">
                	<? 
                	$totalNumberOfRoll += $row[csf("no_of_roll")];
                	echo $row[csf("no_of_roll")];
                	?>
                </td>
                
                
			</tr>
			<? $i++; } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" align="right"><strong>Total :</strong></td>
                <td align="right"><?php echo $transfer_qnty_sum; ?></td>
                <td>&nbsp;  </td>
                <td align="center"><?php echo  $totalNumberOfRoll; ?></td>
            </tr>                           
        </tfoot>
      </table>
        <br>
		 <?
            echo signature_table(24, $data[0], "900px");
         ?>
      </div>
   </div>   
 <?
 exit();
}
?>
