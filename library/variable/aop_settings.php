<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Order Tracking Variable Settings
					Select company and select Variable List that onchange will change content
Functionality	:	Must fill Company, Variable List
JS Functions	:
Created by		:	K.M Nazim Uddin 
Creation date 	: 	14-05-2019
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
echo load_html_head_contents("AOP Variable Settings", "../../", 1, 1,$unicode,'','');


if ($_SESSION['logic_erp']["data_level_secured"]==1) 
{
	if ($_SESSION['logic_erp']["buyer_id"]!=0 && $_SESSION['logic_erp']["buyer_id"]!="") $buyer_name=" and id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_name="";
	if ($_SESSION['logic_erp']["company_id"]!=0 && $_SESSION['logic_erp']["company_id"]!="") $company_name="and id in (".$_SESSION['logic_erp']["company_id"].")"; else $company_name="";
}
else
{
	$buyer_name="";
	$company_name="";
}
?>
 
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';
function fnc_order_tracking_variable_settings( operation )
{
	var cbo_sales_year_started_date="";
	var cbo_tna_integrated="";
	var cbo_profit_calculative="";
	var cbo_consumption_basis="";
	var cbo_copy_quotation="";
	if(operation==1){
		alert("Update Is Restricted in Variable Settings");
		return;
	}
	if(operation==2){
		alert("Delete Is Restricted in Variable Settings");
		return;
	}
	if (document.getElementById('cbo_variable_list_wo').value*1==46) //cbo_po_current_date
	{
		if ( form_validation('cbo_company_name_wo*cbo_ship_date','Company Name*Ship Date')==0 )
		{
			return;
		}
		else
		{				
			var publish_shipment_date = $("#cbo_ship_date").val();
			var data="action=save_update_delete&operation="+operation+"&publish_shipment_date="+publish_shipment_date+get_submitted_data_string('cbo_company_name_wo*cbo_variable_list_wo*update_id',"../../");
			//alert(data);
			freeze_window(operation);
			http.open("POST","requires/aop_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_tracking_variable_settings_reponse;
		}
	
	}
}	


function fnc_order_tracking_variable_settings_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);
		//document.getElementById('update_id').value  = reponse[2];
		set_button_status(0, permission, 'fnc_order_tracking_variable_settings',1);
		reset_form('aopsettings_1','variable_settings_container','');
		release_freezing();
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
	
function users_popup(page_link,title)
{
 	var data= document.getElementById('user_hidden_id').value;
	page_link=page_link+"&data="+data;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=210px,height=320px,center=1,resize=1,scrolling=0','../')
emailwindow.onclose=function()
        {
            var selected_id=this.contentDoc.getElementById("txt_selected_id")          
            var selected_name=this.contentDoc.getElementById("txt_selected") //Access form field with id="emailfield"
           
                document.getElementById('users_name_id').value=selected_name.value;	
                document.getElementById('user_hidden_id').value=selected_id.value;	           
        }
}	
function fnc_check_yes_no(type)
{
		//var cbo_excess_cut_source= $("#cbo_excess_cut_source").val();
		if(type==1 || type==3)
		{
			$('#cbo_editable_id').attr('disabled','disabled');
		}
		else
		{
			$('#cbo_editable_id').removeAttr('disabled','disabled');
		}
		
		//
}

function ena_dib(val,i){
	if(val==1){
		$('#txt_exeed_qty_'+i).val(0);
		$('#txt_exeed_qty_'+i).attr('disabled','disabled');
	}else{
		//$('#txt_exeed_qty_'+i).val(0);
		$('#txt_exeed_qty_'+i).removeAttr('disabled','disabled');
	}
}
function fnc_check_field(type)
{
	if(type==2 || type==0) //No
	{
		$('#cbo_capacity_exceed_level').attr('disabled','disabled');
		$('#cbo_capacity_exceed_level').val(0);
	}
	else
	{
		$('#cbo_capacity_exceed_level').removeAttr('disabled','disabled');
	}
}
</script>

</head>

<body  onLoad="set_hotkey()">
	<div align="center" style="width:100%;">
         
				<? echo load_freeze_divs ("../../",$permission);  ?>
               
	<fieldset style="width:850px;">
		<legend>AOP Variable Settings</legend>
		<form name="aopsettings_1" id="aopsettings_1" autocomplete="off">	
      			<table  width="750" cellspacing="2" cellpadding="0" border="0">
            		<tr>
                		<td width="200" align="left" class="must_entry_caption">Company Name</td>
                        <td width="250">
                           			<? 
										echo create_drop_down( "cbo_company_name_wo", 250, "select company_name,id from lib_company where is_deleted=0  and status_active=1 $company_name order by company_name",'id,company_name', 1, '--- Select Company ---', '', "show_list_view(document.getElementById('cbo_variable_list_wo').value+'_'+this.value,'on_change_data','variable_settings_container','../variable/requires/aop_settings_controller','')", '' );
									?>
                        </td>
                		<td width="200" align="center">Variable List</td>
                        <td width="250">
                            		<? //asort($order_tracking_module);
										echo create_drop_down( "cbo_variable_list_wo", 250, $aop_tracking_module,'', '1', '---- Select ----', '',"show_list_view(this.value+'_'+document.getElementById('cbo_company_name_wo').value,'on_change_data','variable_settings_container','../variable/requires/aop_settings_controller','')",''); //data, action, div, path, extra_func 
									?>
                        </td>
            		</tr>
        		</table>
            <div style="width:895px; float:left; min-height:40px; margin:auto" align="center" id="variable_settings_container">
            </div>
		</form>	
	</fieldset>

    </div>
 </body>
 
</html>    

