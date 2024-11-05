<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "")
{
	header("location:login.php");
	die;
}

function months_diff($larger_date, $smaller_date){
    $d1=new DateTime($larger_date); 
    $d2=new DateTime($smaller_date);                                  
    $Months = $d2->diff($d1); 
    $howeverManyMonths = (($Months->y) * 12) + ($Months->m);
    return $howeverManyMonths;
}

function days_difference($later_date, $earlier_date){
    $date1 = new DateTime($earlier_date);
    $date2 = new DateTime($later_date);
    $days  = $date2->diff($date1)->format('%a'); 
    return $days;
}



$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

//--------------------------------------------------------------------------------------------

if($action == "supplier_popup")
	{
		echo load_html_head_contents("Supplier Info","../../../../", 1, 1, '','1','');
		extract($_REQUEST);
		?>
		<script>
		var selected_id = new Array(); var selected_name = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'table_body' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str )
		{

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}

			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hidden_supplier_id').val(id);
			$('#hidden_supplier').val(name);
		}
		</script>
		</head>
		<fieldset style="width:390px">

			<input type="hidden" name="hidden_supplier" id="hidden_supplier" value="">
			<input type="hidden" name="hidden_supplier_id" id="hidden_supplier_id" value="">
			<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th width="50">SL</th>
						<th width="">Supplier Name</th>
					</tr>
				</thead>
			</table>
			<div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
				<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?

			if($companyID){$companyCon=" and a.tag_company='$companyID'";}
			else{$companyCon="";}

			$result=sql_select("select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id $companyCon and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name");
			$i = 1;
			foreach ($result as $row)
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
					<td width="50">
						<? echo $i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
						<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf("supplier_name")]; ?>"/>
					</td>
					<td width=""><p><? echo $row[csf("supplier_name")]; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>

			</table>
			</div>
			<table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
				<tr>
					<td align="center" height="30" valign="bottom">
						<div style="width:100%">
							<div style="width:50%; float:left" align="left">
								<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
							</div>
							<div style="width:50%; float:left" align="left">
								<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
							</div>
						</div>
					</td>
				</tr>
			</table>
		</fieldset>
		<script type="text/javascript">
			setFilterGrid('table_body',-1);
		</script>
		<?
	}

if($action == "yarn_type_popup")
	{
		echo load_html_head_contents("Yarn Type Info","../../../../", 1, 1, '','1','');
		extract($_REQUEST);
		?>
		<script>
		var selected_id = new Array(); var selected_name = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'table_body' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str )
		{

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}

			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hidden_yarn_type_id').val(id);
			$('#hidden_yarn_type').val(name);
		}
		</script>
		</head>
		<fieldset style="width:390px">
			<input type="hidden" name="hidden_yarn_type" id="hidden_yarn_type" value="">
			<input type="hidden" name="hidden_yarn_type_id" id="hidden_yarn_type_id" value="">
			<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th width="50">SL</th>
						<th width="">Yarn Type Name</th>
					</tr>
				</thead>
			</table>
			<div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
				<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?
			$i = 1;
			foreach ($yarn_type as $key=> $val)
			{
				//var_dump($val);
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
					<td width="50">
						<? echo $i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $key; ?>"/>
						<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $val; ?>"/>
					</td>
					<td width=""><p><? echo $val; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>

			</table>
			</div>
			<table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
				<tr>
					<td align="center" height="30" valign="bottom">
						<div style="width:100%">
							<div style="width:50%; float:left" align="left">
								<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
							</div>
							<div style="width:50%; float:left" align="left">
								<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
							</div>
						</div>
					</td>
				</tr>
			</table>
		</fieldset>
		<script type="text/javascript">
			setFilterGrid('table_body',-1);
		</script>
		<?
	}
    
if($action == "yarn_count_popup")
{
    echo load_html_head_contents("Yarn Count Info","../../../../", 1, 1, '','1','');
    extract($_REQUEST);
    ?>
    <script>
    var selected_id = new Array(); var selected_name = new Array();

    function check_all_data()
    {
        var tbl_row_count = document.getElementById( 'table_body' ).rows.length;

        tbl_row_count = tbl_row_count-1;
        for( var i = 1; i <= tbl_row_count; i++ ) {
            js_set_value( i );
        }
    }

    function toggle( x, origColor )
    {
        var newColor = 'yellow';
        if ( x.style ) {
            x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
        }
    }

    function js_set_value( str )
    {

        toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

        if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
            selected_id.push( $('#txt_individual_id' + str).val() );
            selected_name.push( $('#txt_individual' + str).val() );

        }
        else {
            for( var i = 0; i < selected_id.length; i++ ) {
                if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
            }
            selected_id.splice( i, 1 );
            selected_name.splice( i, 1 );
        }

        var id = ''; var name = '';
        for( var i = 0; i < selected_id.length; i++ ) {
            id += selected_id[i] + ',';
            name += selected_name[i] + ',';
        }

        id = id.substr( 0, id.length - 1 );
        name = name.substr( 0, name.length - 1 );

        $('#hidden_yarn_count_id').val(id);
        $('#hidden_yarn_count').val(name);
    }
    </script>
    </head>
    <fieldset style="width:390px">

        <input type="hidden" name="hidden_yarn_count" id="hidden_yarn_count" value="">
        <input type="hidden" name="hidden_yarn_count_id" id="hidden_yarn_count_id" value="">
        <table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
            <thead>
                <tr>
                    <th width="50">SL</th>
                    <th width="">Yarn Count Name</th>
                </tr>
            </thead>
        </table>
        <div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
            <table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
        <?
        $result=sql_select("select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count");
        $i = 1;
        foreach ($result as $row)
        {
            if ($i % 2 == 0)
                $bgcolor = "#E9F3FF";
            else
                $bgcolor = "#FFFFFF";
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                <td width="50">
                    <? echo $i; ?>
                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
                    <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf("yarn_count")]; ?>"/>
                </td>
                <td width=""><p><? echo $row[csf("yarn_count")]; ?></p></td>
            </tr>
            <?
            $i++;
        }
        ?>

        </table>
        </div>
        <table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%">
                        <div style="width:50%; float:left" align="left">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                        </div>
                        <div style="width:50%; float:left" align="left">
                            <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </fieldset>
    <script type="text/javascript">
        setFilterGrid('table_body',-1);
    </script>
    <?
}

if ($action == "generate_report"){
    //CONS_QUANTITY
    $txt_date_from = $_POST['txt_date_from'];
    $txt_date_to = $_POST['txt_date_to']; 
    $company_id = $_POST['cbo_company_name']; 
    //$supplier = $_POST['txt_supplier']; 
    $supplier = $_POST['txt_supplier']; 
    $cbo_dyed_type = $_POST['cbo_dyed_type']; 
    $txt_yarn_type = $_POST['txt_yarn_type']; 
    $txt_yarn_count = $_POST['txt_yarn_count']; 
    $txt_lot_no = $_POST['txt_lot_no']; 
    $cbo_value_with = $_POST['cbo_value_with']; 
    $txt_excange_rate = $_POST['txt_excange_rate']; 

    $companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");
    $companyArr[0] = "All Company";
    $yarn_composition = return_library_array("select id, COMPOSITION_NAME from LIB_COMPOSITION_ARRAY", "id", "COMPOSITION_NAME");
    $yarn_type = return_library_array("select yarn_type_id, YARN_TYPE_SHORT_NAME from LIB_YARN_TYPE", "yarn_type_id", "YARN_TYPE_SHORT_NAME");
    $yarn_count = return_library_array("select id, YARN_COUNT from LIB_YARN_COUNT", "id", "YARN_COUNT");
    $brandArr = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
    $storeArr = return_library_array("select id, STORE_NAME from LIB_STORE_LOCATION", "id", "STORE_NAME");
    $floorArr = return_library_array("select id, FLOOR_NAME from LIB_PROD_FLOOR", "id", "FLOOR_NAME");
    $roomRackArr = return_library_array("select FLOOR_ROOM_RACK_ID, FLOOR_ROOM_RACK_NAME from LIB_FLOOR_ROOM_RACK_MST", "FLOOR_ROOM_RACK_ID", "FLOOR_ROOM_RACK_NAME");


    $yarn_test_comments_arr = sql_select("select B.PROD_ID, A.COMMENTS_KNIT, A.COMMENTS_DYE, A.COMMENTS_AUTHOR 
                                            from INV_YARN_TEST_COMMENTS A
                                            JOIN INV_YARN_TEST_MST B ON A.MST_TABLE_ID = B.ID ");

    $yarn_comments_array = array();
    foreach($yarn_test_comments_arr as $single_test_mst){
        $yarn_comments_array[$single_test_mst['PROD_ID']] = $single_test_mst['COMMENTS_KNIT']." - ".$single_test_mst['COMMENTS_DYE']." - ".$single_test_mst['COMMENTS_AUTHOR'];
    }

//echo "<pre>"; print_r($yarn_comments_array); exit();

    
    $filter = '';
    if(!empty($company_id)){
        $filter .= " AND P.COMPANY_ID='$company_id'";
    }

    if(!empty($supplier)){
        $filter .= " AND P.SUPPLIER_ID in($supplier)";
    }
    if(!empty($cbo_dyed_type)){
        $filter .= " AND P.DYED_TYPE='$cbo_dyed_type'";
    }
    if(!empty($txt_yarn_type)){
        $filter .= " AND P.YARN_TYPE in ($txt_yarn_type)";
    }
    if(!empty($txt_yarn_count)){
        $filter .= " AND P.YARN_COUNT_ID in ($txt_yarn_count)";
    }
    if(!empty($txt_lot_no)){
        $filter .= " AND P.LOT='$txt_lot_no'";
    }
    if($cbo_value_with == 1){
        $filter .= " AND P.STOCK_VALUE > 0 ";
    }
    /* if(!empty($txt_excange_rate)){
        $filter .= " AND T.ORDER_RATE='$txt_excange_rate'";
    } */
    
    $typeNdateCond = " TO_CHAR(T.TRANSACTION_DATE, 'YYYY-MM-DD') <= TO_CHAR(TO_DATE ('$txt_date_to', 'DD-MM-YYYY'), 'YYYY-MM-DD') AND T.STATUS_ACTIVE=1 AND T.IS_DELETED=0 AND P.STATUS_ACTIVE=1 AND P.IS_DELETED=0 ";

    
    
    $sql = "SELECT P.ID,  P.LOT, P.COMPANY_ID,P.CURRENT_STOCK,  P.YARN_COMP_TYPE1ST, P.YARN_TYPE, P.YARN_COUNT_ID, P.BRAND,
            T.STORE_ID,
            T.CONS_RATE AS RATE_BDT, T.CONS_AMOUNT AS BDT_AMOUNT, T.ORDER_RATE AS RATE_USD, T.ORDER_AMOUNT AS AMOUNT_USD, T.ROOM, T.RACK, T.SELF, T.BIN_BOX, T.FLOOR_ID,
            T.TRANSACTION_DATE, T.TRANSACTION_TYPE, T.CONS_QUANTITY, T.RECEIVE_BASIS

        FROM PRODUCT_DETAILS_MASTER P, INV_TRANSACTION T
        WHERE T.PROD_ID = P.ID AND P.ITEM_CATEGORY_ID = 1 AND 
            $typeNdateCond $filter
    ";
    //echo $sql; exit(); 
    $sql_data = sql_select( $sql );

    $previous_lot_arr = array();
    $today_receive_lot_arr = array();
    $today_close_lot_arr = array();
    $iterable_data = array();
    foreach($sql_data as $sdata){
        //if(  strtotime($txt_date_from) <= strtotime($data[csf('TRANSACTION_DATE')])  ){ 
            //$iterable_data[$sdata['ID']]['SL'] = $sdata['SL'];
            $iterable_data[$sdata['ID']]['COMPANY']             = $companyArr[$sdata[csf('COMPANY_ID')]];
            $iterable_data[$sdata['ID']]['CURRENT_STOCK']       = $sdata[csf('CURRENT_STOCK')];
            $iterable_data[$sdata['ID']]['ID']                  = $sdata[csf('ID')];
            $iterable_data[$sdata['ID']]['YARN_COMPOSITION']    = $yarn_composition[$sdata[csf('YARN_COMP_TYPE1ST')]];
            $iterable_data[$sdata['ID']]['YARN_TYPE']           = $yarn_type[$sdata[csf('YARN_TYPE')]];
            $iterable_data[$sdata['ID']]['YARN_COUNT']          = $yarn_count[$sdata[csf('YARN_COUNT_ID')]];
            $iterable_data[$sdata['ID']]['LOT']                 = $sdata['LOT'];
            $iterable_data[$sdata['ID']]['BRAND']               = $brandArr[$sdata[csf('BRAND')]];
            $iterable_data[$sdata['ID']]['RECEIVE_DATE'][]      = $sdata['TRANSACTION_DATE'];

            $iterable_data[$sdata['ID']]['RATE_BDT']    += $sdata['RATE_BDT'];
            $iterable_data[$sdata['ID']]['BDT_AMOUNT']  += $sdata['BDT_AMOUNT'];
            $iterable_data[$sdata['ID']]['RATE_USD']    += $sdata['RATE_USD'];
            $iterable_data[$sdata['ID']]['AMOUNT_USD']  += $sdata['AMOUNT_USD'];


            $iterable_data[$sdata['ID']]['QUALITY_STATUS']  = !empty($yarn_comments_array[$sdata[csf('ID')]]) ? $yarn_comments_array[$sdata[csf('ID')]]: '';
            $iterable_data[$sdata['ID']]['STORE_NAME']      = $storeArr[$sdata[csf('STORE_ID')]];
            $iterable_data[$sdata['ID']]['FLOOR_NAME']      = $roomRackArr[$sdata[csf('FLOOR_ID')]];
            $iterable_data[$sdata['ID']]['ROOM']            = $roomRackArr[$sdata[csf('ROOM')]];
            $iterable_data[$sdata['ID']]['RACK']            = $roomRackArr[$sdata[csf('RACK')]];
            $iterable_data[$sdata['ID']]['SELF']            = $sdata[csf('SELF')];
            $iterable_data[$sdata['ID']]['BIN_BOX']         = $sdata[csf('BIN_BOX')];
            //MAX_MIN_RECEIVE_DATE CALCULATION.
            if(in_array($sdata['TRANSACTION_TYPE'], [1,4,5])){
                $iterable_data[$sdata['ID']]['RECEIVE_DATE_ARR'][] = $sdata['TRANSACTION_DATE']."*".$sdata['CONS_QUANTITY']; //
            }else{
                $iterable_data[$sdata['ID']]['TOTAL_PRODUCT_ISSUE'] += $sdata['CONS_QUANTITY'];
            }

            //last day closing balance OR OPENNING_BALANCE
            if( strtotime($sdata[csf('TRANSACTION_DATE')]) < strtotime($txt_date_from) ){
                if(in_array($sdata['TRANSACTION_TYPE'], [1,4,5])){
                    $iterable_data[$sdata['ID']]['OPENNING_BALANCE'] += $sdata['CONS_QUANTITY'];
                    $previous_lot_arr[$sdata['LOT']] = $sdata['LOT'];
                }else{
                    $iterable_data[$sdata['ID']]['OPENNING_BALANCE'] -= $sdata['CONS_QUANTITY'];
                }
            }
            //last day OPENNING balance OR CLOSSING_BALANCE + DAY'S
            if( strtotime($sdata[csf('TRANSACTION_DATE')]) <= strtotime($txt_date_from) ){
                if(in_array($sdata['TRANSACTION_TYPE'], [1,4,5])){
                    $iterable_data[$sdata['ID']]['CLOSSING_BALANCE'] += $sdata['CONS_QUANTITY'];
                }else{
                    $iterable_data[$sdata['ID']]['CLOSSING_BALANCE'] -= $sdata['CONS_QUANTITY'];
                }
            }
            //Till last day + today + total receive
            if( strtotime($sdata[csf('TRANSACTION_DATE')]) <= strtotime($txt_date_to) ){
                if( strtotime($sdata[csf('TRANSACTION_DATE')]) == strtotime($txt_date_to) ){
                    if(in_array($sdata['TRANSACTION_TYPE'], [1,4,5])){
                        $today_receive_lot_arr[$sdata['LOT']] = $sdata['LOT'];
                        $iterable_data[$sdata['ID']]['RECEIVE_TODAY'] += $sdata['CONS_QUANTITY'];
                        if($sdata['TRANSACTION_TYPE'] == 4){
                            $iterable_data[$sdata['ID']]['TODAY_ISSUE_RETURN'] += $sdata['CONS_QUANTITY'];
                        }elseif($sdata['TRANSACTION_TYPE'] == 5){
                            $iterable_data[$sdata['ID']]['TODAY_TRANSFER_IN'] += $sdata['CONS_QUANTITY'];
                        }
                    }else{
                        $iterable_data[$sdata['ID']]['ISSUE_TODAY'] += $sdata['CONS_QUANTITY'];
                        if($sdata['TRANSACTION_TYPE'] == 3){
                            $iterable_data[$sdata['ID']]['TODAY_RECEIVE_RETURN'] += $sdata['CONS_QUANTITY'];
                        }elseif($sdata['TRANSACTION_TYPE'] == 6){
                            $iterable_data[$sdata['ID']]['TODAY_TRANSFER_OUT'] += $sdata['CONS_QUANTITY'];
                        }
                    } 
                }else{
                    if(in_array($sdata['TRANSACTION_TYPE'], [1,4,5])){
                        $iterable_data[$sdata['ID']]['RECEIVE_TILL_LAST_DAY'] += $sdata['CONS_QUANTITY'];
                    }else{
                        $iterable_data[$sdata['ID']]['ISSUE_TILL_LAST_DAY'] += $sdata['CONS_QUANTITY'];
                    } 
                }
                
            }

            //tHIS MONTH RECEIVE + ISSUE
            if(date('M', strtotime($sdata[csf('TRANSACTION_DATE')])) == date('M', strtotime($txt_date_to)) ){
                if(in_array($sdata['TRANSACTION_TYPE'], [1,4,5])){
                    $iterable_data[$sdata['ID']]['RECEIVE_THIS_MONTH'] += $sdata['CONS_QUANTITY'];
                    if($sdata['TRANSACTION_TYPE'] == 4){
                        $iterable_data[$sdata['ID']]['THIS_MONTH_ISSUE_RETURN'] += $sdata['CONS_QUANTITY'];
                    }
                }else{
                    $iterable_data[$sdata['ID']]['ISSUE_THIS_MONTH'] += $sdata['CONS_QUANTITY'];
                    if($sdata['TRANSACTION_TYPE'] == 3){
                        $iterable_data[$sdata['ID']]['THIS_MONTH_RECEIVE_RETURN'] += $sdata['CONS_QUANTITY'];
                    }
                }
            }
            //Identify PI basis
            if($sdata['RECEIVE_BASIS'] == 1){ //Receive_basis = 1 PI basis.
                $iterable_data[$sdata['ID']]['PI_RECEIVE'] += $sdata['CONS_QUANTITY'];
            }
        //}

    }

    //last_receive_Date calculation
    $date_total_quantity = 0;
    foreach($iterable_data as $idata){ //echo "<pre>"; print_r($idata['RECEIVE_DATE_ARR']);
        foreach($idata['RECEIVE_DATE_ARR'] as  $receive_date_data){ 
            $date_quantity_arr = explode("*", $receive_date_data); //0 index date, 1 index quantity value.
            $total_issue_product = !empty($iterable_data[$idata['ID']]['TOTAL_PRODUCT_ISSUE']) ? $iterable_data[$idata['ID']]['TOTAL_PRODUCT_ISSUE'] : 0;
            $date_total_quantity += (float)$date_quantity_arr[1];
            if($date_total_quantity > $total_issue_product){
                $iterable_data[$idata['ID']]['LAST_RECEIVE_DATE'] = $date_quantity_arr[0];
            }else{
                $iterable_data[$idata['ID']]['LAST_RECEIVE_DATE'] = '';
            }
        }
        $date_total_quantity = 0;
    }
    //exit();
    //print_r($iterable_data); 
    
    

?>
<div>
    <table width="2222" border="1">
        <tr >
            <td colspan="">
                <h2>AUKO TEX GROUP</h2>
            </td>
            <td colspan="" align="right">
                <h3>Stock Date: <?=$txt_date_from;?>  Report Date: <?=$txt_date_to;?></h3>
            </td>
        </tr>
    </table>
    <table width="3820" border="1" style="font:'Arial Narrow';" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
        <thead>
            <tr>
                <th rowspan="3" width="15">SL</th>
                <th rowspan="3" width="130">Company</th>
                <th rowspan="3" width="60">Prod.ID</th>
                <th rowspan="2" colspan="5">Yarn Description</th>
                <th rowspan="2" colspan="2" width="100">Receive Date</th>
                <th rowspan="3" width="100">Stock Month</th>
                <th rowspan="3" width="110">Opening Balance</th>

                <th colspan="6">Receiving Information</th>
                <th >Blank</th>
                <th colspan="6" width="100">Issue Function</th>
                <th >Blank </th>
                <th rowspan="3" width="50">Closing Balance (Kg)</th>
                <th rowspan="2" colspan="2" width="50">Re-order Level</th>
                <th rowspan="3" width="140">Rate (BDT)</th>
                <th rowspan="3" width="140">Amount(BDT)</th>
                <th rowspan="3" width="140">Rate(USD)</th>
                <th rowspan="3" width="140">Amount(USD)</th>
                <th rowspan="3" width="140">Quality Status</th>
                <th rowspan="2" colspan="6">Storage Location Details</th>
            </tr>
            <tr>
                <th colspan="4" width="90">Spinning Receive</th>
                <th colspan="2" width="90">Return</th>
                <th >Transfer In</th>
                <th colspan="4">Consumption</th>
                <th colspan="2">Return</th>
                <th >Transfer Out</th>
            </tr>
            <tr>
                <th width="100">Yarn Composition</th>
                <th width="100">Yarn Type</th>
                <th width="100">Count</th>
                <th width="50">Lot</th>
                <th width="90">Brand</th>
                <th width="90">First</th>
                <th width="90">Last</th>

                <th width="100">Previous</th>
                <th width="100">Days's</th>
                <th width="100">Total</th>
                <th width="100">Cum Total Monthly</th>
                <th width="90">Day's from Knitting</th>
                <th width="90">Cum Total Monthly</th>
                <th width="90">Day's</th>

                <th width="100">Previous</th>
                <th width="100">Days's</th>
                <th width="100">Total</th>
                <th width="100">Cum Total Monthly</th>
                <th width="90">Day's from Knitting</th>
                <th width="90">Cum Total Monthly</th>
                <th width="90">Day's</th>

                <th width="90">Days Of Coverage</th>
                <th width="90">Re-Order Qty (Kg)</th>


                <th width="130">Store Name</th>
                <th width="100">Floor Name</th>
                <th width="100">Room Name</th>
                <th width="100">Rack No</th>
                <th width="90">Shelf No</th>
                <th width="90">Bin/Box No</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $total_openning_balance = 0;
                $total_receive_today = 0;
                $total_today_issue_return = 0;
                $total_today_transfer_in = 0;
                $total_issue_today = 0;
                $total_today_receive_return = 0;
                $total_pi_receive_balance = 0;

                $index = 1;
                foreach($iterable_data as $data){
                    if ($index % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";

                    //ALL PI RECEIVE BASIS.
                    if(!empty($data['PI_RECEIVE'])){
                        $total_pi_receive_balance += $data['PI_RECEIVE'];
                    }
                        
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $index; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $index; ?>">
                <td width="15" ><?=$index;?></td>
                <td width="130"><?=$data['COMPANY'];?></td>
                <td width="100"><?=$data['ID'];?></td>
                <td width="100"><?=$data[csf('YARN_COMPOSITION')];?></td>
                <td width="90"><?=$data[csf('YARN_TYPE')];?></td>
                <td width="90"><?=$data[csf('YARN_COUNT')];?></td>
                <td width="50"><?=$data['LOT']; ?></td>
                <td width="100"><?=$data['BRAND']; ?></td>
                <td width="90"><?=min($data['RECEIVE_DATE']); ?></td> 
                <td width="90">
                    <?php 
                        //echo $data['RECEIVE_TILL_LAST_DAY']." - ".$data['ISSUE_TILL_LAST_DAY']." = ".$data['CURRENT_STOCK']."<br>"; 
                        echo $data['LAST_RECEIVE_DATE']; 
                    ?>
                </td>
                <td width="90"><?=days_difference($txt_date_to, $data['LAST_RECEIVE_DATE']);?></td>
                <td width="90">
                    <?php 
                        if(!empty($data['OPENNING_BALANCE'])){
                            $openning_balance = round($data['OPENNING_BALANCE'], 2);
                        }else{
                            $openning_balance = 0;
                        }
                        $total_openning_balance += $openning_balance;
                        echo $openning_balance;
                    ?>
                </td>
                <td width="90"><?=!empty($data['RECEIVE_TILL_LAST_DAY']) ? $data['RECEIVE_TILL_LAST_DAY']: 0;?></td>
                <td width="90">
                    <?php 
                        if(!empty($data['RECEIVE_TODAY'])){
                            $receive_today = $data['RECEIVE_TODAY'];
                        }else{
                            $receive_today = 0;
                        }
                        $total_receive_today += $receive_today;
                        echo $receive_today;
                    ?>
                </td>
                <td width="90"><?=(!empty($data['RECEIVE_TILL_LAST_DAY']) ? $data['RECEIVE_TILL_LAST_DAY']: 0) +  (!empty($data['RECEIVE_TODAY']) ? $data['RECEIVE_TODAY']: 0);?></td>
                <td width="90"><?=!empty($data['RECEIVE_THIS_MONTH']) ? $data['RECEIVE_THIS_MONTH']: 0;?></td>

                <td width="100">
                    <?php 
                        if(!empty($data['TODAY_ISSUE_RETURN'])){
                            $today_issue_return = $data['TODAY_ISSUE_RETURN'];
                        }else{
                            $today_issue_return = 0;
                        }
                        $total_today_issue_return += $today_issue_return;
                        echo $today_issue_return;
                    ?>
                </td>
                <td width="100"><?=!empty($data['THIS_MONTH_ISSUE_RETURN']) ? $data['THIS_MONTH_ISSUE_RETURN']: 0;?></td>
                <td width="100">
                    <?php 
                        if(!empty($data['TODAY_TRANSFER_IN'])){
                            $today_transfer_in = $data['TODAY_TRANSFER_IN'];
                        }else{
                            $today_transfer_in = 0;
                        }
                        $total_today_transfer_in += $today_transfer_in;
                        echo $today_transfer_in;
                    ?>
                </td>
                <td width="100"><?=!empty($data['ISSUE_TILL_LAST_DAY']) ? $data['ISSUE_TILL_LAST_DAY']: 0;?></td>
                <td width="90">
                    <?php 
                        if(!empty($data['ISSUE_TODAY'])){
                            $issue_today = $data['ISSUE_TODAY'];
                        }else{
                            $issue_today = 0;
                        }
                        $total_issue_today += $issue_today;
                        echo $issue_today;
                        
                    ?>
                </td>
                <td width="90"><?=(!empty($data['ISSUE_TILL_LAST_DAY']) ? $data['ISSUE_TILL_LAST_DAY']: 0) +  (!empty($data['ISSUE_TODAY']) ? $data['ISSUE_TODAY']: 0);?></td>
                <td width="90"><?=!empty($data['ISSUE_THIS_MONTH']) ? $data['ISSUE_THIS_MONTH']: 0;?></td>

                <td width="90">
                    <?php 
                        if(!empty($data['TODAY_RECEIVE_RETURN'])){
                            $today_receive_return = $data['TODAY_RECEIVE_RETURN'];
                        }else{
                            $today_receive_return = 0;
                        }
                        $total_today_receive_return += $today_receive_return;
                        echo $today_receive_return;
                        
                    ?>
                </td>
                <td width="90"><?=!empty($data['THIS_MONTH_RECEIVE_RETURN']) ? $data['THIS_MONTH_RECEIVE_RETURN']: 0;?></td>
                <td width="100">
                    <?php 
                        if(!empty($data['TODAY_TRANSFER_OUT'])){
                            $today_transfer_out = $data['TODAY_TRANSFER_OUT'];
                        }else{
                            $today_transfer_out = 0;
                        }
                        $total_today_transfer_out += $today_transfer_out;
                        echo $today_transfer_out;
                        
                    ?>
                </td>
                <td width="100"><?=!empty($data['CLOSSING_BALANCE']) ? round($data['CLOSSING_BALANCE'], 2): 0;?></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="100"><?php echo !empty($txt_excange_rate) ? $txt_excange_rate * $data['RATE_USD'] : $data['RATE_BDT']; ?></td>
                <td width="100"><?php echo !empty($txt_excange_rate) ? $txt_excange_rate * $data['AMOUNT_USD'] : $data['BDT_AMOUNT']; ?></td>
                <td width="100"><?=$data['RATE_USD'];?></td>
                <td width="100"><?=$data['AMOUNT_USD'];?></td>
                <td width="100"><?=$data['QUALITY_STATUS'];?></td>
                <td width="130"><?=$data['STORE_NAME'];?></td>
                <td width="100"><?=$data['FLOOR_NAME'];?></td>
                <td width="100"><?=$data['ROOM'];?></td>
                <td width="100"><?=$data['RACK'];?></td>
                <td width="100"><?//=$data['SELF'];?></td>
                <td width="100"><?//=$data['BIN_BOX'];?></td>
            </tr>
            <?php
                $index +=1;  
                }
             ?>
        </tbody>
    </table>

    <!-- Report summery goes here -->
    <table width="1200" border="1" style="font:'Arial Narrow'; margin-top: 30px; margin-bottom: 30px;" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="report_summery">
        <tbody>
            <tr>
                <td>
                    <table width="500" border="1" style="font:'Arial Narrow';" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
                        <thead>
                            <tr>
                                <th colspan="4">Lot Summery</th>
                            </tr>
                            <tr>
                                <th ><?=count($previous_lot_arr);?></th>
                                <th >Days Received</th>
                                <th >Days Closed</th>
                                <th >Present LOT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td >667</td>
                                <td >3</td>
                                <td >2</td>
                                <td >668</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td>
                    <table width="600" border="1" style="font:'Arial Narrow';" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
                        <thead>
                            <tr>
                                <th colspan="2">Stock Summery</th>
                            </tr>
                            <tr>
                                <th >Description</th>
                                <th >Qty (KG)</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Opening Balance</td> 					 
                            <td><?=$total_openning_balance;?></td>
                        </tr> 
                        <tr>
                            <td> Day's Yarn Receive</td> 					 
                            <td><?=$total_receive_today;?></td>
                        </tr> 
                        <tr>
                            <td>Day's Yarn Issue Return	</td> 					 
                            <td><?=$total_today_issue_return;?></td>
                        </tr> 			 
                        <tr>
                            <td>Day's Yarn Transfer In</td> 					 
                            <td><?=$total_today_transfer_in;?></td>
                        </tr> 				  
                        <tr bgcolor="#CCCCCC" style="font-weight:bold;">
                            <td>Total Receive</td> 					 
                            <td>
                                <?php 
                                    $total_received = $total_openning_balance+$total_receive_today+$total_today_issue_return+$total_today_transfer_in;
                                    echo $total_received;
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Day's Yarn Consume</td> 					 
                            <td><?=$total_issue_today;?></td>
                        </tr>					  
                        <tr>
                            <td>Day's Yarn Receive Return</td> 					 
                            <td><?=$total_today_receive_return;?></td>
                        </tr>					  
                        <tr>
                            <td>Day's Yarn Transfer Out		</td> 					 
                            <td><?=$total_today_transfer_out;?></td>
                        </tr>
                        <tr bgcolor="#CCCCCC" style="font-weight:bold;">
                            <td>Total Issue	</td> 					 
                            <td>
                                <?php 
                                    $total_issued = $total_issue_today+$total_today_receive_return+$total_today_transfer_out;
                                    echo $total_issued;
                                ?>
                            </td>
                        </tr>
                        <tr bgcolor="#CCCCCC" style="font-weight:bold;">
                            <td>Closing Balance	</td> 					 
                            <td><?=$total_received-$total_issued;?></td>
                        </tr>
                        <tr>
                            <td>Auko-Tex L/C In-Transit		</td> 					 
                            <td><?=$total_pi_receive_balance;?></td>
                        </tr>				  
                        <tr>
                            <td>Yasin Knit L/C In-Transit</td> 					 
                            <td>---</td>
                        </tr>				 
                        <tr bgcolor="#CCCCCC" style="font-weight:bold;">
                            <td>Total In-Transit</td> 					 
                            <td><?=$total_pi_receive_balance;?></td>
                        </tr>				 
                        <tr bgcolor="#CCCCCC" style="font-weight:bold;">
                            <td>Total Balance (Closing+In-Transit)</td> 					 
                            <td>1,259,529.48 </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    
</div>


<?php } ?>

