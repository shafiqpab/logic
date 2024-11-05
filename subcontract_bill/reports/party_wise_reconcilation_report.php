<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Party Wise Reconcilation Report.
Functionality	:	
JS Functions	:
Created by		:	Tajik 
Creation date 	: 	29-01-2018
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
echo load_html_head_contents("Party Wise Reconcilation Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
			
	function fnc_report_generated()
	{
		if (form_validation('cbo_company_id','Comapny Name')==false) { return; }
		if($("#txt_party_name").val()=="" && $("#txt_order_no").val()=="")
		{
			if (form_validation('txt_date_from*txt_date_to','Date From*Date To')==false) { return; }
		}

		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*txt_party_id*cbo_item_category*txt_order_no*order_no_id*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/party_wise_reconcilation_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("**"); 
			$('#report_container2').html(reponse[0]);
			setFilterGrid('list_views',-1);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body_1').style.overflow="auto";
		document.getElementById('scroll_body_1').style.maxHeight="none"; 
		$(".flt").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body_1').style.overflowY="scroll"; 
		document.getElementById('scroll_body_1').style.maxHeight="400px";
		$(".flt").show();
	}

	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}

	function fnc_party_popup()
	{
		if (form_validation('cbo_company_id','Comapny Name')==false) { return; }
		var data=$('#cbo_company_id').val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/party_wise_reconcilation_report_controller.php?data='+data+'&action=party_popup','Buyer Party Popup', 'width=420px,height=320px,center=1,resize=1,scrolling=0','../')
		
		emailwindow.onclose=function()
		{
			var hide_party_id=this.contentDoc.getElementById("hide_party_id").value;//Access form field with id="emailfield"
			var hide_party_name=this.contentDoc.getElementById("hide_party_name").value;
			if (hide_party_name!="")
			{
				$('#txt_party_id').val(hide_party_id);
				$('#txt_party_name').val(hide_party_name);
			}
		}
	}

	function job_search_popup(page_link,title)
	{
		if ( form_validation('cbo_company_id','Company Name')==false ) { return; }
		var data=document.getElementById('txt_order_no').value+"_"+document.getElementById('cbo_company_id').value;
		page_link='requires/party_wise_reconcilation_report_controller.php?action=job_popup&data='+data
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			freeze_window(5);
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_order").value;
			var splt_val=theemail.split("_");
			//alert(theemail);
			$("#txt_order_no").val(splt_val[1]);
			$("#order_no_id").val(splt_val[0]);
			release_freezing();
		}
	}

	function show_popup_report_details(action,datas,width)
	{ 
		 emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/party_wise_reconcilation_report_controller.php?action='+action+'&datas='+datas, 'Details', 'width='+width+',height=320px,center=1,resize=0,scrolling=0','../');
		
	}

</script>
</head>
<body onLoad="set_hotkey();">
<form id="greyStock_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:750px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:750px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="140" class="must_entry_caption">Company</th>
                    <th width="130">Party</th>
                    <th width="130">Item Category</th>
                    <th width="130">Order No</th>
                    <th width="170" class="must_entry_caption" colspan="2">Transaction Date</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                </thead>
                <tbody>
                    <tr class="general" >
                        <td>
                        	<? 
                        		echo create_drop_down( "cbo_company_id", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-Select Company-", $selected, "" ); 
                        	?>
                        </td>
                        <td>
                        	<input class="text_boxes" type="text" style="width:120px" name="txt_party_name" id="txt_party_name" placeholder="Browse Party" onDblClick="fnc_party_popup();" readonly />
                        	<input class="text_boxes" type="hidden" style="width:70px" name="txt_party_id" id="txt_party_id" readonly />
                        </td>
                        <td>
                        	<? 
                        		echo create_drop_down( "cbo_item_category", 130, $item_category,"", 1, "--Select Item--",0,"", "","1,2,3,4,13,14,30" );
                        	?>
                        </td>
                        <td>
                        	<input class="text_boxes" name="txt_order_no" id="txt_order_no" type="text" style="width:130px" placeholder="Browse Order" readonly onDblClick="job_search_popup('requires/party_wise_reconcilation_report_controller.php?action=job_popup','Order Selection Form')" />
                        	<input type="text" name="order_no_id" id="order_no_id" hidden>
                        </td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" ></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" ></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fnc_report_generated();" /></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7" align="center">
							<? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </tfoot>
           </table> 
           <br />
        </fieldset>
    	</div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
