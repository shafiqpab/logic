<?
/*-------------------------------------------- Comments -----------------------
Version (MySql)          :  V2
Version (Oracle)         :  V1
Converted by             :  MONZU
Converted Date           :  24-05-2014
Purpose			         : 	This Form Will Create Woven Garments Price Quotation Entry.
Functionality	         :	
JS Functions	         :
Created by		         :	Monzu 
Creation date 	         : 	18-10-2012
Requirment Client        : 
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                   
DB Script                : 
Updated by 		         : 		
Update date		         : 		   
QC Performed BY	         :		
QC Date			         :	
Comments		         :
-------------------------------------------------------------------------------*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Stripe Color Info","../../", 1, 1, $unicode,'','');
?>	
<script type="text/javascript">
// common for all----------------------------
/*$(window).bind('beforeunload', function(){
		return '>>>>>Before You Go<<<<<<<< \n Your custom message go here';
	});*/
/*var index_page=$('#index_page', window.parent.document).val();
if(index_page !=1)
{
	index_page=<? //echo $index_page;?>;
}
if(index_page !=1) window.location.href = "../../logout.php"; */
var permission = '<? echo $permission; ?>';
//Master form---------------------------------------------------------------------------
function openmypage(page_link,title)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		if(title=="Job/Order Selection Form")
		{
			var action="populate_data_from_job_table";	
		}
		var theform=this.contentDoc.forms[0]
		var theemail=this.contentDoc.getElementById("selected_job")
		if (theemail.value!="")
		{
			freeze_window(5);
			get_php_form_data( theemail.value, action, "requires/copy_job_controller" );
			release_freezing();
		}
	}
}

function fnc_copy_job()
{
	var txt_job_no=document.getElementById('txt_job_no').value;
	if (form_validation('txt_job_no*cbo_company_name','Job No*Company Name')==false)
	{
		return;
	}
	else
	{
		var data="action=save_update_delete_copy_job"+get_submitted_data_string('txt_job_no*cbo_company_name',"../../");
		freeze_window(0);
		http.open("POST","requires/copy_job_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_copy_job_reponse;
	}

}
function fnc_copy_job_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);
		//alert(reponse[1])
		document.getElementById('txt_new_job_no').value  = reponse[1];
		//set_button_status(1, permission, 'fnc_quotation_entry_dtls',2);
		release_freezing();
	}
}
</script>

</head>
 
<body onLoad="set_hotkey()" >
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",$permission);  ?>
       
         <fieldset style="width:1070px;">
         <legend>Pre-Costing</legend>
        <table width="90%" cellpadding="0" cellspacing="2" align="center">
            <tr>
                <td width="100%" align="left" valign="top">
                   
                        <form name="precosting_1" id="precosting_1" autocomplete="off"> 
                            <div style="width:1070px;">  
                                <table  width="100%" cellspacing="2" cellpadding=""  border="0">
                                    <tr>
                                        <td align="right" width="120" class="must_entry_caption">Job No</td>
                                        <td  width="150">
                                        <input  style="width:150px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/copy_job_controller.php?action=order_popup','Job/Order Selection Form')" class="text_boxes" placeholder=" Search Job No" name="txt_job_no" id="txt_job_no" readonly />                    
                                        </td>
                                        <td  align="right"  width="150">Company</td>
                                        <td  width="150">
                                        <?
                                        echo create_drop_down("cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/copy_job_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );");
                                        ?>
                                        </td>                                        
                                        <td align="right">Buyer</td>
                                        <td id="buyer_td" >
                                        <? 
                                        echo create_drop_down( "cbo_buyer_name", 160, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                                        ?>
                                        </td>
                                       
                                        <td align="right"  width="120">Style Ref</td>
                                        <td>
                                        <input class="text_boxes" type="text" style="width:150px;" name="txt_style_ref" id="txt_style_ref" maxlength="75" title="Maximum 75 Character" readonly/>
                                        <input type="hidden" id="update_id" value="" />
                                        </td>
                                    </tr>
                                     
                                    
                                    <tr>
                                        <td width="100%" align="center" valign="top" id="cost_container" colspan="8" >
                                        New Job No: <input  style="width:150px;" type="text"  class="text_boxes"  name="txt_new_job_no" id="txt_new_job_no" readonly /> 
                                        </td>
                                        </tr>
                                    <tr> 
                                    
                                        <td align="center" valign="middle" class="button_container" colspan="8"> 
                                        <input type="button" id="copyjob" name="copyjob" value="Copy" class="formbutton" style="width:100px" onClick="fnc_copy_job()">
										<?
										//echo load_submit_buttons( $permission, "fnc_precosting_entry", 0,0 ,"reset_form('precosting_1','','','')",1,1) ;
										?>  
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            </form>
                </td>
            </tr>
            
        </table> 
          </fieldset>
    </div>
    <div id="stripe_color_list_view_container">
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
