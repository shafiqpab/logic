<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Daily Cutting and Input Inhand Report.
Functionality	:	
JS Functions	:
Created by		:	Ashraful Islam
Creation date 	: 	26-04-2014
Updated by 		:	Kausar
Update date		: 	20-10-2015	   
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
echo load_html_head_contents("Date Wise Production Report", "../../", 1, 1,$unicode,1,'');
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	function open_style_ref()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer=$("#cbo_buyer_name").val();
		var job_no=$("#txt_job_no").val();
		var job_id=$("#hidden_job_id").val();
		var cbo_year = $("#cbo_year").val();
		var page_link='requires/style_wise_production_report_controller.php?action=style_wise_search&company='+company+'&buyer='+buyer+'&job_no='+job_no+'&job_id='+job_id+'&cbo_year='+cbo_year; 
		var title="Search Style Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var styleID=this.contentDoc.getElementById("txt_selected_id").value;
			var styleDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#txt_style_ref").val(styleDescription);
			$("#hidden_style_id").val(styleID); 
		}
	}	
	
	function open_order_no()
	{
		if( form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var job_no=$("#txt_job_no").val();
		var job_id=$("#hidden_job_id").val();
		var company = $("#cbo_company_name").val();	
		var buyer=$("#cbo_buyer_name").val();
		var style_no=$('#txt_style_ref').val();
		var style_id=$('#hidden_style_id').val();
		var cbo_year = $("#cbo_year").val();
		var page_link='requires/style_wise_production_report_controller.php?action=order_wise_search&company='+company+'&buyer='+buyer+'&style_id='+style_id+'&job_id='+job_id+'&job_no='+job_no+'&cbo_year='+cbo_year; 
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=560px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			//alert(prodID); // product ID
			var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#txt_order_no").val(prodDescription);
			$("#hidden_order_id").val(prodID); 
		}
	}
	 
	function open_job_no()
	{
		if( form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer=$("#cbo_buyer_name").val();
		var cbo_year = $("#cbo_year").val();	
		var page_link='requires/style_wise_production_report_controller.php?action=job_wise_search&company='+company+'&buyer='+buyer+'&cbo_year='+cbo_year; 
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=510px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			//alert(prodID); // product ID
			var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#txt_job_no").val(prodDescription);
			$("#hidden_job_id").val(prodID); 
			//alert($("#hidden_job_id").val())
		}
	}

	function generate_report(report_type)
	{
		var company=$("#cbo_company_name").val();
		var work_comp=$("#cbo_working_company_name").val();
 		if(company=="" && work_comp=="")
		{
			if( form_validation('cbo_company_name*txt_date_from','Company Name*Date')==false )
			{
				return;
			}

		}
		
		var report_title=$( "div.form_caption" ).html();
		
		var data="action=generate_report"+get_submitted_data_string
		('cbo_company_name*cbo_working_company_name*cbo_buyer_name*txt_file_no*txt_job_no*txt_style_ref*txt_ref_no*txt_po_no*txt_date_from*cbo_search_by*cbo_year*cbo_ship_status*cbo_location_name*cbo_floor_name',"../../")+'&report_title='+report_title+'&report_type='+report_type;
		freeze_window(3);
		http.open("POST","requires/style_wise_production_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
	       	show_msg('3');
			release_freezing();
			
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML = report_convert_button('../../'); 
			var search_by=$('#cbo_search_by').val();
			
			
			if(reponse[1]==1 || reponse[1]==5)
			{
				if(search_by==1 || search_by==3)
				{
						var tableFilters = 
						{
						   col_operation: {
						   id: ["value_job_total","value_fabric_issue","value_plan_cut","value_cut_today","value_cut_total","value_cut_bal","value_embl_iss","value_embl_iss_total","value_embl_iss_bal","value_embl_rec","value_embl_rec_total","value_embl_rec_bal","value_sew_in","value_sew_in_to","value_sew_in_bal","value_sew_out","value_sew_out_total","value_sew_out_bal","value_iron","value_iron_to","value_iron_bal","value_re_iron","value_re_iron_to","value_reject","value_reject_to","value_finish","value_finish_to","value_finish_bal","value_exfactory","value_exfactory_to","value_exfac_bal"],
						   
						   col: [7,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40],
						   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
						   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
						}	
					}
				}
				else if(search_by==2 || search_by==4)
				{
					 var tableFilters = 
					 {
						   col_operation: {
						   id: ["value_job_total","value_fabric_issue","value_plan_cut","value_cut_today","value_cut_total","value_cut_bal","value_embl_iss","value_embl_iss_total","value_embl_iss_bal","value_embl_rec","value_embl_rec_total","value_embl_rec_bal","value_sew_in","value_sew_in_to","value_sew_in_bal","value_sew_out","value_sew_out_total","value_sew_out_bal","value_iron","value_iron_to","value_iron_bal","value_reject","value_reject_to","value_finish","value_finish_to","value_finish_bal","value_exfactory","value_exfactory_to","value_exfac_bal"],
						   //col: [6,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36],
						   //col: [8,13,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38],
						     col: [8,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40],
						   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
						   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
						}	
					}
				}
			}
			else if(reponse[1]==2)
			{
				if(search_by==4)
					{
					   var tableFilters = 
						{
							col_operation: {
							id: ["value_job_total","value_fabric_issue","value_plan_cut","value_cut_today","value_cut_total","value_cut_bal",
							"value_print_iss","value_print_iss_total","value_print_iss_bal","value_print_rec","value_print_rec_total","value_print_rec_bal",
							"value_embl_iss","value_embl_iss_total","value_embl_iss_bal","value_embl_rec","value_embl_rec_total","value_embl_rec_bal",
							
							"value_sp_iss","value_sp_iss_total","value_sp_iss_bal","value_sp_rec","value_sp_rec_total","value_sp_rec_bal",
							
							"value_sew_in","value_sew_in_to","value_sew_in_bal","value_sew_out","value_sew_out_total","value_sew_out_bal",
							
							"value_wash_in","value_wash_in_to","value_wash_in_bal","value_wash_out","value_wash_out_total","value_wash_out_bal",
							
							"value_iron","value_iron_to","value_iron_bal","value_finish","value_finish_to","value_finish_bal","value_exfactory","value_exfactory_to","value_exfac_bal"],
							col: [7,10,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55],
							
							operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
							write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
							}	
						}
					}
			}
			else if(reponse[1]==7){
				var avgVAl = $("#avgProdEffeciency").val(); //
				$(".prodEffecincy").text(avgVAl);

				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[2]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			}
			setFilterGrid("table_body",-1,tableFilters);
		} 
	}

	function new_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	}

	function reset_form()
	{
		$("#hidden_style_id").val("");
		$("#hidden_order_id").val("");
		$("#hidden_job_id").val("");
	}
	
	function mypopup(data,action,width,height,title)
	{
		var popup_width=width;
		var popup_height=height;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_production_report_controller.php?data='+data+'&action='+action, title, 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');
	}	
	
	
	
	
	
	function openmypage(company_id,jobnumber_prefix,insert_date,action,width)
	{
		var popup_width=width;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_production_report_controller.php?company_id='+company_id+'&action='+action+'&insert_date='+insert_date+'&jobnumber_prefix='+jobnumber_prefix, 'Detail Veiw', 'width='+popup_width+', height=400px,center=1,resize=0,scrolling=0','../');
	}
	
	function openmypage_order(company_id,order_id,order_number,insert_date,type,action,width,height)
	{
		var popup_width=width;
		var popup_height=height;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_production_report_controller.php?company_id='+company_id+'&action='+action+'&insert_date='+insert_date+'&order_id='+order_id+'&order_number='+order_number+'&type='+type, 'Detail Veiw', 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');
	}	
	
	
	
	
	function openmypage(company_id,jobnumber_prefix,insert_date,action,width)
	{
		var popup_width=width;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_production_report_controller.php?company_id='+company_id+'&action='+action+'&insert_date='+insert_date+'&jobnumber_prefix='+jobnumber_prefix, 'Detail Veiw', 'width='+popup_width+', height=400px,center=1,resize=0,scrolling=0','../');
	}
	
	function openmypage_embl(company_id,order_id,order_number,insert_date,type,action,width,height,embl_type)
	{
		var popup_width=width;
		var popup_height=height;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_production_report_controller.php?company_id='+company_id+'&action='+action+'&insert_date='+insert_date+'&order_id='+order_id+'&order_number='+order_number+'&type='+type+'&embl_type='+embl_type, 'Detail Veiw', 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');
	}
	
	function disable_enable(val)
	{
		$("#txt_job_no").val("");
		$("#txt_style_ref").val("");
		$("#txt_order_no").val("");
		if(val==1 || val==2)
		{
			$('#txt_job_no').attr('disabled','disabled');
			$('#txt_style_ref').attr('disabled','disabled');
			$('#txt_order_no').attr('disabled','disabled');
		}
		else
		{
			$('#txt_job_no').removeAttr('disabled','disabled');
			$('#txt_style_ref').removeAttr('disabled','disabled');
			$('#txt_order_no').removeAttr('disabled','disabled');
		}
	}
	
	function job_no_popup(type)
	{
		if( form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer=$("#cbo_buyer_name").val();
		var cbo_year = $("#cbo_year").val();
		var txt_job_no = $("#txt_job_no").val();	
		var page_link='requires/style_wise_production_report_controller.php?action=job_wise_search&company='+company+'&buyer='+buyer+'&cbo_year='+cbo_year+'&type='+type+'&txt_job_no='+txt_job_no; 
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=780px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			//alert(type);
			
			if(type==1)
			{
				$('#txt_job_no').val(job_no);
				$('#txt_job_id').val(job_id);
			}
			else if(type==2)
			{
				$('#txt_style_ref').val(job_no);
				$('#txt_style_hidden').val(job_id);
			}
			else if(type==3)
			{
				$('#txt_po_no').val(job_no);
				$('#txt_po_no_hidden').val(job_id);
			}
			/*else if(type==4)
			{
				$('#txt_ref_no').val(job_no);
				$('#txt_job_id').val(job_id);
			}*/
			
		}
	}
function getCompanyId() 
{
    var company_id = document.getElementById('cbo_company_name').value;
    //var search_type = document.getElementById('cbo_search_by').value;
    if(company_id !='') 
    {
      	var data="action=load_drop_down_buyer&choosenCompany="+company_id;
      	http.open("POST","requires/style_wise_production_report_controller.php",true);
      	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
      	http.send(data); 
      	http.onreadystatechange = function()
      	{
          	if(http.readyState == 4) 
          	{
              	var response = trim(http.responseText);
              	$('#buyer_td').html(response);  
    			showReportButton();   
          	}			 
      	};
    }    
}

function getButtonSetting()
	{
		 var company_id = document.getElementById('cbo_company_name').value;
		get_php_form_data(company_id,'print_button_variable_setting','requires/style_wise_production_report_controller' );
	}

function showReportButton()
{
	var company_id = document.getElementById('cbo_company_name').value;
    if(company_id !='') {
      	var data="action=load_report_button&choosenCompany="+company_id;
      	http.open("POST","requires/style_wise_production_report_controller.php",true);
      	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
      	http.send(data); 
      	http.onreadystatechange = function(){
          	if(http.readyState == 4) 
          	{
              	var report_id = trim(http.responseText).split(',');
              	// alert(report_id[1]);
              	if(report_id!="")
              	{              		
              		$('.btn1').hide();
              		$('.btn2').hide();
              		$('.btn3').hide();
              		$('.btn4').hide();
              		$('.btn5').hide();
					$('.btn6').hide();

              		$('.btn_'+report_id[0]).show();
              		$('.btn_'+report_id[1]).show();
              		$('.btn_'+report_id[2]).show();
              		$('.btn_'+report_id[3]).show();
              		$('.btn_'+report_id[4]).show();
              		$('.btn_'+report_id[5]).show();
              		
              	}
		            
          	}			 
      	};
    }
}

function print_report_button_setting(report_ids) 
    {
        //alert(report_ids);
        $('#show_button').hide();
        $('#show_wip').hide();
		$('#show_wip2').hide();
        $('#show_snf').hide();
        $('#show_snf1').hide();
        $('#show_button1').hide();
        var report_id=report_ids.split(",");
        report_id.forEach(function(items){
            if(items==108){$('#show_button').show();}
            else if(items==262){$('#show_wip').show();}
			else if(items==501){$('#show_wip2').show();}
            else if(items==195){$('#show_snf').show();}
            else if(items==263){$('#show_snf1').show();}
            else if(items==264){$('#show_button1').show();}
            });
    }
    

</script>
</head>
    <body onLoad="set_hotkey();">
    <div style="width:100%;" align="center"> 
    <? echo load_freeze_divs ("../../",'');  ?>
    <h3 style="width:1650px;  margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel','')"> -Search Panel</h3>
    <div style="width:100%;" align="center" id="content_search_panel">
        <form id="dateWiseProductionReport_1"  autocomplete="off">    
            <fieldset style="width:1700px;">
                <table class="rpt_table" width="1680px" cellpadding="0" cellspacing="0" align="center" rules="all">
                    <thead>                    
                        <tr>
                            <th class="must_entry_caption" width="150">Company Name</th>
                            <th class="" width="150">Working Company</th>
                            <th width="125">Location Name</th>
                            <th width="125">Floor Name</th>
                            <th width="100">Buyer Name</th>
                            <th width="120">Type</th>
                            <th width="60">Year</th>
                            <th width="100">Shipment Status</th>
                            <th width="90">File No</th>
                            <th width="90">Int Ref No</th>
                            <th width="80">Job No</th>
                            <th width="80">Style Reference</th>
                            <th width="80">Order No </th>
                            <th id="search_text_td" class="must_entry_caption" width="70"> Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:60px" value="Reset" onClick="reset_form()"/></th>
                        </tr>   
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td id="company_td"> 
								<?
                                	echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 0, "-- Select Company --", $selected, "" );
                                ?>
                            </td>

                             <td id="working_company_td"> 
								<?
                                	echo create_drop_down( "cbo_working_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 0, "-- Select Company --", $selected, "" );
                                ?>
                            </td>


                            <td align="center" id="location_td"> 
                        <?
                            echo create_drop_down( "cbo_location_name", 125, $blank_array,"", 0, "-- Select location --", $selected, "" );
                        ?>
                    </td>

                    <td align="center" id="floor_td"> 
                        <?
                            echo create_drop_down( "cbo_floor_name", 125, "SELECT id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 group by id,floor_name order by floor_name","id,floor_name", 0, "-- Select Floor --", $selected, "" );
                        ?>
                    </td>
                            <td id="buyer_td">
                            	<? echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" ); ?>
                            </td>
                            <td>
								<? 
									//$search_by_arr = array(1=>"Style Wise(All)",2=>"Order Wise(All)",3=>"Style Wise",4=>"Order Wise");
									$search_by_arr = array(3=>"Style Wise",4=>"Order Wise");
									echo create_drop_down( "cbo_search_by", 120, $search_by_arr,"",0, "", 4,'disable_enable(this.value);',0 );//search_by(this.value)
                                ?>
                            </td>
                            <td>
                            	<? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-All-","", "",0,"" ); ?>
                            </td>
                            <td>
                            	<?
								$ship_status_arr = array(1=>"Full Pending",2=>"Partial Shipment",3=>"Full Shipment/Closed"); 
								echo create_drop_down( "cbo_ship_status", 100, $ship_status_arr,"", 1,"-All-","", "",0,"" ); ?>
                            </td>
                            <td>
                            	<input type="text" id="txt_file_no" name="txt_file_no"  style="width:80px" class="text_boxes"  placeholder="Write" />
                            </td>
                            <td>
                            	<input type="text" id="txt_ref_no" name="txt_ref_no" style="width:80px" class="text_boxes"  placeholder="Write" />
                            </td>
                            <td>
                            	<input type="text"  name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px;" tabindex="1" placeholder="Write/Browse" onDblClick="job_no_popup(1);"> 
                            </td>
                            <td>
                                <input type="text"  name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:70px;" tabindex="1" placeholder="Write/Browse" onDblClick="job_no_popup(2);"> 
                              
                            </td>
                            <td>
                            	 <input type="text"  name="txt_po_no" id="txt_po_no" class="text_boxes" style="width:70px;" tabindex="1" placeholder="Write/Browse" onDblClick="job_no_popup(3);"> 
                            </td>
                            
                            <td>
                            	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="Date" >
                            </td>
                            <td>
                                <input type="button" id="show_button" class="formbutton btn1 btn_178" style="width:50px;display: none;" value="Show" onClick="generate_report(1)" />
                                <input type="button" id="show_wip" class="formbutton btn2 btn2" style="width:50px; display:none;" value="WIP" onClick="generate_report(2)" />
                                <input type="button" id="show_snf" class="formbutton btn3 btn_195" style="width:50px;display: none;" value="Show 2" onClick="generate_report(3)" />
                                <input type="button" id="show_snf1" class="formbutton btn4 btn_122" style="width:50px;display: none;" value="Report 3" onClick="generate_report(4)" />
                                 <input type="button" id="show_button1" class="formbutton btn5 btn_123" style="width:50px;display: none;" value="Report 4" onClick="generate_report(5)" />
								 <input type="button" id="show_wip2" class="formbutton btn6 btn_124" style="width:50px;display: none;" value="WIP2" onClick="generate_report(6)" />
								 <input type="button" id="report5" class="formbutton" style="width:50px;" value="Report 5" onClick="generate_report(7)" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </form> 
    </div>
    </div> 
        <div id="report_container" align="center"></div>
        <div id="report_container2"></div>  
    </body>
    <script>    	
    	set_multiselect('cbo_company_name','0','0','','0');
    	setTimeout[($("#company_td a").attr("onclick","disappear_list(cbo_company_name,'0');getCompanyId();getButtonSetting();") ,3000)];
    	
    	set_multiselect('cbo_working_company_name','0','0','','0'); 
    	set_multiselect('cbo_location_name','0','0','0','0');     	
    	set_multiselect('cbo_floor_name','0','0','','0'); 
    	

    	$("#multiselect_dropdown_table_headercbo_working_company_name a").click(function(){
    		load_location();
    	});
    	 
    	 

    	 


    	function load_location()
    	{
    		var company=$("#cbo_working_company_name").val();
    		load_drop_down( 'requires/style_wise_production_report_controller',company, 'load_drop_down_location', 'location_td' );
    		set_multiselect('cbo_location_name','0','0','0','0'); 
    		//var loc=$("#cbo_working_company_name").val();
     		// load_drop_down( 'requires/style_wise_production_report_controller',company, 'load_drop_down_floor', 'floor_td' );
    		// set_multiselect('cbo_floor_name','0','0','','0'); 

    		 
    	}

    	function load_floor()
    	{
			var company=$("#cbo_working_company_name").val();
     		var loc=$("#cbo_location_name").val();
     		load_drop_down( 'requires/style_wise_production_report_controller',loc, 'load_drop_down_floor', 'floor_td' );
    		set_multiselect('cbo_floor_name','0','0','','0'); 
    	}
    	$('#cbo_buyer_name').val(0);
    </script>
 
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
