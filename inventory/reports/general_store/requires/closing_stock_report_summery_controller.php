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


if ($action=="load_drop_down_store")
{
	$data=explode("**",$data);
	$cat_cond = ($data[1]) ? " and b.CATEGORY_TYPE in($data[1])" : "" ;
	$userCredential = sql_select("SELECT store_location_id, item_cate_id FROM user_passwd where id=$user_id");
	$store_cond = ($userCredential[0][csf("store_location_id")]) ? " and a.id in (".$userCredential[0][csf("store_location_id")].")" : "" ;
	echo create_drop_down( "cbo_store_name", 150, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id in($data[0]) and b.CATEGORY_TYPE in(".implode(",",array_flip($general_item_category)).") $cat_cond $store_cond group by a.id,a.store_name","id,store_name", 1, "--Select Store--", 0, "",$disable );
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



if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$item_group_id=str_replace("'","",$item_group_id);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
    $report_type=str_replace("'","",$report_type);
	$selected_cat_arr=explode(",",$cbo_item_category_id);
	//echo "<pre>";print_r($selected_cat_arr);die;
	if($db_type==0)
	{
		$select_from_date=change_date_format($from_date,'yyyy-mm-dd');
		$select_to_date=change_date_format($to_date,'yyyy-mm-dd');
	}
	else
	{
		$select_from_date=change_date_format($from_date,'','',1);
		$select_to_date=change_date_format($to_date,'','',1);
	}
	
	$search_day_diff=datediff('d',$select_from_date,$select_to_date);
	//echo $search_day_diff;die;
	$search_month=array();
	$p=0;
	for($i=0; $i<$search_day_diff;$i++)
	{
		if($i==0) $search_month_year=date('m',strtotime($select_from_date))."-".date('Y',strtotime($select_from_date));
		else $search_month_year=date('m',strtotime(add_date($select_from_date, $i)))."-".date('Y',strtotime(add_date($select_from_date, $i)));
		$month_val_ref=explode("-",$search_month_year);
		$max_days_month=change_date_format((cal_days_in_month(CAL_GREGORIAN,$month_val_ref[0],$month_val_ref[1])."-".$search_month_year),'','',1);
		
		$search_month[$max_days_month]=$search_month_year;
		$p++;
	}
	
	//echo "<pre>";print_r($search_month);die;
	
	//echo $cbo_item_category_id."__".$item_group_id."__".$item_account_id."__".$item_sub_group_id;die;

	$str_cond="";$zero_cond="";
	if ($cbo_company_name!=0) $str_cond =" and a.company_id in($cbo_company_name)";
	if ($cbo_item_category_id!="") $str_cond .=" and b.item_category_id in($cbo_item_category_id)";
	if ($item_group_id!="") $str_cond .=" and b.item_group_id in($item_group_id)";
	
	$store_cond="";
	if ($cbo_store_name!=0) $store_cond .=" and a.store_id='$cbo_store_name'";
	$item_cate_credential_cond="".implode(",",array_flip($general_item_category))."";
	
	$sql="";
	foreach($search_month as $mx_month_day=>$month_val)
	{
		$sql="Select B.ITEM_CATEGORY_ID, SUM((CASE WHEN a.TRANSACTION_TYPE IN(1,4,5) THEN a.CONS_AMOUNT ELSE 0 END)-(CASE WHEN a.TRANSACTION_TYPE IN(2,3,6) THEN a.CONS_AMOUNT ELSE 0 END)) AS BAL_AMT
		from inv_transaction a, product_details_master b
		where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category in($item_cate_credential_cond) and b.entry_form<>24 and a.transaction_date <= '".$mx_month_day."' $str_cond $store_cond $zero_cond
		group by B.ITEM_CATEGORY_ID";
		$result = sql_select($sql);
		foreach($result as $val)
		{
			$data_set[$month_val][$val["ITEM_CATEGORY_ID"]]=$val["BAL_AMT"];
		}
	}
	//echo '<pre>';print_r($data_set);die;
	//echo $sql;die;
	/*$sql="Select A.PROD_ID, B.ID, B.ITEM_CODE, B.ITEM_CATEGORY_ID, B.ITEM_GROUP_ID, B.AVG_RATE_PER_UNIT, B.SUB_GROUP_NAME, B.ITEM_DESCRIPTION, B.ITEM_SIZE, B.UNIT_OF_MEASURE, B.RE_ORDER_LABEL, A.TRANSACTION_TYPE, A.TRANSACTION_DATE, A.CONS_QUANTITY, A.CONS_AMOUNT
	from inv_transaction a, product_details_master b
	where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category in($item_cate_credential_cond) and b.entry_form<>24 and a.transaction_date < '".$select_to_date."' $str_cond $store_cond $zero_cond
	order by A.TRANSACTION_DATE";

	//echo $sql;//die;
	$result = sql_select($sql);
	$data_set=array();
	foreach($result as $val)
	{
		
		if($val["TRANSACTION_TYPE"]==1 || $val["TRANSACTION_TYPE"]==4 || $val["TRANSACTION_TYPE"]==5)
		{
			$data_set[date('m',strtotime($val["TRANSACTION_DATE"]))."-".date('y',strtotime($val["TRANSACTION_DATE"]))][$val["ITEM_CATEGORY_ID"]]+=$val["CONS_AMOUNT"];
		}
		else
		{
			$data_set[date('m',strtotime($val["TRANSACTION_DATE"]))."-".date('y',strtotime($val["TRANSACTION_DATE"]))][$val["ITEM_CATEGORY_ID"]]-=$val["CONS_AMOUNT"];
		}
	}*/
	
	//echo '<pre>';print_r($search_month);die;
	
	$div_width=120+(count($selected_cat_arr)*100);
	$table_width=100+(count($selected_cat_arr)*100);
	ob_start();
	?>
    <style>
		.wrd_brk{word-break: break-all;}
		.left{text-align: left;}
		.center{text-align: center;}
		.right{text-align: right;}
	</style>
	<fieldset style="width: <?= $div_width; ?>px">
		<table style="width:<?= $table_width; ?>px" border="1" cellpadding="0" cellspacing="0" rules="all">
			<tr>
				<td align="center" style="border:none; font-size:14px;">
					<p>Stock Value</p> 
				</td>
			</tr>
		</table>	
		<table style="width:<?= $table_width; ?>px" border="1" cellpadding="0" cellspacing="0" class="rpt_table" id="table_header_1" rules="all" align="left">		
			<thead>	
				<tr>
					<th width="100" class="wrd_brk left">Month</th>
                    <?
					foreach($selected_cat_arr as $cat_id)
					{
						?>
                        <th width="100" class="wrd_brk center"><?= $item_category[$cat_id];?></th>
                        <?
					}
					?>
				</tr>
			</thead>
            <tbody>
            	<?
				$sl=1;
				foreach($search_month as $month_id)
				{
					if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
                    	<td align="center" title="<?= $month_id;?>"><?  $month_year_ref=explode("-",$month_id); echo $months[$month_year_ref[0]*1]."-".$month_year_ref[1];?></td>
                        <?
						foreach($selected_cat_arr as $cat_id)
						{
							?>
							<td align="right"><? echo number_format($data_set[$month_id][$cat_id],2);?></td>
							<?
							$cat_total[$cat_id]+=$data_set[$month_id][$cat_id];
						}
						?>
                    </tr>
                    <?
					$sl++;
				}
				?>
            </tbody>
            <tfoot>
            	<tr>
                	<th>Total:</th>
                    <?
					foreach($selected_cat_arr as $cat_id)
					{
						?>
                        <th align="right"><? echo number_format($cat_total[$cat_id],2);?></th>
                        <?
					}
					?>
                </tr>
            </tfoot>				
		</table>
	</fieldset>	
	<?
	
	
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("$user_id*.xls") as $filename) {
    	@unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$report_type";
    exit();
}

?>
