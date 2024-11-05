<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Dyeing Production For Woven Textile Entry

Functionality	:
JS Functions	:
Created by		:	Md Mamun Ahmed Sagor
Creation date 	: 	27-03-2023
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
echo load_html_head_contents("Dyeing Production For Woven Textile Entry Info","../../", 1, 1, "",'1','');
?>

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	<?
	if(count($_SESSION['logic_erp']['data_arr'][35])>0)
	{
	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][35] );
	if($data_arr) echo "var field_level_data= ". $data_arr . ";\n";
	//echo "alert(JSON.stringify(field_level_data));";
	}
	?>
	var today='<?  echo date("d-m-Y")?>';
	var hr='<? echo date('H'); ?>';
	var mint='<? echo date('i'); ?>';
	function openmypage_servicebook()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var supplier_id = $('#cbo_service_company').val();
		var process_id = $('#txt_process_id').val();

		if (form_validation('cbo_company_id*cbo_service_company*txt_batch_no','Company*Service Company*Batch No.')==false)
		{
			return;
		}

		var page_link='requires/dyeing_production_for_woven_textile_entry_controller.php?cbo_company_id='+cbo_company_id+'&supplier_id='+supplier_id+'&process_id='+process_id+'&action=service_booking_popup';
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
					var amount=0;
					booking_rate=determination_data_arr[$("#txtdeterid_"+i).val()];
					if(typeof booking_rate!="undefined")
					{
						$("#txtrate_"+i).val(booking_rate);
						var p_qty=$("#txtprodqnty_"+i).val()*1;
						amount=p_qty*(booking_rate*1);
						total_amount+=amount;
						$("#txtamount_"+i).val(amount);
					}
				}
				$("#total_amount").val(total_amount);
			}

		}
	}

	function openmypage_batchnum()
	{
		if($('#cbo_yesno').val()!=1) $('#txt_system_no').val('');
		var cbo_company_id = $('#cbo_company_id').val();
		var batch_no = $('#txt_batch_no').val();
		var load_unload = $('#cbo_load_unload').val();
		var cbo_dyeing_type = $('#cbo_dyeing_type').val();
		var roll_maintained=$('#roll_maintained').val();
	  	//alert(load_unload);
		if (form_validation('cbo_load_unload','Load_unload')==false)
		{
			return;
		}
		else
		{
			page_link='requires/dyeing_production_for_woven_textile_entry_controller.php?action=batch_number_popup&cbo_company_id='+cbo_company_id+'&batch_no='+batch_no+'&load_unload='+load_unload;

			//var page_link='requires/dyeing_production_for_woven_textile_entry_controller.php?cbo_company_id='+cbo_company_id+'&action=batch_number_popup'+'&load_unload=load_unload';
			var title='Batch Number Popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=420px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var batch_datas=this.contentDoc.getElementById("hidden_batch_id").value;
				if(batch_datas!="")
				{
					batch_data=batch_datas.split("_");
					batch_id=batch_data[0];
					batch_no=batch_data[1];
					func_batch_no=batch_data[2];
					is_sales=batch_data[3];
					entry_form_no=batch_data[4];
					//alert(batch_no);
					$("#txt_batch_no").val(batch_no);
					$('#txt_batch_ID').val(batch_id);
					if(load_unload==2)
					{
						$("#txt_system_no").val(func_batch_no);
					}
					$('#txt_entry_form_no').val(entry_form_no);
					freeze_window(5);
					//get_php_form_data(document.getElementById('cbo_load_unload').value+'_'+response[1]+'_'+batch_no+'_'+response[2], "populate_data_from_batch", "requires/dyeing_production_for_woven_textile_entry_controller" );
					
					$('#txt_process_start_date').val(today);
					$('#txt_process_date').val(today);
					$('#txt_process_end_date').val(today);
					$('#txt_start_hours').val(hr);
					$('#txt_start_minutes').val(mint);
					get_php_form_data(document.getElementById('cbo_load_unload').value+'_'+batch_id+'_'+batch_no+'_'+is_sales+'_'+entry_form_no, "populate_data_from_batch", "requires/dyeing_production_for_woven_textile_entry_controller" );
					var hidden_result_id=$('#hidden_result_id').val();
					var hidden_last_loadunload_id=$('#hidden_last_loadunload_id').val();
					var hidden_double_dyeing=$('#hidden_double_dyeing').val();

					var hidden_control_chemical_issue=$('#hidden_control_chemical_issue').val();
					if (hidden_control_chemical_issue==20) 
					{
						alert('Batch Not Found In Dyes and Chemical Issue');
						fnResetForm();
						release_freezing();return;
					}
					show_list_view(batch_id+'_'+roll_maintained+'_'+document.getElementById('cbo_load_unload').value+'_'+document.getElementById('txt_process_id').value+'_'+entry_form_no+'_'+hidden_double_dyeing+'_'+hidden_result_id+'_'+cbo_dyeing_type,'show_fabric_desc_listview','list_fabric_desc_container','requires/dyeing_production_for_woven_textile_entry_controller','');
					$('#cbo_company_id').attr('disabled','disabled');

					//show_list_view(batch_id+'_'+document.getElementById('cbo_load_unload').value,'show_dtls_batch_list_view','list_container','requires/dyeing_production_for_woven_textile_entry_controller','');
					release_freezing();
				}
				get_php_form_data(document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_floor').value+'**'+document.getElementById('cbo_machine_name').value, 'populate_data_from_machine', 'requires/dyeing_production_for_woven_textile_entry_controller' );
				var cbo_yesno=$('#cbo_yesno').val();
				if(cbo_yesno==1)
				{
				 fnc_load_data(cbo_yesno);
				}

				set_field_level_access( document.getElementById('cbo_company_id').value);

			}
		}
	}
	function check_batch()
	{
		$('#txt_batch_ID').val('');
		var batch_no=$('#txt_batch_no').val();
		var cbo_dyeing_type=$('#cbo_dyeing_type').val();
		var cbo_company_id = $('#cbo_company_id').val();
		var roll_maintained=$('#roll_maintained').val();

		//$('#txt_system_no').val('');
		if($('#cbo_yesno').val()!=1) $('#txt_system_no').val('');
		$('#cbo_company_id').removeAttr('disabled','disabled');
		if(batch_no!="")
		{
			if (form_validation('cbo_load_unload','Load Unload')==false)
			{
				return;
			}
			var response=return_global_ajax_value( cbo_company_id+"**"+batch_no, 'check_batch_no', '', 'requires/dyeing_production_for_woven_textile_entry_controller');
			var response=response.split("_");
			// alert(response[1]);return;
			
			$('#cbo_company_id').val(response[2]);
			if(response[0]==0)
			{
				alert('Batch no not found.');
				$('#txt_batch_no').val('');
				$('#txt_batch_ID').val('');
				$('#txt_entry_form_no').val('');
				$('#hidden_batch_id').val('');
				//$('#cbo_company_id').val('');
				$('#txt_update_id').val('');
				$('#txt_hidden_service_company').val('');
				$('#txt_process_id').val('');
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
				$('#txt_batch_ID').val(response[1]);
				$('#txt_entry_form_no').val(response[3]);
				var system_no=$('#txt_system_no').val();
				//alert(response[1]);
				//get_php_form_data(response[1], "populate_data_from_batch2", "requires/dyeing_production_for_woven_textile_entry_controller" );
				get_php_form_data(document.getElementById('cbo_load_unload').value+'_'+response[1]+'_'+batch_no+'_'+response[2]+'_'+response[3], "populate_data_from_batch", "requires/dyeing_production_for_woven_textile_entry_controller" );

				$('#txt_process_start_date').val(today);
				$('#txt_process_date').val(today);
				$('#txt_process_end_date').val(today);
				$('#txt_start_hours').val(hr);
				$('#txt_start_minutes').val(mint);
				$('#txt_end_minutes').val(mint);
				$('#txt_end_hours').val(hr);
				var hidden_result_id=$('#hidden_result_id').val();
				var hidden_last_loadunload_id=$('#hidden_last_loadunload_id').val();
				var hidden_double_dyeing=$('#hidden_double_dyeing').val();

				var hidden_control_chemical_issue=$('#hidden_control_chemical_issue').val();
				if (hidden_control_chemical_issue==20) 
				{
					alert('Batch Not Found In Dyes and Chemical Issue');
					fnResetForm();
					release_freezing();return;
				}

				show_list_view(response[1]+'_'+roll_maintained+'_'+document.getElementById('cbo_load_unload').value+'_'+document.getElementById('txt_process_id').value+'_'+response[3]+'_'+hidden_double_dyeing+'_'+hidden_result_id+'_'+cbo_dyeing_type,'show_fabric_desc_listview','list_fabric_desc_container','requires/dyeing_production_for_woven_textile_entry_controller','');
				//show_list_view(response[1]+'_'+document.getElementById('cbo_load_unload').value+'_'+document.getElementById('txt_system_no').value,'show_dtls_list_view','list_container','requires/dyeing_production_for_woven_textile_entry_controller','');
				//show_list_view(response[1]+'_'+document.getElementById('cbo_load_unload').value+'_'+document.getElementById('txt_system_no').value,'show_dtls_batch_list_view','list_container','requires/dyeing_production_for_woven_textile_entry_controller','');
				get_php_form_data(document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_floor').value+'**'+document.getElementById('cbo_machine_name').value, 'populate_data_from_machine', 'requires/dyeing_production_for_woven_textile_entry_controller' );
				$('#cbo_company_id').focus();//show_dtls_batch_list_view
				$('#cbo_company_id').attr('disabled','disabled');
				var cbo_yesno=$('#cbo_yesno').val();
				if(cbo_yesno==1)
				{
				 fnc_load_data(cbo_yesno);
				}
			}
		}
		set_field_level_access( document.getElementById('cbo_company_id').value);
	}

	function fnc_pro_fab_subprocess( operation ) //Save Update Here...
	{
		if(operation==4)// print
		{
			//function print_report( data, action, path )
			var report_title=$( "div.form_caption") .html();
			print_report( $('#cbo_company_id').val()+'*'+$('#txt_system_no').val()+'*'+$('#cbo_load_unload').val()+'*'+$('#txt_batch_ID').val()+'*'+report_title, "dyeing_pro_print", "requires/dyeing_production_for_woven_textile_entry_controller")
			//alert(print_report);
			return;
		}
		if( form_validation('cbo_load_unload*total_batch_qnty','Load Unload*Total Ref. Qty')==false )
		{
			return;
		}

		if(operation==2)
		{
			show_msg('13');
			return;
		}
		//total_batch_qnty
		var entry_form_no=$('#txt_entry_form_no').val()*1;
		var hidden_last_loadunload_id=$('#hidden_last_loadunload_id').val();
		var hidden_result_id=$('#hidden_result_id').val();
		var multi_dyeing=$('#hidden_double_dyeing').val();
		if(operation==0)
		{
			if(hidden_result_id>0)
			{
				if(multi_dyeing==1 && hidden_result_id!=4)
				{
					alert('Result is not Incompleted');return;
				}
			}
		}
		
		
		//alert(txt_process_start_date);
		if (document.getElementById('cbo_load_unload').value==1) //Loading Here
		{
			var process_id=$('#txt_process_id').val();
			if(process_id!=31)
			{
				alert("Other then Fabric Dyeing Process, Loading not allowed");
				return;
			}
			if(operation==0)
			{
				if(hidden_last_loadunload_id==1)
				{
					alert("Already Loaded, Please Unload");
					return;
				}
			}
			var service_source=document.getElementById('cbo_service_source').value;
			var service_company=document.getElementById('cbo_service_company').value;
			if(service_source==3)
			{
				if( form_validation('cbo_load_unload*txt_batch_no*cbo_company_id*cbo_service_source*cbo_service_company*txt_recevied_chalan*txt_process_id*cbo_ltb_btb*txt_process_start_date*txt_batch_ID','Load Unload*Batch No*Company*Service Source*Service Company*Received Challan*Process Name*LTB/BTB*Process Date*Batch ID')==false )
				{
					return;
				}
			}
			else
			{
				if( form_validation('cbo_load_unload*txt_batch_no*cbo_company_id*cbo_service_source*cbo_service_company*txt_process_id*cbo_ltb_btb*txt_process_start_date*cbo_floor*cbo_machine_name*txt_batch_ID','Load Unload*Batch No*Company*Service Source*Service Company*Process Name*LTB/BTB*Process Date*Floor*Machine*Batch ID')==false )
				{
					return;
				}
			}
			var start_hours=document.getElementById('txt_start_hours').value;
			var start_minutes=document.getElementById('txt_start_minutes').value;
			if( start_hours=="" ||  start_minutes==""  )
			{
			alert('Hour & Minute Must fill Up');
			return;
			}
			var data_all="";
			var roll_maintained=$('#roll_maintained').val();
			var page_upto=$('#page_upto').val();
			var row_num=$('#tbl_item_details tbody tr').length-1;
			//alert(row_num);return;
			if((page_upto*1==2 || page_upto*1>2) && roll_maintained==1  ) // && roll_maintained==1
			{
			var total_batch_qnty=$('#total_batch_qnty').val()*1;
			var total_production_qnty=$('#total_production_qnty').val()*1;
				//alert(total_production_qnty);
			if(total_production_qnty>total_batch_qnty)
			{
			alert('Production Qty Should not greater than Ref. Qty');
			return;
			}
			var z=1; //var dataAll="";
			for (var i=1; i<=row_num; i++)
			{
				if(entry_form_no!=136)
				{
					if (form_validation('txtroll_'+i+'*txtprodqnty_'+i,'Roll*Prod Qnty')==false)
					{
					return;
					}
				}
				else
				{
					if (form_validation('txtprodqnty_'+i,'Prod Qnty')==false)
					{
					return;
					}
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
			      //  data_all+=get_submitted_data_string('txtconscomp_'+i+'*txtgsm_'+i+'*txtdiawidth_'+i+'*txtdiatype_'+i+'*txtroll_'+i+'*txtbatchqnty_'+i+'*txtprodqnty_'+i+'*hiddendiatypeid_'+i+'*txtlot_'+i+'*rollid_'+i+'*txtyarncount_'+i+'*updateiddtls_'+i+'*txtbrand_'+i+'*txtprodid_'+i+'*checkRow_'+i+'*txtbarcode_'+i+'*txtremark_'+i,"../../",i);
			        //alert(data_all);return;
					 data_all+="&txtconscomp_" + z + "='" + $('#txtconscomp_'+i).val()+"'"+"&txtgsm_" + z + "='" + $('#txtgsm_'+i).val()+"'"+"&txtdiawidth_" + z + "='" + $('#txtdiawidth_'+i).val()+"'"+"&txtdiatype_" + z + "='" + $('#txtdiatype_'+i).val()+"'"+"&txtroll_" + z + "='" + $('#txtroll_'+i).val()+"'"+"&txtbatchqnty_" + z + "='" + $('#txtbatchqnty_'+i).val()+"'"+"&txtprodqnty_" + z + "='" + $('#txtprodqnty_'+i).val()+"'"+"&hiddendiatypeid_" + z + "='" + $('#hiddendiatypeid_'+i).val()+"'"+"&txtlot_" + z + "='" + $('#txtlot_'+i).val()+"'"+"&rollid_" + z + "='" + $('#rollid_'+i).val()+"'"+"&txtyarncount_" + z + "='" + $('#txtyarncount_'+i).val()+"'"+"&updateiddtls_" + z + "='" + $('#updateiddtls_'+i).val()+"'"+"&txtbrand_" + z + "='" + $('#txtbrand_'+i).val()+"'"+"&txtprodid_" + z + "='" + $('#txtprodid_'+i).val()+"'"+"&checkRow_" + z + "='" + $('#checkRow_'+i).val()+"'"+"&txtbarcode_" + z + "='" + $('#txtbarcode_'+i).val()+"'"+"&txtremark_" + z + "='" + $('#txtremark_'+i).val()+"'";
					z++;
					
				 }
			}
			else
			{
			var total_batch_qnty=$('#total_batch_qnty').val()*1;
			var total_production_qnty=$('#total_production_qnty').val()*1;
				//alert(total_production_qnty);
			if(total_production_qnty>total_batch_qnty)
			{
			alert('Production Qty Should not greater than Ref. Qty');
			return;
			}
			var z=1; var data_all="";
			for (var i=1; i<=row_num; i++)
			{
				if(entry_form_no!=136)
				{
					if (form_validation('txtroll_'+i+'*txtprodqnty_'+i,'Roll*Prod Qnty')==false)
					{
					return;
					}
				}
				else
				{
					if (form_validation('txtprodqnty_'+i,'Prod Qnty')==false)
					{
					return;
					}
				}
				//alert(2);
		       // data_all+=get_submitted_data_string('txtconscomp_'+i+'*txtgsm_'+i+'*txtdiawidth_'+i+'*txtdiatype_'+i+'*txtroll_'+i+'*txtbatchqnty_'+i+'*txtprodqnty_'+i+'*hiddendiatypeid_'+i+'*txtlot_'+i+'*rollid_'+i+'*txtyarncount_'+i+'*updateiddtls_'+i+'*txtbrand_'+i+'*txtprodid_'+i+'*txtremark_'+i,"../../",i);
			    data_all+="&txtconscomp_" + z + "='" + $('#txtconscomp_'+i).val()+"'"+"&txtgsm_" + z + "='" + $('#txtgsm_'+i).val()+"'"+"&txtdiawidth_" + z + "='" + $('#txtdiawidth_'+i).val()+"'"+"&txtdiatype_" + z + "='" + $('#txtdiatype_'+i).val()+"'"+"&txtroll_" + z + "='" + $('#txtroll_'+i).val()+"'"+"&txtbatchqnty_" + z + "='" + $('#txtbatchqnty_'+i).val()+"'"+"&txtprodqnty_" + z + "='" + $('#txtprodqnty_'+i).val()+"'"+"&hiddendiatypeid_" + z + "='" + $('#hiddendiatypeid_'+i).val()+"'"+"&txtlot_" + z + "='" + $('#txtlot_'+i).val()+"'"+"&rollid_" + z + "='" + $('#rollid_'+i).val()+"'"+"&txtyarncount_" + z + "='" + $('#txtyarncount_'+i).val()+"'"+"&updateiddtls_" + z + "='" + $('#updateiddtls_'+i).val()+"'"+"&txtbrand_" + z + "='" + $('#txtbrand_'+i).val()+"'"+"&txtprodid_" + z + "='" + $('#txtprodid_'+i).val()+"'"+"&txtremark_" + z + "='" + $('#txtremark_'+i).val()+"'";
					z++;
			}
		    //alert(data_all);
			}
			//alert(operation);
			var update_id=$('#txt_update_id').val();
			var batch_no=$('#txt_batch_no').val();
			var machine_no=$('#cbo_machine_name').val();
			var cbo_company_id = $('#cbo_company_id').val();
			var batch_id = $('#hidden_batch_id').val();
			var yesno = $('#cbo_yesno').val();
			//var multi_dyeing = $('#hidden_double_dyeing').val();

			var response2=0;
			if(multi_dyeing==2) //Is Multi No
			{
			var response2=return_global_ajax_value(cbo_company_id+"**"+batch_no+"**"+batch_id+"**"+machine_no,'check_for_shade_matched','','requires/dyeing_production_for_woven_textile_entry_controller');
			var response2=response2.split("_");
			}
			//alert(1);
			var response=return_global_ajax_value( service_company+"**"+batch_no+"**"+batch_id+"**"+machine_no, 'check_batch_no_for_machine', '', 'requires/dyeing_production_for_woven_textile_entry_controller');
			var response=response.split("_");
			if(service_source!=3)
			{
			var 	response=response;
			}
			else{
			var	response=0;
			}
			//alert(response);
			//alert(2);
			if(operation==0)
			{
				//alert(3);
				if(response2[0]==1)
				{
					alert('This Batch Shade Matched');
					return;
				}
				else
				{
					if(yesno==2)
					{
						var process_start_date=$('#txt_process_start_date').val();
						var cbo_floor=$('#cbo_floor').val();


						/*var response3=return_global_ajax_value( process_start_date+"**"+cbo_floor+"**"+machine_no+"**"+update_id, 'machine_load_status', '', 'requires/dyeing_production_for_woven_textile_entry_controller');
						var response3=response3.split("_");
						if(response3[0]==1)
						{
							alert('This Machine Currently Loaded By = '+response3[1]);return;
						}
						*/


						if(response[0]==1)
						{

								alert('This Machine Currently Loaded By='+response[1]);
								return;

						}
						else
						{
							var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_load_unload*txt_process_start_date*txt_start_hours*txt_start_minutes*cbo_company_id*txt_batch_no*txt_batch_ID*hidden_batch_id*txt_process_id*cbo_machine_name*txt_remarks*txt_ext_id*txt_update_id*cbo_floor*cbo_ltb_btb*txt_water_flow*cbo_yesno*cbo_service_source*cbo_service_company*txt_recevied_chalan*txt_issue_chalan*txt_issue_mst_id*roll_maintained*txt_load_meter*txt_system_no*txt_entry_form_no*hidden_double_dyeing*hidden_result_id*cbo_dyeing_type',"../../")+data_all+'&total_row='+row_num;
						//alert(data);return;
							  freeze_window(operation);
							  http.open("POST","requires/dyeing_production_for_woven_textile_entry_controller.php",true);
							  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
							  http.send(data);
							  http.onreadystatechange = fnc_pro_fab_subprocess_response;
						}
					}
					else if(yesno==1)
					{

						var functional_batch=$('#txt_system_no').val();
						var load_unload=$('#cbo_load_unload').val();
						var process_start_date=$('#txt_process_start_date').val();
						if(functional_batch!="" && load_unload==1)
						{
							var date_response=trim(return_global_ajax_value( functional_batch+"**"+process_start_date, 'check_process_start_date', '', 'requires/dyeing_production_for_woven_textile_entry_controller'));

							var ddd=($('#txt_process_start_date').val()).split("-");
							process_st_date=ddd[2]+"-"+ddd[1]+"-"+ddd[0];
							//alert(process_st_date);
							if(process_st_date!=date_response && date_response!="")
							{
								alert("Process Start Date Must be Same in Functional Batch No "+functional_batch); return;
							}
						}

						var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_load_unload*txt_process_start_date*txt_start_hours*txt_start_minutes*cbo_company_id*txt_batch_no*txt_batch_ID*hidden_batch_id*txt_process_id*cbo_machine_name*txt_remarks*txt_ext_id*txt_update_id*cbo_floor*cbo_ltb_btb*txt_water_flow*cbo_yesno*cbo_service_source*cbo_service_company*txt_recevied_chalan*txt_issue_chalan*txt_issue_mst_id*roll_maintained*txt_load_meter*txt_system_no*txt_entry_form_no*hidden_double_dyeing*hidden_result_id*cbo_dyeing_type',"../../")+data_all+'&total_row='+row_num;
						//alert(data);return;
						freeze_window(operation);
						http.open("POST","requires/dyeing_production_for_woven_textile_entry_controller.php",true);
						http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
						http.send(data);
						http.onreadystatechange = fnc_pro_fab_subprocess_response;
					}
				}
			}//ggg
			else
			{
				if(yesno==1)
				{
					var functional_batch=$('#txt_system_no').val();
					var load_unload=$('#cbo_load_unload').val();
					var process_start_date=$('#txt_process_start_date').val();
					if(functional_batch!="" && load_unload==1)
					{
						var date_response=trim(return_global_ajax_value( functional_batch+"**"+process_start_date, 'check_process_start_date', '', 'requires/dyeing_production_for_woven_textile_entry_controller'));
						var ddd=($('#txt_process_start_date').val()).split("-");
						process_st_date=ddd[2]+"-"+ddd[1]+"-"+ddd[0];
						 if(process_st_date!=date_response && date_response!="")
						 {
							alert("Process Start Date Must be Same in Functional Batch No"+functional_batch); return;
						 }
					}
				}
				if(yesno==2)
				{
					var process_start_date=$('#txt_process_start_date').val();
					var cbo_floor=$('#cbo_floor').val();

					if(service_source!=3)
					{
						var response3=return_global_ajax_value( process_start_date+"**"+cbo_floor+"**"+machine_no+"**"+update_id, 'machine_load_status', '', 'requires/dyeing_production_for_woven_textile_entry_controller');
						var response3=response3.split("_");
						if(response3[0]==1)
						{
							//alert('This Machine Currently Loaded By = '+response3[1]);return;
						}
					}
				}


				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_load_unload*txt_process_start_date*txt_start_hours*txt_start_minutes*cbo_company_id*txt_batch_no*txt_batch_ID*hidden_batch_id*txt_process_id*cbo_machine_name*txt_remarks*txt_ext_id*txt_update_id*cbo_floor*cbo_ltb_btb*txt_water_flow*cbo_yesno*cbo_service_source*cbo_service_company*txt_recevied_chalan*txt_issue_chalan*txt_issue_mst_id*roll_maintained*txt_load_meter*txt_system_no*txt_entry_form_no*hidden_double_dyeing*hidden_result_id*cbo_dyeing_type',"../../")+data_all+'&total_row='+row_num;
				  //alert (data);return;
			 	 freeze_window(operation);
			 	 http.open("POST","requires/dyeing_production_for_woven_textile_entry_controller.php",true);
			 	 http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			 	 http.send(data);
			 	 http.onreadystatechange = fnc_pro_fab_subprocess_response;
			}
		}

		if (document.getElementById('cbo_load_unload').value==2) //unloading here
		{
			
			//alert(operation);
			if(operation==0)
			{
				if(hidden_last_loadunload_id==2)
				{
					alert("Already Unload");
					return;
				}
			}
			var service_source=document.getElementById('cbo_service_source').value;
			if(service_source==3)
			{
				if( form_validation('cbo_load_unload*txt_batch_no*cbo_company_id*cbo_service_source*cbo_service_company*txt_recevied_chalan*txt_process_id*cbo_ltb_btb*txt_process_end_date*txt_process_date*cbo_result_name*txt_batch_ID','Load Unload*Batch No*Company*Service Source*Service Company*Received Challan*Process Name*LTB/BTB*Production Date*Process Date*Result*Batch ID')==false )				{
					return;
				}
			}
			else
			{
				if( form_validation('cbo_load_unload*txt_batch_no*cbo_company_id*cbo_service_source*cbo_service_company*txt_process_id*cbo_ltb_btb*txt_process_end_date*txt_process_date*cbo_floor*cbo_machine_name*txt_batch_ID','Load Unload*Batch No*Company*Service Source*Service Company*Process Name*LTB/BTB*Production Date*Process Date*Floor*Machine*Batch ID')==false )				{
					return;
				}
			}
			var txt_process_id=$('#txt_process_id').val();
			if(txt_process_id==31)
			{
				if(form_validation('cbo_result_name','Result')==false )
				{
					return;
				}

			}


			var end_hours=document.getElementById('txt_end_hours').value;
			var end_minutes=document.getElementById('txt_end_minutes').value;
			if( end_hours=='' ||  end_minutes=='' )
			{
			alert('Hour & Minute Must Be fill Up');
			return;
			}
			var dyeing_started_date=$('#txt_dying_started').val();
			//alert(dyeing_started_date);return;
			var dyeing_end_date=$('#txt_process_date').val();
			var txt_ext_id=$('#txt_ext_id').val();
			
			var cbo_responsibility=$('#cbo_responsibility').val();
			if(txt_ext_id>0 && cbo_responsibility==0)
			{
				alert('Please Select responsibility dept.');
				$('#cbo_responsibility').focus();
				return;	
			}
			var load_hr_min=$('#txt_dying_end_load').val();
			var min_hr=load_hr_min.split(':');
			var load_hr=min_hr[0]*1;
			var load_min=min_hr[1]*1;
			//alert(min_hr);return;
			var end_hour=$('#txt_end_hours').val()*1;
			var end_minute=$('#txt_end_minutes').val()*1;
			var unload_hr_min=end_hours+':'+end_minutes;
			//alert(dyeing_started_date+'>'+dyeing_end_date);
			//alert(load_hr_min);return;

			//var date_time_response=return_global_ajax_value( cbo_company_id+"**"+batch_no+"**"+batch_id+"**"+dyeing_end_date+"**"+dyeing_started_date+"**"+unload_hr_min+"**"+load_hr_min, 'check_date_time', '', 'requires/dyeing_production_for_woven_textile_entry_controller');

			//if( dyeing_started_date>dyeing_end_date)
			if(date_compare(dyeing_started_date, dyeing_end_date)==false)
			{
			alert('Process End date & Time should be greater than start date & Time');
			return;
			}
			if(dyeing_started_date==dyeing_end_date)
			{
				if(load_hr_min>unload_hr_min)
					{
					alert('Process End date & Time should be greater than start date & Time');
					return;
					}

			}


			var load_end_hour_minute_data=return_global_ajax_value( $('#hidden_batch_id').val(), 'get_load_end_hour_minute', '', 'requires/dyeing_production_for_woven_textile_entry_controller');
			//alert(load_end_hour_minute_data+'=='+end_hour+':'+end_minute);
			if(load_end_hour_minute_data==end_hour+':'+end_minute){
				alert("Loading Time: "+load_end_hour_minute_data+". Loading end time same not allowed when unload a batch. Please change end time.");
				return;
			}
			
			 
			//var yesno = $('#cbo_yesno').val();
			//if(yesno==1)
			//{
				var functional_batch=$('#txt_system_no').val();
				var load_unload=$('#cbo_load_unload').val();
				var result_name_id=$('#cbo_result_name').val();
				var process_end_date=$('#txt_process_end_date').val();
			 
					//var end_hour=$('#txt_end_hours').val()*1;
			//var end_minute=$('#txt_end_minutes').val()*1;
				
				if(functional_batch!="" && load_unload==2)
				{
					var date_response=trim(return_global_ajax_value( functional_batch+"**"+process_start_date, 'check_process_production_date', '', 'requires/dyeing_production_for_woven_textile_entry_controller'));

					var date_prod_date=date_response.split("#");
					var prod_date=date_prod_date[0];
					var tot_count_batch_no=date_prod_date[1]*1;
					
					var ddd=($('#txt_process_end_date').val()).split("-");
					production_date=ddd[2]+"-"+ddd[1]+"-"+ddd[0];
					
					//unload_end_hr=date_prod_date[2]*1;
					//unload_end_min=date_prod_date[3]*1;
					
					//var unloaded_hr_min=unload_end_hr*1+'.'+unload_end_min*1;
					var from_date=ddd[0]+"-"+ddd[1]+"-"+ddd[2];
						var ttt=date_prod_date[0].split("-");
					var to_date=ttt[2]+"-"+ttt[1]+"-"+ttt[0];
					
					//var chk_unload_hr_min=end_hours*1+'.'+end_minutes*1;
					
					//alert(process_st_date);
					 
						if(production_date!=prod_date && prod_date!="")
						{
							if(tot_count_batch_no>1)
							{
							alert("Productuon Date Must be Same in Functional Batch No "+functional_batch); return;
							}
						}
					 
					/*if(operation==1) //Update unloaded_hr_min
					{
						//alert(to_date+'='+from_date+'='+unloaded_hr_min+'='+chk_unload_hr_min);
						if(date_compare(to_date, from_date)==false)
						{
							// alert(from_date+'='+to_date+'='+unload_hr_min+'='+unloaded_hr_min);
							if(tot_count_batch_no>1)
							{
							alert("Productuon Date Must be Same in Functional Batch No "+functional_batch); return;
							}
						}
					}*/
					 
				}
			//}
			//return;
			var data_all="";
			var roll_maintained=$('#roll_maintained').val();
			var page_upto=$('#page_upto').val();
			var row_num=$('#tbl_item_details tbody tr').length-1;
			if((page_upto*1==2 || page_upto*1>2) && roll_maintained==1  ) // Unload Roll
			{
					var z=1; //var dataAll="";
				for (var i=1; i<=row_num; i++)
				{

					if(entry_form_no!=136)
					{
						if (form_validation('txtroll_'+i+'*txtprodqnty_'+i,'Roll*Prod Qnty')==false)
						{
						return;
						}
					}
					else
					{
						if (form_validation('txtprodqnty_'+i,'Prod Qnty')==false)
						{
						return;
						}
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
					// data_all+=get_submitted_data_string('txtconscomp_'+i+'*txtgsm_'+i+'*txtdiawidth_'+i+'*txtdiatype_'+i+'*txtroll_'+i+'*txtbatchqnty_'+i+'*txtprodqnty_'+i+'*hiddendiatypeid_'+i+'*txtlot_'+i+'*rollid_'+i+'*txtyarncount_'+i+'*updateiddtls_'+i+'*txtbrand_'+i+'*txtprodid_'+i+'*checkRow_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtbarcode_'+i+'*txtremark_'+i,"../../",i);
					data_all+="&txtconscomp_" + z + "='" + $('#txtconscomp_'+i).val()+"'"+"&txtgsm_" + z + "='" + $('#txtgsm_'+i).val()+"'"+"&txtdiawidth_" + z + "='" + $('#txtdiawidth_'+i).val()+"'"+"&txtdiatype_" + z + "='" + $('#txtdiatype_'+i).val()+"'"+"&txtroll_" + z + "='" + $('#txtroll_'+i).val()+"'"+"&txtbatchqnty_" + z + "='" + $('#txtbatchqnty_'+i).val()+"'"+"&txtprodqnty_" + z + "='" + $('#txtprodqnty_'+i).val()+"'"+"&hiddendiatypeid_" + z + "='" + $('#hiddendiatypeid_'+i).val()+"'"+"&txtlot_" + z + "='" + $('#txtlot_'+i).val()+"'"+"&rollid_" + z + "='" + $('#rollid_'+i).val()+"'"+"&txtyarncount_" + z + "='" + $('#txtyarncount_'+i).val()+"'"+"&updateiddtls_" + z + "='" + $('#updateiddtls_'+i).val()+"'"+"&txtbrand_" + z + "='" + $('#txtbrand_'+i).val()+"'"+"&txtprodid_" + z + "='" + $('#txtprodid_'+i).val()+"'"+"&checkRow_" + z + "='" + $('#checkRow_'+i).val()+"'"+"&txtrate_" + z + "='" + $('#txtrate_'+i).val()+"'"+"&txtamount_" + z + "='" + $('#txtamount_'+i).val()+"'"+"&txtbarcode_" + z + "='" + $('#txtbarcode_'+i).val()+"'"+"&txtremark_" + z + "='" + $('#txtremark_'+i).val()+"'";
					z++;
				}
			}
			else
			{
				var z=1; var dataAll="";
			for (var i=1; i<=row_num; i++)
			{

				if(entry_form_no!=136)
				{
					if (form_validation('txtroll_'+i+'*txtprodqnty_'+i,'Roll*Prod Qnty')==false)
					{
					return;
					}
				}
				else
				{
					if (form_validation('txtprodqnty_'+i,'Prod Qnty')==false)
					{
					return;
					}
				}



			 	//data_all+=get_submitted_data_string('txtconscomp_'+i+'*txtgsm_'+i+'*txtdiawidth_'+i+'*txtdiatype_'+i+'*txtroll_'+i+'*txtbatchqnty_'+i+'*txtprodqnty_'+i+'*hiddendiatypeid_'+i+'*txtlot_'+i+'*rollid_'+i+'*txtyarncount_'+i+'*updateiddtls_'+i+'*txtbrand_'+i+'*txtprodid_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtremark_'+i,"../../",i);
				 data_all+="&txtconscomp_" + z + "='" + $('#txtconscomp_'+i).val()+"'"+"&txtgsm_" + z + "='" + $('#txtgsm_'+i).val()+"'"+"&txtdiawidth_" + z + "='" + $('#txtdiawidth_'+i).val()+"'"+"&txtdiatype_" + z + "='" + $('#txtdiatype_'+i).val()+"'"+"&txtroll_" + z + "='" + $('#txtroll_'+i).val()+"'"+"&txtbatchqnty_" + z + "='" + $('#txtbatchqnty_'+i).val()+"'"+"&txtprodqnty_" + z + "='" + $('#txtprodqnty_'+i).val()+"'"+"&hiddendiatypeid_" + z + "='" + $('#hiddendiatypeid_'+i).val()+"'"+"&txtlot_" + z + "='" + $('#txtlot_'+i).val()+"'"+"&rollid_" + z + "='" + $('#rollid_'+i).val()+"'"+"&txtyarncount_" + z + "='" + $('#txtyarncount_'+i).val()+"'"+"&updateiddtls_" + z + "='" + $('#updateiddtls_'+i).val()+"'"+"&txtbrand_" + z + "='" + $('#txtbrand_'+i).val()+"'"+"&txtprodid_" + z + "='" + $('#txtprodid_'+i).val()+"'"+"&txtremark_" + z + "='" + $('#txtremark_'+i).val()+"'"+"&txtrate_" + z + "='" + $('#txtrate_'+i).val()+"'"+"&txtamount_" + z + "='" + $('#txtamount_'+i).val()+"'";
					z++;
			}

			//alert(data_all);return;

				}

			var process_id=$('#txt_process_id').val();
			var batch_no=$('#txt_batch_no').val();
			var cbo_company_id = $('#cbo_company_id').val();
			var batch_id = $('#hidden_batch_id').val();
			var response=return_global_ajax_value( cbo_company_id+"**"+batch_no+"**"+batch_id+"**"+process_id, 'check_batch_no_load', '', 'requires/dyeing_production_for_woven_textile_entry_controller');
			var response=response.split("_");
			if(response[0]==0)
				{
					//alert(process_id);
					//if(process_id==31)
					//{
						alert('Without Load  Unload Not Allow ');
						return;
					//}
				}
				else
				{
					
					if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][35]);?>'){
						if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][35]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][35]);?>')==false)
						{
							return;
						}
					}			
					
					
					var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_load_unload*txt_process_end_date*txt_end_hours*txt_end_minutes*cbo_company_id*txt_batch_no*txt_batch_ID*hidden_batch_id*txt_process_id*cbo_machine_name*cbo_result_name*txt_remarks*txt_ext_id*txt_update_id*cbo_floor*cbo_ltb_btb*txt_water_flow*cbo_shift_name*txt_process_date*cbo_service_source*cbo_service_company*txt_recevied_chalan*txt_issue_chalan*txt_issue_mst_id*roll_maintained*cbo_fabric_type*txt_booking_no*hidden_exchange_rate*hidden_currency*txt_unload_meter*txt_system_no*txt_entry_form_no*hidden_double_dyeing*hidden_result_id*cbo_dyeing_type*cbo_responsibility',"../../")+data_all+'&total_row='+row_num;
				//alert (data);
			  freeze_window(operation);
			  http.open("POST","requires/dyeing_production_for_woven_textile_entry_controller.php",true);
			  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			  http.send(data);
			  http.onreadystatechange = fnc_pro_fab_subprocess_response;
			}
		}
	}

	function fnc_pro_fab_subprocess_response()
	{
		if(http.readyState == 4)
		{
			//alert (http.responseText);
			var response=trim(http.responseText).split('**');
			if(response[0]==0)
			{
				show_msg(response[0]);
				//set_button_status(1, permission, 'fnc_pro_fab_subprocess',1,1);

				document.getElementById('txt_update_id').value = '';
				//alert(response[3]);
				document.getElementById('txt_system_no').value = response[3];
				set_button_status(0, permission, 'fnc_pro_fab_subprocess',0,1);
				release_freezing();
			}
			else if(response[0]==1)
			{
				show_msg(response[0]);
				set_button_status(0, permission, 'fnc_pro_fab_subprocess',1,1);
				release_freezing();
				document.getElementById('txt_update_id').value = '';
			}
			else if(response[0]==100)
			{
				alert('Without Load Unload Not Allow');
				//show_msg(response[0]);
				release_freezing();
				return;
			}
			else if(response[0]==23)
			{
				alert(response[1]);
				//show_msg(response[0]);
				$("#txt_batch_no").focus();
				release_freezing();
				return;
			}
			else if(response[0]==11)
			{
				alert('Duplicate Unload Data Found');
				//show_msg(response[0]);
				release_freezing();
				return;
			}
			else if(response[0]==111)
			{
				alert(response[1]);
				//show_msg(response[0]);
				release_freezing();
				return;
			}
			else if(response[0]==101)
			{
				alert(response[1]);
				//show_msg(response[0]);
				release_freezing();
				return;
			}
			else if(response[0]==13)
			{
				alert('Duplicate Load Data Found');
				//show_msg(response[0]);
				release_freezing();
				return;
			}
			else if(response[0]==14)
			{
				alert(response[1]);
				//show_msg(response[0]);
				release_freezing();
				return;
			}
			else if(response[0]==10)
			{
				show_msg(response[0]);
				release_freezing();
				return;
			}
			
			//show_list_view(document.getElementById('txt_batch_ID').value+'_'+document.getElementById('roll_maintained').value+'_'+document.getElementById('cbo_load_unload').value,'show_fabric_desc_listview','list_fabric_desc_container','requires/dyeing_production_for_woven_textile_entry_controller','');
			//alert(response[3]);
			show_list_view( response[2]+'_'+response[4]+'_'+response[3]+'_'+document.getElementById('txt_entry_form_no').value,'show_dtls_batch_list_view','list_container','requires/dyeing_production_for_woven_textile_entry_controller','');
			reset_form('','','txt_update_id*txt_batch_no*txt_batch_ID*txt_entry_form_no*hidden_batch_id*txt_dying_started*txt_ext_id*txt_dying_end_load*txt_job_no*txt_machine_no*txt_buyer*txt_mc_group*txt_order_no*txt_color*txt_ltb_btb*txt_file*txt_ref*txtconscomp_1*txtgsm_1*txtdiawidth_1*txtdiatype_1*txtroll_1*txtbatchqnty_1*txtprodqnty_1*txtrate_1*txtamount_1*txtlot_1*txtyarncount_1*txtbrand_1*rollid_1*txtbarcode_1*updateiddtls_1','','$(\'#list_fabric_desc_container tr:not(:first)\').remove();','');
			//reset_form('slittingsqueezing_1','','','','$(\'#list_fabric_desc_container tr:not(:first)\').remove();');
			$('#cbo_yesno').focus();

			//$("#list_fabric_desc_container").html("");

		}
	}

	function load_list_view (str)
	{

			$('#txt_batch_ID').val('');
			$('#hidden_batch_id').val('');
			$('#hidden_double_dyeing').val('');
			$('#txt_job_no').val('');
			$('#txt_machine_no').val('');
			$('#txt_buyer').val('');
			$('#txt_mc_group').val('');
			$('#txt_order_no').val('');
			$('#txt_color').val('');
			$('#txt_ltb_btb').val('');
			$('#txt_file').val('');
			$('#txt_ref').val('');

		reset_form('','load_unload_container','','','$(\'#list_fabric_desc_container tr:not(:first)\').remove();');


		$('#txtconscomp_1').val('');
		$('#txtgsm_1').val('');
		$('#txtdiawidth_1').val('');
		$('#txtdiatype_1').val('');
		$('#txtroll_1').val('');
		$('#rollid_1').val('');
		$('#txtbarcode_1').val('');

		$('#txtbatchqnty_1').val('');
		$('#txtprodqnty_1').val('');
		$('#txtrate_1').val('');
		$('#txtamount_1').val('');
		$('#txtlot_1').val('');
		$('#txtyarncount_1').val('');
		$('#txtbrand_1').val('');
		$('#txtremark_').val('');
		$("#checkRow_1").removeAttr("checked");



		show_list_view(str,'on_change_data','load_unload_container','../fabric_finishing_progress/requires/dyeing_production_for_woven_textile_entry_controller','');
		document.slittingsqueezing_1.txt_batch_no.focus();
		//reset_form('slittingsqueezing_1','','','','$(\'#tbl_item_details  tbody tr:not(:first)\').remove();');
		set_all_onclick(); roll_maintain();
		if(str==1)
		{
			document.getElementById('batch_no_th').innerHTML='Batch No';
			$('#batch_no_th').css('color','blue');
			document.getElementById('company_th').innerHTML='Company';
			$('#company_th').css('color','blue');
			document.getElementById('service_source_caption').innerHTML='Service Source';
			$('#service_source_caption').css('color','blue');
			document.getElementById('service_company_caption').innerHTML='Service Company';
			$('#service_company_caption').css('color','blue');
			document.getElementById('ltb_ltb_caption').innerHTML='BTB LTB';
			$('#ltb_ltb_caption').css('color','blue');
			//document.getElementById('process_start_date').innerHTML='Process Start Date';
			$('#process_start_date').css('color','blue');
			//document.getElementById('hour_min_td').innerHTML='Process Start Time';
			$('#hour_min_td').css('color','blue');

			document.getElementById('floor_caption').innerHTML='Floor';
			$('#floor_caption').css('color','blue');
			document.getElementById('machine_caption').innerHTML='Machine Name';
			$('#machine_caption').css('color','blue');
			document.getElementById('process_td').innerHTML='Process Name';
			$('#process_td').css('color','blue');
			$("#list_container").html("");

		}
		else
		{

			process_check(31);

			document.getElementById('batch_no_th').innerHTML='Batch No';
			$('#batch_no_th').css('color','blue');
			document.getElementById('company_th').innerHTML='Company';
			$('#company_th').css('color','blue');
			document.getElementById('service_source_caption').innerHTML='Service Source';
			$('#service_source_caption').css('color','blue');
			document.getElementById('service_company_caption').innerHTML='Service Company';
			$('#service_company_caption').css('color','blue');
			document.getElementById('process_td').innerHTML='Process Name';
			$('#process_td').css('color','blue');
			document.getElementById('ltb_ltb_caption').innerHTML='BTB LTB';
			$('#ltb_ltb_caption').css('color','blue');
			document.getElementById('production_date_td').innerHTML='Productuon Date';
			$('#production_date_td').css('color','blue');
			document.getElementById('process_end_date').innerHTML='Process End Date';
			$('#process_end_date').css('color','blue');
			document.getElementById('process_end_time').innerHTML='Process End Time';
			$('#process_end_time').css('color','blue');
			document.getElementById('floor_caption').innerHTML='Floor';
			$('#floor_caption').css('color','blue');
			document.getElementById('machine_caption').innerHTML='Machine Name';
			$('#machine_caption').css('color','blue');
			document.getElementById('result_caption').innerHTML='Result';
			$('#result_caption').css('color','blue');
			$("#list_container").html("");
		}

		set_button_status(0, permission, 'fnc_pro_fab_subprocess',1);
	}
	function fnResetForm()
	{

			reset_form('slittingsqueezing_1','load_unload_container*list_container','','','$(\'#list_fabric_desc_container tr:not(:first)\').remove();');
			$('#txt_batch_ID').val('');
			$('#txt_dying_started').val('');
			$('#txt_ext_id').val('');
			$('#txt_dying_end_load').val('');
			$('#txt_job_no').val('');
			$('#txt_machine_no').val('');
			$('#txt_buyer').val('');
			$('#txt_mc_group').val('');
			$('#txt_order_no').val('');
			$('#txt_color').val('');
			$('#txt_ltb_btb').val('');
			$('#txt_file').val('');
			$('#txt_ref').val('');
			//$('#txt_ref').val('');
	}
	function fnc_move_cursor(val,id, field_id,lnth,max_val)
	{  //alert(id);
		var str_length=val.length;
		if(str_length==lnth)
		{
			$('#'+field_id).select();
			$('#'+field_id).focus();
		}
		if(val>max_val)
		{
			if(id=='txt_start_hours' || id=='txt_end_hours')
			{
				//alert(3);
				document.getElementById(id).value="00";
			}
			else
			{
				//alert(4);
				document.getElementById(id).value=max_val;
			}

			//document.getElementById(id).value.substring(0, max_val);
			// field.value = field.value.substring(0, max_val);
		}
		else
		{

			if(str_length==1)
			{

				if(val>max_val)
				{
					document.getElementById(id).value=max_val;
				}
				else
				{
				document.getElementById(id).value="0"+val;
				}
			}

		}
	}
	function scan_batchnumber(str)
	{
		 //check_batch();
	var batch_no=$('#txt_batch_no').val();
	var cbo_company_id = $('#cbo_company_id').val();
	//var response=return_global_ajax_value( cbo_company_id+"**"+str, 'check_batch_no_scan', '', 'requires/dyeing_production_for_woven_textile_entry_controller');
	//var response=response.split("_");
	check_batch();
	$('#txt_batch_no').val(str);
	$('#cbo_company_id').focus();
	return;
	/*if(response[0]==0)
		{
			//alert('Batch no not found.');
			$('#txt_batch_no').val('');
			$('#hidden_batch_id').val('');
			//$('#cbo_company_id').val('');
			$('#txt_update_id').val('');
			$('#txt_process_id').val('');
			$('#txt_process_end_date').val('');
			$('#txt_end_hours').val('');
			$('#txt_end_minutes').val('');
			$('#cbo_machine_name').val('');
			$('#txt_remarks').val('');
			$('#txt_batch_no').focus();
			reset_form('slittingsqueezing_1','','','','$(\'#list_fabric_desc_container tr:not(:first)\').remove();');
		}
	else
		{
			$('#hidden_batch_id').val(response[1]);
			get_php_form_data(document.getElementById('cbo_load_unload').value+'_'+response[1]+'_'+batch_no+'_'+response[2], "populate_data_from_batch", "requires/dyeing_production_for_woven_textile_entry_controller" );
			show_list_view(response[1],'show_fabric_desc_listview','list_fabric_desc_container','requires/dyeing_production_for_woven_textile_entry_controller','');
			get_php_form_data(document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_floor').value+'**'+document.getElementById('cbo_machine_name').value, 'populate_data_from_machine', 'requires/dyeing_production_for_woven_textile_entry_controller' );
			$('#cbo_company_id').focus();
		}*/
	}
$('#txt_batch_no').live('keydown', function(e) {
    if (e.keyCode === 13) {
        e.preventDefault();
		var batch_no=$('#txt_batch_no').val();
		//alert(batch_no);
		// scan_batchnumber(batch_no);
		$('#txt_batch_no').removeAttr('onChange','onChange');// This function Call Off --onChange="check_batch()--;"
		$('#cbo_company_id').focus();
		 check_batch();
    }
});
function openmypage_process()
	{
		var txt_process_id = $('#txt_process_id').val();
		var title = 'Process Name Selection Form';
		var page_link = 'requires/dyeing_production_for_woven_textile_entry_controller.php?txt_process_id='+txt_process_id+'&action=process_name_popup';
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
	function openmypage_process_unload()
	{
		var txt_process_id = $('#txt_process_id').val();
		var title = 'Process Name Selection Form';
		var page_link = 'requires/dyeing_production_for_woven_textile_entry_controller.php?txt_process_id='+txt_process_id+'&action=process_name_popup_unload';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var process_id=this.contentDoc.getElementById("hidden_process_id").value;	 //Access form field with id="emailfield"
		var process_name=this.contentDoc.getElementById("hidden_process_name").value;
		}
	}
	function roll_maintain()
	{
		//var com=$('#cbo_company_id').val();
		//alert(com);
		get_php_form_data($('#cbo_company_id').val(),'roll_maintained_setting','requires/dyeing_production_for_woven_textile_entry_controller' );
		var roll_maintained=$('#roll_maintained').val();
		var page_upto=$('#page_upto').val();
	if(roll_maintained==1 ) // && roll_maintained==1 (page_upto*1==2 || page_upto*1>2) &&
		{
		$('#txt_issue_chalan').attr('placeholder','Write/Browse/Scan');
		$('#txt_issue_chalan').removeAttr('disabled','disabled');
		}
		else
		{
			$('#txt_issue_chalan').attr('disabled','disabled');
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
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/dyeing_production_for_woven_textile_entry_controller.php?cbo_company_id='+cbo_company_id+'&action=issue_challan_popup','Issue Popup', 'width=880px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var issue_id=this.contentDoc.getElementById("hidden_system_id").value;	 //challan Id and Number
			if(issue_id!="")
			{
				get_php_form_data(issue_id, "populate_data_from_data", "requires/dyeing_production_for_woven_textile_entry_controller");
				//show_list_view(issue_id,'show_fabric_desc_listview_issue','list_fabric_desc_container','requires/heat_setting_controller','');

			}
		}
	}
	function check_issue_challan_scan(str) //Issue Challan
	{
		var issue_chalan=$('#txt_issue_chalan').val();
		var cbo_company_id = $('#cbo_company_id').val();
		var batch_id=$('#hidden_batch_id').val();
		if(issue_chalan!="")
		{
			var response=return_global_ajax_value( cbo_company_id+"**"+issue_chalan, 'check_issue_challan_no', '', 'requires/dyeing_production_for_woven_textile_entry_controller');
			var response=response.split("_");
			//var response=return_global_ajax_value( cbo_company_id+"**"+issue_chalan, 'check_issue_challan_no', '', 'requires/heat_setting_controller');
			//var response=response.split("_");
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


				get_php_form_data(response[1], "populate_data_from_data", "requires/dyeing_production_for_woven_textile_entry_controller" );
				var hidden_roll_id = $('#txt_roll_id').val();
				show_list_view(document.getElementById('txt_batch_ID').value+'_'+document.getElementById('roll_maintained').value+'_'+document.getElementById('cbo_load_unload').value,'issue_show_fabric_desc_listview','list_fabric_desc_container','requires/dyeing_production_for_woven_textile_entry_controller','');

				$('#cbo_service_source').focus();
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

			var response=return_global_ajax_value( cbo_company_id+"**"+issue_chalan, 'check_issue_challan_no', '', 'requires/dyeing_production_for_woven_textile_entry_controller');
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
				$('#'+txt_issue_chalan).focus();
				//$('#cbo_machine_name').val('');
				//$('#txt_remarks').val('');
			//reset_form('slittingsqueezing_1','','','','$(\'#list_fabric_desc_container tr:not(:first)\').remove();');

			}
			else
			{

				get_php_form_data(response[1], "populate_data_from_data", "requires/dyeing_production_for_woven_textile_entry_controller" );
				var row_num=$('#tbl_item_details tbody tr').length-1;
				//alert(row_num);return;

				var hidden_roll_id = $('#txt_roll_id').val();
				show_list_view(document.getElementById('txt_batch_ID').value+'_'+document.getElementById('roll_maintained').value+'_'+document.getElementById('cbo_load_unload').value,'issue_show_fabric_desc_listview','list_fabric_desc_container','requires/dyeing_production_for_woven_textile_entry_controller','');

				$('#cbo_service_source').focus();
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
	 function check_issue_challan_scan(str) //Issue Challan Scan
	{
		var issue_chalan=$('#txt_issue_chalan').val();
		var cbo_company_id = $('#cbo_company_id').val();
		var batch_id=$('#hidden_batch_id').val();
		if(issue_chalan!="")
		{
			var response=return_global_ajax_value( cbo_company_id+"**"+issue_chalan, 'check_issue_challan_no_scan', '', 'requires/dyeing_production_for_woven_textile_entry_controller');
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

				get_php_form_data(response[1], "populate_data_from_data", "requires/dyeing_production_for_woven_textile_entry_controller" );
				//var hidden_roll_id = $('#txt_roll_id').val();
			//	show_list_view(batch_id+'_'+hidden_roll_id+'_'+cbo_company_id,'issue_show_fabric_desc_listview','list_fabric_desc_container','requires/dyeing_production_for_woven_textile_entry_controller','');
				$('#cbo_service_source').focus();
			}

		}
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
	function load_batch_working_company(service_company)
	{
			if (form_validation('cbo_company_id*txt_batch_ID','Company*Batch Id')==false)
			{
				return;
			}
		//
			var service_source=$('#cbo_service_source').val();
			var load_unload=$('#cbo_load_unload').val();
			var hidden_service_company=$('#txt_hidden_service_company').val();
			if(service_source==1)
			{
					if(hidden_service_company!=service_company)
					{
						alert('Service company not same with batch entry');
						$('#cbo_service_company').val(hidden_service_company);
						$('#cbo_service_company').attr('disabled','disabled');
						load_drop_down( 'requires/dyeing_production_for_woven_textile_entry_controller', hidden_service_company, 'load_drop_floor', 'floor_td' );
					}
					else
					{
						$('#cbo_service_company').removeAttr('disabled','disabled');
					}
			}
			if(load_unload==2)
			{
				$('#cbo_machine_name').attr('disabled','disabled');
				$('#cbo_floor').attr('disabled','disabled');
			}


	}

	function process_check(process_id)
	{

		var load_unload=$('#cbo_load_unload').val();
		var row_num=$('#tbl_item_details tbody tr').length-1;

		if(process_id==31)
		{


			document.getElementById('result_caption').innerHTML="Result";
			$('#result_caption').css('color','blue');
			if(load_unload==2)
			{
				for (var i=1; i<=row_num; i++)
				{
					//alert(process_id);
					$('#txtprodqnty_'+i).attr('readOnly','readOnly');
				}

			}
		}
		else
		{

			document.getElementById('result_caption').innerHTML="Result";
			$('#result_caption').css('color','black');
			if(load_unload==2)
			{
				for (var i=1; i<=row_num; i++)
				{
					$('#txtprodqnty_'+i).removeAttr('readOnly','readOnly');
					//$('#txt_batch_number').removeAttr('readOnly','readOnly');
				}

			}
		}

	}
	function calculate_production_qnty()
	{
		var numRow = $('#tbl_item_details tbody tr').length-1;
		//alert(numRow);
		var ddd={ dec_type:2, comma:0}
		//math_operation( "total_batch_qnty", "txt_batch_qnty_", "+",numRow,ddd );
		math_operation( "total_production_qnty", "txtprodqnty_", "+",numRow,ddd );
		
			for(var i=1;i<=numRow; i++)
			{
				var p_qty=$("#txtprodqnty_"+i).val()*1;
				//alert(p_qty);
				var batch_qty=$("#txtbatchqnty_"+i).val()*1;
				if(batch_qty<p_qty)
				{
					alert('Prod Qty is greater than Ref. Qty');
					$("#txtprodqnty_"+i).val(batch_qty);
					return;
				}
				
			}
			
		if($("#cbo_load_unload").val()==2)
		{
			var total_amount=0;
			for(var i=1;i<=numRow; i++)
			{
				booking_rate=$("#txtrate_"+i).val()*1;
				var p_qty=$("#txtprodqnty_"+i).val()*1;
				var batch_qty=$("#txtbatchqnty_"+i).val()*1;
				
				var amount=p_qty*booking_rate;
				total_amount+=amount;
				$("#txtamount_"+i).val(amount);
			}
			$("#total_amount").val(total_amount);
		}
		
			


	}
	function calculate_production_qnty2()
	{
		var numRow = $('#tbl_item_details tbody tr').length-1;
		//alert(numRow);
		var ddd={ dec_type:2, comma:0}
		math_operation( "total_batch_qnty", "txtbatchqnty_", "+",numRow,ddd );
		//math_operation( "total_production_qnty", "txt_prod_qnty_", "+",numRow,ddd );
	}
	// popup for System No----------------------
function open_syspopup(page_link,title)
{
	if( form_validation('cbo_load_unload','Load Unload')==false )
	{
		return;
	}

	var title ='Batch System Popoup';
	var company = $("#cbo_company_id").val();
	var system_no = $("#txt_system_no").val();
	var load_unload = $("#cbo_load_unload").val();
	var batch_no = $("#txt_batch_no").val();
	page_link='requires/dyeing_production_for_woven_textile_entry_controller.php?action=sys_popup&company='+company+'&batch_no='+batch_no+'&load_unload='+load_unload+'&system_no='+system_no;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px, height=400px, center=1, resize=0, scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var sysNumber=(this.contentDoc.getElementById("hidden_sys_number").value).split("_"); // System No
		if (sysNumber!="")
		{

			freeze_window(5);

			//alert(sysNumber[4]);
			$("#txt_system_no").val(sysNumber[1]);
			$("#txt_batch_no").val(sysNumber[3]);
			$("#txt_entry_form_no").val(sysNumber[5]);
			$("#txt_process_date").val(sysNumber[6]);
			$("#txt_process_end_date").val(sysNumber[7]);
			$("#txt_end_hours").val(sysNumber[8]);
			$("#txt_end_minutes").val(sysNumber[9]);
			//reset_form('','','txt_item_description*cbo_uom*txt_quantity*txt_rate*txt_amount','','','');
			//get_php_form_data(sysNumber[0], "populate_master_from_data", "requires/dyeing_production_for_woven_textile_entry_controller" );

			show_list_view( sysNumber[2]+'_'+sysNumber[4]+'_'+sysNumber[1]+'_'+sysNumber[5],'show_dtls_batch_list_view','list_container','requires/dyeing_production_for_woven_textile_entry_controller','');			//function disable_enable_fields( flds, operation, loop_flds, loop_leng )
			//disable_enable_fields( 'cbo_company_name*txt_pass_id*cbo_out_company*txt_receive_from*txt_challan_no', 1, "", "" );
			//set_button_status(0, permission, 'fnc_getin_entry',1,1);
			//set_field_level_access( document.getElementById('cbo_company_id').value);
			release_freezing();
		}
	}
}

function fnc_company_val()
{
	var company=$('#cbo_company_id').val();
	var service=$('#cbo_service_source').val();
	if(company!=0 || service==1 )
	{
		$('#cbo_service_company').val(company);
	}
}
function fnc_load_data(type_id)
{
		//var com=$('#cbo_company_id').val();
		//alert(type_id);
		if( form_validation('cbo_machine_name','Machine')==false )
		{
			return;
		}
		if(type_id==1)
		{
		get_php_form_data($('#cbo_company_id').val()+'**'+$('#cbo_machine_name').val()+'**'+$('#cbo_floor').val(),'load_batch_machine','requires/dyeing_production_for_woven_textile_entry_controller' );
		}
		/*else
		{
			$('#txt_process_start_date').val('');
			$('#txt_start_minutes').val('');
			$('#txt_start_hours').val('');
			$('#cbo_floor').val(0);
			$('#cbo_machine_name').val(0);
		}*/
		//var cbo_machine_name=$('#cbo_machine_name').val();
}
function fnc_mc_load()//
{
	var floor_length=$("#cbo_floor option").length;
	//alert(floor_length);
	if(floor_length==1)
	{
		load_drop_down( 'requires/dyeing_production_for_woven_textile_entry_controller', document.getElementById('cbo_service_company').value+'**'+document.getElementById('cbo_floor').value, 'load_drop_machine', 'machine_td' );
	}
}
</script>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
<? echo load_freeze_divs ("../../",$permission); ?>
    <form name="slittingsqueezing_1" id="slittingsqueezing_1" autocomplete="off" >
    <div style="width:1300px; float:left;">
        <fieldset style="width:1250px;">
        <table cellpadding="0" cellspacing="1" width="1250" border="0" align="left" height="auto" id="master_tbl">
            <tr>
                <td width="29%" valign="top">
                     <fieldset>
                     <legend>Input Area</legend>
                        <table width="130px" cellpadding="0" cellspacing="2" align="right"  >
                            <tr>
                                <td align="center" width="130" class="must_entry_caption"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Load/Un-load</b></td>
                                <td  style="float:left" width="130">
                                    <?
                                        echo create_drop_down( "cbo_load_unload", 130, $loading_unloading,'', '1', '---- Select ----', '',"load_list_view(this.value)",'','','','','',1); //data, action, div, path, extra_func
                                    ?>
                                </td>
                            </tr>
                        </table>
                        <div style="width:auto; float:left; min-height:40px; margin:auto" align="center" id="load_unload_container">
                        </div>
                </fieldset>
                </td>
                <td width="1%" valign="top">&nbsp;</td>
                <td width="70%" valign="top">
                    <table cellpadding="0" cellspacing="1" width="100%" border="0" align="left">
                        <tr>
                            <td colspan="3"> <center> <legend>Reference Display</legend></center> </td>
                        </tr>
                        <tr>
                            <td width="45%" valign="top">
                                <fieldset style="height:auto;">
                                    <table width="370" align="left" id="tbl_body1" >
                                    <tr>
                                    <td colspan="4" align="center"><strong>Functional Batch No :</strong> &nbsp;<input type="text" name="txt_system_no" id="txt_system_no" class="text_boxes_numeric" onDblClick="open_syspopup()" style="width:100px;" placeholder="Double Click To Browse" readonly />  </td>
                                    </tr>
                                        <tr>
                                            <td width="50">Batch ID</td>
                                            <td width="110">
                                                <input type="text" name="txt_batch_ID" id="txt_batch_ID" class="text_boxes" style="width:100px;"  readonly />
                                                <input type="hidden" name="hidden_control_chemical_issue" id="hidden_control_chemical_issue" />
                                            </td>
                                            <td width="70">Loading Date</td>
                                            <td width="110">
                                                <input type="text" name="txt_dying_started" id="txt_dying_started" class="text_boxes" style="width:100px;" readonly  />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Ext. No.</td>
                                            <td>
                                               <input type="text" name="txt_ext_id" id="txt_ext_id" class="text_boxes" style="width:100px;" readonly />
                                            </td>
                                            <td>Loading Time</td>
                                            <td>
                                                <input type="text" name="txt_dying_end_load" id="txt_dying_end_load" class="text_boxes" style="width:100px;" readonly  />
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
                                        </tr>
                                        <tr>
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
                                             <td>Color</td>
                                            <td>
                                                 <input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:100px;"  readonly />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>BTB/LTB</td>
                                            <td>
                                                <input type="text" name="txt_ltb_btb" id="txt_ltb_btb" class="text_boxes" style="width:100px;" readonly />
                                                <? //echo create_drop_down( "txt_ltb_btb", 135, $ltb_btb,"", 1, "-- Select --", 0, "","","","","",""); ?>
                                            </td>
                                            <td>File No</td>
                                            <td>
                                                 <input type="text" name="txt_file" id="txt_file" class="text_boxes" style="width:100px;"  readonly />
                                            </td>
                                        </tr>
                                         <tr>

                                            <td>Ref. No</td>
                                            <td>
                                                 <input type="text" name="txt_ref" id="txt_ref" class="text_boxes" style="width:100px;"  readonly />
                                            </td>
                                              <td>Trims Wgt:</td>
                                            <td>
                                            <input type="text" name="txt_trim_wgt" id="txt_trim_wgt" class="text_boxes" placeholder="Display" style="width:95px;"  readonly /> 
                                           </td>
                                        </tr>
                                        <tr>
                                            <td>Batch Date</td>
                                            <td>
                                                <input type="text" name="txt_batch_date" id="txt_batch_date" class="text_boxes" style="width:100px;" readonly />
                                            </td>
                                            <td colspan="2">
                                            <div id="batch_type" style="color:#F00"> </div> 
                                            </td>
                                             
                                        </tr>
                                       <!-- <tr>
                                            <td colspan="6"> <div id="batch_type" style="color:#F00"> </div> </td>
                                        </tr>-->
                                    </table>
                                </fieldset>
                              </td>
                            <td width="1%" valign="top">&nbsp;</td>
                            <td width="54%" valign="top">
                                <fieldset>
                                     <table width="660" align="right" class="rpt_table" rules="all" id="tbl_item_details">
                                        <thead>
                                            <th width="70">&nbsp;SL&nbsp;</th>
                                            <th>Const & Composition</th>
                                            <th>GSM</th>
                                            <th>Dia/Width</th>
                                            <th>D/W Type</th>
                                            <th>Roll No</th>
                                            <th>Ref. Qty</th>
                                            <th>Prod. Qty</th>
                                            <th width="" >Rate</th>
                                            <th width="" >Amount</th>
                                            <th>Lot</th>
                                            <th>Yarn Count</th>
                                            <th>Brand</th>

                                        </thead>
                                        <tbody id="list_fabric_desc_container">
                                            <tr class="general" id="row_1">
                                            <td> 1
											<input type="hidden" id="checkRow_1" name="checkRow_1" >
											</td>
                                                <td><input type="text" name="txtconscomp_1" id="txtconscomp_1" class="text_boxes" style="width:170px;" readonly disabled /></td>
                                                <td><input type="text" name="txtgsm_1" id="txtgsm_1" class="text_boxes" style="width:40px;"readonly disabled /> </td>
                                                <td><input type="text" name="txtdiawidth_1" id="txtdiawidth_1" class="text_boxes" style="width:40px;" readonly  disabled/></td>
                                                <td><input type="text" name="txtdiatype_1" id="txtdiatype_1" class="text_boxes" style="width:70px;" readonly disabled/></td>
                                                 <td><input type="text" name="txtroll_1" id="txtroll_1" class="text_boxes" style="width:40px;" readonly  disabled/>
												 </td>
                                                 <input type="hidden" name="rollid_1" id="rollid_1" style="width:50px;"  class="text_boxes_numeric" /></td>
                                                <td><input type="text" name="txtbatchqnty_1" id="txtbatchqnty_1" class="text_boxes_numeric" style="width:60px;"  /></td>
                                                <td><input type="text" name="txtprodqnty_1" id="txtprodqnty_1" class="text_boxes_numeric" style="width:60px;"  /></td>
                                                <td><input type="text" name="txtrate_1" id="txtrate_1" class="text_boxes_numeric" style="width:60px;" readonly/></td>
                                                <td><input type="text" name="txtamount_1" id="txtamount_1" class="text_boxes_numeric" style="width:70px;"  readonly/> </td>
                                                 <td><input type="text" name="txtlot_1" id="txtlot_1" class="text_boxes_numeric" style="width:40px;" readonly disabled />                                                </td>
												<td><input type="text" name="txtyarncount_1" id="txtyarncount_1" class="text_boxes_numeric" style="width:60px;" readonly disabled />                                                </td>
												<td><input type="text" name="txtbrand_1" id="txtbrand_1" class="text_boxes_numeric" style="width:60px;" readonly disabled />
                                                <input type="hidden" name="txtprodid_1" id="txtprodid_1"  />
				 								<input type="hidden" name="hiddendiatypeid_1" id="hiddendiatypeid_1" class="text_boxes" readonly />
												<input type="hidden" name="txtbarcode_1" id="txtbarcode_1" class="text_boxes" readonly />
												<input type="hidden" name="txtdeterid_1" id="txtdeterid_1" class="text_boxes" readonly />
												<input type="hidden" name="txtremark_1" id="txtremark_1" class="text_boxes" readonly />
												<input type="hidden" name="updateiddtls_1" id="updateiddtls_1" class="text_boxes" readonly />
                                                 </td>

                                            </tr>

											<tr>
											<td colspan="6" align="right"><b>Sum:</b>  </td>
											<td align="right"><b>
											<input type="text" name="total_batch_qnty" id="total_batch_qnty" class="text_boxes_numeric" style="width:60px"  readonly/> </b>
											</td>
											<td><input type="text" name="total_production_qnty" id="total_production_qnty" class="text_boxes_numeric" style="width:60px"
											readonly/></td>
											<td align="right"></td>
											<td align="right"><input type="text" name="total_amount" id="total_amount" class="text_boxes_numeric" style="width:70px"  readonly/></td>

											</tr>
                                        </tbody>
                                  </table>
                               </fieldset>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <fieldset style="width:600px;">
                        <table style="width:600">
                            <tr>
                                <td width="100">Remarks:</td>
                                <td>
                                    <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:600px;"    />
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
                <td align="right">&nbsp;</td>
            </tr>
            <tr>
                <td align="center" colspan="4" class="button_container">
                    <?
                        echo load_submit_buttons($permission, "fnc_pro_fab_subprocess", 0,1,"fnResetForm()",1);
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
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>