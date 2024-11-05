<?
die;
/*--- ----------------------------------------- Comments
Purpose         :   Soft Coning Production Delivery entry                   
Functionality   :   
JS Functions    :
Created by      :   Shakil Ahmed
Creation date   :   10-02-2020
Updated by      :       
Update date     :
Oracle Convert  :       
Convert date    :      
QC Performed BY :       
QC Date         :   
Comments        :
*/
session_start(); 
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Wash Order Entry Info", "../../", 1,1, $unicode,1,'');

?>
<script>
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
    var permission='<? echo $permission; ?>';

    
</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",$permission); ?>
        <form name="deliveryentry_1" id="deliveryentry_1" autocomplete="off"> 
            <fieldset style="width:700px;">
            <legend>Soft Coning Production Delivery entry</legend>
                <table width="700px" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                    <tr>
                        <td colspan="3" align="right"><strong>Delivery ID</strong></td>
                        <td colspan="2">
                            <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes_numeric" style="width:90px" readonly />
                            <input type="hidden" name="txt_deleted_id_dtls" id="txt_deleted_id_dtls" class="text_boxes_numeric" style="width:90px" readonly />

                            <input class="text_boxes"  type="text" name="txt_delivery_id" id="txt_delivery_id" onDblClick="openmypage_delivery();" placeholder="Double Click" style="width:140px;" readonly />
                        </td>
                    </tr>
                    <tr>
                        <td width="110" class="must_entry_caption">Company Name </td>
                        <td width="160"><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/wash_order_entry_controller', this.value+'_'+1, 'load_drop_down_location', 'location_td'); location_select(); fnc_load_party(1,document.getElementById('cbo_within_group').value);"); ?>
                        </td>
                        <td width="110">Location Name</td>
                        <td width="160" id="location_td"><? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected, "" ); ?></td>




                        <td class="must_entry_caption">Delivery Date</td>
                        <td ><input type="text" name="txt_delivery_date" id="txt_delivery_date"  style="width:140px" value="<? echo date("d-m-Y")?>" class="datepicker" value="" /> </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Party Name</td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_load_party(2,document.getElementById('cbo_within_group').value);"); ?></td>

                            <td width="120">Remarks</td>
                            <td width="160" colspan="5">
                              <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:395px"  placeholder="Entry" /></td>

                    </tr> 
                    
                    <tr>
                        <td>Style</td>
                        <td><input type="text" name="txt_style" id="txt_style" style="width:140px" class="text_boxes" value="" placeholder="Text" /></td>

                        <td align="right" ><strong>Production Qty</strong></td>
                        <td><input name="txtProductionQty" id="txtProductionQty" type="text"  class="text_boxes_numeric" style="width:140px" value="1"/>
                        </td>
                        <td >Balance</td>
                        <td><input type="text" name="txt_balance" id="txt_balance"  style="width:140px"  class="text_boxes_numeric" value="1" readonly /></td>
                    </tr> 
                </table>
        </fieldset> 
        <fieldset style="width:800px;">
           <legend>Soft Coning Production Delivery entry Details</legend>
                <table  cellpadding="0" cellspacing="2" border="0" class="rpt_table" rules="all" id="tbl_dtls_delivery">
                    <thead class="form_table_header">
                        <th width="90" id="sales_order">Sales order no</th>
                        <th width="90" id="lot_td">Lot</th>
                        <th width="90" id="count_td">Count</th>
                        <th width="90" class="must_entry_caption">Yarn Type</th>
                        <th width="90" id="composition_td" class="must_entry_caption">Yarn Composition</th>
                        <th width="70" >Y/D Color</th>
                        <th width="70" class="must_entry_caption"> Bobbin Type</th>
                        <th width="60" class="must_entry_caption" id="order_uom_td">Package Qty(pcs)</th>
                        <th width="80" class="must_entry_caption">Delivery Qty</th>
                       
                       
                    </thead>
                    <tbody id="delivery_details_container">
                        <tr>
                            <td><input name="txtSalesOrder_1" id="txtSalesOrder_1" type="text" class="text_boxes" style="width:80px" placeholder="Display" readonly /></td>

                            <td><input name="txtLot_1" id="txtLot_1" type="text" class="text_boxes" style="width:80px" placeholder="Display" readonly />
                                <input name="txtbuyerPoId_1" id="txtbuyerPoId_1" type="hidden" class="text_boxes" style="width:70px" />
                            </td>

                            <td><input name="txtcount_1" id="txtcount_1" type="text" class="text_boxes" style="width:80px" placeholder="Display" readonly /></td>

                            <td><input name="txttype_1" id="txttype_1" type="text" class="text_boxes" style="width:80px" placeholder="Display" readonly /></td>

                            <td>
                                <input name="txtComposition_1" id="txtComposition_1" type="text" class="text_boxes" style="width:80px" readonly placeholder="Display" />
                                <input name="txtComposition_1" id="txtComposition_1" type="hidden" class="text_boxes" style="width:50px" />
                            </td>

                            <td><input name="txtYDcolor_1" id="txtYDcolor_1" type="text" class="text_boxes" style="width:60px" readonly placeholder="Display" />
                                <input name="txtYDcolor_1" id="txtYDcolor_1" type="hidden" class="text_boxes" style="width:50px" /></td>

                            <td><input name="txtBobbinType_1" id="txtBobbinType_1" class="text_boxes" type="text"  style="width:60px" placeholder="Plastic" readonly/></td>

                            <td><input name="txtPackageQty_1" id="txtPackageQty_1" type="text"  class="text_boxes_numeric" style="width:50px" onClick="" placeholder="Browse" readonly="readonly" /></td>

                            <td><input name="txtDeliveryQty_1" id="txtDeliveryQty_1" type="text" style="width:70px"  class="text_boxes_numeric" readonly /></td> 

                            <td width="65">
                                <input type="button" id="increase_1" name="increase[]" style="width:18px" class="formbuttonplasminus" value="+" onClick="add_dtls_tr(1)" />
                                <input type="button" id="decrease_1" name="decrease[]" style="width:18px" class="formbuttonplasminus" value="-" onClick="fnc_delet_dtls_tr(1);" />
                            </td>
                        </tr>                     
                    </tbody>
                </table>
                <table width="800px" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td align="center" colspan="11" class="button_container">
                            <? echo load_submit_buttons( $permission, "fnc_job_order_entry", 0,0,"fnResetForm();",1); ?>
                        </td>
                    </tr>   
                </table>
            </fieldset> 
        </form>                         
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>