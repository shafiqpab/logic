<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
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
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>', 'create_job_no_search_list_view', 'search_div', 'yarn_allocation_history_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
		$buyer_id_cond=" and buyer_name=$data[1]";
	}
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";
	
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by job_no";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
	exit(); 
} // Job Search end

if ($action=="booking_no_popup")
{
	echo load_html_head_contents("Order Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	$ex_data=explode('_',$data);
	$company_id=$ex_data[0];
	$buyer_id=$ex_data[1];
?>
	<script>
		function js_set_value(wo_id,wo_no)
		{
			document.getElementById('txt_wo_no').value=wo_no;
			document.getElementById('txt_wo_id').value=wo_id;
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
	<fieldset style="width:600px;margin-left:10px">
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="600" class="rpt_table" align="center">
                <thead>
                    <th>Buyer</th>
                    <th>Please Enter Booking No</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="txt_wo_no" id="txt_wo_no" value="" style="width:50px">
                        <input type="hidden" name="txt_wo_id" id="txt_wo_id" value="" style="width:50px">
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">
                        <?
							echo create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $buyer_id, "" );   	 
                        ?>       
                    </td>
                    <td align="center">				
                        <input type="text" style="width:150px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_id').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('txt_search_common').value, 'create_wo_search_list_view', 'search_div', 'yarn_allocation_history_report_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
                    </td>
                </tr>
            </table>
            <div id="search_div" style="margin-top:10px"></div>   
        </form>
    </fieldset>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if ($action=="create_wo_search_list_view")
{
	$data=explode('_',$data);
	echo $data;
	if ($data[0]==0) $buyer_id=""; else $buyer_id=" and buyer_id=$data[0]";
	if ($data[1]==0) $company_id=""; else $company_id=" and company_id=$data[1]";
	if ($data[2]==0) $search_wo=""; else $search_wo=" and booking_no_prefix_num=$data[2]";
	/*if($db_type==0)
	{
		if ($data[3]==0) $year_id_cond=""; else $year_id_cond=" and YEAR(insert_date)=$data[3]";
	}
	elseif($db_type==2)
	{
		if ($data[3]==0) $year_id_cond=""; else $year_id_cond=" and TO_CHAR(insert_date,'YYYY')=$data[3]";
	}
	
	if ($data[4]==0) $category_id_cond=""; else $category_id_cond=" and item_category=$data[4]";
	if ($data[5]==1 || $data[5]==2)  $wo_type_cond=" and booking_type in (1,2) and is_short='$data[5]'"; else $wo_type_cond="";
	if ($data[5]==3) $wo_type_cond_sam="  and booking_type=4"; else $wo_type_cond_sam="";
	
	*/
	if($db_type==0)
	{
		$year=" YEAR(insert_date) as year";
	}
	elseif($db_type==2)
	{
		$year=" TO_CHAR(insert_date,'YYYY') as year";
	}
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	/*if($data[5]==0)
	{*/
		$sql= "select id, booking_no, $year, booking_no_prefix_num, booking_date, buyer_id, booking_type, is_short
		from wo_booking_mst
		where status_active=1 and is_deleted=0 $company_id $buyer_id $year_id_cond $search_wo and booking_type=1 and is_short=2
		order by id Desc";
 
		/*echo $sql= "select id, booking_no, $year, booking_no_prefix_num, booking_date, buyer_id, booking_type, is_short, 0 as type from wo_booking_mst where status_active=1 and is_deleted=0 $company_id $buyer_id $year_id_cond 
		union all
		SELECT id, booking_no, $year, booking_no_prefix_num, booking_date, buyer_id, booking_type, is_short, 1 as type from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0 $company_id $buyer_id $year_id_cond ";*/
		//$search_wo $category_id_cond $wo_type_cond $wo_type_cond_sam  $search_wo $category_id_cond

	/*}
	else if ($data[5]==1 || $data[5]==2 || $data[5]==3)
	{
		$sql= "select id, booking_no, $year, booking_no_prefix_num, booking_date, buyer_id, booking_type, is_short, 0 as type from wo_booking_mst where status_active=1 and is_deleted=0 $company_id $buyer_id $year_id_cond $search_wo $category_id_cond $wo_type_cond $wo_type_cond_sam";
	}
	else
	{
		$sql= "SELECT id, booking_no, $year, booking_no_prefix_num, booking_date, buyer_id, booking_type, is_short, 1 as type from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0 $company_id $buyer_id $year_id_cond $search_wo $category_id_cond";
	}*/
	//echo $sql;

?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="80">WO No </th>
                <th width="80">Year</th>
                <th width="130">WO Type</th>
                <th width="150">Buyer</th>
                <th width="100">WO Date</th>
            </thead>
        </table>
        <div style="width:618px; overflow-y:scroll; max-height:300px;" id="" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="list_view" >
            <?
				$i=1;
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					if ($selectResult[csf("type")]==0)
					{	
						if ($selectResult[csf("booking_type")]==1 || $selectResult[csf("booking_type")]==2)
						{
							if ($selectResult[csf("is_short")]==1)
							{
								$wo_type="Short";
							}
							else
							{
								$wo_type="Main";
							}
						}
						elseif($selectResult[csf("booking_type")]==4)
						{
							$wo_type="Sample With Order";
						}
					}
					else
					{
						$wo_type="Sample Non Order";
					}					
				?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>,'<? echo $selectResult[csf('booking_no')]; ?>')"> 
                        <td width="30" align="center"><? echo $i; ?></td>	
                        <td width="80" align="center"><p><? echo $selectResult[csf('booking_no_prefix_num')]; ?></p></td>
                        <td width="80" align="center"><? echo $selectResult[csf('year')]; ?></td>
                        <td width="130"><p><? echo $wo_type; ?></p></td>
                        <td width="150"><p><? echo $buyerArr[$selectResult[csf('buyer_id')]]; ?></p></td>
                        <td  width="100" align="center"><? echo change_date_format($selectResult[csf('booking_date')]); ?></td>	
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

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$job_no=str_replace("'","",$txt_job_no);
	$order_no=str_replace("'","",$txt_order_no);
	$cbo_job_year_id=str_replace("'","",$cbo_job_year_id);
	$txt_booking_no=str_replace("'","",trim($txt_booking_no));//die;
	//echo $date_from."sssss";die;
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier=return_library_array( "select id, short_name from  lib_supplier",'id','short_name');
	$yarn_count=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_arr=return_library_array( "select id,color_name from lib_color","id","color_name");
    $user_name = return_library_array("select id, user_name from user_passwd", 'id', 'user_name');
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	//--------------------------------------------------------------------------------------------------------------------
	
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and c.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and c.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and c.job_no_prefix_num in ('$job_no') ";
	if($order_no=="")
	{
		$po_cond="";
	}
	else
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id=str_replace("'","",$hide_order_id);
			$po_cond="and b.id in(".$po_id.")";
		}
		else
		{
			$po_number=trim($order_no)."%";
			$po_cond="and b.po_number like '$po_number'";
		}
	}
	$start_date_db=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
	
	if($db_type==0)
	{
		$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
		$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
	}
	else if($db_type==2)
	{
		$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
		$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
	}
	if($start_date=="" && $end_date=="")
	{
		$date_cond="";
	}
	else
	{
		$date_cond=" and b.allocation_date between '$start_date' and '$end_date'";
	}
	
	if($db_type==0) $lead_day="DATEDIFF(b.pub_shipment_date,b.po_received_date) as  date_diff";
	else if($db_type==2) $lead_day="(b.pub_shipment_date-b.po_received_date) as  date_diff";
	
	
	$prod_data=sql_select( "select id, product_name_details, supplier_id, lot, yarn_count_id, yarn_type, yarn_comp_type1st,	yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, color from product_details_master where item_category_id=1");
	$prod_data_arr=array();
	foreach($prod_data as $row)
	{
		$compos="";
		if($row[csf('yarn_comp_percent2nd')]!=0)
		{
			$compos=$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]." %"." ".$composition[$row[csf('yarn_comp_type2nd')]]." ".$row[csf('yarn_comp_percent2nd')]." %";
		}
		else
		{
			$compos=$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]." %"." ".$composition[$row[csf('yarn_comp_type2nd')]];
		}
		$prod_data_arr[$row[csf('id')]]['prod_details']=$compos;
		$prod_data_arr[$row[csf('id')]]['supp']=$row[csf('supplier_id')];
		$prod_data_arr[$row[csf('id')]]['lot']=$row[csf('lot')];
		$prod_data_arr[$row[csf('id')]]['yarn_count_id']=$row[csf('yarn_count_id')];
		$prod_data_arr[$row[csf('id')]]['yarn_type']=$row[csf('yarn_type')];
		$prod_data_arr[$row[csf('id')]]['color']=$row[csf('color')];
		
	}
	if($db_type==0) $year_field="YEAR(c.insert_date)"; 
	else if($db_type==2) $year_field="to_char(c.insert_date,'YYYY')";
	if($cbo_job_year_id) $year_field_cond = " and $year_field = '$cbo_job_year_id'";
	//if($txt_booking_no != "") $booking_cond = " and b.booking_no = '$txt_booking_no'";else $booking_cond = "";
	if ($txt_booking_no=="") $booking_cond=""; else $booking_cond=" and b.booking_no like '%$txt_booking_no%' ";

	if($db_type==0)
	{
		$sql_data=("SELECT b.mst_id,b.job_no, YEAR(b.insert_date) as year,b.booking_no,b.item_category,b.item_id,sum(b.qnty) as qnty,c.buyer_name, b.insert_date,c.remarks,c.company_name,b.status_active,b.inserted_by 
		FROM inv_mat_allocation_dtls_log b, wo_po_details_master c 
		where b.job_no=c.job_no and c.company_name=$company_id $buyer_id_cond $date_cond $job_no_cond $booking_cond $year_field_cond
		group by b.mst_id, b.item_category,b.item_id,b.job_no, c.insert_date ,b.booking_no,c.buyer_name, b.insert_date, c.remarks,c.company_name,b.inserted_by,b.status_active
		order by b.item_id"); 
		//echo $sql_data;
	}

	elseif($db_type==2)
	{
		/*$sql_data=("SELECT b.mst_id,b.job_no, to_char(b.insert_date,'YYYY') as year,b.booking_no,b.item_category,b.item_id,sum(b.qnty) as qnty,c.buyer_name, b.insert_date,c.remarks,c.company_name,b.status_active,b.inserted_by 
		FROM inv_mat_allocation_dtls_log b, wo_po_details_master c
		where b.job_no=c.job_no and c.company_name=$company_id $buyer_id_cond $date_cond $job_no_cond $booking_cond $year_field_cond
		group by b.mst_id, b.item_category,b.item_id,b.job_no, c.insert_date ,b.booking_no,c.buyer_name, b.insert_date, c.remarks,c.company_name,b.inserted_by,b.status_active
		order by b.item_id");*/

		$sql_data=("SELECT b.mst_id,b.job_no, to_char(b.insert_date,'YYYY') as year,b.booking_no,b.item_category,b.item_id,sum(b.qnty) as qnty,c.buyer_name, b.insert_date,a.remarks,c.company_name,b.status_active,b.inserted_by 
		FROM inv_mat_allocation_mst_log a, inv_mat_allocation_dtls_log b, wo_po_details_master c
		where b.job_no=c.job_no and c.company_name=$company_id $buyer_id_cond $date_cond $job_no_cond $booking_cond $year_field_cond and a.id=b.mst_id
		group by b.mst_id, b.item_category,b.item_id,b.job_no, c.insert_date ,b.booking_no,c.buyer_name, b.insert_date, a.remarks,c.company_name,b.inserted_by,b.status_active
		order by b.job_no,b.item_id");
		//echo $sql_data;
	}	
		
	$data_result=sql_select($sql_data);

	foreach ($data_result as $key => $value) 
	{
		$item_id_row_marge[$value[csf('job_no')]][$value[csf('item_id')]]++;
	}
	//echo "<pre>";print_r($booking_row_marge);//die();
	ob_start(); 
	?>
	<div align="center">
	   
	    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1722" class="rpt_table" align="left" id="table_header">
	        <thead>

	        	<tr class="form_caption" style="border:none;">
	        	    <td align="center" style="border:none; font-size:18px;" colspan="12">
	        	        <? echo $company_library[$company_id]; ?>                                
	        	    </td>
	        	</tr>
	        	<tr class="form_caption" style="border:none;">
	        	    <td align="center" style="border:none;font-size:16px; font-weight:bold"  colspan="12"> <? echo $report_title ;?></td>
	        	</tr>
	        	<tr class="form_caption" style="border:none;">
	        	    <td align="center" style="border:none;font-size:12px; font-weight:bold"  colspan="12"> <? if($start_date!="" && $end_date!="") echo "From ".change_date_format($start_date)." To ".change_date_format($end_date);?></td>
	        	</tr>

	        	<tr>
		            <th width="30"><p>SL</p></th>
		            <th width="100"><p>Buyer</p></th>
		            <th width="100"><p>Job No.</p></th>
		            <th width="60"><p>Year</p></th>
		            <th width="120"><p>Booking No</p></th>               
		            <th width="100"><p>Product ID</p></th>               
		            <th width="70"><p>Lot</p></th>
		            <th width="110"><p>Supplier</p></th>
		            <th width="100"><p>Count</p></th>
		            <th width="130"><p>Composition</p></th>
		            <th width="100"><p>Yarn Type</p></th>
		            <th width="100"><p>Color</p></th>
		            <th width="100">Quantity (kg)</th>
		            <th width="150"><p>Allocation Date & Time</p></th>
		            <th width="100"><p>User ID</p></th>
		            <th width="100"><p>Status</p></th>
		            <th width=""><p>Remarks</p></th>
	        	</tr>

	        </thead>
	    </table>

	    <div style="width:1740px; max-height:320px; overflow-y:scroll;float: left;"  id="scroll_body" align="left">	
		    
		    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1722" class="rpt_table" id="tbl_list_search" align="left">  

				<?
		        $i=1;
		        foreach( $data_result as $row)
		        {
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
			
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">

				
						<?
						
						if($item_id_marge_check[$row[csf('job_no')]][$row[csf('item_id')]]=="")
						{
							$item_id_marge_check[$row[csf('job_no')]][$row[csf('item_id')]]=$row[csf('item_id')];

							$rspn = $item_id_row_marge[$row[csf('job_no')]][$row[csf('item_id')]];

							?> 
			                <td rowspan="<? echo $rspn;;?>" width="30" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $i; ?></p></td>
			                <td rowspan="<? echo $rspn;;?>" width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
			                <td rowspan="<? echo $rspn;;?>" width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $row[csf('job_no')]; ?></p></td>
			                <td rowspan="<? echo $rspn;;?>" width="60" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $row[csf('year')]; ?></p></td>
			                <td rowspan="<? echo $rspn;;?>" width="120" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $row[csf('booking_no')]; ?></p></td>   
		                	<td rowspan="<? echo $rspn;;?>" width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $row[csf('item_id')]; ?></p></td>        
		                	
			                <?
			                $i++;
						}	
						$date = date_create($row[csf('insert_date')]);	
						$date_time = date_format($date,"d-m-Y h:i:s a");		
		                ?>     

		                <td width="70" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $prod_data_arr[$row[csf('item_id')]]['lot']; ?></p></td>
		                <td width="110" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p style="word-break: break-all;"><? echo $supplier[$prod_data_arr[$row[csf('item_id')]]['supp']]; ?></p></td>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $yarn_count[$prod_data_arr[$row[csf('item_id')]]['yarn_count_id']]; ?></p></td>
		                <td width="130" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p style="word-break: break-all;"><? echo $prod_data_arr[$row[csf('item_id')]]['prod_details']; ?></p></td>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p style="word-break: break-all;"><? echo $yarn_type[$prod_data_arr[$row[csf('item_id')]]['yarn_type']]; ?></p></td>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p style="word-break: break-all;"><? echo $color_arr[$prod_data_arr[$row[csf('item_id')]]['color']]; ?></p></td>
		                <td width="100" align="right">
							
							<? 
		                	echo number_format($row[csf('qnty')], 2);
		                	$total_allocation_qty += $row[csf('qnty')];
		                	?>
							
		                	
		                </td>
		                <td width="150" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $date_time; ?>&nbsp;</p></td>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $user_name[$row[csf('inserted_by')]]; ?>&nbsp;</p></td>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center">
		                	<?
		                		$status = $row[csf('status_active')];
		                		echo ($status == 1) ? "Active": "Delete";
		                	?>
		                <p>&nbsp;</p></td>
		                <td width="" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
					</tr>
					<?
					
		        }
		        ?>
		    </table>
	    </div>

	    <table width="1722" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">	
	        <tr class="tbl_bottom">
	        	<td width="30">&nbsp;</td>
	        	<td width="100">&nbsp;</td>
	        	<td width="100">&nbsp;</td>
	        	<td width="60">&nbsp;</td>
	        	<td width="120">&nbsp;</p></td>               
	        	<td width="100">&nbsp;</td>               
	        	<td width="70">&nbsp;</td>
	        	<td width="110">&nbsp;</td>
	        	<td width="100">&nbsp;</td>
	        	<td width="130">&nbsp;</td>
	        	<td width="100">&nbsp;</td>
	        	<td width="100">&nbsp;</td>
	        	<td width="100"  id="value_total_allocation_qty"><? echo number_format($total_allocation_qty,2);?>&nbsp;</td>
	        	<td width="150">&nbsp;</td>
	        	<td width="100">&nbsp;</td>
	        	<td width="100">&nbsp;</td>
	        	<td width="">&nbsp;</td>
	        </tr>
		</table>	

	</div>
    <?
	        
	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename="".$user_name."_".$name.".xls";
	echo "$total_data####$filename";
	
	exit();
		
}

if($action=="report_generate_20112022")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$job_no=str_replace("'","",$txt_job_no);
	$order_no=str_replace("'","",$txt_order_no);
	$cbo_job_year_id=str_replace("'","",$cbo_job_year_id);
	$txt_booking_no=str_replace("'","",trim($txt_booking_no));//die;
	//echo $date_from."sssss";die;
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier=return_library_array( "select id, short_name from  lib_supplier",'id','short_name');
	$yarn_count=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_arr=return_library_array( "select id,color_name from lib_color","id","color_name");
    $user_name = return_library_array("select id, user_name from user_passwd", 'id', 'user_name');
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	//--------------------------------------------------------------------------------------------------------------------
	
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and c.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and c.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and c.job_no_prefix_num in ('$job_no') ";
	if($order_no=="")
	{
		$po_cond="";
	}
	else
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id=str_replace("'","",$hide_order_id);
			$po_cond="and b.id in(".$po_id.")";
		}
		else
		{
			$po_number=trim($order_no)."%";
			$po_cond="and b.po_number like '$po_number'";
		}
	}
	$start_date_db=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
	
	if($db_type==0)
	{
		$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
		$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
	}
	else if($db_type==2)
	{
		$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
		$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
	}
	if($start_date=="" && $end_date=="")
	{
		$date_cond="";
	}
	else
	{
		$date_cond=" and b.allocation_date between '$start_date' and '$end_date'";
	}
	
	if($db_type==0) $lead_day="DATEDIFF(b.pub_shipment_date,b.po_received_date) as  date_diff";
	else if($db_type==2) $lead_day="(b.pub_shipment_date-b.po_received_date) as  date_diff";
	
	
	$prod_data=sql_select( "select id, product_name_details, supplier_id, lot, yarn_count_id, yarn_type, yarn_comp_type1st,	yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, color from product_details_master where item_category_id=1");
	$prod_data_arr=array();
	foreach($prod_data as $row)
	{
		$compos="";
		if($row[csf('yarn_comp_percent2nd')]!=0)
		{
			$compos=$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]." %"." ".$composition[$row[csf('yarn_comp_type2nd')]]." ".$row[csf('yarn_comp_percent2nd')]." %";
		}
		else
		{
			$compos=$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]." %"." ".$composition[$row[csf('yarn_comp_type2nd')]];
		}
		$prod_data_arr[$row[csf('id')]]['prod_details']=$compos;
		$prod_data_arr[$row[csf('id')]]['supp']=$row[csf('supplier_id')];
		$prod_data_arr[$row[csf('id')]]['lot']=$row[csf('lot')];
		$prod_data_arr[$row[csf('id')]]['yarn_count_id']=$row[csf('yarn_count_id')];
		$prod_data_arr[$row[csf('id')]]['yarn_type']=$row[csf('yarn_type')];
		$prod_data_arr[$row[csf('id')]]['color']=$row[csf('color')];
		
	}
	if($db_type==0) $year_field="YEAR(c.insert_date)"; 
	else if($db_type==2) $year_field="to_char(c.insert_date,'YYYY')";
	if($cbo_job_year_id) $year_field_cond = " and $year_field = '$cbo_job_year_id'";
	//if($txt_booking_no != "") $booking_cond = " and b.booking_no = '$txt_booking_no'";else $booking_cond = "";
	if ($txt_booking_no=="") $booking_cond=""; else $booking_cond=" and b.booking_no like '%$txt_booking_no%' ";

	if($db_type==0)
	{
		$sql_data=("SELECT b.mst_id,b.job_no, YEAR(b.insert_date) as year,b.booking_no,b.item_category,b.item_id,sum(b.qnty) as qnty,c.buyer_name, b.insert_date,c.remarks,c.company_name,b.status_active,b.inserted_by 
		FROM inv_mat_allocation_dtls_log b, wo_po_details_master c 
		where b.job_no=c.job_no and c.company_name=$company_id $buyer_id_cond $date_cond $job_no_cond $booking_cond $year_field_cond
		group by b.mst_id, b.item_category,b.item_id,b.job_no, c.insert_date ,b.booking_no,c.buyer_name, b.insert_date, c.remarks,c.company_name,b.inserted_by,b.status_active
		order by b.item_id"); 
		//echo $sql_data;
	}

	elseif($db_type==2)
	{
		/*$sql_data=("SELECT b.mst_id,b.job_no, to_char(b.insert_date,'YYYY') as year,b.booking_no,b.item_category,b.item_id,sum(b.qnty) as qnty,c.buyer_name, b.insert_date,c.remarks,c.company_name,b.status_active,b.inserted_by 
		FROM inv_mat_allocation_dtls_log b, wo_po_details_master c
		where b.job_no=c.job_no and c.company_name=$company_id $buyer_id_cond $date_cond $job_no_cond $booking_cond $year_field_cond
		group by b.mst_id, b.item_category,b.item_id,b.job_no, c.insert_date ,b.booking_no,c.buyer_name, b.insert_date, c.remarks,c.company_name,b.inserted_by,b.status_active
		order by b.item_id");*/

		$sql_data=("SELECT b.mst_id,b.job_no, to_char(b.insert_date,'YYYY') as year,b.booking_no,b.item_category,b.item_id,sum(b.qnty) as qnty,c.buyer_name, b.insert_date,a.remarks,c.company_name,b.status_active,b.inserted_by 
		FROM inv_mat_allocation_mst_log a, inv_mat_allocation_dtls_log b, wo_po_details_master c
		where b.job_no=c.job_no and c.company_name=$company_id $buyer_id_cond $date_cond $job_no_cond $booking_cond $year_field_cond and a.id=b.mst_id
		group by b.mst_id, b.item_category,b.item_id,b.job_no, c.insert_date ,b.booking_no,c.buyer_name, b.insert_date, a.remarks,c.company_name,b.inserted_by,b.status_active
		order by b.item_id");
		//echo $sql_data;
	}	
		
	$data_result=sql_select($sql_data);

	foreach ($data_result as $key => $value) 
	{		
		$item_id_row_marge[$value[csf('item_id')]]++;
	}
	//echo "<pre>";print_r($booking_row_marge);//die();
	ob_start(); 
	?>
	<div align="center">
	   
	    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1722" class="rpt_table" align="left" id="table_header">
	        <thead>

	        	<tr class="form_caption" style="border:none;">
	        	    <td align="center" style="border:none; font-size:18px;" colspan="12">
	        	        <? echo $company_library[$company_id]; ?>                                
	        	    </td>
	        	</tr>
	        	<tr class="form_caption" style="border:none;">
	        	    <td align="center" style="border:none;font-size:16px; font-weight:bold"  colspan="12"> <? echo $report_title ;?></td>
	        	</tr>
	        	<tr class="form_caption" style="border:none;">
	        	    <td align="center" style="border:none;font-size:12px; font-weight:bold"  colspan="12"> <? if($start_date!="" && $end_date!="") echo "From ".change_date_format($start_date)." To ".change_date_format($end_date);?></td>
	        	</tr>

	        	<tr>
		            <th width="30"><p>SL</p></th>
		            <th width="100"><p>Buyer</p></th>
		            <th width="100"><p>Job No.</p></th>
		            <th width="60"><p>Year</p></th>
		            <th width="120"><p>Booking No</p></th>               
		            <th width="100"><p>Product ID</p></th>               
		            <th width="70"><p>Lot</p></th>
		            <th width="110"><p>Supplier</p></th>
		            <th width="100"><p>Count</p></th>
		            <th width="130"><p>Composition</p></th>
		            <th width="100"><p>Yarn Type</p></th>
		            <th width="100"><p>Color</p></th>
		            <th width="100">Quantity (kg)</th>
		            <th width="150"><p>Allocation Date & Time</p></th>
		            <th width="100"><p>User ID</p></th>
		            <th width="100"><p>Status</p></th>
		            <th width=""><p>Remarks</p></th>
	        	</tr>

	        </thead>
	    </table>

	    <div style="width:1740px; max-height:320px; overflow-y:scroll;float: left;"  id="scroll_body" align="left">	
		    
		    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1722" class="rpt_table" id="tbl_list_search" align="left">  

				<?
		        $i=1;
		        foreach( $data_result as $row)
		        {
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
			
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">

				
						<?
						if($item_id_marge_check[$row[csf('item_id')]]=="")
						{
							$item_id_marge_check[$row[csf('item_id')]]=$row[csf('item_id')];

							?> 
			                <td rowspan="<? echo $item_id_row_marge[$row[csf('item_id')]];?>" width="30" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $i; ?></p></td>
			                <td rowspan="<? echo $item_id_row_marge[$row[csf('item_id')]];?>" width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
			                <td rowspan="<? echo $item_id_row_marge[$row[csf('item_id')]];?>" width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $row[csf('job_no')]; ?></p></td>
			                <td rowspan="<? echo $item_id_row_marge[$row[csf('item_id')]];?>" width="60" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $row[csf('year')]; ?></p></td>
			                <td rowspan="<? echo $item_id_row_marge[$row[csf('item_id')]];?>" width="120" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $row[csf('booking_no')]; ?></p></td>   
		                	<td rowspan="<? echo $item_id_row_marge[$row[csf('item_id')]];?>" width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $row[csf('item_id')]; ?></p></td>        
		                	
			                <?
			                $i++;
						}	
						$date = date_create($row[csf('insert_date')]);	
						$date_time = date_format($date,"d-m-Y h:i:s a");		
		                ?>     

		                <td width="70" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $prod_data_arr[$row[csf('item_id')]]['lot']; ?></p></td>
		                <td width="110" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p style="word-break: break-all;"><? echo $supplier[$prod_data_arr[$row[csf('item_id')]]['supp']]; ?></p></td>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $yarn_count[$prod_data_arr[$row[csf('item_id')]]['yarn_count_id']]; ?></p></td>
		                <td width="130" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p style="word-break: break-all;"><? echo $prod_data_arr[$row[csf('item_id')]]['prod_details']; ?></p></td>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p style="word-break: break-all;"><? echo $yarn_type[$prod_data_arr[$row[csf('item_id')]]['yarn_type']]; ?></p></td>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p style="word-break: break-all;"><? echo $color_arr[$prod_data_arr[$row[csf('item_id')]]['color']]; ?></p></td>
		                <td width="100" align="right">
							
							<? 
		                	echo number_format($row[csf('qnty')], 2);
		                	$total_allocation_qty += $row[csf('qnty')];
		                	?>
							
		                	
		                </td>
		                <td width="150" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $date_time; ?>&nbsp;</p></td>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $user_name[$row[csf('inserted_by')]]; ?>&nbsp;</p></td>
		                <td width="100" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center">
		                	<?
		                		$status = $row[csf('status_active')];
		                		echo ($status == 1) ? "Active": "Delete";
		                	?>
		                <p>&nbsp;</p></td>
		                <td width="" style="word-wrap: break-word;word-break: break-all;" valign="middle" align="center"><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
					</tr>
					<?
					
		        }
		        ?>
		    </table>
	    </div>

	    <table width="1722" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">	
	        <tr class="tbl_bottom">
	        	<td width="30">&nbsp;</td>
	        	<td width="100">&nbsp;</td>
	        	<td width="100">&nbsp;</td>
	        	<td width="60">&nbsp;</td>
	        	<td width="120">&nbsp;</p></td>               
	        	<td width="100">&nbsp;</td>               
	        	<td width="70">&nbsp;</td>
	        	<td width="110">&nbsp;</td>
	        	<td width="100">&nbsp;</td>
	        	<td width="130">&nbsp;</td>
	        	<td width="100">&nbsp;</td>
	        	<td width="100">&nbsp;</td>
	        	<td width="100"  id="value_total_allocation_qty"><? echo number_format($total_allocation_qty,2);?>&nbsp;</td>
	        	<td width="150">&nbsp;</td>
	        	<td width="100">&nbsp;</td>
	        	<td width="100">&nbsp;</td>
	        	<td width="">&nbsp;</td>
	        </tr>
		</table>	

	</div>
    <?
	        
	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename="".$user_name."_".$name.".xls";
	echo "$total_data####$filename";
	
	exit();
		
}


?>
      
 