<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create  for HnM Calendar Entry
Functionality	:	
JS Functions	:
Created by		:	Md Mamun Ahmed Sagor
Creation date 	: 	25-08-2022
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
 echo load_html_head_contents("HnM Calendar Entry", "../../", 1, 1,$unicode,'','');
 $weekArr=array();
 for($i=1;$i<=53;$i++){
  $weekArr[$i]="week-".$i;
}
?>
<script type="text/javascript" charset="utf-8">

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';

function fnc_hnm_calender( operation )
{

    
  if(form_validation('cbo_buyer_id*cbo_year*cbo_week_id','Buyer Name*Year*Week Name')==false)
		{
			return;
		}

   
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_buyer_id*cbo_year*txt_from_date*txt_to_date*cbo_week_id*update_id',"../../");

    $.ajax({
						url: 'requires/hnm_calendar_entry_controller.php',
						type: 'POST',
						data: data,
						success: function(data){
              reset_form('subdepartment_1','','');
		          set_button_status(0, permission, 'fnc_hnm_calender',1);
              show_list_view('','sub_department_list_view','sub_department_list_view','../merchandising_details/requires/hnm_calendar_entry_controller','setFilterGrid("list_view",-1)');

							release_freezing();
						}
					});


}

</script>
<body onLoad="set_hotkey()">
   <div align="center" style="width:100%;">  
	 <? echo load_freeze_divs ("../../",$permission);  ?> 
        <fieldset style="width:550px; margin-top:10px;">
           <legend>Hnm Calender Information</legend>
             <form id="subdepartment_1"  name="subdepartment_1" autocomplete="off" >  
               <table width="100%" border="0" cellpadding="0" cellspacing="0">
                 
                    <tr>
                     <td width="120" class="must_entry_caption"  align="center">Buyer</td>
                     <td  width="80" align="center">Year</td>
                     <td width="90" align="center">From Date</td>
                     <td width="90" align="center">To Date</td>
                     <td width="100" align="center">Weak</td>
                  </tr> 
                  <tr>
                     <td width="120" class="must_entry_caption"  align="center"><? echo create_drop_down( "cbo_buyer_id", 120, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id    and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, ""); ?></td>
                     <td  width="80" align="center"><? 	echo create_drop_down( "cbo_year", 80, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );  //date("Y",time()) ?></td>
                     <td  width="100" align="center"><input type="text" name="txt_from_date" class="datepicker"  id="txt_from_date"  style="width: 90px;"  /></td>
                     <td  width="100" align="center"> <input type="text" name="txt_to_date"  class="datepicker" id="txt_to_date"   style="width: 90px;" /> 
                     <input type="hidden" id="update_id" name="update_id" value=""></td>
                     <td width="100" id="week_id" align="center"><?  	echo create_drop_down( "cbo_week_id", 100, $weekArr, 0, 1, "-Select-",$selected, "", "", "" );	 ?>
                    
                    </td>
                  </tr> 
                 
                 
                  <tr>
                        <td colspan="5">&nbsp;</td>
                    </tr>
                  <tr>
                     <td colspan="5" align="center" class="button_container" >
                       <? 
                         echo load_submit_buttons( $permission, "fnc_hnm_calender", 0,0 ,"reset_form('subdepartment_1','','','','','')");
                       ?> 
                    </td>
                  </tr>
                  <tr>
                        <td colspan="5">&nbsp;</td>
                    </tr>
                   <tr>
                      <td colspan="5" id="sub_department_list_view" align="center">
                      <?
               
                     $arr=array(2=>$weekArr);
				        		echo  create_list_view ( "list_view", "Buyer Name,Year, Week", "120,80,100","450","220",1, "SELECT a.year, a.week, b.buyer_name, a.id FROM lib_hnm_calendar a, lib_buyer b WHERE a.buyer_id = b.id AND a.status_active =1 AND a.is_deleted =0  order by id", "get_php_form_data", "id", "'load_php_data_to_form'", 1,"0,0,week", $arr, "buyer_name,year,week", "../merchandising_details/requires/hnm_calendar_entry_controller", 'setFilterGrid("list_view",-1);',''); 
                      ?>
                    </td>
                </tr>
                          
          </table>
       		</form>
       		</fieldset>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<!-- load_drop_down('requires/hnm_calendar_entry_controller', this.value, 'load_drop_down_week', 'week_id' ); -->
</html>
