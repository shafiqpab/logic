<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

//========== user credential start ========
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];
$location_id = $userCredential[0][csf('location_id')];

$company_credential_cond = "";

if ($company_id >0) {
    $company_credential_cond = " and comp.id in($company_id)";
}

if (!empty($store_location_id)) {
    $store_location_credential_cond = " and a.id in($store_location_id)";
}

if ($location_id !='') {
    $location_credential_cond = " and id in($location_id)";
}

if($item_cate_id !='') {
    $item_cate_credential_cond = $item_cate_id ;
}

 $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");


//========== user credential end ==========

if ($action=="load_drop_down_location")
{
    echo create_drop_down( "cbo_location_name", 162,"SELECT id,location_name from lib_location where company_id=$data and is_deleted=0 and status_active=1 $location_credential_cond","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/service_requisition_controller', this.value, 'load_drop_down_store','store_td');" );
    die;
}

if ($action == "load_drop_down_division") {
	$sql="SELECT id,division_name from lib_division where company_id=$data and is_deleted=0  and status_active=1";
	echo create_drop_down("cbo_division_name", 162, "SELECT id,division_name from lib_division where company_id=$data and is_deleted=0  and status_active=1", "id,division_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/service_requisition_controller', this.value, 'load_drop_down_department','department_td');");
}

if ($action == "load_drop_down_department") {
	echo create_drop_down("cbo_department_name", 162, "SELECT id,department_name from lib_department where division_id=$data and is_deleted=0 and status_active=1", "id,department_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/service_requisition_controller', this.value, 'load_drop_down_section','section_td');");
	exit();
}

if ($action == "load_drop_down_section") {
	echo create_drop_down("cbo_section_zname", 162, "SELECT id,section_name from lib_section where department_id=$data and is_deleted=0 and status_active=1", "id,section_name", 1, "-- Select --", $selected, "");
	exit();
}

if ($action == "load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 162, "SELECT a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.location_id=$data and b.category_type not in(4,11) $store_location_credential_cond group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select --", "", "", "","");
	exit();
}

if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=19 and report_id=238 and is_deleted=0 and status_active=1");
//    echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
    echo "print_report_button_setting('".$print_report_format."');\n";
    exit();
}

if($action=="tag_req_popup")
{
    extract($_REQUEST);
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1);
    ?>

    <script>
        var selected_id = new Array;
        var selected_number = new Array;
        function check_all_data() {
            var tbl_row_count = document.getElementById( 'table_body' ).rows.length;
            tbl_row_count = tbl_row_count - 1;
            for( var i = 1; i <= tbl_row_count; i++ ) {
                $('#tr_'+i).trigger('click');
            }
        }

        function set_all()
        {
            var old=document.getElementById('txt_row_data').value;
            if(old!="")
            {
                old=old.split(",");
                for(var i=0; i<old.length; i++)
                {
                    js_set_value( old[i] )
                }
            }
        }

        function toggle( x, origColor ) {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }

        var kk=0;

        function js_set_value( strParam )
        {
            var chk_st=$('#check_all').is(":checked");
            if(chk_st==true)kk++;

            var splitArr = strParam.split("_");
            var str = splitArr[0];
            var numbers = splitArr[1];
            var ids = splitArr[2];

            toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );

            if( jQuery.inArray( ids, selected_id ) == -1 ) {
                selected_id.push( ids );
                selected_number.push( numbers );
            }
            else {
                for( var i = 0; i < selected_id.length; i++ ) {
                    if( selected_id[i] == numbers ) break;
                }
                selected_id.splice( i, 1 );
                selected_number.splice( i, 1 );
            }

            var num = id = '';
            for( var i = 0; i < selected_id.length; i++ ) {
                id += selected_id[i] + ',';
                num += selected_number[i] + ',';
            }
            id = id.substr( 0, id.length - 1 );
            num = num.substr( 0, num.length - 1 );

            $('#txt_selected_ids').val( id );
            $('#txt_selected_numbers').val( num );
        }

        function reset_hidden()
        {
            $('#txt_selected_ids').val('');
            $('#txt_selected_numbers').val('');
        }

    </script>

    </head>

    <body>
    <div align="center" style="width:100%;" >

        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="980" cellspacing="0" cellpadding="0" class="rpt_table" align="center">
                <tr>
                    <td align="center" width="100%">
                        <table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                            <thead>
                            <th width="150">Company Name</th>
                            <th width="150">Store Name</th>
                            <th width="100">Reqsition Year</th>
                            <th width="100">Reqsition No</th>
                            <th width="200">Date Range</th>
                            <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                            </thead>
                            <tr>
                                <td width="150">
                                    <?
                                    echo create_drop_down( "cbo_company_name", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", $company, "",1 );
                                    ?>
                                </td>
                                <td width="150">
                                    <?
                                    echo create_drop_down( "cbo_store_name", 162, "SELECT a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$company and b.category_type not in(4,11) $store_location_credential_cond group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select --", "", "", "","");
                                    ?>
                                </td>
                                <td  align="center"> <? echo create_drop_down("cbo_req_year", 65, create_year_array(), "", 0, "-- --", date("Y", time()), "", 0, ""); ?></td>
                                <td  align="center"> <input type="text" id="txt_req_no" name="txt_req_no" class="text_boxes" style="width:100px;" ></td>
                                <td align="center">
                                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
                                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                                    <input type="hidden" id="txt_selected_ids" name="txt_selected_ids" value="<? echo $req_numbers_id; ?>" />
                                    <input type="hidden" id="txt_selected_numbers" name="txt_selected_numbers" value="<? echo $req_numbers; ?>" />
                                </td>
                                <td align="center">
                                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_store_name').value+'_'+document.getElementById('cbo_req_year').value+'_'+document.getElementById('txt_req_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_tag_req_search_list_view', 'search_div', 'service_work_order_controller', 'setFilterGrid(\'table_body\',-1)');reset_hidden();set_all();" style="width:100px;" />
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td  align="center" height="40" valign="middle">
                        <? echo load_month_buttons(1);  ?>
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="top" id="search_div"></td>
                </tr>
            </table>
        </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}

if($action=="create_tag_req_search_list_view")
{

    extract($_REQUEST);
    list($companyName,$storeName,$reqsition_year,$req_no,$txt_date_from,$txt_date_to) = explode("_",$data);

    $company=return_library_array( "SELECT id, company_name from lib_company",'id','company_name');
    $location=return_library_array("SELECT id,location_name from lib_location",'id','location_name');
    $store_library=return_library_array( "SELECT id, store_name from  lib_store_location", "id", "store_name"  );
    $department_library=return_library_array("SELECT id,department_name from lib_department",'id','department_name');
    $section_library=return_library_array("SELECT id,section_name from lib_section",'id','section_name');
    $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

    $sql_cond="";
    if($companyName!=0){$sql_cond = " and a.company_id = '".$companyName."'";}
    if($storeName!=0){$sql_cond .= " and a.store_name = '".$storeName."'";}

    if($txt_date_from!="" || $txt_date_to!="")
    {
        if($db_type==0) $sql_cond .= " and a.requisition_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
        else if($db_type==2) $sql_cond .= " and a.requisition_date between '".change_date_format($txt_date_from,'','',-1)."' and '".change_date_format($txt_date_to,'','',-1)."'";
    }

    if ($req_no!="")
    {
        $sql_cond .=" and a.requ_prefix_num=$req_no";
    }

    if($db_type==0){ $sql_cond .=" and year(a.insert_date)=".$reqsition_year.""; }
    else{ $sql_cond .=" and to_char(a.insert_date,'YYYY')=".$reqsition_year.""; }

    $sql = "SELECT a.ID, a.REQU_PREFIX_NUM, a.REQU_NO, a.COMPANY_ID, a.REQUISITION_DATE, a.LOCATION_ID, a.DEPARTMENT_ID,a.SECTION_ID, a.STORE_NAME, a.MANUAL_REQ, a.INSERTED_BY, a.READY_TO_APPROVE, a.IS_APPROVED from inv_purchase_requisition_mst a where a.entry_form = 69 and a.status_active=1 and a.is_deleted=0 $sql_cond order by a.id desc";
    //  echo $sql;
    ?>
    <div style="margin-top:10px; width:1120px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table" align="left">
            <thead>
            <th width="30">SL</th>
            <th width="60">Requisition No</th>
            <th width="65">Requisition Date</th>
            <th width="110">Company</th>
            <th width="110">Location</th>
            <th width="90">Department</th>
            <th width="140">Section</th>
            <th width="90">Store Name</th>
            <th width="80">Manual Req</th>
            <th width="80">Inserted by</th>
            <th width="80">Ready to Approve</th>
            <th>Approval Status</th>
            </thead>
        </table>
        <div style="width:1120px; overflow-y:scroll; max-height:200px">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table" id="table_body">
                <?php
                $i = 1;
                $txt_row_data = "";
                $hidden_dtls_id = explode(",", $req_dtls_id);
                $nameArray = sql_select($sql);
                foreach ($nameArray as $selectResult)
                {
                    if ($i % 2 == 0) { $bgcolor = "#E9F3FF";} else { $bgcolor = "#FFFFFF"; }
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $i."_".$selectResult[csf('requ_no')]."_".$selectResult[csf('id')]; ?>')">
                        <td width="30" align="center"><?php echo "$i"; ?></td>
                        <td width="60" align="center"><p><?php echo $selectResult['REQU_PREFIX_NUM']; ?></p></td>
                        <td width="65" align="center"><?php echo change_date_format($selectResult['REQUISITION_DATE']); ?></td>
                        <td width="110"><p><?php echo $company[$selectResult['COMPANY_ID']]; ?></p></td>
                        <td width="110"><p><?php echo $location[$selectResult['LOCATION_ID']]; ?>&nbsp;</p></td>
                        <td width="90"><p><?php echo $department_library[$selectResult['DEPARTMENT_ID']]; ?>&nbsp;</p></td>
                        <td width="140"><p><?php echo $section_library[$selectResult['SECTION_ID']]; ?></p></td>
                        <td width="90"><p><?php echo $store_library[$selectResult['STORE_NAME']]; ?></p></td>
                        <td width="80"><p><?php echo $selectResult['MANUAL_REQ']; ?></p></td>
                        <td width="80" ><?php echo $user_lib_name[$selectResult['INSERTED_BY']]; ?></td>
                        <td width="80" >
                            <?
                            if ($selectResult["READY_TO_APPROVE"]==1){ $ready_msg='Yes';}else{ $ready_msg='No';}
                            echo $ready_msg;
                            ?>
                        </td>
                        <td align="center"><p>
                                <?
                                if ($selectResult["IS_APPROVED"]==1) $approved_msg='Yes';
                                else if ($selectResult["IS_APPROVED"]==3) $approved_msg='Partial Approved';
                                else $approved_msg='No';
                                echo $approved_msg;
                                ?>
                            </p></td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
            </table>
        </div>
        <table width="1100" cellspacing="0" cellpadding="0" border="1" align="left">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%">
                        <div style="width:45%; float:left" align="left">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                        </div>
                        <div style="width:55%; float:left" align="left">
                            <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <?
    exit();
}


if($action=="service_details_popup")
{
    echo load_html_head_contents("Service Details Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
	
    ?>
     <script>

     var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

     function check_all_data()
        {
            var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
            tbl_row_count = tbl_row_count - 1;

            for( var i = 1; i <= tbl_row_count; i++ ) {
                 eval($('#tr_'+i).attr("onclick"));
            }
        }

    function toggle( x, origColor ) {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }

    function js_set_value(id)
    {
        
        var str=id.split("_");
        toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
        var id=str[1];
		var name=str[2];
		//alert (id);return;
        if( jQuery.inArray(  id , selected_id ) == -1 ) {
            selected_id.push( id );
			selected_name.push( name );
        }
        else 
		{
            for( var i = 0; i < selected_id.length; i++ ) 
			{
                if( selected_id[i] == id  ) break;
            }
            selected_id.splice( i, 1 );
			selected_name.splice( i, 1 );
        }
        var ids = ''; var names = '';
        for( var i = 0; i < selected_id.length; i++ ) {
            ids += selected_id[i] + ',';
			names += selected_name[i] + ',';
        }
        ids = ids.substr( 0, ids.length - 1 );
		names = names.substr( 0, names.length - 1 );

        $('#hdnServiceId').val( ids );
		$('#hdnServiceName').val( names );
    }

    </script>
    </head>
    <body>
    <div align="center">
        <h3>Service Details</h3>
        <input type="hidden" id="hdnServiceId" name="hdnServiceId" />
        <input type="hidden" id="hdnServiceName" name="hdnServiceName" />
        <form name="styleRef_form" id="styleRef_form">
        <?
		$serivce_sql="select ID, SERVICE_CODE, SERVICE_GROUP, SERVICE_CATEGORY, SERVICE_NAME from LIB_SERVICE_CATEGORY where STATUS_ACTIVE=1";
		$serivce_sql_result=sql_select($serivce_sql);
		if(count($serivce_sql_result)>0)
		{
			?>
            <fieldset style="width:580px;">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="580" class="rpt_table" id="list_view">
                	<thead>
                    	<tr>
                        	<td width="30" align="center" style="font-size:14px; font-weight:bold">SL</td>
                            <td width="130" align="center" style="font-size:14px; font-weight:bold">Service Code</td>
                            <td width="140" align="center" style="font-size:14px; font-weight:bold">Service Group</td>
                            <td width="140" align="center" style="font-size:14px; font-weight:bold">Service Category</td>
                            <td align="center" style="font-size:14px; font-weight:bold">Service Name</td>
                        </tr>
                    </thead>
                    <tbody id="list_view_2">
                    <?
					$i=1;
					foreach($serivce_sql_result as $val)
					{
						 if ($i%2==0) $bgcolor="#E9F3FF";
	                     else $bgcolor="#FFFFFF";
						?>
                        <tr bgcolor="<?=$bgcolor; ?>" id="tr_<?=$i; ?>" onClick="js_set_value('<? echo $i."_".$val["ID"]."_".$val["SERVICE_NAME"]; ?>')" style="cursor:pointer">
                        	<td align="center"><?= $i;?></td>
                            <td align="center" id="serviceCode_<?=$i;?>"><? echo $val["SERVICE_CODE"];?></td>
                            <td align="center" id="serviceGroup_<?=$i;?>"><? echo $val["SERVICE_GROUP"];?></td>
                            <td align="center" id="serviceCategory_<?=$i;?>"><? echo $val["SERVICE_CATEGORY"];?></td>
                            <td align="center" id="serviceName_<?=$i;?>"><? echo $val["SERVICE_NAME"];?></td>
                        </tr>
                        <?
						$i++;
					}
					?>
                    	
                    </tbody>
                </table>
            </fieldset>
            <script type="text/javascript">
                setFilterGrid('list_view_2',-1);
                set_all();
            </script>
            <?
		}
		else
		{
			?>
            <fieldset style="width:580px;">
                <textarea style="height: 80px;" name="txt_service_details" id="txt_service_details" class="text_area" cols="90" rows="10" value="<? echo $service_details;?>"><? echo $service_details;?></textarea>
            </fieldset>
            <?
		}
		?>
        <br>
        <input type="button" id="btn_close" style="width:100px" class="formbutton" value="Close" onClick="parent.emailwindow.hide();" />
    	</form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action=="load_php_popup_to_lib")
{
    $explode_data = explode("**",$data);
    $i=$explode_data[0];
    $service_id=$explode_data[1];
    $service_for=$explode_data[2];
    //$item=$explode_data[2];

    if($data!="")
    {
        $serivce_sql="select ID, SERVICE_CODE, SERVICE_GROUP, SERVICE_CATEGORY, SERVICE_NAME from LIB_SERVICE_CATEGORY where STATUS_ACTIVE=1 and id in($service_id)";
		$serivce_sql_result=sql_select($serivce_sql);
        //$i=1;
        foreach ($serivce_sql_result as $val)
        {
			?>
			<tr class="general" id="tr_<?= $i; ?>">
				<td>
					<? echo create_drop_down( "cboServiceFor_".$i, 90, $service_for_arr, "", 1, "-- Select --",$service_for, "", "", "", "", "", "", "", "", "cboServiceFor[]", "cboServiceFor_".$i ); ?>
				</td>
				<td align="center">
					<input type="text" name="txtServiceDetails[]" id="txtServiceDetails_<?= $i; ?>" class="text_boxes" style="width:90px;" onDblClick="fncServiceDetails(<?=$i;?>)" placeholder="Double Click To Search" value="<? echo $val["SERVICE_NAME"];?>" readonly/>
					<input type="hidden" name="hdnServiceId[]" id="hdnServiceId_<?= $i; ?>" value="<? echo $val["ID"];?>" />
				</td>
				<td align="center">
					<input type="text" name="txtItemDescription[]" id="txtItemDescription_<?= $i; ?>" class="text_boxes" style="width:130px;" onDblClick="itemDetailsPopup(<?= $i; ?>)" placeholder="Double Click To Search" value="" readonly disabled />
					<input type="hidden" name="txtItemId[]" id="txtItemId_<? echo $i; ?> " value="0"/>
					<input type="hidden" name="txtRowId[]" id="txtRowId_<? echo $i; ?>" value="0" />
				</td>
				<td align="center">
					<? echo create_drop_down( "cboItemCategory_".$i, 120, $blank_array,"", 1, "-- Select --",0, "",1,"","","","","","","cboItemCategory[]","cboItemCategory_".$i ); ?>
				</td>
				<td align="center">
					<? echo create_drop_down( "cboItemGroup_".$i,100,$blank_array,"", 1,"Select",0, "",1,"","","","","","","cboItemGroup[]","cboItemGroup_".$i ); ?>
				</td>
				<td align="center">
					<? echo create_drop_down( "cboUom_".$i, 70, $service_uom_arr,"", 1, "--Select--", 1, "","", "", "", "", "", "", "", "cboUom[]", "cboUom_".$i ); ?>
				</td>
				<td>
					<input type="text" name="txtQnty[]" id="txtQnty_<?= $i; ?>" class="text_boxes_numeric" style="width:60px" value="" onKeyUp="calculate_amount(<?= $i; ?>)"/>
				</td>
				<td>
					<input type="text" name="txtRate[]" id="txtRate_<?= $i; ?>" class="text_boxes_numeric" style="width:60px;"  value="" onKeyUp="calculate_amount(<?= $i; ?>)" />
				</td>
				<td><input type="text" name="txtAmount[]" id="txtAmount_<?= $i; ?>" class="text_boxes_numeric" style="width:60px;" value="" readonly /></td>
				<td><input type="text" name="txtRemarks[]" id="txtRemarks_<?= $i; ?>" class="text_boxes" style="width:120px;" value="" onDblClick="openmypage_remarks(<? echo $i;?>)"/></td>
				<td>
					<input type="text" name="txtTagMaterialsName[]" id="txtTagMaterialsName_<?= $i; ?>" class="text_boxes" style="width:90px;" onDblClick="fncItemTagMaterials(<?= $i; ?>)" placeholder="Double Click To Search" value="" readonly/>
					<input type="hidden" name="txtTagMaterials[]" id="txtTagMaterials_<?= $i; ?>" value="" readonly/>
				</td>
				<td>
					<input type="button" name="increase[]" id="increase_<?= $i; ?>" style="width:18px" class="formbuttonplasminus" value="+" onClick="add_dtls_tr(<?= $i; ?>)" />
					<input type="button" name="decrease[]" id="decrease_<?= $i; ?>" style="width:18px" class="formbuttonplasminus" value="-" onClick="fnc_delet_dtls_tr(<?= $i; ?>);" />
				</td>
			</tr>   
			<?
			$i++;
        }
    }
    exit();
}


if($action=="item_description_popup")
{
    echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    //echo $company;die;
    ?>
    <script>

    var selected_id = new Array;
    var vselected_name = new Array();
    var selected_attach_id = new Array();

    function check_all_data()
    {
        var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
        tbl_row_count = tbl_row_count - 1;

        for( var i = 1; i <= tbl_row_count; i++ ) {
            eval($('#tr_'+i).attr("onclick"));
        }
    }

    function toggle( x, origColor ) {
        var newColor = 'yellow';
        if ( x.style ) {
            x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
        }
    }

    function js_set_value(id)
    {
        //alert (id);
        var str=id.split("_");
        toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
        str=str[1];
        if( jQuery.inArray(  str , selected_id ) == -1 )
        {
            selected_id.push( str );
        }
        else
        {
            for( var i = 0; i < selected_id.length; i++ ) {
                if( selected_id[i] == str  ) break;
            }
            selected_id.splice( i, 1 );
        }
        var id = '';
        for( var i = 0; i < selected_id.length; i++ ) {
            id += selected_id[i] + ',';
        }
        id = id.substr( 0, id.length - 1 );

        $('#item_1').val( id );

    }

    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
        <fieldset style="width:580px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                <thead>
                    <th>Item Category</th>
                    <th>Item group</th>
                    <th>Item Code</th>
                    <th>Item Description</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                </thead>
                <tbody>
                    <tr>
                        <td>
                        <?
                        echo create_drop_down( "cbo_item_category", 130, "select category_id, short_name from  lib_item_category_list where status_active=1 and is_deleted=0 and category_type=1 and category_id not in(4,11) order by short_name","category_id,short_name", 1, "-- Select --", "", "", 0,"" );
                        ?>
                        </td>
                        <td align="center">
                            <input type="text" style="width:120px" class="text_boxes" name="txt_item_group" id="txt_item_group" />
                        </td>
                        <td align="center">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_item_code" id="txt_item_code" />
                        </td>
                        <td align="center">
                             <input type="text" style="width:130px" class="text_boxes" name="txt_item_description" id="txt_item_description" />
                        </td>
                        <td align="center">
                            <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_item_category').value+'**'+document.getElementById('txt_item_description').value+'**'+document.getElementById('txt_item_code').value+'**'+document.getElementById('txt_item_group').value, 'item_description_popup_list_view', 'search_div', 'service_requisition_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
        <div style="margin-top:15px" id="search_div"></div>
    </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action=="item_description_popup_list_view")
{
    extract($_REQUEST);
    list($company,$itemCategory,$item_description,$item_code,$item_group)=explode('**',$data);
    ?>

    </head>
    <body>
        <div align="center" style="width:100%" >
            <form name="order_popup_1"  id="order_popup_1">
            <fieldset style="width:900px">
            <input type="hidden" id="item_1" />
            <?
            if($item_description!=""){$search_con=" and a.item_description like('%$item_description%')";}
            if($item_code!=""){$search_con .= " and a.item_code like('%$item_code')";}
            if($item_group!=""){$search_con .= " and b.item_name like('%$item_group%')";}
            if($itemCategory){$search_con .= " and item_category_id='$itemCategory'";}

            if($itemIDS!="") $itemIDScond = " and a.id not in ($itemIDS)"; else $itemIDScond = "";
            $arr=array (1=>$item_category,5=>$unit_of_measurement,9=>$row_status);//5=>$unit_of_measurement,

            $sql="select a.id, a.item_account, a.item_category_id, a.item_description, a.item_size, a.item_group_id, a.unit_of_measure, a.current_stock, a.re_order_label, a.status_active, b.item_name, a.order_uom
            from product_details_master a, lib_item_group b
            where a.item_group_id=b.id and a.status_active in(1,3) and a.is_deleted=0 and company_id='$company' and a.item_category_id in (89,51,52,49,90,99,55,21,67,93,59,48,64,15,57,66,45,47,107,54,70,50,37,69,68,18,46,60,62,9,16,17,38,92,65,10,33,44,34,35,63,19,22,61,97,36,56,8,41,40,91,43,53,20,94,32,58,39) and a.entry_form<>24 $itemIDScond $search_con";
            //echo $sql;//die;
            echo  create_list_view ( "list_view","Item Account,Item Category,Item Description,Item Size,Item Group,Order UOM,Stock,Re-Order Level,Product ID,Status", "120,100,140,80,100,80,80,80,80,50","950","250",0, $sql, "js_set_value", "id", "", '', "0,item_category_id,0,0,0,order_uom,0,0,0,status_active", $arr , "item_account,item_category_id,item_description,item_size,item_name,order_uom,current_stock,re_order_label,id,status_active", "", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,1,1,0,0','',1 );
            ?>
            </fieldset>
            </form>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="tag_materials_popup")
{
    echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    //echo $company;die;
    ?>
    <script>

    var selected_id = new Array;
    var item_name = new Array;

    function check_all_data()
    {
        var tbl_row_count = document.getElementById( 'table_body' ).rows.length;
        tbl_row_count = tbl_row_count - 1;

        for( var i = 1; i <= tbl_row_count; i++ ) {
            eval($('#tr_'+i).attr("onclick"));
        }
    }

    function toggle( x, origColor ) {
        var newColor = 'yellow';
        if ( x.style ) {
            x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
        }
    }

    function js_set_value(strParam)
    {
        // alert (id);
        var splitArr = strParam.split("_");
        toggle( document.getElementById( 'tr_' + splitArr[0] ), '#FFFFFF' );
        str=splitArr[1];
        selected_name=splitArr[2];
        if( jQuery.inArray(  str , selected_id ) == -1 )
        {
            selected_id.push( str );
            item_name.push( selected_name );
        }
        else
        {
            for( var i = 0; i < selected_id.length; i++ ) {
                if( selected_id[i] == str  ) break;
            }
            selected_id.splice( i, 1 );
            item_name.splice( i, 1 );
        }
        var id = '';
        var num = '';
        for( var i = 0; i < selected_id.length; i++ ) {
            id += selected_id[i] + ',';
            num += item_name[i] + ',';
        }
        id = id.substr( 0, id.length - 1 );
        num = num.substr( 0, num.length - 1 );

        $('#txt_selected_ids').val( id );
        $('#txt_selected_numbers').val( num );

    }

    function set_all()
    {
        var old=document.getElementById('txt_row_data').value;
        if(old!="")
        {
            old=old.split(",");
            for(var i=0; i<old.length; i++)
            {
                js_set_value( old[i] )
            }
        }
    }

    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
        <fieldset style="width:580px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                <thead>
                    <th>Item Category</th>
                    <th>Item group</th>
                    <th>Item Code</th>
                    <th>Item Description</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                </thead>
                <tbody>
                    <tr>
                        <td>
                        <?
                        echo create_drop_down( "cbo_item_category", 130, "SELECT category_id, short_name from  lib_item_category_list where status_active=1 and is_deleted=0 and category_type=1 and category_id not in(4,11) order by short_name","category_id,short_name", 1, "-- Select --", "", "", 0,"" );
                        ?>
                        </td>
                        <td align="center">
                            <input type="text" style="width:120px" class="text_boxes" name="txt_item_group" id="txt_item_group" />
                        </td>
                        <td align="center">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_item_code" id="txt_item_code" />
                        </td>
                        <td align="center">
                             <input type="text" style="width:130px" class="text_boxes" name="txt_item_description" id="txt_item_description" />
                        </td>
                        <td align="center">
                            <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_item_category').value+'**'+document.getElementById('txt_item_description').value+'**'+document.getElementById('txt_item_code').value+'**'+document.getElementById('txt_item_group').value+'**'+'<? echo $prod_id; ?>', 'tag_materials_popup_list_view', 'search_div', 'service_requisition_controller', 'setFilterGrid(\'table_body\',-1)');set_all();" style="width:100px;" />
                    </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
        <div style="margin-top:15px" id="search_div"></div>
    </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action=="tag_materials_popup_list_view")
{
    extract($_REQUEST);
    list($company,$itemCategory,$item_description,$item_code,$item_group,$prod_id)=explode('**',$data);
    ?>

    </head>
    <body>
        <div align="center" style="width:100%" >
            <form name="order_popup_1"  id="order_popup_1">
            <fieldset style="width:900px">
            <input type="hidden" id="txt_selected_ids" />
            <input type="hidden" id="txt_selected_numbers" />
            <?
            if($item_description!=""){$search_con=" and a.item_description like('%$item_description%')";}
            if($item_code!=""){$search_con .= " and a.item_code like('%$item_code')";}
            if($item_group!=""){$search_con .= " and b.item_name like('%$item_group%')";}
            if($itemCategory){$search_con .= " and item_category_id='$itemCategory'";}

            if($itemIDS!="") $itemIDScond = " and a.id not in ($itemIDS)"; else $itemIDScond = "";
            $arr=array (1=>$item_category,5=>$unit_of_measurement,9=>$row_status);//5=>$unit_of_measurement,

            $sql="SELECT a.id, a.item_account, a.item_category_id, a.item_description, a.item_size, a.item_group_id, a.unit_of_measure, a.current_stock, a.re_order_label, a.status_active, b.item_name, a.order_uom
            from product_details_master a, lib_item_group b
            where a.item_group_id=b.id and a.status_active in(1,3) and a.is_deleted=0 and company_id='$company' and a.item_category_id in (89,51,52,49,90,99,55,21,67,93,59,48,64,15,57,66,45,47,107,54,70,50,37,69,68,18,46,60,62,9,16,17,38,92,65,10,33,44,34,35,63,19,22,61,97,36,56,8,41,40,91,43,53,20,94,32,58,39) and a.entry_form<>24 $itemIDScond $search_con order by id desc";
            //echo $sql;//die;

            // echo  create_list_view ( "list_view","Item Account,Item Category,Item Description,Item Size,Item Group,Order UOM,Stock,Re-Order Level,Product ID,Status", "120,100,140,80,100,80,80,80,80,50","950","250",0, $sql, "js_set_value", "id", "", '', "0,item_category_id,0,0,0,order_uom,0,0,0,status_active", $arr , "item_account,item_category_id,item_description,item_size,item_name,order_uom,current_stock,re_order_label,id,status_active", "", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,1,1,0,0','',1 );
            ?>
            <div style="margin-top:10px; width:970px;">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table" align="left">
                    <thead>
                        <th width="30">SL</th>
                        <th width="120">Item Account</th>
                        <th width="100">Item Category</th>
                        <th width="140">Item Description</th>
                        <th width="80">Item Size</th>
                        <th width="80">Item Group</th>
                        <th width="80">Order UOM</th>
                        <th width="80">Stock</th>
                        <th width="80">Re-Order Level</th>
                        <th width="80">Product ID</th>
                        <th >Status</th>
                    </thead>
                </table>
                <div style="width:970px; overflow-y:scroll; max-height:200px">
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table" id="table_body">
                        <?php
                            $i = 1;
                            $txt_row_data = "";
                            $hidden_prod_id = explode(",", $prod_id);
                            $nameArray = sql_select($sql);
                            foreach ($nameArray as $selectResult)
                            {
                                $data = $i."_".$selectResult[csf('id')]."_".$selectResult[csf('item_description')];

                                if(in_array($selectResult[csf('id')], $hidden_prod_id))
                                {
                                    if($txt_row_data == "") { $txt_row_data=$data; }
                                    else{ $txt_row_data.=",".$data; }
                                }

                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $data; ?>')">
                                    <td width="30" align="center"><?php echo $i; ?></td>
                                    <td width="120"><p><?php echo $selectResult[csf('item_account')]; ?></p></td>
                                    <td width="100"><?php echo $item_category[$selectResult[csf('item_category_id')]]; ?></td>
                                    <td width="140"><p><?php echo $selectResult[csf('item_description')]; ?></p></td>
                                    <td width="80"><p><?php echo $selectResult[csf('item_size')]; ?>&nbsp;</p></td>
                                    <td width="80"><p><?php echo $selectResult[csf('item_name')]; ?>&nbsp;</p></td>
                                    <td width="80" align="center"><p><?php echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></p></td>
                                    <td width="80" align="right"><p><?php echo number_format($selectResult[csf('current_stock')],2); ?></p></td>
                                    <td width="80" align="right"><p><?php echo $selectResult[csf('re_order_label')]; ?></p></td>
                                    <td width="80" align="center"><?php echo $selectResult[csf('id')]; ?></td>
                                    <td align="center"><?php echo $row_status[$selectResult[csf("status_active")]]; ?></td>
                                </tr>
                                <?
                                $i++;
                            }
                        ?>
                        <input type="hidden" name="txt_row_data" id="txt_row_data" value="<?php echo $txt_row_data; ?>"/>
                    </table>
                </div>
                <table width="1100" cellspacing="0" cellpadding="0" border="1" align="left">
                    <tr>
                        <td align="center" height="30" valign="bottom">
                            <div style="width:100%">
                                <div style="width:45%; float:left" align="left">
                                    <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                                </div>
                                <div style="width:55%; float:left" align="left">
                                    <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <?
            ?>
            </fieldset>
            </form>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action=="load_php_popup_to_form_itemDtls")
{
    $explode_data = explode("**",$data);
    $data_id=$explode_data[0];
    $company=$explode_data[1];
    $i=$explode_data[2];

    if($data_id!="")
    {
        $lib_item_group=return_library_array("SELECT id, item_name from lib_item_group","id","item_name");
        $sql="SELECT a.id as ID, a.item_category_id as ITEM_CATEGORY_ID, a.item_description as ITEM_DESCRIPTION, a.item_group_id as ITEM_GROUP_ID
        from product_details_master a
        where a.id in ($data_id) and a.status_active in(1,3) ";
        $nameArray=sql_select($sql);

        foreach ($nameArray as $val)
        {
            ?>
            <tr class="general" id="tr_<? echo $i;?>">
                <td>
                <?
                    echo create_drop_down( "cboServiceFor_".$i, 90, $service_for_arr, "", 1, "-- Select --",$selected, 0, "", "", "", "", "", "", "", "cboServiceFor[]", "cboServiceFor_".$i );
                ?>
                </td>
                <td align="center">
                    <input type="text" name="txtServiceDetails[]" id="txtServiceDetails_<?=$i;?>" class="text_boxes" style="width:90px;" onDblClick="fncServiceDetails(<?=$i;?>)" placeholder="Double Click To Search" readonly/>
                     <input type="hidden" name="hdnServiceId[]" id="hdnServiceId_<?=$i;?>" />
                </td>

                <td align="center">
                    <input type="text" name="txtItemDescription[]"; id="txtItemDescription_<?= $i; ?>" class="text_boxes" style="width:130px;" onDblClick="itemDetailsPopup(<?= $i;?>)" placeholder="Double Click To Search" value="<? echo $val["ITEM_DESCRIPTION"]; ?>" />
                    <input type="hidden" name="txtItemId[]" id="txtItemId_<? echo $i;?>" value="<? echo $val["ID"];?>" />
                    <input type="hidden" name="txtRowId[]" id="txtRowId_<? echo $i;?>" value="" />
                </td>
                <td align="center">
                    <?
                    echo create_drop_down( "cboItemCategory_".$i, 120, $item_category,"", 1, "-- Select --", $val["ITEM_CATEGORY_ID"], "",1,"","","","","","","cboItemCategory[]","cboItemCategory_".$i );
                    ?>
                </td>
                <td align="center">
                    <?
                    echo create_drop_down( "cboItemGroup_".$i,100,$lib_item_group,"", 1,"Select",$val["ITEM_GROUP_ID"], "",1,"","","","","","","cboItemGroup[]","cboItemGroup_".$i );
                    ?>
                </td>
                <td align="center">
                    <? echo create_drop_down( "cboUom_".$i, 70, $service_uom_arr,"", 1, "--Select--", 0, "",0, "", "", "", "", "", "", "cboUom[]", "cboUom_".$i ); ?>
                </td>
                <td>
                    <input type="text" name="txtQnty[]" id="txtQnty_<?= $i; ?>" class="text_boxes_numeric" style="width:60px" onKeyUp="calculate_amount(<?= $i; ?>)"/>
                </td>
                <td>
                    <input type="text" name="txtRate[]" id="txtRate_<?= $i; ?>" class="text_boxes_numeric" value="" style="width:60px;" onKeyUp="calculate_amount(<?= $i; ?>)" />
                </td>
                <td><input type="text" name="txtAmount[]" id="txtAmount_<?= $i; ?>" class="text_boxes_numeric" style="width:60px;" readonly /></td>
                <td><input type="text" name="txtRemarks[]" id="txtRemarks_<?= $i; ?>" class="text_boxes" style="width:120px;" /></td>
                <td>
                    <input type="text" name="txtTagMaterialsName[]" id="txtTagMaterialsName_<?= $i; ?>" class="text_boxes" style="width:90px;" onDblClick="fncItemTagMaterials(<?= $i; ?>)" placeholder="Double Click To Search" readonly/>
					<input type="hidden" name="txtTagMaterials[]" id="txtTagMaterials_<?= $i; ?>" readonly/>
                </td>
                <td>
                    <input type="button" name="increase[]" id="increase_<?= $i; ?>" style="width:18px" class="formbuttonplasminus" value="+" onClick="add_dtls_tr(<?= $i; ?>)" />
                    <input type="button" name="decrease[]" id="decrease_<?= $i; ?>" style="width:18px" class="formbuttonplasminus" value="-" onClick="fnc_delet_dtls_tr(<?= $i; ?>);" />
                </td>
            </tr>
            <?
            $i++;
        }
    }
    exit();
}

if($action=="remarks_popup")
{
	echo load_html_head_contents("Remarks","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
		<script>
        function js_set_value(val)
        {
            document.getElementById('text_new_remarks').value=val;
            parent.emailwindow.hide();
        }
        </script>
    </head>
    <body>
    <div align="center">
        <fieldset style="width:400px;margin-left:4px;">
            <form name="remarksfrm_1"  id="remarksfrm_1" autocomplete="off">
                <table cellpadding="0" cellspacing="0" width="370" >
                    <tr>
                        <td align="center"><input type="hidden" name="auto_id" id="auto_id" value="<? echo $data; ?>" />
                          <textarea id="text_new_remarks" name="text_new_remarks" class="text_area" title="Maximum 1000 Character" maxlength="1000" style="width:370px; height:250px" placeholder="Remarks Here. Maximum 1000 Character." ><? echo $data; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                     <input type="button" id="formbuttonplasminus" align="middle" class="formbutton" style="width:100px" value="Close" onClick="js_set_value(document.getElementById('text_new_remarks').value)" />
                        </td>
                    </tr>
                </table>
            </form>
        </fieldset>
    </div>    
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
    
}


if ($action=="append_load_details_container")
{
    //echo $data;
    $i = $data;
    ?>
    <tr class="general" id="tr_<?= $i; ?>">
        <td>
            <?
                echo create_drop_down( "cboServiceFor_".$i, 90, $service_for_arr, "", 1, "-- Select --",$selected, 0, "", "", "", "", "", "", "", "cboServiceFor[]", "cboServiceFor_".$i );
            ?>
        </td>
        <td align="center">
            <input type="text" name="txtServiceDetails[]" id="txtServiceDetails_<?=$i;?>" class="text_boxes" style="width:90px;" onDblClick="fncServiceDetails(<?=$i;?>)" placeholder="Double Click To Search" readonly/>
             <input type="hidden" name="hdnServiceId[]" id="hdnServiceId_<?=$i;?>" />
            
        </td>

        <td align="center">
            <input type="text" name="txtItemDescription[]" id="txtItemDescription_<?= $i; ?>" class="text_boxes" style="width:130px;" onDblClick="itemDetailsPopup(<?= $i; ?>)" placeholder="Double Click To Search" readonly />
            <input type="hidden" name="txtItemId[]" id="txtItemId_<? echo $i; ?>" />
            <input type="hidden" name="txtRowId[]" id="txtRowId_<? echo $i; ?>" value="" />
        </td>
        <td align="center">
            <? echo create_drop_down( "cboItemCategory_".$i, 120, $blank_array,"", 1, "--Select--", 0, "", 1, "", "", "", "", "", "", "cboItemCategory[]", "cboItemCategory_".$i ); ?>
        </td>
        <td align="center">
            <? echo create_drop_down( "cboItemGroup_".$i, 120, $blank_array,"", 1, "--Select--", 0, "",1, "", "", "", "", "", "", "cboItemGroup[]", "cboItemGroup_".$i ); ?>
        </td>
        <td align="center">
            <? echo create_drop_down( "cboUom_".$i, 70, $service_uom_arr,"", 0, "--Select--", 0, "",0, "", "", "", "", "", "", "cboUom[]", "cboUom_".$i ); ?>
        </td>
        <td>
            <input type="text" name="txtQnty[]" id="txtQnty_<?= $i; ?>" class="text_boxes_numeric" style="width:60px" onKeyUp="calculate_amount(<?= $i; ?>)"/>
        </td>
        <td>
            <input type="text" name="txtRate[]" id="txtRate_<?= $i; ?>" class="text_boxes_numeric" value="" style="width:60px;" onKeyUp="calculate_amount(<?= $i; ?>)" />
        </td>
        <td><input type="text" name="txtAmount[]" id="txtAmount_<?= $i; ?>" class="text_boxes_numeric" style="width:60px;" readonly /></td>

        <td><input type="text" name="txtRemarks[]" id="txtRemarks_<?= $i; ?>" class="text_boxes" style="width:120px;" onDblClick="openmypage_remarks(<? echo $i;?>)" /></td>

        <td>
            <input type="text" name="txtTagMaterialsName[]" id="txtTagMaterialsName_<?= $i; ?>" class="text_boxes" style="width:90px;" onDblClick="fncItemTagMaterials(<?= $i; ?>)" placeholder="Double Click To Search" readonly/>
			<input type="hidden" name="txtTagMaterials[]" id="txtTagMaterials_<?= $i; ?>" readonly/>
        </td>
        <td>
            <input type="button" name="increase[]" id="increase_<?= $i; ?>" style="width:18px" class="formbuttonplasminus" value="+" onClick="add_dtls_tr(<?= $i; ?>)" />
            <input type="button" name="decrease[]" id="decrease_<?= $i; ?>" style="width:18px" class="formbuttonplasminus" value="-" onClick="fnc_delet_dtls_tr(<?= $i; ?>);" />
        </td>
    </tr>
    <?
    exit();
}

if($action=="show_dtls_listview_update")
{
    $lib_item_des=return_library_array("SELECT id, item_description from product_details_master ","id","item_description");

    $sql = "SELECT b.id as ID, b.product_id as PRODUCT_ID, b.quantity as QUANTITY, b.rate as RATE, b.amount as AMOUNT, b.remarks as REMARKS, b.service_for as SERVICE_FOR, b.service_details as SERVICE_DETAILS, b.tag_materials as TAG_MATERIALS,b.service_uom as SERVICE_UOM, b.service_lib_id as SERVICE_LIB_ID 
	from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b 
	where a.id=$data and a.id=b.mst_id and a.entry_form=526 and b.status_active=1 and b.is_deleted=0 order by b.id";
    //echo $sql;die;
    $result = sql_select($sql);
    foreach ($result as $val) {
        if ($val['PRODUCT_ID'] != '')
        $prod_id.=$val['PRODUCT_ID'].',';
    }

    $prod_ids=rtrim($prod_id,',');
    if ($prod_ids != ''){
        $sql_prod=sql_select("SELECT ID as id, ITEM_DESCRIPTION as item_description, ITEM_CATEGORY_ID as item_category_id, ITEM_GROUP_ID as item_group_id from product_details_master where id in($prod_ids) and status_active in(1,3) and is_deleted=0");
        $prod_arr=array();
        foreach ($sql_prod as $val) {
           $prod_arr[$val['ID']]['item_description']=$val['ITEM_DESCRIPTION'];
           $prod_arr[$val['ID']]['item_category_id']=$val['ITEM_CATEGORY_ID'];
           $prod_arr[$val['ID']]['item_group_id']=$val['ITEM_GROUP_ID'];
        }
    }

    $i=1;
    foreach($result as $val)
    {
        $item_nam='';
        if($val["TAG_MATERIALS"])
        {
            $tag_arr=explode(",",$val["TAG_MATERIALS"]);
            foreach($tag_arr as $row)
            {$item_nam.=$lib_item_des[$row].', ';}
            $item_nam=rtrim($item_nam,", ");
        }
        ?>
        <tr class="general" id="tr_<?= $i; ?>">
            <td>
                <? echo create_drop_down( "cboServiceFor_".$i, 90, $service_for_arr, "", 1, "-- Select --",$val["SERVICE_FOR"], "", "", "", "", "", "", "", "", "cboServiceFor[]", "cboServiceFor_".$i ); ?>
            </td>
            <td align="center">
                <input type="text" name="txtServiceDetails[]" id="txtServiceDetails_<?= $i; ?>" class="text_boxes" style="width:90px;" onDblClick="fncServiceDetails(<?=$i;?>)" placeholder="Double Click To Search" value="<? echo $val["SERVICE_DETAILS"];?>" readonly/>
                <input type="hidden" name="hdnServiceId[]" id="hdnServiceId_<?= $i; ?>" value="<? echo $val["SERVICE_LIB_ID"];?>" />
            </td>
            <td align="center">
                <input type="text" name="txtItemDescription[]" id="txtItemDescription_<?= $i; ?>" class="text_boxes" style="width:130px;" onDblClick="itemDetailsPopup(<?= $i; ?>)" placeholder="Double Click To Search" value="<? echo $prod_arr[$val['PRODUCT_ID']]['item_description'];?>" <? if($val["SERVICE_LIB_ID"]>0) echo " disabled readonly"; ?>  />
                <input type="hidden" name="txtItemId[]" id="txtItemId_<? echo $i; ?> " value="<? echo $val["PRODUCT_ID"];?>"/>
                <input type="hidden" name="txtRowId[]" id="txtRowId_<? echo $i; ?>" value="<? echo $val["ID"];?>" />
            </td>
            <td align="center">
                <? echo create_drop_down( "cboItemCategory_".$i, 120, $item_category,"", 1, "-- Select --", $prod_arr[$val['PRODUCT_ID']]['item_category_id'], "",1,"","","","","","","cboItemCategory[]","cboItemCategory_".$i ); ?>
            </td>
            <td align="center">
                <? echo create_drop_down( "cboItemGroup_".$i,100,"select id,item_name  from lib_item_group","id,item_name", 1,"Select",$prod_arr[$val['PRODUCT_ID']]['item_group_id'], "",1,"","","","","","","cboItemGroup[]","cboItemGroup_".$i ); ?>
            </td>
            <td align="center">
                <? echo create_drop_down( "cboUom_".$i, 70, $service_uom_arr,"", 1, "--Select--", $val["SERVICE_UOM"], "","", "", "", "", "", "", "", "cboUom[]", "cboUom_".$i ); ?>
            </td>
            <td>
                <input type="text" name="txtQnty[]" id="txtQnty_<?= $i; ?>" class="text_boxes_numeric" style="width:60px" value="<? echo $val["QUANTITY"];?>" onKeyUp="calculate_amount(<?= $i; ?>)"/>
            </td>
            <td>
                <input type="text" name="txtRate[]" id="txtRate_<?= $i; ?>" class="text_boxes_numeric" style="width:60px;"  value="<? echo $val["RATE"];?>" onKeyUp="calculate_amount(<?= $i; ?>)" />
            </td>
            <td><input type="text" name="txtAmount[]" id="txtAmount_<?= $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $val["AMOUNT"];?>" readonly /></td>
            <td><input type="text" name="txtRemarks[]" id="txtRemarks_<?= $i; ?>" class="text_boxes" style="width:120px;" value="<? echo $val["REMARKS"];?>" onDblClick="openmypage_remarks(<? echo $i;?>)"/></td>
            <td>
                <input type="text" name="txtTagMaterialsName[]" id="txtTagMaterialsName_<?= $i; ?>" class="text_boxes" style="width:90px;" onDblClick="fncItemTagMaterials(<?= $i; ?>)" placeholder="Double Click To Search" value="<? echo $item_nam;?>" readonly/>
				<input type="hidden" name="txtTagMaterials[]" id="txtTagMaterials_<?= $i; ?>" value="<? echo $val["TAG_MATERIALS"];?>" readonly/>
            </td>
            <td>
                <input type="button" name="increase[]" id="increase_<?= $i; ?>" style="width:18px" class="formbuttonplasminus" value="+" onClick="add_dtls_tr(<?= $i; ?>)" />
                <input type="button" name="decrease[]" id="decrease_<?= $i; ?>" style="width:18px" class="formbuttonplasminus" value="-" onClick="fnc_delet_dtls_tr(<?= $i; ?>);" />
            </td>
        </tr>
        <?
        $i++;
    }
    exit();
}

if($action=="save_update_delete")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));

    if(str_replace("'","",$hidden_delivery_info_dtls)!=''){
        $txt_delivery_place=$hidden_delivery_info_dtls;
    }
    if ($operation==0) // Insert Here----------------------------------------------------------
    {
        $con = connect();
        if($db_type==0) { mysql_query("BEGIN"); }

        $id=return_next_id("id", "inv_purchase_requisition_mst", 1);

        if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";

        $new_req_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SRQ', date("Y",time()), 5, "SELECT requ_no_prefix, requ_prefix_num from inv_purchase_requisition_mst where company_id=$cbo_company_name $insert_date_con and entry_form = 526 order by id desc", "requ_no_prefix", "requ_prefix_num" ));
        //echo "10**".$new_req_number[0];die;
        $field_array_mst="id, requ_no, requ_no_prefix, requ_prefix_num, entry_form, company_id, location_id, division_id, department_id, section_id, requisition_date, tag_requisition_no, tag_requisition_id, store_name, pay_mode, req_by, cbo_currency, delivery_date, ready_to_approve, manual_req, remarks, status_active, is_deleted, inserted_by, insert_date";

        $field_array="id, mst_id, product_id, item_category, quantity, rate, amount, remarks, service_for, service_details, service_lib_id, tag_materials, service_uom, status_active, is_deleted, inserted_by, insert_date";
        $dtlsid=return_next_id("id", "inv_purchase_requisition_dtls", 1);
        $data_array="";
        $total_row = str_replace("'","",$total_row);
        for($i=1;$i<=$total_row;$i++)
        {
            if($i>1) $data_array.=",";
            $cboServiceFor      = "cboServiceFor_".$i;
            $txtServiceDetails  = "txtServiceDetails_".$i;
			$hdnServiceId       = "hdnServiceId_".$i;
            $item_category      = "cboItemCategory_".$i;
            $txtQnty            = "txtQnty_".$i;
            $txtRate            = "txtRate_".$i;
            $txtAmount            = "txtAmount_".$i;
            $txtTagMaterials    = "txtTagMaterials_".$i;
            $txtRemarks         = "txtRemarks_".$i;
            $item_id            = "txtItemId_".$i;
            $cboUom             = "cboUom_".$i;
			
			

            if( str_replace("'","",$$txtQnty) != "" )
            {
                $data_array.="(".$dtlsid.",".$id.",'".$$item_id."','".$$item_category."','".$$txtQnty."','".$$txtRate."','".$$txtAmount."','".$$txtRemarks."','".$$cboServiceFor."','".$$txtServiceDetails."','".$$hdnServiceId."','".$$txtTagMaterials."','".$$cboUom."',1,0,'".$user_id."','".$pc_date_time."')";
                $dtlsid = $dtlsid + 1;
            }
        }

        $data_array_mst="(".$id.",'".$new_req_number[0]."','".$new_req_number[1]."','".$new_req_number[2]."',526,".$cbo_company_name.",".$cbo_location_name.",".$cbo_division_name.",".$cbo_department_name.",".$cbo_section_zname.",".$txt_req_date.",".$txt_tag_req.",".$txt_tag_req_id.",".$cbo_store_name.",".$cbo_pay_mode.",".$txt_req_by.",".$cbo_currency.",".$txt_delivery_date.",".$cbo_ready_to_approved.",".$txt_manual_req_no.",".$txt_remarks.",1,0,'".$user_id."','".$pc_date_time."')";
        // echo "10** insert into inv_purchase_requisition_dtls ($field_array) values $data_array";die;
        // echo "10** insert into inv_purchase_requisition_mst ($field_array_mst) values $data_array_mst";die;
        $rID=sql_insert("inv_purchase_requisition_mst",$field_array_mst,$data_array_mst,1);
        $dtlsrID=sql_insert("inv_purchase_requisition_dtls",$field_array,$data_array,1);
        // echo "5**".$rID."**".$dtlsrID; die;

        if($db_type==0)
        {
            if($rID && $dtlsrID)
            {
                mysql_query("COMMIT");
                echo "0**".$new_req_number[0]."**".$id;
            }
            else
            {
                mysql_query("ROLLBACK");
                echo "10**";
            }
        }
        if($db_type==2 || $db_type==1 )
        {
            if($rID && $dtlsrID)
            {
                oci_commit($con);
                echo "0**".$new_req_number[0]."**".$id;
            }
            else
            {
                oci_rollback($con);
                echo "10**";
            }
        }
        disconnect($con);
        die;
    }
    else if ($operation==1) // Update Here----------------------------------------------------------
    {
        $update_id=str_replace("'","",$update_id);
        $total_row = str_replace("'","",$total_row);
         // Delete Part
        $dtlsUpdate_array = array();

        $approved_sql = "select a.is_approved from inv_purchase_requisition_mst a where a.id=$update_id  and a.entry_form=526 and a.status_active=1 and a.is_approved!=0 and a.is_deleted=0";

        $approved_arr=sql_select($approved_sql);

        if(count($approved_arr)>0)
        {
            if($approved_arr[0][csf('is_approved')]==1)
            {
                echo "13**Update Or Delete not allowed. Full Approved Found";
                die;
            }
            else
            {
                echo "13**Update Or Delete not allowed. Partial Approved Found";
                die;
            }
        }

        $sql_dtls="SELECT b.id from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and b.mst_id=$update_id and b.status_active=1 and b.is_deleted=0 and a.entry_form=526 and a.status_active=1 and a.is_deleted=0";
        $nameArray=sql_select($sql_dtls);

        foreach($nameArray as $row)
        {
            $dtlsUpdate_array[]=$row[csf('id')];
        }
        for($i=1;$i<=$total_row;$i++)
        {
            $dtls_ID            = "txtRowId_".$i;
            $txtAmount          = "txtAmount_".$i;
            $dtls_ID=str_replace("'",'',$$dtls_ID);
            $dtlsIdArr[$dtls_ID]['id']=$dtls_ID;
            $dtlsIdArr[$dtls_ID]['amount']=str_replace("'",'',$$txtAmount);
        }
        $wo_result=sql_select("SELECT a.wo_number,b.requisition_dtls_id,sum(b.amount) as amount from wo_non_order_info_mst a,wo_non_order_info_dtls b where a.id=b.mst_id and a.entry_form =484 and a.status_active=1 and b.status_active=1 and b.requisition_dtls_id in  (select c.id from inv_purchase_requisition_dtls c where c.mst_id = $update_id) group by a.wo_number,b.requisition_dtls_id ");
        foreach ($wo_result as $row)
        {
            //if(empty($dtlsIdArr[$row[csf('requisition_dtls_id')]]['id']))
            if($dtlsIdArr[$row[csf('requisition_dtls_id')]]['id'])
            {
                echo "11**".$row[csf('wo_number')];
                die;
            }
            else if($dtlsIdArr[$row[csf('requisition_dtls_id')]]['amount']<$row[csf('amount')])
            {
                echo "12**".$row[csf('amount')];
                die;
            }
        }

        $con = connect();
        if($db_type==0) { mysql_query("BEGIN"); }



        $field_array_insert="id, mst_id, product_id, item_category, quantity, rate, amount, remarks, service_for, service_details, service_lib_id, tag_materials,service_uom, status_active, is_deleted, inserted_by, insert_date";

        $field_array="product_id*item_category*quantity*rate*amount*remarks*service_for*service_details*service_lib_id*tag_materials*service_uom*status_active*is_deleted*updated_by*update_date";

        $dtlsid=return_next_id("id", "inv_purchase_requisition_dtls", 1);
        //$dtlsid_check=return_next_id("id", "inv_purchase_requisition_dtls", 1);
        $data_array=array(); $data_array_insert="";
        for($i=1;$i<=$total_row;$i++)
        {

            $cboServiceFor      = "cboServiceFor_".$i;
            $txtServiceDetails  = "txtServiceDetails_".$i;
			$hdnServiceId       = "hdnServiceId_".$i;
            $item_category      = "cboItemCategory_".$i;
            $txtQnty            = "txtQnty_".$i;
            $txtRate            = "txtRate_".$i;
            $txtAmount          = "txtAmount_".$i;
            $txtTagMaterials    = "txtTagMaterials_".$i;
            $txtRemarks         = "txtRemarks_".$i;
            $item_id            = "txtItemId_".$i;
            $dtls_ID            = "txtRowId_".$i;
            $cboUom             = "cboUom_".$i;
             
            if(str_replace("'",'',$$dtls_ID)=="0" || str_replace("'",'',$$dtls_ID)=='') // new insert
            {
              
                if( str_replace("'","",$$txtQnty) != "" )
                {
                     
                    if($data_array_insert!="") $data_array_insert .=",";
                    $data_array_insert.="(".$dtlsid.",".$update_id.",'".$$item_id."','".$$item_category."','".$$txtQnty."','".$$txtRate."','".$$txtAmount."','".$$txtRemarks."','".$$cboServiceFor."','".$$txtServiceDetails."','".$$hdnServiceId."','".$$txtTagMaterials."','".$$cboUom."',1,0,'".$user_id."','".$pc_date_time."')";
                    $dtlsid=$dtlsid+1;
                }
            }
            else  // Update
            {
                $deleteId_array[]=str_replace("'",'',$$dtls_ID);
                $updateId_array[]=str_replace("'",'',$$dtls_ID);
                $data_array[str_replace("'",'',$$dtls_ID)]=explode("*",("'".$$item_id."'*'".$$item_category."'*'".$$txtQnty."'*'".$$txtRate."'*'".$$txtAmount."'*'".$$txtRemarks."'*'".$$cboServiceFor."'*'".$$txtServiceDetails."'*'".$$hdnServiceId."'*'".$$txtTagMaterials."'*'".$$cboUom."'*1*0*'".$user_id."'*'".$pc_date_time."'"));
            }

        }

        if($update_id>0)
        {
            $field_array_mst="company_id*location_id*division_id*department_id*section_id*requisition_date*store_name*tag_requisition_no*tag_requisition_id*pay_mode*req_by*cbo_currency*delivery_date*ready_to_approve*manual_req*remarks*updated_by*update_date";
            $data_array_mst="".$cbo_company_name."*".$cbo_location_name."*".$cbo_division_name."*".$cbo_department_name."*".$cbo_section_zname."*".$txt_req_date."*".$cbo_store_name."*".$txt_tag_req."*".$txt_tag_req_id."*".$cbo_pay_mode."*".$txt_req_by."*".$cbo_currency."*".$txt_delivery_date."*".$cbo_ready_to_approved."*".$txt_manual_req_no."*".$txt_remarks."*".$user_id."*'".$pc_date_time."'";
        }



        if(implode(',',$deleteId_array) != "")
        {
            $distance_delete_id = array_diff($dtlsUpdate_array, $deleteId_array);
        }
        else
        {
            $distance_delete_id = $dtlsUpdate_array;
        }

        $rID=$deleterID=$dtlsrID=$dtlsUpdaterID=true;
        $field_array_dtls_del="updated_by*update_date*status_active*is_deleted";
        $data_array_dtls_del="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
        if(implode(',',$distance_delete_id) != "")
        {
            foreach($distance_delete_id as $id_val)
            {
                $deleterID=sql_delete("inv_purchase_requisition_dtls",$field_array_dtls_del,$data_array_dtls_del,"id","$id_val",1);
            }
        }

        if($update_id>0)
        {
            $rID=sql_update("inv_purchase_requisition_mst",$field_array_mst,$data_array_mst,"id",$update_id,1);
        }

        if($data_array_insert!="")
        {
            //echo "10** insert into inv_purchase_requisition_dtls ($field_array_insert) values $data_array_insert";die;
            $dtlsrID=sql_insert("inv_purchase_requisition_dtls",$field_array_insert,$data_array_insert,1);
        }
        if(count($updateId_array)>0)
        {
            $dtlsUpdaterID=execute_query(bulk_update_sql_statement("inv_purchase_requisition_dtls","id",$field_array,$data_array,$updateId_array));
        }
        // echo "10**".$rID."**".$deleterID."**".$dtlsrID."**".$dtlsUpdaterID; die;

        if($db_type==0)
        {
            if($rID && $deleterID && $dtlsrID && $dtlsUpdaterID)
            {
                mysql_query("COMMIT");
                echo "1**".str_replace("'","",$txt_req_no)."**".$update_id;
            }
            else
            {
                mysql_query("ROLLBACK");
                echo "10**";
            }
        }
        if($db_type==2 || $db_type==1 )
        {
            if($rID && $deleterID && $dtlsrID && $dtlsUpdaterID)
            {
                oci_commit($con);
                echo "1**".str_replace("'","",$txt_req_no)."**".$update_id;
            }
            else
            {
                oci_rollback($con);
                echo "10**";
            }
        }
        disconnect($con);
        die;
    }
    else if ($operation==2) // Delete Here--------------------
    {
        $sql=sql_select("SELECT a.wo_number from wo_non_order_info_mst a,wo_non_order_info_dtls b where a.id=b.mst_id and a.entry_form =484 and a.status_active=1 and b.status_active=1 and b.requisition_dtls_id in (select c.id from inv_purchase_requisition_dtls c where c.mst_id = $update_id)");

        $approved_sql = "select a.is_approved from inv_purchase_requisition_mst a where a.id=$update_id  and a.entry_form=526 and a.status_active=1 and a.is_approved!=0 and a.is_deleted=0";

        $approved_arr=sql_select($approved_sql);

        if(count($approved_arr)>0)
        {
            if($approved_arr[0][csf('is_approved')]==1)
            {
                echo "13**Update Or Delete not allowed. Full Approved Found";
                die;
            }
            else
            {
                echo "13**Update Or Delete not allowed. Partial Approved Found";
                die;
            }
        }

        if(count($sql))
        {
            if($db_type==0)
            {
                mysql_query("ROLLBACK");
            }
            else if($db_type==2 || $db_type==1 )
            {
                oci_rollback($con);
            }
            echo "11**".$sql[0][csf('wo_number')];
            disconnect($con);
            die;
        }
        $con = connect();
        if($db_type==0) { mysql_query("BEGIN"); }
        $rID = sql_update("inv_purchase_requisition_mst",'status_active*is_deleted','0*1',"id",$update_id,0);
        $dtlsrID = sql_update("inv_purchase_requisition_dtls",'status_active*is_deleted','0*1',"mst_id",$update_id,1);
        if($db_type==0)
        {
            if($rID && $dtlsrID)
            {
                mysql_query("COMMIT");
                echo "2**".str_replace("'","",$txt_req_no);
            }
            else
            {
                mysql_query("ROLLBACK");
                echo "10**";
            }
        }
        if($db_type==2 || $db_type==1 )
        {
            if($rID && $dtlsrID)
            {
                oci_commit($con);
                echo "2**".str_replace("'","",$rID);
            }
            else
            {
                oci_rollback($con);
                echo "10**";
            }
            //echo "2**".$rID;
        }
        disconnect($con);
        die;
    }
}

if($action=="req_popup")
{
    extract($_REQUEST);
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    ?>

    <script>
    function js_set_value(requ_no)
    {
        $("#hidden_wo_number").val(requ_no);
        //alert(requ_no);return;
        parent.emailwindow.hide();
    }

    </script>
    </head>

    <body>
    <div align="center" style="width:100%;" >
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="800" cellspacing="0" cellpadding="0" class="rpt_table" align="center">
            <tr>
                <td align="center" width="100%">
                    <table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                         <thead>
                            <th width="160">Item Category</th>
                            <th width="160" align="center">Req. Number</th>
                            <th width="200">Req. Date Range</th>
                            <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                        </thead>
                        <tr>
                            <td width="160">
                            <?
                                echo create_drop_down( "cbo_item_category", 160, "SELECT category_id, short_name from  lib_item_category_list where status_active=1 and is_deleted=0 and category_type=1 and category_id not in(4,11) order by short_name","category_id,short_name", 1, "-- Select --", "", "","","4,11");
                            ?>
                            </td>
                            <td width="160" align="center">
                                <input type="text" style="width:140px" class="text_boxes"  name="txt_req" id="txt_req" />
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                            </td>
                            <td align="center">
                                <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_item_category').value+'_'+document.getElementById('txt_req').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_req_search_list_view', 'search_div', 'service_requisition_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td  align="center" height="40" valign="middle">
                    <? echo load_month_buttons(1);  ?>
                    <input type="hidden" id="hidden_wo_number" name="hidden_wo_number" value="" />
                </td>
            </tr>
            <tr>
            <td align="center" valign="top" id="search_div"></td>
            </tr>
        </table>
        </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}

if($action=="create_req_search_list_view")
{

    extract($_REQUEST);
    $ex_data = explode("_",$data);
    $itemCategory = $ex_data[0];
    $txt_req_no = $ex_data[1];
    $txt_date_from = $ex_data[2];
    $txt_date_to = $ex_data[3];
    $company = $ex_data[4];

    $sql_cond="";
    if(trim($itemCategory)) $sql_cond .= " and b.item_category='$itemCategory'";
    if ($txt_req_no!="") $sql_cond .= " and a.requ_no like '%".trim($txt_req_no)."'";

    if ($txt_date_from!="" &&  $txt_date_to!="")
    {
        if($db_type==0)
        {
            $sql_cond .= " and a.requisition_date between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-")."'";
        }
        else
        {
            $sql_cond .= " and a.requisition_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
        }
    }

    if (trim($company) !="") $sql_cond .= " and a.company_id='$company'";

    $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
    
    $sql = "SELECT a.id, a.requ_no, a.company_id, a.requisition_date, a.pay_mode,a.inserted_by from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and a.entry_form = 526 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond group by a.id, a.requ_no, a.company_id, a.requisition_date, a.pay_mode,a.inserted_by order by a.id desc";
    // echo $sql;die;
    $result = sql_select($sql);
    $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

    $arr=array(0=>$company_arr,3=>$pay_mode,4=>$user_lib_name);
    
    echo  create_list_view("list_view", "Company, Req. Number, Req. Date, Pay Mode, Insert User", "150,150,150,150,150","800","250",0, $sql, "js_set_value", "requ_no,id", "", 1, "company_id,0,0,pay_mode,inserted_by", $arr , "company_id,requ_no,requisition_date,pay_mode,inserted_by", "",'','0,0,3,0');
    exit();
}

if($action=="populate_data_from_search_popup")
{

    $sql_cause="select MAX(id) as id from fabric_booking_approval_cause where entry_form=61 and booking_id='$data' and approval_type=0 and status_active=1 and is_deleted=0";
	//echo $sql_cause; //die;
  	$app_cause = '';
	$nameArray_cause=sql_select($sql_cause);
	if(count($nameArray_cause)>0){
		foreach($nameArray_cause as $row)
		{
			$app_cause1=return_field_value("approval_cause", "fabric_booking_approval_cause", "id='".$row[csf("id")]."' and status_active=1 and is_deleted=0");
			$app_cause = str_replace(array("\r", "\n"), ' ', $app_cause1);
		}
	}

    $sql = "select id as id, company_id as company_id, location_id as location_id, division_id as division_id, department_id as department_id, section_id as section_id, requisition_date as requisition_date, store_name as store_name, pay_mode as pay_mode, req_by as req_by, cbo_currency as cbo_currency, delivery_date as delivery_date, ready_to_approve as ready_to_approve, manual_req as manual_req, remarks as remarks, tag_requisition_no as TAG_REQUISITION_NO, tag_requisition_id as TAG_REQUISITION_ID, is_approved from inv_purchase_requisition_mst where id='$data'";
    //echo $sql;die;
    $result = sql_select($sql);
    foreach($result as $resultRow)
    {
        echo "$('#cbo_company_name').val('".$resultRow["COMPANY_ID"]."');\n";
        echo "$('#cbo_location_name').val('".$resultRow["LOCATION_ID"]."');\n";
        echo "document.getElementById('txt_not_approve_cause').value 		= '".$app_cause."';\n";
        echo "load_drop_down('requires/service_requisition_controller', '".$resultRow["LOCATION_ID"]."', 'load_drop_down_store','store_td');\n";
        echo "$('#cbo_division_name').val('".$resultRow["DIVISION_ID"]."');\n";
        if($resultRow["DIVISION_ID"]>0)
        {
            echo "load_drop_down( 'requires/service_requisition_controller','".$resultRow["DIVISION_ID"]."', 'load_drop_down_department','department_td');\n";
        }
        if($resultRow["DEPARTMENT_ID"]>0)
        {
            echo "load_drop_down( 'requires/service_requisition_controller','".$resultRow["DEPARTMENT_ID"]."', 'load_drop_down_section','section_td');\n";
        }

        echo "$('#cbo_department_name').val('".$resultRow["DEPARTMENT_ID"]."');\n";
        echo "$('#cbo_section_zname').val('".$resultRow["SECTION_ID"]."');\n";
        echo "$('#txt_req_date').val('".change_date_format($resultRow["REQUISITION_DATE"])."');\n";
        echo "$('#cbo_store_name').val('".$resultRow["STORE_NAME"]."');\n";
        echo "$('#cbo_pay_mode').val('".$resultRow["PAY_MODE"]."');\n";
        echo "$('#txt_req_by').val('".$resultRow["REQ_BY"]."');\n";
        echo "$('#cbo_currency').val('".$resultRow["CBO_CURRENCY"]."');\n";
        echo "$('#txt_delivery_date').val('".change_date_format($resultRow["DELIVERY_DATE"])."');\n";
        echo "$('#cbo_ready_to_approved').val('".$resultRow["READY_TO_APPROVE"]."');\n";
        echo "$('#txt_manual_req_no').val('".$resultRow["MANUAL_REQ"]."');\n";
        echo "$('#txt_remarks').val('".$resultRow["REMARKS"]."');\n";
        echo "$('#txt_tag_req').val('".$resultRow["TAG_REQUISITION_NO"]."');\n";
        echo "$('#txt_tag_req_id').val('".$resultRow["TAG_REQUISITION_ID"]."');\n";


        if($resultRow[csf("is_approved")]==1)
        {
            echo "$('#approved').text('Approved');\n";

        }
        else if($resultRow[csf("is_approved")]==0)
        {
            echo "$('#approved').text('');\n";
        }
        else{

            echo "$('#approved').text('Partial Approved');\n";
        }

    }
    exit();
}

if ($action=="not_approve_cause_popup")
{
	$menu_id=$_SESSION['menu_id'];
	$user_id=$_SESSION['logic_erp']['user_id'];

	echo load_html_head_contents("Un Approval Request","../../../", 1, 1, $unicode);
	extract($_REQUEST);

	$data_all=explode('_',$data);
	?>
    <body>
		<div align="center" style="width:100%;">
            <table align="center" cellspacing="0" width="380" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <thead>
                	<tr>
                		<th>Not Appv. Cause</th>
                	</tr>
                </thead>
                <tbody>
                	<tr>
                		<td><?php echo $data_all[0]; ?></td>
                	</tr>
                </tbody>
            </table>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}


if ($action=="service_requisition_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
    $company=$data[0];
    $mst_id=$data[1];
    $rpt_title=$data[2];
    $template_id=$data[3];
    echo load_html_head_contents($rpt_title,"../../", 1, 1, $unicode,'','')
    ?>
    <link rel="stylesheet" href="../../../css/style_common.css" type="text/css" />
    <?

	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$location_library=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$designation_library=return_library_array("select id,custom_designation from lib_designation",'id','custom_designation');
    $department_library=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section_library=return_library_array("select id,section_name from lib_section",'id','section_name');

    $sql_user_info=sql_select("select id, user_name, user_full_name, designation, department_id from user_passwd where valid=1");
    foreach ($sql_user_info as $row){
        $user_arr[$row[csf('id')]]['user_name']=$row[csf('user_name')];
        $user_arr[$row[csf('id')]]['user_full_name']=$row[csf('user_full_name')];
        $user_arr[$row[csf('id')]]['designation_id']=$row[csf('designation')];
        $user_arr[$row[csf('id')]]['department_id']=$row[csf('department_id')];
    }

    $sql_company=sql_select("SELECT id, company_name, company_short_name, plot_no, level_no, road_no, block_no, city, zip_code from lib_company where status_active=1 and is_deleted=0 and id=$company");
    $com_name=$sql_company[0][csf("company_name")];
    $company_short_name=$sql_company[0][csf("company_short_name")];
    $plot_no=$sql_company[0][csf("plot_no")];
    $level_no=$sql_company[0][csf("level_no")];
    $road_no=$sql_company[0][csf("road_no")];
    $block_no=$sql_company[0][csf("block_no")];
    $city=$sql_company[0][csf("city")];
    $zip_code=$sql_company[0][csf("zip_code")];

    $com_address='';
    if($plot_no !=''){ $com_address.=$plot_no;}
    if($level_no !=''){ $com_address.=", ".$level_no;}
    if($road_no !=''){ $com_address.=", ".$road_no;}
    if($block_no !=''){ $com_address.=", ".$block_no;}
    if($city !=''){ $com_address.=", ".$city;}
    if($zip_code !=''){ $com_address.=", ".$zip_code;}

    $data_sql="SELECT a.requ_no as REQU_NO, a.requisition_date as REQUISITION_DATE, a.delivery_date as DELIVERY_DATE, a.location_id as LOCATION_ID, a.pay_mode as PAY_MODE, a.store_name as STORE_NAME, a.department_id as DEPARTMENT_ID, a.section_id as SECTION_ID, a.remarks as REMARKS, a.req_by as REQ_BY, a.inserted_by as INSERTED_BY, a.cbo_currency as CURRENCY_ID, to_char(a.insert_date, 'DD-MM-YYYY HH:MI:SS AM') as INSERT_DATE, b.user_full_name as USER_FULL_NAME, c.custom_designation as CUSTOM_DESIGNATION, a.is_approved from inv_purchase_requisition_mst a left join user_passwd b on b.id = a.inserted_by left join lib_designation c on c.id = b.designation  where a.id=$mst_id";
    // echo $data_sql;
    $data_result=sql_select($data_sql);
    $inserted_by=$data_result[0]['INSERTED_BY'];
    $currency_id=$data_result[0]['CURRENCY_ID'];

    $sql_dtls= "SELECT a.product_id as PRODUCT_ID, a.quantity as QUANTITY, a.rate as RATE, a.amount as AMOUNT, a.remarks as REMARKS, a.service_for as SERVICE_FOR, a.service_details as SERVICE_DETAILS, a.service_uom as SERVICE_UOM, b.item_description as ITEM_DESCRIPTION
    from inv_purchase_requisition_dtls a
    left join product_details_master b on a.product_id=b.id and b.status_active=1
    where a.mst_id=$mst_id and a.status_active in(1,3) order by a.id";
    // echo $sql_dtls;
    $sql_result= sql_select($sql_dtls);
    $item_description=0;
    foreach($sql_result as $row)
    {
        if($row['ITEM_DESCRIPTION']!=''){ $item_description=1;}
    }
    $com_dtls = fnc_company_location_address($company, $data_result[0]['LOCATION_ID'], 2);


    $sql_approved="select b.approved_by as APPROVED_BY, to_char(b.approved_date, 'dd-mm-yyyy hh:mi:ss am') as APPROVED_DATE from inv_purchase_requisition_mst a, approval_history b where a.id=b.mst_id and a.id=$mst_id and a.entry_form=526 and a.is_approved in(1,3)and b.entry_form=61 and b.current_approval_status=1 and a.status_active=1 and a.is_deleted=0";
    $sql_approved_res=sql_select($sql_approved);
    ?>

	<div id="table_row" style="width:930px;">
        <table align="center" cellspacing="0" width="900" >
            <tbody>
                <tr>
                    <td rowspan="2" width='100' ><img src='../../<? echo $com_dtls[2]; ?>' height='70' width='100' align="middle" /></td>
                    <td  style="font-size:25px;" align="center"><strong><? echo $company_library[$company];  ?></strong></td>
                    <td rowspan="2" width='140' ><div style="font-size:16px; border: solid 2px black;padding:10px 5px;"><b>Service Requisition</b></div></td>
                </tr>
                <tr>
                    <td align="center" style="font-size:21px;"><strong><? echo $com_address;  ?></strong></td>
                </tr>
            </tbody>
        </table>
        <br>
        <table align="center" cellspacing="0" width="900" >
            <tbody>
                <tr>
                    <td width="150" style="font-size:16px;"><b>Rqsn No:</b></td>
                    <td width="150" style="font-size:16px;"><? echo $data_result[0]['REQU_NO']; ?></td>
                    <td width="150" style="font-size:16px;"><b>Rqsn Date:</b></td>
                    <td width="150" style="font-size:16px;"><? echo change_date_format($data_result[0]['REQUISITION_DATE']); ?></td>
                    <td width="150" style="font-size:16px;"><b>Del Date:</b></td>
                    <td style="font-size:16px;" ><? echo change_date_format($data_result[0]['DELIVERY_DATE']); ?></td>
                </tr>
                <tr>
                    <td style="font-size:16px;" ><b>Business Unit:</b></td>
                    <td style="font-size:16px;" ><? echo $location_library[$data_result[0]['LOCATION_ID']]; ?></td>
                    <td style="font-size:16px;" ><b>Pay Mood:</b></td>
                    <td style="font-size:16px;" ><? echo $pay_mode[$data_result[0]['PAY_MODE']]; ?></td>
                    <td style="font-size:16px;" ><b>Store Name:</b></td>
                    <td style="font-size:16px;" ><? echo $store_library[$data_result[0]['STORE_NAME']]; ?></td>
                </tr>
                <tr>
                    <td style="font-size:16px;" ><b>Department:</b></td>
                    <td style="font-size:16px;" ><? echo $department_library[$data_result[0]['DEPARTMENT_ID']]; ?></td>
                    <td style="font-size:16px;" ><b>Section:</b></td>
                    <td style="font-size:16px;" ><? echo $section_library[$data_result[0]['SECTION_ID']]; ?></td>
                    <td style="font-size:16px;" ><b>Req by:</b></td>
                    <td style="font-size:16px;" ><? echo $data_result[0]['REQ_BY']; ?></td>
                </tr>
                <tr>
                    <td style="font-size:16px;" ><b>Remarks:</b></td>
                    <td style="font-size:16px;" colspan="4"><? echo $data_result[0]['REMARKS']; ?></td>
                    <td>
                        <?php
                            if($data_result[0][csf("is_approved")] == 1)
                            {
                                ?>
                                    <div id="approved" style="float:left; font-size:24px; color:#FF0000;">Full Approved</div>
                                <?php
                            }
                            else if($data_result[0][csf("is_approved")]!=0)
                            {
                                ?>
                                    <div id="approved" style="float:left; font-size:24px; color:#FF0000;">Partial Approved</div>
                                <?php
                            }
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <br>
        <table align="center" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
            <thead>
                <th style="font-size:16px;" width="50" >SL</th>
                <th style="font-size:16px;" width="100" >Service Type</th>
                <th style="font-size:16px;" width="180" >Service Details</th>
                <?
                    if($item_description==1)
                    {
                        ?>
                            <th style="font-size:16px;" width="100" >Item Description List</th>
                        <?
                    }
                ?>
                <th style="font-size:16px;" width="100" >Service UOM</th>
                <th style="font-size:16px;" width="80" >Qty.</th>
                <th style="font-size:16px;" width="80" >Rate</th>
                <th style="font-size:16px;" width="80" >Amount</th>
                <th style="font-size:16px;" >Remarks</th>
            </thead>
            <tbody>
            <?
                $i=1;
                foreach($sql_result as $row)
                {
                    if ($i%2==0){ $bgcolor="#E9F3FF";}else{ $bgcolor="#FFFFFF";}
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td style="font-size:16px;"><? echo $i; ?></td>
                        <td style="font-size:16px;" align="center"><? echo $service_for_arr[$row['SERVICE_FOR']]; ?></td>
                        <td style="font-size:16px;" ><? echo $row['SERVICE_DETAILS']; ?></td>
                        <?
                            if($item_description==1)
                            {
                                ?>
                                    <td style="font-size:16px;"><?echo $row['ITEM_DESCRIPTION'];?></td>
                                <?
                            }
                        ?>
                        <td style="font-size:16px;" align="center"><? echo $service_uom_arr[$row['SERVICE_UOM']]; ?></td>
                        <td style="font-size:16px;" align="right"><? echo number_format($row['QUANTITY'],2,".",""); ?></td>
                        <td style="font-size:16px;" align="right"><? echo number_format($row['RATE'],2,".",""); ?></td>
                        <td style="font-size:16px;" align="right"><? echo number_format($row['AMOUNT'],2,".",""); ?></td>
                        <td style="font-size:16px;"><? echo $row['REMARKS']; ?></td>
                    </tr>
                    <?php
                    $i++;
                    $total_qnty+=$row['QUANTITY'];
                    $total_amount+=$row['AMOUNT'];
                }
            ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <?if($item_description==1){?><th>&nbsp;</th><?}?>
                    <th style="font-size:16px;">Grand Total:&nbsp;</th>
                    <th style="font-size:16px;">&nbsp;</th>
                    <th >&nbsp;</th>
                    <th style="font-size:16px;"><?=number_format($total_amount,2);?></th>
                    <th >&nbsp;</th>
                </tr>
                <tr>
                    <td colspan="<?=($item_description==1)?9:8;?>" align="left" style="font-size:16px;"><strong>Total Amount in Word: </strong><?=number_to_words(number_format($total_amount,2))." ".$currency[$currency_id];?> only</td>
                </tr>
            </tfoot>
        </table>
        <br>
    <? 
        $signature_arr=return_library_array( "SELECT MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='user_signature' ",'MASTER_TBLE_ID','IMAGE_LOCATION');
        $appSql="SELECT APPROVED_BY from APPROVAL_HISTORY where ENTRY_FORM=1 and MST_ID = $mst_id ";
         //echo $appSql;die();
        $appSqlRes=sql_select($appSql);
        foreach($appSqlRes as $row){
            $userSignatureArr[$row['APPROVED_BY']]=base_url($signature_arr[$row['APPROVED_BY']]);
        }
    
        if($signature_arr[$inserted_by]){ $userSignatureArr[$inserted_by]=base_url($signature_arr[$inserted_by]); }
    
        echo signature_table(267,$company,"900px",$template_id,10,$inserted_by,$userSignatureArr); 
    ?>
    <!-- <table id="signatureTblId" width="901.5" style="padding-top:20px;">
            <tr>
                <td style="text-align: center; font-size: 18px;" width="345">
                    <strong><?=$data_result[0]['USER_FULL_NAME']?></strong>
                    <br>
                    <strong><?=$data_result[0]['CUSTOM_DESIGNATION']?></strong>
                    <br>
                    <?=$data_result[0]['INSERT_DATE']?>
                </td>

                <td width="95"></td>
                <td style="text-align: center; font-size: 18px;" width="345">
                    <strong><? echo $user_arr[$sql_approved_res[0]['APPROVED_BY']]['user_full_name']; ?></strong>
                    <br>
                    <strong><? echo $designation_library[$user_arr[$sql_approved_res[0]['APPROVED_BY']]['designation_id']]; ?></strong>
                    <br>
                    <? echo $sql_approved_res[0]['APPROVED_DATE']; ?>
                </td>
            </tr>
            <tr>
                <td style="text-align: center; font-size:18px; border-top:1px solid;"><strong>Prepared by</strong></td>
                <td width="75"></td>
                <td style="text-align: center; font-size:18px; border-top:1px solid;"><strong>Approved by</strong></td>
            </tr>
        </table> -->
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <?
    exit();
}

if ($action=="service_requisition_print2")
{
    extract($_REQUEST);
    $data=explode('*',$data);
    $company=$data[0];
    $mst_id=$data[1];
    $rpt_title=$data[2];
    $template_id=$data[3];
    echo load_html_head_contents($rpt_title,"../../", 1, 1, $unicode,'','')
    ?>
        <link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
        <?

        $company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );

        $store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
        $division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
        $department_library=return_library_array("select id,department_name from lib_department",'id','department_name');
        $section_library=return_library_array("select id,section_name from lib_section",'id','section_name');
        $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
        $location_arr=return_library_array( "select id, address from lib_location",'id','address');

        $sql_company=sql_select("SELECT id, company_name, company_short_name, plot_no, level_no, road_no, block_no, city, zip_code from lib_company where status_active=1 and is_deleted=0 and id=$company");
        $com_name=$sql_company[0][csf("company_name")];
        $company_short_name=$sql_company[0][csf("company_short_name")];
        $plot_no=$sql_company[0][csf("plot_no")];
        $level_no=$sql_company[0][csf("level_no")];
        $road_no=$sql_company[0][csf("road_no")];
        $block_no=$sql_company[0][csf("block_no")];
        $city=$sql_company[0][csf("city")];
        $zip_code=$sql_company[0][csf("zip_code")];

        $com_address='';
        if($plot_no !=''){ $com_address.=$plot_no;}
        if($level_no !=''){ $com_address.=", ".$level_no;}
        if($road_no !=''){ $com_address.=", ".$road_no;}
        if($block_no !=''){ $com_address.=", ".$block_no;}
        if($city !=''){ $com_address.=", ".$city;}
        if($zip_code !=''){ $com_address.=", ".$zip_code;}

        $data_sql="SELECT requ_no as REQU_NO, requisition_date as REQUISITION_DATE, delivery_date as DELIVERY_DATE, location_id as LOCATION_ID, pay_mode as PAY_MODE, store_name as STORE_NAME, department_id as DEPARTMENT_ID, section_id as SECTION_ID, remarks as REMARKS, req_by as REQ_BY, inserted_by as INSERTED_BY, cbo_currency as CURRENCY_ID, tag_requisition_no as TAG_REQUISITION_NO, tag_requisition_id as TAG_REQUISITION_ID from inv_purchase_requisition_mst where id=$mst_id";
        // echo $data_sql;
        $data_result=sql_select($data_sql);
        $inserted_by = $data_result[0]['INSERTED_BY'];
        $currency_id = $data_result[0]['CURRENCY_ID'];
        $tag_requ_id = $data_result[0]['TAG_REQUISITION_ID'];

        $sql_dtls= "SELECT a.id as DTLS_ID, a.product_id as PRODUCT_ID, a.quantity as QUANTITY, a.rate as RATE, a.amount as AMOUNT, a.remarks as REMARKS, a.service_for as SERVICE_FOR, a.service_details as SERVICE_DETAILS,c.service_category, a.service_uom as SERVICE_UOM, b.item_description as ITEM_DESCRIPTION, b.item_category_id as ITEM_CATEGORY_ID, b.item_group_id as ITEM_GROUP_ID, a.remarks
        from lib_service_category c,inv_purchase_requisition_dtls a left join product_details_master b on a.product_id=b.id and b.status_active in(1,3) 
		where a.mst_id=$mst_id and a.status_active=1 and a.service_details=c.service_name order by a.id";
        //echo $sql_dtls;
        $sql_result= sql_select($sql_dtls);
        //print_r($sql_result);
        $item_description=0;
        $dtls_data_arr = array();

        // $service_sql= sql_select("select service_category from lib_service_category where service_name in (SELECT a.service_details as SERVICE_DETAILS from inv_purchase_requisition_dtls a left join product_details_master b on a.product_id=b.id and b.status_active in(1,3) where a.mst_id=$mst_id and a.status_active=1)");
        // echo "<pre>";
        //     print_r($service_sql);
    
        $category_cnt=array(); $cnt=1;
        for($i=0,$j=1; $i<count($sql_result);$i++,$j++){
            if($sql_result[$i]['SERVICE_CATEGORY']==$sql_result[$j]['SERVICE_CATEGORY']){
                $cnt++;
            }
            else{
                array_push($category_cnt,$cnt);$cnt=1;
            }
        }
        // echo "<pre>";
        //     print_r($category_cnt);
        

        foreach($sql_result as $row)
        {
            $dtls_data_arr[$row['SERVICE_FOR']][$row['DTLS_ID']]['service_for'] = $row['SERVICE_FOR'];
            $dtls_data_arr[$row['SERVICE_FOR']][$row['DTLS_ID']]['item_description'] = $row['ITEM_DESCRIPTION'];
            $dtls_data_arr[$row['SERVICE_FOR']][$row['DTLS_ID']]['service_details'] = $row['SERVICE_DETAILS'];
            $dtls_data_arr[$row['SERVICE_FOR']][$row['DTLS_ID']]['uom'] = $row['SERVICE_UOM'];
            $dtls_data_arr[$row['SERVICE_FOR']][$row['DTLS_ID']]['qty'] = $row['QUANTITY'];
            $dtls_data_arr[$row['SERVICE_FOR']][$row['DTLS_ID']]['rate'] = $row['RATE'];
            $dtls_data_arr[$row['SERVICE_FOR']][$row['DTLS_ID']]['amount'] = $row['AMOUNT'];
            $dtls_data_arr[$row['SERVICE_FOR']][$row['DTLS_ID']]['item_category_id'] = $row['ITEM_CATEGORY_ID'];
            $dtls_data_arr[$row['SERVICE_FOR']][$row['DTLS_ID']]['item_group_id'] = $row['ITEM_GROUP_ID'];
            $dtls_data_arr[$row['SERVICE_FOR']][$row['DTLS_ID']]['remarks'] = $row['REMARKS'];
        }
        $com_dtls = fnc_company_location_address($company, $data_result[0]['LOCATION_ID'], 2);
        ?>
        <!-- <style type="text/css">
            @media print
            {
                .main_tbl td {
                    margin: 0px;padding: 0px;
                }
                .rpt_tables, .rpt_table{
                    border: 1px solid #dccdcd !important;
                }
            }
        </style> -->
       <style>
         .tabs {
         border: 1px solid;
          }

        /* table {
        border-collapse: collapse;
        } */
       </style>
        <div id="table_row" style="width:1170px;">
            <table align="center" cellspacing="0" width="970" >
                <tbody>
                <tr>
                    <td rowspan="2" width='200' ><img src='../../<? echo $com_dtls[2]; ?>' height='50' width='70' align="middle" /></td>
                    <td   align="center"><strong style="font-size:16px;">Service Requisition</strong><br><strong style="font-size:14px;"><? echo $company_library[$company];  ?></strong></td>
                </tr>
                <tr>
                    <td align="center" style="font-size:14px;"><strong><? echo $location_arr[$data_result[0]['LOCATION_ID']];  ?></strong></td>
                </tr>
                </tbody>
            </table>
            <br>
            <table align="center" cellspacing="0" width="1170"  border="1" rules="all" >
                <tbody>
                    <tr>
                        <td class="tabs" width="150" style="font-size:12px;"><b>Service Req. No</b></td>
                        <td class="tabs" width="200" style="font-size:12px;"><strong><? echo $data_result[0]['REQU_NO']; ?></strong></td>
                        <td class="tabs" width="150" style="font-size:12px;"><b>Service Req. Date</b></td>
                        <td class="tabs" width="200" style="font-size:12px;"><? echo change_date_format($data_result[0]['REQUISITION_DATE']); ?></td>
                        <td class="tabs" width="150" style="font-size:12px;"><b>Currency</b></td>
                        <td class="tabs" style="font-size:12px;" ><? echo $currency[$data_result[0]['CURRENCY_ID']]; ?></td>
                    </tr>
                    <tr>
                        <td class="tabs" style="font-size:12px;" ><b>Department</b></td>
                        <td class="tabs" style="font-size:12px;" ><? echo $department_library[$data_result[0]['DEPARTMENT_ID']]; ?></td>
                        <td class="tabs" style="font-size:12px;" ><b>Section</b></td>
                        <td class="tabs" style="font-size:12px;" ><? echo $section_library[$data_result[0]['SECTION_ID']]; ?></td>
                        <td class="tabs" style="font-size:12px;" ><b>Store Name</b></td>
                        <td class="tabs" style="font-size:12px;" ><? echo $store_library[$data_result[0]['STORE_NAME']]; ?></td>
                    </tr>
                </tbody>
            </table>
            <br>
            <table align="center" cellspacing="0" width="1170"  border="1" rules="all"  >
                <thead bgcolor="#dddddd">
                    <tr>
                        <th class="tabs" style="font-size:12px;" width="40" >SL</th>
                        <th class="tabs" style="font-size:12px;" width="130" >Service For</th>
                        <th class="tabs" style="font-size:12px;" width="200" >Service Category</th>
                        <!-- <th class="tabs" style="font-size:20px;" width="150" >Item Category</th>
                        <th class="tabs" style="font-size:20px;" width="150" >Item Group</th> -->
                        <th class="tabs" style="font-size:12px;" width="250" >Service Details</th>
                        <th class="tabs" style="font-size:12px;" width="350" >Declaration Details</th>
                        <th class="tabs" style="font-size:12px;" width="90" >UOM</th>
                        <th class="tabs" style="font-size:12px;" width="120" >Req. Qty.</th>
                        <th class="tabs" style="font-size:12px;" width="120" >Rate</th>
                        <th class="tabs" style="font-size:12px;" width="150">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                <?
                $i=0;
                $total_amount = 0; $swip=0;$j=0;
                // echo "<pre>"; print_r($dtls_data_arr); die;
                $item_group_arr = return_library_array("SELECT id, item_name from lib_item_group", 'id', 'item_name');
                foreach ($dtls_data_arr as $service_for => $data){
                    $counter  = 0;
                    foreach ($data as $id => $row){
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        $i++;
                        if($counter == 0){
                            ?>
                            <tr bgcolor="<?=$bgcolor?>">
                                <td class="tabs" valign="middle" align="center" style="padding: 2px 3px;"><?=$i?></td>
                                <td class="tabs" style="padding: 2px 3px; font-size:12px;" valign="middle" align="left" rowspan="<?=count($data)?>"><?=$service_for_arr[$row['service_for']]?></td>
                                <?
                                    $service_name= $row['service_details'];
                                    $category_sql="select service_category from lib_service_category where service_name='$service_name'";
                                    $category_result=sql_select($category_sql);
                                    $category_result=$category_result[0]['SERVICE_CATEGORY'];
                                     
                                    if($category_cnt[0] && $j==$swip){
                                ?>
                                        <td class="tabs" rowspan="<? echo $category_cnt[0]; ?>"style="padding: 2px 3px; font-size:12px;" valign="middle" align="left"><?=$category_result?></td>
                                <? 
                                        $swip=$swip+$category_cnt[0];
                                        array_shift($category_cnt);                                         
                                    }
                                ?>
                                <!-- <td class="tabs" style="padding: 2px 3px; font-size:20px;" valign="middle" align="left"><?//=$item_category[$row['item_category_id']]?></td>
                                <td class="tabs" style="padding: 2px 3px; font-size:20px;" valign="middle" align="left"><?//=$item_group_arr[$row['item_group_id']]?></td> -->
                                <td class="tabs" style="padding: 2px 3px; font-size:12px;" valign="middle" align="left"><?=$row['service_details']?></td>
                                <td class="tabs" style="padding: 2px 3px; font-size:12px;" valign="middle" align="left"><?=$row['remarks']?></td>
                                <td  class="tabs"style="padding: 2px 3px; font-size:12px;" valign="middle" align="center"><?=$service_uom_arr[$row['uom']]?></td>
                                <td class="tabs" style="padding: 2px 3px; font-size:12px;" valign="middle" align="right"><?=number_format($row['qty'], 2)?></td>
                                <td class="tabs" style="padding: 2px 3px; font-size:12px;" valign="middle" align="right"><?=number_format($row['rate'], 4)?></td>
                                <td class="tabs" style="padding: 2px 3px; font-size:12px;" valign="middle" align="right"><?=number_format($row['amount'], 4)?></td>
                            </tr>
                            <?
                        }else{
                            ?>
                            <tr>
                                <td class="tabs" style=" padding: 2px 3px; font-size:12px;" valign="middle" align="center"><?=$i?></td>
                                <?
                                    $service_name=$row['service_details'];
                                    $category_sql="select service_category from lib_service_category where service_name='$service_name'";
                                    $category_result=sql_select($category_sql);
                                    $category_result=$category_result[0]['SERVICE_CATEGORY'];                                     
                                    if($category_cnt[0] && $j==$swip){
                                ?>
                                        <td class="tabs" rowspan="<? echo $category_cnt[0]; ?>" style=" padding: 2px 3px; font-size:12px;" valign="middle" align="left"><?=$category_result?></td>
                                <?
                                        $swip=$swip+$category_cnt[0];
                                        array_shift($category_cnt);                                         
                                    }
                                ?>
                                <!-- <td class="tabs" style=" padding: 2px 3px; font-size:20px;" valign="middle" align="left"><?//=$item_category[$row['item_category_id']]?></td>
                                <td class="tabs" style=" padding: 2px 3px; font-size:20px;" valign="middle" align="left"><?//=$item_group_arr[$row['item_group_id']]?></td> -->
                                <td class="tabs" style=" padding: 2px 3px; font-size:12px;" valign="middle" align="left"><?=$row['service_details']?></td>
                                <td class="tabs" style=" padding: 2px 3px; font-size:12px;" valign="middle" align="left"><?=$row['remarks']?></td>
                                <td class="tabs" style=" padding: 2px 3px; font-size:12px;" valign="middle" align="center"><?=$service_uom_arr[$row['uom']]?></td>
                                <td class="tabs" style=" padding: 2px 3px; font-size:12px;" valign="middle" align="right"><?=number_format($row['qty'], 2)?></td>
                                <td class="tabs" style=" padding: 2px 3px; font-size:12px;" valign="middle" align="right"><?=number_format($row['rate'], 4)?></td>
                                <td class="tabs" style=" padding: 2px 3px; font-size:12px;" valign="middle" align="right"><?=number_format($row['amount'], 4)?></td>
                            </tr>
                            <?
                        }
                        $total_amount += $row['amount'];
                        $counter++; $j++;
                    }
                }
                ?>
                </tbody>
                <tfoot >
                    <tr bgcolor="#dddddd">
                        <td class="tabs" align="left" colspan="8" style=" padding: 2px 3px; font-size:12px">
                            <strong>Total:</strong>
                        </td>
                        <td class="tabs" align="right" style=" padding: 2px 3px; font-size:12px;">
                            <strong><?=number_format($total_amount, 4)?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="9" class="tabs" align="left" style=" padding: 2px 3px; font-size:12px;"><strong>Total Amount in Word: <?=$currency[$data_result[0]['CURRENCY_ID']].". ".number_to_words(number_format($total_amount,2));?> Only</strong></td>
                    </tr>
                    <tr>
                        <td colspan="9" class="tabs" align="left" style=" padding: 2px 3px; font-size:12px;"><strong>Remarks: </strong><?=$data_result[0]['REMARKS'];?></td>
                    </tr>
                </tfoot>
            </table>
            <br/>
            <?
            $total_amount1 = $total_amount;
            $total_amount = 0;
            if($tag_requ_id != '')
			{
            $sql="select id, requ_no, item_category_id, requisition_date, location_id, delivery_date, source, manual_req, department_id, section_id, store_name, pay_mode, cbo_currency, remarks,req_by from inv_purchase_requisition_mst where id in ($tag_requ_id)";
            $dataArray=sql_select($sql);

            $company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
            $location_arr=return_library_array( "select id, address from lib_location",'id','address');
            $store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
            $division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
            $department=return_library_array("select id,department_name from lib_department",'id','department_name');
            $section=return_library_array("select id,section_name from lib_section",'id','section_name');
            $country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
            $supplier_array=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
            $origin_lib=return_library_array( "select country_name,id from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name"  );

            $tbl_width=1170;
            $colspan=15;
            $tot_colspan=9;

            $requ_arr = [];
            foreach ($dataArray as $key => $val){
                $requ_arr['requ_no'][$val[csf('requ_no')]] = $val[csf('requ_no')];
                $requ_arr['requ_date'][change_date_format($val[csf('requisition_date')])] = change_date_format($val[csf('requisition_date')]);
                $requ_arr['currency'][$val[csf('cbo_currency')]] = $currency[$val[csf('cbo_currency')]];
                $requ_arr['dept'][$department[$val[csf('department_id')]]] = $department[$val[csf('department_id')]];
                $requ_arr['section'][$section[$val[csf('section_id')]]] = $section[$val[csf('section_id')]];
                $requ_arr['store'][$store_library[$val[csf('store_name')]]] = $store_library[$val[csf('store_name')]];
            }

            ?>


            <div style="max-width:<?=$tbl_width;?>px;">
                <table cellspacing="0" width="<?=$tbl_width;?>"  border="0" >
                    <tr style="border: none;">
                        <td   align="center" style="border: none;" colspan="6"><strong style="font-size:25px;">Purchase Requisition</strong></td>
                    </tr>
                </table>
                <br>
                <table cellspacing="0" width="<?=$tbl_width;?>"  border="1" rules="all" class="rpt_table">
                    <tr>
                        <td width="150" style="font-size:20px;"><strong>Req. No:</strong></td>
                        <td width="200" style="font-size:20px;"><strong><? echo implode(', ', $requ_arr['requ_no']); ?></strong></td>

                        <td style="font-size:20px;" width="150"><strong>Req. Date:</strong></td>
                        <td style="font-size:20px;" width="200"><? echo implode(', ', $requ_arr['requ_date']);?></td>

                        <td width="150" style="font-size:20px;"><strong>Currency:</strong></td>
                        <td style="font-size:20px;"><? echo implode(', ', $requ_arr['currency']); ?></td>

                    </tr>
                    <tr>
                        <td style="font-size:20px;"><strong>Department:</strong></td>
                        <td style="font-size:20px;"><? echo implode(', ', $requ_arr['dept']); ?></td>
                        <td style="font-size:20px;"><strong>Section:</strong></td>
                        <td style="font-size:20px;"><? echo implode(', ', $requ_arr['section']); ?></td>
                        <td style="font-size:20px;"><strong>Store Name:</strong></td>
                        <td style="font-size:20px;"><? echo implode(', ', $requ_arr['store']); ?></td>
                    </tr>
                </table>

                <!-- <br> -->
                <style type="text/css">
                    table thead tr th, table tbody tr td{
                        wordwrap: break-word;
                        break-ward: break-word;
                    }
           
                </style>
                <table cellspacing="0" width="<?=$tbl_width;?>"  border="1" rules="all" class="rpt_table" style="border: 1px;font-size: 18px;" >
                    <thead bgcolor="#dddddd"  align="center">
                    <tr>
                        <th colspan="<?=$colspan;?>" style="font-size:20px;" align="center" ><strong>Item Details</strong></th>
                    </tr>
                        <tr>
                            <th width="30" style="font-size:20px;">SL</th>
                            <th width="200" style="font-size:20px;">Item Description</th>
                            <th width="60" style="font-size:20px;">Model / Article</th>
                            <th width="50" style="font-size:20px;">Size/MSR</th>
                            <th width="50" style="font-size:20px;">Brand</th>
                            <th width="50" style="font-size:20px;">UOM</th>
                            <th width="70" style="font-size:20px;"> Stock</th>
                            <th width="70" style="font-size:20px;">Req. Qty.</th>
                            <th width="80" style="font-size:20px;">Rate</th>
                            <th width="70" style="font-size:20px;">Total Amount</th>
                            <th width="70" style="font-size:20px;">Last Rcv. Date</th>
                            <th width="70" style="font-size:20px;">Last Rcv. Qty.</th>
                            <th width="60" style="font-size:20px;">Last. Rate</th>
                            <th width="70" style="font-size:20px;">Last Month issue</th>
                            <th style="font-size:20px;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
                    $receive_array=array();
                    $i=1;
                    $sql= " SELECT a.id,b.id as dtls_id,b.item_category,b.brand_name,b.origin, b.model, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock,b.remarks,b.brand_name, c.item_account, c.item_category_id, c.item_description,c.sub_group_name,c.item_code, c.item_size,c.brand_name as prod_brand_name, c.model as prod_model, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label, c.item_number, a.company_id
		            from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c
		            where a.id=b.mst_id and b.product_id=c.id and a.id in ($tag_requ_id) and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0 and c.status_active in(1,3) order by b.id";
                    $sql_result=sql_select($sql);
                    foreach($sql_result as $row)
                    {

                        $all_prod_ids.=$row[csf('product_id')].",";
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['id'] = $row[csf('id')];
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['company_id'] = $row[csf('company_id')];
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['item_category'] = $row[csf('item_category')];
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['brand_name'] = $row[csf('brand_name')];
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['origin'] = $row[csf('origin')];
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['brand_name'] = $row[csf('brand_name')];
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['model'] = $row[csf('model')];
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['requisition_date'] = $row[csf('requisition_date')];
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['product_id'] = $row[csf('product_id')];
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['required_for'] = $row[csf('required_for')];
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['cons_uom'] = $row[csf('cons_uom')];
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['quantity'] = $row[csf('quantity')];
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['rate'] = $row[csf('rate')];
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['amount'] = $row[csf('amount')];
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['stock'] = $row[csf('stock')];
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['remarks'] = $row[csf('remarks')];
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['item_account'] = $row[csf('item_account')];
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['item_category_id'] = $row[csf('item_category_id')];
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['item_description'] = $row[csf('item_description')];
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['sub_group_name'] = $row[csf('sub_group_name')];
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['item_code'] = $row[csf('item_code')];
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['item_number'] = $row[csf('item_number')];
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['item_size'] = $row[csf('item_size')];
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['prod_brand_name'] = $row[csf('prod_brand_name')];
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['prod_model'] = $row[csf('prod_model')];
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['item_group_id'] = $row[csf('item_group_id')];
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['unit_of_measure'] = $row[csf('unit_of_measure')];
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['current_stock'] = $row[csf('current_stock')];
                        $all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['re_order_label'] = $row[csf('re_order_label')];
                    }
                    $all_prod_ids=implode(",",array_unique(explode(",",chop($all_prod_ids,","))));
                    if($all_prod_ids=="") $all_prod_ids=0;
                    $prod_sql="select company_id, item_category_id, item_group_id, sub_group_name, item_description, item_size, model, item_number, item_code
		            from product_details_master where status_active in(1,3) and id in ($all_prod_ids)";
                    $prod_sql_result=sql_select($prod_sql);
                    foreach($prod_sql_result as $row)
                    {
                        $prod_company=$row[csf("company_id")];
                        $prod_category[$row[csf("item_category_id")]]=$row[csf("item_category_id")];
                        $prod_group[$row[csf("item_group_id")]]=$row[csf("item_group_id")];
                        $pord_description.="'".$row[csf("item_description")]."',";
                    }
                    $pord_description=chop($pord_description,",");
                    $rcv_cond="";
                    if($prod_company) $rcv_cond.=" and c.company_id=$prod_company";
                    if(count($prod_category)>0) $rcv_cond.=" and c.item_category_id in(".implode(",",$prod_category).")";
                    if(count($prod_group)>0) $rcv_cond.=" and c.item_group_id in(".implode(",",$prod_group).")";
                    if($pord_description) $rcv_cond.=" and c.item_description in($pord_description)";

                    $rec_sql="select b.id, b.item_category, b.prod_id, b.transaction_date as transaction_date, b.supplier_id, b.cons_quantity as rec_qty, cons_rate as cons_rate, c.company_id, c.item_category_id, c.item_group_id, c.sub_group_name, c.item_description, c.item_size, c.model, c.item_number, c.item_code
		            from inv_receive_master a, inv_transaction b, product_details_master c
		            where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,3) and c.is_deleted=0 $rcv_cond
		            order by  b.id ";
                    //echo  $rec_sql;
                    $rec_sql_result= sql_select($rec_sql);
                    foreach($rec_sql_result as $row)
                    {
                        $item_key=$row[csf('company_id')]."*".$row[csf('item_category_id')]."*".$row[csf('item_group_id')]."*".$row[csf('sub_group_name')]."*".$row[csf('item_description')]."*".$row[csf('item_size')]."*".$row[csf('model')]."*".$row[csf('item_number')]."*".$row[csf('item_code')];
                        $receive_array[$item_key]['transaction_date']=$row[csf('transaction_date')];
                        $receive_array[$item_key]['rec_qty']=$row[csf('rec_qty')];
                        $receive_array[$item_key]['rate']=$row[csf('cons_rate')];
                        $receive_array[$item_key]['supplier_id']=$row[csf('supplier_id')];
                    }

                    if($db_type==2)
                    {
                        $cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'";
                    }
                    elseif($db_type==0) $cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";

                    $last_month_issue_sql=sql_select("select c.company_id, c.item_category_id, c.item_group_id, c.sub_group_name, c.item_description, c.item_size, c.model, c.item_number, c.item_code, sum(b.cons_quantity) as isssue_qty
		            from  inv_transaction b, product_details_master c
		            where b.prod_id=c.id and b.transaction_type=2 and b.is_deleted=0 and b.status_active=1 and c.status_active in(1,3) and c.is_deleted=0 and transaction_date >= add_months(trunc(sysdate,'mm'),-1)  and transaction_date < add_months(trunc(sysdate,'mm'),0) $rcv_cond
		            group by c.company_id, c.item_category_id, c.item_group_id, c.sub_group_name, c.item_description, c.item_size, c.model, c.item_number, c.item_code");

                    $last_month_issue_data=array();
                    foreach($last_month_issue_sql as $row)
                    {
                        $item_key=$row[csf('company_id')]."*".$row[csf('item_category_id')]."*".$row[csf('item_group_id')]."*".$row[csf('sub_group_name')]."*".$row[csf('item_description')]."*".$row[csf('item_size')]."*".$row[csf('model')]."*".$row[csf('item_number')]."*".$row[csf('item_code')];
                        $last_month_issue_data[$item_key]["prod_id"]=$row[csf("prod_id")];
                        $last_month_issue_data[$item_key]["isssue_qty"]=$row[csf("isssue_qty")];
                    }



                    $receive_last_month_sql=sql_select("select b.prod_id, c.company_id, c.item_category_id, c.item_group_id, c.sub_group_name, c.item_description, c.item_size, c.model, c.item_number, c.item_code, sum(cons_quantity) as receive_qty
		            from  inv_transaction b, product_details_master c
		            where b.prod_id=c.id and b.transaction_type=1 and b.is_deleted=0 and b.status_active=1 and c.status_active in(1,3) and c.is_deleted=0 and transaction_date >= add_months(trunc(sysdate,'mm'),-1) $rcv_cond
		            group by b.prod_id, c.company_id, c.item_category_id, c.item_group_id, c.sub_group_name, c.item_description, c.item_size, c.model, c.item_number, c.item_code");

                    $last_month_receive_data=array();
                    foreach($receive_last_month_sql as $row)
                    {
                        $item_key=$row[csf('company_id')]."*".$row[csf('item_category_id')]."*".$row[csf('item_group_id')]."*".$row[csf('sub_group_name')]."*".$row[csf('item_description')]."*".$row[csf('item_size')]."*".$row[csf('model')]."*".$row[csf('item_number')]."*".$row[csf('item_code')];
                        $last_month_receive_data[$item_key]["prod_id"]=$row[csf("prod_id")];
                        $last_month_receive_data[$item_key]["receive_qty"]=$row[csf("receive_qty")];
                    }


                    // echo "<pre>";
                    // print_r($all_data_array);
                    $previos_item_category='';
                    $total_amount=0;$last_qnty=0;$total_reqsit_value=0;
                    $total_monthly_rej=0;$total_monthly_iss=0;$total_stock=0;
                    $last_issue=0;
                    $last_receive=0;
                    $i=1;
                    foreach ($all_data_array as $category_key => $category_val)
                    {
                        foreach($category_val as $dtls_id => $row)
                        {
                            $item_cat=$row['item_category'];
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            if($previos_item_category!=$item_cat)
                            {
                                if($i>1)
                                {
                                    ?>
                                    <tr bgcolor="#dddddd">
                                        <td  colspan="<?=$tot_colspan;?>" style=" padding: 2px 3px;"><strong>Sub Total : </strong></td>
                                        <td align="right" style=" padding: 2px 3px;"><strong><? echo number_format($total_amount,2); ?></strong></td>

                                        <td colspan="5">&nbsp;</td>
                                    </tr>
                                    <?
                                    $total_amount=0;$last_qnty=0;$total_reqsit_value=0;
                                    $total_monthly_rej=0;$total_monthly_iss=0;$total_stock=0;
                                    $previos_item_category=$item_cat;
                                    $last_issue=0;
                                    $last_receive=0;
                                }

                                ?>
                                <tr bgcolor="#dddddd">
                                    <td colspan="<?=$colspan;?>" align="left" style=" padding: 2px 3px;">Category : <? echo $item_category[$row["item_category"]]; ?></td>
                                </tr>
                                <?
                            }

                            $item_key=$row['company_id']."*".$row['item_category']."*".$row['item_group_id']."*".$row['sub_group_name']."*".$row['item_description']."*".$row['item_size']."*".$row['model']."*".$row['item_number']."*".$row['item_code'];

                            $quantity=$row['quantity'];
                            $quantity_sum += $quantity;
                            $amount=$row['amount'];
                            //test
                            $sub_group_name=$row['sub_group_name'];
                            $amount_sum += $amount;

                            $current_stock=$row['stock'];
                            $current_stock_sum += $current_stock;
                            if($db_type==2)
                            {
                                $last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  a.requisition_date<'".change_date_format($row['requisition_date'],'','',1)."' order by requisition_date desc", "data" );
                            }
                            if($db_type==0)
                            {
                                $last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row['product_id']."' and  requisition_date<'".$row['requisition_date']."' order by requisition_date desc", "data" );
                            }
                            $last_req_info=explode('_',$last_req_info);
                            //print_r($dataaa);

                            $item_account=explode('-',$row['item_account']);
                            $item_code=$item_account[3];
                            /*$last_rec_date=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['transaction_date'];
                            $last_rec_qty=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['rec_qty'];*/
                            $last_rec_date=$receive_array[$item_key]['transaction_date'];
                            $last_rec_qty=$receive_array[$item_key]['rec_qty'];
                            $last_rec_rate=$receive_array[$item_key]['rate'];
                            $last_rec_supp=$receive_array[$item_key]['supplier_id']; 

                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" style="font-size:14px; ">
                                <td valign="middle" style=" padding: 2px 3px;font-size:20px;" align="center"><? echo $i; ?></td>
                                <td valign="middle" style=" padding: 2px 3px;font-size:20px;"><p> <? echo $row["item_description"];?> </p></td>
                                <?
                                $model_artical="";
                                if($row["prod_model"]!="" || $row["model"]!="" )
                                {
                                    if($row["prod_model"]!="")
                                    {
                                        if($row["prod_model"]!="" && $row["item_number"]!="" )
                                        {
                                            $model_artical=$row["prod_model"].' / '.$row["item_number"];
                                        }
                                        else
                                        {
                                            $model_artical=$row["prod_model"];
                                        }
                                    }
                                    else
                                    {
                                        if($row["model"]!="" || $row["item_number"]!="" )
                                        {
                                            if($row["model"]!="" && $row["item_number"]!="" )
                                            {
                                                $model_artical=$row["model"].' / '.$row["item_number"];
                                            }
                                            else
                                            {
                                                $model_artical=$row["model"];
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    $model_artical=$row["item_number"];
                                }
                                ?>
                                <td valign="middle" style=" padding: 2px 3px;font-size:20px;"><p><? echo $model_artical;?> </p></td>
                                <td valign="middle" style=" padding: 2px 3px;font-size:20px;"><p><? echo $row["item_size"]; ?></p></td>
                                <td valign="middle" style=" padding: 2px 3px;font-size:20px;"><p><? echo ($row["prod_brand_name"]!="")? $row["prod_brand_name"]:$row["brand_name"]; ?></p></td>

                                <td valign="middle" style=" padding: 2px 3px;font-size:20px;" align="center"><p><? echo $unit_of_measurement[$row["unit_of_measure"]]; ?></p></td>
                                <td valign="middle" style=" padding: 2px 3px;font-size:20px;" align="right"><p><? echo number_format($row['stock'],2); ?></p></td>
                                <td valign="middle" style=" padding: 2px 3px;font-size:20px;" align="right"><p><? echo $row['quantity']; ?>&nbsp;</p></td>
                                <td valign="middle" style=" padding: 2px 3px;font-size:20px;" align="right"><? echo $row['rate']; ?></td>
                                <td valign="middle" style=" padding: 2px 3px;font-size:20px;" align="right"><? echo number_format($row['amount'], 2); ?></td>
                                <td valign="middle" style=" padding: 2px 3px;font-size:20px;" align="center"><p><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?>&nbsp;</p></td>
                                <td valign="middle" style=" padding: 2px 3px;font-size:20px;" align="right"><p><? echo number_format($last_rec_qty,0,'',','); ?>&nbsp;</p></td>
                                <td valign="middle" style=" padding: 2px 3px;font-size:20px;" align="right"><p><? echo number_format($last_rec_rate,2);//$last_req_info[2]; ?>&nbsp;</p></td>

                                <td valign="middle" style=" padding: 2px 3px;font-size:20px;" align="right">
                                    <?php echo number_format($last_month_issue_data[$item_key]["isssue_qty"],2); ?>
                                </td>
                                <td valign="middle" style=" padding: 2px 3px;font-size:20px;" align="left"><? echo $row['remarks']; ?></td>
                            </tr>
                            <?
                            $total_amount += $row['amount'];
                            $Grand_tot_total_amount += $row['amount'];
                            $previos_item_category=$item_cat;
                            $i++;

                        }

                    }
                    ?>
                    <tr bgcolor="#dddddd">
                        <td  colspan="<?=$tot_colspan;?>" style=" padding: 2px 3px;font-size:20px;"><strong>Sub Total: </strong></td>
                        <td align="right" style=" padding: 2px 3px;font-size:20px;"><strong><? echo number_format($total_amount,2); ?></strong></td>
                        <td align="right" colspan="5">&nbsp;</td>

                    </tr>
                    <tr bgcolor="#B0C4DE">
                        <td  colspan="<?=$tot_colspan;?>" style=" padding: 2px 3px;font-size:20px;"><strong>Grand Total: </strong></td>
                        <td align="right" style=" padding: 2px 3px;font-size:20px;"><strong><? echo number_format($Grand_tot_total_amount+$total_amount1,2); ?></strong></td>

                        <td colspan="5">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="<?=$colspan;?>" style=" padding: 2px 3px;font-size:20px;"><strong>Total Amount (In Word): <? echo $currency[$dataArray[0][csf('cbo_currency')]].". ".number_to_words(number_format($Grand_tot_total_amount+$total_amount1,0,'',','))." Only"; ?></strong></td>
                    </tr>
                    <tr>
                        <td colspan="<?=$colspan;?>" align="left" style=" padding: 2px 3px;font-size:20px;"><strong>Remarks: </strong><? echo $dataArray[0][csf('remarks')]; ?></td>
                    </tr>
                    </tbody>
                </table>
                <br>
            <?
            }
            ?>

            <? 
                function signature_table_modified($report_id, $company, $width, $template_id="", $padding_top = 70,$prepared_by='',$userSignatureArr=array(),$break_tr=7, $custom_style='') {
	
                    if ($template_id != '') {
                        $template_id = " and template_id=$template_id ";
                    }
                     
                    $sql = sql_select("select USER_ID,designation,name,activities,prepared_by from variable_settings_signature where report_id=$report_id and company_id=$company   and status_active=1 $template_id order by sequence_no ");
                    $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
                    
                    
                
                    if($sql[0][csf("prepared_by")]==1){
                        list($prepared_by,$activities)=explode('**',$prepared_by);
                        $CUSTOM_DESIGNATION = return_field_value("b.CUSTOM_DESIGNATION", "user_passwd a,LIB_DESIGNATION b", "b.id=a.DESIGNATION and a.id=$prepared_by", "CUSTOM_DESIGNATION");
                        $sql_2[100] = array ( 'USER_ID'=>$prepared_by,'DESIGNATION' => 'Prepared By' ,'NAME' => ((($user_lib_name[$prepared_by])?$user_lib_name[$prepared_by]:$prepared_by)."<br>".$CUSTOM_DESIGNATION), 'ACTIVITIES' =>$activities, 'PREPARED_BY' => 0 );
                        $sql=$sql_2+$sql;
                    }
                    
                    $count = count($sql);
                    $td_width = floor($width / $count);
                    $standard_width = $count * (150);
                    $table_margin_left= (1170-$standard_width)/2;
                    if ($standard_width > $width) {
                        $td_width = 150;
                    }
                    $no_coloumn_per_tr = floor($width / $td_width);
                    $i = 1;
                    if ($count == 0) {$message = "<p style='font-size:13px;'><b>Note: This is Software Generated Copy , Signature is not Required.</b></p>";}
                    echo '<table cellspacing="5" id="signatureTblId" width="' . $width . '" style= padding-top:' . $padding_top . 'px; '.$custom_style.'"><tr><td width="100%"  height="' . $padding_top . '" colspan="' . $count . '">' . $message . '</td></tr>';
                    $flag=0;
                    foreach ($sql as $row) {
                        $flag++;
                        $sigHtml='';
                        if($userSignatureArr[$row['USER_ID']]){$sigHtml='<img src="'.$userSignatureArr[$row['USER_ID']].'" height="40">';}
                        else{$sigHtml='<div height="40"></div>';}
                        if($flag==1){echo "<tr >";}
                        
                        echo '<td  width="' . $td_width . '" align="center" valign="top" >
                        <p style="min-height:40px;"  >'.$sigHtml.'</p>
                        <strong>' . $row[csf("activities")] . '</strong><br>
                        <strong style="text-decoration:overline; font-size:12px;">' . $row[csf("designation")] . "</strong><br>" ."<p style='font-size:12px;'>". $row[csf("name")]."</p>" . '</td>';
                        // if ($i % $no_coloumn_per_tr == 0) {
                        // 	echo '</tr><tr><td style="border: 1px solid #f00;" width="100%" height="70" colspan="' . $no_coloumn_per_tr . '"></td></tr>';
                        // }
                        if($flag==$break_tr || $count==$i){echo "</tr>";$flag=0;}
                        $i++;
                    }
                    echo '</table>';
                }
                 
                    echo signature_table_modified(267, $company,$tbl_width,$template_id,70,$user_lib_name[$inserted_by], "", 12); 
                 
            ?>
            <script type="text/javascript" src="../../js/jquery.js"></script>

            <style>
                 #signatureTblId td{
                    font-size:20px!important;
                }
            </style>
            <?
            exit();
}

if ($action=="service_requisition_po_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
    $company=$data[0];
    $mst_id=$data[1];
    $rpt_title=$data[2];
    $template_id=$data[3];
    echo load_html_head_contents($rpt_title,"../../", 1, 1, $unicode,'','')
    ?>
    <link rel="stylesheet" href="../../../css/style_common.css" type="text/css" />
    <?

	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$location_library=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$designation_library=return_library_array("select id,custom_designation from lib_designation",'id','custom_designation');
    $department_library=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section_library=return_library_array("select id,section_name from lib_section",'id','section_name');

    $sql_user_info=sql_select("select id, user_name, user_full_name, designation, department_id from user_passwd where valid=1");
    foreach ($sql_user_info as $row){
        $user_arr[$row[csf('id')]]['user_name']=$row[csf('user_name')];
        $user_arr[$row[csf('id')]]['user_full_name']=$row[csf('user_full_name')];
        $user_arr[$row[csf('id')]]['designation_id']=$row[csf('designation')];
        $user_arr[$row[csf('id')]]['department_id']=$row[csf('department_id')];
    }

    $sql_company=sql_select("SELECT id, company_name, company_short_name, plot_no, level_no, road_no, block_no, city, zip_code from lib_company where status_active=1 and is_deleted=0 and id=$company");
    $com_name=$sql_company[0][csf("company_name")];
    $company_short_name=$sql_company[0][csf("company_short_name")];
    $plot_no=$sql_company[0][csf("plot_no")];
    $level_no=$sql_company[0][csf("level_no")];
    $road_no=$sql_company[0][csf("road_no")];
    $block_no=$sql_company[0][csf("block_no")];
    $city=$sql_company[0][csf("city")];
    $zip_code=$sql_company[0][csf("zip_code")];

    $com_address='';
    if($plot_no !=''){ $com_address.=$plot_no;}
    if($level_no !=''){ $com_address.=", ".$level_no;}
    if($road_no !=''){ $com_address.=", ".$road_no;}
    if($block_no !=''){ $com_address.=", ".$block_no;}
    if($city !=''){ $com_address.=", ".$city;}
    if($zip_code !=''){ $com_address.=", ".$zip_code;}

    $data_sql="SELECT a.requ_no as REQU_NO, a.requisition_date as REQUISITION_DATE, a.delivery_date as DELIVERY_DATE, a.location_id as LOCATION_ID, a.pay_mode as PAY_MODE, a.store_name as STORE_NAME, a.department_id as DEPARTMENT_ID, a.section_id as SECTION_ID, a.remarks as REMARKS, a.req_by as REQ_BY, a.inserted_by as INSERTED_BY, a.cbo_currency as CURRENCY_ID, to_char(a.insert_date, 'DD-MM-YYYY HH:MI:SS AM') as INSERT_DATE, b.user_full_name as USER_FULL_NAME, c.custom_designation as CUSTOM_DESIGNATION, a.is_approved from inv_purchase_requisition_mst a left join user_passwd b on b.id = a.inserted_by left join lib_designation c on c.id = b.designation  where a.id=$mst_id";
    // echo $data_sql;
    $data_result=sql_select($data_sql);
    $inserted_by=$data_result[0]['INSERTED_BY'];
    $currency_id=$data_result[0]['CURRENCY_ID'];

    $sql_dtls= "SELECT a.product_id as PRODUCT_ID, a.quantity as QUANTITY, a.rate as RATE, a.amount as AMOUNT, a.remarks as REMARKS, a.service_for as SERVICE_FOR, a.service_details as SERVICE_DETAILS, a.service_uom as SERVICE_UOM, b.item_description as ITEM_DESCRIPTION
    from inv_purchase_requisition_dtls a
    left join product_details_master b on a.product_id=b.id and b.status_active in(1,3)
    where a.mst_id=$mst_id and a.status_active=1 order by a.id";
    // echo $sql_dtls;
    $sql_result= sql_select($sql_dtls);
    $item_description=0;
    foreach($sql_result as $row)
    {
        if($row['ITEM_DESCRIPTION']!=''){ $item_description=1;}
    }
    $com_dtls = fnc_company_location_address($company, $data_result[0]['LOCATION_ID'], 2);


    $sql_approved="select b.approved_by as APPROVED_BY, to_char(b.approved_date, 'dd-mm-yyyy hh:mi:ss am') as APPROVED_DATE from inv_purchase_requisition_mst a, approval_history b where a.id=b.mst_id and a.id=$mst_id and a.entry_form=526 and a.is_approved in(1,3)and b.entry_form=61 and b.current_approval_status=1 and a.status_active=1 and a.is_deleted=0";
    $sql_approved_res=sql_select($sql_approved);
    ?>

	<div id="table_row" style="width:930px;">
        <table align="center" cellspacing="0" width="900" >
            <tbody>
                <tr>
                    <td rowspan="2" width='100' ><img src='../../<? echo $com_dtls[2]; ?>' height='70' width='100' align="middle" /></td>
                    <td  style="font-size:25px;" align="center"><strong><? echo $company_library[$company];  ?></strong></td>
                    <td rowspan="2" width='140' ><div style="font-size:16px; border: solid 2px black;padding:10px 5px;"><b>Service Requisition</b></div></td>
                </tr>
                <tr>
                    <td align="center" style="font-size:21px;"><strong><? echo $location_library[$data_result[0]['LOCATION_ID']]; //$com_address;  ?></strong></td>
                </tr>
            </tbody>
        </table>
        <br>
        <table align="center" cellspacing="0" width="900" >
            <tbody>
                <tr>
                    <td width="150" style="font-size:16px;"><b>Rqsn No:</b></td>
                    <td width="150" style="font-size:16px;"><? echo $data_result[0]['REQU_NO']; ?></td>
                    <td width="150" style="font-size:16px;"><b>Rqsn Date:</b></td>
                    <td width="150" style="font-size:16px;"><? echo change_date_format($data_result[0]['REQUISITION_DATE']); ?></td>
                    <td width="150" style="font-size:16px;"><b>Del Date:</b></td>
                    <td style="font-size:16px;" ><? echo change_date_format($data_result[0]['DELIVERY_DATE']); ?></td>
                </tr>
                <tr>
                    <td style="font-size:16px;" ><b>Business Unit:</b></td>
                    <td style="font-size:16px;" ><? echo $location_library[$data_result[0]['LOCATION_ID']]; ?></td>
                    <td style="font-size:16px;" ><b>Pay Mood:</b></td>
                    <td style="font-size:16px;" ><? echo $pay_mode[$data_result[0]['PAY_MODE']]; ?></td>
                    <td style="font-size:16px;" ><b>Store Name:</b></td>
                    <td style="font-size:16px;" ><? echo $store_library[$data_result[0]['STORE_NAME']]; ?></td>
                </tr>
                <tr>
                    <td style="font-size:16px;" ><b>Department:</b></td>
                    <td style="font-size:16px;" ><? echo $department_library[$data_result[0]['DEPARTMENT_ID']]; ?></td>
                    <td style="font-size:16px;" ><b>Section:</b></td>
                    <td style="font-size:16px;" ><? echo $section_library[$data_result[0]['SECTION_ID']]; ?></td>
                    <td style="font-size:16px;" ><b>Req by:</b></td>
                    <td style="font-size:16px;" ><? echo $data_result[0]['REQ_BY']; ?></td>
                </tr>
                <tr>
                    <td style="font-size:16px;" ><b>Remarks:</b></td>
                    <td style="font-size:16px;" colspan="4"><? echo $data_result[0]['REMARKS']; ?></td>
                    <td>
                        <?php
                            if($data_result[0][csf("is_approved")] == 1)
                            {
                                ?>
                                    <div id="approved" style="float:left; font-size:24px; color:#FF0000;">Full Approved</div>
                                <?php
                            }
                            else if($data_result[0][csf("is_approved")]!=0)
                            {
                                ?>
                                    <div id="approved" style="float:left; font-size:24px; color:#FF0000;">Partial Approved</div>
                                <?php
                            }
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <br>
        <table align="center" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
            <thead>
                <th style="font-size:16px;" width="50" >SL</th>
                <th style="font-size:16px;" width="100" >Service Type</th>
                <th style="font-size:16px;" width="180" >Service Details</th>
                <?
                    if($item_description==1)
                    {
                        ?>
                            <th style="font-size:16px;" width="100" >Item Description List</th>
                        <?
                    }
                ?>
                <th style="font-size:16px;" width="100" >Service UOM</th>
                <th style="font-size:16px;" width="80" >Qty.</th>
                <th style="font-size:16px;" width="80" >Rate</th>
                <th style="font-size:16px;" width="80" >Amount</th>
                <th style="font-size:16px;" >Remarks</th>
            </thead>
            <tbody>
            <?
                $i=1;
                foreach($sql_result as $row)
                {
                    if ($i%2==0){ $bgcolor="#E9F3FF";}else{ $bgcolor="#FFFFFF";}
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td style="font-size:16px;"><? echo $i; ?></td>
                        <td style="font-size:16px;" align="center"><? echo $service_for_arr[$row['SERVICE_FOR']]; ?></td>
                        <td style="font-size:16px;" ><? echo $row['SERVICE_DETAILS']; ?></td>
                        <?
                            if($item_description==1)
                            {
                                ?>
                                    <td style="font-size:16px;"><?echo $row['ITEM_DESCRIPTION'];?></td>
                                <?
                            }
                        ?>
                        <td style="font-size:16px;" align="center"><? echo $service_uom_arr[$row['SERVICE_UOM']]; ?></td>
                        <td style="font-size:16px;" align="right"><? echo number_format($row['QUANTITY'],2,".",""); ?></td>
                        <td style="font-size:16px;" align="right"><? echo number_format($row['RATE'],2,".",""); ?></td>
                        <td style="font-size:16px;" align="right"><? echo number_format($row['AMOUNT'],2,".",""); ?></td>
                        <td style="font-size:16px;"><? echo $row['REMARKS']; ?></td>
                    </tr>
                    <?php
                    $i++;
                    $total_qnty+=$row['QUANTITY'];
                    $total_amount+=$row['AMOUNT'];
                }
            ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <?if($item_description==1){?><th>&nbsp;</th><?}?>
                    <th style="font-size:16px;">Grand Total:&nbsp;</th>
                    <th style="font-size:16px;">&nbsp;</th>
                    <th >&nbsp;</th>
                    <th style="font-size:16px;"><?=number_format($total_amount,2);?></th>
                    <th >&nbsp;</th>
                </tr>
                <tr>
                    <td colspan="<?=($item_description==1)?9:8;?>" align="left" style="font-size:16px;"><strong>Total Amount in Word: </strong><?=number_to_words(number_format($total_amount,2))." ".$currency[$currency_id];?> only</td>
                </tr>
            </tfoot>
        </table>
    <br/>
   
    <? 
			 
             $appSql = "select a.APPROVED_BY, a.APPROVED_DATE,b.USER_FULL_NAME,c.CUSTOM_DESIGNATION from approval_mst a,USER_PASSWD b,LIB_DESIGNATION c where a.mst_id =$mst_id and a.APPROVED_BY=b.id and b.DESIGNATION=c.id and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0
             union all
             select b.id as APPROVED_BY, a.INSERT_DATE as APPROVED_DATE,b.USER_FULL_NAME,c.CUSTOM_DESIGNATION from inv_purchase_requisition_mst a,USER_PASSWD b,LIB_DESIGNATION c where a.id =$mst_id and a.INSERTED_BY=b.id and b.DESIGNATION=c.id and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0";
             // echo $appSql;die;
             $appSqlRes=sql_select($appSql);
             $userDtlsArr=array();
             foreach($appSqlRes as $row){
                 $userDtlsArr[$row['APPROVED_BY']]="<div><b>".$row['USER_FULL_NAME']."</b></div><div><b>".$row['CUSTOM_DESIGNATION']."</b></div><div><small>".$row['APPROVED_DATE']."</small></div>";
             }

             
             echo get_app_signature(267, $data[0],"900px",$template_id, 50,$inserted_by,$userDtlsArr); 
 
             ?>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <?
    exit();
}

if($action=="file_upload")
{
	header("Content-Type: application/json");
	$filename = time().$_FILES['file']['name']; 
	$location = "../../../file_upload/".$filename; 
    //echo "0**".$filename; die;
	$uploadOk = 1;
	if(empty($mst_id))
	{
		$mst_id=$_GET['mst_id'];
	} 
    
	if(move_uploaded_file($_FILES['file']['tmp_name'], $location))
	{ 
		$uploadOk = 1;
	}
	else
	{ 
		$uploadOk=0; 
	} 
    // echo "0**".$uploadOk; die;
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}

	$id=return_next_id( "id","COMMON_PHOTO_LIBRARY", 1 ) ;
	$data_array .="(".$id.",".$mst_id.",'service_requisition','file_upload/".$filename."','2','".$filename."','".$pc_date_time."')";
	$field_array="id,master_tble_id,form_name,image_location,file_type,real_file_name,insert_date";
	$rID=sql_insert("COMMON_PHOTO_LIBRARY",$field_array,$data_array,1);

	if($db_type==0)
	{
		if($rID==1 && $uploadOk==1)
		{
			mysql_query("COMMIT");
			echo "0**".$new_system_id[0]."**".$mst_id;
		}
		else
		{
			mysql_query("ROLLBACK");
			echo "10**".$mst_id;
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID==1 && $uploadOk==1)
		{
			oci_commit($con);
			echo "0**".$new_system_id[0]."**".$mst_id;
		}
		else
		{
			oci_rollback($con);
			echo "10**".$rID."**".$uploadOk."**INSERT INTO COMMON_PHOTO_LIBRARY(".$field_array.") VALUES ".$data_array;
		}
	}
	disconnect($con);
	die;
}
?>
