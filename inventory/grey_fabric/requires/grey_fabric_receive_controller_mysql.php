<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


 //-------------------START ----------------------------------------
$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
$count_arr = return_library_array("select id, yarn_count from lib_yarn_count","id","yarn_count");
$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");
$buyer_arr = return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
$company_arr = return_library_array("select id, company_name from lib_company","id","company_name");

if($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[1]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",$data[0] );  
	exit();
	
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 151, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_drop_down( 'requires/grey_fabric_receive_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );" );
	exit();
}

if ($action=="load_drop_down_store")
{
	$data=explode("_",$data);
	if($data[1]==2) $category_id=13; else $category_id=14;
	echo create_drop_down( "cbo_store_name", 152, "select id, store_name from lib_store_location where company_id='$data[0]' and find_in_set($category_id,item_category_id) and status_active=1 and is_deleted=0 order by store_name","id,store_name", 1, "--Select store--", 0, "" );
	exit();
}

if ($action=="load_drop_down_floor")
{
	$data=explode("_",$data);
	$company_id=$data[0];
	$location_id=$data[1];
	if($location_id==0 || $location_id=="") $location_cond=""; else $location_cond=" and b.location_id=$location_id";
	
	echo create_drop_down( "cbo_floor_id", 132, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1 and b.company_id=$company_id and b.status_active=1 and b.is_deleted=0 and a.production_process=2 $location_cond group by a.id order by a.floor_name","id,floor_name", 1, "-- Select Floor --", 0, "load_drop_down( 'requires/grey_fabric_receive_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_machine', 'machine_td' );","" );
  exit();	 
}


if ($action=="load_drop_machine")
{
	$data=explode("_",$data);
	$company_id=$data[0];
	$floor_id=$data[1];
	if($floor_id==0 || $floor_id=="") $floor_cond=""; else $floor_cond=" and floor_id=$floor_id";
	
	echo create_drop_down( "cbo_machine_name", 132, "select id,concat(machine_no,'-',brand) as machine_name from lib_machine_name where category_id=1 and company_id=$company_id and status_active=1 and is_deleted=0 and is_locked=0 $floor_cond order by machine_name","id,machine_name", 1, "-- Select Machine --", 0, "","" );
	exit();
}


if($action=="load_drop_down_knitting_com")
{
	$data = explode("_",$data);
	$company_id=$data[1];
	
	if($data[0]==1)
	{
		echo create_drop_down( "cbo_knitting_company", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Knit Company--", "$company_id", "","" );
	}
	else if($data[0]==3)
	{	
		echo create_drop_down( "cbo_knitting_company", 152, "select id, supplier_name from lib_supplier where find_in_set(20,party_type) and find_in_set($company_id,tag_company) and status_active=1 and is_deleted=0","id,supplier_name", 1, "--Select Knit Company--", 1, "" );
	}
	else
	{
		echo create_drop_down( "cbo_knitting_company", 152, $blank_array,"",1, "--Select Knit Company--", 1, "" );
	}
	exit();
}

if ($action=="wo_pi_production_popup")
{
	echo load_html_head_contents("WO/PI/Production Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
	
		function js_set_value(id,no,type,buyer_id)
		{
			$('#hidden_wo_pi_production_id').val(id);
			$('#hidden_wo_pi_production_no').val(no);
			$('#booking_without_order').val(type);
			$('#hidden_buyer_id').val(buyer_id);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:830px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:930px; margin-left:3px">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="720" class="rpt_table">
                <thead>
                    <th>Search By</th>
                    <th width="240">Enter WO/PI/Pro. Recv. No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                    	<input type="hidden" name="hidden_wo_pi_production_id" id="hidden_wo_pi_production_id" class="text_boxes" value="">  
                        <input type="hidden" name="hidden_wo_pi_production_no" id="hidden_wo_pi_production_no" class="text_boxes" value=""> 
                        <input type="hidden" name="booking_without_order" id="booking_without_order" class="text_boxes" value="">
                        <input type="hidden" name="hidden_buyer_id" id="hidden_buyer_id" class="text_boxes" value=""> 
                    </th> 
                </thead>
                <tr>
                    <td align="center">	
                    	<?
							echo create_drop_down("cbo_receive_basis",152,$receive_basis_arr,"",1,"-- Select --",$recieve_basis,"","1","1,2,4,6,9");
						?>
                    </td>                 
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_receive_basis').value+'_'+document.getElementById('txt_company_id').value+'_'+<? echo $garments_nature; ?>, 'create_wo_pi_production_search_list_view', 'search_div', 'grey_fabric_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                     </td>
                </tr>
           </table>
            <div style="margin-top:10px;" id="search_div" align="left"></div> 
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_wo_pi_production_search_list_view")
{
	
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0])."%";
	$recieve_basis=$data[1];
	$company_id =$data[2];
	$category_id =$data[3];
	
	if($recieve_basis==1)
	{
		if(trim($data[0])!="")
		{
			$search_field_cond="and pi_number like '$search_string'";
		}
		else
		{
			$search_field_cond="";
		}
		
		if($data[3]==2) $category_id=13; else $category_id=14;
		
		$sql = "select id, pi_number, supplier_id, pi_date, last_shipment_date, pi_basis_id, internal_file_no, currency_id, source from com_pi_master_details where item_category_id='$category_id' and pi_basis_id=2 and status_active=1 and is_deleted=0 and importer_id=$company_id $search_field_cond"; 
		//echo $sql;
		$result = sql_select($sql);
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="125">PI No</th>
				<th width="80">PI Date</th>
                <th width="110">PI Basis</th>               
				<th width="160">Supplier</th>
				<th width="100">Last Shipment Date</th>
				<th width="100">Internal File No</th>
				<th width="80">Currency</th>
				<th>Source</th>
			</thead>
		</table>
		<div style="width:928px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">	 
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table" id="tbl_list_search">  
			<?
				$i=1;
				foreach ($result as $row)
				{  
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	 
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('pi_number')]; ?>','0','0');"> 
						<td width="30"><? echo $i; ?></td>
						<td width="125"><p><? echo $row[csf('pi_number')]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('pi_date')]); ?></td>  
                        <td width="110"><p><? echo $pi_basis[$row[csf('pi_basis_id')]]; ?>&nbsp;</p></td>             
						<td width="160"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
						<td width="100" align="center"><? echo change_date_format($row[csf('last_shipment_date')]); ?>&nbsp;</td>
						<td width="100"><p><? echo $row[csf('internal_file_no')]; ?>&nbsp;</p></td>
						<td width="80"><p><? echo $currency[$row[csf('currency_id')]]; ?>&nbsp;</p></td>
						<td><p><? echo $source[$row[csf('source')]]; ?>&nbsp;</p></td>
					</tr>
				<?
				$i++;
				}
				?>
			</table>
		</div>
	<?	
	}
	else if($recieve_basis==2)
	{
		if(trim($data[0])!="")
		{
			$search_field_cond="and a.booking_no like '$search_string'";
			$search_field_cond_sample="and s.booking_no like '$search_string'";
		}
		else
		{
			$search_field_cond="";
		}
		
		$sql = "select a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.buyer_id, a.po_break_down_id, a.item_category, a.delivery_date, b.job_no_mst, 0 as type from wo_booking_mst a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_id=$company_id and a.item_category=$category_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond group by a.id
				union all
				SELECT s.id, s.booking_no_prefix_num, s.booking_no, s.booking_date, s.buyer_id, '' as po_break_down_id, s.item_category, s.delivery_date, '' as job_no_mst, 1 as type FROM wo_non_ord_samp_booking_mst s WHERE s.company_id=$company_id and s.status_active =1 and s.is_deleted=0 and s.item_category=$category_id $search_field_cond_sample 
		"; 
		//echo $sql;die;
		$result = sql_select($sql);
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="115">Booking No</th>
				<th width="80">Booking Date</th>               
				<th width="100">Buyer</th>
				<th width="85">Item Category</th>
				<th width="80">Delivary date</th>
				<th width="90">Job No</th>
				<th width="90">Order Qnty</th>
				<th width="80">Shipment Date</th>
				<th>Order No</th>
			</thead>
		</table>
		<div style="width:928px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">	 
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table" id="tbl_list_search">  
			<?
				$i=1;
				foreach ($result as $row)
				{  
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	 
					
					$po_qnty_in_pcs=''; $po_no=''; $min_shipment_date='';
					
					if($row[csf('po_break_down_id')]!="")
					{
						$po_sql="select b.po_number, b.pub_shipment_date, b.po_quantity, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($row[po_break_down_id]) group by b.id";									
						$nameArray=sql_select($po_sql);
						foreach ($nameArray as $po_row)
						{
							if($po_no=="") $po_no=$po_row[csf('po_number')]; else $po_no.=",".$po_row[csf('po_number')];
							
							if($min_shipment_date=='')
							{
								$min_shipment_date=$po_row[csf('pub_shipment_date')];
							}
							else
							{
								if($po_row[csf('pub_shipment_date')]<$min_shipment_date) $min_shipment_date=$po_row[csf('pub_shipment_date')]; else $min_shipment_date=$min_shipment_date;
							}
							
							$po_qnty_in_pcs+=$po_row[csf('po_qnty_in_pcs')];
						}
					}
					
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('booking_no')]; ?>','<? echo $row[csf('type')]; ?>','0');"> 
						<td width="30"><? echo $i; ?></td>
						<td width="115"><p><? echo $row[csf('booking_no')]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>               
						<td width="100"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
						<td width="85"><p><? echo $item_category[$row[csf('item_category')]]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</td>
						<td width="90"><p><? echo $row[csf('job_no_mst')]; ?>&nbsp;</p></td>
						<td width="90" align="right"><? echo $po_qnty_in_pcs; ?>&nbsp;</td>
						<td width="80" align="center"><? echo change_date_format($min_shipment_date); ?>&nbsp;</td>
						<td><p><? echo $po_no; ?>&nbsp;</p></td>
					</tr>
				<?
				$i++;
				}
				?>
			</table>
		</div>
	<?	
	}
	else
	{
		if(trim($data[0])!="")
		{
			$search_field_cond="and recv_number like '$search_string'";
		}
		else
		{
			$search_field_cond="";
		}
		

		$po_array=array();
		$po_sql=sql_select("select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and company_name='$company_id'");
		foreach($po_sql as $row)
		{
			$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
		}
		
		$sql = "select id, recv_number_prefix_num, recv_number, buyer_id, knitting_source, knitting_company, receive_date, challan_no from inv_receive_master where entry_form=2 and status_active=1 and is_deleted=0 and company_id=$company_id $search_field_cond"; 
		//echo $sql;
		$result = sql_select($sql);
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table">
			<thead>
				<th width="30">SL</th>
                <th width="115">Production No</th>
				<th width="80">Receive Date</th>
                <th width="90">Challan No</th>
				<th width="100">Knitting Source</th>
                <th width="110">Knitting Company</th>
                <th width="100">Buyer</th>
				<th width="120">Style Ref.</th>
				<th>Order No</th>
			</thead>
		</table>
		<div style="width:928px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">	 
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table" id="tbl_list_search">
            <?
				$i=1;
				foreach ($result as $row)
				{  
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	 
						
					if($row[csf('knitting_source')]==1)	$knit_comp=$company_arr[$row[csf('knitting_company')]]; else $knit_comp=$supplier_arr[$row[csf('knitting_company')]];
					
					$order_id=return_field_value("group_concat(order_id)","pro_grey_prod_entry_dtls","mst_id=$row[id] and status_active=1 and is_deleted=0");
					$order_id=array_unique(explode(",",$order_id));
					
					$order_no=''; $style_ref='';
					foreach($order_id as $value)
					{
						if($order_no=='') $order_no=$po_array[$value]['no']; else $order_no.=",".$po_array[$value]['no'];
						if($style_ref=='') $style_ref=$po_array[$value]['style']; else $style_ref.=",".$po_array[$value]['style'];
					}
					
					$style_ref=array_unique(explode(",",$style_ref));
					$style_ref=implode(",",$style_ref);
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('recv_number')]; ?>','0','<? echo $row[csf('buyer_id')]; ?>');"> 
						<td width="30"><? echo $i; ?></td>
						<td width="115"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
                        <td width="80" align="center"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>   
                        <td width="90"><p><? echo $row[csf('challan_no')]; ?></p></td>            
						<td width="100"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
                        <td width="110"><p><? echo $knit_comp; ?></p></td>
                        <td width="100"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
						<td width="120"><p><? echo $style_ref; ?></p></td>
						<td><p><? echo $order_no; ?></p></td>
					</tr>
				<?
				$i++;
				}
				?>
            </table>
        </div>  
	<?
	}
	
exit();
}
if($action=='populate_data_from_booking')
{
	$data=explode("**",$data);
	$booking_id=$data[0];
	$is_sample=$data[1];
	
	if($is_sample==0)
	{
		$sql="select booking_no, buyer_id, job_no from wo_booking_mst where id='$booking_id'";
	}
	else
	{
		$sql="select booking_no, buyer_id, '' as job_no from wo_non_ord_samp_booking_mst where id='$booking_id'";
	}
	
	$data_array=sql_select($sql);
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_booking_no').value 				= '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('txt_booking_no_id').value 			= '".$booking_id."';\n";
		echo "document.getElementById('cbo_buyer_name').value 				= '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_job_no').value 					= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('booking_without_order').value 		= '".$is_sample."';\n";
		
		if($is_sample==1)
		{
			echo "$('#txt_receive_qnty').removeAttr('readonly','readonly');\n";
			echo "$('#txt_receive_qnty').removeAttr('onClick','onClick');\n";	
			echo "$('#txt_receive_qnty').removeAttr('placeholder','placeholder');\n";		
		}
		else
		{
			echo "$('#txt_receive_qnty').attr('readonly','readonly');\n";
			echo "$('#txt_receive_qnty').attr('onClick','openmypage_po();');\n";	
			echo "$('#txt_receive_qnty').attr('placeholder','Single Click');\n";	
		}
		
		exit();
	}
}

if($action=='populate_data_from_production')
{
	$data=explode("**",$data);
	$id=$data[0];
	$roll_maintained=$data[1];
	
	$data_array=sql_select("select body_part_id, prod_id, febric_description_id, no_of_roll, gsm, width, grey_receive_qnty, reject_fabric_receive, uom, yarn_lot, yarn_count, brand_id, floor_id, shift_name, machine_no_id, order_id, room, rack, self, bin_box, color_id, color_range_id, stitch_length from pro_grey_prod_entry_dtls where id='$id'");
	foreach ($data_array as $row)
	{ 
		$comp='';
		$determination_sql=sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=$row[febric_description_id]");
				
		if($determination_sql[0][csf('construction')]!="")
		{
			$comp=$determination_sql[0][csf('construction')].", ";
		}
		
		foreach($determination_sql as $d_row )
		{
			$comp.=$composition[$d_row[csf('copmposition_id')]]." ".$d_row[csf('percent')]."% ";
		}
		
		echo "document.getElementById('cbo_body_part').value 				= '".$row[csf("body_part_id")]."';\n";
		echo "document.getElementById('txt_fabric_description').value 		= '".trim($comp)."';\n";
		echo "document.getElementById('fabric_desc_id').value 				= '".$row[csf("febric_description_id")]."';\n";
		echo "document.getElementById('txt_gsm').value 						= '".$row[csf("gsm")]."';\n";
		echo "document.getElementById('txt_width').value 					= '".$row[csf("width")]."';\n";
		echo "document.getElementById('txt_roll_no').value 					= '".$row[csf("no_of_roll")]."';\n";
		echo "document.getElementById('txt_receive_qnty').value 			= '".$row[csf("grey_receive_qnty")]."';\n";
		echo "document.getElementById('all_po_id').value 					= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('product_id').value 					= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('txt_color').value 					= '".$color_arr[$row[csf("color_id")]]."';\n";  
		echo "document.getElementById('txt_stitch_length').value 			= '".$row[csf("stitch_length")]."';\n";
		echo "document.getElementById('txt_reject_fabric_recv_qnty').value 	= '".$row[csf("reject_fabric_receive")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("uom")]."';\n";
		echo "document.getElementById('txt_yarn_lot').value 				= '".$row[csf("yarn_lot")]."';\n";
		echo "document.getElementById('cbo_color_range').value 				= '".$row[csf("color_range_id")]."';\n";
		echo "document.getElementById('cbo_yarn_count').value 				= '".$row[csf("yarn_count")]."';\n";
		echo "set_multiselect('cbo_yarn_count','0','1','".$row[csf('yarn_count')]."','0');\n";
		echo "document.getElementById('txt_brand').value 					= '".$brand_arr[$row[csf("brand_id")]]."';\n";
		echo "document.getElementById('txt_shift_name').value 				= '".$row[csf("shift_name")]."';\n";
		echo "document.getElementById('cbo_floor_id').value 				= '".$row[csf("floor_id")]."';\n";
		
		echo "load_drop_down( 'requires/grey_fabric_receive_controller',document.getElementById('cbo_company_id').value+'_'+".$row[csf("floor_id")].", 'load_drop_machine', 'machine_td' );\n";
		
		echo "document.getElementById('cbo_machine_name').value 			= '".$row[csf("machine_no_id")]."';\n";
		echo "document.getElementById('txt_room').value 					= '".$row[csf("room")]."';\n";
		echo "document.getElementById('txt_rack').value 					= '".$row[csf("rack")]."';\n";
		echo "document.getElementById('txt_self').value 					= '".$row[csf("self")]."';\n";
		echo "document.getElementById('txt_binbox').value 					= '".$row[csf("bin_box")]."';\n";
		
		$save_string='';
		if($roll_maintained==1)
		{
			$data_roll_array=sql_select("select id, roll_used, po_breakdown_id, qnty, roll_no from pro_roll_details where dtls_id='$id' and entry_form=1 and status_active=1 and is_deleted=0");
			foreach($data_roll_array as $row_roll)
			{ 
				if($row_roll[csf('roll_used')]==1) $roll_id=$row_roll[csf('id')]; else $roll_id=0;
				
				if($save_string=="")
				{
					$save_string=$row_roll[csf("po_breakdown_id")]."**".$row_roll[csf("qnty")]."**".$row_roll[csf("roll_no")]."**".$roll_id."**".$row_roll[csf("id")];
				}
				else
				{
					$save_string.=",".$row_roll[csf("po_breakdown_id")]."**".$row_roll[csf("qnty")]."**".$row_roll[csf("roll_no")]."**".$roll_id."**".$row_roll[csf("id")];
				}
			}
		}
		else
		{
			$data_po_array=sql_select("select po_breakdown_id, quantity from order_wise_pro_details where dtls_id='$id' and entry_form=2 and status_active=1 and is_deleted=0");
			foreach($data_po_array as $row_po)
			{ 
				if($save_string=="")
				{
					$save_string=$row_po[csf("po_breakdown_id")]."**".$row_po[csf("quantity")];
				}
				else
				{
					$save_string.=",".$row_po[csf("po_breakdown_id")]."**".$row_po[csf("quantity")];
				}
			}
		}
		
		echo "document.getElementById('save_data').value 				= '".$save_string."';\n";
		
		exit();
	}
}

if($action=='show_fabric_desc_listview')
{
	$data=explode("**",$data);
	$booking_pi_production_no=$data[0];
	$is_sample=$data[1];
	$receive_basis=$data[2];
	
	$composition_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}
	
	if($receive_basis==1)
	{
		$sql="select determination_id, '' as body_part_id, gsm as gsm_weight, dia_width, sum(quantity) as qnty from com_pi_item_details where pi_id='$booking_pi_production_no' and status_active=1 and is_deleted=0 group by determination_id, gsm_weight, dia_width";
	}
	else if($receive_basis==2)
	{
		if($is_sample==0)
		{
			$sql="select b.lib_yarn_count_deter_id as determination_id, b.body_part_id, b.gsm_weight, a.dia_width, sum(a.grey_fab_qnty) as qnty from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b where a.pre_cost_fabric_cost_dtls_id=b.id and a.booking_no='$booking_pi_production_no' and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 group by b.lib_yarn_count_deter_id, b.body_part_id, b.gsm_weight, a.dia_width";
		}
		else
		{
			$sql="select lib_yarn_count_deter_id as determination_id, body_part as body_part_id, gsm_weight, dia_width, construction, composition, sum(grey_fabric) as qnty from wo_non_ord_samp_booking_dtls where booking_no='$booking_pi_production_no' and status_active=1 and is_deleted=0 group by lib_yarn_count_deter_id, body_part, gsm_weight, dia_width, construction, composition";
		}
	}
	else
	{
		$sql="select id, febric_description_id as determination_id, body_part_id, gsm as gsm_weight, width as dia_width, grey_receive_qnty as qnty from pro_grey_prod_entry_dtls where mst_id='$booking_pi_production_no' and status_active=1 and is_deleted=0";
	}
	$data_array=sql_select($sql);
	
	?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300">
        <thead>
            <th>SL</th>
            <th>Fabric Description</th>
            <th width="60">Qnty</th>
        </thead>
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";
				
				$fabric_desc='';
				if($receive_basis!=1)
				{
					$fabric_desc=$body_part[$row[csf('body_part_id')]].", ";
				}
				
				if($receive_basis==2 && $is_sample==1 && $row[csf('determination_id')]==0)
				{
					$fabric_desc.=$row[csf('construction')].", ".$row[csf('composition')].", ".$row[csf('gsm_weight')];
				}
				else
				{
					$fabric_desc.=$composition_arr[$row[csf('determination_id')]].", ".$row[csf('gsm_weight')];
				}
				
				if($row[csf('dia_width')]!="")
				{
					$fabric_desc.=", ".$row[csf('dia_width')];	
				}
				
				if($receive_basis==9)
				{
					$data=$row[csf('id')];
				}
				else
				{
					if($receive_basis==2 && $is_sample==1 && $row[csf('determination_id')]==0)
					{
						$cons_comp=$row[csf('construction')].", ".$row[csf('composition')];
						$data=$row[csf('body_part_id')]."**".$cons_comp."**".$row[csf('gsm_weight')]."**".$row[csf('dia_width')]."**".$row[csf('determination_id')];
					}
					else
					{
						$data=$row[csf('body_part_id')]."**".$composition_arr[$row[csf('determination_id')]]."**".$row[csf('gsm_weight')]."**".$row[csf('dia_width')]."**".$row[csf('determination_id')];
					}
				}
             ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $data; ?>")' style="cursor:pointer" >
                    <td><? echo $i; ?></td>
                    <td><? echo $fabric_desc; ?></td>
                    <td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
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
if ($action=="fabricDescription_popup")
{
	echo load_html_head_contents("Fabric Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
		
		$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });
		
		function js_set_value(id,comp,gsm)
		{
			$('#hidden_desc_id').val(id);
			$('#hidden_desc_no').val(comp);
			$('#hidden_gsm').val(gsm);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:720px;margin-left:10px">
			<?
                $data_array=sql_select("select id, construction, fab_nature_id, gsm_weight from lib_yarn_count_determina_mst where fab_nature_id='$garments_nature' and status_active=1 and is_deleted=0");
            ?>
            <input type="hidden" name="hidden_desc_id" id="hidden_desc_id" class="text_boxes" value="">
            <input type="hidden" name="hidden_desc_no" id="hidden_desc_no" class="text_boxes" value="">  
            <input type="hidden" name="hidden_gsm" id="hidden_gsm" class="text_boxes" value="">  
            
            <div style="margin-left:10px; margin-top:10px">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="680">
                    <thead>
                        <th width="50">SL</th>
                        <th width="100">Fabric Nature</th>
                        <th width="150">Construction</th>
                        <th width="100">GSM/Weight</th>
                        <th>Composition</th>
                    </thead>
                </table>
                <div style="width:700px; max-height:280px; overflow-y:scroll" id="list_container" align="left"> 
                    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="680" id="tbl_list_search">  
                        <? 
                        $i=1;
                        foreach($data_array as $row)
                        {  
                            if ($i%2==0)  
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                            
							if($row[csf('construction')]!="")
							{    
                            	$comp=$row[csf('construction')].", ";
							}
                            $determ_sql=sql_select("select copmposition_id, percent from lib_yarn_count_determina_dtls where mst_id=$row[id] and status_active=1 and is_deleted=0");
                            foreach( $determ_sql as $d_row )
                            {
                                $comp.=$composition[$d_row[csf('copmposition_id')]]." ".$d_row[csf('percent')]."% ";
                            }
                            
                         ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $comp; ?>','<? echo $row[csf('gsm_weight')]; ?>')" style="cursor:pointer" >
                                <td width="50"><? echo $i; ?></td>
                                <td width="100"><? echo $item_category[$row[csf('fab_nature_id')]]; ?></td>
                                <td width="150"><p><? echo $row[csf('construction')]; ?></p></td>
                                <td width="100"><? echo $row[csf('gsm_weight')]; ?></td>
                                <td><p><? echo $comp; ?></p></td>
                            </tr>
                        <? 
                        $i++; 
                        } 
                        ?>
                    </table>
                </div> 
            </div>
		</fieldset>
	</form>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action=="po_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$data=explode("_",$data);
	$po_id=$data[0]; $type=$data[1];
	
	if($type==1) 
	{
		$dtls_id=$data[2]; 
		$roll_maintained=$data[3]; 
		$save_data=$data[4]; 
		$prev_distribution_method=$data[5]; 
		$receive_basis=$data[6]; 
	}
?> 

	<script>
		var receive_basis=<? echo $receive_basis; ?>;
		var roll_maintained=<? echo $roll_maintained; ?>;
		
		function fn_show_check()
		{
			if( form_validation('cbo_buyer_name','Buyer Name')==false )
			{
				return;
			}			
			show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+'<? echo $all_po_id; ?>', 'create_po_search_list_view', 'search_div', 'grey_fabric_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1);hidden_field_reset();');
			set_all();
		}
		
		function distribute_qnty(str)
		{
			if(str==1)
			{
				var tot_po_qnty=$('#tot_po_qnty').val()*1;
				var txt_prop_grey_qnty=$('#txt_prop_grey_qnty').val()*1;
				var tblRow = $("#tbl_list_search tr").length;
				var len=totalGrey=0;
				
				$("#tbl_list_search").find('tr').each(function()
				{
					len=len+1;
					
					var txtOrginal=$(this).find('input[name="txtOrginal[]"]').val()*1;
					var isDisbled=$(this).find('input[name="txtGreyQnty[]"]').is(":disabled");
					
					if(txtOrginal==0)
					{
						$(this).remove();
					}
					else if(isDisbled==false && txtOrginal==1)
					{
						var po_qnty=$(this).find('input[name="txtPoQnty[]"]').val()*1;
						var perc=(po_qnty/tot_po_qnty)*100;
						
						var grey_qnty=(perc*txt_prop_grey_qnty)/100;
						totalGrey = totalGrey*1+grey_qnty*1;
						totalGrey = totalGrey.toFixed(2);
												
						if(tblRow==len)
						{
							var balance = txt_prop_grey_qnty-totalGrey;
							if(balance!=0) grey_qnty=grey_qnty*1+(balance*1);
						}
						
						$(this).find('input[name="txtGreyQnty[]"]').val(grey_qnty.toFixed(2));
					}
				});
			}
			else
			{
				$('#txt_prop_grey_qnty').val('');
				$("#tbl_list_search").find('tr').each(function()
				{
					if($(this).find('input[name="txtGreyQnty[]"]').is(":disabled")==false)
					{
						$(this).find('input[name="txtGreyQnty[]"]').val('');
					}
				});
			}
		}
		
		function roll_duplication_check(row_id)
		{
			var row_num=$('#tbl_list_search tr').length;
			var po_id=$('#txtPoId_'+row_id).val();
			var roll_no=$('#txtRoll_'+row_id).val();
			
			if(roll_no*1>0)
			{
				for(var j=1; j<=row_num; j++)
				{
					if(j==row_id)
					{
						continue;
					}
					else
					{
						var po_id_check=$('#txtPoId_'+j).val();
						var roll_no_check=$('#txtRoll_'+j).val();	
			
						if(po_id==po_id_check && roll_no==roll_no_check)
						{
							alert("Duplicate Roll No.");
							$('#txtRoll_'+row_id).val('');
							return;
						}
					}
				}
				
				var txtRollTableId=$('#txtRollTableId_'+row_id).val();
				var data=po_id+"**"+roll_no+"**"+txtRollTableId;
				var response=return_global_ajax_value( data, 'roll_duplication_check', '', 'grey_fabric_receive_controller');
				var response=response.split("_");
				
				if(response[0]!=0)
				{
					var po_number=$('#tr_'+row_id).find('td:first').text();
					alert("This Roll Already Used. Duplicate Not Allowed");
					$('#txtRoll_'+row_id).val('');
					return;
				}
			}
			
		}
		
		function add_break_down_tr( i )
		{ 
			var cbo_distribiution_method=$('#cbo_distribiution_method').val();
			var isDisbled=$('#txtRoll_'+i).is(":disabled");
			
			if(cbo_distribiution_method==2 && isDisbled==false)
			{
				var row_num=$('#tbl_list_search tr').length;
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
				
				$('#txtOrginal_'+row_num).removeAttr("value").attr("value","0");
				
				$('#txtRoll_'+row_num).removeAttr("value").attr("value","");
				$('#txtGreyQnty_'+row_num).removeAttr("value").attr("value","");
				$('#txtRoll_'+row_num).removeAttr("onBlur").attr("onBlur","roll_duplication_check("+row_num+");");
					
				$('#increase_'+row_num).removeAttr("value").attr("value","+");
				$('#decrease_'+row_num).removeAttr("value").attr("value","-");
				$('#increase_'+row_num).removeAttr("onclick").attr("onclick","add_break_down_tr("+row_num+");");
				$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
				
				set_all_onclick();
			}
		}
		
		function fn_deleteRow(rowNo) 
		{ 
			var cbo_distribiution_method=$('#cbo_distribiution_method').val();
			
			if(cbo_distribiution_method==2)
			{
				var txtOrginal=$('#txtOrginal_'+rowNo).val()*1;
				if(txtOrginal==0)
				{
					$("#tr_"+rowNo).remove();
				}
			}
		}
		
		var selected_id = new Array();
		
		 function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i,1 );
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
				for(var i=0; i<old.length; i++)
				{  
					js_set_value( old[i],0 ) 
				}
			}
		}
		
		function js_set_value( str, check_or_not ) 
		{
			if(check_or_not==1)
			{
				var roll_used=$('#roll_used'+str).val();
				if(roll_used==1)
				{
					var po_number=$('#search' + str).find("td:eq(3)").text();
					alert("Batch Roll Found Against PO- "+po_number);
					return;
				}
			}
			
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			
			$('#po_id').val( id );
		}
		
		function show_grey_prod_recv() 
		{ 
			var po_id=$('#po_id').val();
			show_list_view ( po_id+'_'+'1'+'_'+'<? echo $dtls_id; ?>'+'_'+'<? echo $roll_maintained; ?>'+'_'+'<? echo $save_data; ?>'+'_'+'<? echo $prev_distribution_method; ?>'+'_'+'<? echo $receive_basis; ?>', 'po_popup', 'search_div', 'grey_fabric_receive_controller', '');
		}
		
		function hidden_field_reset()
		{
			$('#po_id').val('');
			$('#save_string').val( '' );
			$('#tot_grey_qnty').val( '' );
			$('#number_of_roll').val( '' );
			selected_id = new Array();
		}
		
		function fnc_close()
		{
			var save_string='';	 var tot_grey_qnty=''; var no_of_roll=''; 
			var po_id_array = new Array();
			
			$("#tbl_list_search").find('tr').each(function()
			{
				var txtPoId=$(this).find('input[name="txtPoId[]"]').val();
				var txtGreyQnty=$(this).find('input[name="txtGreyQnty[]"]').val();
				var txtRoll=$(this).find('input[name="txtRoll[]"]').val();
				var txtRollId=$(this).find('input[name="txtRollId[]"]').val();
				var txtRollTableId=$(this).find('input[name="txtRollTableId[]"]').val();
				
				tot_grey_qnty=tot_grey_qnty*1+txtGreyQnty*1;
				
				if(roll_maintained==0)
				{
					txtRoll=0;
				}
				
				if(txtRoll*1>0)
				{
					no_of_roll=no_of_roll*1+1;	
				}
				
				if(txtGreyQnty*1>0)
				{
					
					if(save_string=="")
					{
						save_string=txtPoId+"**"+txtGreyQnty+"**"+txtRoll+"**"+txtRollId+"**"+txtRollTableId;
					}
					else
					{
						save_string+=","+txtPoId+"**"+txtGreyQnty+"**"+txtRoll+"**"+txtRollId+"**"+txtRollTableId;
					}
					
					if( jQuery.inArray( txtPoId, po_id_array) == -1 ) 
					{
						po_id_array.push(txtPoId);
					}
				}

			});
			
			$('#save_string').val( save_string );
			$('#tot_grey_qnty').val( tot_grey_qnty.toFixed(2));
			$('#number_of_roll').val( no_of_roll );
			$('#all_po_id').val( po_id_array );
			$('#distribution_method').val( $('#cbo_distribiution_method').val() );	
			
			parent.emailwindow.hide();
		}
    </script>

</head>

<body>
	<? 
	if($type!=1)
	{
	?>
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:620px;margin-left:10px">
        	<input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
            <input type="hidden" name="tot_grey_qnty" id="tot_grey_qnty" class="text_boxes" value="">
            <input type="hidden" name="number_of_roll" id="number_of_roll" class="text_boxes" value="">
            <input type="hidden" name="all_po_id" id="all_po_id" class="text_boxes" value="">
            <input type="hidden" name="distribution_method" id="distribution_method" class="text_boxes" value="">
	<?
	}
	
	if(($receive_basis==1 || $receive_basis==4 || $receive_basis==6) && $type!=1)
	{
	?>
		<table cellpadding="0" cellspacing="0" width="620" class="rpt_table">
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
						echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",$data[0] ); 
					?>       
				</td>
				<td align="center">	
					<?
						$search_by_arr=array(1=>"PO No",2=>"Job No");
						echo create_drop_down( "cbo_search_by", 170, $search_by_arr,"",0, "--Select--", "",$dd,0 );
					?>
				</td>                 
				<td align="center">				
					<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
				</td> 						
				<td align="center">
					<input type="button" name="button2" class="formbutton" value="Show" onClick="fn_show_check();" style="width:100px;" />
				</td>
			</tr>
		</table>
		<div id="search_div" style="margin-top:10px">
       		<?
			if($save_data!="")
			{
			?>
				<div style="width:600px; margin-top:10px" align="center">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300" align="center">
						<thead>
							<th>Total Grey Qnty</th>
							<th>Distribution Method</th>
						</thead>
						<tr class="general">
							<td><input type="text" name="txt_prop_grey_qnty" id="txt_prop_grey_qnty" class="text_boxes_numeric" value="<? echo $txt_receive_qnty; ?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)"></td>
							<td>
								<? 
									$distribiution_method=array(1=>"Proportionately",2=>"Manually");
									echo create_drop_down( "cbo_distribiution_method", 160, $distribiution_method,"",0, "",$prev_distribution_method, "distribute_qnty(this.value);",0 );
								?>
							</td>
						</tr>
					</table>
				</div>
				<div style="margin-left:10px; margin-top:10px">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="580">
						<thead>
							<th width="120">PO No</th>
							<th width="90">PO Qnty</th>
                            <th width="80">Ship. Date</th>
							<th>Grey Qnty</th>
							<?
							if($roll_maintained==1)
							{
							?>
								<th width="100">Roll</th>
								<th width="65"></th>
							<?
							}
							?>
						</thead>
					</table>
					<div style="width:600px; max-height:200px; overflow-y:scroll" id="list_container" align="left"> 
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="580" id="tbl_list_search">  
							<? 
							$i=1; $tot_po_qnty=0; $po_array=array();  

							$explSaveData = explode(",",$save_data); 	
							for($z=0;$z<count($explSaveData);$z++)
							{
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
									
								$po_wise_data = explode("**",$explSaveData[$z]);
								$order_id=$po_wise_data[0];
								$grey_qnty=$po_wise_data[1];
								$roll_no=$po_wise_data[2];
								$roll_not_delete_id=$po_wise_data[3];
								$roll_id=$po_wise_data[4];
								
								$po_data=sql_select("select b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$order_id");
								
								if($roll_maintained==1)
								{
									$roll_used=return_field_value("roll_used","pro_roll_details","id='$roll_id'");
									
									if(!(in_array($order_id,$po_array)))
									{
										if($roll_not_delete_id==0) $tot_po_qnty+=$po_data[0][csf('po_qnty_in_pcs')];
										$orginal_val=1;
										$po_array[]=$order_id;
									}
									else
									{
										if($roll_used==1) $orginal_val=1; else $orginal_val=0;
									}
									
									if($roll_used==1)
									{
										$disable="disabled='disabled'";
										$roll_not_delete_id=$roll_not_delete_id;
									}
									else
									{
										$disable="";
										$roll_not_delete_id=0;
									}
								}
								else
								{
									if(!(in_array($order_id,$po_array)))
									{
										$tot_po_qnty+=$po_data[0][csf('po_qnty_in_pcs')];
										$orginal_val=1;
										$po_array[]=$order_id;
									}
									else
									{
										$orginal_val=0;
									}
									
									$roll_not_delete_id=0;
									$roll_id=0;
									$disable="";	
								}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td width="120">
										<p><? echo $po_data[0][csf('po_number')]; ?></p>
										<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $order_id; ?>">
										<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
										<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_not_delete_id; ?>">
                                        <!--txtRollId is used for not delete row which is used in batch -->
                                        <input type="hidden" name="txtRollTableId[]" id="txtRollTableId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
                                        <!--txtRollTableId is used for Duplication Check -->
									</td>
									<td width="90" align="right">
										<? echo $po_data[0][csf('po_qnty_in_pcs')] ?>
										<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_data[0][csf('po_qnty_in_pcs')]; ?>">
									</td>
                                    <td width="80" align="center"><? echo change_date_format($po_data[0][csf('pub_shipment_date')]); ?></td>
									<td align="center">
										<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $grey_qnty; ?>" <? echo $disable; ?>>
									</td>
									<?
									if($roll_maintained==1)
									{
									?>
										<td width="100" align="center">
											<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? if($roll_no!=0) echo $roll_no; ?>" <? echo $disable; ?> onBlur="roll_duplication_check(<? echo $i; ?>);" />
										</td>
										<td width="65">
											<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
											<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
										</td>
									<?
									}
									?>
								</tr>
							<? 
							$i++;
							}
							?>
							<input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes" value="<? echo $tot_po_qnty; ?>">
						</table>
					</div>
					<table width="620">
						 <tr>
							<td align="center" >
								<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
							</td>
						</tr>
					</table>
				</div>
			<?
			}
			?>
        </div>
	<?
	}
	else
	{
	?>
		<div style="width:600px; margin-top:10px" align="center">
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300" align="center">
				<thead>
					<th>Total Grey Qnty</th>
					<th>Distribution Method</th>
				</thead>
				<tr class="general">
					<td><input type="text" name="txt_prop_grey_qnty" id="txt_prop_grey_qnty" class="text_boxes_numeric" value="<? echo $txt_receive_qnty; ?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)"></td>
					<td>
						<? 
							$distribiution_method=array(1=>"Proportionately",2=>"Manually");
							echo create_drop_down( "cbo_distribiution_method", 160, $distribiution_method,"",0, "",$prev_distribution_method, "distribute_qnty(this.value);",0 );
						?>
					</td>
				</tr>
			</table>
		</div>
		<div style="margin-left:10px; margin-top:10px">
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="580">
				<thead>
					<th width="120">PO No</th>
					<th width="90">PO Qnty</th>
                    <th width="80">Ship. Date</th>
					<th>Grey Qnty</th>
                    <?
					if($roll_maintained==1)
					{
					?>
                        <th width="100">Roll</th>
                        <th width="65"></th>
					<?
                    }
                    ?>
				</thead>
			</table>
			<div style="width:600px; max-height:200px; overflow-y:scroll" id="list_container" align="left"> 
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="580" id="tbl_list_search">  
					<? 
					$i=1; $tot_po_qnty=0;  $po_array=array();
					
					if($save_data!="" && ($receive_basis==1 || $receive_basis==4 || $receive_basis==6))
					{ 
						$po_id = explode(",",$po_id);
						$explSaveData = explode(",",$save_data); 	
						for($z=0;$z<count($explSaveData);$z++)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
								
							$po_wise_data = explode("**",$explSaveData[$z]);
							$order_id=$po_wise_data[0];
							$grey_qnty=$po_wise_data[1];
							$roll_no=$po_wise_data[2];
							$roll_not_delete_id=$po_wise_data[3];
							$roll_id=$po_wise_data[4];
							
							if(in_array($order_id,$po_id))
							{
								$po_data=sql_select("select b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$order_id");
								
								if($roll_maintained==1)
								{
									$roll_used=return_field_value("roll_used","pro_roll_details","id='$roll_id'");
									
									if(!(in_array($order_id,$po_array)))
									{
										if($roll_not_delete_id==0) $tot_po_qnty+=$po_data[0][csf('po_qnty_in_pcs')];
										$orginal_val=1;
										$po_array[]=$order_id;
									}
									else
									{
										if($roll_used==1) $orginal_val=1; else $orginal_val=0;
									}
									
									if($roll_used==1)
									{
										$disable="disabled='disabled'";
										$roll_not_delete_id=$roll_not_delete_id;
									}
									else
									{
										$disable="";
										$roll_not_delete_id=0;
									}
								}
								else
								{
									if(!(in_array($order_id,$po_array)))
									{
										$tot_po_qnty+=$po_data[0][csf('po_qnty_in_pcs')];
										$orginal_val=1;
										$po_array[]=$order_id;
									}
									else
									{
										$orginal_val=0;
									}
									
									$roll_id=0;
									$roll_not_delete_id=0;
									$disable="";
								}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td width="120">
										<p><? echo $po_data[0][csf('po_number')]; ?></p>
										<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $order_id; ?>">
										<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
										<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_not_delete_id; ?>">
										<!--This is used for not delete row which is used in batch -->
										<input type="hidden" name="txtRollTableId[]" id="txtRollTableId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
										<!--This is used for Duplication Check -->
									</td>
									<td width="90" align="right">
										<? echo $po_data[0][csf('po_qnty_in_pcs')] ?>
										<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_data[0][csf('po_qnty_in_pcs')]; ?>">
									</td>
                                    <td width="80" align="center"><? echo change_date_format($po_data[0][csf('pub_shipment_date')]); ?></td>
									<td align="center">
										<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $grey_qnty; ?>" <? echo $disable; ?>>
									</td>
									<?
									if($roll_maintained==1)
									{
									?>
										<td width="100" align="center">
											<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? if($roll_no!=0) echo $roll_no; ?>" <? echo $disable; ?> onBlur="roll_duplication_check(<? echo $i; ?>);" />
										</td>
										<td width="65">
											<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
											<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
										</td>
									<?
									}
									?>
								</tr>
							<? 
							$i++;
							}
						}
						
						$result=implode(",",array_diff($po_id, $po_array));
						if($result!="")
						{
							$po_sql="select b.id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($result) order by b.pub_shipment_date, b.id";
							$nameArray=sql_select($po_sql);
							foreach($nameArray as $row)
							{  
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								
								$orginal_val=1;
								$roll_id=0;
								$roll_not_delete_id=0;

								$tot_po_qnty+=$row[csf('po_qnty_in_pcs')];
								
							 ?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td width="120">
										<p><? echo $row[csf('po_number')]; ?></p>
										<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
										<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
										<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_not_delete_id; ?>">
										<!--This is used for not delete row which is used in batch -->
										<input type="hidden" name="txtRollTableId[]" id="txtRollTableId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
										<!--This is used for Duplication Check -->
									</td>
									<td width="90" align="right">
										<? echo $row[csf('po_qnty_in_pcs')]; ?>
										<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $row[csf('po_qnty_in_pcs')]; ?>">
									</td>
                                    <td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
									<td align="center">
										<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="" >
									</td>
									<?
									if($roll_maintained==1)
									{
									?>
										<td width="100" align="center">
											<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="" onBlur="roll_duplication_check(<? echo $i; ?>);" />
										</td>
										<td width="65">
											<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
											<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
										</td>
									<?
									}
									?>
								</tr>
							<? 
							$i++; 
							} 
						}
					}
					else if($save_data!="" && $receive_basis==2)
					{
						$all_po_id = explode(",",$all_po_id);
						$explSaveData = explode(",",$save_data); 	
						for($z=0;$z<count($explSaveData);$z++)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
								
							$po_wise_data = explode("**",$explSaveData[$z]);
							$order_id=$po_wise_data[0];
							$grey_qnty=$po_wise_data[1];
							$roll_no=$po_wise_data[2];
							$roll_not_delete_id=$po_wise_data[3];
							$roll_id=$po_wise_data[4];
							
							$po_data=sql_select("select b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$order_id");
							
							if($roll_maintained==1)
							{
								$roll_used=return_field_value("roll_used","pro_roll_details","id='$roll_id'");
								
								if(!(in_array($order_id,$po_array)))
								{
									if($roll_not_delete_id==0) $tot_po_qnty+=$po_data[0][csf('po_qnty_in_pcs')];
									$orginal_val=1;
									$po_array[]=$order_id;
								}
								else
								{
									if($roll_used==1) $orginal_val=1; else $orginal_val=0;
								}
								
								if($roll_used==1)
								{
									$disable="disabled='disabled'";
									$roll_not_delete_id=$roll_not_delete_id;
								}
								else
								{
									$disable="";
									$roll_not_delete_id=0;
								}
							}
							else
							{
								if(!(in_array($order_id,$po_array)))
								{
									$tot_po_qnty+=$po_data[0][csf('po_qnty_in_pcs')];
									$orginal_val=1;
									$po_array[]=$order_id;
								}
								else
								{
									$orginal_val=0;
								}
								
								$roll_id=0;
								$roll_not_delete_id=0;
								$disable="";
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
								<td width="120">
									<p><? echo $po_data[0][csf('po_number')]; ?></p>
									<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $order_id; ?>">
									<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
									<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_not_delete_id; ?>">
									<!--This is used for not delete row which is used in batch -->
									<input type="hidden" name="txtRollTableId[]" id="txtRollTableId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
									<!--This is used for Duplication Check -->
								</td>
								<td width="90" align="right">
									<? echo $po_data[0][csf('po_qnty_in_pcs')]; ?>
									<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_data[0][csf('po_qnty_in_pcs')]; ?>">
								</td>
                                <td width="80" align="center"><? echo change_date_format($po_data[0][csf('pub_shipment_date')]); ?></td>
								<td align="center">
									<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $grey_qnty; ?>" <? echo $disable; ?>>
								</td>
								<?
								if($roll_maintained==1)
								{
								?>
									<td width="100" align="center">
										<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? if($roll_no!=0) echo $roll_no; ?>" <? echo $disable; ?> onBlur="roll_duplication_check(<? echo $i; ?>);" />
									</td>
									<td width="65">
										<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
										<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
									</td>
								<?
								}
								?>
							</tr>
						<? 
						$i++;
						}
						
						$booking_po_id=return_field_value("group_concat(distinct(po_break_down_id)) as po_id","wo_booking_dtls","booking_no='$booking_no' and status_active=1 and is_deleted=0","po_id");
						$booking_po_id=explode(",",$booking_po_id);
						$result=implode(",",array_diff($booking_po_id,$all_po_id));
						
						if($result!="")
						{
							$po_sql="select b.id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($result) order by b.pub_shipment_date, b.id";	
							$nameArray=sql_select($po_sql);
							foreach($nameArray as $row)
							{  
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								
								$orginal_val=1;
								$roll_id=0;
								$roll_not_delete_id=0;
								$disable="";
								$tot_po_qnty+=$row[csf('po_qnty_in_pcs')];
								
							 ?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td width="120">
										<p><? echo $row[csf('po_number')]; ?></p>
										<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
										<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
										<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_not_delete_id; ?>">
										<!--This is used for not delete row which is used in batch -->
										<input type="hidden" name="txtRollTableId[]" id="txtRollTableId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
										<!--This is used for Duplication Check -->
									</td>
									<td width="90" align="right">
										<? echo $row[csf('po_qnty_in_pcs')]; ?>
										<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $row[csf('po_qnty_in_pcs')]; ?>">
									</td>
                                    <td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
									<td align="center">
										<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="">
									</td>
									<?
									if($roll_maintained==1)
									{
									?>
										<td width="100" align="center">
											<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="" onBlur="roll_duplication_check(<? echo $i; ?>);" />
										</td>
										<td width="65">
											<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
											<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
										</td>
									<?
									}
									?>
								</tr>
							<? 
							$i++; 
							}
						}
					}
					else if($save_data!="" && $receive_basis==9)
					{
						$all_po_id = explode(",",$all_po_id);
						$orginal_val=1; $disable="disabled='disabled'";
						$explSaveData = explode(",",$save_data); 	
						for($z=0;$z<count($explSaveData);$z++)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
								
							$po_wise_data = explode("**",$explSaveData[$z]);
							$order_id=$po_wise_data[0];
							$grey_qnty=$po_wise_data[1];
							$roll_no=$po_wise_data[2];
							$roll_not_delete_id=$po_wise_data[3];
							$roll_id=$po_wise_data[4];
							
							$po_data=sql_select("select b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$order_id");
							
							if($roll_maintained==1)
							{
								$roll_used=return_field_value("roll_used","pro_roll_details","id='$roll_id'");
								
								if(!(in_array($order_id,$po_array)))
								{
									if($roll_not_delete_id==0) $tot_po_qnty+=$po_data[0][csf('po_qnty_in_pcs')];
									$po_array[]=$order_id;
								}
								
							}
							else
							{
								if(!(in_array($order_id,$po_array)))
								{
									$tot_po_qnty+=$po_data[0][csf('po_qnty_in_pcs')];
									$po_array[]=$order_id;
								}
								$roll_id=0;
								$roll_not_delete_id=0;
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
								<td width="120">
									<p><? echo $po_data[0][csf('po_number')]; ?></p>
									<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $order_id; ?>">
									<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
									<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_not_delete_id; ?>">
									<!--This is used for not delete row which is used in batch -->
									<input type="hidden" name="txtRollTableId[]" id="txtRollTableId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
									<!--This is used for Duplication Check -->
								</td>
								<td width="90" align="right">
									<? echo $po_data[0][csf('po_qnty_in_pcs')]; ?>
									<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_data[0][csf('po_qnty_in_pcs')]; ?>">
								</td>
                                <td width="80" align="center"><? echo change_date_format($po_data[0][csf('pub_shipment_date')]); ?></td>
								<td align="center">
									<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $grey_qnty; ?>">
								</td>
								<?
								if($roll_maintained==1)
								{
								?>
									<td width="100" align="center">
										<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? if($roll_no!=0) echo $roll_no; ?>" <? echo $disable; ?> onBlur="roll_duplication_check(<? echo $i; ?>);" />
									</td>
									<td width="65">
										<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
										<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
									</td>
								<?
								}
								?>
							</tr>
						<? 
						$i++;
						}
					}
					else
					{ 
						if($type==1)
						{
							if($po_id!="")
							{
								$po_sql="select b.id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($po_id) order by b.pub_shipment_date, b.id";
							}
						}
						else
						{
							$po_sql="select b.id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.booking_no='$booking_no' and c.status_active=1 and c.is_deleted=0 group by b.id order by b.pub_shipment_date, b.id";
						}
						
						$nameArray=sql_select($po_sql);
						foreach($nameArray as $row)
						{  
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							
							$orginal_val=1;
							$roll_id=0;
							$roll_not_delete_id=0;
							$disable="";
							$tot_po_qnty+=$row[csf('po_qnty_in_pcs')];
							
						 ?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
								<td width="120">
									<p><? echo $row[csf('po_number')]; ?></p>
									<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
									<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
									<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_not_delete_id; ?>">
									<!--This is used for not delete row which is used in batch -->
									<input type="hidden" name="txtRollTableId[]" id="txtRollTableId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
									<!--This is used for Duplication Check -->
								</td>
								<td width="90" align="right">
									<? echo $row[csf('po_qnty_in_pcs')]; ?>
									<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $row[csf('po_qnty_in_pcs')]; ?>">
								</td>
                                <td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
								<td align="center">
									<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $grey_qnty; ?>" <? echo $disable; ?>>
								</td>
								<?
								if($roll_maintained==1)
								{
								?>
									<td width="100" align="center">
										<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? if($roll_no!=0) echo $roll_no; ?>" <? echo $disable; ?> onBlur="roll_duplication_check(<? echo $i; ?>);" />
									</td>
									<td width="65">
										<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
										<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
									</td>
								<?
								}
								?>
							</tr>
						<? 
						$i++; 
						} 
					}
					?>
					<input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes" value="<? echo $tot_po_qnty; ?>">
				</table>
			</div>
			<table width="620">
				 <tr>
					<td align="center" >
						<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
					</td>
				</tr>
			</table>
		</div>
	<?
	}
	if($type!=1)
	{
	?>
		</fieldset>
	</form>
    <?
	}
	?>
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
	else
		$search_field='a.job_no';
		
	$company_id =$data[2];
	$buyer_id =$data[3];
	
	$all_po_id=$data[4];
	
	if($all_po_id!="")
		$po_id_cond=" or b.id in($all_po_id)";
	else 
		$po_id_cond="";
	
	$hidden_po_id=explode(",",$all_po_id);

	if($buyer_id==0) { echo "Please Select Buyer First."; die; }
	
	$sql = "select a.job_no, a.style_ref_no, a.order_uom, b.id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id and a.buyer_name=$buyer_id and $search_field like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_id_cond group by b.id"; 
	//echo $sql;die;
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" >
            <thead>
                <th width="40">SL</th>
                <th width="100">Job No</th>
                <th width="110">Style No</th>
                <th width="110">PO No</th>
                <th width="90">PO Quantity</th>
                <th width="50">UOM</th>
                <th>Shipment Date</th>
            </thead>
        </table>
        <div style="width:618px; overflow-y:scroll; max-height:240px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="tbl_list_search" >
            <?
				$i=1; $po_row_id='';
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
						
					$roll_used=0;
					
					if(in_array($selectResult[csf('id')],$hidden_po_id)) 
					{
						if($po_row_id=="") $po_row_id=$i; else $po_row_id.=",".$i;
						
						$roll_data_array=sql_select("select roll_no from pro_roll_details where po_breakdown_id=$selectResult[id] and roll_used=1 and entry_form=1 and status_active=1 and is_deleted=0");
						if(count($roll_data_array)>0)
						{
							$roll_used=1;
						}
						else
							$roll_used=0;
					}
							
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>,1)"> 
                        <td width="40" align="center">
                            <? echo $i; ?>
                            <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
                            <input type="hidden" name="roll_used" id="roll_used<? echo $i ?>" value="<? echo $roll_used; ?>"/>	
                        </td>	
                        <td width="100"><p><? echo $selectResult[csf('job_no')]; ?></p></td>
                        <td width="110"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
                        <td width="110"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                        <td width="90" align="right"><? echo $selectResult[csf('po_qnty_in_pcs')]; ?></td> 
                        <td width="50" align="center"><p><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></p></td>
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
                            <input type="button" name="close" onClick="show_grey_prod_recv();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
	</div>           
<?
	
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
		
		$grey_recv_num=''; $grey_update_id=''; $flag=1;
		
		if(str_replace("'","",$garments_nature)==2)
		{
			$category_id=13; $entry_form=22; $prefix='KNGFR';
		}
		else 
		{
			$category_id=14; $entry_form=23; $prefix='WVGFR';
		}
		
		if(str_replace("'","",$update_id)=="")
		{
			$new_grey_recv_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', $prefix, date("Y",time()), 5, "select recv_number_prefix, recv_number_prefix_num from inv_receive_master where company_id=$cbo_company_id and entry_form='$entry_form' and YEAR(insert_date)=".date('Y',time())." order by recv_number_prefix_num desc", "recv_number_prefix", "recv_number_prefix_num" ));
		 	
			$id=return_next_id( "id", "inv_receive_master", 1 ) ;
					 
			$field_array="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, receive_basis, company_id, receive_date, challan_no, booking_id, booking_no, booking_without_order, store_id, location_id, knitting_source, knitting_company, buyer_id, yarn_issue_challan_no, remarks, fabric_nature, inserted_by, insert_date";
			
			$data_array="(".$id.",'".$new_grey_recv_system_id[1]."',".$new_grey_recv_system_id[2].",'".$new_grey_recv_system_id[0]."',$entry_form,$category_id,".$cbo_receive_basis.",".$cbo_company_id.",".$txt_receive_date.",".$txt_receive_chal_no.",".$txt_booking_no_id.",".$txt_booking_no.",".$booking_without_order.",".$cbo_store_name.",".$cbo_location_name.",".$cbo_knitting_source.",".$cbo_knitting_company.",".$cbo_buyer_name.",".$txt_yarn_issue_challan_no.",".$txt_remarks.",".$garments_nature.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//echo "insert into pro_grey_prod_entry_mst (".$field_array.") values ".$data_array;die;
			$rID=sql_insert("inv_receive_master",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
			
			$grey_recv_num=$new_grey_recv_system_id[0];
			$grey_update_id=$id;
		}
		else
		{
			$field_array_update="receive_basis*receive_date*challan_no*booking_id*booking_no*booking_without_order*store_id*location_id*knitting_source*knitting_company*buyer_id*yarn_issue_challan_no*remarks*updated_by*update_date";
			
			$data_array_update=$cbo_receive_basis."*".$txt_receive_date."*".$txt_receive_chal_no."*".$txt_booking_no_id."*".$txt_booking_no."*".$booking_without_order."*".$cbo_store_name."*".$cbo_location_name."*".$cbo_knitting_source."*".$cbo_knitting_company."*".$cbo_buyer_name."*".$txt_yarn_issue_challan_no."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; 
			
			$grey_recv_num=str_replace("'","",$txt_recieved_id);
			$grey_update_id=str_replace("'","",$update_id);
		}
		
		
		$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
		
		if (str_replace("'", "", trim($txt_brand)) != "") {
			if (!in_array(str_replace("'", "", trim($txt_brand)),$new_array_brand)){
				$brand_id = return_id( str_replace("'", "", trim($txt_brand)), $brand_arr, "lib_brand", "id,brand_name","$entry_form");
				$new_array_brand[$brand_id]=str_replace("'", "", trim($txt_brand));
			}
			else $brand_id =  array_search(str_replace("'", "", trim($txt_brand)), $new_array_brand);
		} else $brand_id = 0;
		/*$brand_id=return_id( $txt_brand, $brand_arr, "lib_brand", "id,brand_name");
		if($brand_id=="") $brand_id=0;*/
		/*$color_id=return_id( $txt_color, $color_arr, "lib_color", "id,color_name");
		if($color_id=="") $color_id=0;*/
		
		if (str_replace("'", "", trim($txt_color)) != "") {
			if (!in_array(str_replace("'", "", trim($txt_color)),$new_array_color)){
				$color_id = return_id( str_replace("'", "", trim($txt_color)), $color_arr, "lib_color", "id,color_name","$entry_form");
				$new_array_color[$color_id]=str_replace("'", "", trim($txt_color));
			}
			else $color_id =  array_search(str_replace("'", "", trim($txt_color)), $new_array_color);
		} else $color_id = 0;
		
		if(str_replace("'", '',$cbo_receive_basis)==9)
		{
			$cur_st_value=0;
			$cur_st_rate=0;
			
			$field_array_prod_update="current_stock*avg_rate_per_unit*stock_value";
			$data_array_prod_update="current_stock+".$txt_receive_qnty."*".$cur_st_value."*".$cur_st_rate;
			
			$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			} 
			
			$prod_id=$product_id;
		}
		else
		{
			if(str_replace("'","",$fabric_desc_id)=="") $fabric_desc_id=0;
			if(str_replace("'","",$cbo_receive_basis)==2 && str_replace("'","",$booking_without_order)==1 && str_replace("'","",$fabric_desc_id)==0)
			{
				$fabric_description=trim(str_replace("'","",$txt_fabric_description));
				$row_prod=sql_select("select id, current_stock from product_details_master where company_id=$cbo_company_id and item_category_id=$category_id and detarmination_id=$fabric_desc_id and item_description='$fabric_description' and gsm=$txt_gsm and dia_width=$txt_width and status_active=1 and is_deleted=0");
			}
			else
			{
				$row_prod=sql_select("select id, current_stock from product_details_master where company_id=$cbo_company_id and item_category_id=$category_id and detarmination_id=$fabric_desc_id and gsm=$txt_gsm and dia_width=$txt_width and status_active=1 and is_deleted=0");
			}
			
			if(count($row_prod)>0)
			{
				$prod_id=$row_prod[0][csf('id')];
				$stock_qnty=$row_prod[0][csf('current_stock')];
	
				$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_receive_qnty);
				$avg_rate_per_unit=0;
				$stock_value=0;
	
				$field_array_prod_update="store_id*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*brand*lot*updated_by*update_date";
				
				$data_array_prod_update=$cbo_store_name."*".$avg_rate_per_unit."*".$txt_receive_qnty."*".$curr_stock_qnty."*".$stock_value."*".$brand_id."*".$txt_yarn_lot."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				
				$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,0);
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				} 
			}
			else
			{
				$prod_id=return_next_id( "id", "product_details_master", 1 ) ;
				
				$avg_rate_per_unit=0;
				$stock_value=0;
				$prod_name_dtls=trim(str_replace("'","",$txt_fabric_description)).", ".trim(str_replace("'","",$txt_gsm)).", ".trim(str_replace("'","",$txt_width));
				$field_array_prod="id, company_id, store_id, item_category_id, detarmination_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, brand, gsm, dia_width, lot, inserted_by, insert_date";
				
				$data_array_prod="(".$prod_id.",".$cbo_company_id.",".$cbo_store_name.",$category_id,".$fabric_desc_id.",".$txt_fabric_description.",'".$prod_name_dtls."',".$cbo_uom.",".$avg_rate_per_unit.",".$txt_receive_qnty.",".$txt_receive_qnty.",".$stock_value.",".$brand_id.",".$txt_gsm.",".$txt_width.",".$txt_yarn_lot.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				//echo "insert into product_details_master (".$field_array_prod.") values ".$data_array_prod."chd".$txt_fabric_description;die;
				$rID2=sql_insert("product_details_master",$field_array_prod,$data_array_prod,0);
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				} 
			}
		}

		$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		
		$order_rate=0; $order_amount=0; $cons_rate=0; $cons_amount=0;
		
		$field_array_trans="id, mst_id, receive_basis, pi_wo_batch_no, company_id, prod_id, item_category, transaction_type, transaction_date, store_id, brand_id, order_uom, order_qnty, order_rate, order_amount, cons_uom, cons_quantity, cons_reject_qnty, cons_rate, cons_amount, floor_id, machine_id, room, rack, self, bin_box, inserted_by, insert_date";
		
		$data_array_trans="(".$id_trans.",".$grey_update_id.",".$cbo_receive_basis.",".$txt_booking_no_id.",".$cbo_company_id.",".$prod_id.",$category_id,1,".$txt_receive_date.",".$cbo_store_name.",".$brand_id.",".$cbo_uom.",".$txt_receive_qnty.",".$order_rate.",".$order_amount.",".$cbo_uom.",".$txt_receive_qnty.",".$txt_reject_fabric_recv_qnty.",".$cons_rate.",".$cons_amount.",".$cbo_floor_id.",".$cbo_machine_name.",".$txt_room.",".$txt_rack.",".$txt_self.",".$txt_binbox.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		$rID3=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		
		$id_dtls=return_next_id( "id", "pro_grey_prod_entry_dtls", 1 ) ;
		
		$cbo_yarn_count=explode(",",str_replace("'","",$cbo_yarn_count));
		asort($cbo_yarn_count);
		$cbo_yarn_count=implode(",",$cbo_yarn_count);
		
		$txt_yarn_lot=explode(",",str_replace("'","",$txt_yarn_lot));
		asort($txt_yarn_lot);
		$txt_yarn_lot=implode(",",$txt_yarn_lot);
		$rate=0; $amount=0;
		
		$field_array_dtls="id, mst_id, trans_id, prod_id, body_part_id, febric_description_id, gsm, width, no_of_roll, order_id, grey_receive_qnty, reject_fabric_receive, rate, amount, uom, yarn_lot, yarn_count, brand_id, shift_name, floor_id, machine_no_id, room, rack, self, bin_box, color_id, color_range_id, stitch_length, inserted_by, insert_date";
		
		$data_array_dtls="(".$id_dtls.",".$grey_update_id.",".$id_trans.",".$prod_id.",".$cbo_body_part.",".$fabric_desc_id.",".$txt_gsm.",".$txt_width.",".$txt_roll_no.",".$all_po_id.",".$txt_receive_qnty.",".$txt_reject_fabric_recv_qnty.",".$rate.",".$amount.",".$cbo_uom.",'".$txt_yarn_lot."','".$cbo_yarn_count."',".$brand_id.",".$txt_shift_name.",".$cbo_floor_id.",".$cbo_machine_name.",".$txt_room.",".$txt_rack.",".$txt_self.",".$txt_binbox.",".$color_id.",".$cbo_color_range.",".$txt_stitch_length.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		$rID4=sql_insert("pro_grey_prod_entry_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		} 
		
		$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, inserted_by, insert_date";
		
		$save_string=explode(",",str_replace("'","",$save_data));
		
		$po_array=array();
		
		for($i=0;$i<count($save_string);$i++)
		{
			$order_dtls=explode("**",$save_string[$i]);
			
			$order_id=$order_dtls[0];
			$order_qnty_roll_wise=$order_dtls[1];
			$roll_no=$order_dtls[2];
			
			if($i==0) $add_comma=""; else $add_comma=",";
			
			if( $id_roll=="" ) $id_roll = return_next_id( "id", "pro_roll_details", 1 ); else $id_roll = $id_roll+1;
			
			$data_array_roll.="$add_comma(".$id_roll.",".$grey_update_id.",".$id_dtls.",'".$order_id."',$entry_form,'".$order_qnty_roll_wise."','".$roll_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			if(array_key_exists($order_id,$po_array))
			{
				$po_array[$order_id]+=$order_qnty_roll_wise;
			}
			else
			{
				$po_array[$order_id]=$order_qnty_roll_wise;
			}
		}
		
		//echo "insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;	$booking_without_order
		if($data_array_roll!="" && str_replace("'","",$roll_maintained)==1 && str_replace("'","",$booking_without_order)!=1)
		{
			$rID5=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			} 
		}
		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, inserted_by, insert_date";

		$i=0;
		foreach($po_array as $key=>$val)
		{
			if($i==0) $add_comma=""; else $add_comma=",";
			
			if( $id_prop=="" ) $id_prop = return_next_id( "id", "order_wise_pro_details", 1 ); else $id_prop = $id_prop+1;
			
			$order_id=$key;
			$order_qnty=$val;
			
			$data_array_prop.="$add_comma(".$id_prop.",".$id_trans.",1,$entry_form,".$id_dtls.",'".$order_id."',".$prod_id.",'".$color_id."','".$order_qnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$i++;
		}
		
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;	
		if($data_array_prop!="" && str_replace("'","",$booking_without_order)!=1)
		{
			$rID6=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			if($flag==1) 
			{
				if($rID6) $flag=1; else $flag=0; 
			} 
		}

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$grey_update_id."**".$grey_recv_num."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				echo "0**".$grey_update_id."**".$grey_recv_num."**0";
			}
			else
			{
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		
		check_table_status( $_SESSION['menu_id'],0);
				
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
		
		if(str_replace("'","",$garments_nature)==2)
		{
			$category_id=13; $entry_form=22; 
		}
		else 
		{
			$category_id=14; $entry_form=23; 
		}
		
		$field_array_update="receive_basis*receive_date*challan_no*booking_id*booking_no*booking_without_order*store_id*location_id*knitting_source*knitting_company*buyer_id*yarn_issue_challan_no*remarks*updated_by*update_date";
			
		$data_array_update=$cbo_receive_basis."*".$txt_receive_date."*".$txt_receive_chal_no."*".$txt_booking_no_id."*".$txt_booking_no."*".$booking_without_order."*".$cbo_store_name."*".$cbo_location_name."*".$cbo_knitting_source."*".$cbo_knitting_company."*".$cbo_buyer_name."*".$txt_yarn_issue_challan_no."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,0);
		if($rID) $flag=1; else $flag=0;
		
		$stock= return_field_value("current_stock","product_details_master","id=$previous_prod_id");
		$adjust_curr_stock=$stock-str_replace("'", '',$hidden_receive_qnty);
		$cur_st_value=0;
		$cur_st_rate=0;
		
		$field_array_adjust="current_stock*avg_rate_per_unit*stock_value";
			
		$data_array_adjust=$adjust_curr_stock."*".$cur_st_value."*".$cur_st_rate;
		
		$rID_adjust=sql_update("product_details_master",$field_array_adjust,$data_array_adjust,"id",$previous_prod_id,0);
		if($flag==1) 
		{
			if($rID_adjust) $flag=1; else $flag=0; 
		} 

		$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
		
		if (str_replace("'", "", trim($txt_brand)) != "") {
			if (!in_array(str_replace("'", "", trim($txt_brand)),$new_array_brand)){
				$brand_id = return_id( str_replace("'", "", trim($txt_brand)), $brand_arr, "lib_brand", "id,brand_name","$entry_form");
				$new_array_brand[$brand_id]=str_replace("'", "", trim($txt_brand));
			}
			else $brand_id =  array_search(str_replace("'", "", trim($txt_brand)), $new_array_brand);
		} else $brand_id = 0;
		
		/*$brand_id=return_id( $txt_brand, $brand_arr, "lib_brand", "id,brand_name");
		if($brand_id=="") $brand_id=0;*/
		/*$color_id=return_id( $txt_color, $color_arr, "lib_color", "id,color_name");
		if($color_id=="") $color_id=0;*/
		
		if (str_replace("'", "", trim($txt_color)) != "") {
			if (!in_array(str_replace("'", "", trim($txt_color)),$new_array_color)){
				$color_id = return_id( str_replace("'", "", trim($txt_color)), $color_arr, "lib_color", "id,color_name","$entry_form");
				$new_array_color[$color_id]=str_replace("'", "", trim($txt_color));
			}
			else $color_id =  array_search(str_replace("'", "", trim($txt_color)), $new_array_color);
		} else $color_id = 0;
		
		if(str_replace("'", '',$cbo_receive_basis)==9)
		{
			$cur_st_value=0;
			$cur_st_rate=0;
			
			$field_array_prod_update="current_stock*avg_rate_per_unit*stock_value";
			$data_array_prod_update="current_stock+".$txt_receive_qnty."*".$cur_st_value."*".$cur_st_rate;
			
			$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			} 
			
			$prod_id=$product_id;
		}
		else
		{
			if(str_replace("'","",$fabric_desc_id)=="") $fabric_desc_id=0;
			if(str_replace("'","",$cbo_receive_basis)==2 && str_replace("'","",$booking_without_order)==1 && str_replace("'","",$fabric_desc_id)==0)
			{
				$fabric_description=trim(str_replace("'","",$txt_fabric_description));
				$row_prod=sql_select("select id, current_stock from product_details_master where company_id=$cbo_company_id and item_category_id=$category_id and detarmination_id=$fabric_desc_id and item_description='$fabric_description' and gsm=$txt_gsm and dia_width=$txt_width and status_active=1 and is_deleted=0");
			}
			else
			{
				$row_prod=sql_select("select id, current_stock from product_details_master where company_id=$cbo_company_id and item_category_id=$category_id and detarmination_id=$fabric_desc_id and gsm=$txt_gsm and dia_width=$txt_width and status_active=1 and is_deleted=0");
			}
			
			if(count($row_prod)>0)
			{
				$prod_id=$row_prod[0][csf('id')];
				$stock_qnty=$row_prod[0][csf('current_stock')];
	
				$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_receive_qnty);
				$avg_rate_per_unit=0;
				$stock_value=0;
	
				$field_array_prod_update="store_id*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*brand*lot*updated_by*update_date";
				
				$data_array_prod_update=$cbo_store_name."*".$avg_rate_per_unit."*".$txt_receive_qnty."*".$curr_stock_qnty."*".$stock_value."*".$brand_id."*".$txt_yarn_lot."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				
				$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,0);
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				}
			}
			else
			{
				$prod_id=return_next_id( "id", "product_details_master", 1 ) ;
				
				$avg_rate_per_unit=0;
				$stock_value=0;
				
				$prod_name_dtls=trim(str_replace("'","",$txt_fabric_description)).", ".trim(str_replace("'","",$txt_gsm)).", ".trim(str_replace("'","",$txt_width));
				$field_array_prod="id, company_id, store_id, item_category_id, detarmination_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, brand, gsm, dia_width, lot, inserted_by, insert_date";
				
				$data_array_prod="(".$prod_id.",".$cbo_company_id.",".$cbo_store_name.",$category_id,".$fabric_desc_id.",".$txt_fabric_description.",'".$prod_name_dtls."',".$cbo_uom.",".$avg_rate_per_unit.",".$txt_receive_qnty.",".$txt_receive_qnty.",".$stock_value.",".$brand_id.",".$txt_gsm.",".$txt_width.",".$txt_yarn_lot.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$rID2=sql_insert("product_details_master",$field_array_prod,$data_array_prod,0);
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				} 
			}
		}
		
		$order_rate=0; $order_amount=0; $cons_rate=0; $cons_amount=0;
		
		$field_array_trans_update="receive_basis*pi_wo_batch_no*prod_id*transaction_date*store_id*brand_id*order_qnty*order_rate*order_amount*cons_quantity*cons_reject_qnty*cons_rate*cons_amount*floor_id*machine_id*room*rack*self*bin_box*updated_by*update_date";
		
		$data_array_trans_update=$cbo_receive_basis."*".$txt_booking_no_id."*".$prod_id."*".$txt_receive_date."*".$cbo_store_name."*".$brand_id."*".$txt_receive_qnty."*".$order_rate."*".$order_amount."*".$txt_receive_qnty."*".$txt_reject_fabric_recv_qnty."*".$cons_rate."*".$cons_amount."*".$cbo_floor_id."*".$cbo_machine_name."*".$txt_room."*".$txt_rack."*".$txt_self."*".$txt_binbox."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID4=sql_update("inv_transaction",$field_array_trans_update,$data_array_trans_update,"id",$update_trans_id,0);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		} 
		
		$cbo_yarn_count=explode(",",str_replace("'","",$cbo_yarn_count));
		asort($cbo_yarn_count);
		$cbo_yarn_count=implode(",",$cbo_yarn_count);
		
		$txt_yarn_lot=explode(",",str_replace("'","",$txt_yarn_lot));
		asort($txt_yarn_lot);
		$txt_yarn_lot=implode(",",$txt_yarn_lot);
		$rate=0; $amount=0;
		
		$field_array_dtls_update="prod_id*body_part_id*febric_description_id*gsm*width*no_of_roll*order_id*grey_receive_qnty*reject_fabric_receive*rate*amount*uom*yarn_lot*yarn_count*brand_id*shift_name*floor_id*machine_no_id*room*rack*self*bin_box*color_id*color_range_id*stitch_length*updated_by*update_date";
		
		$data_array_dtls_update=$prod_id."*".$cbo_body_part."*".$fabric_desc_id."*".$txt_gsm."*".$txt_width."*".$txt_roll_no."*".$all_po_id."*".$txt_receive_qnty."*".$txt_reject_fabric_recv_qnty."*".$rate."*".$amount."*".$cbo_uom."*'".$txt_yarn_lot."'*'".$cbo_yarn_count."'*".$brand_id."*".$txt_shift_name."*".$cbo_floor_id."*".$cbo_machine_name."*".$txt_room."*".$txt_rack."*".$txt_self."*".$txt_binbox."*".$color_id."*".$cbo_color_range."*".$txt_stitch_length."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$rID5=sql_update("pro_grey_prod_entry_dtls",$field_array_dtls_update,$data_array_dtls_update,"id",$update_dtls_id,0);
		if($flag==1) 
		{
			if($rID5) $flag=1; else $flag=0; 
		} 
		
		$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, inserted_by, insert_date";
		
		$save_string=explode(",",str_replace("'","",$save_data));
		
		$po_array=array();
		$not_delete_roll_table_id=''; $j=0;
		
		for($i=0;$i<count($save_string);$i++)
		{
			$order_dtls=explode("**",$save_string[$i]);
			
			$order_id=$order_dtls[0];
			$order_qnty_roll_wise=$order_dtls[1];
			$roll_no=$order_dtls[2];
			$roll_not_delete_id=$order_dtls[3];
			$roll_id=$order_dtls[4];

			if($roll_not_delete_id==0)
			{
				if($j==0) $add_comma=""; else $add_comma=",";
				
				if( $id_roll=="" ) $id_roll = return_next_id( "id", "pro_roll_details", 1 ); else $id_roll = $id_roll+1;
				
				$data_array_roll.="$add_comma(".$id_roll.",".$update_id.",".$update_dtls_id.",'".$order_id."',$entry_form,'".$order_qnty_roll_wise."','".$roll_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$j++;
			}
			else
			{
				if($not_delete_roll_table_id=="") $not_delete_roll_table_id=$roll_id; else $not_delete_roll_table_id.=",".$roll_id;
			}
			
			if(array_key_exists($order_id,$po_array))
			{
				$po_array[$order_id]+=$order_qnty_roll_wise;
			}
			else
			{
				$po_array[$order_id]=$order_qnty_roll_wise;
			}
		}
		
		if(str_replace("'","",$roll_maintained)==1)
		{
			if($not_delete_roll_table_id=="") $delete_cond=""; else $delete_cond="and id not in($not_delete_roll_table_id)"; 
	
			$delete_roll=execute_query( "delete from pro_roll_details where dtls_id=$update_dtls_id and entry_form=$entry_form $delete_cond",0);
			if($flag==1) 
			{
				if($delete_roll) $flag=1; else $flag=0; 
			} 

			if($data_array_roll!="" && str_replace("'","",$booking_without_order)!=1)
			{
				$rID6=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
				if($flag==1) 
				{
					if($rID6) $flag=1; else $flag=0; 
				} 
			}
		}
		
		$delete_prop=execute_query( "delete from order_wise_pro_details where dtls_id=$update_dtls_id and trans_id=$update_trans_id and entry_form=$entry_form",0);
		if($flag==1) 
		{
			if($delete_prop) $flag=1; else $flag=0; 
		}
		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, inserted_by, insert_date";

		$i=0;
		foreach($po_array as $key=>$val)
		{
			if($i==0) $add_comma=""; else $add_comma=",";
			
			if( $id_prop=="" ) $id_prop = return_next_id( "id", "order_wise_pro_details", 1 ); else $id_prop = $id_prop+1;
			
			$order_id=$key;
			$order_qnty=$val;
			
			$data_array_prop.="$add_comma(".$id_prop.",".$update_trans_id.",1,$entry_form,".$update_dtls_id.",'".$order_id."',".$prod_id.",'".$color_id."','".$order_qnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$i++;
		}
		
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;	
		if($data_array_prop!="" && str_replace("'","",$booking_without_order)!=1)
		{
			$rID7=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			if($flag==1) 
			{
				if($rID7) $flag=1; else $flag=0; 
			} 
		}
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_recieved_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**0**1";
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_recieved_id)."**0";
			}
			else
			{
				echo "6**0**0**1";
			}
		}
		disconnect($con);
		die;
	}
	
}


if ($action=="grey_receive_popup_search")
{
	echo load_html_head_contents("Grey Receive Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
	
		function js_set_value(id)
		{
			$('#hidden_recv_id').val(id);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:880px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:875px; margin-left:5px">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="820" class="rpt_table">
                <thead>
                    <th>Buyer</th>
                    <th>Received Date Range</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="200">Enter Received ID</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                    	<input type="hidden" name="hidden_recv_id" id="hidden_recv_id" class="text_boxes" value="">  
                    </th> 
                </thead>
                <tr>
                    <td align="center">
                    	<?
							echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] ); 
						?>       
                    </td>
                    <td align="center">
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To
					  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					</td>
                    <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"WO/PI/Production No",2=>"Received ID",3=>"Challan No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../../') ";							
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", 2,$dd,0);
						?>
                    </td>     
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+<? echo $garments_nature; ?>, 'create_grey_recv_search_list_view', 'search_div', 'grey_fabric_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
           <div style="width:100%; margin-top:15px; margin-left:3px" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_grey_recv_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$buyer_id =$data[5];
	$garments_nature =$data[6];
	
	if($garments_nature==2) $entry_form=22; else $entry_form=23;
	
	if($buyer_id==0) $buyer_name="%%"; else $buyer_name=$buyer_id;
	
	if($start_date!="" && $end_date!="")
	{
		$date_cond="and receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
	}
	else
	{
		$date_cond="";
	}
	
	if(trim($data[0])!="")
	{
		if($search_by==1)
			$search_field_cond="and booking_no like '$search_string'";
		else if($search_by==2)	
			$search_field_cond="and recv_number like '$search_string'";
		else	
			$search_field_cond="and challan_no like '$search_string'";
	}
	else
	{
		$search_field_cond="";
	}
	
	$sql = "select id, recv_number_prefix_num, recv_number, booking_no, buyer_id, knitting_source, knitting_company, receive_date, challan_no, year(insert_date) as year from inv_receive_master where entry_form=$entry_form and fabric_nature=$garments_nature and status_active=1 and is_deleted=0 and company_id=$company_id and buyer_id like '$buyer_name' $search_field_cond $date_cond"; 
	//echo $sql;die;
	$result = sql_select($sql);

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table">
        <thead>
            <th width="35">SL</th>
            <th width="60">Year</th>
            <th width="70">Received ID</th>
            <th width="120">Booking/PI /Production No</th>               
            <th width="100">Knitting Source</th>
            <th width="110">Knitting Company</th>
            <th width="80">Receive date</th>
            <th width="90">Receive Qnty</th>
            <th width="75">Challan No</th>
            <th>Buyer</th>
        </thead>
	</table>
	<div style="width:870px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";	 
                if($row[csf('knitting_source')]==1)
					$knit_comp=$company_arr[$row[csf('knitting_company')]]; 
				else
					$knit_comp=$supllier_arr[$row[csf('knitting_company')]];
				
				$recv_qnty=return_field_value("sum(grey_receive_qnty)","pro_grey_prod_entry_dtls","mst_id='$row[id]' and status_active=1 and is_deleted=0");
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>);"> 
                    <td width="35"><? echo $i; ?></td>
                    <td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="70"><p>&nbsp;<? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
                    <td width="120"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>               
                    <td width="100"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
                    <td width="110"><p><? echo $knit_comp; ?></p></td>
                    <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td width="90" align="right"><? echo number_format($recv_qnty,2); ?></td>
                    <td width="75"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                    <td><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
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

if($action=='populate_data_from_grey_recv')
{
	$data_array=sql_select("select id, recv_number, company_id, receive_basis, booking_id, booking_no, booking_without_order, buyer_id, store_id, location_id, knitting_source, knitting_company, receive_date, challan_no, yarn_issue_challan_no, remarks from inv_receive_master where id='$data'");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_recieved_id').value 				= '".$row[csf("recv_number")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_receive_basis').value 			= '".$row[csf("receive_basis")]."';\n";

		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		echo "set_receive_basis();\n";
		
		echo "document.getElementById('txt_receive_date').value 			= '".change_date_format($row[csf("receive_date")])."';\n";
		echo "document.getElementById('txt_receive_chal_no').value 			= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_booking_no').value 				= '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('txt_booking_no_id').value 			= '".$row[csf("booking_id")]."';\n";
		echo "document.getElementById('booking_without_order').value 		= '".$row[csf("booking_without_order")]."';\n";
		
		if($row[csf("booking_without_order")]==1)
		{
			echo "$('#txt_receive_qnty').removeAttr('readonly','readonly');\n";
			echo "$('#txt_receive_qnty').removeAttr('onClick','onClick');\n";	
			echo "$('#txt_receive_qnty').removeAttr('placeholder','placeholder');\n";		
		}
		else
		{
			echo "$('#txt_receive_qnty').attr('readonly','readonly');\n";
			echo "$('#txt_receive_qnty').attr('onClick','openmypage_po();');\n";	
			echo "$('#txt_receive_qnty').attr('placeholder','Single Click');\n";	
		}
		
		echo "document.getElementById('cbo_buyer_name').value 				= '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("store_id")]."';\n";
		echo "document.getElementById('cbo_knitting_source').value 			= '".$row[csf("knitting_source")]."';\n";
		
		echo "load_drop_down( 'requires/grey_fabric_receive_controller', $row[knitting_source]+'_'+$row[company_id], 'load_drop_down_knitting_com','knitting_com');\n";
		
		$job_no='';
		if($row[csf("receive_basis")]==2)
		{
			$job_no=return_field_value("job_no","wo_booking_mst","id='$row[booking_id]'");
		}
		
		echo "document.getElementById('cbo_knitting_company').value 		= '".$row[csf("knitting_company")]."';\n";
		
		//echo "load_drop_down( 'requires/grey_fabric_receive_controller',document.getElementById('cbo_company_id').value+'_'+".$row[csf("location_id")].", 'load_drop_down_floor', 'floor_td' );\n";
		
		echo "document.getElementById('cbo_location_name').value 			= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('txt_yarn_issue_challan_no').value 	= '".$row[csf("yarn_issue_challan_no")]."';\n";
		echo "document.getElementById('txt_job_no').value 					= '".$job_no."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('update_id').value 					= '".$row[csf("id")]."';\n";
		echo "set_auto_complete();\n";
		
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_grey_fabric_receive',1);\n";  
		exit();
	}
}

if($action=="show_grey_prod_listview")
{
	$fabric_desc_arr=return_library_array("select id, item_description from product_details_master where item_category_id in(13,14)","id","item_description");
	$composition_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}

	$sql="select id, prod_id, body_part_id, febric_description_id, gsm, width, grey_receive_qnty, reject_fabric_receive, uom, yarn_lot, no_of_roll, brand_id, shift_name, machine_no_id from pro_grey_prod_entry_dtls where mst_id='$data' and status_active = '1' and is_deleted = '0'";
	$result=sql_select($sql);
	
	//$arr=array(0=>$body_part,1=>$composition_arr,6=>$unit_of_measurement,9=>$brand_arr,10=>$shift_name,11=>$machine_arr);
	//echo  create_list_view("list_view", "Body Part,Fabric Description,GSM,Dia / Width,Grey Rec. Qnty, Reject Feb. Qty, uom, Yarn Lot,No of Roll,Brand,Shift Name,Machine No", "80,140,50,60,80,80,60,60,60,80,80,60","930","200",0, $sql, "put_data_dtls_part", "id", "'populate_grey_details_form_data'", 0, "body_part_id,febric_description_id,0,0,0,0,uom,0,0,brand_id,shift_name,machine_no_id", $arr, "body_part_id,febric_description_id,gsm,width,grey_receive_qnty,reject_fabric_receive,uom,yarn_lot,no_of_roll,brand_id,shift_name,machine_no_id", "requires/grey_fabric_receive_controller",'','0,0,0,0,1,1,0,0,0,0,0,0');
?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="930" class="rpt_table">
        <thead>
            <th width="80">Body Part</th>
            <th width="120">Fabric Description</th>
            <th width="60">GSM</th>
            <th width="60">Dia / Width</th>
            <th width="80">Grey Recv. Qnty</th>
            <th width="80">Reject Feb. Qty</th>
            <th width="50">uom</th>
            <th width="80">Yarn Lot</th>
            <th width="60">No of Roll</th>
            <th width="80">Brand</th>
            <th width="80">Shift Name</th>
            <th>Machine No</th>
        </thead>
	</table>
	<div style="width:930px; max-height:200px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table" id="list_view">  
        <?
            $i=1;
            foreach($result as $row)
            {  
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	 
					
                if($row[csf('febric_description_id')]==0)
					$fabric_desc=$fabric_desc_arr[$row[csf('prod_id')]]; 
				else
					$fabric_desc=$composition_arr[$row[csf('febric_description_id')]];
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_data_dtls_part(<? echo $row[csf('id')]; ?>,'populate_grey_details_form_data', 'requires/grey_fabric_receive_controller');"> 
                    <td width="80"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
                    <td width="120"><p><? echo $fabric_desc; ?></p></td>
                    <td width="60"><p><? echo $row[csf('gsm')]; ?>&nbsp;</p></td>
                    <td width="60"><p><? echo $row[csf('width')]; ?>&nbsp;</p></td>
                    <td width="80" align="right"><? echo number_format($row[csf('grey_receive_qnty')],2); ?></td>
                    <td width="80" align="right"><? echo number_format($row[csf('reject_fabric_receive')],2); ?>&nbsp;</td>
                    <td width="50"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?>&nbsp;</p></td>
                    <td width="80"><p><? echo $row[csf('yarn_lot')]; ?>&nbsp;</p></td>
                    <td width="60"><p><? echo $row[csf('no_of_roll')]; ?>&nbsp;</p></td>
                    <td width="80"><p><? echo $brand_arr[$row[csf('brand_id')]]; ?></p></td>
                    <td width="80"><p><? echo $shift_name[$row[csf('shift_name')]]; ?></p></td>
                    <td><p><? echo $machine_arr[$row[csf('machine_no_id')]]; ?>&nbsp;</p></td>
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


if($action=='populate_grey_details_form_data')
{
	$data=explode("**",$data);
	$id=$data[0];
	$roll_maintained=$data[1];
	$fabric_nature=$data[2];
	
	if($fabric_nature==2) $entry_form=22; else $entry_form=23;
	
	$data_array=sql_select("select id, body_part_id, trans_id,	prod_id, febric_description_id, no_of_roll, gsm, width, grey_receive_qnty, reject_fabric_receive, uom, yarn_lot, yarn_count, brand_id, shift_name, floor_id, machine_no_id, order_id, room, rack, self, bin_box, color_id, color_range_id, stitch_length from pro_grey_prod_entry_dtls where id='$id'");
	foreach ($data_array as $row)
	{ 
		$comp='';
		if($row[csf('febric_description_id')]==0)
		{
			$comp=return_field_value("item_description","product_details_master","id=".$row[csf('prod_id')]);
		}
		else
		{
			$determination_sql=sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=$row[febric_description_id]");
					
			if($determination_sql[0][csf('construction')]!="")
			{
				$comp=$determination_sql[0][csf('construction')].", ";
			}
			
			foreach( $determination_sql as $d_row )
			{
				$comp.=$composition[$d_row[csf('copmposition_id')]]." ".$d_row[csf('percent')]."% ";
			}
		}
		
		echo "document.getElementById('cbo_body_part').value 				= '".$row[csf("body_part_id")]."';\n";
		echo "document.getElementById('txt_fabric_description').value 		= '".$comp."';\n";
		echo "document.getElementById('fabric_desc_id').value 				= '".$row[csf("febric_description_id")]."';\n";
		echo "document.getElementById('txt_gsm').value 						= '".$row[csf("gsm")]."';\n";
		echo "document.getElementById('txt_width').value 					= '".$row[csf("width")]."';\n";
		echo "document.getElementById('txt_roll_no').value 					= '".$row[csf("no_of_roll")]."';\n";
		echo "document.getElementById('txt_color').value 					= '".$color_arr[$row[csf("color_id")]]."';\n";  
		echo "document.getElementById('txt_stitch_length').value 			= '".$row[csf("stitch_length")]."';\n";
		echo "document.getElementById('cbo_color_range').value 				= '".$row[csf("color_range_id")]."';\n";
		echo "document.getElementById('txt_receive_qnty').value 			= '".$row[csf("grey_receive_qnty")]."';\n";
		echo "document.getElementById('txt_reject_fabric_recv_qnty').value 	= '".$row[csf("reject_fabric_receive")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("uom")]."';\n";
		echo "document.getElementById('txt_yarn_lot').value 				= '".$row[csf("yarn_lot")]."';\n";
		echo "document.getElementById('cbo_yarn_count').value 				= '".$row[csf("yarn_count")]."';\n";
		echo "set_multiselect('cbo_yarn_count','0','1','".$row[csf('yarn_count')]."','0');\n";
		echo "document.getElementById('txt_brand').value 					= '".$brand_arr[$row[csf("brand_id")]]."';\n";
		echo "document.getElementById('txt_shift_name').value 				= '".$row[csf("shift_name")]."';\n";
		echo "document.getElementById('cbo_floor_id').value 				= '".$row[csf("floor_id")]."';\n";
		
		echo "load_drop_down( 'requires/grey_fabric_receive_controller',document.getElementById('cbo_company_id').value+'_'+".$row[csf("floor_id")].", 'load_drop_machine', 'machine_td' );\n";
		
		echo "document.getElementById('cbo_machine_name').value 			= '".$row[csf("machine_no_id")]."';\n";
		echo "document.getElementById('txt_room').value 					= '".$row[csf("room")]."';\n";
		echo "document.getElementById('txt_rack').value 					= '".$row[csf("rack")]."';\n";
		echo "document.getElementById('txt_self').value 					= '".$row[csf("self")]."';\n";
		echo "document.getElementById('txt_binbox').value 					= '".$row[csf("bin_box")]."';\n";
		echo "document.getElementById('hidden_receive_qnty').value 			= '".$row[csf("grey_receive_qnty")]."';\n";
		echo "document.getElementById('all_po_id').value 					= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('product_id').value 					= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('previous_prod_id').value 			= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('update_trans_id').value 				= '".$row[csf("trans_id")]."';\n";
		
		$save_string='';
		if($roll_maintained==1)
		{
			$data_roll_array=sql_select("select id, roll_used, po_breakdown_id, qnty, roll_no from pro_roll_details where dtls_id='$id' and entry_form=$entry_form and status_active=1 and is_deleted=0");
			foreach($data_roll_array as $row_roll)
			{ 
				if($row_roll[csf('roll_used')]==1) $roll_id=$row_roll[csf('id')]; else $roll_id=0;
				//$roll_id=$row_roll[csf('id')];
				
				if($save_string=="")
				{
					$save_string=$row_roll[csf("po_breakdown_id")]."**".$row_roll[csf("qnty")]."**".$row_roll[csf("roll_no")]."**".$roll_id."**".$row_roll[csf("id")];
				}
				else
				{
					$save_string.=",".$row_roll[csf("po_breakdown_id")]."**".$row_roll[csf("qnty")]."**".$row_roll[csf("roll_no")]."**".$roll_id."**".$row_roll[csf("id")];
				}
			}
		}
		else
		{
			$data_po_array=sql_select("select po_breakdown_id, quantity from order_wise_pro_details where dtls_id='$id' and entry_form=$entry_form and status_active=1 and is_deleted=0");
			foreach($data_po_array as $row_po)
			{ 
				if($save_string=="")
				{
					$save_string=$row_po[csf("po_breakdown_id")]."**".$row_po[csf("quantity")];
				}
				else
				{
					$save_string.=",".$row_po[csf("po_breakdown_id")]."**".$row_po[csf("quantity")];
				}
			}
		}
		
		echo "document.getElementById('save_data').value 				= '".$save_string."';\n";
		
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_grey_fabric_receive',1);\n";  
		exit();
	}
}

if($action=="roll_duplication_check")
{
	$data=explode("**",$data);
	$po_id=$data[0];
	$roll_no=trim($data[1]);
	$roll_id=$data[2];
	
	if($roll_id=="" || $roll_id=="0")
	{
		$sql="select a.recv_number from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and b.po_breakdown_id='$po_id' and b.roll_no='$roll_no' and a.is_deleted=0 and a.status_active=1 and b.entry_form=1 and b.is_deleted=0 and b.status_active=1";
	}
	else
	{
		$sql="select a.recv_number from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and b.po_breakdown_id='$po_id' and b.roll_no='$roll_no' and a.is_deleted=0 and a.status_active=1 and b.entry_form=1 and b.id<>$roll_id and b.is_deleted=0 and b.status_active=1";
	}
	//echo $sql;
	$data_array=sql_select($sql,1);
	if(count($data_array)>0)
	{
		echo "1"."_".$data_array[0][csf('recv_number')];
	}
	else
	{
		echo "0_";
	}
	
	exit();	
}

if($action=="roll_maintained")
{
	$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$data' and variable_list=3 and is_deleted=0 and status_active=1");

	if($roll_maintained=="" || $roll_maintained==2) $roll_maintained=0; else $roll_maintained=$roll_maintained;
	
	echo "document.getElementById('roll_maintained').value 					= '".$roll_maintained."';\n";
	
	echo "reset_form('greyreceive_1','list_fabric_desc_container','','','set_receive_basis();','update_id*txt_recieved_id*cbo_company_id*cbo_receive_basis*txt_receive_date*txt_receive_chal_no*cbo_store_name*cbo_knitting_source*cbo_knitting_company*txt_remarks*roll_maintained*txt_yarn_issue_challan_no*txt_shift_name*txt_width*txt_gsm*cbo_floor_id*cbo_machine_name*txt_room*txt_rack*txt_reject_fabric_recv_qnty*txt_self*cbo_uom*txt_binbox*txt_yarn_lot*cbo_yarn_count*txt_brand*cbo_color_range');\n";
	
	exit();	
}

if($action=="load_color")
{
	$data=explode("**",$data);
	$booking_id=$data[0];
	$is_sample=$data[1];
	$receive_basis=$data[2];
	
	/*if($receive_basis==1)
	{
		if($is_sample==0)
		{
			$sql="select c.color_name from wo_booking_mst a, wo_booking_dtls b, lib_color c where a.booking_no=b.booking_no and b.fabric_color_id=c.id and a.id=$booking_id and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.fabric_color_id";
		}
		else
		{
			$sql="select c.color_name from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, lib_color c where a.booking_no=b.booking_no and b.fabric_color=c.id and a.id=$booking_id and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.fabric_color";
		}
	}
	else
	{
		$sql="select c.color_name from wo_booking_mst a, wo_booking_dtls b, lib_color c, ppl_planning_info_entry_mst d, ppl_planning_info_entry_dtls e where d.id=e.mst_id and  a.booking_no=b.booking_no and a.booking_no=d.booking_no and b.fabric_color_id=c.id and e.id=$booking_id and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.fabric_color_id";
	}*/
	
	if($is_sample==0)
	{
		$sql="select c.color_name from wo_booking_mst a, wo_booking_dtls b, lib_color c where a.booking_no=b.booking_no and b.fabric_color_id=c.id and a.id=$booking_id and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.fabric_color_id";
	}
	else
	{
		$sql="select c.color_name from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, lib_color c where a.booking_no=b.booking_no and b.fabric_color=c.id and a.id=$booking_id and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.fabric_color";
	}
	//echo $sql;die;
	echo "var str_color = [". substr(return_library_autocomplete( $sql, "color_name" ), 0, -1). "];\n";
	echo "$('#txt_color').autocomplete({
						 source: str_color
					  });\n";
	exit();	
}

if ($action=="grey_fabric_receive_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$sql="select id, recv_number, item_category, receive_basis, receive_date, challan_no, booking_id, store_id, knitting_source, knitting_company, location_id, yarn_issue_challan_no, buyer_id, fabric_nature from inv_receive_master where id='$data[1]' and company_id='$data[0]' ";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$wo_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no"  );
	$pi_arr=return_library_array( "select id, pi_number from  com_pi_master_details", "id", "pi_number"  );
	$po_arr=return_library_array( "select id, job_no from  wo_po_details_master", "id", "job_no"  );
	$job_arr=return_library_array( "select id, job_no from  wo_booking_mst", "id", "job_no"  );
	$job_arr=return_library_array( "select id, job_no from  wo_booking_mst", "id", "job_no"  );

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
        	<td width="120"><strong>Receive ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
            <td width="130"><strong>Receive Basis :</strong></td> <td width="175px"><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
            <td width="125"><strong>Receive Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
        </tr>
        <tr>
            <td><strong>Rec. Chal. No :</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong><? $show_label=""; if($dataArray[0][csf('fabric_nature')]==2) echo $show_label='WO/PI/Prod: '; else echo $show_label='WO/PI: '; ?></strong></td><td width="175px"><? if ($dataArray[0][csf('item_category')]==13 || $dataArray[0][csf('item_category')]==14 ) echo $pi_arr[$dataArray[0][csf('booking_id')]]; else if ($dataArray[0][csf('receive_basis')]==2) echo $wo_arr[$dataArray[0][csf('booking_id')]]; else $po_arr[$dataArray[0][csf('booking_id')]]; ?></td>
            <td><strong>Store:</strong></td> <td width="175px"><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Kniting Source :</strong></td><td width="175px"><? echo $knitting_source[$dataArray[0][csf('knitting_source')]]; ?></td>
            <td><strong>Kniting Com:</strong></td><td width="175px"><? if ($dataArray[0][csf('knitting_source')]==1) echo $company_library[$dataArray[0][csf('knitting_company')]]; else if($dataArray[0][csf('knitting_source')]==3) echo $supplier_library[$dataArray[0][csf('knitting_company')]];  ?></td>
            <td><strong>Issue Chal. No:</strong></td> <td width="175px"><? echo $dataArray[0][csf('yarn_issue_challan_no')]; ?></td>
        </tr>
        <tr>
            <td><strong>Job No:</strong></td><td width="175px"><? echo $job_arr[$dataArray[0][csf('booking_id')]]; ?></td>
            <td><strong>Buyer:</strong></td><td width="175px"><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td>
            <td><strong>&nbsp;</strong></td><td width="175px">&nbsp;</td>
        </tr>
    </table>
        <br>
	<div style="width:100%;">
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="100" >Body Part</th>
            <th width="160" >Feb. Description</th>
            <th width="40" >GSM</th>
            <th width="50" >Dia/ Width</th>
            <th width="40" >UoM</th> 
            <th width="70" >Grey Qnty</th>
            <th width="70" >Reject Qnty</th>
            <th width="60" >Yarn Lot</th>
            <th width="50" >No of Roll</th>
            <th width="80" >Brand</th>
            <th width="60" >Shift Name</th> 
            <th width="70" >Machine No</th>
        </thead>
        <tbody> 
   
<?
	$composition_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}

	$sql_dtls="select id, body_part_id, febric_description_id, gsm, width, grey_receive_qnty, reject_fabric_receive, uom, yarn_lot, no_of_roll, brand_id, shift_name, machine_no_id from pro_grey_prod_entry_dtls where mst_id='$data[1]' and status_active = '1' and is_deleted = '0'";

	$sql_result= sql_select($sql_dtls);
	$i=1;
	$group_arr=return_library_array( "select id, item_name from  lib_item_group", "id", "item_name"  );
	foreach($sql_result as $row)
	{
		if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
			
			$grey_receive_qnty=$row[csf('grey_receive_qnty')];
			$grey_receive_qnty_sum += $grey_receive_qnty;
			
			$reject_fabric_receive=$row[csf('reject_fabric_receive')];
			$reject_fabric_receive_sum += $reject_fabric_receive;
		?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td align="center"><? echo $i; ?></td>
                <td><? echo $body_part[$row[csf("body_part_id")]]; ?></td>
                <td><? echo $composition_arr[$row[csf("febric_description_id")]]; ?></td>
                <td><? echo $row[csf("gsm")]; ?></td>
                <td><? echo $row[csf("width")]; ?></td>
                <td><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
                <td align="right"><? echo $row[csf("grey_receive_qnty")]; ?></td>
                <td  align="right"><? echo $row[csf("reject_fabric_receive")]; ?></td>
                <td align="center"><? echo $row[csf("yarn_lot")]; ?></td>
                <td align="center"><? echo $row[csf("no_of_roll")]; ?></td>
                <td align="center"><? echo $brand_arr[$row[csf("brand_id")]]; ?></td>
                <td><? echo $shift_name[$row[csf("shift_name")]]; ?></td>
                <td align="center"><? echo $machine_arr[$row[csf("machine_no_id")]]; ?></td>
			</tr>
			<? $i++; } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" align="right"><strong>Total :</strong></td>
                <td align="right"><?php echo $grey_receive_qnty_sum; ?></td>
                <td align="right" ><?php echo $reject_fabric_receive_sum; ?></td>
                <td colspan="5">&nbsp;</td>
            </tr>                           
        </tfoot>
      </table>
        <br>
		 <?
            echo signature_table(16, $data[0], "900px");
         ?>
      </div>
   </div>         
<?
exit();
}


?>
