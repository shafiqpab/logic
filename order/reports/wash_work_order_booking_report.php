<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Wash Work Order [Booking] Report
Functionality	:	
JS Functions	:
Created by		:	Md Mamun Ahmed Sagor
Creation date 	: 	18-04-2022
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
echo load_html_head_contents("Wash Work Order [Booking] Report", "../../", 1, 1,$unicode,'1','');
?>	

<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';


	var tableFilters = 
	{
		col_operation: {
		id: ["tot_order_qty","tot_smv","tot_value"],
		col: [29,31,33],
		operation: ["sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML"]
		}
	} 
	
	function fn_report_generated(action)
	{
			var work_company=$("#cbo_company_name").val();
			var job=$("#txt_job_no").val();
			var date_from=$("#txt_date_from").val();
			var date_to=$("#txt_date_to").val();
			var wo_status=$("#cbo_wo_status").val();

			if(work_company==''){
				if(form_validation('cbo_company_name','Company Name')==false )
				{
					return;
				}
			}
			
			
			var report_title=$( "div.form_caption" ).html();
			var data="action="+action+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year*txt_job_no*txt_job_id*txt_style_ref*txt_date_from*txt_date_to*cbo_team_leader*cbo_date_type*cbo_emb_type*cbo_wo_status',"../../")+"&report_title="+report_title;
			//alert(data);
			//return;
			
			http.open("POST","requires/wash_work_order_booking_report_controller.php",true);
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
			//var search_by=document.getElementById('cbo_search_by').value;
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		
			show_msg('3');
			setFilterGrid("table_body",-1,tableFilters);
			release_freezing();
			
		}
	}

	function openmypage_order_details(job_id,po_id,emb_type,action)
	{
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/wash_work_order_booking_report_controller.php?po_id='+po_id+'&job_id='+job_id+'&emb_type='+emb_type+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
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

	function new_window(html_filter_print)
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		$(".flt").css("display","none");
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_print.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
		$(".flt").css("display","block");
	}	
	
	function print_report_button_setting(report_ids)
	{
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==50)
			{
				$("#report_btn_1").show();	 
			}
			if(report_id[k]==51)
			{
				$("#report_btn_2").show();	 
			}
			if(report_id[k]==52)
			{
				$("#report_btn_3").show();	 
			}
			if(report_id[k]==63)
			{
				$("#report_btn_4").show();	 
			}
		}
	}
	
	function generate_report(company,job_no,buyer_name,style_ref_no,costing_date,po_id,costing_per,type)
	{
		
		var zero_val='';
		var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		if (r==true)
		{
			zero_val="1";
		}
		else
		{
			zero_val="0";
		} 
		var data="action="+type+"&zero_value="+zero_val
			+"&txt_job_no='"+job_no
			+"'&cbo_company_name="+company
			+"'&cbo_buyer_name="+buyer_name
			+"'&txt_style_ref="+style_ref_no
			+"'&txt_costing_date="+costing_date
			+"'&txt_po_breack_down_id="+po_id
			+"'&cbo_costing_per="+costing_per
		;
		http.open("POST","../woven_order/requires/pre_cost_entry_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_report_reponse;
	}
	
	function fnc_generate_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><body>'+http.responseText+'</body</html>');
			d.close();

			setFilterGrid("table_body",-1,tableFilters);
		}

	}

	function openmypage_jobstyle(type)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();
		var cbo_year = $("#cbo_year").val();
		var from_date = $("#txt_date_from").val();
		var to_date = $("#txt_date_to").val();
		var txt_style_ref = $("#txt_style_ref").val();
		var page_link='requires/wash_work_order_booking_report_controller.php?action=job_style_popup&companyID='+company+'&buyer_name='+buyer+'&txt_style_ref='+txt_style_ref+'&cbo_year='+cbo_year+'&type='+type+'&from_date='+from_date+'&to_date='+to_date;
		var title="Search Job/Style Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=400px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var selected_data=this.contentDoc.getElementById("txt_selected_data").value;
			var selected_type=this.contentDoc.getElementById("txt_selected_type").value;
			var paramArr = selected_data.split("_");
			if(selected_type==2)
			{
				$("#txt_style_ref").val(paramArr[2]);
			}
			if(selected_type==1)
			{
				$("#txt_job_no").val(paramArr[1]);
				$("#txt_job_id").val(paramArr[0]);
			}
			//$("#txt_style_ref").val(style_des);
			//$("#txt_style_ref_id").val(style_id); 
			//$("#txt_style_ref_no").val(style_no); 
		}
	}
	
	function openmypage(po_id,item_name,job_no,book_num,trim_dtla_id,action)
	{ //alert(book_num);
		var cbo_company_name=$("#cbo_company_name").val();
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/accessories_followup_budget2_report_controller.php?po_id='+po_id+'&item_name='+item_name+'&job_no='+job_no+'&book_num='+book_num+'&trim_dtla_id='+trim_dtla_id+'&action='+action+'&cbo_company_name='+cbo_company_name, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}

	function getBuyerId() 
	{
	    var company_name = document.getElementById('cbo_company_name').value;
		
	    if(company_name !='') {
		  var data="action=load_drop_down_buyer&data="+company_name;
		  //alert(data);die;
		  http.open("POST","requires/wash_work_order_booking_report_controller.php",true);
		  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		  http.send(data); 
		  http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#buyer_td').html(response);
				  
				  set_multiselect('cbo_buyer_name','0','0','','0');
	          }			 
	      };
	    }         
	}	

</script>

</head>

<body onLoad="set_hotkey();">
    <form id="materialsFollowup_report">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",''); ?>
    <h3 align="left" id="accordion_h1" style="width:1200px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
            <fieldset style="width:1200px;">
                <table class="rpt_table" width="1200" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
                    <thead>
                        <tr>                    
                            <th width="150" class="must_entry_caption">Company Name</th>
							<th width="100">Team Name</th>
                            <th width="130">Buyer Name</th>
                            <th width="50">Year</th>
                            <th width="90">Job No</th>
                            <th width="100">Style Ref.</th>
							<th width="80">Order Type</th>
							<th width="80">Emb. Type</th>
							<th width="80">Wo Status</th>
							<th width="80">Date Type</th>
                            <th width="130" colspan="2"> Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td id="lccompany_td"><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "-- Select Company --", $selected, "" ); ?></td>
						
                          <td id="leader_td"><?=create_drop_down( "cbo_team_leader", 100, "select id,team_name from lib_marketing_team where project_type=2 and status_active =1 and is_deleted=0 order by team_name","id,team_name", 1, "Select Team", $selected, ""); ?></td>
                            <td id="buyer_td"> <? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
                            <td><?
							$selected_year=date("Y");
							echo create_drop_down( "cbo_year", 50, create_year_array(),"", 1,"-All Year-", $selected_year, "",0,"" ); ?></td>
                            <td><input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:78px" placeholder="Wr./Browse" onDblClick="openmypage_jobstyle(1)">
							<input type="hidden" name="txt_job_id" id="txt_job_id" class="text_boxes" style="width:90px" ></td>
                            <td><input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:88px" placeholder="Wr./Browse" onDblClick="openmypage_jobstyle(2)" >
							<input type="hidden" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:90px" ></td>
							</td>
							<td> <? 
								$order_type=array(1=>'Confirmed',2=>'Projected');
								echo create_drop_down( "cbo_order_type", 80, $order_type,"", 0, "--Select  --", $selected, "",0,"" ); ?>
							</td>
							<td> <? 
								$emb_type=array(1=>'Printing',2=>'Embroidary',3=>'Wash');
								echo create_drop_down( "cbo_emb_type", 80, $emb_type,"", 0, "--Select  --", 3, "",0,"" ); ?>
							</td>
							<td> <? 
								$wo_status=array(1=>'WO Pending',2=>'WO Partial Pending',3=>'WO Done');
								echo create_drop_down( "cbo_wo_status", 100, $wo_status,"", 0, "--Select  --", $selected, "",0,"" ); ?>
							</td>
							<td> <? 
							$date_type=array(1=>'Ship Date',2=>'Pub Ship Date',3=>'Country Ship Date',4=>'PHD Date');
							echo create_drop_down( "cbo_date_type", 80, $date_type,"", 1, "--Select Date type --", $selected, "",0,"" ); ?></td>
                            <td ><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date" ></td>
							<td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date" ></td>
                            <td>
                                <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated('report_generate')" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="13" align="center"><? echo load_month_buttons(1); ?></td>                         
                     </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" ></div>
    </form>    

</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script class="">
	set_multiselect('cbo_company_name','0','0','0','0');
	set_multiselect('cbo_team_leader','0','0','0','0');
	set_multiselect('cbo_buyer_name','0','0','0','0');
	 setTimeout[($("#lccompany_td a").attr("onclick","disappear_list(cbo_company_name,'0'); getBuyerId();"),3000)]; 
	 setTimeout[($("#leader_td a").attr("onclick","disappear_list(cbo_team_leader,'0');"),3000)]; 
</script>
</html>
