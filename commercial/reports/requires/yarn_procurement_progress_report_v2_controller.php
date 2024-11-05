<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

if ($action=="load_drop_down_buyer")
{
    extract($_REQUEST);
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($data) and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,2)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", "1", "-- Select Buyer --", $selected, "",0);
	exit();
}

if($action=="req_such_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	//echo $style_id;die;
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
		
    </script>
    </head>
    <body>
        <div align="center">
            <form name="searchwofrm"  id="searchwofrm" autocomplete=off>
                <fieldset style="width:98%;">
                    <h3 align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Enter search words</h3>
                    <div id="content_search_panel" >
                        <table cellpadding="0" cellspacing="0" width="100%" class="rpt_table" border="1" rules="all">
                            <thead>
                                <th>Buyer</th>
                                <th>Requisition No</th>
                                <th>Requisition Date</th>
                                <th>
                                    <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                                    <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $company; ?>">
                                    <input type="hidden" name="cbo_year" id="cbo_year" class="text_boxes" value="<? echo $cbo_year; ?>">
                                    <input type='hidden' id='txt_selected_id' />
                                    <input type='hidden' id='txt_selected' />
                                    <input type='hidden' id='txt_selected_no' />
                                </th>
                            </thead>
                            <tr class="general">
                                <td align="center">
                                    <?
                                    $buyer_cond="";
									if($cbo_buyer_name) $buyer_cond=" and buy.id in ($cbo_buyer_name)";
                                    $buyer_sql = "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($company) $buyer_cond and buy.id in (select buyer_id from  lib_buyer_party_type where party_type in (1,2)) group by buy.id, buy.buyer_name order by buyer_name";
                                    echo create_drop_down( "cbo_buyer", 150, $buyer_sql,"id,buyer_name", 1, "-- Select Buyer --", $cbo_buyer_name, "" );
                                    ?>
                                </td>
                                <td align="center">
                                     <input type="text" name="txt_req_no" id="txt_req_no" class="text_boxes" style="width:100px">
                                </td>
                                <td align="center">
                                    <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                                    <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                                </td>
                                <!--accordion_menu(accordion_h1.id,'content_search_panel','')-->
                                <td align="center">
                                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer').value+'_'+document.getElementById('txt_req_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_year').value, 'create_req_search_list_view', 'search_div', 'yarn_procurement_progress_report_v2_controller', 'setFilterGrid(\'list_view\',-1);');" style="width:100px;" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" align="center"  valign="middle"><? echo load_month_buttons(1); ?></td>
                            </tr>
                        </table>
                    </div>
                    <table width="100%" style="margin-top:5px">
                        <tr>
                            <td colspan="5">
                                <div style="width:100%; margin-top:3px; margin-left:3px" id="search_div" align="left"></div>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </form>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	
	exit();
}

if($action=="create_req_search_list_view")
{
	$data = explode("_",$data);
	$cbo_buyer		= trim($data[0]);
	$txt_req_no		= trim($data[1]);
	$date_from		= trim($data[2]);
	$date_to 		= trim($data[3]);
	$company_ids	= trim($data[4]);
	$req_year 		= trim($data[5]);
	$buyer_arr 		= return_library_array( "select id, short_name from lib_buyer",'id','short_name');

	$sql_cond="";
	if($cbo_buyer) $sql_cond=" and b.buyer_id=$cbo_buyer";
	if($txt_req_no!="") $sql_cond.=" and a.requ_no like '%$txt_req_no'";
	if($company_ids!="") $sql_cond.=" and a.company_id in($company_ids)";
	if($date_from!="" && $date_to!="")
	{
		if($db_type==0)
		{
			$sql_cond.=" and a.requisition_date between '".change_date_format(trim($date_from), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($date_to), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$sql_cond.=" and a.requisition_date between '".change_date_format(trim($date_from),'','',1)."' and '".change_date_format(trim($date_to),'','',1)."'";
		}
	}
	

	$sql= "select a.id, a.requ_no, a.requisition_date, b.buyer_id  
	from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b 
	where a.id=b.mst_id and a.entry_form=70 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond
	group by a.id, a.requ_no, a.requisition_date, b.buyer_id";
	$arr=array(0=>$buyer_arr);
	echo create_list_view("list_view", "Buyer, Requisition No, Requisition Date","200,200","645","260",0, $sql , "js_set_value", "id,requ_no", "", 1, "buyer_id,0,0", $arr, "buyer_id,requ_no,requisition_date", "","","0,0,3","",1);	
	/*//echo $sql;die;
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="645" class="rpt_table">
		<thead>
			<th width="50">SL</th>
			<th width="150">Buyer</th>
			<th width="150">Requisition No</th>
			<th>Requisition Date</th>
		</thead>
	</table>
	<div style="width:745px; max-height:265px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="725" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;
			$result = sql_select($sql);
			foreach ($result as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$data=$row[csf('id')].'__'.$row[csf('booking_no_prefix_num')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $data; ?>','<? echo $row[csf('is_approved')]; ?>')">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="65" align="center"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
					<td width="65" align="center"><? echo $company_arr[$row[csf('company_id')]]; ?></td>
					<td width="90" align="center"><? echo $row[csf('booking_no')]; ?></td>
					<td width="50" align="center"><? echo $row[csf('booking_no_prefix_num')]; ?></td>
					<td width="90" align="center"><? echo $row[csf('job_no')]; ?></td>
					<td width="110" align="center"><? echo $row[csf('style_ref_no')]; ?></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="80" align="center"><? echo change_date_format($apporved_date_arr[$row[csf('id')]]); ?></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
					<td width="70" align="center"><? echo $currency[$row[csf('currency_id')]]; ?></td>
					<td width="60" align="center"><? echo ($row[csf('is_approved')]==1)? "Yes":"No"; ?></td>
					<td style="word-break: break-all;"><? echo $po_no; ?></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<?*/
	exit();
}

if($action=="report_generate_summary")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	$yarnCount_arr=return_library_array( "select id,yarn_count from  lib_yarn_count",'id','yarn_count');
	//("cbo_company_name*cbo_year*cbo_based_on*txt_search_no*txt_date_from*txt_date_to","../../")var data="action=report_generate_summary"+get_submitted_data_string("cbo_company_name*cbo_year*cbo_buyer_name*txt_req_no*txt_req_id*txt_date_from*txt_date_to","../../")+'&report_title='+report_title;
	
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_year=str_replace("'","",$cbo_year);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_req_no=str_replace("'","",$txt_req_no);
	$txt_req_id=str_replace("'","",$txt_req_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	
	
	$str_cond="";
	if($txt_req_id!="") $str_cond.=" and a.id in($txt_req_id)";
	if($cbo_buyer_name>0) $str_cond.=" and b.buyer_id = $cbo_buyer_name ";
	if($txt_date_from!="" && $txt_date_to!="") $str_cond.=" and a.requisition_date between '$txt_date_from' and '$txt_date_to'";
	if($cbo_year>0)
	{
		if($db_type==0) $str_cond.=" and year(a.insert_date)='$cbo_year'"; else $str_cond.=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
	}
	$sql_req="select a.id as req_id, a.requ_no, a.requisition_date, b.id as dtls_id, b.buyer_id, b.job_no, b.count_id, b.composition_id, b.yarn_type_id, b.quantity as req_qnty, b.amount as req_amt, c.id as po_id, c.pub_shipment_date, c.po_quantity, c.po_total_price as po_amount
	from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, wo_po_break_down c
	where a.id=b.mst_id and b.job_no=c.job_no_mst and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id in($cbo_company_name) and a.entry_form=70 $str_cond 
	order by a.requ_no asc, c.pub_shipment_date desc";
	//echo $sql_req;//die;
	$req_result=sql_select($sql_req);
	$requisition_arr=$total_data=$dtls_data=array();
	foreach($req_result as $row)
	{
		$requisition_arr[$row[csf("req_id")]]["req_id"]=$row[csf("req_id")];
		$requisition_arr[$row[csf("req_id")]]["requ_no"]=$row[csf("requ_no")];
		$requisition_arr[$row[csf("req_id")]]["requisition_date"]=$row[csf("requisition_date")];
		if($req_ship_data_check[$row[csf("req_id")]][$row[csf("pub_shipment_date")]]=="")
		{
			$req_ship_data_check[$row[csf("req_id")]][$row[csf("pub_shipment_date")]]=$row[csf("pub_shipment_date")];
			$requisition_arr[$row[csf("req_id")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
		}
		$requisition_arr[$row[csf("req_id")]]["buyer_id"]=$row[csf("buyer_id")];
		$req_ref=$row[csf("count_id")]."__".$row[csf("composition_id")]."__".$row[csf("yarn_type_id")];
		if($dtls_id_check[$row[csf("dtls_id")]]=="")
		{
			$dtls_id_check[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
			$dtls_data[$req_ref][$row[csf("req_id")]]+=$row[csf("req_qnty")];
			$requisition_arr[$row[csf("req_id")]]["req_qnty"]+=$row[csf("req_qnty")];
			$requisition_arr[$row[csf("req_id")]]["req_amt"]+=$row[csf("req_amt")];
		}
		if($req_order_check[$row[csf("req_id")]][$row[csf("po_id")]]=="")
		{
			$req_order_check[$row[csf("req_id")]][$row[csf("po_id")]]=$row[csf("po_id")];
			$requisition_arr[$row[csf("req_id")]]["po_quantity"]+=$row[csf("po_quantity")];
			$requisition_arr[$row[csf("req_id")]]["po_amount"]+=$row[csf("po_amount")];
		}
	}
	//echo count($requisition_arr)."<pre>";print_r($requisition_arr);die;
	//echo "<pre>";print_r($dtls_buyer_data);die;
	$table_width=400+(count($requisition_arr)*100);
	$div_width=$table_width+20;
	ob_start();
	?>
	<div style="width:<? echo $div_width ?>px" align="left">
	<table width="<? echo $table_width ?>"  align="left">
		<tr>
			<td style="font-size:18px; font-weight:bold;" class="form_caption" align="center">Yarn Procurement follow up Report</td>
		</tr>
	</table>
	<br />
	<table width="<? echo $table_width ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header"  align="left" style="margin-top:15px;">
		<tbody>
			<tr>
				<td width="50" rowspan="4" align="center" valign="middle" style="font-weight:bold; font-size:14px;">SL</td>
				<td width="350" colspan="3" align="right" style="font-weight:bold; font-size:14px;">Requisition No</td>
				<?
				foreach($requisition_arr as $req_id=>$req_val)
				{
					?>
					<td width="100" align="center"><? $req_no_str=explode("-",$req_val["requ_no"]); echo $req_no_str[0]."-".$req_no_str[3]; ?></td>
					<?
				}
				?>
				<td width="100" rowspan="4" align="center" valign="middle" style="font-weight:bold; font-size:14px;">Total</td>
			</tr>
            <tr>
				<td width="350" colspan="3" align="right" style="font-weight:bold; font-size:14px;">Requisition Date</td>
				<?
				foreach($requisition_arr as $req_id=>$req_val)
				{
					?>
					<td width="100" align="center"><? echo change_date_format($req_val["requisition_date"]); ?></td>
					<?
				}
				?>
			</tr>
            <tr>
				<td width="350" colspan="3" align="right" style="font-weight:bold; font-size:14px;">Shipment Date</td>
				<?
				foreach($requisition_arr as $req_id=>$req_val)
				{
					?>
					<td width="100" align="center"><? echo change_date_format($req_val["pub_shipment_date"]); ?></td>
					<?
				}
				?>
			</tr>
            <tr>
				<td width="100" align="center" style="font-weight:bold; font-size:14px;">Yarn Count</td>
                <td width="150" align="center" style="font-weight:bold; font-size:14px;">Composition</td>
                <td width="100" align="center" style="font-weight:bold; font-size:14px;">Type</td>
				<?
				foreach($requisition_arr as $req_id=>$req_val)
				{
					?>
					<td width="100" align="center"><p><? echo $buyer_arr[$req_val["buyer_id"]]; ?>&nbsp;</p></td>
					<?
				}
				?>
			</tr>
            <?
			$i=1;
			foreach($dtls_data as $dtls_ref=>$dtls_qnty)
			{
				if ($i%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";
				$dtls_ref_arr=explode("__",$dtls_ref);
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td align="center"><? echo $i; ?></td>
                    <td align="center" title="<? echo "count id=".$dtls_ref_arr[0];?>"><p><? echo $yarnCount_arr[$dtls_ref_arr[0]]; ?>&nbsp;</p></td>
                    <td title="<? echo "comp id=".$dtls_ref_arr[1];?>"><p><? echo $composition[$dtls_ref_arr[1]]; ?>&nbsp;</p></td>
                    <td title="<? echo "type id=".$dtls_ref_arr[2];?>"><p><? echo $yarn_type[$dtls_ref_arr[2]]; ?>&nbsp;</p></td>
                    <?
					$ref_total=0;
                    foreach($requisition_arr as $req_id=>$req_val)
                    {
                        ?>
                        <td width="100" align="right" title="<? echo $req_id;?>"><? echo number_format($dtls_qnty[$req_id],2); ?></td>
                        <?
						$ref_total+=$dtls_qnty[$req_id];
                        $buyer_wise_total[$buyer_id]+=$dtls_buyer_data[$count_id][$comp_id][$type_id][$buyer_id];
                    }
                    ?>
                    <td width="100" align="right" style="font-weight:bold; font-size:14px;"><? echo number_format($ref_total,2); $grand_ref_total+=$ref_total; ?></td>
                </tr>
                <?
				$i++;
			}
			?>
            <tr style="font-weight:bold; font-size:14px;">
				<td colspan="4" align="right">Total</td>
				<?
				$req_qnty_tot=0;
				foreach($requisition_arr as $req_id=>$req_val)
				{
					?>
					<td width="100" align="right"><? echo number_format($req_val["req_qnty"],2); ?></td>
					<?
					$req_qnty_tot+=$req_val["req_qnty"];
				}
				?>
				<td align="right"><? echo number_format($req_qnty_tot,2); ?></td>
			</tr>
            <tr style="font-weight:bold; font-size:14px;">
				<td colspan="4" align="right">Req. Order QTY</td>
				<?
				$po_qnty_tot=0;
				foreach($requisition_arr as $req_id=>$req_val)
				{
					?>
					<td width="100" align="right"><? echo number_format($req_val["po_quantity"],2); ?></td>
					<?
					$po_qnty_tot+=$req_val["po_quantity"];
				}
				?>
				<td align="right"><? echo number_format($po_qnty_tot,2); ?></td>
			</tr>
            <tr style="font-weight:bold; font-size:14px;">
				<td colspan="4" align="right">Req. Order FOB</td>
				<?
				$po_amount_tot=0;
				foreach($requisition_arr as $req_id=>$req_val)
				{
					?>
					<td width="100" align="right"><? echo number_format($req_val["po_amount"],2); ?></td>
					<?
					$po_amount_tot+=$req_val["po_amount"];
				}
				?>
				<td align="right"><? echo number_format($po_amount_tot,2); ?></td>
			</tr>
            <tr style="font-weight:bold; font-size:14px;">
				<td colspan="4" align="right">Requisition Value</td>
				<?
				$req_amt_tot=0;
				foreach($requisition_arr as $req_id=>$req_val)
				{
					?>
					<td width="100" align="right"><? echo number_format($req_val["req_amt"],2); ?></td>
					<?
					$req_amt_tot+=$req_val["req_amt"];
				}
				?>
				<td align="right"><? echo number_format($req_amt_tot,2); ?></td>
			</tr>
            <tr style="font-weight:bold; font-size:14px;">
				<td colspan="4" align="right">Percentage(%)</td>
				<?
				foreach($requisition_arr as $req_id=>$req_val)
				{
					$req_percent=($req_val["req_amt"]/$req_val["po_amount"])*100;
					?>
					<td width="100" align="right"><? echo number_format($req_percent,2); ?></td>
					<?
				}
				$tot_req_parcent=($req_amt_tot/$po_amount_tot)*100;
				?>
				<td align="right" title="<? echo $req_amt_tot."=".$po_amount_tot; ?>"><? echo number_format($tot_req_parcent,2); ?></td>
			</tr>
		</tbody>
	</table>
	</div>
	<?

	//echo "test";die;
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$total_data=ob_get_contents();
	ob_clean();
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$total_data);
	echo "$total_data####$filename####$cbo_based_on";
	die;
} 

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$yarnCount_arr=return_library_array( "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1",'id','yarn_count');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	//("cbo_company_name*cbo_year*cbo_based_on*txt_search_no*txt_date_from*txt_date_to","../../")
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_year=str_replace("'","",$cbo_year);
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$txt_search_no=str_replace("'","",$txt_search_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_receive_status=str_replace("'","",$cbo_receive_status);
	
	$str_cond=$str_cond_independ="";
	
	//echo $cbo_based_on ; die;
	// req condition check here
	if($db_type==0)
	{
		$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
		$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$txt_date_from=change_date_format($txt_date_from,'','',-1);
		$txt_date_to=change_date_format($txt_date_to,'','',-1);
	}
	
	$str_cond=$str_cond_independ=$pi_cond=$btb_cond="";
	if($cbo_based_on==1)
	{
		$str_cond.=" and a.requ_prefix_num like '%$txt_search_no%'";
		if($txt_date_from!="" && $txt_date_to!="") $str_cond.=" and a.requisition_date between '$txt_date_from' and '$txt_date_to'";
	}
	else if($cbo_based_on==2)
	{
		$str_cond.=" and d.wo_number_prefix_num like '%$txt_search_no%'";
		if($txt_date_from!="" && $txt_date_to!="")  $str_cond.=" and d.wo_date between '$txt_date_from' and '$txt_date_to'";
		$str_cond_independ.=" and d.wo_number_prefix_num like '%$txt_search_no%'";
		if($txt_date_from!="" && $txt_date_to!="") $str_cond_independ.=" and d.wo_date between '$txt_date_from' and '$txt_date_to'";
	}
	else if($cbo_based_on==3)
	{
		if ($txt_search_no != "")
		{
			$pi_cond.=" and a.pi_number like '%$txt_search_no%'";
		}
		if($txt_date_from!="" && $txt_date_to!="") $pi_cond.=" and a.pi_date between '$txt_date_from' and '$txt_date_to'";
	}
	
	
	$sql_pi=sql_select("select a.id as pi_id, a.supplier_id as pi_suplier, a.pi_number, a.pi_date, a.last_shipment_date, a.currency_id, b.work_order_id, b.work_order_dtls_id, b.color_id, b.count_name, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_composition_item2, b.yarn_composition_percentage2, b.yarn_type, b.uom, b.quantity, b.rate, b.amount from com_pi_master_details a, com_pi_item_details b 
	where a.item_category_id=1 and a.id=b.pi_id and a.importer_id=$cbo_company_name and a.status_active=1 and b.status_active=1  and a.goods_rcv_status<>1 and b.work_order_id is not null $pi_cond");
	
	$pi_data_arr=array();
	foreach($sql_pi as $row)
	{
		if($row[csf("work_order_dtls_id")]) $pi_wo_dtls_id_all[]=$row[csf("work_order_dtls_id")];
		$pi_id_arr[]=$row[csf("pi_id")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["work_order_dtls_id"]=$row[csf("work_order_dtls_id")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["pi_id"]=$row[csf("pi_id")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["pi_id_all"].=$row[csf("pi_id")].",";
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["pi_suplier"].=$row[csf("pi_suplier")].",";
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["pi_number"].=$row[csf("pi_number")].",";
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["pi_date"].=$row[csf("pi_date")].",";
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["last_shipment_date"].=$row[csf("last_shipment_date")].",";
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["currency_id"].=$row[csf("currency_id")].",";
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["work_order_id"]=$row[csf("work_order_id")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["color_id"]=$row[csf("color_id")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["count_name"]=$row[csf("count_name")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["yarn_composition_item1"]=$row[csf("yarn_composition_item1")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["yarn_composition_percentage1"]=$row[csf("yarn_composition_percentage1")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["yarn_composition_item2"]=$row[csf("yarn_composition_item2")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["yarn_comp_percent2nd"]=$row[csf("yarn_comp_percent2nd")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["yarn_type"]=$row[csf("yarn_type")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["uom"]=$row[csf("uom")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["quantity"]+=$row[csf("quantity")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["rate"]=$row[csf("rate")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["amount"]+=$row[csf("amount")];
	}
	
	if($cbo_based_on==3)
	{
		$pi_id_arr_all=array_chunk(array_unique($pi_id_arr),999);
		//$sql_btb="select a.id as btb_id, a.lc_number, a.lc_date, a.issuing_bank_id, a.payterm_id, a.tenor, a.lc_value, b.pi_id from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and a.item_category_id=1 and a.status_active=1 and b.status_active=1";
		
		$sql_btb="select a.id as btb_id, a.lc_number, a.lc_date, a.issuing_bank_id, a.payterm_id, a.tenor, a.lc_value, b.pi_id from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and a.pi_entry_form=165 and a.status_active=1 and b.status_active=1";
		
		$p=1;
		if(!empty($pi_id_arr_all))
		{
			foreach($pi_id_arr_all as $pi_id)
			{
				if($p==1) $sql_btb .=" and (b.pi_id in(".implode(',',$pi_id).")"; else $sql_btb .=" or b.pi_id in(".implode(',',$pi_id).")";
				$p++;
			}
			$sql_btb .=" ) ";
		}
		
		//echo $sql_btb;die;
	}
	else
	{
		//$sql_btb="select a.id as btb_id, a.lc_number, a.lc_date, a.issuing_bank_id, a.payterm_id, a.tenor, a.lc_value, b.pi_id from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and a.item_category_id=1 and a.status_active=1 and b.status_active=1";
		
		$sql_btb="select a.id as btb_id, a.lc_number, a.lc_date, a.issuing_bank_id, a.payterm_id, a.tenor, a.lc_value, b.pi_id from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and a.pi_entry_form=165 and a.status_active=1 and b.status_active=1";
	}
	//echo $sql_btb;die;
		
	$sql_btb_result=sql_select($sql_btb);
	$btb_data_arr=array();
	foreach($sql_btb_result as $row)
	{
		$btb_data_arr[$row[csf("pi_id")]]["pi_id"]=$row[csf("pi_id")];
		$btb_data_arr[$row[csf("pi_id")]]["btb_id"]=$row[csf("btb_id")];
		$btb_data_arr[$row[csf("pi_id")]]["btb_id_all"].=$row[csf("btb_id")].",";
		$btb_data_arr[$row[csf("pi_id")]]["lc_number"].=$row[csf("lc_number")].",";
		$btb_data_arr[$row[csf("pi_id")]]["lc_date"].=$row[csf("lc_date")].",";
		$btb_data_arr[$row[csf("pi_id")]]["issuing_bank_id"].=$row[csf("issuing_bank_id")].",";
		$btb_data_arr[$row[csf("pi_id")]]["payterm_id"].=$row[csf("payterm_id")].",";
		$btb_data_arr[$row[csf("pi_id")]]["tenor"].=$row[csf("tenor")].",";
		$btb_data_arr[$row[csf("pi_id")]]["lc_value"]+=$row[csf("lc_value")];
	}
	
	$rcv_return_sql=sql_select("select b.prod_id, a.received_id, b.cons_quantity, b.cons_quantity, b.cons_amount from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=8 and b.transaction_type=3 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	$rcv_rtn_data=array();
	foreach($rcv_return_sql as $row)
	{
		$rcv_rtn_data[$row[csf("received_id")]][$row[csf("prod_id")]]["cons_quantity"]+=$row[csf("cons_quantity")];
		$rcv_rtn_data[$row[csf("received_id")]][$row[csf("prod_id")]]["cons_amount"]+=$row[csf("cons_amount")];
	}
	
	$req_wo_recv_sql=sql_select("select a.receive_basis, a.booking_id, c.color, c.yarn_type, c.yarn_count_id, c.yarn_comp_type1st, b.order_qnty as recv_qnty, b.order_amount as recv_amt, b.mst_id, b.prod_id, b.transaction_date, a.exchange_rate 
	from  inv_receive_master a, inv_transaction b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and a.item_category=1 and a.booking_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	order by a.booking_id, a.receive_basis, c.color, c.yarn_type, c.yarn_count_id, c.yarn_comp_type1st, b.transaction_date");
	$min_date=$max_date="";
	$b=0;
	foreach($req_wo_recv_sql as $row)
	{
		if($item_check[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]=="")
		{
			$item_check[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]=$row[csf("yarn_comp_type1st")];
			$min_date=$row[csf("transaction_date")];
			$max_date=$row[csf("transaction_date")];
			$b++;
		}
		else
		{
			if(strtotime($row[csf("transaction_date")])>strtotime($max_date)) $max_date=$row[csf("transaction_date")];
		}
		$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['booking_id']=$row[csf("booking_id")];
		$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['min_date']=$min_date;
		$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['max_date']=$max_date;
		
		$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['receive_basis']=$row[csf("receive_basis")];
		$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['recv_qnty']+=$row[csf("recv_qnty")]-$rcv_rtn_data[$row[csf("mst_id")]][$row[csf("prod_id")]]["cons_quantity"];
		$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['recv_amt']+=$row[csf("recv_amt")]-($rcv_rtn_data[$row[csf("mst_id")]][$row[csf("prod_id")]]["cons_amount"]/$row[csf("exchange_rate")]);
		
	}
	//echo $b."<pre>";print_r($req_wo_recv_arr[15678][1]);die;
	
	
	$wo_qty_arr=sql_select("select a.id as wo_id, b.color_name as color, b.yarn_type as yarn_type, b.yarn_count as yarn_count_id, b.yarn_comp_type1st as yarn_comp_type1st, sum(b.supplier_order_quantity) as qty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.company_name=$cbo_company_name and a.item_category in (1) and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	group by a.id,b.color_name,b.yarn_type,b.yarn_count,b.yarn_comp_type1st");
	$wo_pipe_array=array();
	foreach($wo_qty_arr as $row)
	{
		$wo_pipe_array[$row[csf("wo_id")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]=$row[csf("qty")];
	}
	/*echo "select a.id as pi_id, b.color_id as color, b.yarn_type as yarn_type, b.count_name as yarn_count_id, b.yarn_composition_item1 as yarn_comp_type1st, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.importer_id=$cbo_company_name and a.item_category_id in (1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, b.color_id, b.yarn_type, b.count_name, b.yarn_composition_item1";die;*/
	$pi_qty_arr=sql_select("select a.id as pi_id, b.color_id as color, b.yarn_type as yarn_type, b.count_name as yarn_count_id, b.yarn_composition_item1 as yarn_comp_type1st, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.importer_id=$cbo_company_name and a.item_category_id in (1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, b.color_id, b.yarn_type, b.count_name, b.yarn_composition_item1"); 
	$pi_pipe_array=array();
	foreach($pi_qty_arr as $row)
	{
		$pi_pipe_array[$row[csf("pi_id")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]=$row[csf("qty")];
	}
	
	//echo $cbo_based_on;
	//echo "<pre>";print_r($pi_pipe_array[5710]); die;
	
	if($cbo_based_on==3)
	{
		//print_r($pi_wo_dtls_id_all);die;
		if($cbo_year>0)
		{
			if($db_type==0) $year_cond=" and year(a.insert_date)='$cbo_year'"; else $year_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
		}
		if(!empty($pi_wo_dtls_id_all))
		{
			$pi_wo_dtls_id_all_arr=array_chunk(array_unique($pi_wo_dtls_id_all),999);
			$sql_req_wo="select a.id, a.requ_no, a.requ_prefix_num, a.requisition_date, a.company_id, b.buyer_id, b.id as req_dtls_id, b.color_id, b.count_id, b.composition_id, b.com_percent, b.yarn_type_id, b.cons_uom as req_uom, b.quantity as req_qnty, b.rate as req_rate, b.amount as req_amt, b.yarn_inhouse_date, d.id as wo_id, d.wo_number_prefix_num, d.wo_date, e.id as wo_dtls_id, e.color_name as wo_color, e.yarn_count as wo_count, e.yarn_comp_type1st as wo_yarn_comp_type1st, e.yarn_comp_percent1st as wo_yarn_comp_percent1st, e.yarn_comp_type2nd, e.yarn_comp_percent2nd,  e.yarn_type as wo_yarn_type, e.uom as wo_uom, e.supplier_order_quantity as wo_qnty, e.rate as wo_rate, e.amount as wo_amount
			from inv_purchase_requisition_mst a,  inv_purchase_requisition_dtls b 
			left join  wo_non_order_info_dtls e  on  b.id=e.requisition_dtls_id and e.status_active=1 and e.is_deleted=0 
			left join wo_non_order_info_mst d on d.id=e.mst_id  and d.status_active=1 and d.is_deleted=0
			where a.id=b.mst_id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=70 and a.company_id='$cbo_company_name' $str_cond $year_cond ";
			//echo $sql_req_wo;die;
			if(!empty($pi_wo_dtls_id_all_arr))
			{
				$p=1;
				foreach($pi_wo_dtls_id_all_arr as $pi_wo_dtls_id)
				{
					if($p==1) $sql_req_wo .=" and (e.id in(".implode(',',$pi_wo_dtls_id).")"; else $sql_req_wo .=" or e.id in(".implode(',',$pi_wo_dtls_id).")";
					$p++;
				}
				$sql_req_wo .=" ) ";
			}
			$sql_req_wo .=" order by  a.id desc, b.color_id, b.yarn_type_id, b.count_id, b.composition_id ";	
		}
		
	}
	else
	{
		if($cbo_year>0)
		{
			if($db_type==0) $year_cond=" and year(a.insert_date)='$cbo_year'"; else $year_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
		}
		$sql_req_wo="select a.id, a.requ_no, a.requ_prefix_num, a.requisition_date, a.company_id, b.buyer_id, b.id as req_dtls_id, b.color_id, b.count_id, b.composition_id, b.com_percent, b.yarn_type_id, b.cons_uom as req_uom, b.quantity as req_qnty, b.rate as req_rate, b.amount as req_amt, b.yarn_inhouse_date, d.id as wo_id, d.wo_number_prefix_num, d.wo_date, e.id as wo_dtls_id, e.color_name as wo_color, e.yarn_count as wo_count, e.yarn_comp_type1st as wo_yarn_comp_type1st, e.yarn_comp_percent1st as wo_yarn_comp_percent1st, e.yarn_comp_type2nd, e.yarn_comp_percent2nd, e.yarn_type as wo_yarn_type, e.uom as wo_uom, e.supplier_order_quantity as wo_qnty, e.rate as wo_rate, e.amount as wo_amount
		from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b 
		left join  wo_non_order_info_dtls e  on  b.id=e.requisition_dtls_id and e.status_active=1 and e.is_deleted=0 
		left join wo_non_order_info_mst d on d.id=e.mst_id  and d.status_active=1 and d.is_deleted=0
		where a.id=b.mst_id and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id='$cbo_company_name' and a.entry_form=70 $str_cond $year_cond 
		order by a.id desc, b.color_id, b.count_id, b.yarn_type_id, b.composition_id";
	}
	//echo $sql_req_wo;//die;
	$req_result=sql_select($sql_req_wo);
	//echo "jahid";die;
	ob_start();
	?>
    <div style="width:4070px">
        <table width="3800" cellpadding="0" cellspacing="0" id="caption"  align="left">
        <tr>
            <td align="center" width="100%"  class="form_caption" colspan="47"><strong style="font-size:18px">Company Name:<? echo " ". $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
        </tr> 
        <tr>  
            <td align="center" width="100%" class="form_caption"  colspan="47"><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
        </tr>
        <tr>  
            <td align="center" width="100%"  class="form_caption"  colspan="47"><strong style="font-size:18px">From : <? echo $txt_date_from; ?> To : <? echo $txt_date_to; ?></strong></td>
        </tr>
        </table>
    	<br />
        <table width="3800"  align="left">
        	<tr>
            	<td style="font-size:18; font-weight:bold;">Based on Requisition</td>
            </tr>
        </table>
        <br />       
            <table width="4070" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1"  align="left">
            <thead>
            	<tr>
                	<th width="30" rowspan="2">Sl</th>
                	<th colspan="12">Requisiton Details</th>
                    <th colspan="9">Work Order Details</th>
                    <th colspan="13">PI Details</th>
                    <th colspan="6">BTB LC Details</th>
                    <th colspan="6">Matarials Received Information</th>
                </tr>
                <tr>
                    <th width="50">Req. No</th>
                    <th width="70">Req. Date</th>
                    <th width="100">Buyer</th>
                    <th width="80">Yarn Color</th>
                    <th width="50">Count</th>
                    <th width="150">Composition</th>
                    <th width="80">Yarn Type</th>
                    <th width="50">UOM</th>
                    <th width="80">Req. Qnty.</th>
                    <th width="70">Req. Rate</th>
                    <th width="100">Req. Amount</th>
                    <th width="75">Yarn Inhouse Date</th>
                    <th width="50">WO No.</th>
                    <th width="80">Yarn Color</th>
                    <th width="50">Count</th>
                    <th width="250">Composition</th>
                    <th width="80">Yarn Type</th>
                    <th width="50">UOM</th>
                    <th width="80">WO Qnty</th>
                    <th width="70">WO Rate</th>
                    <th width="100">WO Amount</th>
                    <th width="150">Supplier</th>
                    <th width="100">PI No.</th>
                    <th width="70">PI Date</th>
                    <th width="70">Last Ship Date</th>
                    <th width="80">Yarn Color</th>
                    <th width="50">Count</th>
                    <th width="250">Composition</th>
                    <th width="80">Yarn Type</th>
                    <th width="50">UOM</th>
                    <th width="80">PI Qnty</th>
                    <th width="70">PI Rate</th>
                    <th width="100">PI Amount</th>
                    <th width="70">Currency</th>
                    <th width="70">LC Date</th>
                    <th width="100">LC No</th>
                    <th width="100">Issuing Bank</th>
                    <th width="70">Pay Term</th>
                    <th width="80">Tenor</th>
                    <th width="100">LC Amount</th>
                    <th width="80">MRR Qnty</th>
                    <th width="100">MRR Value</th>
                    <th width="80">Short Value</th>
                    <th width="80">Pipe Line</th>
                    <th width="70">1st Rcv Date</th>
                    <th>Last Rcv Date</th>
                </tr>
            </thead>
        </table>
        <div style="width:4087px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
        <table width="4070px" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
            <tbody>
            <?
            $k=1;
			
			/*foreach($req_result as $val)
			{
				if($val[csf("wo_id")]>0)
				{
					$tem_arr[$val[csf("wo_id")]][$val[csf("prod_id")]]++;
				}
			}*/
			
			//var_dump($req_result);die;
			
			$array_check=array();$m=1;$q=1;
            foreach($req_result as $row)
            {
            	if($row[csf("wo_dtls_id")]=='') 
            	{
            		$row[csf("wo_dtls_id")]=0;
            	}
            	
				if(!in_array($row[csf("id")],$temp_arr_req))
				{
					$temp_arr_req[]=$row[csf("id")];
					if($m%2==0)$bgcolor="#F8F9D0";else $bgcolor="#C8C4FD"; 
					$m++;
				}
				
				$mrr_qnty=$pipe_wo_qnty=$pipe_pi_qnty=$pipe_line=0;$min_date=$max_date="";
				$mrr_value=$short_value=0;
				$booking_id=$receive_basis="";			

				$id=$row_result[csf('id')];
				$reqsn_rate=$row[csf("amount")]/$row[csf("req_quantity")];
				if($req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_amt']!="")
				{
					$wo_pi_ids=$row[csf("wo_id")];
				}
				else
				{
					$wo_pi_ids=$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"];
				}
				
				if($mrr_check_prev[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]=="")
                {
					$mrr_check_prev[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]=$row[csf("wo_id")];
					if($req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_qnty']!="")
					$mrr_qnty=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_qnty']; 
					else $mrr_qnty=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_qnty'];
					
					
					
					if($req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_amt']!="")
					{
						$mrr_value=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_amt'];
						$min_date=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['min_date'];
						$max_date=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['max_date'];
						$short_value=$row[csf("wo_amount")]-$mrr_value;
						$booking_id=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['booking_id'];
						
						$receive_basis=2;
					}
					else 
					{
						
						$mrr_value=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_amt'];
						$min_date=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['min_date'];
						$max_date=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['max_date'];
						
						$short_value=$row[csf("wo_amount")]-$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"];
						$booking_id=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['booking_id'];
						$receive_basis=1;
					}
					
				}
				
				if($pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]=="") $pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]=0;
				if($mrr_value=="") $mrr_value=0;
				if($mrr_qnty=="") $mrr_qnty=0;
				if($mrr_value>0 && $mrr_qnty>0)  $recv_rate=$mrr_value/$mrr_qnty;
				
				$receiving_cond=0;
				if($cbo_receive_status==1 && ($mrr_value=="" || $mrr_value==0)) $receiving_cond=1;
				if($cbo_receive_status==2 && $receive_basis==2 && $row[csf("wo_amount")]>$mrr_value && $mrr_value>0) $receiving_cond=1;
				if($cbo_receive_status==2 && $receive_basis==1 && $pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]>$mrr_value && $mrr_value>0) $receiving_cond=1;
				if($cbo_receive_status==3 && $receive_basis==2 && $mrr_value>=$row[csf("wo_amount")] && $mrr_value>0) $receiving_cond=1;
				if($cbo_receive_status==3 && $receive_basis==1 && $mrr_value>=$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"] && $mrr_value>0) $receiving_cond=1;
				if($cbo_receive_status==4 && $receive_basis==2 && $mrr_value < $row[csf("wo_amount")]) $receiving_cond=1;
				if($cbo_receive_status==4 && $receive_basis==1 && $mrr_value < $pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]) $receiving_cond=1;
				if($cbo_receive_status==5) $receiving_cond=1;
				
				if($receiving_cond==1)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                        <td width="30" align="center" title="<? echo $cbo_receive_status."=".$receive_basis."=".$mrr_value."=".$row[csf("wo_amount")]."=".$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]; ?>"><p><? echo $k;//$row_result[csf('id')];?></p></td>
                        <td width="50" align="center" title="<? echo $row[csf("id")]?>"><p><? echo $row[csf("requ_prefix_num")]; ?></p></td>
                        <td width="70" align="center"><p>&nbsp;<? if($row[csf("requisition_date")]!="" && $row[csf("requisition_date")]!='0000-00-00') echo change_date_format($row[csf("requisition_date")]); ?></p></td>
                        <td width="100"><p><? echo $buyer_arr[$row[csf("buyer_id")]]; ?></p></td>
                        <td width="80"><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
                        <td width="50" align="center"><p>&nbsp;<? echo $yarnCount_arr[$row[csf("count_id")]]; ?></p></td>
                        <td width="150"><p><? echo $composition[$row[csf("composition_id")]]." ".$row[csf("com_percent")]."%"; ?></p></td>
                        <td width="80"><p><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?></p></td>
                        <td width="50"><p><? echo $unit_of_measurement[$row[csf("req_uom")]]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($row[csf("req_qnty")],2); ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($row[csf("req_rate")],2); ?></p></td>
                        <td width="100" align="right"><p><? echo number_format($row[csf("req_amt")],2); ?></p></td>
                        <td width="75"  align="center"><p>&nbsp;<? if($row[csf("yarn_inhouse_date")]!="" && $row[csf("yarn_inhouse_date")]!="0000-00-00") echo change_date_format($row[csf("yarn_inhouse_date")]); ?></p></td>
                        <td width="50" align="center" title="<? echo $row[csf("wo_id")]?>"><p><? echo $row[csf("wo_number_prefix_num")]; ?></p></td>
                        <td width="80"><p><? echo $color_arr[$row[csf("wo_color")]]; ?></p></td>
                        <td width="50" align="center"><p>&nbsp;<? echo $yarnCount_arr[$row[csf("wo_count")]]; ?></p></td>
                        <td width="250"><p>
                        <? 
                        if($row[csf("yarn_comp_type2nd")]>0) $wo_com_percent2=$row[csf("yarn_comp_percent2nd")]."%"; else $wo_com_percent2=" ";
                        if ($row[csf("wo_yarn_comp_percent1st")] >0 ) $wo_com_percent1=$row[csf("wo_yarn_comp_percent1st")]."%"; else $wo_com_percent1 = "";
                        echo $composition[$row[csf("wo_yarn_comp_type1st")]]." ".$wo_com_percent1." ".$composition[$row[csf("yarn_comp_type2nd")]]." ".$wo_com_percent2; ?></p></td>
                        <td width="80"><p><? echo $yarn_type[$row[csf("wo_yarn_type")]]; ?></p></td>
                        <td width="80"><p><? echo $yarn_type[$row[csf("wo_yarn_type")]]; ?></p></td>
                        <td width="50"><p><? echo $unit_of_measurement[$row[csf("wo_uom")]]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($row[csf("wo_qnty")],2); ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($row[csf("wo_rate")],2); ?></p></td>
                        <td width="100" align="right"><p><? echo number_format($row[csf("wo_amount")],2); ?></p></td>
                       
                        <td width="150"><p><? $suplier_pi_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["pi_suplier"]," , ")));
                        //echo "10**".$row[csf("wo_dtls_id")]; die; //print_r($suplier_pi_arr);
                        $supplier='';
                        foreach ($suplier_pi_arr as $value) 
                        {
                        	$supplier.=$supplier_arr[$value].",";
                        }
                        echo chop($supplier,","); ?></p></td>
                        <td width="100"><p><? $pi_num=implode(" , ",array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["pi_number"]," , ")))); echo $pi_num; ?></p></td>
                        <td width="70"><p>
                        <? 
                        $pi_date_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["pi_date"]," , ")));
                        $pi_date='';
                        foreach ($pi_date_arr as $value) 
                        {
                        	if($value !="" && $value!="0000-00-00")
                        	{
                        		$pi_date.=change_date_format($value)."</br>";
                        	}
                        }
                        //$pi_date=implode(",",$pi_date_arr); 
                        //if($pi_date_arr[0]!="" && $pi_date_arr[0]!="0000-00-00") echo change_date_format($pi_date_arr[0]); 
                        echo chop($pi_date,"</br>");
                        ?></p></td>
                        <td width="70"><p>
                        <? 
                        $pi_last_ship_date_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["last_shipment_date"]," , "))); 

                        $shipment_date='';
                        foreach ($pi_last_ship_date_arr as $value) 
                        {
                        	if($value !="" && $value!="0000-00-00")
                        	{
                        		$shipment_date.=change_date_format($value)."</br>";
                        	}
                        }
                       	// if($pi_last_ship_date_arr[0]!="" && $pi_last_ship_date_arr[0]!="0000-00-00") echo change_date_format($pi_last_ship_date_arr[0]); 
                        echo chop($shipment_date,"</br>");
                        ?></p></td>
                        <td width="80" title="<? echo "color id=".$pi_data_arr[$row[csf("wo_dtls_id")]]["color_id"];?>"><p><? echo  $color_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["color_id"]]; ?></p></td>
                        <td width="50" title="<? echo "count id=".$pi_data_arr[$row[csf("wo_dtls_id")]]["count_name"];?>"><p>&nbsp;<? echo  $yarnCount_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["count_name"]]; ?></p></td>
                        <td width="250" title="<? echo "count id=".$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item1"];?>"><p>
                        <?
                        if($pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item2"]>0) $com_percent2=$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_comp_percent2nd"]."%"; else $com_percent2="";
                        if($pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item1"]>0) $com_percent=$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_percentage1"]."%"; else $com_percent="";
                        echo  $composition[$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item1"]]." ".$com_percent." ".$composition[$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item2"]]." ".$com_percent2;
                        ?></p></td>
                        <td width="80" title="<? echo "yarn type id=".$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_type"];?>"><p><? echo  $yarn_type[$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_type"]]; ?></p></td>
                        <td width="50"><p><? echo  $unit_of_measurement[$pi_data_arr[$row[csf("wo_dtls_id")]]["uom"]]; ?></p></td>
                        <td width="80" align="right"><p><? $pi_qnty=$pi_data_arr[$row[csf("wo_dtls_id")]]["quantity"]; echo  number_format($pi_qnty,2); ?></p></td>
                        <td width="70" align="right"><p><? $pi_rate=$pi_data_arr[$row[csf("wo_dtls_id")]]["rate"]; echo  number_format($pi_rate,2); ?></p></td>
                        <td width="100" align="right"><p><? $pi_amt=$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]; echo  number_format($pi_amt,2); ?></p></td>
                        <td width="70"><p><? $pi_curency_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["currency_id"]," , "))); 
                        $currency_val='';
                        foreach ($pi_curency_arr as $value) 
                        {
                        	$currency_val.=$currency[$value].",";
                        }
                        echo chop($currency_val,",");
                        //echo  $currency[$pi_curency_arr[0]]; ?></p></td>
                        <?
                        $btb_lc_no=$btb_issue_bank=$btb_pay_term=$btb_tenor="";$btb_lc_amount=0; $btb_tenor="";$btb_lc_date="";
                        if(!in_array($pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"],$temp_arr_btb))
                        {
                            $temp_arr_btb[]=$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"];
                            $btb_lc_no_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["lc_number"]," , ")));
                            $btb_lc_no=implode(" , ",$btb_lc_no_arr);

                            $btb_issue_bank_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["issuing_bank_id"]," , "))); 
                            foreach ($btb_issue_bank_arr as $value) 
	                        {
	                        	$btb_issue_bank.=$bank_arr[$value].",";
	                        }

                            $btb_pay_tarm_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["payterm_id"]," , ")));
	                        foreach ($btb_pay_tarm_arr as $value) 
	                        {
	                        	$btb_pay_term.=$pay_term[$value].",";
	                        }

                            $btb_tenor_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["tenor"]," , ")));
                           	$btb_tenor=implode(" Days, ",$btb_tenor_arr);

                            $btb_lc_amount=$btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["lc_value"];
                            $btb_lc_date_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["lc_date"]," , "))); 
                            $btb_lc_date='';
	                        foreach ($pi_last_ship_date_arr as $value) 
	                        {
	                        	if($value !="" && $value!="0000-00-00")
	                        	{
	                        		$btb_lc_date.=change_date_format($value)."</br>";
	                        	}
	                        }
                        }
                        else
                        {
                            $btb_lc_no=$btb_issue_bank=$btb_pay_term="";
                            $btb_lc_amount=0;
                            $btb_tenor="";
                            $btb_lc_date="";
                        }
                        ?>
                        <td width="70"><p>&nbsp;<? echo chop($btb_lc_date,"</br>");  ?></p></td>
                        
                        <td width="100"><p><?  echo chop( $btb_lc_no,","); ?></p></td>
                        <td width="100"><p><? echo  chop( $btb_issue_bank,","); ?></p></td>
                        <td width="70"><p><? echo chop( $btb_pay_term,",");  ?></p></td>
                        <td width="80"><p><? if ($btb_tenor!='') echo $btb_tenor." Days";  ?></p></td>
                        <td width="100" align="right"><p><? if($btb_lc_amount>0) echo number_format($btb_lc_amount,2); ?></p></td>
                        <?
                        //if(!in_array($row[csf("wo_id")],$temp_arr_rcv))
						$pipe_line="";
						if($mrr_check[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]=="")
                        {
							$mrr_check[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]=$row[csf("color_id")];
							$pipe_wo_qnty=$wo_pipe_array[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]];
							$pipe_pi_qnty=$pi_pipe_array[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]];
							$pipe_line=(($pipe_wo_qnty+$pipe_pi_qnty)-$mrr_qnty);
							$total_pipe_line += $pipe_line;
                            if($mrr_qnty>0)
                            { 
								$pipe_mrr_qnty=$mrr_qnty;
								
                                ?>
                                <td width="80" align="right" title="<? echo $wo_pi_ids."=".$receive_basis."=".$row[csf("color_id")]."=".$row[csf("yarn_type_id")]."=".$row[csf("count_id")]."=".$row[csf("composition_id")];?>"><p><a href="##" onClick="fn_mrr_details('<? echo $booking_id;?>','<? echo $receive_basis;?>','<? echo $row[csf("color_id")];?>','<? echo $row[csf("yarn_type_id")];?>','<? echo $row[csf("count_id")];?>','<? echo $row[csf("composition_id")];?>','receive_details_popup')"><? echo number_format($mrr_qnty,2); ?></a></p></td>
                                <?
								 $total_mrr_qnty  += $mrr_qnty;
                    			 //
                            }
                            else
                            {
                                ?>
                                <td width="80" align="right" title="<? echo $wo_pi_ids."=".$receive_basis."=".$row[csf("color_id")]."=".$row[csf("yarn_type_id")]."=".$row[csf("count_id")]."=".$row[csf("composition_id")];?>"><p></p></td>
                                <?
                            }
                        }
                        else
                        {
                            $mrr_qnty=$mrr_value=0;
							//if($mrr_check[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]=="")$pipe_mrr_qnty=0;
                            ?>
                            <td width="80" align="right" title="<? echo $wo_pi_ids."=".$receive_basis."=".$row[csf("color_id")]."=".$row[csf("yarn_type_id")]."=".$row[csf("count_id")]."=".$row[csf("composition_id")];?>"><p></p></td>
                            <?
                        }
                        ?>
                        <td width="100" align="right"><p><? if($mrr_value>0) { echo number_format($mrr_value,2); $total_mrr_value += $mrr_value; }?></p></td>
                        <td align="right" width="80"><p><? echo number_format($short_value,2); ?></p></td>
                        <?
						
                        //$pipe_line=(($pipe_wo_qnty+$pipe_pi_qnty)-$pipe_mrr_qnty);
						/*$pipe_line="";
						if($mrr_check[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]=="")
						{
							$mrr_check[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]=$row[csf("composition_id")];
							
						}*/
						?>
                        <td align="right" width="80" title="<? echo $wo_pi_ids."=".$pipe_wo_qnty."=".$pipe_pi_qnty."=".$pipe_mrr_qnty."=".$mrr_qnty; ?>"><? echo number_format($pipe_line,2); ?></td>
                        <td width="70" align="center"><? if($min_date!="" && $min_date!="0000-00-00" && $mrr_qnty>0) echo change_date_format($min_date);?></td>
                        <td align="center"><? if($max_date!="" && $max_date!="0000-00-00" && $mrr_qnty>0) echo change_date_format($max_date);?></td>
                    </tr>
                    <?
                    $k++;
                    $total_req_qty   += $row[csf("req_qnty")];
                    $total_req_amount+=$row[csf("req_amt")];
                    $total_wo_qty+=$row[csf("wo_qnty")];
                    $total_wo_amount+=$row[csf("wo_amount")];
                    $total_pi_qnty   += $pi_data_arr[$row[csf("wo_dtls_id")]]["quantity"];
                    $total_pi_amt    += $pi_data_arr[$row[csf("wo_dtls_id")]]["amount"];
                    $total_short_amt += $short_amt;
                    
				}
				
				
            }
            ?>
            </tbody>
        </table>
        </div>
        <table cellspacing="0" width="4070px"  border="1" rules="all" class="rpt_table" id="report_table_footer" >
            <tfoot>
            	<th width="30"></th>
                <th width="50" >&nbsp;</th>
                <th width="70" >&nbsp;</th>
                <th width="100" >&nbsp;</th>
                <th width="80" >&nbsp;</th>
                <th width="50" >&nbsp;</th>
                <th width="150" >&nbsp;</th>
                <th width="80" >&nbsp;</th>
                <th width="50" ><strong>Total:</strong></th>
                <th width="80" ><p><? echo number_format($total_req_qty,2);?></p></th>
                <th width="70" >&nbsp;</th>
                <th width="100" ><? echo number_format($total_req_amount,2);?></th>
                <th width="75" align="right"></th>
                <th width="50" align="center">&nbsp;</th>
                <th width="80" align="right">&nbsp;</th>
                <th width="50" align="center">&nbsp;</th>
                <th width="250" align="center">&nbsp;</th>
                <th width="80" align="center">&nbsp;</th>
                <th width="50" align="center">&nbsp;</th>
                <th width="80" align="right"><? echo number_format($total_wo_qty,2);?></th>
                <th width="70" align="right">&nbsp;</th>
                <th width="100" align="right"><? echo number_format($total_wo_amount,2);?></th>
                <th width="150" align="center">&nbsp;</th>
                <th width="100" align="center">&nbsp;</th>
                <th width="70" align="center">&nbsp;</th>
                <th width="70" align="center">&nbsp;</th>
                <th width="80" align="center">&nbsp;</th>
                <th width="50" align="center">&nbsp;</th>
                <th width="250" align="center">&nbsp;</th>
                <th width="80" align="center">&nbsp;</th>

                <th width="50" align="center"></th>
                <th width="80" align="right"><? echo number_format($total_pi_qnty,2); ?></th>

                <th width="70" align="center"></th>
                <th width="100" align="right"><? echo number_format($total_pi_amt,2); ?></th>

                <th width="70" align="center">&nbsp;</th>
                <th width="70" align="center">&nbsp;</th>
                <th width="100" align="center">&nbsp;</th>
                <th align="right" width="100">&nbsp;</th>
                <th width="70" align="center">&nbsp;</th>
                <th width="80" align="center">&nbsp;</th>

                <th width="100" align="center"></th>
                <th width="80" align="right"><? echo number_format($total_mrr_qnty,2); ?></th>                
                <th width="100" align="right"><? echo number_format($total_mrr_value,2); ?></th>
                <th width="80" align="right"><? echo number_format($total_short_amt,2); ?></th>
                <th width="80" align="right" title="<? echo $test_data; ?>"><? echo number_format($total_pipe_line,2); ?></th>
                <th width="70" align="center"></th>
                <th align="center"></th>
            </tfoot>        
		</table>
        
        </div>
    	<br />
        <div style="width:2770px" >
        <?
		
		if($cbo_based_on==3)
		{
			if(!empty($pi_wo_dtls_id_all_arr))
			{
				$pi_wo_dtls_id_all_arr=array_chunk(array_unique($pi_wo_dtls_id_all),999);
				$sql_wo_independ="select d.id as wo_id, d.wo_number_prefix_num, d.wo_date, d.supplier_id, e.id as wo_dtls_id, e.color_name as wo_color, e.yarn_count as wo_count, e.yarn_comp_type1st as wo_yarn_comp_type1st, e.yarn_comp_percent1st as wo_yarn_comp_percent1st, e.yarn_comp_type2nd, e.yarn_comp_percent2nd, e.yarn_type as wo_yarn_type, e.uom as wo_uom, e.supplier_order_quantity as wo_qnty, e.rate as wo_rate, e.amount as wo_amount
			from wo_non_order_info_mst d, wo_non_order_info_dtls e
			where d.id=e.mst_id and d.wo_basis_id=2 and d.company_name=$cbo_company_name and d.entry_form=144 and e.item_category_id=1 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $str_cond_independ ";
				$p=1;
				foreach($pi_wo_dtls_id_all_arr as $pi_wo_dtls_id)
				{
					if($p==1) $sql_wo_independ .=" and (e.id in(".implode(',',$pi_wo_dtls_id).")"; else $sql_wo_independ .=" or e.id in(".implode(',',$pi_wo_dtls_id).")";
					$p++;
				}
				$sql_wo_independ .=" ) ";
			}
			
		}
		else
		{
			$sql_wo_independ="select d.id as wo_id, d.wo_number_prefix_num, d.wo_date, d.supplier_id, e.id as wo_dtls_id, e.color_name as wo_color, e.yarn_count as wo_count, e.yarn_comp_type1st as wo_yarn_comp_type1st, e.yarn_comp_percent1st as wo_yarn_comp_percent1st, e.yarn_comp_type2nd, e.yarn_comp_percent2nd, e.yarn_type as wo_yarn_type, e.uom as wo_uom, e.supplier_order_quantity as wo_qnty, e.rate as wo_rate, e.amount as wo_amount
		from wo_non_order_info_mst d, wo_non_order_info_dtls e
		where d.id=e.mst_id and d.wo_basis_id=2 and d.company_name=$cbo_company_name and d.entry_form=144 and e.item_category_id=1 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0   $str_cond_independ ";
		}
		//echo $sql_wo_independ;//die;
		$req_result_independ=sql_select($sql_wo_independ);
		if($cbo_based_on==2 || $cbo_based_on==3)
		{
			?>
			<table width="2950"  align="left">
				<tr>
					<td style="font-size:18; font-weight:bold;">Based on Independent WO</td>
				</tr>
			</table>
			<br />
			<table width="2950" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_2"  align="left">
				<thead>
					<tr>
						<th width="30" rowspan="2">Sl</th>
						<th colspan="10">Work Order Details</th>
						<th colspan="12">PI Details</th>
						<th colspan="6">BTB LC Details</th>
						<th colspan="4">Matarials Received Information</th>
					</tr>
					<tr>
						<th width="50">WO No.</th>
						<th width="80">Yarn Color</th>
						<th width="50">Count</th>
						<th width="230">Composition</th>
						<th width="80">Yarn Type</th>
						<th width="50">UOM</th>
						<th width="80">WO Qnty</th>
						<th width="70">WO Rate</th>
						<th width="100">WO Amount</th>
						<th width="150">Supplier</th>
						<th width="100">PI No.</th>
						<th width="70">PI Date</th>
						<th width="70">Last Ship Date</th>
						<th width="80">Yarn Color</th>
						<th width="50">Count</th>
						<th width="230">Composition</th>
						<th width="80">Yarn Type</th>
						<th width="50">UOM</th>
						<th width="80">PI Qnty</th>
						<th width="70">PI Rate</th>
						<th width="100">PI Amount</th>
						<th width="70">Currency</th>
						<th width="70">LC Date</th>
						<th width="100">LC No</th>
						<th width="100">Issuing Bank</th>
						<th width="70">Pay Term</th>
						<th width="80">Tenor</th>
						<th width="100">LC Amount</th>
						<th width="80">MRR Qnty</th>
						<th width="100">MRR Value</th>
						<th width="100">Short Value</th>
                        <th >Pipe Line</th>
					</tr>
				</thead>
			</table>
			<div style="width:2950px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body2" align="left">
			<table width="2932" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2" align="left">
				<tbody>
				<?
				$i=1;
				//print_r($wo_po_arr);die;
				$array_check=array();
				foreach($req_result_independ as $row)
				{ 
					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					$pine_wo_qnty=$pipe_pi_qnty=$pipe_line=$mrr_qnty=$mrr_value=$short_amt="";
					$wo_rate_inde=$req_wo_recv_arr[$row[csf("id")]][$row[csf("prod_id")]]['recv_amt']/$req_wo_recv_arr[$row[csf("id")]][$row[csf("prod_id")]]['recv_qnty'];
					
					
					if($req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['recv_qnty']!="") 
					$mrr_qnty=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['recv_qnty']; 
					else $mrr_qnty=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['recv_qnty'];
					
					if($req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['recv_amt']!="")
					{
						$mrr_value=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['recv_amt'];
						$short_amt=$row[csf("wo_amount")]-$mrr_value;
						
						$booking_id=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['booking_id'];
						$receive_basis=2;
					}
					else 
					{
						$mrr_value=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['recv_amt'];
						$short_amt=$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]-$mrr_value;
						$booking_id=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['booking_id'];
						$receive_basis=1;
					}
					
					$receiving_cond=0;
					if($cbo_receive_status==1 && ($mrr_value=="" || $mrr_value==0)) $receiving_cond=1;
					if($cbo_receive_status==2 && $receive_basis==2 && $row[csf("wo_amount")]>$mrr_value && $mrr_value>0) $receiving_cond=1;
					if($cbo_receive_status==2 && $receive_basis==1 && $pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]>$mrr_value && $mrr_value>0) $receiving_cond=1;
					if($cbo_receive_status==3 && $receive_basis==2 && $mrr_value>=$row[csf("wo_amount")] && $mrr_value>0) $receiving_cond=1;
					if($cbo_receive_status==3 && $receive_basis==1 && $mrr_value>=$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"] && $mrr_value>0) $receiving_cond=1;
					if($cbo_receive_status==4 && $receive_basis==2 && $mrr_value < $row[csf("wo_amount")]) $receiving_cond=1;
					if($cbo_receive_status==4 && $receive_basis==1 && $mrr_value < $pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]) $receiving_cond=1;
					if($cbo_receive_status==5) $receiving_cond=1;
					if($receiving_cond==1)
					{
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                            <td width="30" title="<? echo $cbo_receive_status."=".$receive_basis."=".$mrr_value."=".$row[csf("wo_amount")]."=".$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]; ?>"><? echo $i; ?></td>
                            <td width="50" align="center"><p><? echo $row[csf("wo_number_prefix_num")]; ?></p></td>
                            <td width="80"><p><? echo $color_arr[$row[csf("wo_color")]]; ?></p></td>
                            <td width="50" align="center"><p>&nbsp;<? echo $yarnCount_arr[$row[csf("wo_count")]]; ?></p></td>
                            <td width="230"><p>
                            <?
                            if ($row[csf("yarn_comp_type2nd")]>0) $compo_percent2=$row[csf("yarn_comp_percent2nd")]."%"; else $compo_percent2="";
                            echo $composition[$row[csf("wo_yarn_comp_type1st")]]." ".$row[csf("wo_yarn_comp_percent1st")]."% ".$composition[$row[csf("yarn_comp_type2nd")]]." ".$compo_percent2; 
                            ?></p></td>
                            <td width="80"><p><? echo $yarn_type[$row[csf("wo_yarn_type")]]; ?></p></td>
                            <td width="50"><p><? echo $unit_of_measurement[$row[csf("wo_uom")]]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf("wo_qnty")],0); ?></p></td>
                            <td width="70" align="right"><p><? echo number_format($row[csf("wo_rate")],2); ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($row[csf("wo_amount")],2); ?></p></td>
                           
                            <td width="150"><p><? echo $supplier_arr[$row[csf("supplier_id")]]; ?></p></td>
                            <td width="100"><p><? $pi_num=implode(" , ",array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["pi_number"]," , ")))); echo $pi_num; ?></p></td>
                            <td width="70"><p>&nbsp;
                            <? 
	                            $pi_date_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["pi_date"]," , "))); 
	                            $pi_date='';
		                        foreach ($pi_date_arr as $value) 
		                        {
		                        	if($value !="" && $value!="0000-00-00")
		                        	{
		                        		$pi_date.=change_date_format($value)."</br>";
		                        	}
		                        }
	                        	echo chop($pi_date,"</br>");
                            ?></p></td>
                            <td width="70"><p>&nbsp;
                            <? 
                            $pi_last_ship_date_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["last_shipment_date"]," , "))); 
                            $shipment_date='';
	                        foreach ($pi_last_ship_date_arr as $value) 
	                        {
	                        	if($value !="" && $value!="0000-00-00")
	                        	{
	                        		$shipment_date.=change_date_format($value)."</br>";
	                        	}
	                        }
	                        echo chop($shipment_date,"</br>");
                            ?></p></td>
                            <td width="80"><p><? echo  $color_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["color_id"]]; ?></p></td>
                            <td width="50"><p>&nbsp;<? echo  $yarnCount_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["count_name"]]; ?></p></td>
                            <td width="230"><p>
                            <?
                            if($pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item2"]>0) $com_percent2=$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_comp_percent2nd"]."%"; else $com_percent2="";
                            if($pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item1"]>0) $com_percent=$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_percentage1"]."%"; else $com_percent="";
                            echo  $composition[$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item1"]]." ".$com_percent." ".$composition[$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item2"]]." ".$com_percent2;  
                            ?></p></td>
                            <td width="80"><p><? echo  $yarn_type[$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_type"]]; ?></p></td>
                            <td width="50"><p><? echo  $unit_of_measurement[$pi_data_arr[$row[csf("wo_dtls_id")]]["uom"]]; ?></p></td>
                            <td width="80" align="right"><p><? $pi_qnty=$pi_data_arr[$row[csf("wo_dtls_id")]]["quantity"]; echo  number_format($pi_qnty,0); ?></p></td>
                            <td width="70" align="right"><p><? $pi_rate=$pi_data_arr[$row[csf("wo_dtls_id")]]["rate"]; echo  number_format($pi_rate,2); ?></p></td>
                            <td width="100" align="right"><p><? $pi_amt=$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]; echo  number_format($pi_amt,2); ?></p></td>
                            <td width="70"><p><? $pi_curency_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["currency_id"]," , "))); echo  $currency[$pi_curency_arr[0]]; ?></p></td>
                            <td width="70"><p>&nbsp;
                            <? 
                            $btb_lc_date_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["lc_date"]," , "))); 
                            $btb_lc_date='';
	                        foreach ($btb_lc_date_arr as $value) 
	                        {
	                        	if($value !="" && $value!="0000-00-00")
	                        	{
	                        		$btb_lc_date.=change_date_format($value)."</br>";
	                        	}
	                        }
                           	echo chop($btb_lc_date,"</br>"); 
                            ?></p></td>
                            <td width="100"><p><? $btb_lc_no_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["lc_number"]," , "))); 
                            $btb_lc_no=implode(" , ",$btb_lc_no_arr);
                            echo $btb_lc_no;  ?></p></td>
        
                            <td width="100"><p><? $btb_issue_bank_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["issuing_bank_id"]," , "))); 
                            foreach ($btb_issue_bank_arr as $value) 
	                        {
	                        	$btb_issue_bank.=$bank_arr[$value].",";
	                        }
                            echo chop($btb_issue_bank,",");  ?></p></td>
                            <td width="70"><p><? $btb_pay_tarm_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["payterm_id"]," , "))); 
							foreach ($btb_pay_tarm_arr as $value) 
	                        {
	                        	$btb_pay_term.=$pay_term[$value].",";
	                        }
                            echo chop($btb_pay_term,","); ?></p></td>
                            <td width="80"><p><? $btb_tenor_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["tenor"]," , "))); 
                            $btb_tenor=implode(" Days, ",$btb_tenor_arr);
                            if ($btb_tenor!='') echo $btb_tenor." Days"; ?></p></td>
                            <td width="100" align="right"><p><? $btb_lc_amount=$btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["lc_value"]; echo number_format($btb_lc_amount,2); ?></p></td>
                            <?
                            if($mrr_qnty>0)
                            {
                                ?>
                                <td width="80" align="right"><p><a href="##" onClick="fn_mrr_details('<? echo $booking_id;?>','<? echo $receive_basis;?>','<? echo $row[csf("wo_color")];?>','<? echo $row[csf("wo_yarn_type")];?>','<? echo $row[csf("wo_count")];?>','<? echo $row[csf("wo_yarn_comp_type1st")];?>','receive_details_popup','<? echo $piIds;?>')"><? echo number_format($mrr_qnty,2);?> </a></p></td>
                                <?
                            }
                            else
                            {
                                ?>
                                <td width="80" align="right"></td>
                                <?
                            }
                            ?>
                            <td width="100" align="right"><p> <? echo number_format($mrr_value,2); ?></p></td>
                            <td align="right" width="100"><p><? echo number_format($short_amt,2); ?></p></td>
                            <td align="right"><p>
                            <?
                            //$pine_wo_qnty=$pipe_pi_qnty=$pipe_line 
                            $pine_wo_qnty=$wo_pipe_array[$row[csf("wo_id")]][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]];
                            $pipe_pi_qnty=$pi_pipe_array[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]];
                            $pipe_line=(($pine_wo_qnty+$pipe_pi_qnty)-$mrr_qnty);
                            if(number_format($pipe_line,2) > 0.00) echo number_format($pipe_line,2); else echo "0.00";
                            ?>
                            </p></td>
                        </tr>
                        <?
						$tot_ind_wo_qnty+=$row[csf("wo_qnty")];
						$tot_ind_wo_amount+=$row[csf("wo_amount")];
						$tot_ind_pi_qnty+=$pi_qnty;
						$tot_ind_pi_amt+=$pi_amt;
						$tot_ind_btb_lc_amount+=$btb_lc_amount;
						$tot_ind_mrr_qnty+=$mrr_qnty;
						$tot_ind_mrr_value+=$mrr_value;
						$tot_ind_short_amt+=$short_amt;
					}
					$k++;
					$i++;
				}
				?>
				</tbody>
                <tfoot>
					<tr>
                    	<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>Total</th>
						<th align="right"><? echo number_format($tot_ind_wo_qnty,2);?></th>
						<th>&nbsp;</th>
						<th align="right"><? echo number_format($tot_ind_wo_amount,2);?></th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? echo number_format($tot_ind_pi_qnty,2);?></th>
						<th>&nbsp;</th>
						<th align="right"><? echo number_format($tot_ind_pi_amt,2);?></th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? echo number_format($tot_ind_btb_lc_amount,2);?></th>
						<th align="right"><? echo number_format($tot_ind_mrr_qnty,2);?></th>
						<th align="right"><? echo number_format($tot_ind_mrr_value,2);?></th>
						<th>&nbsp;</th>
                        <th>&nbsp;</th>
					</tr>
                </tfoot>
			</table>
			</div>
			<?
		}
		?>
    </div>
    <br />
    <div style="width:2030px" >
    <?
	if($cbo_based_on==3)
    {
    	$sql_independent_pi="select a.id as pi_id, a.supplier_id as pi_suplier, a.pi_number, a.pi_date, a.last_shipment_date, a.currency_id, b.work_order_id, b.work_order_dtls_id, b.color_id, b.count_name, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_composition_item2, b.yarn_composition_percentage2, b.yarn_type, b.uom, b.quantity, b.rate, b.amount from com_pi_master_details a, com_pi_item_details b where a.pi_basis_id=2 and a.item_category_id=1 and a.importer_id=$cbo_company_name and a.id=b.pi_id and a.status_active=1 and b.status_active=1 $pi_cond";
    	$req_result_pi_independ=sql_select($sql_independent_pi);
	}
		//echo $sql_independent_pi;die;
	if($cbo_based_on==3)
	{
		?>
		<table width="2150"  align="left">
			<tr>
				<td style="font-size:18; font-weight:bold;">Based on Independent PI</td>
			</tr>
		</table>
		<br />
		<table width="2150" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_3"  align="left">
			<thead>
				<tr>
					<th width="30" rowspan="2">Sl</th>
					<th colspan="14">PI Details</th>
					<th colspan="6">BTB LC Details</th>
					<th colspan="4">Matarials Received Information</th>
				</tr>
				<tr>
					<th width="150">Supplier</th>
					<th width="100">PI No.</th>
					<th width="70">PI Date</th>
					<th width="70">Last Ship Date</th>
					<th width="80">Yarn Color</th>
					<th width="50">Count</th>
					<th width="230">Composition</th>
					<th width="80">Yarn Type</th>
					<th width="50">UOM</th>
					<th width="80">PI Qnty</th>
					<th width="70">PI Rate</th>
					<th width="100">PI Amount</th>
					<th width="70">Currency</th>
					<th width="70">LC Date</th>
					<th width="100">LC No</th>
					<th width="100">Issuing Bank</th>
					<th width="70">Pay Term</th>
					<th width="80">Tenor</th>
					<th width="100">LC Amount</th>
					<th width="80">MRR Qnty</th>
					<th width="100">MRR Value</th>
					<th width="100">Short Value</th>
                    <th >Pipe Line</th>
				</tr>
			</thead>
		</table>

		


		<div style="width:2150px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body3" align="left">
		<table width="2132" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body3" align="left">
			<tbody>
			<?
			$i=1;

			//var_dump($tem_arr);die;
			$array_check=array();
			foreach($req_result_pi_independ as $row)
			{
				if ($i%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";
				$pipe_pi_qnty=$pipe_line="";
				$mrr_qnty=0;$mrr_value=0;
				$mrr_qnty=$req_wo_recv_arr[$row[csf("pi_id")]][1][$row[csf("color_id")]][$row[csf("yarn_type")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]]['recv_qnty'];
				$mrr_value=$req_wo_recv_arr[$row[csf("pi_id")]][1][$row[csf("color_id")]][$row[csf("yarn_type")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]]['recv_amt'];
				$pi_id_ref=$req_wo_recv_arr[$row[csf("pi_id")]][1][$row[csf("color_id")]][$row[csf("yarn_type")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]]['booking_id'];
				$receive_basis=1;
				
				$receiving_cond=0;
				if($cbo_receive_status==1 && ($mrr_value=="" || $mrr_value==0)) $receiving_cond=1;
				if($cbo_receive_status==2 && $row[csf("amount")]>$mrr_value && $mrr_value>0) $receiving_cond=1;
				if($cbo_receive_status==3 && $mrr_value>=$row[csf("amount")]) $receiving_cond=1;
				if($cbo_receive_status==4 && $mrr_value < $row[csf("amount")]) $receiving_cond=1;
				if($cbo_receive_status==5) $receiving_cond=1;
				
				if($receiving_cond==1)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                        <td width="30"><? echo $i; ?></td>
                        <td width="150"><p><? echo $supplier_arr[$row[csf("pi_suplier")]]; ?></p></td>
                        <td width="100"><p><? echo $row[csf("pi_number")]; ?></p></td>
                        <td width="70"><p>&nbsp;
                        <? 
                        if($row[csf("pi_date")]!="" && $row[csf("pi_date")]!="0000-00-00") echo change_date_format($row[csf("pi_date")]); 
                        ?></p></td>
                        <td width="70"><p>&nbsp;
                        <? 
                        if($row[csf("last_shipment_date")]!="" && $row[csf("last_shipment_date")]!="0000-00-00") echo change_date_format($row[csf("last_shipment_date")]); 
                        ?></p></td>
                        <td width="80"><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
                        <td width="50"><p>&nbsp;<? echo $yarnCount_arr[$row[csf("count_name")]]; ?></p></td>
                        <td width="230"><p>
                        <? 
                        if($row[csf("yarn_composition_item2")]>0) $comp_percent2=$row[csf("yarn_composition_percentage2")]."%"; else $comp_percent2="";
                        echo $composition[$row[csf("yarn_composition_item1")]]." ".$row[csf("yarn_composition_percentage1")]."% ".$composition[$row[csf("yarn_composition_item2")]]; 
                        ?></p></td>
                        <td width="80"><p><? echo $yarn_type[$row[csf("yarn_type")]];?></p></td>
                        <td width="50"><p><? echo $unit_of_measurement[$row[csf("uom")]];?></p></td>
                        <td width="80" align="right"><p><? echo  number_format($row[csf("quantity")],0); ?> </p></td>
                        <td width="70" align="right"><p><? echo  number_format($row[csf("rate")],2); ?> </p></td>
                        <td width="100" align="right"><p><? echo number_format($row[csf("amount")],2); ?> </p></td>
                        <td width="70"><p><? echo $currency[$row[csf("currency_id")]]; ?></p></td>
                        <td width="70"><p>&nbsp;
                        <? 
                        $btb_lc_date_arr=array_unique(explode(",",chop($btb_data_arr[$row[csf("pi_id")]]["lc_date"]," , ")));
                        $btb_lc_date='';
                        foreach ($btb_lc_date_arr as $value) 
                        {
                        	if($value !="" && $value!="0000-00-00")
                        	{
                        		$btb_lc_date.=change_date_format($value)."</br>";
                        	}
                        }
                        echo chop($btb_lc_date,"</br>");  ?> </p></td>
                        <td width="100"><p><? $btb_lc_no_arr=array_unique(explode(",",chop($btb_data_arr[$row[csf("pi_id")]]["lc_number"]," , ")));
                        $btb_lc_no=implode(" , ",$btb_lc_no_arr);
                        echo $btb_lc_no;  ?></p></td>
                        <td width="100"><p><? $btb_issue_bank_arr=array_unique(explode(",",chop($btb_data_arr[$row[csf("pi_id")]]["issuing_bank_id"]," , ")));
                        foreach ($btb_issue_bank_arr as $value) 
                        {
                        	$btb_issue_bank.=$bank_arr[$value].",";
                        }
                        echo chop($btb_issue_bank,","); ?></p></td>
                        <td width="70"><p><? $btb_pay_tarm_arr=array_unique(explode(",",chop($btb_data_arr[$row[csf("pi_id")]]["payterm_id"]," , ")));
                        $btb_pay_term=''; 
                        foreach ($btb_pay_tarm_arr as $value) 
                        {
                        	$btb_pay_term.=$pay_term[$value].",";
                        }
                        echo chop($btb_pay_term,","); ?></p></td>
                        <td width="80"><p><? $btb_tenor_arr=array_unique(explode(",",chop($btb_data_arr[$row[csf("pi_id")]]["tenor"]," , "))); 
                        $btb_tenor=implode(" Days, ",$btb_tenor_arr);
                        if ($btb_tenor!='') echo $btb_tenor." Days"; ?></p></td>
                        <td width="100" align="right"><p><? $btb_lc_amount=$btb_data_arr[$row[csf("pi_id")]]["lc_value"]; echo number_format($btb_lc_amount,2); ?> </p></td>
                        <td width="80" align="right"><p><a href="##" onClick="fn_mrr_details('<? echo $pi_id_ref;?>','<? echo $receive_basis;?>','<? echo $row[csf("color_id")];?>','<? echo $row[csf("yarn_type")];?>','<? echo $row[csf("count_name")];?>','<? echo $row[csf("yarn_composition_item1")];?>','receive_details_popup')"><? echo number_format($mrr_qnty,2); ?></a></p></td>
                        <td width="100" align="right"><p><? echo number_format($mrr_value,2);?></p></td>
                        <td align="right"><p><? $short_amt=$row[csf("amount")]-$mrr_value; echo number_format($short_amt,2); ?></p></td>
                        <td align="right" title="<?= $pi_pipe_array[$row[csf("pi_id")]][$row[csf("color_id")]][$row[csf("yarn_type")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]];?>"><p>
                        <?
                        //$pipe_pi_qnty=$pipe_line 
                        $pipe_pi_qnty=$pi_pipe_array[$row[csf("pi_id")]][$row[csf("color_id")]][$row[csf("yarn_type")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]];
                        $pipe_line=($pipe_pi_qnty-$mrr_qnty);
                        echo number_format($pipe_line,2); 
                        ?>
                        </p></td>
                    </tr>
                    <?
                    $k++;
                    $i++;
                    $total_pi_qntty+=$row[csf("quantity")];
                    $total_pi_amount+=$row[csf("amount")];
                    $total_mrr_qty+=$mrr_qnty;
                    $total_mrr_val+=$mrr_value;
				}
			}
			?>
			</tbody>
			<tfoot style="background: #dbdbdb;">
				<td colspan="10" align="right"><strong>Total</strong></td>
				<td align="right"><b><? echo number_format($total_pi_qntty,2);?></b></td>
				<td>&nbsp;</td>
				<td align="right"><b><? echo number_format($total_pi_amount,2);?></b></td>
				<td colspan="8" align="right"><b><? echo number_format($total_mrr_qty,2);?></b></td>
				<td align="right"><b><? echo number_format($total_mrr_val,2);?></b></td>
				<td colspan="2">&nbsp;</td>
			</tfoot>
		</table>
		</div>
		<?
	}
    ?>
</div>
    <?
	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$cbo_based_on";
	exit();
}




?>


 