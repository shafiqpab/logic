<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);

	if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	else $load_function="";
	
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $data[2], "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
	}	
	exit();	 
} 

//item search------------------------------//
if($action=="batch_popup")
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
		
		function fn_check_batch()
		{ 
			show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $company; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_batch_search_list_view', 'search_div', 'wash_batch_status_report_controller', 'setFilterGrid(\'list_view\',-1);');
		}
    </script>
    <body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="650" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>                	 
						<th>Search By</th>
                        <th>Search</th>
                        <th>Date Range</th>
 						<th>
                       		<input type="reset" name="re_button" id="re_button" value="Reset" style="width:80px" class="formbutton"  />
                            <input type='hidden' id='txt_selected_id' />
							<input type='hidden' id='txt_selected' />
							<input type='hidden' id='txt_selected_no' />
                        </th>

					</tr>
				</thead>
				<tbody>
					<tr class="general">
                        <td>	
                            <?
                                $search_by_arr=array(1=>"Batch No",2=>"Style No");
                                echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                            ?>
                        </td>                 
                        <td>				
                            <input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                        </td> 
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" value="">
                                &nbsp; To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date"  value="">
                        </td>						
                        <td>
                           <input type="button" name="btn_show" class="formbutton" value="Show" onClick="fn_check_batch()" style="width:80px;" />	
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

if($action=="create_batch_search_list_view")
{
	$data=explode('_',$data);
	$search_string=trim($data[0]);
	$search_by =$data[1];
	$company_id =$data[2];
	$date_from=$data[3];
	$date_to=$data[4];
	
	if($search_string!='')
	{
		if($search_by==1)
		{
			$search_field="and a.batch_no like '$search_string'";
		}
		else if($search_by==2)
		{
			//$style_cond=" and a.style_ref_no like '$search_string'";
			$buyer_style_cond=" and b.buyer_style_ref like '%$search_string%'";
		}
		else
		{
			$search_field='booking_no';
		}
	}
	 
	$order_buyer_po_array=array();
	$buyer_po_arr=array();
	$order_buyer_po='';
	$order_sql ="select b.id,b.buyer_po_no,b.buyer_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.entry_form='295' $buyer_style_cond"; 
	$order_sql_res=sql_select($order_sql);
	foreach ($order_sql_res as $row)
	{
		$order_buyer_po_array[]=$row[csf("id")];
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
	}
	//unset($order_sql_res);
	$order_buyer_po=implode(",",$order_buyer_po_array);
	//echo $order_buyer_po; 
	if ($order_buyer_po!="") $order_order_buyer_poCond=" and b.po_id in ($order_buyer_po)"; else $order_order_buyer_poCond="";
	if($data[0] != "") $date_cond="";
	else
	{
		if($date_from != "" && $date_to != ""){
			if($db_type==0)
			{
				$date_cond=" and a.batch_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
			}
			else
			{
				$date_cond=" and a.batch_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";
			}
		}
	}
	
	 $sql = "select a.id, a.batch_no, a.extention_no,a.batch_against, a.batch_for,a.color_id,c.job_no_mst,c.buyer_po_no, c.buyer_style_ref,c.order_no from pro_batch_create_mst a, pro_batch_create_dtls b,subcon_ord_dtls c ,subcon_ord_mst d  where a.id=b.mst_id and a.company_id=$company_id  and b.po_id=c.id and c.mst_id=d.id and  d.entry_form=295  $search_field $date_cond and a.status_active=1 and a.entry_form=316 and a.is_deleted=0 $order_order_buyer_poCond group by a.id, a.batch_no, a.extention_no,a.batch_against, a.batch_for,a.color_id,c.job_no_mst,c.buyer_po_no, c.buyer_style_ref, c.order_no"; 
	
	$arr=array(5=>$batch_against,6=>$color_arr);
	echo create_list_view("list_view", "Batch No,Extention No,Job No,Order No,Buyer Style, Batch Against,Color","160,100,120,120,120,100","900","260",0, $sql , "js_set_value", "id,batch_no", "", 1, "0,0,0,0,0,batch_against,color_id", $arr, "batch_no,extention_no,job_no_mst,order_no,buyer_style_ref,batch_against,color_id", "","","0","",1) ;	
	exit();	
}

if($action=="report_generate")
{ 
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$batch_no=str_replace("'","",$txt_batch_no);
	$batch_id=str_replace("'","",$txt_batch_id);
	$cbo_buyer_id=str_replace("'","",$cbo_party_name);
	$company_id=str_replace("'","",$cbo_company_id);
	$cbo_within_group=str_replace("'","",$cbo_within_group);
	$shift=str_replace("'","",$cbo_shift);
	
	$from_date=str_replace("'","",$txt_date_from);
	$to_date=str_replace("'","",$txt_date_to);
	
	if($db_type==0) $select_from_date=change_date_format($from_date,'yyyy-mm-dd');
	if($db_type==2) $select_from_date=change_date_format($from_date,'','',1);
	if($db_type==0) $select_to_date=change_date_format($to_date,'yyyy-mm-dd');
	if($db_type==2) $select_to_date=change_date_format($to_date,'','',1);
	if($cbo_buyer_id==0) $party_con=""; else $party_con=" and d.party_id='$cbo_buyer_id'";
	if($cbo_within_group==0) $within_group=""; else $within_group=" and d.within_group='$cbo_within_group'";
	if($cbo_within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
	if(str_replace("'","",$company_id)==0)$company_name=""; else $company_name=" and d.company_id=$company_id";
	if(str_replace("'","",$shift)==0)$shift_cond=""; else $shift_cond=" and a.shift_id=$shift";
	
	
	if ($batch_no=="") $batch_no_cond=""; else $batch_no_cond=" and a.id in ($batch_id) ";
	
	
	if($db_type==0)
	{
		if( $from_date==0 && $to_date==0 ) $batch_date=""; else $batch_date= " and a.batch_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";	
 	}
	if($db_type==2)
	{
		if( $from_date==0 && $to_date==0 ) $batch_date=""; else $batch_date= " and a.batch_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";	
 	
	}
 	
	
	$receeive_qty_array=array();
	$sql_receeive="Select b.job_dtls_id,a.subcon_date,b.quantity as issue_qnty from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.entry_form=297 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_receeive_result=sql_select($sql_receeive); $receeive_date_array=array(); 
	foreach ($sql_receeive_result as $row)
	{
		$receeive_qty_array[$row[csf('job_dtls_id')]]['issue_qnty']+=$row[csf('issue_qnty')];
	}
	
	
	
		 $batch_sql = "select a.batch_no,a.extention_no,a.shift_id,a.gmts_type,c.job_no_mst,c.buyer_po_no, c.buyer_style_ref,d.party_id,sum(b.roll_no) as batch_qnty, sum(c.order_quantity) as order_quantity,b.po_id from pro_batch_create_mst a, pro_batch_create_dtls b,subcon_ord_dtls c ,subcon_ord_mst d 
			where a.id=b.mst_id and a.process_id='1'  and  a.status_active=1 and  a.entry_form=316 and b.po_id=c.id and c.mst_id=d.id and  d.entry_form=295 and a.is_deleted=0  $batch_date $within_group $party_con $company_name $batch_no_cond $shift_cond group by a.batch_no,a.extention_no,a.shift_id,a.gmts_type,c.job_no_mst,c.buyer_po_no, c.buyer_style_ref,d.party_id,b.po_id";
	
	 //echo $batch_sql;
	 
	$batch_sql_result=sql_select($batch_sql);
	
	ob_start();
	
	
	
	?>
    <style type="text/css">
		.wrd_brk{word-break: break-all;}
	</style>
     <fieldset style="width:1220px;">
     <div style="width:1220px; margin:0 auto;">
          <table cellpadding="0" cellspacing="0" width="1220">
         		<tr  class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="12" style="font-size:20px"><strong><? echo 'Wash Batch Status Report'; ?></strong></td>
                </tr>
                <tr  class="form_caption" style="border:none;">
                    <td colspan="12" align="center" style="border:none; font-size:14px;">
                        <b><? echo $company_library[$company_id]; ?></b>
                    </td>
                </tr>
                <tr  class="form_caption" style="border:none;">
                    <td align="center" width="100%" colspan="12" style="font-size:12px">
                        <? if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") echo "From ".change_date_format(str_replace("'","",$txt_date_from),'dd-mm-yyyy')." To ".change_date_format(str_replace("'","",$txt_date_to),'dd-mm-yyyy')."" ;?>
                    </td>
                </tr>
            </table>
            <table width="1200" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
                <thead>
                        <th width="35">SL</th>
                        <th width="100">Party</th>
                        <th width="100">Buyer PO</th>
                        <th width="100">Buyer Style</th>
                        <th width="130">Job No</th>
                        <th width="130">Shift</th>
                        <th width="100">Gamts Type</th>
                        <th width="100">Batch No</th>
                        <th width="80">Ext No</th>
                        <th width="80">Po Qty</th>
                        <th width="80">Material Issue Qty</th>
                        <th>Batch Qty</th>
                        
                </thead>
            </table>
             <div style="max-height:300px; overflow-y:scroll; width:1220px" id="scroll_body">
             <table width="1200" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_body">
				<?  
					$i=1;
					
					foreach($batch_sql_result as $row)
					{
						$issue_qty =$receeive_qty_array[$row[csf('po_id')]]['issue_qnty'];
						?>
						
                          <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td  width="35" id="wrd_brk"><? echo $i; ?></td>
                            <td width="100" id="wrd_brk"><? echo $party_arr[$row[csf("party_id")]]; ?></td>
                            <td  width="100" id="wrd_brk"><? echo $row[csf("buyer_po_no")]; ?></td>
                            <td  width="100" id="wrd_brk"><? echo $row[csf("buyer_style_ref")]; ?></td>
                            <td   width="130" id="wrd_brk"><? echo $row[csf("job_no_mst")]; ?></td>
                            <td width="130" id="wrd_brk"><? echo $shift_name[$row[csf("shift_id")]]; ?></td>
                            <td  width="100" id="wrd_brk"><? echo $wash_gmts_type_array[$row[csf("gmts_type")]]; ?></td>
                            <td  width="100" id="wrd_brk"><? echo $row[csf("batch_no")]; ?></td>
                            <td  width="80" id="wrd_brk" align="right"><? echo $row[csf("extention_no")]; ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo $row[csf('order_quantity')]; ?></td>
                            <td width="80" id="wrd_brk" align="right"><?php echo $issue_qty; ?></td>
                            <td id="wrd_brk" align="right"><?php echo $row[csf('batch_qnty')]; ?></td>
                            
						  </tr>
                       
						<?		
					$i++;
					}
                  ?>
                </table>
         	</div>
         <table width="1200" border="1" cellpadding="0" cellspacing="0" rules="all"> 
			<tr class="tbl_bottom">
                <td  width="35" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td   width="130" >&nbsp;</td>
                <td width="130" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td width="80"></td>
                <td width="80"></td>
                <td width="80">Grand Total:</td>
                <td  id="gt_batch_qty_id"></td>
			</tr>
		</table> 
     </div>
     </fieldset>
    <?
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename"; 
    exit();
}

?>