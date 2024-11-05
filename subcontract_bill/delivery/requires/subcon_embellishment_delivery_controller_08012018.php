<?php
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//************************************ Start *************************************************
$order_num_arr=return_library_array("select id, order_no from subcon_ord_dtls", "id", "order_no");
$location_arr=return_library_array("select id, location_name from  lib_location", "id", "location_name");

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 152, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );		 
}
/*
if ($action=="load_drop_down_party_name")
{
    echo create_drop_down( "cbo_party_name", 152, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and b.tag_company='$data' and buy.id in (select buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "",'' ); 
	exit();	
}
*/

if ($action == "party_name") 
{
	$sql = "select buy.id,buy.buyer_name label from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and b.tag_company='$data' and buy.id in (select buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name";
	$result = sql_select($sql);
	$partyArr = array();
	foreach($result as $key => $val){
		$partyArr[$key]["id"]=$val[csf("id")];
		$partyArr[$key]["label"]=$val[csf("label")];
	}
	echo json_encode($partyArr);
	//echo strtolower(json_encode($result));
	//echo "[" . substr(return_library_autocomplete($sql, "party_name"), 0, -1) . "]";
    exit();
	
}

if ($action == "transport_company") 
{
	$sql = "select transport_company from subcon_delivery_mst where status_active=1 and is_deleted=0  and transport_company is not null group by transport_company order by transport_company";
	//and company_id ='$data'
	$result = sql_select($sql);
	echo "[" . substr(return_library_autocomplete($sql, "transport_company"), 0, -1) . "]";
    exit();
}

if ($action=="order_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1,'');
 	//$ex_data = explode("_",$data);
	$company_id	= $company;
	$party_id	= $cbo_party_name;
	$hiddenBatchAgainst = $Batch_Against;
	$orderID = $txtPoId;
	//echo $company_id."**".$party_id;die;
	?>
	<script>
		$(document).ready(function(e) {
            $("#txt_search_order").focus();
        });
	
		function js_set_value(id)
		{ 
			//alert(id); return;
			var response=id.split("_");
			$("#hidden_order_id").val(response[0]);
			//$("#hidden_item_id").val(response[1]);
			$("#BatchAgainst").val(response[1]);
			parent.emailwindow.hide();
		}
		
    </script>
    </head>
    <body>
        <div align="center" style="width:100%;" >
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead> 
                        <!--<tr>
                            <th colspan="5" align="center">
								<?php //echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?>
                            </th>
                        </tr>-->
                    	<tr>               	 
                            <!--<th width="100">Job No</th>
                            <th width="100">Style No</th>-->
                            <th width="100">Order No</th>
                            <th width="170">Date Range</th>
                            <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="ganeral">
                            <!--<td  align="center">  
                                <input type="text" style="width:100px" class="text_boxes"  name="txt_search_job" id="txt_search_job" placeholder="Search Job"/>			
                            </td>
                            <td  align="center">  
                                <input type="text" style="width:100px" class="text_boxes"  name="txt_search_style" id="txt_search_style" placeholder="Search Style" />			
                            </td>-->
                            <td align="center">				
                                <input type="text" style="width:100px" class="text_boxes"  name="txt_search_order" id="txt_search_order" placeholder="Search Order" />			
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"> To
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td align="center">
                                <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_order').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<?php echo $company_id; ?>+'_'+'<?php echo $party_id; ?>'+'_'+'<?php echo $hiddenBatchAgainst; ?>'+'_'+'<?php echo $orderID; ?>', 'create_order_search_list_view', 'search_div', 'subcon_embellishment_delivery_controller', 'setFilterGrid(\'tbl_order_list\',-1)')" style="width:80px;" />
                                
                                <!--document.getElementById('txt_search_job').value+'_'+document.getElementById('txt_search_style').value+'_'++'_'+document.getElementById('cbo_string_search_type').value-->
                                
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" align="center" height="40" valign="middle">
                                <?php echo load_month_buttons(0);  ?>
                                <input type="hidden" id="hidden_order_id">
                                <input type="hidden" id="hidden_item_id">
                                <input type="hidden" id="BatchAgainst">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" align="center" valign="top" id=""><div id="search_div"></div></td>
                        </tr>
                    </tbody>
                </table>    
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?php
	exit();
}

if($action=="create_order_search_list_view")
{
	//echo $data; die;
	$ex_data = explode("_",$data);
	
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$party_id_by_Order=return_library_array( " select b.id, a.party_id from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job =b.job_no_mst and a.is_deleted=0 and a.status_active=1",'id','party_id');
	$po_style_arr=return_library_array( " select b.id, b.cust_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job =b.job_no_mst and a.is_deleted=0 and a.status_active=1",'id','cust_style_ref');
	
	
	$search_order = $ex_data[0];
	$date_from = $ex_data[1];
	$date_to = $ex_data[2];
	$company = $ex_data[3];
	$party = $ex_data[4];
	$poID = $ex_data[6];
	
	if($ex_data[5] != "")
	{
		$BatchAgainstCond = " and b.batch_against = '$ex_data[5]'";
	}else{
		$BatchAgainstCond = "";
	}
	
	if($poID != "")
	{
		$poIDCond = " and b.po_id not in($ex_data[6])";
	}else{
		$poIDCond = "";
	}
	
	
	
	if(	$party!=0) $party_cond	= " and b.party_id='$party'"; else $party_cond="";
	if ($search_order!='') $order_cond	= " and b.po_no like '%$search_order%'"; else $order_cond="";
	if($db_type==0)
	{ 
		if ($date_from!="" &&  $date_to!="") $date_cond= "and a.product_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; else $date_cond= "";
		//$year_cond= "year(b.insert_date)as year";

	}
	else if ($db_type==2)
	{
		if ($date_from!="" &&  $date_to!="") $date_cond= "and a.product_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";  else $date_cond= "";
		//$year_cond= "TO_CHAR(b.insert_date,'YYYY') as year";
	}
	
	
	echo $sql ="select a.product_date,b.batch_against, b.po_no, b.po_id from subcon_embel_production_mst  a, subcon_embel_production_dtls b where a.id=b.mst_id and a.company_id='$company' $party_cond $order_cond $date_cond $BatchAgainstCond $poIDCond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.product_date,b.batch_against, b.po_no, b.po_id order by b.po_id";
	
	$arr=array (2=>$po_style_arr,3=>$batch_against);
	echo  create_list_view("tbl_order_list", "Embl Prod Date,Order No,Style,Embl Type", "70,100,100,100","750","250",0, $sql , "js_set_value", "po_id,batch_against", "", 1, "0,0,po_id,batch_against", $arr , "product_date,po_no,po_id,batch_against", "requires/subcon_embellishment_delivery_controller",'','3,0,0,0') ;
	exit();
}


if( $action == 'populate_order_data') 
{
	//$data=order_id+"**"+company+"**"+batch_against+"**"+tot_row;
	$data=explode('**',$data);
	
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$party_id_by_Order=return_library_array( " select b.id, a.party_id from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job =b.job_no_mst and a.is_deleted=0 and a.status_active=1",'id','party_id');
	$po_style_arr=return_library_array( " select b.id, b.cust_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job =b.job_no_mst and a.is_deleted=0 and a.status_active=1",'id','cust_style_ref');
	//$QcPassQtyArr=return_library_array( " select b.id, sum(b.qcpass_qty) as qcpass_qty from subcon_embel_production_mst  a, subcon_embel_production_dtls b where a.id=b.mst_id and a.company_id='$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id",'id','qcpass_qty');
	$custBuyerArr=return_library_array( " select id as po_id, cust_buyer from subcon_ord_dtls where status_active = 1 and  is_deleted=0 ",'po_id','cust_buyer');
	$OperatorNameArr=return_library_array( " select emp_code, (first_name|| ' ' ||middle_name|| ' ' ||last_name ) as employeeName  from lib_employee where status_active=1 and  is_deleted =0 and company_id='$data[1]'",'emp_code','employeeName');
	
	$QcPassQtyArr=return_library_array( " select b.id, sum(b.qcpass_qty) as qcpass_qty from subcon_embel_production_mst  a, subcon_embel_production_dtls b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id",'id','qcpass_qty');
	//and a.company_id='$data[1]'
	$totDeliveryArr=return_library_array( " select b.batch_id,  sum(b.delivery_qty) as totDelivery   from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id  and a.entry_form='4' and a.status_active=1 and a.is_deleted=0 group by b.batch_id ",'batch_id','totDelivery');
	$totBatchDeliveryArr=return_library_array( " select b.batch_id,  sum(b.delivery_qty) as batchTot   from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id  and a.entry_form='4' and a.status_active=1 and a.is_deleted=0 group by b.batch_id ",'batch_id','batchTot');
	
	
	
	
	$po_id			= $data[0];
	$batchAgainst	= $data[2];
	
	if($data[3]==0){
		$tblRow = 1;
	}else{
		$tblRow += $data[3]+1;
	}
	
	//$batch_for=$data[1];
	
	
	//$i=$data[2];
	/*
	if($db_type==0)  
    {
       $gmts_item_id_cond = "group_concat(c.item_id) as gmts_item_id";
    }
    else
    {
       $gmts_item_id_cond = "listagg(cast(c.item_id as varchar2(4000)),',') within group (order by c.item_id) as gmts_item_id";
    }

	$po_data_array=sql_select( "select $gmts_item_id_cond, b.id, b.order_no as po_number from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and b.id=c.order_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id, b.order_no");
	$po_array=array(); 
	$po_item_array=array();
	foreach($po_data_array as $poRow)
	{
		$po_array[$poRow[csf('id')]]=$poRow[csf('po_number')];
		$po_item_array[$poRow[csf('id')]]=$poRow[csf('gmts_item_id')];
	}

	$po_item_color_qty=array();
	$item_wise_order_qty_array=sql_select("select b.order_uom,c.order_id, c.item_id, c.color_id, sum(c.qnty) as po_qnty from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.id=c.mst_id and b.id=c.order_id and a.subcon_job=b.job_no_mst group by c.order_id, c.color_id, c.item_id,b.order_uom");
	foreach ($item_wise_order_qty_array as $val) 
    {
    	$po_item_color_qty[$val[csf("order_id")]][$val[csf("item_id")]][$val[csf("color_id")]]["qty"]=$val[csf("po_qnty")];
    	$po_item_color_qty[$val[csf("order_id")]][$val[csf("item_id")]][$val[csf("color_id")]]["uom"]=$val[csf("order_uom")];
    }

	$batch_qty_arr=array();
    $batch_dtls_sql="select a.color_id, b.po_id, b.prod_id, sum(b.roll_no) as roll_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_id, b.prod_id,a.color_id";
    $batchArray = sql_select($batch_dtls_sql);
    foreach ($batchArray as $value) 
    {
    	$batch_qty_arr[$value[csf("po_id")]][$value[csf("prod_id")]][$value[csf("color_id")]]=$value[csf("roll_no")];
    }
	
	
	
	
	//$data_array=sql_select("select a.id as batch_id, a.color_id, a.batch_no, a.entry_form, a.batch_date, a. batch_against, a.batch_for, a.company_id, a.booking_no_id, a.booking_no, a.booking_without_order, a.extention_no, a.color_id, a.batch_weight, a.color_range_id, a.process_id, a.shift_id, b.id, b.prod_id, b.batch_qnty, b.po_id, b.roll_no, b.batch_qnty, b.program_no, b.po_batch_no, b.dtls_id, b.fabric_from, b.barcode_no, b.roll_id, b.body_part_id, b.gsm, b.grey_dia  from pro_batch_create_mst a, pro_batch_create_dtls b  where b.mst_id in($batch_id) and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 order by b.id DESC"); 
	
	
	
	$data_query="select a.id as batch_id, a.color_id, a.batch_no, a.entry_form, a.batch_date, a. batch_against, a.batch_for, a.company_id, a.booking_no_id, a.booking_no, a.booking_without_order, a.extention_no, a.color_id, a.batch_weight, a.color_range_id, a.process_id, a.shift_id, b.id, b.prod_id, b.batch_qnty, b.po_id, b.roll_no, b.batch_qnty, b.program_no, b.po_batch_no, b.dtls_id, b.fabric_from, b.barcode_no, b.roll_id, b.body_part_id, b.gsm, b.grey_dia  
	from pro_batch_create_mst a, pro_batch_create_dtls b  where b.mst_id in($batch_id) and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0
	and  b.id not in (select c.batch_dtls_id from subcon_embel_production_dtls c where b.id=c.batch_dtls_id and c.batch_id=a.id and c.status_active=1 and c.is_deleted=0 ) 
	order by b.id DESC"; 
	*/
	
	if($db_type==0)  
    {
       $gmts_item_id_cond = "group_concat(c.item_id) as gmts_item_id";
    }
    else
    {
       $gmts_item_id_cond = "listagg(cast(c.item_id as varchar2(4000)),',') within group (order by c.item_id) as gmts_item_id";
    }
	
	$po_data_array = sql_select( "select $gmts_item_id_cond, b.id, b.order_no as po_number from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and b.id=c.order_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id, b.order_no");
	$po_item_array = array();
	foreach($po_data_array as $poRow)
	{
		$po_item_array[$poRow[csf('id')]] = $poRow[csf('gmts_item_id')];
	}
	
	
	$data_query = "select a.product_date, a.prod_source, b.id, b.mst_id, b.batch_id, b.batch_no, b.batch_against, b.color_id, b.po_no, b.po_id, b.party_id, b.prod_id, b.process_id, b.batch_qty, b.reje_qty, b.repro_qty, b.qcpass_qty, b.operator_name, b.operator_id, b.shift_id, b.batch_dtls_id from subcon_embel_production_mst  a, subcon_embel_production_dtls b where a.id=b.mst_id and a.company_id='$data[1]' and b.po_id='$po_id' and b.batch_against='$batchAgainst' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; 	
	
	//echo $data_query; //die;
	$data_array = sql_select($data_query);
	$tblRow += count($data_array);
	
	foreach($data_array as $row)
	{
		$tblRow-- ;
		$gmts_item_array=array();
		$item_array=explode(",",$po_item_array[$row[csf('po_id')]]);
		foreach($item_array as $item)
		{
			$gmts_item_array[$item]=$garments_item[$item];
		}
		
		
		$QcPassQty 		= $QcPassQtyArr[$row[csf('id')]]*1;
		
		$totDelivery 	= $totDeliveryArr[$row[csf('batch_id')]]*1;
		$currDelivery 	= $row[csf('delivery_qty')]*1;
		
		$prevDelivery	= ($totDelivery - $currDelivery);
		$balance	 	= $QcPassQty - $prevDelivery;
		
				
		
		?>
        <tr class="general" name="tr[]" id="tr_<? echo $tblRow; ?>">
        	<td>
               <input type="text" name="txtSl[]"  id="txtSl_<? echo $tblRow; ?>" value="<? echo $tblRow; ?>" class="text_boxes_numeric" style="text-align:center; width:30px" disabled />
                <input type="hidden" name="emblProdDtlsId[]"  id="emblProdDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('id')]; ?>" class="text_boxes_numeric" style="width:50px" />
				<input type="hidden" name="txtOrderId[]"  id="txtOrderId_<? echo $tblRow; ?>" value="<? echo $row[csf('po_id')]; ?>" class="" style="width:50px" />
               <input type="hidden" name="updateIdDtls[]"  id="updateIdDtls_<? echo $tblRow; ?>" value="" class="" style="width:50px" />
            </td>
            <td>
                <input type="text" name="EmblType[]"  id="EmblType_<? echo $tblRow; ?>" value="<? echo $batch_against[$row[csf('batch_against')]]; ?>" class="text_boxes" style="width:80px" placeholder="Display"disabled />
                <input type="hidden" name="txtEmblTypeId[]"  id="txtEmblTypeId_<? echo $tblRow; ?>" value="<? echo $row[csf('batch_against')]; ?>" class="text_boxes_numeric" style="width:50px" disabled />
            </td>
            <td>
                 <input type="text" name="txtBatchNo[]"  id="txtBatchNo_<? echo $tblRow; ?>"  value="<? echo $row[csf('batch_no')]; ?>" class="text_boxes_numeric" style="width:100px" placeholder="Display"disabled />
                 <input type="hidden" name="hiddenBatchId[]"  id="hiddenBatchId_<? echo $tblRow; ?>" value="<? echo $row[csf('batch_id')]; ?>" class="text_boxes_numeric" style="width:50px" placeholder="Display"disabled />
            </td>
            <td>
               <input type="text" name="txtBatchcolor[]"  id="txtBatchcolor_<? echo $tblRow; ?>" value="<? echo $color_arr[$row[csf('color_id')]]; ?>" class="text_boxes" style="width:80px" placeholder="Display"disabled />
                <input type="hidden" name="txtBatchColorId[]"  id="txtBatchColorId_<? echo $tblRow; ?>" value="<? echo $row[csf('color_id')]; ?>" style="width:30px" disabled />
            </td>
            <td id="">
                <input type="text" name="cboGmtsItem[]"  id="cboGmtsItem_<? echo $tblRow; ?>" value="<? echo $gmts_item_array[$row[csf('prod_id')]]; ?>" class="text_boxes"  style="width:80px" readonly disabled />
                <input type="hidden" name="txtGmtsItem[]"  id="txtGmtsItem_<? echo $tblRow; ?>" value="<? echo $row[csf('prod_id')]; ?>" class="text_boxes"  style="width:50px" />
            </td>
            <td>
                 <input type="text" name="txtProcessName[]" id="txtProcessName_<? echo $tblRow; ?>" class="text_boxes" style="width:80px;"  tabindex="12"   placeholder="Dbl.Click" readonly onDblClick="process_popup('<? echo $row[csf('process_id')]; ?>');" title="Bbl. Click" />
                <input type="hidden" name="txtProcessId[]" id="txtProcessId_<? echo $tblRow; ?>" value="<? echo $row[csf('process_id')]; ?>" />
            </td>
            
            <td>
               <input type="text" name="txtQcPassQty[]"  id="txtQcPassQty_<? echo $tblRow; ?>" value="<? echo $QcPassQty; ?>" class="text_boxes_numeric"  style="width:50px" readonly disabled />
            </td>
            <td>
                <input type="text" name="txtPreDelivQty[]"  id="txtPreDelivQty_<? echo $tblRow; ?>" value="<? echo $prevDelivery; ?>" class="text_boxes_numeric" style="width:50px"   placeholder="Display"  />
            </td>
            <td>
                <input type="text" name="txtBalance[]"  id="txtBalance_<? echo $tblRow; ?>" value="<? echo $balance; ?>" class="text_boxes_numeric"  style="width:50px" placeholder="Display" readonly />
            </td>
            <td>
                <input type="text" name="txtCurrentDelivery[]"  id="txtCurrentDelivery_<? echo $tblRow; ?>" value="<? //echo $row[csf('qcpass_qty')]; ?>"  onKeyUp="calculate_amount(<? echo $tblRow; ?>);" class="text_boxes_numeric"    placeholder="Write"  style="width:50px" />
                <input type="hidden" name="hiddenCurrentDelivery[]"  id="hiddenCurrentDelivery_<? echo $tblRow; ?>" value="<? //echo $row[csf('qcpass_qty')]; ?>" class="text_boxes_numeric"   placeholder="Write"  style="width:50px" />
            </td>
            <td>
                <input type="text" name="txtCustBuyer[]"  id="txtCustBuyer_1" value="<? echo $custBuyerArr[$row[csf('po_id')]]; ?>" class="text_boxes" style="width:80px" placeholder="Display" disabled  />
                
            </td>
            <td>
                <input type="text" name="txtStyle[]"  id="txtStyle_<? echo $tblRow; ?>" value="<? echo $po_style_arr[$row[csf('po_id')]]; ?>" class="text_boxes" style="width:80px"   placeholder="Display" disabled  />
            </td>
            <td>
            <input type="text" name="txtOperatorName[]" id="txtOperatorName_<? echo $tblRow; ?>" value="<? echo $OperatorNameArr[$row[csf('operator_id')]]; ?>" class="text_boxes" style="width:100px;"   placeholder="Display"  disabled  tabindex="4" />
            <input type="hidden" name="txtOperatorId[]" id="txtOperatorId_<? echo $tblRow; ?>" value="<? echo $row[csf('operator_id')]; ?>" style="width:60px"/>
            </td>
            <td>
            <?
                echo create_drop_down( "cboShift_1", 80, $shift_name,"",1, '- Select -',$row[csf('shift_id')],"",'1','','','','','','','cboShift[]');
            ?>
            </td>
        </tr>
		<?
	}
	exit();
}


if( $action == 'populate_delivery_dtls_data') 
{
	
	$data=explode('**',$data);
	
	$MsstDtlsID=explode('_',$data[0]);
	$mstID = $MsstDtlsID[0];
	$dtlsID = $MsstDtlsID[1];
	
	//$mstDtlsIdCond = "and a.id='$mstID' and b.id ='$dtlsID'";
	$mstDtlsIdCond = "and a.id='$mstID'";
	
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$batch_no_arr=return_library_array( "select batch_id, batch_no    from subcon_embel_production_dtls where status_active=1 and is_deleted =0",'batch_id','batch_no');
	$party_id_by_Order=return_library_array( " select b.id, a.party_id from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job =b.job_no_mst and a.is_deleted=0 and a.status_active=1",'id','party_id');
	$po_style_arr=return_library_array( " select b.id, b.cust_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job =b.job_no_mst and a.is_deleted=0 and a.status_active=1",'id','cust_style_ref');
	
	$custBuyerArr=return_library_array( " select id as po_id, cust_buyer from subcon_ord_dtls where status_active = 1 and  is_deleted=0 ",'po_id','cust_buyer');
	$OperatorNameArr=return_library_array( " select emp_code, (first_name|| ' ' ||middle_name|| ' ' ||last_name ) as employeeName  from lib_employee where status_active=1 and  is_deleted =0 and company_id='$data[1]'",'emp_code','employeeName');
	
		$QcPassQtyArr=return_library_array( " select b.id, sum(b.qcpass_qty) as qcpass_qty from subcon_embel_production_mst  a, subcon_embel_production_dtls b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id",'id','qcpass_qty');
	//and a.company_id='$data[1]'
		$totDeliveryArr=return_library_array( " select b.batch_id,  sum(b.delivery_qty) as totDelivery   from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id  and a.entry_form='4' and a.status_active=1 and a.is_deleted=0 group by b.batch_id ",'batch_id','totDelivery');
		$totBatchDeliveryArr=return_library_array( " select b.batch_id,  sum(b.delivery_qty) as batchTot   from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id  and a.entry_form='4' and a.status_active=1 and a.is_deleted=0 group by b.batch_id ",'batch_id','batchTot');
	
	//$embl_deliv_mst_id=$mstID;
	//echo "Reaz*********************************************".$po_id; die;
	if($data[3]==0){
		$tblRow = 1;
	}else{
		$tblRow += $data[3]+1;
	}
	$batchAgainst=$data[2];
	//$batch_for=$data[1];
	
	
	//$i=$data[2];
	/*
	if($db_type==0)  
    {
       $gmts_item_id_cond = "group_concat(c.item_id) as gmts_item_id";
    }
    else
    {
       $gmts_item_id_cond = "listagg(cast(c.item_id as varchar2(4000)),',') within group (order by c.item_id) as gmts_item_id";
    }

	$po_data_array=sql_select( "select $gmts_item_id_cond, b.id, b.order_no as po_number from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and b.id=c.order_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id, b.order_no");
	$po_array=array(); 
	$po_item_array=array();
	foreach($po_data_array as $poRow)
	{
		$po_array[$poRow[csf('id')]]=$poRow[csf('po_number')];
		$po_item_array[$poRow[csf('id')]]=$poRow[csf('gmts_item_id')];
	}

	$po_item_color_qty=array();
	$item_wise_order_qty_array=sql_select("select b.order_uom,c.order_id, c.item_id, c.color_id, sum(c.qnty) as po_qnty from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.id=c.mst_id and b.id=c.order_id and a.subcon_job=b.job_no_mst group by c.order_id, c.color_id, c.item_id,b.order_uom");
	foreach ($item_wise_order_qty_array as $val) 
    {
    	$po_item_color_qty[$val[csf("order_id")]][$val[csf("item_id")]][$val[csf("color_id")]]["qty"]=$val[csf("po_qnty")];
    	$po_item_color_qty[$val[csf("order_id")]][$val[csf("item_id")]][$val[csf("color_id")]]["uom"]=$val[csf("order_uom")];
    }

	$batch_qty_arr=array();
    $batch_dtls_sql="select a.color_id, b.po_id, b.prod_id, sum(b.roll_no) as roll_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_id, b.prod_id,a.color_id";
    $batchArray = sql_select($batch_dtls_sql);
    foreach ($batchArray as $value) 
    {
    	$batch_qty_arr[$value[csf("po_id")]][$value[csf("prod_id")]][$value[csf("color_id")]]=$value[csf("roll_no")];
    }
	
	
	
	
	//$data_array=sql_select("select a.id as batch_id, a.color_id, a.batch_no, a.entry_form, a.batch_date, a. batch_against, a.batch_for, a.company_id, a.booking_no_id, a.booking_no, a.booking_without_order, a.extention_no, a.color_id, a.batch_weight, a.color_range_id, a.process_id, a.shift_id, b.id, b.prod_id, b.batch_qnty, b.po_id, b.roll_no, b.batch_qnty, b.program_no, b.po_batch_no, b.dtls_id, b.fabric_from, b.barcode_no, b.roll_id, b.body_part_id, b.gsm, b.grey_dia  from pro_batch_create_mst a, pro_batch_create_dtls b  where b.mst_id in($batch_id) and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 order by b.id DESC"); 
	
	
	
	$data_query="select a.id as batch_id, a.color_id, a.batch_no, a.entry_form, a.batch_date, a. batch_against, a.batch_for, a.company_id, a.booking_no_id, a.booking_no, a.booking_without_order, a.extention_no, a.color_id, a.batch_weight, a.color_range_id, a.process_id, a.shift_id, b.id, b.prod_id, b.batch_qnty, b.po_id, b.roll_no, b.batch_qnty, b.program_no, b.po_batch_no, b.dtls_id, b.fabric_from, b.barcode_no, b.roll_id, b.body_part_id, b.gsm, b.grey_dia  
	from pro_batch_create_mst a, pro_batch_create_dtls b  where b.mst_id in($batch_id) and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0
	and  b.id not in (select c.batch_dtls_id from subcon_embel_production_dtls c where b.id=c.batch_dtls_id and c.batch_id=a.id and c.status_active=1 and c.is_deleted=0 ) 
	order by b.id DESC"; 
	*/
	//echo "select a.id,a.location_id, a.delivery_date,a.challan_no, a.transport_company,b.id as dtlsId, b.mst_id, b.delivery_qty, b.carton_roll, b.remarks, b.bill_status, b.order_id, b.process_id, b.gsm, b.color_id, b.width_dia_type, b.sub_process_id, b.batch_id, b.item_id, b.total_carton_qnty, b.entry_break_down_type, b.yarn_lot, b.collar_cuff, b.gray_qty, b.delivery_pcs, b.dia, b.prod_id, b.batch_against, b.operator_id, b.embl_prod_dtls_id   from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.id='$mstID' and a.status_active=1 and a.is_deleted=0 order by a.id";
	
	$data_query ="select a.id,a.location_id, a.delivery_date,a.challan_no, a.transport_company,b.id as dtlsId, b.mst_id, b.delivery_qty, b.carton_roll, b.remarks, b.bill_status, b.order_id, b.process_id, b.gsm, b.color_id, b.width_dia_type, b.sub_process_id, b.batch_id, b.item_id, b.total_carton_qnty, b.entry_break_down_type, b.yarn_lot, b.collar_cuff, b.gray_qty, b.delivery_pcs, b.dia, b.prod_id, b.batch_against, b.operator_id, b.embl_prod_dtls_id, b.shift_id   from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id $mstDtlsIdCond and a.status_active=1 and a.is_deleted=0 order by a.id DESC";
	
	//$PreviousQty=return_library_array( "select b.order_id, sum(b.delivery_qty) as previousQty from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and b.mst_id not in( select mst_id from subcon_delivery_dtls where mst_id='$mstID') and a.status_active=1 and a.is_deleted=0 group by b.order_id",'id','previousQty');
	
	//echo "select b.order_id, sum(b.delivery_qty) as previousQty from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and b.mst_id not in( select mst_id from subcon_delivery_dtls where mst_id='$mstID') and a.status_active=1 and a.is_deleted=0 group by b.order_id";
	
	//echo $data_query; //die;

	
	
	
	$data_array=sql_select($data_query);
	$sumQcPass="";
	$sumPrevous="";
	$sumBlance="";
	$sumCurrentDelv="";
	$tblRow += count($data_array);
	
	foreach($data_array as $row)
	{
		$tblRow-- ;
		
		
		$sumQcPass 	+=$QcPassQtyArr[$row[csf('embl_prod_dtls_id')]];
		$sumPrevous +="";
		$sumBlance 	+="";
		$sumCurrentDelv +=$row[csf('delivery_qty')];
		
		//26**21**5**21
		//$prevDeliveryArr = $prevDeliveryArr[$row[csf('batch_id')]];
		$QcPassQty 	= $QcPassQtyArr[$row[csf('embl_prod_dtls_id')]]*1;
		$totDelivery 	= $totDeliveryArr[$row[csf('batch_id')]]*1;
		$currDelivery 	= $row[csf('delivery_qty')]*1;
		$prevDelivery	= ($totDelivery - $currDelivery);
		$balance	 	= $QcPassQty - $prevDelivery;
		//echo "$totDelivery**$currDelivery**$prevDelivery**$balance"; 
		//$need_multiply=($po_item_color_qty[$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("color_id")]]["uom"]==2)?12:1;

		//$chkgmtsqty=($po_item_color_qty[$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("color_id")]]["qty"]*$need_multiply-$batch_qty_arr[$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("color_id")]])+$row[csf('roll_no')];
		?>
        <tr class="general" name="tr[]" id="tr_<? echo $tblRow; ?>">
        	<td>
               <input type="text" name="txtSl[]"  id="txtSl_<? echo $tblRow; ?>" value="<? echo $tblRow; ?>" class="text_boxes_numeric" style="text-align:center; width:30px" disabled />
                <input type="hidden" name="emblProdDtlsId[]"  id="emblProdDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('embl_prod_dtls_id')]; ?>" class="text_boxes_numeric" style="width:50px" />
               <input type="hidden" name="txtOrderId[]"  id="txtOrderId_<? echo $tblRow; ?>" value="<? echo $row[csf('order_id')]; ?>" class="" style="width:50px" />
                <input type="hidden" name="updateIdDtls[]"  id="updateIdDtls_<? echo $tblRow; ?>" value="<? echo $row[csf('dtlsId')]; ?>" class="" style="width:50px" />
            </td>
            <td>
                <input type="text" name="EmblType[]"  id="EmblType_<? echo $tblRow; ?>" value="<? echo $batch_against[$row[csf('batch_against')]]; ?>" class="text_boxes" style="width:80px" placeholder="Display"disabled />
                <input type="hidden" name="txtEmblTypeId[]"  id="txtEmblTypeId_<? echo $tblRow; ?>" value="<? echo $row[csf('batch_against')]; ?>" class="text_boxes_numeric" style="width:50px" disabled />
            </td>
            <td>
                 <input type="text" name="txtBatchNo[]"  id="txtBatchNo_<? echo $tblRow; ?>"  value="<? echo $batch_no_arr[$row[csf('batch_id')]]; ?>" class="text_boxes_numeric" style="width:100px" placeholder="Display"disabled />
                 <input type="hidden" name="hiddenBatchId[]"  id="hiddenBatchId_<? echo $tblRow; ?>" value="<? echo $row[csf('batch_id')]; ?>" class="text_boxes_numeric" style="width:50px" placeholder="Display"disabled />
            </td>
            <td>
               <input type="text" name="txtBatchcolor[]"  id="txtBatchcolor_<? echo $tblRow; ?>" value="<? echo $color_arr[$row[csf('color_id')]]; ?>" class="text_boxes" style="width:80px" placeholder="Display"disabled />
                <input type="hidden" name="txtBatchColorId[]"  id="txtBatchColorId_<? echo $tblRow; ?>" value="<? echo $row[csf('color_id')]; ?>" style="width:30px" disabled />
            </td>
            <td id="">
                <input type="text" name="cboGmtsItem[]"  id="cboGmtsItem_<? echo $tblRow; ?>" value="<? echo $gmts_item_array[$row[csf('prod_id')]]; ?>" class="text_boxes"  style="width:80px" readonly disabled />
                <input type="hidden" name="txtGmtsItem[]"  id="txtGmtsItem_<? echo $tblRow; ?>" value="<? echo $row[csf('prod_id')]; ?>" class="text_boxes"  style="width:50px" />
            </td>
            <td>
                 <input type="text" name="txtProcessName[]" id="txtProcessName_<? echo $tblRow; ?>" class="text_boxes" style="width:80px;"  tabindex="12"   placeholder="Dbl.Click" readonly onDblClick="process_popup('<? echo $row[csf('sub_process_id')]; ?>');" title="Bbl. Click" />
                <input type="hidden" name="txtProcessId[]" id="txtProcessId_<? echo $tblRow; ?>" value="<? echo $row[csf('sub_process_id')]; ?>" />
            </td>
            
            <td>
               <input type="text" name="txtQcPassQty[]"  id="txtQcPassQty_<? echo $tblRow; ?>" value="<? echo $QcPassQty; ?>" class="text_boxes_numeric"  style="width:50px" readonly disabled />
            </td>
            <td>
                <input type="text" name="txtPreDelivQty[]"  id="txtPreDelivQty_<? echo $tblRow; ?>" value="<? echo $prevDelivery; ?>" class="text_boxes_numeric" style="width:50px"   placeholder="Display"  />
            </td>
            <td>
                <input type="text" name="txtBalance[]"  id="txtBalance_<? echo $tblRow; ?>" value="<? echo $balance; ?>" class="text_boxes_numeric"  style="width:50px" placeholder="Display" readonly />
            </td>
            <td>
                <input type="text" name="txtCurrentDelivery[]"  id="txtCurrentDelivery_<? echo $tblRow; ?>" value="<? echo $currDelivery; ?>" class="text_boxes_numeric" onKeyUp="calculate_amount(<? echo $tblRow; ?>);"   placeholder="Write"  style="width:50px" />
                <input type="hidden" name="hiddenCurrentDelivery[]"  id="hiddenCurrentDelivery_<? echo $tblRow; ?>" value="<? echo $currDelivery; ?>" class="text_boxes_numeric" onKeyUp="calculate_amount(<? echo $tblRow; ?>);"   placeholder="Write"  style="width:50px" />
            </td>
            <td>
                <input type="text" name="txtCustBuyer[]"  id="txtCustBuyer_1" value="<? echo $custBuyerArr[$row[csf('order_id')]]; ?>" class="text_boxes" style="width:80px" placeholder="Display" disabled  />
                
            </td>
            <td>
                <input type="text" name="txtStyle[]"  id="txtStyle_<? echo $tblRow; ?>" value="<? echo $po_style_arr[$row[csf('order_id')]]; ?>" class="text_boxes" style="width:80px"   placeholder="Display" disabled  />
            </td>
            <td>
            <input type="text" name="txtOperatorName[]" id="txtOperatorName_<? echo $tblRow; ?>" value="<? echo $OperatorNameArr[$row[csf('operator_id')]]; ?>" class="text_boxes" style="width:100px;"   placeholder="Display"  disabled  tabindex="4" />
            <input type="hidden" name="txtOperatorId[]" id="txtOperatorId_<? echo $tblRow; ?>" value="<? echo $row[csf('operator_id')]; ?>" style="width:60px"/>
            </td>
            <td>
				<?
                echo create_drop_down( "cboShift_1", 80, $shift_name,"", 1, '- Select -',$row[csf('shift_id')],"",'1','','','','','','','cboShift[]');
                ?>
            </td>
        </tr>
		<?
	}
	?>
    <!--<tr>
    	<td colspan="6" style="text-align:right;font-weight:bold">Sum</td>
        <td>
               <input type="text" name="txtSumQcPassQty"  id="txtSumQcPassQty" value="<? //echo $sumQcPass; ?>" class="text_boxes_numeric"  style="width:50px" readonly disabled />
            </td>
            <td>
                <input type="text" name="txtSumPreDelivQty"  id="txtSumPreDelivQty" value="<? //echo $sumPrevous; ?>" class="text_boxes_numeric" style="width:50px"   placeholder="Display"  readonly/>
            </td>
            <td>
                <input type="text" name="txtSumBalance"  id="txtSumBalance" value="<? //echo $sumBlance; ?>" class="text_boxes_numeric"  style="width:50px" placeholder="Display" readonly />
            </td>
            <td>
                <input type="text" name="txtSumCurrentDelivery"  id="txtSumCurrentDelivery" value="<? //echo $sumCurrentDelv; ?>" class="text_boxes_numeric" placeholder="Write"  style="width:50px" readonly />
            </td>
	</tr>-->
	<?
	exit();
}


if($action=="process_name_popup")
{
  	echo load_html_head_contents("Process Name Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
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
			var old=document.getElementById('txt_process_row_id').value; 
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
			
			$('#hidden_process_id').val(id);
			$('#hidden_process_name').val(name);
		}
    </script>

</head>

<body>
<div align="center">
	<fieldset style="width:275px;margin-left:10px">
        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="272" class="rpt_table" >
                <thead>
                    <th width="50">SL</th>
                    <th>Process Name</th>
                </thead>
            </table>
            <div style="width:274px; overflow-y:scroll; max-height:300px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="255" class="rpt_table" id="tbl_list_search" >
                <?

                	if($hiddenBatchAgainst==6){ $new_subprocess_array = $emblishment_wash_type;}
                	else if($hiddenBatchAgainst==10){ $new_subprocess_array = $emblishment_print_type;}
                	else if($hiddenBatchAgainst==7){ $new_subprocess_array = $emblishment_gmts_type;}

                    $i=1; $process_row_id=''; 

					$hidden_process_id=explode(",",$process_id);
                    foreach($new_subprocess_array as $id=>$name)
                    {
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						 
						if(in_array($id,$hidden_process_id)) 
						{ 
							if($process_row_id=="") $process_row_id=$i; else $process_row_id.=",".$i;
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" >
							<td width="50" align="center"><?php echo "$i"; ?>
								<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>	
								<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name; ?>"/>
                                <input type="hidden" name="txt_mandatory" id="txt_mandatory<?php echo $i ?>" value="<? echo $mandatory; ?>"/>
							</td>	
							<td><p><? echo $name; ?></p></td>
						</tr>
						<?
						$i++;
                    }
                ?>
                    <input type="hidden" name="txt_process_row_id" id="txt_process_row_id" value="<?php echo $process_row_id; ?>"/>
                </table>
            </div>
             
        </form>
    </fieldset>
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_all();
</script>
</html>
<?
exit();
}


if ($action=="delivery_id_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('selected_delivery_id').value=id;
			parent.emailwindow.hide();
		}		
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="deliverysearch_1"  id="deliverysearch_1" autocomplete="off">
                <table width="650" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="110">Delivery ID</th>
                        <th width="80">Year</th>
                        <th width="200">Date Range</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('deliverysearch_1','search_div','','','','');" /></th>           
                    </thead>
                    <tbody>
                        <tr>
                            <td> <input type="hidden" id="selected_delivery_id"><?php //$data=explode("_",$data); ?>  <!--  echo $data;-->
								<?php echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $data, "",0); ?>
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:95px" />
                            </td>
                            <td> 
                                <?php
                                    $selected_year=date("Y");
                                    echo create_drop_down( "cbo_year", 60, $year,"", 1, "-Year-", $selected_year, "",0 );
                                ?>
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_year').value, 'create_delivery_search_list_view', 'search_div', 'subcon_embellishment_delivery_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="5" align="center" height="40" valign="middle">
								<?php echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" align="center" valign="top" id=""><div id="search_div"></div> </td>
                        </tr>
                    </tbody>
                </table>  
            </form>
        </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?php
	exit();
}

if($action=="create_delivery_search_list_view")
{
	//echo $data;die;
	$data=explode('_',$data);
	
	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[3]!='') $delivery_id_cond=" and delivery_prefix_num= '$data[3]'"; else $delivery_id_cond="";
	//$trans_Type="issue";
	
	if($db_type==0)
	{ 
		if ($data[1]!="" &&  $data[2]!="") $delivery_date= " and delivery_date between '".change_date_format($data[1],'yyyy-mm-dd')."' and '".change_date_format($data[2],'yyyy-mm-dd')."'"; else $delivery_date= "";
		$year_cond= "year(insert_date)as year";
	}
	else if ($db_type==2)
	{
		if ($data[1]!="" &&  $data[2]!="") $delivery_date= " and delivery_date between '".change_date_format($data[1], "", "",1)."' and '".change_date_format($data[2], "", "",1)."'";  else $delivery_date= "";
		$year_cond= "TO_CHAR(insert_date,'YYYY') as year";
	}
	
	$sql= "select id, delivery_no, company_id, delivery_prefix_num, $year_cond, location_id, party_id, challan_no, delivery_date, forwarder, transport_company from subcon_delivery_mst where status_active=1 and is_deleted=0 and entry_form=4 $company $delivery_date $delivery_id_cond order by id DESC";
	//echo $sql; die;
	$result = sql_select($sql);
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$location_arr=return_library_array( "select id, location_name from  lib_location",'id','location_name');
	
	?> 
    <script>
		$(document).ready(function(e) {
            setFilterGrid('tbl_po_list',-1);
        });

	</script>   
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table">
            <thead>
                <th width="50" >SL</th>
                <th width="70" >Delivery ID</th>
                <th width="60" >Year</th>
                <th width="120" >Party</th>
                <th width="120" >Challan No</th>
                <th width="70" >Delivery Date</th>
                <th>Location</th>
            </thead>
     	</table>
     </div>
     <div style="width:650px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="630" class="rpt_table" id="tbl_po_list">
			<?php
			$i=1;
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
					
                <tr bgcolor="<?php echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<?php echo $row[csf('id')]; ?>);" > 
						<td width="50" align="center"><?php echo $i; ?></td>
						<td width="70" align="center"><?php echo $row[csf("delivery_prefix_num")]; ?></td>
                        <td width="60" align="center"><?php echo $row[csf("year")]; ?></td>		
						<td width="120" align="center"><?php echo $party_arr[$row[csf("party_id")]]; ?></td>
						<td width="120"><?php echo $row[csf("challan_no")];  ?></td>	
						<td width="70"><?php echo $row[csf("delivery_date")]; ?></td>
						<td ><?php echo $location_arr[$row[csf("location_id")]];?> </td>	
					</tr>
				<?php 
				$i++;
            }
   		?>
			</table>
		</div> 
	<?php	
	exit();		
}

if ($action=="load_php_data_to_form")
{
	//echo "select id, delivery_no, company_id, location_id, party_id, challan_no, delivery_date, forwarder, transport_company from subcon_delivery_mst where id='$data' and status_active=1 and is_deleted=0";die;
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	
	$nameArray=sql_select( "select id, delivery_no, company_id, location_id, party_id, challan_no, delivery_date,process_id, order_id, transport_company from subcon_delivery_mst where id='$data' and entry_form=4 and status_active=1 and is_deleted=0" ); 
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_sys_id').value 			= '".$row[csf("delivery_no")]."';\n";
		echo "document.getElementById('cbo_company_id').value 	= '".$row[csf("company_id")]."';\n";
		echo "load_drop_down( 'requires/subcon_embellishment_delivery_controller', $('#cbo_company_id').val(), 'load_drop_down_location', 'location_td' );";
		echo "document.getElementById('cbo_location_name').value			= '".$row[csf("location_id")]."';\n";
		//echo "load_drop_down( 'requires/subcon_embellishment_delivery_controller', $('#cbo_company_name').val(), 'load_drop_down_party_name', 'party_td' );";
		echo "document.getElementById('cbo_party_name').value		= '".$party_arr[$row[csf("party_id")]]."';\n"; 
		echo "document.getElementById('cbo_party_id').value		= '".$row[csf("party_id")]."';\n"; 
		echo "$('#cbo_party_name').attr('disabled','true')".";\n"; 
		echo "document.getElementById('txt_challan_no').value		= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_delivery_date').value	= '".change_date_format($row[csf("delivery_date")])."';\n"; 
		echo "document.getElementById('txt_transport_company').value 		= '".$row[csf("transport_company")]."';\n";   
		//echo "document.getElementById('cbo_forwarder').value		= '".$row[csf("forwarder")]."';\n"; 
		//echo "document.getElementById('txt_vehical_no').value		= '".$row[csf("vehical_no")]."';\n"; 
		echo "document.getElementById('txtPoId').value		= '".$row[csf("order_id")]."';\n"; 
		echo "document.getElementById('hiddenBatchAgainst').value		= '".$row[csf("process_id")]."';\n";
		
		//echo "document.getElementById('txt_order_no').value		= '".$row[csf("order_id")]."';\n";
		
		 
		echo "document.getElementById('txt_update_id').value			= '".$row[csf("id")]."';\n"; 
		//echo "document.getElementById('txt_mst_id').value			= '".$row[csf("id")]."';\n"; 
		//echo "set_button_status(0, '".$_SESSION['page_permission']."','fnc_material_issue',1);\n";txt_mst_id
	}
	exit();	
}

if($action=="delivery_list_view")
{
?>	
	<div style="width:300px">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="120" >Order No</th>
                <th width="80" >Delivery Qty</th>                    
            </thead>
    	</table> 
    </div>
	<div style="width:300px;max-height:180px; overflow:y-scroll" id="" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="details_table">
		<?php  
			$i=1;
			$sqlResult =sql_select("select a.id,a.delivery_date,a.location_id,a.challan_no,b.id as dtlsID,b.order_id,b.item_id,b.process_id,b.delivery_qty from  subcon_delivery_mst a,  subcon_delivery_dtls b where a.id=b.mst_id and  a.id='$data' and a.status_active=1 and a.is_deleted=0 order by a.id");
 			foreach($sqlResult as $selectResult)
			{
 				if ($i%2==0)  
                	$bgcolor="#E9F3FF";
                else
               	 	$bgcolor="#FFFFFF";
				
 			?>
                <tr bgcolor="<?php echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="fnc_embl_delivery_dtls('<?php echo $selectResult[csf('id')]; ?>_<?php echo $selectResult[csf('dtlsID')]; ?>','populate_delivery_form_data','requires/subcon_embellishment_delivery_controller');" > 
                    <td width="40" align="center"><?php echo $i; ?></td>
                    <td width="120" align="center"><p><?php echo $order_num_arr[$selectResult[csf('order_id')]]; ?>&nbsp;</p></td>
                    <td width="80" align="center"><p><?php echo $selectResult[csf('delivery_qty')]; ?></p></td>
                </tr>
			<?php
			$i++;
			}
			?>
		</table>
	</div>
<?php
	exit();
}


if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
 		//table lock here 
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		if(str_replace("'","",$txt_update_id)=="")
		{
			$id=return_next_id("id", "subcon_delivery_mst", 1);

			if($db_type==2) $mrr_cond="and  TO_CHAR(insert_date,'YYYY')=".date('Y',time()); else if($db_type==0) $mrr_cond="and year(insert_date)=".date('Y',time());
			$new_sys_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'EMBL', date("Y",time()), 5, "select delivery_prefix, delivery_prefix_num from subcon_delivery_mst where company_id=$cbo_company_id $mrr_cond order by id DESC ", "delivery_prefix", "delivery_prefix_num" ));
			
			$field_array_mst="id, delivery_prefix, delivery_prefix_num, delivery_no, company_id, location_id, challan_no, party_id, transport_company, delivery_date, process_id, entry_form, inserted_by, insert_date";
			if(str_replace("'","",$txt_challan_no)=="")
			{
				$challan_no=$new_sys_number[2];
			}
			else
			{
				$challan_no=str_replace("'","",$txt_challan_no);
			}
			
			$data_array_mst="(".$id.",'".$new_sys_number[1]."','".$new_sys_number[2]."','".$new_sys_number[0]."', ".$cbo_company_id.",".$cbo_location_name.",'".$challan_no."',".$cbo_party_id.",".$txt_transport_company.",".$txt_delivery_date.",".$hiddenBatchAgainst.",4,".$user_id.",'".$pc_date_time."')";
			$mrr_no=$new_sys_number[0];
			$mrr_no_challan=$new_sys_number[2];
		}
		/*
		else
		{
			$mst_id=str_replace("'","",$txt_update_id);
			$mrr_no=str_replace("'","",$txt_sys_id);
			$mrr_no_challan=str_replace("'","",$txt_challan_no);
			
			$field_array_delivery="location_id*challan_no*party_id*transport_company*delivery_date*vehical_no*forwarder*updated_by*update_date";
			$data_array_delivery="".$cbo_location_name."*".$txt_challan_no."*".$cbo_party_name."*".$txt_transport_company."*".$txt_delivery_date."*".$txt_vehical_no."*".$cbo_forwarder."*".$user_id."*'".$pc_date_time."'";
		}
		*/
		
		//echo "10**INSERT INTO subcon_delivery_mst (".$field_array_mst.") VALUES ".$data_array_mst;    die;
		
		
		
		
		
		$PoIdValidation="";
		$data_array_dtls="";
		$id_dtls=return_next_id("id", "subcon_delivery_dtls", 1);
		//id, mst_id, batch_against, batch_id, color_id, prod_id, sub_process_id, delivery_qty, operator_id, operator_id, subcon_delivery_dtls 
  		$field_array_dtls="id, mst_id, batch_against, batch_id, color_id, prod_id, sub_process_id, delivery_qty, operator_id, order_id, embl_prod_dtls_id, shift_id";
		for($i=1; $i<= str_replace("'","",$txt_tot_row); $i++)
		{
			
			$txtEmblTypeId="txtEmblTypeId_".$i;
			$txtOrderId="txtOrderId_".$i;
			$emblProdDtlsId="emblProdDtlsId_".$i;
			$hiddenBatchId="hiddenBatchId_".$i;
			$txtBatchColorId="txtBatchColorId_".$i;
			$txtGmtsItem="txtGmtsItem_".$i;
			$txtProcessId="txtProcessId_".$i;
			$txtQcPassQty="txtQcPassQty_".$i;
			$txtCurrentDelivery="txtCurrentDelivery_".$i;
			$txtOperatorName="txtOperatorName_".$i;
			$txtOperatorId="txtOperatorId_".$i;
			$updateIdDtls="updateIdDtls_".$i;
			$cboShift="cboShift_".$i;
			 
			if($data_array_dtls!="") $data_array_dtls.=","; 	
			$data_array_dtls.="(".$id_dtls.",".$id.",".$$txtEmblTypeId.",'".$$hiddenBatchId."','".$$txtBatchColorId."','".$$txtGmtsItem."','".$$txtProcessId."','".$$txtCurrentDelivery."','".$$txtOperatorId."',".$$txtOrderId.",'".$$emblProdDtlsId."','".$$cboShift."')"; 
			$id_dtls=$id_dtls+1;
		}
		
		//echo "10**$total_row**INSERT INTO subcon_delivery_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls;    die;
		$rID=sql_insert("subcon_delivery_mst",$field_array_mst,$data_array_mst,1);
		$rID1=sql_insert("subcon_delivery_dtls",$field_array_dtls,$data_array_dtls,1);
		
		//echo "10**$rID**$rID1"; die;
		
		if($db_type==0)
		{
			if($rID  && $rID1 )
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'","",$id)."**".$new_sys_number[0]."**".$challan_no;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$id);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
				if($rID  && $rID1 )
				{
					oci_commit($con);  
					echo "0**".str_replace("'","",$id)."**".$new_sys_number[0]."**".$challan_no;
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
		}
		
		disconnect($con);
		die;
	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		
		$delivery_mst_id=str_replace("'","",$txt_update_id);
		$mrr_no=str_replace("'","",$txt_system_no);
		$mrr_no_challan=str_replace("'","",$txt_challan_no);
		$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
		
		/*
		if(str_replace("'","",$txt_update_id)=="")
		{
			$id=return_next_id("id", "subcon_delivery_mst", 1);

			if($db_type==2) $mrr_cond="and  TO_CHAR(insert_date,'YYYY')=".date('Y',time()); else if($db_type==0) $mrr_cond="and year(insert_date)=".date('Y',time());
			$new_sys_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'EMBL', date("Y",time()), 5, "select delivery_prefix, delivery_prefix_num from subcon_delivery_mst where company_id=$cbo_company_id $mrr_cond order by id DESC ", "delivery_prefix", "delivery_prefix_num" ));
			
			$field_array_mst="id, delivery_prefix, delivery_prefix_num, delivery_no, company_id, location_id, challan_no, party_id, transport_company, delivery_date, order_id, entry_form, inserted_by, insert_date";
			if(str_replace("'","",$txt_challan_no)=="")
			{
				$challan_no=$new_sys_number[2];
			}
			else
			{
				$challan_no=str_replace("'","",$txt_challan_no);
			}
			
			$data_array_mst="(".$id.",'".$new_sys_number[1]."','".$new_sys_number[2]."','".$new_sys_number[0]."', ".$cbo_company_id.",".$cbo_location_name.",'".$challan_no."',".$cbo_party_id.",".$txt_transport_company.",".$txt_delivery_date.",".$txtPoId.",4,".$user_id.",'".$pc_date_time."')";
			$mrr_no=$new_sys_number[0];
			$mrr_no_challan=$new_sys_number[2];
		}
		*/
		
		//$field_array_delivery="company_id*location_id*challan_no*party_id*transport_company*delivery_date*vehical_no*forwarder*updated_by*update_date";
		//$data_array_delivery="".$cbo_company_id."*".$cbo_location_name."*".$txt_challan_no."*".$cbo_party_name."*".$txt_transport_company."*".$txt_delivery_date."*".$txt_vehical_no."*".$cbo_forwarder."*".$user_id."*'".$pc_date_time."'";
		
		$field_array_update_mst="company_id*location_id*challan_no*party_id*transport_company*delivery_date*updated_by*update_date";
		$data_array_update_mst="".$cbo_company_id."*".$cbo_location_name."*".$txt_challan_no."*".$cbo_party_id."*".$txt_transport_company."*".$txt_delivery_date."*".$user_id."*'".$pc_date_time."'";
		
		$data_array_dtls="";
		$id_dtls=return_next_id("id", "subcon_delivery_dtls", 1);
  		$field_array_dtls="id, mst_id, batch_against, batch_id, color_id, prod_id, sub_process_id, delivery_qty, operator_id, order_id, embl_prod_dtls_id, shift_id";
		
		
		
		
		$field_array_update_dtls="delivery_qty";
		$data_array_update_dtls="";
		for($i=1; $i<= str_replace("'","",$txt_tot_row); $i++)
		{
			
			$txtEmblTypeId="txtEmblTypeId_".$i;
			$txtOrderId="txtOrderId_".$i;
			$emblProdDtlsId="emblProdDtlsId_".$i;
			$hiddenBatchId="hiddenBatchId_".$i;
			$txtBatchColorId="txtBatchColorId_".$i;
			$txtGmtsItem="txtGmtsItem_".$i;
			$txtProcessId="txtProcessId_".$i;
			$txtQcPassQty="txtQcPassQty_".$i;
			$txtCurrentDelivery="txtCurrentDelivery_".$i;
			$txtOperatorName="txtOperatorName_".$i;
			$txtOperatorId="txtOperatorId_".$i;
			$updateIdDtls="updateIdDtls_".$i;
			$cboShift="cboShift_".$i;
			
			$updateIds = str_replace("'","",$$updateIdDtls);
			if( $updateIds != "")
			{
				$updateIdDtls_array[]=$updateIds;
				$data_array_update_dtls[$updateIds] = explode("*",("".$$txtCurrentDelivery.""));
			}else{
				if($data_array_dtls!="") $data_array_dtls.=","; 	
				$data_array_dtls.="(".$id_dtls.",".$txt_update_id.",".$$txtEmblTypeId.",'".$$hiddenBatchId."','".$$txtBatchColorId."','".$$txtGmtsItem."','".$$txtProcessId."','".$$txtCurrentDelivery."','".$$txtOperatorId."',".$$txtOrderId.",'".$$emblProdDtlsId."','".$$cboShift."')"; 
				$id_dtls=$id_dtls+1;
			}
		}

		
		$rID=$rID1=$rID2=1;
		
		$rID=sql_update("subcon_delivery_mst",$field_array_update_mst,$data_array_update_mst,"id",$txt_update_id,0);
		//echo "10**$txt_tot_row<pre>";
		//print_r($data_array_update_dtls);die;
		//echo "10**".bulk_update_sql_statement("subcon_delivery_dtls", "id", $field_array_update_dtls, $data_array_update_dtls, $updateIdDtls_array); die;
		if($data_array_update_dtls !=""){
			$rID1=execute_query(bulk_update_sql_statement( "subcon_delivery_dtls", "id", $field_array_update_dtls, $data_array_update_dtls, $updateIdDtls_array));
		}
		
		if($data_array_dtls !=""){
			//echo "10**insert into subcon_delivery_dtls ($field_array_dtls) values $data_array_dtls "; die;
			$rID2=sql_insert("subcon_delivery_dtls",$field_array_dtls,$data_array_dtls,1);
		}
		
		//echo "10**$rID**$rID1**$rID2"; die;
		if($db_type==0)
		{
				if( $rID && $rID1 && $rID2 )
				{
					mysql_query("COMMIT");  
					//echo "1**".str_replace("'","",$txt_mst_id)."**".$mrr_no."**".$mrr_no_challan;
					echo "1**".str_replace("'","",$txt_update_id)."**".$mrr_no."**".$mrr_no_challan;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".str_replace("'","",$txt_update_id);
				}
			
		}
		if($db_type==2 || $db_type==1 )
		{

			if($rID && $rID1 && $rID2 )
			{
				oci_commit($con); 
				echo "1**".str_replace("'","",$txt_update_id)."**".$mrr_no."**".$mrr_no_challan;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_update_id);
			}
			
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here---------------------------------------------------------- 
	{
		
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$delivery_mst_id=str_replace("'","",$txt_update_id);
		$mrr_no=str_replace("'","",$txt_system_no);
		$mrr_no_challan=str_replace("'","",$txt_challan_no);
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="'".$user_id."'*'".$pc_date_time."'*0*1";

		$rID=sql_delete("subcon_delivery_mst",$field_array,$data_array,"id","".$txt_update_id."",1);
  		
		//$rID = sql_delete("pro_ex_factory_mst",$field_array,$data_array,"id",$txt_mst_id,1);
		//$dtlsrID = sql_delete("pro_ex_factory_dtls","status_active*is_deleted","0*1",'mst_id',$txt_mst_id,1);
 		
 		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_update_id)."**".$mrr_no."**".$mrr_no_challan;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_update_id); 
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con); 
				echo "2**".str_replace("'","",$txt_update_id)."**".$mrr_no."**".$mrr_no_challan; 
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_update_id); 
			}
		}
		disconnect($con);
		die;
		
	}
}


























die;


//=============================================================================================================================================//
//=============================================================================================================================================//
//=============================================================================================================================================//
if ($action=="load_variable_settings__1")
{
	$data=explode("_",$data);
	//echo "KKKKK";
	echo "$('#sewing_production_variable').val(0);\n";

	if($data[1]==1)
	{
		$sql_result = sql_select("select cutting_update as ex_factory,sewing_production as production_entry  from  variable_settings_production where company_name=$data[0] and variable_list=1 and status_active=1");
	}
	else if($data[1]==5)
	{
		$sql_result = sql_select("select ex_factory, production_entry from variable_settings_production where company_name=$data[0] and variable_list=1 and status_active=1");
	}
	else if($data[1]==8)
	{
		$sql_result = sql_select("select printing_emb_production as ex_factory,sewing_production as production_entry  from  variable_settings_production where company_name=$data[0] and variable_list=1 and status_active=1");
	}
	else if($data[1]==10)
	{
		$sql_result = sql_select("select  	iron_update as ex_factory,sewing_production as production_entry  from  variable_settings_production where company_name=$data[0] and variable_list=1 and status_active=1");
	}
	else if($data[1]==11)
	{
		$sql_result = sql_select("select finishing_update as ex_factory,sewing_production as production_entry  from  variable_settings_production where company_name=$data[0] and variable_list=1 and status_active=1");
	}
	
	foreach($sql_result as $result)
	{
		echo "$('#sewing_production_variable').val(".$result[csf("ex_factory")].");\n";
		echo "$('#styleOrOrderWisw').val(".$result[csf("production_entry")].");\n";
	}
 	exit();
}

if($action=="populate_delivery_form_data____________________1")
{
	$delivery_value=array(); $amountArr=array();
	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
	
	
	 $sqlResult =sql_select("select a.id, b.id,b.order_id as po_id, b.item_id as item_number_id,b.process_id, a.location_id, a.delivery_date, b.delivery_qty, b.total_carton_qnty, a.challan_no, b.carton_roll, a.transport_company, b.entry_break_down_type  from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.id='$data' and a.status_active=1 and a.is_deleted=0 order by a.id");
	/*$sqlResult =sql_select("select a.id,a.garments_nature,a.po_break_down_id,a.item_number_id,a.country_id,a.location,a.ex_factory_date,a.ex_factory_qnty,a.total_carton_qnty,a.challan_no,a.invoice_no,a.lc_sc_no,a.carton_qnty,a.transport_com,a.remarks,a.shiping_status,a.entry_break_down_type,b.id as delivery_id,b.sys_number,b.transport_supplier,b.lock_no,b.driver_name,b.truck_no,b.dl_no,b.transport_supplier  from pro_ex_factory_mst a, pro_ex_factory_delivery_mst b where a.id='$data' and a.delivery_mst_id=b.id and b.status_active=1 and b.is_deleted=0  and a.status_active=1 and a.is_deleted=0 order by a.id");*/
 	foreach($sqlResult as $result)
	{
		 
 		echo "$('#hidden_po_break_down_id').val('".$result[csf('po_id')]."');\n";
		//echo "$('#txt_order_no').val('".$order_num_arr[$result[csf('po_id')]]."');\n";
 		echo "$('#txt_ctn_qnty').val('".$result[csf('carton_roll')]."');\n";
		echo "$('#txt_total_carton_qnty').val('".$result[csf('total_carton_qnty')]."');\n";
		echo "$('#txt_delivery_qty').val('".$result[csf('delivery_qty')]."');\n";
		echo "$('#cbo_item_name').val('".$result[csf('item_number_id')]."');\n";
		echo "$('#cbo_process_name').val('".$result[csf('process_id')]."');\n";
		echo "get_php_form_data(document.getElementById('hidden_po_break_down_id').value+'**'+document.getElementById('cbo_item_name').value, 'populate_data_from_search_popup', 'requires/subcon_embellishment_delivery_controller');\n";
		echo "$('#txt_dtls_id').val('".$result[csf('id')]."');\n";
	
		$process_id=$result[csf('process_id')];
				
 	
		//echo "$('#txt_transport_company').val('".$result[csf('transport_company')]."');\n";
		
		//echo "$('#txt_mst_id').val('".$result[csf('id')]."');\n";
 		echo "set_button_status(1, permission, 'fnc_embl_delivery',1,1);\n";
		
		//break down of color and size------------------------------------------
 		//#############################################################################################//
		// order wise - color level, color and size level

		$variableSettings = $result[csf('entry_break_down_type')];
		
		
		//$variableSettings=2;
		
		if( $variableSettings!=1 ) // gross level
		{ 
			$po_id = $result[csf('po_id')];
			$item_id = $result[csf('item_number_id')];
			
			
			$sql_dtls = sql_select("select a.id,b.delivery_qty,a.size_id, a.color_id from  subcon_ord_breakdown a,subcon_gmts_delivery_dtls b where   a.id=b.breakdown_color_size_id and a.order_id='$po_id' and a.item_id='$item_id' ");	
			foreach($sql_dtls as $row)
			{				  
				if( $variableSettings==2 ) $index = $row[csf('color_id')]; else $index = $row[csf('size_id')].$row[csf('color_id')];
			  	 $amountArr[$index] = $row[csf('delivery_qty')];
				// echo  $index;
			}  
			
			if( $variableSettings==2 ) // color level
			{
				//if($db_type==2)
				//{
					
					if($process_id==1)
					{
					$sql = "select a.item_id, a.color_id, sum(a.qnty) as order_quantity, sum(a.plan_cut) as plan_cut_qnty,
							sum(CASE WHEN b.production_type=1 then b.prod_qnty ELSE 0 END) as prod_qnty
							
							from subcon_ord_breakdown a 
							left join subcon_gmts_prod_col_sz b on a.id=b.ord_color_size_id
							where a.order_id='$po_id' and a.item_id='$item_id'  group by a.item_id, a.color_id";
					}
					else if($process_id==5)
					{
					$sql = "select a.item_id, a.color_id, sum(a.qnty) as order_quantity, sum(a.plan_cut) as plan_cut_qnty,
							sum(CASE WHEN b.production_type=2 then b.prod_qnty ELSE 0 END) as prod_qnty
							from subcon_ord_breakdown a 
							left join subcon_gmts_prod_col_sz b on a.id=b.ord_color_size_id
							where a.order_id='$po_id' and a.item_id='$item_id'  group by a.item_id, a.color_id";
					}
					else if($process_id==10)
					{
					$sql = "select a.item_id, a.color_id, sum(a.qnty) as order_quantity, sum(a.plan_cut) as plan_cut_qnty,
							sum(CASE WHEN b.production_type=3 then b.prod_qnty ELSE 0 END) as prod_qnty
							from subcon_ord_breakdown a 
							left join subcon_gmts_prod_col_sz b on a.id=b.ord_color_size_id
							where a.order_id='$po_id' and a.item_id='$item_id'  group by a.item_id, a.color_id";
					}
					else if($process_id==11)
					{
					$sql = "select a.item_id, a.color_id, sum(a.qnty) as order_quantity, sum(a.plan_cut) as plan_cut_qnty,
							sum(CASE WHEN b.production_type=4 then b.prod_qnty ELSE 0 END) as prod_qnty
							from subcon_ord_breakdown a 
							left join subcon_gmts_prod_col_sz b on a.id=b.ord_color_size_id
							where a.order_id='$po_id' and a.item_id='$item_id'  group by a.item_id, a.color_id";
					}
							
					$sql_del=sql_select("select a.item_id,a.color_id,sum(b.delivery_qty) as delivery_qty from subcon_ord_breakdown a
							left join subcon_gmts_delivery_dtls b on b.breakdown_color_size_id=a.id
							where a.order_id='$po_id' and a.item_id='$item_id' group by a.item_id, a.color_id");
					foreach($sql_del as $row_d)
					{
						$delivery_value[$row_d[csf("item_id")]][$row_d[csf("color_id")]]=$row_d[csf("delivery_qty")];
						
					}
				//}
				
				
			}
			else if( $variableSettings==3 ) //color and size level
			{
				/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=8 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN ex.color_size_break_down_id=wo_po_color_size_breakdown.id then ex.production_qnty ELSE 0 END) from pro_ex_factory_dtls ex where ex.is_deleted=0 ) as ex_production_qnty  
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1"; */
					if($process_id==1)
					{
					 $prodData = "select b.ord_color_size_id,sum(a.production_qnty) as production_qnty
										from  subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz b where  b.dtls_id=a.id and a.order_id='$po_id' and a.gmts_item_id='$item_id' and b.production_type=1 group by b.ord_color_size_id";
					}
					else if($process_id==5)
					{
					 $prodData = "select b.ord_color_size_id,sum(a.production_qnty) as production_qnty
										from  subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz b where  b.dtls_id=a.id and a.order_id='$po_id' and a.gmts_item_id='$item_id' and b.production_type=2 group by b.ord_color_size_id";
					}
					else if($process_id==10)
					{
					 $prodData = "select b.ord_color_size_id,sum(a.production_qnty) as production_qnty
										from  subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz b where  b.dtls_id=a.id and a.order_id='$po_id' and a.gmts_item_id='$item_id' and b.production_type=3 group by b.ord_color_size_id";
					}
					else if($process_id==11)
					{
					 $prodData = "select b.ord_color_size_id,sum(a.production_qnty) as production_qnty
										from  subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz b where  b.dtls_id=a.id and a.order_id='$po_id' and a.gmts_item_id='$item_id' and b.production_type=4 group by b.ord_color_size_id";
					}
					$result_prod_data=sql_select( $prodData);
					//echo $prodData;die;
			foreach($result_prod_data as $row)
			{				  
				$color_size_pro_qnty_array[$row[csf('ord_color_size_id')]]= $row[csf('production_qnty')];
			}
			
			$sql_del=sql_select("select a.item_id,a.color_id,a.size_id,sum(b.delivery_qty) as production_qnty from subcon_ord_breakdown a
                    left join subcon_gmts_delivery_dtls b on b.breakdown_color_size_id=a.id where a.order_id='$po_id' and a.item_id='$item_id'   group by a.item_id, a.color_id, a.size_id");
			foreach($sql_del as $row_exfac)
			{
				$delivery_value[$row_exfac[csf("item_id")]][$row_exfac[csf("color_id")]][$row_exfac[csf("size_id")]]=$row_exfac[csf("production_qnty")];
				
			}
					
			$sql = "select id, item_id, size_id, color_id, qnty, plan_cut from subcon_ord_breakdown where order_id='$po_id' and item_id='$item_id'   order by color_id";
				
			}
			
 			$colorResult = sql_select($sql);
 			//print_r($sql);die;
			$colorHTML="";
			$colorID='';
			$chkColor = array(); 
			$i=0;$totalQnty=0;$colorWiseTotal=0;
			foreach($colorResult as $color)
			{
				if( $variableSettings==2 ) // color level
				{  
					$amount = $amountArr[$color[csf("color_id")]];
					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$delivery_value[$color[csf('item_id')]][$color[csf('color_id')]]+$amount).'" value="'.$amount.'" onblur="fn_colorlevel_total('.($i+1).')">
				<input type="text" name="txt_color_size_id" id="txt_color_size_id_'.($i+1).'" style="width:80px"  class="text_boxes_numeric"  value="'.$color[csf("id")].'" >
					</td></tr>';				
					$totalQnty += $amount;
					$colorID .= $color[csf("color_id")].",";
				}
				else //color and size level
				{
					 $index = $color[csf("size_id")].$color[csf("color_id")];
					$amount = $amountArr[$index];
				//echo $amount.'hhhhh';
					if( !in_array( $color[csf("color_id")], $chkColor ) )
					{
						if( $i!=0 ) $colorHTML .= "</table></div>";
						$i=0;$colorWiseTotal=0;
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_id")].'span">+</span>'.$color_library[$color[csf("color_id")]].' : <span id="total_'.$color[csf("color_id")].'"></span> </h3>';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_id")].'">' ;
						$chkColor[] = $color[csf("color_id")];
						$totalFn .= "fn_total(".$color[csf("color_id")].");";
						
					}
 					$colorID .= $color[csf("size_id")]."*".$color[csf("color_id")].",";
					
					 $pro_qnty=$color_size_pro_qnty_array[$color[csf('id')]];
					$delivery_qnty=$delivery_value[$color[csf('item_id')]][$color[csf('color_id')]][$color[csf('size_id')]];
					//echo $pro_qnty.'p'.$delivery_qnty.'d'.$amount;
					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($pro_qnty-$delivery_qnty+$amount).'" onblur="fn_total('.$color[csf("color_id")].','.($i+1).')" value="'.$amount.'" ><input type="hidden" name="hidden_ord_breakdown_id" id="hidden_ord_breakdown_id_'.$color[csf("id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" value="'.$color[csf("id")].'" ></td></tr>';				
					$colorWiseTotal += $amount;
				}
				$i++; 
			}
			//echo $colorHTML;die; 
			
			if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="hidden" id="total_color" placeholder="'.$totalQnty.'" value="'.$totalQnty.'" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>'; }
			echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
			if( $variableSettings==3 )echo "$totalFn;\n";
			$colorList = substr($colorID,0,-1);
			echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
		}//end if condtion
	}
 	exit();		
}



if ($action=="load_php_data_to_form__1")
{
	//echo "select id, delivery_no, company_id, location_id, party_id, challan_no, delivery_date, forwarder, transport_company from subcon_delivery_mst where id='$data' and status_active=1 and is_deleted=0";die;
	$nameArray=sql_select( "select id, delivery_no,party_id, company_id, location_id, party_id, challan_no, delivery_date, forwarder, transport_company, vehical_no from subcon_delivery_mst where id='$data' and status_active=1 and is_deleted=0 " ); 
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_sys_id').value 			= '".$row[csf("delivery_no")]."';\n";
		//echo "document.getElementById('cbo_company_name').value 	= '".$row[csf("company_id")]."';\n";
		//echo "load_drop_down( 'requires/subcon_embellishment_delivery_controller', $('#cbo_company_name').val(), 'load_drop_down_location', 'location_td' );";
		echo "document.getElementById('cbo_location_name').value			= '".$row[csf("location_id")]."';\n";
		//echo "load_drop_down( 'requires/subcon_embellishment_delivery_controller', $('#cbo_company_name').val(), 'load_drop_down_party_name', 'party_td' );";
		echo "document.getElementById('cbo_party_name').value		= '".$row[csf("party_id")]."';\n"; 
		echo "$('#cbo_party_name').attr('disabled','true')".";\n"; 
		echo "document.getElementById('txt_challan_no').value		= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_delivery_date').value	= '".change_date_format($row[csf("delivery_date")])."';\n"; 
		echo "document.getElementById('txt_transport_company').value 		= '".$row[csf("transport_company")]."';\n";   
		echo "document.getElementById('cbo_forwarder').value		= '".$row[csf("forwarder")]."';\n"; 
		echo "document.getElementById('txt_vehical_no').value		= '".$row[csf("vehical_no")]."';\n"; 
		echo "document.getElementById('txt_update_id').value			= '".$row[csf("id")]."';\n"; 
		echo "document.getElementById('txt_mst_id').value			= '".$row[csf("id")]."';\n"; 
		//echo "set_button_status(0, '".$_SESSION['page_permission']."','fnc_material_issue',1);\n";txt_mst_id
	}
	exit();	
}

 

 
if($action=="populate_data_from_search_popup__1")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	//echo "select a.id, a.delivery_date, a.order_no, a.main_process_id, b.subcon_job, c.item_id, sum(c.qnty) as order_quantity from  subcon_ord_dtls a, subcon_ord_mst b, subcon_ord_breakdown c where a.main_process_id in (1,5,8,9,10,11) and a.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.company_id='$company' and a.id='$po_id' and c.item_id='$item_id' group by a.id, a.delivery_date, a.order_no, a.main_process_id, b.subcon_job, c.item_id";
	$res = sql_select("select a.id, a.delivery_date, a.order_no, a.main_process_id, b.subcon_job, c.item_id, sum(c.qnty) as order_quantity from  subcon_ord_dtls a, subcon_ord_mst b, subcon_ord_breakdown c where a.main_process_id in (1,5,8,9,10,11) and a.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and a.id='$po_id' and c.item_id='$item_id' group by a.id, a.delivery_date, a.order_no, a.main_process_id, b.subcon_job, c.item_id"); 
	
 	foreach($res as $result)
	{
		echo "$('#txt_order_qty').val('".$result[csf('order_quantity')]."');\n";
		echo "$('#cbo_item_name').val(".$item_id.");\n";
		
		//echo "$('#txt_order_no').val('".$result[csf('order_no')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_process_name').val('".$result[csf('main_process_id')]."');\n";
		echo "get_php_form_data(document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_process_name').value,'load_variable_settings','requires/subcon_embellishment_delivery_controller');\n";
		echo "$('#txt_job_no').val('".$result[csf('subcon_job')]."');\n";
		if ($result[csf('main_process_id')]==1)
		{
			$prod_type=1;
		}
		else if ($result[csf('main_process_id')]==5)
		{
			$prod_type=2;
		}
		else if ($result[csf('main_process_id')]==10)
		{
			$prod_type=3;
		}
		else if ($result[csf('main_process_id')]==11)
		{
			$prod_type=4;
		}

		$production_qty = return_field_value("sum(production_qnty)","subcon_gmts_prod_dtls","order_id=".$result[csf('id')]." and gmts_item_id='$item_id' and production_type='$prod_type' and status_active=1 and is_deleted=0");
 		if($production_qty=="")$production_qty=0;
		//echo $result[csf('id')].'='.$item_id;die;
		 $total_delivery = return_field_value("sum(delivery_qty) as delivery_qty ","subcon_delivery_dtls","order_id=".$result[csf('id')]." and item_id='$item_id'","delivery_qty");
		// echo $total_delivery.'Aziz';
		if($total_delivery=="")$total_delivery=0;
		
 		echo "$('#txt_prod_quantity').val('".$production_qty."');\n";
 		echo "$('#txt_cumul_quantity').attr('placeholder','".$total_delivery."');\n";
		echo "$('#txt_cumul_quantity').val('".$total_delivery."');\n";
		$yet_to_produced = $production_qty-$total_delivery;
		echo "$('#txt_yet_quantity').attr('placeholder','".$yet_to_produced."');\n";
		echo "$('#txt_yet_quantity').val('".$yet_to_produced."');\n";
  	}
 	exit();	
}

if($action=="color_and_size_level__1")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$variableSettings = $dataArr[2];
	$styleOrOrderWisw = $dataArr[3];
	$cbo_process_name = $dataArr[4];
	//echo $cbo_process_name;
	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
	//#############################################################################################//
	// order wise - color level, color and size level
	
	$delivery_value=array();
	
	//$variableSettings=2;
	
	if( $variableSettings==2 ) // color level
	{
		if($db_type==0) $group_cond=" GROUP BY a.color_id";	
		else if($db_type==2) $group_cond=" GROUP BY  a.id,a.item_id, a.color_id";	
					
		$sql = "SELECT a.id,a.item_id, a.color_id, sum(a.qnty) as qnty, sum(a.plan_cut) as plan_cut FROM subcon_ord_breakdown a left join subcon_gmts_prod_col_sz b on a.id=b.ord_color_size_id WHERE a.order_id ='$po_id' and a.item_id='$item_id' $group_cond";

		
		
	}
	else if( $variableSettings==3 ) //color and size level
	{
			
			$sql = "SELECT id, item_id, color_id, size_id, qnty as qnty, plan_cut as plan_cut FROM subcon_ord_breakdown WHERE order_id ='$po_id' and item_id='$item_id' ";
			//echo "select a.ord_color_size_id, sum(a.prod_qnty) as production_qnty from  subcon_gmts_prod_col_sz a, subcon_gmts_prod_dtls b where a.dtls_id=b.id and b.order_id='$po_id' and b.gmts_item_id='$item_id' and a.ord_color_size_id!=0 group by a.ord_color_size_id";
			
			if($cbo_process_name==1) $prod_type=1;
			else if($cbo_process_name==5) $prod_type=2;
			else if($cbo_process_name==10) $prod_type=3;
			else if($cbo_process_name==11) $prod_type=4;
			$prodData = sql_select("select a.ord_color_size_id, sum(a.prod_qnty) as production_qnty from  subcon_gmts_prod_col_sz a, subcon_gmts_prod_dtls b where a.dtls_id=b.id and b.order_id='$po_id' and b.gmts_item_id='$item_id' and a.production_type='$prod_type' and a.ord_color_size_id!=0 group by a.ord_color_size_id");
			
			foreach($prodData as $row)
			{				  
				$color_size_pro_qnty_array[$row[csf('ord_color_size_id')]]= $row[csf('production_qnty')];
			}
			$sql_del=sql_select("select a.item_id,a.color_id,a.size_id,sum(b.delivery_qty) as production_qnty from 
			subcon_ord_breakdown a left join subcon_gmts_delivery_dtls b on b.breakdown_color_size_id=a.id where a.order_id='$po_id' and a.item_id='$item_id' group by a.item_id, a.color_id, a.size_id");
			foreach($sql_del as $row_exfac)
			{
				$delivery_value[$row_exfac[csf("item_id")]][$row_exfac[csf("color_id")]][$row_exfac[csf("size_id")]]=$row_exfac[csf("production_qnty")];
				
			}
			
	
/*			$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id";
*/	}
	else // by default color and size level
	{
		
			
			if($cbo_process_name==1) $prod_type=1;
			else if($cbo_process_name==5) $prod_type=2;
			else if($cbo_process_name==10) $prod_type=3;
			else if($cbo_process_name==11) $prod_type=4;
			
			$prodData = sql_select("select a.ord_color_size_id, sum(a.prod_qnty) as production_qnty from  subcon_gmts_prod_col_sz a, subcon_gmts_prod_dtls b where a.dtls_id=b.id and b.order_id='$po_id' and b.gmts_item_id='$item_id' and a.production_type='$prod_type' and a.ord_color_size_id!=0 group by a.ord_color_size_id");
			foreach($prodData as $row)
			{				  
				$color_size_pro_qnty_array[$row[csf('ord_color_size_id')]]= $row[csf('production_qnty')];
			}
			
			
					
			$sql = "SELECT id, item_id, color_id, size_id, qnty as qnty, plan_cut as plan_cut FROM subcon_ord_breakdown WHERE order_id ='$po_id' and item_id='$item_id' ";
	}
	
	//print_r($ex_fac_value);die;
	
	$colorResult = sql_select($sql);		
	//print_r($sql);
	$colorHTML="";
	$colorID='';
	$chkColor = array(); 
	$i=0;$totalQnty=0;
	foreach($colorResult as $color)
	{
		if( $variableSettings==2 ) // color level
		{
			
			
			 
			$colorHTML .='<tr><td>'.$color_library[$color[csf("color_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($color[csf("plan_cut")]-$ex_fac_value[$color[csf('item_id')]][$color[csf('color_id')]]).'" onblur="fn_colorlevel_total('.($i+1).')"></td></tr>';				
			$totalQnty += $color[csf("plan_cut")]-$ex_fac_value[$color[csf('item_id')]][$color[csf('color_id')]];
			$colorID .= $color[csf("color_id")].",";
		}
		else //color and size level
		{
			if( !in_array( $color[csf("color_id")], $chkColor ) )
			{
				if( $i!=0 ) $colorHTML .= "</table></div>";
				$i=0;
				$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_id")].'span">+</span>'.$color_library[$color[csf("color_id")]].' : <span id="total_'.$color[csf("color_id")].'"></span> </h3>';
				$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_id")].'">';
				$chkColor[] = $color[csf("color_id")];					
			}
			//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
			$colorID .= $color[csf("size_id")]."*".$color[csf("color_id")].",";
			
			$pro_qnty=$color_size_pro_qnty_array[$color[csf('id')]];
			$exfac_qnty=$delivery_value[$color[csf('item_id')]][$color[csf('color_id')]][$color[csf('size_id')]];
			
			$colorHTML .='<tr><td>'.$size_library[$color[csf("size_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($pro_qnty-$exfac_qnty).'" onblur="fn_total('.$color[csf("color_id")].','.($i+1).')"><input type="hidden" name="hidden_ord_breakdown_id" id="hidden_ord_breakdown_id_'.$color[csf("id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" value="'.$color[csf("id")].'" ></td></tr>';				
		}
		$i++; 
	}
	//echo $colorHTML;die; 
	if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>'; }
	
	echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
	$colorList = substr($colorID,0,-1);
	echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
	//#############################################################################################//
	exit();
}


if($action=="ex_factory_print__1")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	echo load_html_head_contents("Garments Delivery Info","../", 1, 1, $unicode,'','');
	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$invoice_library=return_library_array( "select id, invoice_no from  com_export_invoice_ship_mst", "id", "invoice_no"  );
	$order_sql=sql_select("select a.id, a.po_number, b.buyer_name from  wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active=1 and b.status_active=1");
	foreach($order_sql as $row)
	{
		$order_job_arr[$row[csf("id")]]['po_number']=$row[csf("po_number")];
		$order_job_arr[$row[csf("id")]]['buyer_name']=$row[csf("buyer_name")];
	}
	
	//echo "select transport_supplier from pro_ex_factory_delivery_mst where id=$data[1]";die;
	$delivery_mst_sql=sql_select("select id, transport_supplier, driver_name, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num from pro_ex_factory_delivery_mst where id=$data[1]");
	foreach($delivery_mst_sql as $row)
	{
		$supplier_name=$row[csf("transport_supplier")];
		$driver_name=$row[csf("driver_name")];
		$truck_no=$row[csf("truck_no")];
		$dl_no=$row[csf("dl_no")];
		$lock_no=$row[csf("lock_no")];
		$destination_place=$row[csf("destination_place")];
		$challan_no=$row[csf("challan_no")];
		$sys_number_prefix_num=$row[csf("sys_number_prefix_num")];
	}
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	
?>
<div style="width:710px;">
    <table width="700" cellspacing="0" align="right" style="margin-bottom:20px;">
        <tr>
            <td rowspan="2" align="center"><img src="../<?php echo $image_location; ?>" height="50" width="60"></td>
            <td colspan="5" align="center"  style="font-size:xx-large; "><strong><?php echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="5" align="center" style="font-size:14px;">  
				<?php
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
					?>
						<?php if($result[csf('plot_no')]!="") echo $result[csf('plot_no')].", "; ?> 
						<?php if($result[csf('level_no')]!="") echo $result[csf('level_no')].", ";?>
						<?php if($result[csf('road_no')]!="") echo $result[csf('road_no')].", "; ?> 
						<?php if($result[csf('block_no')]!="") echo $result[csf('block_no')].", ";?>
						<?php if($result[csf('city')]!="") echo $result[csf('city')].", ";?>
						<?php if($result[csf('zip_code')]!="") echo $result[csf('zip_code')].", "; ?> 
						<?php if($result[csf('province')]!="") echo $result[csf('province')];?> 
						<?php if($result[csf('country_id')]!=0) echo $country_arr[$result[csf('country_id')]].", "; ?><br> 
						<?php if($result[csf('email')]!="") echo $result[csf('email')].", ";?> 
						<?php if($result[csf('website')]!="") echo $result[csf('website')]; 
					}
                ?> 
            </td>  
        </tr>
        	<?php
				$supplier_sql=sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$supplier_name");
				foreach($supplier_sql as $row)
				{
				
				$address_1=$row[csf("address_1")];
				$address_2=$row[csf("address_2")];
				$address_3=$row[csf("address_3")];
				$address_4=$row[csf("address_4")];
				$contact_no=$row[csf("contact_no")];
				}
				//echo $supplier_sql;die;
            
            ?>
        <tr>
            <td colspan="5" style="font-size:x-large; padding-left:252px;"><strong>Delivery Challan<?php// echo $data[3]; ?></strong></td>
            <td style="font-size:12px;">Date : <?php echo change_date_format($data[2]); ?></td>
        </tr>
        <tr style="font-size:12px;">
        	<td width="80" valign="top"><strong>Name:</strong></td> 
            <td width="220" valign="top"><?php echo $supplier_library[$supplier_name]; ?></td>
            <td width="80" valign="top"><strong>Challan No :</strong></td>
            <td width="120" valign="top"><?php echo $challan_no; ?> </td>
            <td width="80" valign="top"><strong>DL/NO:</strong></td>
            <td valign="top"><?php echo $dl_no; ?> </td>
        </tr>
			
        <tr style="font-size:12px;">
            <td valign="top"><strong>Address:</strong></td>
            <td colspan="3" valign="top"><?php echo $address_1."<br>"; if($contact_no!="") echo "Phone : ".$contact_no; ?> </td>
            <td><strong>Truck No:</strong></td>
            <td ><?php echo $truck_no; ?> </td>
        </tr>
        <tr style="font-size:12px;">
            <td><strong>Destination :</strong></td>
            <td ><?php echo $destination_place; ?> </td>
            <td  valign="top"><strong >Driver Name :</strong></td>
            <td  valign="top"><?php echo $driver_name; ?> </td>
            <td><strong >Lock No :</strong></td>
            <td ><?php echo $lock_no; ?> </td>
        </tr>
    </table><br>
        <?php
		//listagg(CAST(b.po_breakdown_id as VARCHAR(4000)),',') within group (order by b.po_breakdown_id) as po_id
		if($db_type==2)
		{
			$sql="SELECT po_break_down_id, listagg(CAST(invoice_no as VARCHAR(4000)),',') within group (order by invoice_no) as invoice_no, sum(ex_factory_qnty) as ex_factory_qnty, sum(total_carton_qnty) as total_carton_qnty from pro_ex_factory_mst where delivery_mst_id=$data[1]  and status_active=1 and is_deleted=0 group by po_break_down_id";
		}
		else if($db_type==0)
		{
			$sql="SELECT po_break_down_id, group_concat(invoice_no) as invoice_no, sum(ex_factory_qnty) as ex_factory_qnty, sum(total_carton_qnty) as total_carton_qnty from pro_ex_factory_mst where delivery_mst_id=$data[1] and status_active=1 and is_deleted=0 group by po_break_down_id";
		}
		//echo $sql;die;
		$result=sql_select($sql);
			
		?> 
         
	<div style="width:700px;">
    <table align="right" cellspacing="0" width="700"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="50">SL</th>
            <th width="140" >Order No</th>
            <th width="140" >Buyer</th>
            <th width="280" >Invoice No</th>
            <th  >NO Of Carton</th>
        </thead>
        <tbody style="font-size:12px;">
		<?php
        $i=1;
        $tot_qnty=array();
        foreach($result as $row)
        {
            if ($i%2==0)  
                $bgcolor="#E9F3FF";
            else
                $bgcolor="#FFFFFF";
            $color_count=count($cid);
            ?>
            <tr bgcolor="<?php echo $bgcolor; ?>">
                <td><?php echo $i;  ?></td>
                <td><?php echo $order_job_arr[$row[csf("po_break_down_id")]]['po_number']; ?></td>
                <td><?php echo $buyer_library[$order_job_arr[$row[csf("po_break_down_id")]]['buyer_name']]; ?></td>
                <td>
				<?php
				 $invoice_id="";
				 $invoice_id_arr=array_unique(explode(",",$row[csf("invoice_no")]));
				 foreach($invoice_id_arr as $inv_id)
				 {
					 if($invoice_id=="") $invoice_id=$invoice_library[$inv_id]; else $invoice_id=$invoice_id.",".$invoice_library[$inv_id];
					 
				 }
				 echo $invoice_id;
				?></td>
                <td align="right"><?php echo number_format($row[csf("total_carton_qnty")],0,"",""); $tot_carton_qnty +=$row[csf("total_carton_qnty")]; ?></td>
            </tr>
            <?php
            $i++;
        }
        ?>
        </tbody>
        
        <tr>
            <td colspan="4" align="right"><strong>Grand Total :</strong></td>
            <td align="right"><?php echo number_format($tot_carton_qnty,0,"",""); ?></td>
        </tr>                           
    </table>
	</div>
		 <?php
            echo signature_table(63, $data[0], "700px");
         ?>
	</div>
<?php
exit();	
}

if($action=="gmts_delivery_print__1")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	echo load_html_head_contents("Garments Delivery Info","../", 1, 1, $unicode,'','');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$location_library=return_library_array( "select id,location_name from  lib_location", "id","location_name"  );
	//echo "select transport_supplier from pro_ex_factory_delivery_mst where id=$data[1]";die;
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
?>
<div style="width:710px;">
    <table width="700" cellspacing="0" align="right" style="margin-bottom:20px;">
        <tr>
            <td rowspan="2" align="center"><img src="../<?php echo $image_location; ?>" height="50" width="60"></td>
            <td colspan="5" align="center"  style="font-size:xx-large; "><strong><?php echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="5" align="center" style="font-size:14px;">  
				<?php
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
					?>
						<?php if($result[csf('plot_no')]!="") echo $result[csf('plot_no')].", "; ?> 
						<?php if($result[csf('level_no')]!="") echo $result[csf('level_no')].", ";?>
						<?php if($result[csf('road_no')]!="") echo $result[csf('road_no')].", "; ?> 
						<?php if($result[csf('block_no')]!="") echo $result[csf('block_no')].", ";?>
						<?php if($result[csf('city')]!="") echo $result[csf('city')].", ";?>
						<?php if($result[csf('zip_code')]!="") echo $result[csf('zip_code')].", "; ?> 
						<?php if($result[csf('province')]!="") echo $result[csf('province')];?> 
						<?php if($result[csf('country_id')]!=0) echo $country_arr[$result[csf('country_id')]].", "; ?><br> 
						<?php if($result[csf('email')]!="") echo $result[csf('email')].", ";?> 
						<?php if($result[csf('website')]!="") echo $result[csf('website')]; 
					}
                ?> 
            </td>  
        </tr>
        	<?php
				$supplier_sql=sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$supplier_name");
				foreach($supplier_sql as $row)
				{
				
				$address_1=$row[csf("address_1")];
				$address_2=$row[csf("address_2")];
				$address_3=$row[csf("address_3")];
				$address_4=$row[csf("address_4")];
				$contact_no=$row[csf("contact_no")];
				}
				//echo $supplier_sql;die;
            
            ?>
        <tr>
            <td colspan="5" style="font-size:x-large; padding-left:252px; text-align:center;"><strong><?php echo $data[2]; ?></strong></td>
            
        </tr>
        <?php 
		
		$sql_master ="select a.id,a.party_id,b.order_id,a.challan_no,b.item_id,a.delivery_date,a.transport_company,a.vehical_no,a.location_id,a.company_id,b.process_id,b.total_carton_qnty,b.delivery_qty  from  subcon_delivery_mst a,  subcon_delivery_dtls b where a.id=b.mst_id and  a.delivery_no='".$data[1]."' and a.status_active=1 and a.is_deleted=0 order by a.id";
		$result_master=sql_select($sql_master);
		$poID=$result_master[0][csf("order_id")];
		$sql_wo_po_break =sql_select("select a.id,a.po_number,a.job_no_mst from wo_po_break_down a where a.id=$poID");
		foreach($sql_wo_po_break as $poData)
		{
			$po_data_arr[$poData[csf("id")]]["po_number"]=$poData[csf("po_number")];
			$po_data_arr[$poData[csf("id")]]["job_no_mst"]=$poData[csf("job_no_mst")];
		}
		?>
        <tr style="font-size:12px;">
        	<td width="120" valign="top"><strong>Party Name:</strong></td> 
            <td width="220" valign="top"><?php echo $buyer_library[$result_master[0][csf("party_id")]]; ?></td>
            <td width="120" valign="top"><strong>Challan No :</strong></td>
            <td width="120" valign="top"><?php echo $result_master[0][csf("challan_no")]; ?> </td>
            <td width="120" valign="top"><strong>Delivery Date:</strong></td>
            <td valign="top" width="120"><?php echo change_date_format($result_master[0][csf("delivery_date")]); ?> </td>
        </tr>
			
        <tr style="font-size:12px;">
            <td valign="top" width="120"><strong>Delivery Company:</strong></td>
            <td valign="top"><?php echo $company_library[$result_master[0][csf("company_id")]]; ?> </td>
            <td><strong>Transport Com.</strong></td>
            <td ><?php echo $result_master[0][csf("transport_company")]; ?> </td>
            <td><strong>Vehicle No:</strong></td>
            <td ><?php echo $result_master[0][csf("vehical_no")]; ?> </td>
        </tr>
        <tr style="font-size:12px;">
            <td><strong>Delivery Location :</strong></td>
            <td ><?php echo $location_library[$result_master[0][csf("location_id")]]; ?> </td>
        </tr>
    </table><br>

	<div style="width:860px;">
    <table align="right" cellspacing="0" width="860"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="50">SL</th>
            <th width="140" >Item Name</th>
            <th width="140" >Job No</th>
            <th width="140" >Order No</th>
            <th width="140" >Process</th>
            <th width="140" >Total Carton Qty</th>
            <th >Delivery Qty</th>
        </thead>
        <tbody style="font-size:12px;">
		<?php
        $i=1;
        $tot_qnty=array();
        foreach($result_master as $row)
        {
            if ($i%2==0)  
                $bgcolor="#E9F3FF";
            else
                $bgcolor="#FFFFFF";
            $color_count=count($cid);
            ?>
            <tr bgcolor="<?php echo $bgcolor; ?>">
                <td><?php echo $i;  ?></td>
                <td><?php echo $garments_item[$row[csf("item_id")]]; ?></td>
                <td><?php echo $po_data_arr[$row[csf("order_id")]]["job_no_mst"]; ?></td>
                <td><?php echo $po_data_arr[$row[csf("order_id")]]["po_number"]; ?></td>
                <td align="center"><?php echo $production_process[$row[csf("process_id")]]; ?></td>
                <td align="right"><?php echo number_format($row[csf("total_carton_qnty")],0,"",""); $tot_carton_qnty +=$row[csf("total_carton_qnty")]; ?></td>
                <td align="right"><?php echo number_format($row[csf("delivery_qty")],0,"",""); $tot_del_qnty +=$row[csf("delivery_qty")]; ?></td>

            </tr>
            <?php
            $i++;
        }
        ?>
        </tbody>
        
        <tr>
            <td colspan="6" align="right"><strong>Grand Total :</strong></td>
            <td align="right"><?php echo number_format($tot_del_qnty,0,"",""); ?></td>
        </tr>                           
    </table>
	</div>
    	<div style="margin-left:-30px;">
		 <?php
            echo signature_table(63, $data[0], "860px","","10");
         ?>
         </div>
	</div>
<?php
exit();	
}



//pro_ex_factory_mst

//For testing Update Query
function sql_update_a($strTable, $arrUpdateFields, $arrUpdateValues, $arrRefFields, $arrRefValues, $commit) {
    $strQuery = "UPDATE " . $strTable . " SET ";
    $arrUpdateFields = explode("*", $arrUpdateFields);
    $arrUpdateValues = explode("*", $arrUpdateValues);
    if (is_array($arrUpdateFields)) {
        $arrayUpdate = array_combine($arrUpdateFields, $arrUpdateValues);
        $Arraysize = count($arrayUpdate);
        $i = 1;
        foreach ($arrayUpdate as $key => $value):
            $strQuery .= ($i != $Arraysize) ? $key . "=" . $value . ", " : $key . "=" . $value . " WHERE ";
            $i++;
        endforeach;
    }
    else {
        $strQuery .= $arrUpdateFields . "=" . $arrUpdateValues . " WHERE ";
    }
    $arrRefFields = explode("*", $arrRefFields);
    $arrRefValues = explode("*", $arrRefValues);
    if (is_array($arrRefFields)) {
        $arrayRef = array_combine($arrRefFields, $arrRefValues);
        $Arraysize = count($arrayRef);
        $i = 1;
        foreach ($arrayRef as $key => $value):
            $strQuery .= ($i != $Arraysize) ? $key . "=" . $value . " AND " : $key . "=" . $value . "";
            $i++;
        endforeach;
    }
    else {
        $strQuery .= $arrRefFields . "=" . $arrRefValues . "";
    }

    global $con;
    echo $strQuery;
    die;
    //return $strQuery; die;
    $stid = oci_parse($con, $strQuery);
    $exestd = oci_execute($stid, OCI_NO_AUTO_COMMIT);
    if ($exestd)
        return "1";
    else
        return "0";

    die;
    if ($commit == 1) {
        if (!oci_error($stid)) {
            oci_commit($con);
            return "1";
        } else {
            oci_rollback($con);
            return "10";
        }
    } else
        return 1;
    die;
}


?>