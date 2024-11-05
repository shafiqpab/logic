<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$permission=$_SESSION['page_permission'];
include('../../../includes/common.php');
$payment_yes_no=array(0=>"yes", 1=>"No");

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$user_store_ids = $_SESSION['logic_erp']['store_location_id'];
$user_supplier_ids = trim($_SESSION['logic_erp']['supplier_id']);

$userCredential = sql_select("SELECT store_location_id  FROM user_passwd where id=$user_id");
$storeCredentialId = $userCredential[0][csf('store_location_id')];
if ($storeCredentialId !='') {
    $store_location_credential_cond = " and a.id in($storeCredentialId)"; 
}

if ($action == "load_drop_down_supplier")
{
	if($user_supplier_ids!="")
	{
		$user_supplier_cond = "and c.id in ($user_supplier_ids)";
	}else {
		$user_supplier_cond = "";
	}
	echo create_drop_down("cbo_supplier", 142, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' $user_supplier_cond and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	exit();
}

if ($action == "load_drop_down_supplier_from_issue")
{
	echo create_drop_down("cbo_supplier", 142, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c, inv_issue_master d where c.id=b.supplier_id and a.supplier_id = b.supplier_id and c.id=d.knit_dye_company and d.knit_dye_source=3 and d.issue_purpose in(15,50,51) and d.entry_form=3 and a.tag_company='$data' and b.party_type in(2,93,94) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	exit();
}

if ($action == "load_drop_down_store") 
{
	$data = explode("_", $data);
	$category_id = 1;
	$sql_store = "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and
	a.company_id='$data[0]' and b.category_type=$category_id and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name";
	echo create_drop_down("cbo_store_name", 142, $sql_store, "id,store_name", 1, "--Select store--", 0, "");
	exit();
}

if ($action == "load_drop_down_party")
{
	echo create_drop_down("cbo_party", 142, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type=91 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	exit();
}




if ($action=="wo_pi_popup")
{
	echo load_html_head_contents("WO/PI Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 

	<script>
		var update_id='<? echo $update_id; ?>';
		
		function js_set_value(id,no,data,receive_basis)
		{
			if(update_id!="")
			{
				var response = trim(return_global_ajax_value(update_id, 'duplication_check', '', 'yarn_receive_v2_controller'));
				if(response!="")
				{
					var curr_data=data.split("**");
					var curr_supplier_id=curr_data[0];
					var curr_currency_id=curr_data[1];
					var curr_source=curr_data[2];
					var curr_lc_id=curr_data[4];
					
					var prev_data=response.split("**");
					var prev_supplier_id=prev_data[0];
					var prev_currency_id=prev_data[1];
					var prev_source=prev_data[2];
					var prev_lc_id=prev_data[3];
					
					if(!(curr_supplier_id==prev_supplier_id && curr_currency_id==prev_currency_id && curr_source==prev_source))
					{
						alert("Supplier, Currency and Source Mix not allow in Same Received ID \n");
						//alert("Supplier, Currency and Source Mix not allow in Same Received ID \n"+curr_supplier_id+"=="+prev_supplier_id+"=="+curr_currency_id+"=="+prev_currency_id+"=="+curr_source+"=="+prev_source);
						return;
					}
				}
			}
			//alert("Fuad");return;
			$('#hidden_wo_pi_id').val(id);
			$('#hidden_wo_pi_no').val(no);
			$('#hidden_data').val(data);
			$('#receive_basis').val(receive_basis);
			parent.emailwindow.hide();
		}
	
    </script>

	</head>

	<body onLoad="set_hotkey()">
	<div align="center" style="width:900px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:800px; margin-left:5px">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="800" class="rpt_table">
                <thead>
                    <th width="180">Company</th>
                    <th width="180">Supplier Name</th>
                    <th width="140">GRN No</th>
                    <th width="200">GRN Date</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:60px" class="formbutton" />
                    	<input type="hidden" name="hidden_wo_pi_id" id="hidden_wo_pi_id" class="text_boxes" value="">  
                        <input type="hidden" name="hidden_wo_pi_no" id="hidden_wo_pi_no" class="text_boxes" value=""> 
                        <input type="hidden" name="hidden_data" id="hidden_data" class="text_boxes" value="">
                        <input type="hidden" name="receive_basis" id="receive_basis" class="text_boxes" value=""> 
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">	
                    	<? echo create_drop_down("cbo_company_id",140,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--",$cbo_company_id,"",1,"1,2"); ?>
                    </td>
                    <td align="center" id="supplier_td_id">	
                    	<?
						$sup_cond="";
						
						if(str_replace("'","",$cbo_supplier)>0) $sup_cond=" and a.id=$cbo_supplier"; 
						//echo create_drop_down( "cbo_supplier", 140,"select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$cbo_company_id $sup_cond and b.party_type in(4,5) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name",'id,supplier_name', 1, '-- Select Supplier --',0,0);
						echo create_drop_down("cbo_supplier", 142, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$cbo_company_id $sup_cond and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
						?>
                    </td>
                    <td align="center">				
                        <input type="text" style="width:100px" class="text_boxes"  name="txt_grn_no" id="txt_grn_no" />	
                    </td>
                    <td align="center">
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px">To
					  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px">
					</td>						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_grn_no').value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_supplier').value+'_'+document.getElementById('cbo_year_selection').value, 'create_wo_pi_search_list_view', 'search_div', 'yarn_grn_return_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:60px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="8" align="center" height="30" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
            <div style="margin-top:10px;" id="search_div" align="left"></div> 
		</fieldset>
	</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_wo_pi_search_list_view")
{
	$data = explode("_",$data);
	//echo $data[1]."jahid";die;
	$txt_grn_no=trim($data[0]);
	$company_id =$data[1];
	$date_form=trim($data[2]);
	$date_to =trim($data[3]);
	$cbo_supplier =trim($data[4]);
	$cbo_year =$data[5];
	//echo $pay_mode.jahid;die;
	if($cbo_supplier==0 && $txt_grn_no=="" && $date_form=="" && $date_to=="")
	{
		echo "Select Date Range";die;
	}
	
	if($date_form!="" && $date_to!="")
	{
		if($db_type==0)
		{
			$date_form=change_date_format($date_form,'yyyy-mm-dd', "-");
			$date_to=change_date_format($date_to,'yyyy-mm-dd', "-");
		}
		else
		{
			$date_form=change_date_format($date_form,'','',1);
			$date_to=change_date_format($date_to,'','',1);
		}
	}
	else
	{
		if($db_type==0){ $year_cond=" and year(a.insert_date)=$cbo_year ";}
		else if($db_type==2){ $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year ";}
	}

	
	//echo $date_form."==".$date_to;die;
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$user_name_arr = return_library_array("select id, user_full_name from user_passwd","id","user_full_name");
	
	$search_field_cond="";
	if(trim($txt_grn_no)!="")
	{
		$search_field_cond.=" and a.recv_number like '%$txt_grn_no'";
	}
	
	if($date_form!="" && $date_to!="")
	{
		$search_field_cond.=" and a.receive_date between '$date_form' and '$date_to'";
	}
	
	if($cbo_supplier>0) $search_field_cond.=" and a.supplier_id=$cbo_supplier";
	
	$sql_grn_qc="SELECT p.WO_PI_ID, sum(b.PARKING_QUANTITY) as ORDER_QNTY
	from QUARANTINE_PARKING_DTLS p 
	where p.ITEM_CATEGORY_ID=1 and p.status_active=1 and p.is_deleted=0 and p.ENTRY_FORM=531
	group by p.WO_PI_ID";
	//echo $sql_receive;//die;
	$sql_grn_qc_result = sql_select($sql_grn_qc);
	$grn_qc_data=array();
	foreach($sql_grn_qc_result as $val)
	{
		$grn_prev_return_data[$val["WO_PI_ID"]]+=$val["ORDER_QNTY"];
	}
	unset($sql_grn_qc_result);
	
	
	$sql = "SELECT a.ID, a.RECV_NUMBER_PREFIX_NUM, a.RECV_NUMBER, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.CHALLAN_NO, a.CURRENCY_ID, a.SOURCE, a.SUPPLIER_ID, a.BOOKING_ID, a.BOOKING_NO, to_char(a.insert_date,'YYYY') as YEAR, a.INSERTED_BY, sum(b.PARKING_QUANTITY) as QUANTITY
	from INV_RECEIVE_MASTER a,  QUARANTINE_PARKING_DTLS b  
	where a.id=b.mst_id and a.entry_form=529 and b.entry_form=529 and b.ITEM_CATEGORY_ID=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.IS_QC_PASS=0 and a.COMPANY_ID=$company_id $search_field_cond $year_cond
	group by a.ID, a.RECV_NUMBER_PREFIX_NUM, a.RECV_NUMBER, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.CHALLAN_NO, a.CURRENCY_ID, a.SOURCE, a.SUPPLIER_ID, a.BOOKING_ID, a.BOOKING_NO, a.INSERT_DATE, a.INSERTED_BY
	order by a.ID desc"; 
	//echo $sql;
	$result = sql_select($sql);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="930" class="rpt_table">
        <thead>
            <tr>
                <th colspan="9"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
            </tr>
            <tr>
                <th width="50">SL</th>
                <th width="110">GRN No</th>
                <th width="70">GRN Date</th>               
                <th width="150">Supplier</th>
                <th width="80">Challan No</th>
                <th width="100">Source</th>
                <th width="80">Currency</th>
                <th width="110">WO/PI</th>
                <th>Insert User</th>
            </tr>
        </thead>
    </table>
    <div style="width:930px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {
                $balance_qnty=$row["QUANTITY"]-$grn_prev_return_data[$row["BOOKING_ID"]];
                //echo $balance_qnty."=".$grn_qc_data[$row["ID"]]."=".$pi_receive_data[$row["BOOKING_ID"]];
                if($balance_qnty>0)
                {  
                    if ($i%2==0)  
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";	
                    
                    $data=$row['SUPPLIER_ID']."**".$row['CURRENCY_ID']."**".$row['SOURCE']."**".$row['BOOKING_ID']."**".$row['BOOKING_NO'];
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row["ID"]; ?>,'<? echo $row['RECV_NUMBER']; ?>','<? echo $data; ?>','<? echo $row['RECEIVE_BASIS']; ?>');">
                        <td width="50" align="center"><? echo $i; ?></td>
                        <td width="110" align="center"><p><? echo $row['RECV_NUMBER']; ?></p></td>
                        <td width="70" align="center"> <p> <? echo change_date_format($row['RECEIVE_DATE']); ?> </p> </td>
                        <td width="150"><? echo $supplier_arr[$row['SUPPLIER_ID']]; ?></td>						
                        <td width="80" align="center"><p><? echo $row['CHALLAN_NO']; ?></p></td>
                        <td width="100" align="center"><? echo $source[$row['SOURCE']]; ?>&nbsp;</td>               
                        <td width="80" align="center"><p><? echo $currency[$row['CURRENCY_ID']]; ?>&nbsp;</p></td>
                        <td width="110"><? echo $row['BOOKING_NO']; ?>&nbsp;</td>
                        <td ><p><? echo $user_name_arr[$row['INSERTED_BY']]; ?></p></td>
                    </tr>
                    <?
                    $i++;
                }
            }
            ?>
        </table>
    </div>
    <?
	exit();
}


if( $action == 'mrr_details' ) 
{
	//echo $data;die;
	
	?>
    <tr id="row_1" align="center">
        <td id="count_1"></td>
        <td id="composition_1"></td>
        <td id="comPersent_1"></td>
        <td id="color_1"></td>
        <td id="yarnType_1"></td>
        <td id="uom_1"></td>
        <td id="grnqnty_1"></td>
        <td id="tdlreturnqnty_1"><input type="text" name="returnqnty[]" id="returnqnty_1" class="text_boxes_numeric" style="width:70px;" value="" onBlur="calculate(1);"/></td>
        <td id="tdrate_1"></td>
        <td id="tdamount_1">
        <input type="hidden" name="updatedtlsid[]" id="updatedtlsid_1" value="" readonly>
        <input type="hidden" name="grnDtlsId[]" id="grnDtlsId_1" value="" readonly>
        </td>
    </tr>
    <?
	exit();
}


if( $action == 'show_fabric_desc_listview' ) 
{
	$data=explode("**",$data);
	
	$bookingNo_piId=$data[0];
	
	$sql="select a.ID, a.RECV_NUMBER_PREFIX_NUM, a.RECV_NUMBER, a.ENTRY_FORM, a.RECEIVE_BASIS, a.RECEIVE_DATE, b.ID as DTLS_ID, b.WO_PI_ID, b.WO_PI_DTLS_ID, b.WO_PI_NO, b.PO_BREAK_DOWN_ID, b.ITEM_ID, b.YARN_COUNT, b.YARN_COMP_TYPE1ST, b.YARN_COMP_PERCENT1ST, b.YARN_TYPE, b.COLOR_NAME, b.LOT, b.BRAND_NAME, b.PRODUCT_CODE, b.UOM, b.WO_PI_QUANTITY, b.PARKING_QUANTITY, b.RATE, b.AMOUNT 
	from INV_RECEIVE_MASTER a, QUARANTINE_PARKING_DTLS b
	where a.id=b.mst_id and a.entry_form=529 and b.entry_form=529 and b.ITEM_CATEGORY_ID=1 and b.IS_QC_PASS=0 and a.id in($bookingNo_piId) and a.status_active=1 and b.status_active=1";
	//echo $sql; //die;
	$data_array=sql_select($sql);
	$yarn_count_library = return_library_array("select id,yarn_count from lib_yarn_count where status_active=1 and is_deleted=0", 'id', 'yarn_count');
	$color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$i=1;
	foreach($data_array as $row)
	{
		if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $i; ?>" align="center">
        	<td id="count_<? echo $i; ?>" title="<?= $row["YARN_COUNT"];?>"><? echo $yarn_count_library[$row["YARN_COUNT"]]; ?></td>
            <td id="composition_<? echo $i; ?>" title="<?= $row["YARN_COMP_TYPE1ST"];?>"><? echo $composition[$row["YARN_COMP_TYPE1ST"]]; ?></td>
            <td id="comPersent_<? echo $i; ?>" title="<?= $row["YARN_COMP_PERCENT1ST"];?>"><? echo $row["YARN_COMP_PERCENT1ST"]; ?></td>
            <td id="color_<? echo $i; ?>" title="<?= $row["COLOR_NAME"];?>"><? echo $color_library[$row["COLOR_NAME"]]; ?></td>
            <td id="yarnType_<? echo $i; ?>" title="<?= $row["YARN_TYPE"];?>"><? echo $yarn_type[$row["YARN_TYPE"]]; ?></td>
            <td id="uom_<? echo $i; ?>" title="<?= $row["UOM"];?>"><? echo $unit_of_measurement[$row["UOM"]]; ?></td>
            <td id="grnqnty_<? echo $i; ?>" align="right" title="<?= $row["PARKING_QUANTITY"];?>"><? echo number_format($row["PARKING_QUANTITY"],3,'.',''); ?></td>
            <td id="tdlreturnqnty_<? echo $i; ?>"><input type="text" name="returnqnty[]" id="returnqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px;" value="<? echo number_format($row["PARKING_QUANTITY"],3,'.',''); ?>" onBlur="calculate(<? echo $i; ?>);" readonly disabled /></td>
            <td id="tdrate_<? echo $i; ?>" align="right" title="<?= $row["RATE"];?>"><? echo number_format($row["RATE"],3,'.',''); ?></td>
            <td id="tdamount_<? echo $i; ?>" align="right" title="<?= $row["AMOUNT"];?>">
			<? echo number_format($row["AMOUNT"],3,'.',''); ?>
            <input type="hidden" name="updatedtlsid[]" id="updatedtlsid_<? echo $i; ?>" value="" readonly>
            <input type="hidden" name="grnDtlsId[]" id="grnDtlsId_<? echo $i; ?>" value="<? echo $row["DTLS_ID"]; ?>" readonly>
            </td>
		</tr>
		<?
		$i++;
    }
	exit();
}

if( $action == 'show_fabric_desc_listview_update' ) 
{
	$data=explode("**",$data);
	
	$bookingNo_piId=$data[0];
	
	$sql="select a.ID, b.ID as DTLS_ID, b.WO_PI_ID, b.WO_PI_DTLS_ID, b.WO_PI_NO, b.PO_BREAK_DOWN_ID, b.ITEM_ID, b.YARN_COUNT, b.YARN_COMP_TYPE1ST, b.YARN_COMP_PERCENT1ST, b.YARN_TYPE, b.COLOR_NAME, b.LOT, b.BRAND_NAME, b.PRODUCT_CODE, b.UOM, b.WO_PI_QUANTITY, b.PARKING_QUANTITY, b.RATE, b.AMOUNT 
	from QUARANTINE_PARKING_MST a, QUARANTINE_PARKING_DTLS b
	where a.id=b.mst_id and a.entry_form=531 and b.entry_form=531 and b.ITEM_CATEGORY_ID=1 and a.id in($bookingNo_piId) and a.status_active=1 and b.status_active=1";
	//echo $sql; //die;
	$data_array=sql_select($sql);
	$yarn_count_library = return_library_array("select id,yarn_count from lib_yarn_count where status_active=1 and is_deleted=0", 'id', 'yarn_count');
	$color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$i=1;
	foreach($data_array as $row)
	{
		if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $i; ?>" align="center">
        	<td id="count_<? echo $i; ?>" title="<?= $row["YARN_COUNT"];?>"><? echo $yarn_count_library[$row["YARN_COUNT"]]; ?></td>
            <td id="composition_<? echo $i; ?>" title="<?= $row["YARN_COMP_TYPE1ST"];?>"><? echo $composition[$row["YARN_COMP_TYPE1ST"]]; ?></td>
            <td id="comPersent_<? echo $i; ?>" title="<?= $row["YARN_COMP_PERCENT1ST"];?>"><? echo $row["YARN_COMP_PERCENT1ST"]; ?></td>
            <td id="color_<? echo $i; ?>" title="<?= $row["COLOR_NAME"];?>"><? echo $color_library[$row["COLOR_NAME"]]; ?></td>
            <td id="yarnType_<? echo $i; ?>" title="<?= $row["YARN_TYPE"];?>"><? echo $yarn_type[$row["YARN_TYPE"]]; ?></td>
            <td id="uom_<? echo $i; ?>" title="<?= $row["UOM"];?>"><? echo $unit_of_measurement[$row["UOM"]]; ?></td>
            <td id="grnqnty_<? echo $i; ?>" align="right" title="<?= $row["WO_PI_QUANTITY"];?>"><? echo number_format($row["WO_PI_QUANTITY"],3,'.',''); ?></td>
            <td id="tdlreturnqnty_<? echo $i; ?>"><input type="text" name="returnqnty[]" id="returnqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px;" value="<? echo number_format($row["PARKING_QUANTITY"],3,'.',''); ?>" onBlur="calculate(<? echo $i; ?>);" readonly disabled /></td>
            <td id="tdrate_<? echo $i; ?>" align="right" title="<?= $row["RATE"];?>"><? echo number_format($row["RATE"],3,'.',''); ?></td>
            <td id="tdamount_<? echo $i; ?>" align="right" title="<?= $row["AMOUNT"];?>">
			<? echo number_format($row["AMOUNT"],3,'.',''); ?>
            <input type="hidden" name="updatedtlsid[]" id="updatedtlsid_<? echo $i; ?>" value="<? echo $row["DTLS_ID"]; ?>" readonly>
            <input type="hidden" name="grnDtlsId[]" id="grnDtlsId_<? echo $i; ?>" value="<? echo $row["WO_PI_DTLS_ID"]; ?>" readonly>
            </td>
		</tr>
		<?
		$i++;
    }
	exit();
}

 

if ($action=="save_update_delete")
{
	//$process = array( &$_POST );
	$process = $_POST;
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$trims_recv_num=''; $master_id='';
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			$id = return_next_id_by_sequence("QUARANTINE_PARKING_MST_PK_SEQ", "quarantine_parking_mst", $con);
			$new_trims_recv_system_id = explode("*", return_next_id_by_sequence("QUARANTINE_PARKING_MST_PK_SEQ", "quarantine_parking_mst",$con,1,$cbo_company_id,'YGRNR',531,date("Y",time()) ));
			$field_array="id, qc_number_prefix, qc_number_prefix_num, qc_number, entry_form, receive_date, company_id, supplier_id, qa_parson, grn_id, grn_number, inserted_by, insert_date";
			
			$data_array="(".$id.",'".$new_trims_recv_system_id[1]."',".$new_trims_recv_system_id[2].",'".$new_trims_recv_system_id[0]."',531,".$txt_return_date.",".$cbo_company_id.",".$cbo_supplier.",".$txt_remarks.",".$txt_wo_pi_id.",".$txt_booking_pi_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$trims_recv_num=$new_trims_recv_system_id[0];
			$master_id=$id;
		}
		else
		{
			$field_array_update="supplier_id*qa_parson*updated_by*update_date";
			
			$data_array_update=$cbo_supplier."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$trims_recv_num=str_replace("'","",$txt_recieved_id);
			$master_id=str_replace("'","",$update_id);
		}
		
		$field_array_dtls="id, mst_id, wo_pi_id, wo_pi_dtls_id, parking_date, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_type, color_name, uom, wo_pi_quantity, parking_quantity, rate, amount, inserted_by, insert_date, status_active, is_deleted, item_category_id, entry_form";
		$grn_dtls_id_arr=array();
		for($i=1;$i<=$tot_row; $i++)
		{
			$count="count".$i;
			$composition="composition".$i;
			$comPersent="comPersent".$i;
			$yarnType="yarnType".$i;
			$color="color".$i;
			$uom="uom".$i;
			$grnqnty="grnqnty".$i;
			$returnqnty="returnqnty".$i;
			$rate="rate".$i;
			$amount="amount".$i;
			$grnDtlsId="grnDtlsId".$i;
			$updatedtlsid="updatedtlsid".$i;
			
			$id_dtls = return_next_id_by_sequence("QUARANTINE_PARKING_DTLS_PK_SEQ", "quarantine_parking_dtls", $con);
			$data_array_dtls.="(".$id_dtls.",".$master_id.",".$txt_wo_pi_id.",'".$$grnDtlsId."',".$txt_return_date.",'".$$count."','".$$composition."','".$$comPersent."','".$$yarnType."','".$$color."','".$$uom."','".$$grnqnty."','".$$returnqnty."','".$$rate."','".$$amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0','1','531')";
			
			$grn_dtls_id_arr[str_replace("'","",$$grnDtlsId)]=str_replace("'","",$$grnDtlsId);
		}
		
		//echo "10**insert into inv_receive_master (".$field_array.") values ".$data_array;oci_rollback($con);die;
		$rID=$rID2=$rID3=true;
		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("quarantine_parking_mst",$field_array,$data_array,0);
		}
		else
		{
			$rID=sql_update("quarantine_parking_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		}
		
		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;oci_rollback($con);disconnect($con);die;
		if($data_array_dtls!="")
		{
			//echo "10**insert into quarantine_parking_dtls (".$field_array_dtls.") values ".$data_array_dtls;oci_rollback($con);disconnect($con);die;
			$rID2=sql_insert("quarantine_parking_dtls",$field_array_dtls,$data_array_dtls,0);
		}
		
		if(count($grn_dtls_id_arr)>0)
		{
			$rID3=execute_query("update quarantine_parking_dtls set is_return=1 where id in(".implode(",",$grn_dtls_id_arr).")");
		}
		//echo "10**$rID=$rID2=$rID3";oci_rollback($con);disconnect($con);die;
		//oci_rollback($con);check_table_status( $_SESSION['menu_id'],0);disconnect($con);die;$ordProdUpdate=$ordProdInsert=
		
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3)
			{
				mysql_query("COMMIT");  
				echo "0**".$master_id."**".$trims_recv_num."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3)
			{
				oci_commit($con);  
				echo "0**".$master_id."**".$trims_recv_num."**0";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$trims_recv_num=str_replace("'","",$txt_recieved_id);
		$master_id=str_replace("'","",$update_id);
		
		if($master_id<1)
		{
			echo "40**Update Not Allow";disconnect($con);die;
		}
		
		$field_array_update="supplier_id*qa_parson*updated_by*update_date";
		$data_array_update=$cbo_supplier."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$field_array_dtls="id, mst_id, wo_pi_id, wo_pi_dtls_id, parking_date, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_type, color_name, uom, wo_pi_quantity, parking_quantity, rate, amount, inserted_by, insert_date, status_active, is_deleted, item_category_id, entry_form";
		
		$field_array_dtls_update="wo_pi_quantity*parking_quantity*rate*amount*updated_by*update_date";
		$updateDtlsID_array=$grn_dtls_back_id_arr=array();
		$data_array_dtls="";
		for($i=1;$i<=$tot_row; $i++)
		{
			$count="count".$i;
			$composition="composition".$i;
			$comPersent="comPersent".$i;
			$yarnType="yarnType".$i;
			$color="color".$i;
			$uom="uom".$i;
			$grnqnty="grnqnty".$i;
			$returnqnty="returnqnty".$i;
			$rate="rate".$i;
			$amount="amount".$i;
			$grnDtlsId="grnDtlsId".$i;
			$updatedtlsid="updatedtlsid".$i;
			
			
			if($$updatedtlsid>0)
			{
				$updateDtlsID_array[]=$$updatedtlsid;
				$data_array_dtls_update[$$updatedtlsid]=explode("*",("'".$$rejectqnty."'*'".$$qcqnty."'*'".$$cbostatus."'*'".$$cbograde."'*'".$$cboyarntest."'*'".$$comments."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				unset($prev_grn_dlts_ids[$$updatedtlsid]);
			}
			else
			{
				$id_dtls = return_next_id_by_sequence("QUARANTINE_PARKING_DTLS_PK_SEQ", "quarantine_parking_dtls", $con);
				$data_array_dtls.="(".$id_dtls.",".$master_id.",".$txt_wo_pi_id.",'".$$grnDtlsId."',".$txt_return_date.",'".$$count."','".$$composition."','".$$comPersent."','".$$yarnType."','".$$color."','".$$uom."','".$$grnqnty."','".$$returnqnty."','".$$rate."','".$$amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0','1','531')";
				
				$grn_dtls_id_arr[str_replace("'","",$$grnDtlsId)]=str_replace("'","",$$grnDtlsId);
			}
			
		}
		
		
		$rID=$dtlsUpdate=$rID2=$rID3=true;
		//echo "10** $field_array_update = $data_array_update = $master_id";oci_rollback($con);disconnect($con);die;
		$rID=sql_update("quarantine_parking_mst",$field_array_update,$data_array_update,"id",$master_id,1);
		
		if(count($updateDtlsID_array)>0)
		{
			$dtlsUpdate=execute_query(bulk_update_sql_statement("quarantine_parking_dtls","id",$field_array_dtls_update,$data_array_dtls_update,$updateDtlsID_array),1);
		}
		
		if($data_array_dtls!="")
		{
			$rID2=sql_insert("quarantine_parking_dtls",$field_array_dtls,$data_array_dtls,0);
		}
		
		if(count($grn_dtls_id_arr)>0)
		{
			$rID3=execute_query("update quarantine_parking_dtls set is_return=1 where id in(".implode(",",$grn_dtls_id_arr).")");
		}
		
		//echo "10**$rID=$dtlsUpdate=$rID2=$rID3=$rID4=$rID5";oci_rollback($con);disconnect($con);die;
		//oci_rollback($con);check_table_status( $_SESSION['menu_id'],0);disconnect($con);die;
		
		
		
		if($db_type==0)
		{
			if($rID && $dtlsUpdate && $rID2 && $rID3)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $master_id)."**".str_replace("'", '', $txt_recieved_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**0**1";
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID && $dtlsUpdate && $rID2 && $rID3)
			{
				oci_commit($con);  
				echo "1**".str_replace("'", '', $master_id)."**".str_replace("'", '', $txt_recieved_id)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**0**0**1";
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}		
		
		$update_id=str_replace("'","",$update_id);
		
		if($update_id>0)
		{
			if($db_type==0) $trns_id_select=", group_concat(id) as all_rcv_id"; else $trns_id_select=", LISTAGG(cast(id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY id) as all_rcv_id";
			$rcv_sql=sql_select("select max(id) as rcv_id, prod_id, sum(cons_quantity) as rcv_qnty, sum(cons_amount) as rcv_amt  $trns_id_select  from inv_transaction where transaction_type=1 and mst_id=$update_id and status_active=1 group by prod_id order by prod_id");
			$receive_data=array();
			$all_rcv_trans_id="";$all_product_id="";$prod_wise_data=array();
			foreach($rcv_sql as $row)
			{
				$receive_data[$row[csf("prod_id")]]=$row[csf("rcv_id")];
				$all_rcv_trans_id.=$row[csf("rcv_id")].",";
				$all_rcv_id.=$row[csf("all_rcv_id")].",";
				$all_product_id.=$row[csf("prod_id")].",";
				$prod_wise_data[$row[csf("prod_id")]]["rcv_qnty"]=$row[csf("rcv_qnty")];
				$prod_wise_data[$row[csf("prod_id")]]["rcv_amt"]=$row[csf("rcv_amt")];
				
			}
			
			$all_rcv_id=chop($all_rcv_id,",");
			$all_rcv_trans_id=chop($all_rcv_trans_id,",");
			$all_product_id=chop($all_product_id,",");
			
			
			$issue_sql=sql_select("select min(a.id) as issue_id, min(b.issue_number) as issue_number, a.prod_id  
			from inv_transaction a, inv_issue_master b 
			where a.mst_id=b.id and a.transaction_type in(2,3) and a.transaction_date >='".str_replace("'","",$txt_receive_date)."' and a.status_active=1 and a.prod_id in($all_product_id)
			group by prod_id
			union all 
			select min(a.id) as issue_id, min(b.transfer_system_id) as issue_number, a.prod_id  
			from inv_transaction a, inv_item_transfer_mst b 
			where a.mst_id=b.id and a.transaction_type in(2,3) and a.transaction_date >='".str_replace("'","",$txt_receive_date)."' and a.status_active=1 and a.prod_id in($all_product_id)
			group by prod_id");
			$issue_data=array();
			foreach($issue_sql as $row)
			{
				$issue_data[$row[csf("prod_id")]]["issue_id"]=$row[csf("issue_id")];
				$issue_data[$row[csf("prod_id")]]["issue_number"]=$row[csf("issue_number")];
			}
			
			foreach($receive_data as $prod_id=>$rcv_val)
			{
				if($issue_data[$prod_id]["issue_id"]>0)
				{
					if($issue_data[$prod_id]["issue_id"]>$rcv_val)
					{
						$issue_num=$issue_data[$prod_id]["issue_number"];
						echo "20**Issue Number $issue_num Found, Product Id $prod_id , Delete Not Allow.";die;
					}
				}
			}
			
			
			$field_array_prod_update="avg_rate_per_unit*current_stock*stock_value*updated_by*update_date";
			$row_prod=sql_select( "select id, current_stock, avg_rate_per_unit, stock_value 
			from product_details_master where id in($all_product_id) and status_active=1 and is_deleted=0");
			foreach($row_prod as $row)
			{
				$prev_prod_qnty=$prod_wise_data[$row[csf("id")]]["rcv_qnty"];
				$prev_prod_amount=$prod_wise_data[$row[csf("id")]]["rcv_amt"];
				
				$curr_stock_qnty=($row[csf("current_stock")]-$prev_prod_qnty);
				if ($curr_stock_qnty != 0){
					$curr_stock_value=($row[csf("current_stock")]-$prev_prod_amount);
					$avg_rate_per_unit=0;
					if ($curr_stock_value != 0 && $curr_stock_qnty != 0) $avg_rate_per_unit=abs($curr_stock_value/$curr_stock_qnty);
					else $avg_rate_per_unit=0;
				} else {
					$curr_stock_value=0;
					$avg_rate_per_unit=0;
				}				
			
				$updateProdID_array[]=$row[csf("id")];
				$data_array_prod_update[$row[csf("id")]]=explode("*",("".$avg_rate_per_unit."*".$curr_stock_qnty."*".$curr_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			//echo "10**";print_r($updateProdID_array);print_r($data_array_prod_update);die;
			
			$order_wise_sql=sql_select("select prod_id, po_breakdown_id, quantity, order_amount from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form=24 and trans_id in($all_rcv_id)");
			$prod_order_data=array();
			foreach($order_wise_sql as $row)
			{
				$all_prod_ids[$row[csf("prod_id")]]=$row[csf("prod_id")];
				$all_order_ids[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
				$prod_order_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]]["quantity"]+=$row[csf("quantity")];
				$prod_order_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]]["order_amount"]+=$row[csf("order_amount")];
			}
			$field_array_prod_order_update="avg_rate*stock_quantity*stock_amount*updated_by*update_date";
			$prod_order_sql=sql_select("select id, prod_id, po_breakdown_id, stock_quantity, stock_amount from order_wise_stock where status_active=1 and is_deleted=0 and prod_id in(".implode(",",$all_prod_ids).") and po_breakdown_id in(".implode(",",$all_order_ids).")");
			$avg_rate_per_unit=$curr_stock_qnty=$curr_stock_value=0;
			foreach($prod_order_sql as $row)
			{
				$prev_prod_ord_qnty=$prod_order_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]]["quantity"];
				$prev_prod_ord_amount=$prod_order_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]]["order_amount"];
				
				$curr_stock_qnty=($row[csf("stock_quantity")]-$prev_prod_ord_qnty);
				$curr_stock_value=($row[csf("stock_amount")]-$prev_prod_ord_amount);
				if($curr_stock_qnty > 0 && $curr_stock_value > 0)
				{
					$avg_rate_per_unit=0;
					if($curr_stock_value !=0 && $curr_stock_qnty !=0) $avg_rate_per_unit=abs($curr_stock_value/$curr_stock_qnty);
				}
				else
				{
					$avg_rate_per_unit=0;
				}
				
				$updateProdOrderID_array[]=$row[csf("id")];
				$data_array_prod_order_update[$row[csf("id")]]=explode("*",("".$avg_rate_per_unit."*".$curr_stock_qnty."*".$curr_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			
			$rID=$rID2=$rID3=$rID4=$rID5=$rID6=true;
			if(count($data_array_prod_update)>0)
			{
				$rID=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_prod_update,$data_array_prod_update,$updateProdID_array),1);
			}
			if(count($data_array_prod_order_update)>0)
			{
				$rID5=execute_query(bulk_update_sql_statement("order_wise_stock","id",$field_array_prod_order_update,$data_array_prod_order_update,$updateProdOrderID_array),1);
			}
			$rID6=execute_query("update inv_receive_master set status_active=0, is_deleted=1, updated_by='".$_SESSION['logic_erp']['user_id']."', update_date='".$pc_date_time."' where id=$update_id");
			$rID2=execute_query("update inv_transaction set status_active=0, is_deleted=1, updated_by='".$_SESSION['logic_erp']['user_id']."', update_date='".$pc_date_time."' where transaction_type=1 and mst_id=$update_id");
			$rID3=execute_query("update inv_trims_entry_dtls set status_active=0, is_deleted=1, updated_by='".$_SESSION['logic_erp']['user_id']."', update_date='".$pc_date_time."' where mst_id=$update_id");
			$rID4=execute_query("update order_wise_pro_details set status_active=0, is_deleted=1, updated_by='".$_SESSION['logic_erp']['user_id']."', update_date='".$pc_date_time."' where trans_id in($all_rcv_id) and trans_type=1 and entry_form=24");
			
			//echo "10**$rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6";die;
			
			if($db_type==0)
			{
				if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6)
				{
					mysql_query("COMMIT");  
					echo "2**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_recieved_id)."**0";
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "7**0**0**1";
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6)
				{
					oci_commit($con);  
					echo "2**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_recieved_id)."**0";
				}
				else
				{
					oci_rollback($con);
					echo "7**0**0**1";
				}
			}
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}
	}
}



if ($action=="yarn_receive_popup_search")
{
	echo load_html_head_contents("Yarn QC Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 

	<script>
	
		function js_set_value(id)
		{
			var ids= id.split("_");
			$('#hidden_recv_id').val(ids[0]);
			//$('#hidden_posted_in_account').val(ids[1]);
			parent.emailwindow.hide();
		}
	
    </script>

	</head>

	<body>
	<div align="center" style="width:885px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:883px; margin-left:3px">
			<legend>Enter search words</legend>           
				<table cellpadding="0" cellspacing="0" width="820" class="rpt_table" border="1" rules="all">
					<thead>
						<th>Supplier</th>
						<th>Received Date Range</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="200">Enter Received ID No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
							<input type="hidden" name="hidden_recv_id" id="hidden_recv_id" class="text_boxes" value=""> 
							<input type="hidden" name="hidden_posted_in_account" id="hidden_posted_in_account" class="text_boxes" value=""> 
						</th> 
					</thead>
					<tr class="general">
						<td align="center">
							<?
								echo create_drop_down( "cbo_supplier", 150,"select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$cbo_company_id' and b.party_type in(4,5) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name",'id,supplier_name', 1, '-- ALL Supplier --',0);
							?>       
						</td>
						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
						</td>
						<td align="center">	
							<?
								$search_by_arr=array(1=>"Received ID");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../../') ";							
								echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>     
						<td align="center" id="search_by_td">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
						</td> 						
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_supplier').value+'_'+document.getElementById('cbo_year_selection').value, 'create_trims_recv_search_list_view', 'search_div', 'yarn_grn_return_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
			</table>
			<div style="width:100%; margin-top:5px; margin-left:2px" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_trims_recv_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$supplier_id =$data[5];
	$cbo_year =$data[6];
	
	if($supplier_id==0) $supplier_name="%%"; else $supplier_name=$supplier_id;
	$com_arr = return_library_array("select id, company_name from lib_company","id","company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	//$store_arr = return_library_array("select id, store_name from lib_store_location","id","store_name");
	$user_name_arr = return_library_array("select id, user_name from user_passwd","id","user_name");
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	else
	{
		if($db_type==0){ $year_cond=" and year(insert_date)=$cbo_year";}
		else if($db_type==2){ $year_cond=" and to_char(insert_date,'YYYY')=$cbo_year";}
	}
	

	if(trim($data[0])!="")
	{
		if($search_by==1)	
			$search_field_cond="and qc_number like '%$search_string'";
		else if($search_by==2)
			$search_field_cond="and booking_no like '$search_string'";
		else	
			$search_field_cond="and challan_no like '$search_string'";
	}
	else
	{
		$search_field_cond="";
	}
	
	if($db_type==0){ $year_field="YEAR(insert_date) as year"; }
	else if($db_type==2){ $year_field="to_char(insert_date,'YYYY') as year"; }
	else{ $year_field="";}//defined Later

	$sql = "SELECT id, qc_number_prefix_num as qc_number_prefix, $year_field, qc_number as recv_number, supplier_id, qa_parson, supplier_grading_id, receive_date, inserted_by 
	from QUARANTINE_PARKING_MST where entry_form=531 and status_active=1 and is_deleted=0 and company_id=$company_id and supplier_id like '$supplier_name' $search_field_cond $date_cond  $year_cond order by id desc"; 
	//echo $sql;
	
	//$arr=array(2=>$receive_basis_arr,3=>$supplier_arr,4=>$store_arr,8=>$currency,9=>$source);
	//echo create_list_view("list_view", "Received No,Year,Receive Basis,Supplier,Store,Receive date,Challan No,Challan Date,Currency,Source", "75,50,105,130,80,75,75,80,60","870","240",0, $sql, "js_set_value", "id", "", 1, "0,0,receive_basis,supplier_id,store_id,0,0,0,currency_id,source", $arr, "recv_number_prefix_num,year,receive_basis,supplier_id,store_id,receive_date,challan_no,challan_date,currency_id,source", "",'','0,0,0,0,0,3,0,3,0,0');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table">
        <thead>
			<tr>
				<th colspan="8"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
			</tr>
			<tr>
				<th width="50">Sl</th>	
				<th width="150">Return No</th>
				<th width="50">Year</th>
				<th width="150">Supplier</th>
				<th width="150">Remarks</th>
				<th width="150">Return date</th>
				<th>Insert User</th>
			</tr>
        </thead>
	</table>
    <div style="width:820px; max-height:240px; overflow-y:scroll" id="search_div" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="list_view"> 
        	<tbody>
            </tbody> 
        	<?
            $i=1;
			$result=sql_select($sql);
            foreach($result as $row)
            {  
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
        		?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>');"> 
                    <td width="50" align="center"><p><? echo $i; ?></p></td>	
                    <td width="150"><p><? echo $row[csf('recv_number')]; ?>&nbsp;</p></td>
                    <td width="50" align="center"><p><? echo $row[csf('year')]; ?>&nbsp;</p></td>
                    <td width="150"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
                    <td width="150"><p><? echo $row[csf('qa_parson')]; ?>&nbsp;</p></td>
                    <td width="150" align="center"><p><? if($row[csf('receive_date')]!="" && $row[csf('receive_date')]!="0000-00-00") echo change_date_format($row[csf('receive_date')]); ?>&nbsp;</p></td>
                    <td><p><? echo $user_name_arr[$row[csf('inserted_by')]]; ?>&nbsp;</p></td>
                </tr>
        		<?
            	$i++;
            }
        	?>
        </table>
    </div>
    <?
	exit();
}

if($action=='populate_data_from_trims_recv')
{
	$data_array=sql_select("select id, qc_number as recv_number, company_id, supplier_id, qa_parson, receive_date, grn_id, grn_number
	from QUARANTINE_PARKING_MST where id='$data'");//, booking_id, booking_no, booking_without_order
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_recieved_id').value 				= '".$row[csf("recv_number")]."';\n";
		echo "document.getElementById('update_id').value 					= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		//echo "load_drop_down( 'requires/yarn_grn_return_controller', '".$row[csf("company_id")]."', 'load_drop_down_supplier', 'supplier' );";
		echo "document.getElementById('txt_return_date').value 				= '".change_date_format($row[csf("receive_date")])."';\n";
		echo "document.getElementById('cbo_supplier').value 				= '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_remarks').value 				= '".$row[csf("qa_parson")]."';\n";
		echo "document.getElementById('txt_wo_pi_id').value 				= '".$row[csf("grn_id")]."';\n";
		echo "document.getElementById('txt_booking_pi_no').value 			= '".$row[csf("grn_number")]."';\n";
		
		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		echo "$('#txt_booking_pi_no').attr('disabled','true')".";\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_yarn_return',1,1);\n";  
		exit();
	}
}

function return_domestic_rate($rate,$ile_cost,$exchange_rate,$conversion_factor)
{
	$rate_ile=$rate+$ile_cost;
	$rate_ile_exchange=$rate_ile*$exchange_rate;
	$doemstic_rate=$rate_ile_exchange/$conversion_factor;
	return $doemstic_rate;	
}

if ($action=="trims_receive_entry_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	 //print_r ($data);
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name"  );
	$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	
	$sql="select id, recv_number, receive_basis, receive_date, booking_id, booking_no, challan_no, challan_date, lc_no, source, store_id, supplier_id, currency_id, exchange_rate, booking_without_order, pay_mode from inv_receive_master where id='$data[1]' and status_active=1 and is_deleted=0 and entry_form=24 ";
	//echo $sql;
	$dataArray=sql_select($sql);
	
   ?>
  <div style="width:985px; margin-left:20px;">
    <table width="980" cellspacing="0" align="right" border="0" >
        <tr>
            <td colspan="7" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong>
                <br><b style="font-size:13px">
                <?
                $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
                foreach ($nameArray as $result)
                { 
                    echo $result[csf('plot_no')].', ';  
                    echo $result[csf('level_no')].', ';
                    echo $result[csf('road_no')].', ';  
                    echo $result[csf('block_no')].', '; 
                    echo $result[csf('city')].', '; 
                    echo $result[csf('zip_code')].', ';  
                    echo $result[csf('province')].', '; 
                    echo $country_arr[$result[csf('country_id')]]; 
                    
                }
                ?>
                </b>
            </td>
        </tr>
        
        <tr>
            <td colspan="7" align="center" style="font-size:x-large"><strong><u>Trims Receive Challan</u></strong></center></td>
        </tr>
        <tr>
            <td width="160"><strong>System ID:</strong></td> <td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
            <td width="120"><strong> Receive Basis :</strong></td><td width="175px" ><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
            <td width="125"><strong>Received Date:</strong></td><td width="175px"><? echo  change_date_format($dataArray[0][csf('receive_date')]); ?></td>
        </tr>
        <tr>
            <!--<td><strong>WO/PI:</strong></td> <td width="175px"><?echo $dataArray[0][csf('booking_no')]; ?></td>-->
            <td><strong>Challan No :</strong></td><td width="175px" ><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Challan Date:</strong></td><td width="175px"><? echo  change_date_format($dataArray[0][csf('challan_date')]); ?></td>
            <td><strong>Source:</strong></td><td width="175px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Supplier:</strong></td><td width="175px"><? if($dataArray[0][csf('pay_mode')]==3 || $dataArray[0][csf('pay_mode')]==5) echo $company_library[$dataArray[0][csf('supplier_id')]]; else echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
            <td><strong> Currency:</strong></td><td width="175px"><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
        </tr>
    </table>
    <br>
	<div style="width:100%;">
        <table align="right" cellspacing="0" width="980"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="90" align="center">WO/PI No.</th>
                <th width="90" align="center">Item Group</th>
                <th width="110" align="center">Item Des.</th>
                <th width="70" align="center">Gmts Color</th>
                <th width="70" align="center">Item Color</th>
                <th width="70" align="center">Item Size</th>
                <th width="70" align="center">Buyer Order</th>
                 <th width="70" align="center">Internal Ref. No</th>
                <th width="40" align="center">UOM</th>
                <th width="70" align="center">WO. Qty </th>
                <th width="70" align="center">Curr. Rec. Qty </th>
                <th width="60" align="center">Rate</th>
                <th width="70" align="center">Amount</th>
                <th width="70" align="center">Total Recv. Qty.</th>
                <th width="70" align="center">Balance Qty.</th>
                <th width="50" align="center">Reject Qty</th>
            </thead>
    <?
		$mst_id=$dataArray[0][csf('id')];
		$booking_nos=''; $booking_sam_nos=''; $pi_ids=''; $orderIds='';
		//echo "select booking_no, booking_id, booking_without_order, order_id from inv_trims_entry_dtls where mst_id='$mst_id' and status_active='1' and is_deleted='0'";
		$dtls_data=sql_select("select booking_no, booking_id, booking_without_order, order_id from inv_trims_entry_dtls where mst_id='$mst_id' and status_active='1' and is_deleted='0'");
		foreach($dtls_data as $row)
		{
			$orderIds.=$row[csf('order_id')].",";
			
			if($dataArray[0][csf('receive_basis')]==1)
			{
				$pi_ids.=$row[csf('booking_id')].",";
			}
			else if($dataArray[0][csf('receive_basis')]==12)
			{
				$booking_nos.="'".$row[csf('booking_no')]."',";
			}
			else if($dataArray[0][csf('receive_basis')]==2)
			{
				if($row[csf('booking_without_order')]==1)
				{
					$booking_sam_nos.="'".$row[csf('booking_no')]."',";
				}
				else
				{
					$booking_nos.="'".$row[csf('booking_no')]."',";
				}
			}
		}
		
		$orderIds=chop($orderIds,','); 
		$piArray=array();
		//echo $orderIds.test;
		if($orderIds!="")
		{
			$orderIds=implode(",",array_unique(explode(",",$orderIds)));
			
			$piArray=array();
			$sql="select a.id, a.po_number, a.grouping as internal_ref from wo_po_break_down a where a.id in($orderIds)";
			//echo $sql;
			$po_data=sql_select($sql);
			foreach($po_data as $row)
			{
				
				$piArray[$row[csf('id')]]['po_number']=$row[csf('po_number')];
				$piArray[$row[csf('id')]]['grouping']=$row[csf('internal_ref')];
			}
			
		}
		//echo "<pre>";print_r($piArray);die;
		//echo $dataArray[0][csf('receive_basis')];die;
		if($dataArray[0][csf('receive_basis')]==2)
		{
			
			$recv_wo_data_arr=array();$recv_wo_data_arr_amt=array();
			$sql_recv = "select a.booking_no, b.order_id as po_id, b.item_group_id as item_group, b.item_description, b.gmts_color_id, b.item_color, b.item_size, a.recv_number, sum(c.quantity) as receive_qnty 
			from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c
			where a.id=b.mst_id  and a.booking_no=b.booking_no and b.id=c.dtls_id and b.trans_id=c.trans_id and c.entry_form=24 and a.entry_form=24 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.trans_type=1 and c.po_breakdown_id in($orderIds) 
			group by a.recv_number, a.booking_no, b.item_group_id, b.item_description, b.gmts_color_id, b.item_color, b.item_size, b.order_id";
			//echo $sql_recv;//die;
			$recv_data=sql_select($sql_recv);
			foreach($recv_data as $row)
			{ //pre_cost_fabric_cost_dtls_id
				$po_id_arr=array_unique(explode(",",$row[csf('po_id')]));
				foreach($po_id_arr as $po)
				{
					$recv_wo_data_arr[$row[csf('booking_no')]][$po][$row[csf('item_group')]][$row[csf('item_description')]]['recv_no'].=$row[csf('recv_number')].',';
					$recv_wo_data_arr_amt[$row[csf('recv_number')]][$row[csf('booking_no')]][$po][$row[csf('item_group')]][$row[csf('item_description')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$row[csf('item_size')]]['recv_qty']=$row[csf('receive_qnty')];		
				}
			}
			//echo "<pre>";
			//print_r($recv_wo_data_arr_amt);
			
			
			$booking_nos=chop($booking_nos,','); $booking_sam_nos=chop($booking_sam_nos,',');
			//echo $booking_nos.kok;
			if($booking_nos!="")
			{
				$booking_sam_nos=implode(",",array_unique(explode(",",$booking_sam_nos)));
				//,b.po_break_down_id
				$sql_bookingqty = sql_select("select b.booking_no, sum(c.cons) as wo_qnty, b.trim_group as item_group, c.color_number_id, c.item_color, c.description, c.gmts_sizes, c.item_size 
				from wo_booking_dtls b,wo_trim_book_con_dtls c 
				where b.id=c.wo_trim_booking_dtls_id and b.booking_no=c.booking_no and c.cons>0 and c.status_active=1 and c.is_deleted=0 and b.booking_no in($booking_nos) 
				group by b.booking_no, b.trim_group, c.color_number_id, c.item_color, c.description, c.gmts_sizes, c.item_size");
			}
			
			foreach($sql_bookingqty as $b_qty)
			{
				if($b_qty[csf('color_number_id')]=="") $b_qty[csf('color_number_id')]=0;
				if($b_qty[csf('item_color')]=="") $b_qty[csf('item_color')]=0;
				$booking_qty_arr[$b_qty[csf('booking_no')]][$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]][$b_qty[csf('gmts_sizes')]][$b_qty[csf('item_size')]]+=$b_qty[csf('wo_qnty')];
				
			}
			
			if($booking_sam_nos!="")
			{
				$booking_sam_nos=implode(",",array_unique(explode(",",$booking_sam_nos)));
				$sql_bookingqtysam = sql_select("select a.booking_no, 0 as po_break_down_id, sum(a.trim_qty) as wo_qnty,a.trim_group as item_group,a.fabric_color as item_color,a.gmts_color as color_number_id,a.fabric_description as description, a.item_size, a.gmts_size 
				from wo_non_ord_samp_booking_dtls a 
				where a.booking_no in($booking_sam_nos) and a.status_active=1 and a.is_deleted=0 
				group by a.booking_no,b.po_break_down_id, a.trim_group, a.fabric_color, a.gmts_color, a.fabric_description, a.item_size, a.gmts_size ");	
			}
			foreach($sql_bookingqtysam as $b_qty)
			{
				if($b_qty[csf('color_number_id')]=="") $b_qty[csf('color_number_id')]=0;
				if($b_qty[csf('item_color')]=="") $b_qty[csf('item_color')]=0;
				$booking_qty_arr[$b_qty[csf('booking_no')]][$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]][$b_qty[csf('gmts_size')]][$b_qty[csf('item_size')]]+=$b_qty[csf('wo_qnty')];
			}
		}
		else if($dataArray[0][csf('receive_basis')]==1)
		{
			$pi_ids=chop($pi_ids,',');
			$sql_bookingqty = sql_select("select a.id, b.item_group, b.item_color, b.color_id as color_number_id, b.item_description as description, c.po_break_down_id, sum(b.quantity) as wo_qnty 
			from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c 
			where a.id=b.pi_id and b.work_order_dtls_id=c.id and a.id in($pi_ids) and b.status_active=1 and b.is_deleted=0 
			group by a.id, b.item_group, b.item_color, b.color_id, b.item_description, c.po_break_down_id");	
			foreach($sql_bookingqty as $b_qty)
			{
				if($b_qty[csf('color_number_id')]=="") $b_qty[csf('color_number_id')]=0;
				if($b_qty[csf('item_color')]=="") $b_qty[csf('item_color')]=0;
				$booking_qty_arr[$b_qty[csf('id')]][$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]]+=$b_qty[csf('wo_qnty')];
			}
		}
		else if($dataArray[0][csf('receive_basis')]==12)
		{ 
			$booking_nos=chop($booking_nos,',');	
			$sql_bookingqty = sql_select("select b.po_break_down_id,c.trim_group as item_group,b.booking_no, sum(b.requirment) as wo_qnty,b.item_color,b.gmts_sizes ,b.description from 
			wo_booking_mst a,wo_trim_book_con_dtls b,wo_booking_dtls c
			 where a.booking_no=b.booking_no and a.supplier_id=147 and a.item_category=4 and c.po_break_down_id=b.po_break_down_id and c.job_no=b.job_no and c.booking_no=b.booking_no and c.booking_type=2  and b.booking_no in($booking_nos) group by b.booking_no,b.po_break_down_id, c.trim_group, b.item_color, b.gmts_sizes, b.description");
			 	
			foreach($sql_bookingqty as $b_qty)
			{
				if($b_qty[csf('gmts_sizes')]=="") $b_qty[csf('gmts_sizes')]=0;
				if($b_qty[csf('item_color')]=="") $b_qty[csf('item_color')]=0;
				$booking_qty_arr[$b_qty[csf('booking_no')]][$b_qty[csf('po_break_down_id')]][$b_qty[csf('item_group')]][$b_qty[csf('gmts_sizes')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]]=$b_qty[csf('wo_qnty')];
			}
			
			
		}
		
		//echo "<pre>";print_r($booking_qty_arr);die;

        $i=1;$total_rec_qty=0; $total_rec_balance_qty=0;
        
		 $sql_dtls="select b.booking_no, b.booking_id, b.booking_without_order, b.item_group_id, b.item_description, b.order_id, b.gmts_color_id, b.item_color, b.item_size, sum(b.cons_qnty) as cons_qnty, b.order_uom, b.cons_uom, sum(b.receive_qnty) as receive_qnty, max(b.rate) as rate, sum(b.amount) as amount, sum(b.reject_receive_qnty) as reject_receive_qnty, b.gmts_size_id 
		from inv_trims_entry_dtls b 
		where b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'
		group by b.booking_no, b.booking_id, b.booking_without_order, b.item_group_id, b.item_description, b.order_id, b.gmts_color_id, b.item_color, b.item_size, b.order_uom, b.cons_uom, b.gmts_size_id";
		
        //echo $sql_dtls;
        $sql_result=sql_select($sql_dtls);
        foreach($sql_result as $row)
        {
			//print_r($booking_qty_arr);
            if ($i%2==0)  
                $bgcolor="#E9F3FF";
            else
                $bgcolor="#FFFFFF";
                
                $order_id_arr=explode(",",$row[csf('order_id')]);
				
				$order_number='';$recv_no_arr='';$grouping_number='';$grouping_number_arr=array();
				//echo "<pre>";print_r($piArray);
				foreach($order_id_arr as $po_id)
				{
					$prev_recv_qty=0;
					//echo $po_id."=".$piArray[$po_id]['po_number'];die;
					$order_number.=$piArray[$po_id]['po_number'].',';
					$grouping_number_arr[$piArray[$po_id]['grouping']]=$piArray[$po_id]['grouping'];
					$recv_no_arr=implode(",",array_unique(explode(",",$recv_wo_data_arr[$row[csf('booking_no')]][$po_id][$row[csf('item_group_id')]][$row[csf('item_description')]]['recv_no'])));
					
					$recv_id_arr=explode(",",$recv_no_arr);
					foreach($recv_id_arr as $recv_id)
					{
						if($recv_id!=$dataArray[0][csf('recv_number')])
						{
							$prev_recv_qty+=$recv_wo_data_arr_amt[$recv_id][$row[csf('booking_no')]][$po_id][$row[csf('item_group_id')]][$row[csf('item_description')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$row[csf('item_size')]]['recv_qty'];
						}
					}
				}
				//echo $prev_recv_qty;
				$order_number=chop($order_number,',');
				//$grouping_number=chop($grouping_number,',');
				$grouping_number=implode(',',$grouping_number_arr);
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td title="<?= $row[csf('order_id')]; ?>"><p><? echo $row[csf('booking_no')]; ?></p></td>
                    <td><p><? echo $item_library[$row[csf('item_group_id')]]; ?></p></td>
                    <td><p><? echo $row[csf('item_description')]; ?></p></td>
                    <td><p><? echo $color_library[$row[csf('gmts_color_id')]]; ?></p></td>
                    <td><p><? echo $color_library[$row[csf('item_color')]]; ?></p></td>
                    <td><p><? echo $row[csf('item_size')]; ?></p></td>
                    <td width="170" style="word-break:break-all;"><? echo $order_number; ?></td>
                    <td width="170" style="word-break:break-all;"><? echo $grouping_number; ?></td>
                    <td align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                    <td align="right" title="<? echo $row[csf('booking_no')]."=".$row[csf('item_group_id')]."=".$row[csf('gmts_color_id')]."=".$row[csf('item_color')]."=".$des_dtls."=".$row[csf('gmts_size_id')]."=".$row[csf('item_size')];?>">
					<?
                        if($row[csf('gmts_size_id')]=="") $row[csf('gmts_size_id')]=0;
                        if($row[csf('gmts_color_id')]=="") $row[csf('gmts_color_id')]=0;
                        if($row[csf('item_color')]=="") $row[csf('item_color')]=0;							
                        $woorder_qty='';
                        $descrip_arr=explode(",",$row[csf('item_description')]);
                        $last_index=end(array_values($descrip_arr));
                        $last_index=str_replace("[","",$last_index);
                        $last_index=str_replace("]","",$last_index);
                        if(trim($last_index)=="BS") $des_dtls=chop($row[csf('item_description')],', [BS]'); else $des_dtls=$row[csf('item_description')];
                        if($dataArray[0][csf('receive_basis')]==1)
                        {
							$woorder_qty = $booking_qty_arr[$row[csf('booking_id')]][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$des_dtls];
                        }
                        if($dataArray[0][csf('receive_basis')]==12)
                        {
                            $woorder_qty=$booking_qty_arr[$row[csf('booking_no')]][$row[csf('order_id')]][$row[csf('item_group_id')]][$row[csf('gmts_size_id')]][$row[csf('item_color')]][$row[csf('item_description')]];
                        }
                        else
                        {
							$woorder_qty = $booking_qty_arr[$row[csf('booking_no')]][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$des_dtls][$row[csf('gmts_size_id')]][$row[csf('item_size')]];
                            
                        }
                        $total_woorder_qty+=$woorder_qty;
                        echo number_format($woorder_qty,2,".",""); 
                        $tot_recv_qty=$row[csf('receive_qnty')]+$prev_recv_qty;
                        $tot_recv_balance=$woorder_qty-$tot_recv_qty;//$row[csf('receive_qnty')]+$prev_recv_qty;
                    ?>
                    </td>
                    <td align="right" title="<? echo $des_dtls; ?>"><? echo number_format($row[csf('receive_qnty')],2,".",""); ?></td>
                    <td align="right"><? echo number_format($row[csf('rate')],4,'.',''); ?></td>
                    <td align="right"><? echo number_format($row[csf('amount')],2,'.',''); ?></td>
                    <td align="right"><? echo number_format($tot_recv_qty,2,'.',''); ?></td>
                    <td align="right"><? echo number_format($tot_recv_balance,2,'.',''); ?></td>
                    <td align="right"><? echo number_format($row[csf('reject_receive_qnty')],2,'.',''); ?></td>
                </tr>
            <?
			$i++;
			$tot_rec_qty+=$row[csf('receive_qnty')];
			$tot_amount+=$row[csf('amount')];
			$tot_reject_qty+=$row[csf('reject_receive_qnty')];
			$total_rec_qty+=$tot_recv_qty;
			$total_rec_balance_qty+=$tot_recv_balance;
        }
       ?>
            <tr bgcolor="#dddddd">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                 <td>&nbsp;</td>

                <td colspan="2" align="right"><b>Total :</b></td>
                <td align="right"><? echo number_format($total_woorder_qty,2,'.',''); ?></td>
                <td align="right"><? echo number_format($tot_rec_qty,2,'.',''); ?></td>
                <td>&nbsp;</td>
                <td align="right"><? echo number_format($tot_amount,2,'.',''); ?></td>
                <td align="right"><? echo number_format($total_rec_qty,2,'.',''); ?></td>
                <td align="right"><? echo number_format($total_rec_balance_qty,2,'.',''); ?></td>
                <td align="right"><? echo number_format($tot_reject_qty,2,'.',''); ?></td>
            </tr>
       </table>
       <br>
       <?
		  echo signature_table(35, $data[0], "980px");
	   ?>
	</div>
  </div>
   <?
  exit();
}

?>