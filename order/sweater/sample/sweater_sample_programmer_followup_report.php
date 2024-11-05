<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Sweater Sample Acknowledge
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam Reza [Cell: +880 151 1100004]
Creation date 	: 	01-10-2019
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
$menu_id=$_SESSION['menu_id'];
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sweater Sample Acknowledge", "../../../", 1, 1,'','','');

?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php"; 
	var permission='<? echo $permission; ?>';

	function open_text_popup(row_id,type)
	{
		if(type ==1)
		{
			var data=document.getElementById('txt_designer_'+row_id).value;
	        var title = 'Designer';
		}
		if(type ==2)
		{
			var data=document.getElementById('txt_programmer_'+row_id).value;
	        var title = 'Programmer';
		}

        var page_link = 'requires/sweater_sample_programmer_followup_report_controller.php?data='+data+'&type='+type+'&action=text_popup';

        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=470px,height=200px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var description=this.contentDoc.getElementById("description");
            //var type=this.contentDoc.getElementById("type");
            if(type==1)
            {
            	$('#txt_designer_'+row_id).val(description.value);
            	//document.getElementById('txt_designer_'+row_id).value=description.value;
            }
            if(type==2)
            {
            	$('#txt_programmer_'+row_id).val(description.value);
            	//document.getElementById('txt_programmer_'+row_id).value=description.value;
            }
            
        }
	}
	function file_dtls(update_id)
	{
		var page_link='requires/sweater_sample_programmer_followup_report_controller.php?action=file_dtls&sys_id='+update_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Fabric Remarks', 'width=550px,height=250px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{

		}
	}
	
	
	function print_requsion(req_id)
	{
		 print_report( $('#cbo_company_name').val()+'*'+req_id, "sweater_sample_requisition_print", "requires/sweater_sample_requisition_v2_controller" );
			 return;
	}
	function fn_report_generated(type)
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		
		if(type ==1)
		{
			var data="action=report_generate&type="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_sample_name*txt_style_ref*cbo_comp_status*txt_date_from*txt_date_to*cbo_brand_id*cbo_programmer*cbo_season_id',"../../../");
		}
	
		freeze_window(3);
		http.open("POST","requires/sweater_sample_programmer_followup_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####");
			//$('#report_container').html(response[0]);
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//var tableFilters = { col_0: "none" }
			if(reponse[2]==1) setFilterGrid("tbl_list_search",-1);
			else setFilterGrid("tbl_list_search_show2",-1);
			show_msg('3');
			release_freezing();
		}
	}
	
	
	function update_tna_process(dataStr)
	{ 
	
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sweater_sample_programmer_followup_report_controller.php?data='+dataStr+'&action=save_update_tna&permission='+permission, "Save and Update", 'width=380px,height=300px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var date=this.contentDoc.getElementById("txt_actual_start_date").value; 			
			if (date!="")
			{
				freeze_window(5);
				var dataArr=dataStr.split('_'); 
				//alert(date);
				$('#td_date_'+dataArr[0]+dataArr[2]+dataArr[3]).html(date);
				release_freezing();
			}
		}
	}

	
	function tna_process_comments(dataStr,report_type)
	{ 
	
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sweater_sample_programmer_followup_report_controller.php?data='+dataStr+'&report_type='+report_type+'&action=comments_popup&permission='+permission, "Save and Update", 'width=550px,height=300px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			
			//var theform=this.contentDoc.forms[0]; 
			var date=this.contentDoc.getElementById("selected_data").value; 
			 
			if (date!="")
			{
				freeze_window(5);
				var dataArr=dataStr.split('_');
				if(dataArr[2]==9)
				{
					$('#td_designer_'+dataArr[0]).html(date);
				}
				else if(dataArr[2]==10)
				{
					$('#td_programmer_'+dataArr[0]).html(date);
				}
				else if(dataArr[2]==16)
				{
					$('#td_nature_'+dataArr[0]).html(date);
				}
				else if(dataArr[2]==18)
				{
					$('#td_grade_'+dataArr[0]).html(date);
				}
				else if(dataArr[2]==19)
				{
					$('#td_cause_'+dataArr[0]).html(date);
				}
				else if(dataArr[2]==20)
				{
					$('#td_category_'+dataArr[0]).html(date);
				}
				else{
					$('#td_comments_'+dataArr[0]).html(date);
				}
				
				release_freezing();
			}
		}
	}
	
	
	
	function tna_process_time_weight(dataStr)
	{ 
	
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sweater_sample_programmer_followup_report_controller.php?data='+dataStr+'&action=time_weight_popup&permission='+permission, "Save and Update", 'width=550px,height=300px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var date=this.contentDoc.getElementById("selected_data").value; 
			if (date!="")
			{
				freeze_window(5);
				var dataArr=dataStr.split('_');
				$('#td_comments_'+dataArr[0]+dataArr[2]).html(date);
				release_freezing();
			}
		}
	}
	
	
		
	
	function new_window()
	{
		document.getElementById('buyer_list_view').style.overflow="auto";
		document.getElementById('buyer_list_view').style.maxHeight="none";
		
		const el = document.querySelector('#tbl_list_search_show2');
		  if (el) {
		    
			$("#tbl_list_search_show2 tr:first").hide();

		}
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_print.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		if (el) {
		    
			$("#tbl_list_search_show2 tr:first").show();

		}
		document.getElementById('buyer_list_view').style.overflowY="scroll";
		document.getElementById('buyer_list_view').style.maxHeight="400px";
	}	

	
function help_popup___(action){

		var page_link='../../../library/help/requires/help_controller.php?action=sweater_sample_progress_monitoring_report';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Help Desk', 'width=900px,height=450px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			
		}
	}
</script>
</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs("../../../",''); ?>
		 <form name="sample_acknowledgement_1" id="sample_acknowledgement_1"> 
         <h3 style="width:920px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
             <fieldset style="width:920px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <tr>
                                <th width="110" class="must_entry_caption">Company Name</th> 
                                <th width="110">Buyer</th>
                                <th width="80">Brand</th>
								<th width="110">Programmer</th>
                                <th width="80">Season</th>
                                <th width="70">Sample Name</th>
                                <th width="80">Style Ref</th>
                                <th width="80">Comp Status</th>
                                <th width="130" colspan="2" class="must_entry_caption">Date Range</th>
                                
                                <th>
                                    <input type="reset" name="res" id="res" value="Reset" onClick="reset_form('sample_acknowledgement_1','report_container','','','')" class="formbutton" style="width:70px" />
                                </th>
                        	</tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td><?=create_drop_down( "cbo_company_name", 110, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sweater_sample_programmer_followup_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?>
                                </td>
                                <td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 110, $blank_array,"", 1, "-- All --", 0, "" ); ?></td>
                                <td id="brand_td"><?=create_drop_down( "cbo_brand_id", 80, $blank_array,'', 1, "--Brand--",$selected, "" ); ?>
                                <td><?=create_drop_down( "cbo_programmer", 110, "select ORDER_ID,COMMENTS from TNA_PROGRESS_COMMENTS where  TASK_ID=10 and task_type=5","ORDER_ID,COMMENTS", 1,"-Select-", $selected,"","" ); ?></td>
                                <td id="season_td"><?=create_drop_down( "cbo_season_id", 80, $blank_array,'', 1, "--Season--",$selected, "" ); ?>
                      			<td><input name="txt_sample_name" id="txt_sample_name" class="text_boxes" style="width:60px"></td>
                                <td><input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:70px"></td>
                                <td> 
                                    <? 
									  	$comp_status_arr=array(1=>"Pending",2=>"Complete");
										echo create_drop_down( "cbo_comp_status", 80, $comp_status_arr,"", 1, "-- All --", 0, "" );
									?>
                                </td>
                                <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From"/></td>
                                <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To"/></td>
                                <td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:70px;" onClick="fn_report_generated(1);"/></td>
                            </tr>
                            <tr>
                                <td colspan="13" align="center" valign="middle">
									<? 
									echo load_month_buttons(1);
									// echo get_help_button('../../../','sweater_sample_progress_monitoring_report',1);
									?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

<script>
$('#cbo_approval_type').val(0);
</script>
</html>