<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
if ($action=="load_variable_settings")
{
	echo "$('#variable_check').val(0);\n";
	echo "$('#bill_on').text('');\n"; 
	$sql_result = sql_select("select dyeing_fin_bill,variable_list,allow_per from  variable_settings_subcon where company_id='$data' and variable_list in(1,18) order by id");
 	foreach($sql_result as $result)
	{
		if($result[csf("variable_list")]==1)
		{
			echo "$('#variable_check').val(".$result[csf("dyeing_fin_bill")].");\n";
			if ($result[csf("dyeing_fin_bill")]==1)
			{
				echo "$('#bill_on').text('Bill On Grey Qty');\n"; 
			}
			else if ($result[csf("dyeing_fin_bill")]==2)
			{
				echo "$('#bill_on').text('Bill On Delivery Qty');\n"; 
			}
			else
			{
				echo "$('#bill_on').text('');\n"; 
			}
		}
		if($result[csf("variable_list")]==18)
		{
			if($result[csf("dyeing_fin_bill")]==1) //Outside Dyeing Finishing Bill
			{
				echo "$('#mandatory_check').val(".$result[csf("allow_per")].");\n";
			}
			
		}
	}
 	exit();
}

// ================================Print button ==============================

if($action=="print_button_variable_setting")
{

    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=8 and report_id=267 and is_deleted=0 and status_active=1");
	 $printButton=explode(',',$print_report_format);
	   
	   echo "$('#printb').hide();";
	   echo "$('#printb1').hide();";

	foreach($printButton as $id){				
		if($id==143){echo "$('#printb').show();";}
		else if($id==66){echo "$('#printb1').show();";}		
	}

    exit();
}
// ======================= End Print button =================================================

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","","","","","",3 );
	exit();	 
}

if ($action=="load_drop_down_supplier_name")
{
	echo create_drop_down( "cbo_supplier_company", 150, "select sup.id, sup.supplier_name from lib_supplier sup,lib_supplier_tag_company b where sup.status_active=1 and sup.is_deleted=0 and b.supplier_id=sup.id and b.tag_company='$data' $supplier_cond and sup.id in (select  supplier_id from  lib_supplier_party_type where party_type in (9,21)) order by supplier_name", "id,supplier_name", 1, "-- Select supplier --", $selected, "","","","","","",5 );
	exit();
}
if ($action=="load_drop_down_supplier_name_new")
{
	$sql = "SELECT sup.id, sup.supplier_name  FROM lib_supplier sup, lib_supplier_tag_company b   WHERE   sup.status_active = 1   AND sup.is_deleted = 0    AND b.supplier_id = sup.id   AND b.tag_company ='$data' AND sup.id IN (SELECT supplier_id                          FROM lib_supplier_party_type    WHERE party_type IN (9, 21))    UNION ALL   SELECT sup.id, sup.supplier_name    FROM lib_supplier sup, lib_supplier_tag_company b  WHERE   sup.status_active IN(1,3)   AND sup.is_deleted = 0    AND b.supplier_id = sup.id   AND b.tag_company = '$data'    AND sup.id IN (SELECT supplier_id     FROM lib_supplier_party_type   WHERE party_type IN (9, 21))  ORDER BY supplier_name";
	

	echo create_drop_down( "cbo_supplier_company", 150, " $sql ", "id,supplier_name", 1, "-- Select supplier --", $selected, "","","","","","",5 );
	exit();
}
if ($action=="load_drop_down_supplier_name_pop")
{
	echo create_drop_down( "cbo_supplier_company", 150, "select sup.id, sup.supplier_name from lib_supplier sup,lib_supplier_tag_company b where sup.status_active=1 and sup.is_deleted=0 and b.supplier_id=sup.id and b.tag_company='$data' $supplier_cond and sup.id in (select  supplier_id from  lib_supplier_party_type where party_type in(9,21)) order by supplier_name", "id,supplier_name", 1, "-- Select supplier --", $selected, "","","","","","",5 );
	exit();
}

if ($action=="outside_finishing_info_list_view")
{
	echo load_html_head_contents("Popup Info","../", 1, 1, $unicode,1,'');
	$from=1;
	$exdata=explode('***',$data);
	//print_r($data);
	$ex_bill_for=$exdata[2];
	$date_from=$exdata[3];
	$date_to=$exdata[4];
	$manualChallan=$exdata[5];
	$bill_date=$exdata[9];
	//$variable_check=$exdata[6];
	$variable_check = return_field_value("dyeing_fin_bill","variable_settings_subcon","company_id ='$exdata[0]' and variable_list=1 and is_deleted=0 and status_active=1","dyeing_fin_bill");
	
	//sql_select("select dyeing_fin_bill from  variable_settings_subcon where company_id='$exdata[0]' and variable_list=1 order by id");
	$update_id=$exdata[7];
	$str_data=$exdata[8];
	//print_r($data);
	//die;
	// echo $bill_date.'=';
	$ex_str_data=explode("!!!!",$str_data);
	$str_arr=array();
	foreach($ex_str_data as $str)
	{
		$str_arr[]=$str;
	}
	if($db_type==0)
	{ 
		if ($date_from!="" &&  $date_to!="") $date_cond= "and a.receive_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; else $date_cond= "";
	}
	else if ($db_type==2)
	{
		if ($date_from!="" &&  $date_to!="") { $date_cond= "and a.receive_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'"; $date_cond2= "and a.issue_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";}
		  else { $date_cond= "";$date_cond2= "";
		  
		  }
	}
	
	
	$composition_arr=array(); $constructtion_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
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
	$usd_id=2;//===============Dollar========
$conversion_date=change_date_format($bill_date, "d-M-y", "-",1);	
$currency_rate=set_conversion_rate($usd_id,$conversion_date,$exdata[0]);
//echo $currency_rate.'s';
	
	?>
	</head>
	<body>
    <div id="body-close-after-populate">
        <div align="center" style="width:100%;" >	
            <div style="width:100%;">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1170px" class="rpt_table">
                    <thead>
                    	<th width="30"><input type="checkbox" name="checkall" id="checkall" onClick="fnc_check('all'); check_all_data();" value="2" ></th>
                        <th width="25">SL</th>
                        <th width="80">Sys. Challan</th>
                        <th width="60">Challan No</th>
                        <th width="60">Recive Date</th>
                        <th width="100">Process</th>
						<th width="100">Sub Process</th>
                        <th width="80">Color</th>
                        <th width="80">Body Part</th>
                        <th width="120">Fabric Description</th>
                        <th width="60">Receive Qty</th>
                        <th width="60">Used Qty</th>
                        <th width="80">Order No</th>
                        <th width="100">Style Ref.</th>
                        <th>Buyer</th>
                    </thead>
                </table>
            </div>
        <div style="width:1170px;max-height:180px; overflow-y:scroll" id="kintt_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1150px" class="rpt_table" id="tbl_list_search">
            <? 
			
			
			// echo "<pre>";
			// print_r($booking_dtls_data_arr);
			$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
			$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
         
			
			$bill_qty_array=array();
			$sql_bill="select receive_id, challan_no, order_id, item_id, color_id, (roll_no) as roll_qty, (receive_qty) as bill_qty from subcon_outbound_bill_dtls where status_active=1 and is_deleted=0 and process_id=4 ";
			$sql_bill_result =sql_select($sql_bill);
			foreach($sql_bill_result as $row)
			{
				$bill_qty_array[$row[csf('receive_id')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('color_id')]]['qty']+=$row[csf('bill_qty')];
				$bill_qty_array[$row[csf('receive_id')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('color_id')]]['roll']+=$row[csf('roll_qty')];
				$bill_qty_array2[$row[csf('challan_no')]][$row[csf('receive_id')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('color_id')]]['qty']+=$row[csf('bill_qty')];
				$bill_qty_array2[$row[csf('challan_no')]][$row[csf('receive_id')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('color_id')]]['roll']+=$row[csf('roll_qty')];
			}
			unset($sql_bill_result);
			
			$sql_grey_used=sql_select("select dtls_id, entry_form, used_qty from  pro_material_used_dtls where status_active=1 and is_deleted=0");
			$grey_used_arr=array();
			foreach ($sql_grey_used as $row) {
				$grey_used_arr[$row[csf('dtls_id')]][$row[csf('entry_form')]]=$row[csf('used_qty')];
			}
 
            $i=1;
			
			if($db_type==0)
			{
				$year_cond="year(a.insert_date)";
				$booking_without_order="IFNULL(d.booking_without_order,0)";
			}
			else if($db_type==2) 
			{
				$year_cond="TO_CHAR(a.insert_date,'YYYY')";
				$booking_without_order="nvl(d.booking_without_order,0)";
			}
			
			// $sql="SELECT c.id, a.currency_id, a.challan_no, a.receive_date, a.recv_number, a.recv_number_prefix_num, a.buyer_id, b.prod_id, b.color_id, b.body_part_id,  sum(b.receive_qnty) as rec_qty, sum(b.grey_used_qty) as grey_qty, sum(c.quantity) as order_qnty, c.po_breakdown_id, b.id as dtls_id, 0 as biid FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c WHERE a.id = b.mst_id $date_cond and b.id=c.dtls_id and c.trans_type=1 and c.entry_form in (7,37) AND a.knitting_source=3 and a.company_id =$data[0] AND a.knitting_company=$data[1] and a.item_category=2 and a.status_active=1 group by c.id, a.currency_id, a.challan_no, a.receive_date, a.recv_number, a.recv_number_prefix_num, a.buyer_id, b.prod_id, b.color_id, b.body_part_id, c.po_breakdown_id, c.dtls_id ";
			$order_arr=array();
			if($ex_bill_for!=3)
			{
				  if($ex_bill_for==4) //============FSO for Service==============
				  { 
						  $sql="SELECT a.id as mst_id, a.entry_form, a.receive_basis,a.recv_number, a.recv_number_prefix_num, a.challan_no, a.receive_date, a.booking_id as bookingno, $year_cond as year, b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.color_id, b.dia_width_type, sum(b.receive_qnty) as rec_qty, sum(b.grey_used_qty) as grey_qty, sum(c.quantity) as order_qnty,sum(b.production_qty) as production_qty, sum(b.no_of_roll) as carton_roll, b.id as dtls_id, c.id, c.po_breakdown_id, d.sales_order_no,e.sales_booking_no,e.po_buyer,e.po_job_no,e.style_ref_no,d.booking_no_id, d.booking_no,c.is_sales
						FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d,fabric_sales_order_mst e
						WHERE a.id=b.mst_id and b.id=c.dtls_id and d.id=b.batch_id and e.id=c.PO_BREAKDOWN_ID and d.SALES_ORDER_ID=e.id and c.trans_type=1 and c.entry_form in (225)   and a.entry_form in (225) and a.receive_basis=14 AND a.knitting_source=3 AND a.company_id=$exdata[0] AND a.knitting_company=$exdata[1] and a.item_category=2 
						and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $manual_challan_cond
						group by a.id, a.entry_form, a.receive_basis,a.recv_number, a.recv_number_prefix_num, a.challan_no, a.receive_date, a.booking_id, a.insert_date, b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, d.sales_order_no,e.sales_booking_no,e.po_buyer,e.po_job_no,e.style_ref_no,b.color_id,c.is_sales, b.dia_width_type, b.id, c.id, c.po_breakdown_id, d.booking_no_id, d.booking_no
						order by a.recv_number_prefix_num DESC";

				  }
				  else
				  {

						$sql_order="Select a.id, a.po_number, b.style_ref_no, b.buyer_name from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
						$sql_order_result=sql_select($sql_order);
						foreach ($sql_order_result as $row)
						{
							$order_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
							$order_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
							$order_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
						}
					//For Norban-> and c.trans_id!=0 
					unset($sql_order_result);
					 $sql="SELECT a.id as mst_id, a.entry_form, a.receive_basis,a.recv_number, a.recv_number_prefix_num, a.challan_no, a.receive_date, a.booking_id as bookingno, $year_cond as year, b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.color_id, b.dia_width_type, sum(b.receive_qnty) as rec_qty, sum(c.grey_used_qty) as grey_qty, sum(c.quantity) as order_qnty,sum(b.production_qty) as production_qty, sum(b.no_of_roll) as carton_roll, b.id as dtls_id, c.id, c.po_breakdown_id, d.booking_no_id, d.booking_no
						FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d
						WHERE a.id=b.mst_id and b.id=c.dtls_id and d.id=b.batch_id and c.trans_type=1 and c.entry_form in (7,37,66,68)   and a.entry_form in (7,37,66,68) AND a.knitting_source=3 AND a.company_id=$exdata[0] AND a.knitting_company=$exdata[1] and a.item_category=2 
						and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $manual_challan_cond
						group by a.id, a.entry_form, a.receive_basis,a.recv_number, a.recv_number_prefix_num, a.challan_no, a.receive_date, a.booking_id, a.insert_date, b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.color_id, b.dia_width_type, b.id, c.id, c.po_breakdown_id, d.booking_no_id, d.booking_no
						order by a.recv_number_prefix_num DESC
						"; //and a.receive_basis in (2,4,5,9,11)  and d.batch_against in (0,1,3) and $booking_without_order=0
				  }
			}
			else{
				//and b.trans_id!=0 
					$sql="SELECT a.id as mst_id, a.entry_form, a.receive_basis,a.recv_number, a.recv_number_prefix_num, a.challan_no, a.receive_date, a.booking_id as bookingno, $year_cond as year, b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.color_id, b.dia_width_type, sum(b.receive_qnty) as rec_qty, sum(b.grey_used_qty) as grey_qty, sum(b.receive_qnty) as order_qnty, sum(b.no_of_roll) as carton_roll, b.id as dtls_id, b.id, '' as po_breakdown_id, d.booking_no_id, d.booking_no
					FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst d
					WHERE a.id=b.mst_id and d.id=b.batch_id and a.booking_without_order=1  and a.entry_form in (7,37,66,68) AND a.knitting_source=3 AND a.company_id=$exdata[0] AND a.knitting_company=$exdata[1] and a.item_category=2  and d.booking_without_order=1
					and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $manual_challan_cond
					group by a.id, a.entry_form, a.receive_basis, a.recv_number,a.recv_number_prefix_num, a.challan_no, a.receive_date, a.booking_id, a.insert_date, b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.color_id, b.dia_width_type, b.id, d.booking_no_id, d.booking_no order by a.recv_number_prefix_num DESC";
			}
		 	 // echo $sql; 
			$sql_result=sql_select($sql);
			

			$rec_no_array=array();
			$prod_id_array=array();
			foreach($sql_result as $row)
			{
				array_push($rec_no_array, $row[csf('bookingno')]);
				array_push($prod_id_array, $row[csf('prod_id')]);				
				$po_ids=$row[csf('po_breakdown_id')].",";
			}

			$po_ids=implode(",",array_unique(explode(",",rtrim($po_ids,","))));
			//--------------start--------------booking and delivery data-----------------------------------

			$delivery_data=sql_select("select a.sys_number,barcode_no,reprocess,dtls_id,po_breakdown_id ,a.id 
			from pro_grey_prod_delivery_mst a ,pro_roll_details b  where a.id=b.mst_id   and a.entry_form=67 and a.status_active=1 and a.is_deleted=0
			and b.entry_form=67 and b.status_active=1 and b.is_deleted=0 and po_breakdown_id in ($po_ids)");

			foreach($delivery_data as $row){
					$po_wise_data[$row[csf('po_breakdown_id')]]['delivery_challan']=$row[csf('sys_number')];
			}

			$booking_sql="select a.id,a.booking_mst_id,a.pre_cost_fabric_cost_dtls_id,a.po_break_down_id,a.rate,a.description,a.process,a.gmts_color_id,b.lib_yarn_count_deter_id as deter_id,a.sub_process_id
			from wo_booking_dtls a,wo_pre_cost_fabric_cost_dtls b,wo_booking_mst c where a.pre_cost_fabric_cost_dtls_id=b.id and  a.status_active=1 and a.booking_mst_id=c.id and c.company_id=$exdata[0]  and a.po_break_down_id in ($po_ids) ";

			$booking_sql_data=sql_select($booking_sql);
			$processId="";
			foreach($booking_sql_data as $val){
				$sub_process_arr=explode(",",$val[csf('sub_process_id')]);
				foreach($sub_process_arr as $sub_processId){
					$processId.=$conversion_cost_head_array[$sub_processId].",";
				}
				$booking_dtls_data_arr[$val[csf('booking_mst_id')]][$val[csf('po_break_down_id')]][$val[csf('deter_id')]][$val[csf('process')]][$val[csf('gmts_color_id')]]['rate']=$val[csf('rate')];
				$bookingDtlsArr[$val[csf('booking_mst_id')]][$val[csf('process')]]['sub_process_id']=rtrim($processId,',');;
				$bookingDtlsArr[$val[csf('booking_mst_id')]][$val[csf('process')]]['process_id']=$conversion_cost_head_array[$val[csf('process')]];;
			}

			//---------------end-------------booking and delivery data-----------------------------------


			$rec_cond=where_con_using_array(array_unique($rec_no_array),1,"id");
			$prod_id_cond=where_con_using_array(array_unique($prod_id_array),1,"id");
			$recive_basis_arr=return_library_array( "select id, receive_basis from inv_receive_master where 1=1 $rec_cond",'id','receive_basis');
			$product_dtls_arr=return_library_array( "select id, product_name_details from  product_details_master where 1=1 $prod_id_cond",'id','product_name_details');
			unset($rec_cond);
			unset($rec_no_array);
			unset($prod_id_cond);
			unset($prod_id_array);
			
			$process_cost_maintain=return_field_value("process_costing_maintain","variable_settings_production","company_name=$exdata[0] and variable_list in (34) and is_deleted=0 and status_active=1"); 
			foreach($sql_result as $row) // for update row
			{
				
				//echo 333;//$all_value=$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$row[csf('prod_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('fabric_description_id')].'_'.$row[csf('dia_width_type')].'_'.$row[csf('batch_id')];
				$all_value=$row[csf('id')];
				if(in_array($all_value,$str_arr))
				{
					$booking_no=0; $independent=''; $bill_for_id=0; $bill_for_sb=0;$bill_for_dsb=0;
					if ($row[csf('entry_form')]==7)
					{
						if($ex_bill_for==1) { if($row[csf('receive_basis')]==4) $independent=4; } //else $independent='';
						if ($row[csf('receive_basis')]==5) $booking_no=$row[csf('booking_no')]; else $booking_no=0;
						if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2) $bill_for_id="SM"; else if($ex_bill_for==3) $bill_for_id="SMN";
					}
					else if ($row[csf('entry_form')]==37)
					{
						if ($row[csf('receive_basis')]==9) 
						{
							if($recive_basis_arr[$row[csf('bookingno')]]==4) $independent=4; //else $row[csf('receive_basis')]=5;
						}
						
						if($ex_bill_for==1) { if($row[csf('receive_basis')]==4) $independent=4; }// else $independent='';
						if($row[csf('receive_basis')]==2 || $row[csf('receive_basis')]==11) 
							$booking_no=$row[csf('booking_no')];
						else if($row[csf('receive_basis')]==9 && $recive_basis_arr[$row[csf('bookingno')]]==5) 
							$booking_no=$row[csf('booking_no')];
						else 
							$booking_no=0;
						if($ex_bill_for==1) { $bill_for_id="Fb"; $bill_for_sb="SB";$bill_for_dsb="DSB"; } 
						else if($ex_bill_for==2 && $row[csf('receive_basis')]!=4) $bill_for_id="SM";
						 else if($ex_bill_for==3 && $row[csf('receive_basis')]==11) $bill_for_id="SBKD"; 
						  else if($ex_bill_for==3) $bill_for_id="SMN";
						  if($ex_bill_for==1 && $row[csf('receive_basis')]==11){ $bill_for_id="FSB";$bill_for_id2="DSB";$bill_for_sb="SB";}
					}
					else if ($row[csf('entry_form')]==66)
					{
						$rec_basis=0; $bookinNo=""; $bookingId=0;// booking id is barcode, and booking no is delv id
						$rec_basis=0;
						$bookinNo=$row[csf('booking_no')];
						$bookingId=$row[csf('booking_id')];
						
						if($ex_bill_for==1) { if($rec_basis==0) $independent=4; } //else $independent='';
						if ($rec_basis==0) $booking_no=$bookinNo; else $booking_no=0;
						if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2 && $rec_basis!=0) $bill_for_id="SM"; else if($ex_bill_for==3) $bill_for_id="SMN";
					}
					else if ($row[csf('entry_form')]==61) //Grey Fabric Roll Issue
					{
						$rec_basis=0; $bookinNo=""; $bookingId=0;// booking id is barcode, and booking no is delv id
						$rec_basis=0;
						$bookinNo=$row[csf('booking_no')];
						$bookingId=$row[csf('booking_id')];
						
						if($ex_bill_for==1) { if($rec_basis==0) $independent=4; } //else $independent='';
						if ($rec_basis==0) $booking_no=$bookinNo; else $booking_no=0;
						if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2 && $rec_basis!=0) $bill_for_id="SM"; else if($ex_bill_for==3) $bill_for_id="SMN";
					}
					else if ($row[csf('entry_form')]==68)
					{
						$rec_basis=0; $bookinNo=""; $bookingId=0;// booking id is barcode, and booking no is delv id
						$rec_basis=0;
						$bookinNo=$row[csf('booking_no')];
						$bookingId=$row[csf('booking_id')];
						
						if ($ex_bill_for==1) { if($rec_basis==0) $independent=4; } //else $independent='';
						if ($rec_basis==0) $booking_no=$bookinNo; else $booking_no=0;
						if ($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2 && $rec_basis!=0) $bill_for_id="SM"; else if($ex_bill_for==3) $bill_for_id="SMN";
					} 
					else if ($row[csf('entry_form')]==225)
					{
						$rec_basis=0; $bookinNo=""; $bookingId=0;// booking id is barcode, and booking no is delv id
						$rec_basis=0;
						$bookinNo=$row[csf('sales_booking_no')];
						$bookingId=$row[csf('booking_id')];
						
						
						if ($ex_bill_for==4) $bill_for_id="Fb"; else if($ex_bill_for==2 && $rec_basis!=0) $bill_for_id="SM"; else if($ex_bill_for==3) $bill_for_id="SMN";
					} 

					
					$ex_booking="";
					if($booking_no!='') $ex_booking=explode('-',$booking_no); else $ex_booking="_";
					$process_id=array_unique(explode(',',$batch_array[$row[csf('batch_id')]]['process_id']));
					$process_name='';
					foreach ($process_id as $val)
					{
						if($process_name=='') $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
					}
					$on_bill_qty=0;
					
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; //rec_qty grey_qty
				
					$avilable_qty=0; $rec_percent=0; $bill_qty=0;
					$bill_qty=$bill_qty_array[$row[csf('id')]][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('color_id')]]['qty'];
					$roll_qty=$bill_qty_array[$row[csf('id')]][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('color_id')]]['roll'];
					
					/*if($data[6]==1)
					{
						$rec_percent=$row[csf('order_qnty')]/$row[csf('rec_qty')];
						$used_qty=($grey_used_arr[$row[csf('dtls_id')]]*$rec_percent)-$bill_qty;
					}
					else if($data[6]==2)*/
					

					$rec_percent=$row[csf('order_qnty')]/$row[csf('rec_qty')];
					
					if($process_cost_maintain ==1){
					$used_qty=($grey_used_arr[$row[csf('dtls_id')]][$row[csf('entry_form')]]*$rec_percent);//-$bill_qty;
					}
					else
					{
					$used_qty= $row[csf('grey_qty')];
					}

					//$used_qty=($grey_used_arr[$row[csf('dtls_id')]]*$rec_percent);//-$bill_qty;
					$avilable_qty=$row[csf('order_qnty')]-$bill_qty;
						
					$avilable_roll=$roll_no_arr[$row[csf('dtls_id')]]-$roll_qty;
					if ($variable_check==1) $on_bill_qty=$used_qty; //$batch_array[$row[csf('batch_id')]]['batch_qnty'];
					else $on_bill_qty=$row[csf('order_qnty')];

					if($bookingDtlsArr[$row[csf('booking_no_id')]][31]['sub_process_id']){
						$process_name=$bookingDtlsArr[$row[csf('booking_no_id')]][31]['process_id'];
						// $sub_process_id=$bookingDtlsArr[$row[csf('booking_no_id')]][31]['sub_process_id'];
						$sub_process_id=implode("+",array_unique(explode(",",$bookingDtlsArr[$row[csf('booking_no_id')]][31]['sub_process_id'])));
					}
					if($ex_bill_for==4) //==========FSO for Sales===========
					{

						$order_no=$row[csf('sales_order_no')];
						$style=$row[csf('style_ref_no')];
						$buyer=$row[csf('po_buyer')];
						$job_no=$row[csf('po_job_no')];
						 
						 
					}
					else
					{
						$order_no=$order_arr[$row[csf('po_breakdown_id')]]['po'];
						$style=$order_arr[$row[csf('po_breakdown_id')]]['style'];
						$buyer=$order_arr[$row[csf('po_breakdown_id')]]['buyer'];
						$job_no=$order_arr[$row[csf('po_breakdown_id')]]['job'];
					}



					$str_val=$row[csf('id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$order_no.'_'.$style.'_'.$buyer_arr[$buyer].'_'.$job_no.'_'.$row[csf('carton_roll')].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('fabric_description_id')].'_'.$row[csf('prod_id')].'_'.$product_dtls_arr[$row[csf('prod_id')]].'_'.$row[csf('batch_id')].'_'.$row[csf('color_id')].'_'.$color_arr[$row[csf("color_id")]].'_'.$batch_array[$row[csf('batch_id')]]['process_id'].'_'.$process_name.'_'.$row[csf('dia_width_type')].'_'.$fabric_typee[$row[csf('dia_width_type')]].'_'.number_format($on_bill_qty, 4, '.', '').'_'.$row[csf('challan_no')].'_'.$sub_process_id.'___1'.'_'.$currency_rate;//$currency_rate
					 // echo $str_val.'<br>';
					if($independent==4)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr id="tr_<? echo $all_value; ?>" bgcolor="yellow" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>');" >
							<td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="1" checked ></td>
							<td width="25"><? echo $i; ?></td>
							<td width="50"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="60"><? echo $row[csf('challan_no')]; ?></td>
							<td width="60"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="100" ><?  echo $process_name; ?></td>
							<td width="100" ><?  echo $sub_process_id; ?></td>
							<td width="80" ><? echo $color_arr[$row[csf('color_id')]]; ?></td>
							<td width="80" style="word-break: break-all;"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
							<td width="120" style="word-break: break-all;"><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></td>
							<td width="60" align="right"><? echo number_format($row[csf('order_qnty')],2); ?></td>
							<td width="60" align="right"><? echo number_format($used_qty,2); ?></td>
							<td width="80" style="word-break: break-all;"><? echo $order_no; ?></td>
							<td width="100" style="word-break: break-all;"><? echo $style; ?></td>
							<td style="word-break: break-all;"><? echo $buyer_arr[$buyer]; ?>
							<input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
							<input type="hidden" id="hidsyschallan" value="<? echo $row[csf('recv_number_prefix_num')]; ?>">
							<input type="hidden" id="currid<? echo $i; ?>" value="<? echo $all_value; ?>"></td>

						</tr>
						<?php
						$i++;
					}
					else //bill_for_dsb
					{
						if( strtolower($ex_booking[1])==strtolower($bill_for_id) || strtolower($ex_booking[1])==strtolower($bill_for_sb) || strtolower($ex_booking[1])==strtolower($bill_for_dsb) || strtolower($ex_booking[1])==strtolower($bill_for_id2)) 
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr id="tr_<? echo $all_value; ?>" bgcolor="yellow" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>');" >
								<td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="1" checked ></td>
								<td width="25"><? echo $i; ?></td>
								<td width="50"><p><? echo $row[csf('recv_number')]; ?></p></td>
								<td width="60"><? echo $row[csf('challan_no')]; ?></td>
								<td width="60"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
								<td width="100" ><? //echo $conversion_cost_head_array[$row[csf('process_id')]]; ?></td>
								<td width="100" ><? //echo $conversion_cost_head_array[$row[csf('process_id')]]; ?></td>
								<td width="80" style="word-break: break-all;"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
								<td width="80" style="word-break: break-all;"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
								<td width="120" style="word-break: break-all;"><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></td>
								<td width="60" align="right"><? echo number_format($row[csf('order_qnty')],2); ?></td>
								<td width="60" align="right"><? echo number_format($used_qty,2); ?></td>
								<td width="80" style="word-break: break-all;"><? echo $order_no; ?></td>
								<td width="100" style="word-break: break-all;"><? echo $style; ?></td>
								<td style="word-break: break-all;"><? echo $buyer_arr[$buyer]; ?>
								<input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
								<input type="hidden" id="hidsyschallan" value="<? echo $row[csf('recv_number_prefix_num')]; ?>">
								<input type="hidden" id="currid<? echo $i; ?>" value="<? echo $all_value; ?>"></td>
							</tr>
							<?php
							$i++;
						}
					}
				}
			}
		
			
			foreach($sql_result as $row) // for new row
			{
				$booking_no=0; $independent=''; $bill_for_id=0; $bill_for_sb=0;$bill_for_dsb=0;
				if ($row[csf('entry_form')]==7)
				{
					if($ex_bill_for==1) { if($row[csf('receive_basis')]==4) $independent=4; } //else $independent='';
					if ($row[csf('receive_basis')]==5) $booking_no=$row[csf('booking_no')]; else $booking_no=0;
					if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2) $bill_for_id="SM"; else if($ex_bill_for==3) $bill_for_id="SMN";
				}
				else if ($row[csf('entry_form')]==37)
				{
					if ($row[csf('receive_basis')]==9) 
					{
						if($recive_basis_arr[$row[csf('bookingno')]]==4) $independent=4;
					}
					
					if($ex_bill_for==1) { if($row[csf('receive_basis')]==4) $independent=4; }// else $independent='';
					if($row[csf('receive_basis')]==2 || $row[csf('receive_basis')]==11) 
						$booking_no=$row[csf('booking_no')];
					else if($row[csf('receive_basis')]==9 && $recive_basis_arr[$row[csf('bookingno')]]==5) 
						$booking_no=$row[csf('booking_no')];
					else 
						$booking_no=0;
					if($ex_bill_for==1) { $bill_for_id="Fb"; $bill_for_sb="SB"; $bill_for_dsb="DSB"; } else if($ex_bill_for==2 && $row[csf('receive_basis')]!=4) $bill_for_id="SM"; else if($ex_bill_for==3 && $row[csf('receive_basis')]==11) $bill_for_id="SBKD"; else if($ex_bill_for==3) $bill_for_id="SMN";
					if($ex_bill_for==1 && $row[csf('receive_basis')]==11){ $bill_for_id="FSB";$bill_for_id2="DSB";$bill_for_sb="SB";}
				}
				else if ($row[csf('entry_form')]==66)
				{
					$rec_basis=0; $bookinNo=""; $bookingId=0;// booking id is barcode, and booking no is delv id
					$rec_basis=0;
					$bookinNo=$row[csf('booking_no')];
					$bookingId=$row[csf('booking_id')];
					
					if($ex_bill_for==1) { if($rec_basis==0) $independent=4; } //else $independent='';
					if ($rec_basis==0) $booking_no=$bookinNo; else $booking_no=0;
					if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2 && $rec_basis!=0) $bill_for_id="SM"; else if($ex_bill_for==3) $bill_for_id="SMN";
				}
				
				else if ($row[csf('entry_form')]==61) //from Grey Fabric Roll Issue
				{
					$rec_basis=0; $bookinNo=""; $bookingId=0;// booking id is barcode, and booking no is delv id
					$rec_basis=0;
					$bookinNo=$row[csf('booking_no')];
					$bookingId=$row[csf('booking_id')];
					
					if($ex_bill_for==1) { if($rec_basis==0) $independent=4; } //else $independent='';
					if ($rec_basis==0) $booking_no=$bookinNo; else $booking_no=0;
					if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2 && $rec_basis!=0) $bill_for_id="SM"; else if($ex_bill_for==3) $bill_for_id="SMN";
				}
				else if ($row[csf('entry_form')]==68)
				{
					$rec_basis=0; $bookinNo=""; $bookingId=0;// booking id is barcode, and booking no is delv id
					$rec_basis=0;
					$bookinNo=$row[csf('booking_no')];
					$bookingId=$row[csf('booking_id')];
					
					if ($ex_bill_for==1) { if($rec_basis==0) $independent=4; } //else $independent='';
					if ($rec_basis==0) $booking_no=$bookinNo; else $booking_no=0;
					if ($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2 && $rec_basis!=0) $bill_for_id="SM"; else if($ex_bill_for==3) $bill_for_id="SMN";
				}
				else if ($row[csf('entry_form')]==225)
				{
					$rec_basis=0; $bookinNo=""; $bookingId=0;// booking id is barcode, and booking no is delv id
					$rec_basis=0;
					$bookinNo=$row[csf('sales_booking_no')];
					$bookingId=$row[csf('booking_id')];
					
					
					if ($ex_bill_for==4) $bill_for_id="Fb"; else if($ex_bill_for==2 && $rec_basis!=0) $bill_for_id="SM"; else if($ex_bill_for==3) $bill_for_id="SMN";
				}
				
				$ex_booking=""; $bill_qty=0;
				if($booking_no!='') $ex_booking=explode('-',$booking_no); else $ex_booking="_";
				//echo $row[csf('booking_no')];
				//if($ex_booking[1]!='Fb') echo $ex_booking[1];
				$bill_qty=$bill_qty_array[$row[csf('id')]][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('color_id')]]['qty'];
				$roll_qty=$bill_qty_array[$row[csf('id')]][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('color_id')]]['roll'];
				//echo $currency_rate.'A';

				$avilable_qty=$row[csf('rec_qnty')]-$bill_qty;
				$avilable_roll=$roll_no_arr[$row[csf('dtls_id')]]-$roll_qty;
				
				$rec_percent=$row[csf('order_qnty')]/$row[csf('rec_qty')];

				//$process_cost_maintain=return_field_value("process_costing_maintain","variable_settings_production","company_name=$exdata[0] and variable_list in (34) and is_deleted=0 and status_active=1"); 
				// echo $process_cost_maintain.'==d';
				if($process_cost_maintain ==1){ ////Gray Used Qty Calculation
					if($used_qty) $used_qty=$used_qty;
					else $used_qty=($grey_used_arr[$row[csf('dtls_id')]][$row[csf('entry_form')]]*$rec_percent);//-$bill_qty;
				}
				else
				{
					$production_qty=$row[csf('production_qty')];
					if($row[csf('grey_qty')]) $row[csf('grey_qty')]=$row[csf('grey_qty')];else $row[csf('grey_qty')]=$production_qty;
					$used_qty= $row[csf('grey_qty')];
					//echo $variable_check.'='. $row[csf('grey_qty')].'='.$row[csf('production_qty')].'='.$used_qty.'=d';
				} 
				// echo $process_cost_maintain.'='.$row[csf('grey_qty')].'<br>';
				
				$avilable_qty=$used_qty-$bill_qty;
				//$usd=$row[csf('order_qnty')].'='.$row[csf('rec_qty')].'='.$grey_used_arr[$row[csf('dtls_id')]].'='.$bill_qty;
				//$all_value=$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$row[csf('prod_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('fabric_description_id')].'_'.$row[csf('dia_width_type')].'_'.$row[csf('batch_id')];
				$all_value=$row[csf('id')];
				
				$process_id=array_unique(explode(',',$batch_array[$row[csf('batch_id')]]['process_id']));
				$process_name='';
				foreach ($process_id as $val)
				{
					if($process_name=='') $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
				}
				if($used_qty>0) //Gray Used Qty Calculation
				{
				//$used_qty=($used_qty/$row[csf('rec_qty')])*$row[csf('order_qnty')];
				}
				//echo $variable_check.'dddx';
				$on_bill_qty=0;
				if ($variable_check==1) $on_bill_qty=$used_qty;//$batch_array[$row[csf('batch_id')]]['batch_qnty'];
				else $on_bill_qty=$row[csf('order_qnty')];
				//echo $on_bill_qty.'d';
				$fabric_description=$composition_arr[$row[csf('fabric_description_id')]];
				// echo strtolower($ex_booking[1]).'='.strtolower($bill_for_id).'='.strtolower($ex_booking[1]).'='.strtolower($bill_for_sb).'<br>';
				
				if($bookingDtlsArr[$row[csf('booking_no_id')]][31]['sub_process_id']){
					$process_name=$bookingDtlsArr[$row[csf('booking_no_id')]][31]['process_id'];
					// $sub_process_id=$bookingDtlsArr[$row[csf('booking_no_id')]][31]['sub_process_id'];
					$sub_process_id=implode("+",array_unique(explode(",",$bookingDtlsArr[$row[csf('booking_no_id')]][31]['sub_process_id'])));
				}

				if($row[csf('challan_no')]){
					$challan_no=$row[csf('challan_no')];
				}else{
					$challan_no=$po_wise_data[$row[csf('po_breakdown_id')]]['delivery_challan'];
				}
				if($ex_bill_for==4) 
				{

					$order_no=$row[csf('sales_order_no')];
					$style=$row[csf('style_ref_no')];
					$buyer=$row[csf('po_buyer')];
					$job_no=$row[csf('po_job_no')];
						
				}
				else
				{
					$order_no=$order_arr[$row[csf('po_breakdown_id')]]['po'];
					$style=$order_arr[$row[csf('po_breakdown_id')]]['style'];
					$buyer=$order_arr[$row[csf('po_breakdown_id')]]['buyer'];
					$job_no=$order_arr[$row[csf('po_breakdown_id')]]['job'];
				}
				
				
				
				$str_val=$row[csf('id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$order_no.'_'.$style.'_'.$buyer_arr[$buyer].'_'.$job_no.'_'.$row[csf('carton_roll')].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('fabric_description_id')].'_'.$row[csf('prod_id')].'_'.$fabric_description.'_'.$row[csf('batch_id')].'_'.$row[csf('color_id')].'_'.$color_arr[$row[csf("color_id")]].'_'.$batch_array[$row[csf('batch_id')]]['process_id'].'_'.$process_name.'_'.$row[csf('dia_width_type')].'_'.$fabric_typee[$row[csf('dia_width_type')]].'_'.number_format($on_bill_qty, 4, '.', '').'_'.$challan_no.'_'.$sub_process_id.'___1'.'_'.$currency_rate;
				if($independent==4)
				{
					
					if($avilable_qty>0)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr id="tr_<? echo $all_value; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>');" >
							<td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="2" ></td>
							<td width="25"><?=$i; ?></td>
							<td width="80"><p><?=$row[csf('recv_number')]; ?></p></td>
							<td width="60"><? echo $challan_no; ?></td>
							<td width="60"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="100" style="word-break: break-all;"><? echo $process_name; ?></td>
							<td width="100" style="word-break: break-all;"><? echo $sub_process_id; ?></td>
							<td width="80" style="word-break: break-all;" title="<?=$row[csf('color_id')];?>"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
							<td width="80" style="word-break: break-all;"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
							<td width="120" style="word-break: break-all;" title="<?=$row[csf('fabric_description_id')];?>"><? echo $fabric_description; ?></td>
							<td width="60" align="right"><? echo number_format($row[csf('order_qnty')],2); ?></td>
							<td width="60" align="right"><? echo number_format($used_qty,2); ?></td>
							<td width="80" style="word-break: break-all;" title="<?=$row[csf('po_breakdown_id')];?>"><? echo $order_no; ?></td>
							<td width="100" style="word-break: break-all;"><? echo $style; ?></td>
							<td style="word-break: break-all;"><? echo $buyer_arr[$buyer]; ?>
							<input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
							<input type="hidden" id="hidsyschallan" value="<? echo $row[csf('recv_number_prefix_num')]; ?>">
							<input type="hidden" id="currid<? echo $i; ?>" value="<? echo $all_value; ?>"></td>
						</tr>
						<?php
						$i++;
					}
				}
				else
				{ //bill_for_dsb
				// echo $avilable_qty.'='.strtolower($ex_booking[1]).'='.strtolower($bill_for_id).'='.strtolower($ex_booking[1]).'='.strtolower($bill_for_sb2).'<br>';
					if( strtolower($ex_booking[1])==strtolower($bill_for_id) || strtolower($ex_booking[1])==strtolower($bill_for_sb) || strtolower($ex_booking[1])==strtolower($bill_for_dsb) || strtolower($ex_booking[1])==strtolower($bill_for_id2)) 
					{
						if($avilable_qty>0)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$process_id=$batch_array[$row[csf('batch_id')]]['process_id'];
							// $booking_dtls_data_arr[$row[csf('booking_id')]][$row[csf('po_breakdown_id')]][$row[csf('fabric_description_id')]][$process_id][$row[csf('color_id')]]['rate'];
							?>
							<tr id="tr_<? echo $all_value; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>');" >
								<td width="30" align="center"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="2" ></td>
								<td width="25"><? echo $i; ?></td>
								<td width="80"><p><? echo $row[csf('recv_number')]; ?></p></td>
								<td width="60"><? echo $challan_no; ?></td>
								<td width="60"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
								<td width="100" ><? //echo $conversion_cost_head_array[$row[csf('process_id')]]; ?></td>
								<td width="100" ><? //echo $conversion_cost_head_array[$row[csf('process_id')]]; ?></td>
								<td width="80" style="word-break: break-all;"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
								<td width="80" style="word-break: break-all;"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
								<td width="120" style="word-break: break-all;"><? echo $fabric_description; ?></td>
								<td width="60" align="right"><? echo number_format($row[csf('order_qnty')],2); ?></td>
								<td width="60" title="Gray Used/Tot Fin Rec*Fin Rec Qty" align="right"><? echo number_format($used_qty,2); ?></td>
								<td width="80" style="word-break: break-all;"><? echo $order_no; ?></td>
								<td width="100" style="word-break: break-all;"><? echo $style; ?></td>
								<td style="word-break: break-all;"><? echo $buyer_arr[$buyer]; ?>
									<input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
									<input type="hidden" id="hidsyschallan" value="<? echo $row[csf('recv_number_prefix_num')]; ?>">
									<input type="hidden" id="currid<? echo $i; ?>" value="<? echo $all_value; ?>"></td>
							</tr>
							<?php
							$i++;
						}
					}
				}
			}



			//fabric service booking
			//$dyeing_company_cond=" and dyeing_company=$exdata[1]";
			 $sql_service=" SELECT a.id as mst_id, a.entry_form, a.receive_basis, a.recv_number,a.recv_number_prefix_num, a.challan_no, a.receive_date, b.booking_id as bookingno, $year_cond as year, b.batch_id, b.prod_id, b.body_part_id, b.febric_description_id as fabric_description_id, b.color_id, sum(b.batch_issue_qty) as rec_qnty, sum(b.grey_used) as grey_qty, b.id as dtls_id, b.order_id as po_breakdown_id, b.booking_id, b.booking_no, b.process_id FROM inv_receive_mas_batchroll a, pro_grey_batch_dtls b WHERE a.id=b.mst_id and a.entry_form=92 AND a.company_id=$exdata[0] and dyeing_company=$exdata[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $date_cond $manual_challan_cond
				group by a.id, a.entry_form, a.receive_basis,a.recv_number, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.booking_id, a.insert_date, b.batch_id, b.prod_id, b.body_part_id, b.febric_description_id, b.color_id, b.order_id, b.id, b.booking_id, b.booking_no,b.process_id order by a.recv_number_prefix_num DESC";
				 //echo $sql_service;
			$sql_result_service=sql_select($sql_service);
			
			//echo $currency_rate.'D';

			foreach($sql_result_service as $row) // for update row
			{
				$all_value=$row[csf('dtls_id')];
				if(in_array($all_value,$str_arr))
				{
					$booking_no=0; $independent=''; $bill_for_id=0; $bill_for_sb=0;
					$rec_basis=0; $bookinNo=""; $bookingId=0;// booking id is barcode, and booking no is delv id
					$rec_basis=0;
					$bookinNo=$row[csf('booking_no')];
					$bookingId=$row[csf('booking_id')];
					
					$process_id=array_unique(explode(',',$batch_array[$row[csf('batch_id')]]['process_id']));
					$process_name='';
					foreach ($process_id as $val)
					{
						if($process_name=='') $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
					}
					$on_bill_qty=0;
					
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; //rec_qty grey_qty
				
					$avilable_qty=0; $rec_percent=0; $bill_qty=0;
					//$bill_qty=$bill_qty_array[$row[csf('dtls_id')]]['qty'];
					$bill_qty=$bill_qty_array2[$row[csf('challan_no')]][$row[csf('dtls_id')]][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('color_id')]]['qty'];
					$roll_qty=$bill_qty_array2[$row[csf('challan_no')]][$row[csf('dtls_id')]][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('color_id')]]['roll'];

					
					$used_qty=$row[csf('grey_qty')];//-$bill_qty;
					$avilable_qty=$row[csf('rec_qnty')]-$bill_qty;

					if ($variable_check==1) $on_bill_qty=$used_qty; //$batch_array[$row[csf('batch_id')]]['batch_qnty'];
					else $on_bill_qty=$row[csf('rec_qnty')];
					$fabric_description=$composition_arr[$row[csf('fabric_description_id')]];
					
					$booking_rate=$booking_dtls_data_arr[$row[csf("booking_id")]][$row[csf('po_breakdown_id')]][$row[csf('fabric_description_id')]][$row[csf('process_id')]][$row[csf("color_id")]]['rate'];
					$sub_process_id=$booking_dtls_data_arr[$row[csf("booking_id")]][$row[csf('po_breakdown_id')]][$row[csf('fabric_description_id')]][$row[csf('process_id')]][$row[csf("color_id")]]['sub_process_id'];
					
					$str_val=$row[csf('dtls_id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$order_arr[$row[csf('po_breakdown_id')]]['po'].'_'.$order_arr[$row[csf('po_breakdown_id')]]['style'].'_'.$buyer_arr[$order_arr[$row[csf('po_breakdown_id')]]['buyer']].'_'.$po_array[$row[csf('po_breakdown_id')]]['job'].'_'.$row[csf('carton_roll')].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('fabric_description_id')].'_'.$row[csf('prod_id')].'_'.$fabric_description.'_'.$row[csf('batch_id')].'_'.$row[csf('color_id')].'_'.$color_arr[$row[csf("color_id")]].'_'.$batch_array[$row[csf('batch_id')]]['process_id'].'_'.$process_name.'_'.$row[csf('dia_width_type')].'_'.$fabric_typee[$row[csf('dia_width_type')]].'_'.number_format($on_bill_qty, 4, '.', '').'_'.$row[csf('challan_no')].'_'.$conversion_cost_head_array[$row[csf('process_id')]].'_'.$row[csf('process_id')].'_2_'.$booking_rate.'_'.$sub_process_id.'_'.$currency_rate;
					
				
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr id="tr_<? echo $all_value; ?>" bgcolor="yellow" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>');" >
						<td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="1" checked ></td>
						<td width="25"><? echo $i; ?></td>
						<td width="80"><p><? echo $row[csf('recv_number')]; ?></p></td>
						<td width="60"><? echo $row[csf('challan_no')]; ?></td>
						<td width="60"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
						<td width="100" ><? echo $conversion_cost_head_array[$row[csf('process_id')]]; ?></td>
						<td width="100" ><? echo $sub_process_id; ?></td>
						<td width="80" ><? echo $color_arr[$row[csf('color_id')]]; ?></td>
						
						<td width="80" style="word-break: break-all;"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
						<td width="120" style="word-break: break-all;"><? echo $composition_arr[$row[csf('fabric_description_id')]]; ?></td>
						<td width="60" align="right"><? echo number_format($row[csf('rec_qnty')],2); ?></td>
						<td width="60" align="right"><? echo number_format($used_qty,2); ?></td>
						<td width="80" style="word-break: break-all;"><? echo $order_arr[$row[csf('po_breakdown_id')]]['po']; ?></td>
						<td width="100" style="word-break: break-all;"><? echo $order_arr[$row[csf('po_breakdown_id')]]['style']; ?></td>
						<td style="word-break: break-all;"><? echo $buyer_arr[$order_arr[$row[csf('po_breakdown_id')]]['buyer']]; ?>
						<input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
						<input type="hidden" id="hidsyschallan" value="<? echo $row[csf('recv_number_prefix_num')]; ?>">
						<input type="hidden" id="currid<? echo $i; ?>" value="<? echo $all_value; ?>"></td>
					</tr>
					<?php
					$i++;
				}
			}
			
			foreach($sql_result_service as $row) // for new row
			{
				
				$booking_no=0; $independent=''; $bill_for_id=0; $bill_for_sb=0;
				//echo $row[csf('rec_qnty')]."yuyu";
				$rec_basis=0; $bookinNo=""; $bookingId=0;// booking id is barcode, and booking no is delv id
				$rec_basis=0;
				$bookinNo=$row[csf('booking_no')];
				$bookingId=$row[csf('booking_id')];
				
				if ($ex_bill_for==1) { if($rec_basis==0) $independent=4; } //else $independent='';
				if ($rec_basis==0) $booking_no=$bookinNo; else $booking_no=0;
				if ($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2 && $rec_basis!=0) $bill_for_id="SM"; else if($ex_bill_for==3) $bill_for_id="SMN";
				
				$ex_booking=""; $bill_qty=0;
				if($booking_no!='') $ex_booking=explode('-',$booking_no); else $ex_booking="_";
				//echo $row[csf('booking_no')];
				//if($ex_booking[1]!='Fb') echo $ex_booking[1];
				//$bill_qty=$bill_qty_array[$row[csf('dtls_id')]]['qty'];
				$bill_qty=$bill_qty_array2[$row[csf('challan_no')]][$row[csf('dtls_id')]][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('color_id')]]['qty'];
				$roll_qty=$bill_qty_array2[$row[csf('challan_no')]][$row[csf('dtls_id')]][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('color_id')]]['roll'];
			   // $roll_qty=$bill_qty_array[$row[csf('id')]]['roll'];
				
				$avilable_qty=$row[csf('rec_qnty')]-$bill_qty;
				$avilable_roll=$roll_no_arr[$row[csf('dtls_id')]]-$roll_qty;
				
				$rec_percent=$row[csf('order_qnty')]/$row[csf('rec_qty')];
				$used_qty=$row[csf('grey_qty')];//-$bill_qty;
				//$avilable_qty=$row[csf('order_qnty')]-$bill_qty;
				//$usd=$row[csf('order_qnty')].'='.$row[csf('rec_qty')].'='.$grey_used_arr[$row[csf('dtls_id')]].'='.$bill_qty;
				//$all_value=$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$row[csf('prod_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('fabric_description_id')].'_'.$row[csf('dia_width_type')].'_'.$row[csf('batch_id')];
				$all_value=$row[csf('dtls_id')];
				
				$process_id=array_unique(explode(',',$batch_array[$row[csf('batch_id')]]['process_id']));
				$process_name='';
				foreach ($process_id as $val)
				{
					if($process_name=='') $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
				}
				// echo $currency_rate.'Dx';;
				
				$on_bill_qty=0;
				if ($variable_check==1) $on_bill_qty=$used_qty;//$batch_array[$row[csf('batch_id')]]['batch_qnty'];
				else $on_bill_qty=$row[csf('rec_qnty')];
				$booking_rate=$booking_dtls_data_arr[$row[csf("booking_id")]][$row[csf('po_breakdown_id')]][$row[csf('fabric_description_id')]][$row[csf('process_id')]][$row[csf("color_id")]]['rate'];
				$sub_process_id=$booking_dtls_data_arr[$row[csf("booking_id")]][$row[csf('po_breakdown_id')]][$row[csf('fabric_description_id')]][$row[csf('process_id')]][$row[csf("color_id")]]['sub_process_id']; 
				$str_val=$row[csf('dtls_id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$order_arr[$row[csf('po_breakdown_id')]]['po'].'_'.$order_arr[$row[csf('po_breakdown_id')]]['style'].'_'.$buyer_arr[$order_arr[$row[csf('po_breakdown_id')]]['buyer']].'_'.$po_array[$row[csf('po_breakdown_id')]]['job'].'_'.$row[csf('carton_roll')].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('fabric_description_id')].'_'.$row[csf('prod_id')].'_'.$composition_arr[$row[csf('fabric_description_id')]].'_'.$row[csf('batch_id')].'_'.$row[csf('color_id')].'_'.$color_arr[$row[csf("color_id")]].'_'.$batch_array[$row[csf('batch_id')]]['process_id'].'_'.$process_name.'_'.$row[csf('dia_width_type')].'_'.$fabric_typee[$row[csf('dia_width_type')]].'_'.number_format($on_bill_qty, 4, '.', '').'_'.$row[csf('challan_no')].'_'.$conversion_cost_head_array[$row[csf('process_id')]].'_0_2_'.$booking_rate.'_'.$currency_rate;
				//$str_val=$row[csf('id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$order_no.'_'.$style.'_'.$buyer_arr[$buyer].'_'.$job_no.'_'.$row[csf('carton_roll')].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('fabric_description_id')].'_'.$row[csf('prod_id')].'_'.$fabric_description.'_'.$row[csf('batch_id')].'_'.$row[csf('color_id')].'_'.$color_arr[$row[csf("color_id")]].'_'.$batch_array[$row[csf('batch_id')]]['process_id'].'_'.$process_name.'_'.$row[csf('dia_width_type')].'_'.$fabric_typee[$row[csf('dia_width_type')]].'_'.number_format($on_bill_qty, 4, '.', '').'_'.$challan_no.'_'.$sub_process_id.'_0_0_1'.'_'.$currency_rate;
				//.'_'.$challan_no.'_'.$sub_process_id.'_0_0_1'.'_'.$currency_rate
				//.'_'.$row[csf('challan_no')].'_'.$conversion_cost_head_array[$row[csf('process_id')]].'_'.$row[csf('process_id')].'_2_'.$booking_rate.'_'.$sub_process_id.'_'.$currency_rate;//.'_'.$challan_no.'_'.$sub_process_id.'___1'.'_'.$currency_rate//.'_'.$challan_no.'_'.$sub_process_id.'___1'.'_'.$currency_rate//.'_'.$challan_no.'_'.$sub_process_id.'___1'.'_'.$currency_rate///////.'_'.$challan_no.'_'.$sub_process_id.'___1'.'_'.$currency_rate/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////.'_'.$challan_no.'_'.$sub_process_id.'_0_2_1'.'_'.$currency_rate
				
				
				if($avilable_qty>0)
				{
					
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr id="tr_<? echo $all_value; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>');" >
						<td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="2" ></td>
						
						<td width="25"><? echo $i; ?></td>
						<td width="80"><p><? echo $row[csf('recv_number')]; ?></p></td>

						<td width="60" style=" word-break:break-all"><? echo $row[csf('challan_no')]; ?></td>
						<td width="60"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
						<td width="100" ><? echo $conversion_cost_head_array[$row[csf('process_id')]]; ?></td>
						<td width="100" ><? echo $sub_process_id; ?></td>
						<td width="80" style="word-break: break-all;"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
						<td width="80" style="word-break: break-all;"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
						<td width="120" style="word-break: break-all;"><? echo $composition_arr[$row[csf('fabric_description_id')]]; ?></td>
						<td width="60" align="right"><? echo number_format($row[csf('rec_qnty')],2); ?></td>
						<td width="60" align="right"><? echo number_format($used_qty,2); ?></td>
						<td width="80" style="word-break: break-all;"><? echo $order_arr[$row[csf('po_breakdown_id')]]['po']; ?></td>
						<td width="100" style="word-break: break-all;"><? echo $order_arr[$row[csf('po_breakdown_id')]]['style']; ?></td>
						<td style="word-break: break-all;"><? echo $buyer_arr[$order_arr[$row[csf('po_breakdown_id')]]['buyer']]; ?>
						<input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
						<input type="hidden" id="hidsyschallan" value="<? echo $row[csf('recv_number_prefix_num')]; ?>">
						<input type="hidden" id="currid<? echo $i; ?>" value="<? echo $all_value; ?>"></td>
					</tr>
					<?php
					$i++;
				}
			}
			?>
            </table>
            </div>
            </div>
            <div>
                <table width="940px" >
                    <tr>
                        <td colspan="10" align="center">
                        <input type="button" id="show_button" class="formbutton" style="width:100px" value="Close" onClick="window_close(0)" />
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        </div>
	</body>           
	<script src="../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="load_dtls_data") 
{
	$exdata=explode("__",$data);
	$mstid=$exdata[0];
	$billfor=$exdata[1];

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$roll_no_arr=return_library_array( "select id, no_of_roll from  pro_grey_prod_entry_dtls",'id','no_of_roll');
   
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$booking_arr=return_library_array( "select a.id,a.booking_no from wo_booking_mst a,subcon_outbound_bill_dtls b where a.id=b.wo_num_id and b.mst_id='$mstid' ",'id','booking_no');
	

	$determ_arr = return_library_array( "select mst_id, copmposition_id from lib_yarn_count_determina_dtls",'mst_id','copmposition_id');
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

	


	//var_dump($order_array);
		if($billfor==4) //========FSO================ 
		{
			    $sql_order_sales="Select b.id, b.job_no as po_number,b.po_buyer as buyer_name, b.style_ref_no,b.po_job_no from subcon_outbound_bill_dtls a, fabric_sales_order_mst b where a.order_id=b.id  and a.mst_id='$mstid' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$sql_order_sales_result=sql_select($sql_order_sales); 
			foreach ($sql_order_sales_result as $row)
			{
				$job_order_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
				$job_order_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
				$job_order_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
				$job_order_arr[$row[csf('id')]]['job']=$row[csf('po_job_no')];
			}
			unset($sql_order_result);
			//print_r($po_array);
	   }

	
	//$sql="select id as upd_id, delivery_id, delivery_date, challan_no, item_id, body_part_id, color_id, batch_id, febric_description_id, dia_width_type, add_process, add_process_name, packing_qnty as carton_roll, delivery_qty, lib_rate_id, rate, add_rate_id, add_rate, amount, remarks, order_id from subcon_inbound_bill_dtls where mst_id=$data  and status_active=1 and is_deleted=0 order by id ASC";
	$sql="select id, receive_id, receive_date, item_id, roll_no, wo_num_id, receive_qty, uom, rate, amount, remarks, currency_id,amount_usd,ex_rate, process_id, prod_mst_id, order_id, color_id, body_part_id, febric_description_id, batch_id, dia_width_type, challan_no, rec_qty_pcs, sub_process_id, source  from subcon_outbound_bill_dtls where mst_id='$mstid' and status_active=1 and 
is_deleted=0 and process_id=4 order by id ASC";
	//echo $sql;
	$sql_result_arr =sql_select($sql); $str_val="";


	$order_id_popup=array();
	$batch_id_popup=array();
	$produt_id_popup=array();
	foreach ($sql_result_arr as $row)
	{
		array_push($order_id_popup, $row[csf('order_id')]);
		array_push($batch_id_popup, $row[csf('batch_id')]);
		array_push($produt_id_popup, $row[csf('item_id')]);
		$orderIds .=$row[csf('order_id')].",";
	}
	$order_id_popup=array_unique($order_id_popup);
	$order_id_cond=where_con_using_array($order_id_popup,0,"b.id");

	$batch_id_popup=array_unique($batch_id_popup);
	$batch_id_cond=where_con_using_array($batch_id_popup,0,"a.id");

	$produt_id_popup=array_unique($produt_id_popup);
	$product_id_cond=where_con_using_array($produt_id_popup,0,"id");
//echo "select id,product_name_details from  product_details_master where 1=1 and $product_id_cond";
	$product_dtls_arr=return_library_array( "select id,product_name_details from  product_details_master where 1=1  $product_id_cond",'id','product_name_details');
	unset($produt_id_popup);
	unset($product_id_cond);
	if($billfor==2 || $billfor==1)
	{
	$job_order_arr=array();
	$sql_job="Select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $order_id_cond";
	//echo $sql_job;
	$sql_job_result =sql_select($sql_job);
	foreach($sql_job_result as $row)
	{
		$job_order_arr[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
		$job_order_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
		$job_order_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$job_order_arr[$row[csf('id')]]['po']=$row[csf('po_number')];
	}
   } 
	unset($sql_job_result);
	unset($order_id_cond);
	unset($order_id_popup);
	$batch_array=array();
	if(count($batch_id_popup))
	{
		$grey_sql="Select a.id, a.batch_no, a.extention_no, a.process_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form!=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batch_id_cond group by a.id, a.batch_no, a.extention_no, a.process_id";
		//echo $grey_sql;
		$grey_sql_result =sql_select($grey_sql);
		
		foreach($grey_sql_result as $row)
		{
			$batch_array[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$batch_array[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
			$batch_array[$row[csf('id')]]['process_id']=$row[csf('process_id')];
			$batch_array[$row[csf('id')]]['batch_qnty']=$row[csf('batch_qnty')];
		}
		unset($grey_sql_result);
	}
	unset($batch_id_cond);
	unset($batch_id_cond);

	//====================================================================================================================
	$po_ids=rtrim($orderIds,",");
	$booking_sql="select a.id,a.booking_mst_id,a.pre_cost_fabric_cost_dtls_id,a.po_break_down_id,a.rate,a.description,a.process,a.gmts_color_id,b.lib_yarn_count_deter_id as deter_id,a.sub_process_id
	from wo_booking_dtls a,wo_pre_cost_fabric_cost_dtls b,wo_booking_mst c where a.pre_cost_fabric_cost_dtls_id=b.id and  a.status_active=1 and a.booking_mst_id=c.id    and a.po_break_down_id in ($po_ids) ";

	$booking_sql_data=sql_select($booking_sql);
	$processId="";
	foreach($booking_sql_data as $val){
		$sub_process_arr=explode(",",$val[csf('sub_process_id')]);
		foreach($sub_process_arr as $sub_processId){
			$processId.=$conversion_cost_head_array[$sub_processId].",";
		}
		$booking_dtls_data_arr[$val[csf('booking_mst_id')]][$val[csf('po_break_down_id')]][$val[csf('deter_id')]][$val[csf('process')]][$val[csf('gmts_color_id')]]['rate']=$val[csf('rate')];
		$bookingDtlsArr[$val[csf('booking_mst_id')]][$val[csf('process')]]['sub_process_id']=rtrim($processId,',');;
		$bookingDtlsArr[$val[csf('booking_mst_id')]][$val[csf('process')]]['process_id']=$conversion_cost_head_array[$val[csf('process')]];;
	}

//========================================================================================================================


	
	foreach ($sql_result_arr as $row)
	{

		// $bookingDtlsArr[$row[csf('wo_num_id')]][31]['sub_process_id'];
		// $bookingDtlsArr[$row[csf('wo_num_id')]][$val[csf('process')]]['process_id'];

		$amount_usd=$row[csf('amount_usd')];
		$ex_rate=$row[csf('ex_rate')];

		$process_id=array_unique(explode(',',$batch_array[$row[csf('batch_id')]]['process_id']));
		$process_name='';
		foreach ($process_id as $val)
		{
			if($process_name=='') $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
		}
		if($row[csf('source')]==2)
		{
			$product_name=$composition_arr[$row[csf('febric_description_id')]];
		}
		else
		{
			$product_name=$product_dtls_arr[$row[csf('item_id')]];
		}
		if($row[csf('recv_number_prefix_num')]=="") $row[csf('recv_number_prefix_num')]=0;
		if($row[csf('sub_process_id')]=="") $row[csf('sub_process_id')]=0;
		
		/*$str_val=$row[csf('id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$order_arr[$row[csf('po_breakdown_id')]]['po'].'_'.$order_arr[$row[csf('po_breakdown_id')]]['style'].'_'.$buyer_arr[$order_arr[$row[csf('po_breakdown_id')]]['buyer']].'_'.$po_array[$row[csf('po_breakdown_id')]]['job'].'_'.$row[csf('carton_roll')].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('fabric_description_id')].'_'.$row[csf('prod_id')].'_'.$fabric_description.'_'.$row[csf('batch_id')].'_'.$row[csf('color_id')].'_'.$color_arr[$row[csf("color_id")]].'_'.$batch_array[$row[csf('batch_id')]]['process_id'].'_'.$process_name.'_'.$row[csf('dia_width_type')].'_'.$fabric_typee[$row[csf('dia_width_type')]].'_'.number_format($on_bill_qty, 4, '.', '').'_'.$row[csf('challan_no')].'_0_0_1';*/
		//$str_val=$row[csf('receive_id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number_prefix_num')].'_'.$row[csf('order_id')].'_'.$job_order_arr[$row[csf('order_id')]]['po'].'_'.$job_order_arr[$row[csf('order_id')]]['style'].'_'.$buyer_arr[$job_order_arr[$row[csf('order_id')]]['buyer']].'_'.$job_order_arr[$row[csf('order_id')]]['job'].'_'.$row[csf('roll_no')].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('febric_description_id')].'_'.$row[csf('item_id')].'_'.$product_dtls_arr[$row[csf('item_id')]].'_'.$row[csf('batch_id')].'_'.$row[csf('color_id')].'_'.$color_arr[$row[csf("color_id")]].'_'.$batch_array[$row[csf('batch_id')]]['process_id'].'_'.$process_name.'_'.$row[csf('dia_width_type')].'_'.$fabric_typee[$row[csf('dia_width_type')]].'_'.number_format($row[csf('receive_qty')], 2, '.', '').'_'.$row[csf('challan_no')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('id')].'_'.$row[csf('remarks')].'_'.$row[csf('uom')];
		
		//$str_val=$row[csf('id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$order_arr[$row[csf('po_breakdown_id')]]['po'].'_'.$order_arr[$row[csf('po_breakdown_id')]]['style'].'_'.$buyer_arr[$order_arr[$row[csf('po_breakdown_id')]]['buyer']].'_'.$po_array[$row[csf('po_breakdown_id')]]['job'].'_'.$row[csf('carton_roll')].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('fabric_description_id')].'_'.$row[csf('prod_id')].'_'.$product_dtls_arr[$row[csf('prod_id')]].'_'.$row[csf('batch_id')].'_'.$row[csf('color_id')].'_'.$color_arr[$row[csf("color_id")]].'_'.$batch_array[$row[csf('batch_id')]]['process_id'].'_'.$process_name.'_'.$row[csf('dia_width_type')].'_'.$fabric_typee[$row[csf('dia_width_type')]].'_'.number_format($on_bill_qty, 2, '.', '').'_'.$row[csf('challan_no')];
		$booking_no=$booking_arr[$row[csf('wo_num_id')]];

		if($bookingDtlsArr[$row[csf('wo_num_id')]][31]['sub_process_id']){
			$process_name=$bookingDtlsArr[$row[csf('wo_num_id')]][31]['process_id'];
			$process_id=31;
			// $sub_process_id=$bookingDtlsArr[$row[csf('booking_no_id')]][31]['sub_process_id'];
			$sub_process_id=implode("+",array_unique(explode(",",$bookingDtlsArr[$row[csf('wo_num_id')]][31]['sub_process_id'])));
		}
		if($process_name ==""){
			$process_name=$conversion_cost_head_array[$row[csf('sub_process_id')]];
			$process_id=$row[csf('sub_process_id')];
		}


		$booking_rate=$booking_dtls_data_arr[$row[csf("wo_num_id")]][$row[csf('order_id')]][$row[csf('febric_description_id')]][$row[csf('process_id')]][$row[csf("color_id")]]['rate'];
		if($str_val=="") $str_val=$row[csf('receive_id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number_prefix_num')].'_'.$row[csf('order_id')].'_'.$job_order_arr[$row[csf('order_id')]]['po'].'_'.$job_order_arr[$row[csf('order_id')]]['style'].'_'.$buyer_arr[$job_order_arr[$row[csf('order_id')]]['buyer']].'_'.$job_order_arr[$row[csf('order_id')]]['job'].'_'.$row[csf('roll_no')].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('febric_description_id')].'_'.$row[csf('item_id')].'_'.$product_name.'_'.$row[csf('batch_id')].'_'.$row[csf('color_id')].'_'.$color_arr[$row[csf("color_id")]].'_'.$batch_array[$row[csf('batch_id')]]['process_id'].'_'.$process_name.'_'.$row[csf('dia_width_type')].'_'.$fabric_typee[$row[csf('dia_width_type')]].'_'.number_format($row[csf('receive_qty')], 4, '.', '').'_'.$row[csf('challan_no')].'_'.$row[csf('rate')].'_'.number_format($row[csf('amount')], 4, '.', '').'_'.$row[csf('id')].'_'.$row[csf('remarks')].'_'.$row[csf('uom')].'_'.$process_name.'_'.$process_id.'_'.$row[csf('source')].'_'.$row[csf('currency_id')].'_'.$booking_no.'_'.$row[csf('wo_num_id')].'_'.$booking_rate.'_'.$sub_process_id.'_'.$amount_usd.'_'.$ex_rate;
		
		else $str_val.="###".$row[csf('receive_id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number_prefix_num')].'_'.$row[csf('order_id')].'_'.$job_order_arr[$row[csf('order_id')]]['po'].'_'.$job_order_arr[$row[csf('order_id')]]['style'].'_'.$buyer_arr[$job_order_arr[$row[csf('order_id')]]['buyer']].'_'.$job_order_arr[$row[csf('order_id')]]['job'].'_'.$row[csf('roll_no')].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('febric_description_id')].'_'.$row[csf('item_id')].'_'.$product_name.'_'.$row[csf('batch_id')].'_'.$row[csf('color_id')].'_'.$color_arr[$row[csf("color_id")]].'_'.$batch_array[$row[csf('batch_id')]]['process_id'].'_'.$process_name.'_'.$row[csf('dia_width_type')].'_'.$fabric_typee[$row[csf('dia_width_type')]].'_'.number_format($row[csf('receive_qty')], 4, '.', '').'_'.$row[csf('challan_no')].'_'.$row[csf('rate')].'_'.number_format($row[csf('amount')], 4, '.', '').'_'.$row[csf('id')].'_'.$row[csf('remarks')].'_'.$row[csf('uom')].'_'.$process_name.'_'.$process_id.'_'.$row[csf('source')].'_'.$row[csf('currency_id')].'_'.$booking_no.'_'.$row[csf('wo_num_id')].'_'.$booking_rate.'_'.$sub_process_id.'_'.$amount_usd.'_'.$ex_rate;
	}
	
	echo $str_val;
	exit();
}


/*if ($action=="load_php_dtls_form")
{
	$data = explode("_",$data);
	//echo $data[0].'=='.$data[1];
	$del_id=array_diff(explode(",",$data[0]), explode(",",$data[1]));
	$bill_id=array_intersect(explode(",",$data[0]), explode(",",$data[1]));
	$delete_id=array_diff(explode(",",$data[1]), explode(",",$data[0]));
	$delete_id=implode(",",$delete_id); $del_id=implode(",",$del_id); $bill_id=implode(",",$bill_id);
	//echo $delete_id.'=='.$del_id.'=='.$bill_id;
	$order_array=array();
	$sql_order="Select a.id, a.po_number, b.style_ref_no, b.buyer_name from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
	$sql_order_result=sql_select($sql_order);
	foreach ($sql_order_result as $row)
	{
		$order_array[$row[csf("id")]]['po_number']=$row[csf("po_number")];
		$order_array[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$order_array[$row[csf("id")]]['buyer_name']=$row[csf("buyer_name")];
	}
	//var_dump($order_array);
    $buyer_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name'); 
	$roll_no_arr=return_library_array( "select id, no_of_roll from  pro_grey_prod_entry_dtls",'id','no_of_roll');
    $item_id_arr=return_library_array( "select id,product_name_details from  product_details_master",'id','product_name_details');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$grey_used_arr=return_library_array( "select dtls_id, used_qty from  pro_material_used_dtls",'dtls_id','used_qty');
	
	if( $data[2]!="" )//update===========
	{
		$sql="SELECT id as upd_id, receive_id, receive_date, challan_no, order_id, item_id as prod_id, color_id, body_part_id, roll_no as no_of_roll, receive_qty as order_qnty, uom, 0 as dtls_id, rate, amount, remarks FROM subcon_outbound_bill_dtls  WHERE mst_id=$data[2] and process_id=4 order by id";
	}
	else //insert=================order_wise_pro_details
	{
		if($db_type==0)
		{
			if($bill_id!="" && $del_id!="")
				$sql="(SELECT id as upd_id, receive_id, receive_date, challan_no, order_id, item_id as prod_id, color_id, body_part_id, roll_no as no_of_roll, 0 as rec_qty, receive_qty as order_qnty, uom, 0 as dtls_id, rate, amount, remarks  FROM subcon_outbound_bill_dtls  WHERE receive_id in ($bill_id) and process_id='4' )
				 union 
				 (SELECT 0 as upd_id, c.id as receive_id, a.receive_date, a.challan_no, c.po_breakdown_id as order_id, b.prod_id, b.color_id, b.body_part_id, b.no_of_roll, sum(b.receive_qnty) as rec_qty, sum(c.quantity) as order_qnty, 0 as uom, c.dtls_id, 0 as rate, 0 as amount, null as remarks FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c WHERE a.id = b.mst_id and b.id=c.dtls_id and c.trans_type=1 and c.entry_form in (7,37) AND a.knitting_source=3 AND c.id in ($del_id) and a.item_category=2 and a.id not in (select id from inv_receive_master where knitting_source=3 AND entry_form=37 AND receive_basis in (1,6,9)) group by c.id, a.receive_date, a.challan_no, c.po_breakdown_id, b.prod_id, b.color_id, b.body_part_id, b.no_of_roll, c.dtls_id) order by receive_id";
			else if($bill_id!="" && $del_id=="")
				$sql="SELECT id as upd_id, receive_id, receive_date, challan_no, order_id, item_id as prod_id, color_id, body_part_id, roll_no as no_of_roll, 0 as rec_qty, receive_qty as order_qnty, uom, 0 as dtls_id, rate, amount, remarks  FROM subcon_outbound_bill_dtls  WHERE receive_id in ($bill_id) and process_id='4' order by id";
			else if($bill_id=="" && $del_id!="")
				$sql="SELECT 0 as upd_id, c.id as receive_id, a.receive_date, a.challan_no, c.po_breakdown_id as order_id, b.prod_id, b.color_id, b.body_part_id, b.no_of_roll, sum(b.receive_qnty) as rec_qty, sum(c.quantity) as order_qnty, 0 as uom, c.dtls_id, 0 as rate, 0 as amount, null as remarks FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c WHERE a.id = b.mst_id and b.id=c.dtls_id and c.trans_type=1 and c.entry_form in (7,37) AND a.knitting_source=3 AND c.id in ($del_id) and a.item_category=2 and a.id not in (select id from inv_receive_master where knitting_source=3 AND entry_form=37 AND receive_basis in (1,6,9)) group by c.id, a.receive_date, a.challan_no, c.po_breakdown_id, b.prod_id, b.color_id, b.body_part_id, b.no_of_roll, c.dtls_id order by c.id";
		}
		elseif($db_type==2)
		{
			if($bill_id!="" && $del_id!="")
				$sql="(SELECT id as upd_id, receive_id, receive_date, challan_no, order_id, item_id as prod_id, color_id, body_part_id, roll_no as no_of_roll, 0 as rec_qty, receive_qty as order_qnty, uom, 0 as dtls_id, rate, amount, remarks  FROM subcon_outbound_bill_dtls  WHERE receive_id in ($bill_id) and process_id='4' )
				 union 
				 (SELECT 0 as upd_id, c.id as receive_id, a.receive_date, a.challan_no, c.po_breakdown_id as order_id, b.prod_id, b.color_id, b.body_part_id, b.no_of_roll, sum(b.receive_qnty) as rec_qty, sum(c.quantity) as order_qnty, 0 as uom, c.dtls_id, 0 as rate, 0 as amount, null as remarks FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c WHERE a.id = b.mst_id and b.id=c.dtls_id and c.trans_type=1 and c.entry_form in (7,37) AND a.knitting_source=3 AND c.id in ($del_id) and a.item_category=2 and a.id not in (select id from inv_receive_master where knitting_source=3 AND entry_form=37 AND receive_basis in (1,6,9)) group by c.id, a.receive_date, a.challan_no, c.po_breakdown_id, b.prod_id, b.color_id, b.body_part_id, b.no_of_roll, c.dtls_id) order by receive_id";
			else if($bill_id!="" && $del_id=="")
				$sql="SELECT id as upd_id, receive_id, receive_date, challan_no, order_id, item_id as prod_id, color_id, body_part_id, roll_no as no_of_roll, 0 as rec_qty, receive_qty as order_qnty, uom, 0 as dtls_id, rate, amount, remarks  FROM subcon_outbound_bill_dtls  WHERE receive_id in ($bill_id) and process_id='4' order by id ";
			else if($bill_id=="" && $del_id!="")
				$sql="SELECT 0 as upd_id, c.id as receive_id, a.receive_date, a.challan_no, c.po_breakdown_id as order_id, b.prod_id, b.color_id, b.body_part_id, b.no_of_roll, sum(b.receive_qnty) as rec_qty, sum(c.quantity) as order_qnty, 0 as uom, c.dtls_id, 0 as rate, 0 as amount, null as remarks FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c WHERE a.id = b.mst_id and b.id=c.dtls_id and c.trans_type=1 and c.entry_form in (7,37) AND a.knitting_source=3 AND c.id in ($del_id) and a.item_category=2 and a.id not in (select id from inv_receive_master where knitting_source=3 AND entry_form=37 AND receive_basis in (1,6,9)) group by c.id, a.receive_date, a.challan_no, c.po_breakdown_id, b.prod_id, b.color_id, b.body_part_id, b.no_of_roll, c.dtls_id order by c.id";
		}
	}
	
	$sql_result=sql_select($sql);
	$k=0;
	$num_rowss=count($sql_result);
	foreach ($sql_result as $row)
	{
		 $k++;
		 if( $data[2]!="" )
		 {
			 if($data[1]=="") $data[1]=$row[csf("receive_id")]; else $data[1].=",".$row[csf("receive_id")];
		 }
	?>
         <tr align="center">				
            <td>
				 <? if ($k==$num_rowss) { ?>
                    <input type="hidden" name="issue_id_all" id="issue_id_all"  style="width:80px" value="<? echo $data[1]; ?>" />
                    <input type="hidden" name="delete_id" id="delete_id"  style="width:80px" value="<? echo $delete_id; ?>" />
                 <? } ?>
                <input type="hidden" name="updateiddtls_<? echo $k; ?>" id="updateiddtls_<? echo $k; ?>" value="<? echo ($row[csf("upd_id")] != 0 ? $row[csf("upd_id")] : "") ?>">
                <input type="hidden" name="reciveid_<? echo $k; ?>" id="reciveid_<? echo $k; ?>" value="<? echo $row[csf("receive_id")]; ?>"> 
                <input type="date" name="txtreceivedate_<? echo $k; ?>" id="txtreceivedate_<? echo $k; ?>"  class="datepicker" style="width:60px" value="<? echo change_date_format($row[csf("receive_date")]); ?>" readonly />									
            </td>
            <td>
                <input type="text" name="txtchallenno_<? echo $k; ?>" id="txtchallenno_<? echo $k; ?>"  class="text_boxes" style="width:70px" value="<? echo $row[csf("challan_no")]; ?>" readonly />							 
            </td>
            <td>
                <input type="hidden" name="ordernoid_<? echo $k; ?>" id="ordernoid_<? echo $k; ?>" value="<? echo $row[csf("order_id")]; ?>" style="width:50px" /> 
                <input type="text" name="txtorderno_<? echo $k; ?>" id="txtorderno_<? echo $k; ?>"  class="text_boxes" style="width:60px" value="<? echo $order_array[$row[csf("order_id")]]['po_number']; ?>" readonly />										
            </td>
            <td>
                <input type="text" name="txtstylename_<? echo $k; ?>" id="txtstylename_<? echo $k; ?>"  class="text_boxes" style="width:70px;" value="<? echo $order_array[$row[csf("order_id")]]['style_ref_no']; ?>" readonly />
            </td>
            <td>
                <input type="text" name="txtpartyname_<? echo $k; ?>" id="txtpartyname_<? echo $k; ?>"  class="text_boxes" style="width:60px" value="<? echo $buyer_arr[$order_array[$row[csf("order_id")]]['buyer_name']]; ?>" readonly />								
            </td>
            <td>			
                <input name="txtnumberroll_<? echo $k; ?>" id="txtnumberroll_<? echo $k; ?>" type="text" class="text_boxes" style="width:40px" value="<? echo $row[csf("no_of_roll")]; ?>" readonly />							
            </td> 
            <td>
                <input type="hidden" name="itemid_<? echo $k; ?>" id="itemid_<? echo $k; ?>" value="<? echo $row[csf("prod_id")]; ?>">
                <input type="text" name="textfebricdesc_<? echo $k; ?>" id="textfebricdesc_<? echo $k; ?>"  class="text_boxes" style="width:100px" title="<? echo $item_id_arr[$row[csf("prod_id")]]; ?>" value="<? echo $item_id_arr[$row[csf("prod_id")]]; ?>" readonly />
            </td>
            <td>
            	<input type="hidden" name="colorid_<? echo $k; ?>" id="colorid_<? echo $k; ?>" value="<? echo $row[csf("color_id")]; ?>">
                <input type="text" name="textcolor_<? echo $k; ?>" id="textcolor_<? echo $k; ?>"  class="text_boxes" style="width:60px" title="<? echo $color_arr[$row[csf("color_id")]]; ?>" value="<? echo $color_arr[$row[csf("color_id")]]; ?>" readonly/>
            </td>
            <td>
            	<input type="hidden" name="bodypartid_<? echo $k; ?>" id="bodypartid_<? echo $k; ?>" value="<? echo $row[csf("body_part_id")]; ?>">
                <input type="text" name="textbodypart_<? echo $k; ?>" id="textbodypart_<? echo $k; ?>"  class="text_boxes" style="width:60px" title="<? echo $body_part[$row[csf("body_part_id")]]; ?>" value="<? echo $body_part[$row[csf("body_part_id")]]; ?>" readonly/>
            </td>
            <td>
            	<input type="hidden" name="textwonumid_<? echo $k; ?>" id="textwonumid_<? echo $k; ?>" value="<? echo $row[csf("wo_num_id")]; ?>">
                <input type="text" name="textwonum_<? echo $k; ?>" id="textwonum_<? echo $k; ?>"  class="text_boxes" style="width:60px" value="<? echo $booking_arr[$row[csf("wo_num_id")]]; ?>" placeholder="Browse" onDblClick="openmypage_wonum();" readonly />
            </td>
            <td>
				<? 
				$rec_bill_qty=0;
				if($row[csf("upd_id")]!="" || $row[csf("upd_id")]!=0)
				{
					$selected_uom=$row[csf("uom")];
					$rec_bill_qty=$row[csf("order_qnty")];
				}
				if($row[csf("upd_id")]=="" || $row[csf("upd_id")]==0)
				{
					$rec_percent=0; $rec_bill_qty=0;
					$selected_uom=12;
					
					if($data[3]==1)
					{
						$rec_percent=$row[csf('order_qnty')]/$row[csf('rec_qty')];
						$rec_bill_qty=($grey_used_arr[$row[csf('dtls_id')]]*$rec_percent);
					}
					else if($data[3]==2) $rec_bill_qty=$row[csf('order_qnty')];
				}
				echo create_drop_down( "cbouom_$k", 55, $unit_of_measurement,"", 0, "--Select UOM--",12,"",1,$selected_uom,"" );?>
            </td>
            <td><input type="text" name="txtfabqnty_<? echo $k; ?>" id="txtfabqnty_<? echo $k; ?>" class="text_boxes_numeric" style="width:50px" value="<? echo $rec_bill_qty; ?>" disabled /></td>
            <td><input type="text" name="txtrate_<? echo $k; ?>" id="txtrate_<? echo $k; ?>" class="text_boxes_numeric" style="width:40px" value="<? echo $row[csf("rate")]; ?>" onBlur="amount_caculation(<? echo $k; ?>);" /></td>
            <td><input type="text" name="txtamount_<? echo $k; ?>" id="txtamount_<? echo $k; ?>" style="width:60px"  class="text_boxes_numeric"  value="<? echo $row[csf("amount")]; ?>" readonly /></td>
            <td><input type="button" name="remarks_<? echo $k; ?>" id="remarks_<? echo $k; ?>"  class="formbuttonplasminus" style="width:25px" value="R" onClick="openmypage_remarks(<? echo $k; ?>);" />
                <input type="hidden" name="txtremarks_<? echo $k; ?>" id="txtremarks_<? echo $k; ?>" class="text_boxes" value="<? echo $row[csf("remarks")]; ?>" />
            </td>
        </tr>
	<?	
	}
	exit();
}*/

if ($action=="wonum_popup")
{
	echo load_html_head_contents("Popup Info", "../../", 1, 1,'',1,'');
	$data=explode('_',$data);
	?>	
    <script>
		  function js_set_value(id)
		  { 
			  document.getElementById('hidd_item_id').value=id;
			  parent.emailwindow.hide();
		  }
	</script>
    </head>
    <body>
        <form name="searchpofrm"  id="searchpofrm">
        <input type="hidden" id="hidd_item_id" />
        <div style="width:100%;">
        <table cellspacing="0" width="100%" class="rpt_table">
            <thead>
                <th width="50">SL</th>
                <th width="150" align="center">Wo No</th>
                <th width="150" align="center">Supplier id </th>
                <th width="100" align="center">Color</th>
                <th width="100" align="center">Construction</th>
                <th width="100" align="center">Copmposition</th>
                <th width="50" align="center">GSM</th>
                <th width="50" align="center">Dia</th>
                <th width="50" align="center">Rate</th>                    
                <th width="" align="center">Uom</th>
            </thead>
        </table>
        </div>
        <div style="width:100%;max-height:180px; overflow:y-scroll" id="sewing_production_list_view" align="left">
        <table cellspacing="0" width="100%" class="rpt_table">
			<?  
                $supplier_library_arr=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name"  );
				$color_library_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id","color_name"  );
			    $i=1;
			    if($data[2]!="")
				{
					$po_cond="and b.po_break_down_id in($data[2])";
				}  else $wo_rec_idcond="";
				 if($data[3]==4) // FSO ////knitting_work_order_mst
				 {
					   $sql="select a.id,a.do_no as booking_no,a.dyeing_compnay_id as supplier_id,b.color_id as gmts_color_id,b.rate,b.fabric_desc as construction,a.currency_id from dyeing_work_order_mst a,dyeing_work_order_dtls b,fabric_sales_order_mst c where a.id=b.mst_id  and a.fabric_sales_order_no=c.job_no   and a.company_id=$data[0] and a.dyeing_compnay_id=$data[1] and c.id=$data[2]";
				}
				else
				{
					$sql="select a.id,a.booking_no,a.supplier_id,b.color_size_table_id,b.rate,b.construction,b.copmposition,b.gsm_weight,b.dia_width,b.uom,b.process,a.currency_id from wo_booking_mst a,wo_booking_dtls b  where a.booking_no=b.booking_no and a.company_id=$data[0] and a.supplier_id=$data[1] and b.process in (31,32,33,34,35,36,37,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,93,94,127,133,134,135,136,137,138,139,140,141,142,143,144,145,146,150,153,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,180,181,182,183,184,185,189,190,192,193,195,196,197,198,199,200,202,205,206,207,209,210,211,212,219,220,221,223,224,225,226,227,229,230,232,234,237,239,240,242,243,244,245,246,247,257,258,259,260,261,262,265,266,267,268,269,270,271,272,273,274,275,276,277,278,287,318,472,478) $po_cond";
				}

				
				//echo $sql;
				$sql_result=sql_select($sql);
				$color_size_table_id_arr=array();
				foreach($sql_result as $row)
                {
                	array_push($color_size_table_id_arr, $row[csf('color_size_table_id')]);
                }
                $color_size_id_cond=where_con_using_array($color_size_table_id_arr,0,"id");
                $color_id_arr=return_library_array( "select id,color_number_id from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 $color_size_id_cond", "id","color_number_id"  );
                foreach($sql_result as $row)
                {
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('booking_no')]."_".$row[csf('rate')]."_".$row[csf('currency_id')]; ?>');" > 
                        <td width="50" align="center"><? echo $i; ?></td>
                        <td width="150" align="center"><p><? echo $row[csf('booking_no')]; ?></p></td>
                        <td width="150" align="center"><? echo $supplier_library_arr[$row[csf('supplier_id')]]; ?></td>
                        <td width="100" align="center"><? echo $color_library_arr[$color_id_arr[$row[csf('color_size_table_id')]]]; ?></td>
                        <td width="100" align="center"><? echo $row[csf('construction')]; ?></td>
                        <td width="100" align="center"><? echo $row[csf('copmposition')]; ?></td>
                        <td width="50" align="center"><? echo $row[csf('gsm_weight')]; ?></td>
                        <td width="50" align="center"><? echo $row[csf('dia_width')]; ?></td>
                        <td width="50" align="center"><? echo $row[csf('rate')]; ?></td>
                        <td width="" align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
					</tr>
					<?
					$i++;
                }
                ?>
            </table>
        </div>
    </form>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
	exit();
}

if ($action=="load_php_data_to_form_wonum")
{
}

if ($action=="outside_bill_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	$ex_data=explode('_',$data);
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('outside_bill_id').value=id;
			parent.emailwindow.hide();
		}
    </script>
  </head>
  <body>
  <div align="center" style="width:100%;" >
  <form name="finishingbill_1"  id="finishingbill_1" autocomplete="off">
	  <table width="650" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
          <thead>                	 
              <th width="150">Company Name</th>
              <th width="150">Supplier Name</th>
              <th width="80">Bill ID</th>
              <th width="170">Date Range</th>
              <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>           
          </thead>
          <tbody>
                <tr>
                <td> <input type="hidden" id="outside_bill_id">  
					<?   
						echo create_drop_down( "cbo_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $ex_data[0],"load_drop_down( 'outside_finishing_bill_entry_controller', this.value, 'load_drop_down_supplier_name_pop', 'supplier_td' );",1 );
                    ?>
                </td>
                <td width="140" id="supplier_td">
					<?
						echo create_drop_down( "cbo_supplier_company", 150, "select sup.id, sup.supplier_name from lib_supplier sup,lib_supplier_tag_company b where sup.status_active=1 and sup.is_deleted=0 and b.supplier_id=sup.id and b.tag_company='$ex_data[0]' $supplier_cond and sup.id in (select  supplier_id from  lib_supplier_party_type where party_type=21) order by supplier_name", "id,supplier_name", 1, "-- Select supplier --", $ex_data[1], "","","","","","",5 );
                    ?> 
                </td>
                <td>
                    <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:75px" />
                </td>
                <td align="center">
                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                </td> 
                <td align="center">
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_supplier_company').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value, 'outside_finishing_bill_list_view', 'search_div', 'outside_finishing_bill_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                </td>
            </tr>
            <tr>
                <td colspan="5" align="center" height="40" valign="middle">
					<? echo load_month_buttons(1);  ?>
                </td>
            </tr>
            <tr>
                <td colspan="5" align="center" valign="top" id=""><div id="search_div"></div></td>
            </tr>
	  </table>    
	  </form>
    </div>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="outside_finishing_bill_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company_name=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $supplier_name=" and supplier_id='$data[1]'"; 
	//if ($data[2]!="" &&  $data[3]!="") $return_date = "and bill_date between '".change_date_format($data[2], "mm-dd-yyyy", "/",1)."' and '".change_date_format($data[3], "mm-dd-yyyy", "/",1)."'"; else $return_date="";
	
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $return_date = "and bill_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $return_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $return_date = "and bill_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $return_date ="";
	}
	if ($data[4]!='') $bill_id_cond=" and prefix_no_num='$data[4]'"; else $bill_id_cond="";
	
	$location=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$supplier_library_arr=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name"  );
	
	$arr=array (2=>$location,4=>$supplier_library_arr,5=>$bill_for,6=>$production_process);
	
	if($db_type==0)
	{
		$year_cond= "year(insert_date)as year";
	}
	else if($db_type==2)
	{
		$year_cond= "TO_CHAR(insert_date,'YYYY') as year";
	}
	
	$sql= "select id, bill_no, prefix_no_num, $year_cond, party_bill_no, bill_date, supplier_id, bill_for from subcon_outbound_bill_mst where process_id=4 and status_active=1 $company_name $party_name $return_date $bill_id_cond order by id Desc";
	
	echo  create_list_view("list_view", "Bill No,Year,Party Bill No,Bill Date,Supplier,Bill For", "70,70,100,100,120,100","600","250",0, $sql , "js_set_value", "id", "", 1, "0,0,0,0,supplier_id,bill_for", $arr , "prefix_no_num,year,party_bill_no,bill_date,supplier_id,bill_for", "outside_finishing_bill_entry_controller","",'0,0,0,3,0,0') ;
	exit();	
}

if ($action=="load_php_data_to_form_outside_bill")
{
	$sql="SELECT min(receive_date) as min_date, max(receive_date) as max_date FROM subcon_outbound_bill_dtls WHERE mst_id='$data' and status_active=1 and is_deleted=0 group by mst_id";
	
	$sql_result_arr =sql_select($sql); 
	$mindate='';  $maxdate='';
	$mindate=$sql_result_arr[0][csf('min_date')];
	$maxdate=$sql_result_arr[0][csf('max_date')];
	
	$nameArray= sql_select("select id, bill_no, company_id, location_id, bill_date, supplier_id, bill_for,upcharge,discount, party_bill_no, is_posted_account from subcon_outbound_bill_mst where id='$data'");
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_bill_no').value 					= '".$row[csf("bill_no")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "load_drop_down( 'requires/outside_finishing_bill_entry_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_location', 'location_td' );\n";
		echo "document.getElementById('cbo_location_name').value			= '".$row[csf("location_id")]."';\n"; 
		echo "document.getElementById('txt_bill_date').value 				= '".change_date_format($row[csf("bill_date")])."';\n";   
		echo "load_drop_down( 'requires/outside_finishing_bill_entry_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_supplier_name_new', 'supplier_td' );\n";
		echo "document.getElementById('cbo_supplier_company').value			= '".$row[csf("supplier_id")]."';\n"; 
		echo "document.getElementById('cbo_bill_for').value					= '".$row[csf("bill_for")]."';\n"; 
		echo "document.getElementById('txt_party_bill').value				= '".$row[csf("party_bill_no")]."';\n"; 
		echo "document.getElementById('txt_bill_form_date').value 			= '".change_date_format($mindate)."';\n";  
		echo "document.getElementById('txt_bill_to_date').value 			= '".change_date_format($maxdate)."';\n"; 
		echo "document.getElementById('hidden_acc_integ').value				= '".$row[csf("is_posted_account")]."';\n";


		if($row[csf("is_posted_account")]==1)
		{
			echo "$('#accounting_integration_div').text('Already Posted In Accounts.');\n"; 
		}
		else 
		{
			echo "$('#accounting_integration_div').text('');\n"; 
		}
		
		echo "disable_enable_fields('cbo_company_id*cbo_location_name*cbo_supplier_company*cbo_bill_for',1);\n";
	    echo "document.getElementById('update_id').value            		= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_upcharge').value				= '".$row[csf("upcharge")]."';\n"; 
		echo "document.getElementById('txt_discount').value				= '".$row[csf("discount")]."';\n";
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$bill_process_id="4";
	
	$bill_for_id=str_replace("'","",$cbo_bill_for);
	$is_sales=0;
	if($bill_for_id==4)
	{
		$is_sales=1;
	}
	if ($operation==0)   // Insert Here========================================================================================delivery_id
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0)
		{
			$year_cond=" and YEAR(insert_date)";	
		}
		else if($db_type==2)
		{
			$year_cond=" and TO_CHAR(insert_date,'YYYY')";	
		}
		
		

		$new_bill_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'FIN', date("Y",time()), 5, "select prefix_no, prefix_no_num from  subcon_outbound_bill_mst where company_id=$cbo_company_id and process_id=$bill_process_id $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
		
		
	//	echo "10**";print_r($new_bill_no);die;
		
	//	print_r($new_bill_no);
		//die;
		if(str_replace("'",'',$update_id)=="")
		{
			$id=return_next_id( "id", "subcon_outbound_bill_mst", 1 ) ; 	
			$field_array="id, prefix_no, prefix_no_num, bill_no, company_id, location_id, bill_date, supplier_id, bill_for,upcharge,discount, party_bill_no, process_id, inserted_by, insert_date";
			$data_array="(".$id.",'".$new_bill_no[1]."','".$new_bill_no[2]."','".$new_bill_no[0]."',".$cbo_company_id.",".$cbo_location_name.",".$txt_bill_date.",".$cbo_supplier_company.",".$cbo_bill_for.",".$txt_upcharge.",".$txt_discount.",".$txt_party_bill.",".$bill_process_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
			//echo "10**INSERT INTO subcon_outbound_bill_mst (".$field_array.") VALUES ".$data_array; die;
			$rID=sql_insert("subcon_outbound_bill_mst",$field_array,$data_array,0);
			$return_no=$new_bill_no[0]; 
		}
		else
		{
			$id=str_replace("'",'',$update_id);
			$field_array="location_id*bill_date*supplier_id*bill_for*upcharge*discount*party_bill_no*updated_by*update_date";
			$data_array="".$cbo_location_name."*".$txt_bill_date."*".$cbo_supplier_company."*".$cbo_bill_for."*".$txt_upcharge."*".$txt_discount."*".$txt_party_bill."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			$rID=sql_update("subcon_outbound_bill_mst",$field_array,$data_array,"id",$update_id,0);
			$return_no=str_replace("'",'',$txt_bill_no);
		}
			
		$id1=return_next_id( "id","subcon_outbound_bill_dtls",1);
		$field_array1 ="id, mst_id, receive_id, receive_date ,challan_no, order_id, item_id, febric_description_id, batch_id, dia_width_type, color_id, body_part_id, roll_no, wo_num_id, receive_qty, uom, rate, amount, remarks, is_sales,process_id, inserted_by, insert_date, currency_id,sub_process_id,source,amount_usd,ex_rate";
		  
		$add_comma=0;
		for($i=1; $i<=$tot_row; $i++)
		{
			$receive_date="txtreceivedate_".$i;
			$challen_no="txtchallenno_".$i;
			$orderid="ordernoid_".$i;
			$style_name="txtstylename_".$i;
			$party_name="txtpartyname_".$i;
			$number_roll="txtnumberroll_".$i;
			$item_id="itemid_".$i;
			$colorid="colorid_".$i;
			$bodypartid="bodypartid_".$i;
			$wo_number="textwonumid_".$i;
			$feb_qnty="txtfabqnty_".$i;
			$cbo_uom="cbouom_".$i;
			$rate="txtrate_".$i;
			$amount="txtamount_".$i;
			$ex_rate="txtexRate_".$i;
			$amount_usd="txtamountusd_".$i;
			
			$remarks="txtremarks_".$i;
			$recive_id="reciveid_".$i;
			$compoid="compoid_".$i;
			$batchid="batchid_".$i;
			$diatype="diatype_".$i;
			$updateid_dtls="updateiddtls_".$i;
			$curanci="curanci_".$i;
			$subprocessId="subprocessId_".$i;
			$serviceSource="serviceSource_".$i;
			
			if(str_replace("'",'',$$updateid_dtls)=="")  
			{
				if ($add_comma!=0) $data_array1 .=",";
				$data_array1 .="(".$id1.",".$id.",".$$recive_id.",".$$receive_date.",".$$challen_no.",".$$orderid.",".$$item_id.",".$$compoid.",".$$batchid.",".$$diatype.",".$$colorid.",".$$bodypartid.",".$$number_roll.",".$$wo_number.",".$$feb_qnty.",".$$cbo_uom.",".$$rate.",".$$amount.",".$$remarks.",".$is_sales.",'".$bill_process_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$curanci.",".$$subprocessId.",".$$serviceSource.",".$$amount_usd.",".$$ex_rate.")";
				$id1=$id1+1;
				$add_comma++;
			}
			else
			{
				$id_arr[]=str_replace("'",'',$$updateid_dtls);
				$data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$delivery_id."*".$$delevery_date."*".$$challen_no."*".$$orderid."*".$$item_id."*".$$colorid."*".$$bodypartid."*".$$number_roll."*".$$quantity."*".$$rate."*".$$amount."*".$$remarks."*".$$curanci."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				$id_arr_delivery[]=str_replace("'",'',$$delivery_id);
				$data_array_delivery[str_replace("'",'',$$delivery_id)] =explode("*",("1"));
			}
		}
		if($data_array1!="")
		{
			//echo "insert into subcon_outbound_bill_dtls (".$field_array1.") values ".$data_array1;die;
			$rID1=sql_insert("subcon_outbound_bill_dtls",$field_array1,$data_array1,1);
		}
		//echo "10**".$rID."**".$rID1;die;
		if($db_type==0)
		{
			if($rID && $rID1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}
		if($db_type==2)
		{
			if($rID && $rID1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}	
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here=============================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	
		$id=str_replace("'",'',$update_id);
		$field_array="bill_date*party_bill_no*upcharge*discount*updated_by*update_date";
		$data_array="".$txt_bill_date."*".$txt_party_bill."*".$txt_upcharge."*".$txt_discount."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$rID=sql_update("subcon_outbound_bill_mst",$field_array,$data_array,"id",$update_id,0);
		$return_no=str_replace("'",'',$txt_bill_no);
		
		$dtls_update_id_array=array();
		$sql_dtls="Select id from subcon_outbound_bill_dtls where mst_id=$update_id and status_active=1 and is_deleted=0";
		$nameArray=sql_select( $sql_dtls );
		foreach($nameArray as $row)
		{
			$dtls_update_id_array[]=$row[csf('id')];
		}
		
		$id1=return_next_id( "id","subcon_outbound_bill_dtls",1);
		$field_array1="id, mst_id, receive_id, receive_date, challan_no, order_id, item_id, febric_description_id, batch_id, dia_width_type, color_id, body_part_id, roll_no, wo_num_id, receive_qty, uom,rate, amount, remarks,is_sales, process_id, inserted_by, insert_date, currency_id,sub_process_id,source,amount_usd,ex_rate";
		$field_array_up ="receive_id*receive_date*challan_no*order_id*item_id*febric_description_id*batch_id*dia_width_type*color_id*body_part_id*roll_no*wo_num_id*receive_qty*uom*rate*amount*remarks*updated_by*update_date*currency_id*sub_process_id*source*amount_usd*ex_rate";
		$add_comma=0;
		for($i=1; $i<=$tot_row; $i++)
	  	{
			$receive_date="txtreceivedate_".$i;
			$challen_no="txtchallenno_".$i;
			$orderid="ordernoid_".$i;
			$style_name="txtstylename_".$i;
			$party_name="txtpartyname_".$i;
			$number_roll="txtnumberroll_".$i;
			$item_id="itemid_".$i;
			$colorid="colorid_".$i;
			$bodypartid="bodypartid_".$i;
			$wo_number="textwonumid_".$i;
			$feb_qnty="txtfabqnty_".$i;
			$cbo_uom="cbouom_".$i;
			$rate="txtrate_".$i;
			$amount="txtamount_".$i;
			$ex_rate="txtexRate_".$i;
			$amount_usd="txtamountusd_".$i;
			$remarks="txtremarks_".$i;
			$recive_id="reciveid_".$i;
			$compoid="compoid_".$i;
			$batchid="batchid_".$i;
			$diatype="diatype_".$i;
			$updateid_dtls="updateiddtls_".$i;
			$curanci="curanci_".$i;
			$subprocessId="subprocessId_".$i;
			$serviceSource="serviceSource_".$i;
			
			if(str_replace("'",'',$$updateid_dtls)=="")  
			{ 
				if ($add_comma!=0) $data_array1 .=",";
				$data_array1 .="(".$id1.",".$id.",".$$recive_id.",".$$receive_date.",".$$challen_no.",".$$orderid.",".$$item_id.",".$$compoid.",".$$batchid.",".$$diatype.",".$$colorid.",".$$bodypartid.",".$$number_roll.",".$$wo_number.",".$$feb_qnty.",".$$cbo_uom.",".$$rate.",".$$amount.",".$$remarks.",".$is_sales.",".$bill_process_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$curanci.",".$$subprocessId.",".$$serviceSource.",".$$amount_usd.",".$$ex_rate.")";
				$id1=$id1+1;
				$add_comma++;
				
				/*$id_arr_deli=implode(',',explode('_',str_replace("'",'',$$delivery_id)));
				$delv_id=explode(',',$id_arr_deli);
				foreach($delv_id as $val)
				{
					$id_arr_delivery[]=$val;
					$data_array_delivery[$val] =explode("*",("1"));
				}*/
			}
			else
			{
				$id_arr[]=str_replace("'",'',$$updateid_dtls);
				$data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$recive_id."*".$$receive_date."*".$$challen_no."*".$$orderid."*".$$item_id."*".$$compoid."*".$$batchid."*".$$diatype."*".$$colorid."*".$$bodypartid."*".$$number_roll."*".$$wo_number."*".$$feb_qnty."*".$$cbo_uom."*".$$rate."*".$$amount."*".$$remarks."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$$curanci."*".$$subprocessId."*".$$serviceSource."*".$$amount_usd."*".$$ex_rate.""));
			}
		}
			  
		$rID1=execute_query(bulk_update_sql_statement("subcon_outbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
		if($data_array1!="")
		{
			//echo "insert into subcon_outbound_bill_dtls (".$field_array1.") values ".$data_array1;
			$rID1=sql_insert("subcon_outbound_bill_dtls",$field_array1,$data_array1,1);
		}
		//var_dump($id_arr); die;
		
		/*if(str_replace("'",'',$delete_id)!="")
		{
			$delete_id=str_replace("'",'',$delete_id);
			$rID3=execute_query( "delete from subcon_outbound_bill_dtls where receive_id in ($delete_id)",0);
			$delete_id=explode(",",str_replace("'",'',$delete_id));
			for ($i=0;$i<count($delete_id);$i++)
			{
				$id_delivery[]=$delete_id[$i];
				$data_delivery[str_replace("'",'',$delete_id[$i])] =explode(",",("0"));
			}
		}*/

		if(!empty($id_arr))
		{
			$delete_arr=array_diff($dtls_update_id_array, $id_arr);
			$delete_id=implode(",", $delete_arr);
			if($delete_id)
			{
				$rID3=execute_query( "update subcon_outbound_bill_dtls set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id in ($delete_id)",1);
			}
		}
		
		
		if($db_type==0)
		{
			if($rID && $rID1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}
		if($db_type==2)
		{
			if($rID && $rID1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}		
		disconnect($con);
		die;
	}
}

if($action=="remarks_popup")
{
	echo load_html_head_contents("Remarks","../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <script>
	function js_set_value(val)
	{
		document.getElementById('text_new_remarks').value=val;
		parent.emailwindow.hide();
	}
	</script>
    </head>
<body>
<div align="center">
	<fieldset style="width:400px;margin-left:4px;">
        <form name="remarksfrm_1"  id="remarksfrm_1" autocomplete="off">
            <table cellpadding="0" cellspacing="0" width="370" >
                <tr>
                    <td align="center"><input type="hidden" name="auto_id" id="auto_id" value="<? echo $data; ?>" />
                      <textarea id="text_new_remarks" name="text_new_remarks" class="text_area" title="Maximum 1000 Character" maxlength="1000" style="width:330px; height:270px" placeholder="Remarks Here. Maximum 1000 Character." ><? echo $data; ?></textarea>
                    </td>
                </tr>
                <tr>
                	<td align="center">
                 <input type="button" id="formbuttonplasminus" align="middle" class="formbutton" style="width:100px" value="Close" onClick="js_set_value(document.getElementById('text_new_remarks').value)" />
                 	</td>
                </tr>
            </table>
        </form>
    </fieldset>
</div>    
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="fabric_finishing_print") 
{
    extract($_REQUEST);
	$data=explode('*', $data);

	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where master_tble_id='".$data[0]."' and form_name='company_details' ",'master_tble_id','image_location');
	//print_r($imge_arr);die;
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$party_library=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name");
	$color_library=return_library_array( "select id,color_name from  lib_color where status_active=1 and is_deleted=0", "id","color_name");
	$color_id_arr=return_library_array( "select id, color_id from subcon_delivery_dtls",'id','color_id');
	$inv_item_arr=return_library_array( "select id,material_description from sub_material_dtls",'id','material_description');
	$prod_item_arr=return_library_array( "select id,fabric_description from subcon_production_dtls",'id','fabric_description');
	$prod_process_arr=return_library_array( "select cons_comp_id, process from subcon_production_dtls",'cons_comp_id','process');
	$prod_dtls_arr=return_library_array( "select id, product_name_details from  product_details_master",'id','product_name_details');
	$location_arr=return_library_array("select id,location_name from lib_location", "id","location_name");
	
	$sql_mst="Select id, prefix_no, prefix_no_num, bill_no, company_id, location_id, bill_date, supplier_id, bill_for,upcharge,discount, party_bill_no, process_id from subcon_outbound_bill_mst where company_id=$data[0] and id='$data[1]' and status_active=1 and is_deleted=0";

	$dataArray=sql_select($sql_mst);
	$supplier_arr=return_library_array("select id,supplier_name from lib_supplier where id=".$dataArray[0][csf('supplier_id')]."", "id","supplier_name");
	//print_r($supplier_arr);die;
	?>
    <div style="width:1130px;" align="center">
    <table width="900" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <td width="100" align="right"> 
            	<img  src='../../<? echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='100%' width='100%' />
            </td>
            <td>
                <table width="800" cellspacing="0" align="center">
                    <tr>
                    	<td align="center" style="font-size:20px"><strong ><? echo $company_library[$data[0]]; ?></strong></td>
                    </tr>
                    <tr>
                        <td align="center"  style="font-size:16px"><strong>Unit : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td  align="center" style="font-size:14px">  
							<?
								$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website, vat_number from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
								foreach ($nameArray as $result)
								{ 
									?>
									<? echo $result[csf('plot_no')]; ?> &nbsp; 
									<? echo $result[csf('level_no')]?> &nbsp; 
									<? echo $result[csf('road_no')]; ?> &nbsp; 
									<? echo $result[csf('block_no')];?> &nbsp; 
									<? echo $result[csf('city')];?> &nbsp; 
									<? echo $result[csf('zip_code')]; ?> &nbsp; 
									<? echo $result[csf('province')];?> &nbsp;
									<? echo $result[csf('contact_no')];?> &nbsp; 
									<? echo $result[csf('email')];?> &nbsp; <br>
									<b>Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
								}
                            ?> 
                        </td>  
                    </tr>
                    <tr>
                    	<td align="center" style="font-size:18px"><strong>Dyeing And Finishing Bill Entry</strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table> 
    <table width="930" cellspacing="0" align="center" border="0">   
    	  <tr><td colspan="6" align="center"><hr></hr></td></tr>
             <tr>
		
                <td width="100"><strong>Supplier :</strong></td><td width="175" align="left"><? echo $supplier_arr[$dataArray[0][csf('supplier_id')]]; ?></td>
                <td width="100"><strong>Location Name: </strong></td><td width="175px" align="left"> <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
                <td width="150"><strong>Source :</strong></td><td>Out-bound Subcontract</td>
            </tr>
             <tr>
             	<td width="100"><strong>Bill No :</strong></td> <td width="175" align="left"><? echo $dataArray[0][csf('bill_no')]; ?></td>
             	<td><strong>Bill Date: </strong></td><td> <? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
                <td><strong>Party Bill No :</strong></td> <td><? echo $dataArray[0][csf('party_bill_no')]; ?></td>
            </tr>
            <tr>
            	<td><strong>Bill For :</strong></td> <td><? echo $bill_for[$dataArray[0][csf('bill_for')]]; ?></td>
            </tr>
        </table>
        <br>
        <?

        $job_order_arr=array();
		$sql_job="Select a.job_no, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0";
		$sql_job_result =sql_select($sql_job);
		foreach($sql_job_result as $row)
		{
			$job_order_arr[$row[csf('id')]]['job']=$row[csf('job_no')];
			$job_order_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$job_order_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$job_order_arr[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		unset($sql_job_result);

		$batch_array=array(); $order_array=array();
		$grey_color_array=array();
		$batch_array=array();
		$grey_sql="Select a.id, a.batch_no, a.extention_no, a.process_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form!=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.extention_no, a.process_id";
		$grey_sql_result =sql_select($grey_sql);
	
	foreach($grey_sql_result as $row)
	{
		$batch_array[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
		$batch_array[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
		$batch_array[$row[csf('id')]]['process_id']=$row[csf('process_id')];
		$batch_array[$row[csf('id')]]['batch_qnty']=$row[csf('batch_qnty')];
	}
	//print_r($batch_array[3619]);die;
	unset($grey_sql_result);
	$determ_arr = return_library_array( "select mst_id, copmposition_id from lib_yarn_count_determina_dtls",'mst_id','copmposition_id');
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
	
		 ?>
			<div style="width:100%;">
			<table align="center" cellspacing="0" width="1250"  border="1" rules="all" class="rpt_table" >
	            <thead bgcolor="#dddddd" align="center" style="font-size:12px"> 
	                <th width="30">SL</th>
	                <th width="60">System ID</th>
	                <th width="60">Challan & <br> Delv. Date</th>
                    <th width="75">Job No</th>
	                <th width="80">Order</th> 
	                <th width="70">Buyer  & <br> Style</th>
	                <th width="150">Fabric Des.</th>
	                <th width="60">D.W Type</th>
	                <th width="60">Color</th>
	                <th width="90">Booking/ Del/Prod No</th>
	                <th width="70">Wo No</th>
	                <th width="100">Process</th>
	                <th width="60">Bill Qty</th>
	                <th width="30">UOM</th>
	                <th width="60">Rate</th>
	                <th width="60">Amount</th>
	                <th width="50">Currency</th>
	                <th>Remarks</th>
	            </thead>
			 <?
	     		$i=1;
				$mst_id=$dataArray[0][csf('id')];
				$sql_result =sql_select("select id, receive_id, receive_date, item_id, roll_no, wo_num_id, receive_qty, uom, rate, amount, remarks, currency_id, process_id, prod_mst_id, order_id, color_id, body_part_id, febric_description_id, batch_id, dia_width_type, challan_no, rec_qty_pcs, sub_process_id, source  from subcon_outbound_bill_dtls where mst_id='$mst_id' and status_active=1 and is_deleted=0 and process_id=4 order by id ASC");
				$recId="";
				$woId="";
				foreach($sql_result as $row)
				{
					if($recId=="") $recId=$row[csf('receive_id')]; else $recId.=",".$row[csf('receive_id')];
					if($woId=="") $woId=$row[csf('wo_num_id')]; else $woId.=",".$row[csf('wo_num_id')];
					//$booking_no=return_field_value("booking_no","wo_booking_mst","id=".$row[csf('wo_num_id')]." and is_deleted=0 and status_active=1"); 
					 
				}

				$booking_no=return_library_array("select id, booking_no from wo_booking_mst where id in ($woId) and is_deleted=0 and status_active=1","id","booking_no");
				
				$sql_rec="SELECT c.id, a.booking_no as bookingno,recv_number_prefix_num
					FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c
					WHERE a.id=b.mst_id and b.id=c.dtls_id and c.trans_type=1 and c.entry_form in (7,37,66,68) and c.trans_id!=0 and a.entry_form in (7,37,66,68) AND a.knitting_source=3
					and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.id in ($recId)
					group by c.id, a.booking_no,recv_number_prefix_num";
				$sql_recData =sql_select($sql_rec); $recdataArr=array();
				foreach($sql_recData as $rrow)
				{
					$recdataArr[$rrow[csf('id')]]['bookno']=$rrow[csf('bookingno')];
					$recdataArr[$rrow[csf('id')]]['prefix_num']=$rrow[csf('recv_number_prefix_num')];
				}
				unset($sql_recData);
				

				foreach($sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
					if($row[csf('source')]==2)
					{
						$product_name=$composition_arr[$row[csf('febric_description_id')]];
					}
					else
					{
						$product_name=$prod_dtls_arr[$row[csf('item_id')]];
					}
				?>

					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:12px"> 
	                    <td><? echo $i; ?></td>
	                    <td><? echo $recdataArr[$row[csf('receive_id')]]['prefix_num']; ?></td>
	                    <td align="center" style="word-break:break-all"><? echo $row[csf('challan_no')].'<br>'.change_date_format($row[csf('receive_date')]); ?></td>
	                	<td style="word-break:break-all"><? echo $job_order_arr[$row[csf('order_id')]]['job']; ?></td>
	                    <td style="word-break:break-all"><? echo $job_order_arr[$row[csf('order_id')]]['po']; ?></td>
	                    <td align="center" style="word-break:break-all"><? echo $buyer_arr[$job_order_arr[$row[csf('order_id')]]['buyer']].'<br>'.$job_order_arr[$row[csf('order_id')]]['style']; ?></td>
	                    <td style="word-break:break-all"><? echo $product_name; ?></td>
	                    <td style="word-break:break-all"><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></td>
	                    <td style="word-break:break-all"><? echo $color_library[$row[csf('color_id')]]; ?></td>
	                    <td style="word-break:break-all"><?=$recdataArr[$row[csf('receive_id')]]['bookno']; ?></td>
	                    <td style="word-break:break-all"><? echo $booking_no[$row[csf("wo_num_id")]]; ?></td>
                        <td style="word-break:break-all"><? echo $conversion_cost_head_array[$row[csf('sub_process_id')]] ?></td>
	                    <td align="right"><? echo number_format($row[csf('receive_qty')],2,'.',','); $tot_delivery_qty+=$row[csf('receive_qty')]; ?>&nbsp;</td>
	                    <td style="word-break:break-all"><? echo $unit_of_measurement[12]; ?></td>
	                    <td align="right"><? echo number_format($row[csf('rate')],2,'.',','); ?>&nbsp;</td>
	                    <td align="right"><? echo number_format($row[csf('amount')],2,'.',',');  $total_amount += $row[csf('amount')]; ?>&nbsp;</td>

	                    <td align="center" style="word-break:break-all"><? echo $currency[$row[csf('currency_id')]]; ?></td>
	                    <td style="word-break:break-all"><? echo $row[csf('remarks')]; ?></td>
	                    <? 
						$carrency_id=$row[csf('currency_id')];
						if($carrency_id==1) $paysa_sent="Paisa"; else if($carrency_id==2) $paysa_sent="CENTS";
					    ?>
	                </tr>
	                <?php
	                $i++;
				}
				?>
	        	<tr style="font-size:12px"> 
	                <td align="right" colspan="12"><strong>Total</strong></td>
	              
	                <td align="right"><strong><? echo number_format($tot_delivery_qty,2,'.',','); ?>&nbsp;</strong></td>
	                <td>&nbsp;</td>
	                <td>&nbsp;</td>
	                <td align="right"><strong><? echo $format_total_amount=number_format($total_amount,2,'.',','); ?>&nbsp;</strong></td>
	                <td>&nbsp;</td>
	                <td>&nbsp;</td>
				</tr>
				<tr style="font-size:12px"> 
                <td align="right" colspan="15"><strong>Upcharge</strong></td>
                <td align="right">
					<?
					echo $dataArray[0][csf('upcharge')]; 
					?>
					&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
			<tr style="font-size:12px"> 
                <td align="right" colspan="15"><strong>Discount</strong></td>
                <td align="right"><? echo $dataArray[0][csf('discount')]; ?>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
			<tr style="font-size:12px"> 
                <td align="right" colspan="15"><strong>Net total</strong></td>
                <td align="right">
					<? 
						$upcharge=$dataArray[0][csf('upcharge')];
						$discount=$dataArray[0][csf('discount')];
						$tot_up=$total_amount+$upcharge;
						$net_total=$tot_up-$discount;
						echo $format_total_amount=number_format($net_total,2,'.',''); 
					?>
				&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
           <tr>
	           <tr>
	               <td colspan="17" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
	           </tr>
	        </table>
		 <? echo signature_table(335, $data[0], "930px"); ?>
   </div>
   </div>
	<?
	exit();
}

if($action=="fabric_dyeing_finishing_print") 
{
    extract($_REQUEST);
	$data=explode('*',$data);

	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where master_tble_id='".$data[0]."' and form_name='company_details' ",'master_tble_id','image_location');
	//print_r($imge_arr);die;
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$party_library=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name");
	$color_library=return_library_array( "select id,color_name from  lib_color where status_active=1 and is_deleted=0", "id","color_name");
	$color_id_arr=return_library_array( "select id, color_id from subcon_delivery_dtls",'id','color_id');
	$inv_item_arr=return_library_array( "select id,material_description from sub_material_dtls",'id','material_description');
	$prod_item_arr=return_library_array( "select id,fabric_description from subcon_production_dtls",'id','fabric_description');
	$prod_process_arr=return_library_array( "select cons_comp_id, process from subcon_production_dtls",'cons_comp_id','process');
	$prod_dtls_arr=return_library_array( "select id, product_name_details from  product_details_master",'id','product_name_details');
	$location_arr=return_library_array("select id,location_name from lib_location", "id","location_name");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
			
	$sql_mst="Select id, prefix_no, prefix_no_num, manual_challan,bill_no, company_id, location_id, bill_date, supplier_id, bill_for,upcharge,discount, party_bill_no, process_id from subcon_outbound_bill_mst where company_id=$data[0] and id='$data[1]' and status_active=1 and is_deleted=0";

	$dataArray=sql_select($sql_mst);
	$supplier_arr=return_library_array("select id,supplier_name from lib_supplier where id=".$dataArray[0][csf('supplier_id')]."", "id","supplier_name");
	//$booking_arr=return_library_array( "select a.id,a.booking_no from wo_booking_mst a,subcon_outbound_bill_dtls b where a.id=b.wo_num_id and b.mst_id='$data[1]' ",'id','booking_no');
	 
	
	//print_r($supplier_arr);die;
	?>
    <div style="width:1130px;" align="center">
    <table width="900" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <td width="100" align="right"> 
            	<img  src='../../<? echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='100%' width='100%' />
            </td>
            <td>
                <table width="800" cellspacing="0" align="center">
                    <tr>
                    	<td align="center" style="font-size:20px"><strong ><? echo $company_library[$data[0]]; ?></strong></td>
                    </tr>
                    <tr>
                        <td align="center"  style="font-size:16px"><strong>Unit : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td  align="center" style="font-size:14px">  
							<?
								$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website, vat_number from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
								foreach ($nameArray as $result)
								{ 
									?>
									<? echo $result[csf('plot_no')]; ?> &nbsp; 
									<? echo $result[csf('level_no')]?> &nbsp; 
									<? echo $result[csf('road_no')]; ?> &nbsp; 
									<? echo $result[csf('block_no')];?> &nbsp; 
									<? echo $result[csf('city')];?> &nbsp; 
									<? echo $result[csf('zip_code')]; ?> &nbsp; 
									<? echo $result[csf('province')];?> &nbsp;
									<? echo $result[csf('contact_no')];?> &nbsp; 
									<? echo $result[csf('email')];?> &nbsp; <br>
									<b>Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
								}
                            ?> 
                        </td>  
                    </tr>
                    <tr>
                    	<td align="center" style="font-size:18px"><strong>Dyeing And Finishing Bill Entry</strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table> 
    <table width="930" cellspacing="0" align="center" border="0">   
    	  <tr><td colspan="6" align="center"><hr></hr></td></tr>
             <tr>
				<td width="100"><strong>Bill No :</strong></td> <td width="175" align="left"><? echo $dataArray[0][csf('bill_no')]; ?></td>
                <td><strong>Bill Date: </strong></td><td> <? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
                <td width="100"><strong>Source :</strong></td><td width="175" align="left"><? echo 'OutBound Subcontact'; ?></td>
                
            </tr> 
             <tr>
             	
                 <td width="100"><strong>Party Name :</strong></td><td width="175" align="left"><? echo $supplier_arr[$dataArray[0][csf('supplier_id')]]; ?></td>
                 <td width="100"><strong>Party Location: </strong></td><td width="175px" align="left"> <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
                 <td><strong>Bill For :</strong></td> <td><? echo $bill_for[$dataArray[0][csf('bill_for')]]; ?></td>
            </tr>
            
        </table>
        <br>
        <?
			 if($db_type==0)
			{
				$year_field="  YEAR(a.insert_date) as year";
			
			}
			else
			{
				$year_field="  to_char(a.insert_date,'YYYY') as year";
			}
        $job_order_arr=array();
		 $sql_job="Select a.job_no, a.buyer_name,$year_field, a.style_ref_no, b.id, b.po_number,b.grouping as ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0";
		$sql_job_result =sql_select($sql_job);
		foreach($sql_job_result as $row)
		{
			$job_order_arr[$row[csf('id')]]['job']=$row[csf('job_no')];
			$job_order_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$job_order_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$job_order_arr[$row[csf('id')]]['po']=$row[csf('po_number')];
			$job_order_arr[$row[csf('id')]]['ref_no']=$row[csf('ref_no')];
			$job_order_arr[$row[csf('id')]]['year']=$row[csf('year')];
		}
		unset($sql_job_result);

		$batch_array=array(); $order_array=array();
		$grey_color_array=array();
		$batch_array=array();
		$grey_sql="Select a.id, a.batch_no, a.extention_no, a.process_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form!=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.extention_no, a.process_id";
		$grey_sql_result =sql_select($grey_sql);
	
	foreach($grey_sql_result as $row)
	{
		$batch_array[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
		$batch_array[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
		$batch_array[$row[csf('id')]]['process_id']=$row[csf('process_id')];
		$batch_array[$row[csf('id')]]['batch_qnty']=$row[csf('batch_qnty')];
	}
	//print_r($batch_array[3619]);die;
	unset($grey_sql_result);
	$determ_arr = return_library_array( "select mst_id, copmposition_id from lib_yarn_count_determina_dtls",'mst_id','copmposition_id');
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
	
		 ?>
			<div style="width:100%;">
			<table align="center" cellspacing="0" width="1090"  border="1" rules="all" class="rpt_table" >
	            <thead bgcolor="#dddddd" align="center" style="font-size:12px"> 
	                <th width="30">SL</th>
	                <th width="60">Challan No.</th>
                    <th width="60">Process</th>
                    <th width="70">Order</th> 
                    <th width="70">Style</th>
                    <th width="70">Buyer</th>
                    <th width="40">No.O.Roll</th>
	                <th width="120">Fabric Des.</th>
                    <th width="100">Color</th>
	                <th width="70">Body Part</th>
	                <th width="70">WO Num</th>
                  	<th width="40">Uom</th> 
                    <th width="70">Fabric Qty</th>
	                <th width="60">Rate</th>
	                <th width="60">Amount</th>
	                <th width="50">Currency</th> 
                    <th width="50">RMK</th>
	            </thead>
			 <?
			
		
	     		$i=1;
				$mst_id=$dataArray[0][csf('id')];
				$sql_result =sql_select("select id, receive_id, receive_date, item_id, roll_no, wo_num_id, receive_qty, uom, rate, amount, remarks, currency_id, process_id, prod_mst_id, order_id, color_id, body_part_id, febric_description_id, batch_id, dia_width_type, challan_no, rec_qty_pcs, sub_process_id, source  from subcon_outbound_bill_dtls where mst_id='$mst_id' and status_active=1 and is_deleted=0 and process_id=4 order by id ASC"); 
				/*echo "select id, receive_id, receive_date, item_id, roll_no, wo_num_id, receive_qty, uom, rate, amount, remarks, currency_id, process_id, prod_mst_id, order_id, color_id, body_part_id, febric_description_id, batch_id, dia_width_type, challan_no, rec_qty_pcs, sub_process_id, source  from subcon_outbound_bill_dtls where mst_id='$mst_id' and status_active=1 and is_deleted=0 and process_id=4 order by id ASC";die;*/
				$tot_req_qty=$tot_req_qty=0;
				foreach($sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
					if($row[csf('source')]==2)
					{
						$product_name=$composition_arr[$row[csf('febric_description_id')]];
					}
					else
					{
						$product_name=$prod_dtls_arr[$row[csf('item_id')]];
					}
					$process_id=array_unique(explode(',',$row[csf('process_id')]));
					$process_name='';
					foreach ($process_id as $val)
					{
						if($process_name=='') $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
					}
					$booking_no=return_field_value("booking_no","wo_booking_mst","id=".$row[csf('wo_num_id')]." and is_deleted=0 and status_active=1"); 
					
				
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:12px"> 
	                    <td><? echo $i; ?></td>
	                    <td align="center" style="word-break:break-all"><? echo $row[csf('challan_no')];//$row[csf('challan_no')].'<br>'.change_date_format($row[csf('receive_date')]); ?></td>
                        <td align="center" style="word-break:break-all"><? echo $process_name; ?></td>
	                    <td style="word-break:break-all"><? echo $job_order_arr[$row[csf('order_id')]]['po']; ?></td>
                         <td style="word-break:break-all"><? echo $job_order_arr[$row[csf('order_id')]]['style'];//echo $product_name; ?></td>
	                    <td align="center" style="word-break:break-all"><? echo $buyer_arr[$job_order_arr[$row[csf('order_id')]]['buyer']]; ?></td>
	                  
                        <td style="word-break:break-all"><? echo $$row[csf('roll_no')]; ?></td>
                      
	                    <td style="word-break:break-all"><? echo $product_name; ?></td>
	                    <td style="word-break:break-all"><? echo $color_library[$row[csf('color_id')]]; ?></td>
	                  
	                  
                         <td align="right"><? echo $body_part[$row[csf('body_part_id')]]; ?>&nbsp;</td>
                        
	                 
                         <td style="word-break:break-all"><? echo $booking_no;  ?></td>
                          <td style="word-break:break-all"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                         <td  align="right" style="word-break:break-all"><? echo number_format($row[csf('receive_qty')],2,'.',','); $tot_prod_qty+=$row[csf('receive_qty')]; ?></td>
                        
                            
	                    <td align="right"><? echo number_format($row[csf('rate')],4,'.',','); ?>&nbsp;</td>
	                    <td align="right"><? echo number_format($row[csf('amount')],4,'.',',');  $total_amount += $row[csf('amount')]; ?>&nbsp;</td>
	                    <td align="center" style="word-break:break-all"><? echo $currency[$row[csf('currency_id')]]; ?></td>
                          <td align="center" style="word-break:break-all"><? echo $row[csf('remarks')]; ?></td>
	                  
	                    <? 
						$carrency_id=$row[csf('currency_id')];
						if($carrency_id==1) $paysa_sent="Paisa"; else if($carrency_id==2) $paysa_sent="CENTS";
					    ?>
	                </tr>
	                <?php
	                $i++;
				}
				?>
	        	<tr style="font-size:12px"> 
	                <td align="right" colspan="12"><strong>Total</strong></td>
	              
	                <td align="right"><strong><? echo number_format($tot_prod_qty,2,'.',','); ?>&nbsp;</strong></td>
                    <td align="right"><strong><? //echo number_format($tot_prod_qty,2,'.',','); ?>&nbsp;</strong></td>
	              
	                
                     <td align="right"><strong><? echo $format_total_amount=number_format($total_amount,4,'.',','); ?>&nbsp;</strong></td>
	                <td>&nbsp;</td>
                     
	                <td>&nbsp;</td>
				</tr>
				<tr style="font-size:12px"> 
                <td align="right" colspan="14"><strong>Upcharge</strong></td>
                <td align="right">
					<?
					echo $dataArray[0][csf('upcharge')]; 
					?>
					&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
			<tr style="font-size:12px"> 
                <td align="right" colspan="14"><strong>Discount</strong></td>
                <td align="right"><? echo $dataArray[0][csf('discount')]; ?>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
			<tr style="font-size:12px"> 
                <td align="right" colspan="14"><strong>Net total</strong></td>
                <td align="right">
					<? 
						$upcharge=$dataArray[0][csf('upcharge')];
						$discount=$dataArray[0][csf('discount')];
						$tot_up=$total_amount+$upcharge;
						$net_total=$tot_up-$discount;
						echo $format_total_amount=number_format($net_total,4,'.',''); 
					?>
				&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
           <tr>
	           <tr>
	               <td colspan="17" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
	           </tr>
	        </table>
		 <?
            echo signature_table(335, $data[0], "930px");
         ?>
   </div>
   </div>
<?
exit();
}

if($action=="file_upload")
{
	header("Content-Type: application/json");
	$filename = time().$_FILES['file']['name']; 
	$location = "../../file_upload/".$filename; 
    //echo "0**".$filename; die;
	$uploadOk = 1;
	if(empty($mst_id))
	{
		$mst_id=$_GET['mst_id'];
	} 
    
	if(move_uploaded_file($_FILES['file']['tmp_name'], $location))
	{ 
		$uploadOk = 1;
	}
	else
	{ 
		$uploadOk=0; 
	} 
    // echo "0**".$uploadOk; die;
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}

	$id=return_next_id( "id","COMMON_PHOTO_LIBRARY", 1 ) ;
	$data_array .="(".$id.",'".$mst_id."','outside_finishing_bill_entry','file_upload/".$filename."','2','".$filename."','".$pc_date_time."')";
	$field_array="id,master_tble_id,form_name,image_location,file_type,real_file_name,insert_date";
	$rID=sql_insert("COMMON_PHOTO_LIBRARY",$field_array,$data_array,1);

	if($db_type==0)
	{
		if($rID==1 && $uploadOk==1)
		{
			mysql_query("COMMIT");
			echo "0**".$new_system_id[0]."**".$mst_id;
		}
		else
		{
			mysql_query("ROLLBACK");
			echo "10**".$mst_id;
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID==1 && $uploadOk==1)
		{
			oci_commit($con);
			echo "0**".$new_system_id[0]."**".$mst_id;
		}
		else
		{
			oci_rollback($con);
			echo "10**".$rID."**".$uploadOk."**INSERT INTO COMMON_PHOTO_LIBRARY(".$field_array.") VALUES ".$data_array;
		}
	}
	disconnect($con);
	die;
}
?>