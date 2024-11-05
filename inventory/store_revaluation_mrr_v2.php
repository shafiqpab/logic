<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Store Revaluation
				
Functionality	:	
JS Functions	:
Created by		:	Fuad
Creation date 	: 	06-09-2016
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
echo load_html_head_contents("Store Revaluation Info","../", 1, 1, $unicode);
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

function fn_store_revaluation()
{
	if(form_validation('cbo_item_category_id*txt_item_desc*txt_avg_rate*txt_mrr_no','Item Category*Product Name*New Avg. Rate (Tk.)*MRR NO')==false)
	{
		return;
	}
	
	var prod_id=$('#txt_product_id').val();
	var item_category_id=$('#cbo_item_category_id').val();
	var avg_rate=$('#txt_avg_rate').val();
	var effective_date=$('#txt_effective_date').val();
	var txt_mrr_no=$('#txt_mrr_no').val();
	var txt_received_id=$('#txt_received_id').val();
	
	
	var data="action=store_revaluation&prod_id="+prod_id+"&item_category_id="+item_category_id+"&avg_rate="+avg_rate+"&effective_date="+effective_date+"&txt_mrr_no="+txt_mrr_no+"&txt_received_id="+txt_received_id;
	freeze_window(3);
	http.open("POST","requires/store_revaluation_mrr_v2_controller.php",true);
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
	if (form_validation('cbo_item_category_id','Item Category')==false)
	{
		return;
	}
	
	var item_category_id=$('#cbo_item_category_id').val();
	var page_link='requires/store_revaluation_mrr_v2_controller.php?action=item_desc_popup&item_category_id='+item_category_id;
	var title='Item Search';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=370px,center=1,resize=1,scrolling=0','');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var hide_data=this.contentDoc.getElementById("hide_data").value;
		var datas=hide_data.split("_");
		
		$('#txt_product_id').val(datas[0]);
		$('#txt_item_desc').val(datas[1]);
		$('#cbo_company_id').val(datas[2]);
		$('#txt_avg_rate').val('');
		$('#txt_mrr_no').val('');
		$('#txt_received_id').val('');
	}
}

function clear_fld()
{
	$('#txt_product_id').val('');
	$('#txt_item_desc').val('');
	$('#cbo_company_id').val('');
	$('#txt_avg_rate').val('');
	$('#txt_mrr_no').val('');
	$('#txt_received_id').val('');
}


function open_mrrpopup()
{
	if( form_validation('cbo_company_id*cbo_item_category_id*txt_item_desc','Company Name*Item Category*Item')==false )
	{
		return;
	}
	var company = $("#cbo_company_id").val();
	var item_category_id = $("#cbo_item_category_id").val();
	var txt_product_id = $("#txt_product_id").val();	
	var page_link='requires/store_revaluation_mrr_v2_controller.php?action=mrr_popup&company='+company+'&item_category_id='+item_category_id+'&txt_product_id='+txt_product_id; 
	var title="Search MRR Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=370px,center=1,resize=0,scrolling=0',' ')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var mrrNumber=this.contentDoc.getElementById("hidden_recv_number").value.split("_"); 
		$("#txt_mrr_no").val(mrrNumber[1]);
		$("#txt_received_id").val(mrrNumber[0]);
	}
}

</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../",$permission);  ?>    		 
    <form name="storeRevaluation_1" id="storeRevaluation_1" autocomplete="off" > 
    <div style="width:100%;" align="center">
        <fieldset style="width:950px;">
            <table class="rpt_table" width="950" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr> 	 	
                        <th>Company</th> 
                        <th class="must_entry_caption">Item Category</th>                               
                        <th class="must_entry_caption">Product Description</th>
                        <th>Product Id</th>
                        <th class="must_entry_caption">New Avg. Rate (Tk.)</th>
                        <th class="must_entry_caption">MRR NO</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" /></th>
                    </tr>
                </thead>
                <tr class="general">
                    <td>
                        <? 
                        echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Display --", $selected, "",1 );
                        ?>                            
                    </td>
                    <td> 
                        <? echo create_drop_down("cbo_item_category_id",150,$item_category,"",1,"--- Select Item Category ---",$selected,"clear_fld()",0,'','',"","12,24,25,28"); ?>
                    </td>
                    <td>
                        <input type="text" id="txt_item_desc" name="txt_item_desc" class="text_boxes" style="width:180px"onDblClick="openmypage();" placeholder="Browse" readonly />
                    </td>
                    <td>
                        <input type="text" id="txt_product_id" name="txt_product_id" class="text_boxes_numeric" style="width:70px" placeholder="Display" readonly disabled />
                    </td>
                    <td>
                        <input type="text" id="txt_avg_rate" name="txt_avg_rate" class="text_boxes_numeric" style="width:80px" placeholder="Write"/>
                    </td>
                    <td>
                        <input type="text" id="txt_mrr_no" name="txt_mrr_no" class="text_boxes" style="width:120px;" onDblClick="open_mrrpopup()" placeholder="Browse" readonly />
                        <input type="hidden" name="txt_received_id" id="txt_received_id" readonly /> 
                        <input type="text" id="txt_effective_date" name="txt_effective_date" class="datepicker" style="width:80px;display:none;" value="<? //echo date("d-m-Y"); ?>" readonly />
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
