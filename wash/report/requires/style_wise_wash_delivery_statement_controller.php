<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');



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
if($action=="job_no_popup")
{
	//echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	 
      <script>
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() {
			 
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;//'setFilterGrid(\'list_view\',-1)'
			
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
			//alert(strCon);
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
		if($('#tr_'+str).css("display")!="none")
		{
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
				selected_no.push( str );				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_no.splice( i, 1 ); 
			}
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
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name ); 
			$('#hide_job_sl').val( num );
		}
    </script>
    
    </head>
    <body>
    <div align="center">
       <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Party Name</th>
                    <th>Search By</th>
                    
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th>Year</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					 
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
							// echo $cbo_within_group.'='.$buyer_name;
							 if($cbo_within_group==1)
							 {
								// echo "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.id=$companyID $company_cond order by comp.company_name";
							// echo create_drop_down( "cbo_party_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
							echo create_drop_down( "cbo_buyer_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $buyer_name, "");
							 
							 }
							 else
							 {
								 	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							 }
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Wash Job No",2=>"Buyer Style",3=>"W/O");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td> 
                         <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 
                         <td align="center">	
                    	<?
                       		//$search_by_arr=array(1=>"Wash Job No",2=>"Buyer Style",3=>"W/O");
							//$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							 echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); 
						?>
                        </td>     
                       	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_year').value+'**'+'<? echo $cbo_within_group; ?>'+'**'+'<? echo $txt_job; ?>'+'**'+'<? echo $txt_job_id; ?>'+'**'+'<? echo $txt_job_sl; ?>', 'create_list_style_search', 'search_div', 'style_wise_wash_delivery_statement_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
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

if($action=="create_list_style_search")
{
	extract($_REQUEST);
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$within_group=$data[5];
	
	$cbo_search_by=$data[2];
	$search_str=$data[3];
	$txt_job=$data[6];
	$txt_job_id=$data[7];
	$txt_job_sl=$data[8];
	//echo $within_group.'='.$txt_job;;
	//echo $year_id;
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
	if($within_group==1)
	{
		//$within_con="and d.within_group=$cbo_within_group";
		$party_arr=return_library_array( "SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
	}
	$search_cond="";
	if($search_str!="")
	{
		if($cbo_search_by==1)
		{
			$search_cond="and a.job_no_prefix_num=$search_str";
		}
		else if($cbo_search_by==2)
		{
			$search_cond="and b.buyer_style_ref='$search_str'";
		}
		elseif($cbo_search_by==3)
		{
			$search_cond="and b.order_no like '%$search_str%'";
		}
	}
	
	//$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	//$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.party_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.party_id=$data[1]";
	}
	
	//$search_by=$data[2];
	//$search_string="%".trim($data[3])."%";
	//if($search_by==2) $search_field="a.buyer_style_ref"; else $search_field="a.subcon_job";
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";
	
	if($db_type==0)
	{
		//$year_field_con=" and YEAR(a.insert_date) as year";
		if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		//$year_field_con=" and to_char(a.insert_date,'YYYY') as year";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
	//echo $within_group;
	//wo_booking_mst
	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (2=>$party_arr,7=>$buyer_arr);
	if($within_group==1)
	{
		
	  $sql= "select a.id, a.party_id,a.subcon_job as job_no, a.job_no_prefix_num, a.company_id as company_name, a.party_id as buyer_name,b.party_buyer_name, b.buyer_style_ref as style_ref_no,b.order_no as wo_no,c.booking_no_prefix_num as wo_prefix, $year_field from subcon_ord_mst a,subcon_ord_dtls b,wo_booking_mst c where  a.subcon_job=b.job_no_mst and b.order_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and a.entry_form=295 and c.booking_type=6 and a.within_group=$within_group  and c.status_active=1 and c.is_deleted=0 and a.company_id=$company_id   $buyer_id_cond $search_cond $year_cond  group by a.id,a.insert_date, a.subcon_job, a.job_no_prefix_num, a.company_id, b.order_no,b.party_buyer_name,a.party_id, b.buyer_style_ref,a.insert_date,c.booking_no_prefix_num order by a.id desc";
	}
	else
	{
		  $sql= "select a.id, a.party_id,a.subcon_job as job_no, a.job_no_prefix_num, a.company_id as company_name, a.party_id as buyer_name,b.party_buyer_name, b.buyer_style_ref as style_ref_no,b.order_no as wo_no, $year_field from subcon_ord_mst a,subcon_ord_dtls b where  a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and a.entry_form=295 and a.company_id=$company_id  and a.within_group=$within_group  $buyer_id_cond $search_cond $year_cond  group by a.id,a.insert_date, a.subcon_job, a.job_no_prefix_num, a.company_id, b.order_no,b.party_buyer_name,a.party_id, b.buyer_style_ref,a.insert_date order by a.id desc";
	}
	
	echo create_list_view("list_view", "Wash Job no,Job Year,Customer,Customer Buyer,Buyer Style,WO No,WO Suffix", "120,50,100,80,100,100,60","700","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0,0,party_id,0,0", $arr , "job_no_prefix_num,year,party_id,party_buyer_name,style_ref_no,wo_no,wo_prefix",  "","setFilterGrid('list_view',-1)","0","",1) ;
	
	echo "<input type='hidden' id='hide_job_no' />";
	echo "<input type='hidden' id='hide_job_id' />";
	echo "<input type='hidden' id='hide_job_sl' />";
	?>
   <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_job;?>';
	var style_id='<? echo $txt_job_id;?>';
	var style_des='<? echo $txt_job_sl;?>';
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
		}
	}
	</script>
    <?
	exit(); 
} // Job Search end


if($action=="report_generate")
{ 
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_buyer_id=str_replace("'","",$cbo_party_name);
	$company_id=str_replace("'","",$cbo_company_id);
	$cbo_within_group=str_replace("'","",$cbo_within_group);
	$from_date=str_replace("'","",$txt_date_from);
	$to_date=str_replace("'","",$txt_date_to);
	$txt_job=str_replace("'","",$txt_job);
	$txt_job_id=str_replace("'","",$txt_job_id);
	$cbo_shipping=str_replace("'","",$cbo_shipping);
	$cbo_party_name=str_replace("'","",$cbo_party_name);
	$report_type=str_replace("'","",$report_type);
	
	//if($db_type==0) $select_from_date=change_date_format($from_date,'yyyy-mm-dd');
	//if($db_type==2) $select_from_date=change_date_format($from_date,'','',1);
	
	//$date_from=date('Y-m-d', strtotime('-1 day', strtotime($from_date)));
	//$datefrom=change_date_format($date_from,'yyyy-mm-dd');
	 
	if($txt_job!="") $job_cond="and d.job_no_prefix_num in($txt_job)";else $job_cond="";
	if($txt_job_id) $job_cond_id="and d.id in($txt_job_id)";else $job_cond_id="";
	
	if($cbo_within_group>0)
	{
		$within_con="and d.within_group=$cbo_within_group";
	}
	else $within_con="";
	
	if($cbo_shipping==2 || $cbo_shipping==1)
	{
		$ship_con="and f.job_order_status in(1,2)";
	}
	elseif($cbo_shipping==3)
	{
		$ship_con="and f.job_order_status in(3)";
	}
	else $ship_con="";
	
	if($cbo_party_name>0)
	{
		$party_con="and d.party_id in($cbo_party_name)";
	}
	else $party_con="";
	
	
	// if($cbo_within_group==1)
	// {
	// 	//$within_con="and d.within_group=$cbo_within_group";
	// 	$party_arr=return_library_array( "SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
	// }
	// else
	// {
	// 	$party_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
	// }
	
	$search_str=trim(str_replace("'","",$txt_search_string));
	$search_type =str_replace("'","",$cbo_type);
	if($search_str!="")
	{
		 if($search_type==1) $search_com_cond=" and c.buyer_style_ref like '%$search_str'"; 
		else if($search_type==2) $search_com_cond=" and c.order_no like '%$search_str'";  
	}
		//echo $search_com_cond; die;
//	if($cbo_buyer_id==0) $party_con=""; else $party_con=" and d.party_id='$cbo_buyer_id'";
	if($cbo_within_group==0) $within_group=""; else $within_group=" and d.within_group='$cbo_within_group'";
	// return_library_array satart 
	$color_library_arr=return_library_array( "select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	// if($cbo_within_group==1)
	// {
	// 	$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	// }
	// else
	// {
	// 	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	// }
	// return_library_array end 
	if(str_replace("'","",$company_id)==0)$company_name=""; else $company_name=" and d.company_id=$company_id";
	if($db_type==0)
	{ 
		if ($from_date!="" &&  $to_date!="") $delivery_date_cond = "and f.delivery_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'"; else $order_rcv_date ="";
	}
	else
	{
		if ($from_date!="" &&  $to_date!="") $delivery_date_cond = "and f.delivery_date between '".change_date_format($from_date, "", "",1)."' and '".change_date_format($to_date, "", "",1)."'"; else $order_rcv_date ="";
	}
	//wo_booking_mst
	
	  $job_sql="select d.id as job_id,d.subcon_job,d.party_id,d.order_no,d.within_group,d.order_no,d.currency_id, d.exchange_rate,c.party_buyer_name,c.id as order_id,c.order_uom,c.buyer_style_ref,c.delivery_status,c.gmts_color_id,c.amount,f.remarks, c.amount_domestic,c.order_quantity,c.gmts_item_id,g.delivery_qty
from subcon_ord_mst d, subcon_ord_dtls c,subcon_delivery_mst f,subcon_delivery_dtls g
where d.id=c.mst_id  and f.id=g.mst_id  and f.job_no=d.subcon_job  and c.id=g.order_id and f.entry_form=303 and d.entry_form=295 and d.status_active=1 and c.status_active=1 and f.status_active=1 and g.status_active=1 $within_group $search_com_cond $company_name $job_cond  $job_cond_id $within_con $party_con  $delivery_date_cond order by d.party_id  "; 
	// echo $batch_sql;
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		
		$job_id_arr[$row[csf('job_id')]]=$row[csf('job_id')];
	}
if($report_type==1)// ======Show Button==========
{	
	 $delivery_sql="select d.id as job_id,d.subcon_job,d.party_id,d.order_no,d.within_group,d.order_no,d.currency_id, d.exchange_rate,c.party_buyer_name,c.id as order_id,c.order_uom,c.buyer_style_ref,c.delivery_status,c.gmts_color_id,c.amount,f.remarks, c.amount_domestic,c.order_quantity,c.gmts_item_id,g.delivery_qty
from subcon_ord_mst d, subcon_ord_dtls c,subcon_delivery_mst f,subcon_delivery_dtls g
where d.id=c.mst_id  and f.id=g.mst_id  and f.job_no=d.subcon_job  and c.id=g.order_id and f.entry_form=303 and d.entry_form=295 and d.status_active=1 and c.status_active=1 and f.status_active=1 and g.status_active=1 ".where_con_using_array($job_id_arr,0,'d.id')."   order by d.party_id  ";
	$delivery_sql_result=sql_select($delivery_sql);
	foreach($delivery_sql_result as $row)
	{
		$style_wise_chk_arr[$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['delivery_qty']+=$row[csf('delivery_qty')];
	}
			
	 $job_sql2="select  d.subcon_job,d.party_id,c.order_uom,c.buyer_style_ref,c.order_quantity,c.amount
	from subcon_ord_mst d, subcon_ord_dtls c
	where d.id=c.mst_id and d.entry_form=295 and d.status_active=1 and d.is_deleted=0  and c.status_active=1 and c.is_deleted=0 ".where_con_using_array($job_id_arr,0,'d.id')." ";
			$job_sql_result2=sql_select($job_sql2);	
			$receive_arr=array();
			foreach($job_sql_result2 as $row)
			{
				$order_uom=$row[csf('order_uom')];
				$order_quantity=0;
				if($order_uom==2)
				{
					$order_quantity=$row[csf('order_quantity')]*12;
					//echo $order_quantity.'<br>';
				}
				else {
					$order_quantity=$row[csf('order_quantity')];
					}
			
				$job_style_arr[$row[csf('buyer_style_ref')]]['po_qty']+=$order_quantity;
				$job_style_arr[$row[csf('buyer_style_ref')]]['po_amt']+=$row[csf('amount')];
				//echo $order_quantity.'DD';
				$style_wise_chk_arr[$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['order_quantity']+=$order_quantity;
			}
			//print_r($style_wise_chk_arr);
			//unset($job_sql_result);
			
	$po_chk_arr=array();
	foreach($job_sql_result as $row)
	{
		 $order_uom=$row[csf('order_uom')];
		 $chk_order_quantity=$style_wise_chk_arr[$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['order_quantity'];
		 $chk_delivery_qty=$style_wise_chk_arr[$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['delivery_qty'];
		
		$order_quantity=0;
		if($order_uom==2)
		{
			$order_quantity=$row[csf('order_quantity')]*12;
			//echo $order_quantity.'<br>';
		}
		else {
			$order_quantity=$row[csf('order_quantity')];
			}
			//echo $cbo_shipping.',';
			if($cbo_shipping==2) //Partial
			{
				// echo $chk_order_quantity.'='.$chk_delivery_qty.'<br>';
				if($chk_delivery_qty<$chk_order_quantity) //PO qty is less than Delivery qty
				{
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['status']='Partial Delivery';
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['buyer_name']=$row[csf('party_buyer_name')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['gmts_item_id']=$row[csf('gmts_item_id')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['within_group']=$row[csf('within_group')];
					if($row[csf('remarks')])
					{
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['remarks'].=$row[csf('remarks')].',';
					}
					//$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['delivery_status'].=$delivery_status[$row[csf('delivery_status')]].',';
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['currency_id']=$row[csf('currency_id')];
					if($po_chk_arr[$row[csf('order_id')]]=="")
					{
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['amount']+=$row[csf('amount')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['order_quantity']+=$order_quantity;
					$po_chk_arr[$row[csf('order_id')]]=$row[csf('order_id')];
					}
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['delivery_qty']+=$row[csf('delivery_qty')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['order_uom']=$row[csf('order_uom')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['order_id'].=$row[csf('order_id')].',';
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['color_id'].=$color_library_arr[$row[csf('gmts_color_id')]].',';
					
					$order_id_arr[$row[csf('order_id')]]=$row[csf('order_id')];
					$job_id_arr[$row[csf('job_id')]]=$row[csf('job_id')];
					
					$subcon_job=explode("-",$row[csf('subcon_job')]);
					$subcon_job_prefix=ltrim($subcon_job[3], '0');
					if($row[csf('within_group')]==1)
					{
					$order_no=explode("-",$row[csf('order_no')]);
					$wo_no=ltrim($order_no[3], '0');
					}
					else
					{
						$order_no=$row[csf('order_no')];
						$wo_no=$order_no;
					}
					
					$party_job_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]].=$subcon_job_prefix.',';
					$party_wo_no_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]].=$wo_no.',';
					
					$party_full_job_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]].=$row[csf('subcon_job')].',';
					$party_full_job_id_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]].=$row[csf('job_id')].',';
					$party_full_wo_no_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]].=$row[csf('order_no')].',';
				}
			
			
			} //Partial check end
			else if($cbo_shipping==3) //Full
			{
			
				
				if($chk_delivery_qty>=$chk_order_quantity) //PO qty is equal or greater than Delivery qty
				{
					
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['buyer_name']=$row[csf('party_buyer_name')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['status']='Full Delivery';
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['gmts_item_id']=$row[csf('gmts_item_id')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['within_group']=$row[csf('within_group')];
					if($row[csf('remarks')])
					{
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['remarks'].=$row[csf('remarks')].',';
					}
					//$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['delivery_status'].=$delivery_status[$row[csf('delivery_status')]].',';
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['currency_id']=$row[csf('currency_id')];
					if($po_chk_arr[$row[csf('order_id')]]=="")
					{
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['amount']+=$row[csf('amount')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['order_quantity']+=$order_quantity;
					$po_chk_arr[$row[csf('order_id')]]=$row[csf('order_id')];
					}
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['delivery_qty']+=$row[csf('delivery_qty')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['order_uom']=$row[csf('order_uom')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['order_id'].=$row[csf('order_id')].',';
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['color_id'].=$color_library_arr[$row[csf('gmts_color_id')]].',';
					
					$order_id_arr[$row[csf('order_id')]]=$row[csf('order_id')];
					$job_id_arr[$row[csf('job_id')]]=$row[csf('job_id')];
					
					$subcon_job=explode("-",$row[csf('subcon_job')]);
					$subcon_job_prefix=ltrim($subcon_job[3], '0');
					if($row[csf('within_group')]==1)
					{
					$order_no=explode("-",$row[csf('order_no')]);
					$wo_no=ltrim($order_no[3], '0');
					}
					else
					{
						$order_no=$row[csf('order_no')];
						$wo_no=$order_no;
					}
					
					$party_job_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]].=$subcon_job_prefix.',';
					$party_wo_no_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]].=$wo_no.',';
					
					$party_full_job_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]].=$row[csf('subcon_job')].',';
					$party_full_wo_no_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]].=$row[csf('order_no')].',';
					$party_full_job_id_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]].=$row[csf('job_id')].',';
				}
			
			
			} //Full end
			else  //All 
			{
					if($chk_delivery_qty<$chk_order_quantity) //PO qty is less than Delivery qty
					{
						$status_msg="Partial Delivery";
					}
					else if($chk_delivery_qty>=$chk_order_quantity) //PO qty is less than Delivery qty
					{
						$status_msg="Full Delivery";
					}
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['buyer_name']=$row[csf('party_buyer_name')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['gmts_item_id']=$row[csf('gmts_item_id')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['within_group']=$row[csf('within_group')];
					if($row[csf('remarks')])
					{
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['remarks'].=$row[csf('remarks')].',';
					}
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['status']=$status_msg;
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['currency_id']=$row[csf('currency_id')];
					if($po_chk_arr[$row[csf('order_id')]]=="")
					{
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['amount']+=$row[csf('amount')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['order_quantity']+=$order_quantity;
					$po_chk_arr[$row[csf('order_id')]]=$row[csf('order_id')];
					}
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['delivery_qty']+=$row[csf('delivery_qty')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['order_uom']=$row[csf('order_uom')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['order_id'].=$row[csf('order_id')].',';
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['color_id'].=$color_library_arr[$row[csf('gmts_color_id')]].',';
					
					$order_id_arr[$row[csf('order_id')]]=$row[csf('order_id')];
					$job_id_arr[$row[csf('job_id')]]=$row[csf('job_id')];
					
					$subcon_job=explode("-",$row[csf('subcon_job')]);
					$subcon_job_prefix=ltrim($subcon_job[3], '0');
					if($row[csf('within_group')]==1)
					{
					$order_no=explode("-",$row[csf('order_no')]);
					$wo_no=ltrim($order_no[3], '0');
					}
					else
					{
						$order_no=$row[csf('order_no')];
						$wo_no=$order_no;
					}
					
					$party_job_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]].=$subcon_job_prefix.',';
					$party_wo_no_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]].=$wo_no.',';
					
					$party_full_job_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]].=$row[csf('subcon_job')].',';
					$party_full_wo_no_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]].=$row[csf('order_no')].',';
					$party_full_job_id_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]].=$row[csf('job_id')].',';	
		}
		
		//$style_job_arr[$row[csf('party_id')]][$row[csf('buyer_style_ref')]].=$subcon_job_prefix.',';
		//$style_wo_arr[$row[csf('party_id')]][$row[csf('buyer_style_ref')]].=$wo_no.',';
		
	}
	 
			
	
	  $embl_sql="select c.buyer_style_ref,c.delivery_status,c.gmts_color_id,d.party_id,d.within_group,e.order_id,e.process,e.embellishment_type from subcon_ord_mst d, subcon_ord_dtls c,subcon_ord_breakdown e where   d.id=c.mst_id  and c.id=e.mst_id and e.status_active=1 and d.status_active=1 and c.status_active=1 ".where_con_using_array($order_id_arr,0,'e.mst_id')." ";
			$embl_sql_result=sql_select($embl_sql);	
			$embl_arr=array();
			foreach($embl_sql_result as $row)
			{
				if($row[csf('process')]==1) $process_type=$wash_wet_process;
				else if($row[csf('process')]==2) $process_type=$wash_dry_process;
				else if($row[csf('process')]==3) $process_type=$wash_laser_desing;
				else $process_type=$blank_array;
				if($row[csf('embellishment_type')])
				{
				$embl_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['embl_type'].=$process_type[$row[csf('embellishment_type')]].',';
				}
			}
			unset($embl_sql_result);
			
	
			 $receive_sql="select c.buyer_style_ref,
			sum(case when f.trans_type=1 and f.entry_form=296  then g.quantity else 0 end) as recv_qty,
			sum(case when f.trans_type=1 and f.entry_form=296  then g.quantity else 0 end) as prevtotal_recv_qty
	from subcon_ord_mst d, subcon_ord_dtls c,sub_material_mst f,sub_material_dtls g
	where d.id=c.mst_id and d.entry_form=295  and f.id=g.mst_id  and f.embl_job_no=d.subcon_job  and c.id=g.job_dtls_id  and f.trans_type=1 and f.entry_form=296 and d.status_active=1 and d.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and g.status_active=1 and g.is_deleted=0 ".where_con_using_array($job_id_arr,0,'d.id')."  group by c.buyer_style_ref";
			$receive_sql_result=sql_select($receive_sql);	
			$receive_arr=array();
			foreach($receive_sql_result as $row)
			{
				$receive_arr[$row[csf('buyer_style_ref')]]['recv_qty']=$row[csf('recv_qty')];
			}
			
			unset($receive_sql_result);
			
			$pi_sql="select  c.buyer_style_ref,b.pi_number,
			avg(a.rate) as pi_rate
			from com_export_pi_dtls a,com_export_pi_mst b,subcon_ord_dtls c
			where b.id=a.pi_id  and c.id=a.work_order_dtls_id  and b.entry_form = 152  and b.item_category_id=37 and a.rate>0  ".where_con_using_array($order_id_arr,0,'c.id')." group by c.buyer_style_ref,b.pi_number"; 

			$pi_result = sql_select($pi_sql);
			$pi_arr=array();
			foreach($pi_result as $row)
			{
				$pi_arr[$row[csf('buyer_style_ref')]]['pi_rate']=$row[csf('pi_rate')];
				$pi_no_arr[$row[csf('buyer_style_ref')]]['pi_number'].=$row[csf('pi_number')].',';
			}
		  unset($pi_result);
		
		 
		
	ob_start();
	?>
     <style type="text/css">
		.wrd_brk{word-break: break-all;}
		 #td_color{ background:#FFFF33;}
	</style>
     <fieldset style="width:1740px;">
     <? //if($cbo_within_group==1){ ?>
     <div style="width:1740px; margin:2 auto;">
          <table cellpadding="0" cellspacing="0" width="1740">
         		<tr  class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="12" style="font-size:20px"><strong><? echo str_replace("'","",$report_title); ?></strong></td>
                </tr>
                <tr  class="form_caption" style="border:none;">
                    <td colspan="12" align="center" style="border:none; font-size:14px;">
                        <b><? echo $company_library[$company_id]; ?></b>
                    </td>
                </tr>
                <tr  class="form_caption" style="border:none;">
                    <td align="center" width="100%" colspan="12" style="font-size:12px">
                        <? if(str_replace("'","",$txt_date_from)!="") echo "Date &nbsp;".change_date_format(str_replace("'","",$txt_date_from),'dd-mm-yyyy').' &nbsp; To &nbsp;'.change_date_format(str_replace("'","",$txt_date_to),'dd-mm-yyyy');?>
                    </td>
                </tr>
            </table>
            <?
			$garnd_total_order_qty=$garnd_total_order_amount=$garnd_total_recv_qty=$garnd_total_delivery_qty=$garnd_total_revenue=$garnd_total_access_shortage_per=$garnd_total_access_shortage_val=$garnd_delivery_amount=$garnd_total_access_shortage_qty=0;
			$p=1;$buyer_summary_arr=array();
           foreach($style_party_wise_arr as $party_id=>$partyArr)
			{
				$company_party_arr=explode('_',$party_id);
			?>
            <div style="width:1740px;">
            <table width="1740" border="1" cellpadding="0" cellspacing="0" rules="all"  class="rpt_table">
            <tr>
            <td colspan="19"  title="<? echo ($company_party_arr[0]==1) ? 'Within Group-Yes' : 'Within Group-No';?>" ><p><b style="float:left"> Customer Name: <? 
				if($company_party_arr[0]==1){
					echo $company_library[$company_party_arr[1]];
				}
				if($company_party_arr[0]==2){
					echo $party_arr[$company_party_arr[1]];
				}
			?></b></p> </td>
            </tr>
            <tr>
            <td colspan="7"><p style="float:left"><b> Wash Job No: <?  $wash_job=rtrim($party_job_arr[$party_id],',');  $wash_jobArr=implode(",",array_unique(explode(",",$wash_job)));echo $wash_jobArr;?></b></p> </td>  
            
            <td colspan="11">
           <p><b style="float:left">  W/O No: 
            <? $wash_wo=rtrim($party_wo_no_arr[$party_id],','); $wash_woArr=implode(",",array_unique(explode(",",$wash_wo)));
			echo $wash_woArr;
			?>
            </b>
            </p>
             </td>
            </tr>
            
            </table>
            </div>
            <table width="1740" border="1" cellpadding="0" cellspacing="0" rules="all"  class="rpt_table">
            	<caption style="border:solid 0px;">
                </caption>
                <thead>
                        <th width="20">SL#</th>
                        <th width="100">Buyer</th>
                        <th width="100">Style Name</th>
                        <th width="100">Color</th>
                        <th width="100">Garments Item</th>
                        <th width="120">Type of Wash</th>
                        <th width="100">UOM</th>
                        <th width="100">Currency</th>
                        <th width="80">Order Qty.</th>
                        <th width="80" title="Amount/Order Qty">WO Rate</th>
                        <th width="80">Order Value</th>
                        <th width="80">Received Qty.</th>
                        <th width="80">Delivery Qty.</th>
                        <th width="80">PI Rate</th>
                        <th width="80" title="PI Rate*Delivery Qty">Revenue</th>
                        <th width="80" title="Delivery Qty-Order Qty">Excess/<br>(Shortage) Qty.</th>
                        <th width="80">Excess/<br>(Shortage)%</th>
                        <th width="80">Excess/<br>(Shortage)<br>Value</th>
                        <th width="80">Delivery Status</th>
                        <th width="">Remarks</th>
                </thead>
            </table>
             <div style="max-height:300px; overflow-y:scroll; width:1740px" id="scroll_body">
             <table width="1720" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_body<? //echo $p;?>">
				<?  
					$i=1;
					$total_order_qty=$total_order_amount=$total_recv_qty=$total_delivery_qty=$total_revenue=$total_access_shortage_per=$total_access_shortage_val=$total_delivery_amount=0;$total_access_shortage_qty=0;
					foreach($partyArr as $style_no=>$row)
					{
						 
						if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF"; 
						$order_uom=$row[("order_uom")];
						/*$delivery_status_id=$row[("delivery_status")];
						$delivery_statusArr=rtrim($row[("delivery_status")],',');
						$delivery_Status=implode(", ",array_unique(explode(",",$delivery_statusArr)));
						
						if($delivery_statusArr)
						{
							$deliveryStatus=$delivery_Status;
						}
						else
						{
							$deliveryStatus=$row[("status")];
						}*/
						$deliveryStatus=$row[("status")];
						$order_id=rtrim($row[("order_id")],',');
						$order_idArr=array_unique(explode(",",$order_id));
						$recv_qty=0;
						$recv_qty=$receive_arr[$style_no]['recv_qty'];
						/*foreach($order_idArr as $oId)
						{
						$recv_qty+=$receive_arr[$style_no]['recv_qty'];
						}*/
						//$currency_id=$currency_id[$row[csf("currency_id")]];
						$order_quantity=$job_style_arr[$style_no]['po_qty'];
						$po_amt=$job_style_arr[$style_no]['po_amt'];
						/*if($order_uom==2)
						{
							$order_quantity=$row[("order_quantity")]*12;
						}*/
						$embl_typeArr=rtrim($embl_arr[$party_id][$style_no]['embl_type'],',');
						$remarksArr=rtrim($row[("remarks")],',');
						$remarks=implode(", ",array_unique(explode(",",$remarksArr)));
						
						
						
						$party_job=rtrim($party_full_job_arr[$party_id][$style_no],',');
						$party_jobAll=implode(", ",array_unique(explode(",",$party_job)));	
						
						$party_job_id=rtrim($party_full_job_id_arr[$party_id][$style_no],',');
						$party_job_id=implode(",",array_unique(explode(",",$party_job_id)));
						
						$pi_no=rtrim($pi_no_arr[$style_no]['pi_number'],',');
						$pi_nos=implode(", ",array_unique(explode(",",$pi_no)));
						$party_wo_no=rtrim($party_full_wo_no_arr[$party_id][$style_no],',');
						$party_woAll=implode(", ",array_unique(explode(",",$party_wo_no)));
						//$party_jobAll=;
						$tool_tip='JobNo='.$party_jobAll.', Wo No= '.$party_woAll;
						?>
                          <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i.$p; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i.$p; ?>">
                            <td  width="20" id="wrd_brk"><? echo $i; ?></td>
                            <td width="100" id="wrd_brk"><? echo $row[("buyer_name")]; ?></td>
                            <td  width="100" id="wrd_brk" title="<? echo $tool_tip;?>"> <? echo $style_no; ?></td>
                            <td  width="100" id="wrd_brk"><? $color=rtrim($row[("color_id")],',');$colors=implode(", ",array_unique(explode(",",$color)));echo $colors; ?></td>
                            <td  width="100" id="wrd_brk"><? echo $garments_item[$row[("gmts_item_id")]]; ?></td>
                            <td width="120" id="wrd_brk"><?  $embl_types=implode(", ",array_unique(explode(",",$embl_typeArr))); echo $embl_types; ?></td> 
                            <td  width="100" id="wrd_brk"><? echo 'Pcs'; ?></td>
                            <td  width="100" id="wrd_brk" align="center"><?php echo $currency[$row[("currency_id")]];; ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo number_format($order_quantity,0); ?></td>
                            <td  width="80" id="wrd_brk" align="right" title="Avg Rate=<? echo $po_amt/$order_quantity;?>"><?php echo number_format($po_amt/$order_quantity,4);?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo number_format($po_amt,2); ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo number_format($recv_qty,0); ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo number_format($row[("delivery_qty")],0); ?></td>
                            <td  width="80" id="wrd_brk" align="right" title="PI Rate=<? echo $pi_arr[$style_no]['pi_rate'].', PI No='.$pi_nos;?>"><?php echo number_format($pi_arr[$style_no]['pi_rate'],4); ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php
								$delivery_amount=$row[("delivery_qty")]*$pi_arr[$style_no]['pi_rate'];
								echo number_format($delivery_amount,2);
							?></td>
                             <td  width="80" id="wrd_brk" title="" align="right"><?php //$revenue=$order_quantity*$pi_arr[$style_no]['pi_rate'];
							 $access_shortage_qty=($row[("delivery_qty")]-$order_quantity);
							 echo number_format($access_shortage_qty,0); ?></td>
                             <?
							 $access_shortage_per=($row[("delivery_qty")]-$order_quantity)/$order_quantity*100;
							  
							 $bg_color="";
                             if($access_shortage_per>=5)
							 {
								  $bg_color="#FFFF33";
							 }
							 ?>
                             <td  width="80" id="wrd_brk" bgcolor="<? echo $bg_color;?>"  title="Delivery Qty-Order Qty/Order Qty*100" align="right"><?php echo number_format($access_shortage_per,2).'%'; ?></td>
                             <td  width="80" id="wrd_brk" title="Delivery Qty-Order Qty*Pi Rate" align="right"><?php   $access_shortage_val=($row[("delivery_qty")]-$order_quantity)*$pi_arr[$style_no]['pi_rate'];echo number_format($access_shortage_val,2); ?></td>
                             <td  width="80" id="wrd_brk" align="center"><?php echo $deliveryStatus;//$delivery_status; ?></td>
                                
                            <td id="wrd_brk" align="center"><p><a href="##" onClick="report_po_popup('<? echo $company_id; ?>','<? echo $style_no; ?>','<? echo $party_job_id; ?>','show_remark_popup',1)"><? if($remarks) echo "View";else echo " "; ?></a><?php //echo $remarks; ?></p></td>
						  </tr>
						<?	
						$total_order_qty+=$order_quantity;	
						$total_order_amount+=$po_amt;
						$total_delivery_qty+=$row[("delivery_qty")];
						$total_recv_qty+=$recv_qty;
						$total_delivery_amount+=$delivery_amount;
						$total_access_shortage_qty+=$access_shortage_qty;
						$total_access_shortage_per+=$access_shortage_per;
						$total_access_shortage_val+=$access_shortage_val;
						//========================Buyer Summary===========
						$buyer_summary_arr[$row[("buyer_name")]]['uom']='Pcs';
						$buyer_summary_arr[$row[("buyer_name")]]['currency_id']=$row[("currency_id")];
						$buyer_summary_arr[$row[("buyer_name")]]['orderQty']+=$order_quantity;
						$buyer_summary_arr[$row[("buyer_name")]]['orderValue']+=$po_amt;
						$buyer_summary_arr[$row[("buyer_name")]]['recv_qty']+=$recv_qty;
						$buyer_summary_arr[$row[("buyer_name")]]['delivery_qty']+=$row[("delivery_qty")];
						//$buyer_summary_arr[$row[("buyer_name")]]['delivery_qty']+=$pi_arr[$style_no]['pi_rate'];
						$buyer_summary_arr[$row[("buyer_name")]]['delivery_amt']+=$delivery_amount;
						$buyer_summary_arr[$row[("buyer_name")]]['excess_short_qty']+=($row[("delivery_qty")]-$order_quantity);
						$buyer_summary_arr[$row[("buyer_name")]]['excess_short_val']+=$access_shortage_val;
						 
						
						
					$i++;
					}
                  ?>
                </table>
         	</div>
         	<table width="1720" border="1" cellpadding="0" cellspacing="0" rules="all"> 
			<tr class="tbl_bottom">
                <td width="20" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td  width="120" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td  width="100" >Total:</td>
                <td  width="80" ><? echo number_format($total_order_qty,0); ?></td>
                <td width="80" id=""></td>
                <td width="80" id=""><? echo number_format($total_order_amount,2); ?></td>
                <td width="80" id=""><? echo number_format($total_recv_qty,0); ?></td>
                <td width="80" id=""><? echo number_format($total_delivery_qty,0); ?></td>
              
               <td width="80" id=""></td>
               <td width="80" id=""><? echo number_format($total_delivery_amount,2); ?></td>
               <td width="80" id=""><? echo number_format($total_access_shortage_qty,0); ?></td>
               <?
			   $tot_access_shortage_per=($total_delivery_qty-$total_order_qty)/$total_order_qty*100;
			   
               $tot_bg_color=""; $td_id="";
				 if($tot_access_shortage_per>=5)
				 {
					  $tot_bg_color="#FFFF33";
					  $td_id='id="td_color"';
				 }
			   ?>
               <td width="80"  <? echo $td_id;?>   ><? echo number_format($tot_access_shortage_per,2); ?>%</td>
                <td width="80" id=""><? echo number_format($total_access_shortage_val,0); ?></td>
                <td width="80" id=""></td>
                <td id=""></td>
			</tr>
		</table> 
        <?
						$garnd_total_order_qty+=$total_order_qty;
						$garnd_total_order_amount+=$total_order_amount;
						$garnd_total_recv_qty+=$total_recv_qty;
						$garnd_total_delivery_qty+=$total_delivery_qty;	
						$garnd_total_access_shortage_qty+=$total_access_shortage_qty;
						$garnd_delivery_amount+=$total_delivery_amount;
						$garnd_total_access_shortage_per+=$total_access_shortage_per;
						$garnd_total_access_shortage_val+=$total_access_shortage_val;
$p++;
			}
		?>
        <table width="1720" border="1" cellpadding="0" cellspacing="0" rules="all"> 
			<tr class="tbl_bottom">
                <td  width="20" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td width="120" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td  width="100" >Grand Total:</td>
                <td  width="80" ><? echo number_format($garnd_total_order_qty,0); ?></td>
                <td width="80" id=""></td>
                <td width="80" id=""><? echo number_format($garnd_total_order_amount,2); ?></td>
                <td width="80" id=""><? echo number_format($garnd_total_recv_qty,0); ?></td>
                <td width="80" id=""><? echo number_format($garnd_total_delivery_qty,0); ?></td>
                <td width="80" id=""></td>
                <td width="80" id=""><? echo number_format($garnd_delivery_amount,2); ?></td>
                <td width="80" id=""><? echo number_format($garnd_total_access_shortage_qty,2); ?></td>
                <td width="80" id=""><? //echo number_format($garnd_total_access_shortage_per,0); ?></td>
                <td width="80" id=""><? echo number_format($garnd_total_access_shortage_val,0); ?></td>
                <td width="80" id=""></td>
                <td id=""></td>
			</tr>
		</table> 
        <br>
         <table width="1040" border="1" cellpadding="0" cellspacing="0" rules="all"  class="rpt_table">
           <caption style="border:solid 0px;">  <b style="float:left">Buyer wise Summary </b></caption>
                <thead>
                        <th width="20">SL#</th>
                        <th width="100">Buyer</th>
                        <th width="100">UOM</th>
                        <th width="100">Currency</th>
                        <th width="100">Order Qty.</th>
                       
                        <th width="100">Order Value</th>
                        <th width="100">Received Qty.</th>
                        <th width="80">Delivery Qty.</th>
                       
                        <th width="80">Revenue</th>
                        <th width="80" title="Delivery Qty-Order Qty">Excess/<br>(Shortage) Qty.</th>
                        <th width="80">Excess/<br>(Shortage)%</th>
                        <th width="">Excess/<br>(Shortage)<br>Value</th>
                       
                </thead>
            </table>
             <div style="max-height:300px; overflow-y:scroll; width:1060px" id="scroll_body">
             <table width="1040" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_body<? //echo $p;?>">
             <? 
				 $buyer_total_order_qty=$buyer_total_order_val=$buyer_total_recv_qty=$buyer_total_delivery_qty=$buyer_total_access_shortage_qty=$buyer_total_access_shortage_val=0;
				 $b=1;
             		foreach($buyer_summary_arr as $buyer=>$row)
					{
						 
						if ($b%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF"; 
						//$order_uom=$row[("order_uom")];
                        $revenue_amount=$row[("delivery_amt")];
                        ?>
               <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trbuyer_<? echo $b; ?>','<? echo $bgcolor; ?>')" id="trbuyer_<? echo $b; ?>">
                            <td  width="20" id="wrd_brk"><? echo $b; ?></td>
                            <td width="100" id="wrd_brk"><? echo $buyer; ?></td>
                            <td  width="100" id="wrd_brk"><? echo 'Pcs'; ?></td>
                            <td  width="100" id="wrd_brk" align="center"><?php echo $currency[$row[("currency_id")]]; ?></td>
                            <td  width="100" id="wrd_brk" align="right"><?php echo number_format($row[("orderQty")],0); ?></td>
                             
                            <td  width="100" id="wrd_brk" align="right"><?php echo number_format($row[("orderValue")],2); ?></td>
                            <td  width="100" id="wrd_brk" align="right"><?php echo number_format($row[("recv_qty")],0); ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo number_format($row[("delivery_qty")],0); ?></td>
                           
                            <td  width="80" id="wrd_brk" align="right"><?php
								
								echo number_format($revenue_amount,2);
							?></td>
                             <td  width="80" id="wrd_brk" title="" align="right"><?php //$revenue=$order_quantity*$pi_arr[$style_no]['pi_rate'];
							 $access_shortage_qty=($row[("delivery_qty")]-$row[("orderQty")]);
							 echo number_format($access_shortage_qty,0); ?></td>
                             <?
							 $buyer_access_shortage_per=($row[("delivery_qty")]-$row[("orderQty")])/$row[("orderQty")]*100;
							 
							 $buyer_bg_color="";
                             if($buyer_access_shortage_per>=5)
							 {
								  $buyer_bg_color="#FFFF33";
							 }
							 ?>
                             <td  width="80" id="wrd_brk" bgcolor="<? echo $buyer_bg_color;?>"  title="Delivery Qty-Order Qty/Order Qty*100" align="right"><?php 
							 echo number_format($buyer_access_shortage_per,2).'%'; ?></td>
                             <td  width="" id="wrd_brk" title="Delivery Qty-Order Qty*Pi Rate" align="right"><?php  
							  $access_shortage_val=$row[("excess_short_val")];echo number_format($access_shortage_val,2); ?></td>
                             
                </tr>
                <?
					
					$b++;
					$buyer_total_order_qty+=$row[("orderQty")];
					$buyer_total_order_val+=$row[("orderValue")];
					$buyer_total_recv_qty+=$row[("recv_qty")];
					$buyer_total_delivery_qty+=$row[("delivery_qty")];
					$buyer_total_revenue_amount+=$revenue_amount;
					$buyer_total_access_shortage_qty+=$access_shortage_qty;
					$buyer_total_access_shortage_val+=$access_shortage_val;
					
					}
				?>
             </table>
        
   	  </div>
      	<table width="1040" border="1" cellpadding="0" cellspacing="0" rules="all"> 
			<tr class="tbl_bottom">
                <td width="20" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td  width="100" >Total:</td>
                <td  width="100" ><? echo number_format($buyer_total_order_qty,0); ?></td>
               
                <td  width="100" ><? echo number_format($buyer_total_order_val,2); ?></td>
                <td  width="100" ><? echo number_format($buyer_total_recv_qty,0); ?></td>
              
                <td width="80" id=""><? echo number_format($buyer_total_delivery_qty,0); ?></td>
                
                
                <td width="80" id=""><? echo number_format($buyer_total_revenue_amount,0); ?></td>
                <?
              
				?>
              
               <td width="80"><? echo number_format($buyer_total_access_shortage_qty,0); ?></td>
               <?
			    $tot_buyer_access_shortage_per=($buyer_total_delivery_qty-$buyer_total_order_qty)/$buyer_total_order_qty*100;
				 $tot_buyer_bg_color="";$td_id_buyer='';
				 if($tot_buyer_access_shortage_per>=5)
				 {
					  $tot_buyer_bg_color="#FFFF33";
					   $td_id_buyer='id="td_color"';
				 }
			   ?>
               <td width="80" <? echo $td_id_buyer;?> ><? echo number_format($tot_buyer_access_shortage_per,2); ?>%</td>
               <td width="" id=""><? echo number_format($buyer_total_access_shortage_val,0); ?></td>
              
			</tr>
		</table> 
   
 <? } //Show End 
   else if($report_type==2)// ======Show2 Button=======
   {	
	 $delivery_sql="select d.id as job_id,d.subcon_job,d.party_id,d.order_no,d.within_group,d.order_no,d.currency_id, d.exchange_rate,c.party_buyer_name,c.id as order_id,c.order_uom,c.buyer_style_ref,c.delivery_status,c.gmts_color_id,c.amount,f.remarks, c.amount_domestic,c.order_quantity,c.gmts_item_id,g.delivery_qty
from subcon_ord_mst d, subcon_ord_dtls c,subcon_delivery_mst f,subcon_delivery_dtls g
where d.id=c.mst_id  and f.id=g.mst_id  and f.job_no=d.subcon_job  and c.id=g.order_id and f.entry_form=303 and d.entry_form=295 and d.status_active=1 and c.status_active=1 and f.status_active=1 and g.status_active=1 ".where_con_using_array($job_id_arr,0,'d.id')."   order by d.party_id  ";
	$delivery_sql_result=sql_select($delivery_sql);
	foreach($delivery_sql_result as $row)
	{
		$style_wise_chk_arr[$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['delivery_qty']+=$row[csf('delivery_qty')];
		$style_wise_chk_delivery_arr[$row[csf('buyer_style_ref')]]['delivery_qty']+=$row[csf('delivery_qty')];
	}
			
	 $job_sql2="select  d.subcon_job,d.party_id,c.order_uom,c.buyer_style_ref,c.order_quantity,c.amount
	from subcon_ord_mst d, subcon_ord_dtls c
	where d.id=c.mst_id and d.entry_form=295 and d.status_active=1 and d.is_deleted=0  and c.status_active=1 and c.is_deleted=0 ".where_con_using_array($job_id_arr,0,'d.id')." ";
			$job_sql_result2=sql_select($job_sql2);	
			$receive_arr=array();
			foreach($job_sql_result2 as $row)
			{
				$order_uom=$row[csf('order_uom')];
				$order_quantity=0;
				if($order_uom==2)
				{
					$order_quantity=$row[csf('order_quantity')]*12;
					//echo $order_quantity.'<br>';
				}
				else {
					$order_quantity=$row[csf('order_quantity')];
					}
			
				$job_style_arr[$row[csf('buyer_style_ref')]]['po_qty']+=$order_quantity;
				$job_style_arr[$row[csf('buyer_style_ref')]]['po_amt']+=$row[csf('amount')];
				//echo $order_quantity.'DD';
				$style_wise_chk_arr[$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['order_quantity']+=$order_quantity;
			}
			//print_r($style_wise_chk_arr);
			//unset($job_sql_result);
			
	$po_chk_arr=array();
	foreach($job_sql_result as $row)
	{
		 $order_uom=$row[csf('order_uom')];
		 $chk_order_quantity=$style_wise_chk_arr[$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['order_quantity'];
		 $chk_delivery_qty=$style_wise_chk_arr[$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['delivery_qty'];
		
		$order_quantity=0;
		if($order_uom==2)
		{
			$order_quantity=$row[csf('order_quantity')]*12;
			//echo $order_quantity.'<br>';
		}
		else {
			$order_quantity=$row[csf('order_quantity')];
			}
			//echo $cbo_shipping.',';
			if($cbo_shipping==2) //Partial
			{
				// echo $chk_order_quantity.'='.$chk_delivery_qty.'<br>';
				if($chk_delivery_qty<$chk_order_quantity) //PO qty is less than Delivery qty
				{
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['status']='Partial Delivery';
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['buyer_name']=$row[csf('party_buyer_name')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['gmts_item_id']=$row[csf('gmts_item_id')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['within_group']=$row[csf('within_group')];
					if($row[csf('remarks')])
					{
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['remarks'].=$row[csf('remarks')].',';
					}
					//$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['delivery_status'].=$delivery_status[$row[csf('delivery_status')]].',';
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['currency_id']=$row[csf('currency_id')];
					if($po_chk_arr[$row[csf('order_id')]]=="")
					{
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['amount']+=$row[csf('amount')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['order_quantity']+=$order_quantity;
					$po_chk_arr[$row[csf('order_id')]]=$row[csf('order_id')];
					}
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['delivery_qty']+=$row[csf('delivery_qty')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['order_uom']=$row[csf('order_uom')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['order_id'].=$row[csf('order_id')].',';
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['color_id'].=$color_library_arr[$row[csf('gmts_color_id')]].',';
					
					$order_id_arr[$row[csf('order_id')]]=$row[csf('order_id')];
					$job_id_arr[$row[csf('job_id')]]=$row[csf('job_id')];
					
					$subcon_job=explode("-",$row[csf('subcon_job')]);
					$subcon_job_prefix=ltrim($subcon_job[3], '0');
					if($row[csf('within_group')]==1)
					{
					$order_no=explode("-",$row[csf('order_no')]);
					$wo_no=ltrim($order_no[3], '0');
					}
					else
					{
						$order_no=$row[csf('order_no')];
						$wo_no=$order_no;
					}
					
					$party_job_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]].=$subcon_job_prefix.',';
					$party_wo_no_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]].=$wo_no.',';
					
					$party_full_job_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]].=$row[csf('subcon_job')].',';
					$party_full_job_id_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]].=$row[csf('job_id')].',';
					$party_full_wo_no_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]].=$row[csf('order_no')].',';
				}
			
			
			} //Partial check end
			else if($cbo_shipping==3) //Full
			{
			
				
				if($chk_delivery_qty>=$chk_order_quantity) //PO qty is equal or greater than Delivery qty
				{
					
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['buyer_name']=$row[csf('party_buyer_name')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['status']='Full Delivery';
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['gmts_item_id']=$row[csf('gmts_item_id')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['within_group']=$row[csf('within_group')];
					if($row[csf('remarks')])
					{
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['remarks'].=$row[csf('remarks')].',';
					}
					//$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['delivery_status'].=$delivery_status[$row[csf('delivery_status')]].',';
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['currency_id']=$row[csf('currency_id')];
					if($po_chk_arr[$row[csf('order_id')]]=="")
					{
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['amount']+=$row[csf('amount')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['order_quantity']+=$order_quantity;
					$po_chk_arr[$row[csf('order_id')]]=$row[csf('order_id')];
					}
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['delivery_qty']+=$row[csf('delivery_qty')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['order_uom']=$row[csf('order_uom')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['order_id'].=$row[csf('order_id')].',';
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['color_id'].=$color_library_arr[$row[csf('gmts_color_id')]].',';
					
					$order_id_arr[$row[csf('order_id')]]=$row[csf('order_id')];
					$job_id_arr[$row[csf('job_id')]]=$row[csf('job_id')];
					
					$subcon_job=explode("-",$row[csf('subcon_job')]);
					$subcon_job_prefix=ltrim($subcon_job[3], '0');
					if($row[csf('within_group')]==1)
					{
					$order_no=explode("-",$row[csf('order_no')]);
					$wo_no=ltrim($order_no[3], '0');
					}
					else
					{
						$order_no=$row[csf('order_no')];
						$wo_no=$order_no;
					}
					
					$party_job_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]].=$subcon_job_prefix.',';
					$party_wo_no_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]].=$wo_no.',';
					
					$party_full_job_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]].=$row[csf('subcon_job')].',';
					$party_full_wo_no_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]].=$row[csf('order_no')].',';
					$party_full_job_id_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]].=$row[csf('job_id')].',';
				}
			
			
			} //Full end
			else  //All 
			{
					if($chk_delivery_qty<$chk_order_quantity) //PO qty is less than Delivery qty
					{
						$status_msg="Partial Delivery";
					}
					else if($chk_delivery_qty>=$chk_order_quantity) //PO qty is less than Delivery qty
					{
						$status_msg="Full Delivery";
					}
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['buyer_name']=$row[csf('party_buyer_name')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['gmts_item_id']=$row[csf('gmts_item_id')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['within_group']=$row[csf('within_group')];
					if($row[csf('remarks')])
					{
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['remarks'].=$row[csf('remarks')].',';
					}
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['status']=$status_msg;
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['currency_id']=$row[csf('currency_id')];
					if($po_chk_arr[$row[csf('order_id')]]=="")
					{
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['amount']+=$row[csf('amount')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['order_quantity']+=$order_quantity;
					$po_chk_arr[$row[csf('order_id')]]=$row[csf('order_id')];
					}
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['delivery_qty']+=$row[csf('delivery_qty')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['order_uom']=$row[csf('order_uom')];
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['order_id'].=$row[csf('order_id')].',';
					$style_party_wise_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['color_id'].=$color_library_arr[$row[csf('gmts_color_id')]].',';
					
					$order_id_arr[$row[csf('order_id')]]=$row[csf('order_id')];
					$job_id_arr[$row[csf('job_id')]]=$row[csf('job_id')];
					
					$subcon_job=explode("-",$row[csf('subcon_job')]);
					$subcon_job_prefix=ltrim($subcon_job[3], '0');
					if($row[csf('within_group')]==1)
					{
					$order_no=explode("-",$row[csf('order_no')]);
					$wo_no=ltrim($order_no[3], '0');
					}
					else
					{
						$order_no=$row[csf('order_no')];
						$wo_no=$order_no;
					}
					
					$party_job_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]].=$subcon_job_prefix.',';
					$party_wo_no_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]].=$wo_no.',';
					
					$party_full_job_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]].=$row[csf('subcon_job')].',';
					$party_full_wo_no_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]].=$row[csf('order_no')].',';
					$party_full_job_id_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]].=$row[csf('job_id')].',';	
		}
		
		//$style_job_arr[$row[csf('party_id')]][$row[csf('buyer_style_ref')]].=$subcon_job_prefix.',';
		//$style_wo_arr[$row[csf('party_id')]][$row[csf('buyer_style_ref')]].=$wo_no.',';
		
	}
	 
			
	
	  $embl_sql="select c.buyer_style_ref,c.delivery_status,c.gmts_color_id,d.party_id,d.within_group,e.order_id,e.process,e.embellishment_type from subcon_ord_mst d, subcon_ord_dtls c,subcon_ord_breakdown e where   d.id=c.mst_id  and c.id=e.mst_id and e.status_active=1 and d.status_active=1 and c.status_active=1 ".where_con_using_array($order_id_arr,0,'e.mst_id')." ";
			$embl_sql_result=sql_select($embl_sql);	
			$embl_arr=array();
			foreach($embl_sql_result as $row)
			{
				if($row[csf('process')]==1) $process_type=$wash_wet_process;
				else if($row[csf('process')]==2) $process_type=$wash_dry_process;
				else if($row[csf('process')]==3) $process_type=$wash_laser_desing;
				else $process_type=$blank_array;
				if($row[csf('embellishment_type')])
				{
				$embl_arr[$row[csf('within_group')].'_'.$row[csf('party_id')]][$row[csf('buyer_style_ref')]]['embl_type'].=$process_type[$row[csf('embellishment_type')]].',';
				}
			}
			unset($embl_sql_result);
			
	
			 $receive_sql="select c.buyer_style_ref,
			sum(case when f.trans_type=1 and f.entry_form=296  then g.quantity else 0 end) as recv_qty,
			sum(case when f.trans_type=1 and f.entry_form=296  then g.quantity else 0 end) as prevtotal_recv_qty
	from subcon_ord_mst d, subcon_ord_dtls c,sub_material_mst f,sub_material_dtls g
	where d.id=c.mst_id and d.entry_form=295  and f.id=g.mst_id  and f.embl_job_no=d.subcon_job  and c.id=g.job_dtls_id  and f.trans_type=1 and f.entry_form=296 and d.status_active=1 and d.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and g.status_active=1 and g.is_deleted=0 ".where_con_using_array($job_id_arr,0,'d.id')."  group by c.buyer_style_ref";
			$receive_sql_result=sql_select($receive_sql);	
			$receive_arr=array();
			foreach($receive_sql_result as $row)
			{
				$receive_arr[$row[csf('buyer_style_ref')]]['recv_qty']=$row[csf('recv_qty')];
			}
			
			unset($receive_sql_result);
			
			$pi_sql="select  c.buyer_style_ref,b.pi_number,
			avg(a.rate) as pi_rate
			from com_export_pi_dtls a,com_export_pi_mst b,subcon_ord_dtls c
			where b.id=a.pi_id  and c.id=a.work_order_dtls_id  and b.entry_form = 152  and b.item_category_id=37 and a.rate>0  ".where_con_using_array($order_id_arr,0,'c.id')." group by c.buyer_style_ref,b.pi_number"; 

			$pi_result = sql_select($pi_sql);
			$pi_arr=array();
			foreach($pi_result as $row)
			{
				$pi_arr[$row[csf('buyer_style_ref')]]['pi_rate']=$row[csf('pi_rate')];
				$pi_no_arr[$row[csf('buyer_style_ref')]]['pi_number'].=$row[csf('pi_number')].',';
			}
		  unset($pi_result);
		
		 
		
	ob_start();
	?>
     <style type="text/css">
		.wrd_brk{word-break: break-all;}
		 #td_color{ background:#FFFF33;}
	</style>
     <fieldset style="width:1820px;">
     <? //if($cbo_within_group==1){ ?>
     <div style="width:1740px; margin:2 auto;">
          <table cellpadding="0" cellspacing="0" width="1740">
         		<tr  class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="13" style="font-size:20px"><strong><? echo str_replace("'","",$report_title); ?></strong></td>
                </tr>
                <tr  class="form_caption" style="border:none;">
                    <td colspan="13" align="center" style="border:none; font-size:14px;">
                        <b><? echo $company_library[$company_id]; ?></b>
                    </td>
                </tr>
                <tr  class="form_caption" style="border:none;">
                    <td align="center" width="100%" colspan="13" style="font-size:12px">
                        <? if(str_replace("'","",$txt_date_from)!="") echo " &nbsp;Date&nbsp;".change_date_format(str_replace("'","",$txt_date_from),'dd-mm-yyyy').' To '.change_date_format(str_replace("'","",$txt_date_to),'dd-mm-yyyy');?>
                    </td>
                </tr>
            </table>
            <?
			$garnd_total_order_qty=$garnd_total_order_amount=$garnd_total_recv_qty=$garnd_total_delivery_qty=$garnd_total_revenue=$garnd_total_access_shortage_per=$garnd_total_access_shortage_val=$garnd_delivery_amount=$garnd_total_access_shortage_qty=$tot_garnd_total_delivery_qty=0;
			$p=1;$buyer_summary_arr=array();
           foreach($style_party_wise_arr as $party_id=>$partyArr)
			{
				$company_party_arr=explode('_',$party_id);
			?>
            <div style="width:1820px;">
            <table width="1820" border="1" cellpadding="0" cellspacing="0" rules="all"  class="rpt_table">
            <tr>
            <td colspan="19"  title="<? echo ($company_party_arr[0]==1) ? 'Within Group-Yes' : 'Within Group-No';?>" ><p><b style="float:left"> Customer Name: <? 
				if($company_party_arr[0]==1){
					echo $company_library[$company_party_arr[1]];
				}
				if($company_party_arr[0]==2){
					echo $party_arr[$company_party_arr[1]];
				}
			?></b></p> </td>
            </tr>
            <tr>
            <td colspan="8"><p style="float:left"><b> Wash Job No: <?  $wash_job=rtrim($party_job_arr[$party_id],',');  $wash_jobArr=implode(",",array_unique(explode(",",$wash_job)));echo $wash_jobArr;?></b></p> </td>  
            
            <td colspan="11">
           <p><b style="float:left">  W/O No: 
            <? $wash_wo=rtrim($party_wo_no_arr[$party_id],','); $wash_woArr=implode(",",array_unique(explode(",",$wash_wo)));
			echo $wash_woArr;
			?>
            </b>
            </p>
             </td>
            </tr>
            
            </table>
            </div>
            <table width="1820" border="1" cellpadding="0" cellspacing="0" rules="all"  class="rpt_table">
            	<caption style="border:solid 0px;">
                </caption>
                <thead>
                        <th width="20">SL#</th>
                        <th width="100">Buyer</th>
                        <th width="100">Style Name</th>
                        <th width="100">Color</th>
                        <th width="100">Garments Item</th>
                        <th width="120">Type of Wash</th>
                        <th width="100">UOM</th>
                        <th width="100">Currency</th>
                        <th width="80">Order Qty.</th>
                        <th width="80" title="Amount/Order Qty">WO Rate</th>
                        <th width="80">Order Value</th>
                        <th width="80">Received Qty.</th>
                        <th width="80">Delivery Qty.</th>
                        <th width="80">PI Rate</th>
                        <th width="80" title="PI Rate*Delivery Qty">Revenue</th>
                        <th width="80" title="">Total Delivery Qty.</th>
                        <th width="80" title="Total Delivery Qty-Order Qty">Excess/<br>(Shortage) Qty.</th>
                        <th width="80">Excess/<br>(Shortage)%</th>
                        <th width="80">Excess/<br>(Shortage)<br>Value</th>
                        <th width="80">Delivery Status</th>
                        <th width="">Remarks</th>
                </thead>
            </table>
             <div style="max-height:300px; overflow-y:scroll; width:1840px" id="scroll_body">
             <table width="1820" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_body<? //echo $p;?>">
				<?  
					$i=1;
					$total_order_qty=$total_order_amount=$total_recv_qty=$total_delivery_qty=$total_revenue=$total_access_shortage_per=$total_access_shortage_val=$total_delivery_amount=0;$total_access_shortage_qty=$total_tot_delivery_qty=0;
					foreach($partyArr as $style_no=>$row)
					{
						 
						if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF"; 
						$order_uom=$row[("order_uom")];
						/*$delivery_status_id=$row[("delivery_status")];
						$delivery_statusArr=rtrim($row[("delivery_status")],',');
						$delivery_Status=implode(", ",array_unique(explode(",",$delivery_statusArr)));
						
						if($delivery_statusArr)
						{
							$deliveryStatus=$delivery_Status;
						}
						else
						{
							$deliveryStatus=$row[("status")];
						}*/
						$deliveryStatus=$row[("status")];
						$order_id=rtrim($row[("order_id")],',');
						$order_idArr=array_unique(explode(",",$order_id));
						$recv_qty=0;
						$recv_qty=$receive_arr[$style_no]['recv_qty'];
						/*foreach($order_idArr as $oId)
						{
						$recv_qty+=$receive_arr[$style_no]['recv_qty'];
						}*/
						//$currency_id=$currency_id[$row[csf("currency_id")]];
						$order_quantity=$job_style_arr[$style_no]['po_qty'];
						$po_amt=$job_style_arr[$style_no]['po_amt'];
						/*if($order_uom==2)
						{
							$order_quantity=$row[("order_quantity")]*12;
						}*/
						$embl_typeArr=rtrim($embl_arr[$party_id][$style_no]['embl_type'],',');
						$remarksArr=rtrim($row[("remarks")],',');
						$remarks=implode(", ",array_unique(explode(",",$remarksArr)));
						
						
						
						$party_job=rtrim($party_full_job_arr[$party_id][$style_no],',');
						$party_jobAll=implode(", ",array_unique(explode(",",$party_job)));	
						
						$party_job_id=rtrim($party_full_job_id_arr[$party_id][$style_no],',');
						$party_job_id=implode(",",array_unique(explode(",",$party_job_id)));
						
						$pi_no=rtrim($pi_no_arr[$style_no]['pi_number'],',');
						$pi_nos=implode(", ",array_unique(explode(",",$pi_no)));
						$party_wo_no=rtrim($party_full_wo_no_arr[$party_id][$style_no],',');
						$party_woAll=implode(", ",array_unique(explode(",",$party_wo_no)));
						$tot_delivery_qty=$style_wise_chk_delivery_arr[$style_no]['delivery_qty'];
						//$party_jobAll=;
						$tool_tip='JobNo='.$party_jobAll.', Wo No= '.$party_woAll;
						?>
                          <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i.$p; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i.$p; ?>">
                            <td  width="20" id="wrd_brk"><? echo $i; ?></td>
                            <td width="100" id="wrd_brk"><? echo $row[("buyer_name")]; ?></td>
                            <td  width="100" id="wrd_brk" title="<? echo $tool_tip;?>"> <? echo $style_no; ?></td>
                            <td  width="100" id="wrd_brk"><? $color=rtrim($row[("color_id")],',');$colors=implode(", ",array_unique(explode(",",$color)));echo $colors; ?></td>
                            <td  width="100" id="wrd_brk"><? echo $garments_item[$row[("gmts_item_id")]]; ?></td>
                            <td width="120" id="wrd_brk"><?  $embl_types=implode(", ",array_unique(explode(",",$embl_typeArr))); echo $embl_types; ?></td> 
                            <td  width="100" id="wrd_brk"><? echo 'Pcs'; ?></td>
                            <td  width="100" id="wrd_brk" align="center"><?php echo $currency[$row[("currency_id")]];; ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo number_format($order_quantity,0); ?></td>
                            <td  width="80" id="wrd_brk" align="right" title="Avg Rate=<? echo $po_amt/$order_quantity;?>"><?php echo number_format($po_amt/$order_quantity,4);?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo number_format($po_amt,2); ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo number_format($recv_qty,0); ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo number_format($row[("delivery_qty")],0); ?></td>
                            <td  width="80" id="wrd_brk" align="right" title="PI Rate=<? echo $pi_arr[$style_no]['pi_rate'].', PI No='.$pi_nos;?>"><?php echo number_format($pi_arr[$style_no]['pi_rate'],4); ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php
								$delivery_amount=$row[("delivery_qty")]*$pi_arr[$style_no]['pi_rate'];
								echo number_format($delivery_amount,2);
							?></td>
                             <td  width="80" id="wrd_brk" align="right"><?php echo number_format($tot_delivery_qty,0); ?></td>
                             <td  width="80" id="wrd_brk" title="" align="right"><?php //$revenue=$order_quantity*$pi_arr[$style_no]['pi_rate'];
							 $access_shortage_qty=($tot_delivery_qty-$order_quantity);
							 echo number_format($access_shortage_qty,0); ?></td>
                             <?
							 $access_shortage_per=($tot_delivery_qty-$order_quantity)/$order_quantity*100;
							  
							 $bg_color="";
                             if($access_shortage_per>=5)
							 {
								  $bg_color="#FFFF33";
							 }
							 ?>
                             <td  width="80" id="wrd_brk" bgcolor="<? echo $bg_color;?>"  title="Total Delivery Qty-Order Qty/Order Qty*100" align="right"><?php echo number_format($access_shortage_per,2).'%'; ?></td>
                             <td  width="80" id="wrd_brk" title="Total Delivery Qty-Order Qty*Pi Rate" align="right"><?php   $access_shortage_val=($tot_delivery_qty-$order_quantity)*$pi_arr[$style_no]['pi_rate'];echo number_format($access_shortage_val,2); ?></td>
                             <td  width="80" id="wrd_brk" align="center"><?php echo $deliveryStatus;//$delivery_status; ?></td>
                                
                            <td id="wrd_brk" align="center"><p><a href="##" onClick="report_po_popup('<? echo $company_id; ?>','<? echo $style_no; ?>','<? echo $party_job_id; ?>','show_remark_popup',1)"><? if($remarks) echo "View";else echo " "; ?></a><?php //echo $remarks; ?></p></td>
						  </tr>
						<?	
						$total_order_qty+=$order_quantity;	
						$total_order_amount+=$po_amt;
						$total_delivery_qty+=$row[("delivery_qty")];
						$total_tot_delivery_qty+=$tot_delivery_qty;
						$total_recv_qty+=$recv_qty;
						$total_delivery_amount+=$delivery_amount;
						$total_access_shortage_qty+=$access_shortage_qty;
						$total_access_shortage_per+=$access_shortage_per;
						$total_access_shortage_val+=$access_shortage_val;
						//========================Buyer Summary===========
						$buyer_summary_arr[$row[("buyer_name")]]['uom']='Pcs';
						$buyer_summary_arr[$row[("buyer_name")]]['currency_id']=$row[("currency_id")];
						$buyer_summary_arr[$row[("buyer_name")]]['orderQty']+=$order_quantity;
						$buyer_summary_arr[$row[("buyer_name")]]['orderValue']+=$po_amt;
						$buyer_summary_arr[$row[("buyer_name")]]['recv_qty']+=$recv_qty;
						$buyer_summary_arr[$row[("buyer_name")]]['delivery_qty']+=$row[("delivery_qty")];
						$buyer_summary_arr[$row[("buyer_name")]]['tot_delivery_qty']+=$tot_delivery_qty;
						//$buyer_summary_arr[$row[("buyer_name")]]['delivery_qty']+=$pi_arr[$style_no]['pi_rate'];
						$buyer_summary_arr[$row[("buyer_name")]]['delivery_amt']+=$delivery_amount;
						$buyer_summary_arr[$row[("buyer_name")]]['excess_short_qty']+=($tot_delivery_qty-$order_quantity);
						$buyer_summary_arr[$row[("buyer_name")]]['excess_short_val']+=$access_shortage_val;
						 
						
						
					$i++;
					}
                  ?>
                </table>
         	</div>
         	<table width="1820" border="1" cellpadding="0" cellspacing="0" rules="all"> 
			<tr class="tbl_bottom">
                <td width="20" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td  width="100" >&nbsp;</td>
                <td  width="120" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td  width="100" >Total:</td>
                <td  width="80" ><? echo number_format($total_order_qty,0); ?></td>
                <td width="80" id=""></td>
                <td width="80" id=""><? echo number_format($total_order_amount,2); ?></td>
                <td width="80" id=""><? echo number_format($total_recv_qty,0); ?></td>
                <td width="80" id=""><? echo number_format($total_delivery_qty,0); ?></td>
              
               <td width="80" id=""></td>
               <td width="80" id=""><? echo number_format($total_delivery_amount,2); ?></td>
               <td width="80" id=""><? echo number_format($total_tot_delivery_qty,2); ?></td>
               <td width="80" id=""><? echo number_format($total_access_shortage_qty,0); ?></td>
               <?
			   $tot_access_shortage_per=($total_tot_delivery_qty-$total_order_qty)/$total_order_qty*100;
			   
               $tot_bg_color=""; $td_id="";
				 if($tot_access_shortage_per>=5)
				 {
					  $tot_bg_color="#FFFF33";
					  $td_id='id="td_color"';
				 }
			   ?>
               <td width="80"  <? echo $td_id;?>   ><? echo number_format($tot_access_shortage_per,2); ?>%</td>
                <td width="80" id=""><? echo number_format($total_access_shortage_val,0); ?></td>
                <td width="80" id=""></td>
                <td id=""></td>
			</tr>
		</table> 
        <?
						$garnd_total_order_qty+=$total_order_qty;
						$garnd_total_order_amount+=$total_order_amount;
						$garnd_total_recv_qty+=$total_recv_qty;
						$garnd_total_delivery_qty+=$total_delivery_qty;
						$tot_garnd_total_delivery_qty+=$total_tot_delivery_qty;	
						$garnd_total_access_shortage_qty+=$total_access_shortage_qty;
						$garnd_delivery_amount+=$total_delivery_amount;
						$garnd_total_access_shortage_per+=$total_access_shortage_per;
						$garnd_total_access_shortage_val+=$total_access_shortage_val;
$p++;
			}
		?>
        <table width="1820" border="1" cellpadding="0" cellspacing="0" rules="all"> 
			<tr class="tbl_bottom">
                <td  width="20" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td width="120" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td  width="100" >Grand Total:</td>
                <td  width="80" ><? echo number_format($garnd_total_order_qty,0); ?></td>
                <td width="80" id=""></td>
                <td width="80" id=""><? echo number_format($garnd_total_order_amount,2); ?></td>
                <td width="80" id=""><? echo number_format($garnd_total_recv_qty,0); ?></td>
                <td width="80" id=""><? echo number_format($garnd_total_delivery_qty,0); ?></td>
                <td width="80" id=""></td>
                <td width="80" id=""><? echo number_format($garnd_delivery_amount,2); ?></td>
                  <td width="80" id=""><? echo number_format($tot_garnd_total_delivery_qty,2); ?></td>
                <td width="80" id=""><? echo number_format($garnd_total_access_shortage_qty,2); ?></td>
                <td width="80" id=""><? //echo number_format($garnd_total_access_shortage_per,0); ?></td>
                <td width="80" id=""><? echo number_format($garnd_total_access_shortage_val,0); ?></td>
                <td width="80" id=""></td>
                <td id=""></td>
			</tr>
		</table> 
        <br>
         <table width="1120" border="1" cellpadding="0" cellspacing="0" rules="all"  class="rpt_table">
           <caption style="border:solid 0px;">  <b style="float:left">Buyer wise Summary </b></caption>
                <thead>
                        <th width="20">SL#</th>
                        <th width="100">Buyer</th>
                        <th width="100">UOM</th>
                        <th width="100">Currency</th>
                        <th width="100">Order Qty.</th>
                       
                        <th width="100">Order Value</th>
                        <th width="100">Received Qty.</th>
                        <th width="80">Delivery Qty.</th>
                       
                        <th width="80">Revenue</th>
                         <th width="80">Total Delivery Qty.</th>
                        <th width="80" title="Total Delivery Qty-Order Qty">Excess/<br>(Shortage) Qty.</th>
                        <th width="80">Excess/<br>(Shortage)%</th>
                        <th width="">Excess/<br>(Shortage)<br>Value</th>
                       
                </thead>
            </table>
             <div style="max-height:300px; overflow-y:scroll; width:1140px" id="scroll_body">
             <table width="1120" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_body<? //echo $p;?>">
             <? 
				 $buyer_total_order_qty=$buyer_total_order_val=$buyer_total_recv_qty=$buyer_total_delivery_qty=$buyer_total_access_shortage_qty=$buyer_total_access_shortage_val=$tot_buyer_total_delivery_qty=0;
				 $b=1;
             		foreach($buyer_summary_arr as $buyer=>$row)
					{
						 
						if ($b%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF"; 
						//$order_uom=$row[("order_uom")];
                        $revenue_amount=$row[("delivery_amt")];
                        ?>
               <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trbuyer_<? echo $b; ?>','<? echo $bgcolor; ?>')" id="trbuyer_<? echo $b; ?>">
                            <td  width="20" id="wrd_brk"><? echo $b; ?></td>
                            <td width="100" id="wrd_brk"><? echo $buyer; ?></td>
                            <td  width="100" id="wrd_brk"><? echo 'Pcs'; ?></td>
                            <td  width="100" id="wrd_brk" align="center"><?php echo $currency[$row[("currency_id")]]; ?></td>
                            <td  width="100" id="wrd_brk" align="right"><?php echo number_format($row[("orderQty")],0); ?></td>
                             
                            <td  width="100" id="wrd_brk" align="right"><?php echo number_format($row[("orderValue")],2); ?></td>
                            <td  width="100" id="wrd_brk" align="right"><?php echo number_format($row[("recv_qty")],0); ?></td>
                            <td  width="80" id="wrd_brk" align="right"><?php echo number_format($row[("delivery_qty")],0); ?></td>
                           
                            <td  width="80" id="wrd_brk" align="right"><?php
								
								echo number_format($revenue_amount,2);
							?></td>
                             <td  width="80" id="wrd_brk" align="right"><?php echo number_format($row[("tot_delivery_qty")],0); ?></td>
                             <td  width="80" id="wrd_brk" title="" align="right"><?php //$revenue=$order_quantity*$pi_arr[$style_no]['pi_rate'];
							 $access_shortage_qty=($row[("tot_delivery_qty")]-$row[("orderQty")]);
							 echo number_format($access_shortage_qty,0); ?></td>
                             <?
							 $buyer_access_shortage_per=($row[("tot_delivery_qty")]-$row[("orderQty")])/$row[("orderQty")]*100;
							 
							 $buyer_bg_color="";
                             if($buyer_access_shortage_per>=5)
							 {
								  $buyer_bg_color="#FFFF33";
							 }
							 ?>
                             <td  width="80" id="wrd_brk" bgcolor="<? echo $buyer_bg_color;?>"  title="Total Delivery Qty-Order Qty/Order Qty*100" align="right"><?php 
							 echo number_format($buyer_access_shortage_per,2).'%'; ?></td>
                             <td  width="" id="wrd_brk" title="Total Delivery Qty-Order Qty*Pi Rate" align="right"><?php  
							  $access_shortage_val=$row[("excess_short_val")];echo number_format($access_shortage_val,2); ?></td>
                             
                </tr>
                <?
					
					$b++;
					$buyer_total_order_qty+=$row[("orderQty")];
					$buyer_total_order_val+=$row[("orderValue")];
					$buyer_total_recv_qty+=$row[("recv_qty")];
					$buyer_total_delivery_qty+=$row[("delivery_qty")];
					$tot_buyer_total_delivery_qty+=$row[("tot_delivery_qty")];
					$buyer_total_revenue_amount+=$revenue_amount;
					$buyer_total_access_shortage_qty+=$access_shortage_qty;
					$buyer_total_access_shortage_val+=$access_shortage_val;
					
					}
				?>
             </table>
        
   	  </div>
      	<table width="1120" border="1" cellpadding="0" cellspacing="0" rules="all"> 
			<tr class="tbl_bottom">
                <td width="20" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td  width="100" >Total:</td>
                <td  width="100" ><? echo number_format($buyer_total_order_qty,0); ?></td>
               
                <td  width="100" ><? echo number_format($buyer_total_order_val,2); ?></td>
                <td  width="100" ><? echo number_format($buyer_total_recv_qty,0); ?></td>
              
                <td width="80" id=""><? echo number_format($buyer_total_delivery_qty,0); ?></td>
                
                
                <td width="80" id=""><? echo number_format($buyer_total_revenue_amount,0); ?></td>
                <td width="80" id=""><? echo number_format($tot_buyer_total_delivery_qty,0); ?></td>
                <?
              
				?>
              
               <td width="80"><? echo number_format($buyer_total_access_shortage_qty,0); ?></td>
               <?
			    $tot_buyer_access_shortage_per=($tot_buyer_total_delivery_qty-$buyer_total_order_qty)/$buyer_total_order_qty*100;
				 $tot_buyer_bg_color="";$td_id_buyer='';
				 if($tot_buyer_access_shortage_per>=5)
				 {
					  $tot_buyer_bg_color="#FFFF33";
					   $td_id_buyer='id="td_color"';
				 }
			   ?>
               <td width="80" <? echo $td_id_buyer;?> ><? echo number_format($tot_buyer_access_shortage_per,2); ?>%</td>
               <td width="" id=""><? echo number_format($buyer_total_access_shortage_val,0); ?></td>
              
			</tr>
		</table> 
   
 <? } //Show End ?>
     
      
     </fieldset>
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

if($action=="show_remark_popup")
{
	echo load_html_head_contents("Remarks Details", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	
//	$order_arr=return_library_array( "select id, po_number from wo_po_break_down where id='$po_ids'", "id", "po_number");
	//$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	
	if($type==1)
	{
		$td_width=620;
		//$row_span=4;	
	}
	//echo $party_id; 
	
	?>
	<script>
		function print_window()
		{
			$("#table_body_popup tr:first").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
			$("#table_body_popup tr:first").show();
		}	
	</script>	
	<fieldset style="width:<? echo $td_width?>px; margin-left:3px">
        <div style="width:<? echo $td_width?>px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
        </div>
        <div id="report_div" align="center">
            <table rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center">
                <tr> 
                	<td colspan="3" align="left"><strong> Remarks Details </strong></td>
                </tr>
               
            </table>
            <table border="1" class="rpt_table" rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center" >
                <thead>
                    <th width="30">SL</th>
                    <th width="110">Delivery No</th>
                    <th width="110">Job No</th>
                    <th width="">Remarks</th>
                </thead>
                </table>
                 <table border="1" class="rpt_table" rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center" id="table_body_popup">
                <?
				
					//$party_id=explode("_",$party_id);
					//$party_id=$party_id[1];
				 $deli_sql="select  f.delivery_no,f.remarks,d.subcon_job
from subcon_ord_mst d, subcon_ord_dtls c,subcon_delivery_mst f,subcon_delivery_dtls g
where d.id=c.mst_id  and f.id=g.mst_id  and f.job_no=d.subcon_job  and c.id=g.order_id and f.entry_form=303 and d.entry_form=295 and d.status_active=1 and c.status_active=1  and f.status_active=1 and g.status_active=1 and f.status_active=1 and g.status_active=1  and d.company_id in($cbo_company_name) and c.buyer_style_ref='$style_ref' and d.id in($party_id) group by  f.delivery_no,f.remarks,d.subcon_job order by f.delivery_no  "; 
		
				$deli_sql_result=sql_select($deli_sql); $i=1;
				 
				foreach($deli_sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><p><? echo $i; ?></p></td>
						<td width="110"><div style="word-wrap:break-word; width:110px"><? echo $row[csf('delivery_no')]; ?></div></td>
                        <td width="110"><div style="word-wrap:break-word; width:110px"><? echo $row[csf('subcon_job')]; ?></div></td>
						<td width=""><div style="word-wrap:break-word;"><? echo $row[csf('remarks')]; ?></div></td>
                        
                       
					</tr>
					<?
					 
					$i++;
				}
				?>
				
				<tfoot>
					<tr class="tbl_bottom">
						<td align="right"></td>
						<td align="right"><? //echo number_format($tot_cut_qty,0); ?></td>
                        <td align="right"><? //echo number_format($tot_cut_qty,0); ?></td>
                        <td align="right"><? //echo number_format($tot_size_qty,0); ?>&nbsp;</td>
                        
					</tr>
				</tfoot>
			</table>
           
         <script>   setFilterGrid("table_body_popup",-1);</script>
		</div>
	</fieldset>
	<?
	exit();
} //Po wise button end

?>