<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Trims Issue Report
				
Functionality	:	
JS Functions	:
Created by		:	Md Jakir Hosen
Creation date 	: 	03-08-2022
Updated by 		: 	Md. Jakir Hosen
Update date		: 	03-08-2022
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Trims Received Issue Stock Report","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function openmypage_style()
	{		
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val()+"_"+$("#txt_job_no_id").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/trims_issue_report_controller.php?data='+data+'&action=style_popup', 'Style Search', 'width=480px,height=380px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theemailid=this.contentDoc.getElementById("txt_po_id");
			var theemailval=this.contentDoc.getElementById("txt_po_val");
			if (theemailid.value!="" || theemailval.value!="")
			{
				freeze_window(5);
				$("#txt_style").val(theemailval.value);
				$("#txt_style_id").val(theemailid.value);
				release_freezing();
			}
		}
	}
	
	function openmypage_order()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		
		var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val()+"_"+$("#txt_style_id").val()+"_"+$("#txt_job_no_id").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/trims_issue_report_controller.php?data='+data+'&action=order_no_popup', 'Order No Search', 'width=480px,height=360px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theemailid=this.contentDoc.getElementById("txt_po_id");
			var theemailval=this.contentDoc.getElementById("txt_po_val");
			if (theemailid.value!="" || theemailval.value!="")
			{
				freeze_window(5);
				$("#txt_order_no_id").val(theemailid.value);
				$("#txt_order_no").val(theemailval.value);
				release_freezing();
			}
		}
	}

    function openmypage_job()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}

		var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/trims_issue_report_controller.php?data='+data+'&action=job_no_popup', 'Job No Search', 'width=480px,height=360px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theemailid=this.contentDoc.getElementById("txt_po_id");
			var theemailval=this.contentDoc.getElementById("txt_po_val");
			if (theemailid.value!="" || theemailval.value!="")
			{
				freeze_window(5);
				$("#txt_job_no_id").val(theemailid.value);
				$("#txt_job_no").val(theemailval.value);
				release_freezing();
			}
		}
	}

	
	function fn_report_generated(operation)
	{
		var style =document.getElementById('txt_style').value;
		var order_no = document.getElementById('txt_order_no').value;
		var job_no = document.getElementById('txt_job_no').value;

        if(form_validation('cbo_company_id*txt_date_from*txt_date_to','Company*From date*To date')==false)
        {
            return;
        }
		var report_title=$( "div.form_caption" ).html();
        var action = ""
        if(operation == 1){
            action = "report_generate";
        }else if(operation == 2){
            action = "report_generate_2";
        }
		var data="action="+action+get_submitted_data_string('cbo_company_id*cbo_buyer_id*txt_style*txt_style_id*txt_order_no*txt_order_no_id*txt_job_no_id*txt_job_no*cbo_item_group*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/trims_issue_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;  

	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	 		show_msg('3');
			release_freezing();
		}
	}
    function clear_data(){
        $('#txt_job_no_id').val('');
        $('#txt_order_no_id').val('');
        $('#txt_style_id').val('');
    }

	function new_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
	}

	function openmypage_color_size_issue(po_id,item_group,itemcolor,item_size,action)
	{
		var companyID = $("#cbo_company_id").val();
		var popup_width='500px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_trims_receive_issue_stock_controller.php?companyID='+companyID+'&po_id='+po_id+'&item_group='+item_group+'&itemcolor='+itemcolor+'&item_size='+item_size+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=250px,center=1,resize=0,scrolling=0','../../');
	}

    function open_recv_popup(str){
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/trims_issue_report_controller.php?data='+str+'&action=receive_popup', 'Receive Details Popup', 'width=780px,height=350px,center=1,resize=0,scrolling=0','../../');
    }

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?>    		 
        <form name="greyissuestatus_1" id="greyissuestatus_1" autocomplete="off" > 
         <h3 style="width:1100px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
         <div id="content_search_panel" style="width:1100px" >
            <fieldset>  
                <table class="rpt_table" width="1100" cellpadding="0" cellspacing="0" rules="all">
                    <thead>
                        <th width="140" class="must_entry_caption">Company</th>
                        <th width="140" >Buyer</th>
                        <th width="120" >Job No.</th>
                        <th width="135">Style Ref.</th>
                        <th width="120">Order No.</th>
                        <th width="145">Item Group</th>
                        <th  width="160" align="center" class="must_entry_caption">Date Range</th>
                        <th><input type="reset" name="search" id="search" value="Reset" style="width:70px" onclick="clear_data();" class="formbutton" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td align="center">
                                <? 
                                    echo create_drop_down( "cbo_company_id", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and company_name!='0' $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/trims_issue_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                                ?>                            
                            </td>
                            <td align="center" id="buyer_td">
								<?
									echo create_drop_down( "cbo_buyer_id", 140,$blank_array,"", 1, "-- Select Buyer--", $selected, "","","","","","");
                                ?> 
                          	</td>
                            <td align="center">
                            	<input style="width:110px;" name="txt_job_no" id="txt_job_no" class="text_boxes" onDblClick="openmypage_job()" placeholder="Browse Job No." readonly />
                                <input type="hidden" name="txt_job_no_id" id="txt_job_no_id" style="width:90px;"/>
                            </td>
                            <td align="center">
                                <input style="width:125px;" name="txt_style" id="txt_style" class="text_boxes" onDblClick="openmypage_style()" placeholder="Browse Style" readonly />
                                <input type="hidden" name="txt_style_id" id="txt_style_id" style="width:90px;"/>
                            </td>
                            <td align="center" >
                            	<input style="width:110px;" name="txt_order_no" id="txt_order_no" class="text_boxes" onDblClick="openmypage_order()" placeholder="Browse Order"  readonly/>
                                <input type="hidden" name="txt_order_no_id" id="txt_order_no_id" style="width:90px;"/>
                            </td>
                            <td align="center">
                                <?
                                echo create_drop_down( "cbo_item_group", 140, "select item_name,id from lib_item_group where item_category=4 and is_deleted=0 and status_active=1 order by item_name","id,item_name", 0, "", $selected, "" );
                                ?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:60px;" />
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:60px;"/>
                            </td>
                            <td>
                                <input type="button" name="search" id="search" value="Show" onClick="fn_report_generated(1)" style="width:70px" class="formbutton" />
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                    
                        <tr>
                            <td colspan="7" align="center">
							<? echo load_month_buttons(1);  ?>
                            </td>
                            <td align="center">
                                <input type="button" name="search2" id="search2" value="Show 2" onClick="fn_report_generated(2)" style="width:70px" class="formbutton" />
                            </td>
                        </tr>
                    </tfoot>
                </table> 
            </fieldset> 
            </div>
          		
                <div id="report_container" align="center"></div>
                <div id="report_container2" align="left"></div> 
              
        </form>    
    </div>
</body>
<script> set_multiselect('cbo_item_group','0','0','','0');</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
