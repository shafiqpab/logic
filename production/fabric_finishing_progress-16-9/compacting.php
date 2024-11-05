<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Dyeing Production Entry

Functionality	:	
JS Functions	:
Created by		:	Aziz
Creation date 	: 	15-04-2014
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
echo load_html_head_contents("Compacting  Entry Info","../../", 1, 1, "",'1','');
?>

<script>

<?
	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][33] );
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

	function openmypage_batchnum()
	{
		var roll_maintained=$('#roll_maintained').val();
		var cbo_company_id = $('#cbo_company_id').val();

		/*if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{*/
			var page_link='requires/compacting_controller.php?cbo_company_id='+cbo_company_id+'&action=batch_number_popup';
			var title='Batch Number Popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=880px,height=420px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var batch_ref=this.contentDoc.getElementById("hidden_batch_id").value.split("_");
				if(batch_ref[0]!="")
				{
					//alert(batch_ref[2]);
					freeze_window(5);
					get_php_form_data(batch_ref[0]+'_'+batch_ref[3]+'_'+batch_ref[2], "populate_data_from_batch", "requires/compacting_controller" );
					//document.getElementById('txt_restenter_no').value=batch_ref[2];
					check_re_stenter();
					show_list_view(batch_ref[0]+'_'+roll_maintained,'show_fabric_desc_listview','list_fabric_desc_container','requires/compacting_controller','');
					show_list_view( batch_ref[0]+'_'+batch_ref[2],'show_dtls_list_view','list_container','requires/compacting_controller','');
					release_freezing();
				}
				get_php_form_data(document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_floor').value+'**'+document.getElementById('cbo_machine_name').value, 'populate_data_from_machine', 'requires/compacting_controller' );
				
			}
		//}
	}
	
	function openmypage_servicebook()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var supplier_id = $('#cbo_service_company').val();
		var process_id = $('#txt_process_id').val();
		
		if (form_validation('cbo_company_id*cbo_service_company*txt_batch_no','Company*Service Company*Batch No.')==false)
		{
			return;
		}
		
		var page_link='requires/compacting_controller.php?cbo_company_id='+cbo_company_id+'&supplier_id='+supplier_id+'&process_id='+process_id+'&action=service_booking_popup';
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
					$("#txtrate_"+i).val(booking_rate);
					var p_qty=$("#txtproductionqty_"+i).val()*1;
					var amount=p_qty*(booking_rate*1);
					total_amount+=amount;
					$("#txtamount_"+i).val(amount);
				}
				$("#total_amount").val(total_amount);
			}
	
		}
	}
	
	
	
	function check_batch()
	{
		var batch_no=$('#txt_batch_no').val();$('#txt_batch_ID').val('');$('#hidden_batch_id').val('');$('#txt_restenter_no').val('');
		var cbo_company_id = $('#cbo_company_id').val();
		var roll_maintained=$('#roll_maintained').val();
		if(batch_no!="")
		{
			/*if (form_validation('cbo_company_id','Company')==false)
			{
				return;
			}*/
			var response=return_global_ajax_value( cbo_company_id+"**"+batch_no, 'check_batch_no', '', 'requires/compacting_controller');
			var response=response.split("_");
			//alert(response[0]);
			if(response[0]==0)
			{
				alert('Batch no not found.');
				 var row_num=$('#list_fabric_desc_container  tr').length;
				// alert(row_num)
					   for(var j=1;j<=row_num;j++)
					   {
						  if(j!=1)
						   {
							 $('#list_fabric_desc_container  tr:last').remove();   
						   }
					   }
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
					/*var response2=return_global_ajax_value(batch_no, 'populate_restenter_check', '', 'requires/compacting_controller');
					var response2=response2.split("_");
					//alert(response[0]);
					if(response2[0]==1)
					{
						//alert(response2[0]);
						load_drop_down('requires/compacting_controller', response[1]+'_'+roll_maintained, 'load_drop_down_re_stenter', 're_stenter_td' )
						if (form_validation('txt_restenter_no','Re Stenter No')==false)
						{
							
							$('#txt_restenter_no').focus();return;
						}
					}
					else
					{*/
				var re_compact_no=$('#txt_restenter_no').val();
				get_php_form_data(response[1]+'_'+response[2]+'_'+re_compact_no, "populate_data_from_batch", "requires/compacting_controller" );
				check_re_stenter();
				show_list_view(response[1]+'_'+roll_maintained+'_'+re_compact_no,'show_fabric_desc_listview','list_fabric_desc_container','requires/compacting_controller','');
				show_list_view( response[1],'show_dtls_list_view','list_container','requires/compacting_controller','');
				get_php_form_data(document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_floor').value+'**'+document.getElementById('cbo_machine_name').value, 'populate_data_from_machine', 'requires/compacting_controller' );
					//}
				
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
		
		var service_source=document.getElementById('cbo_service_source').value;
		if (operation==0)
		{
			if(service_source==3)
			{
				if( form_validation('cbo_company_id*txt_batch_no*cbo_service_source*cbo_service_company*txt_recevied_chalan*txt_process_end_date*txt_process_start_date*txt_batch_ID','Company*Batch No*Service Company*Service Source*Recevied Challan*Production Date*Process Start Date*Batch ID')==false )
				{
					return;
				}
			}
			else
			{
				if( form_validation('cbo_company_id*txt_batch_no*cbo_service_source*cbo_service_company*txt_process_end_date*txt_process_start_date*txt_batch_ID','Company*Batch No*Service Source*Service Company*Production Date*Process Start Date*Batch ID')==false )
				{
					return;
				}	
			}

			if('<?php echo implode('*',$_SESSION['logic_erp']['mandatory_field'][33]);?>'){
				if (form_validation('<?php echo implode('*',$_SESSION['logic_erp']['mandatory_field'][33]);?>','<?php echo implode('*',$_SESSION['logic_erp']['field_message'][33]);?>')==false)
				{
					return;
				}
			}
		}
		else
		{
			if(service_source==3)
			{
				if( form_validation('cbo_company_id*txt_batch_no*cbo_service_source*cbo_service_company*txt_recevied_chalan*txt_process_end_date*txt_process_start_date*txt_process_date*txt_batch_ID','Company*Batch No*Service Company*Service Source*Recevied Challan*Production Date*Process Start Date*Process Date*Batch ID')==false )
				{
					return;
				}
			}
			else
			{
				if( form_validation('cbo_company_id*txt_batch_no*cbo_service_source*cbo_service_company*txt_process_end_date*txt_process_start_date*txt_process_date*txt_batch_ID','Company*Batch No*Service Source*Service Company*Production Date*Process Start Date*Process Date*Batch ID')==false )
				{
					return;
				}	
			}

			if('<?php echo implode('*',$_SESSION['logic_erp']['mandatory_field'][33]);?>'){
				if (form_validation('<?php echo implode('*',$_SESSION['logic_erp']['mandatory_field'][33]);?>','<?php echo implode('*',$_SESSION['logic_erp']['field_message'][33]);?>')==false)
				{
					return;
				}
			}
		}	
	
		
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		var roll_maintained=$('#roll_maintained').val();
		//alert(roll_maintained);
		var end_hours=document.getElementById('txt_end_hours').value;	
		var end_minutes=document.getElementById('txt_end_minutes').value;
		var start_hours=document.getElementById('txt_start_hours').value;	
		var start_minutes=document.getElementById('txt_start_minutes').value;
		var restenter_no=document.getElementById('txt_restenter_no').value*1;
		/*if(restenter_no=="") 
		{
			alert('Re Stenter no Must fill Up');
			$('#txt_restenter_no').focus();
			return;		
		}*/
		if(operation==1)
		{
			var re_stenter_max=$("#re_stenter_from").val()*1;
			if( restenter_no!=re_stenter_max)
			{
				alert("This Batch No is already Re Compacting. Update is not allowed.");
				return;
			}
		}

		if (operation==0)
		{
			if( start_hours=="" ||  start_minutes=="")
			{
				alert('Hour & Minute Must fill Up');
				return;
			}
		}
		else
		{
			if( end_hours=="" ||  end_minutes==""  || start_hours=="" ||  start_minutes==""  )
			{
				alert('Hour & Minute Must fill Up');
				return;	
			}
		}
			
		var row_num=$('#tbl_item_details tbody tr').length-1;
		//alert(row_num);return;
		
		var data_all="";
		var roll_maintained=$('#roll_maintained').val();
		var page_upto=$('#page_upto').val();
		if((page_upto*1==7 || page_upto*1>7) && roll_maintained==1  ) // && roll_maintained==1 
		{			
			var total_batch_qnty=$('#total_batch_qnty').val()*1;
			var total_production_qnty=$('#total_production_qnty').val()*1;
				//alert(total_production_qnty);
			if(total_production_qnty>total_batch_qnty)
			{
				alert('Production Qty Should not greater then Batch Qty');
				return;	
			}
			
			for (var i=1; i<=row_num; i++)
			{
				if (form_validation('txtroll_'+i+'*txtproductionqty_'+i,'Roll*Prod Qnty')==false)
				{
					return;
				}
				if (document.getElementById('checkRow_'+i).checked==true)
				{
				    document.getElementById('checkRow_'+i).value=1;
				   //alert(1);	
				}
				else
				{
					document.getElementById('checkRow_'+i).value=0;
				}
				//alert(2);	
			    data_all+=get_submitted_data_string('txtconscomp_'+i+'*txtgsm_'+i+'*txtbatchqnty_'+i+'*txtprodid_'+i+'*updateiddtls_'+i+'*txtdiawidthtype_'+i+'*txtdiawidth_'+i+'*txtdiawidthtypeid_'+i+'*txtroll_'+i+'*txtproductionqty_'+i+'*rollid_'+i+'*checkRow_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtbarcode_'+i,"../../",i);						 
			    //alert(data_all);return;
			}
			
		}
		else
		{
			var total_batch_qnty=$('#total_batch_qnty').val()*1;
			var total_production_qnty=$('#total_production_qnty').val()*1;
				//alert(total_production_qnty);
			if(total_production_qnty>total_batch_qnty)
			{
			    alert('Production Qty Should not greater then Batch Qty');
			    return;	
			}
			
			
			var k=0;
			for (var i=1; i<=row_num; i++)
			{
				
				if (document.getElementById('checkRow_'+i).checked==true)
				{
				    document.getElementById('checkRow_'+i).value=1;
				   //alert(1);	
				}
				else
				{
					document.getElementById('checkRow_'+i).value=0;
				}
				var productionqty=$('#txtproductionqty_'+i).val()*1;
				if(productionqty>0)
				{
				    //prod_arr[k]=k;
				    k++;
				    /*if (form_validation('txtroll_'+i+'*txtproductionqty_'+i,'Roll*Prod Qnty')==false)
					{
					return;
					}*/		
			        data_all+=get_submitted_data_string('txtconscomp_'+i+'*txtgsm_'+i+'*txtdiawidthtype_'+i+'*txtdiawidth_'+i+'*txtbatchqnty_'+i+'*txtprodid_'+i+'*updateiddtls_'+i+'*txtdiawidthtypeid_'+i+'*txtroll_'+i+'*txtproductionqty_'+i+'*rollid_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtbarcode_'+i+'*checkRow_'+i,"../../",i);
				}
			
			}
			
			if(k<1)
				{
					alert("Please Insert Prod Qty At Least One Item");
					return;
				}
		}
		//alert( data_all);return;
	    var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_id*txt_batch_no*txt_batch_ID*hidden_batch_id*txt_process_id*cbo_previous_process*txt_process_end_date*txt_end_hours*txt_end_minutes*cbo_machine_name*txt_remarks*txt_ext_id*txt_update_id*cbo_floor*txt_length_shrinkage*txt_width_shrinkage*txt_spirality*txt_process_date*cbo_shift_name*txt_process_start_date*txt_start_hours*txt_start_minutes*txt_recevied_chalan*cbo_service_company*cbo_service_source*txt_issue_chalan*roll_maintained*txt_roll_id*txt_issue_mst_id*txt_restenter_no*txt_booking_no*txt_trims_weight*hidden_exchange_rate*hidden_currency*cbo_result_name',"../../")+data_all+'&total_row='+row_num;
		//alert (data);return;
	    freeze_window(operation);
	    http.open("POST","requires/compacting_controller.php",true);
	    http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	    http.send(data);
	    http.onreadystatechange = fnc_pro_fab_subprocess_response;
	}
	function fnc_pro_fab_subprocess_response()
	{
		if(http.readyState == 4) 
		{
			//alert (http.responseText);
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);
			
			//alert(response[2]);
			
			if(response[0]==0)
			{
				document.getElementById('txt_restenter_no').value = response[4];
				document.getElementById('txt_update_id').value = '';
				set_button_status(0, permission, 'fnc_pro_fab_subprocess',0,1);
				//reset_form('slittingsqueezing_1','list_container','','','$(\'#list_fabric_desc_container tr:not(:first)\').remove();');
			}
			else if(response[0]==1)
			{
				set_button_status(0, permission, 'fnc_pro_fab_subprocess',1,1);
				document.getElementById('txt_update_id').value ='';
			//load_drop_down('requires/compacting_controller',response[2]+'_'+response[3]+'_'+response[0], 'load_drop_down_re_stenter', 're_stenter_td' )
			$('#txt_restenter_no').val(''); 
			}
			
			//show_list_view(document.getElementById('txt_batch_ID').value,'show_fabric_desc_listview','list_fabric_desc_container','requires/compacting_controller','');
			show_list_view(response[2]+'_'+document.getElementById('roll_maintained').value+'_'+response[4],'show_fabric_desc_listview','list_fabric_desc_container','requires/compacting_controller','');
			reset_form('slittingsqueezing_1','list_container','','','$(\'#list_fabric_desc_container tr:not(:first)\').remove();','txt_batch_ID*txt_job_no*txt_buyer*txt_order_no*txt_color*txt_mc_group*txt_machine_no*txt_slitting_date*txt_ext_id'); 
			show_list_view(response[2]+'_'+response[4],'show_dtls_list_view','list_container','requires/compacting_controller','');
			
			
			release_freezing();
		}
	}
function checkbox_all(type)
{
	var i=0;
	if($('#allcheckbox').is(':checked'))
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
	}
}


	function fnResetForm() //cbo_company_id,2*hidden_batch_id3,txt_batch_ID
	{
		reset_form('slittingsqueezing_1','','','','$(\'#list_fabric_desc_container tr:not(:first)\').remove();');
	}
	function scan_batchnumber(str)
	{
	var cbo_company_id = $('#cbo_company_id').val();
	var roll_maintained=$('#roll_maintained').val();
	//var response=return_global_ajax_value( cbo_company_id+"**"+str, 'check_batch_no_scan', '', 'requires/compacting_controller');
	//var response=response.split("_");
	
	$('#txt_batch_no').val(str);
	$('#cbo_company_id').focus();return;
	/*if(response[0]==0)
		{
			var row_num=$('#list_fabric_desc_container  tr').length;
		   for(var j=1;j<=row_num;j++)
		   {
			  if(j!=1)
			   {
				 $('#list_fabric_desc_container  tr:last').remove();   
			   }
		   }
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
	get_php_form_data(response[1]+'_'+str, "populate_data_from_batch", "requires/compacting_controller" );
	show_list_view(response[1]+'_'+roll_maintained,'show_fabric_desc_listview','list_fabric_desc_container','requires/compacting_controller','');
	get_php_form_data(document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_floor').value+'**'+document.getElementById('cbo_machine_name').value, 'populate_data_from_machine', 'requires/compacting_controller' );
	$('#cbo_company_id').focus();
		}*/
	}
$('#txt_batch_no').live('keydown', function(e) {
	//$('#txt_batch_no').val(''); ;
    if (e.keyCode === 13) {
        e.preventDefault();
        var batch_no=$('#txt_batch_no').val();
		scan_batchnumber(batch_no); 
    }
});
function openmypage_process()
	{
		var txt_process_id = $('#txt_process_id').val();
		var title = 'Process Name Selection Form';	
		var page_link = 'requires/compacting_controller.php?txt_process_id='+txt_process_id+'&action=process_name_popup';
		  
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
		//var batch_no = $('#txt_batch_no').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/compacting_controller.php?cbo_company_id='+cbo_company_id+'&action=issue_challan_popup','Issue Popup', 'width=880px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var issue_id=this.contentDoc.getElementById("hidden_system_id").value;	 //challan Id and Number
			if(issue_id!="")
			{
				get_php_form_data(issue_id, "populate_data_from_data", "requires/compacting_controller");
				//show_list_view(issue_id,'show_fabric_desc_listview_issue','list_fabric_desc_container','requires/heat_setting_controller','');
				var row_num=$('#tbl_item_details tbody tr').length-1;
				//alert(row_num);return;
				var issue_roll = $('#txt_roll_id').val();
				var update_roll_arr=new Array();
				var issue_data=issue_roll.split(',');
				
				for(var k=0; k<issue_data.length; k++)
				{
					update_roll_arr[issue_data[k]] = 1;
				} 
				for (var j=1; j<=row_num; j++)
					{
						var issue_roll_dtls=$('#rollid_'+j).val();
						
						if(update_roll_arr[issue_roll_dtls]==1)	
						{
						document.getElementById('checkRow_'+j).checked==true;
						document.getElementById('checkRow_'+j).value=1;
							
						}
						/*else
						{
						document.getElementById('checkRow_'+j).checked==false;
						document.getElementById('checkRow_'+j).value=0;
						}*/
					}
			}
		}
	}
	function check_issue_challan() //Issue Challan
	{
		var issue_chalan=$('#txt_issue_chalan').val();
		var cbo_company_id = $('#cbo_company_id').val();
		var batch_id=$('#hidden_batch_id').val();
		
		//alert(issue_chalan);
		if(issue_chalan!="")
		{
			/*if (form_validation('cbo_company_id','Company')==false)
			{
				return;
			}*/
			
			var response=return_global_ajax_value( cbo_company_id+"**"+issue_chalan, 'check_issue_challan_no', '', 'requires/compacting_controller');
			var response=response.split("_");
			
			if(response[0]==0)
			{
				alert('Issue Challan  not found.');
				$('#txt_issue_chalan').val('');
				//$('#hidden_batch_id').val(''); 
				$('#txt_issue_mst_id').val(''); 
				//$('#txt_update_id').val(''); 
				$('#txt_roll_id').val('');
				$('#cbo_service_source').val('');
				$('#cbo_service_company').val('');
				$('#txt_recevied_chalan').val('');
				$('#txt_issue_chalan').focus();
				
				//$('#cbo_machine_name').val('');
				//$('#txt_remarks').val('');
			//reset_form('slittingsqueezing_1','','','','$(\'#list_fabric_desc_container tr:not(:first)\').remove();');
				
			}
			else
			{
				//alert(response[1]);
				//$('#hidden_batch_id').val(response[1]);
				//var update_id_dtl=$('#txt_update_id').val(); 
				get_php_form_data(response[1], "populate_data_from_data", "requires/compacting_controller" );
				var row_num=$('#tbl_item_details tbody tr').length-1;
				//alert(row_num);return;
				
				var hidden_roll_id = $('#txt_roll_id').val();
				show_list_view(hidden_roll_id+'_'+batch_id,'issue_show_fabric_desc_listview','list_fabric_desc_container','requires/compacting_controller','');
				var issue_roll = $('#txt_roll_id').val();
				var update_roll_arr=new Array();
				var issue_data=issue_roll.split(',');
				
				for(var k=0; k<issue_data.length; k++)
				{
					update_roll_arr[issue_data[k]] = 1;
				} 
				
				for (var j=1; j<=row_num; j++)
					{
						var issue_roll_dtls=$('#rollid_'+j).val();
						
						if(update_roll_arr[issue_roll_dtls]==1)	
						{
						document.getElementById('checkRow_'+j).checked==true;
						document.getElementById('checkRow_'+j).value=1;
						
						}
						
		
					}
				$('#cbo_service_source').focus();
			}
		
		}
	}
	function roll_maintain()
	{ 
		var com=$('#cbo_company_id').val();
		//alert(com);
		get_php_form_data($('#cbo_company_id').val(),'roll_maintained_data','requires/compacting_controller' );
		var roll_maintained=$('#roll_maintained').val();
		var page_upto=$('#page_upto').val();
		if((page_upto*1==7 || page_upto*1>7) && roll_maintained==1  ) // && roll_maintained==1 
		{
			
			var row_num=$('#tbl_item_details tbody tr').length-1;
			//alert(row_num);
			for (var k=1; k<=row_num; k++)
			{ //alert(k);
			//$('#txtroll_'+k).attr('readOnly','readOnly');
			//$('#txtroll_'+k).attr('readOnly','readOnly');
			$('#txtroll_'+k).attr('readOnly','Display');
			$('#txtroll_'+k).removeAttr('placeholder','readOnly');
				
			}
		$('#txt_issue_chalan').attr('placeholder','Write/Browse/Scan');
		$('#txt_issue_chalan').removeAttr('disabled','disabled');
		//document.getElementById('barcode_no_th').innerHTML='Barcode';
		
		}
		else
		{
			var row_num=$('#tbl_item_details tbody tr').length-1;
			for (var k=1; k<=row_num; k++)
			{
			//$('#txtroll_'+j).attr('placeholder','Display');
			//$('#txtroll_'+j).attr('readOnly','readOnly');
			$('#txtroll_'+k).attr('placeholder','Write');
			$('#txtroll_'+k).removeAttr('readOnly','readOnly');
			//$('#txtroll_'+k).attr('readOnly','readOnly');
			}
			$('#txt_issue_chalan').attr('disabled','disabled');
			//document.getElementById('barcode_no_th').innerHTML='Roll';
			//$('#batch_no_th').css('color','blue');
			
			
		}
	}
	function calculate_production_qnty()
	{ 
		var numRow = $('#tbl_item_details tbody tr').length-1;
		//alert(numRow);
		var ddd={ dec_type:2, comma:0}
		math_operation( "total_production_qnty", "txtproductionqty_", "+",numRow,ddd );
		var total_amount=0;
		for(var i=1;i<=numRow; i++)
		{
			booking_rate=$("#txtrate_"+i).val()*1;
			var p_qty=$("#txtproductionqty_"+i).val()*1;
			var amount=p_qty*booking_rate;
			total_amount+=amount;
			$("#txtamount_"+i).val(amount);
		}
		$("#total_amount").val(total_amount);
	}
	function search_populate(str)
	{
		if(str==3)
		{
			document.getElementById('search_by_th_up').innerHTML="Received Challan";
			$('#search_by_th_up').css('color','blue');
		}
		else
		{
			document.getElementById('search_by_th_up').innerHTML="Received Challan";
			$('#search_by_th_up').css('color','black');
		}
	}
	$('#txt_issue_chalan').live('keydown', function(e) {
	
    if (e.keyCode === 13) 
	 {
     e.preventDefault();
	 check_issue_challan_scan(this.value); 
     }
     });
	 function check_issue_challan_scan(str) //Issue Challan Scan
	{
		var issue_chalan=$('#txt_issue_chalan').val();
		var cbo_company_id = $('#cbo_company_id').val();
		var batch_id=$('#hidden_batch_id').val();
		if(issue_chalan!="")
		{
			var response=return_global_ajax_value( cbo_company_id+"**"+issue_chalan, 'check_issue_challan_no_scan', '', 'requires/compacting_controller');
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
				
				get_php_form_data(response[1], "populate_data_from_data", "requires/compacting_controller" );
				var hidden_roll_id = $('#txt_roll_id').val();
				show_list_view(batch_id+'_'+hidden_roll_id+'_'+cbo_company_id,'issue_show_fabric_desc_listview','list_fabric_desc_container','requires/compacting_controller','');
				$('#cbo_service_source').focus();
			}
		
		}
	}
	function check_re_stenter()
	{
		var batch_id=$('#hidden_batch_id').val();
		var txt_restenter_no=$('#txt_restenter_no').val();
		get_php_form_data(batch_id+'_'+txt_restenter_no, "populate_restenter_from_data", "requires/compacting_controller" );
		//$('#txt_restenter_no').removeAttr('readonly','readonly');
		
	}			
</script>
</head>
<body onLoad="set_hotkey(); $('#txt_batch_no').focus();">
<div style="width:100%;" align="center">
<? echo load_freeze_divs ("../../",$permission); ?>
    <form name="slittingsqueezing_1" id="slittingsqueezing_1" autocomplete="off" >
    <div style="width:1090px; float:left;">   
        <fieldset style="width:1090px;">
        <table cellpadding="0" cellspacing="1" width="1090" border="0" align="left">
            <tr>
                <td width="25%" valign="top">
                    <fieldset>
                    <legend>Input Area</legend>
                    <table cellpadding="0" cellspacing="2" width="100%" id="main_tbl">
                    	
                        <tr> 
                            <td width="" class="must_entry_caption">Batch No.</td>
                            <td>
                                <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:122px;" placeholder="Write/Browse/Scan" maxlength="100" title="Maximum 100 Character" onDblClick="openmypage_batchnum();"  onChange="check_batch();" />
                                <input type="hidden" name="hidden_batch_id" id="hidden_batch_id" class="text_boxes" readonly />
								
                               
                            </td>
                        </tr>
                         <tr> 
                            <td width="">Re Compact No.</td>
                            <td id="re_stenter_td">
                                <input type="text" name="txt_restenter_no" id="txt_restenter_no" class="text_boxes_numeric" style="width:122px;"  value="0" onChange="check_re_stenter();" readonly />  <!--onChange="check_re_stenter();"   -->  
								<input type="hidden" name="re_stenter_from" id="re_stenter_from" class="text_boxes" readonly />                        
                         </td>
                        </tr>
                        <tr>
                         <td class="must_entry_caption" width="130">Company</td>
                            <td>
                                <? //$selected //load_drop_down('requires/compacting_controller', this.value, 'load_drop_floor', 'floor_td' )
                                    echo create_drop_down( "cbo_company_id", 135, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "roll_maintain();" );
                                ?>
                                <input type="hidden" name="txt_update_id" id="txt_update_id" style="width:100px;" class="text_boxes" readonly />
                            </td>
                        </tr>
                         <tr>
                            <td class="">Issue Challan No</td>
                            <td>
                                <input type="text" name="txt_issue_chalan" id="txt_issue_chalan"  class="text_boxes" style="width:122px;" placeholder="Write/Browse/Scan"  onDblClick="openmypage_issue_challan();" onChange="check_issue_challan();"   />
                                <input type="hidden" name="txt_issue_mst_id" id="txt_issue_mst_id" style="width:100px;" class="text_boxes" readonly />
                                <input type="hidden" name="txt_roll_id" id="txt_roll_id" style="width:50px;" class="text_boxes" readonly />
                                 <input type="hidden" name="roll_maintained" id="roll_maintained" style="width:50px;" class="text_boxes" />					<input type="hidden" name="page_upto" id="page_upto" style="width:30px;" class="text_boxes" />
                            </td>
                        </tr>
                        <tr>
                        <td class="must_entry_caption">Service Source</td>
                        <td>
							<?
							
                                echo create_drop_down( "cbo_service_source", 135, $knitting_source, "", 1, "-- Select --", $selected, "load_drop_down( 'requires/compacting_controller',this.value+'**'+$('#cbo_company_id').val(),'load_drop_down_knitting_com','dyeing_company_td' );search_populate(this.value);","","1,3" );
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
                            <td id="search_by_th_up">Received Challan</td>
                            <td>
                                <input type="text" name="txt_recevied_chalan" id="txt_recevied_chalan"  class="text_boxes" style="width:122px;"   />
                            </td>
                        </tr>
                        <tr>
                            <td class="">Process </td>
                            <td>
                               <? //onDblClick="openmypage_process();" placeholder="Double Click To Search"
								   echo create_drop_down( "txt_process_id", 135, $conversion_cost_head_array,"", 0, "-- Select --", 156, "","","156,240" );
                               ?>
                                <!--<input type="hidden" name="txt_process_name" id="txt_process_name" class="text_boxes" style="width:122px;" value="<? //echo $conversion_cost_head_array[156]?>"   readonly />
                            <input type="hidden" name="txt_process_id" id="txt_process_id" value="156" />-->
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
                            <td>Length Shrinkage%</td>
                            <td>
                                <input type="text" name="txt_length_shrinkage" id="txt_length_shrinkage"  class="text_boxes_numeric" style="width:122px;" />
                            </td>
                        </tr>
                         <tr>
                            <td>Width Shrinkage%</td>
                            <td>
                                <input type="text" name="txt_width_shrinkage" id="txt_width_shrinkage"  class="text_boxes_numeric" style="width:122px;"/>
                            </td>
                        </tr>
                         <tr>
                            <td>Spirality</td>
                            <td>
                                <input type="text" name="txt_spirality" id="txt_spirality" class="text_boxes" style="width:122px;" />
                            </td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Production Date</td>
                            <td>
                                <input type="text" name="txt_process_end_date" id="txt_process_end_date" class="datepicker" style="width:122px;"  value="<? echo date('d-m-Y');?>"  readonly/>
                            </td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Process Start Date</td>
                            <td>
                                <input type="text" name="txt_process_start_date" id="txt_process_start_date" class="datepicker" style="width:122px;"  value="<? echo date('d-m-Y');?>"  readonly/>
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
                                 <input type="text" name="txt_end_hours" id="txt_end_hours" class="text_boxes_numeric" placeholder="Hours" style="width:50px;" onKeyUp="fnc_move_cursor(this.value,'txt_end_hours','txt_end_minutes',2,23)" value="<? //echo date('H');?>" />
                                <input type="text" name="txt_end_minutes" id="txt_end_minutes" class="text_boxes_numeric" placeholder="Minutes" style="width:50px;" onKeyUp="fnc_move_cursor(this.value,'txt_end_minutes','txt_end_date',2,59)" value="<? //echo date('i');?>" />
                            </td>
                        </tr>
                        <tr>
                            <td>Production Type</td>
                            <td>
								<?
									 echo create_drop_down( "cbo_previous_process", 135,$fabric_finishing_previous_process,"", 1, "-- Select Process --", $selected, "",0,"","","","","" );
                                ?>
                            </td>
                        </tr>

                         <tr>
                            <td>Floor</td>
                            <td id="floor_td">
								<?
									 echo create_drop_down( "cbo_floor", 135, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 order by floor_name limit 0 ,0","id,floor_name", 1, "-- Select Floor --", $selected, "",0,"","","","",4 );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Machine Name</td>
                            <td id="machine_td">
								<?
									echo create_drop_down("cbo_machine_name", 135, $blank_array,"", 1, "-- Select Machine --", 0, "",0 ); 
                                ?>
                            </td>
                        </tr>
                         <td id="result_caption">Result</td>
                        <td>
                            <?
                            echo create_drop_down("cbo_result_name", 135, $dyeing_result, "", 1, "-- Select Result --", 0, "", 0, "11,12,13,14,15,16", "", "", "", "");
                            ?>
                        </td>
                        <tr>
                            <td>Shift Name</td>
                            <td>
								<?
									echo create_drop_down("cbo_shift_name", 135, $shift_name,"", 1, "-- Select Shift --", 0, "",0 ,"","","","",""); 
                                ?>
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
                <td width="50%" valign="top">
                    <table cellpadding="0" cellspacing="1" width="100%" border="0" align="left">
                        <tr>
                            <td colspan="3"> <center> <legend>Reference Display</legend></center> </td>
                        </tr>
                        <tr>
                            <td width="45%" valign="top">
                                <fieldset>
                                    <table width="820" align="left" id="tbl_body1">
                                        <tr>
                                            <td width="70">Batch ID</td>
                                            <td width="100">
                                            
                                                <input type="text" name="txt_batch_ID" id="txt_batch_ID" class="text_boxes" style="width:100px;" readonly />
                                            </td>
                                            <td width="90">M/C Group </td>
                                            <td width="100">
                                               <input type="text" name="txt_mc_group" id="txt_mc_group" class="text_boxes" style="width:100px;" value="<? echo $data;?>" readonly />
                                            <td> Color</td>
                                            <td>
                                                <input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:100px;"  readonly />
                                            </td>
                                            <td>Dyeing En Date</td>
                                            <td>
                                                  <input type="text" name="txt_dyeing_date" id="txt_dyeing_date" class="text_boxes" style="width:100px;" readonly  />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Job No</td>
                                            <td>
                                                <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px;"  readonly />
                                            </td>
                                            <td>Dyeing En Time</td>
                                            <td id="machine_fg_td">
                                                 <input type="text" name="txt_dyeing_time" id="txt_dyeing_time" class="text_boxes" style="width:100px;" readonly  />
                                            </td>
                                       
                                            <td >Buyer</td>
                                            <td>
                                                <input type="text" name="txt_buyer" id="txt_buyer" class="text_boxes" style="width:100px;"  readonly />
                                            </td>
                                            <td >M/C Floor</td>
                                            <td id="">
                                             <input type="text" name="txt_machine_no" id="txt_machine_no" class="text_boxes" style="width:100px;" readonly />
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
                                            <td width="90">Trims Weight</td>
                                            <td width="110" id="trims_weight_td">
                                            <input type="text" name="txt_trims_weight" id="txt_trims_weight" class="text_boxes" style="width:100px;"/>
                                            </td>
                                        </tr>
                                          <tr>
                                            <td colspan="4"> <div id="batch_type" style="color:#F00"> </div> </td>
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
                                 <table width="500" align="left" class="rpt_table" rules="all" id="tbl_item_details">
                                    <thead>
                                        <th>&nbsp;SL&nbsp;</th> 
                                        <th>Const & Composition</th> 
                                        <th>GSM</th>
                                         <th>Dia/Width</th> 
                                        <th>D/W Type</th>
                                        <th class="must_entry_caption">Roll</th>
                                        <th>Barcode</th>
                                        <th>Batch Qty</th>
                                        <th class="must_entry_caption">Prod. Qty</th>
                                        <th  >Rate</th> 
                                        <th width="" >Amount</th>
                                    </thead>
                                    <tbody id="list_fabric_desc_container">
                                        <tr class="general" id="row_1">
                                         <td width="40">&nbsp;1&nbsp;</td>
                                            <td><input type="text" name="txtconscomp_1" id="txtconscomp_1" class="text_boxes" style="width:180px;" readonly disabled />
                                            </td>
                                            <td><input type="text" name="txtgsm_1" id="txtgsm_1" class="text_boxes_numeric" style="width:40px;"readonly /> </td>
                                             <td><input type="text" name="txtdiawidth_1" id="txtdiawidth_1" class="text_boxes" style="width:50px;" readonly  disabled/>
                                             </td>
                                            <td><input type="text" name="txtdiawidthtype_1" id="txtdiawidthtype_1" class="text_boxes" style="width:70px;" readonly disabled/>
                                                <input type="hidden" name="txtdiawidthtypeid_1" id="txtdiawidthtypeid_1" readonly />
                                            </td>
                                             <td><input type="text" name="txtroll_1" id="txtroll_1" class="text_boxes_numeric" style="width:35px;" />
                                             <input type="hidden" name="rollid_1" id="rollid_1" style="width:50px;" class="text_boxes_numeric" />
                                              </td>
                                              <td>
                                                 <input type="text" name="txtbarcode_1" id="txtbarcode_1" class="text_boxes_numeric" style="width:65px;" />
                                              </td>
                                            <td><input type="text" name="txtbatchqnty_1" id="txtbatchqnty_1" class="text_boxes" style="width:60px;" readonly disabled/>
                                                <input type="hidden" name="txtprodid_1" id="txtprodid_1" />
                                                <input type="hidden" name="updateiddtls_1" id="updateiddtls_1" class="text_boxes" readonly />
                                            </td>
                                             <td><input type="text" name="txtproductionqty_1" id="txtproductionqty_1" class="text_boxes_numeric" onKeyUp="calculate_production_qnty();" style="width:60px;" /></td>
                                             <td><input type="text" name="txtrate_1" id="txtrate_1" class="text_boxes_numeric" style="width:50px;" readonly/></td>
                                            <td><input type="text" name="txtamount_1" id="txtamount_1" class="text_boxes_numeric" style="width:70px;"  readonly/> </td>
                                            
                                        </tr>
                                        <tr>
                                            <td colspan="7" align="right"><b>Sum:</b> <? //echo $b_qty; ?> </td>
                                            <td align="right"><input type="text" name="total_batch_qnty" id="total_batch_qnty" class="text_boxes_numeric" style="width:60px" readonly /></td> 
                                            <td align="right"><input type="text" name="total_production_qnty" id="total_production_qnty" class="text_boxes_numeric" style="width:60px"  readonly /></td>
                                            <td align="right"></td>           
                                            <td align="right"><input type="text" name="total_amount" id="total_amount" class="text_boxes_numeric" style="width:70px" readonly /> </td>
                                        </tr>
                                    </tbody>
                              </table>
                           </fieldset>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                  
                </td>
                <td align="right">&nbsp;</td>
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
         <br>
        <div id="list_container" style="width:800px; margin:0 auto; text-align:center;"></div>
     
        </div>
	</form>
</div>
</body>
<!--<script> set_multiselect('cbo_sub_process','0','0','','0'); </script>
--><script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>