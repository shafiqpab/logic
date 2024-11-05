<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Gmts Shipment Schedule Report po and style wise
				
Functionality	:	
JS Functions	:
Created by		:	Md. Shafiqul Islam Shafiq
Creation date 	: 	25/10/2018
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
echo load_html_head_contents("Weekly Capacity and Order Booking Status", "../../../", 1, 1, $unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	var permission = '<? echo $permission; ?>';

	var tableFilters = 
	{
		/*col_operation: 
		{
			id: ["value_smv_tot","total_order_qnty","total_order_qnty_pcs","value_yarn_req_tot","value_total_order_value","value_total_commission","value_total_net_order_value","total_ex_factory_qnty","total_short_access_qnty","total_over_access_qnty","value_total_short_access_value","value_total_over_access_value"],
			col: [14,15,17,18,20,21,22,23,24,25,26,27],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		} , 
		col_28: "select",
		col_32: "select",*/
	}
		
	var tableFiltersCountry = 
	{
		col_operation: 
		{
			id: ["total_order_qnty","total_ord_qnty_pcs","value_total_order_value","total_ex_factory_qnty","total_ex_factory_qnty_bal","value_total_ex_factory_value"],
			col: [12,14,15,16,17,18],
			operation: ["sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		} , 
		col_28: "select",
		col_32: "select",
	}
			
	function generate_report(type)
	{
		
		document.getElementById('report_container2').innerHTML="";
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var cbo_working_company_name=document.getElementById('cbo_working_company_name').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var cbo_location_name=document.getElementById('cbo_location_name').value;
		var txt_style_ref=document.getElementById('txt_style_ref').value;
		var txt_job_no=document.getElementById('txt_job_no').value;
		var txt_po_no=document.getElementById('txt_po_no').value;	
		var cbo_country_name=document.getElementById('cbo_country_name').value;	
		var txt_date_from=document.getElementById('txt_date_from').value;
		var txt_date_to=document.getElementById('txt_date_to').value;
		var cbo_year=document.getElementById('cbo_year').value;
		var cbo_ship_status=document.getElementById('cbo_ship_status').value;
		var cbo_order_status=document.getElementById('cbo_order_status').value;
		var txt_booking_no=document.getElementById('txt_booking_no').value;
	/*	if(txt_booking_no!='' || txt_job_no!='' || txt_style_ref!='' || txt_po_no!='')
		{
			if( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
		}
		else
		{
			if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false )
			{
				return;
			}
		}*/
		if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false )
			{
				return;
			}
		
		
		var data=cbo_company_name+"_"+cbo_working_company_name+"_"+cbo_buyer_name+"_"+cbo_location_name+"_"+txt_style_ref+"_"+txt_job_no+"_"+txt_po_no+"_"+cbo_country_name+"_"+txt_date_from+"_"+txt_date_to+"_"+cbo_year+"_"+cbo_ship_status+"_"+cbo_order_status+"_"+txt_booking_no+'_'+type;
		if (window.XMLHttpRequest)
		{// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else
		{// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		
		freeze_window(3);
		xmlhttp.onreadystatechange=function()
		{
			if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{				
				var response=(xmlhttp.responseText).split('####');	
				// alert(response[1]);
				$('#report_container2').html(response[0]);
				document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
				// ppend_report_checkbox('table_header_1',1);
				setFilterGrid("table_filter",-1,tableFilters);
				release_freezing();
			}
		}
		xmlhttp.open("GET","requires/weekly_capacity_and_order_booking_status_v2_controller.php?data="+data+"&action=report_generate",true);
		xmlhttp.send();
	}
		
	function percent_set()
	{
		//alert("monzu");
		var tot_row=document.getElementById('tot_row').value;
		var tot_value_js=document.getElementById('total_value').value;
		
			for(var i=1;i<tot_row;i++)
		{
			var value_js=document.getElementById('value_'+i).value;
			var percent_value_js=((value_js*1)/(tot_value_js*1))*100
			document.getElementById('value_percent_'+i).innerHTML=percent_value_js.toFixed(2);
		}
	}
	function openmypage_image(page_link,title)
	{
		//alert("monzu");
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
		}
	}

	function print_report_part_by_part(id,button_id)
	{		
		$(button_id).removeAttr("onClick").attr("onClick","javascript:window.print()");
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+document.getElementById(id).innerHTML+'</body</html>');
			
		d.close();
		$(button_id).removeAttr("onClick").attr("onClick","print_report_part_by_part("+id,button_id+")");
		
	}
		
	function generate_ex_factory_popup(action,job_no,id,width)
	{
		//alert(job_no); 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/weekly_capacity_and_order_booking_status_v2_controller.php?action='+action+'&job_no='+job_no+'&id='+id, 'Ex-Factory Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}
	
	function getCompanyId() 
	{
	    var company_id = document.getElementById('cbo_company_name').value;
	    if(company_id !='') {
	      var data="action=load_drop_down_buyer&choosenCompany="+company_id;
	      http.open("POST","requires/weekly_capacity_and_order_booking_status_v2_controller.php",true);
	      http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	      http.send(data); 
	      http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#buyer_td').html(response);
	          }			 
	      };
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
		var page_link='requires/weekly_capacity_and_order_booking_status_v2_controller.php?action=job_wise_search&company='+company+'&buyer='+buyer+'&cbo_year='+cbo_year+'&type='+type+'&txt_job_no='+txt_job_no; 
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=370px,center=1,resize=0,scrolling=0','../../')
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
			else if(type==4)
			{
				$('#txt_booking_no').val(job_no);
				$('#txt_po_no_hidden').val(job_id);
			}			
		}
	}
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$("#scroll_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="400px";
		
		$("#scroll_body tr:first").show();
	}
	function openmypage_cutting_sewing_total(po,type,action)
	{
		var data=po+'**'+type;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/weekly_capacity_and_order_booking_status_v2_controller.php?data='+data+'&action='+action, 'Production Popup', 'width=650px,height=450px,center=1,resize=0,scrolling=0','../');
	}
</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../");  ?>
   <h3 style="width:1570px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')"> -Search Panel</h3>
       <div id="content_search_panel"> 
       
            <form id="form">
                <fieldset style="width:98%;">
                    <div  style="width:100%" align="center">
                            <table class="rpt_table" width="1570" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <thead>                                   
                                    <tr>
                                        <th class="must_entry_caption">Company</th>
                                        <th>Working Company</th>
                                        <th>Location</th>
                                        <th>Buyer</th>
                                        <th>Style</th>
                                        <th>Job</th>
                                        <th>Order</th>
                                        <th>Booking No</th>
                                        <th>Country</th>
                                        <th>Year</th>
                                        <th class="must_entry_caption" colspan="2">Country Ship Date</th>
                                        <th>Shiping Status</th>
                                        <th>Order Status</th>
                                        <th><input type="reset" name="reset" id="reset" value="Reset" style="width:80px" class="formbutton" /></th>
                                    </tr>
                                </thead>
                                <tr>
                                    <td id="company_td"> 
                                       	<?

											
                                       	echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, " load_drop_down( '../merchandising_report/requires/weekly_capacity_and_order_booking_status_v2_controller', this.value, 'load_drop_down_buyer', 'buyer_td' )" );
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
                                    <td id="buyer_td">
                                     <? 
                                        echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --", $selected, "" );
                                     ?>	
                                    </td>
                                 
                                     <td> 
                                       <input type="text"  name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:70px;" tabindex="1" placeholder="Write/Browse" onDblClick="job_no_popup(2);"> 
                                    </td>
                                    <td> 
                                    	<input type="text"  name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px;" tabindex="1" placeholder="Write/Browse" onDblClick="job_no_popup(1);"> 
                                    </td>
                                    <td>
                                    	<input type="text"  name="txt_po_no" id="txt_po_no" class="text_boxes" style="width:70px;" tabindex="1" placeholder="Write/Browse" onDblClick="job_no_popup(3);"> 
                                    </td>
                                     <td>
                                    	<input type="text"  name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:100px;" tabindex="1" placeholder="Write/Browse" onDblClick="job_no_popup(4);"> 
                                    </td>
                                    <td>
                                    	<?
                                       	echo create_drop_down( "cbo_country_name", 172, "select id,country_name from lib_country where status_active =1 and is_deleted=0 order by country_name","id,country_name", 1, "-- Select --", $selected, "" );
                                        ?>
                                    </td>
                                    <td>
                                    	<? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-All-","", "",0,"" ); ?>
                                    </td>
                                   	<td><input name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:65px">
                                    </td>
                                    <td><input name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:65px">
                                    </td>
                                    <td>
										<?
											$ship_status_arr = array(1=>"Full Pending",2=>"Partial Shipment",3=>"Full Shipment/Closed"); 
											echo create_drop_down( "cbo_ship_status", 100, $ship_status_arr,"", 1,"-All-",1, "",0,"" ); 
										?>	
                                    </td>
                                    <td>
									<? 
                                    	echo create_drop_down( "cbo_order_status", 100, $order_status,"", 1, "ALL", 1, "" );
                                    ?>	
                                    </td>
                                    <td>
                                    	<div style="display:flex;">
                                    		 <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:80px" class="formbutton" />
                                    		 <input type="button" name="search1" id="search1" value="Show 2" onClick="generate_report(2)" style="width:80px" class="formbutton" />
                                    	</div>
                                                                      
                                    </td>
                                </tr>                            	
                                <tr>
                                    <td colspan="12" align="center">
                                        <? echo load_month_buttons(1); ?>
                                    </td>
                                </tr>
                            </table>
                    </div>
                </fieldset>
            </form>
        </div>
       	<div id="report_container" align="center" style="margin: 5px 0"></div>
       	<div id="report_container2"></div> 
      	<script>
			set_multiselect('cbo_company_name','0','0','','0');
			setTimeout[($("#company_td a").attr("onclick","disappear_list(cbo_company_name,'0');getCompanyId();") ,3000)];
			set_multiselect('cbo_working_company_name','0','0','','0');
			set_multiselect('cbo_location_name','0','0','','0');
			set_multiselect('cbo_ship_status','0','0','','0');
			$("#multiselect_dropdown_table_headercbo_working_company_name a").click(function(){
	    		load_location();
	    	});
	    	$("#multiselect_dropdown_table_headercbo_company_name a").click(function(){
	    		load_button();
	    	});

	    	function load_location()
	    	{
	    		var company=$("#cbo_working_company_name").val();
	    		load_drop_down( 'requires/weekly_capacity_and_order_booking_status_v2_controller',company, 'load_drop_down_location', 'location_td' );
	    		set_multiselect('cbo_location_name','0','0','0','0');     		 
	    	}
	    	function load_button()
	    	{
	    		var company=$("#cbo_company_name").val();
	    		get_php_form_data( company, 'company_wise_report_button_setting','requires/weekly_capacity_and_order_booking_status_v2_controller' );
	    		     		 
	    	}
			
		</script>

		<?
		$sql=sql_select("select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name");
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
        
    </div>
    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>