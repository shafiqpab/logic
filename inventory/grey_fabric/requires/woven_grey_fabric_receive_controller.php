<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


//========== user credential start ========
$user_id=$_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];
$location_id = $userCredential[0][csf('location_id')];

$company_credential_cond = "";

if ($company_id >0) {
    $company_credential_cond = " and comp.id in($company_id)";
}

if (!empty($store_location_id)) {
    $store_location_credential_cond = " and a.id in($store_location_id)";
}

if ($location_id !='') {
    $location_credential_cond = " and id in($location_id)";
}

if($item_cate_id !='') {
    $item_cate_credential_cond = $item_cate_id ;
}

 $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");


//========== user credential end ==========


//$distribiution_method=array(1=>"Proportionately",2=>"Manually");
$distribiution_method=array(1=>"Distribute Based On Lowest Shipment Date",2=>"Manually");

 //-------------------START ----------------------------------------

if($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[1]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",$data[0] );  
	exit();
	
}

if($action=="load_drop_down_company_in_buyer_td")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_buyer_name", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond  $company_credential_cond group by comp.id, comp.company_name order by comp.company_name","id,company_name", 1, "-- Select Unit --", $selected, "",$data[0] );  
	
	exit();
}

if ($action=="load_drop_down_location")
{
	$data=explode("_", $data);
	$comp=$data[0];
	$knit_source=$data[1];
	if($knit_source==1)
	{
		echo create_drop_down( "cbo_location", 151, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0  $location_credential_cond order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_floor();load_room_rack_self_bin('requires/woven_grey_fabric_receive_controller*13', 'store','store_td', $('#cbo_company_id').val(), this.value);" );//$('#cbo_knitting_company').val()
	}
	else
	{
		echo create_drop_down( "cbo_location", 151, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0  $location_credential_cond order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_floor();load_room_rack_self_bin('requires/woven_grey_fabric_receive_controller*13', 'store','store_td', $('#cbo_company_id').val(), this.value);" );
	}

	exit();
}
if ($action=="load_room_rack_self_bin")
{
	//echo $data;die;
	load_room_rack_self_bin("requires/woven_grey_fabric_receive_controller",$data);
}


if ($action=="load_drop_down_store")
{
	$data=explode("_",$data);
	if($data[1]==2) $category_id=13; else $category_id=14;
	// echo "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[0]' $store_location_credential_cond and b.category_type=$category_id and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name";die;
	echo create_drop_down( "cbo_store_name", 152, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[0]' $store_location_credential_cond and b.category_type=$category_id and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
	exit();
}

if ($action=="load_drop_down_floor")
{
	$data=explode("_",$data);
	$company_id=$data[0];
	$location_id=$data[1];
	if($location_id==0 || $location_id=="") $location_cond=""; else $location_cond=" and b.location_id=$location_id";
	
	echo create_drop_down( "cbo_floor", 132, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1 and b.company_id=$company_id and b.status_active=1 and b.is_deleted=0 and a.production_process=2 $location_cond group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "-- Select Floor --", 0, "load_machine();","" );//load_drop_down( 'requires/woven_grey_fabric_receive_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_machine', 'machine_td' );
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
		echo create_drop_down( "cbo_knitting_company", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond  $company_credential_cond order by comp.company_name","id,company_name",1, "--Select Knit Company--", "$company_id", "","" );//load_location(); // only company wise location, store, floor, room, rack, shelf load confirm by Rasel bhai
	}
	else if($data[0]==3)
	{	
		echo create_drop_down( "cbo_knitting_company", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(9,20) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select Knit Company--", 0, "","");//load_location(); // only company wise location, store, floor, room, rack, shelf load confirm by Rasel bhai 
	}
	else
	{
		echo create_drop_down( "cbo_knitting_company", 152, $blank_array,"",1, "--Select Knit Company--", 0, "" );//load_location(); // only company wise location, store, floor, room, rack, shelf load confirm by Rasel bhai
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

		function js_set_value(id,no,type,buyer_id,data,knit_company)
		{
			$('#hidden_wo_pi_production_id').val(id);
			$('#hidden_wo_pi_production_no').val(no);
			$('#booking_without_order').val(type);
			$('#hidden_buyer_id').val(buyer_id);
			$('#hidden_production_data').val(data);
			$('#hidden_knitting_company').val(knit_company);
			parent.emailwindow.hide();
		}

	</script>
</head>
<body>
	<div align="center" style="width:1030px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:1030px; margin-left:3px">
				<table cellpadding="0" cellspacing="0" border="1" rules="all" width="750" class="rpt_table">
					<thead>
                    	<tr>
                        	<th colspan="5"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",1 ); ?></th>
                        </tr>
                    	<tr>
                            <th>Search By</th>
                            <th width="240">Enter WO/PI/Prod./Sales No</th>
                            <th width="130" colspan="2">Date Range</th>
                            <th>
                                <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                                <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                                <input type="hidden" name="hidden_wo_pi_production_id" id="hidden_wo_pi_production_id" class="text_boxes" value="">  
                                <input type="hidden" name="hidden_wo_pi_production_no" id="hidden_wo_pi_production_no" class="text_boxes" value=""> 
                                <input type="hidden" name="booking_without_order" id="booking_without_order" class="text_boxes" value="">
                                <input type="hidden" name="hidden_buyer_id" id="hidden_buyer_id" class="text_boxes" value="">
                                <input type="hidden" name="hidden_production_data" id="hidden_production_data" class="text_boxes" value="">
                                <input type="hidden" name="hidden_knitting_company" id="hidden_knitting_company" class="text_boxes" value=""> 
                            </th> 
                        </tr>
					</thead>
					<tr class="general">
						<td>	
							<? echo create_drop_down("cbo_receive_basis",152,$receive_basis_arr,"",1,"-- Select --",$recieve_basis,"","1","1,2,4,6,9,11,14"); ?>
						</td>                 
						<td id="search_by_td">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
						</td> 	
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" placeholder="From" style="width:60px"></td>
                    	<td><input name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To" style="width:60px"></td>					
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_receive_basis').value+'_'+document.getElementById('txt_company_id').value+'_'+<? echo $garments_nature; ?>+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_wo_pi_production_search_list_view', 'search_div', 'woven_grey_fabric_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
                    <tr>
                        <td colspan="5" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
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
	$company_arr = return_library_array("select id, company_name from lib_company","id","company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	
	$search_string=trim($data[0]);
	$recieve_basis=$data[1];
	$company_id =$data[2];
	$category_id =$data[3];
	$string_search_type=$data[4];
	$year_selection=$data[5];
	$date_from=$data[6];
	$date_to=$data[7];
	
	$search_field_cond=""; $dateCond=""; $year_cond="";
	
	if($recieve_basis==1)
	{
		if($search_string!="")
		{
			if($string_search_type==1) $search_field_cond="and pi_number='$search_string'";
			if($string_search_type==4 || $string_search_type==0) $search_field_cond="and pi_number like '%$search_string%'";
			if($string_search_type==2) $search_field_cond="and pi_number like '$search_string%'";
			if($string_search_type==3) $search_field_cond="and pi_number like '%$search_string'";
		}
		
		if($db_type==0)
		{ 
			if ($date_from!="" &&  $date_to!="") $dateCond = "and pi_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; else $dateCond ="";
			$year_cond=" and YEAR(insert_date)=$year_selection";
		}
		else
		{
			if ($date_from!="" &&  $date_to!="") $dateCond = "and pi_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'"; else $dateCond ="";
			$year_cond=" and to_char(insert_date,'YYYY')=$year_selection";
		}
		
		if($data[3]==2) $category_id=13; else $category_id=14;
		
		$sql = "select id, pi_number, supplier_id, pi_date, last_shipment_date, pi_basis_id, internal_file_no, currency_id, source from com_pi_master_details where item_category_id='$category_id' and pi_basis_id in (1,2) and status_active=1 and is_deleted=0 and importer_id=$company_id $search_field_cond $dateCond $year_cond order by id desc"; 
		//echo $sql;
		$result = sql_select($sql);
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1010" class="rpt_table">
			<thead>
				<th width="50">SL</th>
				<th width="150">PI No</th>
				<th width="110">PI Date</th>
				<th width="130">PI Basis</th>               
				<th width="160">Supplier</th>
				<th width="100">Last Shipment Date</th>
				<th width="100">Internal File No</th>
				<th width="80">Currency</th>
				<th>Source</th>
			</thead>
		</table>
		<div style="width:1028px; max-height:250px; overflow-y:scroll" id="list_container_batch" align="left">	 
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1010" class="rpt_table" id="tbl_list_search">  
				<?
				$i=1;
				foreach ($result as $row)
				{  
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	 
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('pi_number')]; ?>','0','0','','<? echo $row[csf('supplier_id')]; ?>');"> 
						<td width="50"><? echo $i; ?></td>
						<td width="150"><p><? echo $row[csf('pi_number')]; ?></p></td>
						<td width="110" align="center"><? echo change_date_format($row[csf('pi_date')]); ?></td>  
						<td width="130"><p><? echo $pi_basis[$row[csf('pi_basis_id')]]; ?>&nbsp;</p></td>             
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
		$buyer_short_arr = return_library_array("select id, short_name from lib_buyer","id","short_name");
		
		
		if($search_string!="")
		{
			if($string_search_type==1)
			{
				$search_field_cond="and a.booking_no='$search_string'";
				$search_field_cond_sample="and s.booking_no='$search_string'";
			}
			if($string_search_type==4 || $string_search_type==0)
			{
				$search_field_cond="and a.booking_no like '%$search_string%'";
				$search_field_cond_sample="and s.booking_no like '%$search_string%'";
			}
			if($string_search_type==2) 
			{
				$search_field_cond="and a.booking_no like '$search_string%'";
				$search_field_cond_sample="and s.booking_no like '$search_string%'";
			}
			if($string_search_type==3) 
			{
				$search_field_cond="and a.booking_no like '%$search_string'";
				$search_field_cond_sample="and s.booking_no like '%$search_string'";
			}
		}
		
		if($db_type==0)
		{ 
			if ($date_from!="" &&  $date_to!="") $dateCond = "and a.booking_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; else $dateCond ="";
			if ($date_from!="" &&  $date_to!="") $sdateCond = "and s.booking_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; else $sdateCond ="";
			
			$year_cond=" and YEAR(a.insert_date)=$year_selection";
			$syear_cond=" and YEAR(s.insert_date)=$year_selection";
		}
		else
		{
			if ($date_from!="" &&  $date_to!="") $dateCond = "and a.booking_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'"; else $dateCond ="";
			if ($date_from!="" &&  $date_to!="") $sdateCond = "and s.booking_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'"; else $sdateCond ="";
			$year_cond=" and to_char(a.insert_date,'YYYY')=$year_selection";
			$syear_cond=" and to_char(s.insert_date,'YYYY')=$year_selection";
		}
		if($data[3]==2) $category_id=13; else $category_id=14;
		$sql = "SELECT a.id,a.booking_no_prefix_num,a.supplier_id,a.booking_no,a.booking_date,a.buyer_id,c.po_break_down_id,a.item_category,a.delivery_date,c.job_no AS job_no_mst,2 AS type
		    FROM wo_booking_mst a, wo_booking_dtls c, fabric_sales_order_dtls b
		   WHERE     a.booking_no = c.booking_no AND c.pre_cost_fabric_cost_dtls_id = b.mst_id AND c.po_break_down_id = b.id AND a.company_id = $company_id AND a.item_category = $category_id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.fabric_source IN (1, 2, 3) AND a.entry_form in (549) $search_field_cond $dateCond $year_cond
		GROUP BY a.id, a.supplier_id, a.booking_no_prefix_num, a.booking_no, a.booking_date,a.buyer_id,c.po_break_down_id,a.item_category,a.delivery_date,c.job_no
		ORDER by type, id DESC";
		
		//echo $sql;//die;
		$result = sql_select($sql);

		$po_arr=$po_id_arr = array();
		if(!empty($result))
		{
			foreach($result as $row)
			{
				$po_id_arr[$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
			}
		}
		$po_id_cond = implode(",",$po_id_arr);
		if(!empty($po_id_arr))
		{
			$po_data=sql_select("select b.id, b.po_number, b.pub_shipment_date, b.po_quantity, b.grouping, b.file_no, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($po_id_cond)");	
			foreach($po_data as $row)
			{
				$po_arr[$row[csf('id')]]=$row[csf('po_number')]."**".$row[csf('pub_shipment_date')]."**".$row[csf('po_quantity')]."**".$row[csf('po_qnty_in_pcs')]."**".$row[csf('grouping')]."**".$row[csf('file_no')];
			}
		}
		
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1010" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="105">Booking No</th>
				<th width="75">Booking Date</th>               
				<th width="60">Buyer</th>
				<th width="87">Item Category</th>
				<th width="75">Delivary date</th>
				<th width="80">Job No</th>
				<th width="80">Order Qnty</th>
				<th width="75">Shipment Date</th>
				<th width="100">Internal Ref.</th>
				<th width="100">File No</th>
				<th width="140">Order No</th>
			</thead>
		</table>
		<div style="width:1028px; max-height:250px; overflow-y:scroll" id="list_container_batch" align="left">	 
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1010" class="rpt_table" id="tbl_list_search">  
				<?
				$i=1;
				foreach ($result as $row)
				{  
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	 
					
					$po_qnty_in_pcs=''; $po_no=''; $min_shipment_date=''; $internal_ref=''; $file_no='';
					
					if($row[csf('po_break_down_id')]!="" && $row[csf('type')]==0)
					{
						$po_id=explode(",",$row[csf('po_break_down_id')]);
						foreach ($po_id as $id)
						{
							$po_data=explode("**",$po_arr[$id]);
							$po_number=$po_data[0];
							$pub_shipment_date=$po_data[1];
							$po_qnty=$po_data[2];
							$poQntyPcs=$po_data[3];
							$internalRef=$po_data[4];
							$fileNo=$po_data[5];
							
							if($po_no=="") $po_no=$po_number; else $po_no.=",".$po_number;
							if($internal_ref=='') $internal_ref=$internalRef; else $internal_ref.=",".$internalRef;
							if($file_no=='') $file_no=$fileNo; else $file_no.=",".$fileNo;
							
							if($min_shipment_date=='')
							{
								$min_shipment_date=$pub_shipment_date;
							}
							else
							{
								if($pub_shipment_date<$min_shipment_date) $min_shipment_date=$pub_shipment_date; else $min_shipment_date=$min_shipment_date;
							}
							
							$po_qnty_in_pcs+=$poQntyPcs;
							
						}
					}
					
					$internal_ref=implode(",",array_unique(explode(",",$internal_ref)));
					$file_no=implode(",",array_unique(explode(",",$file_no)));
					$po_no=implode(",",array_unique(explode(",",$po_no)));
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('booking_no')]; ?>','<? echo $row[csf('type')]; ?>','0','','<? echo $row[csf('supplier_id')]; ?>');"> 
						<td width="30"><? echo $i; ?></td>
						<td width="105"><p><? echo $row[csf('booking_no')]; ?></p></td>
						<td width="75" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>               
						<td width="60"><p><? echo $buyer_short_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
						<td width="87"><p><? echo $item_category[$row[csf('item_category')]]; ?></p></td>
						<td width="75" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</td>
						<td width="80"><p><? echo $row[csf('job_no_mst')]; ?>&nbsp;</p></td>
						<td width="80" align="right"><? echo $po_qnty_in_pcs; ?>&nbsp;</td>
						<td width="75" align="center"><? echo change_date_format($min_shipment_date); ?>&nbsp;</td>
						<td width="100"><p><? echo $internal_ref; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $file_no; ?>&nbsp;</p></td>
						<td><p style="word-break: break-all; word-wrap: break-word; max-width: 140px"><? echo $po_no; ?>&nbsp;</p></td>
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
	$receive_basis=$data[2];
	$roll_maintained=$data[3];
	
	//echo $roll_maintained;die;
	//select s.id, s.prefix_num as booking_no_prefix_num, s.booking_no, s.booking_date, s.buyer_id, s.supplier_id, null as po_break_down_id, s.item_category, null as delivery_date, null as job_no_mst, 1 as type from wo_non_ord_knitdye_booking_mst s where s.company_id=$company_id and s.item_category=1 and s.status_active=1 and s.is_deleted=0  $search_field_cond_sample
	if($receive_basis==2)
	{
		if($is_sample==0 || $is_sample==2)
		{
			$sql="select a.id as booking_id, a.booking_no, a.buyer_id, b.job_no from wo_booking_mst a,wo_booking_dtls b where a.id='$booking_id' and a.booking_no=b.booking_no";
		}
		else
		{
			$sql="select id as booking_id, booking_no, buyer_id, '' as job_no from wo_non_ord_samp_booking_mst where id='$booking_id'";
		}
	}
	else
	{
		if($is_sample==0)
		{
			//$sql="select id as booking_id, booking_no, buyer_id, job_no from wo_booking_mst where id='$booking_id'";
			$sql="select a.id as booking_id, a.booking_no, a.buyer_id, b.job_no from wo_booking_mst a,wo_booking_dtls b where a.id='$booking_id' and a.booking_no=b.booking_no";
		}
		else
		{
			$sql="select fab_booking_id as booking_id, booking_no, buyer_id, '' as job_no from wo_non_ord_knitdye_booking_mst  where id='$booking_id'";
		}
	}
	
	
	$data_array=sql_select($sql);
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_booking_no').value 				= '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('txt_booking_no_id').value 			= '".$row[csf("booking_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value 				= '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_job_no').value 					= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('booking_without_order').value 		= '".$is_sample."';\n";
		
		if($receive_basis==2)
		{
			if($is_sample==1 && $receive_basis==2)
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
		}
		else
		{
			if($roll_maintained==1)
			{
				echo "$('#txt_receive_qnty').attr('readonly','readonly');\n";
				echo "$('#txt_receive_qnty').attr('onClick','openmypage_po();');\n";	
				echo "$('#txt_receive_qnty').attr('placeholder','Single Click');\n";		
			}
			else
			{
				echo "$('#txt_receive_qnty').removeAttr('readonly','readonly');\n";
				echo "$('#txt_receive_qnty').removeAttr('onClick','onClick');\n";	
				echo "$('#txt_receive_qnty').removeAttr('placeholder','placeholder');\n";	
			}
		}
		
		exit();
	}
}

if($action=='populate_data_from_sales_order')
{
	$data=explode("**",$data);
	$booking_id=$data[0];
	$is_sample=$data[1];
	$receive_basis=$data[2];
	$roll_maintained=$data[3];
	if($is_sample == "") $is_sample=0;
	$sql="select id, job_no, buyer_id, within_group, po_job_no from fabric_sales_order_mst where id='$booking_id'";

	$data_array=sql_select($sql);
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_booking_no').value 				= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_booking_no_id').value 			= '".$row[csf("id")]."';\n";

		if($row[csf("within_group")] == 1)
		{
			echo "load_drop_down('requires/woven_grey_fabric_receive_controller','". $row[csf("buyer_id")] ."', 'load_drop_down_company_in_buyer_td','buyer_td_id');\n";
		}
		
		echo "document.getElementById('cbo_buyer_name').value 				= '".$row[csf("buyer_id")]."';\n";
		//echo "document.getElementById('txt_job_no').value 					= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('booking_without_order').value 		= '".$is_sample."';\n";
		
		echo "$('#txt_receive_qnty').attr('readonly','readonly');\n";
		echo "$('#txt_receive_qnty').attr('onClick','openmypage_po();');\n";	
		echo "$('#txt_receive_qnty').attr('placeholder','Single Click');\n";

		exit();
	}
}
if($action=='populate_data_from_production')
{
	$data=explode("**",$data);
	$id=$data[0];
	$roll_maintained=$data[1];
	$knitting_source=$data[2];
	$process_costing_maintain=$data[3];
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$brand_arr=return_library_array( "select id, brand_name from lib_brand where status_active=1 and is_deleted=0",'id','brand_name');
	
	$data_array=sql_select("select body_part_id, prod_id, febric_description_id, no_of_roll, gsm, width, grey_receive_qnty, reject_fabric_receive, uom, yarn_lot, yarn_count, brand_id, floor_id, shift_name, machine_no_id, order_id, room, rack, self, bin_box, color_id, color_range_id, stitch_length,machine_dia,machine_gg, yarn_rate, kniting_charge from pro_grey_prod_entry_dtls where id='$id'");
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
		
		$color='';
		$color_id=explode(",",$row[csf('color_id')]);
		foreach($color_id as $val)
		{
			if($val>0) $color.=$color_arr[$val].",";
		}
		$color=chop($color,',');
		
		echo "document.getElementById('cbo_body_part').value 				= '".$row[csf("body_part_id")]."';\n";
		echo "document.getElementById('txt_fabric_description').value 		= '".trim($comp)."';\n";
		echo "document.getElementById('fabric_desc_id').value 				= '".$row[csf("febric_description_id")]."';\n";
		echo "document.getElementById('txt_gsm').value 						= '".$row[csf("gsm")]."';\n";
		echo "document.getElementById('txt_width').value 					= '".$row[csf("width")]."';\n";
		echo "document.getElementById('txt_roll_no').value 					= '".$row[csf("no_of_roll")]."';\n";
		echo "document.getElementById('txt_receive_qnty').value 			= '".$row[csf("grey_receive_qnty")]."';\n";
		echo "document.getElementById('all_po_id').value 					= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('product_id').value 					= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('txt_color').value 					= '".$color."';\n"; 
		echo "document.getElementById('color_id').value 					= '".$row[csf("color_id")]."';\n";    
		echo "document.getElementById('txt_stitch_length').value 			= '".$row[csf("stitch_length")]."';\n";
		echo "document.getElementById('txt_machine_dia').value 				= '".$row[csf("machine_dia")]."';\n";
		echo "document.getElementById('txt_machine_gg').value 				= '".$row[csf("machine_gg")]."';\n";
		echo "document.getElementById('txt_reject_fabric_recv_qnty').value 	= '".$row[csf("reject_fabric_receive")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("uom")]."';\n";
		echo "document.getElementById('txt_yarn_lot').value 				= '".$row[csf("yarn_lot")]."';\n";
		echo "document.getElementById('cbo_color_range').value 				= '".$row[csf("color_range_id")]."';\n";
		echo "document.getElementById('cbo_yarn_count').value 				= '".$row[csf("yarn_count")]."';\n";
		echo "set_multiselect('cbo_yarn_count','0','1','".$row[csf('yarn_count')]."','0');\n";
		echo "document.getElementById('txt_brand').value 					= '".$brand_arr[$row[csf("brand_id")]]."';\n";
		echo "document.getElementById('txt_shift_name').value 				= '".$row[csf("shift_name")]."';\n";
		echo "document.getElementById('cbo_floor').value 				= '".$row[csf("floor_id")]."';\n";
		
		echo "load_machine();\n";

		echo "document.getElementById('cbo_machine_name').value 			= '".$row[csf("machine_no_id")]."';\n";
		echo "document.getElementById('cbo_room').value 					= '".$row[csf("room")]."';\n";
		echo "document.getElementById('txt_rack').value 					= '".$row[csf("rack")]."';\n";
		echo "document.getElementById('txt_shelf').value 					= '".$row[csf("self")]."';\n";
		echo "document.getElementById('cbo_bin').value 					= '".$row[csf("bin_box")]."';\n";
		echo "document.getElementById('grey_prod_dtls_id').value 			= '".$id."';\n";
		
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
		
		if($process_costing_maintain==1 && $knitting_source==1)
		{
			$fabric_rate=$row[csf("kniting_charge")]+$row[csf("yarn_rate")];
			$receive_qty=$row[csf("grey_receive_qnty")];
			$fabric_amount=$fabric_rate*$receive_qty;
			echo "document.getElementById('txt_rate').value 					= '".$fabric_rate."';\n";
			echo "document.getElementById('txt_amount').value 					= '".$fabric_amount."';\n";
			echo "$('#txt_rate').attr('title','Yarn Rate/Kg : ".$row[csf("yarn_rate")]."; knitting Charge/Kg :".$row[csf("kniting_charge")]."');\n";
		}
		exit();
	}
}

if($action=='quantity_check_for_service_booking')
{
	//$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
	$data=explode("**",$data);
	$booking_pi_production_no=$data[0];
	$is_sample=$data[1];
	$receive_basis=$data[2];
	$company_id=$data[3];
	$body_part_id=$data[4];
	$determination_id=$data[5];
	$recive_no=$data[6];
	$txt_receive_qnty=$data[7];
	
	
	
		$field="a.wo_qnty";
		//$sql="select b.lib_yarn_count_deter_id as determination_id, b.body_part_id, b.gsm_weight, a.dia_width, sum($field) as qnty, avg(a.rate) as rate from wo_booking_dtls a, wo_pre_cost_fab_conv_cost_dtls c, wo_pre_cost_fabric_cost_dtls b where a.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=b.id and a.booking_no='$booking_pi_production_no' and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 group by b.lib_yarn_count_deter_id, b.body_part_id, b.gsm_weight, a.dia_width"; // old
		if($is_sample==0)
		{
			$sql_chk_prog_no=sql_select("select program_no from wo_booking_dtls where booking_no='$booking_pi_production_no' group by program_no");
			$row_prog="";
			foreach($sql_chk_prog_no as $row_prog)
			{
				$row_prog= $row_prog[csf('program_no')];
			}

			$sql_chk_prog_no_2=sql_select("select id from ppl_planning_info_entry_dtls where id='$row_prog' group by id");
			$row_prog_2="";
			foreach($sql_chk_prog_no_2 as $row_prog_2)
			{
				$row_prog_2= $row_prog_2[csf('id')];
			}
			
			if($row_prog_2 != "")
			{
				$sql="select b.lib_yarn_count_deter_id as determination_id, b.body_part_id, sum($field) as qnty from wo_booking_dtls a, wo_pre_cost_fab_conv_cost_dtls c, wo_pre_cost_fabric_cost_dtls b,ppl_planning_info_entry_dtls d where a.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=b.id and a.booking_no='$booking_pi_production_no' and b.body_part_id='$body_part_id' and b.lib_yarn_count_deter_id='$determination_id' and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and a.program_no=d.id 
				group by b.lib_yarn_count_deter_id, b.body_part_id"; //new
				$exchange_rate=return_field_value("exchange_rate","wo_booking_mst","booking_no='$booking_pi_production_no'");
			}
			else
			{
				$sql="select b.lib_yarn_count_deter_id as determination_id, b.body_part_id, sum($field) as qnty from wo_booking_dtls a, wo_pre_cost_fab_conv_cost_dtls c, wo_pre_cost_fabric_cost_dtls b where a.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=b.id and a.booking_no='$booking_pi_production_no' and b.body_part_id='$body_part_id' and b.lib_yarn_count_deter_id='$determination_id' and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 group by b.lib_yarn_count_deter_id, b.body_part_id"; //old
				$exchange_rate=return_field_value("exchange_rate","wo_booking_mst","booking_no='$booking_pi_production_no'");
				
			}
			//$color_library=return_library_array( "select id,program_no from wo_booking_dtls group by id,program_no", "id", "program_no"  );
			//$color_library=return_library_array( "select id,color_name from ppl_planning_info_entry_dtls", "id", "color_name"  );
			//$sql="select b.lib_yarn_count_deter_id as determination_id, b.body_part_id, b.gsm_weight, a.dia_width, sum($field) as qnty, avg(a.rate) as rate,a.lot_no,a.yarn_count,a.brand,a.fabric_color_id,a.slength,d.stitch_length,d.color_id,d.color_range from wo_booking_dtls a, wo_pre_cost_fab_conv_cost_dtls c, wo_pre_cost_fabric_cost_dtls b,ppl_planning_info_entry_dtls d where a.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=b.id and a.booking_no='$booking_pi_production_no' and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and a.program_no=d.id group by b.lib_yarn_count_deter_id, b.body_part_id, b.gsm_weight, a.dia_width,a.lot_no,a.yarn_count,a.brand,a.fabric_color_id,a.slength,d.stitch_length,d.color_id,d.color_range"; //new
			//$exchange_rate=return_field_value("exchange_rate","wo_booking_mst","booking_no='$booking_pi_production_no'");
		}
		else
		{
			$sql="select c.lib_yarn_count_deter_id as determination_id, c.body_part as body_part_id, sum(b.wo_qty) as qnty
			from wo_non_ord_knitdye_booking_mst a, wo_non_ord_knitdye_booking_dtl b, wo_non_ord_samp_booking_dtls c  
			where a.id=b.mst_id and b.fab_des_id=c.id and b.fabric_source=1 and b.process_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no='$booking_pi_production_no' and c.body_part='$body_part_id' and c.lib_yarn_count_deter_id='$determination_id' group by c.lib_yarn_count_deter_id , c.body_part ";
			
		}
	
	
	//echo "0**".$sql;die;
	$data_array=sql_select($sql);
	$over_receive = 0;
	$recieved_qty_with_percentage = 0;
	$variable_set_invent= sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$company_id and variable_list=23 and category = 13 order by id");
	$over_receive = !empty($variable_set_invent)?$variable_set_invent[0][csf('over_rcv_percent')]:0;
	$receive_arr = array();
	if(empty($recive_no))
	{
		$sql_recv="SELECT a.booking_no, sum(b.grey_receive_qnty) grey_receive_qnty, b.body_part_id, b.febric_description_id
		FROM inv_receive_master a
		INNER JOIN pro_grey_prod_entry_dtls b ON a.id = b.mst_id and b.status_active=1 and b.is_deleted=0
		WHERE a.booking_no = '$booking_pi_production_no'
		and a.status_active=1 and a.is_deleted=0 and a.item_category=13 and a.entry_form=550 
		GROUP BY a.booking_no, b.body_part_id, b.febric_description_id";
	}
	else{
		$sql_recv="SELECT a.booking_no, sum(b.grey_receive_qnty) grey_receive_qnty, b.body_part_id, b.febric_description_id
		FROM inv_receive_master a
		INNER JOIN pro_grey_prod_entry_dtls b ON a.id = b.mst_id and b.status_active=1 and b.is_deleted=0
		WHERE a.booking_no = '$booking_pi_production_no'
		and a.recv_number not in ('$recive_no')
		and a.status_active=1 and a.is_deleted=0 and a.item_category=13 and a.entry_form=550  
		GROUP BY a.booking_no, b.body_part_id, b.febric_description_id";
	}
	
	$received_qty = sql_select($sql_recv);
	foreach ($received_qty as $row) {
		$receive_arr[$booking_pi_production_no][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['grey_receive_qnty'] = $row[csf('grey_receive_qnty')];
	}
	$allowedQtyTotal_with_percent=0;
	$grey_receive_qnty=0;
	foreach($data_array as $row)
	{  
		
		$grey_receive_qnty = $receive_arr[$booking_pi_production_no][$row[csf('body_part_id')]][$row[csf('determination_id')]]['grey_receive_qnty'];
				$rec_qty = (!empty($receive_arr)?$grey_receive_qnty:0);
		$qnty=$row[csf('qnty')];
		
		$allowedQtyTotal_with_percent = (($over_receive / 100) * $qnty)+$qnty;
		break;
		
	}
	if(empty($txt_receive_qnty))
	{
		$txt_receive_qnty=0;
	}
	if($grey_receive_qnty+$txt_receive_qnty<=$allowedQtyTotal_with_percent)
	{
		echo "1**".$grey_receive_qnty."**".$allowedQtyTotal_with_percent;
	}
	else{
		echo "0**".$grey_receive_qnty."**".$allowedQtyTotal_with_percent;
	}
	
				
	exit();
}

if($action=='quantity_check_for_sales_order')
{
	$data=explode("**",$data);
	$sales_no=$data[0];
	$receive_basis=$data[1];
	$company_id=$data[2];
	$body_part_id=$data[3];
	$determination_id=$data[4];
	$txt_gsm=$data[5];
	$txt_width=$data[6];
	$update_dtls_id=$data[7];
	$txt_receive_qnty=$data[8];
	
	//document.getElementById('txt_booking_no').value+'**14**'+document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_body_part').value+'**'+document.getElementById('fabric_desc_id').value+'**'+document.getElementById('txt_gsm').value+'**'+document.getElementById('txt_width').value+'**'+document.getElementById('txt_recieved_id').value+'**'+txt_receive_qnty;
	
	if($txt_width=="")
	{
		if($db_type==0){
			$txt_width_cond=" and b.dia=''";
		}else{
			$txt_width_cond=" and b.dia is null";
		}
	}
	else{
		$txt_width_cond="and b.dia='$txt_width'";
	}

	$sql="select a.id, a.job_no, sum(b.grey_qty) as qnty from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.job_no = '$sales_no' and b.body_part_id='$body_part_id' and b.determination_id='$determination_id' and b.gsm_weight='$txt_gsm' $txt_width_cond group by a.id, a.job_no";
	
	//echo $sql;
	$data_array=sql_select($sql);
	$sales_qnty=0;
	foreach($data_array as $row)
	{
		$sales_qnty +=$row[csf('qnty')];
	}

	$over_receive = 0;
	$recieved_qty_with_percentage = 0;
	$variable_set_invent= sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$company_id and variable_list=23 and category = 13 and status_active=1 and is_deleted=0 order by id");
	$over_receive = !empty($variable_set_invent)?$variable_set_invent[0][csf('over_rcv_percent')]:0;
	
	if($update_dtls_id !="")
	{
		$up_cond = " and b.id<> $update_dtls_id";
	}
	$sql_recv="SELECT a.booking_no, sum(b.grey_receive_qnty) grey_receive_qnty, b.body_part_id, b.febric_description_id, b.gsm, b.width FROM inv_receive_master a , pro_grey_prod_entry_dtls b WHERE a.booking_no = '$sales_no' and a.receive_basis=14 and  a.id = b.mst_id and b.status_active=1 and b.body_part_id ='$body_part_id' and b.febric_description_id= '$determination_id' and b.gsm='$txt_gsm' and b.width='$txt_width' $up_cond GROUP BY a.booking_no, b.body_part_id, b.febric_description_id, b.gsm, b.width";
	$received_qty = sql_select($sql_recv);
	$receive_arr = array();
	foreach ($received_qty as $row) {
		$receive_arr[$sales_no]['grey_receive_qnty'] += $row[csf('grey_receive_qnty')];
	}

	$grey_receive_qnty=$receive_arr[$sales_no]['grey_receive_qnty'];
	$allowedQtyTotal_with_percent = (($over_receive / 100) * $sales_qnty)+$sales_qnty;

	if($grey_receive_qnty+$txt_receive_qnty<=$allowedQtyTotal_with_percent)
	{
		echo "1**".$grey_receive_qnty."**".$allowedQtyTotal_with_percent;
	}
	else{
		echo "0**".$grey_receive_qnty."**".$allowedQtyTotal_with_percent;
	}	
	exit();
}

if($action=='show_fabric_desc_listview')
{
	$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
	$data=explode("**",$data);
	$booking_pi_production_no=$data[0];
	$is_sample=$data[1];
	$receive_basis=$data[2];
	$company_id=$data[3];
	
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
		$sql="SELECT a.currency_id, b.determination_id, b.body_part_id as body_part_id, b.gsm as gsm_weight, b.dia_width, sum(b.quantity) as qnty, avg(b.rate) as rate,b.color_range,a.pi_number as booking_no,b.color_id from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and b.pi_id='$booking_pi_production_no' and b.status_active=1 and b.is_deleted=0 group by a.currency_id, b.determination_id, b.gsm, b.dia_width,b.body_part_id,b.color_range,a.pi_number,b.color_id";
	}
	else if($receive_basis==2)
	{
		if($is_sample==0)
		{
			//if($receive_basis==2) $field="a.grey_fab_qnty"; else $field="a.wo_qnty";
			$field="a.grey_fab_qnty";
			$sql="SELECT b.lib_yarn_count_deter_id as determination_id, b.body_part_id, b.gsm_weight, a.dia_width, sum($field) as qnty, avg(a.rate) as rate,a.booking_no,b.color_range_id as color_range from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b where a.pre_cost_fabric_cost_dtls_id=b.id and a.booking_no='$booking_pi_production_no' and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 group by b.lib_yarn_count_deter_id, b.body_part_id, b.gsm_weight, a.dia_width,a.booking_no,b.color_range_id";

			$exchange_rate=return_field_value("exchange_rate","wo_booking_mst","booking_no='$booking_pi_production_no'");
		}
		else if($is_sample==2)
		{
			$field="a.grey_fab_qnty";
			$sql="SELECT b.determination_id AS determination_id,b.body_part_id,b.gsm_weight,a.dia_width,SUM ($field) AS qnty,AVG (a.rate) AS rate,b.color_range_id as color_range,a.booking_no,b.color_id
    			FROM wo_booking_dtls a, fabric_sales_order_dtls b,wo_booking_mst c
   				WHERE     a.pre_cost_fabric_cost_dtls_id = b.mst_id
   				and a.booking_no=c.booking_no and c.entry_form=549
         		and a.po_break_down_id = b.id and a.booking_no='$booking_pi_production_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
         		group by b.determination_id, b.body_part_id, b.gsm_weight, a.dia_width,b.color_range_id,a.booking_no,b.color_id";

			$exchange_rate=return_field_value("exchange_rate","wo_booking_mst","booking_no='$booking_pi_production_no'");
		}
		else
		{
			$sql="SELECT lib_yarn_count_deter_id as determination_id, body_part as body_part_id, gsm_weight, dia_width, construction, composition, sum(grey_fabric) as qnty, avg(rate) as rate,booking_no from wo_non_ord_samp_booking_dtls where booking_no='$booking_pi_production_no' and status_active=1 and is_deleted=0 group by lib_yarn_count_deter_id, body_part, gsm_weight, dia_width, construction, composition,booking_no";
			//echo "select exchange_rate from wo_non_ord_samp_booking_mst where booking_no='$booking_pi_production_no'";
			$exchange_rate=return_field_value("exchange_rate","wo_non_ord_samp_booking_mst","booking_no='$booking_pi_production_no'");
		}
	}
	else
	{
		$cons_comps_arr = return_library_array("select id,item_description from product_details_master where item_category_id=13","id","item_description");
		$sql="SELECT a.booking_no as program_no,b.id, b.prod_id, b.febric_description_id as determination_id, b.body_part_id, b.gsm as gsm_weight, b.width as dia_width, b.grey_receive_qnty as qnty, b.rate from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and b.mst_id='$booking_pi_production_no' and b.status_active=1 and b.is_deleted=0";
	}
	//echo $receive_basis. " , " . $is_sample;
	//echo $sql;
	$data_array=sql_select($sql);
	$over_receive = 0;
	$recieved_qty_with_percentage = 0;
	$variable_set_invent= sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$company_id and variable_list=23 and category = 14 order by id");
	$over_receive = !empty($variable_set_invent)?$variable_set_invent[0][csf('over_rcv_percent')]:0;
	$receive_arr = array();
	$booking_cond = "a.booking_no = '".$booking_pi_production_no."'";
	if($receive_basis==1) $booking_cond = "a.booking_id = '".$booking_pi_production_no."'";
	$received_qty = sql_select("SELECT a.booking_no, sum(b.grey_receive_qnty) grey_receive_qnty, b.body_part_id, b.febric_description_id FROM inv_receive_master a
		INNER JOIN pro_grey_prod_entry_dtls b ON a.id = b.mst_id
		WHERE $booking_cond and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and item_category=14 and entry_form=550 GROUP BY a.booking_no, b.body_part_id, b.febric_description_id");
	
	foreach ($received_qty as $row) 
	{
		$receive_arr[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['grey_receive_qnty'] = $row[csf('grey_receive_qnty')];
	}
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="550">
		<thead>
			<th width="20">SL</th>
			<th>Fabric Description</th>
			<th width="60">Qnty</th>
			<th width="60">Recv</th>
			<th width="60">Blance</th>
			<th width="50" style="display:none">Avg. Rate</th>
		</thead>
		<tbody>
			<? 
			$i=1;
			foreach($data_array as $row)
			{  
				$deter = $row[csf('determination_id')];
				$body_part_id = $row[csf('body_part_id')];
				$gsm = $row[csf('gsm_weight')];

				if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";

				$fabric_desc=''; $rate=0;
				
				$fabric_desc=$body_part[$row[csf('body_part_id')]].", ";
				

				if($receive_basis==2 && $is_sample==1 && ($row[csf('determination_id')]==0 || $row[csf('determination_id')]==""))
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
				if($receive_basis==1)
				{
					$rate=$row[csf('rate')];
				}
				else
				{
					$rate=$row[csf('rate')]*$exchange_rate;
				}

				if($receive_basis==2 && $is_sample==1 && ($row[csf('determination_id')]==0 || $row[csf('determination_id')]==""))
				{
					$cons_comp=$row[csf('construction')].", ".$row[csf('composition')];
					$data=$row[csf('body_part_id')]."**".$cons_comp."**".$row[csf('gsm_weight')]."**".$row[csf('dia_width')]."**".$row[csf('determination_id')]."**".$rate."**".$row[csf('currency_id')]."**".$row[csf('color_range')]."**".$row[csf('color_id')];
				}
				else if($receive_basis==1)
				{
					$data=$row[csf('body_part_id')]."**".$composition_arr[$row[csf('determination_id')]]."**".$row[csf('gsm_weight')]."**".$row[csf('dia_width')]."**".$row[csf('determination_id')]."**".$rate."**".$row[csf('currency_id')]."**".$row[csf('qnty')]."**".$rec_qty."**".$over_receive."**".$row[csf('color_range')]."**".$row[csf('color_id')]; // old $row_prog_2
				}
				else
				{
					$grey_receive_qnty = $receive_arr[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('determination_id')]]['grey_receive_qnty'];
					$rec_qty = (!empty($receive_arr)?$grey_receive_qnty:0);						
					$data=$row[csf('body_part_id')]."**".$composition_arr[$row[csf('determination_id')]]."**".$row[csf('gsm_weight')]."**".$row[csf('dia_width')]."**".$row[csf('determination_id')]."**".$rate."**".$row[csf('currency_id')]."**".$row[csf('qnty')]."**".$rec_qty."**".$over_receive."**".$row[csf('color_range')]."**".$row[csf('color_id')]; // old $row_prog_2
				}
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $data; ?>")' style="cursor:pointer" >
					<td><? echo $i; ?></td>
					
					<td><? echo $fabric_desc; ?></td>
					<td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
					<td align="right"><? echo number_format($rec_qty,2); ?></td>
					<td align="right"><? echo number_format($row[csf('qnty')]-$rec_qty,2); ?></td>
					<td align="right" style="display:none"><? echo number_format($rate,2); ?></td>
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

if($action=='show_fabric_desc_listview_sales_order')
{
	$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
	$data=explode("**",$data);
	$sales_no=$data[0];
	$is_sample=$data[1];
	$receive_basis=$data[2];
	$company_id=$data[3];
	
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

	$sql="SELECT b.body_part_id, b.determination_id, b.gsm_weight, b.dia, sum(b.grey_qty) as qnty from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.job_no = '$sales_no' group by b.body_part_id, b.determination_id, b.gsm_weight, b.dia";

	//$exchange_rate=return_field_value("exchange_rate","wo_booking_mst","booking_no='$booking_pi_production_no'");
	
	//echo $sql;
	$data_array=sql_select($sql);
	$over_receive = 0; $recieved_qty_with_percentage = 0;
	$variable_set_invent= sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$company_id and variable_list=23 and category = 13 order by id");
	$over_receive = !empty($variable_set_invent)?$variable_set_invent[0][csf('over_rcv_percent')]:0;
	$receive_arr = array();
	$received_qty = sql_select("SELECT a.booking_no, sum(b.grey_receive_qnty) grey_receive_qnty, b.body_part_id, b.febric_description_id, b.gsm, b.width
		FROM inv_receive_master a
		INNER JOIN pro_grey_prod_entry_dtls b ON a.id = b.mst_id and b.status_active=1
		WHERE a.booking_no = '$sales_no' and a.receive_basis=14
		GROUP BY a.booking_no, b.body_part_id, b.febric_description_id, b.gsm, b.width");

	foreach ($received_qty as $row) 
	{
		$receive_arr[$sales_no][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('gsm')]][$row[csf('width')]]['qnty']=$row[csf('grey_receive_qnty')];
	}
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="350">
		<thead>
			<th width="20">SL</th>
			<th>Fabric Description</th>
			<th width="60">Qnty</th>
			<th width="50" style="display:none">Avg. Rate</th>
		</thead>
		<tbody>
			<? 
			$i=1;
			foreach($data_array as $row)
			{  
				$deter = $row[csf('determination_id')];
				$body_part_id = $row[csf('body_part_id')];
				$gsm = $row[csf('gsm_weight')];

				if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";

				$fabric_desc=''; $rate=0;

				$fabric_desc=$body_part[$row[csf('body_part_id')]].", ";
				
				$fabric_desc.=$composition_arr[$row[csf('determination_id')]].", ".$row[csf('gsm_weight')];

				if($row[csf('dia')]!="")
				{
					$fabric_desc.=", ".$row[csf('dia')];
				}
				
				//$rate=$row[csf('rate')]*$exchange_rate;
				
				$grey_receive_qnty = $receive_arr[$sales_no][$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('gsm_weight')]][$row[csf('dia')]]['qnty'];
				$rec_qty = $grey_receive_qnty*1;
				
				$data=$row[csf('body_part_id')]."**".$composition_arr[$row[csf('determination_id')]]."**".$row[csf('gsm_weight')]."**".$row[csf('dia')]."**".$row[csf('determination_id')]."**".$row[csf('qnty')]."**".$rec_qty."**".$over_receive; 
				
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
	//echo $txt_reject_fabric_recv_qnty;
	//$txt_receive_qnty;
	$data=explode("_",$data);
	$po_id=$data[0]; $type=$data[1];
	//echo "book_ord=".$booking_without_order;die;
	//echo "type=".$type;//die;
	if($type==1) 
	{
		$dtls_id=$data[2]; 
		$roll_maintained=$data[3]; 
		$save_data=$data[4]; 
		$prev_distribution_method=$data[5]; 
		$receive_basis=$data[6]; 
		$txt_deleted_id=$data[7]; 
		$booking_dtls_id_pi=$data[8]; 
	}
	
	$recv_qnty_array=array();
	if($receive_basis==2)
	{	
		if($dtls_id=="") $dtls_id_cond=""; else $dtls_id_cond="and a.dtls_id<>$dtls_id";
		if($txt_width!=""){$dia_widthCond="and b.dia_width='$txt_width'";}
		$sql_rcv="SELECT a.po_breakdown_id, sum(quantity) as grey_fabric_recv from order_wise_pro_details a, product_details_master b, pro_grey_prod_entry_dtls c, inv_receive_master d where a.prod_id=b.id and a.dtls_id = c.id and c.mst_id= d.id and d.booking_no= '$booking_no' and b.detarmination_id='$fabric_desc_id' and b.gsm='$txt_gsm' $dia_widthCond and b.item_category_id=14 and a.entry_form=550 and a.is_deleted=0 and a.status_active=1 $dtls_id_cond group by a.po_breakdown_id";
		//echo $sql_rcv;
		$recvData=sql_select($sql_rcv);
		foreach($recvData as $row)
		{
			$recv_qnty_array[$row[csf('po_breakdown_id')]]=$row[csf('grey_fabric_recv')]+$row[csf('grey_fabric_trans_recv')]-$row[csf('grey_fabric_trans_issued')];	
		}
	}
	else if($receive_basis==1)
	{
		if($dtls_id=="") $dtls_id_cond=""; else $dtls_id_cond="and a.dtls_id<>$dtls_id";
		if($txt_width!=""){$dia_widthCond="and b.dia_width='$txt_width'";}
		$sql_rcv="SELECT a.po_breakdown_id, sum(quantity) as grey_fabric_recv from order_wise_pro_details a, product_details_master b, pro_grey_prod_entry_dtls c, inv_receive_master d where a.prod_id=b.id and a.dtls_id = c.id and c.mst_id= d.id and a.po_breakdown_id in ($po_id)  $dia_widthCond and b.item_category_id=14 and a.entry_form=550 and d.receive_basis=1 and a.is_deleted=0 and a.status_active=1 $dtls_id_cond group by a.po_breakdown_id";
		//echo $sql_rcv;
		$recvData=sql_select($sql_rcv);
		foreach($recvData as $row)
		{
			$recv_qnty_array[$row[csf('po_breakdown_id')]]=$row[csf('grey_fabric_recv')]+$row[csf('grey_fabric_trans_recv')]-$row[csf('grey_fabric_trans_issued')];	
		}
	}
	
	
	if($roll_maintained==1) 
	{
		$width="855";
		//$roll_arr=return_library_array("select po_breakdown_id,max(roll_no) as roll_no from pro_roll_details where entry_form in(2,22) group by po_breakdown_id",'po_breakdown_id','roll_no');
	}
	else $width="830";
	?> 

	<script>
		var receive_basis=<? echo $receive_basis; ?>;
		var roll_maintained=<? echo $roll_maintained; ?>;
		var barcode_generation=<? echo $barcode_generation; ?>;
		
		function fn_show_check()
		{
						
			show_list_view ( document.getElementById('txt_search_common').value+'*'+document.getElementById('cbo_search_by').value+'*'+<? echo $cbo_company_id; ?>+'*'+document.getElementById('cbo_buyer_name').value+'*'+'<? echo $all_po_id; ?>'+'*'+'<? echo $booking_no; ?>', 'create_po_search_list_view', 'search_div', 'woven_grey_fabric_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1);hidden_field_reset();');
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
		
		//For Reject Qty
		function distribute_qnty2(str)
		{
			if(str==1)
			{
				$('#txt_prop_reject_qnty').attr('disabled',false);
				var txt_prop_reject_qnty=$('#txt_prop_reject_qnty').val()*1;
				var tblRow = $("#tbl_list_search tr").length;
				var balance =txt_prop_reject_qnty;
				var len=totalReject=0;
				
				$("#tbl_list_search").find('tr').each(function()
				{
					len=len+1;
					
					var txtOrginal=$(this).find('input[name="txtOrginal[]"]').val()*1;
					var isDisbled=$(this).find('input[name="txtRejectQnty[]"]').is(":disabled");
					var placeholder_value =$(this).find('input[name="txtRejectQnty[]"]').attr('placeholder')*1;
					
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
								var reject_qnty=placeholder_value;
								balance=balance-placeholder_value;
							}
							else
							{
								var reject_qnty=balance;
								balance=0;
							}
							
							if(tblRow==len)
							{
								var reject_qnty=txt_prop_reject_qnty-totalReject;							
							}
							
							totalReject = totalReject*1+reject_qnty*1;

							$(this).find('input[name="txtRejectQnty[]"]').val(reject_qnty.toFixed(2));
						}
						else
						{
							$(this).find('input[name="txtRejectQnty[]"]').val('');
						}
					}
				});
			}
			else
			{
				$('#txt_prop_reject_qnty').val('');
				$('#txt_prop_reject_qnty').attr('disabled',true);
				$("#tbl_list_search").find('tr').each(function()
				{
					if($(this).find('input[name="txtRejectQnty[]"]').is(":disabled")==false)
					{
						$(this).find('input[name="txtRejectQnty[]"]').val('');
					}
				});
			}
			
			calculate_tot_qnty2();
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
				var response=return_global_ajax_value( data, 'roll_duplication_check', '', 'woven_grey_fabric_receive_controller');
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
				var txtRollId=$('#txtRollTableId_'+rowNo).val();
				var selected_id='';
				if(txtOrginal==0)
				{
					if(txtBarcodeNo!='')
					{
						if(txt_deleted_id=='') selected_id=txtRollId; else selected_id=txt_deleted_id+','+txtRollId;
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
		
		function calculate_tot_qnty2()
		{
			var tot_reject_qnty='';
			$("#tbl_list_search").find('tr').each(function()
			{
				var txtRejectQnty=$(this).find('input[name="txtRejectQnty[]"]').val()*1;
				tot_reject_qnty=tot_reject_qnty*1+txtRejectQnty*1;
			});
			//alert(tot_reject_qnty);
			$('#txt_tot_reject_qnty').val( tot_reject_qnty.toFixed(2));
		}
		
		var selected_id = new Array();
		var selected_booking_dtls_id = new Array();
		
		
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
				selected_booking_dtls_id.push( $('#booking_dtls_pi' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_booking_dtls_id.splice( i, 1 );
			}
			var id = '';
			var bd_id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				bd_id += selected_booking_dtls_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			bd_id = bd_id.substr( 0, bd_id.length - 1 );
			
			$('#hidden_booking_dtls_id_pi').val( bd_id );
			$('#po_id').val( id );
		}
		
		function show_grey_prod_recv() 
		{ 
			var po_id=$('#po_id').val();
			var hidden_booking_dtls_id_pi=$('#hidden_booking_dtls_id_pi').val();
			show_list_view ( po_id+'_'+'1'+'_'+'<? echo $dtls_id; ?>'+'_'+'<? echo $roll_maintained; ?>'+'_'+'<? echo $save_data; ?>'+'_'+'<? echo $prev_distribution_method; ?>'+'_'+'<? echo $receive_basis; ?>'+'_'+'<? echo $txt_deleted_id; ?>'+'_'+hidden_booking_dtls_id_pi, 'po_popup', 'search_div', 'woven_grey_fabric_receive_controller', '');
		}
		
		function hidden_field_reset()
		{
			$('#po_id').val('');
			$('#hidden_booking_dtls_id_pi').val('');
			$('#save_string').val( '' );
			$('#tot_grey_qnty').val( '' );
			$('#number_of_roll').val( '' );
			selected_id = new Array();
		}
		
		function fnc_close()
		{
			var save_string='';	 var tot_grey_qnty=''; var no_of_roll=''; var tot_reject_qnty='';
			var po_id_array = new Array(); var po_id_array2 = new Array(); var save_string2='';	

			var flag = false;
			
			$("#tbl_list_search").find('tr').each(function()
			{
				var txtPoQnty=$(this).find('input[name="txtPoQnty[]"]').val();
				var txtPoId=$(this).find('input[name="txtPoId[]"]').val();
				var txtGreyQnty=$(this).find('input[name="txtGreyQnty[]"]').val();
				var txtRejectQnty=$(this).find('input[name="txtRejectQnty[]"]').val();
				var txtRoll=$(this).find('input[name="txtRoll[]"]').val();
				var txtRollId=$(this).find('input[name="txtRollId[]"]').val();
				var txtRollTableId=$(this).find('input[name="txtRollTableId[]"]').val();
				var txtBarcodeNo=$(this).find('input[name="txtBarcodeNo[]"]').val();
				var txtPoBalance=$(this).find('input[name="txtPoBalance[]"]').val();

				
				tot_grey_qnty=tot_grey_qnty*1+txtGreyQnty*1;
				tot_reject_qnty=tot_reject_qnty*1+txtRejectQnty*1;
				
				if(roll_maintained==0)
				{
					txtRoll=0;
				}
				
				/*if(txtRoll*1>0)
				{
					no_of_roll=no_of_roll*1+1;	
				}*/
				
				if(txtGreyQnty*1>0)
				{
					if(roll_maintained==1)
					{
						no_of_roll=no_of_roll*1+1;
					}
					
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
				
				//for Reject Qty
				
				if(txtGreyQnty*1>0)
				{
					if(save_string2=="")
					{
						save_string2=txtPoId+"**"+txtRejectQnty+"**"+txtRoll+"**"+txtRollId+"**"+txtRollTableId+"**"+txtBarcodeNo;
					}
					else
					{
						save_string2+=","+txtPoId+"**"+txtRejectQnty+"**"+txtRoll+"**"+txtRollId+"**"+txtRollTableId+"**"+txtBarcodeNo;
					}
					//alert(save_string2);
					
					if( jQuery.inArray( txtPoId, po_id_array2) == -1 ) 
					{
						po_id_array2.push(txtPoId);
					}
				}

				if( txtGreyQnty * 1 > txtPoBalance * 1 ) {
					alert('Can not over quantity');
					flag= true;
					return;
				}
				
			});
			
			$('#save_string').val( save_string );
			$('#save_string2').val( save_string2 );
			$('#tot_grey_qnty').val( tot_grey_qnty.toFixed(2));
			$('#tot_reject_qnty').val( tot_reject_qnty.toFixed(2));
			$('#number_of_roll').val( no_of_roll );
			$('#all_po_id').val( po_id_array );
			$('#distribution_method').val( $('#cbo_distribiution_method').val() );	

			if(flag == false) parent.emailwindow.hide();
		}

	</script>

</head>

<body>
	<? 
	if($type!=1)
	{
		?>
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:<? echo $width; ?>px;margin-left:5px">
				<input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
				<input type="hidden" name="save_string2" id="save_string2" class="text_boxes" value="">
				<input type="hidden" name="tot_grey_qnty" id="tot_grey_qnty" class="text_boxes" value="">
				<input type="hidden" name="tot_reject_qnty" id="tot_reject_qnty" class="text_boxes" value="">
				<input type="hidden" name="number_of_roll" id="number_of_roll" class="text_boxes" value="">
				<input type="hidden" name="all_po_id" id="all_po_id" class="text_boxes" value="">
				<input type="hidden" name="distribution_method" id="distribution_method" class="text_boxes" value="">
				<input type="hidden" name="hide_deleted_id" id="hide_deleted_id" class="text_boxes" value="<? echo $txt_deleted_id; ?>">
		<?
	}

	if(($receive_basis==1 ) && $type!=1)
	{
		?>
		<table cellpadding="0" cellspacing="0" width="<? echo $width; ?>" class="rpt_table" border="1" rules="all">
			<thead>
				<th>Buyer</th>
				<th>Search By</th>
				<th>Search</th>
				<th>
					<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
					<input type="hidden" name="po_id" id="po_id" value="">
					<input type="hidden" name="hidden_booking_dtls_id_pi" id="hidden_booking_dtls_id_pi" value="">
				</th> 
			</thead>
			<tr class="general">
				<td align="center">
					<?
					echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",$data[0] ); 
					?>       
				</td>
				<td align="center">	
					<?
					$search_by_arr=array(1=>"PO No",2=>"Job No",3=>"Internal Ref",4=>"File No");
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
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="400" align="center">
						<thead>
							<th>Total Grey Qnty</th>
							<th>Total Reject Qnty</th>
							<th>Distribution Method</th>
						</thead>
						<tr class="general">
							<td><input type="text" name="txt_prop_grey_qnty" id="txt_prop_grey_qnty" class="text_boxes_numeric" value="<? echo $txt_receive_qnty; ?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)" disabled></td>
							<td><input type="text" name="txt_prop_reject_qnty" id="txt_prop_reject_qnty" class="text_boxes_numeric" value="<? echo $txt_reject_fabric_recv_qnty; ?>" style="width:100px" onBlur="distribute_qnty2(document.getElementById('cbo_distribiution_method').value)" disabled></td>
							<td>
								<? 
								echo create_drop_down( "cbo_distribiution_method", 250, $distribiution_method,"",0, "",2, "distribute_qnty(this.value);distribute_qnty2(this.value);",1 );
								?>
							</td>
						</tr>
					</table>
				</div>
				<div style="margin-left:5px; margin-top:10px">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $width-20; ?>">
						<thead>
							<th width="100">PO No</th>
							<th width="80">Internal Ref.</th>
							<th width="80">PO Qnty</th>
							<th width="80">Ship. Date</th>
							<th width="90">Grey Qnty</th>
							<th width="90">Reject Qnty</th>
							<?
							if($roll_maintained==1)
							{
								?>
								<th width="80">Roll</th>
								<th width="80">Barcode No.</th>
								<th width="65"></th>
								<?
							}
							?>
						</thead>
					</table>
					<div style="width:<? echo $width; ?>px; max-height:200px; overflow-y:scroll" id="list_container" align="left"> 
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $width-20; ?>" id="tbl_list_search">  
							<? 
							$i=1; $tot_po_qnty=0; $tot_grey_qnty=0; $po_array=array(); $tot_reject_qnty=0; 

							$explSaveData = explode(",",$save_data); 
							$explSaveData2 = explode(",",$save_data2); 	
							for($z=0;$z<count($explSaveData);$z++)
							{
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";

								$po_wise_data = explode("**",$explSaveData[$z]);
								$po_wise_data2 = explode("**",$explSaveData2[$z]);
								$order_id=$po_wise_data[0];
								$grey_qnty=$po_wise_data[1];
								$roll_no=$po_wise_data[2];
								$roll_not_delete_id=$po_wise_data[3];
								$roll_id=$po_wise_data[4];
								$barcode_no=$po_wise_data[5];

								$reject_qnty=$po_wise_data2[1];

								

								$po_data=sql_select("SELECT c.po_break_down_id as id,
									        c.job_no as po_number,
									        1 as total_set_qnty,
									        c.grey_fab_qnty as po_quantity,
									        c.grey_fab_qnty as po_qnty_in_pcs,
									        c.delivery_date as pub_shipment_date,
									        '' grouping
									    FROM  wo_booking_dtls c
									    WHERE     
									        c.po_break_down_id in ( $order_id )
									        AND c.booking_type = 7
									        AND c.status_active = 1
									        AND c.is_deleted = 0
									    GROUP BY c.po_break_down_id ,
									        c.job_no,
									        c.grey_fab_qnty,
									        c.delivery_date
									    ORDER BY c.delivery_date ASC");

								$booking_qnty_array=return_library_array("SELECT a.po_break_down_id, SUM (a.grey_fab_qnty) AS qnty FROM wo_booking_dtls a,fabric_sales_order_dtls b, com_pi_item_details c WHERE    a.po_break_down_id=b.id and a.pre_cost_fabric_cost_dtls_id=b.mst_id and a.po_break_down_id in ( $order_id )   and a.id = c.work_order_dtls_id and a.booking_mst_id = c.work_order_id and a.booking_type = 7  and a.status_active = 1   and a.is_deleted = 0  and c.is_deleted=0 GROUP BY a.po_break_down_id","po_break_down_id","qnty");


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
								$tot_reject_qnty+=$reject_qnty;

								$bl_qnty=$booking_qnty_array[$po_data[0][csf('id')]]-$recv_qnty_array[$po_data[0][csf('id')]];
								//echo "<pre>".$bl_qnty."=".$booking_qnty_array[$po_data[0][csf('id')]]."-".$recv_qnty_array[$po_data[0][csf('id')]]."</pre>";

								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td width="100">
										<p><? echo $po_data[0][csf('po_number')]; ?></p>
										<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $order_id; ?>">
										<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
										<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_not_delete_id; ?>">
										<!--txtRollId is used for not delete row which is used in batch -->
										<input type="hidden" name="txtRollTableId[]" id="txtRollTableId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
										<!--txtRollTableId is used for Duplication Check -->
									</td>
									<td width="80"><p><? echo $po_data[0][csf('grouping')]; ?>&nbsp;</p></td>
									<td width="80" align="right">
										<? echo $po_data[0][csf('po_qnty_in_pcs')] ?>
										<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_data[0][csf('po_qnty_in_pcs')]; ?>">
									</td>
									<td width="80" align="center"><? echo change_date_format($po_data[0][csf('pub_shipment_date')]); ?></td>
									<td align="center" width="90">
										<input type="hidden" name="txtPoBalance[]" id="txtPoBalance_<? echo $i; ?>" value="<? echo $bl_qnty; ?>">
										<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:77px" value="<? echo $grey_qnty; ?>" placeholder="<?=$bl_qnty?>" <? echo $disable; ?> onKeyUp="calculate_tot_qnty();">
									</td>
									<td align="center" width="90">
										<input type="text" name="txtRejectQnty[]" id="txtRejectQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:77px" value="<? echo $reject_qnty; ?>" <? echo $disable; ?> onKeyUp="calculate_tot_qnty2();">
									</td>
									<?
									if($roll_maintained==1)
									{
										?>
										<td width="80" align="center">
											<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? if($roll_no!=0) echo $roll_no; ?>" <? echo $disable; ?> placeholder="" onBlur="roll_duplication_check(<? echo $i; ?>);" readonly />
										</td>
										<td width="80" align="center"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? echo $barcode_no; ?>" disabled/></td>
										<td width="65">
											<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
											<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
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
							<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="<? echo $i-1; ?>">
						</table>
					</div>
					<table width="<? echo $width-20; ?>" border="1" cellpadding="0" cellspacing="0" rules="all" class="tbl_bottom">
						<tr>
							<td width="100"></td>
							<td width="80"></td>
							<td width="80"></td>
							<td width="80" align="right"><b>Total</b></td>
							<td style="text-align:center" width="90"><input type="text" name="txt_tot_grey_qnty" id="txt_tot_grey_qnty" class="text_boxes_numeric" style="width:77px" value="<? echo number_format($tot_grey_qnty,2); ?>" disabled></td>
							<td style="text-align:center" width="90"><input type="text" name="txt_tot_reject_qnty" id="txt_tot_reject_qnty" class="text_boxes_numeric" style="width:77px" value="<? echo number_format($tot_reject_qnty,2); ?>" disabled></td>
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
		if(($receive_basis==1 ) || $roll_maintained==1) 
		{
			$prev_distribution_method=2;
			$disabled="disabled='disabled'";
			$disabled_dropdown=1;
		}
		?>
		<div style="width:<? echo $width-20; ?>px; margin-top:10px" align="center">
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="400" align="center">
				<thead>
					<th>Total Grey Qnty</th>
					<th>Total Reject Qnty</th>
					<th>Distribution Method</th>
				</thead>
				<tr class="general">
					<td><input type="text" name="txt_prop_grey_qnty" id="txt_prop_grey_qnty" class="text_boxes_numeric" value="<? echo $txt_receive_qnty; ?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)" <? echo $disabled; ?>></td>
					<td><input type="text" name="txt_prop_reject_qnty" id="txt_prop_reject_qnty" class="text_boxes_numeric" value="<? echo $txt_reject_fabric_recv_qnty; ?>" style="width:100px" onBlur="distribute_qnty2(document.getElementById('cbo_distribiution_method').value)" <? echo $disabled; ?>></td>
					<td>
						<? 
						echo create_drop_down( "cbo_distribiution_method", 250, $distribiution_method,"",0, "",$prev_distribution_method, "distribute_qnty(this.value);distribute_qnty2(this.value);",$disabled_dropdown );
						?>
					</td>
				</tr>
			</table>
		</div>
		<div style="margin-left:5px; margin-top:10px">
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $width-20; ?>">
				<thead>
					
						<th width="100">PO No</th>
						<th width="80">Internal Ref.</th>
						<th width="80">PO Qnty</th>
						<th width="80">Ship. Date</th>
					<?
					

					if(($receive_basis==2) && $booking_without_order!=1)
					{
						echo '<th width="80">Req. Qnty</th>';
						if($roll_maintained==0)
						{
							echo '<th width="80">Cumm. Rcv</th>';
						}
						
					}
					?>

					<th width="90">Grey Qnty</th>
					<th width="90">Reject Qnty</th>

					<?
					if($roll_maintained==1)
					{
						?>
						<th width="75">Roll</th>
						<th width="80">Barcode No.</th>
						<th width="65"></th>
						<?
					}

					?>
				</thead>
			</table>
			<div style="width:<? echo $width; ?>px; max-height:200px; overflow-y:scroll" id="list_container" align="left"> 
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $width-20; ?>" id="tbl_list_search">  
					<? 
					$i=1; $tot_po_qnty=0; $tot_grey_qnty=0; $tot_reject_qnty=0; $po_array=array();

					if($save_data!="" && ($receive_basis==1))
					{ 
						$po_id = explode(",",$po_id);
						$explSaveData = explode(",",$save_data); 
						$explSaveData2 = explode(",",$save_data2); 	
						for($z=0;$z<count($explSaveData);$z++)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";

							$po_wise_data = explode("**",$explSaveData[$z]);
							$po_wise_data2 = explode("**",$explSaveData2[$z]);
							$order_id=$po_wise_data[0];
							$grey_qnty=$po_wise_data[1];
							$reject_qnty=$po_wise_data2[1];
							$roll_no=$po_wise_data[2];
							$roll_not_delete_id=$po_wise_data[3];
							$roll_id=$po_wise_data[4];
							$barcode_no=$po_wise_data[5];

							if(in_array($order_id,$po_id))
							{
								$po_data=sql_select("SELECT 
									        c.job_no as po_number,
									        c.grey_fab_qnty as po_qnty_in_pcs,
									        c.delivery_date as pub_shipment_date,
									        '' grouping
									    FROM  wo_booking_dtls c
									    WHERE     
									        c.booking_no = '$booking_no'
									        AND c.po_break_down_id = $order_id
									        AND c.status_active = 1
									        AND c.is_deleted = 0
									    GROUP BY
									        c.job_no,
									        c.grey_fab_qnty,
									        c.delivery_date
									    ORDER BY c.delivery_date ASC");	

								$booking_qnty_array=return_library_array("SELECT a.po_break_down_id, SUM (a.grey_fab_qnty) AS qnty FROM wo_booking_dtls a,fabric_sales_order_dtls b, com_pi_item_details c WHERE    a.po_break_down_id=b.id and a.pre_cost_fabric_cost_dtls_id=b.mst_id and a.po_break_down_id in ( $order_id )   and a.id = c.work_order_dtls_id and a.booking_mst_id = c.work_order_id and a.booking_type = 7  and a.status_active = 1   and a.is_deleted = 0  and c.is_deleted=0 GROUP BY a.po_break_down_id","po_break_down_id","qnty");


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
								$tot_reject_qnty+=$reject_qnty;
								$bl_qnty=$booking_qnty_array[$order_id]-$recv_qnty_array[$order_id];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td width="100">
										<p><? echo $po_data[0][csf('po_number')]; ?></p>
										<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $order_id; ?>">
										<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
										<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_not_delete_id; ?>">
										<!--This is used for not delete row which is used in batch -->
										<input type="hidden" name="txtRollTableId[]" id="txtRollTableId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
										<!--This is used for Duplication Check -->
									</td>
									<td width="80"><p><? echo $po_data[0][csf('grouping')]; ?>&nbsp;</p></td>
									<td width="80" align="right">
										<? echo $po_data[0][csf('po_qnty_in_pcs')] ?>
										<input type="hidden" name="txtPoBalance[]" id="txtPoBalance_<? echo $i; ?>" value="<? echo $bl_qnty; ?>">
										<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_data[0][csf('po_qnty_in_pcs')]; ?>">
									</td>
									<td width="80" align="center"><? echo change_date_format($po_data[0][csf('pub_shipment_date')]); ?></td>
									<td align="center" width="90">
										<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:77px" value="<? echo $grey_qnty; ?>" <? echo $disable; ?> onKeyUp="calculate_tot_qnty();">
									</td>
									<td align="center" width="90">
										<input type="text" name="txtRejectQnty[]" id="txtRejectQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:77px" value="<? echo $reject_qnty; ?>" <? echo $disable; ?> onKeyUp="calculate_tot_qnty2();">
									</td>
									<?
									if($roll_maintained==1)
									{
										?>
										<td width="75" align="center">
											<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" value="<? if($roll_no!=0) echo $roll_no; ?>" <? echo $disable; ?> placeholder="<? //echo $roll_arr[$order_id]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" readonly />
										</td>
										<td width="80" align="center"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? echo $barcode_no; ?>" disabled/></td>
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
							$po_sql="SELECT c.po_break_down_id as id,
									        c.job_no as po_number,
									        c.grey_fab_qnty as po_qnty_in_pcs,
									        c.delivery_date as pub_shipment_date,
									        '' grouping
									    FROM  wo_booking_dtls c
									    WHERE     
									        c.booking_no = '$booking_no'
									        AND c.po_break_down_id in ($result)
									        AND c.status_active = 1
									        AND c.is_deleted = 0
									    GROUP BY c.po_break_down_id ,
									        c.job_no,
									        c.grey_fab_qnty,
									        c.delivery_date
									    ORDER BY c.delivery_date ASC";	
						    $booking_qnty_array=return_library_array("SELECT a.po_break_down_id, SUM (a.grey_fab_qnty) AS qnty FROM wo_booking_dtls a,fabric_sales_order_dtls b, com_pi_item_details c WHERE    a.po_break_down_id=b.id and a.pre_cost_fabric_cost_dtls_id=b.mst_id and a.booking_no = '$booking_no'   and a.id = c.work_order_dtls_id and a.booking_mst_id = c.work_order_id and a.booking_type = 7  and a.status_active = 1   and a.is_deleted = 0  and c.is_deleted=0 GROUP BY a.po_break_down_id","po_break_down_id","qnty");
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
								$bl_qnty=$booking_qnty_array[$row[csf('id')]]-$recv_qnty_array[ $row[csf('id')]];

								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td width="100">
										<p><? echo $row[csf('po_number')]; ?></p>
										<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
										<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
										<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_not_delete_id; ?>">
										<!--This is used for not delete row which is used in batch -->
										<input type="hidden" name="txtRollTableId[]" id="txtRollTableId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
										<!--This is used for Duplication Check -->
									</td>
									<td width="80"><p><? echo $row[csf('grouping')]; ?>&nbsp;</p></td>
									<td width="80" align="right">
										<? echo $row[csf('po_qnty_in_pcs')]; ?>
										<input type="hidden" name="txtPoBalance[]" id="txtPoBalance_<? echo $i; ?>" value="<? echo $bl_qnty; ?>">
										<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $row[csf('po_qnty_in_pcs')]; ?>">
									</td>
									<td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
									<td align="center" width="90">
										<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:77px" value="" onKeyUp="calculate_tot_qnty();">
									</td>
									<td align="center" width="90">
										<input type="text" name="txtRejectQnty[]" id="txtRejectQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:77px" value="" onKeyUp="calculate_tot_qnty2();">
									</td>
									<?
									if($roll_maintained==1)
									{
										?>
										<td width="75" align="center">
											<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" value="" onBlur="roll_duplication_check(<? echo $i; ?>);" placeholder="<? //echo $roll_arr[$row[csf('id')]]+1; ?>" readonly />
										</td>
										<td width="80"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="" disabled/></td>
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
					else if($save_data!="" && ($receive_basis==2 ))
					{
						if($txt_width!=""){$txt_width_cond="and a.dia_width='$txt_width'";}
						$booking_qnty_array=return_library_array("  SELECT a.po_break_down_id, SUM (a.grey_fab_qnty) AS qnty FROM wo_booking_dtls a,fabric_sales_order_dtls b WHERE    A.PO_BREAK_DOWN_ID=b.id and A.PRE_COST_FABRIC_COST_DTLS_ID=b.mst_id AND a.booking_no = '$booking_no'  AND a.booking_type = 7  AND a.status_active = 1   AND a.is_deleted = 0  GROUP BY a.po_break_down_id","po_break_down_id","qnty");
						
						if($roll_maintained==1)
						{
							$all_po_id = explode(",",$all_po_id);
							$explSaveData = explode(",",$save_data); 	
							$explSaveData2 = explode(",",$save_data2); 	
							for($z=0;$z<count($explSaveData);$z++)
							{
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";

								$po_wise_data = explode("**",$explSaveData[$z]);	
								$po_wise_data2 = explode("**",$explSaveData2[$z]);
								$order_id=$po_wise_data[0];
								$grey_qnty=$po_wise_data[1];
								$reject_qnty=$po_wise_data2[1];
								$roll_no=$po_wise_data[2];
								$roll_not_delete_id=$po_wise_data[3];
								$roll_id=$po_wise_data[4];
								$barcode_no=$po_wise_data[5]; 

								$req_qnty=$booking_qnty_array[$order_id];
								$bl_qnty=$req_qnty-$recv_qnty_array[$order_id];
								//echo "<pre>".$bl_qnty."=".$req_qnty."-".$recv_qnty_array[$order_id]."</pre>";

								
								
								
								$po_data=sql_select("SELECT 
									        c.job_no as po_number,
									        c.grey_fab_qnty as po_qnty_in_pcs,
									        c.delivery_date as pub_shipment_date,
									        '' grouping
									    FROM  wo_booking_dtls c
									    WHERE     
									        c.booking_no = '$booking_no'
									        AND c.po_break_down_id = $order_id
									        AND c.status_active = 1
									        AND c.is_deleted = 0
									    GROUP BY
									        c.job_no,
									        c.grey_fab_qnty,
									        c.delivery_date
									    ORDER BY c.delivery_date ASC");	
								


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
								$tot_reject_qnty+=$reject_qnty;
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									
									<td width="100">
										<p><? echo $po_data[0][csf('po_number')]; ?></p>
										<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $order_id; ?>">
										<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
										<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_not_delete_id; ?>">
										<!--This is used for not delete row which is used in batch -->
										<input type="hidden" name="txtRollTableId[]" id="txtRollTableId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
										<!--This is used for Duplication Check -->
									</td>
									<td width="80"><p><? echo $po_data[0][csf('grouping')]; ?>&nbsp;</p></td>
									<td width="80" align="right">
										<? echo $po_data[0][csf('po_qnty_in_pcs')]; ?>
										<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_data[0][csf('po_qnty_in_pcs')]; ?>">
										<input type="hidden" name="txtPoBalance[]" id="txtPoBalance_<? echo $i; ?>" value="<? echo $bl_qnty; ?>">
									</td>
									<td width="80" align="center"><? echo change_date_format($po_data[0][csf('pub_shipment_date')]); ?></td>
									<td width="80" align="right"><? echo number_format($req_qnty,2,'.',''); ?></td>
									

									<td align="center" width="90">
										<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:77px" value="<? echo $grey_qnty; ?>" <? echo $disable; ?> placeholder="<? echo number_format($bl_qnty,2,'.',''); ?>" onKeyUp="calculate_tot_qnty();">
									</td>
									<td align="center" width="90">
										<input type="text" name="txtRejectQnty[]" id="txtRejectQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:77px" value="<? echo $reject_qnty; ?>" <? echo $disable; ?> placeholder="<? //echo number_format($bl_qnty,2,'.',''); ?>" onKeyUp="calculate_tot_qnty2();">
									</td>
									<td width="75" align="center">
										<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" value="<? if($roll_no!=0) echo $roll_no; ?>" <? echo $disable; ?> placeholder="<? //echo $roll_arr[$order_id]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" readonly />
									</td>
									<td width="80"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? echo $barcode_no; ?>" disabled/></td>
									<td width="65">
										<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
										<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
									</td>
								</tr>
								<? 
								$i++;
							}
							if(($receive_basis==2 ) && $booking_without_order!=1)
							{
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
									
									$po_sql="SELECT c.po_break_down_id as id,
									        c.job_no as po_number,
									        c.grey_fab_qnty as po_qnty_in_pcs,
									        c.delivery_date as pub_shipment_date,
									        '' grouping
									    FROM  wo_booking_dtls c
									    WHERE     
									        c.booking_no = '$booking_no'
									        AND c.po_break_down_id in ($result)
									        AND c.status_active = 1
									        AND c.is_deleted = 0
									    GROUP BY c.po_break_down_id ,
									        c.job_no,
									        c.grey_fab_qnty,
									        c.delivery_date
									    ORDER BY c.delivery_date ASC";	
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
											<td width="100">
												<p><? echo $row[csf('po_number')]; ?></p>
												<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
												<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
												<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_not_delete_id; ?>">
												<!--This is used for not delete row which is used in batch -->
												<input type="hidden" name="txtRollTableId[]" id="txtRollTableId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
												<!--This is used for Duplication Check -->
											</td>
											<td width="80"><p><? echo $row[csf('grouping')]; ?>&nbsp;</p></td>
											<td width="80" align="right">
												<? echo $row[csf('po_qnty_in_pcs')]; ?>
												<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $row[csf('po_qnty_in_pcs')]; ?>">
											</td>
											<td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
											<td width="80" align="right"><? echo number_format($req_qnty,2,'.',''); ?></td>
											<td align="center" width="90">
												<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:77px" value="" placeholder="<? echo number_format($bl_qnty,2,'.',''); ?>" onKeyUp="calculate_tot_qnty();">
											</td>
											<td align="center" width="90">
												<input type="text" name="txtRejectQnty[]" id="txtRejectQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:77px" value="" placeholder="<? //echo number_format($bl_qnty,2,'.',''); ?>" onKeyUp="calculate_tot_qnty2();">
											</td>
											<td width="75" align="center">
												<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" value="" onBlur="roll_duplication_check(<? echo $i; ?>);" placeholder="<? //echo $roll_arr[$row[csf('id')]]+1; ?>" readonly />
											</td>
											<td width="80"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="" disabled/></td>
											<td width="65">
												<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
												<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
											</td>
										</tr>
										<? 
										$i++; 
									}
								}
							}
						}
						else
						{
							$prev_po_qnty_arr=array();$prev_po_qnty_arr2=array();
							$explSaveData = explode(",",$save_data); 
							$explSaveData2 = explode(",",$save_data2); 
							for($z=0;$z<count($explSaveData);$z++)
							{
								$po_wise_data = explode("**",$explSaveData[$z]);
								$po_wise_data2 = explode("**",$explSaveData2[$z]);
								$order_id=$po_wise_data[0];
								$grey_qnty=$po_wise_data[1];
								$reject_qnty=$po_wise_data2[1];
								$prev_po_qnty_arr[$order_id]=$grey_qnty;
								$prev_po_qnty_arr2[$order_id]=$reject_qnty;
							}
							
							$po_sql="SELECT c.po_break_down_id as id,
									        c.job_no as po_number,
									        1 as total_set_qnty,
									        c.grey_fab_qnty as po_quantity,
									        c.delivery_date as pub_shipment_date,
									        '' grouping
									    FROM  wo_booking_dtls c
									    WHERE     
									        c.booking_no = '$booking_no'
									        AND c.status_active = 1
									        AND c.is_deleted = 0
									    GROUP BY c.po_break_down_id ,
									        c.job_no,
									        c.grey_fab_qnty,
									        c.delivery_date
									    ORDER BY c.delivery_date ASC";
							//echo $po_sql;
							
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
								$reject_qnty=$prev_po_qnty_arr2[$row[csf('id')]];
								$req_qnty=$booking_qnty_array[$row[csf('id')]];
								$tot_grey_qnty+=$grey_qnty;
								$tot_reject_qnty+=$reject_qnty;
								$bl_qnty=$req_qnty-$recv_qnty_array[$row[csf('id')]];
								//echo "<pre>".$bl_qnty."=".$req_qnty."-".$recv_qnty_array[$row[csf('id')]]."</pre>";
								// echo "<pre>";
								// print_r($recv_qnty_array);

								// echo $row[csf('id')]."=>".$recv_qnty_array[$row[csf('id')]];

								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td width="100">
										<p><? echo $row[csf('po_number')]; ?></p>
										<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
										<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
										<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_not_delete_id; ?>">
										<!--This is used for not delete row which is used in batch -->
										<input type="hidden" name="txtRollTableId[]" id="txtRollTableId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
										<!--This is used for Duplication Check -->
									</td>
									<td width="80"><p><? echo $row[csf('grouping')]; ?>&nbsp;</p></td>
									<td width="80" align="right">
										<? echo $po_qnty_in_pcs; ?>

										<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_qnty_in_pcs; ?>">
										<input type="hidden" name="txtPoBalance[]" id="txtPoBalance_<? echo $i; ?>" value="<? echo $bl_qnty; ?>">
									</td>
									<td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
									<td width="80" align="right"><? echo number_format($req_qnty,2,'.',''); ?></td>
									<td width="80" align="right"><? echo number_format($recv_qnty_array[$row[csf('id')]],2,'.',''); ?></td>
									<td align="center" width="90">
										<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:77px" value="<? echo $grey_qnty; ?>" placeholder="<? echo number_format($bl_qnty,2,'.',''); ?>" onKeyUp="calculate_tot_qnty();">
									</td>
									<td align="center" width="90">
										<input type="text" name="txtRejectQnty[]" id="txtRejectQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:77px" value="<? echo $reject_qnty; ?>" placeholder="<? //echo number_format($bl_qnty,2,'.',''); ?>" onKeyUp="calculate_tot_qnty2();">
									</td>
								</tr>
								<? 
								$i++; 
							} 
						}
					}
					else if($receive_basis==2)
					{
						$booking_qnty_array=return_library_array("  SELECT a.po_break_down_id, SUM (a.grey_fab_qnty) AS qnty FROM wo_booking_dtls a,fabric_sales_order_dtls b WHERE    A.PO_BREAK_DOWN_ID=b.id and A.PRE_COST_FABRIC_COST_DTLS_ID=b.mst_id AND a.booking_no = '$booking_no'  AND a.booking_type = 7  AND a.status_active = 1   AND a.is_deleted = 0  GROUP BY a.po_break_down_id","po_break_down_id","qnty");
						
						if($type==1)
						{
							if($po_id!="")
							{
								$po_sql="SELECT c.po_break_down_id as id,
									        c.job_no as po_number,
									        1 as total_set_qnty,
									        c.grey_fab_qnty as po_quantity,
									        c.delivery_date as pub_shipment_date,
									        '' grouping
									    FROM  wo_booking_dtls c,fabric_sales_order_dtls b
									    WHERE c.pre_cost_fabric_cost_dtls_id = b.mst_id   
											and c.po_break_down_id = b.id 
											and b.determination_id=$fabric_desc_id
									        and c.booking_no = '$booking_no'
											and b.status_active = 1
									        and b.is_deleted = 0
									        AND c.status_active = 1
									        AND c.is_deleted = 0
									    GROUP BY c.po_break_down_id ,
									        c.job_no,
									        c.grey_fab_qnty,
									        c.delivery_date
									    ORDER BY c.delivery_date ASC";
							}
						}
						else
						{
							$po_sql="SELECT c.po_break_down_id as id,
									        c.job_no as po_number,
									        1 as total_set_qnty,
									        c.grey_fab_qnty as po_quantity,
									        c.delivery_date as pub_shipment_date,
									        '' grouping
									    FROM  wo_booking_dtls c,fabric_sales_order_dtls b
									    WHERE  c.pre_cost_fabric_cost_dtls_id = b.mst_id   
											and c.po_break_down_id = b.id 
											and b.determination_id=$fabric_desc_id
									        and c.booking_no = '$booking_no'
											and b.status_active = 1
									        and b.is_deleted = 0
									        AND c.status_active = 1
									        AND c.is_deleted = 0
									    GROUP BY c.po_break_down_id ,
									        c.job_no,
									        c.grey_fab_qnty,
									        c.delivery_date
									    ORDER BY c.delivery_date ASC";
						}
						
						//echo $po_sql;
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
							$tot_reject_qnty+=$reject_qnty;

							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
								
								<td width="100">
									<p><? echo $row[csf('po_number')]; ?></p>
									<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
									<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
									<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_not_delete_id; ?>">
									<!--This is used for not delete row which is used in batch -->
									<input type="hidden" name="txtRollTableId[]" id="txtRollTableId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
									<!--This is used for Duplication Check -->
								</td>
								<td width="80"><p><? echo $row[csf('grouping')]; ?>&nbsp;</p></td>
								<td width="80" align="right">
									<? echo $po_qnty_in_pcs; ?>
									<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_qnty_in_pcs; ?>">
								</td>
								<td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
								<?
								
									$req_qnty=$booking_qnty_array[$row[csf('id')]];
									$bl_qnty=$req_qnty-$recv_qnty_array[$row[csf('id')]];
									echo '<td width="80" align="right">'.number_format($req_qnty,2,'.','').'</td>';
								
								
								?>
								<td width="80" align="right"><? echo number_format($recv_qnty_array[$row[csf('id')]],2,'.',''); ?></td>

								<td align="center" width="90">
									<input type="hidden" name="txtPoBalance[]" id="txtPoBalance_<? echo $i; ?>" value="<? echo $bl_qnty; ?>">
									<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" value="<? echo $grey_qnty; ?>" <? echo $disable; ?> placeholder="<? if($receive_basis==2) echo number_format($bl_qnty,2,'.',''); ?>" onKeyUp="calculate_tot_qnty();">
								</td>
								<td align="center" width="90">
									<input type="text" name="txtRejectQnty[]" id="txtRejectQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" value="<? echo $reject_qnty; ?>" <? echo $disable; ?> placeholder="<? //if($receive_basis==2) echo number_format($bl_qnty,2,'.',''); ?>" onKeyUp="calculate_tot_qnty2();">
								</td>
								<?
								if($roll_maintained==1)
								{
									?>
									<td width="75" align="center">
										<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" value="<? if($roll_no!=0) echo $roll_no; ?>" <? echo $disable; ?> placeholder="<? //echo $roll_arr[$row[csf('id')]]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" readonly />
									</td>
									<td width="80" align="center"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" value="" disabled/></td>
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
					else if($receive_basis==1)
					{
						
						
						if($type==1)
						{
							if($po_id!="")
							{
								$po_sql="SELECT c.po_break_down_id as id,
									        c.job_no as po_number,
									        1 as total_set_qnty,
									        c.grey_fab_qnty as po_quantity,
									        c.delivery_date as pub_shipment_date,
									        '' grouping
									    FROM  wo_booking_dtls c
									    WHERE     
									        c.po_break_down_id in ( $po_id )
									        AND c.booking_type = 7
									        AND c.status_active = 1
									        AND c.is_deleted = 0
									    GROUP BY c.po_break_down_id ,
									        c.job_no,
									        c.grey_fab_qnty,
									        c.delivery_date
									    ORDER BY c.delivery_date ASC";
								$booking_qnty_array=return_library_array("SELECT a.po_break_down_id, SUM (a.grey_fab_qnty) AS qnty FROM wo_booking_dtls a,fabric_sales_order_dtls b, com_pi_item_details c WHERE    a.po_break_down_id=b.id and a.pre_cost_fabric_cost_dtls_id=b.mst_id and a.po_break_down_id in ( $po_id )   and a.id = c.work_order_dtls_id and a.booking_mst_id = c.work_order_id and a.booking_type = 7  and a.status_active = 1   and a.is_deleted = 0  and c.is_deleted=0 GROUP BY a.po_break_down_id","po_break_down_id","qnty");
							}
						}
						else
						{
							$po_sql="SELECT c.po_break_down_id as id,
									        c.job_no as po_number,
									        1 as total_set_qnty,
									        c.grey_fab_qnty as po_quantity,
									        c.delivery_date as pub_shipment_date,
									        '' grouping
									    FROM  wo_booking_dtls c
									    WHERE     
									        c.booking_no = '$booking_no'
									        AND c.booking_type = 7
									        AND c.status_active = 1
									        AND c.is_deleted = 0
									    GROUP BY c.po_break_down_id ,
									        c.job_no,
									        c.grey_fab_qnty,
									        c.delivery_date
									    ORDER BY c.delivery_date ASC";

							$booking_qnty_array=return_library_array("SELECT a.po_break_down_id, SUM (a.grey_fab_qnty) AS qnty FROM wo_booking_dtls a,fabric_sales_order_dtls b WHERE    a.po_break_down_id=b.id and a.pre_cost_fabric_cost_dtls_id=b.mst_id  and  a.booking_no= '$booking_no' AND a.booking_type = 7  AND a.status_active = 1   AND a.is_deleted = 0  GROUP BY a.po_break_down_id","po_break_down_id","qnty");
						}
						
						//echo $po_sql;
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
							$grey_qnty = $row[csf('po_quantity')];
							$tot_grey_qnty+=$grey_qnty;	
							$tot_reject_qnty+=$reject_qnty;

							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
								
								<td width="100">
									<p><? echo $row[csf('po_number')]; ?></p>
									<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
									<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
									<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_not_delete_id; ?>">
									<!--This is used for not delete row which is used in batch -->
									<input type="hidden" name="txtRollTableId[]" id="txtRollTableId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
									<!--This is used for Duplication Check -->
								</td>
								<td width="80"><p><? echo $row[csf('grouping')]; ?>&nbsp;</p></td>
								<td width="80" align="right">
									<? echo $po_qnty_in_pcs; ?>
									<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_qnty_in_pcs; ?>">
								</td>
								<td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
								<?
								
								$bl_qnty=$booking_qnty_array[$row[csf('id')]]-$recv_qnty_array[$row[csf('id')]];
								?>
								

								<td align="center" width="90">
									<input type="hidden" name="txtPoBalance[]" id="txtPoBalance_<? echo $i; ?>" value="<? echo $bl_qnty; ?>">
									<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" value="<? echo $grey_qnty; ?>" <? echo $disable; ?> placeholder="<?  echo number_format($bl_qnty,2,'.',''); ?>" onKeyUp="calculate_tot_qnty();">
								</td>
								<td align="center" width="90">
									<input type="text" name="txtRejectQnty[]" id="txtRejectQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" value="<? echo $reject_qnty; ?>" <? echo $disable; ?> placeholder="<? //if($receive_basis==2) echo number_format($bl_qnty,2,'.',''); ?>" onKeyUp="calculate_tot_qnty2();">
								</td>
								<?
								if($roll_maintained==1)
								{
									?>
									<td width="75" align="center">
										<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" value="<? if($roll_no!=0) echo $roll_no; ?>" <? echo $disable; ?> placeholder="<? //echo $roll_arr[$row[csf('id')]]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" readonly />
									</td>
									<td width="80" align="center"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" value="" disabled/></td>
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
					<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="<? echo $i-1; ?>">
				</table>
			</div>
			<table width="<? echo $width-20; ?>" border="1" cellpadding="0" cellspacing="0" rules="all" class="tbl_bottom">
				<tr>
					
					<td width="100"></td>
					<td width="80"></td>
					<td width="80"></td>
					<?
					

					if(($receive_basis==2 ) && $booking_without_order!=1)
					{
						echo '<td width="80"></td>';
						if($roll_maintained ==0){
							echo '<td width="80"></td>';
						}
					}
					?>
					<td width="80" align="right"><b>Total</b></td>
					<td style="text-align:center; width:90px"><input type="text" name="txt_tot_grey_qnty" id="txt_tot_grey_qnty" class="text_boxes_numeric" style="width:77px" value="<? echo number_format($tot_grey_qnty,2); ?>" disabled></td>
					<td style="text-align:center;width:90px"><input type="text" name="txt_tot_reject_qnty" id="txt_tot_reject_qnty" class="text_boxes_numeric" style="width:77px" value="<? echo number_format($tot_reject_qnty,2); ?>" disabled></td>
					<?
					if($roll_maintained==1)
					{
						echo '<td width="75"></td><td width="80"></td><td width="65"></td>';
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

if ($action=="sales_order_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $txt_reject_fabric_recv_qnty;
	//$txt_receive_qnty;
	$data=explode("_",$data);
	$po_id=$data[0]; $type=$data[1];
	//echo "book_ord=".$booking_without_order;die;
	//echo "type=".$type;die;
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
	if($dtls_id=="") $dtls_id_cond=""; else $dtls_id_cond="and a.dtls_id<>$dtls_id";
	if($txt_width!=""){$dia_widthCond="and b.dia_width='$txt_width'";}

	$recvData=sql_select("select a.po_breakdown_id, sum(quantity) as grey_fabric_recv from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and b.detarmination_id='$fabric_desc_id' and b.gsm='$txt_gsm' $dia_widthCond and b.item_category_id=13 and a.entry_form=550 and a.is_deleted=0 and a.status_active=1 $dtls_id_cond and a.is_sales=1 group by a.po_breakdown_id");
	foreach($recvData as $row)
	{
		$recv_qnty_array[$row[csf('po_breakdown_id')]]=$row[csf('grey_fabric_recv')]+$row[csf('grey_fabric_trans_recv')]-$row[csf('grey_fabric_trans_issued')];	
	}
	


	if($roll_maintained==1) 
	{
		$width="855";
	}
	else $width="830";
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
			show_list_view ( document.getElementById('txt_search_common').value+'*'+document.getElementById('cbo_search_by').value+'*'+<? echo $cbo_company_id; ?>+'*'+document.getElementById('cbo_buyer_name').value+'*'+'<? echo $all_po_id; ?>', 'create_po_search_list_view', 'search_div', 'woven_grey_fabric_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1);hidden_field_reset();');
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
		
		//For Reject Qty
		function distribute_qnty2(str)
		{
			if(str==1)
			{
				$('#txt_prop_reject_qnty').attr('disabled',false);
				var txt_prop_reject_qnty=$('#txt_prop_reject_qnty').val()*1;
				var tblRow = $("#tbl_list_search tr").length;
				var balance =txt_prop_reject_qnty;
				var len=totalReject=0;
				
				$("#tbl_list_search").find('tr').each(function()
				{
					len=len+1;
					
					var txtOrginal=$(this).find('input[name="txtOrginal[]"]').val()*1;
					var isDisbled=$(this).find('input[name="txtRejectQnty[]"]').is(":disabled");
					var placeholder_value =$(this).find('input[name="txtRejectQnty[]"]').attr('placeholder')*1;
					
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
								var reject_qnty=placeholder_value;
								balance=balance-placeholder_value;
							}
							else
							{
								var reject_qnty=balance;
								balance=0;
							}
							
							if(tblRow==len)
							{
								var reject_qnty=txt_prop_reject_qnty-totalReject;							
							}
							
							totalReject = totalReject*1+reject_qnty*1;

							$(this).find('input[name="txtRejectQnty[]"]').val(reject_qnty.toFixed(2));
						}
						else
						{
							$(this).find('input[name="txtRejectQnty[]"]').val('');
						}
					}
				});
			}
			else
			{
				$('#txt_prop_reject_qnty').val('');
				$('#txt_prop_reject_qnty').attr('disabled',true);
				$("#tbl_list_search").find('tr').each(function()
				{
					if($(this).find('input[name="txtRejectQnty[]"]').is(":disabled")==false)
					{
						$(this).find('input[name="txtRejectQnty[]"]').val('');
					}
				});
			}
			
			calculate_tot_qnty2();
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
				var response=return_global_ajax_value( data, 'roll_duplication_check', '', 'woven_grey_fabric_receive_controller');
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
				var txtRollId=$('#txtRollTableId_'+rowNo).val();
				var selected_id='';
				if(txtOrginal==0)
				{
					if(txtBarcodeNo!='')
					{
						if(txt_deleted_id=='') selected_id=txtRollId; else selected_id=txt_deleted_id+','+txtRollId;
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
		
		function calculate_tot_qnty2()
		{
			var tot_reject_qnty='';
			$("#tbl_list_search").find('tr').each(function()
			{
				var txtRejectQnty=$(this).find('input[name="txtRejectQnty[]"]').val()*1;
				tot_reject_qnty=tot_reject_qnty*1+txtRejectQnty*1;
			});
			//alert(tot_reject_qnty);
			$('#txt_tot_reject_qnty').val( tot_reject_qnty.toFixed(2));
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
			show_list_view ( po_id+'_'+'1'+'_'+'<? echo $dtls_id; ?>'+'_'+'<? echo $roll_maintained; ?>'+'_'+'<? echo $save_data; ?>'+'_'+'<? echo $prev_distribution_method; ?>'+'_'+'<? echo $receive_basis; ?>'+'_'+'<? echo $txt_deleted_id; ?>', 'po_popup', 'search_div', 'woven_grey_fabric_receive_controller', '');
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
			var save_string='';	 var tot_grey_qnty=''; var no_of_roll=''; var tot_reject_qnty='';
			var po_id_array = new Array(); var po_id_array2 = new Array(); var save_string2='';	
			
			$("#tbl_list_search").find('tr').each(function()
			{
				var txtPoId=$(this).find('input[name="txtPoId[]"]').val();
				var txtGreyQnty=$(this).find('input[name="txtGreyQnty[]"]').val();
				var txtRejectQnty=$(this).find('input[name="txtRejectQnty[]"]').val();
				var txtRoll=$(this).find('input[name="txtRoll[]"]').val();
				var txtRollId=$(this).find('input[name="txtRollId[]"]').val();
				var txtRollTableId=$(this).find('input[name="txtRollTableId[]"]').val();
				var txtBarcodeNo=$(this).find('input[name="txtBarcodeNo[]"]').val();
				
				tot_grey_qnty=tot_grey_qnty*1+txtGreyQnty*1;
				tot_reject_qnty=tot_reject_qnty*1+txtRejectQnty*1;
				
				if(roll_maintained==0)
				{
					txtRoll=0;
				}
				
				/*if(txtRoll*1>0)
				{
					no_of_roll=no_of_roll*1+1;	
				}*/
				
				if(txtGreyQnty*1>0)
				{
					if(roll_maintained==1)
					{
						no_of_roll=no_of_roll*1+1;
					}
					
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
				
				//for Reject Qty
				
				if(txtRejectQnty*1>0)
				{
					if(save_string2=="")
					{
						save_string2=txtPoId+"**"+txtRejectQnty+"**"+txtRoll+"**"+txtRollId+"**"+txtRollTableId+"**"+txtBarcodeNo;
					}
					else
					{
						save_string2+=","+txtPoId+"**"+txtRejectQnty+"**"+txtRoll+"**"+txtRollId+"**"+txtRollTableId+"**"+txtBarcodeNo;
					}
					//alert(save_string2);
					
					if( jQuery.inArray( txtPoId, po_id_array2) == -1 ) 
					{
						po_id_array2.push(txtPoId);
					}
				}
				
			});
			
			$('#save_string').val( save_string );
			$('#save_string2').val( save_string2 );
			$('#tot_grey_qnty').val( tot_grey_qnty.toFixed(2));
			$('#tot_reject_qnty').val( tot_reject_qnty.toFixed(2));
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
			<fieldset style="width:<? echo $width; ?>px;margin-left:5px">
				<input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
				<input type="hidden" name="save_string2" id="save_string2" class="text_boxes" value="">
				<input type="hidden" name="tot_grey_qnty" id="tot_grey_qnty" class="text_boxes" value="">
				<input type="hidden" name="tot_reject_qnty" id="tot_reject_qnty" class="text_boxes" value="">
				<input type="hidden" name="number_of_roll" id="number_of_roll" class="text_boxes" value="">
				<input type="hidden" name="all_po_id" id="all_po_id" class="text_boxes" value="">
				<input type="hidden" name="distribution_method" id="distribution_method" class="text_boxes" value="">
				<input type="hidden" name="hide_deleted_id" id="hide_deleted_id" class="text_boxes" value="<? echo $txt_deleted_id; ?>">
		<?
	}


	$disabled=""; $disabled_dropdown=0;
	if(($receive_basis==1 || $receive_basis==4 || $receive_basis==6 || $receive_basis==9) || $roll_maintained==1) 
	{
		$prev_distribution_method=2;
		$disabled="disabled='disabled'";
		$disabled_dropdown=1;
	}
	?>
	<div style="width:<? echo $width-20; ?>px; margin-top:10px" align="center">
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="400" align="center">
			<thead>
				<th>Total Grey Qnty</th>
				<th>Total Reject Qnty</th>
				<th>Distribution Method</th>
			</thead>
			<tr class="general">
				<td><input type="text" name="txt_prop_grey_qnty" id="txt_prop_grey_qnty" class="text_boxes_numeric" value="<? echo $txt_receive_qnty; ?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)" <? echo $disabled; ?>></td>
				<td><input type="text" name="txt_prop_reject_qnty" id="txt_prop_reject_qnty" class="text_boxes_numeric" value="<? echo $txt_reject_fabric_recv_qnty; ?>" style="width:100px" onBlur="distribute_qnty2(document.getElementById('cbo_distribiution_method').value)" <? echo $disabled; ?>></td>
				<td>
					<? 
					echo create_drop_down( "cbo_distribiution_method", 250, $distribiution_method,"",0, "",$prev_distribution_method, "distribute_qnty(this.value);distribute_qnty2(this.value);",$disabled_dropdown );
					?>
				</td>
			</tr>
		</table>
	</div>
	<div style="margin-left:5px; margin-top:10px">
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $width-20; ?>">
			<thead>

				<th width="100">Sale Order No</th>
				<th width="80">Req. Qnty</th>
				<th width="80">Cumm. Rcv</th>

				<th width="90">Grey Qnty</th>
				<th width="90">Reject Qnty</th>

				<?
				if($roll_maintained==1)
				{
					?>
					<th width="75">Roll</th>
					<th width="80">Barcode No.</th>
					<th width="65"></th>
					<?
				}

				?>
			</thead>
		</table>
		<div style="width:<? echo $width; ?>px; max-height:200px; overflow-y:scroll" id="list_container" align="left"> 
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $width-20; ?>" id="tbl_list_search">  
				<? 
				$i=1; $tot_po_qnty=0; $tot_grey_qnty=0; $tot_reject_qnty=0; $po_array=array();

				if($save_data!="")
				{
					if($txt_width=="")
					{
						if($db_type==0){
							$txt_width_cond=" and b.dia=''";
						}else{
							$txt_width_cond=" and b.dia is null";
						}
					}
					else{
						$txt_width_cond="and b.dia='$txt_width'";
					}

					$po_sql=sql_select("select a.id, a.job_no, sum(b.grey_qty) as qnty from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.job_no = '$booking_no' and b.body_part_id='$cbo_body_part' and b.determination_id='$fabric_desc_id' and b.gsm_weight='$txt_gsm' $txt_width_cond group by a.id, a.job_no");



					if($roll_maintained==1)
					{
						$all_po_id = explode(",",$all_po_id);
						$explSaveData = explode(",",$save_data); 	
						$explSaveData2 = explode(",",$save_data2); 

						for($x=0;$x<count($explSaveData);$x++)
						{
							$all_roll_id[]=$po_wise_data[4];
						}

						$all_roll_ids = implode(",",array_filter(array_unique($all_roll_id)));

						if($all_roll_ids !="")
						{
							$roll_used_arr = return_library_array("select roll_used,id from pro_roll_details where id in ($all_roll_ids)","id","roll_used");
						}


						for($z=0;$z<count($explSaveData);$z++)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";

							$po_wise_data = explode("**",$explSaveData[$z]);	
							$po_wise_data2 = explode("**",$explSaveData2[$z]);
							$order_id=$po_wise_data[0];
							$grey_qnty=$po_wise_data[1];
							$reject_qnty=$po_wise_data2[1];
							$roll_no=$po_wise_data[2];
							$roll_not_delete_id=$po_wise_data[3];
							$roll_id=$po_wise_data[4];
							$barcode_no=$po_wise_data[5]; 

							$req_qnty=$po_sql[0][csf('qnty')];
							$bl_qnty=$req_qnty-$recv_qnty_array[$order_id];


							//$po_data=sql_select("select b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$order_id");
							


							//$roll_used=return_field_value("roll_used","pro_roll_details","id='$roll_id'");  $po_sql
							$roll_used= $roll_used_arr[$roll_id];

							if(!(in_array($order_id,$po_array)))
							{
								if($roll_not_delete_id==0) $tot_po_qnty+=$po_sql[0][csf('qnty')];
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
							$tot_reject_qnty+=$reject_qnty;
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">

								<td width="100">
									<p><? echo $po_sql[0][csf('job_no')]; ?></p>
									<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $order_id; ?>">
									<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
									<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_not_delete_id; ?>">
									<!--This is used for not delete row which is used in batch -->
									<input type="hidden" name="txtRollTableId[]" id="txtRollTableId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
									<!--This is used for Duplication Check -->
								</td>

								<td width="80" align="right"><? echo number_format($req_qnty,2,'.',''); ?></td>
								<td width="80" align="right"><? echo number_format($recv_qnty_array[$order_id],2,'.',''); ?></td>
									

								<td align="center" width="90">
									<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:77px" value="<? echo $grey_qnty; ?>" <? echo $disable; ?> placeholder="<? //echo number_format($bl_qnty,2,'.',''); ?>" onKeyUp="calculate_tot_qnty();">
								</td>
								<td align="center" width="90">
									<input type="text" name="txtRejectQnty[]" id="txtRejectQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:77px" value="<? echo $reject_qnty; ?>" <? echo $disable; ?> placeholder="<? //echo number_format($bl_qnty,2,'.',''); ?>" onKeyUp="calculate_tot_qnty2();">
								</td>
								<td width="75" align="center">
									<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" value="<? if($roll_no!=0) echo $roll_no; ?>" <? echo $disable; ?> placeholder="<? //echo $roll_arr[$order_id]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" readonly />
								</td>
								<td width="80"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? echo $barcode_no; ?>" disabled/></td>
								<td width="65">
									<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
									<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
								</td>
							</tr>
							<? 
							$i++;
						}
					}
					else
					{

						echo "Yet not Developed";die;

						$prev_po_qnty_arr=array();$prev_po_qnty_arr2=array();
						$explSaveData = explode(",",$save_data); 
						$explSaveData2 = explode(",",$save_data2); 
						for($z=0;$z<count($explSaveData);$z++)
						{
							$po_wise_data = explode("**",$explSaveData[$z]);
							$po_wise_data2 = explode("**",$explSaveData2[$z]);
							$order_id=$po_wise_data[0];
							$grey_qnty=$po_wise_data[1];
							$reject_qnty=$po_wise_data2[1];
							$prev_po_qnty_arr[$order_id]=$grey_qnty;
							$prev_po_qnty_arr2[$order_id]=$reject_qnty;
						}

						if($txt_width=="")
						{
							if($db_type==0){
								$txt_width_cond=" and b.dia=''";
							}else{
								$txt_width_cond=" and b.dia is null";
							}
						}
						else{
							$txt_width_cond="and b.dia='$txt_width'";
						}

						$po_sql="select a.id, a.job_no, sum(b.grey_qty) as qnty from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.job_no = '$booking_no' and b.body_part_id='$cbo_body_part' and b.determination_id='$fabric_desc_id' and b.gsm_weight='$txt_gsm' $txt_width_cond group by a.id, a.job_no";
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
							$reject_qnty=$prev_po_qnty_arr2[$row[csf('id')]];
							$req_qnty=$booking_qnty_array[$row[csf('id')]];
							$tot_grey_qnty+=$grey_qnty;
							$tot_reject_qnty+=$reject_qnty;
							$bl_qnty=$req_qnty-$recv_qnty_array[$row[csf('id')]];

							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
								<td width="100">
									<p><? echo $row[csf('po_number')]; ?></p>
									<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
									<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
									<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_not_delete_id; ?>">
									<!--This is used for not delete row which is used in batch -->
									<input type="hidden" name="txtRollTableId[]" id="txtRollTableId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
									<!--This is used for Duplication Check -->
								</td>
								<td width="80"><p><? echo $row[csf('grouping')]; ?>&nbsp;</p></td>
								<td width="80" align="right">
									<? echo $po_qnty_in_pcs; ?>
									<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_qnty_in_pcs; ?>">
								</td>
								<td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
								<td width="80" align="right"><? echo number_format($req_qnty,2,'.',''); ?></td>
								<td width="80" align="right"><? echo number_format($recv_qnty_array[$row[csf('id')]],2,'.',''); ?></td>
								<td align="center" width="90">
									<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:77px" value="<? echo $grey_qnty; ?>" placeholder="<? echo number_format($bl_qnty,2,'.',''); ?>" onKeyUp="calculate_tot_qnty();">
								</td>
								<td align="center" width="90">
									<input type="text" name="txtRejectQnty[]" id="txtRejectQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:77px" value="<? echo $reject_qnty; ?>" placeholder="<? //echo number_format($bl_qnty,2,'.',''); ?>" onKeyUp="calculate_tot_qnty2();">
								</td>
							</tr>
							<? 
							$i++; 
						} 
					}
				}
				else
				{
					if($txt_width=="")
					{
						if($db_type==0){
							$txt_width_cond=" and b.dia=''";
						}else{
							$txt_width_cond=" and b.dia is null";
						}
					}
					else{
						$txt_width_cond="and b.dia='$txt_width'";
					}

					$po_sql="select a.id, a.job_no, sum(b.grey_qty) as qnty from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.job_no = '$booking_no' and b.body_part_id='$cbo_body_part' and b.determination_id='$fabric_desc_id' and b.gsm_weight='$txt_gsm' $txt_width_cond group by a.id, a.job_no";

					//echo $po_sql;
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
						$po_qnty_in_pcs=$row[csf('qnty')];
						$tot_po_qnty+=$po_qnty_in_pcs;

						$tot_grey_qnty+=$grey_qnty;	
						$tot_reject_qnty+=$reject_qnty;

						?>
						<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">

							<td width="100">
								<p><? echo $row[csf('job_no')]; ?></p>
								<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
								<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
								<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_not_delete_id; ?>">
								<!--This is used for not delete row which is used in batch -->
								<input type="hidden" name="txtRollTableId[]" id="txtRollTableId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
								<!--This is used for Duplication Check -->
							</td>
							<td width="80" align="right"><? echo number_format($row[csf('qnty')],2,'.','');?></td>
							<?
							$bl_qnty=$req_qnty-$recv_qnty_array[$row[csf('id')]];

							?>
							<td width="80" align="right"><? echo number_format($recv_qnty_array[$row[csf('id')]],2,'.',''); ?></td>

							<td align="center" width="90">
								<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:77px" value="<? echo $grey_qnty; ?>" <? echo $disable; ?> placeholder="<? if($receive_basis==2 || $receive_basis==11) echo number_format($bl_qnty,2,'.',''); ?>" onKeyUp="calculate_tot_qnty();">
							</td>
							<td align="center" width="90">
								<input type="text" name="txtRejectQnty[]" id="txtRejectQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:77px" value="<? echo $reject_qnty; ?>" <? echo $disable; ?> placeholder="<? //if($receive_basis==2) echo number_format($bl_qnty,2,'.',''); ?>" onKeyUp="calculate_tot_qnty2();">
							</td>
							<?
							if($roll_maintained==1)
							{
								?>
								<td width="75" align="center">
									<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" value="<? if($roll_no!=0) echo $roll_no; ?>" <? echo $disable; ?> placeholder="<? //echo $roll_arr[$row[csf('id')]]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" readonly />
								</td>
								<td width="80" align="center"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="" disabled/></td>
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
				<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="<? echo $i-1; ?>">
			</table>
		</div>
		<table width="<? echo $width-20; ?>" border="1" cellpadding="0" cellspacing="0" rules="all" class="tbl_bottom">
			<tr>
				<td width="100"></td>
				<td width="80"></td>
				<td width="80" align="right"><b>Total</b></td>
				<td style="text-align:center; width:90px"><input type="text" name="txt_tot_grey_qnty" id="txt_tot_grey_qnty" class="text_boxes_numeric" style="width:77px" value="<? echo number_format($tot_grey_qnty,2); ?>" disabled></td>
				<td style="text-align:center;width:90px"><input type="text" name="txt_tot_reject_qnty" id="txt_tot_reject_qnty" class="text_boxes_numeric" style="width:77px" value="<? echo number_format($tot_reject_qnty,2); ?>" disabled></td>
				<?
				if($roll_maintained==1)
				{
					echo '<td width="75"></td><td width="80"></td><td width="65"></td>';
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
	$data = explode("*",$data);
	
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];

	$search_field='a.job_no';	

	$company_id =$data[2];
	$buyer_id =$data[3];
	
	$all_po_id=$data[4];
	$pi_no=$data[5];
	
	if($all_po_id!="")
		$po_id_cond=" or b.id in($all_po_id)";
	else 
		$po_id_cond="";
	
	$hidden_po_id=explode(",",$all_po_id);

	$buyer_cond = "";

	if($buyer_id > 0) { $buyer_cond = "AND a.buyer_id = $buyer_id " ;}
	
	
	$sql = "SELECT a.job_no,
			       a.style_ref_no,
			       a.order_uom,
			       b.id,
			       b.grey_qty,
			       b.finish_qty,
			       a.delivery_date,
			       d.id as booking_dtls_id
			  FROM fabric_sales_order_mst a,
			       fabric_sales_order_dtls b,
			       wo_booking_dtls d,
			       com_pi_item_details c,
			       com_pi_master_details e
			 WHERE     a.job_no = b.job_no_mst
			       AND d.pre_cost_fabric_cost_dtls_id = a.id
			       AND d.po_break_down_id = b.id
			       AND c.pi_id = e.id
			       AND e.pi_number = '$pi_no'
			       AND d.id = c.work_order_dtls_id
			       AND d.booking_mst_id = c.work_order_id
			       AND a.entry_form = 547
			       AND a.company_id = $company_id
			       $buyer_cond
			       AND a.status_active = 1
			       AND a.is_deleted = 0
			       AND b.status_active = 1
			       AND b.is_deleted = 0";
	//echo $sql;die;// $po_id_cond group by b.id
	?>
	<div align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="828" class="rpt_table" >
			<thead>
				<th width="40">SL</th>
				<th width="100">Job No</th>
				<th width="110">Style No</th>
				<th width="100">Grey Qnty</th>
				<th width="90">Finish Qnty</th>
				<th width="60">UOM</th>
				<th>Delivery Date</th>
			</thead>
		</table>
		<div style="width:828px; overflow-y:scroll; max-height:240px;" id="buyer_list_view" align="center">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="810" class="rpt_table" id="tbl_list_search" >
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
						
						$roll_data_array=sql_select("select roll_no from pro_roll_details where po_breakdown_id=".$selectResult[csf('id')]." and roll_used=1 and entry_form in(550) and status_active=1 and is_deleted=0");
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
							<input type="hidden" name="booking_dtls_pi" id="booking_dtls_pi<? echo $i ?>" value="<? echo $selectResult[csf('booking_dtls_id')]; ?>"/>	
								
						</td>	
						<td width="100"><p><? echo $selectResult[csf('job_no')]; ?></p></td>
						<td width="110"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
						<td width="100"><p><? echo $selectResult[csf('grey_qty')]; ?></p></td>
						<td width="90" align="right"><? echo $selectResult[csf('finish_qty')]; ?></td> 
						<td width="60" align="center"><p><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></p></td>
						<td align="center"><? echo change_date_format($selectResult[csf('delivery_date')]); ?></td>	
					</tr>
					<?
					$i++;
				}
				?>
				<input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<? echo $po_row_id; ?>"/>
			</table>
		</div>
		<table width="830" cellspacing="0" cellpadding="0" style="border:none" align="center">
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

if($action=="color_popup")
{
	echo load_html_head_contents("Color Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);	
	//echo $booking_without_order.jahid;die;
	//echo $program_no;
	?> 
	<script>

		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
			set_all();
		});

		var selected_id = new Array, selected_name = new Array();
		
		function check_all_data(is_checked)
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) 
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function set_all()
		{
			var old=document.getElementById('txt_color_row_id').value;
			
			if(old!="")
			{   
				old=old.split(",");
				
				for(var i=0; i<old.length; i++)
				{  
					js_set_value( old[i] ) 
				}
			}
		}

		function js_set_value( str) 
		{
			
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				
				selected_id.push( $('#txt_individual_id' + str).val() );
				
				selected_name.push( $('#txt_individual' + str).val() );
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id =''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hidden_color_id').val( id );
			$('#hidden_color_no').val( name );
		}
		
	</script>
	<input type="hidden" name="hidden_color_id" id="hidden_color_id" value="" />
	<input type="hidden" name="hidden_color_no" id="hidden_color_no" value="" />
	<div>	
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="340" class="rpt_table">
			<thead>
				<th width="50">SL</th>
				<th>Color Name</th>
			</thead>
		</table>
		<div style="width:340px; max-height:280px; overflow-y:scroll">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="320" class="rpt_table" id="tbl_list_search">
				<?php 
				$i=1; $color_row_id=""; $color_id=explode(",",$color_id);
				if($recieve_basis==1)
				{
					$sql="select c.id, c.color_name from com_pi_item_details b, lib_color c where b.color_id=c.id and b.pi_id=$booking_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.id, c.color_name";
				}
				else if($recieve_basis==2)
				{
					if($booking_without_order==0)
					{
						$sql="SELECT c.id, c.color_name from wo_booking_mst a, wo_booking_dtls b, lib_color c where a.booking_no=b.booking_no and b.fabric_color_id=c.id and a.id=$booking_id and a.item_category=14 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.id, c.color_name";
					}
					if($booking_without_order==2)
					{
						$sql="SELECT c.id, c.color_name from wo_booking_mst a, wo_booking_dtls b, lib_color c where a.booking_no=b.booking_no and b.fabric_color_id=c.id and a.id=$booking_id and a.item_category=14 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.id, c.color_name";
					}
					else
					{
						$sql="SELECT c.id, c.color_name from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, lib_color c where a.booking_no=b.booking_no and b.fabric_color=c.id and a.id=$booking_id and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.id, c.color_name";
					}
				}
				else if($recieve_basis==11)
				{
					if($program_no!="")
					{
						//$lib_color=return_library_array( "select id, color_name from lib_color",'id','color_name');
						//echo $program_no.'---';
						$sql="select b.id, b.color_name from ppl_planning_info_entry_dtls a, lib_color b  where a.color_id=b.id and a.id=$program_no and b.status_active=1 and b.is_deleted=0 group by a.color_id, b.color_name";
						//$sql="select c.id, c.color_name from ppl_planning_info_entry_dtls a, lib_color c where  a.color_id=c.id and a.id=$program_no and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.id, c.color_name"; // new
						//echo $sql;
					}
					else
					{
						if($booking_without_order==1)
						{
							
							$sql="select c.id, c.color_name from wo_non_ord_knitdye_booking_mst a, wo_non_ord_knitdye_booking_dtl b, lib_color c where a.id=b.mst_id and b.gmts_color=c.id and a.id=$booking_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.id, c.color_name";
						}
						else
						{
							$sql="select c.id, c.color_name from wo_booking_mst a, wo_booking_dtls b, lib_color c where a.booking_no=b.booking_no and b.fabric_color_id=c.id and a.id=$booking_id and a.item_category=12 and b.process=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.id, c.color_name"; // old
						}
					}
				}
				else if($recieve_basis==14)
				{
					if($txt_width =="")
					{
						if($db_type==0)
						{
							$dia_cond= " and a.dia=''";
						}else{
							$dia_cond = " and a.dia is null";
						}
					}else
					{
						$dia_cond= " and a.dia='$txt_width'";
					}

					$sql="select b.id, b.color_name from fabric_sales_order_dtls a, lib_color b where a.color_id=b.id and a.mst_id=$booking_id and a.status_active=1 and a.body_part_id=$cbo_body_part and a.determination_id=$fabric_desc_id and a.gsm_weight=$txt_gsm $dia_cond group by b.id, b.color_name";
				}
				else
				{
					$sql="select id, color_name from lib_color where status_active=1 and is_deleted=0 order by color_name";
				}

				//echo $sql;		
				$i=1;
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					//$indiv_color_id= explode(",",$selectResult[csf('color_id')]);
					
					//$color_row_ids=="";
					//foreach($indiv_color_id as $key=>$colorId) 
					//{
						//if($colorId)
						//{
							//if($color_row_ids=="") $color_row_ids=$i; else $color_row_ids.=",".$i;
						//}

					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
						<td width="50" align="center"><?php echo "$i"; ?>

							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $selectResult[csf('id')]; ?>"/>	
							<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $selectResult[csf('color_name')]; ?>"/>
						</td>	
						<td><p><?php echo $selectResult[csf('color_name')]; ?></p></td>
					</tr>
					<?

					$i++;
					//}

				}
				?>
				<input type="hidden" name="txt_color_row_id" id="txt_color_row_id" value="<?php echo $color_row_ids;?>"/>	


			</table>
		</div>
		<table width="340" cellspacing="0" cellpadding="0" border="1" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%"> 
						<div style="width:45%; float:left" align="left">
							<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data(this.checked)" /> Check / Uncheck All
						</div>
						<div style="width:55%; float:left" align="left">
							<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
						</div>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<?
	//echo create_list_view("tbl_list_search", "Color Name", "280","340","280",0, $sql , "js_set_value", "color_name", "", 1, "0", $arr , "color_name", "",'setFilterGrid("tbl_list_search",-1);','0,','',1) ;
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$brand_arr=return_library_array( "select id, brand_name from lib_brand where status_active=1 and is_deleted=0",'id','brand_name');
	//echo "10**";die;

	$cbo_floor = str_replace("'", "", $cbo_floor);
	$cbo_room = str_replace("'", "", $cbo_room);
	$txt_rack = str_replace("'", "", $txt_rack);
	$txt_shelf = str_replace("'", "", $txt_shelf);

	if($cbo_floor==""){$cbo_floor=0;}
	if($cbo_room==""){$cbo_room=0;}
	if($txt_rack==""){$txt_rack=0;}
	if($txt_shelf==""){$txt_shelf=0;}
	
	if(str_replace("'","",$update_id)!="")
	{
		$recBill=return_field_value("bill_no","inv_receive_master","id=$update_id and status_active=1 and is_deleted=0","bill_no");
		if($recBill!="")
		{
			if(str_replace("'","",$cbo_knitting_source)==1)
			{
				$inboundBill=return_field_value("bill_no","subcon_inbound_bill_mst","id='$recBill'","bill_no");
				
				if($inboundBill!="")
				{
					echo "30**In-Bound->Knitting Bill Issue.Bill No:".$inboundBill;
					die;
				}
			}
			else if(str_replace("'","",$cbo_knitting_source)==3)
			{
				$outboundBill=return_field_value("bill_no","subcon_outbound_bill_mst","id='$recBill'","bill_no");
				if($outboundBill!="")
				{
					echo "30**Out-Bound->Knitting Bill Entry[Gross].Bill No:".$outboundBill;
					die;
				}
			}
		}
	}


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
			$category_id=13; $entry_form=550; $prefix='KNFR';
		}
		else 
		{
			$category_id=14; $entry_form=550; $prefix='WVFR';
		}
		
		$variable_set_invent=return_field_value("user_given_code_status","variable_settings_inventory","company_name=$cbo_company_id and variable_list=19 and item_category_id=$category_id and status_active=1 and is_deleted=0","user_given_code_status");
		
		//---------------Check Receive control on Gate Entry according to variable settings inventory---------------------------//
		if($variable_set_invent==1)
		{
			$challan_no=str_replace("'","",$txt_receive_chal_no);
			if($challan_no!="")
			{
				$variable_set_invent=return_field_value("a.id as id"," inv_gate_in_mst a,  inv_gate_in_dtl b","a.id=b.mst_id and a.company_id=$cbo_company_id and a.challan_no='$challan_no' and b.item_category_id=$category_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","id");
				if(empty($variable_set_invent))
				{
					echo "30** This Item Not Found In Gate Entry. \n Please Gate Entry First.";disconnect($con);die;
				}
				
			}
		}
		//---------------End Check Receive control on Gate Entry---------------------------//
		
		if(str_replace("'", '',$cbo_receive_basis)==9)
		{
			$recv_qty=return_field_value("sum(grey_receive_qnty) as qty","pro_grey_prod_entry_dtls","grey_prod_dtls_id=$grey_prod_dtls_id and status_active=1 and is_deleted=0","qty")+str_replace("'","",$txt_receive_qnty);
			$prodQty=return_field_value("grey_receive_qnty as qty","pro_grey_prod_entry_dtls","id=$grey_prod_dtls_id","qty");
			if($recv_qty>$prodQty)
			{
				echo "30**Receive Qty. Exceeds Production Qty.";disconnect($con);
				die;
			}
		}
		
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
			$new_grey_recv_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,$cbo_company_id,$prefix,$entry_form,date("Y",time()),13 ));
			
				 
			$field_array="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, receive_basis, company_id, receive_date, challan_no, booking_id, booking_no, booking_without_order, store_id, location_id, knitting_source, knitting_company, buyer_id, yarn_issue_challan_no, remarks, fabric_nature, inserted_by, insert_date";
			
			$data_array="(".$id.",'".$new_grey_recv_system_id[1]."',".$new_grey_recv_system_id[2].",'".$new_grey_recv_system_id[0]."',$entry_form,$category_id,".$cbo_receive_basis.",".$cbo_company_id.",".$txt_receive_date.",".$txt_receive_chal_no.",".$txt_booking_no_id.",".$txt_booking_no.",".$booking_without_order.",".$cbo_store_name.",".$cbo_location.",".$cbo_knitting_source.",".$cbo_knitting_company.",".$cbo_buyer_name.",".$txt_yarn_issue_challan_no.",".$txt_remarks.",".$garments_nature.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			
			
			$grey_recv_num=$new_grey_recv_system_id[0];
			$grey_update_id=$id;
		}
		else
		{
			$field_array_update="receive_basis*receive_date*challan_no*booking_id*booking_no*booking_without_order*store_id*location_id*knitting_source*knitting_company*buyer_id*yarn_issue_challan_no*remarks*updated_by*update_date";
			
			$data_array_update=$cbo_receive_basis."*".$txt_receive_date."*".$txt_receive_chal_no."*".$txt_booking_no_id."*".$txt_booking_no."*".$booking_without_order."*".$cbo_store_name."*".$cbo_location."*".$cbo_knitting_source."*".$cbo_knitting_company."*".$cbo_buyer_name."*".$txt_yarn_issue_challan_no."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$grey_recv_num=str_replace("'","",$txt_recieved_id);
			$grey_update_id=str_replace("'","",$update_id);
		}
		
		
		
		if (str_replace("'", "", trim($txt_brand)) != "") {
				if (!in_array(str_replace("'", "", trim($txt_brand)),$new_array_brand)){
					$brand_id = return_id( str_replace("'", "", trim($txt_brand)), $brand_arr, "lib_brand", "id,brand_name","$entry_form");
					$new_array_brand[$brand_id]=str_replace("'", "", trim($txt_brand));
				}
				else $brand_id =  array_search(str_replace("'", "", trim($txt_brand)), $new_array_brand);
			} else $brand_id = 0;
		
		
		$cons_rate=$txt_rate; $cons_amount=$txt_amount;
		
		if(str_replace("'", '',$cbo_receive_basis)==9)
		{
			$stockData=sql_select("select current_stock, avg_rate_per_unit, stock_value from product_details_master where id=$product_id");
			//$stock= return_field_value("current_stock","product_details_master","id=$product_id");
			$stock= $stockData[0][csf('current_stock')];
			$avg_rate=$stockData[0][csf('avg_rate_per_unit')];
			$stock_value=$stockData[0][csf('stock_value')];
			
			$cur_st_qnty=$stock+str_replace("'","",$txt_receive_qnty);
			$cur_st_value=$stock_value+str_replace("'","",$cons_amount);
			
			$cur_st_rate=number_format($cur_st_value/$cur_st_qnty,$dec_place[3],'.','');
			// if Qty is zero then rate & value will be zero
			if ($cur_st_qnty<=0) 
			{
				$cur_st_rate=0;
				$cur_st_value=0;
			}
			$field_array_prod_update="current_stock*avg_rate_per_unit*stock_value";
			$data_array_prod_update=$cur_st_qnty."*".$cur_st_rate."*".$cur_st_value;
			
			$prod_id=$product_id;
		}
		else
		{
			if(str_replace("'","",$fabric_desc_id)=="") $fabric_desc_id=0;
			if(str_replace("'","",$cbo_receive_basis)==2 && str_replace("'","",$booking_without_order)==1 && str_replace("'","",$fabric_desc_id)==0)
			{
				$fabric_description=trim(str_replace("'","",$txt_fabric_description));
				$row_prod=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id and item_category_id=$category_id and detarmination_id=$fabric_desc_id and item_description='$fabric_description' and gsm=$txt_gsm and dia_width=$txt_width and status_active=1 and is_deleted=0");
			}
			else
			{
				$row_prod=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id and item_category_id=$category_id and detarmination_id=$fabric_desc_id and gsm=$txt_gsm and dia_width=$txt_width and status_active=1 and is_deleted=0");
			}
			
			if(count($row_prod)>0)
			{
				$prod_id=$row_prod[0][csf('id')];
				$stock_qnty=$row_prod[0][csf('current_stock')];
				$stockValue=$row_prod[0][csf('stock_value')];

				$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_receive_qnty);
				$stock_value=$stockValue+str_replace("'", '',$cons_amount);
				$avg_rate_per_unit=number_format($stock_value/$curr_stock_qnty,$dec_place[3],'.','');
				// if Qty is zero then rate & value will be zero
				if ($curr_stock_qnty<=0) 
				{
					$stock_value=0;
					$avg_rate_per_unit=0;
				}
				$field_array_prod_update="store_id*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*brand*lot*updated_by*update_date";
				
				$data_array_prod_update=$cbo_store_name."*".$avg_rate_per_unit."*".$txt_receive_qnty."*".$curr_stock_qnty."*".$stock_value."*".$brand_id."*".$txt_yarn_lot."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			}
			else
			{
				//$prod_id=return_next_id( "id", "product_details_master", 1 ) ;
				$prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				
				$avg_rate_per_unit=$cons_rate;
				$stock_value=$cons_amount;
				
				$prod_name_dtls=trim(str_replace("'","",$txt_fabric_description)).", ".trim(str_replace("'","",$txt_gsm)).", ".trim(str_replace("'","",$txt_width));
				$field_array_prod="id, company_id, store_id, item_category_id, detarmination_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, brand, gsm, dia_width, lot, inserted_by, insert_date";
				
				$data_array_prod="(".$prod_id.",".$cbo_company_id.",".$cbo_store_name.",$category_id,".$fabric_desc_id.",".$txt_fabric_description.",'".$prod_name_dtls."',".$cbo_uom.",".$avg_rate_per_unit.",".$txt_receive_qnty.",".$txt_receive_qnty.",".$stock_value.",".$brand_id.",".$txt_gsm.",".$txt_width.",".$txt_yarn_lot.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
		}
                
        //------------------Check Receive Date with last Transaction Date-------------------
        $max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$prod_id and store_id = $cbo_store_name and status_active=1", "max_date");      
        if($max_issue_date !="")
        {
            $max_issue_date = date("Y-m-d", strtotime($max_issue_date));
            $receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_receive_date)));

            if ($receive_date < $max_issue_date) 
            {
                echo "20**Receive Date Can not Be Less Than Last Transaction Date Of This Lot";
                disconnect($con);
                die;
            }
        }
        //-----------------------------------------------------------------------------
                
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		
		$order_rate=0; $order_amount=0; 
		
		$field_array_trans="id, mst_id, receive_basis, pi_wo_batch_no, booking_without_order, company_id, prod_id, item_category, transaction_type, transaction_date, store_id, brand_id, order_uom, order_qnty, order_rate, order_amount, cons_uom, cons_quantity, cons_reject_qnty, cons_rate, cons_amount, balance_qnty, balance_amount, floor_id, machine_id, room, rack, self, bin_box, inserted_by, insert_date";
		
		$data_array_trans="(".$id_trans.",".$grey_update_id.",".$cbo_receive_basis.",".$txt_booking_no_id.",".$booking_without_order.",".$cbo_company_id.",".$prod_id.",$category_id,1,".$txt_receive_date.",".$cbo_store_name.",".$brand_id.",".$cbo_uom.",".$txt_receive_qnty.",".$order_rate.",".$order_amount.",".$cbo_uom.",".$txt_receive_qnty.",".$txt_reject_fabric_recv_qnty.",".$cons_rate.",".$cons_amount.",".$txt_receive_qnty.",".$cons_amount.",".$cbo_floor.",".$cbo_machine_name.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		$id_dtls = return_next_id_by_sequence("PRO_GREY_PROD_DTLS_PK_SEQ", "pro_grey_prod_entry_dtls", $con);
		
		$cbo_yarn_count=explode(",",str_replace("'","",$cbo_yarn_count));
		asort($cbo_yarn_count);
		$cbo_yarn_count=implode(",",$cbo_yarn_count);
		
		$txt_yarn_lot=explode(",",str_replace("'","",$txt_yarn_lot));
		asort($txt_yarn_lot);
		$txt_yarn_lot=implode(",",$txt_yarn_lot);
		
		
		$knitting_charge_data=explode("*",str_replace("'","",$knitting_charge_string));
		$knitting_charge_taka=number_format($knitting_charge_data[0],2,".","");
		$yarn_rate_taka=number_format($knitting_charge_data[1],2,".","");
		//*********************************************************************************
		
		
		$field_array_dtls="id, mst_id, trans_id, prod_id, body_part_id, febric_description_id, gsm, width, original_gsm, original_width, no_of_roll, order_id, grey_receive_qnty, reject_fabric_receive, rate, amount, uom, yarn_lot, yarn_count, brand_id, shift_name, floor_id, machine_no_id, room, rack, self, bin_box, color_id, color_range_id, stitch_length,machine_dia, machine_gg, grey_prod_dtls_id, yarn_rate, kniting_charge,program_no, inserted_by, insert_date";
		
		$data_array_dtls="(".$id_dtls.",".$grey_update_id.",".$id_trans.",".$prod_id.",".$cbo_body_part.",".$fabric_desc_id.",".$txt_gsm.",".$txt_width.",".$txt_old_gsm.",".$txt_old_dia.",".$txt_roll_no.",".$all_po_id.",".$txt_receive_qnty.",".$txt_reject_fabric_recv_qnty.",".$cons_rate.",".$cons_amount.",".$cbo_uom.",'".$txt_yarn_lot."','".$cbo_yarn_count."',".$brand_id.",".$txt_shift_name.",".$cbo_floor.",".$cbo_machine_name.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$color_id.",".$cbo_color_range.",".$txt_stitch_length.",".$txt_machine_dia.",".$txt_machine_gg.",".$grey_prod_dtls_id.",'".$yarn_rate_taka."','".$knitting_charge_taka."',".$hidden_program_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		// ***************************************For Accounting Balance *******************************************************
		if(str_replace("'","",$cbo_receive_basis)==11)
		{
			$field_array_material="id,mst_id,dtls_id,entry_form,prod_id,item_category,used_qty,rate,amount, yarn_percentage, porcess_loss, inserted_by, insert_date,status_active, is_deleted";
			//$id_material_used = return_next_id( "id", "pro_material_used_dtls", 1 );

			$process_string=explode("__",str_replace("'","",$process_string));
			for($sl=0;$sl<count($process_string);$sl++)
			{
				$id_material_used = return_next_id_by_sequence("PRO_MATERIAL_USED_DTLS_PK_SEQ", "pro_material_used_dtls", $con);
				$product_dtls=explode("*",$process_string[$sl]);
				$used_prod_id=$product_dtls[0];
				$net_used=number_format($product_dtls[1],4,".","");
				$yarn_rate=number_format($product_dtls[2],4,".","");
				$used_amount=number_format($yarn_rate*$net_used,4,".","");
				$txt_yarn_percentage=$product_dtls[4];
				$txt_process_loss=$product_dtls[5];
				if($sl==0) $add_comma=""; else $add_comma=",";
				$data_array_material_used.="$add_comma(".$id_material_used.",".$grey_update_id.",".$id_dtls.",$entry_form,'".$used_prod_id."',1,'".$net_used."','".$yarn_rate."','".$used_amount."','".$txt_yarn_percentage."','".$txt_process_loss."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				//$id_material_used++;
			}
		}
		// ********************************Finish For Accounting Balance *******************************************************	
		
		$field_array_roll="id, barcode_year,barcode_suffix_no,barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, qc_pass_qnty, roll_no, rate ,amount, booking_no, booking_without_order, is_sales, reject_qnty, inserted_by, insert_date";
		$save_string=explode(",",str_replace("'","",$save_data));
		$save_string2=explode(",",str_replace("'","",$save_data2));
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 ); 
		
		if(str_replace("'","",$roll_maintained)==1)
		{
			$roll_arr=return_library_array("select po_breakdown_id,max(roll_no) as roll_no from pro_roll_details where entry_form in(550) group by po_breakdown_id",'po_breakdown_id','roll_no'); 
		}

		$is_sales=sql_select("select b.is_sales from inv_receive_master a, ppl_planning_info_entry_dtls b where a.booking_id=b.id and a.recv_number=$txt_booking_no");
		if($is_sales[0][csf('is_sales')]==1 || str_replace("'","",$cbo_receive_basis)==14) { $is_sales=1; } else{ $is_sales=0; }
		
		$po_array=array();$po_array2=array(); $barcode_year = date("y");
		//echo "10**";
		for($i=0;$i<count($save_string);$i++)
		{
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
			
			// ###### only barcode creation entry form 2 asign for barcode suffix
			$barcode_suffix_no = explode("*", return_next_id_by_sequence("ROLL_BARCODE_SUFFIX_NO_SEQ", "pro_roll_details",$con,1,0,'',2,date("Y",time()),13 ));
			$barcode_no=$barcode_year.$entry_form.str_pad($barcode_suffix_no[2], 7,"0",STR_PAD_LEFT);
			//print_r($barcode_suffix_no);
			//echo $barcode_no;
			$order_dtls=explode("**",$save_string[$i]);
			$order_dtls2=explode("**",$save_string2[$i]);
			
			$order_id=$order_dtls[0];
			$order_qnty_roll_wise=$order_dtls[1];
			$roll_no=$order_dtls[2];
			$roll_no=$roll_arr[$order_id]+1;
			$roll_arr[$order_id]+=1;
			//for reject
			$order_id2=$order_dtls2[0];
			$order_reject_qnty_roll_wise=$order_dtls2[1];
			
			if($data_array_roll!="") $data_array_roll.= ",";
			/*############ Note ##############
			when receive basis service booking without order booking_id = smaple booking id and booking_no=service booking without order no 
			else booking_id = booking id and booking_no=booking no
			*/

			$data_array_roll.="(".$id_roll.",".$barcode_year.",".$barcode_suffix_no[2].",".$barcode_no.",".$grey_update_id.",".$id_dtls.",'".$order_id."',$entry_form,'".$order_qnty_roll_wise."','".$order_qnty_roll_wise."','".$roll_no."',".$cons_rate.",".$cons_amount.",".$hidden_program_no.",".$booking_without_order.",".$is_sales.",'".$order_reject_qnty_roll_wise."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			
			if(array_key_exists($order_id,$po_array))
			{
				$po_array[$order_id]+=$order_qnty_roll_wise;
				$po_array2[$order_id2]+=$order_reject_qnty_roll_wise;
			}
			else
			{
				$po_array[$order_id]=$order_qnty_roll_wise;
				$po_array2[$order_id2]=$order_reject_qnty_roll_wise;
			}
		}

		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity,reject_qty, is_sales, inserted_by, insert_date";//, color_id

		foreach($po_array as $key=>$val)
		{
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$order_id=$key;
			$order_qnty=$val;
			$tot_reject_qty=$po_array2[$order_id];
			
			if($data_array_prop!="") $data_array_prop.= ",";
			$data_array_prop.="(".$id_prop.",".$id_trans.",1,$entry_form,".$id_dtls.",'".$order_id."',".$prod_id.",'".$order_qnty."','".$tot_reject_qty."',1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";//,'".$color_id."'

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
		//echo "5**insert into pro_grey_prod_entry_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;	
		$rID4=sql_insert("pro_grey_prod_entry_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		} 
		$rID5=true;
		if(str_replace("'","",$cbo_receive_basis)==11)
		{
			if($data_array_roll!="" && str_replace("'","",$roll_maintained)==1)
			{
				$rID5=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
				if($flag==1) 
				{
					if($rID5) $flag=1; else $flag=0; 
				} 
			}
		}
		else
		{
			if($data_array_roll!="" && str_replace("'","",$roll_maintained)==1 && str_replace("'","",$booking_without_order)!=1)
			{
				$rID5=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
				if($flag==1) 
				{
					if($rID5) $flag=1; else $flag=0; 
				} 
			}
		}
		
		$rID6=true;	
		if($data_array_prop!="" && str_replace("'","",$booking_without_order)!=1)
		{
			$rID6=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			if($flag==1) 
			{
				if($rID6) $flag=1; else $flag=0; 
			} 
		}
		$rID7=true;
		if($data_array_material_used!="")
		{
			$rID7=sql_insert("pro_material_used_dtls",$field_array_material,$data_array_material_used,0);
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
				echo "5**0**"."&nbsp;"."**0** insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;
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
		
		if(str_replace("'","",$garments_nature)==2)
		{
			$category_id=13; $entry_form=550; 
		}
		else 
		{
			$category_id=14; $entry_form=550;
		}
		
		$variable_set_invent=return_field_value("user_given_code_status","variable_settings_inventory","company_name=$cbo_company_id and variable_list=19 and item_category_id=$category_id","user_given_code_status");
		//---------------Check Receive control on Gate Entry according to variable settings inventory---------------------------//
		if($variable_set_invent==1)
		{
			$challan_no=str_replace("'","",$txt_receive_chal_no);
			if($challan_no!="")
			{
				$variable_set_invent=return_field_value("a.id as id"," inv_gate_in_mst a,  inv_gate_in_dtl b","a.id=b.mst_id and a.company_id=$cbo_company_id and a.challan_no='$challan_no' and b.item_category_id=$category_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","id");
				if(empty($variable_set_invent))
				{
					echo "30** This Item Not Found In Gate Entry. \n Please Gate Entry First.";disconnect($con);die;
				}
			}
		}
		//---------------End Check Receive control on Gate Entry---------------------------//
		
		if(str_replace("'", '',$cbo_receive_basis)==9)
		{
			$recv_qty=return_field_value("sum(grey_receive_qnty) as qty","pro_grey_prod_entry_dtls","grey_prod_dtls_id=$grey_prod_dtls_id and id!=$update_dtls_id and status_active=1 and is_deleted=0","qty")+str_replace("'","",$txt_receive_qnty);
			$prodQty=return_field_value("grey_receive_qnty as qty","pro_grey_prod_entry_dtls","id=$grey_prod_dtls_id","qty");
			if($recv_qty>$prodQty)
			{
				echo "30**Receive Qty. Exceeds Production Qty.";disconnect($con);
				die;
			}
		}
		
		/*#### Stop not eligible field from update operation start ####*/
		//receive_basis*knitting_source*knitting_company*location_id*
		//$cbo_receive_basis."*".$cbo_knitting_source."*".$cbo_knitting_company."*".$cbo_location."*".
		/*#### Stop not eligible field from update operation end ####*/
		
		$field_array_update="receive_date*challan_no*booking_id*booking_no*booking_without_order*store_id*buyer_id*yarn_issue_challan_no*remarks*updated_by*update_date";

		$data_array_update=$txt_receive_date."*".$txt_receive_chal_no."*".$txt_booking_no_id."*".$txt_booking_no."*".$booking_without_order."*".$cbo_store_name."*".$cbo_buyer_name."*".$txt_yarn_issue_challan_no."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		
		$cons_rate=$txt_rate; $cons_amount=$txt_amount;
		
		//$stock= return_field_value("current_stock","product_details_master","id=$previous_prod_id");
		$stockData=sql_select("select current_stock, avg_rate_per_unit, stock_value from product_details_master where id=$previous_prod_id");
		$stock= $stockData[0][csf('current_stock')];
		$avgRate=$stockData[0][csf('avg_rate_per_unit')];
		$stockValue=$stockData[0][csf('stock_value')];
		
		
		
		//$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
		
		if (str_replace("'", "", trim($txt_brand)) != "") {
			if (!in_array(str_replace("'", "", trim($txt_brand)),$new_array_brand)){
				$brand_id = return_id( str_replace("'", "", trim($txt_brand)), $brand_arr, "lib_brand", "id,brand_name","$entry_form");
				$new_array_brand[$brand_id]=str_replace("'", "", trim($txt_brand));
			}
			else $brand_id =  array_search(str_replace("'", "", trim($txt_brand)), $new_array_brand);
		} else $brand_id = 0;
		
		
		
		if(str_replace("'", '',$cbo_receive_basis)==9)
		{
			if(str_replace("'","",$product_id)==str_replace("'","",$previous_prod_id))
			{
				$cur_st_qnty=$stock+str_replace("'", '',$txt_receive_qnty)-str_replace("'", '',$hidden_receive_qnty);
				$cur_st_value=$stockValue+str_replace("'", '',$cons_amount)-str_replace("'", '',$hidden_receive_amnt);
				$cur_st_rate=number_format($cur_st_value/$cur_st_qnty,$dec_place[3],'.','');
				
				// if Qty is zero then rate & value will be zero
				if ($cur_st_qnty<=0) 
				{
					$cur_st_rate=0;
					$cur_st_value=0;
				}

				$field_array_prod_update="current_stock*avg_rate_per_unit*stock_value";
				$data_array_prod_update=$cur_st_qnty."*".$cur_st_rate."*".$cur_st_value;
				
				if($cur_st_qnty<0)
				{
					echo "30**Stock cannot be less than zero.";disconnect($con);die;
				}
				
			}
			else
			{
				$adjust_curr_stock=$stock-str_replace("'", '',$hidden_receive_qnty);
				$adj_cur_st_value=$stockValue-str_replace("'", '',$hidden_receive_amnt); 
				$adj_cur_st_rate=number_format($adj_cur_st_value/$adjust_curr_stock,$dec_place[3],'.','');
				
				// if Qty is zero then rate & value will be zero
				if ($adjust_curr_stock<=0) 
				{
					$adj_cur_st_rate=0;
					$adj_cur_st_value=0;
				}

				$field_array_adjust="current_stock*avg_rate_per_unit*stock_value";
				$data_array_adjust=$adjust_curr_stock."*".$adj_cur_st_rate."*".$adj_cur_st_value;
				
				if($adjust_curr_stock<0)
				{
					echo "30**Stock cannot be less than zero.";disconnect($con);die;
				}
				 
				
				//$current_stock= return_field_value("current_stock","product_details_master","id=$product_id");
				$currStockData=sql_select("select current_stock, avg_rate_per_unit, stock_value from product_details_master where id=$product_id");
				$current_stock=$currStockData[0][csf('current_stock')];
				$current_stock_value=$currStockData[0][csf('stock_value')];
				
				
				$cur_st_qnty=$current_stock+str_replace("'", '',$txt_receive_qnty);
				$cur_st_value=$current_stock_value+str_replace("'", '',$cons_amount);
				$cur_st_rate=number_format($cur_st_value/$cur_st_qnty,$dec_place[3],'.','');
				// if Qty is zero then rate & value will be zero
				if ($cur_st_qnty<=0) 
				{
					$cur_st_value=0;
					$cur_st_rate=0;
				}
				$field_array_prod_update="current_stock*avg_rate_per_unit*stock_value";
				$data_array_prod_update=$cur_st_qnty."*".$cur_st_rate."*".$cur_st_value;
				
				
			}
			$prod_id=$product_id;
		}
		else
		{
			if(str_replace("'","",$fabric_desc_id)=="") $fabric_desc_id=0;
			if(str_replace("'","",$cbo_receive_basis)==2 && str_replace("'","",$booking_without_order)==1 && str_replace("'","",$fabric_desc_id)==0)
			{
				$fabric_description=trim(str_replace("'","",$txt_fabric_description));
				$row_prod=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id and item_category_id=$category_id and detarmination_id=$fabric_desc_id and item_description='$fabric_description' and gsm=$txt_gsm and dia_width=$txt_width and status_active=1 and is_deleted=0");
			}
			else
			{
				$row_prod=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id and item_category_id=$category_id and detarmination_id=$fabric_desc_id and gsm=$txt_gsm and dia_width=$txt_width and status_active=1 and is_deleted=0");
			}
			
			if(count($row_prod)>0)
			{
				$prod_id=$row_prod[0][csf('id')];
				if($prod_id==str_replace("'","",$previous_prod_id))
				{
					$stock_qnty=$row_prod[0][csf('current_stock')];
					$stockValue=$row_prod[0][csf('stock_value')];
					
					$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_receive_qnty)-str_replace("'", '',$hidden_receive_qnty);
					$stock_value=$stockValue+str_replace("'", '',$cons_amount)-str_replace("'", '',$hidden_receive_amnt);
					$avg_rate_per_unit=number_format($stock_value/$curr_stock_qnty,$dec_place[3],'.','');
					
					// if Qty is zero then rate & value will be zero
					if ($curr_stock_qnty<=0) 
					{
						$stock_value=0;
						$avg_rate_per_unit=0;
					}

					$field_array_prod_update="store_id*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*brand*lot*updated_by*update_date";
					$data_array_prod_update=$cbo_store_name."*".$avg_rate_per_unit."*".$txt_receive_qnty."*".$curr_stock_qnty."*".$stock_value."*".$brand_id."*".$txt_yarn_lot."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
					
					if($curr_stock_qnty<0)
					{
						echo "30**Stock cannot be less than zero.";disconnect($con);die;
					}
					/*$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,0);
					if($flag==1) 
					{
						if($rID2) $flag=1; else $flag=0; 
					}*/
				}
				else
				{
					$adjust_curr_stock=$stock-str_replace("'", '',$hidden_receive_qnty);
					$adj_cur_st_value=$stockValue-str_replace("'", '',$hidden_receive_amnt); 
					$adj_cur_st_rate=number_format($adj_cur_st_value/$adjust_curr_stock,$dec_place[3],'.','');
					// if Qty is zero then rate & value will be zero
					if ($adjust_curr_stock<=0) 
					{
						$adj_cur_st_value=0;
						$adj_cur_st_rate=0;
					}
					$field_array_adjust="current_stock*avg_rate_per_unit*stock_value";
					$data_array_adjust=$adjust_curr_stock."*".$adj_cur_st_rate."*".$adj_cur_st_value;
					
					if($adjust_curr_stock<0)
					{
						echo "30**Stock cannot be less than zero.";disconnect($con);die;
					}
					/*$rID_adjust=sql_update("product_details_master",$field_array_adjust,$data_array_adjust,"id",$previous_prod_id,0);
					if($flag==1) 
					{
						if($rID_adjust) $flag=1; else $flag=0; 
					} */
					
					$stock_qnty=$row_prod[0][csf('current_stock')];
					$current_stock_value=$row_prod[0][csf('stock_value')];
					
					$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_receive_qnty);
					$stock_value=$current_stock_value+str_replace("'", '',$cons_amount);
					$avg_rate_per_unit=number_format($stock_value/$curr_stock_qnty,$dec_place[3],'.','');
					// if Qty is zero then rate & value will be zero
					if ($curr_stock_qnty<=0) 
					{
						$stock_value=0;
						$avg_rate_per_unit=0;
					}

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
				$adj_cur_st_value=$stockValue-str_replace("'", '',$hidden_receive_amnt); 
				$adj_cur_st_rate=number_format($adj_cur_st_value/$adjust_curr_stock,$dec_place[3],'.','');
				
				// if Qty is zero then rate & value will be zero
				if ($adjust_curr_stock<=0) 
				{
					$adj_cur_st_value=0;
					$adj_cur_st_rate=0;
				}
				
				$field_array_adjust="current_stock*avg_rate_per_unit*stock_value";
				$data_array_adjust=$adjust_curr_stock."*".$adj_cur_st_rate."*".$adj_cur_st_value;
				
				if($adjust_curr_stock<0)
				{
					echo "30**Stock cannot be less than zero.";disconnect($con);die;
				}
				/*$rID_adjust=sql_update("product_details_master",$field_array_adjust,$data_array_adjust,"id",$previous_prod_id,0);
				if($flag==1) 
				{
					if($rID_adjust) $flag=1; else $flag=0; 
				} */
				
				//$prod_id=return_next_id( "id", "product_details_master", 1 ) ;
				$prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				$avg_rate_per_unit=$cons_rate; 
				$stock_value=$cons_amount;
				
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
		
         //------------------Check Receive Date with last Issue Date-------------------
        $max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$prod_id and store_id=$cbo_store_name and status_active=1 and id <> $update_trans_id", "max_date");      
        if($max_issue_date !="")
        {
            $max_issue_date = date("Y-m-d", strtotime($max_issue_date));
            $receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_receive_date)));

            if ($receive_date < $max_issue_date) 
            {
                echo "20**Receive Date Can not Be Less Than Last Transaction Date Of This Lot";
                disconnect($con);
                die;
            }
        }
        //-----------------------------------------------------------------------------
                
		$order_rate=0; $order_amount=0; 
		$sqlBl = sql_select("select cons_quantity,cons_amount,balance_qnty,balance_amount from inv_transaction where id=$update_trans_id");
		$before_receive_qnty	= $sqlBl[0][csf("cons_quantity")]; 
		$beforeAmount			= $sqlBl[0][csf("cons_amount")];
		$beforeBalanceQnty		= $sqlBl[0][csf("balance_qnty")]; 
		$beforeBalanceAmount	= $sqlBl[0][csf("balance_amount")];
		
		$adjBalanceQnty		=$beforeBalanceQnty-$before_receive_qnty+str_replace("'", '',$txt_receive_qnty);
		$adjBalanceAmount	=$beforeBalanceAmount-$beforeAmount+$con_amount; 
		
		$field_array_trans_update="receive_basis*pi_wo_batch_no*prod_id*transaction_date*store_id*brand_id*order_qnty*order_rate*order_amount*cons_quantity*cons_reject_qnty*cons_rate*cons_amount*balance_qnty*balance_amount*floor_id*machine_id*room*rack*self*bin_box*updated_by*update_date";
		
		$data_array_trans_update=$cbo_receive_basis."*".$txt_booking_no_id."*".$prod_id."*".$txt_receive_date."*".$cbo_store_name."*".$brand_id."*".$txt_receive_qnty."*".$order_rate."*".$order_amount."*".$txt_receive_qnty."*".$txt_reject_fabric_recv_qnty."*".$cons_rate."*".$cons_amount."*".$adjBalanceQnty."*".$adjBalanceAmount."*".$cbo_floor."*".$cbo_machine_name."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
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
		//$rate=0; $amount=0;
		
		// ******** Add For Accountce **************************************************
		
		$knitting_charge_data=explode("*",str_replace("'","",$knitting_charge_string));
		$knitting_charge_taka=number_format($knitting_charge_data[0],2,".","");
		$yarn_rate_taka=number_format($knitting_charge_data[1],2,".","");
		$material_deleted_id=$knitting_charge_data[5];
		//*********************************************************************************
		
		$field_array_dtls_update="prod_id*body_part_id*febric_description_id*gsm*width*original_gsm*original_width*no_of_roll*order_id*grey_receive_qnty*reject_fabric_receive*rate*amount*uom*yarn_lot*yarn_count*brand_id*shift_name*floor_id*machine_no_id*room*rack*self*bin_box*color_id*color_range_id*stitch_length*machine_dia*machine_gg*grey_prod_dtls_id*yarn_rate*kniting_charge*program_no*updated_by*update_date";
		
		$data_array_dtls_update=$prod_id."*".$cbo_body_part."*".$fabric_desc_id."*".$txt_gsm."*".$txt_width."*".$txt_old_gsm."*".$txt_old_dia."*".$txt_roll_no."*".$all_po_id."*".$txt_receive_qnty."*".$txt_reject_fabric_recv_qnty."*".$cons_rate."*".$cons_amount."*".$cbo_uom."*'".$txt_yarn_lot."'*'".$cbo_yarn_count."'*".$brand_id."*".$txt_shift_name."*".$cbo_floor."*".$cbo_machine_name."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$color_id."*".$cbo_color_range."*".$txt_stitch_length."*".$txt_machine_dia."*".$txt_machine_gg."*".$grey_prod_dtls_id."*'".$yarn_rate_taka."'*'".$knitting_charge_taka."'*".$hidden_program_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		
		//****************************** for account Process rate  ************************************************************
		
		if(str_replace("'","",$cbo_receive_basis)==11)
		{
			
			if(str_replace("'","",$process_string)!="")
			{
				$process_string=explode("__",str_replace("'","",$process_string));
				$field_array_material="id,mst_id,dtls_id,entry_form,prod_id,item_category,used_qty,rate,amount,yarn_percentage, porcess_loss, inserted_by, insert_date,
				status_active, is_deleted";
				$field_array_material_update="used_qty*rate*amount*yarn_percentage*porcess_loss*updated_by*update_date";
				//$id_material_used = return_next_id( "id", "pro_material_used_dtls", 1 );
				for($sl=0;$sl<count($process_string);$sl++)
				{
					$id_material_used = return_next_id_by_sequence("PRO_MATERIAL_USED_DTLS_PK_SEQ", "pro_material_used_dtls", $con);
					$product_dtls=explode("*",$process_string[$sl]);
					$used_prod_id=$product_dtls[0];
					$net_used=number_format($product_dtls[1],4,".","");
					$yarn_rate=number_format($product_dtls[2],4,".","");
					$used_amount=number_format($yarn_rate*$net_used,4,".","");
					$material_update_id=$product_dtls[3];
					$txt_yarn_percentage=$product_dtls[4];
					$txt_process_loss=$product_dtls[5];
					if($material_update_id>0)
					{
						$material_id_arr[]=$material_update_id;
						$material_data_array_update[$material_update_id]=explode("*",("'".$net_used."'*'".$yarn_rate."'* '".$used_amount."'*'".$txt_yarn_percentage."'* '".$txt_process_loss."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					}
					else
					{
						if(str_replace("'","",$data_array_material_used)=="") $add_comma=""; else $add_comma=",";
						$data_array_material_used.="$add_comma(".$id_material_used.",".$update_id.",".$update_dtls_id.",".$entry_form.",".$used_prod_id.",1,'".$net_used."','".$yarn_rate."','".$used_amount."','".$txt_yarn_percentage."','".$txt_process_loss."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
						//$id_material_used++;
					}
				}
			}
		}
		//		echo "10**".$data_array_material_used;die;
		
		$barcode_year=date("y");  
		//$barcode_suffix_no=return_field_value("max(barcode_suffix_no) as suffix_no","pro_roll_details","barcode_year=$barcode_year","suffix_no")+1;// and entry_form=$entry_form
		//$barcode_no=$barcode_year.$entry_form.str_pad($barcode_suffix_no,7,"0",STR_PAD_LEFT);
		
		$field_array_roll="id, barcode_year,barcode_suffix_no,barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,qc_pass_qnty, roll_no, rate ,amount,booking_no, booking_without_order, is_sales, reject_qnty, inserted_by, insert_date";
		$field_array_roll_update="po_breakdown_id*qnty*qc_pass_qnty*roll_no*rate*amount*booking_no*is_sales*reject_qnty*updated_by*update_date";
		$save_string=explode(",",str_replace("'","",$save_data));
		$save_string2=explode(",",str_replace("'","",$save_data2));
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		
		if(str_replace("'","",$roll_maintained)==1)
		{
			$roll_arr=return_library_array("select po_breakdown_id,max(roll_no) as roll_no from pro_roll_details where entry_form in(550) group by po_breakdown_id",'po_breakdown_id','roll_no');
		}

		$is_sales=sql_select("select b.is_sales from inv_receive_master a, ppl_planning_info_entry_dtls b where a.booking_id=b.id and a.recv_number=$txt_booking_no");
		if($is_sales[0][csf('is_sales')]==1 || str_replace("'", '',$cbo_receive_basis)==14) { $is_sales=1; } else{ $is_sales=0; } 

		$po_array=array(); $po_array2=array();
		for($i=0;$i<count($save_string);$i++)
		{
			
			$order_dtls=explode("**",$save_string[$i]);
			$order_dtls2=explode("**",$save_string2[$i]);
			
			$order_id=$order_dtls[0];
			$order_qnty_roll_wise=$order_dtls[1];
			$roll_no=$order_dtls[2];
			$roll_not_delete_id=$order_dtls[3];
			$roll_id=$order_dtls[4];
			
			if($roll_no=="")
			{
				$roll_no=$roll_arr[$order_id]+1;
				$roll_arr[$order_id]+=1;
			}
			//for reject Qty
			
			$order_id2=$order_dtls2[0];
			$order_reject_qnty_roll_wise=$order_dtls2[1];
			
			if($roll_id=="" || $roll_id==0)
			{
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				
				// ###### only barcode creation entry form 2 asign for barcode suffix
				$barcode_suffix_no = explode("*", return_next_id_by_sequence("ROLL_BARCODE_SUFFIX_NO_SEQ", "pro_roll_details",$con,1,0,'',2,date("Y",time()),13 ));
				//$new_grey_recv_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,$cbo_company_id,'GPE',2,date("Y",time()),13 ));
				$barcode_no=$barcode_year.$entry_form.str_pad($barcode_suffix_no[2], 7,"0",STR_PAD_LEFT);
				
				if($data_array_roll!="") $data_array_roll .= ",";
				$data_array_roll.="(".$id_roll.",".$barcode_year.",".$barcode_suffix_no[2].",".$barcode_no.",".$update_id.",".$update_dtls_id.",'".$order_id."',$entry_form,'".$order_qnty_roll_wise."','".$order_qnty_roll_wise."','".$roll_no."', ".$cons_rate.",".$cons_amount.",".$hidden_program_no.",".$booking_without_order.",".$is_sales.",'".$order_reject_qnty_roll_wise."', ".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
			else
			{
				$roll_id_arr[]=$roll_id;
				$roll_data_array_update[$roll_id]=explode("*",($order_id."*'".$order_qnty_roll_wise."'*'".$order_qnty_roll_wise."'*'".$roll_no."'*".$cons_rate."*".$cons_amount."*".$hidden_program_no."*".$is_sales."*'".$order_reject_qnty_roll_wise."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			
			if(array_key_exists($order_id,$po_array))
			{
				$po_array[$order_id]+=$order_qnty_roll_wise;
				$po_array2[$order_id2]+=$order_reject_qnty_roll_wise;
			}
			else
			{
				$po_array[$order_id]=$order_qnty_roll_wise;
				$po_array2[$order_id2]=$order_reject_qnty_roll_wise;
			}
		}
		
		//echo "insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, reject_qty, is_sales, inserted_by, insert_date";//, color_id
		    	
		foreach($po_array as $key=>$val)
		{
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$order_id=$key;
			$order_qnty=$val;
			$tot_reject_qty=$po_array2[$order_id];
			
			if($data_array_prop!="") $data_array_prop.= ",";
			$data_array_prop.="(".$id_prop.",".$update_trans_id.",1,$entry_form,".$update_dtls_id.",'".$order_id."',".$prod_id.",'".$order_qnty."','".$tot_reject_qty."',1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";//,'".$color_id."'
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
		
		
		if(str_replace("'","",$cbo_receive_basis)==11)
		{
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
					//echo "10**".bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_update, $roll_data_array_update, $roll_id_arr );die;
					$rollUpdate=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_update, $roll_data_array_update, $roll_id_arr ));
					if($flag==1)
					{
						if($rollUpdate) $flag=1; else $flag=0;
					}
				}
				
				if($data_array_roll!="")
				{
					$rID6=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
					if($flag==1) 
					{
						if($rID6) $flag=1; else $flag=0; 
					} 
				}
			}
		}
		else
		{
			if(str_replace("'","",$roll_maintained)==1 && str_replace("'","",$booking_without_order)!=1)
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
					//echo "10**".bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_update, $roll_data_array_update, $roll_id_arr );die;
					$rollUpdate=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_update, $roll_data_array_update, $roll_id_arr ));
					if($flag==1)
					{
						if($rollUpdate) $flag=1; else $flag=0;
					}
				}
				
				if($data_array_roll!="")
				{
					$rID6=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
					if($flag==1) 
					{
						if($rID6) $flag=1; else $flag=0; 
					} 
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
		

		if(str_replace("'","",$cbo_receive_basis)==11)
		{
			if(count($material_data_array_update)>0)
			{
				//echo bulk_update_sql_statement( "pro_material_used_dtls", "id", $field_array_material_update, $material_data_array_update, $material_id_arr );
				$materialUpdate=execute_query(bulk_update_sql_statement( "pro_material_used_dtls", "id", $field_array_material_update, $material_data_array_update, $material_id_arr ));
				if($flag==1)
				{
					if($materialUpdate) $flag=1; else $flag=0;
				}
			}
			
			if($data_array_material_used!="")
			{
			//echo "10**insert into pro_material_used_dtls (".$field_array_material.") values ".$data_array_material_used;die;	
				$rID8=sql_insert("pro_material_used_dtls",$field_array_material,$data_array_material_used,0);
				if($flag==1) 
				{
					if($rID8) $flag=1; else $flag=0; 
				} 
			}
			//echo "10**".$flag;die;
			if($material_deleted_id!="")
			{
				$deletedMaterial=execute_query( "delete from pro_material_used_dtls where id in($material_deleted_id) ",0);
				if($flag==1) 
				{
					if($deletedMaterial) $flag=1; else $flag=0; 
				} 
			}
		}
		
		// echo "10**".$flag;die;
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
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_recieved_id)."**0**".str_replace("'", '', $update_dtls_id)."**".str_replace("'","",$roll_maintained)."==1 && ".str_replace("'","",$booking_without_order)."!=1 **".$data_array_roll;
			}
			else
			{
				oci_rollback($con);
				echo "6**0**0**1** insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;;
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

		function js_set_value(id,posted_account)
		{
			$('#hidden_recv_id').val(id);
			$('#hidden_posted_account').val(posted_account);
			parent.emailwindow.hide();
		}

	</script>

</head>

<body>
	<div align="center" style="width:970px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:965px; margin-left:5px">
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
							<input type="hidden" name="hidden_posted_account" id="hidden_posted_account" class="text_boxes" value=""> 
						</th> 
					</thead>
					<tr class="general">
						<td align="center">
							<?
							echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] ); 
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
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+<? echo $garments_nature; ?>, 'create_grey_recv_search_list_view', 'search_div', 'woven_grey_fabric_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
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
exit();
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
	
	$entry_form=550;
	
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

	$buyer_cond = "";
	if(!empty($buyer_id))
	{
		$buyer_cond = " and buyer_id = $buyer_id ";
	}
	
	$sql = "select id, recv_number_prefix_num, recv_number, booking_no, buyer_id, location_id, knitting_source, knitting_company, receive_date, challan_no, $year_field as year, is_posted_account from inv_receive_master where entry_form=$entry_form and fabric_nature=$garments_nature and status_active=1 and is_deleted=0 and company_id=$company_id $buyer_cond $search_field_cond $date_cond order by id DESC"; 
	//echo $sql;//die;
	$result = sql_select($sql);
	
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$company_arr = return_library_array("select id, company_name from lib_company","id","company_name");
	$location_arr = return_library_array("select id, location_name from lib_location","id","location_name");
	$buyer_arr = return_library_array("select id, short_name from lib_buyer","id","short_name");
	$grey_recv_arr=return_library_array( "select mst_id, sum(grey_receive_qnty) as recv from pro_grey_prod_entry_dtls where status_active=1 and is_deleted=0 group by mst_id",'mst_id','recv');
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="940" class="rpt_table">
		<thead>
			<th width="35">SL</th>
			<th width="55">Year</th>
			<th width="65">Received ID</th>
			<th width="110">Booking/PI /Production No</th>   
			<th width="115">Location</th>             
			<th width="115">Source</th>
			<th width="120">Supplier Name</th>
			<th width="80">Receive date</th>
			<th width="80">Receive Qnty</th>
			<th width="70">Challan No</th>
			<th>Buyer</th>
		</thead>
	</table>
	<div style="width:960px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="940" class="rpt_table" id="tbl_list_search">  
			<?
			$i=1;
			foreach ($result as $row)
			{  
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				 
				if($row[csf('knitting_source')]==1) $knit_comp=$company_arr[$row[csf('knitting_company')]]; else $knit_comp=$supplier_arr[$row[csf('knitting_company')]];
				
				$recv_qnty=$grey_recv_arr[$row[csf('id')]];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('is_posted_account')]; ?>');"> 
					<td width="35"><? echo $i; ?></td>
					<td width="55" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="65"><p>&nbsp;<? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
					<td width="110"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>     
					<td width="115"><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>          
					<td width="115"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
					<td width="120"><p><? echo $knit_comp; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
					<td width="80" align="right"><? echo number_format($recv_qnty,2,'.',''); ?>&nbsp;</td>
					<td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
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
	$data=explode("**",$data);
	$process_costing_maintain=$data[1];
	$data_array=sql_select("select id, recv_number, company_id, receive_basis, booking_id, booking_no, booking_without_order, buyer_id, store_id, location_id, knitting_source, knitting_company, receive_date, challan_no, yarn_issue_challan_no, remarks from inv_receive_master where id='$data[0]'");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_recieved_id').value 				= '".$row[csf("recv_number")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_receive_basis').value 			= '".$row[csf("receive_basis")]."';\n";

		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		echo "set_receive_basis();\n";

		echo "$('#cbo_receive_basis').attr('disabled','true');\n";
		
		
		echo "document.getElementById('txt_receive_date').value 			= '".change_date_format($row[csf("receive_date")])."';\n";
		echo "document.getElementById('txt_receive_chal_no').value 			= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_booking_no').value 				= '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('txt_booking_no_id').value 			= '".$row[csf("booking_id")]."';\n";
		echo "document.getElementById('booking_without_order').value 		= '".$row[csf("booking_without_order")]."';\n";
		echo "$('#txt_booking_no').attr('disabled','true');\n";
		if($row[csf("receive_basis")]==11)
		{
			echo "$('#txt_receive_qnty').attr('readonly','readonly');\n";
			echo "$('#txt_receive_qnty').attr('onClick','openmypage_po();');\n";	
			echo "$('#txt_receive_qnty').attr('placeholder','Single Click');\n";
		}
		else
		{
			if($row[csf("booking_without_order")]==1)
			{
				echo "$('#txt_receive_qnty').removeAttr('readonly','readonly');\n";
				echo "$('#txt_receive_qnty').removeAttr('onClick','onClick');\n";	
				echo "$('#txt_receive_qnty').removeAttr('placeholder','placeholder');\n";
				if($process_costing_maintain==1)
				{
					echo "$('#txt_receive_qnty').attr('onkeyup','calculate_amount();');\n";
				}

			}
			else
			{
				echo "$('#txt_receive_qnty').attr('readonly','readonly');\n";
				echo "$('#txt_receive_qnty').attr('onClick','openmypage_po();');\n";	
				echo "$('#txt_receive_qnty').attr('placeholder','Single Click');\n";	
			}
		}
		
		if($row[csf("receive_basis")]==14)
		{
			echo "load_drop_down('requires/woven_grey_fabric_receive_controller','". $row[csf("buyer_id")] ."', 'load_drop_down_company_in_buyer_td','buyer_td_id');\n";
		}
		echo "document.getElementById('cbo_buyer_name').value 				= '".$row[csf("buyer_id")]."';\n";

		echo "load_room_rack_self_bin('requires/woven_grey_fabric_receive_controller*13', 'store','store_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."',this.value);\n";

		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("store_id")]."';\n";
		echo "document.getElementById('cbo_knitting_source').value 			= '".$row[csf("knitting_source")]."';\n";
		echo "$('#cbo_store_name').attr('disabled','true');\n";
		echo "$('#cbo_knitting_source').attr('disabled','true');\n";
		
		echo "load_drop_down( 'requires/woven_grey_fabric_receive_controller', ".$row[csf("knitting_source")]."+'_'+".$row[csf("company_id")].", 'load_drop_down_knitting_com','knitting_com');\n";
		
		$job_no='';
		if(($row[csf("receive_basis")]==2 || $row[csf("receive_basis")]==11) && $row[csf("booking_without_order")]==0)
		{
			$job_no=return_field_value("job_no","wo_booking_mst","id='".$row[csf("booking_id")]."'");
		}
		else if($row[csf("receive_basis")]==9)
		{
			$prodData=sql_select("select receive_basis, booking_id, booking_without_order from inv_receive_master where id='".$row[csf("booking_id")]."'");
			$receive_basis=$prodData[0][csf('receive_basis')];
			$booking_plan_id=$prodData[0][csf('booking_id')];
			$booking_without_order=$prodData[0][csf('booking_without_order')];
			
			if($receive_basis==1 && $booking_without_order==0)
			{
				$job_no=return_field_value("job_no","wo_booking_mst","id='".$booking_plan_id."'");
			}
			else if($receive_basis==2)
			{
				$job_no=return_field_value("c.job_no as job_no","ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, wo_booking_mst c","a.id=b.mst_id and a.booking_no=c.booking_no and b.id='".$booking_plan_id."'","job_no");
			}
		}
		
		echo "document.getElementById('cbo_knitting_company').value 		= '".$row[csf("knitting_company")]."';\n";


		echo "load_location();\n";
		echo "document.getElementById('cbo_location').value 			= '".$row[csf("location_id")]."';\n";
		echo "load_floor();\n";
		echo "document.getElementById('txt_yarn_issue_challan_no').value 	= '".$row[csf("yarn_issue_challan_no")]."';\n";
		echo "document.getElementById('txt_job_no').value 					= '".$job_no."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('update_id').value 					= '".$row[csf("id")]."';\n";

		echo "$('#cbo_knitting_company').attr('disabled','true');\n";
		echo "$('#cbo_location').attr('disabled','true');\n";
		
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_grey_fabric_receive',1);\n";  
		exit();
	}
}

if($action=="show_grey_prod_listview")
{
	$brand_arr=return_library_array( "select id, brand_name from lib_brand where status_active=1 and is_deleted=0",'id','brand_name');
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");
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
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_data_dtls_part(<? echo $row[csf('id')]; ?>,'populate_grey_details_form_data', 'requires/woven_grey_fabric_receive_controller');"> 
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
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$brand_arr=return_library_array( "select id, brand_name from lib_brand where status_active=1 and is_deleted=0",'id','brand_name');
	
	$data=explode("**",$data);
	$id=$data[0];
	$roll_maintained=$data[1];
	$fabric_nature=$data[2];
	$receive_basis=$data[3];
	$company_id = $data[4];
	
	$entry_form=550; 
	

	$over_receive = 0;
	$recieved_qty_with_percentage = 0;

	$variable_set_invent= sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$company_id and variable_list=23 and category = 13 order by id");
	$over_receive = !empty($variable_set_invent)?$variable_set_invent[0][csf('over_rcv_percent')]:0;
	
	//$data_array=sql_select("select id,mst_id, body_part_id, trans_id,	prod_id, febric_description_id, no_of_roll, gsm, width, grey_receive_qnty, reject_fabric_receive, uom, yarn_lot, yarn_count, brand_id, shift_name, floor_id, machine_no_id, order_id, room, rack, self, bin_box, color_id, color_range_id, stitch_length, machine_dia, machine_gg, grey_prod_dtls_id, rate, amount,yarn_rate,kniting_charge,program_no from pro_grey_prod_entry_dtls where id='$id'");

	$data_array=sql_select("SELECT a.booking_no, b.id,b.mst_id, b.body_part_id, b.trans_id, b.prod_id, b.febric_description_id, b.no_of_roll, b.gsm, b.width, b.original_gsm, b.original_width, b.grey_receive_qnty, b.reject_fabric_receive, b.uom, b.yarn_lot, b.yarn_count, b.brand_id, b.shift_name, b.floor_id, b.machine_no_id, b.order_id, b.room, b.rack, b.self, b.bin_box, b.color_id, b.color_range_id, b.stitch_length, b.machine_dia, b.machine_gg, b.grey_prod_dtls_id, b.rate, b.amount,b.yarn_rate, b.kniting_charge, b.program_no from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and b.id='$id'");
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
		
		$color='';
		$color_id=explode(",",$row[csf('color_id')]);
		foreach($color_id as $val)
		{
			if($val>0) $color.=$color_arr[$val].",";
		}
		$color=chop($color,',');
		
		echo "document.getElementById('hidden_program_no').value 			= '".$row[csf("program_no")]."';\n";
		echo "document.getElementById('cbo_body_part').value 				= '".$row[csf("body_part_id")]."';\n";
		echo "document.getElementById('txt_fabric_description').value 		= '".$comp."';\n";
		echo "document.getElementById('fabric_desc_id').value 				= '".$row[csf("febric_description_id")]."';\n";
		echo "document.getElementById('txt_gsm').value 						= '".$row[csf("gsm")]."';\n";
		echo "document.getElementById('txt_width').value 					= '".$row[csf("width")]."';\n";
		echo "document.getElementById('txt_old_gsm').value 						= '".$row[csf("original_gsm")]."';\n";
		echo "document.getElementById('txt_old_dia').value 					= '".$row[csf("original_width")]."';\n";
		echo "document.getElementById('txt_roll_no').value 					= '".$row[csf("no_of_roll")]."';\n";
		echo "document.getElementById('txt_color').value 					= '".$color."';\n"; 
		echo "document.getElementById('color_id').value 					= '".$row[csf("color_id")]."';\n";   
		echo "document.getElementById('txt_stitch_length').value 			= '".$row[csf("stitch_length")]."';\n";
		echo "document.getElementById('txt_machine_dia').value 				= '".$row[csf("machine_dia")]."';\n";
		echo "document.getElementById('txt_machine_gg').value 				= '".$row[csf("machine_gg")]."';\n";
		echo "document.getElementById('cbo_color_range').value 				= '".$row[csf("color_range_id")]."';\n";
		echo "document.getElementById('txt_receive_qnty').value 			= '".$row[csf("grey_receive_qnty")]."';\n";
		echo "document.getElementById('txt_reject_fabric_recv_qnty').value 	= '".$row[csf("reject_fabric_receive")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("uom")]."';\n";
		echo "document.getElementById('txt_yarn_lot').value 				= '".$row[csf("yarn_lot")]."';\n";
		echo "document.getElementById('cbo_yarn_count').value 				= '".$row[csf("yarn_count")]."';\n";
		echo "set_multiselect('cbo_yarn_count','0','1','".$row[csf('yarn_count')]."','0');\n";
		echo "document.getElementById('txt_brand').value 					= '".$brand_arr[$row[csf("brand_id")]]."';\n";
		echo "document.getElementById('txt_shift_name').value 				= '".$row[csf("shift_name")]."';\n";

		echo "load_room_rack_self_bin('requires/woven_grey_fabric_receive_controller', 'floor','floor_td', $('#cbo_company_id').val(),$('#cbo_location').val(),$('#cbo_store_name').val(),this.value);\n";

		echo "document.getElementById('cbo_floor').value 				= '".$row[csf("floor_id")]."';\n";
		
		echo "load_machine();\n";
		
		echo "document.getElementById('cbo_machine_name').value 			= '".$row[csf("machine_no_id")]."';\n";

		echo "load_room_rack_self_bin('requires/woven_grey_fabric_receive_controller', 'room','room_td', $('#cbo_company_id').val(),$('#cbo_location').val(),$('#cbo_store_name').val(),'".$row[csf('floor_id')]."',this.value);\n";
		echo "document.getElementById('cbo_room').value 					= '".$row[csf("room")]."';\n";

		echo "load_room_rack_self_bin('requires/woven_grey_fabric_receive_controller', 'rack','rack_td', $('#cbo_company_id').val(),$('#cbo_location').val(),$('#cbo_store_name').val(),'".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		echo "document.getElementById('txt_rack').value 					= '".$row[csf("rack")]."';\n";
		echo "load_room_rack_self_bin('requires/woven_grey_fabric_receive_controller', 'shelf','shelf_td', $('#cbo_company_id').val(),$('#cbo_location').val(),$('#cbo_store_name').val(),'".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";
		echo "document.getElementById('txt_shelf').value 					= '".$row[csf("self")]."';\n";
		echo "load_room_rack_self_bin('requires/woven_grey_fabric_receive_controller', 'bin','bin_td', $('#cbo_company_id').val(),$('#cbo_location').val(),$('#cbo_store_name').val(),'".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."','".$row[csf('self')]."',this.value);\n";


		echo "document.getElementById('cbo_bin').value 					= '".$row[csf("bin_box")]."';\n";
		echo "document.getElementById('hidden_receive_qnty').value 			= '".$row[csf("grey_receive_qnty")]."';\n";
		echo "document.getElementById('hidden_receive_amnt').value 			= '".$row[csf("amount")]."';\n";
		echo "document.getElementById('all_po_id').value 					= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('product_id').value 					= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('previous_prod_id').value 			= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('update_trans_id').value 				= '".$row[csf("trans_id")]."';\n";
		echo "document.getElementById('grey_prod_dtls_id').value 			= '".$row[csf("grey_prod_dtls_id")]."';\n";
		echo "document.getElementById('txt_deleted_id').value 				= '';\n";		
		echo "document.getElementById('txt_rate').value 					= '".number_format($row[csf("rate")],4,".","")."';\n";
		echo "document.getElementById('txt_amount').value 					= '".number_format($row[csf("amount")],4,".","")."';\n";

		$knitting_charge_string=$row[csf("kniting_charge")]."*".$row[csf("yarn_rate")];
		echo "document.getElementById('knitting_charge_string').value 		= '".$knitting_charge_string."';\n";
		if($receive_basis!=4 && $receive_basis!=6)
		{
			if($receive_basis ==14)
			{
				$order_qty = sql_select("select a.id, a.job_no, sum(b.grey_qty) as qty from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.job_no = '".$row[csf('booking_no')]."' and b.body_part_id='".$row[csf("body_part_id")]."' and b.determination_id='".$row[csf("febric_description_id")]."' and b.gsm_weight='".$row[csf("gsm")]."' and b.dia='".$row[csf("width")]."' group by a.id, a.job_no");

				//$up_cond = " and b.id<> ".$row[csf("id")];
				$sql_recv=sql_select("SELECT a.booking_no, sum(b.grey_receive_qnty) grey_receive_qnty FROM inv_receive_master a , pro_grey_prod_entry_dtls b WHERE a.booking_no = '".$row[csf('booking_no')]."' and a.receive_basis=14 and  a.id = b.mst_id and b.status_active=1 and b.body_part_id ='".$row[csf("body_part_id")]."' and b.febric_description_id= '".$row[csf("febric_description_id")]."' and b.gsm='".$row[csf("gsm")]."' and b.width='".$row[csf("width")]."' $up_cond GROUP BY a.booking_no");

				echo "var totalProduction = ".$sql_recv[0][csf('grey_receive_qnty')].";\n";  

				
				echo "$('#txt_gsm').attr('disabled',true);\n";
				echo "$('#txt_width').attr('disabled',true);\n";
			}
			else
			{
				$order_qty = sql_select("SELECT a.id,a.booking_no, sum(b.wo_qnty) qty from inv_receive_master a inner join wo_booking_dtls b on a.booking_no = b.booking_no where a.status_active=1 and a.is_deleted=0 and a.id=".$row[csf('mst_id')]." group by a.id,a.booking_no");
				/*$order_qty = sql_select("SELECT b.lib_yarn_count_deter_id as determination_id, b.body_part_id, b.gsm_weight, a.dia_width, sum(a.wo_qnty) as qty, avg(a.rate) as rate 
				from inv_receive_master x, wo_booking_dtls a, wo_pre_cost_fab_conv_cost_dtls c, wo_pre_cost_fabric_cost_dtls b 
				where  x.booking_no = a.booking_no  and a.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=b.id and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 
				and x.id=57016
				group by b.lib_yarn_count_deter_id, b.body_part_id, b.gsm_weight, a.dia_width");*/
				echo "var totalProduction = ".$row[csf('grey_receive_qnty')].";\n";
			}

			echo "var totalAllowed = parseFloat(".$order_qty[0][csf('qty')].");\n";
			echo "var result = parseFloat((". $over_receive." / 100) * totalAllowed);\n";		
			
			if($over_receive != "0")
			{
				echo "$('#allowedQty').text('(' + totalAllowed + ' + ' + ". $over_receive." + '%) = ' +(totalAllowed + result).toFixed(2)).attr('title', 'Over receive is allowed up to ' + ". $over_receive." + '%');\n";
			}
			else
			{
				echo "$('#allowedQty').text('= ' + totalAllowed);\n";
			}
			echo "$('#totalProduction').text(totalProduction);\n";
			echo "$('#allowedQtyTotal').val((totalAllowed + result).toFixed(2));\n";
			echo "$('#balance').text(((totalAllowed + result).toFixed(2) - totalProduction).toFixed(2));\n";
		}

		$material_use_data='';
		if($receive_basis==11)
		{
			$material_data_sql = sql_select("select id,prod_id,item_category,used_qty,rate,amount, yarn_percentage, porcess_loss from pro_material_used_dtls where status_active=1 and is_deleted=0 and mst_id=".$row[csf('mst_id')]." and dtls_id=".$row[csf('id')]." and item_category=1 and entry_form=550 ");
			foreach ($material_data_sql as  $value) {
				if($material_use_data!="") $material_use_data.="__";
				$material_use_data.=$value[csf('prod_id')]."*".$value[csf('used_qty')]."*".$value[csf('rate')]."*".$value[csf('id')]."*".$value[csf('yarn_percentage')]."*".$value[csf('porcess_loss')];
			}

			echo "document.getElementById('process_string').value 		= '".$material_use_data."';\n";
			echo "$('#txt_rate').attr('title','Yarn Rate/Kg : ".$row[csf("yarn_rate")]."; knitting Charge/Kg :".$row[csf("kniting_charge")]."');\n";
		}
		else
		{
			echo "$('#txt_rate').attr('title','');\n";
		}
		
		$save_string='';
		if($roll_maintained==1)
		{
			$data_roll_array=sql_select("select id, roll_used, po_breakdown_id, qnty, reject_qnty, roll_no, barcode_no from pro_roll_details where dtls_id='$id' and entry_form=$entry_form and status_active=1 and is_deleted=0 order by id");
			foreach($data_roll_array as $row_roll)
			{ 
				if($row_roll[csf('roll_used')]==1) $roll_id=$row_roll[csf('id')]; else $roll_id=0;
				//$roll_id=$row_roll[csf('id')];
				
				if($save_string=="")
				{
					$save_string=$row_roll[csf("po_breakdown_id")]."**".$row_roll[csf("qnty")]."**".$row_roll[csf("roll_no")]."**".$roll_id."**".$row_roll[csf("id")]."**".$row_roll[csf("barcode_no")];
					$save_string2=$row_roll[csf("po_breakdown_id")]."**".$row_roll[csf("reject_qnty")]."**".$row_roll[csf("roll_no")]."**".$roll_id."**".$row_roll[csf("id")]."**".$row_roll[csf("barcode_no")];
				}
				else
				{
					$save_string.=",".$row_roll[csf("po_breakdown_id")]."**".$row_roll[csf("qnty")]."**".$row_roll[csf("roll_no")]."**".$roll_id."**".$row_roll[csf("id")]."**".$row_roll[csf("barcode_no")];
					$save_string2.=",".$row_roll[csf("po_breakdown_id")]."**".$row_roll[csf("reject_qnty")]."**".$row_roll[csf("roll_no")]."**".$roll_id."**".$row_roll[csf("id")]."**".$row_roll[csf("barcode_no")];
				}
			}
		}
		else
		{
			$data_po_array=sql_select("select po_breakdown_id, quantity,reject_qty from order_wise_pro_details where dtls_id='$id' and entry_form=$entry_form and status_active=1 and is_deleted=0");
			foreach($data_po_array as $row_po)
			{ 
				if($save_string=="")
				{
					$save_string=$row_po[csf("po_breakdown_id")]."**".$row_po[csf("quantity")];
					$save_string2=$row_po[csf("po_breakdown_id")]."**".$row_po[csf("reject_qty")];
				}
				else
				{
					$save_string.=",".$row_po[csf("po_breakdown_id")]."**".$row_po[csf("quantity")];
					$save_string2.=",".$row_po[csf("po_breakdown_id")]."**".$row_po[csf("reject_qty")];
				}
			}
		}
		
		echo "document.getElementById('save_data').value 				= '".$save_string."';\n";
		echo "document.getElementById('save_data2').value 				= '".$save_string2."';\n";		
		
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
		$sql="select a.recv_number from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and b.po_breakdown_id='$po_id' and b.roll_no='$roll_no' and a.is_deleted=0 and a.status_active=1 and b.entry_form in(2,22,550) and b.is_deleted=0 and b.status_active=1";
	}
	else
	{
		$sql="select a.recv_number from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and b.po_breakdown_id='$po_id' and b.roll_no='$roll_no' and a.is_deleted=0 and a.status_active=1 and b.entry_form in(2,22,550) and b.id<>$roll_id and b.is_deleted=0 and b.status_active=1";
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
	$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$data' and variable_list=3 and item_category_id=3 and is_deleted=0 and status_active=1");
	if($roll_maintained=="" || $roll_maintained==2) $roll_maintained=0; else $roll_maintained=$roll_maintained;
	
	$variable_data=sql_select("select variable_list, smv_source, process_costing_maintain from variable_settings_production where company_name ='$data' and variable_list in(27,34) and is_deleted=0 and status_active=1");
	foreach($variable_data as $row)
	{
		if($row[csf('variable_list')]==34)
		{
			$process_costing_maintain=$row[csf('process_costing_maintain')];	
		}
		else
		{
			$barcode_generation=$row[csf('smv_source')];
		}
	}
	
	//if($roll_maintained==1) $roll_maintained=$roll_maintained; else $roll_maintained=0;
	if($barcode_generation==2) $barcode_generation=$barcode_generation; else $barcode_generation=1;
	if($process_costing_maintain==1) $process_costing_maintain=$process_costing_maintain; else $process_costing_maintain=0;
	
	echo "document.getElementById('roll_maintained').value 					= '".$roll_maintained."';\n";
	echo "document.getElementById('barcode_generation').value 				= '".$barcode_generation."';\n";
	echo "document.getElementById('process_costing_maintain').value 		= '".$process_costing_maintain."';\n";
	
	echo "reset_form('greyreceive_1','list_fabric_desc_container','','','set_receive_basis();','update_id*txt_recieved_id*cbo_company_id*cbo_receive_basis*txt_receive_date*txt_receive_chal_no*cbo_store_name*cbo_knitting_source*cbo_knitting_company*txt_remarks*roll_maintained*barcode_generation*txt_yarn_issue_challan_no*txt_shift_name*txt_width*txt_gsm*cbo_floor*cbo_machine_name*cbo_room*txt_rack*txt_reject_fabric_recv_qnty*txt_shelf*cbo_uom*cbo_bin*txt_yarn_lot*cbo_yarn_count*txt_brand*cbo_color_range*process_costing_maintain*grey_prod_dtls_id');\n";
	
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
		$sql="select c.color_name from wo_booking_mst a, wo_booking_dtls b, lib_color c where a.booking_no=b.booking_no and b.fabric_color_id=c.id and a.id=$booking_id and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.fabric_color_id, c.color_name";
	}
	else
	{
		$sql="select c.color_name from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, lib_color c where a.booking_no=b.booking_no and b.fabric_color=c.id and a.id=$booking_id and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.fabric_color, c.color_name";
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
	$company=$data[0];
	$location=$data[5];

	$sql="SELECT a.id, a.recv_number, a.item_category, a.receive_basis, a.receive_date, a.challan_no, a.booking_id, a.booking_no, a.store_id, a.knitting_source, a.knitting_company, a.location_id, a.yarn_issue_challan_no, a.buyer_id, a.fabric_nature,a.remarks,b.order_id,a.booking_without_order 
	from inv_receive_master a,pro_grey_prod_entry_dtls b 
	where a.id=b.mst_id and a.id='$data[1]' and company_id='$data[0]' ";
	//echo $sql;
	$dataArray=sql_select($sql);
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name");
	
	$pi_arr=return_library_array( "select id, pi_number from  com_pi_master_details", "id", "pi_number");

	/* $wo_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
	$job_arr=return_library_array( "select a.id, b.job_no from  wo_booking_mst a,wo_booking_dtls b where  a.booking_no=b.booking_no", "id", "job_no");
	$po_arr=return_library_array( "select id, job_no from  wo_po_details_master", "id", "job_no");
	$po_number_arr = return_library_array("select id, po_number from wo_po_break_down","id","po_number"); */

	
	$booking_sql = sql_select("select a.id, b.job_no, a.booking_no,a.tagged_booking_no from  wo_booking_mst a,wo_booking_dtls b where  a.booking_no=b.booking_no and a.booking_type in (1,3,4) and b.status_active=1 and b.is_deleted=0");
	foreach($booking_sql as $row){
		$job_arr[$row[csf("id")]] = $row[csf("job_no")];
		$wo_arr[$row[csf("id")]] = $row[csf("booking_no")];
		$tagged_booking_no_arr[$row[csf("id")]] = $row[csf("tagged_booking_no")];
		$tagged_booking_no_arr2[$row[csf("booking_no")]] = $row[csf("booking_no")];
	}
	unset($booking_sql);
	//var_dump($tagged_booking_no_arr[30544]);

	$job_sql = sql_select("select a.id, a.job_no, b.id as po_id, b.po_number from  wo_po_details_master a, wo_po_break_down b where  a.id=b.job_id and a.status_active=1 and a.is_deleted=0");
	foreach($job_sql as $row){
		$po_arr[$row[csf("id")]] = $row[csf("job_no")];
		$po_number_arr[$row[csf("po_id")]] = $row[csf("po_number")];
	}
	unset($job_sql);
	
	$location_arr=return_library_array( "select id, location_name from lib_location", "id", "location_name");
	$brand_arr=return_library_array( "select id, brand_name from lib_brand where status_active=1 and is_deleted=0",'id','brand_name');
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");
	
	$yarn_count_arr = return_library_array("select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1","id","yarn_count");
	$color_name_arr = return_library_array("select id,color_name from lib_color where is_deleted=0 and status_active=1","id","color_name");	
	
	$program_no="";
	if($dataArray[0][csf('receive_basis')]==9 )
	{
		$program_no=return_field_value("booking_id","inv_receive_master","id=".$dataArray[0][csf('booking_id')]." and entry_form=2 and receive_basis=2");
		$program_booking_no=return_field_value("booking_no","ppl_planning_entry_plan_dtls","dtls_id=".$program_no);


	}
	//new development
	if($dataArray[0][csf('receive_basis')]== 11)
	{
		$sql=sql_select("select a.program_no from wo_booking_dtls a,wo_booking_mst b,inv_receive_master c  where a.booking_no='$data[3]' and a.booking_no=b.booking_no and c.booking_no=a.booking_no group by a.program_no");

		foreach($sql as $result)
		{
			$program_no= $result[csf('program_no')];
			$booking_without_order= $result[csf('booking_without_order')];
		}
	}
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
	<div style="width:1050px;">
		<table width="1020" cellspacing="0" align="right">
			<tr>				
				<td colspan="6" align="center" style="font-size:22px"><strong><? echo $com_dtls[0]; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">  
					<?
						echo $com_dtls[1];
					?> 
				</td>  
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:16px"><strong><u><? echo $data[2]; ?> Report</u></strong></td>
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
				<td><strong>Source :</strong></td><td width="175px"><? echo $knitting_source[$dataArray[0][csf('knitting_source')]]; ?></td>
				<td><strong>Supplier Name:</strong></td><td width="200px"><? if ($dataArray[0][csf('knitting_source')]==1) echo $company_library[$dataArray[0][csf('knitting_company')]]; else if($dataArray[0][csf('knitting_source')]==3) echo $supplier_library[$dataArray[0][csf('knitting_company')]];  ?></td>
				<td><strong>Location:</strong></td> <td width="175px"><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Issue Chal. No:</strong></td> <td width="175px"><? echo $dataArray[0][csf('yarn_issue_challan_no')]; ?></td>
				<?
				$job_no='';
				if($dataArray[0][csf('receive_basis')]==2  || $dataArray[0][csf('receive_basis')]==9 || $dataArray[0][csf('receive_basis')]==11)
				{
					if($dataArray[0][csf('booking_without_order')]==0){
						$job_no=$job_arr[$dataArray[0][csf('booking_id')]];
						if($dataArray[0][csf('receive_basis')]==9)
						{
							$m_booking_no=$tagged_booking_no_arr2[$program_booking_no];
						}
						else
						{
							$m_booking_no=$tagged_booking_no_arr[$dataArray[0][csf('booking_id')]];
						}
					}
				
				}
				else if($dataArray[0][csf('receive_basis')]==9)
				{
					$prodData=sql_select("select receive_basis, booking_id, booking_without_order from inv_receive_master where id='".$dataArray[0][csf('booking_id')]."'");
					$receive_basis=$prodData[0][csf('receive_basis')];
					$booking_plan_id=$prodData[0][csf('booking_id')];
					$booking_without_order=$prodData[0][csf('booking_without_order')];
					
					if($receive_basis==1 && $booking_without_order==0)
					{
						$job_no=return_field_value("job_no","wo_booking_mst","id='".$booking_plan_id."'");
					}
					else if($receive_basis==2)
					{
						$job_no=return_field_value("c.job_no as job_no","ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, wo_booking_mst c","a.id=b.mst_id and a.booking_no=c.booking_no and b.id='".$booking_plan_id."'","job_no");
					}
				}
				?>
				<td><strong>Job No:</strong></td><td width="175px"><?  echo $job_no; ?></td>
                
                <td><strong>Order No.:</strong></td><td width="175px" style="word-break:break-all">
				<? 
					$po_number=explode(",",$dataArray[0][csf('order_id')]);
					$po_nbr="";
					foreach($po_number as $po_num)
					{
						if($po_nbr=="")
						{
							$po_nbr=$po_number_arr[$po_num];
							
						}
						else
						{
							$po_nbr.=",".$po_number_arr[$po_num];
							
						}
					}
					if($dataArray[0][csf('booking_without_order')]==0){
						echo $po_nbr;
					}

					if($dataArray[0][csf('receive_basis')]==14)
					{
						$buyer_name_id = $sales_buyer_id;
					}else{
						$buyer_name_id = $dataArray[0][csf('buyer_id')];
					}
				?>
                </td>
                </tr>
                <tr>
				<td><strong>Buyer:</strong></td><td width="175px"><? echo $buyer_library[$buyer_name_id]; ?></td>
                <td><strong>Remarks:</strong></td><td width="175px"><? echo $dataArray[0][csf('remarks')]; ?></td>
                <td><strong>M.Fabric Booking No:</strong></td><td width="175px"><? echo $m_booking_no; ?></td>

			</tr>
		</table>
		<br>        
		<div style="width:100%;">
			<table align="right" cellspacing="0" width="1020"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="50">Program No:</th>
					<th width="100" >Body Part</th>
					<th width="110" >Feb. Description</th>
                    <th width="110" >Feb. Color</th>
					<th width="40" >Weight</th>
					<th width="50" >Dia/ Width</th>
					<th width="40" >UOM</th> 
					<th width="70" >Grey Rcv. Qnty</th>
					<th width="70" >Reject Qnty</th>
					<th width="50" >Rate</th>
					<th width="70" >Amount</th>
                    <th width="50" >No of Roll</th>
                    <th width="60" >Yarn Count</th>
					<th width="60" >Yarn Lot</th>
					<th width="80" >Brand</th>
					<th width="60" >Shift Name</th> 
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

					$sql_dtls="SELECT id, body_part_id, febric_description_id, gsm, width, grey_receive_qnty, reject_fabric_receive, uom, yarn_lot, no_of_roll, brand_id, shift_name,program_no,yarn_count,order_id,color_id,stitch_length, rate, amount 
					from pro_grey_prod_entry_dtls where mst_id='$data[1]' and status_active = '1' and is_deleted = '0'";
					// echo $sql_dtls;
					$sql_result= sql_select($sql_dtls);
					$i=1;
					$group_arr=return_library_array( "select id, item_name from  lib_item_group", "id", "item_name"  );
					foreach($sql_result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$grey_receive_qnty=$row[csf('grey_receive_qnty')];
						$rate=$row[csf("rate")];
						$grey_receive_qnty_sum += $grey_receive_qnty;
						$totall_amount = $grey_receive_qnty*$rate;
						$reject_fabric_receive=$row[csf('reject_fabric_receive')];
						$reject_fabric_receive_sum += $reject_fabric_receive;
						$color_ids=array_unique(explode(",",$row[csf('color_id')]));
						$color_name="";
						foreach($color_ids as $cid)
						{
							if($color_name!="") $color_name.=", ".$color_name_arr[$cid];else $color_name=$color_name_arr[$cid];
						}
						$color_names=implode(", ",array_unique(explode(", ",$color_name)));
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $i; ?></td>
							<td align="center"><? echo $program_no; ?></td>
							<td><? echo $body_part[$row[csf("body_part_id")]]; ?></td>
							<td><? echo $composition_arr[$row[csf("febric_description_id")]]; ?></td>
                            <td style="word-break:break-all;"><p><? echo $color_names;//$color_name_arr[$row[csf("color_id")]]; ?></p></td>
							<td><? echo $row[csf("gsm")]; ?></td>
							<td><? echo $row[csf("width")]; ?></td>
							<td><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
							<td align="right"><? echo $row[csf("grey_receive_qnty")]; ?></td>
							<td align="right"><? echo $row[csf("reject_fabric_receive")]; ?></td>
							<td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
							<td align="right"><? echo $totall_amount; ?></td>
                            <td align="center"><? echo $row[csf("no_of_roll")]; ?></td>
                            <td align="center" width="60"><? 							
							$yarn_count=explode(",",$row[csf("yarn_count")]); 
							$yarn_cnt=="";
							foreach($yarn_count as $yarn)
							{							
								if($yarn_cnt=="")
								{
									$yarn_cnt=$yarn_count_arr[$yarn];
									
								}
								else
								{
									$yarn_cnt.=",".$yarn_count_arr[$yarn];
									
								}
							}
							echo $yarn_cnt;
							?></td>
							<td align="center"><? echo $row[csf("yarn_lot")]; ?></td>							
							<td align="center"><? echo $brand_arr[$row[csf("brand_id")]]; ?></td>
							<td><? echo $shift_name[$row[csf("shift_name")]]; ?></td>							
						</tr>
						<? 
						$i++; 
						$total_roll += $row[csf("no_of_roll")];
						$total_amount += $row[csf("amount")];
					} ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="8" align="right"><strong>Total :</strong></td>
						<td align="right"><?php echo $grey_receive_qnty_sum; ?></td>
						<td align="right"><?php echo $reject_fabric_receive_sum; ?></td>
						<td align="right"></td>
						<td align="right"><?php echo $total_amount; ?></td>
						<td align="right"><?php echo $total_roll; ?></td>
						<td colspan="4">&nbsp;</td>
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
	$booking_without_order=$data[2];
	if($booking_without_order==1)
	{
		$query="select id,roll_no,barcode_no,po_breakdown_id,qnty,booking_no as po_number, is_sales from pro_roll_details  where dtls_id=$dtls_id and entry_form=550 and status_active=1 and is_deleted=0";
		//$caption="Booking No.";
	}
	else
	{
		//$query="select a.id, a.roll_no, a.barcode_no, a.po_breakdown_id, a.qnty, b.po_number, a.booking_without_order from pro_roll_details a, wo_po_break_down b where a.po_breakdown_id=b.id and a.dtls_id=$dtls_id and a.entry_form=22 and a.status_active=1 and a.is_deleted=0 order by a.id";

		$query="SELECT a.id, a.roll_no, a.barcode_no, a.po_breakdown_id, a.qnty,  b.job_no_mst as po_number, a.booking_without_order, a.is_sales from pro_roll_details a, fabric_sales_order_dtls b where a.po_breakdown_id=b.id and a.dtls_id=$dtls_id and a.entry_form=550 and a.is_sales=0 and a.is_sales=0 and a.status_active=1 and a.is_deleted=0 union all select a.id, a.roll_no, a.barcode_no, a.po_breakdown_id, a.qnty, b.job_no as po_number, a.booking_without_order, a.is_sales from pro_roll_details a, fabric_sales_order_mst b where a.po_breakdown_id=b.id and a.dtls_id=$dtls_id and a.entry_form=550 and a.is_sales=1 and a.status_active=1 and a.is_deleted=0 order by id";

		//$caption="PO No.";
	}
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
			<th width="90">Booking/PO/Sales No</th>
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
			//echo $query;
			$result=sql_select($query);  
			foreach($result as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
					<td width="90">
						<p><? if($row[csf('booking_without_order')]!=1) echo $row[csf('po_number')]; ?></p>
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

	$brand_arr=return_library_array( "select id, brand_name from lib_brand where status_active=1 and is_deleted=0",'id','brand_name');
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count","id","yarn_count");

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

	//$color=$color_arr[$row[csf('color_id')]];
		$color='';
		$color_id=explode(",",$row[csf('color_id')]);
		foreach($color_id as $val)
		{
			if($val>0) $color.=$color_arr[$val].",";
		}
		$color=chop($color,',');

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

	$brand_arr=return_library_array( "select id, brand_name from lib_brand where status_active=1 and is_deleted=0",'id','brand_name');
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count","id","yarn_count");
	
	$sql="select a.company_id, a.recv_number, a.location_id, a.receive_basis, a.booking_id, a.receive_date, a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm, b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.color_id, b.febric_description_id, b.shift_name, b.insert_date, b.color_range_id, b.machine_dia, b.machine_gg from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and b.id=$data[1]";
	$result=sql_select($sql);
	$party_name=''; $prod_date=''; $order_id=''; $buyer_name=''; $grey_dia=''; $tube_type=''; $program_no=''; 
	$yarn_lot=''; $yarn_count=''; $brand=''; $gsm=''; $finish_dia=''; $shiftName=''; $colorRange=''; $productionId='';
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
		
		//$prod_date=date("d-m-Y",strtotime($row[csf('insert_date')]));
		//$prod_time=date("H:i",strtotime($row[csf('insert_date')]));
		$prod_date=date("d-m-Y",strtotime($row[csf('receive_date')]));
		$productionId=$row[csf('recv_number')];
		
		$order_id=rtrim($row[csf('order_id')],",");
		$gsm=$row[csf('gsm')];
		$finish_dia=$row[csf('width')];
		$shiftName=$shift_name[$row[csf('shift_name')]];
		$colorRange=$color_range[$row[csf('color_range_id')]];
		
		//$color=$color_arr[$row[csf('color_id')]];
		$color='';
		$color_id=explode(",",$row[csf('color_id')]);
		foreach($color_id as $val)
		{
			if($val>0) $color.=$color_arr[$val].",";
		}
		$color=chop($color,',');
		if(trim($color)!="")
		{
			$color=", ".$color;
		}
		
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
		//$machine_dia_width=$machine_data[0][csf('dia_width')];
		//$machine_gauge=$machine_data[0][csf('gauge')];
		
		$machine_dia_width=$row[csf('machine_dia')];
		$machine_gauge=$row[csf('machine_gg')];
		
		//$location_name=return_field_value("location_name","lib_location", "id=".$row[csf('location_id')]);
		$buyer_name=return_field_value("short_name","lib_buyer", "id=".$row[csf('buyer_id')]);
		
		$comp='';
		if($row[csf('febric_description_id')]==0 || $row[csf('febric_description_id')]=="")
		{
			$comp=return_field_value("item_description","product_details_master","id=".$row[csf('prod_id')]);
		}
		else
		{
			$deter_id = $row[csf('febric_description_id')];
			$determination_sql=sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=$deter_id");

			if($determination_sql[0][csf('construction')]!="")
			{
				$comp=$determination_sql[0][csf('construction')].", ";
			}
			
			foreach( $determination_sql as $d_row )
			{
				$comp.=$composition[$d_row[csf('copmposition_id')]]." ".$d_row[csf('percent')]."% ";
			}
		}

		if($row[csf('receive_basis')]==9)
		{
			$recvData=sql_select("select receive_basis, booking_id from inv_receive_master where id=".$row[csf('booking_id')]." and entry_form=2");
			if($recvData[0][csf('receive_basis')]==2)
			{
				$program_data=sql_select("select width_dia_type, machine_dia from ppl_planning_info_entry_dtls where id='".$recvData[0][csf('booking_id')]."'");
				$program_no=$recvData[0][csf('booking_id')];
				$grey_dia=$program_data[0][csf('machine_dia')]; 
				$tube_type=$fabric_typee[$program_data[0][csf('width_dia_type')]]; 
			}
		}
	}
	//echo "select a.job_no, a.style_ref_no, a.job_no_prefix_num, b.id, b.po_number, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($order_id)";
	$po_array=array();
	$po_sql=sql_select("select a.job_no, a.style_ref_no, a.job_no_prefix_num, b.id, b.po_number, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($order_id)");
	foreach($po_sql as $row)
	{
		$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
		$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')]; 
		$po_array[$row[csf('id')]]['prefix']=$row[csf('job_no_prefix_num')];
		$po_array[$row[csf('id')]]['grouping']=$row[csf('grouping')]; 
		$po_array[$row[csf('id')]]['file_no']=$row[csf('file_no')]; 
		$po_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')]; 
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
		$txt .= $row[csf('barcode_no')]."\r\n";
		//$txt .="Barcode No: ".$row[csf('barcode_no')]."\r\n";
		$txt .= $row[csf('barcode_no')]."\r\n";
		$txt .="D:".$prod_date." ".$buyer_name."\r\n";
		$txt .="Order No: ". $po_array[$row[csf('po_breakdown_id')]]['no']."\r\n";
		$txt .="File No:". $po_array[$row[csf('po_breakdown_id')]]['file_no'].",Ref.No:". $po_array[$row[csf('po_breakdown_id')]]['grouping']."\r\n";
		$txt .=$comp."\r\n";
		$txt .="G/F-Dia:".trim($grey_dia)."/".trim($finish_dia)." ".trim($stitch_length)." ".trim($tube_type)."\r\n";
		$txt .="GSM:".$gsm." ";
		$txt .= $yarn_count." ".$brand." Lot:".$yarn_lot."\r\n";
		$txt .="Prg:".$program_no."/RollWt:".number_format($row[csf('qnty')],2,'.','')." Kg,Sft:".$shiftName."\r\n";
		$txt .="Roll No:". $row[csf('roll_no')].trim($color)."\r\n";
		$txt .="Color range: ".trim($colorRange)."\r\n";
		$txt .="Style Ref.: ".$po_array[$row[csf('po_breakdown_id')]]['style_ref']."\r\n";
		$txt .= "Sys. ID: ".$productionId;
		
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

if($action=="report_barcode_text_file_prev")
{
	$data=explode("***",$data);

	$brand_arr=return_library_array( "select id, brand_name from lib_brand where status_active=1 and is_deleted=0",'id','brand_name');
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count","id","yarn_count");
	
	$sql="select a.company_id, a.location_id, a.receive_basis, a.booking_id, a.receive_date, a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm, b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.color_id, b.febric_description_id,b.insert_date from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and b.id=$data[1]";
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
		
		//$color=$color_arr[$row[csf('color_id')]];
		$color='';
		$color_id=explode(",",$row[csf('color_id')]);
		foreach($color_id as $val)
		{
			if($val>0) $color.=$color_arr[$val].",";
		}
		$color=chop($color,',');
		
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
		
		$location_name=return_field_value("location_name","lib_location", "id=".$row[csf('location_id')]);
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
	$po_sql=sql_select("select a.job_no, a.job_no_prefix_num, b.id, b.po_number, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($order_id)");
	foreach($po_sql as $row)
	{
		$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
		$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')]; 
		$po_array[$row[csf('id')]]['prefix']=$row[csf('job_no_prefix_num')];
		$po_array[$row[csf('id')]]['grouping']=$row[csf('grouping')]; 
		$po_array[$row[csf('id')]]['file_no']=$row[csf('file_no')];  
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
		$txt .="Unit: ". $location_name."\r\n";
		$txt .="Ref. ". $po_array[$row[csf('po_breakdown_id')]]['grouping'].", File: ". $po_array[$row[csf('po_breakdown_id')]]['file_no']."\r\n";
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

if($action=="check_conversion_rate")
{ 
	$data=explode("**",$data);
	if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$exchange_rate=set_conversion_rate( $data[0], $conversion_date );
	echo $exchange_rate;
	exit();	
}


if($action=="yarn_lot_popup")
{
	echo load_html_head_contents("Yarn Lot Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);	
	$save_data=explode(",",$save_data);
	foreach($save_data as $data_arr)
	{
		$data_arr=explode("**",$data_arr);
		$po_arr[]=$data_arr[0];
	}
	$po_id_all=implode(",",$po_arr);
	$row_cond="";
	$row_limit="";
	if($db_type==0) {$txt_receive_date=change_date_format($txt_receive_date,'yyyy-mm-dd','-'); $row_limit=" limit 1";} 
	else { $txt_receive_date=change_date_format($txt_receive_date,'yyyy-mm-dd','-',1); $row_cond=" and rownum=1";}
	
	?> 
	<script>
		function fnc_process_cost()
		{
			var process_string="";
			var knitting_rate_string="";
			var receive_qty=<? echo $txt_receive_qnty; ?>;
			var rate_with_knitting_charge=0;
			var total_amount=0;
			var total_lot="";
			var total_count="";
			var total_brand="";
			var brand_name="";
			var all_deleted_id="";
			var total_used_qty=0;
			var total_percentage =0;
			$("#tbl_lot_list").find('tr').each(function()
			{
				var txt_used=$(this).find('input[name="txt_used_qty[]"]').val()*1;
				
				if(txt_used>0)
				{
					total_used_qty=total_used_qty+txt_used;
					var txt_prod_id=$(this).find('input[name="txt_prod_id[]"]').val();
					var txt_cons_rate=$(this).find('input[name="txt_cons_rate[]"]').val();
					var txt_net_used=$(this).find('input[name="txt_net_used[]"]').val();
					var txt_material_update_id=$(this).find('input[name="update_material_id[]"]').val();
					var lot_id=$(this).find('input[name="txt_lot[]"]').val();
					var brand=$(this).find('input[name="txt_brand[]"]').val();
					var yarn_count_id=$(this).find('input[name="txt_yarn_count_id[]"]').val();
					
					var txt_yarn_percentage=$(this).find('input[name="txt_yarn_percentage[]"]').val()*1;
					var txt_process_loss=$(this).find('input[name="txt_process_loss[]"]').val();
					total_percentage += txt_yarn_percentage;
					if(txt_net_used==0) txt_net_used=txt_used;
					if(trim(lot_id)!="")
					{
						if(trim(total_lot)!="") total_lot=total_lot+","+lot_id;
						else 					 total_lot=lot_id;
					}

					if(trim(yarn_count_id)!="")
					{
						if(trim(total_count)!="") total_count=total_count+","+yarn_count_id;
						else 					  total_count=yarn_count_id;
					}

					if(trim(brand)!="")
					{
						if(trim(total_brand)!="") total_brand=total_brand+","+brand;
						else 					  total_brand=brand;
					}
					
					var txt_yarn_cost=txt_cons_rate*txt_used;
					total_amount+=txt_yarn_cost;
					var yarn_rate=txt_yarn_cost/txt_net_used;
					if(trim(process_string)=="")
					{
						process_string=txt_prod_id+"*"+txt_used+"*"+txt_cons_rate+"*"+txt_material_update_id+"*"+txt_yarn_percentage+"*"+txt_process_loss;
					}
					else
					{
						process_string=process_string+"__"+txt_prod_id+"*"+txt_used+"*"+txt_cons_rate+"*"+txt_material_update_id+"*"+txt_yarn_percentage+"*"+txt_process_loss;
					}
				}
				else
				{
					if($(this).find('input[name="update_material_id[]"]').val()>0)
					{
						if(trim(all_deleted_id)!="") { all_deleted_id=all_deleted_id=","+$(this).find('input[name="update_material_id[]"]').val();}
						else { all_deleted_id=$(this).find('input[name="update_material_id[]"]').val();}
					}	
				}
			});

			if(total_percentage > 100){
				alert("Total Yarn Percentage Must be Less or equal to 100");
				return;
			}
			if( total_used_qty<receive_qty ){
				alert("Total Used Qty Must be Greater or Equal to Receive Qty.");
				return;
			}
			
			
			if(total_brand!="")
			{
				total_brand=total_brand.split(",");
				if(total_brand.length==1) brand_name=total_brand[0];
				else 					  brand_name="";
			}
			var total_rate=total_amount/receive_qty;
			var knitting_charge=$("#txt_knitting_charge").val()*1;
			rate_with_knitting_charge=total_rate+knitting_rate_string;
			knitting_rate_string=knitting_charge+"*"+total_rate+"*"+total_lot+"*"+total_count+"*"+brand_name+"*"+all_deleted_id;
			//alert(process_string)
			$('#hidden_process_string').val( process_string );
			$('#hidden_knitting_rate').val( knitting_rate_string );
			parent.emailwindow.hide();
			
		}
		
		
		function calculate_net_qty(yarn_percentage,id,receive_qty)
		{
			if(yarn_percentage>100) {
				alert("Yarn Percentage Must be Less or equal to 100");
				$("#txt_yarn_percentage_"+id).val('');
				
				return;
			}
			var process_loss=$("#txt_process_loss_"+id).val();
			var calculateed_net_used=(receive_qty*yarn_percentage)/100;
			//alert(calculateed_net_used)
			$("#net_used_td_"+id).text(calculateed_net_used);
			$("#txt_net_used"+id).val(calculateed_net_used);
			var used_qty=0;
			used_qty=(100/(100-process_loss))*calculateed_net_used;
			$("#txt_used_qty_"+id).val(used_qty);
		}
		
		
		
		
		function calculate_used_qty(process_loss,id,receive_qty)
		{
			var yarn_percentage=$("#txt_yarn_percentage_"+id).val();
			if(yarn_percentage>100) {
				alert("Yarn Percentage Must be Less or equal to 100");
				$("#txt_yarn_percentage_"+id).val('');
				return;
			}
			
			var calculateed_net_used=(receive_qty*yarn_percentage)/100;
			//alert(calculateed_net_used)
			$("#net_used_td_"+id).text(calculateed_net_used);
			$("#txt_net_used"+id).val(calculateed_net_used);
			var used_qty=0;
			used_qty=(100/(100-process_loss))*calculateed_net_used;
			$("#txt_used_qty_"+id).val(used_qty);
		}
	</script>
	<input type="hidden" name="hidden_process_string" id="hidden_process_string" value="" />
	<input type="hidden" name="hidden_knitting_rate" id="hidden_knitting_rate" value="" />

	<div>
		<?php
		$yarn_count_arr = return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
		$brand_arr = return_library_array( "select id,brand_name  from  lib_brand where status_active=1 and is_deleted=0",'id','brand_name ');
			//$precost_exchange_rate=return_field_value("exchange_rate","wo_pre_cost_mst", "job_no='$txt_job_no'");
			//$conversion_cost=sql_select("select a.id,a.charge_unit from  wo_pre_cost_fab_conv_cost_dtls a,wo_pre_cost_fabric_cost_dtls b where a.job_no='$txt_job_no' and a.fabric_description=b.id and a.job_no=b.job_no and b.lib_yarn_count_deter_id=".$fabric_description_id." order by id");
			//$kitting_charge=$conversion_cost[0][csf('charge_unit')]*$precost_exchange_rate;
		
		$conversition_cost_sql=sql_select("select b.process, a.currency_id,a.exchange_rate,sum(amount)/sum(wo_qnty) as rate     from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.id=".$booking_id." and b.process in (1,3,4) group by  a.currency_id,a.exchange_rate,b.process ");
		$knitting_charge=0;
		foreach($conversition_cost_sql as $charge_value)
		{
			$knitting_charge+=$charge_value[csf('rate')]*$charge_value[csf('exchange_rate')];
		}
		$processloss_sql=sql_select("select sum(process_loss) as process_loss from conversion_process_loss   where  mst_id=".$fabric_description_id." and process_id in (1,3,4)");
		$process_loss=$processloss_sql[0][csf('process_loss')];
		if( empty($process_loss) ) $process_loss=0;

		$sql_determination=sql_select("select b.count_id,b.type_id, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=".$fabric_description_id."");
		$serch_count_arr=array();
		$serch_composition_arr=array();
		$serch_type_arr=array();
		$determination_arr=array();
		foreach($sql_determination as $inv)
		{
			$serch_count_arr[]=$inv[csf('count_id')];
			$serch_composition_arr[]=$inv[csf('copmposition_id')];
			$serch_type_arr[]=$inv[csf('type_id')];
			$determination_arr[$inv[csf('count_id')]][$inv[csf('copmposition_id')]][$inv[csf('type_id')]]=$inv[csf('percent')];
		}
		?>	
		<table cellspacing="0" cellpadding="0" border="0" rules="all" width="840" class="" align="center">
			<tr>
				<td colspan="5" align="center" style="font-size:16px"><strong>Knitting process loss <?php echo $process_loss." %";?></strong>
				</td>
				<td colspan="5" align="center" style="font-size:16px"><strong>Knitting Charge <?php echo number_format($knitting_charge,2)."Tk./Kg";?></strong>
				</td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="50">Prod Id</th>
				<th width="60">Lot</th>
				<th width="40">Count</th>
				<th width="130">Composition</th>
				<th width="70">Type</th>
				<th width="80">Brand</th>
				<th width="50">Yarn %</th>
				<th width="100">Avg Yarn Rate /Kg (Tk.) </th>
				<th width="70">Net Qty</th>
				<th width="70">Process Loss %</th>
				<th >Used Qty</th>
			</thead>
		</table>
		<div style="width:880px; max-height:280px; overflow-y:scroll">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="860" class="rpt_table" id="tbl_lot_list">
				<?php 
				$i=1;
				$sql_cond="";
				if($serch_count_arr>0) $sql_cond=" and c.yarn_count_id in (".implode(",",$serch_count_arr).") ";
				if($serch_composition_arr>0) $sql_cond.=" and c.yarn_comp_type1st in (".implode(",",$serch_composition_arr).") ";
				if($serch_type_arr>0) $sql_cond.=" and c.yarn_type in (".implode(",",$serch_type_arr).") ";

				$sql="select c.id,c.lot,c.brand, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type,sum(b.cons_quantity) issue_qty,sum(b.cons_amount) cons_amount, 0 as type 
				from inv_issue_master a, inv_transaction b, product_details_master c,order_wise_pro_details d where a.id=b.mst_id  and a.entry_form=3 and b.prod_id=c.id and a.item_category=1 and b.id=d.trans_id and b.prod_id=d.prod_id and d.trans_type=2 and d.entry_form=3 and d.po_breakdown_id in(".$po_id_all.") and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.item_category=1  and b.status_active=1 and b.is_deleted=0 and knit_dye_source=3  and a.issue_purpose=1 group by c.id,c.lot,c.brand,c.supplier_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type
				union all
				select c.id,c.lot,c.brand, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type,sum(b.cons_quantity) issue_qty,sum(b.cons_amount) cons_amount, 1 as type 
				from inv_issue_master a, inv_transaction b, product_details_master c, wo_non_ord_samp_booking_mst d, wo_non_ord_knitdye_booking_mst e 
				where a.id=b.mst_id and b.prod_id=c.id and a.booking_id=d.id and a.booking_no=d.booking_no and d.id=e.fab_booking_id and a.entry_form=3 and a.item_category=1 and b.item_category=1 and b.transaction_type=2 and a.knit_dye_source=3  and a.issue_purpose=8 and e.fab_booking_id in(".$po_id_all.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by c.id,c.lot,c.brand,c.supplier_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type"; 
				//echo $sql;//die;
				if($update_dtls_id!="")
				{
					$update_sql=sql_select("select id,prod_id,used_qty,rate,amount, yarn_percentage, porcess_loss from pro_material_used_dtls where mst_id=$update_id and dtls_id =$update_dtls_id");
					$update_data_arr=array();
					foreach($update_sql as $val)
					{
						$update_data_arr[$val[csf('prod_id')]]['prod_id']=$val[csf('prod_id')];
						$update_data_arr[$val[csf('prod_id')]]['id']=$val[csf('id')];
						$update_data_arr[$val[csf('prod_id')]]['used_qty']=$val[csf('used_qty')];
						$update_data_arr[$val[csf('prod_id')]]['rate']=$val[csf('rate')];
						$update_data_arr[$val[csf('prod_id')]]['amount']=$val[csf('amount')];
						$update_data_arr[$val[csf('prod_id')]]['yarn_percentage']=$val[csf('yarn_percentage')];
						$update_data_arr[$val[csf('prod_id')]]['porcess_loss']=$val[csf('porcess_loss')];
						$check_arr[]=$val[csf('prod_id')];
					}
				}
				
				$nameArray=sql_select($sql);
				foreach ($nameArray as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	

					$composition_string = $composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]."%";
					if($row[csf('yarn_comp_type2nd')]!=0) $composition_string .= " ".$composition[$row[csf('yarn_comp_type2nd')]]." ".$row[csf('yarn_comp_percent2nd')]."%";
					$yarn_percentage=$determination_arr[$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]];
					$net_used=($txt_receive_qnty*$yarn_percentage)/100;
					$process_loss_used=($net_used*100)/(100-$process_loss);
					if(in_array($row[csf("id")], $check_arr))
					{
						$update_process_loss_used=$update_data_arr[$row[csf('id')]]['used_qty'];
						//$update_process_loss_used=$update_data_arr[$row[csf('id')]]['used_qty'];
						//if( empty( $update_process_loss_used )) $update_process_loss_used=$process_loss_used;
						
						$update_process_loss=$update_data_arr[$row[csf('id')]]['porcess_loss'];
						//if( empty( $update_process_loss )) $update_process_loss=$process_loss;
						
						$update_yarn_percentage=$update_data_arr[$row[csf('id')]]['yarn_percentage'];
						if( empty( $update_yarn_percentage )) $update_yarn_percentage=$yarn_percentage;
						$net_used=($txt_receive_qnty*$update_yarn_percentage)/100;
						?>
						<tr bgcolor="#FFFF99" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>"> 
							<td width="30" align="center"><?php echo "$i"; ?>
								<input type="hidden" name="update_material_id[]" id="update_material_id<?php echo $i; ?>" value="<?php echo $update_data_arr[$row[csf('id')]]['id']; ?>"/>	
								<input type="hidden" name="txt_net_used[]" id="txt_net_used<?php echo $i; ?>" value="<?php  echo $net_used; ?>"/>
								<input type="hidden" name="txt_cons_rate[]" id="txt_cons_rate<?php echo $i; ?>" value="<?php echo $update_data_arr[$row[csf('id')]]['rate']; ?>"/>
								<input type="hidden" name="txt_prod_id[]" id="txt_prod_id<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>" />
								<input type="hidden" name="txt_yarn_count_id[]" id="txt_yarn_count_id<?php echo $i; ?>" value="<?php echo $row[csf('yarn_count_id')]; ?>" />
								<input type="hidden" name="txt_brand[]" id="txt_brand<?php echo $i; ?>" value="<?php echo $brand_arr[$row[csf('brand')]]; ?>" />	         <input type="hidden" name="txt_lot[]" id="txt_lot<?php echo $i; ?>" value="<?php echo $row[csf('lot')]; ?>" />
							</td>	
							<td width="50"><p><?php  echo $row[csf('id')];?></p></td>
							<td width="60"><p><?php  echo $row[csf('lot')];?></p></td> 
							<td width="40"><p><?php  echo $yarn_count_arr[$row[csf('yarn_count_id')]];?></p></td>
							<td width="130"><p><?php echo $composition_string;?></p></td>
							<td width="70"><p><?php  echo $yarn_type[$row[csf('yarn_type')]];?></p></td>
							<td width="80"><p><?php  echo $brand_arr[$row[csf('brand')]];?></p></td>
							<td width="50" align="right"><input type="text" id="txt_yarn_percentage_<? echo $i;  ?>" name="txt_yarn_percentage[]"  style="width:35px" class="text_boxes_numeric" value="<?php  echo $update_yarn_percentage;?>" onKeyUp="calculate_net_qty(this.value,<? echo $i;  ?>,<? echo $txt_receive_qnty;  ?>)" /></td>
							<td width="100" align="right"><p><?php  echo $update_data_arr[$row[csf('id')]]['rate'];?></p></td>
							<td width="70" align="right" id="net_used_td_<? echo $i;  ?>"><p><?php   echo $net_used;?></p></td>
							<td width="70" align="right"><input type="text" id="txt_process_loss_<? echo $i;  ?>" name="txt_process_loss[]"  style="width:35px" class="text_boxes_numeric" value="<?php echo $update_process_loss;?>" onKeyUp="calculate_used_qty(this.value,<? echo $i;  ?>,<? echo $txt_receive_qnty;  ?>)" /></td>

                            <td><input type="text" id="txt_used_qty_<? echo $i;  ?>" name="txt_used_qty[]"  style="width:80px" class="text_boxes_numeric" value="<? //if($process_loss_used==0) { $process_loss_used=$update_data_arr[$row[csf('id')]]['used_qty'];}
                            	echo number_format($update_process_loss_used,2,'.',''); ?>"/></td>
                            </tr>
                            <?
                            $i++;
                        }
                        else
                        {
                        	?>
                        	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>"> 
                        		<td width="30" align="center"><?php echo "$i"; ?>
                        			<input type="hidden" name="update_material_id[]" id="update_material_id<?php echo $i; ?>" value="0"/>	
                        			<input type="hidden" name="txt_net_used[]" id="txt_net_used<?php echo $i; ?>" value="<?php  echo $net_used; ?>"/>
                        			<input type="hidden" name="txt_cons_rate[]" id="txt_cons_rate<?php echo $i;?>" value="<?php echo $row[csf('cons_amount')]/$row[csf('issue_qty')]; ?>"/>
                        			<input type="hidden" name="txt_prod_id[]" id="txt_prod_id<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>" />
                        			<input type="hidden" name="txt_yarn_count_id[]" id="txt_yarn_count_id<?php echo $i; ?>" value="<?php echo $row[csf('yarn_count_id')]; ?>" />
                        			<input type="hidden" name="txt_brand[]" id="txt_brand<?php echo $i; ?>" value="<?php echo $brand_arr[$row[csf('brand')]]; ?>" />	         
                        			<input type="hidden" name="txt_lot[]" id="txt_lot<?php echo $i; ?>" value="<?php echo $row[csf('lot')]; ?>" />
                        		</td>	
                        		<td width="50"><p><?php  echo $row[csf('id')];?></p></td>
                        		<td width="60"><p><?php  echo $row[csf('lot')];?></p></td> 
                        		<td width="40"><p><?php  echo $yarn_count_arr[$row[csf('yarn_count_id')]];?></p></td>
                        		<td width="130"><p><?php echo $composition_string;?></p></td>
                        		<td width="70"><p><?php  echo $yarn_type[$row[csf('yarn_type')]];?></p></td>
                        		<td width="80"><p><?php echo $brand_arr[$row[csf('brand')]];?></p></td>
                        		<td width="50" align="right"><input type="text" id="txt_yarn_percentage_<? echo $i;  ?>" name="txt_yarn_percentage[]"  style="width:35px" class="text_boxes_numeric" value="<?  echo $yarn_percentage; ?>" onKeyUp="calculate_net_qty(this.value,<? echo $i;  ?>,<? echo $txt_receive_qnty;  ?>)" /></td>
                        		<td width="100" align="right"><p><?php  echo $row[csf('cons_amount')]/$row[csf('issue_qty')];?></p></td>
                        		<td width="70" align="right" id="net_used_td_<? echo $i;  ?>"><p><?php   echo $net_used;?></p></td>
                        		<td width="70" align="right"><input type="text" id="txt_process_loss_<? echo $i;  ?>" name="txt_process_loss[]"  style="width:35px" class="text_boxes_numeric" value="<?php echo $process_loss;?>" onKeyUp="calculate_used_qty(this.value,<? echo $i;  ?>,<? echo $txt_receive_qnty;  ?>)" /></td>
                        		<td><input type="text" id="txt_used_qty_<? echo $i;  ?>" name="txt_used_qty[]"  style="width:80px" class="text_boxes_numeric" value="<?  echo number_format($process_loss_used,2,'.',''); //if(count($check_arr)==0) ?>"/></td>
                        	</tr>
                        	<?
                        	$i++;
                        }
                    }
				?>
				<input type="hidden" name="txt_color_row_id" id="txt_color_row_id" value="<?php echo $color_row_id; ?>"/>	
			</table>
		</div>
		<table width="640" cellspacing="0" cellpadding="0" border="1" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%"> 
						<div style="width:100%; float:left" align="center">
							<input type="hidden" name="txt_knitting_charge" id="txt_knitting_charge" value="<?php  echo number_format($knitting_charge,2,'.',''); ?>"/>

							<input type="button" name="close" onClick="fnc_process_cost();" class="formbutton" value="Close" style="width:100px" />
						</div>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<?
	exit();
}

if($action=="bill_generate")
{
	$recId=$data;
	
	$sqlRec="select knitting_source, receive_basis, company_id, knitting_company, location_id, receive_date, booking_without_order from inv_receive_master where entry_form=550 and id='$recId' and item_category=14";
	//echo $sqlRec; die;
	$sqlRecData=sql_select($sqlRec);
	if(count($sqlRecData)<1)
	{
		echo "55**".count($sqlRecData);
		die;
	}
	foreach($sqlRecData as $rrow)
	{
		$partySource=$rrow[csf('knitting_source')];
		$receive_basis=$rrow[csf('receive_basis')];
		$company_id=$rrow[csf('company_id')];
		$knitting_company=$rrow[csf('knitting_company')];
		$location_id=$rrow[csf('location_id')];
		$receive_date=$rrow[csf('receive_date')];
		$booking_without_order=$rrow[csf('booking_without_order')];
	}
	unset($sqlRecData);
	
	if($db_type==0) 
	{
		$bookingWithoutOrderCond="IFNULL(a.booking_without_order,0)";
	}
	else if ($db_type==2)
	{
		$bookingWithoutOrderCond="nvl(a.booking_without_order,0)";
	}
	
	if(str_replace("'","",$recId)!="")
	{
		$recBill=return_field_value("bill_no","inv_receive_master","id='$recId' and status_active=1 and is_deleted=0","bill_no");
		if($recBill!="")
		{
			if($partySource==1)
			{
				$inboundBill=return_field_value("bill_no","subcon_inbound_bill_mst","id='$recBill'","bill_no");
				
				if($inboundBill!="")
				{
					echo "30**In-Bound->Knitting Bill Issue.Bill No:".$inboundBill;
					die;
				}
			}
			else if($partySource==3)
			{
				$outboundBill=return_field_value("bill_no","subcon_outbound_bill_mst","id='$recBill'","bill_no");
				if($outboundBill!="")
				{
					echo "30**Out-Bound->Knitting Bill Entry[Gross].Bill No:".$outboundBill;
					die;
				}
			}
		}
	}
	
	//echo $booking_without_order;
	$pageNameMsg="";
	if($partySource==1)
	{
		if($booking_without_order==0)//with Order
		{
			if($receive_basis==9)//Receive Basis Production
			{
				$sql="SELECT a.id, d.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, c.po_breakdown_id, sum(c.quantity) as quantity, sum(b.rate) as rate, sum(b.amount) as amount, d.receive_basis, d.booking_no, d.booking_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, inv_receive_master d
				 where a.id=b.mst_id and $bookingWithoutOrderCond=0 and b.id=c.dtls_id and d.id=a.booking_id and a.knitting_source=1 and a.company_id='$company_id' and a.knitting_company='$knitting_company' and a.location_id='$location_id' and c.trans_type=1 and a.entry_form=550 and c.entry_form=550 and a.receive_basis=9 and d.receive_basis!=4 and a.item_category=13 and d.entry_form=2 and c.trans_id!=0
				 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id='$recId'
				 group by a.id, d.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id, d.receive_basis, d.booking_no, d.booking_id";
			}
			else if($receive_basis==2 || $receive_basis==4 || $receive_basis==11) //Receive Basis WO/Booking Based, Independent, Service Booking Based
			{
				$sql="SELECT a.id, a.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, c.po_breakdown_id, sum(c.quantity) as quantity, sum(b.rate) as rate, sum(b.amount) as amount, a.receive_basis, a.booking_no, a.booking_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c
			where a.id=b.mst_id and $bookingWithoutOrderCond=0 and b.id=c.dtls_id and a.knitting_source=1 and a.company_id='$company_id' and a.knitting_company='$knitting_company' and a.location_id='$location_id' and c.trans_type=1 and a.entry_form=550 and c.entry_form=550 and a.item_category=13 and a.receive_basis in (2,4,11) and c.trans_id!=0
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id='$recId'
			group by a.id, a.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id, a.receive_basis, a.booking_no, a.booking_id";
			}
		}
		else if($booking_without_order==1)//with out Order
		{
			if($receive_basis==9)//Receive Basis Production
			{
				$sql="SELECT a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, sum(b.grey_receive_qnty) as quantity, c.entry_form, c.receive_basis, c.booking_no, c.booking_id, 0 as order_id 
					from inv_receive_master a, pro_grey_prod_entry_dtls b, inv_receive_master c 
					where a.id=b.mst_id and c.id=a.booking_id and a.booking_without_order=1 and a.knitting_source=1 and a.company_id='$company_id' and a.knitting_company='$knitting_company' and a.location_id='$location_id' and a.entry_form=550 and a.receive_basis=9 and a.item_category=13 and c.entry_form=2 and c.receive_basis in (0,1,2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.trans_id!=0 and a.id='$recId'
					group by a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.entry_form, c.receive_basis, c.booking_no, c.booking_id";
			}
			else if($receive_basis==2 || $receive_basis==4 || $receive_basis==11) //Receive Basis WO/Booking Based, Independent, Service Booking Based
			{
				$sql="SELECT a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, sum(b.grey_receive_qnty) as quantity, a.entry_form, a.receive_basis, a.booking_no, a.booking_id, 0 as order_id 
					from inv_receive_master a, pro_grey_prod_entry_dtls b
					where a.id=b.mst_id and a.booking_without_order=1 and a.knitting_source=1 and a.company_id='$company_id' and a.knitting_company='$knitting_company' and b.trans_id!=0 and a.location_id='$location_id' and a.entry_form=550 and a.item_category=13 and a.receive_basis in (2,4,11)
					and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='$recId'
					group by a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, a.entry_form, a.receive_basis, a.booking_no, a.booking_id";
			}
		}
		
		//echo $sql; die;
		$sqlData=sql_select($sql); $yarnCountDetarmiId=""; $planId="";
		if(count($sqlData)<1)
		{
			echo "55**".count($sqlData);
			die;
		}
		foreach($sqlData as $row)
		{
			if($row[csf('entry_form')]==2 || $row[csf('receive_basis')]==2)
			{
				$planId.= $row[csf('booking_id')].',';
			}
			$yarnCountDetarmiId.= $row[csf('febric_description_id')].',';
		}
		$planId=chop($planId,',');
		$yarnCountDetarmiId=chop($yarnCountDetarmiId,',');
		$plan_booking_arr=array(); $ycdRateArr=array();
		if($planId!="")
		{
			$knit_booking="select b.id, a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.status_active=1 and b.is_deleted=0 and b.id in (".$planId.")";
			$knit_booking_result =sql_select($knit_booking);
			foreach($knit_booking_result as $row)
			{
				$plan_booking_arr[$row[csf('id')]]=$row[csf('booking_no')];
			}
			unset($knit_booking_result);
		}
		
		if($yarnCountDetarmiId!="")
		{
			$ycdSql="select mst_id, rate from conversion_process_loss where process_id=1 and mst_id in (".$yarnCountDetarmiId.") and status_active=1 and is_deleted=0";
			//echo $ycdSql;
			$ycdSqlData =sql_select($ycdSql); 
			foreach($ycdSqlData as $yrow)
			{
				$ycdRateArr[$yrow[csf('mst_id')]]+=$yrow[csf('rate')];
			}
			unset($ycdSqlData);
			//print_r($ycdRateArr);
		}
		
		$dtlsDataArr=array(); $isRateFound=1;
		foreach($sqlData as $row)
		{
			$billFor=0; $booking_no="";
			if($row[csf('entry_form')]==2)
			{
				if($row[csf('receive_basis')]==0) { $billFor=1; $booking_no=""; }
				else if ($row[csf('receive_basis')]==2) $booking_no=$plan_booking_arr[$row[csf('booking_no')]];
				else if ($row[csf('receive_basis')]==1) $booking_no=$row[csf('booking_no')]; 
				else { $billFor=1; $booking_no=""; }
			}
			else if ($row[csf('entry_form')]==550)
			{
				if($row[csf('receive_basis')]==4) { $billFor=1; $booking_no=""; }
				else if($row[csf('receive_basis')]==2 || $row[csf('receive_basis')]==11) $booking_no=$row[csf('booking_no')];
				else { $billFor=1; $booking_no=""; }
			}
			
			if($booking_no!="") $ex_booking=explode('-',$booking_no); else $ex_booking="_";
			
			if(strtolower($ex_booking[1])=='fb' || strtolower($ex_booking[1])=='sb') $billFor=1;
			else if(strtolower($ex_booking[1])=='sm') $billFor=2;
			else if(strtolower($ex_booking[1])=='smn') $billFor=3;
			else if(strtolower($ex_booking[1])=='sbkd') $billFor=3;
			
			$strVal=$row[csf('id')].'__'.$row[csf('receive_date')].'__'.$row[csf('recv_number_prefix_num')].'__'.$row[csf('po_breakdown_id')].'__'.$row[csf('prod_id')].'__'.$row[csf('body_part_id')].'__'.$row[csf('febric_description_id')];
			//echo $row[csf('febric_description_id')];
			
			if($ycdRateArr[$row[csf('febric_description_id')]]==0 || $ycdRateArr[$row[csf('febric_description_id')]]=="") $isRateFound=0;
			
			$dtlsDataArr[$strVal]+=$row[csf('quantity')];
		}
		unset($sqlData);
		
		if($isRateFound==0)
		{
			echo "56**".$yarnCountDetarmiId;
			die;
		}
		
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		
		if($db_type==0)$year_cond=" and YEAR(insert_date)";	
		else if($db_type==2) $year_cond=" and TO_CHAR(insert_date,'YYYY')";	
		
		$new_bill_no=explode("*",return_mrr_number( $knitting_company, '', 'KNT', date("Y",time()), 5, "select prefix_no, prefix_no_num from  subcon_inbound_bill_mst where company_id=$knitting_company and process_id=2 $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
		
		//print_r($new_bill_no); die;
		
		$id=return_next_id( "id", "subcon_inbound_bill_mst", 1) ; 	
		$field_array="id, prefix_no, prefix_no_num, bill_no, company_id, location_id, party_location_id, bill_date, party_id, party_source, bill_for, process_id, inserted_by, insert_date, status_active, is_deleted";
		$data_array="(".$id.",'".$new_bill_no[1]."','".$new_bill_no[2]."','".$new_bill_no[0]."','".$knitting_company."','".$location_id."','".$location_id."','".date('d-M-Y')."','".$company_id."','".$partySource."','".$billFor."',2,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)"; 
		
		$return_no=$new_bill_no[0];
		$pageNameMsg="In-Bound->Knitting Bill Issue. Bill No:".$return_no;
		
		$id1=return_next_id( "id", "subcon_inbound_bill_dtls",1);
		$fieldDtlsArr ="id, mst_id, delivery_id, delivery_date, challan_no, order_id, item_id, febric_description_id, body_part_id, uom, delivery_qty, rate, amount, currency_id, process_id, inserted_by, insert_date";
		
		$add_comma=0; $dataDtlsArr="";
		foreach($dtlsDataArr as $str=>$recQty)
		{
			$exstr=explode("__",$str);
			$receive_date=$recSysChallan=$poid=$prod_id=$body_part_id=$yarnCountLibId="";
			
			$recId=$exstr[0];
			$receive_date=$exstr[1];
			$recSysChallan=$exstr[2];
			$poid=$exstr[3];
			$prod_id=$exstr[4];
			$body_part_id=$exstr[5];
			$yarnCountLibId=$exstr[6];
			
			$rate=$amount=0;
			
			$rate=$ycdRateArr[$yarnCountLibId];
			$amount=$recQty*$rate;
			
			if($amount!="")
			{
				if ($add_comma!=0) $dataDtlsArr .=",";
				$dataDtlsArr .="(".$id1.",".$id.",'".$recId."','".$receive_date."','".$recSysChallan."','".$poid."','".$prod_id."','".$yarnCountLibId."','".$body_part_id."',12,'".$recQty."','".$rate."','".$amount."',1,2,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id1=$id1+1;
				$add_comma++;
			}
		}
		
		$flag=1;
		//echo "insert into subcon_inbound_bill_mst (".$field_array.") values ".$data_array;die;
		$rID=sql_insert("subcon_inbound_bill_mst",$field_array,$data_array,1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		if($dataDtlsArr!="")
		{
			//echo "insert into subcon_inbound_bill_dtls (".$fieldDtlsArr.") values ".$dataDtlsArr;die;
			$rID1=sql_insert("subcon_inbound_bill_dtls",$fieldDtlsArr,$dataDtlsArr,1);
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		if($flag==1)
		{
			$rID2=execute_query( "update inv_receive_master set bill_no='$id' where id='$recId'",0);
			if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		}
		//echo "10**".$rID.'=='.$rID1.'=='.$rID2.'=='.$flag; die;
		if($flag==1)
		{
			if($db_type==0) mysql_query("COMMIT");
			else if($db_type==2) oci_commit($con);
		}
		else
		{
			if($db_type==0) mysql_query("ROLLBACK"); 
			else if($db_type==2) oci_rollback($con);	
		}
	}
	else if($partySource==3)
	{
		if($booking_without_order==0)//with Order
		{
			if($receive_basis==9)//Receive Basis Production
			{
				$sql="SELECT a.id, d.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, c.po_breakdown_id, sum(c.quantity) as quantity, sum(b.rate) as rate, sum(b.amount) as amount, d.receive_basis, d.booking_no, d.booking_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, inv_receive_master d
				 where a.id=b.mst_id and $bookingWithoutOrderCond=0 and b.id=c.dtls_id and d.id=a.booking_id and a.knitting_source=3 and a.company_id='$company_id' and a.knitting_company='$knitting_company' and a.location_id='$location_id' and c.trans_type=1 and a.entry_form=550 and c.entry_form=550 and a.receive_basis=9 and d.receive_basis!=4 and a.item_category=13 and d.entry_form=2 and c.trans_id!=0
				 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id='$recId'
				 group by a.id, d.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id, d.receive_basis, d.booking_no, d.booking_id";
			}
			else if($receive_basis==2 || $receive_basis==4 || $receive_basis==11) //Receive Basis WO/Booking Based, Independent, Service Booking Based
			{
				$sql="SELECT a.id, a.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, c.po_breakdown_id, sum(c.quantity) as quantity, sum(b.rate) as rate, sum(b.amount) as amount, a.receive_basis, a.booking_no, a.booking_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c
				where a.id=b.mst_id and $bookingWithoutOrderCond=0 and b.id=c.dtls_id and a.knitting_source=3 and a.company_id='$company_id' and a.knitting_company='$knitting_company' and a.location_id='$location_id' and c.trans_type=1 and a.entry_form=550 and c.entry_form=550 and a.item_category=13 and a.receive_basis in (2,4,11) and c.trans_id!=0
				and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id='$recId'
				group by a.id, a.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id, a.receive_basis, a.booking_no, a.booking_id";
			}
		}
		else if($booking_without_order==1)//with out Order
		{
			if($receive_basis==9)//Receive Basis Production
			{
				$sql="SELECT a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, sum(b.grey_receive_qnty) as quantity, c.entry_form, c.receive_basis, c.booking_no, c.booking_id, 0 as order_id 
					from inv_receive_master a, pro_grey_prod_entry_dtls b, inv_receive_master c 
					where a.id=b.mst_id and c.id=a.booking_id and a.booking_without_order=1 and a.knitting_source=3 and a.company_id='$company_id' and a.knitting_company='$knitting_company' and a.location_id='$location_id' and a.entry_form=550 and a.receive_basis=9 and a.item_category=13 and c.entry_form=2 and c.receive_basis in (0,1,2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.trans_id!=0 and a.id='$recId'
					group by a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.entry_form, c.receive_basis, c.booking_no, c.booking_id";
			}
			else if($receive_basis==2 || $receive_basis==4 || $receive_basis==11) //Receive Basis WO/Booking Based, Independent, Service Booking Based
			{
				$sql="SELECT a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, sum(b.grey_receive_qnty) as quantity, a.entry_form, a.receive_basis, a.booking_no, a.booking_id, 0 as order_id 
					from inv_receive_master a, pro_grey_prod_entry_dtls b
					where a.id=b.mst_id and a.booking_without_order=1 and a.knitting_source=3 and a.company_id='$company_id' and a.knitting_company='$knitting_company' and b.trans_id!=0 and a.location_id='$location_id' and a.entry_form=550 and a.item_category=13 and a.receive_basis in (2,4,11)
					and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='$recId'
					group by a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, a.entry_form, a.receive_basis, a.booking_no, a.booking_id";
			}
		}
		
		//echo $sql; die;
		$sqlData=sql_select($sql); $yarnCountDetarmiId=""; $planId="";
		if(count($sqlData)<1)
		{
			echo "55**".count($sqlData);
			die;
		}
		foreach($sqlData as $row)
		{
			if($row[csf('entry_form')]==2 || $row[csf('receive_basis')]==2)
			{
				$planId.= $row[csf('booking_id')].',';
			}
			$yarnCountDetarmiId.= $row[csf('febric_description_id')].',';
		}
		$planId=chop($planId,',');
		$yarnCountDetarmiId=chop($yarnCountDetarmiId,',');
		$plan_booking_arr=array(); $ycdRateArr=array();
		if($planId!="")
		{
			$knit_booking="select b.id, a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.status_active=1 and b.is_deleted=0 and b.id in (".$planId.")";
			$knit_booking_result =sql_select($knit_booking);
			foreach($knit_booking_result as $row)
			{
				$plan_booking_arr[$row[csf('id')]]=$row[csf('booking_no')];
			}
			unset($knit_booking_result);
		}
		
		if($yarnCountDetarmiId!="")
		{
			$ycdSql="select mst_id, rate from conversion_process_loss where process_id=1 and mst_id in (".$yarnCountDetarmiId.") and status_active=1 and is_deleted=0";
			//echo $ycdSql;
			$ycdSqlData =sql_select($ycdSql); 
			foreach($ycdSqlData as $yrow)
			{
				$ycdRateArr[$yrow[csf('mst_id')]]+=$yrow[csf('rate')];
			}
			unset($ycdSqlData);
			//print_r($ycdRateArr);
		}
		
		$dtlsDataArr=array(); $isRateFound=1;
		foreach($sqlData as $row)
		{
			$billFor=0; $booking_no="";
			if($row[csf('entry_form')]==2)
			{
				if($row[csf('receive_basis')]==0) { $billFor=1; $booking_no=""; }
				else if ($row[csf('receive_basis')]==2) $booking_no=$plan_booking_arr[$row[csf('booking_no')]];
				else if ($row[csf('receive_basis')]==1) $booking_no=$row[csf('booking_no')]; 
				else { $billFor=1; $booking_no=""; }
			}
			else if ($row[csf('entry_form')]==550)
			{
				if($row[csf('receive_basis')]==4) { $billFor=1; $booking_no=""; }
				else if($row[csf('receive_basis')]==2 || $row[csf('receive_basis')]==11) $booking_no=$row[csf('booking_no')];
				else { $billFor=1; $booking_no=""; }
			}
			
			if($booking_no!="") $ex_booking=explode('-',$booking_no); else $ex_booking="_";
			
			if(strtolower($ex_booking[1])=='fb' || strtolower($ex_booking[1])=='sb') $billFor=1;
			else if(strtolower($ex_booking[1])=='sm') $billFor=2;
			else if(strtolower($ex_booking[1])=='smn') $billFor=3;
			else if(strtolower($ex_booking[1])=='sbkd') $billFor=3;
			
			$strVal=$row[csf('id')].'__'.$row[csf('receive_date')].'__'.$row[csf('recv_number_prefix_num')].'__'.$row[csf('po_breakdown_id')].'__'.$row[csf('prod_id')].'__'.$row[csf('body_part_id')].'__'.$row[csf('febric_description_id')];
			//echo $row[csf('febric_description_id')];
			
			if($ycdRateArr[$row[csf('febric_description_id')]]==0 || $ycdRateArr[$row[csf('febric_description_id')]]=="") $isRateFound=0;
			
			$dtlsDataArr[$strVal]+=$row[csf('quantity')];
		}
		unset($sqlData);
		
		if($isRateFound==0)
		{
			echo "56**".$yarnCountDetarmiId;
			die;
		}
		
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		
		if($db_type==0) $year_cond=" and YEAR(insert_date)"; else if($db_type==2) $year_cond=" and TO_CHAR(insert_date,'YYYY')";	
		
		$new_bill_no=explode("*",return_mrr_number( $company_id, '', 'KNT', date("Y",time()), 5, "select prefix_no, prefix_no_num from subcon_outbound_bill_mst where company_id='$company_id' and process_id=2 $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
		
		//print_r($new_bill_no); die;
		
		$id=return_next_id( "id", "subcon_outbound_bill_mst", 1) ; 	
		$field_array="id, prefix_no, prefix_no_num, bill_no, company_id, location_id, bill_date, supplier_id, bill_for, process_id, inserted_by, insert_date, status_active, is_deleted";
		$data_array="(".$id.",'".$new_bill_no[1]."','".$new_bill_no[2]."','".$new_bill_no[0]."','".$company_id."','".$location_id."','".date('d-M-Y')."','".$knitting_company."','".$billFor."',2,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)"; 
		
		$return_no=$new_bill_no[0];
		$pageNameMsg="Out-Bound->Knitting Bill Entry[Gross]. Bill No:".$return_no;
		
		$id1=return_next_id( "id", "subcon_outbound_bill_dtls",1);
		$fieldDtlsArr ="id, mst_id, receive_id, receive_date, challan_no, order_id, item_id, body_part_id, febric_description_id, receive_qty, uom, rate, amount, currency_id, process_id, inserted_by, insert_date";
		
		$add_comma=0; $dataDtlsArr="";
		foreach($dtlsDataArr as $str=>$recQty)
		{
			$exstr=explode("__",$str);
			$receive_date=$recSysChallan=$poid=$prod_id=$body_part_id=$yarnCountLibId="";
			
			$recId=$exstr[0];
			$receive_date=$exstr[1];
			$recSysChallan=$exstr[2];
			$poid=$exstr[3];
			$prod_id=$exstr[4];
			$body_part_id=$exstr[5];
			$yarnCountLibId=$exstr[6];
			
			$rate=$amount=0;
			
			$rate=$ycdRateArr[$yarnCountLibId];
			$amount=$recQty*$rate;
			
			if($amount!="")
			{
				if ($add_comma!=0) $dataDtlsArr .=",";
				$dataDtlsArr .="(".$id1.",".$id.",'".$recId."','".$receive_date."','".$recSysChallan."','".$poid."','".$prod_id."','".$body_part_id."','".$yarnCountLibId."','".$recQty."',12,'".$rate."','".$amount."',1,2,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id1=$id1+1;
				$add_comma++;
			}
		}
		
		$flag=1;
		//echo "insert into subcon_inbound_bill_mst (".$field_array.") values ".$data_array;die;
		$rID=sql_insert("subcon_outbound_bill_mst",$field_array,$data_array,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		if($dataDtlsArr!="")
		{
			//echo "insert into subcon_inbound_bill_dtls (".$fieldDtlsArr.") values ".$dataDtlsArr;die;
			$rID1=sql_insert("subcon_outbound_bill_dtls",$fieldDtlsArr,$dataDtlsArr,0);
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		if($flag==1)
		{
			$rID2=execute_query( "update inv_receive_master set bill_no='$id' where id='$recId'",0);
			if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		}
		//echo "10**".$rID.'=='.$rID1.'=='.$rID2.'=='.$flag; die;
		if($flag==1)
		{
			if($db_type==0) mysql_query("COMMIT");
			else if($db_type==2) oci_commit($con);
		}
		else
		{
			if($db_type==0) mysql_query("ROLLBACK"); 
			else if($db_type==2) oci_rollback($con);	
		}
	}
	
	
	
	echo "0**".$pageNameMsg; 
	disconnect($con); 
	die;
}

    ?>
