<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Closing Stock Report
				
Functionality	:	
JS Functions	:
Created by		:	Ashraful
Creation date 	: 	21-01-2014
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
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/batch_dying_fnishing_cost_controller.php?action=batch_popup&data='+data,'Batch Popup', 'width=950px,height=400px,center=1,resize=0','../../')
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
				id: ["value_total_batch_weight_single","value_total_chemical_cost_single","value_total_dyeing_cost_single","value_total_chemical_price_single","value_total_redying_chemic_oost_single","value_total_redying_dying_cost_single","value_total_redying_all_cost_single","value_total_redying_all_cost_single_tk","value_grand_total_cost_single"],
				col: [15,16,17,18,23,24,25,26,27],
				operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}
		}
	
		var tableFilters2 = 
		{
			col_0: "none",
			col_operation: { 
				id: ["value_total_batch_weight_multiple","value_total_chemical_cost_multiple","value_total_dyeing_cost_multiple","value_total_chemical_price_multiple","value_total_cost_per_kg_multi","value_total_chemical_cost_multi","value_total_redying_dying_cost_multiple","value_total_redying_all_cost_multiple","value_total_redye_cost_per_kg_multi","value_grand_total_cost_multiple","value_total_total_per_kg_cost_multi"],
				col: [16,17,18,19,20,24,25,26,27,28,29],
				operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}
		}
		/*var tableFilters3 = 
		{
			col_0: "none",
			col_operation: { 
				id: ["value_total_chemical_cost","value_total_tot_dyes_cost","value_total_material_cost","value_grand_tot_dying_oh_cost","value_grand_tot_fin_oh_cost","value_grand_batch_cost"],
				col: [15,16,17,18,19,20],
				operation: ["sum","sum","sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}
		}*/
		var tableFilters3 = 
		{
			col_0: "none",
			col_operation: { 
				id: ["value_total_chemical_cost_single","value_total_dyeing_cost_single","value_total_chemical_price_single","value_total_cost_per_kg","value_total_redying_chemic_oost_single","value_total_redying_dying_cost_single","value_total_redying_all_cost_single","value_total_total_cost_single","value_total_total_per_kg_cost"],
				col: [16,17,18,19,23,24,25,26,27],
				operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}
		}

		var tableFilters4 = 
		{
			col_0: "none",
			col_operation: { 
				id: ["value_total_batch_weight_single","value_total_chemical_cost_single","value_total_dyeing_cost_single","value_total_chemical_price_single","value_total_chemical_per_single","value_total_redying_chemic_oost_single","value_total_redying_dying_cost_single","value_total_redying_all_cost_single","value_total_redying_all_cost_single_tk","value_total_total_cost_single","value_total_total_cost_per_single"],
				col: [16,17,18,19,20,24,25,26,27,28,29],
				operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}
		}

		var tableFiltersSummary = 
		{
			col_0: "none",
			col_operation: { 
				id: ["value_total_chemical_cost_summary","value_total_dyeing_cost_summary","value_total_chemical_price_summary","value_total_redying_chemic_oost_summary","value_total_redying_dying_cost_summary","value_total_redying_all_cost_summary","value_total_total_cost_summary"],
				col: [3,4,5,7,8,9],
				operation: ["sum","sum","sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}
		}
	
	
	function generate_report(operation)
	{
		
		var report_title=$( "div.form_caption" ).html(); 
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_working_name = $("#cbo_working_name").val();
		var cbo_value_with = $("#cbo_value_with").val();
		var from_date = $("#txt_date_from").val();
		var to_date = $("#txt_date_to").val();
		var batch_id = $("#txt_batch_id").val();
		var batch_no = $("#txt_batch_no").val();
		var batch_type =$("#cbo_batch_type").val();
		var txt_file_no =$("#txt_file_no").val();
		var txt_ref_no =$("#txt_ref_no").val();
		var txt_booking_no =$("#txt_booking_no").val();	
		var txt_po_no =$("#txt_po_no").val();
		var txt_job =$("#txt_job").val();
		var cbo_buyer_name =$("#cbo_buyer_name").val();
		var cbo_season_id =$("#cbo_season_id").val();
		var txt_samp_ref_no =$("#txt_samp_ref_no").val();
		var location_id =$("#location_id").val();
		var floor_id =$("#floor_id").val();
		var cbo_year_selection =$("#cbo_year_selection").val();
		
		if($("#txt_batch_no").val()!='' || $("#txt_job").val()!='' || $("#txt_file_no").val()!='' || $("#txt_ref_no").val()!='' || $("#txt_samp_ref_no").val()!='' || $("#txt_booking_no").val()!='' || $("#cbo_season_id").val()!=0)
		{
			
			if(operation==8)
			{
				if( form_validation('txt_date_from*txt_date_to','Form Date*To Date')==false )
				{
					return;
				}
			}
			/*if( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}*/
			if(cbo_company_name == 0 && cbo_working_name ==0) 
			{			
				alert("Please Select either a company or a working company");
				return;			
			}	
		}
		else
		{
			/*if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Range*To Range')==false )
			{
				return;
			}*/
			if(cbo_company_name == 0 && cbo_working_name ==0) 
			{			
				alert("Please Select either a company or a working company");
				return;			
			}
			else if( form_validation('txt_date_from*txt_date_to','Form Date*To Date')==false )
			{
				return;
			}
		}
		
		var dataString = "";
		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_working_name="+cbo_working_name+"&from_date="+from_date+"&to_date="+to_date+"&cbo_value_with="+cbo_value_with
		+"&batch_id="+batch_id+"&batch_no="+batch_no+"&txt_file_no="+txt_file_no+"&txt_ref_no="+txt_ref_no+"&batch_type="+batch_type+"&txt_booking_no="+txt_booking_no+"&txt_po_no="+txt_po_no+"&txt_job="+txt_job+"&txt_samp_ref_no="+txt_samp_ref_no+"&cbo_buyer_name="+cbo_buyer_name+"&cbo_season_id="+cbo_season_id+"&report_type="+operation+"&report_title="+report_title+"&floor_id="+floor_id+"&location_id="+location_id+"&cbo_year_selection="+cbo_year_selection;
		
		//var data="action=report_generate"+get_submitted_data_string('cbo_company_name*txt_date_from*txt_date_to*cbo_year*cbo_report_type*cbo_search_by*txt_search_comm*cbo_presentation',"../../../")+'&report_title='+report_title;
		if(batch_type==3 && operation==5) //Batch Wise Button
		{
			alert("This Type not allowed For Batch Wise Button");
			$("#cbo_batch_type").val(1);
			$("#cbo_batch_type").focus();
			return;
		}
		if(operation==3 || operation==10)
		{
			var data="action=generate_report"+dataString;
		}
		 if(operation==4)
		{
			var data="action=generate_report2"+dataString;
		}
		 if(operation==5)
		{
			var data="action=generate_report5"+dataString;
		}
		 if(operation==6)
		{
			var data="action=generate_report6"+dataString;
		}
		 if(operation==7)
		{
			var data="action=generate_report_floor"+dataString;
		}
		 if(operation==8)
		{
			var data="action=generate_report3"+dataString;
		}
		 if(operation==9)
		{
			//alert(operation);
			//var data="action=generate_batch_report"+dataString;
			var data="action=generate_report9"+dataString;
		}
			
		  // alert (data);
		freeze_window(operation);
		http.open("POST","requires/batch_dying_fnishing_cost_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("****");
			var rpt_type = reponse[3]*1;
			
			
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
			if(rpt_type==3 || rpt_type==10)
			{
				if(rpt_type==3)
				{
					$("#report_container2").html(reponse[0]);  
					document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>&nbsp;&nbsp;<a href="requires/'+reponse[2]+'" style="text-decoration:none"><input type="button" value="Excel Preview (Summary)" name="excel" id="excel" class="formbutton" style="width:170px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(2)" value="Print Preview (Summary)" name="Print" class="formbutton" style="width:170px"/>';
				}
				else{
					 
					$('#excel_print_show').removeAttr('href').attr('href','requires/'+trim(reponse[1]));
					document.getElementById('excel_print_show').click();
				}
				
			}
			else
			{
				$("#report_container2").html(reponse[0]);  
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			}
			if(rpt_type!=10)
			{
				if(rpt_type!=7)
				{
					 
				setFilterGrid("table_body_id",-1,tableFilters3);
				setFilterGrid("re_table_body_id",-1);
				setFilterGrid("table_body_multibatch_id",-1,tableFilters2);
				setFilterGrid("summary_table_body_id",-1,tableFiltersSummary);
				setFilterGrid("table_body_id_s",-1,tableFilters4);
				setFilterGrid("table_body_batch",-1);
				}
		  }
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window(type)
	{
		if (type==1) 
		{
			var report_cont='report_container2';
			var scroll_body='scroll_body';
			var scroll_body='re_scroll_body';
		}
		else if (type==2) 
		{
			var report_cont='report_container2';
			var scroll_body='scroll_body_s';
		}
		else
		{
			var report_cont='summary_part';
			var scroll_body='scroll_body_summary';
		}
		document.getElementById(scroll_body).style.overflow="auto";
		document.getElementById(scroll_body).style.maxHeight="none"; 

		$("#summary_table_body_id tr:first").hide();
		$("#table_body_id tr:first").hide();
		$("#table_body_id_s tr:first").hide();
		$("#re_table_body_id tr:first").hide();
		$("#table_body_batch tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById(report_cont).innerHTML+'</body</html>');
		d.close(); 
		document.getElementById(scroll_body).style.overflow="auto"; 
		document.getElementById(scroll_body).style.maxHeight="400px";

		$("#summary_table_body_id tr:first").show();
		$("#table_body_id tr:first").show();
		$("#table_body_id_s tr:first").show();
		$("#re_table_body_id tr:first").show();
		$("#table_body_batch tr:first").show();
	}

	function new_window2_______()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById().innerHTML+'</body</html>');
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
	 var width=1250;
	 emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/batch_dying_fnishing_cost_controller.php?action='+action+'&batch_id='+batch_id+'&batch_no='+batch_no+'&batch_type='+batch_type, 'Subprocess Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	 
  }
  
  function fn_1st_batch(batch_id,action)
  {
	 var batch_type=$('#cbo_batch_type').val();
	 var width=900;
	 emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/batch_dying_fnishing_cost_controller.php?action='+action+'&batch_id='+batch_id+'&batch_type='+batch_type, 'Batch Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	 
  }
   function fn_1st_batch2(batch_id,action,req_id,type)
  {
	 var batch_type=$('#cbo_batch_type').val();
	 if(type==2 || type==2)
	 {
	 var width=900;
	 }
	 else{width=1200;}
	 
	 emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/batch_dying_fnishing_cost_controller.php?action='+action+'&batch_id='+batch_id+'&batch_type='+batch_type+'&req_id='+req_id, 'Batch Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	 
  }
  
  function fn_total_batch(batch_id,action)
  {
	 var batch_type=$('#cbo_batch_type').val();
	 var width=920;
	 emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/batch_dying_fnishing_cost_controller.php?action='+action+'&batch_id='+batch_id+'&batch_type='+batch_type+'&action='+action, 'Batch Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	 
  }
  
  
  
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?><br />    		 
        <form name="closingstock_1" id="closingstock_1" autocomplete="off" > 
         <h3 style="width:1860px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:1860px" >      
            <fieldset>  
                <table class="rpt_table" width="1860" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                       <th class="must_entry_caption">Company</th>
                       <th class="must_entry_caption">Working Company</th>
                       <th>Location</th>
                       <th>Batch Type</th>
                       <th>Buyer</th>
                        <th width="80">Season</th>
                       <th>Job No</th> 
                       <th>Order No</th> 
                       <th>File No</th> 
                       <th>Ref. No</th>
                       <th>Sample Ref. No</th>
                       <th>Batch No</th>
                       <th>Booking No</th>
                       <th>Floor</th>
                       <th>Based On</th>
                       <th class="must_entry_caption"> Date Range</th>
                       <th width="230"><input type="reset" name="res" id="res" value="Reset" style="width:40px" class="formbutton" onClick="reset_form('closingstock_1','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                           <td>
							<? 
                            echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/batch_dying_fnishing_cost_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/batch_dying_fnishing_cost_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data( this.value, 'company_wise_report_button_setting','requires/batch_dying_fnishing_cost_controller' );" );//load_drop_down( 'requires/batch_dying_fnishing_cost_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );
                            ?>                            
                            </td>
                            <td>
							<? 
                            echo create_drop_down( "cbo_working_name", 120, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/batch_dying_fnishing_cost_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/batch_dying_fnishing_cost_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data( this.value, 'company_wise_report_button_setting','requires/batch_dying_fnishing_cost_controller' );" );//load_drop_down( 'requires/batch_dying_fnishing_cost_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );
                            ?>                            
                            </td>
                            <td id="location_td">
                            	<?
	                                echo create_drop_down( "location_id", 100, $blank_array,"",1, "--Select--", $selected, "",0,""  );
	                            ?>
                            </td>
                            <td align="center">	
							<?
                                echo create_drop_down( "cbo_batch_type", 100, $order_source,"",1, "--Select--", 0,0,0 );
                            ?>
                            </td>
                             <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "- All Buyer -", $selected, "",0,"" );
                            ?>
                       		</td>
                             <td id="season_td">
                            <? //echo create_drop_down( "txt_season", 150, $blank_array,'', 1, "-- Select Season--",$selected, "" ); 
							echo create_drop_down( "cbo_season_id", 80, "select id, season_name from lib_buyer_season where  status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "",1 );?>
                        
                        </td>
                        
                            
                              <td>
                           		 <input type="text" id="txt_job" name="txt_job" class="text_boxes" style="width:60px"  placeholder="Write" />
                       		 </td>
                         	 <td>
                           		 <input type="text" id="txt_po_no" name="txt_po_no" class="text_boxes" style="width:60px"  placeholder="Write" />
                       		 </td>
                             <td>
                            	<input style="width:60px;" name="txt_file_no" id="txt_file_no" class="text_boxes"  placeholder="Write"   />
                               
                            </td>
                             <td>
                            	<input style="width:60px;" name="txt_ref_no" id="txt_ref_no" class="text_boxes"  placeholder="Write"  />
                            </td>
                             <td>
                           		 <input type="text" id="txt_samp_ref_no" name="txt_samp_ref_no" class="text_boxes" style="width:60px"  placeholder="Write" />
                       		 </td>
                            <td>
                            	<input style="width:80px;" name="txt_batch_no" id="txt_batch_no" class="text_boxes" onDblClick="openmypage_batch_popup(1)" placeholder="Browse/Write"  onKeyUp="clear_box()" />
                                <input type="hidden" name="txt_batch_id" id="txt_batch_id" style="width:90px;"/>
                            </td>
                             <td>
                            	<input style="width:80px;" name="txt_booking_no" id="txt_booking_no" class="text_boxes" onDblClick="openmypage_batch_popup(2)" placeholder="Browse/Write"  onKeyUp="clear_box()" />
                                <input type="hidden" name="txt_batch_id" id="txt_batch_id" style="width:90px;"/>
                            </td>
                            
                            <td id="floor_td">
                            	<?
	                                echo create_drop_down( "floor_id", 100, $blank_array,"",1, "--Select--", $selected, "",0,""  );
	                            ?>
                            </td>

                             <td> 
                           <?   
                                $valueWithArr=array(1=>'Dyeing Date',2=>'Batch Date');
                                echo create_drop_down( "cbo_value_with", 80, $valueWithArr, "",0, "-- Select --",1, 0, 0);
                            ?>
                          </td>
                            <td align="center">
                                <input type="text" name="txt_date_from" id="txt_date_from" value="<? // echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:50px;" placeholder="From Date"/>                    							
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" value="<? //echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:50px;" placeholder="To Date"/>                        
                            </td>
                            <td>
                                <input type="button" name="search" id="show" value="Show" onClick="generate_report(3)" style="width:40px" class="formbutton" />
                                <input type="button" name="search" id="report" value="Report" onClick="generate_report(4)" style="width:40px" class="formbutton" />
                                 <input type="button" name="search" id="batch-wise" value="Batch Wise" onClick="generate_report(5)" style="width:60px" class="formbutton" />
                                 <input type="button" name="search" id="batch-wise2" value="Batch Wise2" onClick="generate_report(6)" style="width:80px" class="formbutton" />
								
                                 <input type="button" name="search" id="batch-wise3" value="Floor Wise" onClick="generate_report(7)" style="width:80px" class="formbutton" />
								 <input type="button" name="search" id="batch-wise4" value="All Dyeing Cost" onClick="generate_report(8)" style="width:90px" class="formbutton" />
								 <input type="button" name="search" id="show2" value="show2" onClick="generate_report(9)" style="width:60px" class="formbutton" />
								 <input type="button" name="search" id="excel_show" value="Excel show" onClick="generate_report(10)" style="width:70px" class="formbutton" /><a id="excel_print_show" href="" style="text-decoration:none" download hidden>BB</a>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="15" align="center"><? echo load_month_buttons(1);  ?></td>
                            
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
