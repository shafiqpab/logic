<?

/*--- ----------------------------------------- Comments
Purpose         :   Dyes and chemical issue requision for yarn dyeing
Functionality   :   
JS Functions    :
Created by      :   Samiur
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
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Dyes And Chemical Issue Requisition For Y/D", "../", 1,1, $unicode,1,'');

?>

</head>
<body>
<div style="width:100%;">
    <? echo load_freeze_divs ("../",$permission);  ?>
    <fieldset style="width:900px;">
    <legend style="text-align: center;">Dyes And Chemical Issue Requisition For Y/D</legend>
        <form name="DyesAndChemicalIssueRequisition_1" id="DyesAndChemicalIssueRequisition_1" autocomplete="off">  
            <table  width="900" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td></td>
                    <td></td>
                    <td  width="130" height="" align="right">Requisition Number</td>
                    <td  width="170">
                        <input class="text_boxes"  type="text" name="txt_requisition_no" id="txt_requisition_no" onDblClick="openmypage_rec_id('xx','Dyes And Chemical Issue Requisition For Y/D')"  placeholder="Double Click" style="width:160px;" readonly/>
                    </td>
                   
                </tr>

                <tr>
                    <td  width="130" align="right" class="must_entry_caption">Company Name </td>
                    <td width="170"> 
                        <? echo create_drop_down( "cbo_company_name", 172, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sub_contract_material_receive_controller', this.value, 'load_drop_down_location', 'location_td' ); load_drop_down( 'requires/sub_contract_material_receive_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );"); ?>
                    </td>
                    <td width="130" align="right">Location Name</td>
                    <td id="location_td">
                         <? echo create_drop_down( "cbo_location_name", 172, $blank_array,"", 1, "-- Select Location --", $selected, "" );?>
                    </td>
                    
                    <td  width="130" class="must_entry_caption" align="right">Requisition Date</td>
                    <td>
                        <input type="text" name="txt_requisition_date" id="txt_requisition_date"  class="datepicker" style="width:160px" />             
                    </td>
                    
                </tr>
                <tr>
                    <td width="130" class="must_entry_caption" align="right">Issue Basis</td>
                    <td id="issue_basis_td">
                         <? echo create_drop_down( "cbo_issue_basis", 172, $blank_array,"", 1, "-- Select Location --", $selected, "" );?>
                    </td>
                     <td  width="130"  align="right">Recipe No</td>
                    <td  width="170">
                        <input class="text_boxes" placeholder="Browse" type="text" name="txt_recipe_no" id="txt_recipe_no" style="width:160px;" />  
                    </td>
                    
                    <td  width="130"  align="right">Batch No</td>
                    <td>
                        <input type="text" name="txt_batch_no" id="txt_batch_no"  class="text_boxes" style="width:160px" />             
                    </td>
                    
                </tr>
                <tr>
                    <td width="130" align="right" class="must_entry_caption">Store Name</td>
                    <td id="store_td">
                         <? echo create_drop_down( "cbo_store_name", 172, $blank_array,"", 1, "-- Select Location --", $selected, "" );?>
                    </td>
                     <td  width="130"  align="right">Batch Weight</td>
                    <td>
                        <input type="text" placeholder="Display" name="txt_batch_weight" id="txt_batch_weight"  class="text_boxes" style="width:160px" />             
                    </td>
                    <td  width="130"  align="right">Machine No</td>
                    <td>
                        <input type="text" placeholder="Browse" name="txt_machine_no" id="txt_machine_no"  class="text_boxes" style="width:160px" />             
                    </td>
                    
                    
                    
                </tr>
                <tr>
                    <td width="130" align="right" >Remarks</td>
                    <td>
                        <input type="text" placeholder="write" name="txt_remarks" id="txt_remarks"  class="text_boxes" style="width:160px" />             
                    </td>
                </tr>


            </table>
            <br/>
            <fieldset style="width:1000px;">
            <legend>Metarial Details Entry</legend>
            <table cellpadding="0" cellspacing="2" border="0" width="800">
                <thead class="form_table_header">
                    <tr align="center" >
                        <th width="80">SL</th>
                        <th width="60">Sub Process</th>
                        <th width="40">Product ID</th>
                        <th width="40">Lot</th>
                        <th width="60">Item Category</th>
                        <th width="60">Group</th>
                        <th width="40">Item Description</th>
                        <th width="40">UOM</th>
                        <th width="40">Stock</th>
                        <th width="40">Seq. No.</th>
                        <th width="40">Dose Base</th>
                        <th width="40">Ratio</th>
                        <th width="40">Recipe Qnty</th>
                        <th width="40">Adj%.</th>
                        <th width="40">Adj. Type</th>
                        <th width="40">Reqn. Qnty</th>
                    </tr>
                </thead> 
                <tr>
                     <td>
                        <input name="txt_serial" id="txt_serial" class="text_boxes" type="text"  style="width:70px" value=""/>
                    </td>
                    <td>
                         <input type="text" id="txt_sub_process" name="txt_sub_process" class="text_boxes" style="width:120px">
                    </td>
                    <td>
                         <input type="text" id="txt_product_id" name="txt_product_id" class="text_boxes" style="width:40px">
                    </td>

                    <td>
                         <input type="text" id="txt_lot" name="txt_lot" class="text_boxes" style="width:50px">
                    </td>
                    <td>
                         <input type="text" id="txt_item_category" name="txt_item_category" class="text_boxes" style="width:50px">
                    </td>
                    <td>
                        <input name="txt_group" id="txt_group" class="text_boxes" type="text"  style="width:60px" value=""/>
                    </td>
                   <td>
                        <input name="txt_item_description" id="txt_item_description" class="text_boxes" type="text"  style="width:50px" value=""/>
                    </td>
                    <td>
                        <input name="txt_uom" id="txt_uom" class="text_boxes" type="text"  style="width:40px" />
                    </td>
                     <td>
                        <input name="txt_stock" id="txt_stock" class="text_boxes_numeric" type="text"  style="width:40px" />
                    </td>
                     <td>
                        <input name="txt_seq_no" id="txt_seq_no" class="text_boxes_numeric" type="text"  style="width:50px" />
                    </td>
                    <td>
                        <input name="txt_dose_base" id="txt_dose_base" class="text_boxes_numeric" type="text"  style="width:70px" />
                    </td>
                    <td>
                        <input name="txt_ratio" id="txt_ratio" class="text_boxes" type="text"  style="width:50px" value=""/>
                    </td>
                    <td>
                        <input name="txt_recipe_qnty" id="txt_recipe_qnty" class="text_boxes_numeric" type="text"  style="width:70px" />
                    </td>
                    <td>
                        <input name="txt_adj" id="txt_adj" class="text_boxes" type="text"  style="width:70px" />
                    </td>
                    <td>
                        <input name="txt_adj_type" id="txt_adj_type" class="text_boxes_numeric" type="text"  style="width:70px" />
                    </td>
                    <td>
                        <input name="txt_reqn_qnty" id="txt_reqn_qnty" class="text_boxes_numeric" type="text"  style="width:70px" />
                    </td>
                    
                     
                    
                   
                </tr>
                
             </table>
             </fieldset>  
             <table width="900" cellspacing="2" cellpadding="0" border="0">
                 <tr>
                      <td><input type="hidden" name="update_id" id="update_id"></td>
                      <td><input type="hidden" name="update_id2" id="update_id2"></td>
                      <td><input type="hidden" name="delete_allowed" id="delete_allowed"></td>
                      <td><input type="hidden" name="batch_no" id="batch_no"></td>
                 </tr>
                 <tr>
                    <td align="center" colspan="13" valign="middle" class="button_container">
                        <? echo load_submit_buttons($permission, "fnc_material_receive", 0,0,"reset_form('materialreceive_1','receive_list_view','','cbo_status,2', 'disable_enable_fields(\'cbo_company_name\',0)')",1); ?>
                    </td>
                 </tr>  
                 <br/>
                 <tr align="center">
                    <td colspan="13" id="receive_list_view"> </td>  
                </tr>               
          </table>
        </form>
    </fieldset>



   
    

</body>

<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>