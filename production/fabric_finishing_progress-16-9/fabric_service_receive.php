<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create 
				
Functionality	:	
JS Functions	:
Created by		:	Ashraful
Creation date 	: 	20-3-2016
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
echo load_html_head_contents("Grey Fabric Issue Roll Wise","../../", 1, 1, $unicode,'',''); 
$grey_issue_basis=array(1=>"Booking",2=>"Independent",3=>"Knitting Plan");

?>	
<script>
	
	
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	var scanned_booking_arr=new Array();
	var scanned_barcode=new Array();
	var scanned_batch_arr=new Array();

 	<? 
	$scanned_barcode_array=array(); $barcode_dtlsId_array=array(); $barcode_rollTableId_array=array();
	//$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
	//$company_name_array=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	//$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	//$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	 
	
	/*
	$data_array=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst");
	$po_details_array=array();
	foreach($data_array as $row)
	{
		$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
		$po_details_array[$row[csf("po_id")]]['buyer_name']=$buyer_name_array[$row[csf("buyer_name")]];
		$po_details_array[$row[csf("po_id")]]['buyer_id']=$row[csf("buyer_name")];
		$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
		$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
	}
	
	$jspo_details_array= json_encode($po_details_array);
	echo "var po_details_array = ". $jspo_details_array . ";\n";
	
	$composition_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}
	
	$prod_array=array();
	$prodData=sql_select("select id, item_description, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id=13");
	foreach($prodData as $row)
	{
		$prod_array[$row[csf('id')]]['desc']=$row[csf('product_name_details')];
		$prod_array[$row[csf('id')]]['comp']=$row[csf('item_description')];
		$prod_array[$row[csf('id')]]['dt_id']=$row[csf('detarmination_id')];
		$prod_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
		$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
	}
	
	$jsprod_array=json_encode($prod_array);
	echo "var prod_array = ". $jsprod_array . ";\n";
	
	$batch_details_array=array();
	$batch_details_sql=sql_select("select prod_id,po_id, sum(batch_qnty) as qnty,width_dia_type,mst_id,body_part_id, 0 as febric_description_id, null as fin_dia, null as fin_gsm, 1 as type 
	from pro_batch_create_dtls where status_active=1 and is_deleted=0  group by prod_id, width_dia_type, mst_id, po_id, body_part_id
	union all
	select b.prod_id, b.order_id as po_id, b.batch_issue_qty as qnty, 0 as width_dia_type, b.id as mst_id, b.body_part_id as body_part_id, febric_description_id, fin_dia, fin_gsm, 2 as type 
	from inv_receive_mas_batchroll a, pro_grey_batch_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=92 and a.dyeing_source=3");
	foreach($batch_details_sql as $val)
	{
		if($val[csf("type")]==2)
		{
			
			
			$batch_out_details_array[$val[csf('mst_id')]]['width_dia_type_id']=$val[csf('width_dia_type')];
			$batch_out_details_array[$val[csf('mst_id')]]['batch_qnty']+=$val[csf('qnty')];
			$batch_out_details_array[$val[csf('mst_id')]]['determination_id']=$val[csf('febric_description_id')];
			$batch_out_details_array[$val[csf('mst_id')]]['comps']=$composition_arr[$val[csf('febric_description_id')]];
			$batch_out_details_array[$val[csf('mst_id')]]['fin_dia']=$val[csf('fin_dia')];
			$batch_out_details_array[$val[csf('mst_id')]]['fin_gsm']=$val[csf('fin_gsm')];
			$batch_out_details_array[$val[csf('mst_id')]]['po_id']=$val[csf('po_id')];
			$batch_out_details_array[$val[csf('mst_id')]]['body_part_id']=$val[csf('body_part_id')];
			$batch_out_details_array[$val[csf('mst_id')]]['body_part']=$body_part[$val[csf('body_part_id')]];
			
			$batch_out_details_array[$val[csf('mst_id')]]['gsm']=$gsm;
			$batch_out_details_array[$val[csf('mst_id')]]['dia']=$dia;
			$batch_out_details_array[$val[csf('mst_id')]]['width_dia_type']=$fabric_typee[$val[csf('width_dia_type')]];
		}
		else
		{
			$determination_id=$prod_array[$val[csf('prod_id')]]['dt_id'];
			$gsm=$prod_array[$val[csf('prod_id')]]['gsm'];
			$dia=$prod_array[$val[csf('prod_id')]]['dia'];
			
			if($determination_id==0 || $determination_id=="")
			{
				$comps=$prod_array[$val[csf('prod_id')]]['comp'];
			}
			else
			{
				$comps=$composition_arr[$determination_id];
			}
			
			$batch_details_array[$val[csf('mst_id')]][$val[csf('prod_id')]]['width_dia_type_id']=$val[csf('width_dia_type')];
			$batch_details_array[$val[csf('mst_id')]][$val[csf('prod_id')]]['batch_qnty']+=$val[csf('qnty')];
			$batch_details_array[$val[csf('mst_id')]][$val[csf('prod_id')]]['determination_id']=$determination_id;
			$batch_details_array[$val[csf('mst_id')]][$val[csf('prod_id')]]['po_id']=$val[csf('po_id')];
			$batch_details_array[$val[csf('mst_id')]][$val[csf('prod_id')]]['body_part_id']=$val[csf('body_part_id')];
			$batch_details_array[$val[csf('mst_id')]][$val[csf('prod_id')]]['body_part']=$body_part[$val[csf('body_part_id')]];
			
			$batch_details_array[$val[csf('mst_id')]][$val[csf('prod_id')]]['gsm']=$gsm;
			$batch_details_array[$val[csf('mst_id')]][$val[csf('prod_id')]]['dia']=$dia;
			$batch_details_array[$val[csf('mst_id')]][$val[csf('prod_id')]]['comps']=$comps;
			$batch_details_array[$val[csf('mst_id')]][$val[csf('prod_id')]]['width_dia_type']=$fabric_typee[$val[csf('width_dia_type')]];
		}
		
		//body_part
	}
	
	$jsbatch_out_details_array=json_encode($batch_out_details_array);
	echo "var batch_out_details_array = ". $jsbatch_out_details_array . ";\n";
	$jsbatch_details_array=json_encode($batch_details_array);
	echo "var batch_details_array = ". $jsbatch_details_array . ";\n";
	$jscolor_arr=json_encode($color_arr);
	echo "var color_arr = ". $jscolor_arr . ";\n";
	*/

	?>
	


		
	function openmypage_receive()
	{ 
		var cbo_company_id = $('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/fabric_service_receive_controller.php?cbo_company_id='+cbo_company_id+'&action=receive_popup','Recv. Popup', 'width=780px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var receive_id=this.contentDoc.getElementById("hidden_system_id").value;	 //challan Id and Number
			var order_type=this.contentDoc.getElementById("hidden_order_type").value;
			
			if(receive_id!="")
			{
				fnc_reset_form();
				get_php_form_data(receive_id, "populate_data_from_data", "requires/fabric_service_receive_controller");
				show_list_view(receive_id+"_"+cbo_company_id+"_"+order_type, 'grey_item_details_update', 'scanning_tbody','requires/fabric_service_receive_controller', '' );	
				set_button_status(1, permission, 'fnc_grey_roll_receive_from_subcon',1);
			}
		}
	}
	
	

	/*function openmypage_batchNo()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_service_source = $('#cbo_service_source').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			var page_link='requires/fabric_service_receive_controller.php?cbo_company_id='+cbo_company_id+'&cbo_service_source='+cbo_service_source+'&action=batch_number_popup';
			var title='Batch Number Popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=390px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;
				var batch_no=this.contentDoc.getElementById("hidden_batch_no").value; //Access form field with id="emailfield" hidden_service_source
				var color_id=this.contentDoc.getElementById("hidden_color_id").value;
				var booking_type=this.contentDoc.getElementById("hidden_booking_withorder").value;
				var booking_type=this.contentDoc.getElementById("hidden_booking_withorder").value;
				var service_source=this.contentDoc.getElementById("hidden_service_source").value;

				if(batch_id!="")
				{
					//$('#txt_batch_no').val(batch_no);
					//$('#txt_batch_id').val(batch_id);
					
					if( jQuery.inArray( batch_id, scanned_batch_arr )>-1) 
					{ 
						alert('Sorry! Batch Already Scanned.'); 
						$('#txt_batch_no').val('');
						$('#txt_batch_id').val('');
						return; 
					}
					
					scanned_batch_arr.push(batch_id); 
					create_row(0,batch_id,batch_no,color_id,booking_type,service_source); 
				}
			}
		}
	}

	function create_row(is_update,batch_id,batch_no,color_id,booking_type,service_source)
	{

		var batch_id=trim(batch_id);
		var row_num =$('#scanning_tbl tbody tr').length; 
		var cbo_company_id = $('#cbo_company_id').val();
		
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		if(service_source==2)
		{
			var last_tr_batch_id=$('#batchId_'+row_num).val();
			if(last_tr_batch_id!="")
			{
				row_num++;
				
				$("#scanning_tbl tbody tr:first").clone().find("td,input,select").each(function()
				{
					$(this).attr({ 
					  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
					  'value': function(_, value) { return value }              
					});
				}).end().appendTo("#scanning_tbl");
				$("#scanning_tbl tbody tr:last").removeAttr('id').attr('id','tr_'+row_num);
			}
			 
			$("#cboProcess_"+row_num).val(0);

			$("#productId_"+row_num).val("");
			$("#orderId_"+row_num).val("");
			$("#colorId_"+row_num).val("");
			$("#dtlsId_"+row_num).val('');
			$("#batchId_"+row_num).val('');
			$("#inHouseBatchNo_"+row_num).val('');
			$("#bodyPart_"+row_num).val(''); 
			$("#buyerId_"+row_num).val('');
			$("#determinationId_"+row_num).val('');
			$("#widthTypeId_"+row_num).val('');
			$("#txtIssueQty_"+row_num).val("");
			$("#txtRollNo_"+row_num).val("");
			$("#finDia_"+row_num).val("");
			$("#finGsm_"+row_num).val("");
			
			$("#sl_"+row_num).text(row_num);
			$("#batchNo_"+row_num).text(batch_no);
			$("#diaType_"+row_num).text(batch_out_details_array[batch_id]['width_dia_type']);
			$("#prodId_"+row_num).text(0);
			$("#batchWeight_"+row_num).text(batch_out_details_array[batch_id]['batch_qnty']);
			$("#bodyPart_"+row_num).text(batch_out_details_array[batch_id]['body_part']);
			
			
			$("#gsm_"+row_num).text(batch_out_details_array[batch_id]['fin_gsm']);
			$("#dia_"+row_num).text(batch_out_details_array[batch_id]['fin_dia']);
			$("#cons_"+row_num).text(batch_out_details_array[batch_id]['comps']);
			
			$("#color_"+row_num).text(color_arr[color_id]);
			
			$("#buyer_"+row_num).text(po_details_array[batch_out_details_array[batch_id]['po_id']]['buyer_name']);
			$("#job_"+row_num).text(po_details_array[batch_out_details_array[batch_id]['po_id']]['job_no']);
			$("#order_"+row_num).text(po_details_array[batch_out_details_array[batch_id]['po_id']]['po_number']);
			
			$("#orderId_"+row_num).val(batch_out_details_array[batch_id]['po_id']);
			$("#widthTypeId_"+row_num).val(batch_out_details_array[batch_id]['width_dia_type_id']);
			$("#batchWgt_"+row_num).val(batch_out_details_array[batch_id]['batch_qnty']);
			
			$("#colorId_"+row_num).val(color_id);
			$("#buyerId_"+row_num).val(po_details_array[batch_out_details_array[batch_id]['po_id']]['buyer_id']);
			$("#determinationId_"+row_num).val(batch_out_details_array[batch_id]['determination_id']);
			$("#finDia_"+row_num).val(batch_out_details_array[batch_id]['fin_dia']);
			$("#finGsm_"+row_num).val(batch_out_details_array[batch_id]['fin_gsm']);
			$("#bodypartId_"+row_num).val(batch_out_details_array[batch_id]['body_part_id']);
			$("#productId_"+row_num).val(prod_id);
			$("#batchId_"+row_num).val(batch_id);
			$('#increase_'+row_num).removeAttr("onclick").attr("onclick","fn_add_row("+row_num+");");
			$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fnc_remove_row("+row_num+");");
		}
		else
		{ 
			 
			for(var prod_id in batch_details_array[batch_id])
			{
				//alert(prod_id);

				var last_tr_batch_id=$('#batchId_'+row_num).val();
				if(last_tr_batch_id!="")
				{
					row_num++;
					
					$("#scanning_tbl tbody tr:first").clone().find("td,input,select").each(function()
					{
						$(this).attr({ 
						  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
						  'value': function(_, value) { return value }              
						});
					}).end().appendTo("#scanning_tbl");
					$("#scanning_tbl tbody tr:last").removeAttr('id').attr('id','tr_'+row_num);
				}

				 
				$("#cboProcess_"+row_num).val(0);
				$("#productId_"+row_num).val("");//not found
				$("#orderId_"+row_num).val("");
				$("#colorId_"+row_num).val("");
				$("#dtlsId_"+row_num).val('');
				$("#batchId_"+row_num).val('');
				$("#bodypartId_"+row_num).val('');
				$("#buyerId_"+row_num).val('');
				$("#determinationId_"+row_num).val('');
				$("#widthTypeId_"+row_num).val('');
				$("#txtIssueQty_"+row_num).val("");
				$("#txtRollNo_"+row_num).val("");
				
				$("#sl_"+row_num).text(row_num);
				$("#batchNo_"+row_num).text(batch_no);
				$("#diaType_"+row_num).text(batch_details_array[batch_id][prod_id]['width_dia_type']);
				$("#prodId_"+row_num).text(prod_id);
				$("#batchWeight_"+row_num).text(batch_details_array[batch_id][prod_id]['batch_qnty']);
				$("#bodyPart_"+row_num).text(batch_details_array[batch_id][prod_id]['body_part']);
				
				
				$("#gsm_"+row_num).text(batch_details_array[batch_id][prod_id]['gsm']);
				$("#dia_"+row_num).text(batch_details_array[batch_id][prod_id]['dia']);
				$("#cons_"+row_num).text(batch_details_array[batch_id][prod_id]['comps']);
				$("#color_"+row_num).text(color_arr[color_id]);
				
				$("#buyer_"+row_num).text(po_details_array[batch_details_array[batch_id][prod_id]['po_id']]['buyer_name']);
				$("#job_"+row_num).text(po_details_array[batch_details_array[batch_id][prod_id]['po_id']]['job_no']);
				$("#order_"+row_num).text(po_details_array[batch_details_array[batch_id][prod_id]['po_id']]['po_number']);
				
				$("#orderId_"+row_num).val(batch_details_array[batch_id][prod_id]['po_id']);
				$("#widthTypeId_"+row_num).val(batch_details_array[batch_id][prod_id]['width_dia_type_id']);
				$("#batchWgt_"+row_num).val(batch_details_array[batch_id][prod_id]['batch_qnty']);
				
				$("#colorId_"+row_num).val(color_id);
				$("#buyerId_"+row_num).val(po_details_array[batch_details_array[batch_id][prod_id]['po_id']]['buyer_id']);
				$("#determinationId_"+row_num).val(batch_details_array[batch_id][prod_id]['determination_id']);
				$("#bodypartId_"+row_num).val(batch_details_array[batch_id][prod_id]['body_part_id']);
				$("#productId_"+row_num).val(prod_id);
				$("#batchId_"+row_num).val(batch_id);
				$('#increase_'+row_num).removeAttr("onclick").attr("onclick","fn_add_row("+row_num+");");
				$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fnc_remove_row("+row_num+");");
				$("#scanning_tbl tbody tr:first").remove();

				//alert(prod_id);
			}
		}
		
	}*/



	
	function openmypage_booking()
	{
		var company_id = $('#cbo_company_id').val();
		var cbo_service_source = $('#cbo_service_source').val();
		var supplier_id = $('#cbo_service_company').val();
		if (form_validation('cbo_company_id*cbo_service_company','Company*Service Company')==false)
		{
			return;
		}
		
		//bookingNo_1
		var dataString="";
				$("#scanning_tbl").find('tbody tr').each(function()
				{
					
					var booking_no  = $(this).children('td').eq(15).html();
					dataString+= booking_no+"_";
				});

			//alert(dataString);//return;

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/fabric_service_receive_controller.php?company_id='+company_id+'&supplier_id='+supplier_id+'&cbo_service_source='+cbo_service_source+'&prebookingNos='+dataString+'&action=service_booking_popup', 'Service Booking Search', 'width=1150px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_booking");
			
			if (theemail.value!="")
			{
				var booking_data=(theemail.value).split("_");
				$("#txt_woorder_no").val(booking_data[1]);
				$("#txt_woorder_id").val(booking_data[0]);
				if( jQuery.inArray( booking_data[1], scanned_booking_arr )>-1) 
				{ 
					alert('Sorry! Booking Already Generated.'); 
					$('#txt_woorder_no').val('');
					$('#txt_woorder_id').val('');
					return; 
				}
				
				if($("#cboProcess_1").val()==0)
				{
					var total_row=0;	
				}
				else
				{
					var total_row=$("#scanning_tbl tbody tr").length;
				}
				$('#cbo_service_company').attr('disabled',"disabled");
				
				var ddd=return_global_ajax_value(booking_data[1]+'**'+booking_data[2]+'**'+booking_data[0]+'**'+total_row+'**'+booking_data[3], "fabric_detls_list_view", "requires/", "fabric_service_receive_controller");
				
				if($("#cboProcess_1").val()==0)
				{
					$("#scanning_tbody").text('');	
				}
				
				if( ddd!="") $("#scanning_tbody").prepend(ddd);
				scanned_booking_arr.push(booking_data[1]);
				
			}
		}
	}

	function generate_report_file(data,action)
	{
		window.open("requires/fabric_service_receive_controller.php?data=" + data+'&action='+action, true );
	}
	
	function fnc_grey_roll_receive_from_subcon( operation )
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		if(operation==4)
		{
			
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title,'fabric_receive_print','requires/aop_roll_receive_entry_controller');
			return;
		}
		
	 	if(form_validation('cbo_company_id*cbo_service_source*cbo_service_company*txt_receive_date','Company*Service Source*Service Company*Receive Date')==false)
		{
			return; 
		}
		
		var j=0; var dataString=''; var error=0;var privCurrentQty=batchQty=0; 
		$("#scanning_tbl").find('tbody tr').each(function()
		{
				
				var prifix=$(this).attr("id").split("_");
				var id=prifix[1];
				var txtReceiveQty=$(this).find('input[name="txtReceiveQty[]"]').val()*1;
				if(txtReceiveQty>0)
				{
					var txtBatchNo=$(this).find('input[name="txtBatchNo[]"]').val();
					var txtInhouseBatchNo=$(this).find('input[name="txtinHouseBatchNo[]"]').val();
					var cboProcess=$(this).find('select[name="cboProcess[]"]').val();
					var colorId=$(this).find('input[name="colorId[]"]').val();
					var bodypartId=$(this).find('input[name="bodypartId[]"]').val();
					var determinationId=$(this).find('input[name="determinationId[]"]').val();
					var buyerId=$(this).find('input[name="buyerId[]"]').val();
					var orderId=$(this).find('input[name="orderId[]"]').val();
					var txtRate=$(this).find('input[name="txtRate[]"]').val();
					var txtAmount=$(this).find('input[name="txtAmount[]"]').val();
					var greyUsed=$(this).find('input[name="txtgreyUsed[]"]').val();
					var currencyId=$(this).find('input[name="currencyId[]"]').val();
					var exchangeRate=$(this).find('input[name="exchangeRate[]"]').val();
					var bookingId=$(this).find('input[name="progBookPiId[]"]').val();
					var bookingNo=$(this).find('input[name="txtBookingNo[]"]').val();
					var finDia=$(this).find('input[name="finDia[]"]').val();
					var finGsm=$(this).find('input[name="finGsm[]"]').val();
					var bookWithoutOrder=$(this).find('input[name="bookWithoutOrder[]"]').val();
					var finDia=$(this).find('input[name="finDia[]"]').val();
					var finGsm=$(this).find('input[name="finGsm[]"]').val();
					
					var woorder_qty=$(this).find("td:eq(9)").text();
					var jobNo=$(this).find('input[name="hiddnJobNo[]"]').val();
					var dia=$(this).find("td:eq(5)").text();
					var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
					var bookingDtlsId=$(this).find('input[name="bookingDtlsId[]"]').val();
					if(operation==0)
					{
						var privCurrentQty=$(this).find('input[name="privCurrentQty[]"]').val()*1;
						var batchQty=$(this).find('input[name="batchtQty[]"]').val()*1;
						//$prev_recv_balance_qty<= $po_batch_qty) 
						if(privCurrentQty>=batchQty || batchQty==0)
						{
							//alert('G');
							//alert('Recv Qty is over then Batch Qty.\n'+'RecvQty:'+privCurrentQty+'\n BatchQty:'+batchQty);
							//error=1;
							//return; 
						}
					}

					j++;
					dataString+='&cboProcess_' + j + '=' + cboProcess + '&determinationId_' + j + '=' + determinationId + '&buyerId_' + j + '=' + buyerId + '&txtRate_' + j + '=' + txtRate + '&orderId_' + j + '=' + orderId + '&txtBatchNo_' + j + '=' + txtBatchNo+ '&txtinHouseBatchNo_' + j + '=' + txtInhouseBatchNo + '&jobNo_' + j + '=' + jobNo + '&colorId_' + j + '=' + colorId + '&dtlsId_' + j + '=' + dtlsId + '&txtAmount_' + j + '=' + txtAmount + '&dia_' + j + '=' + dia + '&bodypartId_' + j + '=' + bodypartId + '&txtReceiveQty_' + j + '=' + txtReceiveQty+ '&tr_' + j + '=' + id+ '&currencyId_' + j + '=' + currencyId+ '&exchangeRate_' + j + '=' + exchangeRate+ '&bookingId_' + j + '=' + bookingId+ '&bookingNo_' + j + '=' + bookingNo+ '&workorderNo_' + j + '=' + woorder_qty+ '&finDia_' + j + '=' + finDia+ '&finGsm_' + j + '=' + finGsm+ '&bookWithoutOrder_' + j + '=' + bookWithoutOrder+ '&greyUsed_' + j + '=' + greyUsed+ '&bookingDtlsId_' + j + '=' + bookingDtlsId;
				}
		});
		
		//alert(dataString);
		if(error==1)
		{
			return;
		}
		if(j<1)
		{
			alert('No data');
			return;
		}
		
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_receive_no*cbo_company_id*cbo_service_source*cbo_service_company*txt_receive_date*txt_receive_challan*update_id',"../../")+dataString;
		//alert(data)
		freeze_window(operation);
		http.open("POST","requires/fabric_service_receive_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_grey_roll_receive_from_subcon_Reply_info;
	}

	function fnc_grey_roll_receive_from_subcon_Reply_info()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);
			
			if((response[0]==0 || response[0]==1))
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_receive_no').value = response[2];
				add_dtls_data(response[3]);
				show_list_view($('#update_id').val()+"_"+$('#cbo_company_id').val()+"_"+"", 'grey_item_details_update', 'scanning_tbody','requires/fabric_service_receive_controller', '' );
				set_button_status(1, permission, 'fnc_grey_roll_receive_from_subcon',1);
			}
			release_freezing();
		}
	}
	


	function add_dtls_data( data )
	{
		var batch_datas=data.split(",");
		for(var k=0; k<batch_datas.length; k++)
		{
			var datas=batch_datas[k].split("__");
			var tr_no=datas[0];
			var dtls_id=datas[1];
			$("#dtlsId_"+tr_no).val(dtls_id);
		}
		
	}
	
	
	
	$('#txt_woorder_no').live('keydown', function(e) {
		if (e.keyCode === 13) 
		{
			e.preventDefault();
			var booking_no=$('#txt_woorder_no').val();
			check_woorder(booking_no);
		}
	});
	
	
	function check_woorder(data)
	{
		if(data=="")
		{
			$('#txt_woorder_no').val('');
			return;	
		}
		var cbo_company_id=$('#cbo_company_id').val();
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		
		var booking_data=return_global_ajax_value( data+"**"+cbo_company_id, 'check_booking_no', '', 'requires/fabric_service_receive_controller');
		
		if(booking_data==0)
		{
			alert("Booking No Found");
			$('#txt_woorder_no').val('');
			$('#txt_woorder_id').val('');
			return;
		}
		else
		{
			booking_data=booking_data.split("**");
			booking_id=booking_data[0];
			if( jQuery.inArray( booking_id, scanned_booking_arr )>-1) 
			{ 
				alert('Sorry! Booking Already Scanned.'); 
				$('#txt_woorder_no').val('');
				$('#txt_woorder_id').val('');
				return; 
			}
			
			$("#txt_woorder_no").val(booking_data[1]);
			$("#txt_woorder_id").val(booking_data[0]);
			
			
			if($("#cboProcess_1").val()==0)
			{
				var total_row=0;	
			}
			else
			{
				var total_row=$("#scanning_tbl tbody tr").length;
			}
			
			var ddd=return_global_ajax_value(booking_data[1]+'**'+booking_data[2]+'**'+booking_data[0]+'**'+total_row, "fabric_detls_list_view", "requires/", "fabric_service_receive_controller");
			
			if($("#cboProcess_1").val()==0)
			{
				$("#scanning_tbody").text('');	
			}
			
			if( ddd!="") $("#scanning_tbody").prepend(ddd);
			scanned_booking_arr.push(booking_id);
		}
	}
	
	function fnc_reset_form()
	{
		$('#scanning_tbl tbody tr').remove();
		
		var html='<tr id="tr_1" align="center" valign="middle"><td width="25" id="sl_1"></td><td width="70" id="batchNo_1"><input type="text" id="txtBatchNo_1" name="txtBatchNo[]"  style=" width:60px" class="text_boxes"/></td><td width="70" id="inHouseBatchNo_1" style="display:none"><input type="text" id="txtinHouseBatchNo_1" name="txtinHouseBatchNo[]"  style=" width:60px" class="text_boxes"/></td><td style="word-break:break-all;" width="80" id="bodyPart_1"></td><td style="word-break:break-all;" width="120" id="cons_1" align="left"></td><td style="word-break:break-all;" width="50" id="dia_1"></td><td style="word-break:break-all;" width="50" id="gsm_1"></td><td style="word-break:break-all;" width="70" id="color_1"></td><td width="120" align="right" id=""><? echo create_drop_down( "cboProcess_1", 120, $conversion_cost_head_array,"", 1, "-- Select Process --", "", "","","","","","","","","cboProcess[]" );?></td><td style="word-break:break-all;" width="60" id="woQty_1"></td><td width="60" align="center" id=""><input type="text" id="totalreceiveQty_1" name="totalreceiveQty[]"  style=" width:40px" class="text_boxes_numeric" readonly/></td><td width="60" align="center" id=""><input type="text" id="txtReceiveQty_1" name="txtReceiveQty[]"  style=" width:40px" class="text_boxes_numeric"/></td><td width="60" align="center" id=""><input type="text" id="txtRate_1" name="txtRate[]"  style=" width:40px" class="text_boxes_numeric" readonly/></td><td width="70" align="center" id=""><input type="text" id="txtAmount_1" name="txtAmount[]"  style=" width:55px" class="text_boxes_numeric"/></td><td style="word-break:break-all;" width="90" id="bookingNo_1" align="left"></td><td style="word-break:break-all;" width="60" id="buyer_1"></td><td style="word-break:break-all;" width="80" id="job_1"></td><td style="word-break:break-all;" width="100" id="order_1" align="left"></td><td style="word-break:break-all;" width="" id="currency_1" align="left"><? echo create_drop_down( "currencyId_1", 65, $currency,"", 1, "Select", "", "","","","","","","","","currencyId[]" ); ?><input type="hidden" name="hiddnJobNo[]" id="hiddnJobNo_<? echo $i; ?>" value=""/><input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/><input type="hidden" name="orderId[]" id="orderId_1"/><input type="hidden" name="colorId[]" id="colorId_1"/><input type="hidden" name="dtlsId[]" id="dtlsId_1"/><input type="hidden" name="bodypartId[]" id="bodypartId_1"/><input type="hidden" name="buyerId[]" id="buyerId_1"/><input type="hidden" name="determinationId[]" id="determinationId_1"/><input type="hidden" name="currencyId[]" id="currencyId_1"/><input type="hidden" name="exchangeRate[]" id="exchangeRate_1"/><input type="hidden" name="txtBookingNo[]" id="txtBookingNo_1"/><input type="hidden" name="finDia[]" id="finDia_1"/><input type="hidden" name="finGsm[]" id="finGsm_1"/></td></tr>';
		
		$('#cbo_company_id').val(0);
		$('#cbo_company_id').attr('disabled',false);
		$('#cbo_service_source').val(0);
		$('#cbo_service_company').val(0);
		$('#update_id').val('');
		$('#txt_receive_no').val('');
		$('#txt_receive_challan').val('');
		$('#txt_receive_date').val('');
		$('#txt_woorder_no').val('');
		$('#txt_woorder_id').val('');
		$("#scanning_tbl tbody").html(html);	
	}
	function check_variable_setting(){
		var cbo_company_id=$('#cbo_company_id').val();
		var booking_data=return_global_ajax_value( cbo_company_id, 'check_variable_setting', '', 'requires/fabric_service_receive_controller');
		if(booking_data!=0)
		{
			var variableData=booking_data.split("**");
			$('#txt_variable_checked').val(variableData[0]);
			$('#txt_variable_percent').val(variableData[1]);
			return;
		}
	}

	function calculate_amount(id)
	{	

		var issue_qnty = $("#txtReceiveQty_"+id).attr("placeholder")*1;
		var tot_rcv = $("#totalreceiveQty_"+id).val()*1;
		var curr_rcv = $("#txtReceiveQty_"+id).val()*1;
		var hidden_curr_rcv = $("#txtReceiveQtyHidden_"+id).val()*1;
		var txt_variable_checked= $('#txt_variable_checked').val()*1;
		var txt_variable_percent= $('#txt_variable_percent').val()*1;

		if(hidden_curr_rcv != 0)
		{
			var totRcv = (tot_rcv + curr_rcv ) - hidden_curr_rcv ;
		}else
		{
			var totRcv =tot_rcv + curr_rcv;
		}
		
		if($("#cboProcess_"+id).val()*1!==35)
		{
			if(totRcv > issue_qnty)
			{
				$("#txtReceiveQty_"+id).val($("#txtReceiveQtyHidden_"+id).val()*1);
			}
		}
		else if($("#cboProcess_"+id).val()*1==35)
		{
			if(txt_variable_checked==1)
			{
				var issue_qnty_perc=(issue_qnty*txt_variable_percent)/100;
				if(totRcv > issue_qnty+issue_qnty_perc)
				{
					$("#txtReceiveQty_"+id).val($("#txtReceiveQtyHidden_"+id).val()*1);
				}
			}
			else
			{
				if(totRcv > issue_qnty)
				{
					$("#txtReceiveQty_"+id).val($("#txtReceiveQtyHidden_"+id).val()*1);
				}
			}
			
		}
		
		var amount=($("#txtReceiveQty_"+id).val()*1)*($("#txtRate_"+id).val()*1);
		$("#txtAmount_"+id).val(number_format(amount,4,".",""));
	}
	function fnc_batch_popup(i,batchString)
	{	
		alert(batchString);
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
    <div align="center" style="width:100%;">
		<? echo load_freeze_divs ("../../",$permission); ?>
        <form name="rollscanning_1" id="rollscanning_1"  autocomplete="off"  >
            <fieldset style="width:810px;">
				<legend>Roll Scanning</legend>
                <table cellpadding="0" cellspacing="2" width="800">
                    <tr>
                        <td colspan="3" align="right"><b>Service Receive Number&nbsp;</b>
                        </td>
                         <td colspan="3" align="left">
                        	<input type="text" name="txt_receive_no" id="txt_receive_no" class="text_boxes" style="width:140px;" onDblClick="openmypage_receive()" placeholder="Double Click To Search" readonly/>
                            <input type="hidden" name="update_id" id="update_id"/>
                            <input type="hidden" name="txt_variable_checked" id="txt_variable_checked"/>
                            <input type="hidden" name="txt_variable_percent" id="txt_variable_percent"/>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" class="must_entry_caption">Company</td>
                        <td>
                            <? 
                                echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "check_variable_setting();",0 );

                                //load_drop_down( 'requires/fabric_service_receive_controller',this.value+'**'+$('#cbo_company_id').val(),'load_drop_down_knitting_com','dyeing_company_td' );
                            ?>
                        </td>
                        <td class="must_entry_caption" align="right">Service Source</td>
                        <td>
							<?
                                echo create_drop_down( "cbo_service_source", 142, $knitting_source, "", 1, "-- Select --", 0, "load_drop_down( 'requires/fabric_service_receive_controller',this.value+'**'+$('#cbo_company_id').val(),'load_drop_down_knitting_com','dyeing_company_td' );","","1,3" );
                            ?>
                        </td>
                        <td align="right" class="must_entry_caption">Service Company</td>
                        <td id="dyeing_company_td">
                            <?
                                echo create_drop_down( "cbo_service_company", 152, $blank_array,"", 1, "-- Select --", $selected, "","","" );
                            ?>
                        </td>
                    </tr>
                    <tr>
                    	<td align="right" class="must_entry_caption" width="100">Receive Date</td>
                        <td><input type="text" name="txt_receive_date" id="txt_receive_date" class="datepicker" style="width:140px;" readonly /></td>
                        <td align="right">Work Order No</td>
                        <td>
                        	<input type="text" name="txt_woorder_no" id="txt_woorder_no" class="text_boxes" style="width:130px;" placeholder="Browse/scan" onDblClick="openmypage_booking();"  />
                            <input type="hidden" id="txt_woorder_id" />
                        </td>
                        <td align="right"  width="">Receive Challan No</td>
                        <td><input type="text" name="txt_receive_challan" id="txt_receive_challan" class="text_boxes" style="width:140px;" />
                        </td>
                        <!-- <td align="right">Batch No</td>
                        <td>
                        	<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:140px;" placeholder="Browse/Write/scan" onDblClick="openmypage_batchNo();" onChange="check_batch(this.value);" />
                            <input type="hidden" id="txt_batch_id" />
                            
                        </td> -->
                    </tr>
                    <!-- <tr>
                    						<td>&nbsp;</td>
                    						<td align="right" colspan="2" autofocus="autofocus">Batch No</td>
                        <td>
                        	<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:130px;" placeholder="Browse/Write/scan" onDblClick="openmypage_batchNo();" onChange="check_batch(this.value);" />
                            <input type="hidden" id="txt_batch_id" />
                            
                        </td>
                    						<td>&nbsp;</td>
                    </tr> -->
                </table>
			</fieldset>
			<br>
			<fieldset style="width:1430px;text-align:left">
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
				<table cellpadding="0" width="1410" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="25">SL</th>
                        <th width="70">Subcon Batch</th>
                        <th width="100" style="display:none">Inhouse Batch</th>
                        <th width="80">Body Part</th>
                        <th width="120">Construction/ Composition</th>
                        <th width="50">Fin. Dia</th>
                        <th width="50">Fin. gsm</th>
                        <th width="70">Color</th>
                        <th width="120">Process</th>
                        <th width="60">WO. Qnty</th>
                        <th width="60">Total Rev. Qty.</th>
                        <th width="60">Current Rev. Qty</th>
                        <th width="60">Rate</th>
                        <th width="70">Amount</th>
                        <th width="70">Grey Used</th>
                        <th width="90">Booking No</th>
                        <th width="60">Buyer</th>
                        <th width="80">Job No</th>
                        <th width="100">Order No</th>
                        <th width="">Currency</th>
                    </thead>
                 </table>
                 <div style="width:1430px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="1410" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody id="scanning_tbody">
                        	<tr id="tr_1" align="center" valign="middle">
                                <td width="25" id="sl_1"></td>
                                <td width="70" id="batchNo_1"  ><input type="text" id="txtBatchNo_1" name="txtBatchNo[]"  style=" width:60px" class="text_boxes"/></td>
                                <td width="100" id="inHouseBatchNo_1" style="display:none"><input type="text" id="txtinHouseBatchNo_1" name="txtinHouseBatchNo[]"  style=" width:90px" class="text_boxes"/></td>
                                <td style="word-break:break-all;" width="80" id="bodyPart_1"></td>
                                <td style="word-break:break-all;" width="120" id="cons_1" align="left"></td>
                                <td style="word-break:break-all;" width="50" id="dia_1"></td>
                                <td style="word-break:break-all;" width="50" id="gsm_1"></td>
                                <td style="word-break:break-all;" width="70" id="color_1"></td>
                                <td width="120" align="right" id="">
                                	<? 
										echo create_drop_down( "cboProcess_1", 120, $conversion_cost_head_array,"", 1, "-- Select Process --", "", "","","","","","","","","cboProcess[]" ); 
									?>
                              	</td>
                                <td style="word-break:break-all;" width="60" id="batchWeight_1"></td>
                             	<td width="60" align="center" id=""><input type="text" id="totalreceiveQty_1" name="totalreceiveQty[]"  style=" width:40px" class="text_boxes_numeric" readonly/></td>
                             	<td width="60" align="center" id="batchQtytd_1"><input type="text" id="txtReceiveQty_1" name="txtReceiveQty[]"  style=" width:40px" class="text_boxes_numeric" placeholder="" /> <input type="hidden" name="txtReceiveQtyHidden_1" name="txtReceiveQtyHidden[]"/></td> 
                                <td width="60" align="center" id=""><input type="text" id="txtRate_1" name="txtRate[]"  style=" width:40px" class="text_boxes_numeric" readonly/></td>
                                <td width="70" align="center" id=""><input type="text" id="txtAmount_1" name="txtAmount[]"  style=" width:55px" class="text_boxes_numeric"/></td>
                                <td width="70" align="center" id=""><input type="text" id="txtgreyUsed_1" name="txtgreyUsed[]"  style=" width:55px" class="text_boxes_numeric"/></td>
                                <td style="word-break:break-all;" width="90" id="bookingNo_1" align="left"></td>
                                <td style="word-break:break-all;" width="60" id="buyer_1"></td>
                                <td style="word-break:break-all;" width="80" id="job_1"></td>
                                <td style="word-break:break-all;" width="100" id="order_1" align="left"></td>
                                <td style="word-break:break-all;" width="" id="currency_1" align="left">
									<? 
											echo create_drop_down( "currencyId_1", 65, $currency,"", 1, "Select", "", "","","","","","","","","currencyId[]" );
									?>
                                	<input type="hidden" name="hiddnJobNo[]" id="hiddnJobNo_1" value=""/>
                                    <input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/>
                                    <input type="hidden" name="orderId[]" id="orderId_1"/>
                                    <input type="hidden" name="colorId[]" id="colorId_1"/>
                                    <input type="hidden" name="dtlsId[]" id="dtlsId_1"/>
                                    <input type="hidden" name="bodypartId[]" id="bodypartId_1"/>
                                    <input type="hidden" name="buyerId[]" id="buyerId_1"/>
                                    <input type="hidden" name="determinationId[]" id="determinationId_1"/>
                                    <input type="hidden" name="exchangeRate[]" id="exchangeRate_1"/>
                                    <input type="hidden" name="txtBookingNo[]" id="txtBookingNo_1"/>
                                    <input type="hidden" name="finDia[]" id="finDia_1"/>
                                    <input type="hidden" name="finGsm[]" id="finGsm_1"/>
                                    <input type="hidden" name="bookingDtlsId[]" id="bookingDtlsId_1"/>
                                    <input type="hidden" name="privCurrentQty[]" id="privCurrentQty_1"/>
                                    <input type="hidden" name="batchtQty[]" id="batchtQty_1"/>
                                </td>
                            </tr>
                        </tbody>
                	</table>
                </div>
                <br>
                <table width="1200" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                    <tr>
                        <td align="center" class="button_container">
                        	<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
                            <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes" value="">
                            <? 
                            	echo load_submit_buttons($permission,"fnc_grey_roll_receive_from_subcon",0,1,"fnc_reset_form()",1);
                            ?>
                        </td>
                    </tr>  
                </table>
			</fieldset>
        </form>	 
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
