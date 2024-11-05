<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Knit Grey Fabric Issue Entry
				
Functionality	:	
JS Functions	:
Created by		:	Bilas 
Creation date 	: 	07-05-2013
Updated by 		: 	Fuad (Add Plan Field in Details Part)	
Update date		: 	23-11-2014   
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
echo load_html_head_contents("Grey Issue Info","../../", 1, 1, $unicode,1,1); 

?>	

<script>
	
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";


<? 
		$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][16]);
		if($data_arr) echo "var field_level_data= ". $data_arr . ";\n";
		//echo "alert(JSON.stringify(field_level_data));";
		
	?>
	
		

// popup for booking no ----------------------	
function popuppage_fabbook()
{
	if( form_validation('cbo_company_id*cbo_basis*cbo_issue_purpose','Company Name*Issue Basis*Issue Purpose')==false )
	{
		return;
	}
	var company			= $("#cbo_company_id").val();
	var cbo_basis	 	= $("#cbo_basis").val();
	var issue_purpose	= $("#cbo_issue_purpose").val();
	var hidden_is_sales	= $("#hidden_is_sales").val();
	var cbo_buyer_name	= $("#cbo_buyer_name").val();
	var update_id		= $("#hidden_system_id").val();
	var dtls_tbl_id		= $("#dtls_tbl_id").val();
	
	var page_link='requires/grey_fabric_issue_controller.php?action=fabbook_popup&company='+company+'&cbo_basis='+cbo_basis+'&issue_purpose='+issue_purpose+'&cbo_buyer_name='+cbo_buyer_name+'&hidden_is_sales='+hidden_is_sales+'&update_id='+update_id+'&dtls_tbl_id='+dtls_tbl_id;
	if(cbo_basis==1) var title="Booking Information"; else var title="Program Information";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px, height=400px, center=1, resize=0, scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var bookingNumber = this.contentDoc.getElementById("hidden_booking_number").value; //bookingID_bookingNo_buyerID_jobNo concate

		$("#txtItemDescription").val(''); 
		$("#hiddenProdId").val('');	

		if (bookingNumber!="")
		{ 

			bookingNumber = bookingNumber.split("__"); 
			freeze_window(5);
			
			$("#txt_program_no").val('');
			$("#txtItemDescription").val(''); 
			$("#hiddenProdId").val('');	
			$("#txtIssueQnty").val('');	
			$("#save_data").val('');
			$("#txt_fabric_received").val(''); 
			$("#txt_cumulative_issued").val('');	
			$("#txt_yet_to_issue").val('');	
			$("#txt_global_stock").val('');
			$("#hiddenAvgRate").val('');	
			$("#hidden_is_sales").val('');	
				
			if(cbo_basis==1)
			{
				$("#txt_booking_id").val(bookingNumber[0]);
				$("#txt_booking_no").val(bookingNumber[1]);
			}
			else
			{
				$("#txt_program_no").val(bookingNumber[0]);
			}
			
			$("#cbo_buyer_name").val(bookingNumber[2]);				
			$("#txt_style_ref").val(bookingNumber[3]);
			$("#txt_order_no").val(bookingNumber[4]);
			$("#hidden_order_id").val(bookingNumber[5]);
			$("#hidden_is_sales").val(bookingNumber[6]);
			
			/*if(bookingNumber[5]!="")
			{
				get_php_form_data(bookingNumber[5], "populate_display_from_data", "requires/grey_fabric_issue_controller");	
			}*/


			var is_sales=bookingNumber[6];
			var color_id=bookingNumber[7];
			if(issue_purpose==8)
			{
				load_drop_down( 'requires/grey_fabric_issue_controller', bookingNumber[1]+'_'+$('#cbo_issue_purpose').val()+'_'+color_id+'_'+cbo_basis+'_'+$('#hidden_order_id').val(), 'load_drop_down_color', 'color_td' );
				set_multiselect('cbo_color_id','0','0','','0');
			}
			/*if (cbo_basis==3) 
			{

				load_drop_down( 'requires/grey_fabric_issue_controller', bookingNumber[1]+'_'+$('#cbo_issue_purpose').val()+'_'+color_id+'_'+cbo_basis+'_'+$('#hidden_order_id').val(), 'load_drop_down_color', 'color_td' );
				set_multiselect('cbo_color_id','0','0','','0');
			}*/
			if(issue_purpose==8)
			{
				load_drop_down( 'requires/grey_fabric_issue_controller', bookingNumber[1]+'_'+$('#cbo_issue_purpose').val()+'_'+color_id+'_'+cbo_basis+'_'+$('#hidden_order_id').val()+'_'+is_sales, 'load_drop_down_color', 'color_td' );
				set_multiselect('cbo_color_id','0','0','','0');
			}
			else if (cbo_basis==3 && is_sales==1) 
			{

				load_drop_down( 'requires/grey_fabric_issue_controller', bookingNumber[1]+'_'+$('#cbo_issue_purpose').val()+'_'+color_id+'_'+cbo_basis+'_'+$('#hidden_order_id').val()+'_'+is_sales, 'load_drop_down_color', 'color_td' );
				set_multiselect('cbo_color_id','0','0','','0');
			}
			else{
				load_drop_down( 'requires/grey_fabric_issue_controller', bookingNumber[1]+'_'+$('#cbo_issue_purpose').val()+'_'+color_id+'_'+cbo_basis+'_'+$('#hidden_order_id').val()+'_'+is_sales, 'load_drop_down_color', 'color_td' );
				set_multiselect('cbo_color_id','0','0','','0');
			}
			
			release_freezing();	 
		}
	}		
}

function openmypage_order() 
{
	var cbo_company_id=$('#cbo_company_id').val();
	var cbo_basis=$('#cbo_basis').val();
	var hidden_order_id=$('#hidden_order_id').val();
	var buyer_name=$('#cbo_buyer_name').val();
	var buyer_name=$('#cbo_buyer_name').val();
	var bookingNumber=0;
	var color_id=0;
	var is_sales=0;
	
	if(form_validation('cbo_company_id*cbo_basis','Company*Basis')==false)
	{
		return;
	}  

	if(cbo_basis==2)
	{
		var title = 'PO Info';	
		var page_link = 'requires/grey_fabric_issue_controller.php?cbo_company_id='+cbo_company_id+'&hidden_order_id='+hidden_order_id+'&buyer_name='+buyer_name+'&action=po_search_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hidden_order_id=this.contentDoc.getElementById("hidden_order_id").value;  
			var hidden_order_no=this.contentDoc.getElementById("hidden_order_no").value;  
			var buyer=this.contentDoc.getElementById("hide_buyer").value; 
			var style_ref=this.contentDoc.getElementById("hide_style_ref").value; 
			
			$('#cbo_buyer_name').val(buyer);
			$('#txt_style_ref').val(style_ref);
			$("#txt_order_no").val(hidden_order_no);
			$("#hidden_order_id").val(hidden_order_id);

			if (hidden_order_id!="")
			{	
				load_drop_down( 'requires/grey_fabric_issue_controller', bookingNumber+'_'+$('#cbo_issue_purpose').val()+'_'+color_id+'_'+cbo_basis+'_'+$('#hidden_order_id').val()+'_'+is_sales, 'load_drop_down_color', 'color_td' );
				set_multiselect('cbo_color_id','0','0','','0');
			}
		}
	}
}

function new_item_controll()
{
	var isRoll=$("#hidden_is_roll_maintain").val();
	var isBatch=$("#hidden_is_batch_maintain").val();
	var issuePurpose=$("#cbo_issue_purpose").val();
	if(isRoll==1)
	{
		$("#txtNoOfRoll").attr("placeholder","Double Click");
		$("#txtItemDescription").attr("placeholder","Display");
		$("#txtNoOfRoll").attr("readonly","readonly");
		$("#txtItemDescription").attr("readonly","readonly");
	}
	else
	{
		$("#txtNoOfRoll").attr("placeholder","Write No of Roll");
		$("#txtItemDescription").attr("placeholder","Double Click");
		$("#txtNoOfRoll").removeAttr("readonly","readonly");
		$("#txtItemDescription").attr("readonly","readonly");
	}
	
	if(issuePurpose==11 || issuePurpose==4)
	{
		$("#txtIssueQnty").attr("placeholder","Double Click"); 
		$("#txtIssueQnty").attr("readonly","readonly");
	}
	else
	{
		$("#txtIssueQnty").removeAttr("placeholder").attr("placeholder","Wirte"); 
		$("#txtIssueQnty").removeAttr("readonly");
	}
}

function order_browse_active_inactive()
{
	var issueBasis=$("#cbo_basis").val();
	if(issueBasis==2)
	{
		$("#txt_order_no").attr("placeholder","Double Click"); 
		$('#txt_order_no').attr('onDblClick','openmypage_order();');	
	}
	else
	{
		$("#txt_order_no").attr("placeholder","Display"); 
		$("#txt_order_no").removeAttr("onDblClick");
	}
}

//function for field enable disable
function enable_disable()
{
	var issuePurpose	=$("#cbo_issue_purpose").val();
	var issueBasis		=$("#cbo_basis").val();
	var isBatch			=$("#hidden_is_batch_maintain").val();
	
	$("#txt_booking_no").val(""); 
	$("#txt_booking_id").val("");
	$("#txt_program_no").val("");
	//fabric booking
	if(issueBasis==2)
	{	
		$("#txt_booking_no").attr("disabled",true);	
		$("#txt_program_no").attr("disabled",true);	
		$("#txt_order_no").attr("placeholder","Double Click"); 
		$('#txt_order_no').attr('onDblClick','openmypage_order();');	
		$("#txtIssueQnty").attr("placeholder","Double Click"); 
		$("#txtIssueQnty").attr("readonly","readonly");	
	}
	else if(issueBasis==3)	
	{
		/*if(issuePurpose==11 || issuePurpose==4)
		{
			$("#txt_booking_no").attr("disabled",true);	
			$("#txt_program_no").removeAttr("disabled");	
			$("#txt_order_no").attr("placeholder","Display"); 
			$("#txt_order_no").removeAttr("onDblClick");
			$("#txtIssueQnty").attr("placeholder","Double Click"); 
			$("#txtIssueQnty").attr("readonly","readonly");	
		}
		else
		{
			alert("Knitting Plan is not Allowed For This Issue Purpose");
			$("#cbo_basis").val(0);
		}*/
		
		if(issuePurpose==8)
		{
			//alert("Knitting Plan is not Allowed For This Issue Purpose");
			//$("#cbo_basis").val(0);
			$("#txt_booking_no").attr("disabled",true);	
			$("#txt_program_no").removeAttr("disabled");
		}
		else
		{
			$("#txt_booking_no").attr("disabled",true);	
			$("#txt_program_no").removeAttr("disabled");	
			$("#txt_order_no").attr("placeholder","Display"); 
			$("#txt_order_no").removeAttr("onDblClick");
			$("#txtIssueQnty").attr("placeholder","Double Click"); 
			$("#txtIssueQnty").attr("readonly","readonly");	
		}
	}
	else
	{	
		$("#txt_booking_no").removeAttr("disabled");
		$("#txt_program_no").attr("disabled",true);	
		$("#txt_order_no").attr("placeholder","Display"); 
		$("#txt_order_no").removeAttr("onDblClick");
		
		if(issuePurpose==11 || issuePurpose==4)
		{
			$("#txtIssueQnty").attr("placeholder","Double Click"); 
			$("#txtIssueQnty").attr("readonly","readonly");	
		}
		else
		{
			$("#txtIssueQnty").removeAttr("placeholder").attr("placeholder","Wirte"); 
			$("#txtIssueQnty").removeAttr("readonly");	
		}
	}
	 	
	if(isBatch==1) 
	{
		//$("#txt_batch_no").removeAttr("disabled").attr("disabled",true);
		$("#txt_batch_no").removeAttr("placeholder").attr("placeholder","Display/Browse"); 	
		$("#txt_batch_no").attr("readonly","readonly");		 
		//$("#txt_batch_no").val("");
	}
	else 
	{
		$("#txt_batch_no").removeAttr("placeholder").attr("placeholder","Write");  
		$("#txt_batch_no").removeAttr("readonly","readonly"); 
	}

	//function call for item list enable disable
	//new_item_controll();
}

function openpopup_batch()
{
	var isBatch=$("#hidden_is_batch_maintain").val();	
	if( isBatch!=1 ) // batch pop up not allow
	{ 		
		return;
	}
	var cbo_company_id = $('#cbo_company_id').val();
	var title = 'Batch Info';	
	var page_link = 'requires/grey_fabric_issue_controller.php?cbo_company_id='+cbo_company_id+'&action=batch_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var batch_id=this.contentDoc.getElementById("txt_batch_id").value;	 
		var batch_no=this.contentDoc.getElementById("txt_batch_no").value;
		var batch_color=this.contentDoc.getElementById("txt_batch_color").value;
		 
		$("#txt_batch_id").val(batch_id);
		$("#txt_batch_no").val(batch_no);
		$("#txt_batch_color").val(batch_color);
  	}
}

function openroll_popup() 	 
{
	//txtRollNo  txtRollPOid txtRollPOQnty
	var cbo_company_id = $('#cbo_company_id').val();
 	var hidden_roll_id = $('#txtRollNo').val();
	var hidden_roll_qnty = $('#txtRollPOQnty').val();	
	var txt_batch_id = $('#txt_batch_id').val();
	//alert(hidden_roll_id+"="+hidden_roll_qnty);
	if(form_validation('cbo_company_id*cbo_basis*cbo_issue_purpose','Company*Basis*Issue Purpose')==false)
	{
		return;
	} 
	var isRoll=$("#hidden_is_roll_maintain").val();	
	if( isRoll!=1 ) // roll pop up not allow, roll if No
	{ 		
		return;
	}
	var title = 'Roll Info';	
	var page_link = 'requires/grey_fabric_issue_controller.php?cbo_company_id='+cbo_company_id+'&hidden_roll_id='+hidden_roll_id+'&hidden_roll_qnty='+hidden_roll_qnty+'&txt_batch_id='+txt_batch_id+'&action=roll_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var rollTableId=this.contentDoc.getElementById("txt_selected_id").value;	 
		var issueQnty=this.contentDoc.getElementById("txt_issue_qnty").value;
		 	
 		//show_list_view(rollTableId+"**"+issueQnty,'populate_child_from_data','td_item_list','requires/grey_fabric_issue_controller','');		
		get_php_form_data(rollTableId+"**"+issueQnty, "populate_child_from_data", "requires/grey_fabric_issue_controller");		
	}
}

function openDescription_popup() 	 
{
	var cbo_company_id = $('#cbo_company_id').val();	
	var cbo_basis = $('#cbo_basis').val();	
	var txt_booking_no = $('#txt_booking_no').val();
	var txt_program_no = $('#txt_program_no').val();
	var cbo_issue_purpose = $('#cbo_issue_purpose').val();	
	var txt_booking_id = $('#txt_booking_id').val();
	var hidden_order_id = $('#hidden_order_id').val();
	var isRackBalance=$("#hidden_is_rack_balance").val();
	var cbo_store_name=$("#cbo_store_name").val();		
	var hidden_is_sales=$("#hidden_is_sales").val();		
	var txt_service_booking_no=$("#txt_service_booking_no").val();		
	
	if(form_validation('cbo_company_id*cbo_basis*cbo_issue_purpose*cbo_store_name','Company*Basis*Issue Purpose*Store')==false)
	{
		return;
	} 
	if(cbo_basis==1 && txt_booking_no=="")
	{
		alert("Select Booking/ Program No First.");
		$('#txt_booking_no').focus();
		return;
	}
	else if(cbo_basis==3 && txt_program_no=="")
	{
		alert("Select Program No First.");
		$('#txt_program_no').focus();
		return;
	}
	
	if(cbo_basis==2 && hidden_order_id=="")
	{
		alert("Select Order Numbers First.");
		$('#txt_order_no').focus();
		return;
	}
	
	if(cbo_basis==3) 
	{
		txt_booking_no=txt_program_no;
		if (hidden_is_sales==2)
		{
			txt_booking_id=txt_program_no;
		}
	}
		
	
	var isRoll=$("#hidden_is_roll_maintain").val();	
	if( isRoll==1 ) // roll pop up not allow, roll if Yes
	{ 		
		return;
	}
	
	var title = 'Item Description Info';	
	var page_link = 'requires/grey_fabric_issue_controller.php?cbo_company_id='+cbo_company_id+'&action=itemDescription_popup'+'&txt_booking_no='+txt_booking_no+'&cbo_basis='+cbo_basis+'&cbo_issue_purpose='+cbo_issue_purpose+'&txt_booking_id='+txt_booking_id+'&hidden_order_id='+hidden_order_id+'&isRackBalance='+isRackBalance+'&cbo_store_name='+cbo_store_name+'&txt_service_booking_no='+txt_service_booking_no;     
	 
	if( isRackBalance==1 ) 
	{
		var width="1040px";
	}
	else
	{
		var width="980px";
	}
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+width+',height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var data=this.contentDoc.getElementById("txt_selected_id").value;
		data=data.split("_");
		var prodID=data[0];
		$('#hiddenProdId').val(data[0]);
		$('#txtItemDescription').val(data[1]);
		$('#txtYarnLot').val(data[2]);
		set_multiselect('cbo_yarn_count','0','1',data[3],'0');
		$('#txt_rack_hidden').val(data[4]);
		$('#txt_shelf_hidden').val(data[5]);
		$('#txt_global_stock').val(data[6]);
		$('#txt_stitch_length').val(data[7]);
		$('#hiddenAvgRate').val(data[8]);
		$('#cbo_floor_hidden').val(data[9]);
		$('#cbo_room_hidden').val(data[10]);

		$('#cbo_floor').val(data[11]);
		$('#cbo_room').val(data[12]);
		$('#txt_rack').val(data[13]);
		$('#txt_shelf').val(data[14]);

		if(cbo_issue_purpose==8 && cbo_basis==3) 
		{
			$("#txtIssueQnty").removeAttr("placeholder").attr("placeholder","Write");  
			$("#txtIssueQnty").removeAttr("readonly","readonly");

			get_php_form_data(txt_booking_id+"**"+data[0], "populate_data_about_sample", "requires/grey_fabric_issue_controller" );
		}
		
		if(cbo_basis==1 && (cbo_issue_purpose==3 || cbo_issue_purpose==8 || cbo_issue_purpose==26 || cbo_issue_purpose==29 || cbo_issue_purpose==30 || cbo_issue_purpose==31))
		{
			get_php_form_data(txt_booking_id+"**"+data[0], "populate_data_about_sample", "requires/grey_fabric_issue_controller" );
		}
  	}
}

function issueQntyPopup() //issue quantity
{
	// alert();return;
	var isRoll=$("#hidden_is_roll_maintain").val();
	var purpose = $("#cbo_issue_purpose").val();
	var receive_basis=$('#cbo_basis').val();
	var booking_no=$('#txt_booking_no').val();
	var program_no=$('#txt_program_no').val();
	var cbo_company_id = $('#cbo_company_id').val();
 	var save_data = $("#save_data").val();
	var all_po_id = $("#all_po_id").val();
	var prod_id = $("#hiddenProdId").val();
	var issueQnty = $('#txtReqQnty').val();
	var hidden_order_id = $('#hidden_order_id').val();
	var dtls_tbl_id = $('#dtls_tbl_id').val();
	var distribution_method = $('#distribution_method_id').val();
	var cbo_store_name = $('#cbo_store_name').val();

	var cbo_floor_hidden = $('#cbo_floor_hidden').val();
	var cbo_room_hidden = $('#cbo_room_hidden').val();
	var txt_rack_hidden = $('#txt_rack_hidden').val();
	var txt_shelf_hidden = $('#txt_shelf_hidden').val();
	var hidden_is_sales = $('#hidden_is_sales').val();
	var txt_service_booking_no = $('#txt_service_booking_no').val();
	
	if((purpose==3 || purpose==8 || purpose==26 || purpose==29 || purpose==30 || purpose==31) && (receive_basis==1)) 
	{
		return;
	}
	else if(purpose==8 && receive_basis==3) 
	{
		//$("#txtIssueQnty").removeAttr("placeholder").attr("placeholder","Write");  
		//$("#txtIssueQnty").removeAttr("readonly","readonly"); 
		return;
	}
	
	if(form_validation('cbo_company_id*cbo_basis*cbo_issue_purpose*txtItemDescription*cbo_store_name','Company*Basis*Issue Purpose*Item Description*Store Name')==false)
	{
		return;
	}  
	else if(receive_basis==1 && (purpose==11 || purpose==4) )
	{ 
		if( form_validation('txt_booking_no','Booking')==false )
			return;
	}
	else if(receive_basis==3 && (purpose==11 || purpose==4) )
	{ 
		if( form_validation('txt_order_no','Order No')==false )
			return;
	}
	var title = 'PO Info';	
	var page_link = 'requires/grey_fabric_issue_controller.php?receive_basis='+receive_basis+'&cbo_company_id='+cbo_company_id+'&booking_no='+booking_no+'&all_po_id='+all_po_id+'&save_data='+save_data+'&issueQnty='+issueQnty+'&distribution_method='+distribution_method+'&isRoll='+isRoll+'&prod_id='+prod_id+'&hidden_order_id='+hidden_order_id+'&program_no='+program_no+'&store_id='+cbo_store_name+'&floor_id='+cbo_floor_hidden+'&room_id='+cbo_room_hidden+'&rack_id='+txt_rack_hidden+'&self_id='+txt_shelf_hidden+'&dtls_tbl_id='+dtls_tbl_id+'&hidden_is_sales='+hidden_is_sales+'&txt_service_booking_no='+txt_service_booking_no+'&action=po_popup';  
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var save_string=this.contentDoc.getElementById("save_string").value;	 
		var tot_issue_qnty=this.contentDoc.getElementById("tot_grey_qnty").value;  //this is issue qnty 
 		var all_po_id=this.contentDoc.getElementById("all_po_id").value;  
		var distribution_method=this.contentDoc.getElementById("distribution_method").value;
		
		$('#save_data').val(save_string);
		$('#txtIssueQnty').val(tot_issue_qnty);
		$('#txtReqQnty').val(tot_issue_qnty);
 		$('#all_po_id').val(all_po_id);
		$('#distribution_method_id').val(distribution_method);
		var prod_id = $('#hiddenProdId').val();
		var cbo_basis = $('#cbo_basis').val();

		//hidden_order_id
		
		/*if(receive_basis==2)
		{
			get_php_form_data(all_po_id+"**"+prod_id, "populate_data_about_order", "requires/grey_fabric_issue_controller" );
		}*/
		
		get_php_form_data(all_po_id+"**"+prod_id+"**"+cbo_company_id+"**"+program_no+"**"+receive_basis+"**"+cbo_store_name+"**"+cbo_floor_hidden+"**"+cbo_room_hidden+"**"+txt_rack_hidden+"**"+txt_shelf_hidden+"**"+hidden_is_sales, "populate_data_about_order", "requires/grey_fabric_issue_controller" );
		load_drop_down( 'requires/grey_fabric_issue_controller', all_po_id+'_'+$('#cbo_issue_purpose').val()+'_'+color_id+'_'+cbo_basis+'_'+$('#hidden_order_id').val(), 'load_drop_down_color', 'color_td' );


		set_multiselect('cbo_color_id','0','0','','0');
	}
}

function generate_report_file(data,action,page)
{
	window.open("requires/grey_fabric_issue_controller.php?data=" + data+'&action='+action, true );
}

function fnc_grey_fabric_issue_entry(operation)
{
	
	
	if(operation==2)
	{
		show_msg('13');
		return;
	}
	//alert(operation);
	if(operation==4 || operation==6 || operation==7 || operation==9)
	{

		var print_with_vat=0;
		var report_title=$( "div.form_caption" ).html();
		var basis=$('#cbo_basis').val();
		var show_item='';
		if(operation==4 && basis==1)
		{			
			var r=confirm("Press  \"Cancel\"  to hide  Comments\nPress  \"OK\"  to Show Comments");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
			window.open("requires/grey_fabric_issue_controller.php?data=" + $('#cbo_company_id').val()+'*'+$('#hidden_system_id').val()+'*'+$('#txt_system_no').val()+'*'+report_title+'*'+print_with_vat+'*'+operation+'*'+show_item+'&action=grey_fabric_issue_print', true );
		}
			
		else if(operation==7)
		{
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#hidden_system_id').val()+'*'+$('#txt_system_no').val()+'*'+report_title+'*'+print_with_vat+'*'+operation+'*'+show_item,'grey_fabric_issue_print_7','requires/grey_fabric_issue_controller');
		}
		else if(operation==9)
		{
			if( form_validation('txt_service_booking_no','Service Booking')==false )
			{
				return;
			}
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#hidden_system_id').val()+'*'+$('#txt_system_no').val()+'*'+report_title+'*'+print_with_vat+'*'+operation+'*'+show_item,'grey_fabric_issue_print_9','requires/grey_fabric_issue_controller');
		}
		else
		{
			var r=confirm("Press  \"OK\"  to Show  Buyer and Style\nPress  \"Cancel\"  to hide Buyer and Style");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#hidden_system_id').val()+'*'+$('#txt_system_no').val()+'*'+report_title+'*'+print_with_vat+'*'+operation+'*'+show_item+'*'+$('#hidden_is_sales').val(),'grey_fabric_issue_print','requires/grey_fabric_issue_controller');
		}

		/*print_report( $('#cbo_company_id').val()+'*'+$('#hidden_system_id').val()+'*'+report_title, "grey_fabric_issue_print", "requires/grey_fabric_issue_controller" ) */
		return;
	}
	else if(operation==5)
	{
		if ($("#txt_system_no").val()=="")
		{
			alert ("Please Save First.");
			return;
		}
		var print_with_vat=1;
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#hidden_system_id').val()+'*'+$('#txt_system_no').val()+'*'+report_title+'*'+print_with_vat,'grey_fabric_issue_print','requires/grey_fabric_issue_controller');
		/*print_report( $('#cbo_company_id').val()+'*'+$('#hidden_system_id').val()+'*'+report_title, "grey_fabric_issue_print", "requires/grey_fabric_issue_controller" ) */
		return;
	}
	else if(operation==8)
	{
		if ($("#txt_system_no").val()=="")
		{
			alert ("Please Save First.");
			return;
		}
		var print_with_vat=0;
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#hidden_system_id').val()+'*'+$('#txt_system_no').val()+'*'+report_title+'*'+print_with_vat,'grey_fabric_issue_print_8','requires/grey_fabric_issue_controller');
		/*print_report( $('#cbo_company_id').val()+'*'+$('#hidden_system_id').val()+'*'+report_title, "grey_fabric_issue_print", "requires/grey_fabric_issue_controller" ) */
		return;
	}
	else if(operation==0 || operation==1)
	{
		if($("#is_posted_accout").val()==1)
		{
			alert("Already Posted In Accounting. Save Update Delete Restricted.");
			return;
		}

		if($("#cbo_basis").val()==3 && $("#txt_program_no").val()=="")
		{
			alert("Please Select Program No First.");
			return;
		}
		if( form_validation('cbo_company_id*txt_issue_date*cbo_basis*cbo_issue_purpose*cbo_dyeing_com*cbo_store_name*txtItemDescription*txtIssueQnty','Company Name*Issue Date*Basis*Issue Purpose*Dyeing Company*Store Name*Item Description*Issue Quantity')==false )
		{
			return;
		}
		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_issue_date').val(), current_date)==false)
		{
			alert("Issue Date Can not Be Greater Than Today");
			return;
		}
		
		if(($("#txtIssueQnty").val()*1 > $("#txt_yet_to_issue").val()*1+$("#hiddenIssueQnty").val()*1)) 
		{
			alert("Issue Quantity Exceeds Yet To Issue Quantity.");
			return;
		}

		// Store upto validation start
		var store_update_upto=$('#store_update_upto').val()*1;
		var cbo_floor=$('#cbo_floor').val()*1;
		var cbo_room=$('#cbo_room').val()*1;
		var txt_rack=$('#txt_rack').val()*1;
		var txt_shelf=$('#txt_shelf').val()*1;
		//var cbo_bin=$('#cbo_bin').val()*1;
		
		if(store_update_upto > 1)
		{
			/*if(store_update_upto==6 && (cbo_floor==0 || cbo_room==0 || txt_rack==0 || txt_shelf==0 || cbo_bin==0))
			{
				alert("Up To Bin Value Full Fill Required For Inventory");return;
			}
			else */if(store_update_upto==5 && (cbo_floor==0 || cbo_room==0 || txt_rack==0 || txt_shelf==0))
			{
				alert("Up To Shelf Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==4 && (cbo_floor==0 || cbo_room==0 || txt_rack==0))
			{
				alert("Up To Rack Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==3 && (cbo_floor==0 || cbo_room==0))
			{
				alert("Up To Room Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==2 && cbo_floor==0)
			{
				alert("Up To Floor Value Full Fill Required For Inventory");return;
			}
		}
		// Store upto validation End

		var dataString = "txt_system_no*hidden_system_id*cbo_company_id*hidden_is_roll_maintain*hidden_is_batch_maintain*hidden_is_rack_balance*txt_issue_date*cbo_basis*cbo_issue_purpose*cbo_dyeing_source*cbo_dyeing_com*txt_booking_no*txt_booking_id*txt_batch_no*txt_batch_id*cbo_buyer_name*txt_challan_no*txt_style_ref*hidden_order_id*cbo_store_name*txtNoOfRoll*txtRollNo*txtRollPOid*txtRollPOQnty*txtItemDescription*hiddenProdId*txtIssueQnty*hiddenAvgRate*save_data*all_po_id*distribution_method_id*txtYarnLot*cbo_color_id*cbo_yarn_count*dtls_tbl_id*trans_tbl_id*txt_stitch_length*txt_remarks*cbo_floor_hidden*cbo_room_hidden*txt_rack_hidden*txt_shelf_hidden*txt_program_no*txt_service_booking_no*hidden_is_sales*store_update_upto";
		
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
		// alert (data);return;
		freeze_window(operation);
		http.open("POST","requires/grey_fabric_issue_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_grey_fabric_issue_entry_reponse;
	}
}

function fnc_grey_fabric_issue_entry_reponse()
{	
	if(http.readyState == 4) 
	{	  		
		var reponse=trim(http.responseText).split('**');	
 		
		show_msg(reponse[0]);
		if(reponse[0]*1==20*1)
		{
			release_freezing();
			alert(reponse[1]);
			return;
		}
		else if(reponse[0]==10 || reponse[0]==15)
		{
			release_freezing();
			return;
		}
		else if(reponse[0]==0 || reponse[0]==1) //insert
		{
 			//show_msg(reponse[0]);
			$("#txt_system_no").val(reponse[1]); 
			$('#hidden_system_id').val(reponse[2]);	

			$('#cbo_company_id').attr('disabled','disabled');
			$('#cbo_basis').attr('disabled','disabled');
			$('#cbo_issue_purpose').attr('disabled','disabled');
			$('#cbo_dyeing_source').attr('disabled','disabled');
			$('#cbo_dyeing_com').attr('disabled','disabled');
			$('#txt_booking_no').attr('disabled','disabled');

			show_list_view(reponse[2],'show_dtls_list_view','list_view_container','requires/grey_fabric_issue_controller','');
			set_button_status(0, permission, 'fnc_grey_fabric_issue_entry',1,1);
			//after save reset child form
			var issuePurpose=$("#cbo_issue_purpose").val();
			if(issuePurpose!=8)
			{
				//$("#color_td").html('<?echo create_drop_down( "cbo_color_id", 170, $blank_array,"", 1, "-- Select Color --", $selected, "","","" ); ?>');
			}
			
			$("#child_tbl").find('input,select').val('');
			$("#display").find('input,select').val('');
			$("#dtls_tbl_id").val(''); 
			$("#trans_tbl_id").val('');					
		}	
			 	
		release_freezing();
	}
}

function open_mrrpopup()
{
	if( form_validation('cbo_company_id','Company Name')==false )
	{
		return;
	}
	
	var company = $("#cbo_company_id").val();	
	var page_link='requires/grey_fabric_issue_controller.php?action=mrr_popup&company='+company; 
	var title="Search MRR Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=0,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var sysNumber=this.contentDoc.getElementById("hidden_sys_number").value; // system number
		var posted_in_account=this.contentDoc.getElementById("hidden_posted_account").value; // posted in accounce
 		var hidden_id=this.contentDoc.getElementById("hidden_id").value; // posted in accounce
		
		$("#txt_system_no").val(sysNumber);		
		// master part call here
		$("#is_posted_accout").val(posted_in_account);
		if(posted_in_account==1) document.getElementById("accounting_posted_status").innerHTML="Already Posted In Accounting.";
		else 					 document.getElementById("accounting_posted_status").innerHTML="";
		
		get_php_form_data(hidden_id, "populate_data_from_data", "requires/grey_fabric_issue_controller");	 
		//list view call here
		show_list_view($("#hidden_system_id").val(),'show_dtls_list_view','list_view_container','requires/grey_fabric_issue_controller','');
 		$("#child_tbl").find('input,select').val('');
		$("#display").find('input,select').val('');
		
		var issuePurpose=$("#cbo_issue_purpose").val();
		if(issuePurpose!=8)
		{
			//$("#color_td").html('<?echo create_drop_down( "cbo_color_id", 170, $blank_array,"", 1, "-- Select Color --", $selected, "","","" ); ?>');
		}
		
		set_button_status(0, permission, 'fnc_grey_fabric_issue_entry',1,0);
		//enable_disable();
  	}
}

//form reset/refresh function here
function fnResetForm()
{ 
	//disable_enable_fields( 'cbo_company_id*cbo_basis*cbo_receive_purpose*cbo_store_name', 0, "", "" );
 	set_button_status(0, permission, 'fnc_grey_fabric_issue_entry',1,0);
	reset_form('grey_issue_1','list_view_container','','','','');
	document.getElementById("accounting_posted_status").innerHTML="";
	disable_enable_fields('cbo_company_id',0);
	//$("#color_td").html('<?echo create_drop_down( "cbo_color_id", 170, $blank_array,"", 1, "-- Select Color --", $selected, "","","" ); ?>');
	$("#cbo_issue_purpose").val(11);
	enable_disable();
}

$(document).ready(function(e) {
    $("#cbo_issue_purpose").val(11); //default set issue purpose fabric dyeing 
	enable_disable();
});

function set_form_data(data)
{
	var data=data.split("**");
	$('#cbo_floor_hidden').val(data[0]);
	$('#cbo_room_hidden').val(data[1]);
	$('#txt_rack_hidden').val(data[2]);
	$('#txt_shelf_hidden').val(data[3]);
	$('#cbo_floor').val(data[4]);
	$('#cbo_room').val(data[5]);
	$('#txt_rack').val(data[6]);
	$('#txt_shelf').val(data[7]);	
}


function openmypage_service_booking(page_link,title)
{
	var company=$("#cbo_company_id").val()*1;

	var hidden_order_id=$("#hidden_order_id").val()*1;
	var hidden_is_sales=$("#hidden_is_sales").val()*1;

	var fso_id="";
	if(hidden_is_sales==1)
	{
		fso_id= hidden_order_id;
	}
	
	//if( form_validation('cbo_company_id*cbo_basis*cbo_issue_purpose*txt_booking_no','Company Name*Issue Basis*Issue Purpose*Booking No')==false )
	if( form_validation('cbo_company_id*cbo_basis*cbo_issue_purpose*txt_order_no','Company Name*Issue Basis*Issue Purpose*Order No')==false )
	{
		return;
	}
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link+'&company='+company+'&fso_id='+fso_id, title, 'width=995px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");
		if (theemail.value!="")
		{
			$('#txt_service_booking_no').val(theemail.value);
			reset_product_info();
		}
	}
}

//for func_multiple_issue_no_print
function func_multiple_issue_no_print()
{
	if( form_validation('cbo_company_id*txt_system_no','Company Name*System Number')==false )
	{
		return;
	}
	//cbo_basis
	//cbo_issue_purpose
	
	var company 	  = $("#cbo_company_id").val();
	var issue_basis   = $("#cbo_basis").val();
	var issue_purpose = $("#cbo_issue_purpose").val();
	var dyeing_source = $("#cbo_dyeing_source").val();

	var page_link='requires/grey_fabric_issue_controller.php?action=multiple_issue_no_popup&company='+company+'&issue_basis='+issue_basis+'&issue_purpose='+issue_purpose+'&dyeing_source='+dyeing_source; 
	var title="Search Issue No Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=410px,center=1,resize=0,scrolling=0',' ')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		//var returnNumber=this.contentDoc.getElementById("hidden_return_number").value;
		var issue_id=this.contentDoc.getElementById("hnd_issue_id").value;
		var report_title=$( "div.form_caption" ).html();
		print_report( issue_id +'*'+$('#cbo_company_id').val(), 'multiple_issue_no_print', 'requires/grey_fabric_issue_controller' );
		return;
	}
}

function reset_product_info()
{
	$("#txtItemDescription").val("");
	$("#hiddenProdId").val("");

	$('#txtYarnLot').val("");
	$('#cbo_yarn_count').val(0);
	//set_multiselect('cbo_yarn_count','0','1',0,'0');
	$('#txt_rack_hidden').val(0);
	$('#txt_shelf_hidden').val(0);
	$('#txt_global_stock').val(0);
	$('#txt_stitch_length').val("");
	$('#hiddenAvgRate').val("");
	$('#cbo_floor_hidden').val(0);
	$('#cbo_room_hidden').val(0);
	$('#txtIssueQnty').val("");

	$('#cbo_floor').val("");
	$('#cbo_room').val("");
	$('#txt_rack').val("");
	$('#txt_shelf').val("");
	$('#txt_fabric_received').val('');
	$('#txt_cumulative_issued').val('');
	$('#txt_yet_to_issue').val('');
	$('#txt_global_stock').val('');
}

</script>
</head>

<body onLoad="set_hotkey()">
	<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../",$permission);  ?>  		 
    <form name="grey_issue_1" id="grey_issue_1" autocomplete="off" > 
    	<div style="width:100%;" align="center">  
            <fieldset style="width:1000px;">
                <legend>Grey Fabric Issue</legend>
                   <!-- ========================== Master table start ============================ -->     
                       <fieldset style="width:950px;">                                       
                            <table  width="950" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                                <tr>
                                    <td colspan="6" align="center"><b>Issue No&nbsp;</b>
                                        <input type="text" name="txt_system_no" id="txt_system_no" class="text_boxes" style="width:160px" placeholder="Double Click To Search" onDblClick="open_mrrpopup()" readonly />&nbsp;&nbsp;
                                    	<input type="hidden" id="hidden_system_id" /> 
                                    </td>
                               </tr>
                               <tr>
                                    <td  width="120" align="right" class="must_entry_caption">Company Name </td>
                                    <td width="170">
                                        <?  		 
                                        echo create_drop_down( "cbo_company_id", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and comp.core_business not in(3)  $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "get_php_form_data(this.value, 'is_roll_maintain', 'requires/grey_fabric_issue_controller');reset_form('','','txt_booking_no*txt_batch_no*txt_batch_id*txt_batch_color*cbo_buyer_name*txt_order_no','','','');enable_disable();$('#child_tbl').find('input,select').val('');load_drop_down( 'requires/grey_fabric_issue_controller', this.value, 'load_drop_down_store', 'store_td' );get_php_form_data( this.value, 'company_wise_report_button_setting','requires/grey_fabric_issue_controller' );" );
                                        //load_room_rack_self_bin('requires/grey_fabric_issue_controller*13', 'store','store_td', this.value);
                                        ?>
                                        
                                        <!-- hiden field for check start-->
                                        <input type="hidden" id="hidden_is_roll_maintain" >
                                        <input type="hidden" id="hidden_is_rack_balance" >
                                        <input type="hidden" id="hidden_is_batch_maintain" >
                                        <input type="hidden" id="store_update_upto" >
                                        <!-- hiden field for check end -->
                                        
                                    </td>
                                    <td width="120" align="right" class="must_entry_caption">Issue Date</td>
                                    <td width="160"><input type="text" name="txt_issue_date" id="txt_issue_date" class="datepicker" style="width:160px;" value="<? echo date('d-m-Y'); ?>" placeholder="Select Date"/></td>
                                    <td width="120" align="right" class="must_entry_caption">Issue Basis</td>
                                    <td width="" >
										<? 
											$grey_issue_basis=array(1=>"Booking",2=>"Independent",3=>"Knitting Plan");
                                            echo create_drop_down( "cbo_basis", 170, $grey_issue_basis,"", 1, "-- Select Basis --", $selected, "reset_form('','','txt_booking_id*txt_booking_no*txt_batch_no*txt_batch_id*cbo_buyer_name*txt_style_ref*txt_order_no*hidden_order_id*txtReqQnty*hiddenIssueQnty*hiddenAvgRate*save_data*txtIssueQnty*all_po_id*distribution_method_id*txt_fabric_received*txt_cumulative_issued*txt_yet_to_issue*hidden_yet_issue_qnty*hiddenProdId*txtItemDescription*txt_stitch_length*txtYarnLot*cbo_yarn_count*txt_rack_hidden*txt_shelf_hidden*show_textcbo_yarn_count','','','');enable_disable();", "", "1,2,3");
                                        ?>
                                    </td>
                                </tr>
                                <tr>                           
                                    <td width="120" align="right" class="must_entry_caption">Issue Purpose </td>
                                    <td width="170">
										<? 
                                         	echo create_drop_down( "cbo_issue_purpose", 170, $yarn_issue_purpose,"", 1, "-- Select Purpose --", $selected, "reset_form('','','txt_booking_no*txt_batch_no*cbo_buyer_name*txt_order_no*cbo_color_id*txtItemDescription*txtIssueQnty','','','');enable_disable()","","11,3,4,8,26,29,30,31" );
                                        ?>
                                    </td>
                                    <td width="120" align="right" >Dyeing Source</td>
                                    <td width="160"><?
                                        echo create_drop_down( "cbo_dyeing_source", 172, $knitting_source, "", 1, "-- Select --", $selected, "load_drop_down( 'requires/grey_fabric_issue_controller', this.value+'**'+$('#cbo_company_id').val()+'**'+$('#cbo_issue_purpose').val(), 'load_drop_down_knit_com', 'dyeing_company_td' );","","1,3" );
                                    ?></td>
                                    <td width="120" class="must_entry_caption" align="right">Dyeing Company</td>
                                    <td width="" id="dyeing_company_td">
										<?
                                        	echo create_drop_down( "cbo_dyeing_com", 170, $blank_array,"", 1, "-- Select --", $selected, "","","" );
                                    	?>
                                	</td>
                                </tr>
                                <tr>                          
                                    <td width="120" align="right" id="knit_source">Fabric Booking</td>
                                    <td width="170">
                                        <input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:160px"  placeholder="Double Click to Search" onDblClick="popuppage_fabbook();" readonly />
                                        <input type="hidden" name="txt_booking_id" id="txt_booking_id" />
                                    </td>
                                    <td width="120" align="right"> Batch Number</td>
                                    <td width="160">
                                        <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:160px" placeholder="Display" onDblClick="openpopup_batch()" />
                                        <input type="hidden" id="txt_batch_id" />
                                    </td>
                                    <td width="120" align="right">Buyer Name</td>
                                    <td width="" id="supplier">
										<? 
                                            echo create_drop_down( "cbo_buyer_name", 170, "select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select --", "","", 1 );
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                   <td  width="120" align="right" >Challan No</td>
                                   <td width="170">
                                        <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:160px" placeholder="Entry" >
                                   </td>
                                   <td width="120" align="right" >Style Reference</td>
                                   <td width="160">
                                   		<input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:160px" readonly placeholder="Display" />
                                   </td>
                                   <td width="120" align="right">Batch Color</td>
                                   <td width=""><input  type="text" name="txt_batch_color" id="txt_batch_color" class="text_boxes" style="width:160px"  readonly placeholder="Display" disabled /></td>                                   
                              	</tr>
                                <tr>
                                    <td align="right">Order Numbers</td>
                                    <td colspan="3">
                                        <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:455px" readonly placeholder="Display" />
                                        <input type="hidden" id="hidden_order_id" />
                                        <input type="hidden" name="hidden_is_sales" id="hidden_is_sales" />
                                    </td>
                                    <td align="right">Service Booking No</td>
                                    <td>
                                    <input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_service_booking('requires/grey_fabric_issue_controller.php?action=service_booking_popup','Service Booking Search')" readonly placeholder="Double Click for Booking" name="txt_service_booking_no" id="txt_service_booking_no" />
                                    </td>
                                 </tr>
                            </table>
                        </fieldset> 
              <fieldset style="width:450px; margin-left:30px; position:relative; float:left">  
                <legend>Issued New  Item</legend>                                     
                	<table width="400" cellspacing="2" cellpadding="0" border="0" id="child_tbl" > 
                    	<tr>  		
                      		<td>Program No.</td>
                                <td>
                                    <input name="txt_program_no" id="txt_program_no" class="text_boxes" style="width:160px"  placeholder="Double Click to Search" onDblClick="popuppage_fabbook();" disabled readonly />
                                    
                                </td>  
                           	</tr>                                 
                            <tr>                                
                            	 <td width="130" class="must_entry_caption">Store Name</td>
                                 <td id="store_td">
								 		<? 
                                        	echo create_drop_down( "cbo_store_name", 170, $blank_array,"", 1, "-- Select Store --", $storeName, "" );
                                        ?>
                                 </td>
                            </tr>
                            <tr>                                
                            	 <td width="">No Of Roll</td>
                                 <td width="">
                                 	<input  type="text" name="txtNoOfRoll" id="txtNoOfRoll" class="text_boxes_numeric" style="width:160px" placeholder="Write No of Roll" />
                                 	<!-- hidden field for roll table entry very very important -->
                                    <input type="hidden" name="txtRollNo" id="txtRollNo" value="" readonly disabled />
                                    <input type="hidden" name="txtRollPOid" id="txtRollPOid" value="" readonly disabled />
                                    <input type="hidden" name="txtRollPOQnty" id="txtRollPOQnty" value="" readonly disabled />
                                    <!-- end -->
                                 </td>
                            </tr> 
                            <tr>
                                <td class="must_entry_caption">Item Description</td>
                                <td>
                                	<input type="text" name="txtItemDescription" id="txtItemDescription" class="text_boxes" style="width:280px" placeholder="Double Click" onDblClick="openDescription_popup()" readonly />
                                    <input type="hidden" name="hiddenProdId" id="hiddenProdId" />
                                 </td>
                            </tr>
                            <tr>
                                <td class="must_entry_caption">Issue Quantity</td>
                                <td>
                                	<input type="hidden" name="txtReqQnty" id="txtReqQnty" class="text_boxes_numeric" />
                                    <input type="hidden" name="hiddenIssueQnty" id="hiddenIssueQnty" class="text_boxes_numeric" />
                                    <input type="hidden" name="hiddenAvgRate" id="hiddenAvgRate" class="text_boxes_numeric" />
                                    <input type="text" name="txtIssueQnty" id="txtIssueQnty" class="text_boxes_numeric" style="width:160px" onDblClick="issueQntyPopup()" readonly />
                                </td>
                            </tr>
                            <tr>
                                <td>Fabric Color</td>
                                <td id="color_td">
									<? 
										echo create_drop_down( "cbo_color_id", 170, $blank_array,"", 0, "-- Select Color --", $selected, "","","" );
									?>
                                 </td>
                            </tr>
                            <tr>
                            	<td>Stitch Length</td>
                                <td>
                                    <input type="text" name="txt_stitch_length" id="txt_stitch_length" class="text_boxes" style="width:160px;" placeholder="Display" disabled/>
                                </td>
                            </tr>
                            <tr>
                                <td>Yarn Lot</td>
                                <td><input  type="text" name="txtYarnLot" id="txtYarnLot" class="text_boxes" style="width:160px" placeholder="Display" readonly disabled /></td>
                            </tr>
                            <tr>
                                <td>Yarn Count</td>
                                <td>
                                	<?
										echo create_drop_down( "cbo_yarn_count", 170, "select id,yarn_count from lib_yarn_count where is_deleted = 0 AND status_active = 1 ORDER BY yarn_count ASC","id,yarn_count", 0, "--Select--", "", "",1 );
									?>
                                    	<!-- important hidden field --> 
                                             <input type="hidden" name="save_data" id="save_data" readonly  />	
                                             <input type="hidden" name="all_po_id" id="all_po_id" readonly />
                                             <input type="hidden" name="distribution_method_id" id="distribution_method_id" readonly />
                                        <!-- important hidden field -->
                                </td>
                            </tr>
                            <tr>
                            	<td>Floor</td>
								<td id="floor_td">
									<input type="text" name="cbo_floor" id="cbo_floor" class="text_boxes" style="width:160px" placeholder="Display" readonly disabled>
                                    	<input type="hidden" name="cbo_floor_hidden" id="cbo_floor_hidden" class="text_boxes" style="width:160px" placeholder="Display" readonly disabled>
								</td>
							</tr> 
							<tr>
                            	<td>Room</td>
								<td id="room_td">
									<input type="text" name="cbo_room" id="cbo_room" class="text_boxes" style="width:160px" placeholder="Display" readonly disabled>
                                    	<input type="hidden" name="cbo_room_hidden" id="cbo_room_hidden" class="text_boxes" style="width:160px" placeholder="Display" readonly disabled>
								</td>
							</tr> 
                            <tr>
                            	<td>Rack</td>
                                	<td id="rack_td">
										 <input type="text" name="txt_rack" id="txt_rack" class="text_boxes" style="width:160px" placeholder="Display" readonly disabled>
                                    	<input type="hidden" name="txt_rack_hidden" id="txt_rack_hidden" class="text_boxes" style="width:160px" placeholder="Display" readonly disabled>
                                </td>
                            </tr>
                            <tr>
                            	<td>Shelf</td>
                                <td id="shelf_td">
									<input type="text" name="txt_shelf" id="txt_shelf" class="text_boxes" style="width:160px" placeholder="Display" readonly disabled>
                                    <input type="hidden" name="txt_shelf_hidden" id="txt_shelf_hidden" class="text_boxes_numeric" style="width:160px" placeholder="Display" readonly disabled>
                                </td>
                            </tr>
                            <tr>
                                <td>Remarks</td>
                                <td><input  type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:280px"  /></td>
                            </tr>
                      </table>
                </fieldset>
                
             <fieldset style="width:450px; float:left; margin-left:30px">  
                <legend>Display</legend>                                     
                      <table  width="350" cellspacing="2" cellpadding="0" border="0" id="display" >                           
                            <tr>
                                  <td>Fabric Received</td>
                                  <td width="100"><input  type="text" name="txt_fabric_received" id="txt_fabric_received" class="text_boxes" style="width:160px" readonly disabled  /></td>
                            </tr>                        
                            <tr>
                                <td>Cumulative Issued</td>
                                <td><input  type="text" name="txt_cumulative_issued" id="txt_cumulative_issued" class="text_boxes" style="width:160px"  readonly disabled /></td>
                            </tr>
                            <tr>
                                <td>Yet to Issue</td>
                                <td width="100">
                                    <input  type="text" name="txt_yet_to_issue" id="txt_yet_to_issue" class="text_boxes" style="width:160px"  readonly disabled />
                                    <input type="hidden" id="hidden_yet_issue_qnty" readonly disabled  />
                                </td>
                            </tr> 
                            <tr>
                                <td>Global Stock</td>
                                <td><input type="text" name="txt_global_stock" id="txt_global_stock" placeholder="Display" class="text_boxes" style="width:160px" disabled /></td>
                            </tr>
                      </table>
                      
                </fieldset>   
                <div style="clear:both"></div>
                   <!-- ========================== Master table end ============================ -->     
                    
                    
                   <!-- ========================== Child table start ============================ -->                                      
                    <table cellpadding="0" cellspacing="1" width="100%">
                        <tr> 
                           <td colspan="6" align="center"></td>				
                        </tr>
                        <tr>
                            <td align="center" colspan="6" valign="middle" class="button_container">
                                 <!-- details table id for update -->                             
                                 <input type="hidden" id="dtls_tbl_id" name="dtls_tbl_id" readonly />
                                 <input type="hidden" id="trans_tbl_id" name="trans_tbl_id" readonly />
                                 <input type="hidden" id="update_id" name="update_id" readonly />
                                 <input type="hidden" name="is_posted_accout" id="is_posted_accout"/>
                                 <!-- -->
                                 <? echo load_submit_buttons( $permission, "fnc_grey_fabric_issue_entry", 0,0,"fnResetForm()",1);?>
                                 <input type="button" name="print_1" id="print_1" value="Print" onClick="fnc_grey_fabric_issue_entry(4)" style="width:100px; display:none" class="formbutton" />
                                  <input type="button" name="print_2" id="print_2" value="Print 2" onClick="fnc_grey_fabric_issue_entry(6)" style="width:100px; display:none" class="formbutton" />
                                  <input type="button" name="print_3" id="print_3" value="Print 3" onClick="fnc_grey_fabric_issue_entry(7)" style="width:100px;display:none" class="formbutton" />
                                  <input type="button" name="print_4" id="print_4" value="Print 4" onClick="fnc_grey_fabric_issue_entry(8)" style="width:100px;display:none" class="formbutton" />
                                 <input type="button" name="print_vat" id="print_vat" value="Print With VAT" onClick="fnc_grey_fabric_issue_entry(5)" style="width:100px;display:none" class="formbutton" />
                                  <input type="button" name="print_sb" id="print_sb" value="Print SB" onClick="fnc_grey_fabric_issue_entry(9)" style="width:100px;display:none" class="formbutton" />
								  <input type="button" name="multiple_issue_no_print" value="Print Multi Issue No" id="multiple_issue_no_print" class="formbutton" style="width: 120px;display:none" onClick="func_multiple_issue_no_print()"/> 
                                 <div id="accounting_posted_status" style=" color:red; font-size:24px;" align="left"></div>
                            </td>
                       </tr> 
                    </table>                 
                    </fieldset>              	
                  <!-- ========================== Child table end ============================ -->   

    			<div style="width:990px; margin-top:5px" id="list_view_container"></div>

    		</div>
		</form>
	</div>    
</body>  
<script>
	set_multiselect('cbo_yarn_count*cbo_color_id','0*0','0*0','','0*0');
	disable_enable_fields('show_textcbo_yarn_count','1','','');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
