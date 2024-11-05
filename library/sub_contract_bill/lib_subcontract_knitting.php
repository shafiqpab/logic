<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Marchandising team who will operate order
					level entry and marketing. here 2 form is available where 1 is creating team leader
					and 2nd is creating team member belongs to the team.

Functionality	:	First create team info and save then add multiple members one by one.
					select a team from list view for update.
JS Functions	:
Created by		:	Kausar
Creation date 	: 	04-10-2012
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
echo load_html_head_contents("Knitting Charge Set up", "../../", 1, 1,$unicode,1,1);

?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';

	var str_const_comp = [<? echo substr(return_library_autocomplete( "select const_comp from lib_subcon_charge group by const_comp", "const_comp" ), 0, -1); ?>];
	$(document).ready(function(e)
	 {
            $("#text_cons_comp").autocomplete({
			 source: str_const_comp
		  });
     });

	var str_yarn_desc = [<? echo substr(return_library_autocomplete( "select yarn_description from lib_subcon_charge group by yarn_description", "yarn_description" ), 0, -1); ?>];
	$(document).ready(function(e)
	 {
            $("#text_yarn_description").autocomplete({
			 source: str_yarn_desc
		  });
     });

	function fnc_lib_subcontract_knitting( operation )
	{
	    if (form_validation('cbo_company_name*cbo_body_part*text_cons_comp*text_gsm*text_gauge*text_yarn_description*cbo_uom','Company Name*Body Part*Construction & Composition*GSM*Gauge*Yarn Description*UOM')==false)
		{
			return;
		}

		else
		{
			//eval(get_submitted_variables('cbo_company_name*cbo_body_part*txt_cons_comp_id*text_cons_comp*text_gsm*text_yarn_description*cbo_uom*text_inhouse_rate*txt_customer_rate*cbo_buyer_id*cbo_status*update_id'));
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_body_part*text_cons_comp*text_gsm*text_gauge*txt_cons_comp_id*text_yarn_description*cbo_uom*text_inhouse_rate*txt_customer_rate*cbo_buyer_id*cbo_status*update_id*cbo_color_id*text_spandex_cat*cbo_fabric_genetic*text_dyeing_type*text_composition',"../../");
			freeze_window(operation);
			http.open("POST","requires/lib_subcontract_knitting_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_lib_subcontract_knitting_reponse;
		}
	}

	function fnc_lib_subcontract_knitting_reponse()
	{
		if(http.readyState == 4)
		{
			//alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			//if (reponse[0].length>2) reponse[0]=10;
			show_msg(reponse[0]);
			//document.getElementById('id').value  = reponse[1];
			document.getElementById('update_id').value  = reponse[2];
			/*var company_id=document.getElementById('cbo_company_name').value;
			fn_list_show(company_id);*/
			show_list_view('','list_container_subcont','list_container_subcont','../sub_contract_bill/requires/lib_subcontract_knitting_controller','setFilterGrid("list_view",-1)');
			reset_form('libsubcontractknitting_1','','','');
			set_button_status(0, permission, 'fnc_lib_subcontract_knitting',1);
			release_freezing();
		}
	}

	function openmypage_const_comp()
	{
		var data=document.getElementById('cbo_company_name').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/lib_subcontract_knitting_controller.php?data='+data+'&action=const_comp_popup','Construction & Composition Popup', 'width=780px,height=400px,center=1,resize=1,scrolling=0','../')

		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("hddn_sll_data");//Access form field with id="emailfield"
			if (theemail.value!="")
			{
				var pop_data=trim(theemail.value).split('***');
				$('#txt_cons_comp_id').val(pop_data[0]);
				$('#text_cons_comp').val(pop_data[1]);
				$('#text_gsm').val(pop_data[2]);
			}
		}
	}

	/*function fn_list_show(company_id)
	{
		if(company_id!=0)
		{
			show_list_view(company_id,'list_container_subcont','list_container_subcont','requires/lib_subcontract_knitting_controller','setFilterGrid("list_view",-1)');
		}
	}*/

function service_work_order()
{
	var cbo_company_name = $('#cbo_company_name').val();
	var save_string = $('#save_string').val();
	if (form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
	if($("#update_id").val()=="")
	{
		alert("At First Save Master Part.");
		return;
	}
	var title="Supplier Work Order Rate Info";
	var page_link = 'requires/lib_subcontract_knitting_controller.php?cbo_company_name='+cbo_company_name+'&update_id='+$("#update_id").val()+'&action=Supplier_workorder_popup';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=400px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var save_string=this.contentDoc.getElementById("hide_save_string").value;

		$('#save_string').val(save_string);
	}
}
function color_select_popup()
	{

		var buyer_name=$('#cbo_buyer_id').val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/lib_subcontract_knitting_controller.php?action=color_popup&buyer_name='+buyer_name, 'Color Select Pop Up', 'width=250px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var color_name=this.contentDoc.getElementById("color_name");
			if (color_name.value!="")
			{
				var color_data = color_name.value.split('_');
				$('#cbo_color').val(color_data[0]);
				$('#cbo_color_id').val(color_data[1]);
			}
		}
	}
function fnc_variable_settings_check(company_id){
	//$('#text_cons_comp').val('');
	//$('#txt_cons_comp_id').val('');
	var textile_business_concept=return_ajax_request_value(company_id, 'load_variable_settings', 'requires/lib_subcontract_knitting_controller');
	if(textile_business_concept == 2){
		$('#text_cons_comp').attr('readonly',true);
		$('#text_cons_comp').attr('placeholder','Browse');
		$('#text_cons_comp').removeAttr("onDblClick").attr("onDblClick","openmypage_const_comp();");
	}
	else
	{
		$('#text_cons_comp').attr('readonly',false);
		$('#text_cons_comp').attr('placeholder','Write');
		$('#text_cons_comp').removeAttr('onDblClick','onDblClick');
		$('#txt_cons_comp_id').val('');
	}
}

</script>
</head>
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <fieldset style="width:900px">
        <form  name="libsubcontractknitting_1" id="libsubcontractknitting_1"  autocomplete="off">
        <table width="800px" align="center" >
            <tr align="left">
                <td class="must_entry_caption">Company Name</td>
                <td>
					<?
						echo create_drop_down( "cbo_company_name", 171, "select id,company_name from lib_company comp where is_deleted=0  and status_active=1 $company_cond order by company_name","id,company_name", 1, "--Select Company--",  $selected, "load_drop_down( 'requires/lib_subcontract_knitting_controller', this.value, 'load_drop_down_buyer_name', 'buyer_td' );fnc_variable_settings_check(this.value); " );
                    ?>
                </td>
                <td class="must_entry_caption">Body Part</td>
                <td>
					<?
						echo create_drop_down( "cbo_body_part", 171, $body_part,'', 1, "--- Select Body Part ---", $selected, "", "","" );
                    ?>
                </td> 
				 <td class="">Spandex Category</td>
                <td>
				<input class="text_boxes" style="width:161px" name="text_spandex_cat" id="text_spandex_cat" type="text" maxlength="100" title="Maximum 100 Character" />
                </td>
            </tr>
            <tr align="left">
			<td class="must_entry_caption">Fabric Genetic Name </td>
				<td>
					<?
						echo create_drop_down( "cbo_fabric_genetic", 171, $fabric_genetic_nameArr,'', 1, "--- Select Genetic ---", $selected, "", "","" );
                    ?>
                </td>
				<td class="must_entry_caption">GSM &amp; Gauge</td>
                <td>
                    <input  style="width:72px"  name="text_gsm" id="text_gsm" type="text" class="text_boxes_numeric" placeholder="GSM" />
                    <input  style="width:72px"  name="text_gauge" id="text_gauge" type="text" class="text_boxes" placeholder="Gauge" />
                </td>
				<td class="must_entry_caption">Dyeing Type</td>
                <td>
                    <input  style="width:161px"  name="text_dyeing_type" id="text_dyeing_type" type="text" class="text_boxes" placeholder="Write" />
                     
                </td>
            </tr>
			<tr>
				
			<td class="must_entry_caption">Construction </td>
                <td>
                    <input class="text_boxes" style="width:161px"  name="text_cons_comp" id="text_cons_comp" type="text"/> <!--onDblClick="openmypage_const_comp();" placeholder="Browse" readonly-->
                    <input type="hidden" name="txt_cons_comp_id" id="txt_cons_comp_id" value=""/>
                    <input type="hidden" name="save_string" id="save_string">
                </td>
				<td>In-House Rate</td>
                <td>
                    <input style="width:161px"  name="text_inhouse_rate" id="text_inhouse_rate" type="number" class="text_boxes_numeric" maxlength="100" title="Maximum 100 Character" />
                </td>
				<td class="must_entry_caption">UOM</td>
                <td>
					<?
						echo create_drop_down( "cbo_uom", 171, $unit_of_measurement,'', 1, "--- Select UOM ---", $selected, "", "","1,2,12,27" );
                    ?>
                </td>
			</tr>
            <tr>
			<td class="must_entry_caption">Composition </td>
                <td>
                    <input class="text_boxes" style="width:161px"  name="text_composition" id="text_composition" type="text"/>  
                </td>
				<td>Customer Rate</td>
                <td><input type="text"  name="txt_customer_rate" id="txt_customer_rate" class="text_boxes_numeric" style="width:161px;" title="Maximum 30 Character" />
				</td>
				<td>Color</td>
                <td>
                	<input style="width: 161px"  name="cbo_color" id="cbo_color" type="text" class="text_boxes" placeholder="Browse" readonly onDblClick="color_select_popup()"/>
                	<input type="hidden" id="cbo_color_id" value="">
                </td>
               
            </tr>
            <tr>
			<td class="must_entry_caption"> Yarn Description</td>
                <td>
                    <input class="text_boxes" style="width:161px" name="text_yarn_description" id="text_yarn_description" type="text" maxlength="150" title="Maximum 150 Character" />
                </td>
               
                <td>Subcon Buyer</td>
                <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_id", 171, $blank_array,'', 1, "-- Select Buyer --", $selected, "","" ,"0" ); ?></td>
            
               
                <td>Status </td>
                <td>
					<?
						echo create_drop_down( "cbo_status", 171, $row_status,'', '', '', 1, '', "",'','','','3' );
                    ?>
                    <input type="hidden" name="update_id" id="update_id" value=""/>
                </td>
            </tr>
            <tr>
                <td></td>
                <td height="25" valign="middle">
                    <input type="button" class="formbuttonplasminus" style="width:170px" value="Supplier Work Order Rate" onClick="service_work_order()">
                </td>
               
            </tr>
            <tr>
                <td colspan="4" height="15"> </td>
            </tr>
            <tr>
                <td colspan="4" align="center" class="button_container">
					<?
						echo load_submit_buttons( $permission, "fnc_lib_subcontract_knitting", 0,0,"reset_form('libsubcontractknitting_1','','','')",1);
                    ?>
                </td>
            </tr>
        </table>
        </form>
    </fieldset>
    <fieldset style="width:800px;">
    	<legend>List View</legend>
        <form>
            <table width="700px" cellpadding="0" cellspacing="3" border="0">
                <tr>
                    <td colspan="5" id="list_container_subcont">
                        <?
                            //$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');spandex_category,dyeing_type,fabric_genetic,composition
                            $buyer_arr=return_library_array("select id, buyer_name from lib_buyer",'id','buyer_name');
                            $arr=array (0=>$buyer_arr,1=>$body_part,2=>$fabric_genetic_nameArr,9=>$unit_of_measurement,13=>$row_status);
                            echo  create_list_view ( "list_view", "Buyer Name,Body Part,Fabric Generic Name,Construction,Composition,GSM,Gauge,Yarn Description,In House Rate,UOM,Spandex Category,Dyeing Type,Customer Rate,Status", "120,100,100,150,150,60,60,100,70,70,100,100,70,60","1380","220",1, "select id, body_part,spandex_category,dyeing_type,fabric_genetic,composition, const_comp, gsm,gauge, yarn_description, uom_id, status_active, customer_rate, buyer_id, in_house_rate from lib_subcon_charge where is_deleted=0 and rate_type_id=2 order by id desc", "get_php_form_data", "id","'load_php_data_to_form'", 1, "buyer_id,body_part,fabric_genetic,0,0,0,0,0,0,uom_id,0,0,0,status_active", $arr , "buyer_id,body_part,fabric_genetic,const_comp,composition,gsm,gauge,yarn_description,in_house_rate,uom_id,spandex_category,dyeing_type,customer_rate,status_active", "requires/lib_subcontract_knitting_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,2,0,2,0' ) ;
							exit();
                        ?>
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
