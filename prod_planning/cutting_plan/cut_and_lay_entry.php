﻿<?
/*-------------------------------------------- Comments
Purpose			: 
Functionality	:	
JS Functions	:
Created by		:	Ashraful Islam 
Creation date 	: 	23-03-2014
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
   echo load_html_head_contents("Cut and Lay Entry","../../", 1, 1, $unicode,'','');
   ?>
 <script>
    var txt_job_id=$("#txt_job_no").val();
	var permission='<? echo $permission; ?>';
	function add_break_down_tr(i)
	   { 
			var row_num=$('#tbl_order_details tbody tr').length;
			if (row_num!=i)
			{
				return false;
			}
			else
			{ 
				i++;
		       var k=i-1;
				$("#tbl_order_details tbody tr:last").clone().find("input,select").each(function(){
				$(this).attr({ 
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
				  'value': function(_, value) { return value }              
				});
				}).end().appendTo("#tbl_order_details");
				$("#tbl_order_details tbody tr:last").css({"height":"10px","background-color":"#FFF"});	
				$("#tbl_order_details tbody tr:last ").removeAttr('id').attr('id','tr_'+i);
				$("#tbl_order_details tbody tr:last td:nth-child(2)").removeAttr('id').attr('id','cutNo_'+i);
				$("#tbl_order_details tbody tr:last td:nth-child(3)").removeAttr('id').attr('id','ship_'+i);
				$("#tbl_order_details tbody tr:last td:nth-child(4)").removeAttr('id').attr('id','garment_'+i);
				$("#tbl_order_details tbody tr:last td:nth-child(6)").removeAttr('id').attr('id','color_'+i);
				$("#tbl_order_details tbody tr:last td:nth-child(10)").removeAttr('id').attr('id','order_'+i);
				$("#tbl_order_details tbody tr:last td:nth-child(9)").removeAttr('id').attr('id','marker_'+i);
				
			    $('#cbogmtsitem_'+i).val('');
				$('#orderCutNo_'+i).val('');
				$('#updateDetails_'+i).val('');
				$('#cboorderno_'+i).val('');
				$('#rollData_'+i).val('');
				$('#hiddenExtralRollData_'+i).val(''); 
				$('#txtorderqty_'+i).val('');
				$('#txtshipdate_'+i).val('');
				$('#cbocolor_'+i).val('');
				$('#txtplics_'+i).val('');
				$('#txtmarkerqty_'+i).val('');
				$('#cboorderno_'+i).val('');
				$('#txttotallay_'+i).val('');
				$('#txtlaybalanceqty_'+i).val('');
				$('#txtshipdate_'+i).addClass("datepicker");
				$('#txtplics_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_roll("+i+");");
				$('#increase_'+i).removeAttr("value").attr("value","+");
				$('#decrease_'+i).removeAttr("value").attr("value","-");
				$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
				$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
				$('#increase_'+i).attr("onkeydown","add_break_down_tr("+i+");");
			}
	   }
	   
	function fn_deleteRow(rowNo) 
		{ 
			if($('#tbl_order_details').val()!=2)
			{
				var numRow = $('#tbl_order_details tbody tr').length; 
				var k=rowNo-1;
				if(numRow==rowNo && rowNo!=1)
					{
						var updateIdDtls=$('#updateIdDtls_'+rowNo).val();
						var txt_deleted_id=$('#txt_deleted_id').val();
						var selected_id='';
						if(updateIdDtls!='')
					 		{
								if(txt_deleted_id=='') selected_id=updateIdDtls; else selected_id=txt_deleted_id+','+updateIdDtls;
								$('#txt_deleted_id').val( selected_id );
							}
						$('#tbl_order_details tbody tr:last').remove();
					}
				else
				    {
					 return false;
				    }
		    }
	  }

function calculate_efficiency()
{
	var lay_fabric_weight=$("#txt_lay_wght").val()*1;
	if(lay_fabric_weight !="")
	{
		var wastae_qnty=$("#txt_wastage_qnty").val()*1;
		var efficiency=((lay_fabric_weight - wastae_qnty)/lay_fabric_weight*1) * 100;
		$("#txt_efficiency").val(efficiency.toFixed(3));
    }
    else
    {
    	$("#txt_efficiency").val('');
    }

}
function openmypage_roll(row_no)
{
	if(form_validation('cbo_company_name*cboorderno_'+row_no+'*cbocolor_'+row_no,'Company*Order No*Color')==false)
	{
		return;   
	}
	
	var roll_maintained=$('#roll_maintained').val();
	var order_no=$('#cboorderno_'+row_no).val();
	var color=$('#cbocolor_'+row_no).val();
	var rollData=$('#rollData_'+row_no).val();
	var ExtraRollData=$('#hiddenExtralRollData_'+row_no).val();
 
	var title = 'Plies Entry Roll Wise Form';	
	var page_link = 'requires/cut_and_lay_entry_controller.php?roll_maintained='+roll_maintained+'&order_no='+order_no+'&color='+color+'&rollData='+rollData+'&ExtraRollData='+ExtraRollData+'&action=roll_popup';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=410px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function() 
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var plies=this.contentDoc.getElementById("hide_plies").value; //Access form field with id="emailfield"
		var data=this.contentDoc.getElementById("hide_data").value; //Access form field with id="emailfield"
		var tot_weight=this.contentDoc.getElementById("hide_sum_roll_weight").value;
		var extra_roll=this.contentDoc.getElementById("hide_extra_roll_data").value; 
		var extra_wgt=this.contentDoc.getElementById("hide_extra_wgt").value; 
		//alert(tot_weight);
		$('#txtplics_'+row_no).val(plies);
		$('#rollData_'+row_no).val(data);
		$('#hiddenExtralRollData_'+row_no).val(extra_roll);
		$('#txt_lay_wght').val(tot_weight);
		$('#hidden_lay_extra_wgt').val(extra_wgt);
		var wastae_qnty=$("#txt_wastage_qnty").val()*1;

		calculate_efficiency();
	}
}
	
function openmypage_jobNo(id)
	{
		    var cbo_company_id = $('#cbo_company_name').val();
			if(form_validation('cbo_company_name','Company Name')==false)
			   {
				return;
			   }
			var title = 'Search Job No';	
			var page_link = 'requires/cut_and_lay_entry_controller.php?cbo_company_id='+cbo_company_id+'&action=job_search_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1030px,height=400px,center=1,resize=0,scrolling=0','../');
			emailwindow.onclose=function()
			 {
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var job_no=(this.contentDoc.getElementById("hidden_job_no").value).split('_');
				$('#txt_buyer_name').val(job_no[1]);
				$('#txt_job_year').val(job_no[2]);
		        document.getElementById('txt_job_no').value=job_no[0];
				load_drop_down( 'requires/cut_and_lay_entry_controller',job_no[0]+'**'+cbo_company_id, 'load_drop_down_order', 'order_id' );
				var $job_name=job_no[0];
				release_freezing();
			 }
	}
	
  	function change_order(value)
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var txt_job=$("#txt_job_no").val();
		var cbo_company_id = $('#cbo_company_name').val();
		if(txt_job.length<5)
		{
			load_drop_down( 'requires/cut_and_lay_entry_controller',txt_job+'**'+value+'**'+cbo_company_id, 'load_drop_down_job', 'job_change_id' );
			load_drop_down( 'requires/cut_and_lay_entry_controller',txt_job+'**'+value+'**'+cbo_company_id, 'load_drop_down_buyer', 'buyer_id' );
		}
		var txt_job=$("#txt_job_no").val();
		load_drop_down( 'requires/cut_and_lay_entry_controller',txt_job+'**'+cbo_company_id, 'load_drop_down_order', 'order_id' );
	//load_drop_down( 'requires/cut_and_lay_entry_controller',value+'**'+txt_year+'**'+cbo_company_id, 'load_drop_down_buyer', 'buyer_id' );
	  
  	}
	
/*	function option_select()
	{
	
	if($('#cboorderno_1 option').length==2)
		{
			if($('#cboorderno_1 option:first').val()==0)
			{
				$('#cboorderno_1').val($('#cboorderno_1 option:last').val());
				eval($('#cboorderno_1').attr('onchange'));
			}
			
		}
		else if($('#cboorderno_1 option').length==1)
		{
			$('#cboorderno_1').val($('#cboorderno_1 option:last').val());
			eval($('#cboorderno_1').attr('onchange'));
			//change_data($('#cboorderno_1').val(),id)
		}
			
	}*/
	
	function change_data(value,id)
    { 
	
	    var id=id.split('_');
		var ship_id='ship_'+id[1];
		var order_qty_id='order_'+id[1];
		var gmt_id='garment_'+id[1];
		
		$('#orderCutNo_'+id[1]).val();
		
		load_drop_down( 'requires/cut_and_lay_entry_controller', value+"_"+ship_id, 'load_drop_down_ship', ship_id);
		load_drop_down( 'requires/cut_and_lay_entry_controller', value+"_"+gmt_id, 'load_drop_down_order_garment', gmt_id);
		var gmt_value=$("#cbogmtsitem_"+id[1]).val();
		if(gmt_value!=0)
		{
		 var gmt_id="cbogmtsitem_"+id[1];
		  change_color(gmt_id,gmt_value);	
		}
    }

	function change_color(id,value)
		 {
		
			var id=id.split('_');
			var color_id='color_'+id[1];
			var order_id=$('#cboorderno_'+id[1]).val();
			load_drop_down( 'requires/cut_and_lay_entry_controller', order_id+"_"+value+"_"+id[1], 'load_drop_down_color', color_id);
			var color_value=$("#cbocolor_"+id[1]).val();
			if(color_value!=0)
				{
				  var color_id="cbogmtsitem_"+id[1];
				  change_marker(color_id,color_value);	
				}
		}
	
	function change_marker(id,value)
		{
			id=id.split('_');
			var order_id_no='order_'+id[1];
			marker_id='marker_'+id[1];
			var order_id=$('#cboorderno_'+id[1]).val();
		    var gmt_id=$('#cbogmtsitem_'+id[1]).val();
			var txt_job_no=$("#txt_job_no").val();
			var ship_date=$("#txtshipdate_"+id[1]).val();
			var gmt_value=$("#cbogmtsitem_"+id[1]).val();
			var color_value=$("#cbocolor_"+id[1]).val();
			var row_num=$('#tbl_order_details tbody tr').length;
			for(var i=1;i<=row_num;i++)
				{
				if(row_num!=1 && id[1]!=i)
					{
					   if(order_id==$('#cboorderno_'+i).val() && gmt_value==$('#cbogmtsitem_'+i).val() && color_value==$('#cbocolor_'+i).val() && ship_date==$('#txtshipdate_'+i).val() )
						   {
						   alert(" Order number,Ship date,Gmt Item,Color are same");
						   $("#cbocolor_"+id[1]).val("");
						   return;
						   }
					}
				}
			get_php_form_data(  order_id+"_"+gmt_id+"_"+value+"_"+id[1], "load_drop_down_order_qty", "requires/cut_and_lay_entry_controller" );
			//load_drop_down( 'requires/cut_and_lay_entry_controller', order_id+"_"+gmt_id+"_"+value+"_"+id[1], 'load_drop_down_order_qty', order_id_no);
		}
	
	
	function openmypage_sizeNo(id)
    	{
			$('#'+id).attr("onkeydown","openmypage_sizeNo(id);");
		    var job_id = $('#txt_job_no').val();
			var size_wise_repeat_cut_no = $('#size_wise_repeat_cut_no').val();
		    var cbo_company_id = $('#cbo_company_name').val();
			var id=id.split('_');
		    var size=id[1];
			var mst_id = $('#update_id').val();
			var details_id= $('#updateDetails_'+size).val();
			if(details_id=="" && mst_id=="")
			{
			   alert("Please save first");return;	
			}
			if(details_id=="" && mst_id!="")
			{
			   alert("Please Update first");return;	
			}

			var order_id= $('#cboorderno_'+size).val();
		    var marker_quantity = $('#txtmarkerqty_'+size).val();
			var order_quantity = $('#txtorderqty_'+size).val();
			var total_lay_qty = $('#txttotallay_'+size).val();
			var total_lay_balance = $('#txtlaybalanceqty_'+size).val();
			var piles = $('#txtplics_'+size).val();
			var cutting_no = $('#txt_cutting_no').val();
			var cbo_color_id = $('#cbocolor_'+size).val();
			var cbo_gmt_id = $('#cbogmtsitem_'+size).val();
			var cbo_countries = $('#countryId_'+size).val();
			$("#tr_"+size).css({"background-color":"yellow"});
		//	$("#tbl_order_details tbody tr_"+size).css({"background-color":"red"});
			var title = 'Size Ratio Form';
			
			var page_link = 'requires/cut_and_lay_entry_controller.php?cbo_company_id='+cbo_company_id+'&job_id='+job_id+'&mst_id='+mst_id+'&details_id='+details_id+'&cbo_gmt_id='+cbo_gmt_id+'&cbo_color_id='+cbo_color_id+'&size='+size+'&txt_piles='+piles+'&cutting_no='+cutting_no+'&order_id='+order_id+'&marker_quantity='+marker_quantity+'&order_quantity='+order_quantity+'&total_lay_qty='+total_lay_qty+'&total_lay_balance='+total_lay_balance+'&size_wise_repeat_cut_no='+size_wise_repeat_cut_no+'&action=size_popup&cbo_countries='+cbo_countries;
			//alert(page_link)
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=500px,center=1,resize=0,scrolling=0','../../');
			emailwindow.onclose=function()
			 { 
				var sysNumber=this.contentDoc.getElementById("hidden_marker_no_x").value;
				var marker_no=sysNumber.split('**');
			    $('#txtmarkerqty_'+marker_no[0]).val(marker_no[1]);
			    $('#txtorderqty_'+marker_no[0]).val(marker_no[2]);
			    $('#txttotallay_'+marker_no[0]).val(marker_no[3]);
			    $('#txtlaybalanceqty_'+marker_no[0]).val(marker_no[4]);
				//freeze_window(5);
				$("#tr_"+marker_no[0]).css({"background-color":"white"});
				//release_freezing();
			 }
	     }

		
function fnc_cut_lay_info( operation )
{        

        if(form_validation('cbo_company_name*txt_job_no*txt_entry_date*txt_end_date','Company Name*Job No*Plan Start Date*Plan End Date')==false)
			   {
				return;
			   }
        var row_num=$('#tbl_order_details tbody tr').length;
		var tna_order_id="";
		for(var r=1; r<=row_num; r++)
		{
			if(tna_order_id=="") tna_order_id=$("#cboorderno_"+r).val();
			else                 tna_order_id=tna_order_id+","+$("#cboorderno_"+r).val();
			
		}
	    var cut_start_date=$("#txt_entry_date").val();
		var cut_finish_date=$("#txt_end_date").val();
		var company_name=$("#cbo_company_name").val();
		var tna_data=cut_start_date+"**"+cut_finish_date+"**"+tna_order_id+"**"+company_name;
		
		var tna_date=return_ajax_request_value(tna_data, "tna_date_status", "requires/cut_and_lay_entry_controller");
		if(tna_date!=2)
		{
			tna_date=trim(tna_date).split("##");
			if(tna_date[0]==0)
			{
				var new_table="Cutting Plane date range has been crossed \nTNA date range\n";
				new_table+="Po Number   TNA Start Date  TNA End Date\n";
				var tna_order=(tna_date[1]).split("**");
				var tna_start=tna_date[2].split("**");
				var tna_end=tna_date[3].split("**");
				for(var p=0; p<tna_order.length;p++)
				{
					new_table+=tna_order[p]+"            "+tna_start[p]+"             "+tna_end[p]+"\n";
				}
				new_table+="Maximum TNA Date-"+tna_date[4]+"\n Minimum TNA Date-"+tna_date[5];
				r=confirm(new_table);
				if(r==false)
				{
				  $("#txt_entry_date").val(tna_date[4]);
				  $("#txt_end_date").val(tna_date[5]);
				  return;	
				}
			}
		 }
        var data1="action=save_update_delete&operation="+operation+"&row_num="+row_num+get_submitted_data_string('update_id*update_tbl_id*txt_job_no*txt_batch_no*cbo_company_name*cbo_floor_name*cbo_location_name*txt_cutting_no*txt_table_no*txt_entry_date*txt_marker_length*txt_marker_width*txt_fabric_width*txt_gsm*txt_cutting_no*txt_in_time_hours*txt_in_time_minuties*txt_out_time_hours*txt_out_time_minuties*txt_end_date*cbo_width_dia*txt_lay_wght*txt_marker_cons*txt_remark*txt_efficiency*txt_wastage_qnty*roll_maintained*hidden_lay_extra_wgt',"../../");
		//alert(row_num)
	    var data2='';
		for(var i=1; i<=row_num; i++)
		{
			if(form_validation('cboorderno_'+i+'*orderCutNo_'+i+'*cbocolor_'+i+'*txtplics_'+i,'Order No*Order Cut No*Color*Plies')==false)
			{
				return;
			}
			data2+=get_submitted_data_string('updateDetails_'+i+'*cboorderno_'+i+'*cbogmtsitem_'+i+'*txtshipdate_'+i+'*cbocolor_'+i+'*txtplics_'+i+'*txtorderqty_'+i+'*orderCutNo_'+i+'*rollData_'+i+'*hiddenExtralRollData_'+i+'*orderCutNo_'+i+'*countryId_'+i,"../../",i);

			// data2 += '&updateDetails_' + i + '=' + $('#updateDetails_' + i).val() + '&cboorderno_' + i + '=' + $('#cboorderno_' + i).val() + '&cbogmtsitem_' + i + '=' + $('#cbogmtsitem_' + i).val() + '&txtshipdate_' + i + '=' + $('#txtshipdate_' + i).val() + '&cbocolor_' + i + '=' + $('#cbocolor_' + i).val() + '&txtplics_' + i + '=' + $('#txtplics_' + i).val() + '&txtorderqty_' + i + '=' + $('#txtorderqty_' + i).val() + '&orderCutNo_' + i + '=' + $('#orderCutNo_' + i).val()+ '&rollData_' + i + '=' + $('#rollData_' + i).val()+ '&hiddenExtralRollData_' + i + '=' + $('#hiddenExtralRollData_' + i).val()+ '&orderCutNo_' + i + '=' + $('#orderCutNo_' + i).val()+ '&countryId_' + i + '=' + $('#countryId_' + i).val();
		}
	    var data=data1+data2;
		//alert(data1);orderCutNo_1
		freeze_window(operation);
		http.open("POST","requires/cut_and_lay_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_cut_lay_info_reponse;
	}


function fnc_cut_lay_info_reponse()
{
	if(http.readyState == 4) 
	{
		//alert(http.responseText);
		var reponse=trim(http.responseText).split('**');
		if(reponse[0]==200)
		{
			alert("Update Restricted.This information found in Cutting Qc Page Which System Id "+reponse[1]+".");
		}
		else if(reponse[0]==786) // for duplicate barcode chk
		{
			alert(reponse[1]);
		}
		show_msg(trim(reponse[0]));
		if(reponse[0]==0)
			{
				document.getElementById('update_id').value=reponse[1];
				document.getElementById('update_tbl_id').value=reponse[3];
				document.getElementById('txt_cutting_no').value=reponse[2];
				var details_id=reponse[4].split('_');
				for(var i=1;i<=details_id.length;i++)
					{
						var data=details_id[i-1].split("#");
						document.getElementById('updateDetails_'+i).value=data[0];	
						//document.getElementById('orderCutNo_'+i).value=data[1];	
					}
				$("#cbo_company_name").attr("disabled",true);
				$("#txt_job_no").attr("disabled",true);
				set_button_status(1, permission, 'fnc_cut_lay_info',1,1);
			}
		if( reponse[0]==1)
			{
				document.getElementById('update_id').value=reponse[1];
				document.getElementById('update_tbl_id').value=reponse[3];
				document.getElementById('txt_cutting_no').value=reponse[2];
				var details_id=reponse[4].split('_');
			
				for(var i=1;i<=details_id.length;i++)
					{
						var data=details_id[i-1].split("#");
				   		document.getElementById('updateDetails_'+i).value=data[0];
						//document.getElementById('orderCutNo_'+i).value=data[1];		
					}
				$("#cbo_company_name").attr("disabled",true);
				$("#txt_job_no").attr("disabled",true);
				set_button_status(1, permission, 'fnc_cut_lay_info',1,1);
			}
		
		//set_button_status(1, permission, 'fnc_cut_lay_info');
		release_freezing();
	}
} 

function clear_tr()
{
 var row_num=$('#tbl_order_details tbody tr').length;
   for(var j=1;j<=row_num;j++)
   {
	   if(j!=1)
	   {
	     $('#tbl_order_details tbody tr:last').remove();   
	   }
   }
   $("#cbo_company_name").attr("disabled",false);
   $("#txt_job_no").attr("disabled",false);
}

function open_cutting_popup()
{ 
	if( form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	} 
	var company_id=$("#cbo_company_name").val();
	var page_link='requires/cut_and_lay_entry_controller.php?action=cutting_number_popup&company_id='+company_id; 
	var title="Search Cutting Number Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=420px,center=1,resize=0,scrolling=0',' ../');
	emailwindow.onclose=function()
	{
		var sysNumber = this.contentDoc.getElementById("update_mst_id"); 
		var sysNumber=sysNumber.value.split('_');
		get_php_form_data( sysNumber[0], "load_php_mst_form", "requires/cut_and_lay_entry_controller" );
		show_list_view( sysNumber[0], 'order_details_list', 'cut_details_container', 'requires/cut_and_lay_entry_controller', '' ) ;
		$("#cbo_company_name").attr("disabled",true);
		$("#txt_job_no").attr("disabled",true);
		$("#txt_job_year").attr("disabled",true);
		set_button_status(0, permission, 'fnc_cut_lay_info');
	
 	}
}

function fnc_move_cursor(val,id, field_id,lnth,max_val)
{
	var str_length=val.length;
	
	if(str_length==lnth)
	{
		$('#'+field_id).select();
		$('#'+field_id).focus();
	}
	
	if(val>max_val)
	{
		document.getElementById(id).value=max_val;
	}
}
	

function fnc_intime_populate(val2,val1)
{
	var tot_row=$('#emp_tab tr').length;
	var intimeho=document.getElementById(val1).value;
	
	if(val2== '')
	{
		val2='00';
	}
	for(var i=1; i<=tot_row; i++)
	{
		if($("#txtintimehours_"+i).val()== '')
		{
			$("#txtintimehours_"+i).val(intimeho);
			$("#txtintimeminuties_"+i).val(val2);
		}
	}
}  
	
function fnc_outtime_populate(val2,val1)
{
	var tot_row=$('#emp_tab tr').length;
	var outtimeho=document.getElementById(val1).value;
	
	if(val2== '')
	{
		val2='00';
	}
	
	for(var i=1; i<=tot_row; i++)
	{
		if($("#txtouttimehours_"+i).val()== '')
		{
			$("#txtouttimehours_"+i).val(outtimeho);
			$("#txtouttimeminuties_"+i).val(val2);
		}
	}
}


// for report lay chart
function generate_report_lay_chart(data,action)
{
	if(form_validation('txt_cutting_no','Cutting Number')==false)
   {
	return;
   }
	window.open("requires/cut_and_lay_entry_controller.php?data=" + data+'&action='+action, true );
}
//openmypage_country
function openmypage_country(row)
{
	var hidden_country_id=$("#countryId_"+row).val();
	if(form_validation('cbocolor_'+row,'Color')==false)
	{
		return;
	}
	var order_id=$('#cboorderno_'+row).val();
	var gmt_id=$('#cbogmtsitem_'+row).val();
	var gmt_value=$("#cbogmtsitem_"+row).val();
	var color_value=$("#cbocolor_"+row).val();
	var title = 'Country Selection Form';	
	var page_link = 'requires/cut_and_lay_entry_controller.php?hidden_country_id='+hidden_country_id+'&action=country_popup'+'&order_id='+order_id+'&gmt_id='+gmt_id+'&color_value='+color_value;
		
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=300px,center=1,resize=1,scrolling=0','');
	emailwindow.onclose=function()
	{
		var country_id=this.contentDoc.getElementById("hidden_search_id").value;
		var country_name=this.contentDoc.getElementById("hidden_search_name").value;

		$("#countryId_"+row).val(country_id);
		$("#countryName_"+row).val(country_name);
		get_php_form_data( order_id+"_"+gmt_id+"_"+color_value+"_"+row+"_"+country_id, "load_drop_down_order_qty_with_country", "requires/cut_and_lay_entry_controller" );
	}
}
</script>
 
</head>
 
<body onLoad="set_hotkey()">

<div style="width:1200px;" align="center">
	
     <? echo load_freeze_divs ("../../",$permission);  ?>
     <form name="cutandlayentry_1" id="cutandlayentry_1">
    
    <table width="95%" cellpadding="0" cellspacing="2" align="center">
     	<tr>
        	<td width="95%" align="center" valign="top">  <!--   Form Left Container -->
            	<fieldset style="width:1000px;">
                     <legend>cut and lay entry</legend>
                        <table  width="1050" cellspacing="2" cellpadding="0" border="0">
                         <tr>
                            <td colspan="4" align="right"><b>Cutting Number</b></td>
                            <td colspan="4">
                            	<input type="text" name="txt_cutting_no" id="txt_cutting_no" class="text_boxes" style="width:130px" placeholder="Double Click To Search" onDblClick="open_cutting_popup()" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).ondblclick()" readonly />
                            	 <input type="hidden" name="update_id"  id="update_id"  />
                            </td>
                      </tr>
                           <tr>
                                <td width="110" class="must_entry_caption">Company Name</td>              <!-- 11-00030  -->
                                <td width="150" colspan="2">
                                 <? 
                                    echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/cut_and_lay_entry_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data(this.value,'roll_maintained','requires/cut_and_lay_entry_controller' );get_php_form_data(this.value,'size_wise_repeat_cut_no','requires/cut_and_lay_entry_controller' );" );
                                ?>
                                	<input type="hidden" name="size_wise_repeat_cut_no" id="size_wise_repeat_cut_no" readonly>
                                </td>
                                <td  width="110" align="left" >Location </td>
                                <td width="150" id="location_td">
                                     <? 
                                    echo create_drop_down( "cbo_location_name", 140, $blank_array,"", 1, "-- Select Location --", $selected, "" );
                                     ?>
                                </td>
                                <td width="120" align="left" >Floor</td>
                                <td  width="140" id="floor_td">
                                    <? 
									//echo create_drop_down( "cbo_floor_name", 140, "select id,floor_name from lib_prod_floor where production_process=1 and status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 ); 
                                   echo create_drop_down( "cbo_floor_name", 140,"select id,floor_name from lib_prod_floor where production_process=1  and status_active =1 and is_deleted=0","id,floor_name", 1, "-- Select Floor --", $selected, "" );
                                     ?>
                               
                                </td>
                              <td  width="90" align="left">Table No </td>
                                <td width="120">
                                    <input style="width:120px;" type="text"   class="text_boxes_numeric" autocomplete="off"  name="txt_table_no" id="txt_table_no"  />
                                </td>
                          </tr>
                          <tr>
                                <td  align="left">CAD Marker length
                                </td>
                                <td   align="left">
                                   <input style="width:140px;" type="text"  class="text_boxes" autocomplete="off"  name="txt_marker_length" id="txt_marker_length"  />
                                     
                                </td>
                                 <td  width="" align="left">
                                     
                                </td>
                                <td   height="" align="left">CAD Marker Width/Dia  </td>           
                                <td   >
                                        <input style="width:130px;" type="text"  class="text_boxes_numeric" autocomplete="off"  name="txt_marker_width" id="txt_marker_width" />
                                </td>
                               <td  width="" height="" align="left">CAD Fabric Width/Dia </td>         
                                <td  width="" >
                                     <input type="text" name="txt_fabric_width" id="txt_fabric_width" class="text_boxes_numeric" style="width:130px" />
                                    
    
                                </td>
                                <td width="" align="left">CAD GSM</td>
                                <td>
                                     <input style="width:120px;" type="text"  class="text_boxes_numeric" autocomplete="off"  name="txt_gsm" id="txt_gsm" />  
                                </td>
                          </tr>
                           <tr>
                                   <td  width="" class="must_entry_caption">Job No</td>
                                     <td width="" colspan="2" id="job_change_id">
                                              <input style="width:140px;" type="text"  onDblClick="openmypage_jobNo()" class="text_boxes" autocomplete="off" placeholder="Browse/Write" name="txt_job_no" id="txt_job_no" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).ondblclick()" />

                                     </td>
                                     
                                     <td  width="" align="left" >Year</td>
                                     <td>
                                     <?
                                      	     echo create_drop_down( "txt_job_year", 140, $year,"", 1, "-- Select year --", $selected, "change_order(this.value)","");
											 ?>

                                     </td>
                                       <td  width="" align="left">Batch   </td>
                                     <td>
                                          <input style="width:130px;" type="text"   class="text_boxes" autocomplete="off"  name="txt_batch_no" id="txt_batch_no"  />
                                     </td> 
                                      <td  width="" align="left" >Buyer </td>
                                     <td id="buyer_id">
                                          	<? 
                                    echo create_drop_down( "txt_buyer_name", 130,"select id, buyer_name from  lib_buyer","id,buyer_name", 1, "", $selected, "" ,1);
                                     ?>
                                     </td>
                                    
                            </tr>
                             <tr>
                                <td width="" align="left" class="must_entry_caption">Plan Start Date
                                </td>
                                <td  width="" align="left">
                                      <input style="width:140px;" type="text"   class="datepicker" autocomplete="off"  name="txt_entry_date" id="txt_entry_date"  />
                                      <input type="hidden" name="update_job_no"  id="update_job_no"  />
                                      <input type="hidden" name="update_tbl_id"  id="update_tbl_id"  />
                                      <input type="hidden" name="roll_maintained" id="roll_maintained" readonly>
                                       <input type="hidden" name="bundle_bo_creation" id="bundle_bo_creation" readonly>
                               
                                </td>
                                <td  width="" align="left"></td>
                                <td  width="" height="" align="left"> Start Time </td>           
                                <td  width="" >
                                     <input type="text" name="txt_in_time_hours" id="txt_in_time_hours" class="text_boxes_numeric" placeholder="HH"  style="width:30px;"  onKeyUp="fnc_move_cursor(this.value,'txt_in_time_hours','txt_in_time_minuties',2,23);" /> :
                                    <input type="text" name="txt_in_time_minuties" id="txt_in_time_minuties" class="text_boxes_numeric" placeholder="MM"  style="width:30px;" onKeyUp="fnc_move_cursor(this.value,'txt_in_time_minuties','txt_in_time_seconds',2,59)" onBlur="fnc_intime_populate(this.value,'txt_in_time_hours')" />
                                </td>
                                <td width="" align="left" class="must_entry_caption">Plan End Date
                                </td>
                                <td  width="" align="left">
                                      <input style="width:130px;" type="text"   class="datepicker" autocomplete="off"  name="txt_end_date" id="txt_end_date"  />
                                 </td>
                               <td  width="90" height="" align="left"> End Time </td>         
                               <td  width="120" >
                                     <input type="text" name="txt_out_time_hours" id="txt_out_time_hours" class="text_boxes_numeric" placeholder="HH"  style="width:30px;"  onKeyUp="fnc_move_cursor(this.value,'txt_out_time_hours','txt_out_time_minuties',2,23);" /> :
                                    <input type="text" name="txt_out_time_minuties" id="txt_out_time_minuties" class="text_boxes_numeric" placeholder="MM"  style="width:30px;"  onKeyUp="fnc_move_cursor(this.value,'txt_out_time_minuties','txt_out_time_seconds',2,59)" onBlur="fnc_outtime_populate(this.value,'txt_out_time_hours')"/> 
    
                                </td>
                          	</tr>
                          	<tr>
                                <td>Width/Dia Type</td>
                                <td><? echo create_drop_down( "cbo_width_dia", 150, $fabric_typee,"", 1, "-- Select --", "", "",$disabled,"" ); ?></td>
                                <td colspan="2">Lay Fabric Weight</td>
                                <td><input style="width:130px;" type="text"  class="text_boxes_numeric" autocomplete="off" name="txt_lay_wght" id="txt_lay_wght" />
                                <input type="hidden" name="hidden_lay_extra_wgt" id="hidden_lay_extra_wgt">
                                </td>
                                <td>CAD Marker Cons/Dzn</td>
                                <td><input style="width:130px;" type="text"  class="text_boxes_numeric" autocomplete="off" name="txt_marker_cons" id="txt_marker_cons" /></td> 
                                <td align="left">Wastage Qnty. (kg) : </td>
                                <td id=""><input style="width:122px;" type="text"  class="text_boxes_numeric" autocomplete="off" name="txt_wastage_qnty" id="txt_wastage_qnty" onBlur="calculate_efficiency();" /></td>
                      		</tr>
                            <tr>
                            <td>Remark </td>
                            <td><input style="width: 140px;" type="text"  class="text_boxes"  name="txt_remark" id="txt_remark" placeholder="Max length 100 characters" maxlength="100" /></td>
                            <td colspan="2">Efficiency %</td>
                            <td><input type="text" name="txt_efficiency" id="txt_efficiency" class="text_boxes" style="width: 130px;" placeholder="Display" readonly></td>
                            <td></td>
                            <td></td>
                            <td align="left"></td>
                            <td></td>
                            </tr>
                      	</table>
                 	</fieldset>
              </td>
         </tr>
         <tr>
             <td align="center" valign="top" id="po_list_view">
               <fieldset style="width:1200px; margin-top:10px">
                    <legend>Cut and Lay details</legend>
                <table cellpadding="0" cellspacing="0" width="1200" class="rpt_table" border="1" rules="all" id="tbl_order_details">
                    <thead>
                        <th class="must_entry_caption">Order No</th>
                        <th class="must_entry_caption">Order Cut No</th>
                        <th>Ship Date</th>
                        <th>Gmt Item</th>
                        <th>Country</th>
                        <th class="must_entry_caption">Color</th>
                        <th class="must_entry_caption">Plies</th>
                        <th >Size Ratio</th>
                        <th >Marker Qnty</th>
                        <th>Order qty</th>
                        <th>Total Lay qty</th>
                        <th>Lay balance qty</th>
                        <th ></th>
                    </thead>
                    
                    <tbody id="cut_details_container">
                        <tr class="" id="tr_1" style="height:10px;">
                            <td align="center" id="order_id">
                            	<?
							        $sql="select id,job_no_mst,po_number from  wo_po_break_down where  status_active=1";
                            		echo create_drop_down( "cboorderno_1", 120, $blank_array,"id,po_number", 1, "select order", $selected, "");	
								?>		 
                            	<input  type="hidden" id="neworder_1" name="neworder_1" />
                            </td>
                            <td align="center" id="cutNo_1">
                                <input style="width:70px;" class="text_boxes_numeric" type="text" name="orderCutNo_1" id="orderCutNo_1" placeholder=""  />
                            </td>                             
                            <td align="center" id="ship_1">
                                <input style="width:80px;" type="text" class="datepicker" autocomplete="off" name="txtshipdate_1" id="txtshipdate_1" placeholder="Display" disabled readonly/>
                            </td>                              
                            <td align="center" id="garment_1">
                                 <? 
								 	echo create_drop_down( "cbogmtsitem_1", 120, $blank_array,"", 1, "-- Select Item --", $selected, "","");
								  
                                 ?>
                            </td>
                            <td align="center" id="">
                                <input style="width:70px;" class="text_boxes" type="text" name="countryName_1" id="countryName_1" placeholder="Browse"  onDblClick="openmypage_country(1)"/>
                                <input  class="text_boxes" type="hidden" name="countryId_1" id="countryId_1"/>
                            </td>
                              <td align="center" id="color_1"> 
                                 <? 
								     echo create_drop_down( "cbocolor_1", 130, $blank_array,"", 1, "select color", $selected, "");
                                 ?>
                            </td>
                            <td align="center">
                                 <input type="text" name="txtplics_1"  id="txtplics_1" class="text_boxes_numeric"  style="width:80px" placeholder="Double Click" onDblClick="openmypage_roll(1)" readonly/>
                                <input type="hidden" name="hiddenorder_1"  id="hiddenorder_1"  />
                                 <input type="hidden" name="updateDetails_1"  id="updateDetails_1"  />
                               <input type="hidden" name="rollData_1" id="rollData_1" class="text_boxes" readonly />
                               <input type="hidden" name="hiddenExtralRollData_1" id="hiddenExtralRollData_1">
                                <input type="hidden" name="prifix_id"  id="prifix_id"  />
                            </td>
                            <td align="center">
                                <input type="text" name="txtsizeratio_1"  id="txtsizeratio_1" class="text_boxes_numeric"  onDblClick="openmypage_sizeNo(this.id);"  placeholder="Browse" style="width:50px" />
                            </td>
                            <td align="center" id="marker_1">
                                <input type="text" name="txtmarkerqty_1"  id="txtmarkerqty_1" class="text_boxes_numeric"  placeholder="Display" style="width:60px"  readonly/>
                            </td>
                              <td align="center" id="order_1">
                                <input type="text" name="txtorderqty_1"  id="txtorderqty_1" class="text_boxes_numeric"  placeholder="Display" style="width:60px"  readonly/>
                            </td>
                              <td align="center">
                                <input type="text" name="txttotallay_1"  id="txttotallay_1" class="text_boxes_numeric"  placeholder="Display" style="width:60px"  readonly/>
                            </td>
                              <td align="center">
                                <input type="text" name="txtlaybalanceqty_1"  id="txtlaybalanceqty_1" class="text_boxes_numeric"  placeholder="Display" style="width:60px"  readonly/>
                            </td>
                          <td width="70">
                                <input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1)"  onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).onclick()" />
                                <input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                            </td>
                        </tr>
                      </tbody>
                            
                   </table>
                </fieldset> 
                     
				</td>
			</tr>
			<tr>
               <td colspan="4" align="center" class="">
               <input type="hidden" name="txt_batch_no_mandatory" id="txt_batch_no_mandatory" readonly>
                    <? echo load_submit_buttons( $permission, "fnc_cut_lay_info", 0,0,"reset_form('cutandlayentry_1','','','','clear_tr()')",1); ?>
                    <input type="button" id="btn_cost_print" name="btn_cost_print"   style="width:100px;"  class="formbutton" value="Lay Chart"  onClick="generate_report_lay_chart($('#txt_cutting_no').val()+'*'+$('#txt_job_no').val()+'*'+$('#size_wise_repeat_cut_no').val(),'cut_lay_entry_report_print');"/>
                 </td>
                 <td align="left" colspan="4"></td>				
            </tr>
		</table>
    </form>
	</div>
</body>
           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$("#cbo_location_name").val(0);

</script>
</html>