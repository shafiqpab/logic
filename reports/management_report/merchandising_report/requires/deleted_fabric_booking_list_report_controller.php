<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

if($db_type==2) $select_date=" to_char(a.insert_date,'YYYY')";
else if ($db_type==0) $select_date=" year(a.insert_date)";

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond  group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0); 
	exit();
}



if($action=="work_order_popup")
{

	echo load_html_head_contents("Work Order No Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
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
			var str_or = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
			
			toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );
			
			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
				selected_no.push( str_or );				
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
			//alert(num);
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name ); 
			$('#txt_selected_no').val( num );
		}
    </script>

 </head>

 <body>
 <div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" width="100%" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th style="width:100px;">Buyer</th>
					<th style="width:100px;">Booking No</th>
                    <th style="width:200px;"  >Booking Date</th>
                    <th ><input type="reset" name="button" class="formbutton" value="Reset"  style="width:80px;"></th> 
                    <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
						<td align="center">
                            <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes"   style="width:100px">
                            
                        </td>	
                       <!--cbo_year_selection-->
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                        </td>	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_year_selection').value+'**'+document.getElementById('txt_booking_no').value, 'work_order_list_view', 'search_div', 'deleted_fabric_booking_list_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
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
 <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
 </html>
 <?
	exit(); 
}

if($action=="work_order_list_view")
{
	extract($_REQUEST);
	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	list($company,$buyer,$start_date,$end_date,$cbo_year,$booking_no)=explode('**',$data);
	?>
    <script>
    </script>
    <?
	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$booking_prefix_no=str_replace("'","",$booking_no);
	$cbo_year=str_replace("'","",$cbo_year);
	//echo $cbo_year.'dd';
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(b.insert_date)=$cbo_year";
		}
		else
		{
			$year_cond=" and to_char(b.insert_date,'YYYY')=$cbo_year";	
		}
	}
	
	/*if($search_type==1 && $search_value!=''){
		$search_con=" and a.po_number like('%$search_value')";	
	}
	elseif($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value')";		
	}
	elseif($search_type==3 && $search_value!=''){
		$search_con=" and a.job_no_mst like('%$search_value')";		
	}*/
	
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.booking_date between '".change_date_format(trim($start_date),"yyyy-mm-dd")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd")."'";
		}
		else
		{
			$date_cond="and a.booking_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	
	
	
	
	if($buyer!=0) $buyer_cond="and a.buyer_id=$buyer"; else $buyer_cond="";
	if($booking_prefix_no!="")
	{
		if($db_type==0) $booking_cond="and a.booking_no_prefix_num in($booking_prefix_no)  "; 
		else $booking_cond="and a.booking_no_prefix_num in($booking_prefix_no)  ";
	}
	else $booking_cond="";
	
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	
	$arr=array (1=>$buyer_arr,3=>$pay_mode,9=>$item_category,10=>$suplier,11=>$approved,12=>$yes_no);
	
	//echo $style_cond."jahid";die;
	// $sql = "select a.id,a.buyer_id,a.booking_no_prefix_num,a.booking_no,a.booking_date,a.pay_mode from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.booking_type in(1,4) and a.company_id=$company $buyer_cond  $date_cond and a.status_active in (1,0)  and b.status_active=0 group by a.id,a.buyer_id,a.booking_no_prefix_num,a.booking_no,a.booking_date,a.pay_mode order by a.id desc"; 

	$sql="SELECT a.id,a.buyer_id,a.booking_no_prefix_num,a.booking_no,a.booking_date,a.pay_mode
		from wo_booking_mst a,wo_booking_dtls c, wo_po_break_down b,wo_po_details_master d 
		where a.booking_no=c.booking_no and
		 	  c.job_no=b.job_no_mst and
		 	  d.job_no=b.job_no_mst and 
			  d.job_no=c.job_no	 and 
		 	  b.id=c.po_break_down_id and		   
		      b.is_deleted=0  and  
			   a.entry_form in (118,108,88) and
			  b.status_active=1 and
		      c.is_deleted=1 and
		      c.status_active=0  and a.company_id=$company $buyer_cond  $date_cond $booking_cond $year_cond
		  group by  a.id,a.buyer_id,a.booking_no_prefix_num,a.booking_no,a.booking_date,a.pay_mode
		 Union all 
			SELECT  a.id,a.buyer_id,a.booking_no_prefix_num,a.booking_no,a.booking_date,a.pay_mode
			From wo_non_ord_samp_booking_mst a ,wo_non_ord_samp_booking_dtls b
			where a.booking_no=b.booking_no and 
			 a.is_deleted in (1,0) and 
			  a.booking_type in (4) and 
			 b.status_active=0 and 
			 a.company_id=$company $buyer_cond  $date_cond $booking_cond $year_cond
			Group By a.id,a.buyer_id,a.booking_no_prefix_num,a.booking_no,a.booking_date,a.pay_mode";
	 //echo $sql;
	echo create_list_view("list_view", "Booking No,Buyer,Booking Date,Pay Mode","150,100,100,100","500","150",0, $sql , "js_set_value", "id,booking_no_prefix_num", "", 1, "0,buyer_id,0,pay_mode", $arr, "booking_no,buyer_id,booking_date,pay_mode", "","","0,0,3,0,0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_order_id_no;?>';
	var style_id='<? echo $txt_wo_id;?>';
	var style_des='<? echo $txt_wo_no;?>';
	//alert(style_id);
	if(style_no!="")
	{
		style_no_arr=style_no.split(",");
		style_id_arr=style_id.split(",");
		style_des_arr=style_des.split(",");
		var str_ref="";
		for(var k=0;k<style_no_arr.length; k++)
		{
			str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k];
			js_set_value(str_ref);
			//alert(str_ref);
		}
	}
	</script>
    <?
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));


	$txt_wo_no=str_replace("'","",$txt_wo_no);
	$txt_wo_id=str_replace("'","",$txt_wo_id);	
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_year=str_replace("'","",$cbo_year);
	

	if($db_type==0)
	{
		if(trim($cbo_year)!=0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if(trim($cbo_year)!=0) $year_cond=" $year_field_con=$cbo_year"; else $year_cond="";
	}	
	if($cbo_company_name!=0) $company_name_cond="and a.company_id in($cbo_company_name) ";else $company_name_cond="";

	
	
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_id=$cbo_buyer_name";
	}
	
	$booking_date_cond="";
	if($txt_date_from!="" && $txt_date_to!="")
	{
		$booking_date_cond="and a.booking_date between '$txt_date_from' and '$txt_date_to' ";
	}
	
	
	$job_no_cond="";
	if(trim($style_ref_id)!="") $job_no_cond="and d.id  in($style_ref_id)";
	
	$job_no_cond2="";
	if(trim($txt_style_ref)!="") $job_no_cond2="and d.job_no_prefix_num  in($txt_style_ref)";
	
	$wo_order_cond="";
	if($txt_wo_id!="") $wo_order_cond="and a.id in($txt_wo_id)";
	$wo_order_no_cond="";
	if($txt_wo_no!="") $wo_order_no_cond="and a.booking_no_prefix_num in($txt_wo_no)";
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	$user_arr=return_library_array("select id,user_name from  user_passwd","id","user_name");
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	 

		$sql_wo="SELECT a.buyer_id,
					a.company_id,
					a.booking_date,
					a.booking_no,
					c.job_no,       
					c.inserted_by,
					a.entry_form,
					a.booking_type,
					a.is_short,
					COUNT (c.id)                                         AS dtls_id,
					COUNT (CASE WHEN c.is_deleted = 1 THEN c.id END)     AS is_deleted_id,
					listagg(c.updated_by, ',') within group (order by c.updated_by) updated_by,a.is_deleted, COUNT (CASE WHEN c.status_active = 3 THEN c.id END) AS full_booked_id 
					FROM wo_booking_mst a left join wo_booking_dtls c on a.booking_no = c.booking_no 
				 
				WHERE   a.entry_form IN (118, 108, 88)					 
					$buyer_id_cond  $wo_order_no_cond $wo_order_cond $booking_date_cond   $year_cond  $company_name_cond 
			GROUP BY a.buyer_id,
					a.company_id,
					a.supplier_id,
					a.booking_date,
					a.booking_no,
					c.job_no,
					
					c.inserted_by,
					a.entry_form,
					a.booking_type,
					a.is_short,a.is_deleted 
			UNION ALL
				SELECT a.buyer_id,
					a.company_id,
					a.booking_date,
					a.booking_no,
					NULL                                                 AS job_no,
				
					b.inserted_by,
					a.entry_form_id                                      AS entry_form,
					a.booking_type,
					a.is_short,
					COUNT (b.id)                                         AS dtls_id,
					COUNT (CASE WHEN b.is_deleted = 1 THEN b.id END)     AS is_deleted_id,
					listagg(b.updated_by, ',') within group (order by b.updated_by) updated_by,a.is_deleted 
					, COUNT (CASE WHEN b.status_active = 3 THEN b.id END) AS full_booked_id
				FROM wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
				WHERE     a.booking_no = b.booking_no
					AND a.is_deleted IN (1, 0)
					$buyer_id_cond $company_name_cond $wo_order_no_cond $wo_order_cond $booking_date_cond   $year_cond 
			GROUP BY a.buyer_id,
					a.company_id,
					a.booking_date,
					a.booking_no,
					
					b.inserted_by,
					a.entry_form_id,
					a.booking_type,
					a.is_short,a.is_deleted";

	//echo $sql_wo;

	$sql_wo_result=sql_select($sql_wo);
	
	ob_start(); 
	?>
        <div style="width:1120px; margin: auto;" >
			<fieldset style="width:100%;" align="center" >	
             <table width="1120px" style="font-size:22px">
                <tr>
                    <td align="center" width="100%" colspan="70" class="form_caption"><? echo $report_title.'<br>'.$company_library[str_replace("'","",$cbo_company_name)].'<br/>';
					if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") echo 'From : '.change_date_format(str_replace("'","",$txt_date_from)).' To : '.change_date_format(str_replace("'","",$txt_date_to)) 
					 ?></td>
                </tr>
            </table>
            
		   <br>
		  
		    <br>
		   <table width="1120" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
		   
                <thead>
                	
					<tr style="font-size:20px">
                        <th width="30">SL</th>
						<th width="200">Comapny</th>
                        <th width="100">Booking Number</th>
						<th width="120">Booking Type</th>
						<th width="100">Buyer</th>
						<th width="100">Job No</th>
						<th width="120">Booking Create Date</th>								
						<th width="120">Insert User</th>
					   <th width="120">Delete User</th>	
					   <th width="120">Remarks</th>						 
					</tr>
					
                 </thead>
           		</table>	
                <div class="scroll_div_inner"  style="width:1140px; max-height:400px;overflow-y:scroll;" align="left" id="scroll_body">
				<table class="rpt_table" width="1120" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<?
				$j=1;
				$bookingType="";$remarks="";
				foreach($sql_wo_result  as $value)
				{
					
				  	$bgcolor=($j%2==0)?"#E9F3FF":"#FFFFFF";
					if($value[csf('entry_form')]==118){
						$bookingType="Main";
					}else if($value[csf('booking_type')]==4 && $value[csf('is_short')]==2){
						$bookingType="Sample With Order";
					}else if($value[csf('entry_form')]==108 &&  $value[csf('booking_type')]==1 && $value[csf('is_short')]==2){
						$bookingType="Partial";
					}else if($value[csf('booking_type')]==4){
						$bookingType="Sample With Out Order";
					}else if($value[csf('entry_form')]==88 &&  $value[csf('booking_type')]==1 && $value[csf('is_short')]==1){
						$bookingType="Short";
					}
					$deleteUser=explode(",",$value[csf('updated_by')]);
					$deleteUserArr=array_unique($deleteUser);
					foreach($deleteUserArr as $val){
						if($val>0){
						$deleteUserids[$user_arr[$val]]=$user_arr[$val];
						}
					}
				 if($value[csf('dtls_id')]==$value[csf('is_deleted_id')] || $value[csf('is_deleted')]==1 || $value[csf('full_booked_id')] >0 || $value[csf('dtls_id')] <= 0){

						if($value[csf('full_booked_id')] >0){
							$remarks="Full Booked Found";
						}else if($value[csf('dtls_id')] <= 0){
							$remarks="Booking Details Not Found";
						}

 
				   ?>
					 <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trwo_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="trwo_<? echo $j; ?>" style="font-size:16px">
						 <td width="30" align="center"><? echo $j;?></td>
						  <td width="200" align="left"><p><? echo $company_library[$value[csf('company_id')]]; ?></p></td>
						  <td width="100" align="center" title="<?=$entry_form[$value[csf('entry_form')]];?>"><p><? echo $value[csf('booking_no')]; ?></p></td>
						  <td width="120" align="left"><p><? echo $bookingType; ?></p></td>
						  <td width="100" align="center"><p><? echo $buyer_arr[$value[csf('buyer_id')]]; ?></p></td>
						  <td width="100" align="center"><p><? echo $value[csf('job_no')]; ?></p></td>
						  <td width="120" align="center"><p><? echo change_date_format($value[csf('booking_date')]); ?></p></td>				
						  <td width="120" align="center" title="<?=$value[csf('inserted_by')];?>"><p><? echo $user_arr[$value[csf('inserted_by')]]; ?></p></td>
						  <td width="120" align="left" title="<?=$value[csf('updated_by')];?>"><div style="word-wrap:break-word; width:120px"><? echo  implode(",",$deleteUserids); ?></div></td>
						  <td width="120" align="center" ><p><?=$remarks; ?></p></td>
					</tr>
					<?
					$j++;
					} 
				}
					?>
				 
					
				</table>
				
           </div>
     	
           
          </fieldset>
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

?>
