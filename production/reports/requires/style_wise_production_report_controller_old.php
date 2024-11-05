<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$color_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name");
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );
  exit();	 
}

//item style-------------------------------------------------------------------------------------------------------------------------//
if($action=="style_wise_search")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
<script>
		
		var selected_id = new Array;
		var selected_name = new Array;
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
				var str = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );					
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 ); 
				}
				var id = ''; var name = ''; var job = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ','; 
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 ); 
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name ); 
		}
    </script>
<?
	extract($_REQUEST);
	if($company==0) $company_name=""; else $company_name="and a.company_name=$company";
	if($buyer==0) $buyer_name=""; else $buyer_name="and a.buyer_name=$buyer";
	if(str_replace("'","",$job_id)!="")  $job_cond="and b.id in(".str_replace("'","",$job_id).")";
    else  if (str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and b.job_no_mst='".$job_no."'";
    if($db_type==2) $year_cond="  extract(year from b.insert_date) as year";
	if($db_type==0) $year_cond="  SUBSTRING_INDEX(b.insert_date, '-', 1) as year";
	$sql = "select a.id,a.style_ref_no,a.job_no_prefix_num, $year_cond from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst $company_name $buyer_name $job_cond "; 
	echo create_list_view("list_view", "Style Refference,Job no,Year","190,100,100","440","310",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}

//order wise browse-------------------------------------------------------------------------------------------------------------------------------------//
if($action=="job_wise_search")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
<script>
		
		var selected_id = new Array;
		var selected_name = new Array;
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
				var str = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );					
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 ); 
				}
				var id = ''; var name = ''; var job = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ','; 
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 ); 
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name ); 
		}
    </script>
<?
	extract($_REQUEST);
	if($company==0) $company_name=""; else $company_name=" and b.company_name=$company";//job_no
	if($buyer==0) $buyer_name=""; else $buyer_name="and b.buyer_name=$buyer";
    if($db_type==2) $year_cond="  extract(year from b.insert_date) as year";
	if($db_type==0) $year_cond="  SUBSTRING_INDEX(b.insert_date, '-', 1) as year";
	$sql = "select distinct b.id,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$year_cond from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $company_name  $buyer_name";
	echo create_list_view("list_view", "Order Number,Job No,Year,Style Ref","150,100,100,100","500","310",0, $sql , "js_set_value", "id,job_no_mst", "", 1, "0", $arr, "po_number,job_no_prefix_num,year,style_ref_no", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}

//order wise browse------------------------------//
if($action=="order_wise_search")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
<script>
		
		var selected_id = new Array;
		var selected_name = new Array;
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
				var str = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );					
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 ); 
				}
				var id = ''; var name = ''; var job = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ','; 
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 ); 
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name ); 
		}
    </script>
<?
	extract($_REQUEST);
	if($company==0) $company_name=""; else $company_name=" and b.company_name=$company";
	if($buyer==0) $buyer_name=""; else $buyer_name="and b.buyer_name=$buyer";
	if(str_replace("'","",$job_id)!="")  $job_cond="and a.id in(".str_replace("'","",$job_id).")";
    else  if (str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and a.job_no_mst='".$job_no."'";
	if(str_replace("'","",$style_id)!="")  $style_cond="and b.id in(".str_replace("'","",$style_id).")";
    else  if (str_replace("'","",$style_no)=="") $style_cond=""; else $style_cond="and b.style_ref_no='".$style_no."'";
	if($db_type==2) $year_cond="  extract(year from b.insert_date) as year";
	if($db_type==0) $year_cond="  SUBSTRING_INDEX(b.insert_date, '-', 1) as year";
	
	$sql = "select distinct a.id,a.po_number,b.style_ref_no,b.job_no_prefix_num,$year_cond from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $company_name $job_cond  $buyer_name $style_cond";
	echo create_list_view("list_view", "Style Ref,Order Number,Job No, Year","150,150,100,100,","550","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "style_ref_no,po_number,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}

if($action=="generate_report")
{ 
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
	$job_cond_id=""; 
	$style_cond="";
	$order_cond="";
   	if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and b.company_name=".str_replace("'","",$cbo_company_name)."";
	if(str_replace("'","",$cbo_buyer_name)==0)  $buyer_name=""; else $buyer_name="and b.buyer_name=".str_replace("'","",$cbo_buyer_name)."";
	if(str_replace("'","",$hidden_job_id)!="")  $job_cond_id="and b.id in(".str_replace("'","",$hidden_job_id).")";
	else  if (str_replace("'","",$txt_job_no)=="") $job_cond_id=""; else $job_cond_id="and a.job_no_mst='".str_replace("'","",$txt_job_no)."'";
	if(str_replace("'","",$hidden_style_id)!="")  $style_cond="and b.id in(".str_replace("'","",$hidden_style_id).")";
	else  if (str_replace("'","",$txt_style_no)=="") $style_cond=""; else $style_cond="and b.style_ref_no='".$txt_style_no."'";
	if (str_replace("'","",$hidden_order_id)!=""){ $job_cond_id="and a.id in (".str_replace("'","",$hidden_order_id).")";  }
	else if (str_replace("'","",$txt_order_no)=="") $order_cond=""; else $order_cond="and a.po_number='".str_replace("'","",$txt_order_no)."'"; 
    $serch_by=str_replace("'","",$cbo_search_by);
	
  if(str_replace("'","",$cbo_search_by)==1)
  {
 ?>
  <fieldset style="width:3050px;">
        	   <table width="1880"  cellspacing="0"   >
                    <tr class="form_caption" style="border:none;">
                            <td colspan="29" align="center" style="border:none;font-size:14px; font-weight:bold" >Daily Style Wise Garments Production Status Report</td>
                     </tr>
                    <tr style="border:none;">
                            <td colspan="29" align="center" style="border:none; font-size:16px; font-weight:bold">
                                Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
                            </td>
                      </tr>
                      <tr style="border:none;">
                            <td colspan="29" align="center" style="border:none;font-size:12px; font-weight:bold">
                            	<? echo "Date: ". str_replace("'","",$txt_date_from) ;?>
                            </td>
                      </tr>
                </table>
             <br />	
     <!--        <table cellspacing="0" cellpadding="0" border="1" rules="all"  width="2990" class="rpt_table">-->
             <table id="table_header_2" class="rpt_table" width="3060" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                	<tr >
                        <th width="40" rowspan="2">SL</th>
                        <th width="100" rowspan="2">Buyer</th>
                        <th width="140" rowspan="2">Style</th>
                        <th width="60" rowspan="2">Job No</th>
                        <th width="50" rowspan="2">Year</th>
                        <th width="100" rowspan="2">Job Qty.</th>
                        <th width="100" rowspan="2">Ship Date</th>
                        <th width="120" rowspan="2">Shiping Status</th>
                        <th width="100" rowspan="2">Fin. Fab. Issued</th>
                        <th width="100" rowspan="2">Possible Cut Qty.</th>
                        <th width="240" colspan="3">Cutting</th>
                        <th width="240" colspan="3">Embl. Issue	</th>
                        <th width="240" colspan="3">Embl. Receive</th>
                        <th width="240" colspan="3">Sewing Input	</th>
                        <th width="240" colspan="3">Sewing Output</th>
                        <th width="240" colspan="3">Iron</th>
                        <th width="160" colspan="2">Sewing Reject</th>
                        <th width="240" colspan="3">Finish	</th>
                        <th  colspan="3">Ex- Factory</th>
                        
                    </tr>
                    <tr>
                        <th width="80" >Today</th>
                        <th width="80" >Total </th>
                        <th width="80" >WIP Bal. </th>
                        <th width="80" >Today</th>
                        <th width="80" >Total</th>
                        <th width="80" >Issue Bal.</th>
                        <th width="80" >Today </th>
                        <th width="80" >Total</th>
                        <th width="80" >WIP Bal</th>
                        <th width="80" > Today </th>
                        <th width="80" >Total </th>
                        <th width="80" >WIP Bal.</th>
                        <th width="80" >Today </th>
                        <th width="80" >Total </th>
                        <th width="80" >WIP Bal.</th>
                        <th width="80" >Today </th>
                        <th width="80" >Today </th>
                        <th width="80" >WIP Bal.</th>
                        <th width="80" >Today </th>
                        <th width="80" >Total</th>
                        <th width="80" >Today </th>
                        <th width="80" >Total</th>
                        <th width="80" >WIP Bal.</th>
                        <th width="80" >Today </th>
                        <th width="80" >Total</th>
                        <th  >Ex-fac. Bal.</th>
                       
                    </tr>
                </thead>
            </table>
            <?
	
		   $production_data_arr=array();		
	       $production_mst_sql= sql_select("SELECT po_break_down_id,
				sum(CASE WHEN production_type ='1' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty,
				sum(CASE WHEN production_type ='2' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS printing_qnty,
				sum(CASE WHEN production_type ='3' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS printreceived_qnty,
				sum(CASE WHEN production_type ='4' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewingin_qnty,
				sum(CASE WHEN production_type ='5' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewing_out_qnty,
				sum(CASE WHEN production_type ='7' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS iron_qnty,
				sum(CASE WHEN production_type ='5' and production_date=".$txt_date_from." THEN reject_qnty ELSE 0 END) AS reject_today,
				sum(CASE WHEN production_type ='8' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS finish_qnty,
				sum(CASE WHEN production_type ='1' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty_pre,
				sum(CASE WHEN production_type ='2' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS printing_qnty_pre,
				sum(CASE WHEN production_type ='3' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS printreceived_qnty_pre,
				sum(CASE WHEN production_type ='4' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewingin_qnty_pre,
				sum(CASE WHEN production_type ='5' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewing_out_pre,
				sum(CASE WHEN production_type ='7' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS iron_pre,
				sum(CASE WHEN production_type ='8' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS finish_pre,
				sum(CASE WHEN production_type ='5' and production_date<".$txt_date_from." THEN reject_qnty ELSE 0 END) AS reject_pre
				from 
				pro_garments_production_mst 
				where  
				is_deleted=0 and status_active=1
				group by po_break_down_id "); //reject_qnty
		foreach($production_mst_sql as $val)
		     {
		  	    $production_data_arr[$val[csf('po_break_down_id')]]['cutting_qnty']=$val[csf('cutting_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['printing_qnty']=$val[csf('printing_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['printreceived_qnty']=$val[csf('printreceived_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sewingin_qnty']=$val[csf('sewingin_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sewing_out_qnty']=$val[csf('sewing_out_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['iron_qnty']=$val[csf('iron_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['finish_qnty']=$val[csf('finish_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['reject_today']=$val[csf('reject_today')];
				
				$production_data_arr[$val[csf('po_break_down_id')]]['cutting_qnty_pre']=$val[csf('cutting_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['printing_qnty_pre']=$val[csf('printing_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['printreceived_qnty_pre']=$val[csf('printreceived_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sewingin_qnty_pre']=$val[csf('sewingin_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sewing_out_pre']=$val[csf('sewing_out_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['iron_pre']=$val[csf('iron_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['finish_pre']=$val[csf('finish_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['reject_pre']=$val[csf('reject_pre')];	
		     }
			
	       $exfactory_sql=sql_select("SELECT po_break_down_id,
									  sum(CASE WHEN  ex_factory_date=".$txt_date_from." THEN ex_factory_qnty ELSE 0 END) AS exfac_qty,
									  sum(CASE WHEN  ex_factory_date<".$txt_date_from." THEN ex_factory_qnty ELSE 0 END) AS exfac_pre
									  from 
								 	  pro_ex_factory_mst 
									  where  
									  is_deleted=0 and status_active=1
									  group by po_break_down_id ");
									  
			$exfactory_data_arr=array();
		    foreach($exfactory_sql as $value)
		      {
		  	    $exfactory_data_arr[$value[csf('po_break_down_id')]]['ex_qnty']=$value[csf('exfac_qty')];
				$exfactory_data_arr[$value[csf('po_break_down_id')]]['exfac_pre']=$value[csf('exfac_pre')];
				
			  }
		 $sql_fabric_qty=sql_select( "SELECT a.po_breakdown_id,
			  sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =2  AND a.entry_form =18  THEN a.quantity ELSE 0 END ) AS fabric_qty,
			  sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_qty,
			  sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_qty
			  FROM order_wise_pro_details a,inv_transaction b
			  WHERE a.trans_id = b.id 
			  and b.status_active=1 and a.entry_form in(18,15) and a.quantity!=0 and  b.is_deleted=0  group by a.po_breakdown_id");
		 $fabric_data_arr=array();
		 foreach($sql_fabric_qty as $inf)
			  {
				 $fabric_data_arr[$inf[csf("po_breakdown_id")]]["fab_iss"]=$inf[csf("fabric_qty")]+$inf[csf("trans_in_qty")]-$inf[csf("trans_out_qty")];
			  }	
			?>
  <!--<div style="max-height:425px; overflow-y:scroll; width:3030px; margin-left:15px" id="scroll_body">
      <table cellspacing="0" border="1" class="rpt_table"   width="2990" rules="all" id="table_body">-->
      
   <div style="width:3075px; max-height:425px; overflow-y:scroll" id="scroll_body">
        <table class="rpt_table" width="3040" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
        <?
		if($db_type==0)
			{
				
			 $sql=sql_select("select b.job_no,b.job_no_prefix_num,b.company_name,b.buyer_name,b.style_ref_no,b.job_quantity,group_concat(a.id) as po_id,sum(a.plan_cut 	) as plan_cut,year(b.insert_date) as year,max(pub_shipment_date) as ship_date,group_concat(a.shiping_status) as status from  wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no $company_name $buyer_name $job_cond_id $style_cond $order_cond  group by  b.job_no,b.job_no_prefix_num,b.company_name order by b.buyer_name,b.job_no_prefix_num");	
			}
		if($db_type==2)
			{
			 $sql=sql_select( "select b.job_no,b.job_no_prefix_num,b.company_name,b.buyer_name,b.style_ref_no,b.job_quantity,listagg(a.id,',') within group (order by a.id) as po_id,sum(a.plan_cut 	) as plan_cut,extract(year from b.insert_date) as year,max(pub_shipment_date) as ship_date,listagg(a.shiping_status,',') within group (order by a.shiping_status) as status from  wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no $company_name $buyer_name $job_cond_id $style_cond $order_cond  group by  b.job_no,b.job_no_prefix_num,b.company_name,b.buyer_name,b.style_ref_no,b.job_quantity,b.insert_date order by b.buyer_name,b.job_no_prefix_num");	
			}
		$fabric_iss=0;
		$grand_cut=$grand_cut_total=$grand_embl_issue=$grand_embl_iss_total=$grand_sew_in=$grand_sew_in_total=$grand_sew_out=$grand_sew_out_total=0;
		$grand_iron=$grand_iron_total=$grand_finish=$grand_finish_total=$grand_reject=$grand_reject_total=$grand_exfactory=$grand_exfa_total=0; 
		$grand_fabric_iss=$grand_embl_rec=$grand_embl_rev_total=$grand_plan_cut=$job_total=0;	
		$tot_rows=count($sql);
		$i=1;	   		
		 foreach($sql as $row)	
	        {
				$status_all=array_unique(explode(',',$row[csf('status')]));
				$all_ship=0;
				$partial_ship=0;
				$full_pend=0;
				$full_ship=$fabric_iss=$exfactory_total=0;
				$cut_today=$embl_issue_today=$embl_rcv_today=$sewing_in_today=$sew_out_today=$iron_today=$finish_today=$reject_today=$exfactory_qty=0;
				$fabric_iss=$cut_total=$embl_issue_total=$embl_rcv_total=$sewing_in_total=$iron_total=$sew_out_total=$finish_total=$reject_total=0;
				
				foreach($status_all as $ship_val)
				{
					if($ship_val==3) $full_ship=$ship_val;
				    else if($ship_val==2) $partial_ship=$ship_val;
					else if($ship_val==1) $full_pend=$ship_val;
					else $all_ship=$ship_val;
				}
				if($full_ship==0)
				  {
					if($partial_ship==0)
						   {
						   if($full_pend==0)
								 {
									 $ship_status=0;
								 }
							 else
								 {
									$ship_status=1;
								 }
						   }
					 else
						   {
							 $ship_status=2; 
						   }
				  }
				  else
				  {
					   if($partial_ship==0)
						   {
							  if($full_pend==0)
								 {
									 $ship_status=3;
								 }
							 else
								 {
									$ship_status=2;
								 }  
						   }
						else
						{  
							$ship_status=2;
						}
				  }
				$po_id_all=explode(',',$row[csf('po_id')]);
				$k=0;
			  	foreach( $po_id_all as $val_a)
					{
						
						$cut_today+=$production_data_arr[$val_a]['cutting_qnty'];
						$embl_issue_today+=$production_data_arr[$val_a]['printing_qnty'];
						$embl_rcv_today+=$production_data_arr[$val_a]['printreceived_qnty'];
						$sewing_in_today+=$production_data_arr[$val_a]['sewingin_qnty'];
						$sew_out_today+=$production_data_arr[$val_a]['sewing_out_qnty'];
						$iron_today+=$production_data_arr[$val_a]['iron_qnty'];
						$finish_today+=$production_data_arr[$val_a]['finish_qnty'];
						$reject_today+=$production_data_arr[$val_a]['reject_today'];
						$exfactory_qty+=$exfactory_data_arr[$val_a]['ex_qnty'];
						$fabric_iss+=$fabric_data_arr[$val_a]['fab_iss'];
						$cut_total+=$production_data_arr[$val_a]['cutting_qnty_pre']+$production_data_arr[$val_a]['cutting_qnty'];
						$embl_issue_total+=$production_data_arr[$val_a]['printing_qnty_pre']+$production_data_arr[$val_a]['printing_qnty'];
						$embl_rcv_total+=$production_data_arr[$val_a]['printreceived_qnty_pre']+$production_data_arr[$val_a]['printreceived_qnty'];
						$sewing_in_total+=$production_data_arr[$val_a]['sewingin_qnty_pre']+$production_data_arr[$val_a]['sewingin_qnty'];
						$iron_total+=$production_data_arr[$val_a]['iron_pre']+$production_data_arr[$val_a]['iron_qnty'];
						$sew_out_total=$production_data_arr[$val_a]['sewing_out_pre']+$production_data_arr[$val_a]['sewing_out_qnty'];
						$finish_total+=$production_data_arr[$val_a]['finish_pre']+$production_data_arr[$val_a]['finish_qnty'];
						$reject_total+=$production_data_arr[$val_a]['reject_today']+$production_data_arr[$val_a]['reject_pre'];
						$exfactory_total+=$exfactory_data_arr[$val_a]['ex_qnty']+$exfactory_data_arr[$val_a]['exfac_pre'];
						$k++;
					}
				if($fabric_iss!=0 || ($cut_today!=0 || $embl_issue_today!=0 || $embl_rcv_today!=0 || $sewing_in_today!=0  || $sew_out_today!=0  || $iron_today!=0 || $exfactory_qty!=0  || $finish_today!=0))
			{	
				        $grand_cut+=$cut_today;
						$grand_cut_total+=$cut_total;
						$grand_embl_issue+=$embl_issue_today;
						$grand_embl_iss_total+=$embl_issue_total;
						$grand_embl_rec+=$embl_rcv_today;
						$grand_embl_rev_total+=$embl_rcv_total;
						$grand_sew_in+=$sewing_in_today;
						$grand_sew_in_total+=$sewing_in_total;
						$grand_sew_out+=$sew_out_today;
						$grand_sew_out_total+=$sew_out_total;
						$grand_iron+=$iron_today;
						$grand_iron_total+=$iron_total;
						$grand_finish+=$finish_today;
						$grand_finish_total+=$finish_total;
						$grand_reject+=$reject_today;
						$grand_reject_total+=$reject_total;
						$grand_exfactory+=$exfactory_qty;
						$grand_exfa_total+=$exfactory_total;
						$grand_fabric_iss+=$fabric_iss;
						$grand_plan_cut+=$row[csf('plan_cut')];
						$job_total+=$row[csf('job_quantity')];
					    $txt_date=str_replace("'","",$txt_date_from);
					    $diff=datediff("d",$txt_date,$row[csf('ship_date')]);
				  
				 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
	      ?>
               <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                    <td width="40"><? echo $i;?></td>
                    <td width="100" align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                    <td width="140" align="center"><p><? echo $row[csf('style_ref_no')];?>&nbsp;</p></td>
                    <td width="60" align="center"><?  echo $row[csf('job_no_prefix_num')];?></td>
                    <td width="50" align="center"><?  echo $row[csf('year')];?></td>
                    <td width="100" align="right"><a href="#report_details" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no_prefix_num')]; ?>', '<? echo $row[csf('year')]; ?>','job_color_size',850)"><?  echo $row[csf('job_quantity')];?></a></td>
                    <td width="100" align="center"><? echo change_date_format($row[csf('ship_date')]);?></td>
                    <td width="120" align="center"><p><a href="#report_details" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no_prefix_num')]; ?>', '<? echo $row[csf('year')]; ?>','shipping_sataus_style',400)"><? echo $shipment_status[$ship_status]; ?></a></p></td>
                    <td width="100" align="right"><?  echo $fabric_iss; ?></td>
                    <td width="100" align="right"><a href="#report_details" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no_prefix_num')]; ?>', '<? echo $row[csf('year')]; ?>','cut_qty_all',850)"><?  echo $row[csf('plan_cut')]; ?></a></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'cut_entry_today_all',850)"><?   echo number_format($cut_today,2); ?><a/></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'cut_entry_total_all',850)"><?   echo number_format($cut_total,2);?><a/></td>
                    <td width="80" align="right"><?   echo number_format(($row[csf('job_quantity')]-$cut_total),2); ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'embl_today_all',850)"><?   echo number_format($embl_issue_today,2);?><a/></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'embl_total_all',850)"><?   echo number_format($embl_issue_total,2); ?><a/></td>
                    <td width="80" align="right"><?   echo number_format(($cut_total-$embl_issue_total),2); ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'embl_receive_today_all',850)"><?   echo number_format($embl_rcv_today,2); ?><a/></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'embl_receive_total_all',850)"><?   echo number_format($embl_rcv_total,2); ?></a/></td>
                    <td width="80" align="right"><?   echo number_format(($embl_issue_total-$embl_rcv_total),2); ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'sewing_input_today_all',850)"><?   echo number_format($sewing_in_today,2); ?><a/></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'sewing_input_total_all',850)"><?   echo number_format($sewing_in_total,2); ?><a/></td>
                    <td width="80" align="right"><?   echo number_format(($cut_total-$sewing_in_total),2); ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'sewing_output_today_all',850)"><?   echo number_format($sew_out_today,2); ?><a/></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'sewing_ooutput_total_all',850)"><?   echo number_format($sew_out_total,2); ?><a/></td>
                    <td width="80" align="right"><?   echo number_format(($sewing_in_total-$sew_out_total),2); ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'iron_today_all',850)"><?   echo number_format($iron_today,2);?><a/></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'iron_total_all',850)"><?   echo number_format($iron_total,2); ?><a/></td>
                    <td width="80" align="right"><?   echo number_format(($sew_out_total-$iron_total),2); ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'reject_today_all',400)"><?   echo number_format($reject_today,2); ?><a/></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'reject_total_all',470)"><?   echo number_format($reject_total,2); ?><a/></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'finish_today_all',850)"><?   echo number_format($finish_today,2); ?><a/></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'finish_total_all',850)"><?   echo number_format($finish_total,2); ?><a/></td>
                    <td width="80" align="right"><?   echo number_format(($sew_out_total-$finish_total),2); ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'exfactory_today_all',850)"><?   echo number_format($exfactory_qty,2); ?><a/></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'exfactory_total_all',850)"><?   echo number_format(($exfactory_total),2); ?><a/></td>
                    <td  align="right" ><?  echo number_format(($row[csf('job_quantity')]-$exfactory_total),2);   ?></td>
         </tr>    
           <?
		   $i++;
		    }
			
	  }
		   ?>
           <tfoot>
                     <tr>
                        <th width="40"><? // echo $i;?></th>
                        <th width="100"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></th>
                        <th width="140"><strong>Grand Total:</strong></td>
                        <th width="60"></td>
                        <th width="50"><? //echo $pro_date_sql_row[csf("po_number")];?></th>
                        <th width="100" align="right" id="value_job_total"><?  echo number_format($job_total,2); ?></th>
                        <th width="100"></th>
                        <th width="120"> </th>
                        <th width="100" id="value_fabric_issue"><? echo number_format($grand_fabric_iss,2); ?></th>
                        <th width="100" align="right" id="value_plan_cut"><? echo number_format($grand_plan_cut,2); ?></th>
                        <th width="80" align="right" id="value_cut_today"><?  echo number_format($grand_cut,2); ?></th>
                        <th width="80" align="right" id="value_cut_total"><?  echo number_format($grand_cut_total,2); ?></th>
                        <th width="80" align="right" id="value_cut_bal"><?  echo number_format(($job_total-$grand_cut_total),2); ?></th>
                        <th width="80" align="right" id="value_embl_iss"><?  echo number_format($grand_embl_issue,2); ?></th>
                        <th width="80" align="right" id="value_embl_iss_total"><?  echo number_format($grand_embl_iss_total,2);?></th>
                        <th width="80" align="right" id="value_embl_iss_bal"><?  echo number_format(($grand_cut_total-$grand_embl_iss_total),2); ?></th>
                        <th width="80" align="right" id="value_embl_rec"><?  echo number_format($grand_embl_rec,2); ?></th>
                        <th width="80" align="right" id="value_embl_rec_total"><?  echo number_format($grand_embl_rev_total,2); ?></th>
                        <th width="80" align="right" id="value_embl_rec_bal"><?  echo number_format(($grand_embl_iss_total-$grand_embl_rev_total),2); ?></th>
                        <th width="80" align="right" id="value_sew_in"><?  echo number_format($grand_sew_in,2); ?></th>
                        <th width="80" align="right" id="value_sew_in_to"><?  echo number_format($grand_sew_in_total,2); ?></th>
                        <th width="80" align="right" id="value_sew_in_bal"><?  echo number_format(($grand_cut_total-$grand_sew_in_total),2); ?></th>
                        <th width="80" align="right" id="value_sew_out"><?  echo number_format($grand_sew_out,2); ?></th>
                        <th width="80" align="right" id="value_sew_out_total"><?  echo number_format($grand_sew_out_total,2); ?></th>
                        <th width="80" align="right" id="value_sew_out_bal"><?  echo number_format(($grand_sew_in_total-$grand_sew_out_total),2); ?></th>
                        <th width="80" align="right" id="value_iron"><?  echo number_format($grand_iron,2); ?></th>
                        <th width="80" align="right" id="value_iron_to"><?  echo number_format($grand_iron_total,2); ?></th>
                        <th width="80" align="right" id="value_iron_bal"><?  echo number_format($grand_sew_out_total-$grand_iron_total,2); ?></th>
                        <th width="80" align="right" id="value_reject"><?  echo number_format($grand_reject,2); ?></th>
                        <th width="80" align="right" id="value_reject_to"><?  echo number_format($grand_reject_total,2); ?></th>
                        <th width="80" align="right" id="value_finish"><?  echo number_format($grand_finish,2); ?></th>
                        <th width="80" align="right" id="value_finish_to"><?  echo number_format($grand_finish_total,2); ?></th>
                        <th width="80" align="right" id="value_finish_bal"><?  echo number_format(($grand_sew_out_total-$grand_finish_total),2);?></th>
                        <th width="80" align="right" id="value_exfactory"><?  echo number_format($grand_exfactory,2); ?></th>
                        <th width="80" align="right" id="value_exfactory_to"><?  echo number_format($grand_exfa_total,2); ?></th>
                        <th  align="right" id="value_exfac_bal"><?  echo number_format(($job_total-$grand_exfa_total),2); ?></th>
                 </tr> 
            </tfoot>
    </table>
    
  	</div>
 
  </fieldset>
 <?	
  }
  
 if(str_replace("'","",$cbo_search_by)==3)
  {
 ?>
  <fieldset style="width:3800px;">
        	   <table width="1880"  cellspacing="0"   >
                    <tr class="form_caption" style="border:none;">
                            <td colspan="29" align="center" style="border:none;font-size:14px; font-weight:bold" >Daily Style Wise Garments Production Status Report</td>
                     </tr>
                    <tr style="border:none;">
                            <td colspan="29" align="center" style="border:none; font-size:16px; font-weight:bold">
                                Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
                            </td>
                      </tr>
                      <tr style="border:none;">
                            <td colspan="29" align="center" style="border:none;font-size:12px; font-weight:bold">
                            	<? echo "Date: ". str_replace("'","",$txt_date_from) ;?>
                            </td>
                      </tr>
                </table>
             <br />	
             
             <table class="rpt_table" width="3785" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                	<tr >
                        <th width="40" rowspan="2">SL</th>
                        <th width="100" rowspan="2">Buyer</th>
                        <th width="140" rowspan="2">Style</th>
                        <th width="60" rowspan="2">Job No</th>
                        <th width="50" rowspan="2">Year</th>
                        <th width="100" rowspan="2">Job Qty.</th>
                        <th width="100" rowspan="2">Ship Date</th>
                        <th width="120" rowspan="2">Shiping Status</th>
                        <th width="100" rowspan="2">Fin. Fab. Issued</th>
                        <th width="100" rowspan="2">Possible Cut Qty.</th>
                        <th width="240" colspan="3">Cutting</th>
                        <th width="240" colspan="3">EMBL Issue	</th>
                        <th width="240" colspan="3">EMBL Receive</th>
                        <th width="240" colspan="3">Sewing Input	</th>
                        <th width="240" colspan="3">Sewing Output</th>
                        <th width="240" colspan="3">Iron</th>
                        <th width="200" colspan="2">Re-Iron Qty</th>
                        <th width="160" colspan="2">Sewing Reject</th>
                        <th width="240" colspan="3">Finish	</th>
                        <th  colspan="3">Ex- Factory</th>
                        
                    </tr>
                    <tr>
                        <th width="100" >Today.</th>
                        <th width="100" >Total </th>
                        <th width="100">WIP Bal. </th>
                        <th width="100" >Today</th>
                        <th width="100" >Total</th>
                        <th width="100" >Issue Bal.</th>
                        <th width="100" >Today </th>
                        <th width="100" >Total</th>
                        <th width="100" >WIP Bal</th>
                        <th width="100" > Today </th>
                        <th width="100" >Total </th>
                        <th width="100" >WIP Bal.</th>
                        <th width="100" >Today </th>
                        <th width="100" >Total </th>
                        <th width="100" >WIP Bal.</th>
                        <th width="100" >Today </th>
                        <th width="100" >Total </th>
                        <th width="100" >WIP Bal.</th>
                        <th width="100" >Today </th>
                        <th width="100" >Total </th>
                        <th width="100" >Today </th>
                        <th width="100" >Total</th>
                        <th width="100" >Today </th>
                        <th width="100" >Total</th>
                        <th width="100" >WIP Bal.</th>
                        <th width="100" >Today </th>
                        <th width="100" >Total</th>
                        <th  >Ex-fac. Bal.</th>
                    </tr>
                </thead>
            </table>
            <?
	
		
		   $production_data_arr=array();  		
	       $production_mst_sql= sql_select("SELECT po_break_down_id,
				sum(CASE WHEN production_type ='1' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty,
				sum(CASE WHEN production_type ='2' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS printing_qnty,
				sum(CASE WHEN production_type ='3' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS printreceived_qnty,
				sum(CASE WHEN production_type ='4' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewingin_qnty,
				sum(CASE WHEN production_type ='5' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewing_out_qnty,
				sum(CASE WHEN production_type ='7' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS iron_qnty,
				sum(CASE WHEN production_type ='7' and production_date=".$txt_date_from." THEN re_production_qty ELSE 0 END) AS re_iron_qnty,
				sum(CASE WHEN production_type ='5' and production_date=".$txt_date_from." THEN reject_qnty ELSE 0 END) AS reject_today,
				sum(CASE WHEN production_type ='8' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS finish_qnty,
				sum(CASE WHEN production_type ='1' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty_pre,
				sum(CASE WHEN production_type ='2' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS printing_qnty_pre,
				sum(CASE WHEN production_type ='3' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS printreceived_qnty_pre,
				sum(CASE WHEN production_type ='4' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewingin_qnty_pre,
				sum(CASE WHEN production_type ='5' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewing_out_pre,
				sum(CASE WHEN production_type ='7' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS iron_pre,
				sum(CASE WHEN production_type ='7' and production_date<".$txt_date_from." THEN re_production_qty ELSE 0 END) AS re_iron_pre,
				sum(CASE WHEN production_type ='8' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS finish_pre,
				sum(CASE WHEN production_type ='5' and production_date<".$txt_date_from." THEN reject_qnty ELSE 0 END) AS reject_pre
				from 
				pro_garments_production_mst 
				where  
				is_deleted=0 and status_active=1
				group by po_break_down_id "); //reject_qnty
				
		foreach($production_mst_sql as $val)
		     {
		  	    $production_data_arr[$val[csf('po_break_down_id')]]['cutting_qnty']=$val[csf('cutting_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['printing_qnty']=$val[csf('printing_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['printreceived_qnty']=$val[csf('printreceived_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sewingin_qnty']=$val[csf('sewingin_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sewing_out_qnty']=$val[csf('sewing_out_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['iron_qnty']=$val[csf('iron_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['finish_qnty']=$val[csf('finish_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['reject_today']=$val[csf('reject_today')];
				$production_data_arr[$val[csf('po_break_down_id')]]['cutting_qnty_pre']=$val[csf('cutting_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['printing_qnty_pre']=$val[csf('printing_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['printreceived_qnty_pre']=$val[csf('printreceived_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sewingin_qnty_pre']=$val[csf('sewingin_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sewing_out_pre']=$val[csf('sewing_out_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['iron_pre']=$val[csf('iron_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['finish_pre']=$val[csf('finish_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['reject_pre']=$val[csf('reject_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['re_iron_qnty']=$val[csf('re_iron_qnty')];	
				$production_data_arr[$val[csf('po_break_down_id')]]['re_iron_pre']=$val[csf('re_iron_pre')];		
		     }
			//print_r($production_data_arr[2961]);die;
	       $exfactory_sql=sql_select("SELECT po_break_down_id,
									  sum(CASE WHEN  ex_factory_date=".$txt_date_from." THEN ex_factory_qnty ELSE 0 END) AS exfac_qty,
									  sum(CASE WHEN  ex_factory_date<".$txt_date_from." THEN ex_factory_qnty ELSE 0 END) AS exfac_pre
									  from 
								 	  pro_ex_factory_mst 
									  where  
									  is_deleted=0 and status_active=1
									  group by po_break_down_id ");
			$exfactory_data_arr=array();
		    foreach($exfactory_sql as $value)
		      {
		  	    $exfactory_data_arr[$value[csf('po_break_down_id')]]['ex_qnty']=$value[csf('exfac_qty')];
				$exfactory_data_arr[$value[csf('po_break_down_id')]]['exfac_pre']=$value[csf('exfac_pre')];
				
			  }
		 $sql_fabric_qty=sql_select( "SELECT a.po_breakdown_id,
			  sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =2  AND a.entry_form =18  THEN a.quantity ELSE 0 END ) AS fabric_qty,
			  sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_qty,
			  sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_qty
			  FROM order_wise_pro_details a,inv_transaction b
			  WHERE a.trans_id = b.id 
			  and b.status_active=1 and a.entry_form in(18,15) and a.quantity!=0 and  b.is_deleted=0  group by a.po_breakdown_id");
			$fabric_data_arr=array();
			foreach($sql_fabric_qty as $inf)
			  {
				 $fabric_data_arr[$inf[csf("po_breakdown_id")]]["fab_iss"]=$inf[csf("fabric_qty")]+$inf[csf("trans_in_qty")]-$inf[csf("trans_out_qty")];
			  }	

			?>
      <div style="width:3800px; max-height:425px; overflow-y:scroll"   id="scroll_body">
           <table class="rpt_table" width="3770" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">

        <?
		if($db_type==0)
			{
				
			   $sql=sql_select("select b.job_no,b.job_no_prefix_num,b.company_name,b.buyer_name,b.style_ref_no,
			                  sum(a.po_quantity) as job_quantity ,group_concat(a.id) as po_id,sum(a.plan_cut) as plan_cut,
							  year(b.insert_date) as year,max(pub_shipment_date) as ship_date,group_concat(a.shiping_status) as status 
							  from  wo_po_break_down a,wo_po_details_master b
							  where a.job_no_mst=b.job_no  $company_name $buyer_name $job_cond_id $style_cond $order_cond and b.status_active=1 and                               b.is_deleted=0 and a.status_active=1   
							  group by b.company_name,b.job_no_prefix_num,b.job_no,b.insert_date order by b.buyer_name,b.job_no_prefix_num");	
			}
		if($db_type==2)
			{
		
			   $sql=sql_select( "select b.job_no,b.job_no_prefix_num,b.company_name,b.buyer_name,b.style_ref_no,
			                   sum(a.po_quantity) as job_quantity,listagg(a.id,',') within group (order by a.id) as po_id,
							   sum(a.plan_cut) as plan_cut,extract(year from b.insert_date) as year,
							   max(pub_shipment_date) as ship_date,listagg(a.shiping_status,',') within group (order by a.shiping_status) as status
							   from  wo_po_break_down a,wo_po_details_master b
							   where a.job_no_mst=b.job_no $company_name $buyer_name $job_cond_id $style_cond $order_cond
							   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 
							   group by b.company_name,b.job_no,b.job_no_prefix_num,b.buyer_name,b.style_ref_no,b.job_quantity,
							    b.insert_date order by b.buyer_name,b.job_no_prefix_num");	
			}
			//print_r($sql);
		$fabric_iss=0;
		$grand_cut=$grand_cut_total=$grand_embl_issue=$grand_embl_iss_total=$grand_sew_in=$grand_sew_in_total=$grand_sew_out=$grand_sew_out_total=0;
		$grand_iron=$grand_iron_total=$grand_finish=$grand_finish_total=$grand_reject=$grand_reject_total=$grand_exfactory=$grand_exfa_total=0; 
		$grand_fabric_iss=$grand_embl_rec=$grand_embl_rev_total=$grand_plan_cut=$job_total=0;	
		$tot_rows=count($sql);
		$i=1;	   		
		 foreach($sql as $row)	
	        {
				$all_ship=0;
				$partial_ship=0;
				$full_pend=0;
				$ship_status=0;
				$full_ship=$fabric_iss=$exfactory_total=0;
				$cut_today=$embl_issue_today=$embl_rcv_today=$sewing_in_today=$sew_out_today=$iron_today=$finish_today=$reject_today=$exfactory_qty=0;
				$re_iron_today=$re_iron_total=0;
				$fabric_iss=$cut_total=$embl_issue_total=$embl_rcv_total=$sewing_in_total=$iron_total=$sew_out_total=$finish_total=$reject_total=0;
				
				$po_id_all=explode(',',$row[csf('po_id')]);
				$k=0;
			  	foreach( $po_id_all as $val_a)
					{
						$cut_today+=$production_data_arr[$val_a]['cutting_qnty'];
						$embl_issue_today+=$production_data_arr[$val_a]['printing_qnty'];
						$embl_rcv_today+=$production_data_arr[$val_a]['printreceived_qnty'];
						$sewing_in_today+=$production_data_arr[$val_a]['sewingin_qnty'];
						$sew_out_today+=$production_data_arr[$val_a]['sewing_out_qnty'];
						$iron_today+=$production_data_arr[$val_a]['iron_qnty'];
						$re_iron_today+=$production_data_arr[$val_a]['re_iron_qnty'];
						
						$finish_today+=$production_data_arr[$val_a]['finish_qnty'];
						$reject_today+=$production_data_arr[$val_a]['reject_today'];
						$exfactory_qty+=$exfactory_data_arr[$val_a]['ex_qnty'];
						$fabric_iss+=$fabric_data_arr[$val_a]['fab_iss'];
						$cut_total+=$production_data_arr[$val_a]['cutting_qnty_pre']+$production_data_arr[$val_a]['cutting_qnty'];
						$embl_issue_total+=$production_data_arr[$val_a]['printing_qnty_pre']+$production_data_arr[$val_a]['printing_qnty'];
						$embl_rcv_total+=$production_data_arr[$val_a]['printreceived_qnty_pre']+$production_data_arr[$val_a]['printreceived_qnty'];
						$sewing_in_total+=$production_data_arr[$val_a]['sewingin_qnty_pre']+$production_data_arr[$val_a]['sewingin_qnty'];
						$iron_total+=$production_data_arr[$val_a]['iron_pre']+$production_data_arr[$val_a]['iron_qnty'];
						$re_iron_total+=$production_data_arr[$val_a]['re_iron_qnty']+$production_data_arr[$val_a]['re_iron_pre'];
						
						$sew_out_total+=$production_data_arr[$val_a]['sewing_out_pre']+$production_data_arr[$val_a]['sewing_out_qnty'];
						$finish_total+=$production_data_arr[$val_a]['finish_pre']+$production_data_arr[$val_a]['finish_qnty'];
						$reject_total+=$production_data_arr[$val_a]['reject_today']+$production_data_arr[$val_a]['reject_pre'];
						$exfactory_total+=$exfactory_data_arr[$val_a]['ex_qnty']+$exfactory_data_arr[$val_a]['exfac_pre'];
						$k++;
					}	
					
					
			if($fabric_iss!=0 || ($cut_today!=0 || $embl_issue_today!=0 || $embl_rcv_today!=0 || $sewing_in_today!=0  || $sew_out_today!=0  || $iron_today!=0 || $exfactory_qty!=0  || $finish_today!=0))
			    {
					$status_all=array_unique(explode(',',$row[csf('status')]));
					foreach($status_all as $ship_val)
					   {
						  
							if($ship_val==3) $full_ship=$ship_val;
							 if($ship_val==2) $partial_ship=$ship_val;
							 if($ship_val==1) $full_pend=$ship_val;
							 if($ship_val==0) $all_ship=$ship_val;
					   }
				
				
					if($full_ship==0)
					  {
						if($partial_ship==0)
							   {
							   if($full_pend==0)
									 {
										 $ship_status=0;
									 }
								 else
									 {
										$ship_status=1;
									 }
							   }
						 else
							   {
								 $ship_status=2; 
							   }
					  }
					  else
					  {
						   if($partial_ship==0)
							   {
								  if($full_pend==0)
									 {
										 $ship_status=3;
									 }
								 else
									 {
										$ship_status=2;
									 }  
							   }
							else
							{  
								$ship_status=2;
							}
					  } 
					  
		if($ship_status!=3)
		      	 {
					// echo $ship_status."<br>";
				        $grand_cut+=$cut_today;
						$grand_cut_total+=$cut_total;
						$grand_embl_issue+=$embl_issue_today;
						$grand_embl_iss_total+=$embl_issue_total;
						$grand_embl_rec+=$embl_rcv_today;
						$grand_embl_rev_total+=$embl_rcv_total;
						$grand_sew_in+=$sewing_in_today;
						$grand_sew_in_total+=$sewing_in_total;
						$grand_sew_out+=$sew_out_today;
						$grand_sew_out_total+=$sew_out_total;
						$grand_iron+=$iron_today;
						$grand_iron_total+=$iron_total;
						$grand_finish+=$finish_today;
						$grand_finish_total+=$finish_total;
						$grand_reject+=$reject_today;
						$grand_reject_total+=$reject_total;
						
						$grand_re_iron_today+=$re_iron_today;
						$grand_re_iron_total+=$re_iron_total;
						$grand_exfactory+=$exfactory_qty;
						$grand_exfa_total+=$exfactory_total;
						$grand_fabric_iss+=$fabric_iss;
						$grand_plan_cut+=$row[csf('plan_cut')];
						$job_total+=$row[csf('job_quantity')];
				   $txt_date=str_replace("'","",$txt_date_from);
				   $diff=datediff("d",$txt_date,$row[csf('ship_date')]);
				 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
	      ?>
               <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                    <td width="40"><? echo $i;?></td>
                    <td width="100" align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                    <td width="140" align="center"><p><? echo $row[csf('style_ref_no')];?>&nbsp;</p></td>
                    <td width="60" align="center"><?  echo $row[csf('job_no_prefix_num')];?></td>
                    <td width="50" align="center"><?  echo $row[csf('year')];?></td>
                    <td width="100" align="right"><a href="#report_details" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no_prefix_num')]; ?>', '<? echo $row[csf('year')]; ?>','job_color_style',850)"><?  echo $row[csf('job_quantity')];?></a></td>
                    <td width="100" align="center"><? echo change_date_format($row[csf('ship_date')]);?></td>
                    <td width="120" align="center"><p><a href="#report_details" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no_prefix_num')]; ?>', '<? echo $row[csf('year')]; ?>','shipping_sataus',400)"><? echo $shipment_status[$ship_status]; ?></a></p></td>
                    <td width="100" align="right"><?  echo $fabric_iss; ?></td>
                    <td width="100" align="right"><a href="#report_details" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no_prefix_num')]; ?>', '<? echo $row[csf('year')]; ?>','cut_qty',850)"><?  echo $row[csf('plan_cut')]; ?></a></td>
                    <td width="100" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'cut_entry_today',1050)"><?   echo number_format($cut_today,0); ?><a/></td>
                    <td width="100" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'cut_entry_total',1050)"><?   echo number_format($cut_total,0);?><a/></td>
                    <td width="100" align="right"><?   echo number_format(($row[csf('job_quantity')]-$cut_total),0); ?></td>
                    <td width="100" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'embl_today',1000)"><?   echo number_format($embl_issue_today,0);?><a/></td>
                    <td width="100" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'embl_total',1000)"><?   echo number_format($embl_issue_total,0); ?><a/></td>
                    <td width="100" align="right"><?   echo number_format(($cut_total-$embl_issue_total),0); ?></td>
                    <td width="100" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'embl_receive_today',900)"><?   echo number_format($embl_rcv_today,0); ?><a/></td>
                    <td width="100" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'embl_receive_total',900)"><?   echo number_format($embl_rcv_total,0); ?><a/></td>
                    <td width="100" align="right"><?   echo number_format(($embl_issue_total-$embl_rcv_total),0); ?></td>
                    <td width="100" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'sewing_input_today',900)"><?   echo number_format($sewing_in_today,0); ?><a/></td>
                    <td width="100" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'sewing_input_total',900)"><?   echo number_format($sewing_in_total,0); ?></td>
                    <td width="100" align="right"><?   echo number_format(($cut_total-$sewing_in_total),0); ?></td>
                    <td width="100" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'sewing_output_today',900)"><?   echo number_format($sew_out_today,0); ?><a/></td>
                    <td width="100" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'sewing_ooutput_total',900)"><?   echo number_format($sew_out_total,0); ?><a/></td>
                    <td width="100" align="right"><?   echo number_format(($sewing_in_total-$sew_out_total),0); ?></td>
                    <td width="100" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'iron_today',900)"><?   echo number_format($iron_today,0);?><a/></td>
                    <td width="100" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'iron_total',900)"><?   echo number_format($iron_total,0); ?><a/></td>
                    <td width="100" align="right"><?   echo number_format(($sew_out_total-$iron_total),0); ?></td>
                     <td width="100" align="right"><?   echo number_format($re_iron_today,0);?></td>
                    <td width="100" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'re_iron_total',850)"><?   echo number_format($re_iron_total,0); ?><a/></td>
                    <td width="100" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'reject_today',850)"><?   echo number_format($reject_today,0); ?><a/></td>
                    <td width="100" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'reject_total',850)"><?   echo number_format($reject_total,0); ?><a/></td>
                    <td width="100" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'finish_today',900)"><?   echo number_format($finish_today,0); ?><a/></td>
                    <td width="100" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'finish_total',900)"><?   echo number_format($finish_total,0); ?><a/></td>
                    <td width="100" align="right"><?   echo number_format(($sew_out_total-$finish_total),0); ?></td>
                    <td width="100" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'exfactory_today',900)"><?   echo number_format($exfactory_qty,0); ?><a/></td>
                    <td width="100" align="right"><a href="##" onclick="openmypage('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('job_no')]; ?>', <? echo $txt_date_from; ?>,'exfactory_total',900)"><?   echo number_format(($exfactory_total),0); ?><a/></td>
                    <td  align="right" ><?  echo number_format(($row[csf('job_quantity')]-$exfactory_total),0);  ?></td>
         </tr>    
           <?
		   $ship_status="";
		   $i++;
		    }
		  }
		}
		   ?>
           </table>
           <table class="rpt_table" width="3770" cellpadding="0" cellspacing="0" border="1" rules="all">
          <!-- <table cellspacing="0" border="1" class="rpt_table"   width="2990" rules="all" id="table_body">-->
                 <tfoot>
                     <tr>
                        <th width="40"><? // echo $i;?></th>
                        <th width="100"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></th>
                        <th width="140"><strong>Grand Total:</strong></td>
                        <th width="60"></td>
                        <th width="50"><? //echo $pro_date_sql_row[csf("po_number")];?></th>
                        <th width="100" align="right" id="value_job_total"><?  echo number_format($job_total,0); ?></th>
                        <th width="100"></th>
                        <th width="120"> </th>
                        <th width="100" id="value_fabric_issue"><? echo number_format($grand_fabric_iss,0); ?></th>
                        <th width="100" align="right" id="value_plan_cut"><? echo number_format($grand_plan_cut,0); ?></th>
                        <th width="100" align="right" id="value_cut_today"><?  echo number_format($grand_cut,0); ?></th>
                        <th width="100" align="right" id="value_cut_total"><?  echo number_format($grand_cut_total,0); ?></th>
                        <th width="100" align="right" id="value_cut_bal"><?  echo number_format(($job_total-$grand_cut_total),0); ?></th>
                        <th width="100" align="right" id="value_embl_iss"><?  echo number_format($grand_embl_issue,0); ?></th>
                        <th width="100" align="right" id="value_embl_iss_total"><?  echo number_format($grand_embl_iss_total,0);?></th>
                        <th width="100" align="right" id="value_embl_iss_bal"><?  echo number_format(($grand_cut_total-$grand_embl_iss_total),0); ?></th>
                        <th width="100" align="right" id="value_embl_rec"><?  echo number_format($grand_embl_rec,0); ?></th>
                        <th width="100" align="right" id="value_embl_rec_total"><?  echo number_format($grand_embl_rev_total,0); ?></th>
                        <th width="100" align="right" id="value_embl_rec_bal"><?  echo number_format(($grand_embl_iss_total-$grand_embl_rev_total),0); ?></th>
                        <th width="100" align="right" id="value_sew_in"><?  echo number_format($grand_sew_in,0); ?></th>
                        <th width="100" align="right" id="value_sew_in_to"><?  echo number_format($grand_sew_in_total,0); ?></th>
                        <th width="100" align="right" id="value_sew_in_bal"><?  echo number_format(($grand_cut_total-$grand_sew_in_total),0); ?></th>
                        <th width="100" align="right" id="value_sew_out"><?  echo number_format($grand_sew_out,0); ?></th>
                        <th width="100" align="right" id="value_sew_out_total"><?  echo number_format($grand_sew_out_total,0); ?></th>
                        <th width="100" align="right" id="value_sew_out_bal"><?  echo number_format(($grand_sew_in_total-$grand_sew_out_total),0); ?></th>
                        <th width="100" align="right" id="value_iron"><?  echo number_format($grand_iron,0); ?></th>
                        <th width="100" align="right" id="value_iron_to"><?  echo number_format($grand_iron_total,0); ?></th>
                        <th width="100" align="right" id="value_iron_bal"><?  echo number_format($grand_sew_out_total-$grand_iron_total,0); ?></th>
                        <th width="100" align="right" id="value_re_iron"><?  echo number_format($grand_re_iron_today,0); ?></th>
                        <th width="100" align="right" id="value_re_iron_to"><?  echo number_format($grand_re_iron_total,0); ?></th>
                        <th width="100" align="right" id="value_reject"><?  echo number_format($grand_reject,0); ?></th>
                        <th width="100" align="right" id="value_reject_to"><?  echo number_format($grand_reject_total,0); ?></th>
                        <th width="100" align="right" id="value_finish"><?  echo number_format($grand_finish,0); ?></th>
                        <th width="100" align="right" id="value_finish_to"><?  echo number_format($grand_finish_total,0); ?></th>
                        <th width="100" align="right" id="value_finish_bal"><?  echo number_format(($grand_sew_out_total-$grand_finish_total),0);?></th>
                        <th width="100" align="right" id="value_exfactory"><?  echo number_format($grand_exfactory,0); ?></th>
                        <th width="100" align="right" id="value_exfactory_to"><?  echo number_format($grand_exfa_total,0); ?></th>
                        <th  align="right" id="value_exfac_bal"><?  echo number_format(($job_total-$grand_exfa_total),0); ?></th>
                       
                 </tr> 
            </tfoot>
		</table>
  	</div>
 
  </fieldset>
 <?	
  }
  //for order wise search
    
  if(str_replace("'","",$cbo_search_by)==2)
  {
 ?>
  <fieldset style="width:3800px;">
        	   <table width="1880"  cellspacing="0"   >
                    <tr class="form_caption" style="border:none;">
                            <td colspan="29" align="center" style="border:none;font-size:14px; font-weight:bold" >Daily Order Wise Garments Production Status Report</td>
                     </tr>
                    <tr style="border:none;">
                            <td colspan="29" align="center" style="border:none; font-size:16px; font-weight:bold">
                                Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
                            </td>
                      </tr>
                      <tr style="border:none;">
                            <td colspan="29" align="center" style="border:none;font-size:12px; font-weight:bold">
                            	<? echo "Date: ". str_replace("'","",$txt_date_from) ;?>
                            </td>
                      </tr>
                </table>
             <br />	
           
              <table class="rpt_table" width="3772" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                	<tr >
                        <th width="40"  rowspan="2">SL</th>
                        <th width="100" rowspan="2">Buyer</th>
                        <th width="150" rowspan="2">Style</th>
                        <th width="60" rowspan="2">Job No</th>
                        <th width="50" rowspan="2">Year</th>
                        <th width="200" rowspan="2">Order No</th>
                        <th width="100" rowspan="2">Order Qty.</th>
                        <th width="100" rowspan="2">Ship Date</th>
                        <th width="120" rowspan="2">Shiping Status</th>
                        <th width="100" rowspan="2">Fin. Fab. Issued</th>
                        <th width="100" rowspan="2">Possible Cut Qty.</th>
                        <th width="300" colspan="3">Cutting</th>
                        <th width="300" colspan="3">EMBL Issue	</th>
                        <th width="300" colspan="3">EMBL Receive</th>
                        <th width="300" colspan="3">Sewing Input	</th>
                        <th width="300" colspan="3">Sewing Output</th>
                        <th width="300" colspan="3">Iron</th>
                        <th width="200" colspan="2">Sewing Reject</th>
                        <th width="300" colspan="3">Finish	</th>
                        <th  colspan="3">Ex- Factory</th>
                    </tr>
                    <tr>
                        <th width="100" >Today.</th>

                        <th width="100">Total </th>
                        <th width="100" >WIP Bal. </th>
                        <th width="100" >Today</th>
                        <th width="100" >Total</th>
                        <th width="100" >Issue Bal.</th>
                        <th width="100" >Today </th>
                        <th width="100" >Total</th>
                        <th width="100" >WIP Bal</th>
                        <th width="100" > Today </th>
                        <th width="100" >Total </th>
                        <th width="100" >WIP Bal.</th>
                        <th width="100" >Today </th>
                        <th width="100" >Total </th>
                        <th width="100" >WIP Bal.</th>
                        <th width="100" >Today </th>
                        <th width="100" >Today </th>
                        <th width="100" >WIP Bal.</th>
                        <th width="100" >Today </th>
                        <th width="100" >Total</th>
                        <th width="100" >Today </th>
                        <th width="100" >Total</th>
                        <th width="100" >WIP Bal.</th>
                        <th width="100" >Today </th>
                        <th width="100" >Total</th>
                        <th  >Ex-fac. Bal.</th>
                    </tr>
                </thead>
            </table>
            <?
		   $production_data_arr=array();		
	       $production_mst_sql= sql_select("SELECT po_break_down_id,
				sum(CASE WHEN production_type ='1' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty,
				sum(CASE WHEN production_type ='2' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS printing_qnty,
				sum(CASE WHEN production_type ='3' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS printreceived_qnty,
				sum(CASE WHEN production_type ='4' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewingin_qnty,
				sum(CASE WHEN production_type ='5' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewing_out_qnty,
				sum(CASE WHEN production_type ='7' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS iron_qnty,
				sum(CASE WHEN production_type ='8' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS finish_qnty,
				sum(CASE WHEN production_type ='5' and production_date=".$txt_date_from." THEN reject_qnty ELSE 0 END) AS reject_today,
				sum(CASE WHEN production_type ='1' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty_pre,
				sum(CASE WHEN production_type ='2' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS printing_qnty_pre,
				sum(CASE WHEN production_type ='3' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS printreceived_qnty_pre,
				sum(CASE WHEN production_type ='4' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewingin_qnty_pre,
				sum(CASE WHEN production_type ='5' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewing_out_pre,
				sum(CASE WHEN production_type ='7' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS iron_pre,
				sum(CASE WHEN production_type ='8' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS finish_pre,
				sum(CASE WHEN production_type ='5' and production_date<".$txt_date_from." THEN reject_qnty ELSE 0 END) AS reject_pre
				from 
				pro_garments_production_mst 
				where  
				is_deleted=0 and status_active=1 
				group by po_break_down_id "); //reject_qnty
		foreach($production_mst_sql as $val)
		     {
		  	    $production_data_arr[$val[csf('po_break_down_id')]]['cutting_qnty']=$val[csf('cutting_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['printing_qnty']=$val[csf('printing_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['printreceived_qnty']=$val[csf('printreceived_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sewingin_qnty']=$val[csf('sewingin_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sewing_out_qnty']=$val[csf('sewing_out_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['iron_qnty']=$val[csf('iron_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['finish_qnty']=$val[csf('finish_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['reject_today']=$val[csf('reject_today')];
				$production_data_arr[$val[csf('po_break_down_id')]]['cutting_qnty_pre']=$val[csf('cutting_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['printing_qnty_pre']=$val[csf('printing_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['printreceived_qnty_pre']=$val[csf('printreceived_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sewingin_qnty_pre']=$val[csf('sewingin_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sewing_out_pre']=$val[csf('sewing_out_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['iron_pre']=$val[csf('iron_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['finish_pre']=$val[csf('finish_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['reject_pre']=$val[csf('reject_pre')];	
		     }
		  
			
	       $exfactory_sql=sql_select("SELECT po_break_down_id,
									  sum(CASE WHEN  ex_factory_date=".$txt_date_from." THEN ex_factory_qnty ELSE 0 END) AS exfac_qty,
									  sum(CASE WHEN  ex_factory_date<".$txt_date_from." THEN ex_factory_qnty ELSE 0 END) AS exfac_pre
									  from 
								 	  pro_ex_factory_mst 
									  where  
									  is_deleted=0 and status_active=1
									  group by po_break_down_id ");
			$exfactory_data_arr=array();
		    foreach($exfactory_sql as $value)
		      {
		  	    $exfactory_data_arr[$value[csf('po_break_down_id')]]['ex_qnty']=$value[csf('exfac_qty')];
				$exfactory_data_arr[$value[csf('po_break_down_id')]]['exfac_pre']=$value[csf('exfac_pre')];
			  }
		 $sql_fabric_qty=sql_select( "SELECT a.po_breakdown_id,
			  sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =2  AND a.entry_form =18  THEN a.quantity ELSE 0 END ) AS fabric_qty,
			  sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_qty,
			  sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_qty
			  FROM order_wise_pro_details a,inv_transaction b
			  WHERE a.trans_id = b.id 
			  and b.status_active=1 and a.entry_form in(18,15) and a.quantity!=0 and  b.is_deleted=0  group by a.po_breakdown_id");
				$fabric_data_arr=array();
			foreach($sql_fabric_qty as $inf)
			  {
				 $fabric_data_arr[$inf[csf("po_breakdown_id")]]["fab_iss"]=$inf[csf("fabric_qty")]+$inf[csf("trans_in_qty")]-$inf[csf("trans_out_qty")];
			  }	

			?>
 <!-- <div style="max-height:425px; overflow-y:scroll; width:3150px; margin-left:20px" id="scroll_body">
      <table cellspacing="0" border="1" class="rpt_table"   width="3120" rules="all" id="table_body">-->
      
      
      <div style="width:3772px; max-height:425px; overflow-y:scroll"   id="scroll_body">
           <table class="rpt_table" width="3750" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
        <?
		if($db_type==0)
			{
			 $sql=sql_select("select b.company_name,a.id as po_id,a.po_number,b.job_no_prefix_num,b.buyer_name,b.style_ref_no,a.po_quantity,a.plan_cut as plan_cut,year(b.insert_date) as year,pub_shipment_date as ship_date,a.shiping_status as status from  wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no $company_name $buyer_name $job_cond_id $style_cond $order_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1   order by b.buyer_name,b.job_no_prefix_num");	
			}
		if($db_type==2)
			{

			 $sql=sql_select("select b.company_name,a.id as po_id,a.po_number,b.job_no_prefix_num,b.buyer_name,b.style_ref_no,a.po_quantity,a.plan_cut as plan_cut,extract(year from b.insert_date) as year,pub_shipment_date as ship_date,a.shiping_status as status from  wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no $company_name $buyer_name $job_cond_id $style_cond $order_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 order by b.buyer_name,b.job_no_prefix_num");	
			}
		$grand_fabric_iss=$grand_embl_rec=$grand_embl_rev_total=$grand_plan_cut=$job_total=$exfactory_total=0;
		$cut_today=$embl_issue_today=$embl_rcv_today=$sewing_in_today=$sew_out_today=$iron_today=$finish_today=$reject_today=$exfactory_qty=0;
		$fabric_iss=$cut_total=$embl_issue_total=$embl_rcv_total=$sewing_in_total=$iron_total=$sew_out_total=$finish_total=$reject_total=0;
		$grand_cut=$grand_cut_total=$grand_embl_issue=$grand_embl_iss_total=$grand_sew_in=$grand_sew_in_total=$grand_sew_out=$grand_sew_out_total=0;
		$grand_iron=$grand_iron_total=$grand_finish=$grand_finish_total=$grand_reject=$grand_reject_total=$grand_exfactory=$grand_exfa_total=0; 
		 $tot_rows=count($sql);	
		 $i=1;	
		 foreach($sql as $row)	
	        {
				
				$cut_today=$production_data_arr[$row[csf('po_id')]]['cutting_qnty'];
				$embl_issue_today=$production_data_arr[$row[csf('po_id')]]['printing_qnty'];
				$embl_rcv_today=$production_data_arr[$row[csf('po_id')]]['printreceived_qnty'];
				$sewing_in_today=$production_data_arr[$row[csf('po_id')]]['sewingin_qnty'];
				$sew_out_today=$production_data_arr[$row[csf('po_id')]]['sewing_out_qnty'];
				$iron_today=$production_data_arr[$row[csf('po_id')]]['iron_qnty'];
				$finish_today=$production_data_arr[$row[csf('po_id')]]['finish_qnty'];
				$reject_today=$production_data_arr[$row[csf('po_id')]]['reject_today'];
				$exfactory_qty=$exfactory_data_arr[$row[csf('po_id')]]['ex_qnty'];
				$fabric_iss=$fabric_data_arr[$row[csf('po_id')]]['fab_iss'];
				$cut_total=$production_data_arr[$row[csf('po_id')]]['cutting_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['cutting_qnty'];
				$embl_issue_total=$production_data_arr[$row[csf('po_id')]]['printing_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['printing_qnty'];
				$embl_rcv_total=$production_data_arr[$row[csf('po_id')]]['printreceived_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['printreceived_qnty'];
				$sewing_in_total=$production_data_arr[$row[csf('po_id')]]['sewingin_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['sewingin_qnty'];
				$iron_total=$production_data_arr[$row[csf('po_id')]]['iron_pre']+$production_data_arr[$row[csf('po_id')]]['iron_qnty'];
				$sew_out_total+=$production_data_arr[$row[csf('po_id')]]['sewing_out_pre']+$production_data_arr[$row[csf('po_id')]]['sewing_out_qnty'];
				$finish_total=$production_data_arr[$row[csf('po_id')]]['finish_pre']+$production_data_arr[$row[csf('po_id')]]['finish_qnty'];
				$reject_total=$production_data_arr[$row[csf('po_id')]]['reject_today']+$production_data_arr[$row[csf('po_id')]]['reject_pre'];
				$exfactory_total=$exfactory_data_arr[$row[csf('po_id')]]['ex_qnty']+$exfactory_data_arr[$row[csf('po_id')]]['exfac_pre'];
		
			if($fabric_iss!=0 || ($cut_today!=0 || $embl_issue_today!=0 || $embl_rcv_today!=0 || $sewing_in_today!=0  || $sew_out_today!=0  || $iron_today!=0 || $exfactory_qty!=0  || $finish_today!=0))
			{		
				$grand_cut+=$cut_today;
				$grand_cut_total+=$cut_total;
				$grand_embl_issue+=$embl_issue_today;
				$grand_embl_iss_total+=$embl_issue_total;
				$grand_embl_rec+=$embl_rcv_today;
				$grand_embl_rev_total+=$embl_rcv_total;
				$grand_sew_in+=$sewing_in_today;
				$grand_sew_in_total+=$sewing_in_total;
				$grand_sew_out+=$sew_out_today;
				$grand_sew_out_total+=$sew_out_total;
				$grand_iron+=$iron_today;
				$grand_iron_total+=$iron_total;
				$grand_finish+=$finish_today;
				$grand_finish_total+=$finish_total;
				$grand_reject+=$reject_today;
				$grand_reject_total+=$reject_total;
				$grand_exfactory+=$exfactory_qty;
				$grand_exfa_total+=$exfactory_total;
				$grand_fabric_iss+=$fabric_iss;
				$grand_plan_cut+=$row[csf('plan_cut')];
				$job_total+=$row[csf('po_quantity')];
				$txt_date=str_replace("'","",$txt_date_from);
				$diff=datediff("d",$txt_date,$row[csf('ship_date')]);
				  
			 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
	      ?>
           <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                <td width="40"><? echo $i;?></td>
                <td width="100" align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                <td width="150" align="center"><p><? echo $row[csf('style_ref_no')];?>&nbsp;</p></td>
                <td width="60" align="center"><?  echo $row[csf('job_no_prefix_num')];?></td>
                <td width="50" align="center"><?  echo $row[csf('year')];?></td>
                <td width="200" align="center"><p><?  echo $row[csf('po_number')];?></p></td>
                <td width="100" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'order_quantity',1050,250)"><?  echo $row[csf('po_quantity')];?><a/></td>
                <td width="100" align="center"><? echo change_date_format($row[csf('ship_date')]);?></td>
                <td width="120" align="center"><p><? echo $shipment_status[$row[csf('status')]]; ?></p></td>
                <td width="100" align="right"><?  echo $fabric_iss; ?></td>
                <td width="100" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'plan_cut_quantity',800,250)"><?  echo $row[csf('plan_cut')]; ?><a/></td>
                <td width="100" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'today_order_cutting',850,250)"><?   echo number_format($cut_today,0); ?><a/></td>
                <td width="100" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'today_order_cutting',850,250)"><?   echo number_format($cut_total,0);?><a/></td>
                <td width="100" align="right"><?   echo number_format(($row[csf('po_quantity')]-$cut_total),0); ?></td>
                <td width="100" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'embl_issue_order',850,250)"><?   echo number_format($embl_issue_today,0);?><a/></td>
                <td width="100" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'embl_issue_order',850,250)"><?   echo number_format($embl_issue_total,0);; ?><a/></td>
                <td width="100" align="right"><?   echo number_format(($cut_total-$embl_issue_total),0); ?></td>
                <td width="100" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'embl_receive_order',850,250)"><?   echo number_format($embl_rcv_today,0); ?><a/></td>
                <td width="100" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'embl_receive_order',850,250)"><?   echo number_format($embl_rcv_total,0); ?><a/></td>
                <td width="100" align="right"><?   echo number_format(($embl_issue_total-$embl_rcv_total),2); ?></td>
                <td width="100" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'sewing_input_order',850,250)"><?   echo number_format($sewing_in_today,0); ?><a/></td>
                <td width="100" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'sewing_input_order',850,250)"><?   echo number_format($sewing_in_total,0); ?><a/></td>
                <td width="100" align="right"><?   echo number_format(($cut_total-$sewing_in_total),2); ?></td>
                <td width="100" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'sewing_output_order',850,250)"><?   echo number_format($sew_out_today,0); ?><a/></td>
                <td width="100" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'sewing_output_order',850,250)"><?   echo number_format($sew_out_total,0); ?><a/></td>
                <td width="100" align="right"><?   echo number_format(($sewing_in_total-$sew_out_total),0); ?></td>
                <td width="100" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'iron__entry_order',850,300)"><?   echo number_format($iron_today,0);?><a/></td>
                <td width="100" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'iron__entry_order',850,300)"><?   echo number_format($iron_total,0); ?><a/></td>
                <td width="100" align="right"><?   echo number_format(($sew_out_total-$iron_total),0); ?></td>
                <td width="100" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'reject_qty_order',700,250)"><?   echo number_format($reject_today,0); ?><a/></td>
                <td width="100" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'reject_qty_order',700,250)"><?   echo number_format($reject_total,0); ?><a/></td>
                <td width="100" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'finish_qty_order',850,300)"><?   echo number_format($finish_today,0); ?><a/></td>
                <td width="100" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'finish_qty_order',850,300)"><?   echo number_format($finish_total,2); ?><a/></td>
                <td width="100" align="right"><?   echo number_format(($sew_out_total-$finish_total),0); ?></td>
                <td width="100" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'exfactrory__entry_order',850,250)"><?   echo number_format($exfactory_qty,0); ?><a/></td>
                <td width="100" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'exfactrory__entry_order',850,250)"><?   echo number_format(($exfactory_total),0); ?><a/></td>
                <td  align="right" ><?  echo number_format(($row[csf('po_quantity')]-$exfactory_total),0);   ?></td>
     </tr>    
           <?
		   $i++;
		    }
		}
		   ?>
 </table>     
    <table class="rpt_table" width="3750" cellpadding="0" cellspacing="0" border="1" rules="all">
       <tfoot>
             <tr>
                <th width="40"><? // echo $i;?></th>
                <th width="100"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></th>
                <th width="150"><strong>Grand Total:</strong></td>
                <th width="60"></td>
                <th width="50"><? //echo $pro_date_sql_row[csf("po_number")];?></th>
                <th width="160"><? //echo $pro_date_sql_row[csf("po_number")];?></th>
                <th width="100" align="right" id="value_job_total"><?  echo number_format($job_total,0); ?></th>
                <th width="100"></th>
                <th width="120"></th>
                <th width="100" id="value_fabric_issue"><? echo number_format($grand_fabric_iss,0); ?></th>
                <th width="100" align="right" id="value_plan_cut"><? echo number_format($grand_plan_cut,0); ?></th>
                <th width="100" align="right" id="value_cut_today"><?  echo number_format($grand_cut,0); ?></th>
                <th width="100" align="right" id="value_cut_total"><?  echo number_format($grand_cut_total,0); ?></th>
                <th width="100" align="right" id="value_cut_bal"><?  echo number_format(($job_total-$grand_cut_total),0); ?></th>
                <th width="100" align="right" id="value_embl_iss"><?  echo number_format($grand_embl_issue,0); ?></th>
                <th width="100" align="right" id="value_embl_iss_total"><?  echo number_format($grand_embl_iss_total,0);?></th>
                <th width="100" align="right" id="value_embl_iss_bal"><?  echo number_format(($grand_cut_total-$grand_embl_iss_total),0); ?></th>
                <th width="100" align="right" id="value_embl_rec"><?  echo number_format($grand_embl_rec,0); ?></th>
                <th width="100" align="right" id="value_embl_rec_total"><?  echo number_format($grand_embl_rev_total,0); ?></th>
                <th width="100" align="right" id="value_embl_rec_bal"><?  echo number_format(($grand_embl_iss_total-$grand_embl_rev_total),0); ?></th>
                <th width="100" align="right" id="value_sew_in"><?  echo number_format($grand_sew_in,0); ?></th>
                <th width="100" align="right" id="value_sew_in_to"><?  echo number_format($grand_sew_in_total,0); ?></th>
                <th width="100" align="right" id="value_sew_in_bal"><?  echo number_format(($grand_cut_total-$grand_sew_in_total),0); ?></th>
                <th width="100" align="right" id="value_sew_out"><?  echo number_format($grand_sew_out,0); ?></th>
                <th width="100" align="right" id="value_sew_out_total"><?  echo number_format($grand_sew_out_total,0); ?></th>
                <th width="100" align="right" id="value_sew_out_bal"><?  echo number_format(($grand_sew_in_total-$grand_sew_out_total),0); ?></th>
                <th width="100" align="right" id="value_iron"><?  echo number_format($grand_iron,0); ?></th>
                <th width="100" align="right" id="value_iron_to"><?  echo number_format($grand_iron_total,0); ?></th>
                <th width="100" align="right" id="value_iron_bal"><?  echo number_format($grand_sew_out_total-$grand_iron_total,0); ?></th>
                <th width="100" align="right" id="value_reject"><?  echo number_format($grand_reject,0); ?></th>
                <th width="100" align="right" id="value_reject_to"><?  echo number_format($grand_reject_total,0); ?></th>
                <th width="100" align="right" id="value_finish"><?  echo number_format($grand_finish,0); ?></th>
                <th width="100" align="right" id="value_finish_to"><?  echo number_format($grand_finish_total,0); ?></th>
                <th width="100" align="right" id="value_finish_bal"><?  echo number_format(($grand_sew_out_total-$grand_finish_total),0);?></th>
                <th width="100" align="right" id="value_exfactory"><?  echo number_format($grand_exfactory,0); ?></th>
                <th width="100" align="right" id="value_exfactory_to"><?  echo number_format($grand_exfa_total,0); ?></th>
                <th width="100" align="right" id="value_exfac_bal"><?  echo number_format(($job_total-$grand_exfa_total),0); ?></th>
          </tr> 
      </tfoot>
  </table>        
 
</div>
 
  </fieldset>
 <?	
 }
 if(str_replace("'","",$cbo_search_by)==4)
  {
 ?>
  <fieldset style="width:3300px;">
        	   <table width="1880"  cellspacing="0"   >
                    <tr class="form_caption" style="border:none;">
                            <td colspan="29" align="center" style="border:none;font-size:14px; font-weight:bold" >Daily Order Wise Garments Production Status Report</td>
                     </tr>
                    <tr style="border:none;">
                            <td colspan="29" align="center" style="border:none; font-size:16px; font-weight:bold">
                                Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
                            </td>
                      </tr>
                      <tr style="border:none;">
                            <td colspan="29" align="center" style="border:none;font-size:12px; font-weight:bold">
                            	<? echo "Date: ". str_replace("'","",$txt_date_from) ;?>
                            </td>
                      </tr>
                </table>
             <br />	
             <table cellspacing="0" cellpadding="0" border="1" rules="all"  width="3260" class="rpt_table">
                <thead>
                	<tr >
                        <th width="40" rowspan="2">SL</th>
                        <th width="100" rowspan="2">Buyer</th>
                        <th width="140" rowspan="2">Style</th>
                        <th width="60" rowspan="2">Job No</th>
                        <th width="50" rowspan="2">Year</th>
                        <th width="200" rowspan="2">Order No</th>
                        <th width="100" rowspan="2">Order Qty.</th>
                        <th width="100" rowspan="2">Ship Date</th>
                        <th width="120" rowspan="2">Shiping Status</th>
                        <th width="100" rowspan="2">Fin. Fab. Issued</th>
                        <th width="100" rowspan="2">Possible Cut Qty.</th>
                        <th width="240" colspan="3">Cutting</th>
                        <th width="240" colspan="3">EMBL Issue	</th>
                        <th width="240" colspan="3">EMBL Receive</th>
                        <th width="240" colspan="3">Sewing Input	</th>
                        <th width="240" colspan="3">Sewing Output</th>
                        <th width="240" colspan="3">Iron</th>
                        <th width="160" colspan="2">Sewing Reject</th>
                        <th width="240" colspan="3">Finish	</th>
                        <th  colspan="3">Ex- Factory</th>
                        
                    </tr>
                    <tr>
                        <th width="80">Today.</th>
                        <th width="80" >Total </th>
                        <th width="80" >WIP Bal. </th>
                        <th width="80" >Today</th>
                        <th width="80" >Total</th>
                        <th width="80" >Issue Bal.</th>
                        <th width="80" >Today </th>
                        <th width="80" >Total</th>
                        <th width="80" >WIP Bal</th>
                        <th width="80" > Today </th>
                        <th width="80" >Total </th>
                        <th width="80" >WIP Bal.</th>
                        <th width="80" >Today </th>
                        <th width="80" >Total </th>
                        <th width="80" >WIP Bal.</th>
                        <th width="80" >Today </th>
                        <th width="80" >Today </th>
                        <th width="80" >Iron Bal.</th>
                        <th width="80" >Today </th>
                        <th width="80" >Total</th>
                        <th width="80" >Today </th>
                        <th width="80" >Total</th>
                        <th width="80" >WIP Bal.</th>
                        <th width="80" >Today </th>
                        <th width="80" >Total</th>
                        <th  >Ex-fac. Bal.</th>
                       
                    </tr>
                </thead>
            </table>
            <?
		   $production_data_arr=array();		
	       $production_mst_sql= sql_select("SELECT po_break_down_id,
				sum(CASE WHEN production_type ='1' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty,
				sum(CASE WHEN production_type ='2' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS printing_qnty,
				sum(CASE WHEN production_type ='3' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS printreceived_qnty,
				sum(CASE WHEN production_type ='4' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewingin_qnty,
				sum(CASE WHEN production_type ='5' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewing_out_qnty,
				sum(CASE WHEN production_type ='7' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS iron_qnty,
				sum(CASE WHEN production_type ='8' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS finish_qnty,
				sum(CASE WHEN production_type ='5' and production_date=".$txt_date_from." THEN reject_qnty ELSE 0 END) AS reject_today,
				sum(CASE WHEN production_type ='1' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty_pre,
				sum(CASE WHEN production_type ='2' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS printing_qnty_pre,
				sum(CASE WHEN production_type ='3' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS printreceived_qnty_pre,
				sum(CASE WHEN production_type ='4' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewingin_qnty_pre,
				sum(CASE WHEN production_type ='5' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS sewing_out_pre,
				sum(CASE WHEN production_type ='7' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS iron_pre,
				sum(CASE WHEN production_type ='8' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS finish_pre,
				sum(CASE WHEN production_type ='5' and production_date<".$txt_date_from." THEN reject_qnty ELSE 0 END) AS reject_pre
				from 
				pro_garments_production_mst 
				where  
				is_deleted=0 and status_active=1 
				group by po_break_down_id "); //reject_qnty
		foreach($production_mst_sql as $val)
		     {
		  	    $production_data_arr[$val[csf('po_break_down_id')]]['cutting_qnty']=$val[csf('cutting_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['printing_qnty']=$val[csf('printing_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['printreceived_qnty']=$val[csf('printreceived_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sewingin_qnty']=$val[csf('sewingin_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sewing_out_qnty']=$val[csf('sewing_out_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['iron_qnty']=$val[csf('iron_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['finish_qnty']=$val[csf('finish_qnty')];
				$production_data_arr[$val[csf('po_break_down_id')]]['reject_today']=$val[csf('reject_today')];
				$production_data_arr[$val[csf('po_break_down_id')]]['cutting_qnty_pre']=$val[csf('cutting_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['printing_qnty_pre']=$val[csf('printing_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['printreceived_qnty_pre']=$val[csf('printreceived_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sewingin_qnty_pre']=$val[csf('sewingin_qnty_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['sewing_out_pre']=$val[csf('sewing_out_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['iron_pre']=$val[csf('iron_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['finish_pre']=$val[csf('finish_pre')];
				$production_data_arr[$val[csf('po_break_down_id')]]['reject_pre']=$val[csf('reject_pre')];	
		     }
	       $exfactory_sql=sql_select("SELECT po_break_down_id,
									  sum(CASE WHEN  ex_factory_date=".$txt_date_from." THEN ex_factory_qnty ELSE 0 END) AS exfac_qty,
									  sum(CASE WHEN  ex_factory_date<".$txt_date_from." THEN ex_factory_qnty ELSE 0 END) AS exfac_pre
									  from 
								 	  pro_ex_factory_mst 
									  where  
									  is_deleted=0 and status_active=1
									  group by po_break_down_id ");
			$exfactory_data_arr=array();
		    foreach($exfactory_sql as $value)
		      {
		  	    $exfactory_data_arr[$value[csf('po_break_down_id')]]['ex_qnty']=$value[csf('exfac_qty')];
				$exfactory_data_arr[$value[csf('po_break_down_id')]]['exfac_pre']=$value[csf('exfac_pre')];
			  }
		 $sql_fabric_qty=sql_select( "SELECT a.po_breakdown_id,
			  sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =2  AND a.entry_form =18  THEN a.quantity ELSE 0 END ) AS fabric_qty,
			  sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_qty,
			  sum(CASE WHEN b.transaction_date <= ".$txt_date_from." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_qty
			  FROM order_wise_pro_details a,inv_transaction b
			  WHERE a.trans_id = b.id 
			  and b.status_active=1 and a.entry_form in(18,15) and a.quantity!=0 and  b.is_deleted=0  group by a.po_breakdown_id");
		 $fabric_data_arr=array();
		 foreach($sql_fabric_qty as $inf)
			  {
				 $fabric_data_arr[$inf[csf("po_breakdown_id")]]["fab_iss"]=$inf[csf("fabric_qty")]+$inf[csf("trans_in_qty")]-$inf[csf("trans_out_qty")];
			  }	
			?>
 <!-- <div style="max-height:425px; overflow-y:scroll; width:3150px; margin-left:20px" id="scroll_body">
      <table cellspacing="0" border="1" class="rpt_table"   width="3120" rules="all" id="table_body">-->
      
      <div style="width:3270px; max-height:425px; overflow-y:scroll"   id="scroll_body">
           <table class="rpt_table" width="3240" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
        <?
		if($db_type==0)
			{
			 $sql=sql_select("select a.id as po_id,a.po_number,b.company_name,b.job_no_prefix_num,b.buyer_name,b.style_ref_no,a.po_quantity,a.plan_cut as plan_cut,year(b.insert_date) as year,pub_shipment_date as ship_date,a.shiping_status as status from  wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no and  a.shiping_status!=3 $company_name $buyer_name $job_cond_id $style_cond $order_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1  order by b.buyer_name,b.job_no_prefix_num");	
			}
		if($db_type==2)
			{
	
			 $sql=sql_select("select a.id as po_id,a.po_number,b.company_name,b.job_no_prefix_num,b.buyer_name,b.style_ref_no,a.po_quantity,a.plan_cut as plan_cut,extract(year from b.insert_date) as year,pub_shipment_date as ship_date,a.shiping_status as status from  wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no and  a.shiping_status!=3 $company_name $buyer_name $job_cond_id $style_cond $order_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 order by b.buyer_name,b.job_no_prefix_num");	
			}
			
			
		$grand_fabric_iss=$grand_embl_rec=$grand_embl_rev_total=$grand_plan_cut=$job_total=$exfactory_total=0;
		$cut_today=$embl_issue_today=$embl_rcv_today=$sewing_in_today=$sew_out_today=$iron_today=$finish_today=$reject_today=$exfactory_qty=0;
		$fabric_iss=$cut_total=$embl_issue_total=$embl_rcv_total=$sewing_in_total=$iron_total=$sew_out_total=$finish_total=$reject_total=0;
		$grand_cut=$grand_cut_total=$grand_embl_issue=$grand_embl_iss_total=$grand_sew_in=$grand_sew_in_total=$grand_sew_out=$grand_sew_out_total=0;
		$grand_iron=$grand_iron_total=$grand_finish=$grand_finish_total=$grand_reject=$grand_reject_total=$grand_exfactory=$grand_exfa_total=0; 
		 $tot_rows=count($sql);
		 $i=1;		
		 foreach($sql as $row)	
	        {
						$cut_today=$production_data_arr[$row[csf('po_id')]]['cutting_qnty'];
						$embl_issue_today=$production_data_arr[$row[csf('po_id')]]['printing_qnty'];
						$embl_rcv_today=$production_data_arr[$row[csf('po_id')]]['printreceived_qnty'];
						$sewing_in_today=$production_data_arr[$row[csf('po_id')]]['sewingin_qnty'];
						$sew_out_today=$production_data_arr[$row[csf('po_id')]]['sewing_out_qnty'];
						$iron_today=$production_data_arr[$row[csf('po_id')]]['iron_qnty'];
						$finish_today=$production_data_arr[$row[csf('po_id')]]['finish_qnty'];
						$reject_today=$production_data_arr[$row[csf('po_id')]]['reject_today'];
						$exfactory_qty=$exfactory_data_arr[$row[csf('po_id')]]['ex_qnty'];
						$fabric_iss=$fabric_data_arr[$row[csf('po_id')]]['fab_iss'];
						
						$cut_total=$production_data_arr[$row[csf('po_id')]]['cutting_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['cutting_qnty'];
						$embl_issue_total=$production_data_arr[$row[csf('po_id')]]['printing_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['printing_qnty'];
						$embl_rcv_total=$production_data_arr[$row[csf('po_id')]]['printreceived_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['printreceived_qnty'];
						$sewing_in_total=$production_data_arr[$row[csf('po_id')]]['sewingin_qnty_pre']+$production_data_arr[$row[csf('po_id')]]['sewingin_qnty'];
						$iron_total=$production_data_arr[$row[csf('po_id')]]['iron_pre']+$production_data_arr[$row[csf('po_id')]]['iron_qnty'];
						$sew_out_total=$production_data_arr[$row[csf('po_id')]]['sewing_out_pre']+$production_data_arr[$row[csf('po_id')]]['sewing_out_qnty'];
						$finish_total=$production_data_arr[$row[csf('po_id')]]['finish_pre']+$production_data_arr[$row[csf('po_id')]]['finish_qnty'];
						$reject_total=$production_data_arr[$row[csf('po_id')]]['reject_today']+$production_data_arr[$row[csf('po_id')]]['reject_pre'];
						$exfactory_total=$exfactory_data_arr[$row[csf('po_id')]]['ex_qnty']+$exfactory_data_arr[$row[csf('po_id')]]['exfac_pre'];
				if($fabric_iss!=0 || ($cut_today!=0 || $embl_issue_today!=0 || $embl_rcv_today!=0 || $sewing_in_today!=0  || $sew_out_today!=0  || $iron_today!=0 || $exfactory_qty!=0  || $finish_today!=0))
				{	
				        $grand_cut+=$cut_today;
						$grand_cut_total+=$cut_total;
						$grand_embl_issue+=$embl_issue_today;
						$grand_embl_iss_total+=$embl_issue_total;
						$grand_embl_rec+=$embl_rcv_today;
						$grand_embl_rev_total+=$embl_rcv_total;
						$grand_sew_in+=$sewing_in_today;
						$grand_sew_in_total+=$sewing_in_total;
						$grand_sew_out+=$sew_out_today;
						$grand_sew_out_total+=$sew_out_total;
						$grand_iron+=$iron_today;
						$grand_iron_total+=$iron_total;
						$grand_finish+=$finish_today;
						$grand_finish_total+=$finish_total;
						$grand_reject+=$reject_today;
						$grand_reject_total+=$reject_total;
						$grand_exfactory+=$exfactory_qty;
						$grand_exfa_total+=$exfactory_total;
						$grand_fabric_iss+=$fabric_iss;
						$grand_plan_cut+=$row[csf('plan_cut')];
						$job_total+=$row[csf('po_quantity')];
				        $txt_date=str_replace("'","",$txt_date_from);
				        $diff=datediff("d",$txt_date,$row[csf('ship_date')]);
				  
				 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
	      ?>
               <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                    <td width="40"><? echo $i;?></td>
                    <td width="100" align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                    <td width="140" align="center"><p><? echo $row[csf('style_ref_no')];?>&nbsp;</p></td>
                    <td width="60" align="center"><?  echo $row[csf('job_no_prefix_num')];?></td>
                    <td width="50" align="center"><?  echo $row[csf('year')];?></td>
                    <td width="200" align="center"><p><?  echo $row[csf('po_number')];?></p></td>
                    <td width="100" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'order_quantity',800,250)"><?  echo $row[csf('po_quantity')];?><a/></td>
                    <td width="100" align="center"><? echo change_date_format($row[csf('ship_date')]);?></td>
                    <td width="120" align="center"><p><? echo $shipment_status[$row[csf('status')]]; ?></p></td>
                    <td width="100" align="right"><?  echo $fabric_iss; ?></td>
                    <td width="100" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'plan_cut_quantity',800,250)"><?  echo $row[csf('plan_cut')]; ?><a/></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'today_order_cutting',850,250)"><?   echo number_format($cut_today,2); ?><a/></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'today_order_cutting',850,250)"><?   echo number_format($cut_total,2);?><a/></td>
                    <td width="80" align="right"><?   echo number_format(($row[csf('po_quantity')]-$cut_total),2); ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'embl_issue_order',850,250)"><?   echo number_format($embl_issue_today,2);?><a/></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'embl_issue_order',850,250)"><?   echo number_format($embl_issue_total,2); ?><a/></td>
                    <td width="80" align="right"><?   echo number_format(($cut_total-$embl_issue_total),2); ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'embl_receive_order',850,250)"><?   echo number_format($embl_rcv_today,2); ?><a/></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'embl_receive_order',850,250)"><?   echo number_format($embl_rcv_total,2); ?><a/></td>
                    <td width="80" align="right"><?   echo number_format(($embl_issue_total-$embl_rcv_total),2); ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'sewing_input_order',850,250)"><?   echo number_format($sewing_in_today,2); ?><a/></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'sewing_input_order',850,250)"><?   echo number_format($sewing_in_total,2); ?><a/></td>
                    <td width="80" align="right"><?   echo number_format(($cut_total-$sewing_in_total),2); ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'sewing_output_order',850,250)"><?   echo number_format($sew_out_today,2); ?><a/></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'sewing_output_order',850,250)"><?   echo number_format($sew_out_total,2); ?><a/></td>
                    <td width="80" align="right"><?   echo number_format(($sewing_in_total-$sew_out_total),2); ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'iron__entry_order',850,300)"><?   echo number_format($iron_today,2);?><a/></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'iron__entry_order',850,300)"><?   echo number_format($iron_total,2); ?><a/></td>
                    <td width="80" align="right"><?   echo number_format(($sew_out_total-$iron_total),2); ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'reject_qty_order',700,250)"><?   echo number_format($reject_today,2); ?><a/></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'reject_qty_order',700,250)"><?   echo number_format($reject_total,2); ?><a/></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'finish_qty_order',850,300)"><?   echo number_format($finish_today,2); ?><a/></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'finish_qty_order',850,300)"><?   echo number_format($finish_total,2); ?><a/></td>
                    <td width="80" align="right"><?   echo number_format(($sew_out_total-$finish_total),2); ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,1,'exfactrory__entry_order',850,250)"><?   echo number_format($exfactory_qty,2); ?><a/></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf('company_name')]; ?>' ,'<? echo  $row[csf('po_id')]; ?>','<? echo  $row[csf('po_number')]; ?>', <? echo $txt_date_from; ?>,2,'exfactrory__entry_order',850,250)"><?   echo number_format(($exfactory_total),2); ?><a/></td>
                    <td  align="right" ><?  echo number_format(($row[csf('po_quantity')]-$exfactory_total),2);   ?></td>
         </tr>    
           <?
		   $i++;
		    }
		}
		   ?>
         <tfoot>
                     <tr>
                        <th width="40"><? // echo $i;?></th>
                        <th width="100"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></th>
                        <th width="140"><strong>Grand Total:</strong></td>
                        <th width="60"></td>
                        <th width="50"><? //echo $pro_date_sql_row[csf("po_number")];?></th>
                        <th width="200"><? //echo $pro_date_sql_row[csf("po_number")];?></th>
                        <th width="100" align="right" id="value_job_total"><?  echo number_format($job_total,2); ?></th>
                        <th width="100"></th>
                        <th width="120"> </th>
                        <th width="100" id="value_fabric_issue"><? echo number_format($grand_fabric_iss,2); ?></th>
                        <th width="100" align="right" id="value_plan_cut"><? echo number_format($grand_plan_cut,2); ?></th>
                        
                        <th width="80" align="right" id="value_cut_today"><?  echo number_format($grand_cut,2); ?></th>
                        <th width="80" align="right" id="value_cut_total"><?  echo number_format($grand_cut_total,2); ?></th>
                        <th width="80" align="right" id="value_cut_bal"><?  echo number_format(($job_total-$grand_cut_total),2); ?></th>
                        <th width="80" align="right" id="value_embl_iss"><?  echo number_format($grand_embl_issue,2); ?></th>
                        <th width="80" align="right" id="value_embl_iss_total"><?  echo number_format($grand_embl_iss_total,2);?></th>
                        <th width="80" align="right" id="value_embl_iss_bal"><?  echo number_format(($grand_cut_total-$grand_embl_iss_total),2); ?></th>
                        <th width="80" align="right" id="value_embl_rec"><?  echo number_format($grand_embl_rec,2); ?></th>
                        <th width="80" align="right" id="value_embl_rec_total"><?  echo number_format($grand_embl_rev_total,2); ?></th>
                        <th width="80" align="right" id="value_embl_rec_bal"><?  echo number_format(($grand_embl_iss_total-$grand_embl_rev_total),2); ?></th>
                        <th width="80" align="right" id="value_sew_in"><?  echo number_format($grand_sew_in,2); ?></th>
                        <th width="80" align="right" id="value_sew_in_to"><?  echo number_format($grand_sew_in_total,2); ?></th>
                        <th width="80" align="right" id="value_sew_in_bal"><?  echo number_format(($grand_cut_total-$grand_sew_in_total),2); ?></th>
                        <th width="80" align="right" id="value_sew_out"><?  echo number_format($grand_sew_out,2); ?></th>
                        <th width="80" align="right" id="value_sew_out_total"><?  echo number_format($grand_sew_out_total,2); ?></th>
                        <th width="80" align="right" id="value_sew_out_bal"><?  echo number_format(($grand_sew_in_total-$grand_sew_out_total),2); ?></th>
                        <th width="80" align="right" id="value_iron"><?  echo number_format($grand_iron,2); ?></th>
                        <th width="80" align="right" id="value_iron_to"><?  echo number_format($grand_iron_total,2); ?></th>
                        <th width="80" align="right" id="value_iron_bal"><?  echo number_format($grand_sew_out_total-$grand_iron_total,2); ?></th>
                        <th width="80" align="right" id="value_reject"><?  echo number_format($grand_reject,2); ?></th>
                        <th width="80" align="right" id="value_reject_to"><?  echo number_format($grand_reject_total,2); ?></th>
                        <th width="80" align="right" id="value_finish"><?  echo number_format($grand_finish,2); ?></th>
                        <th width="80" align="right" id="value_finish_to"><?  echo number_format($grand_finish_total,2); ?></th>
                        <th width="80" align="right" id="value_finish_bal"><?  echo number_format(($grand_sew_out_total-$grand_finish_total),2);?></th>
                        <th width="80" align="right" id="value_exfactory"><?  echo number_format($grand_exfactory,2); ?></th>
                        <th width="80" align="right" id="value_exfactory_to"><?  echo number_format($grand_exfa_total,2); ?></th>
                        <th  align="right" id="value_exfac_bal"><?  echo number_format(($job_total-$grand_exfa_total),2); ?></th>
                       
                 </tr> 
            </tfoot>   
       </table>     
         
  	</div>
 
  </fieldset>
 <?	
  }
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
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$tot_rows####$serch_by";
	exit(); 
}
//======================================================all popup for order ===============================================================================

    // job qty for style
if($action=="order_quantity")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	if($db_type==0) $year_cond=" and year(a.insert_date)=$insert_date";
	if($db_type==2) $year_cond=" and  extract(year from a.insert_date)=$insert_date";
	
	$sql="SELECT  d.size_number_id, d.color_number_id, d.order_quantity
		 FROM wo_po_color_size_breakdown d 
		 WHERE
	     d.po_break_down_id=$order_id and
		 d.is_deleted =0
		 AND d.status_active =1
		";
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
    $itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$job_size_array=array();
	$job_size_qnty_array=array();
	$job_color_array=array();
	$job_color_qnty_array=array();
	$job_color_size_qnty_array=array();
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
	$job_size_array[$order_id][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
	$job_size_qnty_array[$order_id][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
	$job_color_array[$order_id][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
	$job_color_qnty_array[$order_id][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
	$job_color_size_qnty_array[$order_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
	}
	$job_color_tot=0;
	 ?> 
     <div id="data_panel" align="center" style="width:100%">
     <fieldset style="width:780px;">  
        <label> <strong>Po Number: <? echo $order_number; ?><strong><label/>
        <table  align="center" border="1" rules="all" class="rpt_table" >
            <thead>
                <tr>
                    <th width="200">Color</th>
                    <th width="80">Color Total</th>
                    <?
					foreach($job_size_array[$order_id] as $key=>$value)
                    {
						if($value !="")
						{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
						}
					}
					?>
                </tr>
            </thead>
            <?
			$i=1;
			foreach($job_color_array[$order_id] as $key_c=>$value_c)
            {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			if($value_c != "")
		 	{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$order_id][$value_c]; $job_color_tot+=$job_color_qnty_array[$order_id][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$order_id] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="60" align="right"><? echo $job_color_size_qnty_array[$order_id][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
			}
			?>
            <tfoot>
             <tr >
             <th>Total</th>
             <th align="right"><? echo  $job_color_tot; ?></th>
             <?
					foreach($job_size_array[$order_id] as $key_s=>$value_s)
                    {
						if($value_s !="")
						{
							?>
							<th width="60" align="right"><? echo $job_size_qnty_array[$order_id][$value_s];?></th>
							<?
						}
					}
					?>
             </tr>
           </tfoot>
         </table>
       </fieldset>
    </div>
              <br />
	 <?
	
}

   // job qty for style
if($action=="plan_cut_quantity")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql="SELECT  d.size_number_id, d.color_number_id, d.plan_cut_qnty
		 FROM wo_po_color_size_breakdown d 
		 WHERE
	     d.po_break_down_id=$order_id and
		 d.is_deleted =0
		 AND d.status_active =1
		";
		//echo $sql;
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
    $itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$job_size_array=array();
	$job_size_qnty_array=array();
	$job_color_array=array();
	$job_color_qnty_array=array();
	$job_color_size_qnty_array=array();
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
	$job_size_array[$order_id][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
	$job_size_qnty_array[$order_id][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
	$job_color_array[$order_id][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
	$job_color_qnty_array[$order_id][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
	$job_color_size_qnty_array[$order_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
	}
	$job_color_tot=0;
	 ?> 
  <div id="data_panel" align="center" style="width:100%">
     <fieldset  style="width:780px">  
        <label> <strong>Po Number: <? echo $order_number; ?><strong><label/>
        <table  align="center" border="1" rules="all" class="rpt_table" >
            <thead>
                <tr>
                    <th width="180">Color</th>
                    <th width="70">Color Total</th>
                    <?
					foreach($job_size_array[$order_id] as $key=>$value)
                    {
						if($value !="")
						{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
						}
					}
					?>
                </tr>
            </thead>
            <?
			$i=1;
			foreach($job_color_array[$order_id] as $key_c=>$value_c)
            {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			if($value_c != "")
		 	{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$order_id][$value_c]; $job_color_tot+=$job_color_qnty_array[$order_id][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$order_id] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="60" align="right"><? echo $job_color_size_qnty_array[$order_id][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
			}
			?>
            <tfoot>
             <tr bgcolor="<? // echo $bgcolor;?>">
             <th>Total</th>
             <th align="right"><? echo  $job_color_tot; ?></th>
             <?
					foreach($job_size_array[$order_id] as $key_s=>$value_s)
                    {
						if($value_s !="")
						{
							?>
							<th width="60" align="right"><? echo $job_size_qnty_array[$order_id][$value_s];?></th>
							<?
						}
					}
					?>
             </tr>
           </tfoot>
        </table>
     </fieldset>
     </div>
              <br />
	 <?
	
}


     // job qty for style
if($action=="cut_qty")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	if($db_type==0) $year_cond=" and year(a.insert_date)=$insert_date";
	if($db_type==2) $year_cond=" and  extract(year from a.insert_date)=$insert_date";
	
	$sql="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.plan_cut_qnty
			FROM wo_po_details_master a
			LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
			AND c.is_deleted =0
			AND c.status_active =1
			LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
			AND c.id = d.po_break_down_id
			AND d.is_deleted =0
			AND d.status_active =1
			WHERE
			a.job_no_prefix_num=$jobnumber_prefix and a.company_name=$company_id  $year_cond and
			a.is_deleted =0 and
			a.status_active =1";
		//echo $sql;die;
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
    $itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
    $po_number=array();
	$job_size_array=array();
	$job_size_qnty_array=array();
	$job_color_array=array();
	$job_color_qnty_array=array();
	$job_color_size_qnty_array=array();
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
		{
			$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
			$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
			$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
		}
	  ?>
       <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px"> 
      <?
	    foreach($po_number as $key_po=>$value_po)
                    {
					$job_color_tot=0;
	 ?> 
  
        <label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
            <thead>
                <tr>
                    <th width="180">Color</th>  <th width="70">Color Total</th>
                    <?
					foreach($job_size_array[$value_po] as $key=>$value)
                    {
						if($value !="")
						{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
						}
					}
					?>
                </tr>
            </thead>
            <?
			$i=1;
			foreach($job_color_array[$value_po] as $key_c=>$value_c)
            {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			if($value_c != "")
            {
			?>
           
             <tr bgcolor="<? echo $bgcolor;?>">
             <td align="center"><? echo  $colorArr[$value_c]; ?></td>
             <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
             <?
					foreach($job_size_array[$value_po] as $key_s=>$value_s)
                    {
						if($value_s !="")
						{
							?>
							<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
							<?
						}
					}
					?>
             </tr>
            <?
			$i++;
			}
			}
			?>
            <tfoot>
             <tr bgcolor="<? // echo $bgcolor;?>">
             <th>Total</th>
             <th align="right"><? echo  $job_color_tot; ?></th>
             <?
					foreach($job_size_array[$value_po] as $key_s=>$value_s)
                    {
						if($value_s !="")
						{
							?>
							<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
							<?
						}
					}
					?>
             </tr>
           </tfoot>
        </table>
	 <?
    }
	?>
    <br />
       
  </div>
     </fieldset>
    <?
		
}




if($action=="today_order_cutting")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select cutting_update,production_entry from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('cutting_update')];  
	  }
	 
	 if($type==1)  $insert_cond="   and  d.production_date='$insert_date'";
     if($type==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	$sql_job=sql_select("select a.buyer_name,a.job_no,a.style_ref_no,b.po_number,shipment_date,b.po_quantity as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and b.id='$order_id'  and a.company_name=$company_id  ");
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="200">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
				 // if($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                        <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
      </fieldset>
       <br />
    <?
	  
	if($entry_break_down==1)
	  {
		  
		  	$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=1 $insert_cond and
			d.po_break_down_id=$order_id  and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        <fieldset  style="widows:400px">
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Cutting Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  
						  
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		$sql="SELECT  sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type=1  and
		    e.is_deleted =0 and
			e.status_active =1   $insert_cond
			group by f.size_number_id,f.color_number_id";
			
		//echo $sql;
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$order_number][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$order_number][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		 $job_color_tot=0;
		 ?> 
        <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">  
			<label> <strong>Po Number: <? echo $order_number; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
						<?
						
						foreach($job_size_array[$order_number] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="60"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$order_number] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$order_number][$value_c]; $job_color_tot+=$job_color_qnty_array[$order_number][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="60" align="right"><? echo $job_color_size_qnty_array[$order_number][$value_c][$value_s];?></td>
						<?
							}
						}
				 ?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
						<?
							}
						}
				?>
				 </tr>
			  </tfoot>
		</table>
				  <br />
     </fieldset>
 </div>
				 
		 <?
		}
}
//today cut qty  $txt_date_from

if($action=="embl_issue_order")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	
	$sql_result = sql_select("select printing_emb_production,production_entry from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('printing_emb_production')];  
	  }
	$sql_job=sql_select("select a.buyer_name,a.job_no,a.style_ref_no,b.po_number,shipment_date,b.po_quantity as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and b.id='$order_id'  and a.company_name=$company_id  ");

	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="200">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                        <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
      </fieldset>
       <br />
    <?
	if($type==1)  $insert_cond="   and  d.production_date='$insert_date'";
	if($type==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=2 $insert_cond and
			d.po_break_down_id=$order_id and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Embl. Issue Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  
						  
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		 $sql="SELECT  sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type=2  and
		    e.is_deleted =0 and
			e.status_active =1   $insert_cond
			group by f.size_number_id,f.color_number_id";
		//echo $sql;
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
			$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$order_number][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array[$order_number][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
			$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
			$job_color_tot=0;
		 ?>  
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px"> 
			<label> <strong>Po Number: <? echo $order_number; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
						<?
						foreach($job_size_array[$order_number] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="60"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$order_number] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$order_number][$value_c]; $job_color_tot+=$job_color_qnty_array[$order_number][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="60" align="right"><? echo $job_color_size_qnty_array[$order_number][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<th width="60" align="right"><? echo $job_size_qnty_array[$order_number][$value_s];?></th>
								<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		 </table>
				  <br />
      </fieldset>
   </div> 
		 <?
		}
}



if($action=="embl_receive_order")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select printing_emb_production,production_entry from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('printing_emb_production')];  
	  }
	
	  $sql_job=sql_select("select a.buyer_name,a.job_no,a.style_ref_no,b.po_number,shipment_date,b.po_quantity as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and b.id='$order_id' and a.company_name=$company_id  ");
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="200">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                        <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
      </fieldset>
       <br />
    <?
	
    if($type==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($type==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=3 $insert_cond and
			d.po_break_down_id=$order_id and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Embl. Receive Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	  }
	else
	 {
		 $sql="SELECT  sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type=3  and
		    e.is_deleted =0 and
			e.status_active =1   $insert_cond
			group by f.size_number_id,f.color_number_id";
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
			$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$order_number][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array[$order_number][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
			$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
			$job_color_tot=0;
		 ?> 
         <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">  
			<label> <strong>Po Number: <? echo $order_number; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
						<?
						foreach($job_size_array[$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$order_number] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$order_number][$value_c]; $job_color_tot+=$job_color_qnty_array[$order_number][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="60" align="right"><? echo $job_color_size_qnty_array[$order_number][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<th width="60" align="right"><? echo $job_size_qnty_array[$order_number][$value_s];?></th>
								<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		 </table>
				  <br />
      </fieldset>            
 </div>
		 <?
		}
}

if($action=="sewing_input_order")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select sewing_production,production_entry from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('sewing_production')];  
	  }

	  $sql_job=sql_select("select a.buyer_name,a.job_no,a.style_ref_no,b.po_number,shipment_date,b.po_quantity as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and b.id='$order_id' and a.company_name=$company_id  ");
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="200">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                        <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
      </fieldset>
       <br />
    <?
	if($type==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($type==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=4 $insert_cond and
			d.po_break_down_id=$order_id and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
        <fieldset style="width:400px">
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Swing Input Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	  }
	else
	 {
		 $sql="SELECT  sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type=4  and
		    e.is_deleted =0 and
			e.status_active =1   $insert_cond
			group by f.size_number_id,f.color_number_id";
		//echo $sql;
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
			$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$order_number][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array[$order_number][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
			$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
			$job_color_tot=0;
		 ?>  
       <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px"> 
			<label> <strong>Po Number: <? echo $order_number; ?><strong><label/>
			<table  align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
						<?
						foreach($job_size_array[$order_number] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="60"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$order_number] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$order_number][$value_c]; $job_color_tot+=$job_color_qnty_array[$order_number][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="60" align="right"><? echo $job_color_size_qnty_array[$order_number][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<th width="60" align="right"><? echo $job_size_qnty_array[$order_number][$value_s];?></th>
								<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		 </table>
				  <br />
      </fieldset>
  </div>
		 <?
		}
}



if($action=="sewing_output_order")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select sewing_production,production_entry from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('sewing_production')];  
	  }
	$sql_job=sql_select("select a.buyer_name,a.job_no,a.style_ref_no,b.po_number,shipment_date,b.po_quantity as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and b.id='$order_id' and a.company_name=$company_id  ");

	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="200">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                        <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
      </fieldset>
       <br />
    <?
	if($type==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($type==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=5 $insert_cond and
			d.po_break_down_id=$order_id and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
        <fieldset style="width:400px">
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Swing Output Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	  }
	else
	 {
		 $sql="SELECT  sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type=5  and
		    e.is_deleted =0 and
			e.status_active =1   $insert_cond
			group by f.size_number_id,f.color_number_id";
		//echo $sql;
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
			$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$order_number][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array[$order_number][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
			$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
			$job_color_tot=0;
		 ?>  
       <div id="data_panel" align="center" style="width:100%">
          <fieldset  style="width:820px"> 
		  <label> <strong>Po Number: <? echo $order_number; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
						<?
						foreach($job_size_array[$order_number] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="60"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$order_number] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$order_number][$value_c]; $job_color_tot+=$job_color_qnty_array[$order_number][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="60" align="right"><? echo $job_color_size_qnty_array[$order_number][$value_c][$value_s];?></td>
						<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$order_number][$value_s];?></th>
						<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		 </table>
				  <br />
     </fieldset>
 </div>
		 <?
		}
}





if($action=="iron__entry_order")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select iron_update,production_entry from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('iron_update')];  
	  }
	$sql_job=sql_select("select a.buyer_name,a.job_no,a.style_ref_no,b.po_number,shipment_date,b.po_quantity as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and b.id='$order_id'  and a.company_name=$company_id  ");
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="200">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                        <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
      </fieldset>
       <br />
    <?
if($type==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($type==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=7 $insert_cond and
			d.po_break_down_id=$order_id and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
        <fieldset style="width:400px">
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Iron Qty Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	  }
	else
	 {
		 $sql="SELECT  sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type=7  and
		    e.is_deleted =0 and
			e.status_active =1   $insert_cond
			group by f.size_number_id,f.color_number_id";
		//echo $sql;
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
			$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$order_number][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array[$order_number][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
			$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
			$job_color_tot=0;
		 ?> 
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">  
			<label> <strong>Po Number: <? echo $order_number; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
						<?
						foreach($job_size_array[$order_number] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="60"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$order_number] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$order_number][$value_c]; $job_color_tot+=$job_color_qnty_array[$order_number][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="60" align="right"><? echo $job_color_size_qnty_array[$order_number][$value_c][$value_s];?></td>
						<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$order_number][$value_s];?></th>
						<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		 </table>
				  <br />
        </fieldset>
 </div>
		 <?
		}
}



if($action=="finish_qty_order")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select finishing_update,production_entry from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('finishing_update')];  
	  }
	
	$sql_job=sql_select("select a.buyer_name,a.job_no,a.style_ref_no,b.po_number,shipment_date,b.po_quantity as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and b.id='$order_id'  and a.company_name=$company_id  ");

	  foreach($sql_result as $val)
		  {
			$entry_break_down=$val[csf('cutting_update')];  
		   }
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="200">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                        <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
      </fieldset>
       <br />
    <?
	    
	if($entry_break_down!=1)
	{
		
		 if($type==1)  $insert_cond="   and  d.production_date='$insert_date'";
		 if($type==2)  $insert_cond="   and  d.production_date<='$insert_date'";
		 $sql="SELECT  sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type=8 and
		    e.is_deleted =0 and
			e.status_active =1   $insert_cond
			group by f.size_number_id,f.color_number_id";
		//echo $sql;
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
			$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$order_number][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array[$order_number][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
			$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
			$job_color_tot=0;
		 ?> 
  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">  
			<label> <strong>Po Number: <? echo $order_number; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
						<?
						foreach($job_size_array[$order_number] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="60"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$order_number] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$order_number][$value_c]; $job_color_tot+=$job_color_qnty_array[$order_number][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="60" align="right"><? echo $job_color_size_qnty_array[$order_number][$value_c][$value_s];?></td>
						<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$order_number][$value_s];?></th>
						<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		 </table>
				  <br />
                  
      </fieldset>
 </div>
		 <?
		}
}



if($action=="exfactrory__entry_order")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select ex_factory,production_entry from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('ex_factory')];  
	  }
	$sql_job=sql_select("select a.buyer_name,a.job_no,a.style_ref_no,b.po_number,shipment_date,b.po_quantity as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and b.id='$order_id'  and a.company_name=$company_id  ");

	  foreach($sql_result as $val)
		  {
			$entry_break_down=$val[csf('cutting_update')];  
		   }
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="200">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                        <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
      </fieldset>
       <br />
    <?
	    
	if($entry_break_down!=1)
	{
		
		 if($type==1)  $insert_cond="   and  d.ex_factory_date='$insert_date'";
		 if($type==2)  $insert_cond="   and  d.ex_factory_date<='$insert_date'";
		 $sql="SELECT  sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM pro_ex_factory_mst d,pro_ex_factory_dtls e,wo_po_color_size_breakdown f
			WHERE 
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
		    e.is_deleted =0 and
			e.status_active =1   $insert_cond
			group by f.size_number_id,f.color_number_id";
		//echo $sql;
		  
		  
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
			$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$order_number][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array[$order_number][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
			$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
			$job_color_tot=0;
		 ?> 
 <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">  
			<label> <strong>Po Number: <? echo $order_number; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
						<?
						foreach($job_size_array[$order_number] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="60"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$order_number] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$order_number][$value_c]; $job_color_tot+=$job_color_qnty_array[$order_number][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="60" align="right"><? echo $job_color_size_qnty_array[$order_number][$value_c][$value_s];?></td>
						<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$order_number][$value_s];?></th>
						<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		 </table>
				  <br />
       </fieldset>
 </div>
		 <?
		}
}

if($action=="reject_qty_order")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

			$sql_job=sql_select("select a.buyer_name,a.job_no,a.style_ref_no,b.po_number,shipment_date,b.po_quantity as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and b.id='$order_id'  and a.company_name=$company_id  ");

	  foreach($sql_result as $val)
		  {
			$entry_break_down=$val[csf('cutting_update')];  
		   }
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:670px">
         <table width="650px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="150">Buyer Name</th>
              <th width="80">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="150">Order No</th>
              <th width="80">Ship Date</th>
              <th width="80">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                        <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
      </fieldset>
       <br />
    <?
			
		 if($type==1)  $insert_cond="   and  d.production_date='$insert_date'";
		 if($type==2)  $insert_cond="   and  d.production_date<='$insert_date'";
		 $sql="SELECT  d.production_date,sum(d.reject_qnty) as reject_qnty
			FROM pro_garments_production_mst d
			WHERE 
			d.po_break_down_id=$order_id  and
			d.production_type=5 and
		    d.is_deleted =0 and
			d.status_active =1   $insert_cond
			group by d.production_date";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        
        <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:400px">
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Production Date</th>
                        <th width="100">Sewing Rejact Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('production_date')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('reject_qnty')]; echo  $row[csf('reject_qnty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
      </fieldset>
 </div>
		<?
	
}


//============================================finish all popup for order=================================================================================
// for shipping status popup all shipment

//========================================================================================================================================================
if($action=="shipping_sataus_style")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

?>
	<fieldset style="width:350px; margin-left:5px">
        <table border="1" class="rpt_table" rules="all" width="350" cellpadding="0" cellspacing="0">
            <thead>
                <th width="40">SL</th>
                <th width="150">order Number</th>
                <th width="150">Shiping status</th>
               
            </thead>
         </table>
         <div style="width:380px; max-height:270px; overflow-y:scroll" id="scroll_body">
             <table border="1" class="rpt_table" rules="all" width="350" cellpadding="0" cellspacing="0">
                <?
				if($db_type==0) $year_cond=" and year(b.insert_date)=$insert_date";
				if($db_type==2) $year_cond=" and  extract(year from b.insert_date)=$insert_date";
                $i=1; $total_qnty=0;
                $sql="select a.po_number,a.shiping_status  as status from  wo_po_break_down a,wo_po_details_master b where  a.job_no_mst=b.job_no and b.job_no_prefix_num=$jobnumber_prefix and b.company_name=$company_id  $year_cond  group by a.po_number,a.shiping_status order by a.po_number";
				//echo $sql;
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if ($i%2==0)  
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";	
                
                    $total_qnty+=$row[csf('qnty')];
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="150" align="center"><p><? echo $row[csf('po_number')]; ?>&nbsp;</td>
                        <td width="150" align="center"><p><? echo $shipment_status[$row[csf('status')]]; ?>&nbsp;</p></td>
                 
                    </tr>
                <?
                $i++;
                }
                ?>
              
            </table>
        </div>	
	</fieldset>   
<?
exit();
}


if($action=="shipping_sataus")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

?>
	<fieldset style="width:350px; margin-left:5px">
        <table border="1" class="rpt_table" rules="all" width="350" cellpadding="0" cellspacing="0">
            <thead>
                <th width="40">SL</th>
                <th width="150">order Number</th>
                <th width="150">Shiping status</th>
               
            </thead>
         </table>
         <div style="width:380px; max-height:270px; overflow-y:scroll" id="scroll_body">
             <table border="1" class="rpt_table" rules="all" width="350" cellpadding="0" cellspacing="0">
                <?
				if($db_type==0) $year_cond=" and year(b.insert_date)=$insert_date";
				if($db_type==2) $year_cond=" and  extract(year from b.insert_date)=$insert_date";
                $i=1; $total_qnty=0;
                $sql="select a.po_number,a.shiping_status  as status from  wo_po_break_down a,wo_po_details_master b where  a.job_no_mst=b.job_no and b.job_no_prefix_num=$jobnumber_prefix and b.company_name=$company_id  $year_cond  group by a.po_number,a.shiping_status order by a.po_number";
				//echo $sql;
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if ($i%2==0)  
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";	
                
                    $total_qnty+=$row[csf('qnty')];
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="150" align="center"><p><? echo $row[csf('po_number')]; ?>&nbsp;</td>
                        <td width="150" align="center"><p><? echo $shipment_status[$row[csf('status')]]; ?>&nbsp;</p></td>
                 
                    </tr>
                <?
                $i++;
                }
                ?>
              
            </table>
        </div>	
	</fieldset>   
<?
exit();
}

// for job qty all  job_qty_all



if($action=="job_color_size")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	if($db_type==0) $year_cond=" and year(a.insert_date)=$insert_date";
	if($db_type==2) $year_cond=" and  extract(year from a.insert_date)=$insert_date";
	
	$sql="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity
		FROM wo_po_details_master a
		LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
		AND c.is_deleted =0
		AND c.status_active =1
		LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
		AND c.id = d.po_break_down_id
		AND d.is_deleted =0
		AND d.status_active =1
		WHERE
		a.job_no_prefix_num=$jobnumber_prefix and a.company_name=$company_id  $year_cond and
		a.is_deleted =0 and
		a.status_active =1
		";
		//echo $sql;die;
	
	
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
    $itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
    $po_number=array();
	$job_size_array=array();
	$job_size_qnty_array=array();
	$job_color_array=array();
	$job_color_qnty_array=array();
	$job_color_size_qnty_array=array();
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
    $po_number[$row[csf('po_number')]]=$row[csf('po_number')];
	$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
	$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
	$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
	$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
	$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
	}
	
	// print_r($job_size_array);die;
	?>

     <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
    
      <?
	    foreach($po_number as $key_po=>$value_po)
            {
			$job_color_tot=0;
			
				
	 ?>  
   
            <label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
            <table width="" align="center" border="1" rules="all" class="rpt_table" >
                 
                <thead>
                    
                
                    <tr>
                        <th width="180">Color</th>
                        <th width="80">Color Total</th>
                        <?
                        
                        foreach($job_size_array[$value_po] as $key=>$value)
                        {
                            if($value !="")
                            {
                        ?>
                        <th width="60"><? echo $itemSizeArr[$value];?></th>
                        <?
                            }
                        }
                        ?>
                    </tr>
                </thead>
                <?
                $i=1;
                foreach($job_color_array[$value_po] as $key_c=>$value_c)
                {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                if($value_c != "")
                {
                ?>
               
                 <tr bgcolor="<? echo $bgcolor;?>">
                 <td><? echo  $colorArr[$value_c]; ?></td>
                 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
                 <?
                        foreach($job_size_array[$value_po] as $key_s=>$value_s)
                        {
                            if($value_s !="")
                            {
                        ?>
                        <td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
                        <?
                            }
                        }
                        ?>
                 </tr>
                <?
                $i++;
                }
                }
                ?>
                <tfoot>
                 <tr bgcolor="<? // echo $bgcolor;?>">
                 <th>Total</th>
                 <th align="right"><? echo  $job_color_tot; ?></th>
                 <?
                        foreach($job_size_array[$value_po] as $key_s=>$value_s)
                        {
                            if($value_s !="")
                            {
                        ?>
                        <th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
                        <?
                            }
                        }
                        ?>
                 </tr>
               </tfoot>
                  </table>
                  <br />
                 
	 <?
        }
		?>
  </fieldset>
 
 <?   
}

     // job qty for style
if($action=="job_color_style")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	if($db_type==0) $year_cond=" and year(a.insert_date)=$insert_date";
	if($db_type==2) $year_cond=" and  extract(year from a.insert_date)=$insert_date";
    $sql="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity
			FROM wo_po_details_master a
			LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
			AND c.is_deleted =0
			AND c.status_active =1
			LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
			AND c.id = d.po_break_down_id
			AND d.is_deleted =0
			AND d.status_active =1
			WHERE
			a.job_no_prefix_num=$jobnumber_prefix and a.company_name=$company_id  $year_cond and
			a.is_deleted =0 and
			a.status_active =1";
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
    $itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
    $po_number=array();
	$job_size_array=array();
	$job_size_qnty_array=array();
	$job_color_array=array();
	$job_color_qnty_array=array();
	$job_color_size_qnty_array=array();
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
    $po_number[$row[csf('po_number')]]=$row[csf('po_number')];
	$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
	$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
	$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
	$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
	$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
	}
	?>
      <div id="data_panel" align="center" style="width:100%">
    <fieldset  style="width:820px">
      <?
	    foreach($po_number as $key_po=>$value_po)
                    {
					$job_color_tot=0;
	 ?>   
        <label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
        <table width="" align="center" border="1" rules="all" class="rpt_table" >
            <thead>
                <tr>
                    <th width="180">Color</th><th width="80">Color Total</th>
                    <?
					foreach($job_size_array[$value_po] as $key=>$value)
                    {
					if($value !="")
						{
							?>
							<th width="60" align="center"><? echo $itemSizeArr[$value];?></th>
							<?
						}
					}
					?>
                </tr>
            </thead>
            <?
			$i=1;
			foreach($job_color_array[$value_po] as $key_c=>$value_c)
            {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			if($value_c != "")
            {
			?>
             <tr bgcolor="<? echo $bgcolor;?>">
             <td align="center"><? echo  $colorArr[$value_c]; ?></td>
             <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
             <?
					foreach($job_size_array[$value_po] as $key_s=>$value_s)
                    {
						if($value_s !="")
						{
							?>
							<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
							<?
						}
					}
					?>
             </tr>
            <?
			$i++;
			}
			}
			?>
            <tfoot>
             <tr bgcolor="<? // echo $bgcolor;?>">
             <th>Total</th>
             <th align="right"><? echo  $job_color_tot; ?></th>
             <?
					foreach($job_size_array[$value_po] as $key_s=>$value_s)
                    {
						if($value_s !="")
						{
							?>
							<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
							<?
						}
					}
					?>
             </tr>
           </tfoot>
        </table>
              <br />
             
	 <?
        }
	?>
    </fieldset>
    </div>
    <?
	
}

     // job qty for style
if($action=="cut_qty_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	if($db_type==0) $year_cond=" and year(a.insert_date)=$insert_date";
	if($db_type==2) $year_cond=" and  extract(year from a.insert_date)=$insert_date";
	
	$sql="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.plan_cut_qnty
		FROM wo_po_details_master a
		LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
		AND c.is_deleted =0
		AND c.status_active =1
		LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
		AND c.id = d.po_break_down_id
		AND d.is_deleted =0
		AND d.status_active =1
		WHERE
		a.job_no_prefix_num=$jobnumber_prefix and a.company_name=$company_id  $year_cond and
		a.is_deleted =0 and
		a.status_active =1";
		//echo $sql;die;
	
	
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
    $itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
    $po_number=array();
	$job_size_array=array();
	$job_size_qnty_array=array();
	$job_color_array=array();
	$job_color_qnty_array=array();
	$job_color_size_qnty_array=array();
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
    $po_number[$row[csf('po_number')]]=$row[csf('po_number')];
	$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
	$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
	$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
	$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
	$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
	}
	
	// print_r($job_size_array);die;
	?>

    
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
      <?
	    foreach($po_number as $key_po=>$value_po)
                    {
					$job_color_tot=0;
	 ?>   
     
        <label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
        <table width="" align="center" border="1" rules="all" class="rpt_table" >
             
            <thead>
                
            
                <tr>
                    <th width="60">Color</th>
                    <th width="60">Color Total</th>
                    <?
					
					foreach($job_size_array[$value_po] as $key=>$value)
                    {
						if($value !="")
						{
					?>
                    <th width="60"><? echo $itemSizeArr[$value];?></th>
                    <?
						}
					}
					?>
                </tr>
            </thead>
            <?
			$i=1;
			foreach($job_color_array[$value_po] as $key_c=>$value_c)
            {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			if($value_c != "")
            {
			?>
           
             <tr bgcolor="<? echo $bgcolor;?>">
             <td><? echo  $colorArr[$value_c]; ?></td>
             <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
             <?
					foreach($job_size_array[$value_po] as $key_s=>$value_s)
                    {
						if($value_s !="")
						{
					?>
                    <td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
                    <?
						}
					}
					?>
             </tr>
            <?
			$i++;
			}
			}
			?>
            <tfoot>
             <tr bgcolor="<? // echo $bgcolor;?>">
             <th>Total</th>
             <th align="right"><? echo  $job_color_tot; ?></th>
             <?
					foreach($job_size_array[$value_po] as $key_s=>$value_s)
                    {
						if($value_s !="")
						{
					?>
                    <th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
                    <?
						}
					}
					?>
             </tr>
           </tfoot>
              </table>
              <br />
             
	 <?
        }
	?>
	  </fieldset>
   </div>
	
	<?	
}

//for  ====================================style with full shipment========================================================================================


// total cut quantity 

if($action=="cut_entry_total_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select cutting_update,production_entry from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('cutting_update')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	
	
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=1 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
       <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Cutting Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  
						  
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
      </fieldset>
		<?
	}
	else
	 {
		
		
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=1 and  d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
			
		//echo $sql;
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		
		// print_r($job_size_array);die;
		?>
		 <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
		
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		 
			<label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				 
				<thead>
					
				
					<tr>
						<th width="180">Color</th>
						<th width="80">Color Total</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="60"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
			   
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
						<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
						<?
							}
						}
						?>
				 </tr>
			   </tfoot>
				  </table>
				  <br />
				 
		 <?
		}
	 ?>
     </fieldset>
 </div>
  
  <?
	}
}
//today cut qty  $txt_date_from

if($action=="cut_entry_today_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select cutting_update,production_entry from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('cutting_update')];  
	  }
	
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	
	
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=1 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
       <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Cutting Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
       </fieldset>
		<?
	}
	else
	 {
		
		
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=1 and  d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
			
		//echo $sql;
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
			$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
			$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
			$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		
		// print_r($job_size_array);die;
		?>
	
		<div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
		
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		 
			<label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				 
				<thead>
					
				
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="60"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
			   
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
						<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
						<?
							}
						}
						?>
				 </tr>
			   </tfoot>
				  </table>
				  <br />
				 
		 <?
		}
		?>
           </fieldset>
 </div>
 <?
	}
}
// fro embleshment total
if($action=="embl_today_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select printing_emb_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('printing_emb_production')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=2 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
       <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Embl. Issue Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  
						  
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
      </fieldset>
		<?
	}
	else
	 {
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=2 and  d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
				{
				   $job_color_tot=0;
		 ?>   
 <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
			<label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="80">Color Total</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="60"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
						<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
						<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		  </table>
	     <br />
		 <?
		}
	 ?>
     </fieldset>
 </div>
  <?
	}
}


if($action=="embl_total_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select printing_emb_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('printing_emb_production')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=2 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
       <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Embl. Issue Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  
						  
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
      </fieldset>
		<?
	}
	else
	 {
		
		
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=2 and  d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
			
		//echo $sql;
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		
		// print_r($job_size_array);die;
		?>
        <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">	
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
  
			<label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="60"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
			   
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
							?>
							<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
							<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
								<?
							}
						}
						?>
				 </tr>
			   </tfoot>
				  </table>
				  <br />
				 
		 <?
		}
	 ?>
      
     </fieldset>
  </div>
  <?
	}

}

if($action=="embl_receive_total_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select printing_emb_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('printing_emb_production')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=3 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Embl. Receive Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  
						  
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
      </fieldset>
		<?
	}
	else
	 {
		
		
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=3 and  d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
			
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		?>
  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		<label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				 
				<thead>
					
				
					<tr>
						<th width="180">Color</th>
						<th width="80">Color Total</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="60"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
			   
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
						<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
						<?
							}
						}
						?>
				 </tr>
			   </tfoot>
				  </table>
				  <br />
				 
		 <?
		}
	   ?>
     </fieldset>
 </div>
  <?
	}
}

if($action=="embl_receive_today_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select printing_emb_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('printing_emb_production')];  
	  }
	  if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=3 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
       <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Embl. Receive Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
       </fieldset>
		<?
	}
	else
	 {
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=3 and  d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
			
		//echo $sql;
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		
	 ?>
    <div  align="center" style="width:100%">
       <fieldset  style="width:820px">
	  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
				{
				   $job_color_tot=0;
		 ?>   
			<label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="60"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
							?>
							<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
							<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		  </table>
	     <br />
		 <?
		}
	   ?>
     </fieldset>
 </div>
  <?
  
	}
}
//fro sewing input qty


if($action=="sewing_input_total_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select sewing_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('sewing_production')];  
	  }
 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and  and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=4 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Sewing Input Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  
						  
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
      </fieldset>
		<?
	}
	else
	 {
		
		
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=4 and  d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
			
		//echo $sql;
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		?>
  <div  align="center" style="width:100%">
       <fieldset  style="width:820px">
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
			<label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="60"><? echo $itemSizeArr[$value];?></th>
								<?
									}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
								<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		  </table>
				  <br />
		 <?
		}
		  ?>
     </fieldset>
 </div>
  <?
	}
}

if($action=="sewing_input_today_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select sewing_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('sewing_production')];  
	  }
	   if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=4 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
       <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Sewing Input Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
      </fieldset>
		<?
	}
	else
	 {
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=4 and  d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		?>
    <div  align="center" style="width:100%">
       <fieldset  style="width:820px">
        
        <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
				{
				   $job_color_tot=0;
		 ?>   
			<label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="60"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
								<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		  </table>
	     <br />
		 <?
		}
	   ?>
     </fieldset>
 </div>
      <?
	}
}

if($action=="sewing_ooutput_total_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select sewing_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('sewing_production')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                       <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=5 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
       <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Sewing Output Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
      </fieldset>
		<?
	}
	else
	 {
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=5 and  d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
		//echo $sql;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		?>
      <div  align="center" style="width:100%">
       <fieldset  style="width:820px">
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
			<label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="60"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
								<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		  </table>
				  <br />
		 <?
		}
	}
}

if($action=="sewing_output_today_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select sewing_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('sewing_production')];  
	  }
	  if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=5 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Sewing Output Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
		<?
	}
	else
	 {
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=5 and  d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		?>
      <div  align="center" style="width:100%">
       <fieldset  style="width:820px">
       <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
				{
				   $job_color_tot=0;
		 ?>   
			<label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="60"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td  align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
								<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		  </table>
	     <br />
		 <?
		}
		  ?>
     </fieldset>
 </div>
  <?
	}
}

if($action=="iron_total_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select sewing_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('sewing_production')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";


	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=7 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Iron Entry Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
      </fieldset>
		<?
	}
	else
	 {
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=7 and  d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
		//echo $sql;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		 ?>
    <div  align="center" style="width:100%">
       <fieldset  style="width:820px">
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
			<label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="60"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
								<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		  </table>
				  <br />
		 <?
		}
		  ?>
     </fieldset>
 </div>
  <?
	}
}

if($action=="iron_today_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select iron_update from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('iron_update')];  
	  }
	  if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=7 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
       <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Iron Entry Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
      </fieldset>
		<?
	}
	else
	 {
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=7 and  d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
	   ?>
  <div  align="center" style="width:100%">
       <fieldset  style="width:820px">
       <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
				{
				   $job_color_tot=0;
		 ?>   
			<label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="1030px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Color Total</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="60"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
						<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
						<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		  </table>
	     <br />
		 <?
		}
		  ?>
     </fieldset>
 </div>
  <?
	}
}


// for reject qty  reject_today


if($action=="reject_today_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
		$sql="SELECT  c.po_number,sum(d.reject_qnty) as reject_qnty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=5 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
      <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Sewing Rejact Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('reject_qnty')]; echo  $row[csf('reject_qnty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
      </fieldset>
		<?
	
}

if($action=="reject_total_all")
{
	 echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
     extract($_REQUEST);
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
		$sql="SELECT  c.po_number,sum(d.reject_qnty) as reject_qnty,d.production_date
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=5 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number,d.production_date";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
      <fieldset>
          <table width="450px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">production Date </th>
                        <th width="100">Sewing Reject Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="center"><? echo  $row[csf('production_date')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('reject_qnty')]; echo  $row[csf('reject_qnty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200"> </th>
                        <th width="100">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        
      </fieldset>
		<?
}


if($action=="finish_total_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select finishing_update from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('finishing_update')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=8 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
      <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Finish Qty.</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        
       </fieldset>
		<?
	}
	else
	 {
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=8 and  d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
		//echo $sql;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		?>
  <div  align="center" style="width:100%">
       <fieldset  style="width:820px">
	    <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
			<label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="60"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
								<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		  </table>
				  <br />
		 <?
		}
	  ?>
     </fieldset>
 </div>
  <?
	}
}

if($action=="finish_today_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select sewing_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('sewing_production')];  
	  }
	  
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
      </table>
    </fieldset>
       <br />
       <?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=8 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
      <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Finish Qty.</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
      </fieldset>
		<?
	}
	else
	 {
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=8 and  d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		?>
       <div  align="center" style="width:100%">
       <fieldset  style="width:820px">
	   <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
				{
				   $job_color_tot=0;
		 ?>   
			<label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="70">Color Total</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="60"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
								<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		  </table>
	     <br />
		 <?
		}
    
	}
}
// for iron entry 

if($action=="exfactory_total_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select ex_factory from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('ex_factory')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.ex_factory_qnty) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_ex_factory_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.ex_factory_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Exfactory Qty.</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
		<?
	}
	else
	 {
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_ex_factory_mst d, pro_ex_factory_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			 d.ex_factory_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
		//echo $sql;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		?>
  <div  align="center" style="width:100%">
       <fieldset  style="width:820px">
		 <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
			<label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="1030px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Color Total</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="60"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
						<?
							}
						}
						?>

				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
						<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		  </table>
				  <br />
		 <?
		}
	}
}

if($action=="exfactory_today_all")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select ex_factory from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('ex_factory')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.ex_factory_qnty) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_ex_factory_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.ex_factory_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Exfactory Qty.</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
		<?
	}
	else
	 {
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_ex_factory_mst d, pro_ex_factory_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			 d.ex_factory_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
		//echo $sql;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		foreach( $sql_data as $row)
		{
		$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
		$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('product_qty')];
		$job_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		}
		?>
  <div  align="center" style="width:100%">
       <fieldset  style="width:820px">
		 <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
			<label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="1030px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Color Total</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="60"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td><? echo  $colorArr[$value_c]; ?></td>
				 <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="60" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
						<?
							}
						}
						?>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
				 <th>Total</th>
				 <th align="right"><? echo  $job_color_tot; ?></th>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$value_po][$value_s];?></th>
						<?
							}
						}
						?>
				 </tr>
			   </tfoot>
		  </table>
				  <br />
		 <?
		}
	}
}

//================================================finish style with full shipment=====================================================================




// total cut quantity 

if($action=="cut_entry_total")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select cutting_update,production_entry from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
//  echo "select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no";
	$sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");

	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('cutting_update')];  
	  }
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
				 // if($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
    <?
	
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=1 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        <fieldset style="width:400px">
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Cutting Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,sum(f.order_quantity) as order_quantity,sum(f.plan_cut_qnty) as plan_cut_qnty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=1 and  d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
			
		//echo $sql;
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		//******************************************************
		
	    $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
		FROM wo_po_details_master a
		LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
		AND c.is_deleted =0
		AND c.status_active =1
		LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
		AND c.id = d.po_break_down_id
		AND d.is_deleted =0
		AND d.status_active =1
		WHERE
		a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
		a.is_deleted =0 and
		a.status_active =1";
		//echo $sql_order;
		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
        //******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
		{
			$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
			$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
		}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
		{
			$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
			$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
			$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
			$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
			
			$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
			$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
		}

		?>
	
		 <fieldset>
		
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		
			<label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="930px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="40"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
						<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
                            ?>
                            <td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
                            <?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                    
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
                            ?>
                            <td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
                            <?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
				<?
				$i++;
				}
				}
				?>
				  </table>
				  <br />
		 <?
		}
		?>
      </fieldset>
        <?
	}
	?>
  
    </div>
    
    <?
}

if($action=="cut_entry_today")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select cutting_update,production_entry from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('cutting_update')];  
	  }
     if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
	 // echo "select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no";
	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                       <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
       </table>
       </fieldset>
       <br />
	<?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.shiping_status!=3 and
			c.id=d.po_break_down_id and
			d.production_type=1 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Cutting Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
					 if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		  $sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
				FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id  and
				d.id=e.mst_id and
				e.color_size_break_down_id=f.id and
				c.id=f.po_break_down_id and
				e.production_type=1 and  d.production_date='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
				a.status_active =1 and e.is_deleted =0 and
				e.status_active =1
				group by c.po_number,f.size_number_id,f.color_number_id";
				
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		
	    $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
		FROM wo_po_details_master a
		LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
		AND c.is_deleted =0
		AND c.status_active =1
		LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
		AND c.id = d.po_break_down_id
		AND d.is_deleted =0
		AND d.status_active =1
		WHERE
		a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
		a.is_deleted =0 and
		a.status_active =1";
		//echo $sql_order;
		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
		{
			$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
			$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
		}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
		{
			$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
			$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
			$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
			$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
			
			$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
			$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			
		}
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="930px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="40"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
						<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
                            ?>
                            <td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
                            <?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>
				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                    
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
                            ?>
                            <td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
                            <?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 </tr>
				<?
				$i++;
				}
				}
				?>
				  </table>
				  <br />
		 <?
		}
		?>
      </fieldset>
        <?
	}
	?>
    </div>
    <?
}

// fro embleshment total
if($action=="embl_today")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select printing_emb_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('printing_emb_production')];  
	  }
	
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty 
	                      from  wo_po_details_master a,wo_po_break_down b
						  where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and  a.company_name=$company_id 
						  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                       <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
	<?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=2 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Embl. Issue Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		
		
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=2 and  d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		
	    $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
		FROM wo_po_details_master a
		LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
		AND c.is_deleted =0
		AND c.status_active =1
		LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
		AND c.id = d.po_break_down_id
		AND d.is_deleted =0
		AND d.status_active =1
		WHERE
		a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
		a.is_deleted =0 and
		a.status_active =1";

		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
			{
				$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
				$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
				$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
		  {
			$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
			$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
			$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
			$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
			
			$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
			$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
		  }
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="930px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="40"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                          <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                     <td align="left">Production Qty</td>
                          <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 </tr>
				<?
				$i++;
				}
			}
				?>
	  </table>
		 <br />
		 <?
		}
		?>
     </fieldset>
        <?
	}
	?>
    </div>
    <?
}

if($action=="embl_total")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select printing_emb_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('printing_emb_production')];  
	  }
	
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
	 // echo "select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no";
	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
       <?
	
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=2 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Embl. Issue Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		  $sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
				FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
				WHERE 
				a.job_no = c.job_no_mst and
				c.shiping_status!=3 and
				c.id=d.po_break_down_id  and
				d.id=e.mst_id and
				e.color_size_break_down_id=f.id and
				c.id=f.po_break_down_id and
				e.production_type=2 and  d.production_date<='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
				a.status_active =1 and e.is_deleted =0 and
				e.status_active =1
				group by c.po_number,f.size_number_id,f.color_number_id";
				
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		
	    $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
					FROM wo_po_details_master a
					LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
					AND c.is_deleted =0
					AND c.status_active =1
					LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
					AND c.id = d.po_break_down_id
					AND d.is_deleted =0
					AND d.status_active =1
					WHERE
					a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
					a.is_deleted =0 and
					a.status_active =1";

		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
			{
				$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
				$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
				$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
			{
				$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
				$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
				$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			}
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="930px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="40"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                    
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
				<?
				$i++;
				}
				}
				?>
				  </table>
               
				  <br />
		 <?
		}
		?>
      </fieldset>
        <?
	}
	?>
    </div>
    <?
}

if($action=="embl_receive_total")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select printing_emb_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('printing_emb_production')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                       <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
       </table>
       </fieldset>
       <br />
       <?
	if($entry_break_down==1)
	  {
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.shiping_status!=3 and
			c.id=d.po_break_down_id and
			d.production_type=3 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
		$sql_data = sql_select($sql);
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Embl. Receive Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
	   	$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
				FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id  and
				d.id=e.mst_id and
				e.color_size_break_down_id=f.id and
				c.id=f.po_break_down_id and
				e.production_type=3 and  d.production_date<='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
				a.status_active =1 and e.is_deleted =0 and
				e.status_active =1
				group by c.po_number,f.size_number_id,f.color_number_id";
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql); 
	    $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
		FROM wo_po_details_master a
		LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
		AND c.is_deleted =0
		AND c.status_active =1
		LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
		AND c.id = d.po_break_down_id
		AND d.is_deleted =0
		AND d.status_active =1
		WHERE
		a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
		a.is_deleted =0 and
		a.status_active =1
		";
		//echo $sql_order;
		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
		{
			$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
			$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
		}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
		{
			$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
			$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
			$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
			$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
			$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
			$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
		}
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="830px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="40"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                    
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
				<?
				$i++;
				}
				}
				?>
				  </table>
				  <br />
		 <?
		}
		?>
      </fieldset>
        <?
	}
	?>
  
    </div>
    
    <?
}

if($action=="embl_receive_today")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select printing_emb_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('printing_emb_production')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                       <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
       </table>
       </fieldset>
       <br />
       <?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			c.shiping_status!=3 and
			d.production_type=3 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Embl. Receive Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			c.id=f.po_break_down_id and
			e.production_type=3 and  d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
			a.status_active =1 and e.is_deleted =0 and
			e.status_active =1
			group by c.po_number,f.size_number_id,f.color_number_id";
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
				
	    $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
		FROM wo_po_details_master a
		LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
		AND c.is_deleted =0
		AND c.status_active =1
		LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
		AND c.id = d.po_break_down_id
		AND d.is_deleted =0
		AND d.status_active =1
		WHERE
		a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
		a.is_deleted =0 and
		a.status_active =1";
		//echo $sql_order;
		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
			{
				$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
				$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
				$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
			{
				$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
				$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
				$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				
				$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
				
			}
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="830px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="40"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                    
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?>                      </td>

				 </tr>
				<?
				$i++;
				}
				}
				?>
	     </table>
	  <br />
		 <?
		}
		?>
      </fieldset>
        <?
	}
	?>
    </div>
    <?
}



if($action=="sewing_input_total")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select sewing_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('sewing_production')];  
	  }
	
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                       <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
	<?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.shiping_status!=3 and
			c.id=d.po_break_down_id and
			d.production_type=4 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Sewing Input Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                          ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		  $sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
				FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id  and
				d.id=e.mst_id and
				e.color_size_break_down_id=f.id and
				c.id=f.po_break_down_id and
				e.production_type=4 and  d.production_date<='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
				a.status_active =1 and e.is_deleted =0 and
				e.status_active =1
				group by c.po_number,f.size_number_id,f.color_number_id";
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		
	   $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
					FROM wo_po_details_master a
					LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
					AND c.is_deleted =0
					AND c.status_active =1
					LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
					AND c.id = d.po_break_down_id
					AND d.is_deleted =0
					AND d.status_active =1
					WHERE
					a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
					a.is_deleted =0 and
					a.status_active =1";
		//echo $sql_order;
		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
			{
				$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
				$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
				$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
			{
				$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
				$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
				$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				
				$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
				
			}
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="830px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="40"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                    
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
				<?
				$i++;
				}
				}
				?>
		   </table>
	     <br />
		 <?
		}
		?>
      </fieldset>
        <?
	}
	?>
    </div>
    <?
}

if($action=="sewing_input_today")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select sewing_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('sewing_production')];  
	  }
	  if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                       <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
	<?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=4 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Sewing Input Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		  $sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
				FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id  and
				d.id=e.mst_id and
				e.color_size_break_down_id=f.id and
				c.id=f.po_break_down_id and
				e.production_type=4 and  d.production_date='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
				a.status_active =1 and e.is_deleted =0 and
				e.status_active =1
				group by c.po_number,f.size_number_id,f.color_number_id";
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		
	    $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
					FROM wo_po_details_master a
					LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
					AND c.is_deleted =0
					AND c.status_active =1
					LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
					AND c.id = d.po_break_down_id
					AND d.is_deleted =0
					AND d.status_active =1
					WHERE
					a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
					a.is_deleted =0 and
					a.status_active =1";
		//echo $sql_order;
		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
			{
				$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
				$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
				$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
			{
				$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
				$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
				$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			}
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="830px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="40"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                    
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
				<?
				$i++;
				}
			}
		?>
	 </table>
	 <br />
		 <?
	}
		?>
     </fieldset>
        <?
	}
	?>
    </div>
    <?
}


if($action=="sewing_ooutput_total")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select sewing_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('sewing_production')];  
	  }
	
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
	
	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                       <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
	<?
	
	if($entry_break_down==1)
	{
		  $sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
				FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id and
				d.production_type=5 and d.production_date<='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
				a.status_active =1 and d.is_deleted =0 and
				d.status_active =1
				group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Sewing Output Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						 if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		  $sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
				FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id  and
				d.id=e.mst_id and
				e.color_size_break_down_id=f.id and
				c.id=f.po_break_down_id and
				e.production_type=5 and  d.production_date<='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
				a.status_active =1 and e.is_deleted =0 and
				e.status_active =1
				group by c.po_number,f.size_number_id,f.color_number_id";
		//echo $sql;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		
		$sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
					FROM wo_po_details_master a
					LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
					AND c.is_deleted =0
					AND c.status_active =1
					LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
					AND c.id = d.po_break_down_id
					AND d.is_deleted =0
					AND d.status_active =1
					WHERE
					a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
					a.is_deleted =0 and
					a.status_active =1";
		//echo $sql_order;
		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
			{
				$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
				$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
				$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
			{
				$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
				$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
				$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				
				$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
				
			}
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="830px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="40"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                    
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 </tr>
				<?
				$i++;
				}
				}
				?>
			 </table>
				  <br />
		 <?
		}
		?>
      </fieldset>
        <?
	}
	?>
    </div>
    <?
}

if($action=="sewing_output_today")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select sewing_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('sewing_production')];  
	  }
	  if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
	
	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                        <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
       </table>
       </fieldset>
       <br />
	<?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=5 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Sewing Output Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		  $sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
				FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id  and
				d.id=e.mst_id and
				e.color_size_break_down_id=f.id and
				c.id=f.po_break_down_id and
				e.production_type=5 and  d.production_date='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
				a.status_active =1 and e.is_deleted =0 and
				e.status_active =1
				group by c.po_number,f.size_number_id,f.color_number_id";
			$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
			$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		
		
	   $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
					FROM wo_po_details_master a
					LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
					AND c.is_deleted =0
					AND c.status_active =1
					LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
					AND c.id = d.po_break_down_id
					AND d.is_deleted =0
					AND d.status_active =1
					WHERE
					a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
					a.is_deleted =0 and
					a.status_active =1";
		//echo $sql_order;
		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
			{
				$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
				$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
				$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
			{
				$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
				$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
				$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			}
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?><strong><label/>

			<table width="830px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="40"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                    
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 </tr>
				<?
				$i++;
				}
				}
				?>
		  </table>
		  <br />
		 <?
		}
		?>
      </fieldset>
        <?
	}
	?>
    </div>
    <?
}


if($action=="iron_total")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select sewing_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('sewing_production')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";

	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                       <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
       </table>
       </fieldset>
       <br />
	<?
	
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=7 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Iron Entry Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		 $sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
				FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id  and
				d.id=e.mst_id and
				e.color_size_break_down_id=f.id and
				c.id=f.po_break_down_id and
				e.production_type=7 and  d.production_date<='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
				a.status_active =1 and e.is_deleted =0 and
				e.status_active =1
				group by c.po_number,f.size_number_id,f.color_number_id";
			//echo $sql;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		
	    $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
					FROM wo_po_details_master a
					LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
					AND c.is_deleted =0
					AND c.status_active =1
					LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
					AND c.id = d.po_break_down_id
					AND d.is_deleted =0
					AND d.status_active =1
					WHERE
					a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
					a.is_deleted =0 and
					a.status_active =1";
		//echo $sql_order;
		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
			{
				$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
				$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
				$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
			{
				$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
				$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
				$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			}
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="830px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="40"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                    
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
				<?
				$i++;
				}
				}
				?>
		  </table>
		  <br />
		 <?
		}
		?>
      </fieldset>
        <?
	}
	?>
    </div>
    <?
}


if($action=="iron_today")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select iron_update from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('iron_update')];  
	  }
	   if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
	 // echo "select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no";
	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                       <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
	<?
	if($entry_break_down==1)
	{
		  $sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
				FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id and
				d.production_type=7 and d.production_date='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
				a.status_active =1 and d.is_deleted =0 and
				d.status_active =1
				group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Iron Entry Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		 $sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
				FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id  and
				d.id=e.mst_id and
				e.color_size_break_down_id=f.id and
				c.id=f.po_break_down_id and
				e.production_type=7 and  d.production_date='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
				a.status_active =1 and e.is_deleted =0 and
				e.status_active =1
				group by c.po_number,f.size_number_id,f.color_number_id";
	    $colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
	    $itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		
		
	    $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
					FROM wo_po_details_master a
					LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
					AND c.is_deleted =0
					AND c.status_active =1
					LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
					AND c.id = d.po_break_down_id
					AND d.is_deleted =0
					AND d.status_active =1
					WHERE
					a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
					a.is_deleted =0 and
					a.status_active =1";
		//echo $sql_order;
		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
			{
				$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
				$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
				$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
			{
				$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
				$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
				$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			}
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="830px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="40"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                    
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
                            ?>
                            <td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
                            <?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
				<?
				$i++;
				}
				}
				?>
			 </table>
				  <br />
		 <?
		}
		?>
      </fieldset>
        <?
	}
	?>
    </div>
    <?
}


// for reject qty  reject_today


if($action=="reject_today")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
	 // echo "select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no";
	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
	<?
		$sql="SELECT  c.po_number,sum(d.reject_qnty) as reject_qnty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=5 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        <fieldset  style="width:400px">
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Sewing Output Total</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('reject_qnty')]; echo  $row[csf('reject_qnty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	
}


if($action=="re_iron_total")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
	 // echo "select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no";
	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                         <td align="right"><? echo $row[csf('qty')]; ?></td>
                    
                    
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
	<?
	
		$sql="SELECT  c.po_number,sum(d.re_production_qty) as re_iron,d.production_date
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=7 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number,d.production_date";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        <fieldset  style="width:500px">
          <table width="450px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">production Date </th>
                        <th width="100">Re-Iron Qty</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="center"><? echo  $row[csf('production_date')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('re_iron')]; echo  $row[csf('re_iron')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200"> </th>
                        <th width="100">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
}





if($action=="reject_total")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
	 // echo "select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no";
	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                       <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
       </table>
       </fieldset>
       <br />
	<?
	
		  $sql="SELECT  c.po_number,sum(d.reject_qnty) as reject_qnty,d.production_date
				FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id and
				d.production_type=5 and d.production_date<='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
				a.status_active =1 and d.is_deleted =0 and
				d.status_active =1
				group by c.po_number,d.production_date";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        <fieldset  style="width:500px">
          <table width="450px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">production Date </th>
                        <th width="100">Sewing Reject</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="center"><? echo  $row[csf('production_date')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('reject_qnty')]; echo  $row[csf('reject_qnty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200"> </th>
                        <th width="100">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
}


if($action=="finish_total")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select finishing_update from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('finishing_update')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
	 // echo "select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no";
	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                       <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
       </table>
       </fieldset>
       <br />
	<?
	
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=8 and d.production_date<='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        <fieldset  style="width:400px">
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Finish Qty.</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		  $sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
				FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id  and
				d.id=e.mst_id and
				e.color_size_break_down_id=f.id and
				c.id=f.po_break_down_id and
				e.production_type=8 and  d.production_date<='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
				a.status_active =1 and e.is_deleted =0 and
				e.status_active =1
				group by c.po_number,f.size_number_id,f.color_number_id";
		//echo $sql;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
	    $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
					FROM wo_po_details_master a
					LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
					AND c.is_deleted =0
					AND c.status_active =1
					LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
					AND c.id = d.po_break_down_id
					AND d.is_deleted =0
					AND d.status_active =1
					WHERE
					a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
					a.is_deleted =0 and
					a.status_active =1	";
		//echo $sql_order;
		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
			{
				$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
				$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
				$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
			{
				$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
				$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
				$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			}
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="830px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="40"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                    
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
				<?
				$i++;
				}
				}
				?>
		 </table>
				  <br />
		 <?
		}
		?>
      </fieldset>
        <?
	}
	?>
  
    </div>
    
    <?
}


if($action=="finish_today")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$sql_result = sql_select("select sewing_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('sewing_production')];  
	  }
	   if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
	
	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                       <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
	<?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.production_quantity) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.production_type=8 and d.production_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		?>
        <fieldset  style="width:400px">
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Finish Qty.</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
       </fieldset>
		<?
	}
	else
	 {
	  	$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
				FROM wo_po_details_master a,wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id  and
				d.id=e.mst_id and
				e.color_size_break_down_id=f.id and
				c.id=f.po_break_down_id and
				e.production_type=8 and  d.production_date='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
				a.status_active =1 and e.is_deleted =0 and
				e.status_active =1
				group by c.po_number,f.size_number_id,f.color_number_id";
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		
	    $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
					FROM wo_po_details_master a
					LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
					AND c.is_deleted =0
					AND c.status_active =1
					LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
					AND c.id = d.po_break_down_id
					AND d.is_deleted =0
					AND d.status_active =1
					WHERE
					a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
					a.is_deleted =0 and
					a.status_active =1";
		//echo $sql_order;
		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
		{
			$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
			$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
		}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
		{
			$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
			$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
			$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
			$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
			$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
			$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
		}
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="830px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
						?>
						<th width="40"><? echo $itemSizeArr[$value];?></th>
						<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
						<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
                            ?>
                            <td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
                            <?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>
				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                    
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
                            ?>
                            <td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
                            <?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 </tr>
				<?
				$i++;
				}
				}
				?>
		  </table>
	  <br />
				 
		 <?
		}
		?>
    </fieldset>
        <?
    }
	?>
    </div>
    <?
}



if($action=="exfactory_total")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select ex_factory from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('ex_factory')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
	
	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix'  and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                        <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
	<?
	
	if($entry_break_down==1)
	{
		 $sql="SELECT  c.po_number,sum(d.ex_factory_qnty) as product_qty
				FROM wo_po_details_master a,wo_po_break_down c,pro_ex_factory_mst d
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id and
				c.shiping_status!=3 and
				d.ex_factory_date<='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
				a.status_active =1 and d.is_deleted =0 and
				d.status_active =1
				group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Exfactory Qty.</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
	   	$sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
				FROM wo_po_details_master a,wo_po_break_down c,pro_ex_factory_mst d, pro_ex_factory_dtls e,wo_po_color_size_breakdown f
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id  and
				d.id=e.mst_id and
				e.color_size_break_down_id=f.id and
				c.id=f.po_break_down_id and
				 d.ex_factory_date<='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
				a.status_active =1 and e.is_deleted =0 and
				e.status_active =1
				group by c.po_number,f.size_number_id,f.color_number_id";
		//echo $sql;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		
	    $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
		FROM wo_po_details_master a
		LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
		AND c.is_deleted =0
		AND c.status_active =1
		LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
		AND c.id = d.po_break_down_id
		AND d.is_deleted =0
		AND d.status_active =1
		WHERE
		a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
		a.is_deleted =0 and
		a.status_active =1";
		//echo $sql_order;
		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
			{
				$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
				$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
				$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
			{
				$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
				$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
				$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			}
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="830px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="40"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c];                        ?></td>
				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>
				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>
				 </tr>
				<?
				$i++;
				}
				}
				?>
		  </table>
		 <br />
		 <?
		}
		?>
      </fieldset>
        <?
	}
	?>
    </div>
    <?
}

if($action=="exfactory_today")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$sql_result = sql_select("select ex_factory from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	  foreach($sql_result as $val)
	  {
		$entry_break_down=$val[csf('ex_factory')];  
	  }
	 if($db_type==0) $group_concat="group_concat(distinct(b.po_number)) as po_number"; 
	 if($db_type==2) $group_concat="listagg(b.po_number,',') within group (order by b.po_number) as po_number";
	 $sql_job=sql_select("select a.buyer_name,a.style_ref_no,$group_concat,max(shipment_date) max_date,sum(b.po_quantity) as qty from  wo_po_details_master a,wo_po_break_down b where a.job_no = b.job_no_mst  and a.job_no='$jobnumber_prefix' and b.shiping_status!=3 and a.company_name=$company_id  group by a.buyer_name,a.style_ref_no");
	?>
	  <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="100">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="200">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		    foreach($sql_job as $row)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $jobnumber_prefix; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('max_date')]); ?></td>
                        <td align="right"><? echo $row[csf('qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          
       </table>
       </fieldset>
       <br />
	<?
	if($entry_break_down==1)
	{
		$sql="SELECT  c.po_number,sum(d.ex_factory_qnty) as product_qty
			FROM wo_po_details_master a,wo_po_break_down c,pro_ex_factory_mst d
			WHERE 
			a.job_no = c.job_no_mst and
			c.id=d.po_break_down_id and
			d.ex_factory_date='$insert_date' and
			a.job_no='$jobnumber_prefix' and a.company_name=$company_id  $year_cond and a.is_deleted =0 and
			a.status_active =1 and d.is_deleted =0 and
			d.status_active =1
			group by c.po_number";
			//echo $sql;
		$sql_data = sql_select($sql);
		
		?>
        <fieldset>
          <table width="350px" align="center" border="1" rules="all" class="rpt_table" >
                <thead>
					<tr>
						<th width="40">SL</th>
						<th width="200">Po Number </th>
                        <th width="100">Exfactory Qty.</th>
                     </tr>
                 </thead>
					<?
					$total_qty=0;
					$i=1;
                    foreach( $sql_data as $row)
                     {
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						  ?>	
						  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                               <td width="40"><? echo  $i; ?></td>
                               <td width="150" align="center"><? echo  $row[csf('po_number')]; ?></td>
                               <td width="100" align="right"><? $total_qty+=$row[csf('product_qty')]; echo  $row[csf('product_qty')];?></td>
						  </tr>
						  <?
					  $i++;
                    }
                    ?>
		       <tfoot>
                 	<tr>
						<th width="40"></th>
						<th width="200">Total </th>
                        <th width="100"><? echo $total_qty; ?></th>
                     </tr>
                </tfoot>
		</table>
        </fieldset>
		<?
	}
	else
	 {
		  $sql="SELECT  c.po_number,sum(e.production_qnty) as product_qty,f.size_number_id,f.color_number_id
				FROM wo_po_details_master a,wo_po_break_down c,pro_ex_factory_mst d, pro_ex_factory_dtls e,wo_po_color_size_breakdown f
				WHERE 
				a.job_no = c.job_no_mst and
				c.id=d.po_break_down_id  and
				d.id=e.mst_id and
				e.color_size_break_down_id=f.id and
				c.id=f.po_break_down_id and
				 d.ex_factory_date='$insert_date' and
				a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and a.is_deleted =0 and
				a.status_active =1 and e.is_deleted =0 and
				e.status_active =1
				group by c.po_number,f.size_number_id,f.color_number_id";
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$po_number=array();
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		
	    $sql_order="SELECT  c.po_number,d.size_number_id, d.color_number_id, d.order_quantity,d.plan_cut_qnty
					FROM wo_po_details_master a
					LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
					AND c.is_deleted =0
					AND c.status_active =1
					LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
					AND c.id = d.po_break_down_id
					AND d.is_deleted =0
					AND d.status_active =1
					WHERE
					a.job_no='$jobnumber_prefix' and a.company_name=$company_id  and
					a.is_deleted =0 and
					a.status_active =1";
		//echo $sql_order;
		$order_size_qnty_array=array();
		$order_color_qnty_array=array();
		$order_total_qnty_array=array();
		//******************************************************
		$plan_size_qnty_array=array();
		$plan_color_qnty_array=array();
		$plan_total_qnty_array=array();
		//******************************************************
		$sql_data = sql_select($sql);
		foreach( $sql_data as $val)
			{
				$job_size_qnty_array[$val[csf('po_number')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
				$job_color_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]]+=$val[csf('product_qty')];
				$job_color_size_qnty_array[$val[csf('po_number')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('product_qty')];
			}
		//*****************************************************************************************************************************************
		$sql_order_data = sql_select($sql_order);
		foreach( $sql_order_data as $row)
			{
				$po_number[$row[csf('po_number')]]=$row[csf('po_number')];
				$job_size_array[$row[csf('po_number')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_color_array[$row[csf('po_number')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$order_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$order_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
				$order_color_size_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
				$plan_size_qnty_array[$row[csf('po_number')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
				$plan_color_size_qnty_array[$row[csf('po_number')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
			}
			?>
		 <fieldset>
		  <?
		    $i=1;
			foreach($po_number as $key_po=>$value_po)
						{
						$job_color_tot=0;
		 ?>   
		    <label> <strong>Po Number: <? echo $value_po; ?><strong><label/>
			<table width="830px" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="60">Color</th>
						<th width="60">Quantity Type</th>
						<?
						
						foreach($job_size_array[$value_po] as $key=>$value)
						{
							if($value !="")
							{
								?>
								<th width="40"><? echo $itemSizeArr[$value];?></th>
								<?
							}
						}
						?>
                       <th width="60">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($job_color_array[$value_po] as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($value_c != "")
				{
				?>
                  <tr bgcolor="<? echo $bgcolor;?>">
				     <td rowspan="3"><? echo  $colorArr[$value_c]; ?></td>
				     <td align="left">Order qty</td>
				 <?
						foreach($job_size_array[$value_po] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
								?>
								<td width="40" align="right"><? echo $order_color_size_array[$value_po][$value_c][$value_s];?></td>
								<?
							}
						}
						?>
                        <td align="right"><? echo $order_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                         
                         <td align="left">Plan Cut Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $plan_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                        <td align="right"><? echo $plan_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$plan_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
                 <tr bgcolor="<? echo $bgcolor;?>">
                    
                     <td align="left">Production Qty</td>
                     <?
                            foreach($job_size_array[$value_po] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td width="40" align="right"><? echo $job_color_size_qnty_array[$value_po][$value_c][$value_s];?></td>
									<?
                                }
                            }
                            ?>
                      <td align="right"><? echo  $job_color_qnty_array[$value_po][$value_c]; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
				<?
				$i++;
				}
				}
				?>
		  </table>
		  <br />
		 <?
		}
		?>
      </fieldset>
        <?
	}
	?>
    </div>
    <?
}
