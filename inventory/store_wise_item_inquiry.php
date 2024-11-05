<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Store Item Inquiry
				
Functionality	:	
JS Functions	:
Created by		:	Fuad
Creation date 	: 	24-08-2015
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
echo load_html_head_contents("Store Item Inquiry Info","../", 1, 1, $unicode);
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

function generate_report()
{
	if( form_validation('cbo_company_id*cbo_item_category_id*txt_product_id','Company Name*Item Category*Product Id')==false )
	{
		return;
	}
	var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_item_category_id*txt_product_id*variable_lot',"../");
	
	freeze_window(3);
	http.open("POST","requires/store_wise_item_inquiry_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fn_report_generated_reponse;
}

function fn_report_generated_reponse()
{	
	if(http.readyState == 4) 
	{	 
 		var response=trim(http.responseText).split("**");
		$("#report_container2").html(response[0]);  
		show_msg('3');
		release_freezing();
	}
}

function synchronize_stock(prod_id)
{
	var variable_lot=$('#variable_lot').val();
	var data="action=synchronize_stock&prod_id="+prod_id+'&variable_lot='+variable_lot;
	freeze_window(3);
	http.open("POST","requires/store_wise_item_inquiry_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fn_synchronize_stock_reponse;
}

function fn_synchronize_stock_reponse()
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
	var companyID = $("#cbo_company_id").val();
	var item_category = $("#cbo_item_category_id").val();
	
	if( form_validation('cbo_company_id*cbo_item_category_id','Company Name*Item category')==false )
	{
		return;
	}
	
	var popup_width='700px';
	
	var page_link='requires/store_wise_item_inquiry_controller.php?action=item_desc_popup&companyID='+companyID+'&item_category_id='+item_category;
	var title='Item Search';
	popup_width='520px';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=370px,center=1,resize=1,scrolling=0','');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var hide_data=this.contentDoc.getElementById("hide_data").value;
		$('#txt_product_id').val(hide_data);
	}
} 
 
 
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../",$permission);  ?>    		 
    <form name="storeItemInquiry_1" id="storeItemInquiry_1" autocomplete="off" > 
    <div style="width:100%;" align="center">
        <h3 style="width:730px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div style="width:100%;" id="content_search_panel">
            <fieldset style="width:730px;">
                <table class="rpt_table" width="730" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th class="must_entry_caption">Company</th> 
                            <th class="must_entry_caption">Item Category</th>                               
                            <th class="must_entry_caption">Product Id</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td align="center">
						<? 
                            echo create_drop_down( "cbo_company_id", 180, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "get_php_form_data(this.value, 'populate_data_lib_data', 'requires/store_wise_item_inquiry_controller');reset_form('','','cbo_item_category_id*txt_product_id','','','');" );
                        ?>
                        <input type="hidden" id="variable_lot" name="variable_lot*" />                            
                        </td>
                        <td align="center"> 
							<? echo create_drop_down( "cbo_item_category_id", 180, $item_category,"",1, "--- Select Item Category ---", $selected,"",0,"5,6,7,23","","",""); ?>
                        </td>
                        <td align="center">
                            <input type="text" id="txt_product_id" name="txt_product_id" class="text_boxes_numeric" style="width:150px" onDblClick="openmypage();" placeholder="Browse/Write" />
                        </td>
                        <td align="center">
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:100px" class="formbutton" />
                        </td>
                    </tr>
                </table> 
            </fieldset> 
		</div>
    </div>
    <br /> 
    <div id="report_container2" style="margin-left:5px"></div> 
    </form>    
</div>  
</body> 
<script src="../includes/functions_bottom.js" type="text/javascript"></script>  
</html>
