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
	echo create_drop_down( "cbo_buyer_name", 120, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 order by a.buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );
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
	                    <!-- <th>PO Number</th> -->
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
									echo create_drop_down( "cbo_company_id", 100, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company_name, "load_drop_down('requires/finish_goods_ledger_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
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
	                        <!-- <td align="center">				
	                            <input type="text" style="width:100px" class="text_boxes" name="txt_po_no" id="txt_po_no" />	
	                        </td>  -->
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_style_ref').value+'**'+document.getElementById('txt_job_no').value, 'search_list_view', 'search_div', 'finish_goods_ledger_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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
	// $po_no=str_replace("'", "", $data[4]);
		
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
	/*if($po_no!='')
	{
		$search_string.=" and b.po_number='".trim($po_no)."' ";
	}*/

	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$arr=array (0=>$company_library,1=>$buyer_arr);
	
	/*if($db_type==0) $po_number="group_concat(distinct(b.po_number)) as po_number,"; 
	else if($db_type==2) $po_number="listagg(cast(b.po_number as varchar(4000)),', ') within group(order by b.id) as po_number";
	else  $po_number="";*/

	// , $po_number
	$sql= "SELECT a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 $search_string group by a.id, a.job_no, a.insert_date, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no order by a.id desc"; 
    // echo $sql;
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Style,Job No", "160,150,150,100","650","220",0, $sql , "js_set_value", "id,job_no,style_ref_no","",1,"company_name,buyer_name,0,0",$arr,"company_name,buyer_name,style_ref_no,job_no","",'','0,0,0,0','',1) ;
   exit(); 
}

if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$hidden_job_id=str_replace("'","",$hidden_job_id);
	$rpt_type=str_replace("'","",$type);
	if($rpt_type==1)
	{
		$company_arr = return_library_array("SELECT id,company_name from lib_company", "id", "company_name");
		$supplier_arr = return_library_array("SELECT id,supplier_name from lib_supplier", "id", "supplier_name");
		$store_arr = return_library_array("SELECT id,store_name from lib_store_location", "id", "store_name");
		$season_arr = return_library_array("SELECT id,season_name from lib_buyer_season", "id", "season_name");
		$buyer_arr = return_library_array("SELECT id,buyer_name from lib_buyer", "id", "buyer_name");
		$purpose_arr=array(1=>"Delivery",2=>"Buyer Inspection",3=>"Sales");
		
		if($cbo_company_name!=0){$com_cond=" and a.company_id=$cbo_company_name ";}
		$job_id_arr=explode(',',$hidden_job_id);
		$job_id_in=where_con_using_array($job_id_arr,0,'c.job_id');
		
		$sql ="SELECT a.ID, a.SYS_NUMBER, a.COMPANY_ID, a.DELIVERY_DATE, a.PRODUCTION_SOURCE, a.WORKING_COMPANY_ID, a.STORE_ID, a.PURPOSE_ID, b.ITEM_NUMBER_ID, b.PRODUCTION_TYPE, b.PRODUCTION_QUANTITY, c.PO_NUMBER, c.UNIT_PRICE, d.JOB_NO, d.STYLE_REF_NO, d.BUYER_NAME, d.SEASON_BUYER_WISE from pro_gmts_delivery_mst a, pro_garments_production_mst b, wo_po_break_down c, wo_po_details_master d where a.id=b.delivery_mst_id and a.production_type in ('81','82','83') and b.production_type in ('81','82','83') and b.po_break_down_id=c.id and c.job_id=d.id $com_cond $job_id_in and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 order by c.job_id,c.id,b.item_number_id,a.id";	
						
		// echo $sqlResult;die;
		$result = sql_select($sql);	
		
		ob_start();	
		?>
		<style>
			.wrd_brk{word-break: break-all;}
			.center{text-align: center;}
			.right{text-align: right;}
		</style>

			<table width="1600" border="0">
				<tr class="form_caption" style="border:none;">
					<td colspan="17" align="center" style="border:none;font-size:16px; font-weight:bold" >Finished Goods Item Ledger </td> 
				</tr>
				<tr style="border:none;">
					<td colspan="17" align="center" style="border:none; font-size:14px;">
						Company Name : <? echo $company_arr[$cbo_company_name]; ?>                                
					</td>
				</tr>
			</table>
			<table width="1600" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" > 
				<thead>
					<tr>
						<th width="30" >SL</th>
						<th width="100">Job No</th>
						<th width="100" >Buyer</th>
						<th width="100" >Style Ref.</th>
						<th width="100" >Order No</th>
						<th width="100" >Season</th>
						<th width="100" >Item</th>
						<th width="100" >Store Name</th>
						<th width="80" >Trans Date</th>
						<th width="100" >Trans Ref No</th>
						<th width="80" >Trans Type</th>                    
						<th width="80" >Purpose</th>                    
						<th width="100" >Trans With</th>                    
						<th width="80" >Receive Qnty</th>                    
						<th width="80" >Issue Qnty</th>                    
						<th width="80" >Balance Qnty</th>                    
						<th width="80" >Rate</th>                    
						<th >Amount</th>                    
					</tr>
				</thead>
			</table>  
			<div style="width:1620px; overflow-y:scroll; max-height:250px" id="scroll_body" > 
				<table width="1600" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body"> 
					<tbody>
					<?	
					$chk_arr=array();	
					$i=1;
					foreach($result as $row)
					{			
						if ($i%2==0)$bgcolor="#E9F3FF";	else $bgcolor="#FFFFFF"; 
						if($row["PRODUCTION_TYPE"]==82)$stylecolor='style="color:#A61000"'; else $stylecolor='style="color:#000000"';
						if(!in_array($row['JOB_NO'].'*'.$row['PO_NUMBER'].'*'.$row['ITEM_NUMBER_ID'],$chk_arr))
						{
							$chk_arr[]=$row['JOB_NO'].'*'.$row['PO_NUMBER'].'*'.$row['ITEM_NUMBER_ID'];
							$balance_qnty=0;
						}			
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30" align="center"><? echo $i; ?></td>
							<td class="wrd_brk" width="100"><? echo $row["JOB_NO"]; ?></td>
							<td class="wrd_brk" width="100"><? echo $buyer_arr[$row["BUYER_NAME"]]; ?></td>
							<td class="wrd_brk" width="100"><? echo $row["STYLE_REF_NO"]; ?></td>
							<td class="wrd_brk" width="100"><? echo $row["PO_NUMBER"]; ?></td>
							<td class="wrd_brk" width="100"><? echo $season_arr[$row["SEASON_BUYER_WISE"]]; ?></td>
							<td class="wrd_brk" width="100"><? echo $garments_item[$row["ITEM_NUMBER_ID"]]; ?></td>
							<td class="wrd_brk" width="100"><? echo $store_arr[$row["STORE_ID"]]; ?></td>
							<td class="wrd_brk center" width="80" ><? echo change_date_format($row["DELIVERY_DATE"]); ?></td>                                 
							<td class="wrd_brk" width="100"><? echo $row["SYS_NUMBER"]; ?></td>
							<td class="wrd_brk" width="80">
								<? 
									if($row["PRODUCTION_TYPE"]==81) {echo "Receive"; }
									if($row["PRODUCTION_TYPE"]==82) {echo "Issue"; }
									if($row["PRODUCTION_TYPE"]==83) {echo "Issue Return"; }
								?>
							</td>
							<td class="wrd_brk" width="80"><? if($row[csf("PRODUCTION_TYPE")]!=81) echo $purpose_arr[$row["PURPOSE_ID"]]; ?></td>
							<td class="wrd_brk" width="100">
								<? 
									if($row[csf("PRODUCTION_TYPE")]==81) 
									{
										if($row["PRODUCTION_SOURCE"]==1) {echo $company_arr[$row["WORKING_COMPANY_ID"]];}
										if($row["PRODUCTION_SOURCE"]==3) {echo $supplier_arr[$row["WORKING_COMPANY_ID"]]; }
									}
									else
									{
										echo $company_arr[$row["COMPANY_ID"]];
									}
								?>
							</td>
							<td class="wrd_brk right" width="80">
								<?
									if($row["PRODUCTION_TYPE"]==81) {echo $row["PRODUCTION_QUANTITY"]; }
									if($row["PRODUCTION_TYPE"]==83) {echo $row["PRODUCTION_QUANTITY"]; }
								?>
							</td>
							<td class="wrd_brk right" width="80"><? if($row["PRODUCTION_TYPE"]=='82') {echo $row["PRODUCTION_QUANTITY"];} ?></td>
							<td class="wrd_brk right" width="80">
								<? 
									if($row["PRODUCTION_TYPE"]==81) {$balance_qnty+=$row["PRODUCTION_QUANTITY"];$tot_rcv+=$row["PRODUCTION_QUANTITY"]; }
									if($row["PRODUCTION_TYPE"]==82) {$balance_qnty-=$row["PRODUCTION_QUANTITY"];$tot_issue+=$row["PRODUCTION_QUANTITY"];  }
									if($row["PRODUCTION_TYPE"]==83) {$balance_qnty+=$row["PRODUCTION_QUANTITY"];$tot_rcv+=$row["PRODUCTION_QUANTITY"];  }
									echo $balance_qnty;
								?>
							</td>
							<td class="wrd_brk right" width="80"><? if($row["PURPOSE_ID"]!=3){echo number_format($row["UNIT_PRICE"],2,'.','');} ?></td>
							<td class="wrd_brk right" ><?  if($row["PURPOSE_ID"]!=3){echo number_format($balance_qnty*$row["UNIT_PRICE"],2,'.','');} ?></td>
						</tr>
						<?
						$i++;
					} 
					?> 
					</tbody>  
					<tfoot>
						<tr>
							<td class="right"colspan="13"><b>Total</b> &nbsp;</td>
							<td class="wrd_brk right" ><b><? echo $tot_rcv; ?></b></td>
							<td class="wrd_brk right" ><b><? echo $tot_issue; ?></b></td>
							<td class="wrd_brk right" ><b><? echo $tot_rcv-$tot_issue; ?></b></td>
							<td></td>
							<td></td>
						</tr>
					</tfoot>
				</table> 
			</div>
			
		<?
	}
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("$user_id*.xls") as $filename) {
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
?>