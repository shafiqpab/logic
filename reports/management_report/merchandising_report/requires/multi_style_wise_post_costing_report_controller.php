
<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Multi Style wise Post Costing Report.
Functionality	:	
JS Functions	:
Created by		:	Zakaria joy 
Creation date 	: 	26-10-2023
Updated by 		: 		
Update date		: 		   
QC Performed BY	:	Abidul Islam	
QC Date			:	13-11-2023
Comments		:   Temp tbl id:144
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.yarns.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.commisions.php');
require_once('../../../../includes/class4/class.commercials.php');
require_once('../../../../includes/class4/class.others.php');
require_once('../../../../includes/class4/class.washes.php');
require_once('../../../../includes/class4/class.fabrics.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------

$user_name = $_SESSION['logic_erp']["user_id"];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

function chop_last_string($str,$trim_str)
{
	$main_str_len=strlen($str);
	$trim_str_len=strlen($trim_str);
	$str2='';
	$str2=substr($str, $main_str_len-$trim_str_len, $trim_str_len);
	if ($trim_str==$str2) return substr($str,0,$main_str_len-$trim_str_len);
	else return $str;		
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

if($action=="order_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		
		var selected_id = new Array; var selected_name = new Array;
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			
			if (str!="") str=str.split("_");
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_order_id').val( id );
			$('#hide_order_no').val( name );
		}
	
    </script>

</head>

<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:780px;">
            <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Order No</th>
                    <th>Shipment Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                    <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                        </td>	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_order_no_search_list_view', 'search_div', 'multi_style_wise_post_costing_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    	</td>
                    </tr>
                    <tr>
                        <td colspan="5" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:5px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 
}

if($action=="create_order_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
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
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==1) 
		$search_field="b.po_number"; 
	else if($search_by==2) 
		$search_field="a.style_ref_no"; 	
	else 
		$search_field="a.job_no";
		
	$start_date =$data[4];
	$end_date =$data[5];	
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date),"yyyy-mm-dd")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd")."'";
		}
		else
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	
	$company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$arr=array (0=>$company_short_arr,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql= "select b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond order by b.id, b.pub_shipment_date";
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "70,70,50,70,150,180","760","210",0, $sql , "js_set_value", "id,po_number","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3','',1) ;
   exit(); 
}
if($action=="yarn_cost")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);

	//$exchange_rate=76;
	$costing_per_arr=return_library_array( "select job_no, costing_per_id from wo_pre_cost_dtls where status_active=1 and is_deleted=0", "job_no", "costing_per_id");
	//$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master where item_category_id=1", "id", "avg_rate_per_unit"  );
	$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );

	$dataArrayYarn=array();
	$yarn_sql="select job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, sum(cons_qnty) as qnty, sum(amount) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 group by job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id";
	$resultYarn=sql_select($yarn_sql);
	foreach($resultYarn as $yarnRow)
	{
		$dataArrayYarn[$yarnRow[csf('job_no')]].=$yarnRow[csf('count_id')]."**".$yarnRow[csf('copm_one_id')]."**".$yarnRow[csf('percent_one')]."**".$yarnRow[csf('copm_two_id')]."**".$yarnRow[csf('percent_two')]."**".$yarnRow[csf('type_id')]."**".$yarnRow[csf('qnty')]."**".$yarnRow[csf('amount')].",";
	}

	$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master where item_category_id=1", "id", "avg_rate_per_unit"  );
	$receive_array=array();
	$sql_receive="select a.prod_id,c.receive_purpose,b.lot,b.color, sum(a.order_qnty) as qty, sum(a.order_amount) as amnt from inv_transaction a,product_details_master b,inv_receive_master c where a.prod_id=b.id and c.id=a.mst_id and c.entry_form=1 and c.item_category=1 and a.transaction_type=1 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 group by a.prod_id,c.receive_purpose,b.lot,b.color";
	$resultReceive = sql_select($sql_receive);
	foreach($resultReceive as $invRow)
	{
		$avg_rate=$invRow[csf('amnt')]/$invRow[csf('qty')];
		$receive_array[$invRow[csf('prod_id')]]=$avg_rate;
		$yarn_receive_arr[$invRow[csf('prod_id')]][$invRow[csf('lot')]][$invRow[csf('color')]]['purpose']=$invRow[csf('receive_purpose')];
	}
	$yarnData="select b.po_breakdown_id, b.prod_id, a.item_category, b.issue_purpose,c.lot,c.color,
		sum(CASE WHEN a.transaction_type=2 and b.entry_form ='3' and b.trans_type=2 and b.issue_purpose in(2,4,15,38) THEN b.quantity ELSE 0 END) AS yarn_iss_qty,
		sum(CASE WHEN a.transaction_type=2 and b.entry_form ='3' and b.trans_type=2 and b.issue_purpose in(2,4,15,38) THEN b.returnable_qnty ELSE 0 END) AS returnable_qnty,
		(CASE WHEN a.transaction_type=2 and b.entry_form ='3' and b.trans_type=2  THEN a.transaction_date ELSE null END) AS transaction_date,
		sum(CASE WHEN a.transaction_type=4 and b.entry_form ='9' and b.trans_type=4 THEN b.quantity ELSE 0 END) AS yarn_iss_return_qty,
		sum(CASE WHEN a.transaction_type=5 and b.entry_form ='11' and b.trans_type=5 THEN b.quantity ELSE 0 END) AS trans_in_qty_yarn,
		sum(CASE WHEN a.transaction_type=6 and b.entry_form ='11' and b.trans_type=6 THEN b.quantity ELSE 0 END) AS trans_out_qty_yarn,
		sum(CASE WHEN a.transaction_type=2 and b.entry_form ='25' and b.trans_type=2 THEN a.cons_rate*b.quantity ELSE 0 END) AS trims_issue_amnt
		from inv_transaction a, order_wise_pro_details b,product_details_master c where a.id=b.trans_id and c.id=b.prod_id and c.id=a.prod_id and a.item_category in(1,4) and a.transaction_type in(2,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.po_breakdown_id in($po_id)  group by b.po_breakdown_id, b.prod_id, a.item_category,a.transaction_date,a.transaction_type,b.entry_form,b.trans_type,b.issue_purpose,c.lot,c.color";
		 $yarnDataArray=sql_select($yarnData); $yarnTrimsCostArray=array();
	
	foreach($yarnDataArray as $invRow)
	{
					$issue_purpose=$invRow[csf('issue_purpose')];
						//echo $recv_purpose.'<br>';
						$issue_qty=$invRow[csf('yarn_iss_qty')]-$invRow[csf('yarn_iss_return_qty')];
					
						$transaction_date=$invRow[csf('transaction_date')];
						if($db_type==0)
						{
							$conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
						}
						else
						{
							$conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
						}
						$currency_rate=set_conversion_rate($usd_id,$conversion_date );
						//echo $currency_rate.'dd';
						if($receive_array[$invRow[csf('prod_id')]]>0)
						{
							$rate=$receive_array[$invRow[csf('prod_id')]];
						}
						else
						{
							$rate=$avg_rate_array[$invRow[csf('prod_id')]]/$currency_rate;
						}
						
						$issue_amnt=$issue_qty*$rate;
						//$retble_iss_amnt=$returnable_qnty*$rate;	
						if($issue_purpose==1)
						{
							$recv_purpose=$yarn_receive_arr[$invRow[csf('prod_id')]][$invRow[csf('lot')]][$invRow[csf('color')]]['purpose'];
							
							if($recv_purpose==16)
							{
								$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]]['grey_yarn_amt']+=$issue_amnt;
								$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]]['grey_yarn_qty']+=$issue_qty;
								
							}
							
						}
						
		
		$iss_qty=$invRow[csf('yarn_iss_qty')]+$invRow[csf('trans_in_qty_yarn')]-$invRow[csf('yarn_iss_return_qty')]-$invRow[csf('trans_out_qty_yarn')];
		$dataArrayYarnIssue[$invRow[csf('po_breakdown_id')]][1]+=$iss_qty;
		$rate='';
		if($receive_array[$invRow[csf('prod_id')]]>0)
		{
			$rate=$receive_array[$invRow[csf('prod_id')]];
		}
		else
		{
			$rate=$avg_rate_array[$invRow[csf('prod_id')]]/$exchange_rate;
		}
		$dataArrayYarnIssue[$invRow[csf('po_breakdown_id')]][2]+=$iss_qty*$rate;
	}

	?>
	<style>
		hr
		{
			color: #676767;
			background-color: #676767;
			height: 1px;
		}
	</style> 
    <div>
        <fieldset style="width:1033px;">
        	<table class="rpt_table" width="1030" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="40">SL</th>
                    <th width="60">Buyer</th>
                    <th width="80">PO No</th>
                    <th width="50">Year</th>
                    <th width="60">Job No</th>
                    <th width="90">Order Qty</th>
                    <th width="70">Count</th>
                    <th width="100">Composition</th>
                    <th width="70">Type</th>
                    <th width="90">Required<br/><font style="font-size:9px; font-weight:100">(As Per Pre-Cost)</font></th>
                    <th width="100">Cost ($)</th>
                    <th width="90">Issued</th>
                    <th>Cost ($)</th>
                </thead>
            </table>	
            <div style="width:1030px; max-height:310px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="1010" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
					else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
					else $year_field="";//defined Later
						
					$sql="select a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id, b.po_number, b.pub_shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($po_id) order by b.pub_shipment_date, a.job_no_prefix_num, b.id";
					$result=sql_select($sql);
					$i=1; $tot_po_qnty=0; $tot_mkt_required=0; $tot_required_cost=0; $tot_yarn_iss_qty=0; $tot_yarn_iss_cost=0;
				
					 $condition= new condition();
						if($po_id!='')
						{
							$condition->po_id("in($po_id)"); 
						}
					
					 $condition->init();
				
					 $yarn= new yarn($condition);
					 	// echo $yarn->getQuery(); die;
					$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
					foreach($result as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$dzn_qnty=0; $job_mkt_required=0; $yarn_issued=0; $yarn_data_array=array(); //$job_mkt_required_cost=0;
						$costing_per_id=$costing_per_arr[$row[csf('job_no')]];
						if($costing_per_id==1)
						{
							$dzn_qnty=12;
						}
						else if($costing_per_id==3)
						{
							$dzn_qnty=12*2;
						}
						else if($costing_per_id==4)
						{
							$dzn_qnty=12*3;
						}
						else if($costing_per_id==5)
						{
							$dzn_qnty=12*4;
						}
						else
						{
							$dzn_qnty=1;
						}
						
						$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
						$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
						$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
						$tot_po_qnty+=$order_qnty_in_pcs;
						$job_mkt_required_cost=$yarn_costing_arr[$row[csf('id')]];
						
						$dataYarn=explode(",",substr($dataArrayYarn[$row[csf('job_no')]],0,-1));
						foreach($dataYarn as $yarnRow)
						{
							$yarnRow=explode("**",$yarnRow);
							$count_id=$yarnRow[0];
							$copm_one_id=$yarnRow[1];
							$percent_one=$yarnRow[2];
							$copm_two_id=$yarnRow[3];
							$percent_two=$yarnRow[4];
							$type_id=$yarnRow[5];
							$qnty=$yarnRow[6];
							$amnt=$yarnRow[7];
							
							$mkt_required=$plan_cut_qnty*($qnty/$dzn_qnty);
							//$mkt_required_cost=$plan_cut_qnty*($amnt/$dzn_qnty);
							$job_mkt_required+=$mkt_required;
							//$job_mkt_required_cost+=$mkt_required_cost;
							
							$yarn_data_array['count'][]=$yarn_count_details[$count_id];
							$yarn_data_array['type'][]=$yarn_type[$type_id];
							
							if($percent_two!=0)
							{
								$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id]." ".$percent_two." %";
							}
							else
							{
								$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id];
							}

							$yarn_data_array['comp'][]=$compos;
						}
						
						$yarn_iss_qty=$yarnTrimsCostArray[$row[csf('id')]]['grey_yarn_qty'];//$dataArrayYarnIssue[$row[csf('id')]][1];
						$yarn_iss_cost=$yarnTrimsCostArray[$row[csf('id')]]['grey_yarn_amt'];//$dataArrayYarnIssue[$row[csf('id')]][2];
						
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="60" style="word-break:break-all;"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                            <td width="80" style="word-break:break-all;"><? echo $row[csf('po_number')]; ?></td>
                            <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                            <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                            <td width="90" align="right"><? echo $order_qnty_in_pcs; ?></td>
                            <td width="70">
								<? 
                                    $d=1;
                                    foreach($yarn_data_array['count'] as $yarn_count_value)
                                    {
                                        if($d!=1)
                                        {
                                            echo "<hr/>";
                                        }
                                        echo $yarn_count_value;
                                        $d++;
                                    }
                                ?>
                            </td>
                            <td width="100">
                                <div style="word-wrap:break-word; width:100px">
                                    <? 
                                         $d=1;
                                         foreach($yarn_data_array['comp'] as $yarn_composition_value)
                                         {
                                            if($d!=1)
                                            {
                                                echo "<hr/>";
                                            }
                                            echo $yarn_composition_value;
                                            $d++;
                                         }
                                    ?>
                                </div>
                            </td>
                            <td width="70">
                                <p>
                                    <? 
                                         $d=1;
                                         foreach($yarn_data_array['type'] as $yarn_type_value)
                                         {
                                            if($d!=1)
                                            {
                                               echo "<hr/>";
                                            }
                                            
                                            echo $yarn_type_value; 
                                            $d++;
                                         }
                                    ?>
                                </p>
                            </td>
							<td width="90" align="right"><? echo fn_number_format($job_mkt_required,2,'.',''); ?></td>
                            <td width="100" align="right"><? echo fn_number_format($job_mkt_required_cost,2,'.',''); ?></td>
                            <td width="90" align="right"><? echo fn_number_format($yarn_iss_qty,2,'.',''); ?></td>
                            <td align="right" ><? echo fn_number_format($yarn_iss_cost,2,'.',''); ?></td>
						</tr>
					<?
						$i++;
						$tot_mkt_required+=$job_mkt_required; 
						$tot_required_cost+=$job_mkt_required_cost;
						$tot_yarn_iss_qty+=$yarn_iss_qty; 
						$tot_yarn_iss_cost+=$yarn_iss_cost; 
					}
					?>
                	<tfoot>
                        <th colspan="5">Total</th>
                        <th><? echo $tot_po_qnty; ?></th>
                        <th colspan="3">&nbsp;</th>
                        <th><? echo fn_number_format($tot_mkt_required,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_required_cost,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_yarn_iss_qty,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_yarn_iss_cost,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
	<?
	exit();
}

if($action=="mkt_yarn_cost")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$data=explode("**",$mkt_data);
	$po_ids=$data[0];
	$job_nos=rtrim($data[1],',');
	//echo $po_ids.'='.$job_nos;
	$job_nos=array_unique(explode(",",$job_nos));$po_nos=array_unique(explode(",",$po_ids));
	$job_noss='';
	foreach($job_nos as $job)
	{
		if($job_noss=='') $job_noss="'".$job."'"; else $job_noss.=","."'".$job."'";
	}
	$job_noss=implode(',',array_unique(explode(",",$job_noss)));
	$po_no=str_replace("'","",$po_ids);
	$condition= new condition();
	if(str_replace("'","",$job_noss) !=''){
		 // $condition->job_no("=$job_noss");
	 }
	  if(str_replace("'","",$po_no)!='')
	 {
		$condition->po_id("in($po_no)"); 
	 }
	  $condition->init();
   $yarn= new yarn($condition);
   //echo $yarn->getQuery(); die;
  $yarn_costing_arr=$yarn->getOrderCountCompositionColorAndTypeWiseYarnAmountArray();
	//print_r($yarn_costing_arr);
	$sql = "select min(id) as id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, color,type_id, min(cons_ratio) as cons_ratio, sum(cons_qnty) as cons_qnty, rate, sum(amount) as amount from wo_pre_cost_fab_yarn_cost_dtls where job_no in($job_noss) group by count_id, copm_one_id, percent_one, copm_two_id, percent_two, color,type_id, rate";
 $data_array=sql_select($sql); 
	?>

    <div align="center">
        <fieldset style="width:620px;">
        	<table class="rpt_table" width="600" cellpadding="0" cellspacing="0" border="1" rules="all">
            <caption> <h3> Grey Yarn Details</h3></caption>
            	<thead>
                    <th width="60">SL</th>
                   <th width="350">Yarn Desc </th>
                    <th width="">Yarn Cost</th>
                </thead>
                <tbody> 
                <?
				//order,Count,Composition,color,type wise
                  $total_yarn_amount=0;$k=1;
			foreach( $data_array as $row )
            { 
				if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($row[csf("percent_one")]==100)
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$color_library[$row[csf("color")]]." ".$yarn_type[$row[csf("type_id")]];
            	else
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$composition[$row[csf("copm_two_id")]]." ".$row[csf("percent_two")]."% ".$color_library[$row[csf("color")]]." ".$yarn_type[$row[csf("type_id")]];
					
 				
				$yarn_cost=0;
				foreach($po_nos as $pid)
				{
				 $yarn_cost+=$yarn_costing_arr[$pid][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("color")]][$row[csf("type_id")]];	
				}
			?>	
            
				
                  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                	<td align="center" style="padding-right:3px"><? echo $k; ?></td>
                  <td align="center" style="padding-right:3px"><? echo $item_descrition; ?></td>
                    <td align="right" style="padding-right:3px"><? echo fn_number_format($yarn_cost,2); ?></td>
                </tr>
               
                <?
				$k++;
				$total_yarn_amount+=$yarn_cost;
			}
				?>
                </tbody>
                <tfoot>
                 <th colspan="2" align="right"> Total</th><th align="right"> <? echo fn_number_format($total_yarn_amount,2);?></th>
                 </tfoot>
            </table>	
        </fieldset>
    </div>
	<?
	exit();
}
if($action=="mkt_yarn_dyeing_twisting_cost") 
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$data=explode("**",$mkt_data);
	$job_id=$data[0];
	$condition= new condition();
	if(str_replace("'","",$job_id)!='')
	{
		$condition->jobid_in("$job_id"); 
	}
	$condition->init();
	$conversion= new conversion($condition);
	$conversion_costing_arr=$conversion->getAmountArray_by_jobAndProcess();
	?>
    <script>
    function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('main_body').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="290px";
	}
	
    </script>
    <input type="hidden" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="main_body" style="width:400px;">
     <fieldset style="width:400px;">
        	<table class="rpt_table" width="400" cellpadding="0" cellspacing="0" id="table_search" border="1" rules="all" >
             <caption> <h3> Yarn Value Addition Cost </h3></caption>
            	<thead>
                	<th width="50">SL</th>
                    <th width="250">Process</th>
                    <th width="100">Amount</th>
                </thead>
                <tbody id="scroll_body">
                	<?
					$k=1;$total_conversion_cost=0;
					$sql = "SELECT  a.cons_process, b.job_no from wo_pre_cost_fab_conv_cost_dtls a join wo_pre_cost_fabric_cost_dtls b on a.job_id=b.job_id and a.fabric_description=b.id where a.job_id=$job_id and a.cons_process in(30,134)  group by a.cons_process,b.job_no ";
					
					$data_array=sql_select($sql);
					foreach($data_array as $row)
					{
						if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$conversion_cost=0;
						$conversion_cost+=array_sum($conversion_costing_arr[$row[csf('job_no')]][$row[csf("cons_process")]]);	
					?>
                  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                    <td align="center"><? echo $k; ?></td>
                    <td align="left"><? echo $conversion_cost_head_array[$row[csf("cons_process")]]; ?></td>
                    <td align="right"><? echo fn_number_format($conversion_cost,4); ?></td>
                    </tr>
                    <?
					$k++;
						$total_conversion_cost+=$conversion_cost;
					}
					?>
                    </tbody>
                <tfoot>
                 <th colspan="2" align="right"> Total</th><th align="right"> <? echo fn_number_format($total_conversion_cost,2);?></th>
                 </tfoot>
               </table>
           </table>
  </fieldset>
  <script>
  setFilterGrid("table_search",-1);
  </script>
    </div>
	<?
	exit();
}
if($action=="knitting_cost")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	
	$exchange_rate=76;
	$costing_per_arr=return_library_array( "select job_no, costing_per_id from wo_pre_cost_dtls where status_active=1 and is_deleted=0", "job_no", "costing_per_id");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	
	$subconInBillDataArray=sql_select("select b.order_id, sum(b.amount) AS knit_bill, sum(b.delivery_qty) as qty from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_source=1 and a.process_id=2 group by b.order_id");
	foreach($subconInBillDataArray as $subRow)
	{
		$subconCostArray[$subRow[csf('order_id')]]['amnt']=$subRow[csf('knit_bill')];
		$subconCostArray[$subRow[csf('order_id')]]['qty']=$subRow[csf('qty')];
	}	
	
	$subconOutBillDataArray=sql_select("select b.order_id, sum(b.amount) AS knit_bill, sum(b.receive_qty) as qty from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.process_id=2 and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id");
	foreach($subconOutBillDataArray as $subRow)
	{
		//$subconCostArray[$subRow[csf('order_id')]]['amnt']+=$subRow[csf('knit_bill')];
		//$subconCostArray[$subRow[csf('order_id')]]['qty']+=$subRow[csf('qty')];
	}
	
	$bookingArray=return_library_array( "select b.po_break_down_id, sum(b.grey_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id) and a.item_category in(2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id", "po_break_down_id", "qnty");
	
	$greyProdArray=return_library_array( "select po_breakdown_id, sum(quantity) as qnty from order_wise_pro_details where po_breakdown_id in($po_id) and entry_form=2 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "qnty");
	
	$knitCostArray=return_library_array( "select job_no, sum(amount) AS knit_charge from wo_pre_cost_fab_conv_cost_dtls where cons_process=1 and status_active=1 and is_deleted=0 group by job_no", "job_no", "knit_charge");
	?>
	<script>
    function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('main_body').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="290px";
	}
	function openmypage_bill(po_id,type,tittle)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'multi_style_wise_post_costing_report_controller.php?po_id='+po_id+'&action='+type, tittle, 'width=560px, height=350px, center=1, resize=0, scrolling=0', '../../../');
	}
    </script>
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="main_body" style="width:1050px;">
        <fieldset style="width:1050px;">
        	<table class="rpt_table" width="1030" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="40">SL</th>
                    <th width="60">Buyer</th>
                    <th width="80">PO No</th>
                    <th width="50">Year</th>
                    <th width="60">Job No</th>
                    <th width="80">Style Name</th>
                    <th width="110">Gmts Item</th>
                    <th width="90">Order Qty</th>
                    <th width="80">Booking Qty</th>
                    <th width="80">Grey Prod.</th>
                    <th width="90">Knitting Cost</th>
                    <th width="80">Fabric Bill Qty</th>
                    <th>Bill Amount (USD)</th>
                </thead>
            </table>	
            <div style="width:1050px; max-height:290px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="1030" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
					else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
					else $year_field="";//defined Later
						
					$sql="select a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id, b.po_number, b.pub_shipment_date, b.po_quantity, b.unit_price, b.po_total_price, b.shiping_status from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($po_id) order by b.pub_shipment_date";
					$result=sql_select($sql);
					$i=1; $tot_po_qnty=0; $tot_booking_qnty=0; $tot_greyProd_qnty=0; $tot_knitCost=0; $tot_knitbill=0; $tot_knitQty=0;
					foreach($result as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$gmts_item='';
						$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
						}
						
						$dzn_qnty=0;
						$costing_per_id=$costing_per_arr[$row[csf('job_no')]];
						if($costing_per_id==1)
						{
							$dzn_qnty=12;
						}
						else if($costing_per_id==3)
						{
							$dzn_qnty=12*2;
						}
						else if($costing_per_id==4)
						{
							$dzn_qnty=12*3;
						}
						else if($costing_per_id==5)
						{
							$dzn_qnty=12*4;
						}
						else
						{
							$dzn_qnty=1;
						}
						
						$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
						$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
						$tot_po_qnty+=$order_qnty_in_pcs;
						$bookingQty=$bookingArray[$row[csf('id')]];
						$tot_booking_qnty+=$bookingQty;
						$greyProdQty=$greyProdArray[$row[csf('id')]];
						$tot_greyProd_qnty+=$greyProdQty;
						$knitCost=($order_qnty_in_pcs/$dzn_qnty)*$knitCostArray[$row[csf('job_no')]];
						$tot_knitCost+=$knitCost;
						$knitQty=$subconCostArray[$row[csf('id')]]['qty'];
						$knitbill=$subconCostArray[$row[csf('id')]]['amnt']/$exchange_rate;
						$tot_knitQty+=$knitQty;
						$tot_knitbill+=$knitbill;
						
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="60" style="word-break:break-all;"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                            <td width="80" style="word-break:break-all;"><? echo $row[csf('po_number')]; ?></td>
                            <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                            <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                            <td width="80" style="word-break:break-all;"><? echo $row[csf('style_ref_no')]; ?></td>
                            <td width="110" style="word-break:break-all;"><? echo $gmts_item; ?></td>
                            <td width="90" align="right"><? echo $order_qnty_in_pcs; ?></td>
							<td width="80" align="right"><? echo fn_number_format($bookingQty,2,'.',''); ?></td>
							<td width="80" align="right"><? echo fn_number_format($greyProdQty,2,'.',''); ?></td>
                            <td width="90" align="right"><? echo fn_number_format($knitCost,2,'.',''); ?></td>
							<td width="80" align="right"><? echo fn_number_format($knitQty,2,'.',''); ?></td>
							<td align="right"><a href="##" onClick="openmypage_bill('<? echo $row[csf('id')]; ?>','knitting_bill','Knitting bill Details')"><? echo fn_number_format($knitbill,2,'.',''); ?></a></td>
						</tr>
					<?
					$i++;
					}
					?>
                	<tfoot>
                        <th colspan="7">Total</th>
                        <th><? echo $tot_po_qnty; ?></th>
                        <th><? echo fn_number_format($tot_booking_qnty,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_greyProd_qnty,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_knitCost,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_knitQty,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_knitbill,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
	<?
	exit();
}
if($action=="knitting_bill")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$exchange_rate=76;
	$costing_per_arr=return_library_array( "select job_no, costing_per_id from wo_pre_cost_dtls where status_active=1 and is_deleted=0", "job_no", "costing_per_id");
	
	$subconInBillDataArray=sql_select("select a.id as mst_id, a.bill_no, a.company_id, a.party_source, sum(b.delivery_qty) as qty, sum(b.amount) AS knit_bill from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_source=1 and a.process_id=2 group by a.id, a.bill_no, a.company_id, a.party_source");// b.order_id, b.currency_id
	/*foreach($subconInBillDataArray as $subRow)
	{
		$subconCostArray[$subRow[csf('order_id')]]['amnt']=$subRow[csf('knit_bill')];
		$subconCostArray[$subRow[csf('order_id')]]['qty']=$subRow[csf('qty')];
	}*/	
	
	$subconOutBillDataArray=sql_select("select a.id as mst_id, a.bill_no, sum(b.receive_qty) as qty, sum(b.amount) AS knit_bill from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.process_id=2 and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.bill_no");// b.order_id, b.currency_id
	/*foreach($subconOutBillDataArray as $subRow)
	{
		$subconCostArray[$subRow[csf('order_id')]]['amnt']+=$subRow[csf('knit_bill')];
		$subconCostArray[$subRow[csf('order_id')]]['qty']+=$subRow[csf('qty')];
	}*/
	
	
	$bookingArray=return_library_array( "select b.po_break_down_id, sum(b.grey_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id) and a.item_category in(2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id", "po_break_down_id", "qnty");
	
	$greyProdArray=return_library_array( "select po_breakdown_id, sum(quantity) as qnty from order_wise_pro_details where po_breakdown_id in($po_id) and entry_form=2 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "qnty");
	
	$knitCostArray=return_library_array( "select job_no, sum(amount) AS knit_charge from wo_pre_cost_fab_conv_cost_dtls where cons_process=1 and status_active=1 and is_deleted=0 group by job_no", "job_no", "knit_charge");
	?>
	<script>
    function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		document.getElementById('scroll_body2').style.overflow="auto";
		document.getElementById('scroll_body2').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('main_body').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="290px";
		document.getElementById('scroll_body2').style.overflowY="scroll";
		document.getElementById('scroll_body2').style.maxHeight="290px";
	}
	
	function print_report(data,party_source)
	{
		//alert("su..re");
		var report_title="Knitting Bill";
		var show_val_column='';
		if(party_source==1)
		{
			var r=confirm("Press \"OK\" to open with Order Comments\nPress \"Cancel\" to open without Order Comments");
			if (r==true)
			{
				show_val_column="1";
			}
			else
			{
				show_val_column="0";
			}
		}
		else show_val_column="0";
		var data=data+"*"+report_title+"*"+show_val_column;
		window.open("../../../../subcontract_bill/requires/knitting_bill_issue_controller.php?data="+data+'&action=knitting_bill_print', true );
	}
    </script>
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="main_body" style="width:470px;">
    
        <fieldset style="width:470px;">
        <legend>In Bound Bill</legend>
        	<table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="50">SL</th>
                    <th width="150">Bill No</th>
                    <th width="100">Bill Quantity</th>
                    <th>Bill Amount (USD)</th>
                </thead>
            </table>	
            <div style="width:470px; max-height:290px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					$i=1;
					$tot_bill_qnty=$tot_bill_amt=0;
					foreach($subconInBillDataArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$knitbill=$row[csf('knit_bill')]/$exchange_rate;
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="50"><? echo $i; ?></td>
							<td width="150" style="word-break:break-all;"><a href="##" onClick="print_report('<?php echo $row[csf('company_id')]."*".$row[csf('mst_id')]."*".$row[csf('bill_no')]; ?>','<?php echo $row[csf('party_source')]; ?>')"><? echo $row[csf('bill_no')]; ?></a></td>
                            <td width="100" align="right"><? echo fn_number_format($row[csf('qty')],2,'.',''); $tot_bill_qnty+=$row[csf('qty')]; ?></td>
							<td align="right"><? echo fn_number_format($knitbill,2,'.',''); $tot_bill_amt+=$knitbill; ?></td>
						</tr>
						<?
                        $i++;
					}
					?>
                	<tfoot>
                        <th colspan="2">Total</th>
                        <th><? echo fn_number_format($tot_bill_qnty,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_bill_amt,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
            <br>
        <legend>Out Bound Bill</legend>
        	<table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="50">SL</th>
                    <th width="150">Bill No</th>
                    <th width="100">Bill Quantity</th>
                    <th>Bill Amount (USD)</th>
                </thead>
            </table>	
            <div style="width:470px; max-height:290px; overflow-y:scroll" id="scroll_body2">
                <table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					$i=1;
					$tot_bill_qnty=$tot_bill_amt=0;
					foreach($subconOutBillDataArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$knitbill=$row[csf('knit_bill')]/$exchange_rate;	
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="50"><? echo $i; ?></td>
							<td width="150" style="word-break:break-all;"><? echo $row[csf('bill_no')]; ?></td>
                            <td width="100" align="right"><? echo fn_number_format($row[csf('qty')],2,'.',''); $tot_bill_qnty+=$row[csf('qty')]; ?></td>
							<td align="right"><? echo fn_number_format($knitbill,2,'.',''); $tot_bill_amt+=$knitbill; ?></td>
						</tr>
						<?
                        $i++;
					}
					?>
                	<tfoot>
                        <th colspan="2">Total</th>
                        <th><? echo fn_number_format($tot_bill_qnty,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_bill_amt,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
	<?
	exit();
}

if($action=="dye_fin_cost")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	
	$exchange_rate=76;
	$costing_per_arr=return_library_array( "select job_no, costing_per_id from wo_pre_cost_dtls where status_active=1 and is_deleted=0", "job_no", "costing_per_id");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	
	$subconInBillDataArray=sql_select("select b.order_id, sum(b.amount) AS knit_bill, sum(b.delivery_qty) as qty from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_source=1 and a.process_id=4 group by b.order_id");
	foreach($subconInBillDataArray as $subRow)
	{
		$subconCostArray[$subRow[csf('order_id')]]['amnt']=$subRow[csf('knit_bill')];
		$subconCostArray[$subRow[csf('order_id')]]['qty']=$subRow[csf('qty')];
	}	
	
	$subconOutBillDataArray=sql_select("select b.order_id, sum(b.amount) AS knit_bill, sum(b.receive_qty) as qty from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.process_id=4 and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id");
	foreach($subconOutBillDataArray as $subRow)
	{
		$subconCostArray[$subRow[csf('order_id')]]['amnt']+=$subRow[csf('knit_bill')];
		$subconCostArray[$subRow[csf('order_id')]]['qty']+=$subRow[csf('qty')];
	}
	
	$bookingArray=array();
	$bookingDataArray=sql_select( "select b.po_break_down_id, sum(b.grey_fab_qnty) as grey_qnty, sum(b.fin_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id) and a.item_category in(2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id");
	foreach($bookingDataArray as $bokRow)
	{
		$bookingArray[$bokRow[csf('po_break_down_id')]]['grey']=$bokRow[csf('grey_qnty')];
		$bookingArray[$bokRow[csf('po_break_down_id')]]['fin']=$bokRow[csf('qnty')];
	}
	
	$finProdArray=return_library_array( "select po_breakdown_id, sum(quantity) as qnty from order_wise_pro_details where po_breakdown_id in($po_id) and entry_form=7 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "qnty");
	
	$finCostArray=return_library_array( "select job_no, sum(amount) AS dye_fin_charge from wo_pre_cost_fab_conv_cost_dtls where cons_process not in(1,2,30,35) and status_active=1 and is_deleted=0 group by job_no", "job_no", "dye_fin_charge");

	?>
	<script>
		function openmypage_bill(po_id,type,tittle)
		{
			//alert("su..re"); return;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'multi_style_wise_post_costing_report_controller.php?po_id='+po_id+'&action='+type, tittle, 'width=560px, height=350px, center=1, resize=0, scrolling=0', '../../../');
		}
	</script>
    <div>
        <fieldset style="width:1113px;">
        	<table class="rpt_table" width="1110" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="40">SL</th>
                    <th width="60">Buyer</th>
                    <th width="80">PO No</th>
                    <th width="50">Year</th>
                    <th width="60">Job No</th>
                    <th width="80">Style Name</th>
                    <th width="110">Gmts Item</th>
                    <th width="90">Order Qty</th>
                    <th width="80">Booking Qty(Grey)</th>
                    <th width="80">Booking Qty(Fin)</th>
                    <th width="80">Finish Prod.</th>
                    <th width="90">Dye & Fin Cost</th>
                    <th width="80">Fabric Bill Qty</th>
                    <th>Bill Amount (USD)</th>
                </thead>
            </table>	
            <div style="width:1110px; max-height:290px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="1090" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					/*$po_ids=count(array_unique(explode(",",$po_id)));
					//echo $all_po_id;
					$all_po_ids=chop($all_po_id,','); $poIds_cond="";
					//print_r($all_po_ids);
					if($db_type==2 && $po_ids>990)
					{
						$poIds_cond=" and (";
						$poIdsArr=array_chunk(explode(",",$all_po_ids),990);
						//print_r($gate_outIds);
						foreach($poIdsArr as $ids)
						{
							$ids=implode(",",$ids);
							$poIds_cond.=" po_id  in($ids) or ";
						}
						$poIds_cond=chop($poIds_cond,'or ');
						$poIds_cond.=")";
					}
					else
					{
						$poIds_cond=" and  po_id  in($all_po_id)";
					}*/
					if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
					else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
					else $year_field="";//defined Later
						
					$sql="select a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id, b.po_number, b.pub_shipment_date, b.po_quantity, b.plan_cut , b.unit_price, b.po_total_price, b.shiping_status from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($po_id) order by b.pub_shipment_date";
					$result=sql_select($sql);
					$i=1; $tot_po_qnty=0; $tot_booking_qnty=0; $tot_finProd_qnty=0; $tot_finCost=0; $tot_finbill=0; $tot_knitQty=0;
					$condition= new condition();
					
					/* $poids=explode(",",$po_id);
					 foreach( $poids as $po)
					 {
						$condition->po_id(" in($po)");  
					 }*/
					if(trim(str_replace("'","",$po_id))!='')
					 {
						$condition->po_id(" in($po_id)"); 
					 }
					  $condition->init();
					  $conversion= new conversion($condition);
					//  echo $conversion->getQuery();
				 	$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
					
					  
					foreach($result as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$gmts_item='';
						$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
						}
						
						$dzn_qnty=0;
						$costing_per_id=$costing_per_arr[$row[csf('job_no')]];
						if($costing_per_id==1)
						{
							$dzn_qnty=12;
						}
						else if($costing_per_id==3)
						{
							$dzn_qnty=12*2;
						}
						else if($costing_per_id==4)
						{
							$dzn_qnty=12*3;
						}
						else if($costing_per_id==5)
						{
							$dzn_qnty=12*4;
						}
						else
						{
							$dzn_qnty=1;
						}
						
						$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
						$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
						$tot_po_qnty+=$order_qnty_in_pcs;
						
						$plun_qnty_in_pcs=$row[csf('plan_cut')]*$row[csf('ratio')];
						
						$bookingQty=$bookingArray[$row[csf('id')]]['fin'];
						$tot_booking_qnty+=$bookingQty;
						
						$bookingQtyGrey=$bookingArray[$row[csf('id')]]['grey'];
						$tot_booking_grey_qnty+=$bookingQtyGrey;
						
						$finProdQty=$finProdArray[$row[csf('id')]];
						$tot_finProd_qnty+=$finProdQty;
						
						$finCost=0;$not_yarn_dyed_cost_arr=array(1,2,30,35);
						foreach($conversion_cost_head_array as $process_id=>$val)
						{
							if(!in_array($process_id,$not_yarn_dyed_cost_arr))
							{
								$finCost+=array_sum($conversion_costing_arr_process[$row[csf('id')]][$process_id]);
							}
						}	
						//$finCost=($order_qnty_in_pcs/$dzn_qnty)*$finCostArray[$row[csf('job_no')]];
						//$finCost=($plun_qnty_in_pcs/$dzn_qnty)*$finCostArray[$row[csf('job_no')]];
						$tot_finCost+=$finCost;
						
						$finQty=$subconCostArray[$row[csf('id')]]['qty'];
						$finbill=$subconCostArray[$row[csf('id')]]['amnt']/$exchange_rate;
						
						$tot_finQty+=$finQty;
						$tot_finbill+=$finbill;
						
						
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="60" style="word-break:break-all;"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                            <td width="80" style="word-break:break-all;"><? echo $row[csf('po_number')]; ?></td>
                            <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                            <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                            <td width="80" style="word-break:break-all;"><? echo $row[csf('style_ref_no')]; ?></td>
                            <td width="110" style="word-break:break-all;"><? echo $gmts_item; ?></td>
                            <td width="90" align="right"><? echo $order_qnty_in_pcs; ?></td>
							<td width="80" align="right"><? echo fn_number_format($bookingQtyGrey,2,'.',''); ?></td>
                            <td width="80" align="right"><? echo fn_number_format($bookingQty,2,'.',''); ?></td>
							<td width="80" align="right"><? echo fn_number_format($finProdQty,2,'.',''); ?></td>
                            <td width="90" align="right"><? echo fn_number_format($finCost,2,'.',''); ?></td>
							<td width="80" align="right"><? echo fn_number_format($finQty,2,'.',''); ?></td>
							<td align="right"><a href="##" onClick="openmypage_bill('<? echo $row[csf('id')]; ?>','dyeing_bill','Dyeing bill Details')"><? echo fn_number_format($finbill,2,'.',''); ?></a></td>
						</tr>
					<?
					$i++;
					}
					?>
                	<tfoot>
                        <th colspan="7">Total</th>
                        <th><? echo $tot_po_qnty; ?></th>
                        <th><? echo fn_number_format($tot_booking_grey_qnty,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_booking_qnty,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_finProd_qnty,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_finCost,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_finQty,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_finbill,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
	<?
	exit();
}

if($action=="dyeing_bill")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$exchange_rate=76;
	$costing_per_arr=return_library_array( "select job_no, costing_per_id from wo_pre_cost_dtls where status_active=1 and is_deleted=0", "job_no", "costing_per_id");
	
	$subconInBillDataArray=sql_select("select a.id as mst_id, a.bill_no, a.company_id, a.party_source, sum(b.delivery_qty) as qty, sum(b.amount) AS knit_bill from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_source=1 and a.process_id=4 group by a.id, a.bill_no, a.company_id, a.party_source");//, b.order_id, b.currency_id
	
	$subconOutBillDataArray=sql_select("select a.id as mst_id, a.bill_no, sum(b.receive_qty) as qty, sum(b.amount) AS knit_bill from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.process_id=4 and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.bill_no");//, b.currency_id, b.order_id
	
	$bookingArray=return_library_array( "select b.po_break_down_id, sum(b.grey_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id) and a.item_category in(2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id", "po_break_down_id", "qnty");
	
	$greyProdArray=return_library_array( "select po_breakdown_id, sum(quantity) as qnty from order_wise_pro_details where po_breakdown_id in($po_id) and entry_form=2 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "qnty");
	
	$knitCostArray=return_library_array( "select job_no, sum(amount) AS knit_charge from wo_pre_cost_fab_conv_cost_dtls where cons_process=1 and status_active=1 and is_deleted=0 group by job_no", "job_no", "knit_charge");
	?>
	<script>
    function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		document.getElementById('scroll_body2').style.overflow="auto";
		document.getElementById('scroll_body2').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('main_body').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="290px";
		document.getElementById('scroll_body2').style.overflowY="scroll";
		document.getElementById('scroll_body2').style.maxHeight="290px";
	}
	
	function print_report(data)
	{
		//alert("su..re");
		var report_title="Dyeing And Finishing Bill";
		var show_val_column=1;
		var data=data+"*"+report_title+"*"+show_val_column;
		window.open("../../../../subcontract_bill/requires/sub_fabric_finishing_bill_issue_controller.php?data="+data+'&action=fabric_finishing_print', true );
	}
	
    </script>
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="main_body" style="width:470px;">
        <fieldset style="width:470px;">
        <legend>In Bound Bill</legend>
        	<table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="50">SL</th>
                    <th width="150">Bill No</th>
                    <th width="100">Bill Quantity</th>
                    <th>Bill Amount (USD)</th>
                </thead>
            </table>	
            <div style="width:470px; max-height:290px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					$i=1;
					$tot_bill_qnty=$tot_bill_amt=0;
					foreach($subconInBillDataArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$knitbill=$row[csf('knit_bill')]/$exchange_rate;
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="50"><? echo $i; ?></td>
							<td width="150" style="word-break:break-all;"><a href="##" onClick="print_report('<?php echo $row[csf('company_id')]."*".$row[csf('mst_id')]."*".$row[csf('bill_no')]; ?>')"><? echo $row[csf('bill_no')]; ?></a></td>
                            <td width="100" align="right"><? echo fn_number_format($row[csf('qty')],2,'.',''); $tot_bill_qnty+=$row[csf('qty')]; ?></td>
							<td align="right"><? echo fn_number_format($knitbill,2,'.',''); $tot_bill_amt+=$knitbill; ?></td>
						</tr>
						<?
                        $i++;
					}
					?>
                	<tfoot>
                        <th colspan="2">Total</th>
                        <th><? echo fn_number_format($tot_bill_qnty,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_bill_amt,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
            <br>
        <legend>Out Bound Bill</legend>
        	<table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="50">SL</th>
                    <th width="150">Bill No</th>
                    <th width="100">Bill Quantity</th>
                    <th>Bill Amount (USD)</th>
                </thead>
            </table>	
            <div style="width:470px; max-height:290px; overflow-y:scroll" id="scroll_body2">
                <table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					$i=1;
					$tot_bill_qnty=$tot_bill_amt=0;
					foreach($subconOutBillDataArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$knitbill=$row[csf('knit_bill')]/$exchange_rate;	
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="50"><? echo $i; ?></td>
							<td width="150" style="word-break:break-all;"><? echo $row[csf('bill_no')]; ?></td>
                            <td width="100" align="right"><? echo fn_number_format($row[csf('qty')],2,'.',''); $tot_bill_qnty+=$row[csf('qty')]; ?></td>
							<td align="right"><? echo fn_number_format($knitbill,2,'.',''); $tot_bill_amt+=$knitbill; ?></td>
						</tr>
						<?
                        $i++;
					}
					?>
                	<tfoot>
                        <th colspan="2">Total</th>
                        <th><? echo fn_number_format($tot_bill_qnty,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_bill_amt,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
	<?
	exit();
}
if($action=="yarn_cost_actual")
{
	echo load_html_head_contents("Yarn Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);
	$con = connect();
	$user_name = $_SESSION['logic_erp']["user_id"];
	
	$supplier_array=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
	unset($result_fab_result);
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+ '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');		
			d.close();
		}		
	</script>	
		<div style="width:980px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
		<fieldset style="width:975px; margin-left:7px">
			<div id="report_container">
				<table class="rpt_table" width="975" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th colspan="10"><b>Yarn Issue</b></th>
					</thead>
					<thead>
						<th width="40">SL</th>
						<th width="110">Issue Id</th>
						<th width="80">Issue Date</th>
						<th width="80">Purpose</th>
						<th width="80">Supplier</th>
						<th width="80">Lot</th>
						<th width="180">Yarn Description</th>
						<th width="90">Issue Qty.</th>
						<th width="80">Avg. Rate (USD)</th>
						<th>Cost ($)</th>
					</thead>
					<?
					$job_array=return_library_array( "select id,job_no_mst from wo_po_break_down where job_id in($job_id)", "id", "job_no_mst"  );

					//New From Here
					$booking_data=sql_select("SELECT a.id,a.booking_date,a.currency_id,a.item_category,a.booking_no,a.booking_type,a.is_short,b.po_break_down_id as po_id, b.job_no, (CASE WHEN a.item_category=12 and a.booking_type ='3'  THEN b.wo_qnty ELSE 0 END) AS wo_qnty, (CASE WHEN a.item_category=12 and a.booking_type ='3'  THEN b.amount ELSE 0 END) AS amount from wo_booking_mst a,wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no  and a.item_category in(2,3) and a.booking_type in(1,3,4)  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id=c.id and c.job_id in ($job_id)");
					foreach($booking_data as $row){
						$booking_array[$row[csf('booking_no')]]['btype']=$row[csf('booking_type')];
						$booking_array[$row[csf('booking_no')]]['is_short']=$row[csf('is_short')];
					}
					$usd_arr = array();
					$sqlSelectData = sql_select("select con_date,conversion_rate from currency_conversion_rate where currency=2 and is_deleted=0 order by con_date desc");
					foreach ($sqlSelectData as $row) 
					{
						$usd_arr[date('d-m-Y',strtotime($row[csf('con_date')]))] = $row[csf('conversion_rate')];
					}

					$sql_issue_data=sql_select("SELECT a.trans_id, c.id as job_id, c.job_no, c.buyer_name as buyer_name, c.style_ref_no as style_ref_no, d.brand_id as brand_id, e.company_id as company_id, e.booking_id as booking_id, e.booking_no as booking_no,d.supplier_id, e.issue_purpose,e.id as issue_id, e.issue_number, d.cons_quantity as issue_qnty, d.cons_rate, f.brand,f.id as product_id,f.lot,f.yarn_type, f.yarn_count_id, f.yarn_comp_type1st, f.yarn_comp_percent1st,e.issue_date,e.challan_no, f.product_name_details from order_wise_pro_details a, wo_po_details_master c,inv_transaction d,inv_issue_master e, product_details_master f where a.trans_id=d.id and c.job_no=e.buyer_job_no and d.mst_id=e.id and d.prod_id=f.id and d.item_category=1 and a.trans_type=2 and e.entry_form=3 and a.entry_form=3 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.issue_basis=1 and e.issue_purpose in(1,2) and c.id in ($job_id)");
					foreach($sql_issue_data as $row){
						$issue_id_arr[$row[csf('Issue_id')]]=$row[csf('Issue_id')];		
					}
					fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 144, 8, $issue_id_arr, $empty_arr);
					

					$transissueIdChk=array();
					foreach($sql_issue_data as $row)
					{
						if($transissueIdChk[$row[csf("trans_id")]]=="")
						{
							$transissueIdChk[$row[csf("trans_id")]] = $row[csf("trans_id")];
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							$booking_no=$row[csf('booking_no')];
							$booking_type=$booking_array[$booking_no]['btype'];
							$is_short=$booking_array[$booking_no]['is_short'];
							$exchangeRate=$usd_arr[date('d-m-Y',strtotime($row[csf('issue_date')]))];
							if($exchangeRate =="")
							{
								foreach ($usd_arr as $rate_date => $rat) 
								{
									if(strtotime($rate_date) <= strtotime($row[csf('issue_date')]))
									{
										$rate_date = date('d-m-Y',strtotime($rate_date));
										$exchangeRate=$rat;
										break;
									}
								}
							}
							$compPercent = $row[csf('yarn_comp_percent1st')];
							$yanrType = $row[csf('yarn_type')];
							$yarnComposition = $row[csf('yarn_comp_type1st')];
							$yarnCount = $row[csf('yarn_count_id')];
							$product_id=$row[csf('product_id')];
							$issue_id = $row[csf('issue_id')];
							$groupKey = $yarnCount."**".$yarnComposition."**".$compPercent."**".$yanrType."**".$product_id."**".$issue_id;
							if(($booking_type==1) && $is_short==2 &&  $booking_type!='')
							{
								$yarn_cost_data[$groupKey]['exchangeRate']= $exchangeRate;
								$yarn_cost_data[$groupKey]['cons_rate']= $row[csf('cons_rate')];
								$yarn_cost_data[$groupKey]['issue_purpose']= $row[csf('issue_purpose')];
								$avg_rate=$row[csf('cons_rate')]/$exchangeRate
								?>
								<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
									<td width="40"><? echo $i; ?></td>
									<td width="110" title="<?= $booking_type.'--'.$recv_purpose ?>"><p><? echo $row[csf('issue_number')]; ?></p></td>
									<td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
									<td width="80" ><p><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></p></td>
									<td width="80"><p><? echo $supplier_array[$row[csf('supplier_id')]]; ?></p></td>
									<td width="80"><p><? echo $row[csf('lot')]; ?></p></td>
									<td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
									<td align="right" width="90">
										<? 
											echo fn_number_format($row[csf('issue_qnty')],2); 
											$total_yarn_issue_qnty+=$row[csf('issue_qnty')];
										?>
									</td>
									<td align="right" width="80">
										<? 
											
											echo fn_number_format($avg_rate,2); 
										?>&nbsp;
									</td>
									<td align="right">
										<?
											$yarn_cost=$row[csf('issue_qnty')]*fn_number_format($avg_rate,2);
											echo fn_number_format($yarn_cost,2); 
											$total_yarn_cost+=$yarn_cost;
										?>
									</td>
								</tr>
								<?
								$i++;
							}
						}							
					}
					
					?>
					
					<tr style="font-weight:bold">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right">Total</td>
						<td align="right"><? echo fn_number_format($total_yarn_issue_qnty,2);?></td>
						<td>&nbsp;</td>
						<td align="right"><? echo fn_number_format($total_yarn_cost,2);?></td>
					</tr>
					<thead>
						<th colspan="10"><b>Yarn Return</b></th>
					</thead>
					<thead>
						<th width="40">SL</th>
						<th width="110">Return Id</th>
						<th width="80">Return Date</th>
						<th width="80">Purpose</th>
						<th width="80">Supplier</th>
						<th width="80">Lot</th>
						<th width="180">Yarn Description</th>
						<th width="90">Return Qty.</th>
						<th width="80">Avg. Rate (USD)</th>
						<th>Cost ($)</th>
				</thead>
					<?
					$total_yarn_return_qnty=0; $total_yarn_return_cost=0;
					$issue_return_res = sql_select("SELECT a.recv_number,a.booking_no, a.receive_date, a.supplier_id, a.receive_purpose, b.cons_quantity, b.issue_id,b.id as trans_id,d.brand,d.id as product_id,d.lot,d.yarn_type, d.yarn_count_id, d.yarn_comp_type1st,d.yarn_comp_percent1st, d.product_name_details from inv_receive_master a, inv_transaction b, inv_issue_master c,product_details_master d, gbl_temp_engine e where a.id = b.mst_id and a.issue_id = c.id and b.transaction_type = 4 and b.item_category = 1 and b.prod_id=d.id and a.entry_form = 9 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and e.ref_val=c.id and e.ref_from=8 and e.entry_form=144 and e.user_id=$user_name");
					$transIdChk=array();
					foreach($issue_return_res as $row)
					{
						if($transIdChk[$row[csf("trans_id")]]=="")
						{
							$transIdChk[$row[csf("trans_id")]] = $row[csf("trans_id")];
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							$booking_no=$row[csf('booking_no')];
							$booking_type=$booking_array[$booking_no]['btype'];
							$is_short=$booking_array[$booking_no]['is_short'];
							$compPercent = $row[csf('yarn_comp_percent1st')];
							$yanrType = $row[csf('yarn_type')];
							$yarnComposition = $row[csf('yarn_comp_type1st')];
							$yarnCount = $row[csf('yarn_count_id')];
							$product_id=$row[csf('product_id')];
							$issue_id = $row[csf('issue_id')];
							$groupKey = $yarnCount."**".$yarnComposition."**".$compPercent."**".$yanrType."**".$product_id."**".$issue_id;
								$exchangeRate=$yarn_cost_data[$groupKey]['exchangeRate'];
								$cons_rate=$yarn_cost_data[$groupKey]['cons_rate'];
								$issue_purpose=$yarn_cost_data[$groupKey]['issue_purpose'];

							if(($booking_type==1 || $booking_type==4) &&  $is_short==2 &&  $booking_type!='') //Main and Sample Faric Booking
							{
								$avg_rate=$cons_rate/$exchangeRate
								?>
								<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
									<td width="40"><? echo $i; ?></td>
									<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
									<td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
									<td width="80"><p><? echo $yarn_issue_purpose[$issue_purpose]; ?></p></td>
									<td width="80"><p><? echo $supplier_array[$row[csf('supplier_id')]]; ?></p></td>
									<td width="80"><p><? echo $row[csf('lot')]; ?></p></td>
								
									<td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
									<td align="right" width="90">
										<? 
											echo fn_number_format($row[csf('cons_quantity')],2); 
											$total_yarn_return_qnty+=$row[csf('cons_quantity')];
										?>
									</td>
									<td align="right" width="80">
										<? 
											echo fn_number_format($avg_rate,2); 
										?>&nbsp;
									</td>
									<td align="right">
										<?
											$yarn_return_cost=$row[csf('cons_quantity')]*fn_number_format($avg_rate,2);
											echo fn_number_format($yarn_return_cost,2); 
											$total_yarn_return_cost+=$yarn_return_cost;
										?>
									</td>
								</tr>
								<?
								$i++;
							}
						}
					}
					?>
					<tr style="font-weight:bold">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right">Total</td>
						<td align="right"><? echo fn_number_format($total_yarn_return_qnty,2);?></td>
						<td>&nbsp;</td>
						<td align="right"><? echo fn_number_format($total_yarn_return_cost,2);?></td>
					</tr>
				
					<tfoot>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right">Balance</th>
						<th align="right"><? echo fn_number_format(($total_yarn_issue_qnty+$total_trans_in_qnty)-($total_yarn_return_qnty+$total_trans_out_qnty),2); ?></th>
						<th>&nbsp;</th>
						<th align="right"><? echo fn_number_format(($total_yarn_cost+$total_trans_in_cost)-($total_yarn_return_cost+$total_trans_out_cost),2); ?></th>
					</tfoot>
				</table>	
			</div>
		</fieldset>  
	<?	
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_name." and ENTRY_FORM=144");
	oci_commit($con);
	disconnect($con);
	exit();

}

if($action=="yarn_dye_twist_cost_actual")
{
	echo load_html_head_contents("Yarn Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);
	
	$supplier_array=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
		
	$job_array=array();
	 $sql_job="SELECT  a.job_quantity,b.job_no_mst,b.po_quantity,b.id as po_id from wo_po_break_down b, wo_po_details_master a where  a.id=b.job_id and a.id=$job_id and b.status_active=1 and b.is_deleted=0";
	$resultjob = sql_select($sql_job);
	$job_no="";
	foreach($resultjob as $row)
	{
		$job_array[$row[csf('po_id')]]['po_qty']+=$row[csf('po_quantity')];
		$total_job_qty+=$row[csf('po_quantity')];
		$job_array[$row[csf('po_id')]]['job_qty']=$row[csf('job_quantity')];
		$job_no.="'".$row[csf('job_no_mst')]."'".',';
	}
	$job_nos=rtrim($job_no,',');
	$jobnos=implode(",",array_unique(explode(",",$job_nos)));
	$po_ids=array_unique(explode(",",$po_id));
	

	?>
	<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+ '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');	
		d.close();
	}	
	function generate_worder_report(version_type, booking_no, cbo_company_name, update_id,form_name,entry_form, cbo_pay_mode, cbo_supplier_name, show_comment, path,action_type) {	
				
				if (action_type == 'show_trim_booking_report')
				{
					var action_method = "action=show_trim_booking_report";
				}				
				if (entry_form == 41) {

						if (version_type == 1) {
							report_title = "&report_title=Yarn Dyeing Charge Booking";
							http.open("POST", "../../../../order/woven_order/requires/yarn_dyeing_charge_booking_controller.php", true);
						}
						else if (version_type == 2) {
							report_title = "&report_title=Partial Fabric Booking";
							http.open("POST", "../../../../order/woven_order/requires/yarn_dyeing_charge_booking_controller2.php", true);
						} 
						 
					
					
				}
				else {
					report_title = "&report_title=Yarn Service Work Order";
					http.open("POST", "../../../../order/woven_order/requires/yarn_service_work_order_controller.php", true);
				}
				var data = action_method + report_title + '&txt_booking_no=' + "'" + booking_no + "'" + '&cbo_company_name=' + "'" + cbo_company_name + "'" + '&update_id=' + "'" + update_id + "'" + '&cbo_pay_mode=' + "'" + cbo_pay_mode + "'" + '&cbo_supplier_name=' + "'" + cbo_supplier_name + "'" + '&show_comment=' + "'" + show_comment + "'" + '&path=' + "'" + path + "'";
				//alert(data);
				
				http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = generate_fabric_report_reponse;
			}

			function generate_fabric_report_reponse() {
				if (http.readyState == 4) {
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' + '<html><head><title></title></head><body>' + http.responseText + '</body</html>'); 
					d.close();
				}
			}
	</script>	
	<div style="width:1100px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:1000px; margin-left:7px">
		<div id="report_container">
            <table class="rpt_table" width="1100" cellpadding="0" cellspacing="0"  border="1" rules="all">
            	<thead>
					<th colspan="10"><b>Yarn Issue (Additional Value)</b></th>
				</thead>
				<thead>
                	<th width="40">SL</th>
                    <th width="110">Issue ID</th>
					 <th width="120">Wo No</th>
                    <th width="80">Issue Date</th>
                    <th width="80">Purpose</th>
                    <th width="70">Supplier</th>
                    <th width="250">Yarn Description</th>
                    <th width="90">Issue Qty.</th>
                    <th width="80">Rate (USD)</th>
                    <th width="80">Cost ($)</th>
				</thead>
				<tbody id="table_search">
                <?
			$yarn_dyeing_costArray=array(); //product_id
			$yarndyeing_sql="SELECT b.booking_date,b.id,b.currency,d.is_short,b.ydw_no, a.job_no_id, a.job_no,a.yarn_color,a.product_id, b.entry_form, avg(a.dyeing_charge) as dyeing_charge, sum(a.amount) as amnt,sum(a.yarn_wo_qty) as yarn_wo_qty from wo_yarn_dyeing_mst b,wo_yarn_dyeing_dtls a, wo_booking_mst d where b.id=a.mst_id and b.entry_form in(41,94) and a.fab_booking_no=d.booking_no and a.status_active=1 and a.is_deleted=0 and a.job_no_id=$job_id group by b.id,b.currency,d.is_short,a.job_no,a.product_id,b.ydw_no, a.yarn_color,b.booking_date, a.job_no_id, b.entry_form";
			//echo $yarndyeing_sql; die;
			$yarndyeing_result = sql_select($yarndyeing_sql);
			foreach($yarndyeing_result as $yarnRow)
			{
				$yarn_dyeing_costArray[$yarnRow[csf('job_no')]]['avg_rate']=$yarnRow[csf('amnt')]/$yarnRow[csf('yarn_wo_qty')];
				$yarn_dyeing_costArray2[$yarnRow[csf('id')]]['job']=$yarnRow[csf('job_no')];
				$yarn_dyeing_costArray2[$yarnRow[csf('id')]]['job_id']=$yarnRow[csf('job_no_id')];
				$yarn_dyeing_costArray2[$yarnRow[csf('id')]]['ydw_no']=$yarnRow[csf('ydw_no')];
				$yarn_dyeing_isshort_arr[$yarnRow[csf('ydw_no')]]['is_short']=$yarnRow[csf('is_short')];
				$yarn_dyeing_isshort_arr[$yarnRow[csf('ydw_no')]]['booking_date']=$yarnRow[csf('booking_date')];
				$yarn_dyeing_curr_arr[$yarnRow[csf('ydw_no')]]['currency']=$yarnRow[csf('currency')];
				if($yarnRow[csf('entry_form')]==41){
					$yarn_dyeing_rate_arr[$yarnRow[csf('job_no')]][$yarnRow[csf('yarn_color')]]['rate']=$yarnRow[csf('dyeing_charge')];
				}
				else{
					$yarn_service_rate_arr[$yarnRow[csf('job_no')]]['rate']=$yarnRow[csf('dyeing_charge')];
				}		
				$yarn_dyeing_mst_idArr[$yarnRow[csf('id')]]=$yarnRow[csf('id')];
			}
			$yarn_dyed_cond = where_con_using_array($yarn_dyeing_mst_idArr,0,"c.booking_id");

			$sql_issue="SELECT c.id as issue_id, c.issue_number, c.issue_purpose, c.booking_id, a.job_no, a.prod_id, e.is_short, e.ydw_no, a.supplier_id, b.product_name_details, max(a.transaction_date) as transaction_date,c.issue_purpose, b.lot,a.dyeing_color_id as color, sum(case when c.issue_purpose in(2,15,38) then a.cons_quantity else 0 end) as cons_quantity from inv_transaction a,product_details_master b,inv_issue_master c, wo_yarn_dyeing_mst e where a.prod_id=b.id and c.id=a.mst_id and c.entry_form=3 and c.item_category=1 and a.transaction_type=2 and a.item_category=1 and a.status_active=1 and e.id=c.booking_id and a.is_deleted=0 and c.issue_purpose in(2,15,38) $yarn_dyed_cond group by c.id,c.issue_number,c.booking_id,a.job_no,a.prod_id, c.issue_purpose,b.lot,a.dyeing_color_id, e.is_short, e.ydw_no, a.supplier_id, b.product_name_details";
			//echo $sql_issue; die;
			$resultyarnissue = sql_select($sql_issue);
			$all_recv_prod_ids=array();
				
                $i=1; $total_yarn_recv_qnty=0; $total_yarn_cost=0;$usd_id=2;
				foreach($resultyarnissue as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					$yarnissue_id[$row[csf('issue_id')]]=$row[csf('issue_id')];
					$job_id=$yarn_dyeing_costArray2[$row[csf('booking_id')]]['job_id'];
					$jobNo=$yarn_dyeing_costArray2[$row[csf('booking_id')]]['job'];
					$yarn_issue_pur[$row[csf('issue_id')]]=$row[csf('issue_purpose')];
					if($row[csf('issue_purpose')]==2){
						$dye_charge=$yarn_dyeing_rate_arr[$jobNo][$row[csf('color')]]['rate'];
						$product_wise_yarn_rate[$row[csf('issue_id')]]=$dye_charge;
					}
					else{
						$dye_charge=$yarn_service_rate_arr[$jobNo]['rate'];
						$product_wise_yarn_rate[$row[csf('issue_id')]]=$dye_charge;
					}
					$ydw_no=$row[csf('ydw_no')];
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
						 <td width="120"><?php echo $ydw_no;?></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transaction_date')]); ?></td>
                        <td width="70"><p><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></p></td>
                        <td width="70"><p><? echo $supplier_array[$row[csf('supplier_id')]]; ?></p></td>                      
                        <td width="250"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="90" title="<? echo $row[csf('cons_quantity')]; ?>"><? echo fn_number_format($row[csf('cons_quantity')],2); ?></td>
                        <td align="right" width="80"><? echo fn_number_format($dye_charge,2); ?></td>
                        <td align="right" width="80"><? echo fn_number_format($row[csf('cons_quantity')]*$dye_charge,2); ?>
                        </td>
                    </tr>
                	<?
					$total_yarn_recv_qnty+=$row[csf('cons_quantity')];
					$total_yarn_cost+=$row[csf('cons_quantity')]*$dye_charge;
					$i++;
                }
				//print_r($yarn_rec_job_arr);
                ?>
                <tr style="font-weight:bold; background:#CCC">
                    <td>&nbsp;</td>
					<td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"> Total</td>
                    <td align="right"><? echo fn_number_format($total_yarn_recv_qnty,2);?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo fn_number_format($total_yarn_cost,2);?></td>
                </tr>
				</tbody>
                <div>
                <thead >
                    <th colspan="10"><b>Yarn Issue Return</b></th>
                </thead>
                <thead>
                	<th>SL</th>
                	<th width="110">Return Id</th>
                    <th width="80">Return Date</th>
                    <th width="120">Issue ID</th>
                    <th width="70">Purpose</th>
                    <th width="70">Supplier</th>
                    <th width="250">Yarn Description</th>
                    <th width="90">Return Qty.</th>
                    <th width="80">Rate (USD)</th>
                    <th width="80">Cost ($)</th>
               </thead>
			   <tbody id="table_search2">
                <?
				$yarn_issue_cond = where_con_using_array($yarnissue_id,0,"a.issue_id");
                $total_yarn_return_qnty=0; $total_yarn_return_cost=0;
				$sql_issue_ret="SELECT a.prod_id,b.lot,b.color,c.recv_number, c.booking_no, a.issue_id, d.issue_number, d.supplier_id, sum(a.cons_quantity) as cons_quantity, b.product_name_details, max(a.transaction_date) as transaction_date from inv_transaction a, product_details_master b,inv_receive_master c, inv_issue_master d, wo_yarn_dyeing_mst e where a.prod_id=b.id and c.id=a.mst_id and e.id=c.booking_id and d.id=a.issue_id and c.entry_form=9 and c.item_category=1 and c.receive_basis=1 and a.receive_basis=1 and a.transaction_type=4 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 $yarn_issue_cond group by a.prod_id,b.lot,b.color,c.recv_number, c.booking_no, a.issue_id, d.issue_number, d.supplier_id, b.product_name_details";
				//echo $sql_issue_ret; die;
				$resultissue_ret = sql_select($sql_issue_ret);
            	$k=1;
				foreach($resultissue_ret as $row)
				{
					if ($k%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
					$job_no=$yarn_issue_wise_job[$row[csf('issue_id')]];
					$dye_charge=$product_wise_yarn_rate[$row[csf('issue_id')]];
					//$yarn_issue_amount_arr[$job_id]['return_amount']+=$rate*$row[csf('cons_quantity')];
					$yarn_return_cost=$dye_charge*$row[csf('cons_quantity')];
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k;?>">
                    	<td width="40"><? echo $k; ?></td>
                    	<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transaction_date')]); ?></td>
						<td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
						<td width="120"><p><? echo $yarn_issue_purpose[$yarn_issue_pur[$row[csf('issue_id')]]]; ?></p></td>
                        <td width="70"><p><? echo $supplier_array[$row[csf('supplier_id')]]; ?></p></td>
                        <td width="250"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="90"><? echo fn_number_format($row[csf('cons_quantity')],2);  ?></td>
                        <td align="right" width="80"><? echo fn_number_format($dye_charge,2); ?>&nbsp;</td>
                        <td align="right" width="80" colspan="2"><? echo fn_number_format($yarn_return_cost,2); ?></td>
                    </tr>
                	<?
                	$k++;
					$total_yarn_return_qnty+=$row[csf('cons_quantity')];
					$total_yarn_return_cost+=$yarn_return_cost;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo fn_number_format($total_yarn_return_qnty,2);?></td>
                    <td>&nbsp;</td>
                    <td align="right"  colspan="2"><? echo fn_number_format($total_yarn_return_cost,2);?></td>
                </tr>
				</tbody>
               
               	<tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th align="right">Balance</th>
                    <th align="right"><? echo fn_number_format(($total_yarn_recv_qnty+$total_trans_in_qnty)-($total_yarn_return_qnty+$total_trans_out_qnty),2); ?></th>
                    <th>&nbsp;</th>
                    <th align="right"  colspan="2"><? echo fn_number_format(($total_yarn_cost+$total_trans_in_cost)-($total_yarn_return_cost+$total_trans_out_cost),2); ?></th>
                </tfoot>
                </div>
            </table>	
		</div>
	<script>
  	setFilterGrid("table_search",-1);setFilterGrid("table_search2",-1);
  </script>
	</fieldset>  
<?
exit();
}

if($action=="knit_cost_actual")
{
	echo load_html_head_contents("Knitting Cost Actual Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

	$product_array=return_library_array( "select id, product_name_details from product_details_master where item_category_id=13", "id", "product_name_details");

	?>
	<script>
	function generate_work_order_report(booking_no, cbo_company_name,show_comment,path,action_type) {
				
				if (action_type == 'show_trim_booking_report3')
				{
					var action_method = "action=show_trim_booking_report3";
				}
				
				report_title = "&report_title=Service Booking Sheet For Knitting";
				http.open("POST", "../../../../order/woven_order/requires/service_booking_knitting_controller.php", true);
						
				var data = action_method + report_title +
				'&txt_booking_no=' + "'" + booking_no + "'" +
				'&cbo_company_name=' + "'" + cbo_company_name + "'" +
				'&show_comment=' + "'" + show_comment + "'" +
				'&path=' + "'" + path + "'";
				//alert(data);
				
				http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = generate_fabric_report_reponse;
			}

			function generate_fabric_report_reponse() {
				if (http.readyState == 4) {
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
						'<html><head><title></title></head><body>' + http.responseText + '</body</html>');
					d.close();
				}
			}
	</script>
	<fieldset style="width:980px; margin-left:7px">
        <table class="rpt_table" width="875" cellpadding="0" cellspacing="0" border="1" rules="all">
		<caption style="background:#CCDDEE"><b>Knitting Cost Actaul</b></caption>
            <thead>
                <th width="40">SL</th>
                <th width="110">System No.</th> 
				<th width="110">Service Booking No.</th>
                <th width="80">Recv. Date</th>
                <th width="180">Fabric Description</th>
                <th width="110">Knitting Qty</th>
                <th width="80">Rate</th>
                <th>Amount (USD)</th>
            </thead>
        </table>
        <div style="width:875px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="855" cellpadding="0"  cellspacing="0">  
			
				 
				<tbody id="table_search">
            	
				<?
				$condition= new condition();//PROCESS_COSTING_MAINTAIN
				
				 if(str_replace("'","",$po_id) !=''){
					  $condition->po_id("in($po_id)");
				 }
				 	 $condition->init();
				
				
				  $conversion= new conversion($condition);
				 $conversion_costing_arr_process_arr=$conversion->getAmountArray_by_orderAndProcess();
				  $conversion= new conversion($condition);
				  $conversion_costing_arr_process_qty=$conversion->getQtyArray_by_orderAndProcess();
				  
				
				$plan_details_array = return_library_array("select dtls_id, booking_no as booking_no from ppl_planning_entry_plan_dtls where po_id in($po_id) group by dtls_id,booking_no", "dtls_id", "booking_no");
				//print_r($plan_details_array);
				$reqs_array = array();
				$reqs_sql = sql_select("select knit_id, requisition_no as reqs_no, sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 group by knit_id, requisition_no");
				foreach ($reqs_sql as $row)
				 {
					//$reqs_array[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
					$reqs_array[$row[csf('reqs_no')]]['knit_id'] = $row[csf('knit_id')];
				}
				unset($reqs_sql);
				$sql_wo_aop="select a.id,a.item_category,a.booking_no,a.booking_type,a.is_short,b.po_break_down_id as po_id,
				(CASE WHEN a.item_category=12 and a.booking_type ='3'  THEN b.wo_qnty ELSE 0 END) AS wo_qnty,
				(CASE WHEN a.item_category=12 and a.booking_type ='3'  THEN b.amount ELSE 0 END) AS amount
				 from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no  and a.item_category in(2,3,12) and a.booking_type in(1,3,4)  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and b.po_break_down_id in($po_id)";
				$result_aop_rate=sql_select( $sql_wo_aop );
				foreach ($result_aop_rate as $row)
				{
					if($row[csf('item_category')]==12)
					{
						$wo_qnty=$row[csf('wo_qnty')];
						$amount=$row[csf('amount')];
						$avg_wo_aop_rate=$amount/$wo_qnty;
						$aop_prod_array[$row[csf('po_id')]]['aop_rate']=$avg_wo_aop_rate;
					}
					else
					{
						$booking_array[$row[csf('booking_no')]]['btype']=$row[csf('booking_type')];
						$booking_array[$row[csf('booking_no')]]['is_short']=$row[csf('is_short')];
						
						$booking_type_arr[$row[csf('id')]]['btype']=$row[csf('booking_type')];
						$booking_type_arr[$row[csf('id')]]['is_short']=$row[csf('is_short')];
					}
					//$product_id_arr[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].",";
				}
				unset($result_aop_rate);
					
				
                $i=1;  
				$total_in_knit_qnty=0; $total_in_knit_cost_tk=0; $total_in_knit_cost_usd=0;
				$total_out_knit_qnty=0; $total_out_knit_cost_tk=0; $total_out_knit_cost_usd=0;
				$total_knit_qnty=0; $total_knit_cost_tk=0; $total_knit_cost_usd=0;
				$sql_grey_trans="select  a.recv_number,a.company_id,a.service_booking_no,a.receive_basis,a.booking_no,a.receive_date,b.po_breakdown_id as po_id,c.febric_description_id as deter_id,c.body_part_id,c.prod_id,c.kniting_charge,
				 (CASE WHEN b.entry_form =2 and b.trans_type in(1) and a.item_category=13  THEN  b.quantity ELSE 0 END) AS grey_qnty
				
				  from inv_receive_master a, order_wise_pro_details b,pro_grey_prod_entry_dtls c where a.id=c.mst_id and c.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2) and a.item_category in(2,13)  and b.trans_type in(1) and b.po_breakdown_id in($po_id) ";
				  //group by a.recv_number,a.receive_basis,b.po_breakdown_id,a.booking_no,a.receive_date,c.febric_description_id,c.prod_id,c.body_part_id
				$result_grey=sql_select( $sql_grey_trans );
				$currency_id=1;$usd_id=2;
                foreach($result_grey as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					$receive_basis=$row[csf('receive_basis')];
					$company_id=$row[csf('company_id')];
					$po_id=$row[csf('po_id')];
				$process_costing=return_field_value("process_costing_maintain", "variable_settings_production", "company_name=$company_id and variable_list=34 and is_deleted=0 and status_active=1");
				//echo $process_costing.'FF';
						
					
					
					if($db_type==0)
						{
							$conversion_date=change_date_format($row[csf('receive_date')], "Y-m-d", "-",1);
						}
						else
						{
							$conversion_date=change_date_format($row[csf('receive_date')], "d-M-y", "-",1);
						}
						$exchange_rate=set_conversion_rate($usd_id,$conversion_date );
						
						if($process_costing==1)//Yes//Pre Costing
						{
							$knit_cost_mkt=array_sum($conversion_costing_arr_process_arr[$po_id][1]);
							$knit_qty_mkt=array_sum($conversion_costing_arr_process_qty[$po_id][1]);
							$knit_charge=$knit_cost_mkt/$knit_qty_mkt;
						}
						else //Knitting Prod.
						{
							if($currency_id==1) //TK
							{
								$knit_charge=$row[csf('kniting_charge')]/$exchange_rate;
								
							}
							else
							{
								$knit_charge=$row[csf('kniting_charge')];
							}
						}
						
						
						//$knit_charge=$row[csf('kniting_charge')];//$prodcostArray[$row[csf('po_breakdown_id')]][$row[csf('deter_id')]][$row[csf('body_part_id')]]['knit_charge'];
						//echo $knit_charge.'='.$row[csf('deter_id')].'ff'.$row[csf('po_breakdown_id')];
						if($receive_basis==1) //Booking Basis
						{
							 $booking_no=$row[csf('booking_no')];
							$booking_type=$booking_array[$booking_no]['btype'];
							$is_short=$booking_array[$booking_no]['is_short'];
						}
						else if($receive_basis==2) //Knit plan Basis
						{
							$booking_no=$plan_details_array[$row[csf('booking_no')]];
							$booking_type=$booking_array[$booking_no]['btype'];
							$is_short=$booking_array[$booking_no]['is_short'];
						}
						else if($receive_basis==4) //Salse Basis
						{
							 $prog_no=$row[csf('booking_no')];
							$booking_no=$plan_details_array[$prog_no];
							$booking_type=$booking_array[$booking_no]['btype'];
							$is_short=$booking_array[$booking_no]['is_short'];
						}
						//echo $booking_type.'='.$is_short;
						if(($booking_type==1 || $booking_type==4) &&  $is_short==2 &&  $booking_type!='') //Main and Sample Faric Booking
						{
							$grey_amt=$row[csf('grey_qnty')]*$knit_charge;
							//echo $row[csf('grey_qnty')].'*'.$knit_charge.', ';
						}
						//echo $booking_no.'='.$row[csf('service_booking_no')];
              		 if(($booking_type==1 || $booking_type==4) &&  $is_short==2 &&  $booking_type!='') //Main and Sample Faric Booking
					 {
					 	$show_comment=1;$path="../../../../";$action_type="show_trim_booking_report3";
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
						
						 <td width="110"><p><span style="cursor: pointer; text-decoration: underline; color: blue;" title="Print Booking"
							onClick="generate_work_order_report('<?php echo $row[csf('service_booking_no')];?>',<?php echo $row[csf('company_id')];?>,<?php echo $show_comment;?>,'<?php echo $path;?>','<?php echo $action_type;?>')"><?php echo $row[csf('service_booking_no')];?></span></p></td>
						
                        <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td width="180"><p><? echo $product_array[$row[csf('prod_id')]]; ?></p></td>
                        <td align="right" width="110"><? echo fn_number_format($row[csf('grey_qnty')],2); ?></td>
                        <td align="right" width="80"><? echo fn_number_format($knit_charge,4); ?>&nbsp;</td>
                        <td align="right">
                            <?
								$amount_usd=$grey_amt;
                                echo fn_number_format($amount_usd,2); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_in_knit_qnty+=$row[csf('grey_qnty')];
				
					$total_in_knit_cost_usd+=$amount_usd;
						}
                }
                ?>
                <tr bgcolor="#CCCCCC">
                    <td>&nbsp;</td>
					 <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><b>Total</b></td>
                    <td align="right"><? echo fn_number_format($total_in_knit_qnty,2); ?></td>
                    <td align="right"><? echo fn_number_format($total_in_knit_cost_usd/$total_in_knit_qnty,2); ?>&nbsp;</td>
                    <td align="right"><? echo fn_number_format($total_in_knit_cost_usd,2); ?></td>
                </tr>
				</tbody>
                
              
			</table>
        </div>	
	<script>
  	setFilterGrid("table_search",-1);
  </script>
	</fieldset>  
<?
exit();
}


if($action=="knit_cost_actual_popup_bk")
{
	echo load_html_head_contents("Knitting Cost Actual Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

	$product_array=return_library_array( "select id, product_name_details from product_details_master where item_category_id=13", "id", "product_name_details");

	?>
	<script>
	function generate_work_order_report(booking_no, cbo_company_name,show_comment,path,action_type) {				
		if (action_type == 'show_trim_booking_report3')
		{
			var action_method = "action=show_trim_booking_report3";
		}		
		report_title = "&report_title=Service Booking Sheet For Knitting";
		http.open("POST", "../../../../order/woven_order/requires/service_booking_knitting_controller.php", true);
				
		var data = action_method + report_title +
		'&txt_booking_no=' + "'" + booking_no + "'" +
		'&cbo_company_name=' + "'" + cbo_company_name + "'" +
		'&show_comment=' + "'" + show_comment + "'" +
		'&path=' + "'" + path + "'";
		//alert(data);
		
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_fabric_report_reponse;
	}

	function generate_fabric_report_reponse() {
		if (http.readyState == 4) {
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><title></title></head><body>' + http.responseText + '</body</html>');
			d.close();
		}
	}
	</script>
	<fieldset style="width:980px; margin-left:7px">
        <table class="rpt_table" width="875" cellpadding="0" cellspacing="0" border="1" rules="all">
		<caption style="background:#CCDDEE"><b>Knitting Cost Actaul</b></caption>
            <thead>
                <th width="40">SL</th>
                <th width="110">System No.</th> 
				<th width="110">Service Booking No.</th>
                <th width="80">Recv. Date</th>
                <th width="180">Fabric Description</th>
                <th width="110">Knitting Qty</th>
                <th width="80">Rate</th>
                <th>Amount (USD)</th>
            </thead>
        </table>
        <div style="width:875px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="855" cellpadding="0"  cellspacing="0">  
			
				 
				<tbody id="table_search">            	
				<?
				$plan_details_array = return_library_array("select a.dtls_id, a.booking_no as booking_no from ppl_planning_entry_plan_dtls a join wo_po_break_down b on a.po_id=b.id where b.job_id=$job_id group by a.dtls_id,a.booking_no", "dtls_id", "booking_no");
				//print_r($plan_details_array);
				$reqs_array = array();
				$reqs_sql = sql_select("select knit_id, requisition_no as reqs_no, sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 group by knit_id, requisition_no");
				foreach ($reqs_sql as $row)
				 {
					$reqs_array[$row[csf('reqs_no')]]['knit_id'] = $row[csf('knit_id')];
				}
				unset($reqs_sql);
				$usd_id=2;
				 $knit_fin_fab_array=array(); 
			  $converson_fab_cost_sql="select a.company_id,b.currency_id,a.bill_no,b.order_id, a.bill_date, b.id as dtls_id,b.item_id, b.rate, b.receive_qty,b.rate, b.amount as amount  from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b, wo_po_break_down c where a.id=b.mst_id and c.id=b.order_id and c.job_id=$job_id and a.process_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   order by b.item_id";


				 $con_fab_data=sql_select($converson_fab_cost_sql);

				 foreach($con_fab_data as $row )
				 {
					$currency_id=$row[csf('currency_id')];
					$company_id=$row[csf('company_id')];
					$conversion_date=change_date_format($row[csf('bill_date')], "d-M-y", "-",1);
					$currency_rate=set_conversion_rate($usd_id,$conversion_date,$company_id );
					$knit_subcon_array[$row[csf('dtls_id')]]['currency_id']=$currency_id;
					$knit_subcon_array[$row[csf('dtls_id')]]['company_id']=$company_id;
					$knit_subcon_array[$row[csf('dtls_id')]]['bill_date']=$row[csf('bill_date')];

					if($currency_id==2) //Usd
					{	
					 $knit_fin_fab_array[$row[csf('order_id')]]['knit_gross_amt']=$row[csf('amount')];
				  	 $knit_fin_fab_array[$row[csf('order_id')]]['rate']=$row[csf('rate')];
					}
					else
					{
						$knit_fin_fab_array[$row[csf('order_id')]]['knit_gross_amt']=$row[csf('amount')]/$currency_rate;
						$knit_fin_fab_array[$row[csf('order_id')]]['rate']=$row[csf('rate')]/$currency_rate;
					}
				 }

				 unset($con_fab_data);
				$sql_wo_aop="select a.id,a.item_category,a.booking_no,a.booking_type,a.is_short,b.po_break_down_id as po_id,
				(CASE WHEN a.item_category=12 and a.booking_type ='3'  THEN b.wo_qnty ELSE 0 END) AS wo_qnty,
				(CASE WHEN a.item_category=12 and a.booking_type ='3'  THEN b.amount ELSE 0 END) AS amount
				 from wo_booking_mst a,wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no and c.id=b.po_break_down_id  and a.item_category in(2,3,12) and a.booking_type in(1,3,4)  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and c.job_id=$job_id";
				$result_aop_rate=sql_select( $sql_wo_aop );
				foreach ($result_aop_rate as $row)
				{
					if($row[csf('item_category')]==12)
					{
						$wo_qnty=$row[csf('wo_qnty')];
						$amount=$row[csf('amount')];
						$avg_wo_aop_rate=$amount/$wo_qnty;
						$aop_prod_array[$row[csf('po_id')]]['aop_rate']=$avg_wo_aop_rate;
					}
					else
					{
						$booking_array[$row[csf('booking_no')]]['btype']=$row[csf('booking_type')];
						$booking_array[$row[csf('booking_no')]]['is_short']=$row[csf('is_short')];
						
						$booking_type_arr[$row[csf('id')]]['btype']=$row[csf('booking_type')];
						$booking_type_arr[$row[csf('id')]]['is_short']=$row[csf('is_short')];
					}
					//$product_id_arr[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].",";
				}
				unset($result_aop_rate);
					
				
                $i=1;  
				$total_in_knit_qnty=0; $total_in_knit_cost_tk=0; $total_in_knit_cost_usd=0;
				$total_out_knit_qnty=0; $total_out_knit_cost_tk=0; $total_out_knit_cost_usd=0;
				$total_knit_qnty=0; $total_knit_cost_tk=0; $total_knit_cost_usd=0;
				$sql_grey_trans="SELECT  a.recv_number,a.company_id,d.currency_id,a.service_booking_no,a.receive_basis,a.booking_no,a.receive_date,b.po_breakdown_id as po_id,c.febric_description_id as deter_id,c.body_part_id,c.prod_id,c.kniting_charge,(CASE WHEN b.entry_form =2 and b.trans_type in(1) and a.item_category=13  THEN  b.quantity ELSE 0 END) AS grey_qnty,d.rate,d.amount,d.id as dtls_id from inv_receive_master a, order_wise_pro_details b,pro_grey_prod_entry_dtls c,subcon_outbound_bill_dtls d, wo_po_break_down e where a.id=c.mst_id and c.id=b.dtls_id  and b.po_breakdown_id=d.order_id and c.body_part_id=d.body_part_id and c.prod_id=d.item_id and e.id=b.po_breakdown_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2) and a.item_category in(2,13)  and b.trans_type in(1) and e.job_id=$job_id";
				$result_grey=sql_select( $sql_grey_trans );
				 $usd_id=2;
                foreach($result_grey as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";					
					$receive_basis=$row[csf('receive_basis')];
					$po_id=$row[csf('po_id')];
					$currency_id=$row[csf('currency_id')];
					$company_id=$knit_subcon_array[$row[csf('dtls_id')]]['company_id'];
					$bill_date=$knit_subcon_array[$row[csf('dtls_id')]]['bill_date'];

					
				$process_costing=return_field_value("process_costing_maintain", "variable_settings_production", "company_name=$company_id and variable_list=34 and is_deleted=0 and status_active=1");
				$conversion_date=change_date_format($bill_date, "d-M-y", "-",1);
				$exchange_rate=set_conversion_rate($usd_id,$conversion_date,$company_id );
						
					if($process_costing==1)//Yes//Pre Costing
					{
						$knit_cost_mkt=array_sum($conversion_costing_arr_process_arr[$po_id][1]);
						$knit_qty_mkt=array_sum($conversion_costing_arr_process_qty[$po_id][1]);
						$knit_charge=$knit_cost_mkt/$knit_qty_mkt;
					}
					else //Knitting Prod.
					{
						if($currency_id==1) //TK
						{
							$knit_charge=$row[csf('kniting_charge')]/$exchange_rate;
							
						}
						else
						{
							$knit_charge=$row[csf('kniting_charge')];
						}
					}
					if($currency_id==1) //TK
					{
						$amount=$row[csf('amount')]/$exchange_rate;
						
					}
					else
					{
						$amount=$row[csf('amount')];
					}
					if($receive_basis==1) //Booking Basis
					{
							$booking_no=$row[csf('booking_no')];
						$booking_type=$booking_array[$booking_no]['btype'];
						$is_short=$booking_array[$booking_no]['is_short'];
					}
					else if($receive_basis==2) //Knit plan Basis
					{
						$booking_no=$plan_details_array[$row[csf('booking_no')]];
						$booking_type=$booking_array[$booking_no]['btype'];
						$is_short=$booking_array[$booking_no]['is_short'];
					}
					else if($receive_basis==4) //Salse Basis
					{
							$prog_no=$row[csf('booking_no')];
						$booking_no=$plan_details_array[$prog_no];
						$booking_type=$booking_array[$booking_no]['btype'];
						$is_short=$booking_array[$booking_no]['is_short'];
					}
						//echo $booking_type.'='.$is_short;
					if(($booking_type==1 || $booking_type==4) &&  $is_short==2 &&  $booking_type!='') //Main and Sample Faric Booking
					{
						$grey_amt=$row[csf('grey_qnty')]*$knit_charge;
					}
              		 if(($booking_type==1 || $booking_type==4) &&  $is_short==2 &&  $booking_type!='') //Main and Sample Faric Booking
					 {
					 	$show_comment=1;$path="../../../../";$action_type="show_trim_booking_report3";
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
						<td width="110"><p><span style="cursor: pointer; text-decoration: underline; color: blue;" title="Print Booking"
							onClick="generate_work_order_report('<?php echo $row[csf('service_booking_no')];?>',<?php echo $row[csf('company_id')];?>,<?php echo $show_comment;?>,'<?php echo $path;?>','<?php echo $action_type;?>')"><?php echo $row[csf('service_booking_no')];?></span></p></td>
						
                        <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td width="180"><p><? echo $product_array[$row[csf('prod_id')]]; ?></p></td>
                        <td align="right" width="110"> <? echo fn_number_format($row[csf('grey_qnty')],2); ?></td>
                        <td align="right" width="80"><? echo fn_number_format($amount/$row[csf('grey_qnty')],2); ?>&nbsp;</td>
                        <td align="right"><? echo fn_number_format($amount,2);?></td>
                    </tr>
                <?
                	$i++;
					$total_in_knit_qnty+=$row[csf('grey_qnty')];
					$total_in_knit_cost_usd+=$amount;
						}
                }
                ?>
                <tr bgcolor="#CCCCCC">
                    <td>&nbsp;</td>
					 <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><b>Total</b></td>
                    <td align="right"><? echo fn_number_format($total_in_knit_qnty,2); ?></td>
                    <td align="right"><? echo fn_number_format($total_in_knit_cost_usd/$total_in_knit_qnty,2); ?>&nbsp;</td>
                    <td align="right"><? echo fn_number_format($total_in_knit_cost_usd,2); ?></td>
                </tr>
				</tbody>
                
              
			</table>
        </div>	
	<script>
  	setFilterGrid("table_search",-1);
  </script>
	</fieldset>  
<?
exit();
}
if($action=="knit_cost_actual_popup"){
	echo load_html_head_contents("Knitting Cost Actual Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);
	$budget_data=sql_select("SELECT exchange_rate from wo_pre_cost_mst where job_id=$job_id and status_active=1 and is_deleted=0");
	foreach($budget_data as $row){
		$budget_exchange_rate=$row[csf('exchange_rate')];
	}
	$knit_cost_sql=sql_select("SELECT b.currency_id,a.bill_no,b.order_id, a.bill_date, b.item_id, b.rate, b.receive_qty, b.amount as amount, d.job_id, b.febric_description_id, d.id as po_id from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b, wo_po_break_down d where a.id=b.mst_id and b.order_id=d.id and d.job_id=$job_id and a.process_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by a.bill_no");
	//echo $knit_cost_sql; die;
	foreach($knit_cost_sql as $row){
		$fabric_description_arr[$row[csf('febric_description_id')]]=$row[csf('febric_description_id')];
		$order_ids_inhouse[$row[csf('order_id')]]=$row[csf('order_id')];
	}
	$fabric_cond=where_con_using_array($fabric_description_arr,0,"a.id");
	$composition_arr = array();
	$constructtion_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id $fabric_cond";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
		$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	}

	$sql_order=sql_select("SELECT a.id, a.company_id, a.recv_number,a.booking_id,a.booking_no, a.receive_basis, a.receive_date, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.febric_description_id,b.trans_id, b.gsm,b.room,b.rack,b.self,b.bin_box,c.qnty,c.rate, c.qc_pass_qnty,c.roll_no,b.width,b.body_part_id,b.yarn_lot,b.brand_id,b.shift_name,b.floor_id,b.machine_no_id,b.yarn_count,b.color_id,b.color_range_id, c.barcode_no, c.id as roll_id, c.po_breakdown_id, c.booking_without_order, c.is_sales, c.qc_pass_qnty_pcs,b.stitch_length FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id ".where_con_using_array($order_ids_inhouse,0,'c.po_breakdown_id') ."  and c.status_active=1 and c.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
    foreach ($sql_order as $data) 
    {
    	$order_wise_data[$data[csf('po_breakdown_id')]]['gsm_width']=$data[csf('gsm')].','.$data[csf('width')];
    }
	?>
	<fieldset style="width:770px; margin-left:7px">
        <table class="rpt_table" width="750" cellpadding="0" cellspacing="0" border="1" rules="all">
			<caption style="background:#CCDDEE"><b>Knitting Cost Actaul</b></caption>
			<thead>
				<th width="40">SL</th>
				<th width="110">Bill No</th> 
				<th width="110">Bill Date</th>
				<th width="250">Fabric Description</th>
				<th width="80">Bill Qty</th>
				<th width="80">Rate</th>
				<th>Amount (USD)</th>
			</thead>        
			<tbody>
				<? 
				$i=1;
				foreach($knit_cost_sql as $row){
					$description= $constructtion_arr[$row[csf('febric_description_id')]].','.$composition_arr[$row[csf('febric_description_id')]].','.$order_wise_data[$row[csf('po_id')]]['gsm_width'];
					if($row[csf('currency_id')]==2){
						
						$amount=$row[csf('amount')];
						$rate=$row[csf('rate')];
					}
					else{
						$amount=$row[csf('amount')]/$budget_exchange_rate;
						$rate=$row[csf('rate')]/$budget_exchange_rate;
					}
					
					?>
					<tr>
						<td><?= $i; ?></td>
						<td><?= $row[csf('bill_no')]; ?></td>
						<td><?= change_date_format($row[csf('bill_date')]); ?></td>
						<td><?= $description; ?></td>
						<td align="right"><?= $row[csf('receive_qty')]; ?></td>
						<td align="right"><?= fn_number_format($rate,4); ?></td>
						<td align="right"><?= fn_number_format($amount,2); ?></td>
					</tr>
				<? 
					$i++;
					$total_qty+=$row[csf('receive_qty')];
					$total_amount+=$amount;
					} 
				?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="4" align="right"><b>Total</b></td>
					<td align="right"><b><?= $total_qty ?></b></td>
					<td align="right"></td>
					<td align="right"><b><?= fn_number_format($total_amount,2); ?></b></td>
				</tr>
			</tfoot>
		</table>
	</fieldset>
	<?
	exit();
}

if($action=="dye_finish_cost_actual")
{
	echo load_html_head_contents("Dyeing Bill Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

	$product_array=return_library_array( "select id, product_name_details from product_details_master where item_category_id=2", "id", "product_name_details");
	$batch_no_array=return_library_array( "select id, batch_no from pro_batch_create_mst where status_active=1", "id", "batch_no");
	?>
	<script>
	function print_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		$("#list_view_data tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		//document.getElementById('scroll_body').style.overflowY="scroll";
		$("#list_view_data tr:first").show();
	}	
    </script>
	<fieldset style="width:870px; margin-left:7px">
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="report_container">
        <table class="rpt_table" width="850" cellpadding="0" cellspacing="0" border="1" rules="all" >
        <caption style="background:#CCDDEE"> <b>Dye and Finish Details</b></caption>
            <thead>
                <th width="40">SL</th>
                <th width="130">System No.</th>
                <th width="80">Recv. Date</th>
                <th width="100">Barcode/ Batch No</th>
                <th width="200">Fabric Description</th>
                <th width="100">Qty</th>
                <th width="80">Rate</th>
                <th>Amount</th>
            </thead>
        </table>
        <div style="width:850px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="830" cellpadding="0" cellspacing="0" id="list_view_data">  
            	 
				<?
                $i=1; $avg_rate=76;
				$total_in_knit_qnty=0; $total_in_knit_cost_tk=0; $total_in_knit_cost_usd=0;
				$total_out_knit_qnty=0; $total_out_knit_cost_tk=0; $total_out_knit_cost_usd=0;
				$total_knit_qnty=0; $total_knit_cost_tk=0; $total_knit_cost_usd=0;
				
				 $prodcostDataArray="select a.body_part_id,a.lib_yarn_count_deter_id as deter_id,a.body_part_id,b.po_break_down_id as po_id,
									  avg(CASE WHEN c.cons_process=1 THEN c.charge_unit END) AS knit_charge,
									  avg(CASE WHEN c.cons_process=30 THEN c.charge_unit END) AS yarn_dye_charge,
									  avg(CASE WHEN c.cons_process=35 THEN c.charge_unit END) AS aop_charge,
									  avg(CASE WHEN c.cons_process not in(1,2,30,35,134) THEN c.charge_unit END) AS dye_finish_charge
									  from wo_pre_cost_fabric_cost_dtls a,wo_pre_cost_fab_conv_cost_dtls c,wo_pre_cos_fab_co_avg_con_dtls b where a.id=c.fabric_description and a.job_no=c.job_no and b.pre_cost_fabric_cost_dtls_id=a.id and b.job_no=c.job_no and c.status_active=1 and c.is_deleted=0  and b.po_break_down_id in($po_id) group by  a.lib_yarn_count_deter_id,b.po_break_down_id,a.body_part_id";
				$resultfab_arr = sql_select($prodcostDataArray);
				foreach($resultfab_arr as $prodRow)
				{
					//echo $prodRow[csf('knit_charge')].',';
					$prodcostArray[$prodRow[csf('po_id')]][$prodRow[csf('deter_id')]][$prodRow[csf('body_part_id')]]['knit_charge']=$prodRow[csf('knit_charge')];
					$prodcostArray[$prodRow[csf('po_id')]][$prodRow[csf('deter_id')]][$prodRow[csf('body_part_id')]]['yarn_dye_charge']=$prodRow[csf('yarn_dye_charge')];
					$prodcostArray[$prodRow[csf('po_id')]][$prodRow[csf('deter_id')]][$prodRow[csf('body_part_id')]]['aop_charge']=$prodRow[csf('aop_charge')];
					$prodcostArray2[$prodRow[csf('po_id')]][$prodRow[csf('deter_id')]][$prodRow[csf('body_part_id')]]['dye_finish_charge']=$prodRow[csf('dye_finish_charge')];
				}	
				
				
				
				$grey_used_sql = "select a.body_part_id,a.fabric_description_id as deter_id,a.prod_id,b.barcode_no,b.qnty,b.po_breakdown_id as po_id,c.recv_number,c.receive_date,c.receive_purpose from pro_finish_fabric_rcv_dtls a, pro_roll_details b, inv_receive_master c where a.id=b.dtls_id and c.id=b.mst_id and b.entry_form=66 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id in($po_id)";
				$grey_used_data =sql_select($grey_used_sql );
                foreach($grey_used_data as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                
				$dye_finish_charge=$prodcostArray2[$row[csf('po_id')]][$row[csf('deter_id')]][$row[csf('body_part_id')]]['dye_finish_charge'];
				$used_amount=$row[csf('qnty')]*$dye_finish_charge;
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="130"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                         <td width="100"><p><? echo $row[csf('barcode_no')]; ?></p></td>
                        <td width="200"><p><? echo $product_array[$row[csf('prod_id')]]; ?></p></td>
                        <td align="right" width="100"><? echo fn_number_format($row[csf('qnty')],2); ?></td>
                        <td align="right" width="80"><? echo fn_number_format($dye_finish_charge,2); ?>&nbsp;</td>
                        <td align="right" width="">
						<? 
							echo fn_number_format($used_amount,2); 
						?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_in_knit_qnty+=$row[csf('qnty')];
					
					$total_in_knit_cost_usd+=$used_amount;
                }
				
				$finProd_grey_used_sql = "select c.prod_id,c.batch_id,c.body_part_id,c.fabric_description_id as deter_id,d.recv_number,d.receive_date,d.receive_purpose,b.po_breakdown_id as po_id, b.quantity as qnty from order_wise_pro_details b,pro_finish_fabric_rcv_dtls c,inv_receive_master d where  b.dtls_id=c.id and  d.id=c.mst_id and b.entry_form=7 and d.entry_form=7 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id in($po_id) ";
				$fingrey_used_data =sql_select($finProd_grey_used_sql );
				foreach($fingrey_used_data as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                
				$dye_finish_charge=$prodcostArray2[$row[csf('po_id')]][$row[csf('deter_id')]][$row[csf('body_part_id')]]['dye_finish_charge'];
				$used_amount=$row[csf('qnty')]*$dye_finish_charge;
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="130"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                         <td width="100"><p><? echo $batch_no_array[$row[csf('batch_id')]]; ?></p></td>
                        <td width="200"><p><? echo $product_array[$row[csf('prod_id')]]; ?></p></td>
                        <td align="right" width="100"><? echo fn_number_format($row[csf('qnty')],2); ?></td>
                        <td align="right" width="80"><? echo fn_number_format($dye_finish_charge,2); ?>&nbsp;</td>
                        <td align="right" width="">
						<? 
							echo fn_number_format($used_amount,2); 
						?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_in_knit_qnty+=$row[csf('qnty')];
					
					$total_in_knit_cost_usd+=$used_amount;
                }
                ?>
                <tr bgcolor="#CCCCCC">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><b>Total</b></td>
                    <td align="right"><? echo fn_number_format($total_in_knit_qnty,2); ?></td>
                    <td align="right"><? echo fn_number_format($total_in_knit_qnty/$total_in_knit_cost_usd,2); ?>&nbsp;</td>
                    <td  align="right"> <? echo fn_number_format($total_in_knit_cost_usd,2); ?>&nbsp;</td>
                </tr>
			</table>
        </div>	
        </div>
	</fieldset> 
    <script>
    	setFilterGrid("list_view_data",-1); 
	</script>
<?
exit();
}

if($action=="dye_finish_cost_actual_popup_bk")
{
	echo load_html_head_contents("Dyeing Bill Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

	$product_array=return_library_array( "select id, product_name_details from product_details_master where item_category_id=2", "id", "product_name_details");
	$batch_no_array=return_library_array( "select id, batch_no from pro_batch_create_mst where status_active=1", "id", "batch_no");
	?>
	<script>
	function print_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		$("#list_view_data tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		//document.getElementById('scroll_body').style.overflowY="scroll";
		$("#list_view_data tr:first").show();
	}	
    </script>
	<fieldset style="width:770px; margin-left:7px">
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="report_container">
        <table class="rpt_table" width="750" cellpadding="0" cellspacing="0" border="1" rules="all" >
        <caption style="background:#CCDDEE"> <b>Dye and Finish Details</b></caption>
            <thead>
                <th width="40">SL</th>
                <th width="130">System No.</th>
                <th width="80">Recv. Date</th>
                <th width="200">Fabric Description</th>
                <th width="100">Qty</th>
                <th width="80">Rate</th>
                <th>Amount</th>
            </thead>
        </table>
        <div style="width:750px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0" id="list_view_data">  
            	 
				<?
				$usd_id=2;
                $i=1; $avg_rate=76;
				$total_in_knit_qnty=0; $total_in_knit_cost_tk=0; $total_in_knit_cost_usd=0;
				$total_out_knit_qnty=0; $total_out_knit_cost_tk=0; $total_out_knit_cost_usd=0;
				$total_knit_qnty=0; $total_knit_cost_tk=0; $total_knit_cost_usd=0;
				$sql_dye=sql_select("select d.company_id,c.currency_id,d.bill_date from subcon_inbound_bill_mst d,subcon_inbound_bill_dtls c, wo_po_break_down d where d.id=c.mst_id and d.id=c.order_id and c.status_active=1 and d.job_id=$job_id ");
				 
				foreach($sql_dye as $row)
                {
					$currency_id=$row[csf('currency_id')];
					$bill_date=$row[csf('bill_date')];
					$company_id=$row[csf('company_id')];
				}

				 $grey_sql="select a.body_part_id, a.fabric_description_id as deter_id, a.prod_id, d.delivery_qty, d.currency_id, b.discount, c.recv_number, c.receive_date, d.rate, e.item_description,d.order_id from pro_finish_fabric_rcv_dtls  a, subcon_inbound_bill_mst b, inv_receive_master c, subcon_inbound_bill_dtls d, pro_batch_create_dtls e, wo_po_break_down f where  c.id=d.delivery_id and a.fabric_description_id=d.febric_description_id and d.item_id=a.prod_id and d.order_id=e.po_id and b.id=d.mst_id and f.id=d.order_id and f.job_id=$job_id group by a.body_part_id, a.fabric_description_id,d.currency_id, a.prod_id, c.recv_number, c.receive_date, d.delivery_qty, d.rate, b.discount, e.item_description, d.order_id";
				 //echo $grey_sql; die;
				$grey_used_data =sql_select($grey_sql);
                foreach($grey_used_data as $row)
                {
					$currency_id=$row[csf('currency_id')];
					$conversion_date=change_date_format($bill_date, "d-M-y", "-",1);
					$currency_rate=set_conversion_rate($usd_id,$conversion_date,$company_id );
					if($currency_id==2) //Usd
					{
						if($row[csf('discount')]>0){
							$usd_amount=$row[csf('delivery_qty')]*$row[csf('rate')]-$row[csf('discount')];
						}else{
							$usd_amount=$row[csf('delivery_qty')]*$row[csf('rate')];
						}
					}
					else{
						if($row[csf('discount')]>0){
							$usd_amount=($row[csf('delivery_qty')]*$row[csf('rate')]-$row[csf('discount')])/$currency_rate;
						}else{
							$usd_amount=($row[csf('delivery_qty')]*$row[csf('rate')])/$currency_rate;
						}
					}


					
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                
				$dye_finish_charge=$prodcostArray2[$row[csf('po_id')]][$row[csf('deter_id')]][$row[csf('body_part_id')]]['dye_finish_charge'];
				$used_amount=$row[csf('delivery_qty')]*$row[csf('rate')];
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="130"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td width="200"><p><? echo $row[csf('item_description')]; ?></p></td>
                        <td align="right" width="100"><? echo fn_number_format($row[csf('delivery_qty')],2); ?></td>
                        <td align="right" width="80"><? echo fn_number_format($usd_amount/$row[csf('delivery_qty')],2); ?>&nbsp;</td>
                        <td align="right" width="">
						<? 
							echo fn_number_format($usd_amount,2); 
						?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_in_knit_qnty+=$row[csf('delivery_qty')];
					
					$total_in_knit_cost_usd+=$usd_amount;
                }
				?>
                <tr bgcolor="#CCCCCC">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><b>Total</b></td>
                    <td align="right"><? echo fn_number_format($total_in_knit_qnty,2); ?></td>
                    <td align="right"><? //echo fn_number_format($total_in_knit_qnty/$total_in_knit_cost_usd,2); ?>&nbsp;</td>
                    <td  align="right"> <? echo fn_number_format($total_in_knit_cost_usd,2); ?>&nbsp;</td>
                </tr>
			</table>
        </div>	
        </div>
	</fieldset> 
    <script>
    	setFilterGrid("list_view_data",-1); 
	</script>
<?
exit();
}
if($action=="dye_finish_cost_actual_popup"){
	echo load_html_head_contents("Dyeing Bill Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0","id","color_name");
	?>
	<script>
	function print_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//$("#list_view_data tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+ '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');	
		d.close();
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//$("#list_view_data tr:first").show();
	}	
    </script>
	<?
	$budget_data=sql_select("SELECT exchange_rate from wo_pre_cost_mst where job_id=$job_id and status_active=1 and is_deleted=0");
	foreach($budget_data as $row){
		$budget_exchange_rate=$row[csf('exchange_rate')];
	}
	$inbound_dye_cost_sql=sql_select("SELECT b.currency_id,a.bill_no,  b.rate, a.bill_date,a.discount,b.order_id, b.delivery_qty as receive_qty, b.amount, d.job_id, b.febric_description_id, b.color_id  from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b, wo_po_break_down d where a.id=b.mst_id and b.order_id=d.id and d.job_id=$job_id and a.process_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.bill_no ASC");
	$outbound_dye_cost_sql=sql_select("SELECT b.currency_id,a.bill_no,b.order_id, a.bill_date, b.item_id, b.rate, b.receive_qty, b.amount as amount, d.job_id, b.febric_description_id, d.id as po_id, b.color_id from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b, wo_po_break_down d where a.id=b.mst_id and b.order_id=d.id and d.job_id=$job_id and a.process_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by a.bill_no asc");
	?>
	<fieldset style="width:670px; margin-left:7px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div id="report_container">
			<table class="rpt_table" width="650" cellpadding="0" cellspacing="0" border="1" rules="all" id="list_view_data">
				<caption style="background:#CCDDEE"><b>Dye and Finish Details</b></caption>
				<thead>
					<th width="40">SL</th>
					<th width="110">Bill No</th> 
					<th width="110">Bill Date</th>
					<th width="150">Fabric Color</th>
					<th width="80">Bill Qty</th>
					<th width="80">Rate</th>
					<th>Amount (USD)</th>
				</thead>        
				<tbody>
					<? 
					$i=1;
					foreach($outbound_dye_cost_sql as $row){
						$description= $constructtion_arr[$row[csf('febric_description_id')]].','.$composition_arr[$row[csf('febric_description_id')]].','.$order_wise_data[$row[csf('po_id')]]['gsm_width'];
						if($row[csf('currency_id')]==2){
							
							$amount=$row[csf('amount')];
							$rate=$row[csf('rate')];
						}
						else{
							$amount=$row[csf('amount')]/$budget_exchange_rate;
							$rate=$row[csf('rate')]/$budget_exchange_rate;
						}
						
						?>
						<tr>
							<td><?= $i; ?></td>
							<td><?= $row[csf('bill_no')]; ?></td>
							<td><?= change_date_format($row[csf('bill_date')]); ?></td>
							<td><?= $color_arr[$row[csf('color_id')]]; ?></td>
							<td align="right"><?= $row[csf('receive_qty')]; ?></td>
							<td align="right"><?= fn_number_format($rate,4); ?></td>
							<td align="right"><?= fn_number_format($amount,2); ?></td>
						</tr>
						<? 
						$i++;
						$total_qty+=$row[csf('receive_qty')];
						$total_amount+=$amount;
					}
					foreach($inbound_dye_cost_sql as $row){
						$description= $constructtion_arr[$row[csf('febric_description_id')]].','.$composition_arr[$row[csf('febric_description_id')]].','.$order_wise_data[$row[csf('po_id')]]['gsm_width'];
						if($row[csf('currency_id')]==2){
							
							$amount=$row[csf('amount')];
							$rate=$row[csf('rate')];
						}
						else{
							$amount=$row[csf('amount')]/$budget_exchange_rate;
							$rate=$row[csf('rate')]/$budget_exchange_rate;
						}
						
						?>
						<tr>
							<td><?= $i; ?></td>
							<td><?= $row[csf('bill_no')]; ?></td>
							<td><?= change_date_format($row[csf('bill_date')]); ?></td>
							<td><?= $color_arr[$row[csf('color_id')]]; ?></td>
							<td align="right"><?= $row[csf('receive_qty')]; ?></td>
							<td align="right"><?= fn_number_format($rate,4); ?></td>
							<td align="right"><?= fn_number_format($amount,2); ?></td>
						</tr>
						<? 
						$i++;
						$total_qty+=$row[csf('receive_qty')];
						$total_amount+=$amount;
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="4" align="right"><b>Total</b></td>
						<td align="right"><b><?= $total_qty ?></b></td>
						<td align="right"></td>
						<td align="right"><b><?= $total_amount ?></b></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}

if($action=="fabric_purchase_cost")
{
	echo load_html_head_contents("Fabric Purchase Cost Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	
	//$exchange_rate=76;
	$sql_pre=sql_select("select  exchange_rate from wo_pre_cost_mst a,wo_po_break_down b  where b.job_id=a.job_id and b.id in($po_id)");
	foreach($sql_pre as $row)
	{
		$exchange_rate=$row[csf('exchange_rate')];
	}
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$sql_fin_purchase="select b.po_breakdown_id, sum(b.quantity) as qty, sum(a.cons_rate*b.quantity) as finish_purchase_amnt from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.receive_basis<>9 and a.item_category=2 and a.transaction_type=1 and b.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id in($po_id) group by b.po_breakdown_id";
	$dataArrayFinPurchase=sql_select($sql_fin_purchase);
	foreach($dataArrayFinPurchase as $finRow)
	{
		$finish_purchase_arr[$finRow[csf('po_breakdown_id')]]['qty']=$finRow[csf('qty')];
		$finish_purchase_arr[$finRow[csf('po_breakdown_id')]]['amnt']=$finRow[csf('finish_purchase_amnt')]/$exchange_rate;
	}
	
	$sql_fin_purchase="select b.po_breakdown_id, sum(b.quantity) as qty, sum(a.cons_rate*b.quantity) as woven_purchase_amnt from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category=3 and a.transaction_type=1 and b.entry_form=17 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id in($po_id) group by b.po_breakdown_id";
	$dataArrayFinPurchase=sql_select($sql_fin_purchase);
	foreach($dataArrayFinPurchase as $finRow)
	{
		$finish_purchase_arr[$finRow[csf('po_breakdown_id')]]['qty']+=$finRow[csf('qty')];
		$finish_purchase_arr[$finRow[csf('po_breakdown_id')]]['amnt']+=$finRow[csf('woven_purchase_amnt')]/$exchange_rate;
	}
	?>
    <div>
        <fieldset style="width:710px; margin-left:8px">
        	<table class="rpt_table" width="680" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="40">SL</th>
                    <th width="60">Buyer</th>
                    <th width="100">PO No</th>
                    <th width="50">Year</th>
                    <th width="60">Job No</th>
                    <th width="110">Order Qty.</th>
                    <th width="110">Received</th>
                    <th>Cost ($)</th>
                </thead>
            </table>	
            <div style="width:700px; max-height:310px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="680" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
					else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
					else $year_field="";//defined Later
						

					$sql="select a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id, b.po_number, b.pub_shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($po_id) order by b.pub_shipment_date, a.job_no_prefix_num, b.id";
					$result=sql_select($sql);
					$i=1; $tot_po_qnty=0; $tot_recv_qty=0; $tot_cost=0;
					foreach($result as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
						$tot_po_qnty+=$order_qnty_in_pcs;
						
						$recv_qty=$finish_purchase_arr[$row[csf('id')]]['qty'];
						$recv_cost=$finish_purchase_arr[$row[csf('id')]]['amnt'];
						
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="60" style="word-break:break-all;"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                            <td width="100" style="word-break:break-all;"><? echo $row[csf('po_number')]; ?></td>
                            <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                            <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                            <td width="110" align="right"><? echo $order_qnty_in_pcs; ?></td>
							<td width="110" align="right"><? echo fn_number_format($recv_qty,2,'.',''); ?></td>
                            <td align="right" ><? echo fn_number_format($recv_cost,2,'.',''); ?></td>
						</tr>
					<?
						$i++;
						$tot_recv_qty+=$recv_qty; 
						$tot_cost+=$recv_cost; 
					}
					?>
                	<tfoot>
                        <th colspan="5">Total</th>
                        <th><? echo $tot_po_qnty; ?></th>
                        <th><? echo fn_number_format($tot_recv_qty,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_cost,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
<?
	exit();
}

if($action=="aop_cost_actual")
{
	echo load_html_head_contents("AOP Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

	?>
	<fieldset style="width:760px; margin-left:7px">
        <table class="rpt_table" width="755" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="110">WO NO.</th>
                <th width="80">WO Date</th>
                <th width="80">Currency</th>
                <th width="120">Amount (Taka)</th>
                <th width="120">Conversion rate</th>
                <th>Amount (USD)</th>
            </thead>
        </table>
        <div style="width:755px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="735" cellpadding="0" cellspacing="0">    
				<?
                $i=1; $total_aop_cost=0; $avg_rate=76;
                $sql="select a.booking_no, a.booking_date, a.currency_id, a.exchange_rate, sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id=$po_id and a.item_category=12 and a.booking_type in(3,6) and b.process=35 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no, a.booking_date, a.currency_id, a.exchange_rate";
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('booking_no')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                        <td width="80"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
                        <td align="right" width="120">
                            <? 
								$amnt_tk=$row[csf('amount')]*$row[csf('exchange_rate')];
                                echo fn_number_format($amnt_tk,2); 
                            ?>
                        </td>
                        <td align="right" width="120"><? echo fn_number_format($row[csf('exchange_rate')],2); ?>&nbsp;</td>
                        <td align="right">
                            <?
								if($row[csf('currency_id')]==1)
								{
                                	$amount=$row[csf('amount')]/$row[csf('exchange_rate')];
								}
								else
								{
									$amount=$row[csf('amount')];
								}
                                echo fn_number_format($amount,2); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_aop_cost+=$amount;
                }
                ?>
                <tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Total</th>
                    <th align="right"><? echo fn_number_format($total_aop_cost,2); ?></th>
                </tfoot>
			</table>
        </div>	
	</fieldset>  
<?
exit();
}

if($action=="trims_cost_actual")
{
	echo load_html_head_contents("AOP Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

	?>
	<fieldset style="width:760px; margin-left:7px">
        <table class="rpt_table" width="755" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="110">WO NO.</th>
                <th width="80">WO Date</th>
                <th width="80">Currency</th>
                <th width="120">Amount (Taka)</th>
                <th width="120">Conversion rate</th>
                <th>Amount (USD)</th>
            </thead>
        </table>
        <div style="width:755px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="735" cellpadding="0" cellspacing="0">    
				<?
                $i=1; $total_trims_cost=0; $avg_rate=76;
				$budget_data=sql_select("SELECT exchange_rate from wo_pre_cost_mst where job_id=$job_id and status_active=1 and is_deleted=0");
				foreach($budget_data as $row){
					$budget_exchange_rate=$row[csf('exchange_rate')];
				}

                $wo_sql="select a.booking_no, a.booking_date, a.currency_id, a.exchange_rate, sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and c.job_id=$job_id and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no, a.booking_date, a.currency_id, a.exchange_rate";//c.booking_no,a.transaction_date,c.exchange_rate,c.currency_id
				
				$result_wo=sql_select( $wo_sql );
				 foreach($result_wo as $row)
                {
					$wo_date_arr[$row[csf('booking_no')]]=$row[csf('booking_date')];
				}
				 $sql_gen_trims_inv="select a.booking_no,b.prod_id,b.transaction_date,(b.cons_amount) as amount,b.order_id as po_id
				  from inv_issue_master a, inv_transaction b, wo_po_break_down c where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(21)   and b.transaction_type in(2) and c.id=b.order_id and c.job_id=$job_id";
				  $result_trims_gen=sql_select( $sql_gen_trims_inv );
				  
			    $sql_trims_inv="select c.booking_no,c.exchange_rate, a.transaction_date,a.prod_id, c.exchange_rate, c.currency_id, a.transaction_date, a.cons_amount as amount, b.po_breakdown_id as po_id, (b.quantity) as qnty,b.order_amount as cons_amount
				  from inv_receive_master c,inv_transaction a, order_wise_pro_details b, wo_po_break_down d where  c.id=a.mst_id and a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.entry_form in(24) and a.item_category=4 and a.transaction_type in(1) and b.trans_type in(1) and d.id=b.po_breakdown_id and d.job_id=$job_id";
				  //echo $sql_trims_inv; die;
				$result_trims=sql_select( $sql_trims_inv );
              	$usd_id=2;

				$trim_group=return_library_array( "select item_name,id from  lib_item_group ", "id", "item_name" );
				$store_name_arr = return_library_array("select a.id, a.store_name from lib_store_location a", 'id', 'store_name');

				$store_to_store_sql=sql_select("SELECT b.po_breakdown_id, a.item_color as item_color_id, a.item_size, a.item_group_id, a.item_description, b.quantity, c.cons_rate as rate, b.entry_form, b.trans_type, c.store_id, d.transfer_criteria from product_details_master a, order_wise_pro_details b, inv_transaction c, inv_item_transfer_mst d, wo_po_break_down e where a.id=b.prod_id and b.trans_id=c.id and d.id=c.mst_id and item_category_id=4 and a.entry_form=24 and b.entry_form in(112) and b.trans_type in(5,6) and c.transaction_type in(5,6) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.transfer_criteria=2 and b.po_breakdown_id =e.id and e.job_id=$job_id");
				$transfer_criteria_arr=array();
				foreach($store_to_store_sql as $rows)
				{
					$item_description=trim($row[csf("item_description")]);
					$trim_str=", [BS]";
					$item_desc=chop_last_string($item_description,$trim_str);
					if($rows[csf('item_size')]=="") $item_sizeId=0; else $item_sizeId=$rows[csf('item_size')];
					if($rows[csf('trans_type')]==5 || $rows[csf('trans_type')]==6)
					{
						$transfer_criteria_arr[$rows[csf('po_breakdown_id')]][$rows[csf('item_group_id')]][$item_desc][$rows[csf('item_color_id')]][$item_sizeId]["transfer_criteria"]=$rows[csf('transfer_criteria')];
					}
				}


				$trans_qty_sql=sql_select("SELECT b.po_breakdown_id, a.item_color as item_color_id, a.item_size, a.item_group_id, a.item_description, b.quantity, c.cons_rate as rate, b.entry_form, b.trans_type, c.store_id, d.job_no_mst as job_no from product_details_master a, order_wise_pro_details b, inv_transaction c ,wo_po_break_down d where a.id=b.prod_id and b.trans_id=c.id and d.id=b.po_breakdown_id and d.id=c.order_id and item_category_id=4 and b.entry_form in(78,112) and b.trans_type in(5,6) and c.transaction_type in(5,6) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.job_id=$job_id");
				$trimsout_transfer_data_array=array(); $transin_transfer_data_array=array();
				foreach($trans_qty_sql as $row)
				{
					if($row[csf('item_size')]=="") $item_size_id=0; else $item_size_id=$row[csf('item_size')];

					$item_description=trim($row[csf("item_description")]);
					$trim_str=", [BS]";
					$item_desc=chop_last_string($item_description,$trim_str);		
					if(($row[csf('entry_form')]==78 || $row[csf('entry_form')]==112) && $row[csf('trans_type')]==5)
					{
						if ($transfer_criteria_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$item_desc][$row[csf('item_color_id')]][$item_size_id]["transfer_criteria"] != 2)
						{
							$key=$row[csf('store_id')].'*'.$row[csf('item_group_id')].'*'.$item_desc;
							$amount=$row[csf('quantity')]*$row[csf('rate')];
							$transin_transfer_data_array[$key]['amount']+=$amount/$budget_exchange_rate;				
							$transin_transfer_data_array[$key]['qty']+=$row[csf('quantity')];				
							$transin_transfer_data_array[$key]['store_id']=$store_name_arr[$row[csf('store_id')]];				
							$transin_transfer_data_array[$key]['item_group_id']=$trim_group[$row[csf('item_group_id')]];				
							$transin_transfer_data_array[$key]['item_desc']=$item_desc;				
						}
						
					}
					if(($row[csf('entry_form')]==78 || $row[csf('entry_form')]==112) && $row[csf('trans_type')]==6)
					{
						if ($transfer_criteria_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$item_desc][$row[csf('item_color_id')]][$item_size_id]["transfer_criteria"] != 2)
						{
							
							$key=$row[csf('store_id')].'*'.$row[csf('item_group_id')].'*'.$item_desc;
							$amount=$row[csf('quantity')]*$row[csf('rate')];
							$trimsout_transfer_data_array[$key]['amount']+=$amount/$budget_exchange_rate;				
							$trimsout_transfer_data_array[$key]['qty']+=$row[csf('quantity')];				
							$trimsout_transfer_data_array[$key]['store_id']=$store_name_arr[$row[csf('store_id')]];				
							$trimsout_transfer_data_array[$key]['item_group_id']=$trim_group[$row[csf('item_group_id')]];				
							$trimsout_transfer_data_array[$key]['item_desc']=$item_desc;				
						}
					}
				}
				$trims_return_data=sql_select("SELECT e.booking_no, sum(d.quantity) as quantity, sum(d.order_amount) as amt from 
				inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d, inv_trims_issue_dtls e, 
				wo_po_break_down f where a.id=e.mst_id and a.id=b.mst_id and b.id=e.trans_id and b.prod_id=c.id and b.id=d.trans_id and 
				d.trans_type=3 and d.entry_form=49 and a.entry_form=49 and b.transaction_type=3 and c.item_category_id=4 and 
				a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.po_breakdown_id=f.id 
				and f.job_id=42945 and e.status_active=1 and e.is_deleted=0 group by e.booking_no");
				foreach($trims_return_data as $row){
					$trims_return_arr[$row[csf('booking_no')]]+=$row[csf('amt')];
				}

				

                foreach($result_trims as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					//$transaction_date=$row[csf('transaction_date')];
					$currency_id=$row[csf('currency_id')];
					$exchange_rate=$budget_exchange_rate;
					$amnt_tk=$row[csf('amount')];
					$amount=$row[csf('cons_amount')];
					if($currency_id==2){
						$trims_main_data[$row[csf('booking_no')]]['booking_no']=$row[csf('booking_no')];
						$trims_main_data[$row[csf('booking_no')]]['booking_date']=$wo_date_arr[$row[csf('booking_no')]];
						$trims_main_data[$row[csf('booking_no')]]['currency']=$row[csf('currency_id')];
						$trims_main_data[$row[csf('booking_no')]]['amount_tk']+=$amount*$exchange_rate;
						$trims_main_data[$row[csf('booking_no')]]['currency_rate']=$exchange_rate;
						$trims_main_data[$row[csf('booking_no')]]['amount']+=$amount;
					}
					else{
						$trims_main_data[$row[csf('booking_no')]]['booking_no']=$row[csf('booking_no')];
						$trims_main_data[$row[csf('booking_no')]]['booking_date']=$wo_date_arr[$row[csf('booking_no')]];
						$trims_main_data[$row[csf('booking_no')]]['currency']=$row[csf('currency_id')];
						$trims_main_data[$row[csf('booking_no')]]['amount_tk']+=$amount;
						$trims_main_data[$row[csf('booking_no')]]['currency_rate']=$exchange_rate;
						$trims_main_data[$row[csf('booking_no')]]['amount']+=$amount/$exchange_rate;
					}
					/* $exchange_rateArr[$row[csf('prod_id')]]=$row[csf('exchange_rate')];
					
					 */
					
                	
                }
				foreach($trims_main_data as $value){ ?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40" title="Receive"><? echo $i; ?></td>
                        <td width="110"><p><? echo $value['booking_no']; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($value['booking_date']); ?></td>
                        <td width="80"><p><?= $currency[$value['currency']];  ?></p></td>
                        <td align="right" width="120"><? echo fn_number_format($value['amount_tk'],2); ?></td>
                        <td align="right" width="120"><? echo fn_number_format($value['currency_rate'],2); ?>&nbsp;</td>
                        <td align="right" title="<?= 'Rcv Amt:'.$value['amount'].' Return Amt:'.$trims_return_arr[$value['booking_no']] ?>"><? echo fn_number_format($value['amount']-$trims_return_arr[$value['booking_no']],2); ?>
                        </td>
                    </tr>
					<?
					$i++;
					$total_trims_cost+=$value['amount']-$trims_return_arr[$value['booking_no']];
				}
				
				foreach($result_trims_gen as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					$transaction_date=$row[csf('transaction_date')];
					if($db_type==0)
					{
						$conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
					}
					else
					{
						$conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
					}
					//$currency_rate=set_conversion_rate($usd_id,$conversion_date );
					$currency_rate=$exchange_rateArr[$row[csf('prod_id')]];
					
					$amnt_tk=$row[csf('amount')];
					$amount=$row[csf('amount')]/$currency_rate;
					
					//echo $row[csf('amount')].'=='.$currency_rate.'<br>';
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40" title="Issue"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('booking_no')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($wo_date_arr[$row[csf('booking_no')]]); ?></td>
                        <td width="80"><p><? echo $currency[$usd_id]; ?></p></td>
                        <td align="right" width="120"><? echo fn_number_format($amnt_tk,2); ?></td>
                        <td align="right" width="120"><? echo fn_number_format($currency_rate,2); ?>&nbsp;</td>
                        <td align="right"><? echo fn_number_format($amount,2); ?></td>
                    </tr>
                <?
                	$i++;
					$total_trims_cost+=$amount;
                }
                ?>
                <tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Total</th>
                    <th align="right"><? echo fn_number_format($total_trims_cost,2); ?></th>
                </tfoot>
			</table>
			<table border="1" class="rpt_table" rules="all" width="735" cellpadding="0" cellspacing="0">
				<thead>
					<tr><td colspan="6" align="center"><strong>Transfer IN Details</strong></td></tr>
					<tr>
						<th>Sl</th>
						<th>Store</th>
						<th>Item Description</th>	
						<th>Item Group</th>		
						<th>Qty.</th>	
						<th>Amount (USD)</th>
					</tr>
				</thead>
				<? 
				$i=1;
				foreach($transin_transfer_data_array as $row){ ?>
					<tr>
						<td><?= $i ?></td>
						<td><?= $row['store_id'] ?></td>
						<td><?= $row['item_desc']; ?></td>
						<td><?= $row['item_group_id'] ?></td>
						<td align="right"><?= fn_number_format($row['qty'],2) ?></td>
						<td align="right"><?= fn_number_format($row['amount'],2) ?></td>
					</tr>
				<? 
					$total_trims_qty_in+=$row['qty'];
					$total_trims_in+=$row['amount'];
					$i++;
				}
				 ?>
				 <tfoot>
                    <th colspan="3">&nbsp;</th>
                    <th>Sub Total</th>
                    <th align="right"><? echo fn_number_format($total_trims_qty_in,2); ?></th>
                    <th align="right"><? echo fn_number_format($total_trims_in,2); ?></th>
                </tfoot>
			</table>
			<table border="1" class="rpt_table" rules="all" width="735" cellpadding="0" cellspacing="0">
				<thead>
					<tr><td colspan="6" align="center"><strong>Transfer Out Details</strong></td></tr>
					<tr>
						<th>Sl</th>
						<th>Store</th>
						<th>Item Description</th>	
						<th>Item Group</th>		
						<th>Qty.</th>	
						<th>Amount (USD)</th>
					</tr>
				</thead>
				<? 
				$i=1;
				foreach($trimsout_transfer_data_array as $row){ ?>
					<tr>
						<td><?= $i ?></td>
						<td><?= $row['store_id'] ?></td>
						<td><?= $row['item_desc']; ?></td>
						<td><?= $row['item_group_id'] ?></td>
						<td align="right"><?= fn_number_format($row['qty'],2) ?></td>
						<td align="right"><?= fn_number_format($row['amount'],2) ?></td>
					</tr>
				<? 
				$total_trims_out_qty+=$row['qty'];
				$total_trims_out+=$row['amount'];
				$i++;
				} 
				$grand_trims_cost=$total_trims_cost+$total_trims_in-$total_trims_out;
				?>
				<tfoot>
                    <th colspan="3">&nbsp;</th>
                    <th>Sub Total</th>
                    <th align="right"><? echo fn_number_format($total_trims_out_qty,2); ?></th>
                    <th align="right"><? echo fn_number_format($total_trims_out,2); ?></th>
                </tfoot>				
			</table>
			<table border="1" class="rpt_table" rules="all" width="735" cellpadding="0" cellspacing="0">
				<tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Total Trims Value</th>
                    <th align="right"><? echo fn_number_format($grand_trims_cost,2); ?></th>
				</tfoot>
			</table>
        </div>	
	</fieldset>  
<?
exit();
}

if($action=="gmt_print_wash_dye_embell_cost_actual")
{
	echo load_html_head_contents("Embellishment Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

	$embellishment_cost_data=sql_select("SELECT a.booking_no, sum(b.amount) as amount, b.rate, sum(b.wo_qnty) as wo_qnty, c.job_id, c.emb_name from wo_booking_mst a join wo_booking_dtls b on a.id=b.booking_mst_id join wo_pre_cost_embe_cost_dtls c on c.id=b.pre_cost_fabric_cost_dtls_id where a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.booking_type=6 and c.emb_name=$embl_name and c.job_id=$job_id group by a.booking_no, b.rate, c.job_id, c.emb_name");

 	?>
	<fieldset style="width:500px; margin-left:7px">
        <table class="rpt_table" width="460" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="30">SL</th>
                <th width="150">WO NO.</th>
                <th width="100">Qty</th>
                <th width="80">Rate</th>
                <th width="">Amount</th>
                
            </thead>
        </table>
        <div style="width:480px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="460" cellpadding="0" cellspacing="0">
					<? 
					$i=1;
					foreach($embellishment_cost_data as $row){ ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><? echo $i; ?></td>
                        <td width="150"><p><? echo $row[csf('booking_no')]; ?></p></td>
                        <td width="100" align="right"><? echo fn_number_format($row[csf('wo_qnty')],2); ?></td>
                        <td width="80" align="right"><p><? echo fn_number_format($row[csf('rate')],2); ?></p></td>
                        <td align="right" width=""><? echo fn_number_format($row[csf('amount')],2);?></td>
                       
                    </tr>
                	<?
                	$i++;
					$total_gmt_cost+=$row[csf('amount')];
                }
                ?>
                <tfoot>
                    
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Total</th>
                    <th align="right"><? echo fn_number_format($total_gmt_cost,2); ?></th>
                </tfoot>
			</table>
        </div>	
	</fieldset>  
<?
exit();
}

if($action=="wash_cost_actual")
{
	echo load_html_head_contents("Wash Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

 ?>
	<fieldset style="width:760px; margin-left:7px">
        <table class="rpt_table" width="755" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="110">WO NO.</th>
                <th width="80">WO Date</th>
                <th width="80">Currency</th>
                <th width="120">Amount (Taka)</th>
                <th width="120">Conversion rate</th>
                <th>Amount (USD)</th>
            </thead>
        </table>
        <div style="width:755px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="735" cellpadding="0" cellspacing="0">    
				<?
                $i=1; $total_aop_cost=0; $avg_rate=76;
                $sql="select a.booking_no, a.booking_date, a.currency_id, a.exchange_rate, sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and c.emb_name=3 and b.po_break_down_id=$po_id and a.item_category=25 and a.booking_type in(3,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no, a.booking_date, a.currency_id, a.exchange_rate";
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('booking_no')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                        <td width="80"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
                        <td align="right" width="120">
                            <? 
								$amnt_tk=$row[csf('amount')]*$row[csf('exchange_rate')];
                                echo fn_number_format($amnt_tk,2); 
                            ?>
                        </td>
                        <td align="right" width="120"><? echo fn_number_format($row[csf('exchange_rate')],2); ?>&nbsp;</td>
                        <td align="right">
                            <?
								if($row[csf('currency_id')]==1)
								{
                                	$amount=$row[csf('amount')]/$row[csf('exchange_rate')];
								}
								else
								{
									$amount=$row[csf('amount')];
								}
                                echo fn_number_format($amount,2); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_aop_cost+=$amount;
                }
                ?>
                <tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Total</th>
                    <th align="right"><? echo fn_number_format($total_aop_cost,2); ?></th>
                </tfoot>
			</table>
        </div>	
	</fieldset>  
<?
exit();
}

if($action=="commission_cost_actual")
{
	echo load_html_head_contents("Commission Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);
	$data=explode("_",$job_id);
	$jobid=$data[0];
	$job_no=$data[1];
	$ex_factory_qty=$data[2];
	$dzn_qnty=$data[3];

 	?>
	<fieldset style="width:560px; margin-left:10px">
        <table class="rpt_table" width="555" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="170">Commission Type</th>
                <th width="170">Commission Base</th>
                <th>Amount (USD)</th>
            </thead>
        </table>
        <div style="width:555px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="535" cellpadding="0" cellspacing="0">    
				<?
                $i=1; $total_comm_cost=0;
				$comm_cost_from_invoice=sql_select("SELECT a.commission, a.invoice_value, b.current_invoice_value, b.po_breakdown_id,  c.job_id from com_export_invoice_ship_mst a join com_export_invoice_ship_dtls b on a.id=b.mst_id join wo_po_break_down c on c.id=b.po_breakdown_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.job_id=$jobid");
				foreach($comm_cost_from_invoice as $row){
					$com_amt=$row[csf('commission')]*$row[csf('current_invoice_value')]/$row[csf('invoice_value')];
					$commission_cost_arr[$row[csf('job_id')]][1]=$com_amt;
					$foreign_local_cost_arr[$row[csf('job_id')]][1]=$com_amt;
				}
				$actual_cost_data=sql_select("SELECT b.job_id, c.cost_head, sum(c.amount) as amt, max(c.exchange_rate) as exchange_rate from wo_po_details_master a, wo_po_break_down b, wo_actual_cost_entry c where a.id=b.job_id and b.id=c.po_id and c.status_active=1 and c.is_deleted=0 and b.job_id=$jobid and c.cost_head=10 group by b.job_id, c.cost_head");
				foreach($actual_cost_data as $row){
					$commission_cost_arr[$row[csf('job_id')]][2]=$row[csf('amt')]/$row[csf('exchange_rate')];
					$foreign_local_cost_arr[$row[csf('job_id')]][2]=$row[csf('amt')]/$row[csf('exchange_rate')];
				}
                $sql="select particulars_id, commission_base_id, commission_amount as amount, job_id from wo_pre_cost_commiss_cost_dtls where job_id='$jobid' and status_active=1 and is_deleted=0 and commission_amount>0";
				//echo $sql; die;
				
                /* $result=sql_select($sql);
				if(count($result)>0){
					foreach($result as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="40"><? echo $i; ?></td>
							<td width="170"><p><? echo $commission_particulars[$row[csf('particulars_id')]]; ?></p></td>
							<td width="170"><p><? echo $commission_base_array[$row[csf('commission_base_id')]]; ?></p></td>
							<td align="right">
								<?
									$amount=$commission_cost_arr[$row[csf('job_id')]][$row[csf('particulars_id')]];
									echo fn_number_format($amount,2); 
								?>
							</td>
						</tr>
					<?
						$i++;
						$total_comm_cost+=$amount;
					}
				} */
				foreach($foreign_local_cost_arr as $job=>$particular_data)
				{			
					foreach($particular_data as $pid=>$data){
						if($data>0){
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="40"><? echo $i; ?></td>
								<td width="170"><p><? echo $commission_particulars[$pid]; ?></p></td>
								<td width="170"><p><? echo $commission_base_array[1]; ?></p></td>
								<td align="right">
									<?
										$amount=$data;
										echo fn_number_format($amount,2); 
									?>
								</td>
							</tr>
							<?
							$i++;
							$total_comm_cost+=$amount;
						}
					}						
				}
                ?>
                <tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Total</th>
                    <th align="right"><? echo fn_number_format($total_comm_cost,2); ?></th>
                </tfoot>
			</table>
        </div>	
	</fieldset>  
<?
exit();
}
//Ex-Factory Delv. and Return
if($action=="ex_factory_popup")
{
 	echo load_html_head_contents("Ex-Factory Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $id;//$job_no;
	
	$unit_price_arr=return_library_array( "select id, unit_price from wo_po_break_down where status_active=1 and is_deleted=0 and job_id=$id group by id, unit_price", "id", "unit_price");		?>
    
     <script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
 </script>
	<div style="width:100%" align="center" id="report_container">
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<fieldset style="width:630px"> 
        <div class="form_caption" align="center"><strong>Ex-Factory Details</strong></div><br />
            <div style="width:100%"> 
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="35">SL</th>
                        <th width="90">PO NO</th>
                        <th width="90">Ex-fac. Date</th>
                        <th width="120">System /Challan no</th>
                        <th width="100">Ex-Fact. Del.Qty.</th>
                        <th width="100">Ex-Fact. Return Qty.</th>
                        <th width="">Ex-Fact. Value</th>
                       
                     </tr>   
                </thead> 	 	
            </table>  
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i=1;
              
				$exfac_sql=("select c.total_set_qnty,a.id, a.po_number, b.challan_no,b.ex_factory_date, 
				CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
				CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty 
				from  wo_po_details_master c join wo_po_break_down a on c.id=a.job_id join pro_ex_factory_mst b on a.id=b.po_break_down_id  where  b.status_active=1 and b.is_deleted=0 and a.job_id in($id) ");
                $sql_dtls=sql_select($exfac_sql);
                
                foreach($sql_dtls as $row_real)
                { 
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF"; 
					$tot_exfact_qty=($row_real[csf("ex_factory_qnty")]-$row_real[csf("ex_factory_return_qnty")])/$row_real[csf('total_set_qnty')]; 
					$unit_price=$unit_price_arr[$row_real[csf('id')]]; 
					$tot_exfact_val=$tot_exfact_qty*$unit_price;                            
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="35"><? echo $i; ?></td> 
                        <td width="90"><? echo $row_real[csf("po_number")]; ?></td>
                        <td width="90"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
                        <td width="120"><? echo $row_real[csf("challan_no")]; ?></td>
                        <td width="100" align="right"><? echo $row_real[csf("ex_factory_qnty")]/$row_real[csf('total_set_qnty')]; ?></td>
                        <td width="100" align="right"><? echo $row_real[csf("ex_factory_return_qnty")]/$row_real[csf('total_set_qnty')]; ?></td>
                        <td width="" align="right"><? echo fn_number_format($tot_exfact_val,2); ?></td>
                    </tr>
                    <? 
                    $rec_qnty+=$row_real[csf("ex_factory_qnty")]/$row_real[csf('total_set_qnty')];
					 $rec_return_qnty+=$row_real[csf("ex_factory_return_qnty")]/$row_real[csf('total_set_qnty')];
					  $total_exfact_val+=$tot_exfact_val;
                    $i++;
                }
                ?>
                <tfoot>
                <tr>
                    <th colspan="4">Total</th>
                     <th><? echo fn_number_format($rec_qnty,2); ?></th>
                    <th><? echo fn_number_format($rec_return_qnty,2); ?></th>
                    <th><? echo fn_number_format($total_exfact_val,2); ?></th>
                </tr>
                <tr>
                 <th colspan="4">Total Balance</th>
                 <th colspan="2" align="right"><? echo fn_number_format($rec_qnty-$rec_return_qnty,2); ?></th>
                 <th><? echo fn_number_format($total_exfact_val,2); ?></th>
                </tr>
                </tfoot>
            </table>
        </div> 
		</fieldset>
	</div>    
	<?
    exit();	
}
//Ex-Factory Delv. and Return
if($action=="show_po_detail_report")
{
	?>
    <script>
	
	function new_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('html_print_data').innerHTML+'</body</html>');
		d.close(); 
	}
	function openmypage_mkt(mkt_data,type,tittle)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'multi_style_wise_post_costing_report_controller.php?mkt_data='+mkt_data+'&action='+type, tittle, 'width=660px, height=200px, center=1, resize=0, scrolling=0', '../../../');
	}
	
	function openmypage(po_id,type,tittle)
	{
		var popup_width='';
		if(type=="dye_fin_cost") 
		{
			popup_width='1140px';
		}
		else if(type=="fabric_purchase_cost") 
		{
			popup_width='740px';
		}
		else popup_width='1060px';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'multi_style_wise_post_costing_report_controller.php?po_id='+po_id+'&action='+type, tittle, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../../');
	}
    </script>
    <?	
		
		
		echo load_html_head_contents("Po Detail", "../../../../", 1, 1,$unicode,'','');
		extract($_REQUEST);
		$company_short_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
		
		
	  	$bgcolor="#E9F3FF";  $bgcolor2="#FFFFFF";	
		
	  	 $gsm_weight_top=return_field_value("gsm_weight", "wo_pre_cost_fabric_cost_dtls", "job_no='$job_no' and body_part_id=1");
		 $gsm_weight_bottom=return_field_value("gsm_weight", "wo_pre_cost_fabric_cost_dtls", "job_no='$job_no' and body_part_id=20");
		 $costing_date=return_field_value("costing_date", "wo_pre_cost_mst", "job_no='$job_no'","costing_date");
		 $po_qty=0;
		 $po_plun_cut_qty=0;
		 $total_set_qnty=0;$tot_po_value=0;
		 $sql_po="select a.job_no,a.total_set_qnty,b.id,c.order_total,b.unit_price,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity ,c.plan_cut_qnty  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst   and b.id=c.po_break_down_id and b.id =$po_id  and a.job_no ='$job_no'  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id";
		$sql_po_data=sql_select($sql_po);
		foreach($sql_po_data as $sql_po_row)
		{
		$po_qty+=$sql_po_row[csf('order_quantity')];
		$po_plun_cut_qty+=$sql_po_row[csf('plan_cut_qnty')];
		$tot_po_value+=$sql_po_row[csf('order_total')];
		$total_set_qnty=$sql_po_row[csf('total_set_qnty')];
		$unit_price=$sql_po_row[csf('unit_price')]/$total_set_qnty;
		$tot_po_value=$po_qty*$unit_price;
		}
		$fab_knit_req_kg_avg=0;
		$fab_woven_req_yds_avg=0;
		if($db_type==0)
		{
		$sql = "select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.gmts_item_id,a.order_uom,a.job_quantity,a.avg_unit_price,b.costing_per,b.budget_minute,b.approved,b.updated_by,b.sew_smv,b.sew_effi_percent,b.incoterm,b.exchange_rate,c.fab_knit_req_kg,c.fab_knit_fin_req_kg,c.fab_woven_req_yds,c.fab_woven_fin_req_yds,c.fab_yarn_req_kg from wo_po_details_master a, wo_pre_cost_mst b left join wo_pre_cost_sum_dtls c on   b.job_no=c.job_no where a.job_no=b.job_no and a.job_no='$job_no' and a.company_name=$company_name  and a.status_active=1  order by a.job_no";  
		}
		if($db_type==2)
		{
		 $sql = "select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.gmts_item_id,a.order_uom,a.job_quantity,a.avg_unit_price,b.costing_per,b.budget_minute,b.approved,b.updated_by,b.sew_smv,b.sew_effi_percent,b.incoterm,b.exchange_rate,c.fab_knit_req_kg,c.fab_knit_fin_req_kg,c.fab_woven_req_yds,c.fab_woven_fin_req_yds,c.fab_yarn_req_kg from wo_po_details_master a, wo_pre_cost_mst b left join wo_pre_cost_sum_dtls c on   b.job_no=c.job_no where a.job_no=b.job_no and a.status_active=1 and a.job_no='$job_no' and a.company_name=$company_name  order by a.job_no";  
		}
		
		$data_array=sql_select($sql);
		
		
		$condition= new condition();
		if(str_replace("'","",$job_no) !='')
		{
		  $condition->job_no("='$job_no'");
		}
		if(str_replace("'","",$po_id) !='')
		{
		  $condition->po_id("=$po_id");
		}
		 $condition->init();
		
		$fabric= new fabric($condition);
		$fabric_costing_qty_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();
		$yarn= new yarn($condition);
	    $yarn_req_arr=$yarn->getOrderWiseYarnQtyArray();
				
		
		 $fab_knit_qty_gray=($fabric_costing_qty_arr['knit']['grey'][$po_id]/$po_qty)*12;
		 $fab_woven_qty_gray=($fabric_costing_qty_arr['woven']['grey'][$po_id]/$po_qty)*12;
		 
		 $fab_knit_qty_finish=($fabric_costing_qty_arr['knit']['finish'][$po_id]/$po_qty)*12;
		 $fab_woven_qty_finish=($fabric_costing_qty_arr['woven']['finish'][$po_id]/$po_qty)*12;
		 $yarn_req_qty_avg=($yarn_req_arr[$po_id]/$po_qty)*12;
		 //echo  $fab_knit_qty_gray.'='.$fab_woven_qty_gray;
		 
	?>
	<div style="width:100%" align="center"> <input type="button" onClick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:120px;"/>
		<fieldset style="width:850px" id="html_print_data">
       
         <table width="840px"   cellpadding="0" cellspacing="0" border="0">
        	<tr>
            	<td rowspan="3" width="25%">
                   <?
                $data_array2=sql_select("select image_location  from common_photo_library  where master_tble_id='$company_name' and form_name='company_details' and is_deleted=0 and file_type=1");
              	foreach($data_array2 as $img_row)
                {
					?>
                    <img src='../../../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50' align="middle" />	
                    <? 
                }
                ?>
                </td>
            	<td align="center"><b style="font-size:20px;"><? echo $company_short_arr[$company_name]; ?></b></td>
            	<td rowspan="3" width="25%" align="right">
                <?
                   $data_array_img=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");               
                foreach($data_array_img as $img_row)
                {
					?>
                    <img src='../../../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='80' align="middle" />	
                    <? 
                }
                ?>
               
                </td>
            </tr>
        	<tr>
            	<td align="center"><b style="font-size:14px;">Pre and Post Cost Comparison</b></td>
            </tr>
           <tr>
                <td align="center">
                    
					<? if( $data_array[0][csf("approved")]==1){echo "<div style='font-size:18px; color:#F00; background:#CCC;'>THIS COST SHEET IS APPROVED </div>";
					
					} else {echo "&nbsp;";} 
					
					$prepared_app_data = $data_array[0][csf("updated_by")];		

					?> 
                     
                </td>
            </tr>
        
        </table>
       			 <? 
				$uom="";
				foreach ($data_array as $row)
				{	
					$order_price_per_dzn=0;
					$order_job_qnty=0;
					$avg_unit_price=0;
					
					$order_values = $row[csf("job_quantity")]*$row[csf("avg_unit_price")];
					$result =sql_select("select po_number,grouping,file_no,pub_shipment_date from wo_po_break_down where job_no_mst='$job_no'  and id=$po_id and status_active=1 and is_deleted=0 order by pub_shipment_date DESC");
					$job_in_orders = '';$pulich_ship_date='';$job_in_file = '';$job_in_ref = '';
					foreach ($result as $val)
					{
						$job_in_orders= $val[csf('po_number')];
						$pulich_ship_date = $val[csf('pub_shipment_date')];
						$job_in_ref.=$val[csf('grouping')].",";
						$job_in_file.=$val[csf('file_no')].",";
						
					}
					$job_in_orders = $job_in_orders;
					
					$job_ref=array_unique(explode(",",rtrim($job_in_ref,", ")));
					$job_file=array_unique(explode(",",rtrim($job_in_file,", ")));
					
					foreach ($job_ref as $ref)
					{
						$ref_cond.=", ".$ref;
					}
					$file_con='';
					foreach ($job_file as $file)
					{
						if($file_con=='') $file_cond=$file; else $file_cond.=", ".$file;
					}
					if($row[csf("costing_per")]==1){$order_price_per_dzn=12;$costing_for=" For 1 DZN";}
					else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;$costing_for=" For PCS";}
					else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;$costing_for=" For 2 DZN";}
					else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;$costing_for="For 3 DZN";}
					else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;$costing_for=" For 4 DZN";}
		
		?>
            	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px" rules="all">
                	<tr bgcolor="<? echo $bgcolor2;?>">
                    	<td width="130">Job Number</td>
                        <td width="100"><b><? echo $row[csf("job_no")]; ?></b></td>
                        <td width="130">Buyer</td>
                        <td width="110"><b><? echo $buyer_arr[$row[csf("buyer_name")]]; $buyer_id=$row[csf("buyer_name")];?></b></td>
                        <td>Garments Item</td>
                        <? 
							$grmnt_items = "";
							if($garments_item[$row[csf("gmts_item_id")]]=="")
							{
								
								$grmts_sql = sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where job_no=$job_no");
								foreach($grmts_sql as $key=>$val){
									$grmnt_items .=$garments_item[$val[csf("gmts_item_id")]].", ";
								}
								$grmnt_items = substr_replace($grmnt_items,"",-1,1);
							}else{
								$grmnt_items = $garments_item[$row[csf("gmts_item_id")]];
							}
							
						?>
                        <td width="160"><b><? echo $grmnt_items; ?></b></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                    	<td>Style Ref. No </td>
                        <td><b><? echo $row[csf("style_ref_no")]; ?></b></td>
                         <td>Costing Date</td><td><? echo $costing_date;?></td>
                        <td>PO Qnty</td>
                        <td><b><? $uom=$row[csf("order_uom")]; echo fn_number_format($po_qty)." ". $unit_of_measurement[$row[csf("order_uom")]]; ?></b></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2;?>">
                    	<td>Order Numbers</td>
                        <td colspan="3"><? echo $job_in_orders; ?></td>
                        <td>Plan Cut Qnty [Cut % <? echo fn_number_format((($po_plun_cut_qty/$total_set_qnty-$po_qty)/$po_qty)*100,2);?>]</td>
                        <td><b><? $uom=$row[csf("order_uom")]; echo fn_number_format($po_plun_cut_qty/$total_set_qnty)." ". $unit_of_measurement[$row[csf("order_uom")]]; ?></b></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor;?>">
                    	<td>Knit Fabric Cons</td>
                        <td><b><? echo $fab_knit_qty_gray;$fab_knit_req_kg_avg+=$fab_knit_qty_gray; ?> (Kg)</b></td>
	
                        <td>Woven Fabric Cons</td>
                        <td><b><? echo $fab_woven_qty_gray;$fab_woven_req_yds_avg+= $fab_woven_qty_gray;?> (Yds)</b></td>
                        <td>Price Per Unit</td>
                        <td><b><? echo fn_number_format($row[csf("avg_unit_price")],2); ?> USD</b></td>
                    </tr>
                    
                    <tr bgcolor="<? echo $bgcolor2;?>">
                    	<td>Avg Yarn Req</td>
                        <td><b><? echo $yarn_req_qty_avg; ?> (Kg)</b></td>
                        <td>Woven Fin Fabric Cons</td>
                        <td><b><? echo $fab_woven_qty_finish; ?>(Yds)</b></td>
                        
                        <td>Order Value</td>
                        <td><b><? echo fn_number_format($po_qty*$row[csf("avg_unit_price")],2); ?></b></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor;?>">
                    	<td>Knit Fin Fabric Cons</td>
                        <td><b><? echo $fab_knit_qty_finish; ?> (Kg)</b></td>
                        <td>Costing Per</td>
                        <td><b><? echo $costing_for; ?></b></td>
                        <td>Inco Term</td>
                        <td><b><? echo $incoterm[$row[csf("incoterm")]]; ?></b></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2;?>">
                        <td>GSM</td>
                        <td><b><? echo $gsm_weight_top.",".$gsm_weight_bottom ?></b></td>
                        <td>SMV</td>
                        <td><b><? $sew_smv=$row[csf("sew_smv")]; echo fn_number_format($sew_smv,2); ?> </b></td>
                        <td>Efficiency %</td>
                        <td colspan="1"><b><? echo $sew_effi_percent=$row[csf("sew_effi_percent")] ?> </b></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor;?>">
                        <td>Exchange Rate</td>
                        <td><b><? echo $exchange_rate=$row[csf("exchange_rate")]; ?></b></td>
                        <td>Budget SAH</td>
                        <td><b><? echo fn_number_format(($row[csf("sew_smv")]*$po_qty)/60,2); ?></b></td>
                        <td>Shipment Date </td>
                        <td><b><? echo $pulich_ship_date;?></b></td>
                    </tr>
                    
                    
                    
                    
                </table>
            <?	
			
			
	}//end first foearch
	
				$condition= new condition();
				 $condition->company_name("=$company_name");
				 if(str_replace("'","",$job_no) !=''){
					  $condition->job_no("='$job_no'");
				 }
				if(str_replace("'","",$po_id)!='')
				 {
					 $condition->po_id("=$po_id");
				 }
				 $condition->init();
				$fabric= new fabric($condition);
				$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
				 $yarn= new yarn($condition);
				 $yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
				 $conversion= new conversion($condition);
				 $conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
				// print_r($conversion_costing_arr_process);
				 $trims= new trims($condition);
				 $trims_costing_arr=$trims->getAmountArray_by_order();
				$emblishment= new emblishment($condition);
				$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
				$commission= new commision($condition);
				$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
				$commercial= new commercial($condition);
				$commercial_costing_arr=$commercial->getAmountArray_by_order();
				$other= new other($condition);
				$other_costing_arr=$other->getAmountArray_by_order();
				$wash= new wash($condition);
				$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();
				
				$exchange_rate=76; 
				$sql_fin_purchase="select b.po_breakdown_id, sum(a.cons_rate*b.quantity) as finish_purchase_amnt from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.receive_basis<>9 and a.item_category=2 and a.transaction_type=1 and b.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id=$po_id group by b.po_breakdown_id";
				$dataArrayFinPurchase=sql_select($sql_fin_purchase);
				foreach($dataArrayFinPurchase as $finRow)
				{
					$finish_purchase_amnt_arr[$finRow[csf('po_breakdown_id')]]=$finRow[csf('finish_purchase_amnt')]/$exchange_rate;
				}
				
				 $subconInBillDataArray=sql_select("select b.order_id, 
									  sum(CASE WHEN a.process_id=2 THEN b.amount END) AS knit_bill,
									  sum(CASE WHEN a.process_id=4 THEN b.amount END) AS dye_finish_bill
									  from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_source=1 and a.process_id in(2,4) and b.order_id=$po_id group by b.order_id");
				foreach($subconInBillDataArray as $subRow)
				{
					$subconCostArray[$subRow[csf('order_id')]]['knit_bill']=$subRow[csf('knit_bill')];
					$subconCostArray[$subRow[csf('order_id')]]['dye_finish_bill']=$subRow[csf('dye_finish_bill')];
				}	
				$prodcostDataArray=sql_select("select job_no, 
									  sum(CASE WHEN cons_process=1 THEN amount END) AS knit_charge,
									  sum(CASE WHEN cons_process=30 THEN amount END) AS yarn_dye_charge,
									  sum(CASE WHEN cons_process=35 THEN amount END) AS aop_charge,
									  sum(CASE WHEN cons_process not in(1,2,30,35) THEN amount END) AS dye_finish_charge
									  from wo_pre_cost_fab_conv_cost_dtls where status_active=1 and is_deleted=0 group by job_no");
				foreach($prodcostDataArray as $prodRow)
				{
					$prodcostArray[$prodRow[csf('job_no')]]['knit_charge']=$prodRow[csf('knit_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['yarn_dye_charge']=$prodRow[csf('yarn_dye_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['aop_charge']=$prodRow[csf('aop_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['dye_finish_charge']=$prodRow[csf('dye_finish_charge')];
				}
				$actualCostDataArray=sql_select("select cost_head,po_id,sum(amount_usd) as amount_usd from wo_actual_cost_entry where company_id=$company_name and status_active=1 and is_deleted=0 group by cost_head,po_id");
				foreach($actualCostDataArray as $actualRow)
				{
				   $actualCostArray[$actualRow[csf('cost_head')]][$actualRow[csf('po_id')]]=$actualRow[csf('amount_usd')];
				}
					
				$bookingDataArray=sql_select("select a.booking_type, a.item_category, a.currency_id, a.exchange_rate, b.po_break_down_id, b.process, b.amount, b.pre_cost_fabric_cost_dtls_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id=$po_id and  a.company_id=$company_name and a.item_category in(4,12,25) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
				foreach($bookingDataArray as $woRow)
				{
					$amount=0; $trimsAmnt=0;
					if($woRow[csf('currency_id')]==1) { $amount=$woRow[csf('amount')]/$exchange_rate; } else { $amount=$woRow[csf('amount')]; }
					
					if($woRow[csf('item_category')]==25 && ($woRow[csf('booking_type')]==3 || $woRow[csf('booking_type')]==6)) 
					{ 
						if($embell_type_arr[$woRow[csf('pre_cost_fabric_cost_dtls_id')]]==3)
						{
							$washCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
						}
						else
						{
							$embellCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
						}
					}
					else if($woRow[csf('item_category')]==12 && $woRow[csf('process')]==35 && ($woRow[csf('booking_type')]==3 || $woRow[csf('booking_type')]==6)) 
					{ 
						$aopCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
					}
					else if($woRow[csf('item_category')]==4)
					{
						if($woRow[csf('currency_id')]==1) { $trimsAmnt=$woRow[csf('amount')]/$woRow[csf('exchange_rate')]; } else { $trimsAmnt=$woRow[csf('amount')]; }
						$actualTrimsCostArray[$woRow[csf('po_break_down_id')]]+=$trimsAmnt; 
					}
				}
				$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master where item_category_id=1", "id", "avg_rate_per_unit"  );
				$receive_array=array();
				$sql_receive="select prod_id, sum(order_qnty) as qty, sum(order_amount) as amnt from inv_transaction where transaction_type=1 and item_category=1 and status_active=1 and is_deleted=0 group by prod_id";
				$resultReceive = sql_select($sql_receive);
				foreach($resultReceive as $invRow)
				{
					$avg_rate=$invRow[csf('amnt')]/$invRow[csf('qty')];
					$receive_array[$invRow[csf('prod_id')]]=$avg_rate;
				}
				$yarnTrimsDataArray=sql_select("select b.po_breakdown_id, b.prod_id, a.item_category, 
					sum(CASE WHEN a.transaction_type=2 and b.entry_form ='3' and b.trans_type=2 and b.issue_purpose!=2 THEN b.quantity ELSE 0 END) AS yarn_iss_qty,
					sum(CASE WHEN a.transaction_type=4 and b.entry_form ='9' and b.trans_type=4 THEN b.quantity ELSE 0 END) AS yarn_iss_return_qty,
					sum(CASE WHEN a.transaction_type=5 and b.entry_form ='11' and b.trans_type=5 THEN b.quantity ELSE 0 END) AS trans_in_qty_yarn,
					sum(CASE WHEN a.transaction_type=6 and b.entry_form ='11' and b.trans_type=6 THEN b.quantity ELSE 0 END) AS trans_out_qty_yarn,
					sum(CASE WHEN a.transaction_type=2 and b.entry_form ='25' and b.trans_type=2 THEN a.cons_rate*b.quantity ELSE 0 END) AS trims_issue_amnt
					from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category in(1,4) and a.transaction_type in(2,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.po_breakdown_id=$po_id group by b.po_breakdown_id, b.prod_id, a.item_category");
				foreach($yarnTrimsDataArray as $invRow)
				{
					if($invRow[csf('item_category')]==1)
					{
						$iss_qty=$invRow[csf('yarn_iss_qty')]+$invRow[csf('trans_in_qty_yarn')]-$invRow[csf('yarn_iss_return_qty')]-$invRow[csf('trans_out_qty_yarn')];
						$rate='';
						if($receive_array[$invRow[csf('prod_id')]]>0)
						{
							$rate=$receive_array[$invRow[csf('prod_id')]];
						}
						else
						{
							$rate=$avg_rate_array[$invRow[csf('prod_id')]]/$exchange_rate;
						}
						
						$iss_amnt=$iss_qty*$rate;
						$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]][1]+=$iss_amnt;
					}
					else
					{
						$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]][4]+=$invRow[csf('trims_issue_amnt')];
					}
				}
				$yarncostDataArray=sql_select("select job_no, sum(amount) as amnt, sum(rate*avg_cons_qnty) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 and job_no='$job_no' group by job_no");
				foreach($yarncostDataArray as $yarnRow)
				{
				   $yarncostArray[$yarnRow[csf('job_no')]]=$yarnRow[csf('amount')];
				}
				
				$fabriccostDataArray=sql_select("select job_no, costing_per_id, embel_cost, wash_cost, cm_cost, commission, currier_pre_cost, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where status_active=1 and is_deleted=0 and job_no='$job_no'");
				foreach($fabriccostDataArray as $fabRow)
				{
					 $fabriccostArray[$fabRow[csf('job_no')]]['costing_per_id']=$fabRow[csf('costing_per_id')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['embel_cost']=$fabRow[csf('embel_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['wash_cost']=$fabRow[csf('wash_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['cm_cost']=$fabRow[csf('cm_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['commission']=$fabRow[csf('commission')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['currier_pre_cost']=$fabRow[csf('currier_pre_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['lab_test']=$fabRow[csf('lab_test')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['inspection']=$fabRow[csf('inspection')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['freight']=$fabRow[csf('freight')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['comm_cost']=$fabRow[csf('comm_cost')];
				}
				
				$ex_factory_arr=return_library_array( "select po_break_down_id, 
				sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as qnty 
				from pro_ex_factory_mst where status_active=1 and is_deleted=0 and po_break_down_id =$po_id group by po_break_down_id", "po_break_down_id", "qnty");
				
					$dzn_qnty=0;
					$costing_per_id=$fabriccostArray[$job_no]['costing_per_id'];
					if($costing_per_id==1)
					{
						$dzn_qnty=12;
					}
					else if($costing_per_id==3)
					{
						$dzn_qnty=12*2;
					}
					else if($costing_per_id==4)
					{
						$dzn_qnty=12*3;
					}
					else if($costing_per_id==5)
					{
						$dzn_qnty=12*4;
					}
					else
					{
						$dzn_qnty=1;
					}
					
				 $ex_factory_qty=$ex_factory_arr[$po_id];
				 $unit_price=$unit_price/$total_set_qnty;
				$tot_ex_factory_val=$ex_factory_qty*$unit_price;
				$tot_fabric_purchase_cost_mkt=$fabric_costing_arr['knit']['grey'][$po_id]+$fabric_costing_arr['woven']['grey'][$po_id];
				$tot_fabric_purchase_cost_actual=$finish_purchase_amnt_arr[$po_id];
				$tot_yarn_cost_mkt=$yarn_costing_arr[$po_id];
				$tot_yarn_dye_cost_mkt=$conversion_costing_arr_process[$po_id][30];
				$tot_knit_cost_mkt=$conversion_costing_arr_process[$po_id][1];
				
					$tot_dye_finish_cost_mkt=0;$not_yarn_dyed_cost_arr=array(1,2,30,35);
					foreach($conversion_cost_head_array as $process_id=>$val)
					{
						if(!in_array($process_id,$not_yarn_dyed_cost_arr))
						{
							$tot_dye_finish_cost_mkt+=array_sum($conversion_costing_arr_process[$po_id][$process_id]);
						}
					}
					$tot_dye_finish_cost_actual=$subconCostArray[$po_id]['dye_finish_bill']/$exchange_rate;
					$tot_knit_cost_actual=$subconCostArray[$po_id]['knit_bill']/$exchange_rate;
					
					$tot_aop_cost_actual=$aopCostArray[$po_id];
					$tot_aop_cost_mkt=$conversion_costing_arr_process[$po_id][35];
					$tot_trims_cost_mkt=$trims_costing_arr[$po_id];
					$tot_trims_cost_actual=$actualTrimsCostArray[$po_id];
					
					$print_amount=$emblishment_costing_arr_name[$po_id][1];
					$embroidery_amount=$emblishment_costing_arr_name[$po_id][2];
					$special_amount=$emblishment_costing_arr_name[$po_id][4];
					$wash_cost=$emblishment_costing_arr_name_wash[$po_id][3];
					$other_amount=$emblishment_costing_arr_name[$po_id][5];
					
					$tot_embell_cost_mkt=$print_amount+$embroidery_amount+$special_amount+$other_amount;
					$tot_embell_cost_actual=$embellCostArray[$po_id];
					$tot_wash_cost_mkt=$wash_cost;
					$tot_wash_cost_actual=$washCostArray[$po_id];
					
					$foreign_cost=$commission_costing_arr[$po_id][1];
					$local_cost=$commission_costing_arr[$po_id][2];
					
					
					
					$tot_commission_cost_mkt=$foreign_cost+$local_cost;
					$tot_commission_cost_actual=($ex_factory_qty/$dzn_qnty)*$fabriccostArray[$job_no]['commission'];
					$tot_comm_cost_actual=$actualCostArray[6][$po_id];
					$tot_comm_cost_mkt=$commercial_costing_arr[$po_id];
					
					$tot_test_cost_mkt=$other_costing_arr[$po_id]['lab_test'];
					$tot_freight_cost_mkt=$other_costing_arr[$po_id]['freight'];
					$tot_inspection_cost_mkt=$other_costing_arr[$po_id]['inspection'];
					$tot_certificate_cos_mktt=$other_costing_arr[$po_id]['certificate_pre_cost'];
					//$common_oh_cost=$other_costing_arr[$row[csf('id')]]['common_oh'];
					$tot_currier_cost_mkt=$other_costing_arr[$po_id]['currier_pre_cost'];
					$tot_cm_cost_mkt=$other_costing_arr[$po_id]['cm_cost'];
					
					$tot_yarn_cost_actual=$yarnTrimsCostArray[$po_id][1];
					
					$tot_freight_cost_actual=$actualCostArray[2][$po_id];
					$tot_test_cost_actual=$actualCostArray[1][$po_id];
					$tot_inspection_cost_actual=$actualCostArray[3][$po_id];
					$tot_currier_cost_actual=$actualCostArray[4][$po_id];
					$tot_cm_cost_actual=$actualCostArray[5][$po_id];
					
					//$tot_mkt_all_cost=$tot_mkt_all_cost;
					$tot_mkt_all_cost=$tot_yarn_cost_mkt+$tot_knit_cost_mkt+$tot_dye_finish_cost_mkt+$tot_yarn_dye_cost_mkt+$tot_aop_cost_mkt+$tot_trims_cost_mkt+$tot_embell_cost_mkt+$tot_wash_cost_mkt+$tot_commission_cost_mkt+$tot_comm_cost_mkt+$tot_freight_cost_mkt+$tot_test_cost_mkt+$tot_inspection_cost_mkt+$tot_currier_cost_mkt+$tot_cm_cost_mkt+$tot_fabric_purchase_cost_mkt;
					
					$tot_mkt_margin=$tot_po_value-$tot_mkt_all_cost;
					$tot_mkt_margin_perc=($mkt_margin/$tot_po_value)*100;
					
					$tot_actual_all_cost=$tot_yarn_cost_actual+$tot_knit_cost_actual+$tot_dye_finish_cost_actual+$tot_yarn_dye_cost_actual+$tot_aop_cost_actual+$tot_trims_cost_actual+$tot_embell_cost_actual+$tot_wash_cost_actual+$tot_commission_cost_actual+$tot_comm_cost_actual+$tot_freight_cost_actual+$tot_test_cost_actual+$tot_inspection_cost_actual+$tot_currier_cost_actual+$tot_cm_cost_actual+$tot_fabric_purchase_cost_actual;
					
					$tot_actual_margin=$tot_ex_factory_val-$tot_actual_all_cost;
					$tot_actual_margin_perc=($tot_actual_margin/$tot_ex_factory_val)*100;
					
	?>
         <br/> <br/>
        <table width="760" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
      
                            <thead>
                                <th width="30">SL</th>
                                <th width="180">Particulars</th>
                                <th width="130">Pre Costing</th>
                                <th width="80">%</th>
                                <th width="130">Post-Costing</th>
                                <th width="80">%</th>
                                <th>Variance</th>
                            </thead> 
							<?
								$bgcolor1='#E9F3FF';
								$bgcolor2='#FFFFFF';
							?>
                            </thead>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_1','<? echo $bgcolor1; ?>')" id="trtd_1">
                                <td align="center">1</td>
                                <td>PO/Shipment Value</td>
                                <td align="right"><? echo fn_number_format($tot_po_value,2); ?></td>
                                <td align="right">&nbsp;</td>
                                <td align="right"><? echo fn_number_format($tot_ex_factory_val,2); ?></td>
                                <td align="right">&nbsp;</td>
                                <td align="right"><? echo fn_number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_2','<? echo $bgcolor2; ?>')" id="trtd_2">
                                <td colspan="7"><b>Cost</b></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_3','<? echo $bgcolor1; ?>')" id="trtd_3">
                                <td align="center">2</td>
                                <td>Fabric Purchase Cost</td>
                                <td align="right"><? echo fn_number_format($tot_fabric_purchase_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_fabric_purchase_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_fabric_purchase_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_fabric_purchase_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_fabric_purchase_cost_mkt-$tot_fabric_purchase_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_4','<? echo $bgcolor2; ?>')" id="trtd_4">
                                <td align="center">3</td>
                                <td>Yarn Cost+Yarn Dyeing Cost</td>
                                <td align="right"><a href="#report_details" onClick="openmypage_mkt('<? echo $tot_yarn_cost_mkt."**".$tot_yarn_dye_cost_mkt; ?>','mkt_yarn_cost','Grey And Dyed Yarn Mkt. Cost Details')"></a><? echo fn_number_format($tot_yarn_cost_mkt+$tot_yarn_dye_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format((($tot_yarn_cost_mkt+$tot_yarn_dye_cost_mkt)/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_yarn_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_yarn_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_yarn_cost_mkt+$tot_yarn_dye_cost_mkt)-$tot_yarn_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_5','<? echo $bgcolor2; ?>')" id="trtd_5">
                                <td align="center">4</td>
                                <td>Knitting Cost</td>
                                <td align="right"><? echo fn_number_format($tot_knit_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_knit_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_knit_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_knit_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_knit_cost_mkt-$tot_knit_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>"  onclick="change_color('trtd_6','<? echo $bgcolor2; ?>')" id="trtd_6">
                                <td align="center">5</td>
                                <td>Dye & Fin Cost</td>
                                <td align="right"><? echo fn_number_format($tot_dye_finish_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_dye_finish_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><a href="#report_details" onClick="openmypage('<? echo $po_ids; ?>','dye_fin_cost','Dye & Fin Cost Details')"></a><? echo fn_number_format($tot_dye_finish_cost_actual,2); ?></td>
                                <td align="right">
								<? 
								echo fn_number_format(($tot_dye_finish_cost_actual/$tot_ex_factory_val)*100,2); 
								
								?>
                                </td>
                                <td align="right"><? echo fn_number_format($tot_dye_finish_cost_mkt-$tot_dye_finish_cost_actual,2); ?></td>
                            </tr>
                            
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_7','<? echo $bgcolor1; ?>')" id="trtd_7">
                                <td align="center">6</td>
                                <td>AOP & Others Cost</td>
                                <td align="right"><? echo fn_number_format($tot_aop_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_aop_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_aop_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_aop_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_aop_cost_mkt-$tot_aop_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_8','<? echo $bgcolor2; ?>')" id="trtd_8">
                                <td align="center">7</td>
                                <td>Trims Cost</td>
                                <td align="right"><? echo fn_number_format($tot_trims_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_trims_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_trims_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_trims_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_trims_cost_mkt-$tot_trims_cost_actual,2); ?></td>
                            </tr> 
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_9','<? echo $bgcolor1; ?>')" id="trtd_9">
                                <td align="center">8</td>
                                <td>Embellishment Cost</td>
                                <td align="right"><? echo fn_number_format($tot_embell_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_embell_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_embell_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_embell_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_embell_cost_mkt-$tot_embell_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_10','<? echo $bgcolor2; ?>')" id="trtd_10">
                                <td align="center">9</td>
                                <td>Wash Cost</td>
                                <td align="right"><? echo fn_number_format($tot_wash_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_wash_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_wash_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_wash_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_wash_cost_mkt-$tot_wash_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_11','<? echo $bgcolor1; ?>')" id="trtd_11">
                                <td align="center">10</td>
                                <td>Commission Cost</td>
                                <td align="right"><? echo fn_number_format($tot_commission_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_commission_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_commission_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_commission_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_commission_cost_mkt-$tot_commission_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_12','<? echo $bgcolor2; ?>')" id="trtd_12">
                                <td align="center">11</td>
                                <td>Commercial Cost</td>
                                <td align="right"><? echo fn_number_format($tot_comm_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_comm_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_comm_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_comm_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_comm_cost_mkt-$tot_comm_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_13','<? echo $bgcolor1; ?>')" id="trtd_13">
                                <td align="center">12</td>
                                <td>Freight Cost</td>
                                <td align="right"><? echo fn_number_format($tot_freight_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_freight_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_freight_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_freight_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_freight_cost_mkt-$tot_freight_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>"  onclick="change_color('trtd_14','<? echo $bgcolor2; ?>')" id="trtd_14">
                                <td align="center">13</td>
                                <td>Testing Cost</td>
                                <td align="right"><? echo fn_number_format($tot_test_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_test_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_test_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_test_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_test_cost_mkt-$tot_test_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_15','<? echo $bgcolor1; ?>')" id="trtd_15">
                                <td align="center">14</td>
                                <td>Inspection Cost</td>
                                <td align="right"><? echo fn_number_format($tot_inspection_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_inspection_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_inspection_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_inspection_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_inspection_cost_mkt-$tot_inspection_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_16','<? echo $bgcolor2; ?>')" id="trtd_16">
                                <td align="center">15</td>
                                <td>Courier Cost</td>
                                <td align="right"><? echo fn_number_format($tot_currier_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_currier_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_currier_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_currier_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_currier_cost_mkt-$tot_currier_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_17','<? echo $bgcolor1; ?>')" id="trtd_17">
                                <td align="center">16</td>
                                <td>CM</td>
                                <td align="right"><? echo fn_number_format($tot_cm_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_cm_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_cm_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_cm_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_cm_cost_mkt-$tot_cm_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_18','<? echo $bgcolor2; ?>')" id="trtd_18">
                                <td align="center">17</td>
                                <td>Total Cost</td>
                                <td align="right"><? echo fn_number_format($tot_mkt_all_cost,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_mkt_all_cost/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_actual_all_cost,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_actual_all_cost/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_mkt_all_cost-$tot_actual_all_cost,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_19','<? echo $bgcolor1; ?>')" id="trtd_19">
                                <td align="center">18</td>
                                <td>Margin/Loss</td>
                                <td align="right"><? echo fn_number_format($tot_mkt_margin,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_mkt_margin/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_actual_margin,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_actual_margin/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_mkt_margin-$tot_actual_margin,2); ?></td>
                            </tr>
                        </table>
		</fieldset>
	</div>    
	<?
    exit();	
}
if($action=="yarn_cost_actual3")
{
	echo load_html_head_contents("Yarn Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);
	
	$supplier_array=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
	
	$sql_wo_fab="select a.id,a.item_category,a.booking_no,a.booking_type,a.is_short,b.job_no,b.po_break_down_id as po_id from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no  and a.item_category in(2,3) and a.booking_type in(1,3,4)  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and b.po_break_down_id in($po_id)";
	$result_fab_result=sql_select( $sql_wo_fab );
	foreach ($result_fab_result as $row)
	{
			$booking_array[$row[csf('booking_no')]]['btype']=$row[csf('booking_type')];
			$booking_array[$row[csf('booking_no')]]['is_short']=$row[csf('is_short')];
			$booking_type_arr[$row[csf('id')]]['btype']=$row[csf('booking_type')];
			$booking_type_arr[$row[csf('id')]]['is_short']=$row[csf('is_short')];
			$job_no=$row[csf('job_no')];
	}
	unset($result_fab_result);
	?>
	<script>

		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
		}	
		
	</script>	
		<div style="width:980px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
		<fieldset style="width:975px; margin-left:7px">
			<div id="report_container">
				<table class="rpt_table" width="975" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th colspan="10"><b>Yarn Issue</b></th>
					</thead>
					<thead>
						<th width="40">SL</th>
						<th width="110">Issue Id</th>
						<th width="80">Issue Date</th>
						<th width="80">Purpose</th>
						<th width="80">Supplier</th>
						<th width="80">Lot</th>
						<th width="180">Yarn Description</th>
						<th width="90">Issue Qty.</th>
						<th width="80">Avg. Rate (USD)</th>
						<th>Cost ($)</th>
					</thead>
					<?
				$job_array=return_library_array( "select id,job_no_mst from wo_po_break_down where id in($po_id)", "id", "job_no_mst"  );
				$sql_yarn="select c.id,a.requisition_no,a.receive_basis,a.prod_id,(a.cons_amount) as cons_amount,c.issue_basis,c.issue_number,c.booking_no from inv_transaction a, order_wise_pro_details b, inv_issue_master c where  a.id=b.trans_id and  c.id=a.mst_id and c.entry_form in(3) and b.entry_form in(3)  and  a.transaction_type=2  and b.po_breakdown_id in($po_id)";
				$result_yarn=sql_select($sql_yarn);
				foreach($result_yarn as $invRow)
				{
					if($invRow[csf('issue_basis')]==1)// Booking
					{
						$yarn_booking_no_arr[$invRow[csf('id')]]=$invRow[csf('booking_no')];
					}
					else if($invRow[csf('issue_basis')]==3)// Requesition
					{
						$yarn_booking_no_arr[$invRow[csf('id')]]=$invRow[csf('requisition_no')];
					}
				}
				
				unset($result_yarn);
				
				$yarn_dyeing_costArray=array();
				$yarndyeing_data="select b.booking_date,b.id,b.currency,b.is_short,b.ydw_no,a.job_no,a.yarn_color,a.product_id,avg(a.dyeing_charge) as dyeing_charge,  sum(a.amount) as amnt,sum(a.yarn_wo_qty) as yarn_wo_qty from wo_yarn_dyeing_mst b,wo_yarn_dyeing_dtls a where  b.id=a.mst_id and b.entry_form in(41,94) and a.status_active=1 and a.is_deleted=0 and a.job_no='$job_no'  group by b.id,b.currency,b.is_short,a.job_no,a.product_id,b.ydw_no,a.yarn_color,b.booking_date";
				$yarndyeing_dataResult=sql_select($yarndyeing_data);
				foreach($yarndyeing_dataResult as $yarnRow)
				{
					$yarn_dyeing_costArray[$yarnRow[csf('job_no')]]['avg_rate']=$yarnRow[csf('amnt')]/$yarnRow[csf('yarn_wo_qty')];
					$yarn_dyeing_isshort_arr[$yarnRow[csf('ydw_no')]]['is_short']=$yarnRow[csf('is_short')];
				}
					
				$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master where item_category_id=1", "id", "avg_rate_per_unit"  );
				$receive_array=array();
				$yarnIssueData="select b.prod_id,sum(a.cons_amount) as cons_amount,sum(a.cons_quantity) as cons_quantity
				from inv_transaction a, order_wise_pro_details b,product_details_master c where a.id=b.trans_id and c.id=b.prod_id and c.id=a.prod_id and a.item_category in(1) and a.transaction_type in(2,4,5,6) and b.entry_form ='3' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.entry_form in(3,11,25)   and b.po_breakdown_id in($po_id) group by  b.prod_id";
				$resultyarnIssueData = sql_select($yarnIssueData);
				$all_prod_ids="";
				foreach($resultyarnIssueData as $row)
				{
					if($all_prod_ids=="") $all_prod_ids=$row[csf('prod_id')];else $all_prod_ids.=",".$row[csf('prod_id')];
					
					$yarn_issue_amt_arr[$row[csf('prod_id')]]['amt']+=$row[csf('cons_amount')];
					$yarn_issue_amt_arr[$row[csf('prod_id')]]['qty']+=$row[csf('cons_quantity')];
				}
				unset($resultyarnIssueData);
				$prodIds=chop($all_prod_ids,',');
				$prod_cond_for_in="";
				$prod_ids=count(array_unique(explode(",",$all_prod_ids)));
				if($db_type==2 && $prod_ids>1000)
				{
					$prod_cond_for_in=" and (";
					$prodIdsArr=array_chunk(explode(",",$prodIds),999);
					foreach($prodIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$prod_cond_for_in.=" a.prod_id in($ids) or"; 
					}
					$prod_cond_for_in=chop($prod_cond_for_in,'or ');
					$prod_cond_for_in.=")";
				}
				else
				{
					$prodIds=implode(",",array_unique(explode(",",$all_prod_ids)));
					$prod_cond_for_in=" and a.prod_id in($prodIds)";
				}
				
				if($prodIds!='' || $prodIds!=0)
				{
					$prod_cond_for_in=$prod_cond_for_in;
				}
				else
				{
					$prod_cond_for_in="and a.prod_id in(0)";
				}
				
				$sql_receive_for_issue="select c.booking_id,a.job_no,a.prod_id,max(a.transaction_date) as transaction_date,c.currency_id,c.receive_purpose,c.exchange_rate,b.lot,b.color, sum(a.order_qnty) as qty, sum(a.order_amount) as amnt, sum(CASE WHEN c.receive_purpose in(2,15,38)   THEN a.order_amount ELSE 0 END) AS order_amount_recv from inv_transaction a,product_details_master b,inv_receive_master c where a.prod_id=b.id and c.id=a.mst_id and c.entry_form=1 and c.item_category=1 and a.transaction_type=1 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 $prod_cond_for_in  group by c.booking_id,a.job_no,a.prod_id,c.currency_id,c.receive_purpose,c.exchange_rate,b.lot,b.color";
				$resultReceive_chek = sql_select($sql_receive_for_issue);
				
				foreach($resultReceive_chek as $row)
				{
					$yarn_receive_arr[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('color')]]['purpose']=$row[csf('receive_purpose')];
					$receive_date_array[$row[csf('prod_id')]]['exchange_rate']=$row[csf('exchange_rate')];
					$avg_rate=$row[csf('amnt')]/$row[csf('qty')];
					$receive_array[$row[csf('prod_id')]]=$avg_rate;
				}
				unset($resultReceive_chek);
				
				
				
					$plan_details_array = return_library_array("select dtls_id, booking_no as booking_no from ppl_planning_entry_plan_dtls where po_id in($po_id)  group by dtls_id,booking_no", "dtls_id", "booking_no");
					//print_r($plan_details_array);
					$reqs_array = array();
					$reqs_sql = sql_select("select knit_id, requisition_no as reqs_no, sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 group by knit_id, requisition_no");
					foreach ($reqs_sql as $row)
					{
						//$reqs_array[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
						$reqs_array[$row[csf('reqs_no')]]['knit_id'] = $row[csf('knit_id')];
					}
					unset($reqs_sql);
					
					$i=1; $total_yarn_issue_qnty=0; $total_yarn_cost=0;
					$yarnTrimsData="SELECT a.mst_id, a.transaction_type, a.receive_basis, a.item_category, a.transaction_date, a.cons_rate, b.entry_form, b.po_breakdown_id, b.prod_id, b.issue_purpose, b.quantity, b.returnable_qnty, c.lot, c.color from inv_transaction a, order_wise_pro_details b,product_details_master c where a.id=b.trans_id and c.id=b.prod_id and c.id=a.prod_id and a.item_category in(1,4) and a.transaction_type in(2,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.entry_form in(3,11,25) and b.po_breakdown_id in($po_id) ";
						
					
				$yarnTrimsDataArray=sql_select($yarnTrimsData); $yarnTrimsCostArray=array(); $i=0;
				$usd_id=2;$tot_iss_amnt=0;
				foreach($yarnTrimsDataArray as $invRow)
				{
					if($invRow[csf('item_category')]==1)
					{
						$yarn_issue_amt=$yarn_issue_amt_arr[$invRow[csf('prod_id')]]['amt'];
						$yarn_issue_qty=$yarn_issue_amt_arr[$invRow[csf('prod_id')]]['qty'];
						$last_exchange_rate=$receive_date_array[$invRow[csf('prod_id')]]['exchange_rate'];
					//	$receive_date_array[$row[csf('prod_id')]]['exchange_rate']
						
						if($invRow[csf('receive_basis')]==1)//Booking Basis
						{
							$booking_no=$yarn_booking_no_arr[$invRow[csf('mst_id')]];
							if($invRow[csf('issue_purpose')]==2) //Yarn Dyeing purpose
							{
								$is_short=$yarn_dyeing_isshort_arr[$booking_no]['is_short'];
								$booking_type=1;
								//echo $booking_no.'= '.$booking_type.', ';
							}
							else
							{
								$booking_type=$booking_array[$booking_no]['btype'];
								$is_short=$booking_array[$booking_no]['is_short'];
								//echo $is_short.'= '.$booking_type.'A ';
							}
						}
						else if($invRow[csf('receive_basis')]==3) //Requisition Basis
						{
							$booking_req_no=$yarn_booking_no_arr[$invRow[csf('mst_id')]];
							
							$prog_no=$reqs_array[$booking_req_no]['knit_id'];
							$booking_no=$plan_details_array[$prog_no];
							//echo $prog_no.'req';
							$booking_type=$booking_array[$booking_no]['btype'];
							$is_short=$booking_array[$booking_no]['is_short'];
						}
						$transaction_date='';
						$transaction_date=$last_trans_date;
						if($db_type==0) $conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
						else $conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
						
					//	$exchange_rate=set_conversion_rate($usd_id,$conversion_date );
						$exchange_rate=$last_exchange_rate;
						$issue_rate=$yarn_issue_amt/$yarn_issue_qty;
						
						$avgrate=$issue_rate/$exchange_rate;
						$recv_purpose=$yarn_receive_arr[$invRow[csf('prod_id')]][$invRow[csf('lot')]][$invRow[csf('color')]]['purpose'];
						if($invRow[csf('entry_form')]==3 && $recv_purpose==16)//recv_purpose==16=Grey Yarn
						{
							//echo $is_short.'='.$booking_type.'='.$recv_purpose.'<br>';
							if(($booking_type==1 || $booking_type==4) && $is_short==2 &&  $booking_type!='') //Main and Sample Faric Booking
							{
			
								if($invRow[csf('issue_purpose')]==1 || $invRow[csf('issue_purpose')]==2 || $invRow[csf('issue_purpose')]==4)//Knitting||Yarn Dyeing||Sample With Order
								{
									//echo $invRow[csf('mst_id')].'='.$invRow[csf('issue_purpose')].'='.$q.'='.$invRow[csf('quantity')].'-k<br>';
									
									$iss_amnt=$invRow[csf('quantity')]*$avgrate;
									$tot_iss_amnt+=$iss_amnt;
									
									$retble_iss_amnt=$invRow[csf('returnable_qnty')]*$avgrate;
									$yarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['grey_yarn_amt']+=$iss_amnt;
									$yarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['yq']+=$invRow[csf('quantity')];
								}
							}
							
						}
					}
					
				}
			//	print_r($yarnTrimsCostArray);
				unset($yarnTrimsDataArray);
					$sql_isssue="select d.issue_number,d.issue_date,a.mst_id, a.transaction_type,a.item_category,a.requisition_no, a.receive_basis, a.item_category, a.transaction_date as issue_date, a.cons_rate, b.entry_form, b.po_breakdown_id, b.prod_id, b.issue_purpose, b.quantity as issue_qnty, b.returnable_qnty,c.supplier_id, c.lot, c.color,c.product_name_details from inv_transaction a, order_wise_pro_details b,product_details_master c,inv_issue_master d where a.mst_id=d.id and a.id=b.trans_id and c.id=b.prod_id and c.id=a.prod_id and a.item_category in(1,4) and a.transaction_type in(2,4,5,6) and d.status_active=1 and a.is_deleted=0  and a.status_active=1 and a.is_deleted=0and b.is_deleted=0 and b.status_active=1 and b.entry_form in(3,11,25) and b.issue_purpose in (1) and b.po_breakdown_id in($po_id) ";
					$result_issue=sql_select($sql_isssue);$usd_id=2;
					foreach($result_issue as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						if($row[csf('item_category')]==1)
						{
						$issue_purpose=$row[csf('issue_purpose')];
						$issue_basis=$row[csf('issue_basis')];
						$item_category=$row[csf('item_category')];
						$yarn_issue_amt=$yarn_issue_amt_arr[$row[csf('prod_id')]]['amt'];
						$yarn_issue_qty=$yarn_issue_amt_arr[$row[csf('prod_id')]]['qty'];
						
						$issue_rate=$yarn_issue_amt/$yarn_issue_qty;
						
						$last_trans_date=$last_receive_date_array[$row[csf('prod_id')]]['last_date'];
						$is_short='';
						$yarn_cost=0;
						
						if($row[csf('receive_basis')]==1)//Booking Basis
						{
							$booking_no=$yarn_booking_no_arr[$row[csf('mst_id')]];
							if($row[csf('issue_purpose')]==2) //Yarn Dyeing purpose
							{
								$is_short=$yarn_dyeing_isshort_arr[$booking_no]['is_short'];
								$booking_type=1;
								//echo $is_short.'= '.$booking_type.', ';
							}
							else
							{
								$booking_type=$booking_array[$booking_no]['btype'];
								$is_short=$booking_array[$booking_no]['is_short'];
							}
						}
						else if($row[csf('receive_basis')]==3) //Requisition Basis
						{
							$booking_req_no=$yarn_booking_no_arr[$row[csf('mst_id')]];
							
							$prog_no=$reqs_array[$booking_req_no]['knit_id'];
							$booking_no=$plan_details_array[$prog_no];
							//echo $prog_no.'req';
							$booking_type=$booking_array[$booking_no]['btype'];
							$is_short=$booking_array[$booking_no]['is_short'];
						}
							
							//echo $booking_type.'='.$is_short;
							//if(($booking_type==1 || $booking_type==4) &&  $is_short==2 &&  $booking_type!='') //Main and Sample Faric Booking
							
						$yarn_issued=$row[csf('issue_qnty')]; 
						$issue_date=$row[csf('issue_date')];
						$issue_qty=$row[csf('issue_qnty')];
							//echo $row[csf('yarn_iss_return_qty')].'ff';
							$transaction_date=$last_trans_date;
							//$avg_rate='';
							if($db_type==0)
							{
								$conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
							}
							else
							{
								$conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
							}
							//$currency_rate=set_conversion_rate($usd_id,$conversion_date );
							$currency_rate=$receive_date_array[$row[csf('prod_id')]]['exchange_rate'];
							$avg_rate=$issue_rate/$currency_rate;
							//echo $currency_rate.'dd'.$avg_rate;
							if(($booking_type==1 || $booking_type==4) && ($is_short==2 || $is_short==1 &&  $booking_type!='') ) //Main and Sample Faric Booking
							{
								$issue_amnt=$issue_qty*$avg_rate;
								
							}
							//echo $issue_purpose.'ff';
							if($issue_purpose==1 || $issue_purpose==4) //Knitting || Sample With Order
							{
								$recv_purpose=$yarn_receive_arr[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('color')]]['purpose'];
								//echo $recv_purpose.'R';
								if($recv_purpose==16) ////Grey Yarn
								{
								//	$yarn_cost=$issue_amnt;
									//$yarn_issued=$issue_qty;
								//	echo "A".$issue_amnt.'<br>';
								}
								
							}
							else if($issue_purpose==2) //Yarn Dyeing
							{
								$recv_purpose=$yarn_receive_arr[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('color')]]['purpose'];
								
								if($recv_purpose==16) ////Grey Yarn
								{
								}
							}
						}
						//echo $row[csf('issue_number')]."__".$booking_type.'__'.$is_short."__".$recv_purpose."__".$issue_purpose."<br>";
						
						if(($booking_type==1 || $booking_type==4) && (($is_short==2 || $is_short==1 ) &&  $booking_type!='') && ($recv_purpose==16 || $issue_purpose==2))
						{
							//echo $avg_rate.'a';
						$yarn_cost=$yarnTrimsCostArray[$row[csf('mst_id')]][$row[csf('prod_id')]]['grey_yarn_amt'];
						$yarn_issued=$yarnTrimsCostArray[$row[csf('mst_id')]][$row[csf('prod_id')]]['yq'];
					//	echo $yarn_cost;
							//$yarn_issued=$yarnTrimsCostArray[$row[csf('prod_id')]]['yq'];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="40"><? echo $i; ?></td>
							<td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
							<td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
							<td width="80" title="<?= $issue_purpose ?>"><p><? echo $yarn_issue_purpose[$issue_purpose]; ?></p></td>
							<td width="80"><p><? echo $supplier_array[$row[csf('supplier_id')]]; ?></p></td>
							<td width="80"><p><? echo $row[csf('lot')]; ?></p></td>
							<td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
							<td align="right" width="90">
								<? 
									echo fn_number_format($yarn_issued,2); 
									$total_yarn_issue_qnty+=$yarn_issued;
								?>
							</td>
							<td align="right" width="80">
								<? 
									
									echo fn_number_format($avg_rate,2); 
								?>&nbsp;
							</td>
							<td align="right">
								<?
									//$yarn_cost=$yarn_issued*$avg_rate;
									echo fn_number_format($yarn_cost,4); 
									$total_yarn_cost+=$yarn_cost;
								?>
							</td>
						</tr>
					<?
					$i++;
							}
							
							
					}
					?>
					<tr style="font-weight:bold">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right">Total</td>
						<td align="right"><? echo fn_number_format($total_yarn_issue_qnty,2);?></td>
						<td>&nbsp;</td>
						<td align="right"><? echo fn_number_format($total_yarn_cost,2);?></td>
					</tr>
					<thead>
						<th colspan="10"><b>Yarn Return</b></th>
					</thead>
					<thead>
						<th width="40">SL</th>
						<th width="110">Return Id</th>
						<th width="80">Return Date</th>
						<th width="80">Purpose</th>
						<th width="80">Supplier</th>
						<th width="80">Lot</th>
						<th width="180">Yarn Description</th>
						<th width="90">Return Qty.</th>
						<th width="80">Avg. Rate (USD)</th>
						<th>Cost ($)</th>
				</thead>
					<?
					$total_yarn_return_qnty=0; $total_yarn_return_cost=0;
					$sql="select a.booking_no,a.recv_number,a.receive_basis, a.receive_date,b.issue_purpose, sum(b.quantity+b.reject_qty) as returned_qnty, c.id as prod_id, c.supplier_id,c.color,c.lot, c.product_name_details, c.avg_rate_per_unit,d.requisition_no from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and c.item_category_id=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in($po_id) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose in (1)  group by a.id, c.id, a.recv_number, a.booking_no,a.receive_date,b.issue_purpose, c.supplier_id,c.color,c.lot, c.product_name_details,a.receive_basis, c.avg_rate_per_unit,d.requisition_no";
					//echo $sql; die;
					$result=sql_select($sql);
					foreach($result as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
					
						$issue_basis=$row[csf('receive_basis')];
						$issue_purpose=$row[csf('issue_purpose')];
						$item_category=$row[csf('item_category')];
						if($issue_basis==1) //Booking Basis
							{
								$booking_no=$row[csf('booking_no')];
							}
							else if($issue_basis==3) //Requisition Basis
							{
								$booking_req_no=$row[csf('booking_no')];
								
								$prog_no=$reqs_array[$booking_req_no]['knit_id'];
								$booking_no=$plan_details_array[$prog_no];
								//echo $prog_no.'req';
							}
							$booking_type=$booking_array[$booking_no]['btype'];
							$is_short=$booking_array[$booking_no]['is_short'];
							//echo $issue_basis.'dsd';
							$transaction_date=$row[csf('receive_date')];
							if($db_type==0)
							{
								$conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
							}
							else
							{
								$conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
							}
							$currency_rate=$receive_date_array[$row[csf('prod_id')]]['exchange_rate'];
							//$currency_rate=set_conversion_rate($usd_id,$conversion_date );
							
							$currency_id=$receive_curr_array[$row[csf('prod_id')]];
							if($receive_array[$row[csf('prod_id')]]>0)
							{
								
								//echo $currency_id.'D';
								if($currency_id==1) //Taka
								{
									$avg_rate=$receive_array[$row[csf('prod_id')]]/$currency_rate;
									//echo $avg_rate.'C';
								}
								else
								{
									$avg_rate=$receive_array[$row[csf('prod_id')]];	
									//echo $avg_rate.'B';
								}
							}
							else
							{
								$avg_rate=$avg_rate_array[$row[csf('prod_id')]]/$currency_rate;
								//echo $avg_rate.'A';
							}
							//echo $row[csf('prod_id')].'--'.$row[csf('lot')].'--'.$row[csf('color')].'<br>';
							$recv_purpose=$yarn_receive_arr[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('color')]]['purpose'];
							//echo $recv_purpose.', ';
						if($issue_purpose==1 || $issue_purpose==4)
						{
							if($recv_purpose==16)
							{
								//echo $row[csf('returned_qnty')].'--'.$avg_rate; die;
								$yarn_returned=$row[csf('returned_qnty')];
								$iss_ret_amnt=$row[csf('returned_qnty')]*$avg_rate;
								//echo $yarn_returned.'d'.$avg_rate;
							}
						}
						else if($issue_purpose==2) //Yarn Dyeing
						{
							if($recv_purpose==16)
							{
								$yarn_returned=$row[csf('returned_qnty')];
								$iss_ret_amnt=$row[csf('returned_qnty')]*$avg_rate;
								//echo $yarn_returned.'A';
							}
						}
						/* if(($booking_type==1 || $booking_type==4) &&  $is_short==2 &&  $booking_type!='' && $recv_purpose==16) //Main and Sample Faric Booking
							{ */
								$total_yarn_return_qnty+=$yarn_returned;
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="40"><? echo $i; ?></td>
							<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
							<td width="80"><p><? echo $yarn_issue_purpose[$issue_purpose]; ?></p></td>
							<td width="80"><p><? echo $supplier_array[$row[csf('supplier_id')]]; ?></p></td>
							<td width="80"><p><? echo $row[csf('lot')]; ?></p></td>
						
							<td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
							<td align="right" width="90">
								<? 
									echo fn_number_format($yarn_returned,2); 
								?>
							</td>
							<td align="right" width="80">
								<? 
									echo fn_number_format($avg_rate,2); 
								?>&nbsp;
							</td>
							<td align="right">
								<?
									$yarn_return_cost=$iss_ret_amnt;
									echo fn_number_format($yarn_return_cost,2); 
									$total_yarn_return_cost+=$yarn_return_cost;
								?>
							</td>
						</tr>
					<?
						$i++;
							//}
					}
					?>
					<tr style="font-weight:bold">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right">Total</td>
						<td align="right"><? echo fn_number_format($total_yarn_return_qnty,2);?></td>
						<td>&nbsp;</td>
						<td align="right"><? echo fn_number_format($total_yarn_return_cost,2);?></td>
					</tr>
				
					<tfoot>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right">Balance</th>
						<th align="right"><? echo fn_number_format(($total_yarn_issue_qnty+$total_trans_in_qnty)-($total_yarn_return_qnty+$total_trans_out_qnty),2); ?></th>
						<th>&nbsp;</th>
						<th align="right"><? echo fn_number_format(($total_yarn_cost+$total_trans_in_cost)-($total_yarn_return_cost+$total_trans_out_cost),2); ?></th>
					</tfoot>
				</table>	
			</div>
		</fieldset>  
	<?
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );

	$company_name=str_replace("'","",$cbo_company_name);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$report_type=str_replace("'","",$report);
	
	if($txt_ref_no!='') $ref_cond="and b.grouping='$txt_ref_no'";else $ref_cond="";
	if($txt_file_no!='') $file_cond="and b.file_no=$txt_file_no";else $file_cond="";
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if(trim($txt_job_no)!="") $job_no=trim($txt_job_no); else $job_no="%%";
	
	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";
	}
	else $year_cond="";
	
	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$date_cond=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
	}
	$ex_date_cond='';
	if(str_replace("'","",$txt_ex_date_from)!="" && str_replace("'","",$txt_ex_date_to)!="")
	{
		$ex_date_cond=" and d.ex_factory_date between $txt_ex_date_from and $txt_ex_date_to";
	}
	$po_id_cond="";
	if(trim(str_replace("'","",$txt_order_no))!="")
	{
		if(str_replace("'","",$hide_order_id)!="") $po_id_cond=" and b.id in(".str_replace("'","",$hide_order_id).")";
		else $po_id_cond=" and LOWER(b.po_number) like LOWER('%".trim(str_replace("'","",$txt_order_no))."%')";
	}
	
	$shipping_status_cond='';
	if(str_replace("'","",$shipping_status)!=0) $shipping_status_cond=" and b.shiping_status=$shipping_status";
	
	$po_ids_array=array();
		
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	//echo $ex_date_cond;
	if($ex_date_cond=="" ){
		$sql="SELECT a.id as job_id,a.job_no_prefix_num, a.job_no, $year_field,a.job_quantity, a.avg_unit_price, a.total_price, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.set_smv, b.id, b.po_number,b.grouping,b.file_no, b.pub_shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status,c.exchange_rate from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_pre_cost_mst c on a.id=c.job_id where a.company_name='$company_name' and a.job_no_prefix_num like '$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $date_cond $buyer_id_cond $po_id_cond $ref_cond $file_cond $shipping_status_cond $year_cond";
	}else {
		$sql="SELECT a.id as job_id,a.job_no_prefix_num, a.job_no, $year_field,a.job_quantity,   a.avg_unit_price, a.total_price, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.set_smv, b.id, b.po_number,b.grouping,b.file_no, b.pub_shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status,c.exchange_rate from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_pre_cost_mst c on a.id=c.job_id join pro_ex_factory_mst d on b.id=d.po_break_down_id where a.company_name='$company_name' and a.job_no_prefix_num like '$job_no'   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $ex_date_cond $date_cond $buyer_id_cond $po_id_cond $ref_cond $file_cond $shipping_status_cond $year_cond group by  a.id,a.job_no_prefix_num, a.job_no, a.insert_date,a.job_quantity, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty, a.set_smv, b.id, b.po_number,b.grouping,b.file_no, b.pub_shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status,c.exchange_rate, a.avg_unit_price, a.total_price order by b.pub_shipment_date, a.job_no_prefix_num, b.id";

	}
	//echo $sql; die;
	$get_main_data=sql_select($sql);
	$po_id_arr=array(); $job_id_arr=array();
	foreach($get_main_data as $row)
	{		
		$job_id_arr[$row[csf("job_id")]]=$row[csf("job_id")];
		$job_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
		$job_arr2[$row[csf("job_no")]]['po_quantity']=$row[csf("po_quantity")];
		$job_arr2[$row[csf("job_no")]]['job_quantity']=$row[csf("job_quantity")];
		$job_exchange_rateArr[$row[csf("job_id")]]['exchange_rate']=$row[csf("exchange_rate")];
		$shipment_date_arr[$row[csf("job_id")]][]=$row[csf('pub_shipment_date')];
	}
	
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_name." and ENTRY_FORM=144");
	oci_commit($con);

	fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 144, 1, $job_id_arr, $empty_arr);
	$select_all_po=sql_select("SELECT a.id as po_id, a.shiping_status, a.job_id  from wo_po_break_down a join gbl_temp_engine b on a.job_id=b.ref_val where b.ref_from=1 and b.entry_form=144 and b.user_id=$user_name");
	foreach($select_all_po as $row){
		//$po_id_arr[$row[csf("po_id")]]=$row[csf("po_id")];
		$po_wise_shipment_arr[$row[csf('job_id')]][$row[csf("po_id")]]=$row[csf("shiping_status")];
	}
	/* echo '<pre>';
	print_r($po_wise_shipment_arr); die; */
	$full_shipment_job =array();
	foreach($po_wise_shipment_arr as $job_id => $approvals){

		$appNum = count($approvals);
		$aprovalCountArr = array_count_values($approvals);

		if(empty($aprovalCountArr[3])){
			$status = 'Full Pending';
		}
		elseif(!empty($aprovalCountArr[3]) && ($aprovalCountArr[3] == $appNum)){
			$status = 'fully shipment'; 
			$full_shipment_job[$job_id] = $job_id ;
		}
		elseif(!empty($aprovalCountArr[3]) && ($aprovalCountArr[3] < $appNum)){
			$status = 'partially shipment'; 
		}		
	}
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_name." and ENTRY_FORM=144");
	oci_commit($con);
	
	fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 144, 1, $full_shipment_job, $empty_arr);

	$main_data=sql_select("SELECT a.id as job_id,a.job_no_prefix_num, a.job_no, $year_field,a.job_quantity, a.avg_unit_price, a.total_price, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.set_smv, b.id, b.po_number,b.grouping,b.file_no, b.pub_shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status,c.exchange_rate from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_pre_cost_mst c on a.id=c.job_id join gbl_temp_engine d on c.job_id=d.ref_val where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.ref_from=1 and d.entry_form=144 and d.user_id=$user_name");


	$select_all_po=sql_select("SELECT a.id as po_id, a.shiping_status, a.job_id  from wo_po_break_down a join gbl_temp_engine b on a.job_id=b.ref_val where b.ref_from=1 and b.entry_form=144 and b.user_id=$user_name");
	foreach($select_all_po as $row){
		$po_id_arr[$row[csf("po_id")]]=$row[csf("po_id")];
	}

	fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 144, 2, $po_id_arr, $empty_arr);
	if(count($po_id_arr)==0) 
	{ 
		echo "<div style='width:1000px;font-size:larger'; align='center'><font color='#FF0000'>No Data Found</font></div>"; die;
	}
	foreach($shipment_date_arr as $jobid=>$ship_date_arr){
		for ($i =0; $i <count($ship_date_arr); $i++)
		{
			if ($i == 0)
			{
				$max_date[$jobid] = date("d-M-y", strtotime($ship_date_arr[$i]));
			}
			else if ($i != 0)
			{
				$new_date = date("d-M-y", strtotime($ship_date_arr[$i]));
				if ($new_date > $max_date[$jobid])
				{
					$max_date[$jobid] = $new_date;
				}
			}
		}
	}
	$fabriccostDataArray=sql_select("select a.job_no, a.costing_per_id, a.embel_cost, a.wash_cost, a.cm_cost, a.commission, a.currier_pre_cost, a.lab_test, a.inspection, a.freight, a.comm_cost from wo_pre_cost_dtls a, gbl_temp_engine b where a.status_active=1 and a.is_deleted=0 and a.job_id=b.ref_val and b.ref_from=1 and b.entry_form=144 and b.user_id=$user_name");
	foreach($fabriccostDataArray as $fabRow)
	{
		$fabriccostArray[$fabRow[csf('job_no')]]['costing_per_id']=$fabRow[csf('costing_per_id')];
		$fabriccostArray[$fabRow[csf('job_no')]]['embel_cost']=$fabRow[csf('embel_cost')];
		$fabriccostArray[$fabRow[csf('job_no')]]['wash_cost']=$fabRow[csf('wash_cost')];
		$fabriccostArray[$fabRow[csf('job_no')]]['cm_cost']=$fabRow[csf('cm_cost')];
		$fabriccostArray[$fabRow[csf('job_no')]]['commission']=$fabRow[csf('commission')];
		$fabriccostArray[$fabRow[csf('job_no')]]['currier_pre_cost']=$fabRow[csf('currier_pre_cost')];
		$fabriccostArray[$fabRow[csf('job_no')]]['lab_test']=$fabRow[csf('lab_test')];
		$fabriccostArray[$fabRow[csf('job_no')]]['inspection']=$fabRow[csf('inspection')];
		$fabriccostArray[$fabRow[csf('job_no')]]['freight']=$fabRow[csf('freight')];
		$fabriccostArray[$fabRow[csf('job_no')]]['comm_cost']=$fabRow[csf('comm_cost')];
	}
	//Data From Class
	$condition= new condition();
	$condition->company_name("=$company_name");
	if(str_replace("'","",$cbo_buyer_name)>0){
		$condition->buyer_name("=$cbo_buyer_name");
	}
	if(str_replace("'","",$txt_file_no) !=''){
		$condition->file_no("='$txt_file_no'");
	}
	if(str_replace("'","",$txt_ref_no) !=''){
		$condition->grouping("='$txt_ref_no'");
	}
	
	if(str_replace("'","",$txt_job_no) !=''){
		$condition->job_no_prefix_num("=$txt_job_no");
	}
	if(trim(str_replace("'","",$txt_order_no))!='')
	{ 
		$condition->po_number("='".trim(str_replace("'","",$txt_order_no))."'"); 
	}
	if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
	{
		$start_date=str_replace("'","",$txt_date_from);
		$end_date=str_replace("'","",$txt_date_to);
		$condition->pub_shipment_date(" between '$start_date' and '$end_date'");
	}
	if(count($po_id_arr)>0){
		$po_id_str=implode(",",$po_id_arr);
		$condition->po_id_in($po_id_str); 
	}
	
	
	$condition->init();

	$yarn= new yarn($condition);
	$yarn_costing_arr=$yarn->getJobWiseYarnAmountArray();
	$yarn_costing_qty_arr=$yarn->getJobWiseYarnQtyArray();
	$conversion= new conversion($condition);
	$conversion_costing_arr_process=$conversion->getAmountArray_by_jobAndProcess(); 
	$conversion_costing_arr_process_qty=$conversion->getQtyArray_by_jobAndProcess();
	$trims= new trims($condition);
	$trims_costing_arr=$trims->getAmountArray_by_job();
	$emblishment= new emblishment($condition);
	$emblishment_costing_arr_name=$emblishment->getAmountArray_by_jobAndEmbname();
	$emblishment_costing_arr_name_qty=$emblishment->getQtyArray_by_jobAndEmbname();
	$commission= new commision($condition);
	$commission_costing_arr=$commission->getAmountArray_by_jobAndItemid();
	$commercial= new commercial($condition);
	$commercial_costing_arr=$commercial->getAmountArray_by_job();
	$other= new other($condition);
	$other_costing_arr=$other->getAmountArray_by_job();
	$wash= new wash($condition);
	$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_jobAndEmbname();
	$emblishment_costing_arr_name_wash_qty=$wash->getQtyArray_by_jobAndEmbname();
	$fabric= new fabric($condition);
	$fabric_costing_arr=$fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
	/* echo '<pre>';
	print_r($other_costing_arr); die; */

	//Actual Cost data from Here
	$ex_factory_data=sql_select("SELECT c.total_set_qnty, a.job_id, a.unit_price, a.id as po_id, sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as qnty from wo_po_details_master c join wo_po_break_down a on c.id=a.job_id join pro_ex_factory_mst b on a.id=b.po_break_down_id join  gbl_temp_engine c on a.job_id=c.ref_val where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.ref_from=1 and c.entry_form=144 and c.user_id=$user_name group by a.job_id, a.unit_price, a.id, c.total_set_qnty");
	foreach($ex_factory_data as $row){
		$qty=$row[csf('qnty')]/$row[csf('total_set_qnty')];
		$ex_factory_arr[$row[csf('job_id')]]['qty']+=$qty;
		$ex_factory_arr[$row[csf('job_id')]]['value']+=$qty*$row[csf('unit_price')];
	}
	
	$aop_amt_array=array();
	$sql_wo_aop="SELECT a.id,a.booking_date,a.currency_id,a.item_category,a.booking_no,a.booking_type,a.is_short,b.po_break_down_id as po_id, b.job_no, (CASE WHEN a.item_category=12 and a.booking_type ='3'  THEN b.wo_qnty ELSE 0 END) AS wo_qnty, (CASE WHEN a.item_category=12 and a.booking_type ='3'  THEN b.amount ELSE 0 END) AS amount from wo_booking_mst a,wo_booking_dtls b, gbl_temp_engine c where a.booking_no=b.booking_no  and a.item_category in(2,3,12) and a.booking_type in(1,3,4)  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id=c.ref_val and c.ref_from=2 and c.entry_form=144 and c.user_id=$user_name";
	$result_aop_rate=sql_select( $sql_wo_aop );
	foreach ($result_aop_rate as $row)
	{				
		if($row[csf('item_category')]==12)
		{
			$wo_qnty=$row[csf('wo_qnty')];
			$amount=$row[csf('amount')];
			$avg_wo_aop_rate=$amount/$wo_qnty;
			$aop_prod_array[$row[csf('job_no')]]['aop_rate']=$avg_wo_aop_rate;
			$aop_prod_array[$row[csf('job_no')]]['currency_id']=$row[csf('currency_id')];
			$aop_prod_array[$row[csf('job_no')]]['booking_date']=$row[csf('booking_date')];
			$aop_prod_array[$row[csf('job_no')]]['amount']+=$row[csf('amount')];
		}
		else
		{
			$booking_array[$row[csf('booking_no')]]['btype']=$row[csf('booking_type')];
			$booking_array[$row[csf('booking_no')]]['is_short']=$row[csf('is_short')];
			$booking_type_arr[$row[csf('id')]]['btype']=$row[csf('booking_type')];
			$booking_type_arr[$row[csf('id')]]['is_short']=$row[csf('is_short')];
			$aop_amt_array[$row[csf('job_no')]]['amount']+=$aop_amt;
		}
	}
	$yarndyeing_sql="SELECT b.booking_date,b.id,b.currency,d.is_short,b.ydw_no, a.job_no_id, a.job_no,a.yarn_color,a.product_id, b.entry_form, avg(a.dyeing_charge) as dyeing_charge, sum(a.amount) as amnt,sum(a.yarn_wo_qty) as yarn_wo_qty from wo_yarn_dyeing_mst b,wo_yarn_dyeing_dtls a, gbl_temp_engine c, wo_booking_mst d where b.id=a.mst_id and b.entry_form in(41,94) and a.fab_booking_no=d.booking_no and a.status_active=1 and a.is_deleted=0 and a.job_no_id=c.ref_val and c.ref_from=1 and c.entry_form=144 and c.user_id=$user_name group by b.id,b.currency,d.is_short,a.job_no,a.product_id,b.ydw_no, a.yarn_color,b.booking_date, a.job_no_id, b.entry_form";
	//echo $yarndyeing_sql; die;
	$yarndyeing_result = sql_select($yarndyeing_sql);
	foreach($yarndyeing_result as $yarnRow)
	{
		$yarn_dyeing_costArray[$yarnRow[csf('job_no')]]['avg_rate']=$yarnRow[csf('amnt')]/$yarnRow[csf('yarn_wo_qty')];
		$yarn_dyeing_costArray2[$yarnRow[csf('id')]]['job']=$yarnRow[csf('job_no')];
		$yarn_dyeing_costArray2[$yarnRow[csf('id')]]['job_id']=$yarnRow[csf('job_no_id')];
		$yarn_dyeing_costArray2[$yarnRow[csf('id')]]['ydw_no']=$yarnRow[csf('ydw_no')];
		$yarn_dyeing_isshort_arr[$yarnRow[csf('ydw_no')]]['is_short']=$yarnRow[csf('is_short')];
		$yarn_dyeing_isshort_arr[$yarnRow[csf('ydw_no')]]['booking_date']=$yarnRow[csf('booking_date')];
		$yarn_dyeing_curr_arr[$yarnRow[csf('ydw_no')]]['currency']=$yarnRow[csf('currency')];
		if($yarnRow[csf('entry_form')]==41){
			$yarn_dyeing_rate_arr[$yarnRow[csf('job_no')]][$yarnRow[csf('yarn_color')]]['rate']=$yarnRow[csf('dyeing_charge')];
		}
		else{
			$yarn_service_rate_arr[$yarnRow[csf('job_no')]]['rate']=$yarnRow[csf('dyeing_charge')];
		}		
		$yarn_dyeing_mst_idArr[$yarnRow[csf('id')]]=$yarnRow[csf('id')];
	}
	$usd_arr = array();
    $sqlSelectData = sql_select("select con_date,conversion_rate from currency_conversion_rate where currency=2 and is_deleted=0 order by con_date desc");
    foreach ($sqlSelectData as $row) 
    {
        $usd_arr[date('d-m-Y',strtotime($row[csf('con_date')]))] = $row[csf('conversion_rate')];
    }

	$sql_issue_data=sql_select("SELECT a.trans_id, c.id as job_id, c.job_no, c.buyer_name as buyer_name, c.style_ref_no as style_ref_no, d.brand_id as brand_id, e.company_id as company_id, e.booking_id as booking_id, e.booking_no as booking_no,d.supplier_id, e.issue_purpose,e.id as issue_id, e.issue_number, d.cons_quantity as issue_qnty, d.cons_rate, f.brand,f.id as product_id,f.lot,f.yarn_type, f.yarn_count_id, f.yarn_comp_type1st, f.yarn_comp_percent1st,e.issue_date,e.challan_no from order_wise_pro_details a, wo_po_details_master c,inv_transaction d,inv_issue_master e, product_details_master f, gbl_temp_engine g  where a.trans_id=d.id and c.job_no=e.buyer_job_no and d.mst_id=e.id and d.prod_id=f.id and d.item_category=1 and a.trans_type=2 and e.entry_form=3 and a.entry_form=3 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.issue_basis=1 and e.issue_purpose in(1,2) and g.ref_val=c.id and g.ref_from=1 and g.entry_form=144 and g.user_id=$user_name");
	foreach($sql_issue_data as $row){
		$issue_id_arr[$row[csf('Issue_id')]]=$row[csf('Issue_id')];		
	}
	
	fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 144, 8, $issue_id_arr, $empty_arr);

	$issue_return_res = sql_select("SELECT a.recv_number,a.booking_no, b.cons_quantity, b.issue_id,b.id as trans_id,d.brand,d.id as product_id,d.lot,d.yarn_type, d.yarn_count_id, d.yarn_comp_type1st,d.yarn_comp_percent1st from inv_receive_master a, inv_transaction b, inv_issue_master c,product_details_master d, gbl_temp_engine e where a.id = b.mst_id and a.issue_id = c.id and b.transaction_type = 4 and b.item_category = 1 and b.prod_id=d.id and a.entry_form = 9 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and e.ref_val=c.id and e.ref_from=8 and e.entry_form=144 and e.user_id=$user_name");
	$transIdChk = array(); $short_transIdChk=array();
	foreach ($issue_return_res as $val) 
	{
		$compPercent = $val[csf('yarn_comp_percent1st')];
		$yanrType = $val[csf('yarn_type')];
		$yarnComposition = $val[csf('yarn_comp_type1st')];
		$yarnCount = $val[csf('yarn_count_id')];
		$prod_id=$val[csf('product_id')];
		$issue_id=$val[csf('issue_id')];
		$booking_no=$val[csf('booking_no')];
		$booking_type=$booking_array[$booking_no]['btype'];
		$is_short=$booking_array[$booking_no]['is_short'];
		if($transIdChk[$val[csf("trans_id")]]=="")
		{					
			if(($booking_type==1 || $booking_type==4) && $is_short==2){
				$transIdChk[$val[csf("trans_id")]] = $val[csf("trans_id")];	
				$groupKey = $yarnCount."**".$yarnComposition."**".$compPercent."**".$yanrType."**".$prod_id."**".$issue_id;
				$issue_return_qnty_arr[$groupKey] += $val[csf("cons_quantity")];
			}			
		}
		if($short_transIdChk[$val[csf("trans_id")]]=="")
		{			
			if(($booking_type==1 || $booking_type==4) && $is_short==1){
				$short_transIdChk[$val[csf("trans_id")]] = $val[csf("trans_id")];
				$groupKey = $yarnCount."**".$yarnComposition."**".$compPercent."**".$yanrType."**".$prod_id."**".$issue_id;
				$short_issue_return_qnty_arr[$groupKey] += $val[csf("cons_quantity")];
			}
		}         
	}

	$transissueIdChk=array(); $short_transissueIdChk=array();
	foreach($sql_issue_data as $row){
		$compPercent = $row[csf('yarn_comp_percent1st')];
		$yanrType = $row[csf('yarn_type')];
		$yarnComposition = $row[csf('yarn_comp_type1st')];
		$yarnCount = $row[csf('yarn_count_id')];
		$product_id=$row[csf('product_id')];
		$issue_id = $row[csf('issue_id')];

		$booking_no=$row[csf('booking_no')];
		$booking_type=$booking_array[$booking_no]['btype'];
		$is_short=$booking_array[$booking_no]['is_short'];

		$groupKey = $yarnCount."**".$yarnComposition."**".$compPercent."**".$yanrType."**".$product_id."**".$issue_id;
		$exchangeRate=$usd_arr[date('d-m-Y',strtotime($row[csf('issue_date')]))];
		if($exchangeRate =="")
		{
			foreach ($usd_arr as $rate_date => $rat) 
			{
				if(strtotime($rate_date) <= strtotime($row[csf('issue_date')]))
				{
					$rate_date = date('d-m-Y',strtotime($rate_date));
					$exchangeRate=$rat;
					break;
				}
			}
		} 
		if($transissueIdChk[$row[csf("trans_id")]]=="")
		{
			if(($booking_type==1 || $booking_type==4) && $is_short==2){
				$transissueIdChk[$row[csf("trans_id")]] = $row[csf("trans_id")];
				$yarn_cost_data[$row[csf('job_id')]][$groupKey]['issue_qnty']+= $row[csf('issue_qnty')];
				$yarn_cost_data[$row[csf('job_id')]][$groupKey]['exchangeRate']= $exchangeRate;
				$yarn_cost_data[$row[csf('job_id')]][$groupKey]['cons_rate']= $row[csf('cons_rate')];
			}						
		}
		if($short_transissueIdChk[$row[csf("trans_id")]]=="")
		{
			if(($booking_type==1 || $booking_type==4) && $is_short==1){
				$short_transissueIdChk[$row[csf("trans_id")]] = $row[csf("trans_id")];
				$short_yarn_cost_data[$row[csf('job_id')]][$groupKey]['issue_qnty']+= $row[csf('issue_qnty')];
				$short_yarn_cost_data[$row[csf('job_id')]][$groupKey]['exchangeRate']= $exchangeRate;
				$short_yarn_cost_data[$row[csf('job_id')]][$groupKey]['cons_rate']= $row[csf('cons_rate')];
			}
		}
		
	}

	foreach($yarn_cost_data as $job_id=>$groupdata){
		foreach($groupdata as $group_id=>$value){
			$issue_return_qnty=$issue_return_qnty_arr[$group_id];
			$amount =  ( ($value['issue_qnty']-$issue_return_qnty) * number_format($value['cons_rate']/$value['exchangeRate'], 2) );
			$yarn_cost_value[$job_id]+=$amount;
		}
	}
	foreach($short_yarn_cost_data as $job_id=>$groupdata){
		foreach($groupdata as $group_id=>$value){
			$issue_return_qnty=$short_issue_return_qnty_arr[$group_id];
			$amount =  ( ($value['issue_qnty']-$issue_return_qnty) * number_format($value['cons_rate']/$value['exchangeRate'], 2) );
			$short_yarn_cost_value[$job_id]+=$amount;
		}
	}
	/* echo '<pre>';
	print_r($short_yarn_cost_value); die; */
	fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 144, 5, $yarn_dyeing_mst_idArr, $empty_arr);	

	$sql_issue="SELECT c.id as issue_id, c.issue_number, c.issue_purpose,c.booking_id,a.job_no,a.prod_id, e.is_short,max(a.transaction_date) as transaction_date,c.issue_purpose, b.lot,a.dyeing_color_id as color, sum(case when c.issue_purpose in(2,15,38) then a.cons_quantity else 0 end) as cons_quantity from inv_transaction a,product_details_master b,inv_issue_master c, wo_yarn_dyeing_mst e, gbl_temp_engine d where a.prod_id=b.id and c.id=a.mst_id and c.entry_form=3 and c.item_category=1 and a.transaction_type=2 and a.item_category=1 and a.status_active=1 and e.id=c.booking_id and c.booking_id=d.ref_val and d.ref_from=5 and d.entry_form=144 and d.user_id=$user_name and a.is_deleted=0 group by c.id,c.issue_number,c.booking_id,a.job_no,a.prod_id, c.issue_purpose,b.lot,a.dyeing_color_id, e.is_short";
	//echo $sql_issue; die;
	$resultyarnissue = sql_select($sql_issue);
	$all_recv_prod_ids=array();
	foreach($resultyarnissue as $row)
	{
		$yarnissue_prodid[$row[csf('issue_id')]]=$row[csf('issue_id')];
		$job_id=$yarn_dyeing_costArray2[$row[csf('booking_id')]]['job_id'];
		$jobNo=$yarn_dyeing_costArray2[$row[csf('booking_id')]]['job'];
		$yarn_issue_wise_job[$row[csf('issue_id')]]=$job_id;
		if($row[csf('issue_purpose')]==2){
			$dyeing_rate=$yarn_dyeing_rate_arr[$jobNo][$row[csf('color')]]['rate'];
			$yarn_issue_amount_arr[$job_id]['amount']+=$dyeing_rate*$row[csf('cons_quantity')];
			$product_wise_yarn_rate[$row[csf('issue_id')]]=$dyeing_rate;
		}
		else{
			$service_rate=$yarn_service_rate_arr[$jobNo]['rate'];
			$yarn_issue_amount_arr[$job_id]['amount']+=$service_rate*$row[csf('cons_quantity')];
			$product_wise_yarn_rate[$row[csf('issue_id')]]=$service_rate;
		}
	}
	unset($resultyarnissue); 
	
	fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 144, 6, $yarnissue_prodid, $empty_arr);	

	$sql_issue_ret="SELECT a.prod_id,b.lot,b.color,c.recv_number, c.booking_no, a.issue_id, sum(a.cons_quantity) as cons_quantity from inv_transaction a, product_details_master b,inv_receive_master c, gbl_temp_engine d, wo_yarn_dyeing_mst e where a.prod_id=b.id and c.id=a.mst_id and e.id=c.booking_id and c.entry_form=9 and c.item_category=1 and c.receive_basis=1 and a.receive_basis=1 and a.transaction_type=4 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and a.issue_id=d.ref_val and d.ref_from=6 and d.entry_form=144 and d.user_id=165 group by a.prod_id,b.lot,b.color,c.recv_number, c.booking_no, a.issue_id";
	//echo $sql_issue_ret; die;
	$resultissue_ret = sql_select($sql_issue_ret);
	
	foreach($resultissue_ret as $row)
	{
		$job_no=$yarn_issue_wise_job[$row[csf('issue_id')]];
		$rate=$product_wise_yarn_rate[$row[csf('issue_id')]];
		$yarn_issue_amount_arr[$job_id]['return_amount']+=$rate*$row[csf('cons_quantity')];
	}
	/* 
	echo '<pre>';
	print_r($yarn_issue_amount_arr); die; */

	$knit_fin_fab_array=array(); 
    $converson_fab_cost_sql="SELECT b.currency_id,a.bill_no,b.order_id, a.bill_date, b.item_id, b.rate, b.receive_qty, b.amount as amount, d.job_id  from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b, gbl_temp_engine c, wo_po_break_down d where a.id=b.mst_id and b.order_id=c.ref_val and c.ref_val=d.id  and c.ref_from=2 and c.entry_form=144 and c.user_id=$user_name and a.process_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by b.item_id";
	  //echo $converson_fab_cost_sql; die;


   $con_fab_data=sql_select($converson_fab_cost_sql);
   $usd_id=2;
   foreach($con_fab_data as $row )
   {
		$currency_id=$row[csf('currency_id')];
		$currency_rate=$job_exchange_rateArr[$row[csf("job_id")]]['exchange_rate'];
		if($currency_id==2) //Usd
		{
			$knit_fin_fab_array[$row[csf('job_id')]]['knit_gross_amt']+=$row[csf('amount')];
		}
		else{
			$knit_fin_fab_array[$row[csf('job_id')]]['knit_gross_amt']+=$row[csf('amount')]/$currency_rate;
		}
   }
   $recvIssue_array=array(); 
	$sql_trans="SELECT b.trans_type, b.po_breakdown_id, sum(b.quantity) as qnty, d.job_id from inv_transaction a, order_wise_pro_details b, gbl_temp_engine c, wo_po_break_down d where a.id=b.trans_id and d.id=c.ref_val and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(80,82,81,83,84,13,94) and a.item_category=13 and a.transaction_type in(5,6) and b.trans_type in(5,6) and b.po_breakdown_id=c.ref_val and c.ref_from=2 and c.entry_form=144 and c.user_id=$user_name group by b.trans_type, b.po_breakdown_id, d.job_id";
	$result_trans=sql_select( $sql_trans );
	foreach ($result_trans as $row)
	{
		$recvIssue_array[$row[csf('job_id')]][$row[csf('trans_type')]]=$row[csf('qnty')];
	}
	$dye_fin_fab_array=array(); 
    $converson_fab_dye_cost_in_sql="SELECT b.currency_id as currency,a.bill_date,a.discount,b.order_id,b.amount, d.job_id  from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b, gbl_temp_engine c, wo_po_break_down d where a.id=b.mst_id and b.order_id=c.ref_val and d.id=c.ref_val and c.ref_from=2 and c.entry_form=144 and c.user_id=$user_name and a.process_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  ";
	$con_fab_dye_in_data=sql_select($converson_fab_dye_cost_in_sql);
	foreach($con_fab_dye_in_data as $row )
	{
		$currency_id=$row[csf('currency')];
		$conversion_date=change_date_format($row[csf('bill_date')], "d-M-y", "-",1);
		$currency_rate=set_conversion_rate($usd_id,$conversion_date,$company_name );
		if($currency_id==2) //Usd
		{
			$dye_fin_fab_array[$row[csf('job_id')]]['dye_gross_amt']+=$row[csf('amount')];
			$dye_fin_fab_array[$row[csf('job_id')]]['discount_amt']=$row[csf('discount')];
		}
		else
		{
			$dye_fin_fab_array[$row[csf('job_id')]]['dye_gross_amt']+=$row[csf('amount')]/$currency_rate;
			$dye_fin_fab_array[$row[csf('job_id')]]['discount_amt']=$row[csf('discount')]/$currency_rate;
		}	
	}
	$converson_fab_dye_cost_out_sql="select a.discount,a.bill_no,b.order_id,a.currency_id, a.bill_date, b.item_id, b.rate, b.receive_qty, b.amount as amount, d.job_id  from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b, gbl_temp_engine c, wo_po_break_down d where a.id=b.mst_id and b.order_id=c.ref_val and d.id=c.ref_val and c.ref_from=2 and c.entry_form=144 and c.user_id=$user_name and a.process_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   order by b.item_id";
	$con_fab_dye_out_data=sql_select($converson_fab_dye_cost_out_sql);
	foreach($con_fab_dye_out_data as $row )
	{
		$currency_id=$row[csf('currency_id')];
		$conversion_date=change_date_format($row[csf('bill_date')], "d-M-y", "-",1);
		$currency_rate=set_conversion_rate($usd_id,$conversion_date,$company_name );
		if($currency_id==2) //Usd
		{
			$dye_fin_fab_array[$row[csf('job_id')]]['dye_gros_amt']+=$row[csf('amount')];
			$dye_fin_fab_array[$row[csf('job_id')]]['discoun_amt']+=$row[csf('discount')];
		}
		else{
			$dye_fin_fab_array[$row[csf('job_id')]]['dye_gros_amt']+=$row[csf('amount')]/$currency_rate;
			$dye_fin_fab_array[$row[csf('job_id')]]['discoun_amt']+=$row[csf('discount')]/$currency_rate;

		}	
	}

	$fin_fab_trans_array=array(); 
	$sql_fin_trans="SELECT b.trans_type, sum(b.quantity) as qnty, d.job_id from inv_transaction a, order_wise_pro_details b, gbl_temp_engine c, wo_po_break_down d where a.id=b.trans_id and d.id=c.ref_val and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(15,14) and a.item_category=2 and a.transaction_type in(5,6) and b.trans_type in(5,6) and b.po_breakdown_id=c.ref_val and c.ref_from=2 and c.entry_form=144 and c.user_id=$user_name group by b.trans_type, d.job_id";
	$result_fin_trans=sql_select( $sql_fin_trans );
	foreach ($result_fin_trans as $row)
	{
		$fin_fab_trans_array[$row[csf('job_id')]][$row[csf('trans_type')]]=$row[csf('qnty')];
	}
	$sql_gray_fin_purchase="SELECT a.isgreyfab_purchase, b.po_break_down_id, b.amount, b.job_no from wo_booking_mst a join wo_booking_dtls b on a.id=b.booking_mst_id join gbl_temp_engine c on b.po_break_down_id=c.ref_val where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_short=2 and a.booking_type=1 and a.fabric_source=2 and a.entry_form=108 and c.ref_from=2 and c.entry_form=144 and c.user_id=$user_name";
	//echo $sql_gray_fin_purchase; die;
	$dataArrayFinPurchase=sql_select($sql_gray_fin_purchase);
	$grey_purchase_amnt_arr=array();  $finish_purchase_amnt_arr=array();
	foreach($dataArrayFinPurchase as $row)
	{
		if($row[csf('isgreyfab_purchase')]==2){
			$finish_purchase_amnt_arr[$row[csf('job_no')]]+=$row[csf('amount')];
		}
		else{
			$grey_purchase_amnt_arr[$row[csf('job_no')]]+=$row[csf('amount')];
		}
		
	}
	$sql_garments="SELECT b.po_break_down_id as po_id,b.embel_name,b.production_date,b.production_type, d.job_id, (CASE WHEN b.embel_name=1 THEN b.production_quantity ELSE 0 END) AS printing_qty, (CASE WHEN b.embel_name=2 THEN b.production_quantity ELSE 0 END) AS embro_qty, (CASE WHEN b.embel_name=3 THEN b.production_quantity ELSE 0 END) AS wash_qty, (CASE WHEN b.embel_name=5 THEN b.production_quantity ELSE 0 END) AS dyeing_qty from pro_garments_production_mst b, gbl_temp_engine c, wo_po_break_down d where  b.production_type=2 and b.is_deleted=0 and b.status_active=1 and b.status_active=1 and b.is_deleted=0  and b.po_break_down_id=c.ref_val and c.ref_val=d.id and c.ref_from=2 and c.entry_form=144 and c.user_id=$user_name";
	$result_gar=sql_select( $sql_garments );
	foreach ($result_gar as $row)
	{
		$embl_issue_prod_array[$row[csf('job_id')]]['embro_qty']+=$row[csf('embro_qty')];
		$embl_issue_prod_array[$row[csf('job_id')]]['wash_qty']+=$row[csf('wash_qty')];
		$embl_issue_prod_array[$row[csf('job_id')]]['dyeing_qty']+=$row[csf('dyeing_qty')];
	}

	$trims_trans_array=array(); 				
	$sql_trims_inv="SELECT a.transaction_date, b.order_amount as cons_amount, a.prod_id,b.po_breakdown_id as po_id, b.quantity as qnty,c.exchange_rate, c.currency_id, e.job_id from inv_transaction a, order_wise_pro_details b, inv_receive_master c, gbl_temp_engine d, wo_po_break_down e  where a.id=b.trans_id and c.id=a.mst_id and e.id=d.ref_val and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.entry_form in(24) and a.item_category=4 and a.transaction_type in(1) and b.trans_type in(1) and b.po_breakdown_id=d.ref_val and d.ref_from=2 and d.entry_form=144 and d.user_id=$user_name " ;
	//echo $sql_trims_inv;die;
	$result_trims=sql_select( $sql_trims_inv );
	foreach ($result_trims as $row)
	{
		$currency_id=$row[csf('currency_id')];
		$exchange_rate=$job_exchange_rateArr[$row[csf("job_id")]]['exchange_rate'];	
		if($currency_id==2){			
			$amt=$row[csf('cons_amount')];
			$trims_trans_array[$row[csf('job_id')]]['amt']+=$amt;
		}
		else{			
			$amt=$row[csf('cons_amount')]/$exchange_rate;
			$trims_trans_array[$row[csf('job_id')]]['amt']+=$amt;
		}
		$trims_prod_id_arr[$row[csf('prod_id')]]['currency']=$currency_id;
		$trims_prod_id_arr[$row[csf('prod_id')]]['exchange_rate']=$currency_id;
	}
	$trims_rev_rtn_qty_data = sql_select("SELECT b.prod_id, f.job_id, c.item_group_id, sum(d.quantity) as quantity, sum(d.order_amount) as amt from inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d, gbl_temp_engine e, wo_po_break_down f where f.id=e.ref_val and a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and d.trans_type=3 and d.entry_form=49 and a.entry_form=49 and b.transaction_type=3 and c.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.po_breakdown_id=e.ref_val and e.user_id=$user_name and e.entry_form=144 and e.ref_from=2 group by b.prod_id, f.job_id, c.item_group_id order by c.item_group_id");
	foreach($trims_rev_rtn_qty_data as $row){
		$trims_trans_array[$row[csf('job_id')]]['return_amt']+=$row[csf('amt')];
	}

	$gen_trims_issue_array=array(); 
	$sql_gen_trims_inv="SELECT b.transaction_date,(b.cons_amount) as cons_amount,b.order_id as po_id,b.prod_id, d.job_id from inv_issue_master  a, inv_transaction b, gbl_temp_engine c, wo_po_break_down d where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(21) and b.transaction_type in(2) and b.order_id=c.ref_val and c.ref_val=d.id and c.ref_from=2 and c.entry_form=144 and c.user_id=$user_name";
	$result_gen_trims=sql_select( $sql_gen_trims_inv );
	foreach ($result_gen_trims as $row)
	{
		$transaction_date=$row[csf('transaction_date')];
		if($db_type==0) $conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
		else $conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
		$currency_rate=$last_exchnage_rateArr[$row[csf('prod_id')]];
		$gen_trims_issue_array[$row[csf('job_id')]]['amt']+=$row[csf('cons_amount')]/$currency_rate;
	}
	$store_to_store_sql=sql_select("SELECT b.po_breakdown_id, a.item_color as item_color_id, a.item_size, a.item_group_id, a.item_description, b.quantity, c.cons_rate as rate, b.entry_form, b.trans_type, c.store_id, d.transfer_criteria from product_details_master a, order_wise_pro_details b, inv_transaction c, inv_item_transfer_mst d, gbl_temp_engine e where a.id=b.prod_id and b.trans_id=c.id and d.id=c.mst_id and item_category_id=4 and a.entry_form=24 and b.entry_form in(112) and b.trans_type in(5,6) and c.transaction_type in(5,6) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.transfer_criteria=2 and b.po_breakdown_id =e.ref_val and e.ref_from=2 and e.entry_form=144 and e.user_id=$user_name");
	$transfer_criteria_arr=array();
	foreach($store_to_store_sql as $rows)
	{
		$item_description=trim($row[csf("item_description")]);
		$trim_str=", [BS]";
		$item_desc=chop_last_string($item_description,$trim_str);
		if($rows[csf('item_size')]=="") $item_sizeId=0; else $item_sizeId=$rows[csf('item_size')];
		if($rows[csf('trans_type')]==5 || $rows[csf('trans_type')]==6)
		{
			$transfer_criteria_arr[$rows[csf('po_breakdown_id')]][$rows[csf('item_group_id')]][$item_desc][$rows[csf('item_color_id')]][$item_sizeId]["transfer_criteria"]=$rows[csf('transfer_criteria')];
		}
	}

	$trans_qty_sql=sql_select("SELECT b.po_breakdown_id, a.item_color as item_color_id, a.item_size, a.item_group_id, a.item_description, b.quantity, c.cons_rate as rate, b.entry_form, b.trans_type, c.store_id, d.job_no_mst as job_no, d.job_id from product_details_master a, order_wise_pro_details b, inv_transaction c ,wo_po_break_down d, gbl_temp_engine e where a.id=b.prod_id and b.trans_id=c.id and d.id=b.po_breakdown_id and d.id=c.order_id and item_category_id=4  and b.entry_form in(78,112) and b.trans_type in(5,6) and c.transaction_type in(5,6) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.po_breakdown_id =e.ref_val and e.ref_from=2 and e.entry_form=144 and e.user_id=$user_name");

	foreach($trans_qty_sql as $row)
	{
		if($row[csf('item_size')]=="") $item_size_id=0; else $item_size_id=$row[csf('item_size')];

		$item_description=trim($row[csf("item_description")]);
		$trim_str=", [BS]";
		$item_desc=chop_last_string($item_description,$trim_str);		
		if(($row[csf('entry_form')]==78 || $row[csf('entry_form')]==112) && $row[csf('trans_type')]==5)
		{
			if ($transfer_criteria_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$item_desc][$row[csf('item_color_id')]][$item_size_id]["transfer_criteria"] != 2)
			{
				$exchange_rate=$job_exchange_rateArr[$row[csf("job_id")]]['exchange_rate'];
				$amount=$row[csf('quantity')]*$row[csf('rate')];
				$trims_trans_array[$row[csf('job_id')]]['in_amt']+=$amount/$exchange_rate;				
			}
			
		}
		if(($row[csf('entry_form')]==78 || $row[csf('entry_form')]==112) && $row[csf('trans_type')]==6)
		{
			if ($transfer_criteria_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$item_desc][$row[csf('item_color_id')]][$item_size_id]["transfer_criteria"] != 2)
			{
				$exchange_rate=$job_exchange_rateArr[$row[csf("job_id")]]['exchange_rate'];
				$amount=$row[csf('quantity')]*$row[csf('rate')];
				$trims_trans_array[$row[csf('job_id')]]['out_amt']+=$amount/$exchange_rate;
				$trims_trans_array[$row[csf('job_id')]]['out_quantity']+=$row[csf('quantity')];				
			}
		}
	}
	/* echo '<pre>';
	print_r($trims_trans_array); die; */

	$comm_cost_from_invoice=sql_select("SELECT a.commission, a.invoice_value, b.current_invoice_value, b.po_breakdown_id,  c.job_id from com_export_invoice_ship_mst a join com_export_invoice_ship_dtls b on a.id=b.mst_id join wo_po_break_down c on c.id=b.po_breakdown_id join gbl_temp_engine d on c.id=d.ref_val where d.ref_from=2 and d.entry_form=144 and d.user_id=$user_name");
	foreach($comm_cost_from_invoice as $row){
		$com_amt=$row[csf('commission')]*$row[csf('current_invoice_value')]/$row[csf('invoice_value')];
		$commission_cost_arr[$row[csf('job_id')]]=$com_amt;
	}
	$actual_cost_data=sql_select("SELECT b.job_id, c.cost_head, sum(c.amount) as amt, max(c.exchange_rate) as exchange_rate from wo_po_details_master a, wo_po_break_down b, wo_actual_cost_entry c, gbl_temp_engine d where a.id=b.job_id and b.id=c.po_id and c.status_active=1 and c.is_deleted=0 and b.id=d.ref_val and d.ref_from=2 and d.entry_form=144 and d.user_id=$user_name group by b.job_id, c.cost_head");
	foreach($actual_cost_data as $row){
		$actual_cost_arr[$row[csf('job_id')]][$row[csf('cost_head')]]=$row[csf('amt')]/$row[csf('exchange_rate')];
	}	

	$embellishment_cost_data=sql_select("SELECT sum(b.amount) as amount, c.job_id, c.emb_name from wo_booking_mst a join wo_booking_dtls b on a.id=b.booking_mst_id join wo_pre_cost_embe_cost_dtls c on c.id=b.pre_cost_fabric_cost_dtls_id join gbl_temp_engine d on d.ref_val=c.job_id where a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.ref_from=1 and d.entry_form=144 and d.user_id=$user_name and b.booking_type=6 and c.emb_name in (1,2) group by c.job_id, c.emb_name");
	foreach($embellishment_cost_data as $row){
		$actual_embellishment_cost[$row[csf('job_id')]][$row[csf('emb_name')]]=$row[csf('amount')];
	}

	$main_data_attributes=array('buyer_name', 'year', 'job_no_prefix_num', 'file_no','style_ref_no', 'order_uom', 'set_smv','job_quantity', 'avg_unit_price', 'total_price');
	$not_yarn_dyed_cost_arr=array(1,2,30,134,35);
	$main_data_arr=array();
	foreach($main_data as $row){
		$jobid=$row[csf("job_id")];
		$jobno=$row[csf("job_no")];
		foreach($main_data_attributes as $attr){
			$main_data_arr[$jobid][$attr]= $row[csf($attr)];
		}		
		$main_data_arr[$jobid]['grouping'][$row[csf('grouping')]]= $row[csf('grouping')];
		$main_data_arr[$jobid]['ship_date']= $max_date[$row[csf('job_id')]];
		$main_data_arr[$jobid]['shiping_status'][$row[csf('shiping_status')]]= $shipment_status[$row[csf('shiping_status')]];
		$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
		foreach($gmts_item_id as $item_id)
		{
			$main_data_arr[$jobid]['gmts_item'][$item_id]= $garments_item[$item_id];
		}
		$dzn_qnty=0;
		$costing_per_id=$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
		if($costing_per_id==1) $dzn_qnty=12;
		else if($costing_per_id==3) $dzn_qnty=12*2;
		else if($costing_per_id==4) $dzn_qnty=12*3;
		else if($costing_per_id==5) $dzn_qnty=12*4;
		else $dzn_qnty=1;
		$dzn_qnty=$dzn_qnty*$row[csf('ratio')];

		$yarn_value_addition=array_sum($conversion_costing_arr_process[$jobno][30])+array_sum($conversion_costing_arr_process[$jobno][134]);
		$grey_fab_cost_mkt=array_sum($conversion_costing_arr_process[$jobno][1])+$yarn_costing_arr[$jobno]+$yarn_value_addition;
		$aop_cost_mkt=array_sum($conversion_costing_arr_process[$jobno][35]);
		$dye_finish_cost_mkt=0;$dye_finish_cost_mkt_qty=0;
		foreach($conversion_cost_head_array as $process_id=>$val)
		{
			if(!in_array($process_id,$not_yarn_dyed_cost_arr))
			{
				$dye_finish_cost_mkt+=array_sum($conversion_costing_arr_process[$jobno][$process_id]);
				
				$dye_finish_cost_mkt_qty+=array_sum($conversion_costing_arr_process_qty[$jobno][$process_id]);
			}
		}
		$dye_finish_cost_mkt=0;$dye_finish_cost_mkt_qty=0;
		foreach($conversion_cost_head_array as $process_id=>$val)
		{
			if(!in_array($process_id,$not_yarn_dyed_cost_arr))
			{
				$dye_finish_cost_mkt+=array_sum($conversion_costing_arr_process[$jobno][$process_id]);
				
				$dye_finish_cost_mkt_qty+=array_sum($conversion_costing_arr_process_qty[$jobno][$process_id]);
			}
		}
		if($dye_finish_cost_mkt>0)
		{			
			$knit_charge_mkt_fin=$dye_finish_cost_mkt/$dye_finish_cost_mkt_qty;
		}
		$finish_fabric_purchase_cost_mkt=array_sum($fabric_costing_arr['knit']['finish'][$jobno])+array_sum($fabric_costing_arr['woven']['finish'][$jobno]);
		$gray_fabric_purchase_cost_mkt=array_sum($fabric_costing_arr['knit']['grey'][$jobno])+array_sum($fabric_costing_arr['woven']['grey'][$jobno]);
		$finish_fabric_cost_mkt=$grey_fab_cost_mkt+$dye_finish_cost_mkt+$aop_cost_mkt;
		$tot_fabric_cost_mkt=$finish_fabric_cost_mkt+$gray_fabric_purchase_cost_mkt;
		$trims_cost_mkt=$trims_costing_arr[$jobno];
		$print_amount=$emblishment_costing_arr_name[$jobno][1];
		$print_qty=$emblishment_costing_arr_name_qty[$jobno][1];
		
		if($print_amount) $print_amount=$print_amount;else $print_amount=0;
		if($print_qty) $print_qty=$print_qty;else $print_qty=0;
		if($print_amount==0 || $print_qty==0) $print_avg_rate=0;
		else $print_avg_rate=$print_amount/$print_qty;
		//echo $print_amount.'--'.$print_qty.'--'.$print_avg_rate; die;
		$embroidery_amount=$emblishment_costing_arr_name[$jobno][2];
		$embroidery_qty=$emblishment_costing_arr_name_qty[$jobno][2];
		if($embroidery_amount) $embroidery_amount=$embroidery_amount;else $embroidery_amount=0;
		if($embroidery_qty) $embroidery_qty=$embroidery_qty;else $embroidery_qty=0;
		if($embroidery_amount==0 || $embroidery_qty==0) $embro_avg_rate=0;
		else $embro_avg_rate=$embroidery_amount/$embroidery_qty;

		$gmt_dyeing_amount=$emblishment_costing_arr_name[$jobno][5];
		$dyeing_qty=$emblishment_costing_arr_name_qty[$jobno][5];
		if($gmt_dyeing_amount) $gmt_dyeing_amount=$gmt_dyeing_amount;else $gmt_dyeing_amount=0;
		if($dyeing_qty) $dyeing_qty=$dyeing_qty;else $dyeing_qty=0;
		if($gmt_dyeing_amount==0 || $dyeing_qty==0) $dyeing_avg_rate=0;
		else $dyeing_avg_rate=$gmt_dyeing_amount/$dyeing_qty;

		$wash_cost=$emblishment_costing_arr_name_wash[$jobno][3];
		$wash_qty=$emblishment_costing_arr_name_wash_qty[$jobno][3];
		if($wash_cost) $wash_cost=$wash_cost;else $wash_cost=0;
		if($wash_qty) $wash_qty=$wash_qty;else $wash_qty=0;
		if($wash_cost==0 || $wash_qty==0) $wash_avg_rate=0;
		else $wash_avg_rate=$wash_cost/$wash_qty;

		$foreign_cost=$commission_costing_arr[$jobno][1];
		$local_cost=$commission_costing_arr[$jobno][2];
		$commission_cost_mkt=$foreign_cost+$local_cost;
		$comm_cost_mkt=$commercial_costing_arr[$jobno];

		$test_cost=$other_costing_arr[$jobno]['lab_test'];
		$freight_cost=$other_costing_arr[$jobno]['freight'];
		$inspection_cost=$other_costing_arr[$jobno]['inspection'];	
		$certificate_cost_mkt=$other_costing_arr[$jobno]['certificate_pre_cost'];
		$deffdlc_cost_mkt=$other_costing_arr[$jobno]['deffdlc_cost'];
		$design_cost_mkt=$other_costing_arr[$jobno]['design_cost'];
		$studio_cost_mkt=$other_costing_arr[$jobno]['studio_cost'];
		$operating_exp_mkt=$other_costing_arr[$jobno]['common_oh'];
		$interest_cost_mkt=$other_costing_arr[$jobno]['interest_cost'];
		$incometax_cost_mkt=$other_costing_arr[$jobno]['incometax_cost'];
		$depr_amor_pre_cost_mkt=$other_costing_arr[$jobno]['depr_amor_pre_cost'];
		$currier_pre_cost=$other_costing_arr[$jobno]['currier_pre_cost'];
		$cm_cost=$other_costing_arr[$jobno]['cm_cost'];

		$total_cost_mkt=$tot_fabric_cost_mkt+$trims_cost_mkt+$print_amount+$embroidery_amount+$gmt_dyeing_amount+$wash_cost+$commission_cost_mkt+$comm_cost_mkt+$freight_cost+$test_cost+$inspection_cost+$currier_pre_cost+$certificate_cost_mkt+$deffdlc_cost_mkt+$design_cost_mkt+$studio_cost_mkt+$operating_exp_mkt+$cm_cost+$interest_cost_mkt+$incometax_cost_mkt+$depr_amor_pre_cost_mkt;

		$margin_loss_mkt=$row[csf('total_price')]-$total_cost_mkt;
		$percent_ex_factory_value=($margin_loss_mkt/$row[csf('total_price')])*100;

		$knit_cost_mkt=array_sum($conversion_costing_arr_process[$row[csf('job_no')]][1]);
		$knit_qty_mkt=array_sum($conversion_costing_arr_process_qty[$row[csf('job_no')]][1]);
		if($knit_cost_mkt>0)
		{
			$knit_charge_mkt=$knit_cost_mkt/$knit_qty_mkt;
		}

		$main_data_arr[$jobid]['pre'][1]=$row[csf('job_quantity')];
		$main_data_arr[$jobid]['pre'][2]=$row[csf('total_price')];
		$main_data_arr[$jobid]['pre'][3]=$yarn_costing_arr[$row[csf('job_no')]];
		$main_data_arr[$jobid]['pre'][4]=$yarn_value_addition;
		$main_data_arr[$jobid]['pre'][5]=$knit_cost_mkt;
		$main_data_arr[$jobid]['pre'][6]=0;
		$main_data_arr[$jobid]['pre'][7]=$grey_fab_cost_mkt;
		$main_data_arr[$jobid]['pre'][8]=$dye_finish_cost_mkt;
		$main_data_arr[$jobid]['pre'][9]=$aop_cost_mkt;
		$main_data_arr[$jobid]['pre'][10]=0;
		$main_data_arr[$jobid]['pre'][11]=$finish_fabric_purchase_cost_mkt;
		$main_data_arr[$jobid]['pre'][12]=$gray_fabric_purchase_cost_mkt;
		$main_data_arr[$jobid]['pre'][13]=$finish_fabric_cost_mkt;
		$main_data_arr[$jobid]['pre'][14]=$tot_fabric_cost_mkt;
		$main_data_arr[$jobid]['pre'][15]=$trims_cost_mkt;
		$main_data_arr[$jobid]['pre'][16]=$print_amount;
		$main_data_arr[$jobid]['pre'][17]=$embroidery_amount;
		$main_data_arr[$jobid]['pre'][18]=$gmt_dyeing_amount;
		$main_data_arr[$jobid]['pre'][19]=$wash_cost;
		$main_data_arr[$jobid]['pre'][20]=$commission_cost_mkt;
		$main_data_arr[$jobid]['pre'][21]=$comm_cost_mkt;
		$main_data_arr[$jobid]['pre'][22]=$freight_cost;
		$main_data_arr[$jobid]['pre'][23]=$test_cost;
		$main_data_arr[$jobid]['pre'][24]=$inspection_cost;
		$main_data_arr[$jobid]['pre'][25]=$currier_pre_cost;
		$main_data_arr[$jobid]['pre'][26]=$certificate_cost_mkt;
		$main_data_arr[$jobid]['pre'][27]=$deffdlc_cost_mkt;
		$main_data_arr[$jobid]['pre'][28]=$design_cost_mkt;
		$main_data_arr[$jobid]['pre'][29]=$studio_cost_mkt;
		$main_data_arr[$jobid]['pre'][30]=$operating_exp_mkt;
		$main_data_arr[$jobid]['pre'][31]=$cm_cost;
		$main_data_arr[$jobid]['pre'][32]=$interest_cost_mkt;
		$main_data_arr[$jobid]['pre'][33]=$incometax_cost_mkt;
		$main_data_arr[$jobid]['pre'][34]=$depr_amor_pre_cost_mkt;
		$main_data_arr[$jobid]['pre'][35]=0;
		$main_data_arr[$jobid]['pre'][36]=$total_cost_mkt;
		$main_data_arr[$jobid]['pre'][37]=$margin_loss_mkt;
		$main_data_arr[$jobid]['pre'][38]=$percent_ex_factory_value;
		$main_data_arr[$jobid]['pre'][39]=0;
		$main_data_arr[$jobid]['pre'][40]=0;

		//Actual Data From Here
		$ex_factory_qty=$ex_factory_arr[$jobid]['qty'];
		$ex_factory_value=$ex_factory_arr[$jobid]['value'];
		//$yarn_cost_actual=$yarnTrimsCostArray[$jobid]['grey_yarn_amt']-$yarnissueRetCostArray[$jobid]['grey_yarn_ret_amt'];
		$yarn_cost_actual=$yarn_cost_value[$jobid];
		$order_amount_issue=$yarn_issue_amount_arr[$jobid]['amount'];
		$order_amount_issue_ret=$yarn_issue_amount_arr[$jobid]['return_amount'];		
		$yarn_dyeing_twist_actual=$order_amount_issue-$order_amount_issue_ret;
		$knitting_cost_act=$knit_fin_fab_array[$jobid]['knit_gross_amt'];

		$trans_in_amt=$recvIssue_array[$jobid][5];
		$trans_out_amt=$recvIssue_array[$jobid][6];
		$grey_fab_transfer=$trans_in_amt-$trans_out_amt;
		$grey_fab_transfer_amt_actual=$grey_fab_transfer*$knit_charge_mkt;

		if($dye_fin_fab_array[$jobid]['discount_amt']>0){
			$fin_fab_prod_cost=$dye_fin_fab_array[$jobid]['dye_gross_amt']-$dye_fin_fab_array[$jobid]['discount_amt'];
		}else{
			$fin_fab_prod_cost=$dye_fin_fab_array[$jobid]['dye_gross_amt'];
		}
		$aop_cost_actual=$aop_prod_array[$jobno]['amount'];

		$fin_trans_in_amt=$fin_fab_trans_array[$jobid][5];
		$fin_trans_out_amt=$fin_fab_trans_array[$jobid][6];
		$fin_fab_transfer=$fin_trans_in_amt-$fin_trans_out_amt;
		$fin_fab_transfer_amt_actual=$fin_fab_transfer*$knit_charge_mkt_fin;

		$fabric_purchase_cost_actual=$finish_purchase_amnt_arr[$jobno];
		$grey_fabric_purchase_cost_actual=$grey_purchase_amnt_arr[$jobno];
		$grey_fabric_cost=$yarn_cost_actual+$knitting_cost_act+$grey_fab_transfer_amt_actual;

		$finished_fab_cost_actual=$grey_fabric_cost+$fin_fab_prod_cost+$aop_cost_actual+$fin_fab_transfer_amt_actual;

		$tot_fabric_cost_act=$fabric_purchase_cost_actual+$finished_fab_cost_actual;
		
		$printing_cost_actual=$actual_embellishment_cost[$jobid][1];
		$embro_cost_actual=$actual_embellishment_cost[$jobid][2];
		$dyeing_cost_actual=($embl_issue_prod_array[$jobid]['dyeing_qty']/$dzn_qnty)*$dyeing_avg_rate;
		$wash_cost_actual=($embl_issue_prod_array[$jobid]['wash_qty']/$dzn_qnty)*$wash_avg_rate;

		$trims_amount=$trims_trans_array[$jobid]['amt'];
		$trims_return_amount=$trims_trans_array[$jobid]['return_amt'];
		$trims_in_amount=$trims_trans_array[$jobid]['in_amt'];
		$trims_out_amount=$trims_trans_array[$jobid]['out_amt'];
		$gen_trims_issue_amt=$gen_trims_issue_array[$jobid]['amt'];
		$trims_cost_actual=$trims_amount+$gen_trims_issue_amt+$trims_in_amount-$trims_out_amount-$trims_return_amount;

		$total_commission_cost=$commission_cost_arr[$jobid]+$actual_cost_arr[$jobid][10];
		$comm_cost_act=$actual_cost_arr[$jobid][6];
		$freight_cost_act=$actual_cost_arr[$jobid][2];
		$test_cost_act=$actual_cost_arr[$jobid][1];
		$inspection_cost_act=$actual_cost_arr[$jobid][3];
		$currier_cost_act=$actual_cost_arr[$jobid][4];
		$certificate_cost_act=$certificate_cost_mkt;
		$deffdlc_cost_act=$deffdlc_cost_mkt;
		$design_cost_act=$actual_cost_arr[$jobid][7];		
		$studio_cost_act=$studio_cost_mkt;
		$operating_exp_act=$actual_cost_arr[$jobid][8];
		$cm_cost_act=$actual_cost_arr[$jobid][5];
		$interest_cost_act=$interest_cost_mkt;
		$incometax_cost_act=$incometax_cost_mkt;
		$depr_amor_cost_act=$depr_amor_pre_cost_mkt;
		$other_cost_act=$actual_cost_arr[$jobid][9];

		$total_cost_act=$tot_fabric_cost_act+$trims_cost_actual+$printing_cost_actual+$embro_cost_actual+$dyeing_cost_actual+$wash_cost_actual+$total_commission_cost+$comm_cost_act+$freight_cost_act+$test_cost_act+$inspection_cost_act+$currier_cost_act+$certificate_cost_act+$deffdlc_cost_act+$design_cost_act+$studio_cost_act+$operating_exp_act+$cm_cost_act+$interest_cost_act+$incometax_cost_act+$depr_amor_cost_act+$other_cost_act;

		$margin_loss_act=$ex_factory_value-$total_cost_act;
		$percent_ex_factory_act=($margin_loss_act/$ex_factory_value)*100;

		//$yarn_cost_cpa_actual=$cpashortfabriccost_arr[$jobid]['cpa_grey_yarn_amt']-$cpashortfabriccost_arr[$jobid]['cpa_grey_yarn_rtn_amt'];
		$yarn_cost_cpa_actual=$short_yarn_cost_value[$jobid];
		
		$net_margin_act=$margin_loss_act-$yarn_cost_cpa_actual;	

		$main_data_arr[$jobid]['act'][1]=$ex_factory_qty;
		$main_data_arr[$jobid]['act'][2]=$ex_factory_value;
		$main_data_arr[$jobid]['act'][3]=$yarn_cost_actual;
		$main_data_arr[$jobid]['act'][4]=$yarn_dyeing_twist_actual;
		$main_data_arr[$jobid]['act'][5]=$knitting_cost_act;
		$main_data_arr[$jobid]['act'][6]=$grey_fab_transfer_amt_actual;
		$main_data_arr[$jobid]['act'][7]=$grey_fabric_cost;
		$main_data_arr[$jobid]['act'][8]=$fin_fab_prod_cost;
		$main_data_arr[$jobid]['act'][9]=$aop_cost_actual;
		$main_data_arr[$jobid]['act'][10]=$fin_fab_transfer_amt_actual;
		$main_data_arr[$jobid]['act'][11]=$fabric_purchase_cost_actual;
		$main_data_arr[$jobid]['act'][12]=$grey_fabric_purchase_cost_actual;
		$main_data_arr[$jobid]['act'][13]=$finished_fab_cost_actual;		
		$main_data_arr[$jobid]['act'][14]=$tot_fabric_cost_act;
		$main_data_arr[$jobid]['act'][15]=$trims_cost_actual;		
		$main_data_arr[$jobid]['act'][16]=$printing_cost_actual;
		$main_data_arr[$jobid]['act'][17]=$embro_cost_actual;
		$main_data_arr[$jobid]['act'][18]=$dyeing_cost_actual;
		$main_data_arr[$jobid]['act'][19]=$wash_cost_actual;
		$main_data_arr[$jobid]['act'][20]=$total_commission_cost;
		$main_data_arr[$jobid]['act'][21]=$comm_cost_act;
		$main_data_arr[$jobid]['act'][22]=$freight_cost_act;
		$main_data_arr[$jobid]['act'][23]=$test_cost_act;
		$main_data_arr[$jobid]['act'][24]=$inspection_cost_act;
		$main_data_arr[$jobid]['act'][25]=$currier_cost_act;
		$main_data_arr[$jobid]['act'][26]=$certificate_cost_act;
		$main_data_arr[$jobid]['act'][27]=$deffdlc_cost_act;
		$main_data_arr[$jobid]['act'][28]=$design_cost_act;
		$main_data_arr[$jobid]['act'][29]=$studio_cost_act;
		$main_data_arr[$jobid]['act'][30]=$operating_exp_act;
		$main_data_arr[$jobid]['act'][31]=$cm_cost_act;
		$main_data_arr[$jobid]['act'][32]=$interest_cost_act;
		$main_data_arr[$jobid]['act'][33]=$incometax_cost_act;
		$main_data_arr[$jobid]['act'][34]=$depr_amor_cost_act;
		$main_data_arr[$jobid]['act'][35]=$other_cost_act;
		$main_data_arr[$jobid]['act'][36]=$total_cost_act;
		$main_data_arr[$jobid]['act'][37]=$margin_loss_act;
		$main_data_arr[$jobid]['act'][38]=$percent_ex_factory_act;
		$main_data_arr[$jobid]['act'][39]=$yarn_cost_cpa_actual;
		$main_data_arr[$jobid]['act'][40]=$net_margin_act;
		
	}

	$heading_arr=array('Job/Ex-Factory Qnty','Job/Ex-Factory Value','Grey Yarn Cost','Yarn Value Addition Cost','Knitting Cost','Grey Fabric Transfer Cost','Grey Fabric Cost','Dye & Fin Cost','AOP Cost','Fin. Fab. Transfer Cost','Fin.Fabric Purchase Cost','Grey Fabric Purchease Cost','Finished Fabric Cost[Production]','Total Fabric Cost','Trims Cost','Printing Cost','Embroidery Cost','Gmt Dyeing Cost','Washing Cost','Commission Cost','Commercial Cost','Freight Cost','Testing Cost','Inspection Cost','Courier Cost','Certificate Cost','Deffd. LC Cost','Design Cost','Studio Cost','Opert. Exp.','CM Cost','Interest','Income Tax','Depc. & Amort.','Others Cost','Total Cost','Margin/Loss','% to Ex-Factory Value','CPA/Short Fab. Cost','Net Margin/Loss');

	$act_title=array(4=>'Yarn Dyeing With Order, Yarn Service Work Order(Rate): Yarn Issue: Yarn Dyeing/Twisting/Re-Waxing Cost - Issue Ret:', 5=>'Knitting Bill Entry For Gross.', 6=>'Avg Knitting charge from Budget+Transfer Qty', 7=>'Grey Yarn Cost+Knitting Cost+Grey Fabric Transfer Cost', 8=>'From Dyeing n Finishing Bill Issue.', 9=>'From Service Booking For AOP V2', 10=>'Avg Dye Fin charge from Budget+Transfer Qty', 11=>'From: Partial Fabric Booking', 12=>'From: Partial Fabric Booking', 13=>'Grey Fabric Cost+Dye & Fin Cost+AOP Cost+Fin. Fab. Transfer Cost', 14=>'Fabric Purchase Cost + Finished Fabric Cost', 16=>'Multiple Job Wise Embellishment Work Order', 17=>'Multiple Job Wise Embellishment Work Order', 20=>'Foreign Commisson come from Export Invoice & Local Commission come from Actual cost entry.', 21=>'From Actual Cost Entry', 22=>'From Actual Cost Entry', 23=>'From Actual Cost Entry', 24=>'From Actual Cost Entry', 25=>'From Actual Cost Entry', 28=>'From Actual Cost Entry', 30=>'From Actual Cost Entry', 31=>'From Actual Cost Entry', 35=>'From Actual Cost Entry', 37=>'Ex-Factory Value - Actual all cost', 38=>'Margin /Ex-Fact Value*100', 39=>'Short Booking, Yarn Issue for Grey + Dyed Yarn amount');
	/* echo '<pre>';
	print_r($main_data_arr); die; */

	ob_start();
	?>
	<table width="5530">
		<tr class="form_caption">
			<td colspan="36" align="center"><strong>Multi Style Wise Post Costing Report</strong></td>
		</tr>
		<tr class="form_caption">
			<td colspan="36" align="center"><strong><? echo $company_arr[$company_name];?></strong>
				<br>
				<strong><? echo change_date_format(str_replace("'","",$txt_date_from)) .' To '. change_date_format(str_replace("'","",$txt_date_to)); ?></strong>
			</td>
		</tr>
	</table>
	<table style="margin-top:10px" id="table_header_1" class="rpt_table" width="5530" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
			<th width="40">SL</th>
			<th width="70">Buyer</th>
			<th width="60">Job Year</th>
			<th width="70">Job No</th>
			<th width="70">File No</th>
			<th width="80">Ref. No</th>
			<th width="110">Style Name</th>
			<th width="120">Garments Item</th>
			<th width="90">Job Quantity</th>
			<th width="50">UOM</th>
			<th width="70">Unit Price</th>
			<th width="110">Job Value</th>
			<th width="100">SMV</th>
			<th width="80">Last Ship Date</th>
			<th width="100">Shipping Status</th>

			<th width="100">Cost Source</th>
			<? foreach($heading_arr as $key=>$heading){ ?>
			<th width="110" id="row_<?= $key+1 ?>"><?= $heading ?></th>
			<? } ?>			
		</thead>
		<? 
		$i=1;
		foreach($main_data_arr as $job_id=>$val){
			$total_job_qty+=$val['job_quantity'];
			$total_job_value+=$val['total_price'];
			?>
			<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
				<td rowspan="4" valign="middle" align="center"><? echo $i; ?></td>
				<td rowspan="4" valign="middle" align="center"><? echo $buyer_arr[$val['buyer_name']]; ?></td>
				<td rowspan="4" valign="middle" align="center"><? echo $val['year']; ?></td>
				<td rowspan="4" valign="middle" align="center"><? echo $val['job_no_prefix_num']; ?></td>
				<td rowspan="4" valign="middle" align="center"><? echo $val['file_no']; ?></td>
				<td rowspan="4" valign="middle" align="center"><? echo implode(",",$val['grouping']); ?></td>
				<td rowspan="4" valign="middle" align="center"><? echo $val['style_ref_no']; ?></td>
				<td rowspan="4" valign="middle" align="center"><? echo implode(",",$val['gmts_item']); ?></td>
				<td rowspan="4" valign="middle" align="center"><? echo $val['job_quantity']; ?></td>
				<td rowspan="4" valign="middle" align="center"><? echo $unit_of_measurement[$val['order_uom']]; ?></td>
				<td rowspan="4" valign="middle" align="center"><? echo $val['avg_unit_price']; ?></td>
				<td rowspan="4" valign="middle" align="center"><? echo $val['total_price']; ?></td>
				<td rowspan="4" valign="middle" align="center"><? echo $val['set_smv']; ?></td>
				<td rowspan="4" valign="middle" align="center"><? echo $val['ship_date']; ?></td>
				<td rowspan="4" valign="middle" align="center"><? echo implode(",",$val['shiping_status']); ?></td>
				<td align="left"><b>Pre Costing</b></td>
				<? for($z=1; $z<=40; $z++){
					if($z==4){?>
						<td align="right" ><a href="#report_details" onClick="openmypage_mkt('<? echo $job_id."**".$row[csf('job_no')]; ?>','mkt_yarn_dyeing_twisting_cost','Yarn Dyeing & Twisting Cost Details',420)"><? echo fn_number_format($val['pre'][$z],0,'.',''); ?></a></td>
					<?	} else{ ?>
					<td align="right"><? echo fn_number_format($val['pre'][$z],2,'.',''); ?></td>
					<? 
					}
					$buyer_wise_cost[$val['buyer_name']]['pre'][$z]+=$val['pre'][$z];
					$total_summary_cost['pre'][$z]+=$val['pre'][$z];
				} 
				?>
			</tr>
			<tr bgcolor="#F5F5F5" onClick="change_color('tr_a<? echo $i; ?>','#F5F5F5')" id="tr_a<? echo $i; ?>">
				<td align="left"><b>Actual</b></td>
				<? for($z=1; $z<=40; $z++){
					if($z==1){
					?>
						<td align="right" title="<?= $act_title[$z] ?>"><a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $row[csf('job_no_prefix_num')];?>','<? echo $job_id; ?>','650px')"><? echo fn_number_format($val['act'][$z],2,'.',''); ?></a></td>
					<?	
					}
					else if($z==3){
					?>
						<td align="right" title="<?= $act_title[$z] ?>"><a href="#report_details" onClick="openmypage_actual2('<? echo $job_id; ?>','yarn_cost_actual','Actual Yarn Cost Details','1','1020px')"><? echo fn_number_format($val['act'][$z],2,'.',''); ?></a></td>
					<? } 
					else if($z==4){
					?>
						<td align="right" title="<?= $act_title[$z] ?>"><a href="#report_details"  onClick="openmypage_actual2('<? echo $job_id; ?>','yarn_dye_twist_cost_actual','Actual Yarn Dyeing Twsit Cost Details','2','1130px')"><? echo fn_number_format($val['act'][$z],2,'.',''); ?></a></td>
					<? }
					else if($z==5){
					?>
						<td align="right" title="<?= $act_title[$z] ?>"><a href="#report_details" onClick="openmypage_actual3('<? echo $job_id; ?>','knit_cost_actual_popup','Actual Knitting Cost Details','900px')"><? echo fn_number_format($val['act'][$z],2,'.',''); ?></a></td>
					<? }
					else if($z==8){
					?>
						<td align="right" title="<?= $act_title[$z] ?>"><a href="#report_details" onClick="openmypage_actual3('<? echo $job_id; ?>','dye_finish_cost_actual_popup','Actual Dyeing & Finish Cost Details','800px')"><? echo fn_number_format($val['act'][$z],2,'.',''); ?></a></td>
					<? }
					else if($z==15){
					?>
						<td align="right" title="<?= $act_title[$z] ?>"><a href="#report_details" onClick="openmypage_actual('<? echo $job_id; ?>','trims_cost_actual','Trims Cost Details','800px')"><? echo fn_number_format($val['act'][$z],2,'.',''); ?></a></td>
					<? }
					else if($z==16){
					?>
						<td align="right" title="<?= $act_title[$z] ?>"><a href="#report_details" onClick="openmypage_gmt_actual('<? echo $job_id; ?>','1','gmt_print_wash_dye_embell_cost_actual','Print Cost Details','800px')"><? echo fn_number_format($val['act'][$z],2,'.',''); ?></a></td>
					<? }
					else if($z==17){
					?>
						<td align="right" title="<?= $act_title[$z] ?>"><a href="#report_details" onClick="openmypage_gmt_actual('<? echo $job_id; ?>','2','gmt_print_wash_dye_embell_cost_actual','Embroidery Cost Details','800px')"><? echo fn_number_format($val['act'][$z],2,'.',''); ?></a></td>
					<? }					
					else if($z==20){
					?>
						<td align="right" title="<?= $act_title[$z] ?>"><a href="#report_details" onClick="openmypage_actual('<? echo $job_id."_".$row[csf('job_no')]."_".$ex_factory_qty."_".$dzn_qnty; ?>','commission_cost_actual','Commission Cost Details','600px')"><? echo fn_number_format($val['act'][$z],2,'.',''); ?></a></td>
					<? }
					else{ ?>
						<td align="right" title="<?= $act_title[$z] ?>"><? echo fn_number_format($val['act'][$z],2,'.',''); ?></td>
					<? 
					}
				$buyer_wise_cost[$val['buyer_name']]['act'][$z]+=$val['act'][$z];
				$total_summary_cost['act'][$z]+=$val['act'][$z];
				} 
				?>
			</tr>
			<tr bgcolor="#E9E9E9" onClick="change_color('tr_v<? echo $i; ?>','#E9E9E9')" id="tr_v<? echo $i; ?>">
				<td align="left"><b>Variance</b></td>
				<? for($z=1; $z<=40; $z++){ 
					$variance=$val['pre'][$z]-$val['act'][$z];
					?>
					<td align="right" ><? echo fn_number_format($variance,2,'.',''); ?></td>
				<? } ?>
			</tr>
			<tr bgcolor="#DFDFDF" onClick="change_color('tr_vp<? echo $i; ?>','#DFDFDF')" id="tr_vp<? echo $i; ?>">
				<td align="left"><b>Variance (%)</b></td>
				<? for($z=1; $z<=40; $z++){
					$variance=$val['pre'][$z]-$val['act'][$z];
					$variance_percnet=($variance/$val['pre'][$z])*100;
					 ?>
					<td align="right" ><? echo fn_number_format($variance_percnet,2,'.',''); ?></td>
				<? } ?>
			</tr>

			<? 
			$i++;
		} 
		?>
		<tr bgcolor="#CCDDEE" onClick="change_color('tr_pt','#CCDDEE')" id="tr_pt" style="font-weight:bold;">
			<td align="center"></td>
			<td align="center"></td>
			<td align="center"></td>
			<td align="center"></td>
			<td align="center"></td>
			<td align="center"></td>
			<td align="center"></td>
			<td align="center">Total</td>
			<td align="center"><?= $total_job_qty ?></td>
			<td align="center"></td>
			<td align="center"></td>
			<td align="center"><?= $total_job_value ?></td>
			<td align="center"></td>
			<td align="center"></td>
			<td align="center"></td>
			<td align="left"><b>Pre Costing</b></td>
			<? for($z=1; $z<=40; $z++){ ?>
				<td width="100" align="right" ><? echo fn_number_format($total_summary_cost['pre'][$z],0,'.',''); ?></td>
			<? } ?>
		</tr>
		<tr bgcolor="#CCCCFF" onClick="change_color('tr_at','#CCCCFF')" id="tr_at" style="font-weight:bold;">
			<? for($k=1; $k<=15; $k++){ ?>
			<td align="center"></td>
			<? } ?>
			<td align="left"><b>Actual Total</b></td>
			<? for($z=1; $z<=40; $z++){ ?>
				<td width="100" align="right" ><? echo fn_number_format($total_summary_cost['act'][$z],0,'.',''); ?></td>
			<? } ?>
		</tr>
		<tr bgcolor="#CCCCFF" onClick="change_color('tr_vt','#CCCCFF')" id="tr_vt" style="font-weight:bold;">
			<? for($k=1; $k<=15; $k++){ ?>
			<td align="center"></td>
			<? } ?>
			<td align="left"><b>Variance Total</b></td>
			<? for($z=1; $z<=40; $z++){ 
				$total_variance=$total_summary_cost['pre'][$z]-$total_summary_cost['act'][$z];
				?>
				<td width="100" align="right" ><? echo fn_number_format($total_variance,0,'.',''); ?></td>
			<? } ?>
		</tr>
		<tr bgcolor="#FFEEFF" onClick="change_color('tr_vt','#FFEEFF')" id="tr_vt" style="font-weight:bold;">
			<? for($k=1; $k<=15; $k++){ ?>
			<td align="center"></td>
			<? } ?>
			<td align="left"><b>Variance Total</b></td>
			<? for($z=1; $z<=40; $z++){
				 $total_variance=$total_summary_cost['pre'][$z]-$total_summary_cost['act'][$z];
				 $total_variance_per=($total_variance/$total_summary_cost['pre'][$z])*100;
				 ?>
				<td width="100" align="right" ><? echo fn_number_format($total_variance_per,0,'.',''); ?></td>
			<? } ?>
		</tr>
	</table>
		
 	<?
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_name."  and ENTRY_FORM=144");
	oci_commit($con);
	disconnect($con);
	foreach (glob("../../../../ext_resource/tmp_report/$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$html=ob_get_contents();
	$name=time();
	$filename="../../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	$filename="../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	echo "$total_data****$filename****$report_type";
	exit();
}