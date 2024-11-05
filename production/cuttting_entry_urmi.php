<?
/*-------------------------------------------- Comments
Purpose			:
Functionality	:
JS Functions	:
Created by		:	Ashraful Islam
Creation date 	: 	23-03-2014
Updated by 		: Floor name enabled as per URMI Instruction 03-04-2017
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
//echo $fnat;die;
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
   echo load_html_head_contents("Cut and Lay Entry","../", 1, 1, $unicode,'','');
   //echo $fnat;die;
   ?>
 <script>
    var field_level_menual = 1;
    var txt_job_id=$("#txt_job_no").val();
	var permission='<? echo $permission; ?>';

	var dtls_mandatory_field="";
	var dtls_mandatory_message="";
    <?
	if($_SESSION['logic_erp']['mandatory_field'][604]!="")
	{
		$mandatory_field_arr= json_encode( $_SESSION['logic_erp']['mandatory_field'][604] );
		echo "var mandatory_field_arr= ". $mandatory_field_arr . ";\n";
	}
	
	if($_SESSION['logic_erp']['data_arr'][720]){
		echo "var field_level_data= " . json_encode($_SESSION['logic_erp']['data_arr'][720]). ";\n";
	}
	
	?>


	$('#txt_cutting_no').focus();
	function  add_row(id)
	{
		var inner_loop=0;
			inner_loop=inner_loop+1;
		var tbl_id=id.split("_");
		var table_tr="#table_tr_"+tbl_id[2]+"_"+tbl_id[3];
		var size_td="#size_id_"+tbl_id[2]+"_"+tbl_id[3];

		var size_name=document.getElementById('txt_size_' + tbl_id[2]+'_'+tbl_id[3]).innerHTML;
		//alert(size_name)
		var tr_id="'new_tr_"+tbl_id[2]+"_"+inner_loop+"'";
		var del_td="'txt_del_"+tbl_id[2]+"_"+inner_loop+"'";
		var size_id=$(size_td).val();

		$(table_tr).after("<tr bgcolor='#FEFEE0' id="+tr_id+"><td></td><td>New row</td><td> <input type='hidden' name='size_id_'  id='size_id_'   /> </td><td align='center'><input type='text' class='text_boxes_numeric' style='width:65px'  /></td><td align='center'><input type='text' class='text_boxes_numeric' style='width:65px'  /></td><td align='center'><input type='text' class='text_boxes_numeric' style='width:65px'  /></td><td align='center'><input type='text' class='text_boxes_numeric' style='width:65px'  /><td align='center'><input type='text' class='text_boxes_numeric' style='width:65px'  /></td><td ><input type='button' id="+del_td+" value='Delete'  class='formbuttonplasminus' /></td></tr>");
		var del_tr="#txt_del_"+tbl_id[2]+"_"+inner_loop;
		var del_id="new_tr_"+tbl_id[2]+"_"+inner_loop;

		$('#increase_'+i).removeAttr("value").attr("value","+");
	  $(del_tr).removeAttr("onclick").attr("onclick","fn_deleteRow("+del_id+");");

	}

	function fn_deleteRow(rowNo)
	{
		//alert(rowNo)
		$("#"+rowNo).remove();
	}
	function clear_tr(){
		location.reload();
	}

	function fnc_cut_qc_info( operation )
	{
		freeze_window(operation);

		   if(form_validation('txt_cutting_no*txt_cutting_date*txt_cutting_hour*cbo_cutting_company','Cutting Number*Cutting Date*Cutting Hour*Cut Company')==false)
		   {
		   		release_freezing();
				return;
		   }

		   if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][604]); ?>') 
			{
				if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][604]); ?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][604]); ?>')==false) {release_freezing();return;}
			}
 
		    if(operation==5)
	  		{
	  			var report_title=$( "div.form_caption" ).html();
	  			generate_report_file($('#cbo_cutting_company').val()+'*'+$('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#cbo_source').val()+'*'+report_title+'*'+$('#txt_cutting_no').val(),'print_reject_report','requires/cutting_entry_controller_urmi');
	  			release_freezing();
	  			return;
	  		}
			if(operation==9)//new print working 
	  		{
	  			var report_title=$( "div.form_caption" ).html();
	  			generate_report_file($('#cbo_cutting_company').val()+'*'+$('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#cbo_source').val()+'*'+report_title+'*'+$('#txt_cutting_no').val(),'print4_reject_report','requires/cutting_entry_controller_urmi');
	  			release_freezing();
	  			return;
	  		}
			if(operation==10)//Print 5
	  		{
	  			var report_title=$( "div.form_caption" ).html();
	  			generate_report_file($('#cbo_cutting_company').val()+'*'+$('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#cbo_source').val()+'*'+report_title+'*'+$('#txt_cutting_no').val(),'print5_reject_report','requires/cutting_entry_controller_urmi');
	  			release_freezing();
	  			return;
	  		}

	  		if(operation==6)
	  		{
	  			var report_title=$( "div.form_caption" ).html();
	  			generate_report_file($('#cbo_cutting_company').val()+'*'+$('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#cbo_source').val()+'*'+report_title+'*'+$('#txt_cutting_no').val(),'print_only_reject_report','requires/cutting_entry_controller_urmi');
	  			release_freezing();
	  			return;
	  		}
			
			if(operation==7)
	  		{
				//   alert(operation);
	  			var report_title=$( "div.form_caption" ).html();
	  			generate_report_file($('#cbo_cutting_company').val()+'*'+$('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#cbo_source').val()+'*'+report_title+'*'+$('#txt_cutting_no').val(),'print2_reject_report','requires/cutting_entry_controller_urmi');
	  			release_freezing();
	  			return;
	  		}

		    if(operation==8)
	  		{
	  			var report_title=$( "div.form_caption" ).html();
	  			generate_report_file($('#cbo_cutting_company').val()+'*'+$('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#cbo_source').val()+'*'+report_title+'*'+$('#txt_cutting_no').val(),'print3_reject_report','requires/cutting_entry_controller_urmi');
	  			release_freezing();
	  			return;
	  		}  

			if(date_compare($('#txt_entry_date').val(), $('#txt_cutting_date').val())==false)
			{
				alert("Cutting QC Date cannot be allowed before the cutting Date");
				release_freezing();
				return;
			}

			total_order=$('#total_order_id').val();
			var   txt_qty            ="";
			var   txt_bundle_no      ="";
			var   txt_start          ="";
			var   txt_end            ="";
			var   size_id            ="";
			var   txt_qcpass         ="";
			var   txt_reject         ="";
			var   txt_replace        ="";
			var   size_details_id    ="";
			var   size_details_qty   ="";
			var   size_details_bdl   ="";
			var   pcs_per_bdl        ="";
			var   update_details_id  ="";
			var   country_id         ="";
			var   hidden_barcode_no  ="";
			var   actual_reject		 ='';
			var   txt_rej_matrix  	 ="";
			var   bodypart_data		 ='';
			for(var j=1; j<=total_order; j++)
			{
			   if(j==1)
			   {
				   var   hidden_color            =$('#hidden_color_'+j).val();
				   var   txt_gmt_id              =$('#hidden_gmt_'+j).val();
				   var   txt_order_id            =$('#hidden_po_'+j).val();
				   var   hidden_lay_dtls_id      =$('#hidden_lay_dtls_id_'+j).val();
				   var   txt_oder_qty            = escape(document.getElementById('txt_oder_qty_' + j).innerHTML);
				   var   total_reject_qty        = escape(document.getElementById('total_reject_qty_' + j).innerHTML);
				   var   total_qc_qty            = escape(document.getElementById('total_qc_qty_' + j).innerHTML);
						 size_details_id         =$('#txt_dtls_size_id_'+j).val();
						 size_details_qty        =$('#txt_dtls_size_qty_'+j).val();
						 size_details_bdl        =$('#txt_dtls_size_bdl_'+j).val();
						 pcs_per_bdl             =$('#txt__pcs_per_bdl_'+j).val();
			   }
			   else
			   {
					hidden_color            =hidden_color+"*"+$('#hidden_color_'+j).val();
					txt_gmt_id              =txt_gmt_id+"*"+$('#hidden_gmt_'+j).val();
					txt_order_id            =txt_order_id+"*"+$('#hidden_po_'+j).val();
					hidden_lay_dtls_id      =hidden_lay_dtls_id+"*"+$('#hidden_lay_dtls_id_'+j).val();
					txt_oder_qty            =txt_oder_qty+"*"+escape(document.getElementById('txt_oder_qty_' + j).innerHTML);
					total_reject_qty         =total_reject_qty+"*"+escape(document.getElementById('total_reject_qty_' + j).innerHTML);
					total_qc_qty            =total_qc_qty+"*"+escape(document.getElementById('total_qc_qty_' + j).innerHTML);
					size_details_id         =size_details_id+"___"+$('#txt_dtls_size_id_'+j).val();
					size_details_qty        =size_details_qty+"___"+$('#txt_dtls_size_qty_'+j).val();
					size_details_bdl        =size_details_bdl+"___"+$('#txt_dtls_size_bdl_'+j).val();
					pcs_per_bdl             =pcs_per_bdl+"___"+$('#txt__pcs_per_bdl_'+j).val();
			   }
			   var txt_row_num="#tbl_body_"+j+"  tr";
			   var row_num=$(txt_row_num).length;
			   if(row_num!=0)
			   {
				   for(var i=1; i<=row_num; i++)
				   {
				   		total_qc_pass('txt_reject_' + j+'_'+i,escape(document.getElementById('txt_reject_' + j+'_'+i).value),this.value,j);
				   		total_qc_pass('txt_replace_' + j+'_'+i,escape(document.getElementById('txt_replace_' + j+'_'+i).value),this.value,j);
						if(j==1)
						{
							if(i==1)
							{

								 txt_qty          =trim(escape(document.getElementById('txt_qty_' + j+'_'+i).innerHTML));
								 txt_bundle_no    = escape(document.getElementById('txt_bundle_' + j+'_'+i).innerHTML);
								 txt_start        = escape(document.getElementById('txt_start_' + j+'_'+i).innerHTML);
								 txt_end          = escape(document.getElementById('txt_end_' + j+'_'+i).innerHTML);
								 size_id          =escape(document.getElementById('size_id_' + j+'_'+i).value);
								 txt_qcpass       =escape(document.getElementById('txt_qcpass_' + j+'_'+i).value);
								 txt_reject       =escape(document.getElementById('txt_reject_' + j+'_'+i).value);
								 txt_replace      =escape(document.getElementById('txt_replace_' + j+'_'+i).value);
								 update_details_id=escape(document.getElementById('update_details_id_' + j+'_'+i).value);
								 country_id       =$('#hidden_country_'+ j+'_'+i).val();
								 actual_reject    =$('#actual_reject_'+ j+'_'+i).val();
								 txt_rej_matrix   =$('#body_part_matrix_reject_'+ j+'_'+i).val();
								 bodypart_data    =$('#actual_bodypart_data_'+ j+'_'+i).val();
								 hidden_barcode_no=$('#hidden_barcode_'+ j+'_'+i).val();
							 }
							else
							{
								 txt_qty            =txt_qty+"*"+trim(escape(document.getElementById('txt_qty_' + j+'_'+i).innerHTML));
								 txt_bundle_no      =txt_bundle_no+"*"+escape(document.getElementById('txt_bundle_' + j+'_'+i).innerHTML);
								 txt_start          =txt_start+"*"+escape(document.getElementById('txt_start_' + j+'_'+i).innerHTML);
								 txt_end            =txt_end+"*"+escape(document.getElementById('txt_end_' + j+'_'+i).innerHTML);
								 size_id            =size_id+"*"+(document.getElementById('size_id_' + j+'_'+i).value);
								 txt_qcpass         =txt_qcpass+"*"+escape(document.getElementById('txt_qcpass_' + j+'_'+i).value);
								 txt_reject         =txt_reject+"*"+escape(document.getElementById('txt_reject_' + j+'_'+i).value);
								 txt_replace        =txt_replace+"*"+escape(document.getElementById('txt_replace_' + j+'_'+i).value);
								 update_details_id  =update_details_id+"*"+escape(document.getElementById('update_details_id_' + j+'_'+i).value);
								 country_id         =country_id+"*"+$('#hidden_country_'+ j+'_'+i).val();
								 actual_reject      =actual_reject+"_"+$('#actual_reject_'+ j+'_'+i).val();
								 txt_rej_matrix      =txt_rej_matrix+"_"+$('#body_part_matrix_reject_'+ j+'_'+i).val();
								 bodypart_data      =bodypart_data+"_"+$('#actual_bodypart_data_'+ j+'_'+i).val();
								 hidden_barcode_no	=hidden_barcode_no+"*"+$('#hidden_barcode_'+ j+'_'+i).val();
							}
						}
						else
						{
							if(i==1)
							{
								txt_qty            =txt_qty+"___"+ trim(escape(document.getElementById('txt_qty_' + j+'_'+i).innerHTML));
								txt_bundle_no      = txt_bundle_no+"___"+escape(document.getElementById('txt_bundle_' + j+'_'+i).innerHTML);
								txt_start          = txt_start+"___"+escape(document.getElementById('txt_start_' + j+'_'+i).innerHTML);
								txt_end            = txt_end+"___"+escape(document.getElementById('txt_end_' + j+'_'+i).innerHTML);
								size_id            =size_id+"___"+escape(document.getElementById('size_id_' + j+'_'+i).value);
								txt_qcpass         =txt_qcpass+"___"+escape(document.getElementById('txt_qcpass_' + j+'_'+i).value);
								txt_reject         =txt_reject+"___"+escape(document.getElementById('txt_reject_' + j+'_'+i).value);
								txt_replace        =txt_replace+"___"+escape(document.getElementById('txt_replace_' + j+'_'+i).value);
								update_details_id  =update_details_id+"___"+escape(document.getElementById('update_details_id_' + j+'_'+i).value);
								country_id         =country_id+"___"+$('#hidden_country_'+ j+'_'+i).val();
								actual_reject      =actual_reject+"##"+$('#actual_reject_'+ j+'_'+i).val();
								txt_rej_matrix      =txt_rej_matrix+"##"+$('#body_part_matrix_reject_'+ j+'_'+i).val();
								bodypart_data      =bodypart_data+"##"+$('#actual_bodypart_data_'+ j+'_'+i).val();
								hidden_barcode_no	=hidden_barcode_no+"__"+$('#hidden_barcode_'+ j+'_'+i).val();
							}
							else
							{
								txt_qty            =txt_qty+"*"+trim(escape(document.getElementById('txt_qty_' + j+'_'+i).innerHTML));
								txt_bundle_no      =txt_bundle_no+"*"+escape(document.getElementById('txt_bundle_' + j+'_'+i).innerHTML);
								txt_start          =txt_start+"*"+escape(document.getElementById('txt_start_' + j+'_'+i).innerHTML);
								txt_end            =txt_end+"*"+escape(document.getElementById('txt_end_' + j+'_'+i).innerHTML);
								size_id            =size_id+"*"+(document.getElementById('size_id_' + j+'_'+i).value);
								txt_qcpass         =txt_qcpass+"*"+escape(document.getElementById('txt_qcpass_' + j+'_'+i).value);
								txt_reject         =txt_reject+"*"+escape(document.getElementById('txt_reject_' + j+'_'+i).value);
								txt_replace        =txt_replace+"*"+escape(document.getElementById('txt_replace_' + j+'_'+i).value);
								update_details_id  =update_details_id+"*"+escape(document.getElementById('update_details_id_' + j+'_'+i).value);
								country_id         =country_id+"*"+$('#hidden_country_'+ j+'_'+i).val();
								actual_reject      =actual_reject+"_"+$('#actual_reject_'+ j+'_'+i).val();
								txt_rej_matrix      =txt_rej_matrix+"_"+$('#body_part_matrix_reject_'+ j+'_'+i).val();
								bodypart_data      =bodypart_data+"_"+$('#actual_bodypart_data_'+ j+'_'+i).val();
								hidden_barcode_no  =hidden_barcode_no+"*"+$('#hidden_barcode_'+ j+'_'+i).val();
							}
						}
					}
			   }
			   //console.log(txt_reject);
			}
			//return; 
				//alert(update_details_id) txt_replace_

					var cbo_company						   = escape(document.getElementById('cbo_company_name').value);
					var cbo_location_name				   = escape(document.getElementById('cbo_location_name').value);
					var cbo_floor_name					   = escape(document.getElementById('cbo_floor_name').value);
					var txt_cutting_date				   = escape(document.getElementById('txt_cutting_date').value);
					var txt_cutting_hour				   = escape(document.getElementById('txt_cutting_hour').value);
					var txt_batch_no				       = escape(document.getElementById('txt_batch_no').value);
					var txt_cutting_no                     = escape(trim(document.getElementById('txt_cutting_no').value));
					var txt_cut_prifix                     = escape(document.getElementById('txt_cut_prifix').value);

					var txt_table_no					   = escape(document.getElementById('txt_table_no').value);
					var txt_marker_length				   = escape(document.getElementById('txt_marker_length').value);
					var txt_marker_width				   = escape(document.getElementById('txt_marker_width').value);
					var txt_fabric_width				   = escape(document.getElementById('txt_fabric_width').value);
					var txt_gsm				               = escape(document.getElementById('txt_gsm').value);
					var txt_job_no				           = escape(document.getElementById('txt_job_no').value);
					var txt_entry_date                     = escape(document.getElementById('txt_entry_date').value);
					var txt_in_time_hours                  = escape(document.getElementById('txt_in_time_hours').value);
					var txt_in_time_minuties               = escape(document.getElementById('txt_in_time_minuties').value);

					var txt_out_time_hours			       = escape(document.getElementById('txt_out_time_hours').value);
					var txt_out_time_minuties		       = escape(document.getElementById('txt_out_time_minuties').value);
					var cbo_width_dia                      = escape(document.getElementById('cbo_width_dia').value);
					var txt_end_date                       = escape(document.getElementById('txt_end_date').value);
					var update_id                          = escape(document.getElementById('update_id').value);
					var txt_system_no                      = escape(document.getElementById('txt_system_no').value);
					var cbo_source                         = escape(document.getElementById('cbo_source').value);
					var cbo_cutting_company                = escape(document.getElementById('cbo_cutting_company').value);
					var piece_rate_data_string             = escape(document.getElementById('piece_rate_data_string').value);
					var cbo_work_order                     = escape(document.getElementById('cbo_work_order').value);
					var txt_wo_no                     	   = escape(document.getElementById('txt_wo_no').value);
					var cbo_shift_name                	   = escape(document.getElementById('cbo_shift_name').value);

				var data='action=save_update_delete&operation='+operation+
					'&cbo_company='+cbo_company+
					'&cbo_location_name='+cbo_location_name+
					'&cbo_floor_name='+cbo_floor_name+
					'&txt_cutting_date='+txt_cutting_date+
					'&txt_cutting_hour='+txt_cutting_hour+
					'&txt_cutting_no='+txt_cutting_no+
					'&txt_batch_no='+txt_batch_no+
					'&txt_cut_prifix='+txt_cut_prifix+
					'&country_id='+country_id+
					'&piece_rate_data_string='+piece_rate_data_string+
					'&cbo_source='+cbo_source+
					'&cbo_cutting_company='+cbo_cutting_company+
					//'&hidden_exchange_rate='+hidden_exchange_rate+
					//'&hidden_piece_rate='+hidden_piece_rate+
					'&cbo_work_order='+cbo_work_order+
					'&txt_wo_no='+txt_wo_no+
					'&cbo_shift_name='+cbo_shift_name+

					'&txt_table_no='+txt_table_no+
					'&hidden_barcode_no='+hidden_barcode_no+
					'&txt_marker_length='+txt_marker_length+
					'&txt_marker_width='+txt_marker_width+
					'&txt_fabric_width='+txt_fabric_width+
					'&txt_gsm='+txt_gsm+
					'&txt_job_no='+txt_job_no+
					'&txt_entry_date='+txt_entry_date+
					'&txt_in_time_hours='+txt_in_time_hours+

					'&txt_in_time_minuties='+txt_in_time_minuties+
					'&txt_out_time_hours='+txt_out_time_hours+
					'&txt_out_time_minuties='+txt_out_time_minuties+
					'&cbo_width_dia='+cbo_width_dia+
					'&txt_end_date='+txt_end_date+
					'&update_details_id='+update_details_id+
					'&update_id='+update_id+
					'&txt_system_no='+txt_system_no+
					'&actual_reject='+actual_reject+
					'&txt_rej_matrix='+txt_rej_matrix+
					'&bodypart_data='+bodypart_data+
					'&size_details_id='+size_details_id+
					'&size_details_qty='+size_details_qty+
					'&size_details_bdl='+size_details_bdl+
					'&pcs_per_bdl='+pcs_per_bdl+
					'&txt_order_id='+txt_order_id+
					'&hidden_lay_dtls_id='+hidden_lay_dtls_id+
					'&hidden_color='+hidden_color+
					'&txt_gmt_id='+txt_gmt_id+
					'&txt_oder_qty='+txt_oder_qty+
					'&txt_qty='+txt_qty+
					'&txt_bundle_no='+txt_bundle_no+
					'&txt_start='+txt_start+
					'&txt_end='+txt_end+
					'&size_id='+size_id+
					'&txt_qcpass='+txt_qcpass+
					'&txt_replace='+txt_replace+
					'&total_qc_qty='+total_qc_qty+
					'&total_reject_qty='+total_reject_qty+
					'&txt_reject='+txt_reject+get_submitted_data_string('garments_nature*txt_remarks*txt_check_no_of_pannel',"../");
					// alert(data);return;

			
			http.open("POST","requires/cutting_entry_controller_urmi.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_cut_qc_info_reponse;
	}



	function fnc_cut_qc_info_reponse()
	{
		if(http.readyState == 4)
		{
			//release_freezing();return;
			//alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]==200)
			{
				alert("This Cutting No Already Saved in Cutting Qc Page Which System Id "+reponse[1]+".");
				release_freezing();
				return;
			}
			if(reponse[0]==201)
			{
				alert("You can not delete! PLease contact admin user.");
				release_freezing();
				return;
			}
			if (reponse[0] == 15)
            {
                setTimeout('fnc_cut_qc_info(' + reponse[1] + ')', 8000);
            }
			else if(reponse[0]==786)
			{
				alert("Projected PO is not allowed to production. Please check variable settings."); return;
			}

			if(reponse[0]==0 || reponse[0]==1)
			{
				show_msg(reponse[0]);
				$("#txt_system_no").val(reponse[1]);
				set_button_status(1, permission, 'fnc_cut_qc_info',1,1);
				//get_php_form_data( reponse[1]+"__on_save", "load_system_mst_form", "requires/cutting_entry_controller" );
				get_php_form_data( reponse[1]+"__on_save", "load_system_mst_form", "requires/cutting_entry_controller_urmi" );

				show_list_view( reponse[1]+'**'+reponse[2]+'**'+$('#cbo_company_name').val(), 'update_order_details_list', 'cut_details_container', 'requires/cutting_entry_controller_urmi', '' ) ;

				if( reponse[3]!=undefined && reponse[3]!='')
				{
					alert('Update restricted for following bundles: '+reponse[3] );
					release_freezing();
					return;
				}
			}
			if(reponse[0]==222)
			{
				alert("Color Size Changed After Cut and Lay");
				release_freezing();
				return;
			}

			if(reponse[0]==444)
			{
				alert("Delete Restricted!! Data Found In Next Process");
				release_freezing();
				return;
			}

			if(reponse[0]==445)
			{
				alert("Update Restricted!! Data Found In Next Process");
				release_freezing();
				return;
			}

			if(reponse[0]==2)
			{
				window.location.reload();
			}


			if(reponse[0]!=15)
			{
			  release_freezing();
			}
		}
	}

	function generate_report_file(data,action,page)
	{
		window.open("requires/cutting_entry_controller_urmi.php?data=" + data+'&action='+action, true );
	}

	function open_cutting_popup()
	{
		
		var company_id=$("#cbo_company_name").val();
		var page_link='requires/cutting_entry_controller_urmi.php?action=cutting_number_popup&company_id='+company_id;
		var title="Search Cutting Number Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=420px,center=1,resize=0,scrolling=0','');
		emailwindow.onclose=function()
		{
			var company_id = this.contentDoc.getElementById("cbo_company_name").value;
			var sysNumber = this.contentDoc.getElementById("update_mst_id");
			var sysNumber=sysNumber.value.split('_');
			
			var com_systemNumber = company_id+'____'+sysNumber[0];
			// if (sysNumber[0]) 
			// {
			// 	alert('Cutting QC Already Done');
			// 	return;
			// } 
			get_php_form_data( sysNumber[0], "load_php_mst_form", "requires/cutting_entry_controller_urmi" );
			show_list_view( com_systemNumber, 'order_details_list', 'cut_details_container', 'requires/cutting_entry_controller_urmi', '' ) ;
			$("#cbo_company_name").attr("disabled",true);
			$("#txt_job_no").attr("disabled",true);
			$("#txt_job_year").attr("disabled",true);

			 $("#cbo_location_name").attr("disabled",false);
			$("#cbo_floor_name").attr("disabled",false);
			$("#txt_table_no").attr("disabled",true);
			$("#txt_cutting_no").attr("disabled",true);
			setFieldLevelAccess(company_id);
			//set_button_status(0, permission, 'fnc_cut_qc_info',1,1);

		}
	}
	
	function open_checkNoPanel_popup()
	{
		
		var company_id=$("#cbo_company_name").val();
		var txt_buyer_name=$("#txt_buyer_name").val();
		var txt_job_no=$("#txt_job_no").val();
		var page_link='requires/cutting_entry_controller_urmi.php?action=open_checkNoPanel_popup&company_id='+company_id+'&buyer_name='+txt_buyer_name+'&job_no='+txt_job_no;
		var title="Search Cutting Number Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=420px,center=1,resize=0,scrolling=0','');
		emailwindow.onclose=function()
		{
			var hidden_body_part_id = this.contentDoc.getElementById("hidden_body_part_id").value;
			//alert(hidden_body_part_id);
			$("#txt_check_no_of_pannel").val(hidden_body_part_id.split(",").length);
			var company_id = this.contentDoc.getElementById("cbo_company_name").value;

			var sysNumber = this.contentDoc.getElementById("update_mst_id");
			var sysNumber=sysNumber.value.split('_');
			
			var com_systemNumber = company_id+'____'+sysNumber[0];
			 
			get_php_form_data( sysNumber[0], "load_php_mst_form", "requires/cutting_entry_controller_urmi" );
			show_list_view( com_systemNumber, 'order_details_list', 'cut_details_container', 'requires/cutting_entry_controller_urmi', '' ) ;
			$("#cbo_company_name").attr("disabled",true);
			$("#txt_job_no").attr("disabled",true);
			$("#txt_job_year").attr("disabled",true);

			 $("#cbo_location_name").attr("disabled",false);
			$("#cbo_floor_name").attr("disabled",false);
			$("#txt_table_no").attr("disabled",true);
			$("#txt_cutting_no").attr("disabled",true);
			//set_button_status(0, permission, 'fnc_cut_qc_info',1,1);

		}
	}

	function pop_entry_reject(tbl,row_id)
	{
		var body_part_matrix=$("#body_part_matrix_reject_"+tbl+"_"+row_id).val();
		//alert(body_part_matrix); return;
		var actual_infos=$("#actual_reject_"+tbl+"_"+row_id).val();
		var actual_bodypart_data=$("#actual_bodypart_data_"+tbl+"_"+row_id).val();
		var company_id = $("#cbo_company_name").val();
		var buyer_id = $("#txt_buyer_name").val();
		var job_no = $("#txt_job_no").val();
			

		var page_link='requires/cutting_entry_controller_urmi.php?action=reject_qty_popup&actual_infos='+actual_infos+'&actual_bodypart_data='+actual_bodypart_data+'&body_part_matrix='+body_part_matrix+'&company_id='+company_id+'&buyer_id='+buyer_id+'&job_no='+job_no;
		var title='Reject Record Info';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform				=	this.contentDoc.forms[0]
			var body_part_matrix	=	this.contentDoc.getElementById("body_part_matrix").value;
			var actual_infos		=	this.contentDoc.getElementById("actual_reject_infos").value;
			var bodypart_infos		=	this.contentDoc.getElementById("actual_bodypart_infos").value;

			var actual_qty=this.contentDoc.getElementById("actual_reject_qty").value;
			var sum_avg_reject_qty=this.contentDoc.getElementById("sum_avg_reject_qty").value;
			$("#body_part_matrix_reject_"+tbl+"_"+row_id).val(body_part_matrix);
			$("#actual_reject_"+tbl+"_"+row_id).val(actual_infos);
			$("#actual_bodypart_data_"+tbl+"_"+row_id).val(bodypart_infos);
			
			if(sum_avg_reject_qty>0)
			{
				var id="txt_reject_"+tbl+"_"+row_id;
				var qcpass_id="txt_qcpass_"+tbl+"_"+row_id;
				var hidden_qcpass_id="hidden_qcpass_"+tbl+"_"+row_id;
				$("#"+id).val(Math.round(sum_avg_reject_qty));
				var new_qc_pass_qty = $("#"+hidden_qcpass_id).val() - Math.round(sum_avg_reject_qty);
				$("#"+qcpass_id).val(new_qc_pass_qty)
				$("#"+id).css('background-color','pink');
			}
			//alert(body_part_matrix);
		}
	}


	function system_number_popup()
	{
		
		var company_id=$("#cbo_company_name").val();
		var page_link='requires/cutting_entry_controller_urmi.php?action=system_number_popup&company_id='+company_id;
		var title="Search Cutting Number Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=420px,center=1,resize=0,scrolling=0','');
		emailwindow.onclose=function()
		{
			var sysNumber = this.contentDoc.getElementById("update_mst_id");
			var sysNumber=sysNumber.value.split('_');
			get_php_form_data( sysNumber[0], "load_system_mst_form", "requires/cutting_entry_controller_urmi" );
			show_list_view( sysNumber[0]+'**'+sysNumber[1]+'**'+$('#cbo_company_name').val(), 'update_order_details_list', 'cut_details_container', 'requires/cutting_entry_controller_urmi', '' ) ;

			$("#txt_system_no").val(sysNumber[0]);
			$("#cbo_company_name").attr("disabled",true);
			$("#txt_job_no").attr("disabled",true);
			$("#txt_job_year").attr("disabled",true);
			 $("#cbo_location_name").attr("disabled",true);
			$("#cbo_floor_name").attr("disabled",false);
			$("#txt_table_no").attr("disabled",true);
			$("#txt_marker_length").attr("disabled",true);
			$("#txt_marker_width").attr("disabled",true);
			$("#txt_fabric_width").attr("disabled",true);
			$("#txt_gsm").attr("disabled",true);
			$("#txt_batch_no").attr("disabled",true);
			$("#txt_entry_date").attr("disabled",true);
			$("#txt_in_time_hours").attr("disabled",true);
			$("#txt_in_time_minuties").attr("disabled",true);
			$("#txt_end_date").attr("disabled",true);

			$("#txt_out_time_minuties").attr("disabled",true);
			$("#txt_out_time_hours").attr("disabled",true);
			$("#cbo_width_dia").attr("disabled",true);
			//set_button_status(0, permission, 'fnc_cut_qc_info',1,1);
			setFieldLevelAccess(company_id);

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

	$('#txt_cutting_no').live('keydown', function(e) {

		if (e.keyCode === 13) {
			e.preventDefault();
			  scan_cut_number(trim(this.value));
		}
	});



	function scan_cut_number(str)
	{
		freeze_window(3);
		get_php_form_data( str, "load_php_mst_form", "requires/cutting_entry_controller_urmi" );
		release_freezing();
		show_list_view( "____"+str, 'order_details_list', 'cut_details_container', 'requires/cutting_entry_controller_urmi', '' ) ;

		$("#cbo_company_name").attr("disabled",true);
		$("#txt_job_no").attr("disabled",true);
		$("#txt_job_year").attr("disabled",true);

		 $("#cbo_location_name").attr("disabled",false);
		$("#cbo_floor_name").attr("disabled",false);
		$("#txt_table_no").attr("disabled",true);
		$("#txt_cutting_no").attr("disabled",true);
	}

	function total_qc_pass(ids,value,order_sequence)
	{
		var id=ids.split("_");
		var txt_reject=$("#txt_reject_"+id[2]+"_"+id[3]).val();
		var hidden_qcpass="#hidden_qcpass_"+id[2]+"_"+id[3];

		//var hidden_qcpass="#hidden_qcpass_"+id[2]+"_"+id[3];


		var txt_replace_qty=$("#txt_replace_"+id[2]+"_"+id[3]).val();
		var txt_qc_pass="#txt_qcpass_"+id[2]+"_"+id[3];
		var cutting_qty=$(hidden_qcpass).val();
		if(txt_replace_qty=="")  txt_replace_qty=0;
		if(txt_reject=="")  txt_reject=0;
		if(parseInt(txt_reject)<=parseInt(cutting_qty) && parseInt(txt_replace_qty)<=parseInt(txt_reject))
		{

			var qc_pass=((cutting_qty*1)-(txt_reject*1)+(txt_replace_qty*1));
			$(txt_qc_pass).val(qc_pass);
			total_order=$('#total_order_id').val();
			var total_qc_pass=0;
			var total_reject_quantity=0;
			var total_replace_quantity=0;
			for(var j=1; j<=total_order; j++)
			{
				 var txt_row_num="#tbl_body_"+j+"  tr";
				 var row_num=$(txt_row_num).length;
				 var hidden_order_id=$('#hidden_order_'+j).val();
				 var xxxx=0;
				 var yyyy=0;
				  var zzzz=0;
				 if(order_sequence==j)
				 {
				   	for(var i=1; i<=row_num; i++)
				   {
					   var txt_qc_id="#txt_qcpass_"+id[2]+"_"+i;
					   xxxx+=($(txt_qc_id).val()!='')?$(txt_qc_id).val()*1:0;
					  // total_qc_pass+=($(txt_qc_id).val()!='')?$(txt_qc_id).val()*1:0;

				   }

				  	$('#total_qc_qty_'+j).text(xxxx);
				  	$('#hidden_total_qc_qty_'+j).val(xxxx);
				  	for(var i=1; i<=row_num; i++)
				   	{

					   var txt_reject_id="#txt_reject_"+id[2]+"_"+i;
					   var txt_replace_id="#txt_replace_"+id[2]+"_"+i;
					   //alert($(txt_replace_id).val());
					   yyyy+=($(txt_reject_id).val()!='')?$(txt_reject_id).val()*1:0;
					   zzzz+=($(txt_replace_id).val()!='')?$(txt_replace_id).val()*1:0;
					  // total_replace+=($(txt_replace_id).val()!='')?$(txt_replace_id).val()*1:0;

				   	}
					$('#total_reject_qty_'+j).text(yyyy);
					// $('#total_reject_qty_'+j).text(yyyy);
					$('#total_replace_qty_'+j).text(zzzz);
					$('#hidden_reject_qty_'+j).val(yyyy);
					$('#hidden_replace_qty_'+j).val(zzzz);
				 }
				//alert($('#hidden_reject_qty_'+j).val())
				total_reject_quantity+=parseInt($('#hidden_reject_qty_'+j).val());
				total_qc_pass+=parseInt($('#hidden_total_qc_qty_'+j).val());
				total_replace_quantity+=parseInt($('#hidden_replace_qty_'+j).val());
				//alert(total_replace)
			}

			 $('#grand_reject_qty').text(total_reject_quantity);
			 $('#grand_qc_qty').text(total_qc_pass);
			 $('#grand_replace_qty').text(total_replace_quantity);
		}
		else
		{
			$("#"+ids).val("");
			$("#txt_replace_"+id[2]+"_"+id[3]).val("");
			$(txt_qc_pass).val(cutting_qty);
			//total_qc_pass(ids,'',1);

		}
	}

	function focace_change()
	{
		$('#txt_cutting_no').focus();
	}


	function fnc_valid_time(val,field_id)
	{
		var val_length=val.length;
		if(val_length==2)
		{
			document.getElementById(field_id).value=val+":";
		}

		var colon_contains=val.contains(":");
		if(colon_contains==false)
		{
			if(val>23)
			{
				document.getElementById(field_id).value='23:';
			}
		}
		else
		{
			var data=val.split(":");
			var minutes=data[1];
			var str_length=minutes.length;
			var hour=data[0]*1;

			if(hour>23)
			{
				hour=23;
			}

			if(str_length>=2)
			{
				minutes= minutes.substr(0, 2);
				if(minutes*1>59)
				{
					minutes=59;
				}
			}

			var valid_time=hour+":"+minutes;
			document.getElementById(field_id).value=valid_time;
		}
	}

	function numOnly(myfield, e, field_id)
	{
		var key;
		var keychar;
		if (window.event)
			key = window.event.keyCode;
		else if (e)
			key = e.which;
		else
			return true;
		keychar = String.fromCharCode(key);

		// control keys
		if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;
		// numbers
		else if ((("0123456789:").indexOf(keychar) > -1))
		{
			var dotposl=document.getElementById(field_id).value.lastIndexOf(":");
			if(keychar==":" && dotposl!=-1)
			{
				return false;
			}
			return true;
		}
		else
			return false;
	}

	function  fnc_erage_qty()
	{
		var from_qty=$("#txt_erage_from").val()*1;
		var to_qty=$("#txt_erage_to").val()*1;
		total_order=$('#total_order_id').val();
		var total_qc_pass=0;
		for(var j=1; j<=total_order; j++)
		{
			var txt_row_num="#tbl_body_"+j+"  tr";
			var row_num=$(txt_row_num).length;
			if(row_num!=0)
			{
				var row_qc_qty=$('#total_qc_qty_'+j).text()*1;
				for(var i=1; i<=row_num; i++)
				{
					txt_bundle_no = escape(document.getElementById('txt_bundle_' + j+'_'+i).innerHTML);
					txt_bundle_no=txt_bundle_no.split("-");
					bundle_prifix_num=txt_bundle_no[3]*1;
					if(bundle_prifix_num>=from_qty && bundle_prifix_num<=to_qty)
					{
						$('#txt_qcpass_' + j+'_'+i).val('');
					}
				}
				$('#total_qc_qty_'+j).text(row_qc_qty);
				total_qc_pass=total_qc_pass+row_qc_qty;
			}
		}
		$('#grand_qc_qty').text(total_qc_pass);
		$("#txt_erage_from").val('');
		$("#txt_erage_to").val('');
	}

	function  fnc_replace_qty()
	{
		var from_qty=$("#txt_replace_from").val()*1;
		var to_qty=$("#txt_replace_to").val()*1;
		var total_order=$('#total_order_id').val();
		var total_qc_pass=0;
		for(var j=1; j<=total_order; j++)
		{
			var txt_row_num="#tbl_body_"+j+"  tr";
			var row_num=$(txt_row_num).length;
			if(row_num!=0)
			{
				var row_qc_qty=$('#total_qc_qty_'+j).text()*1;
				for(var i=1; i<=row_num; i++)
				{
					txt_bundle_no = escape(document.getElementById('txt_bundle_' + j+'_'+i).innerHTML);
					txt_bundle_no=txt_bundle_no.split("-");
					var bundle_prifix_num=txt_bundle_no[3]*1;
					if(bundle_prifix_num>=from_qty && bundle_prifix_num<=to_qty)
					{
						var bdl_qty=$('#txt_qty_' + j+'_'+i).text()*1;
						$('#txt_qcpass_' + j+'_'+i).val(bdl_qty);
						row_qc_qty=row_qc_qty+bdl_qty;
					}
				}
				$('#total_qc_qty_'+j).text(row_qc_qty);
				total_qc_pass=total_qc_pass+row_qc_qty;
			}
		}
		$('#grand_qc_qty').text(total_qc_pass);
		$("#txt_replace_from").val('');
		$("#txt_replace_to").val('');
	}

	function fnc_workorder_search(supplier_id)
	{
		if( form_validation('cbo_company_name*txt_cutting_no*cbo_cutting_company','Company Name*Cutting No*Cutt. Company')==false )
		{
			return;
		}

		if($("#cbo_source").val()!=3)
		{
			return;
		}
		var company = $("#cbo_company_name").val();
		var po_break_down_id = $("#all_order_id").val();
		load_drop_down( 'requires/cutting_entry_controller_urmi', company+"_"+supplier_id+"_"+po_break_down_id, 'load_drop_down_workorder', 'workorder_td' );
	}

	function fnc_workorder_rate(id,data)
	{
		get_php_form_data(id+'_'+data, "populate_workorder_rate", "requires/cutting_entry_controller_urmi" );
	}

	function openmypage_woNo()
	{
		var cbo_company_id = $('#cbo_company_name').val();
		var cbo_service_source = $('#cbo_source').val();
		var cbo_service_company = $('#cbo_cutting_company').val()		

		if (form_validation('cbo_company_name*cbo_source*cbo_cutting_company','Company*Source*Service Company')==false)
		{
			return;
		}
		else
	  	{			
			if (form_validation('cbo_cutting_company','Service Company')==false)
			{
				return;
			}
			
			var page_link='requires/cutting_entry_controller_urmi.php?company_id='+cbo_company_id+'&cbo_service_source='+cbo_service_source+'&supplier_id='+cbo_service_company+'&action=service_booking_popup';
			var title='WO Number Popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1320px,height=390px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];				
				var theemail=this.contentDoc.getElementById("selected_booking");
				if (theemail.value!="")
  				{	  				
					var wo_data=(theemail.value).split("_");
	  				var wo_no=wo_data[1];
	  				var wo_id=wo_data[0];
					$('#txt_wo_id').val(wo_id);
					$('#txt_wo_no').val(wo_no);
					$('#txt_wo_no').attr('disabled',true);
					
  				}
				
			}
		}
	}
	function check_combo_boxes()
	{
		$( ".combo_boxes" ).each(function( index ) 
		{
			if($('#'+this.id+' option').length==2)
			{
				if($('#'+this.id+' option:first').val()==0)
				{
					$('#'+this.id).val($('#'+this.id+' option:last').val());
					 //alert($('#'+this.id+' option:last').val());
					 if (!$(this).hasClass("onchange_void")) {
						eval( $('#'+this.id).attr('onchange') );
					 }


					 var str= $(this).attr('id');
					 if( str.indexOf("company") >-1){set_field_level_access( $(this).val() );}



					}
				}
				else if($('#'+this.id+' option').length==1)
				{
					$('#'+this.id).val($('#'+this.id+' option:last').val());
					if (!$(this).hasClass("onchange_void")) {
						eval( $('#'+this.id).attr('onchange') );
					 }
				}

			/*if($('#'+this.id+'').val!=0)
			{
				//$('#'+this.id).val($('#'+this.id+' option:last').val());
				eval($('#'+this.id).attr('onchange'));
			}*/
		});
	}	

	function show_cost_details()
	{
		var system_id=$("#txt_system_no").val();
		if(system_id=="")
		{
			alert('Order No Required!');
			return;
		}

		var page_link='requires/cutting_entry_controller_urmi.php?action=show_cost_details&sys_id='+system_id;
		var title='Cost Details';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=730px,height=330px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{

		}
	}
</script>

</head>

<body onLoad="set_hotkey();focace_change()">
<div style="width:1000px;" align="center">
    <? echo load_freeze_divs ("../",$permission);  ?>
    <form name="cutandlayentry_1" id="cutandlayentry_1">
    <table width="95%" cellpadding="0" cellspacing="2" align="center">
     	<tr>
        	<td width="95%" align="center" valign="top">
            	<fieldset style="width:1000px;">
                     <legend>Cutting Entry</legend>
                        <table  width="1000" cellspacing="2" cellpadding="0" border="0">
                            <tr>
                                <td colspan="4" align="right">&nbsp;&nbsp;<b>System Number</b></td>
                                <td colspan="4"><input type="text" name="txt_system_no" id="txt_system_no" class="text_boxes" style="width:130px" placeholder="Browse " onDblClick="system_number_popup();" /></td>
                            </tr>
                            <tr>
                                <td width="90" class="must_entry_caption">&nbsp;&nbsp;Cutting No</td><!-- 11-00030  -->
                                <td ><input type="text" name="txt_cutting_no" id="txt_cutting_no" class="text_boxes" style="width:130px" placeholder="Browse or Scan" onDblClick="open_cutting_popup();" /></td>
                                <td width="90">&nbsp;&nbsp;Source </td>
                                <td width="120">
                                <?
                                	echo create_drop_down( "cbo_source", 150, $knitting_source,"", 1, "-- Select Source --", 1, "load_drop_down( 'requires/cutting_entry_controller_urmi', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_cutt_company', 'cutt_company_td' );",0,'1,3' );
                                ?>
                                </td>
                                <td width="80" class="must_entry_caption">&nbsp;&nbsp;Working Company</td>
                                <td id="cutt_company_td">
                                <?
								// Working Company Load from user managment work unit
								echo create_drop_down("cbo_sewing_company", 140, $blank_array, "", 1, "-- Select --", $selected, "");								
                                //echo create_drop_down( "cbo_cutting_company", 150, $blank_array,"", 1, "--- Select Cutting Company ---", "",0 );
                                // echo create_drop_down( "cbo_cutting_company", 150, "select id,company_name from lib_company where is_deleted=0 and status_active=1  order by company_name","id,company_name", 1, "--- Select ---", $selected_company, "load_drop_down( 'requires/cutting_entry_controller_urmi', this.value, 'load_drop_down_location', 'location_td' );check_combo_boxes();",0,0 );
                                ?>
                                </td>
                                <td width="90">&nbsp;&nbsp;Locaton </td>
                                <td width="150" id="location_td"><? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected, "","" ); ?></td>
                            </tr>
                            <tr>
                                <td>&nbsp;&nbsp;Floor/Unit</td>
                                <td id="floor_td"><? echo create_drop_down( "cbo_floor_name", 140,"select id,floor_name from lib_prod_floor where production_process=1  and status_active =1 and is_deleted=0","id,floor_name", 1, "-- Select Floor --", $selected, "",1 ); ?></td>
                                <td class="must_entry_caption">&nbsp;&nbsp;Cutting QC Date</td>
                                <td><input style="width:140px;" type="text"   class="datepicker" autocomplete="off" value="<? echo date("d-m-Y",time()); ?>" name="txt_cutting_date" id="txt_cutting_date" /></td>
                                <td class="must_entry_caption">&nbsp;&nbsp;Cutting Hour</td>
                                <td>
                                	<input name="txt_cutting_hour" id="txt_cutting_hour" class="text_boxes" style="width:140px" placeholder="24 Hour Format" onBlur="fnc_valid_time(this.value,'txt_cutting_hour');" onKeyUp="fnc_valid_time(this.value,'txt_cutting_hour');" onKeyPress="return numOnly(this,event,this.id);" maxlength="8" value="<?=date('H:i');?>" />
                                </td>
                                <td class="must_entry_caption" >&nbsp;&nbsp;Work Order</td>
                                <td id="workorder_td" title="Data come from piece rate WO"><? echo create_drop_down( "cbo_work_order", 150, $blank_array,"", 1, "-- Select Work Order--", $selected, "",0 ); ?></td>
                            </tr>
                            <tr>
                                <td>&nbsp;&nbsp;Table No </td>
                                <td><input style="width:130px;" type="text" class="text_boxes_numeric" autocomplete="off"  name="txt_table_no" id="txt_table_no"   disabled/></td>
                                <td class="must_entry_caption">&nbsp;&nbsp;Company Name</td>
                                <td><? echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/cutting_entry_controller_urmi', this.value, 'load_drop_down_location', 'location_td' );",1 ); ?></td>
                                <td>&nbsp;&nbsp;Lay Company</td>
                                <td><input style="width:140px;" type="text" class="text_boxes" autocomplete="off"  name="txt_lay_company" id="txt_lay_company"    disabled/></td>
                                <td>&nbsp;&nbsp;WO NO</td>
		                        <td>
		                            <input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:140px;" placeholder="Browse/Write/scan" onDblClick="openmypage_woNo();" title="Data come from garments service WO" />
		                            <input type="hidden" id="txt_wo_id" value="0" />
		                        </td>
                            </tr>
                             <tr>
                             	<td>&nbsp;&nbsp;Shift</td>
		                        <td>
		                            <? echo create_drop_down( "cbo_shift_name", 140, $shift_name,"", 1, "-- Select Shift --", $selected, "","" ); ?>
		                        </td>
                                <td>&nbsp;&nbsp;Remarks</td>
                                <td colspan="3">
                                   <input style="width:377px;" type="text" class="text_boxes" autocomplete="off"  name="txt_remarks" id="txt_remarks" />
								
								<td>&nbsp;&nbsp;Check No. Pannel </td>
								<td><input style="width:140px;" type="text"  class="text_boxes_numeric" name="txt_check_no_of_pannel" id="txt_check_no_of_pannel" placeholder="Double click" onDblClick="open_checkNoPanel_popup();" /></td>								
                                    <input  type="hidden" class="text_boxes" name="txt_marker_length" id="txt_marker_length"/>
                                    <input  type="hidden" class="text_boxes_numeric"   name="txt_marker_width" id="txt_marker_width" />
                                    <input  type="hidden" class="text_boxes" name="txt_job_no" id="txt_job_no"/>
                                    <input type="hidden" class="text_boxes" autocomplete="off"  name="txt_batch_no" id="txt_batch_no"/>
                                    <input type="hidden" name="txt_fabric_width" id="txt_fabric_width" class="text_boxes_numeric"/>
                                    <input type="hidden" class="text_boxes_numeric"   name="txt_gsm" id="txt_gsm"/>
                                    <input  type="hidden" class="text_boxes" name="txt_job_year" id="txt_job_year"/>
                                    <input  type="hidden" class="text_boxes" name="txt_buyer_name" id="txt_buyer_name"/>

                                    <input type="hidden" name="txt_in_time_hours" id="txt_in_time_hours" class="text_boxes_numeric" placeholder="HH"  style="width:30px;"  onKeyUp="fnc_move_cursor(this.value,'txt_in_time_hours','txt_in_time_minuties',2,23);"  disabled/>
                                    <input type="hidden" name="txt_in_time_minuties" id="txt_in_time_minuties" class="text_boxes_numeric" placeholder="MM"  style="width:30px;" onKeyUp="fnc_move_cursor(this.value,'txt_in_time_minuties','txt_in_time_seconds',2,59)" onBlur="fnc_intime_populate(this.value,'txt_in_time_hours')"  disabled/>

                                     <input style="width:130px;" type="hidden" class="datepicker" autocomplete="off"  name="txt_end_date" id="txt_end_date"  disabled/>
                                     <input style="width:140px;" type="hidden" class="datepicker" autocomplete="off"  name="txt_entry_date" id="txt_entry_date"  disabled />

                                    <input type="hidden" name="txt_out_time_hours" id="txt_out_time_hours" class="text_boxes_numeric" placeholder="HH"  style="width:30px;" onKeyUp="fnc_move_cursor(this.value,'txt_out_time_hours','txt_out_time_minuties',2,23);"  disabled/>
                                    <input type="hidden" name="txt_out_time_minuties" id="txt_out_time_minuties" class="text_boxes_numeric" placeholder="MM"  style="width:30px;" onKeyUp="fnc_move_cursor(this.value,'txt_out_time_minuties','txt_out_time_seconds',2,59)" onBlur="fnc_outtime_populate(this.value,'txt_out_time_hours')" disabled/>

                                    <input type="hidden" name="cbo_width_dia" id="cbo_width_dia"  />

                                    <input type="hidden" name="update_job_no" id="update_job_no"  />
                                    <input type="hidden" name="all_order_id" id="all_order_id"  />
                                    <input type="hidden" name="update_id" id="update_id"  />
                            		<input type="hidden" id="wip_valuation_for_accounts"/>
                                    <input type="hidden" name="txt_cut_prifix" id="txt_cut_prifix" />
                                    <input type="hidden" id="piece_rate_data_string" name="piece_rate_data_string" />
                                </td>
								<td>&nbsp;</td>
								<td><input type="button" id="wip_valuation_for_accounts_button" name="" style="width:90px;display:none;" class="formbutton" value="Cost Details" onClick="show_cost_details();"></td>
                            </tr>
                      </table>
                 </fieldset>
              </td>
         </tr>
         <tr>
             <td align="center" valign="top" id="po_list_view"></td>
         </tr>

	</table>
        <fieldset style="width:700px; margin-top:10px">
            <legend>Cutting QC</legend>
            <div style="width:680px; margin-top:10px" id="cut_details_container" align="left"></div>
        </fieldset>
    </form>
	</div>
</body>

<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>

$('#txt_cutting_no').focus();
</script>

<script>
	$(function(){
		for (var property in mandatory_field_arr) {
			$("#"+mandatory_field_arr[property]).parent().prev('td').css("color", "blue");
		}
	});
</script>

</html>