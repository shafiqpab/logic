<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Woven Finish Fabric Issue Roll Wise
				
Functionality	:	
JS Functions	:
Created by		:	Md Didarul Alam
Creation date 	: 	27-01-2018
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
echo load_html_head_contents("Woven Finish Fabric Issue Roll Wise","../../", 1, 1, $unicode,'',''); 

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	var scanned_barcode=new Array();
<? 
	$scanned_barcode_array=array(); 
	$barcode_dtlsId_array=array(); 
	$barcode_trnasId_array=array(); 
	$barcode_rollTableId_array=array();

	$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
	$company_name_array=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$batch_name_array=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	
	$jsbarcode_buyer_name_array= json_encode($buyer_name_array);
	echo "var jsbuyer_name_array = ". $jsbarcode_buyer_name_array . ";\n";

	
	$composition_arr=array(); $constructtion_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	$jsconstructtion_arr=json_encode($constructtion_arr);
	echo "var constructtion_arr = ". $jsconstructtion_arr . ";\n";
	
	$jscomposition_arr=json_encode($composition_arr);
	echo "var composition_arr = ". $jscomposition_arr . ";\n";
	

	$sql_item_description = sql_select("SELECT a.lib_yarn_count_deter_id ,b.po_break_down_id,a.item_number_id,a.body_part_id FROM wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id = b.pre_cost_fabric_cost_dtls_id and b.cons!=0 GROUP BY a.lib_yarn_count_deter_id ,b.po_break_down_id,a.item_number_id,a.body_part_id");

	$item_description_id_arr=array();
	foreach($sql_item_description as $ival)
	{
		$item_description_id_arr[$ival[csf('po_break_down_id')]][$ival[csf('body_part_id')]][$ival[csf('lib_yarn_count_deter_id')]]=$ival[csf('item_number_id')];
	} 
	$js_item_description_id_arrs= json_encode($item_description_id_arr);
	echo "item_description_id_arr = ". $js_item_description_id_arrs . ";\n";
	
	$jsgarments_item= json_encode($garments_item);
	echo "garments_item = ". $jsgarments_item . ";\n";
	
?>

    garments_item[0]="-- Select Gmt. Item --";

	function openmypage_issue()
	{ 
		var cbo_company_id = $('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/woven_finish_fabric_issue_roll_wise_controller.php?cbo_company_id='+cbo_company_id+'&action=issue_popup','Issue Popup', 'width=780px,height=450px,center=1,resize=1,scrolling=0','../')

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var issue_id=this.contentDoc.getElementById("hidden_system_id").value;	 //challan Id and Number
			
			if(issue_id!="")
			{
				fnc_reset_form();
				get_php_form_data(issue_id, "populate_data_from_data", "requires/woven_finish_fabric_issue_roll_wise_controller");
				var html=return_global_ajax_value(issue_id, 'populate_barcode_data_update', '', 'requires/woven_finish_fabric_issue_roll_wise_controller');
				if(trim(html)!="")
				{
					$("#scanning_tbl tbody").html(html);
					calculate_total();
					
					if($("#cbo_issue_purpose").val()==44)
					{
						var reprocess_data='';
						$("#scanning_tbl").find('tbody tr').each(function()
						{
							var reProcess=$(this).find('input[name="reProcess[]"]').val();
							var preRerocess=$(this).find('input[name="preRerocess[]"]').val();
							var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
							
							if(reprocess_data="") reprocess_data=reprocess_data+",";
							reprocess_data=reprocess_data+barcodeNo+"_"+reProcess;
						});
						get_php_form_data(reprocess_data, "populate_data_for_validation", "requires/woven_finish_fabric_issue_roll_wise_controller");
					}
						
					
				}
				set_button_status(1, permission, 'fnc_grey_fabric_issue_roll_wise',1);


				$("#print2").removeClass('formbutton_disabled');
				$("#print2").addClass('formbutton');


				$("#print_barcode").removeClass('formbutton_disabled');
				$("#print_barcode").addClass('formbutton');
				$("#btn_fabric_details").removeClass('formbutton_disabled');
				$("#btn_fabric_details").addClass('formbutton');
                calculate_total();
			}
		}
	}
	
	function openmypage_barcode()
	{ 
		var company_id=$('#cbo_company_id').val();
		var batch_id=$('#txt_batch_id').val();
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/woven_finish_fabric_issue_roll_wise_controller.php?company_id='+company_id+'&batch_id='+batch_id+'&action=barcode_popup','Barcode Popup', 'width=780px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{										
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var barcode_nos=this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos
			if(barcode_nos!="")
			{
				
				create_row(0,barcode_nos);
				//set_all_onclick();
			}
            calculate_total();
		}
	}



	
	function reqsn_popup()
	{ 
		var company_id=$('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/woven_finish_fabric_issue_roll_wise_controller.php?company_id='+company_id+'&action=requisition_popup','Requisition Popup', 'width=780px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var reqsn_id=(this.contentDoc.getElementById("hidden_reqn_id").value).split("_"); //Barcode Nos
			if(reqsn_id!="")
			{
				$('#txt_rqn_no').val(reqsn_id[1]);	
				var list_view = trim(return_global_ajax_value(reqsn_id[0], 'populate_list_view', '', 'requires/woven_finish_fabric_issue_roll_wise_controller'));
				$("#list_product_container").html(list_view);
			}
		}
	}

	$('#txt_rqn_no').live('keydown', function(e) {
		if (e.keyCode === 13) 
		{
			e.preventDefault();
			scan_rqn_no(this.value); 
		}
	});
	
	function scan_rqn_no(str)
	{
		var response=return_global_ajax_value(str, 'check_reqn_no', '', 'requires/woven_finish_fabric_issue_roll_wise_controller');
		if(response==0)
		{
			alert("Requisition No not found");	
			$("#txt_rqn_no").val('');
		}
		else
		{
			var list_view = trim(return_global_ajax_value(response, 'populate_list_view', '', 'requires/woven_finish_fabric_issue_roll_wise_controller'));
			$("#list_product_container").html(list_view);
		}
   	}
	
	function check_batch(data)
	{
		if(data=="")
		{
			$('#txt_batch_id').val('');
			return;	
		}
		var cbo_company_id=$('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			$('#txt_batch_no').val('');
			$('#txt_batch_id').val('');
			return;
		}
		var batch_id=return_global_ajax_value( data+"**"+cbo_company_id, 'check_batch_no', '', 'requires/woven_finish_fabric_issue_roll_wise_controller');
		if(batch_id==0)
		{
			alert("Batch No Found");
			$('#txt_batch_no').val('');
			
			return;
		}
		else $('#txt_batch_id').val(batch_id);
		
	}
	


	function generate_report_file(data,action)
	{
		window.open("requires/woven_finish_fabric_issue_roll_wise_controller.php?data=" + data+'&action='+action, true );
	}

	function fnc_report_print2()
	{

			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val()+'*'+report_title,'finish_issue_print2');
			return;
	}




	function fnc_grey_fabric_issue_roll_wise( operation )
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val()+'*'+report_title,'finish_issue_print');
			return;
		}


	 	if(form_validation('cbo_company_id*cbo_dyeing_source*cbo_dyeing_company*txt_issue_date*cbo_issue_purpose','Company*Dyeing Source*Dyeing Company*Issue Date*Issue Purpose')==false)
		{
			return; 
		}
                var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_issue_date').val(), current_date)==false)
		{
			alert("Issue Date Can not Be Greater Than Current Date");
			return;
		}
                
		var j=0; var dataString='';
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var recvBasis=$(this).find('input[name="recvBasis[]"]').val();
			var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
			var batchId=$(this).find('input[name="batchId[]"]').val();
			var progBookPiId=$(this).find('input[name="progBookPiId[]"]').val();
			
			var productId=$(this).find('input[name="productId[]"]').val();
			var orderId=$(this).find('input[name="orderId[]"]').val();
			var rollId=$(this).find('input[name="rollId[]"]').val();
			var rollWgt=$(this).find('input[name="rollWgt[]"]').val();
			var rollNo=$(this).find("td:eq(2)").text();
			var dia=$(this).find("td:eq(9)").text();
			
			var bodyPartId=$(this).find('input[name="bodyPartId[]"]').val();
			var colorId=$(this).find('input[name="colorId[]"]').val();
			var deterId=$(this).find('input[name="deterId[]"]').val();
			var diatypeId=$(this).find('input[name="diatypeId[]"]').val();
			
			var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
			var transId=$(this).find('input[name="transId[]"]').val();
			var rolltableId=$(this).find('input[name="rolltableId[]"]').val();
			var rollRate=$(this).find('input[name="rollRate[]"]').val();
			var gmtItemId=$(this).find('select[name="cboItemName[]"]').val();
			
			var reProcess=$(this).find('input[name="reProcess[]"]').val();
			var preRerocess=$(this).find('input[name="preRerocess[]"]').val();
			var IsSalesId=$(this).find('input[name="IsSalesId[]"]').val();
			
			j++;
			dataString+='&recvBasis_' + j + '=' + recvBasis + '&barcodeNo_' + j + '=' + barcodeNo + '&progBookPiId_' + j + '=' + progBookPiId + '&productId_' + j + '=' + productId + '&orderId_' + j + '=' + orderId + '&rollId_' + j + '=' + rollId + '&rollWgt_' + j + '=' + rollWgt + '&bodyPartId_' + j + '=' + bodyPartId + '&deterId_' + j + '=' + deterId + '&colorId_' + j + '=' + colorId + '&diatypeId_' + j + '=' + diatypeId + '&batchId_' + j + '=' + batchId + '&dia_' + j + '=' + dia + '&dtlsId_' + j + '=' + dtlsId + '&transId_' + j + '=' + transId + '&rolltableId_' + j + '=' + rolltableId + '&rollNo_' + j + '=' + rollNo+ '&rollRate_' + j + '=' + rollRate+ '&gmtItemId_' + j + '=' + gmtItemId+ '&reProcess_' + j + '=' + reProcess+ '&preRerocess_' + j + '=' + preRerocess+ '&IsSalesId_' + j + '=' + IsSalesId;
			
		});
		
		if(j<1)
		{
			alert('No data');
			return;
		}
		
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_issue_no*cbo_company_id*cbo_dyeing_source*cbo_dyeing_company*txt_issue_date*cbo_issue_purpose*txt_batch_no*cbo_store_name*txt_batch_id*update_id*txt_deleted_id*txt_rqn_no',"../../")+dataString;
	//alert(data);return;
		freeze_window(operation);
		
		http.open("POST","requires/woven_finish_fabric_issue_roll_wise_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_grey_fabric_issue_roll_wise_Reply_info;
	}

	function fnc_grey_fabric_issue_roll_wise_Reply_info()
	{
		if(http.readyState == 4) 
		{
			//alert(trim(http.responseText))
			//release_freezing();return;
			var response=trim(http.responseText).split('**');
			if(response[0]*1 == 20)
			{
					alert(response[1]);
					release_freezing();
					return;
			} 
			show_msg(response[0]);
			
			if((response[0]==0 || response[0]==1))
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_issue_no').value = response[2];
				$('#txt_deleted_id').val( '' );
				add_dtls_data(response[3]);
				set_button_status(1, permission, 'fnc_grey_fabric_issue_roll_wise',1);
				$("#print_barcode").removeClass('formbutton_disabled');
				$("#print_barcode").addClass('formbutton');
				$("#btn_fabric_details").removeClass('formbutton_disabled');
				$("#btn_fabric_details").addClass('formbutton');
			}
			release_freezing();
		}
	}
	
	function create_row(is_update,barcode_nos)
	{		
		var cbo_company_id = $('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			$('#txt_bar_code_num').val();
			return;
		}

		var barcode_data=trim(return_global_ajax_value(barcode_nos, 'populate_barcode_data', '', 'requires/woven_finish_fabric_issue_roll_wise_controller'));
		
		var row_num=$('#txt_tot_row').val();
		//var bar_code=$('#txt_bar_code_num').val();
	    //	var bar_code=trim(barcode_no);
		var num_row =$('#scanning_tbl tbody tr').length; 
		
		if(barcode_data==0)
		{
			alert('Barcode is Not Valid');
			$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
			{
				$('#messagebox_main', window.parent.document).html('Barcode is Not Valid.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
			});
			$('#txt_bar_code_num').val('');
			return; 
		}
		if(barcode_data==99)
		{
			alert('Barcode is already scanned.');
			$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
			{
				$('#messagebox_main', window.parent.document).html('Barcode is already scanned.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
			});
			$('#txt_bar_code_num').val('');
			return; 
		}
		var barcode_datas=barcode_data.split("_");
		
		//alert(barcode_data)
		for(var k=0; k<barcode_datas.length; k++)
		{
			var data=barcode_datas[k].split("**");
			var bar_code=data[0];
			scanned_barcode.push(bar_code);
			var company_id=data[1];
			var roll_no=data[2];
			var roll_id=data[3];
			var body_part=data[5];
			var body_part_id=data[4];
			var bwo=data[6];
			var receive_basis=data[7];
			var receive_basis_id=data[8];
			var booking_no=data[9];
			var booking_id=data[10];
			var color=data[11];
			var color_id=data[12];
			var batch_id=data[13];
			var batch_name=data[14];
			var prod_id=data[15];
			var deter_id=data[16];
			var cons_comp=constructtion_arr[deter_id]+", "+composition_arr[deter_id];
			var width=data[17];
			var qnty=data[18];
			var rate=data[19];
			var booking_without_order=data[20];
			var reprocess=data[21];
			var gmts_item_id = data[22];
			
			var po_id=data[23];
			var buyer_id=data[24];
			var buyer_name=jsbuyer_name_array[buyer_id];
			var po_no=data[25];
			var job_no=data[26];
			var is_sales=data[27];

			var bar_code_no=$('#barcodeNo_'+row_num).val();
			if(bar_code_no!="")
			{
				row_num++;
				$("#scanning_tbl tbody tr:first").clone().find("td,input,select").each(function()
				{
					$(this).attr({ 

					  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
					  'value': function(_, value) { return value }              
					});
				}).end().prependTo("#scanning_tbl");
				
				$("#scanning_tbl tbody tr:first").removeAttr('id').attr('id','tr_'+row_num);
			}
				
			$("#sl_"+row_num).text(row_num);
			$("#barcode_"+row_num).text(bar_code);
			$("#roll_"+row_num).text(roll_no);
			$("#batch_"+row_num).text(batch_name);
			$("#prodId_"+row_num).text(prod_id);
			$("#bodyPart_"+row_num).text(body_part);
			$("#cons_"+row_num).text(cons_comp);
			$("#dia_"+row_num).text(width);
			$("#color_"+row_num).text(color);
			$("#rollWeight_"+row_num).text(qnty);
			$("#buyer_"+row_num).text(buyer_name);
			$("#job_"+row_num).text(job_no);
			$("#order_"+row_num).text(po_no);
			$("#basis_"+row_num).text(receive_basis);
			$("#progBookPiNo_"+row_num).text(booking_no);
			$("#barcodeNo_"+row_num).val(bar_code);
			$("#recvBasis_"+row_num).val(receive_basis_id);
			$("#progBookPiId_"+row_num).val(booking_id);
			$("#productId_"+row_num).val(prod_id);
			$("#orderId_"+row_num).val(po_id);
			$("#rollId_"+row_num).val(roll_id);
			$("#rollWgt_"+row_num).val(qnty);
			$("#reProcess_"+row_num).val(reprocess);
			$("#preRerocess_"+row_num).val(reprocess);
			$("#cboItemName_"+row_num).val(gmts_item_id);
			$("#IsSalesId_"+row_num).val(is_sales);
			$("#colorId_"+row_num).val(color_id);
			$("#deterId_"+row_num).val(deter_id);
			$("#bodyPartId_"+row_num).val(body_part_id);
			$("#batchId_"+row_num).val(batch_id);
			$("#rollRate_"+row_num).val(rate);
			$("#jobNo_"+row_num).val(job_no);
			$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
		}
		
		$('#txt_tot_row').val(row_num);
		$('#txt_bar_code_num').val('');
		$('#txt_bar_code_num').focus();
	}
	
	$('#txt_bar_code_num').live('keydown', function(e) {
		if (e.keyCode === 13) 
		{
			e.preventDefault();
			var bar_code=$('#txt_bar_code_num').val();
			
			if( jQuery.inArray( bar_code, scanned_barcode )>-1) 
			{
				alert('Barcode is already scanned.');
				$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
				{
					$('#messagebox_main', window.parent.document).html('Barcode is already scanned.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
				});
				$('#txt_bar_code_num').val('');
				return; 
			}
			create_row(0,bar_code);
		}
	});
	
	function add_dtls_data( data )
	{
		var barcode_dtlsId_array=new Array(); var barcode_trnasId_array=new Array(); var barcode_rollTableId_array=new Array();
		var barcode_datas=data.split(",");
		for(var k=0; k<barcode_datas.length; k++)
		{
			var datas=barcode_datas[k].split("__");
			var barcode_no=datas[0];
			var dtls_id=datas[1];
			var trans_id=datas[2];
			var roll_table_id=datas[3];
			
			barcode_dtlsId_array[barcode_no] = dtls_id;
			barcode_trnasId_array[barcode_no] = trans_id;
			barcode_rollTableId_array[barcode_no] = roll_table_id;
		}
		
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
			var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
			
			if(dtlsId=="") 
			{
				$(this).find('input[name="dtlsId[]"]').val(barcode_dtlsId_array[barcodeNo]);
				$(this).find('input[name="transId[]"]').val(barcode_trnasId_array[barcodeNo]);	
				$(this).find('input[name="rolltableId[]"]').val(barcode_rollTableId_array[barcodeNo]);	
			}
		});
	}
	
	function fn_deleteRow( rid )
	{
		var num_row =$('#scanning_tbl tbody tr').length;
		var bar_code =$("#barcodeNo_"+rid).val();
		var rolltableId =$("#rolltableId_"+rid).val();
		var txt_deleted_id=$('#txt_deleted_id').val();
		
		if( jQuery.inArray( bar_code,issue_barcode )>-1) 
		{ 
			alert('Sorry! Barcode Already Received by Cutting.'); 
			return; 
		}

		if(num_row==1)
		{
			$('#tr_'+rid+' td:not(:last-child)').each(function(index, element) {
				$(this).html('');
			});
			
			$('#tr_'+rid).find(":input:not(:button)").val('');
		}
		else
		{
			$("#tr_"+rid).remove();
		}
		
		//var selected_id='';
		if(rolltableId!='')
		{
			if(trim(txt_deleted_id)=='') txt_deleted_id=rolltableId; else txt_deleted_id=txt_deleted_id+','+rolltableId;
			$('#txt_deleted_id').val(txt_deleted_id);
		}
		
		var index = scanned_barcode.indexOf(bar_code);
		//alert(index);
		scanned_barcode.splice(index,1);
        calculate_total();
	}
	
	function openmypage_batchNo()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			var page_link='requires/woven_finish_fabric_issue_roll_wise_controller.php?cbo_company_id='+cbo_company_id+'&action=batch_number_popup';
			var title='Batch Number Popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=820px,height=390px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;
				var batch_no=this.contentDoc.getElementById("hidden_batch_no").value; //Access form field with id="emailfield"
				
				if(batch_id!="")
				{
					$('#txt_batch_no').val(batch_no);
					$('#txt_batch_id').val(batch_id);
				}
			}
		}
	}
	
	
	function fnc_reset_form()
	{
		   $('#scanning_tbl tbody tr').remove();

			var html='<tr id="tr_1" align="center" valign="middle"><td width="35" id="sl_1"></td><td width="80" id="barcode_1"></td><td width="50" id="roll_1"></td><td width="60" id="batch_1"></td><td width="60" id="prodId_1"></td><td id="gmtItemTd_1" width="140"><?php echo create_drop_down( "cboItemName_1", 130, $garments_item,"", 1, "-- Select Gmt. Item --", "", "change_garments_item(this.id,this.value)",0,0,"","","","","","cboItemName[]" );?></td><td style="word-break:break-all;" width="80" id="bodyPart_1"></td><td style="word-break:break-all;" width="130" id="cons_1" align="left"></td><td style="word-break:break-all;" width="50" id="gsm_1"></td><td style="word-break:break-all;" width="50" id="dia_1"></td><td style="word-break:break-all;" width="70" id="color_1"></td><td width="70" align="right" id="rollWeight_1"></td><td style="word-break:break-all;" width="60" id="buyer_1"></td><td style="word-break:break-all;" width="80" id="job_1"></td><td style="word-break:break-all;" width="80" id="order_1" align="left"></td><td style="word-break:break-all;" width="100" id="progBookPiNo_1"></td><td style="word-break:break-all;" width="70" id="basis_1"></td><td id="button_1" align="center"><input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" /><input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/><input type="hidden" name="recvBasis[]" id="recvBasis_1"/><input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/><input type="hidden" name="productId[]" id="productId_1"/><input type="hidden" name="orderId[]" id="orderId_1"/><input type="hidden" name="rollId[]" id="rollId_1"/><input type="hidden" name="rollWgt[]" id="rollWgt_1"/><input type="hidden" name="colorId[]" id="colorId_1"/><input type="hidden" name="bodyPartId[]" id="bodyPartId_1"/><input type="hidden" name="batchId[]" id="batchId_1"/><input type="hidden" name="dtlsId[]" id="dtlsId_1"/><input type="hidden" name="transId[]" id="transId_1"/><input type="hidden" name="rolltableId[]" id="rolltableId_1"/><input type="hidden" name="deterId[]" id="deterId_1"/><input type="hidden" name="diatypeId[]" id="diatypeId_1"/><input type="hidden" name="rollRate[]" id="rollRate_1"/> <input type="hidden" name="jobNo[]" id="jobNo_1"/><input type="hidden" name="reProcess[]" id="reProcess_1"/><input type="hidden" name="preRerocess[]" id="preRerocess_1"/></td></tr>';
		
		
		$('#cbo_company_id').val(0);
		$('#cbo_company_id').attr('disabled',false);
		$('#cbo_dyeing_source').val(0);
		$('#cbo_dyeing_company').val(0);
		$('#txt_tot_row').val(1);
		$('#update_id').val('');
		$('#txt_issue_no').val('');
		$('#txt_issue_date').val('');
		$('#txt_deleted_id').val('');
		$('#cbo_issue_purpose').val(0);
		$('#txt_batch_no').val('');
		$('#txt_batch_id').val('');
		$("#scanning_tbl tbody").html(html);	
	}
	
	function barcode_print()
	{
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val(),'issue_challan_print');
	}
	
	function fabric_details()
	{
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val(),'fabric_details_print');
	}
	
	function change_garments_item(id,value)
	{
		var id_prifix_arr=id.split("_");
		var selected_order=$("#orderId_"+id_prifix_arr[1]).val();
		var selected_deterId=$("#deterId_"+id_prifix_arr[1]).val();
		var selected_bodyPart=$("#bodyPartId_"+id_prifix_arr[1]).val();
	
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var order_no=$(this).find('input[name="orderId[]"]').val();
			var body_part=$(this).find('input[name="bodyPartId[]"]').val();
			var determination_id=$(this).find('input[name="deterId[]"]').val();
			if(selected_order==order_no && selected_deterId==determination_id && selected_bodyPart==body_part) 
			{
				$(this).find('select[name="cboItemName[]"]').val(value);
			}
		});
	}
        
	function calculate_total()
	{
		var total_roll_weight='';
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var rollWgt=$(this).find('input[name="rollWgt[]"]').val();
			total_roll_weight=total_roll_weight*1+rollWgt*1;
		});
		$("#roll_weight_total").val(total_roll_weight.toFixed(2));	
	}
	
</script>
</head>
<body onLoad="set_hotkey();$('#txt_bar_code_num').focus();">
    <div align="center" style="width:100%;">
		<? echo load_freeze_divs ("../../",$permission); ?>
        <form name="rollscanning_1" id="rollscanning_1"  autocomplete="off"  >
         <div style="width:1300px;"> 
         <div style="float:left; " align="center">
            <fieldset style="width:810px;">
				<legend>Roll Scanning</legend>
                <table cellpadding="0" cellspacing="2" width="800">
                    <tr>
                        <td colspan="6" align="center"><b>Issue No&nbsp;</b>
                        	<input type="text" name="txt_issue_no" id="txt_issue_no" class="text_boxes" style="width:140px;" onDblClick="openmypage_issue()" placeholder="Double Click To Search" readonly/>
                            <input type="hidden" name="update_id" id="update_id"/>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" class="must_entry_caption">Company</td>
                        <td>
                            <? 
                                //echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond","id,company_name", 1, "-- Select --", 0, "",0 );

                                echo create_drop_down("cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Company--", 0, "load_drop_down('requires/woven_finish_fabric_issue_roll_wise_controller', this.value, 'load_drop_down_store', 'store_td' );");
                            ?>
                        </td>
                        <td class="must_entry_caption" align="right">Service Source</td>
                        <td>
							<?
                                echo create_drop_down( "cbo_dyeing_source", 152, $knitting_source, "", 1, "-- Select --", $selected, "load_drop_down( 'requires/woven_finish_fabric_issue_roll_wise_controller',this.value+'**'+$('#cbo_company_id').val(),'load_drop_down_knitting_com','dyeing_company_td' );","","1,3" );
                            ?>
                        </td>
                        <td align="right" class="must_entry_caption">Service Company</td>
                        <td id="dyeing_company_td">
                            <?
                                echo create_drop_down( "cbo_dyeing_company", 152, $blank_array,"", 1, "-- Select --", $selected, "","","" );
                            ?>
                        </td>
                    </tr>
                    <tr>
                    	<td align="right" class="must_entry_caption" width="100">Issue Date</td>
                        <td><input type="text" name="txt_issue_date" id="txt_issue_date" class="datepicker" style="width:140px;" value="<? echo date("d-m-Y"); ?>" readonly /></td>
                        <td align="right" class="must_entry_caption" width="100">Issue Purpose</td>
                        <td>
							<? 
								echo create_drop_down( "cbo_issue_purpose", 152, $yarn_issue_purpose,"", 1, "-- Select Purpose --",9, "","","3,4,8,9,10,44" ); 
							?>
                        </td>
                        <td align="right">Batch No</td>
                        <td>
                        	<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:140px;" placeholder="Write / Browse" onDblClick="openmypage_batchNo();" onChange="check_batch(this.value);" />
                            <input type="hidden" id="txt_batch_id" name="txt_batch_id"/>
                        </td>
                    </tr>
                    <tr>
                    	<td height="5" colspan="6"></td>
                    </tr>
                    
                      <tr>
                    	<td align="right"  width="100"><strong>Reqsn. No</strong></td>
                        <td>
                        <input type="text" name="txt_rqn_no" id="txt_rqn_no" class="text_boxes" style="width:140px; text-align:left" onDblClick="reqsn_popup()" placeholder="Browse/Write/scan"/>
                        </td>
                        <td align="right"  width="100"><strong>Roll Number</strong></td>
                        <td>
						<input type="text" name="txt_bar_code_num" id="txt_bar_code_num" class="text_boxes_numeric" style="width:140px;" onDblClick="openmypage_barcode()" placeholder="Browse/Write/scan"/>
                        </td>
                        <td align="right"><strong>Store Name</strong></td>
                        <td id="store_td">
                        	<?
								echo create_drop_down("cbo_store_name", 152, $blank_array, "", 1, "-- Select Store --", 0, "");
							?>
                        </td>
                    </tr>
                   
                </table>
    
			</fieldset>
            </div>&nbsp;&nbsp;&nbsp;
            <div id="list_product_container" style="max-height:auto; float:left; width:450px; overflow: hidden; padding-top:0px; margin-left:10px;"></div>
            </div>
            
			<br><br><br>
           
			<fieldset style="width:1370px;text-align:left; margin-top:20px;">
				<style>
                    #scanning_tbl tr td
                    {
                        background-color:#FFF;
                        color:#000;
                        border: 1px solid #666666;
                        line-height:12px;
                        height:20px;
                        overflow:auto;
                    }
                </style>
				<table cellpadding="0" width="1350" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="35">SL</th>
                        <th width="80">Barcode No</th>
                        <th width="50">Roll No</th>
                        <th width="60">Batch No</th>
                        <th width="60">Product Id</th>
                        <th width="140">Gmt. Item</th>
                        <th width="80">Body Part</th>
                        <th width="130">Construction/ Composition</th>
                        <th width="50">Dia</th>
                        <th width="70">Color</th>
                        <th width="70">Roll Wgt.</th>
                        <th width="60">Buyer</th>
                        <th width="80">Job No</th>
                        <th width="80">Order No</th>
                        <th width="100">Program/ Booking/PI No</th>
                        <th width="70">Basis</th>
                        <th></th>
                    </thead>
                 </table>
                 <div style="width:1370px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="1350" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody>
                        	<tr id="tr_1" align="center" valign="middle">
                                <td width="35" id="sl_1"></td>
                                <td width="80" id="barcode_1"></td>
                                <td width="50" id="roll_1"></td>
                                <td width="60" id="batch_1"></td>
                                <td width="60" id="prodId_1"></td>
                                <td id="gmtItemTd_1" width="140" >
									<?
                                    	echo create_drop_down( "cboItemName_1", 130, $garments_item,"", 1, "-- Select Gmt. Item --", "", "change_garments_item(this.id,this.value)",1,0,"","","","","","cboItemName[]" );	
                                    ?>
                                </td>
                                <td style="word-break:break-all;" width="80" id="bodyPart_1"></td>
                                <td style="word-break:break-all;" width="130" id="cons_1" align="left"></td>
                                <td style="word-break:break-all;" width="50" id="dia_1"></td>
                                <td style="word-break:break-all;" width="70" id="color_1"></td>
                                <td width="70" align="right" id="rollWeight_1"></td>
                                <td style="word-break:break-all;" width="60" id="buyer_1"></td>
                                <td style="word-break:break-all;" width="80" id="job_1"></td>
                                <td style="word-break:break-all;" width="80" id="order_1" align="left"></td>
                                <td style="word-break:break-all;" width="100" id="progBookPiNo_1"></td>
                                <td style="word-break:break-all;" width="70" id="basis_1"></td>
                                <td id="button_1" align="center">
                                    <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                                    <input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/>
                                    <input type="hidden" name="recvBasis[]" id="recvBasis_1"/>
                                    <input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/>
                                    <input type="hidden" name="productId[]" id="productId_1"/>
                                    <input type="hidden" name="orderId[]" id="orderId_1"/>
                                    <input type="hidden" name="rollId[]" id="rollId_1"/>
                                    <input type="hidden" name="rollWgt[]" id="rollWgt_1"/>
                                    <input type="hidden" name="colorId[]" id="colorId_1"/>
                                    <input type="hidden" name="bodyPartId[]" id="bodyPartId_1"/>
                                    <input type="hidden" name="batchId[]" id="batchId_1"/>
                                    <input type="hidden" name="dtlsId[]" id="dtlsId_1"/>
                                    <input type="hidden" name="transId[]" id="transId_1"/>
                                    <input type="hidden" name="rolltableId[]" id="rolltableId_1"/>
                                    <input type="hidden" name="deterId[]" id="deterId_1"/>
                                    <input type="hidden" name="diatypeId[]" id="diatypeId_1"/>
                                    <input type="hidden" name="rollRate[]" id="rollRate_1"/>
                                    <input type="hidden" name="jobNo[]" id="jobNo_1"/>
                                    <input type="hidden" name="reProcess[]" id="reProcess_1"/>
                                    <input type="hidden" name="preRerocess[]" id="preRerocess_1"/>
                                    <input type="hidden" name="IsSalesId[]" id="IsSalesId_1"/>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <th colspan="10">Grand Total</th>
                            <th width="70"><strong><input type="text" name="roll_weight_total" style="width:70px; text-align: right;"  id="roll_weight_total" readonly disabled></strong></th>
                            <th colspan="7"> </th>
                        </tfoot>
                	</table>
                </div>
                <br>
                <table width="1340" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                    <tr>
                        <td align="center" class="button_container">
                        	<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
                            <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes" value="">
                            <? 
                            	echo load_submit_buttons($permission,"fnc_grey_fabric_issue_roll_wise",0,1,"fnc_reset_form()",1);
                            ?>
                            <input type="button" name="print2" id="print2" class="formbutton_disabled" value="Print2" style=" width:100px" onClick="fnc_report_print2(5);" >
                            <input type="button" name="print_barcode" id="print_barcode" class="formbutton_disabled" value="Print Barcode" style=" width:100px" onClick="barcode_print();" >
                            <input type="button" name="btn_fabric_details" id="btn_fabric_details" class="formbutton_disabled" value="Fabric Details" style=" width:100px" onClick="fabric_details();" >
                        </td>
                    </tr>  
                </table>
			</fieldset>
        </form>	
     
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
if($('#cbo_company_id').val()!=0)
{
	$("#cbo_dyeing_source").val('1');
	load_drop_down( 'requires/woven_finish_fabric_issue_roll_wise_controller',1+'**'+$('#cbo_company_id').val(),'load_drop_down_knitting_com','dyeing_company_td' );
}
</script>
</html>
