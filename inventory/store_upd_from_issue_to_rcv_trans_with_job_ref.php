<?
/*-------------------------------------------- Comments
Purpose			: 	This Form Will Create Grey Store Revaluation
				
Functionality	:	
JS Functions	:
Created by		:	
Creation date 	: 	19-11-2019
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
	if(form_validation('cbo_item_category_id*cbo_company_id*txt_job_no','Item Category*Company Name*Job No')==false)
	{
		return;
	}
	
	var cbo_company_id=$('#cbo_company_id').val();
	var item_category_id=$('#cbo_item_category_id').val();
	var txt_job_no=$('#txt_job_no').val();
	var cbo_year=$('#cbo_year').val();
	
	var data="action=synchronize_stock&cbo_company_id="+cbo_company_id+"&item_category_id="+item_category_id+"&cbo_year="+cbo_year+"&txt_job_no="+txt_job_no;
	freeze_window(3);
	http.open("POST","requires/store_upd_from_issue_to_rcv_trans_with_job_ref_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fn_store_revaluation_reponse;
}

function fn_store_revaluation_reponse()
{	
	if(http.readyState == 4) 
	{	 
		var response=trim(http.responseText);
		$("#report_container").html(response);
		//alert(response);
		release_freezing();
	}
} 

function openmypage()
{
	var companyID = $("#cbo_company_id").val();
	var cbo_year = $("#cbo_year").val();
	var item_category = $("#cbo_item_category_id").val();

	if( form_validation('cbo_company_id*cbo_year','Company Name*Year')==false )
	{
		return;
	}

	var popup_width='700px';
	var page_link='requires/store_upd_from_issue_to_rcv_trans_with_job_ref_controller.php?action=job_no_popup&companyID='+companyID+'&cbo_year='+cbo_year;
	var title='Job No Search';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=370px,center=1,resize=1,scrolling=0','');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var txt_job_no=this.contentDoc.getElementById("txt_job_no").value;
		$('#txt_job_no').val(txt_job_no);
	}
}
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../",$permission);  ?>    		 
    <form name="storeRevaluation_1" id="storeRevaluation_1" autocomplete="off" > 
    <div style="width:100%;" align="center">
        <fieldset style="width:500px;">
            <table class="rpt_table" width="500" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr> 	 	
                        <th class="must_entry_caption">Company</th> 
                        <th class="must_entry_caption">Item Category</th>                               
                        <th class="must_entry_caption">Year</th>
                        <th class="must_entry_caption">Job No</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" /></th>
                    </tr>
                </thead>
                <tr class="general">
                    <td>
                        <?
                        	echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", $selected, "",0 );
                        ?>                            
                    </td>
                    <td> 
                        <? echo create_drop_down("cbo_item_category_id",150, $item_category,"",0,"--- Select Item Category ---",13,"",1,'13','',"",""); ?>
                    </td>
                    <td> 
                        <? echo create_drop_down( "cbo_year", 70, create_year_array(),"", 0,"-- All --", date("Y",time()), "",0,"" ); ?>
                    </td>
                    <td>
                        <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes_numeric" style="width:100px" onDblClick="openmypage();" placeholder="Browse" readonly />
                    </td>
                    <td>
                        <input type="button" name="search" id="search" value="Revaluation" onClick="fn_store_revaluation()" style="width:100px" class="formbutton" />
                    </td>
                </tr>
            </table> 
        </fieldset> 
        <br>
        <div id="report_container" align="center"></div>
    </div>
    </form>    
</div>  
</body> 
<script src="../includes/functions_bottom.js" type="text/javascript"></script>  
</html>
