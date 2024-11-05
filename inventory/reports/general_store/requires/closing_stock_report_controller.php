<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if($action=="print_button_variable_setting")
{

    $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name in($data) and module_id=6 and report_id=84 and is_deleted=0 and status_active=1","format_id","format_id");
    echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
    exit();
}

if($action=="varible_inventory")
{
	$sql_variable_inventory=sql_select("select id, independent_controll, rate_optional, is_editable, rate_edit  from variable_settings_inventory where company_name=$data and variable_list=20 and status_active=1 and menu_page_id=20");
	if(count($sql_variable_inventory)>0)
	{
		echo "1**".$sql_variable_inventory[0][csf("is_editable")];
	}
	else
	{
		echo "0**".$sql_variable_inventory[0][csf("is_editable")];
	}

	die;
}

if ($action=="load_drop_down_store")
{
	$data=explode("**",$data);
	if($data[1]==2) $disable=1; else $disable=0;
	$userCredential = sql_select("SELECT store_location_id, item_cate_id FROM user_passwd where id=$user_id");
	if($userCredential[0][csf("store_location_id")]>0) $store_cond =" and a.id in (".$userCredential[0][csf("store_location_id")].")";
	else  $store_cond =" and CATEGORY_TYPE in(select category_id from lib_item_category_list where category_type=1)";
	echo create_drop_down( "cbo_store_name", 120, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id='$data[0]' $store_cond group by a.id,a.store_name","id,store_name", 1, "--Select Store--", 0, "",$disable );
	exit();
}

if($action=="item_group_such_popup")
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
			var onclickString =""; var paramArr = ""; var functionParam = "";
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
			//alert(tbl_row_count);
			for( var i = 1; i <= tbl_row_count; i++ )
			{
				onclickString = $('#tr_' + i).attr('onclick');
				paramArr = onclickString.split("'");
				functionParam = paramArr[1];
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
	$sql="SELECT a.id,a.item_name,b.item_code from lib_item_group a,product_details_master b where a.id=b.item_group_id and a.item_category in($cbo_item_category_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.item_name,b.item_code";
	//echo $sql; die;
	$arr=array();
	echo create_list_view("list_view", "Item Group,Item Code","150,100","300","300",0, $sql , "js_set_value", "id,item_name,item_code", "", 1, "0,0", $arr, "item_name,item_code", "","setFilterGrid('list_view',-1)","0,0","",1);

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
if($action=="item_sub_group_such_popup")
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
			tbl_row_count = tbl_row_count - 1;
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
			$sql="SELECT id,sub_group_code,sub_group_name from  lib_item_sub_group where item_category_id in($cbo_item_category_id) and status_active=1 and is_deleted=0 $str_cond and (sub_group_code !='' or sub_group_name !='') group By id, sub_group_code,sub_group_name";
		}
		else
		{
			$sql="SELECT id,sub_group_code,sub_group_name from  lib_item_sub_group where   item_category_id in($cbo_item_category_id) and status_active=1 and is_deleted=0 $str_cond and (sub_group_code is not null or sub_group_name is not null )  group By id, sub_group_code,sub_group_name";
		}


	//echo $sql; die;
	$arr=array();
	echo create_list_view("list_view", "Item Sub Group Code,Item Sub Group Name","120,120","300","300",0, $sql , "js_set_value", "id,sub_group_name", "", 1, "0", $arr, "sub_group_code,sub_group_name", "","setFilterGrid('list_view',-1)","0","",1);

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

//item group search------------------------------//
if($action=="item_account_such_popup")
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
			tbl_row_count = tbl_row_count - 1;
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
	$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$company=str_replace("'","",$company);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$txt_item_group_id=str_replace("'","",$txt_item_group_id);
	$txt_item_acc=str_replace("'","",$txt_item_acc);
	$txt_item_account_id=str_replace("'","",$txt_item_account_id);
	$txt_item_acc_no=str_replace("'","",$txt_item_acc_no);

	 $sql_cond="";
	if($txt_item_group_id!="") $sql_cond=" and item_group_id in($txt_item_group_id)";


	$item_sub_group_id=str_replace("'","",$txt_item_sub_group_id);

	//echo $item_sub_group_id; die;
	$item_sub_group_id=explode(',',$item_sub_group_id);
		foreach($item_sub_group_id as $id=>$Key)
		{
			$item_sub_group_id[$id]="'".$Key."'";
			$iteme_sub_group_multi_id.=$item_sub_group_id[$id].',';
		}
		$iteme_sub_group_multi_id=chop($iteme_sub_group_multi_id,',');

		if (str_replace("'","",$iteme_sub_group_multi_id)!=="")
		$sql_cond.=" and sub_group_code in($iteme_sub_group_multi_id)";


	$sql="SELECT id,item_account,item_category_id,item_group_id,item_description,supplier_id,sub_group_code, sub_group_name,item_code from  product_details_master where item_category_id in($cbo_item_category_id) and status_active=1 and is_deleted=0 and company_id=$company $sql_cond";
	//echo $sql; die;
	$arr=array(2=>$general_item_category,3=>$itemgroupArr,7=>$supplierArr);
	echo  create_list_view("list_view", "Item Account,Item Code,Item Category,Item Group,Item Sub Group Code,Item Sub Group Name,Item Description,Supplier,Product ID", "70,70,110,130,130,130,150,100","1020","320",0, $sql , "js_set_value", "id,item_description", "", 1, "0,0,item_category_id,item_group_id,0,0,0,supplier_id,0", $arr , "item_account,item_code,item_category_id,item_group_id,sub_group_code,sub_group_name,item_description,supplier_id,id", "",'setFilterGrid("list_view",-1);','0,0,0,0,0,00,0,0','',1) ;


	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";

	?>
    <script language="javascript" type="text/javascript">
	var item_acc_no_arr=item_acc_id_arr=item_acc_arr=new Array();
	var txt_item_acc_no='<? echo $txt_item_acc_no;?>';
	var txt_item_account_id='<? echo $txt_item_account_id;?>';
	var txt_item_acc='<? echo $txt_item_acc;?>';
	//alert(txt_item_acc_no);
	if(txt_item_acc_no !="")
	{
		item_acc_no_arr=txt_item_acc_no.split(",");
		item_acc_id_arr=txt_item_account_id.split(",");
		item_acc_arr=txt_item_acc.split(",");
		var item_account="";
		for(var k=0;k<item_acc_no_arr.length; k++)
		{
			item_account=item_acc_no_arr[k]+'_'+item_acc_id_arr[k]+'_'+item_acc_arr[k];
			js_set_value(item_account);
		}
	}
	</script>

    <?

	exit();
}


if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$item_group_id=str_replace("'","",$item_group_id);
	$item_sub_group_id=str_replace("'","",$item_sub_group_id);
	$item_account_id=str_replace("'","",$item_account_id);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$cbo_yes_no=str_replace("'","",$cbo_yes_no);
	$value_with=str_replace("'","",$cbo_value_with);
    $report_type=str_replace("'","",$report_type);
	$variable_string_inventory=str_replace("'","",$variable_string_inventory);

	//echo $cbo_item_category_id."__".$item_group_id."__".$item_account_id."__".$variable_string_inventory;die;

	$str_cond="";$zero_cond="";
	if ($cbo_company_name!=0) $str_cond =" and a.company_id='$cbo_company_name'";
	if ($cbo_item_category_id!="") $str_cond .=" and b.item_category_id in($cbo_item_category_id)";

	if ($item_group_id!="") $str_cond .=" and b.item_group_id in($item_group_id)";
	if ($item_sub_group_id != "")
	$str_cond .="and b.item_sub_group_id in($item_sub_group_id)";
	//echo $str_cond ; die;

	if ($item_account_id!="") $str_cond .=" and a.prod_id in ($item_account_id)";
	$store_cond="";
	if ($cbo_store_name!=0) $store_cond .=" and a.store_id='$cbo_store_name'";
	if($db_type==0) $select_from_date=change_date_format($from_date,'yyyy-mm-dd');
	if($db_type==2) $select_from_date=change_date_format($from_date,'','',1);

	if($db_type==0) $select_to_date=change_date_format($to_date,'yyyy-mm-dd');
	if($db_type==2) $select_to_date=change_date_format($to_date,'','',1);

	//if($value_with==0) $zero_cond .=""; else $zero_cond .= "  and b.current_stock>0";

	//$item_cate_credential_cond="".implode(",",array_diff(array_flip($general_item_category), array("4")))."";
	$item_cate_credential_cond="".implode(",",array_flip($general_item_category))."";
	//echo $item_cate_credential_cond;die;
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$itemgroupArr = return_library_array("select id,item_name from lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	$item_subgroupArr = return_library_array("select id,sub_group_name from lib_item_sub_group where status_active=1 and is_deleted=0","id","sub_group_name");
	$variable_store_wise_rate=return_field_value("auto_transfer_rcv","variable_settings_inventory","company_name=$cbo_company_name and variable_list=47 and item_category_id=8 and status_active=1 and is_deleted=0","auto_transfer_rcv");
	//echo $variable_store_wise_rate;die;

	$days_doh=array();
	$returnRes="select prod_id, min(transaction_date) || ',' || max(transaction_date )  as total_date from inv_transaction where transaction_type in (1,2,3,4,5,6) and item_category in ($item_cate_credential_cond)  and status_active=1 and is_deleted=0 and transaction_date<='$select_to_date' group by prod_id ";
	$returnRes_result = sql_select($returnRes);
	foreach($returnRes_result as $row_d)
	{
		$date_total=explode(",",$row_d[csf('total_date')]);
		if(strtotime($date_total[1])<=strtotime($select_to_date))
		{
			$today= change_date_format(date("Y-m-d"),'','',1);
			$daysOnHand = datediff("d",change_date_format($date_total[1],'','',1), date("Y-m-d",strtotime($select_to_date)));
			$days_doh[$row_d[csf('prod_id')]]['last_trans_date']=$date_total[1] ;
		}
		$days_doh[$row_d[csf('prod_id')]]['daysonhand']=$daysOnHand ;
	}
	// echo "<pre>";print_r($days_doh);die;
	if($cbo_yes_no==1)
	{
		$store_name_arr=return_library_array("select id, store_name from  lib_store_location","id","store_name");
		$store_field=", a.store_id as trans_store";
		$strore_group="a.store_id, ";
		$store_order=" , a.prod_id, a.store_id";
		$div_width="2090";
		$table_width="2070";
	}
	else
	{
		$store_field=", 0 as trans_store";
		$strore_group="";
		$store_order=" , a.prod_id";
		$div_width="2000";
		$table_width="1980";
	}
	//echo $report_type;die;
    if($report_type == 3)
	{
		if($cbo_yes_no==1)
		{
			if($variable_string_inventory!=1)
			{
				$div_width="2680";
				$table_width="2680";
			}
			else
			{
				$div_width="2280";
				$table_width="2280";
			}
		}
		else
		{
			if($variable_string_inventory!=1)
			{
				$div_width="2780";
				$table_width="2750";
			}
			else
			{
				$div_width="2180";
				$table_width="2150";
			}
		}
		//$cbo_company_name
		$loanRcvData = return_library_array("select id from inv_receive_master where status_active=1 and is_deleted=0 and company_id=$cbo_company_name and entry_form=20 and receive_purpose=1","id","id");
		$loanIssueData = return_library_array("select id from inv_issue_master where status_active=1 and is_deleted=0 and company_id=$cbo_company_name and entry_form=21 and issue_purpose=5","id","id");

		$item_cate_credential_cond="".implode(",",array_diff(array_flip($general_item_category), array("4")))."";
		if($variable_store_wise_rate==1)
		{
			$sql="SELECT a.prod_id, a.store_id, b.id, b.item_number, b.item_code,b.item_number,b.model, b.item_category_id, b.item_group_id, b.avg_rate_per_unit, b.item_sub_group_id, b.sub_group_name, b.item_description, b.re_order_label, b.item_size, b.unit_of_measure, a.transaction_type, a.transaction_date, b.re_order_label, a.mst_id, a.balance_qnty,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.cons_quantity else 0 end) as opening_total_receive,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.store_amount else 0 end) as opening_total_receive_amt,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.cons_quantity else 0 end) as opening_total_issue,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.store_amount else 0 end) as opening_total_issue_amt,
			(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (1,4,5)  then a.store_amount else 0 end) as total_rcv_amount,
			(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (2,3,6)  then a.store_amount else 0 end) as total_issue_amount,
			(case when a.transaction_type=1 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as purchase,
			(case when a.transaction_type=3 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as receive_return,
			(case when a.transaction_type=2 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue,
			(case when a.transaction_type=4 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue_return,
			(case when a.transaction_type=5 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_in,
			(case when a.transaction_type=6 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_out,
			(case when a.transaction_type in (1,4,5) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.store_amount else 0 end) as total_rcv_amt_value,
			(case when a.transaction_type in (2,3,6) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.store_amount else 0 end) as total_iss_amt_value
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.transaction_type in (1,2,3,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category  in($item_cate_credential_cond) and b.entry_form<>24  $str_cond $zero_cond
			order by b.item_category_id, b.item_group_id, a.id";
		}
		else
		{
			$sql="SELECT a.prod_id, a.store_id, b.id, b.item_number, b.item_code,b.item_number,b.model, b.item_category_id, b.item_group_id, b.avg_rate_per_unit, b.item_sub_group_id, b.sub_group_name, b.item_description, b.re_order_label, b.item_size, b.unit_of_measure, a.transaction_type, a.transaction_date, b.re_order_label, a.mst_id, a.balance_qnty,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.cons_quantity else 0 end) as opening_total_receive,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.cons_amount else 0 end) as opening_total_receive_amt,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.cons_quantity else 0 end) as opening_total_issue,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.cons_amount else 0 end) as opening_total_issue_amt,
			(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (1,4,5)  then a.cons_amount else 0 end) as total_rcv_amount,
			(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (2,3,6)  then a.cons_amount else 0 end) as total_issue_amount,
			(case when a.transaction_type=1 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as purchase,
			(case when a.transaction_type=3 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as receive_return,
			(case when a.transaction_type=2 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue,
			(case when a.transaction_type=4 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue_return,
			(case when a.transaction_type=5 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_in,
			(case when a.transaction_type=6 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_out,
			(case when a.transaction_type in (1,4,5) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_amount else 0 end) as total_rcv_amt_value,
			(case when a.transaction_type in (2,3,6) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_amount else 0 end) as total_iss_amt_value
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.transaction_type in (1,2,3,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category  in($item_cate_credential_cond) and b.entry_form<>24  $str_cond $zero_cond
			order by b.item_category_id, b.item_group_id, a.id";
		}

		//echo  $sql;die;
		//and a.prod_id in(4065,6578)
		$result = sql_select($sql);
		$details_data=array();
		$prod_wise_trans_date=array();
		foreach($result as $row)
		{
			if($cbo_store_name>0)
			{
				if($cbo_store_name==$row[csf("store_id")])
				{
					$details_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
					$details_data[$row[csf("prod_id")]]["trans_store"]=$row[csf("store_id")];
					$details_data[$row[csf("prod_id")]]["item_number"]=$row[csf("item_number")];
					$details_data[$row[csf("prod_id")]]["item_code"]=$row[csf("item_code")];
					$details_data[$row[csf("prod_id")]]["item_category_id"]=$row[csf("item_category_id")];
					$details_data[$row[csf("prod_id")]]["item_group_id"]=$row[csf("item_group_id")];
					$details_data[$row[csf("prod_id")]]["avg_rate_per_unit"]=$row[csf("avg_rate_per_unit")];
					$details_data[$row[csf("prod_id")]]["sub_group_name"]=$row[csf("sub_group_name")];
					//$details_data[$row[csf("prod_id")]]["sub_group_name"]=$item_subgroupArr[$row[csf("item_sub_group_id")]];
					$details_data[$row[csf("prod_id")]]["item_description"]=$row[csf("item_description")];
					$details_data[$row[csf("prod_id")]]["re_order_label"]=$row[csf("re_order_label")];
					$details_data[$row[csf("prod_id")]]["item_size"]=$row[csf("item_size")];
					$details_data[$row[csf("prod_id")]]["model"]=$row[csf("model")];
					$details_data[$row[csf("prod_id")]]["unit_of_measure"]=$row[csf("unit_of_measure")];
					$details_data[$row[csf("prod_id")]]["re_order_label"]=$row[csf("re_order_label")];

					$details_data[$row[csf("prod_id")]]["opening_total_receive"]+=$row[csf("opening_total_receive")];
					$details_data[$row[csf("prod_id")]]["opening_total_receive_amt"]+=$row[csf("opening_total_receive_amt")];
					$details_data[$row[csf("prod_id")]]["opening_total_issue"]+=$row[csf("opening_total_issue")];
					$details_data[$row[csf("prod_id")]]["opening_total_issue_amt"]+=$row[csf("opening_total_issue_amt")];
					$details_data[$row[csf("prod_id")]]["total_rcv_amount"]+=$row[csf("total_rcv_amount")];
					$details_data[$row[csf("prod_id")]]["total_issue_amount"]+=$row[csf("total_issue_amount")];
					if($loanRcvData[$row[csf("mst_id")]])
					{
						$details_data[$row[csf("prod_id")]]["loan_rcv"]+=$row[csf("purchase")];
					}
					else
					{
						$details_data[$row[csf("prod_id")]]["purchase"]+=$row[csf("purchase")];
					}
					$details_data[$row[csf("prod_id")]]["receive_return"]+=$row[csf("receive_return")];
					if($loanIssueData[$row[csf("mst_id")]])
					{
						$details_data[$row[csf("prod_id")]]["loan_iss"]+=$row[csf("issue")];
					}
					else
					{
						$details_data[$row[csf("prod_id")]]["issue"]+=$row[csf("issue")];
					}
					$details_data[$row[csf("prod_id")]]["issue_return"]+=$row[csf("issue_return")];
					$details_data[$row[csf("prod_id")]]["transfer_in"]+=$row[csf("transfer_in")];
					$details_data[$row[csf("prod_id")]]["transfer_out"]+=$row[csf("transfer_out")];
					$details_data[$row[csf("prod_id")]]["total_rcv_amt_value"]+=$row[csf("total_rcv_amt_value")];
					$details_data[$row[csf("prod_id")]]["total_iss_amt_value"]+=$row[csf("total_iss_amt_value")];
				}

				if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5)
				{
					$item_wise_stock[$row[csf("prod_id")]]["bal_qnty"]+=($row[csf("opening_total_receive")]+$row[csf("purchase")]+$row[csf("issue_return")]+$row[csf("transfer_in")]);
					$item_wise_stock[$row[csf("prod_id")]]["bal_amt"]+=($row[csf("opening_total_receive_amt")]+$row[csf("total_rcv_amt_value")]);
					$item_wise_stock[$row[csf("prod_id")]]["bal_qnty_opeinng"]+=$row[csf("opening_total_receive")];
					$item_wise_stock[$row[csf("prod_id")]]["bal_amt_opening"]+=$row[csf("opening_total_receive_amt")];
				}
				else
				{
					$item_wise_stock[$row[csf("prod_id")]]["bal_qnty"]-=($row[csf("opening_total_issue")]+$row[csf("issue")]+$row[csf("receive_return")]+$row[csf("transfer_out")]);
					$item_wise_stock[$row[csf("prod_id")]]["bal_amt"]-=($row[csf("opening_total_issue_amt")]+$row[csf("total_iss_amt_value")]);
					$item_wise_stock[$row[csf("prod_id")]]["bal_qnty_opeinng"]-=$row[csf("opening_total_issue")];
					$item_wise_stock[$row[csf("prod_id")]]["bal_amt_opening"]-=$row[csf("opening_total_issue_amt")];
				}

			}
			else
			{
				$details_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
				$details_data[$row[csf("prod_id")]]["trans_store"]=$row[csf("store_id")];
				$details_data[$row[csf("prod_id")]]["item_number"]=$row[csf("item_number")];
				$details_data[$row[csf("prod_id")]]["item_code"]=$row[csf("item_code")];
				$details_data[$row[csf("prod_id")]]["item_category_id"]=$row[csf("item_category_id")];
				$details_data[$row[csf("prod_id")]]["item_group_id"]=$row[csf("item_group_id")];
				$details_data[$row[csf("prod_id")]]["avg_rate_per_unit"]=$row[csf("avg_rate_per_unit")];
				//$details_data[$row[csf("prod_id")]]["sub_group_name"]=$row[csf("sub_group_name")];
				$details_data[$row[csf("prod_id")]]["sub_group_name"]=$item_subgroupArr[$row[csf("item_sub_group_id")]];
				$details_data[$row[csf("prod_id")]]["item_description"]=$row[csf("item_description")];
				$details_data[$row[csf("prod_id")]]["re_order_label"]=$row[csf("re_order_label")];
				$details_data[$row[csf("prod_id")]]["item_size"]=$row[csf("item_size")];
				$details_data[$row[csf("prod_id")]]["model"]=$row[csf("model")];
				$details_data[$row[csf("prod_id")]]["unit_of_measure"]=$row[csf("unit_of_measure")];
				$details_data[$row[csf("prod_id")]]["re_order_label"]=$row[csf("re_order_label")];

				$details_data[$row[csf("prod_id")]]["opening_total_receive"]+=$row[csf("opening_total_receive")];
				$details_data[$row[csf("prod_id")]]["opening_total_receive_amt"]+=$row[csf("opening_total_receive_amt")];
				$details_data[$row[csf("prod_id")]]["opening_total_issue"]+=$row[csf("opening_total_issue")];
				$details_data[$row[csf("prod_id")]]["opening_total_issue_amt"]+=$row[csf("opening_total_issue_amt")];
				$details_data[$row[csf("prod_id")]]["total_rcv_amount"]+=$row[csf("total_rcv_amount")];
				$details_data[$row[csf("prod_id")]]["total_issue_amount"]+=$row[csf("total_issue_amount")];
				if($loanRcvData[$row[csf("mst_id")]])
				{
					$details_data[$row[csf("prod_id")]]["loan_rcv"]+=$row[csf("purchase")];
				}
				else
				{
					$details_data[$row[csf("prod_id")]]["purchase"]+=$row[csf("purchase")];
				}
				$details_data[$row[csf("prod_id")]]["receive_return"]+=$row[csf("receive_return")];
				if($loanIssueData[$row[csf("mst_id")]])
				{
					$details_data[$row[csf("prod_id")]]["loan_iss"]+=$row[csf("issue")];
				}
				else
				{
					$details_data[$row[csf("prod_id")]]["issue"]+=$row[csf("issue")];
				}
				$details_data[$row[csf("prod_id")]]["issue_return"]+=$row[csf("issue_return")];
				$details_data[$row[csf("prod_id")]]["transfer_in"]+=$row[csf("transfer_in")];
				$details_data[$row[csf("prod_id")]]["transfer_out"]+=$row[csf("transfer_out")];
				$details_data[$row[csf("prod_id")]]["total_rcv_amt_value"]+=$row[csf("total_rcv_amt_value")];
				$details_data[$row[csf("prod_id")]]["total_iss_amt_value"]+=$row[csf("total_iss_amt_value")];
			}

			if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5)
			{
				if($row[csf("balance_qnty")]>0 && strtotime($row[csf("transaction_date")])<=strtotime($select_to_date))
				{
					if($cbo_store_name>0)
					{
						if($cbo_store_name==$row[csf("store_id")]) $prod_wise_trans_date[$row[csf("prod_id")]]["transaction_date"].=$row[csf("transaction_date")].',';
					}
					else
					{
						$prod_wise_trans_date[$row[csf("prod_id")]]["transaction_date"].=$row[csf("transaction_date")].',';
					}
				}
			}
		}
		$i=1;
		//echo "<pre>";print_r($details_data);die;
		//ob_start();
		?>
		<table style="margin-left:5px; margin-top:5px" id="table_notes" align="center">
            <tr>
                <td bgcolor="red" height="15" width="30"></td>
                <td>RED Color= 0.00</td>
                <td bgcolor="#FFA500" height="15" width="30">&nbsp;</td>
                <td>ORANGE Color= 1-10.00</td>
            </tr>
        </table>

		<table style="width:<? echo $table_width; ?>px" border="1" cellpadding="2" cellspacing="0" id="table_header_1" rules="all" align="left">
				<tr>
					<th colspan="27"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
				</tr>
		<table>
		<? ob_start(); ?>
		<div>
			<table style="width:<? echo $table_width; ?>px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_header_1" rules="all" align="left">
				<thead>
					<tr class="form_caption" style="border:none;">
						<td colspan="27" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td>
					</tr>
					<tr style="border:none;">
						<td colspan="27" align="center" style="border:none; font-size:14px;">
							<b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="27" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
						</td>
					</tr>
					<tr>
						<th rowspan="2" width="40">SL</th>
						<th colspan="11">Description</th>
						<th rowspan="2" width="100">Opening Stock</th>
                        <?
						if($variable_string_inventory !=1 )
						{
							?>
							<th rowspan="2" width="100">Openning Rate</th>
							<th rowspan="2" width="100">Openning Value</th>
							<?
						}
						?>
						<th colspan="5">Receive</th>
						<th colspan="5">Issue</th>
						<th rowspan="2" width="100">Closing Stock</th>
                        <?
						if($variable_string_inventory !=1 )
						{
							?>
							<th rowspan="2" width="100">Avg. Rate</th>
							<th rowspan="2" width="100">Stock Value</th>
							<?
						}
						?>

						<?
						if($cbo_yes_no==1)
						{
							?>
							<th rowspan="2" width="120">Store Name</th>
							<?
						}
						?>
						<th width="100" rowspan="2">Age [Days]</th>
						<th width="100" rowspan="2">DOH</th>
					</tr>
					<tr>
						<th width="60">Prod.ID</th>
						<th width="100">Item Number</th>
						<th width="60">Item Code</th>
						<th width="110">Item Category</th>
						<th width="100">Item Group</th>
						<th width="100">Item Sub-group</th>
						<th width="180">Item Description</th>
						<th width="100">Re-Order Level</th>
						<th width="70">Item Size</th>
						<th width="70">Model</th>
						<th width="60">UoM</th>
						<th width="80">Purchase</th>
						<th width="80">Issue Return</th>
						<th width="80">Transfer In</th>
                        <th width="80">Loan Received</th>
						<th width="100">Total Received</th>
						<th width="80">Issue</th>
                        <th width="80">As Loan</th>
						<th width="80">Receive Return</th>
						<th width="80">Transfer Out</th>
						<th width="100">Total Issue</th>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $div_width; ?>px; max-height:350px; overflow-y:scroll;" id="scroll_body"  >
				<table style="width:<? echo $table_width; ?>px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left">
					<?
					//echo "<pre>";print_r($details_data);die;
					foreach($details_data as $prod_id=>$row)
					{
						$stockValue=$closingStock=0;
						$min_transaction_date="";
						$transaction_date_arr=explode(',',rtrim($prod_wise_trans_date[$prod_id]["transaction_date"],','));
						$minnimum_trans_date=strtotime($transaction_date_arr[0]);
						foreach($transaction_date_arr as $trans_date)
						{
							$transaction_date=strtotime($trans_date);
							if ($transaction_date<$minnimum_trans_date) $min_transaction_date=$transaction_date;
							else $min_transaction_date=$minnimum_trans_date;
						}

						$ageOfDays=0;
						if($min_transaction_date!="") $ageOfDays = datediff("d", date('d-M-Y', $min_transaction_date), date("Y-m-d",strtotime($select_to_date)));
						//echo rtrim($prod_wise_trans_date[$prod_id]["transaction_date"],',').'system';
						$openingBalance = $row[("opening_total_receive")]-$row[("opening_total_issue")];
						$openingBalanceValue = $row[("opening_total_receive_amt")]-$row[("opening_total_issue_amt")];
						$opening_prod_rate=$openingBalanceValue/$openingBalance;
						$totalReceive = $row[("purchase")]+$row[("issue_return")]+$row[("transfer_in")]+$row[("loan_rcv")];
						$totalIssue =$row[("issue")]+$row[("receive_return")]+$row[("transfer_out")]+$row[("loan_iss")];
						$closingStock=$totalReceive-$totalIssue+$openingBalance;
						
						$openingBalanceValue = $row[("opening_total_receive_amt")]-$row[("opening_total_issue_amt")];
						$totalReceiveValue = $row[("total_rcv_amt_value")];
						$totalIssueValue = $row[('total_iss_amt_value')];
						$stockValue=0;
						$stockValue=($openingBalanceValue + $totalReceiveValue) - $totalIssueValue ;
						$prod_rate=$stockValue/$closingStock;
						$re_order_label = $row[('re_order_label')];
						if($value_with ==1)
						{
							if(number_format($openingBalance,2) > 0.00 || number_format($closingStock,2) > 0.00 )
							{
								if($closingStock <= $re_order_label){$bgcolor="red";}
								//elseif (number_format($closingStock,2) == 0){$bgcolor="#E9F3FF";}
								elseif (number_format($closingStock,2,'.','') > 0 &&  number_format($closingStock,2,'.','')<11){ $bgcolor="#FFA500";}
		                        else{
									if($i%2==0){$bgcolor="#E9F3FF";
                                    }else{
							     	$bgcolor="#FFFFFF";
							    	}
								}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40" align="center" ><? echo $i; ?></td>
									<td style="word-break:break-all" width="60" align="center"><p><? echo $row[("prod_id")]; ?></p></td>
									<td style="word-break:break-all" width="100" align="center"><p><? echo $row[("item_number")]; ?></p></td>
									<td style="word-break:break-all" width="60" align="center"><p><? echo $row[("item_code")]; ?></p></td>
									<td style="word-break:break-all" width="110"><p><? echo $item_category[$row[("item_category_id")]]; ?></p></td>
									<td style="word-break:break-all" width="100"><p><? echo $itemgroupArr[$row[("item_group_id")]]; ?></p></td>
									<td style="word-break:break-all" width="100"><p><? echo $row[("sub_group_name")]; ?></p></td>
									<td style="word-break:break-all" width="180"><p><? echo $row[("item_description")]; ?></p></td>
									<td align="right" style="word-break:break-all" width="100"><p><? echo $row[("re_order_label")]; ?>&nbsp;</p></td>
									<td style="word-break:break-all" width="70"><p><? echo $row[("item_size")]; ?>&nbsp;</p></td>
									<td style="word-break:break-all" width="70"><p><? echo $row[("model")]; ?></p></td>
									<td style="word-break:break-all" width="60" align="center"><p><? echo $unit_of_measurement[$row[("unit_of_measure")]]; ?></p></td>
									<td style="word-break:break-all" width="100" align="right"><? echo  number_format($openingBalance,2,'.','');//$openingBalance ?></td>
                                    <?
                                    if($variable_string_inventory!=1)
									{
										$opening_prod_rate=$openingBalanceValue/$openingBalance;
										?>
                                        <td style="word-break:break-all" width="100" align="right">
										<?
										if(number_format($openingBalance,2)>0) echo number_format($opening_prod_rate,4,'.',''); else echo "0.00";

										?>
                                        </td>
                                        <td style="word-break:break-all" width="100" align="right"><? if(number_format($openingBalance,2)>0) echo number_format($openingBalanceValue,2,'.',''); else echo "0.00"; ?></td>
										<?
										if(number_format($openingBalance,2)>0) $total_opening_stockValue+=$openingBalanceValue;
									}
									?>
									<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("purchase")],2,'.',''); ?></td>
									<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("issue_return")],2,'.',''); ?></td>
									<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("transfer_in")],2,'.',''); ?></td>
                                    <td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("loan_rcv")],2,'.',''); ?></td>
									<td style="word-break:break-all" width="100" align="right"><? echo number_format($totalReceive,2,'.',''); ?></td>
									<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("issue")],2,'.',''); ?></td>
                                    <td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("loan_iss")],2,'.',''); ?></td>
									<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("receive_return")],2,'.',''); ?></td>
									<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("transfer_out")],2,'.',''); ?></td>
									<td style="word-break:break-all" width="100" align="right"><? echo number_format($totalIssue,2,'.',''); ?></td>
									<td style="word-break:break-all" width="100" align="right"  title="<? echo $openingBalance;?>"><? echo number_format($closingStock,2,'.',''); ?></td>
                                    <?
                                    if($variable_string_inventory!=1)
									{
										?>
                                        <td style="word-break:break-all" width="100" align="right" title="<? echo $openingBalanceValue;?>">
										<?
										if(number_format($closingStock,2)>0) echo number_format($prod_rate,4,'.',''); else echo "0.00";
										?>
                                        </td>
                                        <td style="word-break:break-all" width="100" align="right" title="<? echo number_format($openingBalanceValue,2,'.','')."=".number_format($totalReceiveValue,2,'.','')."=".number_format($totalIssueValue,2,'.',''); ?>"><? if(number_format($closingStock,2)>0) echo number_format($stockValue,2,'.',''); else echo "0.00"; ?></td>
										<?
										if(number_format($closingStock,2)>0) $total_stockValue+=$stockValue;

										$total_receive_value+=$totalReceiveValue;
										$total_issue_value+=$totalIssueValue;
									}

									if($cbo_yes_no==1)
									{
									?>
									<td width="120" style="word-break:break-all"><p><? echo $store_name_arr[$row[("trans_store")]]; ?></p></td>
									<?
									}
									?>
									<td align="center" width="100" style="word-break:break-all"><? echo $ageOfDays; ?></td>
									<td align="center" width="100" style="word-break:break-all"><? if(number_format($closingStock,2)>0) echo $days_doh[$row[('prod_id')]]['daysonhand']; else echo "0"; ?></td>
								</tr>
								<?
								$total_openingBalance+=$openingBalance;
								$total_purchase+=$row[("purchase")];
								$total_issue_return+=$row[("issue_return")];
								$total_transfer_in+=$row[("transfer_in")];
								$total_loan_rcv+=$row[("loan_rcv")];
								$total_receive_qnty+=$totalReceive;
								$total_issue_qnty+=$row[("issue")];
								$total_loan_iss+=$row[("loan_iss")];
								$total_receive_return_qnty+=$row[("receive_return")];
								$total_transfer_out+=$row[("transfer_out")];
								$total_issue_recv_return_qnty+=$totalIssue;
								$total_closing_stock+=$closingStock;
								$i++;
							}
						}
						else
						{
							if($closingStock <= $re_order_label){$bgcolor="red";}elseif($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="40" align="center"><? echo $i; ?></td>
								<td style="word-break:break-all" width="60" align="center"><p><? echo $row[("prod_id")]; ?></p></td>
								<td style="word-break:break-all" width="100" align="center"><p><? echo $row[("item_number")]; ?></p></td>
								<td style="word-break:break-all" width="60" align="center"><p><? echo $row[("item_code")]; ?></p></td>
								<td style="word-break:break-all" width="110"><p><? echo $item_category[$row[("item_category_id")]]; ?></p></td>
								<td style="word-break:break-all" width="100"><p><? echo $itemgroupArr[$row[("item_group_id")]]; ?></p></td>
								<td style="word-break:break-all" width="100"><p><? echo $row[("sub_group_name")]; ?></p></td>
								<td style="word-break:break-all" width="180"><p><? echo $row[("item_description")]; ?></p></td>
								<td align="right" style="word-break:break-all" width="100"><p><? echo $row[("re_order_label")]; ?></p></td>
								<td style="word-break:break-all" width="70"><p><? echo $row[("item_size")]; ?>&nbsp;</p></td>
								<td style="word-break:break-all" width="70"><p><? echo $row[("model")]; ?></p></td>
								<td style="word-break:break-all" width="60" align="center"><p><? echo $unit_of_measurement[$row[("unit_of_measure")]]; ?></p></td>
								<td style="word-break:break-all" width="100" align="right"><? echo  number_format($openingBalance,2,'.','');//$openingBalance ?></td>
								<?
								if($variable_string_inventory!=1)
								{
									$opening_prod_rate=$openingBalanceValue/$openingBalance;
									?>
									<td style="word-break:break-all" width="100" align="right">
									<?
									if(number_format($openingBalance,2)>0) echo number_format($opening_prod_rate,4,'.',''); else echo "0.00";

									?>
									</td>
									<td style="word-break:break-all" width="100" align="right" title="<? echo number_format($openingBalanceValue,2,'.','')."=".number_format($totalReceiveValue,2,'.','')."=".number_format($totalIssueValue,2,'.',''); ?>"><? echo number_format($openingBalanceValue,2,'.',''); ?></td>
									<?
									$total_opening_stockValue+=$openingBalanceValue;
								}
								?>
								<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("purchase")],2,'.',''); ?></td>
								<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("issue_return")],2,'.',''); ?></td>
								<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("transfer_in")],2,'.',''); ?></td>
								<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("loan_rcv")],2,'.',''); ?></td>
								<td style="word-break:break-all" width="100" align="right"><? echo number_format($totalReceive,2,'.',''); ?></td>
								<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("issue")],2,'.',''); ?></td>
								<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("loan_iss")],2,'.',''); ?></td>
								<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("receive_return")],2,'.',''); ?></td>
								<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("transfer_out")],2,'.',''); ?></td>
								<td style="word-break:break-all" width="100" align="right"><? echo number_format($totalIssue,2,'.',''); ?></td>
								<td style="word-break:break-all" width="100" align="right"  title="<? echo $openingBalance;?>"><? echo number_format($closingStock,2,'.',''); ?></td>
								<?
								if($variable_string_inventory!=1)
								{
									?>
									<td style="word-break:break-all" width="100" align="right" title="<? echo $openingBalanceValue;?>">
									<?
									if(number_format($closingStock,2)>0) echo number_format($prod_rate,4,'.',''); else echo "0.00";
									?>
									</td>
									<td style="word-break:break-all" width="100" align="right" title="<? echo number_format($openingBalanceValue,2,'.','')."=".number_format($totalReceiveValue,2,'.','')."=".number_format($totalIssueValue,2,'.',''); ?>"><? if(number_format($closingStock,2)>0) echo number_format($stockValue,2,'.',''); else echo "0.00"; ?></td>
									<?
									$total_stockValue+=$stockValue;
									$total_receive_value+=$totalReceiveValue;
									$total_issue_value+=$totalIssueValue;
								}
								if($cbo_yes_no==1)
								{
								?>
								<td width="120" style="word-break:break-all"><p><? echo $store_name_arr[$row[("trans_store")]]; ?></p></td>
								<?
								}
								?>
								<td width="100" align="center" style="word-break:break-all"><? echo $ageOfDays; ?></td>
								<td width="100" align="center" style="word-break:break-all"><? if(number_format($closingStock,2)>0) echo $days_doh[$row[('prod_id')]]['daysonhand']; else echo "0"; ?></td>
							</tr>
							<?
							$total_openingBalance+=$openingBalance;
							$total_purchase+=$row[("purchase")];
							$total_issue_return+=$row[("issue_return")];
							$total_transfer_in+=$row[("transfer_in")];
							$total_loan_rcv+=$row[("loan_rcv")];
							$total_receive_qnty+=$totalReceive;
							$total_issue_qnty+=$row[("issue")];
							$total_loan_iss+=$row[("loan_iss")];
							$total_receive_return_qnty+=$row[("receive_return")];
							$total_transfer_out+=$row[("transfer_out")];
							$total_issue_recv_return_qnty+=$totalIssue;
							$total_closing_stock+=$closingStock;
							$i++;
						}
					}
					?>
					<tr bgcolor="#CCCCCC" style="font-weight:bold">
						<td colspan="12" align="right" style="word-break:break-all"><strong>Grand Total</strong></td>
						<td  align="right" style="word-break:break-all" ><? echo number_format($total_openingBalance,2,'.',''); ?></td>
                        <?
						if($variable_string_inventory!=1)
						{
							?>
                            <td>&nbsp;</td>
                            <td  align="right" style="word-break:break-all"><? echo number_format($total_opening_stockValue,2,'.',''); ?></td>
                            <?
						}
						?>
						<td align="right" style="word-break:break-all"><? echo number_format($total_purchase,2,'.',''); ?></td>
						<td align="right" style="word-break:break-all"><? echo number_format($total_issue_return,2,'.',''); ?></td>
						<td align="right" style="word-break:break-all"><? echo number_format($total_transfer_in,2,'.',''); ?></td>
                        <td align="right" style="word-break:break-all"><? echo number_format($total_loan_rcv,2,'.',''); ?></td>
						<td align="right" style="word-break:break-all"><? echo number_format($total_receive_qnty,2,'.',''); ?></td>
						<td align="right" style="word-break:break-all"><? echo number_format($total_issue_qnty,2,'.',''); ?></td>
                        <td align="right" style="word-break:break-all"><? echo number_format($total_loan_iss,2,'.',''); ?></td>
						<td align="right" style="word-break:break-all"><? echo number_format($total_receive_return_qnty,2,'.',''); ?></td>

						<td align="right" style="word-break:break-all"><? echo number_format($total_transfer_out,2,'.',''); ?></td>
						<td  align="right" style="word-break:break-all"><? echo number_format($total_issue_recv_return_qnty,2,'.',''); ?></td>
						<td  align="right" style="word-break:break-all"><? echo number_format($total_closing_stock,2,'.',''); ?></td>
                        <?
						if($variable_string_inventory!=1)
						{
							$total_closing_value=(number_format($total_opening_stockValue,2,'.','')+number_format($total_receive_value,2,'.',''))-number_format($total_issue_value,2,'.','');
							?>
                            <td>&nbsp;</td>
                            <td  align="right" style="word-break:break-all" title="<? echo "total open value=".$total_opening_stockValue.", total rcv value=".$total_receive_value.", total issue value=".$total_issue_value; ?>"><? echo number_format($total_closing_value,2,'.',''); ?></td>
                            <?
						}
						if($cbo_yes_no==1)
						{
							?>
							<td>&nbsp;</td>
							<?
						}
						?>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</table>
			</div>
		</div>
        <?
    }
    else if($report_type == 4)
	{
		if($cbo_yes_no==1)
		{
			if($variable_string_inventory!=1)
			{
				$div_width="2740";
				$table_width="2760";
			}
			else
			{
				$div_width="2280";
				$table_width="2260";
			}

		}
		else
		{
			if($variable_string_inventory!=1)
			{
				$div_width="2660";
				$table_width="2640";
			}
			else
			{
				$div_width="2160";
				$table_width="2140";
			}
		}
		//$cbo_company_name
		//echo "select id from inv_receive_master where status_active=1 and is_deleted=0 and company_id=$cbo_company_name and entry_form=20 and receive_purpose=1";die;
		$loanRcvData = return_library_array("select id from inv_receive_master where status_active=1 and is_deleted=0 and company_id=$cbo_company_name and entry_form=20 and receive_purpose=1","id","id");
		//print_r($loanRcvData);die;
		$loanIssueData = return_library_array("select id from inv_issue_master where status_active=1 and is_deleted=0 and company_id=$cbo_company_name and entry_form=21 and issue_purpose=5","id","id");
		if($variable_store_wise_rate==1)
		{
			$sql="Select a.prod_id, a.store_id, b.id, b.item_number, b.item_code, b.item_category_id, b.item_group_id, b.avg_rate_per_unit, b.sub_group_name, b.item_description, b.item_size, b.unit_of_measure, a.transaction_type, a.mst_id, b.re_order_label, a.transaction_date, a.balance_qnty,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.cons_quantity else 0 end) as opening_total_receive,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.store_amount else 0 end) as opening_total_receive_amt,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.cons_quantity else 0 end) as opening_total_issue,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.store_amount else 0 end) as opening_total_issue_amt,
			(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (1,4,5)  then a.store_amount else 0 end) as total_rcv_amount,
			(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (2,3,6)  then a.store_amount else 0 end) as total_issue_amount,
			(case when a.transaction_type=1 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as purchase,
			(case when a.transaction_type=3 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as receive_return,
			(case when a.transaction_type=2 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue,
			(case when a.transaction_type=4 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue_return ,
			(case when a.transaction_type=5 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_in,
			(case when a.transaction_type=6 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_out,
			(case when a.transaction_type in (1,4,5) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.store_amount else 0 end) as total_rcv_amt_value,
			(case when a.transaction_type in (2,3,6) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.store_amount else 0 end) as total_iss_amt_value,
			(case when a.transaction_type =2 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.ITEM_RETURN_QTY else 0 end) as ITEM_RETURN_QTY,
			(case when a.transaction_type =2 and a.item_return_qty!=0  and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as non_return_qty 
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.transaction_type in (1,2,3,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category in($item_cate_credential_cond) and b.entry_form<>24 $str_cond $zero_cond
			order by b.item_category_id, b.item_group_id, a.id";
		}
		else
		{
			$sql="Select a.prod_id, a.store_id, b.id, b.item_number, b.item_code, b.item_category_id, b.item_group_id, b.avg_rate_per_unit, b.sub_group_name, b.item_description, b.item_size, b.unit_of_measure, a.transaction_type, a.mst_id, b.re_order_label, a.transaction_date, a.balance_qnty,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.cons_quantity else 0 end) as opening_total_receive,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.cons_amount else 0 end) as opening_total_receive_amt,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.cons_quantity else 0 end) as opening_total_issue,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.cons_amount else 0 end) as opening_total_issue_amt,
			(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (1,4,5)  then a.cons_amount else 0 end) as total_rcv_amount,
			(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (2,3,6)  then a.cons_amount else 0 end) as total_issue_amount,
			(case when a.transaction_type=1 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as purchase,
			(case when a.transaction_type=3 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as receive_return,
			(case when a.transaction_type=2 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue,
			(case when a.transaction_type=4 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue_return ,
			(case when a.transaction_type=5 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_in,
			(case when a.transaction_type=6 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_out,
			(case when a.transaction_type in (1,4,5) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_amount else 0 end) as total_rcv_amt_value,
			(case when a.transaction_type in (2,3,6) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_amount else 0 end) as total_iss_amt_value,
			(case when a.transaction_type =2 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.ITEM_RETURN_QTY else 0 end) as ITEM_RETURN_QTY,
			(case when a.transaction_type =2 and a.item_return_qty!=0  and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as non_return_qty 
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.transaction_type in (1,2,3,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category in($item_cate_credential_cond) and b.entry_form<>24 $str_cond $zero_cond
			order by b.item_category_id, b.item_group_id, a.id";
		}

		//echo  $sql;//die; --and a.prod_id=4660 transaction_date
		$result = sql_select($sql);
		$data_arr=array();
		foreach($result as $row)
		{

			if($cbo_store_name>0)
			{
				if($cbo_store_name==$row[csf("store_id")])
				{
					$details_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
					$details_data[$row[csf("prod_id")]]["trans_store"]=$row[csf("store_id")];
					$details_data[$row[csf("prod_id")]]["item_number"]=$row[csf("item_number")];
					$details_data[$row[csf("prod_id")]]["item_code"]=$row[csf("item_code")];
					$details_data[$row[csf("prod_id")]]["item_category_id"]=$row[csf("item_category_id")];
					$details_data[$row[csf("prod_id")]]["item_group_id"]=$row[csf("item_group_id")];
					$details_data[$row[csf("prod_id")]]["avg_rate_per_unit"]=$row[csf("avg_rate_per_unit")];
					$details_data[$row[csf("prod_id")]]["sub_group_name"]=$row[csf("sub_group_name")];
					$details_data[$row[csf("prod_id")]]["item_description"]=$row[csf("item_description")];
					$details_data[$row[csf("prod_id")]]["item_size"]=$row[csf("item_size")];
					$details_data[$row[csf("prod_id")]]["unit_of_measure"]=$row[csf("unit_of_measure")];
					$details_data[$row[csf("prod_id")]]["re_order_label"]=$row[csf("re_order_label")];

					$details_data[$row[csf("prod_id")]]["opening_total_receive"]+=$row[csf("opening_total_receive")];
					$details_data[$row[csf("prod_id")]]["opening_total_receive_amt"]+=$row[csf("opening_total_receive_amt")];
					$details_data[$row[csf("prod_id")]]["opening_total_issue"]+=$row[csf("opening_total_issue")];
					$details_data[$row[csf("prod_id")]]["opening_total_issue_amt"]+=$row[csf("opening_total_issue_amt")];
					$details_data[$row[csf("prod_id")]]["total_rcv_amount"]+=$row[csf("total_rcv_amount")];
					$details_data[$row[csf("prod_id")]]["item_return_qty"]+=$row[csf("item_return_qty")];
					$details_data[$row[csf("prod_id")]]["non_return_qty"]+=$row[csf("non_return_qty")];
					//$details_data[$row[csf("prod_id")]]["total_issue_amount"] +=$row[csf("total_issue_amount")];
					if($loanRcvData[$row[csf("mst_id")]])
					{
						$details_data[$row[csf("prod_id")]]["loan_rcv"]+=$row[csf("purchase")];
					}
					else
					{
						$details_data[$row[csf("prod_id")]]["purchase"]+=$row[csf("purchase")];
					}
					$details_data[$row[csf("prod_id")]]["receive_return"]+=$row[csf("receive_return")];

					if($loanIssueData[$row[csf("mst_id")]])
					{
						$details_data[$row[csf("prod_id")]]["loan_iss"]+=$row[csf("issue")];
					}
					else
					{
						$details_data[$row[csf("prod_id")]]["issue"]+=$row[csf("issue")];
					}
					$details_data[$row[csf("prod_id")]]["issue_return"]+=$row[csf("issue_return")];
					$details_data[$row[csf("prod_id")]]["transfer_in"]+=$row[csf("transfer_in")];
					$details_data[$row[csf("prod_id")]]["transfer_out"]+=$row[csf("transfer_out")];
					$details_data[$row[csf("prod_id")]]["total_rcv_amt_value"]+=$row[csf("total_rcv_amt_value")];
					$details_data[$row[csf("prod_id")]]["total_iss_amt_value"]+=$row[csf("total_iss_amt_value")];
					$details_data[$row[csf("prod_id")]]["mst_id"]=$row[csf("mst_id")];
				}

				if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5)
				{
					$item_wise_stock[$row[csf("prod_id")]]["bal_qnty"]+=($row[csf("opening_total_receive")]+$row[csf("purchase")]+$row[csf("issue_return")]+$row[csf("transfer_in")]);
					$item_wise_stock[$row[csf("prod_id")]]["bal_amt"]+=($row[csf("opening_total_receive_amt")]+$row[csf("total_rcv_amt_value")]);
					$item_wise_stock[$row[csf("prod_id")]]["bal_qnty_opeinng"]+=$row[csf("opening_total_receive")];
					$item_wise_stock[$row[csf("prod_id")]]["bal_amt_opening"]+=$row[csf("opening_total_receive_amt")];
				}
				else
				{
					$item_wise_stock[$row[csf("prod_id")]]["bal_qnty"]-=($row[csf("opening_total_issue")]+$row[csf("issue")]+$row[csf("receive_return")]+$row[csf("transfer_out")]);
					$item_wise_stock[$row[csf("prod_id")]]["bal_amt"]-=($row[csf("opening_total_issue_amt")]+$row[csf("total_iss_amt_value")]);
					$item_wise_stock[$row[csf("prod_id")]]["bal_qnty_opeinng"]-=$row[csf("opening_total_issue")];
					$item_wise_stock[$row[csf("prod_id")]]["bal_amt_opening"]-=$row[csf("opening_total_issue_amt")];
				}

			}
			else
			{
				$details_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
				$details_data[$row[csf("prod_id")]]["trans_store"]=$row[csf("store_id")];
				$details_data[$row[csf("prod_id")]]["item_number"]=$row[csf("item_number")];
				$details_data[$row[csf("prod_id")]]["item_code"]=$row[csf("item_code")];
				$details_data[$row[csf("prod_id")]]["item_category_id"]=$row[csf("item_category_id")];
				$details_data[$row[csf("prod_id")]]["item_group_id"]=$row[csf("item_group_id")];
				$details_data[$row[csf("prod_id")]]["avg_rate_per_unit"]=$row[csf("avg_rate_per_unit")];
				$details_data[$row[csf("prod_id")]]["sub_group_name"]=$row[csf("sub_group_name")];
				$details_data[$row[csf("prod_id")]]["item_description"]=$row[csf("item_description")];
				$details_data[$row[csf("prod_id")]]["item_size"]=$row[csf("item_size")];
				$details_data[$row[csf("prod_id")]]["unit_of_measure"]=$row[csf("unit_of_measure")];
				$details_data[$row[csf("prod_id")]]["re_order_label"]=$row[csf("re_order_label")];

				$details_data[$row[csf("prod_id")]]["opening_total_receive"]+=$row[csf("opening_total_receive")];
				$details_data[$row[csf("prod_id")]]["opening_total_receive_amt"]+=$row[csf("opening_total_receive_amt")];
				$details_data[$row[csf("prod_id")]]["opening_total_issue"]+=$row[csf("opening_total_issue")];
				$details_data[$row[csf("prod_id")]]["opening_total_issue_amt"]+=$row[csf("opening_total_issue_amt")];
				$details_data[$row[csf("prod_id")]]["total_rcv_amount"]+=$row[csf("total_rcv_amount")];
				$details_data[$row[csf("prod_id")]]["item_return_qty"]+=$row[csf("item_return_qty")];
				$details_data[$row[csf("prod_id")]]["non_return_qty"]+=$row[csf("non_return_qty")];
				//$details_data[$row[csf("prod_id")]]["total_issue_amount"] +=$row[csf("total_issue_amount")];
				if($loanRcvData[$row[csf("mst_id")]])
				{
					$details_data[$row[csf("prod_id")]]["loan_rcv"]+=$row[csf("purchase")];
				}
				else
				{
					$details_data[$row[csf("prod_id")]]["purchase"]+=$row[csf("purchase")];
				}
				$details_data[$row[csf("prod_id")]]["receive_return"]+=$row[csf("receive_return")];

				if($loanIssueData[$row[csf("mst_id")]])
				{
					$details_data[$row[csf("prod_id")]]["loan_iss"]+=$row[csf("issue")];
				}
				else
				{
					$details_data[$row[csf("prod_id")]]["issue"]+=$row[csf("issue")];
				}
				$details_data[$row[csf("prod_id")]]["issue_return"]+=$row[csf("issue_return")];
				$details_data[$row[csf("prod_id")]]["transfer_in"]+=$row[csf("transfer_in")];
				$details_data[$row[csf("prod_id")]]["transfer_out"]+=$row[csf("transfer_out")];
				$details_data[$row[csf("prod_id")]]["total_rcv_amt_value"]+=$row[csf("total_rcv_amt_value")];
				$details_data[$row[csf("prod_id")]]["total_iss_amt_value"]+=$row[csf("total_iss_amt_value")];
				$details_data[$row[csf("prod_id")]]["mst_id"]=$row[csf("mst_id")];
			}
			
			if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==5 && strtotime($row[csf("transaction_date")])<=strtotime($select_to_date))
			{
				if($cbo_store_name>0)
				{
					if($cbo_store_name==$row[csf("store_id")]) 
					{
						$prod_wise_trans_date[$row[csf("prod_id")]]["transaction_date"].=$row[csf("transaction_date")].',';
					}
				}
				else
				{
					$prod_wise_trans_date[$row[csf("prod_id")]]["transaction_date"].=$row[csf("transaction_date")].',';
				}
			}
		}
		//echo "<pre>";print_r($prod_wise_trans_date);die;

		$i=1;
		// ob_start();$days_doh[$row_d[csf('prod_id')]]['last_trans_date']
		?>
		<div>
			<table style="width:<? echo $table_width +430; ?>px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_header_1" rules="all">
				<thead>
					<tr class="form_caption" style="border:none;">
						<td colspan="21" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td>
					</tr>
					<tr style="border:none;">
						<td colspan="21" align="center" style="border:none; font-size:14px;">
							<b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="21" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
						</td>
					</tr>
					<tr>
						<th rowspan="2" width="40">SL</th>
						<th colspan="9">Description</th>
						<th rowspan="2" width="100">Opening Rate</th>
						<th rowspan="2" width="100">Opening Stock</th>
						<th rowspan="2" width="100">Opening Value</th>
						<th colspan="<? if($variable_string_inventory!=1) echo 6; else echo 5; ?>">Receive</th>
						<th colspan="<? if($variable_string_inventory!=1) echo 6; else echo 5; ?>">Issue</th>
						<th rowspan="2" width="100">Closing Stock</th>
						<th rowspan="2" width="100">Used Return Qty</th>
						<th rowspan="2" width="100">Used Non Return Qty</th>
                        <?
                        if($variable_string_inventory!=1)
						{
							?>
                            <th rowspan="2" width="100">Avg. Rate</th>
                            <th rowspan="2" width="130">Stock Value</th>
                            <?
						}
						if($cbo_yes_no==1)
						{
						?>
						<th rowspan="2" width="120">Store Name</th>
						<?
						}
						?>
                        <th width="80" rowspan="2">Age [Days]</th>
						<th width="80" rowspan="2">DOH</th>
                        <th width="80" rowspan="2">Last Rcv. Date</th>
                        <th rowspan="2">Last trans. Date</th>
					</tr>
					<tr>
						<th width="60">Prod.ID</th>
						<th width="100">Item Number</th>
						<th width="60">Item Code</th>
						<th width="110">Item Category</th>
						<th width="100">Item Group</th>
						<th width="70">Item Sub-group</th>
						<th width="180">Item Description</th>
						<th width="70">Item Size</th>
						<th width="60">UoM</th>
						<th width="80">Purchase</th>
						<th width="80">Issue Return</th>
						<th width="80">Transfer In</th>
                        <th width="80">Loan Received</th>
						<th width="100">Total Received</th>
                        <?
						if($variable_string_inventory!=1)
						{
							?>
                            <th width="100">Total RCV Value</th>
                            <?
						}
						?>
						<th width="80">Issue</th>
                        <th width="80">As Loan</th>
						<th width="80">Receive Return</th>
						<th width="80">Transfer Out</th>
						<th width="100">Total Issue</th>
                        <?
						if($variable_string_inventory!=1)
						{
							?>
                            <th width="100">Total Issue Value</th>
                            <?
						}
						?>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $div_width + 430; ?>px; max-height:350px; overflow-y:scroll;" id="scroll_body"  >
				<table style="width:<? echo $table_width + 430; ?>px; float:left;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
					<?
					foreach($details_data as $prod_id=>$row)
					{
						if( $row[("transaction_type")]==1 || $row[("transaction_type")]==2 || $row[("transaction_type")]==3 || $row[("transaction_type")]==4 )
						$stylecolor='style="color:#A61000"';
						else
						$stylecolor='style="color:#000000"';
						
						$transaction_date_arr=explode(',',rtrim($prod_wise_trans_date[$prod_id]["transaction_date"],','));
						$minnimum_trans_date=strtotime($transaction_date_arr[0]);
						$max_transaction_date=strtotime($transaction_date_arr[0]);
						foreach($transaction_date_arr as $trans_date)
						{
							$transaction_date=strtotime($trans_date);
							if ($transaction_date<$minnimum_trans_date) $min_transaction_date=$transaction_date;
							else $min_transaction_date=$minnimum_trans_date;
							
							if ($transaction_date>$max_transaction_date) $max_transaction_date=$transaction_date;
							else $max_transaction_date=$max_transaction_date;
						}
						$ageOfDays=0;
						//if($min_transaction_date!="") $ageOfDays = datediff("d", date('d-M-Y', $min_transaction_date), date("Y-m-d"));
						if($min_transaction_date!="") $ageOfDays = datediff("d", date('d-M-Y', $max_transaction_date), date("Y-m-d"));
						
						$openingBalance = $row[("opening_total_receive")]-$row[("opening_total_issue")];
						$openingBalanceRate = $openingBalanceValue = 0;
						$openingBalanceValue = $row[("opening_total_receive_amt")]-$row[("opening_total_issue_amt")];
						$openingBalanceRate = $openingBalanceValue/$openingBalance;
						$totalReceive = $row[("purchase")]+$row[("issue_return")]+$row[("transfer_in")]+$row[("loan_rcv")];
						$totalIssue =$row[("issue")]+$row[("receive_return")]+$row[("transfer_out")]+$row[("loan_iss")];

						$non_return_qty = $row[("issue")] - $row[("non_return_qty")];
						$closingStock=($totalReceive-$totalIssue)+number_format($openingBalance,4,'.','');

						$totalReceiveValue = $row[("total_rcv_amt_value")];
						$totalIssueValue = $row[('total_iss_amt_value')];

						$closingStockValue = $openingBalanceValue + $totalReceiveValue - $totalIssueValue;


						$closingStockRate = $closingStockValue/$closingStock;
						$stockValue=$row[("total_rcv_amount")]-$row[('total_issue_amount')];
						$re_order_label = $row[('re_order_label')];
						if($closingStock <= $re_order_label){$bgcolor="red";}elseif($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
						$max_transaction_date=date("d-m-Y",$max_transaction_date);
						if($value_with ==1 )
						{
							if(number_format($openingBalance,2) > 0.00 || number_format($closingStock,2) > 0.00 )
							{
								$total_openingBalance+=$openingBalance;
								$total_openingBalanceValue+=$openingBalanceValue;
								$total_stockValue+=$stockValue;
								$total_purchase+=$row[("purchase")];
								$total_issue_return+=$row[("issue_return")];
								$total_transfer_in+=$row[("transfer_in")];
								$total_loan_rcv+=$row[("loan_rcv")];
								$total_receive_qnty+=$totalReceive;
								$total_issue_qnty+=$row[("issue")];
								$total_loan_iss+=$row[("loan_iss")];
								$total_receive_return_qnty+=$row[("receive_return")];
								$total_transfer_out+=$row[("transfer_out")];
								$total_return_qty+=$row[("item_return_qty")];
								$total_non_return_qty+=$non_return_qty;
								$total_issue_recv_return_qnty+=$totalIssue;
								$total_closing_stock+=$closingStock;
								//$prod_rate=$stockValue/$closingStock;
								$GrandtotalIssueValue+=$totalIssueValue;
								$total_closingStockValue+=$closingStockValue;
								$GrandtotalReceiveValue+=$totalReceiveValue;
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40" align="center"><? echo $i; ?></td>
									<td width="60" align="center"><p><? echo $row[("prod_id")]; ?>&nbsp;</p></td>
									<td width="100" align="center"><p><? echo $row[("item_number")]; ?>&nbsp;</p></td>
									<td width="60" align="center"><p><? echo $row[("item_code")]; ?>&nbsp;</p></td>
									<td width="110"><p><? echo $item_category[$row[("item_category_id")]]; ?>&nbsp;</p></td>
									<td width="100" style="word-break:break-all;"><p><? echo $itemgroupArr[$row[("item_group_id")]]; ?>&nbsp;</p></td>
									<td width="70"><p><? echo $row[("sub_group_name")]; ?>&nbsp;</p></td>
									<td width="180" style="word-break:break-all;"><p><? echo $row[("item_description")]; ?>&nbsp;</p></td>
									<td width="70" style="word-break:break-all;"><p><? echo $row[("item_size")]; ?>&nbsp;</p></td>
									<td width="60" align="center"><p><? echo $unit_of_measurement[$row[("unit_of_measure")]]; ?>&nbsp;</p></td>
									<td width="100" align="right">
									<?
									if(number_format($openingBalance,2)>0) echo number_format($openingBalanceRate,4); else echo "0.0000";
									?>
									</td>
									<td width="100" align="right"><? echo  number_format($openingBalance,2);//$openingBalance ?></td>
									<td width="100" style="text-align:right;word-break: break-all;"><p><? if(number_format($openingBalance,2)>0) echo number_format($openingBalanceValue,2); else echo "0.00";?></p></td>
									<td width="80" align="right" title="<? echo "mst_id=".$row[("mst_id")];?>"><p><? echo number_format($row[("purchase")],2); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($row[("issue_return")],2); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($row[("transfer_in")],2); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($row[("loan_rcv")],2); ?></p></td>
									<td width="100" align="right"><p><? echo number_format($totalReceive,2); ?></p></td>
									<?
									if($variable_string_inventory!=1)
									{
										?>
										<td width="100" align="right" style="word-break:break-all;"><p><? echo number_format($totalReceiveValue,2);?></p></td>
										<?
									}
									?>
									<td width="80" align="right"><? echo number_format($row[("issue")],2); ?></td>
									<td width="80" align="right"><p><? echo number_format($row[("loan_iss")],2); ?></p></td>
									<td width="80" align="right"><? echo number_format($row[("receive_return")],2); ?></td>
									<td width="80" align="right"><? echo number_format($row[("transfer_out")],2); ?></td>
									<td width="100" align="right"><? echo number_format($totalIssue,2); ?></td>
									<?
									if($variable_string_inventory!=1)
									{
										?>
										<td width="100" align="right" title="<? echo $row[("prod_id")] ?>"><? echo number_format($totalIssueValue,2);?></td>
										<?
									}
									?>

									<td width="100" align="right" title="<? echo $openingBalance;?>"><? echo number_format($closingStock,2); ?></td>
									<td width="100" align="right"><? echo  number_format($row[("item_return_qty")],2); ?></td> 
									<td width="100" align="right"><? echo number_format($non_return_qty,2); ?></td>
									<?
									if($variable_string_inventory!=1)
									{
										?>
										<td width="100" align="right">
										<?
										if(number_format($closingStock,2)>0) echo number_format($closingStockRate,4); else echo "0.00";
										?>
										</td>
										<td width="130" style="text-align:right; word-break: break-all;" title="<? echo $row[("total_rcv_amount")]."==".$row[("total_issue_amount")]; ?>"><p><? if(number_format($closingStock,2)>0) echo number_format($closingStockValue,2); else echo "0.00"; ?></p></td>
										<?
									}
									if($cbo_yes_no==1)
									{
									?>
									<td width="120"><p><? echo $store_name_arr[$row[("trans_store")]]; ?>&nbsp;</p></td>
									<?
									}
									?>
									<td align="center" width="80"><? echo $ageOfDays; ?></td>
									<td align="center" width="80"><? echo $days_doh[$row[('prod_id')]]['daysonhand'];?></td>
                                    <td align="center" width="80"><? echo change_date_format($max_transaction_date);?></td>
                                    <td align="center"><? echo change_date_format($days_doh[$row[('prod_id')]]['last_trans_date']);?></td>
								</tr>
								<?
								$i++;
								$totalIssueValue=$closingStockValue=0;
							}
						}
						else
						{
							$total_openingBalance+=$openingBalance;
							$total_openingBalanceValue+=$openingBalanceValue;
							$total_stockValue+=$stockValue;
							$total_purchase+=$row[("purchase")];
							$total_issue_return+=$row[("issue_return")];
							$total_transfer_in+=$row[("transfer_in")];
							$total_loan_rcv+=$row[("loan_rcv")];
							$total_receive_qnty+=$totalReceive;
							$total_issue_qnty+=$row[("issue")];
							$total_loan_iss+=$row[("loan_iss")];
							$total_receive_return_qnty+=$row[("receive_return")];
							$total_transfer_out+=$row[("transfer_out")];
							$total_return_qty+=$row[("item_return_qty")];
							$total_non_return_qty+=$non_return_qty;
							$total_issue_recv_return_qnty+=$totalIssue;
							$total_closing_stock+=$closingStock;
							//$prod_rate=$stockValue/$closingStock;
							$GrandtotalIssueValue+=$totalIssueValue;
							$total_closingStockValue+=$closingStockValue;
							$GrandtotalReceiveValue+=$totalReceiveValue;
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="40" align="center"><? echo $i; ?></td>
								<td width="60" align="center"><p><? echo $row[("prod_id")]; ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo $row[("item_number")]; ?>&nbsp;</p></td>
								<td width="60" align="center"><p><? echo $row[("item_code")]; ?>&nbsp;</p></td>
								<td width="110"><p><? echo $item_category[$row[("item_category_id")]]; ?>&nbsp;</p></td>
								<td width="100" style="word-break:break-all;"><p><? echo $itemgroupArr[$row[("item_group_id")]]; ?>&nbsp;</p></td>
								<td width="70"><p><? echo $row[("sub_group_name")]; ?>&nbsp;</p></td>
								<td width="180" style="word-break:break-all;"><p><? echo $row[("item_description")]; ?>&nbsp;</p></td>
								<td width="70" style="word-break:break-all;"><p><? echo $row[("item_size")]; ?>&nbsp;</p></td>
								<td width="60" align="center"><p><? echo $unit_of_measurement[$row[("unit_of_measure")]]; ?>&nbsp;</p></td>
								<td width="100" align="right">
								<?
								if(number_format($openingBalance,2)>0) echo number_format($openingBalanceRate,4); else echo "0.0000";
								?>
								</td>
								<td width="100" align="right"><? echo  number_format($openingBalance,2);//$openingBalance ?></td>
								<td width="100" style="text-align:right;word-break: break-all;"><? if(number_format($openingBalance,2)>0) echo number_format($openingBalanceValue,2);  else echo "0.00";?></td>
								<td width="80" align="right" title="<? echo "mst_id=".$row[("mst_id")];?>"><p><? echo number_format($row[("purchase")],2); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($row[("issue_return")],2); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($row[("transfer_in")],2); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($row[("loan_rcv")],2); ?></p></td>
								<td width="100" align="right"><p><? echo number_format($totalReceive,2); ?></p></td>
								<?
								if($variable_string_inventory!=1)
								{
									?>
									<td width="100" align="right" style="word-break:break-all;"><p><? echo number_format($totalReceiveValue,2);?></p></td>
									<?
								}
								?>
								<td width="80" align="right"><? echo number_format($row[("issue")],2); ?></td>
								<td width="80" align="right"><p><? echo number_format($row[("loan_iss")],2); ?></p></td>
								<td width="80" align="right"><? echo number_format($row[("receive_return")],2); ?></td>
								<td width="80" align="right"><? echo number_format($row[("transfer_out")],2); ?></td>
								<td width="100" align="right"><? echo number_format($totalIssue,2); ?></td>
								<?
								if($variable_string_inventory!=1)
								{
									?>
									<td width="100" align="right" title="<? echo $row[("prod_id")] ?>"><? echo number_format($totalIssueValue,2);?></td>
									<?
								}
								?>

								<td width="100" align="right" title="<? echo $openingBalance;?>"><? echo number_format($closingStock,2); ?></td>

								<td width="100" align="right"><? echo  number_format($row[("item_return_qty")],2); ?></td> 
								<td width="100" align="right"><? echo number_format($non_return_qty,2); ?></td>


								<?
								if($variable_string_inventory!=1)
								{
									?>
									<td width="100" align="right">
									<?
									if(number_format($closingStock,2)>0) echo number_format($closingStockRate,4); else echo "0.00";
									?>
									</td>
									<td width="130" style="text-align:right; word-break: break-all;" title="<? echo $row[("total_rcv_amount")]."==".$row[("total_issue_amount")]; ?>"><p><? if(number_format($closingStock,2)>0) echo number_format($closingStockValue,2); else echo "0.00"; ?></p></td>
									<?
								}
								if($cbo_yes_no==1)
								{
								?>
								<td width="120"><p><? echo $store_name_arr[$row[("trans_store")]]; ?>&nbsp;</p></td>
								<?
								}
								?>
								<td align="center" width="80"><? echo $ageOfDays; ?></td>
                                <td align="center" width="80"><? echo $days_doh[$row[('prod_id')]]['daysonhand'];?></td>
                                <td align="center" width="80"><? echo change_date_format($max_transaction_date);?></td>
                                <td align="center"><? echo change_date_format($days_doh[$row[('prod_id')]]['last_trans_date']);?></td>
							</tr>
							<?
							$i++;
							$totalIssueValue=$closingStockValue=0;
						}
					}
					?>
					<tr bgcolor="#CCCCCC" style="font-weight:bold">
						<td colspan="10" align="right"><strong>Grand Total</strong></td>
						<td>&nbsp;</td>
						<td  align="right"><? echo number_format($total_openingBalance,2); ?></td>
						<td align="right"><? echo number_format($total_openingBalanceValue,2);?></td>
						<td align="right"><? echo number_format($total_purchase,2); ?></td>
						<td align="right"><? echo number_format($total_issue_return,2); ?></td>
						<td align="right"><? echo number_format($total_transfer_in,2); ?></td>
                        <td align="right"><? echo number_format($total_loan_rcv,2); ?></td>
						<td align="right"><? echo number_format($total_receive_qnty,2); ?></td>
                        <?
						if($variable_string_inventory!=1)
						{
							?>
                            <td align="right"><? echo number_format($GrandtotalReceiveValue,2); ?></td>
                            <?
						}
						?>
						<td align="right"><? echo number_format($total_issue_qnty,2); ?></td>
                        <td align="right"><? echo number_format($total_loan_iss,2); ?></td>
						<td align="right"><? echo number_format($total_receive_return_qnty,2); ?></td>
						<td align="right"><? echo number_format($total_transfer_out,2); ?></td>
						<td align="right"><? echo number_format($total_issue_recv_return_qnty,2); ?></td>
                        <?
						if($variable_string_inventory!=1)
						{
							?>
                            <td align="right"><? echo number_format($GrandtotalIssueValue,2); ?></td>
                            <?
						}
						?>
						<td align="right"><? echo number_format($total_closing_stock,2); ?></td>

						<td align="right"><? echo number_format($total_return_qty,2); ?></td>
						<td align="right"><? echo number_format($total_non_return_qty,2); ?></td>

		
                        <?
						if($variable_string_inventory!=1)
						{
							?>
                            <td>&nbsp;</td>
                            <td  align="right"><p><? echo number_format($total_closingStockValue,2); ?></p></td>
                            <?
						}
						if($cbo_yes_no==1)
						{
						?>
						<td>&nbsp;</td>
						<?
						}
						?>
						<td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
					</tr>
				</table>
			</div>
		</div>
		<?
	}
    else if($report_type == 5)
	{
		if($variable_store_wise_rate==1)
		{
			$sql="Select a.prod_id $store_field, b.id, b.item_number,b.item_code,b.store_id,b.item_category_id,b.item_group_id,b.avg_rate_per_unit,b.sub_group_name,b.item_description,b.item_size,b.unit_of_measure,
			b.re_order_label,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.cons_quantity else 0 end) as opening_total_receive,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.store_amount else 0 end) as opening_total_receive_amt,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.cons_quantity else 0 end) as opening_total_issue,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.store_amount else 0 end) as opening_total_issue_amt,
			sum(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (1,4,5)  then a.store_amount else 0 end) as total_rcv_amount,
			sum(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (2,3,6)  then a.store_amount else 0 end) as total_issue_amount,
			sum(case when a.transaction_type=1 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as purchase,
			sum(case when a.transaction_type=3 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as receive_return,
			sum(case when a.transaction_type=2 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue,
			sum(case when a.transaction_type=4 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue_return ,
			sum(case when a.transaction_type=5 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_in,
			sum(case when a.transaction_type=6 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_out,
			sum(case when a.transaction_type in (1,4,5) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.store_amount else 0 end) as total_rcv_amt_value,
			sum(case when a.transaction_type in (2,3,6) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.store_amount else 0 end) as total_iss_amt_value
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.transaction_type in (1,2,3,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category in($item_cate_credential_cond) and b.entry_form<>24 $str_cond $store_cond $zero_cond
			group by $strore_group a.prod_id, b.id, b.store_id, b.item_number, b.item_category_id, b.item_group_id, b.avg_rate_per_unit, b.sub_group_name, b.item_description, b.item_size, b.re_order_label, b.unit_of_measure, b.item_code
			order by b.item_category_id, b.item_group_id $store_order";
		}
		else
		{
			$sql="Select a.prod_id $store_field, b.id, b.item_number,b.item_code,b.store_id,b.item_category_id,b.item_group_id,b.avg_rate_per_unit,b.sub_group_name,b.item_description,b.item_size,b.unit_of_measure,
			b.re_order_label,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.cons_quantity else 0 end) as opening_total_receive,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.cons_amount else 0 end) as opening_total_receive_amt,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.cons_quantity else 0 end) as opening_total_issue,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.cons_amount else 0 end) as opening_total_issue_amt,
			sum(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (1,4,5)  then a.cons_amount else 0 end) as total_rcv_amount,
			sum(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (2,3,6)  then a.cons_amount else 0 end) as total_issue_amount,
			sum(case when a.transaction_type=1 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as purchase,
			sum(case when a.transaction_type=3 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as receive_return,
			sum(case when a.transaction_type=2 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue,
			sum(case when a.transaction_type=4 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue_return ,
			sum(case when a.transaction_type=5 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_in,
			sum(case when a.transaction_type=6 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_out,
			sum(case when a.transaction_type in (1,4,5) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_amount else 0 end) as total_rcv_amt_value,
			sum(case when a.transaction_type in (2,3,6) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_amount else 0 end) as total_iss_amt_value
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.transaction_type in (1,2,3,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category in($item_cate_credential_cond) and b.entry_form<>24 $str_cond $store_cond $zero_cond
			group by $strore_group a.prod_id, b.id, b.store_id, b.item_number, b.item_category_id, b.item_group_id, b.avg_rate_per_unit, b.sub_group_name, b.item_description, b.item_size, b.re_order_label, b.unit_of_measure, b.item_code
			order by b.item_category_id, b.item_group_id $store_order";
		}


		//echo  $sql;//die;
		$result = sql_select($sql);

		$item_category_array = array();
		foreach ($result as $key => $value) {
			$item_category_array[$value[csf('item_category_id')]][] = $value;
		}
		// ob_start();
		//echo '<pre>';print_r($item_category_array);
		if($variable_string_inventory!=1)
		{
			$div_width="1520";
			$table_width="1500";
		}
		else
		{
			$div_width="1020";
			$table_width="1000";
		}
	    ?>
	    <style>
	    	.wrd_brk{word-break: break-all;}
	    	.left{text-align: left;}
	    	.center{text-align: center;}
	    	.right{text-align: right;}
	    </style>
		<div style="width:<?=$div_width;?>px;">
			<table style="width:<?=$table_width;?>px; margin-right: 20px;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_header_1" rules="all">
				<thead>
					<tr style="border:none;">
						<td width="<?=$table_width;?>" colspan="<? if($variable_string_inventory!=1) echo 15; else echo 10;?>" class="center" style="border:none; font-size:14px; font-weight: bold;">
							<b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>
						</td>
					</tr>
					<tr style="border:none;">
						<td width="<?=$table_width;?>" colspan="<? if($variable_string_inventory!=1) echo 15; else echo 10;?>" class="center" style="border:none; font-size:14px;">
							<?
							    $nameArray=sql_select("select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website from lib_company where id=$cbo_company_name and status_active=1 and is_deleted=0");
							    foreach ($nameArray as $value) {
							    	echo $value[csf('city')];
							    }
							?>
						</td>
					</tr>
					<tr style="border:none;">
						<td width="<?=$table_width;?>" colspan="<? if($variable_string_inventory!=1) echo 15; else echo 10;?>" class="center" style="border:none; font-size:14px;">
						    <p>Monthly Inventory Statement</p>
						</td>
					</tr>
					<tr style="border:none;">
						<td width="<?=$table_width;?>" colspan="<? if($variable_string_inventory!=1) echo 15; else echo 10;?>" class="center" style="border:none; font-size:12px; text-decoration: underline;">
							<? if($from_date!="" || $to_date!="") echo "From Date: ".change_date_format($from_date,'dd-mm-yyyy')." To ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
						</td>
					</tr>
					<tr>
						<th width="40" class="wrd_brk">SL</th>
						<th width="100" class="wrd_brk">Item Category</th>
						<th width="100" class="wrd_brk">Item Number</th>
						<th width="100" class="wrd_brk">Item Code</th>
						<th width="100" class="wrd_brk">Item Sub-group</th>
						<th width="200" class="wrd_brk">Item Description</th>
						<th width="60" class="wrd_brk">UoM</th>
                        <?
						if($variable_string_inventory!=1)
						{
							?>
                            <th width="100" class="wrd_brk">Opening Value</th>
                            <?
						}
						?>
						<th width="100" class="wrd_brk">Received Qty</th>
                        <?
						if($variable_string_inventory!=1)
						{
							?>
                            <th width="100" class="wrd_brk">Received Value</th>
                            <?
						}
						?>
						<th width="100" class="wrd_brk">Issue Qty</th>
                        <?
						if($variable_string_inventory!=1)
						{
							?>
                            <th width="100" class="wrd_brk">Issue Value</th>
                            <?
						}
						?>
						<th width="100" class="wrd_brk">Closing Stock</th>
                        <?
						if($variable_string_inventory!=1)
						{
							?>
                            <th width="100" class="wrd_brk">Avg. Rate</th>
							<th width="100" class="wrd_brk">Stock Value</th>
                            <?
						}
						?>

					</tr>
				</thead>
			</table>
			<div style="width:<?=$div_width;?>px; max-height:350px; overflow-y:auto;" id="scroll_body">
				<table style="width:<?=$table_width;?>px; float:left;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
					<?
					$grand_total_openingBalanceValue = 0;
					$grand_total_receive_qnty = 0;
					$grand_GrandtotalReceiveValue = 0;
					$grand_total_issue_qnty = 0;
					$grand_GrandtotalIssueValue = 0;
					$grand_total_closing_stock = 0;
					$grand_total_closingStockValue = 0;

					foreach ($item_category_array as $key => $resault_data) {
						$i=1;
						$total_openingBalance = 0;
	                    $total_openingBalanceValue= 0;
	                    $total_stockValue= 0;
	                    $total_purchase= 0;
	                    $total_issue_return= 0;
	                    $total_transfer_in= 0;
	                    $total_receive_qnty= 0;
	                    $total_issue_qnty= 0;
	                    $total_receive_return_qnty= 0;
	                    $total_transfer_out= 0;
	                    $total_issue_recv_return_qnty= 0;
	                    $total_closing_stock= 0;
	                    $GrandtotalIssueValue= 0;
	                    $total_closingStockValue= 0;
	                    $GrandtotalReceiveValue= 0;
						foreach($resault_data as $row)
						{
			                if( $row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==4 )
			                    $stylecolor='style="color:#A61000"';
			                else
			                    $stylecolor='style="color:#000000"';
			                    $openingBalance = $row[csf("opening_total_receive")]-$row[csf("opening_total_issue")];
			                    $totalReceive = $row[csf("purchase")]+$row[csf("issue_return")]+$row[csf("transfer_in")];
			                    $totalIssue =$row[csf("issue")]+$row[csf("receive_return")]+$row[csf("transfer_out")];

			                    $closingStock=($totalReceive-$totalIssue)+number_format($openingBalance,4,'.','');

			                    $openingBalanceRate = $openingBalanceValue = 0;
			                    /*if($row[csf("opening_total_receive")] > 0){
			                        $openingBalanceRate = $row[csf("opening_total_receive_amt")]/$row[csf("opening_total_receive")];
			                    }
			                    $openingBalanceValue = $openingBalance *$openingBalanceRate;*/
								$openingBalanceValue=$row[csf("opening_total_receive_amt")]-$row[csf("opening_total_issue_amt")];
			                    $totalReceiveValue = $row[csf("total_rcv_amt_value")];
			                    $totalIssueValue = $row[csf('total_iss_amt_value')];
			                    $closingStockValue = $openingBalanceValue + $totalReceiveValue - $totalIssueValue;

			                    $closingStockRate = $closingStockValue/$closingStock;
			                    $stockValue=$row[csf("total_rcv_amount")]-$row[csf('total_issue_amount')];
			                    $re_order_label = $row[csf('re_order_label')];
			                    if($closingStock <= $re_order_label){$bgcolor="red";}elseif($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
			                    if(($value_with ==1 && (number_format($openingBalance,2) > 0.00 || number_format($closingStock,2) > 0.00 ) )    ||    ($value_with ==0 && (number_format($openingBalance,2) > 0.00 || number_format($closingStock,2) > 0.00 || number_format($totalReceive,2) > 0.00 || number_format($totalIssue,2) > 0.00 )))
					            {

				                    $total_openingBalance+=$openingBalance;
				                    $total_openingBalanceValue+=$openingBalanceValue;
				                    $total_stockValue+=$stockValue;
				                    $total_purchase+=$row[csf("purchase")];
				                    $total_issue_return+=$row[csf("issue_return")];
				                    $total_transfer_in+=$row[csf("transfer_in")];
				                    $total_receive_qnty+=$totalReceive;
				                    $total_issue_qnty+=$row[csf("issue")];
				                    $total_receive_return_qnty+=$row[csf("receive_return")];
				                    $total_transfer_out+=$row[csf("transfer_out")];
				                    $total_issue_recv_return_qnty+=$totalIssue;
				                    $total_closing_stock+=$closingStock;
				                    $GrandtotalIssueValue+=$totalIssueValue;
				                    $total_closingStockValue+=$closingStockValue;
				                    $GrandtotalReceiveValue+=$totalReceiveValue;
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="40" class="center wrd_brk"><? echo $i; ?></td>
										<td width="100" class="center wrd_brk"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
										<td width="100" class="center wrd_brk"><? echo $row[csf("item_number")]; ?></td>
										<td width="100" class="center wrd_brk"><? echo $row[csf("item_code")]; ?></td>
										<td width="100" class="center wrd_brk"><? echo $row[csf("sub_group_name")]; ?></td>
										<td width="200" class="center wrd_brk"><? echo $row[csf("item_description")]; ?></td>
										<td width="60" class="center wrd_brk"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
                                        <?
										if($variable_string_inventory!=1)
										{
											?>
                                            <td width="100" class="right wrd_brk"><? echo number_format($openingBalanceValue,2)?></td>
                                            <?
										}
										?>
										<td width="100" class="right wrd_brk"><? echo number_format($totalReceive,2); ?></td>
                                        <?
										if($variable_string_inventory!=1)
										{
											?>
                                            <td width="100" class="right wrd_brk"><? echo number_format($totalReceiveValue,2);?></td>
                                            <?
										}
										?>
										<td width="100" class="right wrd_brk"><? echo number_format($totalIssue,2); ?></td>
                                        <?
										if($variable_string_inventory!=1)
										{
											?>
                                            <td width="100" class="right wrd_brk"><? echo number_format($totalIssueValue,2);?></td>
                                            <?
										}
										?>
										<td width="100" class="right wrd_brk" title="<? echo $openingBalance;?>"><? echo number_format($closingStock,2); ?></td>
                                        <?
										if($variable_string_inventory!=1)
										{
											?>
                                            <td width="100" class="right wrd_brk"><? if($closingStock>0) echo number_format($closingStockRate,4); else echo "0.00"; ?></td>
											<td width="100" class="right wrd_brk" title="<? echo $row[csf("total_rcv_amount")]."==".$row[csf("total_issue_amount")]; ?>"><? if(number_format($closingStock,2)>0) echo number_format($closingStockValue,2); else echo "0.00"; ?></td>
                                            <?
										}
										?>
									</tr>
									<?
							    $i++;
							}
						}
						$grand_total_openingBalanceValue += $total_openingBalanceValue;
						$grand_total_receive_qnty += $total_receive_qnty;
						$grand_GrandtotalReceiveValue += $GrandtotalReceiveValue;
						$grand_total_issue_qnty += $total_issue_qnty;
						$grand_GrandtotalIssueValue += $GrandtotalIssueValue;
						$grand_total_closing_stock += $total_closing_stock;
						$grand_total_closingStockValue += $total_closingStockValue;
						if(($value_with ==1 && (number_format($openingBalance,2) > 0.00 || number_format($closingStock,2) > 0.00 ) )    ||    ($value_with ==0 && (number_format($openingBalance,2) > 0.00 || number_format($closingStock,2) > 0.00 || number_format($totalReceive,2) > 0.00 || number_format($totalIssue,2) > 0.00 )))
					    {
                		?>
                		<tr bgcolor="#CCCCCC" style="font-weight:bold">
							<td width="600" class="right" colspan="7"><strong>Sub Total</strong></td>
                            <?
							if($variable_string_inventory!=1)
							{
								?>
								<td width="100" class="right"><? echo number_format($total_openingBalanceValue,2);?></td>
								<?
							}
							?>
							<td width="100" class="right"><? echo number_format($total_receive_qnty,2); ?></td>
                            <?
							if($variable_string_inventory!=1)
							{
								?>
								<td width="100" class="right"><? echo number_format($GrandtotalReceiveValue,2); ?></td>
								<?
							}
							?>
							<td width="100" class="right"><? echo number_format($total_issue_qnty,2); ?></td>
                            <?
							if($variable_string_inventory!=1)
							{
								?>
								<td width="100" class="right"><? echo number_format($GrandtotalIssueValue,2); ?></td>
								<?
							}
							?>
							<td width="100" class="right"><? echo number_format($total_closing_stock,2); ?></td>
                            <?
							if($variable_string_inventory!=1)
							{
								?>
								<td width="100"></td>
								<td width="100" class="right"><? echo number_format($total_closingStockValue,2); ?></td>
								<?
							}
							?>
						</tr>
                		<?
                		}
					}
					?>
					<tr bgcolor="#CCCCCC" style="font-weight:bold">
						<td width="600" class="right" colspan="7"><strong>Grand Total</strong></td>
                        <?
						if($variable_string_inventory!=1)
						{
							?>
							<td width="100" class="right"><? echo number_format($grand_total_openingBalanceValue,2);?></td>
							<?
						}
						?>
						<td width="100" class="right"><? echo number_format($grand_total_receive_qnty,2); ?></td>
                        <?
						if($variable_string_inventory!=1)
						{
							?>
							<td width="100" class="right"><? echo number_format($grand_GrandtotalReceiveValue,2); ?></td>
							<?
						}
						?>
						<td width="100" class="right"><? echo number_format($grand_total_issue_qnty,2); ?></td>
                        <?
						if($variable_string_inventory!=1)
						{
							?>
							<td width="100" class="right"><? echo number_format($grand_GrandtotalIssueValue,2); ?></td>
							<?
						}
						?>
						<td width="100" class="right"><? echo number_format($grand_total_closing_stock,2); ?></td>
                        <?
						if($variable_string_inventory!=1)
						{
							?>
							<td width="100"></td>
							<td width="100" class="right"><? echo number_format($grand_total_closingStockValue,2); ?></td>
							<?
						}
						?>

					</tr>
				</table>
			</div>
		</div>
		<?
	}
	else if($report_type == 6)
	{
		if($variable_store_wise_rate==1)
		{
			$sql="Select a.prod_id $store_field, b.id, b.item_code ,b.store_id,b.item_category_id,b.item_group_id,b.avg_rate_per_unit,b.sub_group_name,b.item_description,b.item_size,b.unit_of_measure,
			b.re_order_label,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.cons_quantity else 0 end) as opening_total_receive,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.store_amount else 0 end) as opening_total_receive_amt,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.cons_quantity else 0 end) as opening_total_issue,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.store_amount else 0 end) as opening_total_issue_amt,
			sum(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (1,4,5)  then a.store_amount else 0 end) as total_rcv_amount,
			sum(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (2,3,6)  then a.store_amount else 0 end) as total_issue_amount,
			sum(case when a.transaction_type=1 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as purchase,
			sum(case when a.transaction_type=3 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as receive_return,
			sum(case when a.transaction_type=2 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue,
			sum(case when a.transaction_type=4 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue_return ,
			sum(case when a.transaction_type=5 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_in,
			sum(case when a.transaction_type=6 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_out,
			sum(case when a.transaction_type in (1,4,5) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.store_amount else 0 end) as total_rcv_amt_value,
			sum(case when a.transaction_type in (2,3,6) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.store_amount else 0 end) as total_iss_amt_value
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.transaction_type in (1,2,3,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category in($item_cate_credential_cond) and b.entry_form<>24 $str_cond $store_cond $zero_cond
			group by $strore_group a.prod_id, b.id, b.store_id, b.item_category_id, b.item_group_id, b.avg_rate_per_unit, b.sub_group_name, b.item_description, b.item_size, b.re_order_label, b.unit_of_measure, b.item_code
			order by b.item_category_id, b.item_group_id $store_order";
		}
		else
		{
			$sql="Select a.prod_id $store_field, b.id, b.item_code ,b.store_id,b.item_category_id,b.item_group_id,b.avg_rate_per_unit,b.sub_group_name,b.item_description,b.item_size,b.unit_of_measure,
			b.re_order_label,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.cons_quantity else 0 end) as opening_total_receive,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.cons_amount else 0 end) as opening_total_receive_amt,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.cons_quantity else 0 end) as opening_total_issue,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.cons_amount else 0 end) as opening_total_issue_amt,
			sum(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (1,4,5)  then a.cons_amount else 0 end) as total_rcv_amount,
			sum(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (2,3,6)  then a.cons_amount else 0 end) as total_issue_amount,
			sum(case when a.transaction_type=1 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as purchase,
			sum(case when a.transaction_type=3 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as receive_return,
			sum(case when a.transaction_type=2 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue,
			sum(case when a.transaction_type=4 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue_return ,
			sum(case when a.transaction_type=5 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_in,
			sum(case when a.transaction_type=6 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_out,
			sum(case when a.transaction_type in (1,4,5) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_amount else 0 end) as total_rcv_amt_value,
			sum(case when a.transaction_type in (2,3,6) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_amount else 0 end) as total_iss_amt_value
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.transaction_type in (1,2,3,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category in($item_cate_credential_cond) and b.entry_form<>24 $str_cond $store_cond $zero_cond
			group by $strore_group a.prod_id, b.id, b.store_id, b.item_category_id, b.item_group_id, b.avg_rate_per_unit, b.sub_group_name, b.item_description, b.item_size, b.re_order_label, b.unit_of_measure, b.item_code
			order by b.item_category_id, b.item_group_id $store_order";
		}


		//echo  $sql;//die;
		$result = sql_select($sql);

		$sub_group_array = array();
		foreach ($result as $key => $value) {
			$sub_group_array[$value[csf('item_category_id')]][$value[csf('item_group_id')]][$value[csf('sub_group_name')]] = $value;
		}
		// ob_start();
		//echo '<pre>';print_r($sub_group_array);

	    ?>
	    <style>
	    	.wrd_brk{word-break: break-all;}
	    	.left{text-align: left;}
	    	.center{text-align: center;}
	    	.right{text-align: right;}
	    </style>
	    <table style="width:800px" border="1" cellpadding="0" cellspacing="0" id="table_header_1" rules="all" class="rpt_table">
			<tr style="border:none;">
				<td width="800" colspan="5" class="center" style="border:none; font-size:14px; font-weight: bold;">
					<b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>
				</td>
			</tr>
			<tr style="border:none;">
				<td width="800" colspan="5" class="center" style="border:none; font-size:14px;">
					<?
					    $nameArray=sql_select("select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website from lib_company where id=$cbo_company_name and status_active=1 and is_deleted=0");
					    foreach ($nameArray as $value) {
					    	echo $value[csf('city')];
					    }
					?>
				</td>
			</tr>
			<tr style="border:none;">
				<td width="800" colspan="5" class="center" style="border:none; font-size:14px;">
				    <p>Monthly Inventory Statement</p>
				</td>
			</tr>
			<tr style="border:none;">
				<td width="800" colspan="5" class="center" style="border:none; font-size:12px; text-decoration: underline;">
					<? if($from_date!="" || $to_date!="") echo "From Date: ".change_date_format($from_date,'dd-mm-yyyy')." To ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
				</td>
			</tr>
	    </table>
		<?
		$cat_grand_total_openingBalanceValue = 0;
		$cat_grand_GrandtotalReceiveValue = 0;
		$cat_grand_GrandtotalIssueValue = 0;
		$cat_grand_total_closingStockValue = 0;

		foreach ($sub_group_array as $category_id => $category_name)
		{
			$grand_total_openingBalanceValue = 0;
			$grand_GrandtotalReceiveValue = 0;
			$grand_GrandtotalIssueValue = 0;
			$grand_total_closingStockValue = 0;
			foreach($category_name as $group_id => $group_name)
			{
				//echo 'system';
				$i=1;
				$total_openingBalance = 0;
                $total_openingBalanceValue= 0;
                $total_stockValue= 0;
                $total_purchase= 0;
                $total_issue_return= 0;
                $total_transfer_in= 0;
                $total_receive_qnty= 0;
                $total_issue_qnty= 0;
                $total_receive_return_qnty= 0;
                $total_transfer_out= 0;
                $total_issue_recv_return_qnty= 0;
                $total_closing_stock= 0;
                $GrandtotalIssueValue= 0;
                $total_closingStockValue= 0;
                $GrandtotalReceiveValue= 0;
                ?>

                <div style="width:820px; max-height:350px; overflow-y:auto;" id="scroll_body">
				<table style="width:800px;" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
					<thead>
	                	<tr style="border: 1px solid #8DAFDA">
	                		<th colspan="3">
	                			<div style="width: 400px; text-align: left;">CATEGORY : <? echo strtoupper($item_category[$category_id]); ?></div>
	                		</th>
	                		<th colspan="2">
	                			<div style="float: left; width: 400px">GROUP : <? echo strtoupper($itemgroupArr[$group_id]); ?></div>
	                		</th>
	                	</tr>
                	</thead>
                	<tbody>
                    <tr>
						<th width="200" class="wrd_brk left" style="border-right: 1px solid #8DAFDA;">SUB GROUP</th>
						<th width="150" class="wrd_brk center" style="border-right: 1px solid #8DAFDA;">Opening Value</th>
						<th width="150" class="wrd_brk center" style="border-right: 1px solid #8DAFDA;">Receive Value</th>
						<th width="150" class="wrd_brk center" style="border-right: 1px solid #8DAFDA;">Issue Value</th>
						<th width="150" class="wrd_brk center" style="border-right: 1px solid #8DAFDA;">Closing Value</th>
					</tr>
                    <?
					foreach($group_name as $row)
				    {
		                if( $row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==4 )
		                    $stylecolor='style="color:#A61000"';
		                else
		                    $stylecolor='style="color:#000000"';
	                    $openingBalance = $row[csf("opening_total_receive")]-$row[csf("opening_total_issue")];
	                    $totalReceive = $row[csf("purchase")]+$row[csf("issue_return")]+$row[csf("transfer_in")];
	                    $totalIssue =$row[csf("issue")]+$row[csf("receive_return")]+$row[csf("transfer_out")];

	                    $closingStock=($totalReceive-$totalIssue)+number_format($openingBalance,4,'.','');

	                    $openingBalanceRate = $openingBalanceValue = 0;
	                    /*if($row[csf("opening_total_receive")] > 0){
	                        $openingBalanceRate = $row[csf("opening_total_receive_amt")]/$row[csf("opening_total_receive")];
	                    }
	                    $openingBalanceValue = $openingBalance *$openingBalanceRate;*/
						$openingBalanceValue=$row[csf("opening_total_receive_amt")]-$row[csf("opening_total_issue_amt")];
	                    $totalReceiveValue = $row[csf("total_rcv_amt_value")];
	                    $totalIssueValue = $row[csf('total_iss_amt_value')];
	                    $closingStockValue = $openingBalanceValue + $totalReceiveValue - $totalIssueValue;

	                    $closingStockRate = $closingStockValue/$closingStock;
	                    $stockValue=$row[csf("total_rcv_amount")]-$row[csf('total_issue_amount')];
	                    $re_order_label = $row[csf('re_order_label')];
	                    if($closingStock <= $re_order_label){$bgcolor="red";}elseif($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
	                    if(($value_with ==1 && (number_format($openingBalance,2) > 0.00 || number_format($closingStock,2) > 0.00 ) )    ||    ($value_with ==0 && (number_format($openingBalance,2) > 0.00 || number_format($closingStock,2) > 0.00 || number_format($totalReceive,2) > 0.00 || number_format($totalIssue,2) > 0.00 )))
			            {

		                    $total_openingBalance+=$openingBalance;
		                    $total_openingBalanceValue+=$openingBalanceValue;
		                    $total_stockValue+=$stockValue;
		                    $total_purchase+=$row[csf("purchase")];
		                    $total_issue_return+=$row[csf("issue_return")];
		                    $total_transfer_in+=$row[csf("transfer_in")];
		                    $total_receive_qnty+=$totalReceive;
		                    $total_issue_qnty+=$row[csf("issue")];
		                    $total_receive_return_qnty+=$row[csf("receive_return")];
		                    $total_transfer_out+=$row[csf("transfer_out")];
		                    $total_issue_recv_return_qnty+=$totalIssue;
		                    $total_closing_stock+=$closingStock;
		                    $GrandtotalIssueValue+=$totalIssueValue;
		                    $total_closingStockValue+=$closingStockValue;
		                    $GrandtotalReceiveValue+=$totalReceiveValue;
							?>
							<tr>
								<td width="200" class="left wrd_brk"><? echo $row[csf("sub_group_name")]; ?></td>
								<td width="150" class="center wrd_brk"><? echo number_format($openingBalanceValue,2); ?></td>
								<td width="150" class="center wrd_brk"><? echo number_format($totalReceiveValue,2);?></td>
								<td width="150" class="center wrd_brk"><? echo number_format($totalIssueValue,2);?></td>
								<td width="150" class="center wrd_brk" title="<? echo $row[csf("total_rcv_amount")]."==".$row[csf("total_issue_amount")]; ?>"><? if($closingStock>0) echo number_format($closingStockValue,2); else echo "0.00"; ?></td>
							</tr>
		                    <?
						    $i++;
					    }
					}
				    $grand_total_openingBalanceValue += $total_openingBalanceValue;
					$grand_GrandtotalReceiveValue += $GrandtotalReceiveValue;
					$grand_GrandtotalIssueValue += $GrandtotalIssueValue;
					$grand_total_closingStockValue += $total_closingStockValue;
				    ?>
				    <tr bgcolor="#CCCCCC" style="font-weight:bold">
						<td width="200" class="left wrd_brk"><strong>Group Wise Total</strong></td>
						<td width="150" class="center wrd_brk"><? echo number_format($total_openingBalanceValue,2);?></td>
						<td width="150" class="center wrd_brk"><? echo number_format($grand_GrandtotalReceiveValue,2); ?></td>
						<td width="150" class="center wrd_brk"><? echo number_format($grand_GrandtotalIssueValue,2); ?></td>
						<td width="150" class="center wrd_brk"><? echo number_format($grand_total_closingStockValue,2); ?></td>
				    </tr>
				    </tbody>
				    <br>
				    <?
			    }
				$cat_grand_total_openingBalanceValue += $grand_total_openingBalanceValue;
				$cat_grand_GrandtotalReceiveValue += $grand_GrandtotalReceiveValue;
				$cat_grand_GrandtotalIssueValue += $grand_GrandtotalIssueValue;
				$cat_grand_total_closingStockValue += $grand_total_closingStockValue;
		    }
		    ?>
    		<tfoot>
				<tr bgcolor="#CCCCCC" style="font-weight:bold">
					<td width="200" class="wrd_brk left"><strong>Category Wise Total</strong></td>
					<td width="150" class="wrd_brk center"><? echo number_format($cat_grand_total_openingBalanceValue,2);?></td>
					<td width="150" class="wrd_brk center"><? echo number_format($cat_grand_GrandtotalReceiveValue,2); ?></td>
					<td width="150" class="wrd_brk center"><? echo number_format($cat_grand_GrandtotalIssueValue,2); ?></td>
					<td width="150" class="wrd_brk center"><? echo number_format($cat_grand_total_closingStockValue,2); ?></td>
				</tr>
			</tfoot>
	    </table>
		</div>
        <?
	}
	else if($report_type == 7)
	{
		//$store_field , b.store_id ,b.store_id $store_order
		if($variable_store_wise_rate==1)
		{
			$sql="Select a.prod_id, b.id, b.item_code, b.item_category_id, b.item_group_id, b.avg_rate_per_unit, b.sub_group_name, b.item_description, b.item_size, b.unit_of_measure, b.re_order_label,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.cons_quantity else 0 end) as opening_total_receive,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.store_amount else 0 end) as opening_total_receive_amt,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.cons_quantity else 0 end) as opening_total_issue,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.store_amount else 0 end) as opening_total_issue_amt,
			sum(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (1,4,5)  then a.store_amount else 0 end) as total_rcv_amount,
			sum(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (2,3,6)  then a.store_amount else 0 end) as total_issue_amount,
			sum(case when a.transaction_type=1 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as purchase,
			sum(case when a.transaction_type=3 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as receive_return,
			sum(case when a.transaction_type=2 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue,
			sum(case when a.transaction_type=4 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue_return ,
			sum(case when a.transaction_type=5 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_in,
			sum(case when a.transaction_type=6 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_out,
			sum(case when a.transaction_type in (1,4,5) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.store_amount else 0 end) as total_rcv_amt_value,
			sum(case when a.transaction_type in (2,3,6) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.store_amount else 0 end) as total_iss_amt_value
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.transaction_type in (1,2,3,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category in($item_cate_credential_cond) and b.entry_form<>24 $str_cond $store_cond $zero_cond
			group by a.prod_id, b.id, b.item_category_id, b.item_group_id, b.avg_rate_per_unit, b.sub_group_name, b.item_description, b.item_size, b.re_order_label, b.unit_of_measure, b.item_code
			order by b.item_category_id, b.item_group_id , a.prod_id";
		}
		else
		{
			$sql="Select a.prod_id, b.id, b.item_code, b.item_category_id, b.item_group_id, b.avg_rate_per_unit, b.sub_group_name, b.item_description, b.item_size, b.unit_of_measure, b.re_order_label,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.cons_quantity else 0 end) as opening_total_receive,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.cons_amount else 0 end) as opening_total_receive_amt,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.cons_quantity else 0 end) as opening_total_issue,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.cons_amount else 0 end) as opening_total_issue_amt,
			sum(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (1,4,5)  then a.cons_amount else 0 end) as total_rcv_amount,
			sum(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (2,3,6)  then a.cons_amount else 0 end) as total_issue_amount,
			sum(case when a.transaction_type=1 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as purchase,
			sum(case when a.transaction_type=3 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as receive_return,
			sum(case when a.transaction_type=2 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue,
			sum(case when a.transaction_type=4 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue_return ,
			sum(case when a.transaction_type=5 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_in,
			sum(case when a.transaction_type=6 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_out,
			sum(case when a.transaction_type in (1,4,5) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_amount else 0 end) as total_rcv_amt_value,
			sum(case when a.transaction_type in (2,3,6) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_amount else 0 end) as total_iss_amt_value
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.transaction_type in (1,2,3,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category in($item_cate_credential_cond) and b.entry_form<>24 $str_cond $store_cond $zero_cond
			group by a.prod_id, b.id, b.item_category_id, b.item_group_id, b.avg_rate_per_unit, b.sub_group_name, b.item_description, b.item_size, b.re_order_label, b.unit_of_measure, b.item_code
			order by b.item_category_id, b.item_group_id , a.prod_id";
		}


		//echo  $sql;//die;
		$result = sql_select($sql);

		$item_category_array = array();
		foreach ($result as $key => $value) {
			$item_category_array[$value[csf('item_category_id')]][] = $value;
		}
		// ob_start();
		//echo '<pre>';print_r($item_category_array);

	    ?>
	    <style>
	    	.wrd_brk{word-break: break-all;}
	    	.left{text-align: left;}
	    	.center{text-align: center;}
	    	.right{text-align: right;}
	    </style>
	    <fieldset style="width: 800px">
			<table style="width:800px" border="1" cellpadding="0" cellspacing="0" rules="all">
				<tr>
					<td width="800" colspan="5" class="center" style="border:none; font-size:14px; font-weight: bold;">
						<b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>
					</td>
				</tr>
				<tr>
					<td width="800" colspan="5" class="center" style="border:none; font-size:14px;">
						<?
						    $nameArray=sql_select("select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website from lib_company where id=$cbo_company_name and status_active=1 and is_deleted=0");
						    foreach ($nameArray as $value) {
						    	echo $value[csf('city')];
						    }
						?>
					</td>
				</tr>
				<tr>
					<td width="800" colspan="5" class="center" style="border:none; font-size:14px;">
					    <p>Monthly Inventory Statement</p>
					</td>
				</tr>
				<tr>
					<td width="800" colspan="5" class="center" style="border:none; font-size:14px;">
					    <p>Category Wise Summary</p>
					</td>
				</tr>
				<tr>
					<td width="800" colspan="5" class="center" style="border:none; font-size:12px; text-decoration: underline;">
						<? if($from_date!="" || $to_date!="") echo "From Date: ".change_date_format($from_date,'dd-mm-yyyy')." To ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
					</td>
				</tr>
				<tr>
					<td width="800" colspan="5" class="center" style="border:none; font-size:14px;"></td>
				</tr>
				<tr>
					<td width="800" colspan="5" class="center" style="border:none; font-size:14px; text-decoration: underline;">
					    <b>STORE-SPARE</b>
					</td>
				</tr>
			</table>
			<table style="width:800px" border="1" cellpadding="0" cellspacing="0" class="rpt_table" id="table_header_1" rules="all" align="left">
				<thead>
					<tr>
						<th width="200" class="wrd_brk left">CATEGORY</th>
						<th width="150" class="wrd_brk center">Opening Value</th>
						<th width="150" class="wrd_brk center">Receive Value</th>
						<th width="150" class="wrd_brk center">Issue Value</th>
						<th width="150" class="wrd_brk center">Closing Value</th>
					</tr>
				</thead>
		    </table>

		    <div style="width:820px; max-height:350px; overflow-y:auto;" id="scroll_body">
				<table style="width:800px;" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left">
					<tbody>
		            <?
					$grand_total_openingBalanceValue = 0;
					$grand_GrandtotalReceiveValue = 0;
					$grand_GrandtotalIssueValue = 0;
					$grand_total_closingStockValue = 0;
					//echo "<pre>";print_r($item_category_array);die;
					foreach ($item_category_array as $category_id => $resault_data)
					{
						$total_openingBalance = 0;
	                    $total_openingBalanceValue= 0;
	                    $total_stockValue= 0;
	                    $total_purchase= 0;
	                    $total_issue_return= 0;
	                    $total_transfer_in= 0;
	                    $total_receive_qnty= 0;
	                    $total_issue_qnty= 0;
	                    $total_receive_return_qnty= 0;
	                    $total_transfer_out= 0;
	                    $total_issue_recv_return_qnty= 0;
	                    $total_closing_stock= 0;
	                    $GrandtotalIssueValue= 0;
	                    $total_closingStockValue= 0;
	                    $GrandtotalReceiveValue= 0;
						foreach($resault_data as $row)
						{
			                if( $row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==4 )
			                    $stylecolor='style="color:#A61000"';
			                else
			                    $stylecolor='style="color:#000000"';
		                    $openingBalance = $row[csf("opening_total_receive")]-$row[csf("opening_total_issue")];
		                    $totalReceive = $row[csf("purchase")]+$row[csf("issue_return")]+$row[csf("transfer_in")];
		                    $totalIssue =$row[csf("issue")]+$row[csf("receive_return")]+$row[csf("transfer_out")];

		                    $closingStock=($totalReceive-$totalIssue)+number_format($openingBalance,4,'.','');

		                    $openingBalanceRate = $openingBalanceValue = 0;
		                    /*if($row[csf("opening_total_receive")] > 0){
		                        $openingBalanceRate = $row[csf("opening_total_receive_amt")]/$row[csf("opening_total_receive")];
		                    }
		                    $openingBalanceValue = $openingBalance *$openingBalanceRate;*/
							$openingBalanceValue=$row[csf("opening_total_receive_amt")]-$row[csf("opening_total_issue_amt")];
		                    $totalReceiveValue = $row[csf("total_rcv_amt_value")];
		                    $totalIssueValue = $row[csf('total_iss_amt_value')];
		                    $closingStockValue = $openingBalanceValue + $totalReceiveValue - $totalIssueValue;

		                    $closingStockRate = $closingStockValue/$closingStock;
		                    $stockValue=$row[csf("total_rcv_amount")]-$row[csf('total_issue_amount')];
		                    $re_order_label = $row[csf('re_order_label')];
		                    if($closingStock <= $re_order_label){$bgcolor="red";}elseif($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
							//echo $value_with."=".number_format($openingBalance,2)."=".number_format($closingStock,2)."=".$row[csf("id")];die;
							if($value_with ==1)
							{
								if(number_format($openingBalance,2) > 0.00 || number_format($closingStock,2) > 0.00 )
								{
									$total_openingBalance+=$openingBalance;
									$total_openingBalanceValue+=$openingBalanceValue;
									$total_stockValue+=$stockValue;
									$total_purchase+=$row[csf("purchase")];
									$total_issue_return+=$row[csf("issue_return")];
									$total_transfer_in+=$row[csf("transfer_in")];
									$total_receive_qnty+=$totalReceive;
									$total_issue_qnty+=$row[csf("issue")];
									$total_receive_return_qnty+=$row[csf("receive_return")];
									$total_transfer_out+=$row[csf("transfer_out")];
									$total_issue_recv_return_qnty+=$totalIssue;
									$total_closing_stock+=$closingStock;
									$GrandtotalIssueValue+=$totalIssueValue;
									$total_closingStockValue+=$closingStockValue;
									$GrandtotalReceiveValue+=$totalReceiveValue;
								}
							}
							else
							{

								$total_openingBalance+=$openingBalance;
								$total_openingBalanceValue+=$openingBalanceValue;
								$total_stockValue+=$stockValue;
								$total_purchase+=$row[csf("purchase")];
								$total_issue_return+=$row[csf("issue_return")];
								$total_transfer_in+=$row[csf("transfer_in")];
								$total_receive_qnty+=$totalReceive;
								$total_issue_qnty+=$row[csf("issue")];
								$total_receive_return_qnty+=$row[csf("receive_return")];
								$total_transfer_out+=$row[csf("transfer_out")];
								$total_issue_recv_return_qnty+=$totalIssue;
								$total_closing_stock+=$closingStock;
								$GrandtotalIssueValue+=$totalIssueValue;
								$total_closingStockValue+=$closingStockValue;
								$GrandtotalReceiveValue+=$totalReceiveValue;
							}


						}
						?>
						 <tr>
							<td width="200" class="wrd_brk left"><? echo strtoupper($item_category[$category_id]); ?></td>
							<td width="150" class="wrd_brk right"><? echo number_format($total_openingBalanceValue,2);?></td>
							<td width="150" class="wrd_brk right"><? echo number_format($GrandtotalReceiveValue,2); ?></td>
							<td width="150" class="wrd_brk right"><? echo number_format($GrandtotalIssueValue,2); ?></td>
							<td width="150" class="wrd_brk right"><? echo number_format($total_closingStockValue,2); ?></td>
						</tr>
						<?
						$grand_total_openingBalanceValue += $total_openingBalanceValue;
						$grand_GrandtotalReceiveValue += $GrandtotalReceiveValue;
						$grand_GrandtotalIssueValue += $GrandtotalIssueValue;
						$grand_total_closingStockValue += $total_closingStockValue;

			        }
                    ?>
					<tr bgcolor="#CCCCCC" style="font-weight:bold">
						<td width="200" class="wrd_brk right"><strong>Grand Total</strong></td>
						<td width="150" class="wrd_brk right"><? echo number_format($grand_total_openingBalanceValue,2);?></td>
						<td width="150" class="wrd_brk right"><? echo number_format($grand_GrandtotalReceiveValue,2); ?></td>
						<td width="150" class="wrd_brk right"><? echo number_format($grand_GrandtotalIssueValue,2); ?></td>
						<td width="150" class="wrd_brk right"><? echo number_format($grand_total_closingStockValue,2); ?></td>
					</tr>
				    </tbody>
			    </table>
		    </div>
	    </fieldset>
        <?
	}
	else if($report_type == 8)
	{
		if($variable_store_wise_rate==1)
		{
			$sql="SELECT a.prod_id $store_field, b.id, b.item_number,b.item_code,b.store_id,b.item_category_id,b.item_group_id, c.item_name,b.avg_rate_per_unit,b.sub_group_name,b.item_description,b.item_size,b.unit_of_measure,
			b.re_order_label,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.cons_quantity else 0 end) as opening_total_receive,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.store_amount else 0 end) as opening_total_receive_amt,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.cons_quantity else 0 end) as opening_total_issue,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.store_amount else 0 end) as opening_total_issue_amt,
			sum(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (1,4,5)  then a.store_amount else 0 end) as total_rcv_amount,
			sum(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (2,3,6)  then a.store_amount else 0 end) as total_issue_amount,
			sum(case when a.transaction_type=1 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as purchase,
			sum(case when a.transaction_type=3 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as receive_return,
			sum(case when a.transaction_type=2 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue,
			sum(case when a.transaction_type=4 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue_return ,
			sum(case when a.transaction_type=5 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_in,
			sum(case when a.transaction_type=6 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_out,
			sum(case when a.transaction_type in (1,4,5) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.store_amount else 0 end) as total_rcv_amt_value,
			sum(case when a.transaction_type in (2,3,6) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.store_amount else 0 end) as total_iss_amt_value
			from inv_transaction a, product_details_master b, lib_item_group c
			where a.prod_id=b.id and b.item_group_id=c.id and a.transaction_type in (1,2,3,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category in($item_cate_credential_cond) and b.entry_form<>24 $str_cond $store_cond $zero_cond
			group by $strore_group a.prod_id, b.id, b.store_id, b.item_number, b.item_category_id, b.item_group_id, b.avg_rate_per_unit, b.sub_group_name, b.item_description, b.item_size, b.re_order_label, b.unit_of_measure, b.item_code, c.item_name
			order by b.item_category_id, c.item_name , a.prod_id";
		}
		else
		{
			$sql="SELECT a.prod_id $store_field, b.id, b.item_number,b.item_code,b.store_id,b.item_category_id,b.item_group_id, c.item_name,b.avg_rate_per_unit,b.sub_group_name,b.item_description,b.item_size,b.unit_of_measure,
			b.re_order_label,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.cons_quantity else 0 end) as opening_total_receive,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.cons_amount else 0 end) as opening_total_receive_amt,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.cons_quantity else 0 end) as opening_total_issue,
			sum(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.cons_amount else 0 end) as opening_total_issue_amt,
			sum(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (1,4,5)  then a.cons_amount else 0 end) as total_rcv_amount,
			sum(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (2,3,6)  then a.cons_amount else 0 end) as total_issue_amount,
			sum(case when a.transaction_type=1 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as purchase,
			sum(case when a.transaction_type=3 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as receive_return,
			sum(case when a.transaction_type=2 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue,
			sum(case when a.transaction_type=4 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue_return ,
			sum(case when a.transaction_type=5 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_in,
			sum(case when a.transaction_type=6 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_out,
			sum(case when a.transaction_type in (1,4,5) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_amount else 0 end) as total_rcv_amt_value,
			sum(case when a.transaction_type in (2,3,6) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_amount else 0 end) as total_iss_amt_value
			from inv_transaction a, product_details_master b, lib_item_group c
			where a.prod_id=b.id and b.item_group_id=c.id and a.transaction_type in (1,2,3,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category in($item_cate_credential_cond) and b.entry_form<>24 $str_cond $store_cond $zero_cond
			group by $strore_group a.prod_id, b.id, b.store_id, b.item_number, b.item_category_id, b.item_group_id, b.avg_rate_per_unit, b.sub_group_name, b.item_description, b.item_size, b.re_order_label, b.unit_of_measure, b.item_code, c.item_name
			order by b.item_category_id, c.item_name , a.prod_id";
		}


		$result = sql_select($sql);

		$item_category_array = array();
		foreach ($result as $key => $value)
		{
			$item_category_array[$value[csf('item_category_id')]][$value[csf('item_group_id')]][$value[csf('prod_id')]] = $value;
		}

		// ==============last receive date query================
		$sql_last_receive_date = "SELECT a.prod_id, max(a.transaction_date) as last_receive_date
		from inv_transaction a, product_details_master b, lib_item_group c
		where a.prod_id=b.id and b.item_group_id=c.id and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category in($item_cate_credential_cond) and b.entry_form<>24 $str_cond $store_cond $zero_cond
		group by a.prod_id";

		$last_rcv_result = sql_select($sql_last_receive_date);
		$last_rcv_date_array = array();
		foreach ($last_rcv_result as $key => $value)
		{
			$last_rcv_date_array[$value[csf('prod_id')]] = $value;
		}
		// echo '<pre>';print_r($last_rcv_date_array);die;

		// =============last issue date query==============
		$sql_last_issue_date = "SELECT a.prod_id, max(a.transaction_date) as last_issue_date
		from inv_transaction a, product_details_master b, lib_item_group c
		where a.prod_id=b.id and b.item_group_id=c.id and a.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category in($item_cate_credential_cond) and b.entry_form<>24 $str_cond $store_cond $zero_cond
		group by a.prod_id";

		$last_issue_result = sql_select($sql_last_issue_date);
		$last_issue_date_array = array();
		foreach ($last_issue_result as $key => $value)
		{
			$last_issue_date_array[$value[csf('prod_id')]] = $value;
		}
		// echo '<pre>';print_r($last_issue_date_array);die;

		// ob_start();
		$div_width="1190";
		$table_width="1170";
	    ?>
	    <style>
	    	.wrd_brk{word-break: break-all;}
	    	.left{text-align: left;}

	    	.center{text-align: center;}
	    	.right{text-align: right;}
	    </style>
		<div style="width:<?=$div_width;?>px;">
			<table style="width:<?=$table_width;?>px; margin-right: 20px;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_header_1" rules="all">
				<thead>
					<tr style="border:none;">
						<td width="<?=$table_width;?>" colspan="<? if($variable_string_inventory!=1) echo 15; else echo 10;?>" class="center" style="border:none; font-size:14px; font-weight: bold;">
							<b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>
						</td>
					</tr>
					<tr style="border:none;">
						<td width="<?=$table_width;?>" colspan="<? if($variable_string_inventory!=1) echo 15; else echo 10;?>" class="center" style="border:none; font-size:12px; text-decoration: underline;">
							<? if($from_date!="" || $to_date!="") echo "From Date: ".change_date_format($from_date,'dd-mm-yyyy')." To ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
						</td>
					</tr>
					<tr>
						<th width="40" class="wrd_brk">SL</th>
						<th width="60" class="wrd_brk">Product Id</th>
						<th width="100" class="wrd_brk">Item Code</th>
						<th width="100" class="wrd_brk">Item Sub-group</th>
						<th width="200" class="wrd_brk">Item Description</th>
						<th width="100" class="wrd_brk">Last Rcv. Date</th>
						<th width="100" class="wrd_brk">Last Issue Date</th>
						<th width="60" class="wrd_brk">UoM</th>
						<th width="100" class="wrd_brk">Closing Stock</th>
                        <th width="100" class="wrd_brk">Avg. Rate</th>
						<th width="100" class="wrd_brk">Stock Value</th>
                        <th class="wrd_brk">DOH</th>
					</tr>
				</thead>
			</table>
			<div style="width:<?=$div_width;?>px; max-height:350px; overflow-y:auto;" id="scroll_body">
				<table style="width:<?=$table_width;?>px; float:left;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
					<?

					$grand_total_closing_stock = 0;
					$grand_total_closingStockValue = 0;
					$i=1;
					// echo '<pre>';print_r($item_category_array);die;
					foreach ($item_category_array as $category_id => $group_data)
					{
						?>
                        <tr>
                        	<td colspan="12" align="center" style="font-weight:bold; font-size:14px;">Item Category: <? echo $item_category[$category_id]; ?></td>
                        </tr>
                        <?
						foreach ($group_data as $group_id => $resault_data)
						{
							?>
                            <tr>
                                <td colspan="12" align="center" style="font-weight:bold; font-size:14px;">Item Group: <? echo $itemgroupArr[$group_id]; ?></td>
                            </tr>
                            <?
							foreach($resault_data as $row)
							{
								if( $row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==4 )
									$stylecolor='style="color:#A61000"';
								else
									$stylecolor='style="color:#000000"';
								$openingBalance = $row[csf("opening_total_receive")]-$row[csf("opening_total_issue")];
								$totalReceive = $row[csf("purchase")]+$row[csf("issue_return")]+$row[csf("transfer_in")];
								$totalIssue =$row[csf("issue")]+$row[csf("receive_return")]+$row[csf("transfer_out")];

								$closingStock=($totalReceive-$totalIssue)+number_format($openingBalance,4,'.','');
								$openingBalanceRate = $openingBalanceValue = 0;
								$openingBalanceValue=$row[csf("opening_total_receive_amt")]-$row[csf("opening_total_issue_amt")];
								$totalReceiveValue = $row[csf("total_rcv_amt_value")];
								$totalIssueValue = $row[csf('total_iss_amt_value')];
								$closingStockValue = $openingBalanceValue + $totalReceiveValue - $totalIssueValue;
								$closingStockRate = $closingStockValue/$closingStock;
								$stockValue=$row[csf("total_rcv_amount")]-$row[csf('total_issue_amount')];
								$re_order_label = $row[csf('re_order_label')];
								//if($closingStock <= $re_order_label){$bgcolor="red";}elseif($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
								if($value_with ==1){
									if(($value_with ==1 && (number_format($openingBalance,2) > 0.00 || number_format($closingStock,2) > 0.00 ) ) || ($value_with ==0 && (number_format($openingBalance,2) > 0.00 || number_format($closingStock,2) > 0.00 || number_format($totalReceive,2) > 0.00 || number_format($totalIssue,2) > 0.00 )))
									{
										/*$total_openingBalance+=$openingBalance;
										$total_openingBalanceValue+=$openingBalanceValue;
										$total_stockValue+=$stockValue;
										$total_purchase+=$row[csf("purchase")];
										$total_issue_return+=$row[csf("issue_return")];
										$total_transfer_in+=$row[csf("transfer_in")];
										$total_receive_qnty+=$totalReceive;
										$total_issue_qnty+=$row[csf("issue")];
										$total_receive_return_qnty+=$row[csf("receive_return")];
										$total_transfer_out+=$row[csf("transfer_out")];
										$total_issue_recv_return_qnty+=$totalIssue;
										$total_closing_stock+=$closingStock;
										$GrandtotalIssueValue+=$totalIssueValue;
										$total_closingStockValue+=$closingStockValue;
										$GrandtotalReceiveValue+=$totalReceiveValue;*/
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="40" class="center wrd_brk"><? echo $i; ?></td>
											<td width="60" class="center wrd_brk"><? echo $row[csf("prod_id")]; ?></td>
											<td width="100" class="center wrd_brk"><? echo $row[csf("item_code")]; ?></td>
											<td width="100" class="center wrd_brk"><? echo $row[csf("sub_group_name")]; ?></td>
											<td width="200" class="center wrd_brk"><? echo $row[csf("item_description")]; ?></td>
											<td width="100" class="center wrd_brk"><? echo change_date_format($last_rcv_date_array[$row[csf("prod_id")]]['LAST_RECEIVE_DATE'],'dd-mm-yyyy'); ?></td>
											<td width="100" class="center wrd_brk"><? echo change_date_format($last_issue_date_array[$row[csf("prod_id")]]['LAST_ISSUE_DATE'],'dd-mm-yyyy'); ?></td>
											<td width="60" class="center wrd_brk"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
											<td width="100" class="right wrd_brk" title="<? echo $openingBalance;?>"><? echo number_format($closingStock,2); ?></td>
											<td width="100" class="right wrd_brk"><? if(number_format($closingStock,2)>0) echo number_format($closingStockRate,4); else echo "0.00"; ?></td>
											<td width="100" class="right wrd_brk" title="<? echo $row[csf("total_rcv_amount")]."==".$row[csf("total_issue_amount")]; ?>"><? if(number_format($closingStock,2)>0) echo number_format($closingStockValue,2); else echo "0.00"; ?></td>
											<td class="center wrd_brk"><? echo $days_doh[$row[csf("prod_id")]]["daysonhand"]; ?></td>
										</tr>
										<?
										$i++;
										$group_total_closing_stock += $closingStock;
										$group_total_closingStockValue += $closingStockValue;
										$category_total_closing_stock += $closingStock;
										$category_total_closingStockValue += $closingStockValue;
										$grand_total_closing_stock += $closingStock;
										$grand_total_closingStockValue += $closingStockValue;
									}
								}else{
									// 	if(($value_with ==1 && (number_format($openingBalance,2) > 0.00 || number_format($closingStock,2) > 0.00 ) ) || ($value_with ==0 && (number_format($openingBalance,2) > 0.00 || number_format($closingStock,2) > 0.00 || number_format($totalReceive,2) > 0.00 || number_format($totalIssue,2) > 0.00 )))
									// {
										/*$total_openingBalance+=$openingBalance;
										$total_openingBalanceValue+=$openingBalanceValue;
										$total_stockValue+=$stockValue;
										$total_purchase+=$row[csf("purchase")];
										$total_issue_return+=$row[csf("issue_return")];
										$total_transfer_in+=$row[csf("transfer_in")];
										$total_receive_qnty+=$totalReceive;
										$total_issue_qnty+=$row[csf("issue")];
										$total_receive_return_qnty+=$row[csf("receive_return")];
										$total_transfer_out+=$row[csf("transfer_out")];
										$total_issue_recv_return_qnty+=$totalIssue;
										$total_closing_stock+=$closingStock;
										$GrandtotalIssueValue+=$totalIssueValue;
										$total_closingStockValue+=$closingStockValue;
										$GrandtotalReceiveValue+=$totalReceiveValue;*/
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="40" class="center wrd_brk"><? echo $i; ?></td>
											<td width="60" class="center wrd_brk"><? echo $row[csf("prod_id")]; ?></td>
											<td width="100" class="center wrd_brk"><? echo $row[csf("item_code")]; ?></td>
											<td width="100" class="center wrd_brk"><? echo $row[csf("sub_group_name")]; ?></td>
											<td width="200" class="center wrd_brk"><? echo $row[csf("item_description")]; ?></td>
											<td width="100" class="center wrd_brk"><? echo change_date_format($last_rcv_date_array[$row[csf("prod_id")]]['LAST_RECEIVE_DATE'],'dd-mm-yyyy'); ?></td>
											<td width="100" class="center wrd_brk"><? echo change_date_format($last_issue_date_array[$row[csf("prod_id")]]['LAST_ISSUE_DATE'],'dd-mm-yyyy'); ?></td>
											<td width="60" class="center wrd_brk"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
											<td width="100" class="right wrd_brk" title="<? echo $openingBalance;?>"><? echo number_format($closingStock,2); ?></td>
											<td width="100" class="right wrd_brk"><? if(number_format($closingStock,2)>0) echo number_format($closingStockRate,4); else echo "0.00"; ?></td>
											<td width="100" class="right wrd_brk" title="<? echo $row[csf("total_rcv_amount")]."==".$row[csf("total_issue_amount")]; ?>"><? if(number_format($closingStock,2)>0) echo number_format($closingStockValue,2); else echo "0.00"; ?></td>
											<td class="center wrd_brk"><? echo $days_doh[$row[csf("prod_id")]]["daysonhand"]; ?></td>
										</tr>
										<?
										$i++;
										$group_total_closing_stock += $closingStock;
										$group_total_closingStockValue += $closingStockValue;
										$category_total_closing_stock += $closingStock;
										$category_total_closingStockValue += $closingStockValue;
										$grand_total_closing_stock += $closingStock;
										$grand_total_closingStockValue += $closingStockValue;
									// }
								}

							}
							?>
							<tr bgcolor="#FFFF99" style="font-weight:bold">
								<td width="560" class="right" colspan="8"><strong>Item Group Total</strong></td>
								<td width="100" class="right"><? echo number_format($group_total_closing_stock,2); ?></td>
								<td width="100"></td>
								<td width="100" class="right"><? echo number_format($group_total_closingStockValue,2); ?></td>
								<td></td>
							</tr>
							<?
							$group_total_closing_stock=$group_total_closingStockValue=0;
						}

						?>
                        <tr bgcolor="#CCCCCC" style="font-weight:bold">
                            <td width="560" class="right" colspan="8"><strong>Item Category Total</strong></td>
                            <td width="100" class="right"><? echo number_format($category_total_closing_stock,2); ?></td>
                            <td width="100"></td>
                            <td width="100" class="right"><? echo number_format($category_total_closingStockValue,2); ?></td>
                            <td></td>
                        </tr>
                        <?
						$category_total_closing_stock=$category_total_closingStockValue=0;
					}
					?>
					<tr bgcolor="#CCCCCC" style="font-weight:bold">
						<td width="560" class="right" colspan="8"><strong>Grand Total</strong></td>
						<td width="100" class="right"><? echo number_format($grand_total_closing_stock,2); ?></td>
                        <td width="100"></td>
						<td width="100" class="right"><? echo number_format($grand_total_closingStockValue,2); ?></td>
						<td></td>
					</tr>
				</table>
			</div>
		</div>
		<?
	}
	else if($report_type == 9)
	{
		if($variable_store_wise_rate==1)
		{
			$sql="Select a.prod_id $store_field,a.division_id, b.id,b.item_category_id,
			sum(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (2,3,6)  then a.store_amount else 0 end) as total_issue_amount,
			sum(case when a.transaction_type in (2,3,6) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.store_amount else 0 end) as total_iss_amt_value
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.transaction_type in (1,2,3,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category in($item_cate_credential_cond) and b.entry_form<>24 $str_cond $store_cond $zero_cond and a.division_id is not null and a.division_id!=0
			group by $strore_group a.prod_id,a.division_id, b.id,  b.item_category_id
			order by b.item_category_id $store_order";
		}
		else
		{
			$sql="Select a.prod_id $store_field,a.division_id, b.id,b.item_category_id,
			sum(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (2,3,6)  then a.cons_amount else 0 end) as total_issue_amount,
			sum(case when a.transaction_type in (2,3,6) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_amount else 0 end) as total_iss_amt_value
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.transaction_type in (1,2,3,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category in($item_cate_credential_cond) and b.entry_form<>24 $str_cond $store_cond $zero_cond and a.division_id is not null and a.division_id!=0
			group by $strore_group a.prod_id,a.division_id, b.id,  b.item_category_id
			order by b.item_category_id $store_order";
		}


		// echo  $sql;die;
		$result = sql_select($sql);
		$divisionArr = return_library_array("select id,division_name from  lib_division where  company_id='$cbo_company_name' ","id","division_name");

		$division_category_array = array();$division_category_array = array();
		foreach ($result as $key => $value) {
			if($value[csf('total_iss_amt_value')]!=0)
			{
				$division_array[$value[csf('division_id')]] = $value[csf('division_id')];
				$division_category_array[$value[csf('item_category_id')]]['catagory'] = $value[csf('item_category_id')];
				$division_category_array[$value[csf('item_category_id')]][$value[csf('division_id')]]['issue_amt'] += $value[csf('total_iss_amt_value')];
			}
		}
		$division_count=count($division_array);
		$tbl_width=($division_count*120)+280;
		$div_width=(($division_count*120)+280)."px";
		// ob_start();
		// var_dump($division_category_array);die;
		// echo '<pre>';print_r($division_array);

	    ?>
	    <style>
	    	.wrd_brk{word-break: break-all;}
	    	.left{text-align: left;}
	    	.center{text-align: center;}
	    	.right{text-align: right;}
	    </style>
	    <fieldset style="width:<?=$div_width;?>">
			<table style="width:<?=$div_width;?>" border="1" cellpadding="0" cellspacing="0" rules="all">
				<tr>
					<td width="<?=$tbl_width;?>" colspan="5" class="center" style="border:none; font-size:14px; font-weight: bold;">
						<strong><? echo $companyArr[$cbo_company_name]; ?></strong>
					</td>
				</tr>
				<tr>
					<td colspan="5" class="center" style="border:none; font-size:14px;">
					    <strong>Division &  Category Wise Issue Summary</strong>
					</td>
				</tr>
				<tr>
					<td colspan="5" class="center" style="border:none; font-size:12px; text-decoration: underline;">
						<? if($from_date!="" || $to_date!="") echo "From Date: ".change_date_format($from_date,'dd-mm-yyyy')." To ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
					</td>
				</tr>
				<tr>
					<td colspan="5" class="center" style="border:none; font-size:14px;"></td>
				</tr>
			</table>
			<div style="width:<?=$div_width;?>; max-height:350px; overflow-y:auto;" id="scroll_body">
				<table style="width:<?=$div_width;?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" id="table_header_1" rules="all" align="left">
				<thead>
					<tr>
						<th width="200" class="center">Particulars</th>
						<?
							foreach($division_array as $row)
							{
								?>
									<th width="120" class="wrd_brk center"><? echo $divisionArr[$row];?></th>
								<?
							}
						?>
						<th class="wrd_brk center">Total</th>
					</tr>
				</thead>
				<tbody>
				<?
					$grandtotalIssueValue = array();
					$grand_GrandtotalIssueValue = 0;
					foreach($division_category_array as $row){
						$GrandtotalIssueValue= 0;
						?>
							<tr>
								<td width="200" class="wrd_brk left"><? echo strtoupper($item_category[$row['catagory']]); ?></td>
						<?
						foreach($division_array as $value){
							?>
								<td width="120" class="wrd_brk right"><? echo number_format($row[$value]['issue_amt'],2); ?></td>
							<?
							$GrandtotalIssueValue+=$row[$value]['issue_amt'];
							$grandtotalIssueValue[$value] += $row[$value]['issue_amt'];
							$grand_GrandtotalIssueValue+= $row[$value]['issue_amt'];
						}
						?>
							<td class="wrd_brk right"><strong><? echo number_format($GrandtotalIssueValue,2); ?></strong></td>
						</tr>
						<?
					}
				?>
						<tr bgcolor="#CCCCCC" style="font-weight:bold">
							<td width="200"><strong>Grand Total</strong></td>
							<?
							foreach($division_array as $row)
							{
								?>
									<td width="120" class="wrd_brk right"><strong><? echo number_format($grandtotalIssueValue[$row],2); ?></strong></td>
								<?
							}
							?>
							<td class="wrd_brk right"><strong><? echo number_format($grand_GrandtotalIssueValue,2); ?></strong></td>
						</tr>
					</tbody>
				</table>
			</div>
	    </fieldset>
        <?
	}
	else if($report_type == 10)
	{
		if($cbo_yes_no==1)
		{
			if($variable_string_inventory!=1)
			{
				$div_width="2450";
				$table_width="2430";
			}
			else
			{
				$div_width="2250";
				$table_width="2230";
			}
		}
		else
		{
			if($variable_string_inventory!=1)
			{
				$div_width="2350";
				$table_width="2330";
			}
			else
			{
				$div_width="2150";
				$table_width="2130";
			}
		}
		//$cbo_company_name
		$loanRcvData = return_library_array("select id from inv_receive_master where status_active=1 and is_deleted=0 and company_id=$cbo_company_name and entry_form=20 and receive_purpose=1","id","id");
		$loanIssueData = return_library_array("select id from inv_issue_master where status_active=1 and is_deleted=0 and company_id=$cbo_company_name and entry_form=21 and issue_purpose=5","id","id");


		//$item_cate_credential_cond="".implode(",",array_diff(array_flip($general_item_category), array("4")))."";
		if($variable_store_wise_rate==1)
		{
			$sql="Select a.prod_id $store_field, b.id, b.item_number, b.item_code, b.item_category_id, b.item_group_id, b.avg_rate_per_unit, b.sub_group_name, b.item_description, b.item_size, b.unit_of_measure, a.transaction_type, b.re_order_label, a.mst_id, a.batch_lot,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.cons_quantity else 0 end) as opening_total_receive,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.store_amount else 0 end) as opening_total_receive_amt,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.cons_quantity else 0 end) as opening_total_issue,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.store_amount else 0 end) as opening_total_issue_amt,
			(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (1,4,5)  then a.store_amount else 0 end) as total_rcv_amount,
			(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (2,3,6)  then a.store_amount else 0 end) as total_issue_amount,
			(case when a.transaction_type=1 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as purchase,
			(case when a.transaction_type=3 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as receive_return,
			(case when a.transaction_type=2 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue,
			(case when a.transaction_type=4 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue_return,
			(case when a.transaction_type=5 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_in,
			(case when a.transaction_type=6 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_out,
			(case when a.transaction_type in (1,4,5) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.store_amount else 0 end) as total_rcv_amt_value,
			(case when a.transaction_type in (2,3,6) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.store_amount else 0 end) as total_iss_amt_value
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.transaction_type in (1,2,3,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category in(22) $str_cond $store_cond $zero_cond
			order by b.item_category_id, b.item_group_id , a.prod_id";
		}
		else
		{
			$sql="Select a.prod_id $store_field, b.id, b.item_number, b.item_code, b.item_category_id, b.item_group_id, b.avg_rate_per_unit, b.sub_group_name, b.item_description, b.item_size, b.unit_of_measure, a.transaction_type, b.re_order_label, a.mst_id, a.batch_lot,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.cons_quantity else 0 end) as opening_total_receive,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.cons_amount else 0 end) as opening_total_receive_amt,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.cons_quantity else 0 end) as opening_total_issue,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.cons_amount else 0 end) as opening_total_issue_amt,
			(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (1,4,5)  then a.cons_amount else 0 end) as total_rcv_amount,
			(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (2,3,6)  then a.cons_amount else 0 end) as total_issue_amount,
			(case when a.transaction_type=1 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as purchase,
			(case when a.transaction_type=3 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as receive_return,
			(case when a.transaction_type=2 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue,
			(case when a.transaction_type=4 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue_return,
			(case when a.transaction_type=5 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_in,
			(case when a.transaction_type=6 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_out,
			(case when a.transaction_type in (1,4,5) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_amount else 0 end) as total_rcv_amt_value,
			(case when a.transaction_type in (2,3,6) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_amount else 0 end) as total_iss_amt_value
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.transaction_type in (1,2,3,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category in(22) $str_cond $store_cond $zero_cond
			order by b.item_category_id, b.item_group_id , a.prod_id";
		}

		//echo  $sql;die;
		$result = sql_select($sql);
		$details_data=array();
		foreach($result as $row)
		{
			$details_data[$row[csf("prod_id")]][$row[csf("batch_lot")]]["prod_id"]=$row[csf("prod_id")];
			$details_data[$row[csf("prod_id")]][$row[csf("batch_lot")]]["trans_store"]=$row[csf("trans_store")];
			$details_data[$row[csf("prod_id")]][$row[csf("batch_lot")]]["item_number"]=$row[csf("item_number")];
			$details_data[$row[csf("prod_id")]][$row[csf("batch_lot")]]["item_code"]=$row[csf("item_code")];
			$details_data[$row[csf("prod_id")]][$row[csf("batch_lot")]]["item_category_id"]=$row[csf("item_category_id")];
			$details_data[$row[csf("prod_id")]][$row[csf("batch_lot")]]["item_group_id"]=$row[csf("item_group_id")];
			$details_data[$row[csf("prod_id")]][$row[csf("batch_lot")]]["avg_rate_per_unit"]=$row[csf("avg_rate_per_unit")];
			$details_data[$row[csf("prod_id")]][$row[csf("batch_lot")]]["sub_group_name"]=$row[csf("sub_group_name")];
			$details_data[$row[csf("prod_id")]][$row[csf("batch_lot")]]["item_description"]=$row[csf("item_description")];
			$details_data[$row[csf("prod_id")]][$row[csf("batch_lot")]]["item_size"]=$row[csf("item_size")];
			$details_data[$row[csf("prod_id")]][$row[csf("batch_lot")]]["unit_of_measure"]=$row[csf("unit_of_measure")];
			$details_data[$row[csf("prod_id")]][$row[csf("batch_lot")]]["re_order_label"]=$row[csf("re_order_label")];

			$details_data[$row[csf("prod_id")]][$row[csf("batch_lot")]]["opening_total_receive"]+=$row[csf("opening_total_receive")];
			$details_data[$row[csf("prod_id")]][$row[csf("batch_lot")]]["opening_total_receive_amt"]+=$row[csf("opening_total_receive_amt")];
			$details_data[$row[csf("prod_id")]][$row[csf("batch_lot")]]["opening_total_issue"]+=$row[csf("opening_total_issue")];
			$details_data[$row[csf("prod_id")]][$row[csf("batch_lot")]]["opening_total_issue_amt"]+=$row[csf("opening_total_issue_amt")];
			$details_data[$row[csf("prod_id")]][$row[csf("batch_lot")]]["total_rcv_amount"]+=$row[csf("total_rcv_amount")];
			$details_data[$row[csf("prod_id")]][$row[csf("batch_lot")]]["total_issue_amount"]+=$row[csf("total_issue_amount")];
			if($loanRcvData[$row[csf("mst_id")]])
			{
				$details_data[$row[csf("prod_id")]][$row[csf("batch_lot")]]["loan_rcv"]+=$row[csf("purchase")];
			}
			else
			{
				$details_data[$row[csf("prod_id")]][$row[csf("batch_lot")]]["purchase"]+=$row[csf("purchase")];
			}
			$details_data[$row[csf("prod_id")]][$row[csf("batch_lot")]]["receive_return"]+=$row[csf("receive_return")];
			if($loanIssueData[$row[csf("mst_id")]])
			{
				$details_data[$row[csf("prod_id")]][$row[csf("batch_lot")]]["loan_iss"]+=$row[csf("issue")];
			}
			else
			{
				$details_data[$row[csf("prod_id")]][$row[csf("batch_lot")]]["issue"]+=$row[csf("issue")];
			}
			$details_data[$row[csf("prod_id")]][$row[csf("batch_lot")]]["issue_return"]+=$row[csf("issue_return")];
			$details_data[$row[csf("prod_id")]][$row[csf("batch_lot")]]["transfer_in"]+=$row[csf("transfer_in")];
			$details_data[$row[csf("prod_id")]][$row[csf("batch_lot")]]["transfer_out"]+=$row[csf("transfer_out")];
			$details_data[$row[csf("prod_id")]][$row[csf("batch_lot")]]["total_rcv_amt_value"]+=$row[csf("total_rcv_amt_value")];
			$details_data[$row[csf("prod_id")]][$row[csf("batch_lot")]]["total_iss_amt_value"]+=$row[csf("total_iss_amt_value")];
		}
		$i=1;
		// ob_start();
		?>
		<div>
			<table style="width:<? echo $table_width; ?>px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_header_1" rules="all" align="left">
				<thead>
					<tr class="form_caption" style="border:none;">
						<td colspan="24" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td>
					</tr>
					<tr style="border:none;">
						<td colspan="24" align="center" style="border:none; font-size:14px;">
							<b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="24" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
						</td>
					</tr>
					<tr>
						<th rowspan="2" width="40">SL</th>
						<th colspan="10">Description</th>
						<th rowspan="2" width="100">Opening Stock</th>
						<th colspan="5">Receive</th>
						<th colspan="5">Issue</th>
						<th rowspan="2" width="100">Closing Stock</th>
                        <?
						if($variable_string_inventory!=1)
						{
							?>
							<th rowspan="2" width="100">Avg. Rate</th>
							<th rowspan="2" width="100">Stock Value</th>
							<?
						}
						?>

						<?
						if($cbo_yes_no==1)
						{
							?>
							<th rowspan="2" width="120">Store Name</th>
							<?
						}
						?>
						<th rowspan="2">DOH</th>
					</tr>
					<tr>
						<th width="60">Prod.ID</th>
                        <th width="100">Lot</th>
						<th width="100">Item Number</th>
						<th width="60">Item Code</th>
						<th width="110">Item Category</th>
						<th width="100">Item Group</th>
						<th width="100">Item Sub-group</th>
						<th width="180">Item Description</th>
						<th width="70">Item Size</th>
						<th width="60">UoM</th>
						<th width="80">Purchase</th>
						<th width="80">Issue Return</th>
						<th width="80">Transfer In</th>
                        <th width="80">Loan Received</th>
						<th width="100">Total Received</th>
						<th width="80">Issue</th>
                        <th width="80">As Loan</th>
						<th width="80">Receive Return</th>
						<th width="80">Transfer Out</th>
						<th width="100">Total Issue</th>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $div_width; ?>px; max-height:350px; overflow-y:scroll;" id="scroll_body"  >
				<table style="width:<? echo $table_width; ?>px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left">
					<?
					foreach($details_data as $prod_id=>$lot_data)
					{
						foreach($lot_data as $lot_number=>$row)
						{
							$openingBalance = $row[("opening_total_receive")]-$row[("opening_total_issue")];
							$totalReceive = $row[("purchase")]+$row[("issue_return")]+$row[("transfer_in")]+$row[("loan_rcv")];
							$totalIssue =$row[("issue")]+$row[("receive_return")]+$row[("transfer_out")]+$row[("loan_iss")];
							$closingStock=$totalReceive-$totalIssue+$openingBalance;
							//$openingBalanceValue = $row[("opening_total_receive_amt")]-$row[("opening_total_issue_amt")];

							// $openingBalanceRate = $openingBalanceValue = 0;
							// if($row[("opening_total_receive")] > 0){
							// 	$openingBalanceRate = $row[("opening_total_receive_amt")]/$row[("opening_total_receive")];
							// }
							//$openingBalanceValue = $openingBalance *$openingBalanceRate;
							$openingBalanceValue = $row[("opening_total_receive_amt")]-$row[("opening_total_issue_amt")];
							$totalReceiveValue = $row[("total_rcv_amt_value")];
							$totalIssueValue = $row[('total_iss_amt_value')];
							$stockValue=($openingBalanceValue + $totalReceiveValue) -$totalIssueValue;
							$prod_rate=$stockValue/$closingStock;
							$re_order_label = $row[('re_order_label')];

							if($value_with==1)
							{
								if(number_format($closingStock,2) != 0.00 || number_format($openingBalance,2)!= 0.00 )
								{
									//if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
									/*if( $row[("transaction_type")]==1 || $row[("transaction_type")]==2 || $row[("transaction_type")]==3 || $row[("transaction_type")]==4 )
									$stylecolor='style="color:#A61000"';
									else
									$stylecolor='style="color:#000000"';*/
									if($closingStock <= $re_order_label){$bgcolor="red";}elseif($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="40" align="center"><? echo $i; ?></td>
										<td style="word-break:break-all" width="60" align="center"><p><? echo $row[("prod_id")]; ?></p></td>
										<td style="word-break:break-all" width="100" align="center"><p><? echo $lot_number; ?></p></td>
										<td style="word-break:break-all" width="100" align="center"><p><? echo $row[("item_number")]; ?></p></td>
										<td style="word-break:break-all" width="60" align="center"><p><? echo $row[("item_code")]; ?></p></td>
										<td style="word-break:break-all" width="110"><p><? echo $item_category[$row[("item_category_id")]]; ?></p></td>
										<td style="word-break:break-all" width="100"><p><? echo $itemgroupArr[$row[("item_group_id")]]; ?></p></td>
										<td style="word-break:break-all" width="100"><p><? echo $row[("sub_group_name")]; ?></p></td>
										<td style="word-break:break-all" width="180"><p><? echo $row[("item_description")]; ?></p></td>
										<td style="word-break:break-all" width="70"><p><? echo $row[("item_size")]; ?>&nbsp;</p></td>
										<td style="word-break:break-all" width="60" align="center"><p><? echo $unit_of_measurement[$row[("unit_of_measure")]]; ?></p></td>
										<td style="word-break:break-all" width="100" align="right"><? echo  number_format($openingBalance,2);//$openingBalance ?></td>
										<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("purchase")],2); ?></td>
										<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("issue_return")],2); ?></td>

										<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("transfer_in")],2); ?></td>
										<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("loan_rcv")],2); ?></td>
										<td style="word-break:break-all" width="100" align="right"><? echo number_format($totalReceive,2); ?></td>
										<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("issue")],2); ?></td>
										<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("loan_iss")],2); ?></td>
										<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("receive_return")],2); ?></td>
										<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("transfer_out")],2); ?></td>
										<td style="word-break:break-all" width="100" align="right"><? echo number_format($totalIssue,2); ?></td>
										<td style="word-break:break-all" width="100" align="right"  title="<? echo $openingBalance;?>"><? echo number_format($closingStock,2); ?></td>
										<?
										if($variable_string_inventory!=1)
										{
											?>
											<td style="word-break:break-all" width="100" align="right" title="<? echo $openingBalanceValue;?>"><? if(number_format($closingStock,2)>0) echo number_format($prod_rate,4); else echo "0.00"; ?></td>
											<td style="word-break:break-all" width="100" align="right" title="<? echo $row[("total_rcv_amount")]."==".$row[("total_issue_amount")]; ?>"><? if(number_format($closingStock,2)>0) echo number_format($stockValue,2); else echo "0.00"; ?></td>
										<?
										}
										if($cbo_yes_no==1)
										{
										?>
										<td width="120"><p><? echo $store_name_arr[$row[("trans_store")]]; ?></p></td>
										<?
										}
										?>
										<td align="center"><? echo $days_doh[$row[('prod_id')]]['daysonhand']; //$daysOnHand; ?></td>
									</tr>
									<?
									$total_openingBalance+=$openingBalance;
									$total_stockValue+=$stockValue;
									$total_purchase+=$row[("purchase")];
									$total_issue_return+=$row[("issue_return")];
									$total_transfer_in+=$row[("transfer_in")];
									$total_loan_rcv+=$row[("loan_rcv")];
									$total_receive_qnty+=$totalReceive;
									$total_issue_qnty+=$row[("issue")];
									$total_loan_iss+=$row[("loan_iss")];
									$total_receive_return_qnty+=$row[("receive_return")];
									$total_transfer_out+=$row[("transfer_out")];
									$total_issue_recv_return_qnty+=$totalIssue;
									$total_closing_stock+=$closingStock;
									$i++;
								}
							}
							else
							{
								//if(number_format($closingStock,3)>0.000 || number_format($openingBalance,3)>0.000 || number_format($totalReceive,3)>0.000 || number_format($totalIssue,3)>0.000)
								//{
									//if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
									/*if( $row[("transaction_type")]==1 || $row[("transaction_type")]==2 || $row[("transaction_type")]==3 || $row[("transaction_type")]==4 )
									$stylecolor='style="color:#A61000"';
									else
									$stylecolor='style="color:#000000"';*/

									if($closingStock <= $re_order_label){$bgcolor="red";}elseif($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="40" align="center"><? echo $i; ?></td>
										<td style="word-break:break-all" width="60" align="center"><p><? echo $row[("prod_id")]; ?></p></td>
										<td style="word-break:break-all" width="100" align="center"><p><? echo $lot_number; ?></p></td>
                                        <td style="word-break:break-all" width="100" align="center"><p><? echo $row[("item_number")]; ?></p></td>
										<td style="word-break:break-all" width="60" align="center"><p><? echo $row[("item_code")]; ?></p></td>
										<td style="word-break:break-all" width="110"><p><? echo $item_category[$row[("item_category_id")]]; ?></p></td>
										<td style="word-break:break-all" width="100"><p><? echo $itemgroupArr[$row[("item_group_id")]]; ?></p></td>
										<td style="word-break:break-all" width="100"><p><? echo $row[("sub_group_name")]; ?></p></td>
										<td style="word-break:break-all" width="180"><p><? echo $row[("item_description")]; ?></p></td>
										<td style="word-break:break-all" width="70"><p><? echo $row[("item_size")]; ?>&nbsp;</p></td>
										<td style="word-break:break-all" width="60" align="center"><p><? echo $unit_of_measurement[$row[("unit_of_measure")]]; ?></p></td>
										<td style="word-break:break-all" width="100" align="right"><? echo  number_format($openingBalance,2);//$openingBalance ?></td>
										<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("purchase")],2); ?></td>
										<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("issue_return")],2); ?></td>
										<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("transfer_in")],2); ?></td>
										<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("loan_rcv")],2); ?></td>
										<td style="word-break:break-all" width="100" align="right"><? echo number_format($totalReceive,2); ?></td>
										<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("issue")],2); ?></td>
										<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("loan_iss")],2); ?></td>
										<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("receive_return")],2); ?></td>
										<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("transfer_out")],2); ?></td>
										<td style="word-break:break-all" width="100" align="right"><? echo number_format($totalIssue,2); ?></td>
										<td style="word-break:break-all" width="100" align="right"  title="<? echo $openingBalance;?>"><? echo number_format($closingStock,2); ?></td>
										<?
										if($variable_string_inventory!=1)
										{
											?>
											<td style="word-break:break-all" width="100" align="right" title="<? echo $openingBalanceValue;?>"><? if(number_format($closingStock,2)>0) echo number_format($prod_rate,4); else echo "0.00"; ?></td>
											<td style="word-break:break-all" width="100" align="right" title="<? echo $row[("total_rcv_amount")]."==".$row[("total_issue_amount")]; ?>"><? if(number_format($closingStock,2)>0) echo number_format($stockValue,2); else echo "0.00"; ?></td>
										<?
										}
										if($cbo_yes_no==1)
										{
										?>
										<td width="120"><p><? echo $store_name_arr[$row[("trans_store")]]; ?></p></td>
										<?
										}
										?>
										<td align="center"><? echo $days_doh[$row[('prod_id')]]['daysonhand']; //$daysOnHand; ?></td>
									</tr>
									<?
									$total_openingBalance+=$openingBalance;
									$total_stockValue+=$stockValue;
									$total_purchase+=$row[("purchase")];
									$total_issue_return+=$row[("issue_return")];
									$total_transfer_in+=$row[("transfer_in")];
									$total_loan_rcv+=$row[("loan_rcv")];
									$total_receive_qnty+=$totalReceive;
									$total_issue_qnty+=$row[("issue")];
									$total_loan_iss+=$row[("loan_iss")];
									$total_receive_return_qnty+=$row[("receive_return")];
									$total_transfer_out+=$row[("transfer_out")];
									$total_issue_recv_return_qnty+=$totalIssue;
									$total_closing_stock+=$closingStock;
									$i++;
								//}
							}
						}
					}
					?>
					<tr bgcolor="#CCCCCC" style="font-weight:bold">
						<td colspan="11" align="right"><strong>Grand Total</strong></td>
						<td  align="right"><? echo number_format($total_openingBalance,2); ?></td>
						<td align="right"><? echo number_format($total_purchase,2); ?></td>
						<td align="right"><? echo number_format($total_issue_return,2); ?></td>
						<td align="right"><? echo number_format($total_transfer_in,2); ?></td>
                        <td align="right"><? echo number_format($total_loan_rcv,2); ?></td>
						<td align="right"><? echo number_format($total_receive_qnty,2); ?></td>
						<td align="right"><? echo number_format($total_issue_qnty,2); ?></td>
                        <td align="right"><? echo number_format($total_loan_iss,2); ?></td>
						<td align="right"><? echo number_format($total_receive_return_qnty,2); ?></td>
						<td align="right"><? echo number_format($total_transfer_out,2); ?></td>
						<td  align="right"><? echo number_format($total_issue_recv_return_qnty,2); ?></td>
						<td  align="right"><? echo number_format($total_closing_stock,2); ?></td>
                        <?
						if($variable_string_inventory!=1)
						{
							?>
                            <td>&nbsp;</td>
                            <td  align="right"><? echo number_format($total_stockValue,2); ?></td>
                            <?
						}
						if($cbo_yes_no==1)
						{
							?>
							<td>&nbsp;</td>
							<?
						}
						?>
						<td>&nbsp;</td>
					</tr>
				</table>
			</div>
		</div>
        <?
    }
	else if($report_type == 11) // show excel
	{
		if($cbo_yes_no==1)
		{
			if($variable_string_inventory!=1)
			{
				$div_width="2380";
				$table_width="2380";
			}
			else
			{
				$div_width="2180";
				$table_width="2180";
			}
		}
		else
		{
			if($variable_string_inventory!=1)
			{
				$div_width="2480";
				$table_width="2450";
			}
			else
			{
				$div_width="2080";
				$table_width="2050";
			}
		}
		//$cbo_company_name
		$loanRcvData = return_library_array("select id from inv_receive_master where status_active=1 and is_deleted=0 and company_id=$cbo_company_name and entry_form=20 and receive_purpose=1","id","id");
		$loanIssueData = return_library_array("select id from inv_issue_master where status_active=1 and is_deleted=0 and company_id=$cbo_company_name and entry_form=21 and issue_purpose=5","id","id");

		$item_cate_credential_cond="".implode(",",array_diff(array_flip($general_item_category), array("4")))."";
		if($variable_store_wise_rate==1)
		{
			$sql="SELECT a.prod_id , a.store_id, b.id, b.item_number, b.item_code,b.item_number,b.model, b.item_category_id, b.item_group_id, b.avg_rate_per_unit, b.sub_group_name, b.item_description, b.item_size, b.unit_of_measure, a.transaction_type, b.re_order_label, a.mst_id,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.cons_quantity else 0 end) as opening_total_receive,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.store_amount else 0 end) as opening_total_receive_amt,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.cons_quantity else 0 end) as opening_total_issue,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.store_amount else 0 end) as opening_total_issue_amt,
			(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (1,4,5)  then a.store_amount else 0 end) as total_rcv_amount,
			(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (2,3,6)  then a.store_amount else 0 end) as total_issue_amount,
			(case when a.transaction_type=1 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as purchase,
			(case when a.transaction_type=3 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as receive_return,
			(case when a.transaction_type=2 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue,
			(case when a.transaction_type=4 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue_return,
			(case when a.transaction_type=5 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_in,
			(case when a.transaction_type=6 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_out,
			(case when a.transaction_type in (1,4,5) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.store_amount else 0 end) as total_rcv_amt_value,
			(case when a.transaction_type in (2,3,6) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.store_amount else 0 end) as total_iss_amt_value
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.transaction_type in (1,2,3,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category  in($item_cate_credential_cond) and b.entry_form<>24  $str_cond $zero_cond
			order by b.item_category_id, b.item_group_id , a.prod_id";
		}
		else
		{
			$sql="SELECT a.prod_id , a.store_id, b.id, b.item_number, b.item_code,b.item_number,b.model, b.item_category_id, b.item_group_id, b.avg_rate_per_unit, b.sub_group_name, b.item_description, b.item_size, b.unit_of_measure, a.transaction_type, b.re_order_label, a.mst_id,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.cons_quantity else 0 end) as opening_total_receive,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.cons_amount else 0 end) as opening_total_receive_amt,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.cons_quantity else 0 end) as opening_total_issue,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.cons_amount else 0 end) as opening_total_issue_amt,
			(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (1,4,5)  then a.cons_amount else 0 end) as total_rcv_amount,
			(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (2,3,6)  then a.cons_amount else 0 end) as total_issue_amount,
			(case when a.transaction_type=1 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as purchase,
			(case when a.transaction_type=3 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as receive_return,
			(case when a.transaction_type=2 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue,
			(case when a.transaction_type=4 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue_return,
			(case when a.transaction_type=5 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_in,
			(case when a.transaction_type=6 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_out,
			(case when a.transaction_type in (1,4,5) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_amount else 0 end) as total_rcv_amt_value,
			(case when a.transaction_type in (2,3,6) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_amount else 0 end) as total_iss_amt_value
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.transaction_type in (1,2,3,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category  in($item_cate_credential_cond) and b.entry_form<>24  $str_cond $zero_cond
			order by b.item_category_id, b.item_group_id , a.prod_id";
		}

		//echo  $sql;die;
		$result = sql_select($sql);
		$details_data=array();
		foreach($result as $row)
		{
			if($cbo_store_name>0)
			{
				if($cbo_store_name==$row[csf("store_id")])
				{
					$details_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
					$details_data[$row[csf("prod_id")]]["trans_store"]=$row[csf("store_id")];
					$details_data[$row[csf("prod_id")]]["item_number"]=$row[csf("item_number")];
					$details_data[$row[csf("prod_id")]]["item_code"]=$row[csf("item_code")];
					$details_data[$row[csf("prod_id")]]["item_category_id"]=$row[csf("item_category_id")];
					$details_data[$row[csf("prod_id")]]["item_group_id"]=$row[csf("item_group_id")];
					$details_data[$row[csf("prod_id")]]["avg_rate_per_unit"]=$row[csf("avg_rate_per_unit")];
					$details_data[$row[csf("prod_id")]]["sub_group_name"]=$row[csf("sub_group_name")];
					$details_data[$row[csf("prod_id")]]["item_description"]=$row[csf("item_description")];
					$details_data[$row[csf("prod_id")]]["item_size"]=$row[csf("item_size")];
					$details_data[$row[csf("prod_id")]]["model"]=$row[csf("model")];
					$details_data[$row[csf("prod_id")]]["unit_of_measure"]=$row[csf("unit_of_measure")];
					$details_data[$row[csf("prod_id")]]["re_order_label"]=$row[csf("re_order_label")];

					$details_data[$row[csf("prod_id")]]["opening_total_receive"]+=$row[csf("opening_total_receive")];
					$details_data[$row[csf("prod_id")]]["opening_total_receive_amt"]+=$row[csf("opening_total_receive_amt")];
					$details_data[$row[csf("prod_id")]]["opening_total_issue"]+=$row[csf("opening_total_issue")];
					$details_data[$row[csf("prod_id")]]["opening_total_issue_amt"]+=$row[csf("opening_total_issue_amt")];
					$details_data[$row[csf("prod_id")]]["total_rcv_amount"]+=$row[csf("total_rcv_amount")];
					$details_data[$row[csf("prod_id")]]["total_issue_amount"]+=$row[csf("total_issue_amount")];
					if($loanRcvData[$row[csf("mst_id")]])
					{
						$details_data[$row[csf("prod_id")]]["loan_rcv"]+=$row[csf("purchase")];
					}
					else
					{
						$details_data[$row[csf("prod_id")]]["purchase"]+=$row[csf("purchase")];
					}
					$details_data[$row[csf("prod_id")]]["receive_return"]+=$row[csf("receive_return")];
					if($loanIssueData[$row[csf("mst_id")]])
					{
						$details_data[$row[csf("prod_id")]]["loan_iss"]+=$row[csf("issue")];
					}
					else
					{
						$details_data[$row[csf("prod_id")]]["issue"]+=$row[csf("issue")];
					}
					$details_data[$row[csf("prod_id")]]["issue_return"]+=$row[csf("issue_return")];
					$details_data[$row[csf("prod_id")]]["transfer_in"]+=$row[csf("transfer_in")];
					$details_data[$row[csf("prod_id")]]["transfer_out"]+=$row[csf("transfer_out")];
					$details_data[$row[csf("prod_id")]]["total_rcv_amt_value"]+=$row[csf("total_rcv_amt_value")];
					$details_data[$row[csf("prod_id")]]["total_iss_amt_value"]+=$row[csf("total_iss_amt_value")];
				}

				if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5)
				{
					$item_wise_stock[$row[csf("prod_id")]]["bal_qnty"]+=($row[csf("opening_total_receive")]+$row[csf("purchase")]+$row[csf("issue_return")]+$row[csf("transfer_in")]);
					$item_wise_stock[$row[csf("prod_id")]]["bal_amt"]+=($row[csf("opening_total_receive_amt")]+$row[csf("total_rcv_amt_value")]);
					$item_wise_stock[$row[csf("prod_id")]]["bal_qnty_opeinng"]+=$row[csf("opening_total_receive")];
					$item_wise_stock[$row[csf("prod_id")]]["bal_amt_opening"]+=$row[csf("opening_total_receive_amt")];
				}
				else
				{
					$item_wise_stock[$row[csf("prod_id")]]["bal_qnty"]-=($row[csf("opening_total_issue")]+$row[csf("issue")]+$row[csf("receive_return")]+$row[csf("transfer_out")]);
					$item_wise_stock[$row[csf("prod_id")]]["bal_amt"]-=($row[csf("opening_total_issue_amt")]+$row[csf("total_iss_amt_value")]);
					$item_wise_stock[$row[csf("prod_id")]]["bal_qnty_opeinng"]-=$row[csf("opening_total_issue")];
					$item_wise_stock[$row[csf("prod_id")]]["bal_amt_opening"]-=$row[csf("opening_total_issue_amt")];
				}

			}
			else
			{
				$details_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
				$details_data[$row[csf("prod_id")]]["trans_store"]=$row[csf("store_id")];
				$details_data[$row[csf("prod_id")]]["item_number"]=$row[csf("item_number")];
				$details_data[$row[csf("prod_id")]]["item_code"]=$row[csf("item_code")];
				$details_data[$row[csf("prod_id")]]["item_category_id"]=$row[csf("item_category_id")];
				$details_data[$row[csf("prod_id")]]["item_group_id"]=$row[csf("item_group_id")];
				$details_data[$row[csf("prod_id")]]["avg_rate_per_unit"]=$row[csf("avg_rate_per_unit")];
				$details_data[$row[csf("prod_id")]]["sub_group_name"]=$row[csf("sub_group_name")];
				$details_data[$row[csf("prod_id")]]["item_description"]=$row[csf("item_description")];
				$details_data[$row[csf("prod_id")]]["item_size"]=$row[csf("item_size")];
				$details_data[$row[csf("prod_id")]]["model"]=$row[csf("model")];
				$details_data[$row[csf("prod_id")]]["unit_of_measure"]=$row[csf("unit_of_measure")];
				$details_data[$row[csf("prod_id")]]["re_order_label"]=$row[csf("re_order_label")];

				$details_data[$row[csf("prod_id")]]["opening_total_receive"]+=$row[csf("opening_total_receive")];
				$details_data[$row[csf("prod_id")]]["opening_total_receive_amt"]+=$row[csf("opening_total_receive_amt")];
				$details_data[$row[csf("prod_id")]]["opening_total_issue"]+=$row[csf("opening_total_issue")];
				$details_data[$row[csf("prod_id")]]["opening_total_issue_amt"]+=$row[csf("opening_total_issue_amt")];
				$details_data[$row[csf("prod_id")]]["total_rcv_amount"]+=$row[csf("total_rcv_amount")];
				$details_data[$row[csf("prod_id")]]["total_issue_amount"]+=$row[csf("total_issue_amount")];
				if($loanRcvData[$row[csf("mst_id")]])
				{
					$details_data[$row[csf("prod_id")]]["loan_rcv"]+=$row[csf("purchase")];
				}
				else
				{
					$details_data[$row[csf("prod_id")]]["purchase"]+=$row[csf("purchase")];
				}
				$details_data[$row[csf("prod_id")]]["receive_return"]+=$row[csf("receive_return")];
				if($loanIssueData[$row[csf("mst_id")]])
				{
					$details_data[$row[csf("prod_id")]]["loan_iss"]+=$row[csf("issue")];
				}
				else
				{
					$details_data[$row[csf("prod_id")]]["issue"]+=$row[csf("issue")];
				}
				$details_data[$row[csf("prod_id")]]["issue_return"]+=$row[csf("issue_return")];
				$details_data[$row[csf("prod_id")]]["transfer_in"]+=$row[csf("transfer_in")];
				$details_data[$row[csf("prod_id")]]["transfer_out"]+=$row[csf("transfer_out")];
				$details_data[$row[csf("prod_id")]]["total_rcv_amt_value"]+=$row[csf("total_rcv_amt_value")];
				$details_data[$row[csf("prod_id")]]["total_iss_amt_value"]+=$row[csf("total_iss_amt_value")];
			}

		}
		$i=1;
		//echo "<pre>";print_r($details_data);die;
		//ob_start();
		?>

		<table style="width:<? echo $table_width; ?>px" border="1" cellpadding="2" cellspacing="0" id="table_header_1" rules="all" align="left">
				<tr>
					<th colspan="27"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
				</tr>
		<table>
		<!-- <? ob_start(); ?> -->
		<div>
			<table style="width:<? echo $table_width; ?>px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_header_1" rules="all" align="left">
				<thead>
					<tr class="form_caption" style="border:none;">
						<td colspan="26" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td>
					</tr>
					<tr style="border:none;">
						<td colspan="26" align="center" style="border:none; font-size:14px;">
							<b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="26" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
						</td>
					</tr>
					<tr>
						<th rowspan="2" width="40">SL</th>
						<th colspan="10">Description</th>
						<th rowspan="2" width="100">Opening Stock</th>
						<th colspan="5">Receive</th>
						<th colspan="5">Issue</th>
						<th rowspan="2" width="100">Closing Stock</th>
                        <?
						if($variable_string_inventory!=1)
						{
							?>
							<th rowspan="2" width="100">Avg. Rate</th>
							<th rowspan="2" width="100">Stock Value</th>
							<?
						}
						?>

						<?
						if($cbo_yes_no==1)
						{
							?>
							<th rowspan="2" width="120">Store Name</th>
							<?
						}
						?>
						<th width="100" rowspan="2">DOH</th>
					</tr>
					<tr>
						<th width="60">Prod.ID</th>
						<th width="100">Item Number</th>
						<th width="60">Item Code</th>
						<th width="110">Item Category</th>
						<th width="100">Item Group</th>
						<th width="100">Item Sub-group</th>
						<th width="180">Item Description</th>
						<th width="70">Item Size</th>
						<th width="70">Model</th>
						<th width="60">UoM</th>
						<th width="80">Purchase</th>
						<th width="80">Issue Return</th>
						<th width="80">Transfer In</th>
                        <th width="80">Loan Received</th>
						<th width="100">Total Received</th>
						<th width="80">Issue</th>
                        <th width="80">As Loan</th>
						<th width="80">Receive Return</th>
						<th width="80">Transfer Out</th>
						<th width="100">Total Issue</th>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $div_width; ?>px; max-height:350px; overflow-y:scroll;" id="scroll_body"  >
				<table style="width:<? echo $table_width; ?>px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left">
					<?
					foreach($details_data as $prod_id=>$row)
					{
						$openingBalance = $row[("opening_total_receive")]-$row[("opening_total_issue")];
						$totalReceive = $row[("purchase")]+$row[("issue_return")]+$row[("transfer_in")]+$row[("loan_rcv")];
						$totalIssue =$row[("issue")]+$row[("receive_return")]+$row[("transfer_out")]+$row[("loan_iss")];
						$closingStock=$totalReceive-$totalIssue+$openingBalance;
						//$openingBalanceValue = $row[("opening_total_receive_amt")]-$row[("opening_total_issue_amt")];

						// $openingBalanceRate = $openingBalanceValue = 0;
						// if($row[("opening_total_receive")] > 0){
						// 	$openingBalanceRate = $row[("opening_total_receive_amt")]/$row[("opening_total_receive")];
						// }
						//$openingBalanceValue = $openingBalance *$openingBalanceRate;
						$openingBalanceValue = $row[("opening_total_receive_amt")]-$row[("opening_total_issue_amt")];
						$totalReceiveValue = $row[("total_rcv_amt_value")];
						$totalIssueValue = $row[('total_iss_amt_value')];
						$stockValue=($openingBalanceValue + $totalReceiveValue) -$totalIssueValue;
						$prod_rate=$stockValue/$closingStock;
						$re_order_label = $row[('re_order_label')];
						if($value_with ==1)
						{
							if(number_format($openingBalance,2) > 0.00 || number_format($closingStock,2) > 0.00 )
							{
							//if(number_format($closingStock,2)>0.00 || number_format($openingBalance,2)>0.00 )
							//{
								//if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
								/*if( $row[("transaction_type")]==1 || $row[("transaction_type")]==2 || $row[("transaction_type")]==3 || $row[("transaction_type")]==4 )
								$stylecolor='style="color:#A61000"';
								else
								$stylecolor='style="color:#000000"';*/
								if($closingStock <= $re_order_label){$bgcolor="red";}elseif($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40" align="center" ><? echo $i; ?></td>
									<td style="word-break:break-all" width="60" align="center"><p><? echo $row[("prod_id")]; ?></p></td>
									<td style="word-break:break-all" width="100" align="center"><p><? echo $row[("item_number")]; ?></p></td>
									<td style="word-break:break-all" width="60" align="center"><p><? echo $row[("item_code")]; ?></p></td>
									<td style="word-break:break-all" width="110"><p><? echo $item_category[$row[("item_category_id")]]; ?></p></td>
									<td style="word-break:break-all" width="100"><p><? echo $itemgroupArr[$row[("item_group_id")]]; ?></p></td>
									<td style="word-break:break-all" width="100"><p><? echo $row[("sub_group_name")]; ?></p></td>
									<td style="word-break:break-all" width="180"><p><? echo $row[("item_description")]; ?></p></td>
									<td style="word-break:break-all" width="70"><p><? echo $row[("item_size")]; ?></p></td>
									<td style="word-break:break-all" width="70"><p><? echo $row[("model")]; ?></p></td>
									<td style="word-break:break-all" width="60" align="center"><p><? echo $unit_of_measurement[$row[("unit_of_measure")]]; ?></p></td>
									<td style="word-break:break-all" width="100" align="right"><? echo  number_format($openingBalance,2);//$openingBalance ?></td>
									<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("purchase")],2); ?></td>
									<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("issue_return")],2); ?></td>
									<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("transfer_in")],2); ?></td>
                                    <td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("loan_rcv")],2); ?></td>
									<td style="word-break:break-all" width="100" align="right"><? echo number_format($totalReceive,2); ?></td>
									<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("issue")],2); ?></td>
                                    <td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("loan_iss")],2); ?></td>
									<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("receive_return")],2); ?></td>
									<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("transfer_out")],2); ?></td>
									<td style="word-break:break-all" width="100" align="right"><? echo number_format($totalIssue,2); ?></td>
									<td style="word-break:break-all" width="100" align="right"  title="<? echo $openingBalance;?>"><? echo number_format($closingStock,2); ?></td>
                                    <?
                                    if($variable_string_inventory!=1)
									{
										?>
                                        <td style="word-break:break-all" width="100" align="right" title="<? echo $openingBalanceValue;?>">
										<?
										if(number_format($closingStock,2)>0) echo number_format($prod_rate,4); else echo "0.00";
										?>
                                        </td>
                                        <td style="word-break:break-all" width="100" align="right" title="<? echo $row[("total_rcv_amount")]."==".$row[("total_issue_amount")]; ?>"><? if(number_format($closingStock,2)>0) echo number_format($stockValue,2); else echo "0.00"; ?></td>
									<?
									}
									if($cbo_yes_no==1)
									{
									?>
									<td width="120" style="word-break:break-all"><p><? echo $store_name_arr[$row[("trans_store")]]; ?></p></td>
									<?
									}
									?>
									<td align="center" width="100" style="word-break:break-all"><? echo $days_doh[$row[('prod_id')]]['daysonhand']; //$daysOnHand; ?></td>
								</tr>
								<?
								$total_openingBalance+=$openingBalance;
								$total_stockValue+=$stockValue;
								$total_purchase+=$row[("purchase")];
								$total_issue_return+=$row[("issue_return")];
								$total_transfer_in+=$row[("transfer_in")];
								$total_loan_rcv+=$row[("loan_rcv")];
								$total_receive_qnty+=$totalReceive;
								$total_issue_qnty+=$row[("issue")];
								$total_loan_iss+=$row[("loan_iss")];
								$total_receive_return_qnty+=$row[("receive_return")];
								$total_transfer_out+=$row[("transfer_out")];
								$total_issue_recv_return_qnty+=$totalIssue;
								$total_closing_stock+=$closingStock;
								$i++;
							}
						}
						else
						{
							//if(number_format($closingStock,3)>0.000 || number_format($openingBalance,3)>0.000 || number_format($totalReceive,3)>0.000 || number_format($totalIssue,3)>0.000)
							//{
								//if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
								/*if( $row[("transaction_type")]==1 || $row[("transaction_type")]==2 || $row[("transaction_type")]==3 || $row[("transaction_type")]==4 )
								$stylecolor='style="color:#A61000"';
								else
								$stylecolor='style="color:#000000"';*/

								if($closingStock <= $re_order_label){$bgcolor="red";}elseif($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40" align="center"><? echo $i; ?></td>
									<td style="word-break:break-all" width="60" align="center"><p><? echo $row[("prod_id")]; ?></p></td>
									<td style="word-break:break-all" width="100" align="center"><p><? echo $row[("item_number")]; ?></p></td>
									<td style="word-break:break-all" width="60" align="center"><p><? echo $row[("item_code")]; ?></p></td>
									<td style="word-break:break-all" width="110"><p><? echo $item_category[$row[("item_category_id")]]; ?></p></td>
									<td style="word-break:break-all" width="100"><p><? echo $itemgroupArr[$row[("item_group_id")]]; ?></p></td>
									<td style="word-break:break-all" width="100"><p><? echo $row[("sub_group_name")]; ?></p></td>
									<td style="word-break:break-all" width="180"><p><? echo $row[("item_description")]; ?></p></td>
									<td style="word-break:break-all" width="70"><p><? echo $row[("item_size")]; ?></p></td>
									<td style="word-break:break-all" width="70"><p><? echo $row[("model")]; ?></p></td>
									<td style="word-break:break-all" width="60" align="center"><p><? echo $unit_of_measurement[$row[("unit_of_measure")]]; ?></p></td>
									<td style="word-break:break-all" width="100" align="right"><? echo  number_format($openingBalance,2);//$openingBalance ?></td>
									<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("purchase")],2); ?></td>
									<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("issue_return")],2); ?></td>
									<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("transfer_in")],2); ?></td>
                                    <td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("loan_rcv")],2); ?></td>
									<td style="word-break:break-all" width="100" align="right"><? echo number_format($totalReceive,2); ?></td>
									<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("issue")],2); ?></td>
                                    <td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("loan_iss")],2); ?></td>
									<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("receive_return")],2); ?></td>
									<td style="word-break:break-all" width="80" align="right"><? echo number_format($row[("transfer_out")],2); ?></td>
									<td style="word-break:break-all" width="100" align="right"><? echo number_format($totalIssue,2); ?></td>
									<td style="word-break:break-all" width="100" align="right"  title="<? echo $openingBalance;?>"><? echo number_format($closingStock,2); ?></td>
                                    <?
                                    if($variable_string_inventory!=1)
									{
										?>
                                        <td style="word-break:break-all" width="100" align="right" title="<? echo $openingBalanceValue;?>">
                                        <?
										if(number_format($closingStock,2)>0) echo number_format($prod_rate,4); else echo "0.00";
										?>
                                        </td>
                                        <td style="word-break:break-all" width="100" align="right" title="<? echo $row[("total_rcv_amount")]."==".$row[("total_issue_amount")]; ?>"><? if(number_format($closingStock,2)>0) echo number_format($stockValue,2); else echo "0.00"; ?></td>
									<?
									}
									if($cbo_yes_no==1)
									{
									?>
									<td width="120" style="word-break:break-all"><p><? echo $store_name_arr[$row[("trans_store")]]; ?></p></td>
									<?
									}
									?>
									<td width="100" align="center" style="word-break:break-all"><? echo $days_doh[$row[('prod_id')]]['daysonhand']; //$daysOnHand; ?></td>
								</tr>
								<?
								$total_openingBalance+=$openingBalance;
								$total_stockValue+=$stockValue;
								$total_purchase+=$row[("purchase")];
								$total_issue_return+=$row[("issue_return")];
								$total_transfer_in+=$row[("transfer_in")];
								$total_loan_rcv+=$row[("loan_rcv")];
								$total_receive_qnty+=$totalReceive;
								$total_issue_qnty+=$row[("issue")];
								$total_loan_iss+=$row[("loan_iss")];
								$total_receive_return_qnty+=$row[("receive_return")];
								$total_transfer_out+=$row[("transfer_out")];
								$total_issue_recv_return_qnty+=$totalIssue;
								$total_closing_stock+=$closingStock;
								$i++;
							//}
						}
					}
					?>
					<tr bgcolor="#CCCCCC" style="font-weight:bold">
						<td colspan="11" align="right" style="word-break:break-all"><strong>Grand Total</strong></td>
						<td  align="right" style="word-break:break-all" ><? echo number_format($total_openingBalance,2); ?></td>
						<td align="right" style="word-break:break-all"><? echo number_format($total_purchase,2); ?></td>
						<td align="right" style="word-break:break-all"><? echo number_format($total_issue_return,2); ?></td>
						<td align="right" style="word-break:break-all"><? echo number_format($total_transfer_in,2); ?></td>
                        <td align="right" style="word-break:break-all"><? echo number_format($total_loan_rcv,2); ?></td>
						<td align="right" style="word-break:break-all"><? echo number_format($total_receive_qnty,2); ?></td>
						<td align="right" style="word-break:break-all"><? echo number_format($total_issue_qnty,2); ?></td>
                        <td align="right" style="word-break:break-all"><? echo number_format($total_loan_iss,2); ?></td>
						<td align="right" style="word-break:break-all"><? echo number_format($total_receive_return_qnty,2); ?></td>
						<td align="right" style="word-break:break-all"><? echo number_format($total_transfer_out,2); ?></td>
						<td  align="right" style="word-break:break-all"><? echo number_format($total_issue_recv_return_qnty,2); ?></td>
						<td  align="right" style="word-break:break-all"><? echo number_format($total_closing_stock,2); ?></td>
                        <?
						if($variable_string_inventory!=1)
						{
							?>
                            <td>&nbsp;</td>
                            <td  align="right" style="word-break:break-all"><? echo number_format($total_stockValue,2); ?></td>
                            <?
						}
						if($cbo_yes_no==1)
						{
							?>
							<td>&nbsp;</td>
							<?
						}
						?>
						<td>&nbsp;</td>
					</tr>
				</table>
			</div>
		</div>
        <?
    }
	else if($report_type == 12)
	{
		if($cbo_yes_no==1)
		{
			if($variable_string_inventory!=1)
			{
				$div_width="1400";
				$table_width="1400";
			}
			else
			{
				$div_width="1500";
				$table_width="1500";
			}
		}
		else
		{
			if($variable_string_inventory!=1)
			{
				$div_width="1580";
				$table_width="1550";
			}
			else
			{
				$div_width="1380";
				$table_width="1350";
			}
		}
		//$cbo_company_name
		$loanRcvData = return_library_array("select id from inv_receive_master where status_active=1 and is_deleted=0 and company_id=$cbo_company_name and entry_form=20 and receive_purpose=1","id","id");
		$loanIssueData = return_library_array("select id from inv_issue_master where status_active=1 and is_deleted=0 and company_id=$cbo_company_name and entry_form=21 and issue_purpose=5","id","id");
		$form_name = "item_creation";

		// $nameArray=sql_select( "select id, image_location, master_tble_id from common_photo_library where is_deleted=0");

		$item_cate_credential_cond="".implode(",",array_diff(array_flip($general_item_category), array("4")))."";
		if($variable_store_wise_rate==1)
		{
			$sql="SELECT a.prod_id , a.store_id, b.id, b.item_number, b.item_code,b.item_number,b.model, b.item_category_id, b.item_group_id, b.avg_rate_per_unit, b.item_sub_group_id, b.sub_group_name, b.item_description, b.re_order_label, b.item_size, b.unit_of_measure, a.transaction_type, a.transaction_date, b.re_order_label, a.mst_id,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.cons_quantity else 0 end) as opening_total_receive,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.store_amount else 0 end) as opening_total_receive_amt,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.cons_quantity else 0 end) as opening_total_issue,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.store_amount else 0 end) as opening_total_issue_amt,
			(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (1,4,5)  then a.store_amount else 0 end) as total_rcv_amount,
			(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (2,3,6)  then a.store_amount else 0 end) as total_issue_amount,
			(case when a.transaction_type=1 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as purchase,
			(case when a.transaction_type=3 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as receive_return,
			(case when a.transaction_type=2 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue,
			(case when a.transaction_type=4 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue_return,
			(case when a.transaction_type=5 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_in,
			(case when a.transaction_type=6 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_out,
			(case when a.transaction_type in (1,4,5) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.store_amount else 0 end) as total_rcv_amt_value,
			(case when a.transaction_type in (2,3,6) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.store_amount else 0 end) as total_iss_amt_value
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.transaction_type in (1,2,3,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category  in($item_cate_credential_cond) and b.entry_form<>24  $str_cond $zero_cond
			order by b.item_category_id, b.item_group_id, a.prod_id";
		}
		else
		{
			$sql="SELECT a.prod_id , a.store_id, b.id, b.item_number, b.item_code,b.item_number,b.model, b.item_category_id, b.item_group_id, b.avg_rate_per_unit, b.item_sub_group_id, b.sub_group_name, b.item_description, b.re_order_label, b.item_size, b.unit_of_measure, a.transaction_type, a.transaction_date, b.re_order_label, a.mst_id,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.cons_quantity else 0 end) as opening_total_receive,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (1,4,5)  then a.cons_amount else 0 end) as opening_total_receive_amt,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.cons_quantity else 0 end) as opening_total_issue,
			(case when a.transaction_date<'".$select_from_date."' and a.transaction_type in (2,3,6)  then a.cons_amount else 0 end) as opening_total_issue_amt,
			(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (1,4,5)  then a.cons_amount else 0 end) as total_rcv_amount,
			(case when a.transaction_date<'".$select_to_date."' and a.transaction_type in (2,3,6)  then a.cons_amount else 0 end) as total_issue_amount,
			(case when a.transaction_type=1 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as purchase,
			(case when a.transaction_type=3 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as receive_return,
			(case when a.transaction_type=2 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue,
			(case when a.transaction_type=4 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue_return,
			(case when a.transaction_type=5 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_in,
			(case when a.transaction_type=6 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as transfer_out,
			(case when a.transaction_type in (1,4,5) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_amount else 0 end) as total_rcv_amt_value,
			(case when a.transaction_type in (2,3,6) and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_amount else 0 end) as total_iss_amt_value
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.transaction_type in (1,2,3,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category  in($item_cate_credential_cond) and b.entry_form<>24  $str_cond $zero_cond
			order by b.item_category_id, b.item_group_id, a.prod_id";
		}

		//echo  $sql;die;
		//and a.prod_id in(4065,6578)
		$result = sql_select($sql);
		$details_data=array();
		$prod_wise_trans_date=array();
		foreach($result as $row)
		{
			if($cbo_store_name>0)
			{
				if($cbo_store_name==$row[csf("store_id")])
				{
					$details_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
					$details_data[$row[csf("prod_id")]]["id"]=$row[csf("id")];
					$details_data[$row[csf("prod_id")]]["trans_store"]=$row[csf("store_id")];
					$details_data[$row[csf("prod_id")]]["item_number"]=$row[csf("item_number")];
					$details_data[$row[csf("prod_id")]]["item_code"]=$row[csf("item_code")];
					$details_data[$row[csf("prod_id")]]["item_category_id"]=$row[csf("item_category_id")];
					$details_data[$row[csf("prod_id")]]["item_group_id"]=$row[csf("item_group_id")];
					$details_data[$row[csf("prod_id")]]["avg_rate_per_unit"]=$row[csf("avg_rate_per_unit")];
					//$details_data[$row[csf("prod_id")]]["sub_group_name"]=$row[csf("sub_group_name")];
					$details_data[$row[csf("prod_id")]]["sub_group_name"]=$item_subgroupArr[$row[csf("item_sub_group_id")]];
					$details_data[$row[csf("prod_id")]]["item_description"]=$row[csf("item_description")];
					$details_data[$row[csf("prod_id")]]["re_order_label"]=$row[csf("re_order_label")];
					$details_data[$row[csf("prod_id")]]["item_size"]=$row[csf("item_size")];
					$details_data[$row[csf("prod_id")]]["model"]=$row[csf("model")];
					$details_data[$row[csf("prod_id")]]["unit_of_measure"]=$row[csf("unit_of_measure")];
					$details_data[$row[csf("prod_id")]]["re_order_label"]=$row[csf("re_order_label")];

					$details_data[$row[csf("prod_id")]]["opening_total_receive"]+=$row[csf("opening_total_receive")];
					$details_data[$row[csf("prod_id")]]["opening_total_receive_amt"]+=$row[csf("opening_total_receive_amt")];
					$details_data[$row[csf("prod_id")]]["opening_total_issue"]+=$row[csf("opening_total_issue")];
					$details_data[$row[csf("prod_id")]]["opening_total_issue_amt"]+=$row[csf("opening_total_issue_amt")];
					$details_data[$row[csf("prod_id")]]["total_rcv_amount"]+=$row[csf("total_rcv_amount")];
					$details_data[$row[csf("prod_id")]]["total_issue_amount"]+=$row[csf("total_issue_amount")];
					if($loanRcvData[$row[csf("mst_id")]])
					{
						$details_data[$row[csf("prod_id")]]["loan_rcv"]+=$row[csf("purchase")];
					}
					else
					{
						$details_data[$row[csf("prod_id")]]["purchase"]+=$row[csf("purchase")];
					}
					$details_data[$row[csf("prod_id")]]["receive_return"]+=$row[csf("receive_return")];
					if($loanIssueData[$row[csf("mst_id")]])
					{
						$details_data[$row[csf("prod_id")]]["loan_iss"]+=$row[csf("issue")];
					}
					else
					{
						$details_data[$row[csf("prod_id")]]["issue"]+=$row[csf("issue")];
					}
					$details_data[$row[csf("prod_id")]]["issue_return"]+=$row[csf("issue_return")];
					$details_data[$row[csf("prod_id")]]["transfer_in"]+=$row[csf("transfer_in")];
					$details_data[$row[csf("prod_id")]]["transfer_out"]+=$row[csf("transfer_out")];
					$details_data[$row[csf("prod_id")]]["total_rcv_amt_value"]+=$row[csf("total_rcv_amt_value")];
					$details_data[$row[csf("prod_id")]]["total_iss_amt_value"]+=$row[csf("total_iss_amt_value")];
				}

				if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5)
				{
					$item_wise_stock[$row[csf("prod_id")]]["bal_qnty"]+=($row[csf("opening_total_receive")]+$row[csf("purchase")]+$row[csf("issue_return")]+$row[csf("transfer_in")]);
					$item_wise_stock[$row[csf("prod_id")]]["bal_amt"]+=($row[csf("opening_total_receive_amt")]+$row[csf("total_rcv_amt_value")]);
					$item_wise_stock[$row[csf("prod_id")]]["bal_qnty_opeinng"]+=$row[csf("opening_total_receive")];
					$item_wise_stock[$row[csf("prod_id")]]["bal_amt_opening"]+=$row[csf("opening_total_receive_amt")];
				}
				else
				{
					$item_wise_stock[$row[csf("prod_id")]]["bal_qnty"]-=($row[csf("opening_total_issue")]+$row[csf("issue")]+$row[csf("receive_return")]+$row[csf("transfer_out")]);
					$item_wise_stock[$row[csf("prod_id")]]["bal_amt"]-=($row[csf("opening_total_issue_amt")]+$row[csf("total_iss_amt_value")]);
					$item_wise_stock[$row[csf("prod_id")]]["bal_qnty_opeinng"]-=$row[csf("opening_total_issue")];
					$item_wise_stock[$row[csf("prod_id")]]["bal_amt_opening"]-=$row[csf("opening_total_issue_amt")];
				}

			}
			else
			{
				$details_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
				$details_data[$row[csf("prod_id")]]["id"]=$row[csf("id")];
				$details_data[$row[csf("prod_id")]]["trans_store"]=$row[csf("store_id")];
				$details_data[$row[csf("prod_id")]]["item_number"]=$row[csf("item_number")];
				$details_data[$row[csf("prod_id")]]["item_code"]=$row[csf("item_code")];
				$details_data[$row[csf("prod_id")]]["item_category_id"]=$row[csf("item_category_id")];
				$details_data[$row[csf("prod_id")]]["item_group_id"]=$row[csf("item_group_id")];
				$details_data[$row[csf("prod_id")]]["avg_rate_per_unit"]=$row[csf("avg_rate_per_unit")];
				//$details_data[$row[csf("prod_id")]]["sub_group_name"]=$row[csf("sub_group_name")];
				$details_data[$row[csf("prod_id")]]["sub_group_name"]=$item_subgroupArr[$row[csf("item_sub_group_id")]];
				$details_data[$row[csf("prod_id")]]["item_description"]=$row[csf("item_description")];
				$details_data[$row[csf("prod_id")]]["re_order_label"]=$row[csf("re_order_label")];
				$details_data[$row[csf("prod_id")]]["item_size"]=$row[csf("item_size")];
				$details_data[$row[csf("prod_id")]]["model"]=$row[csf("model")];
				$details_data[$row[csf("prod_id")]]["unit_of_measure"]=$row[csf("unit_of_measure")];
				$details_data[$row[csf("prod_id")]]["re_order_label"]=$row[csf("re_order_label")];

				$details_data[$row[csf("prod_id")]]["opening_total_receive"]+=$row[csf("opening_total_receive")];
				$details_data[$row[csf("prod_id")]]["opening_total_receive_amt"]+=$row[csf("opening_total_receive_amt")];
				$details_data[$row[csf("prod_id")]]["opening_total_issue"]+=$row[csf("opening_total_issue")];
				$details_data[$row[csf("prod_id")]]["opening_total_issue_amt"]+=$row[csf("opening_total_issue_amt")];
				$details_data[$row[csf("prod_id")]]["total_rcv_amount"]+=$row[csf("total_rcv_amount")];
				$details_data[$row[csf("prod_id")]]["total_issue_amount"]+=$row[csf("total_issue_amount")];
				if($loanRcvData[$row[csf("mst_id")]])
				{
					$details_data[$row[csf("prod_id")]]["loan_rcv"]+=$row[csf("purchase")];
				}
				else
				{
					$details_data[$row[csf("prod_id")]]["purchase"]+=$row[csf("purchase")];
				}
				$details_data[$row[csf("prod_id")]]["receive_return"]+=$row[csf("receive_return")];
				if($loanIssueData[$row[csf("mst_id")]])
				{
					$details_data[$row[csf("prod_id")]]["loan_iss"]+=$row[csf("issue")];
				}
				else
				{
					$details_data[$row[csf("prod_id")]]["issue"]+=$row[csf("issue")];
				}
				$details_data[$row[csf("prod_id")]]["issue_return"]+=$row[csf("issue_return")];
				$details_data[$row[csf("prod_id")]]["transfer_in"]+=$row[csf("transfer_in")];
				$details_data[$row[csf("prod_id")]]["transfer_out"]+=$row[csf("transfer_out")];
				$details_data[$row[csf("prod_id")]]["total_rcv_amt_value"]+=$row[csf("total_rcv_amt_value")];
				$details_data[$row[csf("prod_id")]]["total_iss_amt_value"]+=$row[csf("total_iss_amt_value")];
			}
			$prod_wise_trans_date[$row[csf("prod_id")]]["transaction_date"].=$row[csf("transaction_date")].',';

		}
		$i=1;
		?>

		<style>
     		.font_size{
				font-size: 23px;
			}
			.heder_siz{
				font-size: 25px;
			}
		</style>


		<!-- <? ob_start(); ?> -->
		<div>
			<table style="width:<? echo $table_width; ?>px" border="2" cellpadding="2" cellspacing="0" class="rpt_table" id="table_header_1" rules="all" align="left">
					<tr class="form_caption" style="border:none;">
						<td colspan="15" align="center" style="border:none;font-size:25px; font-weight:bold" ><? echo $report_title; ?></td>
					</tr>
					<tr style="border:none;">
						<td colspan="15" align="center" style="border:none; font-size:25px;font-weight:bold">
							Company Name : <? echo $companyArr[$cbo_company_name]; ?>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="15" align="center" style="border:none;font-size:25px; font-weight:bold">
							<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
						</td>
					</tr>
				</table>

				<div style="width:<? echo $div_width; ?>px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table style="width:<? echo $table_width; ?>px" border="2" cellpadding="2" cellspacing="0" class="rpt_table" align="left">
				<thead>
					<tr >
						<th colspan="16"></th>
					</tr>
					<tr bgcolor="#CCCCCC" style="font-weight:bold">
				    	<th style="font-size: 20px;"  width="40"><p> SL</p></th>
						<th style="font-size: 20px;" width="60"><p>Prod.ID</p></th>
						<th style="font-size: 20px;" width="100"><p>Item Number</p></th>
						<th style="font-size: 20px;" width="60"><p>Item Code</p></th>
						<th style="font-size: 20px;" width="110"><p>Item Category</p></th>
						<th style="font-size: 20px;" width="100"><p>Item Group</p></th>
						<th style="font-size: 20px;" width="100"><p>Item Sub-group</p></th>
						<th style="font-size: 20px;" width="180"><p>Item Description</p></th>
						<th style="font-size: 20px;" width="120"><p>Image</p></th>
						<th style="font-size: 20px;" width="60"><p>UoM</p></th>
						<th style="font-size: 20px;"  width="100"><p>Opening Stock</p></th>

						<th style="font-size: 20px;" width="100"><p>Closing Stock</p></th>
						<?
						if($variable_string_inventory!=1)
						{
							?>
							<th style="font-size: 20px;"  width="100"><p>Avg. Rate</p></th>
							<th style="font-size: 20px;"  width="100"><p>Stock Value</p></th>
							<?
						}
						?>
							<?
						if($cbo_yes_no==1)
						{
							?>
							<th style="font-size: 20px;" width="120"><p>Store Name</p></th>
							<?
						}
						?>
						<th style="font-size: 20px;" width="100"><p>DOH</p></th>
					</tr>
				</thead>
				<tbody>

					<?
					// echo "<pre>";print_r($details_data);die;
					foreach($details_data as $prod_id=>$row)
					{
						$stockValue=$closingStock=0;
						$min_transaction_date="";
						$transaction_date_arr=explode(',',rtrim($prod_wise_trans_date[$prod_id]["transaction_date"],','));
						$minnimum_trans_date=strtotime($transaction_date_arr[0]);
						foreach($transaction_date_arr as $trans_date)
						{
							$transaction_date=strtotime($trans_date);
							if ($transaction_date<$minnimum_trans_date) $min_transaction_date=$transaction_date;
							else $min_transaction_date=$minnimum_trans_date;
						}

						$ageOfDays = datediff("d", date('d-M-Y', $min_transaction_date), date("Y-m-d"));
						//echo rtrim($prod_wise_trans_date[$prod_id]["transaction_date"],',').'system';
						$openingBalance = $row[("opening_total_receive")]-$row[("opening_total_issue")];
						$totalReceive = $row[("purchase")]+$row[("issue_return")]+$row[("transfer_in")]+$row[("loan_rcv")];
						$totalIssue =$row[("issue")]+$row[("receive_return")]+$row[("transfer_out")]+$row[("loan_iss")];
						$closingStock=$totalReceive-$totalIssue+$openingBalance;

						$openingBalanceValue = $row[("opening_total_receive_amt")]-$row[("opening_total_issue_amt")];
						$totalReceiveValue = $row[("total_rcv_amt_value")];
						$totalIssueValue = $row[('total_iss_amt_value')];
						$stockValue=($openingBalanceValue + $totalReceiveValue) -$totalIssueValue;
						$prod_rate=$stockValue/$closingStock;
						$re_order_label = $row[('re_order_label')];
						$image_mst_id=$row["id"];

						// echo  "select image_location from common_photo_library where master_tble_id='$image_mst_id' and form_name='$form_name'";
						if($value_with ==1)
						{
							if(number_format($openingBalance,2) > 0.00 || number_format($closingStock,2) > 0.00 )
							{

								// if($closingStock <= $re_order_label){$bgcolor="red";}
								// elseif (number_format($closingStock,2,'.','') > 0 &&  number_format($closingStock,2,'.','')<11){ $bgcolor="#FFA500";}
		                        // else{
								// 	if($i%2==0){$bgcolor="#E9F3FF";
                                //     }else{
							    //  	$bgcolor="#FFFFFF";
							    // 	}
								// }
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td class="font_size" width="40" align="center" ><p><? echo $i; ?></p></td>
									<td class="font_size" style="word-break:break-all" width="60" align="center"><p> <? echo $row[("prod_id")]; ?></p></td>
									<td class="font_size" style="word-break:break-all" width="100" align="center"><p><? echo $row[("item_number")]; ?></p></td>
									<td class="font_size" style="word-break:break-all" width="60" align="center"><p><? echo $row[("item_code")]; ?></p></td>
									<td class="font_size" style="word-break:break-all" width="110"><p><? echo $item_category[$row[("item_category_id")]]; ?></p></td>
									<td class="font_size" style="word-break:break-all" width="100"><p><? echo $itemgroupArr[$row[("item_group_id")]]; ?></p></td>
									<td class="font_size" style="word-break:break-all" width="100"><? echo $row[("sub_group_name")]; ?></td>
									<td class="font_size" style="word-break:break-all" width="180"><p><? echo $row[("item_description")]; ?></p></td>
									<td width="120" align="center" class="font_size" style="font-size:12px">
										<?
										$image_location=return_field_value("image_location","common_photo_library","master_tble_id='$image_mst_id' and form_name='$form_name'","image_location");
										?>
										<div >
											<img src="<? echo '../../../'.$image_location; ?>" width="120" height="100" border="2" />
										</div>
									</td>
									<td class="font_size" style="word-break:break-all" width="60" align="center"><p><? echo $unit_of_measurement[$row[("unit_of_measure")]]; ?></p></td>
									<td class="font_size" style="word-break:break-all" width="100" align="right"><p><? echo  number_format($openingBalance,2);//$openingBalance ?></p></td>

									<td class="font_size" style="word-break:break-all" width="100" align="right"  title="<? echo $openingBalance;?>"><p><? echo number_format($closingStock,2); ?></p></td>
                                    <?
                                    if($variable_string_inventory!=1)
									{
										?>
                                        <td class="font_size" style="word-break:break-all" width="100" align="right" title="<? echo $openingBalanceValue;?>"><p>
										<?
										if($cbo_store_name==0)
										{
											if(number_format($closingStock,2)>0) echo number_format($prod_rate,4); else echo "0.00";
										}
										else
										{
											$prod_rate=$item_wise_stock[$prod_id][("bal_amt")]/$item_wise_stock[$prod_id][("bal_qnty")];
											$stockValue=$closingStock*$prod_rate;
											if(number_format($closingStock,2)>0) echo number_format($prod_rate,4); else echo "0.00";
										}

										?>
                                       </p></td>
                                        <td class="font_size" style="word-break:break-all" width="100" align="right" title="<? echo $row[("total_rcv_amount")]."==".$row[("total_issue_amount")]; ?>"><p><? echo number_format($stockValue,2); ?></p></td>
										<?
										$total_stockValue+=$stockValue;
									}
									if($cbo_yes_no==1)
									{
									?>
									<td width="120" class="font_size" style="word-break:break-all"><p><? echo $store_name_arr[$row[("trans_store")]]; ?></p></td>
									<?
									}
									?>
									<td align="center" width="100" class="font_size" style="word-break:break-all"><p><? echo $days_doh[$row[('prod_id')]]['daysonhand']; //$daysOnHand; ?></p></td>
								</tr>
								<?
								$total_openingBalance+=$openingBalance;
								$total_purchase+=$row[("purchase")];
								$total_issue_return+=$row[("issue_return")];
								$total_transfer_in+=$row[("transfer_in")];
								$total_loan_rcv+=$row[("loan_rcv")];
								$total_receive_qnty+=$totalReceive;
								$total_issue_qnty+=$row[("issue")];
								$total_loan_iss+=$row[("loan_iss")];
								$total_receive_return_qnty+=$row[("receive_return")];
								$total_transfer_out+=$row[("transfer_out")];
								$total_issue_recv_return_qnty+=$totalIssue;
								$total_closing_stock+=$closingStock;
								$i++;
							}
						}
						else
						{
							//if(number_format($closingStock,3)>0.000 || number_format($openingBalance,3)>0.000 || number_format($totalReceive,3)>0.000 || number_format($totalIssue,3)>0.000)
							//{
								//if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
								/*if( $row[("transaction_type")]==1 || $row[("transaction_type")]==2 || $row[("transaction_type")]==3 || $row[("transaction_type")]==4 )
								$stylecolor='style="color:#A61000"';
								else
								$stylecolor='style="color:#000000"';*/

								// if($closingStock <= $re_order_label){$bgcolor="red";}elseif($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40" align="center"><? echo $i; ?></td>
									<td style="word-break:break-all" width="60" align="center"><p><? echo $row[("prod_id")]; ?></p></td>
									<td style="word-break:break-all" width="100" align="center"><p><? echo $row[("item_number")]; ?></p></td>
									<td style="word-break:break-all" width="60" align="center"><p><? echo $row[("item_code")]; ?></p></td>
									<td style="word-break:break-all" width="110"><p><? echo $item_category[$row[("item_category_id")]]; ?></p></td>
									<td style="word-break:break-all" width="100"><p><? echo $itemgroupArr[$row[("item_group_id")]]; ?></p></td>
									<td style="word-break:break-all" width="100"><p><? echo $row[("sub_group_name")]; ?></p></td>
									<td style="word-break:break-all" width="180"><p><? echo $row[("item_description")]; ?></p></td>

									<td width="120" align="center"  style="font-size:12px">
										<?
										$image_location=return_field_value("image_location","common_photo_library","master_tble_id='$image_mst_id' and form_name='$form_name'","image_location");
										?>
										<div >
											<img src="<? echo '../../../'.$image_location; ?>" width="120" height="100" border="2" />
										</div>
									</td>
									<td style="word-break:break-all" width="60" align="center"><p><? echo $unit_of_measurement[$row[("unit_of_measure")]]; ?></p></td>
									<td style="word-break:break-all" width="100" align="right"><? echo  number_format($openingBalance,2);//$openingBalance ?></td>

									<td style="word-break:break-all" width="100" align="right"  title="<? echo $openingBalance;?>"><? echo number_format($closingStock,2); ?></td>
                                    <?
                                    if($variable_string_inventory!=1)
									{
										?>
                                        <td style="word-break:break-all" width="100" align="right" title="<? echo $openingBalanceValue;?>">
                                        <?
										if($cbo_store_name==0)
										{
											if(number_format($closingStock,2)>0) echo number_format($prod_rate,4); else echo "0.00";
										}
										else
										{
											$prod_rate=$item_wise_stock[$prod_id][("bal_amt")]/$item_wise_stock[$prod_id][("bal_qnty")];
											$stockValue=$closingStock*$prod_rate;
											if(number_format($closingStock,2)>0) echo number_format($prod_rate,4); else echo "0.00";
										}
										?>
                                        </td>
                                        <td style="word-break:break-all" width="100" align="right" title="<? echo $row[("total_rcv_amount")]."==".$row[("total_issue_amount")]; ?>"><? echo number_format($stockValue,2); ?></td>
										<?
										$total_stockValue+=$stockValue;
									}
									if($cbo_yes_no==1)
									{
									?>
									<td width="120" style="word-break:break-all"><p><? echo $store_name_arr[$row[("trans_store")]]; ?></p></td>
									<?
									}
									?>
									<td width="100" align="center" style="word-break:break-all"><? echo $ageOfDays; ?></td>
									<td width="100" align="center" style="word-break:break-all"><? echo $days_doh[$row[('prod_id')]]['daysonhand']; //$daysOnHand; ?></td>
								</tr>
								<?
								$total_openingBalance+=$openingBalance;
								$total_purchase+=$row[("purchase")];
								$total_issue_return+=$row[("issue_return")];
								$total_transfer_in+=$row[("transfer_in")];
								$total_loan_rcv+=$row[("loan_rcv")];
								$total_receive_qnty+=$totalReceive;
								$total_issue_qnty+=$row[("issue")];
								$total_loan_iss+=$row[("loan_iss")];
								$total_receive_return_qnty+=$row[("receive_return")];
								$total_transfer_out+=$row[("transfer_out")];
								$total_issue_recv_return_qnty+=$totalIssue;
								$total_closing_stock+=$closingStock;
								$i++;
							//}
						}
					}
					?>
					</tbody>
					<tfoot>
					<tr bgcolor="#CCCCCC" style="font-weight:bold">
						<td colspan="10" align="right" style="word-break:break-all"><strong>Grand Total</strong></td>
						<td  align="right" style="word-break:break-all" ><strong><? echo number_format($total_openingBalance,2); ?></strong></td>
						<td  align="right" style="word-break:break-all"><strong><? echo number_format($total_closing_stock,2); ?></strong></td>
                        <?
						if($variable_string_inventory!=1)
						{
							?>
                            <td>&nbsp;</td>
                            <td  align="right" style="word-break:break-all"><strong><? echo number_format($total_stockValue,2); ?></strong></td>
                            <?
						}
						if($cbo_yes_no==1)
						{
							?>
							<td>&nbsp;</td>
							<?
						}
						?>
						<td>&nbsp;</td>
					</tr>
					</tfoot>
				</table>
			</div>
		</div>
        <?
    }
    // $html = ob_get_contents();
    // ob_clean();
    // foreach (glob("$user_id*.xls") as $filename) {
    // @unlink($filename);
    // }
    // //---------end------------//
    // $name=time();
    // $filename=$user_id."_".$name.".xls";
    // $create_new_doc = fopen($filename, 'w');
    // $is_created = fwrite($create_new_doc, $html);
    // echo "$html**$filename**$report_type";
    exit();
}

?>
