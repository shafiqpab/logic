<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Sewing Plan Vs Production Report.
Functionality	:	
JS Functions	:
Created by		:	Aziz 
Creation date 	: 	23-06-2015
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();

extract($_REQUEST);



if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');

$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Style Wise Shipment Report","../../", 1, 1, $unicode,1,1);
?>	
<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	
	var tableFilters = 
	 {
		col_33: "none",
		col_operation: {
		id: ["total_order_qnty","total_order_qnty_in_pcs","value_tot_cm_cost","value_tot_cost","value_order","value_margin","value_tot_trims_cost","value_tot_embell_cost"],
	    col: [9,11,25,26,29,30,31,32],
	    operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
	    write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 }
	 
	 function openmypage_job()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_year_id = $("#cbo_year").val();
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/sewing_plan_vs_production_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../');
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
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value;
		//alert (data);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/sewing_plan_vs_production_report_controller.php?action=order_no_popup&data='+data,'Order No Popup', 'width=630px,height=420px,center=1,resize=0','../')
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
	
	function fn_report_generated(type)
	{
		if(form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false)//*txt_date_from*txt_date_to*From Date*To Date
		{
			return;
		}
		else
		{	
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_floor_id*cbo_line_id*cbo_buyer_name*txt_date_from*txt_date_to*txt_job_no*txt_job_id*txt_order_id*txt_order_no*cbo_date_type*cbo_lc_company_id*cbo_brand_id*cbo_season_id*cbo_season_year',"../../")+'&type='+type;
			freeze_window(3);
			http.open("POST","requires/sewing_plan_vs_production_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			//var reponse=trim(http.responseText).split("****");
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);
			//alert(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//append_report_checkbox('table_header_1',1);
			
			setFilterGrid("table_body",-1,tableFilters);
			setFilterGrid("tbl_header",-1);
			
	 		show_msg('3');
			release_freezing();
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#scroll_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#scroll_body tr:first').show();
		//document.getElementById('scroll_body').style.maxWidth="120px";
	}
	
	function getCompanyId() 
	{
	    var company_id = document.getElementById('cbo_company_id').value;
	    if(company_id !='') {
	      var data="action=load_drop_down_location&data="+company_id;
	      http.open("POST","requires/sewing_plan_vs_production_report_controller.php",true);
	      http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	      http.send(data); 
	      http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              	var response = trim(http.responseText);
	              	$('#location_td').html(response);
					set_multiselect('cbo_location_id','0','0','','0');
					setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location_id,'0');getLocationId();") ,3000)];	
	          }			 
	      };
	    }   
	}
	function getButtonSetting()
	{
		 var company_id = document.getElementById('cbo_company_id').value;
		get_php_form_data(company_id,'print_button_variable_setting','requires/sewing_plan_vs_production_report_controller' );
	}
	
	function getLocationId() 
	{
	    var company_id = document.getElementById('cbo_company_id').value;
	    var location_id = document.getElementById('cbo_location_id').value;
	    if(company_id !='') {
	      var data="action=load_drop_down_floor&company_id="+company_id+"&location_id="+location_id;
	      http.open("POST","requires/sewing_plan_vs_production_report_controller.php",true);
	      http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	      http.send(data); 
	      http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              	var response = trim(http.responseText);
	              	$('#floor_td').html(response);
					set_multiselect('cbo_floor_id','0','0','','0');	
					setTimeout[($("#floor_td a").attr("onclick","disappear_list(cbo_floor_id,'0');getFloorId();") ,3000)];
	          }			 
	      };
	    }   
	}
	
	function getFloorId() 
	{
	    var company_id = document.getElementById('cbo_company_id').value;
	    var location_id = document.getElementById('cbo_location_id').value;
	    var floor_id = document.getElementById('cbo_floor_id').value;
	    if(company_id !='') {
	      var data="action=load_drop_down_line&company_id="+company_id+"&location_id="+location_id+"&floor_id="+floor_id;
	      http.open("POST","requires/sewing_plan_vs_production_report_controller.php",true);
	      http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	      http.send(data); 
	      http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              	var response = trim(http.responseText);
	              	$('#line_td').html(response);
					set_multiselect('cbo_line_id','0','0','','0');	
	          }			 
	      };
	    }   
	}

function print_report_button_setting(report_ids) 
    {
        //alert(report_ids);
        $('#show_button').hide();
        $('#show_button1').hide();
        $('#show_button2').hide();
        var report_id=report_ids.split(",");
        report_id.forEach(function(items){
            if(items==108){$('#show_button').show();}
            else if(items==259){$('#show_button1').show();}
            else if(items==242){$('#show_button2').show();}
            });
    }
    
	
</script>
</head>
 
<body onLoad="set_hotkey();">
		 
<form id="cost_breakdown_rpt">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:1740px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:1740px;">
                <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                	<thead>
                    	<tr>                   
                            <th>LC Company</th>
                            <th class="must_entry_caption">Working Company</th>
                            <th>Location</th>
                            <th>Floor</th>
                            <th>Line</th>
                            <th>Buyer Name</th>
                            <th>Brand</th>
                            <th>Season</th>
                            <th>Season Year</th>
                            <th>Job No</th>
                            <th>Order</th>
                            <th>Date Category</th>
                            <th class="must_entry_caption" id="td_date_caption">Shipment Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td> 
                           <?
								echo create_drop_down( "cbo_lc_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sewing_plan_vs_production_report_controller', this.value, 'load_drop_down_lc_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                    
                    
                    
                    
                        <td id="company_td"> 
                           <?
								echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "-- Select Company --", $selected, "load_drop_down('requires/sewing_plan_vs_production_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                            ?>
                        </td>
                       
                        <td id="location_td">
							<? 
								echo create_drop_down( "cbo_location_id", 150, $blank_array,"", 0, "-- Select --", $selected, "",1,"" );
                            ?>
                        </td>
                        <td id="floor_td" title="Use only button 3">
							<? 
								echo create_drop_down( "cbo_floor_id", 150, $blank_array,"", 0, "-- Select --", $selected, "",1,"" );
                            ?>
                        </td>
                        <td id="line_td" title="Use only button 3">
							<? 
								echo create_drop_down( "cbo_line_id", 150, $blank_array,"", 0, "-- Select --", $selected, "",1,"" );
                            ?>
                        </td>
                          <td id="buyer_td">
                            <? 
								echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );  
                            ?>
                        </td>
                        
                        
                          <td id="brand_td">
                            <? 
								echo create_drop_down( "cbo_brand_id", 80, array(),"", 1, "- All -", $selected, "" );  
                            ?>
                        </td>
                          <td id="season_td">
                            <? 
								echo create_drop_down( "cbo_season_id", 80, array(),"", 1, "- All -", $selected, "" );  
                            ?>
                        </td>
                          <td>
                            <? 
								echo create_drop_down( "cbo_season_year", 50, $year,"", 1, "- All -", $selected, "" );  
                            ?>
                        </td>
                        
                        
                        
                         <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:80px" onDblClick="openmypage_job();" placeholder="Wr./Br. Job" />
                             <input type="hidden" id="txt_job_id" name="txt_job_id"/>
                        </td>
                        <td>
                       <input type="text" id="txt_order_no" name="txt_order_no" class="text_boxes" style="width:80px" onDblClick="openmypage_order();" placeholder="Wr./Br. Order"  />
                            <input type="hidden" id="txt_order_id" name="txt_order_id"/>
                        </td>
                        <td>
                            <? 
                               $date_type_arr=array(1=>'Shipment Date',2=>' Plan Date');
							    echo create_drop_down( "cbo_date_type", 80, $date_type_arr,"", 1, "-- Select --", 1, "if(this.value==1)document.getElementById('td_date_caption').innerHTML='Shipment Date';else document.getElementById('td_date_caption').innerHTML='Plan Date';",0,"" );
                            ?>
                        </td>
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" >&nbsp; To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" ></td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:100px; display: none;" value="Show" onClick="fn_report_generated(1)" />
                            
                        </td>
                    </tr>
                    </tbody>
                </table>
                <table>
                    <tr>
                        <td>
                            <? echo load_month_buttons(1); ?>
                            <input type="button" id="show_button1" class="formbutton" style="width:100px; display: none;" value="Show2" onClick="fn_report_generated(2)" />
                            <input type="button" id="show_button2" class="formbutton" style="width:100px; display: none;" value="Show3" onClick="fn_report_generated(3)" />
                        </td>
                    </tr>
                </table> 
            </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
   
 </form>    
</body>
<script>
	set_multiselect('cbo_company_id','0','0','','0');	
	setTimeout[($("#company_td a").attr("onclick","disappear_list(cbo_company_id,'0');getCompanyId();getButtonSetting();") ,3000)];
	set_multiselect('cbo_location_id','0','0','','0');
	setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location_id,'0');getLocationId();") ,3000)];
	set_multiselect('cbo_floor_id','0','0','','0');
	setTimeout[($("#floor_td a").attr("onclick","disappear_list(cbo_floor_id,'0');getFloorId();") ,3000)];
	set_multiselect('cbo_line_id','0','0','','0');
</script>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
