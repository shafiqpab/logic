<?php
/*--- -----------------------------------------
Purpose			:	This page will display soft conning
Functionality	:
JS Functions	:
Created by		:	Samiur
Creation date 	:	28-04-20
Updated by 		:   
Update date		:   
Oracle Convert 	:
Convert date	:
QC Performed BY	:
QC Date			:
Comments		:
*/
session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$user_level=$_SESSION['logic_erp']["user_level"];

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Soft Conning Production Entry", "../../", 1, 1, $unicode, 0, "");
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php"; 
	var permission="<? echo $permission; ?>";

	function put_data_into_dtls(id, action, controller_path)
	 { // function copied from gray_production_entry put_data_dtls_part
		/*alert(id);return;*/
		//freeze_window();
		reset_form('','','txtWindingCone*txtPrdctnQty*txtRemarks','','');
		get_php_form_data(id, 'populate_soft_conning_from_list', 'requires/yd_soft_conning_production_entry_controller');
		document.getElementById('txtSalesOrder').setAttribute('disabled', 'disabled'); 
		var production_id=document.getElementById('txtProductionId').value;
		/*alert(production_id);*/
		/*if(production_id==''){
			set_button_status(0, permission, 'fnc_soft_conning',1);

		}else{
			set_button_status(1, permission, 'fnc_soft_conning',1);

		}
		*/
		
		set_button_status(0, permission, 'fnc_soft_conning',1);
		release_freezing();
	}

	function put_data_into_form(id, action, controller_path)
	 {
 		var  data = id.split("_");
 		var prod_dtls_id=data[0];
		var job_dtls_id=data[1];
  		//$('#cbo_company_name').removeAttr('disabled');
		//$('#cbo_party_name').removeAttr('disabled');
		$('#txtProductionDate').removeAttr('disabled');
		$('#txtStartDate').removeAttr('disabled');
		$('#txtEndDate').removeAttr('disabled');
		//$('#cbo_yarn_type').removeAttr('disabled');
		$('#txtBobbinType').removeAttr('disabled');
		$('#txtWindingCone').removeAttr('disabled');
		$('#txtPrdctnQty').removeAttr('disabled');
		$('#txtRemarks').removeAttr('disabled');
		document.getElementById('update_dtls_id').value = prod_dtls_id;
 		reset_form('','sales_order_list','txtWindingCone*txtPrdctnQty*txtRemarks','','');
		get_php_form_data(job_dtls_id, 'populate_soft_conning_from_list', 'requires/yd_soft_conning_production_entry_controller');
		get_php_form_data(prod_dtls_id, 'populate_saved_data_from_list', 'requires/yd_soft_conning_production_entry_controller');
  		set_button_status(1, permission, 'fnc_soft_conning',1);
		release_freezing();
	}
	function openmypage_production_id()
    { 
        var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_party_name').value;
        var page_link='requires/yd_soft_conning_production_entry_controller.php?action=production_popup&data='+data;
        var title="Issue ID";
        
        emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=900px, height=400px, center=1, resize=0, scrolling=0','../')
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0]//("search_subcontract_frm"); //Access the form inside the modal window
            //var theemail=this.contentDoc.getElementById("selected_job");
            var theemail=this.contentDoc.getElementById("selected_production").value;
            /*alert (theemail); */
            var splt_val=theemail.split("_");

           /* alert(splt_val);*/
            if (splt_val[0]!='')
			 {
				 
				document.getElementById('txtStyle').setAttribute('disabled', 'disabled');
				document.getElementById('txtSalesOrder').setAttribute('disabled', 'disabled'); 
				document.getElementById('cbo_company_name').setAttribute('disabled', 'disabled'); 
				document.getElementById('cbo_party_name').setAttribute('disabled', 'disabled');
				 document.getElementById('cbo_yarn_type').setAttribute('disabled', 'disabled'); 
 				show_list_view(splt_val[1], 'create_sales_order_list', 'sales_order_list', 'requires/yd_soft_conning_production_entry_controller', '');
				get_php_form_data( splt_val[0], "load_php_data_to_form", "requires/yd_soft_conning_production_entry_controller" );
				show_list_view(splt_val[0], 'create_production_order_list', 'production_list', 'requires/yd_soft_conning_production_entry_controller', '');
				set_button_status(0, permission, 'fnc_soft_conning',1);
			}
        }
    }

	function openSalesNumPopup() 
	{
		if( form_validation('cbo_company_name', 'Company')==false ) {
			return;
		}
		var data = document.getElementById('cbo_company_name').value;
		page_link='requires/yd_soft_conning_production_entry_controller.php?action=job_popup&data='+data;
		title='Search by Sales Order Number';

		popupWindow=dhtmlmodal.open('searchBox', 'iframe', page_link, title, 'width=635px, height=350px, center=1, resize=0, scrolling=0', '../')
		popupWindow.onclose=function() {
			var theform=this.contentDoc.forms[0];
			var job_id_mst=this.contentDoc.getElementById('selected_order_id').value;
			
			var response_data=job_id_mst.split(',');
			var company = $('#cbo_company_name').val();
			//alert(response_data[0]);
			// console.log(job_id_mst);
			if (job_id_mst!='') 
			{
				freeze_window(5);
				if(response_data[1]==1)
				{
					//load_drop_down( 'requires/yd_soft_conning_production_entry_controller', '', 'load_drop_down_buyer', 'party_td' );
					load_drop_down( 'requires/yd_soft_conning_production_entry_controller', company+'_'+1, 'load_drop_down_buyer', 'party_td' );
				}
				else
				{
					//load_drop_down( 'requires/yd_soft_conning_production_entry_controller', '', 'load_drop_down_buyer', 'party_td' );
					load_drop_down( 'requires/yd_soft_conning_production_entry_controller', company+'_'+2, 'load_drop_down_buyer', 'party_td' );
				}
				document.getElementById('cbo_party_name').value = response_data[2];
				
				show_list_view(response_data[0]+'_'+response_data[1], 'create_sales_order_list', 'sales_order_list', 'requires/yd_soft_conning_production_entry_controller', '');
 				release_freezing();
 				document.getElementById('cbo_company_name').value=data;
				$('#cbo_company_name').attr('disabled','disabled');
				$('#cbo_party_name').attr('disabled','disabled');
				 
			}
		}
	}
	function fnc_soft_conning( operation )
    {
        if ( form_validation('txtSalesOrder*cbo_company_name*cbo_party_name*txtProductionDate*txtStartDate*txtEndDate*txtPrdctnQty', 'Sales Order No*Company Name*Party*Production Date*Start Date*End Date*Production Quantity')==false )
        {
           return;
        }
        
       if(document.getElementById('txtIssueQty').value * 1 <= 0)
		{
			var bgcolor='-moz-linear-gradient(bottom, rgb(254,151,174) 0%, rgb(255,255,255) 10%, rgb(254,151,174) 96%)';
        	document.getElementById('txtIssueQty').focus();
			document.getElementById('txtIssueQty').style.backgroundImage=bgcolor;
			$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
			{
				$(this).html('Issue Qty field must be a positive number').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
			});
			return 0;
        }

        if(check_issue_balance(operation)==false)
        {
        	return;

        }
        else
        {
        	var data_str="";
		    var data_str=get_submitted_data_string('txtProductionId*txtStyle*txtSalesOrder*cbo_company_name*cbo_party_name*txtProductionDate*txtStartDate*txtEndDate*cbo_yarn_type*txtBobbinType*txtWindingCone*txtPrdctnQty*txtRemarks*txtLot*txtCount*txtComposition*txtYdColor*txtPkg*txtWghtCone*txtIssueQty*txtPrdcBlQty*update_id*sales_order_serial*update_dtls_id*job_dtls_id*order_id*job_id*within_group*booking_without_order*booking_type*sales_order_id*product_id*previous_production_qty',"../../");
		        //alert(data_str);
		        
		    var data="action=save_update_delete&operation="+operation+data_str;//+'&zero_val='+zero_val
		        /*alert(data);return;*/
		    freeze_window(operation);
		    http.open("POST","requires/yd_soft_conning_production_entry_controller.php",true);
		    http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		    http.send(data);
		    http.onreadystatechange = fnc_soft_conning_response;

        }
     
    }

    
    function fnc_soft_conning_response()
    {
        if(http.readyState == 4) 
        {
           	
			var response=trim(http.responseText).split('**');
             show_msg(response[0]);
			 
			if(response[0]==20)
            {
                alert(response[1]);
				release_freezing();
				return;
				
            }
            if(response[0]==0 || response[0]==1)
            {
                  document.getElementById('txtProductionId').value= response[1];
                   document.getElementById('update_id').value = response[2];
                  if(response[0]==0 )
				  {

                	document.getElementById('sales_order_serial').value="";
                	document.getElementById('txtPrdcBlQty').value=parseInt(document.getElementById('txtPrdcBlQty').value)-parseInt(document.getElementById('txtPrdctnQty').value);
				 }
				 else
				 {
						document.getElementById('txtPrdcBlQty').value=(parseInt(document.getElementById('txtPrdcBlQty').value)+parseInt(document.getElementById('previous_production_qty').value))-parseInt(document.getElementById('txtPrdctnQty').value);
	
				  }
                  document.getElementById('cbo_company_name').setAttribute('disabled', 'disabled');
                 document.getElementById('cbo_party_name').setAttribute('disabled', 'disabled');
                 document.getElementById('cbo_yarn_type').setAttribute('disabled', 'disabled');
                 var job_id_mst=response[2];
				show_list_view(response[4], 'create_sales_order_list', 'sales_order_list', 'requires/yd_soft_conning_production_entry_controller', '');
                show_list_view(job_id_mst, 'create_production_order_list', 'production_list', 'requires/yd_soft_conning_production_entry_controller', '');
				set_button_status(0, permission, 'fnc_soft_conning',1);
               
            }
            if(response[0]==2)
            {
                reset_fnc();
            }
            release_freezing();
        }
    }

	function check_issue_balance(operation)
    {
		var balance_qty=Number(parseFloat(document.getElementById('txtPrdcBlQty').value).toFixed(2));
    	var current_production=Number(parseFloat(document.getElementById('txtPrdctnQty').value).toFixed(2));	
		//alert("Cur: "+current_production+" Balance: "+balance_qty); return;
    	if(operation==0)
    	{//for save
        	if(current_production>balance_qty){
        		alert("Current Production Quantity is Greater than Available Issue Quantity");return false;
        	}else{
        		return true;
        	}
        }
        else if(operation==1){

        	
        	//var issue_available = issue_available + parseFloat(document.getElementById('previous_production_qty').value).toFixed(2);
        	var balance_qty= Number(balance_qty) +  Number(parseFloat(document.getElementById('previous_production_qty').value).toFixed(2));
			//alert("bal: "+balance_qty+" previous_production_qty "+parseFloat(document.getElementById('previous_production_qty').value));
        	if(current_production>balance_qty){
        		alert("Current Production Quantity is greater than Available Issue Quantity");return false;
        	}else{
        		return true;
        	}

        }
        else{
        	return true;
        }

    }
	
	function check_production_balance(operation)
    {

    		
    		if(operation==0)
    		{//for save
        	var available_production=parseInt(document.getElementById('txtPrdcBlQty').value);
        	var current_production=parseInt(document.getElementById('txtPrdctnQty').value);
        	if(current_production>available_production){
        		alert("Current Production Quantity is Greater than Available Production Quantity");return false;
        	}else{
        		return true;
        	}
        }
        else if(operation==1){

        	
        	var available_production=parseInt(document.getElementById('txtPrdcBlQty').value)+parseInt(document.getElementById('previous_production_qty').value);
        	var current_production=parseInt(document.getElementById('txtPrdctnQty').value);
        	if(current_production>available_production){
        		alert("Current Production Quantity greater than Available Production");return false;
        	}else{
        		return true;
        	}

        }
        else{
        	return true;
        }

    }
    
	
    function reset_fnc()
    {
        location.reload(); 
    }
     
     function ResetForm()
    {
        reset_form('yarnissue_1','issue_list_view','','cbouom_1,1', "$('#details_tbl tbody tr:not(:first)').remove(); disable_enable_fields('cbo_company_name*cbo_within_group*cbo_party_name',0)")
    }
</script>
</head>
<body onLoad="set_hotkey();">
<div>
	<?php echo load_freeze_divs("../../", $permission); ?>
	<form name="softconning_1" id="softconning_1" autocomplete="off">
		<fieldset style="width: calc(50% - 20px);; margin-left:20px; position:relative; float: left;">
			<table width="100%">
				<tr>
					<td>
						<!-- left table start -->
						<legend style="width:90%;">Soft Conning</legend>							
						<table border="0">
							<tr>
								<td align="right">Production ID</td>
								<td>
									<input class="text_boxes" type="text" placeholder="Double click" onDblClick="openmypage_production_id('xx','Soft Conning Production Entry')" name="txtProductionId" id="txtProductionId" readonly/>
									<input type="hidden" name="update_id" id="update_id">
									<input type="hidden" name="sales_order_serial" id="sales_order_serial">
									<input type="hidden" name="update_dtls_id" id="update_dtls_id">
									<input type="hidden" name="job_dtls_id" id="job_dtls_id">
									<input type="hidden" name="order_id" id="order_id">
									<input type="hidden" name="job_id" id="job_id">
									<input type="hidden" name="within_group" id="within_group">
									<input type="hidden" name="booking_without_order" id="booking_without_order">
									<input type="hidden" name="booking_type" id="booking_type">
									<input type="hidden" name="sales_order_id" id="sales_order_id">
									<input type="hidden" name="product_id" id="product_id">
									<input type="hidden" name="previous_production_qty" id="previous_production_qty">
									
									
								</td>
							</tr>
							<tr>
								<td align="right">Style No</td>
								<td>
									<input class="text_boxes" width="150" type="text" name="txtStyle" id="txtStyle" readonly/>
								</td>
							</tr>
							<tr>
								<td align="right" class="must_entry_caption">Sales/Job Order No</td>
								<td>
									<input class="text_boxes" width="150" type="text"  placeholder="Double click" onDblClick="openSalesNumPopup();" name="txtSalesOrder" id="txtSalesOrder" readonly />
								</td>
							</tr>
							<tr>
								<td align="right" class="must_entry_caption">Company Name</td>
								<td id="company_td">
									<? echo create_drop_down( "cbo_company_name", 142, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/yd_soft_conning_production_entry_controller','', 'load_drop_down_buyer', 'party_td' );"); ?>
								</td>
							</tr>
							<tr>
								<td align="right">Party</td>
								<td id="party_td">
									<? echo create_drop_down( "cbo_party_name", 142, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, ""); ?>
								</td>
							</tr>
							<tr>
								<td align="right" class="must_entry_caption">Production Date</td>
								<td>
									<input class="datepicker" type="text"  name="txtProductionDate" id="txtProductionDate" style="width:132px;" placeholder="Click to show calendar" />
								</td>
							</tr>
							<tr>
								<td align="right" class="must_entry_caption">Start Date</td>
								<td>
									<input class="datepicker" type="text"  name="txtStartDate" id="txtStartDate" style="width:132px;" placeholder="Click to show calendar" />
								</td>
							</tr>
							<tr>
								<td align="right" class="must_entry_caption">End Date</td>
								<td>
									<input class="datepicker" type="text"  name="txtEndDate" id="txtEndDate" style="width:132px;" placeholder="Click to show calendar" />
								</td>
							</tr>
							<tr>
								<td align="right">Yarn Type</td>
								<td>
									<?php
										 
									 	echo create_drop_down('cbo_yarn_type', 142, $yarn_type, '', 1, '-- Yarn Type --', $selected, '', 1, '', '', '', 212);
									  
									?>
								</td>
							</tr>
							<tr>
								<td align="right">Bobbin Type</td>
								<td>
									<?php
										// $bobbin_arr = array("Bobbin Type 1", "Bobbin Type 2", "Bobbin Type 3");
									 	// echo create_drop_down( "txtBobbinType", 117, $bobbin_arr, 1, "-- Bobbin Type --", $selected, "");
									?>
									<input class="text_boxes" width="150" type="text" name="txtBobbinType" id="txtBobbinType" />
								</td>
							</tr>
							<tr>
								<td align="right">Winding Cone Qty/Pcs</td>
								<td>
									<input class="text_boxes_numeric" type="text"  placeholder="Cone Qty in pcs" name="txtWindingCone" id="txtWindingCone" />
								</td>
							</tr>
							<tr>
								<td align="right" class="must_entry_caption">Production Qty</td>
								<td>
									<input class="text_boxes_numeric" type="text"  placeholder="Production Qty" name="txtPrdctnQty" id="txtPrdctnQty" />
								</td>
							</tr>
							<tr>
								<td align="right">Remarks</td>
								<td>
									<input class="text_boxes" type="text" placeholder="Remarks" name="txtRemarks" id="txtRemarks" />
								</td>
							</tr>
						</table>
						<!-- left table end -->
					</td>
					<td valign="top" align="right">
						<!-- display table start -->
						<legend style="width:90%;">Display</legend>
						<table border="0" style="margin-left:15px;">
							<tr>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<td align="right">Lot:</td>
								<td>
									<input class="text_boxes" width="150" type="text"  placeholder="Display" name="txtLot" id="txtLot" readonly />
								</td>
							</tr>
							<tr>
								<td align="right">Count:</td>
								<td>
									<input class="text_boxes_numeric" width="150" type="text"  placeholder="Display" name="txtCount" id="txtCount" readonly/>
								</td>
							</tr>
							<tr>
								<td align="right">Composition:</td>
								<td>
									<input class="text_boxes" width="150" type="text"  placeholder="Display" name="txtComposition" id="txtComposition" readonly/>
								</td>
							</tr>
							<tr>
								<td align="right">Y/D Color:</td>
								<td>
									<input class="text_boxes" width="150" type="text"  placeholder="Display" name="txtYdColor" id="txtYdColor" align="left" readonly/>
								</td>
							</tr>
							<tr>
								<td align="right">No of Package:</td>
								<td>
									<input class="text_boxes_numeric" width="150" type="text" placeholder="Display"  name="txtPkg" id="txtPkg" readonly />
								</td>
							</tr>
							<tr>
								<td align="right">AVG. Weight Per Cone:</td>
								<td>
									<input class="text_boxes_numeric" width="150" type="text" placeholder="Display"  name="txtWghtCone" id="txtWghtCone" readonly />
								</td>
							</tr>
							<tr>
								<td align="right">Issue Qty:</td>
								<td>
									<input class="text_boxes_numeric must_entry_caption" width="150" type="text" placeholder="Display"  name="txtIssueQty" id="txtIssueQty" readonly/>
								</td>
							</tr>
							<tr>
								<td align="right">Previous Production Qty:</td>
								<td>
									<input class="text_boxes_numeric" width="150" type="text" placeholder="Display"  name="preProdQty" id="preProdQty" readonly/>
								</td>
							</tr>
							<tr>
								<td align="right">Production Balance Qty:</td>
								<td>
									<input class="text_boxes_numeric" width="150" type="text" placeholder="Display"  name="txtPrdcBlQty" id="txtPrdcBlQty" readonly/>
								</td>
							</tr>
						</table>
						<!-- display table end -->
					</td>
				</tr>
				<tr> <!-- this row is to show some blank space before the submit buttons -->
					<td colspan="2" style="padding:10px;">&nbsp;</td>
				</tr>
				<tr style="margin-top:50px;">
					<td colspan="2" align="center">
						<? echo load_submit_buttons($permission, "fnc_soft_conning", 0,0,"",1); ?>
					</td>
				</tr>
			</table>		
		</fieldset>
		
	</form>
	<div style="float:left; margin-left:10px; margin-top:5px; width: 500px;" id="sales_order_list">
        
    </div>
    
  <div style="width: 800px" id="production_list"></div>  
   
</div>




</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>