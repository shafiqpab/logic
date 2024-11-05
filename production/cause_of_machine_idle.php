<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Knit Garments Order Entry

Functionality	:
JS Functions	:
Created by		: shajjad
Creation date 	:
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
echo load_html_head_contents("Cutting Info","../", 1, 1, $unicode,'','');

?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function openmypage_machine_no(page_link,title)
	{
		page_link=page_link+get_submitted_data_string('cbo_company_name','../');
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=930px,height=470px,center=1,resize=1,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];;
			var machine_id=this.contentDoc.getElementById("selected_machine_id");
			if (machine_id.value!="")
			{
				freeze_window(5);
				document.getElementById('txt_machine_table_id').value=machine_id.value;
				get_php_form_data( machine_id.value, "populate_machine_no_data_from_search_popup", "requires/cause_of_machine_idle_controller" );
				show_list_view(machine_id.value,'show_active_listview','cause_of_machine_idle_list_view','requires/cause_of_machine_idle_controller','');
				// reset_form('','','txt_from_date*txt_from_hour*txt_from_minute*txt_to_date*txt_to_hour*txt_to_minute*txt_cause_of_machine_idle*txt_mst_id*txt_remark','','');
                reset_form('','','txt_to_date*txt_to_hour*txt_to_minute*txt_cause_of_machine_idle*txt_mst_id*txt_remark','','');
				release_freezing();
			}
		}
	}

	function fnc_cause_of_machine_idle_entry( operation )
	{
		if (form_validation('txt_machine_no*txt_from_date*txt_to_date*txt_reporting_date*txt_cause_of_machine_idle','Machine No*From Date*To Date*Reporting Date*Cause Of Machine Idle')==false)
		{
			return;
		}
		else
		{
			//var from_date = $('#txt_from_date').val();
			//var to_date = $('#txt_to_date').val();
			//var datediff = date_diff( 'd', from_date, to_date )+1;
			//alert (datediff);return;
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_machine_no*txt_machine_table_id*txt_from_date*txt_from_hour*txt_from_minute*txt_to_date*txt_to_hour*txt_to_minute*txt_cause_of_machine_idle*txt_mst_id*txt_remark*txt_reporting_date',"../");//+'&datediff='+datediff
			freeze_window(operation);
			http.open("POST","requires/cause_of_machine_idle_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_cause_of_machine_idle_entry_reponse;
		}
	}

function fnc_cause_of_machine_idle_entry_reponse()
{
	if(http.readyState == 4)
	{
		var reponse=http.responseText.split('**');
		show_msg(trim(reponse[0]));
		set_button_status(0, permission, 'fnc_cause_of_machine_idle_entry',1);
		show_list_view(document.getElementById('txt_machine_table_id').value,'show_active_listview','cause_of_machine_idle_list_view','requires/cause_of_machine_idle_controller','');
		reset_form('','','txt_from_date*txt_from_hour*txt_from_minute*txt_to_date*txt_to_hour*txt_to_minute*txt_cause_of_machine_idle*txt_mst_id*txt_remark','','');
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

</script>
</head>
<body onLoad="set_hotkey()">

    <div style="width:100%;" align="center">
        <div style="width:850px;" align="center">
             <? echo load_freeze_divs ("../",$permission);  ?>
        </div>

        <fieldset style="width:950px">
        <legend>Production Module</legend>
        <form name="machineidlecause_1" id="machineidlecause_1" action=""  autocomplete="off">
        	<fieldset>
            	<table width="100%">
                    <tr>
                        <td width="130" class="must_entry_caption">Machine No</td>
                        <td width="170" >
                            <input type="text" name="txt_machine_no" id="txt_machine_no" class="text_boxes" style="width:190px" placeholder="Double Click to Search" onDblClick="openmypage_machine_no('requires/cause_of_machine_idle_controller.php?action=machine_no_search_popup','Machine No Search')" readonly/>
                            <input type="hidden" name="txt_machine_table_id" id="txt_machine_table_id" readonly >
                         </td>
                         <td width="130">Company </td>
                         <td width="170">
                            <?
                            echo create_drop_down( "cbo_company_name", 200, "select id,company_name from lib_company where is_deleted=0 and status_active=1 and core_business not in(3) order by company_name","id,company_name", 1, "-- Select Company --", $selected,"", 1,0 );
                            ?>
                         </td>
                         <td width="130">Location </td>
                         <td width="170">
                            <?
                            echo create_drop_down( "cbo_location_name", 200, "select id,location_name from lib_location where is_deleted=0 and status_active=1 order by location_name","id,location_name", 1, "-- Select Location --", $selected,"", 1,0 );
                            ?>
                         </td>
                    </tr>
                    <tr>
                        <td width="130">Floor No</td>
                        <td width="170">
                            <?
                                echo create_drop_down( "cbo_floor_name", 200, "select id,floor_name from lib_prod_floor where is_deleted=0 and status_active=1 order by floor_name","id,floor_name", 1, "-- Select floor --", $selected, "",1,0 );
                            ?>
                         </td>
                        <td width="130">Category</td>
                        <td width="170">
                            <?
                                echo create_drop_down( "cbo_catagory", 200, $machine_category,'', 1, "-- Select Category --", $selected, "",1,0 );
                            ?>
                        </td>
                         <td width="130">Group</td>
                         <td width="170">
                            <input type="text" name="txt_group" id="txt_group" class="text_boxes" style="width:190px" disabled readonly />
                         </td>
                    </tr>
                    <tr>
                         <td width="130">Dia/Width</td>
                         <td width="170">
                            <input name="txt_dia_width" id="txt_dia_width" class="text_boxes"  style="width:190px" disabled readonly  />
                         </td>
                         <td width="130">Gauge</td>
                         <td width="170">
                         	<input name="txt_gauge" id="txt_gauge" class="text_boxes"  style="width:190px" disabled readonly  />
                         </td>
                         <td width="130">Extra Cylinder</td>
                         <td width="170">
                             <input name="txt_extra_cylinder" id="txt_extra_cylinder" class="text_boxes"  style="width:190px" disabled readonly  />
                         </td>
                    </tr>
                    <tr>
                         <td width="130">No of feeder</td>
                         <td width="170">
                            <input name="txt_no_of_feeder" id="txt_no_of_feeder" class="text_boxes"  style="width:190px" disabled readonly  />
                         </td>
                         <td width="130">Attachment</td>
                         <td width="170">
                            <input name="txt_attachment" id="txt_attachment" class="text_boxes"  style="width:190px" disabled readonly  />
                         </td>
                         <td width="130">Prod. Capacity</td>
                         <td width="170">
                            <input name="txt_prod_capacity" id="txt_prod_capacity" class="text_boxes"  style="width:190px" disabled readonly  />
                         </td>
                    </tr>
                    <tr>
                         <td width="130">Capacity UOM</td>
                         <td width="170">
                            <?
								echo create_drop_down( "cbo_capacity_uom", 200, $unit_of_measurement,'', 1, "-- Select UOM --", $selected, "",1,0 );
							?>
                         </td>
                         <td width="130">Remarks</td>
                         <td width="500" colspan="3">
                            <input name="txt_remarks" id="txt_remarks" class="text_boxes"  style="width:527px" disabled readonly  />
                         </td>
                    </tr>
                    </table>
                    </fieldset>
                    <table><tr><td colspan="8" height="5"></td></tr></table><!--this is blank-->
                    <table cellpadding="0" cellspacing="1" width="100%" class="rpt_table" rules="all">
                    	<thead>
                    		<th width="160" colspan="3" class="must_entry_caption">From Date and Time</th>
                            <th width="160" colspan="3" class="must_entry_caption">To Date and Time</th>
                            <th class="must_entry_caption" width="100">Reporting Date</th>
                            <th class="must_entry_caption" width="190">Cause of Machine Idle</th>
                            <th>Remarks</th>
                        </thead>
                     	<tr class="general">
                            <td>
                                <input type="text" name="txt_from_date" id="txt_from_date" class="datepicker"  placeholder="Select Date" style="width:60px; text-align:center" value="<? echo date('d-m-Y');?>"/>
                            </td>
                            <td >
                            	<input title="24 Hour Format" name="txt_from_hour" id="txt_from_hour" class="text_boxes_numeric" placeholder="HH" type="text"  style="width:30px" onKeyUp="fnc_move_cursor(this.value,'txt_from_hour','txt_from_minute',2,23);" value="<? echo date('h');?>"/>
                            </td>
                            <td >
                            	<input name="txt_from_minute" id="txt_from_minute" class="text_boxes_numeric" placeholder="MM" type="text"  style="width:30px" onKeyUp="fnc_move_cursor(this.value,'txt_from_minute','txt_start_date5',2,59)" value="<? echo date('i');?>"/>
                            </td>
                            <!--<td>
								<?
									//echo create_drop_down( "cbo_time_from", 50, $time_source, 0, "", 1, "" );
                                ?>
                            </td>-->
                            <td>
                                <input name="txt_to_date" id="txt_to_date" class="datepicker" type="text" placeholder="Date" value="" style="width:60px; text-align:center" />
                            </td>
                            <td >
                            	<input title="24 Hour Format" name="txt_to_hour" id="txt_to_hour"  class="text_boxes_numeric" placeholder="HH" type="text"  style="width:30px" onKeyUp="fnc_move_cursor(this.value,'txt_to_hour','txt_to_minute',2,23);"/>
                            </td>
                            <td >
                            	<input name="txt_to_minute" id="txt_to_minute"  class="text_boxes_numeric" placeholder="MM" type="text"  style="width:30px" onKeyUp="fnc_move_cursor(this.value,'txt_to_minute','txt_start_date6',2,59)"/>
                            </td>
                            <!--<td>
								<?
									//echo create_drop_down( "cbo_time_to", 50, $time_source, 0, "", 1, "" );
                                ?>
                            </td>-->

                            <td >
                                <input name="txt_reporting_date" id="txt_reporting_date" class="datepicker" type="text" placeholder="Date" value="" style="width:80px; text-align:center" />
                            </td>

                            <td>
                            	<?
									echo create_drop_down( "txt_cause_of_machine_idle", 180, $cause_type,'', 1, "-- Select Cause --", $selected, "",0,0 );
								?>
                            </td>
                            <td >
                            	<input name="txt_remark" id="txt_remark" class="text_boxes" style="width:250px;" />
                            </td>
                    	</tr>
                        <tr>
                            <td align="center" colspan="11" valign="middle" class="button_container">
                                <?
                                echo load_submit_buttons( $permission, "fnc_cause_of_machine_idle_entry", 0,0 ,"reset_form('machineidlecause_1','cause_of_machine_idle_list_view','','','')",1);
                                ?>
                                <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly >
                            </td>

                        </tr>
                	</table>
                	<div style="width:900px; margin-top:5px;"  id="cause_of_machine_idle_list_view" align="center"></div>
          </form>
        </fieldset>
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>