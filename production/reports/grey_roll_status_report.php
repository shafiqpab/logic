<?
/*-------------------------------------------- Comments

Purpose			: 	This form will Create Roll Position Tracking Report
					
Functionality	:	
				

JS Functions	:

Created by		:	Tipu
Creation date 	: 	15-11-2018
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
echo load_html_head_contents("Roll Position Tracking Report", "../../", 1, 1,'','',1);

?>

 <script src="../../Chart.js-master/Chart.js"></script>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	var tableFilters = 
	 {
		col_60: "none",
		col_operation: {
		id: ["value_total_grey_qnty"],
	   	col: [13],
	   	operation: ["sum"],
	   	write_method: ["innerHTML"]
		}
	 }
	 
	function fn_report_generated(type)
	{
		
		$("#hidden_btn_type").val(type);
		var cbo_knitting_company=$('#cbo_knitting_company').val();
		var txt_job_no=$('#txt_job_no').val();
		var txt_order_no=$('#txt_order_no').val();
		var txt_date_from=$('#txt_date_from').val();
		var txt_barcode_no=$('#txt_barcode_no').val();
		var txt_style_ref_no=$('#txt_style_ref_no').val();
		var txt_date_to=$('#txt_date_to').val();
		if(cbo_knitting_company==0 && txt_job_no=="" && txt_order_no=="" && txt_barcode_no=="" && txt_style_ref_no=="" && txt_date_from=="" && txt_date_to=="")
		{
			if(form_validation('cbo_company_name*cbo_buyer_name','Company*Buyer')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_name','Company')==false)
			{
				return;
			}
		}
		if(type==1 || type==2 || type==3)
		{
            if(cbo_knitting_company==0 && txt_job_no=="" && txt_order_no=="" && txt_barcode_no=="" && txt_style_ref_no=="" && txt_date_from=="" && txt_date_to!="")
            {
                alert("Please Select Report2");
                return;
            }
	  		var report_title=$( "div.form_caption" ).html();
	  		var data="action=report_generate&type="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_knitting_company*txt_job_no*txt_style_ref_no*txt_order_no*txt_barcode_no*txt_date_from*txt_date_to*cbo_date_drop_down*cbo_year',"../../")+'&report_title='+report_title;
		}
		//alert(data);
		freeze_window(5);
		http.open("POST","requires/grey_roll_status_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			 var report_type=response[4];
			// alert(report_type);
			 if(report_type==3)
			 {
				release_freezing();
				if(response!='')
				{
				$('#excel_generate').removeAttr('href').attr('href','requires/'+response[1]);
				//$('#aa1')[0].click();
				 document.getElementById('excel_generate').click();
				}
			}
			else
			{
			
				$("#report_container2").html(response[0]);  
				document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
					release_freezing();
				setFilterGrid("table_body",-1,tableFilters);
				var level= new Array();
				var leveld= new Array();
				var obj=JSON.parse(response[2]);
				var objd=JSON.parse(response[3]);
				for(i in obj){
					level.push(obj[i])
					leveld.push(objd[i])
				} 
			}
		
			
	 		show_msg('3');
		}
	}
		
	function new_window()
	{
		var hidden_btn_type=$("#hidden_btn_type").val();
		if(hidden_btn_type==1)
		{	
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$('#table_body tr:first').hide();  
		}
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		if(hidden_btn_type==1)
		{

			$('#table_body tr:first').show();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="330px";
		}
	}
	
	function openmypage(po_id,color_id)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/grey_roll_status_report_controller.php?po_id='+po_id+'&color_id='+color_id+'&action=color_popup', 'Detail Veiw', 'width=860px, height=370px,center=1,resize=0,scrolling=0','../');
	}
	
	
	function openmypage_popup(roll_id,action)
	{
		emailwindow=dhtmlmodal.open('EmailBox','iframe','requires/grey_roll_status_report_controller.php?roll_id='+roll_id+'&action='+action, 'Roll Popup', 'width=1100px,height=250px,center=1,resize=0,scrolling=0','../');
		emailwindow.onclose=function()
		{
			
		}
		
	}
	
	function openmypage_sys_no(entry_form,barcode)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/grey_roll_status_report_controller.php?entry_form='+entry_form+'&barcode_no='+barcode+'&action=system_no_popup', 'System Info', 'width=260px, height=170px,center=1,resize=0,scrolling=0','../');
	}
</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",''); ?>
		 <form name="knitDyeingLoadReport_1" id="knitDyeingLoadReport_1">
		 <input type="hidden" id="hidden_btn_type" name="hidden_btn_type" value="0"> 
         <h3 style="width:1340px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
             <fieldset style="width:1340px;">
                 <table class="rpt_table" width="1340" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th class="must_entry_caption" width="120">Company Name</th>
                            <th  class="must_entry_caption" width="120">Buyer Name</th>
                            <th width="60">Job Year</th>
                            <th  width="60">Job No</th>
                            <th  width="80">Style Ref.</th>
                            <th  width="80">Order No</th>
                            <th  width="80">Source</th>
                            <th  width="152">Party Name</th>
                            <th  width="110">Barcode No</th>
                            <th  width="110">Date</th>
                            <th colspan="2" width="60">Date Range</th> 
                            <th colspan="2"><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('knitDyeingLoadReport_1','report_container*report_container2','','','')" class="formbutton" style="width:60px" /></th>
                            <th></th>
                        </thead>
                        <tbody>
                            <tr class="general" align="center">
                               <td> 
									<?
                                        echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/grey_roll_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                                    ?>
                                </td>
                                <td id="buyer_td" align="center">
                                    <? 
                                        echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0,"" );
                                    ?>
                                </td>
                                <td>
								<? 
                                    $selected_year=date("Y");
                                    echo create_drop_down( "cbo_year", 60, $year,"", 1, "-All-", $selected_year, "",0 );
                                ?>
                                </td>
                                <td align="center">
                                     <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" placeholder="Write" style="width:60px" />
                                </td>
                                 
                                 <td align="center">
                                     <input type="text" name="txt_style_ref_no" id="txt_style_ref_no" class="text_boxes" placeholder="Write" style="width:80px" />
                                </td>
                                <td align="center">
                                     <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" placeholder="Write" style="width:80px" />
                                </td>
                                
								<td>
									<?
									echo create_drop_down("cbo_knitting_source",90,$knitting_source,"", 1, "-- Select --", 0,"load_drop_down( 'requires/grey_roll_status_report_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_knitting_com','knitting_com');",0,'1,3');
									?>
								</td>
                                <td id="knitting_com">
									<?
									echo create_drop_down( "cbo_knitting_company", 152, $blank_array,"",1, "--Select Knit Company--", 1);
									?>
								</td>
                                <td align="center">
                                     <input type="text" name="txt_barcode_no" id="txt_barcode_no" class="text_boxes" placeholder="Write" style="width:90px" />
                                </td>
                                <td align="center">
                                     <?
                                     $cbo_date_arr = array('1' => 'Knitting Production date');
									echo create_drop_down( "cbo_date_drop_down", 100, $cbo_date_arr,"",1, "--Select Date--", 1);
									?>
                                </td>
                                <td align="center" colspan="2">
									<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" readonly>To
									<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" readonly> 
								</td>
                                <td><input type="button" id="show_button" class="formbutton" style="width:60px" value="Show" onClick="fn_report_generated(1)" /></td>
                                <td><input type="button" id="show_button1" class="formbutton" style="width:60px" value="Summary" onClick="fn_report_generated(2)" /></td>
                            </tr>
                            <tr>
							<td colspan="10" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                             <td> &nbsp;<input type="button" id="show_button" class="formbutton" style="width:110px" value="Excel Download" onClick="fn_report_generated(3)" />
                             <a   id="excel_generate" href="" style="text-decoration:none" download hidden>BB</a>
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
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>