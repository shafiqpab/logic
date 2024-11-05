<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
require_once('../../../includes/class3/class.conditions.php');
require_once('../../../includes/class3/class.reports.php');
require_once('../../../includes/class3/class.conversions.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$permission=$_SESSION['page_permission'];

//---------------------------------------------------- Start-----------------------------------------------------------------------------

$userCredential = sql_select("SELECT unit_id as company_id, brand_id FROM user_passwd where id=$user_id");

$brand_id = $userCredential[0][csf('brand_id')];
$brand_cond="";

if ($brand_id !='') {
    $brand_cond = " and id in ( $brand_id)";
}
if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_supplier_name", 140, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=90 order by a.supplier_name", "id,supplier_name",1, "-- Select Company --", "", "",0,"" );
	exit();
}

if($action=="check_conversion_rate") //Conversion Exchange Rate
{
	$data=explode("**",$data);
	if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$currency_rate=set_conversion_rate( $data[0], $conversion_date, $data[2] );
	echo "1"."_".$currency_rate;
	exit;
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/sub_contract_work_order_controller', this.value, 'load_drop_down_brand', 'brand_td');" ); 
	exit();	 
}

if ($action=="load_drop_down_brand")
{
	echo create_drop_down( "cbo_brand_id", 140, "select id, brand_name from lib_buyer_brand where buyer_id='$data' and status_active =1 and is_deleted=0 order by brand_name ASC","id,brand_name", 1, "-Brand-", $selected, "" );
	exit();
}

if ($action=="cbo_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 140, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();
}


$company_library=return_library_array("select id,company_name from lib_company", "id", "company_name");
$supplier_arr=return_library_array("select id, supplier_name from lib_supplier",'id','supplier_name');
$color_arr=return_library_array("select id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name" );
$count_arr=return_library_array("Select id, yarn_count from  lib_yarn_count where  status_active=1",'id','yarn_count');
$brand_arr=return_library_array("Select id, brand_name from  lib_brand where  status_active=1",'id','brand_name');
$buyer_arr=return_library_array("select id, buyer_name from lib_buyer",'id','buyer_name');

if ($action=="job_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$job="";
	$sql=sql_select("select job_no from subcon_wo_mst a,  subcon_wo_dtls b  where a.id=b.mst_id and a.sucon_wo_no='$txt_booking_no'");
	foreach($sql as $row){
		$job=$row[csf('job_no')];
	}
	if($job) $disabled="disabled"; else $disabled="";

	?>
	<script>
			function set_checkvalue()
			{
				if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
				else document.getElementById('chk_job_wo_po').value=0;
			}

			function js_set_value( job_no_po_id )
			{
				var jobPo=job_no_po_id.split("_");
				document.getElementById('selected_job').value=jobPo[0];
				document.getElementById('selected_style').value=jobPo[1];
				document.getElementById('selected_style_num').value=jobPo[2];
				document.getElementById('exchange_rate').value=jobPo[3];
				document.getElementById('gmts_num').value=jobPo[4];
				document.getElementById('gauge_num').value=jobPo[5];
				document.getElementById('gauge_id').value=jobPo[9];
				document.getElementById('wo_qanty').value=jobPo[6];
				document.getElementById('selected_job_id').value=jobPo[7];
				document.getElementById('bwoqty').value=jobPo[8];
				parent.emailwindow.hide();
			}

    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="1020" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>
                     <th colspan="9" align="center"><?=create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>
                    <th width="150">Company Name</th>
                    <th width="150">Buyer Name</th>
                    <th width="80">Job No</th>
                    <th width="100">Style Ref </th>
                    <th width="100">Order No</th>
                    <th width="80">Int. Ref. No</th>
                    <th width="120" colspan="2">Date Range</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tr class="general">
                <td>
                    <input type="hidden" id="selected_job" name="selected_job">
                    <input type="hidden" id="selected_job_id" name="selected_job_id">
                    <input type="hidden" id="bwoqty" name="bwoqty">
                    <input type="hidden" id="selected_style" name="selected_style">
                    <input type="hidden" id="selected_style_num" name="selected_style_num">
                    <input type="hidden" id="exchange_rate" name="exchange_rate">
					<input type="hidden" id="gmts_num" name="gmts_num">
					<input type="hidden" id="gauge_num" name="gauge_num">
					<input type="hidden" id="gauge_id" name="gauge_id">
					<input type="hidden" id="wo_qanty" name="wo_qanty">
                    <input type="hidden" id="txt_booking_no" name="txt_booking_no" value="<? echo $txt_booking_no ?>">
                    
                    <?=create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --",$cbo_company_name,"load_drop_down( 'labtest_work_order_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",1 ); ?>
                </td>
                <td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 162, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_name' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); ?></td>
                <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:70px"></td>
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:90px"></td>
                <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:90px"></td>
                <td><input name="txt_order_search" id="txt_ref_no" class="text_boxes" style="width:70px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"></td>
                <td align="center">
                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('txt_ref_no').value, 'create_po_id_search_list_view', 'search_div', 'sub_contract_work_order_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:90px;" /></td>
        </tr>
        <tr>
             <td align="center" colspan="9" valign="middle"><?=load_month_buttons(1); ?></td>
        </tr>
     </table>
         <div align="center" valign="top" id="search_div"></div>
    </form>
   </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="create_po_id_search_list_view")
{
	$data=explode('_',$data);
	
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else  $buyer="";
	if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$data[5]"; else if($db_type==2) $year_cond="and to_char(a.insert_date,'YYYY')=$data[5]";

	$job="";

	$job_cond=""; $order_cond=""; $style_cond="";$ref_no_cond="";
	if($data[8]==1)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num='$data[4]' $year_cond";
		if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number = '$data[6]' ";
		if (trim($data[7])!="") $style_cond=" and a.style_ref_no ='$data[7]'";
		if (str_replace("'","",$data[10])!="") $ref_no_cond=" and b.grouping ='$data[10]'";
	}
	if($data[8]==2)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '$data[4]%' $year_cond";
		if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '$data[6]%'  ";
		if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '$data[7]%'  ";
		if (str_replace("'","",$data[10])!="") $ref_no_cond=" and b.grouping like '$data[10]%'";
	}
	if($data[8]==3)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]' $year_cond";
		if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '%$data[6]'  ";
		if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '%$data[7]'";
		if (str_replace("'","",$data[10])!="") $ref_no_cond=" and b.grouping like '%$data[10]'";
	}
	if($data[8]==4 || $data[8]==0)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]%' $year_cond";
		if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '%$data[6]%'";
		if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '%$data[7]%'";
		if (str_replace("'","",$data[10])!="") $ref_no_cond=" and b.grouping like '%$data[10]%'";
	}
	if($db_type==0)
	{
	if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	if($db_type==2)
	{
	if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}

	if($data[4]!=""){
		$job_cond=" and a.job_no_prefix_num='$data[4]' $year_cond";
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	?>
	<div style="width:970px;"align="left">

		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="960" class="rpt_table" >
			<thead>
				<th width="50">Year</th>
				<th width="50">Job No</th>
				<th width="120">Company</th>
				<th width="100">Buyer Name</th>
				<th width="100">Style Ref. No</th>
				<th width="80">Job Qty.</th>
				<th width="90">PO number</th>
				<th width="80">Gauge</th>
				<th width="80">Item Name</th>
				<th width="70">PO Quantity</th>
				<th width="90">Shipment Date</th>
			</thead>
		</table>
		<div style="width:980px; overflow-y:scroll; max-height:250px;" id="buyer_list_view">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="960" class="rpt_table" id="tbl_list_search" >
				<?

				$i=1;
				$sql= "SELECT a.id as job_id, to_char(a.insert_date,'YYYY') as year, a.job_no_prefix_num,a.gauge,d.gmts_item_id,a.company_name,a.buyer_name,a.style_ref_no,sum(b.plan_cut) as job_quantity,b.id,b.po_number, b.grouping ,b.po_quantity,b.shipment_date,a.job_no,c.id as pre_id,c.exchange_rate from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_pre_cost_mst c on a.id=c.job_id join wo_po_details_mas_set_details d on a.id=d.job_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 $shipment_date $company $buyer $job_cond $style_cond $order_cond $ref_no_cond group by a.id, a.insert_date,a.job_no_prefix_num,a.gauge,d.gmts_item_id,a.company_name,a.buyer_name,a.style_ref_no,b.id,b.po_number, b.grouping ,b.po_quantity,b.shipment_date,a.job_no,c.id,c.exchange_rate order by a.id Desc";
				//echo $sql; die;				
				$nameArray=sql_select( $sql );
				foreach($nameArray as $row){
					$job_id_arr[$row[csf('job_id')]]=$row[csf('job_id')];
					$total_qty_arr[$row[csf('job_id')]]+=$row[csf('job_quantity')];
				}
				$con = connect();
				execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1) and ENTRY_FORM=148");
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 149, 1, $job_id_arr, $empty_arr);
				$sub_wo_qty_arr=sql_select("SELECT a.job_id, sum(a.wo_qty) as wo_qty from subcon_wo_dtls a join gbl_temp_engine b on a.job_id=b.ref_val where a.status_active=1 and a.is_deleted=0 and b.ref_from=1 and b.entry_form=149 and b.user_id=$user_id group by a.job_id");
				foreach($sub_wo_qty_arr as $row){
					$wo_qty_arr[$row[csf('job_id')]]=$row[csf('wo_qty')];
				}
				foreach ($nameArray as $row)
				{
					$balance_qty=$total_qty_arr[$row[csf('job_id')]]-$wo_qty_arr[$row[csf('job_id')]];
					if($balance_qty>0){
						?>
						<tr style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $row[csf('job_no')]; ?>'+'_'+'<? echo $row[csf('id')]; ?>'+'_'+'<? echo $row[csf('style_ref_no')]; ?>'+'_'+'<? echo $row[csf('exchange_rate')]; ?>'+'_'+'<? echo $garments_item[$row[csf('gmts_item_id')]]; ?>'+'_'+'<? echo $gauge_arr[$row[csf('gauge')]]; ?>'+'_'+'<? echo $total_qty_arr[$row[csf('job_id')]]; ?>'+'_'+'<? echo $row[csf('job_id')]; ?>'+'_'+'<? echo $balance_qty; ?>'+'_'+'<? echo $row[csf('gauge')]; ?>'); ">

							<td width="50"><p><? echo $row[csf('year')]; ?></p></td>
							<td width="50"  align="center"> <p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
							<td width="120"  align="center"> <p><? echo $comp[$row[csf('company_name')]]; ?></p></td>
							<td width="100"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
							<td width="100"> <p><? echo $row[csf('style_ref_no')]; ?></p></td>
							<td width="80"> <p><? echo $total_qty_arr[$row[csf('job_id')]]; ?></p></td>
							<td width="90"  align="center"> <p><? echo $row[csf('po_number')]; ?></p></td>
							<td width="80"  align="center"> <p><? echo $gauge_arr[$row[csf('gauge')]]; ?></p></td>
							<td width="80"><p><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></p></td>
							<td width="70"> <p><? echo $row[csf('po_quantity')]; ?></p></td>
							<td width="90"> <p><? echo change_date_format($row[csf('shipment_date')]); ?></p></td>
						</tr>
						<?
						$i++;
					}
				}
				execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1) and ENTRY_FORM=149");
				oci_commit($con);
				disconnect($con);
				?>
			</table>
		</div>
	</div>
<?
	exit();
}
function sql_update2($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit="",$return_query='')
{
	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);

	if(count($arrUpdateFields)!=count($arrUpdateValues)){
		return "0";
	}

	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value;
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues;
	}
	$strQuery .=" WHERE ";

	$arrRefFields=explode("*",$arrRefFields);
	$arrRefValues=explode("*",$arrRefValues);
	if(is_array($arrRefFields))
	{
		$arrayRef = array_combine($arrRefFields,$arrRefValues);
		$Arraysize = count($arrayRef);
		$i = 1;
		foreach($arrayRef as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrRefFields."=".$arrRefValues."";
	}
	if($return_query==1){return $strQuery ;}

	echo "10**". $strQuery;die;
	global $con;
	if( strpos($strQuery, "WHERE")==false)  return "0";
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	
	if ($exestd){user_activities($exestd);}
	if ($exestd)
		return "1";
	else
		return "0";

	die;
	if ( $commit==1 )
	{
		if (!oci_error($stid))
		{
			oci_commit($con);
			return "1";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else
		return 1;
	die;
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	if ($operation==0)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if(str_replace("'","",$update_id)!="") //update
		{
			$id= return_field_value("id"," subcon_wo_mst","id=$update_id");//check sys id for update or insert
			$field_array="supplier_id*service_sweater*booking_date*currency*exchange_rate*tenor*attention*source*buyer_name*brand_id*team_leader*dealing_merchant*delivery_to*closing_date*mc_available*mc_allocate*prod_qnty*pay_mode*updated_by*update_date*status_active*is_deleted";
			$data_array="".$cbo_supplier."*".$cbo_service_type."*".$txt_booking_date."*".$cbo_currency."*".$txt_exchange_rate."*".$txt_tenor."*".$txt_attention."*".$cbo_source."*".$cbo_buyer_name."*".$cbo_brand_id."*".$cbo_team_leader."*".$cbo_dealing_merchant."*".$txt_delivery_to."*".$txt_closing_date."*".$txt_mc_available."*".$txt_mc_allocate."*".$txt_prod."*".$cbo_pay_mode."*'".$user_id."'*'".$pc_date_time."'*1*0";
			$return_no=str_replace("'",'',$txt_booking_no);
		}
		else // new insert
		{
			$id = return_next_id_by_sequence("SUBCON_WO_MST_MJSW_PK_SEQ", "subcon_wo_mst", $con);
			$new_sys_number = explode("*", return_next_id_by_sequence("SUBCON_WO_MST_MJSW_PK_SEQ", "subcon_wo_mst",$con,1,$cbo_company_name,'SSW',999,date("Y",time()),0 ));

			$field_array="id,subcon_wo_prefix,subcon_wo_suffix_num,sucon_wo_no,entry_form,company_id,supplier_id,service_sweater,booking_date,currency,exchange_rate,tenor,attention,source,buyer_name,brand_id,team_leader,dealing_merchant,delivery_to,closing_date,mc_available,mc_allocate,prod_qnty,pay_mode,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",'".$new_sys_number[1]."','".$new_sys_number[2]."','".$new_sys_number[0]."',643,".$cbo_company_name.",".$cbo_supplier.",".$cbo_service_type.",".$txt_booking_date.",".$cbo_currency.",".$txt_exchange_rate.",".$txt_tenor.",".$txt_attention.",".$cbo_source.",".$cbo_buyer_name.",".$cbo_brand_id.",".$cbo_team_leader.",".$cbo_dealing_merchant.",".$txt_delivery_to.",".$txt_closing_date.",".$txt_mc_available.",".$txt_mc_allocate.",".$txt_prod.",".$cbo_pay_mode.",'".$user_id."','".$pc_date_time."',1,0)";
			// inv_gate_in_mst master table entry here END---------------------------------------//
			$return_no=str_replace("'",'',$new_sys_number[0]);
		}
		
		//for transaction log
		$log_entry_form = 643;
		$log_ref_id = $id; 
		$log_ref_number = $return_no; 

		$dtlsid=return_next_id("id","subcon_wo_dtls", 1);
		$field_array_dts="id,mst_id,job_no,style_no,job_id,entry_form,gmts_no,gauge,wo_qty,dyeing_charge,amount,start_date,end_date,extra_percent,total_wo_qty,rate_break_down,status_active,is_deleted";
		$data_array_dts="(".$dtlsid.",".$id.",".$txt_job_no.",".$txt_style_no.",".$txt_job_id.",643,".$txt_gmts_no.",".$txt_gauge_id.",".$txt_wo_qty.",".$txt_dyeing_charge.",".$txt_amount.",".$txt_start_date.",".$txt_end_date.",".$txt_wo_qty_per.",".$txt_total_wo_qty.",".$rate_break_down.",1,0)";

		if(str_replace("'","",$update_id)!="") //update
		{
			$rID=sql_update("subcon_wo_mst",$field_array,$data_array,"id",$id,1);
		}
		else
		{
			$rID=sql_insert("subcon_wo_mst",$field_array,$data_array,0);
		}
		//echo "10**INSERT INTO subcon_wo_dtls ($field_array_dts) values $data_array_dts"; die;
		$dtlsrID=sql_insert("subcon_wo_dtls",$field_array_dts,$data_array_dts,0);
		//echo "10**".$rID.'-'.$dtlsrID; die;
		if($db_type==1 || $db_type==2)
		{
			if($rID && $dtlsrID)
			{
				//for transaction log
				$log_data = array();
				$log_data['entry_form'] = $log_entry_form; 
				$log_data['ref_id'] = $log_ref_id; 
				$log_data['ref_number'] = $log_ref_number; 
				manage_allocation_transaction_log($log_data);
				//end for transaction log
			
				oci_commit($con);
				echo "0**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id);
			}
		}

		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$dtls_update_id=str_replace("'","",$dtls_update_id);

		//check update id
		if( str_replace("'","",$update_id) == "")
		{
			echo "15"; disconnect($con);exit();
		}

		//subcon_wo_mst master table UPDATE here START----------------------//	".$txt_pro_id.",cbo_pay_mode
		
		$field_array="supplier_id*service_sweater*booking_date*currency*exchange_rate*tenor*attention*source*buyer_name*brand_id*team_leader*dealing_merchant*delivery_to*closing_date*mc_available*mc_allocate*prod_qnty*pay_mode*updated_by*update_date*status_active*is_deleted";
		$data_array="".$cbo_supplier."*".$cbo_service_type."*".$txt_booking_date."*".$cbo_currency."*".$txt_exchange_rate."*".$txt_tenor."*".$txt_attention."*".$cbo_source."*".$cbo_buyer_name."*".$cbo_brand_id."*".$cbo_team_leader."*".$cbo_dealing_merchant."*".$txt_delivery_to."*".$txt_closing_date."*".$txt_mc_available."*".$txt_mc_allocate."*".$txt_prod."*".$cbo_pay_mode."*'".$user_id."'*'".$pc_date_time."'*1*0";

		$field_array_dtls = "job_no*style_no*job_id*gmts_no*gauge*wo_qty*dyeing_charge*amount*start_date*end_date*extra_percent*total_wo_qty*rate_break_down*updated_by*update_date*status_active*is_deleted";
		$data_array_dtls ="".$txt_job_no."*".$txt_style_no."*".$txt_job_id."*".$txt_gmts_no."*".$txt_gauge_id."*".$txt_wo_qty."*".$txt_dyeing_charge."*".$txt_amount."*".$txt_start_date."*".$txt_end_date."*".$txt_wo_qty_per."*".$txt_total_wo_qty."*".$rate_break_down."*'".$user_id."'*'".$pc_date_time."'*1*0";
		//echo "10**".$data_array_dtls; die;
		
		//for transaction log
		$log_entry_form = 643;
		$log_ref_id = str_replace("'", "", $update_id); 
		$log_ref_number = str_replace("'", "", $txt_booking_no); 

		$rID=sql_update("subcon_wo_mst",$field_array,$data_array,"id",$update_id,1);
		$dtlsrID = sql_update("subcon_wo_dtls",$field_array_dtls,$data_array_dtls,"id",$dtls_update_id,1);
		//echo "10**".$rID.'-'.$dtlsrID; die;
		$return_no=str_replace("'",'',$txt_booking_no);
		
		if($db_type==2)
		{
			if($rID && $dtlsrID)
			{
				//for transaction log
				$log_data = array();
				$log_data['entry_form'] = $log_entry_form; 
				$log_data['ref_id'] = $log_ref_id; 
				$log_data['ref_number'] = $log_ref_number;  
				manage_allocation_transaction_log($log_data);
				//end for transaction log
				
				oci_commit($con);
				echo "1**".str_replace("'",'',$return_no)."**".str_replace("'",'',$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$return_no)."**".str_replace("'",'',$update_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$update_id=str_replace("'","",$update_id);
		$dtls_update_id=str_replace("'","",$dtls_update_id);
		$txt_booking_no=str_replace("'","",$txt_booking_no);

		// master table delete here---------------------------------------
		if($update_id=="" || $update_id==0){ echo "15**0";  disconnect($con);die;}

 		//$rID = sql_update("wo_non_order_info_mst",'status_active*is_deleted','0*1',"id",$mst_id,1);
		$dtlsrID = sql_update("subcon_wo_dtls",'status_active*is_deleted','0*1',"id",$dtls_update_id,1);

		if($db_type==2 || $db_type==1 )
		{
			if($dtlsrID)
			{
				//for transaction log
				$log_data = array();
				$log_data['entry_form'] = $log_entry_form; 
				$log_data['ref_id'] = $log_ref_id; 
				$log_data['ref_number'] = $log_ref_number; 
				$log_data['insert_date'] = $pc_date_time;
				manage_allocation_transaction_log($log_data);
				//end for transaction log
				
				oci_commit($con);
				echo "2**".$txt_booking_no."**".$update_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="show_dtls_list_view")
{
	
	$sql="select a.id, a.job_no, a.style_no, a.gmts_no, a.gauge, a.wo_qty, a.dyeing_charge, a.amount, a.start_date, a.end_date from subcon_wo_dtls a where a.status_active=1 and a.is_deleted=0 and a.mst_id in('$data')";

	$sql_result=sql_select($sql);

	if(count($sql_result)>0)
	{
		?>
		<table width="830" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
			<thead>
				<tr>
					<th width="40">SL</th>
					<th width="80">Job No</th>
					<th width="105">Style Ref</th>
					<th width="105">Gmts. Name</th>
					<th width="100">Guage</th>
					<th width="60">Rate</th>
					<th width="70">WO QTY</th>
					<th width="70">Amount</th>
					<th width="100">Delivery Start Date</th>
					<th>Delivery End Date</th>
				</tr>
			</thead>
			<tbody>
				<?
				$i=1;

				foreach($sql_result as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;" onClick="get_php_form_data(<? echo $row[csf("id")]; ?>,'child_form_input_data', 'requires/sub_contract_work_order_controller' );">
						<td align='center'><p><? echo $i; ?></p></td>
						<td align='center'><p><? echo $row[csf("job_no")]; ?>&nbsp;</p></td>
						<td align='center'><p><? echo $row[csf("style_no")]; ?>&nbsp;</p></td>
						<td align='center'><p><? echo $row[csf("gmts_no")]; ?>&nbsp;</p></td>
						<td align='center'><p><? echo $gauge_arr[$row[csf("gauge")]]; ?>&nbsp;</p></td>
						<td align='center'><p><? echo $row[csf("dyeing_charge")]; ?>&nbsp;</p></td>
						<td align='center'><p><? echo $row[csf("wo_qty")]; ?>&nbsp;</p></td>
						<td align='center'><p><? echo $row[csf("amount")]; ?>&nbsp;</p></td>
						<td align='center'><p><? echo change_date_format($row[csf("start_date")]); ?>&nbsp;</p></td>
						<td align='center'><p><? echo change_date_format($row[csf("end_date")]); ?>&nbsp;</p></td>
					</tr>
					<?
					$i++;
				}
				?>
				<tbody>
				</table>
				<?
			}
			exit();

		} 

if($action=="child_form_input_data") 
{

	$plan_cut_qty_arr=sql_select("SELECT a.job_id, sum(a.plan_cut) as plan_cut from wo_po_break_down a join subcon_wo_dtls b on a.job_id=b.job_id where b.id=$data and a.status_active=1 and a.is_deleted=0 group by a.job_id");
	foreach($plan_cut_qty_arr as $row){
		$plan_cut_qty=$row[csf('plan_cut')];
		$job_id=$row[csf('job_id')];
	}
	$wo_qty_arr=sql_select("SELECT job_id, sum(wo_qty) as wo_qty from subcon_wo_dtls where status_active=1 and is_deleted=0 and job_id=$job_id and id<>$data group by job_id");
	foreach($wo_qty_arr as $row){
		$total_wo_qty=$row[csf('wo_qty')];
	}
	$sql="select a.id, a.job_no, a.job_id, a.style_no, a.gmts_no, a.gauge, a.wo_qty, a.dyeing_charge, a.amount, a.start_date, a.end_date, a.extra_percent, a.rate_break_down, a.total_wo_qty from subcon_wo_dtls a where a.id=$data";
	$sql_re=sql_select($sql);
	foreach($sql_re as $row)
	{
		$balance_woqty=$plan_cut_qty-$total_wo_qty;
		echo "$('#txt_job_no').val('".$row[csf("job_no")]."');\n";
		echo "$('#txt_style_no').val('".$row[csf("style_no")]."');\n";
		echo "$('#txt_job_id').val('".$row[csf("job_id")]."');\n";
		echo "$('#txt_gmts_no').val('".$row[csf("gmts_no")]."');\n";
		echo "$('#txt_gauge').val('".$gauge_arr[$row[csf("gauge")]]."');\n";
		echo "$('#txt_gauge_id').val(".$row[csf("gauge")].");\n";
		echo "$('#txt_wo_qty').val(".$row[csf("wo_qty")].");\n";
		echo "$('#balance_req_qty').val(".$balance_woqty.");\n";
		echo "$('#txt_wo_qty').attr('placeholder',".$balance_woqty.");\n";
		echo "$('#txt_dyeing_charge').val(".$row[csf("dyeing_charge")].");\n";
		echo "$('#txt_amount').val(".$row[csf("amount")].");\n";
		echo "$('#txt_wo_qty_per').val(".$row[csf("extra_percent")].");\n";
		echo "$('#rate_break_down').val('".$row[csf("rate_break_down")]."');\n";
		echo "$('#txt_total_wo_qty').val(".$row[csf("total_wo_qty")].");\n";
		echo "$('#txt_req_qty').val(".$plan_cut_qty.");\n";
		$start_date=change_date_format($row[csf("start_date")]);
		$end_date=change_date_format($row[csf("end_date")]);	
		echo "$('#txt_start_date').val('".$start_date."');\n";
		echo "$('#txt_end_date').val('".$end_date."');\n";

		//echo "fnc_calculate();\n";
		$dtls_updateId=$row[csf("id")];
		echo "$('#dtls_update_id').val(".$dtls_updateId.");\n";
		echo "set_button_status(1, permission, 'fnc_sub_contract_wo',1,0);\n";

	
		/* $job_ref=$row[csf("job_no")];
		$style_no_id=$row[csf("style_no")];
		$gmts_noId=$row[csf("gmts_no")];
		$yarn_wo_qty=$row[csf("wo_qty")];
		$dyeing_charge=$row[csf("dyeing_charge")];
		$gaugeId=$row[csf("gauge")];
		$amount=$row[csf("amount")];
		$dtls_updateId=$row[csf("id")];
		$start_date=change_date_format($row[csf("start_date")]);
		$end_date=change_date_format($row[csf("end_date")]);		
		$product_id=$row[csf("product_id")];
		$company_id = return_field_value("a.company_id as company_id", "subcon_wo_mst a,subcon_wo_dtls b"," b.mst_id=a.id and b.id=$data  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","company_id");
		echo "$('#txt_job_no').val('".$job_ref."');\n";
		echo "$('#txt_style_no').val('".$style_no_id."');\n";
		echo "$('#txt_gauge').val('".$gaugeId."');\n";
		echo "$('#txt_gmts_no').val('".$gmts_noId."');\n";
		echo "$('#txt_wo_qty').val(".$yarn_wo_qty.");\n";
		//echo "$('#hdn_wo_qty').val(".$yarn_wo_qty.");\n";
		echo "$('#txt_dyeing_charge').val(".$dyeing_charge.");\n";
		echo "$('#txt_job_no').val('".$job_ref."');\n";
		echo "$('#txt_start_date').val('".$start_date."');\n";
		echo "$('#txt_end_date').val('".$end_date."');\n";
		echo "fnc_calculate();\n";
		echo "$('#dtls_update_id').val(".$dtls_updateId.");\n";
		echo "set_button_status(1, permission, 'fnc_sub_contract_wo',1,0);\n"; */
	}
}

if($action=="subcon_wo_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo "$company";die;
	$select_field_grp="group by a.id,a.supplier_name order by supplier_name";

	$current_date=date('d-m-Y');
	$previous_day=date("d-m-Y",strtotime(date("d-m-Y"). '-60 days'));
	//echo $current_date."##".$previous_day;die;
	?>
	<script>
		function set_checkvalue()
		{
			if(document.getElementById('chk_job_wo_po').value==0)
				document.getElementById('chk_job_wo_po').value=1;
			else
				document.getElementById('chk_job_wo_po').value=0;
		}
		function js_set_value(id)
		{
			$("#hidden_sys_number").val(id);
	//$("#hidden_id").val(id);
	parent.emailwindow.hide();
}
</script>
</head>
<body>
<div style="width:930px;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="830" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th colspan="5">
						<? echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" ); ?>
					</th>
					<th colspan="3" style="text-align:right"><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">WO Without Job</th>
				</tr>
				<tr>
					<th width="120">Buyer Name</th>
					<th width="130">Supplier Name</th>
					<th width="100">WO No</th>
					<th width="100">Job No</th>
					<th width="130" colspan="2">Date Range</th>
					<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('searchorderfrm_1','search_div','','','','');"  /></th>
				</tr>
			</thead>
			<tbody>
				<tr class="general">
					<td> <? echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", "", "",0); ?>
				</td>
				<td>
				<? 
					if($pay_mode==5 || $pay_mode==3)
					{
						echo create_drop_down( "cbo_supplier_name", 130, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Supplier --", "", "",0,"" );
					}
					else
					{
						echo create_drop_down( "cbo_supplier_name", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company c where a.id=c.supplier_id and c.tag_company=$company and a.status_active =1 and a.id in(select supplier_id from lib_supplier_party_type where party_type=2) and a.id in(select supplier_id from lib_supplier_party_type where party_type=21) group by a.id,a.supplier_name order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 ); 
					}
					
					?>
				</td>
			<td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
			
			<td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:100px"></td>
			<td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"/></td>
			<td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" /> </td>
			<td>
				<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('chk_job_wo_po').value, 'create_sys_search_list_view', 'search_div', 'sub_contract_work_order_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" />
			</td>
		</tr>
		<tr>
			<td align="center" valign="middle" colspan="7">
				<? echo load_month_buttons(1);  ?>
				<input type="hidden" id="hidden_sys_number" value="hidden_sys_number" />
				<input type="hidden" id="hidden_id" value="hidden_id" />
			</td>
		</tr>
	</tbody>
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

if($action=="create_sys_search_list_view")
{
	$ex_data = explode("_",$data);
	$supplier = $ex_data[0];
	$fromDate = $ex_data[1];
	$toDate = $ex_data[2];
	$company = $ex_data[3];
	$buyer_val=$ex_data[4];
	$chk_job_wo_po=trim($ex_data[9]);

	$job_ids=implode(",",$job_id_arr);
	if($job_ids!="") $job_ids_cond="and d.id in($job_ids)";else $job_ids_cond="";
		
	if( $supplier!=0 )  $supplier="and a.supplier_id='$supplier'"; else  $supplier="";
	if( $company!=0 )  $company=" and a.company_id='$company'"; else  $company="";
	if( $buyer_val!=0 )  $buyer_cond="and d.buyer_name='$buyer_val'"; else  $buyer_cond="";
	if($db_type==0)
	{
		$booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$ex_data[8]";
		$year_cond=" and SUBSTRING_INDEX(d.insert_date, '-', 1)=$ex_data[8]";
		if( $fromDate!=0 && $toDate!=0 ) $sql_cond= "and a.booking_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
	}
	else if($db_type==2)
	{
		$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$ex_data[8]";
		$year_cond=" and to_char(d.insert_date,'YYYY')=$ex_data[8]";
		if( $fromDate!=0 && $toDate!=0 ) $sql_cond= "and a.booking_date  between '".change_date_format($fromDate,'mm-dd-yyyy','/',1)."' and '".change_date_format($toDate,'mm-dd-yyyy','/',1)."'";
	}

	if($ex_data[5]==4 || $ex_data[5]==0)
	{
		if (str_replace("'","",$ex_data[7])!="") $job_cond=" and d.job_no_prefix_num like '%$ex_data[7]%' $year_cond "; else  $job_cond="";
		if (str_replace("'","",$ex_data[6])!="") $booking_cond=" and a.subcon_wo_suffix_num like '%$ex_data[6]%'  $booking_year_cond  "; else $booking_cond="";
	}
	else if($ex_data[5]==1)
	{
		if (str_replace("'","",$ex_data[7])!="") $job_cond=" and d.job_no_prefix_num ='$ex_data[7]' "; else  $job_cond="";
		if (str_replace("'","",$ex_data[6])!="") $booking_cond=" and a.subcon_wo_suffix_num ='$ex_data[6]'   "; else $booking_cond="";
	}
	else if($ex_data[5]==2)
	{
		if (str_replace("'","",$ex_data[7])!="") $job_cond=" and d.job_no_prefix_num like '$ex_data[7]%'  $year_cond"; else  $job_cond="";
		if (str_replace("'","",$ex_data[6])!="") $booking_cond=" and a.subcon_wo_suffix_num like '$ex_data[6]%'  $booking_year_cond  "; else $booking_cond="";
	}
	else if($ex_data[5]==3)
	{
		if (str_replace("'","",$ex_data[7])!="") $job_cond=" and d.job_no_prefix_num like '%$ex_data[7]'  $year_cond"; else  $job_cond="";
		if (str_replace("'","",$ex_data[6])!="") $booking_cond=" and a.subcon_wo_suffix_num like '%$ex_data[6]'  $booking_year_cond  "; else $booking_cond="";
	}

	if($db_type==0) $select_year="year(a.insert_date) as year"; else $select_year="to_char(a.insert_date,'YYYY') as year";
	if($chk_job_wo_po==1)
	{
		$sql = "select a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode, a.source, a.attention, $select_year, 0 as job_id, null as job_no, 0 as buyer_name, null as po_number
		from subcon_wo_mst a
		where a.status_active=1 and a.is_deleted=0 and a.entry_form=643 and a.id not in(select mst_id from subcon_wo_dtls where job_id>0 and entry_form=643  and status_active=1 and  is_deleted=0) $company $supplier  $sql_cond  $booking_cond order by a.id DESC";
	}
	else
	{

		$sql = "select a.id, a.subcon_wo_suffix_num, a.SUCON_WO_NO, a.company_id, a.supplier_id, a.booking_date, a.CLOSING_DATE, a.currency, a.EXCHANGE_RATE, a.pay_mode,a.source, a.attention,TO_CHAR(a.insert_date,'YYYY') as year, d.buyer_name, LISTAGG(CAST(b.job_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no) as job_no, LISTAGG(CAST(b.job_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_id) as job_id from subcon_wo_mst a,subcon_wo_dtls b, wo_po_details_master d where a.id=b.mst_id and b.job_no = d.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and a.entry_form=643 and b.entry_form=643 $company $supplier  $sql_cond  $buyer_cond  $job_cond $booking_cond $job_ids_cond group by  a.id, a.subcon_wo_suffix_num, a.SUCON_WO_NO, a.company_id, a.supplier_id, a.booking_date, a.CLOSING_DATE, a.currency, a.EXCHANGE_RATE, a.pay_mode,a.source, a.attention,a.insert_date,d.buyer_name order by a.id DESC";


		$nameArray=sql_select( $sql );
		$all_job_id="";
		foreach($nameArray as $row)
		{
			$all_job_id.=$row[csf("job_id")].",";
		}
		//echo $all_job_id;die;


		$all_job_id=chop($all_job_id,",");
		if($all_job_id!="")
		{
			$all_job_id=array_chunk(array_unique(explode(",",$all_job_id)),999);

			$po_sql="select p.mst_id as mst_id, b.id, b.po_number,b.grouping as ref_no from subcon_wo_dtls p, wo_po_details_master a, wo_po_break_down b where p.job_id=a.id and a.job_no=b.job_no_mst and b.status_active=1 and b.is_deleted=0";
			$p=1;
			foreach($all_job_id as $job_id)
			{
				//$po_sql
				if($p==1) $po_sql .=" and (a.id in(".implode(',',$job_id).")"; else $po_sql .=" or a.id in(".implode(',',$job_id).")";
				$p++;
			}
			$po_sql .=")";

			$po_result=sql_select($po_sql);
			$po_data=array();
			foreach($po_result as $row)
			{
				$po_data[$row[csf("mst_id")]].=$row[csf("po_number")].",";
				$po_ref_arr[$row[csf("mst_id")]].=$row[csf("ref_no")].",";
			}
		}
	}

	?>
	<div style="width:830px;" align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table" >
			<thead>
				<th width="50">SL</th>
				<th width="120">WO no</th>
				<th width="60">Year</th>
				<th width="120">Job No</th>
				<th width="140">Buyer Name</th>
				<th width="140">Service Company</th>
				<th width="100">WO Date</th>
				<th >Closing Date</th>
			</thead>
		</table>
		<div style="width:830px; margin-left:3px; overflow-y:scroll; max-height:270px;" id="buyer_list_view">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="812" class="rpt_table" id="tbl_list_search" >
				<?
				$i=1;
				$nameArray=sql_select( $sql );
				//var_dump($nameArray);die;
				foreach ($nameArray as $selectResult)
				{
					$job_no=implode(",",array_unique(explode(",",$selectResult[csf("job_no")])));
					$job_id=implode(",",array_unique(explode(",",$selectResult[csf("job_id")])));
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$pay_mode_id=$selectResult[csf("pay_mode")];
					if($pay_mode_id==3 || $pay_mode_id==5)
					{
						$supplier=$company_library[$selectResult[csf('supplier_id')]];
					}
					else
					{
						$supplier=$supplier_arr[$selectResult[csf('supplier_id')]];
					}
					$ref_no=implode(",",array_unique(explode(",",chop($po_ref_arr[$selectResult[csf("id")]],","))));
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>+'_'+'<? echo $selectResult[csf('subcon_wo_suffix_num')]; ?>'); ">
						<td width="50" align="center"> <p><? echo $i; ?></p></td>
						<td width="120" align="center"><p> <? echo $selectResult[csf('SUCON_WO_NO')]; ?></p></td>
						<td width="60" align="center"><p> <? echo $selectResult[csf('year')]; ?></p></td>
						<td width="120"><p><?  echo $job_no; ?></p></td>
						<td width="140"><p> <? echo $buyer_arr[$selectResult[csf('buyer_name')]]; ?></p></td>
						<td width="140"> <p><? echo $supplier; ?></p></td>
						<td width="100"><p><? echo change_date_format($selectResult[csf('booking_date')]); ?></p></td>
						<td><p><? echo change_date_format($selectResult[csf('CLOSING_DATE')]); ?></p></td>
					</tr>
						<?
					$i++;
				}
				?>
			</table>
		</div>
	</div>
		<?
	exit();
}

if($action=="populate_master_from_data")
{

	$sql="select a.id, a.subcon_wo_suffix_num, a.sucon_wo_no, a.company_id, a.supplier_id, a.booking_date, a.brand_id, a.team_leader, a.dealing_merchant, a.currency, a.exchange_rate, a.pay_mode,a.source, a.attention, a.tenor, a.service_sweater, a.delivery_to, a.closing_date, a.mc_available, a.mc_allocate, a.prod_qnty,a.buyer_name from subcon_wo_mst a where a.id=$data";
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "load_drop_down( 'requires/sub_contract_work_order_controller', '".$row[csf("team_leader")]."', 'cbo_dealing_merchant', 'div_marchant' );\n";
		echo "load_drop_down( 'requires/sub_contract_work_order_controller', '".$row[csf("buyer_name")]."', 'load_drop_down_brand', 'brand_td' );\n";

		echo "$('#txt_booking_no').val('".$row[csf("sucon_wo_no")]."');\n";
		echo "$('#cbo_company_name').val('".$row[csf("company_id")]."');\n";
		echo "$('#cbo_supplier').val('".$row[csf("supplier_id")]."');\n";
		echo "$('#cbo_service_type').val('".$row[csf("service_sweater")]."');\n";
		echo "$('#txt_booking_date').val('".change_date_format($row[csf("booking_date")])."');\n";
		echo "$('#cbo_currency').val('".$row[csf("currency")]."');\n";
		echo "$('#txt_exchange_rate').val('".$row[csf("exchange_rate")]."');\n";
		echo "$('#cbo_pay_mode').val('".$row[csf("pay_mode")]."');\n";
		echo "$('#txt_tenor').val('".$row[csf("tenor")]."');\n";
		echo "$('#txt_attention').val('".$row[csf("attention")]."');\n";
		echo "$('#cbo_source').val('".$row[csf("source")]."');\n";
		echo "$('#cbo_buyer_name').val('".$row[csf("buyer_name")]."');\n";
		echo "$('#cbo_brand_id').val('".$row[csf("brand_id")]."');\n";
		echo "$('#cbo_team_leader').val('".$row[csf("team_leader")]."');\n";
		echo "$('#cbo_dealing_merchant').val('".$row[csf("dealing_merchant")]."');\n";
		echo "$('#txt_closing_date').val('".change_date_format($row[csf("closing_date")])."');\n";
		echo "$('#txt_mc_available').val('".$row[csf("mc_available")]."');\n";
		echo "$('#txt_mc_allocate').val('".$row[csf("mc_allocate")]."');\n";
		echo "$('#txt_delivery_to').val('".$row[csf("delivery_to")]."');\n";
		echo "$('#txt_prod').val('".$row[csf("prod_qnty")]."');\n";
		echo "set_multiselect('cbo_service_type','0','1','".($row[csf("service_sweater")])."','0');\n";
		
		echo "set_exchang('".$row[csf("currency")]."');\n";
		
		

		/* echo "load_drop_down( 'requires/sub_contract_work_order_controller', '".$row[csf("pay_mode")]."', 'load_drop_down_supplier', 'supplier_td' );\n"; */
		echo "$('#update_id').val(".$row[csf("id")].");\n"; 
	}
	exit();
}

if($action=="terms_condition_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		var permission='<? echo $permission; ?>';
		function add_break_down_tr(i)
		{
			var row_num=$('#tbl_termcondi_details tr').length-1;
			if (row_num!=i)
			{
				return false;
			}
			else
			{
				i++;

				$("#tbl_termcondi_details tr:last").clone().find("input,select").each(function() {
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						'name': function(_, name) { return name + i },
						'value': function(_, value) { return value }
					});
				}).end().appendTo("#tbl_termcondi_details");
				$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
				$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
				$('#termscondition_'+i).val("");
				$( "#tbl_termcondi_details tr:last" ).find( "td:first" ).html(i);
			}

		}

		function fn_deletebreak_down_tr(rowNo)
		{

			var numRow = $('table#tbl_termcondi_details tbody tr').length;
			if(numRow==rowNo && rowNo!=1)
			{
				$('#tbl_termcondi_details tbody tr:last').remove();
			}

		}

		function fnc_fabric_booking_terms_condition( operation )
		{
			var row_num=$('#tbl_termcondi_details tr').length-1;
			var data_all="";
			for (var i=1; i<=row_num; i++)
			{
				if (form_validation('termscondition_'+i,'Term Condition')==false)
				{
					return;
				}
				data_all=data_all+get_submitted_data_string('txt_booking_no*termscondition_'+i,"../../../");
			//alert(data_all);
		}
		var data="action=save_update_delete_fabric_booking_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
		//	alert(data);
		//freeze_window(operation);
		http.open("POST","sub_contract_work_order_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_terms_condition_reponse;
	}
	function fnc_fabric_booking_terms_condition_reponse()
	{
		if(http.readyState == 4)
		{
		   // alert(http.responseText);
		   var reponse=trim(http.responseText).split('**');
		   if (reponse[0].length>2) reponse[0]=10;
		   if(reponse[0]==0 || reponse[0]==1)
		   {
					//$('#txt_terms_condision_book_con').val(reponse[1]);
					parent.emailwindow.hide();
					set_button_status(1, permission, 'fnc_fabric_booking_terms_condition',1);
				}
			}
		}
	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<? echo load_freeze_divs ("../../../",$permission);  ?>
			<fieldset>
				<form id="termscondi_1" autocomplete="off">
					<input type="text" id="txt_booking_no" name="txt_booking_no" value="<? echo str_replace("'","",$txt_booking_no) ?>" class="text_boxes" readonly />
					<input type="hidden" id="txt_terms_condision_book_con" name="txt_terms_condision_book_con" >

					<table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
						<thead>
							<tr>
								<th width="50">Sl</th><th width="530">Terms</th><th ></th>
							</tr>
						</thead>
						<tbody>
							<?
						//echo "select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no";
						$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no order by id");// quotation_id='$data'
						if(count($data_array)>0)
						{
							$button_status=1;
							$i=0;
							foreach( $data_array as $row )
							{
								$i++;
								?>
								<tr id="settr_1" align="center">
									<td>
										<? echo $i;?>
									</td>
									<td>
										<input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>" />
									</td>
									<td>
										<input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
										<input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
									</td>
								</tr>
								<?
							}
						}
						else
						{
							$button_status=0;
							$data_array=sql_select("select id, terms from  lib_yarn_dyeing_terms_con where is_default=1 ");// quotation_id='$data'
							foreach( $data_array as $row )
							{
								$i++;
								?>
								<tr id="settr_1" align="center">
									<td>
										<? echo $i;?>
									</td>
									<td>
										<input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>" />
									</td>
									<td>
										<input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
										<input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> );" />
									</td>
								</tr>
								<?
							}
						}
						?>
					</tbody>
				</table>
				<table width="650" cellspacing="0" class="" border="0">
					<tr>
						<td align="center" height="15" width="100%"> </td>
					</tr>
					<tr>
						<td align="center" width="100%" class="button_container">
							<?
							echo load_submit_buttons( $permission, "fnc_fabric_booking_terms_condition", $button_status,0 ,"reset_form('termscondi_1','','','','')",1) ;
							?>
						</td>
					</tr>
				</table>
			</form>
		</fieldset>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="save_update_delete_fabric_booking_terms_condition")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0 || $operation==1)  // Insert Here and Update Here
	{
		$con = connect();

		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$id=return_next_id( "id", "wo_booking_terms_condition", 1 ) ;
		$field_array="id,booking_no,terms";
		for ($i=1;$i<=$total_row;$i++)
		{
			$termscondition="termscondition_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_booking_no.",".$$termscondition.")";
			$id=$id+1;
		}
		//echo "INSERT INTO wo_booking_terms_condition (".$field_array.") VALUES ".$data_array;die;
		//echo "delete from wo_booking_terms_condition where  booking_no =".$txt_booking_no."";
		$rID_de3=execute_query( "delete from wo_booking_terms_condition where  booking_no =".$txt_booking_no."",0);
		$rID=sql_insert("wo_booking_terms_condition",$field_array,$data_array,1);
		// check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $rID_de3)
			{
				mysql_query("COMMIT");
				echo $operation."**".$txt_booking_no;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$txt_booking_no;
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_de3)
			{
				oci_commit($con);
				echo $operation."**".$txt_booking_no;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$txt_booking_no;
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "show_trim_booking_report")
{
	extract($_REQUEST);
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$txt_booking_no = str_replace("'", "", $txt_booking_no);
	$cbo_company_name = str_replace("'", "", $cbo_company_name);
	$id_approved_id = str_replace("'", "", $id_approved_id);
	$report_type = str_replace("'", "", $report_type);
	$show_comment = str_replace("'", "", $show_comment);
	$cbo_template_id = str_replace("'", "", $cbo_template_id);
	$group_library=return_library_array("select id, group_name from lib_group", "id", "group_name");
	$color_library = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$size_library = return_library_array("select id, size_name from  lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$imge_arr = return_library_array("select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1", 'master_tble_id', 'image_location');
	$country_arr = return_library_array("select id,country_name from   lib_country", 'id', 'country_name');
	$supplier_name_arr = return_library_array("select id,supplier_name from   lib_supplier", 'id', 'supplier_name');
	$supplier_address_arr = return_library_array("select id,address_1 from   lib_supplier", 'id', 'address_1');
	$buyer_name_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	$order_uom_arr = return_library_array("select id,order_uom  from lib_item_group", "id", "order_uom");
	$deling_marcent_arr = return_library_array("select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
	$season_arr = return_library_array("select id,season_name from lib_buyer_season", "id", "season_name");
	$team_leader_arr=return_library_array("select id,team_leader_name from lib_marketing_team","id","team_leader_name");
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name"  );
	
	$nameArray = sql_select("select a.sucon_wo_no, a.pay_mode,a.buyer_name, a.booking_date, a.supplier_id, a.currency, a.exchange_rate, a.attention, a.closing_date, a.source,a.delivery_to,a.service_sweater,a.team_leader,a.mc_available,a.mc_allocate,a.prod_qnty,a.brand_id,a.dealing_merchant from subcon_wo_mst a where a.sucon_wo_no='$txt_booking_no' and a.status_active =1 and a.is_deleted=0");
	foreach ($nameArray as $row) {
		$varcode_booking_no = $row[csf('sucon_wo_no')];
		$booking_date = $row[csf('booking_date')];
		$closing_date = $row[csf('closing_date')];
		$pay_mode_id = $row[csf('pay_mode')];
		$supplier_id = $row[csf('supplier_id')];
		$currency_id = $row[csf('currency')];
		$exchange_rate = $row[csf('exchange_rate')];
		$attention = $row[csf('attention')];
		$source_id = $row[csf('source')];
		$delivery_add= $row[csf('delivery_to')];
		$buyer_name= $buyer_name_arr[$row[csf('buyer_name')]];
		$service_types=$row[csf('service_sweater')];
		$mc_available= $row[csf('mc_available')];
		$mc_allocate= $row[csf('mc_allocate')];
		$prod_qnty= $row[csf('prod_qnty')];
		$team_leader= $team_leader_arr[$row[csf('team_leader')]];
		$brand_ids= $brand_arr[$row[csf('brand_id')]];
		$dealing_marchant= $deling_marcent_arr[$row[csf('dealing_merchant')]];
	}
	
		$mcurrency = "";
			$dcurrency = "";
			if ($currency_id == 1) {
				$mcurrency = 'Taka';
				$dcurrency = 'Paisa';
			}
			if ($currency_id == 2) {
				$mcurrency = 'USD';
				$dcurrency = 'CENTS';
			}
			if ($currency_id == 3) {
				$mcurrency = 'EURO';
				$dcurrency = 'CENTS';
			}
 ?>
	<html>
	<div style="width:1333px" align="center">
		<table width="1333px" cellpadding="0" cellspacing="0" style="border:0px solid black">


				<table border="1" align="left" class="rpt_table container" cellpadding="0" width="100%" cellspacing="0" rules="all" style="padding-bottom: 10px">
				<tr>
					<td colspan="8" align="center"><b>Sub-Contract Work Order</b></td>
				</tr>
					<tr>
					<td width="150px"  style="border-right:0" align="left"><? if($report_type==1)
						{
							if($link == 1)

							{
						?>
									<img  src='../../../<? echo $imge_arr[$cbo_company_name]; ?>' height='40%' width='50%' />

						<?
							}
							else
							{
						?>
									<img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='40%' width='50%' />
						<?	}
						}
						else
						{ ?>
							<img  src='../<? echo $imge_arr[$cbo_company_name]; ?>' height='40%' width='50%' />
						<? }
						?></td>		
					<td width="200px" colspan="2" align="center">
					<?
				$nameArray=sql_select("select a.id,a.group_id,a.company_name,b.id,b.address from lib_company a,lib_group b  where a.id=$cbo_company_name and a.group_id=b.id");
				foreach( $nameArray as $row)
				{
				$group_address=$row[csf('address')];
				$group_name=$group_library[$row[csf('group_id')]];
				}
				?>
					<b><?=$group_name?></b></td>	
					<td  colspan="2" align="center"><b>M&M DEPARTMENT</b></td>
					<td   align="center"><b>Work Order Date:<?php echo change_date_format($booking_date); ?></b> </td>
					<td  colspan="2" align="left"><b>Party:<?=$supplier_name_arr[$supplier_id];?><hr>Address:<?=$supplier_address_arr[$supplier_id];?><hr>Attention:<?=$attention;?></b></td>	   
					</tr>
				</table>
				<table border="1" align="left" class="rpt_table container"  cellpadding="0" width="100%" cellspacing="0" rules="all" style="padding-bottom: 10px">
				<tr>
					<td colspan="2" align="left"><b>Factory Name: <?=$company_library[$cbo_company_name];?></b> </td>				   
					<td  colspan="6" align="left"><b>JOB DESCRIPTION: <? 
					$notifying_party_exp = explode(",",$service_types);
					foreach($notifying_party_exp as $notify)
					{
						$service_type_arr[$notify]=$service_type_sweaterArr[$notify];

						//echo $service_type[$notify] ;
						
					}
					echo implode("- ",$service_type_arr);
					 ?></b></td>					   
				</tr>
				<tr>
				   <td width="100" colspan="8" align="left"><b>Head Office: </b>
				   House # 103, Northern Road, Baridhara DOHS, Dhaka. Tel: +88-02-8413580, Fax: +88-02-8413579
					 
					 </td>				   
				   					   
				</tr>
				<tr>
				   <td width="100" colspan="8" align="left"><b>Factory:</b> <?
                            $nameArray=sql_select( "select id,plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                            foreach ($nameArray as $result){
                            ?>
                              <?  if($result[csf('plot_no')]!='') echo $result[csf('plot_no')]; else echo '';?> &nbsp;
                                <? echo $result[csf('level_no')];?> &nbsp;
                                 <? echo $result[csf('road_no')]; ?>  &nbsp;
                                <? echo $result[csf('block_no')];?>  &nbsp;
                                <? echo $result[csf('city')];?>  &nbsp;
                                <? echo $result[csf('zip_code')]; ?>  &nbsp;
                                 <?php echo $result[csf('province')]; ?>  &nbsp;
                                <? echo $country_arr[$result[csf('country_id')]]; ?> &nbsp;<br/>
                                <? echo $result[csf('email')];?>  &nbsp;
                                <? echo $result[csf('website')];
                                if($result[csf('plot_no')]!='')
                                {
                                    $plot_no=$result[csf('plot_no')];
                                }
                                if($result[csf('level_no')]!='')
                                {
                                    $level_no=$result[csf('level_no')];
                                }
                                if($result[csf('road_no')]!='')
                                {
                                    $road_no=$result[csf('road_no')];
                                }
                                if($result[csf('block_no')]!='')
                                {
                                    $block_no=$result[csf('block_no')];
                                }
                                if($result[csf('city')]!='')
                                {
                                    $city=$result[csf('city')];
                                }
                                $company_address[$result[csf('id')]]=$plot_no.'&nbsp'.$level_no.'&nbsp'.$road_no.'&nbsp'.$block_no.'&nbsp'.$city;
                            }
                        ?></td>		   				  
				</tr>
				<tr>
					<td width="100" align="left"> <b>Buyer’s Name:</b></td>	
					<td width="200" align="left"><b><? echo $buyer_name; ?></b></td>	
					<td width="140" align="left"><b> Team Leader  :</b></td>
					<td width="160" align="left"><b><? echo $team_leader; ?></b></td>
					<td width="200" align="left"><b> No Of MC Available  :</b></td>
					<td width="100" align="right"><b><? echo $mc_available; ?></b></td>
					<td width="50" align="left"><b>Pcs</b></td>
				</tr>
				<tr>
					<td width="100" align="left"> <b>Brand Name:</b></td>	
					<td width="200" align="left"><b><? echo $brand_ids; ?></b></td>	
					<td width="140" align="left"><b> Dealing Merchant  :</b></td>
					<td width="160" align="left"><b><? echo $dealing_marchant; ?></b></td>
					<td width="200" align="left"><b> Allocated MC Per Day  :</b></td>
					<td width="100" align="right"><b><? echo $mc_allocate; ?></b></td>
					<td width="50" align="left"><b>Pcs</b></td>
				</tr>
				<tr>
					<td width="100" align="left"> <b>Delivery Place:</b></td>	
					<td width="200" align="left"><b><? echo $delivery_add; ?></b></td>	
					<td width="140" align="left"><b> Closing Date  :</b></td>
					<td width="160" align="left"><b><? echo change_date_format($closing_date); ?></b></td>
					<td width="200" align="left"><b> Produciton Per Day  :</b></td>
					<td width="100" align="right"><b><? echo $prod_qnty; ?></b></td>
					<td width="50" align="left"><b>Pcs</b></td>
				</tr>
          	</table>
			<?
				$nameArray_item = sql_select("select b.job_no,b.style_no from subcon_wo_mst a,subcon_wo_dtls b where a.id= b.mst_id and  a.sucon_wo_no='$txt_booking_no'and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
				if (count($nameArray_item) > 0) {
			?>
					&nbsp;
					<table border="1" align="left" class="rpt_table" cellpadding="0" width="100%" cellspacing="0" rules="all">
						<tr>
							<td colspan="11" align="">
								<table width="100%" style="table-layout: fixed;">
									<tr>
										<td width="10%" align="left"><strong>Buyer Order NO:</strong></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black" align="center"><strong>Sl</strong> </td>
							<td style="border:1px solid black" align="center"><strong>Job No</strong> </td>
							<td style="border:1px solid black" align="center"><strong>Style Reff.</strong> </td>
							<td style="border:1px solid black" align="center"><strong>Gmts Item</strong> </td>
							<td style="border:1px solid black" align="center"><strong>Process Name</strong> </td>
							<td style="border:1px solid black" align="center"><strong>Req. Sweater Qty. (Pcs)</strong> </td>
							<td style="border:1px solid black" align="center"><strong>Rate/Pcs</strong></td>
							<td style="border:1px solid black" align="center"><strong>Amount</strong></td>
							<td style="border:1px solid black" align="center"><strong>Delivery Start Date</strong></td>
							<td style="border:1px solid black" align="center"><strong>Delivery End Date</strong></td>
							<td style="border:1px solid black" align="center"><strong>Days</strong></td>
						</tr>
						<?
						$i = 0;
						$grand_total_as_per_gmts_color = 0;
						foreach ($nameArray_item as $result_item) {
							$i++;
							$date_diff="(b.end_date - b.start_date)";
							$nameArray_item_description = sql_select("select $date_diff as date_diff,a.service_sweater,sum(b.wo_qty) as wo_qnty,avg(b.dyeing_charge) as rate, sum(b.amount) as amt,b.gmts_no,b.start_date,b.end_date from subcon_wo_mst a,  subcon_wo_dtls b where a.id= b.mst_id and  a.sucon_wo_no='$txt_booking_no' and b.job_no='" . $result_item[csf('job_no')] . "' and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.service_sweater,b.gmts_no,b.start_date,b.end_date");
						?>
							<tr>
								<td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description) + 1; ?>"> <? echo $i; ?></td>
								<td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description) + 1; ?>">
									<?
									echo $result_item[csf('job_no')];
									?>
								</td>
								<td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description) + 1; ?>">
									<?
									echo $result_item[csf('style_no')];
									?>
								</td>
								<?
								$item_desctiption_total = 0;
								$total_amount_as_per_gmts_color = 0;
								foreach ($nameArray_item_description as $result_itemdescription) {
								?>
									<td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('gmts_no')]; ?></td>
									<td style="border:1px solid black;text-align:center"><? 
									$service_sweater_exp = explode(",",$result_itemdescription[csf('service_sweater')]);
									foreach($service_sweater_exp as $service_sweater)
									{
										$service_type_arr[$service_sweater]=$service_type_sweaterArr[$service_sweater];
				
										//echo $service_type[$notify] ;
										
									}
									echo implode("+ ",$service_type_arr); ?></td>
									<td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('wo_qnty')], 4);
																							$item_desctiption_total += $result_itemdescription[csf('wo_qnty')]; ?></td>
									<td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')], 4); ?> </td>
									<td style="border:1px solid black; text-align:right">
										<?
										$amount_as_per_gmts_color = $result_itemdescription[csf('wo_qnty')] * $result_itemdescription[csf('rate')];
										echo number_format($amount_as_per_gmts_color, 4);
										$total_amount_as_per_gmts_color += $amount_as_per_gmts_color;
										?>
									</td>
									<td style="border:1px solid black;text-align:center"><? echo change_date_format($result_itemdescription[csf('start_date')]); ?></td>
									<td style="border:1px solid black;text-align:center"><? echo change_date_format($result_itemdescription[csf('end_date')]); ?></td>
									<td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('date_diff')]; ?></td>
							</tr>
						<?
								}
						?>
						<tr>
							<td style="border:1px solid black;  text-align:right" colspan="2"><strong> Item Total</strong></td>
							<td style="border:1px solid black;  text-align:right; font-weight:bold;"><? echo number_format($item_desctiption_total, 4); ?></td>
							<td style="border:1px solid black; text-align:right"></td>
							<td style="border:1px solid black; text-align:right">
								<? echo number_format($total_amount_as_per_gmts_color, 2);
								$grand_total_as_per_gmts_color += $total_amount_as_per_gmts_color;

								$booking_grand_total += $total_amount_as_per_gmts_color;
								$total_amount_as_per_gmts_color = 0;
								?>

							</td>
							<td style="border:1px solid black; text-align:right"></td>
							<td style="border:1px solid black; text-align:right"></td>
						</tr>
					<?
						}
					?>
					<tr>
						<td align="right" style="border:1px solid black" colspan="7"><strong>Total</strong></td>
						<td style="border:1px solid black; text-align:right"><? echo number_format($grand_total_as_per_gmts_color, 2);  ?></td>
						<td style="border:1px solid black; text-align:right"></td>
						<td style="border:1px solid black; text-align:right"></td>
					</tr>
					</table>
				<?
				} //}
				?>

			<table width="100%" style="margin-top:1px">
				<tr>
					<td>
						<table width="100%" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all">
							<tr style="border:1px solid black;">
								<td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount</td>
								<td width="70%" style="border:1px solid black; text-align:left"><? echo number_format($booking_grand_total, 2); ?></td>
							</tr>
							<tr style="border:1px solid black;">
								<td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount (in word)</td>
								<td width="70%" style="border:1px solid black;"><? echo number_to_words(def_number_format($booking_grand_total, 2, ""), $mcurrency, $dcurrency); ?></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<? 
		$mst_id=$txt_booking_no; $width="100%"; $entry_form=643;
		
			if ($entry_form != '') {$entry_form_con = " and entry_form=$entry_form";}
			$data_array = sql_select("select id, terms from  wo_booking_terms_condition where   booking_no='" . str_replace("'", "", $mst_id) . "' $entry_form_con   order by id asc");
			$tot_row=count($data_array)/2;
			//echo $tot_row;
			$k=1;
			foreach($data_array as $row)
			{
				if($k<=$tot_row)
				{
				$term_bookingArr[$row[csf('id')]]['terms']=$row[csf('terms')];
				}
				else
				{
				$other_term_bookingArr[$row[csf('id')]]['terms']=$row[csf('terms')];	
				}
				$k++;
			}
			
	if (count($data_array) > 0) {
		?>
        <br>
        <table align="left"  width="<?=$width;?>" align="center"   border="0" cellpadding="0" cellspacing="0" >
        <tr>
        <td valign="top">
        
        <table   width="650" class="rpt_table"   align="center"  border="1" cellpadding="0" cellspacing="0" rules="all">
        <thead>
            <tr style="border:1px solid black;">
            <th width="3%" style="border:1px solid black;">Sl</th>
            <th width="45%" style="border:1px solid black;">Special Instruction</th>
            </tr>
        </thead>
        <tbody>
		<?
		
			//print_r($term_bookingArr);
		$sl=1;
				foreach ($term_bookingArr as $term=>$row) {
					?>
					<tr id="settr_1" align="" style="border:1px solid black;">
					<td align="center" style="border:1px solid black;text-align:center"><?=$sl;?></td>
				   <td style="border:1px solid black; font-weight:bold"><?=$row['terms'];?></td>
					<?
					$sl++;
					}
				
		?>
	</tbody>
	</table>
    </td>
    <!--1st part end-->
    <?
	$sl2=$sl;
    if (count($other_term_bookingArr) > 0) {
	?>
		<td valign="top">
        	<table  width="650" class="rpt_table"   align="center"  border="1" cellpadding="0" cellspacing="0" rules="all">
        <thead>
            <tr style="border:1px solid black;">
            <th width="3%" style="border:1px solid black;" >Sl</th>
            <th width="45%" style="border:1px solid black;">Special Instruction</th>
            </tr>
        </thead>
        <tbody>
		<?
				foreach ($other_term_bookingArr as $term2=>$row2) {
					?>
					<tr id="settr_2" align="" style="border:1px solid black;">
					<td align="center" style="border:1px solid black; text-align:center"><?=$sl2;?></td>
				   <td style="border:1px solid black; font-weight:bold"><?=$row2['terms'];?></td>
					<?
					$sl2++;
					}
				
		?>
	</tbody>
	</table>
    
        </td> 
        <?
	}
		?>   
    </tr>
    </table>
    <?
	}
	?>	
			
			
		</table>
		<?
		// image show here  -------------------------------------------
		$sql_img = "select id, master_tble_id, image_location from common_photo_library where form_name='print_booking_multijob' and master_tble_id ='$txt_booking_no' ";
		$data_array = sql_select($sql_img);
		?>
		<div align="left" style="margin:5px 2px;float:left;width:100%">
			<? foreach ($data_array as $inf) { ?>
				<img src='../../<? echo $inf[csf("image_location")]; ?>' height='70' width='80' />
			<? } ?>
		</div>
	</div> 
	<!--class="footer_signature 133"-->
	<div style="margin-top:-5px;"><? echo signature_table(210, $cbo_company_name, "1300px", $cbo_template_id); ?></div>
	<br>
	<div id="page_break_div"></div>
	<div><? echo "****" . custom_file_name($txt_booking_no, $style_sting, $job_no); ?></div>
	<?
	if ($link == 1) {
	?>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
		<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<?
	} else {
	?>
		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<?
	}
	?>
	<script>
		fnc_generate_Barcode('<? echo $varcode_booking_no; ?>', 'barcode_img_id');
	</script>

	</html>
 <?
	exit();
}

if ($action == "show_trim_booking_report2")
{
	extract($_REQUEST);
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$txt_booking_no = str_replace("'", "", $txt_booking_no);
	$cbo_company_name = str_replace("'", "", $cbo_company_name);
	$id_approved_id = str_replace("'", "", $id_approved_id);
	$report_type = str_replace("'", "", $report_type);
	$show_comment = str_replace("'", "", $show_comment);
	$cbo_template_id = str_replace("'", "", $cbo_template_id);
	$group_library=return_library_array("select id, group_name from lib_group", "id", "group_name");
	$color_library = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$size_library = return_library_array("select id, size_name from  lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$company_email_library = return_library_array("select id, email from lib_company", "id", "email");
	$imge_arr = return_library_array("select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1", 'master_tble_id', 'image_location');
	$country_arr = return_library_array("select id,country_name from   lib_country", 'id', 'country_name');
	$supplier_name_arr = return_library_array("select id,supplier_name from   lib_supplier", 'id', 'supplier_name');
	$supplier_address_arr = return_library_array("select id,address_1 from   lib_supplier", 'id', 'address_1');
	$buyer_name_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	$order_uom_arr = return_library_array("select id,order_uom  from lib_item_group", "id", "order_uom");
	$deling_marcent_arr = return_library_array("select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
	$season_arr = return_library_array("select id,season_name from lib_buyer_season", "id", "season_name");
	$team_leader_arr=return_library_array("select id,team_leader_name from lib_marketing_team","id","team_leader_name");
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name"  );
	
	$nameArray = sql_select("select a.sucon_wo_no, a.pay_mode,a.buyer_name, a.booking_date, a.supplier_id, a.currency, a.exchange_rate, a.attention, a.closing_date, a.source,a.delivery_to,a.service_sweater,a.team_leader,a.mc_available,a.mc_allocate,a.prod_qnty,a.brand_id,a.dealing_merchant from subcon_wo_mst a where a.sucon_wo_no='$txt_booking_no' and a.status_active =1 and a.is_deleted=0");
	foreach ($nameArray as $row) {
		$varcode_booking_no = $row[csf('sucon_wo_no')];
		$booking_date = $row[csf('booking_date')];
		$closing_date = $row[csf('closing_date')];
		$pay_mode_id = $row[csf('pay_mode')];
		$supplier_id = $row[csf('supplier_id')];
		$currency_id = $row[csf('currency')];
		$exchange_rate = $row[csf('exchange_rate')];
		$attention = $row[csf('attention')];
		$source_id = $row[csf('source')];
		$delivery_add= $row[csf('delivery_to')];
		$buyer_name= $buyer_name_arr[$row[csf('buyer_name')]];
		$service_types=$row[csf('service_sweater')];
		$mc_available= $row[csf('mc_available')];
		$mc_allocate= $row[csf('mc_allocate')];
		$prod_qnty= $row[csf('prod_qnty')];
		$team_leader= $team_leader_arr[$row[csf('team_leader')]];
		$brand_ids= $brand_arr[$row[csf('brand_id')]];
		$dealing_marchant= $deling_marcent_arr[$row[csf('dealing_merchant')]];
	}
	
		$mcurrency = "";
			$dcurrency = "";
			if ($currency_id == 1) {
				$mcurrency = 'Taka';
				$dcurrency = 'Paisa';
			}
			if ($currency_id == 2) {
				$mcurrency = 'USD';
				$dcurrency = 'CENTS';
			}
			if ($currency_id == 3) {
				$mcurrency = 'EURO';
				$dcurrency = 'CENTS';
			}
 ?>
	<html>
	<div style="width:960" align="center">
		<table width="960px" cellpadding="0" cellspacing="0" style="border:0px solid black">
				<table border="1" align="left" class="rpt_table container" cellpadding="0" width="950" cellspacing="0" rules="all" style="padding-bottom: 10px">
				<tr>
					<td width="150px"  style="border-right:0" align="left"><? if($report_type==1)
						{
							if($link == 1)

							{
						?>
							<img  src='../../../<? echo $imge_arr[$cbo_company_name]; ?>' height='40%' width='50%' />

						<?
							}
							else
							{
						?>
							<img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='40%' width='50%' />
						<?	}
						}
						else
						{ ?>
							<img  src='../<? echo $imge_arr[$cbo_company_name]; ?>' height='40%' width='50%' />
						<? }
						?></td>		
					<td colspan="3" align="center"><b><?=$company_library[$cbo_company_name];?></b><br>
					<?
                            $nameArray=sql_select( "select id,plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                            foreach ($nameArray as $result){
                            ?>
                              <?  if($result[csf('plot_no')]!='') echo $result[csf('plot_no')]; else echo '';?> &nbsp;
                                <? echo $result[csf('level_no')];?> &nbsp;
                                <? echo $result[csf('road_no')]; ?>  &nbsp;
                                <? echo $result[csf('block_no')];?>  &nbsp;
                                <? echo $result[csf('city')];?>  &nbsp;
                                <? echo $result[csf('zip_code')]; ?>  &nbsp;
                                <?php echo $result[csf('province')]; ?>  &nbsp;
                                <? echo $country_arr[$result[csf('country_id')]]; ?> &nbsp;<br/>
                                <? echo $result[csf('email')];?>  &nbsp;
                                <? echo $result[csf('website')];
                                if($result[csf('plot_no')]!='')
                                {
                                    $plot_no=$result[csf('plot_no')];
                                }
                                if($result[csf('level_no')]!='')
                                {
                                    $level_no=$result[csf('level_no')];
                                }
                                if($result[csf('road_no')]!='')
                                {
                                    $road_no=$result[csf('road_no')];
                                }
                                if($result[csf('block_no')]!='')
                                {
                                    $block_no=$result[csf('block_no')];
                                }
                                if($result[csf('city')]!='')
                                {
                                    $city=$result[csf('city')];
                                }
                                $company_address[$result[csf('id')]]=$plot_no.'&nbsp'.$level_no.'&nbsp'.$road_no.'&nbsp'.$block_no.'&nbsp'.$city;
                            }
                        ?>
					</td>   	   			
					</tr>
				<tr>
					<td colspan="4" align="center"><b>Sub-Contract Work Order</b></td>
				</tr>
				<tr>
					<td width="200"><span><b>To </b></span></td>
					<td width="350">&nbsp;<span></span></td>
					<td width="200"><span><b>WO Number</b></span></td>
					<td width="200"><span> :&nbsp;<b><? echo $varcode_booking_no; ?></b></span></td>
				</tr>
				<tr>
					<td width="200"><span><b>Party Name </b></span></td>
					<td width="350">&nbsp;<?=$supplier_name_arr[$supplier_id];?><span></span></td>
					<td width="200"><span><b>WO Date </b></span></td>
					<td width="200"><span> :&nbsp;<b><? echo $booking_date; ?></b></span></td>
				</tr>
				<tr>
					<td width="200"><span><b>Address </b></span></td>
					<td width="350">&nbsp;<?=$supplier_address_arr[$supplier_id];?><span></span></td>
					<td width="200"><span><b>Currency </b></span></td>
					<td width="200"><span> :&nbsp;<b><? echo $currency[$currency_id]; ?></b></span></td>
				</tr>
				<tr>
					<td width="200"><span><b>Attention </b></span></td>
					<td width="350">&nbsp;<?=$attention;?><span></span></td>
					<td width="200"><span><b>Exchange Rate </b></span></td>
					<td width="200"><span> :&nbsp;<b><? echo $exchange_rate; ?></b></span></td>
				</tr>
				<tr>
					<td width="200"><span><b>Cell </b></span></td>
					<td width="350">&nbsp;01830466642<span></span></td>
					<td width="200"><span><b>Pay Mode </b></span></td>
					<td width="200"><span> :&nbsp;<b><? echo $pay_mode[$pay_mode_id]; ?></b></span></td>
				</tr>
				<tr>
					<td width="200"><span><b>Email </b></span></td>
					<td width="350">&nbsp;<?=$company_email_library[$cbo_company_name];?><span></span></td>
					<td width="200"><span><b>Delivery Place </b></span></td>
					<td width="200"><span> :&nbsp;<b><? echo $delivery_add; ?></b></span></td>
				</tr>
				</table>
				<table border="1" align="left" class="rpt_table container"  cellpadding="0" width="950" cellspacing="0" rules="all" style="padding-bottom: 10px">
				<tr>				   
					<td  colspan="4" align="left"><b>JOB DESCRIPTION: <? 
					$notifying_party_exp = explode(",",$service_types);
					foreach($notifying_party_exp as $notify)
					{
						$service_type_arr[$notify]=$service_type_sweaterArr[$notify];
					}
					echo implode("- ",$service_type_arr);
					 ?></b></td>					   
				</tr>
				<tr>
					<td width="200"><span><b>Buyer’s Name</b></span></td>
					<td width="350">:&nbsp;<? echo $buyer_name; ?><span></span></td>
					<td width="200"><span><b>No Of MC Available </b></span></td>
					<td width="200"><span> :&nbsp;<b><? echo $mc_available; ?></b></span></td>
				</tr>
				<tr>
					<td width="200"><span><b>Dealing Merchant</b></span></td>
					<td width="350">:&nbsp;<? echo $dealing_marchant; ?><span></span></td>
					<td width="200"><span><b>Allocated MC Per Day </b></span></td>
					<td width="200"><span> :&nbsp;<b><? echo $mc_allocate; ?></b></span></td>
				</tr>
				<tr>
					<td width="200"><span><b>Closing Date</b></span></td>
					<td width="350">:&nbsp;<? echo change_date_format($closing_date); ?><span></span></td>
					<td width="200"><span><b>Produciton Per Day </b></span></td>
					<td width="200"><span> :&nbsp;<b><? echo $prod_qnty; ?></b></span></td>
				</tr>
          	</table>
			<?
				$nameArray_item = sql_select("select b.job_no,b.style_no from subcon_wo_mst a,subcon_wo_dtls b where a.id= b.mst_id and  a.sucon_wo_no='$txt_booking_no'and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
				if (count($nameArray_item) > 0) {
			?>
					&nbsp;
					<table border="1" align="left" class="rpt_table" cellpadding="0" width="950" cellspacing="0" rules="all">
						<tr>
							<td colspan="11" align="">
								<table width="100%" style="table-layout: fixed;">
									<tr>
										<td width="10%" align="left"><strong>Work Order Details:</strong></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black" align="center"><strong>Sl</strong> </td>
							<td style="border:1px solid black" align="center"><strong>Job No</strong> </td>
							<td style="border:1px solid black" align="center"><strong>Style Reff.</strong> </td>
							<td style="border:1px solid black" align="center"><strong>Gmts Item</strong> </td>
							<td style="border:1px solid black" align="center"><strong>Gage</strong> </td>
							<td style="border:1px solid black" align="center"><strong>Total WOQ Qty. (Pcs)</strong> </td>
							<td style="border:1px solid black" align="center"><strong>Rate/Pcs</strong></td>
							<td style="border:1px solid black" align="center"><strong>Amount</strong></td>
							<td style="border:1px solid black" align="center"><strong>Delivery Start Date</strong></td>
							<td style="border:1px solid black" align="center"><strong>Delivery End Date</strong></td>
							<td style="border:1px solid black" align="center"><strong>Days</strong></td>
						</tr>
						<?
						$i = 0;
						$grand_total_as_per_gmts_color = 0;
						foreach ($nameArray_item as $result_item) {
							$i++;
							$date_diff="(b.end_date - b.start_date)";
							$nameArray_item_description = sql_select("select $date_diff as date_diff,a.service_sweater,sum(b.wo_qty) as wo_qnty,avg(b.dyeing_charge) as rate, sum(b.amount) as amt,b.gmts_no,b.gauge,b.start_date,b.end_date from subcon_wo_mst a,  subcon_wo_dtls b where a.id= b.mst_id and  a.sucon_wo_no='$txt_booking_no' and b.job_no='" . $result_item[csf('job_no')] . "' and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.service_sweater,b.gmts_no,b.start_date,b.end_date,b.gauge");
						?>
							<tr>
								<td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description) + 1; ?>"> <? echo $i; ?></td>
								<td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description) + 1; ?>">
									<?
									echo $result_item[csf('job_no')];
									?>
								</td>
								<td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description) + 1; ?>">
									<?
									echo $result_item[csf('style_no')];
									?>
								</td>
								<?
								$item_desctiption_total = 0;
								$total_amount_as_per_gmts_color = 0;
								foreach ($nameArray_item_description as $result_itemdescription) {
								?>
									<td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('gmts_no')]; ?></td>
									<td style="border:1px solid black;text-align:center"><? echo $gauge_arr[$result_itemdescription[csf('gauge')]]; ?></td>
									<td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('wo_qnty')], 4);
																							$item_desctiption_total += $result_itemdescription[csf('wo_qnty')]; ?></td>
									<td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')], 4); ?> </td>
									<td style="border:1px solid black; text-align:right">
										<?
										$amount_as_per_gmts_color = $result_itemdescription[csf('wo_qnty')] * $result_itemdescription[csf('rate')];
										echo number_format($amount_as_per_gmts_color, 4);
										$total_amount_as_per_gmts_color += $amount_as_per_gmts_color;
										?>
									</td>
									<td style="border:1px solid black;text-align:center"><? echo change_date_format($result_itemdescription[csf('start_date')]); ?></td>
									<td style="border:1px solid black;text-align:center"><? echo change_date_format($result_itemdescription[csf('end_date')]); ?></td>
									<td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('date_diff')]; ?></td>
							</tr>
						<?
								}
						?>
						<tr>
							<td style="border:1px solid black;  text-align:right" colspan="2"><strong> Item Total</strong></td>
							<td style="border:1px solid black;  text-align:right; font-weight:bold;"><? echo number_format($item_desctiption_total, 4); ?></td>
							<td style="border:1px solid black; text-align:right"></td>
							<td style="border:1px solid black; text-align:right">
								<? echo number_format($total_amount_as_per_gmts_color, 2);
								$grand_total_as_per_gmts_color += $total_amount_as_per_gmts_color;

								$booking_grand_total += $total_amount_as_per_gmts_color;
								$total_amount_as_per_gmts_color = 0;
								?>

							</td>
							<td style="border:1px solid black; text-align:right"></td>
							<td style="border:1px solid black; text-align:right"></td>
							<td style="border:1px solid black; text-align:right"></td>
						</tr>
					<?
						}
					?>
					<tr>
						<td align="right" style="border:1px solid black" colspan="7"><strong>Total</strong></td>
						<td style="border:1px solid black; text-align:right"><? echo number_format($grand_total_as_per_gmts_color, 2);  ?></td>
						<td style="border:1px solid black; text-align:right"></td>
						<td style="border:1px solid black; text-align:right"></td>
						<td style="border:1px solid black; text-align:right"></td>
					</tr>
					</table>
				<?
				} //}
				?>

			<table width="950" style="margin-top:1px" align="left">
				<tr>
					<td>
						<table align="left" width="100%" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all">
							<tr style="border:1px solid black;">
								<td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount</td>
								<td width="70%" style="border:1px solid black; text-align:left"><? echo number_format($booking_grand_total, 2); ?></td>
							</tr>
							<tr style="border:1px solid black;">
								<td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount (in word)</td>
								<td width="70%" style="border:1px solid black;"><? echo number_to_words(def_number_format($booking_grand_total, 2, ""), $mcurrency, $dcurrency); ?></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<? 
		    $mst_id=$txt_booking_no; $width="950"; $entry_form=643;
		
			if ($entry_form != '') {$entry_form_con = " and entry_form=$entry_form";}
			$data_array = sql_select("select id, terms from  wo_booking_terms_condition where   booking_no='" . str_replace("'", "", $mst_id) . "' $entry_form_con   order by id asc");
			$tot_row=count($data_array)/2;
			//echo $tot_row;
			$k=1;
			foreach($data_array as $row)
			{
				$term_bookingArr[$row[csf('id')]]['terms']=$row[csf('terms')];

				// if($k<=$tot_row)
				// {
				// $term_bookingArr[$row[csf('id')]]['terms']=$row[csf('terms')];
				// }
				// else
				// {
				// $other_term_bookingArr[$row[csf('id')]]['terms']=$row[csf('terms')];	
				// }
				// $k++;
			}
			
	if (count($data_array) > 0) {
		?>
        <br>
        <table align="left" width="<?=$width;?>" border="0" cellpadding="0" cellspacing="0" >
        <tr>
        <td valign="top">
			<table   width="470" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all">
			<thead>
				<tr style="border:1px solid black;">
					<th width="3%" style="border:1px solid black;">Sl</th>
					<th width="45%" style="border:1px solid black;">Special Instruction</th>
				</tr>
			</thead>
        <tbody>
		<? 
		// print_r($term_bookingArr);
		$sl=1;
		foreach ($term_bookingArr as $term=>$row) {
		?>
		    <tr id="settr_1" align="" style="border:1px solid black;">
		        <td align="center" style="border:1px solid black;text-align:center"><?=$sl;?></td>
		        <td style="border:1px solid black; font-weight:bold"><?=$row['terms'];?></td>
		    </tr>
		<?
		$sl++;
		}	
		?>
	</tbody>
	</table>
    </td>
    <!--1st part end-->
    <?
	///$sl2=$sl;
   // if (count($other_term_bookingArr) > 0) {
	?>
		<!-- <td valign="top">
        	<table  width="470" class="rpt_table"   align="center"  border="1" cellpadding="0" cellspacing="0" rules="all">
        <thead>
            <tr style="border:1px solid black;">
				<th width="3%" style="border:1px solid black;">Sl</th>
				<th width="45%" style="border:1px solid black;">Special Instruction</th>
            </tr>
        </thead>
        <tbody> -->
		<?
		//foreach ($other_term_bookingArr as $term2=>$row2) {
		?>
			<!-- <tr id="settr_2" align="" style="border:1px solid black;">
				<td align="center" style="border:1px solid black; text-align:center">< ?=$sl2;?></td>
				<td style="border:1px solid black; font-weight:bold">< ?=$row2['terms'];?></td>
			</tr> -->
		<?
		//$sl2++;
	//	}
				
		?>
	</tbody>
	</table>
    
        </td> 
        <?
	}
		?>   
    </tr>
    </table>
    <?
	// }
	?>	
			
			
		</table>
		<?
		// image show here  -------------------------------------------
		$sql_img = "select id, master_tble_id, image_location from common_photo_library where form_name='print_booking_multijob' and master_tble_id ='$txt_booking_no' ";
		$data_array = sql_select($sql_img);
		?>
		<div align="left" style="margin:5px 2px;float:left;width:100%">
			<? foreach ($data_array as $inf) { ?>
				<img src='../../<? echo $inf[csf("image_location")]; ?>' height='70' width='80' />
			<? } ?>
		</div>
	</div>
	<!--class="footer_signature"-->
	<div style="margin-top:-5px;"><? echo signature_table(210, $cbo_company_name, "950px", $cbo_template_id); ?></div>
	<br>
	<div id="page_break_div"></div>
	<div><? echo "****" . custom_file_name($txt_booking_no, $style_sting, $job_no); ?></div>
	<?
	if ($link == 1) {
	?>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
		<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<?
	} else {
	?>
		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<?
	}
	?>
	<script>
		fnc_generate_Barcode('<? echo $varcode_booking_no; ?>', 'barcode_img_id');
	</script>

	</html>
 <?
	exit();
}
if ($action=="rate_dtls_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);

	//echo "SELECT a.currency,a.booking_date,a.exchange_rate FROM subcon_wo_mst a,subcon_wo_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_id='$job_id'";die;

	$rate_sql=sql_select("select a.insert_date,a.exchange_rate from wo_pre_cost_mst a where a.status_active=1 and a.is_deleted=0  and a.job_id='$job_id'");
	//$rate_sql = sql_select("SELECT a.currency,a.booking_date,a.exchange_rate FROM subcon_wo_mst a,subcon_wo_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_id='$job_id'");

	//echo "<pre>";
	//print_r($rate_sql);die;

	foreach ($rate_sql as $row)
	{
		$usd_id=2;
		$transaction_date=$row[csf('booking_date')];
		$currencyId=$row[csf('currency')];
		$exchange_rate=$row[csf('exchange_rate')];
		$conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
		$currency_rated=set_conversion_rate($usd_id,$conversion_date );
			if($currencyId==1){
				$rate=$exchange_rate*$currency_rated;
			}else{
				$rate=$exchange_rate;
			}
	}
	unset($rate_sql);

	//echo "select id, job_no, particular_id, cost from wo_pre_cost_cm_cost_dtls where status_active=1 and is_deleted=0 and job_id='$job_id' order by id";die;

	//echo $rate;die;

	$budget_data_array=sql_select("select id, job_no, particular_id, cost from wo_pre_cost_cm_cost_dtls where status_active=1 and is_deleted=0 and job_id='$job_id' order by id");

	// print_r($budget_data_array);die;
	$budget_cost=0;
	foreach($budget_data_array as $row){
		$budget_cost+=$row[csf("cost")]*$rate;
	}
	//echo $rate;die;

	?>
	<script>
		function set_sum_value(des_fil_id,field_id,table_id){
			var rowCount = $('#tbl_cm_cost tr').length-1;
			var ddd={ dec_type:1, comma:0, currency:1}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd);
			//document.getElementById('txtratecm_sum').value=document.getElementById('txtratecm_sum').value;
		} 
		function calculate_cm_cost_dtls(i)
		{
			set_sum_value( 'txtratecm_sum', 'txtcmrate_', 'tbl_cm_cost' );
		}
		function js_set_value(){
			//alert('YRS'); // Service Cost Can't Be Greater Then Budget cost(0)
			var budget_cost=$('#hidden_budget_cost').val()*1;
			var total_rate=$('#txtratecm_sum').val()*1;
			if(total_rate>budget_cost){
				alert("Service Cost Can't Be Greater Then Budget cost("+budget_cost+")");
				return;
			}
			var rowCount = $('#tbl_cm_cost tr').length-1;
			var rate_breck_down="";
			for(var i=1; i<=rowCount; i++){
				var unitcharge = $('#txtcmrate_'+i).val();
				if(trim(unitcharge) ==''){
					unitcharge=0;
				}
				if(rate_breck_down==""){
					rate_breck_down=$('#txtparticular_'+i).val()+'$$'+unitcharge;
				}
				else{
					rate_breck_down+="__"+$('#txtparticular_'+i).val()+'$$'+unitcharge;
				}
			}
			document.getElementById('rate_breck_down').value=rate_breck_down;
			document.getElementById('total_rate').value=total_rate;
			parent.emailwindow.hide();
		}
	</script>
		<div id="content_cm_cost">
        <fieldset style="width:350px">
            <form id="cm_cost_form" autocomplete="off">
				<input type="hidden" id="rate_breck_down" />
				<input type="hidden" id="total_rate" />
            <table width="350" cellspacing="0" class="rpt_table" border="0" id="tbl_cm_cost" rules="all">
                <thead>
                    <tr>
                    	<th width="30">SL</th>
                        <th width="200">Particular</th>
                        <th width="100">Cost</th>
                    </tr>
                </thead>
                <tbody>
					<?
					$total_cost=0;
                    $data_array=explode("__",$rate_break_down);
                    if( count($data_array)>0 && $rate_break_down!='')
                    {
						$i=1;
						$z=0;
                        foreach ($cm_cost_particular_arr as $key => $value) {
							$cost_data=explode("$$",$data_array[$z]);
                        ?>
                        <tr id="cmcost_<?echo $i?>" align="center">
                            <td><? echo $key; ?></td>
                            <td><? echo $value; ?>
                            <input type="hidden" id="txtparticular_<? echo $key; ?>" name="txtparticular_<? echo $key; ?>" value="<? echo $key; ?>"/>
                            <input type="hidden" id="txtcmdtlsid_<? echo $key; ?>" name="txtcmdtlsid_<? echo $key; ?>"  value="" />
                            </td>
                            <td>
                            <input type="text" id="txtcmrate_<? echo $key; ?>" name="txtcmrate_<? echo $key; ?>" class="text_boxes_numeric" style="width:90px" onChange="calculate_cm_cost_dtls( <? echo $key;?> )" value="<?= $cost_data[1] ?>" />
                            </td>                            
                        </tr>
                        <?
							$total_cost+=$cost_data[1];							
							$i++;
							$z++;
                        }
                    }
                    else
                    {	
						if(count($budget_data_array)>0){				
							$i=0;
							foreach( $budget_data_array as $row )
							{
								$i++;
								?>
								<tr id="cmcost_<?echo $i?>" align="center">
									<td><? echo $i; ?></td>
									<td><? echo $cm_cost_particular_arr[$row[csf('particular_id')]] ?>
									<input type="hidden" id="txtparticular_<? echo $i; ?>" name="txtparticular_<? echo $i; ?>"  value="<? echo $row[csf("particular_id")]; ?>" />
									</td>
									<td>
									<input type="text" id="txtcmrate_<? echo $i; ?>" name="txtcmrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px" onChange="calculate_cm_cost_dtls( <? echo $i;?> )" value="<? echo $row[csf("cost")]; ?>"/>
									</td>
									
								</tr>
								<?
								$total_cost+=$row[csf("cost")];
								
							}
						}
						else{ ?>
							<tr><td colspan="3" align="center"><span style="color:red;font-size:medium">No Data Found in Budget</span></td></tr>
						<? }
                    }
                    ?>
                </tbody>
            </table>
            <table width="350" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    <tr>
                        <th width="30">&nbsp;</th>
                        <th width="200">Sum :</th>
                        <th width="100"><input type="text" id="txtratecm_sum" name="txtratecm_sum" class="text_boxes_numeric" value="<?= $total_cost ?>" style="width:90px" readonly />
						<input type="" id="hidden_budget_cost" value="<?= $budget_cost ?>" />
						</th>
                    </tr>
					<tr><td colspan="3"></td></tr>
					<tr>
						<? if($budget_cost>0){?>
                    		
						<? } ?>

						<td align="center" colspan="3"> <input type="button" class="formbutton" value="Close" onClick="js_set_value()"/></td>
						
                	</tr>
                </tfoot>
            </table>
            </form>
        </fieldset>
	</div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

?>
