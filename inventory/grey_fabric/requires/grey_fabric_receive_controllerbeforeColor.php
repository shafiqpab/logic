<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//$distribiution_method=array(1=>"Proportionately",2=>"Manually");
$distribiution_method=array(1=>"Distribute Based On Lowest Shipment Date",2=>"Manually");

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
	
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[1]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",$data[0] );  
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
	echo create_drop_down( "cbo_store_name", 152, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[0]' and b.category_type=$category_id and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
	exit();
}

if ($action=="load_drop_down_floor")
{
	$data=explode("_",$data);
	$company_id=$data[0];
	$location_id=$data[1];
	if($location_id==0 || $location_id=="") $location_cond=""; else $location_cond=" and b.location_id=$location_id";
	
	echo create_drop_down( "cbo_floor_id", 132, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1 and b.company_id=$company_id and b.status_active=1 and b.is_deleted=0 and a.production_process=2 $location_cond group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "-- Select Floor --", 0, "load_drop_down( 'requires/grey_fabric_receive_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_machine', 'machine_td' );","" );
  exit();	 
}

if ($action=="load_drop_machine")
{
	$data=explode("_",$data);
	$company_id=$data[0];
	$floor_id=$data[1];
	if($floor_id==0 || $floor_id=="") $floor_cond=""; else $floor_cond=" and floor_id=$floor_id";
	
	echo create_drop_down( "cbo_machine_name", 132, "select id, machine_no as machine_name from lib_machine_name where category_id=1 and company_id=$company_id and status_active=1 and is_deleted=0 and is_locked=0 $floor_cond order by machine_no","id,machine_name", 1, "-- Select Machine --", 0, "","" );
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
		echo create_drop_down( "cbo_knitting_company", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select Knit Company--", 1, "" );
	}
	else
	{
		echo create_drop_down( "cbo_knitting_company", 152, $blank_array,"",1, "--Select Knit Company--", 1, "" );
	}
	exit();
}

if($action=="issue_num_check")
{
	$issue_no=return_field_value("issue_number_prefix_num as issue_number_prefix_num","inv_issue_master","status_active=1 and is_deleted=0 and entry_form=3 and issue_number_prefix_num=$data","issue_number_prefix_num");
	echo $issue_no;
	exit();
}


if ($action=="wo_pi_production_popup")
{
	echo load_html_head_contents("WO/PI/Production Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 
	<script>
	
		function js_set_value(id,no,type,buyer_id,data)
		{
			$('#hidden_wo_pi_production_id').val(id);
			$('#hidden_wo_pi_production_no').val(no);
			$('#booking_without_order').val(type);
			$('#hidden_buyer_id').val(buyer_id);
			$('#hidden_production_data').val(data);
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
                    <th width="240">Enter WO/PI/Production No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                    	<input type="hidden" name="hidden_wo_pi_production_id" id="hidden_wo_pi_production_id" class="text_boxes" value="">  
                        <input type="hidden" name="hidden_wo_pi_production_no" id="hidden_wo_pi_production_no" class="text_boxes" value=""> 
                        <input type="hidden" name="booking_without_order" id="booking_without_order" class="text_boxes" value="">
                        <input type="hidden" name="hidden_buyer_id" id="hidden_buyer_id" class="text_boxes" value="">
                        <input type="hidden" name="hidden_production_data" id="hidden_production_data" class="text_boxes" value=""> 
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
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('pi_number')]; ?>','0','0','');"> 
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
		$po_arr=array();
		$po_data=sql_select("select b.id, b.po_number, b.pub_shipment_date, b.po_quantity, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst");	
		foreach($po_data as $row)
		{
			$po_arr[$row[csf('id')]]=$row[csf('po_number')]."**".$row[csf('pub_shipment_date')]."**".$row[csf('po_quantity')]."**".$row[csf('po_qnty_in_pcs')];
		}
		
		if(trim($data[0])!="")
		{
			$search_field_cond="and a.booking_no like '$search_string'";
			$search_field_cond_sample="and s.booking_no like '$search_string'";
		}
		else
		{
			$search_field_cond="";
		}
		
		$sql = "select a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.buyer_id, a.po_break_down_id, a.item_category, a.delivery_date, a.job_no as job_no_mst, 0 as type from wo_booking_mst a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_id=$company_id and a.item_category=$category_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.fabric_source in (2,3) $search_field_cond group by a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.buyer_id, a.po_break_down_id, a.item_category, a.delivery_date, a.job_no
				union all
				SELECT s.id, s.booking_no_prefix_num, s.booking_no, s.booking_date, s.buyer_id, null as po_break_down_id, s.item_category, s.delivery_date, null as job_no_mst, 1 as type FROM wo_non_ord_samp_booking_mst s WHERE s.company_id=$company_id and s.status_active=1 and s.is_deleted=0 and s.fabric_source in (2,3) and s.item_category=$category_id $search_field_cond_sample order by type, id"; 
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
					
					if($row[csf('po_break_down_id')]!="" && $row[csf('type')]==0)
					{
						/*$po_sql="select b.po_number, b.pub_shipment_date, b.po_quantity, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in (".$row[csf('po_break_down_id')].")";									
						$nameArray=sql_select($po_sql);
						foreach ($nameArray as $po_row)*/
						
						$po_id=explode(",",$row[csf('po_break_down_id')]);
						foreach ($po_id as $id)
						{
							$po_data=explode("**",$po_arr[$id]);
							$po_number=$po_data[0];
							$pub_shipment_date=$po_data[1];
							$po_qnty=$po_data[2];
							$poQntyPcs=$po_data[3];
							
							if($po_no=="") $po_no=$po_number; else $po_no.=",".$po_number;
							
							if($min_shipment_date=='')
							{
								$min_shipment_date=$pub_shipment_date;
							}
							else
							{
								if($pub_shipment_date<$min_shipment_date) $min_shipment_date=$pub_shipment_date; else $min_shipment_date=$min_shipment_date;
							}
							
							$po_qnty_in_pcs+=$poQntyPcs;
							
							/*if($po_no=="") $po_no=$po_row[csf('po_number')]; else $po_no.=",".$po_row[csf('po_number')];
							
							if($min_shipment_date=='')
							{
								$min_shipment_date=$po_row[csf('pub_shipment_date')];
							}
							else
							{
								if($po_row[csf('pub_shipment_date')]<$min_shipment_date) $min_shipment_date=$po_row[csf('pub_shipment_date')]; else $min_shipment_date=$min_shipment_date;
							}
							
							$po_qnty_in_pcs+=$po_row[csf('po_qnty_in_pcs')];*/
						}
					}
					
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('booking_no')]; ?>','<? echo $row[csf('type')]; ?>','0','');"> 
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
		
		if($db_type==0)
		{
			$order_id_arr=return_library_array( "select mst_id, group_concat(order_id) as order_id from pro_grey_prod_entry_dtls where status_active=1 and is_deleted=0 group by mst_id",'mst_id','order_id');
			//$order_id=return_field_value("group_concat(order_id) as order_id","pro_grey_prod_entry_dtls","mst_id=".$row[csf('id')]." and status_active=1 and is_deleted=0","order_id");
		}
		else
		{
			$order_id_arr=return_library_array( "select mst_id, LISTAGG(cast(order_id as VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY order_id) as order_id from pro_grey_prod_entry_dtls where status_active=1 and is_deleted=0 group by mst_id",'mst_id','order_id');
			//$order_id=return_field_value("wm_concat(cast(order_id as VARCHAR2(4000))) as order_id","pro_grey_prod_entry_dtls","mst_id=".$row[csf('id')]." and status_active=1 and is_deleted=0","order_id");
		}
		
		if($db_type==0) $year_field="YEAR(insert_date)"; 
		else if($db_type==2) $year_field="to_char(insert_date,'YYYY')";
		else $year_field="";//defined Later
	
		$sql = "select id, recv_number_prefix_num, booking_without_order, $year_field as year, recv_number, buyer_id, knitting_source, knitting_company, receive_date, challan_no ,yarn_issue_challan_no from inv_receive_master where entry_form=2 and status_active=1 and is_deleted=0 and company_id=$company_id $search_field_cond"; 
		//echo $sql;
		$result = sql_select($sql);
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table">
			<thead>
				<th width="30">SL</th>
                <th width="60">Prod. No</th>
                <th width="50">Year</th>
				<th width="80">Receive Date</th>
                <th width="70">Challan No</th>
				<th width="125">Knitting Source</th>
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
					
					$order_id=$order_id_arr[$row[csf('id')]];
					$order_id=array_unique(explode(",",$order_id));
					
					$order_no=''; $style_ref='';
					foreach($order_id as $value)
					{
						if($order_no=='') $order_no=$po_array[$value]['no']; else $order_no.=",".$po_array[$value]['no'];
						if($style_ref=='') $style_ref=$po_array[$value]['style']; else $style_ref.=",".$po_array[$value]['style'];
					}
					
					$style_ref=array_unique(explode(",",$style_ref));
					$style_ref=implode(",",$style_ref);
					
					$data=$row[csf('knitting_source')]."**".$row[csf('knitting_company')]."**".$row[csf('challan_no')]."**".$row[csf('yarn_issue_challan_no')];
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('recv_number')]; ?>','<? echo $row[csf('booking_without_order')]; ?>','<? echo $row[csf('buyer_id')]; ?>','<? echo $data; ?>');"> 
						<td width="30"><? echo $i; ?></td>
						<td width="60">&nbsp;&nbsp;<? echo $row[csf('recv_number_prefix_num')]; ?></td>
                        <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                        <td width="80" align="center"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>   
                        <td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>            
						<td width="125"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
                        <td width="110"><p><? echo $knit_comp; ?></p></td>
                        <td width="100"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
						<td width="120"><p><? echo $style_ref; ?>&nbsp;</p></td>
						<td><p><? echo $order_no; ?>&nbsp;</p></td>
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
		if($row[csf('febric_description_id')]==0 || $row[csf('febric_description_id')]=="")
		{
			$comp = return_field_value("item_description","product_details_master","id=".$row[csf('prod_id')]);
		}
		else
		{
			$determination_sql=sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=".$row[csf('febric_description_id')]);
					
			if($determination_sql[0][csf('construction')]!="")
			{
				$comp=$determination_sql[0][csf('construction')].", ";
			}
			
			foreach($determination_sql as $d_row )
			{
				$comp.=$composition[$d_row[csf('copmposition_id')]]." ".$d_row[csf('percent')]."% ";
			}
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
			$data_roll_array=sql_select("select id, roll_used, po_breakdown_id, qnty, roll_no from pro_roll_details where dtls_id='$id' and entry_form=2 and status_active=1 and is_deleted=0");
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
		$sql="select determination_id, '' as body_part_id, gsm as gsm_weight, dia_width, sum(quantity) as qnty from com_pi_item_details where pi_id='$booking_pi_production_no' and status_active=1 and is_deleted=0 group by determination_id, gsm, dia_width";
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
		$cons_comps_arr = return_library_array("select id, item_description from product_details_master where item_category_id=13","id","item_description");
		$sql="select id, prod_id, febric_description_id as determination_id, body_part_id, gsm as gsm_weight, width as dia_width, grey_receive_qnty as qnty from pro_grey_prod_entry_dtls where mst_id='$booking_pi_production_no' and status_active=1 and is_deleted=0";
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
				
				if($receive_basis==2 && $is_sample==1 && ($row[csf('determination_id')]==0 || $row[csf('determination_id')]==""))
				{
					$fabric_desc.=$row[csf('construction')].", ".$row[csf('composition')].", ".$row[csf('gsm_weight')];
				}
				else if($receive_basis==9)
				{
					if($row[csf('determination_id')]==0 || $row[csf('determination_id')]=="")
					{
						$fabric_desc.=$cons_comps_arr[$row[csf('prod_id')]].", ".$row[csf('gsm_weight')];
					}
					else
					{
						$fabric_desc.=$composition_arr[$row[csf('determination_id')]].", ".$row[csf('gsm_weight')];
					}
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
					if($receive_basis==2 && $is_sample==1 && ($row[csf('determination_id')]==0 || $row[csf('determination_id')]==""))
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
				$composition_arr=array();
				$compositionData=sql_select("select mst_id, copmposition_id, percent from lib_yarn_count_determina_dtls where status_active=1 and is_deleted=0");
				foreach( $compositionData as $row )
				{
					$composition_arr[$row[csf('mst_id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
				}
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
                        <th>Composition</th>
                        <th width="100">GSM/Weight</th>
                    </thead>
                </table>
                <div style="width:700px; max-height:300px; overflow-y:scroll" id="list_container" align="left"> 
                    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="680" id="tbl_list_search">  
                        <? 
                        $i=1;
						$data_array=sql_select("select id, construction, fab_nature_id, gsm_weight from lib_yarn_count_determina_mst where fab_nature_id='$garments_nature' and status_active=1 and is_deleted=0");
                        foreach($data_array as $row)
                        {  
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            
							if($row[csf('construction')]!="")
							{    
                            	$comp=$row[csf('construction')].", ";
							}
							$comp.=$composition_arr[$row[csf('id')]];
							
                            /*$determ_sql=sql_select("select copmposition_id, percent from lib_yarn_count_determina_dtls where mst_id=".$row[csf('id')]." and status_active=1 and is_deleted=0");
                            foreach( $determ_sql as $d_row )
                            {
                                $comp.=$composition[$d_row[csf('copmposition_id')]]." ".$d_row[csf('percent')]."% ";
                            }*/
                            
                         ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $comp; ?>','<? echo $row[csf('gsm_weight')]; ?>')" style="cursor:pointer" >
                                <td width="50"><? echo $i; ?></td>
                                <td width="100"><? echo $item_category[$row[csf('fab_nature_id')]]; ?></td>
                                <td width="150"><p><? echo $row[csf('construction')]; ?></p></td>
                                <td><p><? echo $comp; ?></p></td>
                                <td width="100"><? echo $row[csf('gsm_weight')]; ?></td>
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
		$txt_deleted_id=$data[7]; 
	}
	
	$recv_qnty_array=array();
	if($receive_basis==2)
	{	
		if($dtls_id=="") $dtls_id_cond=""; else $dtls_id_cond="and a.dtls_id<>$dtls_id";
		
		/*$recvData=sql_select("select a.po_breakdown_id, 
					sum(case when a.entry_form in(22) then quantity end) as grey_fabric_recv,
					sum(case when a.entry_form=13 and a.trans_type=5 then quantity end) as grey_fabric_trans_recv, 
					sum(case when a.entry_form=13 and a.trans_type=6 then quantity end) as grey_fabric_trans_issued
	 from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and b.detarmination_id='$fabric_desc_id' and b.gsm='$txt_gsm' and b.dia_width='$txt_width' and b.item_category_id=13 and a.is_deleted=0 and a.status_active=1 $dtls_id_cond group by a.po_breakdown_id");*/
	 	$recvData=sql_select("select a.po_breakdown_id, sum(quantity) as grey_fabric_recv from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and b.detarmination_id='$fabric_desc_id' and b.gsm='$txt_gsm' and b.dia_width='$txt_width' and b.item_category_id=13 and a.entry_form=22 and a.is_deleted=0 and a.status_active=1 $dtls_id_cond group by a.po_breakdown_id");
	 	foreach($recvData as $row)
		{
			$recv_qnty_array[$row[csf('po_breakdown_id')]]=$row[csf('grey_fabric_recv')]+$row[csf('grey_fabric_trans_recv')]-$row[csf('grey_fabric_trans_issued')];	
		}
	}
	
	if($roll_maintained==1) 
	{
		$width="775";
		$roll_arr=return_library_array("select po_breakdown_id,max(roll_no) as roll_no from pro_roll_details where entry_form in(2,22) group by po_breakdown_id",'po_breakdown_id','roll_no');
	}
	else $width="620";
?> 

	<script>
		var receive_basis=<? echo $receive_basis; ?>;
		var roll_maintained=<? echo $roll_maintained; ?>;
		var barcode_generation=<? echo $barcode_generation; ?>;
		
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
				$('#txt_prop_grey_qnty').attr('disabled',false);
				var txt_prop_grey_qnty=$('#txt_prop_grey_qnty').val()*1;
				var tblRow = $("#tbl_list_search tr").length;
				var balance =txt_prop_grey_qnty;
				var len=totalGrey=0;
				
				$("#tbl_list_search").find('tr').each(function()
				{
					len=len+1;
					
					var txtOrginal=$(this).find('input[name="txtOrginal[]"]').val()*1;
					var isDisbled=$(this).find('input[name="txtGreyQnty[]"]').is(":disabled");
					var placeholder_value =$(this).find('input[name="txtGreyQnty[]"]').attr('placeholder')*1;
					
					if(txtOrginal==0)
					{
						$(this).remove();
					}
					else if(isDisbled==false && txtOrginal==1)
					{
						if(balance>0)
						{
							if(placeholder_value<0) placeholder_value=0;
							if(balance>placeholder_value)
							{
								var grey_qnty=placeholder_value;
								balance=balance-placeholder_value;
							}
							else
							{
								var grey_qnty=balance;
								balance=0;
							}
							
							if(tblRow==len)
							{
								var grey_qnty=txt_prop_grey_qnty-totalGrey;							
							}
							
							totalGrey = totalGrey*1+grey_qnty*1;

							$(this).find('input[name="txtGreyQnty[]"]').val(grey_qnty.toFixed(2));
						}
						else
						{
							$(this).find('input[name="txtGreyQnty[]"]').val('');
						}
					}
				});
			}
			else
			{
				$('#txt_prop_grey_qnty').val('');
				$('#txt_prop_grey_qnty').attr('disabled',true);
				$("#tbl_list_search").find('tr').each(function()
				{
					if($(this).find('input[name="txtGreyQnty[]"]').is(":disabled")==false)
					{
						$(this).find('input[name="txtGreyQnty[]"]').val('');
					}
				});
			}
			
			calculate_tot_qnty();
		}
		
		/*function distribute_qnty(str)
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
		}*/
		
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
				//var row_num=$('#tbl_list_search tr').length;
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
				
				$('#txtOrginal_'+row_num).removeAttr("value").attr("value","0");
				
				$('#txtRoll_'+row_num).removeAttr("value").attr("value","");
				$('#txtGreyQnty_'+row_num).removeAttr("value").attr("value","");
				$('#txtRoll_'+row_num).removeAttr("onBlur").attr("onBlur","roll_duplication_check("+row_num+");");
				$('#txtRollTableId_'+row_num).removeAttr("value").attr("value","");
				$('#txtBarcodeNo_'+row_num).removeAttr("value").attr("value","");
					
				$('#increase_'+row_num).removeAttr("value").attr("value","+");
				$('#decrease_'+row_num).removeAttr("value").attr("value","-");
				$('#increase_'+row_num).removeAttr("onclick").attr("onclick","add_break_down_tr("+row_num+");");
				$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
				
				$('#txt_tot_row').val(row_num);
				set_all_onclick();
			}
		}
		
		function fn_deleteRow(rowNo) 
		{ 
			var cbo_distribiution_method=$('#cbo_distribiution_method').val();
			
			if(cbo_distribiution_method==2)
			{
				var txtOrginal=$('#txtOrginal_'+rowNo).val()*1;
				var txtBarcodeNo=$('#txtBarcodeNo_'+rowNo).val();
				var txt_deleted_id=$('#hide_deleted_id').val();
				var selected_id='';
				if(txtOrginal==0)
				{
					if(txtBarcodeNo!='')
					{
						if(txt_deleted_id=='') selected_id=txtBarcodeNo; else selected_id=txt_deleted_id+','+txtBarcodeNo;
						$('#hide_deleted_id').val( selected_id );
					}
					$("#tr_"+rowNo).remove();
				}
			}
			
			calculate_tot_qnty();
		}
		
		function calculate_tot_qnty()
		{
			var tot_grey_qnty='';
			$("#tbl_list_search").find('tr').each(function()
			{
				var txtGreyQnty=$(this).find('input[name="txtGreyQnty[]"]').val()*1;
				tot_grey_qnty=tot_grey_qnty*1+txtGreyQnty*1;
			});
			
			$('#txt_tot_grey_qnty').val( tot_grey_qnty.toFixed(2));
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
			show_list_view ( po_id+'_'+'1'+'_'+'<? echo $dtls_id; ?>'+'_'+'<? echo $roll_maintained; ?>'+'_'+'<? echo $save_data; ?>'+'_'+'<? echo $prev_distribution_method; ?>'+'_'+'<? echo $receive_basis; ?>'+'_'+'<? echo $txt_deleted_id; ?>', 'po_popup', 'search_div', 'grey_fabric_receive_controller', '');
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
				var txtBarcodeNo=$(this).find('input[name="txtBarcodeNo[]"]').val();
				
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
						save_string=txtPoId+"**"+txtGreyQnty+"**"+txtRoll+"**"+txtRollId+"**"+txtRollTableId+"**"+txtBarcodeNo;
					}
					else
					{
						save_string+=","+txtPoId+"**"+txtGreyQnty+"**"+txtRoll+"**"+txtRollId+"**"+txtRollTableId+"**"+txtBarcodeNo;
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
		
		/*function check_all_report()
		{
			$("input[name=chkBundle]").each(function(index, element) { 
					
					if( $('#check_all').prop('checked')==true) 
						$(this).attr('checked','true');
					else
						$(this).removeAttr('checked');
			});
		}*/
		
		/*function fnc_send_printer_text()
		{
			var dtls_id='<?// echo $dtls_id; ?>';
			if(dtls_id=="")
			{
				alert("Save First");	
				return;
			}
			var data="";
			var error=1;
			$("input[name=chkBundle]").each(function(index, element) {
				if( $(this).prop('checked')==true)
				{
					error=0;
					var idd=$(this).attr('id').split("_");
					var roll_id=$('#txtRollTableId_'+idd[1] ).val();
					if(roll_id!="")
					{
						if(data=="") data=$('#txtRollTableId_'+idd[1] ).val(); else data=data+","+$('#txtRollTableId_'+idd[1] ).val();
					}
					else
					{
						$(this).prop('checked',false);
					}
				}
			});
		
			if( error==1 )
			{
				alert('No data selected');
				return;
			}
			
			data=data+"***"+dtls_id;
			var url=return_ajax_request_value(data, "report_barcode_text_file", "grey_fabric_receive_controller");
			window.open(url+".zip","##");
		}*/
		
		/*function fnc_barcode_generation()
		{
			var dtls_id='<?//echo $dtls_id; ?>';
			if(dtls_id=="")
			{
				alert("Save First");	
				return;
			}
			var data="";
			var error=1;
			$("input[name=chkBundle]").each(function(index, element) {
				if( $(this).prop('checked')==true)
				{
					error=0;
					var idd=$(this).attr('id').split("_");
					var roll_id=$('#txtRollTableId_'+idd[1] ).val();
					if(roll_id!="")
					{
						if(data=="") data=$('#txtRollTableId_'+idd[1] ).val(); else data=data+","+$('#txtRollTableId_'+idd[1] ).val();
					}
					else
					{
						$(this).prop('checked',false);
					}
				}
			});
		
			if( error==1 )
			{
				alert('No data selected');
				return;
			}
			
			data=data+"***"+dtls_id;
			window.open("grey_fabric_receive_controller.php?data=" + data+'&action=report_barcode_generation', true );
		}*/
		
    </script>

</head>

<body>
	<? 
	if($type!=1)
	{
	?>
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:<? echo $width; ?>px;margin-left:10px">
        	<input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
            <input type="hidden" name="tot_grey_qnty" id="tot_grey_qnty" class="text_boxes" value="">
            <input type="hidden" name="number_of_roll" id="number_of_roll" class="text_boxes" value="">
            <input type="hidden" name="all_po_id" id="all_po_id" class="text_boxes" value="">
            <input type="hidden" name="distribution_method" id="distribution_method" class="text_boxes" value="">
            <input type="hidden" name="hide_deleted_id" id="hide_deleted_id" class="text_boxes" value="<? echo $txt_deleted_id; ?>">
	<?
	}
	
	if(($receive_basis==1 || $receive_basis==4 || $receive_basis==6) && $type!=1)
	{
	?>
		<table cellpadding="0" cellspacing="0" width="<? echo $width; ?>" class="rpt_table">
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
						echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",$data[0] ); 
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
				<div style="width:<? echo $width-20; ?>px; margin-top:10px" align="center">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300" align="center">
						<thead>
							<th>Total Grey Qnty</th>
							<th>Distribution Method</th>
						</thead>
						<tr class="general">
							<td><input type="text" name="txt_prop_grey_qnty" id="txt_prop_grey_qnty" class="text_boxes_numeric" value="<? echo $txt_receive_qnty; ?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)" disabled></td>
							<td>
								<? 
									echo create_drop_down( "cbo_distribiution_method", 250, $distribiution_method,"",0, "",2, "distribute_qnty(this.value);",1 );
								?>
							</td>
						</tr>
					</table>
                    <?
						/*if($roll_maintained==1)
						{
							if($barcode_generation==2) 
							{
							?>
								<input type="button" id="btn_send_to_printer" name="btn_send_to_printer" value="Send To Printer" class="formbutton" onClick="fnc_send_printer_text()"/>
							<?
							}
							else
							{
							?>
								<input type="button" id="btn_barcode_generation" name="btn_barcode_generation" value="Barcode Generation" class="formbutton" onClick="fnc_barcode_generation()"/>
							<?	
							}
						}*/
					?>
				</div>
				<div style="margin-left:5px; margin-top:10px">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $width-20; ?>">
						<thead>
							<th width="120">PO No</th>
							<th width="80">PO Qnty</th>
                            <th width="80">Ship. Date</th>
							<th>Grey Qnty</th>
							<?
							if($roll_maintained==1)
							{
							?>
								<th width="80">Roll</th>
                                <th width="80">Barcode No.</th>
								<th width="65"></th>
                                <!--<th width="60">Check All <input type="checkbox" name="check_all"  id="check_all" onClick="check_all_report()"></th>-->
							<?
							}
							?>
						</thead>
					</table>
					<div style="width:<? echo $width; ?>px; max-height:200px; overflow-y:scroll" id="list_container" align="left"> 
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $width-20; ?>" id="tbl_list_search">  
							<? 
							$i=1; $tot_po_qnty=0; $tot_grey_qnty=0; $po_array=array();  

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
								$barcode_no=$po_wise_data[5];
								
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
								
								$tot_grey_qnty+=$grey_qnty;
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
									<td width="80" align="right">
										<? echo $po_data[0][csf('po_qnty_in_pcs')] ?>
										<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_data[0][csf('po_qnty_in_pcs')]; ?>">
									</td>
                                    <td width="80" align="center"><? echo change_date_format($po_data[0][csf('pub_shipment_date')]); ?></td>
									<td align="center">
										<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $grey_qnty; ?>" <? echo $disable; ?> onKeyUp="calculate_tot_qnty();">
									</td>
									<?
									if($roll_maintained==1)
									{
									?>
										<td width="80" align="center">
											<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? if($roll_no!=0) echo $roll_no; ?>" <? echo $disable; ?> placeholder="<? echo $roll_arr[$order_id]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" />
										</td>
                                        <td width="80"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? echo $barcode_no; ?>" disabled/></td>
										<td width="65">
											<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
											<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
										</td>
                                        <!--<td width="60" align="center"><input id="chkBundle_<?// echo $i;  ?>" type="checkbox" name="chkBundle"></td>-->
									<?
									}
									?>
								</tr>
							<? 
							$i++;
							}
							?>
							<input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes" value="<? echo $tot_po_qnty; ?>">
                            <input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="<? echo $i-1; ?>">
						</table>
					</div>
                    <table width="<? echo $width-20; ?>" border="1" cellpadding="0" cellspacing="0" rules="all" class="tbl_bottom">
                    	<tr>
                        	<td width="120"></td>
                            <td width="80"></td>
                            <td width="80" align="right"><b>Total</b></td>
                        	<td style="text-align:center"><input type="text" name="txt_tot_grey_qnty" id="txt_tot_grey_qnty" class="text_boxes_numeric" style="width:80px" value="<? echo number_format($tot_grey_qnty,2); ?>" disabled></td>
                            <?
							if($roll_maintained==1)
							{
								echo '<td width="80"></td><td width="80"></td><td width="65"></td>';
							}
							?>
                        </tr>
                    </table>
					<table width="<? echo $width; ?>">
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
		$disabled=""; $disabled_dropdown=0;
		if(($receive_basis==1 || $receive_basis==4 || $receive_basis==6 || $receive_basis==9) || $roll_maintained==1) 
		{
			$prev_distribution_method=2;
			$disabled="disabled='disabled'";
			$disabled_dropdown=1;
		}
	?>
		<div style="width:<? echo $width-20; ?>px; margin-top:10px" align="center">
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300" align="center">
				<thead>
					<th>Total Grey Qnty</th>
					<th>Distribution Method</th>
				</thead>
				<tr class="general">
					<td><input type="text" name="txt_prop_grey_qnty" id="txt_prop_grey_qnty" class="text_boxes_numeric" value="<? echo $txt_receive_qnty; ?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)" <? echo $disabled; ?>></td>
					<td>
						<? 
							echo create_drop_down( "cbo_distribiution_method", 250, $distribiution_method,"",0, "",$prev_distribution_method, "distribute_qnty(this.value);",$disabled_dropdown );
						?>
					</td>
				</tr>
			</table>
            <?
				/*if($roll_maintained==1)
				{
					if($barcode_generation==2) 
					{
					?>
						<input type="button" id="btn_send_to_printer" name="btn_send_to_printer" value="Send To Printer" class="formbutton" onClick="fnc_send_printer_text()"/>
					<?
					}
					else
					{
					?>
						<input type="button" id="btn_barcode_generation" name="btn_barcode_generation" value="Barcode Generation" class="formbutton" onClick="fnc_barcode_generation()"/>
					<?	
					}
				}*/
			?>
		</div>
		<div style="margin-left:5px; margin-top:10px">
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $width-20; ?>">
				<thead>
					<th width="120">PO No</th>
					<th width="80">PO Qnty</th>
                    <th width="80">Ship. Date</th>
                    <?
					if($receive_basis==2)
					{
						echo '<th width="80">Req. Qnty</th>';
					}
					?>
					<th>Grey Qnty</th>
                    <?
					if($roll_maintained==1)
					{
					?>
                        <th width="80">Roll</th>
                        <th width="80">Barcode No.</th>
                        <th width="65"></th>
                        <!--<th width="60">Check All <input type="checkbox" name="check_all"  id="check_all" onClick="check_all_report()"></th>-->
					<?
                    }
                    ?>
				</thead>
			</table>
			<div style="width:<? echo $width; ?>px; max-height:200px; overflow-y:scroll" id="list_container" align="left"> 
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $width-20; ?>" id="tbl_list_search">  
					<? 
					$i=1; $tot_po_qnty=0; $tot_grey_qnty=0; $po_array=array();
					
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
							$barcode_no=$po_wise_data[5];
							
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
								
								$tot_grey_qnty+=$grey_qnty;
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
									<td width="80" align="right">
										<? echo $po_data[0][csf('po_qnty_in_pcs')] ?>
										<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_data[0][csf('po_qnty_in_pcs')]; ?>">
									</td>
                                    <td width="80" align="center"><? echo change_date_format($po_data[0][csf('pub_shipment_date')]); ?></td>
									<td align="center">
										<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $grey_qnty; ?>" <? echo $disable; ?> onKeyUp="calculate_tot_qnty();">
									</td>
									<?
									if($roll_maintained==1)
									{
									?>
										<td width="80" align="center">
											<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? if($roll_no!=0) echo $roll_no; ?>" <? echo $disable; ?> placeholder="<? echo $roll_arr[$order_id]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" />
										</td>
                                        <td width="80"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? echo $barcode_no; ?>" disabled/></td>
										<td width="65">
											<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
											<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
										</td>
                                        <!--<td width="60" align="center"><input id="chkBundle_<?// echo $i; ?>" type="checkbox" name="chkBundle"></td>-->
									<?
									}
									?>
								</tr>
							<? 
							$i++;
							}
						}
						
						if(count($po_array)<1)
						{
							$result=implode(",",$po_id);
						}
						else
						{
							$result=implode(",",array_diff($po_id, $po_array));
						}
						
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
									<td width="80" align="right">
										<? echo $row[csf('po_qnty_in_pcs')]; ?>
										<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $row[csf('po_qnty_in_pcs')]; ?>">
									</td>
                                    <td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
									<td align="center">
										<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="" onKeyUp="calculate_tot_qnty();">
									</td>
									<?
									if($roll_maintained==1)
									{
									?>
										<td width="80" align="center">
											<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="" onBlur="roll_duplication_check(<? echo $i; ?>);" placeholder="<? echo $roll_arr[$row[csf('id')]]+1; ?>" />
										</td>
                                        <td width="80"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="" disabled/></td>
										<td width="65">
											<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
											<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
										</td>
                                        <!--<td width="60" align="center"><input id="chkBundle_<?// echo $i; ?>" type="checkbox" name="chkBundle"></td>-->
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
						$booking_qnty_array=return_library_array("select a.po_break_down_id, sum(a.grey_fab_qnty) as qnty from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b where a.pre_cost_fabric_cost_dtls_id=b.id and a.booking_no='$booking_no' and b.lib_yarn_count_deter_id='$fabric_desc_id' and b.body_part_id='$cbo_body_part' and b.gsm_weight='$txt_gsm' and a.dia_width='$txt_width' and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 group by a.po_break_down_id","po_break_down_id","qnty");
						if($roll_maintained==1)
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
								$barcode_no=$po_wise_data[5]; 
								
								$req_qnty=$booking_qnty_array[$order_id];
								$bl_qnty=$req_qnty-$recv_qnty_array[$order_id];
								
								$po_data=sql_select("select b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$order_id");
								
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

								$tot_grey_qnty+=$grey_qnty;
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
									<td width="80" align="right">
										<? echo $po_data[0][csf('po_qnty_in_pcs')]; ?>
										<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_data[0][csf('po_qnty_in_pcs')]; ?>">
									</td>
									<td width="80" align="center"><? echo change_date_format($po_data[0][csf('pub_shipment_date')]); ?></td>
									<td width="80" align="right"><? echo number_format($req_qnty,2,'.',''); ?></td>
									<td align="center">
										<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $grey_qnty; ?>" <? echo $disable; ?> placeholder="<? echo number_format($bl_qnty,2,'.',''); ?>" onKeyUp="calculate_tot_qnty();">
									</td>
									<td width="80" align="center">
                                        <input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? if($roll_no!=0) echo $roll_no; ?>" <? echo $disable; ?> placeholder="<? echo $roll_arr[$order_id]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" />
                                    </td>
                                    <td width="80"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? echo $barcode_no; ?>" disabled/></td>
                                    <td width="65">
                                        <input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
                                        <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
                                    </td>
                                    <!--<td width="60" align="center"><input id="chkBundle_<?// echo $i; ?>" type="checkbox" name="chkBundle"></td>-->
								</tr>
							<? 
								$i++;
							}
							if($db_type==0)
							{
								$booking_po_id=return_field_value("group_concat(distinct(po_break_down_id)) as po_id","wo_booking_dtls","booking_no='$booking_no' and status_active=1 and is_deleted=0","po_id");
							}
							else
							{
								$booking_po_id=return_field_value("LISTAGG(po_break_down_id, ',') WITHIN GROUP (ORDER BY po_break_down_id) as po_id","wo_booking_dtls","booking_no='$booking_no' and status_active=1 and is_deleted=0","po_id");
							}
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
									$req_qnty=$booking_qnty_array[$row[csf('id')]];
									$bl_qnty=$req_qnty-$recv_qnty_array[$row[csf('id')]];
									
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
										<td width="80" align="right">
											<? echo $row[csf('po_qnty_in_pcs')]; ?>
											<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $row[csf('po_qnty_in_pcs')]; ?>">
										</td>
										<td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
										<td width="80" align="right"><? echo number_format($req_qnty,2,'.',''); ?></td>
										<td align="center">
											<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="" placeholder="<? echo number_format($bl_qnty,2,'.',''); ?>" onKeyUp="calculate_tot_qnty();">
										</td>
										<td width="80" align="center">
                                            <input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="" onBlur="roll_duplication_check(<? echo $i; ?>);" placeholder="<? echo $roll_arr[$row[csf('id')]]+1; ?>" />
                                        </td>
                                        <td width="80"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="" disabled/></td>
                                        <td width="65">
                                            <input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
                                            <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
                                        </td>
										<!--<td width="60" align="center"><input id="chkBundle_<?//echo $i;  ?>" type="checkbox" name="chkBundle"></td>-->
									</tr>
								<? 
								$i++; 
								}
							}
						}
						else
						{
							$prev_po_qnty_arr=array();
							$explSaveData = explode(",",$save_data); 
							for($z=0;$z<count($explSaveData);$z++)
							{
								$po_wise_data = explode("**",$explSaveData[$z]);
								$order_id=$po_wise_data[0];
								$grey_qnty=$po_wise_data[1];
								$prev_po_qnty_arr[$order_id]=$grey_qnty;
							}
							$po_sql="select b.id, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.booking_no='$booking_no' and c.status_active=1 and c.is_deleted=0 group by b.id, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date order by b.pub_shipment_date, b.id";
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
								
								$po_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
								$tot_po_qnty+=$po_qnty_in_pcs;
								
								$grey_qnty=$prev_po_qnty_arr[$row[csf('id')]];
								$req_qnty=$booking_qnty_array[$row[csf('id')]];
								$tot_grey_qnty+=$grey_qnty;
								$bl_qnty=$req_qnty-$recv_qnty_array[$row[csf('id')]];
								
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
									<td width="80" align="right">
										<? echo $po_qnty_in_pcs; ?>
										<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_qnty_in_pcs; ?>">
									</td>
                                    <td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                                    <td width="80" align="right"><? echo number_format($req_qnty,2,'.',''); ?></td>
									<td align="center">
										<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $grey_qnty; ?>" placeholder="<? echo number_format($bl_qnty,2,'.',''); ?>" onKeyUp="calculate_tot_qnty();">
									</td>
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
							$barcode_no=$po_wise_data[5]; 
							
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
							
							$tot_grey_qnty+=$grey_qnty;
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
								<td width="80" align="right">
									<? echo $po_data[0][csf('po_qnty_in_pcs')]; ?>
									<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_data[0][csf('po_qnty_in_pcs')]; ?>">
								</td>
                                <td width="80" align="center"><? echo change_date_format($po_data[0][csf('pub_shipment_date')]); ?></td>
								<td align="center">
									<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $grey_qnty; ?>" onKeyUp="calculate_tot_qnty();">
								</td>
								<?
								if($roll_maintained==1)
								{
								?>
									<td width="80" align="center">
										<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? if($roll_no!=0) echo $roll_no; ?>" <? echo $disable; ?> placeholder="<? echo $roll_arr[$order_id]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" />
									</td>
                                    <td width="80"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? echo $barcode_no; ?>" disabled/></td>
									<td width="65">
										<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
										<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
									</td>
                                    <!--<td width="60" align="center"><input id="chkBundle_<?// echo $i; ?>" type="checkbox" name="chkBundle"></td>-->
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
								$po_sql="select b.id, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($po_id) order by b.pub_shipment_date, b.id";
							}
						}
						else
						{
							$po_sql="select b.id, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.booking_no='$booking_no' and c.status_active=1 and c.is_deleted=0 group by b.id, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date order by b.pub_shipment_date, b.id";
							$booking_qnty_array=return_library_array("select a.po_break_down_id, sum(a.grey_fab_qnty) as qnty from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b where a.pre_cost_fabric_cost_dtls_id=b.id and a.booking_no='$booking_no' and b.lib_yarn_count_deter_id='$fabric_desc_id' and b.body_part_id='$cbo_body_part' and b.gsm_weight='$txt_gsm' and a.dia_width='$txt_width' and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 group by a.po_break_down_id","po_break_down_id","qnty");
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
							$po_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
							$tot_po_qnty+=$po_qnty_in_pcs;
							
							$tot_grey_qnty+=$grey_qnty;
							
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
								<td width="80" align="right">
									<? echo $po_qnty_in_pcs; ?>
									<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_qnty_in_pcs; ?>">
								</td>
                                <td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                                <?
								if($receive_basis==2)
								{
									$req_qnty=$booking_qnty_array[$row[csf('id')]];
									$bl_qnty=$req_qnty-$recv_qnty_array[$row[csf('id')]];
									echo '<td width="80" align="right">'.number_format($req_qnty,2,'.','').'</td>';
								}
								else {$bl_qnty=''; $req_qnty='';}
								?>
								<td align="center">
									<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $grey_qnty; ?>" <? echo $disable; ?> placeholder="<? if($receive_basis==2) echo number_format($bl_qnty,2,'.',''); ?>" onKeyUp="calculate_tot_qnty();">
								</td>
								<?
								if($roll_maintained==1)
								{
								?>
									<td width="80" align="center">
										<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? if($roll_no!=0) echo $roll_no; ?>" <? echo $disable; ?> placeholder="<? echo $roll_arr[$row[csf('id')]]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" />
									</td>
                                    <td width="80"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="" disabled/></td>
									<td width="65">
										<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
										<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
									</td>
                                    <!--<td width="60" align="center"><input id="chkBundle_<?// echo $i; ?>" type="checkbox" name="chkBundle"></td>-->
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
                    <input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="<? echo $i-1; ?>">
				</table>
			</div>
            <table width="<? echo $width-20; ?>" border="1" cellpadding="0" cellspacing="0" rules="all" class="tbl_bottom">
                <tr>
                    <td width="120"></td>
                    <td width="80"></td>
                    <?
					if($receive_basis==2)
					{
						echo '<td width="80"></td>';
					}
					?>
                    <td width="80" align="right"><b>Total</b></td>
                    <td style="text-align:center"><input type="text" name="txt_tot_grey_qnty" id="txt_tot_grey_qnty" class="text_boxes_numeric" style="width:80px" value="<? echo number_format($tot_grey_qnty,2); ?>" disabled></td>
                    <?
                    if($roll_maintained==1)
                    {
                        echo '<td width="80"></td><td width="80"></td><td width="65"></td>';
                    }
                    ?>
                </tr>
            </table>
			<table width="<? echo $width; ?>">
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
	
	$sql = "select a.job_no, a.style_ref_no, a.order_uom, b.id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id and a.buyer_name=$buyer_id and $search_field like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; 
	//echo $sql;die;// $po_id_cond group by b.id
	?>
    <div align="center">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="618" class="rpt_table" >
            <thead>
                <th width="40">SL</th>
                <th width="100">Job No</th>
                <th width="110">Style No</th>
                <th width="120">PO No</th>
                <th width="90">PO Quantity</th>
                <th width="60">UOM</th>
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
						
						$roll_data_array=sql_select("select roll_no from pro_roll_details where po_breakdown_id=".$selectResult[csf('id')]." and roll_used=1 and entry_form in(2,22) and status_active=1 and is_deleted=0");
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
                        <td width="120"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                        <td width="90" align="right"><? echo $selectResult[csf('po_qnty_in_pcs')]; ?></td> 
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
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			$new_grey_recv_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', $prefix, date("Y",time()), 5, "select recv_number_prefix, recv_number_prefix_num from inv_receive_master where company_id=$cbo_company_id and entry_form='$entry_form' and $year_cond=".date('Y',time())." order by id desc", "recv_number_prefix", "recv_number_prefix_num" ));
		 	
			$id=return_next_id( "id", "inv_receive_master", 1 ) ;
					 
			$field_array="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, receive_basis, company_id, receive_date, challan_no, booking_id, booking_no, booking_without_order, store_id, location_id, knitting_source, knitting_company, buyer_id, yarn_issue_challan_no, remarks, fabric_nature, inserted_by, insert_date";
			
			$data_array="(".$id.",'".$new_grey_recv_system_id[1]."',".$new_grey_recv_system_id[2].",'".$new_grey_recv_system_id[0]."',$entry_form,$category_id,".$cbo_receive_basis.",".$cbo_company_id.",".$txt_receive_date.",".$txt_receive_chal_no.",".$txt_booking_no_id.",".$txt_booking_no.",".$booking_without_order.",".$cbo_store_name.",".$cbo_location_name.",".$cbo_knitting_source.",".$cbo_knitting_company.",".$cbo_buyer_name.",".$txt_yarn_issue_challan_no.",".$txt_remarks.",".$garments_nature.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//echo "insert into pro_grey_prod_entry_mst (".$field_array.") values ".$data_array;die;
			/*$rID=sql_insert("inv_receive_master",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;*/
			
			$grey_recv_num=$new_grey_recv_system_id[0];
			$grey_update_id=$id;
		}
		else
		{
			$field_array_update="receive_basis*receive_date*challan_no*booking_id*booking_no*booking_without_order*store_id*location_id*knitting_source*knitting_company*buyer_id*yarn_issue_challan_no*remarks*updated_by*update_date";
			
			$data_array_update=$cbo_receive_basis."*".$txt_receive_date."*".$txt_receive_chal_no."*".$txt_booking_no_id."*".$txt_booking_no."*".$booking_without_order."*".$cbo_store_name."*".$cbo_location_name."*".$cbo_knitting_source."*".$cbo_knitting_company."*".$cbo_buyer_name."*".$txt_yarn_issue_challan_no."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			/*$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0;*/ 
			
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
			$stock= return_field_value("current_stock","product_details_master","id=$product_id");
			$cur_st_value=0; $cur_st_rate=0;
			$cur_st_qnty=$stock+str_replace("'","",$txt_receive_qnty);
			$field_array_prod_update="current_stock*avg_rate_per_unit*stock_value";
			$data_array_prod_update=$cur_st_qnty."*".$cur_st_value."*".$cur_st_rate;
			
			/*$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			} */
			
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
				
				/*$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,0);
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				} */
			}
			else
			{
				$prod_id=return_next_id( "id", "product_details_master", 1 ) ;
				
				$avg_rate_per_unit=0; $stock_value=0;
				$prod_name_dtls=trim(str_replace("'","",$txt_fabric_description)).", ".trim(str_replace("'","",$txt_gsm)).", ".trim(str_replace("'","",$txt_width));
				$field_array_prod="id, company_id, store_id, item_category_id, detarmination_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, brand, gsm, dia_width, lot, inserted_by, insert_date";
				
				$data_array_prod="(".$prod_id.",".$cbo_company_id.",".$cbo_store_name.",$category_id,".$fabric_desc_id.",".$txt_fabric_description.",'".$prod_name_dtls."',".$cbo_uom.",".$avg_rate_per_unit.",".$txt_receive_qnty.",".$txt_receive_qnty.",".$stock_value.",".$brand_id.",".$txt_gsm.",".$txt_width.",".$txt_yarn_lot.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				//echo "insert into product_details_master (".$field_array_prod.") values ".$data_array_prod."chd".$txt_fabric_description;die;
				/*$rID2=sql_insert("product_details_master",$field_array_prod,$data_array_prod,0);
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				} */
			}
		}

		$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		
		$order_rate=0; $order_amount=0; $cons_rate=0; $cons_amount=0;
		
		$field_array_trans="id, mst_id, receive_basis, pi_wo_batch_no, company_id, prod_id, item_category, transaction_type, transaction_date, store_id, brand_id, order_uom, order_qnty, order_rate, order_amount, cons_uom, cons_quantity, cons_reject_qnty, cons_rate, cons_amount, balance_qnty, balance_amount, floor_id, machine_id, room, rack, self, bin_box, inserted_by, insert_date";
		
		$data_array_trans="(".$id_trans.",".$grey_update_id.",".$cbo_receive_basis.",".$txt_booking_no_id.",".$cbo_company_id.",".$prod_id.",$category_id,1,".$txt_receive_date.",".$cbo_store_name.",".$brand_id.",".$cbo_uom.",".$txt_receive_qnty.",".$order_rate.",".$order_amount.",".$cbo_uom.",".$txt_receive_qnty.",".$txt_reject_fabric_recv_qnty.",".$cons_rate.",".$cons_amount.",".$txt_receive_qnty.",".$cons_amount.",".$cbo_floor_id.",".$cbo_machine_name.",".$txt_room.",".$txt_rack.",".$txt_self.",".$txt_binbox.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		/*$rID3=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} */
		
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
		
		/*$rID4=sql_insert("pro_grey_prod_entry_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		} */
		
		$barcode_year=date("y");  
		$barcode_suffix_no=return_field_value("max(barcode_suffix_no) as suffix_no","pro_roll_details","barcode_year=$barcode_year","suffix_no")+1;// and entry_form=$entry_form
		$barcode_no=$barcode_year.$entry_form.str_pad($barcode_suffix_no,7,"0",STR_PAD_LEFT);
		
		$field_array_roll="id, barcode_year,barcode_suffix_no,barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, inserted_by, insert_date";
		$save_string=explode(",",str_replace("'","",$save_data));
		$id_roll = return_next_id( "id", "pro_roll_details", 1 ); 
		
		$po_array=array();
		
		for($i=0;$i<count($save_string);$i++)
		{
			$order_dtls=explode("**",$save_string[$i]);
			
			$order_id=$order_dtls[0];
			$order_qnty_roll_wise=$order_dtls[1];
			$roll_no=$order_dtls[2];
			
			if($data_array_roll!="") $data_array_roll.= ",";
			$data_array_roll.="(".$id_roll.",".$barcode_year.",".$barcode_suffix_no.",".$barcode_no.",".$grey_update_id.",".$id_dtls.",'".$order_id."',$entry_form,'".$order_qnty_roll_wise."','".$roll_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id_roll = $id_roll+1;
			$barcode_suffix_no=$barcode_suffix_no+1;
			$barcode_no=$barcode_year.$entry_form.str_pad($barcode_suffix_no,7,"0",STR_PAD_LEFT);
			
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
		/*if($data_array_roll!="" && str_replace("'","",$roll_maintained)==1 && str_replace("'","",$booking_without_order)!=1)
		{
			$rID5=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			} 
		}*/
		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, inserted_by, insert_date";
		$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		foreach($po_array as $key=>$val)
		{
			$order_id=$key;
			$order_qnty=$val;
			
			if($data_array_prop!="") $data_array_prop.= ",";
			$data_array_prop.="(".$id_prop.",".$id_trans.",1,$entry_form,".$id_dtls.",'".$order_id."',".$prod_id.",'".$color_id."','".$order_qnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id_prop = $id_prop+1;
		}
		
		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_receive_master",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; 
		}
		
		if(str_replace("'", '',$cbo_receive_basis)==9)
		{
			$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			} 
		}
		else
		{
			if(count($row_prod)>0)
			{
				$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,0);
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				} 
			}
			else
			{
				$rID2=sql_insert("product_details_master",$field_array_prod,$data_array_prod,0);
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				} 
			}
		}
		
		$rID3=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		
		$rID4=sql_insert("pro_grey_prod_entry_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		} 
		
		if($data_array_roll!="" && str_replace("'","",$roll_maintained)==1 && str_replace("'","",$booking_without_order)!=1)
		{
			$rID5=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			} 
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
				echo "0**".$grey_update_id."**".$grey_recv_num."**0**".$id_dtls;
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
				echo "0**".$grey_update_id."**".$grey_recv_num."**0**".$id_dtls;
			}
			else
			{
				oci_rollback($con);
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
		
		/*$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,0);
		if($rID) $flag=1; else $flag=0;*/
		
		$stock= return_field_value("current_stock","product_details_master","id=$previous_prod_id");
		/*$adjust_curr_stock=$stock-str_replace("'", '',$hidden_receive_qnty);
		$cur_st_value=0; $cur_st_rate=0;
		
		$field_array_adjust="current_stock*avg_rate_per_unit*stock_value";
		$data_array_adjust=$adjust_curr_stock."*".$cur_st_value."*".$cur_st_rate;
		
		$rID_adjust=sql_update("product_details_master",$field_array_adjust,$data_array_adjust,"id",$previous_prod_id,0);
		if($flag==1) 
		{
			if($rID_adjust) $flag=1; else $flag=0; 
		} 

		$brand_id=return_id( $txt_brand, $brand_arr, "lib_brand", "id,brand_name");
		if($brand_id=="") $brand_id=0;
		$color_id=return_id( $txt_color, $color_arr, "lib_color", "id,color_name");
		if($color_id=="") $color_id=0;*/
		
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
			if(str_replace("'","",$product_id)==str_replace("'","",$previous_prod_id))
			{
				$cur_st_value=0; $cur_st_rate=0;
				$cur_st_qnty=$stock+str_replace("'", '',$txt_receive_qnty)-str_replace("'", '',$hidden_receive_qnty);
				$field_array_prod_update="current_stock*avg_rate_per_unit*stock_value";
				$data_array_prod_update=$cur_st_qnty."*".$cur_st_value."*".$cur_st_rate;
				
				/*$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				} */
			}
			else
			{
				$adjust_curr_stock=$stock-str_replace("'", '',$hidden_receive_qnty);
				$cur_st_value=0; $cur_st_rate=0;
				
				$field_array_adjust="current_stock*avg_rate_per_unit*stock_value";
				$data_array_adjust=$adjust_curr_stock."*".$cur_st_value."*".$cur_st_rate;
				
				/*$rID_adjust=sql_update("product_details_master",$field_array_adjust,$data_array_adjust,"id",$previous_prod_id,0);
				if($flag==1) 
				{
					if($rID_adjust) $flag=1; else $flag=0; 
				}*/ 
				$current_stock= return_field_value("current_stock","product_details_master","id=$product_id");
				$cur_st_value=0; $cur_st_rate=0;
				$cur_st_qnty=$current_stock+str_replace("'", '',$txt_receive_qnty);
				$field_array_prod_update="current_stock*avg_rate_per_unit*stock_value";
				$data_array_prod_update=$cur_st_qnty."*".$cur_st_value."*".$cur_st_rate;
				
				/*$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				} */
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
				if($prod_id==str_replace("'","",$previous_prod_id))
				{
					$stock_qnty=$row_prod[0][csf('current_stock')];
					$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_receive_qnty)-str_replace("'", '',$hidden_receive_qnty);
					$avg_rate_per_unit=0; $stock_value=0;
		
					$field_array_prod_update="store_id*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*brand*lot*updated_by*update_date";
					$data_array_prod_update=$cbo_store_name."*".$avg_rate_per_unit."*".$txt_receive_qnty."*".$curr_stock_qnty."*".$stock_value."*".$brand_id."*".$txt_yarn_lot."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
					
					/*$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,0);
					if($flag==1) 
					{
						if($rID2) $flag=1; else $flag=0; 
					}*/
				}
				else
				{
					$adjust_curr_stock=$stock-str_replace("'", '',$hidden_receive_qnty);
					$cur_st_value=0; $cur_st_rate=0;
					
					$field_array_adjust="current_stock*avg_rate_per_unit*stock_value";
					$data_array_adjust=$adjust_curr_stock."*".$cur_st_value."*".$cur_st_rate;
					
					/*$rID_adjust=sql_update("product_details_master",$field_array_adjust,$data_array_adjust,"id",$previous_prod_id,0);
					if($flag==1) 
					{
						if($rID_adjust) $flag=1; else $flag=0; 
					} */
					
					$stock_qnty=$row_prod[0][csf('current_stock')];
					$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_receive_qnty);
					$avg_rate_per_unit=0; $stock_value=0;
		
					$field_array_prod_update="store_id*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*brand*lot*updated_by*update_date";
					$data_array_prod_update=$cbo_store_name."*".$avg_rate_per_unit."*".$txt_receive_qnty."*".$curr_stock_qnty."*".$stock_value."*".$brand_id."*".$txt_yarn_lot."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
					
					/*$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,0);
					if($flag==1) 
					{
						if($rID2) $flag=1; else $flag=0; 
					}*/	
				}
			}
			else
			{
				$adjust_curr_stock=$stock-str_replace("'", '',$hidden_receive_qnty);
				$cur_st_value=0; $cur_st_rate=0;
				
				$field_array_adjust="current_stock*avg_rate_per_unit*stock_value";
				$data_array_adjust=$adjust_curr_stock."*".$cur_st_value."*".$cur_st_rate;
				
				/*$rID_adjust=sql_update("product_details_master",$field_array_adjust,$data_array_adjust,"id",$previous_prod_id,0);
				if($flag==1) 
				{
					if($rID_adjust) $flag=1; else $flag=0; 
				} */
				
				$prod_id=return_next_id( "id", "product_details_master", 1 ) ;
				$avg_rate_per_unit=0; $stock_value=0;
				
				$prod_name_dtls=trim(str_replace("'","",$txt_fabric_description)).", ".trim(str_replace("'","",$txt_gsm)).", ".trim(str_replace("'","",$txt_width));
				$field_array_prod="id, company_id, store_id, item_category_id, detarmination_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, brand, gsm, dia_width, lot, inserted_by, insert_date";
				
				$data_array_prod="(".$prod_id.",".$cbo_company_id.",".$cbo_store_name.",$category_id,".$fabric_desc_id.",".$txt_fabric_description.",'".$prod_name_dtls."',".$cbo_uom.",".$avg_rate_per_unit.",".$txt_receive_qnty.",".$txt_receive_qnty.",".$stock_value.",".$brand_id.",".$txt_gsm.",".$txt_width.",".$txt_yarn_lot.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				/*$rID2=sql_insert("product_details_master",$field_array_prod,$data_array_prod,0);
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				}*/ 
			}
		}
		
		$order_rate=0; $order_amount=0; $cons_rate=0; $cons_amount=0;
		$sqlBl = sql_select("select cons_quantity,cons_amount,balance_qnty,balance_amount from inv_transaction where id=$update_trans_id");
		$before_receive_qnty	= $sqlBl[0][csf("cons_quantity")]; 
		$beforeAmount			= $sqlBl[0][csf("cons_amount")];
		$beforeBalanceQnty		= $sqlBl[0][csf("balance_qnty")]; 
		$beforeBalanceAmount	= $sqlBl[0][csf("balance_amount")];
		
		$adjBalanceQnty		=$beforeBalanceQnty-$before_receive_qnty+str_replace("'", '',$txt_receive_qnty);
		$adjBalanceAmount	=$beforeBalanceAmount-$beforeAmount+$con_amount; 
		
		$field_array_trans_update="receive_basis*pi_wo_batch_no*prod_id*transaction_date*store_id*brand_id*order_qnty*order_rate*order_amount*cons_quantity*cons_reject_qnty*cons_rate*cons_amount*balance_qnty*balance_amount*floor_id*machine_id*room*rack*self*bin_box*updated_by*update_date";
		
		$data_array_trans_update=$cbo_receive_basis."*".$txt_booking_no_id."*".$prod_id."*".$txt_receive_date."*".$cbo_store_name."*".$brand_id."*".$txt_receive_qnty."*".$order_rate."*".$order_amount."*".$txt_receive_qnty."*".$txt_reject_fabric_recv_qnty."*".$cons_rate."*".$cons_amount."*".$adjBalanceQnty."*".$adjBalanceAmount."*".$cbo_floor_id."*".$cbo_machine_name."*".$txt_room."*".$txt_rack."*".$txt_self."*".$txt_binbox."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		/*$rID4=sql_update("inv_transaction",$field_array_trans_update,$data_array_trans_update,"id",$update_trans_id,0);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		} */
		
		$cbo_yarn_count=explode(",",str_replace("'","",$cbo_yarn_count));
		asort($cbo_yarn_count);
		$cbo_yarn_count=implode(",",$cbo_yarn_count);
		
		$txt_yarn_lot=explode(",",str_replace("'","",$txt_yarn_lot));
		asort($txt_yarn_lot);
		$txt_yarn_lot=implode(",",$txt_yarn_lot);
		$rate=0; $amount=0;
		
		$field_array_dtls_update="prod_id*body_part_id*febric_description_id*gsm*width*no_of_roll*order_id*grey_receive_qnty*reject_fabric_receive*rate*amount*uom*yarn_lot*yarn_count*brand_id*shift_name*floor_id*machine_no_id*room*rack*self*bin_box*color_id*color_range_id*stitch_length*updated_by*update_date";
		
		$data_array_dtls_update=$prod_id."*".$cbo_body_part."*".$fabric_desc_id."*".$txt_gsm."*".$txt_width."*".$txt_roll_no."*".$all_po_id."*".$txt_receive_qnty."*".$txt_reject_fabric_recv_qnty."*".$rate."*".$amount."*".$cbo_uom."*'".$txt_yarn_lot."'*'".$cbo_yarn_count."'*".$brand_id."*".$txt_shift_name."*".$cbo_floor_id."*".$cbo_machine_name."*".$txt_room."*".$txt_rack."*".$txt_self."*".$txt_binbox."*".$color_id."*".$cbo_color_range."*".$txt_stitch_length."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		/*$rID5=sql_update("pro_grey_prod_entry_dtls",$field_array_dtls_update,$data_array_dtls_update,"id",$update_dtls_id,0);
		if($flag==1) 
		{
			if($rID5) $flag=1; else $flag=0; 
		} */
		
		$barcode_year=date("y");  
		$barcode_suffix_no=return_field_value("max(barcode_suffix_no) as suffix_no","pro_roll_details","barcode_year=$barcode_year","suffix_no")+1;// and entry_form=$entry_form
		$barcode_no=$barcode_year.$entry_form.str_pad($barcode_suffix_no,7,"0",STR_PAD_LEFT);
		
		$field_array_roll="id, barcode_year,barcode_suffix_no,barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, inserted_by, insert_date";
		$field_array_roll_update="po_breakdown_id*qnty*roll_no*updated_by*update_date";
		$save_string=explode(",",str_replace("'","",$save_data));
		$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		$po_array=array();
		
		for($i=0;$i<count($save_string);$i++)
		{
			$order_dtls=explode("**",$save_string[$i]);
			
			$order_id=$order_dtls[0];
			$order_qnty_roll_wise=$order_dtls[1];
			$roll_no=$order_dtls[2];
			$roll_not_delete_id=$order_dtls[3];
			$roll_id=$order_dtls[4];

			/*if($roll_not_delete_id==0)
			{
				if($data_array_roll!="") $data_array_roll.= ",";
				$data_array_roll.="(".$id_roll.",".$update_id.",".$update_dtls_id.",'".$order_id."',$entry_form,'".$order_qnty_roll_wise."','".$roll_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				 $id_roll = $id_roll+1;
			}
			else
			{
				if($not_delete_roll_table_id=="") $not_delete_roll_table_id=$roll_id; else $not_delete_roll_table_id.=",".$roll_id;
			}*/
			
			if($roll_id=="" || $roll_id==0)
			{
				if($data_array_roll!="") $data_array_roll .= ",";
				$data_array_roll.="(".$id_roll.",".$barcode_year.",".$barcode_suffix_no.",".$barcode_no.",".$update_id.",".$update_dtls_id.",'".$order_id."',$entry_form,'".$order_qnty_roll_wise."','".$roll_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id_roll = $id_roll+1;
			}
			else
			{
				$roll_id_arr[]=$roll_id;
				$roll_data_array_update[$roll_id]=explode("*",($order_id."*'".$order_qnty_roll_wise."'*'".$roll_no."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
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
		
		/*if(str_replace("'","",$roll_maintained)==1)
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
		}*/
		
		/*$delete_prop=execute_query( "delete from order_wise_pro_details where dtls_id=$update_dtls_id and trans_id=$update_trans_id and entry_form=$entry_form",0);
		if($flag==1) 
		{
			if($delete_prop) $flag=1; else $flag=0; 
		}*/
		
		
		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, inserted_by, insert_date";
		$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		foreach($po_array as $key=>$val)
		{
			$order_id=$key;
			$order_qnty=$val;
			
			if($data_array_prop!="") $data_array_prop.= ",";
			$data_array_prop.="(".$id_prop.",".$update_trans_id.",1,$entry_form,".$update_dtls_id.",'".$order_id."',".$prod_id.",'".$color_id."','".$order_qnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id_prop = $id_prop+1;
		}
		
		$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,0);
		if($rID) $flag=1; else $flag=0;
		
		if(str_replace("'", '',$cbo_receive_basis)==9)
		{
			if(str_replace("'","",$product_id)==str_replace("'","",$previous_prod_id))
			{
				$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				} 
			}
			else
			{
				$rID_adjust=sql_update("product_details_master",$field_array_adjust,$data_array_adjust,"id",$previous_prod_id,0);
				if($flag==1) 
				{
					if($rID_adjust) $flag=1; else $flag=0; 
				} 
				
				$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				} 
			}
		}
		else
		{
			if(count($row_prod)>0)
			{
				$prod_id=$row_prod[0][csf('id')];
				if($prod_id==str_replace("'","",$previous_prod_id))
				{
					$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,0);
					if($flag==1) 
					{
						if($rID2) $flag=1; else $flag=0; 
					}
				}
				else
				{
					$rID_adjust=sql_update("product_details_master",$field_array_adjust,$data_array_adjust,"id",$previous_prod_id,0);
					if($flag==1) 
					{
						if($rID_adjust) $flag=1; else $flag=0; 
					} 
					
					$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,0);
					if($flag==1) 
					{
						if($rID2) $flag=1; else $flag=0; 
					}	
				}
			}
			else
			{
				$rID_adjust=sql_update("product_details_master",$field_array_adjust,$data_array_adjust,"id",$previous_prod_id,0);
				if($flag==1) 
				{
					if($rID_adjust) $flag=1; else $flag=0; 
				} 
				
				$rID2=sql_insert("product_details_master",$field_array_prod,$data_array_prod,0);
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				} 
			}
		}
		
		$rID4=sql_update("inv_transaction",$field_array_trans_update,$data_array_trans_update,"id",$update_trans_id,0);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		} 
		
		$rID5=sql_update("pro_grey_prod_entry_dtls",$field_array_dtls_update,$data_array_dtls_update,"id",$update_dtls_id,0);
		if($flag==1) 
		{
			if($rID5) $flag=1; else $flag=0; 
		} 
		
		if(str_replace("'","",$roll_maintained)==1)
		{
			/*if($not_delete_roll_table_id=="") $delete_cond=""; else $delete_cond="and id not in($not_delete_roll_table_id)"; 
	
			$delete_roll=execute_query( "delete from pro_roll_details where dtls_id=$update_dtls_id and entry_form=$entry_form $delete_cond",0);
			if($flag==1) 
			{
				if($delete_roll) $flag=1; else $flag=0; 
			} */
			
			$txt_deleted_id=str_replace("'","",$txt_deleted_id);
			if($txt_deleted_id!="")
			{
				$field_array_status="updated_by*update_date*status_active*is_deleted";
				$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		
				$statusChange=sql_multirow_update("pro_roll_details",$field_array_status,$data_array_status,"id",$txt_deleted_id,0);
				if($flag==1) 
				{
					if($statusChange) $flag=1; else $flag=0; 
				} 
			}

			if(count($roll_data_array_update)>0)
			{
				$rollUpdate=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_update, $roll_data_array_update, $roll_id_arr ));
				if($flag==1)
				{
					if($rollUpdate) $flag=1; else $flag=0;
				}
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
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_recieved_id)."**0**".str_replace("'", '', $update_dtls_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**0**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_recieved_id)."**0**".str_replace("'", '', $update_dtls_id);
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
            <table cellpadding="0" cellspacing="0" width="820" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Buyer</th>
                    <th>Received Date Range</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="200">Please Enter Received ID</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                    	<input type="hidden" name="hidden_recv_id" id="hidden_recv_id" class="text_boxes" value="">  
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">
                    	<?
							echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] ); 
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
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", 2,$dd,0 );
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
		if($db_type==0)
		{
			$date_cond="and receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
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
	
	if($db_type==0) $year_field="YEAR(insert_date)"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY')";
	else $year_field="";//defined Later
	
	$sql = "select id, recv_number_prefix_num, recv_number, booking_no, buyer_id, knitting_source, knitting_company, receive_date, challan_no, $year_field as year from inv_receive_master where entry_form=$entry_form and fabric_nature=$garments_nature and status_active=1 and is_deleted=0 and company_id=$company_id and buyer_id like '$buyer_name' $search_field_cond $date_cond"; 
	//echo $sql;die;
	$result = sql_select($sql);

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$grey_recv_arr=return_library_array( "select mst_id, sum(grey_receive_qnty) as recv from pro_grey_prod_entry_dtls where status_active=1 and is_deleted=0 group by mst_id",'mst_id','recv');
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
	<div style="width:870px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">	 
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
				
				//$recv_qnty=return_field_value("sum(grey_receive_qnty)","pro_grey_prod_entry_dtls","mst_id='".$row[csf('id')]."' and status_active=1 and is_deleted=0");
				$recv_qnty=$grey_recv_arr[$row[csf('id')]];
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>);"> 
                    <td width="35"><? echo $i; ?></td>
                    <td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="70"><p>&nbsp;<? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
                    <td width="120"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>               
                    <td width="100"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
                    <td width="110"><p><? echo $knit_comp; ?></p></td>
                    <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td width="90" align="right"><? echo number_format($recv_qnty,2,'.',''); ?></td>
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
		
		echo "load_drop_down( 'requires/grey_fabric_receive_controller', ".$row[csf("knitting_source")]."+'_'+".$row[csf("company_id")].", 'load_drop_down_knitting_com','knitting_com');\n";
		
		$job_no='';
		if($row[csf("receive_basis")]==2)
		{
			$job_no=return_field_value("job_no","wo_booking_mst","id='".$row[csf("booking_id")]."'");
		}
		
		echo "document.getElementById('cbo_knitting_company').value 		= '".$row[csf("knitting_company")]."';\n";
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
	//echo create_list_view("list_view", "Body Part,Fabric Description,GSM,Dia / Width,Grey Rec. Qnty, Reject Feb. Qty, uom, Yarn Lot,No of Roll,Brand,Shift Name,Machine No", "80,140,50,60,80,80,60,60,60,80,80,60","930","200",0, $sql, "put_data_dtls_part", "id", "'populate_grey_details_form_data'", 0, "body_part_id,febric_description_id,0,0,0,0,uom,0,0,brand_id,shift_name,machine_no_id", $arr, "body_part_id,febric_description_id,gsm,width,grey_receive_qnty,reject_fabric_receive,uom,yarn_lot,no_of_roll,brand_id,shift_name,machine_no_id", "requires/grey_fabric_receive_controller",'','0,0,0,0,1,1,0,0,0,0,0,0');
?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="930" class="rpt_table">
        <thead>
            <th width="80">Body Part</th>
            <th width="120">Fabric Description</th>
            <th width="60">GSM</th>
            <th width="60">Dia / Width</th>
            <th width="80">Grey Recv. Qnty</th>
            <th width="80">Reject Feb. Qty</th>
            <th width="50">UOM</th>
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
					
                if($row[csf('febric_description_id')]==0 || $row[csf('febric_description_id')]=="")
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
		if($row[csf('febric_description_id')]==0 || $row[csf('febric_description_id')]=="")
		{
			$comp=return_field_value("item_description","product_details_master","id=".$row[csf('prod_id')]);
		}
		else
		{
			$determination_sql=sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=".$row[csf('febric_description_id')]);
					
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
		echo "document.getElementById('txt_deleted_id').value 				= '';\n";
		
		$save_string='';
		if($roll_maintained==1)
		{
			$data_roll_array=sql_select("select id, roll_used, po_breakdown_id, qnty, roll_no, barcode_no from pro_roll_details where dtls_id='$id' and entry_form=$entry_form and status_active=1 and is_deleted=0");
			foreach($data_roll_array as $row_roll)
			{ 
				if($row_roll[csf('roll_used')]==1) $roll_id=$row_roll[csf('id')]; else $roll_id=0;
				//$roll_id=$row_roll[csf('id')];
				
				if($save_string=="")
				{
					$save_string=$row_roll[csf("po_breakdown_id")]."**".$row_roll[csf("qnty")]."**".$row_roll[csf("roll_no")]."**".$roll_id."**".$row_roll[csf("id")]."**".$row_roll[csf("barcode_no")];
				}
				else
				{
					$save_string.=",".$row_roll[csf("po_breakdown_id")]."**".$row_roll[csf("qnty")]."**".$row_roll[csf("roll_no")]."**".$roll_id."**".$row_roll[csf("id")]."**".$row_roll[csf("barcode_no")];
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
		$sql="select a.recv_number from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and b.po_breakdown_id='$po_id' and b.roll_no='$roll_no' and a.is_deleted=0 and a.status_active=1 and b.entry_form in(2,22) and b.is_deleted=0 and b.status_active=1";
	}
	else
	{
		$sql="select a.recv_number from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and b.po_breakdown_id='$po_id' and b.roll_no='$roll_no' and a.is_deleted=0 and a.status_active=1 and b.entry_form in(2,22) and b.id<>$roll_id and b.is_deleted=0 and b.status_active=1";
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
	//$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$data' and variable_list=3 and is_deleted=0 and status_active=1");
	//if($roll_maintained=="" || $roll_maintained==2) $roll_maintained=0; else $roll_maintained=$roll_maintained;
	
	$variable_data=sql_select("select variable_list, fabric_roll_level, smv_source from variable_settings_production where company_name ='$data' and variable_list in(3,27) and is_deleted=0 and status_active=1");
	foreach($variable_data as $row)
	{
		if($row[csf('variable_list')]==3)
		{
			$roll_maintained=$row[csf('fabric_roll_level')];
		}
		else
		{
			$barcode_generation=$row[csf('smv_source')];
		}
	}
	
	if($roll_maintained==1) $roll_maintained=$roll_maintained; else $roll_maintained=0;
	if($barcode_generation==2) $barcode_generation=$barcode_generation; else $barcode_generation=1;
	
	echo "document.getElementById('roll_maintained').value 					= '".$roll_maintained."';\n";
	echo "document.getElementById('barcode_generation').value 				= '".$barcode_generation."';\n";
	
	echo "reset_form('greyreceive_1','list_fabric_desc_container','','','set_receive_basis();','update_id*txt_recieved_id*cbo_company_id*cbo_receive_basis*txt_receive_date*txt_receive_chal_no*cbo_store_name*cbo_knitting_source*cbo_knitting_company*txt_remarks*roll_maintained*barcode_generation*txt_yarn_issue_challan_no*txt_shift_name*txt_width*txt_gsm*cbo_floor_id*cbo_machine_name*txt_room*txt_rack*txt_reject_fabric_recv_qnty*txt_self*cbo_uom*txt_binbox*txt_yarn_lot*cbo_yarn_count*txt_brand*cbo_color_range');\n";
	
	exit();	
}

if($action=="load_color")
{
	$data=explode("**",$data);
	$booking_id=$data[0];
	$is_sample=$data[1];
	$receive_basis=$data[2];
	
	if($is_sample==0)
	{
		$sql="select c.color_name from wo_booking_mst a, wo_booking_dtls b, lib_color c where a.booking_no=b.booking_no and b.fabric_color_id=c.id and a.id=$booking_id and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.fabric_color_id, c.color_name";
	}
	else
	{
		$sql="select c.color_name from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, lib_color c where a.booking_no=b.booking_no and b.fabric_color=c.id and a.id=$booking_id and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.fabric_color, c.color_name";
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
	$sql="select id, recv_number, item_category, receive_basis, receive_date, challan_no, booking_id, booking_no, store_id, knitting_source, knitting_company, location_id, yarn_issue_challan_no, buyer_id, fabric_nature from inv_receive_master where id='$data[1]' and company_id='$data[0]' ";
	//echo $sql;
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
	
	$program_no="";
	if($dataArray[0][csf('receive_basis')]==9)
	{
		$program_no=return_field_value("booking_id","inv_receive_master","id=".$dataArray[0][csf('booking_id')]." and entry_form=2 and receive_basis=2");
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
        	<td width="125"><strong>Receive ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
            <td width="130"><strong>Receive Basis :</strong></td> <td width="175px"><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
            <td width="125"><strong>Receive Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
        </tr>
        <tr>
            <td><strong>Rec. Chal. No :</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong><? $show_label=""; if($dataArray[0][csf('item_category')]==13) echo $show_label='WO/PI/Prod: '; else echo $show_label='WO/PI: '; ?></strong></td><td width="175px"><? if ($dataArray[0][csf('receive_basis')]==1 ) echo $pi_arr[$dataArray[0][csf('booking_id')]]; else echo $dataArray[0][csf('booking_no')]; ?></td>
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
            <td><strong>Program No:</strong></td><td width="175px"><? echo $program_no; ?></td>
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
            <th width="40" >UOM</th> 
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

if($action=="issue_challan_no_popup")
{
	echo load_html_head_contents("Issue Challan Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);	
	?> 
	<script>
	
		function js_set_value(id)
		{
			$('#issue_challan').val(id);
			parent.emailwindow.hide();
		}
    </script>
    <input type="hidden" name="issue_challan" id="issue_challan" value="" />
    <?
	if($db_type==0)
	{
		$year_cond="year(insert_date)as year";
	}
	else if ($db_type==2)
	{
		$year_cond="TO_CHAR(insert_date,'YYYY') as year";
	}
	$sql="select issue_number_prefix_num, issue_number, $year_cond from inv_issue_master where company_id=$cbo_company_id and entry_form=3 and status_active=1 and is_deleted=0 order by issue_number_prefix_num DESC";
	
	echo create_list_view("tbl_list_search", "System ID, Challan No,Year", "150,80,70","380","350",0, $sql , "js_set_value", "issue_number_prefix_num", "", 1, "0,0,0", $arr , "issue_number,issue_number_prefix_num,year", "",'setFilterGrid("tbl_list_search",-1);','0,0,0','',0) ;
	exit();
}

if($action=="show_roll_listview")
{
	$data=explode("**",str_replace("'","",$data));
	$dtls_id=$data[0];
	$barcode_generation=$data[1];
?>
	<div align="center">
    	<?
			if($barcode_generation==2) 
			{
			?>
				<input type="button" id="btn_send_to_printer" name="btn_send_to_printer" value="Send To Printer" class="formbutton" onClick="fnc_send_printer_text()"/>
			<?
			}
			else
			{
			?>
				<input type="button" id="btn_barcode_generation" name="btn_barcode_generation" value="Barcode Generation" class="formbutton" onClick="fnc_barcode_generation()"/>
			<?	
			}
		?>
    </div>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="100%">
        <thead>
            <th width="90">PO No</th>
            <th width="45">Roll No</th>
            <th width="60">Roll Qnty</th>
            <th width="70">Barcode No.</th>
            <th>Check All <input type="checkbox" name="check_all"  id="check_all" onClick="check_all_report()"></th>
        </thead>
    </table>
    <div style="width:100%; max-height:200px; overflow-y:scroll" id="list_container" align="left"> 
        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="100%" id="tbl_list_search">  
            <? 
            $i=1; 
			$query="select a.id, a.roll_no, a.barcode_no, a.po_breakdown_id, a.qnty, b.po_number from pro_roll_details a, wo_po_break_down b where a.po_breakdown_id=b.id and a.dtls_id=$dtls_id and a.status_active=1 and a.is_deleted=0 order by a.id";
			$result=sql_select($query);  
            foreach($result as $row)
            {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
             ?>
                <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                    <td width="90">
                        <p><? echo $row[csf('po_number')]; ?></p>
                        <input type="hidden" name="txtRollTableId[]" id="txtRollTableId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
                    </td>
                    <td width="43" style="padding-left:2px"><? echo $row[csf('roll_no')]; ?></td>
                    <td align="right" width="58" style="padding-right:2px"><? echo $row[csf('qnty')]; ?></td>
                    <td width="68" style="padding-left:2px"><? echo $row[csf('barcode_no')]; ?></td>
                    <td align="center" valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input id="chkBundle_<? echo $i;  ?>" type="checkbox" name="chkBundle"></td>
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


if($action=="report_barcode_generation")
{
	$data=explode("***",$data);

	$sql="select a.company_id,a.receive_basis,a.booking_id,a.receive_date,a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm,b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.color_id, b.febric_description_id,b.insert_date from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and b.id=$data[1]";
	$result=sql_select($sql);
	$party_name=''; $prod_date=''; $order_id=''; $buyer_name=''; $grey_dia=''; $tube_type=''; $program_no=''; $yarn_lot=''; $yarn_count=''; $brand=''; $gsm=''; $finish_dia='';
	foreach($result as $row)
	{
		if($row[csf('knitting_source')]==1)
		{
			$party_name=return_field_value("company_short_name","lib_company", "id=".$row[csf('knitting_company')]);
		}
		else if($row[csf('knitting_source')]==3)
		{
			$party_name=return_field_value("short_name","lib_supplier", "id=".$row[csf('knitting_company')]);
		}
		
		$prod_date=date("d-m-Y",strtotime($row[csf('insert_date')]));
		$prod_time=date("H:i",strtotime($row[csf('insert_date')]));
		
		$order_id=$row[csf('order_id')];
		$gsm=$row[csf('gsm')];
		$finish_dia=$row[csf('width')];
		$color=$color_arr[$row[csf('color_id')]];
		$stitch_length=$row[csf('stitch_length')];
		$yarn_lot=$row[csf('yarn_lot')];
		$brand=$brand_arr[$row[csf('brand_id')]];
		$yarn_count='';
		$count_id=explode(",",$row[csf('yarn_count')]);
		foreach($count_id as $val)
		{
			if($val>0)
			{
				if($yarn_count=="") $yarn_count=$count_arr[$val]; else $yarn_count.=",".$count_arr[$val];
			}
		}

		$machine_data=sql_select("select machine_no, dia_width, gauge from lib_machine_name where id='".$row[csf('machine_no_id')]."'");
		$machine_name=$machine_data[0][csf('machine_no')];
		$machine_dia_width=$machine_data[0][csf('dia_width')];
		$machine_gauge=$machine_data[0][csf('gauge')];
		
		$buyer_name=return_field_value("short_name","lib_buyer", "id=".$row[csf('buyer_id')]);
		
		$comp='';
		if($row[csf('febric_description_id')]==0 || $row[csf('febric_description_id')]=="")
		{
			$comp=return_field_value("item_description","product_details_master","id=".$row[csf('prod_id')]);
		}
		else
		{
			$determination_sql=sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=".$row[csf('febric_description_id')]);
					
			if($determination_sql[0][csf('construction')]!="")
			{
				$comp=$determination_sql[0][csf('construction')].", ";
			}
			
			foreach( $determination_sql as $d_row )
			{
				$comp.=$composition[$d_row[csf('copmposition_id')]]." ".$d_row[csf('percent')]."% ";
			}
		}
		
		if($row[csf('receive_basis')]==2)
		{
			$program_data=sql_select("select width_dia_type, machine_dia from ppl_planning_info_entry_dtls where id='".$row[csf('booking_id')]."'");
			$program_no=$row[csf('booking_id')];
			$grey_dia=$program_data[0][csf('machine_dia')]; 
			$tube_type=$fabric_typee[$program_data[0][csf('width_dia_type')]]; 
		}
	}
	//echo "select a.job_no,a.job_no_prefix_num,b.id,b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($order_id)";
	$po_array=array();
	$po_sql=sql_select("select a.job_no,a.job_no_prefix_num,b.id,b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($order_id)");
	foreach($po_sql as $row)
	{
		$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
		$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')]; 
		$po_array[$row[csf('id')]]['prefix']=$row[csf('job_no_prefix_num')]; 
	}
	
	$i=1; $barcode_array=array();
	$query="select id, roll_no, po_breakdown_id, barcode_no, qnty from pro_roll_details where id in($data[0])";
	$res=sql_select($query);
	echo '<table width="800" border="0"><tr>';
	foreach($res as $row)
   	{
		$barcode_array[$i]=$row[csf('barcode_no')];
		/*$txt="&nbsp;&nbsp;Barcode No: ".$row[csf('barcode_no')]."<br>";
		$txt .="&nbsp;&nbsp;".$party_name." Job No.".$po_array[$row[csf('po_breakdown_id')]]['prefix']." M/C:".$machine_name."-".$machine_dia_width."X".$machine_gauge."<br>";
		$txt .="&nbsp;&nbsp;D:".$prod_date." T:".$prod_time."<br>";
		$txt .="&nbsp;&nbsp;".$buyer_name.", Order No:". $po_array[$row[csf('po_breakdown_id')]]['no']."<br>";
		$txt .="&nbsp;&nbsp;".$comp."<br>";
		$txt .="&nbsp;&nbsp;G/Dia:".$grey_dia." ".trim($stitch_length)." ".trim($tube_type)." F/Dia:".trim($finish_dia)."<br>";
		$txt .="&nbsp;&nbsp;GSM:".$gsm." ";
		$txt .="&nbsp;&nbsp;".$yarn_count." ".$brand." Lot:".$yarn_lot."<br>";
		$txt .="&nbsp;&nbsp;Prg: ".$program_no."/Roll Wt:".number_format($row[csf('qnty')],2,'.','')." Kg "."<br>";
		$txt .="&nbsp;&nbsp;Roll Sl. ". $row[csf('roll_no')];
		if(trim($color)!="") $txt .=", ".trim($color);*/
		$txt="&nbsp;&nbsp;".$row[csf('barcode_no')]."; ".$party_name." Job No.".$po_array[$row[csf('po_breakdown_id')]]['prefix'].";<br>";
		$txt .="&nbsp;&nbsp;M/C: ".$machine_name."; M/C Dia X Gauge-".$machine_dia_width."X".$machine_gauge.";<br>";
		$txt .="&nbsp;&nbsp;Date: ".$prod_date.";<br>";
		$txt .="&nbsp;&nbsp;Buyer: ".$buyer_name.", Order No: ". $po_array[$row[csf('po_breakdown_id')]]['no'].";<br>";
		$txt .="&nbsp;&nbsp;".$comp."<br>";
		$txt .="&nbsp;&nbsp;G/Dia: ".$grey_dia."; SL: ".trim($stitch_length)."; ".trim($tube_type)."; F/Dia: ".trim($finish_dia).";<br>";
		$txt .="&nbsp;&nbsp;GSM: ".$gsm."; ";
		$txt .=$yarn_count."; Lot: ".$yarn_lot.";<br>";
		$txt .="&nbsp;&nbsp;Prg: ".$program_no."; Roll Wt: ".number_format($row[csf('qnty')],2,'.','')." Kg;<br>";
		$txt .="&nbsp;&nbsp;Custom Roll No: ". $row[csf('roll_no')].";";
		if(trim($color)!="") $txt .=" Color: ".trim($color).";";
		
		echo '<td style="padding-left:7px;padding-top:10px;padding-bottom:5px"><div id="div_'.$i.'"></div>'.$txt.'</td>';//border:dotted;
		if($i%3==0) echo '</tr><tr>';
    	$i++;
    }
	echo '</tr></table>';
	?>
    
    <script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		var barcode_array =<? echo json_encode($barcode_array); ?>;
		function generateBarcode( td_no, valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
		  //alert(value)
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
			
			$("#div_"+td_no).show().barcode(value, btype, settings);
		}

		for (var i in barcode_array) 
		{
			generateBarcode(i,barcode_array[i]);
		}
	</script>
    <?
	exit();
}


if($action=="report_barcode_text_file")
{
	$data=explode("***",$data);

	$sql="select a.company_id,a.receive_basis,a.booking_id,a.receive_date,a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm,b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.color_id, b.febric_description_id,b.insert_date from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and b.id=$data[1]";
	$result=sql_select($sql);
	$party_name=''; $prod_date=''; $order_id=''; $buyer_name=''; $grey_dia=''; $tube_type=''; $program_no=''; $yarn_lot=''; $yarn_count=''; $brand=''; $gsm=''; $finish_dia='';
	foreach($result as $row)
	{
		if($row[csf('knitting_source')]==1)
		{
			$party_name=return_field_value("company_short_name","lib_company", "id=".$row[csf('knitting_company')]);
		}
		else if($row[csf('knitting_source')]==3)
		{
			$party_name=return_field_value("short_name","lib_supplier", "id=".$row[csf('knitting_company')]);
		}
		
		$prod_date=date("d-m-Y",strtotime($row[csf('insert_date')]));
		$prod_time=date("H:i",strtotime($row[csf('insert_date')]));
		
		$order_id=$row[csf('order_id')];
		$gsm=$row[csf('gsm')];
		$finish_dia=$row[csf('width')];
		$color=$color_arr[$row[csf('color_id')]];
		$stitch_length=$row[csf('stitch_length')];
		$yarn_lot=$row[csf('yarn_lot')];
		$brand=$brand_arr[$row[csf('brand_id')]];
		$yarn_count='';
		$count_id=explode(",",$row[csf('yarn_count')]);
		foreach($count_id as $val)
		{
			if($val>0)
			{
				if($yarn_count=="") $yarn_count=$count_arr[$val]; else $yarn_count.=",".$count_arr[$val];
			}
		}

		$machine_data=sql_select("select machine_no, dia_width, gauge from lib_machine_name where id='".$row[csf('machine_no_id')]."'");
		$machine_name=$machine_data[0][csf('machine_no')];
		$machine_dia_width=$machine_data[0][csf('dia_width')];
		$machine_gauge=$machine_data[0][csf('gauge')];
		
		$buyer_name=return_field_value("short_name","lib_buyer", "id=".$row[csf('buyer_id')]);
		
		$comp='';
		if($row[csf('febric_description_id')]==0 || $row[csf('febric_description_id')]=="")
		{
			$comp=return_field_value("item_description","product_details_master","id=".$row[csf('prod_id')]);
		}
		else
		{
			$determination_sql=sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=".$row[csf('febric_description_id')]);
					
			if($determination_sql[0][csf('construction')]!="")
			{
				$comp=$determination_sql[0][csf('construction')].", ";
			}
			
			foreach( $determination_sql as $d_row )
			{
				$comp.=$composition[$d_row[csf('copmposition_id')]]." ".$d_row[csf('percent')]."% ";
			}
		}
		
		if($row[csf('receive_basis')]==2)
		{
			$program_data=sql_select("select width_dia_type, machine_dia from ppl_planning_info_entry_dtls where id='".$row[csf('booking_id')]."'");
			$program_no=$row[csf('booking_id')];
			$grey_dia=$program_data[0][csf('machine_dia')]; 
			$tube_type=$fabric_typee[$program_data[0][csf('width_dia_type')]]; 
		}
	}
	//echo "select a.job_no,a.job_no_prefix_num,b.id,b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($order_id)";
	$po_array=array();
	$po_sql=sql_select("select a.job_no,a.job_no_prefix_num,b.id,b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($order_id)");
	foreach($po_sql as $row)
	{
		$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
		$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')]; 
		$po_array[$row[csf('id')]]['prefix']=$row[csf('job_no_prefix_num')]; 
	}
	
	foreach (glob(""."*.zip") as $filename)
	{			
		@unlink($filename);
	}
	
	$i=1;
	$zip = new ZipArchive();			// Load zip library	
	$filename = str_replace(".sql",".zip",'norsel_bundle.sql');			// Zip name
	if($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE)
	{		// Opening zip file to load files
		$error .=  "* Sorry ZIP creation failed at this time<br/>"; echo $error;
	}
	
	$i=1; $year=date("y");
	$query="select id, roll_no, po_breakdown_id, barcode_no, qnty from pro_roll_details where id in($data[0])";
	$res=sql_select($query);
	foreach($res as $row)
   	{
		$file_name="NORSEL-IMPORT_".$i;
		$myfile = fopen($file_name.".txt", "w") or die("Unable to open file!");
		$txt ="Norsel_imp\r\n1\r\n";
		$txt .=$party_name." Job No.".$po_array[$row[csf('po_breakdown_id')]]['prefix']." M/C:".$machine_name."-".$machine_dia_width."X".$machine_gauge."\r\n";
		//$txt .= $year."-".$row[csf('id')]."\r\n";
		//$txt .="ID: ". $year."-".$row[csf('id')]." D:".$prod_date." T:".$prod_time."\r\n";
		$txt .= $row[csf('barcode_no')]."\r\n";
		$txt .="Barcode No: ". $row[csf('barcode_no')]." D:".$prod_date." T:".$prod_time."\r\n";
		$txt .=$buyer_name.", Order No:". $po_array[$row[csf('po_breakdown_id')]]['no']."\r\n";
		$txt .=$comp."\r\n";
		$txt .="G/Dia:".$grey_dia." ".trim($stitch_length)." ".trim($tube_type)." F/Dia:".trim($finish_dia)."\r\n";
		$txt .="GSM:".$gsm." ";
		$txt .= $yarn_count." ".$brand." Lot:".$yarn_lot."\r\n";
		$txt .="Prg: ".$program_no."/Roll Wt:".number_format($row[csf('qnty')],2,'.','')." Kg "."\r\n";
		$txt .="Roll Sl. ". $row[csf('roll_no')].", ".trim($color)."\r\n";
		
		//Wt:".number_format($row[csf('qnty')],2,'.','')." Kg "."\r\n";
		//$txt .= "Prod Date: ".$prod_date;
		
		fwrite($myfile, $txt);
		fclose($myfile);
		
		$i++;
	}
	
	foreach (glob(""."*.txt") as $filenames)
	{			
	   $zip->addFile($file_folder.$filenames);		
	}
	$zip->close();
	     
	foreach (glob(""."*.txt") as $filename) 
	{			
		@unlink($filename);
	}
	echo "norsel_bundle";
	exit();
}

?>
