<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create  for Product Sub Department 
Functionality	:	
JS Functions	:
Created by		:	Ashraful Islam
Creation date 	: 	01-01-2014
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

function fnc_product_department( operation )
{
	if (form_validation('txt_sub_dep_name*cbo_department_id*cbo_buyer_id','Sub Department Name','Departnemt Name','Buyer Name')==false)
	{
		return;
	}
	else
	{
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_sub_dep_name*cbo_department_id*cbo_buyer_id*update_id',"../../");
		//alert(data);
		freeze_window(operation);
		http.open("POST","requires/product_sub_department_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_product_department_reponse;
	}
}


function fnc_product_department_reponse()
{
	if(http.readyState == 4) 
	{
		 //alert(http.responseText);
		var reponse=trim(http.responseText).split('**');
		show_msg(trim(reponse[0]));
		//alert(reponse[1]);
		get_php_form_data(reponse[0], "load_php_data_to_form", "requires/product_sub_department_controller" );
		show_list_view('','sub_department_list_view','sub_department_list_view','../merchandising_details/requires/product_sub_department_controller','setFilterGrid("list_view",-1)');
		reset_form('subdepartment_1','','');
		set_button_status(0, permission, 'fnc_product_department',1);
		release_freezing();
	}
}
 

</script>
<body onLoad="set_hotkey()">
   <div align="center" style="width:100%;">  
	 <? echo load_freeze_divs ("../../",$permission);  ?> 
        <fieldset style="width:600px; margin-top:10px;">
           <legend>Department Information</legend>
             <form id="subdepartment_1"  name="subdepartment_1" autocomplete="off" >  
               <table width="100%" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                        <td colspan="4">&nbsp;</td>
                    </tr>
                  <tr>
                     <td width="150" class="must_entry_caption" >Sub Department Name:</td>
                     <td align="left" width="220">
                     <input type="text" name="txt_sub_dep_name"  id="txt_sub_dep_name"   sytle="width:180px" class="text_boxes" />
                     </td>
                     <td></td>
                     <td></td>
                  </tr> 
                  <tr>
                     <td width="140" class="must_entry_caption" >Departnemt Name:</td>
                     <td>
                         <? 
							echo create_drop_down( "cbo_department_id", 165, $product_dept, 0, 1, "-Select-",$selected, "", "", "" );
							//echo create_drop_down( "cbo_item_category_id", 150, $item_category, 0, 1, "-- Select Category --", $selected, "", "", "", "", "", "1,2,3,4,12,13,14" ); 
					     ?>
					 
                     </td>
                     <td width="80" class="must_entry_caption">Buyer Name:</td>
                     <td>
                          <? echo create_drop_down( "cbo_buyer_id", 170, "select buyer_name,id from lib_buyer where is_deleted=0  and 
                    status_active=1 order by buyer_name", "id,buyer_name", 1, '--Select--', $selected  );
					
					//cbo_company_id",150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected,
					 ?>
              
                     </td>
                  </tr> 
                  <tr>
                  </tr>
                  <tr>
                    <td><input type="hidden" id="update_id" name="update_id"></td>
                  </tr>
                  <tr>
                        <td colspan="4">&nbsp;</td>
                    </tr>
                  <tr>
                     <td colspan="4" align="center" class="button_container" >
                       <? 
                         echo load_submit_buttons( $permission, "fnc_product_department", 0,0 ,"reset_form('subdepartment_1','','','','','')");
                       ?> 
                    </td>
                  </tr>
                  <tr>
                        <td colspan="4">&nbsp;</td>
                    </tr>
                   <tr>
                      <td colspan="4" id="sub_department_list_view" >
                      <?
						$arr=array (1=>$product_dept);
						echo  create_list_view ( "list_view", "Sub Department Name,Department, Buyer", "200,200,150","620","220",1, "
SELECT a.sub_department_name, a.department_id, b.buyer_name, a.id FROM lib_pro_sub_deparatment a, lib_buyer b WHERE a.buyer_id = b.id AND a.status_active =1 AND a.is_deleted =0", "get_php_form_data", "id", "'load_php_data_to_form'", 1,"0,department_id,0", $arr, "sub_department_name,department_id,buyer_name", "../merchandising_details/requires/product_sub_department_controller", 'setFilterGrid("list_view",-1);',''); 
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
