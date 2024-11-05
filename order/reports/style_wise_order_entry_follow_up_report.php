<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Style Wise materials Follow up Report (Woven)
Functionality	:	
JS Functions	:
Created by		:	Zakaria joy
Creation date 	: 	22-05-2021
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
echo load_html_head_contents("Accessories Followup Report[Budget 2]", "../../", 1, 1,$unicode,'1','');
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
	
	function fn_report_generated(action,type)
	{
			var work_company=$("#cbo_company_name").val();
			var job=$("#txt_job_no").val();
			var style=$("#txt_style_ref").val();
			var date_from=$("#txt_date_from").val();
			var date_to=$("#txt_date_to").val();
			if(work_company==''){
					if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false )
					{
						return;
					}
				}
			if(type==1){
				
				if((job=='' && style=='')  &&  date_from=='')
				{
					if(form_validation('txt_date_from*txt_date_to','Date From*Date To')==false )
					{
						return;
					}
				}
			}else{

				if(job=='' && style=='')
				{
					if(form_validation('txt_job_no*txt_style_ref','Job No*Style Ref')==false )
					{
						return;
					}
				}

			}

			
			
			var report_title=$( "div.form_caption" ).html();
			var data="action="+action+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year*txt_job_no*txt_style_ref*txt_date_from*txt_date_to*cbo_team_leader*cbo_date_type',"../../")+"&report_title="+report_title;
			//alert(data);
			//return;
			freeze_window(3);
			http.open("POST","requires/style_wise_order_entry_follow_up_report_controller.php",true);
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

		// 	document.getElementById('scroll_body').style.overflow="auto";
		// 	document.getElementById('scroll_body').style.maxHeight="none"; 
		// 	$(".flt").css("display","none");		
		// 	var w = window.open("Surprise", "#");
		// 	var d = w.document.open();
		// 	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		// '<html><head><link rel="stylesheet" href="../../css/style_print.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		// 	d.close(); 
			
		// 	document.getElementById('scroll_body').style.overflowY="scroll"; 
		// 	document.getElementById('scroll_body').style.maxHeight="400px";
		// 	$(".flt").css("display","block");
		// 	document.getElementById('scroll_body').style.overflow="auto";
		// 	document.getElementById('scroll_body').style.maxHeight="none";
		// 	if(html_filter_print*1>1) $("#table_body tr:first").hide();

		

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+

	'<html><head><link rel="stylesheet" href="../../css/style_print.css" type="text/css"   /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="400px";
		if(html_filter_print*1>1) $("#table_body tr:first").show();

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
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><body>'+http.responseText+'</body</html>');
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
		var page_link='requires/style_wise_order_entry_follow_up_report_controller.php?action=job_style_popup&companyID='+company+'&buyer_name='+buyer+'&txt_style_ref='+txt_style_ref+'&cbo_year='+cbo_year+'&type='+type+'&from_date='+from_date+'&to_date='+to_date;
		var title="Search Job/Style Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=390px,center=1,resize=0,scrolling=0','../')
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
		  http.open("POST","requires/style_wise_order_entry_follow_up_report_controller.php",true);
		  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		  http.send(data); 
		  http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#buyer_td').html(response);
				  
				//   set_multiselect('cbo_buyer_name','0','0','','0');
	          }			 
	      };
	    }         
	}	


	function report_generate(company,job_no,buyer_name,style_ref_no,costing_date,po_id,costing_per,type,entry_from)
	{
		
		var zero_val='';var rate_amt=2; 
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
			+"'&cbo_company_name='"+company
			+"'&cbo_buyer_name="+buyer_name
			+"&txt_style_ref='"+style_ref_no
			+"'&txt_costing_date='"+costing_date
			+"'&txt_po_breack_down_id='"+po_id
			+"'&cbo_costing_per="+costing_per
			+"&cbo_template_id=1"
			
		;
		if(type=='bom_pcs_woven4'){
				http.open("POST","../../order/woven_gmts/requires/pre_cost_entry_report_controller_v2.php",true);				
			}
			else{
				http.open("POST","../../order/woven_gmts/requires/pre_cost_entry_controller_v2.php",true);
			}
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_report_generate_reponse= function(){
			if(http.readyState == 4) 
		    {
				var w = window.open("Surprise", "_blank");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
				d.close();
				release_freezing();
		   }
		};
	}
	
	function booking_report_generate(company,booking_no,fabric_natu,fabric_source,approved_id,po_id,type,entry_from)
	{
		

		
//alert(entry_from);
		if(entry_from==271){
			
			var report_title='Woven Partial Fabric Booking';
			var data="action="+type
			+"&txt_booking_no='"+booking_no
			+"'&cbo_company_name="+company
			+"'&cbo_fabric_natu="+fabric_natu
			+"'&cbo_fabric_source="+fabric_source
			+"'&id_approved_id="+approved_id
			+"'&txt_order_no_id="+po_id
			+"'&report_title="+report_title
			+"'&mail_data=0"
			+"'&path=../../";
			//alert(data);
			http.open("POST","../../order/woven_gmts/requires/woven_partial_fabric_booking_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_report_generate_reponse= function(){
					if(http.readyState == 4) 
					{
						var w = window.open("Surprise", "_blank");
						var d = w.document.open();
						d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
						d.close();
						release_freezing();
				}
			};
		}
		else if(entry_from==272){
			
			var report_title='Multiple Job Wise Trims Booking V2';
			var data="action="+type
			+"&txt_booking_no='"+booking_no
			+"'&cbo_company_name="+company
			+"'&cbo_buyer_name="+fabric_natu
			+"'&cbo_level="+fabric_source			
			+"'&id_approved_id="+approved_id
			+"'&report_title="+report_title
			+"'&is_mail_send="
			+"'&report_type=1"
			+"'&show_comment=0"
			+"'&mail_id=0";
			http.open("POST","../../order/woven_gmts/requires/trims_booking_multi_job_controllerurmi.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_report_generate_reponse= function(){
					if(http.readyState == 4) 
					{
						var w = window.open("Surprise", "_blank");
						var d = w.document.open();
						d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
						d.close();
						release_freezing();
				}
			};
		}else if(entry_from==24){
			var report_title='Trims Receive Entry Multi Ref V3';		
			print_report(company+'*'+po_id+'*'+report_title, type, "../../inventory/trims_store/requires/trims_receive_multi_ref_entry_v3_controller" ) 
			 return;
		}else if(entry_from==17){

			var report_title='Woven Finish Fabric Receive';		
			if(type=="gwoven_finish_fabric_receive_print"){
				print_report( company+'*'+po_id+'*'+report_title, "gwoven_finish_fabric_receive_print", "../../inventory/finish_fabric/requires/woven_finish_fabric_receive_controller" ) 
			}else{
				print_report( company+'*'+po_id+'*'+booking_no+'*'+report_title, "gwoven_finish_fabric_receive_print_3", "../../inventory/finish_fabric/requires/woven_finish_fabric_receive_controller" ) 
			}
			
		}else if(entry_from==167){
			
			var report_title='Pro Forma Invoice V2';		
			if(type==86){			
				print_report( company+'*'+booking_no+'*'+167+'*'+4, "print", "../../commercial/import_details/requires/pi_print_urmi" );
			}else if(type==116){	
				print_report( company+'*'+booking_no+'*'+3, "print_wf", "../../commercial/import_details/requires/pi_print_urmi");
			}else if(type==85){	
				print_report( company+'*'+booking_no+'*'+12, "print_sf", "../../commercial/import_details/requires/pi_print_urmi");
			}else if(type==751){	
				print_report( company+'*'+booking_no+'*'+4, "print_pi", "../../commercial/import_details/requires/pi_print_urmi");
			}else if(type==89){	
				print_report( company+'*'+booking_no+'*'+4, "print_f", "../../commercial/import_details/requires/pi_print_urmi");
			}
			
		}

		
		
	}
</script>

</head>

<body onLoad="set_hotkey();getBuyerId()">
    <form id="materialsFollowup_report">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",''); ?>
    <h3 align="left" id="accordion_h1" style="width:1260px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
            <fieldset style="width:1260px;">
                <table class="rpt_table" width="1260" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
                    <thead>
                        <tr>                    
                            <th width="160" class="must_entry_caption">Company Name</th>
							<th  width="100">Team Name</th>
                            <th width="130">Buyer Name</th>
                            <th width="50">Year</th>
                            <th width="90">Job No</th>
                            <th width="120">Style Ref.</th>
							<th width="100">Date Type</th>
                            <th width="160"> Date</th>
                            <th width="160"><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td id="lccompany_td"><? echo create_drop_down( "cbo_company_name", 180, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" ); ?></td>
						
                          <td id="leader_td"><?=create_drop_down( "cbo_team_leader", 140, "select id,team_name from lib_marketing_team where project_type=2 and status_active =1 and is_deleted=0 order by team_name","id,team_name", 1, "Select Team", $selected, ""); ?></td>
                            <td id="buyer_td"> <? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
                            <td><? 
							$selected_year=date("Y");   
							echo create_drop_down( "cbo_year", 90, create_year_array(),"", 1,"-All Year-", $selected_year, "",0,"" ); ?></td>
                            <td><input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:90px" placeholder="Wr./Browse" onDblClick="openmypage_jobstyle(1)"></td>
                            <td><input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:120px" placeholder="Wr./Browse" onDblClick="openmypage_jobstyle(2)" ></td>
							<td> <? 
							$date_type=array(1=>'Insert Date',2=>'PHD Date',3=>'OPD Date');
							echo create_drop_down( "cbo_date_type", 100, $date_type,"", 1, "--Select Date type --", $selected, "",0,"" ); ?></td>
                            <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" >
                                <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" ></td>
                            <td>
                                <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated('report_generate',1)" />
								<input type="button" id="show_button" class="formbutton" style="width:80px" value="Style" onClick="fn_report_generated('report_generate2',2)" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" align="center"><? echo load_month_buttons(1); ?></td>                         
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
	 setTimeout[($("#lccompany_td a").attr("onclick","disappear_list(cbo_company_name,'0'); getBuyerId();"),3000)]; 
</script>
<?
		$sql=sql_select("select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name");
		$company_id='';
		$is_single_select=0;
		if(count($sql)==1){
			$company_id=$sql[0][csf('id')];
			$is_single_select=1;
			?>
			<script>
			console.log('shariar');
			set_multiselect('cbo_company_name','0','<? echo $is_single_select?>','<? echo $company_id?>','0');
			
			</script>
			
			<?
		}
		
		?>
</html>
