<?
/*-------------------------------------------- Comments----------------
Purpose			: 	This form will create Capacity and Order Booking Status Report V2
Functionality	:	
JS Functions	:
Created by		:	Md Mamun Ahmed Sagor 
Creation date 	: 	15/03/2023
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		: 
-----------------------------------------------------------------------*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------
echo load_html_head_contents("Capacity and Order Booking Status Report V2","../../../", 1, 1, $unicode,1,1);
?>	
<script>
	var permission='<? echo $permission; ?>';
	//[14,17,18,20,21,22,23,24,25,26,27,28]
	var tableFilters = 
	{
		col_operation: 
		{
			id: ["value_smv_tot","total_order_qnty_pcs","value_yarn_req_tot","value_total_order_value","value_total_commission","value_total_net_order_value","total_ex_factory_qnty","total_ex_factory_val","total_short_access_qnty","total_over_access_qnty","value_total_short_access_value","value_total_over_access_value"],
		  //col: [17,20,21,23,24,25,26,27,28,29,30,31],
			col: [20,23,24,26,27,28,29,30,31,32,33,34],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		} , 
		col_31: "select",
		col_32: "select",
		col_33: "select",
		col_36: "select",
		col_37: "select",
		display_all_text:'Show All'
	}
	var tableFilters5 = 
	{
		col_operation: 
		{
			id: ["total_order_qnty_pcs","value_total_order_value","total_ex_factory_qnty","total_ex_factory_val"],		
			col: [6,8,10,11],
			operation: ["sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		} , 
		
		display_all_text:'Show All'
	}
	var tableFilters2 = 
	{
		col_operation: 
		{
			id: ["value_smv_tot","total_order_qnty_pcs","value_yarn_req_tot","value_total_order_value","value_total_commission","value_total_net_order_value","total_ex_factory_qnty","total_ex_factory_val","total_short_access_qnty","total_over_access_qnty","value_total_short_access_value","value_total_over_access_value"],
		  //col: [17,20,21,23,24,25,26,27,28,29,30,31],
			col: [21,24,25,27,28,29,30,31,32,33,34,35],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		} , 
		col_32: "select",
		col_33: "select",
		col_34: "select",
		col_37: "select",
		col_38: "select",
		display_all_text:'Show All'
	}
	var tableFilters3 = 
	{
		col_operation: 
		{
			id: ["value_smv_tot","total_order_qnty_pcs","value_yarn_req_tot","value_total_order_value","value_total_commission","value_total_net_order_value","total_ex_factory_qnty","total_ex_factory_val","total_short_access_qnty","total_over_access_qnty","value_total_short_access_value","value_total_over_access_value"],
		  //col: [17,20,21,23,24,25,26,27,28,29,30,31],
			col: [20,23,24,26,27,28,29,30,31,32,33,34],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		} , 
		col_32: "select",
		col_33: "select",
		col_34: "select",
		col_37: "select",
		col_38: "select",
		display_all_text:'Show All'
	}

	var tableFilters4 = 
	{
		col_operation: 
		{
			id: ["value_smv_tot","total_order_qnty_pcs","value_total_order_value","value_total_commission","value_total_net_order_value","total_ex_factory_qnty","value_total_ex_factory_val","total_short_access_qnty","value_total_short_access_value","value_total_over_access_value"],
			col: [21,24,26,27,28,29,30,31,32,33],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	var tableFilters44 = 
	{
		col_operation: 
		{
			id: ["value_smv_tot","total_order_qnty_pcs","total_fab_req","value_total_order_value","value_total_commission","value_total_net_order_value","total_ex_factory_qnty","value_total_ex_factory_val","total_short_access_qnty","value_total_short_access_value","value_total_over_access_value"],
			col: [21,24,26,28,29,30,31,32,33,34,36],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	function openmypage_job()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/capacity_and_order_booking_status_report_v2_controller.php?action=job_no_popup&data='+data,'Job No Popup', 'width=650px,height=420px,center=1,resize=0','../../')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("job_no_id");
			var theemailv=this.contentDoc.getElementById("job_no_val");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_job_id").value=theemail.value;
			    document.getElementById("txt_job_no").value=theemailv.value;
				release_freezing();
			}
		}
	}
	
	function generate_report(div,type,type_summary)
	{
		var txt_style_ref = document.getElementById('txt_style_ref').value;
		var txt_job_no    = document.getElementById('txt_job_no').value;
		var txt_internal_ref = document.getElementById('txt_internal_ref').value;

		if (type_summary == 3)
		{
			if (txt_style_ref != '' || txt_job_no != '')
			{
				
			}
			else if(type=='report_generate' && txt_internal_ref != '')
			{
				
			}
			else
			{
				if ( form_validation('txt_date_from*txt_date_to','From Date*To Date')==false )
				{
					return;
				}
			}	
		}
		else 
		{	
			if ( form_validation('txt_date_from*txt_date_to','From Date*To Date')==false )
			{
				return;
			}
		}
		var txt_date_from=document.getElementById('txt_date_from').value;
		var txt_date_to=document.getElementById('txt_date_to').value;
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var cbo_team_name=document.getElementById('cbo_team_name').value;
		var cbo_team_member=document.getElementById('cbo_team_member').value;
		var cbo_category_by=document.getElementById('cbo_category_by').value;
		var zero_value=0;
		var cbo_capacity_source=document.getElementById('cbo_capacity_source').value;
		var cbo_year=document.getElementById('cbo_year').value;		
		var cbo_factory_merchant=document.getElementById('cbo_factory_merchant').value;
		var cbo_agent=document.getElementById('cbo_agent').value;
		var cbo_product_category=document.getElementById('cbo_product_category').value;
		var cbo_style_owner=document.getElementById('cbo_style_owner').value;
		var cbo_location_id=document.getElementById('cbo_location_id').value;
		var txt_job_id = document.getElementById('txt_job_id').value;
		
		
	
			zero_value=0
		
		var data=cbo_company_name+"_"+cbo_buyer_name+"_"+txt_date_from+"_"+txt_date_to+"_"+cbo_team_name+"_"+cbo_team_member+"_"+cbo_category_by+"_"+zero_value+"_"+cbo_capacity_source+"_"+cbo_year+"_"+txt_style_ref+"_"+cbo_factory_merchant+"_"+cbo_agent+"_"+cbo_product_category+"_"+cbo_style_owner+"_"+cbo_location_id+"_"+txt_job_no+"_"+txt_job_id;
		freeze_window(3);
		http.open("GET","requires/capacity_and_order_booking_status_report_v2_controller.php?data="+data+"&type_summary="+type_summary+"&type="+type+'&txt_internal_ref='+txt_internal_ref,true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=(http.responseText).split('****');
			
			if (response[2] == 5)
			{
				append_report_checkbox('table_header_1',1);
				append_report_checkbox('table_header_5',1);
				document.getElementById("check_uncheck_tr").style.display="table";
				if($("#check_uncheck").is(":checked")==false)
					$("#check_uncheck").attr("checked","checked");
				document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window2()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				$('#report_container2').html(response[0]);
			}
			else
			{
			
				document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
				$('#report_container2').html(response[0]);				
				
				// append_report_checkbox('table_header_1',1);
				// document.getElementById("check_uncheck_tr").style.display="table";
				// if($("#check_uncheck").is(":checked")==false)
				// 	$("#check_uncheck").attr("checked","checked");
				if(response[2]==2)
				{
					append_report_checkbox('table_header_1',1);
			
					document.getElementById("check_uncheck_tr").style.display="table";
					if($("#check_uncheck").is(":checked")==false)
					$("#check_uncheck").attr("checked","checked");

					// document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window2()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
					setFilterGrid("table_body",-1,tableFilters3);
				
				}else if(response[2]==6)
				{
				
					append_report_checkbox('table_header_1',1);
					document.getElementById("check_uncheck_tr").style.display="table";
					if($("#check_uncheck").is(":checked")==false)
					$("#check_uncheck").attr("checked","checked");

					// document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window2()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
					
					setFilterGrid("table_body",-1,tableFilters5);
				}
				else
				{
					setFilterGrid("table_body",-1,tableFilters2);
			
				}
			}			
			release_freezing();
		}
	}

	function new_window2()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		// var dataTableBody = document.getElementById('report_container2').innerHTML;
		document.getElementById('table_body').deleteRow(0);
	
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		/*d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+dataTableBody+'</body</html>');*/
		d.close();
		setFilterGrid("table_body",-1,tableFilters2);
	
	}
	
	function openmypage_image(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
		}
	}
	
 
 
	
	function print_report_part_by_part(id,button_id)
	{
		$(button_id).removeAttr("onClick").attr("onClick","javascript:window.print()");
		var w = window.open("Surprise", "_blank");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+document.getElementById(id).innerHTML+'</body</html>');
		d.close();
		$(button_id).removeAttr("onClick").attr("onClick","print_report_part_by_part('"+id+"','"+button_id+"')");
	}
	
	
    //for report summary
	var tableFilters7 = 
	{
		col_operation: 
		{
			id: ["value_smv_tot","total_order_qnty_pcs","total_fab_req","value_total_order_value","value_total_commission","value_total_net_order_value","total_ex_factory_qnty","total_ex_factory_val","total_short_access_qnty","total_over_access_qnty","value_total_short_access_value","value_total_over_access_value"],
			col: [20,23,25,27,28,29,30,31,32,33,34,35],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
		
	}



	function new_window()
	{
		//document.getElementById('approval_div').style.overflow="auto";
		//document.getElementById('approval_div').style.maxHeight="none";		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();		
		//document.getElementById('approval_div').style.overflowY="scroll";
		//document.getElementById('approval_div').style.maxHeight="380px";
	}	
	
	/*function progress_comment_popup(job_no,po_id,template_id,tna_process_type)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/shipment_date_wise_wp_report_controller.php?job_no='+job_no+'&po_id='+po_id+'&template_id='+template_id+'&tna_process_type='+tna_process_type+'&action=update_tna_progress_comment'+'&permission='+permission, "TNA Progress Comment", 'width=1030px,height=390px,center=1,resize=1,scrolling=0','../../');
	}*/
	
	function progress_comment_popup(job_no,po_id,template_id,tna_process_type)
	{
		var data="action=update_tna_progress_comment"+
								'&job_no='+"'"+job_no+"'"+
								'&po_id='+"'"+po_id+"'"+
								'&template_id='+"'"+template_id+"'"+
								'&tna_process_type='+"'"+tna_process_type+"'"+
								'&permission='+"'"+permission+"'";	
								
		http.open("POST","requires/shipment_date_wise_wp_report_controller.php",true);
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_progress_comment_reponse;	
	}
	
	function generate_progress_comment_reponse()
	{
		if(http.readyState == 4) 
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
			d.close();
		}
	}
	
	function openmypage_file(action,job_no,type)
	{
		var page_link='requires/capacity_and_order_booking_status_report_v2_controller.php?action='+action+'&job_no='+job_no+'&type='+type
		var title="File View";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=300px,center=1,resize=0,scrolling=0','../../')
	}
	
	function set_defult_date(companyId)
	{
		var defult_date=return_global_ajax_value(companyId, 'get_defult_date', '', 'requires/capacity_and_order_booking_status_report_v2_controller');
		document.getElementById('cbo_category_by').value=trim(defult_date);
		//alert(defult_date);		
	}

	function generate_ex_factory_popup(action,job_no,id,width)
	{
		//alert(job_no); 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/capacity_and_order_booking_status_report_v2_controller.php?action='+action+'&job_no='+job_no+'&id='+id, 'Ex-Factory Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}

    function fn_check_uncheck(){
        var lengths = $("[type=checkbox]").length;
        if($("#check_uncheck").is(":checked") != true){     
            for(var i=0; i<=lengths; i++){
                
                $("[type=checkbox]").prop('checked', false);
                $("[type=checkbox]").removeClass('rpt_check');
                $("[type=checkbox]").removeAttr('checked');
            }
        }else{
            $("[type=checkbox]").prop('checked', true);
            for(var i=0; i<=lengths; i++){
                
                $("[type=checkbox]").not("#check_uncheck").addClass('rpt_check');
                $("[type=checkbox]").attr('checked',"checked");
            }
        }    
    }

	function load_season()
	{  
		var buyer=$("#cbo_buyer_name").val();
		load_drop_down( 'requires/capacity_and_order_booking_status_report_v2_controller', buyer, 'load_drop_down_season_buyer', 'season_td' );
	}
	
	function fnc_get_company_config(company_id)
	{
		$('#cbo_style_owner').val( company_id );

		get_php_form_data(company_id,'get_company_config','requires/capacity_and_order_booking_status_report_v2_controller' );
		set_multiselect('cbo_buyer_name','0','0','0','0'); 
		setTimeout[($("#buyer_td a").attr("onclick","disappear_list(cbo_buyer_name,'0');getSeasonId();") ,1000)];
		
		/*$('#multi_select_cbo_buyer_name a').click(function(){
			load_season();
		});*/
	}
	
	function fnc_get_style_owner_config(style_owner_id)
	{
		get_php_form_data(style_owner_id,'style_owner_config','requires/capacity_and_order_booking_status_report_v2_controller' );
		set_multiselect('cbo_buyer_name','0','0','','0','');//load_buyer_location();
		setTimeout[($("#buyer_td a").attr("onclick","disappear_list(cbo_buyer_name,'0');getSeasonId();") ,1000)];
	}

	function getSeasonId() 
	{
		var buyer=$("#cbo_buyer_name").val();
		
		if(buyer !='') {
		  var data="action=load_drop_down_season_buyer&data="+buyer;
		  http.open("POST","requires/capacity_and_order_booking_status_report_v2_controller.php",true);
		  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		  http.send(data); 
		  http.onreadystatechange = function(){
			  if(http.readyState == 4) 
			  {
				  var response = trim(http.responseText);
				  $('#season_td').html(response);
			  }          
		  };
		}     
	}


</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../../");  ?>
        <form name="capacityOrderBooking_1" id="capacityOrderBooking_1" autocomplete="off" > 
        <h3 style="width:1635px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div id="content_search_panel" style="width:1630px" >      
			<fieldset>
                <table align="center" class="rpt_table" width="1630" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th class="">Company</th>
                            <th class="">Location</th>
                            <th class="">Style Owner </th>
                            <th>Buyer</th>
							<th>Season</th>
                            <th>Agent</th>
                            <th>Style</th>
                            <th>Job No</th>
                            <th>Internal Ref</th>
                            <th>Job Year</th>
                            <th>Team</th>
                            <th>Dealing Merchant</th>
                            <th>Factory Merchant</th>
                            <th>Product Category</th>
                            <th class="must_entry_caption" colspan="2">Date</th>
                            <th>Date Category</th>
                            <th>Capacity Source</th>
                            <th><input type="reset" name="reset" id="reset" value="Reset" style="width:50px" class="formbutton" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
                            <?
                                echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_get_company_config(this.value);" );
								/* load_drop_down( 'requires/capacity_and_order_booking_status_report_v2_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/capacity_and_order_booking_status_report_v2_controller', this.value, 'load_drop_down_agent', 'agent_td');load_drop_down( 'requires/capacity_and_order_booking_status_report_v2_controller', this.value, 'load_drop_down_location', 'location_td' );set_defult_date(this.value);;set_multiselect('cbo_buyer_name','0','0','0','0');$('#multi_select_cbo_buyer_name a').click(function(){
									load_season();
								 });*/
                            ?> 
                        </td>
                         <td id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location_id", 100, $blank_array,"", 1, "-- Select Location --", $selected, "" );
                            ?>	
                        </td>
                         <td>
                            <?
                                echo create_drop_down( "cbo_style_owner", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_get_style_owner_config(this.value);" );//load_drop_down( 'requires/capacity_and_order_booking_status_report_v2_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/capacity_and_order_booking_status_report_v2_controller', this.value, 'load_drop_down_agent', 'agent_td'); set_defult_date(this.value);set_multiselect('cbo_buyer_name','0','0','','0','load_buyer_location();');
                            ?> 
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 0, "-- Select Buyer --", $selected, "" );
                            ?>	
                        </td>
						<td id="season_td"><? echo create_drop_down( "cbo_season_name", 140, $blank_array,"", 1, "-- Select Season --", $selected, "" ); ?></td>
                        <td id="agent_td">
                            <? 
                                echo create_drop_down( "cbo_agent", 130, $blank_array,"", 1, "-- Select Agent --", $selected, "" );
                            ?>	
                        </td>
                        <td align="center">
                            <input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:70px" />
                        </td>
                        <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:80px" onDblClick="openmypage_job();" placeholder="Wr./Br. Job" />
                            <input type="hidden" id="txt_job_id" name="txt_job_id"/>
                        </td>
                         <td align="center">
                            <input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px" />
                        </td>
                        <td>
                            <? echo create_drop_down( "cbo_year", 50, create_year_array(),"", 1,"-- All --",0 , "",0,"" );//date("Y",time()) ?>	
                        </td>
                        <td >
                            <?
                                echo create_drop_down( "cbo_team_name", 80, "select id,team_name from lib_marketing_team  where status_active =1 and is_deleted=0  order by team_name","id,team_name", 1, "-- Select Team --", $selected, " load_drop_down( '../merchandising_report/requires/capacity_and_order_booking_status_report_v2_controller', this.value, 'load_drop_down_team_member', 'team_td' );load_drop_down( '../merchandising_report/requires/capacity_and_order_booking_status_report_v2_controller', this.value, 'cbo_factory_merchant', 'div_marchant_factory' ) " );
                            ?>
                        </td>
                        <td id="team_td">
                        <div id="div_team">
                            <? 
                                echo create_drop_down( "cbo_team_member", 100, "select id,team_member_name from lib_mkt_team_member_info where  status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "- Select Dealing Merchant- ", $selected, "" );
								//echo create_drop_down( "cbo_dealing_merchant", 140, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
                            ?>	
                            
                        </div>
                        </td>
                        
                        
                        <td id="div_marchant_factory" > 
                            <? 
                                echo create_drop_down( "cbo_factory_merchant", 100, "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select --", $selected, "" );
                            ?>	
                        </td>
                        
                        <td> 
                            <? 
								echo create_drop_down( "cbo_product_category", 100, $product_category,"", 1, "-- Select --", $selected, ""  );
                            ?>	
                        </td>
                        
                        <td><input name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:50px" placeholder="From Date"></td>
                        <td><input name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:50px" placeholder="To Date"></td>
                        <td>
                        <? 
								$report_po_date = array(1 => "Country Ship Date Wise", 2 => "Ship Date Wise", 3 => "Original Ship Date", 4 => "PO Insert Date Wise");
                                echo create_drop_down( "cbo_category_by", 80, $report_po_date,"", 0, "--Select--", $selected,"" );
                            ?>	
                            <!--<select name="cbo_category_by" id="cbo_category_by"  style="width:100px" class="combo_boxes">
                                <option value="1">Country Ship Date Wise </option>
                                <option value="2" selected>Pub Ship Date Wise </option>
                            </select>-->
                        </td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_capacity_source",90,$knitting_source,"", 1, "--All--", $selected, "","","1,3","","","","");
                            ?>
                        </td>
                        <td>
                      
						<input type="button" name="show" id="show" value="Show" onClick="generate_report('report_container2','report_generate',3)" style="width:50px;" class="formbutton" /> 
						<input type="button" name="show" id="show" value="Summary" onClick="generate_report('report_container2','report_generate_summaery',3)" style="width:50px;" class="formbutton" /> 
                        <input name="fillter_check" id="fillter_check" type="hidden" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="16" align="center"><? echo load_month_buttons(1); ?></td>
						
                    </tr>
                </table>
				<table align="left">
                        <tr id="check_uncheck_tr" style="display:none;">
                            <td><input type="checkbox" id="check_uncheck" name="check_uncheck" onClick="fn_check_uncheck()"/> <strong style="color:#176aaa; font-size:14px; font-weight:bold;">Check/Uncheck All</strong>
                            </td>
                        </tr>
                    </table>                    
                    <br />

            </fieldset>
        </div>
        <div id="report_container" align="center" style="padding:5px 0;"></div>
        <div id="report_container2"></div>
        </form>
    </div>
</body>
<script type="text/javascript">
	set_multiselect('cbo_buyer_name','0','0','0','0');
	setTimeout[($("#buyer_td a").attr("onclick","disappear_list(cbo_buyer_name,'0');getSeasonId();") ,1000)];
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

