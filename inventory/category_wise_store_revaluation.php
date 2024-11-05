<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Category Store Revaluation
				
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	07-03-2021
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

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Category Store Revaluation Info","../", 1, 1, $unicode);
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

function fn_store_revaluation()
{
	if(form_validation('cbo_company_id*cbo_item_category_id','Company*Category')==false)
	{
		return;
	}
	
	var cbo_company_id=$('#cbo_company_id').val();
	var cbo_item_category_id=$('#cbo_item_category_id').val();
	var txt_item_account_id=$('#txt_item_account_id').val();
	
	var data="action=store_revaluation&cbo_company_id="+cbo_company_id+"&cbo_item_category_id="+cbo_item_category_id+"&txt_item_account_id="+txt_item_account_id;
	freeze_window(3);
	http.open("POST","requires/category_wise_store_revaluation_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fn_store_revaluation_reponse;
}

function fn_store_revaluation_reponse()
{	
	if(http.readyState == 4) 
	{	 
		var response=trim(http.responseText);
		alert(response);
		release_freezing();
	}
}


function openmypage()
{
	if (form_validation('cbo_company_id*cbo_item_category_id','Item Category')==false)
	{
		return;
	}
	
	var cbo_company_id=$('#cbo_company_id').val();
	var item_category_id=$('#cbo_item_category_id').val();
	var txt_item_acc=$('#txt_item_acc').val();
	var txt_item_account_id=$('#txt_item_account_id').val();
	var txt_item_acc_no=$('#txt_item_acc_no').val();
	var page_link='requires/category_wise_store_revaluation_controller.php?action=item_desc_popup&item_category_id='+item_category_id+'&cbo_company_id='+cbo_company_id+'&txt_item_acc='+txt_item_acc+'&txt_item_account_id='+txt_item_account_id+'&txt_item_acc_no='+txt_item_acc_no;
	var title='Item Search';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1060px,height=400px,center=1,resize=1,scrolling=0','');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var item_acc_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
		var item_acc_des=this.contentDoc.getElementById("txt_selected").value; // product Description
		var item_acc_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
		//alert(style_no);
		$("#txt_item_acc").val(item_acc_des);
		$("#txt_item_account_id").val(item_acc_id); 
		$("#txt_item_acc_no").val(item_acc_no);
	}
}




</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../",$permission);  ?>    		 
    <form name="storeRevaluation_1" id="storeRevaluation_1" autocomplete="off" > 
    <div style="width:100%;" align="center">
        <fieldset style="width:650px;">
            <table class="rpt_table" width="750" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr> 	 	
                        <th class="must_entry_caption">Company</th> 
                        <th class="must_entry_caption">Item Category</th> 
                        <th>Product ID</th>                              
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" /></th>
                    </tr>
                </thead>
                <tr class="general">
                    <td>
                        <? 
                            echo create_drop_down( "cbo_company_id", 200, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company--", $selected, "",0 );
                        ?>                            
                    </td>
                    <td> 
                        <? echo create_drop_down("cbo_item_category_id",200,$item_category,"",1,"--- Select Item Category ---",$selected,"",0,'','',"","1,2,3,5,6,7,23,12,13,14,24,25,28,30,31,42,43,71,72,73,74,75,76,77,78,79,80,81,82,83,84,86,95,96,98,100,102,103,104,105,108,109,110,111,112"); ?>
                    </td>
                    <td>
                        <input type="text" id="txt_item_account_id" name="txt_item_account_id" class="text_boxes" style="width:200px" onDblClick="openmypage();" placeholder="Brouse" readonly />
                        <input type="hidden" name="txt_item_acc" id="txt_item_acc" />   
                        <input type="hidden" name="txt_item_acc_no" id="txt_item_acc_no"/>
                    </td>
                    <td>
                        <input type="button" name="search" id="search" value="Revaluation" onClick="fn_store_revaluation()" style="width:100px" class="formbutton" />
                    </td>
                </tr>
            </table> 
        </fieldset> 
    </div>
    </form>    
</div>  
</body> 
<script src="../includes/functions_bottom.js" type="text/javascript"></script>  
</html>
