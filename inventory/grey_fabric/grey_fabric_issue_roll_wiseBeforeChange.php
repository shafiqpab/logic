<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Grey Fabric Issue Roll Wise
				
Functionality	:	
JS Functions	:
Created by		:	Fuad
Creation date 	: 	18-02-2015
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

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	var scanned_barcode=new Array();
<? 
	$scanned_barcode_array=array(); $barcode_dtlsId_array=array(); $barcode_trnasId_array=array(); $barcode_rollTableId_array=array();
	$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
	$company_name_array=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$machine_array=return_library_array( "select id, machine_no from lib_machine_name where category_id=1", "id", "machine_no");
	$location_array=return_library_array( "select id, location_name from lib_location", "id", "location_name");
	
	$jsbarcode_buyer_name_array= json_encode($buyer_name_array);
	echo "var jsbuyer_name_array = ". $jsbarcode_buyer_name_array . ";\n";
	
	$without_order_buyer=return_library_array( "select c.barcode_no, a.buyer_id from  inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and a.booking_without_order=1 and c.booking_without_order=1 and c.status_active=1 and c.is_deleted=0", "barcode_no", "buyer_id");
	$jsbarcode_without_order_buyer= json_encode($without_order_buyer);
	echo "var without_order_buyer = ". $jsbarcode_without_order_buyer . ";\n";
	
	
	 
	$scanned_barcode_data=sql_select("select a.id, a.barcode_no, a.dtls_id, b.trans_id from pro_roll_details a, inv_grey_fabric_issue_dtls b where a.dtls_id=b.id and a.entry_form=61 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($scanned_barcode_data as $row)
	{
		$scanned_barcode_array[]=$row[csf('barcode_no')];
		$barcode_dtlsId_array[$row[csf('barcode_no')]]=$row[csf('dtls_id')];
		$barcode_trnasId_array[$row[csf('barcode_no')]]=$row[csf('trans_id')];
		$barcode_rollTableId_array[$row[csf('barcode_no')]]=$row[csf('id')];
	}

	$jsscanned_barcode_array= json_encode($scanned_barcode_array);
	echo "scanned_barcode = ". $jsscanned_barcode_array . ";\n";
	
	$jsbarcode_dtlsId_array= json_encode($barcode_dtlsId_array);
	echo "var barcode_dtlsId_array = ". $jsbarcode_dtlsId_array . ";\n";
	
	$jsbarcode_trnasId_array= json_encode($barcode_trnasId_array);
	echo "var barcode_trnasId_array = ". $jsbarcode_trnasId_array . ";\n";
	
	$jsbarcode_rollTableId_array= json_encode($barcode_rollTableId_array); 
	echo "var barcode_rollTableId_array = ". $jsbarcode_rollTableId_array . ";\n";
	
	$data_array=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst");
	$po_details_array=array();
	foreach($data_array as $row)
	{
		$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
		$po_details_array[$row[csf("po_id")]]['buyer_name']=$buyer_name_array[$row[csf("buyer_name")]];
		$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
		$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
	}
	$jspo_details_array= json_encode($po_details_array);
	echo "var po_details_array = ". $jspo_details_array . ";\n";
	
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
	
	$data_array=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.location_id, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, b.color_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.machine_no_id, b.brand_id, b.rack, b.self, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.roll_id as roll_id_prev,c.rate, c.booking_without_order FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0");
	$roll_details_array=array(); $barcode_array=array(); 
	foreach($data_array as $row)
	{
		$roll_details_array[$row[csf("barcode_no")]]['mst_id']=$row[csf("id")];
		$roll_details_array[$row[csf("barcode_no")]]['company_id']=$row[csf("company_id")];
		$roll_details_array[$row[csf("barcode_no")]]['body_part_id']=$body_part[$row[csf("body_part_id")]];
		$roll_details_array[$row[csf("barcode_no")]]['machine_no_id']=$row[csf("machine_no_id")];
		$roll_details_array[$row[csf("barcode_no")]]['machine_no']=$machine_array[$row[csf("machine_no_id")]];
		$roll_details_array[$row[csf("barcode_no")]]['location_id']=$row[csf("location_id")];
		$roll_details_array[$row[csf("barcode_no")]]['location_name']=$location_array[$row[csf("location_id")]];
		
		if(($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==0) || ($row[csf("entry_form")]==22 && ($row[csf("receive_basis")]==4 || $row[csf("receive_basis")]==6)))
		{
			$receive_basis="Independent";
			$receive_basis_id=0;
		}
		else if(($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==1) || ($row[csf("entry_form")]==22 && $row[csf("receive_basis")]==2)) 
		{
			$receive_basis="Booking";
			$receive_basis_id=2;
		}
		else if($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==2) 
		{
			$receive_basis="Knitting Plan";
			$receive_basis_id=3;
		}
		else if($row[csf("entry_form")]==22 && $row[csf("receive_basis")]==1) 
		{
			$receive_basis="PI";
			$receive_basis_id=1;
		}
		else if($row[csf("entry_form")]==58) 
		{
			$receive_basis="Delivery";
			$receive_basis_id=9;
		}
		
		$roll_details_array[$row[csf("barcode_no")]]['receive_basis']=$receive_basis;
		$roll_details_array[$row[csf("barcode_no")]]['receive_basis_id']=$receive_basis_id;
		$roll_details_array[$row[csf("barcode_no")]]['receive_date']=change_date_format($row[csf("receive_date")]);
		$roll_details_array[$row[csf("barcode_no")]]['booking_no']=$row[csf("booking_no")];
		$roll_details_array[$row[csf("barcode_no")]]['booking_id']=$row[csf("booking_id")];
		
		$color='';
		$color_id=explode(",",$row[csf('color_id')]);
		foreach($color_id as $val)
		{
			if($val>0) $color.=$color_arr[$val].",";
		}
		$color=chop($color,',');
		
		$roll_details_array[$row[csf("barcode_no")]]['color']=$color;
		$roll_details_array[$row[csf("barcode_no")]]['color_id']=$row[csf("color_id")];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_source_id']=$row[csf("knitting_source")];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_source']=$knitting_source[$row[csf("knitting_source")]];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_company_id']=$row[csf("knitting_company")];
		$roll_details_array[$row[csf("barcode_no")]]['yarn_lot']=$row[csf("yarn_lot")];
		$roll_details_array[$row[csf("barcode_no")]]['yarn_count']=$row[csf("yarn_count")];
		$roll_details_array[$row[csf("barcode_no")]]['stitch_length']=$row[csf("stitch_length")];
		$roll_details_array[$row[csf("barcode_no")]]['brand_id']=$row[csf("brand_id")];
		$roll_details_array[$row[csf("barcode_no")]]['rack']=$row[csf("rack")];
		$roll_details_array[$row[csf("barcode_no")]]['self']=$row[csf("self")];
		
		if($row[csf("knitting_source")]==1)
		{
			$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$company_name_array[$row[csf("knitting_company")]];
		}
		else if($row[csf("knitting_source")]==3)
		{
			$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$supplier_arr[$row[csf("knitting_company")]];
		}
		
		$roll_details_array[$row[csf("barcode_no")]]['dtls_id']=$row[csf("dtls_id")];
		$roll_details_array[$row[csf("barcode_no")]]['prod_id']=$row[csf("prod_id")];
		$roll_details_array[$row[csf("barcode_no")]]['deter_d']=$row[csf("febric_description_id")];
		$roll_details_array[$row[csf("barcode_no")]]['gsm']=$row[csf("gsm")];
		$roll_details_array[$row[csf("barcode_no")]]['width']=$row[csf("width")];
		
		if($row[csf("entry_form")]==58)
		{
			$roll_details_array[$row[csf("barcode_no")]]['roll_id']=$row[csf("roll_id_prev")];
			$roll_details_array[$row[csf("barcode_no")]]['rate']=$row[csf("rate")];
		}
		else
		{
			$roll_details_array[$row[csf("barcode_no")]]['roll_id']=$row[csf("roll_id")];
		}
		$roll_details_array[$row[csf("barcode_no")]]['roll_no']=$row[csf("roll_no")];
		$roll_details_array[$row[csf("barcode_no")]]['po_breakdown_id']=$row[csf("po_breakdown_id")];
		$roll_details_array[$row[csf("barcode_no")]]['qnty']=number_format($row[csf("qnty")],2,'.','');
		
		$roll_details_array[$row[csf("barcode_no")]]['booking_without_order']=$row[csf("booking_without_order")];
		
		$barcode_array[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
	}
	$jsroll_details_array= json_encode($roll_details_array);
	echo "var roll_details_array = ". $jsroll_details_array . ";\n";
	
	$jsbarcode_array= json_encode($barcode_array);
	echo "var barcode_array = ". $jsbarcode_array . ";\n";
	
?>

	function openmypage_issue()
	{ 
		var cbo_company_id = $('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/grey_fabric_issue_roll_wise_controller.php?cbo_company_id='+cbo_company_id+'&action=issue_popup','Issue Popup', 'width=780px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var issue_id=this.contentDoc.getElementById("hidden_system_id").value;	 //challan Id and Number
			
			if(issue_id!="")
			{
				fnc_reset_form();
				get_php_form_data(issue_id, "populate_data_from_data", "requires/grey_fabric_issue_roll_wise_controller");
					
				var barcode_nos=return_global_ajax_value( issue_id, 'barcode_nos', '', 'requires/grey_fabric_issue_roll_wise_controller');
				if(trim(barcode_nos)!="")
				{
					var barcode_upd=barcode_nos.split(",");
					for(var k=0; k<barcode_upd.length; k++)
					{
						create_row(1,barcode_upd[k]);
					}
				}
				set_button_status(1, permission, 'fnc_grey_fabric_issue_roll_wise',1);
				$("#print_barcode").removeClass('formbutton_disabled');
				$("#print_barcode").addClass('formbutton');
				$("#btn_fabric_details").removeClass('formbutton_disabled');
			    $("#btn_fabric_details").addClass('formbutton');
				$("#btn_mc").removeClass('formbutton_disabled');
			    $("#btn_mc").addClass('formbutton');
				
			}
		}
	}
	
	function openmypage_barcode()
	{ 
		var company_id=$('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/grey_fabric_issue_roll_wise_controller.php?company_id='+company_id+'&action=barcode_popup','Barcode Popup', 'width=980px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var barcode_nos=this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos
			
			if(barcode_nos!="")
			{
				var barcode_upd=barcode_nos.split(",");
				for(var k=0; k<barcode_upd.length; k++)
				{
					create_row(0,barcode_upd[k]);
				}
				set_all_onclick();
			}
		}
	}
	
	function generate_report_file(data,action)
	{
		window.open("requires/grey_fabric_issue_roll_wise_controller.php?data=" + data+'&action='+action, true );
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
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val()+'*'+report_title,'grey_issue_print');
			return;
		}
		
	 	if(form_validation('cbo_company_id*cbo_dyeing_source*cbo_dyeing_company*txt_issue_date*cbo_issue_purpose','Company*Dyeing Source*Dyeing Company*Issue Date*Issue Purpose')==false)
		{
			return; 
		}
		var j=0; var dataString='';
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var recvBasis=$(this).find('input[name="recvBasis[]"]').val();
			var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
			var progBookPiId=$(this).find('input[name="progBookPiId[]"]').val();
			var productId=$(this).find('input[name="productId[]"]').val();
			var orderId=$(this).find('input[name="orderId[]"]').val();
			var rollId=$(this).find('input[name="rollId[]"]').val();
			var rollWgt=$(this).find('input[name="rollWgt[]"]').val();
			var rollNo=$(this).find("td:eq(2)").text();
			
			var yarnLot=$(this).find('input[name="yarnLot[]"]').val();
			var yarnCount=$(this).find('input[name="yarnCount[]"]').val();
			var colorId=$(this).find('input[name="colorId[]"]').val();
			var stichLn=$(this).find('input[name="stichLn[]"]').val();
			var locationId=$(this).find('input[name="locationId[]"]').val();
			var machineId=$(this).find('input[name="machineId[]"]').val();
			var brandId=$(this).find('input[name="brandId[]"]').val();
			var rack=$(this).find('input[name="rack[]"]').val();
			var shelf=$(this).find('input[name="shelf[]"]').val();
			var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
			var transId=$(this).find('input[name="transId[]"]').val();
			var rolltableId=$(this).find('input[name="rolltableId[]"]').val();
			var rollRate=$(this).find('input[name="rollRate[]"]').val();
			var bookWithoutOrder=$(this).find('input[name="bookWithoutOrder[]"]').val();

			j++;
			dataString+='&recvBasis_' + j + '=' + recvBasis + '&barcodeNo_' + j + '=' + barcodeNo + '&progBookPiId_' + j + '=' + progBookPiId + '&productId_' + j + '=' + productId + '&orderId_' + j + '=' + orderId + '&rollId_' + j + '=' + rollId + '&rollWgt_' + j + '=' + rollWgt + '&yarnLot_' + j + '=' + yarnLot + '&yarnCount_' + j + '=' + yarnCount + '&colorId_' + j + '=' + colorId + '&stichLn_' + j + '=' + stichLn + '&brandId_' + j + '=' + brandId + '&rack_' + j + '=' + rack + '&shelf_' + j + '=' + shelf + '&dtlsId_' + j + '=' + dtlsId + '&transId_' + j + '=' + transId + '&rolltableId_' + j + '=' + rolltableId + '&rollNo_' + j + '=' + rollNo + '&locationId_' + j + '=' + locationId + '&machineId_' + j + '=' + machineId+ '&rollRate_' + j + '=' + rollRate+ '&bookWithoutOrder_' + j + '=' + bookWithoutOrder;
			
		});
		
		if(j<1)
		{
			alert('No data');
			return;
		}
		
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_issue_no*cbo_company_id*cbo_dyeing_source*cbo_dyeing_company*txt_issue_date*cbo_issue_purpose*txt_batch_no*txt_batch_id*update_id*txt_deleted_id',"../../")+dataString;
		//alert(data);return;
		freeze_window(operation);
		
		http.open("POST","requires/grey_fabric_issue_roll_wise_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_grey_fabric_issue_roll_wise_Reply_info;
	}

	function fnc_grey_fabric_issue_roll_wise_Reply_info()
	{
		if(http.readyState == 4) 
		{
			//release_freezing();return;
			var response=trim(http.responseText).split('**');
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
				$("#btn_mc").removeClass('formbutton_disabled');
			    $("#btn_mc").addClass('formbutton');
			}
			release_freezing();
		}
	}
	
	//var scanned_barcode=new Array();
	function create_row(is_update,barcode_no)
	{
		var row_num=$('#txt_tot_row').val();
		//var bar_code=$('#txt_bar_code_num').val();
		var bar_code=trim(barcode_no);
		var num_row =$('#scanning_tbl tbody tr').length; 
		
		var cbo_company_id = $('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		
		if(is_update==0)
		{
			if(!barcode_array[bar_code])
			{ 	
				alert('Barcode is Not Valid');
				$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
				 {
					$('#messagebox_main', window.parent.document).html('Barcode is Not Valid.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
				 });
				 $('#txt_bar_code_num').val('');
				return; 
			}
			
			if( jQuery.inArray( bar_code, scanned_barcode )>-1) 
			{ 
				alert('Sorry! Barcode Already Scanned.'); 
				$('#txt_bar_code_num').val('');
				return; 
			}
		}
		
		var company_id=roll_details_array[bar_code]['company_id'];
		if(cbo_company_id!=company_id)
		{
			alert("Multiple Company Not Allowed");
			return;	
		}
		
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
		
		if(is_update==0)
		{
			scanned_barcode.push(bar_code);
		}
		var cons_comp=constructtion_arr[roll_details_array[bar_code]['deter_d']]+", "+composition_arr[roll_details_array[bar_code]['deter_d']];
		
		$("#sl_"+row_num).text(row_num);
		$("#barcode_"+row_num).text(barcode_array[bar_code]);
		$("#roll_"+row_num).text(roll_details_array[bar_code]['roll_no']);
		$("#location_"+row_num).text(roll_details_array[bar_code]['location_name']);
		$("#prodId_"+row_num).text(roll_details_array[bar_code]['prod_id']);
		$("#bodyPart_"+row_num).text(roll_details_array[bar_code]['body_part_id']);
		$("#cons_"+row_num).text(cons_comp);
		$("#gsm_"+row_num).text(roll_details_array[bar_code]['gsm']);
		$("#dia_"+row_num).text(roll_details_array[bar_code]['width']);
		$("#stL_"+row_num).text(roll_details_array[bar_code]['stitch_length']);
		$("#color_"+row_num).text(roll_details_array[bar_code]['color']);
		$("#machine_"+row_num).text(roll_details_array[bar_code]['machine_no']);
		$("#rollWeight_"+row_num).text(roll_details_array[bar_code]['qnty']);
		
		
		if(roll_details_array[bar_code]['booking_without_order']==1)
		{
			$("#order_"+row_num).text("");
			$("#job_"+row_num).text("");
			$("#buyer_"+row_num).text(jsbuyer_name_array[without_order_buyer[bar_code]]);
			
			$("#orderId_"+row_num).val(roll_details_array[bar_code]['booking_id']);
		}
		else
		{
			$("#order_"+row_num).text(po_details_array[roll_details_array[bar_code]['po_breakdown_id']]['po_number']);
			$("#buyer_"+row_num).text(po_details_array[roll_details_array[bar_code]['po_breakdown_id']]['buyer_name']);
			$("#job_"+row_num).text(po_details_array[roll_details_array[bar_code]['po_breakdown_id']]['job_no']);
			
			$("#orderId_"+row_num).val(roll_details_array[bar_code]['po_breakdown_id']);
		}
		
		$("#knitCompany_"+row_num).text(roll_details_array[bar_code]['knitting_company']);
		$("#basis_"+row_num).text(roll_details_array[bar_code]['receive_basis']);
		$("#progBookPiNo_"+row_num).text(roll_details_array[bar_code]['booking_no']);
		
		$("#barcodeNo_"+row_num).val(barcode_array[bar_code]);
		$("#recvBasis_"+row_num).val(roll_details_array[bar_code]['receive_basis_id']);
		$("#progBookPiId_"+row_num).val(roll_details_array[bar_code]['booking_id']);
		$("#productId_"+row_num).val(roll_details_array[bar_code]['prod_id']);
		
		$("#rollId_"+row_num).val(roll_details_array[bar_code]['roll_id']);
		$("#rollWgt_"+row_num).val(roll_details_array[bar_code]['qnty']);
		$("#dtlsId_"+row_num).val(barcode_dtlsId_array[bar_code]);
		$("#transId_"+row_num).val(barcode_dtlsId_array[bar_code]);
		$("#yarnLot_"+row_num).val(roll_details_array[bar_code]['yarn_lot']);
		$("#yarnCount_"+row_num).val(roll_details_array[bar_code]['yarn_count']);
		$("#colorId_"+row_num).val(roll_details_array[bar_code]['color_id']);
		$("#stichLn_"+row_num).val(roll_details_array[bar_code]['stitch_length']);
		$("#locationId_"+row_num).val(roll_details_array[bar_code]['location_id']);
		$("#machineId_"+row_num).val(roll_details_array[bar_code]['machine_no_id']);
		$("#brandId_"+row_num).val(roll_details_array[bar_code]['brand_id']);
		$("#rack_"+row_num).val(roll_details_array[bar_code]['rack']);
		$("#shelf_"+row_num).val(roll_details_array[bar_code]['self']);
		
		$("#dtlsId_"+row_num).val(barcode_dtlsId_array[bar_code]);
		$("#transId_"+row_num).val(barcode_trnasId_array[bar_code]);
		$("#rolltableId_"+row_num).val(barcode_rollTableId_array[bar_code]);
		$("#rollRate_"+row_num).val(roll_details_array[bar_code]['rate']);
		$("#bookWithoutOrder_"+row_num).val(roll_details_array[bar_code]['booking_without_order']);
		
		
		$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
		
		$('#txt_tot_row').val(row_num);
		$('#txt_bar_code_num').val('');
		var total_roll_weight=0;
		total_roll_weight=number_format_common($("#roll_weight_total").text()*1+roll_details_array[bar_code]['qnty']*1,2,0,0);
		$("#roll_weight_total").text(total_roll_weight);
		$('#txt_bar_code_num').focus();
	}
	
	$('#txt_bar_code_num').live('keydown', function(e) {
		if (e.keyCode === 13) 
		{
			e.preventDefault();
			var bar_code=$('#txt_bar_code_num').val();
			create_row(0,bar_code);
		}
	});
	
	function add_dtls_data( data )
	{
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
		var bar_code =$("#barcodeNo_"+rid).val();
		var dtlsId =$("#dtlsId_"+rid).val();
		if(bar_code!="" && dtlsId!="")
		{
			var response=return_global_ajax_value( bar_code, 'roll_used_check', '', 'requires/grey_fabric_issue_roll_wise_controller');
			if(response==1) 
			{ 
				alert('Sorry! Barcode Already Used In Grey Receive By Batch.'); 
				return; 
			}
		}
		var num_row =$('#scanning_tbl tbody tr').length;
		var rolltableId =$("#rolltableId_"+rid).val(); 
		var txt_deleted_id=$('#txt_deleted_id').val();
		
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
		
		var selected_id='';
		if(rolltableId!='')
		{
			if(txt_deleted_id=='') selected_id=rolltableId; else selected_id=txt_deleted_id+','+rolltableId;
			$('#txt_deleted_id').val( selected_id );
		}
		
		var index = scanned_barcode.indexOf(bar_code);
		scanned_barcode.splice(index,1);
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
			var page_link='requires/grey_fabric_issue_roll_wise_controller.php?cbo_company_id='+cbo_company_id+'&action=batch_number_popup';
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
		var batch_id=return_global_ajax_value( data+"**"+cbo_company_id, 'check_batch_no', '', 'requires/grey_fabric_issue_roll_wise_controller');
		if(batch_id==0)
		{
			alert("Batch No Found");
			$('#txt_batch_no').val('');
			$('#txt_batch_id').val('');
			return;
		}
	}
	
	function fnc_reset_form()
	{
		$('#scanning_tbl tbody tr').remove();
		
		var html='<tr id="tr_1" align="center" valign="middle"><td width="30" id="sl_1"></td><td width="70" id="barcode_1"></td><td width="50" id="roll_1"></td><td width="70" id="location_1"></td><td width="50" id="prodId_1"></td><td style="word-break:break-all;" width="70" id="bodyPart_1"></td><td style="word-break:break-all;" width="100" id="cons_1"></td><td style="word-break:break-all;" width="50" id="gsm_1"></td><td style="word-break:break-all;" width="50" id="dia_1"></td><td style="word-break:break-all;" width="50" id="stL_1"></td><td style="word-break:break-all;" width="70" id="color_1"></td><td style="word-break:break-all;" width="60" id="machine_1"></td><td width="60" id="rollWeight_1" align="right"></td><td style="word-break:break-all;" width="60" id="buyer_1"></td><td style="word-break:break-all;" width="70" id="job_1"></td><td style="word-break:break-all;" width="70" id="order_1" align="left"></td><td style="word-break:break-all;" width="90" id="knitCompany_1"></td><td style="word-break:break-all;" width="70" id="basis_1"></td><td style="word-break:break-all;" width="100" id="progBookPiNo_1"></td><td id="button_1" align="center"><input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" /><input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/><input type="hidden" name="recvBasis[]" id="recvBasis_1"/><input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/><input type="hidden" name="productId[]" id="productId_1"/><input type="hidden" name="orderId[]" id="orderId_1"/><input type="hidden" name="rollId[]" id="rollId_1"/><input type="hidden" name="rollWgt[]" id="rollWgt_1"/><input type="hidden" name="yarnLot[]" id="yarnLot_1"/><input type="hidden" name="yarnCount[]" id="yarnCount_1"/><input type="hidden" name="colorId[]" id="colorId_1"/><input type="hidden" name="stichLn[]" id="stichLn_1"/><input type="hidden" name="locationId[]" id="locationId_1"/><input type="hidden" name="machineId[]" id="machineId_1"/><input type="hidden" name="brandId[]" id="brandId_1"/><input type="hidden" name="rack[]" id="rack_1"/><input type="hidden" name="shelf[]" id="shelf_1"/><input type="hidden" name="dtlsId[]" id="dtlsId_1"/><input type="hidden" name="transId[]" id="transId_1"/><input type="hidden" name="rolltableId[]" id="rolltableId_1"/><input type="hidden" name="rollRate[]" id="rollRate_1"/><input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_1"/></td></tr>';
		
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
	
	function fnc_mc_wise_print()
	{
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val()+'*'+report_title,'mc_wise_print');
	}
	
</script>
</head>
<body onLoad="set_hotkey();$('#txt_bar_code_num').focus();">
    <div align="center" style="width:100%;">
		<? echo load_freeze_divs ("../../",$permission); ?>
        <form name="rollscanning_1" id="rollscanning_1"  autocomplete="off"  >
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
                                echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", 0, "",0 );
                            ?>
                        </td>
                        <td class="must_entry_caption" align="right">Dyeing Source</td>
                        <td>
							<?
                                echo create_drop_down( "cbo_dyeing_source", 152, $knitting_source, "", 1, "-- Select --", $selected, "load_drop_down( 'requires/grey_fabric_issue_roll_wise_controller',this.value+'**'+$('#cbo_company_id').val(),'load_drop_down_knitting_com','dyeing_company_td' );","","1,3" );
                            ?>
                        </td>
                        <td align="right" class="must_entry_caption">Dyeing Company</td>
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
								echo create_drop_down( "cbo_issue_purpose", 152, $yarn_issue_purpose,"", 1, "-- Select Purpose --", 11, "","","11,3,4,8" ); 
							?>
                        </td>
                        <td align="right">Batch No</td>
                        <td>
                        	<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:140px;" placeholder="Write / Browse" onDblClick="openmypage_batchNo();" onChange="check_batch(this.value);" />
                            <input type="hidden" id="txt_batch_id" />
                        </td>
                    </tr>
                    <tr>
                    	<td height="5" colspan="6"></td>
                    </tr>
                    <tr>
                        <td height="22" colspan="6" align="center"><strong>Roll Number</strong>&nbsp;&nbsp;
                            <input type="text" name="txt_bar_code_num" id="txt_bar_code_num" class="text_boxes_numeric" style="width:170px;" onDblClick="openmypage_barcode()" placeholder="Browse/Write/scan"/>
                        </td>
                    </tr>
                </table>
			</fieldset>
			<br>
			<fieldset style="width:1335px;text-align:left">
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
				<table cellpadding="0" width="1335" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="30">SL</th>
                        <th width="70">Barcode No</th>
                        <th width="50">Roll No</th>
                        <th width="70">Location</th>
                        <th width="50">Product Id</th>
                        <th width="70">Body Part</th>
                        <th width="100">Construction/ Composition</th>
                        <th width="50">GSM</th>
                        <th width="50">Dia</th>
                        <th width="50">Stitch Length</th>
                        <th width="70">Color</th>
                        <th width="60">Machine No.</th>
                        <th width="60">Roll Wgt.</th>
                        <th width="60">Buyer</th>
                        <th width="70">Job No</th>
                        <th width="70">Order No</th>
                        <th width="90">Knit Company</th>
                        <th width="70">Basis</th>
                        <th width="100">Program/ Booking/PI No</th>
                        <th></th>
                    </thead>
                 </table>
                 <div style="width:1335px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="1310" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody>
                        	<tr id="tr_1" align="center" valign="middle">
                                <td width="30" id="sl_1"></td>
                                <td width="70" id="barcode_1"></td>
                                <td width="50" id="roll_1"></td>
                                <td width="70" id="location_1"></td>
                                <td width="50" id="prodId_1"></td>
                                <td style="word-break:break-all;" width="70" id="bodyPart_1"></td>
                                <td style="word-break:break-all;" width="100" id="cons_1" align="left"></td>
                                <td style="word-break:break-all;" width="50" id="gsm_1"></td>
                                <td style="word-break:break-all;" width="50" id="dia_1"></td>
                                <td style="word-break:break-all;" width="50" id="stL_1"></td>
                                <td style="word-break:break-all;" width="70" id="color_1"></td>
                                <td style="word-break:break-all;" width="60" id="machine_1"></td>
                                <td width="60" align="right" id="rollWeight_1"></td>
                                <td style="word-break:break-all;" width="60" id="buyer_1"></td>
                                <td style="word-break:break-all;" width="70" id="job_1"></td>
                                <td style="word-break:break-all;" width="70" id="order_1" align="left"></td>
                                <td style="word-break:break-all;" width="90" id="knitCompany_1"></td>
                                <td style="word-break:break-all;" width="70" id="basis_1"></td>
                                <td style="word-break:break-all;" width="100" id="progBookPiNo_1"></td>
                                <td id="button_1" align="center">
                                    <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                                    <input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/>
                                    <input type="hidden" name="recvBasis[]" id="recvBasis_1"/>
                                    <input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/>
                                    <input type="hidden" name="productId[]" id="productId_1"/>
                                    <input type="hidden" name="orderId[]" id="orderId_1"/>
                                    <input type="hidden" name="rollId[]" id="rollId_1"/>
                                    <input type="hidden" name="rollWgt[]" id="rollWgt_1"/>
                                    <input type="hidden" name="yarnLot[]" id="yarnLot_1"/>
                                    <input type="hidden" name="yarnCount[]" id="yarnCount_1"/>
                                    <input type="hidden" name="colorId[]" id="colorId_1"/>
                                    <input type="hidden" name="stichLn[]" id="stichLn_1"/>
                                    <input type="hidden" name="locationId[]" id="locationId_1"/>
                                    <input type="hidden" name="machineId[]" id="machineId_1"/>
                                    <input type="hidden" name="brandId[]" id="brandId_1"/>
                                    <input type="hidden" name="rack[]" id="rack_1"/>
                                    <input type="hidden" name="shelf[]" id="shelf_1"/>
                                    <input type="hidden" name="dtlsId[]" id="dtlsId_1"/>
                                    <input type="hidden" name="transId[]" id="transId_1"/>
                                    <input type="hidden" name="rolltableId[]" id="rolltableId_1"/>
                                    <input type="hidden" name="rollRate[]" id="rollRate_1"/>
                                    <input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_1"/>
                                </td>
                            </tr>
                           
                        </tbody>
                        <tfoot>
                         <tr>
                            <th colspan="12" align="right">Total</th>
                            <th id="roll_weight_total"></th>
                            <th colspan="7"> </th>
                        </tr>
                        </tfoot>
                	</table>
                </div>
                <br>
                <table width="1330" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                    <tr>
                        <td align="center" class="button_container">
                        	<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
                            <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes" value="">
                            <? 
                            	echo load_submit_buttons($permission,"fnc_grey_fabric_issue_roll_wise",0,1,"fnc_reset_form()",1);
                            ?>
                             <input type="button" name="print_barcode" id="print_barcode" class="formbutton_disabled" value="Print Barcode" style=" width:100px" onClick="barcode_print();" >
                             <input type="button" name="btn_fabric_details" id="btn_fabric_details" class="formbutton_disabled" value="Fabric Details" style=" width:100px" onClick="fabric_details();" >
                             <input type="button" name="btn_mc" id="btn_mc" class="formbutton_disabled" value="MC Wise" style=" width:100px" onClick="fnc_mc_wise_print();" >
                        </td>
                    </tr>  
                </table>
			</fieldset>
        </form>	 
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
