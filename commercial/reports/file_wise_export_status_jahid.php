<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create File Wise Export Status Report.
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	08-02-2014
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

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("File Wise Export Report", "../../", 1, 1,'','','');
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
 
	var permission = '<? echo $permission; ?>';
	
function generate_report()
{
	if(form_validation('cbo_company_name*hide_year*txt_file_no','Company Name*Year*File No')==false)
	{
	return;
	}
	
	var report_title=$( "div.form_caption" ).html();	
	var data="action=report_generate"+get_submitted_data_string("cbo_company_name*cbo_buyer_name*cbo_lein_bank*txt_file_no*hide_year","../../")+'&report_title='+report_title;
	freeze_window(3);
	http.open("POST","requires/file_wise_export_status_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fn_report_generated_reponse;
}
	
function fn_report_generated_reponse()
{
	if(http.readyState == 4) 
	{
		var response=trim(http.responseText).split("####");
		//alert(http.responseText);return;
		$('#report_container2').html(response[0]);
		//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
		document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		show_msg('3');
		release_freezing();
	}
}

function new_window()
	{
/*		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
*/		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
/*		document.getElementById('scroll_body').style.overflowY="auto";
		document.getElementById('scroll_body').style.maxHeight="auto";
*/	}
function openmypage_file_info()
{
	var company_id=document.getElementById('cbo_company_name').value;
	var buyer_id=document.getElementById('cbo_buyer_name').value;
	var lien_bank=document.getElementById('cbo_lein_bank').value;
	var cbo_year=document.getElementById('hide_year').value;
	//alert(buyer_id);
	page_link='requires/file_wise_export_status_controller.php?action=file_popup&company_id='+company_id+'&buyer_id='+buyer_id+'&lien_bank='+lien_bank+'&cbo_year='+cbo_year;
	if(form_validation('cbo_company_name*hide_year','Company Name*Year')==false)
	{
		return;
	}
	else
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Select File", 'width=600px,height=390px,center=1,resize=0,scrolling=0','../')
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var file_no=this.contentDoc.getElementById("hide_file_no").value.split(",");//alert(item_description_all); 
			//alert(file_no[0]);
			document.getElementById('txt_file_no').value=file_no[0];
		}
	}
}


function openmypage(sub_id)
{
	page_link='requires/file_wise_export_status_controller.php?action=acount_head_details'+'&sub_id='+sub_id;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Account Head Details', 'width=600px,height=350px,center=1,resize=0,scrolling=0','../');
	emailwindow.onclose=function()
	{
		//alert("Jahid");
	}
}


function btb_open(action,btb_id,file_buyer)
{
	page_link='requires/file_wise_export_status_controller.php?action='+action+'&hidden_btb_id='+btb_id+'&file_buyer='+file_buyer;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Select File", 'width=833px,height=390px,center=1,resize=0,scrolling=0','../')
}


</script>
</head>
 
<body onLoad="set_hotkey();">
 <div style="width:1000px" align="center">
    <form id="file_wise_explort_import_status" action="" autocomplete="off" method="post">
            <? echo load_freeze_divs ("../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:920px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel" style="width:920px;"> 

        <fieldset style="width:100%" >
            <table class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" width="900">
                <thead>
                    <th class="must_entry_caption" width="170px">Company Name</th> 
                    <th width="170px">Buyer</th>
                    <th width="170px">Lien Bank</th>
                    <th class="must_entry_caption" width="100px">Year</th>
                    <th class="must_entry_caption" width="170px">File No</th>
                    <th align="center"><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:100px" onClick="reset_form('file_wise_explort_import_status','report_container*report_container2','','','')" /></th>
               </thead>
                <tr class="general">                           
                    <td align="center">
                       <?
                        	echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/file_wise_export_status_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/file_wise_export_status_controller',this.value, 'load_drop_down_lc_year', 'lc_year_td' );" );
                        ?>
                    </td>
                    <td align="center" id="buyer_td">
					<? 
                    	echo create_drop_down( "cbo_buyer_name", 170, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                    ?>
                    </td>
                     <td align="center">
					<? 
                    	echo create_drop_down( "cbo_lein_bank", 170, "select bank_name,id from lib_bank where is_deleted=0  and status_active=1 and lien_bank=1 order by bank_name","id,bank_name", 1, "-- All Bank --", $selected, "",0,"" );
                    ?>
                    </td>
                    <td id="lc_year_td">
                    <?
					echo create_drop_down( "hide_year", 100,$blank_array,"", 1, "-- Select --", 1,"");
					?>
                    </td>
                     <td align="left">
                     <input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes" onDblClick="openmypage_file_info();" placeholder="Browse Or Write" style="width:90%" />
                     </td>
                    <td align="center"><input type="button" name="show" id="show" onClick="generate_report();" class="formbutton" style="width:100px" value="Show" /></td>
                </tr>
             </table>
        </fieldset>
        </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
        </form>
    </div>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
