<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------

//library array-------------------
$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
$countryArr = return_library_array("select id,country_name from lib_country where status_active=1 and is_deleted=0","id","country_name");
$location_details = sql_select("select id,company_name,plot_no,level_no,road_no,block_no,city,zip_code,country_id from lib_company where status_active=1 and is_deleted=0");
$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
$item_group_arr=return_library_array( "select id, item_name from  lib_item_group",'id','item_name');

if($action=="item_group_search_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	//echo $style_id;die;

	?>
    <script>

		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;

    	function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ )
			{
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );

			}
		}

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style )
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( strCon )
		{
			//alert(strCon);
				var splitSTR = strCon.split("_");
				var str = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

				toggle( document.getElementById( 'tr_' + str), '#FFFFCC' );

				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );
					selected_no.push( str );
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
					selected_no.splice( i, 1 );
				}
				var id = ''; var name = ''; var job = ''; var num='';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
					num += selected_no[i] + ',';
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 );
				num 	= num.substr( 0, num.length - 1 );
				//alert(num);
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name );
				$('#txt_selected_no').val( num );
		}

    </script>
    <?
	$company=str_replace("'","",$company);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$txt_item_group=str_replace("'","",$txt_item_group);
	$txt_item_group_id=str_replace("'","",$txt_item_group_id);
	$txt_item_group_no=str_replace("'","",$txt_item_group_no);
	$sql="SELECT id,item_name from  lib_item_group where item_category in($cbo_item_category_id) and status_active=1 and is_deleted=0 order by item_name ASC";
	//echo $sql; die;
	$arr=array();
	echo create_list_view("list_view", "Item Group","300","350","350",0, $sql , "js_set_value", "id,item_name", "", 1, "0", $arr, "item_name", "","setFilterGrid('list_view',-1)","0","",1);

	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";

	?>
    <script language="javascript" type="text/javascript">
	var txt_item_group_no='<? echo $txt_item_group_no;?>';
	var txt_item_group_id='<? echo $txt_item_group_id;?>';
	var txt_item_group='<? echo $txt_item_group;?>';
	//alert(style_id);
	if(txt_item_group_no!="")
	{
		item_group_no_arr=txt_item_group_no.split(",");
		item_group_id_arr=txt_item_group_id.split(",");
		item_group_arr=txt_item_group.split(",");
		var item_group="";
		for(var k=0;k<item_group_no_arr.length; k++)
		{
			item_group=item_group_no_arr[k]+'_'+item_group_id_arr[k]+'_'+item_group_arr[k];
			js_set_value(item_group);
		}
	}
	</script>

    <?
	exit();
}

//item group search------------------------------//
if($action=="item_sub_group_search_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	//echo $style_id;die;

	?>
    <script>

		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;

    	function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ )
			{
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );

			}
		}

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style )
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( strCon )
		{
			//alert(strCon);
				var splitSTR = strCon.split("_");
				var str = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

				toggle( document.getElementById( 'tr_' + str), '#FFFFCC' );

				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );
					selected_no.push( str );
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
					selected_no.splice( i, 1 );
				}
				var id = ''; var name = ''; var job = ''; var num='';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
					num += selected_no[i] + ',';
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 );
				num 	= num.substr( 0, num.length - 1 );
				//alert(num);
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name );
				$('#txt_selected_no').val( num );
		}
    </script>
    <?
	$company=str_replace("'","",$company);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$txt_item_sub_group=str_replace("'","",$txt_item_sub_group);
	$txt_item_sub_group_id=str_replace("'","",$txt_item_sub_group_id);
	$txt_item_sub_group_no=str_replace("'","",$txt_item_sub_group_no);



	$txt_item_group=str_replace("'","",$txt_item_group);
	$txt_item_group_id=str_replace("'","",$txt_item_group_id);
	$txt_item_group_no=str_replace("'","",$txt_item_group_no);

	if (str_replace("'","",$txt_item_group_id)!=="")
		$str_cond .="and item_group_id in($txt_item_group_id)";



		if($db_type==0)
		{
			$sql="SELECT sub_group_code,sub_group_name from  product_details_master where  company_id=$company  and item_category_id in($cbo_item_category_id) and status_active=1 and is_deleted=0 $str_cond and (sub_group_code !='' or sub_group_name !='') group By sub_group_code,sub_group_name order by sub_group_name ASC";
		}
		else
		{
			$sql="SELECT sub_group_code,sub_group_name from  product_details_master where  company_id=$company and  item_category_id in($cbo_item_category_id) and status_active=1 and is_deleted=0 $str_cond and (sub_group_code is not null or sub_group_name is not null )  group By sub_group_code,sub_group_name order by sub_group_name ASC";
		}


	//echo $sql; //die;
	$arr=array();
	echo create_list_view("list_view", "Item Sub Group Code,Item Sub Group Name","120,120","300","300",0, $sql , "js_set_value", "sub_group_code,sub_group_name", "", 1, "0", $arr, "sub_group_code,sub_group_name", "","setFilterGrid('list_view',-1)","0","",1);

	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";

	?>
    <script language="javascript" type="text/javascript">
	var txt_item_sub_group_no='<? echo $txt_item_sub_group_no;?>';
	var txt_item_sub_group_id='<? echo $txt_item_sub_group_id;?>';
	var txt_item_sub_group='<? echo $txt_item_sub_group;?>';
	//alert(style_id);
	if(txt_item_sub_group_no!="")
	{
		item_sub_group_no_arr=txt_item_sub_group_no.split(",");
		item_sub_group_id_arr=txt_item_sub_group_id.split(",");
		item_sub_group_arr=txt_item_sub_group.split(",");
		var item_sub_group="";
		for(var k=0;k<item_sub_group_no_arr.length; k++)
		{
			item_sub_group=item_sub_group_no_arr[k]+'_'+item_sub_group_id_arr[k]+'_'+item_sub_group_arr[k];
			js_set_value(item_sub_group);
		}
	}
	</script>

    <?
	exit();
}
//item description search------------------------------//
if($action=="item_description_search")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);

	?>
    <script>

		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;

    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
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
					selected_no.push(str);
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
					selected_no.splice( i, 1 );
				}
				var id = ''; var name = ''; var job = ''; var num='';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
					num += selected_no[i] + ',';
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 );
				num 	= num.substr( 0, num.length - 1 );

				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name );
				$('#txt_selected_no').val( num );
		}

		function fn_show_description_details()
		{
			var txt_item_group = "<? echo $txt_item_group;?>";
			var txt_item_group_id = "<? echo $txt_item_group_id;?>";
		 	txt_item_group = txt_item_group.replace(/,/g, '**');
		 	txt_item_group_id = txt_item_group_id.replace(/,/g, '**');
			var txt_item_sub_group_id = "<? echo $txt_item_sub_group_id; ?>";
			txt_item_sub_group_id = txt_item_sub_group_id.replace(/,/g, '**');

			show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $company; ?>+'_'+<? echo $cbo_item_cat; ?>+'_'+document.getElementById('txt_item_code').value+'_'+document.getElementById('txt_prod_id').value+'_"'+txt_item_group_id+'"_"'+txt_item_sub_group_id+'"', 'create_item_description_search_list_view', 'search_div', 'item_inquiry_report_controller', 'setFilterGrid("list_view",-1)');
		}
		function fn_item_search(str)
		{
			var field_type="";
			$('#search_by_td').html('');
			$('#search_by_td_up').html('');
			if(str==1)
			{
				field_type='<input type="text" style="width:150px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />';
				$('#search_by_td_up').html('Enter Item Description');
			}
			else if(str==2)
			{
				field_type='<? echo create_drop_down( "txt_search_common", 160,"select id,item_name  from lib_item_group where item_category=$cbo_item_cat and status_active=1","id,item_name", 1, "-- Select --", "", "","","","","",""); ?>';
				$('#search_by_td_up').html('Enter Item Group');
			}
			$('#search_by_td').html(field_type);
		}
    </script>
    <body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="650" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>
						<th width="130">Search By</th>
						<th align="center" width="180" id="search_by_td_up">Enter Item Description</th>
                        <th width="110">Item Code</th>
                        <th width="110">Product Id</th>
 						<th>
                       		<input type="reset" name="re_button" id="re_button" value="Reset" style="width:80px" class="formbutton"  />
                            <input type='hidden' id='txt_selected_id' />
							<input type='hidden' id='txt_selected' />
							<input type='hidden' id='txt_selected_no' />
                        </th>
					</tr>
				</thead>
				<tbody>
					<tr class="general">
						<td align="center">
							<?
								$search_by = array(1=>'Item Description', 2=>'Item Group');
								$dd="";
								echo create_drop_down( "cbo_search_by", 120, $search_by, "", 0, "--Select--", "", "fn_item_search(this.value);", 0);
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:150px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
                        <td align="center">
							<input type="text" style="width:90px" class="text_boxes"  name="txt_item_code" id="txt_item_code" />
						</td>
                        <td align="center" id="search_by_td">
							<input type="text" style="width:90px" class="text_boxes_numeric"  name="txt_prod_id" id="txt_prod_id" />
						</td>
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="fn_show_description_details()" style="width:80px;" />
						</td>
					</tr>
 				</tbody>
			 </tr>
			</table>
			<div align="center" valign="top" style="margin-top:5px" id="search_div"> </div>
			</form>
	   </div>
	</body>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
    <?
	exit();
}

if($action=="create_item_description_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];
	$cbo_item_cat=$ex_data[3];
	$txt_item_code=str_replace("'","",$ex_data[4]);
	$txt_prod_id=str_replace("'","",$ex_data[5]);
	$txt_item_group_id=str_replace("'","",$ex_data[6]);
	$txt_item_group_id=str_replace("**",",",$txt_item_group_id);
	$txt_item_group_id=str_replace('"',"",$txt_item_group_id);

	$txt_item_sub_group_id=str_replace("'","",$ex_data[7]);
	$txt_item_sub_group_id=str_replace("**",",",$txt_item_sub_group_id);
	$txt_item_sub_group_id=str_replace('"',"",$txt_item_sub_group_id);

	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1) // for LOT NO
		{
			$sql_cond= " and product_name_details LIKE '%$txt_search_common%'";
 		}
		else if(trim($txt_search_by)==2) // for Yarn Count
		{
			if($txt_search_common==0)
			{
			$sql_cond= " ";
			}
			else
			{
			$sql_cond= " and item_group_id LIKE '%$txt_search_common%'";
			}
 		}
 	}

	if($txt_item_code!="") $sql_cond.=" and  item_code='$txt_item_code'";
	if($txt_prod_id!="") $sql_cond.=" and  id=$txt_prod_id";
	if($txt_item_group_id!="") $sql_cond.=" and  item_group_id in($txt_item_group_id)";
	if($txt_item_sub_group_id!="") $sql_cond.=" or  sub_group_name like '%$txt_item_sub_group_id%'";


 	$sql = "select id,item_group_id,item_description as product_name_details,item_code,item_size from product_details_master where company_id=$company and item_category_id =$cbo_item_cat $sql_cond order by product_name_details ASC";
	//echo $sql;
	$arr=array(1=>$item_group_arr);
	echo create_list_view("list_view", "Product Id, Item Group, Item Code, Item Description,Item Size","70,160,100,100","600","260",0, $sql , "js_set_value", "id,product_name_details", "", 1, "0,item_group_id,0,0,0", $arr, "id,item_group_id,item_code,product_name_details,item_size", "","","0","",1) ;

	exit();
}

//report generated here--------------------//
if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_item_cat=str_replace("'","",$cbo_item_cat);
	if( "," == substr($txt_item_sub_group_id,-1))  $txt_item_sub_group_id = chop(",",$txt_item_sub_group_id);
	$countryShortNameArr = return_library_array("select id,short_name from lib_country where status_active=1 and is_deleted=0", 'id','short_name');

	//var_dump($location_details);
	foreach ($location_details as  $value) {
		$location[$value[csf("id")]]["id"] = $value[csf("id")];
		$location[$value[csf("id")]]["company_name"] = $value[csf("company_name")];
		$location[$value[csf("id")]]["plot_no"] = $value[csf("plot_no")];
		$location[$value[csf("id")]]["block_no"] = $value[csf("block_no")];
		$location[$value[csf("id")]]["level_no"] = $value[csf("level_no")];
		$location[$value[csf("id")]]["road_no"] = $value[csf("road_no")];
		$location[$value[csf("id")]]["city"] = $value[csf("city")];
		$location[$value[csf("id")]]["zip_code"] = $value[csf("zip_code")];
		$location[$value[csf("id")]]["country_id"] = $value[csf("country_id")];
	}
	//var_dump($location);
	$search_cond="";
	if($txt_item_group_id != ""){
		$search_cond.=" and a.item_group_id in($txt_item_group_id)";
	}

	if($txt_item_sub_group_id != ""){
		$search_cond.=" and a.sub_group_code Like '%$txt_item_sub_group_id%'";
	}

	if($txt_product_id !=""){
		$txt_product_id = explode(",",$txt_product_id);

		if(count($txt_product_id)>1000){
			$txt_product_id_arr=array_chunk($txt_product_id,999);
			//var_dump($txt_product_id_arr);die;
			$p=1;
			foreach($txt_product_id_arr as $txt_product_id_val)
			{
				if($p==1) $search_cond .=" and (a.id in(".implode(',',$txt_product_id_val).")"; else  $search_cond .=" or a.id  in(".implode(',',$txt_product_id_val).")";

				$p++;
			}
			$search_cond .=")";
		}else{
			$product_ids = implode(',', $txt_product_id);

			$search_cond .=" and a.id in($product_ids)";
		}
	}

	$sql = "SELECT a.id, a.company_id, a.item_group_id,a.item_number, a.item_code, a.item_description, a.sub_group_code, a.sub_group_name, a.supplier_id, a.maximum_label, a.re_order_label, a.unit_of_measure,a.model,a.brand_name, a.origin, a.current_stock from product_details_master a where a.company_id = $cbo_company_name and a.is_deleted = 0 and a.status_active = 1 and a.item_category_id = $cbo_item_cat $search_cond";
	$result = sql_select($sql);
	//echo $sql;
	$item_sub_group_arr = array();
	$mst_id_arr = array();
	foreach ($result as $val) 
	{
		$mst_id_arr[$val['ID']] = $val['ID'];
		$item_sub_group_arr[$val['SUB_GROUP_NAME']] = $val['SUB_GROUP_NAME'];
	}
	// print_r($item_sub_group_arr);



// data more then 1000 as like array chung
	$user_id = $_SESSION['logic_erp']["user_id"];
    $con = connect();
    $tmpType = 952;
    foreach($mst_id_arr as $mstId) {
        if($mstId!=0) {
            $r_id2=execute_query("insert into tmp_poid(userid, poid, type) values ($user_id,$mstId,$tmpType)");
            // echo "insert into tmp_poid(userid, poid, type) values ($user_id,$mstId,$tmpType)";die;
        }            
    }

    if($db_type==0) {
        if($r_id2) {
            mysql_query("COMMIT");  
        }
    }
    if($db_type==2 || $db_type==1) {
        if($r_id2) {
            oci_commit($con);  
        }
    }


	//$mst_ids = "'".implode("','", $mst_id_arr)."'";
	$item_sub_group_name = implode(", ", array_filter($item_sub_group_arr));
	/*$image_lib=return_library_array( "SELECT master_tble_id, image_location from  common_photo_library where master_tble_id in($mst_ids) and form_name='item_creation'",'master_tble_id','image_location');*/
	$image_lib=return_library_array( "SELECT a.master_tble_id, a.image_location from  common_photo_library a, tmp_poid b where a.master_tble_id=b.poid and b.userid=$user_id and b.type=$tmpType and a.form_name='item_creation'",'master_tble_id','image_location');

	// =============================== getting last information =============================
	/*$last_info_sql = "SELECT prod_id,transaction_date,supplier_id,cons_quantity as rcv_qty,cons_rate as rcv_rate from inv_transaction where prod_id in($mst_ids) and status_active=1 and is_deleted=0 and transaction_type=1 order by id DESC";*/
	$last_info_sql = "SELECT a.prod_id,a.transaction_date,a.supplier_id,a.cons_quantity as rcv_qty,a.cons_rate as rcv_rate
	from inv_transaction a, tmp_poid b
	where a.prod_id=b.poid and b.userid=$user_id and b.type=$tmpType and a.status_active=1 and a.is_deleted=0 and a.transaction_type=1
	order by a.id DESC";
	$last_info_sql_res = sql_select($last_info_sql);
	// print_r($last_info_sql_res);
	$last_info_arr = array();
	$array_check = array();
	foreach ($last_info_sql_res as $val) 
	{
		if(!isset($array_check[$val['PROD_ID']]))
		{
			$last_info_arr[$val['PROD_ID']]['date'] = $val['TRANSACTION_DATE'];
			$last_info_arr[$val['PROD_ID']]['supplier'] = $val['SUPPLIER_ID'];
			$last_info_arr[$val['PROD_ID']]['qty'] = $val['RCV_QTY'];			
			$last_info_arr[$val['PROD_ID']]['rate'] = $val['RCV_RATE'];			
		}
		$array_check[$val['PROD_ID']] = $val['PROD_ID'];
	}
	// print_r($last_info_arr);
	ob_start();
	?>
	<div style="width: 1280px;padding-bottom: 10px;" id="scroll_body">
		<style type="text/css">
			.img_container img{opacity: 0;position: absolute; right: 6%;top: 19%; max-height: 300px; max-width: 300px;}
			img:hover+.img_container img{
				display: block;
				opacity: 1;
			}
			/*.popover__wrapper {
			    position: relative;
			}
			.popover__content {
			    opacity: 0;
			    visibility: hidden;
			    position: absolute;
			    left: -250px;
			    transform: translate(0,10px);
			    background-color: #BFBFBF;
			    padding: 1.5rem;
			    box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.26);
			    width: auto;
			}
			.popover__content:before {
			    position: absolute;
			    z-index: -1;
			    content: '';
			    right: calc(50% - 10px);
			    top: -8px;
			    border-style: solid;
			    border-width: 0 10px 10px 10px;
			    border-color: transparent transparent red transparent;
			    transition-duration: 0.3s;
			    transition-property: transform;
			}
			.popover__wrapper:hover .popover__content {
			    z-index: 10;
			    opacity: 1;
			    visibility: visible;
			    transform: translate(220px,20px);
			    transition: all 0.5s cubic-bezier(0.75, -0.02, 0.2, 0.97);
			}
			.popover__content img{ max-width: 300px;max-height: 300px; }*/
		</style>
    	<table width="1090" border="0" align = "left">
        	<tr class="form_caption" style="border:none;">
                <td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?> </td>
            </tr>
            <tr style="border:none;">
                <td colspan="15" align="center" style="border:none; font-size:14px;">
                <h1>Company Name : <? echo $companyArr[str_replace("'","",$cbo_company_name)]; ?></h1>
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="15" align="center" style="border:none;font-size:12px; font-weight:bold">
                <? echo "Level No# ".$location["$cbo_company_name"]["level_no"].", Plot No# ".$location["$cbo_company_name"]["plot_no"].", Road No# ".$location["$cbo_company_name"]["road_no"].", Block No# ".$location["$cbo_company_name"]["block_no"].", City: ".$location["$cbo_company_name"]["city"].", ZIP Code# ".$location["$cbo_company_name"]["zip_code"];?>
                </td>
            </tr>

        </table>
		<div id="scroll_body1" style="width: 1080px; overflow-y: scroll; max-height: 350px;">
			<table width="1060" border="0" cellpadding="4" cellspacing="0" class="rpt_table" rules="all" id="table_header_2"  align = "left">
				<tr style="background-color: #def8c4;">
                    <td width="130" align="left"><strong>Item Category : </strong></th>
                    <td><? echo $item_category[$cbo_item_cat];?></td>
				</tr>
				<tr style="background-color: #def8c4;">
                    <td width="130" align="left"><strong>Sub Group Name: </strong></th>
                    <td><? echo $item_sub_group_name;//$txt_item_sub_group;?></td>

                </tr>
			</table>
        	<table width="1060" border="1" cellspacing="0" class="rpt_table" rules="all" id="table_header_1"  align = "left" >
            <thead>
                <tr>
                    <th width="40">SL</th>
                    <th width="120">Sub Group Name</th>
                    <th width="90">Item Number</th>
                    <th width="70">Item Code</th>
                    <th width="220">Item Description</th>
                    <th width="35">M.S.Q</th>
                    <th width="30">R.L</th>
                    <th width="40">UOM</th>
                    <th width="60">Brand</th>
                    <th width="60">Model</th>
                    <th width="70">Origin</th>
                    <th width="60">Stock</th>
                    <th width="70">Last Received Date</th>
                    <th width="70">Last Received Rate</th>
                    <th width="70">Last Received Qty</th>
                    <th width="150">Last Supplier</th>
                    <th>Images</th>
                </tr>
            </thead>

            <tbody id="item_inquiry_rpt_tbl_body">
			<?
				$bgColor="#FEFEFE";
				 $i = 1;
				foreach ($result  as $row) 
				{

					?>
                	<tr id="tr_<? echo $i ?>" bgcolor="<? echo $bgColor;?>" onClick="change_color('<? echo "tr_".$i; ?>', '<? echo $bgColor;?>')">
						<td valign="middle" align="center"><? echo $i; ?></td>
						<td valign="middle" align="center"> <? echo $row[csf("sub_group_name")];?></td>
						<td valign="middle" align="center"> <? echo $row[csf("item_number")];?></td>
						<td valign="middle" align="center"> <? echo $row[csf("item_code")];?></td>
						<td valign="middle" title = "prod_id: <? echo $row[csf("id")];?>"> <? echo $row[csf("item_description")];?></td>
						<td valign="middle" align="center"> <? echo $row[csf("maximum_label")];?></td>
						<td valign="middle" align="center"> <? echo $row[csf("re_order_label")];?></td>
						<td valign="middle" align="center"> <? echo $unit_of_measurement[$row[csf("unit_of_measure")]];?></td>
						<td valign="middle" align="center"> <? echo $row[csf("brand_name")];?></td>
						<td valign="middle" align="center"> <? echo $row[csf("model")];?></td>
						<td valign="middle" align="center"> <? echo $countryShortNameArr[$row[csf("origin")]];?></td>
						<td valign="middle" align="right"> <? echo number_format($row[csf("current_stock")]);?></td>
						<td valign="middle" align="center"> <? echo change_date_format($last_info_arr[$row[csf("id")]]['date']);?></td>
						<td valign="middle" align="right"> <? echo $last_info_arr[$row[csf("id")]]['rate'];?></td>
						<td valign="middle" align="right"> <? echo $last_info_arr[$row[csf("id")]]['qty'];?></td>
						<td valign="middle" align="right"> <? echo $supplierArr[$last_info_arr[$row[csf("id")]]['supplier']];?></td>
						<td valign="middle" align="center">
							<img src="../../../<? echo $image_lib[$row[csf('id')]];?>" height="20" width="50"> 
							<div  class="img_container">
								<img src="../../../<? echo $image_lib[$row[csf('id')]];?>"> 
							</div>
						</td>
					</tr>
					<?
					$i++;
				}
			?>
            </tbody>
			<tfoot>

			</tfoot>
        </table>
		</div>
	</div>

    <?
	$html = ob_get_contents();
	ob_clean();
	//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename";

	if ($db_type == 2 || $db_type == 1) 
    {
        $r_id3=execute_query("delete from tmp_poid where userid=$user_id and type=$tmpType");
        
        if($r_id3)
        {
            oci_commit($con);
        }
    }
    disconnect($con);

	exit();

}

?>
