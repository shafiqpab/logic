<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create for monthly category wise budget V2
Functionality	:
JS Functions	:
Created by		:	Wayasel Ahmmed
Creation date 	: 	25-10-2023
Updated by 		: 	Wayasel Ahmmed
Update date		: 	25-10-2023
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
echo load_html_head_contents("Monthly Category Wise Budget Entry V2", "../../", 1, 1,'','1','');
?>
<script>
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

    var permission='<? echo $permission; ?>';
 
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
                if (form_validation('cbo_profit_center_'+row_number_num+'*txt_amount_'+row_number_num, 'Category Name*Amount')==false)
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
        var datastring = '', temp_row_num_arr = [], temp_row_cat_arr=[], temp_profit_center_arr=[], duplicate_check=0;; 
        $('#table_body_1').find('tr').each(function (index)
		{
            let id = $(this).attr('id');
            let row_number_arr = id.split('_');
            let row_number_num = row_number_arr[1];
            temp_row_num_arr.push(row_number_num);
            temp_row_cat_arr.push($('#cbo_profit_center_'+row_number_num).val());
			
			if( jQuery.inArray( $('#cbo_profit_center_'+row_number_num).val(), temp_profit_center_arr ) !== -1  &&  temp_profit_center_arr.length>0)
			{
				alert("Profit Center Duplicate is Not Allow");
				duplicate_check=1;
				return;
			}
			
			temp_profit_center_arr.push( $('#cbo_profit_center_'+row_number_num).val() );
			
            datastring += '*cbo_profit_center_'+row_number_num+'*txt_amount_'+row_number_num+'*dtls_id_'+row_number_num+'*cbo_division_'+row_number_num+'*cbo_department_'+row_number_num+'*cbo_section_'+row_number_num+'*txt_amount_string_'+row_number_num;
        });
        var row_number_str = temp_row_num_arr.join(',');
        var row_cat_str = temp_row_cat_arr.join(',');
		if(duplicate_check==1){
			return;
		}
        var data="action=save_update_delete&dtlrow="+row_number_str+"&cbo_profitid="+row_cat_str+"&operation="+operation+get_submitted_data_string('txt_system_id*cbo_status_name*cbo_company_name*txt_from_date*txt_to_date*cbo_currency_name*txt_remarks'+datastring,"../../");
        freeze_window(operation);
        http.open("POST","requires/monthly_category_wise_budget_entry_v2_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_category_wise_budget_response;
    }

    function fnc_category_wise_budget_response()
    {
        if(http.readyState == 4)
        {
            var reponse=trim(http.responseText).split('**');
			if(reponse[0]==20)
			{
				alert(reponse[1]);release_freezing();return;
			}
			
            var cbo_company_name=$("#cbo_company_name").val();
            if(trim(reponse[0])==0){
                // $('#txt_system_id').val(reponse[1]);
                show_msg(trim(reponse[0]));
                get_php_form_data(trim(reponse[1]),'load_php_data_to_form','requires/monthly_category_wise_budget_entry_v2_controller');
                show_list_view(cbo_company_name,'budget_list_view','item_group_list_view','requires/monthly_category_wise_budget_entry_v2_controller','setFilterGrid("list_view",-1)');
                set_button_status(1, permission, 'fnc_category_wise_budget',1);
            }else if(trim(reponse[0])==1){
                get_php_form_data(trim(reponse[1]),'load_php_data_to_form','requires/monthly_category_wise_budget_entry_v2_controller');
                show_list_view(cbo_company_name,'budget_list_view','item_group_list_view','requires/monthly_category_wise_budget_entry_v2_controller','setFilterGrid("list_view",-1)');
                show_msg(trim(reponse[0]));
            }else if(trim(reponse[0])==2){
                reset_form('frmCategoryWiseBudget_1','','','','enable_fields(\'"cbo_company_name*txt_from_date*txt_to_date"\')');
                show_msg(trim(reponse[0]));
                show_list_view(trim(reponse[1]),'budget_list_view','item_group_list_view','requires/monthly_category_wise_budget_entry_v2_controller','setFilterGrid("list_view",-1)');
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
            cloneTr.find("#cbo_profit_center_"+val).attr({"name": "cbo_profit_center_"+new_id, "id":"cbo_profit_center_"+new_id, "value":0}).css('background-image', '').prop('disabled', false);
            
            cloneTr.find("#cbo_division_"+val).attr({"name": "cbo_division_"+new_id, "id":"cbo_division_"+new_id, "value":0}).css('background-image', '').prop('disabled', false);

            cloneTr.find("#cbo_department_"+val).attr({"name": "cbo_department_"+new_id, "id":"cbo_department_"+new_id, "value":0}).css('background-image', '').prop('disabled', false); 

            cloneTr.find("#cbo_section_"+val).attr({"name": "cbo_section_"+new_id, "id":"cbo_section_"+new_id, "value":0}).css('background-image', '').prop('disabled', false); 
            
            cloneTr.find("#txt_amount_"+val).attr({"name": "txt_amount_"+new_id, "id":"txt_amount_"+new_id, "value":""}).css('background-image', '');
			cloneTr.find("#txt_amount_string_"+val).attr({"name": "txt_amount_string_"+new_id, "id":"txt_amount_string_"+new_id, "value":""});
            cloneTr.find("#dtls_id_"+val).attr({"name": "dtls_id_"+new_id, "id":"dtls_id_"+new_id, "value":""});
			
            cloneTr.find("#increase_"+val).attr({"onclick": "fnc_addRow("+new_id+")", "id":"increase_"+new_id});
            cloneTr.find("#decrease_"+val).attr({"onclick": "fnc_removeRow("+new_id+")", "id":"decrease_"+new_id}).removeAttr('style').css('width', '30px');
            $("#increase_"+val).css({'cursor': 'not-allowed', 'opacity': '0.4'});
            // $("#decrease_"+val).css({'cursor': 'not-allowed', 'opacity': '0.4'});
            if($("#cbo_profit_center_"+val).prop('disabled') == false) {
                $("#decrease_" + val).removeAttr('style').css('width', '30px');
            }
            $('#table_body_1').append(cloneTr);
            $('.sl_col').each(function (index){
                $(this).html(index+1);
            });
			
			$('#txt_amount_'+new_id).removeAttr("onClick").attr("onClick","fn_budge_brk_amt("+new_id+")");
			$('#cbo_division_'+new_id).removeAttr("onchange").attr("onchange","fn_load_department(this.value,"+new_id+")");
			$('#cbo_department_'+new_id).removeAttr("onchange").attr("onchange","fn_load_section(this.value,"+new_id+")");
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
	
	
	function fn_budge_brk_amt(row_num)
	{
		var amt_string=$("#txt_amount_string_"+row_num).val();
		var tot_amt=$("#txt_amount_"+row_num).val()*1;
		var page_link='requires/monthly_category_wise_budget_entry_v2_controller.php?action=budge_brk_amt&amt_string='+amt_string+'&tot_amt='+tot_amt;  
		var title="Category Wise Amount";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=505px,height=470px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var hdn_brk_amt_data=this.contentDoc.getElementById("hdn_brk_amt_data").value;
			var hdn_tot_amt=this.contentDoc.getElementById("hdn_tot_amt").value;
			
			$("#txt_amount_string_"+row_num).val(hdn_brk_amt_data);
			$("#txt_amount_"+row_num).val(hdn_tot_amt);
		}
	}
	
	function fn_load_department(devision_id, sequenceNo)
	{
		var department_result = return_global_ajax_value(devision_id, 'department_list', '', 'requires/monthly_category_wise_budget_entry_v2_controller');
		var tbl_length=$('#tbl_details_part tbody tr').length;
		//alert(tbl_length+"="+sequenceNo+"="+department_result);return;
		var JSONObject = JSON.parse(department_result);
		for(var i=sequenceNo; i<=tbl_length; i++)
		{
			$('#cbo_department_'+i).html('<option value="'+0+'">Select</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#cbo_department_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}
	}
	
	function fn_load_section(department_id, sequenceNo)
	{
		var section_result = return_global_ajax_value(department_id, 'section_list', '', 'requires/monthly_category_wise_budget_entry_v2_controller');
		var tbl_length=$('#tbl_details_part tbody tr').length;
		//alert(tbl_length+"="+sequenceNo+"="+department_result);return;
		var JSONObject = JSON.parse(section_result);
		for(var i=sequenceNo; i<=tbl_length; i++)
		{
			$('#cbo_section_'+i).html('<option value="'+0+'">Select</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#cbo_section_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}
	}

</script>
</head>

<body onLoad="set_hotkey()">
    
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission); ?>
    <fieldset style="width:650px; margin-bottom:10px;">
    <div align="center">
            <fieldset style="width:600px;">
                <form name="excelImport_1" id="excelImport_1" action="yarn_stock_import_excel.php" enctype="multipart/form-data" method="post">
                    <table cellpadding="0" cellspacing="2" width="600" style="padding-left: 5px; padding-right: 5px;">
                        <tr>
                            <td width="200" align="left"><input type="file" id="uploadfile" name="uploadfile" class="image_uploader" required style="width:200px" /></td>
                            <td width="200" align="left"><input type="submit" name="submit" value="Excel File Upload" class="formbutton" style="width:110px" /></td>                
                            <td width="200" align="right"><a href="../../excel_format/yarn_up_requirement.xls"><input type="button" value="Excel Format Download" name="excel" id="excel" class="formbutton" style="width:150px"/></a></td>
                        </tr>
                    </table>
                </form>
            </fieldset>
        </div>
        <legend>Monthly Category Wise Budget Entry V2</legend>      
        <form name="frmCategoryWiseBudget_1" id="frmCategoryWiseBudget_1" autocomplete="off" method="POST" action="" >
            <table cellpadding="0" cellspacing="1" width="100%">
                <tr>
                    <td width="90" class="must_entry_caption" style="padding: 3px 0px;">Company Name</td>
                    <td colspan="3">
                        <?
                        echo create_drop_down( "cbo_company_name", 510, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond $company_credential_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/monthly_category_wise_budget_entry_v2_controller',this.value, 'load_drop_down_division_popup', 'com_division_td');load_drop_down( 'requires/monthly_category_wise_budget_entry_v2_controller',this.value, 'load_drop_down_com_profit', 'com_profit_td');show_list_view(this.value,'budget_list_view','item_group_list_view','requires/monthly_category_wise_budget_entry_v2_controller','setFilterGrid(\'list_view\',-1)');" );
                        //
                        //
                        ?>
                         <input type="hidden" name="txt_system_id" id="txt_system_id" readonly class="text_boxes">
                    </td>
                </tr> 
                <tr>
                    <td class="must_entry_caption" width="90">Applying Period</td>
                    <td width="170">
                        <input type="text" name="txt_from_date" id="txt_from_date" onChange="apply_period_date_range()" style="width:160px" class="datepicker" readonly value=""/>
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
                    <td style="padding: 3px 0px;" align="center" >Status</td>
                    <td>
                         <?
                        $status = array(1 => "Active", 2 => "InActive");

                        echo create_drop_down( "cbo_status_name", 172, $status,"", 0, "", 1, "", "", "" );
                        ?>
                    </td>
                </tr>
                <tr>
                     <td style="padding: 3px 0px;" >Remarks</td>
                    <td colspan="3">
                        <input type="text" name="txt_remarks" id="txt_remarks" style="width:500px" class="text_boxes"  value="" placeholder="Write"/>
                    </td>
                </tr>
            </table>
            <br>
            <table class="rpt_table" rules="all" width="660" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 10px;" id="tbl_details_part">
                <thead>
                    <tr>
                        <th width="50">SL No.</th>
                        <th width="100" class="must_entry_caption">Profit Center</th>
                        <th width="100" class="must_entry_caption">Division</th>
                        <th width="100" class="must_entry_caption">Department</th>
                        <th width="100" class="must_entry_caption">Section</th>
                        <th width="100" class="must_entry_caption">Amount (BDT)</th>
                        <th>Row (+/-)</th>
                    </tr>
                </thead>
                <tbody id="table_body_1">
                    <tr id="row_1" class="row">
                        <td align="center" class="sl_col">1</td>                  
                        <td align="center" id="com_profit_td">
                            <?
                            echo create_drop_down( "cbo_profit_center_1", 100, "", "", 1, "-- Select Profit", "", "");
                            ?>
                        </td>
                        <td align="center" id="com_division_td">
                            <?
                            echo create_drop_down( "cbo_division_1", 100, "", "", 1, "-- Select Division", "", "fn_load_department(this.value,1)");
                            ?>
                        </td>
                        <td align="center" id="department_td_popup">
                            <?
                            echo create_drop_down( "cbo_department_1", 100, "", "", 1, "-- Select department", "", "fn_load_section(this.value,1)");
                            ?>
                        </td>
                        <td align="center" id="section_td_popup">
                            <?
                            echo create_drop_down( "cbo_section_1", 100, "", "", 1, "-- Select Section", "", "");
                            ?>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_amount_1" id="txt_amount_1" style="width:100px" class="text_boxes_numeric"  value="" onClick="fn_budge_brk_amt(1)" readonly />
                            <input type="hidden" name="txt_amount_string_1" id="txt_amount_string_1" />
                        </td>
                        <td align="center">
                            <input type="hidden" name="dtls_id_1" id="dtls_id_1" value="">
                            <input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(1)">
                            <input type="button" id="decrease_1" name="decrease[]" style="width:30px; cursor: not-allowed; opacity: .4;" class="formbuttonplasminus" value="-" onClick="fnc_removeRow(1)">
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7" height="50" valign="middle" align="center" class="button_container">
                            <? echo load_submit_buttons( $permission, "fnc_category_wise_budget", 0,0,"reset_form('frmCategoryWiseBudget_1','','','','enable_fields(\'cbo_company_name*txt_from_date*txt_to_date*txt_remarks\')');",1); 
							?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </form>
    </fieldset>
    <div style="width:100%; float:left; margin:auto" align="center">
        <fieldset style="width:800px; margin-top:20px">
            <legend>Budget List View </legend>
            <div style="text-align:center;"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --","","","","2,3,4" ); ?></div>
            <div style="width:800px; margin-top:3px; margin-bottom: 3px;" id="item_group_list_view" align="left">
                <?
                /*if($company_id != ""){
                    $company_cond = " and company_id in ($company_id)";
                }
                $company_name=return_library_array("select id, company_name from lib_company where status_active = 1 and is_deleted = 0","id","company_name");
                $lib_profit_center=return_library_array("select id, profit_center_name from lib_profit_center where status_active = 1 and is_deleted = 0","id","profit_center_name");
                $lib_division_name=return_library_array("select id, division_name  from lib_division where status_active = 1 and is_deleted = 0","id","division_name");
                $lib_department_name=return_library_array("select id, department_name  from lib_department where status_active = 1 and is_deleted = 0","id","department_name");
                $lib_section_name=return_library_array("select id, section_name  from lib_section where status_active = 1 and is_deleted = 0","id","section_name");

                $arr = array(0=>$company_name,2=>$lib_profit_center,3=>$lib_division_name, 4=>$lib_department_name, 5=>$lib_section_name);
                echo  create_list_view ( "list_view", "Company, Month, Profit Center,Division, Department,Section", "140,80,100,100,100,100","770","220",0, "SELECT a.id, a.company_id, to_char(a.insert_date, 'Mon-YYYY') as insert_year, b.department, b.profit_center, b.division, b.section from LIB_CATEGORY_BUDGET_ENTRY_MST a, LIB_CATEGORY_BUDGET_ENTRY_DTLS b where a.id=b.mst_id and a.is_deleted = 0 and a.status_active = 1 $company_cond order by a.id desc", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "company_id,0,profit_center,division,department,section", $arr , "company_id,insert_year,profit_center,division,department,section", "requires/monthly_category_wise_budget_entry_v2_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0' ) ;*/
                ?>
            </div>
        </fieldset>
    </div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>