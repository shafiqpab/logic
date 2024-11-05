<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];
//========== start ========
if ($action=="system_popup")
{
	echo load_html_head_contents("Popup Info", "../../../", 1, 1,$unicode,1,'');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value( id )
		{
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
		function fn_show()
		{
			if(form_validation('cbo_company_id*cbo_type','Company Name*Type')==false)
            {
				return;
			}
			show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_transport_company_id').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_sys').value, 'create_system_search_list_view', 'search_div', 'transport_bill_entry_controller', 'setFilterGrid(\'search_div\',-1)');
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr>
                    <th colspan="7"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>
                    <th class="must_entry_caption">Company Name</th>
                    <th >Transport Com.</th>
                    <th class="must_entry_caption">Type</th>
                    <th >System ID</th>
                    <th colspan="2">Bill Date Range</th>
					<th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
					<input type="hidden" name="id_field" id="id_field" value="" /></th>
                </tr>        
            </thead>
            <tbody>
                <tr class="general">
                    <td class="must_entry_caption"> 
                        <input type="hidden" id="selected_id">
                        <? echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $cbo_company_name, '',1);?>
                    </td>
                    <td id="transfer_com" class="must_entry_caption"> 
                        <? echo create_drop_down( "cbo_transport_company_id", 150, "select a.id,a.supplier_name from  lib_supplier a, lib_supplier_tag_company b where a.status_active =1 and a.is_deleted=0 and b.supplier_id=a.id and b.tag_company='$cbo_company_name'  and a.id in (select  supplier_id  from  lib_supplier_party_type where party_type in (35)) order by supplier_name","id,supplier_name", 1, "-- Select Transport --", $selected, "" );?>
                    </td>
                    <td><? echo create_drop_down( "cbo_type",150,array(1=>"Export",2=>"Import"),'',1,'--Select--',0,"",0); ?></td>
                    <td><input name="txt_sys" id="txt_sys" class="text_boxes" style="width:100px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date"></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date"></td> 
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="fn_show()" style="width:100px;" /></td>
                </tr>
                <tr>
                    <td align="center" colspan="7"><? echo load_month_buttons(1); ?></td>
                </tr>
            </tbody>
        </table>
		<br>
        <div id="search_div"></div>
    </form>
    </div>
    </body>
      <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_system_search_list_view")
{
	//echo $data;die;
	$com_cond="";$date_cond ="";$year_cond="";$type_num="";$track_num="";
	list($company_id,$tran_com_id,$type_id,$bill_start_date, $bill_end_date,$year,$search_by,$search_sys ) = explode('_', $data);
	if ($company_id!=0) {$com_cond=" and company_id=$company_id";}
	if ($tran_com_id!=0) {$trans_com=" and trans_com_id=$tran_com_id";}
	if ($type_id!=0) {$type_num=" and type_id =$type_id";}
	if ($bill_start_date != '' && $bill_end_date != '') 
	{
        if ($db_type == 0) {
            $date_cond = "and bill_date '" . change_date_format($bill_start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($bill_end_date, 'yyyy-mm-dd') . "'";
        } else if ($db_type == 2) {
            $date_cond = "and bill_date between '" . change_date_format($bill_start_date, '', '', 1) . "' and '" . change_date_format($bill_end_date, '', '', 1) . "'";
		}
    } 
    else 
    {
        $date_cond = '';
		if($year!=""){
			if($db_type==0)
			{
				$year_cond=" and YEAR(bill_date) =$year ";
			}
			else
			{	
				$year_cond=" and to_char(bill_date,'YYYY') =$year ";
			}
		}
	}
	if ($search_sys != '')
	{
		if($search_string==1)
			{$search_text="and sys_number like '".trim($search_sys)."'";}
		else if ($search_string==2) 
			{$search_text="and sys_number like '".trim($search_sys)."%'";}
		else if ($search_string==3)
			{$search_text="and sys_number like '%".trim($search_sys)."'";}
		else if ($search_string==4 || $search_string==0)
			{$search_text="and sys_number like '%".trim($search_sys)."%'";}
	}

    $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $type=array(1=>"Export",2=>"Import");

    $arr=array(0=>$company_arr,1=>$supplier_arr,2=>$type);
	$sql= "select id, company_id, trans_com_id, type_id ,sys_number_prefix_num, bill_date from transport_bill_mst where status_active=1 and is_deleted=0 $com_cond $trans_com $type_num $date_cond $year_cond $search_text order by id DESC";
	// echo $sql;die;
	echo  create_list_view("search_div", "Company Name,Transport Com.,Type,System ID,Bill Date", "150,150,80,70,100","600","300",0, $sql , "js_set_value", "id", "", 1, "company_id,trans_com_id,type_id,0,0", $arr , "company_id,trans_com_id,type_id,sys_number_prefix_num,bill_date", "",'','0,0,0,0,3');
	exit();
}

if ($action=="populate_data_from_search_popup")
{
	$data_array="select id as ID,sys_number as SYS_NUMBER,READY_TO_APPROVE, company_id as COMPANY_ID,location_id as LOCATION_ID,trans_com_id as TRANS_COM_ID,bill_date as BILL_DATE,bill_no as BILL_NO,type_id as TYPE_ID,ship_mode as SHIP_MODE,depo as DEPO,port_name as PORT_NAME,remarks as REMARKS 
    from transport_bill_mst 
    where status_active=1 and is_deleted=0 and id='$data'  order by id DESC";
     //echo $data_array;die;
    $data_result=sql_select($data_array);
    echo "document.getElementById('txt_system_id').value = '".$data_result[0]["SYS_NUMBER"]."';\n";  
    echo "document.getElementById('cbo_company_name').value = '".$data_result[0]["COMPANY_ID"]."';\n";  
    echo "document.getElementById('cbo_location_name').value = '".$data_result[0]["LOCATION_ID"]."';\n";  
    echo "document.getElementById('cbo_approve_status').value = '".$data_result[0]["READY_TO_APPROVE"]."';\n";  
    echo "document.getElementById('cbo_transport_company').value = '".$data_result[0]["TRANS_COM_ID"]."';\n";  
    echo "document.getElementById('txt_bill_date').value = '".change_date_format($data_result[0]["BILL_DATE"])."';\n";  
    echo "document.getElementById('txt_bill_no').value = '".$data_result[0]["BILL_NO"]."';\n";
    echo "document.getElementById('cbo_type_name').value = '".$data_result[0]["TYPE_ID"]."';\n";
    echo "document.getElementById('cbo_shipment_id').value = '".$data_result[0]["SHIP_MODE"]."';\n";  
    echo "document.getElementById('txt_depo').value = '".$data_result[0]["DEPO"]."';\n"; 
    echo "document.getElementById('txt_port').value = '".$data_result[0]["PORT_NAME"]."';\n"; 
    echo "document.getElementById('txt_remarks').value = '".$data_result[0]["REMARKS"]."';\n";  
    
    echo "document.getElementById('update_id').value = '".$data_result[0]["ID"]."';\n"; 
	exit();
}

if ($action=="populate_dtls_data_from_search_popup")
{
    list($mst_id,$type) = explode('**', $data);
    if($type==1){
        $data_sql="SELECT a.id as ID, a.challan_no as CHALLAN_NO, a.challan_btb_id as CHALLAN_BTB_ID, a.invoice_id as INVOICE_ID, a.buyer_supp_id as BUYER_SUPP_ID, a.cbm_amt as CBM_AMT, a.vechicale_no as VECHICALE_NO, a.no_vechicale as NO_VECHICALE, a.vechicale_rent as VECHICALE_RENT, a.point_unloading as POINT_UNLOADING, a.load_extra_unload as LOAD_EXTRA_UNLOAD, a.local_vechicle_rent as LOCAL_VECHICLE_RENT, a.demurrage_other as DEMURRAGE_OTHER,a.amount  as AMOUNT,sum(b.total_carton_qnty) as TOTAL_CARTON_QNTY, sum(b.ex_factory_qnty) as EX_FACTORY_QNTY, a.OTHER_CHARGE, a.DEDUCTION, a.PAYABLE
        from transport_bill_dtls a, pro_ex_factory_mst b
        where a.mst_id='$mst_id' and b.delivery_mst_id=a.challan_btb_id and a.status_active=1 and b.status_active=1
        group by a.id, a.challan_no, a.challan_btb_id, a.invoice_id, a.buyer_supp_id, a.cbm_amt, a.vechicale_no, a.no_vechicale,a.vechicale_rent, a.point_unloading, a.load_extra_unload, a.local_vechicle_rent, a.demurrage_other,a.amount, a.other_charge, a.deduction, a.payable
        order by a.id DESC";
        // echo $data_sql;
        $data_result=sql_select($data_sql);

        $all_inv_id='';
        foreach($data_result as $row)
        {
            if($row["INVOICE_ID"]){$all_inv_id.=$row["INVOICE_ID"].',';}
        }

        if($all_inv_id)
        {
            $all_inv_id=rtrim($all_inv_id,',');
            $sqlEx = sql_select("SELECT a.id as ID, a.invoice_no, b.delivery_mst_id from com_export_invoice_ship_mst a, pro_ex_factory_mst b  where a.id in($all_inv_id) and a.id=b.invoice_no and  a.status_active=1 and b.status_active=1");
            $invoice_no=array();
            foreach($sqlEx as $row)
            {
                $invoice_no[$row["DELIVERY_MST_ID"]].=$row["INVOICE_NO"].', ';
            }
        }
        $i=1;
        ?>
        <div style="width:1690px;">
            <table cellspacing="0" width="100%" class="rpt_table" id="tbl_details" >
                <thead>
                    <tr>
                        <th>Delivery Challan No</th>
                        <th>Shipment Qty</th>
                        <th>Carton Qty</th>
                        <th>Export Invoice No</th>
                        <th>Buyer Name</th>
                        <th>CBM</th>
                        <th>Vechicle No</th>
                        <th>No of Vechicle</th>
                        <th>Vehicle Rent (Tk)</th>
                        <th>Two Point Unloading Charge</th>
                        <th>Loading Unloading Charge</th>
                        <th>Local Vechicle Rent</th>
                        <th>Demurrage Charge</th>
                        <th>Other Charge</th>
                        <th>Total Amount TK</th>
                        <th>Total Deduction</th>
                        <th>Payable Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
        <?
        foreach($data_result as $row)
        {
            ?>
            <tr class="general" id="<? echo $i;?>">
                <td> 
                    <input type="text" name="txt_challan_no[]" id="txt_challan_no_<? echo $i;?>" class="text_boxes" style="width:100px" onDblClick="numberPopup(<? echo $i;?>)" placeholder="Double Click To Search" value="<? echo $row['CHALLAN_NO'];?>" readonly/>
                    <input type="hidden" name="txt_challan_no_id[]" id="txt_challan_no_id_<? echo $i;?>" value="<? echo $row['CHALLAN_BTB_ID'];?>" />
                </td>
                <td> 
                    <input type="text" name="txt_shipment_qty[]" id="txt_shipment_qty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" readonly value="<? echo $row['EX_FACTORY_QNTY'];?>"/>
                </td>
                <td> 
                    <input type="text" name="txt_carton_qty[]" id="txt_carton_qty_<? echo $i;?>" class="text_boxes_numeric" style="width:70px" readonly value="<? echo $row['TOTAL_CARTON_QNTY'];?>"/>
                </td>
                <td> 
                    <input type="text" name="txt_invoice_no[]" id="txt_invoice_no_<? echo $i;?>" class="text_boxes" style="width:80px" readonly title="<? echo rtrim($invoice_no[$row['CHALLAN_BTB_ID']],', ') ;?>" value="<? echo rtrim($invoice_no[$row['CHALLAN_BTB_ID']],', ') ;?>"/>
                    <input type="hidden" name="txt_invoice_id[]" id="txt_invoice_id_<? echo $i;?>" value="<? echo $row['INVOICE_ID'] ;?>"/>
                </td>
                <td> 
                    <?
                    echo create_drop_down( "cbo_buyer_".$i, 100, "select id,buyer_name from lib_buyer comp where status_active =1 and is_deleted=0 $buyer_cond","id,buyer_name", 1, " Display ", $row['BUYER_SUPP_ID'], "",1,"","","","","","","cbo_buyer[]","cbo_buyer_".$i);
                    ?>
                </td>
                <td> 
                    <input type="text" name="txt_cbm[]" id="txt_cbm_<? echo $i;?>" class="text_boxes_numeric" style="width:70px" value="<? echo $row['CBM_AMT'];?>" />
                </td>
                <td> 
                    <input type="text" name="txt_vehicle_no[]" id="txt_vehicle_no_<? echo $i;?>" class="text_boxes" style="width:80px" readonly value="<? echo $row['VECHICALE_NO'];?>"/>
                </td>
                <td> 
                    <input type="text" name="txt_no_vehicle[]" id="txt_no_vehicle_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row['NO_VECHICALE'];?>"/>
                </td>
                <td> 
                    <input type="text" name="txt_vehicle_rent[]" id="txt_vehicle_rent_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)" value="<? echo $row['VECHICALE_RENT'];?>"/>
                </td>
                <td> 
                    <input type="text" name="txt_point_unloading[]" id="txt_point_unloading_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)" value="<? echo $row['POINT_UNLOADING'];?>"/>
                </td>
                <td> 
                    <input type="text" name="txt_loading_unloading[]" id="txt_loading_unloading_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)" value="<? echo $row['LOAD_EXTRA_UNLOAD'];?>"/>
                </td>
                <td> 
                    <input type="text" name="txt_local_vechicle[]" id="txt_local_vechicle_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)" value="<? echo $row['LOCAL_VECHICLE_RENT'];?>"/>
                </td>
                <td> 
                    <input type="text" name="txt_demurrage[]" id="txt_demurrage_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)" value="<? echo $row['DEMURRAGE_OTHER'];?>" />
                </td>
                <td> 
                    <input type="text" name="txt_other[]" id="txt_other_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)"  value="<? echo $row['OTHER_CHARGE'];?>" />
                </td>
                <td> 
                    <input type="text" name="txt_amount[]" id="txt_amount_<? echo $i;?>" class="text_boxes_numeric" style="width:80px"  readonly  value="<? echo $row['AMOUNT'];?>" />
                </td>
                <td> 
                    <input type="text" name="txt_deduction[]" id="txt_deduction_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_total_payable(<?=$i;?>)" value="<? echo $row['DEDUCTION'];?>" />
                </td>
                <td> 
                    <input type="text" name="txt_payable[]" id="txt_payable_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" readonly  value="<? echo $row['PAYABLE'];?>" />
                </td>
                <td width="100">
                    <input type="button" id="increaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'increase');" />
                    <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'decrease');" />
                </td>
             </tr>
            <?
            $i++;
        }
        ?>
        </tbody>
                <tfoot class="tbl_bottom">
                    <tr>
                        <td colspan="2"><strong>Total Amount</strong>&nbsp;&nbsp;</td>
                        <td>
                            <input type="text" name="txt_crtn_qnt" id="txt_crtn_qnt" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                        </td>
                        <td colspan="5">&nbsp;</td>
                        <td>
                        <input type="text" name="txt_rent_amount" id="txt_rent_amount" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                        </td>
                        <td>
                        <input type="text" name="txt_point_amount" id="txt_point_amount" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                        </td>
                        <td>
                        <input type="text" name="txt_loading_amount" id="txt_loading_amount" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                        </td>
                        <td>
                        <input type="text" name="txt_local_amount" id="txt_local_amount" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                        </td>
                        <td>
                            <input type="text" name="txt_demurrage_amount" id="txt_demurrage_amount" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                        </td>
                        <td>
                            <input type="text" name="txt_other_amount" id="txt_other_amount" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                        </td>
                        <td>
                            <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                        </td>
                        <td>
                            <input type="text" name="txt_deduction_amount" id="txt_deduction_amount" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                        </td>
                        <td>
                            <input type="text" name="txt_payable_amount" id="txt_payable_amount" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                        <td>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?
    }
    if($type==2){
        $data_sql="SELECT a.id as ID, a.challan_no as CHALLAN_NO, a.challan_btb_id as CHALLAN_BTB_ID,a.buyer_supp_id as BUYER_SUPP_ID, a.vechicale_no as VECHICALE_NO, a.no_vechicale as NO_VECHICALE,a.qty as QTY, a.cbm_amt as CBM_AMT,a.vechicale_rent as VECHICALE_RENT, a.load_extra_unload as LOAD_EXTRA_UNLOAD, a.local_vechicle_rent as LOCAL_VECHICLE_RENT, a.demurrage_other as DEMURRAGE_OTHER,a.amount  as AMOUNT,b.lc_number as LC_NUMBER, a.DEDUCTION, a.PAYABLE
        from transport_bill_dtls a, com_btb_lc_master_details b
        where a.status_active=1 and a.is_deleted=0 and a.mst_id='$mst_id' and b.id=a.challan_btb_id 
        group by a.id, a.challan_no, a.challan_btb_id,a.buyer_supp_id, a.vechicale_no, a.no_vechicale ,a.qty, a.cbm_amt,a.vechicale_rent, a.load_extra_unload, a.local_vechicle_rent, a.demurrage_other,a.amount,b.lc_number, a.deduction, a.payable
        order by a.id DESC";
        // echo $data_sql;
        $data_result=sql_select($data_sql);

        $i=1;
        ?>
        <div  style="width:1410px;">
            <table cellspacing="0" width="100%" class="rpt_table" id="tbl_details" >
                <thead>
                    <tr>
                        <th>BTB LC Number</th>
                        <th>Supplier Name</th>
                        <th>Challan No</th>
                        <th>Qty</th>
                        <th>CBM</th>
                        <th>Vechicle No</th>
                        <th>No of Vechicle</th>
                        <th>Vehicle Rent (Tk)</th>
                        <th>Extra Loading Bill TK</th>
                        <th>CTG Port to Godown Local TK</th>
                        <th>Others Amount TK</th>
                        <th>Total Amount TK</th>
                        <th>Total Deduction</th>
                        <th>Payable Amount</th>
                        <th width='100'>Action</th>
                    </tr>
                </thead>
                <tbody>
        <?
        foreach($data_result as $row)
        {
            ?>
            <tr class="general" id="<? echo $i;?>">
                <td>
                    <input type="text" name="txt_btb_lc[]" id="txt_btb_lc_<? echo $i;?>" class="text_boxes" style="width:100px" onDblClick="numberPopup(<? echo $i;?>)" placeholder="Double Click To Search" readonly value="<? echo $row['LC_NUMBER'];?>"/>
                    <input type="hidden" name="txt_btb_lc_id[]" id="txt_btb_lc_id_<? echo $i;?>" value="<? echo $row['CHALLAN_BTB_ID'];?>"/>
                </td>
                <td>
                    <?
                    echo create_drop_down( "cbo_supp_id_".$i, 100, "select id,supplier_name from  lib_supplier where status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, " Display ", $row['BUYER_SUPP_ID'], "",1,"","","","","","","cbo_supp_id[]","cbo_supp_id_".$i);
                    ?>
                </td>
                <td>
                    <input type="text" name="txt_challan_no[]" id="txt_challan_no_<? echo $i;?>" class="text_boxes" style="width:80px" value="<? echo $row['CHALLAN_NO'];?>"/>
                </td>
                <td>
                    <input type="text" name="txt_qty[]" id="txt_qty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row['QTY'];?>"/>
                </td>
                <td> 
                    <input type="text" name="txt_cbm[]" id="txt_cbm_<? echo $i;?>" class="text_boxes_numeric" style="width:70px" value="<? echo $row['CBM_AMT'];?>" />
                </td>
                <td>
                    <input type="text" name="txt_vehicle_no[]" id="txt_vehicle_no_<? echo $i;?>" class="text_boxes" style="width:80px" value="<? echo $row['VECHICALE_NO'];?>"/>
                </td>
                <td> 
                    <input type="text" name="txt_no_vehicle[]" id="txt_no_vehicle_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row['NO_VECHICALE'];?>"/>
                </td>
                <td>
                    <input type="text" name="txt_vehicle_rent[]" id="txt_vehicle_rent_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)" value="<? echo $row['VECHICALE_RENT'];?>"/>
                </td>
                <td>
                    <input type="text" name="txt_loading_unloading[]" id="txt_loading_unloading_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)" value="<? echo $row['LOAD_EXTRA_UNLOAD'];?>"/>
                </td>
                <td>
                    <input type="text" name="txt_local_vechicle[]" id="txt_local_vechicle_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)" value="<? echo $row['LOCAL_VECHICLE_RENT'];?>"/>
                </td>
                <td>
                    <input type="text" name="txt_other[]" id="txt_other_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)" value="<? echo $row['DEMURRAGE_OTHER'];?>"/>
                </td>
                <td>
                    <input type="text" name="txt_amount[]" id="txt_amount_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_total_amount()" readonly value="<? echo $row['AMOUNT'];?>" />
                </td>
                <td> 
                    <input type="text" name="txt_deduction[]" id="txt_deduction_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_total_payable(<?=$i;?>)"  value="<? echo $row['DEDUCTION'];?>"/>
                </td>
                <td> 
                    <input type="text" name="txt_payable[]" id="txt_payable_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" readonly value="<? echo $row['PAYABLE'];?>" />
                </td>
                <td width="100">
                    <input type="button" id="increaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'increase');" />
                    <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'decrease');" />
                </td>
            </tr>
            <?
            $i++;
        }
        ?>
        </tbody>
                <tfoot class="tbl_bottom">
                    <tr>
                        <td colspan="7"><strong>Total Amount</strong>&nbsp;&nbsp;</td>
                        <td style="text-align:center">
                        <input type="text" name="txt_rent_amount" id="txt_rent_amount" class="text_boxes_numeric" style="width:80px;" readonly/>
                        </td>
                        <td style="text-align:center">
                        <input type="text" name="txt_loading_amount" id="txt_loading_amount" class="text_boxes_numeric" style="width:80px;" readonly/>
                        </td>
                        <td style="text-align:center">
                        <input type="text" name="txt_local_amount" id="txt_local_amount" class="text_boxes_numeric" style="width:80px;" readonly/>
                        </td>
                        <td style="text-align:center">
                        <input type="text" name="txt_other_amount" id="txt_other_amount" class="text_boxes_numeric" style="width:80px;" readonly/>
                        </td>
                        <td style="text-align:center">
                        <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" style="width:80px;" readonly/>
                        </td>
                        <td>
                            <input type="text" name="txt_deduction_amount" id="txt_deduction_amount" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                        </td>
                        <td>
                            <input type="text" name="txt_payable_amount" id="txt_payable_amount" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?
    }

	exit();
}

if ($action=="load_drop_down_location")
{
    echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "");
    exit();
}

if ($action=="load_drop_down_transport_com")
{
	echo create_drop_down( "cbo_transport_company", 150, "select a.id,a.supplier_name from  lib_supplier a, lib_supplier_tag_company b where a.status_active =1 and a.is_deleted=0 and b.supplier_id=a.id and b.tag_company='$data'  and a.id in (select  supplier_id  from  lib_supplier_party_type where party_type in (35)) order by supplier_name","id,supplier_name", 1, "-- Select Transport --", $selected, "" );
	exit();
}
if ($action=="load_bill_tbl")
{
	if($data==1) //Export
    {
        $i=1;
        ?>
        <div style="width:1680px;">
            <table cellspacing="0" width="100%" class="rpt_table" id="tbl_details" >
                <thead>
                    <tr>
                        <th>Delivery Challan No</th>
                        <th>Shipment Qty</th>
                        <th>Carton Qty</th>
                        <th>Export Invoice No</th>
                        <th>Buyer Name</th>
                        <th>CBM</th>
                        <th>Vechicle No</th>
                        <th>No of Vechicle</th>
                        <th>Vehicle Rent (Tk)</th>
                        <th>Two Point Unloading Charge</th>
                        <th>Loading Unloading Charge</th>
                        <th>Local Vechicle Rent</th>
                        <th>Demurrage Charge</th>
                        <th>Other Charge</th>
                        <th>Total Amount TK</th>
                        <th>Total Deduction</th>
                        <th>Payable Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="general" id="<? echo $i;?>">
                        <td> 
                            <input type="text" name="txt_challan_no[]" id="txt_challan_no_<? echo $i;?>" class="text_boxes" style="width:100px" onDblClick="numberPopup(<? echo $i;?>)" placeholder="Double Click To Search" readonly/>
                            <input type="hidden" name="txt_challan_no_id[]" id="txt_challan_no_id_<? echo $i;?>" />
                        </td>
                        <td> 
                            <input type="text" name="txt_shipment_qty[]" id="txt_shipment_qty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" readonly/>
                        </td>
                        <td> 
                            <input type="text" name="txt_carton_qty[]" id="txt_carton_qty_<? echo $i;?>" class="text_boxes_numeric" style="width:70px" readonly/>
                        </td>
                        <td> 
                            <input type="text" name="txt_invoice_no[]" id="txt_invoice_no_<? echo $i;?>" class="text_boxes" style="width:80px" readonly/>
                            <input type="hidden" name="txt_invoice_id[]" id="txt_invoice_id_<? echo $i;?>" />
                        </td>
                        <td> 
                            <?
                            echo create_drop_down( "cbo_buyer_".$i, 100, "select id,buyer_name from lib_buyer comp where status_active =1 and is_deleted=0 $buyer_cond","id,buyer_name", 1, " Display ", 0, "",1,"","","","","","","cbo_buyer[]","cbo_buyer_".$i);
                            ?>
                        </td>
                        <td> 
                            <input type="text" name="txt_cbm[]" id="txt_cbm_<? echo $i;?>" class="text_boxes_numeric" style="width:70px" />
                        </td>
                        <td> 
                            <input type="text" name="txt_vehicle_no[]" id="txt_vehicle_no_<? echo $i;?>" class="text_boxes" style="width:80px" readonly/>
                        </td>
                        <td> 
                            <input type="text" name="txt_no_vehicle[]" id="txt_no_vehicle_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" />
                        </td>
                        <td> 
                            <input type="text" name="txt_vehicle_rent[]" id="txt_vehicle_rent_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)"/>
                        </td>
                        <td> 
                            <input type="text" name="txt_point_unloading[]" id="txt_point_unloading_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)"/>
                        </td>
                        <td> 
                            <input type="text" name="txt_loading_unloading[]" id="txt_loading_unloading_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)"/>
                        </td>
                        <td> 
                            <input type="text" name="txt_local_vechicle[]" id="txt_local_vechicle_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)"/>
                        </td>
                        <td> 
                            <input type="text" name="txt_demurrage[]" id="txt_demurrage_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)" />
                        </td>
                        <td> 
                            <input type="text" name="txt_other[]" id="txt_other_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)" />
                        </td>
                        <td> 
                            <input type="text" name="txt_amount[]" id="txt_amount_<? echo $i;?>" class="text_boxes_numeric" style="width:80px"  readonly />
                        </td>
                        <td> 
                            <input type="text" name="txt_deduction[]" id="txt_deduction_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_total_payable(<?=$i;?>)" />
                        </td>
                        <td> 
                            <input type="text" name="txt_payable[]" id="txt_payable_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" readonly />
                        </td>
                        <td width="100">
                            <input type="button" id="increaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'increase');" />
                            <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'decrease');" />
                        </td>
                    </tr>
                </tbody>
                <tfoot class="tbl_bottom">
                    <tr>
                        <td colspan="2"><strong>Total Amount</strong>&nbsp;&nbsp;</td>
                        <td>
                            <input type="text" name="txt_crtn_qnt" id="txt_crtn_qnt" class="text_boxes_numeric" value="" style="width:70px;" readonly/>
                        </td>
                        <td colspan="5">&nbsp;</td>
                        <td>
                            <input type="text" name="txt_rent_amount" id="txt_rent_amount" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                        </td>
                        <td>
                            <input type="text" name="txt_point_amount" id="txt_point_amount" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                        </td>
                        <td>
                            <input type="text" name="txt_loading_amount" id="txt_loading_amount" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                        </td>
                        <td>
                            <input type="text" name="txt_local_amount" id="txt_local_amount" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                        </td>
                        <td>
                            <input type="text" name="txt_demurrage_amount" id="txt_demurrage_amount" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                        </td>
                        <td>
                            <input type="text" name="txt_other_amount" id="txt_other_amount" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                        </td>
                        <td>
                            <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                        </td>
                        <td>
                            <input type="text" name="txt_deduction_amount" id="txt_deduction_amount" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                        </td>
                        <td>
                            <input type="text" name="txt_payable_amount" id="txt_payable_amount" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
        <?
    }
	if($data==2) //Import
    {
        $i=1;
        ?>
        <div  style="width:1410px;">
            <table cellspacing="0" width="100%" class="rpt_table" id="tbl_details" >
                <thead>
                    <tr>
                        <th>BTB LC Number</th>
                        <th>Supplier Name</th>
                        <th>Challan No</th>
                        <th>Qty</th>
                        <th>CBM</th>
                        <th>Vechicle No</th>
                        <th>No of Vechicle</th>
                        <th>Vehicle Rent (Tk)</th>
                        <th>Extra Loading Bill TK</th>
                        <th>CTG Port to Godown Local TK</th>
                        <th>Others Amount TK</th>
                        <th>Total Amount TK</th>
                        <th>Total Deduction</th>
                        <th>Payable Amount</th>
                        <th width='100'>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="general" id="<? echo $i;?>">
                        <td>
                            <input type="text" name="txt_btb_lc[]" id="txt_btb_lc_<? echo $i;?>" class="text_boxes" style="width:100px" onDblClick="numberPopup(<? echo $i;?>)" placeholder="Double Click To Search" readonly/>
                            <input type="hidden" name="txt_btb_lc_id[]" id="txt_btb_lc_id_<? echo $i;?>" />
                        </td>
                        <td>
                            <?
                            echo create_drop_down( "cbo_supp_id_".$i, 100, "select id,supplier_name from  lib_supplier where status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, " Display ", 0, "",1,"","","","","","","cbo_supp_id[]","cbo_supp_id_".$i);
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_challan_no[]" id="txt_challan_no_<? echo $i;?>" class="text_boxes" style="width:80px"/>
                        </td>
                        <td>
                            <input type="text" name="txt_qty[]" id="txt_qty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px"/>
                        </td>
                        <td> 
                            <input type="text" name="txt_cbm[]" id="txt_cbm_<? echo $i;?>" class="text_boxes_numeric" style="width:70px" />
                        </td>
                        <td>
                            <input type="text" name="txt_vehicle_no[]" id="txt_vehicle_no_<? echo $i;?>" class="text_boxes" style="width:80px"/>
                        </td>
                        <td> 
                            <input type="text" name="txt_no_vehicle[]" id="txt_no_vehicle_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" />
                        </td>
                        <td>
                            <input type="text" name="txt_vehicle_rent[]" id="txt_vehicle_rent_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)"/>
                        </td>
                        <td>
                            <input type="text" name="txt_loading_unloading[]" id="txt_loading_unloading_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)"/>
                        </td>
                        <td>
                            <input type="text" name="txt_local_vechicle[]" id="txt_local_vechicle_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)"/>
                        </td>
                        <td>
                            <input type="text" name="txt_other[]" id="txt_other_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)"/>
                        </td>
                        <td>
                            <input type="text" name="txt_amount[]" id="txt_amount_<? echo $i;?>" class="text_boxes_numeric" style="width:80px"  readonly />
                        </td>
                        <td> 
                            <input type="text" name="txt_deduction[]" id="txt_deduction_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_total_payable(<?=$i;?>)" />
                        </td>
                        <td> 
                            <input type="text" name="txt_payable[]" id="txt_payable_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" readonly />
                        </td>
                        <td width="100">
                            <input type="button" id="increaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'increase');" />
                            <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'decrease');" />
                        </td>
                    </tr>
                </tbody>
                <tfoot class="tbl_bottom">
                    <tr>
                        <td colspan="7"><strong>Total Amount</strong>&nbsp;&nbsp;</td>
                        <td style="text-align:center">
                            <input type="text" name="txt_rent_amount" id="txt_rent_amount" class="text_boxes_numeric" style="width:80px;" readonly/>
                        </td>
                        <td style="text-align:center">
                            <input type="text" name="txt_loading_amount" id="txt_loading_amount" class="text_boxes_numeric" style="width:80px;" readonly/>
                        </td>
                        <td style="text-align:center">
                            <input type="text" name="txt_local_amount" id="txt_local_amount" class="text_boxes_numeric" style="width:80px;" readonly/>
                        </td>
                        <td style="text-align:center">
                            <input type="text" name="txt_other_amount" id="txt_other_amount" class="text_boxes_numeric" style="width:80px;" readonly/>
                        </td>
                        <td style="text-align:center">
                            <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" style="width:80px;" readonly/>
                        </td>
                        <td>
                            <input type="text" name="txt_deduction_amount" id="txt_deduction_amount" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                        </td>
                        <td>
                            <input type="text" name="txt_payable_amount" id="txt_payable_amount" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
        <?
    }
	exit();
}

if ($action=="append_load_details_container")
{   //Transport details append table row

    $explodeData = explode("**",$data);
    $i = $explodeData[0];
    $type = $explodeData[1]; 

    if($type==1)
    {
        ?>
        <tr class="general" id="<? echo $i;?>">
            <td> 
                <input type="text" name="txt_challan_no[]" id="txt_challan_no_<? echo $i;?>" class="text_boxes" style="width:100px" onDblClick="numberPopup(<? echo $i;?>)" placeholder="Double Click To Search" readonly/>
                <input type="hidden" name="txt_challan_no_id[]" id="txt_challan_no_id_<? echo $i;?>" />
            </td>
            <td> 
                <input type="text" name="txt_shipment_qty[]" id="txt_shipment_qty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" readonly/>
            </td>
            <td> 
                <input type="text" name="txt_carton_qty[]" id="txt_carton_qty_<? echo $i;?>" class="text_boxes_numeric" style="width:70px" readonly/>
            </td>
            <td> 
                <input type="text" name="txt_invoice_no[]" id="txt_invoice_no_<? echo $i;?>" class="text_boxes" style="width:80px" readonly/>
                <input type="hidden" name="txt_invoice_id[]" id="txt_invoice_id_<? echo $i;?>" />
            </td>
            <td> 
                <?
                echo create_drop_down( "cbo_buyer_".$i, 100, "select id,buyer_name from lib_buyer comp where status_active =1 and is_deleted=0 $buyer_cond","id,buyer_name", 1, " Display ", 0, "",1,"","","","","","","cbo_buyer[]","cbo_buyer_".$i);
                ?>
            </td>
            <td> 
                <input type="text" name="txt_cbm[]" id="txt_cbm_<? echo $i;?>" class="text_boxes_numeric" style="width:70px" />
            </td>
            <td> 
                <input type="text" name="txt_vehicle_no[]" id="txt_vehicle_no_<? echo $i;?>" class="text_boxes" style="width:80px" readonly/>
            </td>
            <td> 
                <input type="text" name="txt_no_vehicle[]" id="txt_no_vehicle_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" />
            </td>
            <td> 
                <input type="text" name="txt_vehicle_rent[]" id="txt_vehicle_rent_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)"/>
            </td>
            <td> 
                <input type="text" name="txt_point_unloading[]" id="txt_point_unloading_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)"/>
            </td>
            <td> 
                <input type="text" name="txt_loading_unloading[]" id="txt_loading_unloading_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)"/>
            </td>
            <td> 
                <input type="text" name="txt_local_vechicle[]" id="txt_local_vechicle_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)"/>
            </td>
            <td> 
                <input type="text" name="txt_demurrage[]" id="txt_demurrage_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)" />
            </td>
            <td> 
                <input type="text" name="txt_other[]" id="txt_other_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)" />
            </td>
            <td> 
                <input type="text" name="txt_amount[]" id="txt_amount_<? echo $i;?>" class="text_boxes_numeric" style="width:80px"  readonly />
            </td>
            <td> 
                <input type="text" name="txt_deduction[]" id="txt_deduction_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_total_payable(<?=$i;?>)" />
            </td>
            <td> 
                <input type="text" name="txt_payable[]" id="txt_payable_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" readonly />
            </td>
            <td width="100">
                <input type="button" id="increaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'increase');" />
                <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'decrease');" />
            </td>
        </tr>
        <?
    }
    if($type==2)
    {
        ?>
         <tr class="general" id="<? echo $i;?>">
            <td>
                <input type="text" name="txt_btb_lc[]" id="txt_btb_lc_<? echo $i;?>" class="text_boxes" style="width:100px" onDblClick="numberPopup(<? echo $i;?>)" placeholder="Double Click To Search" readonly/>
                <input type="hidden" name="txt_btb_lc_id[]" id="txt_btb_lc_id_<? echo $i;?>" />
            </td>
            <td>
                <?
                echo create_drop_down( "cbo_supp_id_".$i, 100, "select id,supplier_name from  lib_supplier where status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, " Display ", 0, "",1,"","","","","","","cbo_supp_id[]","cbo_supp_id_".$i);
                ?>
            </td>
            <td>
                <input type="text" name="txt_challan_no[]" id="txt_challan_no_<? echo $i;?>" class="text_boxes" style="width:80px"/>
            </td>
            <td>
                <input type="text" name="txt_qty[]" id="txt_qty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px"/>
            </td>
            <td> 
                <input type="text" name="txt_cbm[]" id="txt_cbm_<? echo $i;?>" class="text_boxes" style="width:70px" readonly/>
            </td>
            <td>
                <input type="text" name="txt_vehicle_no[]" id="txt_vehicle_no_<? echo $i;?>" class="text_boxes" style="width:80px"/>
            </td>
            <td> 
                <input type="text" name="txt_no_vehicle[]" id="txt_no_vehicle_<? echo $i;?>" class="text_boxes" style="width:80px" />
            </td>
            <td>
                <input type="text" name="txt_vehicle_rent[]" id="txt_vehicle_rent_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)"/>
            </td>
            <td>
                <input type="text" name="txt_loading_unloading[]" id="txt_loading_unloading_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)"/>
            </td>
            <td>
                <input type="text" name="txt_local_vechicle[]" id="txt_local_vechicle_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)"/>
            </td>
            <td>
                <input type="text" name="txt_other[]" id="txt_other_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_row_amount(<? echo $i;?>)"/>
            </td>
            <td>
                <input type="text" name="txt_amount[]" id="txt_amount_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" readonly />
            </td>
            <td> 
                <input type="text" name="txt_deduction[]" id="txt_deduction_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_total_payable(<?=$i;?>)" />
            </td>
            <td> 
                <input type="text" name="txt_payable[]" id="txt_payable_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" readonly />
            </td>
            <td width="100">
                <input type="button" id="increaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'increase');" />
                <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'decrease');" />
            </td>
        </tr>
        <?
    }
    exit();
}

if ($action=="challan_popup")
{
	echo load_html_head_contents("Challan Info", "../../../", 1, 1,$unicode,1,'');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value( id )
		{
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
		function fn_show()
		{
			if(form_validation('cbo_company_id*cbo_transport_company_id','Company Name*Transport Com')==false){
					return;
				}
			show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_transport_company_id').value+'_'+document.getElementById('txt_truck').value+'_'+document.getElementById('txt_challan').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value, 'challan_search_list_view', 'search_div', 'transport_bill_entry_controller', 'setFilterGrid(\'search_div\',-1)');
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="890" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr>
                    <th class="must_entry_caption">Company Name</th>
                    <th class="must_entry_caption">Transport Com.</th>
                    <th>Truck No</th>
                    <th>Delivery Challan No</th>
                    <th colspan="2">Ex-Factory Date Range</th>
					<th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
					<input type="hidden" name="id_field" id="id_field" value="" /></th>
                </tr>        
            </thead>
            <tbody>
                <tr class="general">
                    <td class="must_entry_caption"> 
                        <input type="hidden" id="selected_id">
                        <? echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $cbo_company_name, '',1);?>
                    </td>
                    <td id="transfer_com" class="must_entry_caption"> 
                        <? echo create_drop_down( "cbo_transport_company_id", 150, "select a.id,a.supplier_name from  lib_supplier a, lib_supplier_tag_company b where a.status_active =1 and a.is_deleted=0 and b.supplier_id=a.id and b.tag_company='$cbo_company_name'  and a.id in (select  supplier_id  from  lib_supplier_party_type where party_type in (35)) order by supplier_name","id,supplier_name", 1, "-- Select Transport --", $cbo_trans_company_name, "" );?>
                    </td>
                    <td><input type="text" name="txt_truck" id="txt_truck" style="width:140px" class="text_boxes" ></td>
                    <td><input type="text" name="txt_challan" id="txt_challan" style="width:140px" class="text_boxes" ></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date"></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date"></td> 
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="fn_show()" style="width:100px;" /></td>
                </tr>
                <tr>
                    <td align="center" colspan="6"><? echo load_month_buttons(1); ?></td>
                </tr>
            </tbody>
        </table>
		<br>
        <div id="search_div"></div>
    </form>
    </div>
    </body>
      <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="challan_search_list_view")
{
	//echo $data;die;
	$com_cond="";$date_cond ="";$year_cond="";$trans_com="";$track_num="";$challan_num="";;
	list($company_id,$tran_com_id,$track_no,$challan_no,$ex_factory_start_date, $ex_factory_end_date,$year) = explode('_', $data);
	if ($company_id!=0) {$com_cond=" and a.company_id=$company_id";$com_cond2=" and b.company_id=$company_id";}
	if ($tran_com_id!=0) {$trans_com=" and a.transport_supplier=$tran_com_id";}
	if ($track_no!='') {$track_num=" and a.truck_no like '%$track_no%'";}
    if ($challan_no!='') {$challan_num=" and a.sys_number like '%$challan_no%'";}
	if ($ex_factory_start_date != '' && $ex_factory_end_date != '') 
	{
        if ($db_type == 0) {
            $date_cond = " and a.delivery_date '" . change_date_format($ex_factory_start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($ex_factory_end_date, 'yyyy-mm-dd') . "'";
        } else if ($db_type == 2) {
            $date_cond = " and a.delivery_date between '" . change_date_format($ex_factory_start_date, '', '', 1) . "' and '" . change_date_format($ex_factory_end_date, '', '', 1) . "'";
		}
    } 
    else 
    {
        $date_cond = '';
		if($year!=""){
			if($db_type==0)
			{
				$year_cond=" and YEAR(a.delivery_date) =$year ";
			}
			else
			{	
				$year_cond=" and to_char(a.delivery_date,'YYYY') =$year ";
			}
		}
	}

    $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $arr=array(0=>$company_arr,1=>$supplier_arr);
	$sql= "SELECT a.id, a.company_id, a.transport_supplier, a.truck_no,a.sys_number, a.delivery_date from pro_ex_factory_delivery_mst a where a.status_active=1 and a.is_deleted=0 $com_cond $trans_com $track_num $challan_num $date_cond $year_cond and a.id not in (SELECT distinct c.challan_btb_id from TRANSPORT_BILL_MST b, TRANSPORT_BILL_DTLS c where b.id=c.mst_id and b.type_id=1 and c.challan_btb_id>0 $com_cond2 and b.status_active=1 and c.status_active=1) order by a.id DESC";
	// echo $sql;die;
	echo  create_list_view("search_div", "Company Name,Transport Com., Challan No,Truck No,Ex-Factory Date", "150,120,120,120,120","670","300",0, $sql , "js_set_value", "id", "", 1, "company_id,transport_supplier,0,0", $arr , "company_id,transport_supplier,sys_number,truck_no,delivery_date", "",'','0,0,0,0,3');
	exit();
} 

if ($action=="load_challan_info")
{
    $data=explode("**",$data);

	$sql="SELECT a.id as ID,a.sys_number as SYS_NUMBER, a.challan_no as CHALLAN_NO, a.truck_no as TRUCK_NO, a.buyer_id as BUYER_ID, b.total_carton_qnty, b.ex_factory_qnty, b.invoice_no as INVOICE_NO, b.additional_info_id as additional_info_id
    from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b 
    where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='$data[0]' and b.delivery_mst_id='$data[0]'";
    // echo $sql;
    $sqlResult =sql_select($sql);

    $all_inv_id=$invoice_no='';
    foreach($sqlResult as $row)
    {
        $cbm_amt=explode("___",$row["ADDITIONAL_INFO_ID"]);
        $cbm_sum_arr[$row["DELIVERY_MST_ID"]]["cdm"] +=$cbm_amt[5];
        if($row["INVOICE_NO"]){$all_inv_id.=$row["INVOICE_NO"].',';}
        $ex_carton_qnty+=$row["TOTAL_CARTON_QNTY"];
        $ex_factory_qnty+=$row["EX_FACTORY_QNTY"];
    }
	
	$all_inv_id=implode(",",array_unique(explode(",",$all_inv_id)));

    if($all_inv_id)
    {
        $all_inv_id=rtrim($all_inv_id,',');
        $sqlEx = sql_select("SELECT id as ID,invoice_no as INVOICE_NO from com_export_invoice_ship_mst where status_active=1 and id in($all_inv_id)");
        foreach($sqlEx as $row)
        {
            $invoice_no.=$row["INVOICE_NO"].', ';
        }
        $invoice_no=rtrim($invoice_no,', ');
    }

    echo "$('#txt_challan_no_$data[1]').val('".$sqlResult[0]["SYS_NUMBER"]."');\n";
    echo "$('#txt_challan_no_id_$data[1]').val('".$sqlResult[0]["ID"]."');\n";
    echo "$('#txt_shipment_qty_$data[1]').val('".$ex_factory_qnty."');\n";
    echo "$('#txt_carton_qty_$data[1]').val('".$ex_carton_qnty."');\n";
    echo "$('#txt_invoice_no_$data[1]').val('".$invoice_no."');\n";
    echo "$('#txt_invoice_id_$data[1]').val('".$all_inv_id."');\n";
    echo "$('#cbo_buyer_$data[1]').val('".$sqlResult[0]["BUYER_ID"]."');\n";
    echo "$('#txt_cbm_$data[1]').val('".$cbm_sum_arr[$sqlResult[0]["ID"]]["cdm"]."');\n";
    echo "$('#txt_vehicle_no_$data[1]').val('".$sqlResult[0]["TRUCK_NO"]."');\n";
    echo "document.getElementById('txt_invoice_no_$data[1]').setAttribute('title','".$invoice_no."');\n";

	exit();
}

if ($action=="btb_lc_popup")
{
	echo load_html_head_contents("Challan Info", "../../../", 1, 1,$unicode,1,'');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value( id )
		{
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
		function fn_show()
		{
			if(form_validation('cbo_company_id','Company Name')==false){
					return;
				}
			show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_supp_id').value+'_'+document.getElementById('txt_lc').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value, 'btb_lc_search_list_view', 'search_div', 'transport_bill_entry_controller', 'setFilterGrid(\'search_div\',-1)');
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                <thead>
                    <tr>
                        <th class="must_entry_caption">Company Name</th>
                        <th >Supplier</th>
                        <th>L/C Number</th>
                        <th colspan="2">L/C Date Range</th>
                        <th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="id_field" id="id_field" value="" /></th>
                    </tr>        
                </thead>
                <tbody>
                    <tr class="general">
                        <td > 
                            <input type="hidden" id="selected_id">
                            <? echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $cbo_company_name, '',1);?>
                        </td>
                        <td id="transfer_com"> 
                            <? echo create_drop_down( "cbo_supp_id", 150, "select a.id,a.supplier_name from  lib_supplier a, lib_supplier_tag_company b where a.status_active =1 and a.is_deleted=0 and b.supplier_id=a.id and b.tag_company='$cbo_company_name' order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "" );?>
                        </td>
                        <td><input type="text" name="txt_lc" id="txt_lc" style="width:140px" class="text_boxes" ></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date"></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date"></td> 
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="fn_show()" style="width:100px;" /></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="6"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
            </table>
            <br>
            <div id="search_div"></div>
        </form>
    </div>
    </body>
      <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="btb_lc_search_list_view")
{
	// echo $data;die;
	$com_cond="";$date_cond ="";$year_cond="";$supp_nam="";$lc_num="";
	list($company_id,$supp_id,$lc_no,$ex_factory_start_date, $ex_factory_end_date,$year) = explode('_', $data);
	if ($company_id!=0) {$com_cond=" and a.importer_id=$company_id"; $com_cond2=" and b.company_id=$company_id";}
	if ($supp_id!=0) {$supp_nam=" and a.supplier_id=$supp_id";}
	if ($lc_no!='') {$lc_num=" and a.lc_number like '%$lc_no%'";}
	if ($ex_factory_start_date != '' && $ex_factory_end_date != '') 
	{
        if ($db_type == 0) {
            $date_cond = "and a.lc_date '" . change_date_format($ex_factory_start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($ex_factory_end_date, 'yyyy-mm-dd') . "'";
        } else if ($db_type == 2) {
            $date_cond = "and a.lc_date between '" . change_date_format($ex_factory_start_date, '', '', 1) . "' and '" . change_date_format($ex_factory_end_date, '', '', 1) . "'";
		}
    } 
    else 
    {
        $date_cond = '';
		if($year!=""){
			if($db_type==0)
			{
				$year_cond=" and YEAR(a.lc_date) =$year ";
			}
			else
			{	
				$year_cond=" and to_char(a.lc_date,'YYYY') =$year ";
			}
		}
	}

    $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $arr=array(0=>$company_arr,1=>$supplier_arr);
	// $sql= "SELECT a.id, a.importer_id, a.supplier_id, a.lc_number, a.lc_date from com_btb_lc_master_details a where a.status_active=1 and a.is_deleted=0 $com_cond $supp_nam $lc_num $date_cond $year_cond and a.id not in (SELECT distinct c.challan_btb_id from TRANSPORT_BILL_MST b, TRANSPORT_BILL_DTLS c where b.id=c.mst_id and b.type_id=2 and c.challan_btb_id>0 $com_cond2 and b.status_active=1 and c.status_active=1) order by a.id DESC";
    $sql= "SELECT a.id, a.importer_id, a.supplier_id, a.lc_number, a.lc_date from com_btb_lc_master_details a where a.status_active=1 and a.is_deleted=0 $com_cond $supp_nam $lc_num $date_cond $year_cond  order by a.id DESC";
	// echo $sql;die;
	echo  create_list_view("search_div", "Company Name,Supplier,L/C Number,L/C Date", "150,120,120,120","550","300",0, $sql , "js_set_value", "id", "", 1, "importer_id,supplier_id,0,0", $arr , "importer_id,supplier_id,lc_number,lc_date", "",'','0,0,0,0,3');
	exit();
} 

if ($action=="load_btb_lc_info")
{
    $data=explode("**",$data);

	$sql= "select id as ID, lc_number as LC_NUMBER, supplier_id as SUPPLIER_ID from com_btb_lc_master_details where status_active=1 and is_deleted=0 and id='$data[0]' order by id DESC";

    $sqlResult =sql_select($sql);

    echo "$('#txt_btb_lc_$data[1]').val('".$sqlResult[0]["LC_NUMBER"]."');\n";
    echo "$('#txt_btb_lc_id_$data[1]').val('".$sqlResult[0]["ID"]."');\n";
    echo "$('#cbo_supp_id_$data[1]').val('".$sqlResult[0]["SUPPLIER_ID"]."');\n";
	exit();
}

if($action=="save_update_delete"){
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

        $sql_bill=sql_select("SELECT ID FROM TRANSPORT_BILL_MST where BILL_NO=$txt_bill_no and TYPE_ID=1");
        if($sql_bill[0]["ID"]>0){
            echo "11**"."DUPLICATE BILL NO NOT ALLOWED = ".$txt_bill_no;oci_rollback($con);disconnect($con);die;
        }
        
		$mst_id=return_next_id("id", "TRANSPORT_BILL_MST", 1);
		
		if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
		
		$new_sys_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'TBE', date("Y",time()), 5, "select sys_number_prefix,sys_number_prefix_num from TRANSPORT_BILL_MST where company_id=$cbo_company_name $insert_date_con order by id desc ", "sys_number_prefix", "sys_number_prefix_num" ));

		$field_array_mst="id, sys_number, sys_number_prefix, sys_number_prefix_num, company_id, location_id, trans_com_id, bill_date, bill_no, type_id, ship_mode, depo, port_name, remarks,ready_to_approve,payable_date, inserted_by, insert_date, status_active, is_deleted";
		$data_array_mst="(".$mst_id.",'".$new_sys_no[0]."','".$new_sys_no[1]."','".$new_sys_no[2]."',".$cbo_company_name.",".$cbo_location_name.",".$cbo_transport_company.",".$txt_bill_date.",".$txt_bill_no.",".$cbo_type_name.",".$cbo_shipment_id.",".$txt_depo.",".$txt_port.",".$txt_remarks.",".$cbo_approve_status.",".$txt_payable_date.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		//  echo "10**INSERT INTO TRANSPORT_BILL_MST (".$field_array_mst.") VALUES ".$data_array_mst; 
		//  die;
        $dtls_id=return_next_id("id", "TRANSPORT_BILL_DTLS", 1);
        $data_array_dtls='';
        $cbo_type_name=str_replace("'","",$cbo_type_name);
        if($cbo_type_name==1){
            $field_array_dtls="id, mst_id, challan_no, challan_btb_id, invoice_id, buyer_supp_id, cbm_amt,vechicale_no,no_vechicale,vechicale_rent, point_unloading, load_extra_unload, local_vechicle_rent, demurrage_other,other_charge,amount,deduction,payable, inserted_by, insert_date, is_deleted, status_active";
            for($i=1;$i<=$total_row;$i++)
            {
                $txt_challan_no      = "txt_challan_no_".$i;
                $txt_challan_no_id    = "txt_challan_no_id_".$i;
                $txt_invoice_id    = "txt_invoice_id_".$i;
                $cbo_buyer    = "cbo_buyer_".$i;
                $txt_cbm    = "txt_cbm_".$i;
                $txt_vehicle_no    = "txt_vehicle_no_".$i;
                $txt_no_vehicle    = "txt_no_vehicle_".$i;
                $txt_vehicle_rent    = "txt_vehicle_rent_".$i;
                $txt_point_unloading    = "txt_point_unloading_".$i;
                $txt_loading_unloading    = "txt_loading_unloading_".$i;
                $txt_local_vechicle    = "txt_local_vechicle_".$i;
                $txt_demurrage    = "txt_demurrage_".$i;
                $txt_other    = "txt_other_".$i;
                $txt_amount    = "txt_amount_".$i;
                $txt_deduction    = "txt_deduction_".$i;
                $txt_payable    = "txt_payable_".$i;

                if ($data_array_dtls!='') {$data_array_dtls .=",";}
                $data_array_dtls .="(".$dtls_id.",".$mst_id.",'".$$txt_challan_no."','".$$txt_challan_no_id."','".$$txt_invoice_id."','".$$cbo_buyer."','".$$txt_cbm."','".$$txt_vehicle_no."','".$$txt_no_vehicle."','".$$txt_vehicle_rent."','".$$txt_point_unloading."','".$$txt_loading_unloading."','".$$txt_local_vechicle."','".$$txt_demurrage."','".$$txt_other."','".$$txt_amount."','".$$txt_deduction."','".$$txt_payable."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
                $dtls_id++;
            }
        }
        if($cbo_type_name==2){
            $field_array_dtls="id, mst_id, challan_no, challan_btb_id, buyer_supp_id, qty, cbm_amt, vechicale_no,no_vechicale,vechicale_rent, load_extra_unload, local_vechicle_rent, demurrage_other,amount,deduction,payable, inserted_by, insert_date, is_deleted, status_active";

            for($i=1;$i<=$total_row;$i++)
            {
                $txt_challan_no    = "txt_challan_no_".$i;
                $txt_btb_lc_id    = "txt_btb_lc_id_".$i;
                $cbo_supp_id    = "cbo_supp_id_".$i;
                $txt_qty    = "txt_qty_".$i;
                $txt_cbm    = "txt_cbm_".$i;
                $txt_vehicle_no    = "txt_vehicle_no_".$i;
                $txt_no_vehicle    = "txt_no_vehicle_".$i;
                $txt_vehicle_rent    = "txt_vehicle_rent_".$i;
                $txt_loading_unloading    = "txt_loading_unloading_".$i;
                $txt_local_vechicle    = "txt_local_vechicle_".$i;
                $txt_other    = "txt_other_".$i;
                $txt_amount    = "txt_amount_".$i;
                $txt_deduction    = "txt_deduction_".$i;
                $txt_payable    = "txt_payable_".$i;

                if ($data_array_dtls!='') {$data_array_dtls .=",";}
                $data_array_dtls .="(".$dtls_id.",".$mst_id.",'".$$txt_challan_no."','".$$txt_btb_lc_id."','".$$cbo_supp_id."','".$$txt_qty."','".$$txt_cbm."','".$$txt_vehicle_no."','".$$txt_no_vehicle."','".$$txt_vehicle_rent."','".$$txt_loading_unloading."','".$$txt_local_vechicle."','".$$txt_other."','".$$txt_amount."','".$$txt_deduction."','".$$txt_payable."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
                $dtls_id++;
                
            }
        }

		//echo "10** INSERT INTO TRANSPORT_BILL_DTLS (".$field_array_dtls.") VALUES ".$data_array_dtls;oci_rollback($con);disconnect($con);die; 
		$rID=sql_insert("TRANSPORT_BILL_MST",$field_array_mst,$data_array_mst,0);
		$rID1=sql_insert("TRANSPORT_BILL_DTLS",$field_array_dtls,$data_array_dtls,0);	
		//echo '10**'.$rID.'**'.$rID1;oci_rollback($con);disconnect($con);die;
		
		if($db_type==0)
		{
			if($rID==1 && $rID1==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$new_sys_no[0]."**".$mst_id."**".$dtls_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1 && $rID1==1)
			{
				oci_commit($con);  
				echo "0**".$new_sys_no[0]."**".$mst_id."**".$dtls_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
    else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array_mst="company_id*location_id*trans_com_id*bill_date*bill_no*type_id*ship_mode*depo*port_name*remarks*ready_to_approve*payable_date*updated_by*update_date";

		$data_array_mst="".$cbo_company_name."*".$cbo_location_name."*".$cbo_transport_company."*".$txt_bill_date."*".$txt_bill_no."*".$cbo_type_name."*".$cbo_shipment_id."*".$txt_depo."*".$txt_port."*".$txt_remarks."*".$cbo_approve_status."*".$txt_payable_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

        $dtls_id=return_next_id("id", "TRANSPORT_BILL_DTLS", 1);
        $data_array_dtls='';
        $cbo_type_name=str_replace("'","",$cbo_type_name);
        if($cbo_type_name==1){
            $field_array_dtls="id, mst_id, challan_no, challan_btb_id, invoice_id, buyer_supp_id, cbm_amt,vechicale_no,no_vechicale,vechicale_rent, point_unloading, load_extra_unload, local_vechicle_rent, demurrage_other,OTHER_CHARGE,AMOUNT,DEDUCTION,PAYABLE, inserted_by, insert_date, is_deleted, status_active";
            for($i=1;$i<=$total_row;$i++)
            {
                $txt_challan_no      = "txt_challan_no_".$i;
                $txt_challan_no_id    = "txt_challan_no_id_".$i;
                $txt_invoice_id    = "txt_invoice_id_".$i;
                $cbo_buyer    = "cbo_buyer_".$i;
                $txt_cbm    = "txt_cbm_".$i;
                $txt_vehicle_no    = "txt_vehicle_no_".$i;
                $txt_no_vehicle    = "txt_no_vehicle_".$i;
                $txt_vehicle_rent    = "txt_vehicle_rent_".$i;
                $txt_point_unloading    = "txt_point_unloading_".$i;
                $txt_loading_unloading    = "txt_loading_unloading_".$i;
                $txt_local_vechicle    = "txt_local_vechicle_".$i;
                $txt_demurrage    = "txt_demurrage_".$i;
                $txt_other    = "txt_other_".$i;
                $txt_amount    = "txt_amount_".$i;
                $txt_deduction    = "txt_deduction_".$i;
                $txt_payable    = "txt_payable_".$i;

                if ($data_array_dtls!='') {$data_array_dtls .=",";}
                $data_array_dtls .="(".$dtls_id.",".$update_id.",'".$$txt_challan_no."','".$$txt_challan_no_id."','".$$txt_invoice_id."','".$$cbo_buyer."','".$$txt_cbm."','".$$txt_vehicle_no."','".$$txt_no_vehicle."','".$$txt_vehicle_rent."','".$$txt_point_unloading."','".$$txt_loading_unloading."','".$$txt_local_vechicle."','".$$txt_demurrage."','".$$txt_other."','".$$txt_amount."','".$$txt_deduction."','".$$txt_payable."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
                $dtls_id++;
            }
        }
        if($cbo_type_name==2){
            $field_array_dtls="id, mst_id, challan_no, challan_btb_id, buyer_supp_id, qty, cbm_amt, vechicale_no,no_vechicale,vechicale_rent, load_extra_unload, local_vechicle_rent, demurrage_other,amount,deduction,payable, inserted_by, insert_date, is_deleted, status_active";

            for($i=1;$i<=$total_row;$i++)
            {
                $txt_challan_no    = "txt_challan_no_".$i;
                $txt_btb_lc_id    = "txt_btb_lc_id_".$i;
                $cbo_supp_id    = "cbo_supp_id_".$i;
                $txt_qty    = "txt_qty_".$i;
                $txt_cbm    = "txt_cbm_".$i;
                $txt_vehicle_no    = "txt_vehicle_no_".$i;
                $txt_no_vehicle    = "txt_no_vehicle_".$i;
                $txt_vehicle_rent    = "txt_vehicle_rent_".$i;
                $txt_loading_unloading    = "txt_loading_unloading_".$i;
                $txt_local_vechicle    = "txt_local_vechicle_".$i;
                $txt_other    = "txt_other_".$i;
                $txt_amount    = "txt_amount_".$i;
                $txt_deduction    = "txt_deduction_".$i;
                $txt_payable    = "txt_payable_".$i;

                if ($data_array_dtls!='') {$data_array_dtls .=",";}
                $data_array_dtls .="(".$dtls_id.",".$update_id.",'".$$txt_challan_no."','".$$txt_btb_lc_id."','".$$cbo_supp_id."','".$$txt_qty."','".$$txt_cbm."','".$$txt_vehicle_no."','".$$txt_no_vehicle."','".$$txt_vehicle_rent."','".$$txt_loading_unloading."','".$$txt_local_vechicle."','".$$txt_other."','".$$txt_amount."','".$$txt_deduction."','".$$txt_payable."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
                $dtls_id++;
                
            }
        }

		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$rID=sql_update("TRANSPORT_BILL_MST",$field_array_mst,$data_array_mst,"id","".$update_id."",0);
		$rID1=sql_delete("TRANSPORT_BILL_DTLS",$field_array,$data_array,"mst_id","".$update_id."",0);
		$rID2=sql_insert("TRANSPORT_BILL_DTLS",$field_array_dtls,$data_array_dtls,0);	
	
		// echo "10**".$rID.'='.$rID1.'='.$rID2."</br>"; die;
		if($db_type==0)
		{
			if($rID==1 && $rID1==1 && $rID2==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1 && $rID1==1 && $rID2==1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
	
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
    else if ($operation==2) // Delete Here----------------------------------------------------------  
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=sql_delete("TRANSPORT_BILL_MST",$field_array,$data_array,"id","".$update_id."",0);
		$rID1=sql_delete("TRANSPORT_BILL_DTLS",$field_array,$data_array,"mst_id","".$update_id."",0);
		// echo "10**".$rID.'='.$rID1."</br>"; die;
		if($db_type==0)
		{
			if($rID==1 && $rID1==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id);;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1 && $rID1==1)
			{
				oci_commit($con);  
				echo "2**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id);;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		
		disconnect($con);
	}//
}