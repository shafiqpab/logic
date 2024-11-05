<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];



if ($action=="load_drop_down_working_location")
{
	//echo $data;die;
	echo create_drop_down( "wc_location_id", 100, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/date_wise_gmt_finishing_receive_report_controller', document.getElementById('wc_location_id').value+'_'+document.getElementById('working_company_id').value, 'load_drop_down_wc_company_location_wise_floor', 'wc_floor_td' )" );
	exit();

}

if($action=="load_drop_down_lc_company_location"){
	//echo $data;die;
	$data=explode("***", $data);
	if(count($data)==1)
	{
		echo create_drop_down( "lc_location_id", 90, "select id,location_name from lib_location where company_id=$data[0] and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/date_wise_gmt_finishing_receive_report_controller', document.getElementById('lc_location_id').value+'_'+document.getElementById('lc_company_id').value, 'load_drop_down_fini_company_location_wise_floor', 'fini_floor_td' )" );
	}
	else
	{

		echo create_drop_down( "lc_location_id", 90, "select id,location_name from lib_location where company_id=$data[0] and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", '', "load_drop_down( 'requires/date_wise_gmt_finishing_receive_report_controller', document.getElementById('lc_location_id').value+'_'+document.getElementById('cbo_company_mst').value, 'load_drop_down_fini_company_location_wise_floor', 'fini_floor_pop_td' )" );

	}
	exit();
}
if($action=="load_drop_down_buyer"){
	//echo $data;die;
	echo create_drop_down( "cbo_buyer_id", 90, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data'  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
	exit();
}
if($action=="load_drop_down_wc_company_location_wise_floor"){
	//echo $data;die;
	$data=explode("_", $data);
	$company_id=$data[1];
	$location_id=$data[0];
	$sql="select id,floor_name from lib_prod_floor where company_id=$company_id and location_id=$location_id and status_active =1 and is_deleted=0 and production_process =5 order by floor_name";
	//echo $sql;die;
	echo create_drop_down("wc_floor", 90, $sql,"id,floor_name", 1, "-- Select Location --", $selected, "" );
	exit();
}

if($action=="load_drop_down_fini_company_location_wise_floor"){
	//echo $data;die;
	$data=explode("_", $data);
	$company_id=$data[1];
	$location_id=$data[0];
	$sql="select id,floor_name from lib_prod_floor where company_id=$company_id and location_id=$location_id and status_active =1 and is_deleted=0 and production_process =11 order by floor_name";
	//echo $sql;die;
	echo create_drop_down("finishing_floor", 90, $sql,"id,floor_name", 1, "-- Select Floor --", $selected, "" );
	exit();
}
if($action=="job_wise_search")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $txt_job_no;
	?>
	<script>

		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]);
			$("#hide_job_no").val(splitData[1]);
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:580px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                      	<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <?
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer,"",0 );
							?>
                        </td>
                        <td align="center">
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Order No");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );//txt_job_no
						?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'**'+'<? echo $type; ?>'+'**'+'<? echo $txt_job_no; ?>', 'create_job_no_search_list_view', 'search_div', 'date_wise_gmt_finishing_receive_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}
if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$type_id=$data[5];
	$job=$data[6];

	//echo $month_id;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}
	if($job!='') $job_cond="and a.job_no_prefix_num='$job'";else $job_cond="";
	$search_by=$data[2];
	//$search_string="%".trim($data[3])."%";
	$search_value=$data[3];
	//if($search_by==2) $search_field="a.style_ref_no"; else $search_field="a.job_no";
	if($search_by==1 && $search_value!=''){
		$search_con=" and a.job_no like('%$search_value')";
	}

	else if($search_by==2 && $search_value!=''){
		$search_con=" and b.po_number like('%$search_value%')";
	}
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(a.insert_date) as year";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";

	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";
	}
	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	if($type_id==1)
	{
		$field_type="id,job_no_prefix_num";
	}

	else if($type_id==2)
	{
		$field_type="id,po_number";
	}
	 $sql= "select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.po_number, $year_field from wo_po_details_master a,wo_po_break_down b where  b.job_no_mst=a.job_no and a.status_active=1 and a.is_deleted=0 and a.company_name in($company_id) $search_con $buyer_id_cond $year_cond $job_cond  order by a.job_no";

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref.,PO No", "120,130,80,50,120,80","750","240",0, $sql , "js_set_value", "$field_type", "", 1, "company_name,buyer_name,0,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no,po_number", "",'','0,0,0,0,0,0,3','') ;
	exit();
} // Job Search end
if($action=="generate_report")
{
	//echo "test";die;
	//echo load_html_head_contents("Finish Barcode Generate", "../../", 1, 1, '', '');
	$process = array( &$_POST );
	//print_r($process);die;
	extract(check_magic_quote_gpc( $process ));
	$working_company_id = str_replace("'", "", $working_company_id);
	$wc_location_id = str_replace("'", "", $wc_location_id);
	$lc_company_id = str_replace("'", "", $lc_company_id);
	$lc_location_id = str_replace("'", "", $lc_location_id);
	$lc_floor_id = str_replace("'", "", $lc_floor_id);
	$wc_floor = str_replace("'", "", $wc_floor);
	$buyer_id = str_replace("'", "", $cbo_buyer_id);
	
	$production_date_condition='';
	if (str_replace("'", "", $txt_date_from) != "" || str_replace("'", "", $txt_date_to) != "") {
		if ($db_type == 0) {
			$start_date = change_date_format(str_replace("'", "", $txt_date_from), "yyyy-mm-dd", "");
			$end_date = change_date_format(str_replace("'", "", $txt_date_to), "yyyy-mm-dd", "");
		} else if ($db_type == 2) {
			$start_date = change_date_format(str_replace("'", "", $txt_date_from), "", "", 1);
			$end_date = change_date_format(str_replace("'", "", $txt_date_to), "", "", 1);
		}
		$production_date_condition = " and b.production_date between '$start_date' and '$end_date'";
	}

	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name");
	$color_arr = return_library_array("select id, color_name from lib_color",'id','color_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$floor_arr=return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$item_arr=return_library_array( "select id, item_name from lib_garment_item",'id','item_name');


	$sql_cond="";
	$sql_cond .= ($lc_company_id!=0) ? "and a.fini_company_id=$lc_company_id" : "";
	$sql_cond .= ($lc_location_id!=0) ? "and a.fini_location_id=$lc_location_id" : "";
	$sql_cond .= ($lc_floor_id!=0) ? "and a.floor_id=$lc_floor_id" : "";
	$sql_cond .= ($buyer_id!=0) ? "and a.buyer_id=$cbo_buyer_id" : "";
	// $sql_cond .= ($txt_date!="") ? "and a.receive_date='$txt_date'" : "";
	if (str_replace("'","",$txt_job_no)=="") $job_cond_id=""; else $job_cond_id="and d.job_no_prefix_num=".trim($txt_job_no)."";
	if (str_replace("'","",$txt_po_no)=="") $po_order_cond=""; else $po_order_cond="and c.po_number=".trim($txt_po_no)."";

	// $sql_cond .= ($lc_floor_id!=0) ? "and a.floor_id=$lc_floor_id" : "";

	$sql="SELECT c.shipment_date,d.style_ref_no,b.country_id,c.packing,b.color_id,b.size_id,c.po_quantity,d.job_no,c.po_number,b.line_id,b.fin_receive_qnty,e.article_number,a.floor_id from gmt_finishing_receive_mst a, gmt_finishing_receive_dtls b,wo_po_break_down c,wo_po_details_master d,wo_po_color_size_breakdown e where a.id=b.mst_id and b.po_break_down_id=c.id and c.job_id=d.id and c.id=e.po_break_down_id and d.id=e.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $sql_cond $production_date_condition $job_cond_id $po_order_cond";

	//  echo $sql;
	 $mainsql=sql_select($sql); $data_array=array();
	 foreach($mainsql as $row)
	 {

		$data_array[$row[csf('po_number')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['qty']=$row[csf('po_quantity')];
		$data_array[$row[csf('po_number')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['fin_qty']=$row[csf('fin_receive_qnty')];

	 }
    //  echo '<pre>';
	//  print_r($data_array);
	//  echo '</pre>';
     $sqlcolorsize="SELECT (CASE WHEN b.production_type ='4' THEN a.production_qnty END) AS sewinginput,c.po_number,d.color_number_id,d.size_number_id,d.country_id from  pro_garments_production_dtls a, pro_garments_production_mst b,wo_po_break_down c,wo_po_color_size_breakdown d
	 where a.mst_id=b.id and a.color_size_break_down_id=d.id and d.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and b.production_type in(4) $po_order_cond";
	//  echo $sqlcolorsize;

	$mainsqlcolor=sql_select($sqlcolorsize); $colorsizearr=array();
	foreach($mainsqlcolor as $v)
	{

		$colorsizearr[$v[csf('po_number')]][$v[csf('country_id')]][$v[csf('color_number_id')]][$v[csf('size_number_id')]]['sewinginput']=$v[csf('sewinginput')];

	}
    //    echo '<pre>';
	//  print_r($colorsizearr);
	//  echo '</pre>';
	?>

<br><br>

		<fieldset style="width:1720px";>
				 <div style="width:1720px;">
					<table width="1700px"  cellspacing="0">
						<tr class="form_caption" style="border:none;">
							<td colspan="17" align="center" style="border:none;font-size:18px; font-weight:bold">Date Wise Gmts Finishing Receive report</td>
						</tr>
						<tr class="form_caption" style="border:none;">
							<td colspan="17" align="center" style="border:none;font-size:15px; font-weight:bold" >Date: <?=$start_date;?> To <?=$end_date;?></td>
						</tr>
					</table>
				  </div>
			<!-------------------------------- Header Part ------------------------------------->
				<div class="tbl-header" style="width:1720px">
					<table class="rpt_table" width="1700" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
							<tr>
								<th width="100">Shipment Date</th>
								<th width="100">Style</th>
								<th width="100">Job</th>
								<th width="100">Po</th>
								<th width="100">Country</th>
								<th width="100">Packing System</th>
								<th width="100">Art No</th>
								<th width="100">Sewing Floor</th>
								<th width="100">Line No</th>
								<th width="100">Color</th>
								<th width="100">Size</th>
								<th width="100">Order Qty In Pcs</th>
								<th width="100">SewingInput Qty</th>
								<th width="100">Input Bal</th>
								<th width="100">Rcv.qty in pcs</th>
								<th width="100">Bal.qty in pcs</th>
								<th width="100">Bal.qty in pcs From Rcv</th>
							</tr>
						</thead>
						<!----- -------------------Body Part------------------------------ -->
							<tbody>
							<?
							  $grpoorder=0;
							  $grposewinginput=0;
							  $grinputbal=0;
							  $grrcvqty=0;
							  $grbalqty=0;
							  $grbalqtyrcv=0;
							  $i=1;
							foreach($data_array as $po_number=>$po_data)
							{
								$poorder=0;
								$posewinginput=0;
								$poinputbal=0;
								$porcvqty=0;
								$pobalqty=0;
								$pobalqtyrcv=0;

								foreach($po_data as $country_id=>$country_data)
								{
									foreach($country_data as $color_id=>$color_data)
									{
										foreach($color_data as $size_id=>$val)
										{
											if ($i%2==0)
											 $bgcolor="#E9F3FF";
										   else
											 $bgcolor="#FFFFFF";

							?>
								<tr bgcolor="<?=$bgcolor;?>">
									<td width="100"><? echo $row[csf('shipment_date')];?></td>
									<td width="100"><? echo $row[csf('style_ref_no')];?></td>
									<td width="100"><? echo $row[csf('job_no')];?></td>
									<td width="100"><? echo $po_number; ?></td>
									<td width="100"><?  echo $country_library[$country_id];?></td>
									<td width="100"><? echo $row[csf('packing')];?></td>
									<td width="100"><? echo $row[csf('article_number')];?></td>
									<td width="100"><? echo $floor_arr[$row[csf('floor_id')]];?></td>
									<td width="100"><? echo $row[csf('line_id')]; ?></td>
									<td width="100"><? echo $color_arr[$color_id];?></td>
									<td width="100"><? echo $size_arr[$size_id];?></td>
									<td width="100" align="right"><? echo $data_array[$po_number][$country_id][$color_id][$size_id]['qty'];?></td>
									<td width="100" align="right"><? echo $v[csf('sewinginput')]; ?></td>
									<td width="100" align="right"><? $inputbal=$data_array[$po_number][$country_id][$color_id][$size_id]['qty']-$v[csf('sewinginput')];echo $inputbal; ?></td>
									<td width="100" align="right"><? echo $data_array[$po_number][$country_id][$color_id][$size_id]['fin_qty'];?></td>
									<td width="100" align="right"><? $balqty=$v[csf('sewinginput')]-$data_array[$po_number][$country_id][$color_id][$size_id]['fin_qty']; echo $balqty; ?></td>
									<td width="100" align="right"><? $balqtyrcv=$data_array[$po_number][$country_id][$color_id][$size_id]['qty']-$data_array[$po_number][$country_id][$color_id][$size_id]['fin_qty']; echo $balqtyrcv;?></td>
									
									
								</tr>
							</tbody>
						
							<?
							   $grpoorder+=$data_array[$po_number][$country_id][$color_id][$size_id]['qty'];
							   $grposewinginput+=$v[csf('sewinginput')];
							   $grpoinputbal+=$inputbal;
							   $grrcvqty+=$data_array[$po_number][$country_id][$color_id][$size_id]['fin_qty'];
							   $grbalqty+=$balqty;
							   $grbalqtyrcv+=$balqtyrcv;


								}
							}
						}
						?>
						   <!-- <div class="tbl-header" style="width:1720px">
								<table class="rpt_table" width="1700" cellpadding="0" cellspacing="0" border="1" rules="all">
									<tfoot>
										<tr class="gd-color">
											<th width="100"></th>
											<th width="100"></th>
											<th width="100"></th>
											<th width="100"></th>
											<th width="100"></th>
											<th width="100"></th>
											<th width="100"></th>
											<th width="100"></th>
											<th width="100"></th>
											<th width="100"></th>
											<th width="100">PO Total</th>
											<th width="100"><? $poorder+=$data_array[$po_number][$country_id][$color_id][$size_id]['qty']; echo $poorder; ?></th>
											<th width="100"><? $posewinginput+=$v[csf('sewinginput')]; echo $posewinginput;?></th>
											<th width="100"><? $poinputbal+=$inputbal;echo $poinputbal; ?></th>
											<th width="100"><?$porcvqty+=$data_array[$po_number][$country_id][$color_id][$size_id]['fin_qty']; echo $porcvqty;?></th>
											<th width="100"><? $pobalqty+=$balqty;echo $pobalqty;?></th>
											<th width="100"><?  $pobalqtyrcv+=$balqtyrcv;echo $pobalqtyrcv;  ?></th>
										</tr>
									</tfoot>
								 </table>
							 </div> -->
					<?
					}
					?>
						<div class="tbl-header" style="width:1720px">
							<table class="rpt_table" width="1700" cellpadding="0" cellspacing="0" border="1" rules="all">
								<tfoot>
									<tr class="gd-color">
										<th width="100"></th>
										<th width="100"></th>
										<th width="100"></th>
										<th width="100"></th>
										<th width="100"></th>
										<th width="100"></th>
										<th width="100"></th>
										<th width="100"></th>
										<th width="100"></th>
										<th width="100"></th>
										<th width="100">Grand Total</th>
										<th width="100"><? echo $grpoorder;?></th>
										<th width="100"><? echo $grposewinginput;?></th>
										<th width="100"><? echo $grpoinputbal; ?></th>
										<th width="100"><? echo $grrcvqty; ?></th>
										<th width="100"><? echo $grbalqty;?></th>
										<th width="100"><? echo $grbalqtyrcv;?></th>
									</tr>
								</tfoot>
							</table>
						</div>

			</fieldset>
     <script type="text/javascript">
    	setFilterGrid("scanning_tbl",-1);
    </script>
  <?php
  exit();

}




















































?>