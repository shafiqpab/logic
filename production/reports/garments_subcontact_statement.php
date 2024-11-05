<?php
/********************************* Comments ************************************
*	Purpose			:	This Form Will Create Garments Subcontact Statement
*	Functionality	:	
*	JS Functions	:
*	Created by		:	Md. Nuruzzaman 
*	Creation date 	: 	23-09-2015
*	Updated by 		: 		
*	Update date		: 		   
*	QC Performed BY	:		
*	QC Date			:	
*	Comments		:
********************************************************************************/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Garments Subcontact Statement", "../../", '', 1,$unicode,'','');

?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	function fn_show_report(rpt_type)
	{
		var info_type = $("#cbo_info_type").val();
		var prod_type = $("#cbo_prod_process").val();
		if(info_type==2){
			var field='cbo_company_name';
			var fieldMessage='Company Name';
		}
		else{
			var field='cbo_company_name*txt_date_from*txt_date_to';
			var fieldMessage='Company Name*From Date*To Date';
		}	

		if(rpt_type==2 && info_type!=2)
		{
			alert("Info Type only Order Wise for this button");
			return;
		}

		if(rpt_type==3 && info_type!=3)
		{
			alert("Info Type only Transaction Date Wise for this button");
			return;
		}

		if(rpt_type==3 && prod_type!=5)
		{
			alert("Prod. Type only Sewing for this button");
			return;
		}
		
		if( form_validation(field,fieldMessage)==false )
		{
			return;
		}
		else
		{
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*hidden_order_id*txt_style*hidden_factory_id*cbo_info_type*txt_date_from*txt_date_to*cbo_prod_process*txt_internal_ref*txt_order_no',"../../")+"&report_title="+report_title+"&rpt_type="+rpt_type;
			freeze_window(3);
			http.open("POST","requires/garments_subcontact_statement_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_show_report_reponse;
		}
	}

	function fn_show_report_reponse()
	{
		if(http.readyState == 4) 
		{
		 	
			//alert(http.responseText); return;
			var reponse=trim(http.responseText).split("****"); 
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			show_msg('3');
			release_freezing();
		}
		
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
	
	//new
	function openmypage_working_factory()
	{	
		//alert("su..re"); return;
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var page_link='requires/garments_subcontact_statement_controller.php?action=factory_search&company='+company;  
		var title="Working Factory Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=380px,height=310px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var factory_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var factory_no=this.contentDoc.getElementById("txt_selected").value; // product Description
			//var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			$("#txt_working_factory").val(factory_no);
			$("#hidden_factory_id").val(factory_id); 
			//$("#txt_order_id_no").val(style_des_no);
		}
	}
	
	function openmypage_order()
	{
		//alert("su..re"); return;
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();
		var txt_order_no = $("#txt_order_no").val();
		var hidden_order_id = $("#hidden_order_id").val();
		var page_link='requires/garments_subcontact_statement_controller.php?action=order_search&company='+company+'&buyer='+buyer+'&order_no='+txt_order_no+'&order_id='+hidden_order_id;  
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=520px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_no=this.contentDoc.getElementById("txt_selected").value; // product Description
			var order_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			//var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			$("#txt_order_no").val(order_no);
			$("#hidden_order_id").val(order_id); 
			//$("#txt_order_id_no").val(style_des_no);
		}
	}
	
	function fn_pro_dtls(poId,action,title,width,prod_type,date_from,date_to,company)
	{
		var page_link='requires/garments_subcontact_statement_controller.php?action='+action+'&order_id='+poId+'&prod_type='+prod_type+'&txt_date_from='+date_from+'&txt_date_to='+date_to+'&serving_company='+company;  

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+width+',height=370px,center=1,resize=1,scrolling=1','../')
		emailwindow.onclose=function()
		{
		}
	}
	
	
	
	
	function change_date_caption()
	{
		//alert("su..re");
		var info_type=document.getElementById('cbo_info_type').value;
		if(info_type==1)
		{
			document.getElementById('date_caption').innerHTML="Receive Date";
			//document.getElementById('cbo_prod_process').disabled=false;
		}
		else if(info_type==2)
		{
			document.getElementById('date_caption').innerHTML="Shipment Date";
			//document.getElementById('cbo_prod_process').disabled=true;
		}
		else if(info_type==3)
		{
			document.getElementById('date_caption').innerHTML="Transaction Date";
			//document.getElementById('cbo_prod_process').disabled=true;
		}
	}	 

	function fnc_po_details(poId,prod_type,production_type,width_popup,date_from,date_to,title,action)
	{
		var page_link='requires/garments_subcontact_statement_controller.php?action='+action+'&order_id='+poId+'&prod_type='+prod_type+'&production_type='+production_type+'&txt_date_from='+date_from+'&txt_date_to='+date_to;  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+width_popup+',height=370px,center=1,resize=1,scrolling=1','../');
	}
</script>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">    
<form id="garmentsSubcontact_1">
        <? echo load_freeze_divs ("../../",''); ?>
		<h3 align="left" id="accordion_h1" style="width:1250px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')">-Search Panel</h3>
		<div id="content_search_panel">    
        <fieldset style="width:1250px;">
            <table class="rpt_table" width="1250px" cellpadding="0" cellspacing="0" align="center" rules="all" border="1">
               <thead>                    
                    <tr>
                        <th class="must_entry_caption">Company Name</th>
                        <th>Buyer Name</th>
                        <th>Order No</th>
                        <th>Internal Ref.</th>
                        <th>Style</th>
                        <th>Working Factory</th>
                        <th>Info Type</th>
                        <th colspan="2" class="must_entry_caption" id="date_caption">Receive Date</th>
                        <th class="must_entry_caption">Prod. Type</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                    </tr>    
                 </thead>
                <tbody>
                <tr class="general">
                    <td><? echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/garments_subcontact_statement_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?> </td>
                    <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" ); ?>
                    </td>
                    <td><input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:100px" placeholder="Browse Or Write" onDblClick="openmypage_order();" onChange="$('#hidden_order_id').val('');" autocomplete="off"><input type="hidden" name="hidden_order_id" id="hidden_order_id" class="text_boxes" style="width:100px" readonly></td>

                    <td align="center">
                        <input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px" placeholder="Write" />
                    </td>

                    <td><input type="text"  name="txt_style" id="txt_style" class="text_boxes" style="width:100px;" /></td>
                    <td><input type="text"  name="txt_working_factory" id="txt_working_factory" class="text_boxes" placeholder="Browse" onDblClick="openmypage_working_factory();" style="width:100px;"><input type="hidden" name="hidden_factory_id" id="hidden_factory_id" class="text_boxes" style="width:100px" readonly></td>
                    <td><? echo create_drop_down( "cbo_info_type", 110, $info_type,"", "", "", $selected, "change_date_caption()","","" ); ?></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" ></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" /></td>
                    <td><? 
					$production_process[1001]="Embellishment";
					echo create_drop_down( "cbo_prod_process", 110, $production_process,"", 1, "-- Select --", 5, "","","5,1001" ); 
					?></td>
                    <td>
						<input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_show_report(1)" />
						<input type="button" id="show_button" class="formbutton" style="width:70px" value="Show 2" onClick="fn_show_report(2)" />
					</td>
                </tr>
				<tr>
                	<td colspan="10" align="center"><? echo load_month_buttons(1); ?></td>
					<td align="center">
					<input type="button" id="show_button" class="formbutton" style="width:140px" value="Sewing Status" onClick="fn_show_report(3)" />
					</td>
                </tr>
                </tbody>
            </table>
        </fieldset>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
 </form> 
 </div>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>$('#cbo_location').val(0);</script>
</html>