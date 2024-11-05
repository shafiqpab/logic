<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Out Order Entry

Functionality	:	
JS Functions	:
Created by		:	Ashraful 
Creation date 	: 	01-10-2013
Updated by 		: 	Rakib	
Update date		: 	30-12-2021		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where status_active=1", "id", "supplier_name" );
$supplier_arr=json_encode($supplier_arr);

//========== user credential start ========

$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];
$location_id = $userCredential[0][csf('location_id')];

$company_credential_cond = '';
if ($company_id >0) {
    $company_credential_cond = "and comp.id in($company_id)";
}

if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)";
}

if($item_cate_id !='') {
    $item_cate_credential_cond = $item_cate_id;
}
//========== user credential end ==========

//-------------------------------------------------------------------------------
echo load_html_head_contents("Gate Pass Entry","../", 1, 1, $unicode,1,1); 
//print_r($_SESSION['logic_erp']['data_arr'][251]);
?>
<script>
	var permission='<? echo $permission; ?>';
	var update_id=$("#update_id").val();
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	<?
		$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][251] );
		echo "var field_level_data= ". $data_arr . ";\n";
	?>

	function check_within_group_status(within)
	{
		load_drop_down( 'requires/get_pass_entry_controller', within+'_'+document.getElementById('cbo_basis').value+'_'+document.getElementById('txt_chalan_no').value, 'load_drop_down_sent', 'sent_td');
		load_drop_down( 'requires/get_pass_entry_controller',within+'_'+document.getElementById('txt_sent_to').value, 'load_drop_down_location', 'location_td' );		
	}

	function openmypage_sent_to()
	{
		var txt_sent_to = $('#hidden_sent_to_name').val();
		var cbo_basis = $('#cbo_basis').val();
		var cbo_group = $('#cbo_group').val();
		var title = 'Supplier Selection Form';	
		//var page_link = 'requires/get_pass_entry_controller.php?txt_sent_to='+txt_sent_to+'&action=sent_to_popup';
		page_link='requires/get_pass_entry_controller.php?action=chalan_id_popup&txt_sent_to='+txt_sent_to+'&cbo_basis='+cbo_basis+'&cbo_group='+cbo_group+'&action=sent_to_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var txt_sent_to=this.contentDoc.getElementById("hidden_sent_to_id").value;	 //Access form field with id="emailfield"
			var supplier_name=this.contentDoc.getElementById("hidden_sent_to_name").value;
			$('#txt_sent_to').val(supplier_name);
			$('#hidden_sent_to_name').val(txt_sent_to);
			set_all();
		}
	}

	function auto_load_sent_to(data)
	{
		let DataArray = data.split(",");

		if(DataArray[0]==2 && DataArray[1]!=22)
		{
			var supplier_arr='<? echo $supplier_arr;?>';
 			var supplier_arr=JSON.parse(supplier_arr); 
			var txt_sent_to = $('#hidden_sent_to_name').val();
 			$('#txt_sent_to').val(supplier_arr[txt_sent_to]);
			$('#hidden_sent_to_name').val(txt_sent_to);
		}
	}
	 
	function sent_to_check(within_type)
	{
		if(within_type==2)
		{
		 	var sent_to = [<? echo substr(return_library_autocomplete( "select sent_to from inv_gate_pass_mst where within_group=2 and status_active=1 and is_deleted=0 group by sent_to", "sent_to" ), 0, -1); ?>];
	
			$(document).ready(function(e)
		 	{
				$("#txt_sent_to").autocomplete({
				     source: sent_to
			    });
		 	});
		    // document.getElementById('td_id_location').innerHTML="To Location";
		    //$('#td_id_location').css('color','black');
		}
		if(within_type==1)
		{
			//document.getElementById('td_id_location').innerHTML="To Location";
			//$('#td_id_location').css('color','blue');
		}
	}

    function senttoAutoComplete(){
        var sentto50 = [<? echo substr(return_library_autocomplete( "select buyer_name as supplier_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name", "supplier_name" ), 0, -1); ?>];
        var sentto = [<? echo substr(return_library_autocomplete( "select supplier_name from lib_supplier where status_active =1 and is_deleted=0 order by supplier_name", "supplier_name" ), 0, -1); ?>];
        var getBasis = $('#cbo_basis').val();
        if($('#cbo_group').val() != 1){
            if(getBasis == 50){
                $('#txt_sent_to').autocomplete({
                    source: sentto50
                });
            }else{
                $('#txt_sent_to').autocomplete({
                    source: sentto
                });
            }
        }
    }

    var sent_by = [<? echo substr(return_library_autocomplete( "select sent_by from inv_gate_pass_mst where  status_active=1 and is_deleted=0 group by sent_by", "sent_by" ), 0, -1); ?>];
	$(document).ready(function(e)
	{
        $("#txt_sent_by").autocomplete({
			source: sent_by
		});
    });
	var attention = [<? echo substr(return_library_autocomplete( "select attention from inv_gate_pass_mst where  status_active=1 and is_deleted=0 group by attention", "attention" ), 0, -1); ?>];
	$(document).ready(function(e)
	{
        $("#txt_attention").autocomplete({
			source: attention
		});
    });
	var carried_by = [<? echo substr(return_library_autocomplete( "select carried_by  from inv_gate_pass_mst where  status_active=1 and is_deleted=0 group by carried_by", "carried_by" ), 0, -1); ?>];
	$(document).ready(function(e)
	{
        $("#txt_carried_by").autocomplete({
			source: carried_by
		});
    });	
	var delivery_company = [<? echo substr(return_library_autocomplete( "select delivery_company from inv_gate_pass_mst where  status_active=1 and is_deleted=0 group by delivery_company", "delivery_company" ), 0, -1); ?>];
	$(document).ready(function(e)
	{
		$("#txt_delivery_company").autocomplete({
			source: delivery_company
		});
	});	

	// var item_description = [<?// echo substr(return_library_autocomplete( "select item_description from inv_gate_pass_dtls where  status_active=1 and is_deleted=0 and item_description is not null group by item_description", "item_description" ), 0, -1); ?>];
	// $(document).ready(function(e)
	// {
	// 	$("#txtitemdescription_1").autocomplete({
	// 		source: item_description,
	// 		minLength: 3			
	// 	});
	// }); 

	function add_break_down_tr( i ) 
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		
		var row_num=$('#tbl_order_details tr').length-1;
		if (row_num!=i)
		{
			return false;
		}
		i++;
		
		var boitemcategory=0;
		var cbouom=0;
		var	boitemcategory=$('#cboitemcategory_'+(i-1)).val();
		var	sample=$('#cbosample_'+(i-1)).val();
		var	cbouom=$('#cbouom_'+(i-1)).val();
		//alert(boitemcategory);
			
		$("#cut_details_container tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			'name': function(_, name) { return name + i },
			'value': function(_, value) { return value }              
			});
		}).end().appendTo("#cut_details_container");
		var k=i-1;
		$('#increase_'+k).hide();
		$('#decrease_'+k).hide();
		//$('#updateiddtls_'+i).val('');
		if($('#cbo_basis').val()==1)
		{  
			$('#cboitemcategory_'+i).val(boitemcategory);
			$('#cbosample_'+i).val(sample);
			$('#txtitemdescription_'+i).val();
			$('#txtquantity_'+i).val();
			$('#txtrejectqty_'+i).val();
			$('#cbouom_'+i).val(cbouom);
			$('#txtrate_'+i).val();
			$('#txtamount_'+i).val();
			$('#txtbag_'+i).val();
			$('#txtcartonqnty_'+i).val();
			$('#txtorder_'+i).val();
			$('#txtorderidhidden_'+i).val();
			$('#txtremarks_'+i).val();
			$('#updatedtlsid_'+i).val('');
		}
		else
		{
			$('#cboitemcategory_'+i).val('');
			$('#cbosample_'+i).val('');
			$('#txtitemdescription_'+i).val('');
			$('#txtquantity_'+i).val('');
			$('#txtrejectqty_'+i).val('');
			$('#cbouom_'+i).val('');
			$('#txtrate_'+i).val('');
			$('#txtamount_'+i).val('');
			$('#txtbag_'+i).val('');
			$('#txtcartonqnty_'+i).val('');
			$('#txtorder_'+i).val('');
			$('#txtorderidhidden_'+i).val('');
			$('#txtremarks_'+i).val('');
			$('#updatedtlsid_'+i).val('');
		}
		set_all_onclick();

		/* $('#txtitemdescription_'+i).autocomplete({ 
			source: item_description,
			minLength: 3
		}); */
		//$('#cbosample_'+i).removeAttr("onchange").attr("onchange","gate_enable_disable("+i+");");
		$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
		$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+','+'"tbl_order_details"'+");");
	}

	function fn_deletebreak_down_tr(rowNo,table_id ) 
	{
		var numRow = $('#'+table_id+' tbody tr').length;
		if(numRow==rowNo && rowNo!=1)
		{
			var k=rowNo-1;
			$('#increase_'+k).show();
			$('#decrease_'+k).show();
			
			$('#'+table_id+' tbody tr:last').remove();
		}
		else return false;		
	}

	function openmypage_system()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();

		page_link='requires/get_pass_entry_controller.php?action=system_id_popup&company='+company;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Gate Pass Id Popup', 'width=1050px, height=350px, center=1, resize=0, scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var sysNumber=(this.contentDoc.getElementById("hidden_sys_number").value).split('_');; // system number
			if(sysNumber!="")
			{
				freeze_window(5);
				$("#txt_system_id").val(sysNumber[1]);				
				get_php_form_data(sysNumber[1], "populate_master_from_data", "requires/get_pass_entry_controller");

				$("#print6").removeClass("formbutton_disabled").addClass("formbutton");
				$("#print9").removeClass("formbutton_disabled").addClass("formbutton");
				$("#print10").removeClass("formbutton_disabled").addClass("formbutton");
				$("#print12").removeClass("formbutton_disabled").addClass("formbutton");
				$("#print15").removeClass("formbutton_disabled").addClass("formbutton");
				$("#id_print_to_button_Scandex").removeClass("formbutton_disabled").addClass("formbutton");
				$("#print_mercer").removeClass("formbutton_disabled").addClass("formbutton");
				$("#print_16").removeClass("formbutton_disabled").addClass("formbutton");
				$("#print_17").removeClass("formbutton_disabled").addClass("formbutton");
				$("#print14").removeClass("formbutton_disabled").addClass("formbutton");
				$("#id_print_to_button").removeClass("formbutton_disabled").addClass("formbutton");
				$("#with_color_size_print").removeClass("formbutton_disabled").addClass("formbutton");
				$("#Printt1").removeClass("formbutton_disabled").addClass("formbutton");		
				$("#id_print_to_button4").removeClass("formbutton_disabled").addClass("formbutton");
				$("#id_print_to_button5").removeClass("formbutton_disabled").addClass("formbutton");
				$("#id_print_to_button6").removeClass("formbutton_disabled").addClass("formbutton");
				$("#id_print_to_button7").removeClass("formbutton_disabled").addClass("formbutton");
				$("#id_print_to_button8").removeClass("formbutton_disabled").addClass("formbutton");
				$("#id_print_to_button9").removeClass("formbutton_disabled").addClass("formbutton");
				$("#id_print_to_button10").removeClass("formbutton_disabled").addClass("formbutton");
				$("#id_print_to_button11").removeClass("formbutton_disabled").addClass("formbutton");
				$("#id_print_to_button14").removeClass("formbutton_disabled").addClass("formbutton");
				$("#id_print_to_button15").removeClass("formbutton_disabled").addClass("formbutton");
				$("#id_print_to_button18").removeClass("formbutton_disabled").addClass("formbutton");
				$("#id_print_to_button19").removeClass("formbutton_disabled").addClass("formbutton");
				$("#id_print_to_button20").removeClass("formbutton_disabled").addClass("formbutton");
				$("#id_print_to_button16").removeClass("formbutton_disabled").addClass("formbutton");
				$("#id_print_to_button12").removeClass( "formbutton_disabled").addClass("formbutton");
				$("#print13").removeClass("formbutton_disabled").addClass("formbutton");
				$("#with_challan").removeClass("formbutton_disabled").addClass("formbutton");
				$("#printamt").removeClass("formbutton_disabled").addClass("formbutton");
				$("#printlibas").removeClass("formbutton_disabled").addClass("formbutton");
				$("#print11").removeClass("formbutton_disabled").addClass("formbutton");

				show_list_view(sysNumber[0],'show_update_list_view','cut_details_container','requires/get_pass_entry_controller','');
				set_button_status(1, permission, 'fnc_getpass_entry',1,1);
				$("#cbo_company_name").attr("disabled",true);
				$("#cbo_basis").attr("disabled",true);
				//$("#txt_chalan_no").attr("disabled",true);
				$("#cbo_group").attr("disabled",true);
				$("#txt_sent_to").attr("disabled",true);
				var basis=$("#cbo_basis").val()*1;
				var returnable=$("#cbo_returnable").val()*1;

				if(returnable==1)
				{
					$("#txt_returnable_gate_pass").attr("disabled",true);
					$("#returnable_item_dtls").removeClass( "formbutton_disabled").addClass("formbutton");
				}
				else
				{
					$("#returnable_item_dtls").removeClass("formbutton").addClass("formbutton_disabled");
				}

				if (basis == 50) var row_num=$('#tbl_order_details tbody tr:not(#total_display_last_tr)').length;
				else var row_num=$('#tbl_order_details tbody tr').length;
				//var row_num=$('#tbl_order_details tbody tr').length;
				if (basis!=1)
				{
					for(var j=1;j<=row_num;j++)
					{ 
						//alert(response[3]);
						$("#cboitemcategory_"+j).attr("disabled",true);						
						var sam=$("#cbosample_"+j).val();
						$('#cbosample_'+j).attr('disabled','disabled');						
						$("#txtitemdescription_"+j).attr("disabled",true);
						$("#txtquantity_"+j).attr("disabled",true);
						$("#txtrejectqty_"+j).attr("disabled",true);
						$("#cbouom_"+j).attr("disabled",true);
						$("#txtrate_"+j).attr("disabled",true);
						$("#txtamount_"+j).attr("disabled",true);
						$("#txtorder_"+j).attr("disabled",true);						
					}
				}
				else if(basis==1) 
				{
					//set_all_onclick();
					for(var j=1;j<=row_num;j++)
					{ 
						var itemCat=$("#cboitemcategory_"+j).val();
						var sam=$("#cbosample_"+j).val();
						if(itemCat!=0)
						{
							$('#cbosample_'+j).attr('disabled','disabled');
						}
						else if(sam!=0)
						{
							$('#cboitemcategory_'+j).attr('disabled','disabled');
						}

						/* $('#txtitemdescription_'+j).autocomplete({ 
							source: item_description,
							minLength: 3
						}); */
					}
				}
				release_freezing();
			}
		}
	}

	function openmypage_order(id)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();
		var id=id.split('_');
		page_link='requires/get_pass_entry_controller.php?action=system_order&company='+company;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Gate Out Id Popup', 'width=900px, height=350px, center=1, resize=0, scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var sysNumber=this.contentDoc.getElementById("hidden_order_number").value; // system number
			var sysNumber=sysNumber.split('_');
			//alert(sysNumber); 57_34399
			if(sysNumber!="")
			{
				$("#txtorder_"+id[1]).val(sysNumber[0]);
				$("#txtorderidhidden_"+id[1]).val(sysNumber[1]);
				//alert($("#txtorderidhidden_"+id[1]).val());
			}
		}
	}
	
	//Chalan or system id
	function chalan_popup()
	{
		if( form_validation('cbo_company_name*cbo_basis','Company Name*Basis')==false )
		{
			return;
		}
		var cbo_roll_by = $("#cbo_roll_by").val();
		var e = $('#cbo_roll_by');
		if(e.is(':disabled')==true){var disabled=1;}else{var disabled=0;}
		var company = $("#cbo_company_name").val();
		var basis= $("#cbo_basis").val();
		var variable_roll= $("#variable_roll").val();
		
		var txt_hidden_challan_id = $('#txt_issue_no').val();
		var update_id = $('#update_id').val();
		
		//alert(txt_hidden_challan_id);
		page_link='requires/get_pass_entry_controller.php?action=chalan_id_popup&company='+company+'&basis='+basis+'&cbo_roll_by='+cbo_roll_by+'&disabled='+disabled+'&variable_roll='+variable_roll+'&txt_hidden_challan_id='+txt_hidden_challan_id+'&update_id='+update_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'System ID/Challan No', 'width=1150px, height=420px, center=1, resize=0, scrolling=0','');
		emailwindow.onclose=function()
		{
			freeze_window(5);
			var theform=this.contentDoc.forms[0];
			var sysNumber=(this.contentDoc.getElementById("hidden_sys_number").value).split('_'); // system number
			//var theemailid=this.contentDoc.getElementById("txt_po_id");
			//var theemailval=this.contentDoc.getElementById("txt_po_val");
			var basis= $("#cbo_basis").val();
			
			if( basis==24 || basis==61)
			{
				if(sysNumber!="")
				{
					$("#txt_chalan_no").val(sysNumber[1]);
					$("#txt_issue_no").val(sysNumber[0]);
					var basis=$("#cbo_basis").val();
					get_php_form_data(sysNumber[1]+'_'+basis+'__'+sysNumber[3], "populate_sent_data", "requires/get_pass_entry_controller" );
					show_list_view(sysNumber[1]+'_'+basis+'_'+sysNumber[2]+'_'+sysNumber[3]+'_'+sysNumber[0],'show_dtls_list_view','cut_details_container','requires/get_pass_entry_controller','');					
				}
				release_freezing();
			}	
			else if( basis==23 )	
			{
				
				if(sysNumber!="")
				{
					var theemailid=this.contentDoc.getElementById("txt_po_id");
			     	var theemailval=this.contentDoc.getElementById("txt_po_val");
					
					$("#txt_chalan_no").val(theemailval.value);
					$("#txt_issue_no").val(sysNumber[0]);
					var basis=$("#cbo_basis").val();
					get_php_form_data(theemailval.value+'_'+basis+'__'+sysNumber[3], "populate_sent_data", "requires/get_pass_entry_controller" );
					show_list_view(theemailval.value+'_'+basis+'_'+sysNumber[2]+'_'+sysNumber[3]+'_'+sysNumber[0],'show_dtls_list_view','cut_details_container','requires/get_pass_entry_controller','');					
				}
				release_freezing();
				
			}
			else if( basis==25 )	
			{
				
					var theemailid=this.contentDoc.getElementById("txt_po_id");
			     	var theemailval=this.contentDoc.getElementById("txt_po_val");

				   var rolltype=this.contentDoc.getElementById("txt_rolltype_id").value;
					var basis=$("#cbo_basis").val();
					$("#txt_chalan_no").val(theemailval.value);
					get_php_form_data(theemailval.value+'_'+basis, "populate_sent_data", "requires/get_pass_entry_controller" );
					show_list_view(theemailval.value+'_'+basis+'_'+rolltype,'show_dtls_list_view','cut_details_container','requires/get_pass_entry_controller','');					
				
				release_freezing();
				
			}
			else if(basis==2 || basis==21 || basis==22 || basis==9 || basis==28 || basis==57 || basis==3 || basis==4 || basis==11 || basis==50 || basis==54 || basis==63 || basis==68 || basis==38)
			{
				var theemailid=this.contentDoc.getElementById("txt_po_id");
				var theemailval=this.contentDoc.getElementById("txt_po_val");
				var rolltype=this.contentDoc.getElementById("txt_rolltype_id").value;
				
				//alert(rolltype.value);
				if (theemailid.value!="" || theemailval.value!="")
				{
					$("#txt_chalan_no").val(theemailval.value);
					$("#txt_issue_no").val(theemailid.value);
					//check_within_group_status(rolltype);
					get_php_form_data(theemailid.value+'_'+basis, "populate_sent_data", "requires/get_pass_entry_controller" );
					show_list_view(theemailid.value+'_'+basis+'_'+rolltype,'show_dtls_list_view','cut_details_container','requires/get_pass_entry_controller','');					
				}
				release_freezing();
			}
			else if(basis==8)
			{
				var theemailid=this.contentDoc.getElementById("txt_po_id");
				var theemailval=this.contentDoc.getElementById("txt_po_val");
				var rolltype=this.contentDoc.getElementById("txt_rolltype_id").value;
				
				//alert(rolltype.value);
				if (theemailid.value!="" || theemailval.value!="")
				{
					$("#txt_chalan_no").val(theemailval.value);
					$("#txt_issue_no").val(theemailid.value);
					//check_within_group_status(rolltype);
					get_php_form_data(theemailval.value+'_'+basis, "populate_sent_data", "requires/get_pass_entry_controller" );
					show_list_view(theemailid.value+'_'+basis+'_'+rolltype,'show_dtls_list_view','cut_details_container','requires/get_pass_entry_controller','');					
				}
				release_freezing();
			}				
			else if (basis==56) //Wash Material Receive Return
			{
				if(sysNumber!="")
				{
					$("#txt_chalan_no").val(sysNumber[1]);
					//$("#txt_issue_no").val(sysNumber[0]);
					var basis=$("#cbo_basis").val();
					get_php_form_data(sysNumber[1]+'_'+basis, "populate_sent_data", "requires/get_pass_entry_controller" );
					show_list_view(sysNumber[1]+'_'+basis+'_'+sysNumber[0],'show_dtls_list_view','cut_details_container','requires/get_pass_entry_controller','');					
				}
				release_freezing();
			}
			else if (basis==64) //Subcon Material Receive Return
			{
				// if(sysNumber!="")
				// {
				// 	$("#txt_chalan_no").val(sysNumber[1]);
				// 	//$("#txt_issue_no").val(sysNumber[0]);
				// 	var basis=$("#cbo_basis").val();
				// 	get_php_form_data(sysNumber[1]+'_'+basis, "populate_sent_data", "requires/get_pass_entry_controller" );
				// 	show_list_view(sysNumber[1]+'_'+basis+'_'+sysNumber[0],'show_dtls_list_view','cut_details_container','requires/get_pass_entry_controller','');					
				// }
				// release_freezing();

				var theemailid=this.contentDoc.getElementById("txt_po_id");
				var theemailval=this.contentDoc.getElementById("txt_po_val");
				var rolltype=this.contentDoc.getElementById("txt_rolltype_id").value;
				
				//alert(rolltype.value);
				if (theemailid.value!="" || theemailval.value!="")
				{
					$("#txt_chalan_no").val(theemailval.value);
					$("#txt_issue_no").val(theemailid.value);
					//check_within_group_status(rolltype);
					get_php_form_data(theemailval.value+'_'+basis, "populate_sent_data", "requires/get_pass_entry_controller" );
					show_list_view(theemailid.value+'_'+basis+'_'+rolltype,'show_dtls_list_view','cut_details_container','requires/get_pass_entry_controller','');					
				}
				release_freezing();
			}
			else if(basis==60)
			{
				var theemailid=this.contentDoc.getElementById("txt_po_id");
				var theemailval=this.contentDoc.getElementById("txt_po_val");
				var issue_id=theemailval.value;
				var issue_dtls_id=theemailid.value;
				var rolltype=1;
				if (issue_id!="" && issue_dtls_id!="")
				{
					var issue_ids=issue_id.split(',');
					issue_ids = [...new Set(issue_ids)];
					issue_ids = issue_ids.join(',');
					$("#txt_chalan_no").val(issue_ids);
					$("#txt_issue_no").val(issue_ids);
					//check_within_group_status(rolltype);
					get_php_form_data(issue_ids+'_'+basis, "populate_sent_data", "requires/get_pass_entry_controller" );
					show_list_view(issue_ids+'_'+basis+'_'+rolltype+'_'+issue_dtls_id,'show_dtls_list_view','cut_details_container','requires/get_pass_entry_controller','');					
				}
				release_freezing();
			}
			else if(basis==32)
			{	
				$("#txt_chalan_no").val(sysNumber[1]);
				$("#txt_issue_no").val(sysNumber[0]);
				var basis=$("#cbo_basis").val();
				if (sysNumber[0]!="" || sysNumber[1]!="")
				{
					get_php_form_data(sysNumber[1]+'_'+basis, "populate_sent_data", "requires/get_pass_entry_controller" );
					show_list_view(sysNumber[0]+'_'+basis,'show_dtls_list_view','cut_details_container','requires/get_pass_entry_controller','');	
				}
				release_freezing();
			}						
			else if (basis==10 || basis==12 || basis==13 || basis==19 || basis==34 || basis==55 || basis==62 || basis==16 || basis == 65 || basis == 66 || basis == 15|| basis == 51)
			{
				var theemailid=this.contentDoc.getElementById("txt_po_id");
				var theemailval=this.contentDoc.getElementById("txt_po_val");
				var rolltype=this.contentDoc.getElementById("txt_rolltype_id").value;
				//alert(theemailid.value+'=='+theemailval.value);
				if (theemailid.value!="" || theemailval.value!="")
				{
					if (basis==55 || basis==62)
					{
						$("#txt_chalan_no").val(theemailval.value);
						//$("#txt_issue_no").val(theemailval.value);
					}
					else
					{
						$("#txt_chalan_no").val(theemailval.value);
						$("#txt_issue_no").val(theemailid.value);
					}

					get_php_form_data(theemailval.value+'_'+basis+'_'+rolltype, "populate_sent_data", "requires/get_pass_entry_controller" );
					show_list_view(theemailid.value+'_'+basis+'_'+rolltype,'show_dtls_list_view','cut_details_container','requires/get_pass_entry_controller','');					
				}
				release_freezing();
			}
			else if (basis==17)
			{
				var theemailid=this.contentDoc.getElementById("txt_po_id");
				var theemailval=this.contentDoc.getElementById("txt_po_val");
				var rolltype=this.contentDoc.getElementById("txt_rolltype_id").value;
				//alert(theemailid.value+'=='+theemailval.value);
				if (theemailid.value!="" || theemailval.value!="")
				{

					$("#txt_chalan_no").val(theemailval.value);
					$("#txt_issue_no").val(theemailid.value);

					get_php_form_data(theemailid.value+'_'+basis+'_'+theemailid.value+'_'+rolltype, "populate_sent_data", "requires/get_pass_entry_controller" );
					show_list_view(theemailid.value+'_'+basis+'_'+theemailid.value+'_'+rolltype,'show_dtls_list_view','cut_details_container','requires/get_pass_entry_controller','');					
				}
				release_freezing();
			}
			else if (basis==69)
			{
				var theemailid=this.contentDoc.getElementById("txt_po_id");
				var theemailval=this.contentDoc.getElementById("txt_po_val");
				var rolltype=this.contentDoc.getElementById("txt_rolltype_id").value;
				//alert(theemailid.value+'=='+theemailval.value);
				if (theemailid.value!="" || theemailval.value!="")
				{

					$("#txt_chalan_no").val(theemailval.value);
					$("#txt_issue_no").val(theemailid.value);

					get_php_form_data(theemailid.value+'_'+basis+'_'+theemailid.value+'_'+rolltype, "populate_sent_data", "requires/get_pass_entry_controller" );
					show_list_view(theemailid.value+'_'+basis+'_'+theemailid.value+'_'+rolltype,'show_dtls_list_view','cut_details_container','requires/get_pass_entry_controller','');					
				}
				release_freezing();
			}
			else if (basis==70)
			{
				var theemailid=this.contentDoc.getElementById("txt_po_id");
				var theemailval=this.contentDoc.getElementById("txt_po_val");
				var rolltype=this.contentDoc.getElementById("txt_rolltype_id").value;
				//alert(theemailid.value+'=='+theemailval.value);
				if (theemailid.value!="" || theemailval.value!="")
				{

					$("#txt_chalan_no").val(theemailval.value);
					$("#txt_issue_no").val(theemailid.value);

					get_php_form_data(theemailid.value+'_'+basis+'_'+theemailval.value, "populate_sent_data", "requires/get_pass_entry_controller" );
					show_list_view(theemailid.value+'_'+basis+'_'+theemailval.value,'show_dtls_list_view','cut_details_container','requires/get_pass_entry_controller','');					
				}
				release_freezing();
			}
			else if(basis==7)
			{
				var theemailid=this.contentDoc.getElementById("txt_po_id");
				var theemailval=this.contentDoc.getElementById("txt_po_val");
				var rolltype=this.contentDoc.getElementById("txt_rolltype_id").value;
				
				//alert(rolltype.value);
				if (theemailid.value!="" || theemailval.value!="")
				{					
					$("#txt_chalan_no").val(theemailval.value);
					$("#txt_issue_no").val(theemailid.value);
					//check_within_group_status(rolltype);
					get_php_form_data(theemailval.value+'_'+basis, "populate_sent_data", "requires/get_pass_entry_controller" );
					show_list_view(theemailid.value+'_'+basis+'_'+theemailval.value+'_'+rolltype,'show_dtls_list_view','cut_details_container','requires/get_pass_entry_controller','');				
				}
				release_freezing();
			}
			else if(basis==5)
			{
				var theemailid=this.contentDoc.getElementById("txt_po_id");
				var theemailval=this.contentDoc.getElementById("txt_po_val");
				
				if (theemailid.value!="" && theemailval.value!="")
				{					
					$("#txt_chalan_no").val(theemailval.value);
					$("#txt_issue_no").val(theemailid.value);

					get_php_form_data(theemailval.value+'_'+basis, "populate_sent_data", "requires/get_pass_entry_controller" );
					show_list_view(theemailid.value+'_'+basis+'_'+theemailval.value,'show_dtls_list_view','cut_details_container','requires/get_pass_entry_controller','');				
				}
				release_freezing();
			}
			else if(basis==18)
			{
				var theemailid=this.contentDoc.getElementById("txt_po_id");
				var theemailissueid=this.contentDoc.getElementById("txt_issue_id");
				var theemailval=this.contentDoc.getElementById("txt_po_val");
				
				if (theemailid.value!="" && theemailval.value!="")
				{					
					$("#txt_chalan_no").val(theemailval.value);
					$("#txt_issue_no").val(theemailissueid.value);

					get_php_form_data(theemailval.value+'_'+basis, "populate_sent_data", "requires/get_pass_entry_controller" );
					show_list_view(theemailissueid.value+'_'+basis+'_'+theemailid.value,'show_dtls_list_view','cut_details_container','requires/get_pass_entry_controller','');				
				}
				release_freezing();
			}	
			else if(basis==6)
			{
				var theemailid=this.contentDoc.getElementById("txt_po_id");
				var theemailval=this.contentDoc.getElementById("txt_po_val");
				var rolltype=this.contentDoc.getElementById("txt_rolltype_id").value;
				
				//alert(rolltype.value);
				if (theemailid.value!="" || theemailval.value!="")
				{
					$("#txt_chalan_no").val(theemailval.value);
					$("#txt_issue_no").val(theemailid.value);
					//check_within_group_status(rolltype);
					get_php_form_data(theemailval.value+'_'+basis, "populate_sent_data", "requires/get_pass_entry_controller" );
					show_list_view(theemailid.value+'_'+basis+'_'+rolltype,'show_dtls_list_view','cut_details_container','requires/get_pass_entry_controller','');					
				}
				release_freezing();
			}
			else if(basis==20){
				
					var basis=$("#cbo_basis").val();
					var theemailid=this.contentDoc.getElementById("transfer_system_id");
			     	var theemailval=this.contentDoc.getElementById("challan_no");
					 $("#txt_chalan_no").val(theemailval.value);		
						get_php_form_data(theemailval.value+'_'+basis, "populate_sent_data", "requires/get_pass_entry_controller" );
						show_list_view(sysNumber[0]+'_'+basis+'_'+theemailid.value+'_'+sysNumber[5],'show_dtls_list_view','cut_details_container','requires/get_pass_entry_controller','');						
						
						release_freezing();
			}	
			else if(basis==26){
				    $("#txt_chalan_no").val();
					$("#txt_issue_no").val();
					var basis=$("#cbo_basis").val();
					var theemailId=this.contentDoc.getElementById("txt_po_id_data");
					var theemailSys=this.contentDoc.getElementById("txt_po_val_data");
					$("#txt_chalan_no").val(theemailSys.value);
					get_php_form_data(theemailSys.value+'_'+basis, "populate_sent_data", "requires/get_pass_entry_controller" );
					show_list_view(theemailId.value+'_'+basis,'show_dtls_list_view','cut_details_container','requires/get_pass_entry_controller','');					
					release_freezing();
		   }	
			else
			{
				if(sysNumber!="")
				{
					$("#txt_chalan_no").val(sysNumber[1]);
					$("#txt_issue_no").val(sysNumber[0]);
					var basis=$("#cbo_basis").val();
					
					if(sysNumber[2]!="")
					{					
						get_php_form_data(sysNumber[1]+'_'+basis, "populate_sent_data", "requires/get_pass_entry_controller" );
						show_list_view(sysNumber[0]+'_'+basis+'_'+sysNumber[2]+'_'+sysNumber[5],'show_dtls_list_view','cut_details_container','requires/get_pass_entry_controller','');						
						
					}
							
				}
				release_freezing();
			}

			if(basis!=12)
			{
				if($("#cbo_group").val()==2)
				{
					$("#txt_sent_to").attr("onClick","openmypage_sent_to()").attr("readonly","true");					
				}
			}
		}
	}

	function generate_report_file(data,action,page)
	{
		window.open("requires/get_pass_entry_controller.php?data=" + data+'&action='+action, true );
	}
	
	function print_to_html_report(operation)
	{
		//alert(operation);	
		if (operation==2) 
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
			window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+show_item+'&action=print_to_html_report&template_id='+$('#cbo_template_id').val(), true );
		}
		else if(operation==3)
		  { 
			//For Print Embellishment Issue
			if($("#cbo_basis").val()==13)
			{
				var emb_issue_ids= $("#txt_chalan_no").val();
				var report_title="Gate Pass";
				generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+report_title+'*'+emb_issue_ids+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_emb_issue_print','requires/get_pass_entry_controller');
			}
			else if($("#cbo_basis").val()==62 || $("#cbo_basis").val()==55)
			{
				// Embellishment Issue for Bundle - Embroidery
				var emb_issue_ids= $("#txt_chalan_no").val();
				var report_title="Gate Pass";
				generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+report_title+'*'+emb_issue_ids+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_emb_issue_print_emb_print','requires/get_pass_entry_controller');
			}
			else if($("#cbo_basis").val()==49)
			{
				var emb_issue_ids= $("#txt_chalan_no").val();
				var issue_id= $("#txt_issue_no").val();
				var report_title="Gate Pass";
				//generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+report_title+'*'+show_item+'*'+$('#cbo_basis').val()+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_print','requires/get_pass_entry_controller');				
				generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+report_title+'*'+emb_issue_ids+'*'+issue_id+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_printing_delivery_print','requires/get_pass_entry_controller');
			}
			else
			{
				alert("This is for Embellishment Issue Basis");
			}
			return;
		}
		else if (operation==4) 
		{
			if($("#cbo_basis").val()==12)
			{
				var show_item='';
				var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
				if (r==true)
				{
					show_item="1";
				}
				else
				{
					show_item="0";
				}
				window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+show_item+'&action='+"print_to_html_report4&template_id="+$('#cbo_template_id').val(), true );
			}
			else
			{
				alert("This is for Garments Delivery Basis");
			}
		}
		else if (operation==9) //id_print_to_button10
		{
			var basis_id=$("#cbo_basis").val();			
			if(basis_id==12)
			{
				var show_item='';			
				//window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+show_item+'&action='+"print_to_html_report9", true );
				var report_title=$( "div.form_caption" ).html();
				generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+report_title+'*'+show_item+'*'+$('#cbo_basis').val()+'*'+$('#cbo_com_location_id').val()+'&template_id='+$('#cbo_template_id').val(),'print_to_html_report9','requires/get_pass_entry_controller');
			}
			else
			{
				alert("This is for Garments Delivery Basis");
			}
		}
		else if (operation==10) //id_print_to_button10
		{
			var basis_id=$("#cbo_basis").val();			
			if(basis_id==28)
			{
				var show_item='';			
				//window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+show_item+'&action='+"print_to_html_report9", true );
				var report_title=$( "div.form_caption" ).html();
				generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+report_title+'*'+show_item+'*'+$('#cbo_basis').val()+'*'+$('#cbo_com_location_id').val()+'&template_id='+$('#cbo_template_id').val(),'print_to_html_report10','requires/get_pass_entry_controller');
			}
			else
			{
				alert("This is for Sample Delivery Basis");
			}
		}
		else if (operation==11) 
		{
			if($("#cbo_basis").val()==2)
			{				
				window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+'&action='+"print_to_html_report11&template_id="+$('#cbo_template_id').val(), true );
			}
			else
			{
				alert("This is for Yarn Basis Only");
			}
		}
		else if (operation==20) 
		{
			if($("#cbo_basis").val()==2)
			{
				window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+'&action='+"print_to_html_report20&template_id="+$('#cbo_template_id').val(), true );
			}
			else
			{
				alert("This is for Yarn Basis Only");
			}
		}
		else if (operation==14) 
		{
			if($("#cbo_basis").val()==11)
			{				
				window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+'&action='+"print_to_html_report14&template_id="+$('#cbo_template_id').val(), true );
			}
			else
			{
				alert("This is for Finish Fabric Delivery to Store Basis");
			}
		}
		else if (operation==5) 
		{
			/*var show_item='';
				var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
				if (r==true)
				{
					show_item="1";
				}
				else
				{
					show_item="0";
				}*/
			var show_item=0;	
			window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+show_item+'*'+$('#cbo_location').val()+'&template_id='+$('#cbo_template_id').val()+'&action='+"print_to_html_report5", true );
		}
		else if (operation==6) 
		{
			if($("#cbo_basis").val()!=14)
			{
				alert('Report Generate only for Challan[Cutting Delivery] Basis');
			}
			else
			{
			    var show_item=0;	
				window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+show_item+'*'+$('#txt_chalan_no').val()+'*'+$('#cbo_com_location_id').val()+'&template_id='+$('#cbo_template_id').val()+'&action='+"print_to_html_report6", true );
			}
		}
		else if (operation==12) 
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
			
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+report_title+'*'+show_item+'*'+$('#cbo_basis').val()+'*'+$('#cbo_com_location_id').val()+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_print12','requires/get_pass_entry_controller');
			return;
		}
		else if (operation==13) 
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
			window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+show_item+'&action=print_to_html_report_13&template_id='+$('#cbo_template_id').val(), true );
		}
		else if (operation==15) 
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
			window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+show_item+'&action=print_to_html_report_15&template_id='+$('#cbo_template_id').val(), true );
		}
		else if (operation==18) 
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
			window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+show_item+'&action=print_to_html_report_15_v2&template_id='+$('#cbo_template_id').val(), true );
		}
		else if (operation==19) 
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
			window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+show_item+'&action=print_to_html_report_15_v3&template_id='+$('#cbo_template_id').val(), true );
		}
		else if (operation==16) 
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
			window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+show_item+'&action=print_to_html_report16&template_id='+$('#cbo_template_id').val(), true );
		}
		else if (operation==17)   
		{
			if ($('#cbo_basis').val() != 8){
				alert("This Button Only For Subcon Knitting Delevery Basis");
				return;
			}			
			window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+$('#cbo_basis').val()+'*'+$('#txt_chalan_no').val()+'&action=print_to_html_report17&template_id='+$('#cbo_template_id').val(), true );
		}
		else if (operation==7) 
		{
			if($("#cbo_basis").val()!=4 && $("#cbo_basis").val()!=3)
			{
				alert('Report Generate only for Challan[Grey Fabric] and Challan[Finish Fabric] Basis');
			}
			else
			{
			    var show_item=0;	
				window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+show_item+'*'+$('#txt_chalan_no').val()+'&template_id='+$('#cbo_template_id').val()+'&action='+"print_to_html_report7", true );
			}
		}
		
		else if (operation==111) 
		{
			
				//alert('Left Over Gate Pass Print');
			
			    var show_item=0;	
				window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+show_item+'*'+$('#txt_chalan_no').val()+'&template_id='+$('#cbo_template_id').val()+'&action='+"akh_print_item_print_left_over", true );
			
		}
		else if (operation==222) 
		{
			
				//alert('Left Over Gate Pass Print');
			
			    var show_item=0;	
				window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+show_item+'*'+$('#txt_chalan_no').val()+'&template_id='+$('#cbo_template_id').val()+'&action='+"akh_print_item_print", true );
			
		}
		else if (operation==27) 
		{
							 					     	
				window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#txt_chalan_no').val()+'&template_id='+$('#cbo_template_id').val()+'&action='+"dot_print", true );
			
		}
	}

	function fnc_getpass_entry(operation)
	{
		if(operation==4)
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
			var report_title=$( "div.form_caption" ).html();
			//var report_title="Gate Pass Entry";
			//alert($('#txtorderidhidden_1').val())
			generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+report_title+'*'+show_item+'*'+$('#cbo_basis').val()+'*'+$('#cbo_com_location_id').val()+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_print','requires/get_pass_entry_controller');
			//return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][251]);?>'){
				if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][251]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][251]);?>')==false)
				{
					
					return;
				}
			}
            var basisValue = $('#cbo_basis').val();
            if(basisValue == 1){
                var field_name = 'cbo_company_name*cbo_basis*cbo_com_location_id*cbo_department_name*txt_sent_by*cbo_group*txt_sent_to*txt_rece_date*txt_carried_by';
                var msg = 'Company Name*Basis*Location*Department*Sent By*Within Group*Sent To*Out Date*Carried By';
            }
			else{
                var field_name = 'cbo_company_name*cbo_basis*txt_chalan_no*cbo_com_location_id*cbo_department_name*txt_sent_by*cbo_group*txt_sent_to*txt_rece_date*txt_carried_by';
                var msg = 'Company Name*Basis*System ID/Challan No*Location*Department*Sent By*Within Group*Sent To*Out Date*Carried By';
            }
			if( form_validation(field_name, msg)==false )
			{
				return;
			}

			var start_hours = $('#txt_start_hours').val();

			if(start_hours =='')
			{
				alert("Out Hours Should Not Be Blanck");
				return;
			}

			var start_minuties = $('#txt_start_minuties').val();
			if(start_minuties =='')
			{
				alert("Plz fill up Minuties field value");
				return;
			}
			
			var returnable = $("#cbo_returnable").val();
			if(returnable==1)
			{
				if (form_validation('txt_return_date','Return Date')==false)
				{
					$('#txt_return_date').focus();  
					$("#txt_return_date").removeAttr("disabled",true);
					return;
				}
			}
			else
			{
				$('#txt_return_date').val('');   
				$("#txt_return_date").attr("disabled",true);
			}
			if (basisValue == 50) var row_num=$('#tbl_order_details tbody tr:not(#total_display_last_tr)').length;
			else var row_num=$('#tbl_order_details tbody tr').length;
			// alert(row_num); return;
			var dataString ="txt_system_id*cbo_company_name*cbo_basis*cbo_department_name*cbo_section*txt_sent_by*cbo_group*txt_rece_date*txt_start_hours*txt_start_minuties*cbo_returnable*cbo_delevery_as*txt_return_date*update_id*txt_attention*txt_chalan_no*txt_issue_no*txt_carried_by*cbo_issue_purpose*hidden_purpose_id*cbo_com_location_id*cbo_location*txt_vhicle_number*txt_do_no*txt_mobile*txt_delivery_company*txt_remarks_mst*txt_returnable_gate_pass*txt_security_lock_no*txt_driver_license_no*txt_driver_name*cbo_ready_to_approved*hidden_entry_form"; 
			var txt_sent_to =encodeURIComponent($('#txt_sent_to').val());
 			var data1="action=save_update_delete&operation="+operation+"&row_num="+row_num+"&txt_sent_to='"+txt_sent_to+"'"+get_submitted_data_string(dataString,"../");
			var data2='';
			var test_val=""; 
			/*var categoryP="";
			var itemdescriptionP="";
			var cbouomP="";  
			var sampleP=""; 
			var txtorderP="";*/
			var categoryP=$('#cboitemcategory_'+row_num).val();
			var itemdescription=$('#txtitemdescription_'+row_num).val();
			var itemdescriptionP=itemdescription.trim("");
			var cbouomP=$('#cbouom_'+row_num).val();
			var sampleP=$('#cbosample_'+row_num).val();
			var txtorderP=$('#txtorder_'+row_num).val();
			  
			for(var i=1; i<=row_num; i++)
			{			
				var category=$('#cboitemcategory_'+i).val();
				var itemdescription=$('#txtitemdescription_'+i).val();
				var itemdescription=itemdescription.trim("");
				var cbouom=$('#cbouom_'+i).val();
				var sample=$('#cbosample_'+i).val();
				var txtorder=$('#txtorder_'+i).val();
				var cbo_basis=$('#cbo_basis').val();
				if(cbo_basis==1)
				{
					if(category==0)
					{
						if(sample==0 )
						 {
							if (form_validation('cboitemcategory_'+i,'Item Category')==false)
							{
								return;
							}
						 }
					}
					else if(sample==0 )
					{
						if(category==0)
						{
							if (form_validation('cbosample_'+i,'Sample')==false)
							{
								return;
							}
						}
					}

					if (row_num>1) 
					{
						if(category==categoryP && sample==sampleP && itemdescription==itemdescriptionP && cbouom==cbouomP && txtorder==txtorderP)
						{
							alert("Same Description and same UOM do not Allowed");
							return;
						}
						 categoryP=category;
						 itemdescriptionP=itemdescription;
						 cbouomP=cbouom;
						 sampleP=sample;
						 txtorderP=txtorder;
					}
				}
				else if(cbo_basis==5 || cbo_basis==23 || cbo_basis==18)
				{
					if (form_validation('cboitemcategory_'+i,'Item Category')==false)
					{
						return;
					}
				}			
				
				if($('#txtquantity_'+i).val()!="")
				{
					if($('#txtitemdescription_'+i).val()!="" && $('#txtquantity_'+i).val()!="" && $('#cbouom_'+i).val()!=0 )
					{					
						
						if(cbo_basis==3)
						{
							data2+=get_submitted_data_string('updatedtlsid_'+i+'*cboitemcategory_'+i+'*cbosample_'+i+'*txtitemdescription_'+i+'*txtquantity_'+i+'*txtrejectqty_'+i+'*cbouom_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtorder_'+i+'*txtremarks_'+i+'*txtuomqty_'+i+'*txtorderidhidden_'+i+'*txtbag_'+i+'*txtcartonqnty_'+i+'*challanIds_'+i+'*challandtlsIds_'+i+'*systemid_'+i+'*prodID_'+i+'*hiddenissalesflag_'+i,"../",i);
						}
						else
						{
							data2+=get_submitted_data_string('updatedtlsid_'+i+'*cboitemcategory_'+i+'*cbosample_'+i+'*txtitemdescription_'+i+'*txtquantity_'+i+'*txtrejectqty_'+i+'*cbouom_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtorder_'+i+'*txtremarks_'+i+'*txtuomqty_'+i+'*txtorderidhidden_'+i+'*txtbag_'+i+'*txtcartonqnty_'+i+'*challanIds_'+i+'*challandtlsIds_'+i+'*prodID_'+i,"../",i);
						}
					
						//alert(data2); return;
					}
					else if($('#cbo_basis').val()==51)
					{
						
						//alert($('#cbo_basis').val());						
						if($('#txtquantity_'+i).val()!="" && $('#cbouom_'+i).val()!=0 )
						{							
							if(cbo_basis==3)
							{
								data2+=get_submitted_data_string('updatedtlsid_'+i+'*cboitemcategory_'+i+'*cbosample_'+i+'*txtitemdescription_'+i+'*txtquantity_'+i+'*txtrejectqty_'+i+'*cbouom_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtorder_'+i+'*txtremarks_'+i+'*txtuomqty_'+i+'*txtorderidhidden_'+i+'*txtbag_'+i+'*txtcartonqnty_'+i+'*challanIds_'+i+'*challandtlsIds_'+i+'*systemid_'+i+'*prodID_'+i,"../",i);
							}
							else
							{
								data2+=get_submitted_data_string('updatedtlsid_'+i+'*cboitemcategory_'+i+'*cbosample_'+i+'*txtitemdescription_'+i+'*txtquantity_'+i+'*txtrejectqty_'+i+'*cbouom_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtorder_'+i+'*txtremarks_'+i+'*txtuomqty_'+i+'*txtorderidhidden_'+i+'*txtbag_'+i+'*txtcartonqnty_'+i+'*challanIds_'+i+'*challandtlsIds_'+i+'*prodID_'+i,"../",i);
							}
						}
						else
						{
							if($('#txtquantity_'+i).val()=="")
							{
								alert("Please Fill Up Quantity Field");
								$('#txtquantity_'+i).focus();
								return;
							}
						}
					}
					else if($('#cbo_basis').val()==70)
					{
						//alert($('#cbo_basis').val());		
						//alert($('#txtquantity_'+i).val());				
						if($('#txtquantity_'+i).val()=="" || $('#txtquantity_'+i).val()==0)
						{
							alert("Please Fill Up Quantity Field");
							$('#txtquantity_'+i).focus();
							return;
						}

						if(cbo_basis==3)
						{
							data2+=get_submitted_data_string('updatedtlsid_'+i+'*cboitemcategory_'+i+'*cbosample_'+i+'*txtitemdescription_'+i+'*txtquantity_'+i+'*txtrejectqty_'+i+'*cbouom_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtorder_'+i+'*txtremarks_'+i+'*txtuomqty_'+i+'*txtorderidhidden_'+i+'*txtbag_'+i+'*txtcartonqnty_'+i+'*challanIds_'+i+'*challandtlsIds_'+i+'*systemid_'+i+'*prodID_'+i+'*hiddenissalesflag_'+i,"../",i);
						}
						else
						{
							data2+=get_submitted_data_string('updatedtlsid_'+i+'*cboitemcategory_'+i+'*cbosample_'+i+'*txtitemdescription_'+i+'*txtquantity_'+i+'*txtrejectqty_'+i+'*cbouom_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtorder_'+i+'*txtremarks_'+i+'*txtuomqty_'+i+'*txtorderidhidden_'+i+'*txtbag_'+i+'*txtcartonqnty_'+i+'*challanIds_'+i+'*challandtlsIds_'+i+'*prodID_'+i,"../",i);
						}
					
						//alert(data2); return;
					}

					else
					{
						if($('#txtitemdescription_'+i).val()=="")
						{
							alert("Please Fill Up Description Field");
							$('#txtitemdescription_'+i).focus();
							return;
						}
						else if($('#txtquantity_'+i).val()=="" || $('#txtquantity_'+i).val()==0)
						{
							alert("Please Fill Up Quantity Field");
							$('#txtquantity_'+i).focus();
							return;
						}
						else
						{
							alert("Please Fill Up UOM Field");
							$('#cbouom_'+i).focus();
							return;
						}
					}
				}
				else
				{
					if(cbo_basis==3)
					{
						data2+=get_submitted_data_string('updatedtlsid_'+i+'*cboitemcategory_'+i+'*cbosample_'+i+'*txtitemdescription_'+i+'*txtquantity_'+i+'*txtrejectqty_'+i+'*cbouom_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtorder_'+i+'*txtremarks_'+i+'*txtuomqty_'+i+'*txtorderidhidden_'+i+'*txtbag_'+i+'*txtcartonqnty_'+i+'*challanIds_'+i+'*challandtlsIds_'+i+'*systemid_'+i+'*prodID_'+i+'*hiddenissalesflag_'+i,"../",i);
					}
					else
					{
						data2+=get_submitted_data_string('updatedtlsid_'+i+'*cboitemcategory_'+i+'*cbosample_'+i+'*txtitemdescription_'+i+'*txtquantity_'+i+'*txtrejectqty_'+i+'*cbouom_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtorder_'+i+'*txtremarks_'+i+'*txtuomqty_'+i+'*txtorderidhidden_'+i+'*txtbag_'+i+'*txtcartonqnty_'+i+'*challanIds_'+i+'*challandtlsIds_'+i+'*prodID_'+i,"../",i);
					}
				}
			}
			var data=data1+data2;
			//alert(data); return;
			freeze_window(operation);
			http.open("POST","requires/get_pass_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_getpass_entry_reponse;
		}
		else if(operation==8) // only for Fakir Fashion with QR code (similar as print button 1)
		{
			var show_item="0";
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+report_title+'*'+show_item+'*'+$('#cbo_basis').val()+'*'+$('#cbo_com_location_id').val()+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_print8_fashion','requires/get_pass_entry_controller');
			return;
		}
		if(operation==6)
		{
			if (form_validation('txt_system_id','Gate Pass ID')==false){
					return;
			}
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+report_title+'*'+show_item+'*'+$('#cbo_basis').val()+'*'+$('#cbo_returnable').val()+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_print6','requires/get_pass_entry_controller');
			return;
		}	
		else if (operation==9)
		{
			if (form_validation('txt_system_id','Gate Pass ID')==false){
					return;
			}
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+report_title+'*'+show_item+'*'+$('#cbo_basis').val()+'*'+$('#cbo_returnable').val()+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_print9','requires/get_pass_entry_controller');
			return;
		}
		else if (operation==10)
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
			var report_title=$( "div.form_caption" ).html();
			//alert($('#txtorderidhidden_1').val())
			generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+report_title+'*'+show_item+'*'+$('#cbo_basis').val()+'*'+$('#cbo_com_location_id').val()+'*'+$('#no_copy').val()+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_print10','requires/get_pass_entry_controller');
		}	
		else if (operation==11)
		{
			// if (form_validation('txt_system_id','Gate Pass ID')==false){
			// 		return;
			// }
			var basis_id=$("#cbo_basis").val();
			if(basis_id==13){

				var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+report_title+'*'+show_item+'*'+$('#cbo_basis').val()+'*'+$('#cbo_returnable').val()+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_printamt','requires/get_pass_entry_controller');
			}
			else{
			     alert("This is for Embellishment Issue Entry");
			}
		}
		else if (operation==14)
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
			var report_title=$( "div.form_caption" ).html();
			//alert($('#txtorderidhidden_1').val())
			generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+report_title+'*'+show_item+'*'+$('#cbo_basis').val()+'*'+$('#cbo_com_location_id').val()+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_print14','requires/get_pass_entry_controller');
		}
		else if(operation==15)
		{
			if (form_validation('txt_system_id','Gate Pass ID')==false){
					return;
			}
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+report_title+'*'+show_item+'*'+$('#cbo_basis').val()+'*'+$('#cbo_returnable').val()+'&template_id='+$('#cbo_template_id').val(),'get_pass_entry_print11','requires/get_pass_entry_controller');
			return;
		}
        else if(operation==20)
        {
            if (form_validation('txt_system_id','Gate Pass ID')==false){
                return;
            }
            var show_item='';
            var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
            if (r==true)
            {
                show_item="1";
            }
            else
            {
                show_item="0";
            }
            var report_title=$( "div.form_caption" ).html();
            generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+report_title+'*'+show_item+'*'+$('#cbo_basis').val()+'*'+$('#cbo_returnable').val()+'*'+$('#no_copy').val()+'*'+$('#hiddenissalesflag_1').val()+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_print20','requires/get_pass_entry_controller');
            return;
        }

		else if(operation==21)
        {
            if (form_validation('txt_system_id','Gate Pass ID')==false){
                return;
            }
            var show_item='';
            var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
            if (r==true)
            {
                show_item="1";
            }
            else
            {
                show_item="0";
            }
            var report_title=$( "div.form_caption" ).html();
            generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+report_title+'*'+show_item+'*'+$('#cbo_basis').val()+'*'+$('#cbo_returnable').val()+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_print21','requires/get_pass_entry_controller');
            return;
        }

		else if(operation==22)
        {
            if (form_validation('txt_system_id','Gate Pass ID')==false){
                return;
            }
            var show_item='';
            var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
            if (r==true)
            {
                show_item="1";
            }
            else
            {
                show_item="0";
            }
            var report_title=$( "div.form_caption" ).html();
            generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+report_title+'*'+show_item+'*'+$('#cbo_basis').val()+'*'+$('#cbo_returnable').val()+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_print22','requires/get_pass_entry_controller');
            return;
        }
		else if(operation==23)
		{
			var show_item='';
				var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
				if (r==true)
				{
					show_item="1";
				}
				else
				{
					show_item="0";
				}
			window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+show_item+'&action=print_to_html_report_scandex&template_id='+$('#cbo_template_id').val(), true );
		}
		if(operation==24)
		{	
			//var m_print = $("#print_mercer").val();
			//alert(m_print);
			
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
			var report_title=$( "div.form_caption" ).html();
			//alert($('#txtorderidhidden_1').val())
			generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+report_title+'*'+show_item+'*'+$('#cbo_basis').val()+'*'+$('#cbo_com_location_id').val()+'&template_id='+$('#cbo_template_id').val()+'&print_mercer='+$('#print_mercer').val(),'get_out_entry_print28','requires/get_pass_entry_controller');
			//return;
		}
		if(operation==25)
		{
			if (form_validation('txt_system_id','Gate Pass ID')==false){
					return;
			}
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+report_title+'*'+show_item+'*'+$('#cbo_basis').val()+'*'+$('#cbo_returnable').val()+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_print16','requires/get_pass_entry_controller');
			return;
		}
		if(operation==26)
		{
			if (form_validation('txt_system_id','Gate Pass ID')==false){
					return;
			}
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+report_title+'*'+show_item+'*'+$('#cbo_basis').val()+'*'+$('#cbo_returnable').val()+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_print17','requires/get_pass_entry_controller');
			return;
		}
    }

	function fnc_getpass_entry_reponse()  
	{
		$("#id_print_to_button").removeClass("formbutton_disabled").addClass("formbutton");
		$("#with_color_size_print").removeClass("formbutton_disabled").addClass("formbutton");
		$("#Printt1").removeClass( "formbutton_disabled").addClass( "formbutton");
		$("#id_print_to_button4").removeClass("formbutton_disabled").addClass("formbutton");
		$("#id_print_to_button5").removeClass("formbutton_disabled").addClass("formbutton");
		$("#id_print_to_button6").removeClass("formbutton_disabled").addClass("formbutton");
		$("#id_print_to_button7").removeClass("formbutton_disabled").addClass("formbutton");
		$("#id_print_to_button8").removeClass("formbutton_disabled").addClass("formbutton");
		$("#id_print_to_button9").removeClass("formbutton_disabled").addClass("formbutton");
		$("#print12").removeClass("formbutton_disabled").addClass("formbutton");
		$("#print15").removeClass("formbutton_disabled").addClass("formbutton");
		$("#id_print_to_button_Scandex").removeClass("formbutton_disabled").addClass("formbutton");
		$("#print_mercer").removeClass("formbutton_disabled").addClass("formbutton");
		$("#print_16").removeClass("formbutton_disabled").addClass("formbutton");
		$("#print_17").removeClass("formbutton_disabled").addClass("formbutton");
		$("#print14").removeClass("formbutton_disabled").addClass("formbutton");
		$("#print6").removeClass("formbutton_disabled").addClass("formbutton");
		$("#print9").removeClass("formbutton_disabled").addClass("formbutton");
		$("#print10").removeClass("formbutton_disabled").addClass("formbutton");
		$("#printlibas").removeClass("formbutton_disabled").addClass("formbutton");
		$("#id_print_to_button11").removeClass("formbutton_disabled").addClass("formbutton");
		$("#id_print_to_button14").removeClass("formbutton_disabled").addClass("formbutton");
		$("#id_print_to_button15").removeClass("formbutton_disabled").addClass("formbutton");
		$("#id_print_to_button18").removeClass("formbutton_disabled").addClass("formbutton");
		$("#id_print_to_button16").removeClass("formbutton_disabled").addClass("formbutton");
		$("#id_print_to_button12").removeClass("formbutton_disabled").addClass("formbutton");
		$("#print13").removeClass("formbutton_disabled").addClass("formbutton");
		$("#with_challan").removeClass("formbutton_disabled").addClass("formbutton");
		$("#printamt").removeClass("formbutton_disabled").addClass("formbutton");
		$("#print11").removeClass("formbutton_disabled").addClass("formbutton");

		var returnable=$("#cbo_returnable").val()*1;
		if (returnable==1)
		{
			$("#returnable_item_dtls").removeClass("formbutton_disabled").addClass("formbutton");
		}
		
		if(http.readyState == 4) 
		{
			//alert(http.responseText);
			var response=trim(http.responseText).split('**');
			if(response[0]==0)
			{
				show_msg(trim(response[0]));
			}
			else if(response[0]==1)
			{
				show_msg(trim(response[0]));
			}
			else if(response[0]==10 || response[0]==15)
			{
				show_msg(trim(response[0]));
				release_freezing();
				return;
			}
			else if(response[0]==20)
			{
				//alert(response[1]);
				release_freezing();
				return;
			}
			else if(response[0]==11)
			{
				alert(response[1]);
				release_freezing();
				return;
			}
			else if(trim(response[0])=='26')
			{
				alert ("Returnable Date Will Not Be Allowed Less Than Gate Pass Date");
				release_freezing();
				return;
			}
			if(response[0]==0 || response[0]==1)
			{
				$("#txt_system_id").val(response[1]);
				$("#update_id").val(response[2]);
				$("#cbo_company_name").attr("disabled",true);
				$("#cbo_basis").attr("disabled",true);
				$("#cbo_com_location_id").attr("disabled",true);
				$("#cbo_group").attr("disabled",true);
				$("#txt_sent_to").attr("disabled",true);
				
				get_php_form_data(response[2], "populate_master_from_data", "requires/get_pass_entry_controller" );
				show_list_view(response[2],'show_update_list_view','cut_details_container','requires/get_pass_entry_controller','');
				var cbo_basis=$("#cbo_basis").val()*1;
				
				if(cbo_basis!=2)
				{
					$("#txt_chalan_no").attr("disabled",true);
				}
				if(cbo_basis!=1)
				{
					for(var j=1;j<=response[3];j++)
					{ 
						//alert(response[3]);
						$("#cboitemcategory_"+j).attr("disabled",true);						
						var sam=$("#cbosample_"+j).val();
						$('#cbosample_'+j).attr('disabled','disabled');					
						$("#txtitemdescription_"+j).attr("disabled",true);
						$("#txtquantity_"+j).attr("disabled",true);
						$("#txtrejectqty_"+j).attr("disabled",true);
						$("#cbouom_"+j).attr("disabled",true);
						$("#txtrate_"+j).attr("disabled",true);
						$("#txtamount_"+j).attr("disabled",true);
						$("#txtorder_"+j).attr("disabled",true);
						var sam=$('#cbosample_'+j).val();
						var cat=$('#cboitemcategory_'+j).val();

						if (sam!=0) gate_enable_disable(2);
						else if (cat!=0) gate_enable_disable(1);					
					}
				}
				else if(cbo_basis==1) 
				{
					//set_all_onclick();
					for(var j=1;j<=response[3];j++)
					{ 
						var itemCat=$("#cboitemcategory_"+j).val();						
						var sam=$("#cbosample_"+j).val();
						if(itemCat!=0)
						{
							$('#cbosample_'+j).attr('disabled','disabled');
						}
						else if (sam!=0) 
						{
							$('#cboitemcategory_'+j).attr('disabled','disabled');
						}
						$('#txtcartonqnty_'+j).attr('disabled',false);

						/* $('#txtitemdescription_'+j).autocomplete({ 
							source: item_description,
							minLength: 3
						}); */
					}
				}
				set_button_status(1, permission, 'fnc_getpass_entry',1,1);
			}
			if(response[0]==2)
			{
				reset_form('getpass_1','list_container','','','','');
			}
			release_freezing();
		}
	}

	//amount calculate
	function fn_calculate_amount(id)
	{
		var id=id.split('_');
		var quantity = $("#txtquantity_"+id[1]).val();
		var rate = $("#txtrate_"+id[1]).val();
		var  amount=quantity*rate*1;
		$("#txtamount_"+id[1]).val(number_format_common(amount,"","",7));
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

	function gate_out_scan(str)
	{
		var basis=$('#cbo_basis').val();
		//var str=$('#txt_chalan_no').val();
		get_php_form_data(str+'_'+basis, "populate_sent_data", "requires/get_pass_entry_controller" );
		show_list_view(str+'_'+basis,'show_scan_list_view','cut_details_container','requires/get_pass_entry_controller','');
	}

	$('#txt_chalan_no').live('keydown', function(e) {
		if (e.keyCode === 13) {
			e.preventDefault();
			gate_out_scan(this.value); 
		}
	});

	$('#txt_chalan_no').live("keyup", function(e) {
		var cbo_basis=$('#cbo_basis').val()*1;
		var txt_issue_no=$('#txt_issue_no').val();
		if ( cbo_basis==1 || cbo_basis==28 ) 
		{
        
        }
        else
        {
        	if ( e.keyCode!=17 && e.keyCode!=86 && e.keyCode!=13) 
			{
        		alert("Please Browse Or Scan Only");
            	$('#txt_chalan_no').val("");return;
            }
        }        
    });
	
	function focace_change()
	{
		$('#txt_chalan_no').focus();  
		/* $('#txtitemdescription_1').autocomplete({ 
			source: item_description,
			minLength: 3
		}); */
	}
	
	function returnable_change(type)
	{
		if(type==1)
		{
			$('#txt_return_date').focus();   
			$("#txt_return_date").removeAttr("disabled",true); 
			$("#txt_returnable_gate_pass").attr("disabled",true);
			$("#returnable_item_dtls").removeClass( "formbutton_disabled").addClass( "formbutton");
		}
		else
		{
			$('#txt_return_date').val('');   
			$("#txt_return_date").attr("disabled",true); 
			$("#txt_returnable_gate_pass").removeAttr("disabled",true); 
			$("#returnable_item_dtls").removeClass( "formbutton").addClass( "formbutton_disabled");
		}		
	}

	function sent_to_check_empty(within)
	{
		var cbo_group=$('#cbo_group').val()*1;
		var cbo_basis=$('#cbo_basis').val();
		
		if(within==0)
		{
			$("#txt_sent_to").attr("disabled",true);
			$("#cbo_location").attr("disabled",true);
			//td_id_location 
		}
		else
		{ 
			$("#txt_sent_to").attr("disabled",false); 	
			$("#cbo_location").attr("disabled",false); 	
		}		
	}

	function gate_enable_disable(type)
	{
		// alert(type);
		var cbo_basis=$('#cbo_basis').val();
		if (cbo_basis == 50) var row_num=$('#tbl_order_details tbody tr:not(#total_display_last_tr)').length;
		else var row_num=$('#tbl_order_details tbody tr').length;
		//var row_num=$('#tbl_order_details tbody tr').length;
		for(var j=1;j<=row_num;j++)
		{			
			var category=$('#cboitemcategory_'+j).val();
			var sample_id=$('#cbosample_'+j).val();	
				
			if(type==1)
			{
				if(category!=0)
				{
					if(cbo_basis==1)
					{
						$('#cbosample_'+j).attr('disabled',false); 
					}
					else
					{
						$('#cbosample_'+j).attr('disabled',true); 
					}					
				}
				else
				{
					//$("#cbosample_1").attr("disabled",false); 
					$('#cbosample_'+j).attr('disabled',false); 
				}
			}
			else
			{
				if(sample_id!=0)
				{
					if(sample_id==1)
					{						
						$('#cbo_buyer_'+j).attr('disabled',false);  
					}
					else
					{
						$('#cbo_buyer_'+j).attr('disabled',true);  
					}
					//$("#cboitemcategory_1").attr("disabled",true);
					$('#cboitemcategory_'+j).attr('disabled',true);  
				}
				else
				{
					//$("#cboitemcategory_1").attr("disabled",false);
					$('#cboitemcategory_'+j).attr('disabled',false); 
				}
			}
		}
	}

	function fnResetForm()
	{
		
		//reset_form('getpass_1','list_container','','','','');
		reset_form('getpass_1','list_container','','','$(\'#cut_details_container tr:not(:first)\').remove();','');
		$('#cbosample_1').attr('disabled',false);
		//$('#cbo_buyer_1').attr('disabled',false); 
		$('#cboitemcategory_1').attr('disabled',false); 
		$('#txtitemdescription_1').attr('disabled',false); 
		$('#txtquantity_1').attr('disabled',false); 
		$('#txtrejectqty_1').attr('disabled',false); 
		$('#cbouom_1').attr('disabled',false); 
		$('#txtuomqty_1').attr('disabled',false); 
		$('#txtrate_1').attr('disabled',false); 
		$('#txtamount_1').attr('disabled',true); 
		$('#txtorder_1').attr('disabled',false); 
		$('#txtremarks_1').attr('disabled',false);
        var time = new Date();
        $("#cbo_company_name").attr("disabled",false);
		$("#cbo_basis").attr("disabled",false);
		$("#txt_chalan_no").attr("disabled",false);
		$("#cbo_group").attr("disabled",false);
		$("#txt_sent_to").attr("disabled",true);
		$('#cbo_returnable').val(2);
		$('#txt_start_hours').val(time.getHours());
		$('#txt_start_minuties').val(time.getMinutes() < 10 ? '0'+time.getMinutes() : time.getMinutes());
		$('#txt_rece_date').val('<? echo date("d-m-Y"); ?>');	
		set_button_status(0, permission, 'fnc_getpass_entry',1,1);
	}

	function company_onchange()
	{
		//reset_form('','cut_details_container','','','','');
		var basis = $('#cbo_basis').val();
		if (basis!=0)
		{
			reset_form('','cut_details_container','','','','');
		}

		reset_form('','list_container','cbo_com_location_id*cbo_department_name*cbo_section*cbo_group*txt_sent_to*cbo_location*txt_rece_date*txt_attention*cbo_returnable*txt_return_date*cbo_delevery_as*cbo_issue_purpose*txt_carried_by*txt_sent_by*txt_vhicle_number*txt_do_no*txt_mobile*txt_delivery_company*txt_returnable_gate_pass*txt_remarks_mst','','$(\'#cut_details_container tr:not(:first)\').remove();','');
		$('#cbo_basis').val('').attr('disabled',false);
		$('#txt_chalan_no').val('').attr('disabled',false);
		$('#cboitemcategory_1').val('').attr('disabled',false);
		$('#cbosample_1').val('').attr('disabled',false);
		$('#txtitemdescription_1').val('').attr('disabled',false);		
		$('#txtquantity_1').val('').attr('disabled',false);
		$('#txtrejectqty_1').val('').attr('disabled',false);
		$('#cbouom_1').val('').attr('disabled',false);
		$('#txtrate_1').val('').attr('disabled',false);
		$('#txtamount_1').val('').attr('disabled',false);
		$('#txtbag_1').val('').attr('disabled',false);
		$('#txtcartonqnty_1').val('').attr('disabled',false);
		$('#txtorder_1').val('').attr('disabled',false);
		$('#txtremarks_1').val('').attr('disabled',false);
		$('#txt_start_hours').val('<? echo date('H'); ?>');
		//$('#txt_start_minuties').val('<? echo date('i'); ?>');
		$('#txt_rece_date').val('<? echo date("d-m-Y"); ?>');	
		var time = new Date();
		$('#txt_start_minuties').val(time.getMinutes() < 10 ? '0'+time.getMinutes() : time.getMinutes());
	}

	function basis_onchange(basis)
	{
		//alert(basis);
		var chalan_no = $('#txt_chalan_no').val();
		// alert(chalan_no);

		if(basis==3){
			
			document.getElementById("systemid_th").style.display = "true"; 
		}
		else
		{
			document.getElementById("systemid_th").style.display = "none";
		}

		if ((basis==1 || basis==28) && chalan_no=="")
		{
			
		}
		else if((basis==1 || basis==28) && chalan_no!="" )
		{
			reset_form('','cut_details_container','cbo_department_name*cbo_section*cbo_group*txt_sent_to*cbo_location*txt_attention*cbo_returnable*txt_return_date*cbo_delevery_as*cbo_issue_purpose*txt_carried_by*txt_sent_by*txt_vhicle_number*txt_do_no*txt_mobile*txt_delivery_company*txt_returnable_gate_pass*txt_remarks_mst','','','');
		}
		else
		{
			reset_form('','cut_details_container','cbo_department_name*cbo_section*cbo_group*txt_sent_to*cbo_location*txt_attention*cbo_returnable*txt_return_date*cbo_delevery_as*cbo_issue_purpose*txt_carried_by*txt_sent_by*txt_vhicle_number*txt_do_no*txt_mobile*txt_delivery_company*txt_returnable_gate_pass*txt_remarks_mst','','','');			
		}
		if (basis==1 || basis==28) 
		{
			show_list_view(basis,'independent_list_view','cut_details_container','requires/get_pass_entry_controller','');
		}
	}

	function openmypage_returnable_gate_pass() 
	{
		if( form_validation('cbo_company_name*cbo_group*txt_sent_to*cbo_returnable','Company Name*Within Group*Sent To*Returnable')==false )
		{
			return;
		}	
		
		if ($("#cbo_returnable").val()==2 && $("#cbo_group").val()==1)
		{
			var cbo_company = $("#cbo_company_name").val();
			var cbo_sent_to = $("#txt_sent_to").val();
			var cbo_within_group = $("#cbo_group").val();
			//alert(cbo_company);
			page_link='requires/get_pass_entry_controller.php?action=against_returnable_gate_pass_popup&cbo_company='+cbo_company+'&cbo_sent_to='+cbo_sent_to+'&cbo_within_group='+cbo_within_group;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Against Gate Pass Popup', 'width=900px, height=350px, center=1, resize=0, scrolling=0','');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var sysNumber=(this.contentDoc.getElementById("hidden_gate_pass_number").value).split('_'); // system number
				//var sysID=(this.contentDoc.getElementById("hidden_sys_id").value).split('_'); // system id
				//alert(sysNumber);
				if(sysNumber!="")
				{
					freeze_window(5);
					$("#txt_returnable_gate_pass").val(sysNumber[1]);					
					get_php_form_data(sysNumber[1], "populate_returnable_master_from_data", "requires/get_pass_entry_controller" );
					show_list_view(sysNumber[0],'show_returnable_update_list_view','cut_details_container','requires/get_pass_entry_controller','');
					//set_button_status(1, permission, 'fnc_getpass_entry',1,1);
					$("#cbo_company_name").attr("disabled",true);
					$("#cbo_basis").attr("disabled",true);
					$("#txt_chalan_no").attr("disabled",true);
					$("#cbo_group").attr("disabled",true);
					$("#txt_sent_to").attr("disabled",true);
					//$("#cbo_department_name").attr("disabled",true);
					$("#cbo_returnable").attr("disabled",true);
					//$("#cbo_location").attr("disabled",true);
					//$("#cbo_com_location_id").attr("disabled",true);
					var basis=$("#cbo_basis").val()*1;
					if (basis == 50) var row_num=$('#tbl_order_details tbody tr:not(#total_display_last_tr)').length;
					else var row_num=$('#tbl_order_details tbody tr').length;
					//var row_num=$('#tbl_order_details tbody tr').length;
					
					for(var j=1;j<=row_num;j++)
					{ 
						//alert(response[3]);
						$("#cboitemcategory_"+j).attr("disabled",true);
						
						var sam=$("#cbosample_"+j).val();
						$('#cbosample_'+j).attr('disabled','disabled');
						//alert(sam);
						
						$("#txtitemdescription_"+j).attr("disabled",true);
						//$("#txtquantity_"+j).attr("disabled",true);
						$("#cbouom_"+j).attr("disabled",true);
						$("#txtrate_"+j).attr("disabled",true);
						$("#txtamount_"+j).attr("disabled",true);
						$("#txtorder_"+j).attr("disabled",true);
					}
					release_freezing();
				}
			}			//alert($("#cbo_returnable").val());
		}else{
			alert("Against returnable gate pass will only work for Within Group=yes and Returnable=No");return;
		}
	}

	function returnable_item_pupup() 
	{
		if( form_validation('txt_system_id','Gate Pass ID')==false )
		{
			return;
		}	
		
		if($("#cbo_returnable").val()==1)
		{
			var cbo_company = $("#cbo_company_name").val();
			var txt_system_id = $("#txt_system_id").val();
			var update_id = $("#update_id").val();

			page_link='requires/get_pass_entry_controller.php?action=returnable_item_dtls_pupup&cbo_company='+cbo_company+'&txt_system_id='+txt_system_id+'&update_id='+update_id;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Returnable Item Popup', 'width=900px, height=350px, center=1, resize=0, scrolling=0','');
			emailwindow.onclose=function(){}
		}
		else
		{
			alert("Returnable=No");return;
		}
	}

	function print_button_setting()
	{
		get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/get_pass_entry_controller');
	}
</script>
<body onLoad="set_hotkey();returnable_change(document.getElementById('cbo_returnable').value);sent_to_check_empty(document.getElementById('cbo_group').value);">
    <div style="width:100%;" align="center">
    	<? echo load_freeze_divs ("../",$permission);  ?>
    	<form name="getpass_1" id="getpass_1">
        <fieldset style="width:1150px; float:left">
            <legend>Gate Pass</legend>
    		<fieldset style="width:1150px;">
                <table width="100%" cellpadding="0" cellspacing="1" id="tbl_master">
                    <tr>
                        <td colspan="8" align="center"><b>Gate Pass ID</b>
                        	<input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:160px" placeholder="Double Click To Search" onDblClick="openmypage_system();"  readonly />
							<input type="hidden" id="txt_issue_no" name="txt_issue_no" value="" />
							<input type="hidden" id="update_id" name="update_id" value="" />                        
                        	<input type="hidden" name="hidden_entry_form" id="hidden_entry_form" />
                         </td>
                    </tr>
                    <tr>
                        <td width="130" align="right" class="must_entry_caption">Company Name</td>
                        <td width="150">
							<?  //load_drop_down( 'requires/get_pass_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' )
                            	echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  $company_credential_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected,"company_onchange();load_drop_down( 'requires/get_pass_entry_controller',this.value, 'load_drop_down_com_location', 'com_location_td' );load_drop_down( 'requires/get_pass_entry_controller',this.value, 'load_drop_down_com_department', 'com_department_td' );print_button_setting();","0" );
                            ?>
                            <input type="hidden" name="variable_roll" id="variable_roll" value="0">
                        </td>
                        <td width="130" align="right" class="must_entry_caption">Basis</td>
                        <td width="150" >
							<? // Challan sub.con grey fabric
                            	echo create_drop_down( "cbo_basis", 152, $get_pass_basis,"",1, "-- Select --", 0, "basis_onchange(this.value);load_drop_down( 'requires/get_pass_entry_controller', this.value, 'load_drop_down_chalan', 'chalan_td');load_drop_down( 'requires/get_pass_entry_controller', this.value, 'load_drop_down_purpose', 'purpose_td');focace_change();" ); 
                            ?> 
                        </td>
                        <td width="130" align="right" class="must_entry_caption">System ID/Challan No</td>
                        <td width="150" id="chalan_td">
                        	<input type="text" name="txt_chalan_no" id="txt_chalan_no" class="text_boxes" style="width:140px;" placeholder="Browse Or Scan" onDblClick="chalan_popup()">							
                        </td>
						
                        <td width="130" align="right" class="must_entry_caption">From Location</td>
                        <td width="150" id="com_location_td" >
                        	<? 
                           		echo create_drop_down( "cbo_com_location_id", 152, $blank_array,"", 1, "-- Select  --", 0, "",0 );
                            ?>
                        </td>
                    </tr>
                    <tr>                       
                        <td align="right" class="must_entry_caption">Department</td>
                        <td id="com_department_td" >
							<? 
								echo create_drop_down( "cbo_department_name", 152, $blank_array,"", 1, "-- Select  --", 0, "",0 );
								// echo create_drop_down( "cbo_department_name", 150, "select id,department_name from  lib_department  where status_active=1 and is_deleted=0  order by department_name","id,department_name", 1, "-- Select Department --", $selected,"load_drop_down( 'requires/get_pass_entry_controller', this.value, 'load_drop_down_section', 'section_td');","0" );
                            ?>
                        </td>
                        <td align="right">Section</td>
                        <td id="section_td">
                        	<? 
                        		//echo create_drop_down( "cbo_section", 150, "select id,section_name from  lib_section where status_active=1 order by section_name","id,section_name",1, "-- Select --", 0, "" ); 
                        		echo create_drop_down( "cbo_section", 152, $blank_array,"",1, "-- Select --", 0, "" ); 
                        	?> 
                        </td>
                        <td align="right" class="must_entry_caption">Within Group</td>
                        <td >
							<? 
								//load_drop_down( 'requires/get_pass_entry_controller', this.value+'_'+document.getElementById('cbo_basis').value, 'load_drop_down_sent', 'sent_td')
                            	echo create_drop_down( "cbo_group", 152, $yes_no,"", 1, "-- Select Group --", 0, "sent_to_check(this.value);check_within_group_status(this.value);sent_to_check_empty(this.value);auto_load_sent_to(this.value+'_'+document.getElementById('cbo_basis').value);",0 );
                            ?>
                        </td>                        
                        <td  align="right" class="must_entry_caption">Sent To</td>
                        <td id="sent_td">
                        	<input type="text" name="txt_sent_to" id="txt_sent_to" class="text_boxes" style="width:140px;">
                        	<input type="hidden" name="hidden_sent_to_name" id="hidden_sent_to_name">
                        	<input type="hidden" name="txt_sent_to_hid" id="txt_sent_to_hid">
                        </td>
                       
                    </tr>
                    <tr>
                        <td align="right" class="must_entry_caption">Date</td>
                        <td >
                        	<input class="datepicker" type="text" style="width:140px;" name="txt_rece_date" id="txt_rece_date" value="<? echo date("d-m-Y"); ?>"   placeholder="Select Date" />
                        </td>
                        <td align="right" class="must_entry_caption">Time</td>
                        <td>
                            <input type="text" name="txt_start_hours" id="txt_start_hours" class="text_boxes_numeric" placeholder="Hours" style="width:62px;" onKeyUp="fnc_move_cursor(this.value,'txt_start_hours','txt_start_minuties',2,23);" value="<? echo date('H');?>" />
                            <input type="text" name="txt_start_minuties" id="txt_start_minuties" class="text_boxes_numeric" placeholder="Minutes" style="width:62px;" onKeyUp="fnc_move_cursor(this.value,'txt_start_minuties','txt_start_date',2,59)" value="<? echo date('i');?>" />
                        </td>
                        <td align="right">To Location</td>
                        <td id="location_td">
                        	<? 
                          	// echo create_drop_down( "cbo_location", 150, $blank_array,"", 1, "-- Select  --", 0, "",0 );
						  
                            ?>
                            <input type="text" name="cbo_location" id="cbo_location" class="text_boxes" style="width:140px;">
                        </td> 
                        <td align="right">Attention</td>
                        <td >
                        	<input type="text" name="txt_attention" id="txt_attention" class="text_boxes" style="width:140px;"  />
                        </td>                       
                    </tr>
                    <tr>
                        
                        <td align="right">Returnable</td>
                        <td>
							<? 
                            	echo create_drop_down( "cbo_returnable", 152, $yes_no,"", 1, "-- Select  --", 2, "returnable_change(this.value)",0 );
                            ?>
                        </td>
                        <td align="right" >Est. Return Date</td>
                        <td >
                        	<input class="datepicker" type="text" style="width:140px;" name="txt_return_date" id="txt_return_date"  placeholder="Select Date" />
                        </td>
                        <td align="right" >Delivery As</td>
                        <td>
							<?
								//$basis_arr=array(1=>"Short",2=>"Additional",3=>"Sample",4=>"Bulk");
								echo create_drop_down( "cbo_delevery_as", 152, $basis_arr,"", 1, "-- Select  --", 0, "",0 );
                            ?>
                        </td>
                        <td align="right">Purpose</td>
                        <td id="purpose_td">
                            <input type="text" name="cbo_issue_purpose" id="cbo_issue_purpose" class="text_boxes" style="width:140px;"  />
                            <input type="hidden" name="hidden_purpose_id" id="hidden_purpose_id" />
                        </td>
                    </tr>
                    <tr>                        
                        <td align="right" class="must_entry_caption"> Carried by</td>
                        <td >
                        	<input type="text" name="txt_carried_by" id="txt_carried_by" class="text_boxes" style="width:140px;"  />
                        </td>
                         <td align="right" class="must_entry_caption">Sent By</td>
                        <td >
                        	<input type="text" name="txt_sent_by" id="txt_sent_by" class="text_boxes" style="width:140px;">
                        </td>
                        <td align="right" class="">Vehicle Number</td>
                        <td id="">
                        	<input type="text" name="txt_vhicle_number" id="txt_vhicle_number" class="text_boxes" style="width:140px;">
                        </td>
                        <td align="right" class="">D.O No</td>
                        <td>
                        	<input type="text" name="txt_do_no" id="txt_do_no" class="text_boxes" style="width:140px;">
                        </td>
                    </tr>
                    <tr>
                    	<td align="right" class="">Mobile No</td>
                        <td>
                        	<input type="text" name="txt_mobile" id="txt_mobile" class="text_boxes" style="width:140px;">
                        </td>
                        <td align="right" class="">Driver Name</td>
                        <td id="">
                        	<input type="text" name="txt_driver_name" id="txt_driver_name" class="text_boxes" style="width:140px;">
                        </td>
                        <td align="right" class="">Driver License No</td>
                        <td>
                        	<input type="text" name="txt_driver_license_no" id="txt_driver_license_no" class="text_boxes" style="width:140px;">
                        </td>
                         <td align="right" class="">Security Lock No</td>
                        <td>
                        	<input type="text" name="txt_security_lock_no" id="txt_security_lock_no" class="text_boxes" style="width:140px;">
                        </td>
                    </tr>
                    
                    <tr>
                    	
                        <td align="right" class="">Delivery Company</td>
                        <td>
                        	<input type="text" name="txt_delivery_company" id="txt_delivery_company" class="text_boxes" style="width:140px;">
                        </td>
                    	<td align="right" class="">Against Returnable Gate Pass</td>
                        <td>
                        	<input type="text" name="txt_returnable_gate_pass" id="txt_returnable_gate_pass" class="text_boxes" style="width:140px;" onDblclick="openmypage_returnable_gate_pass()" placeholder="Double click to browse">
                        	
                        </td>
                        <td align="right" class="">Remarks</td>
                        <td>
                        	<input type="text" name="txt_remarks_mst" id="txt_remarks_mst" class="text_boxes" style="width:140px;">
                        	<div style="display: none;"><? echo create_drop_down( "cbo_roll_by", 50, $yes_no,"",0, "--Select--",2 ,0,1 ); ?></div>
                        </td>
						<td align="right">Ready To Approved</td>
                        <td >
							<? echo create_drop_down( "cbo_ready_to_approved", 152, $yes_no,"", 1, "-- Select--", 2, "","","" ); ?>
                        </td> 
                    </tr>
					<tr>
					<td>&nbsp;</td>
                    <td><input type="button" class="image_uploader" style="width:130px" value="ADD IMAGE" onClick="file_uploader ( '../', document.getElementById('update_id').value,'', 'knit_order_entry', 0 ,1);"></td>
					<td>&nbsp;</td>
                    <td>
						<input type="button" class="image_uploader" style="width:130px" value="ADD FILE" onClick="file_uploader ( '../', document.getElementById('update_id').value,'', 'knit_order_entry', 2 ,1);">
					</td>
					<td id="app_status_display"></td>
					</tr>
                </table>
            </fieldset>
            <br>
            <fieldset style="width:1300px;">
            	<legend>Item Part</legend>
                <table width="1400" cellpadding="0" cellspacing="0" border="1" class="rpt_table" align="center" id="tbl_order_details" rules="all">
                    <thead>
                        <th width="120" align="center">Item Category</th>
                        <th width="120" align="center">Sample</th>
                        <th width="180" align="center">Item Description</th>
                        <th width="60" align="center">Quantity</th>
                        <th width="60" align="center">Reject Qty</th>
                        <th width="80" align="center">UOM</th>
                        <th width="70" align="center">Rate</th>
                        <th width="80" align="center">Amount</th>
                        <th width="70" align="center">No Of Bag/Roll/GMT</th>
                        <th width="70" align="center">Tot Carton Qty</th>
                        <th width="80" align="center">Order No</th>
                        <th width="120" id="systemid_th" align="center">System Id/ Challan</th>
                        <th width="100" align="center">Remarks</th>
                        <th width=""></th>
                    </thead>
                    <tbody id="cut_details_container">
                        <tr id="tr_1">
                            <td>
								<? echo create_drop_down( "cboitemcategory_1", 120,$item_category,"",1, "-- Select --", 0, "gate_enable_disable(1)" ); ?>
								<input type="hidden" name="challanIds_1" id="challanIds_1" value="">
                                <input type="hidden" name="challandtlsIds_1" id="challandtlsIds_1" value="">
							</td>
                            <td><? echo create_drop_down( "cbosample_1", 120, "select id,sample_name from lib_sample where status_active=1 order by sample_name","id,sample_name",1, "-- Select --", 0, "gate_enable_disable(2)" ); ?></td>
                            <td><input type="text" name="txtitemdescription_1" id="txtitemdescription_1" class="text_boxes" style="width:180px;">
                       <input type="hidden" name="prodID_1" id="prodID_1" value="">     
                            </td>
                            <td><input type="text" name="txtquantity_1" id="txtquantity_1" class="text_boxes_numeric" onKeyUp="fn_calculate_amount(this.id)" style="width:60px;"></td>
                            <td><input type="text" name="txtrejectqty_1" id="txtrejectqty_1" class="text_boxes_numeric" style="width:60px;"></td>
                            <td><? echo create_drop_down( "cbouom_1", 80, $unit_of_measurement,"", 1, "-- Select--", $selected, "",0 ); ?><input type="hidden" name="txtuomqty_1" id="txtuomqty_1" class="text_boxes"></td>
                            <td><input type="text" name="txtrate_1" id="txtrate_1" class="text_boxes_numeric" onKeyUp="fn_calculate_amount(this.id)" style="width:70px"></td>
                            <td><input type="text" name="txtamount_1" id="txtamount_1" class="text_boxes_numeric" style="width:80px" readonly disabled></td>

                            <td><input type="text" name="txtbag_1" id="txtbag_1" class="text_boxes_numeric" style="width:70px"></td>
                            <td><input type="text" name="txtcartonqnty_1" id="txtcartonqnty_1" class="text_boxes_numeric" style="width:70px"></td>

                            <td><input type="text" name="txtorder_1" id="txtorder_1" class="text_boxes" style="width:80px" placeholder="Browse"  onDblClick="openmypage_order(this.id);" readonly>
                             <input type="hidden" id="txtorderidhidden_1" name="txtorderidhidden_1"  style="width:20px" value="" />
                            </td>

                            <td>
                            	<input readonly disabled type="text" name="systemid_1" id="systemid_1" class="text_boxes" style="width:120px">
                            </td>
                           
                            <td><input type="text" name="txtremarks_1" id="txtremarks_1" class="text_boxes" style="width:100px">
                            <input type="hidden" id="updatedtlsid_1" name="updatedtlsid_1" value="" />
                             <input type="hidden" id="hiddenissalesflag_1" name="hiddenissalesflag_1" value="" />
                            </td>

                            <td> 
                            	<input type="button" id="increase_1" style="width:25px" class="formbutton" value="+" onClick="add_break_down_tr(1)" />
                                <input type="button" id="decrease_1" style="width:25px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1,'tbl_order_details');" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
            <br>
            <table cellpadding="0" cellspacing="1" width="100%">
                <tr>
                    <td align="center" colspan="7" valign="middle" class="button_container">
                    	<input type="button" style="width:80px;" name="print_left_over" id="print_left_over" class="formbutton" onClick="print_to_html_report(111)"  value="Left Over">
						<input type="button" style="width:80px;" name="print_akh_print" id="print_akh_print" class="formbutton" onClick="print_to_html_report(222)"  value="Print_222">
						<input type="button" style="width:80px;" name="dot_print1" id="dot_print1" class="formbutton" onClick="print_to_html_report(27)"  value="Dot_Print_1">
                        <? echo create_drop_down( "cbo_template_id", 85, $report_template_list,'', 0, '', 0, ""); ?>						
                        <? echo load_submit_buttons( $permission, "fnc_getpass_entry", 0,0,"fnResetForm();",1); ?>
                        <input type="button" name="returnable_item_dtls" value="Returnable Item Details" id="returnable_item_dtls" class="formbutton_disabled" onClick="returnable_item_pupup()"/>
                        <span id="button_data_panel"></span>                           
                    </td>
                </tr> 
            </table> 
        </fieldset>
        <br>
        <fieldset style="width:870px;"><div style="width:950px;" id="list_container"></div></fieldset>
    	</form>
    </div>
</body>
<script>	
//load_drop_down( 'requires/get_pass_entry_controller', document.getElementById('cbo_group').value+'_'+document.getElementById('cbo_basis').value, 'load_drop_down_sent', 'sent_td');
</script>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>