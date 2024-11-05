<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create  for Lab Test Rate Chart 
Functionality	:	
JS Functions	:
Created by		:	Ashraful Islam
Creation date 	: 	11-06-2015
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//----------------------------------------------------------------------------------------------------------------
 echo load_html_head_contents("Product Sub Department", "../../", 1, 1,$unicode,'','');

?>
<script type="text/javascript" charset="utf-8">

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';

function fnc_testing_chart( operation )
{
	if (form_validation('cbo_test_category*cbo_test_for*txt_test_item*cbo_supplier*cbo_within_group','Test Catagory*Test For*Test Item*Supplier*Within Group')==false)
	{
		return;
	}

	else
	{
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_test_category*cbo_test_for*txt_test_item*txt_rate*txt_upcharge_per*txt_upcharge_amount*update_id*cbo_currency*cbo_supplier*txt_net_rate*cbo_uom_id*cbo_within_group',"../../");
		freeze_window(operation);
		http.open("POST","requires/lab_test_rate_chart_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_testing_chart_reponse;
	}
}


function fnc_testing_chart_reponse()
{
	if(http.readyState == 4) 
	{
		// alert(http.responseText);release_freezing();return;
		var reponse=trim(http.responseText).split('**');
		show_msg(trim(reponse[0]));
		show_list_view('','search_list_view','sub_department_list_view','../merchandising_details/requires/lab_test_rate_chart_controller','setFilterGrid("list_view",-1)');
		reset_form('subdepartment_1','','');
		set_button_status(0, permission, 'fnc_testing_chart',1);
		release_freezing();
	}
}
 
 function calculate_amount()
 {
	var rate=$("#txt_rate").val();
	var parcentage=$("#txt_upcharge_per").val();
	var amount=(rate*parcentage)/100;
	var total_amount=rate*1+amount*1;
	$("#txt_upcharge_amount").val(amount);
	$("#txt_net_rate").val(total_amount);
 }
 
 function calculate_total_amount()
 {
	var rate=$("#txt_rate").val();
	var amount=$("#txt_upcharge_amount").val();
	var parcentage=(amount/rate)*100;
	var total_amount=rate*1+amount*1;
	$("#txt_net_rate").val(total_amount);
	$("#txt_upcharge_per").val(parcentage);
 }
 
 function calculate_amount_rate()
 {
	var rate=$("#txt_rate").val();
	$("#txt_upcharge_per").val('');
	var amount=$("#txt_upcharge_amount").val();
	if(amount!=0 || amount!='')
	{
		var parcentage=(amount/rate)*100;
		var total_amount=rate*1+amount*1;
		$("#txt_net_rate").val(total_amount);
		$("#txt_upcharge_per").val(parcentage);
	}
	else
	{
	$("#txt_net_rate").val(rate);	
	}
 }
 calculate_amount_rate
 
</script>
<body onLoad="set_hotkey()">
   <div align="center" style="width:100%;">  
	 <? echo load_freeze_divs ("../../",$permission);  ?> 
        <fieldset style="width:900px; margin-top:10px;">
           <legend>Lab Test Rate Chart Information</legend>
             <form id="subdepartment_1"  name="subdepartment_1" autocomplete="off" >  
               <table width="100%" border="0" cellpadding="2" cellspacing="5">
                  <tr>
                     <td colspan="4">&nbsp;</td>
                  </tr>
                  <tr>
                     <td width="100" class="must_entry_caption" align="right">Test Category </td>
                     <td  width="150" >
                     	<?
                     	  asort($testing_category) ;
          						  echo create_drop_down( "cbo_test_category", 165, $testing_category, 0, 1, "--Select Test Category--",$selected, "", "", "" );
          						?>
                     </td>
                     <td width="100" align="right" class="must_entry_caption">Test For</td>
                     <td>
                     	<? 
						
						echo create_drop_down( "cbo_test_for", 165, $test_for, 0, 1, "--Select Test For--",$selected, "", "", "" );
					    ?>	
                     </td>
                     <td width="100" align="right" class="must_entry_caption">Test Item</td>
                     <td>
                        <input type="text" name="txt_test_item"  id="txt_test_item"   style="width:156px" class="text_boxes" />
                     </td>
                  </tr> 
                  <tr>
                     <td  align="right" >Rate </td>
                     <td>
							<input type="text" name="txt_rate"  id="txt_rate"   style="width:156px" class="text_boxes_numeric" onBlur="calculate_amount_rate()"/>                     </td>
                     <td align="right">Upcharge % </td>
                     <td>
              		 	<input type="text" name="txt_upcharge_per"  id="txt_upcharge_per"   style="width:156px" class="text_boxes_numeric"  onBlur="calculate_amount()"/>
                     </td>
                     <td   align="right">Upcharge Amout </td>
                     <td>
              		 	<input type="text" name="txt_upcharge_amount"  id="txt_upcharge_amount"   style="width:156px"  class="text_boxes_numeric"  onBlur="calculate_total_amount()"/>
                    
                     </td>
                  </tr> 
                  <tr>
                     <td   align="right">Net Rate </td>
                     <td>
							<input type="text" name="txt_net_rate"  id="txt_net_rate"   style="width:156px" class="text_boxes_numeric"  readonly/> </td>
                     <td  class="must_entry_caption" align="right">Within Group </td>
                     <td>
                         <? 
							echo create_drop_down( "cbo_within_group", 165, $yes_no, 0, 1, "--Select--",$selected, "load_drop_down( 'requires/lab_test_rate_chart_controller', this.value, 'load_drop_down_supplier', 'supplir_td' );", "", "" );
					     ?>
                     </td>
                     <td   align="right" class="must_entry_caption">Testing Company </td>
                     <td id="supplir_td">
              		 	 <? 
							// echo create_drop_down( "cbo_supplier", 167, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=26 order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "","" );
							 echo create_drop_down( "cbo_supplier", 167, array(),"", 1, "-- Select --", 0, "","" );
					     ?>
                     </td>
                  </tr> 
                   <tr>
                     <td   align="right">Uom </td>
                     <td>
						<? 
						echo create_drop_down( "cbo_uom_id", 165, $unit_of_measurement, 0, 1, "--Select Uom--",$selected, "", "", "" );
						?>
                    </td>
					<td align="right">Currency </td>
                     <td>
                         <? 
							echo create_drop_down( "cbo_currency", 165, $currency, 0, 1, "--Select Currency--",$selected, "", "", "" );
					     ?>
                     </td>
                    
                  </tr> 
                  <tr>
                    <td><input type="hidden" id="update_id" name="update_id">
                    </td>
                  </tr>
                
                  <tr>
                     <td colspan="6" align="center" class="button_container" >
                       
                      <?php  echo load_submit_buttons( $permission, "fnc_testing_chart", 0,0 ,"reset_form('subdepartment_1','','','','','')"); ?> 
                       
                    </td>
                  </tr>
                  <tr>
                        <td colspan="6">&nbsp;</td>
                  </tr>
                   <tr>
                      <td colspan="6" id="sub_department_list_view" >
                      <?
					  $supplier_arr=return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b 
					  where a.id=b.supplier_id and b.party_type=26 order by a.supplier_name", "id", "supplier_name"  );
					 
					  $arr=array (0=>$testing_category,1=>$test_for,7=>$currency,8=>$supplier_arr);
					  echo  create_list_view ( "list_view", "Test Category,Test For, Test Item,Rate,Upcharge %,Upcharge Amount,Net Rate,Currency,Testing Company", "130,80,130,60,60,70,70,60,100","880","220",1, "
SELECT id,test_category,test_for,test_item,rate,upcharge_parcengate,upcharge_amount,net_rate,currency_id,testing_company FROM lib_lab_test_rate_chart WHERE status_active =1 AND is_deleted =0", "get_php_form_data", "id", "'load_php_data_to_form'", 1,"test_category,test_for,0,0,0,0,0,currency_id,testing_company", $arr, "test_category,test_for,test_item,rate,upcharge_parcengate,upcharge_amount,net_rate,currency_id,testing_company", "../merchandising_details/requires/lab_test_rate_chart_controller", 'setFilterGrid("list_view",-1);','0,0,0,2,2,2,2,0,0'); 
                      ?>
                    </td>
                </tr>
                          
          </table>
       		</form>
       	</fieldset>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
