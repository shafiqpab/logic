<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location") {	
	echo create_drop_down( 'cbo_location_name', 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_buyer")
{ 
	list($company,$type)=explode("_",$data);
	if($type==1)
	{
		echo create_drop_down( "cbo_customer_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $company, "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_customer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", "", "" );
	}	
	exit();	 
} 

if($action=="load_drop_down_subsection")
{
	$data=explode('_',$data);
	if($data[0]==1) $subID='1,2,3';
	else if($data[0]==3) $subID='4,5';
	else if($data[0]==5) $subID='6,7,8,9,10,11,12,13';
	else if($data[0]==10) $subID='14,15';
	else $subID='0';
	//echo $data[0]."**".$subID;
	//echo create_drop_down( "cboembtype_".$data[1], 80,$emb_type,"", 1, "-- Select --", "", "","","" ); 
	echo create_drop_down( "cbo_sub_section_id", 100, $trims_sub_section,"",1, "-- Select Sub-Section --","","",0,$subID,'','','','','',"");
	exit();
}

if ($action=="order_popup")
{	
	echo load_html_head_contents("Order Search","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	function js_set_value(id)
	{
		//alert(booking_no); 
		document.getElementById('hidd_booking_data').value=id;
		parent.emailwindow.hide();
	}
	
</script>
</head>
<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
                    <tr>                	 
                        <th width="120">Delv Chlln No</th>
                        <th colspan="2" width="160">Delv Chlln Date</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" id="hidd_booking_data">
                        </th>
                    </tr>                                 
                </thead>
                <tr class="general">
                    <td><input name="txt_order_number" id="txt_order_number" class="text_boxes" style="width:90px"></td>
                    <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From"></td>
                    <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To"></td> 
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_order_number').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company;?>+'_'+<? echo $party_name;?>+'_'+<? echo $customer_source;?>, 'create_booking_search_list_view', 'search_div', 'date_wise_delivery_report_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
                </tr>
            </table>
            <div id="search_div"></div>   
        </form>
    </div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
if($action=="create_booking_search_list_view")
{	
	$data=explode('_',$data);
	//print_r($data);
	
	if ($data[3]!=0) $company=" and a.company_id='$data[3]'";
	if ($data[4]!=0) $party=" and a.party_id='$data[4]'";
	if($db_type==0)
	{
		if ($data[1]!="" &&  $data[2]!="") $delivery_date = "and a.delivery_date between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	else if($db_type==2)
	{
		if ($data[1]!="" &&  $data[2]!="")  $delivery_date= "and a.delivery_date between '".change_date_format($data[1], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	} 
	
	if ($data[0]!="") $woorder_cond=" and a.trims_del like '%$data[0]%' "; 
	
	$sql= "select a.id, a.trims_del, a.del_no_prefix, a.del_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id,  a.delivery_date, a.received_id, a.order_id, a.challan_no, a.gate_pass_no from trims_delivery_mst a, trims_delivery_dtls b where a.entry_form=208 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $delivery_date $woorder_cond  $company $party_id_cond $withinGroup $search_com $withinGroup group by a.id, a.trims_del, a.del_no_prefix, a.del_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id, a.delivery_date, a.received_id, a.order_id, a.challan_no, a.gate_pass_no order by a.id DESC";
	
	

	$data_array=sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="440" >
        <thead>
            <th width="30">SL</th>
            <th width="60">Delv Chlln No</th>
            <th width="70">Delv Chlln Date</th>
        </thead>
        </table>
        <div style="width:440px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="420" class="rpt_table" id="list_view">
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('trims_del')].'_'.$row[csf('currency_id')].'_'.$row[csf('within_group')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60" align="center"><? echo $row[csf('trims_del')]; ?></td>
                    <td width="70"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
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

if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	////////////////////////////////////////////////
	$cbo_company_id=str_replace("'","", $cbo_company_id);
	$cbo_location_name=str_replace("'","", $cbo_location_name);
	$cbo_customer_source=str_replace("'","", $cbo_customer_source);
	$cbo_customer_name=str_replace("'","", $cbo_customer_name);
	$txt_order_no=str_replace("'","", $txt_order_no);
	$hid_order_id=str_replace("'","", $hid_order_id);
	$cbo_section_id=str_replace("'","", $cbo_section_id);
	$cbo_sub_section_id=str_replace("'","", $cbo_sub_section_id);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	$internal_no=str_replace("'","", $txt_internal_no);
	
	if($cbo_company_id){$where_con.=" and a.company_id='$cbo_company_id'";} 
	if($cbo_section_id){$where_con.=" and b.section='$cbo_section_id'";} 
	if($cbo_sub_section_id){$where_con.=" and c.sub_section='$cbo_sub_section_id'";} 
	if($cbo_customer_source){$where_con.=" and a.within_group='$cbo_customer_source'";} 
	if($cbo_customer_name){$where_con.=" and a.party_id='$cbo_customer_name'";} 
	if($cbo_location_name){$where_con.=" and a.location_id='$cbo_location_name'";} 
	
	//////////////////////////////////////////////////////////////////
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 ","id","buyer_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 ","id","supplier_name");
	$trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$colorNameArr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	 
	$machine_noArr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0","id","machine_no");
	//////////////////////////////////////////////////////////////////
	
		if($txt_date_from!="" and $txt_date_to!="")
		{	
			$where_con.=" and a.delivery_date between '$txt_date_from' and '$txt_date_to'";
		}
		
		
	if(trim($txt_order_no)!="")
	{
			$sql_cond="and a.trims_del like '%$txt_order_no%'";
	}
			
	$buyer_po_id_cond = '';
    if($cbo_customer_source==1 || $cbo_customer_source==0)
    {
		if($internal_no !="") $internal_no_cond = " and grouping like('%$internal_no%')";
		
		$buyer_po_arr=array();
		$buyer_po_id_arr=array();
		 $po_sql ="Select id,po_number,grouping from  wo_po_break_down  where is_deleted=0 and status_active=1 $internal_no_cond "; 
		 
		 // $po_sql ="Select b.idb.po_number,b.grouping from  wo_po_details_master a, wo_po_break_down b  where  a.job_no=b.job_no_mst and is_deleted=0 and status_active=1 $internal_no_cond ";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("po_number")]]['grouping']=$row[csf("grouping")];
			$buyer_po_id_arr[]="'".$row[csf("po_number")]."'";
		}
		unset($po_sql_res);
		
		//print_r($buyer_po_arr);
        //$buyer_po_lib = return_library_array("select id, id as id2 from wo_po_break_down where grouping like '%$internal_no%'",'id','id2');
		if($internal_no !="")
		{
			$buyer_po_id = implode(",", $buyer_po_id_arr);
			if ($buyer_po_id=="")
			{
				echo "Not Found."; die;
			}
       	 if($buyer_po_id !="") $buyer_po_id_cond = " and b.buyer_po_no in($buyer_po_id)";
		}
    }
 		
	$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1 and con_date=(select max(con_date) as con_date from currency_conversion_rate)" , "conversion_rate" );
	
	
	//echo $currency_rate; die;

	$order_sql="select a.id,a.subcon_job,a.receive_date,a.exchange_rate,a.currency_id,a.delivery_date,a.order_no as cust_order_no,a.party_id,a.within_group,a.subcon_job,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,c.qnty,c.order_id ,c.qnty,c.rate,c.amount,c.booked_qty,c.description,c.id as breakdown_id,b.delivery_date as delivery_target_date, a.trims_ref
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c
	where a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and a.entry_form=255 and a.company_id =$cbo_company_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $sql_order_no ";
		$order_sql_result = sql_select($order_sql);
		$order_array=array();
		$booked_array=array();
		$booked_sammary_array=array();
		foreach($order_sql_result as $row)
		{
			$order_array[$row[csf('id')]]['receive_date']=$row[csf('receive_date')]; 
			$order_array[$row[csf('id')]]['delivery_date']=$row[csf('delivery_date')];
			$order_array[$row[csf('id')]]['delivery_target_date']=$row[csf('delivery_target_date')];
			$order_array[$row[csf('id')]]['cust_order_no']=$row[csf('cust_order_no')];
			$order_array[$row[csf('id')]]['party_id']=$row[csf('party_id')];
			$order_array[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
			$order_array[$row[csf('id')]]['buyer_buyer']=$row[csf('buyer_buyer')];
			$order_array[$row[csf('id')]]['within_group']=$row[csf('within_group')];
			$order_array[$row[csf('id')]]['exchange_rate']=$row[csf('exchange_rate')];
			$order_array[$row[csf('id')]]['currency_id']=$row[csf('currency_id')];
			$order_array[$row[csf('id')]]['subcon_job']=$row[csf('subcon_job')];
			$order_array[$row[csf('id')]]['trims_ref']=$row[csf('trims_ref')];
			$booked_array[$row[csf('section')]][$row[csf('sub_section')]][$row[csf('description')]][$row[csf('order_no')]][$row[csf('id')]]['order_quantity']+=$row[csf('qnty')];
			$booked_array[$row[csf('section')]][$row[csf('sub_section')]][$row[csf('description')]][$row[csf('order_no')]][$row[csf('id')]]['amount']+=$row[csf('amount')];
			
			$booked_sammary_array[$row[csf('section')]][$row[csf('sub_section')]]['order_quantity']+=$row[csf('qnty')];
			$booked_sammary_array[$row[csf('section')]][$row[csf('sub_section')]]['amount']+=$row[csf('amount')];
		}
	
	/*echo  $trims_order_sql= "select a.id,a.trims_del,a.del_no_prefix,a.del_no_prefix_num,a.party_id,a.currency_id,a.delivery_date,a.challan_no,b.received_id,b.booking_dtls_id,b.receive_dtls_id, b.job_dtls_id,b.production_dtls_id,b.order_id,b.order_no,b.buyer_po_id,b.buyer_po_no,b.buyer_style_ref,b.buyer_buyer,b.section as section_id,b.item_group,b.order_uom, b.order_quantity, b.delevery_qty,b.remarks,b.description as item_description, b.color_id, b.size_id,b.color_name,b.size_name,b.delevery_status,b.workoder_qty,b.order_receive_rate,b.break_down_details_id,c.sub_section,c.job_no_mst,c.conv_factor from trims_delivery_mst a, trims_delivery_dtls b,trims_job_card_dtls c where a.entry_form=208 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and c.id =b.job_dtls_id $sql_cond $where_con  $company group by a.id,a.trims_del,a.del_no_prefix,a.del_no_prefix_num,a.party_id,a.currency_id,a.delivery_date,a.challan_no,b.received_id,b.booking_dtls_id,b.receive_dtls_id, b.job_dtls_id,b.production_dtls_id,b.order_id,b.order_no,b.buyer_po_id,b.buyer_po_no,b.buyer_style_ref,b.buyer_buyer,b.section,b.item_group,b.order_uom, b.order_quantity, b.delevery_qty,b.remarks,b.description, b.color_id, b.size_id,b.color_name,b.size_name,b.delevery_status,b.workoder_qty,b.order_receive_rate,b.break_down_details_id,c.sub_section,c.job_no_mst,c.conv_factor  order by a.id DESC"; */
	
	 /*$trims_order_sql= "select a.id,a.trims_del,a.delivery_date,b.received_id, b.job_dtls_id,b.production_dtls_id,b.order_no,b.buyer_po_no,b.section as section_id,b.delevery_qty,b.remarks,b.description as item_description, b.buyer_buyer,c.sub_section,c.job_no_mst,c.conv_factor from trims_delivery_mst a, trims_delivery_dtls b,trims_job_card_dtls c where a.entry_form=208 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and c.id =b.job_dtls_id $sql_cond $where_con  $company $buyer_po_id_cond  group by a.id,a.trims_del,a.delivery_date,b.received_id, b.job_dtls_id,b.production_dtls_id,b.order_no,b.buyer_po_no,b.section, b.delevery_qty,b.remarks,b.description,b.buyer_buyer,c.sub_section,c.job_no_mst,c.conv_factor  order by a.id DESC";*/  
	$trims_order_sql= "select a.id,a.trims_del,a.delivery_date,b.received_id, b.job_dtls_id,b.production_dtls_id,b.order_no,b.buyer_po_no,b.section as section_id,b.delevery_qty,b.remarks,b.description as item_description, b.buyer_buyer,c.sub_section,c.job_no_mst,c.booked_conv_fac as conv_factor from trims_delivery_mst a, trims_delivery_dtls b,subcon_ord_dtls c where  a.id=b.mst_id and c.id=b.receive_dtls_id $trimsreceiveid_cond  and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond $where_con  $company $buyer_po_id_cond  order by a.id DESC";
	  
		$result = sql_select($trims_order_sql);
        $date_array=array();
		
        foreach($result as $row)
        {
			//echo "<pre>";
			//print_r($row);
			
       	 	$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['section_id']=$row[csf('section_id')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['sub_section']=$row[csf('sub_section')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['buyer_buyer']=$row[csf('buyer_buyer')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['production_date']=$row[csf('production_date')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['delivery_date']=$row[csf('delivery_date')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['job_dtls_id']=$row[csf('job_dtls_id')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['received_id']=$row[csf('received_id')];
       		$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['job_quantity']=$row[csf('job_quantity')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['uom']=$row[csf('uom')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['conv_factor']=$row[csf('conv_factor')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['buyer_po_no']=$row[csf('buyer_po_no')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['item_description']=$row[csf('item_description')];			            
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['job_no_mst']=$row[csf('job_no_mst')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['qc_qty']+=$row[csf('qc_qty')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['break_id']=$row[csf('break_id')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['product_uom']=$row[csf('product_uom')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['machine_id']=$row[csf('machine_id')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['order_no']=$row[csf('order_no')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['delevery_qty'] +=$row[csf('delevery_qty')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['remarks']=$row[csf('remarks')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['trims_del'].=$row[csf('trims_del')].',';
 }
 
 
 	//echo "<pre>";
	//print_r($date_array);
	//die;
 
	/*$trims_delevery_sql= "select b.order_no,b.section as section_id, sum(b.delevery_qty) as delevery_qty,b.description as item_description,c.sub_section  from trims_delivery_mst a, trims_delivery_dtls b,trims_job_card_dtls c where a.entry_form=208 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and c.id =b.job_dtls_id group by b.order_no,b.section, b.delevery_qty,b.description,c.sub_section"; 
	$trims_delevery_result = sql_select($trims_delevery_sql);
	$total_delevery_qty=array();
	foreach($trims_delevery_result as $row)
	{
	$total_delevery_qty_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]]['deleveryqty']+=$row[csf('delevery_qty')];
	}*/

	$trims_delevery_sql="select a.delivery_date,a.received_id,b.delevery_qty,b.order_receive_rate,c.booked_uom as uom,b.section,c.sub_section,b.item_group,b.color_id,b.size_id,b.description,b.delevery_status,b.break_down_details_id,c.id,b.order_no from trims_delivery_mst a, trims_delivery_dtls b, subcon_ord_dtls c where a.id=b.mst_id and c.id=b.receive_dtls_id $trimsreceiveid_cond  and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.delivery_date"; 

	// echo $trims_delevery_sql;

	$trims_delivery_data_arr=array();
	$result_trims_delevery_sql = sql_select($trims_delevery_sql);
	foreach($result_trims_delevery_sql as $row)
	{
		/*$key=$row[csf("item_group")].'*'.$row[csf("received_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
		
		$trims_delivery_data_arr[$key][delevery_qty]+=$row[csf("delevery_qty")];
		$trims_delivery_data_arr[$key][delevery_val]+=$row[csf("delevery_qty")]*$row[csf("order_receive_rate")];
		$trims_delivery_data_arr[$key][delevery_last_date]=$row[csf("delivery_date")];
		$trims_delivery_data_arr[$key][delevery_status]=$row[csf("delevery_status")];
		$trims_delivery_data_arr[$key][break_ids].=$row[csf("break_down_details_id")].',';*/

		$total_delevery_qty_array[$row[csf('section')]][$row[csf('sub_section')]][$row[csf('description')]][$row[csf('order_no')]]['deleveryqty']+=$row[csf('delevery_qty')];
	}
 
	//echo "<pre>";
	//print_r($total_delevery_qty_array);
 
 
	
	$width=2600;
	ob_start();
	?>	
	<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">
    	<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
				<thead class="form_caption" >
					<tr>
						<td colspan="35" align="center" style="font-size:20px;"><? echo $companyArr[$cbo_company_id]; ?></td>
					</tr>
					<tr>
						<td colspan="35" align="center" style="font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
					</tr>
					<tr>
						<td colspan="35" align="center" style="font-size:14px; font-weight:bold">
							<? echo " From : ".change_date_format($txt_date_from) ." To : ". change_date_format($txt_date_to) ;?>
						</td>
					</tr>
				</thead>
			</table>
            <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
				<thead>
                    <th width="35">SL</th>
                    <th width="100">Trims WO No.</th>
                    <th width="100">Section</th>
                    <th width="100">Sub-Section</th>
                    <th width="100">Customer WO No</th>
                    <th width="100">Internal Ref</th>
                    <th width="100">Trims Ref</th>
                    <th width="100">Customer Buyer</th>
                    <th width="100">Party Name</th>
                    <th width="100">Order Rcv date</th>
                    <th width="100">Tgt.Trims Delv.date</th>
                    <th width="100">Actual Delivery Date</th>
                    <th width="100">Delv Chlln No</th>
                    <th width="150">Item Description</th>
                    <th width="100">WO UOM</th>
                    <th width="100">WO Qty</th>
                    <th width="100">U/Price (TK)</th>
                    <th width="100">U/Price ($)</th>
                    <th width="100">WO Value ($)</th>
                    <th width="100">Current Delv Qty</th>
                    <th width="100">Curr. Delv Value ($)</th>
                    <th width="100">Total Delv Qty</th>
                    <th width="100">Total Delv Value ($)</th>
                    <th width="100">Delv.  Bal.Qty</th>
                    <th width="100">Delv. Bal. Value ($)</th>
                    <th>Remarks</th>
				</thead>
			</table>
        <div style="width:<? echo $width+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
        	<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo $width;?>" rules="all" align="left">
            <? 
				$i=1;
				$total_order_qty=0;$total_order_val=0;$total_booked_qty=0;$total_production_qty=0;
				
				foreach($date_array as $section_key_id=>$section_data)
				{
					foreach($section_data as $sub_section_key_id=>$sub_section_key_data)
					{
						foreach($sub_section_key_data as $item_description_id=>$item_description_data)
						{
							foreach($item_description_data as $order_no_id=>$order_no_data)
							{
								foreach($order_no_data as $job_no_mst_id=>$deleverydate_data)
								{
									$trims_del="";
									
									foreach($deleverydate_data as $dev_date_id=>$row)
									{
										//$total_delevery_qty[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]]['delevery_qty']
										
										$delevery_quantity=$total_delevery_qty_array[$row[section_id]][$row[sub_section]][$row[item_description]][$row[order_no]]['deleveryqty'];			
										$orderquantity=$booked_array[$row[section_id]][$row[sub_section]][$row[item_description]][$row[order_no]][$row[received_id]]['order_quantity'];
										$orderamount=$booked_array[$row[section_id]][$row[sub_section]][$row[item_description]][$row[order_no]][$row[received_id]]['amount'];
										$rate=number_format($orderamount/$orderquantity,4);
										$currency_id=$order_array[$row[received_id]]['currency_id'];
										if($currency_id==1)
										{
											 $takarate=$rate;
											 $orderamounttaka=$orderamount;
											 $usdrate=number_format($rate/$currency_rate,4);
											 $orderamountusd=$orderamount/$currency_rate;
											 $delevery_valu_taka=$row[delevery_qty]/$row[conv_factor]*$takarate;
											 $delevery_valu_usd=$row[delevery_qty]/$row[conv_factor]*$usdrate;
											 
											 $total_delevery_valu_taka1=$delevery_quantity/$row[conv_factor]*$takarate;
											 $total_delevery_valu_usd1=$delevery_quantity/$row[conv_factor]*$usdrate;
											 
											 
											 
										}
										elseif($currency_id==2)
										{
											$takarate=number_format($rate*$currency_rate,4);
											$orderamounttaka=$orderamount*$currency_rate;
											$usdrate=$rate;
											$orderamountusd=$orderamount;
											$delevery_valu_taka=$row[delevery_qty]/$row[conv_factor]*$takarate;
											$delevery_valu_usd=$row[delevery_qty]/$row[conv_factor]*$usdrate;
											$total_delevery_valu_taka1=$delevery_quantity/$row[conv_factor]*$takarate;
											$total_delevery_valu_usd1=$delevery_quantity/$row[conv_factor]*$usdrate;
										}
										$trims_del=implode(",",array_unique(explode(",",chop($row[trims_del],','))));
										//$trims_del=implode(",",array_unique(explode(",",chop($row[trims_del]),',')));
										//echo $row[received_id];	
									?>
					                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
					                	<td width="35"  align="center"><? echo $i;?></td>
					                    <td width="100" align="left"><? echo $order_array[$row[received_id]]['subcon_job'] ;?></td>
					                    <td width="100" align="left"><? echo $trims_section[$row[section_id]];?></td>
					                    <td width="100" align="left"><? echo $trims_sub_section[$row[sub_section]];?></td>
					                    <td width="100" align="left"><? echo $order_array[$row[received_id]]['cust_order_no'] ;?></td>
					                    <td width="100" align="left"><? echo $buyer_po_arr[$row[buyer_po_no]]['grouping'] ;?></td>
					                    <td width="100" align="left"><? echo $order_array[$row[received_id]]['trims_ref']; ?></td>
					                    <td width="100" align="left"><? echo $buyer_buyer=($order_array[$row[received_id]]['within_group']==1)?$buyerArr[$order_array[$row[received_id]]['buyer_buyer']]:$row['buyer_buyer'];?></td>
					                    <td width="100" align="left"><? echo $party=($order_array[$row[received_id]]['within_group']==1)?$companyArr[$order_array[$row[received_id]]['party_id']]:$buyerArr[$order_array[$row[received_id]]['party_id']];?></td>
					                    <td width="100" align="center"><? echo $order_array[$row[received_id]]['receive_date'] ;?></td>
					                    <td width="100" align="center"><? echo $order_array[$row[received_id]]['delivery_target_date']; ?></td>
					                    <td width="100" align="center"><? echo $row[delivery_date]; ?></td>
					                    <td width="100" align="center"><? echo $trims_del; ?></td>
					                   	 <td width="150" align="left"><p><? echo $row[item_description];?></p></td>
					                    <td width="100" align="center"><? echo $unit_of_measurement[$order_array[$row[received_id]]['order_uom']]//$unit_of_measurement[$row[uom]];?></td>  			 
					                    <td width="100" align="right"><? echo number_format($orderquantity,2);?></td>
					                    <td width="100" align="right"><? echo number_format($takarate,4);?></td>
					                    <td width="100" align="right"><? echo number_format($usdrate,4);?></td>
					                    <td width="100" align="right"><? echo number_format($orderamountusd); $total_orderamount_usd+=$orderamountusd; ?></td>
					                    <td width="100" align="right"><? echo number_format($row[delevery_qty],2); ?></td>
					                     <td width="100" align="right"><? //echo $row[delevery_qty]*$usdrate;
										 if ($row[conv_factor])echo number_format($row[delevery_qty]*$usdrate);else echo $delevery_valu_usd=0; $total_delevery_valu_usd+=$row[delevery_qty]*$usdrate;  ?></td>
					                    <td width="100" align="right"><? echo $delevery_quantity; //$usdrate ?></td>
					                    <td width="100" align="right"><? //echo $delevery_quantity*$usdrate;
										if ($row[conv_factor])echo number_format($delevery_quantity*$usdrate);else echo $total_delevery_valu_usd2=0; $total_delevery_valu_usd2+=($delevery_quantity*$usdrate); ?></td>
					                    <td width="100" align="right"><? echo number_format($orderquantity-$delevery_quantity);?></td>
					                    <td width="100" align="right"><? echo number_format(($orderquantity-$delevery_quantity)*$usdrate);?></td>
					                    <td><? echo $row[remarks]; ?></th>
					                </tr>
					                <? 
										$prod_sammary_array[$row[section_id]][$row[sub_section]]['delevery_qty']+=($row[delevery_qty]*$usdrate);
										$i++;
									}
							}
						}
					}
				}
			}
		
			
			//print_r($prod_sammary_array);
				?>
                
                
       		 </table>
        </div>
        <table width="<? echo $width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
				<tfoot>
                
                <th width="35"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="150"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100">Total:</th>
                    <th width="100"><? echo number_format($total_delevery_valu_usd);?></th>
                    <th width="100"></th>
                    <th width="100"><? echo number_format($total_delevery_valu_usd2);?></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th></th>
				</tfoot>
			</table>
       
    </div>
    
    
    <div align="left" style="height:auto; width:500px; margin:0 auto; padding:0;">
    	<table width="500" cellpadding="0" cellspacing="0" id="caption" align="left">
				<thead class="form_caption" >
					<tr>
						<td colspan="35" align="center" style="font-size:14px; font-weight:bold" ><? echo "Summary"; ?></td>
					</tr>
				</thead>
			</table>
            <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="500" rules="all" id="rpt_table_header" align="left">
				<thead>
                    <th width="35">SL</th>
                    <th width="100">Section</th>
                    <th width="100">Sub-Section</th>
                    <th>Curr. Delv Value ($)</th>
				</thead>
			</table>
        
        	<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="500" rules="all" align="left">
             <? 
			 
			 /* $sammary_sql=" select a.party_id,a.production_date,a.received_id,a.section_id,b.qc_qty,b.job_dtls_id,b.uom as product_uom,c.sub_section,b.machine_id,c.conv_factor,c.job_quantity,c.uom ,c.buyer_po_no,c.item_description,c.job_no_mst,c.break_id,d.order_no from trims_production_mst a,trims_production_dtls b,trims_job_card_dtls c ,trims_job_card_mst d  where  a.id=b.mst_id and c.id=b.job_dtls_id  and   c.mst_id=d.id and  a.entry_form=269 and  d.entry_form=257  and   a.company_id =$cbo_company_id  and  a.status_active=1 and b.status_active=1 and c.status_active=1 $where_con $sql_cond  group by a.party_id,a.production_date,a.received_id,a.section_id,b.job_dtls_id,c.sub_section,c.conv_factor,c.job_quantity,c.uom,c.buyer_po_no,c.item_description,c.job_no_mst,b.qc_qty,c.break_id,b.uom,b.machine_id,d.order_no"; 
			  */
			/*  $sammary_sql= "select a.id,a.trims_del,a.del_no_prefix,a.del_no_prefix_num,a.party_id,a.currency_id,a.delivery_date,a.challan_no,b.received_id,b.booking_dtls_id,b.receive_dtls_id, b.job_dtls_id,b.production_dtls_id,b.order_id,b.order_no,b.buyer_po_id,b.buyer_po_no,b.buyer_style_ref,b.buyer_buyer,b.section as section_id,b.item_group,b.order_uom, b.order_quantity, b.delevery_qty,b.claim_qty,b.remarks,b.description as item_description, b.color_id, b.size_id,b.color_name,b.size_name,b.delevery_status,b.workoder_qty,b.order_receive_rate,b.break_down_details_id,c.sub_section,c.job_no_mst,c.conv_factor from trims_delivery_mst a, trims_delivery_dtls b,trims_job_card_dtls c where a.entry_form=208 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and c.id =b.job_dtls_id $sql_cond $where_con  $company $party_id_cond $withinGroup $search_com $withinGroup group by a.id,a.trims_del,a.del_no_prefix,a.del_no_prefix_num,a.party_id,a.currency_id,a.delivery_date,a.challan_no,b.received_id,b.booking_dtls_id,b.receive_dtls_id, b.job_dtls_id,b.production_dtls_id,b.order_id,b.order_no,b.buyer_po_id,b.buyer_po_no,b.buyer_style_ref,b.buyer_buyer,b.section,b.item_group,b.order_uom, b.order_quantity, b.delevery_qty,b.claim_qty,b.remarks,b.description, b.color_id, b.size_id,b.color_name,b.size_name,b.delevery_status,b.workoder_qty,b.order_receive_rate,b.break_down_details_id,c.sub_section,c.job_no_mst,c.conv_factor  order by a.id DESC"; 
			  */
			$sammary_sql= "select a.id,a.trims_del,a.delivery_date,b.received_id, b.job_dtls_id,b.production_dtls_id,b.order_no,b.buyer_po_no,b.section as section_id,b.delevery_qty,b.remarks,b.description as item_description, b.buyer_buyer,c.sub_section,c.job_no_mst,c.booked_conv_fac as conv_factor from trims_delivery_mst a, trims_delivery_dtls b,subcon_ord_dtls c where  a.id=b.mst_id and c.id=b.receive_dtls_id $trimsreceiveid_cond  and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond $where_con  $company $party_id_cond $withinGroup $search_com $withinGroup   group by a.id,a.trims_del,a.delivery_date,b.received_id, b.job_dtls_id,b.production_dtls_id,b.order_no,b.buyer_po_no,b.section, b.delevery_qty,b.remarks,b.description,b.buyer_buyer,c.sub_section,c.job_no_mst,c.booked_conv_fac  order by a.id DESC";
			  
			$sammary_result = sql_select($sammary_sql);
	        $sammary_array=array();
	        foreach($sammary_result as $row)
	        {
	       	 	$sammary_array[$row[csf('section_id')]][$row[csf('sub_section')]]['section_id']=$row[csf('section_id')];
				$sammary_array[$row[csf('section_id')]][$row[csf('sub_section')]]['sub_section']=$row[csf('sub_section')];
	 		}
		
				$t=1;
				foreach($sammary_array as $section_key_id=>$section_data)
				{
					$section_total=0;
					foreach($section_data as $sub_section_key_id=>$row)
					{
						?>
                           <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $t; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $t; ?>">
                            <td width="35"  align="center"><? echo $t;?></td>
                            <td width="100" align="center"><? echo $trims_section[$row[section_id]];?></td>
                            <td width="100" align="center"><? echo $trims_sub_section[$row[sub_section]];?></td>
                            <td align="right"><? echo $product_valu_usd=number_format($prod_sammary_array[$row[section_id]][$row[sub_section]]['delevery_qty']);$section_total+=$prod_sammary_array[$row[section_id]][$row[sub_section]]['delevery_qty'];?></td>
                           </tr>
						<? 
					$t++;
					}
					?>
                    <tr style="background-color:#CCC">
                   	 	<td colspan="3" align="right"><b><? echo $trims_section[$row[section_id]];?> Total</b></td>
                    	<td align="right"><b><? echo number_format($section_total);?></b></td>
                    </tr>
                 	<?
				} 
				?>
       		 </table>
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
    echo "$html**$filename**$report_type";
    exit();
	
}

?>