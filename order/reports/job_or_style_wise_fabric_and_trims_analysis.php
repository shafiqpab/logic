<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Job/Style Wise Fabric And Trims Analysis Report Report
				
Functionality	:	
JS Functions	:	
Created by		:	Tipu
Creation date 	: 	25-04-2020
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
//------------------------------------------------------------------------------------------------------
echo load_html_head_contents(" Style Wise Trims Received Issue and Stock Report","../../", 1, 1, $unicode,1,1); 
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
	
	function openmypage_style()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/job_or_style_wise_fabric_and_trims_analysis_controller.php?data='+data+'&action=style_popup', 'style Search', 'width=480px,height=420px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theemailid=this.contentDoc.getElementById("txt_po_id");
			var theemailval=this.contentDoc.getElementById("txt_po_val");
			if (theemailid.value!="" || theemailval.value!="")
			{
				//alert (theemailid.value);
				freeze_window(5);
				$("#txt_style").val(theemailid.value);
				$("#txt_style_id").val(theemailval.value);
				release_freezing();
			}
		}
	}
	
	function openmypage_order()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		
		var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val()+"_"+$("#txt_style").val()+"_"+$("#cbo_year_selection").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/job_or_style_wise_fabric_and_trims_analysis_controller.php?data='+data+'&action=order_no_popup', 'Order No Search', 'width=450px,height=420px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theemailid=this.contentDoc.getElementById("txt_po_id");
			var theemailval=this.contentDoc.getElementById("txt_po_val");
			if (theemailid.value!="" || theemailval.value!="")
			{
				//alert (theemailid.value);
				freeze_window(5);
				$("#txt_order_no_id").val(theemailid.value);
				$("#txt_order_no").val(theemailval.value);
				release_freezing();
			}
		}
	}	
	
	function fn_report_generated(operation)
	{
		var style=document.getElementById('txt_style').value;	
		var order_no=document.getElementById('txt_order_no').value;
		var cbo_brand_id=document.getElementById('cbo_brand_id').value;
		var cbo_season_id=document.getElementById('cbo_season_id').value;
		var cbo_season_year=document.getElementById('cbo_season_year').value;
		
	
		if(style!="" || order_no!="" || cbo_brand_id!=0 || cbo_season_id!=0 || cbo_season_year!=0)
		{
			if(form_validation('cbo_company_id','Company')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_id*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
			{
				return;
			}
		}
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*txt_style*txt_style_id*txt_order_no*txt_order_no_id*txt_date_from*txt_date_to*cbo_year*cbo_brand_id*cbo_season_id*cbo_season_year',"../../")+'&report_title='+report_title+'&operation='+operation;
		freeze_window(3);
		http.open("POST","requires/job_or_style_wise_fabric_and_trims_analysis_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;  
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			/*var reponse=trim(http.responseText).split("**");
			//alert (reponse[0]);
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			setFilterGrid("tbl_issue_status",-1);
			setFilterGrid("tbl_issue_status2",-1);*/
			//===============
			var response=trim(http.responseText).split("**");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window();" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			if(response[0]==1)
			{
				setFilterGrid("tbl_issue_status",-1);
				setFilterGrid("tbl_issue_status2",-1);
			}
			else
			{
				//setFilterGrid("tbl_status",-1);
			}
			//===============
	 		show_msg('3');
			release_freezing();
			
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		document.getElementById('scroll_body2').style.overflow="auto";
		document.getElementById('scroll_body2').style.maxHeight="none";
		
		$("#tbl_issue_status tr:first").hide();
		$("#tbl_issue_status2 tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="450px";
		document.getElementById('scroll_body2').style.overflowY="scroll";
		document.getElementById('scroll_body2').style.maxHeight="450px";
		
		$("#tbl_issue_status tr:first").show();
		$("#tbl_issue_status2 tr:first").show();
	}
	
	function openmypage_des(po_id,item_group,des_prod,action)
	{ //alert(des_prod)
		var companyID = $("#cbo_company_id").val();
		var popup_width='500px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/job_or_style_wise_fabric_and_trims_analysis_controller.php?companyID='+companyID+'&po_id='+po_id+'&item_group='+item_group+'&action='+action+'&des_prod='+des_prod, 'Details Veiw', 'width='+popup_width+', height=250px,center=1,resize=0,scrolling=0','../');
	}

	function search_populate(str)
	{
		if(str==1)
		{
			document.getElementById('search_by_th_up').innerHTML="Shipment Date";
		
		}
		else if(str==2)
		{
			document.getElementById('search_by_th_up').innerHTML="Transaction Date";
		
		}
	}

	function openmypage(po_id,item_group,item_color,gmts_color,item_size,recv_basis,without_order,type,action)
	{  //alert(type)
		var companyID = $("#cbo_company_id").val();
		if(type==2) var popup_width='750px';else var popup_width='700px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/job_or_style_wise_fabric_and_trims_analysis_controller.php?companyID='+companyID+'&po_id='+po_id+'&item_group='+item_group+'&item_color='+item_color+'&gmts_color='+gmts_color+'&item_size='+item_size+'&recv_basis='+recv_basis+'&without_order='+without_order+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../');
	}
	
	function openmypage2(po_id,item_group,item_data,type,action)
	{  //alert(type)
		var companyID = $("#cbo_company_id").val();
		if(type==2) var popup_width='750px';else var popup_width='700px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/job_or_style_wise_fabric_and_trims_analysis_controller.php?companyID='+companyID+'&po_id='+po_id+'&item_group='+item_group+'&item_data='+item_data+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../../');
	}

	function finish_openmypage(po_id,fabric_deter_id,fabr_color_id,recv_basis,without_order,type,action)
	{  //alert(type)
		var companyID = $("#cbo_company_id").val();
		if(type==2) var popup_width='750px';else var popup_width='700px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/job_or_style_wise_fabric_and_trims_analysis_controller.php?companyID='+companyID+'&po_id='+po_id+'&fabric_deter_id='+fabric_deter_id+'&fabr_color_id='+fabr_color_id+'&recv_basis='+recv_basis+'&without_order='+without_order+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../');
	}
	function order_openmypage(job_no,action)
	{  //alert(type)
		var companyID = $("#cbo_company_id").val();
		var popup_width='850px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/job_or_style_wise_fabric_and_trims_analysis_controller.php?companyID='+companyID+'&job_no='+job_no+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../');
	}

	 function generate_trim_report(action,txt_booking_no,cbo_company_name,id_approved_id,cbo_isshort)
	{
			var show_comment='';
			var r=confirm("Press  \"Cancel\"  to hide  comments\nPress  \"OK\"  to Show comments");
			if (r==true) show_comment="1"; else show_comment="0";
			var report_title="";
			if(cbo_isshort==1)
			{
				report_title="Short Trims Booking [Multiple Order]";
			}
			else
			{
				report_title="Multi Job Wise Trim Booking";
			}
			var data="action="+action+'&report_title='+"'"+report_title+'&txt_booking_no='+"'"+txt_booking_no+"'"+'&cbo_company_name='+cbo_company_name+'&id_approved_id='+id_approved_id+'&report_type=1&link=1';
				if(cbo_isshort==1)
				{
					http.open("POST","../woven_gmts/requires/short_trims_booking_multi_job_controllerurmi.php",true);
				}
				else
				{
					http.open("POST","../woven_gmts/requires/trims_booking_multi_job_controllerurmi.php",true);
				}
			//}
	
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_trim_report_reponse;

	} 


	 function generate_trim_report_reponse()
	{
		if(http.readyState == 4)
		{
			release_freezing();
			var file_data=http.responseText.split("****");
			//alert(file_data[2]);
			if(file_data[2]==100)
			{
				var html=file_data[1];
			}
			else
			{
				var html=file_data[1];
				var html=file_data[0];
			}
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+html+'</body</html>');
			d.close();
	
			/* var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../css/prt.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
			var content=document.getElementById('data_panel').innerHTML; */
		}
	}  
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>    		 
        <form name="greyissuestatus_1" id="greyissuestatus_1" autocomplete="off" > 
         	<h3 style="width:1200px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
			<div id="content_search_panel" style="width:1200px" >      
	            <fieldset>  
	                <table class="rpt_table" width="1200" cellpadding="0" cellspacing="0" border="1" rules="all">

	                    <thead>
	                        <th class="must_entry_caption">Company</th>
	                        <th>Buyer</th>
							<th>Brand</th>
							<th>Season</th>
							<th>Season Year</th>
	                        <th>Year</th>
	                        <th>Style/Job No</th>
	                        <th>Order No.</th>
	                        <th align="center" class="must_entry_caption">Shipment Date</th>
	                       	<th><input type="reset" name="res" id="res" value="Reset" style="width:40px" onClick="$('#txt_style').val('');$('#txt_order_no_id').val('');" class="formbutton" /></th>
	                    </thead>

	                    <tbody>
	                        <tr class="general">
	                            <td>
	                                <? 
	                                    echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/job_or_style_wise_fabric_and_trims_analysis_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
	                                ?>                            
	                            </td>
	                            <td id="buyer_td">
									<?
										echo create_drop_down( "cbo_buyer_id", 150,$blank_array,"", 1, "-- Select Buyer--", $selected, "","","","","","");
	                                ?> 
	                          	</td>
								<td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 70, $blank_array,'', 1, "--Brand--",$selected, "" ); ?></td>
								<td id="season_td"><?=create_drop_down( "cbo_season_id", 70, $blank_array,'', 1, "--Season--",$selected, "" ); ?></td>
								<td><? echo create_drop_down( "cbo_season_year", 60, create_year_array(),"", 1,"-Year-", 1, "",0,"" );?> 
	                          	</td>
	                          	<td>
									<?
										echo create_drop_down( "cbo_year", 70, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
	                                ?> 
	                          	</td>
	                            <td>
	                            	<input style="width:120px;" name="txt_style_id" id="txt_style_id" class="text_boxes" onDblClick="openmypage_style()" placeholder="Browse Style" readonly />
	                                <input type="hidden" name="txt_style" id="txt_style" style="width:90px;"/>
	                            </td>
	                            <td >
	                            	<input style="width:120px;" name="txt_order_no" id="txt_order_no" class="text_boxes" onDblClick="openmypage_order()" onChange="$('#txt_order_no_id').val('');" placeholder="Browse/Write Order"  />
	                                <input type="hidden" name="txt_order_no_id" id="txt_order_no_id" style="width:90px;"/>
	                            </td>	                            
	                            <td>
	                                <input type="text" name="txt_date_from" id="txt_date_from" value="<? //echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:50px;" />
	                                To
	                                <input type="text" name="txt_date_to" id="txt_date_to" value="<? //echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:50px;"/>
	                            </td>
	                            <td>
	                                <input type="button" name="search" id="search" value="Show" onClick="fn_report_generated(1)" style="width:45px" class="formbutton" />  &nbsp;<input type="button" name="search" id="search" value="Show2" onClick="fn_report_generated(2)" style="width:45px" class="formbutton" />
									<input type="button" name="search3" id="search3" value="Show 3" onClick="fn_report_generated(3)" style="width:45px" class="formbutton" />
	                            </td>                           
	                        </tr>                       
	                    </tbody>

	                    <tfoot>
	                        <tr>
	                            <td colspan="11" align="center"><? echo load_month_buttons(1);  ?>
	                            </td>
	                        </tr>
	                    </tfoot>

	                </table> 
	            </fieldset> 
			</div>
            <div id="report_container" align="center"></div>
            <div id="report_container2"></div> 
        </form>    
    </div>
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
