<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Count Wise Yarn Requirement Report
				
Functionality	:	
JS Functions	:
Created by		:	Helal Uddin
Creation date 	: 	12-07-2020
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
echo load_html_head_contents("Knitting Bill Report","../../", 1, 1, $unicode,1,1); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	
	
	
	function generate_wo_order_report(company_id,knitting_wo_id)
	{
		
		
		print_report( company_id+'**'+knitting_wo_id,"work_order_print", "requires/piece_rate_wo_bill_report_controller");
	}
	
	

	function generate_report()
	{
		
		var txt_date_from = $("#txt_date_from").val();
		var txt_date_to = $("#txt_date_to").val();
		var hide_wo_dtls_id = $("#hide_wo_dtls_id").val();
		var txt_wo_no = $("#txt_wo_no").val();
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		if(txt_wo_no=='' && hide_wo_dtls_id=='')
		{
			if(txt_date_from=='' || txt_date_to=='')
			{
				alert('Please Fill date First');
				return;
			}
		}
		
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_working_company*cbo_buyer_name*hide_wo_dtls_id*cbo_date_type*txt_wo_no*cbo_source',"../../")+'&txt_date_from='+txt_date_from+'&txt_date_to='+txt_date_to+'&report_title='+report_title;
		
		freeze_window(3);
		http.open("POST","requires/piece_rate_wo_bill_report_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window();" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
			//var batch_type = document.getElementById('cbo_batch_type').value;
			
			setFilterGrid("table_body",-1);
			
			show_msg('3');
			release_freezing();
		}
	} 
	function openmypage_job()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_id").val();
	        
		var page_link='requires/piece_rate_wo_bill_report_controller.php?action=job_no_search_popup&companyID='+companyID;
		var title='Job No Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1090px,height=450px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			
			$('#txt_wo_no').val(job_no);
			$('#hide_wo_dtls_id').val(job_id);	 
		}
	}
	
	function new_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";
		
		$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="400px";
		
		$("#table_body tr:first").show();
	}

	function getCompanyId() 
	{	 
	    var company_id = document.getElementById('cbo_company_id').value;
		//var splidData=company_id.split(',');
		load_drop_down( 'requires/piece_rate_wo_bill_report_controller',$('#cbo_source').val()+'**'+company_id,'load_drop_down_working_company','working_company_td' );
		load_drop_down( 'requires/piece_rate_wo_bill_report_controller',company_id, 'load_drop_down_buyer', 'buyer_td' );
		callMultiSelectForWoCompany();
	}
	function callMultiSelectForWoCompany()
	{
		set_multiselect('cbo_working_company','0','0','0','0');
	}
	
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission); ?>   		 
    <form name="knitting_bill_report_1" id="knitting_bill_report_1" autocomplete="off" > 
    <h3 style="width:1370px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1270px;">
                <table class="rpt_table" width="1350" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="140" class="must_entry_caption">Company</th>                                
                            <th width="140">Source</th>
                            <th width="140">Working Company</th>
                            <th width="140">Buyer</th>
                            <th width="120">Search by</th>
                            
                            <th width="100">Based On</th>
                            <th width="160" class="must_entry_caption">Date Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('knitting_bill_report_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td id="td_company">
                            <? 
                               echo create_drop_down( "cbo_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>                            
                        </td>

                        
                       
                          
	                    <td >

							<?
								echo create_drop_down( "cbo_source", 162, $knitting_source, "",1, "-- Select --", 0, "load_drop_down( 'requires/piece_rate_wo_bill_report_controller',this.value+'**'+$('#cbo_company_id').val(),'load_drop_down_working_company','working_company_td' );callMultiSelectForWoCompany();",0,"1,3" );

								?>
	                    </td>

	                    <td id="working_company_td">
	                    	 <?
								echo create_drop_down("cbo_working_company", 160, $blank,"", 1,"-- Select Company --", 0,"","","");
	                        ?>
	                    </td>
	                    <td width="120" id="buyer_td">
	                        <? 
	                            echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
	                        ?>
	                    </td>
                        <td>
                        	<? /*
								$search_by_arr = array(1=>"Style ref. No",2 => "Job No", 3 => "PO",4=>"WO No",5=>"Bill No");
								$dd = "change_search_event(this.value, '0*0*0*0*0', '0*0*0*0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 140, $search_by_arr, "", 0, "", "", $dd, 0);
								*/
								?>

								 <input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:170px" placeholder="Browse" onDblClick="openmypage_job();" autocomplete="off" readonly>
                           		 <input type="hidden" name="hide_wo_dtls_id" id="hide_wo_dtls_id" readonly>
									
						</td>
                       
                        <td> 
                            <?
								$search_by=array(1=>'Bill Date',2=>'WO Date');
                                echo create_drop_down( "cbo_date_type", 100, $search_by,"", 0, "--Select--", 1, "",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker " style="width:60px" placeholder="From Date"/>
                             To
                             <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker " style="width:60px" placeholder="To Date"/>
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report();" style="width:70px" class="formbutton" />
                        </td>
                    </tr>
                    <tr>
						<td colspan="8" align="center" width="100%"><? echo load_month_buttons(1); ?></td>                    
					</tr>
                </table> 
            </fieldset> 
        </div>
            <div id="report_container" align="center"></div>
            <div id="report_container2"></div>   
    </form>    
</div>    
</body>  

<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 

	<script> 
		
		set_multiselect('cbo_company_id*cbo_working_company','0*0','0','0','0');
		//set_multiselect('cbo_working_company','0','0','0','0');
		setTimeout[($("#td_company a").attr("onclick","disappear_list(cbo_company_id,'0');getCompanyId();") ,3000)];
		//setTimeout[($("#working_company_td a").attr("onclick","disappear_list(cbo_working_company,'0');") ,3000)];
	</script>
</html>

</html>
