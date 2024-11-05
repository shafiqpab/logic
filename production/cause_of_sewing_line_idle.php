<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Cause of Sewing Line Idle
				
Functionality	:	
JS Functions	:
Created by		:	Sapayth 
Creation date 	: 	01-02-2021
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
echo load_html_head_contents('Cause of Sewing Line Idle', '../', 1, 1, $unicode, '', '');
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	function generate_list(type) {
		/*var company_id = document.getElementById('cbo_company_id').value;
		var location_id = document.getElementById('cbo_location_id').value;
		var floor_id = document.getElementById('cbo_floor_id').value;*/

		if ( !form_validation('cbo_company_id*cbo_location_id*txt_date_from', 'Company Name*Location*Date') ) {
            return;
        }
        var dataString = "cbo_company_id*cbo_location_id*cbo_floor_id*txt_date_from";

        var data="action=show_line_list"+get_submitted_data_string(dataString, '../');
		//alert(data)
		freeze_window();
		http.open("POST","requires/cause_of_sewing_line_idle_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_line_list_reponse;
	}

	function fnc_line_list_reponse() {
		if(http.readyState == 4) {
	 		var response=trim(http.responseText);
            // $("#report_container").html(response[0]);
            document.getElementById('line-list-area').innerHTML = response;

			release_freezing();
		}
	}

    
 	
	
	function nptCausePopup(lineIds, rowNum) {
		
        var causeValue = document.getElementById('hdnCauseValue_'+rowNum).value;
		var hdnMstId = document.getElementById('hdnMstId_'+rowNum).value;
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe','requires/cause_of_sewing_line_idle_controller.php?lineIds='+lineIds+'&causeValue='+causeValue+'&hdnMstId='+hdnMstId+'&action=npt_cause_popup','NPT Cause Popup', 'width=810px,height=320px,center=1,resize=1,scrolling=0','')
        
        emailwindow.onclose = function() {
            var theform=this.contentDoc.forms[0];
            var allData=this.contentDoc.getElementById("txtAllData").value;
            var totalIdleMnt=this.contentDoc.getElementById("totalIdleMnt").value;
            document.getElementById('txtCauseNptMnt_'+rowNum).value = totalIdleMnt;
            document.getElementById('hdnCauseValue_'+rowNum).value = allData;
        };
    }

    function openmypage_remarks(id) {
        var data=document.getElementById('remarksvalue_'+id).value;
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/cause_of_sewing_line_idle_controller.php?data='+data+'&action=remarks_popup','Remarks Popup', 'width=420px,height=320px,center=1,resize=1,scrolling=0','')
        
        emailwindow.onclose=function() {
            var theemail=this.contentDoc.getElementById("text_new_remarks");
            if (theemail.value!="") {
                $('#remarksvalue_'+id).val(theemail.value);
            }
        }
    }

    function fnc_sewinglineidle_entry(operation) {
        // freeze_window(operation);

        var dataStr = get_submitted_data_string('cbo_company_id*cbo_location_id', '../');
        var totalRow = $('#tbl_npt_line tbody tr').length;
        var total_row = 0;
        var rowNum = 1;
        freeze_window(operation);
        
        for (var i=1; i<=totalRow; i++) {
            var causeValue = document.getElementById('hdnCauseValue_'+i).value;
            if (causeValue != '') {
                var remarks = document.getElementById('remarksvalue_'+i).value;
                var prodResource = document.getElementById('hdnProdResourceId_'+i).value;
                var date = document.getElementById('hdnDate_'+i).value;
                var lineIds = document.getElementById('hdnLineIds_'+i).value;
                var floorId = document.getElementById('hdnFloorId_'+i).value;
                var idleMstId = document.getElementById('hdnMstId_'+i).value;
                var idleDtlsId = document.getElementById('hdnDtlsId_'+i).value;
                var serialNo = document.getElementById('hdnSerialNo_'+i).value;

                dataStr+='&remarksvalue_'+rowNum+'='+remarks+'&hdnProdResourceId_'+rowNum+'='+prodResource+'&hdnDate_'+rowNum+'='+date+'&hdnLineIds_'+rowNum+'='+lineIds+'&hdnFloorId_'+rowNum+'='+floorId+'&hdnMstId_'+rowNum+'='+idleMstId+'&hdnDtlsId_'+rowNum+'='+idleDtlsId+'&hdnCauseValue_'+rowNum+'='+causeValue+'&hdnSerialNo_'+rowNum+'='+serialNo;
                total_row++;
                rowNum++;
            }
        }

        var data="action=save_update_delete&operation="+operation+"&total_row="+total_row+dataStr;

        http.open("POST","requires/cause_of_sewing_line_idle_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_sewinglineidle_entry_response;
    }

    function fnc_sewinglineidle_entry_response() {
        if(http.readyState == 4) {
            var response=trim(http.responseText).split('**');
            show_msg(response[0]);
            if(response[0]==0 || response[0]==1) {
                var rowValArr = response[1].split('@@@@@');
                var rowCauseValArr = response[2].split('@@@@@');
				  
				 // setTimeout(fnc_reload(), 5000);
				 fnc_reload();

                // console.log(rowCauseValArr);

                for (var i = 0; i < rowValArr.length; i++) {
                    var mstIdArr = rowValArr[i].split('__');
                   // document.getElementById('hdnMstId_'+mstIdArr[0]).value = mstIdArr[1];
                }

                for (var i = 0; i < rowCauseValArr.length; i++) {
                    var newCauseValArr = rowCauseValArr[i].split('__');
                   // document.getElementById('hdnCauseValue_'+newCauseValArr[0]).value = newCauseValArr[1];
                }
                
                // console.log(response[0]);
                /*document.getElementById('txtBatchSerialNo').value= response[1];
                document.getElementById('hdnUpdateId').value = response[2];*/

                // show_list_view('2**'+response[2], 'populate_dtls_data_from_search_popup', 'material_details','requires/yd_batch_creation_controller', '');

                // calculateBatchQty();

                set_button_status(1, permission, 'fnc_sewinglineidle_entry', 1);
                // set_button_status(is_update, permission, submit_func, btn_id, show_print)

            }

            release_freezing();
        }
    }

    function ResetForm() {
        // console.log('reset');
    }
	function fnc_reload()
	{
	
		setTimeout('generate_list(1)',8000);
	}
</script>
</head>
<body>
<div style="width:100%;">
	<?php echo load_freeze_divs('../', $permission); ?>
	<h3 style="width:750px; margin: 0 auto;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div id="content_search_panel" style="width:750px; margin: 0 auto;">  
    	<form name="search_form_1" id="search_form_1" autocomplete="off" >    
            <fieldset>  
                <table class="rpt_table" width="750" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th width="160" class="must_entry_caption">Company</th>
                        <th width="160" class="must_entry_caption">Location</th>
                        <th width="100">Floor</th>
                        <th class="must_entry_caption">Date</th>
                        <th>
                        	<input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('search_form_1','','','','')" />
                        </th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td>
                                <?php 
									echo create_drop_down( 'cbo_company_id', 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", 'id,company_name', 1, '--Select Company--', $selected, "load_drop_down( 'requires/cause_of_sewing_line_idle_controller', this.value, 'load_drop_down_location', 'location_td');", '', '', '', '', '', 2);
                                ?>                            
                            </td>
                            <td id="location_td">
                                <?php 
									echo create_drop_down( 'cbo_location_id', 160, $blank_array, '', 1, '--All--', $selected, '', '', '', '', '', '', 2);
                                ?>
                            </td>
                            <td id="floor_td">
                                <?php
                                    echo create_drop_down( 'cbo_floor_id', 100, $blank_array, '', 1, '--All--', '', '' );
                                ?>
                            </td>
                           	<td width="90">
                                <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px;" readonly />
                            </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_list(1)" style="width:100px" class="formbutton" />
                        </td>
                        </tr>
                    </tfoot>
                </table> 
            </fieldset>
        </form> 
    </div>
    <div id="line-list-area"></div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>