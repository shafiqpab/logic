<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create LC Wise Payment Status
				
Functionality	:	
JS Functions	:
Created by		:	Nayem
Creation date 	: 	02-01-2022
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
echo load_html_head_contents("LC Wise Payment Status","../../", 1, 1, $unicode,1,1); 
?>	
<script>
    var permission='<? echo $permission; ?>';
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	function generate_report(operation)
	{
		if(form_validation('cbo_company_id*txt_lc_sc_no','Company Name*Export LC No or SC No')==false)
		{
			return;
		}
		else
		{	
            if(operation==1){
                var action = 'report_generate';
            }
            else{
                var action = 'report_generate2';
            }
			var report_title=$( "div.form_caption" ).html();
			var data="action="+action+get_submitted_data_string('cbo_company_id*cbo_search_by*txt_lc_sc_no*txt_lc_sc_id',"../../")+'&report_title='+report_title+'&report_type='+operation;
            // alert(data);return;

			freeze_window(3);
			http.open("POST","requires/lc_wise_payment_status_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####");
			$('#report_container2').html(reponse[0]);
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	 		show_msg('3');
			release_freezing();
		}
	}

    function new_window()
    {
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
        d.close();         
    }

    function fnc_change_caption(str)
    {
        if (str==1) 
        {
            var search_by_td = $("#search_by_td_up").html('Export LC No').classList.add("must_entry_caption"); 
        }
        else
        {
            var search_by_td = $("#search_by_td_up").html('SC No').classList.add("must_entry_caption"); 
        }
        $("#txt_lc_sc_no").val('');        
        // $("#search_by_td_up").attr(class,'must_entry_caption');      
        // var element = document.getElementById("search_by_td_up");
        // element.classList.add("must_entry_caption");  
    }

    function fn_openpopup()
    {
        if(form_validation('cbo_company_id','Company Name')==false )
        {
            return;
        }
        else
        {
            var cbo_company_id = document.getElementById('cbo_company_id').value;
			var cbo_search_by = document.getElementById('cbo_search_by').value;
            page_link = 'requires/lc_wise_payment_status_controller.php?action=lc_sc_popup&company_id='+cbo_company_id+'&cbo_search_by='+cbo_search_by,'LC/SC Selection Form';
            
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'LC/SC Information', 'width=900px,height=360px,center=1,resize=1,scrolling=0','../');
            emailwindow.onclose=function()
            {
                var theform=this.contentDoc.forms[0];
                var lc_sc_info=this.contentDoc.getElementById("selected_id").value.split("_");
                $('#txt_lc_sc_id').val(lc_sc_info[0]);
                $('#txt_lc_sc_no').val(lc_sc_info[1]);
            }
        }
    }

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>   		 
        <form name="lcWisePayment_1" id="marginlcregister_1" autocomplete="off" > 
         <h3 style="width:650px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:640px" >      
            <fieldset>  
                <table class="rpt_table" width="640" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th width="160" class="must_entry_caption">Company</th>
                        <th width="100">Search By</th>
                        <th width="130" id="search_by_td_up" class="must_entry_caption"><? echo "Export LC No"; ?></th>
                        <th width="120"><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('lcWisePayment_1','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td align="center">
                                <? 
                                    echo create_drop_down( "cbo_company_id", 180, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and comp.core_business in(1,3) $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "" );
                                ?>                            
                            </td>                    
                            <td align="center">
                                <? 
                                    $search_by_arr = array(1 => "Export LC No", 2 => "SC No");
                                    echo create_drop_down("cbo_search_by", 100, $search_by_arr, "", 0, "--Select--", 1, "fnc_change_caption(this.value);", 0); 
                                ?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_lc_sc_no" id="txt_lc_sc_no" class="text_boxes" style="width:150px"  onDblClick= "fn_openpopup()"  readonly placeholder="Double Click For Search" />
                                <input type="hidden" name="txt_lc_sc_id" id="txt_lc_sc_id" />
                            </td>
                            <td>
                                <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:60px" class="formbutton" />

                                <input type="button" name="search" id="search" value="Show2" onClick="generate_report(2)" style="width:60px" class="formbutton" />
                            </td>
                        </tr>
                    </tbody>
                </table> 
            </fieldset>
        </div>
    <div style="margin-top:10px" id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>  
 </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>