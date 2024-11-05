<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(id)
		{
			$('#hide_job_no').val( id );
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
						<th>
							<input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
							<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
							<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
						</th>
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
								?>
							</td>
							<td align="center">
								<?
								$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Order No");
								$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $type; ?>', 'create_job_no_search_list_view', 'search_div', 'job_wise_dying_cost_summery_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();  }

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$pop_type=$data[5];
	//echo $month_id;

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

	if($search_by==2) $search_field="a.style_ref_no"; else $search_field="a.job_no";

	$arr=array (0=>$company_arr,1=>$buyer_arr);

	if($db_type==0)
	{
		if($year_id!=0) $year_search_cond=" and year(a.insert_date)=$year_id"; else $year_search_cond="";
		$year_cond= "year(a.insert_date)as year";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_search_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id"; else $year_search_cond="";
		$year_cond= "TO_CHAR(a.insert_date,'YYYY') as year";
	}
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
    $buyer_library=return_library_array( "select id,buyer_name from   lib_buyer", "id","buyer_name" );
	$arr=array(0=>$companyArr,1=>$buyer_library);
	if($pop_type==2)
	{
		$sql= "select b.id, b.po_number, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $year_cond from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_search_cond 
		order by a.job_no DESC";
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No,PO No", "130,80,50,50,100","620","270",0, $sql , "js_set_value", "id,po_number,style_ref_no", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number", "",'','0,0,0,0,0','') ;
	}
	else
	{
		$sql= "select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $year_cond from wo_po_details_master a where a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_search_cond order by a.job_no DESC";
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "150,150,80,60","620","270",0, $sql , "js_set_value", "id,job_no,style_ref_no", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
	}
	
	exit();
}


if($action=="booking_no_popup")
{
	echo load_html_head_contents("Booking Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>
		function js_set_value( str ) 
		{
			$('#hide_booking_no').val( str );
			//alert(str);return;
			parent.emailwindow.hide();
		}
	</script>

</head>

<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:680px;">
				<table width="670" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Buyer</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Booking No</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
						<input type="hidden" name="hide_booking_no" id="hide_booking_no" value="" />
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
								?>
							</td>

							<td align="center">
								<?
								$search_by_arr=array(1=>"Booking No",2=>"Job No",3=>"Style Ref");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "1",$dd,0 );
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>', 'create_booking_no_search_list_view', 'search_div', 'job_wise_dying_cost_summery_controller', 'setFilterGrid(\'tbl_list_div\',-1)');" style="width:100px;" />
							</td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_booking_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_id_cond2=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond="";$buyer_id_cond2="";
			}
		}
		else
		{
			$buyer_id_cond="";$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";
		$buyer_id_cond2=" and a.buyer_id=$data[1]";
	}




	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==3)
	{
		$search_field="a.style_ref_no";
	}
	else if($search_by==2)
	{
		$search_field="a.job_no_prefix_num";
	}
	else $search_field="b.booking_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field_by="and YEAR(a.insert_date)";
	else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
	else $year_field_by="";
	if($db_type==0) $month_field_by="and month(a.insert_date)";
	else if($db_type==2) $month_field_by=" and to_char(a.insert_date,'MM')";
	else $month_field_by="";
	if($db_type==0) $year_field=" YEAR(a.insert_date) as year";
	else if($db_type==2) $year_field="  to_char(a.insert_date,'YYYY') as year";
	else $year_field="";

	if($year_id!=0) $year_cond=" $year_field_by=$year_id"; else $year_cond="";
	if($month_id!=0) $month_cond=" $month_field_by=$month_id"; else $month_cond="";

	$sql= "select a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.booking_no, c.booking_no_prefix_num, c.id as booking_id  
	from wo_po_details_master a, wo_booking_dtls b ,wo_booking_mst c 
	where a.job_no=b.job_no and b.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.booking_type in(1,4) and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond $month_cond group by a.job_no,b.booking_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,c.id,c.booking_no_prefix_num  order by a.job_no desc";

	//echo $sql;die;
	$sqlResult=sql_select($sql);
	?>
	<div align="center">

		<fieldset style="width:650px;margin-left:10px">
			<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="670" class="rpt_table">
					<thead>
						<th width="30">SL</th>
						<th width="130">Company</th>
						<th width="110">Buyer</th>
						<th width="110">Job No</th>
						<th width="120">Style Ref.</th>
						<th width="">Booking No</th>

					</thead>
				</table>
				<div style="width:670px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="tbl_list_div">
						<?
						$i=1;
						foreach($sqlResult as $row )
						{
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

							$data=$row[csf('booking_id')].'_'.$row[csf('booking_no')];
						//echo $data;
							?>
							<tr id="tr_<?php echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onClick="js_set_value('<? echo $data;?>')">
								<td width="30" align="center"><?php echo $i; ?>
								<td width="130"><p><? echo $company_arr[$row[csf('company_name')]]; ?></p></td>
								<td width="110"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
								<td width="110"><p><? echo $row[csf('job_no')]; ?></p></td>
								<td width="120"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
								<td width=""><p><? echo $row[csf('booking_no')]; ?></p></td>
							</tr>
							<?
							$i++;
						}
						?>
					</table>
				</div>
			</form>
		</fieldset>

	</div>


	<?

	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year*txt_job_no*txt_job_id*txt_po_no*txt_po_id*txt_style_no*txt_booking_no*txt_booking_id',"../../../")+'&report_title='+report_title;
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_year=str_replace("'","",$cbo_year);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_id=str_replace("'","",$txt_job_id);
	$txt_po_no=str_replace("'","",$txt_po_no);
	$txt_po_id=str_replace("'","",$txt_po_id);
	$txt_style_no=str_replace("'","",$txt_style_no);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$txt_booking_id=str_replace("'","",$txt_booking_id);
	
	$buyer_library=return_library_array( "select id,buyer_name from   lib_buyer", "id","buyer_name" );
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$sql_cond="";
	if($cbo_buyer_name>0) $sql_cond.=" and a.BUYER_NAME=$cbo_buyer_name";
	if($txt_job_no !="") $sql_cond.=" and a.JOB_NO='$txt_job_no'";
	if($txt_po_no !="") $sql_cond.=" and b.ID='$txt_po_id'";
	if($txt_style_no !="") $sql_cond.=" and a.STYLE_REF_NO='$txt_style_no'";
	if($txt_booking_no !="") $sql_cond.=" and D.BOOKING_NO='$txt_booking_no'";
	
	if($db_type==0) $year_cond=" and year(a.INSERT_DATE)=$cbo_year"; else $year_cond=" and to_char(a.INSERT_DATE,'YYYY')='$cbo_year'";
	$batch_sql="select a.ID as JOB_ID, a.JOB_NO, a.JOB_NO_PREFIX_NUM, a.BUYER_NAME, a.STYLE_REF_NO, b.ID as PO_ID, b.PO_NUMBER, d.ID as BATCH_ID, d.BATCH_NO, D.BOOKING_NO 
	from WO_PO_DETAILS_MASTER a, WO_PO_BREAK_DOWN b, PRO_BATCH_CREATE_DTLS c, PRO_BATCH_CREATE_MST d
	where a.job_no=b.job_no_mst and b.id=c.po_id and c.mst_id=d.id and d.entry_form=0 and a.COMPANY_NAME=$cbo_company_name $sql_cond $year_cond";
	$batch_sql_result=sql_select($batch_sql);
	if(count($batch_sql_result)==0)
	{
		echo "NO Data Found";die;
	}
	$job_data="";$batch_ids="";
	foreach($batch_sql_result as $row)
	{
		$job_data="Buyer : ".$buyer_library[$row["BUYER_NAME"]].", Job No : ".$row["JOB_NO"].", Style Ref : ".$row["STYLE_REF_NO"];
		$po_data[$row["PO_ID"]]=$row["PO_NUMBER"];
		if($batch_check[$row["BATCH_ID"]]=="")
		{
			$batch_check[$row["BATCH_ID"]]=$row["BATCH_ID"];
			$batch_ids.="'".$row["BATCH_ID"]."',";
		}
	}
	unset($batch_sql_result);
	$job_data.=", PO No : ".implode(",",$po_data);
	$batch_ids=chop($batch_ids,",");
	//echo $job_data."<br>".$batch_ids;die;
	$sql="select c.ID as PROD_ID, c.PRODUCT_NAME_DETAILS, c.ITEM_CATEGORY_ID, c.UNIT_OF_MEASURE, b.CONS_QUANTITY, b.CONS_AMOUNT, a.BATCH_NO from INV_ISSUE_MASTER a, INV_TRANSACTION b, PRODUCT_DETAILS_MASTER c where a.id=b.mst_id and b.prod_id=c.id and a.entry_form=5 and a.ISSUE_BASIS in(5,7) and b.transaction_type = 2 and c.item_category_id in(5,6,7,23) and a.BATCH_NO in($batch_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	$sql_result=sql_select($sql);
	$dtls_data=array();
	foreach($sql_result as $row)
	{
		$dtls_data[$row["ITEM_CATEGORY_ID"]][$row["PROD_ID"]]["BATCH_ID"].=$row["BATCH_NO"].",";
		$dtls_data[$row["ITEM_CATEGORY_ID"]][$row["PROD_ID"]]["PRODUCT_NAME_DETAILS"]=$row["PRODUCT_NAME_DETAILS"];
		$dtls_data[$row["ITEM_CATEGORY_ID"]][$row["PROD_ID"]]["UNIT_OF_MEASURE"]=$row["UNIT_OF_MEASURE"];
		$dtls_data[$row["ITEM_CATEGORY_ID"]][$row["PROD_ID"]]["CONS_QUANTITY"]+=$row["CONS_QUANTITY"];
		$dtls_data[$row["ITEM_CATEGORY_ID"]][$row["PROD_ID"]]["CONS_AMOUNT"]+=$row["CONS_AMOUNT"];
	}
	unset($sql_result);
	ob_start();	
	?>
	<div align="left" style="height:auto; width:820px; margin:0 auto; padding:0;">
        <table width="820" cellpadding="0" cellspacing="0" id="caption" align="left">
            <thead>
                <tr style="border:none;">
                    <td colspan="7" class="form_caption" align="center" style="border:none; font-size:14px;">
                       <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>                               
                    </td>
                </tr>
                <tr style="border:none;">
                    <td colspan="7" align="center" class="form_caption" style="border:none;font-size:12px; font-weight:bold"><? echo $report_title; ?></td>
                </tr>
                <tr><td colspan="7">&nbsp;</td></tr>
            </thead>
        </table>
        <table width="800" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="caption" align="left">
        	<tr><td colspan="7" style="font-size:16px;"><? echo $job_data; ?></td></tr>
        </table>
        <table width="800" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="caption" align="left">
        	<thead>
                <tr>                         
                    <th width="50">SL</th>
                    <th width="70">Product Id</th>
                    <th width="200">Item Description</th>
                    <th width="100">UOM</th>
                    <th width="130">Quantity</th>
                    <th width="130">Avg. Rate</th>
                    <th>Amount(BDT)</th>
                </tr> 
            </thead>
        </table>
        <?
        //die;
		?>
        <div style="width:820px; max-height:400px;  overflow-y:scroll" id="scroll_body" align="left">
        <table width="800" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_body_id" align="left">
            <tbody>
		    <?
			$i=1;
            $total_qnty=$total_amt=0;
            foreach($dtls_data as $category_id=>$category_data)
            {
				$cat_tot_qnty=$cat_tot_amt=0;
				foreach($category_data as $prod_id=>$val)
				{
					if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
					$avg_rate=$val['CONS_AMOUNT']/$val['CONS_QUANTITY'];
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="50" align="center"><? echo $i; ?></td>								
                        <td width="70" align="center"><? echo $prod_id; ?></td>
                        <td width="200"><? echo $val['PRODUCT_NAME_DETAILS']; ?></td>
                        <td width="100" align="center"><? echo $unit_of_measurement[$val['UNIT_OF_MEASURE']]; ?></td>
                        <td width="130" align="right"><a href="##"  onClick="fn_1st_batch('<? echo implode(",",array_unique(explode(",",chop($val['BATCH_ID'],",")))); ?>','1st_batch_dtls_popup')"><? echo number_format($val['CONS_QUANTITY'],2);?></a></td>
                        <td width="130" align="right" title=""><? echo number_format($avg_rate,2);?></td>
                        <td align="right"><?  echo number_format($val['CONS_AMOUNT'],2);?></td>
                    </tr>
                    <?
					$cat_tot_qnty+=$val['CONS_QUANTITY'];
					$cat_tot_amt+=$val['CONS_AMOUNT'];
					$total_qnty+=$val['CONS_QUANTITY'];
					$total_amt+=$val['CONS_AMOUNT'];
					$i++;
				}
                ?>
                <tr bgcolor="#FFFFCC">
                	<th>&nbsp;</th>
                	<th>&nbsp;</th>
                    <th>&nbsp;</th>
                	<th align="right">Total</th>
                	<th align="right"><? echo number_format($cat_tot_qnty,2);?></th>
                    <th align="right"><? echo number_format(($cat_tot_amt/$cat_tot_qnty),2);?></th>
                    <th align="right"><? echo number_format($cat_tot_amt,2);?></th>
                </tr>
                <? 												
            }
            ?>
            </tbody>
            <tfoot>
            	<tr bgcolor="#CCCCCC">
                	<th>&nbsp;</th>
                	<th>&nbsp;</th>
                    <th>&nbsp;</th>
                	<th align="right">Gr. Total</th>
                	<th align="right"><? echo number_format($total_qnty,2);?></th>
                    <th align="right"><? echo number_format(($total_amt/$total_qnty),2);?></th>
                    <th align="right"><? echo number_format($total_amt,2);?></th>
                </tr>
            </tfoot>
      	</table>
        </div>
  	</div>
    <?
    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename"; 
    exit();
}

if($action=="1st_batch_dtls_popup")
{
	echo load_html_head_contents("Batch Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $batch_id;die;
	$batch_non_redyeing_id=return_field_value("id","pro_batch_create_mst","batch_against<>2 and status_active=1 and id in($batch_id)","id");
	if($batch_non_redyeing_id=="")die;
	
	if($batch_non_redyeing_id!="") $batch_cond=" and a.batch_no like '$batch_non_redyeing_id'";
	
	$sql = "select a.ID, a.BATCH_NO, a.BATCH_DATE
	from pro_batch_create_mst a
	where a.status_active=1 and a.is_deleted=0 and a.id in($batch_id)"; 
	//echo $sql_dtls;die;
	$sql_result= sql_select($sql);
	?>
	<div style="width:320px;">
    <table align="center" cellspacing="0" width="300" border="1" rules="all" class="rpt_table" >
        <thead align="center">
    	   <tr>
                <th width="50">SL</th>
                <th width="150">Batch No</th>
                <th>Batch Date</th>
           </tr>
		</thead>
		<?  
		$i=1;
		foreach($sql_result as $row)
		{
			if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
			?>
			<tbody>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center"><? echo $i; ?></td>
					<td align="center"><? echo $row["BATCH_NO"]; ?></td>
					<td><? echo change_date_format($row["BATCH_DATE"]); ?></td>
				</tr>
			</tbody>
			<? 
			$i++;
		}
		?>
      </table>
    </div>
    <?         
	exit();
}


?>