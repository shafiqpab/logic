<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Operator Wise Cutting Entry V2
Functionality	:	
JS Functions	:
Created by		:	Shafiq 
Creation date 	: 	13-09-2023
Updated by 		: 	
Update date		: 	
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
 
require_once('../includes/common.php');
extract($_REQUEST);

$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Operator Wise Cutting Entry V2", "../", 1,1, $unicode,1,'');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';
	var selected_id = new Array(); var selected_currency_id = new Array();
	var selected_id_listed = new Array();
	var selected_id_removed = new Array(); 
	
	
	function fnc_operator_wise_cutting_entry( operation )
	{
		freeze_window(operation);
		if(operation==2)
		{			
			show_msg('13');
			return;		
		}
		
		if ( form_validation('cbo_company_id*cbo_location_name*txt_cuitting_date','Company Name*Location*Cutting Date')==false )
		{
			return;
		}
		
		// var row_num=$('#tbl_list_search tbody tr').length-1;
		var row_num=$('#tbl_list_search tbody tr.activeRow').length;
		// alert(row_num);
		var dataString=""; var j=0;
		for (var i=1; i<=row_num; i++)
		{
			var cuttingdata=$('#empWiseCuttingData_'+i).val();
			var jobid=$('#jobId_'+i).val();
			var orderid=$('#orderId_'+i).val();
			var gtmsid=$('#gtmsId_'+i).val();
			var colorId=$('#colorId_'+i).val();
			var styleId=$('#styleRefNo_'+i).val();
			var cutNo=$('#cutNo_'+i).val();
			var tblNo=$('#tblNo_'+i).val();
			var mstId=$('#mstId_'+i).val();
			
			if(cuttingdata!="")
			{
				j++;
				dataString+='&empWiseCuttingData_' + j + '=' + cuttingdata + '&jobId_' + j + '=' + jobid + '&orderId_' + j + '=' + orderid + '&gtmsId_' + j + '=' + gtmsid + '&colorId_' + j + '=' + colorId +'&styleRefNo_' + j + '=' + styleId+'&mstId_' + j + '=' + mstId+'&cutNo_' + j + '=' + cutNo+'&tblNo_' + j + '=' + tblNo;
			}
		}
		//alert(dataString);return;
		if(j<1)
		{
			alert('Please fill-up all input fields.');
			return;
		}
		
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_id*cbo_location_name*txt_cuitting_date',"../")+dataString+'&row_num='+row_num;
			//alert (data);return;
			// freeze_window(operation);
			http.open("POST","requires/operator_wise_cutting_entry_controller_v2.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_operator_wise_cutting_entry_reponse;
	}
	
	function fnc_operator_wise_cutting_entry_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert (http.responseText);
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);
			
			if(response[0]==0 || response[0]==1)
			{
				set_button_status(1, permission, 'fnc_operator_wise_cutting_entry',1);
				var cbo_company_id=$('#cbo_company_id').val();
				var location_id=$('#cbo_location_name').val();
				var txt_cuitting_date=$('#txt_cuitting_date').val();
				
				show_list_view($('#cbo_company_id').val()+'***'+$('#cbo_location_name').val()+'***'+$('#txt_cuitting_date').val(),'show_list_view','operator_wise_cutting_entry_list','requires/operator_wise_cutting_entry_controller_v2','setFilterGrid("tbl_list_search",-1);','','');
				
			}
			release_freezing();
		}
		// release_freezing();
	}

	var selected_id = new Array(); var selected_currency_id = new Array();

	 function toggle( x, origColor ) {
		//alert (x);
		var newColor = 'yellow';
		if ( x.style ) {
		x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}
		
	function cutting_popup(row)
	{
		var company = $("#cbo_company_id").val();
		var location_id=$("#cbo_location_name").val();
		// var floor_id=$("#cbofloorName_"+row).val();
		// var table_id=$("#cbotabcbotableName_lename_"+row).val();
		//var table_id=$("#cbotablename_"+row).val();
		var empWiseCuttingData=$("#empWiseCuttingData_"+row).val();
		/* if(floor_id==0 || table_id==0)
		{
			alert('Floor and Table required.');
			return;
		} */
		var page_link = 'requires/operator_wise_cutting_entry_controller_v2.php?action=today_cutting_popup&company=' + company + '&location=' + location_id + '&empWiseCuttingData=' + empWiseCuttingData;
		
		var title="Operator Wise Distribution";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var cutting_data=this.contentDoc.getElementById("data_string").value;
			let cut_qty = 0;
			let cut_data_arr = cutting_data.split('__');
			for (let index = 0; index < cut_data_arr.length; index++) 
			{
				const cut_data_arr_n = cut_data_arr[index].split('**');
				cut_qty += parseInt(cut_data_arr_n[4]);
			}
			$("#empWiseCuttingData_"+row).val(cutting_data);
			$("#txtcutting_"+row).val(cut_qty);
		}
	}

	function fn_get_table(id,floor_id)
	{
		var id_arr = id.split("_");
		var cbo_company_id=$('#cbo_company_id').val();
		var location_id=$('#cbo_location_name').val();
		var data = cbo_company_id+'**'+location_id+'**'+floor_id+'**'+id_arr[1];
		var container = "td_table_"+id_arr[1];
		//alert(container);
		load_drop_down( 'requires/operator_wise_cutting_entry_controller_v2', data,'load_dropdown_table', container );

	}
	
	function fnc_populate_search()
	{
		var cbo_company_id=$('#cbo_company_id').val();
		var location_id=$('#cbo_location_name').val();
		var txt_cuitting_date=$('#txt_cuitting_date').val();
	
			if( form_validation('cbo_company_id*cbo_location_name*txt_cuitting_date','Company Name*Location*cuitting_date')==false)
			{
				return;
			}
			
			show_list_view($('#cbo_company_id').val()+'***'+$('#cbo_location_name').val()+'***'+$('#txt_cuitting_date').val(),'show_list_view','operator_wise_cutting_entry_list','requires/operator_wise_cutting_entry_controller_v2','setFilterGrid("tbl_list_search",-1);','','');
		
	}

</script>
</head>
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
    	<? echo load_freeze_divs ("../",$permission);  ?>
    	<form name="dyinfinishbillissue_1" id="dyinfinishbillissue_1"  autocomplete="off"  >
			<fieldset style="width:800px;">
				<legend>Cutting Entry </legend>
				<table width="1000"  cellspacing="1" cellpadding="0" border="0" >
					<tr>
						<td width="800">
						<fieldset>
							<table cellpadding="0" cellspacing="2" width="100%">
							       
									
								<tr>
									<td  class="must_entry_caption" > Working Company </td>
									<td>
										<? 
											echo create_drop_down( "cbo_company_id",150,"select id, company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/operator_wise_cutting_entry_controller_v2', this.value, 'load_drop_down_location', 'location_td'); get_php_form_data(this.value,'load_variable_settings','requires/operator_wise_cutting_entry_controller_v2');","","","","","",2);
										?>
									</td>
									<td class="must_entry_caption"> Working Location<td>                                              
									<td  id="location_td">
										<? 
											echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "--Select Location--", $selected,"","","","","","",3);
										?>
									</td>
									<td class="must_entry_caption">Cutting Date:</td>                                              
									<td>
										<input class="datepicker" type="text" style="width:140px" name="txt_cuitting_date" id="txt_cuitting_date" tabindex="4" value="<? echo date('d-m-Y'); ?>"/>

										<td>&nbsp;</td>                                              
										<td><input class="formbutton" type="button" onClick="fnc_populate_search();" style="width:130px" name="btn_populate" value="Populate" id="btn_populate" />
									</td>
								
									
								</tr> 
							</table>
						</fieldset>
						</td>
					</tr>
					<tr>
						<td align="center"> 
						</td> 
				   </tr>   
				</table>
      		</fieldset>
       
        </form>
        <br>
        <div id="operator_wise_cutting_entry_list"></div>      
		<div style="margin-top:15px;">
		   <tr>
				<td colspan="17" align="center" class="button_container">
					<? 
					echo load_submit_buttons($permission,"fnc_operator_wise_cutting_entry",0,0,"reset_form('dyinfinishbillissue_1', '', '','')",1); 
					?> 
											
				</td>
		</tr> 
		<tr>
			<td colspan="13" id="list_view" align="center"></td>
		</tr>
		</div>                     
   </div>
</body>

<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
		
			