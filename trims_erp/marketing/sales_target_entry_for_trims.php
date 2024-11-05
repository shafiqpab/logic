<?

/*--- ----------------------------------------- Comments

Purpose			: 	
					
Functionality	:	

JS Functions	:

Created by		:	K.M. Nazim Uddin
Creation date 	: 	02.07.2019
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
echo load_html_head_contents("Consignment Clearing Setup", "../../", 1, 1);

?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  

var permission='<? echo $permission; ?>';
var unit_of_measurement='<? echo json_encode($unit_of_measurement); ?>';
unit_of_measurement=JSON.parse(unit_of_measurement);
//alert(unit_of_measurement[2]);

function fnc_sales_target_setup( operation )
{
    if ( form_validation('cbo_company_name*cboSection_1*cbo_year*cbo_month','Company*Section*Year*Starting Month')==false )
    {
        return;
    }
    var j=0; var check_field=0; data_all=""; var i=0;
    var cbo_company_name        = $('#cbo_company_name').val();
    var section                 = $('#cboSection_1').val();
    var sub_section             = $('#cboSubSection_1').val();
    var cbo_team_leader         = $('#cbo_team_leader').val();
    var cbo_team_member         = $('#cbo_team_member').val();
    var cbo_year                = $('#cbo_year').val();
    var cbo_month               = $('#cbo_month').val();
    var hdn_uom_id              = $('#hdn_uom_id').val();
    var total_qty              = $('#txtTotQuantity').val();
    var total_amt              = $('#txtTotAmount').val();
    var update_id               = $('#update_id').val();
        
    $("#tbl_dtls tbody tr").each(function()
    {
        var hdnMonthYear        = $(this).find('input[name="hdnMonthYear[]"]').val();
        var txtQuantity         = $(this).find('input[name="txtQuantity[]"]').val();
        var txtAmount           = $(this).find('input[name="txtAmount[]"]').val();
        var hdnDtlsUpdateId     = $(this).find('input[name="hdnDtlsUpdateId[]"]').val();
        j++
        /*if(cboSection==0 || cboUom==0)
        {                   
            if(cboSection==0)
            {
                alert('Please Select Section');
                check_field=1 ; return;
            }
            else
            {
                alert('Please Select Order UOM ');
                check_field=1 ; return;
            }
        }*/
        i++;
        data_all += "&hdnMonthYear_" + j + "='" + hdnMonthYear + "'&txtQuantity_" + j + "='" + txtQuantity + "'&txtAmount_" + j + "='" + txtAmount + "'&hdnDtlsUpdateId_" + j + "='" + hdnDtlsUpdateId + "'";
    });
    
    if(check_field==0)
    {
        var data="action=save_update_delete&operation="+operation+'&total_row='+i+'&cbo_company_name='+cbo_company_name+'&section='+section+'&sub_section='+sub_section+'&cbo_team_leader='+cbo_team_leader+'&cbo_team_member='+cbo_team_member+'&cbo_year='+cbo_year+'&cbo_month='+cbo_month+'&hdn_uom_id='+hdn_uom_id+'&total_qty='+total_qty+'&total_amt='+total_amt+'&update_id='+update_id+data_all;
        //alert (data); //return;
        freeze_window(operation);
        http.open("POST","requires/sales_target_entry_for_trims_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_sales_target_setup_reponse;
    }
    else
    {
        return;
    }
}
    
function fnc_sales_target_setup_reponse()
{
    if(http.readyState == 4) 
    {
        var response=trim(http.responseText).split('**');
        if(response[0]==0 || response[0]==1)
        {
            show_msg(trim(response[0]));
            $('#update_id').val(response[1]);
            for_listview(2);
            set_button_status(1, permission, 'fnc_sales_target_setup',1,1);
            //load_booked_uom_list();
        }
        else if(response[0]==2)
        {
            show_msg(trim(response[0]));
            fnResetForm();
            release_freezing();
            return;
        }
        else if(trim(response[0])==11)
        {
            alert('Duplicate Found');
            release_freezing();
            return;
        }
    }
    release_freezing();
}

function fnResetForm() 
{
    set_button_status(0, permission, 'fnc_sales_target_setup', 1);
    //reset_form('salesTarget','','','cbo_within_group,1*cbo_currency,1*cboUom_1,2','','');
    reset_form('salesTarget','','','','','');
    $('#details_view').empty();
    $('#cbo_company_name').attr('disabled',false);
    $('#cboSection_1').attr('disabled',false);
    $('#cboSubSection_1').attr('disabled',false);
    $('#cbo_year').attr('disabled',false);
}

function for_listview(type)
{
    if(type==1)
    {
        var company=$('#cbo_company_name').val();
        var cbo_year=$('#cbo_year').val();
        var section=$('#cboSection_1').val();
        var sub_section=$('#cboSubSection_1').val();
        var cbo_team_leader=$('#cbo_team_leader').val();
        var cbo_team_member=$('#cbo_team_member').val();

        var mst_id=return_global_ajax_value(company+'_'+cbo_year+'_'+section+'_'+sub_section+'_'+cbo_team_leader+'_'+cbo_team_member, 'check_is_saved', '', 'requires/sales_target_entry_for_trims_controller');
        if(mst_id!=0 && mst_id!='')
        {
            get_php_form_data( mst_id, "load_php_data_to_form", "requires/sales_target_entry_for_trims_controller" );
            sum_total(1); sum_total(2);
        }
        else
        {
            $('#update_id').val('');
            $('#txtTotQuantity').val('');
            $('#txtTotAmount').val('');
            var cbo_month=$('#cbo_month').val();
            if(company!=0 && cbo_year!=0 && cbo_month!=0)
            {
                var data=company+'_'+cbo_year+'_'+cbo_month+'_'+section+'_'+sub_section;
                show_list_view(data,'order_dtls_list_view','details_view','requires/sales_target_entry_for_trims_controller','');
                var response=return_global_ajax_value(company+'_'+section+'_'+sub_section, 'check_booked_uom', '', 'requires/sales_target_entry_for_trims_controller');
                $('#hdn_uom_id').val(trim(response));
            }
            set_button_status(0, permission, 'fnc_sales_target_setup',1,1); 
        } 
    }
    else
    {
        var mst_id=$('#update_id').val();
        get_php_form_data( mst_id, "load_php_data_to_form", "requires/sales_target_entry_for_trims_controller" );
        sum_total(1); sum_total(2);
    }
}

function for_uom()
{
    if($('#txtUom_12').val()==undefined)
    {
        return;
    }
    else
    {
        var company=$('#cbo_company_name').val();
        var section=$('#cboSection_1').val();
        var sub_section=$('#cboSubSection_1').val();
        var response=return_global_ajax_value(company+'_'+section+'_'+sub_section, 'check_booked_uom', '', 'requires/sales_target_entry_for_trims_controller');
        $('#hdn_uom_id').val(trim(response));
        for(i=1; i<=12; i++)
        {
            $('#txtUom_'+i).html(unit_of_measurement[trim(response)]);
        }
    }
}

function load_sub_section()
{
    var section=$('#cboSection_1').val();
    var company=$('#cbo_company_name').val();
    load_drop_down( 'requires/sales_target_entry_for_trims_controller',section+'_'+company , 'load_drop_down_subsection', 'subSectionTd_1' );
}

function sum_total(type)
{
    var ddd={ dec_type:5, comma:0, currency:''};
    var tot_row=$('#tbl_dtls tbody tr').length;
    if(type==1)
    {
        math_operation( "txtTotQuantity", "txtQuantity_", "+", tot_row,ddd );
    }
    else
    {
        math_operation( "txtTotAmount", "txtAmount_", "+", tot_row,ddd );
    }
}
</script>

</head>
<body onLoad="set_hotkey();">
   	<div align="center" style="width:100%;">
		<? echo load_freeze_divs ("../../",$permission);  ?>
        <form name="salesTarget" id="salesTarget" autocomplete="off">
        	<fieldset style="width:850px;">
                <legend>Sales Target Entry For Trims</legend>
                    <table width="100%" border="0" cellpadding="0" cellspacing="0" >
                       <tr>
                            <td width="110" class="must_entry_caption">Company Name </td>
                            <td><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sales_target_entry_for_trims_controller', this.value, 'load_drop_down_month', 'month_td');for_listview(1)"); ?>
                            </td>
                            <td width="110" class="must_entry_caption">Section</td>
                           <?
                           unset($trims_section[0]);
                           asort($trims_section);
                           ?>
                            <td><? echo create_drop_down( "cboSection_1", 150, $trims_section,"", 1, "-- Select Section --","","load_sub_section();for_uom();for_listview(1)",0,'','','','','','',"cboSection[]"); ?></td>
                            <td width="110" >Sub-Section </td>
                            <td id="subSectionTd_1"><? echo create_drop_down( "cboSubSection_1", 150, $trims_sub_section,"", 1, "-- Select Sub-Section --","","",0,'','','','','','',"cboSubSection[]"); ?></td>
                    	</tr>
                        <tr>
                            <td>Team Leader</td>
                            <td><? echo create_drop_down( "cbo_team_leader", 150, "select id,team_leader_name from lib_marketing_team where  status_active =1 and is_deleted=0 and project_type=3","id,team_leader_name", 1, "-- Select Leader --", $selected, "load_drop_down( 'requires/sales_target_entry_for_trims_controller', this.value+'_'+1, 'load_drop_down_member', 'member_td');for_listview(1);"); ?>
                            </td>
                            <td>Team Member</td>
                            <td id="member_td"><? echo create_drop_down( "cbo_team_member", 150,  $blank_array,"", 1, "-- Select Member --", $selected, "for_listview(1)"); ?></td>
                            <td class="must_entry_caption">Year</td>
                            <td><? echo create_drop_down( "cbo_year", 150, $year,"", 1, "-- Select Year --", $selected, "for_listview(1)"); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Starting Month</td>
                            <td id="month_td" ><? echo create_drop_down( "cbo_month", 150, $months_short,"", 1, "-- Select month --", $selected, ""); ?>
                            </td>
                            <td colspan="4">&nbsp; </td>
                        </tr>
                        <tr>
                        	<td>&nbsp; </td>
                       	</tr>
                   	</table>
                    <table>
                        <tr>
                            <td align="center" class="button_container" >
                                <input type="hidden" name="update_id" id="update_id"  class="text_boxes" style="width:60px;"  />
                                <input type="hidden" name="hdn_uom_id" id="hdn_uom_id"  class="text_boxes" style="width:60px;"  />
                                <? 
                                    echo load_submit_buttons( $permission, "fnc_sales_target_setup", 0,0 ,"fnResetForm()");
                                ?> 
                            </td>
                    	</tr>
            	    </table>
                    <table cellspacing="0" width="850px"  rules="all" class="rpt_table" border="1" id="tbl_dtls">
                        <thead>
                            <tr>
                                <th rowspan="2" width="150">Month</th>
                                <th colspan="3" width="310">Current Year's Target</th>
                                <th colspan="3" width="310">Previous Year's Target</th>
                            </tr>
                            <tr>
                                <th width="80">UOM</th>
                                <th width="100">Qty</th>
                                <th width="150">Value ($)</th>
                                <th width="80">UOM</th>
                                <th width="100">Qty</th>
                                <th width="150">Value ($)</th>
                            </tr>
                        </thead>
                        <tbody id="details_view"></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" width="150">Total</th>
                                <th><input id="txtTotQuantity" name="txtTotQuantity[]" class="text_boxes_numeric" type="text"  style="width:100px" /></th>
                                <th><input id="txtTotAmount" name="txtTotAmount[]" class="text_boxes_numeric" type="text"  style="width:150px" /></th>
                                <th colspan="3">&nbsp;</th>
                            </tr>
                        </tfoot>
                    </table>
            </fieldset>
        </form>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>