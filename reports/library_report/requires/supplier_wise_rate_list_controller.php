<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action == "load_drop_down_item_group"){
  $queryForItemGroup = "select id, item_name from lib_item_group where item_category='$data' and status_active = 1 and is_deleted = 0 order by item_name";

  echo  create_drop_down( "cbo_item_group", 110, $queryForItemGroup,'id,item_name', 1, "-- Select Item Group --", 0, "" );

  exit;
}

if($action == "load_drop_down_supplier")
{
	$categoryWisePrtyType = array(
			"3" => "(9)",
			"4" => "(1,4,5)",
			"11" => "(8)",
			"57" => "(23)",
			"23" => "(3)"
	);
	$queryForsuppliersForItemCategory = "select id, supplier_name from lib_supplier where id in (select supplier_id from lib_supplier_party_type where party_type in $categoryWisePrtyType[$data]) and status_active=1 and is_deleted=0 order by supplier_name asc";
	echo create_drop_down( "cbo_supplier_name", 120,$queryForsuppliersForItemCategory,"id,supplier_name", '1', '---- Select ----', "" );
	exit;
}

if($action == "openpopup_item_description__")
{
	echo load_html_head_contents("Item Description Select","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	?>
    <script>
		function js_set_value( str) {
			$('#itemdescription').val(str);
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;">
        <input type="hidden" id="itemdescription" name="itemdescription"/>
        <?
        	$sql_tgroup=sql_select( "SELECT id, item_description,item_code from lib_item_details where item_category_id='$item_category' and is_deleted = 0 order by item_description");
         ?>
        <table width="420" cellspacing="0" class="rpt_table" border="0" rules="all">
            <thead>
            	<th width="40">SL</th><th width="195">Item Description</th><th width="160">Item Code</th>
            </thead>
        </table>
        <div style="width:420px; overflow-y:scroll; max-height:340px;" >
        <table width="400" cellspacing="0" class="rpt_table" border="0" rules="all" id="item_table">
            <tbody>
				<?
                $i=1;
                foreach($sql_tgroup as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$str="";
					$str=$row[csf('id')].'__'.$row[csf('item_description')].'__'.$row[csf('item_code')];
					?>
					<tr id="search<? echo $i;?>" class="itemdata" onClick="js_set_value('<? echo $str; ?>')" bgcolor="<? echo $bgcolor; ?>">
						<td width="40"><? echo $i; ?></td>
						<td width="220"><? echo $row[csf('item_description')]; ?>
                        <input type="hidden" name="txtdescription_<? echo $i; ?>" id="txtdescription_<? echo $i; ?>" value="<? echo $str ?>"/>
                        </td>
                        <td width="160"><? echo $row[csf('item_code')]; ?></td>
					</tr>
					<?
					$i++;
                }
                ?>
            </tbody>
        </table>
        </div>
        <table width="420" cellspacing="0" cellpadding="0" style="border:none" align="center">
        <tr>
            <td align="center" height="30" valign="bottom">
                <div style="width:100%">
                    <div style="width:50%; float:left" align="left">
                    	<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                    </div>
                    <div style="width:50%; float:left" align="center">
                    	<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                    </div>
                </div>
            </td>
        </tr>
	</table>
    </div>
    </body>
	<script>setFilterGrid('item_table',-1);</script>
	</html>
	<?
	exit();
}

if($action=="openpopup_item_description")
{
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    ?>
    <script>

        var selected_id = new Array;
        var selected_name = new Array;

        function check_all_data()
        {
            var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
            tbl_row_count = tbl_row_count - 0;
            for( var i = 1; i <= tbl_row_count; i++ ) {
                var onclickString = $('#tr_' + i).attr('onclick');
                var paramArr = onclickString.split("'");
                var functionParam = paramArr[1];
                js_set_value( functionParam );

            }
        }

        function toggle( x, origColor )
        {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }

        function js_set_value( strCon )
        {
            var splitSTR = strCon.split("_");
            var str = splitSTR[0];
            var selectID = splitSTR[1];
            var selectDESC = splitSTR[2];
            //$('#txt_individual_id' + str).val(splitSTR[1]);
            //$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

            toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );

            if( jQuery.inArray( selectID, selected_id ) == -1 ) {
                selected_id.push( selectID );
                selected_name.push( selectDESC );
            }
            else
            {
                for( var i = 0; i < selected_id.length; i++ ) {
                    if( selected_id[i] == selectID ) break;
                }
                selected_id.splice( i, 1 );
                selected_name.splice( i, 1 );
            }
            var id = ''; var name = ''; var job = '';
            for( var i = 0; i < selected_id.length; i++ )
            {
                id += selected_id[i] + '**';
                name += selected_name[i] + '**';
            }
            id      = id.substr( 0, id.length - 2 );
            name    = name.substr( 0, name.length - 2 );

            $id = $('#txt_selected_id').val( id );
            $item= $('#txt_selected').val( name );
        }
    </script>
    <?
    extract($_REQUEST);
    if ($item_group == 0) $item_group_cond=''; else $item_group_cond= "and item_group_id=$item_group";

    $sql = "SELECT distinct id, item_description, item_code from lib_item_details where item_category_id=$item_category and is_deleted = 0 $item_group_cond order by item_description";
    //echo $sql;//die;
    echo create_list_view("list_view", "Item Description,Item Code","195,160","420","310",0, $sql , "js_set_value", "id,item_description", "", 1, "0", $arr, "item_description,item_code", "","setFilterGrid('list_view',-1)","0","",1) ;
    echo "<input type='hidden' id='txt_selected_id' />";
    echo "<input type='hidden' id='txt_selected' />";
    exit();
}

if($action == "openpopup_item_code")
{
	echo load_html_head_contents("Item Code Select","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	?>
    <script>
		function js_set_value( str) {
			$('#itemdescription').val(str);
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;">
        <input type="hidden" id="itemdescription" name="itemdescription"/>
        <? $sql_tgroup=sql_select( "select id, item_description,item_code from lib_item_details where item_category_id='$item_category' and is_deleted = 0 order by item_description"); ?>
        <table width="420" cellspacing="0" class="rpt_table" border="0" rules="all">
            <thead>
            	<th width="40">SL</th><th width="195">Item Description</th><th width="160">Item Code</th>
            </thead>
        </table>
        <div style="width:420px; overflow-y:scroll; max-height:340px;" >
        <table width="400" cellspacing="0" class="rpt_table" border="0" rules="all" id="item_table">
            <tbody>
				<?
                $i=1;
                foreach($sql_tgroup as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$str="";
					$str=$row[csf('id')].'__'.$row[csf('item_code')];
					?>
					<tr id="search<? echo $i;?>" class="itemdata" onClick="js_set_value('<? echo $str; ?>')" bgcolor="<? echo $bgcolor; ?>">
						<td width="40"><? echo $i; ?></td>
						<td width="220"><? echo $row[csf('item_description')]; ?>
                        <input type="hidden" name="txtdescription_<? echo $i; ?>" id="txtdescription_<? echo $i; ?>" value="<? echo $str ?>"/>
                        </td>
                        <td width="160"><? echo $row[csf('item_code')]; ?></td>
					</tr>
					<?
					$i++;
                }
                ?>
            </tbody>
        </table>
        </div>
        <table width="420" cellspacing="0" cellpadding="0" style="border:none" align="center">
        <tr>
            <td align="center" height="30" valign="bottom">
                <div style="width:100%">
                    <!-- <div style="width:50%; float:left" align="left">
                    	<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                    </div> -->
                    <div style="width:50%; float:left" align="center">
                    	<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                    </div>
                </div>
            </td>
        </tr>
	</table>
    </div>
    </body>
	<script>setFilterGrid('item_table',-1);</script>
	<!--<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>-->
	</html>
	<?
	exit();
}

if ($action=="report_generate")
{
	extract($_REQUEST);
	$cbo_item_category=str_replace("'","",$cbo_item_category);
	$item_group=str_replace("'","",$cbo_item_group);
	$supplier=str_replace("'","",$cbo_supplier_name);
	$supplier_code=str_replace("'","",$supplier_code);
	$search_item_description=str_replace("'","",$search_item_description);
    $search_item_description=explode("**",$search_item_description);
    $search_item_desc="";
    foreach ($search_item_description as $key => $value)
    {
        if ($search_item_desc=="")
        {
            $search_item_desc.= $value;
        }
        else
        {
            $search_item_desc.= "','".$value;
        }
    }

    //$search_item_desc = "'" . implode("','", $search_item_description) . "'";
	$item_code=str_replace("'","",$item_code);
	$rate=str_replace("'","",$rate);
	$insert_date=change_date_format(str_replace("'","",$insert_date), "yyyy-mm-dd", "-",1);
	//$effective_from=change_date_format(str_replace("'","",$effective_from), "yyyy-mm-dd", "-",1);
	if($cbo_item_category != 0) $category_con = "and a.item_category_id=$cbo_item_category"; else $category_con = " ";
	if($item_group != 0) $group_con = "and a.item_group_id=$item_group"; else $group_con = " ";
	if($search_item_desc != "") $descrip_con = " and a.item_description in ('$search_item_desc')";
	if($item_code != '') $item_con = "and a.item_code like '%$item_code%'"; else $item_con = " ";
	if($supplier_code != '') $supp_code_con = "and c.supplier_code like '%$supplier_code%'"; else $supp_code_con = " ";
	if($rate != '') $rate_con = "and c.rate like '%$rate%'"; else $rate_con = " ";
	if($supplier != 0) $supplier_con = "and c.supplier_id=$supplier"; else $supplier_con = " ";
	if($insert_date != '') $insert_con = "and TRUNC(c.insert_date) = '$insert_date'" ; else $insert_con = " ";
	//if($effective_from != '') $effective_con = "and TRUNC(c.effective_from) >= '$effective_from'" ; else $effective_con = " ";
    if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
    {
        $start_date=(str_replace("'","",$txt_date_from));
        $end_date=(str_replace("'","",$txt_date_to));
        $effective_con=" and c.effective_from between '$start_date' and '$end_date'";
    }
	$supplier = return_library_array("SELECT id,supplier_name from  lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
    $sql="SELECT a.id, a.item_description, a.item_code, a.item_group_id, a.item_category_id,  b.order_uom, b.trim_uom, b.item_name, b.conversion_factor, c.id as rate_table_id, c.supplier_id, c.rate, c.effective_from, c.supplier_code, c.remarks, c.insert_date from lib_item_details a join  lib_item_group b on a.item_group_id = b.id join lib_supplier_wise_rate c on a.id=c.item_details_id where a.is_deleted=0 $category_con $group_con $descrip_con $item_con $supp_code_con $rate_con $supplier_con $insert_con  $effective_con order by c.supplier_id, c.insert_date";
    //echo $sql;
	$report_data = sql_select($sql);
    foreach ($report_data as $row) {
        $report_arr[$row[csf('id')]]['id'] = $row[csf('id')];
        $report_arr[$row[csf('id')]]['item_category'] = $row[csf('item_category_id')];
        $report_arr[$row[csf('id')]]['item_name'] = $row[csf('item_name')];
        $report_arr[$row[csf('id')]]['item_description'] = $row[csf('item_description')];
        $report_arr[$row[csf('id')]]['item_code'] = $row[csf('item_code')];
        $report_arr[$row[csf('id')]]['order_uom'] = $row[csf('order_uom')];
        $report_arr[$row[csf('id')]]['trim_uom'] = $row[csf('trim_uom')];
        $report_arr[$row[csf('id')]]['conversion_factor'] = $row[csf('conversion_factor')];
        $report_arr[$row[csf('id')]]['supplier_rate'][$row[csf('rate_table_id')]]['supplier_code'] = $row[csf('supplier_code')];
        $report_arr[$row[csf('id')]]['supplier_rate'][$row[csf('rate_table_id')]]['supplier_id'] = $row[csf('supplier_id')];
        $report_arr[$row[csf('id')]]['supplier_rate'][$row[csf('rate_table_id')]]['rate'] = $row[csf('rate')];
        $report_arr[$row[csf('id')]]['supplier_rate'][$row[csf('rate_table_id')]]['insert_date'] = $row[csf('insert_date')];
        $report_arr[$row[csf('id')]]['supplier_rate'][$row[csf('rate_table_id')]]['effective_from'] = $row[csf('effective_from')];
        $report_arr[$row[csf('id')]]['supplier_rate'][$row[csf('rate_table_id')]]['remarks'] = $row[csf('remarks')];
    }
    /*echo '<pre>';
    print_r($report_arr);*/
    ob_start();
	?>
	<div style="width:1322px;">
		<table cellspacing="0" width="1320"  border="1" rules="all" class="rpt_table">
            <thead>
                <tr>
                    <th width="40">SL</th>
                    <th width="40">System ID</th>
                    <th width="60">Item Category</th>
                    <th width="80" align="center">Item Group</th>
                    <th width="60" align="center">Item Description</th>
                    <th width="75" align="center">Item Code</th>
                    <th width="50" align="center">Order Uom</th>
                    <th width="50" align="center">Cons Uom</th>
                    <th width="50" align="center">Ratio Uom</th>
                    <th width="75" align="center">Supplier Code</th>
                    <th width="100" align="center">Supplier</th>
                    <th width="50" align="center">Rate (&dollar;)</th>
                    <th width="60" align="center">Insert Date</th>
                    <th width="60" align="center">Effective Date</th>
                    <th width="100" align="center">Remarks</th>
                </tr>
            </thead>
        </table>
        <div id="scroll_body" style="width:1322px; max-height:400px; overflow-y:scroll">
        	<table cellspacing="0" width="1320"  border="1" rules="all" class="rpt_table">
                <tbody id="table_body">
        		<?
        			$i =1;
        			foreach ($report_arr as $data) {
                        $k=1;
                        $rowspan = count($data['supplier_rate']);
        				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
        			?>
		        		<tr>
                            <? if($k==1){ ?>
                            <td style="vertical-align : middle;text-align:center;" width="40" rowspan="<? echo $rowspan ?>" valign="center"><? echo $i; ?></td>
                            <td style="vertical-align : middle;text-align:center;" width="40" rowspan="<? echo $rowspan ?>"><? echo $data["id"]; ?></td>
                            <td style="vertical-align : middle;text-align:center;" width="60" rowspan="<? echo $rowspan ?>"><? echo $item_category[$data["item_category"]]; ?></td>
                            <td style="vertical-align : middle;text-align:center;" width="80" rowspan="<? echo $rowspan ?>"><? echo $data["item_name"]; ?></td>
                            <td style="vertical-align : middle;text-align:center;" width="60" rowspan="<? echo $rowspan ?>"><? echo $data["item_description"]; ?></td>
                            <td style="vertical-align : middle;text-align:center;" width="75" rowspan="<? echo $rowspan ?>"><? echo $data["item_code"]; ?></td>
                            <td style="vertical-align : middle;text-align:center;" width="50" rowspan="<? echo $rowspan ?>"><? echo $unit_of_measurement[$data["order_uom"]]; ?></td>
                            <td style="vertical-align : middle;text-align:center;" width="50" rowspan="<? echo $rowspan ?>"><? echo $unit_of_measurement[$data["trim_uom"]]; ?></td>
                            <td style="vertical-align : middle;text-align:center;" width="50" rowspan="<? echo $rowspan ?>"><? echo $data["conversion_factor"]; ?></td>
                            <? } ?>
                            <? foreach ($data['supplier_rate']as $value) { ?>
                                <? if($k!=1) echo '<tr>';  ?>
                                <td width="75"><? echo $value['supplier_code']; ?></td>
                                <td width="100"><? echo $supplier[$value['supplier_id']]; ?></td>
                                <td width="50"><? echo $value['rate']; ?></td>
                                <td width="60"><? echo change_date_format($value['insert_date']); ?></td>
                                <td width="60"><? echo change_date_format($value['effective_from']); ?></td>
                                <td width="100"><? echo $value['remarks']; ?></td>
                                <? if($k!=1) echo '</tr>';  ?>
                            <? $k++; } ?>
                        </tr>
        			<?
        			$i++;
        			}
        		?>
                </tbody>
        	</table>
        </div>
	</div>
	<?
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename";
    exit();
}
?>
<!-- <tr bgcolor="<? echo $bgcolor ; ?>">
<td width="40"><? echo $i; ?></td>
<td width="40"><? echo $row[csf("id")]; ?></td>
<td width="100"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
<td width="80"><p><? echo $row[csf("item_name")]; ?></p></td>
<td width="100"><p><? echo $row[csf("item_description")]; ?></p></td>
<td width="75"><p><? echo $row[csf("item_code")]; ?></p></td>
<td width="75"><p><? echo $row[csf("supplier_code")]; ?></p></td>
<td width="50"><p><? echo $unit_of_measurement[$row[csf("order_uom")]]; ?></p></td>
<td width="50"><p><? echo $unit_of_measurement[$row[csf("trim_uom")]]; ?></p></td>
<td width="50"><p><? echo $row[csf("conversion_factor")]; ?></p></td>
<td width="100"><p><? echo $supplier[$row[csf("supplier_id")]]; ?></p></td>
<td width="50"><p><? echo $row[csf("rate")]; ?></p></td>
<td width="60"><p><? echo change_date_format($row[csf("insert_date")]); ?></p></td>
<td width="60"><p><? echo change_date_format($row[csf("effective_from")]); ?></p></td>
<td width="100"><p><? echo $row[csf("remarks")]; ?></p></td>
</tr> -->