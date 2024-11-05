<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create  OMS Report.
Functionality	:	
JS Functions	:
Created by		:	Aziz 
Creation date 	: 	8-03-2016
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

echo load_html_head_contents("OMS Report","../../../", 1, 1, $unicode,1,1);
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
			
	function fn_report_generated(type)
	{
		var job_no=document.getElementById('txt_job_no').value;	
		var order_no=document.getElementById('txt_order_no').value;
		var season=document.getElementById('cbo_season_id').value;
		var file_no=document.getElementById('txt_file_no').value;
		var internal_ref=document.getElementById('txt_internal_ref').value;
		
		/*if (type==1 || type==2 || type==3)
		{
			if(job_no!="" || order_no!="" || season!=0 || file_no!="" || internal_ref!="")
			{
				if(form_validation('cbo_company_name','Company')==false)
				{
					return;
				}
			}
			else
			{
				if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
				{
					return;
				}
			}
		}
		else
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
			{
				return;
			}
		}*/
		
		var data="action=report_generate&reporttype="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to*txt_job_no*txt_job_id*txt_order_id*txt_order_no*cbo_year*cbo_order_status*cbo_search_date*cbo_season_id*txt_file_no*txt_internal_ref*cbo_style_owner*cbo_team_name*cbo_deal_marchant',"../../../");
		//alert(data);return;
		freeze_window(3);
		if(type==1 || type==2 ||  type==3  ||  type==4  ||  type==5)
		{
			http.open("POST","requires/work_load_report_controller.php",true);
		}
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			if(reponse[2]==3 || reponse[2]==4){
				document.getElementById('excel').click();
			}
			else if(reponse[2]==5){
				setFilterGrid("table_body",-1,1);	
			}
			release_freezing();
			show_msg('3');
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		 
		$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_print.css" type="text/css"  /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflow="scroll"; 
		document.getElementById('scroll_body').style.maxHeight="350px";
		
		$("#table_body tr:first").show();
	}
	
	function openmypage_job()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_year_id = $("#cbo_year").val();
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/work_load_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_job_no').val(job_no);
			$('#txt_job_id').val(job_id);	 
		}
	}
	
	function openmypage_order()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value;
		//alert (data);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/work_load_report_controller.php?action=order_no_popup&data='+data,'Order No Popup', 'width=630px,height=420px,center=1,resize=0','../../')
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
	}
	
	function generate_order_report(po_id,company_id,job_no,buyer_id,style,ship_date,action,type_id)
	{  
		
		if(type_id==1) var popup_width=730;
		else if(type_id==2) var popup_width=730+80;
		else if(type_id==3) var popup_width=540;
		else if(type_id==4) var popup_width=530;
		else if(type_id==5) var popup_width=920;
		else if(type_id==6) var popup_width=650;
		
		//var popup_width='730px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/work_load_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style='+style+'&ship_date='+ship_date+'&type_id='+type_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
	}
	
	
	
	
	function country_order_dtls(po_id,country_date,buyer_id,job_no,action)
	{  
		if (action=="country_trims_dtls_popup")
		{
			var popup_width='850px';
		}
		else
		{
			var popup_width='750px';
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/work_load_report_controller.php?po_id='+po_id+'&country_date='+country_date+'&buyer_id='+buyer_id+'&job_no='+job_no+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	function country_order_dtls_trim(po_id,country_id,buyer_id,job_no,action)
	{  
		 
		var popup_width='850px';
		//country_trims_dtls_popup
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/work_load_report_controller.php?po_id='+po_id+'&country_id='+country_id+'&buyer_id='+buyer_id+'&job_no='+job_no+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	
	function new_window1(type)
	{
		var report_div='';
		var scroll_div='';
		if(type==1)
		{
			report_div="yarn_summary";
			//scroll_div='scroll_body';
		}
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById(report_div).innerHTML+'</body</html>');
		d.close();
	}
	
	function precost_bom_pop(po_id,job_no,company_id,buyer_id)
	{ 
		//alert(po_id);  
		/*var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/oms_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');*/
		var data="&action=bomRpt"+
					'&txt_po_breack_down_id='+"'"+po_id+"'"+
					'&txt_job_no='+"'"+job_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&cbo_buyer_name='+"'"+buyer_id+"'";
					//alert(data);
		http.open("POST","../../../order/woven_order/requires/pre_cost_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4) 
		    {
				var w = window.open("Surprise", "_blank");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
				d.close();
		   }
		}
	}
	
	function search_populate(str)
	{
		if(str==1)
		{
			document.getElementById('search_by_th_up').innerHTML="Shipment Date";
			$('#search_by_th_up').css('color','blue');
		}
		else if(str==2)
		{
			document.getElementById('search_by_th_up').innerHTML="PO Received Date";
			$('#search_by_th_up').css('color','blue');
		}
		else if(str==3)
		{
			document.getElementById('search_by_th_up').innerHTML="PO Insert Date";
			$('#search_by_th_up').css('color','blue');
		}
	}
	
	function openmypage_season()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var buyerID = $("#cbo_buyer_name").val();
		var job_no = $("#txt_job_no").val();
		var page_link='requires/work_load_report_controller.php?action=search_popup&companyID='+companyID+'&buyerID='+buyerID+'&job_no='+job_no;
		var title='Season Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=350px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hide_season=this.contentDoc.getElementById("hide_season").value;
	
			$('#txt_season').val(hide_season);
		}
	}
	
	 
	
	
</script>
</head>
<body onLoad="set_hotkey();">
<form id="costSheetReport_1">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../",$permission);  ?>
          <h3 align="left" id="accordion_h1" style="width:1450px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
         <fieldset style="width:1000px;" id="content_search_panel">
            <table class="rpt_table" width="1000" cellpadding="1" cellspacing="2" align="center" border="1" rules="all">
                <thead>                    
                    <th class="must_entry_caption">Company</th>
                    <th>Buyer</th>
                    <th>Year</th>
                    <th>Job No.</th>
                    <th>Style Owner</th>
                    <th>Team</th>
                    <th>Deal Merchant</th>
                    <th>Order Status</th>
                    <th>File No</th>
                    <th>Internal Ref.</th>
                    <th>Order</th>
                    <th>Season</th>
                    <th>Search By</th>
                    <th colspan="2" id="search_by_th_up" class="must_entry_caption">Shipment Date</th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td> 
							<?
                           		echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- All Company --", $selected, "load_drop_down( 'requires/work_load_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
							<? 
                            	echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "--All Buyer--", $selected, "",0,"" );
                            ?>
                        </td>
                        <td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", "", "",0,"" ); ?></td>
                        <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:80px" onDblClick="openmypage_job();" placeholder="Wr./Br. Job" />
                            <input type="hidden" id="txt_job_id" name="txt_job_id"/>
                             <input type="hidden" id="report_ids" name="report_ids"/>
                        </td>
                         <td >
							<? 
                            echo create_drop_down( "cbo_style_owner", 120, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond and id=3 order by company_name","id,company_name", 1, "--Style Owner--", $selected, ""); ?>
                           
                        </td>
                        <td >                
                            <?
                                echo create_drop_down( "cbo_team_name", 100, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, " load_drop_down( 'requires/work_load_report_controller', this.value, 'load_drop_down_deal_marchant', 'team_td' );" );
                            ?>
                        </td>
                        <td id="team_td">
                        <div>
                            <? 
                                echo create_drop_down( "cbo_deal_marchant", 110, $blank_array,"", 1, "- Select Dealing- ", $selected, "" );
                            ?>	
                        </div>
                        </td>
                        
                        <td>
							<? 
								$order_status=array(0=>"All",1=>"Confirmed",2=>"Projected"); 
								echo create_drop_down( "cbo_order_status", 80, $order_status,"", 0, "", 0, "" ); 
                            ?>
                        </td>
                         <td>
                            <input type="text" id="txt_file_no" name="txt_file_no" class="text_boxes" style="width:80px"  placeholder="Write File"  />                           
                        </td>
                         <td>
                            <input type="text" id="txt_internal_ref" name="txt_internal_ref" class="text_boxes" style="width:80px"  placeholder="Write Ref"  />                        </td>
                        <td>
                            <input type="text" id="txt_order_no" name="txt_order_no" class="text_boxes" style="width:80px" onDblClick="openmypage_order();" placeholder="Wr./Br. Order"  />
                            <input type="hidden" id="txt_order_id" name="txt_order_id"/>
                        </td>
                         <td id="season_td">
                        	<!--<input type="text" name="txt_season" id="txt_season" class="text_boxes" style="width:80px" placeholder="Browse" onDblClick="openmypage_season();" readonly/>-->
                            <? //echo create_drop_down( "txt_season", 150, $blank_array,'', 1, "-- Select Season--",$selected, "" ); 
							echo create_drop_down( "cbo_season_id", 80, "select id, season_name from lib_buyer_season where  status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );?>
                        
                        </td>
                        
                        <td width="" align="center">
							<?  
								$search_by = array(1=>'Shipment Date',2=>'Po Received Date',3=>'Po Insert Date');
								$dd="search_populate(this.value)";
								echo create_drop_down( "cbo_search_date", 100, $search_by,"",0, "--Select--", $selected,$dd,0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:65px" placeholder="From Date" >
                        </td>
                        <td>
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px"  placeholder="To Date" >
                        </td>
                    </tr>
                    <tr align="center"  class="general">
                        <td colspan="15">
                        	<? echo load_month_buttons(1); ?>
                         
                            <input type="reset" id="reset_btn" class="formbutton" style="width:80px;float:right" value="Reset" onClick="reset_form('costSheetReport_1','report_container*report_container2','','','')" />   &nbsp;
                            <input type="button" id="show_button" class="formbutton" style="width:80px; float:right" value="Emblishment" onClick="fn_report_generated(1)" /> &nbsp;
                            <input type="button" id="show_button" class="formbutton" style="width:70px; float:right" value="AOP" onClick="fn_report_generated(2)" /> &nbsp;
                            <input type="button" id="show_button" class="formbutton" style="width:100px; float:right" value="Emblishment XLS" onClick="fn_report_generated(3)" /> &nbsp;
                            <input type="button" id="show_button" class="formbutton" style="width:100px; float:right" value="AOP XLS" onClick="fn_report_generated(4)" /> &nbsp;
                            <input type="button" id="show_button" class="formbutton" style="width:130px; float:right" value="Process Wise Fin Qty" onClick="fn_report_generated(5)" />
                            
                        </td>
                    </tr>
                    <tr >
                    	<td colspan="12" align="center" id="data_panel">
                          
                       	</td>
                    </tr>
                   
                </table> 
            </fieldset>
        </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	$("#cbo_style_owner").val(0);
</script>
</html>