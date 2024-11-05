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
$sample_arr=return_library_array( "select id,sample_name from lib_sample where is_deleted=0 and status_active=1 order by sample_name",'id','sample_name');
$color_arr=return_library_array( "select id,color_name from lib_color where is_deleted=0 and status_active=1 order by color_name",'id','color_name');


if ($action=="from_order_popup")
{
	echo load_html_head_contents("sample Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value(data)
		{
			//alert(data);return;
			$('#return_id').val(data);
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<?
	if ($cbo_transfer_criteria==6) // Order to sample
	{
		?>
		<div align="center" style="width:1185px;">
			<form name="searchdescfrm"  id="searchdescfrm">
				<fieldset style="width:1185px;">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" width="850" class="rpt_table" border="1" rules="all">
						<thead>
							<th>Buyer Name</th>
							<th>Order No</th>
							<th>File No</th>
							<th>Ref. No</th>
							<th width="200">Shipment Date Range</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:80px;" class="formbutton" />
								<input type="hidden" name="return_id" id="return_id" class="text_boxes" value="">
							</th>
						</thead>
						<tr class="general">
							<td>
								<?
								echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
								?>
							</td>
							<td>
								<input type="text" style="width:110px;" class="text_boxes" name="txt_to_order_book_no" id="txt_to_order_book_no" placeholder="Enter Order No" />
							</td>
							<td>
								<input type="text" style="width:110px;" class="text_boxes" name="txt_file_no" id="txt_file_no" placeholder="Enter File No" />
							</td>
							<td>
								<input type="text" style="width:110px;" class="text_boxes" name="txt_ref_no" id="txt_ref_no" placeholder="Enter Ref. No" />
							</td>
							<td>
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" readonly>
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" readonly>
							</td>
							<td>
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_to_order_book_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $type; ?>'+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref_no').value+'_'+<? echo $cbo_transfer_criteria; ?>, 'create_po_search_list_view', 'search_div', 'roll_wise_grey_fabric_sample_requisition_for_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:80px;" />
							</td>
						</tr>
						<tr>
							<td colspan="6" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</table>
					<div style="margin-top:10px" id="search_div"></div>
				</fieldset>
			</form>
		</div>
		<?
	}
	else // 
	{
		?>
		<div align="center" style="width:1180px;">
			<form name="searchdescfrm"  id="searchdescfrm">
				<fieldset style="width:1170px;margin-left:10px">
		        <legend>Enter search words</legend>
		            <table cellpadding="0" cellspacing="0" width="800" class="rpt_table">
		                <thead>
		                    <th>Buyer Name</th>
		                    <th>Booking No</th>
		                    <th width="230">Booking Date Range</th>
		                    <th>
		                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
		                        <input type="hidden" name="return_id" id="return_id" class="text_boxes" value="">
		                    </th>
		                </thead>
		                <tr class="general">
		                    <td>
								<?
									echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
								?>
		                    </td>
		                    <td>
		                        <input type="text" style="width:130px;" class="text_boxes" name="txt_sample_no" id="txt_sample_no" />
		                    </td>
		                    <td>
		                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" readonly>
		                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" readonly>
		                    </td>
		                    <td>
		                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_sample_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_transfer_criteria; ?>, 'create_sample_search_list_view', 'search_div', 'roll_wise_grey_fabric_sample_requisition_for_transfer_controller', 'setFilterGrid(\'tbl_list_sample\',-1);')" style="width:100px;" />
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
		<?
	}
	?>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=='create_sample_search_list_view') // sample search list view
{
	$data=explode('_',$data);
		
	if ($data[0]==0) $buyer="%%"; else $buyer=$data[0];
	$search_string="%".trim($data[1])."%";
	$company_id=$data[2];
	
	if ($data[3]!="" &&  $data[4]!="") 
	{
		if($db_type==0)
		{
			$booking_date = "and a.booking_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$booking_date = "and a.booking_date between '".change_date_format($data[3],'','',1)."' and '".change_date_format($data[4],'','',1)."'";
		}
	}
	else 
		$booking_date ="";
	
	$style_name_array=return_library_array( "select id, style_ref_no from sample_development_mst",'id','style_ref_no');
	$arr=array (2=>$company_arr,3=>$buyer_arr,4=>$style_name_array,5=>$sample_arr,6=>$body_part,8=>$color_arr);
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql= "SELECT a.id as booking_id, b.id as dtls_id, a.booking_no, $year_field, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.booking_date, a.delivery_date, b.style_id, b.fabric_description,b.sample_type,b.fabric_color,b.grey_fabric, b.body_part
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no  and a.booking_type=4 and a.company_id=$company_id and a.buyer_id like '$buyer' and b.booking_no like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $booking_date order by a.id, b.id";

	echo create_list_view("tbl_list_sample", "Booking No,Year,Company,Buyer Name,Style Ref. No,Sample,Body Part,Style Description,Fab. Color,Booking Date, Grey Qnty", "60,60,80,80,100,100,130,160,80,80,60","1100","200",0, $sql , "js_set_value", "booking_id,dtls_id,body_part", "", 1, "0,0,company_id,buyer_id,style_id,sample_type,body_part,0,fabric_color,0,0", $arr , "booking_no_prefix_num,year,company_id,buyer_id,style_id,sample_type,body_part,fabric_description,fabric_color,booking_date,grey_fabric", "",'','0,0,0,0,0,0,0,0,0,3');
	exit();
}

if($action=='create_po_search_list_view') // Order to sample search list view
{
	$data=explode('_',$data);
	
	if ($data[0]==0) $buyer="%%"; else $buyer=$data[0];
	$search_string="%".trim($data[1])."%";
	$company_id=$data[2];
	$file_no=$data[6];
	$ref_no=$data[7];
	
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
	else 
		$shipment_date ="";
	
	$type=$data[5];
	$arr=array(2=>$company_arr,3=>$buyer_arr,9=>$body_part);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	$str_cond="";
	if($file_no!="")  $str_cond=" and b.file_no=$file_no";
	if($ref_no!="")  $str_cond.=" and b.grouping like '%$ref_no%'";
	
	if($type=="from") $status_cond=" and b.status_active in(1,3)"; else $status_cond=" and b.status_active=1";
	/*$sql= "SELECT a.job_no, $year_field, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date, b.file_no, b.grouping as ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id and a.buyer_name like '$buyer' and b.po_number like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $status_cond $shipment_date $str_cond order by b.id, b.pub_shipment_date";*/ 

	 
	//echo create_list_view("tbl_list_search", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,File No,Ref. No,PO number,PO Quantity,Shipment Date", "50,40,60,80,110,80,70,90,110,80","910","200",0, $sql , "js_set_value", "id,id", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,file_no,ref_no,po_number,po_quantity,shipment_date", "",'','0,0,0,0,0,1,0,0,0,1,3');

	$sql = "SELECT a.job_no, $year_field, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, c.body_part_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date, b.file_no, b.grouping as ref_no from wo_po_details_master a, wo_po_break_down b, WO_PRE_COST_FABRIC_COST_DTLS c where a.id=b.job_id and b.job_id=c.job_id and a.company_name=$company_id and a.buyer_name like '$buyer' and b.po_number like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $status_cond $shipment_date $str_cond group by a.job_no, a.insert_date, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, c.body_part_id, b.po_number, b.po_quantity, b.pub_shipment_date, b.file_no, b.grouping order by b.id, b.pub_shipment_date"; 

	echo create_list_view("tbl_list_search", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,File No,Ref. No,PO number,Body Part, PO Quantity,Shipment Date", "50,40,60,80,110,80,70,90,100,110,80","1010","200",0, $sql , "js_set_value", "id,id,body_part_id", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0,body_part_id,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,file_no,ref_no,po_number,body_part_id,po_quantity,shipment_date", "",'','0,0,0,0,0,1,0,0,0,0,1,3');
	exit();	
}

if($action=="populate_data_from_sample") // when system no browse problem
{
	//print_r($data);
	$data=explode("**",$data);
	$return_id=$data[0]; // return_id is booking or order no
	$from=$data[1];
	$transfer_criteria=$data[2];
	$body_part_id=$data[3];

	$style_name_array=return_library_array( "select id, style_ref_no from sample_development_mst",'id','style_ref_no');
	if ($transfer_criteria==6) // Order to Sample
	{
		$data_array=sql_select("SELECT a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$return_id");
		foreach ($data_array as $row)
		{ 
			$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
			foreach($gmts_item_id as $item_id)
			{
				if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
			}
			
			echo "document.getElementById('txt_from_order_book_no').value 		= '".$row[csf("po_number")]."';\n";
			echo "document.getElementById('txt_from_order_book_id').value 		= '".$return_id."';\n";
			echo "document.getElementById('txt_from_qnty').value 				= '".$row[csf("po_quantity")]."';\n";
			echo "document.getElementById('cbo_from_buyer_name').value 			= '".$row[csf("buyer_name")]."';\n";
			echo "document.getElementById('txt_from_style_ref').value 			= '".$row[csf("style_ref_no")]."';\n";
			echo "document.getElementById('txt_from_job_no').value 				= '".$row[csf("job_no")]."';\n";
			echo "document.getElementById('txt_from_gmts_item').value 			= '".$gmts_item."';\n";
			echo "document.getElementById('txt_from_shipment_date').value 		= '".change_date_format($row[csf("shipment_date")])."';\n";
			echo "document.getElementById('cbo_from_body_part').value 			= '".$body_part_id."';\n";
			exit();
		}
	}
	else // Sample to Sample and Sample to Order, When Transfer System ID a.id=$return_id
	{
		/*Sample to Sample=$data_array=sql_select("select a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.id as dtls_id, b.style_id, b.body_part, b.grey_fabric
		from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b  
		where a.booking_no=b.booking_no and b.id=$sample_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");*/

		// Sample to Order
		/*$data_array=sql_select("SELECT a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.style_id, b.body_part, b.grey_fabric
		from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b  
		where a.booking_no=b.booking_no and a.id=$sample_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");*/

		$data_array=sql_select("SELECT a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.style_id, b.body_part, b.grey_fabric, b.id as dtls_id
		from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b  
		where a.booking_no=b.booking_no and b.id=$return_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		// Sample to sample b.id=$return_id
		// Sample to Order a.id=$sample_id

		foreach ($data_array as $row)
		{ 
			echo "document.getElementById('txt_from_order_book_no').value 		= '".$row[csf("booking_no")]."';\n";
			echo "document.getElementById('txt_from_order_book_id').value 		= '".$row[csf("booking_id")]."';\n";
			echo "document.getElementById('txt_from_order_book_dtls_id').value 	= '".$row[csf("dtls_id")]."';\n";
			echo "document.getElementById('txt_from_qnty').value 				= '".$row[csf("grey_fabric")]."';\n";
			echo "document.getElementById('cbo_from_buyer_name').value 			= '".$row[csf("buyer_id")]."';\n";
			echo "document.getElementById('txt_from_style_ref').value 			= '".$style_name_array[$row[csf("style_id")]]."';\n";
			echo "document.getElementById('cbo_from_body_part').value 		= '".$row[csf("body_part")]."';\n";
			exit();
		}
	}	
}

if ($action=="to_order_popup")
{
	echo load_html_head_contents("Order Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $cbo_transfer_criteria.'TTT';die;
	?> 
	<script>
		function js_set_value(data)
		{
			// alert(data);return;
			$('#order_id').val(data);
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<?
	if ($cbo_transfer_criteria==7) // Order
	{
		?>
		<div align="center" style="width:930px;">
			<form name="searchdescfrm"  id="searchdescfrm">
				<fieldset style="width:930px;">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" width="850" class="rpt_table" border="1" rules="all">
						<thead>
							<th>Buyer Name</th>
							<th>Order No</th>
							<th>File No</th>
							<th>Ref. No</th>
							<th width="200">Shipment Date Range</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:80px;" class="formbutton" />
								<input type="hidden" name="order_id" id="order_id" class="text_boxes" value="">
							</th>
						</thead>
						<tr class="general">
							<td>
								<?
								echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
								?>
							</td>
							<td>
								<input type="text" style="width:110px;" class="text_boxes" name="txt_to_order_book_no" id="txt_to_order_book_no" placeholder="Enter Order No" />
							</td>
							<td>
								<input type="text" style="width:110px;" class="text_boxes" name="txt_file_no" id="txt_file_no" placeholder="Enter File No" />
							</td>
							<td>
								<input type="text" style="width:110px;" class="text_boxes" name="txt_ref_no" id="txt_ref_no" placeholder="Enter Ref. No" />
							</td>
							<td>
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" readonly>
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" readonly>
							</td>
							<td>
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_to_order_book_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $type; ?>'+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref_no').value, 'create_to_po_search_list_view', 'search_div', 'roll_wise_grey_fabric_sample_requisition_for_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:80px;" />
							</td>
						</tr>
						<tr>
							<td colspan="6" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</table>
					<div style="margin-top:10px" id="search_div"></div>
				</fieldset>
			</form>
		</div>
		<?
	}
	else // Sample
	{
		?>
		<div align="center" style="width:930px;">
			<form name="searchdescfrm"  id="searchdescfrm">
				<fieldset style="width:930px;margin-left:10px">
		        <legend>Enter search words</legend>
		            <table cellpadding="0" cellspacing="0" width="800" class="rpt_table">
		                <thead>
		                    <th>Buyer Name</th>
		                    <th>Booking No</th>
		                    <th width="230">Booking Date Range</th>
		                    <th>
		                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
		                        <input type="hidden" name="order_id" id="order_id" class="text_boxes" value="">
		                    </th>
		                </thead>
		                <tr class="general">
		                    <td>
								<?
									echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
								?>
		                    </td>
		                    <td>
		                        <input type="text" style="width:130px;" class="text_boxes" name="txt_sample_no" id="txt_sample_no" />
		                    </td>
		                    <td>
		                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" readonly>
		                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" readonly>
		                    </td>
		                    <td>
		                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_sample_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_sample_search_to_list_view', 'search_div', 'roll_wise_grey_fabric_sample_requisition_for_transfer_controller', 'setFilterGrid(\'tbl_list_sample\',-1);')" style="width:100px;" />
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
		<?
	}
	?>
	<body>
		
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=='create_sample_search_to_list_view')
{
	$data=explode('_',$data);

	
	if ($data[0]==0) $buyer="%%"; else $buyer=$data[0];
	$search_string="%".trim($data[1])."%";
	$company_id=$data[2];
	
	if ($data[3]!="" &&  $data[4]!="") 
	{
		if($db_type==0)
		{
			$booking_date = "and a.booking_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$booking_date = "and a.booking_date between '".change_date_format($data[3],'','',1)."' and '".change_date_format($data[4],'','',1)."'";
		}
	}
	else 
		$booking_date ="";
	
	$style_name_array=return_library_array( "select id, style_ref_no from sample_development_mst",'id','style_ref_no');
	$arr=array (2=>$company_arr,3=>$buyer_arr,4=>$style_name_array,5=>$sample_arr,6=>$body_part,8=>$color_arr);
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql= "SELECT a.id as booking_id, b.id,a.booking_no, $year_field, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.booking_date, a.delivery_date, b.style_id, b.fabric_description,b.sample_type,b.fabric_color,b.grey_fabric, b.body_part
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no  and a.booking_type=4 and a.company_id=$company_id and a.buyer_id like '$buyer' and b.booking_no like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $booking_date order by a.id, b.id";
	
	// echo  $sql;die;
	 
	echo create_list_view("tbl_list_sample", "Booking No,Year,Company,Buyer Name,Style Ref. No,Sample,Body Part,Style Description,Fab. Color,Booking Date, Grey Qnty", "60,60,80,80,100,100,130,160,80,80,60","1100","200",0, $sql , "js_set_value", "id", "", 1, "0,0,company_id,buyer_id,style_id,sample_type,body_part,0,fabric_color,0,0", $arr , "booking_no_prefix_num,year,company_id,buyer_id,style_id,sample_type,body_part,fabric_description,fabric_color,booking_date,grey_fabric", "",'','0,0,0,0,0,0,0,0,0,3');
	
	exit();
}

if($action=='create_to_po_search_list_view')
{
	$data=explode('_',$data);
	
	if ($data[0]==0) $buyer="%%"; else $buyer=$data[0];
	$search_string="%".trim($data[1])."%";
	$company_id=$data[2];
	$file_no=$data[6];
	$ref_no=$data[7];
	
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
	else 
		$shipment_date ="";
	
	$type=$data[5];
	$arr=array(2=>$company_arr,3=>$buyer_arr,9=>$body_part);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	$str_cond="";
	if($file_no!="")  $str_cond=" and b.file_no=$file_no";
	if($ref_no!="")  $str_cond.=" and b.grouping like '%$ref_no%'";
	
	if($type=="from") $status_cond=" and b.status_active in(1,3)"; else $status_cond=" and b.status_active=1";
	/*$sql= "select a.job_no, $year_field, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date, b.file_no, b.grouping as ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id and a.buyer_name like '$buyer' and b.po_number like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $status_cond $shipment_date $str_cond order by b.id, b.pub_shipment_date";  
	 
	echo create_list_view("tbl_list_search", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,File No,Ref. No,PO number,PO Quantity,Shipment Date", "50,40,60,80,110,80,70,90,110,80","910","200",0, $sql , "js_set_value", "id", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,file_no,ref_no,po_number,po_quantity,shipment_date", "",'','0,0,0,0,0,1,0,0,0,1,3');*/
	$sql = "SELECT a.job_no, $year_field, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, c.body_part_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date, b.file_no, b.grouping as ref_no from wo_po_details_master a, wo_po_break_down b, WO_PRE_COST_FABRIC_COST_DTLS c where a.id=b.job_id and b.job_id=c.job_id and a.company_name=$company_id and a.buyer_name like '$buyer' and b.po_number like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $status_cond $shipment_date $str_cond group by a.job_no, a.insert_date, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, c.body_part_id, b.po_number, b.po_quantity, b.pub_shipment_date, b.file_no, b.grouping order by b.id, b.pub_shipment_date";

	echo create_list_view("tbl_list_search", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,File No,Ref. No,PO number,Body Part, PO Quantity,Shipment Date", "50,40,60,80,110,80,70,90,100,110,80","1010","200",0, $sql , "js_set_value", "id,body_part_id", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0,body_part_id,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,file_no,ref_no,po_number,body_part_id,po_quantity,shipment_date", "",'','0,0,0,0,0,1,0,0,0,0,1,3');
	exit();
}

if($action=='populate_data_to_order')
{
	//print_r($data);
	$data=explode("**", $data);
	$po_id=$data[0];
	$transfer_criteria=$data[1];
	$body_part_id=$data[2];

	if ($transfer_criteria==7) // Order
	{
		$data_array=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$po_id");
		foreach ($data_array as $row)
		{ 
			$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
			foreach($gmts_item_id as $item_id)
			{
				if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
			}
			
			echo "document.getElementById('txt_to_order_book_id').value 	= '".$po_id."';\n";
			echo "document.getElementById('txt_to_order_book_no').value 	= '".$row[csf("po_number")]."';\n";
			echo "document.getElementById('txt_to_qnty').value 				= '".$row[csf("po_quantity")]."';\n";
			echo "document.getElementById('cbo_to_buyer_name').value 		= '".$row[csf("buyer_name")]."';\n";
			echo "document.getElementById('txt_to_style_ref').value 		= '".$row[csf("style_ref_no")]."';\n";
			echo "document.getElementById('cbo_to_body_part').value 		= '".$body_part_id."';\n";
			echo "document.getElementById('txt_to_job_no').value 			= '".$row[csf("job_no")]."';\n";
			echo "document.getElementById('txt_to_gmts_item').value 		= '".$gmts_item."';\n";
			echo "document.getElementById('txt_to_shipment_date').value 	= '".change_date_format($row[csf("shipment_date")])."';\n";
			exit();
		}
	}
	else
	{
		$style_name_array=return_library_array( "select id, style_ref_no from sample_development_mst",'id','style_ref_no');
		$data_array=sql_select("select a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.id as booking_dtls_id, b.style_id, b.body_part, b.grey_fabric
		from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b  
		where a.booking_no=b.booking_no and b.id=$po_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		 
		foreach ($data_array as $row)
		{ 
			
			echo "document.getElementById('txt_to_order_book_no').value 		= '".$row[csf("booking_no")]."';\n";
			echo "document.getElementById('txt_to_order_book_id').value 		= '".$row[csf("booking_id")]."';\n";
			echo "document.getElementById('txt_to_order_book_dtls_id').value 	= '".$row[csf("booking_dtls_id")]."';\n";
			echo "document.getElementById('txt_to_qnty').value 					= '".$row[csf("grey_fabric")]."';\n";
			echo "document.getElementById('cbo_to_buyer_name').value 			= '".$row[csf("buyer_id")]."';\n";
			echo "document.getElementById('txt_to_style_ref').value 			= '".$style_name_array[$row[csf("style_id")]]."';\n";
			echo "document.getElementById('cbo_to_body_part').value 			= '".$row[csf("body_part")]."';\n";
			exit();
		}
	}	
}

//done
if($action=="show_dtls_list_view")
{
	$data=explode("**", $data);
	$booking_order_id = $data[0];
	$transfer_criteria = $data[1];
	$body_part_id = $data[2];

	if($transfer_criteria==8) // Sample to Sample
	{
		$entry_form_no = 180;
	}
	elseif($transfer_criteria==7) // Sample to Order
	{
		$entry_form_no = 183;
	}
	else{
		$entry_form_no = 110; // Order to Sample
	}

	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');	
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');	
	$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
	$delv_arr=return_library_array("select barcode_num, grey_sys_id from pro_grey_prod_delivery_dtls where entry_form=56 and order_id=$booking_order_id", "barcode_num", "grey_sys_id");	
				
	$programArr=return_library_array("select a.id, a.booking_id from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=2 and a.receive_basis=2 and b.entry_form=2 and b.po_breakdown_id=$booking_order_id and b.status_active=1 and b.is_deleted=0", "id", "booking_id");

	$req_sql="SELECT a.id, b.barcode_no from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b
	where a.id=b.mst_id
	and a.from_order_id=$booking_order_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 
	order by barcode_no";
	$req_data = sql_select($req_sql);
	foreach ($req_data as $row) 
	{
		$barcode .= "'".$row[csf("barcode_no")] . "', ";
	}
	$barcodeNo = rtrim($barcode, ", ");
	if ($barcodeNo!="")
	{
		$barcodeNo_cond =" and c.barcode_no not in($barcodeNo)";
	}
	
	//echo $barcodeNo;

	if ($transfer_criteria==6) 
	{
		/*$sql="SELECT a.id, a.entry_form, a.receive_basis, a.booking_id, b.prod_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.floor_id, b.room, b.rack, b.self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 1 as type, a.store_id, c.qc_pass_qnty_pcs, b.body_part_id
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
		WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$booking_order_id $barcodeNo_cond  and b.body_part_id=$body_part_id
		union all
		select a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.floor_id, b.room, b.rack, b.shelf as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 2 as type, b.to_store as store_id, c.qc_pass_qnty_pcs, b.to_body_part as body_part_id
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c 
		WHERE a.id=b.mst_id and b.id=c.dtls_id  and c.entry_form in(82,83,183) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$booking_order_id $barcodeNo_cond and b.to_body_part=$body_part_id";*/

		$sql="SELECT a.id, a.entry_form, a.receive_basis, a.booking_id, b.prod_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.floor_id, b.room, b.rack, b.self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 1 as type, a.store_id, d.gsm, d.dia_width, d.detarmination_id, d.item_description, d.product_name_details, b.body_part_id, b.yarn_rate, b.kniting_charge, c.rate
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, product_details_master d
		WHERE a.id=b.mst_id and b.id=c.dtls_id and b.prod_id = d.id and b.trans_id<>0 and a.entry_form in(2,22,58,84) and c.entry_form in(2,22,58,84) and c.re_transfer=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$booking_order_id and b.body_part_id=$body_part_id $barcodeNo_cond and c.is_sales=0 and c.booking_without_order=0
		union all
		 select a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.to_prod_id as prod_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.to_floor_id as floor_id, b.to_room as room, b.to_rack as rack, b.to_shelf as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 2 as type, b.to_store as store_id, d.gsm, d.dia_width, d.detarmination_id, d.item_description, d.product_name_details, b.to_body_part as body_part_id, b.yarn_rate, b.kniting_charge, c.rate
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, product_details_master d
		WHERE a.id=b.mst_id and b.id=c.dtls_id and b.to_prod_id = d.id  and c.entry_form in(82,83,183) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$booking_order_id and b.to_body_part=$body_part_id $barcodeNo_cond and c.booking_without_order=0";
	}
	else
	{		
		// Sample to Order sql
		/*$sql="SELECT a.id, a.entry_form, a.receive_basis, a.booking_id, b.prod_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.rack, b.self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 1 as type , a.store_id
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
		WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,22,58) and b.trans_id<>0 and c.entry_form in(2,22,58) and c.is_transfer!=6 and c.booking_without_order=1 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$booking_order_id";*/

		// Sample to Sample
		/*$sql="SELECT a.id, a.entry_form, a.receive_basis, a.booking_id, b.prod_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.floor_id, b.room, b.rack, b.self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 1 as type, a.store_id, c.qc_pass_qnty_pcs, b.body_part_id
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
		WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,22,58) and b.trans_id<>0 and c.entry_form in(2,22,58) and c.re_transfer =0 and c.booking_without_order=1 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$booking_order_id $barcodeNo_cond  and b.body_part_id=$body_part_id

		UNION ALL

		SELECT a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.to_prod_id as prod_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.to_floor_id as floor_id, b.to_room as room, b.to_rack as rack, b.to_shelf as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 3 as type , b.to_store as store_id, c.qc_pass_qnty_pcs, b.to_body_part as body_part_id
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c 
		where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(110,180) and c.entry_form in(110,180) and c.booking_without_order=1 and c.re_transfer =0 and c.status_active=1 and c.is_deleted=0 and a.to_order_id =$booking_order_id $barcodeNo_cond  and b.to_body_part=$body_part_id
		order by barcode_no";*/

		$sql="SELECT a.id, a.entry_form, a.receive_basis, a.booking_id, b.prod_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.floor_id, b.room, b.rack, b.self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 1 as type, a.store_id, d.gsm, d.dia_width, d.detarmination_id, d.item_description, d.product_name_details, b.body_part_id, b.yarn_rate, b.kniting_charge, c.rate
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, product_details_master d
		WHERE a.id=b.mst_id and b.id=c.dtls_id and b.prod_id = d.id and a.entry_form in(2,22,58,84) and b.trans_id<>0 and c.entry_form in(2,22,58,84) and c.booking_without_order=1 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$booking_order_id and b.body_part_id=$body_part_id  $barcodeNo_cond and c.re_transfer =0

		UNION ALL

		SELECT a.id,a.entry_form, 0 as receive_basis, 0 as booking_id, b.to_prod_id as prod_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.to_floor_id as floor_id, b.to_room as room, b.to_rack as rack, b.to_shelf as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 3 as type , b.to_store as store_id, d.gsm, d.dia_width, d.detarmination_id, d.item_description, d.product_name_details, b.to_body_part as body_part_id, b.yarn_rate, b.kniting_charge, c.rate
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, product_details_master d
		where a.id=b.mst_id and b.id=c.dtls_id and b.to_prod_id = d.id and a.entry_form in(110,180) and c.entry_form in(110,180) and c.status_active=1 and c.is_deleted=0 
		and a.to_order_id =$booking_order_id and b.to_body_part=$body_part_id $barcodeNo_cond and c.booking_without_order=1 and c.re_transfer =0

		UNION ALL

		SELECT a.id,a.entry_form, 0 as receive_basis, 0 as booking_id, b.to_prod_id as prod_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.to_floor_id as floor_id, b.to_room as room, b.to_rack as rack, b.to_shelf as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 3 as type , b.to_store as store_id, d.gsm, d.dia_width, d.detarmination_id, d.item_description, d.product_name_details, b.to_body_part as body_part_id, b.yarn_rate, b.kniting_charge, c.rate
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, product_details_master d
		where a.id=b.mst_id and b.id=c.dtls_id and b.to_prod_id = d.id and a.entry_form in(82) and c.entry_form in(82) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id =$booking_order_id and b.to_body_part=$body_part_id  and c.booking_without_order=1 and c.re_transfer=0
		order by barcode_no";
	}
	// echo $sql;
	$data_array=sql_select($sql);	
	$i=1;$barcod_NOs="";
	foreach($data_array as $row)
	{
		$barcod_NOs.=$row[csf('barcode_no')].",";
	}  
	// echo $barcod_NOs.'BAER';die;
	$barcod_NOs=chop($barcod_NOs,",");
	$barcodeData = sql_select("select barcode_no,entry_form from pro_roll_details where entry_form=61 and status_active=1 and is_deleted=0 and barcode_no in($barcod_NOs) and is_returned = 0");
	foreach ($barcodeData as $row) 
	{
		$issued_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
		//$issued_barcode_arr2[$row[csf('barcode_no')]] = $row[csf('entry_form')];
	}
	/*echo "<pre>";
	print_r($issued_barcode_arr);die;*/
	foreach($data_array as $row)
	{
		if($issued_barcode_arr[$row[csf('barcode_no')]]=="")
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		
			$ycount='';
			$count_id=explode(',',$row[csf('yarn_count')]);
			foreach($count_id as $count)
			{
				if ($ycount=='') $ycount=$count_arr[$count]; else $ycount.=",".$count_arr[$count];
			}
			
			$transRollId=$row[csf('roll_id')];

			if ($transfer_criteria==6) 
			{
				$program_no='';
				if($row[csf('entry_form')]==2)
				{
					if($row[csf('receive_basis')]==2) $program_no=$row[csf('booking_id')];
				}
				else if($row[csf('entry_form')]==58 || $row[csf('entry_form')]==82 || $row[csf('entry_form')]==83)
				{
					$program_no=$programArr[$delv_arr[$row[csf('barcode_no')]]];
					$row[csf('roll_id')]=$row[csf('roll_id_prev')];
				}
			}
			else
			{
				$program_no='';
				if($row[csf('entry_form')]==2)
				{
					if($row[csf('receive_basis')]==2) $program_no=$row[csf('booking_id')];
				}
				else if($row[csf('entry_form')]==58)
				{
					$program_no=$programArr[$delv_arr[$row[csf('barcode_no')]]];
					$row[csf('roll_id')]=$row[csf('roll_id_prev')];
				}
			}				
			
			?>
			<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
				<td width="40" align="center" valign="middle"><input type="checkbox" id="tbl_<? echo $i;?>" name="check[]" checked/></td> 
				<td width="40"><? echo $i; ?></td>
				<td width="80"><? echo $row[csf('barcode_no')]; ?></td>
				<td width="50"><? echo $row[csf('roll_no')]; ?></td>
				<td width="70"><p><? echo $program_no; ?>&nbsp;</p></td>
				<td width="60"><p><? echo $row[csf('prod_id')]; ?></p></td>
				<td width="180"><p><? echo $row[csf('product_name_details')]; ?>&nbsp;</p></td>
				<td width="80"><p><? echo $ycount; ?>&nbsp;</p></td>
				<td width="70"><p><? echo $brand_arr[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
				<td width="80"><p><? echo $row[csf('yarn_lot')]; ?>&nbsp;</p></td>
				<td width="55"><p><? echo create_drop_down("floor_$i", 55, "","",1, "--Select Floor--", "", "" ); ?>&nbsp;</p></td>
				<td width="55"><p><? echo create_drop_down( "room_$i", 55, "","",1, "--Select Room--", "", "" ); ?>&nbsp;</p></td>
				<td width="55"><p><? echo create_drop_down( "rack_$i", 55, "","",1, "--Select Rack--", "", "" ); ?>&nbsp;</p></td>
				<td width="55"><p><? echo create_drop_down( "shelf_$i", 55, "","",1, "--Select Shelf--", "", "" ); ?>&nbsp;</p></td>
				<td width="80"><p><? echo $row[csf('stitch_length')]; ?>&nbsp;</p></td>
				<td width="60" align="right" style="padding-right:2px"><? echo $row[csf('qc_pass_qnty_pcs')]*1; ?>
                <td align="right" style="padding-right:2px"><? echo $row[csf('qnty')]; ?>
                	<input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $i; ?>" value="<? echo $row[csf('barcode_no')]; ?>"/>
                    <input type="hidden" name="rollNo[]" id="rollNo_<? echo $i; ?>" value="<? echo $row[csf('roll_no')]; ?>"/>
                    <input type="hidden" name="progId[]" id="progId_<? echo $i; ?>" value="<? echo $program_no; ?>"/>
                    <input type="hidden" name="productId[]" id="productId_<? echo $i; ?>" value="<? echo $row[csf('prod_id')]; ?>"/>
                    <input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $row[csf('roll_id')]; ?>"/>
                    <input type="hidden" name="rollWgt[]" id="rollWgt_<? echo $i; ?>" value="<? echo $row[csf('qnty')]; ?>"/>
                    <input type="hidden" name="yarnLot[]" id="yarnLot_<? echo $i; ?>" value="<? echo $row[csf('yarn_lot')]; ?>"/>
                    <input type="hidden" name="yarnCount[]" id="yarnCount_<? echo $i; ?>" value="<? echo $row[csf('yarn_count')]; ?>"/>
                    <input type="hidden" name="stichLn[]" id="stichLn_<? echo $i; ?>" value="<? echo $row[csf('stitch_length')]; ?>"/>
                    <input type="hidden" name="brandId[]" id="brandId_<? echo $i; ?>" value="<? echo $row[csf('brand_id')]; ?>"/>
                    <input type="hidden" name="floor[]" id="floor_<? echo $i; ?>" value="<? echo $row[csf('floor_id')]; ?>"/>
                    <input type="hidden" name="room[]" id="room_<? echo $i; ?>" value="<? echo $row[csf('room')]; ?>"/>
                    <input type="hidden" name="rack[]" id="rack_<? echo $i; ?>" value="<? echo $row[csf('rack')]; ?>"/>
                    <input type="hidden" name="shelf[]" id="shelf_<? echo $i; ?>" value="<? echo $row[csf('self')]; ?>"/>
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value=""/>
                    <input type="hidden" name="transIdFrom[]" id="transIdFrom_<? echo $i; ?>" value=""/>
                    <input type="hidden" name="transIdTo[]" id="transIdTo_<? echo $i; ?>" value=""/>
                    <input type="hidden" name="rolltableId[]" id="rolltableId_<? echo $i; ?>" value=""/>
                    <input type="hidden" name="transRollId[]" id="transRollId_<? echo $i; ?>" value="<? echo $transRollId ?>"/>
                    <input type="hidden" name="storeId[]" id="storeId_<? echo $i; ?>" value="<? echo $row[csf('store_id')]; ?>"/>
                    <input type="hidden" name="hiddenQtyInPcs[]" id="hiddenQtyInPcs_<? echo $i; ?>" value="<? echo $row[csf('qc_pass_qnty_pcs')]*1; ?>"/>
                </td>
			</tr>
			<? 
			$i++; 
		}
	} 
	exit();
}

//done
if($action=="show_transfer_listview")
{
	$data=explode("**",$data);

	$mst_id=$data[0];
	$order_id=$data[1];
	$cbo_transfer_criteria=$data[2];
	if($cbo_transfer_criteria==8) // Sample to Sample
	{
		$entry_form_no = 180;
	}
	elseif($cbo_transfer_criteria==7) // Sample to Order
	{
		$entry_form_no = 183;
	}
	else
	{
		$entry_form_no = 110; // // Order to Sample
	}

	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');	
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');	
	$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
	$delv_arr=return_library_array("select barcode_num, grey_sys_id from pro_grey_prod_delivery_dtls where entry_form=56 and order_id=$order_id", "barcode_num", "grey_sys_id");
	$trans_arr=return_library_array("select barcode_no, mst_id from pro_roll_details where entry_form=2", "barcode_no", "mst_id");
	// $re_trans_arr=return_library_array("select barcode_no from pro_roll_details where entry_form=$entry_form_no and re_transfer=1 and status_active=1 and is_deleted=0 and mst_id=$mst_id ", "barcode_no", "barcode_no");

	//$issued_barcode_arr=return_library_array("select barcode_no from pro_roll_details where entry_form=61 and status_active=1 and is_deleted=0 and po_breakdown_id=$order_id and is_returned = 0","barcode_no", "barcode_no");
	
	$programArr=return_library_array("SELECT a.id, a.booking_id from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=2 and a.receive_basis=2 and b.entry_form=2 and b.po_breakdown_id=$order_id and b.status_active=1 and b.is_deleted=0", "id", "booking_id");

	$sql="SELECT a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.floor_id, b.room, b.to_rack as rack, b.to_shelf as self, b.trans_id as roll_id, b.barcode_no, b.to_order_id as po_breakdown_id, b.roll as roll_no, b.transfer_qnty as qnty, b.roll_id as roll_id_prev, 3 as type , b.to_store as store_id, b.id as dtls_id, b.trans_id, b.to_trans_id, b.qty_in_pcs, b.from_program
	from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b
	WHERE a.id=b.mst_id and a.entry_form in($entry_form_no) and b.entry_form in($entry_form_no) and b.status_active=1 and b.is_deleted=0 and b.mst_id=$mst_id and a.transfer_criteria=$cbo_transfer_criteria
	order by barcode_no";
	
	//echo $sql;
	
	$data_array=sql_select($sql);	
	$i=1;
	foreach($data_array as $row)
	{  
		//if($issued_barcode_arr[$row[csf('barcode_no')]]=="")
		//{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		
			$ycount='';
			$count_id=explode(',',$row[csf('yarn_count')]);
			foreach($count_id as $count)
			{
				if ($ycount=='') $ycount=$count_arr[$count]; else $ycount.=",".$count_arr[$count];
			}
			
			$transRollId=$row[csf('roll_id')];
			$dtls_id=$row[csf('dtls_id')];
			$from_trans_id=$row[csf('trans_id')];
			$to_trans_id=$row[csf('to_trans_id')];
			$rolltableId=$row[csf('roll_id_prev')];
			$program_no=$row[csf('from_program')];
			/*if($row[csf('entry_form')]==2)
			{
				if($row[csf('receive_basis')]==2) $program_no=$row[csf('booking_id')];
			}
			else if($row[csf('entry_form')]==58)
			{
				$program_no=$programArr[$delv_arr[$row[csf('barcode_no')]]];
				$row[csf('roll_id')]=$row[csf('roll_id_prev')];
			}*/
			
			$checked="checked"; 
			
			/*if($re_trans_arr[$row[csf('barcode_no')]]=="")
			{
				$disabled=""; 	
			}
			else*/ 
			$disabled="disabled";
			?>
			<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
				<td width="40" align="center" valign="middle"><input type="checkbox" id="tbl_<? echo $i;?>" <? echo $checked." ".$disabled; ?> name="check[]" /></td> 
				<td width="40"><? echo $i; ?></td>
				<td width="80"><? echo $row[csf('barcode_no')]; ?></td>
				<td width="50"><? echo $row[csf('roll_no')]; ?></td>
				<td width="70"><p><? echo $program_no; ?>&nbsp;</p></td>
				<td width="60"><p><? echo $row[csf('prod_id')]; ?></p></td>
				<td width="180"><p><? echo $product_arr[$row[csf('prod_id')]]; ?>&nbsp;</p></td>
				<td width="80"><p><? echo $ycount; ?>&nbsp;</p></td>
				<td width="70"><p><? echo $brand_arr[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
				<td width="80"><p><? echo $row[csf('yarn_lot')]; ?>&nbsp;</p></td>
				<td width="55"><p><? echo $row[csf('floor_id')]; ?>&nbsp;</p></td>
				<td width="55"><p><? echo $row[csf('room')]; ?>&nbsp;</p></td>
				<td width="55"><p><? echo $row[csf('rack')]; ?>&nbsp;</p></td>
				<td width="55"><p><? echo $row[csf('self')]; ?>&nbsp;</p></td>
				<td width="80"><p><? echo $row[csf('stitch_length')]; ?>&nbsp;</p></td>
				<td width="60" align="right" style="padding-right:2px"><? echo $row[csf('qty_in_pcs')]*1; ?>
                <td align="right" style="padding-right:2px"><? echo $row[csf('qnty')]; ?>
                
                	<input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $i; ?>" value="<? echo $row[csf('barcode_no')]; ?>"/>
                    <input type="hidden" name="rollNo[]" id="rollNo_<? echo $i; ?>" value="<? echo $row[csf('roll_no')]; ?>"/>
                    <input type="hidden" name="progId[]" id="progId_<? echo $i; ?>" value="<? echo $program_no; ?>"/>
                    <input type="hidden" name="productId[]" id="productId_<? echo $i; ?>" value="<? echo $row[csf('prod_id')]; ?>"/>
                    <input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $row[csf('roll_id')]; ?>"/>
                    <input type="hidden" name="rollWgt[]" id="rollWgt_<? echo $i; ?>" value="<? echo $row[csf('qnty')]; ?>"/>
                    <input type="hidden" name="yarnLot[]" id="yarnLot_<? echo $i; ?>" value="<? echo $row[csf('yarn_lot')]; ?>"/>
                    <input type="hidden" name="yarnCount[]" id="yarnCount_<? echo $i; ?>" value="<? echo $row[csf('yarn_count')]; ?>"/>
                    <input type="hidden" name="stichLn[]" id="stichLn_<? echo $i; ?>" value="<? echo $row[csf('stitch_length')]; ?>"/>
                    <input type="hidden" name="brandId[]" id="brandId_<? echo $i; ?>" value="<? echo $row[csf('brand_id')]; ?>"/>
                    <input type="hidden" name="floor[]" id="floor_<? echo $i; ?>" value="<? echo $row[csf('floor_id')]; ?>"/>
                    <input type="hidden" name="room[]" id="room_<? echo $i; ?>" value="<? echo $row[csf('room')]; ?>"/>
                    <input type="hidden" name="rack[]" id="rack_<? echo $i; ?>" value="<? echo $row[csf('rack')]; ?>"/>
                    <input type="hidden" name="shelf[]" id="shelf_<? echo $i; ?>" value="<? echo $row[csf('self')]; ?>"/>
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value="<? echo $dtls_id; ?>"/>
                    <input type="hidden" name="transIdFrom[]" id="transIdFrom_<? echo $i; ?>" value="<? echo $from_trans_id; ?>"/>
                    <input type="hidden" name="transIdTo[]" id="transIdTo_<? echo $i; ?>" value="<? echo $to_trans_id; ?>"/>
                    <input type="hidden" name="rolltableId[]" id="rolltableId_<? echo $i; ?>" value="<? echo $rolltableId; ?>"/>
                    <input type="hidden" name="transRollId[]" id="transRollId_<? echo $i; ?>" value="<? echo $transRollId ?>"/>
                    <input type="hidden" name="storeId[]" id="storeId_<? echo $i; ?>" value="<? echo $row[csf('store_id')]; ?>"/>
                    <input type="hidden" name="hiddenQtyInPcs[]" id="hiddenQtyInPcs_<? echo $i; ?>" value="<? echo $row[csf('qty_in_pcs')]*1; ?>"/>
                </td>
			</tr>
			<? 
			$i++; 
		//}
	} 
	exit();
}

if ($action=="sampleToOrderTransfer_popup")
{
	echo load_html_head_contents("Sample To Order Transfer Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value(data)
		{
			// alert(data);return;
			$('#transfer_id').val(data);
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:780px;">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:760px;margin-left:10px">
	        <legend>Enter search words</legend>
	            <table cellpadding="0" cellspacing="0" width="550" class="rpt_table">
	                <thead>
	                    <th>Search By</th>
	                    <th width="240" id="search_by_td_up">Please Enter Requisition ID</th>
	                    <th>
	                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
	                        <input type="hidden" name="transfer_id" id="transfer_id" class="text_boxes" value="">
	                    </th>
	                </thead>
	                <tr class="general">
	                    <td>
							<?
								$search_by_arr=array(1=>"Requisition ID",2=>"Challan No.");
								$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";
								echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
	                        ?>
	                    </td>
	                    <td id="search_by_td">
	                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
	                    </td>
	                    <td>
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_transfer_criteria; ?>, 'create_transfer_search_list_view', 'search_div', 'roll_wise_grey_fabric_sample_requisition_for_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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

//done
if($action=='create_transfer_search_list_view')
{
	$data = explode("_",$data);

	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	$transfer_criteria =$data[3];

	if($transfer_criteria==8) // Sample to Sample
	{
		$entry_form_no = 180;
	}
	elseif($transfer_criteria==7) // Sample to Order
	{
		$entry_form_no = 183;
	}
	else{
		$entry_form_no = 110; // // Order to Sample
	}
	
	if($search_by==1)
	{
		$search_field="transfer_system_id";
	}
	else {
		$search_field="challan_no";
	}
	
	if($db_type==0) $year_field="YEAR(insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	
 	$sql="SELECT id, transfer_prefix_number, $year_field transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category from inv_item_transfer_requ_mst where item_category=13 and company_id=$company_id and $search_field like '$search_string' and transfer_criteria=$transfer_criteria and entry_form=$entry_form_no and status_active=1 and is_deleted=0 order by id";
	//echo $sql;
	
	$arr=array(3=>$company_arr,5=>$item_transfer_criteria,6=>$item_category);

	echo  create_list_view("tbl_list_search", "Requisition ID,Year,Challan No,Company,Requisition Date,Requisition Criteria,Item Category", "80,70,100,80,90,130","750","250",0, $sql, "js_set_value", "id", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category", '','','0,0,0,0,3,0,0');
	
	exit();
}

if($action=='populate_data_from_transfer_master')
{
	$data_array=sql_select("SELECT a.transfer_criteria, a.transfer_system_id, a.challan_no, a.company_id, a.transfer_date, a.to_company, a.item_category, a.from_order_id, a.to_order_id, a.from_samp_dtls_id, a.to_samp_dtls_id, a.ready_to_approve, a.is_approved, b.body_part_id, b.to_body_part 
		from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b where a.id=b.mst_id and a.id='$data'");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row[csf("transfer_criteria")]."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value 		= '".$row[csf("ready_to_approve")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_company_id_to').value 			= '".$row[csf("to_company")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";
		echo "document.getElementById('cbo_from_body_part').value 			= '".$row[csf("body_part_id")]."';\n";
		echo "document.getElementById('cbo_to_body_part').value 			= '".$row[csf("to_body_part")]."';\n";
		echo "$('#is_approved').val(".$row[csf("is_approved")].");\n";	
		if($row[csf("is_approved")] == 1)	
		{
			echo "$('#approved').text('Approved');\n";
		}
		elseif($row[csf("is_approved")] == 3)	
		{
			echo "$('#approved').text('Partial Approved');\n";
		}
		else
		{
			echo "$('#approved').text('');\n";
	  	}
		$transfer_criteria=$row[csf("transfer_criteria")];
		if($transfer_criteria==8) // Sample to Sample
		{
			$from_order_book_id = $row[csf("from_samp_dtls_id")];
			$to_order_book_id = $row[csf("to_samp_dtls_id")]; 
		}
		elseif($transfer_criteria==7) // Sample to Order
		{
			$from_order_book_id = $row[csf("from_samp_dtls_id")];
			$to_order_book_id = $row[csf("to_order_id")];
			
		}
		else // Order to Sample
		{ 
			$from_order_book_id = $row[csf("from_order_id")];
			$to_order_book_id = $row[csf("to_samp_dtls_id")]; 
		}
		echo "get_php_form_data('".$from_order_book_id."**"."from"."**".$row[csf("transfer_criteria")]."**".$row[csf("body_part_id")]."','populate_data_from_sample','requires/roll_wise_grey_fabric_sample_requisition_for_transfer_controller');\n";
		echo "get_php_form_data('".$to_order_book_id."**".$row[csf("transfer_criteria")]."**".$row[csf("to_body_part")]."','populate_data_to_order','requires/roll_wise_grey_fabric_sample_requisition_for_transfer_controller');\n";
		/*echo "get_php_form_data('".$row[csf("from_order_id")]."**"."from"."**".$row[csf("transfer_criteria")]."','populate_data_from_sample','requires/roll_wise_grey_fabric_sample_requisition_for_transfer_controller');\n";
		echo "get_php_form_data('".$row[csf("to_order_id")]."**".$row[csf("transfer_criteria")]."','populate_data_to_order','requires/roll_wise_grey_fabric_sample_requisition_for_transfer_controller');\n";*/
		echo "$('#cbo_transfer_criteria').attr('disabled','disabled');\n";
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "$('#cbo_company_id_to').attr('disabled','disabled');\n";
		echo "$('#txt_from_order_book_no').attr('disabled','disabled');\n";
		echo "$('#txt_to_order_book_no').attr('disabled','disabled');\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_grey_transfer_entry',1,1);\n"; 
		exit();
	} // +"'**'"+'".$row[csf("transfer_criteria")]."'
}

if ($action=="orderInfo_popup")
{
	echo load_html_head_contents("Order Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

</head>

<body>
<div align="center" style="width:770px;">
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:760px;margin-left:15px">
        <legend><? echo ucfirst($type); ?> Order Info</legend>
        	<br>
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr bgcolor="#FFFFFF">
                    <td align="center"><? echo ucfirst($type); ?> Order No: <b><? echo $txt_to_order_book_no; ?></b></td>
                </tr>
            </table>
            <br>
            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="750" align="center">
                <thead>
                    <th width="40">SL</th>
                    <th width="100">Required</th>
                    <?
					if($type=="from")
					{ 
					?>
                        <th width="100">Knitted</th>
                        <th width="100">Issue to dye</th>
                    	<th width="100">Issue Return</th>
                        <th width="100">Transfer Out</th>
                        <th width="100">Transfer In</th>
                        <th>Remaining</th>
                    <?
					}
					else
					{
					?>
                        <th width="80">Yrn. Issued</th>
                        <th width="80">Yrn. Issue Rtn</th>
                        <th width="80">Knitted</th>
                        <th width="90">Issue Rtn.</th>
                        <th width="100">Transf. Out</th>
                        <th width="100">Transf. In</th>
                        <th>Shortage</th>
                    <?	
					}
					?>
                    
                </thead>
                <?
					$req_qty=return_field_value("sum(b.grey_fab_qnty) as grey_req_qnty","wo_booking_mst a, wo_booking_dtls b","a.booking_no=b.booking_no and a.item_category in(2,13) and b.po_break_down_id=$txt_to_order_book_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1","grey_req_qnty");
					
					$sql="select 
								sum(CASE WHEN entry_form ='3' THEN quantity ELSE 0 END) AS issue_qnty,
								sum(CASE WHEN entry_form ='5' THEN quantity ELSE 0 END) AS dye_issue_qnty,
								sum(CASE WHEN entry_form ='9' THEN quantity ELSE 0 END) AS return_qnty,
								sum(CASE WHEN entry_form ='13' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_out_qnty,
								sum(CASE WHEN entry_form ='13' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_in_qnty,
								sum(CASE WHEN trans_id<>0 and entry_form in(2,22) THEN quantity ELSE 0 END) AS knit_qnty
							from order_wise_pro_details where po_breakdown_id=$txt_to_order_book_id and status_active=1 and is_deleted=0";
					$dataArray=sql_select($sql);
					$remaining=0; $shoratge=0;
				?>
                <tr bgcolor="#EFEFEF">
                    <td>1</td>
                    <td align="right"><? echo number_format($req_qty,2); ?>&nbsp;</td>
                    <?
					if($type=="from")
					{
						$remaining=$dataArray[0][csf('issue_qnty')]-$dataArray[0][csf('return_qnty')]-$dataArray[0][csf('transfer_out_qnty')]+$dataArray[0][csf('transfer_in_qnty')]-$dataArray[0][csf('knit_qnty')];
					?>
                        <td align="right"><? echo number_format($dataArray[0][csf('knit_qnty')],2); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($dataArray[0][csf('dye_issue_qnty')],2); ?></td>
                        <td align="right"><? echo number_format($dataArray[0][csf('return_qnty')],2); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($dataArray[0][csf('transfer_in_qnty')],2); ?></td>
                    	<td align="right"><? echo number_format($dataArray[0][csf('transfer_out_qnty')],2); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($remaining,2); ?>&nbsp;</td>
                    <?
					}
					else
					{
						$shoratge=$req_qty-$dataArray[0][csf('issue_qnty')]-$dataArray[0][csf('return_qnty')]+$dataArray[0][csf('transfer_out_qnty')]-$dataArray[0][csf('transfer_in_qnty')];
					?>
                        <td align="right"><? echo number_format($dataArray[0][csf('issue_qnty')],2); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($dataArray[0][csf('return_qnty')],2); ?></td>
                        <td align="right"><? echo number_format($dataArray[0][csf('knit_qnty')],2); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($dataArray[0][csf('return_qnty')],2); ?></td>
                    	<td align="right"><? echo number_format($dataArray[0][csf('transfer_in_qnty')],2); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($dataArray[0][csf('transfer_out_qnty')],2); ?>&nbsp;</td>
                    	<td align="right"><? echo number_format($shoratge,2); ?>&nbsp;</td>
                    <?	
					}

					?>
                </tr>
            </table>
            <table>
				<tr>
                    <td align="center" >
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px" />
                    </td>
                </tr>
			</table>
		</fieldset>
	</form>
</div>    
</body>           
</html>
<?
exit();
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
    for($k=1;$k<=$total_row;$k++)
    { 
        $productId="productId_".$k;
        $prod_ids.=$$productId.",";
    }
    $prod_ids=implode(",",array_unique(explode(",",chop($prod_ids,','))));
    $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id in ($prod_ids) and transaction_type in (1,4,5)", "max_date");      
	$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
	$trans_date = date("Y-m-d", strtotime(str_replace("'","",$txt_transfer_date)));
	if ($trans_date < $max_recv_date) 
    {
        echo "20**Transfer Date Can not Be Less Than Last Receive Date Of These Lot";
        die;
	}
	$cbo_transfer_criteria=str_replace("'","",$cbo_transfer_criteria);
	if($cbo_transfer_criteria==8) // Sample to Sample
	{
		$entry_form_no = 180;
		$short_prefix_name="GFRSTSTE";
	}
	elseif($cbo_transfer_criteria==7) // Sample to Order
	{
		$entry_form_no = 183;
		$short_prefix_name="GFRSTOTE";
	}
	else{
		$entry_form_no = 110; // Order to Sample
		$short_prefix_name="GFROTSTE";
	}
    //echo "10**".$entry_form_no;die;
	
	/*
	|--------------------------------------------------------------------------
	| Insert
	|--------------------------------------------------------------------------
	|
	*/
	if( $operation==0 )
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		
		$transfer_recv_num=''; $transfer_update_id='';
		//echo "10**";
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			$id = return_next_id_by_sequence("ITEM_TRAN_REQU_MST_PK_SEQ", "inv_item_transfer_requ_mst", $con);
			$new_transfer_system_id = explode("*", return_next_id_by_sequence("ITEM_TRAN_REQU_MST_PK_SEQ", "inv_item_transfer_requ_mst",$con,1,$cbo_company_id,$short_prefix_name,$entry_form_no,date("Y",time()),13 ));
					 
			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, entry_form, transfer_criteria, to_company, from_order_id, from_samp_dtls_id, to_order_id, to_samp_dtls_id, ready_to_approve, item_category, inserted_by, insert_date";

			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$txt_transfer_date.",".$entry_form_no.",".$cbo_transfer_criteria.",".$cbo_company_id_to.",".$txt_from_order_book_id.",".$txt_from_order_book_dtls_id.",".$txt_to_order_book_id.",".$txt_to_order_book_dtls_id.",".$cbo_ready_to_approved.",13,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			//echo "10**insert into inv_item_transfer_requ_mst (".$field_array.") values ".$data_array;die;

			$transfer_recv_num=$new_transfer_system_id[0];
			$transfer_update_id=$id;
		}
		else
		{
			$field_array_update="challan_no*transfer_date*from_order_id*from_samp_dtls_id*to_order_id, to_samp_dtls_id*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$txt_from_order_book_id."*".$txt_from_order_book_dtls_id."*".$txt_to_order_book_id."*".$txt_to_order_book_dtls_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			//$rID=sql_update("inv_item_transfer_requ_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			$transfer_recv_num=str_replace("'","",$txt_system_id);
			$transfer_update_id=str_replace("'","",$update_id);
		}
		$rate=0;
		$amount=0;
		//$id_dtls=return_next_id( "id", "inv_item_transfer_dtls", 1 ) ; 
		$field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, item_category, transfer_qnty, roll, rate, transfer_value, uom, y_count, brand_id, yarn_lot, floor_id, room, rack, shelf, to_rack, to_shelf, from_program, to_program, stitch_length, from_store, to_store, barcode_no, from_order_id, to_order_id, roll_id, entry_form, inserted_by, insert_date,qty_in_pcs,body_part_id,to_body_part";
		//txt_to_order_book_id
		
		$rollIds='';
		for($j=1;$j<=$total_row;$j++)
		{
			$barcodeNo="barcodeNo_".$j;
			$rollNo="rollNo_".$j;
			$progId="progId_".$j;
			$productId="productId_".$j;
			$rollId="rollId_".$j;
			$rollWgt="rollWgt_".$j;
			$yarnLot="yarnLot_".$j;
			$yarnCount="yarnCount_".$j;
			$stichLn="stichLn_".$j;
			$brandId="brandId_".$j;
			$floor="floor_".$j;
			$room="room_".$j;
			$rack="rack_".$j;
			$shelf="shelf_".$j;
			$rollNo="rollNo_".$j;
			$transRollId="transRollId_".$j;
			$storeId="storeId_".$j;
			$rollIds.=$$transRollId.",";
			$hiddenQtyInPcs="hiddenQtyInPcs_".$j;
			
			$id_dtls = return_next_id_by_sequence("ITEM_TRAN_REQU_DTLS_PK_SEQ", "inv_item_transfer_requ_dtls", $con);
			$from_trans_id=0;			
			$id_trans=0;			
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$id_dtls.",".$transfer_update_id.",".$$transRollId.",".$id_trans.",".$$productId.",13,".$$rollWgt.",".$$rollNo.",".$rate.",".$amount.",12,".$$yarnCount.",".$$brandId.",".$$yarnLot.",".$$floor.",".$$room.",".$$rack.",".$$shelf.",".$$rack.",".$$shelf.",".$$progId.",".$$progId.",".$$stichLn.",".$$storeId.",".$$storeId.",".$$barcodeNo.",".$txt_from_order_book_id.",".$txt_to_order_book_id.",".$$rollId.",".$entry_form_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$hiddenQtyInPcs.",".$cbo_from_body_part.",".$cbo_to_body_part.")";

			$all_barcode .=$$barcodeNo.",";
		}

		$all_barcode = chop($all_barcode,",");

		$pre_requ = sql_select("select a.transfer_system_id, a.requisition_status,b.barcode_no from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b where a.id = b.mst_id and a.entry_form in (110,180,183) and b.barcode_no in ($all_barcode) and a.requisition_status=1 and b.status_active=1");

		if(!empty($pre_requ)){
			echo "20**".$pre_requ[0][csf('barcode_no')]." barcode found in another Requisition.\nRequisition no:".$pre_requ[0][csf('transfer_system_id')];
			die;
		}

		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_item_transfer_requ_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("inv_item_transfer_requ_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; 
		}
		
		
		$rID2=sql_insert("inv_item_transfer_requ_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		
		//echo "10**insert into inv_item_transfer_requ_mst (".$field_array.") values ".$data_array;die;
		// echo "10**insert into inv_item_transfer_requ_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		// echo "10**$rID##$rID2";die;
		
		/*
		|--------------------------------------------------------------------------
		| MYSQL Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
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
		
		/*
		|--------------------------------------------------------------------------
		| ORACLE Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
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
		//check_table_status( $_SESSION['menu_id'], 0 );
		disconnect($con);
		die;
	}
		
	/*
	|--------------------------------------------------------------------------
	| Update
	|--------------------------------------------------------------------------
	|
	*/
	else if ($operation==1)
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		//echo "10**".$entry_form_no.'Update';die;
		$field_array_update="challan_no*transfer_date*from_order_id*to_order_id*ready_to_approve*updated_by*update_date";
		$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$txt_from_order_book_id."*".$txt_to_order_book_id."*".$cbo_ready_to_approved."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rate=0;
		$amount=0;
		//$id_dtls=return_next_id( "id", "inv_item_transfer_dtls", 1 ) ; 
		$field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, item_category, transfer_qnty, roll, rate, transfer_value, uom, y_count, brand_id, yarn_lot, floor_id, room, rack, shelf, to_rack, to_shelf, from_program, to_program, stitch_length, from_store, to_store, barcode_no, inserted_by, insert_date, qty_in_pcs,body_part_id,to_body_part";	

		$field_array_dtls_update="from_prod_id*transfer_qnty*roll*rate*transfer_value*y_count*brand_id*yarn_lot*floor_id*room*rack*shelf*to_rack*to_shelf*from_program*to_program*stitch_length*barcode_no*updated_by*update_date*qty_in_pcs*body_part_id*to_body_part";
		
		$rollIds=''; $update_dtls_id='';
		for($j=1;$j<=$total_row;$j++)
		{
			$barcodeNo="barcodeNo_".$j;
			$rollNo="rollNo_".$j;
			$progId="progId_".$j;
			$productId="productId_".$j;
			$rollId="rollId_".$j;
			$rollWgt="rollWgt_".$j;
			$yarnLot="yarnLot_".$j;
			$yarnCount="yarnCount_".$j;
			$stichLn="stichLn_".$j;
			$brandId="brandId_".$j;
			$floor="floor_".$j;
			$room="room_".$j;
			$rack="rack_".$j;
			$shelf="shelf_".$j;
			$rollNo="rollNo_".$j;
			$dtlsId="dtlsId_".$j;
			$transIdFrom="transIdFrom_".$j;
			$transIdTo="transIdTo_".$j;
			$rolltableId="rolltableId_".$j;
			$transRollId="transRollId_".$j;
			$storeId="storeId_".$j;
			$rollIds.=$$transRollId.",";
			$hiddenQtyInPcs="hiddenQtyInPcs_".$j;
			
			if(str_replace("'","",$$rolltableId)>0)
			{
				$update_dtls_id.=str_replace("'","",$$dtlsId).",";
				
				$dtlsId_arr[]=str_replace("'","",$$dtlsId);
				$data_array_update_dtls[str_replace("'","",$$dtlsId)]=explode("*",($$productId."*".$$rollWgt."*".$$rollNo."*".$rate."*".$amount."*".$$yarnCount."*".$$brandId."*".$$yarnLot."*".$$floor."*".$$room."*".$$rack."*".$$shelf."*".$$rack."*".$$shelf."*".$$progId."*".$$progId."*".$$stichLn."*".$$barcodeNo."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$$hiddenQtyInPcs."*".$cbo_from_body_part."*".$cbo_to_body_part));
				
				$dtlsIdProp=str_replace("'","",$$dtlsId);
				$transIdfromProp=str_replace("'","",$$transIdFrom);
				$transIdtoProp=str_replace("'","",$$transIdTo);
			}
			else
			{
				$id_dtls = return_next_id_by_sequence("ITEM_TRAN_REQU_DTLS_PK_SEQ", "inv_item_transfer_requ_dtls", $con);
				
				$transIdfromProp=0;
				//$id_trans=$id_trans+1;
				
				if($data_array_dtls!="")
					$data_array_dtls.=",";
				$data_array_dtls.="(".$id_dtls.",".$update_id.",".$transIdfromProp.",".$transIdtoProp.",".$$productId.",13,".$$rollWgt.",".$$rollNo.",".$rate.",".$amount.",12,".$$yarnCount.",".$$brandId.",".$$yarnLot.",".$$floor.",".$$room.",".$$rack.",".$$shelf.",".$$rack.",".$$shelf.",".$$progId.",".$$progId.",".$$stichLn.",".$$storeId.",".$$storeId.",".$$barcodeNo.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$hiddenQtyInPcs.",".$cbo_from_body_part.",".$cbo_to_body_part.")";

				$dtlsIdProp=$id_dtls;
				//$id_dtls=$id_dtls+1;
				$all_trans_roll_id.=$$transRollId.",";
			}
		}

		$flag=1;

		if($txt_deleted_id!="")
		{
			// echo "10**5**$txt_deleted_id";die;
			$deletedIds=explode(",",$txt_deleted_id); $dtlsIDDel=''; $transIDDel=''; $rollIDDel=''; $rollIDactive=''; $delBarcodeNo='';
			foreach($deletedIds as $delIds)
			{
				$delIds=explode("_",$delIds);
				if($dtlsIDDel=="")
				{
					$dtlsIDDel=$delIds[0];
					$transIDDel=$delIds[1].",".$delIds[2];
					$rollIDDel=$delIds[3];
					$rollIDactive=$delIds[4];
					$delBarcodeNo=$delIds[5];
				}
				else
				{
					$dtlsIDDel.=",".$delIds[0];
					$transIDDel.=",".$delIds[1].",".$delIds[2];
					$rollIDDel.=",".$delIds[3];
					$rollIDactive.=",".$delIds[4];
					$delBarcodeNo.=",".$delIds[5];
				}
			}
			
			if($delBarcodeNo != "")
			{
				/*$check_sql=sql_select("SELECT a.barcode_no , b.issue_number as system_no, a.entry_form, 'Issue' as msg_source from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and a.entry_form = 61 and b.entry_form = 61 and a.is_returned != 1 and  a.status_active=1 and  b.status_active=1 and a.barcode_no in ($delBarcodeNo) 
				union all 
				select a.barcode_no , b.transfer_system_id as system_no, a.entry_form, 'Transfer' as msg_source from inv_item_transfer_requ_dtls a, inv_item_transfer_requ_mst b where a.mst_id = b.id and a.entry_form = $entry_form_no and b.entry_form = $entry_form_no and  a.status_active=1 and b.status_active=1 and a.barcode_no in ($delBarcodeNo) and a.id not in ($rollIDDel) ");

				$msg = "";
				foreach ($check_sql as $val) 
				{
					$msg .= $val[csf("msg_source")]." Found. Barcode :".$val[csf("barcode_no")]." chalan no: ".$val[csf("system_no")]."\n";
				}

				if($msg)
				{
					echo "20**".$msg;
					disconnect($con);
					die;
				}*/

				// =========================================================

				$splited_roll_sql=sql_select("SELECT barcode_no,split_from_id from pro_roll_split where status_active =1 and barcode_no in ($delBarcodeNo)");

				foreach($splited_roll_sql as $bar)
				{ 
					$splited_roll_ref[$bar[csf('barcode_no')]][$bar[csf('split_from_id')]]=$bar[csf('barcode_no')];
				}

				$child_split_sql=sql_select("SELECT barcode_no, id from pro_roll_details where roll_split_from >0 and barcode_no in ($delBarcodeNo) and entry_form = $entry_form_no order by barcode_no");
				foreach($child_split_sql as $bar)
				{ 
					$child_split_arr[$bar[csf('barcode_no')]][$bar[csf('id')]]=$bar[csf('barcode_no')];
				}
				
				foreach($deletedIds as $delIds)
				{
					$delIds=explode("_",$delIds);
					if($splited_roll_ref[$delIds[5]][$delIds[3]] !="" || $child_split_arr[$delIds[5]][$delIds[3]] != "")
					{
						echo "20**"."Split Found. barcode no: ".$delIds[5];
						disconnect($con);
						die;
					}
				}				
			}

			//echo "10**5##select from_roll_id from pro_roll_details where id in($rollIDDel)";die;

			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			// echo "10**".$field_array_status.'='.$data_array_status;die;
			$statusChangeDtls=sql_multirow_update("inv_item_transfer_requ_dtls",$field_array_status,$data_array_status,"id",$dtlsIDDel,0);
			//$active_prev_roll=sql_multirow_update("pro_roll_details","re_transfer","0","id",$prev_rol_id,0);
			
			if($flag==1)
			{
				if($statusChangeDtls) $flag=1; else $flag=0; 
			} 
		}
		
		$rID=sql_update("inv_item_transfer_requ_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($flag==1) 
		{
			if($rID) $flag=1; else $flag=0;
		}

		if(count($data_array_update_dtls)>0)
		{
			
			$rID2=execute_query(bulk_update_sql_statement("inv_item_transfer_requ_dtls","id",$field_array_dtls_update,$data_array_update_dtls,$dtlsId_arr));
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			}			
		}
		
		if($data_array_dtls!="")
		{
			
			$rIDDtls=sql_insert("inv_item_transfer_requ_dtls",$field_array_dtls,$data_array_dtls,0);
			if($flag==1) 
			{
				if($rIDDtls) $flag=1; else $flag=0; 
			} 
			
			//echo $flag;die;
			//echo "insert into inv_item_transfer_requ_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		}

		//echo "10**".$rID.'**'.$rID2.'**'.$rIDDtls;die; 

		/*
		|--------------------------------------------------------------------------
		| MYSQL Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
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
		
		/*
		|--------------------------------------------------------------------------
		| ORACLE Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
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
		//check_table_status( $_SESSION['menu_id'], 0 );
		disconnect($con);
		die;
 	}
}

if ($action=="grey_fabric_order_to_order_transfer_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data); die();
	$cbo_transfer_criteria=$data[3];	

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=13","id","product_name_details");
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');	
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');	
	$style_name_array=return_library_array( "select id, style_ref_no from sample_development_mst",'id','style_ref_no');

	$poDataArray=sql_select("SELECT b.id,a.buyer_name,a.style_ref_no,a.job_no,b.po_number,b.pub_shipment_date, b.file_no, b.grouping as ref_no, (a.total_set_qnty*b.po_quantity) as qty from wo_po_break_down b,wo_po_details_master a where  a.job_no=b.job_no_mst and a.company_name=$data[0] and b.status_active=1 and b.is_deleted=0 ");
	$job_array=array(); //$all_job_id='';
	foreach($poDataArray as $row)
	{
		$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
		$job_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['no']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['date']=$row[csf('pub_shipment_date')];
		$job_array[$row[csf('id')]]['qty']=$row[csf('qty')];
		$job_array[$row[csf('id')]]['file']=$row[csf('file_no')];
		$job_array[$row[csf('id')]]['ref']=$row[csf('ref_no')];
	} 

	$sql="SELECT id, transfer_system_id, transfer_date, challan_no, from_order_id, to_order_id, item_category from inv_item_transfer_requ_mst a where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;
	$dataArray=sql_select($sql);

	$sampledata_array=sql_select("SELECT a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.style_id, b.body_part, b.grey_fabric
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.id=".$dataArray[0][csf('from_order_id')]."");

	$sampledata_to_array=sql_select("SELECT a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.style_id, b.body_part, b.grey_fabric
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.id=".$dataArray[0][csf('to_order_id')]."");

	/*// Sample to Sample
	$sampledata_from_array=sql_select("SELECT a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.style_id, b.body_part, b.grey_fabric
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.id=".$dataArray[0][csf('from_order_id')]."");
	
	$sampledata_to_array=sql_select("SELECT a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.style_id, b.body_part, b.grey_fabric
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.id=".$dataArray[0][csf('to_order_id')]."");

	// Order to Sample
	$sampledata_array=sql_select("SELECT a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.style_id, b.body_part, b.grey_fabric
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.id=".$dataArray[0][csf('to_order_id')]."");*/

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
							Plot No: <? echo $result[csf('plot_no')]; ?> 
							Level No: <? echo $result[csf('level_no')]?>
							Road No: <? echo $result[csf('road_no')]; ?> 
							Block No: <? echo $result[csf('block_no')];?> 
							City No: <? echo $result[csf('city')];?> 
							Zip Code: <? echo $result[csf('zip_code')]; ?> 
							Email Address: <? echo $result[csf('email')];?> 
							Website No: <? echo $result[csf('website')];
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
	    </table>
	    <table width="900" cellspacing="0" align="right" style="margin-top:5px;">
	    	<tr>
	    		<?
	    		if($cbo_transfer_criteria==8) // Sample to Sample
				{
					$entry_form_no = 180;
					?>
					<td>
		                <table width="100%" cellspacing="0" align="right">
		                 	<tr>
		                    	<td colspan="4" align="center" style="font-weight:bold; font-size:18px;"><u><? echo $from_title; ?></u></td>
		                    </tr>
		                    <tr>
		                    	<td width="100">Booking No:</td>
		                        <td width="340" colspan="3">&nbsp;<? echo $sampledata_array[0][csf('booking_no')]; ?></td>
		                    </tr>
		                    <tr>
		                    	<td>Buyer:</td>
		                        <td >&nbsp;<? echo $buyer_library[$sampledata_array[0][csf('buyer_id')]]; ?></td>
		                        <td width="100">Quantity:</td>
		                        <td>&nbsp;<? echo $sampledata_array[0][csf('grey_fabric')]; ?></td>
		                    </tr>
		                    <tr>
		                    	<td>Style Ref. :</td>
		                        <td colspan="3">&nbsp;<? echo $style_name_array[$sampledata_array[0][csf('style_id')]]; ?></td>  
		                    </tr> 
		                    <tr>
		                    	<td>Body Part:</td>
		                        <td colspan="3">&nbsp;<? echo $body_part[$sampledata_array[0][csf('body_part')]]; ?></td>
		                    </tr>
		                </table>
		            </td>
		        	<td width="450">
		                <table width="100%" cellspacing="0" align="right">
		                 	<tr>
		                    	<td colspan="4" align="center" style="font-weight:bold; font-size:18px;"><u>To Sample</u></td>
		                    </tr>
		                    <tr>
		                    	<td width="100">Booking No:</td>
		                        <td width="340" colspan="3">&nbsp;<? echo $sampledata_to_array[0][csf('booking_no')]; ?></td>
		                       
		                    </tr>
		                    <tr>
		                    	<td>Buyer:</td>
		                        <td >&nbsp;<? echo $buyer_library[$sampledata_to_array[0][csf('buyer_id')]]; ?></td>
		                        <td width="100">Quantity:</td>
		                        <td>&nbsp;<? echo $sampledata_to_array[0][csf('grey_fabric')]; ?></td>
		                        
		                    </tr>
		                    <tr>
		                    	<td>Style Ref. :</td>
		                        <td colspan="3">&nbsp;<? echo $style_name_array[$sampledata_to_array[0][csf('style_id')]]; ?></td>
		                        
		                    </tr> 
		                    <tr>
		                    	<td>Body Part:</td>
		                        <td colspan="3">&nbsp;<? echo $body_part[$sampledata_to_array[0][csf('body_part')]]; ?></td>
		              
		                    </tr> 
		                </table>
		            </td>
					<?
				}
				elseif($cbo_transfer_criteria==7) // Sample to Order
				{
					$entry_form_no = 183;
					?>
					<td>
		                <table width="100%" cellspacing="0" align="right">
		                 	<tr>
		                    	<td colspan="4" align="center" style="font-weight:bold; font-size:18px;"><u>Form Sample</u></td>
		                    </tr>
		                    <tr>
		                    	<td width="100">Booking No:</td>
		                        <td width="340" colspan="3">&nbsp;<? echo $sampledata_array[0][csf('booking_no')]; ?></td>
		                       
		                    </tr>
		                    <tr>
		                    	<td>Buyer:</td>
		                        <td >&nbsp;<? echo $buyer_library[$sampledata_array[0][csf('buyer_id')]]; ?></td>
		                        <td width="100">Quantity:</td>
		                        <td>&nbsp;<? echo $sampledata_array[0][csf('grey_fabric')]; ?></td>
		                        
		                    </tr>
		                    <tr>
		                    	<td>Style Ref. :</td>
		                        <td colspan="3">&nbsp;<? echo $style_name_array[$sampledata_array[0][csf('style_id')]]; ?></td>
		                        
		                    </tr> 
		                    <tr>
		                    	<td>Body Part:</td>
		                        <td colspan="3">&nbsp;<? echo $body_part[$sampledata_array[0][csf('body_part')]]; ?></td>
		              
		                    </tr>
		                </table>
		            </td>
		        	<td width="450">
		                <table width="100%" cellspacing="0" align="right">
		                 	<tr>
		                    	<td colspan="4" align="center" style="font-weight:bold; font-size:18px;"><u>To Order</u></td>
		                    </tr>
		                    <tr>
		                    	<td width="100">Order No:</td>
		                        <td width="120">&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['no']; ?></td>
		                        <td width="100">Quantity:</td>
		                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['qty']; ?></td>
		                    </tr>
		                    <tr>
		                    	<td>Buyer:</td>
		                        <td>&nbsp;<? echo $buyer_library[$job_array[$dataArray[0][csf('to_order_id')]]['buyer']]; ?></td>
		                        <td>Job No:</td>
		                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['job']; ?></td>
		                    </tr>
		                    <tr>
		                    	<td>File No:</td>
		                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['file']; ?></td>
		                        <td>Ref. No:</td>
		                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['ref']; ?></td>
		                    </tr> 
		                    <tr>
		                    	<td>Style Ref:</td>
		                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['style']; ?></td>
		                        <td>Ship. Date:</td>
		                        <td>&nbsp;<? echo change_date_format($job_array[$dataArray[0][csf('to_order_id')]]['date']); ?></td>
		                    </tr>  
		                </table>
		            </td>
					<?
				}
				else // Order TO Sample
				{
					$entry_form_no = 110;
					?>
					<td>
		                <table width="100%" cellspacing="0" align="right">
		                 	<tr>
		                    	<td colspan="4" align="center" style="font-weight:bold; font-size:18px;"><u>From Order</u></td>
		                    </tr>
		                    <tr>
		                    	<td width="100">Order No:</td>
		                        <td width="120">&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['no']; ?></td>
		                        <td width="100">Quantity:</td>
		                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['qty']; ?></td>
		                    </tr>
		                    <tr>
		                    	<td>Buyer:</td>
		                        <td>&nbsp;<? echo $buyer_library[$job_array[$dataArray[0][csf('from_order_id')]]['buyer']]; ?></td>
		                        <td>Job No:</td>
		                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['job']; ?></td>
		                    </tr>
		                    <tr>
		                    	<td>File No:</td>
		                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['file']; ?></td>
		                        <td>Ref. No:</td>
		                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['ref']; ?></td>
		                    </tr> 
		                    <tr>
		                    	<td>Style Ref:</td>
		                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['style']; ?></td>
		                        <td>Ship. Date:</td>
		                        <td>&nbsp;<? echo change_date_format($job_array[$dataArray[0][csf('from_order_id')]]['date']); ?></td>
		                    </tr>
		                </table>
		            </td>
		        	<td width="450">
		                <table width="100%" cellspacing="0" align="right">
		                 	<tr>
		                    	<td colspan="4" align="center" style="font-weight:bold; font-size:18px;"><u>To Sample</u></td>
		                    </tr>
		                    <tr>
		                    	<td width="100">Booking No:</td>
		                        <td width="340" colspan="3">&nbsp;<? echo $sampledata_to_array[0][csf('booking_no')]; ?></td>
		                       
		                    </tr>
		                    <tr>
		                    	<td>Buyer:</td>
		                        <td >&nbsp;<? echo $buyer_library[$sampledata_to_array[0][csf('buyer_id')]]; ?></td>
		                        <td width="100">Quantity:</td>
		                        <td>&nbsp;<? echo $sampledata_to_array[0][csf('grey_fabric')]; ?></td>
		                        
		                    </tr>
		                    <tr>
		                    	<td>Style Ref. :</td>
		                        <td colspan="3">&nbsp;<? echo $style_name_array[$sampledata_to_array[0][csf('style_id')]]; ?></td>
		                        
		                    </tr> 
		                    <tr>
		                    	<td>Body Part:</td>
		                        <td colspan="3">&nbsp;<? echo $body_part[$sampledata_to_array[0][csf('body_part')]]; ?></td>
		              
		                    </tr>  
		                </table>
		            </td>
					<?
				}
	    		?>
	    		           
	        </tr>
	    </table>
		<br>
	    <div style="width:100%;">
	        <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
	            <thead bgcolor="#dddddd" align="center">
	                <th width="30">SL</th>
	                <th width="80">Barcode No</th>
	                <th width="60">Roll No</th>
	                <th width="180">Fabric Description</th>
	                <th width="80">Y/Count</th>
	                <th width="70">Y/Brand</th>
	                <th width="80">Y/Lot</th>
	                <th width="55">Rack</th>
	                <th width="55">Shelf</th>
	                <th width="70">Stitch Length</th>
	                <th width="60">UOM</th>
	                <th width="100">Transfered Qnty</th>
	            </thead>
	            <tbody> 
				<?
	            /*$sql_dtls="SELECT a.from_prod_id, a.transfer_qnty, a.uom, a.y_count, a.brand_id, a.yarn_lot, a.to_rack, a.to_shelf, a.stitch_length, b.barcode_no, b.roll_no 
	            from inv_item_transfer_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=$entry_form_no and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.barcode_no";*/
				$sql_dtls="SELECT b.from_prod_id, b.transfer_qnty, b.uom, b.y_count, b.brand_id, b.yarn_lot, b.to_rack, b.to_shelf, b.stitch_length, b.barcode_no, b.roll as roll_no
				from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b
				where a.id=b.mst_id and a.entry_form=$entry_form_no and a.id='$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.barcode_no";
	            
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
						
	                $ycount='';
					$count_id=explode(',',$row[csf('y_count')]);
					foreach($count_id as $count)
					{
						if ($ycount=='') $ycount=$count_arr[$count]; else $ycount.=",".$count_arr[$count];
					}   
	                ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>">
	                        <td align="center"><? echo $i; ?></td>
	                        <td><? echo $row[csf("barcode_no")]; ?></td>
	                        <td><? echo $row[csf("roll_no")]; ?></td>
	                        <td><? echo $product_arr[$row[csf("from_prod_id")]]; ?></td>
	                        <td><? echo $ycount; ?></td>
	                        <td><? echo $brand_arr[$row[csf("brand_id")]]; ?></td>
	                        <td><? echo $row[csf("yarn_lot")]; ?></td>
	                        <td><? echo $row[csf("to_rack")]; ?></td>
	                        <td><? echo $row[csf("to_shelf")]; ?></td>
	                        <td><? echo $row[csf("stitch_length")]; ?></td>
	                        <td align="center"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
	                        <td align="right"><? echo $row[csf("transfer_qnty")]; ?></td>
	                    </tr>
				<? 
	            	$i++; 
	            }
	            ?>
	            </tbody>
	            <tfoot>
	                <tr>
	                    <td colspan="11" align="right"><strong>Total </strong></td>
	                    <td align="right"><?php echo $transfer_qnty_sum; ?></td>
	                </tr>                           
	            </tfoot>
	        </table>
	        <br>
	        <div style="margin-left: 30px;">
			<?
	            echo signature_table(19, $data[0], "900px");
	        ?>
	        </div>
		</div>
	</div>   
	<?	
	exit();
}
?>
