<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$type=$_REQUEST['type'];
$permission=$_SESSION['page_permission'];
$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name"  );
//----------------------------------------------------Start---------------------------------------------------------
//*************************************************Master Form Start************************************************
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 130, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_buyer_new")
{
	echo create_drop_down( "cbo_new_buyer_id", 150, "SELECT buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/job_copy_full_controller', this.value, 'load_drop_down_season', 'season_td'); " );
	exit();
}

if ($action=="load_variable_settings")
{
	$sql_result = sql_select("select variable_list, season_mandatory, publish_shipment_date from variable_order_tracking where company_name=$data and variable_list in (44,47) and status_active=1 and is_deleted=0 order by variable_list ASC");
	$season_mandatory=$set_smv_id=0;
 	foreach($sql_result as $result)
	{
		if($result[csf('variable_list')]==44) $season_mandatory=$result[csf('season_mandatory')];
		else if($result[csf('variable_list')]==47) $set_smv_id=$result[csf('publish_shipment_date')];
	}
	echo trim($season_mandatory)."_".trim($set_smv_id);
 	exit();
}

if ($action=="job_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value( job_no )
		{
			document.getElementById('selected_job').value=job_no;
			parent.emailwindow.hide();
		}
    </script>
	</head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="760" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <th width="130" class="must_entry_caption">Company Name</th>
                <th width="130">Buyer Name</th>
                <th width="90">Job No</th>
                <th width="90">Order No</th>
                <th colspan="2">Date Range</th>
                <th>&nbsp;</th>
            </thead>
            <tr class="general">
                <td><input type="hidden" id="selected_job">
                    <? echo create_drop_down( "cbo_company_mst", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'job_copy_full_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?>
                </td>
                <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_id", 130, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
                <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:85px"></td>
                <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:85px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:65px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px" placeholder="To Date"></td>
                <td>
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('cbo_year_selection').value+'_'+<? echo $garments_nature;?>, 'create_po_search_list_view', 'search_div', 'job_copy_full_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" /></td>
            </tr>
            <tr>
                <td align="center" valign="middle" colspan="7"><? echo load_month_buttons(1); ?></td>
            </tr>
        </table>
        <div id="search_div"></div>
    </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="create_po_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }

	$gmt_nature=str_replace("'","",$data[7]);
	if ($gmt_nature!=0) $gmt_nature_cond=" and a.garments_nature=$gmt_nature";else $gmt_nature_cond="";
	
	if(str_replace("'","",$data[1])==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer="";
		}
		else $buyer="";
	}
	else $buyer=" and a.buyer_name='$data[1]'";

	//if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($data[6]!=0)
	{
		if($db_type==0) $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[6]"; else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$data[6]";
	}
	else $year_cond="";

	if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num='$data[4]' $year_cond"; else  $job_cond="";
	if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number like '%$data[5]%'  "; else  $order_cond="";
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
		$year_slt="YEAR(a.insert_date)";
	}
	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
		$year_slt="to_char(a.insert_date,'YYYY')";
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$arr=array (2=>$buyer_arr);

	$sql= "select $year_slt as year, a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.job_quantity, b.po_number, b.po_quantity, b.shipment_date, c.id as pre_id from wo_po_details_master a, wo_po_break_down b left join wo_pre_cost_mst c on b.job_no_mst=c.job_no and c.status_active=1 and c.is_deleted=0 where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $gmt_nature_cond $shipment_date $company $buyer $job_cond $order_cond order by a.id DESC";
	//echo $sql;
	echo  create_list_view("list_view", "Year,Job No,Buyer Name,Style Ref.,Job Qty.,PO Number,PO Qty,Shipment Date,Precost Id", "60,60,120,100,90,110,90,80,80","850","320",0, $sql , "js_set_value", "job_no", "", 1, "0,0,buyer_name,0,0,0,0,0,0", $arr , "year,job_no_prefix_num,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date,pre_id", "",'','0,0,0,0,1,0,1,3,0') ;
	exit();
}

if ($action=="populate_data_from_job_table")
{
	$data_array=sql_select("select job_no, company_name, buyer_name, style_ref_no,order_uom,set_smv,set_break_down from wo_po_details_master where job_no='$data' and is_deleted=0 and status_active=1");
	foreach ($data_array as $row)
	{
		echo "load_drop_down( 'requires/job_copy_full_controller', '".$row[csf("company_name")]."', 'load_drop_down_buyer', 'buyer_td' );\n";
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('cbo_company_id').value = '".$row[csf("company_name")]."';\n";
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('cbo_buyer_id').value = '".$row[csf("buyer_name")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('cbo_order_uom').value = '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('tot_smv_qnty').value = '".$row[csf("set_smv")]."';\n";
		echo "document.getElementById('set_breck_down').value = '".$row[csf("set_break_down")]."';\n";

		if($row[csf("order_uom")]==1){
			$ex_set_data=explode('_',$row[csf("set_break_down")]);
			$ex_item_id=$ex_set_data[0];
			echo "document.getElementById('cbo_gmtsItem_id').value = '".$ex_set_data[0]."';\n";
			echo "document.getElementById('tot_smv_qnty').disabled = false;\n";
			echo "document.getElementById('cbo_gmtsItem_id').disabled = false;\n";
		}
		else
		{
			echo "$('#cbo_gmtsItem_id').attr('disabled','true')".";\n";
			echo "$('#tot_smv_qnty').attr('disabled','true')".";\n";
		}
		echo "$('#cbo_order_uom').attr('disabled','true')".";\n";

		echo "$('#cbo_buyer_id').attr('disabled','true')".";\n";
		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
	}
	exit();
}

if ($action=="order_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
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
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
		}
    </script>
<?
	$sql = "select id, po_number, pub_shipment_date, po_quantity, unit_price from wo_po_break_down where job_no_mst='$job_no' and status_active=1 and is_deleted=0 order by id Desc";
	//echo $sql; die;
	echo create_list_view("list_view", "Po ID,Po No,Pub. Ship Date, Po Qty","60,120,80,100","400","200",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "id,po_number,pub_shipment_date,po_quantity", "","setFilterGrid('list_view',-1)","0","",1) ;

	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}

if($action=="save_update_delete_copy_job")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}

	$id=return_next_id( "id", "wo_po_details_master", 1 ) ;
	if(str_replace("'","",$cbo_new_company_id)!=0)
	{
		$new_company_id=$cbo_new_company_id;
		$company_name_file=str_replace("'","",$cbo_new_company_id);
	}
	else
	{
		$new_company_id=$cbo_company_id;
		$company_name_file='company_name';
	}

	if(str_replace("'","",$cbo_new_buyer_id)!=0) $buyer_name_file=str_replace("'","",$cbo_new_buyer_id); else $buyer_name_file='buyer_name';

	if(str_replace("'","",$cbo_season_id)!=0) $season_buyer_wise_file=$cbo_season_id; else $season_buyer_wise_file='season_buyer_wise';
	if(str_replace("'","",$cbo_season_year)!=0) $season_year_file=$cbo_season_year; else $season_year_file='season_year';

	if(str_replace("'","",$txt_new_style_ref)!='') $style_ref_no_file="'".str_replace("'","",$txt_new_style_ref)."'"; else $style_ref_no_file='style_ref_no';

	$po_id=str_replace("'","",$txt_po_id);
	if($po_id=="") $po_id_cond=""; else $po_id_cond=" and id in ($po_id)";

	if(str_replace("'","",$tot_smv_qnty)!='')
	{
		$set_smv_file=$tot_smv_qnty; $smv_pcs_file=$tot_smv_qnty; $smv_set_file=$tot_smv_qnty; $sew_smv_file=$tot_smv_qnty; $smvpre_pcs_file=$tot_smv_qnty; $smvpre_set_file=$tot_smv_qnty;
	}
	else
	{
		$set_smv_file='set_smv'; $sew_smv_file='sew_smv'; $smv_pcs_file='smv_pcs'; $smv_set_file='smv_set'; $smvpre_pcs_file='smv_pcs_precost'; $smvpre_set_file='smv_set_precost';
	}
	$set_breck_down_data="";
	if(str_replace("'","",$hiddn_wsdata)!="")
	{
		$exwsdata=explode(',',str_replace("'","",$hiddn_wsdata));
		foreach($exwsdata as $wsrow)
		{
			$exws=explode('_',$wsrow);

			if($exws[0]=="") $exws[0]=0;
			if($exws[1]=="") $exws[1]=0;
			if($exws[2]=="") $exws[2]=0;
			if($exws[3]=="") $exws[3]=0;
			if($exws[4]=="") $exws[4]=0;

			if($set_breck_down_data=="") $set_breck_down_data=$exws[0].'_1_'.$exws[1].'_'.$exws[1].'_0_0_'.$exws[2].'_'.$exws[2].'_'.$exws[3].'_'.$exws[3].'_0_0_0_0_0_0_0_0_0_'.$exws[4];
			else $set_breck_down_data.='__'.$exws[0].'_1_'.$exws[1].'_'.$exws[1].'_0_0_'.$exws[2].'_'.$exws[2].'_'.$exws[3].'_'.$exws[3].'_0_0_0_0_0_0_0_0_0_'.$exws[4];
		}

		if(str_replace("'","",$cbo_gmtsItem_id)!=0 && str_replace("'","",$cbo_order_uom)==1) $gmts_item_id_file=str_replace("'","",$cbo_gmtsItem_id); else $gmts_item_id_file='gmts_item_id';
		$set_break_down_file="'".$set_breck_down_data."'";
	}
	else
	{
		if(str_replace("'","",$cbo_gmtsItem_id)!=0 && str_replace("'","",$cbo_order_uom)==1)
		{
			$ex_set_data=explode('_',str_replace("'","",$set_breck_down));
			$set_breck_down_data=str_replace("'","",$cbo_gmtsItem_id).'_'.$ex_set_data[1].'_'.str_replace("'","",$tot_smv_qnty).'_'.str_replace("'","",$tot_smv_qnty).'_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0';
			if(str_replace("'","",$cbo_gmtsItem_id)!=0)
			{
				$set_break_down_file="'".$set_breck_down_data."'"; $gmts_item_id_file=str_replace("'","",$cbo_gmtsItem_id);
			}
			else
			{
				$set_break_down_file='set_break_down'; $gmts_item_id_file='gmts_item_id';
			}
		}
		else
		{
			$set_break_down_file='set_break_down'; $gmts_item_id_file='gmts_item_id';
		}
	}
	if($db_type==0)
	{
		$new_job_no=explode("*",return_mrr_number( str_replace("'","",$new_company_id), '', '', date("Y",time()), 5, "select job_no_prefix, job_no_prefix_num from wo_po_details_master where company_name=$new_company_id and YEAR(insert_date)=".date('Y',time())." order by id desc ", "job_no_prefix", "job_no_prefix_num" ));
	}
	else if($db_type==2)
	{
		$new_job_no=explode("*",return_mrr_number( str_replace("'","",$new_company_id), '', '', date("Y",time()), 5, "select job_no_prefix, job_no_prefix_num from wo_po_details_master where company_name=$new_company_id and to_char(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "job_no_prefix", "job_no_prefix_num" ));
	}

	$sql_jobInst="insert into wo_po_details_master( id, garments_nature, job_no_prefix, job_no_prefix_num, job_no, quotation_id, copy_from, order_repeat_no, company_name, buyer_name, style_ref_no, product_dept, product_code, location_name, style_description, ship_mode, region, team_leader, dealing_marchant, remarks, job_quantity, avg_unit_price, currency_id, total_price, packing,  agent_name, product_category, order_uom,  gmts_item_id, set_break_down, total_set_qnty, set_smv, pro_sub_dep, client_id, item_number_id, factory_marchant, season_buyer_wise, season_year,  qlty_label, is_excel, style_owner, booking_meeting_date, bh_merchant, ready_for_budget, working_location_id, gauge, fabric_composition, design_source_id,brand_id, is_deleted, status_active, inserted_by, insert_date)
	select
	$id, garments_nature, '".$new_job_no[1]."', '".$new_job_no[2]."', '".$new_job_no[0]."', 0 as quotation_id, job_no, order_repeat_no, $company_name_file, $buyer_name_file, $style_ref_no_file, product_dept, product_code, location_name, style_description, ship_mode, region, team_leader, dealing_marchant, remarks, job_quantity, avg_unit_price, currency_id, total_price, packing,  agent_name, product_category, order_uom,  $gmts_item_id_file, $set_break_down_file, total_set_qnty, $set_smv_file, pro_sub_dep, client_id, item_number_id, factory_marchant, $season_buyer_wise_file, $season_year_file, qlty_label, is_excel, style_owner, booking_meeting_date, bh_merchant, ready_for_budget, working_location_id, gauge, fabric_composition, design_source_id,brand_id, is_deleted, status_active, ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."' from wo_po_details_master where job_no=$txt_job_no"; //Don't hide 0 as quotation_id issue id ISD-21-01768 comments by kausar

	$job_id=$id;

	//echo "10**".$sql_jobInst;die;

	$rID=execute_query($sql_jobInst,0);
	if($db_type==0)
	{
		if($rID){
		mysql_query("COMMIT");
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID){
		oci_commit($con);
		}
	}
	if(str_replace("'","",$hiddn_wsdata)!="")
	{
		$field_array1="id, job_id, job_no, gmts_item_id, set_item_ratio, smv_pcs, smv_set, smv_pcs_precost, smv_set_precost, complexity, embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, ws_id";
		$add_comma=0;
		$id1=return_next_id( "id", "wo_po_details_mas_set_details", 1 ) ;
		$set_breck_down_array=explode('__',str_replace("'",'',$set_breck_down_data));
		for($c=0;$c < count($set_breck_down_array);$c++)
		{
			$set_breck_down_arr=explode('_',$set_breck_down_array[$c]);
			if ($add_comma!=0) $data_array1 .=",";
			if($set_breck_down_arr[2]==0 || $set_breck_down_arr[2]==''){
				echo "SMV**";
				disconnect($con);die;
			}
			$data_array1 .="(".$id1.",".$job_id.",'".$new_job_no[0]."','".$set_breck_down_arr[0]."','".$set_breck_down_arr[1]."','".$set_breck_down_arr[2]."','".$set_breck_down_arr[3]."','".$set_breck_down_arr[2]."','".$set_breck_down_arr[3]."','".$set_breck_down_arr[4]."','".$set_breck_down_arr[5]."','".$set_breck_down_arr[6]."','".$set_breck_down_arr[7]."','".$set_breck_down_arr[8]."','".$set_breck_down_arr[9]."','".$set_breck_down_arr[10]."','".$set_breck_down_arr[11]."','".$set_breck_down_arr[12]."','".$set_breck_down_arr[13]."','".$set_breck_down_arr[14]."','".$set_breck_down_arr[15]."','".$set_breck_down_arr[16]."','".$set_breck_down_arr[17]."','".$set_breck_down_arr[18]."','".$set_breck_down_arr[19]."')";
			$add_comma++;
			$id1=$id1+1;
		}
		$rID1=sql_insert("wo_po_details_mas_set_details",$field_array1,$data_array1,1);
	}
	else
	{
		$sql_se_set=sql_select("select id from wo_po_details_mas_set_details  where job_no=$txt_job_no order by id ASC");
		foreach($sql_se_set as $row_se_set)
		{
			$id_set=return_next_id( "id", "wo_po_details_mas_set_details", 1 ) ;

			$sql_insert_set="insert into  wo_po_details_mas_set_details(id, job_id, job_no, gmts_item_id, set_item_ratio, smv_pcs, smv_set, smv_pcs_precost, smv_set_precost, complexity, embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, quot_id, aop, aopseq, ws_id)
			select $id_set, $job_id, '".$new_job_no[0]."', $gmts_item_id_file, set_item_ratio, $smv_pcs_file, $smv_set_file, $smvpre_pcs_file, $smvpre_set_file, complexity, embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, quot_id, aop, aopseq, ws_id from  wo_po_details_mas_set_details where job_no=$txt_job_no and id=".$row_se_set[csf('id')]."";
			$rID1=execute_query($sql_insert_set,0);
			
		}
	}
	//echo "10**".$sql_insert_set; die;

	if($db_type==0)
	{
		if($rID1){
		mysql_query("COMMIT");
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID1){
		oci_commit($con);
		}
	}

	$po_id_maping_array=array();
	$sql_se_po=sql_select("select id from wo_po_break_down  where job_no_mst=$txt_job_no and is_deleted=0 and status_active=1 $po_id_cond order by id ASC");
	foreach($sql_se_po as $row_se_po)
	{
		$id_po=return_next_id( "id", "wo_po_break_down", 1 ) ;

		$sql_poInst="insert into  wo_po_break_down(id, job_id, job_no_mst, po_number, pub_shipment_date, excess_cut, po_received_date, po_quantity, unit_price, plan_cut, country_name, po_total_price, shipment_date, t_year, t_month, is_confirmed, details_remarks, delay_for, packing, grouping, projected_po_id, tna_task_from_upto, shiping_status, original_po_qty, factory_received_date, original_avg_price, pp_meeting_date, file_no_prev, file_no, matrix_type, no_of_carton, actual_po_no, round_type, doc_sheet_qty, up_charge, status_active, is_deleted, inserted_by, insert_date)
		select $id_po, $job_id, '".$new_job_no[0]."', po_number, pub_shipment_date, excess_cut, po_received_date, po_quantity, unit_price, plan_cut, country_name, po_total_price, shipment_date, t_year, t_month, is_confirmed, details_remarks, delay_for, packing, grouping, projected_po_id, tna_task_from_upto, 1, original_po_qty, factory_received_date, original_avg_price, pp_meeting_date, file_no_prev, file_no, matrix_type, no_of_carton, actual_po_no, round_type, doc_sheet_qty, up_charge, status_active, is_deleted, ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."' from wo_po_break_down where job_no_mst=$txt_job_no and id=".$row_se_po[csf('id')]." and is_deleted=0 and status_active=1";

		$rID2=execute_query($sql_poInst,0);
		$po_id_maping_array[$row_se_po[csf('id')]]=$id_po;
		//=========================================================================PO END======================================
		$color_mst=return_library_array( "select color_mst_id, color_number_id from wo_po_color_size_breakdown where po_break_down_id=".$id_po." and status_active=1 and is_deleted=0 and color_mst_id !=0", "color_number_id", "color_mst_id" );
		$size_mst=return_library_array( "select size_mst_id, size_number_id from wo_po_color_size_breakdown where po_break_down_id=".$id_po." and status_active=1 and is_deleted=0 and size_mst_id !=0", "size_number_id", "size_mst_id" );
		$item_mst=return_library_array( "select item_mst_id, item_number_id from wo_po_color_size_breakdown where po_break_down_id=".$id_po." and status_active=1 and is_deleted=0 and item_mst_id !=0", "item_number_id", "item_mst_id" );
		$i=1;
		$data_array=array();
		$id_co=return_next_id( "id", "wo_po_color_size_breakdown", 1 ) ;

		$field_array="id, job_id, po_break_down_id, job_no_mst, color_mst_id, size_mst_id, item_mst_id, country_mst_id, article_number, item_number_id, country_id, size_number_id, color_number_id, order_quantity, order_rate, order_total, excess_cut_perc, plan_cut_qnty, is_used, is_locked, cutup_date, cutup, country_ship_date, shiping_status, country_remarks, country_type, packing, color_order, size_order, ultimate_country_id, code_id, ul_country_code, pack_qty, pcs_per_pack, pack_type, pcs_pack, status_active, is_deleted, inserted_by, insert_date";

		$sql_se_co=sql_select("select id, po_break_down_id, job_no_mst, color_mst_id, size_mst_id, item_mst_id, country_mst_id, article_number, item_number_id, country_id, size_number_id, color_number_id, order_quantity, order_rate, order_total, excess_cut_perc, plan_cut_qnty, is_used, is_locked, cutup_date, cutup, country_ship_date, shiping_status, country_remarks, country_type, packing, color_order, size_order, ultimate_country_id, code_id, ul_country_code, pack_qty, pcs_per_pack, pack_type, pcs_pack, status_active, is_deleted, inserted_by, insert_date from wo_po_color_size_breakdown where job_no_mst=$txt_job_no and po_break_down_id=".$row_se_po[csf('id')]." and is_deleted=0 and status_active=1");
		foreach($sql_se_co as $rows)
		{
			//item_number_id
			if(str_replace("'","",$cbo_gmtsItem_id)!=0 && str_replace("'","",$cbo_order_uom)==1)
			{
				$item_number_id=str_replace("'","",$cbo_gmtsItem_id);
			}
			else
			{
				$item_number_id=$rows[csf('item_number_id')];
			}

			if (array_key_exists($item_number_id,$item_mst))
			{
				$item_mst_id=$item_mst[$item_number_id];
			}
			else
			{
				$item_mst[$item_number_id]=$id_co;
				$item_mst_id=$id_co;
			}

			if(array_key_exists($rows[csf('color_number_id')],$color_mst))
			{
				$color_mst_id=$color_mst[$rows[csf('color_number_id')]];
			}
			else
			{
				$color_mst[$rows[csf('color_number_id')]]=$id_co;
				$color_mst_id=$id_co;
			}

			if(array_key_exists($rows[csf('size_number_id')],$size_mst))
			{
				$size_mst_id=$size_mst[$rows[csf('size_number_id')]];
			}
			else
			{
				$size_mst[$rows[csf('size_number_id')]]=$id_co;
				$size_mst_id=$id_co;
			}

			$data_array[$id_co]="(".$id_co.",".$job_id.",".$id_po.",'".$new_job_no[0]."','".$color_mst_id."','".$size_mst_id."','".$item_mst_id."',0,'".$rows[csf('article_number')]."','".$item_number_id."','".$rows[csf('country_id')]."','".$rows[csf('size_number_id')]."','".$rows[csf('color_number_id')]."','".$rows[csf('order_quantity')]."','".$rows[csf('order_rate')]."','".$rows[csf('order_total')]."','".$rows[csf('excess_cut_perc')]."','".$rows[csf('plan_cut_qnty')]."','".$rows[csf('is_used')]."','".$rows[csf('is_locked')]."','".$rows[csf('cutup_date')]."','".$rows[csf('cutup')]."','".$rows[csf('country_ship_date')]."',0,'".$rows[csf('country_remarks')]."','".$rows[csf('country_type')]."','".$rows[csf('packing')]."','".$rows[csf('color_order')]."','".$rows[csf('size_order')]."','".$rows[csf('ultimate_country_id')]."','".$rows[csf('code_id')]."','".$rows[csf('ul_country_code')]."','".$rows[csf('pack_qty')]."','".$rows[csf('pcs_per_pack')]."','".$rows[csf('pack_type')]."','".$rows[csf('pcs_pack')]."','".$rows[csf('status_active')]."','".$rows[csf('is_deleted')]."','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
			$id_co=$id_co+1;
			$i++;
		}
		//echo $data_array; die;
		//echo "0**"."insert into wo_po_color_size_breakdown (".$field_array.") values ".$data_array; die;
		$roll_back_msg="Data not save.";
		$data_array_chnk=array_chunk($data_array,50);
		foreach( $data_array_chnk as $rows)
		{
			$rID3.=sql_insert("wo_po_color_size_breakdown",$field_array, implode(",",$rows),0);
			if($rID3==1) $flag=1; //else $flag=0;
			else if($rID3==0)
			{
				if($db_type==2)
				{
					$flag=0;
					oci_rollback($con);
					echo "10**".$roll_back_msg; disconnect($con);die;
				}
				else if($db_type==0)
				{
					$flag=0;
					mysql_query("COMMIT");
					echo "10**".$roll_back_msg; disconnect($con);die;
				}
			}

		}

		//$rID3=sql_insert("wo_po_color_size_breakdown",$field_array,$data_array,0);
		if($db_type==0)
		{
			if($flag==1){
			mysql_query("COMMIT");
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
			oci_commit($con);
			}
		}

		$data_ratio=''; $k=1;
		$field_ratio="id, job_id, po_id, country_id, gmts_item_id, country_ship_date, color_id, size_id, ratio_qty, ratio_rate, status_active, is_deleted, ultimate_country_id, code_id, ul_country_code, inserted_by, insert_date";
		$id_ratio=return_next_id( "id", "wo_po_ratio_breakdown", 1) ;

		$sql_ratio=sql_select("Select id, job_id, po_id, country_id, gmts_item_id, country_ship_date, color_id, size_id, ratio_qty, ratio_rate, status_active, is_deleted, ultimate_country_id, code_id, ul_country_code from wo_po_ratio_breakdown where po_id=".$row_se_po[csf('id')]." and status_active=1 and is_deleted=0");

		foreach($sql_ratio as $rowd)
		{
			//item_number_id
			if(str_replace("'","",$cbo_gmtsItem_id)!=0 && str_replace("'","",$cbo_order_uom)==1)
			{
				$gmts_item_id=str_replace("'","",$cbo_gmtsItem_id);
			}
			else
			{
				$gmts_item_id=$rowd[csf('gmts_item_id')];
			}

			if ($k!=1) $data_ratio .=",";
			$data_ratio .="(".$id_ratio.",".$id.",".$id_po.",'".$rowd[csf('country_id')]."','".$gmts_item_id."','".$rowd[csf('country_ship_date')]."','".$rowd[csf('color_id')]."','".$rowd[csf('size_id')]."','".$rowd[csf('ratio_qty')]."','".$rowd[csf('ratio_rate')]."','".$rowd[csf('status_active')]."','".$rowd[csf('is_deleted')]."','".$rowd[csf('ultimate_country_id')]."','".$rowd[csf('code_id')]."','".$rowd[csf('ul_country_code')]."','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
			$id_ratio=$id_ratio+1;
			$k++;
		}
		$rIDRatio=sql_insert("wo_po_ratio_breakdown",$field_ratio,$data_ratio,0);
		if($db_type==0)
		{
			if($rIDRatio){
			mysql_query("COMMIT");
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rIDRatio){
			oci_commit($con);
			}
		}

		$data_destination=''; $l=1;
		$field_dstination="id, po_id, item_id, country_id, country_ship_date, ultimate_country_id, color_id, destination_id, destination_qty, status_active, is_deleted, ul_country_code, code_id, inserted_by, insert_date";
		$id_des=return_next_id( "id", "wo_po_destination_info", 1) ;

		$sql_destination=sql_select("Select id, po_id, item_id, country_id, country_ship_date, ultimate_country_id, color_id, destination_id, destination_qty, status_active, is_deleted, ul_country_code, code_id from wo_po_destination_info where po_id=".$row_se_po[csf('id')]." and status_active=1 and is_deleted=0");

		foreach($sql_destination as $rowd)
		{
			//item_number_id
			if(str_replace("'","",$cbo_gmtsItem_id)!=0 && str_replace("'","",$cbo_order_uom)==1)
			{
				$item_id=str_replace("'","",$cbo_gmtsItem_id);
			}
			else
			{
				$item_id=$rows[csf('item_id')];
			}

			if ($l!=1) $data_destination .=",";
			$data_destination .="(".$id_des.",".$id_po.",'".$item_id."','".$rows[csf('country_id')]."','".$rows[csf('country_ship_date')]."','".$rows[csf('ultimate_country_id')]."','".$rows[csf('color_id')]."','".$rows[csf('destination_id')]."','".$rows[csf('destination_qty')]."','".$rows[csf('status_active')]."','".$rows[csf('is_deleted')]."','".$rows[csf('ul_country_code')]."','".$rows[csf('code_id')]."','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
			$id_des=$id_des+1;
			$l++;
		}

		$rIDDes=sql_insert("wo_po_destination_info",$field_dstination,$data_destination,0);
		if($db_type==0)
		{
			if($rIDDes){
			mysql_query("COMMIT");
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rIDDes){
			oci_commit($con);
			}
		}

		//=====================================Color Size Break Down End=======================
		$data_array_sm="";
		$sam=1;
		$id_sm=return_next_id( "id", "wo_po_sample_approval_info", 1) ;
		$cbosampletype=return_field_value( 'id', 'lib_sample', 'sample_type=2 and status_active=1 and is_deleted=0');
		$field_array_sm="id, job_no_mst, po_break_down_id, color_number_id, sample_type_id, status_active, is_deleted";
		$data_array_sample=sql_select("select a.id as po_id, b.color_number_id, min(b.id) as color_size_table_id from wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.job_no_mst='".$new_job_no[0]."' and b.color_mst_id!=0 and a.id=b.po_break_down_id and b.po_break_down_id='$id_po' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,b.color_number_id order by a.id");
		foreach ( $data_array_sample as $rowSam )
		{
			if ($sam!=1) $data_array_sm .=",";
			$data_array_sm .="(".$id_sm.",'".$new_job_no[0]."',".$rowSam[csf('po_id')].",".$rowSam[csf('color_size_table_id')].",'".$cbosampletype."',1,0)";
			$id_sm=$id_sm+1;
			$sam=$sam+1;
		}
		$rID4=sql_insert("wo_po_sample_approval_info",$field_array_sm,$data_array_sm,1);

	if($db_type==0)
	{
		if($rID4){
		mysql_query("COMMIT");
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID4){
		oci_commit($con);
		}
	}

		//LabDip Approval
		$data_array_lap="";
		 $lap=1;
		 $id_lap=return_next_id( "id", "wo_po_lapdip_approval_info", 1 ) ;
		 $field_array_lap="id,job_no_mst,po_break_down_id,color_name_id,status_active,is_deleted";
		 foreach ( $data_array_sample as $row_lap1 )
		 {
			  if ($lap!=1) $data_array_lap .=",";
			  $data_array_lap .="(".$id_lap.",'".$new_job_no[0]."',".$row_lap1[csf('po_id')].",".$row_lap1[csf('color_number_id')].",1,0)";
			  $id_lap=$id_lap+1;
			  $lap=$lap+1;
		 }
	 	$rID_lab=sql_insert("wo_po_lapdip_approval_info",$field_array_lap,$data_array_lap,1);

	}
	if($db_type==0)
	{
		if($rID_lab){
		mysql_query("COMMIT");
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID_lab){
		oci_commit($con);
		}
	}
	//txt_costing_date
	$id_pre_mst=return_next_id( "id", "wo_pre_cost_mst", 1 ) ;
	$sql_insert_pre_mst="insert into wo_pre_cost_mst (id, job_id, garments_nature, job_no, costing_date, incoterm, incoterm_place, machine_line, prod_line_hr, costing_per, remarks, copy_quatation, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, efficiency_wastage_percent, ready_to_approved, budget_minute, entry_from, inserted_by, insert_date, status_active, is_deleted)
	select
$id_pre_mst, $job_id, garments_nature, '".$new_job_no[0]."', ".$txt_costing_date.", incoterm, incoterm_place, machine_line, prod_line_hr, costing_per, remarks, copy_quatation, cm_cost_predefined_method_id, exchange_rate, $sew_smv_file, cut_smv, '', cut_effi_percent, efficiency_wastage_percent, 2, budget_minute, entry_from, ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."', status_active, is_deleted from wo_pre_cost_mst where job_no=$txt_job_no ";

	$rID5=execute_query($sql_insert_pre_mst,0);
	if($db_type==0)
	{
		if($rID5){
		mysql_query("COMMIT");
		}
	}
	else if( $db_type==2 || $db_type==1 )
	{
		if($rID5){
		oci_commit($con);
		}
	}

	$id_pre_dtls=return_next_id( "id", "wo_pre_cost_dtls", 1 ) ;

	$sql_insert_pre_dtls="insert into wo_pre_cost_dtls (id, job_id, job_no, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, commission, commission_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, cost_pcs_set, cost_pcs_set_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, depr_amor_pre_cost, depr_amor_po_price, interest_cost, interest_percent, incometax_cost, incometax_percent, deffdlc_cost, deffdlc_percent, design_cost, design_percent, studio_cost, studio_percent, inserted_by, insert_date, status_active, is_deleted)
	select
$id_pre_dtls, $job_id, '".$new_job_no[0]."', costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, commission, commission_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, cost_pcs_set, cost_pcs_set_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, depr_amor_pre_cost, depr_amor_po_price, interest_cost, interest_percent, incometax_cost, incometax_percent, deffdlc_cost, deffdlc_percent, design_cost, design_percent, studio_cost, studio_percent, ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."', status_active, is_deleted from wo_pre_cost_dtls where job_no=$txt_job_no and is_deleted=0";

	$rID6=execute_query($sql_insert_pre_dtls,0);
	if($db_type==0)
	{
		if($rID6){
		mysql_query("COMMIT");
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID6){
		oci_commit($con);
		}
	}

		//item_number_id
	if(str_replace("'","",$cbo_gmtsItem_id)!=0 && str_replace("'","",$cbo_order_uom)==1)
	{
		$item_number_id_fill=str_replace("'","",$cbo_gmtsItem_id);
	}
	else
	{
		$item_number_id_fill='item_number_id';
	}

	$fabric_cost_id_maping=array();
	$sql_se_fabric=sql_select("select id from wo_pre_cost_fabric_cost_dtls where job_no=$txt_job_no order by id ASC");
	foreach($sql_se_fabric as $row_se_fabric)
	{
		$wo_pre_cost_fabric_cost_dtls_id=return_next_id( "id", "wo_pre_cost_fabric_cost_dtls", 1 ) ;
		$sql_insert_fabric="insert into  wo_pre_cost_fabric_cost_dtls (id, job_id, job_no, item_number_id, body_part_id, fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, color_size_sensitive, color, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, company_id, costing_per, consumption_basis, process_loss_method, cons_breack_down, msmnt_break_down, color_break_down, yarn_breack_down, marker_break_down, width_dia_type, avg_cons_yarn, gsm_weight_yarn, plan_cut_qty, job_plan_cut_qty, is_apply_last_update, uom, body_part_type, nominated_supp,gsm_weight_type, sample_id, inserted_by, insert_date, status_active, is_deleted,budget_on)
		select $wo_pre_cost_fabric_cost_dtls_id, $job_id, '".$new_job_no[0]."', $item_number_id_fill, body_part_id, fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, color_size_sensitive, color, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, company_id, costing_per, consumption_basis, process_loss_method, cons_breack_down, msmnt_break_down, color_break_down, yarn_breack_down, marker_break_down, width_dia_type, avg_cons_yarn, gsm_weight_yarn, plan_cut_qty, job_plan_cut_qty, is_apply_last_update, uom, body_part_type, nominated_supp,gsm_weight_type, sample_id, ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."', status_active, is_deleted,budget_on from wo_pre_cost_fabric_cost_dtls where job_no=$txt_job_no and id=".$row_se_fabric[csf('id')]." and is_deleted=0";

		$rID7=execute_query($sql_insert_fabric,0);
		$fabric_cost_id_maping[$row_se_fabric[csf('id')]]=$wo_pre_cost_fabric_cost_dtls_id;
	}
	if($db_type==0)
	{
		if($rID7){
		mysql_query("COMMIT");
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID7){
		oci_commit($con);
		}
	}
	$po_id='';
	//echo "0**";
	//$po_id=return_field_value("id","wo_po_break_down","job_no_mst='".$new_job_no[0]."'","id");
	$pid_sql=sql_select("select id from wo_po_break_down where job_no_mst='".$new_job_no[0]."' and status_active=1 and is_deleted=0");
	foreach($pid_sql as $pidr)
	{
		if($po_id=="") $po_id=$pidr[csf('id')]; else $po_id.=','.$pidr[csf('id')];
	}
	//echo $po_id;

	//$po_id=str_replace("'","",$txt_po_id);
	if($po_id=="") $po_id_cond=""; else $po_id_cond=" and b.id in ($po_id)";
	$array_color_size_table_id=array();

	//echo "select b.id, c.item_number_id, c.color_number_id, c.size_number_id, min(c.id) as color_size_table_id from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and b.job_no_mst='".$new_job_no[0]."' and a.status_active=1 and b.status_active=1 and c.status_active=1 $po_id_cond group by b.id, c.item_number_id, c.color_number_id, c.size_number_id order by b.id";
	$sql_color_size_table_id=sql_select("select b.id, c.item_number_id, c.color_number_id, c.size_number_id, min(c.id) as color_size_table_id from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and b.job_no_mst='".$new_job_no[0]."' and a.status_active=1 and b.status_active=1 and c.status_active=1 $po_id_cond group by b.id, c.item_number_id, c.color_number_id, c.size_number_id order by b.id");
	foreach($sql_color_size_table_id as $row_color_size_table_id)
	{
		$array_color_size_table_id[$row_color_size_table_id[csf('id')]][$row_color_size_table_id[csf('item_number_id')]][$row_color_size_table_id[csf('color_number_id')]][$row_color_size_table_id[csf('size_number_id')]]=$row_color_size_table_id[csf('color_size_table_id')];
	}

	//print_r($array_color_size_table_id);
	//echo '0**'."select a.item_number_id, b.id, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.color_number_id, b.gmts_sizes from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b where a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and a.job_no=$txt_job_no $po_id_cond"; die;
	$po_id=str_replace("'","",$txt_po_id);
	if($po_id=="") $po_id_cond=""; else $po_id_cond=" and b.po_break_down_id in ($po_id)";
	if($po_id=="") $po_id_cond2=""; else $po_id_cond2=" and po_break_down_id in ($po_id)";
	$sql_se_fabric_avg=sql_select("select a.item_number_id, b.id, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.color_number_id, b.gmts_sizes from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b where a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and a.job_no=$txt_job_no $po_id_cond");
	foreach($sql_se_fabric_avg as $row_se_fabric_avg)
	{
		$item_id_cond=0;
		if(str_replace("'","",$cbo_gmtsItem_id)!=0)
		{
			$item_id_cond=str_replace("'","",$cbo_gmtsItem_id);
		}
		else
		{
			$item_id_cond=$row_se_fabric_avg[csf('item_number_id')];
		}
		$color_size_table_id=$array_color_size_table_id[$po_id_maping_array[$row_se_fabric_avg[csf('po_break_down_id')]]][$item_id_cond][$row_se_fabric_avg[csf('color_number_id')]][$row_se_fabric_avg[csf('gmts_sizes')]];

		$wo_pre_cos_fab_co_avg_con_dtls_id=return_next_id( "id", "wo_pre_cos_fab_co_avg_con_dtls", 1) ;
		$sql_insert_fabric_avg="insert into wo_pre_cos_fab_co_avg_con_dtls (id, job_id, pre_cost_fabric_cost_dtls_id, job_no, po_break_down_id, color_number_id, gmts_sizes, dia_width, item_size, cons, process_loss_percent, requirment, pcs, color_size_table_id, body_length, body_sewing_margin, body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin, front_rise_length, front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin, total, marker_dia, marker_yds, marker_inch, gmts_pcs, marker_length, net_fab_cons, rate, amount, length, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, length_sleeve, width_sleeve )
		select $wo_pre_cos_fab_co_avg_con_dtls_id, $job_id, ".$fabric_cost_id_maping[$row_se_fabric_avg[csf('pre_cost_fabric_cost_dtls_id')]].", '".$new_job_no[0]."', ".$po_id_maping_array[$row_se_fabric_avg[csf('po_break_down_id')]].", color_number_id, gmts_sizes, dia_width, item_size, cons, process_loss_percent, requirment, pcs,'".$color_size_table_id."', body_length, body_sewing_margin, body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin, front_rise_length, front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin, total, marker_dia, marker_yds, marker_inch, gmts_pcs, marker_length, net_fab_cons, rate, amount, length, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, length_sleeve, width_sleeve from wo_pre_cos_fab_co_avg_con_dtls where job_no=$txt_job_no and id=".$row_se_fabric_avg[csf('id')]."";
		//echo '0**'.$color_size_table_id.'='; //die;
		$rID8=execute_query($sql_insert_fabric_avg,0);
	}
	if($db_type==0)
	{
		if($rID8){
		mysql_query("COMMIT");
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID8){
		oci_commit($con);
		}
	}
	//echo '0**'.$sql_insert_fabric_avg; die;
	//die;
	$sql_se_fabric_color=sql_select("select id,pre_cost_fabric_cost_dtls_id from wo_pre_cos_fab_co_color_dtls  where job_no=$txt_job_no order by id ASC");
	foreach($sql_se_fabric_color as $row_se_fabric_color)
	{
		$wo_pre_cos_fab_co_color_dtls_id=return_next_id( "id", "wo_pre_cos_fab_co_color_dtls", 1) ;
  $sql_insert_fabric_color="insert into wo_pre_cos_fab_co_color_dtls(id, job_id, pre_cost_fabric_cost_dtls_id, job_no, gmts_color_id, gmts_color, contrast_color_id)
		select $wo_pre_cos_fab_co_color_dtls_id, $job_id, ".$fabric_cost_id_maping[$row_se_fabric_color[csf('pre_cost_fabric_cost_dtls_id')]].", '".$new_job_no[0]."', gmts_color_id, gmts_color, contrast_color_id from  wo_pre_cos_fab_co_color_dtls where job_no=$txt_job_no and id=".$row_se_fabric_color[csf('id')]."";
		$rID9=execute_query($sql_insert_fabric_color,0);
	}
    if($db_type==0)
	{
		if($rID9){
		mysql_query("COMMIT");
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID9){
		oci_commit($con);
		}
	}

	$sql_stripe_color=sql_select("select id,pre_cost_fabric_cost_dtls_id from wo_pre_stripe_color  where job_no=$txt_job_no order by id ASC");
	foreach($sql_stripe_color as $row_stripe_color)
	{
		$wo_pre_cos_stripe_id=return_next_id( "id", "wo_pre_stripe_color", 1) ;
  		$sql_insert_stripe_color="insert into wo_pre_stripe_color (id, job_id, job_no, item_number_id, pre_cost_fabric_cost_dtls_id, color_number_id, stripe_color, measurement, uom, inserted_by, insert_date, status_active, is_deleted, totfidder, fabreq, fabreqtotkg, yarn_dyed, sales_dtls_id, size_number_id, po_break_down_id, lenth, width, sample_color, sample_per, cons, excess_per)
		select $wo_pre_cos_stripe_id, $job_id, '".$new_job_no[0]."', $item_number_id_fill, ".$fabric_cost_id_maping[$row_stripe_color[csf('pre_cost_fabric_cost_dtls_id')]].",  color_number_id, stripe_color, measurement, uom, ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."', status_active, is_deleted, totfidder, fabreq, fabreqtotkg, yarn_dyed, sales_dtls_id, size_number_id, po_break_down_id, lenth, width, sample_color, sample_per, cons, excess_per from  wo_pre_stripe_color where job_no=$txt_job_no and id=".$row_stripe_color[csf('id')]." and status_active=1 and is_deleted=0";
		//echo $sql_insert_stripe_color; die;
		$rID22=execute_query($sql_insert_stripe_color,0);
	}
    if($db_type==0)
	{
		if($rID22){
		mysql_query("COMMIT");
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID22){
		oci_commit($con);
		}
	}

	$sql_se_fabric_yarn=sql_select("select id, fabric_cost_dtls_id from wo_pre_cost_fab_yarn_cost_dtls where job_no=$txt_job_no order by id ASC");
	foreach($sql_se_fabric_yarn as $row_se_fabric_yarn)
	{
		$wo_pre_cost_fab_yarn_cost_dtls_id=return_next_id( "id", "wo_pre_cost_fab_yarn_cost_dtls", 1 ) ;

  		$sql_insert_fabric_yarn="insert into  wo_pre_cost_fab_yarn_cost_dtls (id, job_id, fabric_cost_dtls_id, job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, avg_cons_qnty, supplier_id, color, consdznlbs, rate_dzn, inserted_by, insert_date, status_active, is_deleted)
		select $wo_pre_cost_fab_yarn_cost_dtls_id, $job_id, ".$fabric_cost_id_maping[$row_se_fabric_yarn[csf('fabric_cost_dtls_id')]].", '".$new_job_no[0]."', count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, avg_cons_qnty, supplier_id, color, consdznlbs, rate_dzn, ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."', status_active, is_deleted from wo_pre_cost_fab_yarn_cost_dtls where job_no=$txt_job_no and id=".$row_se_fabric_yarn[csf('id')]." and is_deleted=0";
		$rID10=execute_query($sql_insert_fabric_yarn,0);
	}
    if($db_type==0)
	{
		if($rID10){
		mysql_query("COMMIT");
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID10){
		oci_commit($con);
		}
	}

	$sql_se_fabric_yarn_b=sql_select("select id, fabric_cost_dtls_id from wo_pre_cost_fab_yarnbreakdown  where job_no=$txt_job_no order by id ASC");
	foreach($sql_se_fabric_yarn_b as $row_se_fabric_yarn_b)
	{
		$wo_pre_cost_fab_yarnbreakdown_id=return_next_id( "id", "wo_pre_cost_fab_yarnbreakdown", 1 ) ;
  		$sql_insert_fabric_yarn_b="insert into  wo_pre_cost_fab_yarnbreakdown ( id, job_id, fabric_cost_dtls_id, job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, avg_cons_qnty, supplier_id, color, inserted_by, insert_date, status_active, is_deleted)
		select $wo_pre_cost_fab_yarnbreakdown_id, $job_id, ".$fabric_cost_id_maping[$row_se_fabric_yarn_b[csf('fabric_cost_dtls_id')]].", '".$new_job_no[0]."', count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, avg_cons_qnty, supplier_id, color, ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."', status_active, is_deleted from wo_pre_cost_fab_yarnbreakdown where job_no=$txt_job_no and id=".$row_se_fabric_yarn_b[csf('id')]."";
		$rID11=execute_query($sql_insert_fabric_yarn_b,0);
	}
    if($db_type==0)
	{
		if($rID11){
		mysql_query("COMMIT");
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID11){
		oci_commit($con);
		}
	}

	$sql_se_fabric_conver=sql_select("select id, fabric_description from wo_pre_cost_fab_conv_cost_dtls where job_no=$txt_job_no order by id ASC");
	foreach($sql_se_fabric_conver as $row_se_fabric_conver)
	{
		$wo_pre_cost_fab_conv_cost_dtls_id=return_next_id( "id", "wo_pre_cost_fab_conv_cost_dtls", 1) ;

  		$sql_insert_fabric_conver="insert into  wo_pre_cost_fab_conv_cost_dtls (id, job_id, job_no, fabric_description, cons_process, req_qnty, charge_unit, amount, color_break_down, charge_lib_id, avg_req_qnty, process_loss, inserted_by, insert_date, status_active, is_deleted)
		select $wo_pre_cost_fab_conv_cost_dtls_id, $job_id, '".$new_job_no[0]."', ".$fabric_cost_id_maping[$row_se_fabric_conver[csf('fabric_description')]].", cons_process, req_qnty, charge_unit, amount, color_break_down, charge_lib_id, avg_req_qnty, process_loss, ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."', status_active, is_deleted from  wo_pre_cost_fab_conv_cost_dtls where job_no=$txt_job_no and id=".$row_se_fabric_conver[csf('id')]." and is_deleted=0";
		$rID12=execute_query($sql_insert_fabric_conver,0);
	}
    if($db_type==0)
	{
		if($rID12){
		mysql_query("COMMIT");
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID12){
		oci_commit($con);
		}
	}

	$trim_table_id_maping=array();
	$sql_se_trim=sql_select("select id from wo_pre_cost_trim_cost_dtls  where job_no=$txt_job_no order by id asc");
	foreach($sql_se_trim as $row_se_trim)
	{
		$wo_pre_cost_trim_cost_dtls_id=return_next_id( "id", "wo_pre_cost_trim_cost_dtls", 1 ) ;

        $sql_insert_fabric_trim="insert into  wo_pre_cost_trim_cost_dtls (id, job_id, job_no, trim_group, description, brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req,tot_cons,ex_per, nominated_supp, nominated_supp_multi, cons_breack_down, remark, country, seq, calculatorstring, unit_price, inco_term, add_price, inserted_by, insert_date, status_active, is_deleted)
		select $wo_pre_cost_trim_cost_dtls_id, $job_id, '".$new_job_no[0]."', trim_group, description, brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req,tot_cons,ex_per,nominated_supp, nominated_supp_multi, cons_breack_down, remark, country, seq, calculatorstring, unit_price, inco_term, add_price, ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."', status_active, is_deleted from wo_pre_cost_trim_cost_dtls where job_no=$txt_job_no and id=".$row_se_trim[csf('id')]." and is_deleted=0";
		$rID13=execute_query($sql_insert_fabric_trim,0);
		$trim_table_id_maping[$row_se_trim[csf('id')]]=$wo_pre_cost_trim_cost_dtls_id;
	}

	//item_number_id
	if(str_replace("'","",$cbo_gmtsItem_id)!=0 && str_replace("'","",$cbo_order_uom)==1)
	{
		$item_number_id_fill=str_replace("'","",$cbo_gmtsItem_id);
	}
	else
	{
		$item_number_id_fill='item_number_id';
	}

	$sql_se_trim_cons=sql_select("select id, wo_pre_cost_trim_cost_dtls_id, po_break_down_id, item_number_id, color_number_id, size_number_id from wo_pre_cost_trim_co_cons_dtls b where job_no=$txt_job_no $po_id_cond2 order by id asc");
	foreach($sql_se_trim_cons as $row_se_trim_cons)
	{
		//item_number_id
		if(str_replace("'","",$cbo_gmtsItem_id)!=0 && str_replace("'","",$cbo_order_uom)==1)
		{
			$item_number_id=str_replace("'","",$cbo_gmtsItem_id);
		}
		else
		{
			$item_number_id=$row_se_trim_cons[csf('item_number_id')];
		}

		$color_size_table_id=$array_color_size_table_id[$po_id_maping_array[$row_se_trim_cons[csf('po_break_down_id')]]][$item_number_id][$row_se_trim_cons[csf('color_number_id')]][$row_se_trim_cons[csf('size_number_id')]];
		$wo_pre_cost_trim_co_cons_dtls_id=return_next_id( "id", "wo_pre_cost_trim_co_cons_dtls", 1 ) ;
        $sql_insert_fabric_trim_avg="insert into wo_pre_cost_trim_co_cons_dtls (id, job_id, wo_pre_cost_trim_cost_dtls_id, job_no, po_break_down_id, item_size, cons, place, pcs, country_id, excess_per, tot_cons, ex_cons, item_number_id, color_number_id, item_color_number_id, size_number_id, rate, amount, gmts_pcs, color_size_table_id)
		select $wo_pre_cost_trim_co_cons_dtls_id, $job_id, ".$trim_table_id_maping[$row_se_trim_cons[csf('wo_pre_cost_trim_cost_dtls_id')]].", '".$new_job_no[0]."', ".$po_id_maping_array[$row_se_trim_cons[csf('po_break_down_id')]].", item_size, cons, place, pcs, country_id, excess_per, tot_cons, ex_cons, $item_number_id_fill, color_number_id, item_color_number_id, size_number_id, rate, amount, gmts_pcs, '".$color_size_table_id."' from wo_pre_cost_trim_co_cons_dtls where job_no=$txt_job_no and id=".$row_se_trim_cons[csf('id')]."";

		$rID14=execute_query($sql_insert_fabric_trim_avg,0);
	}
	if($db_type==0)
	{
		if($rID14){
		mysql_query("COMMIT");
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID14){
		oci_commit($con);
		}
	}

	$sql_trim_supp=sql_select("select id, trimid from wo_pre_cost_trim_supplier b where job_no=$txt_job_no order by id asc");
	foreach($sql_trim_supp as $row_trim_supp)
	{
		$trim_supp_dtls_id=return_next_id( "id", "wo_pre_cost_trim_supplier", 1) ;
        $sql_insert_trim_supp="insert into wo_pre_cost_trim_supplier (id, job_id, job_no, trimid, supplier_id, inserted_by, insert_date, status_active, is_deleted)
		select $trim_supp_dtls_id, $job_id,'".$new_job_no[0]."',".$trim_table_id_maping[$row_trim_supp[csf('trimid')]].", supplier_id,".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."', status_active, is_deleted from wo_pre_cost_trim_supplier where job_no=$txt_job_no and id=".$row_trim_supp[csf('id')]." and is_deleted=0";

		$rID24=execute_query($sql_insert_trim_supp,0);
	}
	if($db_type==0)
	{
		if($rID24){
		mysql_query("COMMIT");
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID24){
		oci_commit($con);
		}
	}

	//die;
	$embl_pre_id_arr=array();
	$sql_se_embe_wash=sql_select("select id from wo_pre_cost_embe_cost_dtls where job_no=$txt_job_no order by id ASC");
	foreach($sql_se_embe_wash as $row_se_embe_wash)
	{
		$embe_cost_dtls_id=return_next_id( "id", "wo_pre_cost_embe_cost_dtls", 1 ) ;

        $sql_insert_embe_wash="insert into wo_pre_cost_embe_cost_dtls (id, job_id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, budget_on, supplier_id, country, body_part_id, inserted_by, insert_date, status_active, is_deleted)
		select $embe_cost_dtls_id, $job_id, '".$new_job_no[0]."', emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, budget_on, supplier_id, country, body_part_id, ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."', status_active, is_deleted from wo_pre_cost_embe_cost_dtls where job_no=$txt_job_no and id=".$row_se_embe_wash[csf('id')]." and is_deleted=0";
		$rID15=execute_query($sql_insert_embe_wash,0);
		$embl_pre_id_arr[$row_se_embe_wash[csf('id')]]=$embe_cost_dtls_id;
	}
    if($db_type==0)
	{
		if($rID15){
		mysql_query("COMMIT");
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID15){
		oci_commit($con);
		}
	}
	$po_id=str_replace("'","",$txt_po_id);
	if($po_id=="") $po_id_cond=""; else $po_id_cond=" and po_break_down_id in ($po_id)";

	$sqlemb=sql_select("select id, pre_cost_emb_cost_dtls_id, po_break_down_id, item_number_id, color_number_id, size_number_id from wo_pre_cos_emb_co_avg_con_dtls  where job_no=$txt_job_no $po_id_cond order by id asc");
	foreach($sqlemb as $row_emb)
	{
		if(str_replace("'","",$cbo_gmtsItem_id)!=0 && str_replace("'","",$cbo_order_uom)==1)
		{
			$conditem_id=str_replace("'","",$cbo_gmtsItem_id);
			$item_number_id_fill=str_replace("'","",$cbo_gmtsItem_id);
		}
		else
		{
			$conditem_id=$row_emb[csf('item_number_id')];
			$item_number_id_fill='item_number_id';
		}

		$color_size_table_id=$array_color_size_table_id[$po_id_maping_array[$row_emb[csf('po_break_down_id')]]][$conditem_id][$row_emb[csf('color_number_id')]][$row_emb[csf('size_number_id')]];
		$avg_trim_co_cons_dtls_id=return_next_id( "id", "wo_pre_cos_emb_co_avg_con_dtls", 1) ;

        $sql_insertTrim_avg="insert into wo_pre_cos_emb_co_avg_con_dtls (id, job_id, pre_cost_emb_cost_dtls_id, job_no, po_break_down_id, item_number_id, color_number_id, size_number_id, requirment, rate, amount, gmts_pcs, color_size_table_id, rate_lib_id, country_id)
		select $avg_trim_co_cons_dtls_id, $job_id, '".$embl_pre_id_arr[$row_emb[csf('pre_cost_emb_cost_dtls_id')]]."', '".$new_job_no[0]."', '".$po_id_maping_array[$row_emb[csf('po_break_down_id')]]."', $item_number_id_fill, color_number_id, size_number_id, requirment, rate, amount, gmts_pcs, '".$color_size_table_id."', rate_lib_id, country_id from wo_pre_cos_emb_co_avg_con_dtls where job_no=$txt_job_no and id=".$row_emb[csf('id')]."";

		$rIDavgemv=execute_query($sql_insertTrim_avg,0);
	}
	if($db_type==0)
	{
		if($rIDavgemv){
		mysql_query("COMMIT");
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rIDavgemv){
		oci_commit($con);
		}
	}

	//item_number_id
	if(str_replace("'","",$cbo_gmtsItem_id)!='' && str_replace("'","",$cbo_order_uom)==1)
	{
		$item_id_fill=str_replace("'","",$cbo_gmtsItem_id);
	}
	else
	{
		$item_id_fill='item_id';
	}


	$sql_se_commercial=sql_select("select id from wo_pre_cost_comarci_cost_dtls  where job_no=$txt_job_no order by id ASC");
	foreach($sql_se_commercial as $row_se_commercial)
	{
		$wo_pre_cost_comarci_cost_dtls_id=return_next_id( "id", "wo_pre_cost_comarci_cost_dtls", 1 ) ;
        $sql_insert_commercial="insert into wo_pre_cost_comarci_cost_dtls (id, job_id, job_no, item_id, rate, amount, inserted_by, insert_date, status_active, is_deleted) select $wo_pre_cost_comarci_cost_dtls_id, $job_id, '".$new_job_no[0]."', $item_id_fill, rate, amount, ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."', status_active, is_deleted from  wo_pre_cost_comarci_cost_dtls where job_no=$txt_job_no and id=".$row_se_commercial[csf('id')]." and is_deleted=0";
		$rID16=execute_query($sql_insert_commercial,0);
	}
    if($db_type==0)
	{
		if($rID16){
		mysql_query("COMMIT");
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID16){
		oci_commit($con);
		}
	}

	$sql_se_commision=sql_select("select id from wo_pre_cost_commiss_cost_dtls where job_no=$txt_job_no order by id ASC");
	foreach($sql_se_commision as $row_se_commision)
	{
		$wo_pre_cost_commiss_cost_dtls_id=return_next_id( "id", "wo_pre_cost_commiss_cost_dtls", 1) ;
        $sql_insert_commision="insert into wo_pre_cost_commiss_cost_dtls (id, job_id, job_no, particulars_id, commission_base_id, commision_rate, commission_amount, inserted_by, insert_date, status_active, is_deleted)
		select $wo_pre_cost_commiss_cost_dtls_id, $job_id, '".$new_job_no[0]."', particulars_id, commission_base_id, commision_rate, commission_amount, ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."', status_active, is_deleted from wo_pre_cost_commiss_cost_dtls where job_no=$txt_job_no and id=".$row_se_commision[csf('id')]." and is_deleted=0";
		$rID17=execute_query($sql_insert_commision,0);
	}
    if($db_type==0)
	{
		if($rID17){
		mysql_query("COMMIT");
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID17){
		oci_commit($con);
		}
	}

	$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
	$sql_insert_sum="insert into wo_pre_cost_sum_dtls (id, job_id, job_no, fab_yarn_req_kg, fab_woven_req_yds, fab_knit_req_kg, fab_amount, avg, yarn_cons_qnty, yarn_amount, conv_req_qnty, conv_charge_unit, conv_amount, trim_cons, trim_rate, trim_amount, emb_amount, comar_rate, comar_amount, commis_rate, commis_amount, fab_woven_fin_req_yds, fab_knit_fin_req_kg, pro_woven_grey_fab_req_yds, pro_knit_grey_fab_req_kg, pro_woven_fin_fab_req_yds, pro_knit_fin_fab_req_kg, pur_woven_grey_fab_req_yds, pur_knit_grey_fab_req_kg, pur_woven_fin_fab_req_yds, pur_knit_fin_fab_req_kg, woven_amount, knit_amount, lab_test_rate, inserted_by, insert_date, status_active, is_deleted)
		select $wo_pre_cost_sum_dtls_id, $job_id, '".$new_job_no[0]."', fab_yarn_req_kg, fab_woven_req_yds, fab_knit_req_kg, fab_amount, avg, yarn_cons_qnty, yarn_amount, conv_req_qnty, conv_charge_unit, conv_amount, trim_cons, trim_rate, trim_amount, emb_amount, comar_rate, comar_amount, commis_rate, commis_amount, fab_woven_fin_req_yds, fab_knit_fin_req_kg, pro_woven_grey_fab_req_yds, pro_knit_grey_fab_req_kg, pro_woven_fin_fab_req_yds, pro_knit_fin_fab_req_kg, pur_woven_grey_fab_req_yds, pur_knit_grey_fab_req_kg, pur_woven_fin_fab_req_yds, pur_knit_fin_fab_req_kg, woven_amount, knit_amount, lab_test_rate, ".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."', status_active, is_deleted from  wo_pre_cost_sum_dtls where job_no=$txt_job_no and is_deleted=0";
	$rID18=execute_query($sql_insert_sum,0);

	if($db_type==0)
	{
		if($rID18){
		mysql_query("COMMIT");
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		if($rID18){
		oci_commit($con);
		}
	}
	echo "0**".$new_job_no[0];
	disconnect($con);
	die;
}

if($action=="check_precost")
{
	$sql_data=sql_select("select count(a.id) as id, c.order_uom from  wo_pre_cost_mst a, wo_pre_cost_fabric_cost_dtls b, wo_po_details_master c where a.job_no=b.job_no and a.job_no=c.job_no and a.job_no='$data' and a.is_deleted=0 and  a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.job_no,c.order_uom");
	$id=0;
	$order_uom=0;
	foreach($sql_data as $row)
	{
		$id=$row[csf('id')];
		$order_uom=$row[csf('order_uom')];
	}
	echo trim($id)."_".trim($order_uom);
	disconnect($con);die;
}


if($action=="open_set_list_view")
{
	echo load_html_head_contents("Set Entry","../../../", 1, 1, $unicode);
	extract($_REQUEST); ?>
	<script>
	function add_break_down_set_tr( i )
	{
		var unit_id= document.getElementById('unit_id').value;
		if(unit_id==1)
		{
			alert('Only One Item for Pcs');
			return false;
		}
		var row_num=$('#tbl_set_details tr').length-1;
		if (row_num!=i)
		{
			return false;
		}
		if (form_validation('cboitem_'+i+'*txtsetitemratio_'+i,'Gmts Items*Set Ratio')==false)
		{
			return;
		}
		else
		{
			i++;
			$("#tbl_set_details tr:last").clone().find("input,select,a").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { return name + i },
					'value': function(_, value) { return value }
				});
			}).end().appendTo("#tbl_set_details");
			$('#txtsetitemratio_'+i).removeAttr("onChange").attr("onChange","calculate_set_smv("+i+")");
			$('#smv_'+i).removeAttr("onChange").attr("onChange","calculate_set_smv("+i+")");
			$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_set_tr("+i+")");
			$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_delete_down_tr("+i+",'tbl_set_details')");
			$('#cboitem_'+i).val('');
			set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
			set_sum_value_set( 'tot_smv_qnty', 'smvset_' );
		}
	}

	function fn_delete_down_tr(rowNo,table_id)
	{
		if(table_id=='tbl_set_details')
		{
			var numRow = $('table#tbl_set_details tbody tr').length;
			if(numRow==rowNo && rowNo!=1)
			{
				$('#tbl_set_details tbody tr:last').remove();
			}
			set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
			set_sum_value_set( 'tot_smv_qnty', 'smvset_' );
		}
	}

	function check_duplicate(id,td)
	{
		var item_id=(document.getElementById('cboitem_'+id).value);
		var row_num=$('#tbl_set_details tr').length-1;
		for (var k=1;k<=row_num; k++)
		{
			if(k==id)
			{
				continue;
			}
			else
			{
				if(item_id==document.getElementById('cboitem_'+k).value)
				{
					alert("Same Gmts Item Duplication Not Allowed.");
					document.getElementById(td).value="0";
					document.getElementById(td).focus();
				}
			}
		}
	}

	function calculate_set_smv(i)
	{
		var txtsetitemratio=document.getElementById('txtsetitemratio_'+i).value;
		var smv=document.getElementById('smv_'+i).value;
		var set_smv=txtsetitemratio*smv;
		document.getElementById('smvset_'+i).value=set_smv;
		set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
		set_sum_value_set( 'tot_smv_qnty', 'smvset_' );
	}

	function set_sum_value_set(des_fil_id,field_id)
	{
		var rowCount = $('#tbl_set_details tr').length-1;
		if(des_fil_id=="tot_set_qnty")
		{
			math_operation( des_fil_id, field_id, '+', rowCount );
		}
		else if(des_fil_id=="tot_smv_qnty")
		{
			var ddd={ dec_type:1, comma:0, currency:1}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
		}
	}

	function js_set_value_set()
	{
		var rowCount = $('#tbl_set_details tr').length-1;
		var set_breck_down="";
		var item_id=""
		for(var i=1; i<=rowCount; i++)
		{
			if (form_validation('cboitem_'+i+'*txtsetitemratio_'+i+'*smv_'+i,'Gmts Items*Set Ratio')==false)
			{
				return;
			}
			var smv =document.getElementById('smv_'+i).value;
			if(smv==0)
			{
				alert("Smv 0 not accepted");
				return;
			}
			if(set_breck_down=="")
			{
				set_breck_down+=$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val()+'_'+($('#smv_'+i).val()*1)+'_'+$('#smvset_'+i).val()+'_'+$('#complexity_'+i).val()+'_'+$('#emblish_'+i).val();
				item_id+=$('#cboitem_'+i).val();
			}
			else
			{
				set_breck_down+="__"+$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val()+'_'+($('#smv_'+i).val()*1)+'_'+$('#smvset_'+i).val()+'_'+$('#complexity_'+i).val()+'_'+$('#emblish_'+i).val();
				item_id+=","+$('#cboitem_'+i).val();
			}
		}
		document.getElementById('set_breck_down').value=set_breck_down;
		document.getElementById('item_id').value=item_id;
		parent.emailwindow.hide();
	}

	function open_emblishment_pop_up(i)
	{
		var page_link="order_entry_controller.php?action=open_emblishment_list";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, pcs_or_set, 'width=620px,height=300px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var set_breck_down=this.contentDoc.getElementById("set_breck_down");
			var item_id=this.contentDoc.getElementById("item_id");
			var tot_set_qnty=this.contentDoc.getElementById("tot_set_qnty");
			var tot_smv_qnty=this.contentDoc.getElementById("tot_smv_qnty");
			document.getElementById('set_breck_down').value=set_breck_down.value;
			document.getElementById('item_id').value=item_id.value;
			document.getElementById('tot_set_qnty').value=tot_set_qnty.value;
			document.getElementById('tot_smv_qnty').value=tot_smv_qnty.value;
		}
	}
    </script>
	</head>
	<body>
    <div id="set_details"  align="center">
        <fieldset>
        <?
			$disabled=0;
			if($precostfound >0 ){
				echo "Pre Cost Found, Any Change will be not allowed";
				$disabled=1;
			}
			else{
				$disabled=0;
			}
        ?>
        <form id="setdetails_1" autocomplete="off">
            <input type="hidden" id="set_breck_down" />
            <input type="hidden" id="item_id" />
            <input type="hidden" id="unit_id" value="<? echo $unit_id;  ?>" />
            <table width="560" cellspacing="0" class="rpt_table" border="0" id="tbl_set_details" rules="all">
                <thead>
                    <tr>
                        <th width="230" class="must_entry_caption">Item</th>
                        <th width="40" class="must_entry_caption">Set Ratio</th>
                        <th width="40" class="must_entry_caption">SMV/ Pcs</th>
                        <th width="80">Complexity</th>
                        <th width="80">Embellishment</th>
                        <th width=""></th>
                    </tr>
                </thead>
                <tbody>
					<?
                    //echo $set_breck_down;
                    $data_array=explode("__",$set_breck_down);
                    if($data_array[0]=="")
                    {
                    	$data_array=array();
                    }
                    if(count($data_array)>0)
                    {
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							$data=explode('_',$row);
							?>
							<tr id="settr_1" align="center">
                                <td><? echo create_drop_down( "cboitem_".$i, 230, $garments_item, "",1," -- Select Item --", $data[0], "check_duplicate(".$i.",this.id )",$disabled,'' ); ?></td>
                                <td><input type="text" id="txtsetitemratio_<? echo $i;?>"   name="txtsetitemratio_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric" onChange="calculate_set_smv(<? echo $i;?>)"  value="<? echo $data[1] ?>" <? if ($unit_id==1){echo "readonly";} else{echo "";}?> <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>  /></td>
                                <td><input type="text" id="smv_<? echo $i;?>"   name="smv_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric" onChange="calculate_set_smv(<? echo $i;?>)"  value="<? echo $data[2] ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?> />
                                	<input type="hidden" id="smvset_<? echo $i;?>"   name="smvset_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric"  value="<? echo $data[3] ?>" readonly/>
                                </td>
                                <td><? echo create_drop_down( "complexity_".$i, 80, $complexity_level, "",1," -- Select --", $data[4], "",$disabled,'' ); ?></td>
                                <td><? echo create_drop_down( "emblish_".$i, 80, $yes_no, "",1," -- Select--", $data[5], "",$disabled,'' ); ?></td>
                                <td>
                                    <input type="button" id="increaseset_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(<? echo $i; ?> )" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/>
                                    <input type="button" id="decreaseset_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(<? echo $i; ?> ,'tbl_set_details' );" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/>
                                </td>
							</tr>
							<?
						}
                    }
                    else
                    {
						?>
						<tr id="settr_1" align="center">
                            <td><? echo create_drop_down( "cboitem_1", 230, $garments_item, "",1,"--Select--", 0, "check_duplicate(1,this.id )",'','' ); ?></td>
                            <td>
                            	<input type="text" id="txtsetitemratio_1" name="txtsetitemratio_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(1)"  value="<?  if ($unit_id==1){echo "1";} else{echo "";}?>" <? if ($unit_id==1){echo "readonly";} else{echo "";}?> />
                            </td>
                            <td>
                                <input type="text" id="smv_1" name="smv_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(1)"  value="" />
                                <input type="hidden" id="smvset_1" name="smvset_1" style="width:30px" class="text_boxes_numeric" value="0"  />
                            </td>
                            <td><? echo create_drop_down( "complexity_1", 80, $complexity_level, "",1," -- Select --", 0, "",'','' ); ?></td>
                            <td><? echo create_drop_down( "emblish_1", 80, $yes_no, "",1," -- Select --", 0, "",'','' ); ?></td>
                            <td>
                                <input type="button" id="increaseset_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(1)" />
                                <input type="button" id="decreaseset_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(1 ,'tbl_set_details');"/>
                            </td>
						</tr>
						<?
                    }
                    ?>
                </tbody>
            </table>
            <table width="560" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    <tr>
                        <th width="230">Total</th>
                        <th width="40">
                        	<input type="text" id="tot_set_qnty" name="tot_set_qnty" class="text_boxes_numeric" style="width:30px"  value="<? if($tot_set_qnty !=''){ echo $tot_set_qnty;} else{ echo 1;} ?>" readonly />
                        </th>
                        <th  width="40">
                        	<input type="text" id="tot_smv_qnty" name="tot_smv_qnty" class="text_boxes_numeric" style="width:30px"  value="<? if($tot_smv_qnty !=''){ echo $tot_smv_qnty;} else{ echo 0;} ?>" readonly />
                        </th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            <table width="560" cellspacing="0" class="" border="0">
                <tr>
                    <td align="center" width="100%" class="button_container"><input type="button" class="formbutton" value="Close" onClick="js_set_value_set()"/></td>
                </tr>
            </table>
            </form>
        </fieldset>
    </div>
    </body>
    <script>$('#smv_1').focus();</script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="load_drop_gmts_item")
{
	echo create_drop_down( "cbo_gmtsItem_id", 120, $garments_item, 0, 1, "--Select Item--", $data,"fnc_calAmountQty_ex(0,1);",'',$data);
	exit();
}

if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season_id", 150, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}

if($action=="check_precost_version")
{
	$sql_data=sql_select("select entry_from from wo_pre_cost_mst where job_no='$data' and is_deleted=0 and status_active=1");
	if(count($sql_data)>0) $entry_from=$sql_data[0][csf('entry_from')]; else $entry_from="";

	echo trim($entry_from);
	die;
}

if($action=="open_smv_list")
{
	echo load_html_head_contents("WS SMV Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$item_id=$item_id;
	$style_id=$txt_style_ref;
	$set_smv_id=$set_smv_id;
	$set_smv_id=$set_smv_id;
	$cbo_buyer_name=$cbo_buyer_name;
	$cbo_company_name=$cbo_company_name;
	$job_no=$job_no;
	//echo $cbo_company_name;
	?>
	<script type="text/javascript">
		function js_set_value()
		{
			var str_data="";
			var row_num=$('#list_view tbody tr').length;
			for (var i=1; i<=row_num; i++)
			{
				if(str_data=="") str_data+=$('#hid_datastr'+i).val(); else str_data+="*"+$('#hid_datastr'+i).val();
			}
			//alert(str_data)
			document.getElementById('selected_smv').value=str_data;
			parent.emailwindow.hide();
		}
    </script>

    </head>
    <body>
    <div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="400" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <th width="150">Buyer Name</th>
                <th width="100">Style Ref </th>
                <th>
                    <input type="hidden" id="selected_job">
                    <input type="hidden" id="item_id" value="<? echo $item_id;?>">
                    <input type="hidden" id="job_no" value="<? echo $job_no;?>">
                    <input type="hidden" id="company_id" value="<? echo $cbo_company_name;?>">
                &nbsp;</th>
            </thead>
            <tr class="general">
                <td><? echo create_drop_down( "cbo_buyer_name", 172, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_name' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name",'id,buyer_name', 1, "-- Select Buyer --",$cbo_buyer_name,"",1 ); ?></td>
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px" value="<? echo $txt_style_ref;?>" disabled></td>
                <td>
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('item_id').value+'_'+document.getElementById('job_no').value, 'create_item_smv_search_list_view', 'search_div', 'job_copy_full_controller', '');" style="width:100px;" /><!--setFilterGrid(\'list_view\',-1)--></td>
            </tr>
        </table>
    	<div id="search_div"></div>
    </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
	exit();
}

if($action=="create_item_smv_search_list_view")
{
	$data=explode('_',$data);
	$company=$data[0];
	$buyer_id=$data[1];
	$style=$data[2];
	$item_id=$data[3];
	$job_no=$data[4];

	//if($job_no!="" && $item_id!=0) { $gmtsItem=return_field_value("gmts_item_id","wo_po_details_master","job_no='$job_no' and is_deleted=0 and status_active=1"); }
	//else { $gmtsItem=$item_id; }
	if($job_no!="" && $item_id!=0) { $gmtsItem=return_field_value("gmts_item_id","wo_po_details_master","style_ref_no='$style' and is_deleted=0 and status_active=1"); }
	else { $gmtsItem=$item_id; } //IssueId=20948

	//if ($company!=0) $company_con=" and a.company_id='$company'";else $company_con="";
	if ($buyer_id!=0) $buyer_id_con=" and a.buyer_id='$buyer_id'"; else $buyer_id_con="";
	if ($style!="") $style_con=" and a.style_ref ='$style'"; else $style_con="";
	//if ($item_id!=0) $gmts_item_con=" and a.gmts_item_id='$item_id'";else $gmts_item_con="";
	//if ($item_id!=0) $gmts_item_con2=" and a.gmt_item_id='$item_id'";else $gmts_item_con2="";
	if ($gmtsItem!=0) $gmts_item_con=" and a.gmts_item_id in ($gmtsItem)"; 
	else if($item_id>0 && $gmtsItem=="") $gmts_item_con=" and a.gmts_item_id in ($item_id)";
	else $gmts_item_con="";
	?>
	<input type="hidden" id="selected_smv" name="selected_smv" />
	<?

	if($db_type==0)
	{
		$group_con="group_concat(b.lib_sewing_id)  as lib_sewing_id";
		$id_group_con="group_concat(a.id)";
	}
	else
	{
		$group_con="listagg(b.lib_sewing_id,',') within group (order by b.lib_sewing_id) as lib_sewing_id";
		$id_group_con="listagg(a.id,',') within group (order by a.id)";
	}

	$sql="select a.id, a.system_no, a.color_type, a.extention_no, a.style_ref, a.operation_count, a.gmts_item_id, b.operator_smv, b.helper_smv, b.body_part_id, b.lib_sewing_id, c.department_code from ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b, lib_sewing_operation_entry c where a.id=b.mst_id and b.lib_sewing_id=c.id and a.approved=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $gmts_item_con $style_con $buyer_id_con
	order by a.id DESC";
	$sql_result=sql_select($sql);
	foreach($sql_result as $row)
	{
		//$operation_name=$code_smv_arr[$row[csf('lib_sewing_id')]][$row[csf('body_part_id')]]['operation_name'];
		$smv_dtls_arr[$row[csf('gmts_item_id')]][$row[csf('system_no')]][$row[csf('extention_no')]]['style_ref']=$row[csf('style_ref')];
		$smv_dtls_arr[$row[csf('gmts_item_id')]][$row[csf('system_no')]][$row[csf('extention_no')]]['color_type']=$row[csf('color_type')];
		$smv_dtls_arr[$row[csf('gmts_item_id')]][$row[csf('system_no')]][$row[csf('extention_no')]]['operation_count']=$row[csf('operation_count')];
		$smv_dtls_arr[$row[csf('gmts_item_id')]][$row[csf('system_no')]][$row[csf('extention_no')]]['id'].=$row[csf('id')].',';
		$smv_dtls_arr[$row[csf('gmts_item_id')]][$row[csf('system_no')]][$row[csf('extention_no')]]['system_no'].=$row[csf('system_no')].',';
		//$smv_dtls_arr[$row[csf('id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
		$smv_dtls_arr[$row[csf('gmts_item_id')]][$row[csf('system_no')]][$row[csf('extention_no')]]['lib_sewing_id'].=$row[csf('lib_sewing_id')].',';
		//$smv_dtls_arr[$row[csf('id')]]['body_part_id']=$row[csf('body_part_id')];
		//$smv_dtls_arr[$row[csf('id')]]['operation_name']=$operation_name;
		$code_id=$code_smv_arr[$row[csf('lib_sewing_id')]]['dcode'];
		$smv=0;
		$smv=$row[csf('operator_smv')]+$row[csf('helper_smv')];
		//echo $row[csf('operator_smv')].'<br>'.$row[csf('helper_smv')].'<br>';

		$smv_sewing_arr[$row[csf('id')]][$row[csf('department_code')]][$row[csf('lib_sewing_id')]]['operator_smv']+=$smv;
	}
	/*echo '<pre>';
	print_r($smv_dtls_arr); die;*/
	?>
	<table width="700" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table" id="list_view">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="80">Gmts. Item</th>
                <th width="80">Color</th>
                <th width="80">Sys. ID.</th>
                <th width="50">Ext. NO</th>
                <th width="140">Style</th>
                <th width="60">Avg. Sewing SMV</th>
                <th width="60">Avg. Cuting SMV</th>
                <th width="60">Avg. Finish SMV</th>
                <th>No of Operation</th>
            </tr>
        </thead>
        <tbody>
        <?
        $i=1;
		foreach($smv_dtls_arr as $gmtsitem=>$gmtsitemdata)
		{
			foreach ($gmtsitemdata as $sysno => $systemdata)
			{
				foreach($systemdata as $ext_no=>$arrdata)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$lib_sewing_id=rtrim($arrdata['lib_sewing_id'],',');
					$lib_sewing_ids=array_unique(explode(",",$lib_sewing_id));

					$finish_smv=$cut_smv=$sewing_smv=0;

					$sys_id=rtrim($arrdata['id'],',');
					$ids=array_filter(array_unique(explode(",",$sys_id)));
					//print_r($ids);
					$id_str=""; $k=0;
					foreach($ids as $idstr)
					{
						if($id_str=="") $id_str=$idstr; else $id_str.=','.$idstr;

						foreach($lib_sewing_ids as $lsid)
						{
							$finish_smv+=$smv_sewing_arr[$idstr][4][$lsid]['operator_smv'];
							$cut_smv+=$smv_sewing_arr[$idstr][7][$lsid]['operator_smv'];
							$sewing_smv+=$smv_sewing_arr[$idstr][8][$lsid]['operator_smv'];
						}
						$k++;
					}

					$system_no=rtrim($arrdata['system_no'],',');
					$system_no=implode(",",array_filter(array_unique(explode(",",$system_no))));

					$finish_smv=$finish_smv/$k;
					$cut_smv=$cut_smv/$k;
					$sewing_smv=$sewing_smv/$k;

					$data=$gmtsitem."_".number_format($sewing_smv,2)."_".number_format($cut_smv,2)."_".number_format($finish_smv,2)."_".$id_str;
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer"><!--onClick="js_set_value('<? //echo $data; ?>')"-->
						<td width="30"><? echo $i; ?><input type="hidden" id="hid_datastr<?=$i;?>" value="<?=$data;?>" /></td>
	                    <td width="80" style="word-break:break-all"><? echo $garments_item[$gmtsitem]; ?></td>
	                    <td width="80" style="word-break:break-all"><? echo $color_type[$arrdata['color_type']]; ?></td>
						<td width="80" style="word-break:break-all"><? echo $system_no; ?></td>
						<td width="50" style="word-break:break-all"><? echo $ext_no; ?></td>
						<td width="140" style="word-break:break-all"><? echo $arrdata['style_ref']; ?></td>
						<td width="60" align="right"><p><? echo number_format($sewing_smv,2); ?></p></td>
						<td width="60" align="right"><p><? echo number_format($cut_smv,2); ?></p></td>
						<td width="60" align="right"><p><? echo number_format($finish_smv,2); ?></p></td>
						<td><p><? echo $arrdata['operation_count']; ?></p></td>
					</tr>
					<?
					$i++;
				}
			}

		}
        ?>
        </tbody>
        <tfoot>
            	<tr>
                    <td colspan="9" align="center"><input type="button" class="formbutton" value="Close" onClick="js_set_value();"/></td>
                </tr>
        </tfoot>
	</table>
	<?
	exit();
}
?>