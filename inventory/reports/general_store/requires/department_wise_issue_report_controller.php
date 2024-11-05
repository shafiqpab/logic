<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_store")
{
	$data=explode("**",$data);
	if($data[1]==2) $disable=1; else $disable=0;
	echo create_drop_down( "cbo_store_name", 100, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id='$data[0]' and  b.category_type in(8,9,10,11,15,16,17,18,19,20,21,22) group by a.id,a.store_name","id,store_name", 1, "--Select Store--", 0, "",$disable );
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 120, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' $company_location_credential_cond order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/department_wise_issue_report_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_floor','sewing_td');",0 );
	exit();
}

if ($action=="load_drop_down_department")
{
	//echo "select a.id,a.department_name from  lib_department a, lib_division b where b.id=a.division_id and a.status_active =1 and a.is_deleted=0 and b.company_id='$data' order by department_name";die;
	echo create_drop_down( "cbo_department", 120, "select a.id,a.department_name from  lib_department a, lib_division b where b.id=a.division_id and a.status_active =1 and a.is_deleted=0 and b.company_id='$data' order by department_name","id,department_name", 1, "-- Select --", $selected,"load_drop_down( 'requires/department_wise_issue_report_controller', this.value , 'load_drop_down_section', 'section_td' );",0 );
	exit();
}

if ($action=="load_drop_down_section")
{
	echo create_drop_down( "cbo_section", 90, "select id,section_name from lib_section where status_active =1 and is_deleted=0 and department_id='$data' order by section_name","id,section_name", 1, "-- Select --", $selected, "",0 );
	exit();
}


if ($action=="item_account_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);
	?>
    <script>
	 var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

	 function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			alert (tbl_row_count);
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
 	$group_cond = "";
 	if($data[2] !=""){$group_cond=" and item_group_id in($data[2])";}
	$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");

	$sql="SELECT id,item_account,item_category_id,item_group_id,item_description,supplier_id from  product_details_master where item_category_id in ($data[1]) $group_cond and status_active=1 and is_deleted=0";
	$arr=array(1=>$item_category,2=>$itemgroupArr,4=>$supplierArr);
	echo  create_list_view("list_view", "Item Account,Item Category,Item Group,Item Description,Supplier,Product ID", "70,110,150,150,100,70","780","400",0, $sql , "js_set_value", "id,item_description", "", 0, "0,item_category_id,item_group_id,0,supplier_id,0", $arr , "item_account,item_category_id,item_group_id,item_description,supplier_id,id", "",'setFilterGrid("list_view",-1);','0,0,0,0,0,0','',1) ;
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
	$sql="SELECT id,item_name from  lib_item_group where item_category in($cbo_item_category_id) and status_active=1 and is_deleted=0";
	//echo $sql; die;
	$arr=array();
	echo create_list_view("list_view", "Item Group","250","300","300",0, $sql , "js_set_value", "id,item_name", "", 1, "0", $arr, "item_name", "","setFilterGrid('list_view',-1)","0","",1);

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

if ($action=="load_drop_down_floor")
{
	$data_ref=explode("_",$data);
	$company_id=$data_ref[0];
	$location_id=$data_ref[1];

	echo create_drop_down( "cbo_sewing_floor_name", 90, "SELECT id,floor_name from lib_prod_floor where company_id=$company_id and production_process=5 and location_id=$location_id and status_active=1 and is_deleted=0 ","id,floor_name", 1, "-- Select Sewing Floor --", $selected, "load_drop_down( 'requires/department_wise_issue_report_controller',document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_location').value+'_'+this.value, 'load_drop_down_sewing_line','line_td');" );

	exit();
}

if($action=="load_drop_down_sewing_line")
{
	$explode_data = explode("_",$data);
	$company = $explode_data[0];
	$location = $explode_data[1];
	$floor = $explode_data[2];

	$line_library=return_library_array( "select id,line_name from lib_sewing_line where company_name='$company' and floor_name=$floor and location_name='$location' and status_active=1 and is_deleted=0 group by id, line_name", "id", "line_name"  );

	echo create_drop_down( "cbo_sewing_floor_line", 90,$line_library,"", 1, "--- Select Sewing Line ---", $selected, "",0,0 );
}

if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	//echo "select a.id,a.item_account,a.item_category_id,a.sub_group_name,a.item_description, a.product_name_details,a.item_size,a.unit_of_measure,a.company_id,a.status_active,b.item_name from product_details_master a, lib_item_group b where a.is_deleted=0 and b.is_deleted=0 and a.item_group_id=b.id and a.item_category_id=$cbo_item_category_id and a.company_id=$cbo_company_name";

	$sql=sql_select("select distinct a.id,a.item_account,a.item_category_id,a.sub_group_name,a.item_description, a.product_name_details,a.item_size,a.unit_of_measure,a.company_id,a.status_active,b.item_name from product_details_master a, lib_item_group b where a.is_deleted=0 and b.is_deleted=0 and a.item_group_id=b.id and a.item_category_id in ($cbo_item_category_id) and a.company_id=$cbo_company_name");
	$itemdes=array();
	foreach($sql as $row)
	{
		$itemdes[$row[csf('item_category_id')]].=$row[csf('product_name_details')]." ,";
	}

	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	if ($cbo_company_name==0) $company_id =""; else $company_id =" and a.company_id='$cbo_company_name'";
	if ($cbo_item_category_id==0) $item_category_id=""; else $item_category_id=" and b.item_category in ($cbo_item_category_id)";
	if ($item_account_id==0) $item_code=""; else $item_code=" and b.prod_id in ($item_account_id)";
	if ($item_group_id==0) $group_id=""; else $group_id=" and c.item_group_id in($item_group_id)";
	if ($cbo_department==0) $department_id=""; else $department_id=" and b.department_id='$cbo_department'";
	if ($cbo_section==0) $section_id=""; else $section_id=" and b.section_id='$cbo_section'";
	if ($cbo_store_name==0){ $store_id="";}else{$store_id=" and b.store_id=$cbo_store_name";}
	if ($cbo_location==0){ $location_id="";}else{$location_id=" and b.location_id=$cbo_location";}
	$reference_cond = "";
	if($cbo_search_by == 1 && trim(str_replace("'","",$txt_reference_id)) != "")
	{
		$reference_cond = " and a.req_no like '%".$txt_reference_id."%'";
	}
	else if($cbo_search_by == 2 && trim(str_replace("'","",$txt_reference_id)) != "")
	{
		$reference_cond = " and a.issue_number_prefix_num like  '%".$txt_reference_id."%'";
	}
	if ($cbo_location==0) $location_id=""; else $location_id=" and b.location_id=$cbo_location";
	if ($cbo_location==0) $location_id=""; else $location_id=" and b.location_id=$cbo_location";

	if($db_type==0)
	{
		if( $from_date==0 && $to_date==0 ) $transaction_date=""; else $transaction_date= " and b.transaction_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
 	}
	if($db_type==2)
	{
		if( $from_date==0 && $to_date==0 ) $transaction_date=""; else $transaction_date= " and b.transaction_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";

	}



 	//library array-------------------
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");

	$departArr = return_library_array("select id,department_name from lib_department where status_active=1 and is_deleted=0","id","department_name");
	$storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name");
	$itemnameArr = return_library_array("select id,item_name from lib_item_creation where status_active=1 and is_deleted=0","id","item_name");
	$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	$variable_store_wise_rate=return_field_value("auto_transfer_rcv","variable_settings_inventory","company_name=$cbo_company_name and variable_list=47 and item_category_id=8 and status_active=1 and is_deleted=0","auto_transfer_rcv");
	if($variable_store_wise_rate==1)
	{
		$sql = "select c.id, a.issue_number, a.issue_date, a.req_no, b.cons_uom, b.cons_quantity, b.store_amount as cons_amount, a.remarks, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id, c.item_category_id, c.item_description, c.item_size ,c.item_group_id, b.order_id
		from inv_issue_master a, inv_transaction b, product_details_master c
		where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=2 and a.entry_form=21 and b.status_active=1 $company_id $item_category_id $item_code  $department_id $section_id $store_id $location_id $transaction_date $group_id $reference_cond
		order by a.store_id,a.item_category,c.item_group_id";
	}
	else
	{
		$sql = "select c.id,a.issue_number,a.issue_date,a.req_no,b.cons_uom, b.cons_quantity,b.cons_amount, a.remarks,b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id, c.item_category_id, c.item_description,c.item_size ,c.item_group_id, b.order_id
		from inv_issue_master a, inv_transaction b, product_details_master c
		where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=2 and a.entry_form=21 and b.status_active=1 $company_id $item_category_id $item_code  $department_id $section_id $store_id $location_id $transaction_date $group_id $reference_cond
		order by a.store_id,a.item_category,c.item_group_id";
	}

	//echo $sql;
	$result = sql_select($sql);
	$all_data=array();
	foreach($result as $row)
	{
		$key=$row[csf('department_id')].'_'.$row[csf('item_category_id')].'_'.$row[csf('item_group_id')].'_'.$row[csf('issue_number')].'_'.$row[csf('item_description')].'_'.$row[csf('item_size')];
		$dataArr[$row[csf('department_id')]][$row[csf('item_category_id')]][$row[csf('item_group_id')]][$row[csf('issue_number')]][]=array(
			keys=>$key,
			department_id=>$row[csf('department_id')],
			item_category_id=>$row[csf('item_category_id')],
			item_group_id=>$row[csf('item_group_id')],
			issue_number=>$row[csf('issue_number')],
			issue_date=>$row[csf('issue_date')],
			requisition_no=>$row[csf('req_no')],
			remarks=>$row[csf('remarks')],
			cons_uom=>$row[csf('cons_uom')],
			remarks=>$row[csf('remarks')],
			item_description=>$row[csf('item_description')],
			item_size=>$row[csf('item_size')]
		);

		$amountArr[$key]+=$row[csf('cons_amount')];
		$qtyArr[$key]+=$row[csf('cons_quantity')];
	}

	//var_dump($dataArr[7][18][119]);
	//$r=1;
	// ob_start();
	?>
	<div style="width:100%;">
     <fieldset style="width:750px;">
        <table style="width:720px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
            <tr class="form_caption" style="border:none;">
                <td colspan="10" align="center" style="border:none;font-size:20px; font-weight:bold" ><strong>Department Wise Issue Report- Detail</strong></td>
            </tr>
            <tr style="border:none;">
                <td colspan="10" align="center" style="border:none; font-size:17px;"><strong>
                    Company Name : <? echo $companyArr[str_replace("'","",$cbo_company_name)]; ?>  </strong>
                </td>
            </tr>
            <tr>
            <td align="center" style="border:none; "><? if($cbo_store_name!=0) {?><b>Store:</b><? }?><?  echo $storeArr[$cbo_store_name]; ?></td>
            </tr>
            <tr style="border:none;">
                <td colspan="10" align="center" style="border:none;font-size:12px; font-weight:bold">
                    <? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date)."   To : ".change_date_format($to_date)."" ;?>
                </td>
            </tr>

            <?
	/*	$item_category_array=array();
		$item_group_array=array();
		$store_loc_array=array();
	*/
		$comp_total_qnty=0;
		$comp_total_amount=0;
		$r=1;

		foreach($dataArr as $departeid=>$depart_data)
		{
			$depart_total_qnty=0;
			$depart_total_amount=0;
			?>
               	<tr>
                	<td colspan="10" style="font-size:20px"><b>Department : <? echo $departArr[$departeid]; ?></b></td>
            	</tr>
			<?
			foreach($depart_data as $catid=>$catdata)
			{
				$cate_total_qnty=0;
				$cate_total_amount=0;
				foreach($catdata as $gorupid=>$groupdata)
				{
					?>
						<tr>

							 <td colspan="4"><b>Item Group : <? echo $itemgroupArr[$gorupid];//$itemgroupArr[$item_group_id]; ?></b></td>
						</tr>
                     </table>
                    <div style="width:750px;" id="scroll_body" >
                    <table style="width:720px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_2" >
                        <thead>
                            <th width="50" >SL</th>
                            <th width="200" >Issue Number</th>
                            <th width="200" >Item Des.</th>
                            <th width="200" >Issue Date</th>
                            <th width="200" >Req. No</th>
                            <th width="100" >Quantity</th>
                             <th width="70" >UoM</th>
                              <th width="100" >Rate</th>
                            <th width="100" >Amount</th>
                             <th width="100" >Remarks</th>


                        </thead>
                        <tbody>
					<?

					$total_qnty=0;
					$total_amount=0;

					foreach($groupdata as $rowdataarr)
					{
					foreach($rowdataarr as $rowid=>$rowdata){
						if ($r%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						if($rowdata[csf("transaction_type")]==1)
							$stylecolor='style="color:#A61000"';
						else
							$stylecolor='style="color:#000000"';

						$cons_amount=$amountArr[$rowdata['keys']];
						$cons_amount_sum += $cons_amount;

						$cons_quantity=$qtyArr[$rowdata['keys']];
						$cons_quantity_sum += $cons_quantity;
						$avg_rate=$cons_amount/$cons_quantity;
						?>
                            <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $r; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $r; ?>">
                            	<td align="center"><? echo $r;?></td>
                            	<td><? echo $rowdata['issue_number']; ?></td>
                                <td><?  echo $rowdata['item_description'].",".$rowdata['item_size']; ?></td>
                                <td><? echo change_date_format($rowdata['issue_date']); ?></td>
                                 <td><? echo $rowdata['requisition_no']; ?></td>
                                 <td align="right"><? echo number_format($qtyArr[$rowdata['keys']],2); ?></td>
                                <td align="center"><? echo $unit_of_measurement[$rowdata['cons_uom']]; ?></td>
                                <td align="right"><? echo number_format($avg_rate,2,'.','');//number_format($avg_rate); ?></td>
                                <td align="right"><? echo number_format($amountArr[$rowdata['keys']],2,'.',''); ?></td>
                               <td><? echo $rowdata['remarks']; ?></td>
                           </tr>

						<?
						$total_qnty+=$qtyArr[$rowdata['keys']];
						$total_amount+=$amountArr[$rowdata['keys']];
						$r++;
					}
					}
					?>
                    	<tr>
                        	<td colspan="5" align="right"><b>Group Total: </b></td>
							<td align="right"><b><? echo number_format($total_qnty,0,'',','); ?></b></td>
                            <td></td>
                            <td></td>
                            <td align="right"><b><? echo number_format($total_amount,2,'.',''); ?></b></td>
                            <td>&nbsp;</td>
						</tr>
                    <?
					$cate_total_qnty+=$total_qnty;
					$cate_total_amount+=$total_amount;
				}
				?>

				<?
				$depart_total_qnty+=$cate_total_qnty;
				$depart_total_amount+=$cate_total_amount;
			}
			?>
                <tr>
                    <td colspan="5" align="right"><b>Department Total: </b></td>
                    <td align="right"><b><? echo number_format($depart_total_qnty,0,'',','); ?></b></td>
                     <td></td>
                     <td></td>
                    <td align="right"><b><? echo number_format($depart_total_amount,2,'.',''); ?></b></td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
			<?
		 	$comp_total_qnty+=$depart_total_qnty;
			$comp_total_amount+=$depart_total_amount;
		}
		?>
            <tfoot>
            	<tr>
                    <td align="right" colspan="5" ><strong>Grand Total : </strong></td>
                    <td align="right"><strong><? echo number_format($comp_total_qnty,0,'',','); ?></strong></td>
                     <td></td>
                      <td></td>
                    <td align="right" ><strong><? echo number_format($comp_total_amount,2,'.',''); ?></strong></td>
                    <td>&nbsp;</td>
                </tr>
            </tfoot>
       </table>
     </div>
    </fieldset>
   </div>
     <?
    //die;
	// $html = ob_get_contents();
	// ob_clean();
	// //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	// foreach (glob("$user_id*.xls") as $filename) {
	// 	if( @filemtime($filename) < (time()-$seconds_old) )
	// 	@unlink($filename);
	// }
	// //---------end------------//
	// $name=time();
	// $filename=$user_id."_".$name.".xls";
	// $create_new_doc = fopen($filename, 'w');
	// $is_created = fwrite($create_new_doc, $html);
	// echo "$html**$filename";
	exit();
}

if($action=="generate_summary_report") // shafiq
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	// ============================== GETTING ITEM CATEGORY =================================
	$sql=sql_select("SELECT distinct a.id,a.item_account,a.item_category_id,a.sub_group_name,a.item_description, a.product_name_details,a.item_size,a.unit_of_measure,a.company_id,a.status_active,b.item_name from product_details_master a, lib_item_group b where a.is_deleted=0 and b.is_deleted=0 and a.item_group_id=b.id and a.item_category_id in ($cbo_item_category_id) and a.company_id=$cbo_company_name");

	$itemdes=array();
	foreach($sql as $row)
	{
		$itemdes[$row[csf('item_category_id')]].=$row[csf('product_name_details')]." ,";
	}

	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	if ($cbo_company_name==0) $company_id =""; else $company_id =" and a.company_id='$cbo_company_name'";
	if ($cbo_item_category_id==0) $item_category_id=""; else $item_category_id=" and b.item_category in ($cbo_item_category_id)";
	if ($item_account_id==0) $item_code=""; else $item_code=" and b.prod_id in ($item_account_id)";
	if ($item_group_id==0) $group_id=""; else $group_id=" and c.item_group_id in($item_group_id)";
	if ($cbo_department==0) $department_id=""; else $department_id=" and b.department_id='$cbo_department'";
	if ($cbo_section==0) $section_id=""; else $section_id=" and b.section_id='$cbo_section'";
	if ($cbo_store_name==0){ $store_id="";}else{$store_id=" and b.store_id=$cbo_store_name";}
	if ($cbo_location==0){ $location_id="";}else{$location_id=" and b.location_id=$cbo_location";}
	$reference_cond = "";
	if($cbo_search_by == 1 && trim(str_replace("'","",$txt_reference_id)) != "")
	{
		$reference_cond = " and a.req_no like '%".$txt_reference_id."%'";
	}
	else if($cbo_search_by == 2 && trim(str_replace("'","",$txt_reference_id)) != "")
	{
		$reference_cond = " and a.issue_number_prefix_num like  '%".$txt_reference_id."%'";
	}
	if ($cbo_location==0) $location_id=""; else $location_id=" and b.location_id=$cbo_location";
	if ($cbo_location==0) $location_id=""; else $location_id=" and b.location_id=$cbo_location";

	if($db_type==0)
	{
		if( $from_date==0 && $to_date==0 ) $transaction_date=""; else $transaction_date= " and b.transaction_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
 	}
	if($db_type==2)
	{
		if( $from_date==0 && $to_date==0 ) $transaction_date=""; else $transaction_date= " and b.transaction_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";

	}

 	//library array-------------------
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$departArr = return_library_array("select id,department_name from lib_department where status_active=1 and is_deleted=0","id","department_name");
	$storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name");
	$itemnameArr = return_library_array("select id,item_name from lib_item_creation where status_active=1 and is_deleted=0","id","item_name");
	$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	$variable_store_wise_rate=return_field_value("auto_transfer_rcv","variable_settings_inventory","company_name=$cbo_company_name and variable_list=47 and item_category_id=8 and status_active=1 and is_deleted=0","auto_transfer_rcv");
	if($variable_store_wise_rate==1)
	{
		$sql = "SELECT c.id, a.issue_number, a.issue_date, a.req_no, b.cons_uom, b.cons_quantity, b.store_amount as cons_amount, a.remarks,b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id, c.item_category_id, c.item_description,c.item_size ,c.item_group_id, b.order_id
		from inv_issue_master a, inv_transaction b, product_details_master c
		where a.id=b.mst_id and b.prod_id=c.id  and b.transaction_type=2 and a.entry_form=21 and b.status_active=1 $company_id $item_category_id $item_code  $department_id $section_id $store_id $location_id $transaction_date $group_id $reference_cond
		order by a.store_id,a.item_category,c.item_group_id";
	}
	else
	{
		$sql = "SELECT c.id,a.issue_number,a.issue_date,a.req_no,b.cons_uom, b.cons_quantity,b.cons_amount, a.remarks,b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id, c.item_category_id, c.item_description,c.item_size ,c.item_group_id, b.order_id
		from inv_issue_master a, inv_transaction b, product_details_master c
		where a.id=b.mst_id and b.prod_id=c.id  and b.transaction_type=2 and a.entry_form=21 and b.status_active=1 $company_id $item_category_id $item_code  $department_id $section_id $store_id $location_id $transaction_date $group_id $reference_cond
		order by a.store_id,a.item_category,c.item_group_id";
	}
	// ==================================== MAIN QUERY ============================

	// echo $sql;
	$result = sql_select($sql);

	$all_data=array();
	foreach($result as $row)
	{
		$dataArr[$row[csf('department_id')]][$row[csf('cons_uom')]][$row[csf('item_category_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['issue_number']=$row[csf('issue_number')];
		$dataArr[$row[csf('department_id')]][$row[csf('cons_uom')]][$row[csf('item_category_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['issue_date']=$row[csf('issue_date')];
		$dataArr[$row[csf('department_id')]][$row[csf('cons_uom')]][$row[csf('item_category_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['req_no']=$row[csf('req_no')];
		$dataArr[$row[csf('department_id')]][$row[csf('cons_uom')]][$row[csf('item_category_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['remarks']=$row[csf('remarks')];
		$dataArr[$row[csf('department_id')]][$row[csf('cons_uom')]][$row[csf('item_category_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['item_size']=$row[csf('item_size')];
		$dataArr[$row[csf('department_id')]][$row[csf('cons_uom')]][$row[csf('item_category_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['cons_amount']+=$row[csf('cons_amount')];
		$dataArr[$row[csf('department_id')]][$row[csf('cons_uom')]][$row[csf('item_category_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['cons_quantity']+=$row[csf('cons_quantity')];
	}

	// ================================ COUNT ROWSPAN ==================================
	$rowspan = array();
	$uomArr = array();
	foreach($dataArr as $departeid=>$depart_data)
	{
		foreach($depart_data as $uom=>$uom_data)
		{
			foreach($uom_data as $catid=>$catdata)
			{
				foreach($catdata as $gorupid=>$groupdata)
				{
					foreach($groupdata as $item_des=>$rowdata)
					{
						$rowspan[$departeid]++;
					}
				}
				$uomArr[$departeid]++;
			}

		}
	}
	// echo "<pre>";
	// print_r($uomArr);
	// echo "</pre>";

	// ob_start();
	?>
	<div style="width:100%;">
     <fieldset style="width:815px;">
        <table style="width:810px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
            <tr class="form_caption" style="border:none;">
                <td colspan="10" align="center" style="border:none;font-size:20px; font-weight:bold" ><strong>Department Wise Issue Report- Summary</strong></td>
            </tr>
            <tr style="border:none;">
                <td colspan="10" align="center" style="border:none; font-size:17px;"><strong>
                    Company Name : <? echo $companyArr[str_replace("'","",$cbo_company_name)]; ?>  </strong>
                </td>
            </tr>
            <tr>
            <td align="center" style="border:none; "><? if($cbo_store_name!=0) {?><b>Store:</b><? }?><?  echo $storeArr[$cbo_store_name]; ?></td>
            </tr>
            <tr style="border:none;">
                <td colspan="10" align="center" style="border:none;font-size:12px; font-weight:bold">
                    <? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date)."   To : ".change_date_format($to_date)."" ;?>
                </td>
            </tr>
        </table>
        <div style="width:815px;" id="scroll_body" >
        <table style="width:810px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_2" >
            <thead>
                <th width="120" >Department</th>
                <th width="120" >Item Category</th>
                <th width="120" >Item Group</th>
                <th width="200" >Item Des.</th>
                <th width="70" >UOM</th>
                <th width="80" >Issue Total</th>
                <th width="100" >Remarks</th>
            </thead>
            <tbody>
	        <?
			$r=1;
			foreach($dataArr as $departeid=>$depart_data)
			{
				$r=0;
				foreach($depart_data as $uom=>$uom_data)
				{
					$total=0;
					foreach($uom_data as $catid=>$catdata)
					{
						foreach($catdata as $gorupid=>$groupdata)
						{
							foreach($groupdata as $item_des=>$rowdata)
							{
								if ($r%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
								if($rowdata[csf("transaction_type")]==1)
									$stylecolor='style="color:#A61000"';
								else
									$stylecolor='style="color:#000000"';
								?>
		                            <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $r; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $r; ?>">
		                            	<? if($r==0){?>
		                            	<td valign="middle" rowspan="<? echo $rowspan[$departeid]+$uomArr[$departeid];?>">
		                            		<b><? echo $departArr[$departeid]; ?></b>
		                            	</td>
		                            	<?}?>
		                                <td><? echo $item_category[$catid]; ?></td>
		                                <td><? echo $itemgroupArr[$gorupid]; ?></td>
		                                <td><? echo $item_des; ?></td>
		                                <td align="center"><? echo $unit_of_measurement[$uom]; ?></td>
		                                <td align="right"><? echo number_format($rowdata['cons_quantity'],2,'.',''); ?></td>
		                               <td><? echo $rowdata['remarks']; ?></td>
		                           </tr>

								<?
								$total += $rowdata['cons_quantity'];
								$r++;
							}
						}
					}
					?>
                    	<tr>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                        	<td align="right"><b>Total: </b></td>
                            <td align="right"><b><? echo number_format($total,2,'.',''); ?></b></td>
                            <td>&nbsp;</td>
						</tr>
                    <?
				}
			}
			?>
			</tbody>
       </table>
     </div>
    </fieldset>
   </div>
     <?
    //die;
	// $html = ob_get_contents();
	// ob_clean();
	// //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	// foreach (glob("$user_id*.xls") as $filename) {
	// 	if( @filemtime($filename) < (time()-$seconds_old) )
	// 	@unlink($filename);
	// }
	// //---------end------------//
	// $name=time();
	// $filename=$user_id."_".$name.".xls";
	// $create_new_doc = fopen($filename, 'w');
	// $is_created = fwrite($create_new_doc, $html);
	// echo "$html**$filename";
	exit();
}

if($action=="generate_summary_report2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	// ============================== GETTING ITEM CATEGORY =================================
	$sql=sql_select("SELECT distinct a.id,a.item_account,a.item_category_id,a.sub_group_name,a.item_description, a.product_name_details,a.item_size,a.unit_of_measure,a.company_id,a.status_active,b.item_name from product_details_master a, lib_item_group b where a.is_deleted=0 and b.is_deleted=0 and a.item_group_id=b.id and a.item_category_id in ($cbo_item_category_id) and a.company_id=$cbo_company_name");

	$itemdes=array();
	foreach($sql as $row)
	{
		$itemdes[$row[csf('item_category_id')]].=$row[csf('product_name_details')]." ,";
	}

	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	if ($cbo_company_name==0) $company_id =""; else $company_id =" and a.company_id='$cbo_company_name'";
	if ($cbo_item_category_id==0) $item_category_id=""; else $item_category_id=" and b.item_category in ($cbo_item_category_id)";
	if ($item_account_id==0) $item_code=""; else $item_code=" and b.prod_id in ($item_account_id)";
	if ($item_group_id==0) $group_id=""; else $group_id=" and c.item_group_id in($item_group_id)";
	if ($cbo_department==0) $department_id=""; else $department_id=" and b.department_id='$cbo_department'";
	if ($cbo_section==0) $section_id=""; else $section_id=" and b.section_id='$cbo_section'";
	if ($cbo_store_name==0){ $store_id="";}else{$store_id=" and b.store_id=$cbo_store_name";}
	if ($cbo_location==0){ $location_id="";}else{$location_id=" and b.location_id=$cbo_location";}

	$reference_cond = "";
	if($cbo_search_by == 1 && trim(str_replace("'","",$txt_reference_id)) != "")
	{
		$reference_cond = " and a.req_no like '%".$txt_reference_id."%'";
	}
	else if($cbo_search_by == 2 && trim(str_replace("'","",$txt_reference_id)) != "")
	{
		$reference_cond = " and a.issue_number_prefix_num like  '%".$txt_reference_id."%'";
	}

	if($db_type==0)
	{
		if( $from_date==0 && $to_date==0 ) $transaction_date=""; else $transaction_date= " and b.transaction_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
 	}
	if($db_type==2)
	{
		if( $from_date==0 && $to_date==0 ) $transaction_date=""; else $transaction_date= " and b.transaction_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";

	}

 	//library array-------------------
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$departArr = return_library_array("select id,department_name from lib_department where status_active=1 and is_deleted=0","id","department_name");
	$sectionArr = return_library_array("select id,section_name from lib_section where status_active =1 and is_deleted=0","id","section_name");
	$storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name");
	$itemnameArr = return_library_array("select id,item_name from lib_item_creation where status_active=1 and is_deleted=0","id","item_name");
	$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	$variable_store_wise_rate=return_field_value("auto_transfer_rcv","variable_settings_inventory","company_name=$cbo_company_name and variable_list=47 and item_category_id=8 and status_active=1 and is_deleted=0","auto_transfer_rcv");
	if($variable_store_wise_rate==1)
	{
		$sql = "SELECT c.id,a.issue_number,a.issue_date,a.req_no,b.cons_uom, b.cons_quantity,b.store_amount as cons_amount, a.remarks,b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id, c.item_category_id, c.item_description,c.item_size ,c.item_group_id, c.sub_group_name, b.order_id
		from inv_issue_master a, inv_transaction b, product_details_master c
		where a.id=b.mst_id and b.prod_id=c.id  and b.transaction_type=2 and a.entry_form=21 and b.status_active=1 $company_id $item_category_id $item_code  $department_id $section_id $store_id $location_id $transaction_date $group_id $reference_cond
		order by c.item_description";
	}
	else
	{
		$sql = "SELECT c.id,a.issue_number,a.issue_date,a.req_no,b.cons_uom, b.cons_quantity,b.cons_amount, a.remarks,b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id, c.item_category_id, c.item_description,c.item_size ,c.item_group_id, c.sub_group_name, b.order_id
		from inv_issue_master a, inv_transaction b, product_details_master c
		where a.id=b.mst_id and b.prod_id=c.id  and b.transaction_type=2 and a.entry_form=21 and b.status_active=1 $company_id $item_category_id $item_code  $department_id $section_id $store_id $location_id $transaction_date $group_id $reference_cond
		order by c.item_description";
	}
	// $itemSubGroupArr=return_library_array( "select id, sub_group_name from lib_item_sub_group",'id','sub_group_name');
	// ==================================== MAIN QUERY ============================

	// echo $sql;
	$result = sql_select($sql);

	$dataArr=array();
	foreach($result as $row)
	{
		$dataArr[$row[csf('department_id')]][$row[csf('section_id')]][$row[csf('item_category_id')]."_".$row[csf('item_group_id')]."_".$row[csf('item_description')]."_".$row[csf('cons_uom')]]['item_category_id']=$row[csf('item_category_id')];
		$dataArr[$row[csf('department_id')]][$row[csf('section_id')]][$row[csf('item_category_id')]."_".$row[csf('item_group_id')]."_".$row[csf('item_description')]."_".$row[csf('cons_uom')]]['item_group_id']=$row[csf('item_group_id')];
		$dataArr[$row[csf('department_id')]][$row[csf('section_id')]][$row[csf('item_category_id')]."_".$row[csf('item_group_id')]."_".$row[csf('item_description')]."_".$row[csf('cons_uom')]]['sub_group_name']=$row[csf('sub_group_name')];
		$dataArr[$row[csf('department_id')]][$row[csf('section_id')]][$row[csf('item_category_id')]."_".$row[csf('item_group_id')]."_".$row[csf('item_description')]."_".$row[csf('cons_uom')]]['item_description']=$row[csf('item_description')];
		$dataArr[$row[csf('department_id')]][$row[csf('section_id')]][$row[csf('item_category_id')]."_".$row[csf('item_group_id')]."_".$row[csf('item_description')]."_".$row[csf('cons_uom')]]['cons_uom']=$row[csf('cons_uom')];
		$dataArr[$row[csf('department_id')]][$row[csf('section_id')]][$row[csf('item_category_id')]."_".$row[csf('item_group_id')]."_".$row[csf('item_description')]."_".$row[csf('cons_uom')]]['cons_amount']+=$row[csf('cons_amount')];
		$dataArr[$row[csf('department_id')]][$row[csf('section_id')]][$row[csf('item_category_id')]."_".$row[csf('item_group_id')]."_".$row[csf('item_description')]."_".$row[csf('cons_uom')]]['cons_quantity']+=$row[csf('cons_quantity')];
		$dataArr[$row[csf('department_id')]][$row[csf('section_id')]][$row[csf('item_category_id')]."_".$row[csf('item_group_id')]."_".$row[csf('item_description')]."_".$row[csf('cons_uom')]]['remarks']=$row[csf('remarks')];
	}

	$transfer_sql = "SELECT c.id,a.transfer_system_id,a.transfer_date,b.cons_uom, b.cons_quantity,b.cons_amount, a.remarks,b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id, c.item_category_id, c.item_description,c.item_size ,c.item_group_id, c.sub_group_name, b.order_id from inv_item_transfer_mst a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=6 and a.entry_form=57 and b.status_active=1 $company_id $item_category_id $item_code $store_id $transaction_date $group_id $reference_cond ";
	// echo $transfer_sql;
	$transfer_result = sql_select($transfer_sql);

	foreach($transfer_result as $row)
	{
		$dataArr[0][0][$row[csf('item_category_id')]."_".$row[csf('item_group_id')]."_".$row[csf('item_description')]."_".$row[csf('cons_uom')]]['item_category_id']=$row[csf('item_category_id')];
		$dataArr[0][0][$row[csf('item_category_id')]."_".$row[csf('item_group_id')]."_".$row[csf('item_description')]."_".$row[csf('cons_uom')]]['item_group_id']=$row[csf('item_group_id')];
		$dataArr[0][0][$row[csf('item_category_id')]."_".$row[csf('item_group_id')]."_".$row[csf('item_description')]."_".$row[csf('cons_uom')]]['sub_group_name']=$row[csf('sub_group_name')];
		$dataArr[0][0][$row[csf('item_category_id')]."_".$row[csf('item_group_id')]."_".$row[csf('item_description')]."_".$row[csf('cons_uom')]]['item_description']=$row[csf('item_description')];
		$dataArr[0][0][$row[csf('item_category_id')]."_".$row[csf('item_group_id')]."_".$row[csf('item_description')]."_".$row[csf('cons_uom')]]['cons_uom']=$row[csf('cons_uom')];
		$dataArr[0][0][$row[csf('item_category_id')]."_".$row[csf('item_group_id')]."_".$row[csf('item_description')]."_".$row[csf('cons_uom')]]['cons_amount']+=$row[csf('cons_amount')];
		$dataArr[0][0][$row[csf('item_category_id')]."_".$row[csf('item_group_id')]."_".$row[csf('item_description')]."_".$row[csf('cons_uom')]]['cons_quantity']+=$row[csf('cons_quantity')];
		$dataArr[0][0][$row[csf('item_category_id')]."_".$row[csf('item_group_id')]."_".$row[csf('item_description')]."_".$row[csf('cons_uom')]]['remarks']=$row[csf('remarks')];
	}
	// print_r($dataArr);die;
	// ================================ COUNT ROWSPAN ==================================
	// ob_start();
	?>
	<div style="width:100%;">
     <fieldset style="width:1005px;">
        <table style="width:1000px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
            <tr class="form_caption" style="border:none;">
                <td colspan="10" align="center" style="border:none;font-size:20px; font-weight:bold" ><strong>Department Wise Issue Report- Summary</strong></td>
            </tr>
            <tr style="border:none;">
                <td colspan="10" align="center" style="border:none; font-size:17px;"><strong>
                    Company Name : <? echo $companyArr[str_replace("'","",$cbo_company_name)]; ?>  </strong>
                </td>
            </tr>
            <tr>
            <td align="center" style="border:none; "><? if($cbo_store_name!=0) {?><b>Store:</b><? }?><?  echo $storeArr[$cbo_store_name]; ?></td>
            </tr>
            <tr style="border:none;">
                <td colspan="10" align="center" style="border:none;font-size:12px; font-weight:bold">
                    <? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date)."   To : ".change_date_format($to_date)."" ;?>
                </td>
            </tr>
        </table>
        <div style="width:1005px;" id="scroll_body" >
        <table style="width:1000px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_2" >
            <thead>
                <th width="120" >Item Category</th>
                <th width="120" >Item Group</th>
                <th width="120" >Item Sub Group</th>
                <th width="200" >Item Des.</th>
                <th width="70" >UOM</th>
                <th width="80" >Net Issue Total</th>
                <th width="80" >Unit Price</th>
                <th width="80" >Total</th>
                <th >Remarks</th>
            </thead>
            <tbody>
	        <?
			$r=1;
			$grand_total_qnty=0;
			$grand_total_amount=0;
			foreach($dataArr as $departeid=>$depart_data)
			{
				$sub_total_qnty=0;
				$sub_total_amount=0;
				if ($r%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
				?>
					<tr>
						<td colspan="9" style="font-size:20px"><b>Department : <? echo $departArr[$departeid]; ?></b></td>
					</tr>
				<?
				foreach($depart_data as $sectionid=>$section_data)
				{
					$total_qnty=0;
					$total_amount=0;
					?>
					<tr>
						<td colspan="9" align="center" style="font-size:20px"><b>Section : <? echo $sectionArr[$sectionid]; ?></b></td>
					</tr>
					<?

					foreach($section_data as $k=>$rowdata)
					{
						$cons_amount=$rowdata['cons_amount'];
						$cons_amount_sum += $cons_amount;

						$cons_quantity=$rowdata['cons_quantity'];
						$cons_quantity_sum += $cons_quantity;
						$avg_rate=$cons_amount/$cons_quantity;
						?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $r; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $r; ?>">
                            	<td><? echo $item_category[$rowdata['item_category_id']]; ?></td>
                                <td><? echo $itemgroupArr[$rowdata['item_group_id']]; ?></td>
                                <td><? echo $rowdata['sub_group_name']; ?></td>
                                <td><? echo $rowdata['item_description']; ?></td>
                                <td align="center"><? echo $unit_of_measurement[$rowdata['cons_uom']]; ?></td>
								<td align="right"><? echo number_format($rowdata['cons_quantity'],2); ?></td>
                                <td align="right"><? echo number_format($avg_rate,2,'.',''); ?></td>
                                <td align="right"><? echo number_format($rowdata['cons_amount'],2,'.',''); ?></td>
                               <td><? echo $rowdata['remarks']; ?></td>
                           </tr>
						<?
						$total_qnty+=$rowdata['cons_quantity'];
						$total_amount+=$rowdata['cons_amount'];
						$sub_total_qnty+=$rowdata['cons_quantity'];
						$sub_total_amount+=$rowdata['cons_amount'];
						$grand_total_qnty+=$rowdata['cons_quantity'];
						$grand_total_amount+=$rowdata['cons_amount'];
						$r++;
					}
						?>
						<tr>
							<td colspan="4">&nbsp;</td>
							<td align="right"><b>Total: </b></td>
							<td align="right"><b><? echo number_format($total_qnty,2,'.',''); ?></b></td>
							<td>&nbsp;</td>
							<td align="right"><b><? echo number_format($total_amount,2,'.',''); ?></b></td>
							<td>&nbsp;</td>
						</tr>
						<?
				}
					?>
						<tr>
							<td colspan="4">&nbsp;</td>
							<td align="right"><b>Total: </b></td>
							<td align="right"><b><? echo number_format($sub_total_qnty,2,'.',''); ?></b></td>
							<td>&nbsp;</td>
							<td align="right"><b><? echo number_format($sub_total_amount,2,'.',''); ?></b></td>
							<td>&nbsp;</td>
						</tr>
					<?
			}
			?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="4">&nbsp;</td>
					<td align="right"><b>Total: </b></td>
					<td align="right"><b><? echo number_format($grand_total_qnty,2,'.',''); ?></b></td>
					<td>&nbsp;</td>
					<td align="right"><b><? echo number_format($grand_total_amount,2,'.',''); ?></b></td>
					<td>&nbsp;</td>
				</tr>
			</tfoot>
       </table>
     </div>
    </fieldset>
   </div>
     <?
    //die;
	// $html = ob_get_contents();
	// ob_clean();
	// //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	// foreach (glob("$user_id*.xls") as $filename) {
	// 	if( @filemtime($filename) < (time()-$seconds_old) )
	// 	@unlink($filename);
	// }
	// //---------end------------//
	// $name=time();
	// $filename=$user_id."_".$name.".xls";
	// $create_new_doc = fopen($filename, 'w');
	// $is_created = fwrite($create_new_doc, $html);
	// echo "$html**$filename";
	exit();
}

if($action=="generate_report2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$sql_cond="";
	if($cbo_company_name){ $sql_cond.=" and a.company_id='$cbo_company_name'"; }
	if($cbo_item_category_id!=""){ $sql_cond.=" and b.item_category in ($cbo_item_category_id)"; }
	if($item_account_id!=""){ $sql_cond.=" and b.prod_id in ($item_account_id)"; }
	if($item_group_id!=""){ $sql_cond.=" and c.item_group_id in($item_group_id)"; }
	if($cbo_department){ $sql_cond.=" and b.department_id='$cbo_department'"; }
	if($cbo_section){ $sql_cond.=" and b.section_id='$cbo_section'"; }
	if($cbo_store_name){ $sql_cond.=" and b.store_id=$cbo_store_name"; }
	if($cbo_location){ $sql_cond.=" and b.location_id=$cbo_location"; }
	if($cbo_sewing_floor_name){ $sql_cond.=" and d.SEWING_FLOOR=$cbo_sewing_floor_name"; }
	if($cbo_sewing_floor_line){ $sql_cond.=" and d.SEWING_LINE=$cbo_sewing_floor_line"; }

	if($cbo_search_by == 1 && trim(str_replace("'","",$txt_reference_id)) != "")
	{
		$sql_cond.= " and a.req_no like '%".$txt_reference_id."%'";
	}
	else if($cbo_search_by == 2 && trim(str_replace("'","",$txt_reference_id)) != "")
	{
		$sql_cond.= " and a.issue_number_prefix_num like  '%".$txt_reference_id."%'";
	}

	if($from_date!='' && $to_date!='')
	{
		if($db_type==0)
		{
			$sql_cond.= " and b.transaction_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		 }
		if($db_type==2)
		{
			$sql_cond.= " and b.transaction_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";
		}
	}

 	//library array-------------------
	$companyArr = return_library_array("SELECT id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");

	$departArr = return_library_array("SELECT id,department_name from lib_department where status_active=1 and is_deleted=0","id","department_name");
	$sectionArr = return_library_array("SELECT id, section_name from lib_section", "id", "section_name");
	$sewFloorArr = return_library_array("SELECT id, floor_name from lib_prod_floor", "id", "floor_name");
	$sewLineArr = return_library_array("SELECT id, line_name from lib_sewing_line", "id", "line_name");
	$sewMachineArr = return_library_array("SELECT id, machine_no from lib_machine_name", "id", "machine_no");

	$itemnameArr = return_library_array("SELECT id,item_name from lib_item_creation where status_active=1 and is_deleted=0","id","item_name");
	$itemgroupArr = return_library_array("SELECT id,item_name from lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	$variable_store_wise_rate=return_field_value("auto_transfer_rcv","variable_settings_inventory","company_name=$cbo_company_name and variable_list=47 and item_category_id=8 and status_active=1 and is_deleted=0","auto_transfer_rcv");
	if($variable_store_wise_rate==1)
	{
		$sql = "SELECT a.id as iss_id,a.issue_number,a.issue_date,a.req_no,b.cons_uom, b.cons_quantity,b.store_amount as cons_amount, b.remarks,b.machine_category, b.prod_id, b.department_id, b.section_id, c.item_category_id, c.item_description,c.item_size ,c.item_group_id, b.order_id, d.SEWING_FLOOR, d.SEWING_LINE, d.MACHINE_NO
		from inv_issue_master a, inv_transaction b, product_details_master c, inv_item_issue_requisition_mst d
		where a.id=b.mst_id and b.prod_id=c.id and a.req_id=d.id and b.transaction_type=2 and a.entry_form=21 and b.status_active=1 $sql_cond
		order by a.id,b.department_id,b.section_id,a.id,b.prod_id";
	}
	else
	{
		$sql = "SELECT a.id as iss_id,a.issue_number,a.issue_date,a.req_no,b.cons_uom, b.cons_quantity,b.cons_amount, b.remarks,b.machine_category, b.prod_id, b.department_id, b.section_id, c.item_category_id, c.item_description,c.item_size ,c.item_group_id, b.order_id, d.SEWING_FLOOR, d.SEWING_LINE, d.MACHINE_NO
		from inv_issue_master a, inv_transaction b, product_details_master c, inv_item_issue_requisition_mst d
		where a.id=b.mst_id and b.prod_id=c.id and a.req_id=d.id and b.transaction_type=2 and a.entry_form=21 and b.status_active=1 $sql_cond
		order by a.id,b.department_id,b.section_id,a.id,b.prod_id";
	}

	// echo $sql;
	$result = sql_select($sql);

	$all_data_arr=$count_depart_arr=$count_section_arr=$count_iss_arr=array();
	foreach($result as $row)
	{
		$all_data_arr[$row[csf('department_id')]][$row[csf('section_id')]][$row[csf('iss_id')]][]=$row;
		$count_depart_arr[$row[csf('department_id')]]++;
		$count_section_arr[$row[csf('department_id')]][$row[csf('section_id')]]++;
		$count_iss_arr[$row[csf('department_id')]][$row[csf('section_id')]][$row[csf('iss_id')]]++;
	}
	// ob_start();
	?>
	<div style="width:100%;">
     <fieldset style="width:1550px;">
        <table style="width:1540px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
            <tr class="form_caption">
                <td colspan="16" align="center" style="border:none;font-size:20px; font-weight:bold" ><strong>Department Wise Issue Report- Detail</strong></td>
            </tr>
            <tr >
                <td colspan="16" align="center" style="border:none; font-size:17px;"><strong>
                    Company Name : <? echo $companyArr[str_replace("'","",$cbo_company_name)]; ?>  </strong>
                </td>
            </tr>
            <tr>
            <td align="center" style="border:none; "><? if($cbo_store_name!=0) {?><b>Store:</b><? }?><?  echo $storeArr[$cbo_store_name]; ?></td>
            </tr>
            <tr>
                <td colspan="16" align="center" style="border:none;font-size:12px; font-weight:bold">
                    <? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date)."   To : ".change_date_format($to_date)."" ;?>
                </td>
            </tr>
		</table>
		<div style="width:1540px;" id="scroll_body" >
			<table style="width:1540px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_2" >
				<thead>
					<th width="120" >Department</th>
					<th width="100" >Section</th>
					<th width="100" >Floor</th>
					<th width="100" >Line</th>
					<th width="100" >Machine No</th>
					<th width="100" >Item Category</th>
					<th width="200" >Item Description</th>
					<th width="80" >UOM</th>
					<th width="80" >Quantity</th>
					<th width="80" >Rate</th>
					<th width="80" >Amount</th>
					<th width="100" >Issue Number (System ID)</th>
					<th width="100" >Issue Date</th>
					<th width="100" >Req. No</th>
					<th width="100" >Remarks</th>
				</thead>
				<tbody>
				<?
				$r=1;
				$arr_chk=$arr_chk2=$arr_chk3=$arr_chk4=array();
				foreach($all_data_arr as $departeid=>$depart_data)
				{
					$depart_total_qnty=$depart_total_amount=0;
					foreach($depart_data as $sectionid=>$section_data)
					{
						foreach($section_data as $rowid=>$rowdata){
							foreach($rowdata as $row)
							{
								if ($r%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
								$count_depart=$count_depart_arr[$row[csf('department_id')]];
								$count_section=$count_section_arr[$row[csf('department_id')]][$row[csf('section_id')]];
								$count_iss=$count_iss_arr[$row[csf('department_id')]][$row[csf('section_id')]][$row[csf('iss_id')]];

								$cons_amount=$row['CONS_AMOUNT'];
								$cons_quantity=$row['CONS_QUANTITY'];
								$avg_rate=$cons_amount/$cons_quantity;
								?>
								<tr bgcolor="<?=$bgcolor; ?>" <?=$stylecolor; ?> onclick="change_color('tr_<?=$r; ?>','<?=$bgcolor; ?>')" id="tr_<?=$r; ?>">
									<?
										if(!in_array($row['DEPARTMENT_ID'],$arr_chk))
										{
											$arr_chk[]=$row['DEPARTMENT_ID'];
											?>
												<td rowspan="<?=$count_depart;?>"><?=$departArr[$row['DEPARTMENT_ID']]; ?></td>
											<?
										}
										if(!in_array($row['DEPARTMENT_ID']."__".$row['SECTION_ID'],$arr_chk2))
										{
											$arr_chk2[]=$row['DEPARTMENT_ID']."__".$row['SECTION_ID'];
											?>
												<td rowspan="<?=$count_section;?>"><?=$sectionArr[$row['SECTION_ID']]; ?></td>
											<?
										}
										if(!in_array($row['DEPARTMENT_ID']."__".$row['SECTION_ID']."__".$row['ISS_ID'],$arr_chk3))
										{
											$arr_chk3[]=$row['DEPARTMENT_ID']."__".$row['SECTION_ID']."__".$row['ISS_ID'];
											?>
												<td rowspan="<?=$count_iss;?>"><?=$sewFloorArr[$row['SEWING_FLOOR']]; ?></td>
												<td rowspan="<?=$count_iss;?>"><?=$sewLineArr[$row['SEWING_LINE']]; ?></td>
												<td rowspan="<?=$count_iss;?>"><?=$sewMachineArr[$row['MACHINE_NO']]; ?></td>
											<?
										}
									?>
									<td><?=$item_category[$row['ITEM_CATEGORY_ID']]; ?></td>
									<td><?=$row['ITEM_DESCRIPTION']; ?></td>
									<td align="center"><?=$unit_of_measurement[$row['CONS_UOM']]?></td>
									<td align="right"><?=number_format($cons_quantity,2);?></td>
									<td align="right"><?=number_format($avg_rate,2);?></td>
									<td align="right"><?=number_format($cons_amount,2); ?></td>
									<?
										if(!in_array($row['DEPARTMENT_ID']."__".$row['SECTION_ID']."__".$row['ISS_ID'],$arr_chk4))
										{
											$arr_chk4[]=$row['DEPARTMENT_ID']."__".$row['SECTION_ID']."__".$row['ISS_ID'];
											?>
												<td rowspan="<?=$count_iss;?>"><?=$row['ISSUE_NUMBER']; ?></td>
												<td align="center" rowspan="<?=$count_iss;?>"><?=change_date_format($row['ISSUE_DATE']); ?></td>
												<td rowspan="<?=$count_iss;?>"><?=$row['REQ_NO']; ?></td>
											<?
										}
									?>
									<td><?=$row['REMARKS']; ?></td>
								</tr>
								<?
								$depart_total_qnty+=$cons_quantity;
								$depart_total_amount+=$cons_amount;
								$r++;
							}
						}
					}
					?>
					<tr>
						<td colspan="8" align="right"><b>Department Total: </b></td>
						<td align="right"><b><? echo number_format($depart_total_qnty,0,'',','); ?></b></td>
						<td></td>
						<td align="right"><b><? echo number_format($depart_total_amount,2,'.',''); ?></b></td>
						<td colspan="4">&nbsp;</td>
					</tr>
					<?
					$comp_total_qnty+=$depart_total_qnty;
					$comp_total_amount+=$depart_total_amount;
				}
				?>
				</tbody>
				<tfoot>
					<tr>
						<td align="right" colspan="8" ><strong>Grand Total : </strong></td>
						<td align="right"><strong><? echo number_format($comp_total_qnty,0,'',','); ?></strong></td>
						<td></td>
						<td align="right" ><strong><? echo number_format($comp_total_amount,2,'.',''); ?></strong></td>
						<td colspan="4">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
    	</div>
    </fieldset>
   </div>
     <?
    //die;
	// $html = ob_get_contents();
	// ob_clean();
	// //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	// foreach (glob("$user_id*.xls") as $filename) {
	// 	if( @filemtime($filename) < (time()-$seconds_old) )
	// 	@unlink($filename);
	// }
	// //---------end------------//
	// $name=time();
	// $filename=$user_id."_".$name.".xls";
	// $create_new_doc = fopen($filename, 'w');
	// $is_created = fwrite($create_new_doc, $html);
	// echo "$html**$filename";
	exit();
}
?>