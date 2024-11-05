<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

//--------------------------------------------------------------------------------------------

if($action=="load_drop_down_buyer")
{
	if($data != 0)
	{
		echo create_drop_down( "cbo_buyer_id", 120, "SELECT buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "" );   	 
		exit();
	}
	else{  	
		echo create_drop_down("cbo_buyer_id", 120, $buyer_sql, "id,buyer_name", 1, "-- Select Buyer --", 0, ""); 
		exit();
	}
}

/*if ($action=="job_no_popup")
{
	echo load_html_head_contents("Style Ref Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
    $data=explode('_',$data);
    $text_style_no=str_replace("'","",$data[3]);
	//print_r ($data);
	?>	
    <script>
 		var selected_id = new Array;
		var selected_name = new Array;
		var selected_style_name = new Array();

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
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			var selectStyle = splitSTR[3];
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
					
			if( jQuery.inArray( selectID, selected_id, selectStyle ) == -1 )
			{
			    selected_id.push( selectID );
			    selected_name.push( selectDESC );					
			    selected_style_name.push( selectStyle );					
			}
			else
		    {
				for( var i = 0; i < selected_id.length; i++ )
				{
				    if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 ); 
				selected_style_name.splice( i, 1 ); 
			}
			var id = ''; var name = ''; var style = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
			    id += selected_id[i] + ',';
			    name += selected_name[i] + ','; 
			    style += selected_style_name[i] + ','; 
			}
			id 	 = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 ); 
			style = style.substr( 0, style.length - 1 ); 
			$('#txt_job_id').val( id );
		    $('#txt_job_no').val( name );
		    $('#txt_style_ref').val( style );
		}

	</script>
    <input type="hidden" id="txt_job_id" />
	<input type='hidden' id='txt_job_no' />
	<input type='hidden' id='txt_style_ref' />
    <?
	if ($data[0]==0) $company_id=""; else $company_id=" and a.company_name=$data[0]";
	if ($data[1]==0) $buyer_id=""; else $buyer_id=" and a.buyer_name=$data[1]";
	
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$teamMemberArr = return_library_array("select id,team_leader_name from lib_marketing_team ","id","team_leader_name");
	
	$sql= "SELECT a.id, a.job_no,a.job_no_prefix_num, a.style_ref_no, a.product_dept, a.dealing_marchant, a.team_leader from wo_po_details_master a, wo_po_break_down b where  b.job_no_mst=a.job_no and a.status_active=1 and a.is_deleted=0 $company_id $buyer_id group by a.id, a.job_no,a.job_no_prefix_num, a.style_ref_no, a.product_dept, a.dealing_marchant, a.team_leader order by a.job_no";
	//echo $sql;//die;
	
	$arr=array(2=>$product_dept,3=>$marchentrArr,4=>$teamMemberArr);
	echo  create_list_view("rpt_tablelist_view", "Job No,Style Ref.,Prod. Dept.,Marchant,Team Name", "100,110,110,150,150","680","360",0, $sql , "js_set_value", "id,job_no,style_ref_no", "", 1, "0,0,product_dept,dealing_marchant,team_leader", $arr , "job_no_prefix_num,style_ref_no,product_dept,dealing_marchant,team_leader", "",'setFilterGrid("rpt_tablelist_view",-1);','0,0,0,0,0','',1) ;
	exit();
}*/

if ($action=="style_no_popup")
{
	echo load_html_head_contents("Style No Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
    $data=explode('_',$data);
	//print_r ($data);
	?>	
    <script>
	function js_set_value( style_id )
	{
		//alert(po_id)
		document.getElementById('txt_style_id').value=style_id;
		parent.emailwindow.hide();
	}
    setFilterGrid('rpt_tablelist_view',-1); 
	</script>
    <input type="hidden" id="txt_style_id" />
    <?
	if ($data[0]==0) $company_id=""; else $company_id=" and a.company_name=$data[0]";
	if ($data[1]==0) $buyer_id=""; else $buyer_id=" and a.buyer_name=$data[1]";
	
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$teamMemberArr = return_library_array("select id,team_leader_name from lib_marketing_team ","id","team_leader_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	
	// $sql= "select a.id, a.job_no,a.job_no_prefix_num, a.style_ref_no, a.product_dept, a.dealing_marchant, a.team_leader from wo_po_details_master a, wo_po_break_down  b where  b.job_no_mst=a.job_no and a.status_active=1 and a.is_deleted=0 $company_id $buyer_id group by a.id, a.job_no,a.job_no_prefix_num, a.style_ref_no, a.product_dept, a.dealing_marchant, a.team_leader order by a.job_no";
	$sql= "SELECT a.id, a.wo_number, a.dealing_marchant, a.team_leader, b.buyer_id, b.style_no from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.entry_form=284 and a.wo_basis_id=3 and a.status_active=1 and b.status_active=1 $company_id $buyer_id group by a.id, a.wo_number, a.dealing_marchant, a.team_leader, b.buyer_id, b.style_no order by a.id desc";
	//echo $sql;//die;

	$arr=array(2=>$buyerArr,3=>$marchentrArr,4=>$teamMemberArr);
	echo  create_list_view("rpt_tablelist_view", "Style Ref.,WO Number,Buyer Name,Marchant,Team Name", "110,100,110,150,150","680","360",0, $sql , "js_set_value", "id,style_no", "", 1, "0,0,buyer_id,dealing_marchant,team_leader", $arr , "style_no,wo_number,buyer_id,dealing_marchant,team_leader", "",'setFilterGrid("rpt_tablelist_view",-1);','0,0,0,0,0','') ;
	exit();
}

if ($action == "generate_report")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$rpt_type=str_replace("'","",$rpt_type);
	//echo $cbo_store_name;//die;
	if($rpt_type==1)
	{
		$companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");
		$supplierArr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
		$buyerArr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
		$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
		$yarn_composition_arr = return_library_array("select id, composition_name from lib_composition_array", 'id', 'composition_name');
		$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');	
		$search_cond='';
		if($cbo_company_name!=0)
		{
			$search_cond.=" and a.company_name=$cbo_company_name ";
		}
		if($cbo_buyer!=0)
		{
			$search_cond.=" and b.buyer_id=$cbo_buyer ";
		}
		if($txt_style_no!='')
		{
			$search_cond.=" and b.style_no='$txt_style_no' ";
		}
		if ($from_date != '' && $to_date != '') 
		{
			if ($db_type == 0) {
				$date_cond = "and a.wo_date between '" . change_date_format($from_date, 'yyyy-mm-dd') . "' and '" . change_date_format($to_date, 'yyyy-mm-dd') . "'";
			} else if ($db_type == 2) {
				$date_cond = "and a.wo_date between '" . change_date_format($from_date, '', '', 1) . "' and '" . change_date_format($to_date, '', '', 1) . "'";
			}
		} 
		else 
		{
			$date_cond = '';
		}

		$sql="SELECT a.id as MST_ID ,a.WO_DATE, a.WO_NUMBER, a.SUPPLIER_ID, a.DELIVERY_DATE, b.BUYER_ID, b.STYLE_NO, b.YARN_COUNT,b.COLOR_NAME,b.YARN_COMP_TYPE1ST, b.YARN_COMP_PERCENT1ST, b.REQ_QUANTITY, c.PI_ID
		from wo_non_order_info_mst a, wo_non_order_info_dtls b
		left join com_pi_item_details c on c.item_category_id=1 and c.work_order_id=b.mst_id and c.work_order_dtls_id=b.id
		where a.wo_basis_id=3 and a.entry_form=284 and a.id=b.mst_id $date_cond $search_cond order by a.id,b.buyer_id,b.style_no";
		// echo $sql; die;
		$result = sql_select($sql);
		$pi_id_all=$mst_id_all='';
		$yarn_data_array=array();$rowspan_arr=array();
		foreach($result as $row)
		{
			$key=$row['WO_NUMBER'].'_'.$row['COLOR_NAME'].'_'.$row['YARN_COUNT'].'_'.$row['YARN_COMP_TYPE1ST'].'_'.$row['YARN_COMP_PERCENT1ST'];
			$yarn_data_array[$key]['wo_number']=$row['WO_NUMBER'];
			$yarn_data_array[$key]['wo_date']=$row['WO_DATE'];
			$yarn_data_array[$key]['supplier_id']=$row['SUPPLIER_ID'];
			$yarn_data_array[$key]['delivery_date']=$row['DELIVERY_DATE'];
			$yarn_data_array[$key]['buyer_id']=$row['BUYER_ID'];
			$yarn_data_array[$key]['style_no']=$row['STYLE_NO'];
			$yarn_data_array[$key]['yarn_count']=$row['YARN_COUNT'];
			$yarn_data_array[$key]['color_name']=$row['COLOR_NAME'];
			$yarn_data_array[$key]['yarn_comp_type1st']=$row['YARN_COMP_TYPE1ST'];
			$yarn_data_array[$key]['YARN_COMP_PERCENT1ST']=$row['YARN_COMP_PERCENT1ST'];
			$yarn_data_array[$key]['REQ_QUANTITY']+=$row['REQ_QUANTITY'];
			$mst_id_all.=$row['MST_ID'].',';
			if($row['PI_ID']){$pi_id_all.=$row['PI_ID'].',';}
		}
		foreach($yarn_data_array as $row){
			$rowspan_arr[$row['wo_number'].'*'.$row['buyer_id'].'*'.$row['style_no']]++;
		}
		$mst_id_all=implode(",",array_unique(explode(",",chop($mst_id_all,','))));
		$pi_id_all=implode(",",array_unique(explode(",",chop($pi_id_all,','))));

		$rcv_iss_sql="SELECT a.MST_ID, a.TRANSACTION_TYPE, a.JOB_NO, a.STYLE_REF_NO, a.CONS_QUANTITY, a.PROD_ID, b.COLOR, b.YARN_COUNT_ID, b.YARN_COMP_TYPE1ST, b.YARN_COMP_PERCENT1ST, c.REMARKS
		from inv_transaction a, product_details_master b,inv_receive_master c 
		where a.prod_id=b.id and a.pi_wo_batch_no in($pi_id_all) and a.entry_form=248 and a.transaction_type=1 and a.receive_basis=1 and a.mst_id=c.id
		union all
		SELECT a.MST_ID, a.TRANSACTION_TYPE, a.JOB_NO, a.STYLE_REF_NO, a.CONS_QUANTITY, a.PROD_ID, b.COLOR, b.YARN_COUNT_ID, b.YARN_COMP_TYPE1ST, b.YARN_COMP_PERCENT1ST, null as REMARKS
		from inv_transaction a, product_details_master b 
		where  a.prod_id=b.id and a.pi_wo_batch_no in($mst_id_all) and a.entry_form=277 and a.transaction_type=2 and a.receive_basis=10 ";
		// echo $rcv_iss_sql;die;
		$rcv_iss_reslut=sql_select($rcv_iss_sql);
		$rcv_iss_array=array();
		foreach ($rcv_iss_reslut  as $row) 
		{
			$key=$row['JOB_NO'].'_'.$row['COLOR'].'_'.$row['YARN_COUNT_ID'].'_'.$row['YARN_COMP_TYPE1ST'].'_'.$row['YARN_COMP_PERCENT1ST'];
			if($row['TRANSACTION_TYPE']==1)
			{
				$rcv_iss_array[$key]['rcv_qnty']+=$row['CONS_QUANTITY'];	
				$rcv_iss_array[$key]['remarks']=$row['REMARKS'];
				$rcv_iss_array[$key]['rcv_id'].=$row['MST_ID'].',';	
				$rcv_iss_array[$key]['prod_id'].=$row['PROD_ID'].',';	
			}
			else if($row['TRANSACTION_TYPE']==2)
			{
				$rcv_iss_array[$key]['iss_qnty']+=$row['CONS_QUANTITY'];	
			}	
		}
		ob_start();
		// var_dump($rowspan_arr);die;
		?>
		<style>
			.wrd_brk{word-break: break-all;}
			.left{text-align: left;}
			.center{text-align: center;}
			.right{text-align: right;}
		</style>

		<table width="1250" class="rpt_table" rules="all" border="1" id="data_table" style="word-break:break-all;">
			<thead id="table_header_1">
				<tr>
					<th width="60" >WO Date</th>
					<th width="80" >WO No</th>
					<th width="100" >Buyer Name</th>
					<th width="100" >Style No</th>
					<th width="100" >Suppliers Name</th>
					<th width="60" >Dlv. Confirm date</th>
					<th width="100" >AWB NO</th>
					<th width="100" >Yarn Color</th>
					<th width="100" >Yarn Quality</th> 
					<th width="80" >Yarn Count</th> 
					<th width="80" >Yarn WO Qty. (Lbs)</th>
					<th width="80" >Yarn Receive QTY (Lbs)</th>
					<th width="80" >Yarn Issue  QTY (Lbs)</th>
					<th >Yarn Balance (lbs)</th>
				</tr>
			</thead>
			<tbody id="scroll_body" class="rpt_table">
				<? 
				$i=1;
				$array_check_arr=array();
				foreach ($yarn_data_array as $job_key=>$row ) 
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FEFEFE";				
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<?php
							if(!in_array($row['wo_number'].'*'.$row['buyer_id'].'*'.$row['style_no'],$array_check_arr))
							{
								$array_check_arr[]=$row['wo_number'].'*'.$row['buyer_id'].'*'.$row['style_no'];
								$rowspan_count= $rowspan_arr[$row['wo_number'].'*'.$row['buyer_id'].'*'.$row['style_no']];
								?>
									<td class="wrd_brk center" rowspan="<?= $rowspan_count;?>"><? echo change_date_format($row['wo_date']);?></td>
									<td class="wrd_brk" rowspan="<?= $rowspan_count;?>"><? echo $row['wo_number'];?> </td>
									<td class="wrd_brk" rowspan="<?= $rowspan_count;?>"><? echo $buyerArr[$row['buyer_id']];?></td>
									<td class="wrd_brk" rowspan="<?= $rowspan_count;?>"><? echo $row['style_no'];?></td>
									<td class="wrd_brk" rowspan="<?= $rowspan_count;?>"><? echo $supplierArr[$row['supplier_id']];?></td>
									<td class="wrd_brk center" rowspan="<?= $rowspan_count;?>"><? echo change_date_format($row['delivery_date']);?></td>
									<td class="wrd_brk" rowspan="<?= $rowspan_count;?>"><? echo $rcv_iss_array[$job_key]['remarks']; ?> </td>
								<?
							}
						?>
						<td class="wrd_brk" ><? echo $color_name_arr[$row['color_name']];?> </td>
						<td class="wrd_brk" ><? echo $yarn_composition_arr[$row['yarn_comp_type1st']];?></td>
						<td class="wrd_brk" ><? echo $yarn_count_arr[$row['yarn_count']];?></td>
						<td class="wrd_brk right" ><? echo number_format($row['REQ_QUANTITY']);?></td>
						<td class="wrd_brk right" > <a href='#report_details' onClick="openmypage('<? echo $rcv_iss_array[$job_key]['rcv_id']; ?>','<? echo $rcv_iss_array[$job_key]['prod_id'];?>','received_popup');"><? echo number_format($rcv_iss_array[$job_key]['rcv_qnty'],2);?></a> </td>

						<td class="wrd_brk right" ><? echo number_format($rcv_iss_array[$job_key]['iss_qnty'],2);?></td>
						<td class="wrd_brk right" ><? echo number_format($rcv_iss_array[$job_key]['rcv_qnty']-$rcv_iss_array[$job_key]['iss_qnty'],2);?></td>
					</tr>
					<?
					$total_received_qnty=$total_received_amt=$total_issue_qnty=$total_issue_amt=$total_amount=0;	
					$i++;
				}
				?>
			</tbody>
		</table>
		<?
	}
	
    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("$user_id*.xls") as $filename) {
                //if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
            //---------end------------//
    $name = time();
    $filename = $user_id . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$rpt_type";
    exit();
}

if($action=="received_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	?> 
	<fieldset style="width:1070px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="1050" cellpadding="0" cellspacing="0" align="left" >
				<caption>Receive Details </caption>
                <thead>
                    <th width="30">Sl</th>
                    <th width="100">Rcvd. Basis</th>
                    <th width="100">Receive Purpose</th>
                    <th width="110">PI/WO No</th>
                    <th width="110">MRR No</th>
                    <th width="80">MRR Date</th>
                    <th width="90">Yarn Color</th>
                    <th width="120">Yarn Composition</th>
                    <th width="80">Y. Lot</th>
                    <th width="80">MRR Qty.</th>
                    <th >Unit Price</th>
				</thead>
			</table>
			<?
				$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
				$line_arr=return_library_array( "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1", "id", "line_name");
				$floor_arr=return_library_array( "select id,floor_name from lib_prod_floor where is_deleted=0 and status_active=1", "id", "floor_name");
				$po_arr=return_library_array( "select id,po_number from wo_po_break_down where is_deleted=0 and status_active=1", "id", "po_number");

				$i=1;
				$item_color=str_replace("'","",$item_color);
				$item_size=str_replace("'","",$item_size);

				$item_color_con2="";$item_size_con2="";
				if($item_color)
				{
					$item_color_con2=" and d.item_color in($item_color)";
				}
				if($item_size)
				{
					$item_size_con2=" and d.item_size='$item_size'";
				}
				//echo $item_color_con.'=='.$item_size_con;
				$rec_id=chop($rec_id,',');
				$prod_id=chop($prod_id,',');
				$sql = "SELECT a.id, a.company_id, a.supplier_id, a.prod_id, a.item_category, a.store_id, a.job_no, a.buyer_id, a.style_ref_no, a.entry_form, a.receive_basis as basis, a.cons_amount, a.cons_rate, a.order_rate, a.order_qnty,	a.order_amount, a.transaction_type as trans_type, b.id as product_id, b.lot, b.color, b.current_stock, b.yarn_count_id, b.avg_rate_per_unit, b.allocated_qnty, b.available_qnty, b.yarn_comp_type1st, c.exchange_rate as exchange_rate,c.id as rcv_iss_trans_id,c.recv_number, c.receive_date ,a.cons_quantity, a.pi_wo_batch_no, a.cons_uom,c.receive_purpose
				from inv_transaction a, product_details_master b, inv_receive_master c
				where  a.prod_id = b.id and a.mst_id=c.id and a.company_id = $companyID and c.id in($rec_id) and a.prod_id=$prod_id  and a.status_active = 1 and b.status_active = 1 and c.status_active=1 and a.transaction_type in(1) and a.entry_form in(248)";

				$color_arr = return_library_array("select id, color_name from lib_color where status_active = 1 and is_deleted=0", 'id', 'color_name');
				$req_arr = return_library_array("select id, ydw_no from wo_yarn_dyeing_mst where  company_id=$companyID and status_active = 1 and is_deleted=0", 'id', 'ydw_no');
				$pi_arr = return_library_array("select id, pi_number from com_pi_master_details where importer_id=$companyID and status_active = 1 and is_deleted=0", 'id', 'pi_number');
				$wo_arr = return_library_array("select id, wo_number from wo_non_order_info_mst where company_name=$companyID and status_active = 1 and is_deleted=0", 'id', 'wo_number');
				//echo $sql;
				$dtlsArray=sql_select($sql);
			?>
			<table border="1" class="rpt_table" rules="all" width="1050" cellpadding="0" cellspacing="0" align="left" id="list_view">
				<tbody>
					<?
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="100"><p><? echo $receive_basis_arr[$row[csf('basis')]]; ?></p></td>
							<td width="100"><p><? echo $yarn_issue_purpose[$row[csf('receive_purpose')]]; ?></p></td>
							<td width="110"><p><? 
							if ($row[csf('basis')]==2)
							{
								echo $req_arr[$row[csf('pi_wo_batch_no')]];
							}
							else if ($row[csf('basis')]==1) 
							{
								echo $pi_arr[$row[csf('pi_wo_batch_no')]];
							} 
							else
							{
								echo '';
							} 
							?></td>
							<td width="110" style="word-wrap: break-word; word-break: break-all"><? echo $row[csf('recv_number')]; ?></td>
							<td width="80" align="center"><p><? echo change_date_format($row[csf('receive_date')]); ?></td>
							<td width="90" style="word-wrap: break-word; word-break: break-all"><? echo $color_arr[$row[csf('color')]]; ?></td>
							<td width="120" style="word-wrap: break-word; word-break: break-all"><? echo $composition[$row[csf('yarn_comp_type1st')]]; ?></td>
							<td width="80"><? echo $row[csf('lot')]; ?></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('cons_quantity')],2); ?></td>
							<td align="right"><p><? echo $row[csf('cons_rate')]; ?></td>
						</tr>
						<?
						$tot_qty+=$row[csf('cons_quantity')];
						$tot_val+=$row[csf('cons_amount')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="7" align="right"></td>
						<td align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?></td>
						<td>&nbsp;</td>
						<td align="right"><? echo number_format($tot_val,2); ?></td>
						<td></td>
					</tr>
				</tfoot>
			</table>
        </div>
    </fieldset>
    <?
	exit();
}

?>