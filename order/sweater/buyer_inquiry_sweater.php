<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Buyer Inquiry (Sweater)
Functionality	:
JS Functions	:
Created by		:	zakaria joy
Creation date 	: 	26-11-2020
Updated by 		:	
Update date		:	
QC Performed BY	:
QC Date			:
Comments		:
Entry form      :  
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
echo load_html_head_contents("Buyer Inquiry Sweater","../../", 1, 1, $unicode,1,'');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');
?>
<script type="text/javascript">
    var permission='<? echo $permission; ?>';
    <?
    echo "var mandatory_field = '". implode('*',$_SESSION['logic_erp']['mandatory_field'][457]) . "';\n";
    echo "var field_message = '". implode('*',$_SESSION['logic_erp']['field_message'][457]) . "';\n";

    ?> 
    <?
	if($_SESSION['logic_erp']['mandatory_field'][457]!="")
	{
		$mandatory_field_arr= json_encode( $_SESSION['logic_erp']['mandatory_field'][457] );
		echo "var mandatory_field_arr= ". $mandatory_field_arr . ";\n";
	}
	?>
    function open_buyerinquiry()
    {
       var title = "Buyer Inquiry Popup";
        var page_link='requires/consumption_la_costing_controller.php?action=generate_cad_la_consting';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900,height=480px,center=1,resize=1,scrolling=0','../')
        emailwindow.onclose=function(){
            var theform=this.contentDoc.forms[0];
            var system_id=this.contentDoc.getElementById("hidden_system_number").value;
            if (system_id.value!=""){
                get_php_form_data(system_id, "populate_data_from_consumption", "requires/consumption_la_costing_controller");
                fnc_show_fabrication_list();
            }
        } 
    }
	
	function open_buyer_inquery()
	{
        var title = "Buyer Inquiry";
        var page_link='requires/buyer_inquiry_sweater_controller.php?action=generate_buyer_inquery';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=980,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			var theform=this.contentDoc.forms[0];
            var inquery_data=this.contentDoc.getElementById("hidden_issue_number").value;
			if (inquery_data.value!=""){
                //inquery_data_arr = inquery_data.split("_");
                get_php_form_data(inquery_data, "populate_data_from_data", "requires/buyer_inquiry_sweater_controller");
				set_button_status(1, permission, 'fnc_buyer_inquiry',1,1);
			}
		}
	}
	
    function fnc_buyer_inquiry(operation)
    {
        freeze_window(operation);
        var data_all="";
         if(mandatory_field !=''){
            if (form_validation(mandatory_field,field_message)==false){
                release_freezing();
                return;
            }
        } 
        if (form_validation('cbo_company_name*txt_style_ref*txt_inq_rcvd_date*cbo_buyer_name*cbo_season_id*cbo_season_year*txt_fabrication*txt_fabrication_id*cbo_order_uom*cbo_gauge', 'Company Name*Master Style Ref*Inquiry Rcvd. Data*Buyer Name*Season*Season Year*Fabrication*Fabrication ID*Order Uom*Gauge')==false){
            release_freezing();
            return;
        }
        else{
            data_all=data_all+get_submitted_data_string('cbo_company_name*txt_style_ref*txt_inq_rcvd_date*txt_bom*cbo_buyer_name*cbo_brand_id*cbo_season_id*cbo_season_year*cbo_dealing_merchant*cbo_product_department*txt_fabrication_id*cbo_gmt_item*cbo_priority*cbo_gauge*txt_no_of_ends*txt_comments*txt_offer_qty*set_breck_down*tot_set_qnty*txt_sew_smv*cbo_order_uom*hidd_yarn_count_id*update_id*txt_system_id',"../../");
        }
        //alert(data_all); return;
        var data="action=save_update_delete&operation="+operation+data_all;
        http.open("POST","requires/buyer_inquiry_sweater_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_buyer_inquiry_reponse;
    }
	
    function fnc_buyer_inquiry_reponse()
    {
        if(http.readyState == 4){
            var reponse=trim(http.responseText).split('**');
			if(reponse[0]=='pricequotation')
			{
				var quotation_msg="Delete Restricted, Quotation Found, Quotation ID is: "+reponse[1];
				alert(quotation_msg);
				release_freezing();
				return;
			}
			if(reponse[0]=='jobno')
			{
				var quotation_msg="Delete Restricted, Job Found, Job No is: "+reponse[1];
				alert(quotation_msg);
				release_freezing();
				return;
			}
			if(reponse[0]=='costsheet')
			{
				var quotation_msg="Delete Restricted, Spot Costing Found, Cost Sheet No is: "+reponse[1];
				alert(quotation_msg);
				release_freezing();
				return;
			}
            if(reponse[0]==0 || reponse[0]==1)
            {
                show_msg(trim(reponse[0]));
                document.getElementById('txt_system_id').value=reponse[1];
                document.getElementById('update_id').value=reponse[2];
                release_freezing();
                set_button_status(1, permission, 'fnc_buyer_inquiry',1);
            }
            if(reponse[0]==2)
            {
                show_msg(trim(reponse[0]));
                reset_form('consumption_form','','');
                release_freezing();
            }
            if(reponse[0]==10){
                show_msg(trim(reponse[0]));
                release_freezing();
            }
            if(trim(reponse[0]) ==24)
			{
				alert("Please Tag Image");
				release_freezing();	
				return; 
			}
            
        }
    }
	
    function remarks_popup(i)
    {
        var txtdescription=document.getElementById('txtremarks_'+i).value;
        var data=txtdescription
        var title = 'Remarks';
        var page_link = 'requires/consumption_la_costing_controller.php?data='+data+'&action=remarks_popup';

        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=470px,height=200px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var description=this.contentDoc.getElementById("description");
            $('#txtremarks_'+i).val(description.value);
        }
    }
	
    function openmypage_fabric_popup()
    {
        var cbo_company_id = $('#cbo_company_name').val();
        var save_data = $('#txt_fabrication_id').val();
        // var save_data = $('#txt_fabrication').val();

        if (form_validation('cbo_company_name','Company Name')==false)
        {
            return;
        }

        var page_link='requires/buyer_inquiry_sweater_controller.php?save_data='+save_data+'&action=buyer_inquery_fab_popup';
        var title='Fabric Details';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=780px,height=390px,center=1,resize=1,scrolling=0','../');

        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var save_data=this.contentDoc.getElementById("save_data").value;
            var save_text_data=this.contentDoc.getElementById("save_text_data").value;
			var yarncount_data=this.contentDoc.getElementById("yarncount_data").value;
			var yarncountid_data=this.contentDoc.getElementById("yarncountid_data").value;
			
            $('#txt_fabrication_id').val(save_data);
            $('#txt_fabrication').val(save_text_data);
			
			if(yarncountid_data!="")
			{
				$('#txt_yarn_count').val(yarncount_data);
            	$('#hidd_yarn_count_id').val(yarncountid_data);
			}
        }
    }
	
    function copyData()
    {
        if (form_validation('txt_system_id','System ID')==false)
        {
            return;
        }
        $("#txt_system_id").val("");
        $("#update_id").val("");
        $("#txt_bom").val("");
        $("#txt_fabrication_id").val("");
        $("#txt_fabrication").val("");
        $("#txt_no_of_ends").val("");
        $("#cbo_gauge").val("");
        set_button_status(0, permission, 'fnc_buyer_inquiry',1,1);
    }
	
	function open_set_popup(unit_id)
	{
		var txt_quotation_id=document.getElementById('update_id').value;
		var set_breck_down=document.getElementById('set_breck_down').value;
		var tot_set_qnty=document.getElementById('tot_set_qnty').value;
		var txt_inquery_id=0; var cbo_company_name=0; var set_smv_id=0;
		
		var txt_style_ref=document.getElementById('txt_style_ref').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var item_id=document.getElementById('cbo_gmt_item').value;

		var page_link="requires/buyer_inquiry_sweater_controller.php?txt_quotation_id="+trim(txt_quotation_id)+"&action=open_set_list_view&set_breck_down="+set_breck_down+"&tot_set_qnty="+tot_set_qnty+'&unit_id='+unit_id+'&txt_inquery_id='+txt_inquery_id+'&set_smv_id='+set_smv_id+'&txt_style_ref='+txt_style_ref+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name+'&item_id='+item_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Item Details", 'width=860px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			var set_breck_down=this.contentDoc.getElementById("set_breck_down")
			var item_id=this.contentDoc.getElementById("item_id")
			var tot_set_qnty=this.contentDoc.getElementById("tot_set_qnty")
			var tot_smv_qnty=this.contentDoc.getElementById('tot_smv_qnty');
			document.getElementById('set_breck_down').value=set_breck_down.value;
			document.getElementById('cbo_gmt_item').value=item_id.value;
			document.getElementById('tot_set_qnty').value=tot_set_qnty.value;
			document.getElementById('txt_sew_smv').value=tot_smv_qnty.value;
		}
	}
	
	function fnc_yarn_count()
	{
		var yarn_count_id=document.getElementById('hidd_yarn_count_id').value;

		var page_link="requires/buyer_inquiry_sweater_controller.php?yarn_count_id="+trim(yarn_count_id)+"&action=open_yarn_count_list_view";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Yarn Count Details", 'width=250px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			var yarncountid_data=this.contentDoc.getElementById('hidden_yarn_count_id').value;
			var yarncount_data=this.contentDoc.getElementById('hidden_yarn_count_name').value;
			if(yarncountid_data!="")
			{
				$('#txt_yarn_count').val(yarncount_data);
				$('#hidd_yarn_count_id').val(yarncountid_data);
			}
		}
	}
	
    
    function call_print_button_for_mail(mail,mail_body,type){		
        var company=$('#cbo_company_name').val();
        var update_id=$('#update_id').val();
        var mail_item=145;
        var data=return_global_ajax_value( company+'**'+mail_item+'**'+mail+'**'+mail_body+'**'+type+'**'+update_id, 'send_mail', '', 'requires/buyer_inquiry_sweater_controller');
       // generate_report('bom_epm_woven',mail+'**1'+mail_body);
    }



</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",$permission);  ?>
        <fieldset style="width:1050px;">
            <form name="consumption_form" id="consumption_form" autocomplete="off">
                <table  width="1050" cellspacing="2" cellpadding="1">
                    <tr>
                        <td colspan="4" align="right">System ID.</td>
                        <td colspan="4">
                            <input style="width:140px;" type="text" title="Double Click to Search" onDblClick="open_buyer_inquery();" class="text_boxes" placeholder="Browse" name="txt_system_id" id="txt_system_id" readonly />
                            <input type="hidden" name="update_id" id="update_id" />
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption" width="100">Company Name</td>
                        <td width="160"><? echo create_drop_down( "cbo_company_name", 150, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "Select Company", $selected, "load_drop_down( 'requires/buyer_inquiry_sweater_controller', this.value, 'load_drop_down_buyer', 'buyer_td');",0); ?></td>                        
                        <td class="must_entry_caption" width="100">Master Style Ref</td>
                        <td width="160"><input class="text_boxes" type="text" style="width:140px" placeholder="Write"  name="txt_style_ref" id="txt_style_ref"/></td> 
                        <td class="must_entry_caption" width="100">Inq.Rcvd Date</td>
                        <td width="160"><input  type="text" style="width:140px" class="datepicker" placeholder="Select Date"  name="txt_inq_rcvd_date" id="txt_inq_rcvd_date"/></td>
                    	<td width="100">BOM</td>
                        <td><input name="txt_bom" class="text_boxes" style="width:140px"  id="txt_bom" type="text" value="" /></td>                  
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">Buyer Name</td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "Select Buyer", $selected, "" ); ?></td>
                        <td>Brand</td>
                        <td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 150, $blank_array,"", 1, "Select Brand", $selected, "" ); ?></td>                       
                    	<td class="must_entry_caption">Season Year</td>
						<td><? echo create_drop_down( "cbo_season_year", 150, create_year_array(),"", 1, "Select Year", $selected, "" ); ?></td>
                    	<td class="must_entry_caption">Season</td>
                        <td id="season_td"><? echo create_drop_down( "cbo_season_id", 150, $blank_array,'', 1, "Select Season",$selected, "" ); ?></td>
					</tr>
                    <tr>
                        <td class="must_entry_caption">Order UOM</td>
						<td>
							<?=create_drop_down( "cbo_order_uom",60, $unit_of_measurement, "",0, "", 1, "open_set_popup(this.value);","","1,58" ); ?>
                            <input type="button" id="set_button" class="image_uploader" style="width:80px;" value="Item Details" onClick="open_set_popup(document.getElementById('cbo_order_uom').value);" />
                            <input type="hidden" id="set_breck_down" />
                            <input type="hidden" id="cbo_gmt_item"  />
                            <input type="hidden" id="tot_set_qnty" />
                            <input type="hidden" id="txt_sew_smv" />
                            <input type="hidden" name="is_season_must" id="is_season_must" style="width:30px;" class="text_boxes" />
	                    </td>
                        <td>Prod. Dept.</td>
                        <td><? echo create_drop_down( "cbo_product_department", 150, $product_dept, "", 1, "Select Prod. Dept.", $selected, "", "", "" ); ?></td>
                        <td class="must_entry_caption">Gauge</td>
                        <td><? echo create_drop_down( "cbo_gauge", 150, $gauge_arr,"", 1, "Select Gauge", $selected, "" ); ?></td>
                        <td>No Of Ends</td>
                        <td><input name="txt_no_of_ends" class="text_boxes" style="width:140px"  id="txt_no_of_ends" type="text" value="" /></td>
                    </tr>
                    <tr>
                        <td>Dealing Merchant</td>
                        <td id="div_marchant"><? echo create_drop_down( "cbo_dealing_merchant", 150, "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 group by id,team_member_name order by team_member_name","id,team_member_name", 1, "Select Dealing Merchant", $selected, "" ); ?></td>
                        <td class="must_entry_caption">Fabrication</td>
                        <td colspan="3"><input class="text_boxes" type="text" placeholder="Browse" style="width:400px"  name="txt_fabrication" id="txt_fabrication" onDblClick="openmypage_fabric_popup();" readonly/>
                        <input class="hidden" type="hidden" name="txt_fabrication_id" id="txt_fabrication_id"  readonly/>
                        </td>
                        <td>Yarn Count</td>
                        <td>
                        	<input class="text_boxes" style="width:140px" type="text" placeholder="Browse" name="txt_yarn_count" id="txt_yarn_count" onDblClick="fnc_yarn_count();" readonly/>
                        	<input style="width:30px" type="hidden" name="hidd_yarn_count_id" id="hidd_yarn_count_id"/>
                        </td>
                    </tr>
                    <tr>
                    	<td>Priority</td>
                        <td><? echo create_drop_down( "cbo_priority", 150, $priority_arr,"", 1, "Select Priority", $selected, "" ); ?></td>
                        <td>Bulk Offer Qty</td>
                        <td><input class="text_boxes_numeric" style="width:140px" type="text" placeholder="Write" name="txt_offer_qty" id="txt_offer_qty"/></td>
                        <td>Remarks</td>
                        <td colspan="3"><input class="text_boxes" style="width:400px" type="text" placeholder="Write" name="txt_comments" id="txt_comments"/></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td><input type="button" class="image_uploader" style="width:140px" value="ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'inquery_sweater_front_image', 0 ,1)"></td>
                        <td><input type="button" class="image_uploader" style="width:90px" value="ADD File" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'inquery_sweater', 2 ,1);"></td>
                       
                       <td>&nbsp;</td>
                       <td>&nbsp;</td>
                       <td style="display:none">
<input type="button" id="image_button" class="image_uploader" style="width:100px" value="ADD IMAGE BACK" onClick="file_uploader( '../../', document.getElementById('update_id').value,'', 'inquery_sweater_back_image', 0 ,1);" />
                       </td>
                       <td>&nbsp;</td>
                       <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" valign="middle" style="min-height:15px;" id="size_color_breakdown11">
                            <? echo load_submit_buttons( $permission, "fnc_buyer_inquiry", 0,0 ,"reset_form('consumption_form','','')",1); ?>                        
                            <input class="formbutton" type="button" onClick="fnSendMail('../../','update_id',1,1,0,1,0,$('#cbo_company_name').val()+'_145_1')" value="Mail Send" style="width:80px;">
                            <input class="formbutton" type="button" onClick="copyData()" value="Copy" style="width:80px;">
                        </td>
                   </tr>
                </table>
            </form>
        </fieldset>
    </div>
</body>
<script>
$(document).ready(function() {
		for (var property in mandatory_field_arr) {
			$("#"+mandatory_field_arr[property]).parent().prev('td').css("color", "blue");
		}
	});
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>