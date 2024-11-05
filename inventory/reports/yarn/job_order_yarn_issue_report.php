<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Job/Order Wise Yarn Issue Report
				
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	18-03-2014
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		: issue id=5563, issue=Buyer does not show in report Job/Order Wise Yarn Issue Report, update by Jahid date 05-08-15
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Job/Order Wise Yarn Issue Report","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_year*cbo_month*cbo_search_by*txt_search_comm*cbo_issue_purpose',"../../../")+'&report_title='+report_title;//*txt_job_no
		//alert (data);
		freeze_window(3);
		http.open("POST","requires/job_order_yarn_issue_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_challan()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate_challan"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_year*cbo_month*cbo_search_by*txt_search_comm*cbo_issue_purpose',"../../../")+'&report_title='+report_title;//*txt_job_no
		//alert (data);
		freeze_window(3);
		http.open("POST","requires/job_order_yarn_issue_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
	}

	/*function openmypage_job_popup()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year").val();
		var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/job_order_yarn_issue_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id+'&cbo_month_id='+cbo_month_id;
		
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			
			$('#txt_job_no').val(job_no);
			$('#hide_job_id').val(job_id);	 
		}
	}
	
	function openmypage_order()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}                                                                                                                                      
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_job_no').value;
		//alert (data);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/job_order_yarn_issue_report_controller.php?action=order_no_popup&data='+data,'Order No Popup', 'width=700px,height=420px,center=1,resize=0','../../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("order_no_id");
			var theemailv=this.contentDoc.getElementById("order_no_val");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_order_id").value=theemail.value;
			    document.getElementById("txt_order_no").value=theemailv.value;
				release_freezing();
			}
		}
	}*/
	
	function openmypage_job(order_id,action,yarn_count,yarn_comp_type1st,yarn_comp_percent1st,yarn_comp_type2nd,yarn_comp_percent2nd,yarn_type,lot,issue_purpose)
	{
		var popup_width='890px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/job_order_yarn_issue_report_controller.php?order_id='+order_id+'&action='+action+'&yarn_count='+yarn_count+'&yarn_comp_type1st='+yarn_comp_type1st+'&yarn_comp_percent1st='+yarn_comp_percent1st+'&yarn_comp_type2nd='+yarn_comp_type2nd+'&yarn_comp_percent2nd='+yarn_comp_percent2nd+'&yarn_type_id='+yarn_type+'&lot='+lot+'&issue_purpose='+issue_purpose, 'Details Veiw', 'width='+popup_width+', height=430px,center=1,resize=0,scrolling=0','../../');
	}
	
	function openmypage_challan(order_id,action,yarn_count,yarn_comp_type1st,yarn_comp_percent1st,yarn_comp_type2nd,yarn_comp_percent2nd,yarn_type,lot,challan)
	{
		var popup_width='890px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/job_order_yarn_issue_report_controller.php?order_id='+order_id+'&action='+action+'&yarn_count='+yarn_count+'&yarn_comp_type1st='+yarn_comp_type1st+'&yarn_comp_percent1st='+yarn_comp_percent1st+'&yarn_comp_type2nd='+yarn_comp_type2nd+'&yarn_comp_percent2nd='+yarn_comp_percent2nd+'&yarn_type_id='+yarn_type+'&lot='+lot+'&challan='+challan, 'Details Veiw', 'width='+popup_width+', height=430px,center=1,resize=0,scrolling=0','../../');
	}

	function change_caption(type)
	{
		if(type==1)
		{
			$('#td_search').html('Enter Job');
			$("#txt_search_comm").val('');
		}
		else if(type==2)
		{
			$('#td_search').html('Enter Style');
			$("#txt_search_comm").val('');
		}
		else if(type==3)
		{
			$('#td_search').html('Enter Order');
			$("#txt_search_comm").val('');
		}
		else if(type==4)
		{
			$('#td_search').html('Enter File');
			$("#txt_search_comm").val('');
		}
		else if(type==5)
		{
			$('#td_search').html('Enter Ref.');
			$("#txt_search_comm").val('');
		}
		else if(type==6)
		{
			$('#td_search').html('Enter Booking No');
			$("#txt_search_comm").val('');
		}
		else if(type==7)
		{
			$('#td_search').html('Enter WO No');
			$("#txt_search_comm").val('');
		}
	}
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>   		 
    <form name="jobordewiseyarnissuereport_1" id="jobordewiseyarnissuereport_1" autocomplete="off" > 
    <h3 style="width:1000px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:880px;">
                <table class="rpt_table" width="870" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="140" class="must_entry_caption">Company</th>                                
                            <th width="140">Buyer</th>
                            <th width="110">Year</th>
                            <th width="110">Month</th>
                            <th width="100">Search By</th>
                            <th width="100" id="td_search">Job No</th>
                            <th width="100">Issue Purpose</th>
                            <th width="70"><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('jobordewiseyarnissuereport_1','report_container*report_container2','','','','');" /></th>
                            <th width="100">&nbsp;</th>
                        </tr>
                    </thead>
                    <tr align="center">
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/job_order_yarn_issue_report_controller',this.value+'_'+1+'_'+4, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>                            
                        </td>
                        <td id="buyer_td"> 
                            <?
                                echo create_drop_down( "cbo_buyer_id", 140, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                            ?>
                        </td>
                        <td> 
                            <?
								$selected_year=date("Y");
                                echo create_drop_down( "cbo_year", 110, $year,"", 1, "--Select Year--", $selected_year, "",0 );
                            ?>
                        </td>
                        <td> 
                            <?
								$selected_month=date("m");
                                echo create_drop_down( "cbo_month", 110, $months,"", 1, "--Select Month--", 0, "",0 );
                            ?>
                        </td>
                        <td> 
                            <?
								$search_by=array(1=>'Job No',2=>'Style Ref.',3=>'Order No',4=>'File No',5=>'Ref. No',6=>'Booking No',7=>'WO No');
                                echo create_drop_down( "cbo_search_by", 100, $search_by,"", 0, "--Select--", 1, "change_caption(this.value);",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_search_comm" name="txt_search_comm" class="text_boxes" style="width:100px" placeholder="Write" />
                        </td>
                        <td>
                            <?
                                //echo create_drop_down("cbo_issue_purpose",100,$yarn_issue_purpose,"",1, "-- Select --", $selected, "");
                                echo create_drop_down( "cbo_issue_purpose", 100, $yarn_issue_purpose,"", 1, "--Select Purpose--",$selected,"","","1,2,4");
                            ?>
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Job Wise" onClick="generate_report()" style="width:70px" class="formbutton" />
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Challan Wise" onClick="generate_report_challan()" style="width:100px" class="formbutton" />
                        </td>
                    </tr>
                    <tr>
						<!--<td colspan="6" align="center" width="100%"><?// echo load_month_buttons(1); ?></td>-->                    
					</tr>
                </table> 
            </fieldset> 
        </div>
            <div id="report_container" align="center" style="padding: 10px;"></div>
            <div id="report_container2"></div>   
    </form>    
</div>    
</body>  
<script>
	//set_multiselect('cbo_yarn_type*cbo_yarn_count','0*0','0*0','','0*0');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
