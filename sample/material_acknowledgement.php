<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create for Material Acknowledgement in sample module
Functionality	:
JS Functions	:
Created by		:	zakaria joy
Creation date 	: 	10-11-2020
Updated by 		:	
Update date		:	
QC Performed BY	:
QC Date			:
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
echo load_html_head_contents("Material Acknowledgement","../", 1, 1, $unicode,1,'');
?>
<script type="text/javascript">
	
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../",$permission);  ?>
        <fieldset style="width:820px;">
            <legend>Material Acknowledgement</legend>
            <form name="material_acknowledgement_form" id="material_acknowledgement_form" autocomplete="off">
                <table  width="820" cellspacing="2" cellpadding="0" border="0">
                    <!-- <tr>
                        <td colspan="3" align="right">System NO.</td>
                        <td colspan="4">
                            <input style="width:140px;" type="text" title="Double Click to Search" onDblClick="open_consumptionpopup();" class="text_boxes" placeholder="Browse" name="txt_system_id" id="txt_system_id" readonly />
                            <input type="hidden" name="update_id" id="update_id" />
                        </td>
                    </tr> -->
                    <tr>
                        <td width="100" align="right" class="must_entry_caption">Requisition</td>
                        <td><input class="text_boxes" type="text" style="width:140px" placeholder="Browse"  name="txt_requisition" id="txt_requisition" onDblClick=""/>
                        </td>                         
                    	<td width="100" align="right">Requisition Date</td>
                        <td><input name="txt_requisition_date" style="width:140px"  id="txt_requisition_date" placeholder="Select Date" class="datepicker" type="text" value="" /></td>                       
                        <td width="100" class="must_entry_caption" align="right">Material Rcvd Date</td>
                        <td>
                            <input name="txt_material_rcvd_date" style="width:140px"  id="txt_material_rcvd_date" placeholder="Select Date" class="datepicker" type="text" value="" />
                        </td>                      
                    </tr>
                    <tr>
                    	<td align="right">Master Style</td>
                        <td colspan="3"><input class="text_boxes" type="text" style="width:370px" placeholder="Write"  name="txt_master_style" id="txt_master_style"/></td>
                        <td width="100" align="right">Company Name</td>
                        <td><? echo create_drop_down( "cbo_company_name", 150, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "Select Company", $selected, "",1); ?></td>  
                    	<td align="right">Buyer Name</td>
                        <td><input name="txt_buyer_name" class="text_boxes" style="width:140px"  id="txt_buyer_name" type="text" value="" disabled="" /></td>
                                               
                    </tr>
                    <tr>
                    	<td align="right">Season</td>
                        <td width="70"><input name="txt_season" class="text_boxes" style="width:140px"  id="txt_season" type="text" value="" disabled="" /></td>
                        <td align="right">Brand</td>
                        <td><input name="txt_brand_name" class="text_boxes" style="width:140px"  id="txt_brand_name" type="text" value="" disabled="" /></td>
                    </tr>
                </table>                
            </form>
        </fieldset>
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>