<?
/*-------------------------------------------- Comments
Purpose			: Yarn lot ratio planning
Functionality	:	
JS Functions	:
Created by		:	Ashraful Islam
Creation date 	: 	28-11-2018
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
echo load_html_head_contents("Size Set Weight Calculation","../../", 1, 1, $unicode,'','');
$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
$color_arr=json_encode($color_arr);

?>
<script>
	<?php
	echo "var color_arr = ". $color_arr . ";\n";
	?>
	var permission='<? echo $permission; ?>';
	
	function openmypage_jobNo()
	{
		var title = 'Search Job No';	
		var page_link = 'requires/size_sheet_weight_calclution_controller.php?action=job_search_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1030px,height=400px,center=1,resize=0,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm");
			var job_no=(this.contentDoc.getElementById("hidden_job_no").value).split('_');
			var company_id=(this.contentDoc.getElementById("cbo_company_name").value);
			if(job_no[11]=="") 
			{
				alert("Time And Weight Record  not found.");
				return;	
			}
			$('#company_id').val(company_id);
			
			$('#txt_style_no').val(job_no[2]);
			$('#cbo_deling_marchan').val(job_no[3]);
			$('#txt_sample_size').val(job_no[4]);
			$('#txt_job_no').val(job_no[0]);
			load_drop_down( 'requires/size_sheet_weight_calclution_controller',company_id, 'load_drop_down_buyer', 'buyer_id' );
			$('#cbo_buyer_name').val(job_no[1]);
			$("#cbo_buyer_name").attr("disabled",true);
			$('#cbogmtsitem').val(0);
			$('#gmt_color_id').val('');
			$('#txt_gmt_color').val('');
			
			$('#color_size_list_view').html('');
			
			get_php_form_data( job_no[11], "get_sample_reference", "requires/size_sheet_weight_calclution_controller" );
			load_drop_down( 'requires/size_sheet_weight_calclution_controller', job_no[0], 'load_drop_down_order_garment', 'garmentitem_td');
			release_freezing();
		}
	}
	
	function openmypage_gmt_color()
	{
		if(form_validation('txt_job_no*cbo_yarn_type*cbogmtsitem','Job No*Yarn Type*GMTS Item')==false)
		{
			return;
		}
		
		var title = 'Search Garments Color';	
		var page_link = 'requires/size_sheet_weight_calclution_controller.php?txt_job_no='+$("#txt_job_no").val()+'&cbogmtsitem='+$("#cbogmtsitem").val()+'&action=color_search_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=430px,height=300px,center=1,resize=0,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm");
			var gmt_color_id=(this.contentDoc.getElementById("hidden_color_id").value);
			$("#cbogmtsitem").attr("disabled",true);
			$('#gmt_color_id').val(gmt_color_id);
			$('#txt_gmt_color').val(color_arr[gmt_color_id]);
			load_drop_down('requires/size_sheet_weight_calclution_controller', $("#txt_job_no").val()+'_'+$("#cbogmtsitem").val()+'_'+gmt_color_id+'_'+$("#cbo_yarn_type").val(), 'load_data_color_size','color_size_list_view','');

			//load_drop_down('requires/size_sheet_weight_calclution_controller', $("#txt_job_no").val()+'_'+gmt_color_id+'_'+$("#cbo_yarn_type").val(), 'load_data_color_size_twisting','color_size_twisting_list_view','');
			release_freezing();
		}
	}

	function openmypage_yarn_color_twist(tr_id)
	{
		if(form_validation('txt_job_no*cbo_yarn_type*cbogmtsitem*txt_gmt_color','Job No*Yarn Type*GMTS Item*GMT Color')==false)
		{
			return;
		}
		
		var title = 'Search Garments Color';	
		var page_link = 'requires/size_sheet_weight_calclution_controller.php?txt_job_no='+$("#txt_job_no").val()+'&cbogmtsitem='+$("#cbogmtsitem").val()+'&gmt_color_id='+$("#gmt_color_id").val()+'&action=twisted_color_search_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=930px,height=300px,center=1,resize=0,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm");
			var sample_color_ids=(this.contentDoc.getElementById("hidden_sample_color_id").value);
			var yarn_color_ids=(this.contentDoc.getElementById("hidden_strip_color_id").value);
			var twisting_color_ids=(this.contentDoc.getElementById("hidden_twist_color_id").value);
			var ywd_ids=(this.contentDoc.getElementById("hidden_ydw_id").value);
			var gmt_color_id=$('#gmt_color_id').val();
			sample_color_id_arr=sample_color_ids.split(",");
			load_drop_down('requires/size_sheet_weight_calclution_controller', $("#txt_job_no").val()+'_'+$("#cbogmtsitem").val()+'_'+gmt_color_id+'_'+sample_color_ids+'_'+yarn_color_ids+'_'+twisting_color_ids+'_'+ywd_ids, 'load_data_color_size_twisting',tr_id,'');
			var sample_color_id_string='';
			for(i=0;i<sample_color_id_arr.length; i++)
			{
				if(i>0) sample_color_id_string=	sample_color_id_string+",";
				sample_color_id_string=	sample_color_id_string+sample_color_id_arr[i];
			}
			$("#"+tr_id).removeAttr('id').attr('id','searchtwisting'+sample_color_id_string);
			for(i=0;i<sample_color_id_arr.length; i++)
			{
				$("#searchtwisting"+sample_color_id_arr[i]).remove();	
			}
			//load_drop_down('requires/size_sheet_weight_calclution_controller', $("#txt_job_no").val()+'_'+gmt_color_id+'_'+$("#cbo_yarn_type").val(), 'load_data_color_size_twisting',tr_id,'');
			release_freezing();
		}
	}

	
	function fnc_production_weight_cal(id)
	{
		if($("#txt_extantion_no").val()!="")
		{
			if($("#hidd_copy_type").val()==1)
			{
				/*if($("#txt_production_weight_"+id).val()*1>$("#txt_production_weight_actual_"+id).val()*1)
				{
					alert("Revise Porduction Weight Grater Than Actual Production Weight Not Allow.");
					$("#txt_production_weight_"+id).val('');
					return;
				}*/
			}
		}
		$("#txt_short_excess_"+id).val((($("#txt_sample_weight_"+id).val()*1)-($("#txt_production_weight_"+id).val()*1)).toFixed(4));
		$("#txt_total_size_weight_"+id).val(((($("#txt_plan_qty_"+id).val()*1)*($("#txt_production_weight_"+id).val()*1))/1000).toFixed(4));
		fnc_total_production_weight_cal();
		fnc_total_avg_weight_cal();
		//$("#txt_avg_weight_"+id).val((($("#txt_total_size_weight_"+id).val()*1)/($("#txt_total_weight").val()*1)).toFixed(2));
	}
	
	function fnc_total_production_weight_cal()
	{
		var table_length=$("#tbl_size_wise_weight tbody tr").length;
		
		var total_production_qty=0;
		for(var i=1; i<=table_length; i++)
		{
			total_production_qty+=$("#txt_total_size_weight_"+i).val()*1;
		}
		$("#txt_total_weight").val(total_production_qty.toFixed(4));
		
		var total_plan_qty=$("#txt_total_plan_qty").val()*1;
		$("#txt_total_production_weight").val(((total_production_qty*1000)/total_plan_qty).toFixed(4));
		$("#txt_con_per_dzn_kg").val((($("#txt_total_production_weight").val()*12)/1000).toFixed(4));
		$("#txt_con_per_dzn_lbs").val((($("#txt_total_production_weight").val()*12*2.2046226)/1000).toFixed(4));

		if($("#cbo_yarn_type").val()==2)
		{
			$("#txt_con_per_dzn_kg_twisted").val((($("#txt_total_production_weight").val()*12)/1000).toFixed(4));
			$("#txt_con_per_dzn_lbs_twisted").val((($("#txt_total_production_weight").val()*12*2.2046226)/1000).toFixed(4));
		}
		
	}
	
	function fnc_total_avg_weight_cal()
	{
		var table_length=$("#tbl_size_wise_weight tbody tr").length;
		
		var total_production_qty=$("#txt_total_weight").val()*1;
		var total_avg_weight=0;
		for(var i=1; i<=table_length; i++)
		{
			var size_weight=$("#txt_total_size_weight_"+i).val()*1;
			if(size_weight>0) {
				
				var size_weight_percentage=((size_weight*100)/total_production_qty);
				total_avg_weight+=size_weight_percentage;
				$("#txt_avg_weight_"+i).val(size_weight_percentage.toFixed(2));
				
			}
			else $("#txt_avg_weight_"+i).val(0);
		}
		$("#txt_total_avg_weight").val(total_avg_weight.toFixed(2));
	}
	
	
	function fnc_production_color_calculation(id)
	{
		var consump_per_dzn		=$("#txt_con_per_dzn_kg").val()*1;
		var yarn_color_per		=$("#txt_production_color_per_"+id).val()*1;
		var consump_yarn_color	=(consump_per_dzn*yarn_color_per)/100;
		$("#txt_yarn_cons_"+id).val(consump_yarn_color.toFixed(4));
		$("#txt_yarn_cons_lbs_"+id).val((consump_yarn_color*2.2046226).toFixed(4));
		fnc_processloss_calculation(id,0);
		fnc_total_consumption(id);
		
	}

	function fnc_production_color_calculation_twisted(id)
	{
		//id=parseInt(id);
		//alert(id);
		var consump_per_dzn		=$("#txt_con_per_dzn_kg_twisted").val()*1;
		var yarn_color_per		=$("#txt_production_color_per_twisted_"+id).val()*1;
		var consump_yarn_color	=(consump_per_dzn*yarn_color_per)/100;
		$("#txt_yarn_cons_twisted_"+id).val(consump_yarn_color.toFixed(4));
		$("#txt_yarn_cons_lbs_twisted_"+id).val((consump_yarn_color*2.2046226).toFixed(4));
		fnc_processloss_calculation_twisted(id,0);
		fnc_total_consumption_twist(id);
		
	}
	
	function fnc_total_consumption_twist(id)
	{
		//var table_length=$("#tbl_sample_color_twisted tbody tr").length;
		
		var total_consumption=0;
		var total_actual_consumption=0;
		var total_production_color_per=0;
		
		$("#tbl_sample_color_twisted").find('tbody tr').each(function()
		{
			total_consumption+=$(this).find('input[name="txt_yarn_cons_twisted[]"]').val()*1;
			total_actual_consumption+=$(this).find('input[name="txt_yarn_actual_cons_twisted[]"]').val()*1;
			total_production_color_per+=$(this).find('input[name="txt_production_color_per_twisted[]"]').val()*1;
		});


		/*for(var i=1; i<=table_length; i++)
		{
			total_consumption+=$("#txt_yarn_cons_"+i).val()*1;
			total_actual_consumption+=$("#txt_yarn_actual_cons_"+i).val()*1;
			total_production_color_per+=$("#txt_production_color_per_"+i).val()*1;
		}*/
		$("#total_production_color_per_twisted").text(total_production_color_per.toFixed(2));
		$("#total_cons_per_kg_twisted").text(total_consumption.toFixed(4));
		$("#total_actual_cons_per_kg_twisted").text(total_actual_consumption.toFixed(4));
		$("#total_cons_per_lbs_twisted").text((total_consumption*2.2046226).toFixed(4));
		$("#total_actual_cons_per_lbs_twisted").text((total_actual_consumption*2.2046226).toFixed(4));
		
		if(total_production_color_per>100)
		{
			alert("Sorry, Production Color % More Than 100.Could Not Proceed On.")
			$("#txt_production_color_per_twisted_"+id).val(0);
			fnc_production_color_calculation_twisted(id);
			//return;
		}
		
		
	}
	
	
	function fnc_processloss_calculation_twisted(id,type)
	{
		var consump_yarn_color	=$("#txt_yarn_cons_twisted_"+id).val()*1;
		var process_loss		=$("#txt_process_loss_twisted_"+id).val()*1;
		var actual_consumption	=((consump_yarn_color*process_loss)/100)+consump_yarn_color;
		$("#txt_yarn_actual_cons_twisted_"+id).val(actual_consumption.toFixed(4));
		$("#txt_yarn_actual_cons_lbs_twisted_"+id).val((actual_consumption*2.2046226).toFixed(4));
		if(type==1){
			
			var table_length=$("#tbl_sample_color_twisted tbody tr").length;
			var total_actual_consumption=0;
			$("#tbl_sample_color_twisted").find('tbody tr').each(function()
			{
				total_actual_consumption+=$(this).find('input[name="txt_yarn_actual_cons_twisted[]"]').val()*1;//$("#txt_yarn_actual_cons_twisted_"+i).val()*1;

			});
			$("#total_actual_cons_per_kg_twisted").text(total_actual_consumption.toFixed(4));
			$("#total_actual_cons_per_lbs_twisted").text((total_actual_consumption*2.2046226).toFixed(4));

		}
	}

	function fnc_total_consumption(id)
	{
		var table_length=$("#tbl_sample_color tbody tr").length;
		
		var total_consumption=0;
		var total_actual_consumption=0;
		var total_production_color_per=0;
		
		for(var i=1; i<=table_length; i++)
		{
			total_consumption+=$("#txt_yarn_cons_"+i).val()*1;
			total_actual_consumption+=$("#txt_yarn_actual_cons_"+i).val()*1;
			total_production_color_per+=$("#txt_production_color_per_"+i).val()*1;
		}
		$("#total_production_color_per").text(total_production_color_per.toFixed(2));
		$("#total_cons_per_kg").text(total_consumption.toFixed(4));
		$("#total_actual_cons_per_kg").text(total_actual_consumption.toFixed(4));
		$("#total_cons_per_lbs").text((total_consumption*2.2046226).toFixed(4));
		$("#total_actual_cons_per_lbs").text((total_actual_consumption*2.2046226).toFixed(4));
		
		if(total_production_color_per>100)
		{
			alert("Sorry, Production Color % More Than 100.Could Not Proceed On.")
			$("#txt_production_color_per_"+id).val(0);
			fnc_production_color_calculation(id);
			//return;
		}
	}
	
	function fnc_processloss_calculation(id,type)
	{
		var consump_yarn_color	=$("#txt_yarn_cons_"+id).val()*1;
		var process_loss		=$("#txt_process_loss_"+id).val()*1;
		var actual_consumption	=((consump_yarn_color*process_loss)/100)+consump_yarn_color;
		$("#txt_yarn_actual_cons_"+id).val(actual_consumption.toFixed(4));
		$("#txt_yarn_actual_cons_lbs_"+id).val((actual_consumption*2.2046226).toFixed(4));
		if(type==1){
			
			var table_length=$("#tbl_sample_color tbody tr").length;
			var total_actual_consumption=0;
			for(var i=1; i<=table_length; i++)
			{
				total_actual_consumption+=$("#txt_yarn_actual_cons_"+i).val()*1;

			}
			$("#total_actual_cons_per_kg").text(total_actual_consumption.toFixed(4));
			$("#total_actual_cons_per_lbs").text((total_actual_consumption*2.2046226).toFixed(4));

		}
	}
	function generate_report_file(data,action,page)
	{
		window.open("requires/size_sheet_weight_calclution_controller.php?data=" + data+'&action='+action, true );
	}
	
	function fnc_size_set_calculation_info( operation )
	{        

       	if(form_validation('txt_size_set_date*txt_job_no*cbogmtsitem*txt_gmt_color*txt_size_set_date*txt_sample_ref*txt_sample_size','Size Set Date*Job No*Gmts Item*GMT Color*Buyer*Sample Ref.*Sample Size')==false)
	   	{
			return;
	   	}
		
		if($("#hidden_size_set_copy_form").val()!="")
		{
			if(form_validation('txt_extantion_no','Extantion No')==false)
		   	{
				return;
		   	}
		}
		if(operation==4)
		{
			if($('#update_id').val()<1)
			{
				alert("Save Data First.");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#gmt_color_id').val()+'*'+$('#cbogmtsitem').val(),'size_set_print','requires/size_sheet_weight_calclution_controller');
			return;
		}
		
		var sampleWtGMPCS=$('#txt_total_sample_weight').val();
		var productionWtGMPCS=$('#txt_total_production_weight').val();
		
		if((sampleWtGMPCS*1)<(productionWtGMPCS*1))
		{
			alert("Avg Production Weight(GM)/Pcs can not be greater than Avg Sample Weight(GM)/Pcs.");
			return;
		}
		
       	var size_row_num=$('#tbl_size_wise_weight tbody tr').length;
		var yarn_color_row_num=$('#tbl_sample_color tbody tr').length;
	
        var data1="action=save_update_delete&operation="+operation+"&size_row_num="+size_row_num+"&yarn_color_row_num="+yarn_color_row_num+get_submitted_data_string('update_id*txt_job_no*txt_size_set_date*cbo_buyer_name*sample_size_id*txt_system_no*txt_style_no*cbo_deling_marchan*txt_sample_ref*txt_sample_size*cbogmtsitem*gmt_color_id*company_id*txt_gmt_color*txt_technical_manager*txt_yarn_controller*txt_con_per_dzn_kg*txt_total_sample_weight*txt_total_production_weight*txt_total_plan_qty*txt_total_weight*txt_total_avg_weight*hidden_gmt_color_id*cbo_yarn_type*hidden_size_set_copy_form*txt_extantion_no*hidd_copy_type',"../../");
	
	    var data2='';
		for(var i=1; i<=size_row_num; i++)
		{
			if(form_validation('txt_production_weight_'+i,'Production Weight(GM)/Pcs')==false)
			{
				return;
			}
			data2+=get_submitted_data_string('txt_production_weight_'+i+'*txt_production_weight_actual_'+i+'*cbo_gmt_size_'+i+'*txt_sample_weight_'+i+'*txt_short_excess_'+i+'*txt_plan_qty_'+i+'*txt_total_size_weight_'+i+'*txt_avg_weight_'+i,"../../",i);
		}
		
		var data3='';
		if($("#cbo_yarn_type").val()!=2)
		{
			for(var i=1; i<=yarn_color_row_num; i++)
			{
				if(form_validation('txt_production_color_per_'+i,'Production Color %')==false)
				{
					return;
				}
				data3+=get_submitted_data_string('cbo_sample_color_'+i+'*cbo_stripe_color_'+i+'*txt_sample_color_per_'+i+'*txt_production_color_per_'+i+'*txt_yarn_cons_'+i+'*txt_process_loss_'+i+'*txt_yarn_actual_cons_'+i,"../../",i);
			}
		}
		var dataString=''; var j=1;
		if($("#cbo_yarn_type").val()==2)
		{
			$("#tbl_sample_color_twisted").find('tbody tr').each(function()
			{
				var tr_id=$(this).attr('id').split("g");
				//alert(tr_id[1]);
				var sample_color_ids=tr_id[1];
				var twisted_color=$(this).find('select[name="cbo_stripe_color_twisted[]"]').val();
				var sample_color_per_twisted=$(this).find('input[name="txt_sample_color_per_twisted[]"]').val();
				var production_color_per_twisted=$(this).find('input[name="txt_production_color_per_twisted[]"]').val();
				var yarn_cons_twisted=$(this).find('input[name="txt_yarn_cons_twisted[]"]').val();
				var yarn_actual_cons_twisted=$(this).find('input[name="txt_yarn_actual_cons_twisted[]"]').val();
				var process_loss_twisted=$(this).find('input[name="txt_process_loss_twisted[]"]').val();
				var strip_color_ids=$(this).find('input[name="hidden_strip_color_ids[]"]').val();
				var hidden_ydw_id=$(this).find('input[name="hidden_ydw_ids[]"]').val();
				//var strip_color_ids=$(this).find('input[name="hidden_strip_color_ids[]"]').val();
				//var rollNo=$(this).find("td:eq(2)").text();

				dataString+='&sampleColorIds_' + j + '=' + sample_color_ids + '&twistedColor_' + j + '=' + twisted_color + '&sampleColorPerTwisted_' + j + '=' + sample_color_per_twisted + '&productionColorPerTtwisted_' + j + '=' + production_color_per_twisted + '&yarnConsTwisted_' + j + '=' + yarn_cons_twisted + '&yarnActualConsTwisted_' + j + '=' + yarn_actual_cons_twisted + '&processLossTwisted_' + j + '=' + process_loss_twisted + '&stripColorIds_' + j + '=' + strip_color_ids + '&hiddenYdwId_' + j + '=' + hidden_ydw_id  ;
				j++;
			});
			var twisted_color_row_num=$('#tbl_sample_color_twisted tbody tr').length;
			dataString+='&twisted_color_row_num=' + twisted_color_row_num ;

		}
		
	    var data=data1+data2+data3+dataString;
	    
		freeze_window(operation);
		http.open("POST","requires/size_sheet_weight_calclution_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_size_set_calculation_info_reponse;
	}
	
	function fnc_size_set_calculation_info_reponse()
	{
		if(http.readyState == 4) 
		{
			
			var reponse=trim(http.responseText).split('**');
			
			if(reponse[0]*1==121)
			{
				alert('Yarn Lot Ratio Entry Found: '+reponse[1]);
				release_freezing();
				return;
			}
			show_msg(trim(reponse[0]));
			if(reponse[0]==0 || reponse[0]==1)
			{
				
				document.getElementById('update_id').value=reponse[1];
				document.getElementById('txt_system_no').value=reponse[2];
				//alert(33);
				$("#txt_job_no").attr("disabled",true);
				$('#cbo_yarn_type').attr('disabled',true);
				$("#color_size_list_view").html('');
				set_button_status(0, permission, 'fnc_size_set_calculation_info',1);
				show_list_view(reponse[1],'show_data_listview','list_container','requires/size_sheet_weight_calclution_controller','');
				$("#txt_gmt_color").val('');
				$("#gmt_color_id").val('');
			}
			else if(reponse[0]*1==2)
			{
				release_freezing();
				location.reload();
				return;
			}

		
			release_freezing();
		}
	}
	
	function open_system_popup()
	{ 
		 
		var page_link='requires/size_sheet_weight_calclution_controller.php?action=system_number_popup'; 
		var title="Search System Number Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=420px,center=1,resize=0,scrolling=0',' ../');
		emailwindow.onclose=function()
		{
			$("#color_size_list_view").html('');
			var update_id = this.contentDoc.getElementById("update_mst_id").value; 
			get_php_form_data( update_id, "load_php_mst_form", "requires/size_sheet_weight_calclution_controller" );
			show_list_view(update_id,'show_data_listview','list_container','requires/size_sheet_weight_calclution_controller','');
			
			$("#txt_job_no").attr("disabled",true);
			$("#hidd_copy_type").val(1);
		}
	}
	
	function put_data_dtls_part(str)
	{
		var exstr=str.split('_');
		var gmtsitem_id=exstr[0];
		var color_id=exstr[1];
		show_list_view($("#update_id").val()+"**"+gmtsitem_id+"**"+color_id+"**"+$("#txt_job_no").val()+"**"+$("#cbo_yarn_type").val(),'load_update_data_color_size','color_size_list_view','requires/size_sheet_weight_calclution_controller','');
		
		$('#cbogmtsitem').val(gmtsitem_id);
		$("#cbogmtsitem").attr("disabled",true);
		
		$('#gmt_color_id').val(color_id);
		$('#hidden_gmt_color_id').val(color_id);
		
		$('#txt_gmt_color').val(color_arr[color_id]);
		set_button_status(1, permission, 'fnc_size_set_calculation_info',1,1);
	}
	
	function coppy_size_set(type)
	{
		var sampleWtGMPCS=$('#txt_total_sample_weight').val();
		var productionWtGMPCS=$('#txt_total_production_weight').val();
		
		if((sampleWtGMPCS*1)<(productionWtGMPCS*1))
		{
			alert("Avg Production Weight(GM)/Pcs can not be greater than Avg Sample Weight(GM)/Pcs.");
			return;
		}

		if($("#txt_system_no").val()=="") return;
		if($("#hidden_size_set_copy_form").val()!="") return;
		if (trim($("#color_size_list_view").html())=="") {
		    alert('Please Populae Details Part For Size Set Copy.'); return;
		}
		else {
		    //alert('not empty');
		    $("#hidden_size_set_copy_form").val($("#update_id").val());
		    $("#update_id").val('');
		    $("#txt_system_no").val('');
		    $("#list_container").html('');
		    set_button_status(0, permission, 'fnc_size_set_calculation_info',1);
		    get_php_form_data( $("#txt_job_no").val()+"_"+$("#gmt_color_id").val(), "load_extantion_no", "requires/size_sheet_weight_calclution_controller" );
		}
	}

	function coppy_size_set22(type)
	{
		var hiddenCopyId=$('#hidden_size_set_copy_form').val()*1;
		
		var listDtls=trim($('#color_size_list_view').html());
		
		if( $('#txt_system_no').val() =="" ) alert("a"); return;
		if(hiddenCopyId==0)  alert("b"); return;
		
		alert(listDtls);
		if( listDtls =="" ) 
		{
		    alert('Please Populae Details Part For Size Set Copy.'); return;
		}
		else 
		{
			//alert("c");
			$('#hidd_copy_type').val(type);
		    $('#hidden_size_set_copy_form').val($('#update_id').val());
		    $('#update_id').val('');
		    $('#txt_system_no').val('');
			
			if(type==2)
			{
				$('#txt_gmt_color').val('');
				$('#gmt_color_id').val('');
				$('#hidden_gmt_color_id').val('');
				$('#txt_extantion_no').val('');
			}
		    $('#list_container').html('');
		    set_button_status(0, permission, 'fnc_size_set_calculation_info',1);
		   if(type==1) get_php_form_data( $('#txt_job_no').val()+"_"+$('#gmt_color_id').val(), "load_extantion_no", "requires/size_sheet_weight_calclution_controller" );
		}
	}
	function fnc_resetDtls()
	{
		location.reload(); 
	}
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:1000px;" align="center">
     <? echo load_freeze_divs ("../../",$permission);  ?>
     <form name="sizesetweight_1" id="sizesetweight_1">
    <!--WO_PRE_COS_FAB_CO_AVG_CON_DTLS WO_PRE_COST_FABRIC_COST_DTLS-->
    <table width="100%" cellpadding="0" cellspacing="2" align="center" style="">
     	<tr>
        	<td width="100%" align="center" valign="top">  <!--   Form Left Container -->
            	<fieldset style="width:970px;">
                     <legend>Size Set Weight Calculation</legend>
                        <table  width="960" cellspacing="2" cellpadding="0" border="0">
                            <tr>
                                <td colspan="4" align="right"><b>System ID</b></td>
                                <td colspan="2">
                                    <input type="text" name="txt_system_no" id="txt_system_no" class="text_boxes" style="width:120px" placeholder="Double Click To Search" onDblClick="open_system_popup();" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).ondblclick();" readonly />
                                    <input type="hidden" name="update_id"  id="update_id"  />
                                    <input type="hidden" name="sample_size_id"  id="sample_size_id"  />
                                </td>
                                <td colspan="2">
                                	<input type="button" value="Revised Size Set" name="refresh1" onClick="coppy_size_set(1);" style="width:100px" id="Refresh1" class="formbutton">
                                	<input type="button" value="Copy" name="refresh2" onClick="coppy_size_set(2);" style="width:70px; display:none" id="Refresh2" class="formbuttonplasminus"><input type="hidden" name="hidd_copy_type" id="hidd_copy_type" value="1" />
                                </td>
                            </tr>
                          	<tr>
                                <td width="90" class="must_entry_caption">Size Set Date</td>              
                                <td width="140"><input style="width:120px;" type="text" class="datepicker" autocomplete="off" name="txt_size_set_date" id="txt_size_set_date" /></td>
                                <td width="80" class="must_entry_caption">Job No</td>
                                <td width="150"><input style="width:115px;" type="text"  onDblClick="openmypage_jobNo();" class="text_boxes" autocomplete="off" placeholder="Browse" name="txt_job_no" id="txt_job_no" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).ondblclick();" readonly /></td>
                                <td width="100" class="must_entry_caption">Buyer: </td> 
                                <td width="150" id="buyer_id"><? echo create_drop_down( "cbo_buyer_name", 130,$blank_arr,"", 1, "-Select-", $selected, "" ,1); ?></td>
                                <td width="80">Style Ref.</td>
                                <td><input type="text" name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:120px"  readonly /></td>
                          	</tr>
                         	<tr>
                                <td class="must_entry_caption">Del. Merchan.</td>
                                <td><? echo create_drop_down( "cbo_deling_marchan", 130,"select id, team_member_name from lib_mkt_team_member_info","id,team_member_name", 1, "-Select-", $selected, "" ,1); ?></td>
                                <td class="must_entry_caption">Sample Ref.:</td>
                                <td><input style="width:115px;" type="text"   class="text_boxes"  name="txt_sample_ref" id="txt_sample_ref" readonly /></td>
                                <td class="must_entry_caption">Sample Size:</td>
                                <td>
                                	<input style="width:120px;" type="text" class="text_boxes"   name="txt_sample_size" id="txt_sample_size"  readonly />
                                    <input type="hidden" name="company_id"  id="company_id"  />
                                    <input type="hidden" name="gmt_color_id"  id="gmt_color_id"  />
                                    <input type="hidden" name="hidden_gmt_color_id"  id="hidden_gmt_color_id"  />
                                    <input type="hidden" name="hidden_size_set_copy_form"  id="hidden_size_set_copy_form"  />
                                </td>
                                <td class="must_entry_caption">Yarn Type:</td>
								<td>
                                    <?
                                    $size_set_yarn_type=array(1=>"Regular",2=>"Twisted");
                                    echo create_drop_down( "cbo_yarn_type", 130,$size_set_yarn_type,"", 1, "---Select---", $selected, "" ,0); ?>
                                </td>
                   			</tr>
                            <tr>
                            	<td class="must_entry_caption">GMTS Item:</td>
                                <td id="garmentitem_td"><?=create_drop_down( "cbogmtsitem", 130, $blank_array,"", 1, "-- Select Item --", $selected, "",""); ?></td>
                                <td class="must_entry_caption">GMT Color:</td>
                                <td><input style="width:115px;" type="text"  onDblClick="openmypage_gmt_color();" class="text_boxes" autocomplete="off" placeholder="Browse" name="txt_gmt_color" id="txt_gmt_color" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).ondblclick();" readonly /></td>
                                <td>Technical Manager: </td>
                                <td><input type="text" name="txt_technical_manager" id="txt_technical_manager" class="text_boxes" style="width:120px" /></td>
                               	<td>Yarn Controller:</td>         
                               	<td><input type="text" name="txt_yarn_controller" id="txt_yarn_controller" class="text_boxes" style="width:120px"  /></td>
                                
                          </tr>
						  <tr>
                                <td>Extention No:</td>         
                               	<td><input type="text" name="txt_extantion_no" id="txt_extantion_no" class="text_boxes" style="width:120px"  disabled /></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                          </tr>
                      </table>
                 </fieldset>
              </td>
         </tr>
         <tr>
             <td align="center" valign="top" id="color_size_list_view"></td>
         </tr>
         <tr>
             <td align="center" valign="top" id="color_size_twisting_list_view"></td>
         </tr>
        <tr>
            <td colspan="4" align="center">
            	<? echo load_submit_buttons( $permission, "fnc_size_set_calculation_info", 0,1,"reset_form('sizesetweight_1','list_container','','','fnc_resetDtls();')",1); ?>
            </td>
        </tr>
        <tr>
            <td align="center" colspan="4" id="list_container"></td>				
        </tr>
	</table>
    </form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>