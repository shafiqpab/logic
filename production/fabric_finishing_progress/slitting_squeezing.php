<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Dyeing Production Entry

Functionality	:	
JS Functions	:
Created by		:	Aziz
Creation date 	: 	12-04-2014
Updated by 		: 	Ashraful 	
Update date		: 	28-2-2015   
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
echo load_html_head_contents("Slitting Squeezing Entry Info","../../", 1, 1, "",'1','');

?>
<script>

	<?
		$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][30] );
		echo "var field_level_data= ". $data_arr . ";\n";
	?>


var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	
var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name", "color_name" ), 0, -1); ?>];
$(document).ready(function(e)
 {
		$("#txt_color").autocomplete({
		 source: str_color
	  });
 });
  var str_chemical = [<? echo substr(return_library_autocomplete( "select chemical_name from pro_fab_subprocess where entry_form=30 group by chemical_name", "chemical_name" ), 0, -1); ?>];

	$(document).ready(function(e)
	 {
            $("#txt_chemical").autocomplete({
			 source: str_chemical
		  });
     });


function scan_batchnumber(str)
{
	var cbo_company_id = $('#cbo_company_id').val();
	//var response=return_global_ajax_value( cbo_company_id+"**"+str, 'check_batch_no_scan', '', 'requires/slitting_squeezing_controller');
	$('#txt_batch_no').val(str);
 	$('#cbo_company_id').focus();	return;
	/*var response=response.split("_");
	if(response[0]==0)
	{
		$('#txt_batch_no').val('');
		$('#hidden_batch_id').val(''); 
		//$('#cbo_company_id').val(''); 
		$('#txt_update_id').val(''); 
		$('#cbo_sub_process').val('');
		$('#txt_process_end_date').val('');
		$('#txt_end_hours').val('');
		$('#txt_end_minutes').val('');
		$('#cbo_machine_name').val('');
		$('#txt_remarks').val('');
		reset_form('slittingsqueezing_1','','','','$(\'#list_fabric_desc_container tr:not(:first)\').remove();');
	}
	else
	{
	$('#hidden_batch_id').val(response[1]);
	get_php_form_data(response[1]+'_'+str, "populate_data_from_batch", "requires/slitting_squeezing_controller" );
	var cbo_company_id = $('#cbo_company_id').val();
	
	show_list_view(response[1]+'_'+cbo_company_id,'show_fabric_desc_listview','list_fabric_desc_container','requires/slitting_squeezing_controller','');
	get_php_form_data(document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_floor').value+'**'+document.getElementById('cbo_machine_name').value, 'populate_data_from_machine', 'requires/slitting_squeezing_controller' );
	var roll_variable = $('#roll_maintained').val();
	show_list_view( response[1]+"_"+roll_variable,'show_dtls_list_view','list_container','requires/slitting_squeezing_controller','');
	//$('#txt_batch_no').focus();
	 $('#cbo_company_id').focus();	
	}*/
}


$('#txt_batch_no').live('keydown', function(e) {
	
    if (e.keyCode === 13) 
	 {
     e.preventDefault();
     var batch_no=$('#txt_batch_no').val();
	 scan_batchnumber(batch_no); 
     }
});


function openmypage_batchnum()
{
	
		$("#txt_batch_no").val('');
		var page_link='requires/slitting_squeezing_controller.php?cbo_company_id='+cbo_company_id+'&action=batch_number_popup';
		var title='Batch Number Popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=820px,height=420px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			//var theform=this.contentDoc.forms[0];
			//var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;
			var sysNumber = this.contentDoc.getElementById("hidden_batch_id"); 
			var batch_ids=sysNumber.value.split('_');

			$("#hiddenis_sales").val(batch_ids[2]);
			//alert(batch_id)
			//var batch_ids=batch_id.value.split('_');
		//alert(batch_ids[1])
			if(batch_ids!="")
			{
				freeze_window(5);
				var batch_no= batch_ids[1];
			
				var response=return_global_ajax_value( batch_no, 'check_batch_deying', '', 'requires/slitting_squeezing_controller');
				var response=response.split("_");
				//alert(response[0]);
				if(response[0]==0)
				{
					alert('Without Dyeing production should not Allow.');
					$('#txt_batch_no').val('');
					release_freezing();
					return;
				}
				get_php_form_data(batch_ids[0]+'_'+batch_ids[2]+'_'+batch_no+'_'+batch_ids[2], "populate_data_from_batch", "requires/slitting_squeezing_controller" );
				check_re_sltting();
			
				var cbo_company_id = $('#cbo_company_id').val();
				show_list_view(batch_ids[0]+'_'+cbo_company_id,'show_fabric_desc_listview','list_fabric_desc_container','requires/slitting_squeezing_controller','');
				var roll_variable = $('#roll_maintained').val();
				show_list_view( batch_ids[0]+"_"+roll_variable+'_'+batch_ids[2],'show_dtls_list_view','list_container','requires/slitting_squeezing_controller','');
				
				release_freezing();
			}
			get_php_form_data(document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_floor').value+'**'+document.getElementById('cbo_machine_name').value, 'populate_data_from_machine', 'requires/slitting_squeezing_controller' );
			if($("#roll_maintained").val()==1)
			{
			$('#txt_issue_chalan').focus();
			}
		}
	//}
}

function openmypage_servicebook()
{
	var cbo_company_id = $('#cbo_company_id').val();
	var supplier_id = $('#cbo_service_company').val();
	var process_id = $('#cbo_sub_process').val();
	
	if (form_validation('cbo_company_id*cbo_service_company*txt_batch_no','Company*Service Company*Batch No.')==false)
	{
		return;
	}
	
	var page_link='requires/slitting_squeezing_controller.php?cbo_company_id='+cbo_company_id+'&supplier_id='+supplier_id+'&process_id='+process_id+'&action=service_booking_popup';
	var title='Booking Number Popup';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=420px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var booking_data=this.contentDoc.getElementById("selected_booking").value;
		if(booking_data!="")
		{
			booking_data=booking_data.split("_");
			$("#txt_booking_no").val(booking_data[0]);
			$("#hidden_currency").val(booking_data[1]);
			$("#hidden_exchange_rate").val(booking_data[2]);
			var determination_data=booking_data[3].split("**");
			var determination_data_arr= new Array();
			
			for(var j=0; j<determination_data.length-1; j++)
			{
				var single_data=determination_data[j].split("*");
				determination_data_arr[single_data[0]]=single_data[1];
				
			}
			
			var booking_rate=0;
			var total_row=$("#tbl_item_details tbody tr").length;
			var total_amount=0;
			for(var i=1;i<total_row; i++)
			{
				booking_rate=determination_data_arr[$("#txtdeterid_"+i).val()];
				if(typeof booking_rate!="undefined")
				{
					$("#txtrate_"+i).val(booking_rate);
					var p_qty=$("#txtproductionqty_"+i).val()*1;
					var amount=p_qty*(booking_rate*1);
					total_amount+=amount;
					$("#txtamount_"+i).val(amount);
				}
			}
			$("#total_amount").text(total_amount);
		}

	}
}

function calculate_production_qnty()
{ 
	var numRow = $('#tbl_item_details tbody tr').length-1;
	//alert(numRow);
	var ddd={ dec_type:2, comma:0}
	//math_operation( "total_production_qnty", "txtproductionqty_", "+",numRow,ddd );
	var total_amount=0;
	var total_production=0;
	for(var i=1;i<=numRow; i++)
	{
		var booking_rate=$("#txtrate_"+i).val()*1;
		var prod_qty=$("#txtproductionqty_"+i).val()*1;
		var p_qty=$("#txtproductionqty_"+i).val()*1;
		var amount=p_qty*booking_rate;
		total_amount+=amount;
		total_production+=prod_qty;
		$("#txtamount_"+i).val(amount);
	}
	$("#total_amount").text(total_amount);
	$("#total_production_qnty").text(total_production);
}

/*function check_deying()
{
	var batch_no=$('#txt_batch_no').val();
	
	var cbo_company_id = $('#cbo_company_id').val();
	//if(batch_no!="")
	//{
		var response=return_global_ajax_value( batch_no, 'check_batch_deying', '', 'requires/slitting_squeezing_controller');
		var response=response.split("_");
		alert(response[0]);
		if(response[0]==0)
		{
			alert('Batch no not found in Dyeing Production Entry.');
			return;
			
		}
		
	//}
}*/

function check_batch()
{
	var batch_no=$('#txt_batch_no').val();$('#txt_batch_ID').val('');$('#txt_reslitting_no').val('');
	var cbo_company_id = $('#cbo_company_id').val();
	if(batch_no!="")
	{
		var response=return_global_ajax_value( batch_no, 'check_batch_deying', '', 'requires/slitting_squeezing_controller');
		var response=response.split("_");
		//alert(response[0]);
		if(response[0]==0)
		{
			alert('Without Dyeing production should not Allow.');
			$('#txt_batch_no').val('');
			return;
			
		}
		var response_res=return_global_ajax_value( batch_no, 'check_batch_deying_result', '', 'requires/slitting_squeezing_controller');
		var response_res=response_res.split("_");
		//alert(response[0]);
		if(response_res[0]==1)
		{
			alert('Result='+response_res[2]+' Found');
			$('#txt_batch_no').val('');
			return;
			
		}
		
		var response=return_global_ajax_value( cbo_company_id+"**"+batch_no, 'check_batch_no', '', 'requires/slitting_squeezing_controller');
		var response=response.split("_");
		if(response[0]==0)
		{
			alert('Batch no not found.');
			$('#txt_batch_no').val('');
			$('#hidden_batch_id').val(''); 
			//$('#cbo_company_id').val(''); 
			$('#txt_update_id').val(''); 
			$('#cbo_sub_process').val('');
			$('#txt_process_end_date').val('');
			$('#txt_end_hours').val('');
			$('#txt_end_minutes').val('');
			$('#cbo_machine_name').val('');
			$('#txt_remarks').val('');
			reset_form('slittingsqueezing_1','','','','$(\'#list_fabric_desc_container tr:not(:first)\').remove();');
		}
		else
		{
			$('#hidden_batch_id').val(response[1]);
		
			//get_php_form_data(response[1], "populate_data_from_batch", "requires/slitting_squeezing_controller" );
			var re_slitting_no=$('#txt_reslitting_no').val();
			get_php_form_data(response[1]+'_'+response[2]+'_'+batch_no+'_'+re_slitting_no, "populate_data_from_batch", "requires/slitting_squeezing_controller" );
			check_re_sltting();
			var re_slitting_no=$('#txt_reslitting_no').val();
			var cbo_company_id = $('#cbo_company_id').val();
			var txt_batch_id= $('#txt_batch_ID').val();
			show_list_view(txt_batch_id+'_'+cbo_company_id+'_'+re_slitting_no,'show_fabric_desc_listview','list_fabric_desc_container','requires/slitting_squeezing_controller','');
			var roll_variable = $('#roll_maintained').val();
			show_list_view( txt_batch_id+"_"+roll_variable+'_'+response[2],'show_dtls_list_view','list_container','requires/slitting_squeezing_controller','');
			get_php_form_data(document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_floor').value+'**'+document.getElementById('cbo_machine_name').value, 'populate_data_from_machine', 'requires/slitting_squeezing_controller' );
		 $('#cbo_sub_process').focus();	
		}
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


function fnc_pro_fab_subprocess( operation )
{
	if (operation==0)
	{
		if( form_validation('cbo_company_id*txt_batch_no*txt_process_end_date*txt_process_start_date*txt_batch_ID*cbo_service_source*cbo_service_company','Company* Batch No*Production Date*Start Date*Batch ID*Source*Service Company')==false )
		{
		    return;
		}

		if('<?php echo implode('*',$_SESSION['logic_erp']['mandatory_field'][30]);?>'){
			if (form_validation('<?php echo implode('*',$_SESSION['logic_erp']['mandatory_field'][30]);?>','<?php echo implode('*',$_SESSION['logic_erp']['field_message'][30]);?>')==false)
			{
				return;
			}
		}
	}
	else
	{
		if( form_validation('cbo_company_id*txt_batch_no*txt_process_end_date*txt_process_start_date*txt_process_date*txt_batch_ID*cbo_service_source*cbo_service_company','Company* Batch No*Production Date*Start Date*Process Date*Batch ID*Source*Service Company')==false )
		{
		    return;
		}

		if('<?php echo implode('*',$_SESSION['logic_erp']['mandatory_field'][30]);?>'){
			if (form_validation('<?php echo implode('*',$_SESSION['logic_erp']['mandatory_field'][30]);?>','<?php echo implode('*',$_SESSION['logic_erp']['field_message'][30]);?>')==false)
			{
				return;
			}
		}
	}	
	 
			var txt_ext_id=$('#txt_ext_id').val();
			var re_checkbox=$('#re_checkbox').val();
			if (document.getElementById('re_checkbox').checked==true)
			{
				 document.getElementById('re_checkbox').value=1;
			}
			else
			{
				document.getElementById('re_checkbox').value=0;
				document.getElementById('re_checkbox').checked=false;
			}
			if(txt_ext_id=='')
			{
				document.getElementById('re_checkbox').checked=false;
				document.getElementById('re_checkbox').value=0;
			}
		
			var re_checked=$('#re_checkbox').val();
			if(txt_ext_id>0 && re_checked==0)
			{
				 alert('Please Check Re Dyeing check Box');
				 return;
			}

	
	if($("#cbo_service_source").val()==3)
	{
		if( form_validation('txt_recevied_chalan','Received Chalan')==false )
		{
			return;
		} 
	}
	
	var end_hours=document.getElementById('txt_end_hours').value;	
	var end_minutes=document.getElementById('txt_end_minutes').value;
	var start_hours=document.getElementById('txt_start_hours').value;	
	var start_minutes=document.getElementById('txt_start_minutes').value;
	var reslitting_no=document.getElementById('txt_reslitting_no').value*1;

	if (operation==0)
	{
		if( start_hours=="" ||  start_minutes=="" )
		{
			alert('Hour & Minute Must fill Up');
			return;	
		}
	}
	else
	{
		if( end_hours=="" ||  end_minutes=="" || start_hours=="" ||  start_minutes=="")
		{
			alert('Hour & Minute Must fill Up');
			return;	
		}
	}	
	
	var row_num=$('#tbl_item_details tbody tr').length-1;
	var data_all="";
	var roll_maintained =$("#roll_maintained").val();
  	var page_upto=$('#page_upto').val();
	
	if(operation==1)
		{
			var re_slitting_max=$("#re_reslitting_from").val()*1;
			if( reslitting_no!=re_slitting_max)
			{
				alert("This Batch No is already Re Slitting. Update is not allowed.");
				return;
			}
		}
		

    if((page_upto*1==3 || page_upto*1>3) && roll_maintained*1==1  )
	{
		for (var i=1; i<=row_num; i++)
		{
			if (document.getElementById('checkRow_'+i).checked==true)
			{
				document.getElementById('checkRow_'+i).value=1;
				if (form_validation('txtroll_'+i+'*txtproductionqty_'+i,'Roll*Prod Qnty')==false)
				{
				    return;
				}			 
			}
			else
			{ 
				document.getElementById('checkRow_'+i).value=0;
			}
			data_all+=get_submitted_data_string('txtconscomp_'+i+'*txtgsm_'+i+'*txtbodypart_'+i+'*txtdiawidth_'+i+'*txtbatchqnty_'+i+'*txtprodid_'+i+'*updateiddtls_'+i+'*txtdiawidthID_'+i+'*txtroll_'+i+'*txtproductionqty_'+i+'*txtproductionqty_'+i+'*txtlot_'+i+'*txtyarncount_'+i+'*rollid_'+i+'*checkRow_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtbarcode_'+i,"../../",i);
		}
	}
	else
	{		
		for (var i=1; i<=row_num; i++)
		{
			/*if (form_validation('txtroll_'+i+'*txtproductionqty_'+i,'Roll*Prod Qnty')==false)
			{
			return;
			}*/
			if (document.getElementById('checkRow_'+i).checked==true)
			{
			    document.getElementById('checkRow_'+i).value=1;
			}
			else
			{
			    document.getElementById('checkRow_'+i).value=0;
			}
			data_all+=get_submitted_data_string('txtconscomp_'+i+'*txtgsm_'+i+'*txtbodypart_'+i+'*txtdiawidth_'+i+'*txtbatchqnty_'+i+'*txtprodid_'+i+'*updateiddtls_'+i+'*txtdiawidthID_'+i+'*txtroll_'+i+'*txtproductionqty_'+i+'*txtrate_'+i+'*txtamount_'+i+'*checkRow_'+i,"../../",i);
			
		}
	}
	//alert(data_all);
	var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_id*txt_issue_chalan*txt_issue_mst_id*cbo_service_source*cbo_service_company*txt_recevied_chalan*txt_batch_no*hidden_batch_id*txt_batch_ID*cbo_sub_process*txt_process_end_date*txt_process_date*txt_end_hours*txt_end_minutes*cbo_machine_name*cbo_result_name*txt_remarks*txt_ext_id*txt_trims_weight*txt_update_id*cbo_floor*cbo_shift_name*txt_process_start_date*txt_start_hours*txt_start_minutes*txt_chemical*roll_maintained*txt_booking_no*hidden_exchange_rate*hidden_currency*txt_temparature*txt_speed*txt_feed*txt_steam*txt_advance_prod*cbo_next_process*re_checkbox*txt_reslitting_no',"../../")+data_all+'&total_row='+row_num;
	//alert(data);return;
	freeze_window(operation);
	http.open("POST","requires/slitting_squeezing_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_pro_fab_subprocess_response;
}

function fnc_pro_fab_subprocess_response()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');
		if(reponse[0]==11)
		{
			show_msg(reponse[0]);
			alert(reponse[1]);
			release_freezing();return;
		}
		else if(reponse[0]==0 || reponse[0]==1)
		{
			show_msg(reponse[0]);
			if(reponse[0]==0)
			{
				document.getElementById('txt_reslitting_no').value = reponse[3];
			}
			if(reponse[0]==1)
			{
				$('#txt_reslitting_no').val(''); 
			}
			document.getElementById('txt_update_id').value = '';
			var is_sales = $("#hiddenis_sales").val();
			var batch_id=$("#txt_batch_ID").val();

			//var cbo_company_id = $('#cbo_company_id').val();
			//var roll_maintained=$('#roll_maintained').val();
			//show_list_view(batch_id+'_'+cbo_company_id,'show_fabric_desc_listview','list_fabric_desc_container','requires/slitting_squeezing_controller','');

			show_list_view( batch_id+"_"+reponse[1]+"_"+is_sales,'show_dtls_list_view','list_container','requires/slitting_squeezing_controller','');
			//$("#list_fabric_desc_container").text('');
			//reset_form('slittingsqueezing_1','','','','');
			set_button_status(0, permission, 'fnc_pro_fab_subprocess',1,1);
			release_freezing();
		}
		else if(reponse[0]==100)
		{
			//show_msg(reponse[0]);
			alert(reponse[1]);
			release_freezing();return;
		}
		else if(reponse[0]==13)
		{
			//show_msg(reponse[0]);
			alert(reponse[1]);
			release_freezing();return;
		}
		else
		{
			show_msg(reponse[0]);
			release_freezing();
		}		
	}
}



	function checkbox_all(type)
	{
		var i=0;
		if ( document.getElementById('allcheckbox').checked==true)
		{
			$( "#allcheckbox2" ).prop( "checked", false );
			$( "#allcheckbox3" ).prop( "checked", false );
		}
		/*if($('#allcheckbox').is(':checked'))
		{
			$('#list_fabric_desc_container :checkbox').each(function()
			{
				this.checked = true;
				i++;
			});
		}
		else
		{
			$('#list_fabric_desc_container :checkbox').each(function()
			{
				this.checked = false;
				i++;
			});
		}*/
		var row_num=$('#list_fabric_desc_container  tr').length-1;
		//alert(row_num);
		for (var k=1; k<=row_num; k++)
			{
				//var diawidthType= $('#txtdiawidth_'+k).val()*1;
				//alert(diawidthType); 
				if ( document.getElementById('allcheckbox').checked==true)
				{
						$( "#checkRow_"+k).prop( "checked", true );
				}
				else
				{
					$( "#checkRow_"+k).prop( "checked", false );
				}
			}
			
		
	}
	
	function checkbox_all2(type)
	{
			if ( document.getElementById('allcheckbox2').checked==true)
			{
			$( "#allcheckbox2" ).prop( "checked", true );
				$( "#allcheckbox" ).prop( "checked", false );
				$( "#allcheckbox3" ).prop( "checked", false );
			}
			else
			{
				$( "#allcheckbox2" ).prop( "checked", false );
			}
			var row_num=$('#list_fabric_desc_container  tr').length-1;
		 //alert(row_num);
		     var jj=1;
		   for (var j=1; j<=row_num; j++)
			{
				diawidthTypeid=$('#txtdiawidthID_'+jj).val();
				//alert(diawidthTypeid+'='+j); 
				if ( document.getElementById('allcheckbox2').checked==true)
				{
					if(diawidthTypeid==1)
					{
						$( "#checkRow_"+jj).prop( "checked", true );
					}
					else
					{
						$( "#checkRow_"+jj).prop( "checked", false );
					}
				}
				else
				{
					if(diawidthTypeid==1)
					{
						$( "#checkRow_"+jj).prop( "checked", false );
					}
				}
				jj++;
			}
	}
	
	function checkbox_all3(type)
	{
			if ( document.getElementById('allcheckbox3').checked==true)
			{
				$( "#allcheckbox3" ).prop( "checked", true );
				$( "#allcheckbox" ).prop( "checked", false );
				$( "#allcheckbox2" ).prop( "checked", false );
			}
			else
			{
				$("#allcheckbox3" ).prop( "checked", false );
			}
			var row_num=$('#list_fabric_desc_container  tr').length-1;
		 //alert(row_num);
		     var jj=1;
		   for (var j=1; j<=row_num; j++)
			{
				diawidthTypeid=$('#txtdiawidthID_'+jj).val();
				//alert(diawidthTypeid+'='+j); 
				if ( document.getElementById('allcheckbox3').checked==true)
				{
					if(diawidthTypeid==2)
					{
						$( "#checkRow_"+jj).prop( "checked", true );
					}
					else
					{
						$( "#checkRow_"+jj).prop( "checked", false );
					}
				}
				else
				{
					if(diawidthTypeid==2)
					{
						$( "#checkRow_"+jj).prop( "checked", false );
					}
				}
				jj++;
			}
	}
	


	function fnResetForm()
	{
		reset_form('slittingsqueezing_1','','','','$(\'#list_fabric_desc_container tr:not(:first)\').remove();');
	}
	
	

	function openmypage_process()
	{
		var txt_process_id = $('#txt_process_id').val();
		var title = 'Process Name Selection Form';	
		var page_link = 'requires/slitting_squeezing_controller.php?txt_process_id='+txt_process_id+'&action=process_name_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var process_id=this.contentDoc.getElementById("hidden_process_id").value;	 //Access form field with id="emailfield"
		var process_name=this.contentDoc.getElementById("hidden_process_name").value;
		$('#txt_process_id').val(process_id);
		$('#txt_process_name').val(process_name);
		}
	}
	
	function openmypage_issue_challan()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var batch_id = $('#txt_batch_ID').val();
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/slitting_squeezing_controller.php?cbo_company_id='+cbo_company_id+'&action=issue_challan_popup','Issue Popup', 'width=780px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var issue_id=this.contentDoc.getElementById("hidden_system_id").value;	 //challan Id and Number
			if(issue_id!="")
			{
				
				get_php_form_data(issue_id, "populate_data_from_data", "requires/slitting_squeezing_controller");
				var hidden_roll_id = $('#txt_roll_id').val();
				show_list_view(batch_id+'_'+hidden_roll_id+'_'+cbo_company_id,'show_fabric_issue_listview','list_fabric_desc_container','requires/slitting_squeezing_controller','');
			
				
			}
		}
	}
	
	
	
	
	
	$('#txt_issue_chalan').live('keydown', function(e) {
	
	    if (e.keyCode === 13) 
		 {
	     	e.preventDefault();
		 	check_issue_challan_scan(this.value); 
	     }
     });
	
	function check_issue_challan_scan(str) //Issue Challan
	{
		var issue_chalan=$('#txt_issue_chalan').val();
		var cbo_company_id = $('#cbo_company_id').val();
		var batch_id=$('#hidden_batch_id').val();
		if(issue_chalan!="")
		{
			var response=return_global_ajax_value( cbo_company_id+"**"+issue_chalan, 'check_issue_challan_no_scan', '', 'requires/slitting_squeezing_controller');
			var response=response.split("_");			
			if(response[0]==0)
			{
				alert('Issue Challan  not found.');
				$('#txt_issue_chalan').val('');
				$('#txt_issue_mst_id').val(''); 
				$('#txt_roll_id').val('');
				$('#cbo_service_source').val('');
				$('#cbo_service_company').val('');
				$('#txt_recevied_chalan').val('');
			}
			else
			{
				
				get_php_form_data(response[1], "populate_data_from_data", "requires/slitting_squeezing_controller" );
				var hidden_roll_id = $('#txt_roll_id').val();
				show_list_view(batch_id+'_'+hidden_roll_id+'_'+cbo_company_id,'show_fabric_issue_listview','list_fabric_desc_container','requires/slitting_squeezing_controller','');
			}
		
		}
	}
	
	
	function check_issue_challan() //Issue Challan
	{
		var issue_chalan=$('#txt_issue_chalan').val();
		var cbo_company_id = $('#cbo_company_id').val();
		var batch_id=$('#hidden_batch_id').val();
		if(issue_chalan!="")
		{
			var response=return_global_ajax_value( cbo_company_id+"**"+issue_chalan, 'check_issue_challan_no', '', 'requires/slitting_squeezing_controller');
			var response=response.split("_");
			
			if(response[0]==0)
			{
				alert('Issue Challan  not found.');
				$('#txt_issue_chalan').val('');
				$('#txt_issue_mst_id').val(''); 
				$('#txt_roll_id').val('');
				$('#cbo_service_source').val('');
				$('#cbo_service_company').val('');
				$('#txt_recevied_chalan').val('');
			}
			else
			{
				
				get_php_form_data(response[1], "populate_data_from_data", "requires/slitting_squeezing_controller" );
				var hidden_roll_id = $('#txt_roll_id').val();
				show_list_view(batch_id+'_'+hidden_roll_id+'_'+cbo_company_id,'show_fabric_issue_listview','list_fabric_desc_container','requires/slitting_squeezing_controller','');
			}
		
		}
	}
	
	
	
	function roll_maintain()
	{ 
		var com=$('#cbo_company_id').val();
		get_php_form_data($('#cbo_company_id').val(),'roll_maintained','requires/slitting_squeezing_controller' );
		var roll_maintained=$('#roll_maintained').val();
		var page_upto=$('#page_upto').val();
		
		if((page_upto*1==3 || page_upto*1>3) && roll_maintained==1  )
		{
		//alert(page_upto);
		//$("txt_issue_chalan").removeAttr('disabled');
		$('#txt_issue_chalan').removeAttr('disabled','disabled');
		//$('#txt_issue_chalan').attr('disabled','disabled');
		$('#txt_issue_chalan').attr('placeholder','Write/Browse/Scan');
		$('#roll_status_td').text('Roll No');
		
		}
		else
		{
		$('#roll_status_td').text('Number of Roll');
		$('#txt_issue_chalan').attr('disabled','disabled');
		}
	}
	
function change_css(source_id)	
{
	if(source_id==3)
	{
		document.getElementById('recevied_chalan_td').innerHTML="Received Challan";
		$('#recevied_chalan_td').css('color','blue');
	}
	else
	{
		document.getElementById('recevied_chalan_td').innerHTML="Received Challan";
		$('#recevied_chalan_td').css('color','black');
	}
}
function check_re_sltting()
	{
		var batch_id=$('#hidden_batch_id').val();
		var txt_reslitting_no=$('#txt_reslitting_no').val();
		get_php_form_data(batch_id+'_'+txt_reslitting_no, "populate_restenter_from_data", "requires/slitting_squeezing_controller" );
		//$('#txt_restenter_no').removeAttr('readonly','readonly');
		
	}	
	
</script>
</head>
<body onLoad="set_hotkey();$('#txt_batch_no').focus();">
<div style="width:100%;" align="center">
<? echo load_freeze_divs ("../../",$permission); ?>
    <form name="slittingsqueezing_1" id="slittingsqueezing_1" autocomplete="off" >
    <div style="width:1250px; float:left;"  align="center">   
        <fieldset style="width:1200px;">
        <table cellpadding="0" cellspacing="1" width="1200" border="0" align="center">
            <tr>
                <td width="21%" valign="top">
                    <fieldset>
                    <legend>Input Area</legend>
                    <table cellpadding="0" cellspacing="2" width="100%" id="main_tbl">
                        <tr> 
                            <td width="" class="must_entry_caption">Batch No.</td>
                            <td>
                                <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:122px;" placeholder="Write/Browse/Scan"  onDblClick="openmypage_batchnum();" onChange="check_batch();" />
                                <input type="hidden" name="hidden_batch_id" id="hidden_batch_id" class="text_boxes" readonly />
                               <!-- <input type="hidden" name="txt_ext_id" id="txt_ext_id" style="width:100px;" class="text_boxes" readonly />-->
                            </td>
                        </tr>
                        <tr> 
                            <td width="">Re Sliting No.</td>
                            <td id="re_stenter_td">
                                <input type="text" name="txt_reslitting_no" id="txt_reslitting_no" class="text_boxes_numeric" style="width:122px;"  value="0"  /> 
								<!--onChange="check_re_sltting();"  issue id-13548  -->
								<input type="hidden" name="re_reslitting_from" id="re_reslitting_from" class="text_boxes" readonly />                        
                         </td>
                        </tr>
                        
                        <tr>
                         <td class="must_entry_caption" width="130">Company</td>
                            <td>
                                <?
                                   //load_drop_down('requires/slitting_squeezing_controller', this.value, 'load_drop_floor', 'floor_td' );
								    echo create_drop_down( "cbo_company_id", 135, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "roll_maintain();" );
                                ?>
                                <input type="hidden" name="txt_update_id" id="txt_update_id" style="width:100px;" class="text_boxes" readonly />
                            </td>
                        </tr>
                          <tr>
                            <td class="">Issue Challan</td>
                            <td>
                                <input type="text" name="txt_issue_chalan" id="txt_issue_chalan"  class="text_boxes" style="width:122px;" placeholder="Write/Browse/Scan"  onDblClick="openmypage_issue_challan();" onChange="check_issue_challan();"   />
                                <input type="hidden" name="txt_issue_mst_id" id="txt_issue_mst_id" style="width:100px;" class="text_boxes" readonly />
                                <input type="hidden" name="txt_roll_id" id="txt_roll_id" style="width:100px;" class="text_boxes" readonly />
                                 <input type="hidden" name="roll_maintained" id="roll_maintained" style="width:100px;" class="text_boxes"  /> 				<input type="hidden" name="page_upto" id="page_upto" style="width:30px;" class="text_boxes" />
                            </td>
                        </tr>
                        <tr>
                        <td class="must_entry_caption">Service Source</td>
                        <td>
							<?
                                echo create_drop_down( "cbo_service_source", 135, $knitting_source, "", 1, "-- Select --", $selected, "load_drop_down( 'requires/slitting_squeezing_controller',this.value+'**'+$('#cbo_company_id').val(),'load_drop_down_knitting_com','dyeing_company_td' );change_css(this.value);","","1,3" );
                            ?>
                        </td>
                        
                        </tr>
                        <tr>
                        <td  class="must_entry_caption">Service Company</td>
                        <td id="dyeing_company_td">
                            <?
                                echo create_drop_down( "cbo_service_company", 135, $blank_array,"", 1, "-- Select --", $selected, "","","" );
                            ?>
                        </td>
                        </tr>
                         <tr>
                            <td class="must_entry_caption" id="recevied_chalan_td">Received Chalan</td>
                            <td>
                                <input type="text" name="txt_recevied_chalan" id="txt_recevied_chalan"  class="text_boxes" style="width:122px;"   />
                            </td>
                        </tr>
                         
                        <tr>
                            <td class="">Process </td>
                            <td>
                               <?
								   echo create_drop_down( "cbo_sub_process", 135, $conversion_cost_head_array,"", 0, "-- Select --", 63, "","","63,166,480" );
                               ?>
                                <!--<input type="text" name="txt_process_name" id="txt_process_name" class="text_boxes" style="width:122px;" placeholder="Double Click To Search" onDblClick="openmypage_process();" readonly />
                            <input type="hidden" name="txt_process_id" id="txt_process_id" />-->
                            </td>
                        </tr>
                        <tr>
                            <td >Service Booking</td>
                            <td>
                                <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:122px;" placeholder="Browse"  onDblClick="openmypage_servicebook();" readonly/>
                                  <input type="hidden" name="hidden_exchange_rate" id="hidden_exchange_rate" class="text_boxes" readonly />
                                  <input type="hidden" name="hidden_currency" id="hidden_currency" class="text_boxes" readonly />
                            </td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Production Date</td>
                            <td>
                                <input type="text" name="txt_process_end_date" id="txt_process_end_date" class="datepicker" style="width:122px;" value="<? echo date('d-m-Y');?>" readonly/>
                            </td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Process Start Date</td>
                            <td>
                                <input type="text" name="txt_process_start_date" id="txt_process_start_date" class="datepicker" style="width:122px;" value="<? echo date('d-m-Y');?>" readonly/>
                            </td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Process Start Time</td>
                            <td>
                                 <input type="text" name="txt_start_hours" id="txt_start_hours" class="text_boxes_numeric" placeholder="Hours" style="width:50px;" onKeyUp="fnc_move_cursor(this.value,'txt_start_hours','txt_start_minutes',2,23)" value="<? //echo date('H');?>" />
                                <input type="text" name="txt_start_minutes" id="txt_start_minutes" class="text_boxes_numeric" placeholder="Minutes" style="width:50px;" onKeyUp="fnc_move_cursor(this.value,'txt_start_minutes','txt_end_date',2,59)" value="<? //echo date('i');?>" />
                            </td>
                        </tr>
                        <tr>
                            <td>Process End Date</td>
                            <td>
                                <input type="text" name="txt_process_date" id="txt_process_date" class="datepicker" style="width:122px;" value="<? //echo date('d-m-Y');?>" readonly/>
                            </td>
                        </tr>
                        <tr>
                            <td>Process End Time</td>
                            <td>
                                 <input type="text" name="txt_end_hours" id="txt_end_hours" class="text_boxes_numeric" placeholder="Hours" style="width:50px;" onKeyUp="fnc_move_cursor(this.value,'txt_end_hours','txt_end_minutes',2,23)" value="<?// echo date('H');?>" />
                                <input type="text" name="txt_end_minutes" id="txt_end_minutes" class="text_boxes_numeric" placeholder="Minutes" style="width:50px;" onKeyUp="fnc_move_cursor(this.value,'txt_end_minutes','txt_end_date',2,59)" value="<? //echo date('i');?>" />
                            </td>
                        </tr>
                         <tr>
                            <td class="">Chemical Name</td>
                            <td>
                                <input type="text" name="txt_chemical" id="txt_chemical" class="text_boxes" style="width:122px;"   />
                            </td>
                        </tr>
                        <tr>
                            <td class="">Temparature</td>
                            <td>
                                <input type="text" name="txt_temparature" id="txt_temparature"  class="text_boxes_numeric" style="width:30px;"/>Speed <input type="text" name="txt_speed" id="txt_speed" class="text_boxes_numeric" style="width:30px;" />
                            </td>
                        </tr>
                        <tr>
                            <td class="">Over Feed</td>
                            <td>
                                <input type="text" name="txt_feed" id="txt_feed" class="text_boxes_numeric" style="width:30px;" />Steam <input type="text" name="txt_steam" id="txt_steam" class="text_boxes_numeric" style="width:30px;"   />
                            </td>
                        </tr>
                           <td>Floor</td>
                            <td id="floor_td">
								<?
									 echo create_drop_down( "cbo_floor", 135, "select id,floor_name from lib_prod_floor where status_active=1 and is_deleted=0 order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0,"","","","",4 );
                                ?>
                            </td>
                        <tr>
                        <tr>
                            <td>Machine Name</td>
                            <td id="machine_td">
								<?
									echo create_drop_down("cbo_machine_name", 135, $blank_array,"", 1, "-- Select Machine --", 0, "",0 ); 
                                ?>
                            </td>
                        </tr>
                         <tr>
                        <td id="result_caption">Next Process</td>
                        <td>
                            <?
						
                            echo create_drop_down("cbo_next_process", 135, $dyeing_result, "", 1, "-- Select --", 0, "", 0, "12,13,14,15,16", "", "", "", "");
                            ?>
                        </td>
                         </tr>
                         
                        <tr>
                        <td id="result_caption">Result</td>
                        <td>
                            <?
                            echo create_drop_down("cbo_result_name", 135, $dyeing_result, "", 1, "-- Select Result --", 0, "", 0, "4,11,17,18,19,20,100", "", "", "", "");
                            ?>
                        </td>
                         </tr>
                        <tr>
                            <td>Shift Name</td>
                            <td>
								<?
									echo create_drop_down("cbo_shift_name", 135, $shift_name,"", 1, "-- Select Shift --", 0, "",0 ,"","","","",""); 
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Advance Production Qty</td>
                            <td>
								   <input type="text" name="txt_advance_prod" id="txt_advance_prod" class="text_boxes_numeric" style="width:130px;" />
                            </td>
                        </tr>
                         <tr>
                            <td>Re Dyeing</td>
                            <td>
								   <input type="checkbox" id="re_checkbox" name="re_checkbox"  />
                            </td>
                        </tr>
                        
                        <tr>
                                <td width="100">Remarks:</td>
                                <td>
                                    <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:120px;"  />
                                </td>
                      </tr>
                    </table>
                    </fieldset>
                </td>
                <td width="1%" valign="top">&nbsp;</td>
                <td width="73%" valign="top">
                    <table cellpadding="0" cellspacing="1" width="100%" border="0" align="left">
                        <tr>
                            <td colspan="3"> <center> <legend>Reference Display</legend></center> </td>
                        </tr>
                        <tr>
                            <td  valign="top">
                                <fieldset>
                                    <table width="900" align="left" id="tbl_body1">
                                        <tr>
                                            <td width="70">Batch ID</td>
                                            <td width="110">
                                                <input type="text" name="txt_batch_ID" id="txt_batch_ID" class="text_boxes" style="width:100px;" readonly />
                                                
                                            </td>
                                            <td width="90">Dyeing Start</td>
                                            <td width="110">
                                                <input type="text" name="txt_dying_started" id="txt_dying_started" class="text_boxes" style="width:100px;" readonly  />
                                            </td>
                                       
                                       
                                            <td>Color</td>
                                            <td>
                                                <input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:100px;"  readonly />
                                            </td>
                                        
                                            <td >Dyeing End</td>
                                            <td>
                                                <input type="text" name="txt_dying_end" id="txt_dying_end" class="text_boxes" style="width:100px;" readonly  />
                                            </td>
                                       </tr>
                                        <tr> 
                                            <td>Job No</td>
                                            <td>
                                                <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px;"  readonly />
                                            </td>
                                            <td >M/C Floor</td>
                                            <td id="machine_fg_td">
                                                <input type="text" name="txt_machine_no" id="txt_machine_no" class="text_boxes" style="width:100px;" readonly />
                                            </td>
                                       
                                       
                                            <td >Buyer</td>
                                            <td>
                                                <input type="text" name="txt_buyer" id="txt_buyer" class="text_boxes" style="width:100px;"  readonly />
                                            </td>
                                            <td >M/C Group</td>
                                            <td id="">
                                               <input type="text" name="txt_mc_group" id="txt_mc_group" class="text_boxes" style="width:100px;" value="<? echo $data;?>" readonly />
                                            </td>
                                         </tr>
                                        <tr>
                                            <td>Order No.</td>
                                            <td>
                                                <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:100px;" readonly />
                                            </td>
                                       
                                             <td>Extn.No.</td>
                                            <td>
                                                 <input type="text" name="txt_ext_id" id="txt_ext_id" style="width:100px;" class="text_boxes" readonly />
                                            </td>
                                             <td>Unload End Date</td>
                                            <td>
                                                 <input type="text" name="txt_unload_end_date" id="txt_unload_end_date" style="width:100px;" class="text_boxes" readonly />
                                            </td>
                                             <td>Unload End Time</td>
                                            <td>
                                                 <input type="text" name="txt_unload_end_time" id="txt_unload_end_time" style="width:100px;" class="text_boxes" readonly />
                                            </td>
                                        </tr>
                                        <tr>
                                        	<td width="90">Trims Weight</td>
                                            <td width="110" id="trims_weight_td">
                                            <input type="text" name="txt_trims_weight" id="txt_trims_weight" class="text_boxes" style="width:100px;"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3"> <div id="batch_type" style="color:#F00"> </div> </td>
                                             <td>
                                            </td>
                                            <td> </td>
                                            <td>
                                            </td>
                                            <td width="90"> </td>
                                            <td width="110">
                                            </td>
                                        </tr>
                                    </table>
                                </fieldset>
                              </td>
                        </tr>
                        <fieldset>
                             <legend>Fabric Details </legend>
                                     <table width="940" align="left" class="rpt_table" rules="all" id="tbl_item_details">
                                        <thead>
                                            <tr>
                                                <th width="40">Sl</th> 
                                                <th width="160">Const & Composition</th> 
                                                <th width="60">GSM</th>
                                                <th width="60">Dia/Width</th> 
                                                <th width="65">D/W Type</th>
                                                <th width="50" id="roll_status_td">Roll No.</th>
                                                <th width="65">Barcode</th>
                                                <th width="65">Batch Qty</th>
                                                <th width="65">Prod. Qty</th>
                                                <th width="70">Lot</th>
                                                <th width="60">Yarn Count</th>
                                                <th width="60">Brand</th>
                                                <th width="60" >Rate</th> 
                                                <th width="" >Amount</th>
                                             </tr>
                                        </thead>
                                       <tbody id="list_fabric_desc_container">
                                     	<tr class="general" id="row_<? echo $i; ?>">                                     	
                                          <td width="40" id="sl_<? echo $i; ?>"><input type="checkbox" id="checkRow_<? echo $i; ?>" name="checkRow[]"  checked /> &nbsp; &nbsp;<? echo $i; ?></td>
                                         <td><input type="text" name="txtconscomp_<? echo $i; ?>" id="txtconscomp_<? echo $i; ?>" class="text_boxes" style="width:145px;" value="<? echo $cons_comps; ?>" disabled/></td>
                                          <td><input type="text" name="txtgsm_<? echo $i; ?>" id="txtgsm_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<? echo $gsm; ?>" /></td>
                                          <td><input type="text" name="txtbodypart_<? echo $i; ?>" id="txtbodypart_<? echo $i; ?>" class="text_boxes" style="width:40px;" value="<? echo $dia_width; //$row[csf('width_dia_type')]; ?>" disabled/></td>
                                          <td><input type="text" name="txtdiawidth_<? echo $i; ?>" id="txtdiawidth_<? echo $i; ?>" class="text_boxes" style="width:50px;"  value="<? echo $fabric_typee[$row[csf('width_dia_type')]];?>" disabled/>
                                        <input type="hidden" name="txtdiawidthID_<? echo $i; ?>" id="txtdiawidthID_<? echo $i; ?>" value="<? echo $row[csf('width_dia_type')];?>" readonly /> </td>
                                        <td><input type="text" name="txtroll_<? echo $i; ?>" id="txtroll_<? echo $i; ?>" class="text_boxes_numeric" style="width:35px;" value="<? echo $row[csf('roll_no')];?>"/>
                                         <input type="hidden" name="rollid_<? echo $i; ?>" id="rollid_<? echo $i; ?>" style="width:50px;" value="<? echo $row[csf('roll_id')];?>" class="text_boxes_numeric" /> </td>
                                          <td>
                                            <input type="text" name="txtbarcode_1" id="txtbarcode_1" class="text_boxes_numeric" style="width:65px;" />
                                          </td>
                                                                          
                                          <td><input type="text" name="txtbatchqnty_<? echo $i; ?>" id="txtbatchqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px;" value="<? echo number_format($row[csf('batch_qnty')],2); ?>" disabled/>
                                        <input type="hidden" name="txtprodid_<? echo $i; ?>" id="txtprodid_<? echo $i; ?>"  value="<? echo $row[csf('prod_id')];?>" />
                                        <input type="hidden" name="updateiddtls_<? echo $i; ?>" id="updateiddtls_<? echo $i; ?>" class="text_boxes" value="<? //echo $row[csf('id')];?>" readonly /></td>
                                        <td><input type="text" name="txtproductionqty_<? echo $i; ?>" id="txtproductionqty_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px;"/></td>
                                        <td><input type="text" name="txtlot_<? echo $i; ?>" id="txtlot_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px;"  value="<? echo $lot;?>" disabled /></td>
                                        <td><input type="text" name="txtyarncount_<? echo $i; ?>" id="txtyarncount_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px;"  value="<? echo $yarn_count_value;?>"  disabled /></td>
                                        <td><input type="text" name="txtbrand_<? echo $i; ?>" id="txtbrand_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;"  value="<? echo $brand_value;?>"  disabled/></td>
                                        <td><input type="text" name="txtrate_1" id="txtrate_1" class="text_boxes_numeric" style="width:50px;" readonly/></td>
                                        <td><input type="text" name="txtamount_1" id="txtamount_1" class="text_boxes_numeric" style="width:70px;"  readonly/> </td>
                						</tr>
                                       </tbody>
                                  </table>
                        </fieldset>
                    </table>
                </td>
            </tr>
            
            <tr>
                <td align="center" colspan="4" class="button_container">
                    <?
                        echo load_submit_buttons($permission, "fnc_pro_fab_subprocess", 0,0,"fnResetForm()",1);
                    ?>
                </td>
            </tr>
        </table>
        </fieldset>
         
        </div>
    </form>
    <br>
        <div id="list_container" style="width:800px; margin:0 auto; text-align:center;"></div>
        <input type="hidden" name="hiddenis_sales" id="hiddenis_sales" value="" readonly />
</div>
</body>
<!--<script> set_multiselect('cbo_sub_process','0','0','','0'); </script>-->
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>