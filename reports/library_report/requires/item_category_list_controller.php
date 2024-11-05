<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
 
$user_arr = return_library_array("select id,user_full_name from user_passwd where valid=1","id","user_full_name");

if($action == "load_drop_down_group")
{
    echo create_drop_down( "cbo_item_group", 140, "select a.item_name,a.id from lib_item_group a where a.item_category = $data and a.status_active = 1 and a.is_deleted  = 0 group by a.item_name, a.id order by a.id","id,item_name", 1, "-- Select --", $selected, "" );
    exit();
}
if($action=="report_formate_setting")
{
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=1 and report_id=196 and is_deleted=0 and status_active=1");
    echo "print_report_button_setting('$print_report_format');\n";
    exit();
}

if ($action=="order_popup")
{
	  echo load_html_head_contents("Item List", "../../../", 1, 1,$unicode,1,'');	
	  extract($_REQUEST);
    ?>
    <script>   
        var selected_id = new Array;
        var selected_name = new Array;
        
        function check_all_data() {
            var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
            tbl_row_count = tbl_row_count - 0;
            for( var i = 1; i <= tbl_row_count; i++ ) {
                var onclickString = $('#tr_' + i).attr('onclick');
                var paramArr = onclickString.split("'");
                var functionParam = paramArr[1];
                js_set_value( functionParam );            
            }
        }
        
        function toggle( x, origColor ) {
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

            toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
            
            if( jQuery.inArray( selectID, selected_id ) == -1 ) {
                selected_id.push( selectID );
                selected_name.push( selectDESC );					
            }
            else {
                for( var i = 0; i < selected_id.length; i++ ) {
                    if( selected_id[i] == selectID ) break;
                }
                selected_id.splice( i, 1 );
                selected_name.splice( i, 1 ); 
            }
            var id = ''; var name = ''; var job = '';
            for( var i = 0; i < selected_id.length; i++ ) {
                id += selected_id[i] + ',';
                name += selected_name[i] + ','; 
            }
            id 		= id.substr( 0, id.length - 1 );
            name 	= name.substr( 0, name.length - 1 ); 
            
            $('#item_id').val( id );        
        }
    </script>
    </head>	
    <body>
    <div align="center" style="width:930px" >   
    <fieldset style="width:930px"> 
        <form name="product_popup"  id="product_popup">
            <?
            extract($_REQUEST);
			if($category) $cat_cond=" and item_category=$category";
            $sql="select id, item_category, item_group_code, item_name, trim_type, order_uom, trim_uom, conversion_factor, cal_parameter from lib_item_group where is_deleted=0 $cat_cond";
			//echo $sql;
            $arr=array (0=>$item_category,3=>$trim_type,4=>$unit_of_measurement,5=>$unit_of_measurement,7=>$cal_parameter);
            $sql;
            
            echo create_list_view("list_view", "Item Catagory,Group Code,Item Group Name,Item Type,Order UOM,Cons. UOM,Conv. Factor,Cal Parameter","150,70,200,100,50,50,50","900","320",0, $sql ,"js_set_value", "id", "'load_php_popup_to_form'", 1, "item_category,0,0,trim_type,order_uom,trim_uom,0,cal_parameter", $arr, "item_category,item_group_code,item_name,trim_type,order_uom,trim_uom,conversion_factor,cal_parameter", "","setFilterGrid('list_view',-1)","0","",1);	
            echo "<input type='hidden' id='item_id' />";
			
			/*$arr=array (0=>$item_category,3=>$trim_type,4=>$unit_of_measurement,5=>$unit_of_measurement,7=>$cal_parameter);
            echo create_list_view ( "list_view", "Item Catagory,Group Code,Item Group Name,Item Type,Order UOM,Cons. UOM,Conv. Factor,Cal Parameter", "150,100,200,80,50,50,50","900","320",0, $sql, "js_set_value", "id", "'load_php_popup_to_form'", 1, "item_category,0,0,trim_type,order_uom,trim_uom,0,cal_parameter", $arr , "item_category,item_group_code,item_name,trim_type,order_uom,trim_uom,conversion_factor,cal_parameter", "item_creation_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0' ); */                     
            exit();
           ?>        
        </form>
    </fieldset>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>                                  
    <?
}

if($action=="item_popup")
{
    echo load_html_head_contents("Item Description Info", "../../../", 1, 1,$unicode,'','');
    extract($_REQUEST);
    //$store_item_cat=return_field_value("item_category_id","lib_store_location","company_id=$company and id=$store_id","item_category_id")
    ?>
    <script>

    function js_set_value(data)
    {
        $('#hidden_item').val(data);
        parent.emailwindow.hide();
    }

    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
        <fieldset style="width:780px;">
            <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                <thead>
                    <th width="170" class="must_entry_caption">Item Category</th>
                    <th width="140">Item Group</th>
                    <th width="120">Item Code</th>
                    <th width="160">Item Description</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:80px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td>
                        <?
                            echo create_drop_down( "cbo_item_category_id", 170, $item_category,"", 1, "-- Select --", $selected, "load_drop_down( 'item_category_list_controller', this.value, 'load_drop_down_group','group_td');","","","","");
                        ?>
                        </td>
                        <td align="center" id="group_td">
                            <?
                                echo create_drop_down("cbo_item_group",140,$blank_array,"",1,"-- Select --",$selected, "" );
                            ?>
                        </td>
                        <td align="center">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_item_code" id="txt_item_code" />
                        </td>
                        <td align="center">
                            <input type="text" style="width: 160px" class="text_boxes" name="txt_item_description" id="txt_item_description" />
                        </td>
                        <td align="center">
                            <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $cbo_company_name; ?>'+'**'+document.getElementById('cbo_item_category_id').value+'**'+document.getElementById('txt_item_description').value+'**'+document.getElementById('txt_item_code').value+'**'+document.getElementById('cbo_item_group').value, 'create_item_popup_list_view', 'search_div', 'item_category_list_controller','setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
        <div style="margin-top:15px" id="search_div"></div>
    </form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action=="create_item_popup_list_view")
{
    echo load_html_head_contents("Item Creation popup", "../../../", 1, 1,'','1','');
    extract($_REQUEST);
    list($company_name,$item_category_id,$item_description,$item_code,$item_group)=explode('**',$data);
    $search_con ="";
    $item_description_cond="";
    $item_description_lower=strtolower($item_description);
    if($item_description != "") {$item_description_cond =" and lower(a.item_description) like ('%$item_description_lower%')";}

    if ($company_name!=0) $company=" and a.company_id='$company_name'"; 
    if ($item_category_id!=0) $item_category_list=" and a.item_category_id='$item_category_id'";    
    if($item_code!=""){$search_con .= " and a.item_code like('%$item_code')";}
    if($item_group !=0){$search_con .= " and a.item_group_id = '$item_group'";}

    $entry_cond="";
    if(str_replace("'","",$item_category_id)==4) $entry_cond="and a.entry_form=20";

    $sql="SELECT a.id as ID, a.item_account as ITEM_ACCOUNT, a.sub_group_name as SUB_GROUP_NAME, a.item_category_id as ITEM_CATEGORY_ID, a.item_description as ITEM_DESCRIPTION, a.item_size as ITEM_SIZE, a.item_code as ITEM_CODE, a.item_group_id as ITEM_GROUP_ID, a.unit_of_measure as UNIT_OF_MEASURE, a.current_stock as CURRENT_STOCK, a.status_active as status_active, b.item_name as ITEM_NAME, a.order_uom as ORDER_UOM, a.unit_of_measure as CONS_UOM
    from lib_item_group b, product_details_master a, inv_transaction c  
    where a.item_group_id=b.id and a.id=c.prod_id and a.item_group_id>0 $company $search_con $item_category_list $entry_cond $item_description_cond and c.status_active=1 and a.status_active=1 
    group by a.id, a.item_account, a.sub_group_name, a.item_category_id, a.item_description, a.item_size, a.item_code, a.item_group_id, a.unit_of_measure, a.current_stock, a.status_active, b.item_name, a.order_uom";
    //echo $sql;
    $sql_res=sql_select($sql);
    
    ?>
    <div>
        <input type="hidden" id="hidden_item" />
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="90">Item Account</th>
                <th width="90">Item Category</th>
                <th width="150">Item Description</th>
                <th width="80">Item Code</th>
                <th width="80">Item Size</th>
                <th width="100">Item Group</th>
                <th width="60">Order UOM</th>
                <th>Product ID</th>
            </thead>
        </table>
     </div>
     <div style="width:800px; max-height:270px; overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="list_view">
            <?
            $i=1;
            foreach( $sql_res as $val )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                $item_description=$val["ITEM_DESCRIPTION"];
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $item_description; ?>');" >
                    <td width="40" align="center"><p><?php echo $i; ?></p></td>
                    <td width="90"><p><?php echo $val["ITEM_ACCOUNT"]; ?></p></td>
                    <td width="90"><p><?php echo $item_category[$val["ITEM_CATEGORY_ID"]]; ?></p></td>
                    <td width="150"><p><?php echo $val["ITEM_DESCRIPTION"]; ?></p></td>
                    <td width="80" align="center"><p><?php echo $val["ITEM_CODE"]; ?></p></td>
                    <td width="80" align="center"><p><?php echo $val["ITEM_SIZE"]; ?></p></td>
                    <td width="100"><p><?php echo $val["ITEM_NAME"]; ?></p></td>
                    <td width="60" align="center"><p><?php echo $unit_of_measurement[$val["ORDER_UOM"]]; ?></p></td>
                    <td align="center"><p><?php echo $prod_id; ?></p></td>
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

if ($action=="load_php_popup_to_form")
{
	$data=explode("_",$data);
   
	if ($data[1]!="blur") $data =" and id in($data[0])"; else $data =" and item_group_code like '$data[0]'";
    $nameArray=sql_select( "select id,item_name,item_category,item_group_code from  lib_item_group where status_active=1 $data" ); 
    if (count($nameArray)>0)
	{
		foreach ($nameArray as $inf)
		{
			$item_names .= $inf[csf("item_name")].",";         
            $item_name = rtrim($item_names, ",");
            $item_groupsId .= $inf[csf("id")].","; 
            $item_groupId = rtrim($item_groupsId, ",");
			echo "document.getElementById('txt_item_group').value 	= '".($item_name)."';\n";
			echo "document.getElementById('item_group_id').value  	= '".($item_groupId)."';\n"; 
		}
	}
	else
	{
		echo "document.getElementById('demo_message').innerHTML='Please Browse from Popup'";
	}
	exit();
}


if ($action=="report_generate")
{
	extract($_REQUEST);     
    $company_id = str_replace("'","",$cbo_company_name);
    $item_category_id = str_replace("'","",$cbo_item_category); 
    $item_group_id = str_replace("'","",$item_group_id);
    $item_description = str_replace("'","",$txt_item_description);
    $type = str_replace("'","",$type);
    //echo $type; die;
    $item_description_cond="";
    $item_description_lower=strtolower($item_description);
    if($item_description != "") {$item_description_cond =" and lower(a.item_description) like ('%$item_description_lower%')";}

    $where = '';
    if ($company_id!=0) {$where .= "and a.company_id = $company_id*";}
    if ($item_category_id!=0) {$where .= "a.item_category_id in($item_category_id)*";}
    if ($item_group_id!='') {$where .= "a.item_group_id in($item_group_id)*";}
    
    $where = str_replace("*"," and ",$where);
    $whereCon = substr($where,0,-4); 

    $origin_arr=return_library_array( "select country_name,id from lib_country where is_deleted=0  and status_active=1 order by country_name",'id','country_name');
    //echo "test"; die;
    if ($type==1) 
    {
        $sql_add = "SELECT id,company_name,email,city FROM lib_company where is_deleted=0 and status_active=1 and id = $company_id"; 
        $sql_add_arr = sql_select($sql_add);
        foreach ($sql_add_arr as $row) {
            $companyInfo[$row[csf('id')]]['name']  = $row[csf('company_name')];
            $companyInfo[$row[csf('id')]]['email'] = $row[csf('email')];
            $companyInfo[$row[csf('id')]]['city']  = $row[csf('city')];
        }

        $sql = "select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.company_id, b.item_name, a.model, a.brand_name,a.item_code
    	from product_details_master a, lib_item_group b 
    	where a.is_deleted=0 and b.is_deleted=0 and a.item_group_id=b.id $whereCon $item_description_cond";   
        // echo   $sql;die;
        $sql_arr = sql_select($sql);
        $countRecords = count($sql_arr); 
        foreach ($sql_arr as $row) {        
            $inf[$row[csf('item_group_id')]] = $row[csf('item_name')]; 
			$item_key=$row[csf('company_id')]."*".$row[csf('item_group_id')]."*".$row[csf('sub_group_name')]."*".$row[csf('item_description')]."*".$row[csf('item_size')]."*".$row[csf('unit_of_measure')]."*".$row[csf('item_name')]."*".$row[csf('model')];
            $dataArray[$row[csf('item_category_id')]][$row[csf('item_group_id')]][$item_key] = $row[csf('id')].'_'.$row[csf('item_description')].'_'.$row[csf('item_size')].'_'.$row[csf('unit_of_measure')].'_'.$row[csf('sub_group_name')].'_'.$row[csf('model')].'_'.$row[csf('brand_name')].'_'.$row[csf('item_code')] ; 
        } 
        ?>
 
        <?php
        if ($countRecords>0)
         { ?>
        <? ob_start();?>
        <div id="scroll_body" align="center" style="height:auto; width:850px; margin:0 auto; padding:0;">
            <table width="850px" >
                <caption style="font-size:15px">
                  <center><strong><?php  echo $companyInfo[$company_id]['name']."<br>".$companyInfo[$company_id]['email']."<br>".$companyInfo[$company_id]['city'];?></strong> </center>
                </caption>
                <tr>
                    <td colspan="6" align="center" style="font-size:15px"><center><strong><u><? echo $report_title; ?> Report</u></strong></center></td>
                </tr>
            </table>
           <?php         
            foreach ($dataArray as $itemCatId=>$itemGrourpArr) 
    		{
                $categoryGrandTotal += count($itemCatId);  
                foreach ($itemGrourpArr as $itemGroupId=>$productArr) 
    			{
    				$grandGroup += count($itemGroupId);              
    				?>
                    <div style="width:850px; height:auto">
                        <table align="right" cellspacing="0" width="850px"  border="1" rules="all" class="rpt_table" id="tbl_suppler_list" >   
                            <thead bgcolor="#dddddd" align="center">
                                <tr>
                                    <td colspan="8" align="left"><b>Category : <?php echo $item_category[$itemCatId];?> </b></td>
                                </tr>
                                <tr>
                                    <td colspan="8" align="left"><b>Item Group : <?php echo $inf[$itemGroupId];?> </b></td>
                                </tr>
                                <tr>
                                    <th width="40">SL</th>
                                    <th width="100" align="center">Sub Group</th>
                                    <th width="200" align="center">Item Description</th>
                                    <th width="150" align="center">Item Code </th>
                                    <th width="100" align="center">Item Size</th>
                                    <th width="100" align="center">Model</th>
                                    <th width="100" align="center">Brand</th>
                                    <th align="center">Cons. UOM</th> 
                                </tr>         
                            </thead>
                            <tbody>
                                <?php 
                                $sl = 0;
                                foreach ($productArr as $productId=>$productInfo) 
    							{ 
                                    $sl++;                                  
                                    $groupGrandTotal += count($productId);                            
                                    list($pid,$description,$size,$uom,$subgroupName,$model,$brand_name,$item_code) =explode("_",$productInfo); 
                                    ?>
                                    <tr bgcolor="">
                                        <td align="center"><? echo $sl; ?></td>
                                        <td><? echo $subgroupName; ?></td>
                                        <td><? echo $description; ?></td>
                                        <td><? echo $item_code; ?></td>
                                        <td><? echo $size; ?></td>
                                        <td><? echo $model; ?></td>
                                        <td><? echo $brand_name; ?></td>
                                        <td align="center"><? echo $unit_of_measurement[$uom]; ?></td>                
                                    </tr>                      
                                    <?php 
                                    }                
                                }
                            }         
                            ?>                  
                            </tbody> 
                            
                            <tfoot>
                                <tr bgcolor="#dddddd">                              
                                  <td colspan="8">Total Item: <?php echo $groupGrandTotal;?></td>
                                </tr>
                                <tr bgcolor="#dddddd">                              
                                  <td colspan="8">Total Group: <?php echo $grandGroup;?></td>
                                </tr>
                                <tr bgcolor="#dddddd">                             
                                  <td colspan="8">Total Category: <?php echo $categoryGrandTotal;?></td>
                                </tr>
                            </tfoot>

                        </table>
                    </div>
        </div>
        <?php
        } else {
            echo "<span style='color:red;'>Record Not Found</span>";
        }
    }
    else
    {
        // echo "shakil"; die;

        $sql_add = "SELECT id,company_name,email,city FROM lib_company where is_deleted=0 and status_active=1 and id = $company_id"; 
        $sql_add_arr = sql_select($sql_add);
        foreach ($sql_add_arr as $row) {
            $companyInfo[$row[csf('id')]]['name']  = $row[csf('company_name')];
            $companyInfo[$row[csf('id')]]['email'] = $row[csf('email')];
            $companyInfo[$row[csf('id')]]['city']  = $row[csf('city')];
        }

        $sql = "select min(a.id) as id, a.company_id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, b.item_name, a.origin, a.model, a.brand_name, a.ORDER_UOM, a.ITEM_CODE,a.ITEM_NUMBER, a.CONVERSION_FACTOR, a.INSERTED_BY
        from product_details_master a, lib_item_group b 
        where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_group_id=b.id $whereCon $item_description_cond
		group by a.company_id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, b.item_name, a.origin, a.model, a.brand_name, a.ORDER_UOM, a.ITEM_CODE,a.ITEM_NUMBER, a.CONVERSION_FACTOR, a.INSERTED_BY ";
        //echo $sql;    
        $sql_arr = sql_select($sql);
        $countRecords = count($sql_arr); 

        $twidth = 1500;
         
    
	 
     if ($countRecords>0) { ?>
 
        <div id="scroll_body" style="">
        <table>
        <tr> <th colspan="7"><?  echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th></tr>
        </table>
 
 			<? ob_start();?>
            
            <table width="<?= $twidth;?>px" >
                <caption style="font-size:15px">
                  <center><strong><?php  echo $companyInfo[$company_id]['name']."<br>".$companyInfo[$company_id]['email']."<br>".$companyInfo[$company_id]['city'];?></strong> </center>
                </caption>
                <tr>
                    <td colspan="6" align="center" style="font-size:15px"><center><strong><u><? echo $report_title; ?> Report</u></strong></center></td>
                </tr>
            </table>

            <div style="width:<?= $twidth;?>px; height:auto">
                <table align="left" cellspacing="0" width="<?= $twidth;?>px"  border="1" rules="all" class="rpt_table"  >   
                    <thead bgcolor="#dddddd" align="center">
                        
                        <tr>
                            <th width="40">SL</th>
                            <th width="50" align="center">Product ID</th>
                            <th width="100" align="center">Category</th>
                            <th width="100" align="center">Item Group</th>
                            <th width="100" align="center">Sub Group</th>
                            <th width="200" align="center">Item Description</th>
                            <th width="100" align="center">Item Code</th>
                            <th width="100" align="center">Item Number</th>
                            <th width="100" align="center">Item Size</th>
                            <th width="100" align="center">Model</th>
                            <th width="100" align="center">Brand</th>
                            <th width="100" align="center">Origin</th>
                            <th width="50" align="center">Order UOM</th>
                            <th width="50" align="center">Cons. UOM</th> 
                            <th align="center">Conversion Factor</th>
                            <th width="80" align="center">Insert User</th>
                        </tr>         
                    </thead>
                </table>
               <div id="scroll_body" style="width:<?= $twidth+20;?>px; max-height:350px; overflow-y:scroll; float:left;">
                <table align="left" cellspacing="0" width="<?= $twidth;?>"  border="1" rules="all" class="rpt_table"  id="tbl_suppler_list2" >
                    
                        <?php 
                        $sl = 0;
                        foreach ($sql_arr as $row) 
                        { 
                            $sl++; 
							
							$bgcolor =($sl % 2 == 1)? "#E9F3FF":"#FFFFFF";                                 
                          
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<?=$sl; ?>','<?= $bgcolor; ?>')" id="tr_<?= $sl; ?>">
                                <td width="40" align="center"><? echo $sl; ?></td>
                                <td width="50"><? echo $row[csf('id')]; ?></td>
                                <td width="100"><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
                                <td width="100"><p><? echo $row[csf('item_name')]; ?></p></td>
                                <td width="100"><p><? echo $row[csf('sub_group_name')]; ?></p></td>
                                <td width="200"><p><? echo $row[csf('item_description')]; ?></p></td>
                                <td width="100"><p><? echo $row[csf('ITEM_CODE')]; ?></p></td>
                                <td width="100"><p><? echo $row[csf('ITEM_NUMBER')]; ?></p></td>
                                <td width="100"><? echo $row[csf('item_size')]; ?></td>
                                <td width="100"><? echo $row[csf('model')]; ?></td>
                                <td width="100"><? echo $row[csf('brand_name')]; ?></td>
                                <td width="100"><? echo $origin_arr[$row[csf('origin')]]; ?></td> 
                                <td width="50" align="center" title="<?=$row['ORDER_UOM']; ?>"><? echo $unit_of_measurement[$row['ORDER_UOM']]; ?></td>
                                <td width="50" align="center" title="<?=$row[csf('unit_of_measure')]; ?>"><? echo $unit_of_measurement[$row[csf('unit_of_measure')]]; ?></td>                
                                <td title="<?=$row['CONVERSION_FACTOR']; ?>"><? echo $row['CONVERSION_FACTOR']; ?></td>
                                <td width="80" title="<?= $user_arr[$row['INSERTED_BY ']]; ?>"><? echo $user_arr[$row[csf('INSERTED_BY')]]; ?></td>
                            </tr>                      
                            <?php 
                        }
                    ?>
                </table>
                </div>
            </div>
        </div>
        <?php
        } else {
            echo "<span style='color:red;'>Record Not Found</span>";
        }
		
    }
	
	
		foreach (glob("$user_id*.xls") as $filename) {
			if (@filemtime($filename) < (time() - $seconds_old))
				@unlink($filename);
		}
		//---------end------------//
		$name = time();
		$filename = $user_id . "_" . $name . ".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, ob_get_contents());
		echo "$total_data####$filename";
	


}
?>


