<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Finishing Cost Report
				
Functionality	:	
JS Functions	:
Created by		:	Aziz
Creation date 	: 	12-02-2019
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
echo load_html_head_contents("Closing Stock Report","../../../", 1, 1, $unicode,1,1); 


?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	function openmypage_batch_popup(type)
	{
		var data=document.getElementById('cbo_company_name').value+"_"+$("#cbo_batch_type").val()+"_"+type;
		$("#txt_batch_no").val('');
		$("#txt_batch_id").val('');
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/batch_dying_fnishing_cost_sales_controller.php?action=batch_popup&data='+data,'Batch Popup', 'width=950px,height=400px,center=1,resize=0','../../')
		emailwindow.onclose=function()
		{
			
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			var prodDescription=this.contentDoc.getElementById("txt_selected").value;
			if(type==2)
			{
				$("#txt_booking_no").val(prodDescription);
				//$("#txt_batch_id").val(prodID); 
			}
			else
			{
				$("#txt_batch_no").val(prodDescription);
				$("#txt_batch_id").val(prodID); 	
			}
		    
		}
	}
	
	var tableFilters1 = 
		{
			col_0: "none",
			col_operation: { 
				id: ["value_total_batch_weight_single","value_total_chemical_cost_single","value_total_dyeing_cost_single","value_total_chemical_price_single","value_total_redying_chemic_oost_single","value_total_redying_dying_cost_single","value_grand_total_cost_single"],
				col: [13,14,15,16,18,19,20],
				operation: ["sum","sum","sum","sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}
		}
	
		var tableFilters2 = 
		{
			col_0: "none",
			col_operation: { 
				id: ["value_total_batch_weight_multiple","value_total_chemical_cost_multiple","value_total_dyeing_cost_multiple","value_total_chemical_price_multiple","value_total_chemical_price_multiple_finish","value_grand_total_cost_multiple"],
				col: [13,14,15,16,18,20],
				operation: ["sum","sum","sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}
		}
	
	
	function generate_report(operation)
	{
		if($("#txt_batch_no").val()!='' || $("#txt_job").val()!='' || $("#txt_po_no").val()!='')
		{
			if( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
		}
		else
		{
			if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Range*To Range')==false )
			{
				return;
			}
		}
		var report_title=$( "div.form_caption" ).html(); 
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_value_with = $("#cbo_value_with").val();
		var from_date = $("#txt_date_from").val();
		var to_date = $("#txt_date_to").val();
		var batch_id = $("#txt_batch_id").val();
		var batch_no = $("#txt_batch_no").val();
		var batch_type =$("#cbo_batch_type").val();
		
		var txt_booking_no =$("#txt_booking_no").val();	
		var txt_po_no =$("#txt_po_no").val();
		var txt_job =$("#txt_job").val();
		
		var dataString = "&cbo_company_name="+cbo_company_name+"&from_date="+from_date+"&to_date="+to_date+"&cbo_value_with="+cbo_value_with
		+"&batch_id="+batch_id+"&batch_no="+batch_no+"&batch_type="+batch_type+"&txt_booking_no="+txt_booking_no+"&txt_po_no="+txt_po_no+"&txt_job="+txt_job+"&report_title="+report_title;
		
		//var data="action=report_generate"+get_submitted_data_string('cbo_company_name*txt_date_from*txt_date_to*cbo_year*cbo_report_type*cbo_search_by*txt_search_comm*cbo_presentation',"../../../")+'&report_title='+report_title;
		
		var data="action=generate_report"+dataString;
		//alert (data);
		freeze_window(operation);
		http.open("POST","requires/batch_dying_fnishing_cost_sales_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);  
			
			document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
			/*document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';*/
			setFilterGrid("table_body_id",-1,tableFilters1);
			setFilterGrid("table_body_multibatch_id",-1,tableFilters2);
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
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
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
  function clear_box()
  {
	$("#txt_batch_id").val(''); 
  }
  
  function subprocess_fabric_dtls(batch_id,batch_no,action)
  {
	 var batch_type=$('#cbo_batch_type').val();
	 var width=950;
	 emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/batch_dying_fnishing_cost_sales_controller.php?action='+action+'&batch_id='+batch_id+'&batch_no='+batch_no+'&batch_type='+batch_type+'&action='+action, 'Subprocess Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	 
  }
  
  function fn_1st_batch(batch_id,action)
  {
	 var batch_type=$('#cbo_batch_type').val();
	 var width=800;
	 emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/batch_dying_fnishing_cost_sales_controller.php?action='+action+'&batch_id='+batch_id+'&batch_type='+batch_type+'&action='+action, 'Batch Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	 
  }
  
  function fn_total_batch(batch_id,action)
  {
	 var batch_type=$('#cbo_batch_type').val();
	 var width=920;
	 emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/batch_dying_fnishing_cost_sales_controller.php?action='+action+'&batch_id='+batch_id+'&batch_type='+batch_type+'&action='+action, 'Batch Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	 
  }
  
   function openmypage_sales_order() {
        if (form_validation('cbo_company_name', 'Company Name') == false) {
            return;
        }

        var companyID = $("#cbo_company_name").val();
        var yearID = $("#cbo_year_selection").val();
        var batch_type = $("#cbo_batch_type").val();
        var cbo_value_with = $("#cbo_value_with").val();
        var page_link = 'requires/batch_dying_fnishing_cost_sales_controller.php?action=sales_order_no_search_popup&companyID=' + companyID + '&yearID=' + yearID + '&batch_type=' + batch_type + '&cbo_value_with=' + cbo_value_with;
        var title = 'Sales Order No Search';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=390px,center=1,resize=1,scrolling=0', '../../');
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            var sales_job_no = this.contentDoc.getElementById("hidden_job_no").value;

            $('#txt_po_no').val(sales_job_no);
        }
    }
  
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?><br />    		 
        <form name="closingstock_1" id="closingstock_1" autocomplete="off" > 
         <h3 style="width:1150px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:1150px" >      
            <fieldset>  
                <table class="rpt_table" width="1150" cellpadding="0" cellspacing="0">
                    <thead>
                        <th class="must_entry_caption">Company</th>
                       <th>Batch Type</th>
                       <th>Job No</th> 
                       <th>Sales Order No</th> 
                       
                        <th>Batch No</th>
                        <th>Booking No</th>
                        <th>Based On</th>
                        <th class="must_entry_caption"> Date Range</th>
                        <th width="70"><input type="reset" name="res" id="res" value="Reset" style="width:50px" class="formbutton" onClick="reset_form('closingstock_1','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tbody>
                        <tr>
                           <td>
							<? 
                            echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "" );//load_drop_down( 'requires/batch_dying_fnishing_cost_sales_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );
                            ?>                            
                            </td>
                            <td align="center">	
							<?
                                echo create_drop_down( "cbo_batch_type", 100, $order_source,"",1, "--Select--", 1,0,0,'1,2,3' );
                            ?>
                            </td>
                              <td>
                           		 <input type="text" id="txt_job" name="txt_job" class="text_boxes" style="width:80px"  placeholder="Write" />
                       		 </td>
                         	 <td>
                           		 <input type="text" id="txt_po_no" name="txt_po_no" class="text_boxes" style="width:100px"  onDblClick="openmypage_sales_order();"  placeholder="Write/Browse" />
                       		 </td>
                             
                            <td>
                            	<input style="width:80px;" name="txt_batch_no" id="txt_batch_no" class="text_boxes" onDblClick="openmypage_batch_popup(1)" placeholder="Browse/Write"  onKeyUp="clear_box()" />
                                <input type="hidden" name="txt_batch_id" id="txt_batch_id" style="width:90px;"/>
                            </td>
                             <td>
                            	<input style="width:80px;" name="txt_booking_no" id="txt_booking_no" class="text_boxes" onDblClick="openmypage_batch_popup(2)" placeholder="Browse/Write"  onKeyUp="clear_box()" />
                                <input type="hidden" name="txt_batch_id" id="txt_batch_id" style="width:90px;"/>
                            </td>
                            
                             <td> 
                           <?   
                                $valueWithArr=array(2=>'Batch Date',1=>'Dyeing Date');
                                echo create_drop_down( "cbo_value_with", 80, $valueWithArr, "",0, "-- Select --",1, 0, 0);
                            ?>
                          </td>
                            <td align="center">
                                <input type="text" name="txt_date_from" id="txt_date_from" value="<? // echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:60px;" placeholder="From Date"/>                    							
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" value="<? //echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:60px;" placeholder="To Date"/>                        
                            </td>
                            <td>
                                <input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:50px" class="formbutton" />
                              
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7" align="center"><? echo load_month_buttons(1);  ?></td>
                            
                        </tr>
                    </tfoot>
                </table> 
            </fieldset> 
            </div>
            
                
        </form>    
    </div>
    <br /> 
    <div id="report_container" align="center"></div>
   <div id="report_container2"></div> 
</body>  
<script>
	$("#cbo_value_with").val(0);
</script> 

<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
