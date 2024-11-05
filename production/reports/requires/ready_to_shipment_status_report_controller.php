<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_id = $_SESSION['logic_erp']['user_id'];
require_once('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 120, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- All --", $selected, "",0 );     	 
}


if ($action == "load_drop_down_buyer") {
    echo create_drop_down("cbo_buyer_id", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- All --", $selected, "load_drop_down( 'requires/ready_to_shipment_status_report_controller', this.value, 'load_drop_down_season_buyer', 'season_td');");
    exit();
}


if ($action=="load_drop_down_season_buyer")
{
    echo create_drop_down( "cbo_season_id", 120, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- All--", "", "" );
    exit();
}


if($action=="openmy_search_popup")
{
	echo load_html_head_contents("Search Info", "../../../", 1, 1,'','','');
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
	                    <th id="search_by_td_up" width="170">Please Enter Internal Ref</th>
	                    <th>Shipment Date</th>
	                    <th>
                            <input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"> 
                            <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                            <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
                        </th>
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                        	 <? 
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
								?>
	                        </td>                 
	                        <td align="center">	
	                    	<?
	                       		$search_by_arr=array(1=>"Internal Ref",2=>"Style Ref",3=>"Job No");
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $job_no; ?>'+'**'+'<? echo $type; ?>', 'search_list_view', 'search_div', 'ready_to_shipment_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
	                    	</td>
	                    </tr>
	                    <tr>
	                        <td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
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
	$company_id=$data[0];
	$job_no=$data[6];
	$type=$data[7];
	if($job_no!='') $job_no_cond="and a.job_no_prefix_num=$job_no";else $job_no_cond="";
	
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
	if(str_replace("'", "", $data[3])!="")
	{
		$search_string="".trim($data[3])."";
	}

	if($search_by==1) 
		$search_field="b.grouping"; 
	else if($search_by==2) 
		$search_field="a.style_ref_no"; 	
	else 
		$search_field="a.job_no_prefix_num";
	$search_cond="";
	if($search_string!="")	{$search_cond=" and $search_field='$search_string'";}
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
	$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$arr=array (0=>$company_library,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	if($type==1){$field='grouping';}
	else if($type==2){$field='job_no_prefix_num';}
	else{$field='style_ref_no';}
	
	$sql= "SELECT a.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id $search_cond $buyer_id_cond $job_no_cond $date_cond group by a.id,
         a.job_no, a.insert_date, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.grouping order by a.id, b.grouping"; 
    // echo $sql;
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Int Ref", "80,80,50,70,180","750","220",0, $sql , "js_set_value", "id,$field","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,grouping","",'','0,0,0,0,0,0','',1) ;
   exit(); 
}



if($action=="report_generate")
{ 
	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//---------------------------------------------------------
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_location_id=str_replace("'","",$cbo_location_id);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$cbo_season_id=str_replace("'","",$cbo_season_id);
	$cbo_order_type=str_replace("'","",$cbo_order_type);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_int_ref=str_replace("'","",$txt_int_ref);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_job_id=str_replace("'","",$txt_job_id);

	//---------------------------------------------------------
	$whereCon .= ($cbo_company_id==0)?"":" and a.COMPANY_NAME=$cbo_company_id";
	$whereCon .= ($txt_job_id == "")?"":" and a.id in($txt_job_id)";
	$whereCon .= ($cbo_location_id==0)?"":" and a.LOCATION_NAME=$cbo_location_id";
	$whereCon .= ($cbo_buyer_id==0)?"":" and a.BUYER_NAME=$cbo_buyer_id";
	$whereCon .= ($cbo_season_id==0)?"":" and a.SEASON_BUYER_WISE=$cbo_season_id";
	if($txt_job_id=="" && $txt_int_ref!=""){$whereCon .=" and b.GROUPING like('%$txt_int_ref%')";}
	if($txt_job_id=="" && $txt_style_ref!=""){$whereCon .=" and a.STYLE_REF_NO like('%$txt_style_ref%')";}
	if($txt_job_id=="" && $txt_job_no!=""){$whereCon .=" and c.JOB_NO_MST like('%$txt_job_no')";}
 
	if($txt_date_to !="")
    {
        if($db_type==0)
        {
            $date_to=change_date_format($txt_date_to,"yyyy-mm-dd","");
        }
		//$whereCon .= ($date_to=="")?"":" and b.PUB_SHIPMENT_DATE<='$date_to'";
    }
 	 
  
	if($cbo_order_type==2){$whereCon .=" and b.SHIPING_STATUS=2";}
	else if($cbo_order_type==3){$whereCon .=" and b.SHIPING_STATUS=1";}


 //---------------------------------------------------------
	
    $orderSql= "SELECT a.BUYER_NAME,a.STYLE_REF_NO,b.PO_NUMBER,b.GROUPING,c.JOB_NO_MST,c.COLOR_NUMBER_ID,c.SIZE_NUMBER_ID,c.ORDER_QUANTITY,c.id as COLOR_SIZE_ID
	from  wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.PO_BREAK_DOWN_ID and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.SHIPING_STATUS < 3 $whereCon order by b.GROUPING"; 
  // echo $orderSql;die;
    $orderSqlRes = sql_select($orderSql);
    foreach ($orderSqlRes as  $rows) 
    {
		$key=$rows[COLOR_SIZE_ID].'**'.$rows[COLOR_NUMBER_ID].'**'.$rows[SIZE_NUMBER_ID];
		$dataArr[$rows[BUYER_NAME]][$key]=array(
			BUYER_NAME=>$rows[BUYER_NAME],
			STYLE=>$rows[STYLE_REF_NO],
			JOB_NO=>$rows[JOB_NO_MST],
			PO_NUMBER=>$rows[PO_NUMBER],
			GROUPING=>$rows[GROUPING],
			COLOR=>$rows[COLOR_NUMBER_ID],
			SIZE=>$rows[SIZE_NUMBER_ID],
			ORDER_QTY=>$rows[ORDER_QUANTITY],
			COLOR_SIZE_ID=>$rows[COLOR_SIZE_ID],
		);
		$colorSizeIdArr[$rows[COLOR_SIZE_ID]]=$rows[COLOR_SIZE_ID];
		$colorIdArr[$rows[COLOR_NUMBER_ID]]=$rows[COLOR_NUMBER_ID];
		$sizeIdArr[$rows[SIZE_NUMBER_ID]]=$rows[SIZE_NUMBER_ID];
		$buyerIdArr[$rows[BUYER_NAME]]=$rows[BUYER_NAME];
    }
	unset($orderSqlRes);
   	 
			
	$proSql="select b.COLOR_SIZE_BREAK_DOWN_ID,b.PRODUCTION_QNTY,b.REJECT_QTY from PRO_GARMENTS_PRODUCTION_DTLS b where b.PRODUCTION_TYPE=8 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($colorSizeIdArr,0,'b.COLOR_SIZE_BREAK_DOWN_ID')."";
	//echo $proSql; 
    $prodSqlRes = sql_select($proSql);
    foreach ($prodSqlRes as  $rows) 
    {
		$proDataArr[ok_fin][$rows[COLOR_SIZE_BREAK_DOWN_ID]]+=$rows[PRODUCTION_QNTY];
		$proDataArr[rej_fin][$rows[COLOR_SIZE_BREAK_DOWN_ID]]+=$rows[REJECT_QTY];
	}
	unset($prodSqlRes);
	
	$exfSql="select b.COLOR_SIZE_BREAK_DOWN_ID, sum(case when a.entry_form<>85 then b.PRODUCTION_QNTY else 0 end )- sum(case when a.entry_form=85 then b.PRODUCTION_QNTY else 0 end ) as EXF_QTY  from pro_ex_factory_mst a,pro_ex_factory_dtls b where  a.id=B.MST_ID and  a.status_active=1 and a.is_deleted=0  and  b.status_active=1 and b.is_deleted=0 ".where_con_using_array($colorSizeIdArr,0,'b.COLOR_SIZE_BREAK_DOWN_ID')."  group by b.COLOR_SIZE_BREAK_DOWN_ID";
	 //echo $exfSql; 
    $exfSqlRes = sql_select($exfSql);
    foreach ($exfSqlRes as  $rows) 
    {
		$exfDataArr[exf_qty][$rows[COLOR_SIZE_BREAK_DOWN_ID]]+=$rows[EXF_QTY];
	}
	unset($exfSqlRes);
	
 	$buyer_lib=return_library_array( "select id,SHORT_NAME as buyer_name from lib_buyer where is_deleted=0 and status_active=1 ".where_con_using_array($buyerIdArr,0,'id')."", "id", "buyer_name"  );
	$color_lib=return_library_array( "select id,color_name from lib_color where is_deleted=0 and status_active=1  ".where_con_using_array($colorIdArr,0,'id')." ", "id", "color_name"  ); 
	$size_lib=return_library_array( "select id,size_name from lib_size where is_deleted=0 and status_active=1  ".where_con_using_array($sizeIdArr,0,'id')." ", "id", "size_name"  ); 
	
	
	$width=1015;	 
	ob_start();
	?>
    <div style="width:<?=$width+20;?>px; margin: 0 auto"> 
             <table width="<?=$width;?>" cellspacing="0" border="1" class="rpt_table" rules="all">
                <thead> 	 	 	 	 	 	
                    <th width="30">Sl</th>  
                    <th width="80">Buyer</th>
                    <th width="100">Int Ref</th>
                    <th width="70">Job No</th>  
                    <th>Style</th>
                    <th width="100">PO No</th>
                    <th width="80">Color</th>
                    <th width="60">Size</th>
                    <th width="60">PO Qty</th>
                    <th width="60">OK Fin Qty</th> 
                    <th width="60">Reject Qty</th>
                    <th width="60">Ex-Fact Qty</th>
                    <th width="60">Ready to Ship Qty</th>
                    <th width="60">Ex-Fac Balance</th>
                </thead>
            </table>
            <div style="max-height:350px; overflow-y:auto; width:<?=$width+18;?>px" id="scroll_body">
                <table border="1" cellpadding="0" cellspacing="0" class="rpt_table"  width="<?=$width;?>" rules="all" id="table_body" >
                	<tbody>
                    	
                        <?
                        $i=1;
						foreach($dataArr as $buyerRows){
						$buyerTotal=array();
						foreach($buyerRows as $rows){
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						$ready_to_ship_qty=$proDataArr[ok_fin][$rows[COLOR_SIZE_ID]]-$exfDataArr[exf_qty][$rows[COLOR_SIZE_ID]];
						$balance_qty=$rows[ORDER_QTY]-$exfDataArr[exf_qty][$rows[COLOR_SIZE_ID]];
						
						
						//buyer total......................................................
						$buyerTotal[exf_qty]+=$exfDataArr[exf_qty][$rows[COLOR_SIZE_ID]];
						$buyerTotal[ok_fin]+=$proDataArr[ok_fin][$rows[COLOR_SIZE_ID]];
						$buyerTotal[order_qty]+=$rows[ORDER_QTY];
						$buyerTotal[rej_fin]+=$proDataArr[rej_fin][$rows[COLOR_SIZE_ID]];
						$buyerTotal[ready_to_ship_qty]+=$ready_to_ship_qty;
						$buyerTotal[balance_qty]+=$balance_qty;
						
						//grand total......................................................
						$grandTotal[exf_qty]+=$exfDataArr[exf_qty][$rows[COLOR_SIZE_ID]];
						$grandTotal[ok_fin]+=$proDataArr[ok_fin][$rows[COLOR_SIZE_ID]];
						$grandTotal[order_qty]+=$rows[ORDER_QTY];
						$grandTotal[rej_fin]+=$proDataArr[rej_fin][$rows[COLOR_SIZE_ID]];
						$grandTotal[ready_to_ship_qty]+=$ready_to_ship_qty;
						$grandTotal[balance_qty]+=$balance_qty;
						
						
						?>
                            <tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i;?>','<?=$bgcolor;?>')" id="tr_<?=$i;?>">
                                <td width="30" align="center"><?=$i;?></td>  
                                <td width="80"><?=$buyer_lib[$rows[BUYER_NAME]];?></td>
                                <td width="100"><p><?=$rows[GROUPING];?></p></td>
                                <td width="70"><?=$rows[JOB_NO];?></td>  
                                <td><p><?=$rows[STYLE];?></p></td>
                                <td width="100"><p><?=$rows[PO_NUMBER];?></p></td>
                                <td width="80"><p><?=$color_lib[$rows[COLOR]];?></p></td>
                                <td width="60" align="center"><?=$size_lib[$rows[SIZE]];?></td>
                                <td width="60" align="right"><?=$rows[ORDER_QTY];?></td>
                                <td width="60" align="right"><?=$proDataArr[ok_fin][$rows[COLOR_SIZE_ID]];?></td> 
                                <td width="60" align="right"><?=$proDataArr[rej_fin][$rows[COLOR_SIZE_ID]];?></td>
                                <td width="60" align="right"><?=$exfDataArr[exf_qty][$rows[COLOR_SIZE_ID]];?></td>
                                <td width="60" align="right"><?=$ready_to_ship_qty;?></td>
                                <td width="60" align="right"><?=$balance_qty;?></td>
                            </tr>
                        <?
						$i++;
						}
							echo "<tr bgcolor='#CCCCCC'>
								<td align='right' colspan='8'>Buyer Total</td>
								<td align='right'>".$buyerTotal[order_qty]."</td>
								<td align='right'>".$buyerTotal[ok_fin]."</td>
								<td align='right'>".$buyerTotal[rej_fin]."</td>
								<td align='right'>".$buyerTotal[exf_qty]."</td>
								<td align='right'>".$buyerTotal[ready_to_ship_qty]."</td>
								<td align='right'>".$buyerTotal[balance_qty]."</td>
							</tr>";
						}
						
						?>
                   </tbody>
               </table>
           </div>
            <table width="<?=$width;?>" cellspacing="0" border="1" class="rpt_table" rules="all">
                <tfoot>	 	 	 	 	 	
                    <th colspan="8">Grand Total</th>
                    <th width="60"><?=$grandTotal[order_qty];?></th>
                    <th width="60"><?=$grandTotal[ok_fin];?></th> 
                    <th width="60"><?=$grandTotal[rej_fin];?></th>
                    <th width="60"><?=$grandTotal[exf_qty];?></th>
                    <th width="60"><?=$grandTotal[ready_to_ship_qty];?></th>
                    <th width="60"><?=$grandTotal[balance_qty];?></th>
                </tfoot>
            </table>	
    </div>    
	
	<?	
	
	unset($dataArr);
	unset($proDataArr);
	unset($exfDataArr);
	unset($buyerTotal);
	unset($grandTotal);
	unset($color_lib);
	unset($size_lib);
	unset($buyer_lib);
	unset($colorSizeIdArr);
	unset($colorIdArr);
	unset($sizeIdArr);
	unset($buyerIdArr);
 	
	
	
	
	foreach (glob($user_id."_*.xls") as $filename)
	{		
		@unlink($filename);
	}
	$name=$user_id."_".time().".xls";
	$create_new_excel = fopen($name, 'w') or die('can not open');	
	$is_created = fwrite($create_new_excel,ob_get_contents()) or die('can not write');
	echo "####".$name;
	exit();
}



?>