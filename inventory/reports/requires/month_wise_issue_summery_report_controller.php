<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

require_once('../../../includes/class3/class.conditions.php');
require_once('../../../includes/class3/class.reports.php');
require_once('../../../includes/class3/class.yarns.php');


$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
	$permission=$_SESSION['page_permission'];

	$data=$_REQUEST['data'];
	$action=$_REQUEST['action'];

	$select_year="to_char";
    $year_con=",'YYYY'";


if($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	if($data[1]==1) $party="1,3,21,90"; else $party="80";
	echo create_drop_down( "cbo_buyer_id", 80, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}
if($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 80, "select id, location_name from lib_location where status_active =1 and is_deleted=0 and company_id=$data order by location_name","id,location_name", 1, "--Select Location--", $selected, "","" );
	exit();
}

    if ($action=="item_account_popup")
	{
		echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
		extract($_REQUEST);
		$data=explode('_',$data);
		//print_r ($data);  
		?>	
	    <script>
		 var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
		 
		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
			
		function js_set_value(id)
		{
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];
		
			if( jQuery.inArray(  str , selected_id ) == -1 ) {
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#item_account_id').val( id );
			$('#item_account_val').val( ddd );
		} 
		</script>
	     <input type="hidden" id="item_account_id" />
	     <input type="hidden" id="item_account_val" />
	 	<?
			$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
			$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
			if ($data[2]==0) $item_name =""; else $item_name =" and item_group_id in($data[2])";
			
			$sql="SELECT id,item_account,item_category_id,item_group_id,item_description,supplier_id from  product_details_master where company_id in($data[0]) and item_category_id in($data[1]) $item_name and  status_active=1 and is_deleted=0"; 
			$arr=array(1=>$item_category,2=>$itemgroupArr,4=>$supplierArr);
			echo  create_list_view("list_view", "Item Account,Item Category,Item Group,Item Description,Supplier,Product ID", "70,110,150,150,100,70","780","400",0, $sql , "js_set_value", "id,item_description", "", 0, "0,item_category_id,item_group_id,0,supplier_id,0", $arr , "item_account,item_category_id,item_group_id,item_description,supplier_id,id", "",'setFilterGrid("list_view",-1);','0,0,0,0,0,0','',1) ;
			exit();
	}


if($action == "item_group_popup")
{
	echo load_html_head_contents("Composition Info","../../../", 1, 1, '','','');
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

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function set_all()
		{
			var old=document.getElementById('selected_ids').value;
			if(old!="")
			{
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{
					js_set_value( old[k] )
				}
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

			$('#selected_id').val(id);
			$('#selected_name').val(name);
		}
	</script>
</head>
<fieldset style="width:390px">
	<legend>Item Details</legend>
	<input type="hidden" name="selected_name" id="selected_name" value="">
	<input type="hidden" name="selected_id" id="selected_id" value="">
	<table width="390" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
		<thead>
			<tr>
				<th width="30">SL</th>
                <th width="150">Item Category</th>
                <th width="">Item Group Name</th>
			</tr>
		</thead>
	</table>
	<div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
		<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?
			$i = 1;
			$cbo_item_group=str_replace("'","",$cbo_item_group);
			$cbo_year_selection=str_replace("'","",$cbo_year_selection);

			if($db_type==0) { $year_cond=" and YEAR(insert_date)=$cbo_year_selection";   }
			if($db_type==2) {$year_cond=" and to_char(insert_date,'YYYY')=$cbo_year_selection";}
			
			$sql="select id, item_category, item_name from lib_item_group where status_active=1 and item_category in(5,6,7,23) and is_deleted=0";
			// echo $sql;
			$result=sql_select($sql);
			$selected_id_arr=explode(",",$cbo_item_group);
			foreach ($result as $row)
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				if(in_array($row[csf("id")],$selected_id_arr))
				{
					if($selected_ids=="") $selected_ids=$i; else $selected_ids.=",".$i;
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
					<td width="30" align="center">
						<? echo $i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
						<input type="hidden" name="txt_individual" id="txt_individual<? echo $i ?>" value="<? echo $row[csf("item_name")]; ?>"/>
					</td>
                    <td width="150"><p><? echo $item_category[$row[csf("item_category")]]; ?></p></td>
                    <td width=""><p><? echo $row[csf("item_name")]; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
			<input type="hidden" name="selected_ids" id="selected_ids" value="<? echo $selected_ids; ?>"/>
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
	set_all();
</script>
<?
}



if($action=="report_generate")
{

	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_id);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$cbo_item_cat=str_replace("'","",$cbo_item_cat);
	$cbo_item_group=str_replace("'","",$cbo_item_group);
	$cbo_issue_purpose=str_replace("'","",$cbo_issue_purpose);
	$cbo_uom=str_replace("'","",$cbo_uom);
	$cbo_year_name=str_replace("'","",$cbo_year_name); 
	$cbo_month=str_replace("'","",$cbo_month);
	$cbo_month_end=str_replace("'","",$cbo_month_end);
	$cbo_end_year_name=str_replace("'","",$cbo_end_year_name);
	$cbo_dyed_type=str_replace("'","",$cbo_dyed_type);
	$cbo_yarn_type=str_replace("'","",$cbo_yarn_type); 
	$type=str_replace("'","",$rptType);
	//echo $type; die;

	$company_library=return_library_array("select id, company_name from lib_company", "id", "company_name");
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');

	$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $cbo_month_end, $cbo_year_name);
	$s_date=$cbo_year_name."-".$cbo_month."-"."01";
	$e_date=$cbo_end_year_name."-".$cbo_month_end."-".$daysinmonth;
	if($db_type==2)
	{
		$s_date=change_date_format($s_date,'yyyy-mm-dd','-',1);
		$e_date=change_date_format($e_date,'yyyy-mm-dd','-',1);
	}

	$tot_month = datediff( 'm', $s_date,$e_date);
	for($i=0; $i<= $tot_month; $i++ )
	{
		$next_month=month_add($s_date,$i);
		$month_arr[]=date("Y-m",strtotime($next_month));
	}


	if ($cbo_item_group >0)
	{
		$search_cond .= " and b.item_group_id in($cbo_item_group)";
	}
	if ($cbo_store_name >0)
	{
		$search_cond .= " and b.store_id in($cbo_store_name)";
	}
	if ($cbo_issue_purpose >0)
	{
		$search_cond .= " and c.issue_purpose in($cbo_issue_purpose)";
	}
	if ($cbo_uom >0)
	{
		$search_cond .= " and b.unit_of_measure in($cbo_uom)";
	}
	if ($cbo_item_cat > 0)
	{
		$search_cond .= " and a.item_category in ($cbo_item_cat)";
	}
	
	if ($cbo_company_name > 0)
	{
		$search_cond .= " and a.company_id in($cbo_company_name)";
	}
	
	$sql="SELECT b.id as prod_id,a.transaction_date,
	case when a.transaction_type in(2) then a.cons_quantity else 0 end as issue_qty ,b.yarn_type,a.cons_amount,a.company_id,a.item_category,b.item_group_id,b.unit_of_measure,b.item_description from
	inv_transaction a, product_details_master b, inv_issue_master c
	where  a.prod_id=b.id  $search_cond  and a.transaction_type in(2) and c.id=a.mst_id and  c.entry_form=298 and a.status_active=1 and a.is_deleted=0 and a.transaction_date between '$s_date' and '$e_date'";

	$cpAvgRateArray=sql_select($sql);

	$typeWiseYarnRecQtyArr=array(); $month_wish_issue_arr=array(); $month_wish_issueQtyArr=array();
	foreach( $cpAvgRateArray as $row)
	{
		$date_key=date("Y-m",strtotime($row[csf("transaction_date")]));
		$month_wish_issueQtyArr[$row[csf("item_description")]][$row[csf("unit_of_measure")]][$row[csf("item_group_id")]][$row[csf("company_id")]][$row[csf("item_category")]][$date_key] += $row[csf("issue_qty")];
		
		$month_wish_issue_arr[$row[csf("item_description")]][$row[csf("unit_of_measure")]][$row[csf("item_group_id")]][$row[csf("company_id")]][$row[csf("item_category")]]["company_id"] = $row[csf("company_id")];
		$month_wish_issue_arr[$row[csf("item_description")]][$row[csf("unit_of_measure")]][$row[csf("item_group_id")]][$row[csf("company_id")]][$row[csf("item_category")]]["item_category"] = $row[csf("item_category")];
		$month_wish_issue_arr[$row[csf("item_description")]][$row[csf("unit_of_measure")]][$row[csf("item_group_id")]][$row[csf("company_id")]][$row[csf("item_category")]]["item_group_id"] = $row[csf("item_group_id")];
		$month_wish_issue_arr[$row[csf("item_description")]][$row[csf("unit_of_measure")]][$row[csf("item_group_id")]][$row[csf("company_id")]][$row[csf("item_category")]]["item_description"] = $row[csf("item_description")];
		$month_wish_issue_arr[$row[csf("item_description")]][$row[csf("unit_of_measure")]][$row[csf("item_group_id")]][$row[csf("company_id")]][$row[csf("item_category")]]["unit_of_measure"] = $row[csf("unit_of_measure")];

		$month_wish_issueQtyArr[$date_key] += $row[csf("issue_qty")];
	}
	// print_r($month_wish_issue_arr);
	//var_dump($typeWiseYarnRecQtyArr);

	ob_start();
	$width=($tot_month*75)+($tot_month+895);
	$bgcolor1="#FFFFFF";
	$bgcolor2="#E9F3FF";
	?>    
	<div style="width:<? echo $width;?>px; margin:10px 0; height:auto;">
  
	  
		<table align="right" cellspacing="0" width="<? echo $width;?>"  border="3" rules="all" class="rpt_table" id="tbl_month_pce" >
			<thead>
				<tr style="font-size: 20;">
					<th width="30">Sl</th>
					<th width="120">Company Name</th>
					<th width="120">Item Category</th>
					<th width="120">Group</th>
					<th width="120">Item  name</th>
					<th width="80">Uom</th>
					<? foreach($month_arr as $month_id):?>
					<th width="75"><? list($y,$m)=explode('-',$month_id); $m=$m*1; echo $months[$m].'-'.$y; ?></th>
					<? endforeach; ?>
					<th width="100">Total</th>
				</tr>
			</thead>
			<tbody>
				<?
				$p=1;  
				$i=1;

			foreach($month_wish_issue_arr as $company_id=>$item_category_arr)
			{
				foreach($item_category_arr as $item_category_id=>$item_group_arr)
				{ $data_tot_arr=array();
					foreach($item_group_arr as $item_group_id=>$item_description_arr)
					{
						foreach($item_description_arr as $item_description_id=>$unit_of_measure_arr)
						{
							foreach($unit_of_measure_arr as $uom_id=>$row)
							{
								?>
								<tr onclick="change_color('tr1st_<? echo $p;?>','<? echo $bgcolor1; ?>')" style="font-size: 18;" id="tr1st_<? echo $p;$p++;?>">
								<td><? echo $i;?></td>
								<td align="center"><? echo $company_library[$row["company_id"]];?></td>
								<td align="center"><? echo $item_category[$row["item_category"]];?></td>
								<td align="center"><? echo $trim_group_library[$row["item_group_id"]];?></td>
								<td align="left"> <? echo $row["item_description"];?></td>
								<td align="center"><? echo $unit_of_measurement[$row["unit_of_measure"]];?></td>
								<? foreach($month_arr as $month_id){?>
								<td align="right"><? echo  $month_wish_issueQtyArr[$company_id][$item_category_id][$item_group_id][$item_description_id][$uom_id][$month_id]; ?></td>
								
								<?
								$data_tot_arr[$month_id]+=$month_wish_issueQtyArr[$company_id][$item_category_id][$item_group_id][$item_description_id][$uom_id][$month_id]; 
								} ?> 
								<td align="right"><? echo number_format(array_sum($month_wish_issueQtyArr[$company_id][$item_category_id][$item_group_id][$item_description_id][$uom_id]),0);?></td>
								</tr>
								<? 
								$i++;
							}  
						}
						
					}
					?>
						<tr bgcolor="<? echo $bgcolor ; ?>">
							<th colspan="5" align="right">Item and Uom Total:</th>
							<th  align="right"></th>
							<? foreach($month_arr as $month_id):?>
							<th align="right"><? echo $data_tot_arr[$month_id]; ?></th>
							<? endforeach;?> 
							<th align="right"><? echo array_sum($data_tot_arr);?></th>
						</tr>
					<?
				}
			}
				?> 

			</tbody>
			<tfoot>
					<tr bgcolor="<? echo $bgcolor ; ?>">
						<th colspan="6" align="left">Total:</th>
						<? foreach($month_arr as $month_id):?>
						<th align="right"><? echo $month_wish_issueQtyArr[$month_id]; ?></th>
						<? endforeach;?> 
						<th align="right"><? echo array_sum($month_wish_issueQtyArr);?></th>
					</tr>
			</tfoot>
		</table>
	</div>
     <?
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename) {
				
		@unlink($filename);
	}	
	$name = time();
	$filename = $user_id . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html####$filename";
	exit();	
}

?>