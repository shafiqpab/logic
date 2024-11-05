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

  echo  create_drop_down( "cbo_item_group", 155, $queryForItemGroup,'id,item_name', 1, "-- Select Item Group --", 0, "" );

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
	$queryForsuppliersForItemCategory = "SELECT id, supplier_name from lib_supplier where id in (select supplier_id from lib_supplier_party_type where party_type in $categoryWisePrtyType[$data]) and status_active=1 and is_deleted=0 order by supplier_name asc";
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
        <? $sql_tgroup=sql_select( "SELECT id, item_description,item_code from lib_item_details where item_category_id='$item_category' and is_deleted = 0 order by item_description"); ?>
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
	$effective_from=change_date_format(str_replace("'","",$effective_from), "yyyy-mm-dd", "-",1);
	if($cbo_item_category != 0) $category_con = "and a.item_category_id=$cbo_item_category"; else $category_con = " ";
	if($item_group != 0) $group_con = "and a.item_group_id=$item_group"; else $group_con = " ";
	if($search_item_desc != "") $descrip_con = " and a.item_description in ('$search_item_desc')";
	if($item_code != '') $item_con = "and a.item_code like '%$item_code%'"; else $item_con = " ";
	if($supplier_code != '') $supp_code_con = "and c.supplier_code like '%$supplier_code%'"; else $supp_code_con = " ";
	if($rate != '') $rate_con = "and c.rate like '%$rate%'"; else $rate_con = " ";
	if($supplier != 0) $supplier_con = "and c.supplier_id=$supplier"; else $supplier_con = " ";
	if($insert_date != '') $insert_con = "and TRUNC(c.insert_date) = '$insert_date'" ; else $insert_con = " ";
	if($effective_from != '') $effective_con = "and TRUNC(c.effective_from) >= '$effective_from'" ; else $effective_con = " ";
	$supplier = return_library_array("SELECT id,supplier_name from  lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
    
    $sql="SELECT a.id, a.item_description,  a.item_group_id, a.item_category_id, b.item_name, b.order_uom, c.rate, c.supplier_id, c.effective_from,c.insert_date from lib_item_details a join  lib_item_group b on a.item_group_id = b.id join lib_supplier_wise_rate c on a.id=c.item_details_id where a.is_deleted=0 $category_con $group_con $descrip_con $item_con  $rate_con  $insert_con  $effective_con order by c.insert_date";
    
	$report_data = sql_select($sql);
        foreach ($report_data as $key => $value) {
            $comparative_stat_array[$value[csf("item_category_id")]][$value[csf("item_group_id")]][$value[csf("item_description")]]["item_category_id"]=$value[csf("item_category_id")];
            $comparative_stat_array[$value[csf("item_category_id")]][$value[csf("item_group_id")]][$value[csf("item_description")]]["order_uom"]=$value[csf("order_uom")];
            $comparative_stat_array[$value[csf("item_category_id")]][$value[csf("item_group_id")]][$value[csf("item_description")]]["item_group_id"]=$value[csf("item_group_id")];
            $comparative_stat_array[$value[csf("item_category_id")]][$value[csf("item_group_id")]][$value[csf("item_description")]]["item_name"]=$value[csf("item_name")];
            $comparative_stat_array[$value[csf("item_category_id")]][$value[csf("item_group_id")]][$value[csf("item_description")]]["item_description"]=$value[csf("item_description")];
            $comparative_stat_array[$value[csf("item_category_id")]][$value[csf("item_group_id")]][$value[csf("item_description")]]["supplier_id"]=$value[csf("supplier_id")];
            $comparative_stat_array[$value[csf("item_category_id")]][$value[csf("item_group_id")]][$value[csf("item_description")]]["rate"]=$value[csf("rate")];

            if($supplier_ids[$value[csf('supplier_id')]] != $value[csf('supplier_id')]){
                $supplier_ids[$value[csf('supplier_id')]] = $value[csf('supplier_id')];                               
            }
            $supplier_wise_rate[$value[csf('item_description')]][$value[csf('supplier_id')]]=$value[csf('rate')];
           
        }
         //echo "<pre>";
         //print_r($comparative_stat_array);die;
        $colspan = count($supplier_ids);
        $sup_width =(80*$colspan);
        $table_width = 370+$sup_width;
        $div_width = $table_width+20;
        if (count ($report_data)<1)
        {
            echo "<div style='width:800px;text-align:center;color:red;'>Data is not found</div>";die;
        }
        //print_r($colspan);die;
    ob_start();
	?>
	<div style="width:<? echo $div_width."px";?>; margin: 0 auto; overflow: hidden; ">
		<table cellspacing="0" width="<? echo $table_width; ?>"  border="1" rules="all" class="rpt_table" align="left">
            <thead>
                <tr>
                    <th width="40">SL</th>
                    <th width="100">Item Category</th>
                    <th width="80" align="center">Item Group</th>
                    <th width="100" align="center">Item Description</th>                                        
                    <th width="50" align="center">Order Uom</th>                    
                    <th width="<? echo $sup_width; ?>" align="center" colspan="<? echo $colspan;?>">Supplier Name and Rate</th>               
                </tr>
                <tr>
                    <th width="40"></th>
                    <th width="100"></th>
                    <th width="80" align="center"></th>
                    <th width="100" align="center"></th>                                        
                    <th width="50" align="center"></th> 
                    <?
                        foreach ($supplier_ids as $key => $value) {
                           
                        
                    ?>                   
                    <th width="80" align="center"><? echo $supplier[$value]; ?></th>  
                    <?
                    }
                     ?>
                </tr>              
            </thead>
        </table>
        <div id="scroll_body" style="width: <? echo $div_width."px"; ?>; max-height:400px; overflow-y:scroll">
        	<table cellspacing="0" width="<? echo $table_width; ?>"  border="1" rules="all" class="rpt_table" align="left">
                <tbody id="table_body">
                    
        		<?
        			$i =1;
        			foreach ($comparative_stat_array as $item_key => $item_value) {
                        foreach ($item_value as $group_key => $group_value) {
                            foreach ($group_value as $description_key => $row) { 

                            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
        			?>

		        		<tr bgcolor="<? echo $bgcolor ; ?>">
			            	<td width="40"><? echo $i; ?></td>
			                <td width="100"><? echo $item_category[$row["item_category_id"]]; ?></td>
			                <td width="80"><p><? echo $row["item_name"]; ?></p></td>
			                <td width="100"><p><? echo $row["item_description"]; ?></p></td>               			
                            <td width="50"><p><? echo $unit_of_measurement[$row["order_uom"]]; ?></p></td>  
                            <?                            
                            foreach ($supplier_ids as $key => $val_sup) {                               
                            ?>
                            <td width="80"><p><? echo  $supplier_wise_rate[$row["item_description"]][$key]; ?></p></td>  
                            <?
                            }
                            ?>                
			              
            			</tr>

        			<?
        			$i++;
        			                                             
                            }
                        }
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