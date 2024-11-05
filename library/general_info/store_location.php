<?
/*-------------------------------------------- Comments

Purpose     :   This form will create Store Location of a company.

Functionality : First create Store Location and save.
          select a team from list view for update.

JS Functions  :

Created by    : Monzu 
Creation date   :   07-10-2012
Updated by    :     
Update date   :        

QC Performed BY :   

QC Date     : 

Comments    :

*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Store Location", "../../", 1, 1,$unicode,1,'');

?>

<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>'; 
    
function fnc_store_location( operation )
{
  if (form_validation('txt_store_name*cbo_company_name*cbo_store_location*cbo_catagory_item','Store Name*Company Name*Lacation Name*Item Category')==false)
  {
    return;
  }
  else
  {
	if($('#txt_is_textile').is(':checked'))
	{
		var txt_is_textile=1;
	}
	else
	{
		var txt_is_textile=0;
	}
    eval(get_submitted_variables('txt_store_name*cbo_company_name*cbo_store_location*cbo_catagory_item*cbo_status*update_id'));

    var txt_storeLocationName=$( "#cbo_store_location option:selected" ).text();
    var data="action=save_update_delete&operation="+operation+"&txt_storeLocationName="+txt_storeLocationName+"&txt_is_textile="+txt_is_textile+get_submitted_data_string('txt_store_name*cbo_company_name*cbo_store_location*cbo_catagory_item*cbo_status*update_id',"../../");
    freeze_window(operation);
    http.open("POST","requires/store_location_controller.php",true);
    http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    http.send(data);
    http.onreadystatechange = fnc_store_location_response;
  }
}

function fnc_store_location_response()
{
  if(http.readyState == 4) 
  {
    
    var response=trim(http.responseText).split('**');
    if (response[0].length>2) response[0]=10;
    if(response[0]==10){
		
      show_msg(response[0]);
      release_freezing();
      return;
	  
    } else if(response[0]==13){
		
      alert("Delete restricted, This Information is used in another Page.");
      release_freezing();
      return;
	  
    }
    
    show_msg(response[0]);
    show_list_view(response[1],'store_location_list_view','store_location_list_view','../general_info/requires/store_location_controller','setFilterGrid("list_view",-1)');
    reset_form('storelocation_1','','');
    set_button_status(0, permission, 'fnc_store_location',1);
    release_freezing();
  }
}
  </script>

</head>

<body onLoad="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission);  ?>
<div align="center" style="width:100%;">
  
        <fieldset style="width:600px;">
         <legend>Store Location</legend>
            <form name="storelocation_1" id="storelocation_1" autocomplete = "off"> 
              <table cellpadding="0" cellspacing="2" width="100%">
                <tr>
                    <td width="121" class="must_entry_caption">Store Name </td>
                    <td width="375"><input type="text" name="txt_store_name" id="txt_store_name" class="text_boxes" style="width:212px"  maxlength="50" title="Maximum 50 Character"/></td>            
                </tr>
                <tr>
                    <td width="121" class="must_entry_caption">Company Name</td>
                    <td>
                      <?
                          /*echo create_drop_down( "cbo_company_name", 224, "select id,company_name from lib_company comp where is_deleted=0  and status_active=1 $company_cond order by company_name","id,company_name", 1, "--- Select Company ---", 1, "" );*/

                            echo create_drop_down( "cbo_company_name", 224, "select company_name,id from lib_company comp where is_deleted=0  and status_active=1 $company_cond  order by company_name",'id,company_name', 1, '--- Select Company ---', 0, "load_drop_down( 'requires/store_location_controller', this.value, 'load_drop_down_location', 'location' )" );
                      ?>
                              <input type="hidden" name="update_id" id="update_id">
                    </td>                
                </tr>
                 <tr>
                    <td width="150" class="must_entry_caption">Location</td>
                    <td colspan="2" id="location">  
                    <? 
                      echo create_drop_down( "cbo_store_location", 224, $blank_array,'id,location_name', 1, '--- Select Location ---', 0, "" );
                    ?>
                    </td>
                </tr>    
               
                 <tr>
                    <td width="121" class="must_entry_caption">Item Category</td>
                    <td>
                    <? 
                    echo create_drop_down( "cbo_catagory_item", 224, $item_category, 0, "", 1, "" );
                    ?>  
                  </td>
                </tr>
                <tr>
                    <td width="121">Status</td>
                    <td>
                    <?
                    echo create_drop_down( "cbo_status", 224, $row_status,"", "", "", 1, "" );
                    ?>
                    </td>
                </tr>
				<tr>
                    <td width="121">Textile Store</td>
                    <td> <input type="checkbox" id="txt_is_textile" name="txt_is_textile" ></td> 
                </tr>
                <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                    <tr>
                          <td align="center" colspan="2" class="button_container">
                           <? 
                           echo load_submit_buttons( $permission, "fnc_store_location", 0,0 ,"reset_form('storelocation_1','','')",1);
                          ?>                   
                           </td>
                    </tr>
              </table>
            </form>
          <fieldset style="margin-top:20px">
            <legend>Store Location List View</legend>
              <table cellpadding="0" cellspacing="2" width="100%">          
                  <tr>
                      <td align="center" id="store_location_list_view">      
                         <?
                        $companyarr=return_library_array( "select id, company_name from lib_company",'id','company_name');
						$arr=array (1=>$companyarr);

						 echo  create_list_view ( "list_view", "Store Name,Company Name,Location Name", "120,120,220,","530","220",0, "select id,store_name,company_id,store_location from  lib_store_location where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,company_id,0", $arr , "store_name,company_id,store_location", "../general_info/requires/store_location_controller", 'setFilterGrid("list_view",-1);' ) ; 
						 
						 
                         //echo  create_list_view ( "list_view", "Store Name,Company Name,Location Name", "120,120,220,","530","220",0, "select id,store_name,company_id,store_location from  lib_store_location where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,company_id,0", $arr , "store_name,company_id,store_location", "../general_info/requires/store_location_controller", 'setFilterGrid("list_view",-1);' ) ;
                         ?>
                      </td>
                  </tr>            
              </table>
        </fieldset>
        </fieldset>
        </div>
  </body>
    
<script>set_multiselect('cbo_catagory_item','0','0','','');</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
