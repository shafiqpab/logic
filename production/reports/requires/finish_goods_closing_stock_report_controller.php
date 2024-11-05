<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 100, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company in($data) and a.status_active=1 and a.is_deleted=0 order by a.buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );
  	exit();	 
}

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 100, "select id,store_name from lib_store_location where status_active =1 and is_deleted=0 and company_id in($data) order by store_name","id,store_name", 1, "-- Select Store --", $selected, "",0 );     
	exit();	
}

if($action=="job_popup")
{
	echo load_html_head_contents("Search Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		
		var selected_id = new Array; var selected_name = new Array;var selected_id_arr = new Array;
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
			 
			if( jQuery.inArray( str[0], selected_id_arr ) == -1 ) {
				selected_id_arr.push( str[0] );
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
				name += selected_name[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
		}
	
    </script>

	</head>
	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:710px;">
	            <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
	            	<thead>
	                    <th>Company Name</th>
	                    <th>Buyer</th>
	                    <th>Style Reference</th>
	                    <th>Job No</th>
	                    <th>PO Number</th>
	                    <th>
                            <input type="reset" name="button" class="formbutton" value="Reset"  style="width:80px;"> 
                            <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                            <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                        </th>
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
								<?
									echo create_drop_down( "cbo_company_id", 100, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company_name, "load_drop_down('requires/finish_goods_closing_stock_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
								?>
	                        </td>                  
	                        <td align="center" id="buyer_td"> 
	                        	<? 
									echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_name $buyer_cond order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
								?>
	                        </td>                   
	                        <td align="center">				
	                            <input type="text" style="width:100px" class="text_boxes" name="txt_style_ref" id="txt_style_ref" />	
	                        </td> 
	                        <td align="center">				
	                            <input type="text" style="width:100px" class="text_boxes" name="txt_job_no" id="txt_job_no" />	
	                        </td> 
	                        <td align="center">				
	                            <input type="text" style="width:100px" class="text_boxes" name="txt_po_no" id="txt_po_no" />	
	                        </td> 
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_style_ref').value+'**'+document.getElementById('txt_job_no').value+'**'+document.getElementById('txt_po_no').value, 'search_list_view', 'search_div', 'finish_goods_closing_stock_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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

if($action=="search_list_view")
{
	$data=explode('**',$data);
	$company_id=str_replace("'", "", $data[0]);
	$buyer_id=str_replace("'", "", $data[1]);
	$style_ref=str_replace("'", "", $data[2]);
	$job_no=str_replace("'", "", $data[3]);
	$po_no=str_replace("'", "", $data[4]);
		
	$search_string='';
	if($company_id!=0)
	{
		$search_string.=" and a.company_name=$company_id ";
	}
	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $search_string.=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
		}
	}
	else
	{
		$search_string.=" and a.buyer_name=$buyer_id ";
	}
	if($style_ref!='')
	{
		$search_string.=" and a.style_ref_no='".trim($style_ref)."' ";
	}
	if($job_no!='')
	{
		$search_string.=" and a.job_no like '%".$job_no."' ";
	}
	if($po_no!='')
	{
		$search_string.=" and b.po_number='".trim($po_no)."' ";
	}

	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$arr=array (0=>$company_library,1=>$buyer_arr);
	
	if($db_type==0) $po_number="group_concat(distinct(b.po_number)) as po_number,"; 
	else if($db_type==2) $po_number="listagg(cast(b.po_number as varchar(4000)),', ') within group(order by b.id) as po_number";
	else  $po_number="";

	$sql= "SELECT a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $po_number from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 $search_string group by a.id, a.job_no, a.insert_date, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no order by a.id desc"; 
    // echo $sql;
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Style,Job No,Order No", "100,100,100,100","650","220",0, $sql , "js_set_value", "id,job_no,style_ref_no","",1,"company_name,buyer_name,0,0,0,0",$arr,"company_name,buyer_name,style_ref_no,job_no,po_number","",'','0,0,0,0,0','',1) ;
   exit(); 
}

if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if($rpt_type==1)
	{
		$company_arr = return_library_array("SELECT id,company_name from lib_company", "id", "company_name");
		$color_arr = return_library_array("SELECT id,color_name from lib_color", "id", "color_name");
		$store_arr = return_library_array("SELECT id,store_name from lib_store_location", "id", "store_name");
		$buyer_arr = return_library_array("SELECT id,buyer_name from lib_buyer", "id", "buyer_name");

        if($db_type==0) 
        {       
            $from_date=change_date_format($from_date,'yyyy-mm-dd');
            $to_date=change_date_format($to_date,'yyyy-mm-dd');
        }
        else if($db_type==2) 
        {               
            $from_date=change_date_format($from_date,'','',1);
            $to_date=change_date_format($to_date,'','',1);
        }
        else  
        {
            $from_date=""; $to_date="";
        }

        $search_cond='';
		if($cbo_company_name!=''){$search_cond.=" and a.company_id in($cbo_company_name) ";}
		if($cbo_store_name!=''){$search_cond.=" and a.store_id in($cbo_store_name) ";}
		if($cbo_year!=0){$search_cond.=" and d.season_year=$cbo_year ";}
		if($cbo_buyer_id!=0){$search_cond.=" and d.buyer_name=$cbo_buyer_id ";}
		if($hidden_job_id!='')
        {
			$job_id_arr=explode(',',$hidden_job_id);
			$job_id_in=where_con_using_array($job_id_arr,0,'d.id');
		}
 
        $date_array=array();

        $returnRes_date="SELECT min(a.delivery_date) as MIN_DATE, max(a.delivery_date) as MAX_DATE, a.STORE_ID, c.COLOR_NUMBER_ID, c.ITEM_NUMBER_ID, c.job_id as JOB_ID 
        from pro_gmts_delivery_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
        where a.id=b.delivery_mst_id and a.production_type in (81,82,83) and b.production_type in (81,82,83) and b.color_size_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
        group by a.store_id, c.color_number_id, c.item_number_id, c.job_id";
        $result_returnRes_date = sql_select($returnRes_date);
        foreach($result_returnRes_date as $row)	
		{
            $key=$row['JOB_ID'].'*'.$row['STORE_ID'].'*'.$row['ITEM_NUMBER_ID'].'*'.$row['COLOR_NUMBER_ID'];
			$date_array[$key]['min_date']=$row["MIN_DATE"];
			$date_array[$key]['max_date']=$row["MAX_DATE"];
		}
		unset($result_returnRes_date);

        $sql="SELECT a.COMPANY_ID, a.STORE_ID, c.COLOR_NUMBER_ID, c.ITEM_NUMBER_ID, d.id as JOB_ID, d.JOB_NO, d.STYLE_REF_NO, d.BUYER_NAME, d.CLIENT_ID, d.SEASON_BUYER_WISE, d.ORDER_UOM, d.AVG_UNIT_PRICE,
        sum(case when a.production_type in (81) and a.delivery_date<'$from_date' then b.production_qnty else 0 end) as RCV_TOTAL_OPENING,
        sum(case when a.production_type in (82) and a.delivery_date<'$from_date' then b.production_qnty else 0 end) as ISS_TOTAL_OPENING,
        sum(case when a.production_type in (83) and a.delivery_date<'$from_date' then b.production_qnty else 0 end) as ISS_RTN_TOTAL_OPENING,
        sum(case when a.production_type in (81) and a.delivery_date between '$from_date' and '$to_date' then b.production_qnty else 0 end) as RCV_QNTY,   
        sum(case when a.production_type in (82) and a.delivery_date between '$from_date' and '$to_date' then b.production_qnty else 0 end) as ISSUE_QNTY,
        sum(case when a.production_type in (83) and a.delivery_date between '$from_date' and '$to_date' then b.production_qnty else 0 end) as ISSUE_RTN_QNTY
        from pro_gmts_delivery_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c, wo_po_details_master d
        where a.id=b.delivery_mst_id and a.production_type in (81,82,83) and b.production_type in (81,82,83) and b.color_size_break_down_id=c.id and c.job_id=d.id $search_cond $job_id_in and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 
        group by a.company_id,a.store_id,c.color_number_id,c.item_number_id,d.id,d.job_no,d.style_ref_no,d.buyer_name,d.client_id,d.season_buyer_wise,d.order_uom,d.avg_unit_price";
								
		// echo $sql;die;
		$result = sql_select($sql);	
		$resultArr=array();
		foreach($result as $row)
		{
			$key=$row['JOB_ID'].'*'.$row['STORE_ID'].'*'.$row['ITEM_NUMBER_ID'].'*'.$row['COLOR_NUMBER_ID'];
			$resultArr[$key]['buyer_name']=$row['BUYER_NAME'];
			$resultArr[$key]['client_id']=$row['CLIENT_ID'];
			$resultArr[$key]['job_no']=$row['JOB_NO'];
			$resultArr[$key]['job_id']=$row['JOB_ID'];
			$resultArr[$key]['style_ref_no']=$row['STYLE_REF_NO'];
			$resultArr[$key]['season_buyer_wise']=$row['SEASON_BUYER_WISE'];
			$resultArr[$key]['store_id']=$row['STORE_ID'];
			$resultArr[$key]['order_uom']=$row['ORDER_UOM'];
            $resultArr[$key]['avg_unit_price']=$row['AVG_UNIT_PRICE'];
			$resultArr[$key]['item_id']=$row['ITEM_NUMBER_ID'];
			$resultArr[$key]['color_id']=$row['COLOR_NUMBER_ID'];

            $opening_qty=$row["RCV_TOTAL_OPENING"]+$row["ISS_RTN_TOTAL_OPENING"]-$row["ISS_TOTAL_OPENING"];  
            $resultArr[$key]['opening_qty']=$opening_qty;
            $resultArr[$key]['rcv_qnty']=$row["RCV_QNTY"];  
            $resultArr[$key]['issue_qnty']=$row["ISSUE_QNTY"];  
            $resultArr[$key]['issue_rtn_qnty']=$row["ISSUE_RTN_QNTY"];  
			$closing_stock=$opening_qty+$row["RCV_QNTY"]+$row["ISSUE_RTN_QNTY"]-$row["ISSUE_QNTY"]; 
			$resultArr[$key]['closing_stock']=$closing_stock;  
			$resultArr[$key]['closing_value']=$closing_stock*$row['AVG_UNIT_PRICE'];;  

            $ageOfDays = datediff("d",$date_array[$key]['min_date'],date("Y-m-d"));
			$daysOnHand = datediff("d",$date_array[$key]['max_date'],date("Y-m-d")); 

            $resultArr[$key]['ageOfDays']= $ageOfDays;
            $resultArr[$key]['daysOnHand']= $daysOnHand;

		}
        unset($result);

		$tbl_width=1710;
		ob_start();	
		?>
		<style>
			.wrd_brk{word-break: break-all;}
			.center{text-align: center;}
			.right{text-align: right;}
		</style>

		<table width="<?=$tbl_width;?>" border="0">
			<tr class="form_caption" style="border:none;">
				<td colspan="35" align="center" style="border:none;font-size:16px; font-weight:bold" ><?=$report_title;?></td> 
			</tr>
		</table>
		<table width="<?=$tbl_width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" > 
			<thead>
				<tr>
					<th width="30" >SL</th>
					<th width="100">Buyer</th>
					<th width="100">Buyer Client</th>
					<th width="100" >Job No</th>
					<th width="100" >Style</th>
					<th width="100" >Season</th>
					<th width="100" >GMTS ITEM</th>
					<th width="100" >Gmts Color</th>
					<th width="100" >Store Name</th>
					<th width="80" >UOM</th>
					<th width="80" >Opening Stock</th>                    
					<th width="80" >Receive</th>                    
					<th width="80" >Issue Return</th>                    
					<th width="80" >Total Receive</th>                    
					<th width="80" >Issue</th>                    
					<th width="80" >Closing Stock</th>                    
					<th width="80" >FOB Rate</th>                    
					<th width="80" >Closing Amount</th>                    
					<th width="80" >Age Days</th>                    
					<th >DOH</th>                    
					                 
				</tr>
			</thead>
		</table>  
		<div style="width:<?=$tbl_width+18;?>px; overflow-y:scroll; max-height:250px" id="scroll_body" > 
			<table width="<?=$tbl_width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body"> 
				<tbody>
					<?	
                        foreach($resultArr as $key=>$val)
                        {	
							if(($value_with ==2 && ($val["opening_qty"] != 0 || $val["closing_stock"] != 0 || $val["closing_value"] !=0) ) || ($value_with ==1 && ($val["opening_qty"] != 0 || $val["closing_stock"] != 0 || $val["closing_value"] !=0 || $val["rcv_qnty"] != 0 || $val["issue_rtn_qnty"] || $val["issue_qnty"] != 0 )))
							{
								if((($get_upto==1 && $val["ageOfDays"]>$txt_days) || ($get_upto==2 && $val["ageOfDays"]<$txt_days) || ($get_upto==3 && $val["ageOfDays"]>=$txt_days) || ($get_upto==4 && $val["ageOfDays"]<=$txt_days) || ($get_upto==5 && $val["ageOfDays"]==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $val["closing_stock"]>$txt_qnty) || ($get_upto_qnty==2 && $val["closing_stock"]<$txt_qnty) || ($get_upto_qnty==3 && $val["closing_stock"]>=$txt_qnty) || ($get_upto_qnty==4 && $val["closing_stock"]<=$txt_qnty) || ($get_upto_qnty==5 && $val["closing_stock"]==$txt_qnty) || $get_upto_qnty==0))
								{
									if ($i%2==0)$bgcolor="#E9F3FF";	else $bgcolor="#FFFFFF"; 
									?>
										<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="30" align="center"><? echo $i; ?></td>
											<td class="wrd_brk" width="100"><? echo $buyer_arr[$val["buyer_name"]]; ?></td>
											<td class="wrd_brk" width="100"><? echo $buyer_arr[$val["client_id"]]; ?></td>
											<td class="wrd_brk" width="100"><? echo $val["job_no"]; ?></td>
											<td class="wrd_brk" width="100"><? echo $val["style_ref_no"]; ?></td>
											<td class="wrd_brk" width="100"><? echo $val["season_buyer_wise"]; ?></td>
											<td class="wrd_brk" width="100"><? echo $garments_item[$val["item_id"]]; ?></td>
											<td class="wrd_brk" width="100"><? echo $color_arr[$val["color_id"]]; ?></td>
											<td class="wrd_brk" width="100"><? echo $store_arr[$val["store_id"]]; ?></td>
											<td class="wrd_brkk center" width="80"><? echo $unit_of_measurement[$val["order_uom"]]; ?></td>
											<td class="wrd_brk right" width="80" ><? echo $val["opening_qty"]; ?></td>
											<td class="wrd_brk right" width="80" ><? echo $val["rcv_qnty"]; ?></td>
											<td class="wrd_brk right" width="80" ><?  echo $val["issue_rtn_qnty"]; ?></td>
											<td class="wrd_brk right" width="80" ><? $tot_rcv_qty=$val["rcv_qnty"]+$val["issue_rtn_qnty"]; echo $tot_rcv_qty; ?></td>
				
											<td class="wrd_brk right" width="80" ><? echo $val["issue_qnty"]; ?></td>
											<td class="wrd_brk right" width="80" >
												<a href='##' style='color:#000' onClick="openmypage_stock('<?=$val["job_id"];?>','<?=$val["store_id"];?>','<?=$val["item_id"];?>','<?=$val["color_id"];?>','<?=$to_date;?>');"><? echo $val['closing_stock']; ?></a>
											</td>
											<td class="wrd_brk right" width="80" ><? echo $val["avg_unit_price"]; ?></td>
											<td class="wrd_brk right" width="80" ><? echo number_format($val["closing_value"],2);?></td>
											<td class="wrd_brk center" width="80" ><? echo $val["ageOfDays"]; ?></td>
											<td class="wrd_brk center"><? echo $val["daysOnHand"]; ?></td>                            
										</tr>
									<?
									$i++;
									$total_opening_bal+=$val["opening_bal"];							
									$total_rcv_qnty+=$val["rcv_qnty"];							
									$total_issue_rtn_qnty+=$val["issue_rtn_qnty"];							
									$total_rcv_qty+=$tot_rcv_qty;	
									$total_issue_qnty+=$val["issue_qnty"];						
									$total_closing_stock+=$val['closing_stock'];						
									$total_closing_stock_amount+=$val["closing_value"];		
								}
							}					
						} 
					?>
				</tbody>  
				<tfoot>
					<tr bgcolor="#A9D08E">
						<th colspan="10" class="right"> <b>GTTL</b> </th>
						<th class="wrd_brk right"><?=$total_opening_bal;?></th>
						<th class="wrd_brk right"><?=$total_rcv_qnty;?></th>
						<th class="wrd_brk right"><?=$total_issue_rtn_qnty;?></th>
						<th class="wrd_brk right"><?=$total_rcv_qty;?></th>
						<th class="wrd_brk right"><?=$total_issue_qnty;?></th>
						<th class="wrd_brk right"><?=$total_closing_stock;?></th>
						<th ></th>
						<th class="wrd_brk right"><?=number_format($total_closing_stock_amount,2);?></th>
						<th ></th>
						<th ></th>
					</tr>
				</tfoot>
			</table> 
		</div>
			
		<?
	}
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename) {
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename**$rpt_type"; 
	exit();
}

if($action=="stock_popup")
{
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    extract($_REQUEST);
    $room_rack_self_arr = return_library_array("SELECT floor_room_rack_id,floor_room_rack_name from lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");

    $stock_sql="SELECT b.STORE_ID, b.ROOM_ID, b.RACK_ID, b.SHELF_ID, b.item_number_id, d.color_number_id, e.PO_NUMBER,
    sum(case when a.production_type in (81) then c.production_qnty else 0 end) as RCV_TOTAL,
    sum(case when a.production_type in (82) then c.production_qnty else 0 end) as ISS_TOTAL,
    sum(case when a.production_type in (83) then c.production_qnty else 0 end) as ISS_RTN_TOTAL
    from pro_gmts_delivery_mst a,pro_garments_production_mst b, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e
    where a.id=b.delivery_mst_id and b.id=c.mst_id and a.production_type in (81,82,83) and b.production_type in (81,82,83) and c.color_size_break_down_id=d.id and b.po_break_down_id=e.id and e.job_id=$job_id and b.store_id=$store_id and b.item_number_id=$item_id and d.color_number_id=$color_id and a.delivery_date<='$to_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 group by b.store_id, b.room_id, b.rack_id, b.shelf_id, b.item_number_id, d.color_number_id, e.po_number";
	// echo $stock_sql; die;
	$data_arr=sql_select($stock_sql);  
	?> 
    <style>
      .wrd_brk{word-break: break-all;}
      .left{text-align: left;}
      .center{text-align: center;}
      .right{text-align: right;}
    </style>   
    <div id="data_panel" align="center" style="width:100%">
      <fieldset style="width: 98%">
        <table width="550" cellspacing="0" class="rpt_table" border="0" id="tbl_returnable_details" rules="all">
          <thead>
            <tr>
              <th width="30">Sl No</th>
              <th width="120">PO</th>
              <th width="100">Room No</th>
              <th width="100">Rack No</th>
              <th width="100">Shelf</th>
              <th >Stock qty.</th>
            </tr>
          </thead>  
          <tbody>
            <?
              $i=1;
              foreach ($data_arr as $row) 
              {     
                $stock_balance=$row['RCV_TOTAL']+$row['ISS_RTN_TOTAL']-$row['ISS_TOTAL'];
                if($stock_balance>0)
                {
                    ?>                         
                    <tr>
                        <td class="center"><? echo $i;?></td>
                        <td class="center"><? echo $row['PO_NUMBER'];?></td>
                        <td class="wrd_brk"><? echo $room_rack_self_arr[$row['ROOM_ID']];?></td>
                        <td class="wrd_brk"><? echo $room_rack_self_arr[$row['RACK_ID']]; ?></td>
                        <td class="wrd_brk"><? echo $room_rack_self_arr[$row['SHELF_ID']]; ?></td>
                        <td class="wrd_brk right"><? echo $stock_balance; ?></td>
                    </tr>
                    <?
                    $total_stock+=$stock_balance;
                    $i++;    
                }                
              }
            ?>
          </tbody>   
          <tfoot>
            <tr>
              <th colspan="5"><b>Total</b></th>
              <th class="wrd_brk right"><?echo $total_stock;?></th>
            </tr>
          </tfoot>    
        </table>
      </fieldset>
    </div> 
    <?
    exit(); 
}
?>