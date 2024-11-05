<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Subcon Grey Stock Report.
Functionality	:	
JS Functions	:
Created by		:	Md Mahbubur Rahman 
Creation date 	: 	01-07-2014
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



echo load_html_head_contents("Subcon Grey Stock Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	var tableFilters = 
	{
		col_60: "none",
		col_operation: {
		id: ["receive_quantity","issue_quantity","return_quantity","balance"],
		col: [7,8,9,10],
		operation: ["sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
			
	function fn_report_generated(action)
	{
		if (form_validation('cbo_company_id*txt_date_from*txt_date_to','Comapny Name*Date From*Date To')==false)
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		if(action==1){
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_party_id*cbo_item_category*txt_date_from*txt_date_to*txt_order_no*txt_challan_no*txt_party_name',"../../")+'&report_title='+report_title;
		}else{
		var data="action=report_generate2"+get_submitted_data_string('cbo_company_id*cbo_party_id*cbo_item_category*txt_date_from*txt_date_to*txt_order_no*txt_challan_no*txt_party_name',"../../")+'&report_title='+report_title;
		}
		freeze_window(3);
		http.open("POST","requires/subcon_grey_Stock_report2_controller.php",true);
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
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			//append_report_checkbox('table_header_1',1);
			setFilterGrid("table_body",-1,tableFilters);
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
	
	function openmypage_buyer()
	{ 
	if(form_validation('cbo_company_id','Company')==false)
	{
		return;
	}
	var company_name=document.getElementById('cbo_company_id').value;
	var page_link="requires/subcon_grey_Stock_report2_controller.php?action=buyer_no_popup&company_id="+company_name;
	
	var title="Buyer Name";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=330px,height=320px,center=1,resize=0,scrolling=0','../')

	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("selected_id").value;
		var job=theemail.split("_");
		document.getElementById('cbo_party_id').value=job[0];
		document.getElementById('txt_party_name').value=job[1];
		release_freezing();
	}
	}
	
	
	function openmypage_order()
	{ 
	if(form_validation('cbo_company_id','Company')==false)
	{
		return;
	}
	var company_name=document.getElementById('cbo_company_id').value;
	var cbo_buyer_name=document.getElementById('cbo_party_id').value;
	//var year=document.getElementById('cbo_year').value;
	//var cbo_process_id=document.getElementById('cbo_process_id').value;
	//var job_no=document.getElementById('txt_job_no').value;
	//var page_link="requires/subcon_grey_Stock_report2_controller.php?action=order_no_popup&company_id="+company_name+"&cbo_buyer_name="+cbo_buyer_name+"&year="+year+"&cbo_process_id="+cbo_process_id+"&job_no="+job_no;
	var page_link="requires/subcon_grey_Stock_report2_controller.php?action=order_no_popup&company_id="+company_name+"&cbo_buyer_name="+cbo_buyer_name;
	
	var title="Order Number";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=420px,center=1,resize=0,scrolling=0','../')

	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("selected_id").value;
		var job=theemail.split("_");
		document.getElementById('txt_order_id').value=job[0];
		document.getElementById('txt_order_no').value=job[1];
		release_freezing();
	}
	}
	
	function openmypage_challan()
	{ 
	if(form_validation('cbo_company_id','Company')==false)
	{
		return;
	}
	var company_name=document.getElementById('cbo_company_id').value;
	var cbo_buyer_name=document.getElementById('cbo_party_id').value;
	
	var page_link="requires/subcon_grey_Stock_report2_controller.php?action=challan_no_popup&company_id="+company_name+"&cbo_buyer_name="+cbo_buyer_name;
	
	var title="Order Number";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=420px,center=1,resize=0,scrolling=0','../')

	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("selected_id").value;
		var job=theemail.split("_");
		document.getElementById('txt_challan_id').value=job[0];
		document.getElementById('txt_challan_no').value=job[1];
		release_freezing();
	}
	}

</script>
</head>
<body onLoad="set_hotkey();">
<form id="greyStock_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1050px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1050px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="150" class="must_entry_caption">Company</th>
                    <th width="150">Party </th>
                    <th width="150">Item Category</th>
                    <th width="80">Order No</th>
                    <th width="80">Challan No</th>
                    <th width="200" class="must_entry_caption">Transaction Date</th>
                    <th width="200"><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                </thead>
                <tbody>
                    <tr class="general" >
                        <td  align="center"> 
                            <?
                                echo create_drop_down( "cbo_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
								//"load_drop_down( 'requires/subcon_grey_Stock_report2_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );"
                            ?>
                        </td>
                        <td id="buyer_td">
                        	 <input name="txt_party_name" id="txt_party_name" class="text_boxes" style="width:140px"  placeholder="Wr/Br Party Name" onDblClick="openmypage_buyer();" >
                            <input type="hidden" name="cbo_party_id" id="cbo_party_id" class="text_boxes" style="width:70px">
                            <? 
                                //echo create_drop_down( "cbo_party_id", 140, $blank_array,"", 1, "--Select Party--", $selected, "",1,"" );
                            ?>
                        </td>
                        <td>
                            <? 
                                echo create_drop_down( "cbo_item_category", 120, $item_category,"", 1, "-Select Type-", $selected, "","","" );
                            ?>
                        </td>
                        <td>
                            <input name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:75px"  placeholder="Wr/Br Order" onDblClick="openmypage_order();" >
                            <input type="hidden" name="txt_order_id" id="txt_order_id" class="text_boxes" style="width:70px">
                        </td>
                        
                        <td>
                            <input name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:75px"  placeholder="Wr/Br Challan No" onDblClick="openmypage_challan();" >
                            <input type="hidden" name="txt_challan_id" id="txt_challan_id" class="text_boxes" style="width:70px">
                        </td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" >&nbsp; To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date"  >
                        </td>
                        <td align="center">
                            <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated(1);" />
							<input type="button" id="show_button2" class="formbutton" style="width:100px" value="Show 2" onClick="fn_report_generated(2);" />
                        </td>
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
