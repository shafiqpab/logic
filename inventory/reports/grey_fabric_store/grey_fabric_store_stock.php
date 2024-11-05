<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Grey Fabric Store Stock Report
				
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	24-03-2014
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
echo load_html_head_contents("Grey Fabric Store Stock Report","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	var tableFilters = 
	{
		col_30: "none",
		col_operation: {
		id: ["tot_qnty"],
		col: [6],
		operation: ["sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
	
	function openmypage_job()
	{
		 var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_buyer_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/grey_fabric_store_stock_controller.php?action=job_no_popup&data='+data,'Job No Popup', 'width=900px,height=420px,center=1,resize=0','../../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("job_no_id");
			var theemailv=this.contentDoc.getElementById("job_no_val");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_job_no_id").value=theemail.value;
			    document.getElementById("txt_job_no").value=theemailv.value;
				release_freezing();
			}
		}
	}
	
	function openmypage_batch()
	{
		 var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_buyer_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/grey_fabric_store_stock_controller.php?action=batch_no_popup&data='+data,'Batch No Popup', 'width=900px,height=420px,center=1,resize=0','../../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("batch_no_id");
			var theemailv=this.contentDoc.getElementById("batch_no_val");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_batch_no_id").value=theemail.value;
			    document.getElementById("txt_batch_no").value=theemailv.value;
				release_freezing();
			}
		}
	}
	
	function fn_report_generated(operation)
	{
		if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false )
		{
			return;
		} 
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*txt_job_no*txt_job_no_id*txt_batch_no*txt_batch_no_id*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/grey_fabric_store_stock_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;  

	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			var tot_rows=reponse[2];
			//alert (reponse[2]);return;
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
			setFilterGrid("tbl_issue_status",-1,tableFilters);
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

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?><br />    		 
        <form name="greystorestock_1" id="greystorestock_1" autocomplete="off" > 
         <h3 style="width:900px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:900px" >      
            <fieldset>  
                <table class="rpt_table" width="860" cellpadding="0" cellspacing="0">
                    <thead>
                        <th width="140" class="must_entry_caption">Company</th>
                        <th width="140" >Buyer</th>
                        <th width="140">Job No.</th>
                        <th width="120">Batch No.</th>
                        <th class="must_entry_caption">Date Range</th>
                        <th width="70"><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('greystorestock_1','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <? 
                                    echo create_drop_down( "cbo_company_id", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/grey_fabric_store_stock_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                                ?>                            
                            </td>
                            <td id="buyer_td">
								<?
									echo create_drop_down( "cbo_buyer_id", 140,$blank_array,"", 1, "-- Select Buyer --", $selected, "","","","","","");
                                ?> 
                          	</td>
                            <td>
                            	<input style="width:120px;" name="txt_job_no" id="txt_job_no" class="text_boxes" onDblClick="openmypage_job()" placeholder="Browse Job" readonly />
                                <input type="hidden" name="txt_job_no_id" id="txt_job_no_id" style="width:90px;"/>
                            </td>
                            <td >
                            	<input style="width:120px;" name="txt_batch_no" id="txt_batch_no" class="text_boxes" onDblClick="openmypage_batch()" placeholder="Browse Batch" readonly />
                                <input type="hidden" name="txt_batch_no_id" id="txt_batch_no_id" style="width:90px;"/>
                            </td>
                            <td>
                                <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:70px;" />
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:70px;"/>
                            </td>
                            <td>
                                <input type="button" name="search" id="search" value="Show" onClick="fn_report_generated(3)" style="width:70px" class="formbutton" />
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
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
