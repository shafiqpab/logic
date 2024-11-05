<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Loan Party Ledger
				
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	27-04-2015
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
echo load_html_head_contents("Loan Party Ledger","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";

	function generate_report(Report_type)
	{
		if( form_validation('cbo_company_name*txt_loan_party_id','Company Name*Party Name')==false )
		{
			return;
		} 

		if(Report_type==1){
			var dataString = "cbo_company_name*txt_loan_party_id*cbo_item_cat*txt_date_from*txt_date_to*cbo_store_name";
			var data="action=generate_report"+get_submitted_data_string(dataString,"../../../"); 

		}else if(Report_type==2){
			var dataString = "cbo_company_name*txt_loan_party_id*cbo_item_cat*txt_date_from*txt_date_to";
			var data="action=generate_report2"+get_submitted_data_string(dataString,"../../../");
			
		}
		else if(Report_type==3){
			var dataString = "cbo_company_name*txt_loan_party_id*cbo_item_cat*txt_date_from*txt_date_to";
			var data="action=generate_report3"+get_submitted_data_string(dataString,"../../../");
			
		}
		else if(Report_type==4){
			var dataString = "cbo_company_name*txt_loan_party_id*cbo_item_cat*txt_date_from*txt_date_to";
			var data="action=generate_report4"+get_submitted_data_string(dataString,"../../../");
			
		}		
		freeze_window(3);
		http.open("POST","requires/loan_party_ledger_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}

	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			//alert(reponse[0]);
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			setFilterGrid("table_body_four",-1,'');
			release_freezing();
		}
	} 

	function new_window()
	{		 
			//document.getElementById('scroll_body').style.overflow="auto";
			//document.getElementById('scroll_body').style.maxHeight="none"; 
			$('.fltrow').hide(); 
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close(); 
		    $('.fltrow').show(); 
			//document.getElementById('scroll_body').style.overflow="auto"; 
			//document.getElementById('scroll_body').style.maxHeight="250px";
	}
	
	function openmypage_party()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var txt_loan_party_name = $("#txt_loan_party_name").val();
		var txt_loan_party_id = $("#txt_loan_party_id").val();
		var txt_loan_party_no = $("#txt_loan_party_no").val();
		var page_link='requires/loan_party_ledger_controller.php?action=party_search_popup&company='+company+'&txt_loan_party_name='+txt_loan_party_name+'&txt_loan_party_id='+txt_loan_party_id+'&txt_loan_party_no='+txt_loan_party_no;  
		var title="Search Party Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=370px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var loan_party_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var loan_party_name=this.contentDoc.getElementById("txt_selected").value; // product Description
			var loan_party_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			$("#txt_loan_party_name").val(loan_party_name);
			$("#txt_loan_party_id").val(loan_party_id); 
			$("#txt_loan_party_no").val(loan_party_no);
		}
	}

	function fnc_rcv_details(item_cat_id,item_grup_id,item_descrip,umo_id,company,title,action,loan_party,rcv_basis,to_date,from_date)
	{
		var company = $("#cbo_company_name").val();	
		var page_link='requires/loan_party_ledger_controller.php?item_cat_id='+item_cat_id+'&item_grup_id='+item_grup_id+'&item_descrip='+item_descrip+'&umo_id='+umo_id+'&company='+company+'&title='+title+'&action='+action+'&loan_party='+loan_party+'&rcv_basis='+rcv_basis+'&to_date='+to_date+'&from_date='+from_date;
		// alert(page_link);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1120px,height=350px,center=1,resize=0,scrolling=0','../../');
	}
	
	function fn_empty_loan_party()
	{
		$('#txt_loan_party_name').val("");
		$('#txt_loan_party_id').val("");
		$('#txt_loan_party_no').val("");
	}

	function print_report_button_setting(report_ids)
	{
		
		
		var idCOm = $("#cbo_company_name").val();
		if(idCOm==17)
		{
			$("#search").show();
		    $("#search2").show();	
		    $("#search3").show();	
		}
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
			{
				if(report_id[k]==108)
				{
					$("#search").show();	 
				}
				if(report_id[k]==195)
				{
					$("#search2").show();	 
				}
				if(report_id[k]==242)
				{
					$("#search3").show();	 
				}	
				if(report_id[k]==243)
				{
					$("#search4").show();	 
				}			
			}
	}
	
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission);  ?>   		 
    <form name="loan_ledger_1" id="loan_ledger_1" autocomplete="off" > 
    <h3 style="width:900px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div id="content_search_panel" style="width:100%;" align="center">
        <fieldset style="width:900px;">
        <legend>Search Panel</legend> 
			<table class="rpt_table" width="1030" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th width="160" class="must_entry_caption">Company</th>
                        <th width="160" class="must_entry_caption">Party</th> 
                        <th width="160">Item Category</th>   
						<th width="130">Store</th>                            
                        <th width="160">Date</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_field()" /></th>
                    </tr>
                </thead>
                <tr class="general" align="center">
                    <td>
                            <? 
                               echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "fn_empty_loan_party();get_php_form_data(this.value, 'set_print_button', 'requires/loan_party_ledger_controller');load_drop_down( 'requires/loan_party_ledger_controller', this.value, 'load_drop_down_store', 'store_td' );" );
                            ?>         						                   
                    </td>
                    <td align="center">
                    <input type="text" id="txt_loan_party_name" name="" class="text_boxes" style="width:130px;" placeholder="Browse" onDblClick="openmypage_party();" readonly >
                    <input type="hidden" id="txt_loan_party_id" name="txt_loan_party_id" >  <input type="hidden" id="txt_loan_party_no" name="txt_loan_party_no" >                    
                    </td>
                    <td align="center">
					<?
                        echo create_drop_down( "cbo_item_cat", 150, $item_category,"", 1, "-- Select Item --", $selected, "",0,"5,6,7,22,23" );
                    ?>                           
                    </td>
					<td id="store_td"> 
                            <?
                                echo create_drop_down( "cbo_store_name", 120, $blank_array,"", 1, "-- Select Store --", 0, "" );
                            ?>
                        </td>
                    <td align="center">
                         <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:55px"/>                    							
                         To
                         <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:55px"/>                                                        
                    </td>
                    <td>
                        <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px;display:none;" class="formbutton" />
                        <input type="button" name="search2" id="search2" value="Show 2" onClick="generate_report(2)" style="width:70px;display:none;" class="formbutton" />
                        <input type="button" name="search3" id="search3" value="Show 3" onClick="generate_report(4)" style="width:70px;display:none;" class="formbutton" />
                        <input type="button" name="search4" id="search4" value="Item Wise" onClick="generate_report(3)" style="width:70px;display:none;" class="formbutton" />
                    </td>
                </tr>
                <tr>
                    <td colspan="6"><? echo load_month_buttons(1);  ?></td>
                </tr>
            </table> 
        </fieldset> 
    </div>
    <br /> 
        <!-- Result Contain Start-------------------------------------------------------------------->
        	<div id="report_container" align="center"></div>
            <div id="report_container2"></div> 
        <!-- Result Contain END-------------------------------------------------------------------->
    </form>    
</div>    
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
