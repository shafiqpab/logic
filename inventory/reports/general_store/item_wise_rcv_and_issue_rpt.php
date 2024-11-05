<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Item Wise Receive and Issue Report
				
Functionality	:	
JS Functions	:
Created by		:	Nayem
Creation date 	: 	31-01-2021
Updated by 		: 		
Update date		: 		   
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
echo load_html_head_contents("Item Wise Receive and Issue Report","../../../", 1, 1, $unicode,1,''); 
//var_dump($item_category);
$user_id=$_SESSION['logic_erp']['user_id'];
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	function generate_report(operation)
	{
		if( form_validation('cbo_company_name*txt_prod_id*txt_date_from*txt_date_to','Company Name*Item Description*Date Range*Date Range')==false )
		{
			return;
		}
		var cbo_company_name = $("#cbo_company_name").val();
		var txt_prod_id = $("#txt_prod_id").val();
		var cbo_store_name = $("#cbo_store_name").val();
		var from_date = $("#txt_date_from").val();
		var to_date = $("#txt_date_to").val();
		var cbo_yes_no = $("#cbo_yes_no").val();
				
		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_store_name="+cbo_store_name+"&txt_prod_id="+txt_prod_id+"&from_date="+from_date+"&to_date="+to_date+"&cbo_yes_no="+cbo_yes_no+"&report_type="+operation;
		var data="action=generate_report"+dataString;
		//alert (data);
		freeze_window(operation);
		http.open("POST","requires/item_wise_rcv_and_issue_rpt_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//alert();
			if(reponse[2]!=8) setFilterGrid("table_body",-1);
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		$('#scroll_body tr:first').hide();
		var w = window.open("Surprise", "#");
	
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="350px";
        $('#scroll_body tr:first').show();
	}
	
	function openmypage_itemaccount()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var page_link='requires/item_wise_rcv_and_issue_rpt_controller.php?action=item_account_popup&company='+company;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var info=this.contentDoc.getElementById("txt_selected_id").value.split('_');

			$("#txt_prod_id").val(info[0]);
			$("#txt_item_des").val(info[1]); 
		}
	}
	
	function fn_store_visibility(yes_no_id)
	{
		$('#cbo_store_name').val(0);
		if(yes_no_id==2) {
			$('#cbo_store_name').attr('disabled',true);
		} else {
			$('#cbo_store_name').attr('disabled',false);
		}
	}

	function fnc_prod_details(prod_id,date_transaction,store_wise,store_id,title,action)
	{
		var page_link='requires/item_wise_rcv_and_issue_rpt_controller.php?prod_id='+prod_id+'&date_transaction='+date_transaction+'&store_wise='+store_wise+'&store_id='+store_id+'&title='+title+'&action='+action;
		// alert(page_link);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=350px,center=1,resize=0,scrolling=0','../../');
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?><br />    		 
        <form name="itemWiseRpt_1" id="itemWiseRpt_1" autocomplete="off" > 
         	<h3 style="width:880px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
			<div id="content_search_panel" style="width:880px" >      
				<fieldset>  
					<table class="rpt_table" width="880" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
							<th width="130" class="must_entry_caption">Company</th>
							<th width="150" class="must_entry_caption">Item Description</th>
							<th width="80">Store Wise</th>
							<th width="120">Store</th> 
							<th width="160" class="must_entry_caption">Date</th>
							<th><input type="reset" name="res" id="res" value="Reset" style="width:50px" class="formbutton" onClick="reset_form('itemWiseRpt_1','report_container*report_container2','','','')" /></th>
						</thead>
						<tbody>
							<tr class="general">
								<td align="center">
									<? 
										echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/item_wise_rcv_and_issue_rpt_controller', this.value+'**'+$('#cbo_yes_no').val() , 'load_drop_down_store', 'store_td' );" );
									?>                         
								</td>
								<td align="center">
									<input style="width:150px;"  name="txt_item_des" id="txt_item_des" onDblClick="openmypage_itemaccount()" class="text_boxes" placeholder="Browse" readonly/>   
									<input type="hidden" name="txt_prod_id" id="txt_prod_id"/>    
								</td>
								<td align="center">
									<? echo create_drop_down( "cbo_yes_no", 80, $yes_no,"", 0, "", 2, "fn_store_visibility(this.value)" ); ?>
								</td>
								<td id="store_td" align="center">
									<?  echo create_drop_down( "cbo_store_name", 120, $blank_array,"", 1, "--Select Store--", "", "",1 ); ?>
								</td>
								<td align="center">
									<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px;" readonly />                    							
									To
									<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px;" readonly />                        
								</td>
								<td align="center">
									<input type="button" name="receive" id="receive" value="Receive" onClick="generate_report(1)"  class="formbutton" style="width:60px;"/>
									<input type="button" name="issue" id="issue" value="Issue " onClick="generate_report(2)" class="formbutton" style="width:60px;"/>
									<input type="button" name="summary" id="summary" value="Summary" onClick="generate_report(3)" class="formbutton" style="width:60px;"/>
								</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="6" align="center"><? echo load_month_buttons(1);  ?></td>
							</tr>
						</tfoot>
					</table> 
				</fieldset> 
			</div>
            <br /> 
			<div id="report_container" align="center"></div>
			<div id="report_container2"></div> 
        </form>    
    </div>
</body> 
<script>
	set_multiselect('cbo_item_category_id','0','0','0','0');
</script> 
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
