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
$buyer_array = return_library_array("select id, short_name from  lib_buyer","id","short_name");
$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
$company_arr = return_library_array("select id, company_name from lib_company","id","company_name");

$sql_roll=sql_select("select id,mst_id,prod_id,order_id,no_of_roll as no_of_roll from pro_grey_prod_entry_dtls where status_active=1 and is_deleted=0");
foreach($sql_roll as $row)
{
	$roll_arr[$row[csf("mst_id")]][$row[csf("id")]]=$row[csf("no_of_roll")];
	
}
if($db_type==2) $select_year="to_char(a.insert_date,'YYYY') as job_year"; else if($db_type==0)	$select_year="year(a.insert_date) as job_year";			
$sql_job="select 
				b.id as po_id,
				b.po_number,
				a.job_no,
				a.job_no_prefix_num,
				$select_year
			from
				wo_po_details_master a, wo_po_break_down b
			where
				a.job_no=b.job_no_mst ";

//echo $sql_job;
$job_po_arr=array();
$sql_job_result=sql_select($sql_job);
//echo $sql_job;die;
foreach($sql_job_result as $row)
{
	$job_po_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
	$job_po_arr[$row[csf("po_id")]]["job_no"]=$row[csf("job_no_prefix_num")];
	$job_po_arr[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
	$job_po_arr[$row[csf("po_id")]]["job_year"]=$row[csf("job_year")];
}

$composition_arr=array();
$construction_arr=array();
$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
$data_array=sql_select($sql_deter);
if(count($data_array)>0)
{
	foreach( $data_array as $row )
	{
		$construction_arr[$row[csf('id')]]=$row[csf('construction')];
		if(array_key_exists($row[csf('id')],$composition_arr))
		{
		$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
		else
		{
		$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
	}
}


if($action=="load_php_update_form")
{

	$sql=sql_select("select a.id,a.receive_date,a.company_id,a.store_id,a.location_id,a.recv_number,a.challan_no,a.knitting_source,a.knitting_company,a.yarn_issue_challan_no,a.remarks,a.booking_no from   inv_receive_master a where  a.id='$data'  and a.entry_form=58 ");
	foreach($sql as $val)
	{
		echo "load_drop_down( 'requires/grey_fabric_receive_roll_controller', '".$val[csf("company_id")]."', 'load_drop_down_location', 'location_td');\n"; 
		echo "document.getElementById('cbo_location_name').value  = '".($val[csf("location_id")])."';\n";  
		echo "load_drop_down( 'requires/grey_fabric_receive_roll_controller', '".$val[csf("company_id")]."', 'load_drop_down_store', 'store_td');\n";
		echo "document.getElementById('txt_receive_date').value  = '".change_date_format($val[csf("receive_date")])."';\n";  
		echo "document.getElementById('cbo_store_name').value  = '".($val[csf("store_id")])."';\n"; 
		echo "document.getElementById('txt_yarn_issue_challan_no').value  = '".($val[csf("yarn_issue_challan_no")])."';\n"; 
		echo "document.getElementById('txt_remarks').value  = '".($val[csf("remarks")])."';\n";    
		echo "document.getElementById('update_id').value  = '".($val[csf("id")])."';\n";
		echo "document.getElementById('txt_recieved_id').value  = '".($val[csf("recv_number")])."';\n";
	}
}

if($action=="grey_item_details_update")
{

	$company_name_array=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$yean_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id", "yarn_count");
	$floor_name_array=return_library_array( "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1", "id", "floor_name");
	$machine_array=return_library_array( "select id, machine_no from lib_machine_name where category_id=1", "id", "machine_no");
	$data_array=sql_select("SELECT a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst");
	$po_details_array=array();
	foreach($data_array as $row)
	{
		$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no_prefix_num")];
		$po_details_array[$row[csf("po_id")]]['buyer_name']=$buyer_name_array[$row[csf("buyer_name")]];
		$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
		$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
		$po_details_array[$row[csf("po_id")]]['buyer_id']=$row[csf("buyer_name")];
	}
	
	$composition_arr=array(); $constructtion_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
 
	$data_array=sql_select("SELECT a.id, a.company_id, a.recv_number, a.receive_basis, a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.febric_description_id, b.gsm, b.width,b.body_part_id,b.yarn_lot,b.brand_id,b.shift_name,b.floor_id,b.machine_no_id,b.yarn_count,b.color_id,b.color_range_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2  and c.status_active=1 and c.is_deleted=0 and a.booking_without_order=0 ");
	$roll_details_array=array(); $barcode_array=array(); 
	foreach($data_array as $row)
	{
		
		$roll_details_array[$row[csf("barcode_no")]]['company_id']=$row[csf("company_id")];
		$roll_details_array[$row[csf("barcode_no")]]['recv_number']=$row[csf("recv_number")];
		$roll_details_array[$row[csf("barcode_no")]]['receive_basis']=$row[csf("receive_basis")];
		$roll_details_array[$row[csf("barcode_no")]]['receive_date']=change_date_format($row[csf("receive_date")]);
		$roll_details_array[$row[csf("barcode_no")]]['booking_no']=$row[csf("booking_no")];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_source_id']=$row[csf("knitting_source")];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_source']=$knitting_source[$row[csf("knitting_source")]];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_company_id']=$row[csf("knitting_company")];
		
		if($row[csf("knitting_source")]==1)
		{
		$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$company_name_array[$row[csf("knitting_company")]];
		}
		else if($row[csf("knitting_source")]==3)
		{
		$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$supplier_arr[$row[csf("knitting_company")]];
		}
		$roll_details_array[$row[csf("barcode_no")]]['body_part_id']=$row[csf("body_part_id")];
		$roll_details_array[$row[csf("barcode_no")]]['yarn_lot']=$row[csf("yarn_lot")];
		$roll_details_array[$row[csf("barcode_no")]]['brand_id']=$row[csf("brand_id")];
		$roll_details_array[$row[csf("barcode_no")]]['shift_name']=$row[csf("shift_name")];
		$roll_details_array[$row[csf("barcode_no")]]['floor_id']=$row[csf("floor_id")];
		$roll_details_array[$row[csf("barcode_no")]]['machine_no_id']=$row[csf("machine_no_id")];
		$roll_details_array[$row[csf("barcode_no")]]['yarn_count']=$row[csf("yarn_count")];
		$roll_details_array[$row[csf("barcode_no")]]['color_id']=$row[csf("color_id")];
		$roll_details_array[$row[csf("barcode_no")]]['color_range_id']=$row[csf("color_range_id")];
	
		$roll_details_array[$row[csf("barcode_no")]]['uom']=$row[csf("uom")];
		//$roll_details_array[$row[csf("barcode_no")]]['machine_no_id']=$row[csf("machine_no_id")];
		//$roll_details_array[$row[csf("barcode_no")]]['dtls_id']=$row[csf("dtls_id")];
		$roll_details_array[$row[csf("barcode_no")]]['prod_id']=$row[csf("prod_id")];
		$roll_details_array[$row[csf("barcode_no")]]['deter_id']=$row[csf("febric_description_id")];
		$roll_details_array[$row[csf("barcode_no")]]['gsm']=$row[csf("gsm")];
		$roll_details_array[$row[csf("barcode_no")]]['width']=$row[csf("width")];
		$roll_details_array[$row[csf("barcode_no")]]['roll_id']=$row[csf("roll_id")];
		$roll_details_array[$row[csf("barcode_no")]]['roll_no']=$row[csf("roll_no")];
		$roll_details_array[$row[csf("barcode_no")]]['po_breakdown_id']=$row[csf("po_breakdown_id")];
		$roll_details_array[$row[csf("barcode_no")]]['qnty']=number_format($row[csf("qnty")],2,'.','');
		$barcode_array[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
	}

	$data_array_mst=sql_select("SELECT a.id, a.company_id, a.recv_number,a.booking_no, a.receive_basis, a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.febric_description_id,b.trans_id, b.gsm,b.room,b.rack,b.self,b.bin_box,c.qnty,c.roll_no,b.width,b.body_part_id,b.yarn_lot,b.brand_id,b.shift_name,b.floor_id,b.machine_no_id,b.yarn_count,b.color_id,b.color_range_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=58 and c.entry_form=58 and a.id=$data and c.status_active=1 and c.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=0 ");
	
	foreach($data_array_mst as $row)
	{
		$booking_no_update=$row[csf("booking_no")];
		$roll_details_array[$row[csf("barcode_no")]]['mst_id']=$row[csf("id")];
		$roll_details_array[$row[csf("barcode_no")]]['company_id']=$row[csf("company_id")];
		$roll_details_array[$row[csf("barcode_no")]]['recv_number']=$row[csf("recv_number")];
		//$roll_details_array[$row[csf("barcode_no")]]['receive_basis']=$row[csf("receive_basis")];
		$roll_details_array[$row[csf("barcode_no")]]['receive_date']=change_date_format($row[csf("receive_date")]);
		$roll_details_array[$row[csf("barcode_no")]]['grey_sys_number']=$row[csf("booking_no")];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_source_id']=$row[csf("knitting_source")];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_source']=$knitting_source[$row[csf("knitting_source")]];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_company_id']=$row[csf("knitting_company")];
		$roll_details_array[$row[csf("barcode_no")]]['room']=$row[csf("room")];
		$roll_details_array[$row[csf("barcode_no")]]['rack']=$row[csf("rack")];
		$roll_details_array[$row[csf("barcode_no")]]['self']=$row[csf("self")];
		$roll_details_array[$row[csf("barcode_no")]]['bin_box']=$row[csf("bin_box")];
		$roll_details_array[$row[csf("barcode_no")]]['qnty']=$knitting_source[$row[csf("qnty")]];
		$roll_details_array[$row[csf("barcode_no")]]['roll_no']=$row[csf("roll_no")];
		
		
		if($row[csf("knitting_source")]==1)
		{
			$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$company_name_array[$row[csf("knitting_company")]];
		}
		else if($row[csf("knitting_source")]==3)
		{
			$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$supplier_arr[$row[csf("knitting_company")]];
		}

		$roll_details_array[$row[csf("barcode_no")]]['body_part_id']=$row[csf("body_part_id")];
		$roll_details_array[$row[csf("barcode_no")]]['yarn_lot']=$row[csf("yarn_lot")];
		$roll_details_array[$row[csf("barcode_no")]]['brand_id']=$row[csf("brand_id")];
		$roll_details_array[$row[csf("barcode_no")]]['shift_name']=$row[csf("shift_name")];
		$roll_details_array[$row[csf("barcode_no")]]['floor_id']=$row[csf("floor_id")];
		$roll_details_array[$row[csf("barcode_no")]]['machine_no_id']=$row[csf("machine_no_id")];
		$roll_details_array[$row[csf("barcode_no")]]['yarn_count']=$row[csf("yarn_count")];
		$roll_details_array[$row[csf("barcode_no")]]['color_id']=$row[csf("color_id")];
		$roll_details_array[$row[csf("barcode_no")]]['color_range_id']=$row[csf("color_range_id")];
		$roll_details_array[$row[csf("barcode_no")]]['roll_id']=$row[csf("roll_id")];
		$roll_details_array[$row[csf("barcode_no")]]['uom']=$row[csf("uom")];
		$roll_details_array[$row[csf("barcode_no")]]['trans_id']=$row[csf("trans_id")];
		$roll_details_array[$row[csf("barcode_no")]]['dtls_id']=$row[csf("dtls_id")];
		$roll_details_array[$row[csf("barcode_no")]]['prod_id']=$row[csf("prod_id")];
		$roll_details_array[$row[csf("barcode_no")]]['deter_id']=$row[csf("febric_description_id")];
		$roll_details_array[$row[csf("barcode_no")]]['gsm']=$row[csf("gsm")];
		$roll_details_array[$row[csf("barcode_no")]]['width']=$row[csf("width")];
		$roll_details_array[$row[csf("barcode_no")]]['roll_id']=$row[csf("roll_id")];
		//$roll_details_array[$row[csf("barcode_no")]]['roll_no']=$row[csf("roll_no")];
		$roll_details_array[$row[csf("barcode_no")]]['po_breakdown_id']=$row[csf("po_breakdown_id")];
		$roll_details_array[$row[csf("barcode_no")]]['qnty']=number_format($row[csf("qnty")],2,'.','');
		//$barcode_array[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
	}

	
$sql=sql_select("select a.id,a.delevery_date,a.company_id,a.order_status,a.location_id,a.buyer_id,c.barcode_no,c.id  as roll_id from  pro_grey_prod_delivery_mst a,  pro_grey_prod_delivery_dtls b,pro_roll_details c where a.id=b.mst_id and a.sys_number='".trim($booking_no_update)."' and a.entry_form=56  and b.roll_id=c.id and b.barcode_num=barcode_no" );

	?>

<div style="width:2450px;" id="">
        <form name="delivery_details" id="delivery_details" autocomplete="off" > 
	<div id="report_print" style="width:2450px;">
    <table width="2420" class="rpt_table" id="tbl_header" cellpadding="0" cellspacing="1" rules="all">
    	<thead>
        	<th width="50">Sl</th>
            <th width="80">Barcode</th>
            <th width="110">System Id</th>
            <th width="100">Progm/  Booking No</th>
            <th width="100">Production Basis</th>
            <th width="60">Prod. Id</th>
            <th width="60" >Current Delv.</th>
            <th width="80" >Roll</th>
            <th width="60">Room</th>
            <th width="60">Rack</th>
            <th width="60">Shelf</th>
            <th width="60">Bin/Box</th>   
            <th width="100">Knitting Source</th>
            <th width="70">Prd. date</th>
            <th width="40">Year</th>
            <th width="50">Job No</th>
            <th width="60">Buyer</th>
            <th width="90">Order No</th>
            <th width="100">Body Part</th>
            <th width="120">Construction </th>
            <th width="120">Composition</th>
            <th width="50">GSM</th>
            <th width="50">Dia</th>
            <th width="80">Fabric Color</th>
            <th width="80"> Color Range</th>    
         	 <th width="60">Yarn Lot</th>
            <th width="50"> UOM</th>
            <th width="70">Yarn Count</th> 
            <th width="70">	Brand</th>
            <th width="70">Shift Name</th>
            <th width="80">Prod. Floor</th>
            <th >Machine No.</th> 
        </thead>
    </table>
   
   
    
    </div>
       <div style="width:2420px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body">
    <table width="2420" class="rpt_table" id="table_body" cellpadding="0" cellspacing="1" rules="all">
    	<tbody>
        <?
				$total_row=count($sql);
				$current_row_array=array();
				$i=1;
				foreach($sql as $val)
				{
				    if ($i%2==0)  
					$bgcolor="#E9F3FF";
				    else
					$bgcolor="#FFFFFF";
					if($roll_details_array[$val[csf("barcode_no")]]['mst_id']!="")
					{
					 $checked="checked='checked'";	
					}
					else
					{
					$checked="";	
					}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" >
						<td width="50" align="center"><p><? echo $i; ?> 
                        &nbsp;&nbsp;&nbsp;<input type="checkbox" id="checkedId_<? echo $i; ?>" value="checkedId[]" <? echo $checked;  ?> value=""/>
						<input type="hidden" id="hidesysid_<? echo $i;?>" name="hidesysid_<? echo $i;?>" value=""  />
                        <input type="hidden" id="hiden_transid_<? echo $i;?>" name="hiden_transid[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['trans_id'];?>"  />
                        <input type="hidden" id="hidden_greyid_<? echo $i;?>" name="hidden_greyid[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['dtls_id'];?>"  />
                        <input type="hidden" id="hidden_rollid_<? echo $i;?>" name="hidden_rollid[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['roll_id'];?>"  />
                        &nbsp;
						</p></td>
                        <td width="80"><p><input type="hidden" id="hidesysnum_<? echo $i;?>" name="hidesysnum[]" value="<? echo $row[csf("recv_number")];?>"  />
                        <input type="hidden" id="hidenBarcode_<? echo $i;?>" name="hidenBarcode[]" value="<? echo $val[csf("barcode_no")];?>"  />
						<?
						echo $val[csf("barcode_no")]; 
						?>&nbsp;
						</p></td>
						<td width="110"><p><input type="hidden" id="hidenReceiveId_<? echo $i;?>" name="hidenReceiveId[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['mst_id'];?>"  />
						<?
						echo $roll_details_array[$val[csf("barcode_no")]]['grey_sys_number']; 
						?>&nbsp;
						</p></td>
						<td width="100" align="center"><p>
						<input type="hidden" id="hideprogrum_<? echo $i;?>" name="hideprogrum_<? echo $i;?>" value="<? echo $row[csf("prog_id")];?>"  />
						<? 
						if($roll_details_array[$val[csf("barcode_no")]]['receive_basis']==0) echo "Independent"; else
						 echo $roll_details_array[$val[csf("barcode_no")]]['booking_no'];  ?>&nbsp;
						</p></td>
                        <td width="100" align="center"><p>
                         <input type="hidden" id="txtBasis_<? echo $i;?>" name="txtBasis[]"  value="<? echo $roll_details_array[$val[csf("barcode_no")]]['receive_basis'];?>" >
					   <?
					   $receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
						echo  $receive_basis[$roll_details_array[$val[csf("barcode_no")]]['receive_basis']];
						?>&nbsp;
						</p></td>
                        <td width="60" align="center"><p>
						<input type="hidden" id="hideprodid_<? echo $i;?>" name="hideprodid_<? echo $i;?>" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['prod_id'];?>"  />
						<?  echo $roll_details_array[$val[csf("barcode_no")]]['prod_id']; ?>&nbsp;
						</p></td> 
                        <td width="60">
						<p>
                        <input type="hidden" id="hidden_delivery_qty_<? echo $i;?>" name="hidden_delivery_qty[]" class="text_boxes_numeric" style="width:60px;" value="<? echo  $roll_details_array[$val[csf("barcode_no")]]['qnty'];  ?>" />
						<input type="text" id="txtcurrentdelivery_<? echo $i;?>" name="txtcurrentdelivery[]" class="text_boxes_numeric" style="width:50px;" value="<? echo  $roll_details_array[$val[csf("barcode_no")]]['qnty']; $total_balance+=$roll_details_array[$val[csf("barcode_no")]]['qnty']; ?>" />

						&nbsp;</p></td>
						<td  width="80"><p>
						<input type="text" id="txtroll_<? echo $i;?>" name="txtroll[]" class="text_boxes_numeric" style="width:60px;" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['roll_no']; ?>" />
						<input type="hidden" id="hideroll_<? echo $i;?>" value="<? echo $update_row_check[$index_pk]["roll"]; $to_roll+=$roll_details_array[$val[csf("barcode_no")]]['roll_no']; ?>"  >
						&nbsp;</p></td>  
                         <td width="60" align="center"><p><input type="text"  class="text_boxes" id="txtRoom_<? echo $i;?>" name="txtRoom[]" style="width:040px;" value="<?  echo $roll_details_array[$val[csf("barcode_no")]]['room']; ?>" />
						
						</p></td>
                         <td width="60" align="center"><p>
						<input type="text"  class="text_boxes" id="txtRack_<? echo $i;?>" name="txtRack[]" style="width:40px;"  value="<?  echo $roll_details_array[$val[csf("barcode_no")]]['rack']; ?>"/>
						</p></td>
                        
                         <td width="60" align="center"><p>
						<input type="text"  class="text_boxes" id="txtSelf_<? echo $i;?>" name="txtSelf[]" style="width:40px;"  value="<?  echo $roll_details_array[$val[csf("barcode_no")]]['self']; ?>"/>
						</p></td>
                         <td width="60" align="center"><p>
						<input type="text"  class="text_boxes" id="txtBin_<? echo $i;?>" name="txtBin[]" style="width:40px;" / value="<?  echo $roll_details_array[$val[csf("barcode_no")]]['bin_box']; ?>">
						</p></td>
                       
						<td width="100" align="center"><p>
                        <input type="hidden" id="knittingsource_<? echo $i;?>" name="knittingsource[]"  value="<? echo $roll_details_array[$val[csf("barcode_no")]]['knitting_source_id'];?>" >
						<?
						echo $knitting_source[$roll_details_array[$val[csf("barcode_no")]]['knitting_source_id']]; 
						?>&nbsp;
						</p></td>
						<td width="70" align="center" id="receive_date"><p><?  if($roll_details_array[$val[csf("barcode_no")]]['receive_date']!='0000-00-00')  echo change_date_format($roll_details_array[$val[csf("barcode_no")]]['receive_date']); else echo ""; ?>&nbsp;</p></td>
                        
						<td width="40" align="center"><p><? echo $po_details_array[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]['year']; ?>&nbsp;</p></td>
						<td width="50" align="center"><p>
						<input type="hidden" id="hiddenPoId_<? echo $i;?>" name="hiddenPoId[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']; ?>"  />
						<? echo $po_details_array[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]['job_no']; ?>&nbsp;
						</p></td>
						<td width="60"><p>
						 <input type="hidden" id="hiddenBuyer_<? echo $i;?>" name="hiddenBuyer_<? echo $i;?>" value="<? echo $po_details_array[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]['buyer_id'];?>"  />
						<? echo $po_details_array[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]['buyer_name']; ?>&nbsp;</p></td>
						<td width="90"><p>
						<input type="hidden" id="hideorder_<? echo $i;?>" name="hideorder_<? echo $i;?>" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']; ?>"  />
						<? echo 
						  $po_details_array[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]['po_number']; ?>&nbsp;
						</p></td>
                        <td width="100"><p>
						<input type="hidden" id="hidden_bodypart_<? echo $i;?>" name="hidden_bodypart[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['body_part_id'] ;?>"  />
						<?  echo $body_part[$roll_details_array[$val[csf("barcode_no")]]['body_part_id']]; ?>&nbsp;
						</p></td>
						<td width="120"><p>
						<input type="hidden" id="hideconstruction_<? echo $i;?>" name="hideconstruction[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['deter_id']; ?>"  />
						<?  echo $constructtion_arr[$roll_details_array[$val[csf("barcode_no")]]['deter_id']]; ?>&nbsp;
						</p></td>
						<td width="120"><p>
						<input type="hidden" id="hidecomposition_<? echo $i;?>" name="hidecomposition[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['deter_id'];?>"  />
						<? echo $composition_arr[$roll_details_array[$val[csf("barcode_no")]]['deter_id']]; ?>&nbsp;
						</p></td>
						<td width="50" align="center"><p>
						<input type="hidden" id="hidegsm_<? echo $i;?>" name="hidegsm[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['gsm']; ?>"  />
						<? echo $roll_details_array[$val[csf("barcode_no")]]['gsm']; ?>&nbsp;
						</p></td>
						 <td width="50" align="center"><p>
						<input type="hidden" id="hidedia_<? echo $i;?>" name="hidedia[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['width']; ?>"  />
						<? echo $roll_details_array[$val[csf("barcode_no")]]['width']; ?>&nbsp;
						</p></td>
						
                        <td width="80" align="center"><p>
                        	<input type="hidden" id="hiddenColor_<? echo $i;?>" name="hiddenColor[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['color_id']; ?>"  />
						<? echo $color_arr[$roll_details_array[$val[csf("barcode_no")]]['color_id']]; ?>&nbsp;
						</p></td>
                         <td width="80" align="center"><p>
                         <input type="hidden" id="hiddenColorRange_<? echo $i;?>" name="hiddenColorRange[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['color_range_id']; ?>"  />
						<? echo $color_range[$roll_details_array[$val[csf("barcode_no")]]['color_range_id']]; ?>&nbsp;
						</p></td>
                        <td width="60" align="center" id="yean_lot_id"><p>
                              <input type="hidden" id="hidden_yeanlot_<? echo $i;?>" name="hidden_yeanlot[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['yarn_lot']; ?>"  />
						<? 
						 echo $roll_details_array[$val[csf("barcode_no")]]['yarn_lot']; ?>&nbsp;
						</p></td>
                         <td width="50" align="center"><p>
                           <input type="hidden" id="hiddenUom_<? echo $i;?>" name="hiddenUom[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['uom']; ?>"  />
						<? echo $unit_of_measurement[$roll_details_array[$val[csf("barcode_no")]]['uom']]; ?>&nbsp;
						</p></td>
                         <td width="70" align="center"><p>
                         <input type="hidden" id="hiddenYeanCount_<? echo $i;?>" name="hiddenYeanCount[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['yarn_count']; ?>"  />
						<?
						$yean_count="";
						foreach($roll_details_array[$val[csf("barcode_no")]]['yarn_count'] as $y_id)
						{
						 if($yean_count=="")	$yean_count=$yean_count_arr[$y_id];
						 else                   $yean_count.=",".$yean_count_arr[$y_id];
						}
						 echo $yean_count; ?>&nbsp;
						</p></td>
                          <td width="70" align="center" ><p>
                          <input type="hidden" id="hiddenBand_<? echo $i;?>" name="hiddenBand[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['brand_id']; ?>"  />
						<? echo $roll_details_array[$val[csf("barcode_no")]]['brand_id']; ?>&nbsp;
						</p></td>
                         <td width="70" align="center"><p>
                         <input type="hidden" id="hiddenShift_<? echo $i;?>" name="hiddenShift[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['shift_name']; ?>"  />
						<? echo $shift_name[$roll_details_array[$val[csf("barcode_no")]]['shift_name']]; ?>&nbsp;
						</p></td>
                     <td width="80" align="center"><p>
                     <input type="hidden" id="hiddenFloorId_<? echo $i;?>" name="hiddenFloorId[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['floor_id']; ?>"  />
						<? echo $floor_name_array[$roll_details_array[$val[csf("barcode_no")]]['floor_id']]; ?>&nbsp;
						</p></td>
                 	  <td  align="center"><p>
                      <input type="hidden" id="hiddenMachine_<? echo $i;?>" name="hiddenMachine[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['machine_no_id']; ?>"  />
						<? echo $machine_array[$roll_details_array[$val[csf("barcode_no")]]['machine_no_id']]; ?>&nbsp;
						</p></td>
					</tr>
					<?
					$i++;
				}
		?>
        </tbody>
	</table>
    <table width="2720" class="rpt_table" id="tbl_footer" cellpadding="0" cellspacing="1" rules="all">
    	<tfoot>
            <th width="50"></th>
            <th width="80"></th>
            <th width="110"></th>
            <th width="100"></th>
            <th width="100"></th>
            <th width="60">Total:
        	<input type="hidden" id="total_row" name="total_row" value="<? echo $total_row; ?>" /></th>
            <th width="60"><? echo number_format($total_balance,2); ?></th>
            <th   colspan="26"></th>
           
            
        </tfoot>
    </table>
    </div>

<?


}

if($action=="load_php_mst_form")
{
	
	$sql=sql_select("select a.id,a.delevery_date,a.company_id,a.knitting_source,a.knitting_company,a.order_status,a.location_id from  pro_grey_prod_delivery_mst a where  a.sys_number='$data'  and a.entry_form=56 ");
	
	
	foreach($sql as $val)
	{
		if($val[csf('knitting_source')]==1) $knit_comp=$company_arr[$val[csf('knitting_company')]]; 
		else $knit_comp=$supllier_arr[$val[csf('knitting_company')]];
		echo "document.getElementById('cbo_company_id').value  = '".($val[csf("company_id")])."';\n"; 
		echo "document.getElementById('cbo_knitting_source').value  = '".($val[csf("knitting_source")])."';\n"; 
		echo "document.getElementById('cbo_knitting_company').value  = '".($val[csf("knitting_company")])."';\n"; 
		echo "document.getElementById('txt_knitting_company').value  = '".$knit_comp."';\n"; 
		echo "load_drop_down( 'requires/grey_fabric_receive_roll_controller', '".$val[csf("company_id")]."', 'load_drop_down_location', 'location_td');\n"; 
		echo "document.getElementById('cbo_location_name').value  = '".($val[csf("location_id")])."';\n";  
		//echo "document.getElementById('txt_receive_date').value = '".($val[csf("delevery_date")])."';\n"; 
		echo "load_drop_down( 'requires/grey_fabric_receive_roll_controller', '".$val[csf("company_id")]."', 'load_drop_down_store', 'store_td');\n"; 
		echo "document.getElementById('hidden_delivery_id').value  = '".($val[csf("id")])."';\n";
	}
	
}

if($action=="load_php_challan_form")
{
	
	$sql=sql_select("select a.id,a.delevery_date,a.company_id,a.order_status,a.location_id,a.buyer_id from  pro_grey_prod_delivery_mst a where  a.sys_number='$data'  and a.entry_form=56 ");
	
	
	foreach($sql as $val)
	{
		echo "document.getElementById('cbo_company_id').value  = '".($val[csf("company_id")])."';\n"; 
		//echo "load_drop_down( 'requires/grey_fabric_receive_roll_controller', '".$val[csf("company_id")]."', 'load_drop_down_location', 'location_td');\n"; 
		echo "document.getElementById('cbo_location_name').value  = '".($val[csf("location_id")])."';\n";  
		//echo "document.getElementById('txt_receive_date').value = '".($val[csf("delevery_date")])."';\n"; 
		echo "load_drop_down( 'requires/grey_fabric_receive_roll_controller', '".$val[csf("company_id")]."', 'load_drop_down_store', 'store_td');\n"; 
		echo "document.getElementById('hidden_delivery_id').value  = '".($val[csf("id")])."';\n";
	}
	
}

if($action=="grey_item_details")
{
	
	$company_name_array=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$yean_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id", "yarn_count");
	$floor_name_array=return_library_array( "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1", "id", "floor_name");
	$machine_array=return_library_array( "select id, machine_no from lib_machine_name where category_id=1", "id", "machine_no");
	$data_array=sql_select("SELECT a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst");
	$po_details_array=array();
	foreach($data_array as $row)
	{
		$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no_prefix_num")];
		$po_details_array[$row[csf("po_id")]]['buyer_name']=$buyer_name_array[$row[csf("buyer_name")]];
		$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
		$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
		$po_details_array[$row[csf("po_id")]]['buyer_id']=$row[csf("buyer_name")];
	}
	
	$composition_arr=array(); $constructtion_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	

	$update_barcode_arr=array();
	
	$data_array_mst=sql_select("SELECT a.id, a.company_id, a.recv_number,a.booking_no, c.barcode_no, c.id as roll_id, c.roll_no  FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=58 and c.entry_form=58 and a.booking_no='$data' and c.status_active=1 and c.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=0 ");
	foreach($data_array_mst as $inf)
	{
		$update_barcode_arr[]="'".$inf[csf('barcode_no')]."'";
		
	}

	$data_array=sql_select("SELECT a.id, a.company_id, a.recv_number, a.receive_basis, a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.febric_description_id, b.gsm, b.width,b.body_part_id,b.yarn_lot,b.brand_id,b.shift_name,b.floor_id,b.machine_no_id,b.yarn_count,b.color_id,b.color_range_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2  and c.status_active=1 and c.is_deleted=0 and a.booking_without_order=0 ");
	$roll_details_array=array(); $barcode_array=array(); 
	foreach($data_array as $row)
	{
		$roll_details_array[$row[csf("barcode_no")]]['mst_id']=$row[csf("id")];
		$roll_details_array[$row[csf("barcode_no")]]['company_id']=$row[csf("company_id")];
		$roll_details_array[$row[csf("barcode_no")]]['recv_number']=$row[csf("recv_number")];
		$roll_details_array[$row[csf("barcode_no")]]['receive_basis']=$row[csf("receive_basis")];
		$roll_details_array[$row[csf("barcode_no")]]['receive_date']=change_date_format($row[csf("receive_date")]);
		$roll_details_array[$row[csf("barcode_no")]]['booking_no']=$row[csf("booking_no")];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_source_id']=$row[csf("knitting_source")];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_source']=$knitting_source[$row[csf("knitting_source")]];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_company_id']=$row[csf("knitting_company")];
		
		if($row[csf("knitting_source")]==1)
		{
			$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$company_name_array[$row[csf("knitting_company")]];
		}
		else if($row[csf("knitting_source")]==3)
		{
			$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$supplier_arr[$row[csf("knitting_company")]];
		}
		$roll_details_array[$row[csf("barcode_no")]]['body_part_id']=$row[csf("body_part_id")];
		$roll_details_array[$row[csf("barcode_no")]]['yarn_lot']=$row[csf("yarn_lot")];
		$roll_details_array[$row[csf("barcode_no")]]['brand_id']=$row[csf("brand_id")];
		$roll_details_array[$row[csf("barcode_no")]]['shift_name']=$row[csf("shift_name")];
		$roll_details_array[$row[csf("barcode_no")]]['floor_id']=$row[csf("floor_id")];
		$roll_details_array[$row[csf("barcode_no")]]['machine_no_id']=$row[csf("machine_no_id")];
		$roll_details_array[$row[csf("barcode_no")]]['yarn_count']=$row[csf("yarn_count")];
		$roll_details_array[$row[csf("barcode_no")]]['color_id']=$row[csf("color_id")];
		$roll_details_array[$row[csf("barcode_no")]]['color_range_id']=$row[csf("color_range_id")];
		$roll_details_array[$row[csf("barcode_no")]]['roll_id']=$row[csf("roll_id")];
		$roll_details_array[$row[csf("barcode_no")]]['uom']=$row[csf("uom")];
		//$roll_details_array[$row[csf("barcode_no")]]['machine_no_id']=$row[csf("machine_no_id")];
		$roll_details_array[$row[csf("barcode_no")]]['dtls_id']=$row[csf("dtls_id")];
		$roll_details_array[$row[csf("barcode_no")]]['prod_id']=$row[csf("prod_id")];
		$roll_details_array[$row[csf("barcode_no")]]['deter_id']=$row[csf("febric_description_id")];
		$roll_details_array[$row[csf("barcode_no")]]['gsm']=$row[csf("gsm")];
		$roll_details_array[$row[csf("barcode_no")]]['width']=$row[csf("width")];
		$roll_details_array[$row[csf("barcode_no")]]['roll_id']=$row[csf("roll_id")];
		$roll_details_array[$row[csf("barcode_no")]]['roll_no']=$row[csf("roll_no")];
		$roll_details_array[$row[csf("barcode_no")]]['po_breakdown_id']=$row[csf("po_breakdown_id")];
		$roll_details_array[$row[csf("barcode_no")]]['qnty']=number_format($row[csf("qnty")],2,'.','');
		$barcode_array[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
	}
	if(count($update_barcode_arr)>0) $update_barcode_cond=" and c.barcode_no not in (".implode(",",$update_barcode_arr).") ";
	

	$sql=sql_select("select a.id,a.delevery_date,a.company_id,a.order_status,a.location_id,a.buyer_id,c.barcode_no,b.id as dtls_id,c.id  as roll_id from  pro_grey_prod_delivery_mst a,  pro_grey_prod_delivery_dtls b,pro_roll_details c where a.id=b.mst_id and a.sys_number='$data' and a.entry_form=56  and b.roll_id=c.id and b.barcode_num=barcode_no $update_barcode_cond and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.is_deleted=0 and c.status_active=1 ");

	?>

<div style="width:2350px;" id="">
        <form name="delivery_details" id="delivery_details" autocomplete="off" > 
	<div id="report_print" style="width:2330px;">
    <table width="2300" class="rpt_table" id="tbl_header" cellpadding="0" cellspacing="1" rules="all">
    	<thead>
        	<th width="50">Sl</th>
            <th width="80">Barcode</th>
            <th width="110">System Id</th>
            <th width="100">Progm/  Booking No</th>
            <th width="100">Production Basis</th>
            <th width="60">Prod. Id</th>
            <th width="60" >Current Delv.</th>
            <th width="80" >Roll</th>
            <th width="60">Room</th>
            <th width="60">Rack</th>
            <th width="60">Shelf</th>
            <th width="60">Bin/Box</th>   
            <th width="100">Knitting Source</th>
            
            <th width="70">Prd. date</th>
            <th width="40">Year</th>
            <th width="50">Job No</th>
            
            <th width="60">Buyer</th>
            <th width="90">Order No</th>
            <th width="80">Body Part</th>
            <th width="100">Construction </th>
            <th width="100">Composition</th>
            <th width="50">GSM</th>
            <th width="50">Dia</th>
            <th width="80">Fabric Color</th>
            <th width="80"> Color Range</th>    
         	 <th width="60">Yarn Lot</th>
            <th width="50"> UOM</th>
            <th width="60">Yarn Count</th> 
            <th width="60">	Brand</th>
            <th width="60">Shift Name</th>
            <th width="60">Prod. Floor</th>
            <th >Machine No.</th> 
        </thead>
    </table>
   
   
    
    </div>
       <div style="width:2340px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body">
    <table width="2300" class="rpt_table" id="table_body" cellpadding="0" cellspacing="1" rules="all">
    	<tbody>
        <?
		
				
		
				$total_row=count($sql);
				$current_row_array=array();
				$i=1;
				foreach($sql as $val)
				{
					if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>"  onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="50" align="center"><p><? echo $i; ?> 
                        &nbsp;&nbsp;&nbsp;<input type="checkbox" id="checkedId_<? echo $i; ?>" value="checkedId[]" checked="checked" value="0"/>
						<input type="hidden" id="hidesysid_<? echo $i;?>" name="hidesysid_<? echo $i;?>" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['mst_id'];?>"  />
                        <input type="hidden" id="hiden_transid_<? echo $i;?>" name="hiden_transid[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['trans_id'];?>"  />
                        <input type="hidden" id="hidden_greyid_<? echo $i;?>" name="hidden_greyid[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['dtls_id'];?>"  />
                        <input type="hidden" id="hidden_rollid_<? echo $i;?>" name="hidden_rollid[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['roll_id'];?>"  />
                        &nbsp;
						</p></td>
                        <td width="80"><p><input type="hidden" id="hidesysnum_<? echo $i;?>" name="hidesysnum_<? echo $i;?>" value="<? echo $row[csf("recv_number")];?>"  />
                        <input type="hidden" id="hidenBarcode_<? echo $i;?>" name="hidenBarcode[]" value="<? echo $barcode_array[$val[csf("barcode_no")]];?>"  />
						<?
						echo $barcode_array[$val[csf("barcode_no")]]; 
						?>&nbsp;
						</p></td>
						<td width="110"><p><input type="hidden" id="hidenReceiveId_<? echo $i;?>" name="hidenReceiveId[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['mst_id'];?>"  />
						<?
						echo $roll_details_array[$val[csf("barcode_no")]]['recv_number']; 
						?>&nbsp;
						</p></td>
						<td width="100" align="center"><p>
						<input type="hidden" id="hideprogrum_<? echo $i;?>" name="hideprogrum_<? echo $i;?>" value="<? echo $row[csf("prog_id")];?>"  />
						<? if($roll_details_array[$val[csf("barcode_no")]]['receive_basis']==0) echo "Independent"; else  echo $roll_details_array[$val[csf("barcode_no")]]['booking_no']; ?>&nbsp;
						</p></td>
                        <td width="100" align="center"><p>
                         <input type="hidden" id="txtBasis_<? echo $i;?>" name="txtBasis[]"  value="<? echo $roll_details_array[$val[csf("barcode_no")]]['receive_basis'];?>" >
							<?
							  $receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
							  echo  $receive_basis[$roll_details_array[$val[csf("barcode_no")]]['receive_basis']];
									  
							
							?>&nbsp;
							</p></td>
                        <td width="60" align="center"><p>
						<input type="hidden" id="hideprodid_<? echo $i;?>" name="hideprodid_<? echo $i;?>" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['prod_id'];?>"  />
						<?  echo $roll_details_array[$val[csf("barcode_no")]]['prod_id']; ?>&nbsp;
						</p></td> 
                        <td width="60">
						<p>
                        <input type="hidden" id="hidden_delivery_qty_<? echo $i;?>" name="hidden_delivery_qty[]" class="text_boxes_numeric" style="width:50px;" value="<? echo  $roll_details_array[$val[csf("barcode_no")]]['qnty'];  ?>" />
						<input type="text" id="txtcurrentdelivery_<? echo $i;?>" name="txtcurrentdelivery[]" class="text_boxes_numeric" style="width:50px;" value="<? echo  $roll_details_array[$val[csf("barcode_no")]]['qnty']; $total_balance+=$roll_details_array[$val[csf("barcode_no")]]['qnty']; ?>" />

						&nbsp;</p></td>
						<td  width="80"><p>
						<input type="text" id="txtroll_<? echo $i;?>" name="txtroll[]" class="text_boxes_numeric" style="width:60px;" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['roll_no']; ?>" />
						<input type="hidden" id="hideroll_<? echo $i;?>" value="<? echo $update_row_check[$index_pk]["roll"]; $to_roll+=$roll_details_array[$val[csf("barcode_no")]]['roll_no']; ?>"  >
						&nbsp;</p></td>  
                           <td width="60" align="center"><p><input type="text"  class="text_boxes" id="txtRoom_<? echo $i;?>" name="txtRoom[]" style="width:45px;" />
						
						</p></td>
                         <td width="60" align="center"><p>
						<input type="text"  class="text_boxes" id="txtRack_<? echo $i;?>" name="txtRack[]" style="width:45px;" />
						</p></td>
                        
                         <td width="60" align="center"><p>
						<input type="text"  class="text_boxes" id="txtSelf_<? echo $i;?>" name="txtSelf[]" style="width:45px;" />
						</p></td>
                         <td width="60" align="center"><p>
						<input type="text"  class="text_boxes" id="txtBin_<? echo $i;?>" name="txtBin[]" style="width:45px;" />
						</p></td>
                       
						<td width="100" align="center"><p>
                        <input type="hidden" id="knittingsource_<? echo $i;?>" name="knittingsource[]"  value="<? echo $knitting_source[$roll_details_array[$val[csf("barcode_no")]]['knitting_source_id']];?>" >
						<?
						echo $knitting_source[$roll_details_array[$val[csf("barcode_no")]]['knitting_source_id']]; 
						?>&nbsp;
						</p></td>
						<td width="70" align="center" id="receive_date"><p><?  if($roll_details_array[$val[csf("barcode_no")]]['receive_date']!='0000-00-00')  echo change_date_format($roll_details_array[$val[csf("barcode_no")]]['receive_date']); else echo ""; ?>&nbsp;</p></td>
						<td width="40" align="center"><p><? echo $po_details_array[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]['year']; ?>&nbsp;</p></td>
						<td width="50" align="center"><p>
						<input type="hidden" id="hiddenPoId_<? echo $i;?>" name="hiddenPoId[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']; ?>"  />
						<? echo $po_details_array[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]['job_no']; ?>&nbsp;
						</p></td>
						<td width="60"><p>
						 <input type="hidden" id="hiddenBuyer_<? echo $i;?>" name="hiddenBuyer_<? echo $i;?>" value="<? echo $po_details_array[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]['buyer_id'];?>"  />
						<? echo $po_details_array[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]['buyer_name']; ?>&nbsp;</p></td>
						<td width="90"><p>
						<input type="hidden" id="hideorder_<? echo $i;?>" name="hideorder_<? echo $i;?>" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']; ?>"  />
						<?
						 echo  $po_details_array[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]['po_number']; ?>&nbsp;
						</p></td>
                        <td width="80"><p>
						<input type="hidden" id="hidden_bodypart_<? echo $i;?>" name="hidden_bodypart[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['body_part_id'] ;?>"  />
						<?  echo $body_part[$roll_details_array[$val[csf("barcode_no")]]['body_part_id']]; ?>&nbsp;
						</p></td>
						<td width="100"><p>
						<input type="hidden" id="hideconstruction_<? echo $i;?>" name="hideconstruction[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['deter_id'] ;?>"  />
						<?  echo $constructtion_arr[$roll_details_array[$val[csf("barcode_no")]]['deter_id']]; ?>&nbsp;
						</p></td>
						<td width="100"><p>
						<input type="hidden" id="hidecomposition_<? echo $i;?>" name="hidecomposition[]" value="<? echo $row[csf("detarmination_id")];?>"  />
						<? echo $composition_arr[$roll_details_array[$val[csf("barcode_no")]]['deter_id']]; ?>&nbsp;
						</p></td>
						<td width="50" align="center"><p>
						<input type="hidden" id="hidegsm_<? echo $i;?>" name="hidegsm[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['gsm']; ?>"  />
						<? echo $roll_details_array[$val[csf("barcode_no")]]['gsm']; ?>&nbsp;
						</p></td>
						 <td width="50" align="center"><p>
						<input type="hidden" id="hidedia_<? echo $i;?>" name="hidedia[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['width']; ?>"  />
						<? echo $roll_details_array[$val[csf("barcode_no")]]['width']; ?>&nbsp;
						</p></td>
						
                        <td width="80" align="center"><p>
                        	<input type="hidden" id="hiddenColor_<? echo $i;?>" name="hiddenColor[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['color_id']; ?>"  />
						<? echo $color_arr[$roll_details_array[$val[csf("barcode_no")]]['color_id']]; ?>&nbsp;
						</p></td>
                         <td width="80" align="center"><p>
                         <input type="hidden" id="hiddenColorRange_<? echo $i;?>" name="hiddenColorRange[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['color_range_id']; ?>"  />
						<? echo $color_range[$roll_details_array[$val[csf("barcode_no")]]['color_range_id']]; ?>&nbsp;
						</p></td>
                        <td width="60" align="center" id="yean_lot_id"><p>
                              <input type="hidden" id="hidden_yeanlot_<? echo $i;?>" name="hidden_yeanlot[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['yarn_lot']; ?>"  />
						<? 
						 echo $roll_details_array[$val[csf("barcode_no")]]['yarn_lot']; ?>&nbsp;
						</p></td>
                         <td width="50" align="center"><p>
                           <input type="hidden" id="hiddenUom_<? echo $i;?>" name="hiddenUom[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['uom']; ?>"  />
						<? echo $unit_of_measurement[$roll_details_array[$val[csf("barcode_no")]]['uom']]; ?>&nbsp;
						</p></td>
                         <td width="60" align="center"><p>
                         <input type="hidden" id="hiddenYeanCount_<? echo $i;?>" name="hiddenYeanCount[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['yarn_count']; ?>"  />
						<?
						$yean_count="";
						foreach($roll_details_array[$val[csf("barcode_no")]]['yarn_count'] as $y_id)
						{
						 if($yean_count=="")	$yean_count=$yean_count_arr[$y_id];
						 else                   $yean_count.=",".$yean_count_arr[$y_id];
						}
						 echo $yean_count; ?>&nbsp;
						</p></td>
                          <td width="60" align="center" ><p>
                          <input type="hidden" id="hiddenBand_<? echo $i;?>" name="hiddenBand[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['brand_id']; ?>"  />
						<? echo $roll_details_array[$val[csf("barcode_no")]]['brand_id']; ?>&nbsp;
						</p></td>
                         <td width="60" align="center"><p>
                         <input type="hidden" id="hiddenShift_<? echo $i;?>" name="hiddenShift[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['shift_name']; ?>"  />
						<? echo $shift_name[$roll_details_array[$val[csf("barcode_no")]]['shift_name']]; ?>&nbsp;
						</p></td>
                     <td width="60" align="center"><p>
                     <input type="hidden" id="hiddenFloorId_<? echo $i;?>" name="hiddenFloorId[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['floor_id']; ?>"  />
						<? echo $floor_name_array[$roll_details_array[$val[csf("barcode_no")]]['floor_id']]; ?>&nbsp;
						</p></td>
                 	  <td  align="center"><p>
                      <input type="hidden" id="hiddenMachine_<? echo $i;?>" name="hiddenMachine[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['machine_no_id']; ?>"  />
						<? echo $machine_array[$roll_details_array[$val[csf("barcode_no")]]['machine_no_id']]; ?>&nbsp;
						</p></td>
					</tr>
					<?
					$i++;
				}
		?>
        </tbody>
	</table>
    <table width="2300" class="rpt_table" id="tbl_footer" cellpadding="0" cellspacing="1" rules="all">
    	<tfoot>
        	
             <th width="50"></th>
            <th width="80"></th>
            <th width="110"></th>
            <th width="100"></th>
            <th width="100"></th>
            <th width="60">Total:
        	<input type="hidden" id="total_row" name="total_row" value="<? echo $total_row; ?>" /></th>
            <th width="60"><? echo number_format($total_balance,2); ?></th>
            <th   colspan="26"></th>
            
        </tfoot>
    </table>
    </div>

<?
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
		  $garments_nature=2;
		  $category_id=13; $entry_form=58; $prefix='KNGFRR';
		  if($db_type==0)
		   { 
		   $txt_receive_date=change_date_format($txt_receive_date,"yyyy-mm-dd");
		   }
		   else
		   {
		   $txt_receive_date=change_date_format($txt_receive_date,'yyyy-mm-dd',"-",1);  
		   }
		 if(str_replace("'","",$update_id)=="")
		 {
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			/*$new_grey_recv_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', $prefix, date("Y",time()), 5, "select recv_number_prefix, recv_number_prefix_num from inv_receive_master where company_id=$cbo_company_id and entry_form='$entry_form' and $year_cond=".date('Y',time())." order by id desc", "recv_number_prefix", "recv_number_prefix_num" ));
			$id=return_next_id( "id", "inv_receive_master", 1 ) ;*/
		
		
			$new_grey_recv_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,$cbo_company_id,'KNGFRR',58,date("Y",time()),13 ));
			$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
			
			
			$field_array="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, receive_basis, company_id, receive_date, challan_no, booking_id, booking_no, booking_without_order,store_id, location_id, knitting_source, knitting_company, yarn_issue_challan_no, remarks, fabric_nature, inserted_by, insert_date";
			$data_array="(".$id.",'".$new_grey_recv_system_id[1]."',".$new_grey_recv_system_id[2].",'".$new_grey_recv_system_id[0]."',$entry_form,$category_id,10,".$cbo_company_id.",'".$txt_receive_date."','".$txt_receive_chal_no."','".$hidden_delivery_id."','".$txt_challan_no."','0',".$cbo_store_name.",".$cbo_location_name.",".$cbo_knitting_source.",".$cbo_knitting_company.",'".$yarn_issue_challan_no."','".$txt_remarks."',".$garments_nature.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$grey_recv_num=$new_grey_recv_system_id[0];
			$grey_update_id=$id;
		}
		$receive_number=explode("*",$receive_number);
		$receive_basis=explode("*",$receive_basis);
		$barcode_id=explode("*",$barcode_id);
		$receive_id=explode("*",$receive_id);
		$program_id=explode("*",$program_id);
		$prod_id=explode("*",$prod_id);
		$room_no=explode("*",$room_no);
		$rack=explode("*",$rack);
		$self=explode("*",$self);
		$bin=explode("*",$bin);
		$txt_receive_qnty=explode("*",$issue_qty);
		$roll_no=explode("*",$roll_no);
		$knitting_source=explode("*",$knitting_source);
		$receive_date=explode("*",$receive_date);
		$buyer_id=explode("*",$buyer_id);
		$po_id=explode("*",$po_id);
		$dia=explode("*",$dia);
		$determination_id=explode("*",$determination_id);
		$body_part=explode("*",$body_part);
		$color_id=explode("*",$color_id);
		$color_range=explode("*",$color_range);
		$uom=explode("*",$uom);
		$gsm=explode("*",$gsm);
		$yean_cont=explode("*",$yean_cont);
		$band_id=explode("*",$band_id);
		$floor_id=explode("*",$floor_id);
		$shift_id=explode("*",$shift_id);
		$yean_lot=explode("*",$yean_lot);
		$roll_id=explode("*",$roll_id);
		$machine_name=explode("*",$machine_name);
		
		if($brand_id=="") $brand_id=0;
		if($color_id=="") $color_id=0;
		$field_array_trans="id, mst_id, receive_basis, pi_wo_batch_no, company_id, prod_id, item_category, transaction_type, transaction_date, store_id, brand_id, order_uom, order_qnty, order_rate, order_amount, cons_uom, cons_quantity, cons_reject_qnty, cons_rate, cons_amount, balance_qnty, balance_amount, floor_id, machine_id, room, rack, self, bin_box, inserted_by, insert_date";
		$field_array_prod_update="current_stock";
		$field_array_dtls="id, mst_id, trans_id, prod_id, body_part_id, febric_description_id, gsm, width, no_of_roll, order_id, grey_receive_qnty, reject_fabric_receive, rate, amount, uom, yarn_lot, yarn_count, brand_id, shift_name, floor_id, machine_no_id, room, rack, self, bin_box, color_id, color_range_id, stitch_length, inserted_by, insert_date";
		$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,roll_id, roll_no,barcode_no, inserted_by, insert_date";
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, inserted_by, insert_date";
		
		//$id_prop = return_next_id( "id", "order_wise_pro_details",1 );
		
		$i=0;
		//$id_dtls=return_next_id( "id", "pro_grey_prod_entry_dtls",1) ;
		//$id_roll =return_next_id( "id", "pro_roll_details", 1 );
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$cur_st_qnty=0;
		foreach($barcode_id as $row)
		{
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$id_dtls = return_next_id_by_sequence("PRO_GREY_PROD_DELI_DTLS_PK_SEQ", "pro_grey_prod_entry_dtls", $con);
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
			$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			
			$stock= return_field_value("current_stock","product_details_master","id=$prod_id[$i]");
			if($previour_prod==$prod_id[$i])
			{
			$cur_st_qnty+=str_replace("'","",$txt_receive_qnty[$i]);
			}
			else
			{
			$cur_st_qnty=0;
			$cur_st_qnty=$stock+str_replace("'","",$txt_receive_qnty[$i]);	
			}
			$update_id_arr[]=$prod_id[$i];
			$update_data_arr[$prod_id[$i]]=explode("*",("".$cur_st_qnty.""));
			if($data_array_roll!="") $data_array_roll.= ",";
			if($data_array_trans!="") $data_array_trans.= ",";
			if($data_array_dtls!="") $data_array_dtls.= ",";
			if($data_array_prop!="") $data_array_prop.= ",";
			$data_array_trans.="(".$id_trans.",".$grey_update_id.",10,'".$id_dtls."',".$cbo_company_id.",".$prod_id[$i].",".$category_id.",1,'".$txt_receive_date."',".$cbo_store_name.",'".$band_id[$i]."','".$uom[$i]."',".$txt_receive_qnty[$i].",'".$order_rate."','".$order_amount."','".$uom[$i]."','".$txt_receive_qnty[$i]."','".$txt_reject_fabric_recv_qnty."','".$cons_rate."','".$cons_amount."','".$txt_receive_qnty[$i]."','".$cons_amount."','".$floor_id[$i]."','".$machine_name[$i]."','".$room_no[$i]."','".$rack[$i]."','".$self[$i]."','".$bin[$i]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$data_array_dtls.="(".$id_dtls.",".$grey_update_id.",".$id_trans.",".$prod_id[$i].",'".$body_part[$i]."','".$determination_id[$i]."','".$gsm[$i]."','".$dia[$i]."','".$roll_no[$i]."','".$po_id[$i]."',".$txt_receive_qnty[$i].",'".$txt_reject_fabric_recv_qnty."','".$rate."','".$amount."','".$cbo_uom."','".$yean_lot[$i]."','".$yean_cont[$i]."','".$band_id[$i]."','".$shift_id[$i]."','".$floor_id[$i]."','".$machine_name[$i]."','".$room_no[$i]."','".$rack[$i]."','".$self[$i]."','".$bin[$i]."','".$color_id[$i]."','".$color_range[$i]."','".$txt_stitch_length."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$data_array_roll.="(".$id_roll.",".$grey_update_id.",".$id_dtls.",'".$po_id[$i]."',$entry_form,'".$txt_receive_qnty[$i]."','".$roll_id[$i]."','".$roll_no[$i]."','".$barcode_id[$i]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$data_array_prop.="(".$id_prop.",".$id_trans.",1,$entry_form,".$id_dtls.",'".$po_id[$i]."',".$prod_id[$i].",'".$color_id[$i]."','".$txt_receive_qnty[$i]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$previour_prod=$prod_id[$i];
			//$id_roll=$id_roll+1;
			//$id_prop=$id_prop+1;
			//$id_dtls++;
			//$id_trans++;
			$i++;
			
		}
		
		$rID=sql_insert("inv_receive_master",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
	
			$rID2=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_prod_update,$update_data_arr,$update_id_arr),1);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
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
			$rID5=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			} 
			$rID6=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			if($flag==1) 
			{
				if($rID6) $flag=1; else $flag=0; 
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
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "0**".$grey_update_id."**".$grey_recv_num."**0"."**".$txt_challan_no;
			}
			else
			{
				oci_rollback($con);
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		
		//check_table_status( $_SESSION['menu_id'],0);
				
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
		  if($db_type==0)
		   { 
		   $txt_receive_date=change_date_format($txt_receive_date,"yyyy-mm-dd");
		   }
		   else
		   {
		   $txt_receive_date=change_date_format($txt_receive_date,'yyyy-mm-dd',"-",1);  
		   }
		
		$garments_nature=2;
	    $category_id=13; $entry_form=58; 
		$field_array_update="receive_date*challan_no*store_id*location_id*knitting_source*knitting_company*remarks*updated_by*update_date";
		$data_array_update="'".$txt_receive_date."'*'".$txt_receive_chal_no."'*".$cbo_store_name."*".$cbo_location_name."*".$cbo_knitting_source."*".$cbo_knitting_company."*'".$txt_remarks."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$check_data=explode("*",$check_data);
		$receive_number=explode("*",$receive_number);
		$receive_basis=explode("*",$receive_basis);
		$barcode_id=explode("*",$barcode_id);
		$tran_id=explode("*",$tran_id);
		$gray_dtlsid=explode("*",$gray_dtlsid);
		$roll_id=explode("*",$roll_id);
		$receive_id=explode("*",$receive_id);
		$program_id=explode("*",$program_id);
		$prod_id=explode("*",$prod_id);
		$room_no=explode("*",$room_no);
		$rack=explode("*",$rack);
		$self=explode("*",$self);
		$bin=explode("*",$bin);
		$txt_receive_qnty=explode("*",$issue_qty);
		$roll_no=explode("*",$roll_no);
		$knitting_source=explode("*",$knitting_source);
		$receive_date=explode("*",$receive_date);
		$buyer_id=explode("*",$buyer_id);
		$po_id=explode("*",$po_id);
		$dia=explode("*",$dia);
		$determination_id=explode("*",$determination_id);
		$body_part=explode("*",$body_part);
		$color_id=explode("*",$color_id);
		$color_range=explode("*",$color_range);
		$uom=explode("*",$uom);
		$gsm=explode("*",$gsm);
		$yean_cont=explode("*",$yean_cont);
		$band_id=explode("*",$band_id);
		$floor_id=explode("*",$floor_id);
		$shift_id=explode("*",$shift_id);
		$yean_lot=explode("*",$yean_lot);
		$machine_name=explode("*",$machine_name);
		$hidden_qty=explode("*",$hidden_qty);
		$field_array_prod_update="current_stock";
		$field_array_trans_update="transaction_date*store_id*cons_quantity*room*rack*self*bin_box*updated_by*update_date";
		$field_array_dtls_update="grey_receive_qnty*room*rack*self*bin_box*updated_by*update_date";
		$field_array_roll_update="qnty* updated_by* update_date";
		$field_array_propo_update="quantity*updated_by*update_date";
		$field_array_trans_remove="updated_by*update_date*status_active*is_deleted";
		$field_array_dtls_remove="updated_by*update_date*status_active*is_deleted";
		$field_array_roll_remove="updated_by* update_date*status_active*is_deleted";
		$field_array_propor_remove="updated_by*update_date*status_active*is_deleted";
		$cur_st_qnty=0;
	
		$i=0;
		//********************************insert field *********************************
		$field_array_trans="id, mst_id, receive_basis, pi_wo_batch_no, company_id, prod_id, item_category, transaction_type, transaction_date, store_id, brand_id, order_uom, order_qnty, order_rate, order_amount, cons_uom, cons_quantity, cons_reject_qnty, cons_rate, cons_amount, balance_qnty, balance_amount, floor_id, machine_id, room, rack, self, bin_box, inserted_by, insert_date";
		$field_array_prod_update="current_stock";
		$field_array_dtls="id, mst_id, trans_id, prod_id, body_part_id, febric_description_id, gsm, width, no_of_roll, order_id, grey_receive_qnty, reject_fabric_receive, rate, amount, uom, yarn_lot, yarn_count, brand_id, shift_name, floor_id, machine_no_id,room, rack, self, bin_box, color_id, color_range_id, stitch_length, inserted_by, insert_date";
		$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,roll_id, roll_no,barcode_no, inserted_by, insert_date";
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, inserted_by, insert_date";
		
		/*$id_prop = return_next_id( "id", "order_wise_pro_details",1 );
		$id_dtls=return_next_id( "id", "pro_grey_prod_entry_dtls",1) ;
		$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;*/
		
		$update_roll_id=array();
		$update_array_roll=array();
		$update_array_dtls=array();
		foreach($barcode_id as $row)
			{
			if($tran_id[$i]!="")
				{
					if($check_data[$i]==1)
					{
					$stock=return_field_value("current_stock","product_details_master","id=$prod_id[$i]");
					
					$update_roll_id[]=$roll_id[$i];
					$update_array_roll[$roll_id[$i]]=explode("*",("".$txt_receive_qnty[$i]."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					//$update_array_roll[$roll_id[$i]]=explode("*",("".$txt_receive_qnty[$i].""));
					
					if($previour_prod==$prod_id[$i])
					{
					$cur_st_qnty+=str_replace("'","",$txt_receive_qnty[$i])-str_replace("'","",$hidden_qty[$i]);
					}
					else
					{
					$cur_st_qnty=0;
					$cur_st_qnty=$stock+str_replace("'","",$txt_receive_qnty[$i])-str_replace("'","",$hidden_qty[$i]);	
					}
					$update_prodid_arr[]=$prod_id[$i];
					$update_data_arr[$prod_id[$i]]=explode("*",("".$cur_st_qnty.""));
					
					$update_trans_id[]=$tran_id[$i];
					//$update_trans_arr[$tran_id[$i]]="('".$txt_receive_date."'*'".$cbo_store_name."'*".$txt_receive_qnty[$i]."*'".$room_no[$i]."'*'".$rack[$i]."'*'".$self[$i]."'*'".$bin[$i]."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."')";
					$update_trans_arr[$tran_id[$i]]=explode("*",("'".$txt_receive_date."'*'".$cbo_store_name."'*".$txt_receive_qnty[$i]."*'".$room_no[$i]."'*'".$rack[$i]."'*'".$self[$i]."'*'".$bin[$i]."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					
					$update_detl_id[]=$gray_dtlsid[$i];
					//$update_array_prop[$tran_id[$i]]==explode("*",("".$txt_receive_qnty[$i]."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					$update_array_dtls[$gray_dtlsid[$i]]=explode("*",("".$txt_receive_qnty[$i]."*'".$room_no[$i]."'*'".$rack[$i]."'*'".$self[$i]."'*'".$bin[$i]."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					
					
					$update_prop_id[]=$tran_id[$i];
					$update_array_prop[$tran_id[$i]]=explode("*",("".$txt_receive_qnty[$i]."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					
					$previour_prod=$prod_id[$i];
					}
					if($check_data[$i]==0)
					{
					
						$stock=return_field_value("current_stock","product_details_master","id=$prod_id[$i]");
						$remove_roll_id[]=$roll_id[$i];
						$remove_array_roll[$roll_id[$i]]=explode("*",("".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1"));
						//$update_array_roll[$roll_id[$i]]=explode("*",("".$txt_receive_qnty[$i].""));
						
						if($previour_prod==$prod_id[$i])
						{
						$cur_st_qnty-=str_replace("'","",$hidden_qty[$i]);
						}
						else
						{
						$cur_st_qnty=0;
						$cur_st_qnty=$stock-str_replace("'","",$hidden_qty[$i]);	
						}
						$update_prodid_arr[]=$prod_id[$i];
						$update_data_arr[$prod_id[$i]]=explode("*",("".$cur_st_qnty.""));
						$remove_trans_id[]=$tran_id[$i];
						$remove_trans_arr[$tran_id[$i]]=explode("*",("".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1"));
						$remove_detl_id[]=$gray_dtlsid[$i];
						$remove_array_dtls[$gray_dtlsid[$i]]=explode("*",("".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1"));
						
						if(str_replace("'", "", $tran_id[$i]))
						{
							$remove_prop_id[]=$tran_id[$i];
							$remove_array_prop[$tran_id[$i]]=explode("*",("".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1"));
						}
						$previour_prod=$prod_id[$i];	
						
					}
				}
				else
				{
					if($check_data[$i]==1)
					{
						
					$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					$id_dtls = return_next_id_by_sequence("PRO_GREY_PROD_DELI_DTLS_PK_SEQ", "pro_grey_prod_entry_dtls", $con);
					$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
					$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
						
						
					$stock= return_field_value("current_stock","product_details_master","id=$prod_id[$i]");
					if($previour_prod==$prod_id[$i])
					{
					$cur_st_qnty+=str_replace("'","",$txt_receive_qnty[$i]);
					}
					else
					{
					$cur_st_qnty=0;
					$cur_st_qnty=$stock+str_replace("'","",$txt_receive_qnty[$i]);	
					}
					$update_prodid_arr[]=$prod_id[$i];
					$update_data_arr[$prod_id[$i]]=explode("*",("".$cur_st_qnty.""));
					if($data_array_roll!="") $data_array_roll.= ",";
					if($data_array_trans!="") $data_array_trans.= ",";
					if($data_array_dtls!="") $data_array_dtls.= ",";
					if($data_array_prop!="") $data_array_prop.= ",";
					
					$data_array_trans.="(".$id_trans.",".$update_id.",10,'".$id_dtls."',".$cbo_company_id.",".$prod_id[$i].",".$category_id.",1,'".$txt_receive_date."',".$cbo_store_name.",'".$band_id[$i]."','".$uom[$i]."',".$txt_receive_qnty[$i].",'".$order_rate."','".$order_amount."','".$uom[$i]."','".$txt_receive_qnty[$i]."','".$txt_reject_fabric_recv_qnty."','".$cons_rate."','".$cons_amount."','".$txt_receive_qnty[$i]."','".$cons_amount."','".$floor_id[$i]."','".$machine_name[$i]."','".$room_no[$i]."','".$rack[$i]."','".$self[$i]."','".$bin[$i]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					
					$data_array_dtls.="(".$id_dtls.",".$update_id.",".$id_trans.",".$prod_id[$i].",'".$body_part[$i]."','".$determination_id[$i]."','".$gsm[$i]."','".$dia[$i]."','".$roll_no[$i]."','".$po_id[$i]."',".$txt_receive_qnty[$i].",'".$txt_reject_fabric_recv_qnty."','".$rate."','".$amount."','".$cbo_uom."','".$yean_lot[$i]."','".$yean_cont[$i]."','".$band_id[$i]."','".$shift_id[$i]."','".$floor_id[$i]."','".$machine_name[$i]."','".$room_no[$i]."','".$rack[$i]."','".$self[$i]."','".$bin[$i]."','".$color_id[$i]."','".$color_range[$i]."','".$txt_stitch_length."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$data_array_roll.="(".$id_roll.",".$update_id.",".$id_dtls.",'".$po_id[$i]."',$entry_form,'".$txt_receive_qnty[$i]."','".$roll_id[$i]."','".$roll_no[$i]."','".$barcode_id[$i]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$data_array_prop.="(".$id_prop.",".$id_trans.",1,$entry_form,".$id_dtls.",'".$po_id[$i]."',".$prod_id[$i].",'".$color_id[$i]."','".$txt_receive_qnty[$i]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$previour_prod=$prod_id[$i];
					//$id_roll=$id_roll+1;
					//$id_prop=$id_prop+1;
					//$id_dtls++;
					//$id_trans++;
					}
				}
				$i++;
				
			}
	
	    $rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,0);
		if($rID) $flag=1; else $flag=10;
		
        if($update_data_arr!="")
	 	 {
		  $update_product=execute_query(bulk_update_sql_statement(" product_details_master","id",$field_array_prod_update,$update_data_arr,$update_prodid_arr),1);
			if($flag==1) 
			{
			if($update_product) $flag=1; else $flag=0; 
			} 
	 	 }

		 if($remove_trans_arr!="")
	 	 {
		    $remove_tran=execute_query(bulk_update_sql_statement(" inv_transaction","id",$field_array_trans_remove,$remove_trans_arr,$remove_trans_id),1);
			if($flag==1) 
			{
			if($remove_tran) $flag=1; else $flag=0; 
			} 
	 	 }
		 if($remove_array_dtls!="")
	 	 {
		    $remove_grey=execute_query(bulk_update_sql_statement("pro_grey_prod_entry_dtls","id",$field_array_dtls_remove,$remove_array_dtls,$remove_detl_id),1);
			if($flag==1) 
			{
			if($remove_grey) $flag=1; else $flag=0; 
			} 
	 	 }
		 if($remove_array_roll!="")
	 	 {
		  $remove_roll=execute_query(bulk_update_sql_statement(" pro_roll_details","id",$field_array_roll_remove,$remove_array_roll,$remove_roll_id),1);
		  if($flag==1) 
		  {
		  if($remove_roll) $flag=1; else $flag=0; 
		  } 
	 	 }
		
	    if($remove_array_prop!="")
	 	 {
			 			
		    $remove_order=execute_query(bulk_update_sql_statement(" order_wise_pro_details","trans_id",$field_array_propor_remove,$remove_array_prop,$remove_prop_id),1);
			if($flag==1) 
			{
			if($remove_order) $flag=1; else $flag=0; 
			} 
		
	 	 }
			
	//***************************************************************************************************************************************
		
	  if($update_array_roll!="")
	 	 {
						
		    $update_roll=execute_query(bulk_update_sql_statement("pro_roll_details","id",$field_array_roll_update,$update_array_roll,$update_roll_id),1);
			if($flag==1) 
			{
				if($update_roll) $flag=1; else $flag=0; 
			} 
	 	 }
		
		 if($update_array_dtls!="")
	 	 {
			 			
		    $update_grey_prod=execute_query(bulk_update_sql_statement(" pro_grey_prod_entry_dtls","id",$field_array_dtls_update,$update_array_dtls,$update_detl_id),1);
			if($flag==1) 
			{
				if($update_grey_prod) $flag=1; else $flag=0; 
			} 
		
	 	 }
		
		 if($update_trans_arr!="")
	 	 {
			 			
		    $update_trans=execute_query(bulk_update_sql_statement(" inv_transaction","id",$field_array_trans_update,$update_trans_arr,$update_trans_id),1);
			if($flag==1) 
			{
				if($update_trans) $flag=1; else $flag=0; 
			} 
	
	 	 }
		
		 if($update_array_prop!="")
	 	 {
			 			
		    $update_order=execute_query(bulk_update_sql_statement(" order_wise_pro_details","trans_id",$field_array_propo_update,$update_array_prop,$update_prop_id),1);
			if($flag==1) 
			{
				if($update_order) $flag=1; else $flag=0; 
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
			
	
		if($data_array_trans!="")
		{
			$rID3=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			} 
		}
		if($data_array_dtls!="")
		{
			//echo "insert into pro_grey_prod_entry_dtls($field_array_dtls)values".$data_array_dtls;die;
			$rID4=sql_insert("pro_grey_prod_entry_dtls",$field_array_dtls,$data_array_dtls,0);
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			} 
		
		 }
		
		if($data_array_roll!="")
		{
			$rID5=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			} 
		 }
		if($data_array_prop!="")
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
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_recieved_id)."**0";
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
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_recieved_id)."**0";
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


if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 151, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "" );
	exit();
}

if ($action=="load_drop_down_store")
{
	$data=explode("_",$data);
	$category_id=13; 
	echo create_drop_down( "cbo_store_name", 152, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[0]' and b.category_type=$category_id and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
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




if($action=="challan_popup")
{
	echo load_html_head_contents("Challan Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
	
		function js_set_value(data,id)
		{
			$('#hidden_data').val(data);
			$('#hidden_receive_id').val(id);
			
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:760px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:760px; margin-left:2px">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Company</th>
                    <th>Delivery Date Range</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="180">Please Enter Challan No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                    	<input type="hidden" name="hidden_data" id="hidden_data">  
                        <input type="hidden" name="hidden_receive_id" id="hidden_receive_id">  
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">
                    	 <? echo create_drop_down( "cbo_company_id", 150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '-- Select Company --',0,"",0); ?>        
                    </td>
                    <td align="center">
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
					  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
					</td>
                    <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Challan No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
						?>
                    </td>     
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_company_id').value, 'create_challan_search_list_view', 'search_div', 'grey_fabric_receive_roll_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_challan_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0]);
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];

	if($company_id==0) { echo "Please Select Company First."; die; }
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and delevery_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and delevery_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	$search_field_cond="";
	if(trim($data[0])!="")
	{
	if($search_by==1) $search_field_cond="and sys_number like '$search_string'";
	}
	
	if($db_type==0) 
	{
	$year_field="YEAR(insert_date) as year,";
	}
	else if($db_type==2) 
	{
	$year_field="to_char(insert_date,'YYYY') as year,";
	}
	else $year_field="";//defined Later
	
	
	$data_array=sql_select("SELECT c.barcode_no,a.sys_number FROM pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b,pro_roll_details c 
	WHERE  a.id=b.mst_id and b.id=c.dtls_id and  c.entry_form=56 and a.entry_form=56 and company_id=$company_id and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	$challan_barcode=array();
	$inserted_barcode=array();
	foreach($data_array as $val)
	{
	$challan_barcode[$val[csf('sys_number')]][]=$val[csf('barcode_no')];
	}

	$inserted_roll=sql_select("select b.booking_no,a.barcode_no from pro_roll_details a,inv_receive_master b where a.mst_id=b.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and a.entry_form=58 and company_id=$company_id and b.entry_form=58");
	foreach($inserted_roll as $b_id)
	{
	$inserted_barcode[$b_id[csf('booking_no')]][]=$b_id[csf('barcode_no')];	
	}
	
	$sql = "select id, $year_field sys_number_prefix_num, sys_number, company_id, knitting_source, knitting_company, delevery_date from pro_grey_prod_delivery_mst where entry_form=56 and status_active=1 and is_deleted=0 and company_id=$company_id $search_field_cond $date_cond"; 
	//echo $sql;//die;
	$result = sql_select($sql);

	//$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	//$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="140">Company</th>
            <th width="80">Challan No</th>
            <th width="70">Year</th>
            <th width="120">Knitting Source</th>
            <th width="140">Knitting Company</th>
            <th>Delivery date</th>
        </thead>
	</table>
	<div style="width:740px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
			
			if(count($challan_barcode[$row[csf('sys_number')]])-count($inserted_barcode[$row[csf('sys_number')]])>0)
		     {
				 
				
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				$knit_comp="&nbsp;";
				if($row[csf('knitting_source')]==1) $knit_comp=$company_arr[$row[csf('knitting_company')]]; 
				else $knit_comp=$supllier_arr[$row[csf('knitting_company')]];
				$data_all=$row[csf('sys_number')]."_".$row[csf('company_id')]."_".$company_arr[$row[csf('company_id')]]."_".$row[csf('knitting_source')]."_".$row[csf('knitting_company')]."_".$knit_comp;
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $data_all; ?>','<? echo $row[csf('id')]; ?>');"> 
                    <td width="40"><? echo $i; ?></td>
                    <td width="140"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                    <td width="80"><p>&nbsp;<? echo $row[csf('sys_number_prefix_num')]; ?></p></td>
                    <td width="70" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="120"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?>&nbsp;</p></td>
                    <td width="140"><p><? echo $knit_comp; ?>&nbsp;</p></td>
                    <td align="center"><? echo change_date_format($row[csf('delevery_date')]); ?></td>
                </tr>
        	<?
            $i++;
            }
			}
        	?>
        </table>
    </div>
<?	
exit();
}











if ($action=="grey_receive_popup_search")
{
	echo load_html_head_contents("Grey Receive Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 
	<script>
		function js_set_value(data,id)
		{
			$('#hidden_data').val(data);
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
                    <th>Company</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="150">Receive ID</th>
                     <th>Received Date Range</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                    	<input type="hidden" name="hidden_recv_id" id="hidden_recv_id" class="text_boxes" value="">  
                        <input type="hidden" name="hidden_data" id="hidden_data"> 
                    </th> 
                </thead>
                <tr class="general">
                   <td width="">
						<? 
							echo create_drop_down( "cbo_company_name", 151, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "","");
                        ?>
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
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To
					  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					</td>						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_company_name').value, 'create_grey_recv_search_list_view', 'search_div', 'grey_fabric_receive_roll_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
    $entry_form=58; $garments_nature=2;
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
	
	$sql = "select id, recv_number_prefix_num, recv_number,booking_id,company_id, booking_no, knitting_source, knitting_company, receive_date, challan_no, $year_field as year from inv_receive_master where entry_form=$entry_form and fabric_nature=$garments_nature and status_active=1 and is_deleted=0 and company_id=$company_id  $search_field_cond $date_cond"; 
	//echo $sql;die;
	$result = sql_select($sql);

	//$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
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
                
				$knit_comp="&nbsp;";
				if($row[csf('knitting_source')]==1)
					$knit_comp=$company_arr[$row[csf('knitting_company')]]; 
				else
					$knit_comp=$supllier_arr[$row[csf('knitting_company')]];
				
				$data_all=$row[csf('recv_number')]."_".$row[csf('company_id')]."_".$company_arr[$row[csf('company_id')]]."_".$row[csf('knitting_source')]."_".$row[csf('knitting_company')]."_".$knit_comp."_".$row[csf('booking_id')];
				$recv_qnty=$grey_recv_arr[$row[csf('id')]];
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $data_all; ?>','<? echo $row[csf('id')]; ?>');"> 
                    <td width="35"><? echo $i; ?></td>
                    <td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="70"><p>&nbsp;<? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
                    <td width="120"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>               
                    <td width="100"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
                    <td width="110"><p><? echo $knit_comp; ?></p></td>
                    <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td width="90" align="right"><? echo number_format($recv_qnty,2,'.',''); ?></td>
                    <td width="75"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
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
?>
