<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if($db_type==2 || $db_type==1 )
{
	$select_date=" to_char(a.insert_date,'YYYY')";
	$group_concat="wm_concat";
}
else if ($db_type==0)
{
	$select_date=" year(a.insert_date)";
	$group_concat="group_concat";
}

//--------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------




if($action=="lc_sc_search")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
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
				var isLcSc = splitSTR[3];
				
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
				$('#is_lc_or_sc').val( isLcSc );
		}
		
    </script>
    <body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="500" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>                	 
						<th>Search By</th>
						<th align="center" width="200" id="search_by_td_up">Please Enter LC No</th>
 						<th>
                       		<input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  />
                            <input type='hidden' id='txt_selected_id' />
							<input type='hidden' id='txt_selected' />
							<input type='hidden' id='txt_selected_no' />
                            <input type='hidden' id='is_lc_or_sc' />
                        </th>
					</tr>
				</thead>
				<tbody>
					<tr align="center">
						<td align="center">
							<?  
								$search_by = array(1=>'LC No', 2=>'SC No');
								$dd="change_search_event(this.value, '0*0', '0*0', '../../')";
								echo create_drop_down( "cbo_search_by", 150, $search_by, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td width="180" align="center" id="search_by_td">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
						</td> 
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $company; ?>+'_'+<? echo $cbo_lien_bank; ?>, 'create_lcSc_search_list_view', 'search_div', 'bank_certificate_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />				
						</td>
					</tr>
 				</tbody>
			 </tr>         
			</table>    
			<div align="center" valign="top" style="margin-top:5px" id="search_div"> </div> 
			</form>
	   </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
	</html>
    <?
	exit();
}

if($action=="create_lcSc_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];
	$lien_bank = str_replace("'","",$ex_data[3]);
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$lien_bank_arr=return_library_array( "select id,bank_name from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1",'id','bank_name');
	if($txt_search_by==1)
	{
		$sql_cond="";
		if($txt_search_common!="") $sql_cond=" and export_lc_no LIKE '%$txt_search_common%'";
		if($lien_bank>0)  $sql_cond.=" and lien_bank=$lien_bank";
		$sql = "select id,beneficiary_name,buyer_name,lien_bank,export_lc_no,lc_value, 1 as lc_sc from com_export_lc where beneficiary_name=$company and status_active=1 and is_deleted=0 $sql_cond"; 
		
	}
	else
	{
		$sql_cond="";
		if($txt_search_common!="") $sql_cond=" and contract_no LIKE '%$txt_search_common%'";
		if($lien_bank>0)  $sql_cond.=" and lien_bank=$lien_bank";
		$sql = "select id,beneficiary_name,buyer_name,lien_bank,contract_no as export_lc_no,contract_value as lc_value, 2 as lc_sc from  com_sales_contract where beneficiary_name=$company and status_active=1 and is_deleted=0 $sql_cond"; 
	}
	//echo $sql;die;
	$arr=array(0=>$company_arr,1=>$buyer_arr,2=>$lien_bank_arr);
	//function create_list_view( $table_id, $tbl_header_arr, $td_width_arr, $tbl_width, $tbl_height, $tbl_border, $query, $onclick_fnc_name, $onclick_fnc_param_db_arr, $onclick_fnc_param_sttc_arr,  $show_sl, $field_printed_from_array_arr,  $data_array_name_arr, $qry_field_list_array, $controller_file_path, $filter_grid_fnc, $fld_type_arr, $summary_flds, $check_box_all ,$new_conn )
	echo create_list_view("list_view", "Company,Buyer,Lien Bank,Lc/Sc No,Value","120,120,120,100","600","260",0, $sql , "js_set_value", "id,export_lc_no,lc_sc", "", 1, "beneficiary_name,buyer_name,lien_bank,0,0", $arr, "beneficiary_name,buyer_name,lien_bank,export_lc_no,lc_value", "","","0,0,0,0,2","",1) ;
	exit();	
}






//report generated here--------------------//
if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_lien_bank=str_replace("'","",$cbo_lien_bank);
	$txt_lc_sc=str_replace("'","",$txt_lc_sc);
	//echo $txt_lc_sc.jahid;die;
	$txt_lc_sc_id=str_replace("'","",$txt_lc_sc_id);
	$txt_reference=str_replace("'","",$txt_reference);
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$bank_sql=sql_select("select id, bank_name, address  from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1");
	$bank_details=array();
	foreach($bank_sql as $row)
	{
		$bank_details[$row[csf("id")]]["bank_name"]=$row[csf("bank_name")];
		$bank_details[$row[csf("id")]]["address"]=$row[csf("address")];
	}
	ob_start();	
	?>
	<div style="width:670px;">  
			<table cellpadding="0" cellspacing="0" width="650">
				<tr>
					<td height="100">&nbsp;</td>
				</tr>
				<tr>
					<td style="font-size:14px">Ref: <? echo $txt_reference; ?></td>
				</tr>
				<tr>
					<td style="font-size:14px">Dated: <? echo date("F d").", ".date("Y"); ?></td>
				</tr>
                <tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
				   <td align="center" style="font-size:22px"><b>TO WHOM IT MAY CONCERN</b></td>
				</tr>
				<tr>
					<td height="50">&nbsp;</td>
				</tr>
				<tr>
					<td style="font-size:14px; text-align:justify;"><p>This is to certify that we have opened the following Back to Back L/C from our branch in the A/C of M/S. <? echo $company_arr[$cbo_company_name]." ";
					$query_c = sql_select("select id, company_name, plot_no, level_no, road_no, block_no, city, zip_code, country_id from lib_company where id='$cbo_company_name' and is_deleted=0 and status_active=1");
					foreach($query_c as $row_c)
					{
						if($row_c[csf('plot_no')] != ""){echo $row_c[csf('plot_no')];}
					
						if($row_c[csf('level_no')] != ""){echo ", ".$row_c[csf('level_no')];} 
						
						if($row_c[csf('road_no')] != ""){echo ", ".$row_c[csf('road_no')];} 
						
						if($row_c[csf('block_no')] != ""){echo ", ".$row_c[csf('block_no')];} 
						
						if($row_c[csf('city')] != ""){echo ", ".$row_c[csf('city')];} 
						
						if($row_c[csf('zip_code')] != ""){echo "-".$row_c[csf('zip_code')];}
						
						if($row_c[csf('country_id')] != ""){echo ", ".$country_arr[$row_c[csf('country_id')]];}
					}  
					?> against  
					<? 
					if($is_lc_or_sc==1) echo " Export L/C No. "; else echo " Export S/C No. ";
						//echo $txt_lc_sc; 
						$lc_sc_value="";
						if($is_lc_or_sc==1)
						{
							$lc_sc_value=return_field_value("sum(lc_value)","com_export_lc","beneficiary_name='$cbo_company_name' and id in($txt_lc_sc_id) and is_deleted=0 and status_active=1");
							$lc_sc_sql=sql_select("select id, export_lc_no as lc_sc_no, lc_date as lc_sc_date from com_export_lc where id in($txt_lc_sc_id)");
						}
						else
						{
							$lc_sc_value=return_field_value("sum(contract_value)","com_sales_contract","beneficiary_name='$cbo_company_name' and id in($txt_lc_sc_id) and is_deleted=0 and status_active=1");
							$lc_sc_sql=sql_select("select id, contract_no as lc_sc_no, contract_date as lc_sc_date from com_sales_contract where id in($txt_lc_sc_id)");
						}
						$all_lc_sc=$all_lc_sc_date="";
						foreach($lc_sc_sql as $row)
						{
							$all_lc_sc.=$row[csf("lc_sc_no")].",";
							$all_lc_sc_date.=change_date_format($row[csf("lc_sc_date")]).",";
						}
						$all_lc_sc=chop($all_lc_sc," , ");$all_lc_sc_date=chop($all_lc_sc_date," , ");
						
						echo $all_lc_sc." Dated ".$all_lc_sc_date." for USD ".number_format($lc_sc_value,2)." (U.S. ".strtoupper(number_to_words($lc_sc_value,"Dollar","Cents"))." ONLY).";
					?>
					</p></td> 
				</tr>
				<tr><td height="30"></td></tr>
			</table>
			<table width="650" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
				<thead>
					<th width="150">BTB L/C No</th>
					<th width="100">BTB L/C Date</th>
					<th width="90">Currency</th>
					<th width="140">BTB L/C Value</th>
					<th>Item Category</th>
				</thead>
			</table>
		<div style="width:670px; overflow-y:scroll; max-height:250px" id="scroll_body"> 
			 <table width="650" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">  
			 <?	
				if($is_lc_or_sc==1)
				{
					$sql="select a.lc_number, a.lc_date, a.currency_id, a.item_category_id, a.lc_value as value from com_btb_lc_master_details a,  com_btb_export_lc_attachment b, com_export_lc c where a.id=b.import_mst_id and b.lc_sc_id=c.id and is_lc_sc=0 and c.beneficiary_name='$cbo_company_name' and c.lien_bank like '$cbo_lien_bank' and c.id in($txt_lc_sc_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.lc_number, a.lc_date, a.currency_id, a.item_category_id, a.lc_value order by a.lc_number";
				}
				else
				{
					$sql="select a.lc_number, a.lc_date, a.currency_id, a.item_category_id, a.lc_value as value from com_btb_lc_master_details a,  com_btb_export_lc_attachment b, com_sales_contract c where a.id=b.import_mst_id and b.lc_sc_id=c.id and is_lc_sc=1 and c.beneficiary_name='$cbo_company_name' and c.lien_bank like '$cbo_lien_bank' and c.id in($txt_lc_sc_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.lc_number, a.lc_date, a.currency_id, a.item_category_id, a.lc_value order by a.lc_number";
				}
				//echo $sql;
				$result=sql_select($sql);
				$i=1; $tot_lc_value=0;
				foreach($result as $row)
				{
					 if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					$tot_lc_value+=$row[csf("value")];	
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="150"><? echo split_string($row[csf("lc_number")],17); ?></td>
						<td width="100" align="center"><? echo change_date_format($row[csf("lc_date")]); ?></td>
						<td width="90" align="center"><? echo $currency[$row[csf("currency_id")]]; ?></td>
						<td width="140" align="right"><? echo number_format($row[csf("value")],2); ?></td>
						<td style="padding-left:3px;"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
					</tr>
					<?	
					$i++;
				}
				?>
				 <tfoot>
					<th colspan="3" align="right" width="">Total</th>
					<th width="" align="right"><?php echo number_format($tot_lc_value,2); ?></th>
					<th width=""></th>
				</tfoot>
			</table>
		</div>
		<table width="650" border="0">
			 <tr>
				<td width="100%" colspan="2" height="40">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2" style="font-size:14px"><? echo "(U.S. ".strtoupper(number_to_words($tot_lc_value,"Dollar","Cents"))." ONLY)"; ?></td>
			</tr>
			<tr>
				<td width="100%" colspan="2" height="40">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2" width="100%" style="text-align:justify; font-size:14px">We further declare that we have not opened any other Back to Back L/C (Foreign or Local) for import of yarn against the captioned Export L/C.</td>
			</tr>
			<tr>
				<td width="100%" colspan="2" height="25">&nbsp;</td>
			</tr>
			<tr>
				<td width="250">&nbsp;</td>
				<td width="400" style="font-size:14px" align="right">For and on behalf of</td>
			</tr>
			<tr>
				<td colspan="2" width="100%" height="40">&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td style="font-size:14px" align="right"><? echo $bank_details[$cbo_lien_bank]["bank_name"]."<br>".$bank_details[$cbo_lien_bank]["address"]; ?></td>
			</tr>
		</table>
	</div>    
	<?	 
	
	/*$html = ob_get_contents();
	ob_clean();
	$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("*.xls") as $filename) {
	if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename"; 
	exit();
		
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
	echo "$total_data####$filename";
	exit();*/

disconnect($con);
}
?>

