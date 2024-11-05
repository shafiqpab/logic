<?
/*-------------------------------------------- Comments
Purpose			: 	Test;
Functionality	:	Learning
JS Functions	:
Created by		:	Imran Babu
Creation date 	: 	26-01-2016
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
echo load_html_head_contents("New Quotation Inquery Entry", "../", 1, 1,$unicode,'','');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	function fnc_quotation_inquery( operation )
{
	
	if (form_validation('cbo_company_name*cbo_buyer_name*txt_style_ref*txt_inquery_date','Company*Buyer*Style Ref*Inquery Date')==false)
	{
		return;
	}

	else // Save Here
	{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_style_ref*txt_inquery_date*txt_season*cbo_status*txt_request_no*txt_remarks*cbo_dealing_merchant*txt_system_id*cbo_gmt_item*txt_est_ship_date*txt_fabrication*txt_offer_qty*txt_color*txt_req_quot_date*txt_target_samp_date*txt_actual_req_quot_date*txt_actual_sam_send_date*update_id',"../../");
			//alert(data);	return;
		//freeze_window(operation);
		http.open("POST","requires/prac1_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_quotation_inquery_reponse;
	}
}
</script>
</head>

<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">      
        <? echo load_freeze_divs ("../../",$permission);  ?>
        <fieldset style="width:950px;">
            <legend>New Quotation Inquery</legend>
            <form name="quotationinquery_1" id="quotationinquery_1" autocomplete="off">
                <table  width="940" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td  width="100" height="" align="right"></td>              
                        <td  width="170" ></td>
                        <td  width="100" align="right">System ID </td>                        
                        <td width="170">
                            <input style="width:160px;" type="text" title="Double Click to Search" onDblClick="open_mrrpopup()" class="text_boxes" placeholder="System ID" name="txt_system_id" id="txt_system_id" readonly />
                           <input type="hidden" name="update_id" id="update_id" /> 
                        </td>
                        <td width="100" align="right"></td>
                        <td></td>
					</tr>
                    <tr>
                        <td  width="100" height="" class="must_entry_caption" align="right">Company</td>              
                        <td  width="170" >
                              <?
                                 echo create_drop_down( "cbo_company_name", 172, "select comp.id,comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/quotation_inquery_controller', this.value, 'load_drop_down_buyer', 'buyer_td');",0);
                              ?> 
                        </td>
                        <td  width="100" class="must_entry_caption" align="right">Buyer </td>
                        <td width="170" id="buyer_td">
                           <? 
                        echo create_drop_down( "cbo_buyer_name", 172, "select id,buyer_name from lib_buyer  where status_active =1 and is_deleted=0  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ,0);   
                           ?>
                        </td>
                        <td width="100" class="must_entry_caption" align="right">Style Ref/Name</td>
                        <td id="location" width="170">
                            <input class="text_boxes" type="text" style="width:160px"  name="txt_style_ref" id="txt_style_ref"/>		
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Season</td>
                        <td id="">
                         <input class="text_boxes" type="text" style="width:160px"  name="txt_season" id="txt_season"/>	  	  
                        </td>
                        <td align="right" class="must_entry_caption">Inq.Rcvd Date</td>
                        <td>
                           <input name="txt_inquery_date" id="txt_inquery_date" class="datepicker" type="text" value="" style="width:160px; "  />	
                        </td>
                        <td align="right">Status</td>
                        <td>	
                            <?
							echo create_drop_down( "cbo_status", 172, $row_status,"", "", "", 1, "" );
						     ?>  
                        </td>
                    </tr>
                    <tr>
                        <td height="" align="right">Buyer Inquiry No</td>   
                        <td>
                               <input class="text_boxes" type="text" style="width:160px"  name="txt_request_no" id="txt_request_no"/>	 
                        </td>
                        <td height="" align="right">Dealing Merchant</td>   
                        <td>
                               <? 
                        	echo create_drop_down( "cbo_dealing_merchant", 172, "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Merchant --", $selected, "" );
                        ?> 
                        </td>
                        <td align="right">Gmts Item</td>   
                        <td > 
                       <?   echo create_drop_down( "cbo_gmt_item", 172, $garments_item,"", 1, "-- Select Gmts Item --", $selected, "" );?>		
                        </td>
                    </tr>
                    
                    <tr>
                        <td height="" align="right">Bulk Est. Ship Date</td>   
                        <td>
                            <input  type="text" style="width:160px" class="datepicker"  name="txt_est_ship_date" id="txt_est_ship_date"/>
                        </td>
                        <td height="" align="right">Fabrication</td>   
                        <td>
                               <input class="text_boxes" type="text" style="width:160px"  name="txt_fabrication" id="txt_fabrication" onDblClick="openmypage_fabric_popup()" readonly/>		 
                        </td>
                        <td align="right">Bulk Offer Qty</td>   
                        <td> 
                     		 <input class="text_boxes_numeric" type="text" style="width:160px"  name="txt_offer_qty" id="txt_offer_qty"/>	 	 
                        </td>
                    </tr>
                        <td height="" align="right">Color</td>   
                        <td>
                                 <input class="text_boxes" type="text" style="width:160px"  name="txt_color" id="txt_color"/>	
                        </td>
                        <td height="" align="right">Target Req. Cuot. Date</td>   
                        <td>
                                <input  type="text" style="width:160px" class="datepicker"  name="txt_req_quot_date" id="txt_req_quot_date"/>	 
                        </td>
                        <td align="right">Target Samp Sub:Date</td>   
                        <td> 
                      		  <input  type="text" style="width:160px" class="datepicker"  name="txt_target_samp_date" id="txt_target_samp_date"/>		
                        </td>
                    </tr>
                    <tr>
                         <td height="" align="right">Actual Samp.Send Date</td>   
                        <td>
                               
                               <input  type="text" style="width:160px" class="datepicker"  name="txt_actual_sam_send_date" id="txt_actual_sam_send_date"/>	 
                        </td>
                        <td height="" align="right"></td>   
                        <td>
                          <input type="button" class="image_uploader" style="width:172px" value=" ADD File" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'quotation_inquery', 2 ,1)">
                        </td>
                        <td align="right"></td>   
                       <td>
                            	<input type="button" class="image_uploader" style="width:172px" value="CLICK TO ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'quotation_inquery', 0 ,1)">
                               
                        </td>
                    </tr>
                     <tr>
                         <td height="" align="right">Actual Quot. Date</td>   
                        <td>
                               <input  type="text" style="width:160px" class="datepicker"  name="txt_actual_req_quot_date" id="txt_actual_req_quot_date"/>	
                        </td>
                        <td height="" align="right">Remarks </td>   
                        <td>
                        <input class="text_boxes" type="text" style="width:160px"  name="txt_remarks" id="txt_remarks"/>	 
                          
                        </td>
                        <td align="right"></td>   
                       <td>
                            	
                               
                        </td>
                    </tr>
                   
                    <tr>
                        <td align="center" colspan="6" valign="middle" style="max-height:380px; min-height:15px;" id="size_color_breakdown11">
                        <? 
                        echo load_submit_buttons( $permission, "fnc_quotation_inquery", 0,0 ,"reset_form('quotationinquery_1','','')",1) ;                        ?>
                        </td>
                   </tr>
			</table>
			</form>
		</fieldset>
	</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>