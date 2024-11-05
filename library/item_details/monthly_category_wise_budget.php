<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create for monthly category wise budget
Functionality	:
JS Functions	:
Created by		:	Md Jakir Hosen
Creation date 	: 	26-09-2012
Updated by 		: 	Md Jakir Hosen
Update date		: 	26-09-2022
QC Performed BY	:
QC Date			:
Comments		:

*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$userCredential = sql_select("SELECT unit_id as company_id,store_location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$company_credential_cond = "";
if ($company_id != "") {
    $company_credential_cond = "and comp.id in($company_id)";
}
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Monthly Category Wise Budget ", "../../", 1, 1,'','1','');
?>
<script>
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

    var permission='<? echo $permission; ?>';
    <?
    if($_SESSION['logic_erp']['data_arr'][107])
    {
        $data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][107] );
        echo "var field_level_data= ". $data_arr . ";\n";
    }
    ?>

    function fnc_category_wise_budget(operation)
    {
        if (form_validation('cbo_company_name*txt_from_date*txt_to_date*cbo_currency_name', 'Company Name*Applying Period Date Range*Applying Period Date Range*Currency')==false)
        {
            return;
        }
        if($('#table_body_1').find('tr').length > 0){
            let temp_arr = [];
            $('#table_body_1').find('tr').each(function (index){
                let id = $(this).attr('id');
                let row_number_arr = id.split('_');
                let row_number_num = row_number_arr[1];
                if (form_validation('cbo_category_name_'+row_number_num+'*txt_amount_'+row_number_num, 'Category Name*Amount')==false)
                {
                    temp_arr.push(1);
                    return false;
                }
            });
            if(temp_arr.length > 0){
                return;
            }
        }else{
            alert("Details Part Data is Required to Submit!");
            return;
        }
        var datastring = '', temp_row_num_arr = [], temp_row_cat_arr=[];
        $('#table_body_1').find('tr').each(function (index){
            let id = $(this).attr('id');
            let row_number_arr = id.split('_');
            let row_number_num = row_number_arr[1];
            temp_row_num_arr.push(row_number_num);
            temp_row_cat_arr.push($('#cbo_category_name_'+row_number_num).val());
            datastring += '*cbo_category_name_'+row_number_num+'*txt_amount_'+row_number_num+'*dtls_id_'+row_number_num;
        });
        var row_number_str = temp_row_num_arr.join(',');
        var row_cat_str = temp_row_cat_arr.join(',');

        var data="action=save_update_delete&dtlrow="+row_number_str+"&categoryid="+row_cat_str+"&operation="+operation+get_submitted_data_string('txt_system_id*cbo_company_name*txt_from_date*txt_to_date*cbo_currency_name*txt_remarks'+datastring,"../../");
        freeze_window(operation);
        http.open("POST","requires/monthly_category_wise_budget_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_category_wise_budget_response;
    }

    function fnc_category_wise_budget_response()
    {
        if(http.readyState == 4)
        {
            var reponse=trim(http.responseText).split('**');
            if(trim(reponse[0])==0){
                $('#txt_system_id').val(reponse[1]);
                show_msg(trim(reponse[0]));
                get_php_form_data(trim(reponse[1]),'load_php_data_to_form','requires/monthly_category_wise_budget_controller');
                show_list_view(trim(reponse[1]),'budget_list_view','item_group_list_view','requires/monthly_category_wise_budget_controller','setFilterGrid("list_view",-1)');
                set_button_status(1, permission, 'fnc_category_wise_budget',1);
            }else if(trim(reponse[0])==1){
                get_php_form_data(trim(reponse[1]),'load_php_data_to_form','requires/monthly_category_wise_budget_controller');
                show_list_view(trim(reponse[1]),'budget_list_view','item_group_list_view','requires/monthly_category_wise_budget_controller','setFilterGrid("list_view",-1)');
                show_msg(trim(reponse[0]));
            }else if(trim(reponse[0])==2){
                reset_form('category_wise_budget','','','','enable_fields(\'"cbo_company_name*txt_from_date*txt_to_date"\')');
                show_msg(trim(reponse[0]));
                show_list_view(trim(reponse[1]),'budget_list_view','item_group_list_view','requires/monthly_category_wise_budget_controller','setFilterGrid("list_view",-1)');
            }else if(trim(reponse[0])==20){
               show_msg(5);
               alert(reponse[1]);
            }else if(trim(reponse[0])==55) {
                show_msg(10);
                alert(reponse[1]);
            }else if(trim(reponse[0])==56){
                show_msg(10);
                alert(reponse[1]);
            }else{
                show_msg(trim(reponse[0]));
            }
            release_freezing();
        }
    }

    function enable_fields(str){
        var splitStr = str.split('*');
        $.each(splitStr, function (index, val){
            $('#'+val).prop('disabled', false);
        });
        var countRow = $('.row').length;

        $('.row').each(function (index) {
            var row_id = $(this).attr('id').split('_');
            $(this).find('select').prop('disabled', false);
            if(countRow == 1){
                $(this).find("#decrease_"+row_id[1]).attr({"onclick": "fnc_removeRow("+row_id[1]+")"});
            }else{
                $(this).find("#decrease_"+row_id[1]).attr({"onclick": "fnc_removeRow("+row_id[1]+")"}).removeAttr('style').css('width', '30px');
            }
        });
    }

    function disable_fields(str){
        var splitStr = str.split('*');
        $.each(splitStr, function (index, val){
            $('#'+val).prop('disabled', true);
        });
    }
    function fnc_addRow(val){
        let lastRow = $("#increase_"+val).closest('tr').index();
        if($('#table_body_1').find("tr").length == (lastRow+1)){
            let new_id = val+1;
            let cloneTr = $('#row_'+val).clone();
            cloneTr.closest('.row').attr("id", "row_"+new_id)
            cloneTr.find(".sl_col").text(new_id);
            cloneTr.find("#cbo_category_name_"+val).attr({"name": "cbo_category_name_"+new_id, "id":"cbo_category_name_"+new_id, "value":0}).css('background-image', '').prop('disabled', false);
            cloneTr.find("#txt_amount_"+val).attr({"name": "txt_amount_"+new_id, "id":"txt_amount_"+new_id}).css('background-image', '');
            cloneTr.find("#dtls_id_"+val).attr({"name": "dtls_id_"+new_id, "id":"dtls_id_"+new_id, "value":""});
            cloneTr.find("#increase_"+val).attr({"onclick": "fnc_addRow("+new_id+")", "id":"increase_"+new_id});
            cloneTr.find("#decrease_"+val).attr({"onclick": "fnc_removeRow("+new_id+")", "id":"decrease_"+new_id}).removeAttr('style').css('width', '30px');
            $("#increase_"+val).css({'cursor': 'not-allowed', 'opacity': '0.4'});
            // $("#decrease_"+val).css({'cursor': 'not-allowed', 'opacity': '0.4'});
            if($("#cbo_category_name_"+val).prop('disabled') == false) {
                $("#decrease_" + val).removeAttr('style').css('width', '30px');
            }
            $('#table_body_1').append(cloneTr);
            $('.sl_col').each(function (index){
                $(this).html(index+1);
            });
        }
    }
    function fnc_removeRow(val){
        let lastRow = $("#increase_"+val).closest('tr').index();
        if($('#table_body_1').find("tr").length > 1){
            if($('#table_body_1').find("tr").length == (lastRow+1)){
                let row_numb = $("#increase_"+val).closest('tr').prev('tr').attr('id');
                let row_numb_arr = row_numb.split('_');
                $("#increase_"+row_numb_arr[1]).removeAttr('style').css('width', '30px');
            }
            $('#row_'+val).remove();
            if($('#table_body_1').find("tr").length == 1){
                let row_numb = $('#table_body_1').find('tr').attr('id');
                let row_numb_arr = row_numb.split('_');
                $("#decrease_"+row_numb_arr[1]).removeAttr('style').css({'width': '30px', 'cursor': 'not-allowed', 'opacity': '0.4'});
                $("#increase_"+row_numb_arr[1]).removeAttr('style').css('width', '30px');
            }
            // $("#increase_"+(lastRow+1)).removeAttr('style').css('width', '30px');
            // $("#decrease_"+(lastRow+1)).removeAttr('style').css({'width': '30px', 'cursor': 'not-allowed', 'opacity': '0.4'});
        }else{
            $("#increase_"+(lastRow+1)).removeAttr('style').css('width', '30px');
            $("#decrease_"+(lastRow+1)).removeAttr('style').css({'width': '30px', 'cursor': 'not-allowed', 'opacity': '0.4'});
        }
        $('.sl_col').each(function (index){
            $(this).html(index+1);
        });
    }

    function apply_period_date_range(){
        var thisDate=($('#txt_from_date').val()).split('-');
        var last=new Date( (new Date(thisDate[2], thisDate[1],1))-1 );
        var last_date = last.getDate();
        var month = last.getMonth()+1;
        var year = last.getFullYear();
        if(month<10)
            var months='0'+month;
        else
            var months=month;

        var last_full_date=last_date+'-'+months+'-'+year;
        var first_full_date='01'+'-'+months+'-'+year;

        $('#txt_from_date').val(first_full_date);
        $('#txt_to_date').val(last_full_date);
    }
    jQuery(document).ready(function (){
        jQuery("#table_body_1").keypress(".text_boxes_numeric", function(e) {

            var c = String.fromCharCode(e.which);
            var evt = (e) ? e : window.event;
            var key = (evt.keyCode) ? evt.keyCode : evt.which;
            if(key != null) key = parseInt(key, 10);
            var allowed = '1234567890.'; // ~ replace of Hash(#)
            if (isUserFriendlyChar(key)) return true
            else if (key != 8 && key !=0 && allowed.indexOf(c) < 0)
                return false;
            else if (!numeric_valid( $(this).attr('id'), 0))
                return false;
        });
    });

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission); ?>
    <fieldset style="width:550px; margin-bottom:10px;">
        <legend>Monthly Category Wise Budget Entry</legend>
        <form name="category_wise_budget" id="category_wise_budget" autocomplete="off" method="POST" action="" >
            <table cellpadding="0" cellspacing="1" width="100%">
                <tr>
                    <td width="90" class="must_entry_caption" style="padding: 3px 0px;">Company Name</td>
                    <td colspan="3">
                        <?
                        echo create_drop_down( "cbo_company_name", 450, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond $company_credential_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "" );
                        ?>
                        <input type="hidden" name="txt_system_id" id="txt_system_id" readonly class="text_boxes">
                    </td>
                </tr>
                <tr>
                    <td class="must_entry_caption" width="90">Applying Period</td>
                    <td width="170">
                        <input type="text" name="txt_from_date" id="txt_from_date" onchange="apply_period_date_range()" style="width:160px" class="datepicker" readonly value=""/>
                    </td>
                    <td align="center" width="90">To</td>
                    <td width="170">
                        <input type="text" name="txt_to_date" id="txt_to_date" style="width:160px" class="datepicker" disabled readonly value=""/>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 3px 0px;" class="must_entry_caption">Currency</td>
                    <td>
                        <?
                        echo create_drop_down( "cbo_currency_name", 172, $currency,"", 0, "", 1, "", 1, "1" );
                        ?>
                    </td>
                    <td style="padding: 3px 0px;" align="center">Remarks</td>
                    <td>
                        <input type="text" name="txt_remarks" id="txt_remarks" style="width:160px" class="text_boxes"  value="" placeholder="Write"/>
                    </td>
                </tr>
            </table>
            <br>
            <table class="rpt_table" rules="all" width="550" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 10px;">
                <thead>
                    <tr>
                        <th width="50">SL No.</th>
                        <th width="200" class="must_entry_caption">Category Name</th>
                        <th width="200" class="must_entry_caption">Amount (BDT)</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="table_body_1">
                    <tr id="row_1" class="row">
                        <td align="center" class="sl_col">1</td>
                        <td align="center">
                            <?
                            echo create_drop_down( "cbo_category_name_1", 190, $general_item_category, "", 1, "-- Select Category", "", "");
                            ?>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_amount_1" id="txt_amount_1" style="width:180px" class="text_boxes_numeric"  value="" placeholder="Write"/>
                        </td>
                        <td align="center">
                            <input type="hidden" name="dtls_id_1" id="dtls_id_1" value="">
                            <input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onclick="fnc_addRow(1)">
                            <input type="button" id="decrease_1" name="decrease[]" style="width:30px; cursor: not-allowed; opacity: .4;" class="formbuttonplasminus" value="-" onclick="fnc_removeRow(1)">
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" height="50" valign="middle" align="center" class="button_container">
                            <? echo load_submit_buttons( $permission, "fnc_category_wise_budget", 0,0,"reset_form('category_wise_budget','','','','enable_fields(\'cbo_company_name*txt_from_date*txt_to_date*txt_remarks\')');",1); ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </form>
    </fieldset>
    <div style="width:100%; float:left; margin:auto" align="center">
        <fieldset style="width:580px; margin-top:20px">
            <legend>Budget List View </legend>
            <div style="text-align:center;"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --","","","","2,3,4" ); ?></div>
            <div style="width:580px; margin-top:3px; margin-bottom: 3px;" id="item_group_list_view" align="left">
                <?
                if($company_id != ""){
                    $company_cond = " and company_id in ($company_id)";
                }
                $company_name=return_library_array("select id, company_name from lib_company where status_active = 1 and is_deleted = 0","id","company_name");
                $arr = array(0=>$company_name, 4=>$currency);
                echo  create_list_view ( "list_view", "Company, Year, Period From, Period To, Currency", "140,80,100,100,100","570","220",0, "SELECT id, company_id, to_char(insert_date, 'YYYY') as insert_year, to_char(applying_date_from, 'dd-mm-YYYY') as applying_date_from, to_char(applying_date_to, 'dd-mm-YYYY') as applying_date_to, currency_id from LIB_CATEGORY_BUDGET_MST where is_deleted = 0 and status_active = 1 $company_cond order by id desc", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "company_id,0,0,0,currency_id", $arr , "company_id,insert_year,applying_date_from,applying_date_to,currency_id", "requires/monthly_category_wise_budget_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0' ) ;
                ?>
            </div>
        </fieldset>
    </div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>