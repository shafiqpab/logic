<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Time and Weight Record
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	27-10-2018	
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
echo load_html_head_contents("Time and Weight Record","../../", 1, 1, $unicode,1,'');
?>	
<script>
	
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	// Master Form-----------------------------------------------------------------------------

	var mandatory_field_arr="";

	<?
	echo "var mandatory_field = '". implode('*',$_SESSION['logic_erp']['mandatory_field'][245]) . "';\n";
	echo "var field_message = '". implode('*',$_SESSION['logic_erp']['field_message'][245]) . "';\n";

	
	if($_SESSION['logic_erp']['mandatory_field'][245]!="")
	{
		$mandatory_field_arr= json_encode( $_SESSION['logic_erp']['mandatory_field'][245] );
		echo "var mandatory_field_arr= ". $mandatory_field_arr . ";\n";
	}
	?>
	
	var str_size = [<? echo substr(return_library_autocomplete( "select size_name from  lib_size where status_active=1 and is_deleted=0 order by size_name ASC", "size_name" ), 0, -1); ?>];
	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color where status_active=1 and is_deleted=0 order by color_name ASC", "color_name" ), 0, -1); ?>];
	
	function set_auto_complete(type)
	{
		if(type=='color_return')
		{
			$("#txt_sample_color").autocomplete({
				source: str_color
			});
		}
		
		if(type=='size_return')
		{
			$("#txt_sample_size").autocomplete({
				source: str_size
			});
		}
	}

	function location_select()
	{
		if($('#cbo_location_name option').length==2)
		{
			if($('#cbo_location_name option:first').val()==0)
			{
				$('#cbo_location_name').val($('#cbo_location_name option:last').val());
				//eval($('#cbo_location_name').attr('onchange')); 
			}
		}
		else if($('#cbo_location_name option').length==1)
		{
			$('#cbo_location_name').val($('#cbo_location_name option:last').val());
			//eval($('#cbo_location_name').attr('onchange'));
		}	
	}
	
	
	function color_select_popup(id)
	{
		var buyer_name=$('#cbo_buyer_name').val();
		//var page_link='requires/sample_booking_non_order_controller.php?action=color_popup'
		//alert(texbox_id)
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/time_weight_record_controller.php?action=color_popup&buyer_name='+buyer_name, 'Color Select Pop Up', 'width=250px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var color_name=this.contentDoc.getElementById("color_name");
			if (color_name.value!="")
			{
				$('#txtColorName_'+id).val(color_name.value);
				append_color_size_row(1);
			}
		}
	}
	
	function sub_dept_load(cbo_buyer_name,cbo_product_department)
	{
		if(cbo_buyer_name ==0 || cbo_product_department==0 )
		{
			return;
		}
		else
		{
			load_drop_down( 'requires/time_weight_record_controller',cbo_buyer_name+'_'+cbo_product_department, 'load_drop_down_sub_dep', 'sub_td' );
		}
	}
 
	function fnc_time_weight_record_entry( operation )
	{
		freeze_window(operation);
		if (form_validation('cbo_company_name*cbo_location_name*cbo_buyer_name*txt_style_ref*cbo_product_department*cbo_item_catgory*cbo_team_leader*cbo_dealing_merchant*cbo_sample_type*cbo_kniting_uom*txt_sample_color*txt_sample_size*txtknitingweight_dzn','Company Name*Location Name*Buyer Name*Style Ref*Product Department*Product Catagory*Team Leader*Dealing Merchant*Style ID*Sample Type*Knitting Weight UOM*Sample Color*Sample Size*Knitting Weight per Dzn')==false)
		{
			release_freezing();
			return;
		}	
		else
		{
			if(mandatory_field){
				if (form_validation(mandatory_field,field_message)==false)
				{
					release_freezing();
					return;
				}
			}
			var panel_tr =$('#tbl_panel tbody tr').length; 
			
			var data_mst=get_submitted_data_string('txt_style_no*garments_nature*cbo_company_name*cbo_location_name*cbo_buyer_name*txt_style_ref*cbo_product_department*txt_product_code*cbo_gmts_item*cbo_agent*cbo_region*cbo_item_catgory*cbo_team_leader*cbo_dealing_merchant*txt_remarks*cbo_ready_to_approved*update_id*cbo_season_id*txt_bh_merchant*txt_artical_no*txt_est_ship_date*txt_sample_date*txt_gause*txt_efficiency*cbo_sample_type*cbo_dev_no*txt_sample_color*txt_sample_size*txt_designer*txt_asst_tech_manager*txt_programmer*txt_yarn_quality*txt_count_ply*txtminute_tot*txtsecond_tot*txtmovingsec_tot*txtknitinggm_tot*txtcritical_point*txtknitingweight_dzn*updatedtls_id*cbo_brand_id*cbo_season_year*txt_knitting_system*txt_machine_brand_name*txt_mers_style*cbo_kniting_uom',"../../");
			
			var data_panel="";
			for(var i=1; i<=panel_tr; i++)
			{
				//data_panel+=get_submitted_data_string('txtpanelupid_'+i+'*txtpanelid_'+i+'*txtminute_'+i+'*txtsecond_'+i+'*txtmovingsec_'+i+'*txtknitinggm_'+i,"../../",2);
				data_panel += '&txtpanelupid_' + i + '=' + trim($('#txtpanelupid_'+i).val()) + '&txtpanelid_' + i + '=' + trim($('#txtpanelid_'+i).val())+ '&txtminute_' + i + '=' + trim($('#txtminute_'+i).val()) + '&txtsecond_' + i + '=' + trim($('#txtsecond_'+i).val()) + '&txtmovingsec_' + i + '=' + trim($('#txtmovingsec_'+i).val()) + '&txtknitinggm_' + i + '=' + trim($('#txtknitinggm_'+i).val()); 
			}
			
			var data="action=save_update_delete_mst&operation="+operation+"&data_mst="+data_mst+"&panel_tr="+panel_tr+"&data_panel="+data_panel;
			
			//alert(data); return;
			http.open("POST","requires/time_weight_record_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_time_weight_record_reponse;
		}
	}
	
	function fnc_time_weight_record_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			
			if(trim(reponse[0])=='budget'){
				alert("Style ID Already Used. Job  NO :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
				release_freezing();
				return;
			}
			show_msg(trim(reponse[0]));
			if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1)
			{
				document.getElementById('txt_style_no').value=reponse[1];
				document.getElementById('update_id').value=reponse[2];
				document.getElementById('updatedtls_id').value=reponse[3];
				set_button_status(1, permission, 'fnc_time_weight_record_entry',1);
				fnc_color_bodypart_dtls();
			}
			if(parseInt(trim(reponse[0]))==2)
			{
				location.reload();
			}
			
			release_freezing();
		}
	}
	
	function openmypage_style_no(page_link,title,type)
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;   
		}
		else
		{
			var cbo_company_name=$('#cbo_company_name').val();
			var cbo_buyer_name=$('#cbo_buyer_name').val();
			var garments_nature=document.getElementById('garments_nature').value;
			page_link=page_link+'&garments_nature='+garments_nature+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=430px,center=1,resize=0,scrolling=0','../')
			release_freezing();
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("selected_id");
				if (theemail.value!="")
				{
					freeze_window(5);
					//alert(type);
					if(type==1)
					{
					get_php_form_data(theemail.value, "populate_data_from_search_popup", "requires/time_weight_record_controller" );
					set_button_status(1, permission, 'fnc_time_weight_record_entry',1);
					fnc_color_bodypart_dtls();
					}
					else
					{
						get_php_form_data(theemail.value, "populate_data_from_order_popup", "requires/time_weight_record_controller" );
						//
						$('#txt_style_ref').attr('readonly',true);
					}
					release_freezing();
				}
			}
		}
	}
	// Master Form End -----------------------------------------------------------------------------
	
	function fnc_color_dtls(operation)
	{
		freeze_window(operation);
		if($('#txt_style_no').val()=="")
		{
			alert("Save Master Part (Style Info)")
			release_freezing();
			return;
		}
		
		if($('#updatedtls_id').val()=="")
		{
			alert("Save Details Part (Sample Details Entry)")
			release_freezing();
			return;
		}

		var j=14;  var txtbodyc=$('#txtbodyc').val()*1;
		//alert(txtbodyc);return;		
		var color_data=''; var color_body_data='';

		var panel_tr =$('#tbl_panel tbody tr').length;
		var txtknitinggm = [];
		for(var i=1; i<=panel_tr; i++)
		{
			//txtknitinggm=$('#txtknitinggm_'+i).val()*1;
			var data = $('#txtknitinggm_'+i).val()*1;
			if(data!=0){
				txtknitinggm.push(data);
			}
			
		}
		//console.log(txtknitinggm);
		for(var k=0; k<=txtknitinggm.length; k++)
		{
			/*for(var m=1; m<=txtbodyc; m++)
			{
				color_body_datas=$("#txtbodycolor_"+k+"_"+m).val();				
			}*/
			var m = k+1;
			var body_color_data = $('#txtbodytot_'+m).attr('total_value')*1;
			//alert(txtknitinggm[k]);return;
			if(body_color_data>txtknitinggm[k] || body_color_data<txtknitinggm[k])
			//if((body_color_data*1)!=(txtknitinggm[k]*1))
			{
				alert("Data is Greater or Less than Knitting Weight.");//+(body_color_data*1)+'_'+(txtknitinggm[k]*1)
				release_freezing();
				return;
			}
		}	
		//alert(color_body_datas);return;

		for(var k=1; k<=j; k++)
		{
			var bodypartid_data="";
			color_data+=get_submitted_data_string('txtcolor_'+k+'*txtbodycolorval_'+k+'*txtbodycolorper_'+k,"../../",3);
			for(var m=1; m<=txtbodyc; m++)
			{
				color_body_data+=get_submitted_data_string('txtbodycolor_'+k+'_'+m,"../../",3);
				
				bodypartid_data+=get_submitted_data_string('bodypartid_'+m,"../../",3);
			}
		}
		var data_mst=get_submitted_data_string('txtbodyc*update_id*updatedtls_id',"../../",3);
		var data="action=save_update_delete_colordtls&operation="+operation+"&data_mst="+data_mst+"&color_data="+color_data+"&bodypartid_data="+bodypartid_data+"&color_body_data="+color_body_data;
	
		//alert (color_body_data); return;
		
		http.open("POST","requires/time_weight_record_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_color_dtls_reponse;
	}
	
	function fnc_color_dtls_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=http.responseText.split('**');
			if(trim(reponse[0])=='budget'){
				alert("Style ID Already Used. Job  NO :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
				release_freezing();
				return;
			}
			show_msg(trim(reponse[0]));
			if(reponse[0]==0 || reponse[0]==1)
			{
				//document.getElementById('updatedtls_id').value=reponse[1];
				set_button_status(1, permission, 'fnc_color_dtls',3);
			}
			if(parseInt(trim(reponse[0]))==2)
			{
				fnc_color_bodypart_dtls();
			}
			release_freezing();
		}
	}
	
	function fnc_panel_sum()
	{
		var panel_tr =$('#tbl_panel tbody tr').length; 
		var tot_minute=0; var tot_second=0; var moving_speed=0; var knitting_weight_gm=0; var k=0;
		if($('#cbo_kniting_uom').val()==0)
		{
			alert("Please Select Knitting Weight UOM");	
			$( "#cbo_kniting_uom" ).focus();
			return;
		}
		$('#cbo_kniting_uom').attr('disabled',true);
		for(var i=1; i<=panel_tr; i++)
		{
			var txtminute=0; var txtsecond=0; var txtmovingsec=0; var txtknitinggm=0;
			
			txtminute=$('#txtminute_'+i).val()*1;
			txtsecond=$('#txtsecond_'+i).val()*1;
			if(($('#txtmovingsec_'+i).val()*1)!=0) { txtmovingsec=$('#txtmovingsec_'+i).val()*1; k++; }
			txtknitinggm=$('#txtknitinggm_'+i).val()*1;
			
			tot_minute+=txtminute;
			tot_second+=txtsecond;
			moving_speed+=txtmovingsec;
			knitting_weight_gm+=txtknitinggm;
		}
		var tsec=number_format((tot_minute+(tot_second/60)),4,'.','');
		
		$('#txtminute_tot').val( tsec );//number_format(min_sec_convert,2)
		//$('#txtsecond_tot').val(tot_second);
		var avg_moving_speed=(moving_speed/k);
		$('#txtmovingsec_tot').val(number_format(avg_moving_speed,4,'.',''));
		$('#txtknitinggm_tot').val( number_format(knitting_weight_gm,4,'.','') );
		if($('#cbo_kniting_uom').val()==11)//GM
		{
			var knitingweight_dzn=(((knitting_weight_gm/1000)*2.2046)*12);
		}
		else if($('#cbo_kniting_uom').val()==15)//LBS
		{
			var knitingweight_dzn=knitting_weight_gm*12;
		}
		$('#txtknitingweight_dzn').val( number_format(knitingweight_dzn,4,'.','') );
	}
	
	function openmypage_special_comments(title)
	{
		var data=document.getElementById('txtcritical_point').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/time_weight_record_controller.php?data='+data+'&action=special_comments',title, 'width=420px,height=320px,center=1,resize=1,scrolling=0','../')
		
		emailwindow.onclose=function()
		{
			var critical_pointknitting=this.contentDoc.getElementById("txtcritical_pointknitting").value;
			var critical_pointlinking=this.contentDoc.getElementById("txtcritical_pointlinking").value;
			var critical_pointwashing=this.contentDoc.getElementById("txtcritical_pointwashing").value;
			var critical_pointaddons=this.contentDoc.getElementById("txtcritical_pointaddons").value;
			var critical_pointfinishing=this.contentDoc.getElementById("txtcritical_pointfinishing").value;
			
			var str_ponit=critical_pointknitting+'___'+critical_pointlinking+'___'+critical_pointwashing+'___'+critical_pointaddons+'___'+critical_pointfinishing;
			if (str_ponit!="")
			{
				$('#txtcritical_point').val(str_ponit);
			}
		}
	}
	
	function fnc_color_bodypart_dtls()
	{
		show_list_view( $('#update_id').val()+'_'+$('#updatedtls_id').val(),"color_body_part_dtls_list",'color_body_part_dtls','requires/time_weight_record_controller','');
		fnc_tot_per_cal();
	}
	
	function fnc_tot_per_cal()
	{
		var txtbodyc=$('#txtbodyc').val()*1;
		
		var j=14; var gtot=0;
		for(var k=1; k<=j; k++)
		{
			var color_tot=0;
			for(var m=1; m<=txtbodyc; m++)
			{
				color_tot+=$('#txtbodycolor_'+k+'_'+m).val()*1;
			}
			$('#txtbodycolorval_'+k).val( number_format(color_tot,4,'.','') );
			gtot+=color_tot;
		}
		$('#txtcolortot').val( number_format(gtot,4,'.','') );
		
		var per_tot=0;
		for(var k=1; k<=j; k++)
		{
			var color_row_tot=$('#txtbodycolorval_'+k).val()*1;
			var color_per=(color_row_tot*100)/gtot;
			$('#txtbodycolorper_'+k).val( number_format( color_per,4,'.','') );
			per_tot+=color_per;
		}
		$('#txttotper').val( number_format(per_tot,4,'.','') );
		
		for(var m=1; m<=txtbodyc; m++)
		{
			var color_sum=0;
			for(var k=1; k<=j; k++)
			{
				color_sum+=$('#txtbodycolor_'+k+'_'+m).val()*1;
			}
			//$('#txtbodytot_'+m).val( color_sum );
			document.getElementById('txtbodytot_'+m).value = number_format( color_sum,4,'.','');
			$('#txtbodytot_'+m).attr('total_value',number_format(color_sum,4,'.',''));
		}
	}
	
	function generate_report(action)
	{
		if($('#txt_style_no').val()=="")
		{
			alert("Save Master Part (Style Info)")
			return;
		}
		
		if($('#updatedtls_id').val()=="")
		{
			alert("Save Details Part (Sample Details Entry)")
			return;
		}
			
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_style_no').val()+'*'+report_title, action,'requires/time_weight_record_controller');
		show_msg("3");
	}
	
	function generate_report_file(data,action,page)
	{
		window.open("requires/time_weight_record_controller.php?data=" + data+'&action='+action, true );
	}
	
	function fnc_variable_settings_check(company_id)
	{
		var all_variable_settings=return_ajax_request_value(company_id, 'load_variable_settings', 'requires/time_weight_record_controller');
		var ex_variable=all_variable_settings.split("_");
		
		var color_from_lib=ex_variable[9];
		
		if(color_from_lib==1)
		{
			$('#hidd_color_from_lib').val( color_from_lib );
			$('#txt_sample_color').attr('readonly',true);
			$('#txt_sample_color').attr('placeholder','Browse');
			$('#txt_sample_color').removeAttr("onDblClick").attr("onDblClick","color_select_popup("+1+")");
		}
		else 
		{
			$('#hidd_color_from_lib').val( 2 );
			$('#txt_sample_color').attr('readonly',false);
			$('#txt_sample_color').attr('placeholder','Write');
			$('#txt_sample_color').removeAttr('onDblClick','onDblClick');	
		}
	}
	
	function color_select_popup()
	{
		var buyer_name=$('#cbo_buyer_name').val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/time_weight_record_controller.php?action=color_popup&buyer_name='+buyer_name, 'Color Select Pop Up', 'width=250px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var color_name=this.contentDoc.getElementById("color_name");
			if (color_name.value!="")
			{
				$('#txt_sample_color').val(color_name.value);
			}
		}
	}
	
	function fnc_uomchnage(uomval)
	{
		if(uomval==11)//GM
		{
			$('#tduom').text("Knitting Weight [GM]");
		}
		else if (uomval==15)//LBS
		{
			$('#tduom').text("Knitting Weight [LBS]");
		}
		else
		{
			$('#tduom').text("");
		}
	}
	
	function fnc_copy_weightrecord(operation)
	{
		freeze_window(operation);
		if (form_validation('cbo_company_name*cbo_location_name*cbo_buyer_name*txt_style_ref','Company*Location*Buyer*Style Ref')==false)
		{
			release_freezing();
			return;
		}	
		else
		{
			var data="action=copy_weightrecord&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_sample_type*cbo_dev_no*update_id*updatedtls_id',"../../");
			http.open("POST","requires/time_weight_record_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = copy_weightrecord_reponse;
		}
	}
	
	function copy_weightrecord_reponse()
	{
		if(http.readyState == 4){
			var reponse=trim(http.responseText).split('**');
			show_msg(trim(reponse[0]));
			
			if(parseInt(trim(reponse[0]))==36)
			{
				document.getElementById('txt_style_no').value=reponse[1];
				document.getElementById('update_id').value=reponse[2];
				document.getElementById('updatedtls_id').value=reponse[3];
				$('#txt_style_ref').attr('disabled',false);
				set_button_status(1, permission, 'fnc_time_weight_record_entry',1);
				fnc_color_bodypart_dtls();
			}
			release_freezing();
		}
	}
	
</script>
</head>
<body onLoad="set_hotkey(); set_auto_complete('color_return'); set_auto_complete('size_return');">
<div style="width:100%;" align="center">
<!-- Important Field outside Form -->  
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <table width="100%" cellpadding="0" cellspacing="2" align="center" >
        <tr>
            <td valign="top" width="850">
            <h3 style="width:840px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Style Info</h3> 
         	<div id="content_search_panel" style="width:840px">
            <fieldset style="width:840px;">
                <form name="timeweightentry_1" id="timeweightentry_1" autocomplete="off">
                <table width="840" cellspacing="2" cellpadding="0" border="0">
                	<tr>
                    	<td colspan="3" align="right"><strong>Style ID</strong></td> 
                        <td colspan="3">
                        	<input style="width:140px;" type="text" title="Double Click to Search" onDblClick="openmypage_style_no('requires/time_weight_record_controller.php?action=style_popup','Style Selection Form',1)" class="text_boxes" placeholder="Browse Style" name="txt_style_no" id="txt_style_no" readonly />
                            <input type="hidden" id="update_id"> 
                            <input type="hidden" name="hidd_color_from_lib" id="hidd_color_from_lib" style="width:30px;" class="text_boxes" />
                        </td>
                    </tr>
                    <tr>
                    	<td width="105" class="must_entry_caption">Company</td>
                        <td width="165"><? echo create_drop_down( "cbo_company_name", 150, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/time_weight_record_controller', this.value, 'load_drop_down_location', 'location'); load_drop_down( 'requires/time_weight_record_controller', this.value, 'load_drop_down_buyer', 'buyer_td'); load_drop_down( 'requires/time_weight_record_controller', this.value, 'load_drop_down_agent', 'agent_td'); location_select(); fnc_variable_settings_check(this.value);"); ?></td>
                        <td width="115" class="must_entry_caption">Location</td>
                        <td width="165" id="location"><? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "--Select--", $selected, "" ); ?></td>
                        <td width="105" class="must_entry_caption">Buyer</td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "--Select Buyer--", $selected, "" ); ?></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Style/Master Ref.</td>
                        <td><input class="text_boxes" type="text" style="width:140px" placeholder="Write/Browse" name="txt_style_ref" id="txt_style_ref" onDblClick="openmypage_style_no('requires/time_weight_record_controller.php?action=style_ref_popup','Style Selection Form',2)" /></td>
                        <td class="must_entry_caption">Prod. Dept.</td>
                        <td><? echo create_drop_down( "cbo_product_department", 92, $product_dept, "", 1, "-Select-", $selected, "", "", "" ); ?>
                        	<input class="text_boxes" type="text" style="width:40px;" name="txt_product_code" id="txt_product_code" maxlength="10" title="Maximum 10 Character" />
                        </td>
                        <td>Garments Item</td>
                        <td><? echo create_drop_down( "cbo_gmts_item", 150, get_garments_item_array(100),"", 1, "--Gmts Item--", $selected, "" ); ?></td>
                    </tr>
                    <tr>
                    	<td class="">Mers. Style</td>
                        <td><input class="text_boxes" type="text" style="width:140px" placeholder="Write" name="txt_mers_style" id="txt_mers_style"/></td>
                    	
                        <td class="must_entry_caption">Product Category</td>
                        <td><?=create_drop_down( "cbo_item_catgory", 150, $product_category,"", 1, "--Select Product Category--", 3, "","","" ); ?></td>
                        <td>Region</td>
                        <td><?=create_drop_down( "cbo_region", 150, $region, 1, "-- Select Region --", $selected, "" ); ?></td>
                    </tr>
                    <tr>
                        <td>Agent </td>
                        <td id="agent_td"><? echo create_drop_down( "cbo_agent", 150, $blank_array,"", 1, "-- Select Agent --", $selected, "" ); ?></td>
                        <td>Season<input type="hidden" name="is_season_must" id="is_season_must" style="width:50px;" class="text_boxes" />&nbsp;&nbsp;&nbsp;<?=create_drop_down( "cbo_season_year", 60, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
                        <td id="season_td"><? echo create_drop_down( "cbo_season_id", 150, $blank_array,'', 1, "-- Select Season--",$selected, "" ); ?></td>
                        <td>Gauge & No. Ends</td>
                        <td><input class="text_boxes" type="text" style="width:140px;" name="txt_gause" id="txt_gause"/></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Team Leader</td>   
                        <td id="leader_td"><? echo create_drop_down( "cbo_team_leader", 150, "select id,team_leader_name from lib_marketing_team where project_type=6 and status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "load_drop_down( 'requires/time_weight_record_controller', this.value, 'cbo_dealing_merchant', 'div_marchant' );" ); ?></td>
                        <td class="must_entry_caption">Dealing Merchant</td>   
                        <td id="div_marchant"><? echo create_drop_down( "cbo_dealing_merchant", 150, $blank_array,"", 1, "-- Select Team Member --", $selected, "" ); ?></td>
                        <td>BH Merchant</td>   
                        <td><input type="text" name="txt_bh_merchant" id="txt_bh_merchant" style="width:140px;" class="text_boxes" /></td>
                    </tr>
                    <tr>
                    	<td>Est. Ship Date</td>
                        <td><input class="datepicker" type="text" style="width:140px;" name="txt_est_ship_date" id="txt_est_ship_date"/></td>
                        <td>Sample Date</td>
                        <td><input class="datepicker" type="text" style="width:140px;" name="txt_sample_date" id="txt_sample_date"/></td>
                        <td>Efficiency %</td>
                        <td><input class="text_boxes_numeric" type="text" style="width:140px;" name="txt_efficiency" id="txt_efficiency"/></td>
                    </tr>
                    <tr>
                    	<td>Article No.</td>
                        <td><input class="text_boxes" type="text" style="width:140px" name="txt_artical_no" id="txt_artical_no" /></td>
                        <td>Brand</td>
                        <td id="brand_td" ><? echo create_drop_down( "cbo_brand_id", 150, $blank_array,'', 1, "--Brand--",$selected, "" ); ?></td>
                        <td class="must_entry_caption">Knitting Weight UOM</td>
                        <td><?=create_drop_down( "cbo_kniting_uom",150, $unit_of_measurement, "",0, "", 1, "fnc_uomchnage(this.value);","","11,15" ); ?></td>
                    </tr>
                    <tr>
                    	<td>Remarks</td>
                        <td colspan="3"><input class="text_boxes" type="text" style="width:415px;" name="txt_remarks" id="txt_remarks"/></td>
                        <td>Ready To Approved</td>
                    	<td><?=create_drop_down( "cbo_ready_to_approved", 150, $yes_no,"", 1, "-- Select--", 2, "","","" ); ?></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td><input type="button" class="image_uploader" style="width:150px" value="ADD/FILE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'time_weight_entry', 0 ,1)"></td>
                        <td>&nbsp;</td>
                        <td><input type="button" class="image_uploader" style="width:150px" value="FRONT IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'front_time_weight', 0 ,1)"></td>
                        <td>&nbsp;</td>
                        <td><input type="button" id="set_button" class="image_uploader" style="width:150px;" value="BACK IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'back_time_weight', 0 ,1)"></td>
                    </tr>
                    <tr>
                    	<td colspan="6" id="budgetApp_td" style="font-size:18px; color:#F00" align="center"></td>
                    </tr>
                </table>
                </form>
            </fieldset>
            </div>
            </td>
        </tr>
    </table>
    <div align="left">
        <fieldset style="width:850px;">
        <legend>Sample Details Entry</legend>
            <form id="timeweightentry_2" autocomplete="off">
                <table width="820" cellspacing="2" cellpadding="0" border="0">
                	 <tr>
                    	<td width="120" class="must_entry_caption">Sample Name</td>
                        <td width="100"><? echo create_drop_down( "cbo_sample_type", 100, "select sample_name,id from lib_sample where business_nature=100 and is_deleted=0 and status_active=1 order by sample_name","id,sample_name", '1', "--Select--", '', "",'','' ); ?></td>
                        <td width="120">&nbsp;&nbsp;Development No.</td> 
                        <td width="100"><? echo create_drop_down( "cbo_dev_no", 100, $development_no,"", '1', "--Select--", '', "",'','' ); ?></td>
                        <td width="120" class="must_entry_caption">&nbsp;&nbsp;Sample Color</td>
                        <td width="100"><input style="width:90px;" type="text" class="text_boxes" name="txt_sample_color" id="txt_sample_color" /></td>
                        <td width="120" class="must_entry_caption">&nbsp;&nbsp;Sample Size</td>
                        <td><input style="width:90px;" type="text" class="text_boxes" name="txt_sample_size" id="txt_sample_size" /><input type="hidden" id="updatedtls_id"></td>
                    </tr>
                    <tr>
                    	<td>Designer</td>
                        <td><input style="width:90px;" type="text" class="text_boxes" name="txt_designer" id="txt_designer" /></td>
                        <td>&nbsp;&nbsp;Asst. Tech. Manager</td> 
                        <td><input style="width:90px;" type="text" class="text_boxes" name="txt_asst_tech_manager" id="txt_asst_tech_manager" /></td>
                        <td>&nbsp;&nbsp;Programmer</td>
                        <td><input style="width:90px;" type="text" class="text_boxes" name="txt_programmer" id="txt_programmer" /></td>
                        <td>&nbsp;&nbsp;Knitting System</td>
                        <td><? echo create_drop_down( "txt_knitting_system", 100, $knitting_system_arr,"", '1', "--Select--", '', "",'','' ); ?>
                    </tr>
                    <tr>
                    	<td>Yarn Quality</td>
                        <td colspan="3"><input style="width:310px;" type="text" class="text_boxes" name="txt_yarn_quality" id="txt_yarn_quality" /></td>
                        <td>&nbsp;&nbsp;Yarn Count & Ply</td>
                        <td><input style="width:90px;" type="text" class="text_boxes" name="txt_count_ply" id="txt_count_ply" /></td>
                        <td>&nbsp;&nbsp;Machine Brand</td>
                        <td><input style="width:90px;" type="text" class="text_boxes" name="txt_machine_brand_name" id="txt_machine_brand_name" /></td>
                    </tr>
                 </table>
                 <table width="820" cellspacing="2" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_panel">
                	 <thead>
                    	<tr>
                        	<th width="30" rowspan="2">SL</th>
                        	<th width="300" rowspan="2">Panel Description</th>
                            <th colspan="2">Knitting Time</th>
                            <th width="120">M/C Speed</th>
                            <th rowspan="2" id="tduom">Knitting Weight [GM]</th>
                        </tr>
                        <tr>
                        	<th width="100">Minute</th>
                            <th width="100">Second</th>
                            <th>Moving/Sec</th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?
							$i=1;
							foreach($time_weight_panel as $arrid=>$val)
							{
								if($i%2==0) $bgcolor="#FFFFFF"; else $bgcolor="#E9F3FF";
								?>
                                <tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer">
                                	<td align="center"><? echo $i; ?><input type="hidden" id="txtpanelupid_<? echo $i; ?>" value="" style="width:50px;"></td>
                                    <td><? echo $val; ?><input type="hidden" id="txtpanelid_<? echo $i; ?>" value="<? echo $arrid; ?>" style="width:50px;"></td>
                                    <td align="center"><input type="text" id="txtminute_<? echo $i; ?>" name="txtminute_<? echo $i; ?>" style="width:80px;" class="text_boxes_numeric" onChange="fnc_panel_sum();"></td>
                                    <td align="center"><input type="text" id="txtsecond_<? echo $i; ?>" name="txtsecond_<? echo $i; ?>" style="width:80px;" class="text_boxes_numeric" onChange="fnc_panel_sum();"></td>
                                    <td align="center"><input type="text" id="txtmovingsec_<? echo $i; ?>" name="txtmovingsec_<? echo $i; ?>" style="width:80px;" class="text_boxes_numeric" onChange="fnc_panel_sum();"></td>
                                    <td align="center"><input type="text" id="txtknitinggm_<? echo $i; ?>" name="txtknitinggm_<? echo $i; ?>" style="width:80px;" class="text_boxes_numeric" onChange="fnc_panel_sum();"></td>
                                </tr>
                                <?
								$i++;
							}
						?>
                    </tbody>
                    <tfoot>
                    	<tr class="general">
                            <td>&nbsp;</td>
                            <td><strong>Total Per Pcs: </strong></td>
                            <td colspan="2">
                            	<input type="text" id="txtminute_tot" name="txtminute_tot" style="width:80px;" class="text_boxes_numeric" readonly>
                                <input type="hidden" id="txtsecond_tot" name="txtsecond_tot" style="width:80px;" class="text_boxes_numeric" readonly>
                            </td>
                            <td><input type="text" id="txtmovingsec_tot" name="txtmovingsec_tot" style="width:80px;" class="text_boxes_numeric" readonly></td>
                            <td><input type="text" id="txtknitinggm_tot" name="txtknitinggm_tot" style="width:80px;" class="text_boxes_numeric" readonly></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>Critical Points / Special Comments: </td>
                            <td colspan="2" align="center">
                            	<input type="text" id="txtcritical_point" name="txtcritical_point" style="width:180px;" class="text_boxes" onDblClick="openmypage_special_comments('Critical Points / Special Comments Form')" placeholder="Browse to Write" readonly>
                            </td>
                            <td>Knitting Weight per Dzn/Lbs</td>
                            <td align="center"><input type="text" id="txtknitingweight_dzn" name="txtknitingweight_dzn" style="width:80px;" class="text_boxes_numeric" readonly></td>
                        </tr>
                        <tr>
                            <td colspan="6" valign="middle" align="center" class="button_container">
                                <? echo load_submit_buttons( $permission, "fnc_time_weight_record_entry", 0,0,"reset_form('timeweightentry_1*timeweightentry_2','','','','','cbo_company_name*cbo_location_name*cbo_buyer_name*cbo_product_department*cbo_season_id*cbo_region*cbo_item_catgory*cbo_team_leader*cbo_dealing_merchant');",1); ?>
                                <input type="button" id="copy_btn" class="formbutton" value="Copy" onClick="fnc_copy_weightrecord(5);" />
                            </td>
                        </tr>
                    </tfoot>
                 </table>
            </form>
        </fieldset>
    </div>
    
    <div id="color_body_part_dtls" align="left"></div>
    </div>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>